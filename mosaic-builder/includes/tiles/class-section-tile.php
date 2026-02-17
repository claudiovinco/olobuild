<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Section_Tile extends Olo_Tile_Base {

    protected $type     = 'section';
    protected $name     = 'Sezione';
    protected $icon     = 'dashicons-align-center';
    protected $category = 'structure';
    protected $defaults = [
        'style'   => 'default',
        'width'   => 'default',
        'padding' => 'default',
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
                'expand'  => 'Full Width',
            ]],
            [ 'key' => 'padding', 'type' => 'select', 'label' => 'Padding', 'options' => [
                'default'       => 'Default',
                'small'         => 'Small',
                'large'         => 'Large',
                'xlarge'        => 'X-Large',
                'remove-vertical' => 'None',
            ]],
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
