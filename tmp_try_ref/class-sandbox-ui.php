<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * UI: banner countdown + bottone reset + lockdown menu admin.
 * Il banner appare:
 *   - nel builder (admin admin.php?page=olobuild)
 *   - nel frontend (homepage che renderizza il template del visitatore)
 *
 * Il banner NON appare per admin reali (che vedono normalmente l'interfaccia).
 */
class Olo_Sandbox_UI {

    public function __construct() {
        // Asset banner
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin' ] );

        // Mount banner
        add_action( 'wp_footer', [ $this, 'render_banner' ] );
        add_action( 'admin_footer', [ $this, 'render_banner' ] );

        // Overlay demo per pagine admin Olobuild non-sandbox (SEO, cookie, popup, ecc.)
        add_action( 'admin_footer', [ $this, 'render_demo_overlay' ] );

        // Banner "Demo: N tile · versione completa ~100" dentro il builder Vue
        add_action( 'admin_footer', [ $this, 'render_tile_count_banner' ] );

        // Nascondi chrome TT4 sulla front page
        add_action( 'wp_head', [ $this, 'hide_tt4_chrome_on_front_page' ], 999 );

        // Lockdown menu admin per guest
        add_action( 'admin_menu', [ $this, 'lockdown_admin_menu' ], 999 );
        add_action( 'admin_bar_menu', [ $this, 'lockdown_admin_bar' ], 999 );
    }

    public function enqueue_frontend() {
        if ( ! $this->show_for_current_user() ) return;
        $this->enqueue_assets();
    }

    public function enqueue_admin() {
        if ( ! $this->show_for_current_user() ) return;
        $this->enqueue_assets();
    }

    private function enqueue_assets() {
        wp_enqueue_style(
            'olo-sandbox-banner',
            OLO_SANDBOX_URL . 'assets/css/sandbox.css',
            [],
            OLO_SANDBOX_VERSION
        );
        wp_enqueue_script(
            'olo-sandbox-banner',
            OLO_SANDBOX_URL . 'assets/js/sandbox.js',
            [],
            OLO_SANDBOX_VERSION,
            true
        );
        $session = Olo_Sandbox_Session::current();
        if ( is_array( $session ) && ! empty( $session['last_seen_at'] ) ) {
            $session['last_seen_unix'] = (int) strtotime( $session['last_seen_at'] . ' UTC' );
        }
        // Logo Olobuild ufficiale (versione 1000px, fornita dall'utente)
        $logo_url = OLO_SANDBOX_URL . 'assets/img/olobuild-logo.png';
        wp_localize_script( 'olo-sandbox-banner', 'oloSandboxData', [
            'statusUrl'  => rest_url( 'olo-sandbox/v1/status' ),
            'resetUrl'   => rest_url( 'olo-sandbox/v1/reset' ),
            'nonce'      => wp_create_nonce( 'wp_rest' ),
            'ttl'        => Olo_Sandbox_Config::session_ttl(),
            'now'        => time(),
            'session'    => $session,
            'logoUrl'    => $logo_url,
            'builderUrl' => admin_url( 'admin.php?page=olobuilder-templates&template_id=' . ( $session['template_id'] ?? 0 ) ),
            'tileCount'  => count( Olo_Sandbox_Config::allowed_tiles() ),
            'fullTileCount' => '~100',
            'downloadUrl'   => 'https://olotheme.com/olobuild/',
            'i18n'       => [
                'title'         => __( 'Sandbox personale', 'olo-sandbox' ),
                'expires_in'    => __( 'Si resetta fra', 'olo-sandbox' ),
                'reset_button'  => __( 'Resetta tutto', 'olo-sandbox' ),
                'reset_confirm' => __( 'Sei sicuro di voler resettare la pagina al template originale?', 'olo-sandbox' ),
                'reset_done'    => __( 'Reset completato', 'olo-sandbox' ),
                'open_builder'  => __( 'Apri il builder', 'olo-sandbox' ),
                'hours_short'   => __( 'h', 'olo-sandbox' ),
                'minutes_short' => __( 'm', 'olo-sandbox' ),
                'seconds_short' => __( 's', 'olo-sandbox' ),
                'tile_info'     => __( 'tile demo · ~100 nella versione completa', 'olo-sandbox' ),
                'download'      => __( 'Scarica gratis', 'olo-sandbox' ),
            ],
        ] );
    }

    /**
     * Banner non va mostrato dentro la pagina builder Vue (`?page=olobuilder-templates`)
     * perché coprirebbe la toolbar Olobuild (risoluzioni, salva, ecc.).
     * Resta visibile su frontend e su dashboard Olobuild.
     */
    private function is_builder_inner_page() {
        if ( ! is_admin() ) return false;
        $page = isset( $_GET['page'] ) ? (string) $_GET['page'] : '';
        return in_array( $page, [ 'olobuilder-templates', 'olobuilder-settings', 'olobuilder-config' ], true );
    }

    public function render_banner() {
        if ( ! $this->show_for_current_user() ) return;
        // Banner SOLO sul frontend: in wp-admin la UI Olobuild ha già toolbar/breadcrumbs
        // proprie, sovrapporsi creerebbe rumore + tagli visivi.
        if ( is_admin() ) return;
        echo '<div id="olo-sandbox-banner" aria-live="polite"></div>';
    }

    /**
     * Banner sticky in cima alla sidebar tile del builder: "Demo N/100+ tile",
     * con link "Scarica gratis Olobuild". Iniettato via JS perché la sidebar
     * è renderizzata da Vue dopo il caricamento DOM.
     */
    public function render_tile_count_banner() {
        if ( ! $this->show_for_current_user() ) return;
        $page = isset( $_GET['page'] ) ? (string) $_GET['page'] : '';
        if ( $page !== 'olobuilder-templates' ) return;

        $tile_count = count( Olo_Sandbox_Config::allowed_tiles() );
        $cta_url    = 'https://olotheme.com/olobuild/';
        $title      = esc_js( sprintf( __( 'Demo: %d tile · versione completa ~100', 'olo-sandbox' ), $tile_count ) );
        $cta_text   = esc_js( __( 'Scarica gratis →', 'olo-sandbox' ) );
        ?>
        <style id="olo-sandbox-tile-banner-css">
            .olo-sandbox-tile-banner {
                background: linear-gradient(135deg, #f97316, #ef4444);
                color: #fff;
                padding: 10px 14px;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                font-size: 12px;
                line-height: 1.35;
                font-weight: 500;
                border-radius: 8px;
                margin: 8px;
                display: flex;
                flex-direction: column;
                gap: 6px;
                box-shadow: 0 2px 8px rgba(239, 68, 68, 0.25);
            }
            .olo-sandbox-tile-banner b { font-weight: 700; display: block; font-size: 13px; }
            .olo-sandbox-tile-banner a {
                background: rgba(255, 255, 255, 0.2);
                color: #fff !important;
                padding: 5px 10px;
                border-radius: 5px;
                text-decoration: none;
                text-align: center;
                font-weight: 600;
                font-size: 11px;
                transition: background 0.15s;
            }
            .olo-sandbox-tile-banner a:hover {
                background: rgba(255, 255, 255, 0.32);
            }
        </style>
        <script id="olo-sandbox-tile-banner-js">
        (function(){
            var TITLE   = <?php echo wp_json_encode( $title ); ?>;
            var CTA     = <?php echo wp_json_encode( $cta_text ); ?>;
            var CTA_URL = <?php echo wp_json_encode( $cta_url ); ?>;

            function inject() {
                if (document.getElementById('olo-sandbox-tile-banner')) return true;
                // Cerca la sidebar tile dentro l'app Vue. Selettori candidati
                // (cambiano nel tempo): proviamo i più probabili.
                var sidebar =
                    document.querySelector('.mb-tile-sidebar') ||
                    document.querySelector('.tile-sidebar') ||
                    document.querySelector('[class*="TileSidebar"]') ||
                    document.querySelector('.mb-w-\\[280px\\]') ||
                    document.querySelector('aside[class*="sidebar"]');
                if (!sidebar) return false;
                var el = document.createElement('div');
                el.id = 'olo-sandbox-tile-banner';
                el.className = 'olo-sandbox-tile-banner';
                el.innerHTML = '<b>' + TITLE + '</b><a href="' + CTA_URL + '" target="_blank" rel="noopener">' + CTA + '</a>';
                sidebar.insertBefore(el, sidebar.firstChild);
                return true;
            }

            // Vue monta in modo asincrono → polling fino a 6 secondi
            var attempts = 0;
            var iv = setInterval(function(){
                attempts++;
                if (inject() || attempts > 30) clearInterval(iv);
            }, 200);
        })();
        </script>
        <?php
    }

    /**
     * Overlay demo: full-screen sopra le pagine admin Olobuild "extra".
     * Flag impostato da Olo_Sandbox_Caps::restrict_admin_pages.
     * Mostra CTA verso olotheme.com — la dashboard sotto resta cliccabile
     * solo via i bottoni "Torna alla dashboard"/"Scarica".
     */
    public function render_demo_overlay() {
        if ( empty( $GLOBALS['olo_sandbox_demo_locked'] ) ) return;
        $logo_url = OLO_SANDBOX_URL . 'assets/img/olobuild-logo.png';
        $dash_url = admin_url( 'admin.php?page=olobuild' );
        $cta_url  = 'https://olotheme.com/olobuild/';
        ?>
        <div id="olo-sandbox-demo-overlay" role="dialog" aria-modal="true" aria-labelledby="olo-sandbox-demo-title">
            <div class="olo-sandbox-demo-card">
                <img src="<?php echo esc_url( $logo_url ); ?>" alt="Olobuild" class="olo-sandbox-demo-logo" />
                <div class="olo-sandbox-demo-tag"><?php echo esc_html__( 'Versione gratuita', 'olo-sandbox' ); ?></div>
                <h2 id="olo-sandbox-demo-title"><?php echo esc_html__( 'Tutto già pronto nella versione gratuita', 'olo-sandbox' ); ?></h2>
                <p><?php echo esc_html__( "Quest'area di configurazione (SEO, cookie consent, popup, performance, redirect, analytics e tutto il resto) è già completa e funzionante nella versione gratuita di Olobuild. Scaricala sul tuo WordPress per usarla.", 'olo-sandbox' ); ?></p>
                <div class="olo-sandbox-demo-actions">
                    <a class="olo-sandbox-demo-btn olo-sandbox-demo-btn--primary" href="<?php echo esc_url( $cta_url ); ?>" target="_blank" rel="noopener">
                        <?php echo esc_html__( 'Scarica gratis Olobuild', 'olo-sandbox' ); ?>
                        <span aria-hidden="true">→</span>
                    </a>
                    <a class="olo-sandbox-demo-btn olo-sandbox-demo-btn--ghost" href="<?php echo esc_url( $dash_url ); ?>">
                        <?php echo esc_html__( 'Torna alla dashboard', 'olo-sandbox' ); ?>
                    </a>
                </div>
                <p class="olo-sandbox-demo-foot"><?php echo esc_html__( 'Hai sempre la modifica completa delle pagine — il builder è tutto attivo qui sopra.', 'olo-sandbox' ); ?></p>
            </div>
        </div>
        <style>
            #olo-sandbox-demo-overlay {
                position: fixed;
                inset: 32px 0 0 160px;
                background: rgba(15, 23, 42, 0.78);
                backdrop-filter: blur(6px);
                -webkit-backdrop-filter: blur(6px);
                z-index: 99999;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 24px;
                animation: olo-sb-fadein 0.25s ease-out;
            }
            @media (max-width: 960px) {
                #olo-sandbox-demo-overlay { inset: 46px 0 0 36px; }
            }
            @media (max-width: 600px) {
                #olo-sandbox-demo-overlay { inset: 46px 0 0 0; }
            }
            @keyframes olo-sb-fadein { from { opacity: 0 } to { opacity: 1 } }
            .olo-sandbox-demo-card {
                background: #fff;
                border-radius: 16px;
                padding: 40px;
                max-width: 520px;
                width: 100%;
                text-align: center;
                box-shadow: 0 20px 60px rgba(0,0,0,0.4);
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            }
            .olo-sandbox-demo-logo {
                height: 40px;
                width: auto;
                margin-bottom: 20px;
            }
            .olo-sandbox-demo-tag {
                display: inline-block;
                background: #ecfdf5;
                color: #047857;
                font-size: 11px;
                font-weight: 600;
                padding: 4px 10px;
                border-radius: 999px;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                margin-bottom: 12px;
            }
            .olo-sandbox-demo-card h2 {
                font-size: 24px;
                margin: 0 0 12px;
                color: #0f172a;
                font-weight: 700;
                line-height: 1.3;
            }
            .olo-sandbox-demo-card p {
                font-size: 15px;
                color: #475569;
                line-height: 1.55;
                margin: 0 0 24px;
            }
            .olo-sandbox-demo-actions {
                display: flex;
                flex-direction: column;
                gap: 10px;
                margin-bottom: 20px;
            }
            .olo-sandbox-demo-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                padding: 12px 24px;
                border-radius: 8px;
                font-weight: 600;
                text-decoration: none;
                font-size: 14px;
                transition: all 0.15s ease;
            }
            .olo-sandbox-demo-btn--primary {
                background: #ef4444;
                color: #fff !important;
            }
            .olo-sandbox-demo-btn--primary:hover {
                background: #dc2626;
                transform: translateY(-1px);
                box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
            }
            .olo-sandbox-demo-btn--ghost {
                background: transparent;
                color: #475569 !important;
                border: 1px solid #e2e8f0;
            }
            .olo-sandbox-demo-btn--ghost:hover {
                background: #f8fafc;
                color: #0f172a !important;
            }
            .olo-sandbox-demo-foot {
                font-size: 12px !important;
                color: #94a3b8 !important;
                margin: 0 !important;
            }
            /* Sblur contenuto sottostante per leggibilità banner sopra */
            #wpcontent { position: relative; }
        </style>
        <?php
    }

    /**
     * Sulla front page nasconde header/footer/title del tema TT4 — il template
     * Olobuild è autosufficiente e duplicarlo è solo rumore visivo.
     */
    public function hide_tt4_chrome_on_front_page() {
        if ( ! is_front_page() ) return;
        // Applichiamo a chiunque (admin + guest): la sandbox è autosufficiente,
        // header/footer/title del tema TT4 sono solo rumore.
        // Quando l'admin attiverà un header Olobuild globale, Olobuild lo
        // renderizzerà comunque (questo CSS non interferisce).
        echo '<style id="olo-sandbox-hide-tt4">
            .wp-site-blocks > header:not(.olo-site-header),
            .wp-site-blocks > footer:not(.olo-site-footer),
            .wp-block-template-part[data-type="header"],
            .wp-block-template-part[data-type="footer"],
            .wp-block-post-title,
            .wp-block-template-part header:not(.olo-site-header),
            .wp-block-template-part footer:not(.olo-site-footer) {
                display: none !important;
            }
            body.home main,
            body.home .wp-block-group { padding-top: 0 !important; padding-bottom: 0 !important; }
        </style>';
    }

    /**
     * Mostra banner solo per guest sandbox (admin reali esclusi).
     */
    private function show_for_current_user() {
        $user = wp_get_current_user();
        return $user && $user->ID && $user->user_login === Olo_Sandbox_Config::GUEST_LOGIN;
    }

    /**
     * Rimuove tutte le voci di menu admin tranne Olobuild per il guest user.
     */
    public function lockdown_admin_menu() {
        $user = wp_get_current_user();
        if ( ! $user || $user->user_login !== Olo_Sandbox_Config::GUEST_LOGIN ) {
            return;
        }
        global $menu, $submenu;
        if ( ! is_array( $menu ) ) return;

        foreach ( $menu as $key => $item ) {
            $slug = $item[2] ?? '';
            if ( strpos( $slug, 'olobuild' ) === 0 || strpos( $slug, 'olo-' ) === 0 ) {
                continue;
            }
            unset( $menu[ $key ] );
        }
    }

    /**
     * Nasconde admin bar items per guest (logo WP, commenti, new content, ecc.).
     */
    public function lockdown_admin_bar( $wp_admin_bar ) {
        $user = wp_get_current_user();
        if ( ! $user || $user->user_login !== Olo_Sandbox_Config::GUEST_LOGIN ) {
            return;
        }
        foreach ( [ 'wp-logo', 'comments', 'new-content', 'updates', 'user-info', 'edit-profile', 'site-name' ] as $id ) {
            $wp_admin_bar->remove_node( $id );
        }
    }
}
