<?php
/**
 * Plugin Name: North Demo — canvas + fonts (scoped /north-demo/)
 * Description: Rende la pagina /north-demo/ a tutta pagina (senza chrome del tema) con i
 *              font del blueprint (Space Grotesk / Space Mono / Inter). Scoped: nessun
 *              effetto sulle altre pagine. Rimuovibile cancellando questo file.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_filter( 'show_admin_bar', function ( $show ) {
    return ( function_exists( 'is_page' ) && is_page( 'north-demo' ) ) ? false : $show;
} );

add_filter( 'template_include', function ( $tpl ) {
    if ( function_exists( 'is_page' ) && is_page( 'north-demo' ) ) {
        $canvas = __DIR__ . '/north-demo-tpl/north-demo-canvas.php';
        if ( file_exists( $canvas ) ) { return $canvas; }
    }
    return $tpl;
}, 99 );
