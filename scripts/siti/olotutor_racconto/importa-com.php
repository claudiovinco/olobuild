<?php
/**
 * olotutor.com: il tema «OLOtutor — Il racconto» al posto di quello di prima (7 nov 2026, richiesta dell'utente:
 * «usa questo nuovo tema al posto dell'attuale su olotutor.com»; OLObuild portato prima alla 1.4.977, copia del
 * database in ~/backup-olotutor-com-20261107/).
 *
 * uso: wp eval-file importa-com.php <cartella del tema (variante SITO=com)> [attiva|ripristina]
 *  - senza secondo argomento: crea o aggiorna i 9 template «OLOtutor Racconto — …» e il menu «OLOtutor Racconto — menu»,
 *    coi link alle pagine che il sito ha già (stessi indirizzi); foto e video copiati nella Libreria media. Il sito non
 *    cambia ancora.
 *  - attiva: salva una versione dello stile CON le sei pagine (Configurazione › Palette › Versioni dello stile:
 *    «Ripristina» rimette stile, testata, piè, 404 e template delle pagine), porta Palette, caratteri e cursore del tema
 *    nello stile del sito come fa l'importatore dei temi, assegna i template nuovi alle pagine, testata, piè e 404 nuovi.
 *  - ripristina: rimette la versione dello stile salvata da «attiva».
 *  - solo:<chiave> (es. solo:home): aggiorna solo quel template, senza toccare il menu (le voci non si ricreano).
 * Id e versione salvata nell'opzione otr_racconto_com.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
wp_set_current_user( 1 );
$dir  = rtrim( (string) ( $args[0] ?? '' ), '/' );
$cosa = (string) ( $args[1] ?? '' );
$tema = json_decode( (string) @file_get_contents( $dir . '/theme.json' ), true );
if ( ! is_array( $tema ) ) {
	WP_CLI::error( 'theme.json non trovato in ' . $dir );
}
$stato = get_option( 'otr_racconto_com', [] );
$stato = ( is_array( $stato ) ? $stato : [] ) + [ 'tpl' => [], 'menu' => 0, 'versione' => '' ];
$ss    = Olobuild_Style_System::instance();

// le pagine che il sito ha già: stessi indirizzi di prima
$pagine = [
	'home'     => (int) get_option( 'page_on_front' ),
	'studente' => (int) ( get_page_by_path( 'per-lo-studente' )->ID ?? 0 ),
	'docente'  => (int) ( get_page_by_path( 'per-il-docente' )->ID ?? 0 ),
	'scuola'   => (int) ( get_page_by_path( 'per-la-scuola' )->ID ?? 0 ),
	'metodo'   => (int) ( get_page_by_path( 'il-metodo' )->ID ?? 0 ),
	'prova'    => (int) ( get_page_by_path( 'come-si-prova' )->ID ?? 0 ),
];
foreach ( $pagine as $k => $id ) {
	if ( ! $id ) {
		WP_CLI::error( 'pagina mancante: ' . $k );
	}
}
$url = function ( $k ) use ( $pagine ) {
	return 'home' === $k ? home_url( '/' ) : get_permalink( $pagine[ $k ] );
};

if ( 'ripristina' === $cosa ) {
	if ( ! $stato['versione'] ) {
		WP_CLI::error( 'nessuna versione salvata da ripristinare' );
	}
	$r = $ss->restore_snapshot( $stato['versione'] );
	if ( is_wp_error( $r ) ) {
		WP_CLI::error( $r->get_error_message() );
	}
	if ( class_exists( 'Olobuild_FullPage_Cache' ) ) {
		Olobuild_FullPage_Cache::purge_all();
	}
	WP_CLI::success( 'ripristinata la versione ' . $stato['versione'] );
	return;
}

$solo = 0 === strpos( $cosa, 'solo:' ) ? substr( $cosa, 5 ) : '';
if ( $solo && ( empty( $stato['menu'] ) || empty( $stato['tpl'][ $solo ] ) ) ) {
	WP_CLI::error( 'solo:' . $solo . ' aggiorna un template già importato: lanciare prima senza argomento' );
}
if ( 'attiva' !== $cosa && $solo ) {
	$menu_id = (int) $stato['menu'];
} elseif ( 'attiva' !== $cosa ) {
	// 1. il menu
	$nome_menu = 'OLOtutor Racconto — menu';
	$menu      = wp_get_nav_menu_object( $stato['menu'] ?: $nome_menu );
	$menu_id   = $menu ? (int) $menu->term_id : (int) wp_create_nav_menu( $nome_menu );
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
	WP_CLI::log( 'menu ' . $menu_id );
}
if ( 'attiva' !== $cosa ) {

	// 2. i template
	$db    = new Olobuild_Database();
	$lib   = Olobuild_Template_Library::library_url();
	$media = [ 'copiate' => 0, 'riusate' => 0, 'fallite' => 0 ];
	foreach ( $tema['templates'] as $k => $meta ) {
		if ( $solo && $k !== $solo ) {
			continue;
		}
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
	update_option( 'otr_racconto_com', $stato, false );
	if ( class_exists( 'Olobuild_FullPage_Cache' ) ) {
		Olobuild_FullPage_Cache::purge_all();
	}
	WP_CLI::success( sprintf( 'template pronti; media: %d copiate, %d riusate, %d rimaste sulla libreria remota',
		$media['copiate'], $media['riusate'], $media['fallite'] ) );
	return;
}

// 3. attiva: versione dello stile con le sei pagine, poi stile, cursore, testata, piè, 404 e pagine
foreach ( [ 'header', 'footer', '404', 'home', 'studente', 'docente', 'scuola', 'metodo', 'prova' ] as $k ) {
	if ( empty( $stato['tpl'][ $k ] ) ) {
		WP_CLI::error( 'manca il template ' . $k . ': lanciare prima senza «attiva»' );
	}
}
$prima = [];
foreach ( $pagine as $pid ) {
	$prima[ $pid ] = Olobuild_Style_System::stato_pagina( $pid );
}
$stato['versione'] = $ss->take_snapshot( 'import_tema', (string) $tema['name'], $prima );
update_option( 'otr_racconto_com', $stato, false );
WP_CLI::log( 'versione dello stile salvata: ' . $stato['versione'] );

$attuale = get_option( 'olobuild_styles', [] );
$unito   = wp_parse_args( $tema['styles'], is_array( $attuale ) ? $attuale : [] );
update_option( 'olobuild_styles', $unito );
if ( ! empty( $unito['colors'] ) ) {
	$ss->sync_global_palette( $unito['colors'] );
	if ( empty( $tema['styles']['dark_colors'] ) ) {
		$ss->sync_dark_palette( $unito['colors'] );
	}
}
if ( ! empty( $tema['cursor'] ) && is_array( $tema['cursor'] ) ) {
	if ( ! class_exists( 'Olobuild_Magnetic_Cursor' ) ) {
		require_once OLOBUILD_PATH . 'includes/class-magnetic-cursor.php';
	}
	update_option( 'olobuild_magnetic_cursor', Olobuild_Magnetic_Cursor::sanitize( $tema['cursor'] ) );
}
update_option( 'olobuild_active_header', $stato['tpl']['header'] );
update_option( 'olobuild_active_footer', $stato['tpl']['footer'] );
update_option( 'olobuild_active_404', $stato['tpl']['404'] );
foreach ( $pagine as $k => $pid ) {
	update_post_meta( $pid, '_olo_template_id', $stato['tpl'][ $k ] );
	WP_CLI::log( 'pagina #' . $pid . ' ' . get_permalink( $pid ) . ' → template #' . $stato['tpl'][ $k ] );
}
if ( class_exists( 'Olobuild_FullPage_Cache' ) ) {
	Olobuild_FullPage_Cache::purge_all();
}
if ( class_exists( 'Olobuild_Critical_Css' ) && method_exists( 'Olobuild_Critical_Css', 'purge_all' ) ) {
	Olobuild_Critical_Css::purge_all();
}
WP_CLI::success( 'tema attivo; per tornare indietro: wp eval-file importa-com.php <cartella> ripristina' );
