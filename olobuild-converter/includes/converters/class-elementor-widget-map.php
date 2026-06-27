<?php
/**
 * Elementor widget type → OloBuild element mapping table.
 *
 * This file is designed to be easily updated during the iterative refinement phase.
 * Each entry maps an Elementor widgetType to an OloBuild element type and
 * a callback method that performs the property conversion.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Elementor_Widget_Map {

    /**
     * Get the mapping table.
     * Key = Elementor widgetType, Value = [ 'olo_type', 'method' ]
     */
    public static function get_map() {
        return [
            // ─── Content ───
            'heading'        => [ 'olo_type' => 'headline',    'method' => 'convert_heading' ],
            'text-editor'    => [ 'olo_type' => 'content',     'method' => 'convert_text_editor' ],
            'button'         => [ 'olo_type' => 'button',      'method' => 'convert_button' ],
            'icon-box'       => [ 'olo_type' => 'iconbox',     'method' => 'convert_icon_box' ],
            'image-box'      => [ 'olo_type' => 'content',     'method' => 'convert_image_box' ],
            'counter'        => [ 'olo_type' => 'counter',     'method' => 'convert_counter' ],
            'progress'       => [ 'olo_type' => 'progress',    'method' => 'convert_progress' ],
            'testimonial'    => [ 'olo_type' => 'testimonial', 'method' => 'convert_testimonial' ],
            'alert'          => [ 'olo_type' => 'alert',       'method' => 'convert_alert' ],
            'html'           => [ 'olo_type' => 'html',        'method' => 'convert_html' ],

            // ─── Media ───
            'image'          => [ 'olo_type' => 'image',       'method' => 'convert_image' ],
            'video'          => [ 'olo_type' => 'video',       'method' => 'convert_video' ],
            'image-gallery'  => [ 'olo_type' => 'gallery',     'method' => 'convert_gallery' ],
            'image-carousel' => [ 'olo_type' => 'gallery',     'method' => 'convert_gallery' ],
            'google-maps'    => [ 'olo_type' => 'map',         'method' => 'convert_map' ],

            // ─── Layout ───
            'spacer'         => [ 'olo_type' => 'spacer',      'method' => 'convert_spacer' ],
            'divider'        => [ 'olo_type' => 'spacer',      'method' => 'convert_divider' ],
            'call-to-action' => [ 'olo_type' => 'hero',        'method' => 'convert_cta' ],

            // ─── Interactive ───
            'accordion'      => [ 'olo_type' => 'accordion',   'method' => 'convert_accordion' ],
            'toggle'         => [ 'olo_type' => 'accordion',   'method' => 'convert_toggle' ],
            'tabs'           => [ 'olo_type' => 'switcher',    'method' => 'convert_tabs' ],
            'price-table'    => [ 'olo_type' => 'pricing',     'method' => 'convert_pricing' ],
            'icon-list'      => [ 'olo_type' => 'list',        'method' => 'convert_icon_list' ],
            'social-icons'   => [ 'olo_type' => 'social',      'method' => 'convert_social' ],

            // ─── Forms (Pro) ───
            'form'           => [ 'olo_type' => 'form',        'method' => 'convert_form' ],
        ];
    }

    /**
     * Check if a widget type is mapped.
     */
    public static function has( $widget_type ) {
        return isset( self::get_map()[ $widget_type ] );
    }

    /**
     * Get mapping for a widget type.
     */
    public static function get( $widget_type ) {
        return self::get_map()[ $widget_type ] ?? null;
    }
}
