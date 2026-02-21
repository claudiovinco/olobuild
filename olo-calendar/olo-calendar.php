<?php
/**
 * Plugin Name: Olo Calendar
 * Plugin URI:  https://example.com/olo-calendar
 * Description: Calendario eventi avanzato con viste multiple, modale dettagli e integrazione Olobuild.
 * Version:     1.2.0
 * Author:      Claudio
 * Author URI:  https://example.com
 * Text Domain: olo-calendar
 * Requires PHP: 7.4
 * Requires at least: 5.8
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'OLO_CAL_VERSION', '1.2.0' );
define( 'OLO_CAL_PATH', plugin_dir_path( __FILE__ ) );
define( 'OLO_CAL_URL', plugin_dir_url( __FILE__ ) );

require_once OLO_CAL_PATH . 'includes/class-olo-calendar.php';
require_once OLO_CAL_PATH . 'includes/class-cpt-event.php';
require_once OLO_CAL_PATH . 'includes/class-event-meta.php';
require_once OLO_CAL_PATH . 'includes/class-rest-events.php';
require_once OLO_CAL_PATH . 'includes/class-frontend.php';
require_once OLO_CAL_PATH . 'includes/class-admin-dashboard.php';

// Activation: register CPT + flush rewrite rules
register_activation_hook( __FILE__, function () {
    Olo_CPT_Event::register_post_type();
    Olo_CPT_Event::register_taxonomy();
    flush_rewrite_rules();
} );

// Deactivation: flush rewrite rules
register_deactivation_hook( __FILE__, function () {
    flush_rewrite_rules();
} );

// Initialize
add_action( 'plugins_loaded', function () {
    Olo_Calendar::instance();
} );
