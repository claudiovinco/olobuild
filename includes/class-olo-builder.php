<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Builder {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init();
    }

    private function init() {
        // Admin menu
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );

        // Settings page (API keys)
        add_action( 'admin_init', [ $this, 'register_settings' ] );

        // REST endpoints for Configurazione page
        add_action( 'rest_api_init', [ $this, 'register_settings_rest_routes' ] );

        // Enqueue scripts only on builder page
        add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );

        // Invalida cache mappa meta_keys quando si salva o elimina un post
        add_action( 'save_post',    function () { delete_transient( 'olo_meta_keys_map_v1' ); } );
        add_action( 'deleted_post', function () { delete_transient( 'olo_meta_keys_map_v1' ); } );

        // Submenu icons via CSS
        add_action( 'admin_head', [ $this, 'admin_submenu_icons' ] );

        // Admin body class for Olobuild shell layout
        add_filter( 'admin_body_class', [ $this, 'admin_body_class' ] );

        // Initialize REST API
        $rest_api = new Olo_Rest_Api();
        $rest_api->init();

        // Initialize frontend renderer (shortcode)
        $frontend = new Olo_Frontend_Renderer();
        $frontend->init();

        // Initialize page integration (Edit with Olobuild buttons)
        $page_integration = new Olo_Page_Integration();
        $page_integration->init();

        // Initialize header integration
        $header = new Olo_Header_Integration();
        $header->init();

        // Initialize footer integration
        $footer = new Olo_Footer_Integration();
        $footer->init();

        // Initialize single template integration
        $single = new Olo_Single_Integration();
        $single->init();

        // Initialize archive template integration
        Olo_Archive_Integration::instance();

        // Initialize 404 template integration
        Olo_404_Integration::instance();

        // Initialize search results template integration
        Olo_Search_Results_Integration::instance();

        // Builder iframe page (live preview)
        add_action( 'template_redirect', [ $this, 'serve_builder_iframe' ] );

        // Register core tiles
        $this->register_core_tiles();

        // LiveSearch REST endpoint
        add_action( 'rest_api_init', [ 'Olo_LiveSearch_Tile', 'register_rest_routes' ] );

        // Unsplash integration
        $unsplash = new Olo_Unsplash();
        $unsplash->init();

        // Pexels integration
        $pexels = new Olo_Pexels();
        $pexels->init();

        // Pixabay integration
        $pixabay = new Olo_Pixabay();
        $pixabay->init();

        // Openverse integration
        $openverse = new Olo_Openverse();
        $openverse->init();

        // Freesound integration
        $freesound = new Olo_Freesound();
        $freesound->init();
    }

    /**
     * Serve the builder iframe page for live preview.
     *
     * Comportamento context-aware:
     *
     *   1. Se siamo su un permalink reale (`is_singular()`), NON serviamo il
     *      template standalone: lasciamo WP renderizzare la pagina con il suo
     *      tema (header/footer/regole template Olobuild) e sostituiamo SOLO
     *      il content del post con un placeholder che il bridge JS aggiorna
     *      via postMessage. Così il preview mostra header/footer reali esattamente
     *      come sono in prod (incluso `_olo_header_id` per-page o regole).
     *
     *   2. Se Vue ha passato `olo_tpl=<id>` e siamo sulla home (caso default
     *      quando il template editato è una "page"), facciamo lookup automatico
     *      di un post che usa quel template e ridirezioniamo all'iframe contestuale.
     *
     *   3. Altrimenti (home senza match, archive, ecc.) serviamo il template
     *      standalone come fallback (status quo).
     */
    public function serve_builder_iframe() {
        if ( empty( $_GET['olo_builder_iframe'] ) ) return;
        if ( ! current_user_can( 'edit_pages' ) ) {
            // Diagnostica: distingue tra "non loggato" e "loggato ma senza permessi"
            $uid = get_current_user_id();
            if ( ! $uid ) {
                $msg = '<h2>Sessione scaduta</h2><p>Il browser non ha inviato il cookie di autenticazione all\'iframe del builder.</p>'
                     . '<p><strong>Cosa fare:</strong></p><ul>'
                     . '<li>Ricarica la pagina del builder con <kbd>Ctrl+Shift+R</kbd></li>'
                     . '<li>Se persiste, esegui logout/login da WordPress</li>'
                     . '<li>Verifica che i cookie di terze parti non siano bloccati nel browser</li>'
                     . '</ul>';
            } else {
                $user = wp_get_current_user();
                $roles = implode( ', ', (array) $user->roles );
                $msg = '<h2>Permessi insufficienti</h2><p>L\'utente <strong>' . esc_html( $user->user_login ) . '</strong> (ruoli: ' . esc_html( $roles ) . ') non ha la capability <code>edit_pages</code>.</p>'
                     . '<p>Aggiungi il ruolo Editor o Administrator all\'utente, oppure usa un plugin di gestione capability.</p>';
            }
            wp_die( $msg, 'Olobuild Builder', [ 'response' => 403 ] );
        }

        $tpl_id = isset( $_GET['olo_tpl'] ) ? (int) $_GET['olo_tpl'] : 0;

        // Modalità inline (1): siamo già su un permalink reale.
        if ( is_singular() ) {
            $this->setup_inline_preview_mode();
            return;
        }

        // Modalità inline (2): redirect automatico al primo post associato al template.
        if ( $tpl_id ) {
            $associated = get_posts( [
                'post_type'      => 'any',
                'meta_key'       => '_olo_template_id',
                'meta_value'     => $tpl_id,
                'posts_per_page' => 1,
                'fields'         => 'ids',
                'post_status'    => [ 'publish', 'private', 'draft' ],
            ] );
            if ( ! empty( $associated ) ) {
                $url = get_permalink( $associated[0] );
                if ( $url ) {
                    $url = add_query_arg( [
                        'olo_builder_iframe' => 1,
                        'olo_tpl'            => $tpl_id,
                    ], $url );
                    wp_safe_redirect( $url );
                    exit;
                }
            }
        }

        // Fallback: standalone iframe template (home root, no associated post).
        include OLO_PATH . 'templates/builder-iframe.php';
        exit;
    }

    /**
     * Inline mode: WP renderizza la pagina; sostituiamo content con il placeholder
     * del bridge e iniettiamo gli stessi asset del builder-iframe.php.
     */
    private function setup_inline_preview_mode() {
        // Sostituisce il content del post con il placeholder che bridge.js aggiornerà.
        add_filter( 'the_content', [ $this, 'inline_preview_replace_content' ], 999 );

        // Sostituisce eventuale block `core/post-content` (block theme).
        add_filter( 'render_block', [ $this, 'inline_preview_replace_post_content_block' ], 100, 2 );

        // CSS inline in head (mode-specific) + asset bridge in footer.
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_inline_preview_assets' ], 100 );
        add_action( 'wp_head', [ $this, 'print_inline_preview_styles' ], 99 );

        // Quando l'utente sta editando un template di tipo header o footer
        // nel builder iframe, evitiamo il DOPPIO render (header/footer dal tema
        // + template in editing renderizzato come body): definiamo una costante
        // che Olo_Header_Integration/Olo_Footer_Integration leggono per
        // skippare il rendering dell'header/footer attivo.
        $tpl_id = isset( $_GET['olo_tpl'] ) ? (int) $_GET['olo_tpl'] : 0;
        if ( $tpl_id && class_exists( 'Olo_Database' ) ) {
            $db = new Olo_Database();
            $tpl = $db->get_template( $tpl_id );
            $editing_type = is_array( $tpl ) ? ( $tpl['type'] ?? '' ) : '';
            if ( $editing_type === 'header' && ! defined( 'OLO_BUILDER_EDITING_HEADER' ) ) {
                define( 'OLO_BUILDER_EDITING_HEADER', true );
            }
            if ( $editing_type === 'footer' && ! defined( 'OLO_BUILDER_EDITING_FOOTER' ) ) {
                define( 'OLO_BUILDER_EDITING_FOOTER', true );
            }
        }

        // Disabilita admin bar e altri overlay
        show_admin_bar( false );
    }

    /**
     * Empty-state HTML mostrato nell'iframe del builder durante il boot iniziale,
     * quando il template è interamente vuoto, oppure quando solo il body è vuoto
     * ma header/footer sono presenti. Markup unico = UX coerente nei 3 casi.
     *
     * Uso: server-side (templates/builder-iframe.php, filtri inline_preview_*,
     * REST builder_render); il bridge JS replica lo stesso markup via oloData.
     */
    public static function get_iframe_empty_html() {
        $title       = esc_html__( 'Pagina vuota', 'olobuild' );
        $text        = esc_html__( 'Aggiungi un modulo o scegli un layout per iniziare', 'olobuild' );
        $add_module  = esc_html__( 'Aggiungi modulo', 'olobuild' );
        $add_row     = esc_html__( 'Scegli layout', 'olobuild' );
        return '<div class="olo-iframe-empty">'
             . '<div class="olo-iframe-empty-card">'
             . '<h3 class="olo-iframe-empty-title">' . $title . '</h3>'
             . '<p class="olo-iframe-empty-text">' . $text . '</p>'
             . '<div class="olo-iframe-empty-actions">'
             . '<button type="button" class="olo-iframe-empty-btn" data-olo-empty-action="add-module" aria-label="' . $add_module . '">'
             . '<span class="olo-iframe-empty-btn-icon">'
             . '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
             . '<rect x="3" y="3" width="18" height="18" rx="2" stroke-dasharray="3 3"/>'
             . '<path d="M12 8v8M8 12h8"/>'
             . '</svg>'
             . '</span>'
             . '<span class="olo-iframe-empty-btn-label">' . $add_module . '</span>'
             . '</button>'
             . '<button type="button" class="olo-iframe-empty-btn" data-olo-empty-action="add-row" aria-label="' . $add_row . '">'
             . '<span class="olo-iframe-empty-btn-icon">'
             . '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
             . '<rect x="3" y="3" width="7" height="7" rx="1"/>'
             . '<rect x="14" y="3" width="7" height="7" rx="1"/>'
             . '<rect x="3" y="14" width="7" height="7" rx="1"/>'
             . '<rect x="14" y="14" width="7" height="7" rx="1"/>'
             . '</svg>'
             . '</span>'
             . '<span class="olo-iframe-empty-btn-label">' . $add_row . '</span>'
             . '</button>'
             . '</div>'
             . '</div>'
             . '</div>';
    }

    /** @internal Filter callback: rimpiazza the_content con placeholder. */
    public function inline_preview_replace_content( $content ) {
        return '<div id="olo-iframe-root">' . self::get_iframe_empty_html() . '</div>';
    }

    /** @internal Filter callback: rimpiazza core/post-content block (block themes). */
    public function inline_preview_replace_post_content_block( $html, $block ) {
        if ( ( $block['blockName'] ?? '' ) === 'core/post-content' ) {
            return '<div id="olo-iframe-root">' . self::get_iframe_empty_html() . '</div>';
        }
        return $html;
    }

    /** @internal Enqueue degli stessi asset di templates/builder-iframe.php. */
    public function enqueue_inline_preview_assets() {
        // Core CSS (mirror del builder-iframe.php)
        wp_enqueue_style( 'olo-uikit-inline', OLO_URL . 'assets/vendor/uikit/css/uikit.min.css', [], OLO_VERSION );
        wp_enqueue_style( 'olo-frontend-inline', OLO_URL . 'assets/css/frontend.css', [], OLO_VERSION );
        wp_enqueue_style( 'olo-iframe-builder-inline', OLO_URL . 'assets/css/iframe-builder.css', [], OLO_VERSION );
        // Tile-specific CSS (mirror del builder-iframe.php)
        wp_enqueue_style( 'olo-proslider-css', OLO_URL . 'assets/css/olo-proslider.css', [], OLO_VERSION );
        // Core JS
        wp_enqueue_script( 'olo-uikit-inline', OLO_URL . 'assets/vendor/uikit/js/uikit.min.js', [], OLO_VERSION, true );
        wp_enqueue_script( 'olo-uikit-icons-inline', OLO_URL . 'assets/vendor/uikit/js/uikit-icons.min.js', [ 'olo-uikit-inline' ], OLO_VERSION, true );
        // Tile runtimes (proslider, postgrid, map, ecc.)
        if ( file_exists( OLO_PATH . 'assets/js/olo-proslider.js' ) ) {
            wp_enqueue_script( 'olo-proslider-js', OLO_URL . 'assets/js/olo-proslider.js', [], OLO_VERSION, true );
        }
        if ( file_exists( OLO_PATH . 'assets/js/olo-postgrid.js' ) ) {
            wp_enqueue_script( 'olo-postgrid-js', OLO_URL . 'assets/js/olo-postgrid.js', [], OLO_VERSION, true );
        }
        if ( file_exists( OLO_PATH . 'assets/js/olo-utils.js' ) ) {
            wp_enqueue_script( 'olo-utils', OLO_URL . 'assets/js/olo-utils.js', [], OLO_VERSION, true );
        }
        // Bridge: deve essere DOPO ogni runtime (postMessage receiver)
        wp_enqueue_script( 'olo-iframe-bridge', OLO_URL . 'assets/js/iframe-bridge.js', [], OLO_VERSION, true );
        // Mode flag letto dal bridge.js → segnala al parent (Vue useIframeBridge) che
        // questa è una pagina WP reale, header/footer NON vanno re-iniettati.
        wp_add_inline_script( 'olo-iframe-bridge', "window.OLO_IFRAME_MODE='inline';", 'before' );
    }

    /** @internal Stile inline per la modalità preview (mirror del builder-iframe.php). */
    public function print_inline_preview_styles() {
        ?>
        <style id="olo-inline-preview-styles">
        #olo-iframe-root { min-height: 60vh; }
        .olo-iframe-empty { display: flex; align-items: center; justify-content: center; min-height: 40vh; color: #9CA3AF; font-family: system-ui, sans-serif; font-size: 14px; }
        .olo-site-header.olo-header-sticky,
        .olo-site-header.olo-header-classic.olo-header-sticky,
        .olo-sticky-cover, .olo-sticky-reveal {
            position: relative !important; top: auto !important; z-index: auto !important;
        }
        .olo-template { width: 100% !important; position: static !important; left: auto !important; transform: none !important; }
        .olo-floatingpanel, .olo-fp-wrapper { scroll-margin-top: 80px; }
        /* Hide WP admin bar gap if any */
        html { margin-top: 0 !important; }
        </style>
        <?php
    }

    public function admin_menu() {
        // Use 'manage_options' as primary capability — admins always have it.
        // 'edit_posts' can be missing on sites with custom role plugins (Tutor LMS, etc.)
        add_menu_page(
            __( 'Olobuild', 'olobuild' ),
            __( 'Olobuild', 'olobuild' ),
            'manage_options',
            'olobuild',
            [ $this, 'render_dashboard_page' ],
            OLO_URL . 'assets/img/ob-menu-v2.png',
            30
        );

        // Override the auto-generated first submenu item
        add_submenu_page(
            'olobuild',
            __( 'Dashboard', 'olobuild' ),
            __( 'Dashboard', 'olobuild' ),
            'manage_options',
            'olobuild',
            [ $this, 'render_dashboard_page' ]
        );

        add_submenu_page(
            'olobuild',
            __( 'Gestione Template', 'olobuild' ),
            __( 'Gestione Template', 'olobuild' ),
            'manage_options',
            'olobuilder-templates',
            [ $this, 'render_builder_page' ]
        );

        add_submenu_page(
            'olobuild',
            __( 'Configurazione', 'olobuild' ),
            __( 'Configurazione', 'olobuild' ),
            'manage_options',
            'olobuilder-settings',
            [ $this, 'render_configurazione_page' ]
        );

        add_submenu_page(
            'olobuild',
            __( 'Ricerca Media', 'olobuild' ),
            __( 'Ricerca Media', 'olobuild' ),
            'upload_files',
            'olo-media-search',
            [ 'Olo_Media_Search', 'render_page' ]
        );

        add_submenu_page(
            'olobuild',
            __( 'Invii Form', 'olobuild' ),
            __( 'Invii Form', 'olobuild' ),
            'manage_options',
            'olo-form-submissions',
            [ 'Olo_Form_Submissions', 'render_page' ]
        );

        add_submenu_page(
            'olobuild',
            __( 'Newsletter', 'olobuild' ),
            __( 'Newsletter', 'olobuild' ),
            'manage_options',
            'olo-newsletter',
            [ 'Olo_Newsletter', 'render_page' ]
        );
    }

    /**
     * Inject CSS icons for each Olobuild submenu item using dashicons.
     */
    public function admin_submenu_icons() {
        $screen = get_current_screen();
        if ( ! $screen ) return;
        // Only on Olobuild pages
        $id = $screen->id;
        if ( ! str_contains( $id, 'olobuild' )
            && ! str_contains( $id, 'olo-' ) ) {
            return;
        }

        $icons = [
            'olobuild'          => '\f226', // dashicons-dashboard
            'olobuilder-templates'=> '\f538', // dashicons-layout
            'olobuilder-settings' => '\f107', // dashicons-admin-generic
            'olo-media-search'    => '\f128', // dashicons-format-image
            'olo-form-submissions'=> '\f466', // dashicons-email-alt
            'olo-newsletter'      => '\f465', // dashicons-email
            'olo-analytics'       => '\f185', // dashicons-chart-bar
            'olo-cookie-consent'  => '\f332', // dashicons-shield
            'olo-seo'             => '\f179', // dashicons-search
            'olo-redirects'       => '\f503', // dashicons-randomize
            'olo-performance'     => '\f228', // dashicons-performance
            'olo-woo-templates'   => '\f174', // dashicons-cart
            'olo-global-popups'   => '\f479', // dashicons-admin-page
            'olo-role-manager'    => '\f110', // dashicons-admin-users
            'olo-import-export'   => '\f316', // dashicons-download
            'olo-white-label'     => '\f323', // dashicons-tag
            'olo-tools'           => '\f533', // dashicons-admin-tools
        ];

        echo '<style>';
        foreach ( $icons as $slug => $code ) {
            echo '#adminmenu .wp-submenu a[href*="page=' . $slug . '"]::before{';
            echo 'font-family:dashicons;content:"' . $code . '";margin-right:6px;font-size:16px;vertical-align:middle;opacity:.7;';
            echo '}';
        }
        echo '</style>';
    }

    private function detect_theme_colors() {
        $bg_color   = '';
        $text_color = '';

        // Strategy 1: Block theme with theme.json (WP 5.9+)
        if ( function_exists( 'wp_get_global_styles' ) ) {
            $colors = wp_get_global_styles(
                [ 'color' ],
                [ 'transforms' => [ 'resolve-variables' ] ]
            );
            if ( ! empty( $colors['background'] ) ) $bg_color   = $colors['background'];
            if ( ! empty( $colors['text'] ) )        $text_color = $colors['text'];
        }

        // Strategy 2: Palette slug "base"/"contrast"
        if ( empty( $bg_color ) && function_exists( 'wp_get_global_settings' ) ) {
            $palette = wp_get_global_settings( [ 'color', 'palette', 'theme' ] );
            if ( is_array( $palette ) ) {
                foreach ( $palette as $item ) {
                    if ( $item['slug'] === 'base' && empty( $bg_color ) )       $bg_color   = $item['color'];
                    if ( $item['slug'] === 'contrast' && empty( $text_color ) ) $text_color = $item['color'];
                }
            }
        }

        // Strategy 3: Classic theme - get_background_color()
        if ( empty( $bg_color ) ) {
            $classic_bg = get_background_color();
            if ( ! empty( $classic_bg ) ) $bg_color = '#' . ltrim( $classic_bg, '#' );
        }

        // Final fallback
        return [
            'background' => $bg_color ?: '#ffffff',
            'text'       => $text_color ?: '#333333',
        ];
    }

    public function admin_enqueue_scripts( $hook ) {
        // Shared admin CSS for ALL Olobuild pages
        if ( str_contains( $hook, 'olobuild' ) || str_contains( $hook, 'olo-' ) ) {
            wp_enqueue_style(
                'olo-admin-css',
                OLO_URL . 'assets/css/olo-admin.css',
                [],
                OLO_VERSION
            );
        }

        // Cockpit CSS condiviso per tutte le pagine top-level che usano cockpit_shell_open()
        if ( in_array( $hook, self::cockpit_screen_ids(), true ) ) {
            wp_enqueue_style(
                'olo-cockpit-css',
                OLO_URL . 'assets/css/dashboard.css',
                [],
                OLO_VERSION
            );
        }

        // Submissions cockpit (Fase 3)
        if ( $hook === 'olobuild_page_olo-form-submissions' ) {
            wp_enqueue_script(
                'olo-submissions-js',
                OLO_URL . 'assets/js/olo-submissions.js',
                [],
                OLO_VERSION,
                true
            );
            wp_localize_script( 'olo-submissions-js', 'oloSubmissionsConfig', [
                'restUrl' => esc_url_raw( rest_url( 'olo/v1/' ) ),
                'nonce'   => wp_create_nonce( 'wp_rest' ),
                'i18n'    => [
                    'noResults'    => __( 'Nessun invio trovato.', 'olobuild' ),
                    'noResultsHint'=> __( 'Prova a cambiare filtro o aspetta nuovi messaggi.', 'olobuild' ),
                    'markRead'     => __( 'Segna come letto', 'olobuild' ),
                    'markUnread'   => __( 'Segna come non letto', 'olobuild' ),
                    'confirmDelete'=> __( "Eliminare definitivamente questo invio? L'azione non si può annullare.", 'olobuild' ),
                    'deleted'      => __( 'Invio eliminato', 'olobuild' ),
                    'deleteFailed' => __( 'Errore eliminazione', 'olobuild' ),
                    'loadFailed'   => __( 'Errore caricamento. Riprova.', 'olobuild' ),
                    'fields'       => __( 'Campi compilati', 'olobuild' ),
                    'metadata'     => __( 'Metadati', 'olobuild' ),
                    'ip'           => 'IP',
                    'userAgent'    => 'User Agent',
                    'submittedAt'  => __( 'Inviato il', 'olobuild' ),
                ],
            ] );
        }

        // Dashboard cockpit (toplevel_page_olobuild)
        if ( 'toplevel_page_olobuild' === $hook ) {
            wp_enqueue_style(
                'olo-dashboard-css',
                OLO_URL . 'assets/css/dashboard.css',
                [],
                OLO_VERSION
            );
            wp_enqueue_script(
                'olo-dashboard-js',
                OLO_URL . 'assets/js/dashboard.js',
                [],
                OLO_VERSION,
                true
            );

            // Boot data per evitare flash di skeleton
            $rest = new Olo_Rest_Api();
            $req_recent = new WP_REST_Request( 'GET' );
            $req_recent->set_query_params( [ 'limit' => 6 ] );
            $req_changelog = new WP_REST_Request( 'GET' );
            $req_changelog->set_query_params( [ 'limit' => 3 ] );

            $boot_kpis      = $rest->dashboard_kpis( null );
            $boot_recent    = $rest->dashboard_recent( $req_recent );
            $boot_changelog = $rest->dashboard_changelog( $req_changelog );

            $user_id = get_current_user_id();
            $user_prefs = get_user_meta( $user_id, 'olo_dashboard_prefs', true );
            if ( ! is_array( $user_prefs ) ) $user_prefs = [];
            $user_prefs = wp_parse_args( $user_prefs, [
                'pinned'      => [ 'tpl', 'cfg', 'media' ],
                'rail'        => 'expanded',
                'app_mode'    => true,
                'banners_off' => [],
            ] );

            wp_localize_script( 'olo-dashboard-js', 'oloDashboardData', [
                'restUrl'     => esc_url_raw( rest_url( 'olo/v1/' ) ),
                'adminUrl'    => admin_url(),
                'nonce'       => wp_create_nonce( 'wp_rest' ),
                'pluginUrl'   => OLO_URL,
                'version'     => OLO_VERSION,
                'prefs'       => $user_prefs,
                'searchIndex' => self::dashboard_search_index(),
                'boot' => [
                    'kpis'      => is_wp_error( $boot_kpis )      ? [] : $boot_kpis->get_data(),
                    'recent'    => is_wp_error( $boot_recent )    ? [] : $boot_recent->get_data(),
                    'changelog' => is_wp_error( $boot_changelog ) ? [] : $boot_changelog->get_data(),
                ],
                'i18n' => [
                    'live'         => __( 'live', 'olobuild' ),
                    'draft'        => __( 'bozza', 'olobuild' ),
                    'pin'          => __( 'Aggiungi ai preferiti', 'olobuild' ),
                    'unpin'        => __( 'Rimuovi dai preferiti', 'olobuild' ),
                    'expand'       => __( 'Espandi pannello', 'olobuild' ),
                    'collapse'     => __( 'Comprimi pannello', 'olobuild' ),
                    'searchPh'     => __( 'Cerca pagine, template, impostazioni…', 'olobuild' ),
                    'noResults'    => __( 'Nessun risultato', 'olobuild' ),
                    'emptyRecent'  => __( 'Nessuna attività recente. Crea o modifica una pagina per iniziare.', 'olobuild' ),
                    'creating'     => __( 'Creazione…', 'olobuild' ),
                    'untitled'     => __( 'Senza titolo', 'olobuild' ),
                    'newPageError' => __( 'Errore nella creazione della pagina. Riprova.', 'olobuild' ),
                ],
            ] );
            return;
        }

        // Configurazione page — admin Vue app
        if ( 'olobuild_page_olobuilder-settings' === $hook ) {
            // CSS is injected inline by Vite IIFE build + render_configurazione_page inline styles
            wp_enqueue_script(
                'olo-admin-settings-js',
                OLO_URL . 'assets/js/admin-settings.js',
                [],
                OLO_VERSION,
                true
            );

            $style_system = Olo_Style_System::instance();
            // Timestamp ultimo save (mostrato nella savebar). Ogni tab al suo POST aggiorna
            // l'option `olo_settings_last_saved`; questo è solo il bootstrap iniziale.
            $last_saved_ts = (int) get_option( 'olo_settings_last_saved', 0 );
            $last_saved_str = '';
            if ( $last_saved_ts > 0 ) {
                $diff = max( 0, time() - $last_saved_ts );
                if ( $diff < 60 ) {
                    $last_saved_str = __( 'pochi secondi fa', 'olobuild' );
                } elseif ( $diff < 3600 ) {
                    $last_saved_str = sprintf( __( '%d minuti fa', 'olobuild' ), intval( $diff / 60 ) );
                } else {
                    $last_saved_str = wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $last_saved_ts );
                }
            }
            wp_localize_script( 'olo-admin-settings-js', 'oloData', [
                'restUrl'           => esc_url_raw( rest_url( 'olo/v1' ) . '/' ),
                'nonce'             => wp_create_nonce( 'wp_rest' ),
                'ajaxUrl'           => admin_url( 'admin-ajax.php' ),
                'adminUrl'          => admin_url(),
                'siteUrl'           => home_url( '/' ),
                'pluginUrl'         => OLO_URL,
                'version'           => OLO_VERSION,
                'locale'            => olo_current_locale(),
                'translations'      => olo_get_translations_map(),
                'styles'            => $style_system->get_styles(),
                'presets'           => $style_system->get_presets(),
                'globalColors'      => $style_system->get_global_colors(),
                'globalTypography'  => $style_system->get_global_typography(),
                'settingsLastSaved' => $last_saved_str,
            ] );
            return;
        }

        if ( 'olobuild_page_olobuilder-templates' !== $hook ) {
            return;
        }

        // Load full WP media framework (needed for wp.media settings & templates)
        wp_enqueue_media();

        // Carica frontend.css anche nel builder admin: il canvas Vue usa .olo-template
        // e ha bisogno delle stesse regole, including @keyframes entrance animations.
        wp_enqueue_style(
            'olo-builder-frontend-styles',
            OLO_URL . 'assets/css/frontend.css',
            [],
            OLO_VERSION
        );

        // Cache-busting basato sul mtime reale dei file (oltre OLO_VERSION),
        // così ogni rebuild forza il reload del bundle anche se la versione
        // del plugin non è stata bumpata (utile in dev/staging).
        $css_path = OLO_PATH . 'assets/css/builder.css';
        $js_path  = OLO_PATH . 'assets/js/builder.js';
        $mtime    = file_exists( $js_path ) ? filemtime( $js_path ) : 0;
        $css_ver  = OLO_VERSION . '.' . ( file_exists( $css_path ) ? filemtime( $css_path ) : 0 );
        // OLO_VERSION + mtime sono sufficienti: ogni build aggiorna mtime,
        // ogni release aggiorna OLO_VERSION → il browser riscarica solo quando
        // serve. Aggiungere time() come prima rompeva la cache del browser
        // ad ogni F5 in admin (4 MB di builder.js riscaricati senza motivo).
        // Per forzare il reload in dev: aggiungere `?olo_no_cache=1`.
        $js_ver   = OLO_VERSION . '.' . $mtime;
        if ( isset( $_GET['olo_no_cache'] ) ) {
            $js_ver .= '.' . time();
        }

        // CSS is bundled inline in builder.js by Vite (iife mode injects <style> tags at runtime).
        // Enqueue only if a separate builder.css exists (e.g. if build config switches to extracted CSS).
        if ( file_exists( $css_path ) ) {
            wp_enqueue_style(
                'olobuilder-css',
                OLO_URL . 'assets/css/builder.css',
                [],
                $css_ver
            );
        }

        wp_enqueue_script(
            'olobuilder-js',
            OLO_URL . 'assets/js/builder.js',
            [ 'media-views' ],
            $js_ver,
            true
        );

        // Auto-thumbnail capture: listener su `olobuild:saved` → html2canvas → upload
        wp_enqueue_script(
            'olo-thumb-capture',
            OLO_URL . 'assets/js/olo-thumb-capture.js',
            [],
            OLO_VERSION,
            true
        );
        wp_localize_script( 'olo-thumb-capture', 'oloThumbConfig', [
            'restUrl'   => esc_url_raw( rest_url( 'olo/v1/' ) ),
            'nonce'     => wp_create_nonce( 'wp_rest' ),
            'vendorUrl' => OLO_URL . 'assets/vendor/html2canvas.min.js',
            'debug'     => defined( 'WP_DEBUG' ) && WP_DEBUG,
        ] );

        $style_system = Olo_Style_System::instance();

        // Inject primary color as CSS custom properties so admin UI can use it
        $styles   = $style_system->get_styles();
        $primary  = $styles['colors']['primary'] ?? '#6366F1';
        $hex      = ltrim( $primary, '#' );
        $r        = hexdec( substr( $hex, 0, 2 ) );
        $g        = hexdec( substr( $hex, 2, 2 ) );
        $b        = hexdec( substr( $hex, 4, 2 ) );
        wp_add_inline_style( 'olobuilder-css',
            ':root { --olo-color-primary: ' . esc_attr( $primary ) . '; --olo-primary-rgb: ' . "$r $g $b" . '; }'
        );

        // Validate post_id — user must be able to edit that post
        $post_id = absint( $_GET['post_id'] ?? 0 );
        if ( $post_id && ! current_user_can( 'edit_post', $post_id ) ) {
            $post_id = 0;
        }

        // v3.55.42 — quando l'utente apre il builder per un template (senza un post
        // specifico in URL), risolvi via PHP il permalink REALE del post collegato
        // nei settings del template. Senza questo, il pulsante "Reale" costruiva un
        // URL `?p=ID` lato JS che funziona solo per post_type='post' e dava 404
        // sulle pages (che usano `?page_id=ID`) o sui CPT.
        $linked_post_permalink = '';
        $linked_post_id = 0;
        $template_id_for_link = absint( $_GET['template_id'] ?? 0 );
        if ( $template_id_for_link && class_exists( 'Olo_Database' ) ) {
            $db_for_link = new Olo_Database();
            $tpl_for_link = $db_for_link->get_template( $template_id_for_link );
            if ( $tpl_for_link && ! empty( $tpl_for_link['settings']['post_id'] ) ) {
                $linked_post_id = absint( $tpl_for_link['settings']['post_id'] );
                if ( $linked_post_id ) {
                    $linked_post_permalink = (string) get_permalink( $linked_post_id );
                }
            }
        }

        wp_localize_script( 'olobuilder-js', 'oloData', [
            'restUrl'        => esc_url_raw( rest_url( 'olo/v1' ) ),
            'nonce'          => wp_create_nonce( 'wp_rest' ),
            'userId'         => get_current_user_id(),
            'userName'       => wp_get_current_user()->display_name,
            'version'        => OLO_VERSION,
            'pluginUrl'      => OLO_URL,
            'templateId'     => absint( $_GET['template_id'] ?? 0 ),
            'postId'         => $post_id,
            'postPermalink'  => $post_id ? get_permalink( $post_id ) : '',
            // Permalink REALE del post collegato al template (vedi commento sopra).
            // Usato dal pulsante "Anteprima reale" come priorità 2 se postPermalink è vuoto.
            'linkedPostId'        => $linked_post_id,
            'linkedPostPermalink' => $linked_post_permalink,
            'themeColors'    => $this->detect_theme_colors(),
            'styles'         => $style_system->get_styles(),
            'stylesCss'      => $style_system->generate_css(),
            'presets'        => $style_system->get_presets(),
            'globalColors'   => $style_system->get_global_colors(),
            'globalTypography' => $style_system->get_global_typography(),
            'wpMenus'        => $this->get_wp_menus(),
            'activeHeaderId' => (int) get_option( 'olo_active_header', 0 ),
            'activeFooterId' => (int) get_option( 'olo_active_footer', 0 ),
            'active404Id'    => (int) get_option( 'olo_active_404', 0 ),
            'activeSingles'  => $this->get_active_singles_map(),
            'stockmedia'     => wp_parse_args(
                get_option( 'olo_stockmedia_behavior', [] ) ?: [],
                [ 'preferred' => 'unsplash', 'download_local' => true, 'optimize_on_download' => false ]
            ),
            'templateList'       => $this->get_template_list(),
            'megapanelTemplates' => $this->get_megapanel_templates(),
            'widgetTemplates'    => $this->get_widget_templates(),
            'postTypes'      => $this->get_public_post_types(),
            'taxonomies'     => $this->get_public_taxonomies(),
            'metaPrefixes'   => $this->get_meta_prefixes(),
            'metaKeys'       => $this->get_meta_keys_map(),
            'serviceList'    => $this->get_service_list(),
            'wpPages'        => $this->get_wp_pages(),
            'singlePostItems' => $this->get_single_post_items(),
            'iframeEmptyHtml' => self::get_iframe_empty_html(),
            '_debug_tpl_id'   => absint( $_GET['template_id'] ?? 0 ),
            'hasAiKey'       => ! empty( get_option( 'olo_ai_anthropic_key', '' ) ),
            'breakpointsEnabled' => wp_parse_args( get_option( 'olo_breakpoints_enabled', [] ), [
                'widescreen'       => true,
                'tablet_landscape' => false,
                'tablet'           => true,
                'mobile_landscape' => false,
                'mobile'           => true,
            ] ),
            'userRestrictions' => Olobuild_Role_Manager::instance()->get_current_user_restrictions(),
            'isContentOnly'    => Olobuild_Role_Manager::instance()->is_content_only(),
            'isDesignOnly'     => Olobuild_Role_Manager::instance()->is_design_only(),
            'locale'         => olo_current_locale(),
            'translations'   => olo_get_translations_map(),
            'siteInfo'       => [
                'name'     => get_bloginfo( 'name' ),
                'tagline'  => get_bloginfo( 'description' ),
                'logo_url' => $this->get_site_logo_url(),
                'home_url' => home_url( '/' ),
            ],
        ] );

        // Filtro per plugin esterni che vogliono aggiungere dati a oloData
        $olo_data = apply_filters( 'olo_builder_localize_data', [] );
        if ( ! empty( $olo_data ) ) {
            wp_localize_script( 'olobuilder-js', 'oloExternalData', $olo_data );
        }
    }

    public function render_builder_page() {
        $template_id = absint( $_GET['template_id'] ?? 0 );

        // Forza il browser a NON usare la cache locale per la pagina builder.
        // Senza questi header alcuni browser (Chrome con bfcache) riservano
        // l'HTML completo della pagina builder — inclusi i tag <script src="builder.js?ver=X">
        // — e ricaricarla con F5 normale serve la versione vecchia.
        if ( ! headers_sent() ) {
            header( 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0' );
            header( 'Pragma: no-cache' );
            header( 'Expires: 0' );
        }

        if ( $template_id > 0 ) {
            // Fullscreen editor mode — keep existing behaviour
            include OLO_PATH . 'templates/builder-page.php';
        } else {
            // Template list mode — usa il cockpit shell della dashboard
            // (topbar OLObuild + appback strip + WP sidebar collassata in app mode)
            self::cockpit_shell_open( '<a href="' . esc_url( admin_url( 'admin.php?page=olobuilder-templates' ) ) . '" style="color:inherit;text-decoration:none">' . esc_html__( 'Template', 'olobuild' ) . '</a> · <b>' . esc_html__( 'Salvati', 'olobuild' ) . '</b>' );
            echo '<main class="olo-cockpit-main"><div id="olobuilder-app"></div></main>';
            self::cockpit_shell_close();
        }
    }

    /**
     * Add body class on Olobuild admin pages for shell layout.
     */
    /**
     * Lista degli screen IDs che usano lo shell cockpit (topbar OLObuild + appback +
     * sidebar WP collassata in app mode + body full-width).
     *
     * Single source of truth: usata da admin_body_class() e admin_enqueue_scripts()
     * per applicare CSS + body class in modo consistente.
     */
    public static function cockpit_screen_ids() {
        return [
            'toplevel_page_olobuild',
            'olobuild_page_olobuilder-templates',
            'olobuild_page_olobuilder-settings',     // Configurazione (Vue app)
            'olobuild_page_olo-media-search',
            'olobuild_page_olo-form-submissions',
            'olobuild_page_olo-analytics',
            'olobuild_page_olo-cookie-consent',
            'olobuild_page_olo-role-manager',
            'olobuild_page_olo-seo',
            'olobuild_page_olo-redirects',
            'olobuild_page_olo-performance',
            'olobuild_page_olo-tools',
            'olobuild_page_olo-global-popups',
            'olobuild_page_olo-white-label',
            'olobuild_page_olo-import-export',
            'olobuild_page_olo-woo-templates',
        ];
    }

    public function admin_body_class( $classes ) {
        $screen = get_current_screen();
        $is_olo_page = $screen && ( str_contains( $screen->id, 'olobuild' ) || str_contains( $screen->id, 'olo-' ) );

        if ( $is_olo_page ) {
            $classes .= ' olo-admin-shell';
        }

        // Pagine top-level che usano lo shell cockpit (full-width, no padding WP)
        $is_cockpit = $screen && in_array( $screen->id, self::cockpit_screen_ids(), true );

        if ( $is_cockpit ) {
            $classes .= ' olobuild-cockpit';
            // Mantieni la vecchia classe per la dashboard per compatibilità
            if ( $screen->id === 'toplevel_page_olobuild' ) {
                $classes .= ' olobuild-dashboard';
            }
        }

        // App mode: applicato a TUTTE le pagine Olobuild (sidebar 52px,
        // sotto-menu Olobuild nascosto perché ridondante con cockpit + page-shell).
        if ( $is_olo_page ) {
            $user_id = get_current_user_id();
            $prefs = get_user_meta( $user_id, 'olo_dashboard_prefs', true );
            $app_mode = is_array( $prefs ) && array_key_exists( 'app_mode', $prefs )
                ? (bool) $prefs['app_mode']
                : true; // default ON
            if ( $app_mode ) {
                $classes .= ' olobuild-app-mode';
            }
        }

        return $classes;
    }

    /**
     * Open the shared admin page shell: top bar + sidebar + content area.
     */
    /**
     * @deprecated 3.39.0 Usare cockpit_shell_open() — questo è mantenuto per
     * backward compat con eventuali plugin/tema che lo chiamano.
     */
    public static function page_shell_open( $page_title = '', $extra_class = '' ) {
        $logo_url  = OLO_URL . 'assets/img/olobuild-logo-200-v2.png';
        $white_url = OLO_URL . 'assets/img/olobuild-logo-200-white.png';
        $cls = 'olo-admin-wrap' . ( $extra_class ? ' ' . esc_attr( $extra_class ) : '' );
        $current   = sanitize_key( $_GET['page'] ?? '' );
        ?>
        <div class="olo-shell">
            <!-- Top bar -->
            <div class="olo-shell-topbar">
                <div class="olo-shell-topbar-brand">
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=olobuild' ) ); ?>">
                        <img src="<?php echo esc_url( $white_url ); ?>" alt="Olobuild" />
                    </a>
                    <span class="olo-shell-topbar-label">website builder</span>
                </div>
                <div class="olo-shell-topbar-actions">
                    <span class="olo-shell-topbar-version">v<?php echo OLO_VERSION; ?></span>
                </div>
            </div>

            <div class="olo-shell-body">
                <!-- Sidebar -->
                <?php self::render_sidebar( $current ); ?>

                <!-- Content -->
                <div class="olo-shell-content">
                    <?php if ( $page_title ) : ?>
                        <h1 class="olo-shell-page-title"><?php echo esc_html( $page_title ); ?></h1>
                    <?php endif; ?>
                    <div class="<?php echo esc_attr( $cls ); ?>">
        <?php
    }

    /**
     * Close the shared admin page shell.
     */
    public static function page_shell_close() {
        ?>
                    </div><!-- .olo-admin-wrap -->
                </div><!-- .olo-shell-content -->
            </div><!-- .olo-shell-body -->
        </div><!-- .olo-shell -->
        <?php
    }

    /**
     * Render the Olobuild sidebar navigation.
     */
    private static function render_sidebar( $current_page ) {
        $base = admin_url( 'admin.php?page=' );

        $menu = [
            [ 'slug' => 'olobuild',           'label' => __( 'Avvio Rapido', 'olobuild' ),  'icon' => 'rocket' ],
            [ 'slug' => 'olobuilder-settings',   'label' => __( 'Impostazioni', 'olobuild' ),  'icon' => 'gear' ],
            [ 'slug' => 'olo-tools',             'label' => __( 'Strumenti', 'olobuild' ),     'icon' => 'wrench' ],
            [ 'slug' => 'olobuilder-settings', 'label' => __( 'Permessi & Ruoli', 'olobuild' ), 'icon' => 'users', 'tab' => 'permessi' ],
            [ 'slug' => 'olo-form-submissions',  'label' => __( 'Submissions', 'olobuild' ),   'icon' => 'email' ],
            [
                'group' => __( 'Template', 'olobuild' ),
                'icon'  => 'layout',
                'items' => [
                    [ 'slug' => 'olobuilder-templates', 'label' => __( 'Template Salvati', 'olobuild' ) ],
                    [ 'slug' => 'olo-import-export',    'label' => __( 'Template Website', 'olobuild' ) ],
                    [ 'slug' => 'olobuilder-settings',  'label' => __( 'Popup globali', 'olobuild' ), 'tab' => 'popups' ],
                ],
            ],
            [
                'group' => __( 'Marketing', 'olobuild' ),
                'icon'  => 'chart',
                'items' => [
                    [ 'slug' => 'olobuilder-settings',   'label' => __( 'Analytics & Tracking', 'olobuild' ), 'tab' => 'analytics' ],
                    [ 'slug' => 'olobuilder-settings',   'label' => __( 'Cookie Consent', 'olobuild' ),       'tab' => 'cookie' ],
                    [ 'slug' => 'olobuilder-settings',   'label' => __( 'SEO', 'olobuild' ),                  'tab' => 'seo' ],
                    [ 'slug' => 'olobuilder-settings',   'label' => __( 'Redirect & 404', 'olobuild' ),       'tab' => 'redirects' ],
                ],
            ],
            [
                'group' => __( 'Personalizzazione', 'olobuild' ),
                'icon'  => 'palette',
                'items' => [
                    [ 'slug' => 'olo-media-search',      'label' => __( 'Ricerca Media', 'olobuild' ) ],
                    [ 'slug' => 'olobuilder-settings',   'label' => __( 'Performance', 'olobuild' ), 'tab' => 'performance' ],
                    [ 'slug' => 'olobuilder-settings',   'label' => __( 'WooCommerce', 'olobuild' ), 'tab' => 'wootemplates' ],
                ],
            ],
            [
                'group' => __( 'Sistema', 'olobuild' ),
                'icon'  => 'settings',
                'items' => [
                    [ 'slug' => 'olobuilder-settings', 'label' => __( 'White Label', 'olobuild' ), 'tab' => 'whitelabel' ],
                ],
            ],
        ];

        $icons = [
            'rocket'   => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 00-2.91-.09z"/><path d="M12 15l-3-3a22 22 0 012-3.95A12.88 12.88 0 0122 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 01-4 2z"/><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/></svg>',
            'gear'     => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 01-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>',
            'wrench'   => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>',
            'users'    => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>',
            'email'    => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
            'layout'   => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>',
            'chart'    => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>',
            'palette'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.93 0 1.5-.63 1.5-1.36 0-.35-.14-.69-.38-.96-.22-.25-.34-.54-.34-.9 0-.74.6-1.28 1.34-1.28H16c3.31 0 6-2.69 6-6 0-5.52-4.48-10-10-10z"/><circle cx="7.5" cy="11.5" r="1.5" fill="currentColor"/><circle cx="10.5" cy="7.5" r="1.5" fill="currentColor"/><circle cx="14.5" cy="7.5" r="1.5" fill="currentColor"/><circle cx="17.5" cy="11.5" r="1.5" fill="currentColor"/></svg>',
            'settings' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
        ];

        // Determine which group should be open
        $group_slugs = [];
        foreach ( $menu as $item ) {
            if ( isset( $item['group'] ) ) {
                foreach ( $item['items'] as $sub ) {
                    $group_slugs[ $sub['slug'] ] = $item['group'];
                }
            }
        }
        $active_group = $group_slugs[ $current_page ] ?? '';

        ?>
        <nav class="olo-shell-sidebar">
            <div class="olo-shell-sidebar-head">
                <span class="olo-shell-sidebar-title">Editor</span>
            </div>
            <ul class="olo-shell-nav">
                <?php foreach ( $menu as $item ) : ?>
                    <?php if ( isset( $item['group'] ) ) :
                        $is_open = $active_group === $item['group'];
                        $group_id = 'olo-nav-' . sanitize_key( $item['group'] );
                    ?>
                        <li class="olo-shell-nav-group <?php echo $is_open ? 'open' : ''; ?>">
                            <button class="olo-shell-nav-group-btn" onclick="this.parentElement.classList.toggle('open')" type="button">
                                <span class="olo-shell-nav-icon"><?php echo $icons[ $item['icon'] ] ?? ''; ?></span>
                                <span><?php echo esc_html( $item['group'] ); ?></span>
                                <svg class="olo-shell-nav-arrow" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                            </button>
                            <ul class="olo-shell-nav-sub">
                                <?php foreach ( $item['items'] as $sub ) :
                                    $sub_href = $base . $sub['slug'];
                                    if ( ! empty( $sub['tab'] ) ) { $sub_href .= '&tab=' . rawurlencode( $sub['tab'] ); }
                                ?>
                                    <li>
                                        <a href="<?php echo esc_url( $sub_href ); ?>"
                                           class="<?php echo $current_page === $sub['slug'] ? 'active' : ''; ?>">
                                            <?php echo esc_html( $sub['label'] ); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php else :
                        $item_href = $base . $item['slug'];
                        if ( ! empty( $item['tab'] ) ) { $item_href .= '&tab=' . rawurlencode( $item['tab'] ); }
                    ?>
                        <li>
                            <a href="<?php echo esc_url( $item_href ); ?>"
                               class="olo-shell-nav-item <?php echo $current_page === $item['slug'] ? 'active' : ''; ?>">
                                <span class="olo-shell-nav-icon"><?php echo $icons[ $item['icon'] ] ?? ''; ?></span>
                                <span><?php echo esc_html( $item['label'] ); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </nav>
        <?php
    }

    /**
     * Tile "Gestione" della dashboard cockpit.
     * Lo stesso array è riutilizzato per costruire l'indice di ricerca.
     */
    public static function dashboard_manage_tiles() {
        return [
            [
                'id'    => 'tpl',
                'label' => __( 'Gestione Template', 'olobuild' ),
                'hint'  => __( 'Crea e modifica i tuoi template', 'olobuild' ),
                'icon'  => 'template',
                'color' => '#f97316',
                'href'  => admin_url( 'admin.php?page=olobuilder-templates' ),
            ],
            [
                'id'    => 'cfg',
                'label' => __( 'Configurazione', 'olobuild' ),
                'hint'  => __( 'Stili, colori, tipografia e API', 'olobuild' ),
                'icon'  => 'sliders',
                'color' => '#1f2937',
                'href'  => admin_url( 'admin.php?page=olobuilder-settings' ),
            ],
            [
                'id'    => 'media',
                'label' => __( 'Ricerca Media', 'olobuild' ),
                'hint'  => __( 'Foto, video e audio stock', 'olobuild' ),
                'icon'  => 'image',
                'color' => '#a855f7',
                'href'  => admin_url( 'admin.php?page=olo-media-search' ),
            ],
            [
                'id'    => 'form',
                'label' => __( 'Invii Form', 'olobuild' ),
                'hint'  => __( 'Visualizza i messaggi ricevuti', 'olobuild' ),
                'icon'  => 'form',
                'color' => '#10b981',
                'href'  => admin_url( 'admin.php?page=olo-form-submissions' ),
            ],
            [
                'id'    => 'an',
                'label' => __( 'Analytics', 'olobuild' ),
                'hint'  => __( 'Tracking e statistiche', 'olobuild' ),
                'icon'  => 'chart',
                'color' => '#3b82f6',
                'href'  => admin_url( 'admin.php?page=olobuilder-settings&tab=analytics' ),
            ],
            [
                'id'    => 'cc',
                'label' => __( 'Cookie Consent', 'olobuild' ),
                'hint'  => __( 'Banner GDPR e consenso', 'olobuild' ),
                'icon'  => 'cookie',
                'color' => '#f59e0b',
                'href'  => admin_url( 'admin.php?page=olobuilder-settings&tab=cookie' ),
            ],
            [
                'id'    => 'seo',
                'label' => __( 'SEO', 'olobuild' ),
                'hint'  => __( 'Meta tag, Open Graph e sitemap', 'olobuild' ),
                'icon'  => 'search',
                'color' => '#06b6d4',
                'href'  => admin_url( 'admin.php?page=olobuilder-settings&tab=seo' ),
            ],
            [
                'id'    => '404',
                'label' => __( 'Redirect & 404', 'olobuild' ),
                'hint'  => __( 'Gestisci redirect e pagine 404', 'olobuild' ),
                'icon'  => 'redirect',
                'color' => '#ef4444',
                'href'  => admin_url( 'admin.php?page=olobuilder-settings&tab=redirects' ),
            ],
            [
                'id'    => 'perf',
                'label' => __( 'Performance', 'olobuild' ),
                'hint'  => __( 'Cache, lazy load, ottimizzazione', 'olobuild' ),
                'icon'  => 'zap',
                'color' => '#eab308',
                'href'  => admin_url( 'admin.php?page=olobuilder-settings&tab=performance' ),
            ],
            [
                'id'    => 'tools',
                'label' => __( 'Strumenti', 'olobuild' ),
                'hint'  => __( 'Cache, manutenzione, URL, versioni', 'olobuild' ),
                'icon'  => 'wrench',
                'color' => '#64748b',
                'href'  => admin_url( 'admin.php?page=olo-tools' ),
            ],
            [
                'id'    => 'woo',
                'label' => __( 'WooCommerce', 'olobuild' ),
                'hint'  => __( 'Template per prodotti e shop', 'olobuild' ),
                'icon'  => 'cart',
                'color' => '#7e22ce',
                'href'  => admin_url( 'admin.php?page=olobuilder-settings&tab=wootemplates' ),
            ],
            [
                'id'    => 'pop',
                'label' => __( 'Popup Globali', 'olobuild' ),
                'hint'  => __( 'Banner e modali riusabili', 'olobuild' ),
                'icon'  => 'modal',
                'color' => '#0ea5e9',
                'href'  => admin_url( 'admin.php?page=olobuilder-settings&tab=popups' ),
            ],
        ];
    }

    /**
     * Voci "Sistema" — chip rounded raramente usate.
     */
    public static function dashboard_system_chips() {
        return [
            [ 'id' => 'wl',   'label' => __( 'White Label', 'olobuild' ),       'icon' => 'tag',     'href' => admin_url( 'admin.php?page=olobuilder-settings&tab=whitelabel' ) ],
            [ 'id' => 'imp',  'label' => __( 'Import/Export', 'olobuild' ),     'icon' => 'upload',  'href' => admin_url( 'admin.php?page=olo-import-export' ) ],
            [ 'id' => 'perm', 'label' => __( 'Permessi & Ruoli', 'olobuild' ),  'icon' => 'users',   'href' => admin_url( 'admin.php?page=olobuilder-settings&tab=permessi' ) ],
            [ 'id' => 'subs', 'label' => __( 'Submissions', 'olobuild' ),       'icon' => 'inbox',   'href' => admin_url( 'admin.php?page=olo-form-submissions' ) ],
            [ 'id' => 'log',  'label' => __( 'Diagnostica', 'olobuild' ),       'icon' => 'history', 'href' => admin_url( 'tools.php?page=olo-diagnostics' ) ],
            [ 'id' => 'lic',  'label' => __( 'Licenza', 'olobuild' ),           'icon' => 'key',     'href' => admin_url( 'admin.php?page=olobuilder-settings' ) ],
        ];
    }

    /**
     * Indice di ricerca per la palette ⌘K.
     * Voci di menu + pagine + template (top N più recenti).
     */
    public static function dashboard_search_index() {
        $idx = [];

        foreach ( self::dashboard_manage_tiles() as $t ) {
            $idx[] = [ 'label' => $t['label'], 'hint' => $t['hint'], 'href' => $t['href'], 'icon' => $t['icon'] ];
        }
        foreach ( self::dashboard_system_chips() as $t ) {
            $idx[] = [ 'label' => $t['label'], 'hint' => '', 'href' => $t['href'], 'icon' => $t['icon'] ];
        }

        // Top pagine recenti
        $pages = get_posts( [
            'post_type'      => [ 'page', 'post' ],
            'post_status'    => [ 'publish', 'draft' ],
            'posts_per_page' => 30,
            'orderby'        => 'modified',
            'order'          => 'DESC',
        ] );
        foreach ( $pages as $p ) {
            $idx[] = [
                'label' => $p->post_title ?: __( '(senza titolo)', 'olobuild' ),
                'hint'  => $p->post_type === 'post' ? __( 'Articolo', 'olobuild' ) : __( 'Pagina', 'olobuild' ),
                'href'  => admin_url( 'post.php?post=' . $p->ID . '&action=edit' ),
                'icon'  => 'fileText',
            ];
        }

        // Top template
        global $wpdb;
        $tpl_table = $wpdb->prefix . 'olo_templates';
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$tpl_table'" ) === $tpl_table ) {
            $tpls = $wpdb->get_results(
                "SELECT id, title, type FROM $tpl_table ORDER BY updated_at DESC LIMIT 30",
                ARRAY_A
            );
            foreach ( $tpls as $t ) {
                $idx[] = [
                    'label' => $t['title'] ?: __( '(senza titolo)', 'olobuild' ),
                    'hint'  => __( 'Template', 'olobuild' ) . ' · ' . ucfirst( $t['type'] ?: 'template' ),
                    'href'  => admin_url( 'admin.php?page=olobuilder-templates&template_id=' . $t['id'] ),
                    'icon'  => 'template',
                ];
            }
        }

        return $idx;
    }

    /**
     * Dato per l'hero contestuale: prima la pagina più recentemente modificata,
     * altrimenti il template più recente. Include URL builder Olobuild + thumbnail.
     */
    private static function dashboard_hero_data() {
        global $wpdb;
        $tpl_table = $wpdb->prefix . 'olo_templates';
        $tpl_table_exists = ( $wpdb->get_var( "SHOW TABLES LIKE '$tpl_table'" ) === $tpl_table );

        $pages = get_posts( [
            'post_type'      => [ 'page', 'post' ],
            'post_status'    => [ 'publish', 'draft' ],
            'posts_per_page' => 1,
            'orderby'        => 'modified',
            'order'          => 'DESC',
        ] );
        if ( ! empty( $pages ) ) {
            $p = $pages[0];
            $thumbnail = '';
            $tpl_id = (int) get_post_meta( $p->ID, '_olo_template_id', true );
            if ( $tpl_id && $tpl_table_exists ) {
                $thumbnail = (string) $wpdb->get_var( $wpdb->prepare(
                    "SELECT thumbnail FROM $tpl_table WHERE id = %d", $tpl_id
                ) );
            }
            // Fallback: featured image
            if ( ! $thumbnail ) {
                $thumb_id = get_post_thumbnail_id( $p->ID );
                if ( $thumb_id ) $thumbnail = wp_get_attachment_image_url( $thumb_id, 'large' );
            }

            $edit_url = class_exists( 'Olo_Page_Integration' )
                ? Olo_Page_Integration::get_builder_url( $p->ID )
                : admin_url( 'admin.php?page=olobuilder-templates' );

            return [
                'title'     => $p->post_title ?: __( '(senza titolo)', 'olobuild' ),
                'sub'       => sprintf(
                    /* translators: 1: human time diff, 2: status word */
                    __( 'Hai modificato questa pagina %1$s. La pagina è %2$s.', 'olobuild' ),
                    '<b>' . human_time_diff( strtotime( $p->post_modified_gmt ), time() ) . ' ' . __( 'fa', 'olobuild' ) . '</b>',
                    $p->post_status === 'publish' ? __( 'pubblicata', 'olobuild' ) : __( 'in bozza', 'olobuild' )
                ),
                'edit'      => $edit_url,
                'view'      => get_permalink( $p->ID ),
                'is_page'   => true,
                'thumbnail' => $thumbnail,
                'status'    => $p->post_status,
            ];
        }
        return [
            'title'     => __( 'Inizia a costruire il tuo sito', 'olobuild' ),
            'sub'       => __( 'Crea la prima pagina o sfoglia i template per partire al volo.', 'olobuild' ),
            'edit'      => admin_url( 'admin.php?page=olobuilder-templates' ),
            'view'      => home_url( '/' ),
            'is_page'   => false,
            'thumbnail' => '',
            'status'    => '',
        ];
    }

    /**
     * Apre lo "shell cockpit" condiviso (appback strip + topbar) — usato sia
     * dalla dashboard che dalle altre pagine top-level Olobuild.
     *
     * Dopo questa chiamata, l'output è dentro `.olo-cockpit-wrap`. Chiudere
     * con `cockpit_shell_close()`. Il blocco intermedio è libero (la pagina
     * gestisce il suo layout: dashboard usa `.olo-cockpit-grid` con rail,
     * la lista template usa direttamente `.olo-cockpit-main`).
     *
     * @param string $crumb_html  HTML del breadcrumb dopo "Olobuild · ".
     */
    public static function cockpit_shell_open( $crumb_html = '' ) {
        $user = wp_get_current_user();
        $initials = strtoupper( substr( $user->first_name ?: $user->display_name ?: 'U', 0, 2 ) );
        $site_host = wp_parse_url( home_url(), PHP_URL_HOST );

        $user_prefs = get_user_meta( $user->ID, 'olo_dashboard_prefs', true );
        if ( ! is_array( $user_prefs ) ) $user_prefs = [];
        $app_mode = array_key_exists( 'app_mode', $user_prefs ) ? (bool) $user_prefs['app_mode'] : true;
        ?>
        <div class="olo-cockpit-wrap">
            <?php if ( $app_mode ) : ?>
            <div class="olo-cockpit-appback">
                <a href="<?php echo esc_url( admin_url( 'index.php' ) ); ?>">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    <?php esc_html_e( 'Torna a WordPress', 'olobuild' ); ?>
                </a>
                <span style="opacity:.4">|</span>
                <span><?php echo esc_html( $site_host ); ?></span>
                <span class="spc"></span>
                <span class="pill-app"><?php esc_html_e( 'App mode', 'olobuild' ); ?></span>
            </div>
            <?php endif; ?>

            <div class="olo-cockpit-topbar">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=olobuild' ) ); ?>" style="display:inline-flex;align-items:center;text-decoration:none;">
                    <img class="logo" src="<?php echo esc_url( OLO_URL . 'assets/img/olobuild-horizontal.png' ); ?>" alt="Olobuild" />
                </a>
                <button type="button" class="ver" data-olo-app-mode-toggle title="<?php esc_attr_e( 'Cambia modalità', 'olobuild' ); ?>">v<?php echo esc_html( OLO_VERSION ); ?></button>
                <span class="sep"></span>
                <span class="crumb"><a href="<?php echo esc_url( admin_url( 'admin.php?page=olobuild' ) ); ?>" style="color:inherit;text-decoration:none">Olobuild</a> · <?php echo $crumb_html ?: '<b>' . esc_html__( 'Dashboard', 'olobuild' ) . '</b>'; ?></span>
                <span class="spc"></span>
                <div class="search-mini" data-olo-palette-trigger>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" style="color:var(--olo-text-muted)"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="text" placeholder="<?php esc_attr_e( 'Cerca pagine, template, impostazioni…', 'olobuild' ); ?>" readonly/>
                    <kbd>Ctrl K</kbd>
                </div>
                <a class="ico-btn" href="<?php echo esc_url( admin_url( 'admin.php?page=olo-form-submissions' ) ); ?>" title="<?php esc_attr_e( 'Notifiche', 'olobuild' ); ?>">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 16V11a6 6 0 0112 0v5l2 2H4l2-2zM10 20a2 2 0 004 0"/></svg>
                </a>
                <a class="ico-btn" href="https://olotheme.com/docs" target="_blank" rel="noopener" title="<?php esc_attr_e( 'Documentazione', 'olobuild' ); ?>">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 015 0c0 1.5-2.5 2-2.5 4M12 17h.01"/></svg>
                </a>
                <a class="ico-btn" href="<?php echo esc_url( get_edit_user_link() ); ?>" title="<?php esc_attr_e( 'Profilo', 'olobuild' ); ?>">
                    <span class="av"><?php echo esc_html( $initials ); ?></span>
                </a>
            </div>
        <?php
    }

    /**
     * Chiude lo shell cockpit (chiusura `.olo-cockpit-wrap`).
     */
    public static function cockpit_shell_close() {
        ?>
        </div><!-- .olo-cockpit-wrap -->
        <?php
    }

    /* ════════════════════════════════════════════════════════════════
       TOOLKIT — componenti shared per pagine cockpit (Fase 2)
       Helper PHP che emettono il markup standard dei pattern comuni.
       Vanno usati DENTRO `<main class="olo-cockpit-main">`.
       Vedono CSS classes in dashboard.css sezione TOOLKIT.
       ════════════════════════════════════════════════════════════════ */

    /**
     * Page head: titolo H1 + sottotitolo meta + bottoni a destra.
     *
     * @param array $args {
     *   @type string $title    Titolo H1 (richiesto).
     *   @type string $sub      HTML del sottotitolo (b/a/spans permessi).
     *   @type string $actions  HTML dei bottoni a destra (usa cockpit_button()).
     * }
     *
     * @example
     *   echo Olo_Builder::cockpit_page_head([
     *     'title'   => __('Strumenti', 'olobuild'),
     *     'sub'     => sprintf(__('Cache: %s · DB: %s'), '<b>2.4 MB</b>', '<b>OK</b>'),
     *     'actions' => Olo_Builder::cockpit_button(['label' => 'Nuovo', 'variant' => 'pri']),
     *   ]);
     */
    public static function cockpit_page_head( $args = [] ) {
        $args = wp_parse_args( $args, [ 'title' => '', 'sub' => '', 'actions' => '' ] );
        ob_start();
        ?>
        <header class="olo-page-head">
            <div class="titles">
                <h1><?php echo esc_html( $args['title'] ); ?></h1>
                <?php if ( $args['sub'] ) : ?>
                    <div class="sub"><?php echo wp_kses_post( $args['sub'] ); ?></div>
                <?php endif; ?>
            </div>
            <?php if ( $args['actions'] ) : ?>
                <div class="actions"><?php echo $args['actions']; // markup pre-validato ?></div>
            <?php endif; ?>
        </header>
        <?php
        return ob_get_clean();
    }

    /**
     * Sub-nav tabs (orizzontali) sotto il page head.
     *
     * @param array  $items   Array di tabs: [{slug, label, count?, href}].
     * @param string $active  Slug del tab attivo.
     *
     * @example
     *   echo Olo_Builder::cockpit_subnav([
     *     ['slug' => 'general', 'label' => 'Generale', 'href' => admin_url('admin.php?page=olo-seo')],
     *     ['slug' => 'meta',    'label' => 'Meta tag', 'href' => admin_url('admin.php?page=olo-seo&tab=meta'), 'count' => 12],
     *   ], 'general');
     */
    public static function cockpit_subnav( $items, $active = '' ) {
        if ( empty( $items ) || ! is_array( $items ) ) return '';
        ob_start();
        ?>
        <nav class="olo-subnav">
            <?php foreach ( $items as $it ) :
                $slug = $it['slug'] ?? '';
                $href = $it['href'] ?? '#';
                $label = $it['label'] ?? '';
                $count = $it['count'] ?? null;
                $is_active = ( $slug === $active );
            ?>
                <a href="<?php echo esc_url( $href ); ?>" class="<?php echo $is_active ? 'active' : ''; ?>">
                    <?php echo esc_html( $label ); ?>
                    <?php if ( $count !== null ) : ?>
                        <span class="num"><?php echo (int) $count; ?></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <?php
        return ob_get_clean();
    }

    /**
     * Toolbar sticky: chip filtri + search + sort + view toggle + content libero.
     *
     * @param array $args {
     *   @type array  $chips        Lista chip: [{id, label, count?, dot_color?, href?}]
     *   @type string $active_chip  ID del chip attivo.
     *   @type bool   $search       Mostra input search (default false).
     *   @type string $search_id    ID dell'input search (per binding JS).
     *   @type string $search_placeholder
     *   @type string $extra        HTML aggiuntivo dopo gli spacer (es. sort, view toggle).
     * }
     *
     * @example
     *   echo Olo_Builder::cockpit_toolbar([
     *     'chips' => [
     *       ['id'=>'all', 'label'=>'Tutti', 'count'=>128],
     *       ['id'=>'unread', 'label'=>'Non letti', 'count'=>12, 'dot_color'=>'#ef4444'],
     *     ],
     *     'active_chip' => 'all',
     *     'search'      => true,
     *     'search_placeholder' => 'Cerca…',
     *   ]);
     */
    public static function cockpit_toolbar( $args = [] ) {
        $args = wp_parse_args( $args, [
            'chips'              => [],
            'active_chip'        => '',
            'search'             => false,
            'search_id'          => '',
            'search_placeholder' => __( 'Cerca…', 'olobuild' ),
            'extra'              => '',
        ] );
        ob_start();
        ?>
        <div class="olo-toolbar">
            <?php if ( ! empty( $args['chips'] ) ) : ?>
                <div class="chips">
                    <?php foreach ( $args['chips'] as $c ) :
                        $id    = $c['id']    ?? '';
                        $label = $c['label'] ?? '';
                        $count = $c['count'] ?? null;
                        $dot   = $c['dot_color'] ?? '';
                        $href  = $c['href']  ?? '';
                        $on    = ( $id === $args['active_chip'] );
                        $tag   = $href ? 'a' : 'button';
                        $extra_attr = $href ? ' href="' . esc_url( $href ) . '"' : ' type="button"';
                    ?>
                        <<?php echo $tag; ?> class="olo-chip <?php echo $on ? 'on' : ''; ?>"
                            data-chip-id="<?php echo esc_attr( $id ); ?>"<?php echo $extra_attr; ?>>
                            <?php if ( $dot ) : ?>
                                <span class="dot" style="background: <?php echo esc_attr( $dot ); ?>"></span>
                            <?php endif; ?>
                            <?php echo esc_html( $label ); ?>
                            <?php if ( $count !== null ) : ?>
                                <span class="num"><?php echo (int) $count; ?></span>
                            <?php endif; ?>
                        </<?php echo $tag; ?>>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <span class="spc"></span>
            <?php if ( $args['search'] ) : ?>
                <div class="olo-search">
                    <span class="ic"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg></span>
                    <input type="search" id="<?php echo esc_attr( $args['search_id'] ); ?>"
                        placeholder="<?php echo esc_attr( $args['search_placeholder'] ); ?>" autocomplete="off" />
                </div>
            <?php endif; ?>
            <?php if ( $args['extra'] ) : ?>
                <?php echo $args['extra']; // markup pre-validato ?>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Bottone cockpit standard (atomico).
     *
     * @param array $args {
     *   @type string $label    Testo del bottone (richiesto).
     *   @type string $variant  'pri' | 'sec' | 'danger' | 'ghost'. Default 'sec'.
     *   @type string $size     '' | 'sm'. Default ''.
     *   @type string $icon     SVG path inline (24×24 viewBox, stroke-width 1.7).
     *   @type string $href     Se presente, emette <a> invece di <button>.
     *   @type string $type     button|submit. Default 'button'.
     *   @type string $title    Tooltip.
     *   @type bool   $disabled Default false.
     *   @type array  $attrs    Attributi extra (data-*, onclick, ecc.) come [k=>v].
     * }
     *
     * @example
     *   echo Olo_Builder::cockpit_button([
     *     'label' => 'Salva', 'variant' => 'pri', 'type' => 'submit',
     *     'icon' => '<path d="M19 21H5..." />',
     *   ]);
     */
    public static function cockpit_button( $args = [] ) {
        $args = wp_parse_args( $args, [
            'label'    => '',
            'variant'  => 'sec',
            'size'     => '',
            'icon'     => '',
            'href'     => '',
            'type'     => 'button',
            'title'    => '',
            'disabled' => false,
            'attrs'    => [],
        ] );
        $cls = 'olo-btn olo-btn-' . sanitize_html_class( $args['variant'] );
        if ( $args['size'] ) $cls .= ' olo-btn-' . sanitize_html_class( $args['size'] );
        if ( ! $args['label'] && $args['icon'] ) $cls .= ' olo-btn-icon';

        $attrs_str = '';
        foreach ( $args['attrs'] as $k => $v ) {
            $attrs_str .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
        }
        if ( $args['title'] ) $attrs_str .= ' title="' . esc_attr( $args['title'] ) . '"';

        $svg = $args['icon']
            ? '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">' . $args['icon'] . '</svg>'
            : '';

        if ( $args['href'] ) {
            return sprintf(
                '<a class="%s" href="%s"%s>%s%s</a>',
                esc_attr( $cls ), esc_url( $args['href'] ), $attrs_str,
                $svg, esc_html( $args['label'] )
            );
        }
        return sprintf(
            '<button class="%s" type="%s"%s%s>%s%s</button>',
            esc_attr( $cls ),
            esc_attr( $args['type'] ),
            $args['disabled'] ? ' disabled' : '',
            $attrs_str,
            $svg, esc_html( $args['label'] )
        );
    }

    /**
     * Card grid o list per liste di entità (Submissions, Popups, Media, ecc).
     *
     * @param array $args {
     *   @type array  $items    Array di items, ognuno con shape libera; passato al renderer.
     *   @type string $layout   'grid' | 'list'. Default 'grid'.
     *   @type callable $render Callback `function($item) => string` che ritorna l'HTML
     *                          della card. Riceve l'intero $item.
     *   @type string $empty_title    Titolo dell'empty state.
     *   @type string $empty_message  Messaggio dell'empty state.
     *   @type string $empty_actions  HTML dei bottoni dell'empty state.
     * }
     *
     * @example
     *   echo Olo_Builder::cockpit_card_grid([
     *     'items'  => $submissions,
     *     'layout' => 'grid',
     *     'render' => function($s) {
     *         return '<a class="olo-card" href="#' . $s['id'] . '">' .
     *             '<div class="head"><div class="lab"><div class="t">' . esc_html($s['from']) . '</div></div></div>' .
     *             '<div class="body">' . esc_html($s['preview']) . '</div>' .
     *         '</a>';
     *     },
     *     'empty_title'   => 'Nessun invio ancora',
     *     'empty_message' => 'I messaggi dai form compariranno qui.',
     *   ]);
     */
    public static function cockpit_card_grid( $args = [] ) {
        $args = wp_parse_args( $args, [
            'items'         => [],
            'layout'        => 'grid',
            'render'        => null,
            'empty_title'   => __( 'Nessun elemento', 'olobuild' ),
            'empty_message' => '',
            'empty_actions' => '',
        ] );

        if ( empty( $args['items'] ) ) {
            ob_start();
            ?>
            <div class="olo-empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" style="opacity:.3"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
                <h3><?php echo esc_html( $args['empty_title'] ); ?></h3>
                <?php if ( $args['empty_message'] ) : ?>
                    <p><?php echo esc_html( $args['empty_message'] ); ?></p>
                <?php endif; ?>
                <?php if ( $args['empty_actions'] ) : ?>
                    <div><?php echo $args['empty_actions']; // markup pre-validato ?></div>
                <?php endif; ?>
            </div>
            <?php
            return ob_get_clean();
        }

        $cls = $args['layout'] === 'list' ? 'olo-data-list' : 'olo-data-grid';
        ob_start();
        ?>
        <div class="<?php echo esc_attr( $cls ); ?>">
            <?php
            if ( is_callable( $args['render'] ) ) {
                foreach ( $args['items'] as $item ) {
                    echo call_user_func( $args['render'], $item ); // markup dal renderer
                }
            }
            ?>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_dashboard_page() {
        $manage = self::dashboard_manage_tiles();
        $system = self::dashboard_system_chips();
        $hero   = self::dashboard_hero_data();

        $user = wp_get_current_user();
        $first_name = $user->first_name ?: $user->display_name ?: __( 'Utente', 'olobuild' );
        $site_host = wp_parse_url( home_url(), PHP_URL_HOST );

        // Stato "aggiornamento disponibile" (placeholder — TODO collegare a UpdateChecker)
        $update_available = false;
        $update_info = [];

        $user_prefs = get_user_meta( $user->ID, 'olo_dashboard_prefs', true );
        if ( ! is_array( $user_prefs ) ) $user_prefs = [];
        $rail_collapsed = isset( $user_prefs['rail'] ) && $user_prefs['rail'] === 'collapsed';

        self::cockpit_shell_open( '<b>' . esc_html__( 'Dashboard', 'olobuild' ) . '</b>' );
        ?>
            <div class="olo-cockpit-grid<?php echo $rail_collapsed ? ' collapsed' : ''; ?>">
                <main class="olo-cockpit-main">

                    <?php if ( $update_available ) : ?>
                    <div class="olo-banner" data-banner="update-<?php echo esc_attr( $update_info['version'] ?? '0' ); ?>">
                        <span class="ic"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4l9 16H3z"/><path d="M12 11v4M12 18h.01"/></svg></span>
                        <span><b><?php printf( esc_html__( 'Aggiornamento disponibile · v%s', 'olobuild' ), esc_html( $update_info['version'] ?? '' ) ); ?></b> — <?php echo esc_html( $update_info['note'] ?? '' ); ?></span>
                        <span class="spc"></span>
                        <a class="act" href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>"><?php esc_html_e( 'Aggiorna ora', 'olobuild' ); ?></a>
                        <button type="button" class="x" title="<?php esc_attr_e( 'Chiudi', 'olobuild' ); ?>"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l12 12M18 6L6 18"/></svg></button>
                    </div>
                    <?php endif; ?>

                    <!-- Hero contestuale -->
                    <section class="olo-hero">
                        <div class="olo-hero-l">
                            <div class="greet"><?php
                                /* translators: %s = first name */
                                printf( esc_html__( 'Ciao %s, buon lavoro', 'olobuild' ), esc_html( $first_name ) );
                            ?></div>
                            <h1><?php
                                if ( $hero['is_page'] ) {
                                    /* translators: %s = page title */
                                    printf( esc_html__( 'Continua su %s', 'olobuild' ), '<b>' . esc_html( $hero['title'] ) . '</b>' );
                                } else {
                                    echo '<b>' . esc_html( $hero['title'] ) . '</b>';
                                }
                            ?></h1>
                            <p class="sub"><?php echo wp_kses( $hero['sub'], [ 'b' => [], 'strong' => [] ] ); ?></p>
                            <div class="meta-row">
                                <span class="item"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 010 18M12 3a14 14 0 000 18"/></svg> <?php echo esc_html( $site_host ); ?></span>
                                <span class="item"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h4"/></svg> <b><?php echo esc_html( wp_count_posts( 'page' )->publish ); ?></b> <?php esc_html_e( 'pagine', 'olobuild' ); ?></span>
                                <span class="item"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 19l3-3a4 4 0 015.7 0l.3.3 4-9-9 4 .3.3a4 4 0 010 5.7L5 19zM4 14a3 3 0 00-1 6 3 3 0 006-1"/></svg> v<?php echo esc_html( OLO_VERSION ); ?></span>
                            </div>
                            <div class="ctas">
                                <a class="pri" href="<?php echo esc_url( $hero['edit'] ); ?>">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20l4-1 11-11-3-3L5 16l-1 4z"/></svg>
                                    <?php echo $hero['is_page'] ? esc_html__( 'Apri editor', 'olobuild' ) : esc_html__( 'Vai ai template', 'olobuild' ); ?>
                                </a>
                                <a class="sec" href="<?php echo esc_url( $hero['view'] ); ?>" target="_blank" rel="noopener">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M14 4h6v6M20 4l-8 8M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4"/></svg>
                                    <?php esc_html_e( 'Vedi sito live', 'olobuild' ); ?>
                                </a>
                                <button type="button" class="sec" data-olo-new-page title="<?php esc_attr_e( 'Crea un nuovo template e la pagina WP collegata', 'olobuild' ); ?>">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                                    <?php esc_html_e( 'Nuova pagina', 'olobuild' ); ?>
                                </button>
                            </div>
                        </div>
                        <div class="olo-hero-r">
                            <?php if ( $hero['status'] === 'publish' ) : ?>
                                <span class="live-pill">live</span>
                            <?php endif; ?>
                            <?php
                            // URL nella browser bar: full URL della pagina (host + path),
                            // troncato a metà se troppo lungo per stare nella barra.
                            $hero_url_display = $site_host;
                            if ( ! empty( $hero['view'] ) ) {
                                $parts = wp_parse_url( $hero['view'] );
                                $hero_url_display = ( $parts['host'] ?? $site_host ) . ( $parts['path'] ?? '' );
                                $hero_url_display = rtrim( $hero_url_display, '/' );
                            }
                            ?>
                            <div class="browser">
                                <div class="br-bar">
                                    <span class="dot r"></span><span class="dot y"></span><span class="dot g"></span>
                                    <span class="url" title="<?php echo esc_attr( $hero['view'] ?? '' ); ?>"><?php echo esc_html( $hero_url_display ); ?></span>
                                </div>
                                <?php if ( ! empty( $hero['thumbnail'] ) ) : ?>
                                    <a class="br-screenshot" href="<?php echo esc_url( $hero['view'] ); ?>" target="_blank" rel="noopener" title="<?php esc_attr_e( 'Apri pagina live', 'olobuild' ); ?>">
                                        <img src="<?php echo esc_url( $hero['thumbnail'] ); ?>" alt="<?php echo esc_attr( $hero['title'] ); ?>" loading="lazy" />
                                    </a>
                                <?php else : ?>
                                    <div class="br-body">
                                        <div class="nav">
                                            <span class="lo"><?php echo esc_html( strtoupper( substr( get_bloginfo( 'name' ), 0, 7 ) ) ); ?></span>
                                            <span><?php esc_html_e( 'Home', 'olobuild' ); ?></span>
                                            <span><?php esc_html_e( 'Servizi', 'olobuild' ); ?></span>
                                            <span><?php esc_html_e( 'Contatti', 'olobuild' ); ?></span>
                                        </div>
                                        <h2><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h2>
                                        <p><?php echo esc_html( wp_trim_words( get_bloginfo( 'description' ) ?: __( 'Il tuo sito WordPress costruito con Olobuild.', 'olobuild' ), 12 ) ); ?></p>
                                        <span class="btn"><?php esc_html_e( 'Scopri di più', 'olobuild' ); ?> →</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </section>

                    <!-- KPI strip (popolato da JS / boot data) -->
                    <section class="olo-kpi-strip" aria-label="<?php esc_attr_e( 'Indicatori chiave', 'olobuild' ); ?>"></section>

                    <!-- Recent strip -->
                    <section>
                        <div class="olo-sec-h">
                            <h2><?php esc_html_e( 'Continua dove avevi lasciato', 'olobuild' ); ?></h2>
                            <span class="hint"><?php esc_html_e( 'Le tue ultime modifiche', 'olobuild' ); ?></span>
                            <span class="spc"></span>
                            <a class="more" href="<?php echo esc_url( admin_url( 'edit.php?post_type=page&orderby=modified' ) ); ?>">
                                <?php esc_html_e( 'Vedi tutto', 'olobuild' ); ?>
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </a>
                        </div>
                        <div class="olo-recent-strip"></div>
                    </section>

                    <!-- Quick actions -->
                    <section>
                        <div class="olo-sec-h">
                            <h2><?php esc_html_e( 'Azioni rapide', 'olobuild' ); ?></h2>
                            <span class="hint"><?php esc_html_e( 'Ciò che fai più spesso', 'olobuild' ); ?></span>
                        </div>
                        <div class="olo-quick-row">
                            <button type="button" class="olo-quick-card tone-primary" data-olo-new-page>
                                <div class="ic-box"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg></div>
                                <div class="lab"><span class="t"><?php esc_html_e( 'Crea pagina', 'olobuild' ); ?></span><span class="h"><?php esc_html_e( 'Nuovo template + pagina WP', 'olobuild' ); ?></span></div>
                                <span class="arr"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
                            </button>
                            <a class="olo-quick-card tone-info" href="<?php echo esc_url( $hero['edit'] ); ?>">
                                <div class="ic-box"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20l4-1 11-11-3-3L5 16l-1 4z"/></svg></div>
                                <div class="lab"><span class="t"><?php esc_html_e( 'Apri editor', 'olobuild' ); ?></span><span class="h"><?php echo esc_html( wp_trim_words( $hero['title'], 4 ) ); ?></span></div>
                                <span class="arr"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
                            </a>
                            <a class="olo-quick-card tone-purple" href="<?php echo esc_url( admin_url( 'admin.php?page=olobuilder-templates' ) ); ?>">
                                <div class="ic-box"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg></div>
                                <div class="lab"><span class="t"><?php esc_html_e( 'Sfoglia template', 'olobuild' ); ?></span><span class="h"><?php esc_html_e( 'Pronti all\'uso', 'olobuild' ); ?></span></div>
                                <span class="arr"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
                            </a>
                            <a class="olo-quick-card tone-neutral" href="<?php echo esc_url( admin_url( 'admin.php?page=olo-import-export' ) ); ?>">
                                <div class="ic-box"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v12M7 9l5-5 5 5M5 20h14"/></svg></div>
                                <div class="lab"><span class="t"><?php esc_html_e( 'Importa', 'olobuild' ); ?></span><span class="h"><?php esc_html_e( 'Pagina, sito, JSON', 'olobuild' ); ?></span></div>
                                <span class="arr"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
                            </a>
                        </div>
                    </section>

                    <!-- Manage tile grid -->
                    <section>
                        <div class="olo-sec-h">
                            <h2><?php esc_html_e( 'Gestione', 'olobuild' ); ?></h2>
                            <span class="hint"><?php esc_html_e( 'Configurazione e contenuti del sito', 'olobuild' ); ?></span>
                        </div>
                        <div class="olo-manage-grid">
                            <?php foreach ( $manage as $i => $t ) : ?>
                            <a class="olo-manage-tile" href="<?php echo esc_url( $t['href'] ); ?>" data-id="<?php echo esc_attr( $t['id'] ); ?>" data-order="<?php echo esc_attr( $i ); ?>">
                                <div class="ic-sq" style="background: <?php echo esc_attr( $t['color'] ); ?>"><?php echo self::dashboard_svg( $t['icon'], 18 ); ?></div>
                                <div class="lab">
                                    <span class="t"><?php echo esc_html( $t['label'] ); ?></span>
                                    <span class="h"><?php echo esc_html( $t['hint'] ); ?></span>
                                </div>
                                <button type="button" class="pin" title="<?php esc_attr_e( 'Aggiungi ai preferiti', 'olobuild' ); ?>">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3l7 7-3 3 1 5-5-1-7 7v-4l-3-3 7-7-1-5z"/></svg>
                                </button>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <!-- System chips -->
                    <section>
                        <div class="olo-sec-h">
                            <h2><?php esc_html_e( 'Sistema', 'olobuild' ); ?></h2>
                            <span class="hint"><?php esc_html_e( 'Configurazione tecnica · raramente', 'olobuild' ); ?></span>
                        </div>
                        <div class="olo-system-row">
                            <?php foreach ( $system as $s ) : ?>
                            <a class="olo-system-chip" href="<?php echo esc_url( $s['href'] ); ?>">
                                <span class="ic"><?php echo self::dashboard_svg( $s['icon'], 13 ); ?></span>
                                <?php echo esc_html( $s['label'] ); ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </section>

                </main>

                <!-- Right rail -->
                <aside class="olo-rail">
                    <div class="rail-head">
                        <h2><?php esc_html_e( 'Centro risorse', 'olobuild' ); ?></h2>
                        <button type="button" class="toggle" title="<?php esc_attr_e( 'Comprimi pannello', 'olobuild' ); ?>">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l-6 6 6 6M15 6l6 6-6 6"/></svg>
                        </button>
                    </div>
                    <div class="rail-mini">
                        <button type="button" title="<?php esc_attr_e( 'Cosa c\'è di nuovo', 'olobuild' ); ?>">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 19l3-3a4 4 0 015.7 0l.3.3 4-9-9 4 .3.3a4 4 0 010 5.7L5 19zM4 14a3 3 0 00-1 6 3 3 0 006-1"/></svg>
                            <span class="dot-new"></span>
                        </button>
                        <button type="button" title="<?php esc_attr_e( 'Tutorial', 'olobuild' ); ?>">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M7 5l12 7-12 7z"/></svg>
                        </button>
                        <button type="button" title="<?php esc_attr_e( 'Documentazione', 'olobuild' ); ?>">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 015 0c0 1.5-2.5 2-2.5 4M12 17h.01"/></svg>
                        </button>
                        <button type="button" title="<?php esc_attr_e( 'Notifiche', 'olobuild' ); ?>">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 16V11a6 6 0 0112 0v5l2 2H4l2-2zM10 20a2 2 0 004 0"/></svg>
                        </button>
                    </div>
                    <div class="rail-body">
                        <div class="olo-rail-section">
                            <h3>
                                <?php esc_html_e( 'Cosa c\'è di nuovo', 'olobuild' ); ?>
                                <span class="pill">v<?php echo esc_html( OLO_VERSION ); ?></span>
                            </h3>
                            <div data-olo-changelog></div>
                        </div>
                        <div class="olo-rail-section">
                            <h3><?php esc_html_e( 'Impara Olobuild', 'olobuild' ); ?></h3>
                            <a class="olo-learn-card" href="https://olotheme.com/docs/onboarding" target="_blank" rel="noopener">
                                <div class="th" style="background: linear-gradient(135deg,#4a8c2a,#3fa23f);">🚀</div>
                                <div class="info"><span class="t"><?php esc_html_e( 'Onboarding 60 secondi', 'olobuild' ); ?></span><span class="d"><svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor"><path d="M7 5l12 7-12 7z"/></svg> 1:02</span></div>
                            </a>
                            <a class="olo-learn-card" href="https://olotheme.com/docs/templates" target="_blank" rel="noopener">
                                <div class="th" style="background: linear-gradient(135deg,#f97316,#ef4444);">🎨</div>
                                <div class="info"><span class="t"><?php esc_html_e( 'Template come pro', 'olobuild' ); ?></span><span class="d"><svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor"><path d="M7 5l12 7-12 7z"/></svg> 4:18</span></div>
                            </a>
                            <a class="olo-learn-card" href="https://olotheme.com/docs/seo" target="_blank" rel="noopener">
                                <div class="th" style="background: linear-gradient(135deg,#3b82f6,#1d4ed8);">🔍</div>
                                <div class="info"><span class="t"><?php esc_html_e( 'SEO e Open Graph', 'olobuild' ); ?></span><span class="d"><svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor"><path d="M7 5l12 7-12 7z"/></svg> 3:45</span></div>
                            </a>
                            <a class="olo-learn-card" href="https://olotheme.com/docs/performance" target="_blank" rel="noopener">
                                <div class="th" style="background: linear-gradient(135deg,#eab308,#ca8a04);">⚡</div>
                                <div class="info"><span class="t"><?php esc_html_e( 'Performance: punteggio 100', 'olobuild' ); ?></span><span class="d"><svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor"><path d="M7 5l12 7-12 7z"/></svg> 5:30</span></div>
                            </a>
                        </div>
                        <div class="olo-rail-section">
                            <h3><?php esc_html_e( 'Aiuto & supporto', 'olobuild' ); ?></h3>
                            <div class="olo-help-row">
                                <a href="https://olotheme.com/docs" target="_blank" rel="noopener">
                                    <span class="ic"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 015 0c0 1.5-2.5 2-2.5 4M12 17h.01"/></svg></span>
                                    <?php esc_html_e( 'Documentazione', 'olobuild' ); ?>
                                </a>
                                <a href="https://olotheme.com/support" target="_blank" rel="noopener">
                                    <span class="ic"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></span>
                                    <?php esc_html_e( 'Apri ticket', 'olobuild' ); ?>
                                </a>
                                <a href="https://olotheme.com/community" target="_blank" rel="noopener">
                                    <span class="ic"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0112 0"/><circle cx="17" cy="9" r="2.5"/><path d="M14 20a5 5 0 017-4.5"/></svg></span>
                                    <?php esc_html_e( 'Community', 'olobuild' ); ?>
                                </a>
                                <a href="https://olotheme.com/roadmap" target="_blank" rel="noopener">
                                    <span class="ic"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M14 4h6v6M20 4l-8 8M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4"/></svg></span>
                                    <?php esc_html_e( 'Roadmap', 'olobuild' ); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        <?php
        self::cockpit_shell_close();
    }

    /**
     * Mappa nome icona → SVG inline (subset usato dalla dashboard cockpit).
     */
    public static function dashboard_svg( $name, $size = 18 ) {
        $paths = [
            'template' => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>',
            'sliders'  => '<line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/>',
            'image'    => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/>',
            'form'     => '<path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>',
            'chart'    => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
            'cookie'   => '<circle cx="12" cy="12" r="9"/><circle cx="9" cy="9" r="1"/><circle cx="15" cy="11" r="1"/><circle cx="11" cy="15" r="1"/>',
            'search'   => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>',
            'redirect' => '<path d="M3 9l4-4 4 4M7 5v9a4 4 0 004 4h7M21 15l-4 4-4-4"/>',
            'zap'      => '<path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>',
            'wrench'   => '<path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/>',
            'cart'     => '<circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/>',
            'modal'    => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/>',
            'tag'      => '<path d="M3 12V4h8l10 10-8 8z"/><circle cx="7.5" cy="7.5" r="1"/>',
            'upload'   => '<path d="M12 4v12M7 9l5-5 5 5M5 20h14"/>',
            'users'    => '<circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0112 0"/><circle cx="17" cy="9" r="2.5"/><path d="M14 20a5 5 0 017-4.5"/>',
            'inbox'    => '<path d="M3 12l3-7h12l3 7v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6zM3 12h5l1 2h6l1-2h5"/>',
            'history'  => '<path d="M3 3v5h5"/><path d="M3.05 13A9 9 0 106 5.3L3 8"/><path d="M12 7v5l4 2"/>',
            'key'      => '<circle cx="8" cy="15" r="4"/><path d="M11 12l9-9 2 2-2 2 2 2-2 2-3-3"/>',
        ];
        $path = $paths[ $name ] ?? '';
        return '<svg width="' . (int) $size . '" height="' . (int) $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">' . $path . '</svg>';
    }


    /**
     * Registra le impostazioni API keys nella WP Settings API.
     */
    public function register_settings() {
        // Sezione API Keys
        add_settings_section(
            'olo_api_keys_section',
            __( 'API Keys — Stock Media', 'olobuild' ),
            function () {
                echo '<p>' . esc_html__( 'Inserisci le chiavi API per i servizi di immagini/video stock. Se lasci un campo vuoto, verrà usata la chiave predefinita.', 'olobuild' ) . '</p>';
            },
            'olobuilder-settings'
        );

        // Pexels
        register_setting( 'olo_settings_group', 'olo_pexels_api_key', [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        ] );
        add_settings_field(
            'olo_pexels_api_key',
            __( 'Pexels API Key', 'olobuild' ),
            function () {
                $val = get_option( 'olo_pexels_api_key', '' );
                $via_const = defined( 'OLO_PEXELS_API_KEY' ) && OLO_PEXELS_API_KEY;
                echo '<input type="text" name="olo_pexels_api_key" value="' . esc_attr( $val ) . '" class="regular-text"' . ( $via_const ? ' placeholder="' . esc_attr__( 'Definita in wp-config.php (OLO_PEXELS_API_KEY)', 'olobuild' ) . '" disabled' : '' ) . ' />';
                echo '<p class="description">' . sprintf(
                    /* translators: %s = URL */
                    __( 'Ottieni una chiave su %s', 'olobuild' ),
                    '<a href="https://www.pexels.com/api/" target="_blank" rel="noopener">pexels.com/api</a>'
                ) . '</p>';
            },
            'olobuilder-settings',
            'olo_api_keys_section'
        );

        // Pixabay
        register_setting( 'olo_settings_group', 'olo_pixabay_api_key', [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        ] );
        add_settings_field(
            'olo_pixabay_api_key',
            __( 'Pixabay API Key', 'olobuild' ),
            function () {
                $val = get_option( 'olo_pixabay_api_key', '' );
                $via_const = defined( 'OLO_PIXABAY_API_KEY' ) && OLO_PIXABAY_API_KEY;
                echo '<input type="text" name="olo_pixabay_api_key" value="' . esc_attr( $val ) . '" class="regular-text"' . ( $via_const ? ' placeholder="' . esc_attr__( 'Definita in wp-config.php (OLO_PIXABAY_API_KEY)', 'olobuild' ) . '" disabled' : '' ) . ' />';
                echo '<p class="description">' . sprintf(
                    __( 'Ottieni una chiave su %s', 'olobuild' ),
                    '<a href="https://pixabay.com/api/docs/" target="_blank" rel="noopener">pixabay.com/api/docs</a>'
                ) . '</p>';
            },
            'olobuilder-settings',
            'olo_api_keys_section'
        );

        // Unsplash
        register_setting( 'olo_settings_group', 'olo_unsplash_api_key', [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        ] );
        add_settings_field(
            'olo_unsplash_api_key',
            __( 'Unsplash Access Key', 'olobuild' ),
            function () {
                $val = get_option( 'olo_unsplash_api_key', '' );
                $via_const = defined( 'OLO_UNSPLASH_API_KEY' ) && OLO_UNSPLASH_API_KEY;
                echo '<input type="text" name="olo_unsplash_api_key" value="' . esc_attr( $val ) . '" class="regular-text"' . ( $via_const ? ' placeholder="' . esc_attr__( 'Definita in wp-config.php (OLO_UNSPLASH_API_KEY)', 'olobuild' ) . '" disabled' : '' ) . ' />';
                echo '<p class="description">' . sprintf(
                    __( 'Ottieni una chiave su %s', 'olobuild' ),
                    '<a href="https://unsplash.com/developers" target="_blank" rel="noopener">unsplash.com/developers</a>'
                ) . '</p>';
            },
            'olobuilder-settings',
            'olo_api_keys_section'
        );

        // Freesound
        register_setting( 'olo_settings_group', 'olo_freesound_api_key', [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        ] );
        add_settings_field(
            'olo_freesound_api_key',
            __( 'Freesound API Key', 'olobuild' ),
            function () {
                $val = get_option( 'olo_freesound_api_key', '' );
                echo '<input type="text" name="olo_freesound_api_key" value="' . esc_attr( $val ) . '" class="regular-text" placeholder="xbKm7Gp3..." />';
                echo '<p class="description">' . sprintf(
                    __( 'Ottieni una chiave su %s', 'olobuild' ),
                    '<a href="https://freesound.org/apiv2/apply" target="_blank" rel="noopener">freesound.org/apiv2/apply</a>'
                ) . '</p>';
            },
            'olobuilder-settings',
            'olo_api_keys_section'
        );

        // ── Sezione reCAPTCHA v3 ──
        add_settings_section(
            'olo_recaptcha_section',
            __( 'reCAPTCHA v3', 'olobuild' ),
            function () {
                echo '<p>' . esc_html__( 'Google reCAPTCHA v3 per la protezione dei form. Ottieni le chiavi su google.com/recaptcha', 'olobuild' ) . '</p>';
            },
            'olobuilder-settings'
        );

        register_setting( 'olo_settings_group', 'olo_recaptcha_site_key', [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        ] );
        add_settings_field(
            'olo_recaptcha_site_key',
            __( 'Site Key', 'olobuild' ),
            function () {
                $val = get_option( 'olo_recaptcha_site_key', '' );
                echo '<input type="text" name="olo_recaptcha_site_key" value="' . esc_attr( $val ) . '" class="regular-text" />';
            },
            'olobuilder-settings',
            'olo_recaptcha_section'
        );

        register_setting( 'olo_settings_group', 'olo_recaptcha_secret_key', [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        ] );
        add_settings_field(
            'olo_recaptcha_secret_key',
            __( 'Secret Key', 'olobuild' ),
            function () {
                $val = get_option( 'olo_recaptcha_secret_key', '' );
                echo '<input type="password" name="olo_recaptcha_secret_key" value="' . esc_attr( $val ) . '" class="regular-text" />';
            },
            'olobuilder-settings',
            'olo_recaptcha_section'
        );

        // ── Sezione Mailchimp ──
        add_settings_section(
            'olo_mailchimp_section',
            __( 'Mailchimp', 'olobuild' ),
            function () {
                echo '<p>' . esc_html__( 'API key Mailchimp per le integrazioni dei form contatti.', 'olobuild' ) . '</p>';
            },
            'olobuilder-settings'
        );

        register_setting( 'olo_settings_group', 'olo_mailchimp_api_key', [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        ] );
        add_settings_field(
            'olo_mailchimp_api_key',
            __( 'Mailchimp API Key', 'olobuild' ),
            function () {
                $val = get_option( 'olo_mailchimp_api_key', '' );
                echo '<input type="text" name="olo_mailchimp_api_key" value="' . esc_attr( $val ) . '" class="regular-text" placeholder="xxxxxxxxxxxxxxxx-us1" />';
                echo '<p class="description">' . sprintf(
                    __( 'Ottieni una chiave su %s', 'olobuild' ),
                    '<a href="https://mailchimp.com/help/about-api-keys/" target="_blank" rel="noopener">mailchimp.com</a>'
                ) . '</p>';
            },
            'olobuilder-settings',
            'olo_mailchimp_section'
        );
    }

    /**
     * Renderizza la pagina Configurazione Olobuild (Vue app con tab).
     */
    public function render_configurazione_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        // v1.0.30 — redesign: la pagina ora occupa l'intera area #wpbody con shell propria
        // (topbar + sidebar console navy + savebar). Niente più cockpit chrome — Vue gestisce tutto.
        // .olo-cfg-wrap toglie il margin/padding di default di .wrap WP.
        ?>
        <div class="wrap olo-cfg-wrap">
            <h1 class="screen-reader-text"><?php esc_html_e( 'Configurazione Olobuild', 'olobuild' ); ?></h1>
            <div id="olo-admin-settings"></div>
        </div>
        <?php
    }

    /**
     * REST endpoints per la pagina Configurazione.
     */
    public function register_settings_rest_routes() {
        $ns = 'olo/v1';

        // API Keys
        register_rest_route( $ns, '/settings/api-keys', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'rest_get_api_keys' ],
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'rest_put_api_keys' ],
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // Breakpoints
        register_rest_route( $ns, '/settings/breakpoints', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'rest_get_breakpoints' ],
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'rest_put_breakpoints' ],
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // Palette colori (ColorsTab in Configurazione): brand palette + scala neutri + dark
        register_rest_route( $ns, '/settings/global-colors', [
            [
                'methods'             => 'GET',
                'callback'            => function () {
                    return rest_ensure_response( [
                        'palette'      => get_option( 'olo_palette', [] ),
                        'neutrals'     => get_option( 'olo_neutrals', [] ),
                        'neutral_mode' => get_option( 'olo_neutral_mode', 'auto' ),
                        'neutral_tint' => get_option( 'olo_neutral_tint', 'zinc' ),
                        'dark'         => get_option( 'olo_dark_settings', [ 'enabled' => true, 'strategy' => 'auto' ] ),
                    ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'PUT',
                'callback'            => function ( $req ) {
                    $b = $req->get_json_params();
                    if ( ! is_array( $b ) ) $b = [];

                    if ( isset( $b['palette'] ) && is_array( $b['palette'] ) ) {
                        $clean = [];
                        foreach ( $b['palette'] as $p ) {
                            if ( ! is_array( $p ) ) continue;
                            $hex = sanitize_hex_color( $p['hex'] ?? '' );
                            $clean[] = [
                                'id'   => sanitize_key( $p['id'] ?? '' ),
                                'name' => sanitize_text_field( $p['name'] ?? '' ),
                                'role' => sanitize_text_field( $p['role'] ?? '' ),
                                'hex'  => $hex ?: '#000000',
                            ];
                        }
                        update_option( 'olo_palette', $clean );
                    }

                    if ( isset( $b['neutrals'] ) && is_array( $b['neutrals'] ) ) {
                        $clean = array_values( array_filter( array_map( 'sanitize_hex_color', $b['neutrals'] ) ) );
                        update_option( 'olo_neutrals', $clean );
                    }

                    if ( isset( $b['neutral_mode'] ) ) {
                        $m = $b['neutral_mode'] === 'manual' ? 'manual' : 'auto';
                        update_option( 'olo_neutral_mode', $m );
                    }

                    if ( isset( $b['neutral_tint'] ) ) {
                        $allowed = [ 'slate', 'gray', 'zinc', 'neutral', 'stone' ];
                        $tint = in_array( $b['neutral_tint'], $allowed, true ) ? $b['neutral_tint'] : 'zinc';
                        update_option( 'olo_neutral_tint', $tint );
                    }

                    if ( isset( $b['dark'] ) && is_array( $b['dark'] ) ) {
                        $strategy = in_array( $b['dark']['strategy'] ?? '', [ 'auto', 'manual', 'luminance' ], true ) ? $b['dark']['strategy'] : 'auto';
                        update_option( 'olo_dark_settings', [
                            'enabled'  => ! empty( $b['dark']['enabled'] ),
                            'strategy' => $strategy,
                        ] );
                    }

                    update_option( 'olo_settings_last_saved', time() );
                    return rest_ensure_response( [ 'ok' => true ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // ─── Endpoint generici per i 5 tab migrati in Configurazione (v1.0.30) ───
        // Cookie Consent — option array unica `olo_cookie_settings`
        register_rest_route( $ns, '/cookie-consent', [
            [
                'methods'             => 'GET',
                'callback'            => function () {
                    return rest_ensure_response( get_option( 'olo_cookie_settings', [] ) );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'POST',
                'callback'            => function ( $req ) {
                    $existing = get_option( 'olo_cookie_settings', [] );
                    $payload  = $req->get_json_params();
                    if ( ! is_array( $payload ) ) $payload = [];
                    update_option( 'olo_cookie_settings', array_merge( $existing, $payload ) );
                    update_option( 'olo_settings_last_saved', time() );
                    return rest_ensure_response( [ 'ok' => true ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // Performance — option array unica `olo_performance`
        register_rest_route( $ns, '/performance', [
            [
                'methods'             => 'GET',
                'callback'            => function () {
                    return rest_ensure_response( get_option( 'olo_performance', [] ) );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'POST',
                'callback'            => function ( $req ) {
                    $existing = get_option( 'olo_performance', [] );
                    $payload  = $req->get_json_params();
                    if ( ! is_array( $payload ) ) $payload = [];
                    update_option( 'olo_performance', array_merge( $existing, $payload ) );
                    update_option( 'olo_settings_last_saved', time() );
                    return rest_ensure_response( [ 'ok' => true ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // Performance stats — read-only, dati reali da Critical CSS + CSS cache
        register_rest_route( $ns, '/performance/stats', [
            [
                'methods'             => 'GET',
                'callback'            => function () {
                    $opt = get_option( 'olo_performance', [] );
                    if ( ! is_array( $opt ) ) $opt = [];

                    // Critical CSS pages cached
                    $ccss_count = 0;
                    $ccss_last  = '';
                    if ( class_exists( 'Olo_Critical_CSS' ) && method_exists( 'Olo_Critical_CSS', 'get_status' ) ) {
                        $st = Olo_Critical_CSS::get_status();
                        if ( is_array( $st ) ) {
                            $ccss_count = (int) ( $st['cached_count'] ?? 0 );
                            $ccss_last  = (string) ( $st['last_generated'] ?? '' );
                        }
                    }

                    // CSS cache files dir
                    $upload_dir = wp_upload_dir();
                    $cache_dir  = trailingslashit( $upload_dir['basedir'] ) . 'olobuild-cache/';
                    $cache_count = 0;
                    $cache_size  = 0;
                    if ( is_dir( $cache_dir ) ) {
                        $files = glob( $cache_dir . 'olo-*.css' ) ?: [];
                        $cache_count = count( $files );
                        foreach ( $files as $f ) $cache_size += filesize( $f ) ?: 0;
                    }

                    // Templates totali (proxy per "pages_total")
                    global $wpdb;
                    $pages_total = (int) $wpdb->get_var( $wpdb->prepare(
                        "SELECT COUNT(*) FROM {$wpdb->prefix}olo_templates WHERE status = %s",
                        'published'
                    ) );

                    // Score derivato dai flag performance attivi
                    $flag_keys = [
                        'critical_css_enabled', 'defer_js', 'minify_css', 'css_cache_files',
                        'resource_hints', 'font_preload', 'video_facade', 'fetchpriority',
                        'lazy_images', 'remove_jquery_migrate', 'remove_emoji_scripts',
                    ];
                    $on = 0;
                    foreach ( $flag_keys as $k ) if ( ! empty( $opt[ $k ] ) ) $on++;
                    $score = (int) round( 60 + ( $on / count( $flag_keys ) ) * 40 ); // 60-100

                    return rest_ensure_response( [
                        'score'           => $score,
                        'pages_cached'    => $ccss_count + $cache_count,
                        'pages_total'     => max( $pages_total, $ccss_count + $cache_count ),
                        'hit_rate'        => '—', // no log layer yet
                        'size'            => size_format( $cache_size, 1 ),
                        'size_max'        => '500 MB',
                        'bandwidth_saved' => '—',
                        'last_purge'      => $ccss_last ?: '—',
                    ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // Stockmedia behavior — preferenze importazione stock provider
        register_rest_route( $ns, '/settings/stockmedia-behavior', [
            [
                'methods'             => 'GET',
                'callback'            => function () {
                    $defaults = [ 'preferred' => 'unsplash', 'download_local' => true, 'optimize_on_download' => false ];
                    $saved = get_option( 'olo_stockmedia_behavior', [] );
                    if ( ! is_array( $saved ) ) $saved = [];
                    return rest_ensure_response( wp_parse_args( $saved, $defaults ) );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'POST',
                'callback'            => function ( $req ) {
                    $b = $req->get_json_params();
                    if ( ! is_array( $b ) ) $b = [];
                    $allowed_providers = [ 'unsplash', 'pexels', 'pixabay', 'freesound', 'openverse' ];
                    $clean = [
                        'preferred'            => in_array( $b['preferred'] ?? '', $allowed_providers, true ) ? $b['preferred'] : 'unsplash',
                        'download_local'       => ! empty( $b['download_local'] ),
                        'optimize_on_download' => ! empty( $b['optimize_on_download'] ),
                    ];
                    update_option( 'olo_stockmedia_behavior', $clean );
                    update_option( 'olo_settings_last_saved', time() );
                    return rest_ensure_response( [ 'ok' => true ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // AI usage — rolling stats degli ultimi 30 giorni
        register_rest_route( $ns, '/ai/usage', [
            [
                'methods'             => 'GET',
                'callback'            => function () {
                    $log = get_option( 'olo_ai_usage', [] );
                    if ( ! is_array( $log ) ) $log = [];

                    // Filter ultimi 30 giorni
                    $cutoff = time() - 30 * DAY_IN_SECONDS;
                    $recent = array_filter( $log, function ( $e ) use ( $cutoff ) {
                        return is_array( $e ) && ( $e['ts'] ?? 0 ) >= $cutoff;
                    } );

                    if ( empty( $recent ) ) {
                        return rest_ensure_response( [
                            'calls'         => 0,
                            'tokens'        => '0',
                            'cost_estimate' => '0.00',
                            'latency_avg'   => '0',
                        ] );
                    }

                    $calls = count( $recent );
                    $tokens = 0; $cost = 0.0; $latency_sum = 0;
                    foreach ( $recent as $e ) {
                        $tokens      += (int) ( $e['tokens']  ?? 0 );
                        $cost        += (float) ( $e['cost']  ?? 0 );
                        $latency_sum += (int) ( $e['ms']      ?? 0 );
                    }
                    $latency_avg = $calls > 0 ? round( ( $latency_sum / $calls ) / 1000, 1 ) : 0;

                    return rest_ensure_response( [
                        'calls'         => $calls,
                        'tokens'        => $tokens >= 1000 ? round( $tokens / 1000, 1 ) . 'k' : (string) $tokens,
                        'cost_estimate' => number_format_i18n( $cost, 2 ),
                        'latency_avg'   => (string) $latency_avg,
                    ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // SEO — 6 sub-endpoint, ognuno mappa a una option WP separata
        $seo_subkeys = [
            'titles'         => 'olo_seo_titles',
            'social'         => 'olo_seo_social',
            'local_business' => 'olo_seo_local_business',
            'webmaster'      => 'olo_seo_webmaster',
            'sitemap'        => 'olo_seo_sitemap',
            'advanced'       => 'olo_seo_advanced',
        ];
        foreach ( $seo_subkeys as $route => $option_key ) {
            register_rest_route( $ns, '/seo/' . $route, [
                [
                    'methods'             => 'GET',
                    'callback'            => function () use ( $option_key ) {
                        return rest_ensure_response( get_option( $option_key, [] ) );
                    },
                    'permission_callback' => function () { return current_user_can( 'manage_options' ); },
                ],
                [
                    'methods'             => 'POST',
                    'callback'            => function ( $req ) use ( $option_key ) {
                        $existing = get_option( $option_key, [] );
                        $payload  = $req->get_json_params();
                        if ( ! is_array( $payload ) ) $payload = [];
                        update_option( $option_key, array_merge( $existing, $payload ) );
                        update_option( 'olo_settings_last_saved', time() );
                        return rest_ensure_response( [ 'ok' => true ] );
                    },
                    'permission_callback' => function () { return current_user_can( 'manage_options' ); },
                ],
            ] );
        }

        // ─── Endpoint generici v1.0.31 (sprint configurazione fase 2) ───
        // Analytics / Tracking — option array `olo_analytics`
        register_rest_route( $ns, '/analytics', [
            [
                'methods'             => 'GET',
                'callback'            => function () {
                    return rest_ensure_response( get_option( 'olo_analytics', [] ) );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'POST',
                'callback'            => function ( $req ) {
                    $existing = get_option( 'olo_analytics', [] );
                    $payload  = $req->get_json_params();
                    if ( ! is_array( $payload ) ) $payload = [];
                    update_option( 'olo_analytics', array_merge( $existing, $payload ) );
                    // Mantieni anche le 3 option singole legacy per compat con codice frontend più vecchio.
                    foreach ( [ 'ga_id', 'fb_pixel_id', 'gtm_id' ] as $legacy_k ) {
                        if ( isset( $payload[ $legacy_k ] ) ) {
                            update_option( 'olo_' . ( $legacy_k === 'ga_id' ? 'ga_measurement_id' : $legacy_k ), sanitize_text_field( $payload[ $legacy_k ] ) );
                        }
                    }
                    update_option( 'olo_settings_last_saved', time() );
                    return rest_ensure_response( [ 'ok' => true ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // Maintenance & Coming Soon — 5 option singole
        register_rest_route( $ns, '/maintenance', [
            [
                'methods'             => 'GET',
                'callback'            => function () {
                    return rest_ensure_response( [
                        'mode'                    => get_option( 'olo_maintenance_mode', 'off' ),
                        'template_id'             => (int) get_option( 'olo_maintenance_template_id', 0 ),
                        'coming_soon_template_id' => (int) get_option( 'olo_coming_soon_template_id', 0 ),
                        'bypass_roles'            => (array) get_option( 'olo_maintenance_bypass_roles', [ 'administrator' ] ),
                        'bypass_secret'           => get_option( 'olo_maintenance_bypass_secret', '' ),
                    ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'POST',
                'callback'            => function ( $req ) {
                    $p = $req->get_json_params();
                    if ( isset( $p['mode'] ) ) {
                        $mode = in_array( $p['mode'], [ 'off', 'coming_soon', 'maintenance' ], true ) ? $p['mode'] : 'off';
                        update_option( 'olo_maintenance_mode', $mode );
                    }
                    if ( isset( $p['template_id'] ) )             update_option( 'olo_maintenance_template_id', (int) $p['template_id'] );
                    if ( isset( $p['coming_soon_template_id'] ) ) update_option( 'olo_coming_soon_template_id', (int) $p['coming_soon_template_id'] );
                    if ( isset( $p['bypass_roles'] ) && is_array( $p['bypass_roles'] ) ) {
                        update_option( 'olo_maintenance_bypass_roles', array_map( 'sanitize_key', $p['bypass_roles'] ) );
                    }
                    if ( isset( $p['bypass_secret'] ) )            update_option( 'olo_maintenance_bypass_secret', sanitize_text_field( $p['bypass_secret'] ) );
                    update_option( 'olo_settings_last_saved', time() );
                    return rest_ensure_response( [ 'ok' => true ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // Maintenance — genera un template "Coming Soon" precaricato e lo seleziona automaticamente
        register_rest_route( $ns, '/maintenance/generate-template', [
            'methods'             => 'POST',
            'callback'            => function ( $req ) {
                $params = $req->get_json_params();
                $kind   = ( $params['kind'] ?? 'coming_soon' ) === 'maintenance' ? 'maintenance' : 'coming_soon';
                if ( ! class_exists( 'Olo_Database' ) ) {
                    return new WP_Error( 'no_db', 'Olo_Database non disponibile', [ 'status' => 500 ] );
                }
                $launch_at = strtotime( '+30 days' );
                $launch_iso = wp_date( 'Y-m-d\TH:i:s', $launch_at );
                $title_text = $kind === 'coming_soon' ? __( 'Stiamo per arrivare', 'olobuild' ) : __( 'Sito in manutenzione', 'olobuild' );
                $sub_text   = $kind === 'coming_soon'
                    ? __( 'Un\'esperienza unica sta per cominciare. Lascia la tua email per essere tra i primi a scoprirla.', 'olobuild' )
                    : __( 'Stiamo aggiornando il sito per offrirti una nuova esperienza. Torniamo presto.', 'olobuild' );
                $bg_color   = $kind === 'coming_soon' ? '#0f172a' : '#1c1917';
                $accent     = '#e1474f';

                $headline_id  = wp_generate_uuid4();
                $sub_id       = wp_generate_uuid4();
                $countdown_id = wp_generate_uuid4();
                $spacer_id    = wp_generate_uuid4();
                $btn_id       = wp_generate_uuid4();
                $col_id       = wp_generate_uuid4();
                $row_id       = wp_generate_uuid4();
                $section_id   = wp_generate_uuid4();

                // Template content: 1 section centered → 1 row → 1 column → headline + text + countdown + button.
                // I tile usano default config; settings essenziali sotto.
                $content = [
                    [
                        'id'   => $section_id,
                        'type' => 'section',
                        'settings' => [
                            'width'         => 'default',
                            'bg_scope'      => 'container',
                            'padding'       => 'large',
                            'sticky_effect' => 'none',
                            'flex_direction'=> 'column',
                            'flex_justify'  => 'center',
                            'flex_align'    => 'center',
                        ],
                        'style' => [
                            'bg' => [
                                'type'  => 'solid',
                                'color' => $bg_color,
                                'gradient_angle' => 135, 'gradient_from' => $bg_color, 'gradient_to' => '#1e1b4b',
                                'image_url' => '', 'image_size' => 'cover', 'image_position' => 'center center',
                                'video_url' => '', 'video_poster' => '', 'video_fit' => 'cover',
                                'overlay_color' => '#000000', 'overlay_opacity' => 0, 'color_opacity' => 100,
                            ],
                            'tile_min_height' => '100vh',
                            'padding_top' => 80, 'padding_right' => 24, 'padding_bottom' => 80, 'padding_left' => 24,
                        ],
                        'advanced' => [],
                        'children' => [
                            [
                                'id'   => $row_id,
                                'type' => 'row',
                                'settings' => [ 'layout' => '100', 'gap' => '24', 'vertical_align' => 'stretch' ],
                                'style'    => [],
                                'advanced' => [],
                                'children' => [
                                    [
                                        'id'   => $col_id,
                                        'type' => 'column',
                                        'settings' => [],
                                        'style'    => [ 'tile_max_width' => '720px' ],
                                        'advanced' => [],
                                        'children' => [
                                            [
                                                'id'   => $headline_id,
                                                'type' => 'headline',
                                                'settings' => [
                                                    'preset'             => 'custom',
                                                    'heading'            => $title_text,
                                                    'subtitle'           => '',
                                                    'tag'                => 'h1',
                                                    'alignment'          => 'center',
                                                    'heading_size'       => 'xl',
                                                    'heading_font_size'  => '64',
                                                    'heading_color'      => '#ffffff',
                                                    'heading_italic'     => false,
                                                    'heading_uppercase'  => false,
                                                    'decoration'         => 'none',
                                                ],
                                                'style' => [ 'margin_bottom' => 16 ],
                                                'advanced' => [],
                                                'children' => [],
                                            ],
                                            [
                                                'id'   => $sub_id,
                                                'type' => 'text-block',
                                                'settings' => [
                                                    'content' => '<p style="font-size:18px;line-height:1.6;color:rgba(255,255,255,.75);text-align:center;max-width:560px;margin:0 auto;">' . esc_html( $sub_text ) . '</p>',
                                                ],
                                                'style' => [ 'margin_bottom' => 40 ],
                                                'advanced' => [],
                                                'children' => [],
                                            ],
                                            $kind === 'coming_soon' ? [
                                                'id'   => $countdown_id,
                                                'type' => 'countdown',
                                                'settings' => [
                                                    'date'             => $launch_iso,
                                                    'show_days'        => true,
                                                    'show_hours'       => true,
                                                    'show_minutes'     => true,
                                                    'show_seconds'     => true,
                                                    'expired_message'  => __( 'È arrivato il momento!', 'olobuild' ),
                                                    'digit_color'      => '#ffffff',
                                                    'label_color'      => 'rgba(255,255,255,0.6)',
                                                    'alignment'        => 'center',
                                                    'digit_font_size'  => '56',
                                                    'label_font_size'  => '13',
                                                    'days_label'       => __( 'Giorni', 'olobuild' ),
                                                    'hours_label'      => __( 'Ore', 'olobuild' ),
                                                    'minutes_label'    => __( 'Minuti', 'olobuild' ),
                                                    'seconds_label'    => __( 'Secondi', 'olobuild' ),
                                                ],
                                                'style' => [ 'margin_bottom' => 32 ],
                                                'advanced' => [],
                                                'children' => [],
                                            ] : [
                                                'id'   => $spacer_id,
                                                'type' => 'spacer',
                                                'settings' => [ 'height' => '40' ],
                                                'style'    => [],
                                                'advanced' => [],
                                                'children' => [],
                                            ],
                                            [
                                                'id'   => $btn_id,
                                                'type' => 'button',
                                                'settings' => [
                                                    'preset'           => 'modern-clean',
                                                    'text'             => $kind === 'coming_soon' ? __( 'Avvisami al lancio', 'olobuild' ) : __( 'Contattaci', 'olobuild' ),
                                                    'url'              => 'mailto:' . get_bloginfo( 'admin_email' ),
                                                    'target'           => '_self',
                                                    'alignment'        => 'center',
                                                    'full_width'       => false,
                                                    'bg'               => [ 'type' => 'none' ],
                                                    'bg_color'         => $accent,
                                                    'text_color'       => '#ffffff',
                                                    'border_radius'    => '999',
                                                    'font_size'        => '16',
                                                    'font_weight'      => '600',
                                                    'letter_spacing'   => '0',
                                                    'text_transform'   => 'none',
                                                    'tile_padding'     => [ 'top' => 16, 'right' => 36, 'bottom' => 16, 'left' => 36 ],
                                                    'icon_position'    => 'before',
                                                    'icon_spacing'     => '8',
                                                    'shadow'           => 'sm',
                                                    'hover_bg_color'   => '#c8323a',
                                                    'hover_text_color' => '#ffffff',
                                                    'hover_effect'     => 'lift',
                                                ],
                                                // Forza il wrapper a NON ereditare bg_color dai settings.
                                                // Senza questo, il renderer element copia settings.bg_color
                                                // (rosso del button) anche allo style del wrapper esterno,
                                                // creando il doppio sfondo "scatola rossa" attorno al button.
                                                'style' => [ 'bg_color' => 'transparent', 'bg' => [ 'type' => 'none' ] ],
                                                'advanced' => [],
                                                'children' => [],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];

                $db = new Olo_Database();
                $title = $kind === 'coming_soon' ? __( 'Coming Soon — Default', 'olobuild' ) : __( 'Manutenzione — Default', 'olobuild' );
                $new_id = $db->create_template( [
                    'title'   => $title,
                    'type'    => 'page',
                    'content' => $content,
                    'status'  => 'published',
                    'settings'=> new stdClass(),
                ] );

                if ( ! $new_id ) {
                    return new WP_Error( 'create_failed', 'Errore creazione template', [ 'status' => 500 ] );
                }

                $option_key = $kind === 'coming_soon' ? 'olo_coming_soon_template_id' : 'olo_maintenance_template_id';
                update_option( $option_key, (int) $new_id );
                update_option( 'olo_settings_last_saved', time() );

                return rest_ensure_response( [
                    'ok'          => true,
                    'template_id' => (int) $new_id,
                    'title'       => $title,
                    'edit_url'    => admin_url( 'admin.php?page=olobuilder-templates&template_id=' . (int) $new_id ),
                ] );
            },
            'permission_callback' => function () { return current_user_can( 'manage_options' ); },
        ] );

        // WooCommerce templates — 5 option singole
        register_rest_route( $ns, '/woo-templates', [
            [
                'methods'             => 'GET',
                'callback'            => function () {
                    return rest_ensure_response( [
                        'olo_woo_tpl_product_single'  => (int) get_option( 'olo_woo_tpl_product_single', 0 ),
                        'olo_woo_tpl_product_archive' => (int) get_option( 'olo_woo_tpl_product_archive', 0 ),
                        'olo_woo_tpl_cart'            => (int) get_option( 'olo_woo_tpl_cart', 0 ),
                        'olo_woo_tpl_checkout'        => (int) get_option( 'olo_woo_tpl_checkout', 0 ),
                        'olo_woo_tpl_myaccount'       => (int) get_option( 'olo_woo_tpl_myaccount', 0 ),
                        'woo_active'                  => class_exists( 'WooCommerce' ),
                    ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'POST',
                'callback'            => function ( $req ) {
                    $p = $req->get_json_params();
                    $allowed = [ 'olo_woo_tpl_product_single', 'olo_woo_tpl_product_archive', 'olo_woo_tpl_cart', 'olo_woo_tpl_checkout', 'olo_woo_tpl_myaccount' ];
                    foreach ( $allowed as $k ) {
                        if ( isset( $p[ $k ] ) ) update_option( $k, (int) $p[ $k ] );
                    }
                    update_option( 'olo_settings_last_saved', time() );
                    return rest_ensure_response( [ 'ok' => true ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // Design Presets — behavior (overwrite_manual, snapshot_before, preview_mode)
        // + snapshots stub (per ora list/restore/delete: noop, gli stili reali sono in olo_styles).
        register_rest_route( $ns, '/design-presets/behavior', [
            [
                'methods'             => 'GET',
                'callback'            => function () {
                    return rest_ensure_response( get_option( 'olo_design_preset_behavior', [
                        'overwrite_manual' => true,
                        'snapshot_before'  => true,
                        'preview_mode'     => 'side_by_side',
                    ] ) );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'POST',
                'callback'            => function ( $req ) {
                    $payload = $req->get_json_params();
                    if ( ! is_array( $payload ) ) $payload = [];
                    update_option( 'olo_design_preset_behavior', $payload );
                    update_option( 'olo_settings_last_saved', time() );
                    return rest_ensure_response( [ 'ok' => true ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // Design Presets — apply (stub graceful, l'apply reale richiede integrazione stylesStore).
        register_rest_route( $ns, '/design-presets/apply', [
            'methods'             => 'POST',
            'callback'            => function ( $req ) {
                $p = $req->get_json_params();
                $preset_id = sanitize_key( $p['preset_id'] ?? '' );
                update_option( 'olo_active_preset_id', $preset_id );
                update_option( 'olo_settings_last_saved', time() );
                // TODO: applicare effettivamente i colori/font/tokens del preset a `olo_styles`,
                // `olo_global_colors`, `olo_global_typography`. Per ora salva solo il flag attivo.
                return rest_ensure_response( [ 'ok' => true, 'preset_id' => $preset_id ] );
            },
            'permission_callback' => function () { return current_user_can( 'manage_options' ); },
        ] );

        // Design Presets — snapshots (stub: list ritorna [], action: noop)
        register_rest_route( $ns, '/design-presets/snapshots', [
            [
                'methods'             => 'GET',
                'callback'            => function () {
                    return rest_ensure_response( get_option( 'olo_design_preset_snapshots', [] ) );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'POST',
                'callback'            => function () {
                    // Stub — TODO: implementare restore/delete reale che ripristina olo_styles ecc.
                    return rest_ensure_response( [ 'ok' => true ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // Redirects — CRUD su tabella custom + log 404
        register_rest_route( $ns, '/redirects', [
            [
                'methods'             => 'GET',
                'callback'            => function () {
                    global $wpdb;
                    $redirects = [];
                    $log404    = [];
                    $rt = $wpdb->prefix . 'olo_redirects';
                    $lt = $wpdb->prefix . 'olo_404_log';
                    if ( $wpdb->get_var( "SHOW TABLES LIKE '$rt'" ) === $rt ) {
                        $redirects = $wpdb->get_results( "SELECT id, from_url, to_url, type, hits FROM $rt ORDER BY id DESC LIMIT 500", ARRAY_A );
                    }
                    if ( $wpdb->get_var( "SHOW TABLES LIKE '$lt'" ) === $lt ) {
                        $log404 = $wpdb->get_results( "SELECT id, url, hits, last_hit FROM $lt ORDER BY hits DESC LIMIT 200", ARRAY_A );
                    }
                    $advanced = (array) get_option( 'olo_seo_advanced', [] );
                    return rest_ensure_response( [
                        'redirects'    => $redirects ?: [],
                        'log404'       => $log404 ?: [],
                        'indexnow_key' => $advanced['indexnow_key'] ?? '',
                    ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'POST',
                'callback'            => function ( $req ) {
                    global $wpdb;
                    $p = $req->get_json_params();
                    $action = $p['action'] ?? '';
                    $rt = $wpdb->prefix . 'olo_redirects';
                    $lt = $wpdb->prefix . 'olo_404_log';
                    if ( $action === 'add' ) {
                        if ( empty( $p['from_url'] ) || empty( $p['to_url'] ) ) {
                            return new WP_Error( 'missing', 'from_url e to_url obbligatori', [ 'status' => 400 ] );
                        }
                        $wpdb->insert( $rt, [
                            'from_url'   => sanitize_text_field( $p['from_url'] ),
                            'to_url'     => esc_url_raw( $p['to_url'] ),
                            'type'       => (int) ( $p['type'] ?? 301 ),
                            'hits'       => 0,
                            'created_at' => current_time( 'mysql' ),
                        ] );
                        return rest_ensure_response( [ 'ok' => true, 'id' => $wpdb->insert_id ] );
                    }
                    if ( $action === 'delete' && ! empty( $p['id'] ) ) {
                        $wpdb->delete( $rt, [ 'id' => (int) $p['id'] ], [ '%d' ] );
                        return rest_ensure_response( [ 'ok' => true ] );
                    }
                    if ( $action === 'clear_404' ) {
                        $wpdb->query( "TRUNCATE TABLE $lt" );
                        return rest_ensure_response( [ 'ok' => true ] );
                    }
                    if ( $action === 'save_indexnow' ) {
                        $adv = (array) get_option( 'olo_seo_advanced', [] );
                        $adv['indexnow_key'] = sanitize_text_field( $p['indexnow_key'] ?? '' );
                        update_option( 'olo_seo_advanced', $adv );
                        update_option( 'olo_settings_last_saved', time() );
                        return rest_ensure_response( [ 'ok' => true ] );
                    }
                    return new WP_Error( 'invalid_action', 'Azione non riconosciuta', [ 'status' => 400 ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );
    }

    public function rest_get_api_keys() {
        $keys = [
            'olo_pexels_api_key', 'olo_pixabay_api_key', 'olo_unsplash_api_key',
            'olo_freesound_api_key', 'olo_recaptcha_site_key', 'olo_recaptcha_secret_key',
            'olo_mailchimp_api_key',
        ];
        $data = [];
        foreach ( $keys as $k ) {
            $data[ $k ] = get_option( $k, '' );
        }
        return rest_ensure_response( $data );
    }

    public function rest_put_api_keys( $request ) {
        $allowed = [
            'olo_pexels_api_key', 'olo_pixabay_api_key', 'olo_unsplash_api_key',
            'olo_freesound_api_key', 'olo_recaptcha_site_key', 'olo_recaptcha_secret_key',
            'olo_mailchimp_api_key',
        ];
        $body = $request->get_json_params();
        foreach ( $allowed as $k ) {
            if ( isset( $body[ $k ] ) ) {
                update_option( $k, sanitize_text_field( $body[ $k ] ) );
            }
        }
        return rest_ensure_response( [ 'success' => true ] );
    }

    public function rest_get_breakpoints() {
        $defaults_bps = [
            [ 'id' => 'desktop_xl', 'name' => 'Desktop XL', 'min' => '1440', 'max' => '∞',    'icon' => '🖥️', 'is_default' => false ],
            [ 'id' => 'desktop',    'name' => 'Desktop',    'min' => '1200', 'max' => '1439', 'icon' => '🖥️', 'is_default' => true  ],
            [ 'id' => 'laptop',     'name' => 'Laptop',     'min' => '992',  'max' => '1199', 'icon' => '💻', 'is_default' => false ],
            [ 'id' => 'tablet',     'name' => 'Tablet',     'min' => '768',  'max' => '991',  'icon' => '📱', 'is_default' => false ],
            [ 'id' => 'mobile_l',   'name' => 'Mobile L',   'min' => '576',  'max' => '767',  'icon' => '📱', 'is_default' => false ],
            [ 'id' => 'mobile',     'name' => 'Mobile',     'min' => '0',    'max' => '575',  'icon' => '📱', 'is_default' => false ],
        ];
        $defaults_adv = [ 'strategy' => 'mobile' ];

        $bps = get_option( 'olo_breakpoints_v2', $defaults_bps );
        $adv = get_option( 'olo_breakpoints_advanced', $defaults_adv );

        if ( ! is_array( $bps ) || empty( $bps ) ) $bps = $defaults_bps;
        if ( ! is_array( $adv ) ) $adv = $defaults_adv;

        return rest_ensure_response( [
            'breakpoints' => array_values( $bps ),
            'advanced'    => wp_parse_args( $adv, $defaults_adv ),
        ] );
    }

    public function rest_put_breakpoints( $request ) {
        $body = $request->get_json_params();

        if ( isset( $body['breakpoints'] ) && is_array( $body['breakpoints'] ) ) {
            $clean = [];
            foreach ( $body['breakpoints'] as $b ) {
                if ( ! is_array( $b ) ) continue;
                $clean[] = [
                    'id'         => sanitize_key( $b['id']   ?? 'bp_' . wp_generate_password( 6, false ) ),
                    'name'       => sanitize_text_field( $b['name'] ?? '' ),
                    'min'        => sanitize_text_field( (string) ( $b['min'] ?? '0' ) ),
                    'max'        => sanitize_text_field( (string) ( $b['max'] ?? '∞' ) ),
                    'icon'       => sanitize_text_field( $b['icon'] ?? '📱' ),
                    'is_default' => ! empty( $b['is_default'] ),
                ];
            }
            update_option( 'olo_breakpoints_v2', $clean );
        }

        if ( isset( $body['advanced'] ) && is_array( $body['advanced'] ) ) {
            $strategy = in_array( $body['advanced']['strategy'] ?? '', [ 'mobile', 'desktop' ], true ) ? $body['advanced']['strategy'] : 'mobile';
            update_option( 'olo_breakpoints_advanced', [ 'strategy' => $strategy ] );
        }

        update_option( 'olo_settings_last_saved', time() );
        return rest_ensure_response( [ 'success' => true ] );
    }

    private function register_core_tiles() {
        require_once OLO_PATH . 'includes/class-tile-utils.php';
        require_once OLO_PATH . 'includes/class-text-effects.php';
        require_once OLO_PATH . 'includes/tiles/class-tile-base.php';
        require_once OLO_PATH . 'includes/tiles/class-section-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-column-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-hero-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-hero-split-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-audiohero-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-section-header-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-info-cards-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-worklist-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-workgrid-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-statstrip-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-hoursstrip-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-hoverlist-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-lookbookmixer-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-categoryrail-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-beforeafter-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-tripfinder-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-maskedvideohero-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-searchhero-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-smearhero-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-photocover-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-masthead-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-matchfixtures-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-showcasegrid-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-productgrid-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-announcementbar-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-introsplit-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-mediacta-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-imagehero-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-glowhero-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-producthero-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-featuredstory-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-glowgallery-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-chathero-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-product-cards-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-step-timeline-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-process-steps-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-cta-banner-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-trust-strip-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-content-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-image-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-video-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-spacer-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-button-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-gallery-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-row-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-testimonial-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-pricing-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-counter-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-iconbox-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-alert-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-badge-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-team-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-accordion-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-icontabs-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-projector-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-finder-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-builder-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-mixer-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-schedule-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-hotspots-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-scaler-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-timezone-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-availability-tile.php';

        require_once OLO_PATH . 'includes/tiles/class-social-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-map-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-countdown-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-headline-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-html-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-list-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-text-block-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-slideshow-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-table-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-overlay-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-divider-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-progress-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-desclist-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-panel-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-quotation-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-code-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-icon-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-totop-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-fragment-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-grid-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-switcher-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-switcherpanel-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-nav-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-subnav-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-panelslider-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-overlayslider-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-overlaygrid-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-popover-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-breadcrumbs-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-search-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-sitelogo-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-navmenu-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-postgrid-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-proslider-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-popup-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-megamenu-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-inner-columns-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-timeline-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-flipcard-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-imgcompare-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-marquee-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-togglebtn-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-form-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-killnextprev-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-langswitcher-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-livesearch-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-shatteredimage-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-textmask-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-blendtext-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-progallery-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-pdfviewer-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-pdfpro-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-starrating-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-iconlist-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-animatedheading-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-toc-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-lottie-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-sharebuttons-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-scrollprogress-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-newsticker-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-hotspot-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-loginform-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-videoplaylist-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-textpath-tile.php';

        require_once OLO_PATH . 'includes/tiles/class-chart-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-audio-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-shapedivider-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-countercircle-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-postmeta-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-relatedposts-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-wpcomments-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-pagination-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-carousel-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-authorbox-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-soundcloud-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-tagcloud-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-viewscounter-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-menuanchor-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-osmmap-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-instagram-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-facebookpage-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-twitterfeed-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-postnavigation-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-pricelist-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-progresstracker-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-sitemap-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-linkinbio-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-shortcode-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-templateembed-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-paymentbuttons-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-pagetitlebar-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-portfolio-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-queryloop-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-readingtime-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-darkmode-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-lightbox-tile.php';

        // WooCommerce tiles (solo se WooCommerce attivo)
        if ( class_exists( 'WooCommerce' ) ) {
            require_once OLO_PATH . 'includes/tiles/class-woo-products-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-price-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-minicart-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-addtocart-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-categories-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-rating-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-product-tabs-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-related-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-upsells-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-cart-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-checkout-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-checkout-multistep-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-product-title-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-product-image-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-product-description-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-product-meta-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-product-stock-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-order-tracking-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-breadcrumbs-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-notices-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-product-navigation-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-sale-badge-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-cross-sells-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-recently-viewed-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-product-bundle-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-product-filter-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-product-gallery-slider-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-quickview-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-wishlist-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-comparison-tile.php';
            require_once OLO_PATH . 'includes/tiles/class-woo-myaccount-tile.php';
        }

        // ── Tile speciali (batch tile-speciali) — require ──
        require_once OLO_PATH . 'includes/tiles/class-stackscroll-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-crtoverlay-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-physicsbin-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-scratchfx-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-particlefx-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-asciiviz-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-variablespecimen-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-presencegrid-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-leaderboard-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-scrollscrub-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-goo-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-buildermock-tile.php';
        require_once OLO_PATH . 'includes/class-magnetic-cursor.php';
        Olo_Magnetic_Cursor::init();

        $manager = Olo_Tile_Manager::instance();
        $manager->register_tile( new Olo_Section_Tile() );
        // ── Tile speciali (batch tile-speciali) — register ──
        $manager->register_tile( new Olo_Stackscroll_Tile() );
        // Olo_Crtoverlay_Tile NON è un tile in flusso: è un EFFETTO DI PAGINA
        // (Impostazioni Pagina → Effetti di pagina), reso da render_tiles_array. La classe
        // resta require'd sopra come helper di render; nessun register_tile.
        $manager->register_tile( new Olo_Physicsbin_Tile() );
        $manager->register_tile( new Olo_Scratchfx_Tile() );
        $manager->register_tile( new Olo_Particlefx_Tile() );
        $manager->register_tile( new Olo_Asciiviz_Tile() );
        $manager->register_tile( new Olo_Variablespecimen_Tile() );
        $manager->register_tile( new Olo_Presencegrid_Tile() );
        $manager->register_tile( new Olo_Leaderboard_Tile() );
        $manager->register_tile( new Olo_Scrollscrub_Tile() );
        $manager->register_tile( new Olo_Goo_Tile() );
        $manager->register_tile( new Olo_BuilderMock_Tile() );
        $manager->register_tile( new Olo_Column_Tile() );
        $manager->register_tile( new Olo_Hero_Tile() );
        $manager->register_tile( new Olo_HeroSplit_Tile() );
        $manager->register_tile( new Olo_AudioHero_Tile() );
        $manager->register_tile( new Olo_SectionHeader_Tile() );
        $manager->register_tile( new Olo_InfoCards_Tile() );
        $manager->register_tile( new Olo_WorkList_Tile() );
        $manager->register_tile( new Olo_WorkGrid_Tile() );
        $manager->register_tile( new Olo_StatStrip_Tile() );
        $manager->register_tile( new Olo_HoursStrip_Tile() );
        $manager->register_tile( new Olo_HoverList_Tile() );
        $manager->register_tile( new Olo_LookbookMixer_Tile() );
        $manager->register_tile( new Olo_CategoryRail_Tile() );
        $manager->register_tile( new Olo_BeforeAfter_Tile() );
        $manager->register_tile( new Olo_TripFinder_Tile() );
        $manager->register_tile( new Olo_MaskedVideoHero_Tile() );
        $manager->register_tile( new Olo_SearchHero_Tile() );
        $manager->register_tile( new Olo_SmearHero_Tile() );
        $manager->register_tile( new Olo_PhotoCover_Tile() );
        $manager->register_tile( new Olo_Masthead_Tile() );
        $manager->register_tile( new Olo_MatchFixtures_Tile() );
        $manager->register_tile( new Olo_ShowcaseGrid_Tile() );
        $manager->register_tile( new Olo_ProductGrid_Tile() );
        $manager->register_tile( new Olo_AnnouncementBar_Tile() );
        $manager->register_tile( new Olo_IntroSplit_Tile() );
        $manager->register_tile( new Olo_MediaCTA_Tile() );
        $manager->register_tile( new Olo_ImageHero_Tile() );
        $manager->register_tile( new Olo_GlowHero_Tile() );
        $manager->register_tile( new Olo_ProductHero_Tile() );
        $manager->register_tile( new Olo_FeaturedStory_Tile() );
        $manager->register_tile( new Olo_GlowGallery_Tile() );
        $manager->register_tile( new Olo_ChatHero_Tile() );
        $manager->register_tile( new Olo_ProductCards_Tile() );
        $manager->register_tile( new Olo_StepTimeline_Tile() );
        $manager->register_tile( new Olo_Process_Steps_Tile() );
        $manager->register_tile( new Olo_CtaBanner_Tile() );
        $manager->register_tile( new Olo_TrustStrip_Tile() );
        $manager->register_tile( new Olo_Content_Tile() );
        $manager->register_tile( new Olo_Image_Tile() );
        $manager->register_tile( new Olo_Video_Tile() );
        $manager->register_tile( new Olo_Spacer_Tile() );
        $manager->register_tile( new Olo_Button_Tile() );
        $manager->register_tile( new Olo_Gallery_Tile() );
        $manager->register_tile( new Olo_Row_Tile() );
        $manager->register_tile( new Olo_Testimonial_Tile() );
        $manager->register_tile( new Olo_Pricing_Tile() );
        $manager->register_tile( new Olo_Counter_Tile() );
        $manager->register_tile( new Olo_IconBox_Tile() );
        $manager->register_tile( new Olo_Alert_Tile() );
        $manager->register_tile( new Olo_Badge_Tile() );
        $manager->register_tile( new Olo_Team_Tile() );
        $manager->register_tile( new Olo_Accordion_Tile() );
        $manager->register_tile( new Olo_IconTabs_Tile() );
        $manager->register_tile( new Olo_Projector_Tile() );
        $manager->register_tile( new Olo_Finder_Tile() );
        $manager->register_tile( new Olo_Builder_Tile() );
        $manager->register_tile( new Olo_Mixer_Tile() );
        $manager->register_tile( new Olo_Schedule_Tile() );
        $manager->register_tile( new Olo_Hotspots_Tile() );
        $manager->register_tile( new Olo_Scaler_Tile() );
        $manager->register_tile( new Olo_Timezone_Tile() );
        $manager->register_tile( new Olo_Availability_Tile() );

        $manager->register_tile( new Olo_Social_Tile() );
        $manager->register_tile( new Olo_Map_Tile() );
        $manager->register_tile( new Olo_Countdown_Tile() );
        $manager->register_tile( new Olo_Headline_Tile() );
        $manager->register_tile( new Olo_Html_Tile() );
        $manager->register_tile( new Olo_List_Tile() );
        $manager->register_tile( new Olo_TextBlock_Tile() );
        $manager->register_tile( new Olo_Slideshow_Tile() );
        $manager->register_tile( new Olo_Table_Tile() );
        $manager->register_tile( new Olo_Overlay_Tile() );
        $manager->register_tile( new Olo_Divider_Tile() );
        $manager->register_tile( new Olo_Progress_Tile() );
        $manager->register_tile( new Olo_DescList_Tile() );
        $manager->register_tile( new Olo_Panel_Tile() );
        $manager->register_tile( new Olo_Quotation_Tile() );
        $manager->register_tile( new Olo_Code_Tile() );
        $manager->register_tile( new Olo_Icon_Tile() );
        $manager->register_tile( new Olo_Totop_Tile() );
        $manager->register_tile( new Olo_Fragment_Tile() );
        $manager->register_tile( new Olo_Grid_Tile() );
        $manager->register_tile( new Olo_Switcher_Tile() );
        $manager->register_tile( new Olo_SwitcherPanel_Tile() );
        $manager->register_tile( new Olo_Nav_Tile() );
        $manager->register_tile( new Olo_Subnav_Tile() );
        $manager->register_tile( new Olo_PanelSlider_Tile() );
        $manager->register_tile( new Olo_OverlaySlider_Tile() );
        $manager->register_tile( new Olo_OverlayGrid_Tile() );
        $manager->register_tile( new Olo_Popover_Tile() );
        $manager->register_tile( new Olo_Breadcrumbs_Tile() );
        $manager->register_tile( new Olo_Search_Tile() );
        $manager->register_tile( new Olo_SiteLogo_Tile() );
        $manager->register_tile( new Olo_NavMenu_Tile() );
        $manager->register_tile( new Olo_PostGrid_Tile() );
        $manager->register_tile( new Olo_ProSlider_Tile() );
        $manager->register_tile( new Olo_Popup_Tile() );
        $manager->register_tile( new Olo_MegaMenu_Tile() );
        $manager->register_tile( new Olo_InnerColumns_Tile() );
        $manager->register_tile( new Olo_Timeline_Tile() );
        $manager->register_tile( new Olo_FlipCard_Tile() );
        $manager->register_tile( new Olo_ImgCompare_Tile() );
        $manager->register_tile( new Olo_Marquee_Tile() );
        $manager->register_tile( new Olo_ToggleBtn_Tile() );
        $manager->register_tile( new Olo_Form_Tile() );
        $manager->register_tile( new Olo_KillNextPrev_Tile() );
        $manager->register_tile( new Olo_LangSwitcher_Tile() );
        $manager->register_tile( new Olo_LiveSearch_Tile() );
        $manager->register_tile( new Olo_ShatteredImage_Tile() );
        $manager->register_tile( new Olo_Textmask_Tile() );
        $manager->register_tile( new Olo_Blendtext_Tile() );
        $manager->register_tile( new Olo_ProGallery_Tile() );
        $manager->register_tile( new Olo_PdfViewer_Tile() );
        $manager->register_tile( new Olo_PdfPro_Tile() );
        $manager->register_tile( new Olo_Starrating_Tile() );
        $manager->register_tile( new Olo_Iconlist_Tile() );
        $manager->register_tile( new Olo_Animatedheading_Tile() );
        $manager->register_tile( new Olo_Toc_Tile() );
        $manager->register_tile( new Olo_Lottie_Tile() );
        $manager->register_tile( new Olo_Sharebuttons_Tile() );
        $manager->register_tile( new Olo_Scrollprogress_Tile() );
        $manager->register_tile( new Olo_Newsticker_Tile() );
        $manager->register_tile( new Olo_Hotspot_Tile() );
        $manager->register_tile( new Olo_Loginform_Tile() );
        $manager->register_tile( new Olo_Videoplaylist_Tile() );
        $manager->register_tile( new Olo_Textpath_Tile() );

        $manager->register_tile( new Olo_Chart_Tile() );
        $manager->register_tile( new Olo_Audio_Tile() );
        $manager->register_tile( new Olo_Shapedivider_Tile() );
        $manager->register_tile( new Olo_Countercircle_Tile() );
        $manager->register_tile( new Olo_PostMeta_Tile() );
        $manager->register_tile( new Olo_RelatedPosts_Tile() );
        $manager->register_tile( new Olo_Wpcomments_Tile() );
        $manager->register_tile( new Olo_Pagination_Tile() );
        $manager->register_tile( new Olo_Carousel_Tile() );
        $manager->register_tile( new Olo_Authorbox_Tile() );
        $manager->register_tile( new Olo_Viewscounter_Tile() );
        $manager->register_tile( new Olo_Menuanchor_Tile() );
        $manager->register_tile( new Olo_Osmmap_Tile() );
        $manager->register_tile( new Olo_Soundcloud_Tile() );
        $manager->register_tile( new Olo_Tagcloud_Tile() );
        $manager->register_tile( new Olo_Instagram_Tile() );
        $manager->register_tile( new Olo_Facebookpage_Tile() );
        $manager->register_tile( new Olo_Twitterfeed_Tile() );
        $manager->register_tile( new Olo_Postnavigation_Tile() );
        $manager->register_tile( new Olo_Pricelist_Tile() );
        $manager->register_tile( new Olo_Progresstracker_Tile() );
        $manager->register_tile( new Olo_Sitemap_Tile() );
        $manager->register_tile( new Olo_LinkInBio_Tile() );
        $manager->register_tile( new Olo_Shortcode_Tile() );
        $manager->register_tile( new Olo_TemplateEmbed_Tile() );
        $manager->register_tile( new Olo_Paymentbuttons_Tile() );
        $manager->register_tile( new Olo_Pagetitlebar_Tile() );
        $manager->register_tile( new Olo_Portfolio_Tile() );
        $manager->register_tile( new Olo_Queryloop_Tile() );
        $manager->register_tile( new Olo_Readingtime_Tile() );
        $manager->register_tile( new Olo_Darkmode_Tile() );
        $manager->register_tile( new Olo_Lightbox_Tile() );

        // WooCommerce tiles (solo se WooCommerce attivo)
        if ( class_exists( 'WooCommerce' ) ) {
            $manager->register_tile( new Olo_Woo_Products_Tile() );
            $manager->register_tile( new Olo_Woo_Price_Tile() );
            $manager->register_tile( new Olo_Woo_Minicart_Tile() );
            $manager->register_tile( new Olo_Woo_Addtocart_Tile() );
            $manager->register_tile( new Olo_Woo_Categories_Tile() );
            $manager->register_tile( new Olo_Woo_Rating_Tile() );
            $manager->register_tile( new Olo_Woo_Product_Tabs_Tile() );
            $manager->register_tile( new Olo_Woo_Related_Tile() );
            $manager->register_tile( new Olo_Woo_Upsells_Tile() );
            $manager->register_tile( new Olo_Woo_Cart_Tile() );
            $manager->register_tile( new Olo_Woo_Checkout_Tile() );
            $manager->register_tile( new Olo_Woo_Checkout_Multistep_Tile() );
            $manager->register_tile( new Olo_Woo_Product_Title_Tile() );
            $manager->register_tile( new Olo_Woo_Product_Image_Tile() );
            $manager->register_tile( new Olo_Woo_Product_Description_Tile() );
            $manager->register_tile( new Olo_Woo_Product_Meta_Tile() );
            $manager->register_tile( new Olo_Woo_Product_Stock_Tile() );
            $manager->register_tile( new Olo_Woo_Order_Tracking_Tile() );
            $manager->register_tile( new Olo_Woo_Breadcrumbs_Tile() );
            $manager->register_tile( new Olo_Woo_Notices_Tile() );
            $manager->register_tile( new Olo_Woo_Product_Navigation_Tile() );
            $manager->register_tile( new Olo_Woo_Sale_Badge_Tile() );
            $manager->register_tile( new Olo_Woo_Cross_Sells_Tile() );
            $manager->register_tile( new Olo_Woo_Recently_Viewed_Tile() );
            $manager->register_tile( new Olo_Woo_Product_Bundle_Tile() );
            $manager->register_tile( new Olo_Woo_Product_Filter_Tile() );
            $manager->register_tile( new Olo_Woo_Product_Gallery_Slider_Tile() );
            $manager->register_tile( new Olo_Woo_Quickview_Tile() );
            $manager->register_tile( new Olo_Woo_Wishlist_Tile() );
            $manager->register_tile( new Olo_Woo_Comparison_Tile() );
            $manager->register_tile( new Olo_Woo_Myaccount_Tile() );
        }

        // Hook per plugin esterni che vogliono registrare tile
        do_action( 'olo_register_external_tiles', $manager );
    }

    /**
     * Get active single templates map: { post_type => template_id }.
     */
    private function get_active_singles_map() {
        $post_types = get_post_types( [ 'public' => true ], 'names' );
        $result     = [];
        foreach ( $post_types as $pt ) {
            if ( in_array( $pt, [ 'page', 'attachment' ], true ) ) continue;
            $tpl_id = (int) get_option( "olo_active_single_{$pt}", 0 );
            if ( $tpl_id ) {
                $result[ $pt ] = $tpl_id;
            }
        }
        // Ensure JS gets an object even if empty
        return ! empty( $result ) ? $result : new stdClass;
    }

    /**
     * Get public post types (excluding page and attachment) for the builder UI.
     */
    private function get_public_taxonomies() {
        $taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );
        $result     = [ [ 'value' => '', 'label' => 'Nessuna' ] ];
        foreach ( $taxonomies as $tax ) {
            $result[] = [
                'value' => $tax->name,
                'label' => $tax->label,
            ];
        }
        return $result;
    }

    private function get_public_post_types() {
        $post_types = get_post_types( [ 'public' => true ], 'objects' );
        $result     = [];
        foreach ( $post_types as $pt ) {
            if ( in_array( $pt->name, [ 'page', 'attachment' ], true ) ) continue;
            $result[] = [
                'value' => $pt->name,
                'label' => $pt->label,
            ];
        }
        return $result;
    }

    /**
     * Get all published Olobuild templates for the template selector (e.g. Popup element).
     */
    private function get_template_list() {
        $db     = new Olo_Database();
        $result = $db->list_templates( [ 'status' => 'published', 'per_page' => 100 ] );
        $list   = [ [ 'value' => 0, 'label' => '— Seleziona template —' ] ];
        foreach ( $result['items'] as $t ) {
            $list[] = [ 'value' => (int) $t['id'], 'label' => $t['title'] ];
        }
        return $list;
    }

    /**
     * Get published templates of type "megapanel" for the mega menu panel selector.
     */
    private function get_megapanel_templates() {
        $db     = new Olo_Database();
        $result = $db->list_templates( [ 'status' => 'published', 'type' => 'megapanel', 'per_page' => 100 ] );
        $list   = [ [ 'value' => 0, 'label' => 'Auto (colonne)' ] ];
        foreach ( $result['items'] as $t ) {
            $list[] = [ 'value' => (int) $t['id'], 'label' => $t['title'] ];
        }
        return $list;
    }

    /**
     * Get published templates of type "widget" — riusabili come contenuto
     * delle schede/items dei tile container (accordion, tab, slider, ecc.).
     */
    private function get_widget_templates() {
        $db     = new Olo_Database();
        $result = $db->list_templates( [ 'status' => 'published', 'type' => 'widget', 'per_page' => 100 ] );
        $list   = [ [ 'value' => 0, 'label' => '— Nessun widget —' ] ];
        foreach ( $result['items'] as $t ) {
            $list[] = [ 'value' => (int) $t['id'], 'label' => $t['title'] ];
        }
        return $list;
    }

    /**
     * Get meta prefix options for booking tiles.
     * Only includes CPTs that have an active single template.
     */
    /**
     * Mappa dei meta_key + valori distinti per ciascun public post_type, usata
     * dall'inspector per offrire menu a discesa al posto del campo testo
     * "chiave=valore". Cache transient 5 min.
     *
     * Output: [
     *   'olo_service' => [ [ 'key' => '_olo_service_type', 'label' => '_olo_service_type', 'values' => [ 'accommodation', 'restaurant', ...] ], ... ],
     *   'post'        => [ ... ],
     * ]
     */
    private function get_meta_keys_map() {
        $cache_key = 'olo_meta_keys_map_v1';
        $cached    = get_transient( $cache_key );
        if ( is_array( $cached ) ) return $cached;

        global $wpdb;
        $post_types = get_post_types( [ 'public' => true ], 'names' );
        $map = [];
        foreach ( $post_types as $type ) {
            // Top 50 meta keys per post_type (esclude _edit_*, _wp_*, _oembed_* interne)
            $keys = $wpdb->get_col( $wpdb->prepare(
                "SELECT DISTINCT pm.meta_key
                 FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
                 WHERE p.post_type = %s AND p.post_status IN ('publish','draft','private','future')
                   AND pm.meta_key != '' AND pm.meta_key NOT LIKE %s
                   AND pm.meta_key NOT LIKE %s AND pm.meta_key NOT LIKE %s
                 ORDER BY pm.meta_key ASC LIMIT 50",
                $type, '_edit_%', '_wp_%', '_oembed_%'
            ) );
            if ( empty( $keys ) ) continue;
            $entries = [];
            foreach ( $keys as $k ) {
                // Top 50 valori distinti scartando vuoti/JSON/serializzati lunghi
                $values = $wpdb->get_col( $wpdb->prepare(
                    "SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm
                     INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
                     WHERE p.post_type = %s AND pm.meta_key = %s
                       AND pm.meta_value != ''
                       AND CHAR_LENGTH(pm.meta_value) < 80
                       AND pm.meta_value NOT LIKE %s AND pm.meta_value NOT LIKE %s
                     ORDER BY pm.meta_value ASC LIMIT 50",
                    $type, $k, 'a:%', 'O:%'
                ) );
                $values = array_values( array_filter( $values, function ( $v ) {
                    return $v !== '' && ! is_serialized( $v );
                } ) );
                $entries[] = [
                    'key'    => $k,
                    'label'  => $k,
                    'values' => $values,
                ];
            }
            $map[ $type ] = $entries;
        }
        set_transient( $cache_key, $map, 5 * MINUTE_IN_SECONDS );
        return $map;
    }

    private function get_meta_prefixes() {
        $post_types = get_post_types( [ 'public' => true, '_builtin' => false ], 'objects' );
        $result     = [];

        // Friendly names for known CPTs
        $friendly = [
            'olo_service' => 'Baita / Servizio',
            'location'    => 'Location',
            'olo_event'   => 'Evento',
        ];

        foreach ( $post_types as $pt ) {
            $tpl_id = (int) get_option( "olo_active_single_{$pt->name}", 0 );
            if ( ! $tpl_id ) {
                continue;
            }

            $label = $friendly[ $pt->name ] ?? ( $pt->labels->singular_name ?: $pt->label );
            $count = wp_count_posts( $pt->name );
            $n     = isset( $count->publish ) ? (int) $count->publish : 0;
            if ( $n > 0 ) {
                $label .= " ({$n})";
            }

            $result[] = [
                'value' => "_{$pt->name}_",
                'label' => $label,
            ];
        }

        // Allow themes/plugins to customize labels
        $result = apply_filters( 'olo_meta_prefix_options', $result );

        // Fallback: always include olo_service
        $has_service = false;
        foreach ( $result as $r ) {
            if ( $r['value'] === '_olo_service_' ) {
                $has_service = true;
                break;
            }
        }
        if ( ! $has_service ) {
            $result[] = [ 'value' => '_olo_service_', 'label' => 'Servizio' ];
        }

        return $result;
    }

    /**
     * Get all published olo_service posts for the booking picker tile.
     */
    private function get_service_list() {
        if ( ! post_type_exists( 'olo_service' ) ) {
            return [];
        }
        $posts  = get_posts( [
            'post_type'      => 'olo_service',
            'post_status'    => 'publish',
            'posts_per_page' => 100,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ] );
        $result = [];
        foreach ( $posts as $p ) {
            $result[] = [
                'value' => (string) $p->ID,
                'label' => $p->post_title,
            ];
        }
        return $result;
    }

    /**
     * Get posts that use the current template (for per-post conditional visibility).
     * Looks up which post type this template is assigned to, then returns all published posts of that type.
     */
    private function get_single_post_items() {
        $tpl_id = absint( $_GET['template_id'] ?? 0 );
        if ( ! $tpl_id ) return [];

        // Find which post type uses this template
        $post_types = get_post_types( [ 'public' => true ], 'names' );
        $target_pt  = '';
        foreach ( $post_types as $pt ) {
            $opt_val = get_option( "olo_active_single_{$pt}", 0 );
            if ( (int) $opt_val === $tpl_id ) {
                $target_pt = $pt;
                break;
            }
        }
        // Fallback: also check the template's own post meta for assigned post type
        if ( ! $target_pt ) {
            $meta_pt = get_post_meta( $tpl_id, '_olo_single_post_type', true );
            if ( $meta_pt && post_type_exists( $meta_pt ) ) {
                $target_pt = $meta_pt;
            }
        }
        if ( ! $target_pt ) return [];

        $posts  = get_posts( [
            'post_type'      => $target_pt,
            'post_status'    => 'publish',
            'posts_per_page' => 200,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ] );
        $result = [];
        foreach ( $posts as $p ) {
            $result[] = [
                'value' => (string) $p->ID,
                'label' => $p->post_title,
            ];
        }
        return $result;
    }

    /**
     * Get published WP pages for select dropdowns in the builder.
     */
    private function get_wp_pages() {
        $pages  = get_pages( [ 'post_status' => 'publish', 'sort_column' => 'post_title' ] );
        $result = [ [ 'value' => '', 'label' => '— Seleziona pagina —' ] ];
        foreach ( $pages as $p ) {
            $result[] = [
                'value' => str_replace( home_url(), '', get_permalink( $p->ID ) ),
                'label' => $p->post_title,
            ];
        }
        return $result;
    }

    /**
     * Get all registered WP nav menus for the builder UI.
     */
    private function get_wp_menus() {
        $menus  = wp_get_nav_menus();
        $result = [];
        foreach ( $menus as $menu ) {
            $menu_data = [
                'id'    => $menu->term_id,
                'name'  => $menu->name,
                'items' => [],
            ];
            $nav_items = wp_get_nav_menu_items( $menu->term_id );
            if ( $nav_items && is_array( $nav_items ) ) {
                foreach ( $nav_items as $item ) {
                    $menu_data['items'][] = [
                        'id'     => $item->ID,
                        'title'  => $item->title,
                        'parent' => (int) $item->menu_item_parent,
                    ];
                }
            }
            $result[] = $menu_data;
        }
        return $result;
    }

    /**
     * Get the site logo URL (from Customizer custom_logo).
     */
    private function get_site_logo_url() {
        $custom_logo_id = get_theme_mod( 'custom_logo' );
        if ( $custom_logo_id ) {
            $url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
            if ( $url ) {
                return $url;
            }
        }
        return '';
    }

}
