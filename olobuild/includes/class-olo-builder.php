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

        // Enqueue scripts only on builder page
        add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );

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

    public function admin_menu() {
        add_menu_page(
            __( 'Olobuild', 'olobuilder' ),
            __( 'Olobuild', 'olobuilder' ),
            'edit_posts',
            'olobuilder',
            [ $this, 'render_builder_page' ],
            OLO_URL . 'assets/img/ob-menu.png',
            30
        );

        add_submenu_page(
            'olobuilder',
            __( 'Impostazioni', 'olobuilder' ),
            __( 'Impostazioni', 'olobuilder' ),
            'manage_options',
            'olobuilder-settings',
            [ $this, 'render_settings_page' ]
        );

        add_submenu_page(
            'olobuilder',
            __( 'Ricerca Media', 'olobuilder' ),
            __( 'Ricerca Media', 'olobuilder' ),
            'upload_files',
            'olo-media-search',
            [ 'Olo_Media_Search', 'render_page' ]
        );

        add_submenu_page(
            'olobuilder',
            __( 'Invii Form', 'olobuilder' ),
            __( 'Invii Form', 'olobuilder' ),
            'manage_options',
            'olo-form-submissions',
            [ 'Olo_Form_Submissions', 'render_page' ]
        );
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
        if ( 'toplevel_page_olobuilder' !== $hook ) {
            return;
        }

        // Load full WP media framework (needed for wp.media settings & templates)
        wp_enqueue_media();

        wp_enqueue_style(
            'olobuilder-css',
            OLO_URL . 'assets/css/builder.css',
            [],
            OLO_VERSION
        );

        wp_enqueue_script(
            'olobuilder-js',
            OLO_URL . 'assets/js/builder.js',
            [ 'media-views' ],
            OLO_VERSION . '.' . time(),
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
            'serviceList'    => $this->get_service_list(),
            'wpPages'        => $this->get_wp_pages(),
            'singlePostItems' => $this->get_single_post_items(),
            '_debug_tpl_id'   => absint( $_GET['template_id'] ?? 0 ),
            'hasAiKey'       => ! empty( get_option( 'olo_ai_anthropic_key', '' ) ),
            'userRestrictions' => Olobuild_Role_Manager::instance()->get_current_user_restrictions(),
            'isContentOnly'    => Olobuild_Role_Manager::instance()->is_content_only(),
            'isDesignOnly'     => Olobuild_Role_Manager::instance()->is_design_only(),
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
        include OLO_PATH . 'templates/builder-page.php';
    }

    /**
     * Registra le impostazioni API keys nella WP Settings API.
     */
    public function register_settings() {
        // Sezione API Keys
        add_settings_section(
            'olo_api_keys_section',
            __( 'API Keys — Stock Media', 'olobuilder' ),
            function () {
                echo '<p>' . esc_html__( 'Inserisci le chiavi API per i servizi di immagini/video stock. Se lasci un campo vuoto, verrà usata la chiave predefinita.', 'olobuilder' ) . '</p>';
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
            __( 'Pexels API Key', 'olobuilder' ),
            function () {
                $val = get_option( 'olo_pexels_api_key', '***REMOVED-API-KEY***' );
                echo '<input type="text" name="olo_pexels_api_key" value="' . esc_attr( $val ) . '" class="regular-text" />';
                echo '<p class="description">' . sprintf(
                    /* translators: %s = URL */
                    __( 'Ottieni una chiave su %s', 'olobuilder' ),
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
            __( 'Pixabay API Key', 'olobuilder' ),
            function () {
                $val = get_option( 'olo_pixabay_api_key', '***REMOVED-API-KEY***' );
                echo '<input type="text" name="olo_pixabay_api_key" value="' . esc_attr( $val ) . '" class="regular-text" />';
                echo '<p class="description">' . sprintf(
                    __( 'Ottieni una chiave su %s', 'olobuilder' ),
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
            __( 'Unsplash Access Key', 'olobuilder' ),
            function () {
                $val = get_option( 'olo_unsplash_api_key', 'mAtcGSa97BuefUN55vaORLV6YvFH4SHjdcCFbq_gJ84' );
                echo '<input type="text" name="olo_unsplash_api_key" value="' . esc_attr( $val ) . '" class="regular-text" />';
                echo '<p class="description">' . sprintf(
                    __( 'Ottieni una chiave su %s', 'olobuilder' ),
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
            __( 'Freesound API Key', 'olobuilder' ),
            function () {
                $val = get_option( 'olo_freesound_api_key', '' );
                echo '<input type="text" name="olo_freesound_api_key" value="' . esc_attr( $val ) . '" class="regular-text" placeholder="xbKm7Gp3..." />';
                echo '<p class="description">' . sprintf(
                    __( 'Ottieni una chiave su %s', 'olobuilder' ),
                    '<a href="https://freesound.org/apiv2/apply" target="_blank" rel="noopener">freesound.org/apiv2/apply</a>'
                ) . '</p>';
            },
            'olobuilder-settings',
            'olo_api_keys_section'
        );

        // ── Sezione reCAPTCHA v3 ──
        add_settings_section(
            'olo_recaptcha_section',
            __( 'reCAPTCHA v3', 'olobuilder' ),
            function () {
                echo '<p>' . esc_html__( 'Google reCAPTCHA v3 per la protezione dei form. Ottieni le chiavi su google.com/recaptcha', 'olobuilder' ) . '</p>';
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
            __( 'Site Key', 'olobuilder' ),
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
            __( 'Secret Key', 'olobuilder' ),
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
            __( 'Mailchimp', 'olobuilder' ),
            function () {
                echo '<p>' . esc_html__( 'API key Mailchimp per le integrazioni dei form contatti.', 'olobuilder' ) . '</p>';
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
            __( 'Mailchimp API Key', 'olobuilder' ),
            function () {
                $val = get_option( 'olo_mailchimp_api_key', '' );
                echo '<input type="text" name="olo_mailchimp_api_key" value="' . esc_attr( $val ) . '" class="regular-text" placeholder="xxxxxxxxxxxxxxxx-us1" />';
                echo '<p class="description">' . sprintf(
                    __( 'Ottieni una chiave su %s', 'olobuilder' ),
                    '<a href="https://mailchimp.com/help/about-api-keys/" target="_blank" rel="noopener">mailchimp.com</a>'
                ) . '</p>';
            },
            'olobuilder-settings',
            'olo_mailchimp_section'
        );
    }

    /**
     * Renderizza la pagina Impostazioni Olobuild.
     */
    public function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( 'olo_settings_group' );
                do_settings_sections( 'olobuilder-settings' );
                submit_button( __( 'Salva impostazioni', 'olobuilder' ) );
                ?>
            </form>
        </div>
        <?php
    }

    private function register_core_tiles() {
        require_once OLO_PATH . 'includes/class-tile-utils.php';
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

        require_once OLO_PATH . 'includes/tiles/class-social-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-map-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-countdown-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-headline-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-html-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-list-tile.php';
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
        require_once OLO_PATH . 'includes/tiles/class-calendar-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-booking-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-servicelist-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-serviceinfo-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-servicehero-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-servicestats-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-serviceprices-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-servicegallery-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-serviceamenities-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-servicecheckin-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-servicemushrooms-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-servicevideo-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-servicecipat-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-serviceaddress-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-servicedirections-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-servicerules-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-bookingpicker-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-serviceexcerpt-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-servicedescription-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-serviceclub-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-servicerelated-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-killnextprev-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-langswitcher-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-servicesearch-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-serviceresults-tile.php';
        require_once OLO_PATH . 'includes/tiles/class-hostcard-tile.php';
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
        require_once OLO_PATH . 'includes/tiles/class-offcanvas-tile.php';
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

        $manager->register_tile( new Olo_Social_Tile() );
        $manager->register_tile( new Olo_Map_Tile() );
        $manager->register_tile( new Olo_Countdown_Tile() );
        $manager->register_tile( new Olo_Headline_Tile() );
        $manager->register_tile( new Olo_Html_Tile() );
        $manager->register_tile( new Olo_List_Tile() );
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
        $manager->register_tile( new Olo_Calendar_Tile() );
        $manager->register_tile( new Olo_Booking_Tile() );
        $manager->register_tile( new Olo_ServiceList_Tile() );
        $manager->register_tile( new Olo_ServiceInfo_Tile() );
        $manager->register_tile( new Olo_ServiceHero_Tile() );
        $manager->register_tile( new Olo_ServiceStats_Tile() );
        $manager->register_tile( new Olo_ServicePrices_Tile() );
        $manager->register_tile( new Olo_ServiceGallery_Tile() );
        $manager->register_tile( new Olo_ServiceAmenities_Tile() );
        $manager->register_tile( new Olo_ServiceCheckin_Tile() );
        $manager->register_tile( new Olo_ServiceMushrooms_Tile() );
        $manager->register_tile( new Olo_ServiceVideo_Tile() );
        $manager->register_tile( new Olo_ServiceCipat_Tile() );
        $manager->register_tile( new Olo_ServiceAddress_Tile() );
        $manager->register_tile( new Olo_ServiceDirections_Tile() );
        $manager->register_tile( new Olo_ServiceRules_Tile() );
        $manager->register_tile( new Olo_BookingPicker_Tile() );
        $manager->register_tile( new Olo_ServiceExcerpt_Tile() );
        $manager->register_tile( new Olo_ServiceDescription_Tile() );
        $manager->register_tile( new Olo_ServiceClub_Tile() );
        $manager->register_tile( new Olo_ServiceRelated_Tile() );
        $manager->register_tile( new Olo_KillNextPrev_Tile() );
        $manager->register_tile( new Olo_LangSwitcher_Tile() );
        $manager->register_tile( new Olo_ServiceSearch_Tile() );
        $manager->register_tile( new Olo_ServiceResults_Tile() );
        $manager->register_tile( new Olo_HostCard_Tile() );
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
        $manager->register_tile( new Olo_Offcanvas_Tile() );
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
