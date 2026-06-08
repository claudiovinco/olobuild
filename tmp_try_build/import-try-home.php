<?php
/**
 * Import "Try Home v2" su try.olotheme.com.
 * Eseguire con:  wp eval-file import-try-home.php --path=<wproot> --allow-root
 *
 * Idempotente: aggiorna il template "Try Home v2" se esiste, altrimenti lo crea.
 * NON tocca il master sandbox 134. Ripunta la front page (post 58) al nuovo template.
 * Render-test difensivo (try/catch) per scoprire errori prima dello switch del landing.
 */

if ( ! defined( 'ABSPATH' ) ) { echo "ABORT: non in WP\n"; return; }

$dir  = __DIR__;
$json = @file_get_contents( $dir . '/try-home-tiles.json' );
if ( $json === false ) { echo "ABORT: try-home-tiles.json non trovato in $dir\n"; return; }

$content = json_decode( $json, true );
if ( ! is_array( $content ) ) { echo "ABORT: JSON non valido\n"; return; }

$settings = [
    'page_settings'     => [ 'max_width' => 'fullwidth' ],
    'post_id'           => 58,
    'page_bg'           => [ 'type' => 'solid', 'color' => '#0b0d12' ],
    'content_max_width' => 1200,
];

global $wpdb;
$t = $wpdb->prefix . 'olo_templates';

if ( ! class_exists( 'Olo_Database' ) ) { echo "ABORT: Olo_Database assente (plugin attivo?)\n"; return; }
$db = Olo_Database::instance();

$existing = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $t WHERE title = %s AND type = 'page'", 'Try Home v2' ) );

if ( $existing ) {
    $db->update_template( $existing, [
        'content'  => $content,
        'settings' => $settings,
        'status'   => 'published',
        'type'     => 'page',
    ] );
    $id = $existing;
    echo "UPDATED template_id=$id\n";
} else {
    $id = (int) $db->create_template( [
        'title'    => 'Try Home v2',
        'type'     => 'page',
        'content'  => $content,
        'settings' => $settings,
        'status'   => 'published',
    ] );
    echo "CREATED template_id=$id\n";
}

if ( ! $id ) { echo "ABORT: insert/update fallito\n"; return; }

// Ripunta la front page (58) → nuovo template (NON tocca il master 134).
$front = (int) get_option( 'page_on_front', 0 );
echo "page_on_front=$front\n";
if ( $front ) {
    $old = get_post_meta( $front, '_olo_template_id', true );
    update_post_meta( $front, '_olo_template_id', $id );
    echo "post{$front} _olo_template_id: {$old} -> {$id}\n";
}

// Render-test difensivo.
if ( class_exists( 'Olo_Frontend_Renderer' ) ) {
    try {
        $r    = new Olo_Frontend_Renderer();
        $html = $r->render_shortcode( [ 'id' => $id ] );
        echo "render_len=" . strlen( $html ) . "\n";
        echo "has_section=" . ( strpos( $html, 'olo-section' ) !== false ? 'Y' : 'N' ) . "\n";
        echo "has_goo=" . ( strpos( $html, 'goo' ) !== false ? 'Y' : 'N' ) . "\n";
        echo "has_countdown=" . ( strpos( $html, 'countdown' ) !== false ? 'Y' : 'N' ) . "\n";
        echo "has_sentinel=" . ( strpos( $html, '#apri-builder' ) !== false ? 'Y' : 'N' ) . "\n";
    } catch ( \Throwable $e ) {
        echo "RENDER_ERROR: " . $e->getMessage() . "\n";
    }
}

echo "DONE\n";
