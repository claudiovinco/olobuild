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
            wp_die( 'Unauthorized', 403 );
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

        // Disabilita admin bar e altri overlay
        show_admin_bar( false );
    }

    /** @internal Filter callback: rimpiazza the_content con placeholder. */
    public function inline_preview_replace_content( $content ) {
        return '<div id="olo-iframe-root"><div class="olo-iframe-empty">Caricamento preview...</div></div>';
    }

    /** @internal Filter callback: rimpiazza core/post-content block (block themes). */
    public function inline_preview_replace_post_content_block( $html, $block ) {
        if ( ( $block['blockName'] ?? '' ) === 'core/post-content' ) {
            return '<div id="olo-iframe-root"><div class="olo-iframe-empty">Caricamento preview...</div></div>';
        }
        return $html;
    }

    /** @internal Enqueue degli stessi asset di templates/builder-iframe.php. */
    public function enqueue_inline_preview_assets() {
        // Core CSS (mirror del builder-iframe.php)
        wp_enqueue_style( 'olo-uikit-inline', OLO_URL . 'assets/vendor/uikit/css/uikit.min.css', [], OLO_VERSION );
        wp_enqueue_style( 'olo-frontend-inline', OLO_URL . 'assets/css/frontend.css', [], OLO_VERSION );
        wp_enqueue_style( 'olo-iframe-builder-inline', OLO_URL . 'assets/css/iframe-builder.css', [], OLO_VERSION );
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
            wp_localize_script( 'olo-admin-settings-js', 'oloData', [
                'restUrl'          => esc_url_raw( rest_url( 'olo/v1' ) ),
                'nonce'            => wp_create_nonce( 'wp_rest' ),
                'pluginUrl'        => OLO_URL,
                'version'          => OLO_VERSION,
                'locale'           => olo_current_locale(),
                'translations'     => olo_get_translations_map(),
                'styles'           => $style_system->get_styles(),
                'presets'          => $style_system->get_presets(),
                'globalColors'     => $style_system->get_global_colors(),
                'globalTypography' => $style_system->get_global_typography(),
            ] );
            return;
        }

        if ( 'olobuild_page_olobuilder-templates' !== $hook ) {
            return;
        }

        // Load full WP media framework (needed for wp.media settings & templates)
        wp_enqueue_media();

        // Cache-busting basato sul mtime reale dei file (oltre OLO_VERSION),
        // così ogni rebuild forza il reload del bundle anche se la versione
        // del plugin non è stata bumpata (utile in dev/staging).
        $css_path = OLO_PATH . 'assets/css/builder.css';
        $js_path  = OLO_PATH . 'assets/js/builder.js';
        $css_ver  = OLO_VERSION . '.' . ( file_exists( $css_path ) ? filemtime( $css_path ) : 0 );
        $js_ver   = OLO_VERSION . '.' . ( file_exists( $js_path )  ? filemtime( $js_path )  : 0 );

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
            'templateList'       => $this->get_template_list(),
            'megapanelTemplates' => $this->get_megapanel_templates(),
            'postTypes'      => $this->get_public_post_types(),
            'taxonomies'     => $this->get_public_taxonomies(),
            'metaPrefixes'   => $this->get_meta_prefixes(),
            'metaKeys'       => $this->get_meta_keys_map(),
            'serviceList'    => $this->get_service_list(),
            'wpPages'        => $this->get_wp_pages(),
            'singlePostItems' => $this->get_single_post_items(),
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

        if ( $template_id > 0 ) {
            // Fullscreen editor mode — keep existing behaviour
            include OLO_PATH . 'templates/builder-page.php';
        } else {
            // Template list mode — use the shared admin shell
            self::page_shell_open( __( 'Gestione Template', 'olobuild' ) );
            echo '<div id="olobuilder-app"></div>';
            self::page_shell_close();
        }
    }

    /**
     * Add body class on Olobuild admin pages for shell layout.
     */
    public function admin_body_class( $classes ) {
        $screen = get_current_screen();
        if ( $screen && ( str_contains( $screen->id, 'olobuild' ) || str_contains( $screen->id, 'olo-' ) ) ) {
            $classes .= ' olo-admin-shell';
        }
        return $classes;
    }

    /**
     * Open the shared admin page shell: top bar + sidebar + content area.
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
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=olobuilder' ) ); ?>">
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
            [ 'slug' => 'olobuild',           'label' => 'Avvio Rapido',  'icon' => 'rocket' ],
            [ 'slug' => 'olobuilder-settings',   'label' => 'Impostazioni',  'icon' => 'gear' ],
            [ 'slug' => 'olo-tools',             'label' => 'Strumenti',     'icon' => 'wrench' ],
            [ 'slug' => 'olo-role-manager',      'label' => 'Role Manager',  'icon' => 'users' ],
            [ 'slug' => 'olo-form-submissions',  'label' => 'Submissions',   'icon' => 'email' ],
            [
                'group' => 'Template',
                'icon'  => 'layout',
                'items' => [
                    [ 'slug' => 'olobuilder-templates', 'label' => 'Template Salvati' ],
                    [ 'slug' => 'olo-import-export',    'label' => 'Template Website' ],
                    [ 'slug' => 'olo-global-popups',    'label' => 'Popups' ],
                ],
            ],
            [
                'group' => 'Marketing',
                'icon'  => 'chart',
                'items' => [
                    [ 'slug' => 'olo-analytics',       'label' => 'Analytics' ],
                    [ 'slug' => 'olo-cookie-consent',   'label' => 'Cookie Consent' ],
                    [ 'slug' => 'olo-seo',              'label' => 'SEO' ],
                    [ 'slug' => 'olo-redirects',         'label' => 'Redirect & 404' ],
                ],
            ],
            [
                'group' => 'Personalizzazione',
                'icon'  => 'palette',
                'items' => [
                    [ 'slug' => 'olo-media-search',    'label' => 'Ricerca Media' ],
                    [ 'slug' => 'olo-performance',     'label' => 'Performance' ],
                    [ 'slug' => 'olo-woo-templates',   'label' => 'WooCommerce' ],
                ],
            ],
            [
                'group' => 'Sistema',
                'icon'  => 'settings',
                'items' => [
                    [ 'slug' => 'olo-white-label',     'label' => 'White Label' ],
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
                                <?php foreach ( $item['items'] as $sub ) : ?>
                                    <li>
                                        <a href="<?php echo esc_url( $base . $sub['slug'] ); ?>"
                                           class="<?php echo $current_page === $sub['slug'] ? 'active' : ''; ?>">
                                            <?php echo esc_html( $sub['label'] ); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php else : ?>
                        <li>
                            <a href="<?php echo esc_url( $base . $item['slug'] ); ?>"
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

    public function render_dashboard_page() {
        // Collect all submenu items for the dashboard
        $items = [
            [
                'title' => 'Gestione Template',
                'desc'  => 'Crea e modifica i tuoi template',
                'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>',
                'url'   => admin_url( 'admin.php?page=olobuilder-templates' ),
                'color' => '#e8622a',
            ],
            [
                'title' => 'Configurazione',
                'desc'  => 'Stili, colori, tipografia e API',
                'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>',
                'url'   => admin_url( 'admin.php?page=olobuilder-settings' ),
                'color' => '#1a1a1a',
            ],
            [
                'title' => 'Ricerca Media',
                'desc'  => 'Cerca foto, video e audio stock',
                'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>',
                'url'   => admin_url( 'admin.php?page=olo-media-search' ),
                'color' => '#8b5cf6',
            ],
            [
                'title' => 'Invii Form',
                'desc'  => 'Visualizza i messaggi ricevuti',
                'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>',
                'url'   => admin_url( 'admin.php?page=olo-form-submissions' ),
                'color' => '#10b981',
            ],
            [
                'title' => 'Analytics',
                'desc'  => 'Tracking e statistiche',
                'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>',
                'url'   => admin_url( 'admin.php?page=olo-analytics' ),
                'color' => '#3b82f6',
            ],
            [
                'title' => 'Cookie Consent',
                'desc'  => 'Banner GDPR e consenso cookie',
                'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><circle cx="8" cy="9" r="1" fill="currentColor"/><circle cx="15" cy="13" r="1" fill="currentColor"/><circle cx="10" cy="15" r="1" fill="currentColor"/></svg>',
                'url'   => admin_url( 'admin.php?page=olo-cookie-consent' ),
                'color' => '#f59e0b',
            ],
            [
                'title' => 'SEO',
                'desc'  => 'Meta tag, Open Graph e sitemap',
                'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>',
                'url'   => admin_url( 'admin.php?page=olo-seo' ),
                'color' => '#06b6d4',
            ],
            [
                'title' => 'Redirect & 404',
                'desc'  => 'Gestisci redirect e pagine 404',
                'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 18l6-6-6-6"/></svg>',
                'url'   => admin_url( 'admin.php?page=olo-redirects' ),
                'color' => '#ef4444',
            ],
            [
                'title' => 'Performance',
                'desc'  => 'Cache, lazy load e ottimizzazione',
                'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>',
                'url'   => admin_url( 'admin.php?page=olo-performance' ),
                'color' => '#f97316',
            ],
            [
                'title' => 'Strumenti',
                'desc'  => 'Cache, manutenzione, URL, versioni',
                'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>',
                'url'   => admin_url( 'admin.php?page=olo-tools' ),
                'color' => '#64748b',
            ],
            [
                'title' => 'WooCommerce',
                'desc'  => 'Template per prodotti e shop',
                'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>',
                'url'   => admin_url( 'admin.php?page=olo-woo-templates' ),
                'color' => '#7c3aed',
            ],
        ];
        ?>
        <?php self::page_shell_open(); ?>
                <p class="olo-dash-tagline">Più di un page builder.</p>
                <div class="olo-dash-grid">
                    <?php foreach ( $items as $item ) : ?>
                    <a href="<?php echo esc_url( $item['url'] ); ?>" class="olo-dash-card">
                        <div class="olo-dash-card-icon" style="background: <?php echo esc_attr( $item['color'] ); ?>">
                            <?php echo $item['icon']; ?>
                        </div>
                        <div class="olo-dash-card-body">
                            <h3><?php echo esc_html( $item['title'] ); ?></h3>
                            <p><?php echo esc_html( $item['desc'] ); ?></p>
                        </div>
                        <svg class="olo-dash-card-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                    </a>
                    <?php endforeach; ?>
                </div>
        <?php self::page_shell_close(); ?>
        <style>
            .olo-dash-tagline { font-size: 15px; color: #999; margin: 0 0 28px; font-weight: 400; text-align: center; }
            .olo-dash-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .olo-dash-card {
                display: flex; align-items: center; gap: 16px; padding: 20px 24px;
                background: #fff; border: 1.5px solid #f0f0f0; border-radius: 14px;
                text-decoration: none; color: inherit; transition: all 0.2s;
            }
            .olo-dash-card:hover { border-color: #ddd; box-shadow: 0 4px 20px rgba(0,0,0,0.06); transform: translateY(-1px); }
            .olo-dash-card:focus { outline: none; border-color: #e8622a; }
            .olo-dash-card-icon {
                width: 48px; height: 48px; border-radius: 12px; flex-shrink: 0;
                display: flex; align-items: center; justify-content: center; color: #fff;
            }
            .olo-dash-card-body { flex: 1; min-width: 0; }
            .olo-dash-card-body h3 { margin: 0 0 2px; font-size: 14px; font-weight: 600; color: #1a1a1a; }
            .olo-dash-card-body p { margin: 0; font-size: 12px; color: #999; }
            .olo-dash-card-arrow { color: #ccc; flex-shrink: 0; transition: color 0.15s, transform 0.15s; }
            .olo-dash-card:hover .olo-dash-card-arrow { color: #e8622a; transform: translateX(2px); }
            @media (max-width: 680px) { .olo-dash-grid { grid-template-columns: 1fr; } }
        </style>
        <?php
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
                $val = get_option( 'olo_pexels_api_key', '***REMOVED-API-KEY***' );
                echo '<input type="text" name="olo_pexels_api_key" value="' . esc_attr( $val ) . '" class="regular-text" />';
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
                $val = get_option( 'olo_pixabay_api_key', '***REMOVED-API-KEY***' );
                echo '<input type="text" name="olo_pixabay_api_key" value="' . esc_attr( $val ) . '" class="regular-text" />';
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
                $val = get_option( 'olo_unsplash_api_key', 'mAtcGSa97BuefUN55vaORLV6YvFH4SHjdcCFbq_gJ84' );
                echo '<input type="text" name="olo_unsplash_api_key" value="' . esc_attr( $val ) . '" class="regular-text" />';
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
        ?>
        <?php self::page_shell_open( 'Configurazione' ); ?>
                <div id="olo-admin-settings"></div>
        <?php self::page_shell_close(); ?>
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
        $bp = get_option( 'olo_default_breakpoints', [] );
        $enabled = get_option( 'olo_breakpoints_enabled', [] );
        $defaults = [
            'widescreen'       => 1400,
            'tablet_landscape' => 1200,
            'tablet'           => 960,
            'mobile_landscape' => 640,
            'mobile'           => 480,
        ];
        $enabled_defaults = [
            'widescreen'       => true,
            'tablet_landscape' => false,
            'tablet'           => true,
            'mobile_landscape' => false,
            'mobile'           => true,
        ];
        return rest_ensure_response( [
            'values'  => wp_parse_args( $bp, $defaults ),
            'enabled' => wp_parse_args( $enabled, $enabled_defaults ),
        ] );
    }

    public function rest_put_breakpoints( $request ) {
        $body    = $request->get_json_params();
        $allowed = [ 'widescreen', 'tablet_landscape', 'tablet', 'mobile_landscape', 'mobile' ];

        // Save pixel values
        if ( isset( $body['values'] ) && is_array( $body['values'] ) ) {
            $bp = [];
            foreach ( $allowed as $k ) {
                if ( isset( $body['values'][ $k ] ) ) {
                    $bp[ $k ] = absint( $body['values'][ $k ] );
                }
            }
            update_option( 'olo_default_breakpoints', $bp );
        }

        // Save enabled states
        if ( isset( $body['enabled'] ) && is_array( $body['enabled'] ) ) {
            $en = [];
            foreach ( $allowed as $k ) {
                $en[ $k ] = ! empty( $body['enabled'][ $k ] );
            }
            update_option( 'olo_breakpoints_enabled', $en );
        }

        return rest_ensure_response( [ 'success' => true ] );
    }

    private function register_core_tiles() {
        require_once OLO_PATH . 'includes/class-tile-utils.php';
        require_once OLO_PATH . 'includes/class-text-effects.php';
        require_once OLO_PATH . 'includes/tiles/class-tile-base.php';
        require_once OLO_PATH . 'includes/tiles/class-section-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-column-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-hero-tile.php';
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
        require_once OLO_PATH . 'includes/tiles/class-team-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-accordion-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-icontabs-tile.php';

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

        $manager = Olo_Tile_Manager::instance();
        $manager->register_tile( new Olo_Section_Tile() );
        $manager->register_tile( new Olo_Column_Tile() );
        $manager->register_tile( new Olo_Hero_Tile() );
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
        $manager->register_tile( new Olo_Team_Tile() );
        $manager->register_tile( new Olo_Accordion_Tile() );
        $manager->register_tile( new Olo_IconTabs_Tile() );

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
