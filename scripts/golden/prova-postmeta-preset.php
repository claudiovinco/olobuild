<?php
/**
 * prova-postmeta-preset.php — la tile postmeta resa con il renderer PHP VERO sui 12 preset e su 7
 * casi limite del «Padding chip» (scheda `tile-dinamici-postmeta-chip-fatale`, ondata O0).
 *
 * I casi li scrive in locale esporta-preset-postmeta.mjs: preset di tilePresets.js fusi con i
 * default del config, come li salva il builder, e il padding dei chip reso dal gemello Vue.
 * Su mosaic, con il JSON copiato accanto (vedi README.md):
 *
 *   php -d memory_limit=512M /usr/local/bin/wp --allow-root --path=/var/www/wordpress \
 *     eval-file /root/olo-golden/prova-postmeta-preset.php casi=/root/olo-golden/postmeta-casi.json
 *
 * Una riga per caso: id · OK|KO · chip_style · padding PHP (atteso, dal Vue) · md5 dell'HTML con
 * l'uid normalizzato · motivi. Uscita 1 se un caso fallisce: qualunque Throwable; warning, notice
 * ed errori PHP del render, anche non visualizzati (qui error_reporting è E_ALL, qualunque sia
 * WP_DEBUG; restano fuori solo gli errori soppressi con @ e le deprecazioni emesse fuori da
 * olobuild); un buffer di output rimasto aperto; HTML senza contenitore o senza voci; con
 * chip_style 'none' un chip, altrimenti una voce senza la classe del chip o un padding diverso da
 * quello del Vue. Uscita 2: casi mancanti o olobuild non attivo. Sola lettura.
 *
 * Con la 1.4.481 su PHP 8 (prima della correzione): 11 righe KO, tutte quelle con i chip (i 5
 * preset pill, tag, sticker, chip-3d e 6 casi limite), per «Undefined variable $chip_pad» e il
 * TypeError di array_sum(). Dopo: 19 OK, uscita 0, e le 8 righe con chip_style 'none' hanno lo
 * stesso md5 di prima (a parità di articolo e di giorno: senza articoli la data è quella di oggi).
 */

$olo_pm_file = '';
foreach ( (array) $args as $olo_pm_a ) {
	if ( strpos( (string) $olo_pm_a, 'casi=' ) === 0 ) {
		$olo_pm_file = substr( (string) $olo_pm_a, 5 );
	}
}
$olo_pm_dati = ( $olo_pm_file !== '' && is_readable( $olo_pm_file ) ) ? json_decode( (string) file_get_contents( $olo_pm_file ), true ) : null;
$olo_pm_casi = ( is_array( $olo_pm_dati ) && isset( $olo_pm_dati['casi'] ) && is_array( $olo_pm_dati['casi'] ) ) ? $olo_pm_dati['casi'] : [];
if ( count( $olo_pm_casi ) < 12 ) {
	WP_CLI::error( 'Manca casi=<json di esporta-preset-postmeta.mjs>, o ha meno di 12 casi.', false );
	exit( 2 );
}
if ( ! class_exists( 'Olobuild_PostMeta_Tile' ) || ! defined( 'OLOBUILD_PATH' ) ) {
	WP_CLI::error( 'Olobuild_PostMeta_Tile non caricata: olobuild è attivo su questo sito?', false );
	exit( 2 );
}

// Un articolo vero come contesto (data, autore, categorie, tag): lo stesso a ogni lancio.
$olo_pm_post = get_posts( [ 'numberposts' => 1, 'post_type' => 'post', 'post_status' => 'publish', 'orderby' => 'ID', 'order' => 'ASC' ] );
if ( $olo_pm_post ) {
	$GLOBALS['post'] = $olo_pm_post[0]; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- contesto del render, ripristinato in fondo
	setup_postdata( $olo_pm_post[0] );
}
WP_CLI::log(
	'PHP ' . PHP_VERSION . ' · OLOBUILD ' . OLOBUILD_VERSION
	. ' · casi della ' . (string) ( $olo_pm_dati['versione'] ?? '?' ) . ' (' . (string) ( $olo_pm_dati['generato'] ?? '?' ) . ')'
	. ' · articolo ' . ( $olo_pm_post ? $olo_pm_post[0]->ID : 'nessuno (data di oggi)' )
);

$olo_pm_radice = wp_normalize_path( OLOBUILD_PATH );
$olo_pm_err    = [];
$olo_pm_prima  = error_reporting( E_ALL ); // anche notice e deprecazioni, qualunque sia WP_DEBUG
set_error_handler(
	static function ( $no, $msg, $file, $line ) use ( &$olo_pm_err, $olo_pm_radice ) {
		if ( ! ( error_reporting() & $no ) ) {
			return true; // soppresso con @
		}
		$nostro = strpos( wp_normalize_path( (string) $file ), $olo_pm_radice ) === 0;
		if ( ! $nostro && in_array( $no, [ E_DEPRECATED, E_USER_DEPRECATED ], true ) ) {
			return true; // deprecazioni del core o di altri plugin: fuori perimetro
		}
		$olo_pm_err[] = 'E' . $no . ' ' . $msg . ' @ ' . basename( (string) $file ) . ':' . $line;
		return true;
	}
);

$olo_pm_tile = new Olobuild_PostMeta_Tile();
$olo_pm_ko   = 0;
foreach ( $olo_pm_casi as $olo_pm_id => $olo_pm_caso ) {
	$olo_pm_err    = [];
	$olo_pm_s      = ( isset( $olo_pm_caso['settings'] ) && is_array( $olo_pm_caso['settings'] ) ) ? $olo_pm_caso['settings'] : [];
	$olo_pm_chip   = (string) ( $olo_pm_caso['chip_style'] ?? 'none' );
	$olo_pm_atteso = (string) ( $olo_pm_caso['padding'] ?? '?' );
	$olo_pm_liv    = ob_get_level();
	$olo_pm_html   = '';
	try {
		$olo_pm_html = (string) $olo_pm_tile->render( $olo_pm_s );
	} catch ( \Throwable $olo_pm_e ) {
		$olo_pm_err[] = get_class( $olo_pm_e ) . ': ' . $olo_pm_e->getMessage() . ' @ ' . basename( $olo_pm_e->getFile() ) . ':' . $olo_pm_e->getLine();
	}
	if ( ob_get_level() !== $olo_pm_liv ) {
		// render() apre un ob_start(): un errore a metà lo lascia aperto e inghiottirebbe le righe dopo.
		$olo_pm_err[] = 'buffer di output rimasti aperti: ' . ( ob_get_level() - $olo_pm_liv );
		while ( ob_get_level() > $olo_pm_liv ) {
			ob_end_clean();
		}
	}
	$olo_pm_trovato = '?';
	if ( $olo_pm_html !== '' ) {
		if ( strpos( $olo_pm_html, '<div class="olo-postmeta ' ) === false ) {
			$olo_pm_err[] = 'contenitore .olo-postmeta assente';
		}
		// build_meta_item() apre ogni voce così; wrap_meta_item() aggiunge la classe del chip e lo stile.
		preg_match_all( '/<span class="olo-postmeta-item(?: olo-pm-chip-([a-z0-9-]+))?" style="([^"]*)"/', $olo_pm_html, $olo_pm_m );
		$olo_pm_voci  = count( $olo_pm_m[0] );
		$olo_pm_pad   = [];
		$olo_pm_senza = 0; // voci senza la classe del chip atteso (o con un chip, se è 'none')
		foreach ( array_keys( $olo_pm_m[0] ) as $olo_pm_k ) {
			if ( $olo_pm_m[1][ $olo_pm_k ] !== ( $olo_pm_atteso === '-' ? '' : $olo_pm_chip ) ) {
				$olo_pm_senza++;
			}
			$olo_pm_pad[] = preg_match( '/(?:^|;)padding:([^;]*);/', $olo_pm_m[2][ $olo_pm_k ], $olo_pm_p ) ? trim( $olo_pm_p[1] ) : '';
		}
		$olo_pm_pad     = array_values( array_unique( $olo_pm_pad ) );
		$olo_pm_trovato = $olo_pm_atteso === '-' && $olo_pm_pad === [ '' ] ? '-' : implode( ',', $olo_pm_pad );
		if ( ! $olo_pm_voci ) {
			$olo_pm_err[] = 'nessuna voce resa';
		} elseif ( $olo_pm_senza ) {
			$olo_pm_err[] = $olo_pm_atteso === '-'
				? $olo_pm_senza . ' voci con un chip ma chip_style none'
				: $olo_pm_senza . ' voci su ' . $olo_pm_voci . ' senza la classe olo-pm-chip-' . $olo_pm_chip;
		}
		if ( $olo_pm_voci && $olo_pm_pad !== [ $olo_pm_atteso === '-' ? '' : $olo_pm_atteso ] ) {
			$olo_pm_err[] = 'padding diverso dal Vue';
		}
	} elseif ( ! $olo_pm_err ) {
		$olo_pm_err[] = 'HTML vuoto';
	}
	$olo_pm_md5 = $olo_pm_html === '' ? '-' : md5( (string) preg_replace( '/olo-pm-\d{5}\b/', 'olo-pm-UID', $olo_pm_html ) );
	if ( $olo_pm_err ) {
		$olo_pm_ko++;
	}
	WP_CLI::log(
		implode(
			"\t",
			[
				$olo_pm_id,
				$olo_pm_err ? 'KO' : 'OK',
				$olo_pm_chip,
				( $olo_pm_trovato === '' ? '(nessuno)' : $olo_pm_trovato ) . ' (' . ( $olo_pm_atteso === '' ? 'nessuno' : $olo_pm_atteso ) . ')',
				$olo_pm_md5,
				implode( ' | ', $olo_pm_err ),
			]
		)
	);
}
restore_error_handler();
error_reporting( $olo_pm_prima );
if ( $olo_pm_post ) {
	wp_reset_postdata();
}
if ( $olo_pm_ko ) {
	WP_CLI::error( $olo_pm_ko . ' casi su ' . count( $olo_pm_casi ) . ' falliti.' );
}
WP_CLI::success( count( $olo_pm_casi ) . ' casi resi senza errori (12 preset + casi limite), padding dei chip uguale al Vue.' );
