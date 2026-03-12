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
            'olo-postgrid',
            'olo-proslider',
            'olo-map',
            'olo-livesearch',
            'olo-serviceresults',
            'olo-pdfviewer',
            'olo-progallery-lightbox',
            'olo-utils',
        ];
        if ( in_array( $handle, $defer_handles, true ) ) {
            if ( false === strpos( $tag, 'defer' ) ) {
                $tag = str_replace( ' src=', ' defer src=', $tag );
            }
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
            $minified = self::minify_css( $css );
            file_put_contents( $filepath, $minified );
        }

        return $cache_url . $filename;
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
     */
    public static function init() {
        // Defer frontend scripts
        add_filter( 'script_loader_tag', [ __CLASS__, 'defer_scripts' ], 10, 2 );

        // Clean cache on template save
        add_action( 'olo_template_saved', [ __CLASS__, 'warm_cache' ] );
    }
}
