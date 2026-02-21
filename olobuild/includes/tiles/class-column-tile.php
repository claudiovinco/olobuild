<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Column_Tile extends Olo_Tile_Base {

    protected $type     = 'column';
    protected $name     = 'Colonna';
    protected $icon     = 'dashicons-editor-insertmore';
    protected $category = 'structure';
    protected $defaults = [
        'width_default' => '',
        'width_small'   => '',
        'width_medium'  => '',
        'width_large'   => '',
    ];

    public function get_controls() {
        $width_options = [
            ''      => 'Auto',
            '1-1'   => '1/1',
            '1-2'   => '1/2',
            '1-3'   => '1/3',
            '2-3'   => '2/3',
            '1-4'   => '1/4',
            '3-4'   => '3/4',
            '1-5'   => '1/5',
            '2-5'   => '2/5',
            '3-5'   => '3/5',
            '4-5'   => '4/5',
            '1-6'   => '1/6',
            '5-6'   => '5/6',
        ];

        return [
            [ 'key' => 'width_default', 'type' => 'select', 'label' => 'Phone Width',  'options' => $width_options ],
            [ 'key' => 'width_small',   'type' => 'select', 'label' => 'Tablet Width',  'options' => $width_options ],
            [ 'key' => 'width_medium',  'type' => 'select', 'label' => 'Desktop Width', 'options' => $width_options ],
            [ 'key' => 'width_large',   'type' => 'select', 'label' => 'Large Width',   'options' => $width_options ],
        ];
    }

    /**
     * Render is handled by the recursive renderer.
     */
    public function render( $settings ) {
        return '';
    }
}
