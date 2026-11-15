<?php
/**
 * Porta il tema «OLOtutor — Il racconto» fra i template del sottosito olotheme.com/olotutor (7 nov 2026).
 *
 * Richiesta dell'utente: «il template lavoralo come template nuovo della zona template». Quindi template NUOVI in
 * Blocchi & Pagine, accanto a quelli attivi, senza toccare lo stile del sito, la testata, il piè e la 404 attivi.
 *
 * Fa:
 *  - 9 template «OLOtutor Racconto — …» (testata, piè, 404 e 6 pagine) dai file della cartella del tema
 *    (wp-content/olotutor-temi/olotutor-racconto), con le foto della libreria copiate nella Libreria media del sito;
 *  - il menu «OLOtutor Racconto — menu» (voci verso le pagine qui sotto), letto dalla testata nuova;
 *  - 6 pagine in BOZZA (racconto, racconto-lo-studente, …) con il loro template e la testata e il piè nuovi
 *    (meta _olo_header_id / _olo_footer_id), da vedere in anteprima da collegati e da pubblicare quando si vuole.
 * Si può rilanciare: aggiorna gli stessi template, lo stesso menu e le stesse pagine (id in opzione otr_racconto_import).
 *
 * Uso (sul server): wp eval-file importa.php [secco]
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$secco = in_array( 'secco', (array) ( $args ?? [] ), true );
wp_set_current_user( 1 );

$dir  = WP_CONTENT_DIR . '/olotutor-temi/olotutor-racconto';
$tema = json_decode( (string) file_get_contents( $dir . '/theme.json' ), true );
if ( ! is_array( $tema ) ) {
	WP_CLI::error( 'theme.json non trovato in ' . $dir );
}
$stato = get_option( 'otr_racconto_import', [] );
$stato = is_array( $stato ) ? $stato : [];
$stato += [ 'tpl' => [], 'pagine' => [], 'menu' => 0 ];
$db = new Olobuild_Database();

// le pagine: chiave del tema → indirizzo sul sito (prefisso «racconto»: «lo-studente» e «la-scuola» esistono già)
$slug = [];
foreach ( $tema['pages'] as $k => $p ) {
	$slug[ $k ] = empty( $p['slug'] ) ? 'racconto' : 'racconto-' . $p['slug'];
}
$url = function ( $k ) use ( $slug ) {
	return home_url( '/' . $slug[ $k ] . '/' );
};

// 1. il menu
$nome_menu = 'OLOtutor Racconto — menu';
if ( ! $secco ) {
	$menu = wp_get_nav_menu_object( $stato['menu'] ?: $nome_menu );
	$menu_id = $menu ? (int) $menu->term_id : (int) wp_create_nav_menu( $nome_menu );
	foreach ( (array) wp_get_nav_menu_items( $menu_id ) as $vecchia ) {
		wp_delete_post( (int) $vecchia->ID, true );
	}
	foreach ( $tema['menu']['items'] as $i => $voce ) {
		$dest = preg_replace_callback( '/\{\{PAGINA:([a-z0-9_-]+)\}\}/', function ( $m ) use ( $url ) {
			return $url( $m[1] );
		}, $voce['url'] );
		wp_update_nav_menu_item( $menu_id, 0, [ 'menu-item-title' => $voce['title'], 'menu-item-url' => $dest,
			'menu-item-type' => 'custom', 'menu-item-status' => 'publish', 'menu-item-position' => $i + 1 ] );
	}
	$stato['menu'] = $menu_id;
	WP_CLI::log( 'menu ' . $menu_id . ' · ' . count( $tema['menu']['items'] ) . ' voci' );
} else {
	$menu_id = (int) $stato['menu'];
	WP_CLI::log( '[secco] menu «' . $nome_menu . '»' );
}

// 2. i template
$lib   = Olobuild_Template_Library::library_url();
$media = [ 'copiate' => 0, 'riusate' => 0, 'fallite' => 0 ];
foreach ( $tema['templates'] as $k => $meta ) {
	$json = (string) file_get_contents( $dir . '/' . $meta['file'] );
	$json = str_replace( '{{OLOBUILD_LIBRARY}}', $lib, $json );
	$json = preg_replace_callback( '/\{\{PAGINA:([a-z0-9_-]+)\}\}/', function ( $m ) use ( $url ) {
		return str_replace( '/', '\/', $url( $m[1] ) );
	}, $json );
	$json = preg_replace( '/"menu_id"\s*:\s*"auto"/', '"menu_id":' . (int) $menu_id, $json );
	$dati = json_decode( $json, true );
	if ( ! is_array( $dati ) ) {
		WP_CLI::error( 'JSON non valido: ' . $meta['file'] );
	}
	$impostazioni = [];
	$contenuto    = $dati;
	if ( isset( $dati['content'] ) && ! isset( $dati[0] ) ) {
		$impostazioni = is_array( $dati['settings'] ?? null ) ? $dati['settings'] : [];
		$contenuto    = $dati['content'];
	}
	if ( $secco ) {
		WP_CLI::log( '[secco] ' . $meta['title'] . ' (' . $meta['type'] . ')' . ( ! empty( $stato['tpl'][ $k ] ) ? ' → aggiorna #' . $stato['tpl'][ $k ] : ' → nuovo' ) );
		continue;
	}
	$m = Olobuild_Template_Library::instance()->import_media( $contenuto );
	$contenuto = $m['content'];
	foreach ( $media as $c => $v ) {
		$media[ $c ] += (int) ( $m[ $c ] ?? 0 );
	}
	$riga = [ 'title' => $meta['title'], 'type' => $meta['type'], 'content' => $contenuto, 'settings' => $impostazioni,
		'status' => 'published' ];
	$id = (int) ( $stato['tpl'][ $k ] ?? 0 );
	if ( $id && $db->get_template( $id ) ) {
		$db->update_template( $id, $riga );
		WP_CLI::log( 'aggiornato #' . $id . ' ' . $meta['title'] );
	} else {
		$id = (int) $db->create_template( $riga );
		WP_CLI::log( 'creato #' . $id . ' ' . $meta['title'] );
	}
	$stato['tpl'][ $k ] = $id;
}

// 3. le pagine in bozza
foreach ( $tema['pages'] as $k => $p ) {
	if ( $secco ) {
		WP_CLI::log( '[secco] pagina in bozza «' . $p['title'] . '» /' . $slug[ $k ] . '/' );
		continue;
	}
	$pid  = (int) ( $stato['pagine'][ $k ] ?? 0 );
	$dati = [ 'post_type' => 'page', 'post_title' => 'Racconto — ' . $p['title'], 'post_name' => $slug[ $k ],
		'post_content' => '' ];
	if ( $pid && get_post( $pid ) ) {
		wp_update_post( $dati + [ 'ID' => $pid ] );
	} else {
		$pid = (int) wp_insert_post( $dati + [ 'post_status' => 'draft' ] );
	}
	update_post_meta( $pid, '_olo_template_id', $stato['tpl'][ $p['template'] ] );
	update_post_meta( $pid, '_olo_header_id', $stato['tpl']['header'] );
	update_post_meta( $pid, '_olo_footer_id', $stato['tpl']['footer'] );
	$stato['pagine'][ $k ] = $pid;
	WP_CLI::log( 'pagina #' . $pid . ' ' . get_post_status( $pid ) . ' /' . $slug[ $k ] . '/ → template #' . $stato['tpl'][ $p['template'] ] );
}

if ( ! $secco ) {
	update_option( 'otr_racconto_import', $stato, false );
	WP_CLI::success( sprintf( 'media: %d copiate, %d riusate, %d rimaste sulla libreria remota', $media['copiate'],
		$media['riusate'], $media['fallite'] ) );
}
