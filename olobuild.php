<?php
/**
 * Plugin Name: Olobuild
 * Plugin URI:  https://olotheme.com
 * Description: Page builder professionale olonico con sistema a griglia (tile drag & drop).
 * Version:     1.4.170
 * Author:      Claudio Vinco
 * Author URI:  https://clod.eu
 * Text Domain: olobuild
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 5.9
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'OLO_VERSION', '1.4.170' );
define( 'OLO_PATH', plugin_dir_path( __FILE__ ) );
define( 'OLO_URL', plugin_dir_url( __FILE__ ) );

// Polyfill str_contains() and str_starts_with() for PHP < 8.0
if ( ! function_exists( 'str_contains' ) ) {
    function str_contains( string $haystack, string $needle ): bool {
        return '' === $needle || false !== strpos( $haystack, $needle );
    }
}
if ( ! function_exists( 'str_starts_with' ) ) {
    function str_starts_with( string $haystack, string $needle ): bool {
        return 0 === strncmp( $haystack, $needle, strlen( $needle ) );
    }
}

/**
 * Helper globale per leggere preferenze stockmedia (Configurazione → Stock media → comportamento).
 * Usato dai 4 provider Olo_Unsplash/Pexels/Pixabay/Openverse per decidere download_local + optimize_on_download.
 */
function olo_stockmedia_behavior() {
    static $cached = null;
    if ( $cached !== null ) return $cached;
    $cached = wp_parse_args(
        get_option( 'olo_stockmedia_behavior', [] ) ?: [],
        [ 'preferred' => 'unsplash', 'download_local' => true, 'optimize_on_download' => false ]
    );
    return $cached;
}

/**
 * Converte un file immagine in WebP usando GD o Imagick (se disponibili).
 * Restituisce il path del WebP, o false se la conversione fallisce.
 *
 * @param string $source_path Path al file sorgente.
 * @param int    $quality     Qualità 0-100.
 * @return string|false Path del WebP creato (rimpiazza source) oppure false.
 */
function olo_convert_to_webp( $source_path, $quality = 82 ) {
    if ( ! file_exists( $source_path ) ) return false;
    $info = @getimagesize( $source_path );
    if ( ! $info || ! in_array( $info[2], [ IMAGETYPE_JPEG, IMAGETYPE_PNG ], true ) ) return false;

    $webp_path = preg_replace( '/\.(jpe?g|png)$/i', '.webp', $source_path );
    if ( $webp_path === $source_path ) $webp_path .= '.webp';

    // Imagick (preferito, qualità migliore)
    if ( extension_loaded( 'imagick' ) ) {
        try {
            $im = new Imagick( $source_path );
            $im->setImageFormat( 'webp' );
            $im->setImageCompressionQuality( $quality );
            $im->writeImage( $webp_path );
            $im->clear();
            return file_exists( $webp_path ) ? $webp_path : false;
        } catch ( Exception $e ) { /* fallback to GD */ }
    }

    // GD fallback
    if ( function_exists( 'imagewebp' ) ) {
        $img = ( $info[2] === IMAGETYPE_PNG ) ? @imagecreatefrompng( $source_path ) : @imagecreatefromjpeg( $source_path );
        if ( ! $img ) return false;
        if ( $info[2] === IMAGETYPE_PNG ) {
            imagepalettetotruecolor( $img );
            imagealphablending( $img, true );
            imagesavealpha( $img, true );
        }
        $ok = imagewebp( $img, $webp_path, $quality );
        imagedestroy( $img );
        return $ok && file_exists( $webp_path ) ? $webp_path : false;
    }

    return false;
}

/**
 * Load plugin text domain for translations.
 * Force-load MO for all non-Italian locales (plugin source strings are in Italian).
 */
add_action( 'init', function() {
    $locale = determine_locale();
    if ( ! str_starts_with( $locale, 'it' ) ) {
        $mo = OLO_PATH . 'languages/olobuild-' . $locale . '.mo';
        if ( ! file_exists( $mo ) ) {
            $mo = OLO_PATH . 'languages/olobuild-en_US.mo';
        }
        if ( file_exists( $mo ) ) {
            load_textdomain( 'olobuild', $mo );
        }
    }
} );

/**
 * Abilita upload di file JSON/Lottie e SVG nella Media Library.
 * SVG upload requires unfiltered_html capability (admins only) to prevent XSS.
 */
add_filter( 'upload_mimes', function( $mimes ) {
    $mimes['json'] = 'application/json';
    $mimes['lottie'] = 'application/json';
    // SVG only for users with unfiltered_html (prevents XSS from editor-role uploads)
    if ( current_user_can( 'unfiltered_html' ) ) {
        $mimes['svg'] = 'image/svg+xml';
    }
    return $mimes;
} );

/**
 * Sanitize SVG content — removes dangerous elements and attributes.
 * Used globally for custom icon upload, SVG animator, etc.
 *
 * @param string $svg  Raw SVG content.
 * @return string      Sanitized SVG or empty string if invalid.
 */
function olo_sanitize_svg( $svg ) {
    if ( empty( $svg ) ) return '';

    // Primary: use DOMDocument for proper XML parsing (prevents XXE, handles encodings)
    if ( class_exists( 'DOMDocument' ) ) {
        return olo_sanitize_svg_dom( $svg );
    }

    // Fallback: regex-based sanitization for hosts without DOMDocument
    return olo_sanitize_svg_regex( $svg );
}

/**
 * SVG sanitization via DOMDocument — XML-aware, handles encoded entities.
 */
function olo_sanitize_svg_dom( $svg ) {
    // XXE prevention: LIBXML_NONET disables network access.
    // libxml_disable_entity_loader() was removed in PHP 8.2 (entities disabled by default).
    libxml_use_internal_errors( true );

    $doc = new DOMDocument();
    // Wrap in XML to handle encoding properly
    $wrapped = '<?xml version="1.0" encoding="UTF-8"?>' . $svg;
    if ( ! $doc->loadXML( $wrapped, LIBXML_NONET | LIBXML_NOENT ) ) {
        // Try loading as HTML fragment if XML parsing fails
        $doc = new DOMDocument();
        if ( ! $doc->loadHTML( '<div>' . $svg . '</div>', LIBXML_NONET | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD ) ) {
            libxml_clear_errors();
            return '';
        }
    }

    // Dangerous elements to remove completely
    $dangerous_tags = [ 'script', 'foreignObject', 'iframe', 'object', 'embed', 'applet', 'form', 'input', 'textarea', 'button' ];
    foreach ( $dangerous_tags as $tag ) {
        $nodes = $doc->getElementsByTagName( $tag );
        $remove = [];
        for ( $i = 0; $i < $nodes->length; $i++ ) {
            $remove[] = $nodes->item( $i );
        }
        foreach ( $remove as $node ) {
            $node->parentNode->removeChild( $node );
        }
    }

    // Walk all elements: remove event handlers and dangerous attributes
    $xpath = new DOMXPath( $doc );
    $all   = $xpath->query( '//*' );
    foreach ( $all as $el ) {
        $attrs_to_remove = [];
        foreach ( $el->attributes as $attr ) {
            $name  = strtolower( $attr->name );
            $value = strtolower( trim( $attr->value ) );

            // Remove on* event handlers
            if ( str_starts_with( $name, 'on' ) ) {
                $attrs_to_remove[] = $attr->name;
                continue;
            }
            // Remove javascript:/data: URIs in href, xlink:href, src
            if ( in_array( $name, [ 'href', 'xlink:href', 'src', 'action', 'formaction' ], true ) ) {
                if ( preg_match( '/^\s*(javascript|data|vbscript)\s*:/i', $attr->value ) ) {
                    $attrs_to_remove[] = $attr->name;
                }
            }
            // Remove style attributes with expression() or javascript:
            if ( $name === 'style' ) {
                if ( preg_match( '/(expression|javascript|vbscript|url\s*\(\s*["\']?\s*javascript)/i', $attr->value ) ) {
                    $attrs_to_remove[] = $attr->name;
                }
            }
        }
        foreach ( $attrs_to_remove as $a ) {
            $el->removeAttribute( $a );
        }
    }

    // Remove <use> with external references (SSRF)
    $uses = $doc->getElementsByTagName( 'use' );
    $remove = [];
    for ( $i = 0; $i < $uses->length; $i++ ) {
        $use = $uses->item( $i );
        $href = $use->getAttribute( 'href' ) ?: $use->getAttribute( 'xlink:href' );
        if ( $href ) {
            if ( preg_match( '#^https?://#i', $href ) ) {
                $remove[] = $use;
            }
        }
    }
    foreach ( $remove as $node ) {
        $node->parentNode->removeChild( $node );
    }

    // Extract the SVG element
    $svgs = $doc->getElementsByTagName( 'svg' );
    if ( $svgs->length === 0 ) {
        libxml_clear_errors();
        return '';
    }

    $result = $doc->saveXML( $svgs->item( 0 ) );
    libxml_clear_errors();

    return trim( $result );
}

/**
 * SVG sanitization fallback via regex (for hosts without DOMDocument).
 */
function olo_sanitize_svg_regex( $svg ) {
    // Remove XML declaration and DOCTYPE (prevent XXE)
    $svg = preg_replace( '/<\?xml[^?]*\?>/i', '', $svg );
    $svg = preg_replace( '/<!DOCTYPE[^>]*>/i', '', $svg );
    $svg = preg_replace( '/<!ENTITY[^>]*>/i', '', $svg );
    $svg = preg_replace( '/<!ATTLIST[^>]*>/i', '', $svg );
    $svg = preg_replace( '/<!ELEMENT[^>]*>/i', '', $svg );

    // Remove dangerous elements
    $svg = preg_replace( '/<script[\s\S]*?<\/script>/i', '', $svg );
    $svg = preg_replace( '/<foreignObject[\s\S]*?<\/foreignObject>/i', '', $svg );
    $svg = preg_replace( '/<iframe[\s\S]*?<\/iframe>/i', '', $svg );
    $svg = preg_replace( '/<object[\s\S]*?<\/object>/i', '', $svg );
    $svg = preg_replace( '/<embed[^>]*\/?>/i', '', $svg );

    // Remove on* event attributes (all quoting styles + unquoted)
    $svg = preg_replace( '/\s+on\w+\s*=\s*"[^"]*"/i', '', $svg );
    $svg = preg_replace( '/\s+on\w+\s*=\s*\'[^\']*\'/i', '', $svg );
    $svg = preg_replace( '/\s+on\w+\s*=\s*[^\s>]*/i', '', $svg );

    // Remove javascript: / data: / vbscript: URIs
    $svg = preg_replace( '/(href|src|action)\s*=\s*"(javascript|data|vbscript):[^"]*"/i', '$1=""', $svg );
    $svg = preg_replace( '/(href|src|action)\s*=\s*\'(javascript|data|vbscript):[^\']*\'/i', "$1=''", $svg );

    // Remove <use> with external references (SSRF)
    $svg = preg_replace( '/<use[^>]*(xlink:)?href\s*=\s*"https?:[^"]*"[^>]*\/?>/i', '', $svg );

    // Remove animation event handlers
    $svg = preg_replace( '/\s+(onbegin|onend|onrepeat)\s*=\s*"[^"]*"/i', '', $svg );
    $svg = preg_replace( '/\s+(onbegin|onend|onrepeat)\s*=\s*\'[^\']*\'/i', '', $svg );

    // Remove CSS expressions and javascript in style attributes
    $svg = preg_replace( '/style\s*=\s*"[^"]*expression\s*\([^"]*"/i', '', $svg );
    $svg = preg_replace( '/style\s*=\s*"[^"]*javascript:[^"]*"/i', '', $svg );
    $svg = preg_replace( '/style\s*=\s*\'[^\']*expression\s*\([^\']*\'/i', '', $svg );
    $svg = preg_replace( '/style\s*=\s*\'[^\']*javascript:[^\']*\'/i', '', $svg );

    // Ensure it actually contains an SVG tag
    if ( stripos( $svg, '<svg' ) === false ) return '';

    return trim( $svg );
}

// Bypass la validazione MIME reale di WP per JSON/SVG (wp_check_filetype_and_ext restituisce vuoto)
add_filter( 'wp_check_filetype_and_ext', function( $data, $file, $filename, $mimes ) {
    if ( ! current_user_can( 'upload_files' ) ) {
        return $data;
    }
    // Validate: only allow single extension (prevent double-ext attacks like .json.php)
    $basename = basename( $filename );
    $parts    = explode( '.', $basename );
    if ( count( $parts ) > 2 ) {
        return $data; // Reject double extensions
    }
    $ext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
    if ( $ext === 'json' ) {
        $data['ext']  = 'json';
        $data['type'] = 'application/json';
    }
    if ( $ext === 'svg' ) {
        $data['ext']  = 'svg';
        $data['type'] = 'image/svg+xml';
    }
    return $data;
}, 10, 4 );

/**
 * Traduzione stringhe Olobuild — equivalente di __() per il plugin.
 *
 * Sistema dinamico: legge le traduzioni esclusivamente dal DB di Olo Lang
 * (gestite via UI del plugin). Nessun dizionario hardcoded lato codice —
 * aggiungere una lingua = installarla + tradurre le stringhe dal pannello.
 *
 * @param string $text Testo originale (italiano).
 * @return string Testo tradotto o originale.
 */
function olo_t( $text ) {
    $map = olo_get_translations_map();
    return $map[ $text ] ?? $text;
}

/**
 * Mappa traduzioni originale => tradotto per la lingua corrente.
 * Cache statica nella request. Array vuoto se siamo nella lingua default
 * o se Olo Lang non è attivo.
 *
 * @return array<string,string>
 */
function olo_get_translations_map() {
    static $map = null;
    if ( $map !== null ) return $map;

    $map = [];

    if ( ! class_exists( 'Olo_Lang_Language' ) || ! class_exists( 'Olo_Lang_Database' ) ) {
        return $map;
    }

    $lang    = Olo_Lang_Language::detect_current_lang();
    $default = Olo_Lang_Language::get_default_lang();
    if ( $lang === $default ) return $map;

    $db  = new Olo_Lang_Database();
    $raw = $db->get_translation_map( 0, $lang );
    foreach ( $raw as $row ) {
        $orig  = $row['original'] ?? '';
        $trans = trim( $row['translation'] ?? '' );
        if ( $orig !== '' && $trans !== '' && $orig !== $trans && ( $row['status'] ?? '' ) !== 'bozza' ) {
            $map[ $orig ] = $trans;
        }
    }
    return $map;
}

/**
 * Ritorna il locale corrente (olo-lang > WP locale) in formato xx_XX.
 */
function olo_current_locale() {
    if ( class_exists( 'Olo_Lang_Language' ) ) {
        $code = Olo_Lang_Language::detect_current_lang();
        $known = [
            'en' => 'en_US', 'de' => 'de_DE', 'fr' => 'fr_FR', 'es' => 'es_ES',
            'it' => 'it_IT', 'pt' => 'pt_BR', 'nl' => 'nl_NL', 'pl' => 'pl_PL',
            'ja' => 'ja',    'ru' => 'ru_RU', 'zh' => 'zh_CN',
        ];
        return $known[ $code ] ?? $code;
    }
    return get_locale();
}

require_once OLO_PATH . 'includes/class-database.php';
require_once OLO_PATH . 'includes/class-tile-manager.php';
require_once OLO_PATH . 'includes/class-rest-api.php';
require_once OLO_PATH . 'includes/class-dynamic-content.php';
require_once OLO_PATH . 'includes/class-style-system.php';
require_once OLO_PATH . 'includes/class-font-host.php';
require_once OLO_PATH . 'includes/class-css-builder.php';
require_once OLO_PATH . 'includes/class-animation-builder.php';
require_once OLO_PATH . 'includes/class-frontend-renderer.php';
require_once OLO_PATH . 'includes/class-asset-optimizer.php';
require_once OLO_PATH . 'includes/class-template-library.php';
require_once OLO_PATH . 'includes/class-page-integration.php';
require_once OLO_PATH . 'includes/class-header-integration.php';
require_once OLO_PATH . 'includes/class-footer-integration.php';
require_once OLO_PATH . 'includes/class-single-integration.php';
require_once OLO_PATH . 'includes/class-archive-integration.php';
require_once OLO_PATH . 'includes/class-404-integration.php';
require_once OLO_PATH . 'includes/class-search-results-integration.php';
require_once OLO_PATH . 'includes/class-location-single.php';
require_once OLO_PATH . 'includes/class-form-handler.php';
require_once OLO_PATH . 'includes/class-unsplash.php';
require_once OLO_PATH . 'includes/class-pexels.php';
require_once OLO_PATH . 'includes/class-pixabay.php';
require_once OLO_PATH . 'includes/class-openverse.php';
require_once OLO_PATH . 'includes/class-freesound.php';
require_once OLO_PATH . 'includes/class-media-search.php';
require_once OLO_PATH . 'includes/class-custom-fonts.php';
require_once OLO_PATH . 'includes/class-custom-code.php';
require_once OLO_PATH . 'includes/class-form-submissions.php';
require_once OLO_PATH . 'includes/class-newsletter.php';
require_once OLO_PATH . 'includes/class-maintenance-mode.php';
require_once OLO_PATH . 'includes/class-analytics-tracking.php';
require_once OLO_PATH . 'includes/class-diagnostics.php';
Olo_Diagnostics::init();
// OLOsecurity — moduli di sicurezza
require_once OLO_PATH . 'includes/class-security-audit.php';
require_once OLO_PATH . 'includes/class-security-config-monitor.php';
require_once OLO_PATH . 'includes/class-security-components.php';
require_once OLO_PATH . 'includes/class-security-login.php';
require_once OLO_PATH . 'includes/class-security-hardening.php';
require_once OLO_PATH . 'includes/class-security-sentinel.php';
Olo_Security_Audit::maybe_install();
Olo_Security_Audit::init();
Olo_Security_Login::init();
Olo_Security_Hardening::init();
Olo_Security_Sentinel::init();
require_once OLO_PATH . 'includes/class-critical-css.php';
require_once OLO_PATH . 'includes/class-ab-testing.php';
require_once OLO_PATH . 'includes/class-cookie-consent.php';
require_once OLO_PATH . 'includes/class-role-manager.php';
require_once OLO_PATH . 'includes/class-site-export.php';
require_once OLO_PATH . 'includes/class-ai-assistant.php';
require_once OLO_PATH . 'includes/class-seo-head.php';
require_once OLO_PATH . 'includes/class-seo-settings.php';
require_once OLO_PATH . 'includes/class-seo-redirects.php';
require_once OLO_PATH . 'includes/class-global-popups.php';
require_once OLO_PATH . 'includes/class-template-conditions.php';
require_once OLO_PATH . 'includes/class-accessibility.php';
require_once OLO_PATH . 'includes/class-performance-hints.php';
require_once OLO_PATH . 'includes/class-performance-settings.php';
require_once OLO_PATH . 'includes/class-white-label.php';
require_once OLO_PATH . 'includes/class-site-import-export.php';
require_once OLO_PATH . 'includes/class-tools.php';
require_once OLO_PATH . 'includes/class-olo-lang-bridge.php';
Olo_Lang_Bridge::init();
require_once OLO_PATH . 'includes/class-debug-bar.php';
require_once OLO_PATH . 'includes/class-woo-template-integration.php';
require_once OLO_PATH . 'includes/class-olo-builder.php';

/**
 * Safety net: ensure administrators always have the capabilities needed by Olobuild,
 * even on sites where role-management plugins (Tutor LMS, Members, etc.) may have
 * altered or removed default capabilities.
 *
 * Uses 'user_has_cap' filter which runs on every capability check — adds missing
 * caps on-the-fly only for users with the 'administrator' role.
 */
add_filter( 'user_has_cap', function ( $allcaps, $caps, $args, $user ) {
    if ( ! $user || empty( $user->roles ) || ! in_array( 'administrator', (array) $user->roles, true ) ) {
        return $allcaps;
    }
    // Guarantee these caps for administrators (Olobuild menu requirements)
    $required = [ 'manage_options', 'edit_posts', 'edit_pages', 'upload_files', 'switch_themes', 'unfiltered_html' ];
    foreach ( $required as $cap ) {
        if ( empty( $allcaps[ $cap ] ) ) {
            $allcaps[ $cap ] = true;
        }
    }
    return $allcaps;
}, 10, 4 );

// Activation hook
register_activation_hook( __FILE__, function () {
    // Migrate from old "mosaic" naming to "olo" (one-time, idempotent)
    // MUST run BEFORE create_tables() so RENAME succeeds before dbDelta creates empty tables
    global $wpdb;
    $old_templates  = $wpdb->prefix . 'mosaic_templates';
    $old_revisions  = $wpdb->prefix . 'mosaic_revisions';
    $new_templates  = $wpdb->prefix . 'olo_templates';
    $new_revisions  = $wpdb->prefix . 'olo_revisions';

    // Rename tables if old ones exist and new ones don't
    if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $old_templates ) ) && ! $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $new_templates ) ) ) {
        $wpdb->query( "RENAME TABLE `{$old_templates}` TO `{$new_templates}`" );
    }
    if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $old_revisions ) ) && ! $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $new_revisions ) ) ) {
        $wpdb->query( "RENAME TABLE `{$old_revisions}` TO `{$new_revisions}`" );
    }

    // Create/update tables AFTER migration so dbDelta doesn't interfere with RENAME
    $db = new Olo_Database();
    $db->create_tables();

    // Form submissions table
    Olo_Form_Submissions::create_table();
    Olo_Newsletter::create_table();

    // Security Sentinel: fotografa lo stato "buono" dei file come baseline integrità.
    if ( class_exists( 'Olo_Security_Sentinel' ) ) {
        Olo_Security_Sentinel::build_baseline();
    }

    // Migrate options
    $option_map = [
        'mosaic_active_header' => 'olo_active_header',
        'mosaic_active_footer' => 'olo_active_footer',
        'mosaic_styles'        => 'olo_styles',
    ];
    foreach ( $option_map as $old_key => $new_key ) {
        $val = get_option( $old_key );
        if ( false !== $val && false === get_option( $new_key ) ) {
            update_option( $new_key, $val, false );
            delete_option( $old_key );
        }
    }
    // Migrate per-post-type single options
    foreach ( get_post_types( [ 'public' => true ], 'names' ) as $pt ) {
        $old_key = "mosaic_active_single_{$pt}";
        $new_key = "olo_active_single_{$pt}";
        $val     = get_option( $old_key );
        if ( false !== $val && false === get_option( $new_key ) ) {
            update_option( $new_key, $val, false );
            delete_option( $old_key );
        }
    }

    // Migrate post meta
    $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_key = %s WHERE meta_key = %s", '_olo_template_id', '_mosaic_template_id' ) );
    $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_key = %s WHERE meta_key = %s", '_olo_header_id', '_mosaic_header_id' ) );
    $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_key = %s WHERE meta_key = %s", '_olo_footer_id', '_mosaic_footer_id' ) );

    // Migrate nav menu location assignment
    $locations = get_theme_mod( 'nav_menu_locations', [] );
    if ( isset( $locations['mosaic_header'] ) && ! isset( $locations['olo_header'] ) ) {
        $locations['olo_header'] = $locations['mosaic_header'];
        unset( $locations['mosaic_header'] );
        set_theme_mod( 'nav_menu_locations', $locations );
    }

    // Set transient for first-run wizard redirect
    if ( ! get_option( 'olo_setup_complete' ) ) {
        set_transient( 'olo_activating', true, 60 );
    }
} );

// Deactivation hook
register_deactivation_hook( __FILE__, function () {
    delete_transient( 'olo_builder_activated' );
    wp_clear_scheduled_hook( 'olo_weekly_cleanup' );
    wp_clear_scheduled_hook( 'olo_sentinel_scan' );
} );

// Weekly cron for orphaned revision cleanup
add_action( 'olo_weekly_cleanup', function() {
    $db = Olo_Database::instance();
    $db->cleanup_orphaned_revisions();
} );
if ( ! wp_next_scheduled( 'olo_weekly_cleanup' ) ) {
    wp_schedule_event( time(), 'weekly', 'olo_weekly_cleanup' );
}

// Setup Wizard (first-run experience)
require_once OLO_PATH . 'includes/class-setup-wizard.php';
( new Olo_Setup_Wizard() )->init();

// Anti-cache header per admin loggati: il browser non deve servire la home/pagine
// dalla memory cache mentre stai costruendo il sito, altrimenti i cambi a
// header/footer/template non si vedono finché non fai hard reload.
// Aggiunge anche `Vary: Cookie` perché la response varia in base al login state
// (es. admin bar visibile solo se loggato).
add_action( 'send_headers', function() {
    if ( is_admin() || wp_doing_ajax() || wp_doing_cron() || defined( 'REST_REQUEST' ) ) return;
    if ( ! is_user_logged_in() ) return;
    if ( ! current_user_can( 'edit_posts' ) ) return;
    nocache_headers();
    header( 'Vary: Cookie', false );
}, 1 );

// Google Fonts: self-hosted via Olo_Font_Host (serviti da /uploads), quindi
// nessun preconnect verso i domini Google — il visitatore non li contatta.

// Custom Fonts @font-face CSS — only if custom fonts exist
add_action( 'wp_head', function() {
    $fonts = get_option( 'olo_custom_fonts', [] );
    if ( empty( $fonts ) ) return;
    $css = Olo_Custom_Fonts::generate_css();
    if ( $css ) {
        echo '<style id="olo-custom-fonts">' . $css . '</style>';
    }
}, 5 );

// Add decoding="async" to all Olobuild images
add_filter( 'wp_get_attachment_image_attributes', function( $attr ) {
    if ( ! isset( $attr['decoding'] ) ) {
        $attr['decoding'] = 'async';
    }
    return $attr;
} );

// Registra tile aggiuntive via hook (non toccare class-olo-builder.php)
add_action( 'olo_register_external_tiles', function ( $manager ) {
    require_once OLO_PATH . 'includes/tiles/class-readingtime-tile.php';
    $manager->register_tile( new Olo_Readingtime_Tile() );

    require_once OLO_PATH . 'includes/tiles/class-darkmode-tile.php';
    $manager->register_tile( new Olo_Darkmode_Tile() );

    require_once OLO_PATH . 'includes/tiles/class-queryloop-tile.php';
    $manager->register_tile( new Olo_Queryloop_Tile() );

    require_once OLO_PATH . 'includes/tiles/class-portfolio-tile.php';
    $manager->register_tile( new Olo_Portfolio_Tile() );

    require_once OLO_PATH . 'includes/tiles/class-pagetitlebar-tile.php';
    $manager->register_tile( new Olo_Pagetitlebar_Tile() );

    require_once OLO_PATH . 'includes/tiles/class-lightbox-tile.php';
    $manager->register_tile( new Olo_Lightbox_Tile() );

    require_once OLO_PATH . 'includes/tiles/class-floatingpanel-tile.php';
    $manager->register_tile( new Olo_Floatingpanel_Tile() );

    require_once OLO_PATH . 'includes/tiles/class-mobilebar-tile.php';
    $manager->register_tile( new Olo_Mobilebar_Tile() );

    require_once OLO_PATH . 'includes/tiles/class-svganimator-tile.php';
    $manager->register_tile( new Olo_Svganimator_Tile() );

    require_once OLO_PATH . 'includes/tiles/class-newsletter-tile.php';
    $manager->register_tile( new Olo_Newsletter_Tile() );

    require_once OLO_PATH . 'includes/tiles/class-viewer360-tile.php';
    $manager->register_tile( new Olo_Viewer360_Tile() );

    // WooCommerce tiles — only load if WooCommerce is active
    if ( class_exists( 'WooCommerce' ) ) {
        $woo_tiles = [
            'class-woo-products-tile.php'           => 'Olo_Woo_Products_Tile',
            'class-woo-minicart-tile.php'           => 'Olo_Woo_Minicart_Tile',
            'class-woo-price-tile.php'              => 'Olo_Woo_Price_Tile',
            'class-woo-addtocart-tile.php'          => 'Olo_Woo_Addtocart_Tile',
            'class-woo-categories-tile.php'         => 'Olo_Woo_Categories_Tile',
            'class-woo-rating-tile.php'             => 'Olo_Woo_Rating_Tile',
            'class-woo-product-title-tile.php'      => 'Olo_Woo_Product_Title_Tile',
            'class-woo-product-image-tile.php'      => 'Olo_Woo_Product_Image_Tile',
            'class-woo-product-description-tile.php'=> 'Olo_Woo_Product_Description_Tile',
            'class-woo-product-meta-tile.php'       => 'Olo_Woo_Product_Meta_Tile',
            'class-woo-product-stock-tile.php'      => 'Olo_Woo_Product_Stock_Tile',
            'class-woo-product-tabs-tile.php'       => 'Olo_Woo_Product_Tabs_Tile',
            'class-woo-related-tile.php'            => 'Olo_Woo_Related_Tile',
            'class-woo-upsells-tile.php'            => 'Olo_Woo_Upsells_Tile',
            'class-woo-cart-tile.php'               => 'Olo_Woo_Cart_Tile',
            'class-woo-checkout-tile.php'           => 'Olo_Woo_Checkout_Tile',
            'class-woo-order-tracking-tile.php'     => 'Olo_Woo_Order_Tracking_Tile',
            'class-woo-breadcrumbs-tile.php'        => 'Olo_Woo_Breadcrumbs_Tile',
            'class-woo-notices-tile.php'            => 'Olo_Woo_Notices_Tile',
            'class-woo-product-navigation-tile.php' => 'Olo_Woo_Product_Navigation_Tile',
            'class-woo-sale-badge-tile.php'         => 'Olo_Woo_Sale_Badge_Tile',
            'class-woo-product-filter-tile.php'     => 'Olo_Woo_Product_Filter_Tile',
            'class-woo-quickview-tile.php'          => 'Olo_Woo_Quickview_Tile',
            'class-woo-checkout-multistep-tile.php'  => 'Olo_Woo_Checkout_Multistep_Tile',
            'class-woo-myaccount-tile.php'           => 'Olo_Woo_Myaccount_Tile',
            'class-woo-comparison-tile.php'          => 'Olo_Woo_Comparison_Tile',
            'class-woo-wishlist-tile.php'             => 'Olo_Woo_Wishlist_Tile',
            'class-woo-cross-sells-tile.php'          => 'Olo_Woo_Cross_Sells_Tile',
            'class-woo-recently-viewed-tile.php'      => 'Olo_Woo_Recently_Viewed_Tile',
            'class-woo-product-bundle-tile.php'       => 'Olo_Woo_Product_Bundle_Tile',
            'class-woo-product-gallery-slider-tile.php' => 'Olo_Woo_Product_Gallery_Slider_Tile',
        ];
        foreach ( $woo_tiles as $file => $class ) {
            $path = OLO_PATH . 'includes/tiles/' . $file;
            if ( file_exists( $path ) ) {
                require_once $path;
                if ( class_exists( $class ) ) {
                    $manager->register_tile( new $class() );
                }
            }
        }
    }
} );

// Initialize plugin
add_action( 'plugins_loaded', function () {
    Olo_Builder::instance();
    Olo_Location_Single::instance();
    Olo_404_Integration::instance();

    // Form handler (public REST endpoint)
    $form_handler = new Olo_Form_Handler();
    $form_handler->init();

    // Form submissions dashboard
    Olo_Form_Submissions::init();

    // Newsletter (lista iscritti + endpoint REST dedicato)
    Olo_Newsletter::init();

    // Login form AJAX handlers
    if ( class_exists( 'Olo_Loginform_Tile' ) ) {
        Olo_Loginform_Tile::register_ajax_handlers();
    }

    // Custom code snippets (head/body/footer)
    Olo_Custom_Code::init();

    // Cursore magnetico (feature globale di tema/header — pagina Impostazioni nativa)
    require_once OLO_PATH . 'includes/class-magnetic-cursor.php';
    Olo_Magnetic_Cursor::init();

    // Asset optimizer (defer scripts, CSS minification)
    Olo_Asset_Optimizer::init();

    // Maintenance mode / Coming soon
    Olo_Maintenance_Mode::init();

    // Analytics event tracking
    Olo_Analytics_Tracking::init();

    // Critical CSS generation
    Olo_Critical_CSS::init();

    // A/B Testing framework
    Olo_AB_Testing::init();

    // Cookie consent / GDPR privacy bar
    Olo_Cookie_Consent::instance()->init();

    // Role Manager — builder access control
    Olobuild_Role_Manager::instance()->init();

    // SEO Settings — admin page, meta box, colonna SEO
    Olo_Seo_Settings::instance()->init();

    // SEO Redirects — redirect 301/302, monitor 404, IndexNow
    Olo_Seo_Redirects::instance()->init();

    // SEO Head — JSON-LD, Open Graph, canonical, robots
    Olo_Seo_Head::instance()->init();

    // Accessibility — skip-nav, ARIA, focus styles
    Olo_Accessibility::instance()->init();

    // Performance hints — preload, fetchpriority, video facade
    Olo_Performance_Hints::instance()->init();

    // Performance Settings — admin page (Critical CSS, Assets, Hints)
    Olo_Performance_Settings::instance()->init();

    // Global Popups — display conditions system
    Olo_Global_Popups::instance()->init();

    // Template Conditions — advanced AND/OR display conditions
    Olo_Template_Conditions::instance()->init();

    // White Label — rebrand plugin for clients
    Olo_White_Label::instance()->init();

    // WooCommerce comparison REST endpoint
    if ( class_exists( 'WooCommerce' ) ) {
        add_action( 'rest_api_init', [ 'Olo_Woo_Comparison_Tile', 'register_rest_routes' ] );
    }

    // Import/Export with media
    Olo_Site_Import_Export::instance()->init();

    // Tools page (unified Strumenti)
    Olo_Tools::instance()->init();

    // Debug bar (template tracking in admin toolbar)
    Olo_Debug_Bar::init();

    // WooCommerce Theme Builder integration
    Olo_Woo_Template_Integration::instance()->init();
} );
