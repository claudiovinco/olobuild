<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Footer_Integration {

    public function init() {
        add_filter( 'render_block', [ $this, 'replace_footer_block' ], 10, 2 );
        add_action( 'wp_enqueue_scripts', [ $this, 'maybe_enqueue_global_assets' ], 5 );
    }

    /**
     * Intercept core/template-part with area=footer and replace with Olobuilder footer.
     */
    public function replace_footer_block( $html, $block ) {
        if ( $block['blockName'] !== 'core/template-part' ) {
            return $html;
        }

        $area = $block['attrs']['area'] ?? '';
        if ( $area !== 'footer' ) {
            return $html;
        }

        // Per-page override, then global fallback
        $override = 0;
        if ( is_singular() ) {
            $override = (int) get_post_meta( get_queried_object_id(), '_olo_footer_id', true );
        }
        $footer_id = $override ?: (int) get_option( 'olo_active_footer', 0 );

        if ( ! $footer_id ) {
            return $html;
        }

        return $this->render_footer( $footer_id );
    }

    /**
     * Render the Olobuilder footer template.
     */
    public function render_footer( $template_id ) {
        $db  = new Olo_Database();
        $tpl = $db->get_template( $template_id );

        if ( ! $tpl || $tpl['status'] !== 'published' ) {
            return '';
        }

        $renderer   = new Olo_Frontend_Renderer();
        $inner_html = $renderer->render_shortcode( [ 'id' => $template_id ] );

        return '<footer class="olo-site-footer">' . $inner_html . '</footer>';
    }

    /**
     * Enqueue UIkit + frontend CSS globally when a Olobuilder footer is active.
     * Uses wp_style_is/wp_script_is to avoid double enqueue if header is also active.
     */
    public function maybe_enqueue_global_assets() {
        $override = 0;
        if ( is_singular() ) {
            $override = (int) get_post_meta( get_queried_object_id(), '_olo_footer_id', true );
        }
        $footer_id = $override ?: (int) get_option( 'olo_active_footer', 0 );
        if ( ! $footer_id ) {
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

        // Olobuilder frontend CSS
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
