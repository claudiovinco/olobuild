<?php
/**
 * render-golden.php — banco L1 del sistema compatto (passo 0.1): l'HTML di ogni template,
 * reso come lo rende il sito, in modo ripetibile e senza scrivere niente.
 *
 * COSA RENDE
 *   src=db      tutti i template di {prefix}olobuild_templates, in qualunque stato
 *   src=themes  le pagine dei temi, assets/data/themes/<tema>/<pagina>.json
 *   src=all     entrambi (predefinito)
 * Con l'ingresso del sito: (new Olobuild_Frontend_Renderer())->render_shortcode(['id' => N]),
 * lo stesso di [olobuild_template] e di auto_render_template (class-page-integration.php).
 * NON render_tiles_array (anteprima REST e iframe): niente .olo-template, niente index_tiles.
 * Le pagine dei temi entrano dallo stesso ingresso con una riga finta nella cache NON
 * persistente del gruppo 'olo' (Olobuild_Database::get_template legge prima la cache).
 *
 * UN PROCESSO PER TEMPLATE
 *   Il renderer tiene stato statico per processo: solo le prime 3 tile del PROCESSO sono
 *   eager (maybe_lazy_wrap, static $element_counter), gli script «stampati una volta»,
 *   i registri di index_tiles, il contatore di wp_unique_id. Resi in fila, l'HTML di un
 *   template dipenderebbe da quelli resi prima. Il processo principale elenca le chiavi e
 *   lancia `wp eval-file render-golden.php chiave=<k>` per ognuna (WP_CLI::runcommand, che
 *   ripete da sé --path, --require, --allow-root, --url).
 *
 * RIPETIBILE
 *   - golden-stub.php (--require): wp_rand() e wp_create_nonce() fissi dentro il render;
 *   - mt_srand(crc32(chiave)): shuffle() di gallery e proslider, wp_generate_uuid4();
 *   - ORDER BY RAND() → RAND(1) (relatedposts, woo-related, productgrid);
 *   - rete spenta (pre_http_request): la video tile usa sempre la miniatura hq di ripiego;
 *   - normalizza.json sull'HTML: il token dei moduli (time()) e il ?ver= del plugin.
 *   Restano legati all'ora e al giorno, e sono elencati nella colonna «volatili»: condizioni
 *   per data e ora (anche la seconda, in OR), popup e hiddenpop programmati, postmeta con la
 *   data di oggi, presencegrid, il badge «nuovo» di queryloop, i campi dinamici datetime, i
 *   tag {{current_date|time|year}} e {current_date|year} / {post_count} di resolve_tokens.
 *   Anche quelli dei template incorporati e dei widget globali, che si rendono dentro.
 *
 * SOLA LETTURA (figli E pilota: generate_css() passa da Olobuild_Font_Host)
 *   - ogni INSERT/UPDATE/DELETE/REPLACE/CREATE/ALTER/DROP/TRUNCATE/RENAME è bloccato e contato;
 *   - cache CSS su file del plugin spenta (css_cache_files=false via pre_option): il CSS di
 *     hover e dispositivo resta inline nell'HTML (dove il confronto lo vede) invece di finire
 *     in uploads/olobuild-cache con un <link …?v=VERSIONE>;
 *   - transient e gruppo 'olo' solo in memoria; il processo principale confronta
 *     uploads/olobuild-cache prima e dopo e lo scrive in _statistiche.txt, con le scritture
 *     e le richieste di rete che ha bloccato a sé stesso (riga «pilota:»).
 *
 * USO (su mosaic: vedi README.md, oppure banco.sh che fa tutto)
 *   php -d memory_limit=512M /usr/local/bin/wp --allow-root --path=/var/www/wordpress \
 *     --require=/root/olo-golden/golden-stub.php \
 *     eval-file /root/olo-golden/render-golden.php src=all out=/root/olo-golden/snap/prima
 *   Altri argomenti: ordine=inverso · solo=db-7,tema-atelier--homepage · mem=512M · elenco=1
 *
 * USCITA in out/
 *   <chiave>.html      HTML normalizzato (chiave = db-<id> | tema-<tema>--<pagina>)
 *   _summary.tsv       una riga per chiave: md5, byte, metriche, volatili, errori
 *   _md5.txt           «md5  chiave»: il diff di due fasi è il confronto (confronta.sh)
 *   _style-system.css  generate_css() dello Style System (V4: fra due fasi solo aggiunte)
 *   _dati.tsv          impronta dei DATI (distingue «dati cambiati» da «resa cambiata»)
 *   _urls.tsv          permalink dei post collegati ai template (per golden-shots.mjs)
 *   _ambiente.txt      versioni, tema, plugin, impronte delle opzioni di stile
 *   _statistiche.txt   totali e metriche (style="" vuoti, pxpx, CSS nel body, duplicati)
 *   _solo.txt          solo con solo=…: le chiavi rese (snapshot parziale, per confronta.sh)
 *   _grezzo/  HTML prima della normalizzazione (solo se diverso) · _avvisi/  warning,
 *   scritture e rete bloccate (_pilota.txt per il processo principale) · _righe/  la riga
 *   di ogni processo figlio
 *
 * Compatibile PHP 7.4. Nessuna funzione del plugin viene modificata: solo filtri locali al
 * processo del banco.
 */

if ( ! defined( 'ABSPATH' ) || ! class_exists( 'WP_CLI' ) ) {
	fwrite( STDERR, "render-golden.php va lanciato con «wp eval-file» (vedi scripts/golden/README.md).\n" );
	return;
}

/** Colonne di _summary.tsv, nell'ordine. */
function olo_golden_colonne() {
	return [
		'chiave', 'fonte', 'id', 'tipo', 'stato', 'md5', 'md5_grezzo', 'byte',
		'wrapper', 'wrapper_style_vuoto', 'style_vuoti', 'decl_vuote', 'pxpx',
		'css_blocchi', 'css_byte', 'css_dup_byte', 'rand', 'accodati', 'volatili',
		'scritture', 'rete', 'avvisi', 'errore',
	];
}

function olo_golden_opzioni( $argv ) {
	$o = [ 'src' => 'all', 'out' => '', 'chiave' => '', 'ordine' => '', 'solo' => '', 'mem' => '', 'elenco' => false ];
	foreach ( (array) $argv as $a ) {
		$a = (string) $a;
		$p = strpos( $a, '=' );
		if ( false === $p ) {
			if ( 'elenco' === $a ) {
				$o['elenco'] = true;
			} else {
				WP_CLI::warning( "Argomento ignorato (si scrive chiave=valore): $a" );
			}
			continue;
		}
		$k = substr( $a, 0, $p );
		$v = substr( $a, $p + 1 );
		if ( 'elenco' === $k ) {
			$o['elenco'] = ( '' !== $v && '0' !== $v );
		} elseif ( array_key_exists( $k, $o ) ) {
			$o[ $k ] = $v;
		} else {
			WP_CLI::warning( "Argomento sconosciuto ignorato: $k" );
		}
	}
	if ( '' !== $o['out'] && ! preg_match( '~^(/|[A-Za-z]:[\\\\/])~', $o['out'] ) ) {
		$o['out'] = rtrim( (string) getcwd(), '/' ) . '/' . $o['out'];
	}
	$o['out'] = rtrim( $o['out'], '/' );
	return $o;
}

function olo_golden_pulisci( $s ) {
	return preg_replace( '/[^A-Za-z0-9_.-]/', '_', (string) $s );
}

function olo_golden_cartelle( $out ) {
	foreach ( [ '', '/_righe', '/_avvisi', '/_grezzo' ] as $sotto ) {
		if ( ! is_dir( $out . $sotto ) && ! mkdir( $out . $sotto, 0755, true ) && ! is_dir( $out . $sotto ) ) {
			WP_CLI::error( "Non riesco a creare {$out}{$sotto}" );
		}
	}
}

function olo_golden_tsv( $v ) {
	return str_replace( [ "\t", "\r", "\n" ], ' ', (string) $v );
}

/**
 * Le pagine dei temi: chiave => [file, tema, pagina, tipo, titolo], in ordine stabile.
 */
function olo_golden_pagine_temi() {
	static $pagine = null;
	if ( null !== $pagine ) {
		return $pagine;
	}
	$pagine = [];
	$base   = rtrim( OLOBUILD_PATH, '/' ) . '/assets/data/themes';
	$temi   = glob( $base . '/*', GLOB_ONLYDIR );
	$temi   = $temi ? $temi : [];
	sort( $temi, SORT_STRING );
	foreach ( $temi as $dir ) {
		$slug  = basename( $dir );
		$mappa = [];
		$meta  = is_readable( $dir . '/theme.json' ) ? json_decode( (string) file_get_contents( $dir . '/theme.json' ), true ) : null;
		if ( is_array( $meta ) && isset( $meta['templates'] ) && is_array( $meta['templates'] ) ) {
			foreach ( $meta['templates'] as $tk => $tpl ) {
				if ( is_array( $tpl ) && ! empty( $tpl['file'] ) ) {
					$mappa[ (string) $tpl['file'] ] = [
						'tipo'   => isset( $tpl['type'] ) ? (string) $tpl['type'] : 'page',
						'titolo' => isset( $tpl['title'] ) ? (string) $tpl['title'] : (string) $tk,
					];
				}
			}
		}
		$files = glob( $dir . '/*.json' );
		$files = $files ? $files : [];
		sort( $files, SORT_STRING );
		foreach ( $files as $f ) {
			$nome = basename( $f );
			if ( 'theme.json' === $nome ) {
				continue;
			}
			$pagina = basename( $f, '.json' );
			$info   = isset( $mappa[ $nome ] ) ? $mappa[ $nome ] : [
				'tipo'   => in_array( $pagina, [ 'header', 'footer' ], true ) ? $pagina : 'page',
				'titolo' => $slug . ' — ' . $pagina,
			];
			$pagine[ 'tema-' . olo_golden_pulisci( $slug ) . '--' . olo_golden_pulisci( $pagina ) ] = $info + [
				'file'   => $f,
				'tema'   => $slug,
				'pagina' => $pagina,
			];
		}
	}
	return $pagine;
}

function olo_golden_chiavi( $src ) {
	global $wpdb;
	$chiavi = [];
	if ( 'db' === $src || 'all' === $src ) {
		$t = Olobuild_Database::table( 'templates' );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- banco di sola lettura, tabella del plugin
		foreach ( (array) $wpdb->get_col( "SELECT id FROM {$t} ORDER BY id ASC" ) as $id ) {
			$chiavi[] = 'db-' . (int) $id;
		}
	}
	if ( 'themes' === $src || 'all' === $src ) {
		$chiavi = array_merge( $chiavi, array_keys( olo_golden_pagine_temi() ) );
	}
	return $chiavi;
}

// ─── guardie del processo figlio ─────────────────────────────────────────────

function olo_golden_filtro_query( $q ) {
	if ( preg_match( '/^\s*(?:INSERT|UPDATE|DELETE|REPLACE|CREATE|ALTER|DROP|TRUNCATE|RENAME)\b/i', (string) $q ) ) {
		$GLOBALS['olo_golden_bloccati']['scritture'][] = substr( preg_replace( '/\s+/', ' ', (string) $q ), 0, 200 );
		return ''; // wpdb::query() con query vuota non fa niente e restituisce false
	}
	if ( false !== stripos( (string) $q, 'RAND(' ) ) {
		$n = 0;
		$q = preg_replace( '/\bRAND\(\s*\)/i', 'RAND(1)', (string) $q, -1, $n );
		$GLOBALS['olo_golden_bloccati']['rand_sql'] += $n;
	}
	return $q;
}

function olo_golden_filtro_rete( $pre, $parsed = [], $url = '' ) {
	$host = wp_parse_url( (string) $url, PHP_URL_HOST );
	$GLOBALS['olo_golden_bloccati']['rete'][] = $host ? $host : (string) $url;
	return new WP_Error( 'olo_golden_offline', 'Rete spenta dal banco golden' );
}

function olo_golden_guardie() {
	$GLOBALS['olo_golden_bloccati'] = [ 'scritture' => [], 'rete' => [], 'rand_sql' => 0 ];
	add_filter( 'query', 'olo_golden_filtro_query', PHP_INT_MAX );
	add_filter( 'pre_http_request', 'olo_golden_filtro_rete', PHP_INT_MAX, 3 );
	// Pari merito in ordine fisso: post con la stessa data (importati insieme) tornano da MySQL
	// in ordine libero, e la mappa dei servizi (db-39) cambiava a ogni giro. Si aggiunge l'ID
	// come ultimo criterio: le righe restano quelle, cambia solo l'ordine dei pari merito.
	add_filter(
		'posts_orderby',
		function ( $orderby ) {
			global $wpdb;
			$orderby = (string) $orderby;
			if ( '' === trim( $orderby ) || false !== stripos( $orderby, $wpdb->posts . '.ID' ) ) {
				return $orderby;
			}
			return $orderby . ", {$wpdb->posts}.ID DESC";
		},
		PHP_INT_MAX
	);

	// Cache CSS su file spenta, il resto delle impostazioni resta quello del sito.
	$perf = get_option( 'olobuild_performance', [] );
	$perf = is_array( $perf ) ? $perf : [];
	$perf['css_cache_files'] = false;
	add_filter(
		'pre_option_olobuild_performance',
		function () use ( $perf ) {
			return $perf;
		},
		PHP_INT_MAX
	);

	// Transient e template solo in memoria: niente letture di valori scaduti fra una fase e
	// l'altra, niente scritture nella cache persistente (se c'è).
	if ( function_exists( 'wp_cache_add_non_persistent_groups' ) ) {
		wp_cache_add_non_persistent_groups( [ 'olo', 'transient', 'site-transient' ] );
	}
	wp_using_ext_object_cache( true );
}

function olo_golden_avviso( $errno, $errstr, $errfile = '', $errline = 0 ) {
	if ( ! ( error_reporting() & $errno ) ) {
		return false; // silenziato con @: comportamento normale
	}
	$GLOBALS['olo_golden_avvisi'][] = $errno . ' ' . $errstr . ' @ ' . basename( (string) $errfile ) . ':' . $errline;
	return true; // registrato, e fuori dall'HTML
}

// ─── misure sull'HTML ────────────────────────────────────────────────────────

function olo_golden_normalizza( $html ) {
	static $regole = null;
	if ( null === $regole ) {
		$regole = [];
		$json   = json_decode( (string) file_get_contents( __DIR__ . '/normalizza.json' ), true );
		$ver    = defined( 'OLOBUILD_VERSION' ) ? preg_quote( OLOBUILD_VERSION, '~' ) : '';
		foreach ( ( is_array( $json ) && isset( $json['regole'] ) ) ? $json['regole'] : [] as $r ) {
			$cerca = (string) $r['cerca'];
			if ( false !== strpos( $cerca, '{VERSIONE}' ) ) {
				if ( '' === $ver ) {
					continue;
				}
				$cerca = str_replace( '{VERSIONE}', $ver, $cerca );
			}
			$regole[] = [ '~' . str_replace( '~', '\~', $cerca ) . '~', (string) $r['sostituisci'] ];
		}
		if ( ! $regole ) {
			WP_CLI::error( 'normalizza.json mancante o illeggibile accanto a render-golden.php' );
		}
	}
	foreach ( $regole as $r ) {
		$html = preg_replace( $r[0], $r[1], $html );
	}
	return $html;
}

function olo_golden_misure( $html ) {
	$m                        = [];
	$m['byte']                = strlen( $html );
	$m['wrapper']             = preg_match_all( '/<div class="olo-frontend-tile[\s"]/', $html );
	$m['wrapper_style_vuoto'] = preg_match_all( '/<div class="olo-frontend-tile[\s"][^>]*?\sstyle=""/', $html );
	$m['style_vuoti']         = substr_count( $html, ' style=""' );
	$vuote                    = 0;
	if ( preg_match_all( '/\sstyle="([^"]*)"/', $html, $sa ) ) {
		foreach ( $sa[1] as $v ) {
			foreach ( explode( ';', html_entity_decode( $v, ENT_QUOTES ) ) as $d ) {
				$p = strpos( $d, ':' );
				if ( false !== $p && '' !== trim( substr( $d, 0, $p ) ) && '' === trim( substr( $d, $p + 1 ) ) ) {
					$vuote++;
				}
			}
		}
	}
	$blocchi = preg_match_all( '~<style\b[^>]*>(.*?)</style>~s', $html, $sb ) ? $sb[1] : [];
	$visti   = [];
	$byte    = 0;
	$dup     = 0;
	foreach ( $blocchi as $b ) {
		$vuote += preg_match_all( '/[{;]\s*[-a-zA-Z]+\s*:\s*(?=[;}])/', $b );
		$l      = strlen( $b );
		$byte  += $l;
		$h      = md5( $b );
		if ( isset( $visti[ $h ] ) ) {
			$dup += $l;
		} else {
			$visti[ $h ] = true;
		}
	}
	$m['decl_vuote']   = $vuote;
	$m['pxpx']         = preg_match_all( '/\dpxpx/', $html );
	$m['css_blocchi']  = count( $blocchi );
	$m['css_byte']     = $byte;
	$m['css_dup_byte'] = $dup;
	return $m;
}

/**
 * Gli id dei template che i settings di un nodo incorporano: gli stessi archi di
 * olo_cens_riferimenti() in censimento.php (template_id della templateembed, widget_id,
 * *_template_id come widget_template_id e loop_template_id, panel_templates del megamenu,
 * shortcode [olo_template|olobuild_template|mosaic_template id=…] nei testi).
 */
function olo_golden_riferimenti( $v, $chiave, &$ids ) {
	if ( is_array( $v ) ) {
		if ( 'panel_templates' === $chiave ) {
			foreach ( $v as $x ) {
				if ( is_numeric( $x ) && (int) $x > 0 ) {
					$ids[] = (int) $x;
				}
			}
			return;
		}
		foreach ( $v as $k => $x ) {
			olo_golden_riferimenti( $x, (string) $k, $ids );
		}
		return;
	}
	if ( ( 'template_id' === $chiave || 'widget_id' === $chiave || preg_match( '/_template_id$/', $chiave ) ) && is_numeric( $v ) && (int) $v > 0 ) {
		$ids[] = (int) $v;
		return;
	}
	if ( is_string( $v ) && false !== strpos( $v, '_template' ) && preg_match_all( '/\[(?:olo_template|olobuild_template|mosaic_template)\b[^\]]*?\bid\s*=\s*["\']?(\d+)/', $v, $m ) ) {
		foreach ( $m[1] as $id ) {
			$ids[] = (int) $id;
		}
	}
}

/**
 * Perché un template può cambiare HTML senza che cambi niente: dipende dall'ora o dal giorno.
 * Segue anche ciò che il template rende dentro di sé: i template incorporati
 * (olo_golden_riferimenti, caricati con get_template, una volta sola: niente cicli) e i
 * widget globali (global_id: il renderer fonde tile_data nel nodo). Le note si uniscono.
 */
function olo_golden_volatili( $nodi, $id_radice = 0 ) {
	global $wpdb;
	$tempo  = [ 'hiddenpop', 'popup', 'postmeta', 'presencegrid', 'queryloop' ];
	$out    = [];
	$alberi = [ $nodi ]; // per le ricerche sul JSON: la radice, i template incorporati, i widget globali
	$visti  = $id_radice ? [ 'db-' . (int) $id_radice => true ] : [];
	$db     = new Olobuild_Database();
	$gw     = Olobuild_Database::table( 'global_widgets' );
	$visita = function ( $nodi ) use ( &$visita, &$out, &$alberi, &$visti, $tempo, $db, $gw, $wpdb ) {
		if ( ! is_array( $nodi ) ) {
			return;
		}
		foreach ( $nodi as $n ) {
			if ( ! is_array( $n ) ) {
				continue;
			}
			if ( ! empty( $n['global_id'] ) && is_numeric( $n['global_id'] ) && ! isset( $visti[ 'gw-' . (int) $n['global_id'] ] ) ) {
				$visti[ 'gw-' . (int) $n['global_id'] ] = true;
				$zitto = $wpdb->suppress_errors( true );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- banco di sola lettura, tabella del plugin
				$dati = $wpdb->get_var( $wpdb->prepare( "SELECT tile_data FROM {$gw} WHERE id = %d", (int) $n['global_id'] ) );
				$wpdb->suppress_errors( $zitto );
				$risolto = is_string( $dati ) ? json_decode( $dati, true ) : null;
				if ( is_array( $risolto ) ) {
					$n        = array_merge( $n, $risolto );
					$alberi[] = $risolto;
				}
			}
			$t = isset( $n['type'] ) ? (string) $n['type'] : '';
			if ( in_array( $t, $tempo, true ) ) {
				$out[ 'tempo:' . $t ] = true;
			}
			$s  = ( isset( $n['settings'] ) && is_array( $n['settings'] ) ) ? $n['settings'] : [];
			$a  = ( isset( $n['advanced'] ) && is_array( $n['advanced'] ) ) ? $n['advanced'] : [];
			$ct = isset( $s['cond_type'] ) ? (string) $s['cond_type'] : '';
			if ( in_array( $ct, [ 'date_after', 'date_before', 'day_of_week', 'time_range' ], true ) ) {
				$out[ 'condizione:' . $ct ] = true;
			}
			// La seconda condizione conta solo in OR (check_conditions_result del renderer).
			$ct2 = ( '' !== $ct && isset( $s['cond_logic'] ) && 'or' === $s['cond_logic'] && isset( $s['cond_2_type'] ) ) ? (string) $s['cond_2_type'] : '';
			if ( in_array( $ct2, [ 'date_after', 'date_before', 'day_of_week', 'time_range' ], true ) ) {
				$out[ 'condizione:' . $ct2 ] = true;
			}
			if ( ! empty( $a['cond_show_from'] ) || ! empty( $a['cond_show_until'] ) ) {
				$out['condizione:intervallo'] = true;
			}
			$ids = [];
			olo_golden_riferimenti( $s, '', $ids );
			foreach ( $ids as $id ) {
				if ( isset( $visti[ 'db-' . $id ] ) ) {
					continue;
				}
				$visti[ 'db-' . $id ] = true;
				$figlio = $db->get_template( $id );
				if ( is_array( $figlio ) && ! empty( $figlio['content'] ) && is_array( $figlio['content'] ) ) {
					$alberi[] = $figlio['content'];
					$visita( $figlio['content'] );
				}
			}
			if ( ! empty( $n['children'] ) ) {
				$visita( $n['children'] );
			}
		}
	};
	$visita( $nodi );
	foreach ( $alberi as $albero ) {
		$json = (string) wp_json_encode( $albero );
		if ( preg_match( '/"source"\s*:\s*"datetime"/', $json ) ) {
			$out['dinamico:datetime'] = true;
		}
		// Tag dinamici nei testi: {{current_*}} (Olobuild_Tile_Utils::process_dynamic_tags) e
		// {current_year}, {current_date[:formato]}, {post_count} (Olobuild_Dynamic_Content::
		// resolve_tokens, applicato all'HTML di ogni tile dal renderer).
		if ( preg_match_all( '/\{\{?(current_(?:date|time|year)|post_count)(?::[^}]*)?\}\}?/', $json, $tag ) ) {
			foreach ( array_unique( $tag[1] ) as $x ) {
				$out[ 'tag:' . $x ] = true;
			}
		}
	}
	return implode( ',', array_keys( $out ) );
}

// ─── il processo figlio: una chiave ──────────────────────────────────────────

function olo_golden_rendi_una( $chiave, $out, $o ) {
	if ( ! preg_match( '/^[A-Za-z0-9_.-]+$/', $chiave ) ) {
		WP_CLI::error( "Chiave non valida: $chiave" ); // diventa un nome di file
	}
	if ( '' !== $o['mem'] ) {
		@ini_set( 'memory_limit', $o['mem'] ); // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.PHP.IniSet
	}
	if ( function_exists( 'set_time_limit' ) ) {
		@set_time_limit( 0 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
	}
	olo_golden_cartelle( $out );

	// I sali PRIMA della finestra: se mancano le costanti, wp_salt() li genera (e li salva)
	// con la casualità vera, non con quella fissa dello stub.
	foreach ( [ 'auth', 'secure_auth', 'logged_in', 'nonce' ] as $schema ) {
		wp_salt( $schema );
	}
	olo_golden_guardie();

	$riga = array_fill_keys( olo_golden_colonne(), '' );
	$riga['chiave'] = $chiave;
	$nodi = null;
	$id   = 0;

	if ( preg_match( '/^db-(\d+)$/', $chiave, $mm ) ) {
		$id            = (int) $mm[1];
		$riga['fonte'] = 'db';
		$tpl           = ( new Olobuild_Database() )->get_template( $id );
		if ( is_array( $tpl ) ) {
			$riga['tipo'] = isset( $tpl['type'] ) ? (string) $tpl['type'] : '';
			$nodi         = isset( $tpl['content'] ) ? $tpl['content'] : null;
		}
	} else {
		$pagine        = olo_golden_pagine_temi();
		$riga['fonte'] = 'tema';
		if ( ! isset( $pagine[ $chiave ] ) ) {
			$riga['stato']  = 'errore';
			$riga['errore'] = 'chiave sconosciuta';
		} else {
			$p    = $pagine[ $chiave ];
			$json = json_decode( (string) file_get_contents( $p['file'] ), true );
			if ( ! is_array( $json ) ) {
				$riga['stato']  = 'errore';
				$riga['errore'] = 'JSON illeggibile: ' . basename( $p['file'] );
			} else {
				// Due formati: l'albero nudo (207 pagine) oppure { settings, content }.
				$ha_content = isset( $json['content'] ) && is_array( $json['content'] );
				$nodi       = $ha_content ? $json['content'] : $json;
				$impost     = ( $ha_content && isset( $json['settings'] ) && is_array( $json['settings'] ) ) ? $json['settings'] : [];
				// Id finto, fisso per chiave: compare nell'HTML (olo-template-<id>).
				$id           = 900000 + ( abs( crc32( $chiave ) ) % 100000 );
				$riga['tipo'] = $p['tipo'];
				wp_cache_set(
					'olo_template_' . $id,
					[
						'id'         => $id,
						'title'      => $p['titolo'],
						'type'       => $p['tipo'],
						'content'    => $nodi,
						'settings'   => $impost,
						'thumbnail'  => '',
						'status'     => 'published',
						'author_id'  => 0,
						'created_at' => '2026-01-01 00:00:00',
						'updated_at' => '2026-01-01 00:00:00',
					],
					'olo'
				);
			}
		}
	}
	$riga['id']       = $id;
	$riga['volatili'] = olo_golden_volatili( $nodi, $id );

	$html = '';
	if ( '' === $riga['stato'] ) {
		$prima_css = array_values( (array) wp_styles()->queue );
		$prima_js  = array_values( (array) wp_scripts()->queue );
		$GLOBALS['olo_golden_avvisi'] = [];
		$livello = ob_get_level();

		// ── finestra deterministica ──
		mt_srand( crc32( $chiave ) );
		$GLOBALS['olo_golden_seme']  = $chiave;
		$GLOBALS['olo_golden_conta'] = [];
		set_error_handler( 'olo_golden_avviso' );
		$GLOBALS['olo_golden_det'] = true;
		try {
			$html = (string) ( new Olobuild_Frontend_Renderer() )->render_shortcode( [ 'id' => $id ] );
		} catch ( Throwable $e ) {
			$html           = '';
			$riga['stato']  = 'errore';
			$riga['errore'] = get_class( $e ) . ': ' . $e->getMessage() . ' @ ' . basename( $e->getFile() ) . ':' . $e->getLine();
		}
		$GLOBALS['olo_golden_det'] = false;
		restore_error_handler();
		while ( ob_get_level() > $livello ) {
			ob_end_clean();
		}
		// ── fine finestra ──

		$accodati = [];
		foreach ( array_diff( (array) wp_styles()->queue, $prima_css ) as $h ) {
			$accodati[] = 'css:' . $h;
		}
		foreach ( array_diff( (array) wp_scripts()->queue, $prima_js ) as $h ) {
			$accodati[] = 'js:' . $h;
		}
		$riga['accodati'] = implode( ',', $accodati );
		$riga['rand']     = array_sum( $GLOBALS['olo_golden_conta'] );
		if ( '' === $riga['stato'] ) {
			$vuoto         = 0 === strpos( ltrim( $html ), '<!-- Olobuilder:' ) && false === strpos( $html, '<div' );
			$riga['stato'] = $vuoto ? 'vuoto' : 'ok';
			if ( $vuoto ) {
				$riga['errore'] = trim( $html );
			}
		}
	}

	$norm = olo_golden_normalizza( $html );
	foreach ( olo_golden_misure( $norm ) as $k => $v ) {
		$riga[ $k ] = $v;
	}
	$riga['md5']        = md5( $norm );
	$riga['md5_grezzo'] = md5( $html );
	$riga['scritture']  = count( $GLOBALS['olo_golden_bloccati']['scritture'] );
	$riga['rete']       = count( $GLOBALS['olo_golden_bloccati']['rete'] );
	$riga['avvisi']     = isset( $GLOBALS['olo_golden_avvisi'] ) ? count( $GLOBALS['olo_golden_avvisi'] ) : 0;

	file_put_contents( $out . '/' . $chiave . '.html', $norm );
	if ( $norm !== $html ) {
		file_put_contents( $out . '/_grezzo/' . $chiave . '.html', $html );
	}
	$note = [];
	foreach ( isset( $GLOBALS['olo_golden_avvisi'] ) ? $GLOBALS['olo_golden_avvisi'] : [] as $a ) {
		$note[] = 'avviso: ' . $a;
	}
	foreach ( $GLOBALS['olo_golden_bloccati']['scritture'] as $q ) {
		$note[] = 'scrittura bloccata: ' . $q;
	}
	foreach ( array_count_values( $GLOBALS['olo_golden_bloccati']['rete'] ) as $h => $n ) {
		$note[] = 'rete bloccata: ' . $h . ' ×' . $n;
	}
	if ( $GLOBALS['olo_golden_bloccati']['rand_sql'] ) {
		$note[] = 'ORDER BY RAND() fissato: ×' . $GLOBALS['olo_golden_bloccati']['rand_sql'];
	}
	if ( '' !== $riga['errore'] && 'vuoto' !== $riga['stato'] ) {
		$note[] = 'errore: ' . $riga['errore'];
	}
	if ( $note ) {
		file_put_contents( $out . '/_avvisi/' . $chiave . '.txt', implode( "\n", $note ) . "\n" );
	}
	file_put_contents( $out . '/_righe/' . $chiave . '.tsv', implode( "\t", array_map( 'olo_golden_tsv', $riga ) ) . "\n" );
	WP_CLI::line( $riga['stato'] . ' ' . $chiave );
}

// ─── il processo principale ──────────────────────────────────────────────────

/** Nome → dimensione@mtime dei file in uploads/olobuild-cache (senza creare cartelle). */
function olo_golden_impronta_cache() {
	$up   = wp_upload_dir( null, false );
	$dir  = rtrim( (string) $up['basedir'], '/' ) . '/olobuild-cache';
	$voci = [];
	if ( is_dir( $dir ) ) {
		foreach ( (array) glob( $dir . '/*' ) as $f ) {
			$voci[ basename( $f ) ] = @filesize( $f ) . '@' . @filemtime( $f ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
		}
	}
	ksort( $voci );
	return $voci;
}

function olo_golden_percentile( $valori, $p ) {
	if ( ! $valori ) {
		return 0;
	}
	sort( $valori, SORT_NUMERIC );
	$i = (int) ceil( $p * count( $valori ) ) - 1;
	return $valori[ max( 0, min( count( $valori ) - 1, $i ) ) ];
}

function olo_golden_statistiche( $righe ) {
	$gruppi = [ 'tutti' => $righe, 'db' => [], 'tema' => [] ];
	foreach ( $righe as $r ) {
		if ( isset( $gruppi[ $r['fonte'] ] ) ) {
			$gruppi[ $r['fonte'] ][] = $r;
		}
	}
	$kb    = function ( $b ) {
		return number_format( $b / 1024, 1, ',', '.' ) . ' KB';
	};
	$testo = [];
	foreach ( $gruppi as $nome => $rr ) {
		if ( ! $rr ) {
			continue;
		}
		$conta = [ 'ok' => 0, 'vuoto' => 0, 'errore' => 0, 'fatale' => 0 ];
		$somma = array_fill_keys( [ 'byte', 'wrapper', 'wrapper_style_vuoto', 'style_vuoti', 'decl_vuote', 'pxpx', 'css_byte', 'css_dup_byte', 'scritture', 'rete', 'avvisi' ], 0 );
		$css   = [];
		$vol   = 0;
		foreach ( $rr as $r ) {
			$conta[ $r['stato'] ] = ( isset( $conta[ $r['stato'] ] ) ? $conta[ $r['stato'] ] : 0 ) + 1;
			foreach ( $somma as $k => $v ) {
				$somma[ $k ] = $v + (int) $r[ $k ];
			}
			$css[] = (int) $r['css_byte'];
			$vol  += '' !== $r['volatili'] ? 1 : 0;
		}
		$testo[] = sprintf( '[%s] %d chiavi: %d ok, %d vuote, %d errori, %d fatali · %d con elementi volatili', $nome, count( $rr ), $conta['ok'], $conta['vuoto'], $conta['errore'], $conta['fatale'], $vol );
		$testo[] = sprintf( '  wrapper di tile %d, con style="" vuoto %d · style="" in tutto %d · dichiarazioni vuote %d · pxpx %d', $somma['wrapper'], $somma['wrapper_style_vuoto'], $somma['style_vuoti'], $somma['decl_vuote'], $somma['pxpx'] );
		$testo[] = sprintf(
			'  CSS nel body per pagina: mediana %s, p90 %s, max %s · blocchi <style> duplicati %s%% dei byte',
			$kb( olo_golden_percentile( $css, 0.5 ) ),
			$kb( olo_golden_percentile( $css, 0.9 ) ),
			$kb( $css ? max( $css ) : 0 ),
			$somma['css_byte'] ? number_format( 100 * $somma['css_dup_byte'] / $somma['css_byte'], 1, ',', '.' ) : '0'
		);
		$testo[] = sprintf( '  scritture bloccate %d · richieste di rete bloccate %d · avvisi PHP %d', $somma['scritture'], $somma['rete'], $somma['avvisi'] );
	}
	$problemi = [];
	foreach ( $righe as $r ) {
		// Una pagina di tema vuota non è legittima (nessun JSON è vuoto): la riga finta nel
		// gruppo di cache 'olo' non è stata letta. confronta.sh la conta come non resa.
		if ( 'errore' === $r['stato'] || 'fatale' === $r['stato'] || ( 'vuoto' === $r['stato'] && 'tema' === $r['fonte'] ) ) {
			$problemi[] = '  ' . $r['stato'] . ' ' . $r['chiave'] . ': ' . $r['errore'];
		}
	}
	if ( $problemi ) {
		$testo[] = 'Errori, fatali e pagine di tema vuote:';
		$testo   = array_merge( $testo, $problemi );
	}
	return $testo;
}

/**
 * @param array $vero i valori letti PRIMA delle guardie, che le guardie filtrano
 *                    (object_cache_esterno, impronta_olobuild_performance): vincono.
 */
function olo_golden_ambiente( $vero = [] ) {
	global $wpdb;
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$tutti  = get_plugins();
	$attivi = [];
	foreach ( (array) get_option( 'active_plugins', [] ) as $p ) {
		$attivi[] = $p . '@' . ( isset( $tutti[ $p ]['Version'] ) ? $tutti[ $p ]['Version'] : '?' );
	}
	$t       = Olobuild_Database::table( 'templates' );
	$gw      = Olobuild_Database::table( 'global_widgets' );
	$zitto   = $wpdb->suppress_errors( true );
	// phpcs:disable WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- banco di sola lettura, tabelle del plugin
	$tpl     = $wpdb->get_row( "SELECT COUNT(*) AS n, MAX(updated_at) AS u FROM {$t}", ARRAY_A );
	$widgets = $wpdb->get_col( "SELECT MD5(tile_data) FROM {$gw} ORDER BY id ASC" );
	// phpcs:enable
	$wpdb->suppress_errors( $zitto );
	$tema = wp_get_theme();
	$css  = class_exists( 'Olobuild_Style_System' ) ? Olobuild_Style_System::instance()->generate_css() : '';
	$imp  = function ( $nome ) {
		return md5( maybe_serialize( get_option( $nome, null ) ) );
	};
	$amb = [
		'data_utc'                        => gmdate( 'Y-m-d H:i:s' ),
		'olobuild'                        => defined( 'OLOBUILD_VERSION' ) ? OLOBUILD_VERSION : '?',
		'wordpress'                       => get_bloginfo( 'version' ),
		'php'                             => PHP_VERSION,
		'home'                            => home_url( '/' ),
		'tema'                            => $tema->get_stylesheet() . '@' . $tema->get( 'Version' ),
		'object_cache_esterno'            => wp_using_ext_object_cache() ? 'si' : 'no',
		'plugin_attivi'                   => implode( ', ', $attivi ),
		'template_db'                     => ( $tpl ? $tpl['n'] : '?' ) . ' (ultimo updated_at ' . ( $tpl ? $tpl['u'] : '?' ) . ')',
		'widget_globali'                  => count( (array) $widgets ) . ' (impronta ' . md5( implode( ',', (array) $widgets ) ) . ')',
		'impronta_olobuild_styles'        => $imp( 'olobuild_styles' ),
		'impronta_olobuild_global_colors' => $imp( 'olobuild_global_colors' ),
		'impronta_olobuild_performance'   => $imp( 'olobuild_performance' ),
		'impronta_style_system'           => md5( (string) $css ),
	];
	return array_merge( $amb, array_intersect_key( (array) $vero, $amb ) );
}

function olo_golden_pilota( $o ) {
	global $wpdb;
	$out = $o['out'];
	if ( file_exists( $out . '/_summary.tsv' ) ) {
		WP_CLI::error( "$out contiene già uno snapshot: le fasi non si sovrascrivono, scegli un altro nome." );
	}
	olo_golden_cartelle( $out );

	// Anche il pilota ha le guardie dei figli: generate_css() (_style-system.css e
	// _ambiente.txt) passa da Olobuild_Font_Host, che senza il suo transient chiamerebbe Google,
	// scriverebbe i woff2 in uploads/olo-fonts e salverebbe un transient. Con le guardie la rete
	// è spenta, le query di scrittura bloccate e i transient in memoria: lo Style System esce
	// uguale a ogni giro (senza le @font-face self-hosted). Prima, i due valori che le guardie
	// filtrano, per _ambiente.txt.
	$vero = [
		'object_cache_esterno'          => wp_using_ext_object_cache() ? 'si' : 'no',
		'impronta_olobuild_performance' => md5( maybe_serialize( get_option( 'olobuild_performance', null ) ) ),
	];
	olo_golden_guardie();

	$chiavi = olo_golden_chiavi( $o['src'] );
	if ( '' !== $o['solo'] ) {
		$vuoi   = array_filter( array_map( 'trim', explode( ',', $o['solo'] ) ) );
		$manca  = array_diff( $vuoi, $chiavi );
		$chiavi = array_values( array_intersect( $chiavi, $vuoi ) );
		if ( $manca ) {
			WP_CLI::warning( 'Chiavi sconosciute: ' . implode( ', ', $manca ) );
		}
	}
	if ( ! $chiavi ) {
		WP_CLI::error( 'Nessuna chiave da rendere.' );
	}
	$giro = 'inverso' === $o['ordine'] ? array_reverse( $chiavi ) : $chiavi;
	$mem  = '' !== $o['mem'] ? $o['mem'] : (string) ini_get( 'memory_limit' );
	// WP_CLI::runcommand rilancia PHP SENZA i -d del padre: con i 128M predefiniti i figli
	// morivano già nel caricamento di WordPress, prima di poter alzare il limite da sé. Il
	// limite passa per un ini nella cartella di output, aggiunto (':' iniziale = anche la
	// cartella predefinita) a PHP_INI_SCAN_DIR, che i figli ereditano dall'ambiente.
	if ( preg_match( '/^\d+[KMG]?$/i', $mem ) ) {
		$ini_dir = $out . '/_ini';
		if ( ! is_dir( $ini_dir ) ) {
			mkdir( $ini_dir, 0755, true );
		}
		file_put_contents( $ini_dir . '/olo-golden.ini', 'memory_limit = ' . $mem . "\n" );
		$scan = (string) getenv( 'PHP_INI_SCAN_DIR' );
		putenv( 'PHP_INI_SCAN_DIR=' . $scan . ':' . $ini_dir );
	}

	$cache_prima = olo_golden_impronta_cache();
	$inizio      = microtime( true );
	$n           = count( $giro );
	WP_CLI::log( sprintf( 'Rendo %d chiavi (src=%s%s), un processo per chiave → %s', $n, $o['src'], 'inverso' === $o['ordine'] ? ', ordine inverso' : '', $out ) );
	foreach ( $giro as $i => $k ) {
		// I resti di un giro interrotto non devono coprire un figlio che muore ora.
		foreach ( [ '/_righe/' . $k . '.tsv', '/_avvisi/' . $k . '.txt', '/_grezzo/' . $k . '.html' ] as $resto ) {
			if ( file_exists( $out . $resto ) ) {
				unlink( $out . $resto );
			}
		}
		$cmd = 'eval-file ' . escapeshellarg( __FILE__ ) . ' ' . escapeshellarg( 'chiave=' . $k ) . ' ' . escapeshellarg( 'out=' . $out ) . ' ' . escapeshellarg( 'mem=' . $mem );
		$r   = WP_CLI::runcommand( $cmd, [ 'launch' => true, 'exit_error' => false, 'return' => 'all' ] );
		if ( ! file_exists( $out . '/_righe/' . $k . '.tsv' ) ) {
			// Il figlio è morto prima di scrivere la sua riga (fatale PHP, memoria esaurita…).
			$err  = trim( is_object( $r ) ? (string) $r->stderr . ' ' . (string) $r->stdout : '' );
			$riga = array_fill_keys( olo_golden_colonne(), '' );
			$riga['chiave'] = $k;
			$riga['fonte']  = 0 === strpos( $k, 'db-' ) ? 'db' : 'tema';
			$riga['stato']  = 'fatale';
			$riga['errore'] = substr( preg_replace( '/\s+/', ' ', $err ), -400 );
			file_put_contents( $out . '/_righe/' . $k . '.tsv', implode( "\t", array_map( 'olo_golden_tsv', $riga ) ) . "\n" );
			file_put_contents( $out . '/_avvisi/' . $k . '.txt', 'fatale (codice ' . ( is_object( $r ) ? $r->return_code : '?' ) . "):\n" . $err . "\n" );
		}
		if ( 0 === ( $i + 1 ) % 20 || $i + 1 === $n ) {
			WP_CLI::log( sprintf( '  %d/%d · %ds', $i + 1, $n, (int) ( microtime( true ) - $inizio ) ) );
		}
	}

	// _summary.tsv e _md5.txt nell'ordine canonico (non in quello del giro): due fasi si
	// confrontano anche riga per riga.
	$colonne = olo_golden_colonne();
	$righe   = [];
	$tsv     = [ implode( "\t", $colonne ) ];
	$md5     = [];
	foreach ( $chiavi as $k ) {
		$linea = rtrim( (string) file_get_contents( $out . '/_righe/' . $k . '.tsv' ), "\n" );
		$tsv[] = $linea;
		$campi = array_pad( explode( "\t", $linea ), count( $colonne ), '' );
		$r     = array_combine( $colonne, array_slice( $campi, 0, count( $colonne ) ) );
		$righe[] = $r;
		$md5[]   = $r['md5'] . '  ' . $k;
	}
	file_put_contents( $out . '/_summary.tsv', implode( "\n", $tsv ) . "\n" );
	file_put_contents( $out . '/_md5.txt', implode( "\n", $md5 ) . "\n" );
	// Snapshot parziale (solo=…): confronta.sh confronta solo queste chiavi.
	if ( '' !== $o['solo'] ) {
		file_put_contents( $out . '/_solo.txt', implode( "\n", $chiavi ) . "\n" );
	} elseif ( file_exists( $out . '/_solo.txt' ) ) {
		unlink( $out . '/_solo.txt' ); // resto di un giro parziale interrotto
	}

	// V4: lo Style System, una dichiarazione per riga come lo genera generate_css().
	if ( class_exists( 'Olobuild_Style_System' ) ) {
		// A capo come li legge il browser (CR LF e CR → LF): vedi la regola «a-capo» di normalizza.json.
		file_put_contents( $out . '/_style-system.css', preg_replace( '/\r\n?/', "\n", (string) Olobuild_Style_System::instance()->generate_css() ) );
	}

	// Impronta dei dati: se il diff trova un template cambiato, dice se sono cambiati i dati.
	$dati = [ "chiave\tfonte\tid\ttipo\tstato\tupdated_at\timpronta" ];
	$t    = Olobuild_Database::table( 'templates' );
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- banco di sola lettura, tabella del plugin
	foreach ( (array) $wpdb->get_results( "SELECT id, type, status, updated_at, MD5(CONCAT(content, '|', settings)) AS impronta FROM {$t} ORDER BY id ASC", ARRAY_A ) as $r ) {
		$dati[] = implode( "\t", array_map( 'olo_golden_tsv', [ 'db-' . $r['id'], 'db', $r['id'], $r['type'], $r['status'], $r['updated_at'], $r['impronta'] ] ) );
	}
	foreach ( olo_golden_pagine_temi() as $k => $p ) {
		$dati[] = implode( "\t", array_map( 'olo_golden_tsv', [ $k, 'tema', '', $p['tipo'], 'file', '', md5_file( $p['file'] ) ] ) );
	}
	file_put_contents( $out . '/_dati.tsv', implode( "\n", $dati ) . "\n" );

	// Permalink dei post collegati (meta _olo_template_id): gli indirizzi per golden-shots.mjs.
	$urls  = [ "chiave\tpost_id\turl" ];
	$visti = [];
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.SlowDBQuery -- banco di sola lettura
	$link = $wpdb->get_results( "SELECT pm.meta_value AS tpl, p.ID AS post_id FROM {$wpdb->postmeta} pm JOIN {$wpdb->posts} p ON p.ID = pm.post_id WHERE pm.meta_key = '_olo_template_id' AND p.post_status = 'publish' ORDER BY p.ID ASC", ARRAY_A );
	foreach ( (array) $link as $r ) {
		$k = 'db-' . (int) $r['tpl'];
		if ( isset( $visti[ $k ] ) || ! (int) $r['tpl'] ) {
			continue;
		}
		$visti[ $k ] = true;
		$urls[]      = $k . "\t" . (int) $r['post_id'] . "\t" . get_permalink( (int) $r['post_id'] );
	}
	file_put_contents( $out . '/_urls.tsv', implode( "\n", $urls ) . "\n" );

	$amb = [];
	foreach ( olo_golden_ambiente( $vero ) as $k => $v ) {
		$amb[] = $k . ': ' . $v;
	}
	file_put_contents( $out . '/_ambiente.txt', implode( "\n", $amb ) . "\n" );

	$stat        = olo_golden_statistiche( $righe );
	$bloccati    = $GLOBALS['olo_golden_bloccati'];
	$reti        = array_count_values( $bloccati['rete'] );
	$stat[]      = sprintf( 'pilota: scritture bloccate %d · richieste di rete bloccate %d%s', count( $bloccati['scritture'] ), count( $bloccati['rete'] ), $reti ? ' (' . implode( ', ', array_keys( $reti ) ) . ')' : '' );
	if ( $bloccati['scritture'] || $bloccati['rete'] ) {
		$note = [];
		foreach ( $bloccati['scritture'] as $q ) {
			$note[] = 'scrittura bloccata: ' . $q;
		}
		foreach ( $reti as $h => $nr ) {
			$note[] = 'rete bloccata: ' . $h . ' ×' . $nr;
		}
		file_put_contents( $out . '/_avvisi/_pilota.txt', implode( "\n", $note ) . "\n" );
	} elseif ( file_exists( $out . '/_avvisi/_pilota.txt' ) ) {
		unlink( $out . '/_avvisi/_pilota.txt' ); // resto di un giro interrotto
	}
	if ( $bloccati['scritture'] ) {
		WP_CLI::warning( 'Il pilota ha tentato ' . count( $bloccati['scritture'] ) . ' scritture (bloccate): dettaglio in _avvisi/_pilota.txt.' );
	}
	$cache_dopo  = olo_golden_impronta_cache();
	$cambiati    = array_keys( array_diff_assoc( $cache_dopo, $cache_prima ) + array_diff_key( $cache_prima, $cache_dopo ) );
	$stat[]      = 'uploads/olobuild-cache: ' . count( $cache_prima ) . ' file prima, ' . count( $cache_dopo ) . ' dopo, ' . count( $cambiati ) . ' nuovi o cambiati' . ( $cambiati ? ' (' . implode( ', ', array_slice( $cambiati, 0, 10 ) ) . ')' : '' );
	$stat[]      = sprintf( 'Tempo: %ds · %d template con URL in _urls.tsv', (int) ( microtime( true ) - $inizio ), count( $urls ) - 1 );
	file_put_contents( $out . '/_statistiche.txt', implode( "\n", $stat ) . "\n" );
	foreach ( $stat as $l ) {
		WP_CLI::log( $l );
	}
	if ( $cambiati ) {
		WP_CLI::warning( 'Durante il banco è cambiato qualcosa in uploads/olobuild-cache: il render non era di sola lettura.' );
	}
	WP_CLI::success( "Snapshot in $out" );
}

// ─── ingresso ────────────────────────────────────────────────────────────────

function olo_golden_main( $argv ) {
	$o = olo_golden_opzioni( $argv );
	if ( ! defined( 'OLO_GOLDEN_STUB' ) ) {
		WP_CLI::error( 'Manca lo stub deterministico: aggiungi --require=<cartella>/golden-stub.php. Senza, wp_rand() darebbe uid casuali e il confronto non varrebbe niente.' );
	}
	if ( defined( 'OLO_GOLDEN_STUB_TARDI' ) ) {
		WP_CLI::error( 'golden-stub.php è arrivato dopo WordPress: va passato con --require, non incluso a mano.' );
	}
	if ( ! class_exists( 'Olobuild_Frontend_Renderer' ) || ! class_exists( 'Olobuild_Database' ) || ! defined( 'OLOBUILD_PATH' ) ) {
		WP_CLI::error( 'Olobuild non è attivo su questo sito.' );
	}
	if ( ! in_array( $o['src'], [ 'db', 'themes', 'all' ], true ) ) {
		WP_CLI::error( 'src deve essere db, themes o all.' );
	}
	if ( $o['elenco'] ) {
		foreach ( olo_golden_chiavi( $o['src'] ) as $k ) {
			WP_CLI::line( $k );
		}
		return;
	}
	if ( '' === $o['out'] ) {
		WP_CLI::error( 'Manca out=<cartella dello snapshot>.' );
	}
	if ( '' !== $o['chiave'] ) {
		olo_golden_rendi_una( $o['chiave'], $o['out'], $o );
		return;
	}
	olo_golden_pilota( $o );
}

olo_golden_main( isset( $args ) && is_array( $args ) ? $args : [] );
