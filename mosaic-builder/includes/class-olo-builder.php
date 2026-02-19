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

        // Register core tiles
        $this->register_core_tiles();
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
            OLO_VERSION,
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

        wp_localize_script( 'olobuilder-js', 'oloData', [
            'restUrl'        => esc_url_raw( rest_url( 'olo/v1' ) ),
            'nonce'          => wp_create_nonce( 'wp_rest' ),
            'userId'         => get_current_user_id(),
            'userName'       => wp_get_current_user()->display_name,
            'version'        => OLO_VERSION,
            'pluginUrl'      => OLO_URL,
            'templateId'     => absint( $_GET['template_id'] ?? 0 ),
            'postId'         => absint( $_GET['post_id'] ?? 0 ),
            'themeColors'    => $this->detect_theme_colors(),
            'styles'         => $style_system->get_styles(),
            'stylesCss'      => $style_system->generate_css(),
            'presets'        => $style_system->get_presets(),
            'wpMenus'        => $this->get_wp_menus(),
            'activeHeaderId' => (int) get_option( 'olo_active_header', 0 ),
            'activeFooterId' => (int) get_option( 'olo_active_footer', 0 ),
            'activeSingles'  => $this->get_active_singles_map(),
            'templateList'       => $this->get_template_list(),
            'megapanelTemplates' => $this->get_megapanel_templates(),
            'postTypes'      => $this->get_public_post_types(),
            'taxonomies'     => $this->get_public_taxonomies(),
            'siteInfo'       => [
                'name'     => get_bloginfo( 'name' ),
                'tagline'  => get_bloginfo( 'description' ),
                'logo_url' => $this->get_site_logo_url(),
                'home_url' => home_url( '/' ),
            ],
        ] );
    }

    public function render_builder_page() {
        include OLO_PATH . 'templates/builder-page.php';
    }

    private function register_core_tiles() {
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
     * Get all registered WP nav menus for the builder UI.
     */
    private function get_wp_menus() {
        $menus  = wp_get_nav_menus();
        $result = [];
        foreach ( $menus as $menu ) {
            $result[] = [
                'id'   => $menu->term_id,
                'name' => $menu->name,
            ];
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
