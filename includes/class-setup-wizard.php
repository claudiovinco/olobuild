<?php
/**
 * Olobuild Setup Wizard — first-run experience.
 * Auto-installs Hello Olobuild theme and offers theme import.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Olo_Setup_Wizard {

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
        add_action( 'wp_ajax_olo_setup_install_theme', [ $this, 'ajax_install_theme' ] );
        add_action( 'wp_ajax_olo_setup_import_theme', [ $this, 'ajax_import_theme' ] );
        add_action( 'wp_ajax_olo_setup_skip', [ $this, 'ajax_skip' ] );
        add_action( 'wp_ajax_olo_setup_blank_starter', [ $this, 'ajax_blank_starter' ] );

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
        if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'olo-setup' ) return;
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
        if ( isset( $_GET['activate-multi'] ) ) return; // attivazione bulk: solo notice
        if ( ! current_user_can( 'manage_options' ) ) return;
        if ( get_option( 'olo_setup_complete' ) ) return;

        wp_safe_redirect( admin_url( 'admin.php?page=olo-setup' ) );
        exit;
    }

    /**
     * "Più tardi": nasconde il pannello di benvenuto in modo persistente (option).
     */
    public function maybe_dismiss_welcome() {
        if ( ! isset( $_GET['olo_welcome_dismiss'] ) ) return;
        if ( ! current_user_can( 'manage_options' ) ) return;
        $nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 'olo_welcome_dismiss' ) ) return;

        update_option( 'olo_welcome_dismissed', 1, false );
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
        if ( get_option( 'olo_setup_complete' ) || get_option( 'olo_welcome_dismissed' ) ) return;
        if ( isset( $_GET['page'] ) && $_GET['page'] === 'olo-setup' ) return;

        $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
        if ( $screen && ! in_array( $screen->id, [ 'dashboard', 'plugins' ], true ) ) return;

        $wizard_url  = admin_url( 'admin.php?page=olo-setup' );
        $dismiss_url = wp_nonce_url( add_query_arg( 'olo_welcome_dismiss', '1' ), 'olo_welcome_dismiss' );
        $logo_url    = OLO_URL . 'assets/img/olobuild-logo-200-white.png';
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
                <img class="olo-welcome__logo" src="<?php echo esc_url( $logo_url ); ?>" alt="Olobuild">
                <div class="olo-welcome__text">
                    <h2><?php esc_html_e( 'Ti diamo il benvenuto in Olobuild!', 'olobuild' ); ?></h2>
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
     * Render the setup wizard page.
     */
    public function render_wizard() {
        $theme_installed = wp_get_theme( 'hello-olobuild' )->exists();
        $theme_active    = get_stylesheet() === 'hello-olobuild';

        // Get available Olobuild themes (con campi visivi per le mini-anteprime del picker)
        require_once OLO_PATH . 'includes/class-theme-importer.php';
        $themes = Olo_Theme_Importer::get_themes();

        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo( 'charset' ); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title><?php echo esc_html( __( 'Olobuild — Configurazione iniziale', 'olobuild' ) ); ?></title>
            <style>
                * { box-sizing: border-box; margin: 0; padding: 0; }
                body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #1a1a1a; color: #E2E8F0; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
                .wizard { max-width: 680px; width: 100%; padding: 48px; transition: max-width 0.25s ease; }
                .wizard.wide { max-width: 1080px; }
                .wizard-logo { text-align: center; margin-bottom: 40px; }
                .wizard-logo img { height: 48px; }
                .wizard-logo h1 { font-size: 28px; font-weight: 700; color: #F8FAFC; margin-top: 16px; }
                .wizard-logo p { font-size: 15px; color: #94A3B8; margin-top: 8px; line-height: 1.5; }
                .wizard-steps { display: flex; gap: 8px; margin-bottom: 32px; justify-content: center; }
                .wizard-step { width: 40px; height: 4px; border-radius: 2px; background: #3a3a3a; transition: background 0.3s; }
                .wizard-step.active { background: #e8622a; }
                .wizard-step.done { background: #22C55E; }
                .wizard-card { background: #262626; border: 1px solid #3a3a3a; border-radius: 16px; padding: 32px; margin-bottom: 16px; }
                .wizard-card h2 { font-size: 18px; font-weight: 600; color: #F1F5F9; margin-bottom: 8px; }
                .wizard-card p { font-size: 14px; color: #94A3B8; line-height: 1.6; margin-bottom: 20px; }
                .btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 28px; border: none; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
                .btn-primary { background: #e8622a; color: #fff; }
                .btn-primary:hover { background: #d4561f; transform: translateY(-1px); }
                .btn-primary:disabled { background: #475569; cursor: not-allowed; transform: none; }
                .btn-secondary { background: transparent; color: #94A3B8; border: 1px solid #475569; }
                .btn-secondary:hover { border-color: #64748B; color: #CBD5E1; }
                .btn-success { background: #22C55E; color: #fff; }
                .btn-group { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
                .check { color: #22C55E; font-size: 20px; margin-right: 4px; }
                .spinner { display: inline-block; width: 18px; height: 18px; border: 2px solid rgba(255,255,255,0.2); border-top-color: #fff; border-radius: 50%; animation: spin 0.6s linear infinite; }
                @keyframes spin { to { transform: rotate(360deg); } }
                .status { font-size: 13px; color: #94A3B8; margin-top: 12px; min-height: 20px; }
                /* Il selettore temi (card + filtri + ricerca) è il picker condiviso OloThemePicker. */
                .wizard-picker { margin-bottom: 20px; }
                .wizard-picker .otmp { height: 56vh; min-height: 320px; }
                .skip-link { display: block; text-align: center; margin-top: 20px; color: #64748B; font-size: 13px; cursor: pointer; text-decoration: none; }
                .skip-link:hover { color: #94A3B8; }
                .btn:focus-visible, .skip-link:focus-visible, .quick-card:focus-visible { outline: 2px solid #e8622a; outline-offset: 2px; }
                .quick-cards { display: grid; gap: 10px; }
                .quick-card { display: flex; gap: 14px; align-items: center; padding: 14px 16px; background: #1a1a1a; border: 1px solid #3a3a3a; border-radius: 10px; text-decoration: none; color: #E2E8F0; transition: all 0.2s; }
                .quick-card:hover { border-color: #e8622a; transform: translateX(2px); }
                .quick-card-icon { font-size: 20px; width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; background: #262626; border-radius: 8px; color: #f0a883; flex-shrink: 0; }
                .quick-card strong { display: block; font-size: 14px; font-weight: 600; color: #F1F5F9; }
                .quick-card small { display: block; font-size: 12px; color: #94A3B8; margin-top: 2px; }
            </style>
        </head>
        <body>
            <div class="wizard">
                <div class="wizard-logo">
                    <?php if ( file_exists( OLO_PATH . 'assets/img/olobuild-logo-200-v2.png' ) ) : ?>
                        <img src="<?php echo esc_url( OLO_URL . 'assets/img/olobuild-logo-200-v2.png' ); ?>" alt="Olobuild">
                    <?php endif; ?>
                    <h1><?php esc_html_e( 'Benvenuto in Olobuild', 'olobuild' ); ?></h1>
                    <p><?php echo wp_kses( __( 'Configuriamo il tuo sito in pochi secondi.<br>Potrai personalizzare tutto in seguito.', 'olobuild' ), [ 'br' => [] ] ); ?></p>
                </div>

                <div class="wizard-steps">
                    <div class="wizard-step" id="step-dot-1"></div>
                    <div class="wizard-step" id="step-dot-2"></div>
                    <div class="wizard-step" id="step-dot-3"></div>
                </div>

                <!-- Step 1: Install Theme -->
                <div class="wizard-card" id="step-1">
                    <h2><?php esc_html_e( '1. Tema WordPress', 'olobuild' ); ?></h2>
                    <?php if ( $theme_active ) : ?>
                        <p><span class="check">✓</span> <?php esc_html_e( 'Hello Olobuild è già attivo. Perfetto!', 'olobuild' ); ?></p>
                        <button class="btn btn-success" onclick="nextStep(2)"><?php esc_html_e( 'Continua →', 'olobuild' ); ?></button>
                    <?php elseif ( $theme_installed ) : ?>
                        <p><?php esc_html_e( 'Hello Olobuild è installato ma non attivo. È un tema leggero senza CSS invasivo — perfetto per Olobuild.', 'olobuild' ); ?></p>
                        <div class="btn-group">
                            <button class="btn btn-primary" onclick="installTheme('activate')"><?php esc_html_e( 'Attiva Hello Olobuild', 'olobuild' ); ?></button>
                            <button class="btn btn-secondary" onclick="nextStep(2)"><?php esc_html_e( 'Mantieni tema attuale →', 'olobuild' ); ?></button>
                        </div>
                    <?php else : ?>
                        <p><?php echo wp_kses( __( 'Installiamo <strong>Hello Olobuild</strong> — un tema WordPress leggero e minimale, progettato per non interferire col builder. Solo ~3KB di CSS.', 'olobuild' ), [ 'strong' => [] ] ); ?></p>
                        <div class="btn-group">
                            <button class="btn btn-primary" id="btn-install" onclick="installTheme('install')">
                                <?php esc_html_e( 'Installa e attiva', 'olobuild' ); ?>
                            </button>
                            <button class="btn btn-secondary" onclick="nextStep(2)"><?php esc_html_e( 'Usa tema attuale →', 'olobuild' ); ?></button>
                        </div>
                    <?php endif; ?>
                    <div class="status" id="status-1"></div>
                </div>

                <!-- Step 2: Choose Site Theme -->
                <div class="wizard-card" id="step-2" style="display:none">
                    <h2><?php esc_html_e( '2. Scegli un design', 'olobuild' ); ?></h2>
                    <p><?php esc_html_e( 'Seleziona un tema completo per iniziare. Include header, footer, homepage e menu — tutto personalizzabile.', 'olobuild' ); ?></p>
                    <div class="wizard-picker" id="olo-wizard-picker"><!-- montato da OloThemePicker (embed) --></div>
                    <div class="btn-group">
                        <button class="btn btn-primary" id="btn-import" onclick="importTheme()" disabled><?php esc_html_e( 'Importa tema selezionato', 'olobuild' ); ?></button>
                    </div>
                    <div class="status" id="status-2"></div>
                </div>

                <!-- Step 3: Done -->
                <div class="wizard-card" id="step-3" style="display:none">
                    <h2><?php esc_html_e( '✨ Tutto pronto!', 'olobuild' ); ?></h2>
                    <p id="step-3-intro"><?php esc_html_e( 'Il tuo sito è configurato. Puoi iniziare a personalizzare tutto dal builder visuale.', 'olobuild' ); ?></p>

                    <!-- Quick-start cards (popolate dinamicamente quando si crea uno starter blank) -->
                    <div class="quick-cards" id="quick-cards" style="display:none; margin: 20px 0;">
                        <a href="#" id="card-header" class="quick-card">
                            <span class="quick-card-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="6" rx="1"/><line x1="3" y1="13" x2="21" y2="13" opacity="0.4"/><line x1="3" y1="17" x2="21" y2="17" opacity="0.4"/></svg>
                            </span>
                            <span>
                                <strong><?php esc_html_e( 'Personalizza header', 'olobuild' ); ?></strong>
                                <small><?php esc_html_e( 'Logo e menu già pronti — apri il builder', 'olobuild' ); ?></small>
                            </span>
                        </a>
                        <a href="#" id="card-footer" class="quick-card">
                            <span class="quick-card-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="7" x2="21" y2="7" opacity="0.4"/><line x1="3" y1="11" x2="21" y2="11" opacity="0.4"/><rect x="3" y="15" width="18" height="6" rx="1"/></svg>
                            </span>
                            <span>
                                <strong><?php esc_html_e( 'Personalizza footer', 'olobuild' ); ?></strong>
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

                    <div class="btn-group">
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=olobuild' ) ); ?>" class="btn btn-primary"><?php esc_html_e( 'Apri Olobuild →', 'olobuild' ); ?></a>
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-secondary" target="_blank"><?php esc_html_e( 'Vedi il sito →', 'olobuild' ); ?></a>
                    </div>
                </div>

                <a href="#" role="button" class="skip-link" onclick="skipSetup(); return false;"><?php esc_html_e( 'Salta configurazione', 'olobuild' ); ?></a>
            </div>

            <script src="<?php echo esc_url( OLO_URL . 'assets/js/theme-picker.js' ); ?>?v=<?php echo esc_attr( OLO_VERSION ); ?>"></script>
            <?php // Pipeline thumbnail (render REST → html2canvas → upload): genera le anteprime card subito dopo l'import del tema ?>
            <script>
            window.oloThumbConfig = {
                restUrl:   '<?php echo esc_url_raw( rest_url( 'olo/v1/' ) ); ?>',
                nonce:     '<?php echo esc_js( wp_create_nonce( 'wp_rest' ) ); ?>',
                vendorUrl: '<?php echo esc_url( OLO_URL . 'assets/vendor/html2canvas.min.js' ); ?>',
                debug:     false
            };
            </script>
            <script src="<?php echo esc_url( OLO_URL . 'assets/js/olo-thumb-capture.js' ); ?>?v=<?php echo esc_attr( OLO_VERSION ); ?>"></script>
            <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline JS below only outputs wp_json_encode()'d JSON strings, a wp_create_nonce() token, esc_url()'d admin URLs and a fixed integer literal from a ternary. ?>
            <script>
            var oloWizardThemes = <?php echo wp_json_encode( array_map( function ( $t ) { unset( $t['dir'] ); return $t; }, $themes ) ); ?>;
            var nonce = '<?php echo wp_create_nonce( 'olo_setup' ); ?>';
            var ajaxurl = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
            var selectedThemeId = null;
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
                creatingTemplates: <?php echo wp_json_encode( __( 'Creazione template, menu, pagine...', 'olobuild' ) ); ?>,
                templatesCreated: <?php echo wp_json_encode( __( '%d template creati, homepage impostata!', 'olobuild' ) ); ?>
            };
            var oloBuilderBase = <?php echo wp_json_encode( admin_url( 'admin.php?page=olobuilder-templates&template_id=' ) ); ?>;

            function setStep(n) {
                for (var i = 1; i <= 3; i++) {
                    document.getElementById('step-' + i).style.display = i === n ? 'block' : 'none';
                    var dot = document.getElementById('step-dot-' + i);
                    dot.className = 'wizard-step' + (i < n ? ' done' : (i === n ? ' active' : ''));
                }
                var wiz = document.querySelector('.wizard');
                if (wiz) wiz.classList.toggle('wide', n === 2);
            }

            function nextStep(n) { setStep(n); }

            function installTheme(action) {
                var btn = document.getElementById('btn-install');
                var status = document.getElementById('status-1');
                if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner"></span> ' + oloSetupI18n.installing; }
                status.textContent = oloSetupI18n.installingProgress;

                fetch(ajaxurl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=olo_setup_install_theme&_nonce=' + nonce + '&mode=' + action
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        status.innerHTML = '<span class="check">✓</span> ' + data.data.message;
                        setTimeout(function() { nextStep(2); }, 800);
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
                    host.innerHTML = '<p style="color:#94A3B8;font-size:14px;padding:24px;text-align:center">'
                        + <?php echo wp_json_encode( __( 'Impossibile caricare il selettore temi.', 'olobuild' ) ); ?> + '</p>';
                    return;
                }
                window.OloThemePicker.create({
                    mode: 'embed',
                    target: host,
                    themes: oloWizardThemes,
                    card: { action: 'select' },
                    blank: {
                        id: 'blank',
                        name: <?php echo wp_json_encode( __( 'Vuoto', 'olobuild' ) ); ?>,
                        desc: <?php echo wp_json_encode( __( 'Parti da zero, tela bianca.', 'olobuild' ) ); ?>
                    },
                    i18n: {
                        title: <?php echo wp_json_encode( __( 'Temi Olobuild', 'olobuild' ) ); ?>,
                        importLabel: <?php echo wp_json_encode( __( 'Importa tema', 'olobuild' ) ); ?>
                    },
                    onSelect: function (id) {
                        selectedThemeId = id;
                        var b = document.getElementById('btn-import');
                        if (b) b.disabled = false;
                    }
                });
            })();

            function importTheme() {
                if (!selectedThemeId) return;
                var btn = document.getElementById('btn-import');
                var status = document.getElementById('status-2');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner"></span> ' + oloSetupI18n.importing;

                if (selectedThemeId === 'blank') {
                    status.textContent = oloSetupI18n.creatingStarter;
                    fetch(ajaxurl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'action=olo_setup_blank_starter&_nonce=' + nonce
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.success) {
                            status.innerHTML = '<span class="check">✓</span> ' + oloSetupI18n.starterReady;
                            // Popola le card dello step 3 con i link contestuali
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
                            if (cards) cards.style.display = 'grid';
                            setTimeout(function() { nextStep(3); }, 800);
                        } else {
                            status.textContent = '⚠ ' + (data.data || oloSetupI18n.error);
                            btn.disabled = false;
                            btn.textContent = oloSetupI18n.retry;
                        }
                    })
                    .catch(function(e) { status.textContent = '⚠ ' + oloSetupI18n.connectionError; });
                    return;
                }

                status.textContent = oloSetupI18n.creatingTemplates;

                fetch(ajaxurl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=olo_setup_import_theme&_nonce=' + nonce + '&theme_id=' + selectedThemeId
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        var d = data.data;
                        status.innerHTML = '<span class="check">✓</span> ' + oloSetupI18n.templatesCreated.replace('%d', d.templates);
                        // Anteprime card in background (continua mentre l'utente è sullo step finale;
                        // le eventuali rimanenti le completa l'auto-heal della dashboard template).
                        if (window.oloGenerateMissingThumbs && d.template_ids && d.template_ids.length) {
                            window.oloGenerateMissingThumbs(d.template_ids);
                        }
                        setTimeout(function() { nextStep(3); }, 1000);
                    } else {
                        status.textContent = '⚠ ' + (data.data || oloSetupI18n.error);
                        btn.disabled = false;
                        btn.textContent = oloSetupI18n.retry;
                    }
                })
                .catch(function(e) { status.textContent = '⚠ ' + oloSetupI18n.connectionError; });
            }

            function markComplete() {
                fetch(ajaxurl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=olo_setup_skip&_nonce=' + nonce
                });
            }

            function skipSetup() {
                markComplete();
                window.location.href = '<?php echo esc_url( admin_url( 'admin.php?page=olobuild' ) ); ?>';
            }

            // Init first step
            setStep(<?php echo $theme_active ? 2 : 1; ?>);
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

        $mode = sanitize_text_field( $_POST['mode'] ?? 'install' );
        $theme_slug = 'hello-olobuild';
        $theme_dir  = get_theme_root() . '/' . $theme_slug;

        // Copy theme from plugin bundle if not installed
        if ( ! wp_get_theme( $theme_slug )->exists() ) {
            $source = OLO_PATH . 'includes/theme-bundle/';
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

        $theme_id = sanitize_text_field( $_POST['theme_id'] ?? '' );
        if ( empty( $theme_id ) ) {
            wp_send_json_error( __( 'Nessun tema selezionato.', 'olobuild' ) );
        }

        require_once OLO_PATH . 'includes/class-theme-importer.php';
        $result = Olo_Theme_Importer::import_theme( $theme_id );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( $result->get_error_message() );
        }

        // Mark setup as complete
        update_option( 'olo_setup_complete', true );

        wp_send_json_success( [
            'templates'    => count( $result['templates'] ?? [] ),
            'template_ids' => array_values( array_filter( array_column( $result['templates'] ?? [], 'id' ) ) ),
            'activated'    => $result['activated'] ?? [],
            'menu'         => $result['menu'] ?? null,
        ] );
    }

    /**
     * AJAX: Skip setup.
     */
    public function ajax_skip() {
        check_ajax_referer( 'olo_setup', '_nonce' );
        update_option( 'olo_setup_complete', true );
        wp_send_json_success();
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

        $db = Olo_Database::instance();

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

        if ( $header_id ) update_option( 'olo_active_header', (int) $header_id );
        if ( $footer_id ) update_option( 'olo_active_footer', (int) $footer_id );
        update_option( 'olo_setup_complete', true );

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

        return [
            $this->build_hero_section( $builder_url ),
            $this->build_checklist_section( $builder_url ),
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

    private function build_checklist_section( $builder_url ) {
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
                        $this->build_step_column( '4', __( 'Imposta colori e font', 'olobuild' ), __( 'Style Manager globale del sito.', 'olobuild' ), __( 'Apri Style Manager', 'olobuild' ), $builder_url ),
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
        $year      = date( 'Y' );
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
