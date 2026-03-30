<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Tile_Manager {

    private static $instance = null;
    private $tiles = [];

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public function register_tile( $tile_instance ) {
        if ( ! $tile_instance instanceof Olo_Tile_Base ) {
            return false;
        }
        $this->tiles[ $tile_instance->get_type() ] = $tile_instance;
        return true;
    }

    public function get_tiles() {
        return $this->tiles;
    }

    public function get_tile( $type ) {
        return $this->tiles[ $type ] ?? null;
    }
}
