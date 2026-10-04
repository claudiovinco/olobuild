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

	public static function url( $slug = '' ) {
		$args = [ 'page' => self::SLUG ];
		if ( $slug ) {
			$args['g'] = $slug;
		}
		return add_query_arg( $args, admin_url( 'admin.php' ) );
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
			"%s\n\n\n---\nOlobuild %s\nWordPress %s\nPHP %s\n%s %s\n%s",
			__( 'Descrivi il problema: cosa hai fatto, cosa ti aspettavi, cosa è successo.', 'olobuild' ),
			OLOBUILD_VERSION,
			$wp_version,
			PHP_VERSION,
			__( 'Tema:', 'olobuild' ),
			$tema->get( 'Name' ) . ' ' . $tema->get( 'Version' ),
			home_url( '/' )
		);
		return 'mailto:' . $a
			. '?subject=' . rawurlencode( sprintf( __( 'Supporto Olobuild %s', 'olobuild' ), OLOBUILD_VERSION ) )
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
		if ( 'novita' !== $g && ! isset( $guide[ $g ] ) ) {
			$g = '';
		}
		?>
		<div class="olo-guida">
			<nav class="olo-guida-nav" aria-label="<?php esc_attr_e( 'Guide', 'olobuild' ); ?>">
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
				<a href="<?php echo esc_url( self::url( 'novita' ) ); ?>" class="<?php echo 'novita' === $g ? 'on' : ''; ?>"<?php echo 'novita' === $g ? ' aria-current="page"' : ''; ?>>
					<span class="ic"><?php echo self::icona( 'novita', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG statico di icona(). ?></span><?php esc_html_e( 'Novità', 'olobuild' ); ?>
				</a>
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
			<p class="kick"><?php echo esc_html( sprintf( __( 'Olobuild %s', 'olobuild' ), OLOBUILD_VERSION ) ); ?></p>
			<h1><?php esc_html_e( 'Guida', 'olobuild' ); ?></h1>
			<p class="lead"><?php esc_html_e( 'Le guide di Olobuild, scritte per la versione installata. Ognuna si legge in pochi minuti e rimanda alle schermate che descrive.', 'olobuild' ); ?></p>
		</header>
		<div class="olo-guida-cards">
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
				<span><?php esc_html_e( "Scrivi al supporto. L'email parte già con la versione di Olobuild, WordPress e PHP, così possiamo rispondere senza chiedertele.", 'olobuild' ); ?></span>
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
					<span><?php echo esc_html( sprintf( __( 'Scritta per Olobuild %s', 'olobuild' ), self::VERIFICATA ) ); ?></span>
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
