<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
require_once __DIR__ . '/traits/trait-olobuild-rest-activation.php';
require_once __DIR__ . '/traits/trait-olobuild-rest-config.php';
require_once __DIR__ . '/traits/trait-olobuild-rest-dashboard.php';

class Olobuild_Rest_Api {
    use Olobuild_Rest_Activation_Trait;
    use Olobuild_Rest_Config_Trait;
    use Olobuild_Rest_Dashboard_Trait;

    private $namespace = 'olobuild/v1';

    public function init() {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
        // During REST requests, WP's determine_locale() returns the SITE locale,
        // not the user_locale. This breaks __() output for endpoints that return
        // translated strings to the JS dashboard. Switch to the logged-in user's
        // locale before dispatching any /olobuild/v1/ route.
        add_filter( 'rest_pre_dispatch', [ $this, 'apply_user_locale' ], 10, 3 );
    }

    /**
     * Switch to the logged-in user's locale for /olobuild/v1/ routes so that __()
     * returns strings in the right language (matches admin context behavior).
     */
    public function apply_user_locale( $result, $server, $request ) {
        if ( $result !== null ) {
            return $result;
        }
        if ( ! is_user_logged_in() ) {
            return $result;
        }
        $route = $request->get_route();
        if ( strpos( $route, '/' . $this->namespace . '/' ) !== 0 ) {
            return $result;
        }
        $user_locale = get_user_locale( get_current_user_id() );
        if ( $user_locale && $user_locale !== get_locale() ) {
            switch_to_locale( $user_locale );
        }
        return $result;
    }

    public function register_routes() {
        // Templates collection
        register_rest_route( $this->namespace, '/templates', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_templates' ],
                'permission_callback' => [ $this, 'check_permission' ],
                'args'                => [
                    'page'     => [ 'default' => 1, 'sanitize_callback' => 'absint' ],
                    'per_page' => [ 'default' => 20, 'sanitize_callback' => 'absint' ],
                    'status'   => [ 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ],
                    'type'     => [ 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ],
                ],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'create_template' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
        ] );

        // Single template — usa check_template_permission per ownership-aware ACL.
        register_rest_route( $this->namespace, '/templates/(?P<id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_template' ],
                'permission_callback' => [ $this, 'check_template_permission' ],
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'update_template' ],
                'permission_callback' => [ $this, 'check_template_permission' ],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [ $this, 'delete_template' ],
                'permission_callback' => [ $this, 'check_template_permission' ],
            ],
        ] );

        // Template export
        register_rest_route( $this->namespace, '/templates/(?P<id>\d+)/export', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'export_template' ],
            'permission_callback' => [ $this, 'check_template_permission' ],
        ] );

        // Template thumbnail upload (auto-cattura dal builder)
        register_rest_route( $this->namespace, '/templates/(?P<id>\d+)/thumbnail', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'upload_template_thumbnail' ],
            'permission_callback' => [ $this, 'check_template_permission' ],
        ] );

        // Template duplicate
        register_rest_route( $this->namespace, '/templates/(?P<id>\d+)/duplicate', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'duplicate_template' ],
            'permission_callback' => [ $this, 'check_template_permission' ],
        ] );

        // Template import
        register_rest_route( $this->namespace, '/templates/import', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'import_template' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );

        // Theme bundle: esporta/importa un SET di template selezionati (header+footer+pagine)
        register_rest_route( $this->namespace, '/templates/export-bundle', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'export_bundle' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );
        register_rest_route( $this->namespace, '/templates/import-bundle', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'import_bundle' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );

        // Template revisions
        register_rest_route( $this->namespace, '/templates/(?P<id>\d+)/revisions', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_revisions' ],
            'permission_callback' => [ $this, 'check_template_permission' ],
        ] );

        // Single revision (full content)
        register_rest_route( $this->namespace, '/revisions/(?P<rev_id>\d+)', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_revision' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );

        // Menu items (L1 only) for a given menu
        register_rest_route( $this->namespace, '/menu-items/(?P<menu_id>\d+)', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_menu_items' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );

        // Available tiles
        register_rest_route( $this->namespace, '/tiles', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_tiles' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );

        // Anteprima prodotti WooCommerce per la tile productgrid (canvas builder)
        register_rest_route( $this->namespace, '/productgrid-products', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_productgrid_products' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );

        // Dynamic content sources catalog
        register_rest_route( $this->namespace, '/dynamic-sources', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_dynamic_sources' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );

        // Dynamic content preview (resolve a single binding)
        register_rest_route( $this->namespace, '/dynamic-preview', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'preview_dynamic_field' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );

        // Style System
        register_rest_route( $this->namespace, '/styles', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_styles' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'save_styles' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
        ] );

        register_rest_route( $this->namespace, '/styles/reset', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'reset_styles' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );

        // Stock media — comportamento default (pagina admin "Stock media")
        register_rest_route( $this->namespace, '/stockmedia-behavior', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_stockmedia_behavior' ],
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'save_stockmedia_behavior' ],
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // Stock media — chiavi API provider (dati sensibili → manage_options)
        register_rest_route( $this->namespace, '/api-keys', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_api_keys' ],
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'save_api_keys' ],
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // Cursore magnetico globale (option olo_magnetic_cursor → Olobuild_Magnetic_Cursor)
        register_rest_route( $this->namespace, '/magnetic-cursor', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_magnetic_cursor' ],
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'save_magnetic_cursor' ],
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // HUD mirino globale (option olo_cursor_hud → Olobuild_Cursor_Hud)
        register_rest_route( $this->namespace, '/cursor-hud', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_cursor_hud' ],
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'save_cursor_hud' ],
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // Header activation
        register_rest_route( $this->namespace, '/header/activate', [
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'activate_header' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [ $this, 'deactivate_header' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_active_header' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
        ] );

        // Single template activation (per post_type)
        register_rest_route( $this->namespace, '/single/activate', [
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'activate_single' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [ $this, 'deactivate_single' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_active_singles' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
        ] );

        // Custom Fonts
        register_rest_route( $this->namespace, '/fonts', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_fonts' ],
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'upload_font' ],
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        register_rest_route( $this->namespace, '/fonts/(?P<id>[a-zA-Z0-9_-]+)', [
            'methods'             => 'DELETE',
            'callback'            => [ $this, 'delete_font' ],
            'permission_callback' => function () { return current_user_can( 'manage_options' ); },
        ] );

        // Custom icons
        register_rest_route( $this->namespace, '/custom-icons', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_custom_icons' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'upload_custom_icon' ],
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        register_rest_route( $this->namespace, '/custom-icons/(?P<name>[a-zA-Z0-9_-]+)', [
            'methods'             => 'DELETE',
            'callback'            => [ $this, 'delete_custom_icon' ],
            'permission_callback' => function () { return current_user_can( 'manage_options' ); },
        ] );

        // Global widgets CRUD
        register_rest_route( $this->namespace, '/global-widgets', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_global_widgets' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'create_global_widget' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
        ] );

        register_rest_route( $this->namespace, '/global-widgets/(?P<id>\d+)', [
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'update_global_widget' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [ $this, 'delete_global_widget' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
        ] );

        // Global Colors
        register_rest_route( $this->namespace, '/global-colors', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_global_colors' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'save_global_colors' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
        ] );

        // Global Typography
        register_rest_route( $this->namespace, '/global-typography', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_global_typography' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'save_global_typography' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
        ] );

        // Themes
        register_rest_route( $this->namespace, '/themes', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_themes' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );
        register_rest_route( $this->namespace, '/themes/(?P<theme_id>[a-zA-Z0-9_-]+)/import', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'import_theme' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );

        // Design Presets
        register_rest_route( $this->namespace, '/design-presets', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_design_presets' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'create_design_preset' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
        ] );

        register_rest_route( $this->namespace, '/design-presets/(?P<id>[a-zA-Z0-9_-]+)', [
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'update_design_preset' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [ $this, 'delete_design_preset' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
        ] );

        // Template Library
        register_rest_route( $this->namespace, '/template-library', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_template_library' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );
        register_rest_route( $this->namespace, '/template-library/(?P<id>[a-zA-Z0-9_-]+)', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_library_template' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );
        register_rest_route( $this->namespace, '/template-library/save', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'save_user_template' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );
        register_rest_route( $this->namespace, '/template-library/user/(?P<id>[a-zA-Z0-9_-]+)', [
            'methods'             => 'DELETE',
            'callback'            => [ $this, 'delete_user_template' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );

        // Design Presets Built-in
        register_rest_route( $this->namespace, '/design-presets/builtin', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_builtin_presets' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );

        // Design Tokens Export
        register_rest_route( $this->namespace, '/design-tokens', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'export_design_tokens' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );

        // Custom Code (head/body/footer snippets)
        register_rest_route( $this->namespace, '/custom-code', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_custom_code' ],
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'save_custom_code' ],
                // unfiltered_html, non manage_options: gli snippet vengono emessi raw nel
                // frontend, quindi solo chi può inserire HTML/JS arbitrario deve poterli salvare
                // (su multisite gli admin di sito NON hanno unfiltered_html).
                'permission_callback' => function () { return current_user_can( 'unfiltered_html' ); },
            ],
        ] );

        // Footer activation
        register_rest_route( $this->namespace, '/footer/activate', [
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'activate_footer' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [ $this, 'deactivate_footer' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_active_footer' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
        ] );

        // Archive template activation (per post_type + generic fallback)
        register_rest_route( $this->namespace, '/archive/activate', [
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'activate_archive' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [ $this, 'deactivate_archive' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_active_archives' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
        ] );

        // 404 template activation
        register_rest_route( $this->namespace, '/404/activate', [
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'activate_404' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [ $this, 'deactivate_404' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_active_404' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
        ] );

        // Maintenance mode
        register_rest_route( $this->namespace, '/maintenance', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_maintenance' ],
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'save_maintenance' ],
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // ─── Analytics Settings ──────────────────────────────────────
        register_rest_route( $this->namespace, '/analytics', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_analytics' ],
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'save_analytics' ],
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // ─── Critical CSS ────────────────────────────────────────────
        register_rest_route( $this->namespace, '/critical-css/generate', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'generate_critical_css' ],
            'permission_callback' => function () { return current_user_can( 'manage_options' ); },
        ] );

        register_rest_route( $this->namespace, '/critical-css/regenerate-all', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'regenerate_all_critical_css' ],
            'permission_callback' => function () { return current_user_can( 'manage_options' ); },
        ] );

        register_rest_route( $this->namespace, '/critical-css/purge', [
            'methods'             => 'DELETE',
            'callback'            => [ $this, 'purge_critical_css' ],
            'permission_callback' => function () { return current_user_can( 'manage_options' ); },
        ] );

        register_rest_route( $this->namespace, '/critical-css/status', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_critical_css_status' ],
            'permission_callback' => function () { return current_user_can( 'manage_options' ); },
        ] );

        // Site export/import
        register_rest_route( $this->namespace, '/site/export', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'site_export' ],
            'permission_callback' => function () { return current_user_can( 'manage_options' ); },
        ] );

        register_rest_route( $this->namespace, '/site/import', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'site_import' ],
            'permission_callback' => function () { return current_user_can( 'manage_options' ); },
        ] );

        // Lottie animation search (LottieFiles proxy)
        register_rest_route( $this->namespace, '/lottie/search', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'lottie_search' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );

        // Search results template activation
        register_rest_route( $this->namespace, '/search/activate', [
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'activate_search' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [ $this, 'deactivate_search' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_active_search' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
        ] );

        // Template preview render (returns HTML)
        register_rest_route( $this->namespace, '/templates/(?P<id>\d+)/render', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'render_template_preview' ],
            'permission_callback' => [ $this, 'check_template_permission' ],
        ] );

        // Builder live render (accepts tiles in POST body, returns HTML with data-olo-tile-id)
        register_rest_route( $this->namespace, '/builder/render', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'builder_render' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );

        // Builder render single tile (for incremental updates)
        register_rest_route( $this->namespace, '/builder/render-tile', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'builder_render_tile' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );

        // PostGrid live preview (returns lightweight post data for builder canvas)
        register_rest_route( $this->namespace, '/postgrid-preview', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_postgrid_preview' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );

        // Row Loop: load more (paginazione AJAX, nessun nonce richiesto perché
        // i dati sono pubblici — gli stessi che si vedono in pagina).
        register_rest_route( $this->namespace, '/row-loop/page', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'row_loop_page' ],
            'permission_callback' => '__return_true',
        ] );

        // ───────────────────────────────────────────────────────────
        // Dashboard cockpit endpoints
        // ───────────────────────────────────────────────────────────
        register_rest_route( $this->namespace, '/dashboard/kpis', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'dashboard_kpis' ],
            'permission_callback' => [ $this, 'check_dashboard_permission' ],
        ] );
        register_rest_route( $this->namespace, '/dashboard/palette', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'dashboard_palette' ],
            'permission_callback' => [ $this, 'check_dashboard_permission' ],
        ] );
        register_rest_route( $this->namespace, '/dashboard/recent', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'dashboard_recent' ],
            'permission_callback' => [ $this, 'check_dashboard_permission' ],
            'args'                => [
                'limit' => [ 'default' => 6, 'sanitize_callback' => 'absint' ],
            ],
        ] );
        register_rest_route( $this->namespace, '/dashboard/changelog', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'dashboard_changelog' ],
            'permission_callback' => [ $this, 'check_dashboard_permission' ],
            'args'                => [
                'limit' => [ 'default' => 3, 'sanitize_callback' => 'absint' ],
            ],
        ] );
        register_rest_route( $this->namespace, '/dashboard/prefs', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'dashboard_get_prefs' ],
                'permission_callback' => [ $this, 'check_dashboard_permission' ],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'dashboard_set_prefs' ],
                'permission_callback' => [ $this, 'check_dashboard_permission' ],
            ],
        ] );

        // ───────────────────────────────────────────────────────────
        // Form submissions
        // ───────────────────────────────────────────────────────────
        register_rest_route( $this->namespace, '/submissions', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'submissions_list' ],
            'permission_callback' => [ $this, 'check_submissions_permission' ],
            'args'                => [
                'status'    => [ 'default' => 'all', 'sanitize_callback' => 'sanitize_key' ],
                'form_name' => [ 'default' => '',    'sanitize_callback' => 'sanitize_text_field' ],
                'q'         => [ 'default' => '',    'sanitize_callback' => 'sanitize_text_field' ],
                'page'      => [ 'default' => 1,     'sanitize_callback' => 'absint' ],
                'per_page'  => [ 'default' => 30,    'sanitize_callback' => 'absint' ],
            ],
        ] );
        register_rest_route( $this->namespace, '/submissions/stats', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'submissions_stats' ],
            'permission_callback' => [ $this, 'check_submissions_permission' ],
        ] );
        register_rest_route( $this->namespace, '/submissions/(?P<id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'submissions_get' ],
                'permission_callback' => [ $this, 'check_submissions_permission' ],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [ $this, 'submissions_delete' ],
                'permission_callback' => [ $this, 'check_submissions_permission' ],
            ],
        ] );
        register_rest_route( $this->namespace, '/submissions/(?P<id>\d+)/read', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'submissions_toggle_read' ],
            'permission_callback' => [ $this, 'check_submissions_permission' ],
        ] );
        register_rest_route( $this->namespace, '/submissions/bulk', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'submissions_bulk' ],
            'permission_callback' => [ $this, 'check_submissions_permission' ],
        ] );

        // ── Page SEO (per-post meta box, esposto al builder Vue) ───────────
        register_rest_route( $this->namespace, '/page-seo/(?P<id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'page_seo_get' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'page_seo_save' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
        ] );

        // ── Link Picker: cerca pagine, post, CPT pubblici e tassonomie ─────
        // Usato da FieldLink.vue per popolare l'autocomplete con risultati ricchi
        // (thumbnail, excerpt, type label). Restituisce array di { id, title, url,
        // type, type_label, sublabel, thumbnail, excerpt }.
        register_rest_route( $this->namespace, '/link-search', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'link_search' ],
            'permission_callback' => [ $this, 'check_permission' ],
            'args'                => [
                'q'        => [ 'default' => '',  'sanitize_callback' => 'sanitize_text_field' ],
                'per_page' => [ 'default' => 15, 'sanitize_callback' => 'absint' ],
                'types'    => [ 'default' => '',  'sanitize_callback' => 'sanitize_text_field' ],
            ],
        ] );
    }

    /**
     * Lista campi SEO esposti al builder. Chiave Vue = chiave meta WP (senza prefisso `_olo_seo_`).
     * - schema_type accetta valori predefiniti (Article, BlogPosting, …) o 'none'.
     * - extra_jsonld è textarea libera, validata server-side prima dell'output.
     * - faq è array [{q, a}, ...]
     */
    private function page_seo_meta_map() {
        return [
            'title'           => [ 'meta' => '_olo_seo_title',          'type' => 'text' ],
            'description'     => [ 'meta' => '_olo_seo_description',    'type' => 'textarea' ],
            'focus_keyword'   => [ 'meta' => '_olo_seo_focus_keyword',  'type' => 'text' ],
            'canonical'       => [ 'meta' => '_olo_seo_canonical',      'type' => 'text' ],
            'noindex'         => [ 'meta' => '_olo_seo_noindex',        'type' => 'bool' ],
            'nofollow'        => [ 'meta' => '_olo_seo_nofollow',       'type' => 'bool' ],
            'og_title'        => [ 'meta' => '_olo_seo_og_title',       'type' => 'text' ],
            'og_description'  => [ 'meta' => '_olo_seo_og_description', 'type' => 'textarea' ],
            'og_image'        => [ 'meta' => '_olo_seo_og_image',       'type' => 'text' ],
            'tw_title'        => [ 'meta' => '_olo_seo_tw_title',       'type' => 'text' ],
            'tw_description'  => [ 'meta' => '_olo_seo_tw_description', 'type' => 'textarea' ],
            'schema_type'     => [ 'meta' => '_olo_seo_schema_type',    'type' => 'text' ],
            'extra_jsonld'    => [ 'meta' => '_olo_seo_extra_jsonld',   'type' => 'jsonld' ],
            'faq'             => [ 'meta' => '_olo_seo_faq',            'type' => 'faq' ],
        ];
    }

    public function page_seo_get( $request ) {
        $post_id = absint( $request['id'] );
        $post = get_post( $post_id );
        if ( ! $post || ! current_user_can( 'edit_post', $post_id ) ) {
            return new WP_Error( 'rest_forbidden', 'Not allowed', [ 'status' => 403 ] );
        }
        $out = [];
        foreach ( $this->page_seo_meta_map() as $key => $def ) {
            $val = get_post_meta( $post_id, $def['meta'], true );
            switch ( $def['type'] ) {
                case 'bool': $out[ $key ] = (bool) $val; break;
                case 'faq':  $out[ $key ] = is_array( $val ) ? array_values( $val ) : []; break;
                default:     $out[ $key ] = is_string( $val ) ? $val : ''; break;
            }
        }
        // Anteprime: titolo/url di default, dominio sito (per OG/Twitter preview lato Vue).
        $out['_defaults'] = [
            'site_name'   => get_bloginfo( 'name' ),
            'post_title'  => $post->post_title,
            'post_url'    => get_permalink( $post_id ),
            'site_host'   => wp_parse_url( home_url(), PHP_URL_HOST ),
        ];
        return rest_ensure_response( $out );
    }

    public function page_seo_save( $request ) {
        $post_id = absint( $request['id'] );
        if ( ! get_post( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
            return new WP_Error( 'rest_forbidden', 'Not allowed', [ 'status' => 403 ] );
        }
        $body = $request->get_json_params();
        if ( ! is_array( $body ) ) $body = [];

        $errors = [];
        foreach ( $this->page_seo_meta_map() as $key => $def ) {
            if ( ! array_key_exists( $key, $body ) ) continue;
            $raw = $body[ $key ];
            switch ( $def['type'] ) {
                case 'bool':
                    if ( $raw ) update_post_meta( $post_id, $def['meta'], '1' );
                    else        delete_post_meta( $post_id, $def['meta'] );
                    break;
                case 'text':
                    $v = sanitize_text_field( (string) $raw );
                    if ( $v !== '' ) update_post_meta( $post_id, $def['meta'], $v );
                    else             delete_post_meta( $post_id, $def['meta'] );
                    break;
                case 'textarea':
                    $v = sanitize_textarea_field( (string) $raw );
                    if ( $v !== '' ) update_post_meta( $post_id, $def['meta'], $v );
                    else             delete_post_meta( $post_id, $def['meta'] );
                    break;
                case 'faq':
                    $clean = [];
                    if ( is_array( $raw ) ) {
                        foreach ( $raw as $item ) {
                            $q = isset( $item['q'] ) ? sanitize_text_field( $item['q'] ) : '';
                            $a = isset( $item['a'] ) ? sanitize_textarea_field( $item['a'] ) : '';
                            if ( $q && $a ) $clean[] = [ 'q' => $q, 'a' => $a ];
                        }
                    }
                    if ( $clean ) update_post_meta( $post_id, $def['meta'], $clean );
                    else          delete_post_meta( $post_id, $def['meta'] );
                    break;
                case 'jsonld':
                    $s = is_string( $raw ) ? trim( $raw ) : '';
                    // Rimuove eventuali <script> di wrapping prima della validazione.
                    $s_clean = preg_replace( '#</?script[^>]*>#i', '', $s );
                    if ( $s_clean === '' ) {
                        delete_post_meta( $post_id, $def['meta'] );
                    } else {
                        $decoded = json_decode( $s_clean, true );
                        if ( $decoded === null && json_last_error() !== JSON_ERROR_NONE ) {
                            $errors[ $key ] = 'JSON non valido: ' . json_last_error_msg();
                        } else {
                            update_post_meta( $post_id, $def['meta'], wp_kses_post( $s_clean ) );
                        }
                    }
                    break;
            }
        }

        $resp = [ 'saved' => true ];
        if ( $errors ) $resp['errors'] = $errors;
        return rest_ensure_response( $resp );
    }

    /**
     * Permission callback più permissivo per la dashboard:
     * usa `manage_options` come fallback per ambienti dove `edit_pages` è
     * stato rimosso a livello di ruolo (es. Tutor LMS Pro).
     */
    public function check_dashboard_permission( $request = null ) {
        if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'edit_pages' ) ) {
            return false;
        }
        if ( $request && in_array( $request->get_method(), [ 'POST', 'PUT', 'DELETE' ], true ) ) {
            $nonce = $request->get_header( 'x-wp-nonce' );
            if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
                return new WP_Error( 'rest_forbidden', 'Nonce non valido.', [ 'status' => 403 ] );
            }
        }
        return true;
    }

    /**
     * Permessi submissions: contengono PII (nome, email, IP) quindi di default
     * solo manage_options, coerente con /submissions/export. Per riaprire la
     * dashboard submissions agli Editor:
     * add_filter( 'olobuild_submissions_capability', function () { return 'edit_pages'; } );
     */
    public function check_submissions_permission( $request = null ) {
        $cap = apply_filters( 'olobuild_submissions_capability', 'manage_options' );
        if ( ! current_user_can( $cap ) ) {
            return false;
        }
        if ( $request && in_array( $request->get_method(), [ 'POST', 'PUT', 'DELETE' ], true ) ) {
            $nonce = $request->get_header( 'x-wp-nonce' );
            if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
                return new WP_Error( 'rest_forbidden', 'Nonce non valido.', [ 'status' => 403 ] );
            }
        }
        return true;
    }

    public function check_permission( $request = null ) {
        if ( ! current_user_can( 'edit_pages' ) ) {
            return false;
        }

        // Enforcement lato server delle restrizioni per ruolo (Configurazione → Permessi & Ruoli).
        // Il filtro di default ritorna true: restringe SOLO se l'admin ha configurato i ruoli builder,
        // quindi non blocca chi oggi accede via edit_pages su installazioni non configurate.
        if ( ! apply_filters( 'olobuild_can_edit_builder', true ) ) {
            return new WP_Error( 'rest_forbidden', __( 'Il tuo ruolo non ha accesso al builder.', 'olobuild' ), array( 'status' => 403 ) );
        }

        // Verify WP REST nonce for write operations
        if ( $request && in_array( $request->get_method(), [ 'POST', 'PUT', 'DELETE' ], true ) ) {
            $nonce = $request->get_header( 'x-wp-nonce' );
            if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
                return new WP_Error( 'rest_forbidden', 'Nonce non valido.', array( 'status' => 403 ) );
            }
        }

        // Rate limiting: max 300 requests per minute per user (higher for builder iframe renders)
        $user_id = get_current_user_id();
        $key     = 'olo_api_rl_' . $user_id;
        $count   = (int) get_transient( $key );

        if ( $count > 300 ) {
            return new WP_Error( 'rate_limited', 'Too many requests', array( 'status' => 429 ) );
        }

        set_transient( $key, $count + 1, MINUTE_IN_SECONDS );

        return true;
    }

    /**
     * Permission callback per le route che operano su un singolo template
     * (`/templates/{id}`, `/templates/{id}/export`, `/templates/{id}/thumbnail`,
     * `/templates/{id}/duplicate`, `/templates/{id}/revisions`, `/templates/{id}/render`).
     *
     * Estende `check_permission` aggiungendo un check di ownership:
     * un utente con `edit_pages` (Author) può accedere SOLO ai propri template,
     * a meno che non abbia anche `edit_others_pages` (Editor/Admin).
     *
     * Questo impedisce IDOR cross-author: prima della modifica un Author che
     * conosceva (o indovinava) un ID di template altrui poteva leggerlo/modificarlo.
     */
    public function check_template_permission( $request ) {
        $base = $this->check_permission( $request );
        if ( $base !== true ) {
            return $base; // false o WP_Error → forward
        }

        // Editor+ può vedere tutti i template (cap standard WP).
        if ( current_user_can( 'edit_others_pages' ) ) {
            return true;
        }

        // Author: solo i propri template
        $id = isset( $request['id'] ) ? (int) $request['id'] : 0;
        if ( ! $id ) {
            return true; // nessun id nella route → check non applicabile
        }

        $db       = new Olobuild_Database();
        $template = $db->get_template( $id );
        if ( ! $template ) {
            // Lasciamo che sia il callback a rispondere 404 dopo il check di ownership;
            // ritornare 404 qui esporrebbe l'esistenza dell'ID a un attaccante.
            return true;
        }

        $owner = (int) ( $template['author_id'] ?? 0 );
        if ( $owner && $owner !== get_current_user_id() ) {
            return new WP_Error( 'rest_forbidden', 'Non sei autorizzato ad accedere a questo template.', array( 'status' => 403 ) );
        }

        return true;
    }

    /**
     * Ensure settings is always a JSON object (not array) in REST responses.
     * PHP json_decode('{}', true) returns [], which json_encode turns back to [].
     * JS treats [] as Array, losing non-indexed properties on stringify.
     */
    private function prepare_template( $template ) {
        if ( isset( $template['id'] ) ) {
            $template['id'] = (int) $template['id'];
        }
        // linked_post_id: post canonico associato a questo template, calcolato in modo robusto.
        // Per il pannello SEO serve il post PUBBLICATO che usa questo template, non un eventuale
        // draft "Handoff …" creato come preview. La risoluzione segue questa priorità:
        //   1. Primo post publish con meta `_olo_template_id` = template id
        //   2. Fallback: primo post di qualunque status con quel meta
        //   3. Fallback: settings.post_id del template (legacy / preview)
        if ( isset( $template['id'] ) ) {
            $linked = $this->resolve_template_linked_post( (int) $template['id'], $template );
            if ( $linked ) {
                $template['linked_post_id'] = $linked;
                // Permalink "reale" robusto per il pulsante "Reale" del builder.
                // get_permalink() gestisce correttamente page/CPT/post — a differenza del
                // vecchio fallback JS `?p=ID`, che dava 404 sulle page (usano `?page_id=ID`).
                // Per i contenuti non pubblicati (draft/pending/future/private) usa il preview
                // link, così l'editor loggato può comunque aprirli senza 404.
                $linked_status = get_post_status( $linked );
                if ( $linked_status && ! in_array( $linked_status, [ 'trash', 'auto-draft' ], true ) ) {
                    $permalink = ( $linked_status === 'publish' )
                        ? get_permalink( $linked )
                        : get_preview_post_link( $linked );
                    if ( $permalink ) {
                        $template['linked_post_permalink'] = $permalink;
                        $template['linked_post_status']    = $linked_status;
                    }
                }
            }
        }
        if ( isset( $template['settings'] ) && is_array( $template['settings'] ) && empty( $template['settings'] ) ) {
            $template['settings'] = new stdClass;
        }
        return $template;
    }

    /**
     * Trova il post canonico per un template, privilegiando i pubblicati.
     */
    private function resolve_template_linked_post( $template_id, $template ) {
        global $wpdb;
        // Lookup del post collegato via meta `_olo_template_id`. Join diretto su core
        // posts/postmeta: l'unico valore utente ($template_id) passa da prepare() con %s;
        // i soli token interpolati sono i nomi tabella core ({$wpdb->posts}/{$wpdb->postmeta}).
        // Risultato volatile (dipende dallo stato dei post) → non cacheato qui.
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $published = $wpdb->get_var( $wpdb->prepare(
            "SELECT p.ID FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} m ON m.post_id = p.ID AND m.meta_key = '_olo_template_id'
             WHERE m.meta_value = %s AND p.post_status = 'publish'
             ORDER BY p.post_date ASC LIMIT 1",
            (string) $template_id
        ) );
        if ( $published ) return (int) $published;
        $any = $wpdb->get_var( $wpdb->prepare(
            "SELECT p.ID FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} m ON m.post_id = p.ID AND m.meta_key = '_olo_template_id'
             WHERE m.meta_value = %s AND p.post_status != 'trash'
             ORDER BY FIELD(p.post_status,'publish','private','future','pending','draft','auto-draft','inherit'), p.post_date ASC LIMIT 1",
            (string) $template_id
        ) );
        // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        if ( $any ) return (int) $any;
        $settings_post_id = isset( $template['settings']['post_id'] ) ? (int) $template['settings']['post_id'] : 0;
        return $settings_post_id ?: 0;
    }

    public function get_templates( $request ) {
        $db = new Olobuild_Database();
        $result = $db->list_templates( [
            'page'     => $request->get_param( 'page' ),
            'per_page' => $request->get_param( 'per_page' ),
            'status'   => $request->get_param( 'status' ),
            'type'     => $request->get_param( 'type' ),
        ] );
        if ( isset( $result['items'] ) ) {
            // Batch-fetch instance counts (posts using each template)
            global $wpdb;
            $instance_counts = [];
            // Conteggio batch delle istanze (post che usano ogni template) via meta
            // `_olo_template_id`. Solo nome tabella core interpolato, nessun valore utente;
            // aggregato live non cacheato (cambia a ogni assegnazione template→post).
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
            $rows = $wpdb->get_results(
                "SELECT meta_value AS tpl_id, COUNT(*) AS cnt FROM {$wpdb->postmeta} WHERE meta_key = '_olo_template_id' GROUP BY meta_value"
            );
            foreach ( $rows as $row ) {
                $instance_counts[ (int) $row->tpl_id ] = (int) $row->cnt;
            }

            foreach ( $result['items'] as &$item ) {
                $tpl_id = isset( $item['id'] ) ? (int) $item['id'] : 0;
                $item['instances'] = $instance_counts[ $tpl_id ] ?? 0;

                // Resolve author display name
                $author_id = isset( $item['author_id'] ) ? (int) $item['author_id'] : 0;
                $user = $author_id ? get_userdata( $author_id ) : false;
                $item['author_name'] = $user ? $user->display_name : '';
            }
            unset( $item );

            $result['items'] = array_map( [ $this, 'prepare_template' ], $result['items'] );

            // Aggregato byType per i counter chip della UI templates.
            // Calcolato sull'intero set (non solo la pagina corrente).
            $tpl_table = $wpdb->prefix . 'olobuild_templates';
            $by_type = [];
            $total = 0;
            // Tabella custom del plugin ({prefix}olo_templates); nessun equivalente WP_Query.
            // Solo il nome tabella (da $wpdb->prefix) è interpolato, nessun valore utente;
            // aggregato live non cacheabile.
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
            $rows2 = $wpdb->get_results( "SELECT type, COUNT(*) AS cnt FROM $tpl_table GROUP BY type" );
            foreach ( $rows2 as $r ) {
                $t = $r->type ?: 'page';
                $by_type[ $t ] = (int) $r->cnt;
                $total += (int) $r->cnt;
            }
            $by_type['all'] = $total;
            $result['byType'] = $by_type;
        }
        return rest_ensure_response( $result );
    }

    /**
     * Duplica un template esistente: copia title + " (Copia)", content,
     * settings, type e status='draft'. Stesso author del corrente utente.
     */
    public function duplicate_template( $request ) {
        $id = (int) $request['id'];
        $db = new Olobuild_Database();
        $src = $db->get_template( $id );
        if ( ! $src ) {
            return new WP_Error( 'not_found', 'Template non trovato.', [ 'status' => 404 ] );
        }

        $new_id = $db->create_template( [
            'title'    => ( $src['title'] ?: __( 'Senza titolo', 'olobuild' ) ) . ' ' . __( '(Copia)', 'olobuild' ),
            'type'     => $src['type'] ?: 'page',
            'content'  => is_array( $src['content'] ) ? $src['content'] : [],
            'settings' => is_array( $src['settings'] ) ? $src['settings'] : [],
            'status'   => 'draft',
        ] );

        if ( ! $new_id ) {
            return new WP_Error( 'duplicate_failed', 'Duplicazione fallita.', [ 'status' => 500 ] );
        }

        $copy = $db->get_template( $new_id );
        return rest_ensure_response( $this->prepare_template( $copy ) );
    }

    /**
     * Sanifica i campi di codice grezzo dei tile (html_content, shortcode_text)
     * quando chi salva NON ha la capability unfiltered_html. Gli utenti fidati
     * (admin / chi ha unfiltered_html) mantengono l'HTML completo; gli altri
     * passano per wp_kses_post (niente <script> persistente → no stored XSS,
     * importante su multisite dove gli Editor non hanno unfiltered_html).
     * Cammina l'albero ricorsivamente toccando SOLO quei due campi: la struttura
     * del template resta invariata.
     */
    private function sanitize_unfiltered_tile_fields( $data ) {
        if ( current_user_can( 'unfiltered_html' ) ) {
            return $data;
        }
        if ( is_array( $data ) ) {
            foreach ( $data as $k => $v ) {
                if ( ( 'html_content' === $k || 'shortcode_text' === $k ) && is_string( $v ) ) {
                    $data[ $k ] = wp_kses_post( $v );
                } else {
                    $data[ $k ] = $this->sanitize_unfiltered_tile_fields( $v );
                }
            }
        }
        return $data;
    }

    public function create_template( $request ) {
        $db   = new Olobuild_Database();
        $body = $request->get_json_params();

        // Validate content — must be an array (list of sections/rows)
        $content = $body['content'] ?? [];
        if ( ! is_array( $content ) ) {
            return new WP_Error( 'invalid_content', 'Il campo content deve essere un array.', [ 'status' => 400 ] );
        }
        $content = $this->sanitize_unfiltered_tile_fields( $content );

        // Validate settings — must be an array or object (associative array)
        $settings = $body['settings'] ?? [];
        if ( ! is_array( $settings ) && ! is_object( $settings ) ) {
            return new WP_Error( 'invalid_settings', 'Il campo settings deve essere un oggetto.', [ 'status' => 400 ] );
        }
        if ( is_object( $settings ) ) {
            $settings = (array) $settings;
        }

        $id = $db->create_template( [
            'title'    => sanitize_text_field( $body['title'] ?? 'Senza titolo' ),
            'type'     => sanitize_text_field( $body['type'] ?? 'page' ),
            'content'  => $content,
            'settings' => $settings,
            'status'   => sanitize_text_field( $body['status'] ?? 'draft' ),
        ] );

        if ( ! $id ) {
            return new WP_Error( 'create_failed', 'Impossibile creare il template.', [ 'status' => 500 ] );
        }

        // Auto-binding pagina WP per template di tipo 'page' senza post associato.
        $this->maybe_auto_create_linked_page( $id, $db );

        $template = $db->get_template( $id );
        return rest_ensure_response( $this->prepare_template( $template ) );
    }

    /**
     * Crea automaticamente una pagina WordPress collegata al template
     * quando il template è di tipo 'page' e non ha ancora un post associato.
     * La pagina viene creata come bozza con stesso titolo del template;
     * `auto_render_template` (filtro the_content) si occupa del rendering frontend.
     *
     * Idempotente: se settings.post_id esiste già, non fa nulla.
     */
    private function maybe_auto_create_linked_page( $template_id, $db = null ) {
        if ( ! $db ) {
            $db = new Olobuild_Database();
        }
        $template = $db->get_template( $template_id );
        if ( ! $template ) return;

        // Solo template di tipo 'page' (non header/footer/single/CPT).
        if ( ( $template['type'] ?? '' ) !== 'page' ) return;

        $settings = (array) ( $template['settings'] ?? [] );
        if ( ! empty( $settings['post_id'] ) ) {
            // Verifica che la pagina esista ancora (potrebbe essere stata cestinata).
            $linked = get_post( (int) $settings['post_id'] );
            if ( $linked && $linked->post_status !== 'trash' ) return;
            // Pagina cestinata o eliminata → ne creiamo una nuova
        }

        $title = $template['title'] ?: 'Senza titolo';

        $page_id = wp_insert_post( [
            'post_title'   => $title,
            'post_type'    => 'page',
            'post_status'  => 'draft',
            'post_content' => '',
            'post_author'  => get_current_user_id(),
        ], true );

        if ( ! $page_id || is_wp_error( $page_id ) ) return;

        update_post_meta( $page_id, '_olo_template_id', $template_id );

        // Aggiorna settings del template col post_id appena creato
        $settings['post_id'] = $page_id;
        $db->update_template( $template_id, [ 'settings' => $settings ] );
    }

    public function get_template( $request ) {
        $db       = new Olobuild_Database();
        $template = $db->get_template( (int) $request['id'] );

        if ( ! $template ) {
            return new WP_Error( 'not_found', 'Template non trovato.', [ 'status' => 404 ] );
        }

        return rest_ensure_response( $this->prepare_template( $template ) );
    }

    public function update_template( $request ) {
        $db   = new Olobuild_Database();
        $id   = (int) $request['id'];
        $body = $request->get_json_params();

        $existing = $db->get_template( $id );
        if ( ! $existing ) {
            return new WP_Error( 'not_found', 'Template non trovato.', [ 'status' => 404 ] );
        }

        // Save revision before updating
        $db->create_revision( $id, $existing['content'] );

        $update_data = [];
        if ( isset( $body['title'] ) ) {
            $update_data['title'] = $body['title'];
        }
        if ( isset( $body['type'] ) ) {
            $update_data['type'] = $body['type'];
        }
        if ( isset( $body['content'] ) ) {
            if ( ! is_array( $body['content'] ) ) {
                return new WP_Error( 'invalid_content', 'Il campo content deve essere un array.', [ 'status' => 400 ] );
            }
            $update_data['content'] = $this->sanitize_unfiltered_tile_fields( $body['content'] );
        }
        if ( isset( $body['settings'] ) ) {
            if ( ! is_array( $body['settings'] ) && ! is_object( $body['settings'] ) ) {
                return new WP_Error( 'invalid_settings', 'Il campo settings deve essere un oggetto.', [ 'status' => 400 ] );
            }
            $update_data['settings'] = is_object( $body['settings'] ) ? (array) $body['settings'] : $body['settings'];
        }
        if ( isset( $body['status'] ) ) {
            $update_data['status'] = $body['status'];
        }

        $db->update_template( $id, $update_data );

        // Auto-binding: se il template è di tipo 'page' e non ha ancora una pagina
        // collegata (es. creato in versioni precedenti del plugin), la creiamo ora.
        $this->maybe_auto_create_linked_page( $id, $db );

        // Sincronizza titolo / status della pagina collegata col template.
        // Il template usa 'published'|'draft', WordPress usa 'publish'|'draft'.
        $template_after = $db->get_template( $id );
        $linked_post_id = (int) ( $template_after['settings']['post_id'] ?? 0 );
        if ( $linked_post_id && get_post( $linked_post_id ) ) {
            $sync = [ 'ID' => $linked_post_id ];

            // Sync titolo
            if ( isset( $update_data['title'] ) && $update_data['title'] !== ( $existing['title'] ?? '' ) ) {
                $sync['post_title'] = $update_data['title'];
            }

            // Sync status: template 'published' → page 'publish', template 'draft' → page 'draft'.
            // Si attiva solo quando il client manda esplicitamente uno status nuovo.
            if ( isset( $update_data['status'] ) && $update_data['status'] !== ( $existing['status'] ?? '' ) ) {
                $map = [ 'published' => 'publish', 'draft' => 'draft' ];
                if ( isset( $map[ $update_data['status'] ] ) ) {
                    $sync['post_status'] = $map[ $update_data['status'] ];
                }
            }

            if ( count( $sync ) > 1 ) {
                wp_update_post( $sync );
            }
        }

        return rest_ensure_response( $this->prepare_template( $template_after ) );
    }

    public function delete_template( $request ) {
        $db = new Olobuild_Database();
        $id = (int) $request['id'];

        $existing = $db->get_template( $id );
        if ( ! $existing ) {
            return new WP_Error( 'not_found', 'Template non trovato.', [ 'status' => 404 ] );
        }

        $db->delete_template( $id );
        return rest_ensure_response( [ 'deleted' => true ] );
    }

    public function get_menu_items( $request ) {
        $menu_id = absint( $request['menu_id'] );
        $items   = wp_get_nav_menu_items( $menu_id );

        if ( ! $items || ! is_array( $items ) ) {
            return rest_ensure_response( [] );
        }

        // Return only L1 (top-level) items
        $result = [];
        foreach ( $items as $item ) {
            if ( (int) $item->menu_item_parent === 0 ) {
                $result[] = [
                    'id'    => $item->ID,
                    'title' => $item->title,
                ];
            }
        }

        return rest_ensure_response( $result );
    }

    public function get_tiles() {
        $manager = Olobuild_Tile_Manager::instance();
        $tiles   = $manager->get_tiles();

        $result = [];
        foreach ( $tiles as $type => $tile ) {
            $result[] = [
                'type'     => $tile->get_type(),
                'name'     => $tile->get_name(),
                'icon'     => $tile->get_icon(),
                'category' => $tile->get_category(),
                'defaults' => $tile->get_defaults(),
                'controls' => $tile->get_controls(),
            ];
        }

        return rest_ensure_response( $result );
    }

    public function get_dynamic_sources() {
        $dc = new Olobuild_Dynamic_Content();
        return rest_ensure_response( $dc->get_available_sources() );
    }

    /**
     * Prodotti WooCommerce normalizzati per l'anteprima della tile productgrid.
     * Stessa logica del render frontend (Olobuild_ProductGrid_Tile::woo_items).
     */
    public function get_productgrid_products( $request ) {
        if ( ! class_exists( 'WooCommerce' ) || ! class_exists( 'Olobuild_ProductGrid_Tile' ) ) {
            return rest_ensure_response( [ 'woo' => false, 'items' => [] ] );
        }
        $items = Olobuild_ProductGrid_Tile::woo_items( [
            'woo_category'  => sanitize_text_field( (string) $request->get_param( 'category' ) ),
            'woo_limit'     => intval( $request->get_param( 'limit' ) ?: 8 ),
            'woo_orderby'   => sanitize_key( (string) ( $request->get_param( 'orderby' ) ?: 'date' ) ),
            'woo_order'     => sanitize_key( (string) ( $request->get_param( 'order' ) ?: 'DESC' ) ),
            'woo_on_sale'   => '1' === (string) $request->get_param( 'on_sale' ),
            'woo_quick_add' => sanitize_text_field( (string) ( $request->get_param( 'quick_add' ) ?: 'Quick add' ) ),
        ] );
        return rest_ensure_response( [ 'woo' => true, 'items' => $items ] );
    }

    public function get_styles() {
        $style_system = Olobuild_Style_System::instance();
        return rest_ensure_response( [
            'styles' => $style_system->get_styles(),
            'css'    => $style_system->generate_css(),
        ] );
    }

    public function save_styles( $request ) {
        $body         = $request->get_json_params();
        $style_system = Olobuild_Style_System::instance();
        $saved        = $style_system->save_styles( $body );

        return rest_ensure_response( [
            'styles' => $style_system->get_styles(),
            'css'    => $style_system->generate_css(),
        ] );
    }

    public function reset_styles() {
        $style_system = Olobuild_Style_System::instance();
        $defaults     = $style_system->reset_styles();

        return rest_ensure_response( [
            'styles' => $defaults,
            'css'    => $style_system->generate_css(),
        ] );
    }

    public function preview_dynamic_field( $request ) {
        $body    = $request->get_json_params();
        $source  = sanitize_text_field( $body['source'] ?? '' );
        $field   = sanitize_text_field( $body['field'] ?? '' );
        $post_id = absint( $body['post_id'] ?? 0 );

        // Verify the user can read the requested post
        if ( $post_id && ! current_user_can( 'read_post', $post_id ) ) {
            return new WP_Error( 'forbidden', 'Cannot preview this post', array( 'status' => 403 ) );
        }

        if ( ! $source || ! $field ) {
            return new WP_Error( 'missing_params', 'source and field are required.', [ 'status' => 400 ] );
        }

        // If no post_id provided, use the latest published post for preview
        if ( ! $post_id && in_array( $source, [ 'current_post', 'custom_field', 'acf', 'taxonomy_field', 'author' ], true ) ) {
            $recent = get_posts( [ 'numberposts' => 1, 'post_status' => 'publish' ] );
            if ( ! empty( $recent ) ) {
                $post_id = $recent[0]->ID;
            }
        }

        $dc    = new Olobuild_Dynamic_Content();
        $value = $dc->resolve_field( $source, $field, $post_id );

        return rest_ensure_response( [
            'value'   => $value,
            'post_id' => $post_id,
        ] );
    }


    // ── Custom Fonts ────────────────────────────────────────


    /**
     * Proxy search for LottieFiles animations via public GraphQL API.
     */
    public function lottie_search( $request ) {
        $params = $request->get_json_params();
        $query  = sanitize_text_field( $params['query'] ?? '' );
        $cursor = sanitize_text_field( $params['cursor'] ?? '' );

        if ( empty( $query ) ) {
            return new WP_Error( 'missing_query', 'Query parameter is required.', [ 'status' => 400 ] );
        }

        $per_page = 24;
        // Use GraphQL variables to prevent injection (never interpolate user input into query string)
        $gql      = 'query SearchAnimations($query: String!, $first: Int!, $after: String) { searchPublicAnimations(first: $first, after: $after, query: $query) { edges { node { id name jsonUrl gifUrl } cursor } totalCount pageInfo { hasNextPage endCursor } } }';
        $variables = [ 'query' => $query, 'first' => $per_page ];
        if ( $cursor ) {
            $variables['after'] = $cursor;
        }

        $response = wp_remote_post( 'https://graphql.lottiefiles.com/', [
            'timeout' => 15,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => wp_json_encode( [ 'query' => $gql, 'variables' => $variables ] ),
        ] );

        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'fetch_error', $response->get_error_message(), [ 'status' => 502 ] );
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        $search = $body['data']['searchPublicAnimations'] ?? null;

        if ( ! $search ) {
            return new WP_Error( 'parse_error', 'Risposta non valida da LottieFiles.', [ 'status' => 502 ] );
        }

        $results = [];
        foreach ( $search['edges'] as $edge ) {
            $node = $edge['node'];
            $json_url = $node['jsonUrl'] ?? '';
            if ( ! $json_url ) continue;

            $results[] = [
                'id'      => $node['id'] ?? wp_rand(),
                'name'    => $node['name'] ?? 'Untitled',
                'url'     => $json_url,
                'preview' => $node['gifUrl'] ?? '',
            ];
        }

        $page_info = $search['pageInfo'] ?? [];

        return rest_ensure_response( [
            'results'    => $results,
            'has_more'   => ! empty( $page_info['hasNextPage'] ),
            'end_cursor' => $page_info['endCursor'] ?? '',
            'total'      => $search['totalCount'] ?? 0,
            'query'      => $query,
        ] );
    }

    /**
     * Render a template as HTML for builder preview (header/footer).
     */
    /**
     * Builder live render: accepts tiles JSON in POST body, returns HTML with data-olo-tile-id.
     */
    public function builder_render( $request ) {
        $body = $request->get_json_params();
        $tiles = $body['tiles'] ?? [];
        $header_tiles = $body['header_tiles'] ?? [];
        $footer_tiles = $body['footer_tiles'] ?? [];
        $page_settings = $body['page_settings'] ?? [];
        $template_type = $body['template_type'] ?? 'page';
        $preview_post_id = $body['preview_post_id'] ?? 0;

        if ( empty( $tiles ) && empty( $header_tiles ) && empty( $footer_tiles ) ) {
            return rest_ensure_response( [
                'html'       => Olobuild_Builder::get_iframe_empty_html(),
                'css'        => [],
                'inline_css' => '',
            ] );
        }

        // For single templates, set up a post context so dynamic tiles work
        $original_post = null;
        if ( $template_type === 'single' ) {
            global $post, $wp_query;
            $original_post = $post;

            if ( $preview_post_id ) {
                $post = get_post( $preview_post_id );
            } else {
                // Find a published post with actual content (skip empty test posts)
                $post_type = $body['post_type'] ?? 'post';
                $query_args = [
                    'post_type'      => $post_type,
                    'posts_per_page' => 10,
                    'post_status'    => 'publish',
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                ];

                // For olo_service with active module, filter by service type
                if ( $post_type === 'olo_service' && class_exists( 'Olo_Module_Registry' ) ) {
                    $module = Olo_Module_Registry::get_active_module();
                    if ( $module ) {
                        $svc_type_map = [
                            'real-estate'   => 'property',
                            'accommodation' => 'accommodation',
                            'appointments'  => 'service',
                            'rentals'       => 'service',
                            'events'        => 'service',
                            'restaurants'   => 'service',
                        ];
                        $svc_type = $svc_type_map[ $module->get_id() ] ?? '';
                        if ( $svc_type ) {
                            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- preview builder: filtro per tipo servizio necessario alla funzione, volume limitato (10 post).
                            $query_args['meta_query'] = [
                                [ 'key' => '_olo_service_type', 'value' => $svc_type ],
                            ];
                        }
                    }
                }

                $sample = get_posts( $query_args );
                // Prefer a post with content + gallery (better preview)
                $best = null;
                foreach ( $sample as $s ) {
                    if ( ! empty( $s->post_content ) && get_post_thumbnail_id( $s->ID ) ) {
                        $best = $s;
                        break;
                    }
                }
                // Fallback: any with content
                if ( ! $best ) {
                    foreach ( $sample as $s ) {
                        if ( ! empty( $s->post_content ) ) { $best = $s; break; }
                    }
                }
                if ( ! $best && ! empty( $sample ) ) {
                    $best = $sample[0];
                }
                if ( $best ) {
                    $post = $best;
                }
            }

            if ( $post ) {
                setup_postdata( $post );
                // Trick is_singular() into returning true
                if ( $wp_query ) {
                    $wp_query->is_singular = true;
                    $wp_query->is_single   = true;
                    $wp_query->is_page     = false;
                }
            }
        }

        $renderer = new Olobuild_Frontend_Renderer();
        $renderer->builder_mode = true;

        $parts = [];

        // Resolve per-zone page_bg (so the .olo-template wrapper paints the same bg as in render_shortcode)
        $header_bg = $body['header_page_bg'] ?? null;
        $body_bg   = $page_settings['page_bg'] ?? null;
        $footer_bg = $body['footer_page_bg'] ?? null;
        $css_builder = class_exists( 'Olobuild_CSS_Builder' ) ? new Olobuild_CSS_Builder() : null;
        $bg_inline = function( $bg ) use ( $css_builder ) {
            if ( ! $css_builder ) return '';
            if ( ! is_array( $bg ) || empty( $bg['type'] ) || $bg['type'] === 'none' ) return '';
            $css = $css_builder->get_bg_inline_css( $bg );
            return $css ? ' style="' . esc_attr( $css ) . '"' : '';
        };

        // Header (wrap in .olo-template so frontend.css container max-width rules apply consistently with the public site)
        if ( ! empty( $header_tiles ) && is_array( $header_tiles ) ) {
            ob_start();
            echo '<header class="olo-site-header olo-header-classic" data-olo-zone="header"><div class="olo-template"' . $bg_inline( $header_bg ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $bg_inline() closure returns '' or a style attribute esc_attr()'d at construction
            $renderer->render_tiles_array( $header_tiles, $page_settings );
            echo '</div></header>';
            $parts[] = ob_get_clean();
        }

        // Body
        if ( ! empty( $tiles ) && is_array( $tiles ) ) {
            ob_start();
            echo '<main data-olo-zone="body"><div class="olo-template"' . $bg_inline( $body_bg ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $bg_inline() closure returns '' or a style attribute esc_attr()'d at construction
            $renderer->render_tiles_array( $tiles, $page_settings );
            echo '</div></main>';
            $parts[] = ob_get_clean();
        } elseif ( ! empty( $header_tiles ) || ! empty( $footer_tiles ) ) {
            // Body vuoto ma header/footer presenti: inietta empty state centrato
            // così il canvas non resta uno spazio bianco silenzioso tra le due zone.
            $parts[] = '<main data-olo-zone="body">' . Olobuild_Builder::get_iframe_empty_html() . '</main>';
        }

        // Footer
        if ( ! empty( $footer_tiles ) && is_array( $footer_tiles ) ) {
            ob_start();
            echo '<footer class="olo-site-footer" data-olo-zone="footer"><div class="olo-template"' . $bg_inline( $footer_bg ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $bg_inline() closure returns '' or a style attribute esc_attr()'d at construction
            $renderer->render_tiles_array( $footer_tiles, $page_settings );
            echo '</div></footer>';
            $parts[] = ob_get_clean();
        }

        $html = implode( '', $parts );

        $css_urls = [
            OLOBUILD_URL . 'assets/vendor/uikit/css/uikit.min.css',
            OLOBUILD_URL . 'assets/css/frontend.css?v=' . OLOBUILD_VERSION,
            OLOBUILD_URL . 'assets/css/olo-proslider.css?v=' . OLOBUILD_VERSION,
        ];

        $inline_css = '';
        if ( class_exists( 'Olobuild_Style_System' ) ) {
            $inline_css = Olobuild_Style_System::instance()->generate_css();
        }

        // Estendi inline_css con il page background della body zone, applicato a html+body
        // dell'iframe builder. Senza questo, il bg vive solo dentro `.olo-template` (che ha
        // max-width limitata) e i bordi laterali dell'iframe restano del colore di default
        // del tema. UX-wise l'utente si aspetta che il bg pagina riempia l'intera area canvas.
        // !important perché altri inline_css generati da Olobuild_Style_System potrebbero settare
        // `body { background: ... }` con specificità simile, e l'ordine di append non basta
        // a garantire il vincitore.
        if ( $css_builder && is_array( $body_bg ) && ! empty( $body_bg['type'] ) && $body_bg['type'] !== 'none' ) {
            $body_bg_css = $css_builder->get_bg_inline_css( $body_bg );
            if ( $body_bg_css ) {
                // Aggiungi !important a ciascuna prop CSS generata.
                $body_bg_important = preg_replace( '/;(?=\s*[a-z-]+\s*:)|;\s*$|(?<=[^;])\s*$/i', ' !important;', rtrim( $body_bg_css, '; ' ) . ';' );
                // Targetiamo html, body E il root wrapper interno: il template iframe
                // imposta `body { background: #fff }` di base, e #olo-iframe-root copre
                // body se eredita o ha bg implicito.
                $inline_css .= "\nhtml, body, body > #olo-iframe-root { " . $body_bg_important . " }\n";
            }
        }

        $renderer->builder_mode = false;

        // Restore original post context
        if ( $template_type === 'single' && $original_post !== null ) {
            global $post, $wp_query;
            $post = $original_post;
            if ( $post ) {
                setup_postdata( $post );
            }
            if ( $wp_query ) {
                $wp_query->is_singular = false;
                $wp_query->is_single   = false;
            }
        }

        return rest_ensure_response( [
            'html'       => $html,
            'css'        => $css_urls,
            'inline_css' => $inline_css,
        ] );
    }

    /**
     * Builder render single tile: accepts a tile node, returns rendered HTML.
     */
    /**
     * REST: ritorna l'HTML dei children della Row con loop, paginati alla pagina N.
     * Usato dal bottone "Carica altri" frontend.
     *
     * Body atteso: { template_id: int, row_id: string (md5 short), page: int }
     */
    public function row_loop_page( $request ) {
        $body         = $request->get_json_params();
        $template_id  = absint( $body['template_id'] ?? 0 );
        $row_id_short = sanitize_text_field( $body['row_id'] ?? '' );
        $page         = max( 1, intval( $body['page'] ?? 1 ) );

        if ( ! $template_id || ! $row_id_short ) {
            return new WP_Error( 'invalid_params', 'Parametri mancanti.', [ 'status' => 400 ] );
        }

        $db = new Olobuild_Database();
        $template = $db->get_template( $template_id );
        if ( ! $template ) {
            return new WP_Error( 'not_found', 'Template non trovato.', [ 'status' => 404 ] );
        }

        // Trova la Row col matching id (md5 short)
        $row_node = $this->find_row_by_short_id( $template['content'] ?? [], $row_id_short );
        if ( ! $row_node ) {
            return new WP_Error( 'row_not_found', 'Row con loop non trovata.', [ 'status' => 404 ] );
        }

        $s = $row_node['settings'] ?? [];
        if ( empty( $s['loop_enabled'] ) ) {
            return new WP_Error( 'loop_disabled', 'Loop non attivo su questa row.', [ 'status' => 400 ] );
        }

        $renderer = new Olobuild_Frontend_Renderer();
        $loop_query = $renderer->run_row_loop_query( $s, $page, true );
        $hover_css_rules = [];
        $tile_counter = 0;
        $manager = class_exists( 'Olobuild_Tile_Manager' ) ? new Olobuild_Tile_Manager() : null;

        $html = $renderer->render_row_loop_children(
            $row_node['children'] ?? [],
            $loop_query->posts,
            $manager,
            $template_id,
            $hover_css_rules,
            $tile_counter,
            ( $s['layout_mode'] ?? '' ) === 'grid'
        );

        return rest_ensure_response( [
            'html'         => $html,
            'page'         => $page,
            'max_pages'    => intval( $loop_query->max_num_pages ),
            'has_more'     => $page < intval( $loop_query->max_num_pages ),
        ] );
    }

    /**
     * Cerca ricorsivamente nel content del template una Row con id che, dopo md5 short,
     * coincide con $short_id. Ritorna il nodo trovato o null.
     */
    private function find_row_by_short_id( $nodes, $short_id ) {
        foreach ( $nodes as $node ) {
            if ( ( $node['type'] ?? '' ) === 'row' ) {
                $node_id = $node['id'] ?? '';
                if ( $node_id && substr( md5( $node_id ), 0, 8 ) === $short_id ) {
                    return $node;
                }
            }
            if ( ! empty( $node['children'] ) && is_array( $node['children'] ) ) {
                $found = $this->find_row_by_short_id( $node['children'], $short_id );
                if ( $found ) return $found;
            }
        }
        return null;
    }

    public function builder_render_tile( $request ) {
        $body = $request->get_json_params();
        $tile = $body['tile'] ?? null;
        $page_settings = $body['page_settings'] ?? [];
        $template_type = $body['template_type'] ?? 'page';
        // template_id + counter_hint servono al patch incrementale per generare
        // lo STESSO css_id (`ms-X-Y`) del full render — altrimenti hover CSS e
        // responsive rules non matchano con il nuovo nodo.
        $template_id   = intval( $body['template_id'] ?? 0 );
        $counter_hint  = intval( $body['tile_counter_hint'] ?? 0 );

        if ( empty( $tile ) || empty( $tile['type'] ) ) {
            return rest_ensure_response( [ 'html' => '' ] );
        }

        // Set up post context for single templates
        $original_post = null;
        if ( $template_type === 'single' ) {
            global $post, $wp_query;
            $original_post = $post;
            $preview_post_id = $body['preview_post_id'] ?? 0;

            if ( $preview_post_id ) {
                $post = get_post( $preview_post_id );
            } else {
                $post_type = $body['post_type'] ?? 'post';
                $sample = get_posts( [
                    'post_type'      => $post_type,
                    'posts_per_page' => 10,
                    'post_status'    => 'publish',
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                ] );
                $best = null;
                foreach ( $sample as $s ) {
                    if ( ! empty( $s->post_content ) ) {
                        $best = $s;
                        break;
                    }
                }
                if ( ! $best && ! empty( $sample ) ) {
                    $best = $sample[0];
                }
                if ( $best ) {
                    $post = $best;
                }
            }
            if ( $post ) {
                setup_postdata( $post );
                if ( $wp_query ) {
                    $wp_query->is_singular = true;
                    $wp_query->is_single   = true;
                }
            }
        }

        $renderer = new Olobuild_Frontend_Renderer();
        $renderer->builder_mode = true;

        $renderer->breakpoints = wp_parse_args( $page_settings['breakpoints'] ?? [], [
            'widescreen'       => 1400,
            'tablet_landscape' => 1200,
            'tablet'           => 960,
            'mobile_landscape' => 640,
            'mobile'           => 480,
        ] );

        $manager = Olobuild_Tile_Manager::instance();
        $hover_css = [];
        // counter_hint - 1: prima dell'increment in render_*_node, così che ++$counter
        // produca esattamente l'hint passato dal client (= ID del nodo già nel DOM).
        $counter = max( 0, $counter_hint - 1 );

        ob_start();
        echo $renderer->render_node_public( $tile, $manager, $template_id, $hover_css, $counter ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- builder-preview HTML generated by Olobuild_Frontend_Renderer::render_node_public(); each tile escapes its own output, captured into the REST response buffer
        $html = ob_get_clean();

        // Hover CSS + responsive CSS raccolti durante il render del nodo:
        // il client li userà per aggiornare un `<style data-tile-id="X">` dedicato
        // nell'iframe (impedisce accumulo di rules stale dopo molte patch).
        $hover_css_str = is_array( $hover_css ) ? implode( "\n", $hover_css ) : '';
        $responsive_css_str = '';
        if ( ! empty( $renderer->responsive_css_rules ) ) {
            foreach ( $renderer->responsive_css_rules as $max_w => $rules ) {
                $responsive_css_str .= '@media(max-width:' . esc_attr( $max_w ) . '){' . implode( ' ', $rules ) . '}';
            }
        }
        $scoped_css = trim( $hover_css_str . "\n" . $responsive_css_str );

        // Restore post context
        if ( $template_type === 'single' && $original_post !== null ) {
            global $post, $wp_query;
            $post = $original_post;
            if ( $post ) { setup_postdata( $post ); }
            if ( $wp_query ) {
                $wp_query->is_singular = false;
                $wp_query->is_single   = false;
            }
        }

        return rest_ensure_response( [
            'html'       => $html,
            'scoped_css' => $scoped_css,
        ] );
    }

    public function render_template_preview( $request ) {
        $id = absint( $request['id'] );
        if ( ! $id ) {
            return new WP_Error( 'invalid_id', 'ID template non valido', [ 'status' => 400 ] );
        }

        $db       = new Olobuild_Database();
        $template = $db->get_template( $id );
        if ( ! $template ) {
            return new WP_Error( 'not_found', 'Template non trovato', [ 'status' => 404 ] );
        }

        $tiles = $template['content'];
        if ( empty( $tiles ) || ! is_array( $tiles ) ) {
            return rest_ensure_response( [ 'html' => '', 'css' => [], 'inline_css' => '' ] );
        }

        // Use the frontend renderer to produce HTML
        $renderer = new Olobuild_Frontend_Renderer();
        ob_start();
        $renderer->render_tiles_array( $tiles, $template['settings'] ?? [] );
        $html = ob_get_clean();

        // Collect CSS needed for proper rendering
        $css_urls = [
            OLOBUILD_URL . 'assets/vendor/uikit/css/uikit.min.css',
            OLOBUILD_URL . 'assets/css/frontend.css?v=' . OLOBUILD_VERSION,
            OLOBUILD_URL . 'assets/css/olo-livesearch.css?v=' . OLOBUILD_VERSION,
        ];

        // Style System inline CSS (custom properties, fonts, etc.)
        $inline_css = '';
        if ( class_exists( 'Olobuild_Style_System' ) ) {
            $inline_css = Olobuild_Style_System::instance()->generate_css();
        }

        return rest_ensure_response( [
            'html'       => $html,
            'css'        => $css_urls,
            'inline_css' => $inline_css,
        ] );
    }

    /**
     * PostGrid preview — returns lightweight post data for the builder canvas.
     */
    public function get_postgrid_preview( $request ) {
        $post_type      = sanitize_key( $request->get_param( 'post_type' ) ?: 'post' );
        $posts_per_page = min( 50, absint( $request->get_param( 'posts_per_page' ) ?: 12 ) );
        $orderby        = sanitize_key( $request->get_param( 'orderby' ) ?: 'date' );
        $order          = strtoupper( $request->get_param( 'order' ) ?: 'DESC' ) === 'ASC' ? 'ASC' : 'DESC';
        $meta_key_param = sanitize_key( $request->get_param( 'meta_key' ) ?: '' );
        $meta_filter    = sanitize_text_field( $request->get_param( 'meta_filter' ) ?: '' );
        $excerpt_length = absint( $request->get_param( 'excerpt_length' ) ?: 20 );
        $price_field    = sanitize_key( $request->get_param( 'price_field' ) ?: '' );

        if ( ! post_type_exists( $post_type ) ) {
            return rest_ensure_response( [] );
        }

        $query_args = [
            'post_type'              => $post_type,
            'posts_per_page'         => $posts_per_page,
            'post_status'            => 'publish',
            'no_found_rows'          => true,
            'update_post_meta_cache' => true,
            'update_post_term_cache' => true,
            'orderby'                => $orderby,
            'order'                  => $order,
        ];

        if ( in_array( $orderby, [ 'meta_value_num', 'meta_value' ], true ) ) {
            if ( $meta_key_param ) {
                // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- preview PostGrid: ordinamento per meta necessario alla funzione della tile, volume limitato (max 50 post).
                $query_args['meta_key'] = $meta_key_param;
            }
        }

        if ( $meta_filter && str_contains( $meta_filter, '=' ) ) {
            list( $mf_key, $mf_val ) = array_map( 'trim', explode( '=', $meta_filter, 2 ) );
            if ( $mf_key && $mf_val ) {
                // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- preview PostGrid: filtro per meta necessario alla funzione della tile, volume limitato (max 50 post).
                $query_args['meta_query'] = [
                    [ 'key' => sanitize_key( $mf_key ), 'value' => sanitize_text_field( $mf_val ) ],
                ];
            }
        }

        $query = new WP_Query( $query_args );
        if ( ! $query->have_posts() ) {
            wp_reset_postdata();
            return rest_ensure_response( [] );
        }

        update_post_thumbnail_cache( $query );
        update_post_caches( $query->posts, $post_type, true, true );

        $results = [];
        foreach ( $query->posts as $post ) {
            $item = [
                'id'       => $post->ID,
                'title'    => get_the_title( $post ),
                'date_fmt' => get_the_date( '', $post ),
                'author'   => get_the_author_meta( 'display_name', $post->post_author ),
                'image'    => get_the_post_thumbnail_url( $post->ID, 'medium' ) ?: '',
            ];

            // Excerpt
            $item['excerpt'] = has_excerpt( $post->ID )
                ? wp_trim_words( get_the_excerpt( $post->ID ), $excerpt_length, '...' )
                : wp_trim_words( $post->post_content, $excerpt_length, '...' );

            // Category (first term of first taxonomy)
            $taxonomies = get_object_taxonomies( $post_type, 'names' );
            if ( ! empty( $taxonomies ) ) {
                $terms = get_the_terms( $post->ID, $taxonomies[0] );
                if ( $terms && ! is_wp_error( $terms ) ) {
                    $item['category'] = $terms[0]->name;
                }
            }

            // Price
            if ( $price_field ) {
                $pv = get_post_meta( $post->ID, $price_field, true );
                if ( $pv !== '' && $pv !== false ) {
                    $item['price'] = is_numeric( $pv ) ? floatval( $pv ) : $pv;
                }
            }

            // Service stats (olo_service)
            if ( $post_type === 'olo_service' ) {
                $item['service_capacity']  = get_post_meta( $post->ID, '_olo_service_capacity', true );
                $item['service_bedrooms']  = get_post_meta( $post->ID, '_olo_service_bedrooms', true );
                $item['service_bathrooms'] = get_post_meta( $post->ID, '_olo_service_bathrooms', true );
                $item['service_altitude']  = get_post_meta( $post->ID, '_olo_service_altitude', true );
                $item['service_club']      = get_post_meta( $post->ID, '_olo_service_club', true );
                $item['service_stars']     = get_post_meta( $post->ID, '_olo_service_stars', true );
                $opening = get_post_meta( $post->ID, '_olo_service_opening_type', true );
                if ( $opening ) {
                    $item['opening_type'] = $opening;
                }
            }

            $results[] = $item;
        }

        wp_reset_postdata();
        return rest_ensure_response( $results );
    }

    /* ════════════════════════════════════════════════════════════════
       TEMPLATE THUMBNAIL — auto-capture dal builder
       ════════════════════════════════════════════════════════════════ */

    /**
     * Riceve un file JPEG (binary) dal builder e lo salva come thumbnail
     * del template. Risponde con { thumbnail_url, thumbnail_path }.
     */
    public function upload_template_thumbnail( $request ) {
        $template_id = (int) $request->get_param( 'id' );
        if ( ! $template_id ) {
            return new WP_Error( 'olo_no_id', 'Template ID mancante', [ 'status' => 400 ] );
        }

        $files = $request->get_file_params();
        if ( empty( $files['file'] ) || empty( $files['file']['tmp_name'] ) ) {
            return new WP_Error( 'olo_no_file', 'Nessun file ricevuto', [ 'status' => 400 ] );
        }
        $f = $files['file'];

        // Valida MIME (solo immagini)
        $allowed = [ 'image/jpeg', 'image/png', 'image/webp' ];
        $info = function_exists( 'getimagesize' ) ? @getimagesize( $f['tmp_name'] ) : false;
        if ( ! $info || ! in_array( $info['mime'], $allowed, true ) ) {
            return new WP_Error( 'olo_bad_mime', 'Formato non supportato', [ 'status' => 415 ] );
        }
        if ( $f['size'] > 2 * 1024 * 1024 ) {
            return new WP_Error( 'olo_too_big', 'Thumbnail troppo grande (max 2MB)', [ 'status' => 413 ] );
        }

        // Salva in uploads/olobuild-thumbs/
        $uploads = wp_upload_dir();
        $dir = trailingslashit( $uploads['basedir'] ) . 'olobuild-thumbs';
        if ( ! file_exists( $dir ) ) {
            wp_mkdir_p( $dir );
            // Index per evitare directory listing
            @file_put_contents( $dir . '/index.html', '' );
        }
        if ( ! wp_is_writable( $dir ) ) {
            return new WP_Error( 'olo_not_writable', 'Cartella uploads non scrivibile', [ 'status' => 500 ] );
        }

        $ext = $info['mime'] === 'image/png' ? 'png' : ( $info['mime'] === 'image/webp' ? 'webp' : 'jpg' );
        $filename = 'template-' . $template_id . '-' . substr( md5( time() . wp_rand() ), 0, 6 ) . '.' . $ext;
        $dest = $dir . '/' . $filename;

        // is_uploaded_file() garantisce un upload HTTP legittimo; copy() sostituisce
        // move_uploaded_file() (vietato dal Plugin Check wp.org).
        if ( ! is_uploaded_file( $f['tmp_name'] ) || ! @copy( $f['tmp_name'], $dest ) ) {
            return new WP_Error( 'olo_move_failed', 'Salvataggio file fallito', [ 'status' => 500 ] );
        }
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_chmod -- set web-readable permissions on the freshly generated thumbnail in our own uploads subdir
        @chmod( $dest, 0644 );

        $url = trailingslashit( $uploads['baseurl'] ) . 'olobuild-thumbs/' . $filename;

        // Aggiorna campo `thumbnail` del template + cleanup vecchia thumb
        $db = new Olobuild_Database();
        $template = $db->get_template( $template_id );
        if ( ! $template ) {
            wp_delete_file( $dest );
            return new WP_Error( 'olo_no_template', 'Template non trovato', [ 'status' => 404 ] );
        }

        $old_url = $template['thumbnail'] ?? '';
        $db->update_template( $template_id, [ 'thumbnail' => $url ] );

        // Cleanup: rimuovi vecchia thumb solo se è nel nostro dir (no media library).
        // Hardening anti path-traversal: il path risolto (realpath) DEVE stare dentro
        // olobuild-thumbs/, così un campo `thumbnail` manipolato non puo' cancellare
        // file arbitrari fuori dalla cartella.
        if ( $old_url && strpos( $old_url, '/olobuild-thumbs/' ) !== false ) {
            $old_path   = str_replace( $uploads['baseurl'], $uploads['basedir'], $old_url );
            $thumbs_dir = realpath( trailingslashit( $uploads['basedir'] ) . 'olobuild-thumbs' );
            $real       = realpath( $old_path );
            if ( $thumbs_dir && $real && is_file( $real )
                && strpos( $real, $thumbs_dir . DIRECTORY_SEPARATOR ) === 0 ) {
                wp_delete_file( $real );
            }
        }

        // Invalida cache KPI/recent (così la dashboard mostra subito il nuovo thumb)
        delete_transient( 'olo_dashboard_kpis' );

        return rest_ensure_response( [
            'thumbnail_url' => $url,
            'template_id'   => $template_id,
        ] );
    }

    /* ════════════════════════════════════════════════════════════════
       DASHBOARD COCKPIT — KPI / Recent / Changelog / Preferenze utente
       ════════════════════════════════════════════════════════════════ */

}
