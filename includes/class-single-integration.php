<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Single_Integration {

    public function init() {
        // Priority 15: before page-integration (20) and location-single (20)
        add_filter( 'the_content', [ $this, 'render_single_template' ], 15 );
    }

    /**
     * Il template singolo di un contenuto, scelto in un posto solo (pagina, CSS, CSS critico, «Modifica con Olobuild»):
     * una Regola di visualizzazione col contesto Single (gli articoli di una categoria e delle sue sottocategorie, un
     * tipo, un autore…) e, se nessuna vale, quello attivato per il suo tipo. Le pagine hanno il loro template: 0.
     *
     * @param int $post_id 0 = il contenuto aperto.
     * @return int
     */
    public static function template_id( $post_id = 0 ) {
        $post_id = $post_id ? (int) $post_id : (int) get_queried_object_id();
        $tipo    = $post_id ? get_post_type( $post_id ) : '';
        if ( ! $tipo || in_array( $tipo, [ 'page', 'attachment' ], true ) ) {
            return 0;
        }
        static $memo = [];
        if ( isset( $memo[ $post_id ] ) ) {
            return $memo[ $post_id ];
        }
        $id = (int) get_option( "olobuild_active_single_{$tipo}", 0 );
        if ( class_exists( 'Olobuild_Template_Conditions' ) ) {
            $id = (int) Olobuild_Template_Conditions::instance()->resolve_by_conditions( $id, 'single', $post_id );
        }
        $memo[ $post_id ] = $id;
        return $id;
    }

    /**
     * Replace content with Olobuilder single template for matching CPTs.
     */
    public function render_single_template( $content ) {
        // Recursion guard
        static $rendering = false;
        if ( $rendering ) {
            return $content;
        }

        if ( ! is_singular() || is_page() || ! in_the_loop() || ! is_main_query() ) {
            return $content;
        }

        $template_id = self::template_id( (int) get_the_ID() );

        if ( ! $template_id ) {
            return $content;
        }

        $db  = new Olobuild_Database();
        $tpl = $db->get_template( $template_id );

        if ( ! $tpl || ( $tpl['status'] !== 'published' && $tpl['status'] !== 'draft' ) ) {
            return $content;
        }

        $rendering = true;
        $renderer  = new Olobuild_Frontend_Renderer();
        $output    = $renderer->render_shortcode( [ 'id' => $template_id ] );
        $rendering = false;

        // Replace content entirely (not prepend)
        return $output;
    }
}
