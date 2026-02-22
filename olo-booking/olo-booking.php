<?php
/**
 * Plugin Name: Olo Booking
 * Plugin URI:  https://clod.eu/olo-booking
 * Description: Sistema di prenotazioni per strutture ricettive (baite, appartamenti). Pannello gestore frontend, calendario, stagioni e tariffe.
 * Version:     3.5.5
 * Author:      Claudio
 * Author URI:  https://clod.eu
 * Text Domain: olo-booking
 * Requires PHP: 7.4
 * Requires at least: 5.8
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'OLO_BOOK_VERSION', '3.5.5' );
define( 'OLO_BOOK_PATH', plugin_dir_path( __FILE__ ) );
define( 'OLO_BOOK_URL', plugin_dir_url( __FILE__ ) );
define( 'OLO_BOOK_DB_VERSION', '2.0.0' );

require_once OLO_BOOK_PATH . 'includes/class-olo-booking.php';
require_once OLO_BOOK_PATH . 'includes/class-role-manager.php';
require_once OLO_BOOK_PATH . 'includes/class-cpt-service.php';
require_once OLO_BOOK_PATH . 'includes/class-service-meta.php';
require_once OLO_BOOK_PATH . 'includes/class-booking-db.php';
require_once OLO_BOOK_PATH . 'includes/class-price-calculator.php';
require_once OLO_BOOK_PATH . 'includes/class-rest-api.php';
require_once OLO_BOOK_PATH . 'includes/class-rest-api-manager.php';
require_once OLO_BOOK_PATH . 'includes/class-admin-dashboard.php';
require_once OLO_BOOK_PATH . 'includes/class-frontend.php';
require_once OLO_BOOK_PATH . 'includes/class-emails.php';
require_once OLO_BOOK_PATH . 'includes/class-manager-page.php';
require_once OLO_BOOK_PATH . 'includes/class-amenities-catalog.php';

register_activation_hook( __FILE__, function () {
    Olo_Role_Manager::create_role();
    Olo_CPT_Service::register_post_type();
    Olo_Booking_DB::create_tables();

    // Register rewrite rules before flushing so /gestione/ works immediately
    $theme = get_option( 'olo_manager_theme', [] );
    $slug  = ! empty( $theme['login']['slug'] ) ? $theme['login']['slug'] : 'gestione';
    add_rewrite_rule( '^' . preg_quote( $slug, '/' ) . '/?$', 'index.php?olo_manager_page=1', 'top' );
    add_rewrite_rule( '^' . preg_quote( $slug, '/' ) . '/(.+)/?$', 'index.php?olo_manager_page=1', 'top' );

    flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, function () {
    flush_rewrite_rules();
} );

add_action( 'plugins_loaded', function () {
    Olo_Booking::instance();
} );
