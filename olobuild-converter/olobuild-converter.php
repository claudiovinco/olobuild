<?php
/**
 * Plugin Name: OloBuild Converter
 * Plugin URI:  https://mosaic.clod.eu
 * Description: Converte template da Elementor Pro, YooTheme Pro e Divi nel formato OloBuild.
 * Version:     1.0.0
 * Author:      OloBuild Team
 * Author URI:  https://mosaic.clod.eu
 * Text Domain: olobuild-converter
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * License:     GPL-2.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'OLO_CONVERTER_VERSION', '1.0.0' );
define( 'OLO_CONVERTER_FILE', __FILE__ );
define( 'OLO_CONVERTER_DIR', plugin_dir_path( __FILE__ ) );
define( 'OLO_CONVERTER_URL', plugin_dir_url( __FILE__ ) );

// Autoload classes.
spl_autoload_register( function ( $class ) {
    $prefix = 'Olo_';
    if ( strpos( $class, $prefix ) !== 0 ) {
        return;
    }

    $relative = substr( $class, strlen( $prefix ) );
    $file     = 'class-' . strtolower( str_replace( '_', '-', $relative ) ) . '.php';

    // Check includes/ first, then includes/converters/.
    $paths = [
        OLO_CONVERTER_DIR . 'includes/' . $file,
        OLO_CONVERTER_DIR . 'includes/converters/' . $file,
    ];

    foreach ( $paths as $path ) {
        if ( file_exists( $path ) ) {
            require_once $path;
            return;
        }
    }
} );

/**
 * Boot the plugin.
 */
function olo_converter_init() {
    // Check OloBuild is active.
    if ( ! class_exists( 'Olo_Builder' ) && ! defined( 'OLOBUILD_VERSION' ) ) {
        add_action( 'admin_notices', function () {
            echo '<div class="notice notice-error"><p>';
            esc_html_e( 'OloBuild Converter richiede il plugin OloBuild attivo.', 'olobuild-converter' );
            echo '</p></div>';
        } );
        return;
    }

    Olo_Converter_Plugin::instance();
}
add_action( 'plugins_loaded', 'olo_converter_init' );
