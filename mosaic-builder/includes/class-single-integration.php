<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Single_Integration {

    public function init() {
        // Priority 15: before page-integration (20) and location-single (20)
        add_filter( 'the_content', [ $this, 'render_single_template' ], 15 );
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

        $post_type   = get_post_type();
        $template_id = (int) get_option( "olo_active_single_{$post_type}", 0 );

        if ( ! $template_id ) {
            return $content;
        }

        $db  = new Olo_Database();
        $tpl = $db->get_template( $template_id );

        if ( ! $tpl || ( $tpl['status'] !== 'published' && $tpl['status'] !== 'draft' ) ) {
            return $content;
        }

        $rendering = true;
        $renderer  = new Olo_Frontend_Renderer();
        $output    = $renderer->render_shortcode( [ 'id' => $template_id ] );
        $rendering = false;

        // Replace content entirely (not prepend)
        return $output;
    }
}
