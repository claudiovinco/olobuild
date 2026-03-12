<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_404_Integration {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_filter( 'template_include', [ $this, 'maybe_override_404' ], 99 );
        add_action( 'wp_enqueue_scripts', [ $this, 'maybe_enqueue_global_assets' ], 5 );
    }

    /**
     * Override the 404 template when an Olobuild 404 template is active.
     */
    public function maybe_override_404( $template ) {
        if ( ! is_404() ) {
            return $template;
        }

        $tpl_id = (int) get_option( 'olo_active_404', 0 );
        if ( ! $tpl_id ) {
            return $template;
        }

        // Verify template exists
        $db  = new Olo_Database();
        $tpl = $db->get_template( $tpl_id );
        if ( ! $tpl || ( $tpl['status'] !== 'published' && $tpl['status'] !== 'draft' ) ) {
            return $template;
        }

        $GLOBALS['olo_404_template_id'] = $tpl_id;

        return OLO_PATH . 'templates/404-template.php';
    }

    /**
     * Enqueue UIkit + frontend CSS globally when an Olobuild 404 template is active.
     */
    public function maybe_enqueue_global_assets() {
        if ( ! is_404() ) {
            return;
        }

        $tpl_id = (int) get_option( 'olo_active_404', 0 );
        if ( ! $tpl_id ) {
            return;
        }

        // UIkit CSS
        if ( ! wp_style_is( 'uikit-css', 'enqueued' ) ) {
            wp_enqueue_style(
                'uikit-css',
                OLO_URL . 'assets/vendor/uikit/css/uikit.min.css',
                [],
                '3.21.16'
            );
        }

        // UIkit JS
        if ( ! wp_script_is( 'uikit-js', 'enqueued' ) ) {
            wp_enqueue_script(
                'uikit-js',
                OLO_URL . 'assets/vendor/uikit/js/uikit.min.js',
                [],
                '3.21.16',
                true
            );
        }

        // UIkit Icons
        if ( ! wp_script_is( 'uikit-icons-js', 'enqueued' ) ) {
            wp_enqueue_script(
                'uikit-icons-js',
                OLO_URL . 'assets/vendor/uikit/js/uikit-icons.min.js',
                [ 'uikit-js' ],
                '3.21.16',
                true
            );
        }

        // Olobuild frontend CSS
        if ( ! wp_style_is( 'olo-frontend-css', 'enqueued' ) ) {
            wp_enqueue_style(
                'olo-frontend-css',
                OLO_URL . 'assets/css/frontend.css',
                [ 'uikit-css' ],
                OLO_VERSION
            );
        }

        // Style System CSS
        $style_css = Olo_Style_System::instance()->generate_css();
        if ( ! empty( $style_css ) ) {
            wp_add_inline_style( 'olo-frontend-css', $style_css );
        }
    }
}
