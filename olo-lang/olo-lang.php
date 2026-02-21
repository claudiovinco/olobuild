<?php
/**
 * Plugin Name: Olo Lang
 * Plugin URI:  https://clod.eu/olo-lang
 * Description: Sistema di traduzione professionale per Olobuild — traduce template, contenuti e frontend multilingua.
 * Version:     1.0.8
 * Author:      Claudio
 * Author URI:  https://clod.eu
 * Text Domain: olo-lang
 * Requires PHP: 7.4
 * Requires at least: 5.8
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'OLO_LANG_VERSION', '1.0.8' );
define( 'OLO_LANG_PATH', plugin_dir_path( __FILE__ ) );
define( 'OLO_LANG_URL', plugin_dir_url( __FILE__ ) );

// Verifica dipendenza Olobuild
add_action( 'admin_notices', function () {
    if ( ! defined( 'OLO_VERSION' ) ) {
        echo '<div class="notice notice-error"><p><strong>Olo Lang</strong> richiede il plugin <strong>Olobuild</strong> attivo per funzionare.</p></div>';
    }
} );

require_once OLO_LANG_PATH . 'includes/class-database.php';
require_once OLO_LANG_PATH . 'includes/class-language.php';
require_once OLO_LANG_PATH . 'includes/class-scanner.php';
require_once OLO_LANG_PATH . 'includes/class-plugin-scanner.php';
require_once OLO_LANG_PATH . 'includes/class-frontend.php';
require_once OLO_LANG_PATH . 'includes/class-rest-api.php';
require_once OLO_LANG_PATH . 'includes/class-admin.php';
require_once OLO_LANG_PATH . 'includes/class-post-translation.php';

// Attivazione
register_activation_hook( __FILE__, function () {
    $db = new Olo_Lang_Database();
    $db->create_tables();

    if ( ! get_option( 'olo_lang_config' ) ) {
        update_option( 'olo_lang_config', [
            'default_lang'      => 'it',
            'languages'         => [
                [ 'code' => 'it', 'name' => 'Italiano', 'locale' => 'it_IT', 'active' => true ],
            ],
            'url_mode'          => 'parameter',
            'show_switcher'     => true,
            'switcher_style'    => 'dropdown',
            'switcher_position' => 'bottom-right',
        ], false );
    }

    // Flush rewrite rules per attivare le regole path-mode
    flush_rewrite_rules();
} );

// Disattivazione — conserva i dati
register_deactivation_hook( __FILE__, function () {
    delete_transient( 'olo_lang_activated' );
} );

// Inizializzazione (dopo Olobuild, priority 20)
add_action( 'plugins_loaded', function () {
    if ( ! defined( 'OLO_VERSION' ) ) {
        return;
    }

    // Traduzione frontend
    $frontend = new Olo_Lang_Frontend();
    $frontend->init();

    // REST API
    $api = new Olo_Lang_Rest_Api();
    $api->init();

    // Admin
    if ( is_admin() ) {
        $admin = new Olo_Lang_Admin();
        $admin->init();
    }

    // Traduzione post/CPT tramite copie per lingua
    $post_translation = new Olo_Lang_Post_Translation();
    $post_translation->init();
}, 20 );
