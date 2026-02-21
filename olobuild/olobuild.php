<?php
/**
 * Plugin Name: Olobuild
 * Plugin URI:  https://example.com/olobuild
 * Description: Page builder professionale olonico con sistema a griglia (tile drag & drop).
 * Version:     1.65.31
 * Author:      Claudio
 * Author URI:  https://example.com
 * Text Domain: olobuilder
 * Requires PHP: 7.4
 * Requires at least: 5.8
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'OLO_VERSION', '1.65.31' );
define( 'OLO_PATH', plugin_dir_path( __FILE__ ) );
define( 'OLO_URL', plugin_dir_url( __FILE__ ) );

/**
 * Traduzione stringhe tile — equivalente di __() per Olobuild.
 *
 * Se Olo Lang è attivo e la lingua corrente non è quella default,
 * cerca la traduzione nella mappa globale. Altrimenti ritorna l'originale.
 * Sicuro da usare anche se Olo Lang non è installato.
 *
 * @param string $text Testo originale (italiano).
 * @return string Testo tradotto o originale.
 */
function olo_t( $text ) {
    static $map = null;
    static $is_default = null;

    // Lazy init: determina lingua e carica mappa una sola volta
    if ( $is_default === null ) {
        if ( ! class_exists( 'Olo_Lang_Language' ) ) {
            $is_default = true;
        } else {
            $lang    = Olo_Lang_Language::detect_current_lang();
            $default = Olo_Lang_Language::get_default_lang();
            $is_default = ( $lang === $default );

            if ( ! $is_default ) {
                $db  = new Olo_Lang_Database();
                $raw = $db->get_translation_map( 0, $lang );
                $map = [];
                foreach ( $raw as $row ) {
                    $orig = $row['original'] ?? '';
                    $trans = trim( $row['translation'] ?? '' );
                    if ( $orig !== '' && $trans !== '' && $orig !== $trans && $row['status'] !== 'bozza' ) {
                        $map[ $orig ] = $trans;
                    }
                }
            }
        }
    }

    if ( $is_default || $map === null ) {
        return $text;
    }

    return $map[ $text ] ?? $text;
}

require_once OLO_PATH . 'includes/class-database.php';
require_once OLO_PATH . 'includes/class-tile-manager.php';
require_once OLO_PATH . 'includes/class-rest-api.php';
require_once OLO_PATH . 'includes/class-dynamic-content.php';
require_once OLO_PATH . 'includes/class-style-system.php';
require_once OLO_PATH . 'includes/class-frontend-renderer.php';
require_once OLO_PATH . 'includes/class-page-integration.php';
require_once OLO_PATH . 'includes/class-header-integration.php';
require_once OLO_PATH . 'includes/class-footer-integration.php';
require_once OLO_PATH . 'includes/class-single-integration.php';
require_once OLO_PATH . 'includes/class-location-single.php';
require_once OLO_PATH . 'includes/class-form-handler.php';
require_once OLO_PATH . 'includes/class-olo-builder.php';

// Activation hook
register_activation_hook( __FILE__, function () {
    // Migrate from old "mosaic" naming to "olo" (one-time, idempotent)
    // MUST run BEFORE create_tables() so RENAME succeeds before dbDelta creates empty tables
    global $wpdb;
    $old_templates  = $wpdb->prefix . 'mosaic_templates';
    $old_revisions  = $wpdb->prefix . 'mosaic_revisions';
    $new_templates  = $wpdb->prefix . 'olo_templates';
    $new_revisions  = $wpdb->prefix . 'olo_revisions';

    // Rename tables if old ones exist and new ones don't
    if ( $wpdb->get_var( "SHOW TABLES LIKE '{$old_templates}'" ) && ! $wpdb->get_var( "SHOW TABLES LIKE '{$new_templates}'" ) ) {
        $wpdb->query( "RENAME TABLE `{$old_templates}` TO `{$new_templates}`" );
    }
    if ( $wpdb->get_var( "SHOW TABLES LIKE '{$old_revisions}'" ) && ! $wpdb->get_var( "SHOW TABLES LIKE '{$new_revisions}'" ) ) {
        $wpdb->query( "RENAME TABLE `{$old_revisions}` TO `{$new_revisions}`" );
    }

    // Create/update tables AFTER migration so dbDelta doesn't interfere with RENAME
    $db = new Olo_Database();
    $db->create_tables();

    // Migrate options
    $option_map = [
        'mosaic_active_header' => 'olo_active_header',
        'mosaic_active_footer' => 'olo_active_footer',
        'mosaic_styles'        => 'olo_styles',
    ];
    foreach ( $option_map as $old_key => $new_key ) {
        $val = get_option( $old_key );
        if ( false !== $val && false === get_option( $new_key ) ) {
            update_option( $new_key, $val, false );
            delete_option( $old_key );
        }
    }
    // Migrate per-post-type single options
    foreach ( get_post_types( [ 'public' => true ], 'names' ) as $pt ) {
        $old_key = "mosaic_active_single_{$pt}";
        $new_key = "olo_active_single_{$pt}";
        $val     = get_option( $old_key );
        if ( false !== $val && false === get_option( $new_key ) ) {
            update_option( $new_key, $val, false );
            delete_option( $old_key );
        }
    }

    // Migrate post meta
    $wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_key = '_olo_template_id' WHERE meta_key = '_mosaic_template_id'" );
    $wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_key = '_olo_header_id' WHERE meta_key = '_mosaic_header_id'" );
    $wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_key = '_olo_footer_id' WHERE meta_key = '_mosaic_footer_id'" );

    // Migrate nav menu location assignment
    $locations = get_theme_mod( 'nav_menu_locations', [] );
    if ( isset( $locations['mosaic_header'] ) && ! isset( $locations['olo_header'] ) ) {
        $locations['olo_header'] = $locations['mosaic_header'];
        unset( $locations['mosaic_header'] );
        set_theme_mod( 'nav_menu_locations', $locations );
    }
} );

// Deactivation hook
register_deactivation_hook( __FILE__, function () {
    // Cleanup transients if needed
    delete_transient( 'olo_builder_activated' );
} );

// Initialize plugin
add_action( 'plugins_loaded', function () {
    Olo_Builder::instance();
    Olo_Location_Single::instance();

    // Form handler (public REST endpoint)
    $form_handler = new Olo_Form_Handler();
    $form_handler->init();
} );
