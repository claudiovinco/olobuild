<?php
/**
 * Hello Olobuild — tema minimale per Olobuild page builder.
 * Zero CSS invasivo, zero conflitti, massima compatibilità.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Theme setup
 */
add_action( 'after_setup_theme', function() {
    // Supporto titolo nel <head>
    add_theme_support( 'title-tag' );

    // Logo personalizzato
    add_theme_support( 'custom-logo', [
        'height'      => 100,
        'width'       => 350,
        'flex-height' => true,
        'flex-width'  => true,
    ] );

    // Post thumbnails
    add_theme_support( 'post-thumbnails' );

    // HTML5
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );

    // Wide e full alignment (per blocchi Gutenberg)
    add_theme_support( 'align-wide' );

    // Responsive embeds
    add_theme_support( 'responsive-embeds' );

    // Block editor styles
    add_theme_support( 'wp-block-styles' );

    // Full Site Editing (block theme)
    add_theme_support( 'block-templates' );

    // Registra posizione menu
    register_nav_menus( [
        'primary' => __( 'Menu Principale', 'olobuild' ),
    ] );
} );

/**
 * Enqueue theme styles — solo il CSS minimale
 */
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'hello-olobuild-style',
        get_stylesheet_uri(),
        [],
        wp_get_theme()->get( 'Version' )
    );

    // Rimuovi stili globali di WP che interferiscono con page builder
    wp_dequeue_style( 'global-styles' );
    wp_dequeue_style( 'wp-block-library-theme' );
}, 20 );

/**
 * Rimuovi SVG e stili inline di WP che aggiungono CSS non necessario
 */
add_action( 'wp_enqueue_scripts', function() {
    // Rimuovi duotone SVG filters
    remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
}, 100 );

/**
 * Disabilita inline CSS generato da global-styles del block theme
 */
add_filter( 'wp_theme_json_data_theme', function( $theme_json ) {
    // Non sovrascrivere nulla — lascia che Olobuild gestisca tutto
    return $theme_json;
} );

/**
 * Rimuovi prefetch DNS e meta inutili
 */
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
