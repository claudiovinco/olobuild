<?php
/**
 * Plugin Name: Olobuild
 * Plugin URI:  https://olotheme.com
 * Description: Professional holonic page builder for WordPress with a drag & drop tile grid system.
 * Version:     1.4.407
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

define( 'OLOBUILD_VERSION', '1.4.407' );
define( 'OLOBUILD_PATH', plugin_dir_path( __FILE__ ) );
define( 'OLOBUILD_URL', plugin_dir_url( __FILE__ ) );

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
if ( ! function_exists( 'str_ends_with' ) ) {
    function str_ends_with( string $haystack, string $needle ): bool {
        return '' === $needle || ( strlen( $haystack ) >= strlen( $needle ) && substr( $haystack, -strlen( $needle ) ) === $needle );
    }
}

/**
 * Helper globale per leggere preferenze stockmedia (Configurazione → Stock media → comportamento).
 * Usato dai 4 provider Olobuild_Unsplash/Pexels/Pixabay/Openverse per decidere download_local + optimize_on_download.
 */
function olobuild_stockmedia_behavior() {
    static $cached = null;
    if ( $cached !== null ) return $cached;
    $cached = wp_parse_args(
        get_option( 'olobuild_stockmedia_behavior', [] ) ?: [],
        [ 'preferred' => 'unsplash', 'download_local' => true, 'optimize_on_download' => false ]
    );
    return $cached;
}

/**
 * Valida un URL remoto fornito dal client prima di un download server-side
 * (endpoint stock-media). Anti-SSRF: solo http/https, wp_http_validate_url()
 * (blocca loopback, IP privati e porte non standard) e, se indicata, allowlist
 * di domini del provider (host esatto o sottodominio).
 *
 * @param string $url           URL richiesto dal client.
 * @param array  $allowed_hosts Domini consentiti (es. [ 'pixabay.com' ]); vuoto = solo check generici.
 * @return bool
 */
function olobuild_validate_remote_media_url( $url, $allowed_hosts = [] ) {
    if ( ! is_string( $url ) || '' === $url ) {
        return false;
    }
    $url = esc_url_raw( $url, [ 'http', 'https' ] );
    if ( ! $url || ! wp_http_validate_url( $url ) ) {
        return false;
    }
    if ( $allowed_hosts ) {
        $host = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );
        foreach ( $allowed_hosts as $allowed ) {
            $allowed = strtolower( $allowed );
            if ( $host === $allowed || str_ends_with( $host, '.' . $allowed ) ) {
                return true;
            }
        }
        return false;
    }
    return true;
}

/**
 * Neutralizza la CSV formula injection negli export: un valore che inizia con
 * = + - @ (o tab/CR) verrebbe eseguito come formula aprendo il CSV in
 * Excel/LibreOffice. Prefisso apostrofo = testo letterale per i fogli di calcolo.
 */
function olobuild_csv_safe( $value ) {
    if ( is_string( $value ) && $value !== '' && strpbrk( $value[0], "=+-@\t\r" ) !== false ) {
        return "'" . $value;
    }
    return $value;
}

/**
 * Import di temi/siti/template disabilitato? Pensato per gli ambienti sandbox
 * condivisi (es. try.olotheme.com) dove più utenti provano lo stesso sito: un
 * import riscriverebbe opzioni GLOBALI (olo_active_header/footer/404, olo_styles,
 * page_on_front) rompendo la sandbox per tutti.
 *
 * Si attiva con `define( 'OLOBUILD_DISABLE_IMPORTS', true );` in wp-config.php — per-sito
 * e non disattivabile dalla UI da un utente trial. Inerte ovunque non sia definita.
 */
function olobuild_imports_disabled() {
    return defined( 'OLOBUILD_DISABLE_IMPORTS' ) && OLOBUILD_DISABLE_IMPORTS;
}

/** WP_Error 403 standard per gli endpoint REST di import quando disabilitati. */
function olobuild_imports_disabled_error() {
    return new WP_Error(
        'olobuild_imports_disabled',
        __( 'L\'importazione di temi e template è disabilitata su questo sito.', 'olobuild' ),
        [ 'status' => 403 ]
    );
}

/**
 * Converte un file immagine in WebP usando GD o Imagick (se disponibili).
 * Restituisce il path del WebP, o false se la conversione fallisce.
 *
 * @param string $source_path Path al file sorgente.
 * @param int    $quality     Qualità 0-100.
 * @return string|false Path del WebP creato (rimpiazza source) oppure false.
 */
function olobuild_convert_to_webp( $source_path, $quality = 82 ) {
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
        $mo = OLOBUILD_PATH . 'languages/olobuild-' . $locale . '.mo';
        if ( ! file_exists( $mo ) ) {
            $mo = OLOBUILD_PATH . 'languages/olobuild-en_US.mo';
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
function olobuild_sanitize_svg( $svg ) {
    if ( empty( $svg ) ) return '';

    // Primary: use DOMDocument for proper XML parsing (prevents XXE, handles encodings)
    if ( class_exists( 'DOMDocument' ) ) {
        return olobuild_sanitize_svg_dom( $svg );
    }

    // Fallback: regex-based sanitization for hosts without DOMDocument
    return olobuild_sanitize_svg_regex( $svg );
}

/**
 * SVG sanitization via DOMDocument — XML-aware, handles encoded entities.
 */
function olobuild_sanitize_svg_dom( $svg ) {
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
function olobuild_sanitize_svg_regex( $svg ) {
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
function olobuild_t( $text ) {
    $map = olobuild_get_translations_map();
    if ( isset( $map[ $text ] ) ) {
        return $map[ $text ];
    }
    // Fallback gettext: con locale non-italiano le stringhe sorgente (IT)
    // escono dai .mo bundled — es. sito con lingua default EN via OLOlang,
    // dove la mappa DB è vuota per la lingua default.
    return __( $text, 'olobuild' ); // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText -- runtime catalog lookup by design (olobuild_t wraps source strings)
}

/**
 * Mappa traduzioni originale => tradotto per la lingua corrente.
 * Cache statica nella request. Array vuoto se siamo nella lingua default
 * o se Olo Lang non è attivo.
 *
 * @return array<string,string>
 */
function olobuild_get_translations_map() {
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
function olobuild_current_locale() {
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

// Migrazione una-tantum del prefisso dati olo_ → olobuild_ (opzioni + tabelle custom).
// DEVE girare PRIMA di qualunque classe che legga le opzioni con il nuovo prefisso.
require_once OLOBUILD_PATH . 'includes/class-prefix-migration.php';
Olobuild_Prefix_Migration::maybe_migrate();

require_once OLOBUILD_PATH . 'includes/class-database.php';
require_once OLOBUILD_PATH . 'includes/class-tile-manager.php';
require_once OLOBUILD_PATH . 'includes/class-rest-api.php';
require_once OLOBUILD_PATH . 'includes/class-dynamic-content.php';
require_once OLOBUILD_PATH . 'includes/class-style-system.php';
require_once OLOBUILD_PATH . 'includes/class-font-host.php';
require_once OLOBUILD_PATH . 'includes/class-css-builder.php';
require_once OLOBUILD_PATH . 'includes/class-animation-builder.php';
require_once OLOBUILD_PATH . 'includes/class-frontend-renderer.php';
require_once OLOBUILD_PATH . 'includes/class-asset-optimizer.php';
require_once OLOBUILD_PATH . 'includes/class-page-css.php';
require_once OLOBUILD_PATH . 'includes/class-uikit-subset.php';
require_once OLOBUILD_PATH . 'includes/class-template-library.php';
require_once OLOBUILD_PATH . 'includes/class-page-integration.php';
require_once OLOBUILD_PATH . 'includes/class-header-integration.php';
require_once OLOBUILD_PATH . 'includes/class-footer-integration.php';
require_once OLOBUILD_PATH . 'includes/class-single-integration.php';
require_once OLOBUILD_PATH . 'includes/class-archive-integration.php';
require_once OLOBUILD_PATH . 'includes/class-404-integration.php';
require_once OLOBUILD_PATH . 'includes/class-search-results-integration.php';
require_once OLOBUILD_PATH . 'includes/class-location-single.php';
require_once OLOBUILD_PATH . 'includes/class-form-handler.php';
require_once OLOBUILD_PATH . 'includes/class-unsplash.php';
require_once OLOBUILD_PATH . 'includes/class-pexels.php';
require_once OLOBUILD_PATH . 'includes/class-pixabay.php';
require_once OLOBUILD_PATH . 'includes/class-openverse.php';
require_once OLOBUILD_PATH . 'includes/class-freesound.php';
require_once OLOBUILD_PATH . 'includes/class-media-search.php';
require_once OLOBUILD_PATH . 'includes/class-custom-fonts.php';
require_once OLOBUILD_PATH . 'includes/class-custom-code.php';
require_once OLOBUILD_PATH . 'includes/class-form-submissions.php';
require_once OLOBUILD_PATH . 'includes/class-newsletter.php';
require_once OLOBUILD_PATH . 'includes/class-maintenance-mode.php';
require_once OLOBUILD_PATH . 'includes/class-analytics-tracking.php';
require_once OLOBUILD_PATH . 'includes/class-diagnostics.php';
Olobuild_Diagnostics::init();
// OLOsecurity è un plugin INDIPENDENTE (repo claudiovinco/olosecurity) dalla
// v1.4.227: OLObuild non bundla più i moduli di sicurezza. Se installato, si
// aggancia da solo al menu di OLObuild.
require_once OLOBUILD_PATH . 'includes/class-critical-css.php';
require_once OLOBUILD_PATH . 'includes/class-ab-testing.php';
require_once OLOBUILD_PATH . 'includes/class-cookie-consent.php';
require_once OLOBUILD_PATH . 'includes/class-role-manager.php';
require_once OLOBUILD_PATH . 'includes/class-site-export.php';
require_once OLOBUILD_PATH . 'includes/class-ai-assistant.php';
require_once OLOBUILD_PATH . 'includes/class-seo-head.php';
require_once OLOBUILD_PATH . 'includes/class-seo-settings.php';
require_once OLOBUILD_PATH . 'includes/class-seo-redirects.php';
require_once OLOBUILD_PATH . 'includes/class-global-popups.php';
require_once OLOBUILD_PATH . 'includes/class-template-conditions.php';
require_once OLOBUILD_PATH . 'includes/class-accessibility.php';
require_once OLOBUILD_PATH . 'includes/class-performance-hints.php';
require_once OLOBUILD_PATH . 'includes/class-performance-settings.php';
require_once OLOBUILD_PATH . 'includes/cache/class-full-page-cache.php';
Olobuild_FullPage_Cache::init();
require_once OLOBUILD_PATH . 'includes/class-white-label.php';
require_once OLOBUILD_PATH . 'includes/class-site-import-export.php';
require_once OLOBUILD_PATH . 'includes/class-tools.php';
require_once OLOBUILD_PATH . 'includes/class-olo-lang-bridge.php';
Olobuild_Lang_Bridge::init();
require_once OLOBUILD_PATH . 'includes/class-debug-bar.php';
require_once OLOBUILD_PATH . 'includes/class-woo-template-integration.php';
require_once OLOBUILD_PATH . 'includes/class-olo-builder.php';

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
    $new_templates  = $wpdb->prefix . 'olobuild_templates';
    $new_revisions  = $wpdb->prefix . 'olobuild_revisions';

    // Migrazione one-shot all'attivazione su tabelle custom del plugin (olo_templates/olo_revisions)
    // e su {$wpdb->postmeta}: query dirette $wpdb legittime, nessun equivalente WP_Query; risultato
    // non cacheabile (operazione DDL/UPDATE una tantum). I nomi tabella interpolati derivano SOLO da
    // $wpdb->prefix + suffissi letterali (nessun input utente); RENAME TABLE non ammette placeholder per
    // gli identificatori. Tutti i VALORI passano da $wpdb->prepare con placeholder.
    // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
    // Rename tables if old ones exist and new ones don't
    if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $old_templates ) ) && ! $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $new_templates ) ) ) {
        $wpdb->query( "RENAME TABLE `{$old_templates}` TO `{$new_templates}`" );
    }
    if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $old_revisions ) ) && ! $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $new_revisions ) ) ) {
        $wpdb->query( "RENAME TABLE `{$old_revisions}` TO `{$new_revisions}`" );
    }

    // Create/update tables AFTER migration so dbDelta doesn't interfere with RENAME
    $db = new Olobuild_Database();
    $db->create_tables();

    // Form submissions table
    Olobuild_Form_Submissions::create_table();
    Olobuild_Newsletter::create_table();

    // Security Sentinel: fotografa lo stato "buono" dei file come baseline integrità.
    if ( class_exists( 'Olo_Security_Sentinel' ) ) {
        Olo_Security_Sentinel::build_baseline();
    }

    // Migrate options
    $option_map = [
        'mosaic_active_header' => 'olobuild_active_header',
        'mosaic_active_footer' => 'olobuild_active_footer',
        'mosaic_styles'        => 'olobuild_styles',
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
        $new_key = "olobuild_active_single_{$pt}";
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
    // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter

    // Migrate nav menu location assignment
    $locations = get_theme_mod( 'nav_menu_locations', [] );
    if ( isset( $locations['mosaic_header'] ) && ! isset( $locations['olo_header'] ) ) {
        $locations['olo_header'] = $locations['mosaic_header'];
        unset( $locations['mosaic_header'] );
        set_theme_mod( 'nav_menu_locations', $locations );
    }

    // Set transient for first-run wizard redirect
    if ( ! get_option( 'olobuild_setup_complete' ) ) {
        set_transient( 'olo_activating', true, 60 );
    }

    // Full-page cache: se il toggle è già attivo, reinstalla il drop-in (utile dopo un update).
    if ( class_exists( 'Olobuild_FullPage_Cache' ) ) {
        Olobuild_FullPage_Cache::on_plugin_activate();
    }
} );

// Deactivation hook
register_deactivation_hook( __FILE__, function () {
    delete_transient( 'olo_builder_activated' );
    wp_clear_scheduled_hook( 'olo_weekly_cleanup' );
    wp_clear_scheduled_hook( 'olo_sentinel_scan' );
    // Full-page cache: rimuovi il drop-in e il WP_CACHE nostri (e svuota), per non
    // lasciare un advanced-cache.php orfano quando OLObuild è disattivato.
    if ( class_exists( 'Olobuild_FullPage_Cache' ) ) {
        Olobuild_FullPage_Cache::on_plugin_deactivate();
    }
} );

// Weekly cron for orphaned revision cleanup
add_action( 'olo_weekly_cleanup', function() {
    $db = Olobuild_Database::instance();
    $db->cleanup_orphaned_revisions();
} );
if ( ! wp_next_scheduled( 'olo_weekly_cleanup' ) ) {
    wp_schedule_event( time(), 'weekly', 'olo_weekly_cleanup' );
}

// Setup Wizard (first-run experience)
require_once OLOBUILD_PATH . 'includes/class-setup-wizard.php';
( new Olobuild_Setup_Wizard() )->init();

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

// Google Fonts: self-hosted via Olobuild_Font_Host (serviti da /uploads), quindi
// nessun preconnect verso i domini Google — il visitatore non li contatta.

// Custom Fonts @font-face CSS — only if custom fonts exist
add_action( 'wp_head', function() {
    $fonts = get_option( 'olobuild_custom_fonts', [] );
    if ( empty( $fonts ) ) return;
    $css = Olobuild_Custom_Fonts::generate_css();
    if ( $css ) {
        echo '<style id="olo-custom-fonts">' . $css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- @font-face CSS generated by Olobuild_Custom_Fonts::generate_css(), which escapes font names and file URLs internally (esc_attr/esc_url)
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
add_action( 'olobuild_register_external_tiles', function ( $manager ) {
    require_once OLOBUILD_PATH . 'includes/tiles/class-readingtime-tile.php';
    $manager->register_tile( new Olobuild_Readingtime_Tile() );

    require_once OLOBUILD_PATH . 'includes/tiles/class-darkmode-tile.php';
    $manager->register_tile( new Olobuild_Darkmode_Tile() );

    require_once OLOBUILD_PATH . 'includes/tiles/class-queryloop-tile.php';
    $manager->register_tile( new Olobuild_Queryloop_Tile() );

    require_once OLOBUILD_PATH . 'includes/tiles/class-portfolio-tile.php';
    $manager->register_tile( new Olobuild_Portfolio_Tile() );

    require_once OLOBUILD_PATH . 'includes/tiles/class-pagetitlebar-tile.php';
    $manager->register_tile( new Olobuild_Pagetitlebar_Tile() );

    require_once OLOBUILD_PATH . 'includes/tiles/class-lightbox-tile.php';
    $manager->register_tile( new Olobuild_Lightbox_Tile() );

    require_once OLOBUILD_PATH . 'includes/tiles/class-floatingpanel-tile.php';
    $manager->register_tile( new Olobuild_Floatingpanel_Tile() );

    require_once OLOBUILD_PATH . 'includes/tiles/class-mobilebar-tile.php';
    $manager->register_tile( new Olobuild_Mobilebar_Tile() );

    require_once OLOBUILD_PATH . 'includes/tiles/class-svganimator-tile.php';
    $manager->register_tile( new Olobuild_Svganimator_Tile() );

    require_once OLOBUILD_PATH . 'includes/tiles/class-newsletter-tile.php';
    $manager->register_tile( new Olobuild_Newsletter_Tile() );

    require_once OLOBUILD_PATH . 'includes/tiles/class-viewer360-tile.php';
    $manager->register_tile( new Olobuild_Viewer360_Tile() );

    // WooCommerce tiles — only load if WooCommerce is active
    if ( class_exists( 'WooCommerce' ) ) {
        $woo_tiles = [
            'class-woo-products-tile.php'           => 'Olobuild_Woo_Products_Tile',
            'class-woo-minicart-tile.php'           => 'Olobuild_Woo_Minicart_Tile',
            'class-woo-price-tile.php'              => 'Olobuild_Woo_Price_Tile',
            'class-woo-addtocart-tile.php'          => 'Olobuild_Woo_Addtocart_Tile',
            'class-woo-categories-tile.php'         => 'Olobuild_Woo_Categories_Tile',
            'class-woo-rating-tile.php'             => 'Olobuild_Woo_Rating_Tile',
            'class-woo-product-title-tile.php'      => 'Olobuild_Woo_Product_Title_Tile',
            'class-woo-product-image-tile.php'      => 'Olobuild_Woo_Product_Image_Tile',
            'class-woo-product-description-tile.php'=> 'Olobuild_Woo_Product_Description_Tile',
            'class-woo-product-meta-tile.php'       => 'Olobuild_Woo_Product_Meta_Tile',
            'class-woo-product-stock-tile.php'      => 'Olobuild_Woo_Product_Stock_Tile',
            'class-woo-product-tabs-tile.php'       => 'Olobuild_Woo_Product_Tabs_Tile',
            'class-woo-related-tile.php'            => 'Olobuild_Woo_Related_Tile',
            'class-woo-upsells-tile.php'            => 'Olobuild_Woo_Upsells_Tile',
            'class-woo-cart-tile.php'               => 'Olobuild_Woo_Cart_Tile',
            'class-woo-checkout-tile.php'           => 'Olobuild_Woo_Checkout_Tile',
            'class-woo-order-tracking-tile.php'     => 'Olobuild_Woo_Order_Tracking_Tile',
            'class-woo-breadcrumbs-tile.php'        => 'Olobuild_Woo_Breadcrumbs_Tile',
            'class-woo-notices-tile.php'            => 'Olobuild_Woo_Notices_Tile',
            'class-woo-product-navigation-tile.php' => 'Olobuild_Woo_Product_Navigation_Tile',
            'class-woo-sale-badge-tile.php'         => 'Olobuild_Woo_Sale_Badge_Tile',
            'class-woo-product-filter-tile.php'     => 'Olobuild_Woo_Product_Filter_Tile',
            'class-woo-quickview-tile.php'          => 'Olobuild_Woo_Quickview_Tile',
            'class-woo-checkout-multistep-tile.php'  => 'Olobuild_Woo_Checkout_Multistep_Tile',
            'class-woo-myaccount-tile.php'           => 'Olobuild_Woo_Myaccount_Tile',
            'class-woo-comparison-tile.php'          => 'Olobuild_Woo_Comparison_Tile',
            'class-woo-wishlist-tile.php'             => 'Olobuild_Woo_Wishlist_Tile',
            'class-woo-cross-sells-tile.php'          => 'Olobuild_Woo_Cross_Sells_Tile',
            'class-woo-recently-viewed-tile.php'      => 'Olobuild_Woo_Recently_Viewed_Tile',
            'class-woo-product-bundle-tile.php'       => 'Olobuild_Woo_Product_Bundle_Tile',
            'class-woo-product-gallery-slider-tile.php' => 'Olobuild_Woo_Product_Gallery_Slider_Tile',
        ];
        foreach ( $woo_tiles as $file => $class ) {
            $path = OLOBUILD_PATH . 'includes/tiles/' . $file;
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
    Olobuild_Builder::instance();
    Olobuild_Location_Single::instance();
    Olobuild_404_Integration::instance();

    // Form handler (public REST endpoint)
    $form_handler = new Olobuild_Form_Handler();
    $form_handler->init();

    // Form submissions dashboard
    Olobuild_Form_Submissions::init();

    // Newsletter (lista iscritti + endpoint REST dedicato)
    Olobuild_Newsletter::init();

    // Login form AJAX handlers
    if ( class_exists( 'Olobuild_Loginform_Tile' ) ) {
        Olobuild_Loginform_Tile::register_ajax_handlers();
    }

    // Custom code snippets (head/body/footer)
    Olobuild_Custom_Code::init();

    // Cursore magnetico (feature globale di tema/header — pagina Impostazioni nativa)
    require_once OLOBUILD_PATH . 'includes/class-magnetic-cursor.php';
    Olobuild_Magnetic_Cursor::init();

    // HUD mirino (feature globale di tema — crosshair + coordinate + sezione corrente)
    require_once OLOBUILD_PATH . 'includes/class-cursor-hud.php';
    Olobuild_Cursor_Hud::init();

    // Asset optimizer (defer scripts, CSS minification)
    Olobuild_Asset_Optimizer::init();

    // CSS per-tile (safety net + invalidazione; lo swap vive in Asset_Optimizer)
    Olobuild_Page_CSS::init();

    // UIkit subset auto-appreso (apprendimento nel buffer di Performance_Hints)
    Olobuild_Uikit_Subset::init();

    // Maintenance mode / Coming soon
    Olobuild_Maintenance_Mode::init();

    // Analytics event tracking
    Olobuild_Analytics_Tracking::init();

    // Critical CSS generation
    Olobuild_Critical_CSS::init();

    // A/B Testing framework
    Olobuild_AB_Testing::init();

    // Cookie consent / GDPR privacy bar
    Olobuild_Cookie_Consent::instance()->init();

    // Role Manager — builder access control
    Olobuild_Role_Manager::instance()->init();

    // SEO Settings — admin page, meta box, colonna SEO
    Olobuild_Seo_Settings::instance()->init();

    // SEO Redirects — redirect 301/302, monitor 404, IndexNow
    Olobuild_Seo_Redirects::instance()->init();

    // SEO Head — JSON-LD, Open Graph, canonical, robots
    Olobuild_Seo_Head::instance()->init();

    // Accessibility — skip-nav, ARIA, focus styles
    Olobuild_Accessibility::instance()->init();

    // Performance hints — preload, fetchpriority, video facade
    Olobuild_Performance_Hints::instance()->init();

    // Performance Settings — admin page (Critical CSS, Assets, Hints)
    Olobuild_Performance_Settings::instance()->init();

    // Global Popups — display conditions system
    Olobuild_Global_Popups::instance()->init();

    // Template Conditions — advanced AND/OR display conditions
    Olobuild_Template_Conditions::instance()->init();

    // White Label — rebrand plugin for clients
    Olobuild_White_Label::instance()->init();

    // WooCommerce comparison REST endpoint
    if ( class_exists( 'WooCommerce' ) ) {
        add_action( 'rest_api_init', [ 'Olobuild_Woo_Comparison_Tile', 'register_rest_routes' ] );
    }

    // Import/Export with media
    Olobuild_Site_Import_Export::instance()->init();

    // Tools page (unified Strumenti)
    Olobuild_Tools::instance()->init();

    // Debug bar (template tracking in admin toolbar)
    Olobuild_Debug_Bar::init();

    // WooCommerce Theme Builder integration
    Olobuild_Woo_Template_Integration::instance()->init();
} );
