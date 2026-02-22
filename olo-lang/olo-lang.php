<?php
/**
 * Plugin Name: Olo Lang
 * Plugin URI:  https://clod.eu/olo-lang
 * Description: Sistema di traduzione multilingua per WordPress — traduce contenuti, menu, post e template (con supporto avanzato per Olobuild).
 * Version:     1.0.10
 * Author:      Claudio
 * Author URI:  https://clod.eu
 * Text Domain: olo-lang
 * Requires PHP: 7.4
 * Requires at least: 5.8
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'OLO_LANG_VERSION', '1.0.10' );
define( 'OLO_LANG_PATH', plugin_dir_path( __FILE__ ) );
define( 'OLO_LANG_URL', plugin_dir_url( __FILE__ ) );

// Suggerimento Olobuild (opzionale, non bloccante)
add_action( 'admin_notices', function () {
    if ( ! defined( 'OLO_VERSION' ) ) {
        echo '<div class="notice notice-info is-dismissible"><p><strong>Olo Lang</strong> funziona autonomamente. Installa <strong>Olobuild</strong> per tradurre anche i template del page builder.</p></div>';
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

// Inizializzazione (priority 20 — dopo eventuali plugin che definiscono olo_t())
add_action( 'plugins_loaded', function () {
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
