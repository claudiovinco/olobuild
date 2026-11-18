<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Footer_Integration {

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
     *   2) regole di visualizzazione (Olobuild_Template_Conditions)
     *   3) footer globale (`olobuild_active_footer` option)
     * La catena è una sola, condivisa col builder: Olobuild_Template_Conditions::resolve_zone().
     */
    private function resolve_active_footer() {
        $zone = Olobuild_Template_Conditions::resolve_zone( 'footer' );
        return (int) $zone['id'];
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

        // Skip rendering quando il builder sta editando il template footer
        // (evita doppio render del footer nel preview iframe).
        if ( defined( 'OLOBUILD_BUILDER_EDITING_FOOTER' ) && OLOBUILD_BUILDER_EDITING_FOOTER ) {
            return '';
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
        $db  = new Olobuild_Database();
        $tpl = $db->get_template( $template_id );

        if ( ! $tpl || $tpl['status'] !== 'published' ) {
            return '';
        }

        $renderer   = new Olobuild_Frontend_Renderer();
        $inner_html = $renderer->render_shortcode( [ 'id' => $template_id ] );

        // «Si scopre da sotto» (1.4.1218, impostazione del template): il piè di pagina resta fermo in fondo e la pagina,
        // col suo fondo, scorre via scoprendolo. Lo script lo accende solo se il piè di pagina sta nella finestra.
        $scopre = ! empty( $tpl['settings']['piede_scopre'] );
        return '<footer class="olo-site-footer alignfull' . ( $scopre ? ' olo-piede-scopre' : '' ) . '" role="contentinfo">' . $inner_html . '</footer>'
            . ( $scopre ? self::script_scopre() : '' );
    }

    /**
     * Il piè di pagina che si scopre: la pagina prima di lui prende il fondo del sito e sta sopra; lui resta fermo in
     * fondo alla finestra. Spento se è più alto della finestra (si vedrebbe prima la sua fine) e per chi ha chiesto meno
     * movimento. Niente «&&» nello script: WordPress lo trasformerebbe in entità.
     */
    private static function script_scopre() {
        return '<script>(function(){var f=document.querySelector("footer.olo-piede-scopre");if(!f){return;}var p=f.previousElementSibling;if(!p){return;}'
            . 'var h=document.documentElement;function va(){var no=f.offsetHeight>window.innerHeight*0.92;'
            . 'if(window.matchMedia){if(window.matchMedia("(prefers-reduced-motion: reduce)").matches){no=true;}}'
            . 'if(no){h.classList.remove("olo-piede-scopre-on");p.classList.remove("olo-sopra-piede");return;}'
            . 'if(!p.style.backgroundColor){var c=getComputedStyle(document.body).backgroundColor;if(c==="rgba(0, 0, 0, 0)"){c="#ffffff";}if(c==="transparent"){c="#ffffff";}p.style.backgroundColor=c;}'
            . 'p.classList.add("olo-sopra-piede");h.classList.add("olo-piede-scopre-on");taglia();}'
            // La Luce di pagina (uno strato fisso grande quanto la finestra, dentro la pagina) sta sopra il piè di pagina
            // insieme alla pagina: si ritaglia via la parte dove il piè di pagina si è scoperto. Solo lei: lightbox e
            // popup della pagina restano interi (18 nov 2026, l'utente: «non funziona», il piè restava coperto).
            . 'function taglia(){var L=p.querySelectorAll(".olo-pagelight");if(!L.length){return;}var giu=0;'
            . 'if(h.classList.contains("olo-piede-scopre-on")){giu=Math.max(0,Math.round(window.innerHeight-p.getBoundingClientRect().bottom));}'
            . 'for(var i=0;i<L.length;i++){L[i].style.clipPath=giu>0?"inset(0 0 "+giu+"px 0)":"";}}'
            . 'va();window.addEventListener("resize",va);window.addEventListener("load",va);window.addEventListener("scroll",taglia,{passive:true});})();</script>';
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

        // Olobuilder frontend CSS
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
