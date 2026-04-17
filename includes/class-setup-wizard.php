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

        // Show activation notice with link to wizard (safer than auto-redirect)
        add_action( 'admin_notices', [ $this, 'show_activation_notice' ] );

        // AJAX handlers
        add_action( 'wp_ajax_olo_setup_install_theme', [ $this, 'ajax_install_theme' ] );
        add_action( 'wp_ajax_olo_setup_import_theme', [ $this, 'ajax_import_theme' ] );
        add_action( 'wp_ajax_olo_setup_skip', [ $this, 'ajax_skip' ] );

        // Consume activation transient silently
        add_action( 'admin_init', function() {
            if ( get_transient( 'olo_activating' ) ) {
                delete_transient( 'olo_activating' );
            }
        } );
    }

    /**
     * Show a dismissible admin notice after activation (replaces auto-redirect).
     */
    public function show_activation_notice() {
        if ( ! current_user_can( 'manage_options' ) ) return;
        if ( get_option( 'olo_setup_complete' ) ) return;
        if ( isset( $_GET['page'] ) && $_GET['page'] === 'olo-setup' ) return;

        $wizard_url = admin_url( 'admin.php?page=olo-setup' );
        ?>
        <div class="notice notice-info is-dismissible">
            <p>
                <strong>Olobuild</strong> — <?php esc_html_e( 'Configurazione iniziale disponibile', 'olobuilder' ); ?>
                <a href="<?php echo esc_url( $wizard_url ); ?>" class="button button-primary" style="margin-left:12px;">
                    <?php esc_html_e( 'Avvia configurazione', 'olobuilder' ); ?>
                </a>
            </p>
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
            'Olobuild Setup',
            'Olobuild Setup',
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

        // Get available Olobuild themes
        require_once OLO_PATH . 'includes/class-theme-importer.php';
        $themes = Olo_Theme_Importer::get_themes();

        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo( 'charset' ); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Olobuild — Configurazione iniziale</title>
            <style>
                * { box-sizing: border-box; margin: 0; padding: 0; }
                body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #0F172A; color: #E2E8F0; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
                .wizard { max-width: 680px; width: 100%; padding: 48px; }
                .wizard-logo { text-align: center; margin-bottom: 40px; }
                .wizard-logo img { height: 48px; }
                .wizard-logo h1 { font-size: 28px; font-weight: 700; color: #F8FAFC; margin-top: 16px; }
                .wizard-logo p { font-size: 15px; color: #94A3B8; margin-top: 8px; line-height: 1.5; }
                .wizard-steps { display: flex; gap: 8px; margin-bottom: 32px; justify-content: center; }
                .wizard-step { width: 40px; height: 4px; border-radius: 2px; background: #334155; transition: background 0.3s; }
                .wizard-step.active { background: #3B82F6; }
                .wizard-step.done { background: #22C55E; }
                .wizard-card { background: #1E293B; border: 1px solid #334155; border-radius: 16px; padding: 32px; margin-bottom: 16px; }
                .wizard-card h2 { font-size: 18px; font-weight: 600; color: #F1F5F9; margin-bottom: 8px; }
                .wizard-card p { font-size: 14px; color: #94A3B8; line-height: 1.6; margin-bottom: 20px; }
                .btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 28px; border: none; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
                .btn-primary { background: #3B82F6; color: #fff; }
                .btn-primary:hover { background: #2563EB; transform: translateY(-1px); }
                .btn-primary:disabled { background: #475569; cursor: not-allowed; transform: none; }
                .btn-secondary { background: transparent; color: #94A3B8; border: 1px solid #475569; }
                .btn-secondary:hover { border-color: #64748B; color: #CBD5E1; }
                .btn-success { background: #22C55E; color: #fff; }
                .btn-group { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
                .check { color: #22C55E; font-size: 20px; margin-right: 4px; }
                .spinner { display: inline-block; width: 18px; height: 18px; border: 2px solid rgba(255,255,255,0.2); border-top-color: #fff; border-radius: 50%; animation: spin 0.6s linear infinite; }
                @keyframes spin { to { transform: rotate(360deg); } }
                .status { font-size: 13px; color: #94A3B8; margin-top: 12px; min-height: 20px; }
                .theme-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; margin-bottom: 20px; }
                .theme-option { background: #0F172A; border: 2px solid #334155; border-radius: 12px; padding: 16px; cursor: pointer; transition: all 0.2s; text-align: center; }
                .theme-option:hover { border-color: #3B82F6; }
                .theme-option.selected { border-color: #3B82F6; background: #1E3A5F; }
                .theme-option h3 { font-size: 15px; font-weight: 600; color: #F1F5F9; margin-bottom: 4px; }
                .theme-option p { font-size: 12px; color: #64748B; }
                .theme-option .icon { font-size: 36px; margin-bottom: 8px; }
                .skip-link { display: block; text-align: center; margin-top: 20px; color: #64748B; font-size: 13px; cursor: pointer; text-decoration: none; }
                .skip-link:hover { color: #94A3B8; }
            </style>
        </head>
        <body>
            <div class="wizard">
                <div class="wizard-logo">
                    <?php if ( file_exists( OLO_PATH . 'assets/img/olobuild-logo-200-v2.png' ) ) : ?>
                        <img src="<?php echo esc_url( OLO_URL . 'assets/img/olobuild-logo-200-v2.png' ); ?>" alt="Olobuild">
                    <?php endif; ?>
                    <h1>Benvenuto in Olobuild</h1>
                    <p>Configuriamo il tuo sito in pochi secondi.<br>Potrai personalizzare tutto in seguito.</p>
                </div>

                <div class="wizard-steps">
                    <div class="wizard-step" id="step-dot-1"></div>
                    <div class="wizard-step" id="step-dot-2"></div>
                    <div class="wizard-step" id="step-dot-3"></div>
                </div>

                <!-- Step 1: Install Theme -->
                <div class="wizard-card" id="step-1">
                    <h2>1. Tema WordPress</h2>
                    <?php if ( $theme_active ) : ?>
                        <p><span class="check">✓</span> Hello Olobuild è già attivo. Perfetto!</p>
                        <button class="btn btn-success" onclick="nextStep(2)">Continua →</button>
                    <?php elseif ( $theme_installed ) : ?>
                        <p>Hello Olobuild è installato ma non attivo. È un tema leggero senza CSS invasivo — perfetto per Olobuild.</p>
                        <div class="btn-group">
                            <button class="btn btn-primary" onclick="installTheme('activate')">Attiva Hello Olobuild</button>
                            <button class="btn btn-secondary" onclick="nextStep(2)">Mantieni tema attuale →</button>
                        </div>
                    <?php else : ?>
                        <p>Installiamo <strong>Hello Olobuild</strong> — un tema WordPress leggero e minimale, progettato per non interferire col builder. Solo ~3KB di CSS.</p>
                        <div class="btn-group">
                            <button class="btn btn-primary" id="btn-install" onclick="installTheme('install')">
                                Installa e attiva
                            </button>
                            <button class="btn btn-secondary" onclick="nextStep(2)">Usa tema attuale →</button>
                        </div>
                    <?php endif; ?>
                    <div class="status" id="status-1"></div>
                </div>

                <!-- Step 2: Choose Site Theme -->
                <div class="wizard-card" id="step-2" style="display:none">
                    <h2>2. Scegli un design</h2>
                    <p>Seleziona un tema completo per iniziare. Include header, footer, homepage e menu — tutto personalizzabile.</p>
                    <div class="theme-grid">
                        <?php foreach ( $themes as $theme ) : ?>
                            <div class="theme-option" onclick="selectTheme(this, '<?php echo esc_attr( $theme['id'] ); ?>')" data-theme-id="<?php echo esc_attr( $theme['id'] ); ?>">
                                <div class="icon">🎨</div>
                                <h3><?php echo esc_html( $theme['name'] ); ?></h3>
                                <p><?php echo esc_html( implode( ', ', $theme['tags'] ?? [] ) ); ?></p>
                            </div>
                        <?php endforeach; ?>
                        <div class="theme-option" onclick="selectTheme(this, 'blank')" data-theme-id="blank">
                            <div class="icon">📄</div>
                            <h3>Vuoto</h3>
                            <p>Parti da zero</p>
                        </div>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-primary" id="btn-import" onclick="importTheme()" disabled>Importa tema selezionato</button>
                    </div>
                    <div class="status" id="status-2"></div>
                </div>

                <!-- Step 3: Done -->
                <div class="wizard-card" id="step-3" style="display:none">
                    <h2>✨ Tutto pronto!</h2>
                    <p>Il tuo sito è configurato. Puoi iniziare a personalizzare tutto dal builder visuale.</p>
                    <div class="btn-group">
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=olobuild' ) ); ?>" class="btn btn-primary">Apri Olobuild →</a>
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-secondary" target="_blank">Vedi il sito →</a>
                    </div>
                </div>

                <a href="#" class="skip-link" onclick="skipSetup(); return false;">Salta configurazione</a>
            </div>

            <script>
            var nonce = '<?php echo wp_create_nonce( 'olo_setup' ); ?>';
            var ajaxurl = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
            var selectedThemeId = null;

            function setStep(n) {
                for (var i = 1; i <= 3; i++) {
                    document.getElementById('step-' + i).style.display = i === n ? 'block' : 'none';
                    var dot = document.getElementById('step-dot-' + i);
                    dot.className = 'wizard-step' + (i < n ? ' done' : (i === n ? ' active' : ''));
                }
            }

            function nextStep(n) { setStep(n); }

            function installTheme(action) {
                var btn = document.getElementById('btn-install');
                var status = document.getElementById('status-1');
                if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner"></span> Installazione...'; }
                status.textContent = 'Installazione in corso...';

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
                        status.textContent = '⚠ ' + (data.data || 'Errore');
                        if (btn) { btn.disabled = false; btn.textContent = 'Riprova'; }
                    }
                })
                .catch(function(e) { status.textContent = '⚠ Errore di connessione'; });
            }

            function selectTheme(el, id) {
                document.querySelectorAll('.theme-option').forEach(function(t) { t.classList.remove('selected'); });
                el.classList.add('selected');
                selectedThemeId = id;
                document.getElementById('btn-import').disabled = false;
            }

            function importTheme() {
                if (!selectedThemeId) return;
                var btn = document.getElementById('btn-import');
                var status = document.getElementById('status-2');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner"></span> Importazione...';

                if (selectedThemeId === 'blank') {
                    status.innerHTML = '<span class="check">✓</span> Nessun tema importato — parti da zero!';
                    markComplete();
                    setTimeout(function() { nextStep(3); }, 800);
                    return;
                }

                status.textContent = 'Creazione template, menu, pagine...';

                fetch(ajaxurl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=olo_setup_import_theme&_nonce=' + nonce + '&theme_id=' + selectedThemeId
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        var d = data.data;
                        status.innerHTML = '<span class="check">✓</span> ' + d.templates + ' template creati, homepage impostata!';
                        setTimeout(function() { nextStep(3); }, 1000);
                    } else {
                        status.textContent = '⚠ ' + (data.data || 'Errore');
                        btn.disabled = false;
                        btn.textContent = 'Riprova';
                    }
                })
                .catch(function(e) { status.textContent = '⚠ Errore di connessione'; });
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
            wp_send_json_error( 'Permessi insufficienti.' );
        }

        $mode = sanitize_text_field( $_POST['mode'] ?? 'install' );
        $theme_slug = 'hello-olobuild';
        $theme_dir  = get_theme_root() . '/' . $theme_slug;

        // Copy theme from plugin bundle if not installed
        if ( ! wp_get_theme( $theme_slug )->exists() ) {
            $source = OLO_PATH . 'includes/theme-bundle/';
            if ( ! is_dir( $source ) ) {
                wp_send_json_error( 'Bundle tema non trovato nel plugin.' );
            }

            // Copy recursively
            $this->copy_dir( $source, $theme_dir );
        }

        // Activate theme
        switch_theme( $theme_slug );

        wp_send_json_success( [
            'message' => 'Hello Olobuild installato e attivato!',
        ] );
    }

    /**
     * AJAX: Import a site theme.
     */
    public function ajax_import_theme() {
        check_ajax_referer( 'olo_setup', '_nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Permessi insufficienti.' );
        }

        $theme_id = sanitize_text_field( $_POST['theme_id'] ?? '' );
        if ( empty( $theme_id ) ) {
            wp_send_json_error( 'Nessun tema selezionato.' );
        }

        require_once OLO_PATH . 'includes/class-theme-importer.php';
        $result = Olo_Theme_Importer::import_theme( $theme_id );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( $result->get_error_message() );
        }

        // Mark setup as complete
        update_option( 'olo_setup_complete', true );

        wp_send_json_success( [
            'templates' => count( $result['templates'] ?? [] ),
            'activated' => $result['activated'] ?? [],
            'menu'      => $result['menu'] ?? null,
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
