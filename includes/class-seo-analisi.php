<?php
/**
 * L'analisi SEO della pagina (novembre 2026).
 *
 * Legge la pagina COME LA RENDE IL PHP, cioè come la vedono Google e i motori delle IA: il testo delle tile, i
 * titoli H1–H6, le immagini, i link. Prima l'analisi del riquadro di WordPress leggeva il contenuto dell'editor
 * classico, vuoto in ogni pagina fatta col builder e nell'editor a blocchi, e il builder non ne aveva nessuna.
 *
 * Una sola analisi per i due posti dove si scrive la SEO: il builder (Impostazioni pagina › SEO › Base) le manda
 * le tile non ancora salvate, il riquadro dell'editor di WordPress le chiede la pagina salvata.
 *
 * Controlli moderni: niente densità della parola chiave né soglia delle 300 parole (Google non li usa). Si guarda
 * ciò che conta: indicizzazione, titolo e descrizione, un solo H1, titoli senza salti, testo alternativo, link
 * interni, la parola chiave nei punti che contano e non già usata da un'altra pagina, dati strutturati completi.
 *
 * Testata e piè di pagina restano fuori: si analizza il contenuto che si scrive in questa pagina.
 *
 * @package Olobuild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Olobuild_Seo_Analisi {

	/** Sotto questo numero di parole la pagina dice poco a motori e IA. */
	const PAROLE_MINIME = 150;

	/** La parola chiave va trovata fra le prime parole del testo. */
	const PAROLE_INIZIO = 100;

	public static function init() {
		add_action( 'rest_api_init', [ __CLASS__, 'rotte' ] );
	}

	public static function rotte() {
		register_rest_route( 'olobuild/v1', '/seo/analisi', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'rest_analisi' ],
			'permission_callback' => function ( $request ) {
				$id = absint( $request->get_param( 'post_id' ) );
				return $id && current_user_can( 'edit_post', $id );
			},
		] );
	}

	/**
	 * POST olobuild/v1/seo/analisi
	 *   post_id        il contenuto a cui appartiene la SEO (obbligatorio)
	 *   tiles          le tile da analizzare (dal builder, anche non salvate); senza, quelle della pagina salvata
	 *   page_settings  le impostazioni di pagina del template
	 *   seo            i campi SEO non ancora salvati (title, description, focus_keyword, noindex, canonical,
	 *                  og_image, schema_type, schema_data); senza, quelli salvati
	 */
	public static function rest_analisi( $request ) {
		$post_id = absint( $request->get_param( 'post_id' ) );
		$post    = get_post( $post_id );
		if ( ! $post ) {
			return new WP_Error( 'olo_seo_no_post', __( 'Contenuto non trovato', 'olobuild' ), [ 'status' => 404 ] );
		}
		if ( ! class_exists( 'DOMDocument' ) ) {
			return new WP_Error( 'olo_seo_no_dom', __( 'Il server non ha l’estensione DOM di PHP: l’analisi non è disponibile.', 'olobuild' ), [ 'status' => 501 ] );
		}

		$tiles         = $request->get_param( 'tiles' );
		$page_settings = $request->get_param( 'page_settings' );
		$seo           = self::campi_seo( $post_id, $request->get_param( 'seo' ) );

		$html = self::html_pagina( $post, is_array( $tiles ) ? $tiles : null, is_array( $page_settings ) ? $page_settings : [] );

		return rest_ensure_response( self::analizza( $post, $html, $seo ) );
	}

	/**
	 * I campi SEO da usare: quelli inviati (non ancora salvati) vincono su quelli salvati.
	 */
	private static function campi_seo( $post_id, $inviati ) {
		$campi = [
			'title'         => (string) get_post_meta( $post_id, '_olo_seo_title', true ),
			'description'   => (string) get_post_meta( $post_id, '_olo_seo_description', true ),
			'focus_keyword' => (string) get_post_meta( $post_id, '_olo_seo_focus_keyword', true ),
			'noindex'       => (bool) get_post_meta( $post_id, '_olo_seo_noindex', true ),
			'canonical'     => (string) get_post_meta( $post_id, '_olo_seo_canonical', true ),
			'og_image'      => (string) get_post_meta( $post_id, '_olo_seo_og_image', true ),
			'schema_type'   => (string) get_post_meta( $post_id, '_olo_seo_schema_type', true ),
			'schema_data'   => get_post_meta( $post_id, '_olo_seo_schema_data', true ),
		];
		if ( ! is_array( $campi['schema_data'] ) ) {
			$campi['schema_data'] = [];
		}
		if ( ! is_array( $inviati ) ) {
			return $campi;
		}
		foreach ( [ 'title', 'focus_keyword', 'canonical', 'og_image', 'schema_type' ] as $k ) {
			if ( array_key_exists( $k, $inviati ) ) {
				$campi[ $k ] = sanitize_text_field( (string) $inviati[ $k ] );
			}
		}
		if ( array_key_exists( 'description', $inviati ) ) {
			$campi['description'] = sanitize_textarea_field( (string) $inviati['description'] );
		}
		if ( array_key_exists( 'noindex', $inviati ) ) {
			$campi['noindex'] = ! empty( $inviati['noindex'] );
		}
		if ( array_key_exists( 'schema_data', $inviati ) && is_array( $inviati['schema_data'] ) && class_exists( 'Olobuild_Seo_Head' ) ) {
			$campi['schema_data'] = Olobuild_Seo_Head::sanitizza_dati_schema( $inviati['schema_data'] )['dati'];
		}
		return $campi;
	}

	/**
	 * L'HTML del contenuto della pagina, reso dal PHP come sul sito.
	 *
	 * @param WP_Post    $post          Il contenuto: dà il contesto alle tile dinamiche (titolo, campi…).
	 * @param array|null $tiles         Le tile del builder; null = quelle salvate della pagina.
	 * @param array      $page_settings Le impostazioni di pagina che arrivano col builder.
	 * @return string
	 */
	public static function html_pagina( $post, $tiles = null, $page_settings = [] ) {
		// Il contesto del contenuto, come sul sito: le tile dinamiche leggono il post corrente.
		$GLOBALS['post'] = $post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- contesto del contenuto analizzato, ripristinato da wp_reset_postdata() sotto
		setup_postdata( $post );

		$html = '';
		if ( null === $tiles ) {
			$template_id = (int) get_post_meta( $post->ID, '_olo_template_id', true );
			if ( $template_id && class_exists( 'Olobuild_Database' ) ) {
				$db  = new Olobuild_Database();
				$tpl = $db->get_template( $template_id );
				if ( $tpl && ! empty( $tpl['content'] ) && is_array( $tpl['content'] ) ) {
					$tiles         = $tpl['content'];
					$page_settings = is_array( $tpl['settings'] ?? null ) ? $tpl['settings'] : [];
				}
			}
		}

		if ( is_array( $tiles ) && $tiles && class_exists( 'Olobuild_Frontend_Renderer' ) ) {
			$renderer = new Olobuild_Frontend_Renderer();
			ob_start();
			$renderer->render_tiles_array( $tiles, $page_settings );
			$html = (string) ob_get_clean();
		} elseif ( null === $tiles ) {
			// Un contenuto senza template di Olobuild: il suo testo, coi filtri di WordPress (blocchi, shortcode).
			$html = (string) apply_filters( 'the_content', $post->post_content ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- filtro core di WordPress
		}

		wp_reset_postdata();
		return $html;
	}

	/**
	 * L'analisi.
	 *
	 * @param WP_Post $post Il contenuto.
	 * @param string  $html L'HTML del suo contenuto.
	 * @param array   $seo  I campi SEO (campi_seo()).
	 * @return array { riepilogo, controlli[], titoli[], dati }
	 */
	public static function analizza( $post, $html, $seo ) {
		$pagina    = self::leggi_html( $html );
		$controlli = [];
		$aggiungi  = function ( $id, $esito, $testo, $aiuto = '' ) use ( &$controlli ) {
			$controlli[] = [ 'id' => $id, 'esito' => $esito, 'testo' => $testo, 'aiuto' => $aiuto ];
		};

		/* ── Indicizzazione ───────────────────────────────────────── */
		if ( '0' === (string) get_option( 'blog_public', '1' ) ) {
			$aggiungi( 'sito-nascosto', 'errore', __( 'Tutto il sito chiede ai motori di non indicizzarlo', 'olobuild' ),
				__( 'In Impostazioni › Lettura di WordPress è attivo «Scoraggia i motori di ricerca dall’indicizzazione di questo sito». Va bene su un sito di prova, su quello vero toglilo.', 'olobuild' ) );
		}
		if ( ! empty( $seo['noindex'] ) ) {
			$aggiungi( 'noindex', 'errore', __( 'La pagina è esclusa dai motori (noindex)', 'olobuild' ),
				__( 'Con noindex Google e gli altri motori non mostrano la pagina nei risultati. Lascialo solo sulle pagine di servizio, come il grazie dopo un modulo: si toglie nella scheda Robots.', 'olobuild' ) );
		}
		if ( ! empty( $seo['canonical'] ) && self::url_normalizzato( $seo['canonical'] ) !== self::url_normalizzato( get_permalink( $post ) ) ) {
			$aggiungi( 'canonical', 'info', __( 'Il canonical indica un’altra pagina come principale', 'olobuild' ),
				sprintf(
					/* translators: %s: l'indirizzo indicato come canonical */
					__( 'Google mostrerà al suo posto %s. Va bene solo se questa pagina è una copia di quella.', 'olobuild' ),
					$seo['canonical']
				) );
		}

		/* ── Titolo e descrizione ─────────────────────────────────── */
		$titolo      = self::titolo_effettivo( $post, $seo['title'] );
		$lung_titolo = self::lunghezza( $titolo );
		if ( $lung_titolo > 60 ) {
			$aggiungi( 'titolo', 'attenzione', sprintf( __( 'Titolo lungo: %d caratteri, Google lo taglia', 'olobuild' ), $lung_titolo ),
				__( 'Nei risultati Google mostra circa 60 caratteri: metti all’inizio le parole che contano.', 'olobuild' ) );
		} elseif ( $lung_titolo < 30 ) {
			$aggiungi( 'titolo', 'attenzione', sprintf( __( 'Titolo corto: %d caratteri', 'olobuild' ), $lung_titolo ),
				__( 'Con 30–60 caratteri il titolo dice di più: aggiungi cosa offri o dove.', 'olobuild' ) );
		} else {
			$aggiungi( 'titolo', 'ok', sprintf( __( 'Titolo di %d caratteri', 'olobuild' ), $lung_titolo ) );
		}
		if ( '' !== trim( $seo['title'] ) ) {
			$doppio = self::altro_post_con_meta( '_olo_seo_title', $seo['title'], $post->ID );
			if ( $doppio ) {
				$aggiungi( 'titolo-doppio', 'attenzione', __( 'Lo stesso titolo SEO è usato anche da un’altra pagina', 'olobuild' ),
					sprintf(
						/* translators: %s: titolo dell'altra pagina */
						__( 'Anche «%s» ha questo titolo: due pagine con lo stesso titolo si fanno concorrenza nei risultati.', 'olobuild' ),
						get_the_title( $doppio )
					) );
			}
		}

		$descrizione = trim( (string) $seo['description'] );
		$lung_desc   = self::lunghezza( $descrizione );
		if ( 0 === $lung_desc ) {
			$aggiungi( 'descrizione', 'attenzione', __( 'Manca la meta description', 'olobuild' ),
				__( 'Senza, Google sceglie da solo un pezzo del testo da mostrare sotto il titolo. Scrivine una di 120–160 caratteri che inviti a cliccare.', 'olobuild' ) );
		} elseif ( $lung_desc > 160 ) {
			$aggiungi( 'descrizione', 'attenzione', sprintf( __( 'Descrizione lunga: %d caratteri, Google la taglia', 'olobuild' ), $lung_desc ) );
		} elseif ( $lung_desc < 70 ) {
			$aggiungi( 'descrizione', 'attenzione', sprintf( __( 'Descrizione corta: %d caratteri', 'olobuild' ), $lung_desc ),
				__( 'Con 120–160 caratteri c’è spazio per dire cosa trova chi clicca.', 'olobuild' ) );
		} else {
			$aggiungi( 'descrizione', 'ok', sprintf( __( 'Descrizione di %d caratteri', 'olobuild' ), $lung_desc ) );
		}

		/* ── Titoli H1–H6 ─────────────────────────────────────────── */
		$titoli = $pagina['titoli'];
		$h1     = array_values( array_filter( $titoli, function ( $t ) { return 1 === $t['livello']; } ) );
		if ( 0 === count( $h1 ) ) {
			// Il tema di Olobuild non stampa il titolo della pagina: lì un H1 mancante manca davvero.
			$tema_nostro = false !== stripos( get_template(), 'olobuild' );
			$aggiungi( 'h1', $tema_nostro ? 'errore' : 'attenzione', __( 'Nessun titolo principale (H1) nelle tile', 'olobuild' ),
				$tema_nostro
					? __( 'Ogni pagina ha bisogno di un titolo H1 che dica di cosa parla: imposta il tag H1 sul titolo più importante, di solito quello in cima.', 'olobuild' )
					: __( 'Se il tema non stampa il titolo della pagina, la pagina resta senza titolo principale: imposta il tag H1 sul titolo più importante delle tile.', 'olobuild' ) );
		} elseif ( count( $h1 ) > 1 ) {
			$aggiungi( 'h1', 'attenzione', sprintf( __( '%d titoli H1: tienine uno solo', 'olobuild' ), count( $h1 ) ),
				__( 'Un solo H1 dice chiaramente di cosa parla la pagina. Gli altri titoli scendono a H2, le loro parti a H3.', 'olobuild' ) );
		} else {
			$aggiungi( 'h1', 'ok', __( 'Un solo titolo H1', 'olobuild' ) );
		}

		$salti = 0;
		$vuoti = 0;
		$prec  = 0;
		foreach ( $titoli as $i => $t ) {
			if ( '' === $t['testo'] ) {
				$titoli[ $i ]['problema'] = 'vuoto';
				$vuoti++;
			} elseif ( $prec && $t['livello'] > $prec + 1 ) {
				$titoli[ $i ]['problema'] = 'salto';
				$salti++;
			}
			$prec = $t['livello'];
		}
		if ( $salti ) {
			$aggiungi( 'gerarchia', 'attenzione', sprintf( _n( '%d titolo salta un livello', '%d titoli saltano un livello', $salti, 'olobuild' ), $salti ),
				__( 'Da un H2 si scende a H3, non a H4: i titoli fanno da indice della pagina per i motori, le IA e chi usa lo screen reader. Nell’elenco dei titoli quelli da sistemare sono segnati.', 'olobuild' ) );
		} elseif ( count( $titoli ) > 1 ) {
			$aggiungi( 'gerarchia', 'ok', __( 'I titoli scendono in ordine', 'olobuild' ) );
		}
		if ( $vuoti ) {
			$aggiungi( 'titoli-vuoti', 'attenzione', sprintf( _n( '%d titolo è vuoto', '%d titoli sono vuoti', $vuoti, 'olobuild' ), $vuoti ) );
		}

		/* ── Testo, immagini, link ────────────────────────────────── */
		$parole = $pagina['parole'];
		if ( $parole < self::PAROLE_MINIME ) {
			$aggiungi( 'testo', 'attenzione', sprintf( __( 'Poco testo: %d parole', 'olobuild' ), $parole ),
				__( 'Motori e assistenti IA capiscono una pagina dal suo testo: qualche paragrafo che spieghi cosa offri, a chi e dove la rende più facile da trovare e da citare.', 'olobuild' ) );
		} else {
			$aggiungi( 'testo', 'ok', sprintf( __( '%d parole di testo', 'olobuild' ), $parole ) );
		}

		if ( $pagina['immagini'] ) {
			if ( $pagina['immagini_senza_alt'] ) {
				$aggiungi( 'alt', 'attenzione', sprintf( _n( '%d immagine senza testo alternativo', '%d immagini senza testo alternativo', $pagina['immagini_senza_alt'], 'olobuild' ), $pagina['immagini_senza_alt'] ),
					__( 'Il testo alternativo descrive l’immagine a Google Immagini, alle IA e a chi non vede. Si scrive nella libreria dei media o nel campo dell’immagine della tile.', 'olobuild' ) );
			} else {
				$aggiungi( 'alt', 'ok', __( 'Tutte le immagini hanno il testo alternativo', 'olobuild' ) );
			}
		}

		if ( 0 === $pagina['link_interni'] ) {
			$aggiungi( 'link-interni', 'attenzione', __( 'Nessun link verso altre pagine del sito', 'olobuild' ),
				__( 'I link nel testo verso le altre pagine del sito aiutano i motori a trovarle e a capire di cosa parlano. Testata e piè di pagina non contano qui.', 'olobuild' ) );
		} else {
			$aggiungi( 'link-interni', 'ok', sprintf( _n( '%d link verso il sito', '%d link verso il sito', $pagina['link_interni'], 'olobuild' ), $pagina['link_interni'] ) );
		}
		if ( $pagina['link_senza_testo'] ) {
			$aggiungi( 'link-muti', 'attenzione', sprintf( _n( '%d link senza testo', '%d link senza testo', $pagina['link_senza_testo'], 'olobuild' ), $pagina['link_senza_testo'] ),
				__( 'Un link fatto solo di un’icona o di un’immagine senza testo alternativo non dice dove porta, né ai motori né a chi usa lo screen reader.', 'olobuild' ) );
		}

		/* ── Parola chiave ────────────────────────────────────────── */
		$kw = self::normalizza( $seo['focus_keyword'] );
		if ( '' !== $kw ) {
			$in = function ( $testo ) use ( $kw ) {
				return false !== strpos( ' ' . self::normalizza( $testo ) . ' ', ' ' . $kw . ' ' );
			};
			$slug = str_replace( '-', ' ', (string) $post->post_name );

			$punti = [
				'kw-titolo'      => [ $in( $titolo ), __( 'Parola chiave nel titolo', 'olobuild' ), __( 'Parola chiave assente dal titolo', 'olobuild' ) ],
				'kw-descrizione' => [ $in( $descrizione ), __( 'Parola chiave nella descrizione', 'olobuild' ), __( 'Parola chiave assente dalla descrizione', 'olobuild' ) ],
				'kw-indirizzo'   => [ $in( $slug ), __( 'Parola chiave nell’indirizzo', 'olobuild' ), __( 'Parola chiave assente dall’indirizzo', 'olobuild' ) ],
				'kw-h1'          => [ $h1 && $in( $h1[0]['testo'] ), __( 'Parola chiave nel titolo H1', 'olobuild' ), __( 'Parola chiave assente dal titolo H1', 'olobuild' ) ],
				'kw-inizio'      => [ $in( $pagina['inizio'] ), __( 'Parola chiave all’inizio del testo', 'olobuild' ), __( 'Parola chiave assente dall’inizio del testo', 'olobuild' ) ],
			];
			foreach ( $punti as $id => $p ) {
				$aggiungi( $id, $p[0] ? 'ok' : 'attenzione', $p[0] ? $p[1] : $p[2] );
			}
			if ( ! $in( $pagina['testo'] ) ) {
				$aggiungi( 'kw-testo', 'attenzione', __( 'La parola chiave non compare mai nel testo', 'olobuild' ),
					__( 'Non serve ripeterla: basta che la pagina parli davvero di quello, con le parole che userebbe chi la cerca.', 'olobuild' ) );
			}
			$rivale = self::altro_post_con_meta( '_olo_seo_focus_keyword', $seo['focus_keyword'], $post->ID );
			if ( $rivale ) {
				$aggiungi( 'kw-doppia', 'attenzione', __( 'Un’altra pagina punta alla stessa parola chiave', 'olobuild' ),
					sprintf(
						/* translators: %s: titolo dell'altra pagina */
						__( 'Anche «%s» ha questa parola chiave: le due pagine si contendono la stessa ricerca. Dai a ognuna la sua.', 'olobuild' ),
						get_the_title( $rivale )
					) );
			}
		}

		/* ── Immagine social e dati strutturati ───────────────────── */
		$immagine_social = '' !== $seo['og_image'] || has_post_thumbnail( $post )
			|| ( class_exists( 'Olobuild_Og_Disegnata' ) && Olobuild_Og_Disegnata::attiva() );
		if ( ! $immagine_social ) {
			$aggiungi( 'og', 'info', __( 'Nessuna immagine per i social', 'olobuild' ),
				__( 'Quando qualcuno condivide la pagina, l’anteprima userà l’immagine predefinita del sito o il logo. Ne puoi scegliere una nella scheda Social, o mettere un’immagine in evidenza.', 'olobuild' ) );
		}

		if ( class_exists( 'Olobuild_Seo_Head' ) ) {
			foreach ( Olobuild_Seo_Head::controlla_dati_schema( $seo['schema_type'], $seo['schema_data'] ) as $c ) {
				$aggiungi( $c['id'], $c['esito'], $c['testo'], $c['aiuto'] ?? '' );
			}
		}

		$conta = [ 'errore' => 0, 'attenzione' => 0, 'info' => 0, 'ok' => 0 ];
		foreach ( $controlli as $c ) {
			$conta[ $c['esito'] ]++;
		}

		return [
			'riepilogo' => $conta,
			'controlli' => $controlli,
			'titoli'    => $titoli,
			'dati'      => [
				'parole'             => $parole,
				// Quante arrivano col JavaScript, dalle tile pigre (leggi_html()).
				'parole_pigre'       => $pagina['parole_pigre'],
				'immagini'           => $pagina['immagini'],
				'immagini_senza_alt' => $pagina['immagini_senza_alt'],
				'link_interni'       => $pagina['link_interni'],
				'link_esterni'       => $pagina['link_esterni'],
				'titolo'             => $titolo,
			],
		];
	}

	/**
	 * Cosa contiene l'HTML: titoli, testo, immagini, link.
	 */
	private static function leggi_html( $html ) {
		$vuota = [
			'titoli'             => [],
			'testo'              => '',
			'inizio'             => '',
			'parole'             => 0,
			'immagini'           => 0,
			'immagini_senza_alt' => 0,
			'link_interni'       => 0,
			'link_esterni'       => 0,
			'link_senza_testo'   => 0,
			'parole_pigre'       => 0,
		];
		if ( '' === trim( (string) $html ) ) {
			return $vuota;
		}

		// Script, stili e noscript via PRIMA del parser: quello di PHP (libxml) segue le regole dell'HTML 4 e
		// chiude uno script in linea al primo «<br>» scritto in un commento JavaScript, e il resto del codice
		// finiva nel testo della pagina. Uno script finisce al primo «</script», come nei browser.
		$senza = preg_replace( '#<(script|style|noscript)\b[^>]*>.*?</\1\s*>#is', ' ', (string) $html );
		if ( null !== $senza ) { // null = limite di PCRE superato: si tiene l'HTML com'è
			$html = $senza;
		}

		$dom      = new DOMDocument();
		$vecchio  = libxml_use_internal_errors( true );
		$caricato = $dom->loadHTML( '<?xml encoding="UTF-8"><div id="olo-seo-radice">' . $html . '</div>', LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED );
		libxml_clear_errors();
		libxml_use_internal_errors( $vecchio );
		if ( ! $caricato ) {
			return $vuota;
		}
		$xp = new DOMXPath( $dom );

		// Le tile dopo le prime tre nascono pigre, dentro <template class="olo-lazy-content">, e il JavaScript le
		// mette nella pagina quando si scorre (Olobuild_Frontend_Renderer::maybe_lazy_wrap): chi visita le vede, e
		// qui contano come il resto. Si misura a parte quanto testo arriva così: chi legge l'HTML senza eseguire
		// JavaScript (molti motori delle IA) può non vederlo.
		$pigre = "//template[contains(concat(' ', normalize-space(@class), ' '), ' olo-lazy-content ')]";
		$testo_pigro = '';
		// Solo le più esterne: una tile pigra dentro un'altra pigra (le tile di un contenitore) si conterebbe due volte.
		foreach ( $xp->query( $pigre . "[not(ancestor::template[contains(concat(' ', normalize-space(@class), ' '), ' olo-lazy-content ')])]" ) as $n ) {
			$testo_pigro .= ' ' . self::testo_blocchi( $n );
		}

		// Fuori ciò che non è testo della pagina: script, stili, gli altri modelli, e le copie decorative che le
		// tile nascondono agli screen reader (le scritte che scorrono ripetute, gli sfondi).
		$via = [];
		foreach ( $xp->query( "//script|//style|//template[not(contains(concat(' ', normalize-space(@class), ' '), ' olo-lazy-content '))]|//noscript|//svg|//*[@aria-hidden=\"true\"]" ) as $n ) {
			$via[] = $n;
		}
		foreach ( $via as $n ) {
			if ( $n->parentNode ) {
				$n->parentNode->removeChild( $n );
			}
		}

		$out = $vuota;

		foreach ( $xp->query( '//h1|//h2|//h3|//h4|//h5|//h6' ) as $n ) {
			$out['titoli'][] = [
				'livello'  => (int) substr( strtolower( $n->nodeName ), 1 ),
				'testo'    => self::testo_pulito( $n->textContent ),
				'problema' => '',
			];
		}

		foreach ( $xp->query( '//img' ) as $n ) {
			$out['immagini']++;
			// alt="" è un'immagine decorativa dichiarata: va bene. Manca solo quando l'attributo non c'è.
			if ( ! $n->hasAttribute( 'alt' ) ) {
				$out['immagini_senza_alt']++;
			}
		}

		$casa = self::host( home_url() );
		foreach ( $xp->query( '//a[@href]' ) as $n ) {
			$href = trim( $n->getAttribute( 'href' ) );
			if ( '' === $href || '#' === $href[0] || preg_match( '#^(javascript|mailto|tel|sms|data):#i', $href ) ) {
				continue;
			}
			$host = self::host( $href );
			if ( '' === $host || $host === $casa ) {
				$out['link_interni']++;
			} else {
				$out['link_esterni']++;
			}
			$nome = self::testo_pulito( $n->textContent )
				. $n->getAttribute( 'aria-label' ) . $n->getAttribute( 'title' );
			if ( '' === trim( $nome ) ) {
				foreach ( $n->getElementsByTagName( 'img' ) as $img ) {
					$nome .= $img->getAttribute( 'alt' );
				}
			}
			if ( '' === trim( $nome ) ) {
				$out['link_senza_testo']++;
			}
		}

		// Il testo: i blocchi separati da uno spazio, così «Titolo</h2><p>Testo» non diventa «TitoloTesto».
		$radice = $xp->query( '//*[@id="olo-seo-radice"]' )->item( 0 );
		$testo  = $radice ? self::testo_blocchi( $radice ) : '';
		$testo  = self::testo_pulito( $testo );
		$parole = self::parole( $testo );

		$out['testo']        = $testo;
		$out['parole']       = count( $parole );
		$out['parole_pigre'] = count( self::parole( self::testo_pulito( $testo_pigro ) ) );
		$out['inizio'] = implode( ' ', array_slice( $parole, 0, self::PAROLE_INIZIO ) );

		return $out;
	}

	/** Le parole di un testo: i pezzi fra gli spazi che hanno almeno una lettera o una cifra. */
	private static function parole( $testo ) {
		$pezzi = preg_split( '/\s+/u', (string) $testo, -1, PREG_SPLIT_NO_EMPTY );
		return array_values( array_filter( (array) $pezzi, function ( $p ) { return (bool) preg_match( '/[\p{L}\p{N}]/u', $p ); } ) );
	}

	/** Gli elementi in linea: il loro testo si attacca a quello vicino, come nel browser («<b>W</b>ord» = Word). */
	const IN_LINEA = [ 'a', 'abbr', 'b', 'bdi', 'bdo', 'cite', 'code', 'data', 'del', 'dfn', 'em', 'font', 'i', 'ins', 'kbd', 'mark', 'q', 's', 'samp', 'small', 'span', 'strong', 'sub', 'sup', 'time', 'u', 'var' ];

	/** Il testo di un nodo, con uno spazio attorno agli elementi a blocco: «Titolo</h2><p>Testo» non fa «TitoloTesto». */
	private static function testo_blocchi( $nodo ) {
		$s = '';
		foreach ( $nodo->childNodes as $figlio ) {
			if ( XML_TEXT_NODE === $figlio->nodeType || XML_CDATA_SECTION_NODE === $figlio->nodeType ) {
				$s .= $figlio->nodeValue;
			} elseif ( XML_ELEMENT_NODE === $figlio->nodeType ) {
				$dentro = self::testo_blocchi( $figlio );
				$s     .= in_array( strtolower( $figlio->nodeName ), self::IN_LINEA, true ) ? $dentro : ' ' . $dentro . ' ';
			}
		}
		return $s;
	}

	private static function testo_pulito( $s ) {
		$s = html_entity_decode( (string) $s, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$s = preg_replace( '/[\s\x{00A0}\x{200B}]+/u', ' ', $s );
		return trim( (string) $s );
	}

	/** Minuscolo, senza accenti né punteggiatura: «Page-builder WordPress» = «page builder wordpress». */
	public static function normalizza( $s ) {
		// Niente wp_strip_all_tags: qui arriva già testo, e a un «<» rimasto (un 3 < 5) si mangiava tutto il resto.
		$s = remove_accents( html_entity_decode( (string) $s, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
		$s = function_exists( 'mb_strtolower' ) ? mb_strtolower( $s, 'UTF-8' ) : strtolower( $s );
		$s = preg_replace( '/[^\p{L}\p{N}]+/u', ' ', $s );
		return trim( (string) $s );
	}

	private static function lunghezza( $s ) {
		$s = html_entity_decode( (string) $s, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		return function_exists( 'mb_strlen' ) ? mb_strlen( $s, 'UTF-8' ) : strlen( $s );
	}

	private static function host( $url ) {
		$h = wp_parse_url( (string) $url, PHP_URL_HOST );
		return $h ? preg_replace( '/^www\./i', '', strtolower( $h ) ) : '';
	}

	private static function url_normalizzato( $url ) {
		return untrailingslashit( preg_replace( '#^https?://(www\.)?#i', '', strtolower( trim( (string) $url ) ) ) );
	}

	/**
	 * Il titolo che Google vedrà: quello SEO della pagina o, vuoto, quello dato dagli schemi della scheda SEO
	 * globale (lo stesso calcolo di Olobuild_Seo_Head, ridotto alla pagina singola).
	 */
	private static function titolo_effettivo( $post, $personale ) {
		$personale = trim( (string) $personale );
		if ( '' !== $personale ) {
			return $personale;
		}
		$titoli = class_exists( 'Olobuild_Seo_Settings' ) ? Olobuild_Seo_Settings::get( 'olo_seo_titles' ) : [];
		$titoli = is_array( $titoli ) ? $titoli : [];
		$sito   = get_bloginfo( 'name' );
		$sep    = ( isset( $titoli['separator'] ) && '' !== trim( (string) $titoli['separator'] ) ) ? $titoli['separator'] : '–';
		$nome   = get_the_title( $post );
		$home   = (int) get_option( 'page_on_front' ) === (int) $post->ID;

		$modello = $home ? ( $titoli['homepage_title'] ?? '' ) : ( 'post' === $post->post_type ? ( $titoli['post_title'] ?? '' ) : ( 'page' === $post->post_type ? ( $titoli['page_title'] ?? '' ) : '' ) );
		if ( $modello ) {
			$risolto = trim( strtr( $modello, [
				'%sitename%' => $sito,
				'%tagline%'  => get_bloginfo( 'description' ),
				'%sep%'      => $sep,
				'%title%'    => $nome,
				'%excerpt%'  => '',
				'%page%'     => '',
			] ) );
			if ( '' !== $risolto ) {
				return $risolto;
			}
		}
		if ( $home ) {
			$motto = get_bloginfo( 'description' );
			return $motto ? $sito . ' ' . $sep . ' ' . $motto : $sito;
		}
		$schema = trim( (string) ( $titoli['pattern'] ?? '' ) );
		if ( false !== strpos( $schema, '{page}' ) ) {
			return trim( strtr( $schema, [ '{page}' => $nome, '{sep}' => $sep, '{site}' => $sito ] ) );
		}
		return $nome . ' ' . $sep . ' ' . $sito;
	}

	/**
	 * Un altro contenuto pubblicato con lo stesso valore in un meta (senza badare a maiuscole e accenti).
	 *
	 * @return int L'ID dell'altro contenuto, 0 se non c'è.
	 */
	private static function altro_post_con_meta( $meta_key, $valore, $escludi ) {
		global $wpdb;
		$valore = trim( (string) $valore );
		if ( '' === $valore ) {
			return 0;
		}
		// Una lettura puntuale su postmeta, al momento dell'analisi; il confronto vero lo fa normalizza() qui sotto.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$righe = $wpdb->get_results( $wpdb->prepare(
			"SELECT pm.post_id, pm.meta_value FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
			 WHERE pm.meta_key = %s AND pm.post_id <> %d AND p.post_status = 'publish' AND LOWER(pm.meta_value) = LOWER(%s) LIMIT 5",
			$meta_key,
			(int) $escludi,
			$valore
		) );
		$cerca = self::normalizza( $valore );
		foreach ( (array) $righe as $r ) {
			if ( self::normalizza( $r->meta_value ) === $cerca ) {
				return (int) $r->post_id;
			}
		}
		return 0;
	}
}
