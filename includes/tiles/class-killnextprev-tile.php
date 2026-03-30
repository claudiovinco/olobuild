<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_KillNextPrev_Tile extends Olo_Tile_Base {

    protected $type     = 'killnextprev';
    protected $name     = 'Kill Next/Prev';
    protected $icon     = 'dashicons-hidden';
    protected $category = 'navigation';
    protected $defaults = [];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        // 1) PHP filter — strip post navigation blocks from rendering
        if ( ! has_filter( 'render_block', 'olo_kill_nextprev_filter' ) ) {
            add_filter( 'render_block', 'olo_kill_nextprev_filter', 10, 2 );
        }

        // 2) CSS — immediate hide + JS — remove empty nav wrapper from DOM
        return '<style>.post-navigation,.nav-links,.navigation.post-navigation,.nav-previous,.nav-next,.wp-block-post-navigation-link,.post-navigation-link-previous,.post-navigation-link-next{display:none!important}</style>'
             . '<script>document.addEventListener("DOMContentLoaded",function(){'
             . 'document.querySelectorAll(".wp-block-post-navigation-link,.post-navigation-link-previous,.post-navigation-link-next,.post-navigation,.nav-links").forEach(function(e){e.remove()});'
             . 'var n=document.querySelector("nav[aria-label=\'Articoli\']");if(n){var g=n.closest(".wp-block-group.has-global-padding");if(g&&g.parentElement&&g.parentElement.classList.contains("wp-block-group")){g.parentElement.remove()}else if(g){g.remove()}else{n.remove()}}'
             . '});</script>';
    }
}

/**
 * Strip WordPress post navigation blocks.
 */
if ( ! function_exists( 'olo_kill_nextprev_filter' ) ) {
    function olo_kill_nextprev_filter( $block_content, $block ) {
        if ( ! empty( $block['blockName'] ) && $block['blockName'] === 'core/post-navigation-link' ) {
            return '';
        }
        return $block_content;
    }
}
