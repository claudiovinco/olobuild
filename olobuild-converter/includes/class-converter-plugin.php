<?php
/**
 * Main plugin orchestrator (singleton).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Converter_Plugin {

    private static $instance = null;

    /** @var Olo_Converter_Interface[] */
    private $converters = [];

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->register_converters();

        if ( is_admin() ) {
            new Olo_Admin_Page( $this );
        }
    }

    private function register_converters() {
        $this->converters['elementor'] = new Olo_Elementor_Converter();
        $this->converters['yootheme'] = new Olo_Yootheme_Converter();
        $this->converters['divi']     = new Olo_Divi_Converter();
    }

    /**
     * @return Olo_Converter_Interface[]
     */
    public function get_converters() {
        return $this->converters;
    }

    /**
     * @param string $slug
     * @return Olo_Converter_Interface|null
     */
    public function get_converter( $slug ) {
        return $this->converters[ $slug ] ?? null;
    }
}
