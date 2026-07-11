<?php
/**
 * Inserisce/aggiorna i template della replica olotheme.com.
 * Uso: wp eval-file olox-insert.php --allow-root --path=<wp>
 * Legge i JSON da /tmp/olox-tpl/*.json — { title, slug, kind, content }.
 *
 * kind 'header'/'footer': template condivisi (nessuna pagina WP), riusati su
 * TUTTE le pagine (home inclusa) via meta _olo_header_id/_olo_footer_id —
 * struttura classica olobuild. Sulla home il chrome della tile oloxrail si
 * riduce da solo (logo/lingue/credits li portano header e footer).
 * kind 'page' (default): template + pagina WP collegata via _olo_template_id.
 * Idempotente: match per slug (pagine) o per titolo (header/footer).
 */
global $wpdb;
$dir   = '/tmp/olox-tpl';
$files = glob( $dir . '/*.json' );
if ( ! $files ) { echo "Nessun JSON in $dir\n"; return; }

$table = $wpdb->prefix . 'olobuild_templates';

$jobs = [];
foreach ( $files as $f ) {
    $data = json_decode( file_get_contents( $f ), true );
    if ( ! $data || empty( $data['slug'] ) || empty( $data['content'] ) ) {
        echo 'SKIP ' . basename( $f ) . " (json non valido)\n";
        continue;
    }
    $data['kind'] = $data['kind'] ?? 'page';
    $jobs[] = $data;
}
// Prima header/footer (servono gli id per i meta delle pagine).
usort( $jobs, function ( $a, $b ) {
    $rank = function ( $j ) { return 'page' === $j['kind'] ? 1 : 0; };
    return $rank( $a ) - $rank( $b );
} );

$header_id = 0;
$footer_id = 0;

foreach ( $jobs as $data ) {
    $title   = $data['title'];
    $slug    = $data['slug'];
    $kind    = $data['kind'];
    $content = wp_json_encode( $data['content'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

    if ( 'header' === $kind || 'footer' === $kind ) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- tabella interna del plugin.
        $existing = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE title = %s AND type = %s LIMIT 1", $title, $kind ) );
        if ( $existing ) {
            $wpdb->update( $table, [
                'content'    => $content,
                'status'     => 'published',
                'updated_at' => current_time( 'mysql' ),
            ], [ 'id' => (int) $existing ] );
            $tpl_id = (int) $existing;
            echo "UPD {$kind} #{$tpl_id} · {$title}\n";
        } else {
            $wpdb->insert( $table, [
                'title'      => $title,
                'type'       => $kind,
                'content'    => $content,
                'settings'   => '{}',
                'thumbnail'  => '',
                'status'     => 'published',
                'author_id'  => 1,
                'created_at' => current_time( 'mysql' ),
                'updated_at' => current_time( 'mysql' ),
            ] );
            $tpl_id = (int) $wpdb->insert_id;
            echo "NEW {$kind} #{$tpl_id} · {$title}\n";
        }
        if ( 'header' === $kind ) { $header_id = $tpl_id; } else { $footer_id = $tpl_id; }
        continue;
    }

    // ---- kind 'page' ----
    $page   = get_page_by_path( $slug, OBJECT, 'page' );
    $tpl_id = $page ? (int) get_post_meta( $page->ID, '_olo_template_id', true ) : 0;

    if ( $tpl_id ) {
        $wpdb->update( $table, [
            'title'      => $title,
            'content'    => $content,
            'status'     => 'published',
            'updated_at' => current_time( 'mysql' ),
        ], [ 'id' => $tpl_id ] );
        echo "UPD template #{$tpl_id} · {$slug}\n";
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
        echo "NEW template #{$tpl_id} · {$slug}\n";
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
        echo "  pagina creata #{$pid} /{$slug}/\n";
    } else {
        $pid = $page->ID;
        echo "  pagina esistente #{$pid} /{$slug}/\n";
    }
    update_post_meta( $pid, '_olo_template_id', $tpl_id );

    // Header/footer condivisi su tutte le pagine, home inclusa (il chrome
    // della tile oloxrail si riduce da solo quando la dnav è presente).
    update_post_meta( $pid, '_olo_header_id', $header_id ? $header_id : -1 );
    update_post_meta( $pid, '_olo_footer_id', $footer_id ? $footer_id : -1 );
    echo '  header/footer: ' . ( $header_id ? "#{$header_id}" : '-1' ) . ' / ' . ( $footer_id ? "#{$footer_id}" : '-1' ) . "\n";
}
echo "FATTO\n";
