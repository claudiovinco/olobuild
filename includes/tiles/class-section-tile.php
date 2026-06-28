<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Section_Tile extends Olobuild_Tile_Base {

    protected $type     = 'section';
    protected $name     = 'Sezione';
    protected $icon     = 'dashicons-align-center';
    protected $category = 'structure';
    protected $defaults = [
        'style'                => 'default',
        'width'                => 'default',
        'padding'              => 'default',
        'sticky_effect'        => 'none',
        'sticky_top'           => '',
        'scroll_snap'          => false,
        'snap_dots'            => false,
        'snap_dot_color'       => '#ffffff',
        'snap_dot_active_color'=> '',
        'snap_dot_position'    => 'right',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'style', 'type' => 'select', 'label' => 'Style', 'options' => [
                'default'   => 'Default',
                'muted'     => 'Muted',
                'primary'   => 'Primary',
                'secondary' => 'Secondary',
            ]],
            [ 'key' => 'width', 'type' => 'select', 'label' => 'Max Width', 'options' => [
                'default' => 'Default',
                'small'   => 'Small',
                'large'   => 'Large',
                'xlarge'  => 'X-Large',
                'expand'    => 'Full Width',
                'fullbleed' => 'Edge to Edge',
            ]],
            [ 'key' => 'padding', 'type' => 'select', 'label' => 'Padding', 'options' => [
                'default'       => 'Default',
                'small'         => 'Small',
                'large'         => 'Large',
                'xlarge'        => 'X-Large',
                'remove-vertical' => 'None',
            ]],
            [ 'key' => 'sticky_effect', 'type' => 'select', 'label' => 'Sticky Effect', 'options' => [
                'none'      => 'None',
                'cover'     => 'Cover',
                'reveal'    => 'Reveal',
                'cover-h'   => 'Cover Horizontal',
                'reveal-h'  => 'Reveal Horizontal',
            ]],
            [ 'key' => 'scroll_snap',          'type' => 'toggle', 'label' => 'Scroll Snap' ],
            [ 'key' => 'snap_dots',            'type' => 'toggle', 'label' => 'Snap Dots' ],
            [ 'key' => 'snap_dot_color',       'type' => 'color',  'label' => 'Dot Color' ],
            [ 'key' => 'snap_dot_active_color','type' => 'color',  'label' => 'Active Dot Color' ],
            [ 'key' => 'snap_dot_position',    'type' => 'select', 'label' => 'Dot Position' ],
        ];
    }

    /**
     * Render is handled by the recursive renderer.
     * This method is kept for compatibility but not called directly.
     */
    public function render( $settings ) {
        return '';
    }
}
