<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Footer_Integration {

    public function init() {
        add_filter( 'render_block', [ $this, 'replace_footer_block' ], 10, 2 );
        add_action( 'wp_enqueue_scripts', [ $this, 'maybe_enqueue_global_assets' ], 5 );
        add_filter( 'body_class', [ $this, 'add_footer_body_class' ] );
    }

    /**
     * Add body class when Olobuild footer is active (sticky footer CSS).
     */
    public function add_footer_body_class( $classes ) {
        if ( $this->resolve_active_footer() ) {
            $classes[] = 'olo-has-footer';
        }
        return $classes;
    }

    /**
     * Risolve il template Footer attivo secondo la priorità:
     *   1) override per-pagina (`_olo_footer_id` post-meta)
     *   2) regole di visualizzazione (Olo_Template_Conditions)
     *   3) footer globale (`olo_active_footer` option)
     */
    private function resolve_active_footer() {
        if ( is_singular() ) {
            $override = (int) get_post_meta( get_queried_object_id(), '_olo_footer_id', true );
            if ( $override ) return $override;
        }
        $by_rules = (int) apply_filters( 'olo_resolve_template_id', 0, 'footer' );
        if ( $by_rules ) return $by_rules;
        return (int) get_option( 'olo_active_footer', 0 );
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

        $footer_id = $this->resolve_active_footer();

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

        return '<footer class="olo-site-footer alignfull" role="contentinfo">' . $inner_html . '</footer>';
    }

    /**
     * Enqueue UIkit + frontend CSS globally when a Olobuilder footer is active.
     * Uses wp_style_is/wp_script_is to avoid double enqueue if header is also active.
     */
    public function maybe_enqueue_global_assets() {
        $footer_id = $this->resolve_active_footer();
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
