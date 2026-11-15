<?php
/**
 * olotutor.com: carica in OLOlang le traduzioni (en, de, fr) dei template «OLOtutor Racconto» (7 nov 2026).
 *
 * righe.json = [{template_id, tile_id, field_path, original, lang, translation}], preparate dai template VIVI del sito
 * (stessi percorsi dei campi che legge Olo_Lang_Frontend::apply_translations: oggetto → a.b, lista → a[i]); per il
 * menu template_id 0, tile_id «wp-menu», field_path «menu-item-<ID>». Una riga si salta se l'italiano del template non
 * è più quello di righe.json (campo cambiato nel frattempo): resta da tradurre a mano, e lo script lo dice.
 *
 * uso: wp eval-file carica-traduzioni.php <righe.json> [secco]
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$file  = (string) ( $args[0] ?? '' );
$secco = in_array( 'secco', (array) $args, true );
$righe = json_decode( (string) @file_get_contents( $file ), true );
if ( ! is_array( $righe ) ) {
	WP_CLI::error( 'righe.json non leggibile: ' . $file );
}

// l'italiano vivo dei template: tile::campo → testo
$db   = new Olobuild_Database();
$vivo = [];
$giro = function ( $v, $tile, $pref, $tid ) use ( &$giro, &$vivo ) {
	foreach ( (array) $v as $k => $x ) {
		$fp = '' === $pref ? (string) $k : $pref . '.' . $k;
		if ( is_array( $x ) ) {
			if ( $x === array_values( $x ) ) {
				foreach ( $x as $i => $it ) {
					if ( is_array( $it ) ) {
						$giro( $it, $tile, $fp . '[' . $i . ']', $tid );
					}
				}
			} else {
				$giro( $x, $tile, $fp, $tid );
			}
		} elseif ( is_string( $x ) ) {
			$vivo[ $tid . '|' . $tile . '::' . $fp ] = $x;
		}
	}
};
$nodi = function ( $ns, $tid ) use ( &$nodi, $giro ) {
	foreach ( (array) $ns as $n ) {
		if ( ! empty( $n['id'] ) && ! empty( $n['settings'] ) ) {
			$giro( $n['settings'], $n['id'], '', $tid );
		}
		$nodi( $n['children'] ?? [], $tid );
	}
};
foreach ( array_unique( array_map( function ( $r ) { return (int) $r['template_id']; }, $righe ) ) as $tid ) {
	if ( $tid > 0 ) {
		$t = $db->get_template( $tid );
		$nodi( $t['content'] ?? [], $tid );
	}
}

$ok = 0;
$saltate = [];
$lang_db = Olo_Lang_Database::instance();
foreach ( $righe as $r ) {
	$tid = (int) $r['template_id'];
	if ( $tid > 0 ) {
		$chiave = $tid . '|' . $r['tile_id'] . '::' . $r['field_path'];
		if ( ! isset( $vivo[ $chiave ] ) || $vivo[ $chiave ] !== $r['original'] ) {
			$saltate[ $chiave ] = true;
			continue;
		}
	}
	if ( $secco ) {
		$ok++;
		continue;
	}
	$id = $lang_db->save_translation( [
		'template_id' => $tid,
		'tile_id'     => $r['tile_id'],
		'field_path'  => $r['field_path'],
		'lang'        => $r['lang'],
		'original'    => $r['original'],
		'translation' => $r['translation'],
		'status'      => 'tradotto',
	] );
	if ( $id ) {
		$ok++;
	} else {
		$saltate[ $tid . '|' . $r['tile_id'] . '::' . $r['field_path'] . ' (' . $r['lang'] . ', rifiutata)' ] = true;
	}
}
foreach ( array_slice( array_keys( $saltate ), 0, 15 ) as $s ) {
	WP_CLI::log( 'saltata: ' . $s );
}
if ( ! $secco ) {
	wp_cache_flush();
	if ( class_exists( 'Olobuild_FullPage_Cache' ) ) {
		Olobuild_FullPage_Cache::purge_all();
	}
}
WP_CLI::success( ( $secco ? '[secco] ' : '' ) . $ok . ' traduzioni ' . ( $secco ? 'da salvare' : 'salvate' ) . ', ' . count( $saltate ) . ' saltate' );
