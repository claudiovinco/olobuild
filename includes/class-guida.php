<?php
/**
 * Olobuild_Guida — la pagina Guida della shell e l'elenco delle guide.
 *
 * Le guide sono file del pacchetto (`includes/guide/<lingua>/<slug>.php`, solo HTML):
 * corrispondono sempre alla versione installata e funzionano senza rete. Il
 * Centro risorse della bacheca e l'icona (?) della barra in alto puntano qui.
 * Le novità si leggono da includes/guide/novita.txt (Olobuild_Rest_Dashboard_Trait::changelog_voci).
 *
 * @package Olobuild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Olobuild_Guida {

	const SLUG = 'olo-guida';

	/**
	 * La versione su cui le guide sono state verificate. Si aggiorna quando
	 * una guida viene riletta sul codice, NON a ogni rilascio: dice al lettore
	 * da quando il testo corrisponde all'interfaccia.
	 */
	const VERIFICATA = '1.4.501';

	public static function init() {
		// Dopo admin_menu di Olobuild_Builder, che crea il menu padre «olobuild».
		// La voce esce dal menu di WordPress in Olobuild_Builder::admin_menu_trim()
		// e vive nella sub-nav dell'area Sistema.
		add_action( 'admin_menu', [ __CLASS__, 'add_menu' ], 20 );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue' ] );
	}

	public static function add_menu() {
		add_submenu_page( 'olobuild', __( 'Guida', 'olobuild' ), __( 'Guida', 'olobuild' ), 'manage_options', self::SLUG, [ __CLASS__, 'render_page' ] );
	}

	public static function enqueue( $hook ) {
		if ( 'olobuild_page_' . self::SLUG === $hook ) {
			wp_enqueue_style( 'olo-guida-css', OLOBUILD_URL . 'assets/css/guida.css', [ 'olo-cockpit-css' ], OLOBUILD_VERSION );
		}
	}

	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		Olobuild_Builder::cockpit_shell_open();
		echo '<main class="olo-cockpit-main olo-guida-page">';
		self::render_contenuto();
		echo '</main>';
		Olobuild_Builder::cockpit_shell_close();
	}

	/**
	 * L'elenco delle guide, nell'ordine di lettura. `icona` = nome in icona().
	 */
	public static function guide() {
		return [
			'primi-passi' => [
				'titolo' => __( 'Primi passi', 'olobuild' ),
				'desc'   => __( 'Dalla bacheca alla prima pagina pubblicata: crea, componi con le tile, salva e pubblica.', 'olobuild' ),
				'icona'  => 'bussola',
			],
			'template'    => [
				'titolo' => __( 'Template', 'olobuild' ),
				'desc'   => __( 'Pagine, header, footer e blocchi: dove si trovano, a cosa si applicano, revisioni e import.', 'olobuild' ),
				'icona'  => 'template',
			],
			'seo'         => [
				'titolo' => __( 'SEO e Open Graph', 'olobuild' ),
				'desc'   => __( 'Titolo e descrizione per i motori di ricerca, anteprime social, dati strutturati, redirect e 404.', 'olobuild' ),
				'icona'  => 'cerca',
			],
			'prestazioni' => [
				'titolo' => __( 'Prestazioni', 'olobuild' ),
				'desc'   => __( 'Cosa fa Olobuild da solo per pagine leggere, e cosa conviene fare a te.', 'olobuild' ),
				'icona'  => 'tachimetro',
			],
		];
	}

	/** Le Novità si vedono, salvo «Nascondi changelog» del White Label. */
	public static function novita_visibili() {
		return ! ( class_exists( 'Olobuild_White_Label' ) && Olobuild_White_Label::nascondi_novita() );
	}

	/** Documentazione dell'agenzia (White Label) al posto delle guide di Olobuild. */
	public static function aiuto_esterno() {
		return class_exists( 'Olobuild_White_Label' ) && '' !== Olobuild_White_Label::doc_url();
	}

	/** Dove porta il (?) della barra in alto: la Guida, o la documentazione dell'agenzia. */
	public static function url_aiuto() {
		return self::aiuto_esterno() ? Olobuild_White_Label::doc_url() : self::url();
	}

	private static function marchio() {
		return (string) apply_filters( 'olobuild_brand_name', 'Olobuild' );
	}

	public static function url( $slug = '' ) {
		$args = [ 'page' => self::SLUG ];
		if ( $slug ) {
			$args['g'] = $slug;
		}
		return add_query_arg( $args, admin_url( 'admin.php' ) );
	}

	/* ─────────────────────────────────────────────
	 * Il manuale: gli argomenti del tipo «manuale» del sito di OLObuild (olotheme.com/olobuild), letti dalla rotta
	 * pubblica olobuild/v1/argomenti (Olobuild_Argomenti). Si scrive nella Redazione di quel sito: una correzione lì si
	 * vede qui entro un'ora. Senza rete restano le guide brevi del plugin.
	 * ───────────────────────────────────────────── */

	const MANUALE = 'https://olotheme.com/olobuild/wp-json/olobuild/v1/argomenti/manuale';

	/** Il manuale si vede, salvo la documentazione dell'agenzia (White Label) che prende il posto delle guide di OLObuild. */
	public static function manuale_attivo() {
		return ! self::aiuto_esterno();
	}

	private static function manuale_fonte() {
		return untrailingslashit( (string) apply_filters( 'olobuild_manuale_fonte', self::MANUALE ) );
	}

	/** Il sito che scrive il manuale lo legge da sé: anche gli argomenti privati, per chi li può leggere (per rivederli). */
	private static function manuale_locale() {
		$sito = preg_replace( '#/wp-json/.*$#', '', self::manuale_fonte() );
		return class_exists( 'Olobuild_Argomenti' ) && post_type_exists( 'manuale' ) && untrailingslashit( home_url() ) === $sito;
	}

	/**
	 * L'indice ($id = 0) o un argomento del manuale; null se il sito non risponde o l'argomento non c'è. Le risposte del
	 * sito restano un'ora in memoria.
	 */
	public static function manuale_leggi( $id = 0 ) {
		static $memo = [];
		$id = absint( $id );
		if ( array_key_exists( $id, $memo ) ) {
			return $memo[ $id ];
		}
		if ( self::manuale_locale() ) {
			if ( ! $id ) {
				return $memo[ $id ] = Olobuild_Argomenti::indice_dati( 'manuale' );
			}
			$p = get_post( $id );
			$ok = $p && 'manuale' === $p->post_type && ( 'publish' === $p->post_status || current_user_can( 'read_post', $id ) );
			return $memo[ $id ] = $ok ? Olobuild_Argomenti::argomento_dati( $p ) : null;
		}
		$url    = self::manuale_fonte() . ( $id ? '/' . $id : '' );
		$chiave = 'olo_manuale_' . md5( $url );
		$c      = get_transient( $chiave );
		if ( is_array( $c ) ) {
			return $memo[ $id ] = $c;
		}
		$r = wp_remote_get( $url, [ 'timeout' => 8, 'headers' => [ 'Accept' => 'application/json' ] ] );
		if ( is_wp_error( $r ) || 200 !== (int) wp_remote_retrieve_response_code( $r ) ) {
			return $memo[ $id ] = null;
		}
		$d = json_decode( (string) wp_remote_retrieve_body( $r ), true );
		if ( ! is_array( $d ) ) {
			return $memo[ $id ] = null;
		}
		set_transient( $chiave, $d, HOUR_IN_SECONDS );
		return $memo[ $id ] = $d;
	}

	public static function url_manuale( $id = 0 ) {
		$u = self::url( 'manuale' );
		return $id ? add_query_arg( 'a', absint( $id ), $u ) : $u;
	}

	private static function youtube_id( $url ) {
		return preg_match( '~(?:youtu\.be/|youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/))([A-Za-z0-9_-]{11})~', (string) $url, $m ) ? $m[1] : '';
	}

	/** L'argomento aperto nella Guida (?a=), 0 = il sommario. */
	private static function argomento_aperto() {
		return isset( $_GET['a'] ) ? absint( wp_unslash( $_GET['a'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- sola lettura: sceglie quale argomento mostrare.
	}

	/** L'indice del manuale nella colonna di sinistra, al posto delle guide. */
	private static function render_nav_manuale() {
		$ind  = self::manuale_leggi( 0 );
		$voci = is_array( $ind['voci'] ?? null ) ? $ind['voci'] : [];
		$id   = self::argomento_aperto();
		$arg  = $id ? self::manuale_leggi( $id ) : null;
		$ramo = array_flip( array_map( function ( $x ) {
			return (int) $x['id'];
		}, is_array( $arg['antenati'] ?? null ) ? $arg['antenati'] : [] ) );
		?>
		<a href="<?php echo esc_url( self::url() ); ?>">
			<span class="ic"><?php echo self::icona( 'indietro', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG statico di icona(). ?></span><?php esc_html_e( 'Tutte le guide', 'olobuild' ); ?>
		</a>
		<div class="sep"></div>
		<p class="lbl"><?php esc_html_e( 'Manuale', 'olobuild' ); ?></p>
		<a href="<?php echo esc_url( self::url_manuale() ); ?>" class="<?php echo $id ? '' : 'on'; ?>"<?php echo $id ? '' : ' aria-current="page"'; ?>>
			<span class="ic"><?php echo self::icona( 'libro', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG statico di icona(). ?></span><?php esc_html_e( 'Sommario', 'olobuild' ); ?>
		</a>
		<?php foreach ( $voci as $v ) : ?>
			<?php
			$vid = (int) $v['id'];
			$cls = 'olo-man-g-voce l' . min( 5, (int) $v['livello'] ) . ( $vid === $id ? ' on' : '' ) . ( isset( $ramo[ $vid ] ) ? ' ramo' : '' );
			?>
		<a class="<?php echo esc_attr( $cls ); ?>" href="<?php echo esc_url( self::url_manuale( $vid ) ); ?>"<?php echo $vid === $id ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $v['titolo'] ); ?></a>
		<?php endforeach; ?>
		<?php
	}

	/** Il sommario del manuale o un suo argomento. */
	private static function render_manuale() {
		$ind  = self::manuale_leggi( 0 );
		$voci = is_array( $ind['voci'] ?? null ) ? $ind['voci'] : [];
		$id   = self::argomento_aperto();
		$arg  = $id ? self::manuale_leggi( $id ) : null;
		// La porta d'ingresso del manuale sul sito (la pagina /guida/ del sito di OLObuild).
		$sito = is_string( $ind['casa'] ?? null ) ? $ind['casa'] : ( is_string( $ind['sito'] ?? null ) ? $ind['sito'] : '' );
		if ( null === $ind || ! $voci ) {
			?>
			<header class="olo-guida-head">
				<p class="kick"><?php esc_html_e( 'Manuale', 'olobuild' ); ?></p>
				<h1><?php esc_html_e( 'Manuale di OLObuild', 'olobuild' ); ?></h1>
				<p class="lead"><?php echo null === $ind
					? esc_html__( 'Il manuale si legge dal sito di OLObuild, che ora non risponde. Riprova tra poco: intanto le guide brevi del plugin funzionano anche senza rete.', 'olobuild' )
					: esc_html__( 'Il manuale è in preparazione. Intanto le guide brevi del plugin spiegano le cose principali.', 'olobuild' ); ?></p>
			</header>
			<a class="olo-btn olo-btn-sec" href="<?php echo esc_url( self::url() ); ?>"><?php esc_html_e( 'Vai alle guide', 'olobuild' ); ?></a>
			<?php
			return;
		}
		if ( ! $arg ) {
			?>
			<article class="olo-guida-art olo-man-g">
				<header class="olo-guida-head">
					<p class="kick"><?php esc_html_e( 'Manuale', 'olobuild' ); ?></p>
					<h1><?php esc_html_e( 'Manuale di OLObuild', 'olobuild' ); ?></h1>
					<p class="lead"><?php esc_html_e( 'Tutto quello che serve sapere, un argomento alla volta, coi video del canale. È lo stesso manuale del sito di OLObuild e si aggiorna da solo.', 'olobuild' ); ?></p>
					<?php if ( $sito ) : ?>
					<p class="meta"><a href="<?php echo esc_url( $sito ); ?>" target="_blank" rel="noopener"><?php echo self::icona( 'freccia', 13 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG statico di icona(). ?> <?php esc_html_e( 'Apri il manuale sul sito', 'olobuild' ); ?></a></p>
					<?php endif; ?>
				</header>
				<ul class="olo-man-g-sommario">
					<?php foreach ( $voci as $v ) : ?>
					<li class="l<?php echo (int) min( 5, (int) $v['livello'] ); ?>"><a href="<?php echo esc_url( self::url_manuale( (int) $v['id'] ) ); ?>"><?php echo esc_html( $v['titolo'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</article>
			<?php
			return;
		}
		$yt = self::youtube_id( $arg['video'] ?? '' );
		?>
		<article class="olo-guida-art olo-man-g">
			<header class="olo-guida-head">
				<p class="kick olo-man-g-briciole">
					<a href="<?php echo esc_url( self::url_manuale() ); ?>"><?php esc_html_e( 'Manuale', 'olobuild' ); ?></a>
					<?php foreach ( (array) ( $arg['antenati'] ?? [] ) as $a ) : ?>
					<span aria-hidden="true">›</span> <a href="<?php echo esc_url( self::url_manuale( (int) $a['id'] ) ); ?>"><?php echo esc_html( $a['titolo'] ); ?></a>
					<?php endforeach; ?>
				</p>
				<h1><?php echo esc_html( $arg['titolo'] ?? '' ); ?></h1>
				<?php if ( ! empty( $arg['sottotitolo'] ) ) : ?>
				<p class="lead"><?php echo esc_html( $arg['sottotitolo'] ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $arg['link'] ) ) : ?>
				<p class="meta"><a href="<?php echo esc_url( $arg['link'] ); ?>" target="_blank" rel="noopener"><?php echo self::icona( 'freccia', 13 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG statico di icona(). ?> <?php esc_html_e( 'Apri sul sito', 'olobuild' ); ?></a></p>
				<?php endif; ?>
			</header>
			<?php if ( $yt ) : ?>
			<div class="olo-man-g-video"><iframe src="<?php echo esc_url( 'https://www.youtube-nocookie.com/embed/' . $yt ); ?>" title="<?php echo esc_attr( sprintf( __( 'Video: %s', 'olobuild' ), $arg['titolo'] ?? '' ) ); ?>" loading="lazy" allow="encrypted-media; picture-in-picture" allowfullscreen></iframe></div>
			<?php endif; ?>
			<div class="olo-testo-redazione"><?php echo wp_kses_post( (string) ( $arg['html'] ?? '' ) ); ?></div>
			<?php if ( ! empty( $arg['figli'] ) ) : ?>
			<div class="olo-man-g-figli">
				<strong><?php esc_html_e( 'In questa sezione', 'olobuild' ); ?></strong>
				<ul>
					<?php foreach ( $arg['figli'] as $f ) : ?>
					<li><a href="<?php echo esc_url( self::url_manuale( (int) $f['id'] ) ); ?>"><?php echo esc_html( $f['titolo'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php endif; ?>
			<nav class="olo-guida-piede" aria-label="<?php esc_attr_e( 'Altri argomenti', 'olobuild' ); ?>">
				<?php if ( ! empty( $arg['prima'] ) ) : ?>
				<a href="<?php echo esc_url( self::url_manuale( (int) $arg['prima']['id'] ) ); ?>"><span><?php esc_html_e( 'Precedente', 'olobuild' ); ?></span><strong><?php echo esc_html( $arg['prima']['titolo'] ); ?></strong></a>
				<?php endif; ?>
				<?php if ( ! empty( $arg['dopo'] ) ) : ?>
				<a class="succ" href="<?php echo esc_url( self::url_manuale( (int) $arg['dopo']['id'] ) ); ?>"><span><?php esc_html_e( 'Successivo', 'olobuild' ); ?></span><strong><?php echo esc_html( $arg['dopo']['titolo'] ); ?></strong></a>
				<?php endif; ?>
			</nav>
		</article>
		<?php
	}

	/**
	 * Il file della guida nella lingua del sito, altrimenti in italiano.
	 */
	private static function file( $slug ) {
		$lingua = substr( determine_locale(), 0, 2 );
		foreach ( array_unique( [ $lingua, 'it' ] ) as $l ) {
			$f = OLOBUILD_PATH . 'includes/guide/' . $l . '/' . $slug . '.php';
			if ( file_exists( $f ) ) {
				return $f;
			}
		}
		return '';
	}

	/**
	 * Minuti di lettura, contati sul testo (200 parole al minuto, almeno 1).
	 */
	public static function minuti( $slug ) {
		static $memo = [];
		if ( isset( $memo[ $slug ] ) ) {
			return $memo[ $slug ];
		}
		$f = self::file( $slug );
		// Via i blocchi PHP prima dei tag: strip_tags davanti a «<?php» butta tutto il
		// file. E le parole si contano sugli spazi, perché str_word_count spezza le
		// parole accentate.
		$testo  = $f ? wp_strip_all_tags( preg_replace( '/<\?php.*?\?>/s', ' ', (string) file_get_contents( $f ) ) ) : '';
		$parole = '' === trim( $testo ) ? 0 : count( preg_split( '/\s+/u', trim( $testo ) ) );
		return $memo[ $slug ] = max( 1, (int) round( $parole / 200 ) );
	}

	/**
	 * L'email al supporto, già compilata con i dati che servono a rispondere.
	 * L'utente la legge e la invia dal suo programma di posta: niente parte da sola.
	 */
	public static function mailto_supporto() {
		global $wp_version;
		$a     = apply_filters( 'olobuild_support_email', 'info@olotheme.com' );
		$tema  = wp_get_theme();
		$corpo = sprintf(
			"%s\n\n\n---\n%s %s\nWordPress %s\nPHP %s\n%s %s\n%s",
			__( 'Descrivi il problema: cosa hai fatto, cosa ti aspettavi, cosa è successo.', 'olobuild' ),
			self::marchio(),
			OLOBUILD_VERSION,
			$wp_version,
			PHP_VERSION,
			__( 'Tema:', 'olobuild' ),
			$tema->get( 'Name' ) . ' ' . $tema->get( 'Version' ),
			home_url( '/' )
		);
		return 'mailto:' . $a
			. '?subject=' . rawurlencode( sprintf( __( 'Supporto %1$s %2$s', 'olobuild' ), self::marchio(), OLOBUILD_VERSION ) )
			. '&body=' . rawurlencode( $corpo );
	}

	/**
	 * Icone della Guida: stesso tratto delle icone della shell (stroke 1.7).
	 */
	public static function icona( $nome, $size = 18 ) {
		$p = [
			'bussola'    => '<circle cx="12" cy="12" r="9"/><path d="M15.5 8.5l-2 5-5 2 2-5z"/>',
			'template'   => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>',
			'cerca'      => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
			'tachimetro' => '<path d="M12 14 8 10"/><circle cx="12" cy="14" r="9"/><path d="M3 14a9 9 0 0 1 18 0"/>',
			'libro'      => '<path d="M2 5h6a4 4 0 014 4v11a3 3 0 00-3-3H2zM22 5h-6a4 4 0 00-4 4v11a3 3 0 013-3h7z"/>',
			'novita'     => '<path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8zM19 16l.8 2.2L22 19l-2.2.8L19 22l-.8-2.2L16 19l2.2-.8z"/>',
			'tastiera'   => '<rect x="2" y="6" width="20" height="12" rx="2"/><path d="M6 10h.01M10 10h.01M14 10h.01M18 10h.01M7 14h10"/>',
			'posta'      => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
			'aiuto'      => '<circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 015 0c0 1.5-2.5 2-2.5 4M12 17h.01"/>',
			'documento'  => '<path d="M14 3H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V8z"/><path d="M14 3v5h5M9 13h6M9 17h4"/>',
			'freccia'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
			'indietro'   => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
		];
		return '<svg width="' . (int) $size . '" height="' . (int) $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ( $p[ $nome ] ?? '' ) . '</svg>';
	}

	/**
	 * Il contenuto della pagina (dentro la shell): indice, una guida o le novità.
	 */
	public static function render_contenuto() {
		$guide = self::guide();
		$g     = isset( $_GET['g'] ) ? sanitize_key( wp_unslash( $_GET['g'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- sola lettura: sceglie quale guida mostrare.
		if ( ! isset( $guide[ $g ] ) && ! ( 'novita' === $g && self::novita_visibili() ) && ! ( 'manuale' === $g && self::manuale_attivo() ) ) {
			$g = '';
		}
		if ( 'manuale' === $g ) {
			?>
			<div class="olo-guida">
				<nav class="olo-guida-nav olo-man-g-nav" aria-label="<?php esc_attr_e( 'Manuale', 'olobuild' ); ?>">
					<?php self::render_nav_manuale(); ?>
				</nav>
				<div class="olo-guida-main"><?php self::render_manuale(); ?></div>
			</div>
			<?php
			return;
		}
		?>
		<div class="olo-guida">
			<nav class="olo-guida-nav" aria-label="<?php esc_attr_e( 'Guide', 'olobuild' ); ?>">
				<?php if ( self::manuale_attivo() ) : ?>
				<p class="lbl"><?php esc_html_e( 'Manuale', 'olobuild' ); ?></p>
				<a href="<?php echo esc_url( self::url_manuale() ); ?>">
					<span class="ic"><?php echo self::icona( 'libro', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG statico di icona(). ?></span><?php esc_html_e( 'Manuale di OLObuild', 'olobuild' ); ?>
				</a>
				<div class="sep"></div>
				<?php endif; ?>
				<p class="lbl"><?php esc_html_e( 'Guide', 'olobuild' ); ?></p>
				<a href="<?php echo esc_url( self::url() ); ?>" class="<?php echo '' === $g ? 'on' : ''; ?>"<?php echo '' === $g ? ' aria-current="page"' : ''; ?>>
					<span class="ic"><?php echo self::icona( 'libro', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG statico di icona(). ?></span><?php esc_html_e( 'Tutte le guide', 'olobuild' ); ?>
				</a>
				<?php foreach ( $guide as $slug => $info ) : ?>
				<a href="<?php echo esc_url( self::url( $slug ) ); ?>" class="<?php echo $slug === $g ? 'on' : ''; ?>"<?php echo $slug === $g ? ' aria-current="page"' : ''; ?>>
					<span class="ic"><?php echo self::icona( $info['icona'], 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG statico di icona(). ?></span><?php echo esc_html( $info['titolo'] ); ?>
				</a>
				<?php endforeach; ?>
				<div class="sep"></div>
				<?php if ( self::novita_visibili() ) : ?>
				<a href="<?php echo esc_url( self::url( 'novita' ) ); ?>" class="<?php echo 'novita' === $g ? 'on' : ''; ?>"<?php echo 'novita' === $g ? ' aria-current="page"' : ''; ?>>
					<span class="ic"><?php echo self::icona( 'novita', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG statico di icona(). ?></span><?php esc_html_e( 'Novità', 'olobuild' ); ?>
				</a>
				<?php endif; ?>
				<a href="<?php echo esc_attr( self::mailto_supporto() ); ?>">
					<span class="ic"><?php echo self::icona( 'posta', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG statico di icona(). ?></span><?php esc_html_e( 'Scrivi al supporto', 'olobuild' ); ?>
				</a>
			</nav>
			<div class="olo-guida-main">
				<?php
				if ( 'novita' === $g ) {
					self::render_novita();
				} elseif ( $g ) {
					self::render_guida( $g );
				} else {
					self::render_indice();
				}
				?>
			</div>
		</div>
		<?php
	}

	private static function render_indice() {
		?>
		<header class="olo-guida-head">
			<p class="kick"><?php echo esc_html( self::marchio() . ' ' . OLOBUILD_VERSION ); ?></p>
			<h1><?php esc_html_e( 'Guida', 'olobuild' ); ?></h1>
			<p class="lead"><?php echo esc_html( sprintf( __( 'Le guide di %s, scritte per la versione installata. Ognuna si legge in pochi minuti e rimanda alle schermate che descrive.', 'olobuild' ), self::marchio() ) ); ?></p>
		</header>
		<div class="olo-guida-cards">
			<?php if ( self::manuale_attivo() ) : ?>
			<a class="olo-guida-card olo-guida-card--manuale" href="<?php echo esc_url( self::url_manuale() ); ?>">
				<span class="ic"><?php echo self::icona( 'libro', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG statico di icona(). ?></span>
				<span class="t"><?php esc_html_e( 'Manuale di OLObuild', 'olobuild' ); ?></span>
				<span class="d"><?php esc_html_e( 'Tutto quello che serve sapere, un argomento alla volta, coi video del canale. Si legge dal sito di OLObuild e si aggiorna da solo.', 'olobuild' ); ?></span>
				<span class="m"><?php esc_html_e( 'Argomenti e sottoargomenti', 'olobuild' ); ?></span>
			</a>
			<?php endif; ?>
			<?php foreach ( self::guide() as $slug => $info ) : ?>
			<a class="olo-guida-card" href="<?php echo esc_url( self::url( $slug ) ); ?>">
				<span class="ic"><?php echo self::icona( $info['icona'], 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG statico di icona(). ?></span>
				<span class="t"><?php echo esc_html( $info['titolo'] ); ?></span>
				<span class="d"><?php echo esc_html( $info['desc'] ); ?></span>
				<span class="m"><?php echo esc_html( sprintf( _n( '%d minuto di lettura', '%d minuti di lettura', self::minuti( $slug ), 'olobuild' ), self::minuti( $slug ) ) ); ?></span>
			</a>
			<?php endforeach; ?>
		</div>
		<div class="olo-guida-aiuto">
			<div class="tx">
				<strong><?php esc_html_e( 'Non trovi la risposta?', 'olobuild' ); ?></strong>
				<span><?php echo esc_html( sprintf( __( "Scrivi al supporto. L'email parte già con la versione di %s, WordPress e PHP, così possiamo rispondere senza chiedertele.", 'olobuild' ), self::marchio() ) ); ?></span>
			</div>
			<a class="olo-btn olo-btn-sec" href="<?php echo esc_attr( self::mailto_supporto() ); ?>"><?php echo self::icona( 'posta', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG statico di icona(). ?> <?php esc_html_e( 'Scrivi al supporto', 'olobuild' ); ?></a>
		</div>
		<?php
	}

	private static function render_guida( $slug ) {
		$guide = self::guide();
		$file  = self::file( $slug );
		$chiavi = array_keys( $guide );
		$i      = array_search( $slug, $chiavi, true );
		$prec   = $i > 0 ? $chiavi[ $i - 1 ] : '';
		$succ   = $i < count( $chiavi ) - 1 ? $chiavi[ $i + 1 ] : '';
		?>
		<article class="olo-guida-art">
			<header class="olo-guida-head">
				<p class="kick"><?php esc_html_e( 'Guida', 'olobuild' ); ?></p>
				<h1><?php echo esc_html( $guide[ $slug ]['titolo'] ); ?></h1>
				<p class="lead"><?php echo esc_html( $guide[ $slug ]['desc'] ); ?></p>
				<p class="meta">
					<span><?php echo self::icona( 'documento', 13 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG statico di icona(). ?> <?php echo esc_html( sprintf( _n( '%d minuto di lettura', '%d minuti di lettura', self::minuti( $slug ), 'olobuild' ), self::minuti( $slug ) ) ); ?></span>
					<span><?php echo esc_html( sprintf( __( 'Scritta per %1$s %2$s', 'olobuild' ), self::marchio(), self::VERIFICATA ) ); ?></span>
				</p>
			</header>
			<?php
			if ( $file ) {
				include $file;
			}
			?>
			<nav class="olo-guida-piede" aria-label="<?php esc_attr_e( 'Altre guide', 'olobuild' ); ?>">
				<?php if ( $prec ) : ?>
				<a href="<?php echo esc_url( self::url( $prec ) ); ?>"><span><?php esc_html_e( 'Guida precedente', 'olobuild' ); ?></span><strong><?php echo esc_html( $guide[ $prec ]['titolo'] ); ?></strong></a>
				<?php endif; ?>
				<?php if ( $succ ) : ?>
				<a class="succ" href="<?php echo esc_url( self::url( $succ ) ); ?>"><span><?php esc_html_e( 'Guida successiva', 'olobuild' ); ?></span><strong><?php echo esc_html( $guide[ $succ ]['titolo'] ); ?></strong></a>
				<?php endif; ?>
			</nav>
		</article>
		<?php
	}

	private static function render_novita() {
		$voci = class_exists( 'Olobuild_Rest_Api' ) && method_exists( 'Olobuild_Rest_Api', 'changelog_voci' ) ? Olobuild_Rest_Api::changelog_voci() : [];
		?>
		<article class="olo-guida-art">
			<header class="olo-guida-head">
				<p class="kick"><?php echo esc_html( sprintf( __( 'Installata: %s', 'olobuild' ), 'v' . OLOBUILD_VERSION ) ); ?></p>
				<h1><?php esc_html_e( 'Novità', 'olobuild' ); ?></h1>
				<p class="lead"><?php esc_html_e( 'Cosa cambia in ogni versione, dalla più recente.', 'olobuild' ); ?></p>
			</header>
			<?php if ( empty( $voci ) ) : ?>
				<p><?php esc_html_e( 'Il registro delle novità non è incluso in questa installazione.', 'olobuild' ); ?></p>
			<?php else : ?>
				<?php foreach ( $voci as $n => $v ) : ?>
				<div class="olo-cl-item <?php echo $n > 0 ? 'old' : ''; ?>">
					<div class="v"><?php echo esc_html( $v['v'] ); ?>
						<?php if ( $v['date'] ) : ?><span class="date">· <?php echo esc_html( $v['date'] ); ?></span><?php endif; ?>
						<?php if ( $v['tag'] ) : ?><span class="tag <?php echo esc_attr( str_replace( 'à', 'a', $v['tag'] ) ); ?>"><?php echo esc_html( $v['label'] ?? $v['tag'] ); ?></span><?php endif; ?>
					</div>
					<?php if ( $v['items'] ) : ?>
					<ul><?php foreach ( $v['items'] as $it ) : ?><li><?php echo esc_html( $it ); ?></li><?php endforeach; ?></ul>
					<?php endif; ?>
				</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</article>
		<?php
	}
}
