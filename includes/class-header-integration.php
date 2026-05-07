<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Header_Integration {

    public function init() {
        add_filter( 'render_block', [ $this, 'replace_header_block' ], 10, 2 );
        add_filter( 'render_block', [ $this, 'hide_wp_post_title' ], 10, 2 );
        add_action( 'wp_enqueue_scripts', [ $this, 'maybe_enqueue_global_assets' ], 5 );
        add_filter( 'body_class', [ $this, 'add_header_body_class' ] );

        // Register nav menu location so Appearance → Menus works even with block themes
        add_action( 'after_setup_theme', [ $this, 'register_nav_menus' ], 20 );
    }

    /**
     * Register nav menu locations to enable the classic Menus admin on block themes.
     */
    public function register_nav_menus() {
        register_nav_menus( [
            'olo_header' => __( 'Header Olobuild', 'olobuild' ),
        ] );
    }

    /**
     * Intercept core/template-part with area=header and replace with Olobuild header.
     */
    public function replace_header_block( $html, $block ) {
        if ( $block['blockName'] !== 'core/template-part' ) {
            return $html;
        }

        $area = $block['attrs']['area'] ?? '';
        if ( $area !== 'header' ) {
            return $html;
        }

        $header_id = $this->resolve_active_header();

        if ( ! $header_id ) {
            return $html;
        }

        return $this->render_header( $header_id );
    }

    /**
     * Remove core/post-title block entirely when an Olobuild header is active.
     */
    public function hide_wp_post_title( $html, $block ) {
        if ( $block['blockName'] !== 'core/post-title' ) {
            return $html;
        }

        $header_id = $this->resolve_active_header();

        if ( ! $header_id ) {
            return $html;
        }

        return '';
    }

    /**
     * Risolve il template Header attivo secondo la priorità:
     *   1) override per-pagina (`_olo_header_id` post-meta)
     *   2) regole di visualizzazione (Olo_Template_Conditions)
     *   3) header globale (`olo_active_header` option)
     *
     * @return int  template_id o 0 se nessuno applicabile
     */
    private function resolve_active_header() {
        if ( is_singular() ) {
            $override = (int) get_post_meta( get_queried_object_id(), '_olo_header_id', true );
            if ( $override ) return $override;
        }
        $by_rules = (int) apply_filters( 'olo_resolve_template_id', 0, 'header' );
        if ( $by_rules ) return $by_rules;
        return (int) get_option( 'olo_active_header', 0 );
    }

    /**
     * Add body class when Olobuild header is active (CSS fallback).
     */
    public function add_header_body_class( $classes ) {
        if ( $this->resolve_active_header() ) {
            $classes[] = 'olo-has-header';
        }
        return $classes;
    }

    /**
     * Render the Olobuild header template.
     */
    public function render_header( $template_id ) {
        $db  = new Olo_Database();
        $tpl = $db->get_template( $template_id );

        if ( ! $tpl || $tpl['status'] !== 'published' ) {
            return '';
        }

        $renderer   = new Olo_Frontend_Renderer();
        $inner_html = $renderer->render_shortcode( [ 'id' => $template_id ] );

        // Detect header_mode from template data (look for navmenu element)
        $header_mode = $this->detect_header_mode( $tpl );

        // Inline CSS for header mode — nasconde titolo pagina WP e resetta margini
        $inline_css = '<style>';
        $inline_css .= 'header.olo-site-header { position: relative; z-index: 1000; overflow: visible !important; }';
        $inline_css .= '.wp-site-blocks { overflow: visible !important; }';
        if ( $header_mode === 'overlay' ) {
            $inline_css .= 'header.olo-site-header.olo-header-overlay + main { margin-block-start: 0 !important; margin-top: 0 !important; }';
            $inline_css .= 'header.olo-site-header.olo-header-overlay + main > .wp-block-group:first-child { display: none !important; }';
            $inline_css .= 'header.olo-site-header.olo-header-overlay + main > .wp-block-group:first-child + * { margin-block-start: 0 !important; margin-top: 0 !important; }';
            $inline_css .= 'header.olo-site-header:not(.olo-header-classic) + main { margin-block-start: 0 !important; margin-top: 0 !important; }';
            $inline_css .= 'header.olo-site-header:not(.olo-header-classic) + main > .wp-block-group:first-child { display: none !important; }';
        } else {
            // Classic: hide TT4 hero/title section + collapse gaps (same as overlay)
            $inline_css .= 'header.olo-site-header { margin-top: 0 !important; margin-bottom: 0 !important; }';
            $inline_css .= 'header.olo-site-header + main { margin-block-start: 0 !important; margin-top: 0 !important; }';
            $inline_css .= 'header.olo-site-header + main > .wp-block-group:first-child { display: none !important; }';
            $inline_css .= 'header.olo-site-header + main > .wp-block-group:first-child + * { margin-block-start: 0 !important; margin-top: 0 !important; }';
        }
        // Fallback body-level bulletproof: nasconde titolo E il wrapper/spacer del tema
        $inline_css .= '.olo-has-header .wp-block-post-title { display: none !important; }';
        $inline_css .= '.olo-has-header h1.wp-block-post-title { display: none !important; }';
        $inline_css .= '.olo-has-header main > .wp-block-group:first-child { display: none !important; }';
        $inline_css .= '.olo-has-header main { margin-top: 0 !important; margin-block-start: 0 !important; padding-top: 0 !important; padding-block-start: 0 !important; gap: 0 !important; }';
        $inline_css .= '.olo-has-header main > .entry-content:first-child, .olo-has-header main > .wp-block-post-content:first-child, .olo-has-header main > .wp-block-group:first-child + .entry-content, .olo-has-header main > .wp-block-group:first-child + .wp-block-post-content { margin-top: 0 !important; margin-block-start: 0 !important; padding-top: 0 !important; padding-block-start: 0 !important; }';
        $inline_css .= '</style>';

        $mode_class = ( $header_mode === 'classic' ) ? 'olo-header-classic' : 'olo-header-overlay';
        return '<header class="olo-site-header alignfull ' . $mode_class . '">' . $inline_css . $inner_html . '</header>';
    }

    /**
     * Walk the template tree to find a navmenu element and read its header_mode.
     */
    private function detect_header_mode( $tpl ) {
        $content = $tpl['content'] ?? '';
        $data    = is_string( $content ) ? json_decode( $content, true ) : $content;
        if ( ! is_array( $data ) ) {
            return 'overlay';
        }
        $mode = $this->find_navmenu_mode( $data );
        return $mode ?? 'overlay';
    }

    private function find_navmenu_mode( $nodes ) {
        foreach ( $nodes as $node ) {
            if ( ! is_array( $node ) ) continue;
            $type = $node['type'] ?? '';
            if ( $type === 'navmenu' || $type === 'megamenu' ) {
                return ( $node['settings']['header_mode'] ?? 'overlay' );
            }
            if ( ! empty( $node['children'] ) ) {
                $result = $this->find_navmenu_mode( $node['children'] );
                if ( $result !== null ) {
                    return $result;
                }
            }
        }
        return null;
    }

    /**
     * Enqueue UIkit + frontend CSS globally when a Olobuild header is active.
     */
    public function maybe_enqueue_global_assets() {
        $header_id = $this->resolve_active_header();
        if ( ! $header_id ) {
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
