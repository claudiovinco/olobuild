<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Booking {

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
        $cpt = new Olo_CPT_Service();
        $cpt->init();

        $meta = new Olo_Service_Meta();
        $meta->init();

        Olo_Booking_DB::maybe_upgrade();

        // REST API v1 (legacy)
        $rest = new Olo_Booking_Rest_API();
        $rest->init();

        // REST API v2 (manager + public)
        $rest_v2 = new Olo_Booking_Rest_API_Manager();
        $rest_v2->init();

        if ( is_admin() ) {
            $dashboard = new Olo_Booking_Dashboard();
            $dashboard->init();
        }

        $front = new Olo_Booking_Frontend();
        $front->init();

        $emails = new Olo_Booking_Emails();
        $emails->init();

        // Manager frontend page
        $manager = new Olo_Manager_Page();
        $manager->init();
    }
}
