<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_InnerColumns_Tile extends Olo_Tile_Base {

    protected $type     = 'inner-columns';
    protected $name     = 'Colonne interne';
    protected $icon     = 'dashicons-table-col-after';
    protected $category = 'layout';
    protected $defaults = [
        'layout'         => '50-50',
        'gap'            => '16',
        'vertical_align' => 'stretch',
        'stack_mobile'   => true,
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        // Rendering handled by class-frontend-renderer.php
        return '';
    }
}
