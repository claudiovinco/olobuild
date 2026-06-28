<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Search_Results_Integration {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_filter( 'template_include', [ $this, 'maybe_override_search' ], 99 );
        add_action( 'wp_enqueue_scripts', [ $this, 'maybe_enqueue_global_assets' ], 5 );
    }

    /**
     * Override the search results template when an Olobuild search template is active.
     */
    public function maybe_override_search( $template ) {
        if ( ! is_search() ) {
            return $template;
        }

        $tpl_id = (int) get_option( 'olo_active_search', 0 );
        if ( ! $tpl_id ) {
            return $template;
        }

        // Verify template exists
        $db  = new Olobuild_Database();
        $tpl = $db->get_template( $tpl_id );
        if ( ! $tpl || ( $tpl['status'] !== 'published' && $tpl['status'] !== 'draft' ) ) {
            return $template;
        }

        $GLOBALS['olobuild_search_template_id'] = $tpl_id;

        return OLOBUILD_PATH . 'templates/search-template.php';
    }

    /**
     * Enqueue UIkit + frontend CSS globally when an Olobuild search template is active.
     */
    public function maybe_enqueue_global_assets() {
        if ( ! is_search() ) {
            return;
        }

        $tpl_id = (int) get_option( 'olo_active_search', 0 );
        if ( ! $tpl_id ) {
            return;
        }

        // UIkit CSS
        if ( ! wp_style_is( 'uikit-css', 'enqueued' ) ) {
            wp_enqueue_style(
                'uikit-css',
                OLOBUILD_URL . 'assets/vendor/uikit/css/uikit.min.css',
                [],
                '3.21.16'
            );
        }

        // UIkit JS
        if ( ! wp_script_is( 'uikit-js', 'enqueued' ) ) {
            wp_enqueue_script(
                'uikit-js',
                OLOBUILD_URL . 'assets/vendor/uikit/js/uikit.min.js',
                [],
                '3.21.16',
                true
            );
        }

        // UIkit Icons
        if ( ! wp_script_is( 'uikit-icons-js', 'enqueued' ) ) {
            wp_enqueue_script(
                'uikit-icons-js',
                OLOBUILD_URL . 'assets/vendor/uikit/js/uikit-icons.min.js',
                [ 'uikit-js' ],
                '3.21.16',
                true
            );
        }

        // Olobuild frontend CSS
        if ( ! wp_style_is( 'olo-frontend-css', 'enqueued' ) ) {
            wp_enqueue_style(
                'olo-frontend-css',
                OLOBUILD_URL . 'assets/css/frontend.css',
                [ 'uikit-css' ],
                OLOBUILD_VERSION
            );
        }

        // Style System CSS
        $style_css = Olobuild_Style_System::instance()->generate_css();
        if ( ! empty( $style_css ) ) {
            wp_add_inline_style( 'olo-frontend-css', $style_css );
        }
    }
}
