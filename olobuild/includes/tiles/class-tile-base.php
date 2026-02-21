<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

abstract class Olo_Tile_Base {

    protected $type     = '';
    protected $name     = '';
    protected $icon     = '';
    protected $category = 'general';
    protected $defaults = [];

    public function get_type() {
        return $this->type;
    }

    public function get_name() {
        return $this->name;
    }

    public function get_icon() {
        return $this->icon;
    }

    public function get_category() {
        return $this->category;
    }

    public function get_defaults() {
        return $this->defaults;
    }

    /**
     * Returns control definitions for the tile settings panel.
     * Each control: [ 'key' => string, 'type' => string, 'label' => string, 'default' => mixed ]
     */
    abstract public function get_controls();

    /**
     * Renders the frontend HTML for this tile.
     */
    abstract public function render( $settings );

    /**
     * Build a CSS style string from key-value pairs, skipping empty values.
     * Prevents invalid CSS like "color: ;" when a value is empty.
     *
     * @param array $pairs [ 'property' => 'value', ... ]
     * @return string e.g. "color: #fff; background: #000"
     */
    protected function build_style( $pairs ) {
        $parts = [];
        foreach ( $pairs as $prop => $value ) {
            $v = trim( (string) $value );
            if ( $v !== '' ) {
                $parts[] = $prop . ': ' . esc_attr( $v );
            }
        }
        return implode( '; ', $parts );
    }

    /**
     * Return escaped color value or empty string.
     * Use in scoped <style> blocks: only output the CSS rule if color is non-empty.
     */
    protected function safe_color( $value ) {
        $v = trim( (string) $value );
        return $v !== '' ? esc_attr( $v ) : '';
    }

    /**
     * Sanitize rich text from TipTap editor.
     * Strips block-level tags (<p>), converts rgb() colors to hex
     * (WordPress safecss_filter_attr doesn't support rgb()), then sanitizes.
     */
    /**
     * Wrap an image in a hover-media container for image swap / video on hover.
     *
     * @param string $img_html  The original <img> (or inner HTML).
     * @param string $hover_image  URL of the alternative hover image.
     * @param string $hover_video  URL of an mp4 video to play on hover.
     * @return string  Wrapped HTML, or original if no hover media.
     */
    protected function render_hover_wrap( $img_html, $hover_image = '', $hover_video = '' ) {
        if ( empty( $hover_image ) && empty( $hover_video ) ) {
            return $img_html;
        }

        $hover_el = '';
        if ( ! empty( $hover_video ) ) {
            $hover_el = '<video src="' . esc_url( $hover_video ) . '" muted loop playsinline preload="none"></video>';
        } elseif ( ! empty( $hover_image ) ) {
            $hover_el = '<img src="' . esc_url( $hover_image ) . '" alt="" loading="lazy" />';
        }

        $vid_attrs = '';
        if ( ! empty( $hover_video ) ) {
            $vid_attrs = " onmouseenter=\"var v=this.querySelector('.olo-hover-media video');if(v)v.play()\" onmouseleave=\"var v=this.querySelector('.olo-hover-media video');if(v){v.pause();v.currentTime=0}\"";
        }

        return '<div class="olo-hover-wrap"' . $vid_attrs . '>'
             . $img_html
             . '<div class="olo-hover-media">' . $hover_el . '</div>'
             . '</div>';
    }

    protected function sanitize_richtext( $html ) {
        // Strip block-level wrappers (TipTap may wrap in <p>)
        $html = preg_replace( '/<\/?p[^>]*>/', '', $html );
        // Convert rgb() to hex (WordPress CSS sanitizer only allows hex/named colors)
        $html = preg_replace_callback(
            '/rgb\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)/',
            function( $m ) { return sprintf( '#%02x%02x%02x', $m[1], $m[2], $m[3] ); },
            $html
        );
        return wp_kses_post( $html );
    }
}
