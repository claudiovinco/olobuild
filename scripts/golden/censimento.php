<?php
/**
 * censimento.php — censimento di SOLA LETTURA dei dati salvati, per il sistema compatto
 * (passo 0.1). Dà i numeri che i passi successivi usano per decidere senza indovinare.
 *
 * USO (su mosaic; banco.sh lo lancia con «banco.sh censimento»)
 *   php -d memory_limit=512M /usr/local/bin/wp --allow-root --path=/var/www/wordpress \
 *     eval-file /root/olo-golden/censimento.php out=/root/olo-golden/snap/censimento.json \
 *     [src=all|db|themes] [js=/root/olo-golden/default-js.json]
 *   `js=` è il file di esporta-default-js.mjs (default JS dei config, esportati in locale):
 *   con lui le divergenze PHP↔JS dei default si CALCOLANO invece di elencarle a mano.
 *
 * FONTI (src=, predefinito all)
 *   db      {prefix}olobuild_templates (db-<id>) e i widget globali (gw-<id>)
 *   themes  le pagine dei temi, assets/data/themes/<tema>/<pagina>.json (tema-<tema>--<pagina>):
 *           sono dati che il plugin distribuisce, e import_theme() li salva così come sono
 *           (class-theme-importer.php, create_template senza fondere i default). Su ogni sito
 *           che importa un tema, una chiave che lì manca prende il $defaults PHP: per 0.3 ed E1
 *           contano quanto i template del DB, anche se mosaic quei temi non li ha importati.
 *           Con themes (e all) entra anche la LIBRERIA che il builder inserisce senza fondere i
 *           default (TemplateLibrary.vue → regenerateIds, niente normalizeNodes):
 *           assets/data/template-library.json (lib-<id>) e assets/data/page-templates/*.json
 *           (lib-pagina-<file>).
 *   I conteggi di a), b), c) sono dati anche per fonte (db, gw, tema, lib).
 *
 * LE SEI VOCI (+ una)
 *   a) section.padding per parola, con le parole che il renderer non conosce (rendono il
 *      padding predefinito: trait-olobuild-renderer-structure.php, mappa small/large/…);
 *   b) chiavi dei default assenti nei nodi salvati, per (tipo, chiave): dove una chiave
 *      manca, il renderer usa il $defaults PHP (wp_parse_args) e cambiarlo cambia la resa.
 *      Più le divergenze PHP↔JS (con js=) e i candidati già noti del passo 0.3;
 *   c) chiavi di spazio e raggio valorizzate '' o 'auto' (se 'auto' c'è già su un campo
 *      spazio/raggio, la sentinella di E1 va cambiata prima di E1), anche nelle voci dei
 *      ripetitori (liste di array associativi dentro settings: «settings.items[].btn_padding»);
 *   d) template annidati: templateembed, *_template_id (widget, loop), panel_templates del
 *      megamenu, global_id (widget globali), shortcode [olo_template …] dentro i testi;
 *      più le radici (template attivi e popup globali);
 *   e) usi di tl_density;
 *   f) chiavi per dispositivo (_tablet, _tablet_landscape, _mobile, _mobile_landscape,
 *      _widescreen), style.hover e settings *_hover (servono a B1), anche nelle voci dei
 *      ripetitori;
 *   g) uso dei tipi di tile.
 *
 * USCITA: il JSON in out= (predefinito censimento-<data>.json nella cartella corrente),
 * il riepilogo leggibile su stdout e in <out>.txt. Nessuna scrittura sul sito: ogni query
 * che scrive è bloccata e contata (deve restare 0).
 */

if ( ! defined( 'ABSPATH' ) || ! class_exists( 'WP_CLI' ) ) {
	fwrite( STDERR, "censimento.php va lanciato con «wp eval-file» (vedi scripts/golden/README.md).\n" );
	return;
}

function olo_cens_opzioni( $argv ) {
	$o = [ 'out' => 'censimento-' . gmdate( 'Y-m-d' ) . '.json', 'js' => '', 'src' => 'all' ];
	foreach ( (array) $argv as $a ) {
		$p = strpos( (string) $a, '=' );
		if ( false === $p ) {
			continue;
		}
		$k = substr( $a, 0, $p );
		if ( array_key_exists( $k, $o ) ) {
			$o[ $k ] = substr( $a, $p + 1 );
		}
	}
	return $o;
}

/** Un valore «pieno»: non null, non '', non un array vuoto (o di soli vuoti). */
function olo_cens_pieno( $v ) {
	if ( null === $v || '' === $v ) {
		return false;
	}
	if ( is_array( $v ) ) {
		foreach ( $v as $x ) {
			if ( olo_cens_pieno( $x ) ) {
				return true;
			}
		}
		return false;
	}
	return true;
}

/** Forma canonica di un valore di default, per confrontare PHP e JS ('6' = 6, true = true). */
function olo_cens_canonico( $v ) {
	if ( is_array( $v ) ) {
		$out = [];
		foreach ( $v as $k => $x ) {
			$out[ $k ] = olo_cens_canonico( $x );
		}
		if ( array_keys( $out ) !== range( 0, count( $out ) - 1 ) ) {
			ksort( $out );
		}
		return $out;
	}
	if ( is_bool( $v ) ) {
		return $v ? 'true' : 'false';
	}
	if ( null === $v ) {
		return 'null';
	}
	if ( is_numeric( $v ) ) {
		return (string) ( 0 + $v );
	}
	return (string) $v;
}

function olo_cens_testo( $v ) {
	return is_array( $v ) ? wp_json_encode( $v ) : ( is_bool( $v ) ? ( $v ? 'true' : 'false' ) : ( null === $v ? 'null' : (string) $v ) );
}

/** La fonte di una chiave: db-<id> → db, gw-<id> → gw, tema-<tema>--<pagina> → tema. */
function olo_cens_fonte( $tpl ) {
	$p = strpos( (string) $tpl, '-' );
	return false === $p ? (string) $tpl : substr( (string) $tpl, 0, $p );
}

/** « (db 3 · tema 22)», oppure '' se non c'è niente da dire. */
function olo_cens_fonti_testo( $per_fonte ) {
	$pezzi = [];
	foreach ( (array) $per_fonte as $f => $n ) {
		if ( $n ) {
			$pezzi[] = "$f $n";
		}
	}
	return $pezzi ? ' (' . implode( ' · ', $pezzi ) . ')' : '';
}

/**
 * Le voci di un ripetitore: una lista (chiavi 0..n-1) con almeno un array associativo.
 * I lati di uno spacing ({top,…}) e le liste di numeri ([12,24,12,24]) non lo sono.
 */
function olo_cens_voci( $v ) {
	if ( ! is_array( $v ) || ! $v || array_keys( $v ) !== range( 0, count( $v ) - 1 ) ) {
		return false;
	}
	foreach ( $v as $x ) {
		if ( is_array( $x ) && $x && array_keys( $x ) !== range( 0, count( $x ) - 1 ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Le pagine dei temi, chiave => file, come le rende render-golden.php (stesse chiavi):
 * ogni assets/data/themes/<tema>/*.json salvo theme.json, in ordine stabile.
 */
function olo_cens_pagine_temi() {
	$pulisci = function ( $s ) {
		return preg_replace( '/[^A-Za-z0-9_.-]/', '_', (string) $s );
	};
	$pagine = [];
	$temi   = glob( rtrim( OLOBUILD_PATH, '/' ) . '/assets/data/themes/*', GLOB_ONLYDIR );
	$temi   = $temi ? $temi : [];
	sort( $temi, SORT_STRING );
	foreach ( $temi as $dir ) {
		$files = glob( $dir . '/*.json' );
		$files = $files ? $files : [];
		sort( $files, SORT_STRING );
		foreach ( $files as $f ) {
			if ( 'theme.json' !== basename( $f ) ) {
				$pagine[ 'tema-' . $pulisci( basename( $dir ) ) . '--' . $pulisci( basename( $f, '.json' ) ) ] = $f;
			}
		}
	}
	return $pagine;
}

/**
 * La libreria che il builder inserisce, chiave => nodi (null se illeggibile):
 * lib-<id> per i blocchi di assets/data/template-library.json, lib-pagina-<file> per
 * assets/data/page-templates/*.json. In ordine stabile.
 */
function olo_cens_libreria() {
	$pulisci = function ( $s ) {
		return preg_replace( '/[^A-Za-z0-9_.-]/', '_', (string) $s );
	};
	$base = rtrim( OLOBUILD_PATH, '/' ) . '/assets/data/';
	$out  = [];
	if ( is_readable( $base . 'template-library.json' ) ) {
		$lib = json_decode( (string) file_get_contents( $base . 'template-library.json' ), true );
		if ( ! is_array( $lib ) || ! isset( $lib['templates'] ) || ! is_array( $lib['templates'] ) ) {
			$out['lib-template-library'] = null;
		} else {
			foreach ( $lib['templates'] as $i => $t ) {
				$id                             = is_array( $t ) && ! empty( $t['id'] ) ? $t['id'] : 'n' . $i;
				$out[ 'lib-' . $pulisci( $id ) ] = is_array( $t ) && isset( $t['content'] ) && is_array( $t['content'] ) ? array_values( $t['content'] ) : null;
			}
		}
		unset( $lib );
	}
	$files = glob( $base . 'page-templates/*.json' );
	$files = $files ? $files : [];
	sort( $files, SORT_STRING );
	foreach ( $files as $f ) {
		$json = json_decode( (string) file_get_contents( $f ), true );
		$out[ 'lib-pagina-' . $pulisci( basename( $f, '.json' ) ) ] = is_array( $json ) && isset( $json['content'] ) && is_array( $json['content'] ) ? array_values( $json['content'] ) : null;
	}
	return $out;
}

/** Riferimenti ad altri template dentro i settings (ricorsivo). */
function olo_cens_riferimenti( $v, $chiave, &$out ) {
	if ( is_array( $v ) ) {
		if ( 'panel_templates' === $chiave ) {
			foreach ( $v as $x ) {
				if ( is_numeric( $x ) && (int) $x > 0 ) {
					$out[] = [ 'db-' . (int) $x, 'panel_templates' ];
				}
			}
			return;
		}
		foreach ( $v as $k => $x ) {
			olo_cens_riferimenti( $x, (string) $k, $out );
		}
		return;
	}
	if ( ( 'template_id' === $chiave || 'widget_id' === $chiave || preg_match( '/_template_id$/', $chiave ) ) && is_numeric( $v ) && (int) $v > 0 ) {
		$out[] = [ 'db-' . (int) $v, $chiave ];
		return;
	}
	if ( is_string( $v ) && false !== strpos( $v, '_template' ) && preg_match_all( '/\[(?:olo_template|olobuild_template|mosaic_template)\b[^\]]*?\bid\s*=\s*["\']?(\d+)/', $v, $m ) ) {
		foreach ( $m[1] as $id ) {
			$out[] = [ 'db-' . (int) $id, 'shortcode' ];
		}
	}
}

class Olo_Censimento {
	public $defaults_php = [];
	public $defaults_js  = [];
	public $extra        = []; // chiavi da contare anche se nessun default le dichiara
	public $spazio_js    = [];
	public $nodi         = 0;
	public $uso          = [];
	public $padding      = [];
	public $mancanti     = [];
	public $nodi_tipo    = [];
	public $nodi_tipo_fonte = [];
	public $spazio       = [];
	public $archi        = [];
	public $densita      = [];
	public $disp         = [ 'settings' => [], 'style' => [], 'style_hover' => [], 'settings_hover' => [] ];
	public $disp_chiavi  = [];
	public $non_reg      = [];

	private function segna( &$dove, $voce, $tpl ) {
		if ( ! isset( $dove[ $voce ] ) ) {
			$dove[ $voce ] = [ 'nodi' => 0, 'template' => [], 'fonte' => [] ];
		}
		$dove[ $voce ]['nodi']++;
		$dove[ $voce ]['template'][ $tpl ] = true;
		$f = olo_cens_fonte( $tpl );
		$dove[ $voce ]['fonte'][ $f ] = ( isset( $dove[ $voce ]['fonte'][ $f ] ) ? $dove[ $voce ]['fonte'][ $f ] : 0 ) + 1;
	}

	/** c) '' e 'auto' nelle chiavi di spazio e raggio di $arr, scendendo nelle voci dei ripetitori. */
	private function spazio_in( $arr, $t, $fam, $tpl ) {
		foreach ( $arr as $k => $v ) {
			if ( olo_cens_voci( $v ) ) {
				foreach ( $v as $voce_rip ) {
					if ( is_array( $voce_rip ) ) {
						$this->spazio_in( $voce_rip, $t, $fam . '.' . $k . '[]', $tpl );
					}
				}
				continue;
			}
			if ( ! preg_match( '/padding|margin|radius|gap|spacing/i', (string) $k ) ) {
				continue;
			}
			$valori = is_array( $v ) ? array_filter( $v, 'is_scalar' ) : ( is_scalar( $v ) ? [ $v ] : [] );
			$vuoto  = false;
			$auto   = false;
			foreach ( $valori as $x ) {
				if ( '' === $x ) {
					$vuoto = true;
				} elseif ( is_string( $x ) && 'auto' === strtolower( trim( $x ) ) ) {
					$auto = true;
				}
			}
			if ( ! $vuoto && ! $auto ) {
				continue;
			}
			$voce = $t . ':' . $fam . '.' . $k;
			if ( ! isset( $this->spazio[ $voce ] ) ) {
				$campo = null;
				// spazio_raggio del file js= elenca anche le chiavi degli itemFields
				if ( 0 === strpos( $fam, 'settings' ) && isset( $this->spazio_js[ $t ] ) ) {
					$campo = in_array( (string) $k, $this->spazio_js[ $t ], true );
				}
				$this->spazio[ $voce ] = [ 'vuoto_nodi' => 0, 'auto_nodi' => 0, 'auto_template' => [], 'vuoto_fonte' => [], 'auto_fonte' => [], 'campo_spazio_raggio' => $campo ];
			}
			$f = olo_cens_fonte( $tpl );
			if ( $vuoto ) {
				$this->spazio[ $voce ]['vuoto_nodi']++;
				$this->spazio[ $voce ]['vuoto_fonte'][ $f ] = ( isset( $this->spazio[ $voce ]['vuoto_fonte'][ $f ] ) ? $this->spazio[ $voce ]['vuoto_fonte'][ $f ] : 0 ) + 1;
			}
			if ( $auto ) {
				$this->spazio[ $voce ]['auto_nodi']++;
				$this->spazio[ $voce ]['auto_template'][ $tpl ] = true;
				$this->spazio[ $voce ]['auto_fonte'][ $f ] = ( isset( $this->spazio[ $voce ]['auto_fonte'][ $f ] ) ? $this->spazio[ $voce ]['auto_fonte'][ $f ] : 0 ) + 1;
			}
		}
	}

	/** f) le chiavi non vuote di $arr che finiscono come $re, anche nelle voci dei ripetitori. */
	private function chiavi_con( $arr, $re, $pref, &$trovate ) {
		foreach ( $arr as $k => $v ) {
			if ( olo_cens_voci( $v ) ) {
				foreach ( $v as $voce_rip ) {
					if ( is_array( $voce_rip ) ) {
						$this->chiavi_con( $voce_rip, $re, $pref . $k . '[].', $trovate );
					}
				}
				continue;
			}
			if ( preg_match( $re, (string) $k ) && olo_cens_pieno( $v ) ) {
				$trovate[] = $pref . $k;
			}
		}
	}

	public function visita( $nodi, $tpl ) {
		if ( ! is_array( $nodi ) ) {
			return;
		}
		foreach ( $nodi as $n ) {
			if ( ! is_array( $n ) ) {
				continue;
			}
			$this->nodo( $n, $tpl );
			if ( ! empty( $n['children'] ) ) {
				$this->visita( $n['children'], $tpl );
			}
		}
	}

	private function nodo( $n, $tpl ) {
		$this->nodi++;
		$t  = isset( $n['type'] ) ? (string) $n['type'] : '(senza tipo)';
		$s  = ( isset( $n['settings'] ) && is_array( $n['settings'] ) ) ? $n['settings'] : [];
		$st = ( isset( $n['style'] ) && is_array( $n['style'] ) ) ? $n['style'] : [];

		// g) uso
		$this->segna( $this->uso, $t, $tpl );

		// a) section.padding per parola
		if ( 'section' === $t ) {
			if ( ! array_key_exists( 'padding', $s ) ) {
				$v = '(assente)';
			} elseif ( is_scalar( $s['padding'] ) ) {
				$v = (string) $s['padding'];
			} else {
				$v = '(non stringa)';
			}
			$this->segna( $this->padding, $v, $tpl );
		}

		// b) chiavi dei default assenti (PHP, e JS se c'è il file)
		if ( isset( $this->defaults_php[ $t ] ) || isset( $this->defaults_js[ $t ] ) ) {
			$this->nodi_tipo[ $t ] = ( isset( $this->nodi_tipo[ $t ] ) ? $this->nodi_tipo[ $t ] : 0 ) + 1;
			$fo                    = olo_cens_fonte( $tpl );
			$this->nodi_tipo_fonte[ $t ][ $fo ] = ( isset( $this->nodi_tipo_fonte[ $t ][ $fo ] ) ? $this->nodi_tipo_fonte[ $t ][ $fo ] : 0 ) + 1;
			$chiavi = array_keys(
				( isset( $this->defaults_php[ $t ] ) ? $this->defaults_php[ $t ] : [] )
				+ ( isset( $this->defaults_js[ $t ] ) ? $this->defaults_js[ $t ] : [] )
				+ ( isset( $this->extra[ $t ] ) ? $this->extra[ $t ] : [] )
			);
			foreach ( $chiavi as $k ) {
				if ( ! array_key_exists( $k, $s ) ) {
					if ( ! isset( $this->mancanti[ $t ] ) ) {
						$this->mancanti[ $t ] = [];
					}
					$this->segna( $this->mancanti[ $t ], (string) $k, $tpl );
				}
			}
		} elseif ( isset( $n['type'] ) ) {
			$this->non_reg[ $t ] = ( isset( $this->non_reg[ $t ] ) ? $this->non_reg[ $t ] : 0 ) + 1;
		}

		// c) spazio e raggio a '' o 'auto' (anche nelle voci dei ripetitori)
		$famiglie = [ 'settings' => $s, 'style' => $st, 'style.hover' => ( isset( $st['hover'] ) && is_array( $st['hover'] ) ) ? $st['hover'] : [] ];
		foreach ( $famiglie as $fam => $arr ) {
			$this->spazio_in( $arr, $t, $fam, $tpl );
		}

		// d) annidati
		$rif = [];
		olo_cens_riferimenti( $s, '', $rif );
		if ( ! empty( $n['global_id'] ) && is_numeric( $n['global_id'] ) ) {
			$rif[] = [ 'gw-' . (int) $n['global_id'], 'global_id' ];
		}
		foreach ( $rif as $r ) {
			$this->archi[] = [ 'da' => $tpl, 'a' => $r[0], 'via' => $t . '.' . $r[1] ];
		}

		// e) tl_density
		if ( array_key_exists( 'tl_density', $s ) ) {
			$this->segna( $this->densita, $t . ':' . ( is_scalar( $s['tl_density'] ) ? (string) $s['tl_density'] : '(non stringa)' ), $tpl );
		} elseif ( 'timeline' === $t ) {
			$this->segna( $this->densita, 'timeline:(assente)', $tpl );
		}

		// f) per dispositivo e hover (anche nelle voci dei ripetitori: «settings.items[].x_mobile»)
		foreach ( [ 'settings' => $s, 'style' => $st ] as $fam => $arr ) {
			$trovate = [];
			$this->chiavi_con( $arr, '/_(tablet_landscape|tablet|mobile_landscape|mobile|widescreen)$/', $fam . '.', $trovate );
			foreach ( $trovate as $x ) {
				$this->disp_chiavi[ $x ] = ( isset( $this->disp_chiavi[ $x ] ) ? $this->disp_chiavi[ $x ] : 0 ) + 1;
			}
			if ( $trovate ) {
				$this->segna( $this->disp[ $fam ], 'nodi', $tpl );
			}
		}
		if ( isset( $st['hover'] ) && olo_cens_pieno( $st['hover'] ) ) {
			$this->segna( $this->disp['style_hover'], 'nodi', $tpl );
		}
		$hover = [];
		$this->chiavi_con( $s, '/_hover$/', 'settings.', $hover );
		if ( $hover ) {
			$this->segna( $this->disp['settings_hover'], 'nodi', $tpl );
		}
	}
}

function olo_cens_conta( $voci, $con_template = false ) {
	$out = [];
	foreach ( $voci as $k => $v ) {
		$tpl       = array_keys( $v['template'] );
		$fonti     = isset( $v['fonte'] ) ? $v['fonte'] : [];
		ksort( $fonti );
		$out[ $k ] = [ 'nodi' => $v['nodi'], 'template' => count( $tpl ), 'per_fonte' => $fonti ];
		if ( $con_template ) {
			sort( $tpl );
			$out[ $k ]['elenco'] = $tpl;
		}
	}
	uasort(
		$out,
		function ( $a, $b ) {
			return $b['nodi'] - $a['nodi'];
		}
	);
	return $out;
}

function olo_cens_main( $argv ) {
	global $wpdb;
	$o = olo_cens_opzioni( $argv );
	if ( ! class_exists( 'Olobuild_Database' ) || ! class_exists( 'Olobuild_Tile_Manager' ) ) {
		WP_CLI::error( 'Olobuild non è attivo su questo sito.' );
	}
	@ini_set( 'memory_limit', '512M' ); // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.PHP.IniSet

	// Sola lettura: una query che scrive viene bloccata e contata.
	$GLOBALS['olo_cens_scritture'] = 0;
	add_filter(
		'query',
		function ( $q ) {
			if ( preg_match( '/^\s*(?:INSERT|UPDATE|DELETE|REPLACE|CREATE|ALTER|DROP|TRUNCATE|RENAME)\b/i', (string) $q ) ) {
				$GLOBALS['olo_cens_scritture']++;
				return '';
			}
			return $q;
		},
		PHP_INT_MAX
	);

	$c = new Olo_Censimento();
	foreach ( Olobuild_Tile_Manager::instance()->get_tiles() as $tipo => $tile ) {
		$c->defaults_php[ $tipo ] = (array) $tile->get_defaults();
	}
	$js_meta = null;
	if ( '' !== $o['js'] ) {
		$js = is_readable( $o['js'] ) ? json_decode( (string) file_get_contents( $o['js'] ), true ) : null;
		if ( ! is_array( $js ) || ! isset( $js['tipi'] ) ) {
			WP_CLI::error( 'js=: file illeggibile (lo produce scripts/golden/esporta-default-js.mjs).' );
		}
		foreach ( $js['tipi'] as $tipo => $d ) {
			$c->defaults_js[ $tipo ] = isset( $d['defaults'] ) && is_array( $d['defaults'] ) ? $d['defaults'] : [];
			$c->spazio_js[ $tipo ]   = isset( $d['spazio_raggio'] ) && is_array( $d['spazio_raggio'] ) ? $d['spazio_raggio'] : [];
		}
		$js_meta = [ 'file' => basename( $o['js'] ), 'versione' => isset( $js['versione'] ) ? $js['versione'] : '', 'generato' => isset( $js['generato'] ) ? $js['generato'] : '' ];
	}
	// Candidati già noti del passo 0.3 (lente «evidenze» del 24 set 2026: TILE_DEFAULTS di
	// oloTileDefaults.js contro i $defaults PHP). Si contano anche se nessun default li dichiara.
	$candidati = [
		'button'   => [ 'border_radius', 'tile_padding', 'padding_x', 'padding_y', 'shadow', 'text_color' ],
		'hero'     => [ 'tile_padding' ],
		'alert'    => [ 'text_align', 'shadow' ],
		'headline' => [ 'heading', 'decoration_count', 'decoration_spacing', 'shadow' ],
		'icon'     => [ 'tile_padding' ],
	];
	foreach ( $candidati as $tipo => $chiavi ) {
		foreach ( $chiavi as $k ) {
			$c->extra[ $tipo ][ $k ] = true;
		}
		if ( ! isset( $c->defaults_php[ $tipo ] ) && ! isset( $c->defaults_js[ $tipo ] ) ) {
			$c->defaults_php[ $tipo ] = []; // tipo non registrato: i nodi si contano lo stesso
		}
	}

	if ( ! in_array( $o['src'], [ 'db', 'themes', 'all' ], true ) ) {
		WP_CLI::error( 'src deve essere db, themes o all.' );
	}
	$elenco  = [];
	$stati   = [];
	$esiste  = [];
	$illegg  = [];
	$widgets = [];
	$radici  = [];
	if ( 'themes' !== $o['src'] ) {
		// Template uno alla volta (il content può pesare): id, tipo, stato, poi il contenuto.
		$t  = Olobuild_Database::table( 'templates' );
		$gw = Olobuild_Database::table( 'global_widgets' );
		// phpcs:disable WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- censimento di sola lettura, tabelle del plugin
		$elenco = (array) $wpdb->get_results( "SELECT id, type, status FROM {$t} ORDER BY id ASC", ARRAY_A );
		foreach ( $elenco as $r ) {
			$k              = 'db-' . (int) $r['id'];
			$esiste[ $k ]   = true;
			$stati[ $r['status'] ] = ( isset( $stati[ $r['status'] ] ) ? $stati[ $r['status'] ] : 0 ) + 1;
			$raw            = $wpdb->get_var( $wpdb->prepare( "SELECT content FROM {$t} WHERE id = %d", (int) $r['id'] ) );
			$nodi           = json_decode( (string) $raw, true );
			if ( ! is_array( $nodi ) ) {
				$illegg[] = $k;
				continue;
			}
			$c->visita( $nodi, $k );
			unset( $raw, $nodi );
		}
		$zitto   = $wpdb->suppress_errors( true );
		$widgets = (array) $wpdb->get_results( "SELECT id, tile_data FROM {$gw} ORDER BY id ASC", ARRAY_A );
		$wpdb->suppress_errors( $zitto );
		foreach ( $widgets as $w ) {
			$k            = 'gw-' . (int) $w['id'];
			$esiste[ $k ] = true;
			$nodo         = json_decode( (string) $w['tile_data'], true );
			if ( is_array( $nodo ) ) {
				$c->visita( [ $nodo ], $k );
			}
		}
		// Radici: template attivi (header, footer, archivi, singoli, 404, ricerca) e popup globali.
		foreach ( (array) $wpdb->get_results( "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE 'olobuild\\_active\\_%' ORDER BY option_name", ARRAY_A ) as $r ) {
			if ( (int) $r['option_value'] > 0 ) {
				$radici[ $r['option_name'] ] = 'db-' . (int) $r['option_value'];
			}
		}
		// phpcs:enable
		$popup = get_option( 'olobuild_global_popups', [] );
		foreach ( is_array( $popup ) ? $popup : [] as $i => $p ) {
			if ( is_array( $p ) && ! empty( $p['template_id'] ) ) {
				$radici[ 'olobuild_global_popups[' . $i . ']' ] = 'db-' . (int) $p['template_id'];
			}
		}
	}
	// Le pagine dei temi, nei due formati che legge import_theme(): la lista di nodi oppure
	// { settings, content }.
	if ( 'db' !== $o['src'] && ! defined( 'OLOBUILD_PATH' ) ) {
		WP_CLI::error( 'OLOBUILD_PATH non definita: non trovo assets/data/themes.' );
	}
	$temi = 'db' !== $o['src'] ? olo_cens_pagine_temi() : [];
	foreach ( $temi as $k => $file ) {
		$esiste[ $k ] = true;
		$json         = json_decode( (string) file_get_contents( $file ), true );
		if ( ! is_array( $json ) ) {
			$illegg[] = $k;
			continue;
		}
		$nodi = ( isset( $json['content'] ) && is_array( $json['content'] ) && ! isset( $json[0] ) ) ? $json['content'] : $json;
		$c->visita( $nodi, $k );
		unset( $json, $nodi );
	}
	// La libreria (blocchi di template-library.json e pagine di page-templates/): il builder la
	// inserisce senza fondere i default, come le pagine dei temi.
	$libreria = 'db' !== $o['src'] ? olo_cens_libreria() : [];
	$n_lib    = count( $libreria );
	foreach ( $libreria as $k => $nodi ) {
		$esiste[ $k ] = true;
		if ( ! is_array( $nodi ) ) {
			$illegg[] = $k;
			continue;
		}
		$c->visita( $nodi, $k );
	}
	unset( $libreria );

	// ── a) section.padding
	$note_mappa = [ 'remove-vertical', 'small', 'default', 'large', 'xlarge', 'custom', '(assente)' ];
	$a          = [ 'valori' => olo_cens_conta( $c->padding ), 'non_riconosciute' => [] ];
	foreach ( olo_cens_conta( $c->padding, true ) as $v => $d ) {
		if ( ! in_array( (string) $v, $note_mappa, true ) ) {
			$a['non_riconosciute'][ $v ] = $d;
		}
	}
	$a['nota'] = 'Le parole fuori da remove-vertical/small/default/large/xlarge/custom non sono voci della select né della mappa del renderer: rendono il padding predefinito di .uk-section.';

	// ── b) chiavi dei default assenti, divergenze PHP↔JS, candidati 0.3
	$b        = [ 'tipi' => [], 'divergenze' => null, 'candidati_0_3' => [], 'tipi_non_registrati' => $c->non_reg ];
	$nt_fonte = function ( $tipo ) use ( $c ) {
		$x = isset( $c->nodi_tipo_fonte[ $tipo ] ) ? $c->nodi_tipo_fonte[ $tipo ] : [];
		ksort( $x );
		return $x;
	};
	foreach ( $c->mancanti as $tipo => $chiavi ) {
		$righe = [];
		foreach ( olo_cens_conta( $chiavi ) as $k => $d ) {
			$righe[ $k ] = $d + [
				'php' => array_key_exists( $k, isset( $c->defaults_php[ $tipo ] ) ? $c->defaults_php[ $tipo ] : [] ) ? olo_cens_testo( $c->defaults_php[ $tipo ][ $k ] ) : '(assente)',
			];
		}
		$b['tipi'][ $tipo ] = [ 'nodi' => $c->nodi_tipo[ $tipo ], 'nodi_per_fonte' => $nt_fonte( $tipo ), 'chiavi_assenti' => $righe ];
	}
	ksort( $b['tipi'] );
	$manca = function ( $tipo, $k, $campo ) use ( $c ) {
		$d = isset( $c->mancanti[ $tipo ][ $k ] ) ? $c->mancanti[ $tipo ][ $k ] : null;
		if ( 'fonte' === $campo ) {
			$x = $d ? $d['fonte'] : [];
			ksort( $x );
			return $x;
		}
		return 'nodi' === $campo ? ( $d ? $d['nodi'] : 0 ) : ( $d ? count( $d['template'] ) : 0 );
	};
	if ( $c->defaults_js ) {
		$div = [];
		foreach ( $c->defaults_js as $tipo => $js_def ) {
			if ( ! isset( $c->defaults_php[ $tipo ] ) ) {
				continue;
			}
			$php_def = $c->defaults_php[ $tipo ];
			foreach ( array_keys( $php_def + $js_def ) as $k ) {
				$in_php = array_key_exists( $k, $php_def );
				$in_js  = array_key_exists( $k, $js_def );
				if ( $in_php && $in_js && olo_cens_canonico( $php_def[ $k ] ) === olo_cens_canonico( $js_def[ $k ] ) ) {
					continue;
				}
				$div[] = [
					'tipo'                  => $tipo,
					'chiave'                => (string) $k,
					'genere'                => $in_php && $in_js ? 'valore' : ( $in_php ? 'solo_php' : 'solo_js' ),
					'php'                   => $in_php ? olo_cens_testo( $php_def[ $k ] ) : '(assente)',
					'js'                    => $in_js ? olo_cens_testo( $js_def[ $k ] ) : '(assente)',
					'nodi_tipo'             => isset( $c->nodi_tipo[ $tipo ] ) ? $c->nodi_tipo[ $tipo ] : 0,
					'nodi_tipo_per_fonte'   => $nt_fonte( $tipo ),
					'nodi_senza_chiave'     => $manca( $tipo, (string) $k, 'nodi' ),
					'senza_chiave_per_fonte' => $manca( $tipo, (string) $k, 'fonte' ),
					'template_senza_chiave' => $manca( $tipo, (string) $k, 'template' ),
				];
			}
		}
		$b['divergenze'] = $div;
	}
	foreach ( $candidati as $tipo => $chiavi ) {
		foreach ( $chiavi as $k ) {
			$php_def = isset( $c->defaults_php[ $tipo ] ) ? $c->defaults_php[ $tipo ] : [];
			$js_def  = $c->defaults_js ? ( isset( $c->defaults_js[ $tipo ] ) ? $c->defaults_js[ $tipo ] : [] ) : null;
			$b['candidati_0_3'][] = [
				'tipo'                  => $tipo,
				'chiave'                => $k,
				'php'                   => array_key_exists( $k, $php_def ) ? olo_cens_testo( $php_def[ $k ] ) : '(assente)',
				'js'                    => null === $js_def ? '(senza js=)' : ( array_key_exists( $k, $js_def ) ? olo_cens_testo( $js_def[ $k ] ) : '(assente)' ),
				'nodi_tipo'             => isset( $c->nodi_tipo[ $tipo ] ) ? $c->nodi_tipo[ $tipo ] : 0,
				'nodi_tipo_per_fonte'   => $nt_fonte( $tipo ),
				'nodi_senza_chiave'     => $manca( $tipo, $k, 'nodi' ),
				'senza_chiave_per_fonte' => $manca( $tipo, $k, 'fonte' ),
				'template_senza_chiave' => $manca( $tipo, $k, 'template' ),
			];
		}
	}

	// ── c) '' e 'auto'
	$c_out = [ 'per_chiave' => [], 'vuoto_nodi' => 0, 'auto_nodi' => 0, 'vuoto_per_fonte' => [], 'auto_per_fonte' => [], 'auto_template' => [], 'auto_su_campo_spazio_raggio' => [] ];
	foreach ( $c->spazio as $voce => $d ) {
		$tpl = array_keys( $d['auto_template'] );
		sort( $tpl );
		ksort( $d['vuoto_fonte'] );
		ksort( $d['auto_fonte'] );
		$c_out['per_chiave'][ $voce ] = [ 'vuoto_nodi' => $d['vuoto_nodi'], 'auto_nodi' => $d['auto_nodi'], 'vuoto_per_fonte' => $d['vuoto_fonte'], 'auto_per_fonte' => $d['auto_fonte'], 'auto_template' => $tpl, 'campo_spazio_raggio' => $d['campo_spazio_raggio'] ];
		$c_out['vuoto_nodi'] += $d['vuoto_nodi'];
		$c_out['auto_nodi']  += $d['auto_nodi'];
		foreach ( [ 'vuoto', 'auto' ] as $qu ) {
			foreach ( $d[ $qu . '_fonte' ] as $fo => $n ) {
				$c_out[ $qu . '_per_fonte' ][ $fo ] = ( isset( $c_out[ $qu . '_per_fonte' ][ $fo ] ) ? $c_out[ $qu . '_per_fonte' ][ $fo ] : 0 ) + $n;
			}
		}
		foreach ( $tpl as $x ) {
			$c_out['auto_template'][ $x ] = true;
		}
		if ( $d['auto_nodi'] && false !== $d['campo_spazio_raggio'] ) {
			$c_out['auto_su_campo_spazio_raggio'][] = $voce;
		}
	}
	ksort( $c_out['per_chiave'] );
	ksort( $c_out['vuoto_per_fonte'] );
	ksort( $c_out['auto_per_fonte'] );
	$c_out['auto_template'] = array_keys( $c_out['auto_template'] );
	sort( $c_out['auto_template'] );
	$c_out['nota'] = "Se auto_su_campo_spazio_raggio non è vuoto, 'auto' compare già in un campo spazio/raggio: la sentinella di E1 va cambiata prima di E1. campo_spazio_raggio = null quando manca js= (non si sa se la chiave è un campo spacing/border-radius).";

	// ── d) annidati
	$figli = [];
	$manc  = [];
	foreach ( $c->archi as $e ) {
		$figli[ $e['da'] ][ $e['a'] ] = true;
		if ( ! isset( $esiste[ $e['a'] ] ) ) {
			$manc[ $e['a'] ] = true;
		}
	}
	$prof  = [];
	$cicli = [];
	$lungo = function ( $k, $pila ) use ( &$lungo, &$prof, &$cicli, $figli ) {
		if ( isset( $prof[ $k ] ) ) {
			return $prof[ $k ];
		}
		if ( isset( $pila[ $k ] ) ) {
			$cicli[] = implode( ' → ', array_keys( $pila ) ) . ' → ' . $k;
			return 0;
		}
		$pila[ $k ] = true;
		$max        = 0;
		foreach ( isset( $figli[ $k ] ) ? array_keys( $figli[ $k ] ) : [] as $f ) {
			$max = max( $max, 1 + $lungo( $f, $pila ) );
		}
		$prof[ $k ] = $max;
		return $max;
	};
	$pmax = 0;
	foreach ( array_keys( $figli ) as $k ) {
		$pmax = max( $pmax, $lungo( $k, [] ) );
	}
	$per_via = [];
	foreach ( $c->archi as $e ) {
		$per_via[ $e['via'] ] = ( isset( $per_via[ $e['via'] ] ) ? $per_via[ $e['via'] ] : 0 ) + 1;
	}
	arsort( $per_via );
	$d = [
		'archi'                  => $c->archi,
		'archi_per_via'          => $per_via,
		'template_con_figli'     => count( $figli ),
		'profondita_max'         => $pmax,
		'cicli'                  => array_values( array_unique( $cicli ) ),
		'destinazioni_mancanti'  => array_keys( $manc ),
		'radici'                 => $radici,
	];

	// ── e) tl_density
	$e = [ 'valori' => olo_cens_conta( $c->densita, true ) ];

	// ── f) per dispositivo e hover
	$f = [];
	foreach ( [ 'settings' => 'settings con chiavi _tablet/_mobile/_widescreen…', 'style' => 'style con chiavi per dispositivo', 'style_hover' => 'style.hover non vuoto', 'settings_hover' => 'settings *_hover non vuoti' ] as $k => $etichetta ) {
		$dd      = isset( $c->disp[ $k ]['nodi'] ) ? $c->disp[ $k ]['nodi'] : [ 'nodi' => 0, 'template' => [] ];
		$tpl     = array_keys( $dd['template'] );
		sort( $tpl );
		$f[ $k ] = [ 'descrizione' => $etichetta, 'nodi' => $dd['nodi'], 'template' => count( $tpl ), 'elenco' => $tpl ];
	}
	arsort( $c->disp_chiavi );
	$f['per_chiave'] = $c->disp_chiavi;

	// ── g) uso dei tipi
	$g = olo_cens_conta( $c->uso );

	$json = [
		'generato'   => gmdate( 'Y-m-d H:i:s' ) . ' UTC',
		'sito'       => home_url( '/' ),
		'olobuild'   => defined( 'OLOBUILD_VERSION' ) ? OLOBUILD_VERSION : '?',
		'sorgente'   => [
			'src'               => $o['src'],
			'template'          => count( $elenco ),
			'per_stato'         => $stati,
			'illeggibili'       => $illegg,
			'widget_globali'    => count( $widgets ),
			'pagine_temi'       => count( $temi ),
			'libreria'          => $n_lib,
			'nodi_visitati'     => $c->nodi,
			'default_js'        => $js_meta,
			'scritture_bloccate' => $GLOBALS['olo_cens_scritture'],
		],
		'a_section_padding'   => $a,
		'b_chiavi_default'    => $b,
		'c_spazio_vuoto_auto' => $c_out,
		'd_annidati'          => $d,
		'e_tl_density'        => $e,
		'f_dispositivo_hover' => $f,
		'g_uso_tipi'          => $g,
	];

	// ── riepilogo leggibile
	$r   = [];
	$r[] = sprintf( 'Censimento compatto 0.1 — %s — Olobuild %s — %s', $json['sito'], $json['olobuild'], $json['generato'] );
	$ps  = [];
	foreach ( $stati as $k => $v ) {
		$ps[] = "$k $v";
	}
	$r[] = sprintf( 'Fonti (src=%s): template %d (%s) · widget globali %d · pagine dei temi %d · libreria %d · nodi visitati %d%s', $o['src'], count( $elenco ), implode( ', ', $ps ), count( $widgets ), count( $temi ), $n_lib, $c->nodi, $illegg ? ' · JSON illeggibili: ' . implode( ', ', $illegg ) : '' );
	$pv  = [];
	foreach ( $a['valori'] as $k => $v ) {
		$pv[] = "$k {$v['nodi']}" . olo_cens_fonti_testo( $v['per_fonte'] );
	}
	$r[] = 'a) section.padding: ' . implode( ' · ', $pv );
	$nr  = [];
	foreach ( $a['non_riconosciute'] as $k => $v ) {
		$nr[] = "«$k» {$v['nodi']} nodi in {$v['template']} template" . olo_cens_fonti_testo( $v['per_fonte'] );
	}
	$r[] = '   parole fuori mappa (rendono il predefinito): ' . ( $nr ? implode( ' · ', $nr ) : 'nessuna' );
	$coppie = 0;
	foreach ( $b['tipi'] as $x ) {
		$coppie += count( $x['chiavi_assenti'] );
	}
	$r[] = sprintf( 'b) coppie (tipo, chiave dei default) assenti in almeno un nodo: %d su %d tipi usati', $coppie, count( $b['tipi'] ) );
	if ( null !== $b['divergenze'] ) {
		$gen = [ 'valore' => 0, 'solo_php' => 0, 'solo_js' => 0 ];
		$pes = 0;
		foreach ( $b['divergenze'] as $x ) {
			$gen[ $x['genere'] ]++;
			$pes += $x['nodi_senza_chiave'] ? 1 : 0;
		}
		$r[] = sprintf( '   divergenze PHP↔JS dei default: %d (valore %d, solo PHP %d, solo JS %d); %d con nodi salvati senza la chiave', count( $b['divergenze'] ), $gen['valore'], $gen['solo_php'], $gen['solo_js'], $pes );
	} else {
		$r[] = '   divergenze PHP↔JS: non calcolate (serve js=, da esporta-default-js.mjs)';
	}
	foreach ( $b['candidati_0_3'] as $x ) {
		$r[] = sprintf( '   %s.%s: PHP %s · JS %s · %d nodi del tipo%s, %d senza la chiave%s (%d template)', $x['tipo'], $x['chiave'], $x['php'], $x['js'], $x['nodi_tipo'], olo_cens_fonti_testo( $x['nodi_tipo_per_fonte'] ), $x['nodi_senza_chiave'], olo_cens_fonti_testo( $x['senza_chiave_per_fonte'] ), $x['template_senza_chiave'] );
	}
	$r[] = sprintf( "c) spazio/raggio (anche nelle voci dei ripetitori): '' in %d occorrenze%s, 'auto' in %d%s (%d template)%s", $c_out['vuoto_nodi'], olo_cens_fonti_testo( $c_out['vuoto_per_fonte'] ), $c_out['auto_nodi'], olo_cens_fonti_testo( $c_out['auto_per_fonte'] ), count( $c_out['auto_template'] ), $c_out['auto_su_campo_spazio_raggio'] ? ' → «auto» su campi spazio/raggio: ' . implode( ', ', $c_out['auto_su_campo_spazio_raggio'] ) . ' — cambiare sentinella prima di E1' : '' );
	$r[] = sprintf( 'd) annidati: %d archi (%s), %d template con figli, profondità massima %d, cicli %d, destinazioni mancanti %d · radici %d', count( $c->archi ), implode( ', ', array_map( function ( $k, $v ) { return "$k $v"; }, array_keys( $per_via ), $per_via ) ), count( $figli ), $pmax, count( $d['cicli'] ), count( $manc ), count( $radici ) );
	$dv  = [];
	foreach ( $e['valori'] as $k => $v ) {
		$dv[] = "$k {$v['nodi']}";
	}
	$r[] = 'e) tl_density: ' . ( $dv ? implode( ' · ', $dv ) : 'nessun uso' );
	$r[] = sprintf(
		'f) per dispositivo: settings %d nodi/%d template · style %d/%d — hover: style.hover %d/%d · settings *_hover %d/%d',
		$f['settings']['nodi'], $f['settings']['template'], $f['style']['nodi'], $f['style']['template'],
		$f['style_hover']['nodi'], $f['style_hover']['template'], $f['settings_hover']['nodi'], $f['settings_hover']['template']
	);
	$top = array_slice( $g, 0, 12, true );
	$r[] = sprintf( 'g) %d tipi usati; i più usati: %s', count( $g ), implode( ', ', array_map( function ( $k, $v ) { return "$k {$v['nodi']}"; }, array_keys( $top ), $top ) ) );
	$r[] = 'Scritture bloccate durante il censimento: ' . $GLOBALS['olo_cens_scritture'] . ' (deve essere 0)';

	$dir = dirname( $o['out'] );
	if ( '' !== $dir && ! is_dir( $dir ) ) {
		mkdir( $dir, 0755, true );
	}
	file_put_contents( $o['out'], wp_json_encode( $json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n" );
	file_put_contents( preg_replace( '/\.json$/', '', $o['out'] ) . '.txt', implode( "\n", $r ) . "\n" );
	foreach ( $r as $l ) {
		WP_CLI::log( $l );
	}
	WP_CLI::success( 'Censimento in ' . $o['out'] );
}

olo_cens_main( isset( $args ) && is_array( $args ) ? $args : [] );
