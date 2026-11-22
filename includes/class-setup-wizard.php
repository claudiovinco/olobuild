<?php
/**
 * Olobuild Setup Wizard — first-run experience.
 * Auto-installs Hello Olobuild theme and offers theme import.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Olobuild_Setup_Wizard {

    public function init() {
        // Register wizard page (no auto-redirect to avoid potential loops on restrictive installs)
        add_action( 'admin_menu', [ $this, 'register_wizard_page' ] );

        // Render the wizard as a full-screen document BEFORE WordPress prints the admin
        // chrome (admin bar / header / sidebar). Without this the wizard's complete HTML
        // document gets nested inside the admin page → broken layout. Pattern: WooCommerce setup.
        add_action( 'admin_init', [ $this, 'maybe_takeover' ], 1 );

        // Show activation notice with link to wizard (safer than auto-redirect)
        add_action( 'admin_notices', [ $this, 'show_activation_notice' ] );
        add_action( 'admin_init', [ $this, 'maybe_dismiss_welcome' ], 3 );

        // AJAX handlers
        add_action( 'wp_ajax_olobuild_setup_install_theme', [ $this, 'ajax_install_theme' ] );
        add_action( 'wp_ajax_olobuild_setup_import_theme', [ $this, 'ajax_import_theme' ] );
        add_action( 'wp_ajax_olobuild_setup_plugin_richiesto', [ $this, 'ajax_plugin_richiesto' ] );
        add_action( 'wp_ajax_olobuild_setup_skip', [ $this, 'ajax_skip' ] );
        add_action( 'wp_ajax_olobuild_setup_blank_starter', [ $this, 'ajax_blank_starter' ] );
        add_action( 'wp_ajax_olobuild_setup_rete', [ $this, 'ajax_rete' ] );

        // First-run: redirect una-tantum al wizard dopo l'attivazione (pattern WooCommerce).
        // Niente rischio loop: il transient viene cancellato PRIMA del redirect, quindi
        // qualunque richiesta successiva non rientra qui. Il notice resta come fallback
        // per i casi in cui il redirect viene saltato (bulk activate, AJAX, multisite).
        add_action( 'admin_init', [ $this, 'maybe_first_run_redirect' ], 2 );
    }

    /**
     * Take over the request and render the wizard full-screen, before the admin chrome.
     */
    public function maybe_takeover() {
        if ( wp_doing_ajax() ) return;
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per routing pagina admin; nessuna modifica di stato; valore sanitizzato.
        $page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
        if ( $page !== 'olo-setup' ) return;
        if ( ! current_user_can( 'manage_options' ) ) return;
        $this->render_wizard(); // prints a full HTML document and exits
    }

    /**
     * One-time redirect to the wizard right after plugin activation.
     */
    public function maybe_first_run_redirect() {
        if ( ! get_transient( 'olo_activating' ) ) return;
        delete_transient( 'olo_activating' ); // prima del redirect: mai due volte, mai loop

        if ( wp_doing_ajax() || wp_doing_cron() || is_network_admin() ) return;
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only (sola presenza chiave) per routing redirect attivazione; nessuna modifica di stato.
        if ( isset( $_GET['activate-multi'] ) ) return; // attivazione bulk: solo notice
        if ( ! current_user_can( 'manage_options' ) ) return;
        if ( get_option( 'olobuild_setup_complete' ) ) return;

        wp_safe_redirect( admin_url( 'admin.php?page=olo-setup' ) );
        exit;
    }

    /**
     * "Più tardi": nasconde il pannello di benvenuto in modo persistente (option).
     */
    public function maybe_dismiss_welcome() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- gate di presenza; il nonce 'olo_welcome_dismiss' è verificato sotto con wp_verify_nonce prima di update_option.
        if ( ! isset( $_GET['olo_welcome_dismiss'] ) ) return;
        if ( ! current_user_can( 'manage_options' ) ) return;
        $nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 'olo_welcome_dismiss' ) ) return;

        update_option( 'olobuild_welcome_dismissed', 1, false );
        wp_safe_redirect( remove_query_arg( [ 'olo_welcome_dismiss', '_wpnonce' ] ) );
        exit;
    }

    /**
     * Welcome hero post-attivazione: pannello brandizzato con CTA al wizard.
     * Mostrato solo in Bacheca e nella pagina Plugin finché il setup non è
     * completato (o finché l'utente non sceglie "Più tardi").
     */
    public function show_activation_notice() {
        if ( ! current_user_can( 'manage_options' ) ) return;
        if ( get_option( 'olobuild_setup_complete' ) || get_option( 'olobuild_welcome_dismissed' ) ) return;
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per nascondere il notice nella pagina del wizard; nessuna modifica di stato; valore sanitizzato.
        $current_page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
        if ( $current_page === 'olo-setup' ) return;

        $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
        if ( $screen && ! in_array( $screen->id, [ 'dashboard', 'plugins' ], true ) ) return;

        $wizard_url  = admin_url( 'admin.php?page=olo-setup' );
        $dismiss_url = wp_nonce_url( add_query_arg( 'olo_welcome_dismiss', '1' ), 'olo_welcome_dismiss' );
        $logo_url    = apply_filters( 'olobuild_brand_logo_url', OLOBUILD_URL . 'assets/img/olobuild-logo-200-white.png' );
        $brand       = apply_filters( 'olobuild_brand_name', 'Olobuild' );
        ?>
        <style>
            .olo-welcome{position:relative;overflow:hidden;margin:20px 20px 20px 2px;padding:0;border:0;border-radius:14px;
                background:linear-gradient(135deg,#0F172A 0%,#1E293B 60%,#2A1220 100%);box-shadow:0 12px 32px rgba(15,23,42,.28);}
            .olo-welcome::before{content:"";position:absolute;right:-90px;top:-90px;width:320px;height:320px;border-radius:50%;
                background:radial-gradient(circle,rgba(225,71,79,.38),transparent 70%);pointer-events:none;}
            .olo-welcome__inner{position:relative;display:flex;flex-wrap:wrap;align-items:center;gap:24px 32px;padding:30px 36px;}
            .olo-welcome__logo{height:44px;width:auto;flex:0 0 auto;}
            .olo-welcome__text{flex:1 1 340px;min-width:260px;}
            .olo-welcome__text h2{margin:0 0 7px;font-size:22px;line-height:1.25;color:#F8FAFC;}
            .olo-welcome__text p{margin:0 0 10px;font-size:14px;line-height:1.6;color:#CBD5E1;max-width:56em;}
            .olo-welcome__steps{margin:0;font-size:12.5px;color:#94A3B8;letter-spacing:.2px;}
            .olo-welcome__steps b{color:#E2E8F0;font-weight:600;}
            .olo-welcome__actions{flex:0 0 auto;display:flex;align-items:center;gap:18px;}
            .olo-welcome__cta{display:inline-block;padding:13px 28px;background:#e1474f;color:#fff !important;border-radius:9px;
                font-size:14.5px;font-weight:600;text-decoration:none;box-shadow:0 6px 18px rgba(225,71,79,.42);
                transition:transform .15s ease,box-shadow .15s ease;}
            .olo-welcome__cta:hover{transform:translateY(-1px);box-shadow:0 9px 22px rgba(225,71,79,.5);}
            .olo-welcome__cta:focus-visible{outline:2px solid #fff;outline-offset:3px;}
            .olo-welcome__later{color:#94A3B8 !important;font-size:13px;text-decoration:none;}
            .olo-welcome__later:hover{color:#CBD5E1 !important;}
            .olo-welcome__later:focus-visible{outline:2px solid #94A3B8;outline-offset:3px;border-radius:4px;}
        </style>
        <div class="notice olo-welcome">
            <div class="olo-welcome__inner">
                <img class="olo-welcome__logo" src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $brand ); ?>">
                <div class="olo-welcome__text">
                    <h2><?php echo esc_html( sprintf( /* translators: %s = nome del brand */ __( 'Ti diamo il benvenuto in %s!', 'olobuild' ), $brand ) ); ?></h2>
                    <p><?php esc_html_e( 'Il tuo page builder è installato e pronto. Ti accompagniamo nei primi passi: in un paio di minuti il sito prende già forma.', 'olobuild' ); ?></p>
                    <p class="olo-welcome__steps">
                        <b>1</b> <?php esc_html_e( 'Scegli un tema', 'olobuild' ); ?> &nbsp;·&nbsp;
                        <b>2</b> <?php esc_html_e( 'Importa i contenuti demo', 'olobuild' ); ?> &nbsp;·&nbsp;
                        <b>3</b> <?php esc_html_e( 'Personalizza e pubblica', 'olobuild' ); ?>
                    </p>
                </div>
                <div class="olo-welcome__actions">
                    <a class="olo-welcome__cta" href="<?php echo esc_url( $wizard_url ); ?>"><?php esc_html_e( 'Avvia la configurazione', 'olobuild' ); ?></a>
                    <a class="olo-welcome__later" href="<?php echo esc_url( $dismiss_url ); ?>"><?php esc_html_e( 'Più tardi', 'olobuild' ); ?></a>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Register hidden admin page for the wizard.
     */
    public function register_wizard_page() {
        // Use 'options.php' as parent to avoid WP 6.x "Unauthorized" bug with parent=null
        // The page is still hidden from the menu (no visible link)
        add_submenu_page(
            'options.php',
            __( 'Olobuild Setup', 'olobuild' ),
            __( 'Olobuild Setup', 'olobuild' ),
            'manage_options',
            'olo-setup',
            [ $this, 'render_wizard' ]
        );
    }

    /**
     * I software della rete OLOtheme che la procedura propone (passo «Rete OLOtheme»), con lo stato sul sito. La
     * scelta si ricorda (option olobuild_rete_richiesti); quelli che la licenza comprende si installano subito dal
     * nostro server (1.4.1345, vedi installa_da_olotheme). OLOsocial e OLOlottie non ci sono: senza pagina pubblica.
     */
    public static function rete() {
        $voci = [
            'ololang'     => [ 'nome' => 'OLOlang', 'cosa' => __( 'Lingue', 'olobuild' ),
                'testo' => __( 'Il sito in più lingue: pagine, menu e testi tradotti da un pannello solo.', 'olobuild' ) ],
            'olosecurity' => [ 'nome' => 'OLOsecurity', 'cosa' => __( 'Sicurezza', 'olobuild' ),
                'testo' => __( 'Accessi protetti, verifica in due passaggi, controllo dei file e un firewall leggero, con i dati sul tuo sito.', 'olobuild' ) ],
            'olobooking'  => [ 'nome' => 'OLObooking', 'cosa' => __( 'Prenotazioni', 'olobuild' ),
                'testo' => __( 'Camere, tavoli, appuntamenti, eventi, noleggi e visite agli immobili, con calendario e pagamenti.', 'olobuild' ) ],
            'olotour'     => [ 'nome' => 'OLOtour', 'cosa' => __( 'Tour virtuali', 'olobuild' ),
                'testo' => __( 'Tour a 360° e ambienti 3D da visitare, anche col visore e con le visite guidate dal vivo.', 'olobuild' ) ],
            'olotutor'    => [ 'nome' => 'OLOtutor', 'cosa' => __( 'Corsi online', 'olobuild' ),
                'testo' => __( 'Corsi e lezioni con giochi, quiz, badge e certificati, tutto dentro WordPress.', 'olobuild' ) ],
        ];
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $piatto  = static function ( $s ) { return preg_replace( '/[^a-z]/', '', strtolower( (string) $s ) ); };
        $plugins = get_plugins();
        foreach ( $voci as $k => &$v ) {
            $v['url']   = 'https://olotheme.com/' . $k . '/';
            $v['logo']  = OLOBUILD_URL . 'assets/img/rete/' . $k . '-w.png';
            $v['stato'] = '';
            foreach ( $plugins as $file => $dati ) {
                // la cartella (olo-lang, olo-booking…) o il nome (OLOlang…) del plugin, senza trattini né spazi
                if ( 0 !== strpos( $piatto( dirname( $file ) ), $k ) && 0 !== strpos( $piatto( $dati['Name'] ?? '' ), $k ) ) continue;
                $v['stato'] = is_plugin_active( $file ) ? 'attivo' : 'installato';
                if ( 'attivo' === $v['stato'] ) break;
            }
        }
        unset( $v );
        return $voci;
    }

    /**
     * Render the setup wizard page.
     */
    public function render_wizard() {
        $theme_installed = wp_get_theme( 'hello-olobuild' )->exists();
        $theme_active    = get_stylesheet() === 'hello-olobuild';

        // Get available Olobuild themes (con campi visivi per le mini-anteprime del picker)
        require_once OLOBUILD_PATH . 'includes/class-theme-importer.php';
        $themes = Olobuild_Theme_Importer::get_themes();

        $brand = apply_filters( 'olobuild_brand_name', 'Olobuild' );
        // Con l'etichetta bianca (un altro nome al posto di Olobuild) la procedura non propone i software della rete.
        $con_rete = (bool) apply_filters( 'olobuild_setup_rete', 'Olobuild' === $brand );
        $rete     = $con_rete ? self::rete() : [];
        $scelti   = (array) get_option( 'olobuild_rete_richiesti', [] );
        $logo     = 'Olobuild' === $brand ? OLOBUILD_URL . 'assets/img/rete/olobuild-w.png'
            : apply_filters( 'olobuild_brand_logo_url', OLOBUILD_URL . 'assets/img/olobuild-logo-200-white.png' );

        $passi = [
            'tema'   => [ __( 'Tema WordPress', 'olobuild' ), 'Hello Olobuild' ],
            'design' => [ __( 'Design', 'olobuild' ), __( 'Un tema pronto', 'olobuild' ) ],
        ];
        if ( $con_rete ) $passi['rete'] = [ __( 'Rete OLOtheme', 'olobuild' ), __( 'Altri software', 'olobuild' ) ];
        $passi['pronto'] = [ __( 'Pronto', 'olobuild' ), __( 'Il tuo sito', 'olobuild' ) ];
        $n_passi = count( $passi );
        $numero  = array_flip( array_keys( $passi ) );
        $di      = static function ( $k ) use ( $numero, $n_passi ) {
            /* translators: 1: numero del passo, 2: quanti passi in tutto */
            return sprintf( __( 'Passo %1$d di %2$d', 'olobuild' ), $numero[ $k ] + 1, $n_passi );
        };

        // I caratteri della famiglia grafica (Inter, Fraunces per i titoli) ospitati sul sito; senza rete restano
        // quelli di sistema.
        $font_css = class_exists( 'Olobuild_Font_Host' ) ? Olobuild_Font_Host::get_font_face_css( [ 'Inter', 'Fraunces' ], '400;500;600;700' ) : '';
        wp_register_style( 'olo-setup-wizard', OLOBUILD_URL . 'assets/css/setup-wizard.css', [], OLOBUILD_VERSION );

        $spunta = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>';
        $freccia = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo( 'charset' ); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title><?php echo esc_html( sprintf( /* translators: %s = nome del brand */ __( '%s — Configurazione iniziale', 'olobuild' ), $brand ) ); ?></title>
            <?php if ( $font_css ) : ?><style><?php echo $font_css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- regole @font-face generate dal plugin (URL locali). ?></style><?php endif; ?>
            <?php wp_print_styles( [ 'olo-setup-wizard' ] ); ?>
        </head>
        <body>
        <div class="ws">
            <aside class="ws-rail">
                <img class="ws-logo" src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( $brand ); ?>">
                <div class="ws-rail-t">
                    <span class="ws-kicker"><?php esc_html_e( 'Configurazione iniziale', 'olobuild' ); ?></span>
                    <p><?php esc_html_e( 'Pochi passi, un paio di minuti. Tutto si cambia anche dopo.', 'olobuild' ); ?></p>
                </div>
                <ol class="ws-steps">
                    <?php $i = 0; foreach ( $passi as $k => $p ) : $i++; ?>
                        <li data-step="<?php echo esc_attr( $k ); ?>">
                            <span class="ws-n"><i><?php echo (int) $i; ?></i><?php echo $spunta; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG fisso. ?></span>
                            <span><b><?php echo esc_html( $p[0] ); ?></b><small><?php echo esc_html( $p[1] ); ?></small></span>
                        </li>
                    <?php endforeach; ?>
                </ol>
                <div class="ws-rail-foot">
                    <a href="#" role="button" class="skip-link" onclick="skipSetup(); return false;"><?php esc_html_e( 'Salta configurazione', 'olobuild' ); ?></a>
                    <span class="ws-ver"><?php echo esc_html( $brand . ' ' . OLOBUILD_VERSION ); ?></span>
                </div>
            </aside>

            <main class="ws-main">
                <!-- Passo: il tema WordPress -->
                <section class="ws-step" id="step-tema" hidden>
                    <div class="ws-split">
                        <div>
                            <span class="ws-eyebrow"><?php echo esc_html( $di( 'tema' ) ); ?></span>
                            <h1 class="ws-title"><?php esc_html_e( 'Il tema WordPress', 'olobuild' ); ?></h1>
                            <?php if ( $theme_active ) : ?>
                                <p class="ws-lead"><?php esc_html_e( 'Hello Olobuild è già attivo: WordPress lascia tutto lo spazio a Olobuild.', 'olobuild' ); ?></p>
                                <div class="ws-actions">
                                    <button class="btn btn-primary" onclick="setStep('design')"><?php esc_html_e( 'Continua', 'olobuild' ); ?><?php echo $freccia; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG fisso. ?></button>
                                </div>
                            <?php elseif ( $theme_installed ) : ?>
                                <p class="ws-lead"><?php esc_html_e( 'Hello Olobuild è installato ma non attivo. È il tema leggero fatto apposta per Olobuild: non aggiunge stili suoi.', 'olobuild' ); ?></p>
                                <div class="ws-actions">
                                    <button class="btn btn-primary" id="btn-install" onclick="installTheme('activate')"><?php esc_html_e( 'Attiva Hello Olobuild', 'olobuild' ); ?></button>
                                    <button class="btn btn-secondary" onclick="temaWp = false; setStep('design')"><?php esc_html_e( 'Tieni il tema attuale', 'olobuild' ); ?></button>
                                </div>
                            <?php else : ?>
                                <p class="ws-lead"><?php echo wp_kses( __( 'Testata, pagine e piè di pagina li disegna Olobuild: al tema WordPress resta solo da lasciargli spazio. Installiamo <strong>Hello Olobuild</strong>, il tema leggero fatto apposta.', 'olobuild' ), [ 'strong' => [] ] ); ?></p>
                                <div class="ws-actions">
                                    <button class="btn btn-primary" id="btn-install" onclick="installTheme('install')"><?php esc_html_e( 'Installa e attiva', 'olobuild' ); ?></button>
                                    <button class="btn btn-secondary" onclick="temaWp = false; setStep('design')"><?php esc_html_e( 'Tieni il tema attuale', 'olobuild' ); ?></button>
                                </div>
                            <?php endif; ?>
                            <div class="status" id="status-tema" role="status"></div>
                        </div>
                        <div class="ws-fig" aria-hidden="true">
                            <span class="ws-fig-tag"><?php esc_html_e( 'Hello Olobuild: solo la cornice', 'olobuild' ); ?></span>
                            <div class="ws-fig-page">
                                <div class="ws-fig-b is-bar"><span><?php esc_html_e( 'Testata', 'olobuild' ); ?></span></div>
                                <div class="ws-fig-b is-hero"><span><?php esc_html_e( 'Pagine', 'olobuild' ); ?></span></div>
                                <div class="ws-fig-row"><i></i><i></i><i></i></div>
                                <div class="ws-fig-b is-foot"><span><?php esc_html_e( 'Piè di pagina', 'olobuild' ); ?></span></div>
                            </div>
                            <p class="ws-fig-cap"><i></i><?php echo esc_html( sprintf( /* translators: %s = nome del brand */ __( 'Lo disegna %s', 'olobuild' ), $brand ) ); ?></p>
                        </div>
                    </div>
                </section>

                <!-- Passo: il design (selettore dei temi) -->
                <section class="ws-step" id="step-design" hidden>
                    <div class="step-head">
                        <div>
                            <span class="ws-eyebrow"><?php echo esc_html( $di( 'design' ) ); ?></span>
                            <h1 class="ws-title"><?php esc_html_e( 'Scegli un design', 'olobuild' ); ?></h1>
                            <p class="ws-lead"><?php esc_html_e( 'Ogni tema è un sito completo: testata, piè di pagina, pagine e menu. Dopo cambi tutto dal builder.', 'olobuild' ); ?></p>
                        </div>
                        <button class="btn btn-primary" id="btn-import" onclick="importTheme()" disabled><?php esc_html_e( 'Scegli un tema dall’elenco', 'olobuild' ); ?></button>
                    </div>
                    <div class="ws-pick">
                        <div class="wizard-picker" id="olo-wizard-picker"><!-- montato da OloThemePicker (embed) --></div>
                        <div class="ws-busy" id="ws-busy" hidden>
                            <div class="ws-busy-in">
                                <span class="spinner"></span>
                                <b class="ws-busy-t" id="ws-busy-t"></b>
                                <span class="ws-busy-s" id="ws-busy-s"></span>
                                <i class="ws-bar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="status" id="status-design" role="status"></div>
                </section>

                <?php if ( $con_rete ) : ?>
                <!-- Passo: i software della rete OLOtheme -->
                <section class="ws-step" id="step-rete" hidden>
                    <span class="ws-eyebrow"><?php echo esc_html( $di( 'rete' ) ); ?></span>
                    <h1 class="ws-title"><?php esc_html_e( 'Ti serve altro?', 'olobuild' ); ?></h1>
                    <p class="ws-lead"><?php esc_html_e( 'Olobuild fa parte della rete OLOtheme: software che lavorano insieme sullo stesso sito, con lo stesso stile. Segna quelli che ti servono.', 'olobuild' ); ?></p>
                    <div class="ws-nets">
                        <?php foreach ( $rete as $k => $v ) : $attivo = 'attivo' === $v['stato']; ?>
                            <label class="ws-net<?php echo $attivo ? ' is-attivo' : ''; ?>">
                                <input type="checkbox" name="rete" value="<?php echo esc_attr( $k ); ?>" data-nome="<?php echo esc_attr( $v['nome'] ); ?>"<?php checked( $attivo || in_array( $k, $scelti, true ) ); ?><?php disabled( $attivo ); ?>>
                                <img class="ws-net-logo" src="<?php echo esc_url( $v['logo'] ); ?>" alt="<?php echo esc_attr( $v['nome'] ); ?>">
                                <span class="ws-net-t"><b><?php echo esc_html( $v['cosa'] ); ?></b><span><?php echo esc_html( $v['testo'] ); ?></span></span>
                                <span class="ws-net-st">
                                    <?php if ( $attivo ) : ?>
                                        <span class="ws-net-on"><?php esc_html_e( 'Già attivo', 'olobuild' ); ?></span>
                                    <?php else : ?>
                                        <?php if ( 'installato' === $v['stato'] ) : ?><?php esc_html_e( 'Installato', 'olobuild' ); ?> · <?php endif; ?>
                                        <a class="ws-net-link" href="<?php echo esc_url( $v['url'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Scopri', 'olobuild' ); ?></a>
                                    <?php endif; ?>
                                </span>
                                <span class="ws-sw" aria-hidden="true"></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <p class="ws-note">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg>
                        <span><?php esc_html_e( 'OLOsecurity e, con la Pro, OLOlang si installano subito quando premi Continua. Per gli altri la scelta resta segnata e trovi il link alla loro pagina.', 'olobuild' ); ?></span>
                    </p>
                    <div class="ws-actions">
                        <button class="btn btn-primary" id="btn-rete" onclick="salvaRete()"><?php esc_html_e( 'Continua', 'olobuild' ); ?><?php echo $freccia; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG fisso. ?></button>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Passo: pronto -->
                <section class="ws-step" id="step-pronto" hidden>
                    <span class="ws-ok"><?php echo $spunta; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG fisso. ?></span>
                    <h1 class="ws-title"><?php esc_html_e( 'Tutto pronto!', 'olobuild' ); ?></h1>
                    <p class="ws-lead" id="step-3-intro"><?php esc_html_e( 'Il tuo sito è configurato. Puoi iniziare a personalizzare tutto dal builder visuale.', 'olobuild' ); ?></p>
                    <ul class="ws-done" id="ws-riepilogo"></ul>

                    <!-- Quick-start cards (popolate dinamicamente quando si crea uno starter blank) -->
                    <div class="quick-cards" id="quick-cards" hidden>
                        <a href="#" id="card-header" class="quick-card">
                            <span class="quick-card-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="6" rx="1"/><line x1="3" y1="13" x2="21" y2="13" opacity="0.4"/><line x1="3" y1="17" x2="21" y2="17" opacity="0.4"/></svg>
                            </span>
                            <span>
                                <strong><?php esc_html_e( 'Personalizza la testata', 'olobuild' ); ?></strong>
                                <small><?php esc_html_e( 'Logo e menu già pronti — apri il builder', 'olobuild' ); ?></small>
                            </span>
                        </a>
                        <a href="#" id="card-footer" class="quick-card">
                            <span class="quick-card-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="7" x2="21" y2="7" opacity="0.4"/><line x1="3" y1="11" x2="21" y2="11" opacity="0.4"/><rect x="3" y="15" width="18" height="6" rx="1"/></svg>
                            </span>
                            <span>
                                <strong><?php esc_html_e( 'Personalizza il piè di pagina', 'olobuild' ); ?></strong>
                                <small><?php esc_html_e( 'Logo, copyright e link rapidi', 'olobuild' ); ?></small>
                            </span>
                        </a>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=olobuild' ) ); ?>" id="card-home" class="quick-card">
                            <span class="quick-card-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12L12 4l9 8"/><path d="M5 10v10h14V10"/></svg>
                            </span>
                            <span>
                                <strong><?php esc_html_e( 'Personalizza la home', 'olobuild' ); ?></strong>
                                <small><?php esc_html_e( 'Hero starter pronto da modificare', 'olobuild' ); ?></small>
                            </span>
                        </a>
                    </div>

                    <div class="ws-actions">
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary" id="btn-sito" target="_blank" rel="noopener"><?php esc_html_e( 'Vedi il sito', 'olobuild' ); ?><?php echo $freccia; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG fisso. ?></a>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=olobuild' ) ); ?>" class="btn btn-secondary"><?php echo esc_html( sprintf( /* translators: %s = nome del brand */ __( 'Apri %s', 'olobuild' ), $brand ) ); ?></a>
                    </div>
                </section>
            </main>
        </div>

            <?php
            // Pipeline thumbnail (render REST → html2canvas → upload): theme-picker e
            // thumb-capture registrati via wp_enqueue_script ed emessi qui con
            // wp_print_scripts (questa è una pagina takeover full-page, niente admin_footer).
            wp_enqueue_script( 'olo-wizard-theme-picker', OLOBUILD_URL . 'assets/js/theme-picker.js', [], OLOBUILD_VERSION, true );
            wp_enqueue_script( 'olo-wizard-thumb-capture', OLOBUILD_URL . 'assets/js/olo-thumb-capture.js', [], OLOBUILD_VERSION, true );
            wp_add_inline_script( 'olo-wizard-thumb-capture', 'window.oloThumbConfig=' . wp_json_encode( [
                'restUrl'   => esc_url_raw( rest_url( 'olobuild/v1/' ) ),
                'nonce'     => wp_create_nonce( 'wp_rest' ),
                'vendorUrl' => OLOBUILD_URL . 'assets/vendor/html2canvas.min.js',
                'debug'     => false,
            ] ) . ';', 'before' );
            wp_print_scripts( [ 'olo-wizard-theme-picker', 'olo-wizard-thumb-capture' ] );
            ?>
            <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline JS below only outputs wp_json_encode()'d JSON strings, a wp_create_nonce() token, esc_url()'d admin URLs and fixed literals. ?>
            <script>
            var oloWizardThemes = <?php echo wp_json_encode( array_map( function ( $t ) { unset( $t['dir'] ); return $t; }, $themes ) ); ?>;
            var nonce = '<?php echo wp_create_nonce( 'olo_setup' ); ?>';
            var ajaxurl = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
            var PASSI = <?php echo wp_json_encode( array_keys( $passi ) ); ?>;
            var SPUNTA = <?php echo wp_json_encode( $spunta ); ?>;
            var selectedThemeId = null;
            // per il riepilogo dell'ultimo passo
            var temaWp = <?php echo $theme_active ? 'true' : 'null'; ?>, temaImportato = null, partenzaVuota = false, reteScelta = [], reteInstallati = [], reteErrori = [];
            var oloSetupI18n = {
                installing: <?php echo wp_json_encode( __( 'Installazione...', 'olobuild' ) ); ?>,
                installingProgress: <?php echo wp_json_encode( __( 'Installazione in corso...', 'olobuild' ) ); ?>,
                error: <?php echo wp_json_encode( __( 'Errore', 'olobuild' ) ); ?>,
                connectionError: <?php echo wp_json_encode( __( 'Errore di connessione', 'olobuild' ) ); ?>,
                retry: <?php echo wp_json_encode( __( 'Riprova', 'olobuild' ) ); ?>,
                importing: <?php echo wp_json_encode( __( 'Importazione...', 'olobuild' ) ); ?>,
                creatingStarter: <?php echo wp_json_encode( __( 'Creo header e footer base...', 'olobuild' ) ); ?>,
                starterReady: <?php echo wp_json_encode( __( 'Header e footer base pronti!', 'olobuild' ) ); ?>,
                starterIntro: <?php echo wp_json_encode( __( 'Ho creato un header con il logo del sito e un footer con copyright e link rapidi. Personalizzali con un click:', 'olobuild' ) ); ?>,
                creatingTemplates: <?php echo wp_json_encode( __( 'Creo le pagine, il menu, la testata e il piè di pagina, e copio le foto nella Libreria media…', 'olobuild' ) ); ?>,
                /* translators: %s: nome del tema scelto */
                importNamed: <?php echo wp_json_encode( __( 'Importa «%s»', 'olobuild' ) ); ?>,
                /* translators: %s: nome del tema che si sta importando */
                importingNamed: <?php echo wp_json_encode( __( 'Importo «%s»', 'olobuild' ) ); ?>,
                startBlank: <?php echo wp_json_encode( __( 'Parti da zero', 'olobuild' ) ); ?>,
                /* translators: %s: nome del tema importato */
                themeReady: <?php echo wp_json_encode( __( 'Il tema «%s» è nel tuo sito: pagine, menu, testata e piè di pagina sono già al loro posto. Testi, foto e colori li cambi dal builder.', 'olobuild' ) ); ?>,
                sumWp: <?php echo wp_json_encode( __( 'Hello Olobuild è il tema di WordPress', 'olobuild' ) ); ?>,
                /* translators: %s: nome del tema importato */
                sumTheme: <?php echo wp_json_encode( __( 'Il tema «%s»: pagine, menu, testata e piè di pagina', 'olobuild' ) ); ?>,
                /* translators: %s: i software della rete scelti, separati da virgole */
                sumNet: <?php echo wp_json_encode( __( 'Segnati per dopo: %s', 'olobuild' ) ); ?>,
                sumInst: <?php echo wp_json_encode( __( 'Installati e attivati: %s', 'olobuild' ) ); ?>,
                /* translators: %s: nome del plugin che il tema richiede (WooCommerce) */
                installingPlugin: <?php echo wp_json_encode( __( 'Installo e attivo %s…', 'olobuild' ) ); ?>,
                templatesCreated: <?php echo wp_json_encode( /* translators: %d: number of templates created */ __( '%d template creati, homepage impostata!', 'olobuild' ) ); ?>
            };
            var oloBuilderBase = <?php echo wp_json_encode( admin_url( 'admin.php?page=olobuilder-templates&template_id=' ) ); ?>;

            function setStep(nome) {
                var i = PASSI.indexOf(nome);
                if (i < 0) { nome = 'pronto'; i = PASSI.indexOf(nome); }
                PASSI.forEach(function (p, k) {
                    var sec = document.getElementById('step-' + p);
                    if (sec) sec.hidden = p !== nome;
                    var li = document.querySelector('.ws-steps [data-step="' + p + '"]');
                    if (!li) return;
                    li.classList.toggle('is-active', k === i);
                    li.classList.toggle('is-done', k < i);
                    if (k === i) li.setAttribute('aria-current', 'step'); else li.removeAttribute('aria-current');
                });
                document.body.classList.toggle('is-wide', nome === 'design');
                // a configurazione finita «Salta configurazione» non ha più niente da saltare
                var salta = document.querySelector('.skip-link');
                if (salta) salta.hidden = nome === 'pronto';
                var main = document.querySelector('.ws-main');
                if (main) main.scrollTop = 0;
                if (nome === 'pronto') riepilogo();
            }
            // dopo il design: la rete, se c'è, poi pronto
            function dopoDesign() { setStep(PASSI.indexOf('rete') >= 0 ? 'rete' : 'pronto'); }
            function nextStep(n) { setStep(n); }

            function installTheme(action) {
                var btn = document.getElementById('btn-install');
                var status = document.getElementById('status-tema');
                if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner"></span> ' + oloSetupI18n.installing; }
                status.textContent = oloSetupI18n.installingProgress;

                fetch(ajaxurl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=olobuild_setup_install_theme&_nonce=' + nonce + '&mode=' + action
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        temaWp = true;
                        status.innerHTML = '<span class="check">✓</span> ' + data.data.message;
                        setTimeout(function() { setStep('design'); }, 900);
                    } else {
                        status.textContent = '⚠ ' + (data.data || oloSetupI18n.error);
                        if (btn) { btn.disabled = false; btn.textContent = oloSetupI18n.retry; }
                    }
                })
                .catch(function(e) { status.textContent = '⚠ ' + oloSetupI18n.connectionError; });
            }

            // Selettore temi = picker condiviso (stesso del builder), in modalità embed.
            (function mountThemePicker() {
                var host = document.getElementById('olo-wizard-picker');
                if (!host) return;
                if (!window.OloThemePicker) {
                    host.innerHTML = '<p style="color:#a1959a;font-size:14px;padding:24px;text-align:center">'
                        + <?php echo wp_json_encode( __( 'Impossibile caricare il selettore temi.', 'olobuild' ) ); ?> + '</p>';
                    return;
                }
                window.OloThemePicker.create({
                    mode: 'embed',
                    // la procedura è una pagina scura: il selettore (chiaro nel builder) usa la serie scura
                    tema: 'scuro',
                    target: host,
                    themes: oloWizardThemes,
                    card: { action: 'select' },
                    blank: {
                        id: 'blank',
                        name: <?php echo wp_json_encode( __( 'Vuoto', 'olobuild' ) ); ?>,
                        desc: <?php echo wp_json_encode( __( 'Parti da zero, tela bianca.', 'olobuild' ) ); ?>
                    },
                    i18n: <?php echo wp_json_encode( [
                        'title'                => __( 'Temi Olobuild', 'olobuild' ),
                        'importLabel'          => __( 'Importa tema', 'olobuild' ),
                        'searchPlaceholder'    => __( 'Cerca un tema…', 'olobuild' ),
                        'categoriesLabel'      => __( 'Categorie', 'olobuild' ),
                        'allLabel'             => __( 'Tutti i temi', 'olobuild' ),
                        'detailsLabel'         => __( 'Dettagli', 'olobuild' ),
                        'loading'              => __( 'Caricamento temi…', 'olobuild' ),
                        'emptyFiltered'        => __( 'Nessun tema per questi filtri.', 'olobuild' ),
                        'emptyNone'            => __( 'Nessun tema disponibile.', 'olobuild' ),
                        'loadError'            => __( 'Non è stato possibile caricare i temi.', 'olobuild' ),
                        'themeWord'            => __( 'tema', 'olobuild' ),
                        'themesWord'           => __( 'temi', 'olobuild' ),
                        'pageWord'             => __( 'pagina', 'olobuild' ),
                        'pagesWord'            => __( 'pagine', 'olobuild' ),
                        'requirementsLabel'    => __( 'Requisiti', 'olobuild' ),
                        /* translators: %s: nome del plugin richiesto dai temi (es. OLObooking) */
                        'withPluginLabel'      => __( 'Con %s', 'olobuild' ),
                        'onlyOlobuildLabel'    => __( 'Solo OLObuild', 'olobuild' ),
                        'reqOkLabel'           => __( 'attivo', 'olobuild' ),
                        'reqOldLabel'          => __( 'da aggiornare', 'olobuild' ),
                        'reqMissingLabel'      => __( 'da installare', 'olobuild' ),
                        /* translators: %s: nome del plugin */
                        'requiresTitle'        => __( 'Richiede %s', 'olobuild' ),
                        /* translators: 1: nome del plugin, 2: versione minima */
                        'requiresVersionTitle' => __( 'Richiede %1$s %2$s o successiva', 'olobuild' ),
                        /* translators: %s: nome del plugin */
                        'discoverLabel'        => __( 'Scopri %s', 'olobuild' ),
                        /* translators: %s: nome del plugin */
                        'updateLabel'          => __( 'Aggiorna %s', 'olobuild' ),
                        /* translators: %s: nome del plugin */
                        'importWithLabel'      => __( 'Importa con %s', 'olobuild' ),
                        /* translators: %s: nome del plugin */
                        'installsTitle'        => __( 'Si installa da sé: %s arriva da wordpress.org quando importi il tema', 'olobuild' ),
                    ] ); ?>,
                    onSelect: function (id, tema) {
                        selectedThemeId = id;
                        var b = document.getElementById('btn-import');
                        if (!b) return;
                        b.disabled = false;
                        // il pulsante dice cosa succede: «Importa «Mano Libera»», o «Parti da zero» per la tela bianca
                        b.textContent = id === 'blank' ? oloSetupI18n.startBlank
                            : oloSetupI18n.importNamed.replace('%s', (tema && tema.name) || id);
                    }
                });
            })();

            function attesa(si, titolo) {
                var b = document.getElementById('ws-busy');
                if (!b) return;
                b.hidden = !si;
                if (si) {
                    document.getElementById('ws-busy-t').textContent = titolo || '';
                    document.getElementById('ws-busy-s').textContent = oloSetupI18n.creatingTemplates;
                }
            }
            function nomeScelto() {
                var t = oloWizardThemes.filter(function (x) { return x.id === selectedThemeId; })[0];
                return (t && t.name) || selectedThemeId;
            }

            function importTheme() {
                if (!selectedThemeId) return;
                var btn = document.getElementById('btn-import');
                var status = document.getElementById('status-design');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner"></span> ' + oloSetupI18n.importing;

                if (selectedThemeId === 'blank') {
                    status.textContent = oloSetupI18n.creatingStarter;
                    fetch(ajaxurl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'action=olobuild_setup_blank_starter&_nonce=' + nonce
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.success) {
                            status.innerHTML = '<span class="check">✓</span> ' + oloSetupI18n.starterReady;
                            partenzaVuota = true;
                            // Popola le card dell'ultimo passo con i link contestuali
                            var d = data.data || {};
                            if (d.header_id) {
                                var ch = document.getElementById('card-header');
                                if (ch) ch.href = oloBuilderBase + d.header_id;
                            }
                            if (d.footer_id) {
                                var cf = document.getElementById('card-footer');
                                if (cf) cf.href = oloBuilderBase + d.footer_id;
                            }
                            if (d.home_id) {
                                var ch2 = document.getElementById('card-home');
                                if (ch2) ch2.href = oloBuilderBase + d.home_id;
                            }
                            var intro = document.getElementById('step-3-intro');
                            if (intro) intro.textContent = oloSetupI18n.starterIntro;
                            var cards = document.getElementById('quick-cards');
                            if (cards) cards.hidden = false;
                            setTimeout(dopoDesign, 800);
                        } else {
                            status.textContent = '⚠ ' + (data.data || oloSetupI18n.error);
                            btn.disabled = false;
                            btn.textContent = oloSetupI18n.retry;
                        }
                    })
                    .catch(function(e) { status.textContent = '⚠ ' + oloSetupI18n.connectionError; });
                    return;
                }

                // Il tema richiede un plugin che manca ma si installa da qui (WooCommerce): prima quello, poi l'import.
                var tema = oloWizardThemes.filter(function (x) { return x.id === selectedThemeId; })[0];
                var req = tema ? tema.requires_plugin : null;
                var daInstallare = !!req;
                if (daInstallare) daInstallare = !!req.installabile;
                if (daInstallare) daInstallare = tema.requires_plugin_status === 'missing';
                if (daInstallare) {
                    attesa(true, oloSetupI18n.importingNamed.replace('%s', nomeScelto()));
                    document.getElementById('ws-busy-s').textContent = oloSetupI18n.installingPlugin.replace('%s', req.label || req.slug);
                    fetch(ajaxurl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'action=olobuild_setup_plugin_richiesto&_nonce=' + nonce + '&theme_id=' + encodeURIComponent(selectedThemeId)
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (!data.success) {
                            attesa(false);
                            status.textContent = '⚠ ' + (data.data || oloSetupI18n.error);
                            btn.disabled = false;
                            btn.textContent = oloSetupI18n.retry;
                            return;
                        }
                        tema.requires_plugin_status = 'ok';
                        importaTema();
                    })
                    .catch(function(e) {
                        attesa(false);
                        status.textContent = '⚠ ' + oloSetupI18n.connectionError;
                        btn.disabled = false;
                        btn.textContent = oloSetupI18n.retry;
                    });
                    return;
                }
                importaTema();
            }

            function importaTema() {
                var btn = document.getElementById('btn-import');
                var status = document.getElementById('status-design');
                status.textContent = '';
                attesa(true, oloSetupI18n.importingNamed.replace('%s', nomeScelto()));

                fetch(ajaxurl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=olobuild_setup_import_theme&_nonce=' + nonce + '&theme_id=' + encodeURIComponent(selectedThemeId)
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        var d = data.data;
                        document.getElementById('ws-busy-s').innerHTML = '<span class="check">✓</span> ' + oloSetupI18n.templatesCreated.replace('%d', d.templates);
                        // Anteprime card in background (continua mentre l'utente è sui passi finali;
                        // le eventuali rimanenti le completa l'auto-heal della dashboard template).
                        if (window.oloGenerateMissingThumbs && d.template_ids && d.template_ids.length) {
                            window.oloGenerateMissingThumbs(d.template_ids);
                        }
                        temaImportato = nomeScelto();
                        var intro = document.getElementById('step-3-intro');
                        if (intro) intro.textContent = oloSetupI18n.themeReady.replace('%s', temaImportato);
                        setTimeout(function () { attesa(false); dopoDesign(); }, 1100);
                    } else {
                        attesa(false);
                        status.textContent = '⚠ ' + (data.data || oloSetupI18n.error);
                        btn.disabled = false;
                        btn.textContent = oloSetupI18n.retry;
                    }
                })
                .catch(function(e) {
                    attesa(false);
                    status.textContent = '⚠ ' + oloSetupI18n.connectionError;
                    btn.disabled = false;
                    btn.textContent = oloSetupI18n.retry;
                });
            }

            // I software della rete: si ricorda la scelta (lo scaricamento arriverà con la zona comune di OLOtheme).
            function salvaRete() {
                var scelti = [].slice.call(document.querySelectorAll('.ws-net input:checked:not(:disabled)'));
                reteScelta = scelti.map(function (c) { return c.getAttribute('data-nome'); });
                var btn = document.getElementById('btn-rete');
                if (btn) btn.disabled = true;
                var vai = function () { if (btn) btn.disabled = false; setStep('pronto'); };
                fetch(ajaxurl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=olobuild_setup_rete&_nonce=' + nonce + '&rete=' + encodeURIComponent(scelti.map(function (c) { return c.value; }).join(','))
                }).then(function (r) { return r.json(); }).then(function (j) {
                    // quelli installati adesso escono dai «segnati per dopo» ed entrano fra gli installati
                    var d = (j && j.data) || {};
                    reteInstallati = d.installati || [];
                    reteScelta = reteScelta.filter(function (n) { return reteInstallati.indexOf(n) < 0; });
                    reteErrori = d.errori || [];
                    vai();
                }, vai);
            }

            function riepilogo() {
                var ul = document.getElementById('ws-riepilogo');
                if (!ul) return;
                var voci = [];
                if (temaWp) voci.push(oloSetupI18n.sumWp);
                if (temaImportato) voci.push(oloSetupI18n.sumTheme.replace('%s', temaImportato));
                if (partenzaVuota) voci.push(oloSetupI18n.starterReady);
                if (reteInstallati.length) voci.push(oloSetupI18n.sumInst.replace('%s', reteInstallati.join(', ')));
                if (reteScelta.length) voci.push(oloSetupI18n.sumNet.replace('%s', reteScelta.join(', ')));
                reteErrori.forEach(function (e) { voci.push(e); });
                ul.innerHTML = '';
                voci.forEach(function (t) {
                    var li = document.createElement('li');
                    li.innerHTML = SPUNTA;
                    var s = document.createElement('span');
                    s.textContent = t;
                    li.appendChild(s);
                    ul.appendChild(li);
                });
                ul.hidden = !voci.length;
            }

            function markComplete() {
                fetch(ajaxurl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=olobuild_setup_skip&_nonce=' + nonce
                });
            }

            function skipSetup() {
                markComplete();
                window.location.href = '<?php echo esc_url( admin_url( 'admin.php?page=olobuild' ) ); ?>';
            }

            // Primo passo
            setStep(<?php echo wp_json_encode( $theme_active ? 'design' : 'tema' ); ?>);
            </script>
            <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </body>
        </html>
        <?php
        exit;
    }

    /**
     * AJAX: Install/activate Hello Olobuild theme.
     */
    public function ajax_install_theme() {
        check_ajax_referer( 'olo_setup', '_nonce' );
        if ( ! current_user_can( 'switch_themes' ) ) {
            wp_send_json_error( __( 'Permessi insufficienti.', 'olobuild' ) );
        }

        $mode = isset( $_POST['mode'] ) ? sanitize_text_field( wp_unslash( $_POST['mode'] ) ) : 'install';
        $theme_slug = 'hello-olobuild';
        $theme_dir  = get_theme_root() . '/' . $theme_slug;

        // Copy theme from plugin bundle if not installed
        if ( ! wp_get_theme( $theme_slug )->exists() ) {
            $source = OLOBUILD_PATH . 'includes/theme-bundle/';
            if ( ! is_dir( $source ) ) {
                wp_send_json_error( __( 'Bundle tema non trovato nel plugin.', 'olobuild' ) );
            }

            // Copy recursively
            $this->copy_dir( $source, $theme_dir );
        }

        // Activate theme
        switch_theme( $theme_slug );

        wp_send_json_success( [
            'message' => __( 'Hello Olobuild installato e attivato!', 'olobuild' ),
        ] );
    }

    /**
     * AJAX: Import a site theme.
     */
    public function ajax_import_theme() {
        check_ajax_referer( 'olo_setup', '_nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Permessi insufficienti.', 'olobuild' ) );
        }
        if ( olobuild_imports_disabled() ) {
            wp_send_json_error( __( 'L\'importazione di temi è disabilitata su questo sito.', 'olobuild' ) );
        }

        // importare un tema chiede la chiave di OLObuild (1.4.1345)
        $lic = class_exists( 'Olobuild_Licenza' ) ? Olobuild_Licenza::permesso() : true;
        if ( is_wp_error( $lic ) ) {
            wp_send_json_error( $lic->get_error_message() );
        }

        $theme_id = isset( $_POST['theme_id'] ) ? sanitize_text_field( wp_unslash( $_POST['theme_id'] ) ) : '';
        if ( empty( $theme_id ) ) {
            wp_send_json_error( __( 'Nessun tema selezionato.', 'olobuild' ) );
        }

        require_once OLOBUILD_PATH . 'includes/class-theme-importer.php';
        $result = Olobuild_Theme_Importer::import_theme( $theme_id );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( $result->get_error_message() );
        }

        // Mark setup as complete
        update_option( 'olobuild_setup_complete', true );

        wp_send_json_success( [
            'templates'    => count( $result['templates'] ?? [] ),
            'template_ids' => array_values( array_filter( array_column( $result['templates'] ?? [], 'id' ) ) ),
            'activated'    => $result['activated'] ?? [],
            'menu'         => $result['menu'] ?? null,
        ] );
    }

    /**
     * AJAX: installa e attiva il plugin che il tema scelto richiede (WooCommerce), prima dell'import.
     */
    public function ajax_plugin_richiesto() {
        check_ajax_referer( 'olo_setup', '_nonce' );
        if ( ! current_user_can( 'activate_plugins' ) ) {
            wp_send_json_error( __( 'Permessi insufficienti.', 'olobuild' ) );
        }
        if ( olobuild_imports_disabled() ) {
            wp_send_json_error( __( 'L\'importazione di temi è disabilitata su questo sito.', 'olobuild' ) );
        }
        $theme_id = isset( $_POST['theme_id'] ) ? sanitize_text_field( wp_unslash( $_POST['theme_id'] ) ) : '';
        require_once OLOBUILD_PATH . 'includes/class-theme-importer.php';
        $result = Olobuild_Theme_Importer::installa_plugin_richiesto( $theme_id );
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( $result->get_error_message() );
        }
        wp_send_json_success( $result );
    }

    /**
     * AJAX: Skip setup.
     */
    public function ajax_skip() {
        check_ajax_referer( 'olo_setup', '_nonce' );
        update_option( 'olobuild_setup_complete', true );
        wp_send_json_success();
    }

    /**
     * I software della rete che si scaricano dal nostro server (OLO Store) con la licenza di OLObuild, se la licenza
     * ha il loro modulo: [ voce => [ prodotto di OLO Store, modulo della licenza, file del plugin ] ].
     */
    const DA_OLOTHEME = [
        'olosecurity' => [ 'olosecurity', 'olosecurity', 'olosecurity/olosecurity.php' ],
        'ololang'     => [ 'olo-lang', 'ololang', 'olo-lang/olo-lang.php' ],
    ];

    /**
     * Installa e attiva un software della rete dal nostro server: OLO Store dà il pacchetto (link valido 24 ore) se la
     * licenza di questo sito lo comprende. true, o WP_Error col motivo per chi usa il sito.
     */
    public static function installa_da_olotheme( $voce ) {
        if ( ! isset( self::DA_OLOTHEME[ $voce ] ) || ! class_exists( 'Olobuild_Licenza' ) ) return new WP_Error( 'non_installabile', '' );
        list( $prodotto, $modulo, $file ) = self::DA_OLOTHEME[ $voce ];
        if ( ! in_array( $modulo, Olobuild_Licenza::moduli(), true ) ) return new WP_Error( 'non_compreso', '' );
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        if ( ! file_exists( WP_PLUGIN_DIR . '/' . $file ) ) {
            $r = wp_remote_post( 'https://olotheme.com/wp-json/olo-store/v1/check', [
                'timeout' => 15,
                'headers' => [ 'Content-Type' => 'application/json', 'Accept' => 'application/json' ],
                'body'    => wp_json_encode( [ 'product' => $prodotto, 'version' => '0', 'license_key' => Olobuild_Licenza::chiave(), 'site_url' => Olobuild_Licenza::sito() ] ),
            ] );
            $d   = is_wp_error( $r ) ? null : json_decode( (string) wp_remote_retrieve_body( $r ), true );
            $pkg = is_array( $d ) ? (string) ( $d['package'] ?? '' ) : '';
            if ( '' === $pkg ) return new WP_Error( 'niente_pacchetto', __( 'Il nostro server non ha dato il pacchetto: riprova più tardi da Olobuild › Licenza.', 'olobuild' ) );
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/misc.php';
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
            if ( function_exists( 'set_time_limit' ) ) {
                @set_time_limit( 300 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- alcuni hosting lo vietano
            }
            $skin     = new WP_Ajax_Upgrader_Skin();
            $upgrader = new Plugin_Upgrader( $skin );
            $esito    = $upgrader->install( $pkg );
            if ( is_wp_error( $esito ) ) return $esito;
            if ( is_wp_error( $skin->result ) ) return $skin->result;
            if ( ! $esito || $skin->get_errors()->has_errors() ) return new WP_Error( 'installazione', implode( ' ', $skin->get_error_messages() ) );
            wp_clean_plugins_cache();
        }
        if ( ! is_plugin_active( $file ) ) {
            $a = activate_plugin( $file );
            if ( is_wp_error( $a ) ) return $a;
        }
        return true;
    }

    /**
     * AJAX: i software della rete OLOtheme che servono (passo «Rete OLOtheme»). La scelta si ricorda; quelli che la
     * licenza comprende (OLOsecurity sempre, OLOlang con la Pro) si installano e si attivano subito.
     */
    public function ajax_rete() {
        check_ajax_referer( 'olo_setup', '_nonce' );
        if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'install_plugins' ) ) {
            wp_send_json_error( __( 'Permessi insufficienti.', 'olobuild' ) );
        }
        $grezzo = isset( $_POST['rete'] ) ? sanitize_text_field( wp_unslash( $_POST['rete'] ) ) : '';
        $rete   = self::rete();
        $voci   = array_values( array_intersect( array_map( 'sanitize_key', explode( ',', $grezzo ) ), array_keys( $rete ) ) );
        update_option( 'olobuild_rete_richiesti', $voci, false );
        update_option( 'olobuild_rete_richiesti_il', time(), false );
        $installati = [];
        $errori     = [];
        foreach ( $voci as $v ) {
            if ( ! isset( self::DA_OLOTHEME[ $v ] ) ) continue;
            $r = self::installa_da_olotheme( $v );
            if ( true === $r ) {
                $installati[] = $rete[ $v ]['nome'];
            } elseif ( '' !== $r->get_error_message() ) {
                $errori[] = $rete[ $v ]['nome'] . ': ' . $r->get_error_message();
            }
        }
        wp_send_json_success( [ 'rete' => $voci, 'installati' => $installati, 'errori' => $errori ] );
    }

    /**
     * AJAX: Setup starter completo per chi sceglie "Vuoto".
     * Crea: nav menu primario, page "Home" (publish + page_on_front),
     * template page linkato alla Home, template header con menu, template footer.
     * Restituisce gli ID per popolare le quick-card dello step 3.
     */
    public function ajax_blank_starter() {
        check_ajax_referer( 'olo_setup', '_nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Permessi insufficienti.', 'olobuild' ) );
        }

        $db = Olobuild_Database::instance();

        // 1) Nav menu primario (creato se non esiste già)
        $menu_id = $this->ensure_primary_menu();

        // 2) Page "Home" + impostazione come pagina iniziale
        $home_post_id = $this->ensure_home_page();
        if ( $home_post_id ) {
            update_option( 'show_on_front', 'page' );
            update_option( 'page_on_front', $home_post_id );
        }

        // 3) Template page per la Home (hero starter)
        $home_template_id = 0;
        if ( $home_post_id ) {
            $home_template_id = $db->create_template( [
                'title'    => __( 'Home', 'olobuild' ),
                'type'     => 'page',
                'content'  => $this->get_starter_home_content(),
                'settings' => [ 'post_id' => $home_post_id ],
                'status'   => 'published',
            ] );
            if ( $home_template_id ) {
                update_post_meta( $home_post_id, '_olo_template_id', $home_template_id );
            }
        }

        // 4) Header template (con menu_id reale)
        $header_id = $db->create_template( [
            'title'   => __( 'Header base', 'olobuild' ),
            'type'    => 'header',
            'content' => $this->get_starter_header_content( $menu_id ),
            'status'  => 'published',
        ] );

        // 5) Footer template
        $footer_id = $db->create_template( [
            'title'   => __( 'Footer base', 'olobuild' ),
            'type'    => 'footer',
            'content' => $this->get_starter_footer_content( $home_post_id ),
            'status'  => 'published',
        ] );

        if ( $header_id ) update_option( 'olobuild_active_header', (int) $header_id );
        if ( $footer_id ) update_option( 'olobuild_active_footer', (int) $footer_id );
        update_option( 'olobuild_setup_complete', true );

        wp_send_json_success( [
            'header_id'   => (int) $header_id,
            'footer_id'   => (int) $footer_id,
            'home_id'     => (int) $home_template_id,
            'home_post'   => (int) $home_post_id,
            'menu_id'     => (int) $menu_id,
        ] );
    }

    /**
     * Crea (o ritorna esistente) il nav menu "Menu Principale" con voci base
     * e lo assegna al location `olo_header`.
     */
    private function ensure_primary_menu() {
        $name = __( 'Menu Principale', 'olobuild' );
        $menu = wp_get_nav_menu_object( $name );
        if ( $menu && ! is_wp_error( $menu ) ) return (int) $menu->term_id;

        $menu_id = wp_create_nav_menu( $name );
        if ( is_wp_error( $menu_id ) ) return 0;

        wp_update_nav_menu_item( $menu_id, 0, [
            'menu-item-title'  => __( 'Home', 'olobuild' ),
            'menu-item-url'    => home_url( '/' ),
            'menu-item-status' => 'publish',
        ] );

        $privacy_id = (int) get_option( 'wp_page_for_privacy_policy', 0 );
        if ( $privacy_id && get_post( $privacy_id ) ) {
            wp_update_nav_menu_item( $menu_id, 0, [
                'menu-item-title'     => __( 'Privacy', 'olobuild' ),
                'menu-item-object'    => 'page',
                'menu-item-object-id' => $privacy_id,
                'menu-item-type'      => 'post_type',
                'menu-item-status'    => 'publish',
            ] );
        }

        $locations = get_theme_mod( 'nav_menu_locations', [] );
        $locations['olo_header'] = $menu_id;
        set_theme_mod( 'nav_menu_locations', $locations );

        return (int) $menu_id;
    }

    /**
     * Crea (o ritorna esistente) la page "Home" pubblicata.
     */
    private function ensure_home_page() {
        $existing = get_page_by_path( 'home' );
        if ( $existing ) return (int) $existing->ID;

        $id = wp_insert_post( [
            'post_title'   => __( 'Home', 'olobuild' ),
            'post_name'    => 'home',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '',
            'post_author'  => get_current_user_id(),
        ] );

        return is_wp_error( $id ) ? 0 : (int) $id;
    }

    /**
     * Contenuto starter per la Home: hero semplice (headline + descrizione + CTA).
     */
    private function get_starter_home_content() {
        $builder_url = admin_url( 'admin.php?page=olobuild' );
        // Colori e font stanno in Configurazione › Design › Palette & Stili.
        $stili_url   = admin_url( 'admin.php?page=olobuilder-settings&tab=colori' );

        return [
            $this->build_hero_section( $builder_url ),
            $this->build_checklist_section( $builder_url, $stili_url ),
            $this->build_ideas_section(),
        ];
    }

    private function build_hero_section( $builder_url ) {
        return [
            'id' => $this->uid(), 'type' => 'section',
            'settings' => [ 'style' => 'default', 'width' => 'expand', 'padding' => 'large' ],
            'style' => [ 'bg' => [ 'type' => 'solid', 'color' => '#FAF4ED' ], 'padding_top' => '96', 'padding_bottom' => '48' ],
            'advanced' => new \stdClass(),
            'children' => [ [
                'id' => $this->uid(), 'type' => 'row',
                'settings' => [ 'layout' => '100', 'gap' => 24, 'vertical_align' => 'center' ],
                'style' => new \stdClass(), 'advanced' => new \stdClass(),
                'children' => [ [
                    'id' => $this->uid(), 'type' => 'column',
                    'settings' => [ 'width_default' => '', 'width_small' => '', 'width_medium' => '1-1', 'width_large' => '' ],
                    'style' => new \stdClass(), 'advanced' => new \stdClass(),
                    'children' => [
                        [
                            'id' => $this->uid(), 'type' => 'text-block',
                            'settings' => [
                                'content' => '<p><strong>● ' . esc_html__( 'Setup pronto · 4 passaggi', 'olobuild' ) . '</strong></p>',
                                'text_color' => '#DC2626', 'font_size' => '13', 'alignment' => 'center',
                            ],
                            'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                        ],
                        [
                            'id' => $this->uid(), 'type' => 'spacer',
                            'settings' => [ 'height' => '16' ],
                            'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                        ],
                        [
                            'id' => $this->uid(), 'type' => 'headline',
                            'settings' => [
                                'heading' => __( 'La tua home, in quattro mosse.', 'olobuild' ),
                                'tag' => 'h1', 'heading_size' => 'xl', 'heading_color' => '#1a1a1a',
                                'alignment' => 'center', 'decoration' => 'none',
                            ],
                            'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                        ],
                        [
                            'id' => $this->uid(), 'type' => 'spacer',
                            'settings' => [ 'height' => '24' ],
                            'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                        ],
                        [
                            'id' => $this->uid(), 'type' => 'text-block',
                            'settings' => [
                                'content' => '<p>' . esc_html__( 'Hai appena installato Olobuild. Personalizza le tre aree fondamentali del sito direttamente da qui — gli inviti a fare scompariranno al primo salvataggio.', 'olobuild' ) . '</p>',
                                'text_color' => '#475569', 'font_size' => '18', 'alignment' => 'center', 'max_width' => '640',
                            ],
                            'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                        ],
                        [
                            'id' => $this->uid(), 'type' => 'spacer',
                            'settings' => [ 'height' => '32' ],
                            'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                        ],
                        [
                            'id' => $this->uid(), 'type' => 'button',
                            'settings' => [
                                'text' => __( 'Personalizza la home', 'olobuild' ),
                                'url' => $builder_url,
                                'style' => 'primary', 'size' => 'large', 'alignment' => 'center',
                                'bg_color' => '#DC2626', 'text_color' => '#FFFFFF',
                            ],
                            'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                        ],
                    ],
                ] ],
            ] ],
        ];
    }

    private function build_checklist_section( $builder_url, $stili_url ) {
        return [
            'id' => $this->uid(), 'type' => 'section',
            'settings' => [ 'style' => 'default', 'width' => 'expand', 'padding' => 'large' ],
            'style' => [ 'bg' => [ 'type' => 'solid', 'color' => '#FAF4ED' ], 'padding_top' => '16', 'padding_bottom' => '96' ],
            'advanced' => new \stdClass(),
            'children' => [
                [
                    'id' => $this->uid(), 'type' => 'row',
                    'settings' => [ 'layout' => '100', 'gap' => 24, 'vertical_align' => 'center' ],
                    'style' => new \stdClass(), 'advanced' => new \stdClass(),
                    'children' => [ [
                        'id' => $this->uid(), 'type' => 'column',
                        'settings' => [ 'width_medium' => '1-1' ],
                        'style' => new \stdClass(), 'advanced' => new \stdClass(),
                        'children' => [
                            [
                                'id' => $this->uid(), 'type' => 'text-block',
                                'settings' => [
                                    'content' => '<p><strong>' . esc_html__( 'CHECKLIST DI SETUP', 'olobuild' ) . '</strong></p>',
                                    'text_color' => '#1a1a1a', 'font_size' => '12', 'alignment' => 'center',
                                ],
                                'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                            ],
                            [
                                'id' => $this->uid(), 'type' => 'spacer',
                                'settings' => [ 'height' => '32' ],
                                'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                            ],
                        ],
                    ] ],
                ],
                [
                    'id' => $this->uid(), 'type' => 'row',
                    'settings' => [ 'layout' => '25-25-25-25', 'gap' => 24, 'vertical_align' => 'top', 'stack_mobile' => true ],
                    'style' => new \stdClass(), 'advanced' => new \stdClass(),
                    'children' => [
                        $this->build_step_column( '1', __( 'Personalizza la home', 'olobuild' ), __( 'Sostituisci questo contenuto temporaneo.', 'olobuild' ), __( 'Apri builder', 'olobuild' ), $builder_url ),
                        $this->build_step_column( '2', __( 'Modifica l\'Header', 'olobuild' ), __( 'Logo, menu di navigazione, selettore lingua.', 'olobuild' ), __( 'Modifica', 'olobuild' ), $builder_url ),
                        $this->build_step_column( '3', __( 'Modifica il Footer', 'olobuild' ), __( 'Colonne, link legali, recapiti.', 'olobuild' ), __( 'Modifica', 'olobuild' ), $builder_url ),
                        $this->build_step_column( '4', __( 'Imposta colori e font', 'olobuild' ), __( 'Palette e tipografia del sito, in Sistema › Configurazione.', 'olobuild' ), __( 'Apri Palette & Stili', 'olobuild' ), $stili_url ),
                    ],
                ],
            ],
        ];
    }

    private function build_step_column( $num, $title, $desc, $btn_text, $btn_url ) {
        return [
            'id' => $this->uid(), 'type' => 'column',
            'settings' => [ 'width_medium' => '1-4' ],
            'style' => new \stdClass(), 'advanced' => new \stdClass(),
            'children' => [
                [
                    'id' => $this->uid(), 'type' => 'headline',
                    'settings' => [
                        'heading' => $num, 'tag' => 'div', 'heading_size' => 'xl',
                        'heading_color' => '#DC2626', 'heading_italic' => true,
                        'alignment' => 'left', 'decoration' => 'none',
                    ],
                    'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                ],
                [
                    'id' => $this->uid(), 'type' => 'spacer',
                    'settings' => [ 'height' => '12' ],
                    'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                ],
                [
                    'id' => $this->uid(), 'type' => 'headline',
                    'settings' => [
                        'heading' => $title, 'tag' => 'h3', 'heading_size' => 'md',
                        'heading_color' => '#1a1a1a', 'alignment' => 'left', 'decoration' => 'none',
                    ],
                    'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                ],
                [
                    'id' => $this->uid(), 'type' => 'spacer',
                    'settings' => [ 'height' => '8' ],
                    'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                ],
                [
                    'id' => $this->uid(), 'type' => 'text-block',
                    'settings' => [
                        'content' => '<p>' . esc_html( $desc ) . '</p>',
                        'text_color' => '#475569', 'font_size' => '14', 'alignment' => 'left',
                    ],
                    'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                ],
                [
                    'id' => $this->uid(), 'type' => 'spacer',
                    'settings' => [ 'height' => '16' ],
                    'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                ],
                [
                    'id' => $this->uid(), 'type' => 'button',
                    'settings' => [
                        'text' => $btn_text, 'url' => $btn_url,
                        'style' => 'secondary', 'size' => 'small', 'alignment' => 'left',
                        'bg_color' => '#FFFFFF', 'text_color' => '#DC2626',
                    ],
                    'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                ],
            ],
        ];
    }

    private function build_ideas_section() {
        return [
            'id' => $this->uid(), 'type' => 'section',
            'settings' => [ 'style' => 'default', 'width' => 'expand', 'padding' => 'large' ],
            'style' => [ 'bg' => [ 'type' => 'solid', 'color' => '#FFFFFF' ], 'padding_top' => '96', 'padding_bottom' => '96' ],
            'advanced' => new \stdClass(),
            'children' => [
                [
                    'id' => $this->uid(), 'type' => 'row',
                    'settings' => [ 'layout' => '100', 'gap' => 24, 'vertical_align' => 'center' ],
                    'style' => new \stdClass(), 'advanced' => new \stdClass(),
                    'children' => [ [
                        'id' => $this->uid(), 'type' => 'column',
                        'settings' => [ 'width_medium' => '1-1' ],
                        'style' => new \stdClass(), 'advanced' => new \stdClass(),
                        'children' => [
                            [
                                'id' => $this->uid(), 'type' => 'headline',
                                'settings' => [
                                    'heading' => __( 'Idee per le prossime sezioni', 'olobuild' ),
                                    'tag' => 'h2', 'heading_size' => 'lg', 'heading_color' => '#1a1a1a',
                                    'alignment' => 'center', 'decoration' => 'none',
                                ],
                                'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                            ],
                            [
                                'id' => $this->uid(), 'type' => 'spacer',
                                'settings' => [ 'height' => '40' ],
                                'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                            ],
                        ],
                    ] ],
                ],
                [
                    'id' => $this->uid(), 'type' => 'row',
                    'settings' => [ 'layout' => '33-33-33', 'gap' => 24, 'vertical_align' => 'top', 'stack_mobile' => true ],
                    'style' => new \stdClass(), 'advanced' => new \stdClass(),
                    'children' => [
                        $this->build_idea_card( __( 'Chi siamo', 'olobuild' ), __( 'Racconta in poche righe la tua storia, i valori del brand e cosa rende unico ciò che offri.', 'olobuild' ) ),
                        $this->build_idea_card( __( 'I tuoi servizi', 'olobuild' ), __( '3–6 card con i servizi principali, un\'icona, un titolo e una breve descrizione.', 'olobuild' ) ),
                        $this->build_idea_card( __( 'Recensioni clienti', 'olobuild' ), __( 'Testimonianze reali con foto e nome del cliente. Aumentano la fiducia di chi visita il sito.', 'olobuild' ) ),
                    ],
                ],
            ],
        ];
    }

    private function build_idea_card( $title, $desc ) {
        return [
            'id' => $this->uid(), 'type' => 'column',
            'settings' => [ 'width_medium' => '1-3' ],
            'style' => new \stdClass(), 'advanced' => new \stdClass(),
            'children' => [
                [
                    'id' => $this->uid(), 'type' => 'text-block',
                    'settings' => [
                        'content' => '<p><strong>— ' . esc_html__( 'SEZIONE SUGGERITA', 'olobuild' ) . '</strong></p>',
                        'text_color' => '#DC2626', 'font_size' => '12', 'alignment' => 'left',
                    ],
                    'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                ],
                [
                    'id' => $this->uid(), 'type' => 'spacer',
                    'settings' => [ 'height' => '12' ],
                    'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                ],
                [
                    'id' => $this->uid(), 'type' => 'headline',
                    'settings' => [
                        'heading' => $title, 'tag' => 'h3', 'heading_size' => 'lg',
                        'heading_color' => '#1a1a1a', 'alignment' => 'left', 'decoration' => 'none',
                    ],
                    'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                ],
                [
                    'id' => $this->uid(), 'type' => 'spacer',
                    'settings' => [ 'height' => '12' ],
                    'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                ],
                [
                    'id' => $this->uid(), 'type' => 'text-block',
                    'settings' => [
                        'content' => '<p>' . esc_html( $desc ) . '</p>',
                        'text_color' => '#475569', 'font_size' => '15', 'alignment' => 'left',
                    ],
                    'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                ],
            ],
        ];
    }

    /**
     * Header minimal: una row con megamenu (logo del sito + menu nav).
     * @param int $menu_id Term ID del nav menu da usare nel megamenu.
     */
    private function get_starter_header_content( $menu_id = 0 ) {
        return [ [
            'id' => $this->uid(), 'type' => 'section',
            'settings' => [ 'style' => 'default', 'width' => 'expand', 'padding' => 'none' ],
            'style' => [ 'bg' => [ 'type' => 'solid', 'color' => '#FFFFFF' ], 'padding_top' => '0', 'padding_bottom' => '0' ],
            'advanced' => new \stdClass(),
            'children' => [ [
                'id' => $this->uid(), 'type' => 'row',
                'settings' => [ 'layout' => '100', 'gap' => 0, 'vertical_align' => 'center' ],
                'style' => new \stdClass(), 'advanced' => new \stdClass(),
                'children' => [ [
                    'id' => $this->uid(), 'type' => 'column',
                    'settings' => [ 'width_default' => '', 'width_small' => '', 'width_medium' => '1-1', 'width_large' => '' ],
                    'style' => new \stdClass(), 'advanced' => new \stdClass(),
                    'children' => [ [
                        'id' => $this->uid(), 'type' => 'megamenu',
                        'settings' => [
                            'menu_id' => (int) $menu_id, 'layout' => 'horizontal', 'alignment' => 'right',
                            'font_size' => '15', 'font_weight' => '500', 'text_color' => '#374151',
                            'sticky' => true, 'sticky_bg' => '#FFFFFF', 'sticky_shadow' => true,
                            'logo_image' => '', 'logo_width' => '140', 'logo_position' => 'left', 'logo_link' => '/',
                        ],
                        'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                    ] ],
                ] ],
            ] ],
        ] ];
    }

    /**
     * Footer minimal: 2 colonne — logo + copyright a sinistra, link rapidi a destra.
     * @param int $home_id Post ID della pagina Home (per link "Home").
     */
    private function get_starter_footer_content( $home_id = 0 ) {
        $site_name = get_bloginfo( 'name' );
        $year      = date_i18n( 'Y' );
        $copyright = sprintf( '<p>&copy; %s %s. ' . esc_html__( 'Tutti i diritti riservati.', 'olobuild' ) . '</p>', $year, esc_html( $site_name ) );
        $home_url  = $home_id ? get_permalink( $home_id ) : home_url( '/' );
        $privacy_id = (int) get_option( 'wp_page_for_privacy_policy', 0 );
        $privacy_url = $privacy_id && get_post( $privacy_id ) ? get_permalink( $privacy_id ) : '/privacy-policy/';

        return [ [
            'id' => $this->uid(), 'type' => 'section',
            'settings' => [ 'style' => 'default', 'width' => 'expand', 'padding' => 'medium' ],
            'style' => [ 'bg' => [ 'type' => 'solid', 'color' => '#1a1a1a' ], 'padding_top' => '48', 'padding_bottom' => '32' ],
            'advanced' => new \stdClass(),
            'children' => [ [
                'id' => $this->uid(), 'type' => 'row',
                'settings' => [ 'layout' => '50-50', 'gap' => 30, 'vertical_align' => 'center', 'stack_mobile' => true ],
                'style' => new \stdClass(), 'advanced' => new \stdClass(),
                'children' => [
                    [
                        'id' => $this->uid(), 'type' => 'column',
                        'settings' => [ 'width_default' => '', 'width_small' => '', 'width_medium' => '1-2', 'width_large' => '' ],
                        'style' => new \stdClass(), 'advanced' => new \stdClass(),
                        'children' => [
                            [
                                'id' => $this->uid(), 'type' => 'sitelogo',
                                'settings' => [ 'source' => 'auto', 'max_height' => 36, 'link_home' => true ],
                                'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                            ],
                            [
                                'id' => $this->uid(), 'type' => 'spacer',
                                'settings' => [ 'height' => '12' ],
                                'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                            ],
                            [
                                'id' => $this->uid(), 'type' => 'text-block',
                                'settings' => [ 'content' => $copyright, 'text_color' => '#94A3B8', 'font_size' => '13' ],
                                'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                            ],
                        ],
                    ],
                    [
                        'id' => $this->uid(), 'type' => 'column',
                        'settings' => [ 'width_default' => '', 'width_small' => '', 'width_medium' => '1-2', 'width_large' => '' ],
                        'style' => new \stdClass(), 'advanced' => new \stdClass(),
                        'children' => [
                            [
                                'id' => $this->uid(), 'type' => 'list',
                                'settings' => [
                                    'items' => [
                                        [ 'text' => __( 'Home', 'olobuild' ),     'url' => $home_url ],
                                        [ 'text' => __( 'Privacy', 'olobuild' ),  'url' => $privacy_url ],
                                        [ 'text' => __( 'Cookie', 'olobuild' ),   'url' => '/cookie/' ],
                                        [ 'text' => __( 'Contatti', 'olobuild' ), 'url' => '/contatti/' ],
                                    ],
                                    'style' => 'inline', 'color' => '#CBD5E1', 'hover_color' => '#FFFFFF',
                                    'font_size' => '14', 'gap' => '16', 'alignment' => 'right',
                                ],
                                'style' => new \stdClass(), 'advanced' => new \stdClass(), 'children' => [],
                            ],
                        ],
                    ],
                ],
            ] ],
        ] ];
    }

    /**
     * Genera ID univoco breve per i tile (8 char alfanumerici).
     */
    private function uid() {
        return substr( bin2hex( random_bytes( 4 ) ), 0, 8 );
    }

    /**
     * Recursively copy a directory.
     */
    private function copy_dir( $src, $dst ) {
        if ( ! is_dir( $dst ) ) {
            wp_mkdir_p( $dst );
        }
        $dir = opendir( $src );
        while ( ( $file = readdir( $dir ) ) !== false ) {
            if ( $file === '.' || $file === '..' ) continue;
            $src_path = $src . '/' . $file;
            $dst_path = $dst . '/' . $file;
            if ( is_dir( $src_path ) ) {
                $this->copy_dir( $src_path, $dst_path );
            } else {
                copy( $src_path, $dst_path );
            }
        }
        closedir( $dir );
    }
}
