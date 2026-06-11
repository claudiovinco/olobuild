<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Rest_Api {

    private $namespace = 'olo/v1';

    public function init() {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
        // During REST requests, WP's determine_locale() returns the SITE locale,
        // not the user_locale. This breaks __() output for endpoints that return
        // translated strings to the JS dashboard. Switch to the logged-in user's
        // locale before dispatching any /olo/v1/ route.
        add_filter( 'rest_pre_dispatch', [ $this, 'apply_user_locale' ], 10, 3 );
    }

    /**
     * Switch to the logged-in user's locale for /olo/v1/ routes so that __()
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

        // Cursore magnetico globale (option olo_magnetic_cursor → Olo_Magnetic_Cursor)
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
            'permission_callback' => [ $this, 'check_dashboard_permission' ],
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
            'permission_callback' => [ $this, 'check_dashboard_permission' ],
        ] );
        register_rest_route( $this->namespace, '/submissions/(?P<id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'submissions_get' ],
                'permission_callback' => [ $this, 'check_dashboard_permission' ],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [ $this, 'submissions_delete' ],
                'permission_callback' => [ $this, 'check_dashboard_permission' ],
            ],
        ] );
        register_rest_route( $this->namespace, '/submissions/(?P<id>\d+)/read', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'submissions_toggle_read' ],
            'permission_callback' => [ $this, 'check_dashboard_permission' ],
        ] );
        register_rest_route( $this->namespace, '/submissions/bulk', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'submissions_bulk' ],
            'permission_callback' => [ $this, 'check_dashboard_permission' ],
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

    public function check_permission( $request = null ) {
        if ( ! current_user_can( 'edit_pages' ) ) {
            return false;
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

        $db       = new Olo_Database();
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
        if ( $any ) return (int) $any;
        $settings_post_id = isset( $template['settings']['post_id'] ) ? (int) $template['settings']['post_id'] : 0;
        return $settings_post_id ?: 0;
    }

    public function get_templates( $request ) {
        $db = new Olo_Database();
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
            $tpl_table = $wpdb->prefix . 'olo_templates';
            $by_type = [];
            $total = 0;
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
        $db = new Olo_Database();
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
        $db   = new Olo_Database();
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
            $db = new Olo_Database();
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
        $db       = new Olo_Database();
        $template = $db->get_template( (int) $request['id'] );

        if ( ! $template ) {
            return new WP_Error( 'not_found', 'Template non trovato.', [ 'status' => 404 ] );
        }

        return rest_ensure_response( $this->prepare_template( $template ) );
    }

    public function update_template( $request ) {
        $db   = new Olo_Database();
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
        $db = new Olo_Database();
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
        $manager = Olo_Tile_Manager::instance();
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
        $dc = new Olo_Dynamic_Content();
        return rest_ensure_response( $dc->get_available_sources() );
    }

    /**
     * Prodotti WooCommerce normalizzati per l'anteprima della tile productgrid.
     * Stessa logica del render frontend (Olo_ProductGrid_Tile::woo_items).
     */
    public function get_productgrid_products( $request ) {
        if ( ! class_exists( 'WooCommerce' ) || ! class_exists( 'Olo_ProductGrid_Tile' ) ) {
            return rest_ensure_response( [ 'woo' => false, 'items' => [] ] );
        }
        $items = Olo_ProductGrid_Tile::woo_items( [
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
        $style_system = Olo_Style_System::instance();
        return rest_ensure_response( [
            'styles' => $style_system->get_styles(),
            'css'    => $style_system->generate_css(),
        ] );
    }

    public function save_styles( $request ) {
        $body         = $request->get_json_params();
        $style_system = Olo_Style_System::instance();
        $saved        = $style_system->save_styles( $body );

        return rest_ensure_response( [
            'styles' => $style_system->get_styles(),
            'css'    => $style_system->generate_css(),
        ] );
    }

    public function reset_styles() {
        $style_system = Olo_Style_System::instance();
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

        $dc    = new Olo_Dynamic_Content();
        $value = $dc->resolve_field( $source, $field, $post_id );

        return rest_ensure_response( [
            'value'   => $value,
            'post_id' => $post_id,
        ] );
    }

    public function activate_header( $request ) {
        $body = $request->get_json_params();
        $id   = absint( $body['id'] ?? 0 );

        if ( ! $id ) {
            return new WP_Error( 'missing_id', 'ID template obbligatorio.', [ 'status' => 400 ] );
        }

        // Verify template exists and is of type header
        $db  = new Olo_Database();
        $tpl = $db->get_template( $id );
        if ( ! $tpl ) {
            return new WP_Error( 'not_found', 'Template non trovato.', [ 'status' => 404 ] );
        }

        update_option( 'olo_active_header', $id );

        return rest_ensure_response( [ 'active_header' => $id ] );
    }

    public function deactivate_header() {
        delete_option( 'olo_active_header' );
        return rest_ensure_response( [ 'active_header' => 0 ] );
    }

    public function get_active_header() {
        return rest_ensure_response( [
            'active_header' => (int) get_option( 'olo_active_header', 0 ),
        ] );
    }

    public function activate_footer( $request ) {
        $body = $request->get_json_params();
        $id   = absint( $body['id'] ?? 0 );

        if ( ! $id ) {
            return new WP_Error( 'missing_id', 'ID template obbligatorio.', [ 'status' => 400 ] );
        }

        // Verify template exists
        $db  = new Olo_Database();
        $tpl = $db->get_template( $id );
        if ( ! $tpl ) {
            return new WP_Error( 'not_found', 'Template non trovato.', [ 'status' => 404 ] );
        }

        update_option( 'olo_active_footer', $id );

        return rest_ensure_response( [ 'active_footer' => $id ] );
    }

    public function deactivate_footer() {
        delete_option( 'olo_active_footer' );
        return rest_ensure_response( [ 'active_footer' => 0 ] );
    }

    public function get_active_footer() {
        return rest_ensure_response( [
            'active_footer' => (int) get_option( 'olo_active_footer', 0 ),
        ] );
    }

    public function activate_single( $request ) {
        $body      = $request->get_json_params();
        $id        = absint( $body['id'] ?? 0 );
        $post_type = sanitize_key( $body['post_type'] ?? '' );

        if ( ! $id || ! $post_type ) {
            return new WP_Error( 'missing_params', 'Template ID and post_type are required.', [ 'status' => 400 ] );
        }

        $db  = new Olo_Database();
        $tpl = $db->get_template( $id );
        if ( ! $tpl ) {
            return new WP_Error( 'not_found', 'Template non trovato.', [ 'status' => 404 ] );
        }

        update_option( "olo_active_single_{$post_type}", $id );

        return rest_ensure_response( [ 'post_type' => $post_type, 'template_id' => $id ] );
    }

    public function deactivate_single( $request ) {
        $body      = $request->get_json_params();
        $post_type = sanitize_key( $body['post_type'] ?? '' );

        if ( ! $post_type ) {
            return new WP_Error( 'missing_params', 'post_type is required.', [ 'status' => 400 ] );
        }

        delete_option( "olo_active_single_{$post_type}" );

        return rest_ensure_response( [ 'post_type' => $post_type, 'template_id' => 0 ] );
    }

    public function get_active_singles() {
        $post_types = get_post_types( [ 'public' => true ], 'names' );
        $result     = [];

        foreach ( $post_types as $pt ) {
            if ( in_array( $pt, [ 'page', 'attachment' ], true ) ) continue;
            $tpl_id = (int) get_option( "olo_active_single_{$pt}", 0 );
            if ( $tpl_id ) {
                $result[ $pt ] = $tpl_id;
            }
        }

        return rest_ensure_response( $result );
    }

    // ─── Archive template activation ──────────────────────────────────

    public function activate_archive( $request ) {
        $body      = $request->get_json_params();
        $id        = absint( $body['id'] ?? 0 );
        $post_type = sanitize_key( $body['post_type'] ?? '' );

        if ( ! $id ) {
            return new WP_Error( 'missing_id', 'ID template obbligatorio.', [ 'status' => 400 ] );
        }

        $db  = new Olo_Database();
        $tpl = $db->get_template( $id );
        if ( ! $tpl ) {
            return new WP_Error( 'not_found', 'Template non trovato.', [ 'status' => 404 ] );
        }

        if ( $post_type ) {
            // Post-type-specific archive template
            update_option( "olo_active_archive_{$post_type}", $id );
            return rest_ensure_response( [ 'post_type' => $post_type, 'template_id' => $id ] );
        }

        // Generic archive template (fallback)
        update_option( 'olo_active_archive', $id );
        return rest_ensure_response( [ 'active_archive' => $id ] );
    }

    public function deactivate_archive( $request ) {
        $body      = $request->get_json_params();
        $post_type = sanitize_key( $body['post_type'] ?? '' );

        if ( $post_type ) {
            delete_option( "olo_active_archive_{$post_type}" );
            return rest_ensure_response( [ 'post_type' => $post_type, 'template_id' => 0 ] );
        }

        delete_option( 'olo_active_archive' );
        return rest_ensure_response( [ 'active_archive' => 0 ] );
    }

    public function get_active_archives() {
        $post_types = get_post_types( [ 'public' => true ], 'names' );
        $result     = [];

        // Generic archive fallback
        $generic = (int) get_option( 'olo_active_archive', 0 );
        if ( $generic ) {
            $result['_generic'] = $generic;
        }

        // Per-post-type archive templates
        foreach ( $post_types as $pt ) {
            if ( in_array( $pt, [ 'page', 'attachment' ], true ) ) continue;
            $tpl_id = (int) get_option( "olo_active_archive_{$pt}", 0 );
            if ( $tpl_id ) {
                $result[ $pt ] = $tpl_id;
            }
        }

        return rest_ensure_response( $result );
    }

    // ─── 404 template activation ──────────────────────────────────────

    public function activate_404( $request ) {
        $body = $request->get_json_params();
        $id   = absint( $body['id'] ?? 0 );

        if ( ! $id ) {
            return new WP_Error( 'missing_id', 'ID template obbligatorio.', [ 'status' => 400 ] );
        }

        $db  = new Olo_Database();
        $tpl = $db->get_template( $id );
        if ( ! $tpl ) {
            return new WP_Error( 'not_found', 'Template non trovato.', [ 'status' => 404 ] );
        }

        update_option( 'olo_active_404', $id );

        return rest_ensure_response( [ 'active_404' => $id ] );
    }

    public function deactivate_404() {
        delete_option( 'olo_active_404' );
        return rest_ensure_response( [ 'active_404' => 0 ] );
    }

    public function get_active_404() {
        return rest_ensure_response( [
            'active_404' => (int) get_option( 'olo_active_404', 0 ),
        ] );
    }

    // ─── Search results template activation ───────────────────────────

    public function activate_search( $request ) {
        $body = $request->get_json_params();
        $id   = absint( $body['id'] ?? 0 );

        if ( ! $id ) {
            return new WP_Error( 'missing_id', 'ID template obbligatorio.', [ 'status' => 400 ] );
        }

        $db  = new Olo_Database();
        $tpl = $db->get_template( $id );
        if ( ! $tpl ) {
            return new WP_Error( 'not_found', 'Template non trovato.', [ 'status' => 404 ] );
        }

        update_option( 'olo_active_search', $id );

        return rest_ensure_response( [ 'active_search' => $id ] );
    }

    public function deactivate_search() {
        delete_option( 'olo_active_search' );
        return rest_ensure_response( [ 'active_search' => 0 ] );
    }

    public function get_active_search() {
        return rest_ensure_response( [
            'active_search' => (int) get_option( 'olo_active_search', 0 ),
        ] );
    }

    public function export_template( $request ) {
        $db       = new Olo_Database();
        $template = $db->get_template( (int) $request['id'] );

        if ( ! $template ) {
            return new WP_Error( 'not_found', 'Template non trovato.', [ 'status' => 404 ] );
        }

        $slug = sanitize_title( $template['title'] ?: 'template' );

        $export = [
            'olo_export' => 'template',
            'version'       => OLO_VERSION,
            'title'         => $template['title'],
            'type'          => $template['type'] ?? 'page',
            'content'       => $template['content'] ?? [],
            'settings'      => $template['settings'] ?? new stdClass,
        ];

        $response = rest_ensure_response( $export );
        $response->header( 'Content-Disposition', 'attachment; filename="template-' . $slug . '.json"' );

        return $response;
    }

    public function import_template( $request ) {
        $body = $request->get_json_params();

        if ( empty( $body['olo_export'] ) || $body['olo_export'] !== 'template' ) {
            return new WP_Error( 'invalid_file', 'File non valido: non è un export Olobuild template.', [ 'status' => 400 ] );
        }

        // Validate content/settings on import too
        $import_content = $body['content'] ?? [];
        if ( ! is_array( $import_content ) ) {
            return new WP_Error( 'invalid_content', 'Il campo content deve essere un array.', [ 'status' => 400 ] );
        }
        $import_settings = $body['settings'] ?? [];
        if ( ! is_array( $import_settings ) && ! is_object( $import_settings ) ) {
            return new WP_Error( 'invalid_settings', 'Il campo settings deve essere un oggetto.', [ 'status' => 400 ] );
        }
        if ( is_object( $import_settings ) ) {
            $import_settings = (array) $import_settings;
        }

        $db = new Olo_Database();
        $id = $db->create_template( [
            'title'    => sanitize_text_field( $body['title'] ?? 'Importato' ),
            'type'     => sanitize_text_field( $body['type'] ?? 'page' ),
            'content'  => $import_content,
            'settings' => $import_settings,
            'status'   => 'draft',
        ] );

        if ( ! $id ) {
            return new WP_Error( 'import_failed', 'Impossibile importare il template.', [ 'status' => 500 ] );
        }

        $template = $db->get_template( $id );
        return rest_ensure_response( $this->prepare_template( $template ) );
    }

    /**
     * Esporta un BUNDLE (tema) = i template selezionati in un unico file JSON.
     * Body: { ids:[..], name, description }. L'header/footer/404 attivi vengono marcati
     * in `activate` tramite una chiave stabile, così l'import sa cosa riattivare.
     */
    public function export_bundle( $request ) {
        $body = $request->get_json_params();
        $ids  = ( isset( $body['ids'] ) && is_array( $body['ids'] ) ) ? array_values( array_unique( array_map( 'intval', $body['ids'] ) ) ) : [];
        if ( empty( $ids ) ) {
            return new WP_Error( 'no_ids', 'Nessun template selezionato.', [ 'status' => 400 ] );
        }

        $db            = new Olo_Database();
        $active_header = (int) get_option( 'olo_active_header', 0 );
        $active_footer = (int) get_option( 'olo_active_footer', 0 );
        $active_404    = (int) get_option( 'olo_active_404', 0 );

        $templates = [];
        $activate  = [];
        foreach ( $ids as $id ) {
            $t = $db->get_template( $id );
            if ( ! $t ) continue;
            $key = 'tpl_' . $id;
            $templates[] = [
                'key'      => $key,
                'title'    => $t['title'],
                'type'     => $t['type'] ?? 'page',
                'content'  => $t['content'] ?? [],
                'settings' => ! empty( $t['settings'] ) ? $t['settings'] : new stdClass,
            ];
            if ( $id === $active_header ) $activate['header'] = $key;
            if ( $id === $active_footer ) $activate['footer'] = $key;
            if ( $id === $active_404 )    $activate['404']    = $key;
        }
        if ( empty( $templates ) ) {
            return new WP_Error( 'not_found', 'Template non trovati.', [ 'status' => 404 ] );
        }

        $name   = sanitize_text_field( $body['name'] ?? 'Tema Olobuild' );
        $bundle = [
            'olo_export'  => 'theme-bundle',
            'version'     => OLO_VERSION,
            'name'        => $name,
            'description' => sanitize_text_field( $body['description'] ?? '' ),
            'activate'    => $activate,
            'templates'   => $templates,
        ];

        $response = rest_ensure_response( $bundle );
        $response->header( 'Content-Disposition', 'attachment; filename="tema-' . sanitize_title( $name ?: 'olobuild' ) . '.json"' );
        return $response;
    }

    /**
     * Importa un BUNDLE (tema): crea tutti i template come bozze e riattiva
     * header/footer/404 se il bundle li indica. NON crea pagine WP (i template
     * restano nel cockpit, pronti per essere assegnati o usati via shortcode).
     */
    public function import_bundle( $request ) {
        $body = $request->get_json_params();
        if ( empty( $body['olo_export'] ) || $body['olo_export'] !== 'theme-bundle' ) {
            return new WP_Error( 'invalid_file', 'File non valido: non è un tema Olobuild (theme-bundle).', [ 'status' => 400 ] );
        }
        $templates = ( isset( $body['templates'] ) && is_array( $body['templates'] ) ) ? $body['templates'] : [];
        if ( empty( $templates ) ) {
            return new WP_Error( 'empty', 'Il tema non contiene template.', [ 'status' => 400 ] );
        }

        $db      = new Olo_Database();
        $id_map  = [];
        $created = [];
        foreach ( $templates as $tpl ) {
            if ( ! is_array( $tpl ) ) continue;
            $content  = ( isset( $tpl['content'] ) && is_array( $tpl['content'] ) ) ? $tpl['content'] : [];
            $settings = $tpl['settings'] ?? [];
            if ( is_object( $settings ) ) $settings = (array) $settings;
            $new_id = $db->create_template( [
                'title'    => sanitize_text_field( $tpl['title'] ?? 'Importato' ),
                'type'     => sanitize_text_field( $tpl['type'] ?? 'page' ),
                'content'  => $content,
                'settings' => is_array( $settings ) ? $settings : [],
                'status'   => 'draft',
            ] );
            if ( $new_id ) {
                if ( ! empty( $tpl['key'] ) ) $id_map[ $tpl['key'] ] = $new_id;
                $created[] = [ 'id' => $new_id, 'title' => $tpl['title'] ?? '', 'type' => $tpl['type'] ?? 'page' ];
            }
        }

        $activated = [];
        $activate  = ( isset( $body['activate'] ) && is_array( $body['activate'] ) ) ? $body['activate'] : [];
        if ( ! empty( $activate['header'] ) && isset( $id_map[ $activate['header'] ] ) ) { update_option( 'olo_active_header', $id_map[ $activate['header'] ] ); $activated[] = 'header'; }
        if ( ! empty( $activate['footer'] ) && isset( $id_map[ $activate['footer'] ] ) ) { update_option( 'olo_active_footer', $id_map[ $activate['footer'] ] ); $activated[] = 'footer'; }
        if ( ! empty( $activate['404'] ) && isset( $id_map[ $activate['404'] ] ) )       { update_option( 'olo_active_404', $id_map[ $activate['404'] ] ); $activated[] = '404'; }

        return rest_ensure_response( [
            'success'   => true,
            'imported'  => count( $created ),
            'templates' => $created,
            'activated' => $activated,
        ] );
    }

    // ── Custom Fonts ────────────────────────────────────────

    public function get_fonts() {
        return new WP_REST_Response( Olo_Custom_Fonts::get_fonts(), 200 );
    }

    public function upload_font( $request ) {
        $name   = sanitize_text_field( $request->get_param( 'font_name' ) ?? '' );
        $weight = sanitize_text_field( $request->get_param( 'font_weight' ) ?? '400' );
        $style  = sanitize_text_field( $request->get_param( 'font_style' ) ?? 'normal' );

        if ( empty( $name ) ) {
            return new WP_REST_Response( [ 'message' => 'Nome font obbligatorio.' ], 400 );
        }

        if ( empty( $_FILES['font_file'] ) ) {
            return new WP_REST_Response( [ 'message' => 'Nessun file caricato.' ], 400 );
        }

        $url = Olo_Custom_Fonts::upload_font_file( $_FILES['font_file'] );
        if ( is_wp_error( $url ) ) {
            return new WP_REST_Response( [ 'message' => $url->get_error_message() ], 400 );
        }

        // Add or update font in the list
        $fonts = Olo_Custom_Fonts::get_fonts();
        $font_id = sanitize_title( $name );
        $found = false;
        foreach ( $fonts as &$f ) {
            if ( $f['id'] === $font_id ) {
                $f['variants'][] = [ 'weight' => $weight, 'style' => $style, 'file' => $url ];
                $found = true;
                break;
            }
        }
        unset( $f );

        if ( ! $found ) {
            $fonts[] = [
                'id'       => $font_id,
                'name'     => $name,
                'variants' => [ [ 'weight' => $weight, 'style' => $style, 'file' => $url ] ],
            ];
        }

        Olo_Custom_Fonts::save_fonts( $fonts );
        return new WP_REST_Response( [ 'success' => true, 'fonts' => $fonts ], 200 );
    }

    public function delete_font( $request ) {
        $id = sanitize_text_field( $request->get_param( 'id' ) );
        Olo_Custom_Fonts::delete_font( $id );
        return new WP_REST_Response( [ 'success' => true, 'fonts' => Olo_Custom_Fonts::get_fonts() ], 200 );
    }

    // === Custom Icons ===

    public function get_custom_icons() {
        $icons = get_option( 'olo_custom_icons', [] );
        return new WP_REST_Response( $icons, 200 );
    }

    public function upload_custom_icon( $request ) {
        $files = $request->get_file_params();
        $file = $files['file'] ?? null;
        if ( ! $file || $file['error'] ) {
            return new WP_Error( 'no_file', 'Nessun file caricato', [ 'status' => 400 ] );
        }
        $ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
        if ( $ext !== 'svg' ) {
            return new WP_Error( 'invalid_type', 'Solo file SVG sono supportati', [ 'status' => 400 ] );
        }
        $svg_content = file_get_contents( $file['tmp_name'] );
        // Robust SVG sanitization (XSS, XXE, SSRF prevention)
        $svg_content = olo_sanitize_svg( $svg_content );
        if ( empty( $svg_content ) ) {
            return new WP_Error( 'invalid_svg', 'Il file SVG non è valido o contiene elementi non sicuri', [ 'status' => 400 ] );
        }

        $name = sanitize_file_name( pathinfo( $file['name'], PATHINFO_FILENAME ) );
        $name = preg_replace( '/[^a-zA-Z0-9_-]/', '', $name );

        $icons = get_option( 'olo_custom_icons', [] );
        $icons[ $name ] = $svg_content;
        update_option( 'olo_custom_icons', $icons );

        return new WP_REST_Response( [ 'success' => true, 'name' => $name, 'svg' => $svg_content, 'icons' => $icons ], 200 );
    }

    public function delete_custom_icon( $request ) {
        $name = sanitize_text_field( $request->get_param( 'name' ) );
        $icons = get_option( 'olo_custom_icons', [] );
        unset( $icons[ $name ] );
        update_option( 'olo_custom_icons', $icons );
        return new WP_REST_Response( [ 'success' => true, 'icons' => $icons ], 200 );
    }

    // === Global Widgets ===

    public function get_global_widgets() {
        global $wpdb;
        $table = $wpdb->prefix . 'olo_global_widgets';
        // Safe: $table is constructed from $wpdb->prefix (not user input)
        $rows = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY name ASC", ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL
        return new WP_REST_Response( $rows ?: [], 200 );
    }

    public function create_global_widget( $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'olo_global_widgets';
        $name = sanitize_text_field( $request->get_param( 'name' ) ?? 'Widget globale' );
        $tile_data = $request->get_param( 'tile_data' );
        if ( ! is_string( $tile_data ) ) {
            $tile_data = wp_json_encode( $tile_data );
        }
        $wpdb->insert( $table, [
            'name'       => $name,
            'tile_data'  => $tile_data,
            'created_at' => current_time( 'mysql' ),
            'updated_at' => current_time( 'mysql' ),
        ], [ '%s', '%s', '%s', '%s' ] );
        $id = $wpdb->insert_id;
        return new WP_REST_Response( [ 'id' => $id, 'name' => $name ], 201 );
    }

    public function update_global_widget( $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'olo_global_widgets';
        $id = absint( $request->get_param( 'id' ) );
        $data = [];
        $formats = [];
        if ( $request->get_param( 'name' ) !== null ) {
            $data['name'] = sanitize_text_field( $request->get_param( 'name' ) );
            $formats[] = '%s';
        }
        if ( $request->get_param( 'tile_data' ) !== null ) {
            $td = $request->get_param( 'tile_data' );
            $data['tile_data'] = is_string( $td ) ? $td : wp_json_encode( $td );
            $formats[] = '%s';
        }
        if ( empty( $data ) ) {
            return new WP_REST_Response( [ 'message' => 'Nessun dato da aggiornare.' ], 400 );
        }
        $data['updated_at'] = current_time( 'mysql' );
        $formats[] = '%s';
        $wpdb->update( $table, $data, [ 'id' => $id ], $formats, [ '%d' ] );
        return new WP_REST_Response( [ 'success' => true ], 200 );
    }

    public function delete_global_widget( $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'olo_global_widgets';
        $id = absint( $request->get_param( 'id' ) );
        $wpdb->delete( $table, [ 'id' => $id ], [ '%d' ] );
        return new WP_REST_Response( [ 'success' => true ], 200 );
    }

    // === Global Colors ===

    public function get_global_colors() {
        $colors = get_option( 'olo_global_colors', [] );
        if ( ! is_array( $colors ) ) {
            $colors = [];
        }
        return rest_ensure_response( $colors );
    }

    public function save_global_colors( $request ) {
        $body = $request->get_json_params();

        if ( ! is_array( $body ) ) {
            return new WP_Error( 'invalid_data', 'I dati devono essere un array di colori.', [ 'status' => 400 ] );
        }

        $sanitized = [];
        foreach ( $body as $color ) {
            if ( ! is_array( $color ) || empty( $color['id'] ) ) {
                continue;
            }
            $entry = [
                'id'    => sanitize_key( $color['id'] ),
                'label' => sanitize_text_field( $color['label'] ?? '' ),
                'value' => sanitize_text_field( $color['value'] ?? '#000000' ),
            ];
            if ( ! empty( $color['quick'] ) ) {
                $entry['quick'] = true;
            }
            $sanitized[] = $entry;
        }

        update_option( 'olo_global_colors', $sanitized, false );

        return rest_ensure_response( $sanitized );
    }

    // === Global Typography ===

    public function get_global_typography() {
        $sets = get_option( 'olo_global_typography', [] );
        if ( ! is_array( $sets ) ) {
            $sets = [];
        }
        return rest_ensure_response( $sets );
    }

    public function save_global_typography( $request ) {
        $body = $request->get_json_params();

        if ( ! is_array( $body ) ) {
            return new WP_Error( 'invalid_data', 'I dati devono essere un array di set tipografici.', [ 'status' => 400 ] );
        }

        $sanitized = [];
        foreach ( $body as $set ) {
            if ( ! is_array( $set ) || empty( $set['id'] ) ) {
                continue;
            }
            $sanitized[] = [
                'id'             => sanitize_key( $set['id'] ),
                'label'          => sanitize_text_field( $set['label'] ?? '' ),
                'family'         => sanitize_text_field( $set['family'] ?? '' ),
                'weight'         => sanitize_text_field( $set['weight'] ?? '400' ),
                'transform'      => sanitize_text_field( $set['transform'] ?? 'none' ),
                'line_height'    => sanitize_text_field( $set['line_height'] ?? '1.5' ),
                'letter_spacing' => sanitize_text_field( $set['letter_spacing'] ?? '0' ),
            ];
        }

        update_option( 'olo_global_typography', $sanitized, false );

        return rest_ensure_response( $sanitized );
    }

    // === Stock Media (comportamento + chiavi API provider) ===

    public function get_stockmedia_behavior() {
        return rest_ensure_response( olo_stockmedia_behavior() );
    }

    public function save_stockmedia_behavior( $request ) {
        $b = $request->get_json_params();
        if ( ! is_array( $b ) ) {
            return new WP_Error( 'invalid_data', __( 'Dati non validi.', 'olobuild' ), [ 'status' => 400 ] );
        }
        $allowed = [ 'unsplash', 'pexels', 'pixabay', 'freesound' ];
        $pref    = sanitize_key( $b['preferred'] ?? 'unsplash' );
        if ( ! in_array( $pref, $allowed, true ) ) {
            $pref = 'unsplash';
        }
        $s = [
            'preferred'            => $pref,
            'download_local'       => ! empty( $b['download_local'] ),
            'optimize_on_download' => ! empty( $b['optimize_on_download'] ),
        ];
        update_option( 'olo_stockmedia_behavior', $s, false );
        return rest_ensure_response( $s );
    }

    public function get_api_keys() {
        return rest_ensure_response( [
            'olo_unsplash_api_key'  => (string) get_option( 'olo_unsplash_api_key', '' ),
            'olo_pexels_api_key'    => (string) get_option( 'olo_pexels_api_key', '' ),
            'olo_pixabay_api_key'   => (string) get_option( 'olo_pixabay_api_key', '' ),
            'olo_freesound_api_key' => (string) get_option( 'olo_freesound_api_key', '' ),
        ] );
    }

    public function save_api_keys( $request ) {
        $b    = $request->get_json_params();
        $keys = [ 'olo_unsplash_api_key', 'olo_pexels_api_key', 'olo_pixabay_api_key', 'olo_freesound_api_key' ];
        foreach ( $keys as $k ) {
            if ( is_array( $b ) && array_key_exists( $k, $b ) ) {
                update_option( $k, sanitize_text_field( $b[ $k ] ?? '' ), false );
            }
        }
        return $this->get_api_keys();
    }

    // === Cursore magnetico globale ===

    public function get_magnetic_cursor() {
        if ( ! class_exists( 'Olo_Magnetic_Cursor' ) ) {
            require_once OLO_PATH . 'includes/class-magnetic-cursor.php';
        }
        return rest_ensure_response( Olo_Magnetic_Cursor::get_settings() );
    }

    public function save_magnetic_cursor( $request ) {
        if ( ! class_exists( 'Olo_Magnetic_Cursor' ) ) {
            require_once OLO_PATH . 'includes/class-magnetic-cursor.php';
        }
        $b = $request->get_json_params();
        if ( ! is_array( $b ) ) {
            return new WP_Error( 'invalid_data', __( 'Dati non validi.', 'olobuild' ), [ 'status' => 400 ] );
        }
        // Merge sull'esistente: il pannello può inviare anche solo un sottoinsieme
        // di chiavi (es. il toggle enabled) senza azzerare le altre.
        $merged = array_merge( Olo_Magnetic_Cursor::get_settings(), $b );
        $clean  = Olo_Magnetic_Cursor::sanitize( $merged );
        update_option( Olo_Magnetic_Cursor::OPT, $clean, false );
        return rest_ensure_response( $clean );
    }

    // === Custom Code Snippets ===

    public function get_custom_code() {
        return rest_ensure_response( [
            'head'   => get_option( 'olo_custom_code_head', '' ),
            'body'   => get_option( 'olo_custom_code_body', '' ),
            'footer' => get_option( 'olo_custom_code_footer', '' ),
        ] );
    }

    public function save_custom_code( $request ) {
        // Difesa in profondità: gli snippet sono emessi raw nel frontend.
        if ( ! current_user_can( 'unfiltered_html' ) ) {
            return new WP_Error( 'olo_forbidden', __( 'Permessi insufficienti per salvare codice personalizzato.', 'olobuild' ), [ 'status' => 403 ] );
        }

        $body = $request->get_json_params();

        if ( isset( $body['head'] ) ) {
            update_option( 'olo_custom_code_head', $body['head'], false );
        }
        if ( isset( $body['body'] ) ) {
            update_option( 'olo_custom_code_body', $body['body'], false );
        }
        if ( isset( $body['footer'] ) ) {
            update_option( 'olo_custom_code_footer', $body['footer'], false );
        }

        return rest_ensure_response( [
            'head'   => get_option( 'olo_custom_code_head', '' ),
            'body'   => get_option( 'olo_custom_code_body', '' ),
            'footer' => get_option( 'olo_custom_code_footer', '' ),
        ] );
    }

    // === Maintenance Mode ===

    public function get_maintenance() {
        return rest_ensure_response( [
            'mode'                   => get_option( 'olo_maintenance_mode', 'off' ),
            'template_id'            => (int) get_option( 'olo_maintenance_template_id', 0 ),
            'coming_soon_template_id' => (int) get_option( 'olo_coming_soon_template_id', 0 ),
            'bypass_roles'           => get_option( 'olo_maintenance_bypass_roles', [ 'administrator' ] ),
            'bypass_secret'          => get_option( 'olo_maintenance_bypass_secret', '' ),
        ] );
    }

    public function save_maintenance( $request ) {
        $body = $request->get_json_params();

        if ( isset( $body['mode'] ) ) {
            $allowed = [ 'off', 'maintenance', 'coming_soon' ];
            $mode = in_array( $body['mode'], $allowed, true ) ? $body['mode'] : 'off';
            update_option( 'olo_maintenance_mode', $mode, false );
        }

        if ( isset( $body['template_id'] ) ) {
            update_option( 'olo_maintenance_template_id', absint( $body['template_id'] ), false );
        }

        if ( isset( $body['coming_soon_template_id'] ) ) {
            update_option( 'olo_coming_soon_template_id', absint( $body['coming_soon_template_id'] ), false );
        }

        if ( isset( $body['bypass_roles'] ) ) {
            $roles = [];
            if ( is_array( $body['bypass_roles'] ) ) {
                foreach ( $body['bypass_roles'] as $role ) {
                    $roles[] = sanitize_key( $role );
                }
            }
            update_option( 'olo_maintenance_bypass_roles', $roles, false );
        }

        if ( isset( $body['bypass_secret'] ) ) {
            update_option( 'olo_maintenance_bypass_secret', sanitize_text_field( $body['bypass_secret'] ), false );
        }

        return rest_ensure_response( [
            'mode'                   => get_option( 'olo_maintenance_mode', 'off' ),
            'template_id'            => (int) get_option( 'olo_maintenance_template_id', 0 ),
            'coming_soon_template_id' => (int) get_option( 'olo_coming_soon_template_id', 0 ),
            'bypass_roles'           => get_option( 'olo_maintenance_bypass_roles', [ 'administrator' ] ),
            'bypass_secret'          => get_option( 'olo_maintenance_bypass_secret', '' ),
        ] );
    }

    // === Revisions ===

    public function get_revisions( $request ) {
        $db  = new Olo_Database();
        $id  = (int) $request['id'];
        $tpl = $db->get_template( $id );

        if ( ! $tpl ) {
            return new WP_Error( 'not_found', 'Template non trovato.', [ 'status' => 404 ] );
        }

        $revisions = $db->get_revisions( $id, 50 );

        return rest_ensure_response( $revisions );
    }

    public function get_revision( $request ) {
        $db  = new Olo_Database();
        $rev = $db->get_revision( (int) $request['rev_id'] );

        if ( ! $rev ) {
            return new WP_Error( 'not_found', 'Revisione non trovata.', [ 'status' => 404 ] );
        }

        return rest_ensure_response( $rev );
    }

    // === Themes ===

    public function get_themes() {
        require_once OLO_PATH . 'includes/class-theme-importer.php';
        return rest_ensure_response( Olo_Theme_Importer::get_themes() );
    }

    public function import_theme( $request ) {
        require_once OLO_PATH . 'includes/class-theme-importer.php';
        $result = Olo_Theme_Importer::import_theme( $request['theme_id'] );
        if ( is_wp_error( $result ) ) return $result;
        return rest_ensure_response( $result );
    }

    // === Design Presets ===

    public function get_design_presets() {
        $presets = get_option( 'olo_design_presets', [] );
        if ( ! is_array( $presets ) ) {
            $presets = [];
        }
        return rest_ensure_response( $presets );
    }

    public function create_design_preset( $request ) {
        $body = $request->get_json_params();
        $name = sanitize_text_field( $body['name'] ?? 'Preset' );
        $style = $body['style'] ?? [];

        if ( ! is_array( $style ) ) {
            return new WP_Error( 'invalid_style', 'Lo stile deve essere un oggetto.', [ 'status' => 400 ] );
        }

        $presets = get_option( 'olo_design_presets', [] );
        if ( ! is_array( $presets ) ) {
            $presets = [];
        }

        $new_preset = [
            'id'         => 'dp-' . wp_rand( 10000, 99999 ) . '-' . time(),
            'name'       => $name,
            'style'      => $style,
            'created_at' => current_time( 'mysql' ),
        ];

        $presets[] = $new_preset;
        update_option( 'olo_design_presets', $presets, false );

        return rest_ensure_response( $new_preset );
    }

    public function update_design_preset( $request ) {
        $id   = sanitize_text_field( $request['id'] );
        $body = $request->get_json_params();

        $presets = get_option( 'olo_design_presets', [] );
        if ( ! is_array( $presets ) ) {
            $presets = [];
        }

        $found = false;
        foreach ( $presets as &$preset ) {
            if ( $preset['id'] === $id ) {
                if ( isset( $body['name'] ) ) {
                    $preset['name'] = sanitize_text_field( $body['name'] );
                }
                if ( isset( $body['style'] ) ) {
                    if ( is_array( $body['style'] ) ) {
                        $preset['style'] = $body['style'];
                    }
                }
                $found = true;
                break;
            }
        }
        unset( $preset );

        if ( ! $found ) {
            return new WP_Error( 'not_found', 'Preset non trovato.', [ 'status' => 404 ] );
        }

        update_option( 'olo_design_presets', $presets, false );

        return rest_ensure_response( [ 'success' => true ] );
    }

    public function delete_design_preset( $request ) {
        $id = sanitize_text_field( $request['id'] );

        $presets = get_option( 'olo_design_presets', [] );
        if ( ! is_array( $presets ) ) {
            $presets = [];
        }

        $new_presets = [];
        $found = false;
        foreach ( $presets as $preset ) {
            if ( $preset['id'] === $id ) {
                $found = true;
                continue;
            }
            $new_presets[] = $preset;
        }

        if ( ! $found ) {
            return new WP_Error( 'not_found', 'Preset non trovato.', [ 'status' => 404 ] );
        }

        update_option( 'olo_design_presets', $new_presets, false );

        return rest_ensure_response( [ 'success' => true ] );
    }

    // === Template Library ===

    public function get_template_library( $request ) {
        $lib = Olo_Template_Library::instance();
        $category = sanitize_text_field( $request->get_param( 'category' ) ?? '' );
        $templates = $lib->get_all_templates();
        if ( $category ) {
            $templates = array_values( array_filter( $templates, function( $t ) use ( $category ) {
                return ( $t['category'] ?? '' ) === $category;
            } ) );
        }
        // Strip heavy content for listing (send only metadata)
        $list = array_map( function( $t ) {
            return [
                'id'                  => $t['id'] ?? '',
                'name'                => $t['name'] ?? '',
                'category'            => $t['category'] ?? '',
                'preview_description' => $t['preview_description'] ?? '',
                'is_user'             => ! empty( $t['is_user'] ),
            ];
        }, $templates );
        return rest_ensure_response( $list );
    }

    public function get_library_template( $request ) {
        $id  = sanitize_text_field( $request['id'] );
        $lib = Olo_Template_Library::instance();
        // Check built-in first
        $tpl = $lib->get_template( $id );
        if ( ! $tpl ) {
            // Check user templates
            $user = get_option( 'olo_user_templates', [] );
            foreach ( (array) $user as $u ) {
                if ( ( $u['id'] ?? '' ) === $id ) {
                    $tpl = $u;
                    break;
                }
            }
        }
        if ( ! $tpl ) {
            return new WP_Error( 'not_found', 'Template non trovato.', [ 'status' => 404 ] );
        }
        return rest_ensure_response( $tpl );
    }

    public function save_user_template( $request ) {
        $body = $request->get_json_params();
        $name     = sanitize_text_field( $body['name'] ?? '' );
        $category = sanitize_text_field( $body['category'] ?? 'custom' );
        $content  = $body['content'] ?? [];
        if ( empty( $name ) || empty( $content ) ) {
            return new WP_Error( 'invalid', 'Nome e contenuto richiesti.', [ 'status' => 400 ] );
        }
        $lib = Olo_Template_Library::instance();
        $id  = $lib->save_user_template( $name, $category, $content );
        return rest_ensure_response( [ 'id' => $id, 'success' => true ] );
    }

    public function delete_user_template( $request ) {
        $id  = sanitize_text_field( $request['id'] );
        $lib = Olo_Template_Library::instance();
        $ok  = $lib->delete_user_template( $id );
        if ( ! $ok ) {
            return new WP_Error( 'not_found', 'Template non trovato.', [ 'status' => 404 ] );
        }
        return rest_ensure_response( [ 'success' => true ] );
    }

    // === Built-in Design Presets ===

    public function get_builtin_presets() {
        $style_system = Olo_Style_System::instance();
        return rest_ensure_response( $style_system->get_presets() );
    }

    // === Design Tokens Export ===

    public function export_design_tokens() {
        $style_system = Olo_Style_System::instance();
        $styles = $style_system->get_styles();

        $tokens = [
            'version' => '1.0',
            'colors' => [
                'primary'   => $styles['color_primary'] ?? '#6366F1',
                'secondary' => $styles['color_secondary'] ?? '#8B5CF6',
                'success'   => $styles['color_success'] ?? '#22C55E',
                'warning'   => $styles['color_warning'] ?? '#F59E0B',
                'danger'    => $styles['color_danger'] ?? '#EF4444',
                'muted'     => $styles['color_muted'] ?? '#F3F4F6',
                'emphasis'  => $styles['color_emphasis'] ?? '#111827',
                'text'      => $styles['color_text'] ?? '#374151',
            ],
            'typography' => [
                'font_body'    => $styles['font_body'] ?? 'Inter',
                'font_heading' => $styles['font_heading'] ?? 'Inter',
                'font_size'    => ( $styles['font_size'] ?? '16' ) . 'px',
                'line_height'  => $styles['line_height'] ?? '1.6',
                'h1_size'      => ( $styles['h1_size'] ?? '40' ) . 'px',
                'h2_size'      => ( $styles['h2_size'] ?? '32' ) . 'px',
                'h3_size'      => ( $styles['h3_size'] ?? '24' ) . 'px',
                'h4_size'      => ( $styles['h4_size'] ?? '20' ) . 'px',
            ],
            'spacing' => [
                'global_gap' => ( $styles['global_gap'] ?? '30' ) . 'px',
            ],
            'borders' => [
                'radius' => ( $styles['border_radius'] ?? '4' ) . 'px',
            ],
            'css_custom_properties' => $this->generate_tokens_css( $styles ),
        ];

        // Global colors
        $global_colors = get_option( 'olo_global_colors', [] );
        if ( ! empty( $global_colors ) && is_array( $global_colors ) ) {
            $tokens['global_colors'] = $global_colors;
        }

        return rest_ensure_response( $tokens );
    }

    private function generate_tokens_css( $styles ) {
        $lines = [ ':root {' ];
        $map = [
            '--olo-color-primary'   => $styles['color_primary'] ?? '#6366F1',
            '--olo-color-secondary' => $styles['color_secondary'] ?? '#8B5CF6',
            '--olo-color-success'   => $styles['color_success'] ?? '#22C55E',
            '--olo-color-warning'   => $styles['color_warning'] ?? '#F59E0B',
            '--olo-color-danger'    => $styles['color_danger'] ?? '#EF4444',
            '--olo-color-text'      => $styles['color_text'] ?? '#374151',
            '--olo-color-muted'     => $styles['color_muted'] ?? '#F3F4F6',
            '--olo-font-body'       => $styles['font_body'] ?? 'Inter',
            '--olo-font-heading'    => $styles['font_heading'] ?? 'Inter',
            '--olo-font-size'       => ( $styles['font_size'] ?? '16' ) . 'px',
            '--olo-border-radius'   => ( $styles['border_radius'] ?? '4' ) . 'px',
        ];
        foreach ( $map as $prop => $val ) {
            $lines[] = "  {$prop}: {$val};";
        }
        $lines[] = '}';
        return implode( "\n", $lines );
    }

    // === Analytics Settings ===

    public function get_analytics() {
        return rest_ensure_response( [
            'ga_measurement_id' => get_option( 'olo_ga_measurement_id', '' ),
            'fb_pixel_id'       => get_option( 'olo_fb_pixel_id', '' ),
            'gtm_container_id'  => get_option( 'olo_gtm_container_id', '' ),
        ] );
    }

    public function save_analytics( $request ) {
        $body = $request->get_json_params();

        if ( isset( $body['ga_measurement_id'] ) ) {
            update_option( 'olo_ga_measurement_id', sanitize_text_field( $body['ga_measurement_id'] ), false );
        }
        if ( isset( $body['fb_pixel_id'] ) ) {
            update_option( 'olo_fb_pixel_id', sanitize_text_field( $body['fb_pixel_id'] ), false );
        }
        if ( isset( $body['gtm_container_id'] ) ) {
            update_option( 'olo_gtm_container_id', sanitize_text_field( $body['gtm_container_id'] ), false );
        }

        return rest_ensure_response( [
            'ga_measurement_id' => get_option( 'olo_ga_measurement_id', '' ),
            'fb_pixel_id'       => get_option( 'olo_fb_pixel_id', '' ),
            'gtm_container_id'  => get_option( 'olo_gtm_container_id', '' ),
        ] );
    }

    // === Critical CSS ===

    public function generate_critical_css( $request ) {
        $body    = $request->get_json_params();
        $post_id = absint( $body['post_id'] ?? 0 );

        if ( ! $post_id ) {
            return new WP_Error( 'missing_post_id', 'post_id obbligatorio.', [ 'status' => 400 ] );
        }

        if ( ! class_exists( 'Olo_Critical_CSS' ) ) {
            return new WP_Error( 'not_available', 'Critical CSS non disponibile.', [ 'status' => 500 ] );
        }

        $css = Olo_Critical_CSS::generate_critical_css( $post_id );

        return rest_ensure_response( [
            'post_id' => $post_id,
            'css'     => $css,
            'size'    => strlen( $css ),
        ] );
    }

    public function regenerate_all_critical_css() {
        if ( ! class_exists( 'Olo_Critical_CSS' ) ) {
            return new WP_Error( 'not_available', 'Critical CSS non disponibile.', [ 'status' => 500 ] );
        }

        $result = Olo_Critical_CSS::regenerate_all();

        return rest_ensure_response( $result );
    }

    public function purge_critical_css() {
        if ( ! class_exists( 'Olo_Critical_CSS' ) ) {
            return new WP_Error( 'not_available', 'Critical CSS non disponibile.', [ 'status' => 500 ] );
        }

        $purged = Olo_Critical_CSS::purge_all();

        return rest_ensure_response( [ 'purged' => $purged ] );
    }

    public function get_critical_css_status() {
        if ( ! class_exists( 'Olo_Critical_CSS' ) ) {
            return rest_ensure_response( [ 'enabled' => false, 'cached_count' => 0 ] );
        }

        return rest_ensure_response( Olo_Critical_CSS::get_status() );
    }

    /**
     * Export all Olobuild site data as JSON.
     */
    public function site_export() {
        if ( ! class_exists( 'Olo_Site_Export' ) ) {
            require_once OLO_PATH . 'includes/class-site-export.php';
        }

        $data = Olo_Site_Export::export_site();

        return rest_ensure_response( $data );
    }

    /**
     * Import Olobuild site data from JSON body.
     */
    public function site_import( $request ) {
        if ( ! class_exists( 'Olo_Site_Export' ) ) {
            require_once OLO_PATH . 'includes/class-site-export.php';
        }

        $body = $request->get_json_params();
        if ( empty( $body ) ) {
            return new WP_Error( 'invalid_data', 'Empty or invalid JSON body.', [ 'status' => 400 ] );
        }

        $result = Olo_Site_Export::import_site( $body );

        return rest_ensure_response( $result );
    }

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
                'html'       => Olo_Builder::get_iframe_empty_html(),
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

        $renderer = new Olo_Frontend_Renderer();
        $renderer->builder_mode = true;

        $parts = [];

        // Resolve per-zone page_bg (so the .olo-template wrapper paints the same bg as in render_shortcode)
        $header_bg = $body['header_page_bg'] ?? null;
        $body_bg   = $page_settings['page_bg'] ?? null;
        $footer_bg = $body['footer_page_bg'] ?? null;
        $css_builder = class_exists( 'Olo_CSS_Builder' ) ? new Olo_CSS_Builder() : null;
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
            $parts[] = '<main data-olo-zone="body">' . Olo_Builder::get_iframe_empty_html() . '</main>';
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
            OLO_URL . 'assets/vendor/uikit/css/uikit.min.css',
            OLO_URL . 'assets/css/frontend.css?v=' . OLO_VERSION,
            OLO_URL . 'assets/css/olo-proslider.css?v=' . OLO_VERSION,
        ];

        $inline_css = '';
        if ( class_exists( 'Olo_Style_System' ) ) {
            $inline_css = Olo_Style_System::instance()->generate_css();
        }

        // Estendi inline_css con il page background della body zone, applicato a html+body
        // dell'iframe builder. Senza questo, il bg vive solo dentro `.olo-template` (che ha
        // max-width limitata) e i bordi laterali dell'iframe restano del colore di default
        // del tema. UX-wise l'utente si aspetta che il bg pagina riempia l'intera area canvas.
        // !important perché altri inline_css generati da Olo_Style_System potrebbero settare
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

        $db = new Olo_Database();
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

        $renderer = new Olo_Frontend_Renderer();
        $loop_query = $renderer->run_row_loop_query( $s, $page, true );
        $hover_css_rules = [];
        $tile_counter = 0;
        $manager = class_exists( 'Olo_Tile_Manager' ) ? new Olo_Tile_Manager() : null;

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

        $renderer = new Olo_Frontend_Renderer();
        $renderer->builder_mode = true;

        $renderer->breakpoints = wp_parse_args( $page_settings['breakpoints'] ?? [], [
            'widescreen'       => 1400,
            'tablet_landscape' => 1200,
            'tablet'           => 960,
            'mobile_landscape' => 640,
            'mobile'           => 480,
        ] );

        $manager = Olo_Tile_Manager::instance();
        $hover_css = [];
        // counter_hint - 1: prima dell'increment in render_*_node, così che ++$counter
        // produca esattamente l'hint passato dal client (= ID del nodo già nel DOM).
        $counter = max( 0, $counter_hint - 1 );

        ob_start();
        echo $renderer->render_node_public( $tile, $manager, $template_id, $hover_css, $counter ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- builder-preview HTML generated by Olo_Frontend_Renderer::render_node_public(); each tile escapes its own output, captured into the REST response buffer
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

        $db       = new Olo_Database();
        $template = $db->get_template( $id );
        if ( ! $template ) {
            return new WP_Error( 'not_found', 'Template non trovato', [ 'status' => 404 ] );
        }

        $tiles = $template['content'];
        if ( empty( $tiles ) || ! is_array( $tiles ) ) {
            return rest_ensure_response( [ 'html' => '', 'css' => [], 'inline_css' => '' ] );
        }

        // Use the frontend renderer to produce HTML
        $renderer = new Olo_Frontend_Renderer();
        ob_start();
        $renderer->render_tiles_array( $tiles, $template['settings'] ?? [] );
        $html = ob_get_clean();

        // Collect CSS needed for proper rendering
        $css_urls = [
            OLO_URL . 'assets/vendor/uikit/css/uikit.min.css',
            OLO_URL . 'assets/css/frontend.css?v=' . OLO_VERSION,
            OLO_URL . 'assets/css/olo-livesearch.css?v=' . OLO_VERSION,
        ];

        // Style System inline CSS (custom properties, fonts, etc.)
        $inline_css = '';
        if ( class_exists( 'Olo_Style_System' ) ) {
            $inline_css = Olo_Style_System::instance()->generate_css();
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
                $query_args['meta_key'] = $meta_key_param;
            }
        }

        if ( $meta_filter && str_contains( $meta_filter, '=' ) ) {
            list( $mf_key, $mf_val ) = array_map( 'trim', explode( '=', $meta_filter, 2 ) );
            if ( $mf_key && $mf_val ) {
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
        if ( ! is_writable( $dir ) ) {
            return new WP_Error( 'olo_not_writable', 'Cartella uploads non scrivibile', [ 'status' => 500 ] );
        }

        $ext = $info['mime'] === 'image/png' ? 'png' : ( $info['mime'] === 'image/webp' ? 'webp' : 'jpg' );
        $filename = 'template-' . $template_id . '-' . substr( md5( time() . wp_rand() ), 0, 6 ) . '.' . $ext;
        $dest = $dir . '/' . $filename;

        if ( ! @move_uploaded_file( $f['tmp_name'], $dest ) ) {
            // Fallback per ambienti dove move_uploaded_file fallisce (CLI/test)
            if ( ! @copy( $f['tmp_name'], $dest ) ) {
                return new WP_Error( 'olo_move_failed', 'Salvataggio file fallito', [ 'status' => 500 ] );
            }
        }
        @chmod( $dest, 0644 );

        $url = trailingslashit( $uploads['baseurl'] ) . 'olobuild-thumbs/' . $filename;

        // Aggiorna campo `thumbnail` del template + cleanup vecchia thumb
        $db = new Olo_Database();
        $template = $db->get_template( $template_id );
        if ( ! $template ) {
            @unlink( $dest );
            return new WP_Error( 'olo_no_template', 'Template non trovato', [ 'status' => 404 ] );
        }

        $old_url = $template['thumbnail'] ?? '';
        $db->update_template( $template_id, [ 'thumbnail' => $url ] );

        // Cleanup: rimuovi vecchia thumb solo se è nel nostro dir (no media library)
        if ( $old_url && strpos( $old_url, '/olobuild-thumbs/' ) !== false ) {
            $old_path = str_replace( $uploads['baseurl'], $uploads['basedir'], $old_url );
            if ( file_exists( $old_path ) ) @unlink( $old_path );
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

    /**
     * KPI strip: 4 metriche aggregate, cached 5 minuti via transient.
     */
    public function dashboard_kpis( $request ) {
        // Include locale in cache key — labels are translated via __() and the
        // result is cached, so each locale needs its own cached payload.
        $cache_key = 'olo_dashboard_kpis_' . determine_locale();
        $cached = get_transient( $cache_key );
        if ( $cached !== false ) {
            return rest_ensure_response( $cached );
        }

        global $wpdb;

        // Pagine pubblicate
        $pages_published = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->posts}
             WHERE post_type = 'page' AND post_status = 'publish'"
        );
        $pages_recent = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts}
             WHERE post_type = 'page' AND post_status = 'publish'
             AND post_date_gmt >= %s",
            gmdate( 'Y-m-d H:i:s', strtotime( '-7 days' ) )
        ) );

        // Template Olobuild
        $tpl_table = $wpdb->prefix . 'olo_templates';
        $tpl_total = 0;
        $tpl_draft = 0;
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$tpl_table'" ) === $tpl_table ) {
            $tpl_total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $tpl_table WHERE status = 'published'" );
            $tpl_draft = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $tpl_table WHERE status = 'draft'" );
        }

        // Invii form ultimi 7gg (se la tabella esiste)
        $sub_table = $wpdb->prefix . 'olo_form_submissions';
        $form_7d = 0;
        $form_prev = 0;
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$sub_table'" ) === $sub_table ) {
            $form_7d = (int) $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM $sub_table WHERE created_at >= %s",
                gmdate( 'Y-m-d H:i:s', strtotime( '-7 days' ) )
            ) );
            $form_prev = (int) $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM $sub_table WHERE created_at >= %s AND created_at < %s",
                gmdate( 'Y-m-d H:i:s', strtotime( '-14 days' ) ),
                gmdate( 'Y-m-d H:i:s', strtotime( '-7 days' ) )
            ) );
        }
        $form_delta_pct = $form_prev > 0 ? round( ( ( $form_7d - $form_prev ) / $form_prev ) * 100 ) : 0;

        // Avvisi: redirect 404 + revisioni in bozza + ecc.
        $alerts_404   = 0;
        $alerts_break = 0;
        $tools_404 = $wpdb->prefix . 'olo_404_log';
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$tools_404'" ) === $tools_404 ) {
            $alerts_404 = (int) $wpdb->get_var(
                "SELECT COUNT(*) FROM $tools_404 WHERE handled = 0"
            );
        }
        $alerts_total = $alerts_404 + $tpl_draft;

        $kpis = [
            [
                'label' => __( 'Pagine pubblicate', 'olobuild' ),
                'value' => $pages_published,
                'delta' => $pages_recent > 0
                    ? sprintf( _n( '+%d questa settimana', '+%d questa settimana', $pages_recent, 'olobuild' ), $pages_recent )
                    : __( 'nessuna nuova', 'olobuild' ),
                'trend' => $pages_recent > 0 ? 'up' : 'flat',
                'icon'  => 'fileText',
                'href'  => admin_url( 'edit.php?post_type=page' ),
            ],
            [
                'label' => __( 'Template attivi', 'olobuild' ),
                'value' => $tpl_total,
                'delta' => $tpl_draft > 0
                    ? sprintf( _n( '%d in bozza', '%d in bozza', $tpl_draft, 'olobuild' ), $tpl_draft )
                    : __( 'tutti pubblicati', 'olobuild' ),
                'trend' => 'flat',
                'icon'  => 'template',
                'href'  => admin_url( 'admin.php?page=olobuilder-templates' ),
            ],
            [
                'label' => __( 'Invii form (7gg)', 'olobuild' ),
                'value' => $form_7d,
                'delta' => $form_prev > 0
                    ? sprintf( '%s%d%% vs scorsa', $form_delta_pct >= 0 ? '+' : '', $form_delta_pct )
                    : __( 'periodo iniziale', 'olobuild' ),
                'trend' => $form_delta_pct > 0 ? 'up' : ( $form_delta_pct < 0 ? 'warn' : 'flat' ),
                'icon'  => 'form',
                'href'  => admin_url( 'admin.php?page=olo-form-submissions' ),
            ],
            [
                'label' => __( 'Avvisi da risolvere', 'olobuild' ),
                'value' => $alerts_total,
                'delta' => $alerts_total > 0
                    ? sprintf( '%d 404 · %d bozze', $alerts_404, $tpl_draft )
                    : __( 'tutto a posto', 'olobuild' ),
                'trend' => $alerts_total > 0 ? 'warn' : 'up',
                'icon'  => 'alert',
                'href'  => $alerts_404 > 0 ? admin_url( 'admin.php?page=olo-redirects' ) : admin_url( 'admin.php?page=olobuilder-templates' ),
            ],
        ];

        set_transient( $cache_key, $kpis, 5 * MINUTE_IN_SECONDS );
        return rest_ensure_response( $kpis );
    }

    /**
     * Recent: ultime N modifiche tra pagine + template Olobuild.
     */
    public function dashboard_recent( $request ) {
        $limit = max( 1, min( 20, (int) $request->get_param( 'limit' ) ) );
        global $wpdb;
        $items = [];

        // Ultime pagine modificate. Thumbnail in ordine: template Olobuild associato →
        // featured image della pagina → gradient fallback.
        $page_query = new WP_Query( [
            'post_type'      => [ 'page', 'post' ],
            'post_status'    => [ 'publish', 'draft' ],
            'posts_per_page' => $limit,
            'orderby'        => 'modified',
            'order'          => 'DESC',
            'no_found_rows'  => true,
        ] );
        $tpl_table = $wpdb->prefix . 'olo_templates';
        $tpl_table_exists = ( $wpdb->get_var( "SHOW TABLES LIKE '$tpl_table'" ) === $tpl_table );
        foreach ( $page_query->posts as $p ) {
            $thumb_url = '';
            // 1. Template Olobuild associato
            $tpl_id = (int) get_post_meta( $p->ID, '_olo_template_id', true );
            if ( $tpl_id && $tpl_table_exists ) {
                $thumb_url = (string) $wpdb->get_var( $wpdb->prepare(
                    "SELECT thumbnail FROM $tpl_table WHERE id = %d", $tpl_id
                ) );
            }
            // 2. Featured image
            if ( ! $thumb_url ) {
                $thumb_id = get_post_thumbnail_id( $p->ID );
                $thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'medium' ) : '';
            }
            $items[] = [
                'id'         => 'p' . $p->ID,
                'title'      => $p->post_title ?: __( '(senza titolo)', 'olobuild' ),
                'type'       => $p->post_type === 'post' ? __( 'Articolo', 'olobuild' ) : __( 'Pagina', 'olobuild' ),
                'time'       => self::human_time_ago( $p->post_modified_gmt, $p->post_modified ),
                'time_iso'   => $p->post_modified_gmt,
                'thumb'      => $thumb_url,
                'thumb_grad' => self::get_color_gradient_for( $p->ID ),
                'status'     => $p->post_status === 'publish' ? 'live' : 'draft',
                // Card "Pagina" → editor WP (scelta deliberata: le card "Template"
                // qui sotto aprono il builder; due tipologie, due destinazioni).
                'href'       => admin_url( 'post.php?post=' . $p->ID . '&action=edit' ),
            ];
        }

        // Ultimi template Olobuild
        if ( $tpl_table_exists ) {
            $tpls = $wpdb->get_results( $wpdb->prepare(
                "SELECT id, title, type, status, updated_at, thumbnail
                 FROM $tpl_table
                 ORDER BY updated_at DESC
                 LIMIT %d",
                $limit
            ), ARRAY_A );
            foreach ( $tpls as $t ) {
                $type_label = ucfirst( $t['type'] ?: 'template' );
                $items[] = [
                    'id'         => 't' . $t['id'],
                    'title'      => $t['title'] ?: __( '(senza titolo)', 'olobuild' ),
                    'type'       => __( 'Template', 'olobuild' ) . ' · ' . $type_label,
                    'time'       => self::human_time_ago( $t['updated_at'] ),
                    'time_iso'   => $t['updated_at'],
                    'thumb'      => $t['thumbnail'] ?: '',
                    'thumb_grad' => self::get_color_gradient_for( (int) $t['id'] + 1000 ),
                    'status'     => $t['status'] === 'published' ? 'live' : 'draft',
                    'href'       => admin_url( 'admin.php?page=olobuilder-templates&template_id=' . $t['id'] ),
                ];
            }
        }

        // Sort by time desc + cap to limit
        usort( $items, function( $a, $b ) {
            return strcmp( $b['time_iso'], $a['time_iso'] );
        } );
        $items = array_slice( $items, 0, $limit );

        return rest_ensure_response( $items );
    }

    /**
     * "X fa" robusto: le bozze WP possono avere datetime zero ('0000-00-00 00:00:00'),
     * che strtotime interpreta come anno 0 → "2028 anni fa". Qui: timestamp invalido,
     * negativo o futuro → fallback sulla seconda data, poi su "poco fa".
     *
     * @param string $mysql_gmt    Datetime MySQL preferito (GMT).
     * @param string $fallback_gmt Datetime di riserva (es. post_modified locale).
     * @return string
     */
    public static function human_time_ago( $mysql_gmt, $fallback_gmt = '' ) {
        $ts = $mysql_gmt ? strtotime( $mysql_gmt ) : false;
        if ( ! $ts || $ts <= 0 ) {
            $ts = $fallback_gmt ? strtotime( $fallback_gmt ) : false;
        }
        if ( ! $ts || $ts <= 0 || $ts > time() + MINUTE_IN_SECONDS ) {
            return __( 'poco fa', 'olobuild' );
        }
        return human_time_diff( $ts, time() ) . ' ' . __( 'fa', 'olobuild' );
    }

    /**
     * Genera un gradiente CSS deterministico in base all'ID per fallback thumb.
     */
    private static function get_color_gradient_for( $seed ) {
        $palettes = [
            [ '#a7d7f9', '#79b8e8' ],
            [ '#fde68a', '#f59e0b' ],
            [ '#bbf7d0', '#4a8c2a' ],
            [ '#fecaca', '#ef4444' ],
            [ '#e9d5ff', '#a855f7' ],
            [ '#cffafe', '#06b6d4' ],
            [ '#fed7aa', '#f97316' ],
            [ '#bfdbfe', '#3b82f6' ],
        ];
        $p = $palettes[ abs( crc32( (string) $seed ) ) % count( $palettes ) ];
        return 'linear-gradient(135deg,' . $p[0] . ',' . $p[1] . ')';
    }

    /**
     * Changelog: ultime N versioni del plugin (recent commit / readme).
     * Per ora lettura statica da array hardcoded — TODO: leggere da CHANGELOG.md.
     */
    public function dashboard_changelog( $request ) {
        $limit = max( 1, min( 10, (int) $request->get_param( 'limit' ) ) );

        // Lettura del CHANGELOG.md se esiste
        $changelog_file = OLO_PATH . 'CHANGELOG.md';
        $entries = [];
        if ( file_exists( $changelog_file ) ) {
            $entries = self::parse_changelog_md( $changelog_file, $limit );
        }

        // Fallback: ultima versione dall'header del plugin
        if ( empty( $entries ) ) {
            $entries = [ [
                'v'     => 'v' . OLO_VERSION,
                'date'  => date_i18n( 'j M', time() ),
                'tag'   => 'novità',
                'items' => [ __( 'Vedi changelog completo nel repository.', 'olobuild' ) ],
            ] ];
        }

        return rest_ensure_response( $entries );
    }

    private static function parse_changelog_md( $file, $limit ) {
        $content = @file_get_contents( $file );
        if ( ! $content ) return [];
        $lines = explode( "\n", $content );
        $entries = [];
        $current = null;
        foreach ( $lines as $line ) {
            // ## v3.34.6 — 2026-05-09 (novità)
            if ( preg_match( '/^##\s+(v[\d.]+)(?:\s*[—\-]\s*([\d-]+))?(?:\s*\(([^)]+)\))?/', $line, $m ) ) {
                if ( $current ) $entries[] = $current;
                if ( count( $entries ) >= $limit ) break;
                $current = [
                    'v'     => $m[1],
                    'date'  => ! empty( $m[2] ) ? date_i18n( 'j M', strtotime( $m[2] ) ) : '',
                    'tag'   => ! empty( $m[3] ) ? strtolower( trim( $m[3] ) ) : 'novità',
                    'items' => [],
                ];
            } elseif ( $current && preg_match( '/^[\-\*]\s+(.+)/', $line, $m ) ) {
                $current['items'][] = trim( $m[1] );
            }
        }
        if ( $current && count( $entries ) < $limit ) $entries[] = $current;
        return $entries;
    }

    /**
     * Preferenze utente per la dashboard (pin tile, rail collapsed, app mode).
     * Persiste in user-meta.
     */
    public function dashboard_get_prefs( $request ) {
        $user_id = get_current_user_id();
        $prefs = get_user_meta( $user_id, 'olo_dashboard_prefs', true );
        if ( ! is_array( $prefs ) ) $prefs = [];
        return rest_ensure_response( wp_parse_args( $prefs, [
            'pinned'      => [ 'tpl', 'cfg', 'media' ],
            'rail'        => 'expanded',
            'app_mode'    => true,
            'banners_off' => [],
        ] ) );
    }

    public function dashboard_set_prefs( $request ) {
        $user_id = get_current_user_id();
        $body = $request->get_json_params();
        if ( ! is_array( $body ) ) $body = [];

        $existing = get_user_meta( $user_id, 'olo_dashboard_prefs', true );
        if ( ! is_array( $existing ) ) $existing = [];

        // Merge solo dei campi noti
        $allowed = [ 'pinned', 'rail', 'app_mode', 'banners_off' ];
        foreach ( $allowed as $k ) {
            if ( array_key_exists( $k, $body ) ) {
                $existing[ $k ] = $body[ $k ];
            }
        }
        update_user_meta( $user_id, 'olo_dashboard_prefs', $existing );
        return rest_ensure_response( $existing );
    }

    /* ════════════════════════════════════════════════════════════════
       FORM SUBMISSIONS — list / detail / read / delete / bulk / stats
       ════════════════════════════════════════════════════════════════ */

    private function submissions_table() {
        global $wpdb;
        return $wpdb->prefix . 'olo_form_submissions';
    }

    /**
     * Lista submissions con filtri (status, form_name, q) + paginazione.
     */
    public function submissions_list( $request ) {
        global $wpdb;
        $tab = $this->submissions_table();
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$tab'" ) !== $tab ) {
            return rest_ensure_response( [ 'items' => [], 'total' => 0, 'page' => 1, 'per_page' => 30 ] );
        }

        $status    = $request->get_param( 'status' );
        $form_name = $request->get_param( 'form_name' );
        $q         = $request->get_param( 'q' );
        $page      = max( 1, (int) $request->get_param( 'page' ) );
        $per_page  = max( 1, min( 100, (int) $request->get_param( 'per_page' ) ) );
        $offset    = ( $page - 1 ) * $per_page;

        $where = [ '1=1' ];
        $params = [];
        if ( $status === 'unread' ) $where[] = 'read_status = 0';
        elseif ( $status === 'read' ) $where[] = 'read_status = 1';
        if ( $form_name ) {
            $where[] = 'form_name = %s';
            $params[] = $form_name;
        }
        if ( $q ) {
            $where[] = '(fields_data LIKE %s OR ip_address LIKE %s OR form_name LIKE %s)';
            $like = '%' . $wpdb->esc_like( $q ) . '%';
            $params[] = $like; $params[] = $like; $params[] = $like;
        }
        $where_sql = implode( ' AND ', $where );

        $count_sql = "SELECT COUNT(*) FROM $tab WHERE $where_sql";
        $list_sql  = "SELECT id, form_name, fields_data, submitted_at, ip_address, read_status
                      FROM $tab WHERE $where_sql
                      ORDER BY submitted_at DESC LIMIT %d OFFSET %d";

        $total = (int) $wpdb->get_var( $params ? $wpdb->prepare( $count_sql, $params ) : $count_sql );
        $list_params = array_merge( $params, [ $per_page, $offset ] );
        $rows = $wpdb->get_results( $wpdb->prepare( $list_sql, $list_params ), ARRAY_A );

        $items = array_map( [ $this, 'prepare_submission_summary' ], $rows ?: [] );

        return rest_ensure_response( [
            'items'    => $items,
            'total'    => $total,
            'page'     => $page,
            'per_page' => $per_page,
        ] );
    }

    /**
     * Stats aggregate per KPI strip + chip filters dinamici.
     */
    public function submissions_stats( $request ) {
        global $wpdb;
        $tab = $this->submissions_table();
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$tab'" ) !== $tab ) {
            return rest_ensure_response( [
                'total' => 0, 'unread' => 0, 'read' => 0, 'last_7d' => 0,
                'forms' => [],
            ] );
        }

        $total   = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $tab" );
        $unread  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $tab WHERE read_status = 0" );
        $last_7d = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM $tab WHERE submitted_at >= %s",
            gmdate( 'Y-m-d H:i:s', strtotime( '-7 days' ) )
        ) );
        $prev_7d = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM $tab WHERE submitted_at >= %s AND submitted_at < %s",
            gmdate( 'Y-m-d H:i:s', strtotime( '-14 days' ) ),
            gmdate( 'Y-m-d H:i:s', strtotime( '-7 days' ) )
        ) );

        // Counter per form_name (top 10)
        $forms = $wpdb->get_results(
            "SELECT form_name, COUNT(*) AS n FROM $tab
             GROUP BY form_name ORDER BY n DESC LIMIT 10",
            ARRAY_A
        );
        $forms = array_map( function( $r ) {
            return [
                'name'  => $r['form_name'] ?: '(senza nome)',
                'count' => (int) $r['n'],
            ];
        }, $forms ?: [] );

        return rest_ensure_response( [
            'total'   => $total,
            'unread'  => $unread,
            'read'    => $total - $unread,
            'last_7d' => $last_7d,
            'prev_7d' => $prev_7d,
            'forms'   => $forms,
        ] );
    }

    /**
     * Dettaglio singola submission. Auto-mark as read alla GET.
     */
    public function submissions_get( $request ) {
        global $wpdb;
        $tab = $this->submissions_table();
        $id = (int) $request['id'];
        $row = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $tab WHERE id = %d", $id
        ), ARRAY_A );
        if ( ! $row ) {
            return new WP_Error( 'not_found', __( 'Invio non trovato', 'olobuild' ), [ 'status' => 404 ] );
        }
        // Auto-mark read
        if ( ! $row['read_status'] ) {
            $wpdb->update( $tab, [ 'read_status' => 1 ], [ 'id' => $id ], [ '%d' ], [ '%d' ] );
            $row['read_status'] = 1;
        }
        $fields = json_decode( $row['fields_data'], true );
        if ( ! is_array( $fields ) ) $fields = [];
        return rest_ensure_response( [
            'id'           => (int) $row['id'],
            'form_name'    => $row['form_name'],
            'fields'       => $fields,
            'submitted_at' => $row['submitted_at'],
            'ip_address'   => $row['ip_address'],
            'user_agent'   => $row['user_agent'],
            'read_status'  => (int) $row['read_status'],
        ] );
    }

    /**
     * Toggle read status (POST con body {read: 0|1}, default toggle).
     */
    public function submissions_toggle_read( $request ) {
        global $wpdb;
        $tab = $this->submissions_table();
        $id = (int) $request['id'];
        $body = $request->get_json_params();
        $row = $wpdb->get_row( $wpdb->prepare(
            "SELECT read_status FROM $tab WHERE id = %d", $id
        ), ARRAY_A );
        if ( ! $row ) {
            return new WP_Error( 'not_found', __( 'Invio non trovato', 'olobuild' ), [ 'status' => 404 ] );
        }
        $new = isset( $body['read'] ) ? (int) (bool) $body['read'] : ( $row['read_status'] ? 0 : 1 );
        $wpdb->update( $tab, [ 'read_status' => $new ], [ 'id' => $id ], [ '%d' ], [ '%d' ] );
        return rest_ensure_response( [ 'id' => $id, 'read_status' => $new ] );
    }

    public function submissions_delete( $request ) {
        global $wpdb;
        $tab = $this->submissions_table();
        $id = (int) $request['id'];
        $wpdb->delete( $tab, [ 'id' => $id ], [ '%d' ] );
        return rest_ensure_response( [ 'id' => $id, 'deleted' => true ] );
    }

    /**
     * Bulk action su molti id contemporaneamente.
     * Body: { action: 'delete'|'mark_read'|'mark_unread', ids: [1,2,3] }
     */
    public function submissions_bulk( $request ) {
        global $wpdb;
        $tab = $this->submissions_table();
        $body = $request->get_json_params();
        $action = $body['action'] ?? '';
        $ids = isset( $body['ids'] ) && is_array( $body['ids'] ) ? array_map( 'absint', $body['ids'] ) : [];
        $ids = array_filter( $ids );
        if ( empty( $ids ) ) {
            return new WP_Error( 'no_ids', __( 'Nessun ID selezionato', 'olobuild' ), [ 'status' => 400 ] );
        }
        $placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
        if ( $action === 'delete' ) {
            $wpdb->query( $wpdb->prepare( "DELETE FROM $tab WHERE id IN ($placeholders)", $ids ) );
        } elseif ( $action === 'mark_read' ) {
            $wpdb->query( $wpdb->prepare( "UPDATE $tab SET read_status = 1 WHERE id IN ($placeholders)", $ids ) );
        } elseif ( $action === 'mark_unread' ) {
            $wpdb->query( $wpdb->prepare( "UPDATE $tab SET read_status = 0 WHERE id IN ($placeholders)", $ids ) );
        } else {
            return new WP_Error( 'bad_action', __( 'Azione non valida', 'olobuild' ), [ 'status' => 400 ] );
        }
        return rest_ensure_response( [ 'action' => $action, 'count' => count( $ids ) ] );
    }

    /**
     * Prepara il summary di una submission per la lista (preview campi top).
     */
    private function prepare_submission_summary( $row ) {
        $fields = json_decode( $row['fields_data'] ?? '{}', true );
        if ( ! is_array( $fields ) ) $fields = [];

        // Estrae i campi più rappresentativi: name, email, message → preview
        $name    = '';
        $email   = '';
        $preview = '';
        foreach ( $fields as $k => $v ) {
            $kl = strtolower( $k );
            if ( ! $name && in_array( $kl, [ 'name', 'nome', 'fullname', 'full_name' ], true ) ) {
                $name = is_array( $v ) ? implode( ' ', $v ) : $v;
            } elseif ( ! $email && ( $kl === 'email' || strpos( $kl, 'mail' ) !== false ) ) {
                $email = is_array( $v ) ? reset( $v ) : $v;
            } elseif ( ! $preview && in_array( $kl, [ 'message', 'messaggio', 'note', 'comment', 'comments', 'body', 'testo', 'description' ], true ) ) {
                $preview = is_array( $v ) ? implode( ' ', $v ) : $v;
            }
        }

        // Fallback: se non c'è preview, prendi il primo campo testuale lungo > 20
        if ( ! $preview ) {
            foreach ( $fields as $v ) {
                if ( is_string( $v ) && strlen( $v ) > 20 ) { $preview = $v; break; }
            }
        }

        return [
            'id'           => (int) $row['id'],
            'form_name'    => $row['form_name'] ?: '(senza nome)',
            'name'         => mb_strimwidth( (string) $name, 0, 60, '…' ),
            'email'        => $email,
            'preview'      => mb_strimwidth( wp_strip_all_tags( (string) $preview ), 0, 140, '…' ),
            'fields_count' => count( $fields ),
            'submitted_at' => $row['submitted_at'],
            'time_diff'    => self::human_time_ago( $row['submitted_at'] ),
            'ip_address'   => $row['ip_address'],
            'read_status'  => (int) $row['read_status'],
        ];
    }

    /**
     * Cerca contenuti linkabili del sito (pagine, post, CPT pubblici, tassonomie)
     * per popolare l'autocomplete del FieldLink nel builder.
     *
     * Senza query: restituisce le ultime N pagine pubblicate (lista iniziale utile).
     * Con query: cerca per titolo/slug su tutti i post type pubblici + termini tassonomie.
     */
    public function link_search( $request ) {
        $q        = trim( (string) $request->get_param( 'q' ) );
        $per_page = max( 1, min( 30, absint( $request->get_param( 'per_page' ) ) ?: 15 ) );
        $types    = trim( (string) $request->get_param( 'types' ) );

        // Post types ammessi: tutti i pubblici esclusi attachment + tipi interni Olobuild.
        $public_types = get_post_types( [ 'public' => true ], 'objects' );
        unset( $public_types['attachment'] );
        if ( isset( $public_types['olo_template'] ) ) unset( $public_types['olo_template'] );
        if ( isset( $public_types['olo_global_widget'] ) ) unset( $public_types['olo_global_widget'] );

        // Filtro opzionale per types=page,post,product
        if ( $types ) {
            $allowed = array_filter( array_map( 'sanitize_key', explode( ',', $types ) ) );
            $public_types = array_intersect_key( $public_types, array_flip( $allowed ) );
        }

        $post_type_keys = array_keys( $public_types );
        $results = [];

        // 1. Query post/page/CPT
        if ( ! empty( $post_type_keys ) ) {
            $args = [
                'post_type'        => $post_type_keys,
                'post_status'      => 'publish',
                'posts_per_page'   => $per_page,
                'no_found_rows'    => true,
                'suppress_filters' => true,
                'orderby'          => $q ? 'relevance' : 'modified',
                'order'            => 'DESC',
            ];
            if ( $q !== '' ) {
                $args['s'] = $q;
            }
            $query = new WP_Query( $args );
            foreach ( $query->posts as $p ) {
                $pt_obj    = $public_types[ $p->post_type ] ?? null;
                $type_lbl  = $pt_obj ? ( $pt_obj->labels->singular_name ?: $pt_obj->label ) : $p->post_type;
                $thumb_id  = get_post_thumbnail_id( $p->ID );
                $thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'thumbnail' ) : '';
                $excerpt   = $p->post_excerpt ?: wp_strip_all_tags( $p->post_content );
                $excerpt   = mb_strimwidth( trim( preg_replace( '/\s+/', ' ', $excerpt ) ), 0, 110, '…' );

                $permalink = get_permalink( $p );
                $results[] = [
                    'id'           => (int) $p->ID,
                    'title'        => $p->post_title ?: __( '(senza titolo)', 'olobuild' ),
                    'url'          => $permalink,
                    'url_relative' => wp_make_link_relative( $permalink ),
                    'type'         => 'post',
                    'subtype'      => $p->post_type,
                    'type_label'   => $type_lbl,
                    'sublabel'     => $type_lbl,
                    'thumbnail'    => $thumb_url ?: '',
                    'excerpt'      => $excerpt,
                ];
            }
        }

        // 2. Tassonomie pubbliche (categorie, tag, custom): solo se c'è una query.
        if ( $q !== '' && count( $results ) < $per_page ) {
            $public_taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );
            $tax_keys = array_keys( $public_taxonomies );
            if ( ! empty( $tax_keys ) ) {
                $terms = get_terms( [
                    'taxonomy'   => $tax_keys,
                    'search'     => $q,
                    'number'     => $per_page - count( $results ),
                    'hide_empty' => false,
                ] );
                if ( ! is_wp_error( $terms ) ) {
                    foreach ( $terms as $term ) {
                        $tx_obj   = $public_taxonomies[ $term->taxonomy ] ?? null;
                        $type_lbl = $tx_obj ? ( $tx_obj->labels->singular_name ?: $tx_obj->label ) : $term->taxonomy;
                        $link     = get_term_link( $term );
                        if ( is_wp_error( $link ) ) continue;

                        $results[] = [
                            'id'           => (int) $term->term_id,
                            'title'        => $term->name,
                            'url'          => $link,
                            'url_relative' => wp_make_link_relative( $link ),
                            'type'         => 'term',
                            'subtype'      => $term->taxonomy,
                            'type_label'   => $type_lbl,
                            'sublabel'     => $type_lbl . ' · ' . ( $term->count ) . ' ' . __( 'voci', 'olobuild' ),
                            'thumbnail'    => '',
                            'excerpt'      => mb_strimwidth( wp_strip_all_tags( (string) $term->description ), 0, 110, '…' ),
                        ];
                    }
                }
            }
        }

        // 3. Sempre disponibili: scorciatoie semantiche (homepage, ecc.)
        if ( $q === '' || stripos( __( 'Homepage', 'olobuild' ), $q ) !== false || stripos( 'home', $q ) !== false ) {
            array_unshift( $results, [
                'id'           => 0,
                'title'        => __( 'Homepage', 'olobuild' ),
                'url'          => home_url( '/' ),
                'url_relative' => '/',
                'type'         => 'shortcut',
                'subtype'      => 'home',
                'type_label'   => __( 'Homepage', 'olobuild' ),
                'sublabel'     => home_url( '/' ),
                'thumbnail'    => '',
                'excerpt'      => '',
            ] );
        }

        return rest_ensure_response( [
            'query'   => $q,
            'count'   => count( $results ),
            'results' => $results,
        ] );
    }
}
