<?php
/**
 * Plugin Name: Olo Sandbox
 * Plugin URI:  https://olotheme.com
 * Description: Sandbox interattivo per Olobuild: ogni visitatore riceve un clone personale del template master, con reset automatico per inattività. Blocca creazione pagine, modifica global elements, upload media e tile sensibili. Include widget feedback e integrazione Microsoft Clarity (consenso GDPR).
 * Version:     1.0.13
 * Author:      Claudio Vinco
 * Author URI:  https://clod.eu
 * Text Domain: olo-sandbox
 * Requires PHP: 7.4
 * Requires at least: 5.8
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'OLO_SANDBOX_VERSION', '1.0.13' );
define( 'OLO_SANDBOX_PATH', plugin_dir_path( __FILE__ ) );
define( 'OLO_SANDBOX_URL', plugin_dir_url( __FILE__ ) );

require_once OLO_SANDBOX_PATH . 'includes/class-sandbox-config.php';
require_once OLO_SANDBOX_PATH . 'includes/class-sandbox-session.php';
require_once OLO_SANDBOX_PATH . 'includes/class-sandbox-clone.php';
require_once OLO_SANDBOX_PATH . 'includes/class-sandbox-bootstrap.php';
require_once OLO_SANDBOX_PATH . 'includes/class-sandbox-caps.php';
require_once OLO_SANDBOX_PATH . 'includes/class-sandbox-rest.php';
require_once OLO_SANDBOX_PATH . 'includes/class-sandbox-tiles.php';
require_once OLO_SANDBOX_PATH . 'includes/class-sandbox-render.php';
require_once OLO_SANDBOX_PATH . 'includes/class-sandbox-ui.php';
require_once OLO_SANDBOX_PATH . 'includes/class-sandbox-cron.php';
require_once OLO_SANDBOX_PATH . 'includes/class-sandbox-seed.php';
require_once OLO_SANDBOX_PATH . 'includes/class-sandbox-feedback.php';
require_once OLO_SANDBOX_PATH . 'includes/class-sandbox-clarity.php';

register_activation_hook( __FILE__, [ 'Olo_Sandbox_Bootstrap', 'on_activate' ] );
register_deactivation_hook( __FILE__, [ 'Olo_Sandbox_Bootstrap', 'on_deactivate' ] );

add_action( 'plugins_loaded', function () {
    new Olo_Sandbox_Bootstrap();
    new Olo_Sandbox_Caps();
    new Olo_Sandbox_Rest();
    new Olo_Sandbox_Tiles();
    new Olo_Sandbox_Render();
    new Olo_Sandbox_UI();
    new Olo_Sandbox_Cron();
    new Olo_Sandbox_Feedback();
    new Olo_Sandbox_Clarity();
}, 5 );
