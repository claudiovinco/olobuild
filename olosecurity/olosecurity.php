<?php
/**
 * Plugin Name: OLOsecurity
 * Plugin URI:  https://olotheme.com
 * Description: La barriera di sicurezza WordPress di OLOtheme: integrità di core, plugin e temi, scanner webshell con quarantena, registro attività, anti brute-force con blocklist automatica, verifica in due passaggi (2FA) nativa, hardening opzionale.
 * Version:     1.0.0
 * Author:      Claudio Vinco
 * Author URI:  https://clod.eu
 * Text Domain: olosecurity
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 5.9
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// I moduli sono condivisi con OLObuild (che li bundla): se un'altra copia è già
// caricata — OLObuild network-attivato, o qualunque ordine inatteso — non si
// dichiara nulla una seconda volta. OLObuild fa il check speculare e cede il
// passo a questo plugin quando lo trova in active_plugins.
if ( class_exists( 'Olo_Security_Sentinel' ) ) {
    return;
}

define( 'OLOSEC_VERSION', '1.0.0' );
define( 'OLOSEC_PATH', plugin_dir_path( __FILE__ ) );
define( 'OLOSEC_URL', plugin_dir_url( __FILE__ ) );
define( 'OLOSEC_STANDALONE', true );

require_once OLOSEC_PATH . 'includes/class-security-audit.php';
require_once OLOSEC_PATH . 'includes/class-security-config-monitor.php';
require_once OLOSEC_PATH . 'includes/class-security-components.php';
require_once OLOSEC_PATH . 'includes/class-security-login.php';
require_once OLOSEC_PATH . 'includes/class-security-hardening.php';
require_once OLOSEC_PATH . 'includes/class-security-twofactor.php';
require_once OLOSEC_PATH . 'includes/class-security-sentinel.php';

Olo_Security_Audit::maybe_install();
Olo_Security_Audit::init();
Olo_Security_Login::init();
Olo_Security_Hardening::init();
Olo_Security_TwoFactor::init();
Olo_Security_Sentinel::init();

register_deactivation_hook( __FILE__, function () {
    wp_clear_scheduled_hook( Olo_Security_Sentinel::CRON_HOOK );
} );
