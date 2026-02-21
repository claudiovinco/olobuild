<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Calendar {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init();
    }

    private function init() {
        // Custom Post Type + Taxonomy
        $cpt = new Olo_CPT_Event();
        $cpt->init();

        // Admin meta box
        $meta = new Olo_Event_Meta();
        $meta->init();

        // REST API for FullCalendar
        $rest = new Olo_Rest_Events();
        $rest->init();

        // Frontend (shortcode + assets)
        $front = new Olo_Calendar_Frontend();
        $front->init();

        // Admin Dashboard
        if ( is_admin() ) {
            $dashboard = new Olo_Admin_Dashboard();
            $dashboard->init();
        }
    }
}
