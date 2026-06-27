<?php
/**
 * Cleanup DEMO tema "North": rimuove pagina /north-demo/, olo_template "North — Home"
 * (+ revisioni), menu "North Nav" e i 3 post news. Idempotente.
 *   wp eval-file north-cleanup.php --allow-root --skip-plugins=woocommerce
 */
if ( ! defined( 'ABSPATH' ) ) { fwrite( STDERR, "Run via wp eval-file\n" ); exit( 1 ); }
global $wpdb;
$report = [];

// pagina /north-demo/
$page = get_page_by_path( 'north-demo' );
if ( $page ) { wp_delete_post( $page->ID, true ); $report[] = "page {$page->ID} deleted"; }

// olo_template "North — Home" (+ revisioni)
$tbl = $wpdb->prefix . 'olo_templates';
$rev = $wpdb->prefix . 'olo_revisions';
$ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$tbl} WHERE title = %s", 'North — Home' ) );
foreach ( (array) $ids as $id ) {
    $wpdb->delete( $tbl, [ 'id' => $id ] );
    $wpdb->query( $wpdb->prepare( "DELETE FROM {$rev} WHERE template_id = %d", $id ) );
    $report[] = "template {$id} + revisions deleted";
}

// menu "North Nav"
$m = wp_get_nav_menu_object( 'North Nav' );
if ( $m ) { wp_delete_nav_menu( $m->term_id ); $report[] = 'menu "North Nav" deleted'; }

// 3 post news + thumbnail allegate
$titles = [
    'Introducing North: The next era of enterprise AI',
    'Defining AI automation: A new kind of workplace',
    'Bringing secure AI to critical systems',
];
foreach ( $titles as $t ) {
    $p = get_page_by_title( $t, OBJECT, 'post' );
    if ( $p ) {
        $thumb = get_post_thumbnail_id( $p->ID );
        if ( $thumb ) { wp_delete_attachment( $thumb, true ); }
        wp_delete_post( $p->ID, true );
        $report[] = "post '{$t}' deleted";
    }
}

echo implode( "\n", $report ) . "\n";
echo 'REMAINING_NORTH_TEMPLATES=' . (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$tbl} WHERE title LIKE %s", '%North%' ) ) . "\n";
echo "CLEANUP_DONE\n";
