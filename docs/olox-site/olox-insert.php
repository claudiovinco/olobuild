<?php
/**
 * Inserisce/aggiorna i template della replica olotheme.com su mosaic.
 * Uso: wp eval-file olox-insert.php --allow-root --path=/var/www/wordpress
 * Legge i JSON da /tmp/olox-tpl/*.json — { title, slug, content }.
 * Idempotente: match per slug pagina; template collegato via _olo_template_id.
 */
global $wpdb;
$dir   = '/tmp/olox-tpl';
$files = glob( $dir . '/*.json' );
if ( ! $files ) { echo "Nessun JSON in $dir\n"; return; }

$table = $wpdb->prefix . 'olobuild_templates';

foreach ( $files as $f ) {
    $raw  = file_get_contents( $f );
    $data = json_decode( $raw, true );
    if ( ! $data || empty( $data['slug'] ) || empty( $data['content'] ) ) {
        echo "SKIP " . basename( $f ) . " (json non valido)\n";
        continue;
    }
    $title   = $data['title'];
    $slug    = $data['slug'];
    $content = wp_json_encode( $data['content'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

    // Pagina esistente?
    $page = get_page_by_path( $slug, OBJECT, 'page' );
    $tpl_id = 0;
    if ( $page ) {
        $tpl_id = (int) get_post_meta( $page->ID, '_olo_template_id', true );
    }

    if ( $tpl_id ) {
        $wpdb->update( $table, [
            'title'      => $title,
            'content'    => $content,
            'status'     => 'published',
            'updated_at' => current_time( 'mysql' ),
        ], [ 'id' => $tpl_id ] );
        echo "UPD template #$tpl_id · $slug\n";
    } else {
        $wpdb->insert( $table, [
            'title'      => $title,
            'type'       => 'page',
            'content'    => $content,
            'settings'   => '{}',
            'thumbnail'  => '',
            'status'     => 'published',
            'author_id'  => 1,
            'created_at' => current_time( 'mysql' ),
            'updated_at' => current_time( 'mysql' ),
        ] );
        $tpl_id = (int) $wpdb->insert_id;
        echo "NEW template #$tpl_id · $slug\n";
    }

    if ( ! $page ) {
        $pid = wp_insert_post( [
            'post_title'   => $title,
            'post_name'    => $slug,
            'post_type'    => 'page',
            'post_status'  => 'publish',
            'post_content' => '',
            'post_author'  => 1,
        ] );
        update_post_meta( $pid, '_olo_template_id', $tpl_id );
        echo "  pagina creata #$pid /$slug/\n";
    } else {
        update_post_meta( $page->ID, '_olo_template_id', $tpl_id );
        echo "  pagina esistente #{$page->ID} /$slug/ ricollegata\n";
    }
}
echo "FATTO\n";
