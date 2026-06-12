<?php
/**
 * Olobuild Asset Optimizer
 *
 * CSS minification, deferred JS, aggregated inline styles.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Olo_Asset_Optimizer {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Minify CSS string (remove comments, whitespace, newlines).
     */
    public static function minify_css( $css ) {
        if ( empty( $css ) ) return '';
        // Remove comments
        $css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
        // Remove spaces around selectors/braces
        $css = preg_replace( '/\s*([{}:;,>+~])\s*/', '$1', $css );
        // Remove remaining whitespace
        $css = preg_replace( '/\s+/', ' ', $css );
        // Trim
        return trim( $css );
    }

    /**
     * Add defer attribute to non-critical script tags.
     *
     * @param string $tag Script HTML tag
     * @param string $handle WP script handle
     * @return string Modified tag
     */
    public static function defer_scripts( $tag, $handle ) {
        // Only defer olobuild frontend scripts (not builder scripts)
        $defer_handles = [
            'olo-frontend',
            'olo-map',
            'olo-livesearch',
            'olo-serviceresults',
            'olo-pdfviewer',
        ];
        if ( in_array( $handle, $defer_handles, true ) ) {
            if ( false === strpos( $tag, 'defer' ) ) {
                $tag = str_replace( ' src=', ' defer src=', $tag );
            }
        }
        return $tag;
    }

    /**
     * Add type="module" to ES module scripts.
     * Modules are deferred by spec, so defer attribute is removed.
     */
    public static function module_scripts( $tag, $handle ) {
        $module_handles = [
            'olo-utils',
            'olo-postgrid-js',
            'olo-proslider-js',
            'olo-servicesearch-js',
            'olo-progallery-lightbox-js',
            'olo-svganimator-js',
            'olo-bezier-parallax-js',
        ];
        if ( in_array( $handle, $module_handles, true ) ) {
            $tag = str_replace( ' src=', ' type="module" src=', $tag );
            $tag = str_replace( ' defer', '', $tag );
        }
        return $tag;
    }

    /**
     * Generate a cached CSS file from inline styles.
     *
     * @param string $css Raw CSS
     * @param string $template_id Template identifier
     * @return string|false URL of cached file, or false on failure
     */
    public static function cache_css( $css, $template_id ) {
        if ( empty( $css ) ) return false;

        // Check user preference: se css_cache_files è off, fallback a inline.
        $opt = class_exists( 'Olo_Performance_Settings' )
            ? Olo_Performance_Settings::get_option()
            : [ 'css_cache_files' => true, 'minify_css' => true ];
        if ( empty( $opt['css_cache_files'] ) ) {
            return false;
        }

        $upload_dir = wp_upload_dir();
        $cache_dir  = $upload_dir['basedir'] . '/olobuild-cache/';
        $cache_url  = $upload_dir['baseurl'] . '/olobuild-cache/';

        if ( ! is_dir( $cache_dir ) ) {
            wp_mkdir_p( $cache_dir );
        }

        $hash     = md5( $css );
        $filename = "olo-{$template_id}-{$hash}.css";
        $filepath = $cache_dir . $filename;

        if ( ! file_exists( $filepath ) ) {
            $payload = ! empty( $opt['minify_css'] ) ? self::minify_css( $css ) : $css;
            file_put_contents( $filepath, $payload );
        }

        return $cache_url . $filename;
    }

    /**
     * Serve a minified cached copy of a static plugin CSS file.
     * Invalidated by file mtime + OLO_VERSION. Falls back to false (= original URL)
     * if the file is unreadable, the write fails, or the minified output looks
     * corrupted (brace count mismatch).
     *
     * @param string $rel_path Path relative to plugin root (e.g. 'assets/css/frontend.css').
     * @param string $slug     Cache slug (e.g. 'frontend').
     * @return string|false URL of minified file, or false to keep the original.
     */
    public static function cache_static_css( $rel_path, $slug ) {
        $src_path = OLO_PATH . $rel_path;
        if ( ! is_readable( $src_path ) ) {
            return false;
        }

        $mtime      = (int) filemtime( $src_path );
        $upload_dir = wp_upload_dir();
        $cache_dir  = $upload_dir['basedir'] . '/olobuild-cache/';
        $cache_url  = $upload_dir['baseurl'] . '/olobuild-cache/';
        $slug       = sanitize_key( $slug );
        $hash       = substr( md5( $rel_path . '|' . $mtime . '|' . OLO_VERSION ), 0, 12 );
        $filename   = "olo-static-{$slug}-{$hash}.css";
        $filepath   = $cache_dir . $filename;

        if ( ! file_exists( $filepath ) ) {
            if ( ! is_dir( $cache_dir ) ) {
                wp_mkdir_p( $cache_dir );
            }
            $css = file_get_contents( $src_path );
            if ( false === $css ) {
                return false;
            }
            $min = self::minify_css( $css );
            // Sanity check: il minify non deve alterare la struttura del CSS.
            if ( substr_count( $min, '{' ) !== substr_count( $css, '{' )
                || substr_count( $min, '}' ) !== substr_count( $css, '}' ) ) {
                return false;
            }
            // Rimuovi copie minificate di versioni precedenti dello stesso file.
            $old = glob( $cache_dir . "olo-static-{$slug}-*.css" );
            if ( $old ) {
                foreach ( $old as $f ) {
                    @unlink( $f );
                }
            }
            if ( false === file_put_contents( $filepath, $min ) ) {
                return false;
            }
        }

        return $cache_url . $filename;
    }

    /**
     * style_loader_src filter: swap the static frontend.css with its minified copy.
     */
    public static function swap_static_css( $src, $handle ) {
        if ( 'olo-frontend-css' !== $handle || is_admin() ) {
            return $src;
        }
        $url = self::cache_static_css( 'assets/css/frontend.css', 'frontend' );
        return $url ? $url : $src;
    }

    /**
     * Clean old cached CSS files for a template.
     */
    public static function clean_cache( $template_id ) {
        $upload_dir = wp_upload_dir();
        $cache_dir  = $upload_dir['basedir'] . '/olobuild-cache/';
        if ( ! is_dir( $cache_dir ) ) return;

        $files = glob( $cache_dir . "olo-{$template_id}-*.css" );
        if ( $files ) {
            foreach ( $files as $file ) {
                @unlink( $file );
            }
        }
    }

    /**
     * Try to serve cached CSS via <link> tag instead of inline <style>.
     * Returns HTML string: either <link> tag or inline <style> fallback.
     *
     * @param string $css       Raw CSS content.
     * @param string $tpl_id    Template ID.
     * @return string HTML to output.
     */
    public static function serve_css( $css, $tpl_id ) {
        if ( empty( trim( $css ) ) ) return '';

        // Try to cache to file
        $url = self::cache_css( $css, $tpl_id );
        if ( $url ) {
            $ver = OLO_VERSION;
            return '<link rel="stylesheet" href="' . esc_url( $url ) . '?v=' . $ver . '" media="all" />';
        }

        // Fallback: inline
        return '<style class="olo-hover-styles">' . self::minify_css( $css ) . '</style>';
    }

    /**
     * Warm cache: regenerate cached CSS for a given template on save.
     * Called via olo_template_saved hook.
     *
     * @param int $template_id Template ID.
     */
    public static function warm_cache( $template_id ) {
        // Clean old cached files first
        self::clean_cache( $template_id );

        // Pre-render the template to generate CSS
        if ( ! class_exists( 'Olo_Database' ) ) return;
        $db  = new Olo_Database();
        $tpl = $db->get_template( $template_id );
        if ( ! $tpl || empty( $tpl->content ) ) return;

        $content = json_decode( $tpl->content, true );
        if ( empty( $content ) || ! is_array( $content ) ) return;

        // The actual CSS will be cached on first page view.
        // We just ensure old cache is cleared so fresh CSS is generated.
        // Full pre-rendering requires WP context (shortcodes, widgets) which
        // may not be available during REST save. Clearing is sufficient.
    }

    /**
     * Clean ALL olobuild cache files (flush entire cache).
     */
    public static function flush_all_cache() {
        $upload_dir = wp_upload_dir();
        $cache_dir  = $upload_dir['basedir'] . '/olobuild-cache/';
        if ( ! is_dir( $cache_dir ) ) return;

        $files = glob( $cache_dir . 'olo-*.css' );
        if ( $files ) {
            foreach ( $files as $file ) {
                @unlink( $file );
            }
        }
    }

    /**
     * Initialize optimizer hooks.
     * I flag defer_js / css_cache_files / minify_css sono gestiti da Olo_Performance_Settings.
     */
    public static function init() {
        $opt = class_exists( 'Olo_Performance_Settings' )
            ? Olo_Performance_Settings::get_option()
            : [ 'defer_js' => true, 'minify_css' => true ];

        // Defer frontend scripts solo se l'utente ha il flag attivo
        if ( ! empty( $opt['defer_js'] ) ) {
            add_filter( 'script_loader_tag', [ __CLASS__, 'defer_scripts' ], 10, 2 );
        }

        // Servi frontend.css minificato (copia cache invalidata su mtime+versione)
        if ( ! empty( $opt['minify_css'] ) ) {
            add_filter( 'style_loader_src', [ __CLASS__, 'swap_static_css' ], 10, 2 );
        }
        // ES module scripts: sempre attivo (modules sono deferred by spec, non c'è scelta)
        add_filter( 'script_loader_tag', [ __CLASS__, 'module_scripts' ], 11, 2 );

        // Clean cache on template save
        add_action( 'olo_template_saved', [ __CLASS__, 'warm_cache' ] );
    }
}
