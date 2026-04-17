<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Rest_Api {

    private $namespace = 'olo/v1';

    public function init() {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
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

        // Single template
        register_rest_route( $this->namespace, '/templates/(?P<id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_template' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'update_template' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [ $this, 'delete_template' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
        ] );

        // Template export
        register_rest_route( $this->namespace, '/templates/(?P<id>\d+)/export', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'export_template' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );

        // Template import
        register_rest_route( $this->namespace, '/templates/import', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'import_template' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );

        // Template revisions
        register_rest_route( $this->namespace, '/templates/(?P<id>\d+)/revisions', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_revisions' ],
            'permission_callback' => [ $this, 'check_permission' ],
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
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
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
            'permission_callback' => [ $this, 'check_permission' ],
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
     * Ensure settings is always a JSON object (not array) in REST responses.
     * PHP json_decode('{}', true) returns [], which json_encode turns back to [].
     * JS treats [] as Array, losing non-indexed properties on stringify.
     */
    private function prepare_template( $template ) {
        if ( isset( $template['id'] ) ) {
            $template['id'] = (int) $template['id'];
        }
        if ( isset( $template['settings'] ) && is_array( $template['settings'] ) && empty( $template['settings'] ) ) {
            $template['settings'] = new stdClass;
        }
        return $template;
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
        }
        return rest_ensure_response( $result );
    }

    public function create_template( $request ) {
        $db   = new Olo_Database();
        $body = $request->get_json_params();

        // Validate content — must be an array (list of sections/rows)
        $content = $body['content'] ?? [];
        if ( ! is_array( $content ) ) {
            return new WP_Error( 'invalid_content', 'Il campo content deve essere un array.', [ 'status' => 400 ] );
        }

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

        $template = $db->get_template( $id );
        return rest_ensure_response( $this->prepare_template( $template ) );
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
            $update_data['content'] = $body['content'];
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
        $template = $db->get_template( $id );

        return rest_ensure_response( $this->prepare_template( $template ) );
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

    // === Custom Code Snippets ===

    public function get_custom_code() {
        return rest_ensure_response( [
            'head'   => get_option( 'olo_custom_code_head', '' ),
            'body'   => get_option( 'olo_custom_code_body', '' ),
            'footer' => get_option( 'olo_custom_code_footer', '' ),
        ] );
    }

    public function save_custom_code( $request ) {
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
            return rest_ensure_response( [ 'html' => '', 'css' => [], 'inline_css' => '' ] );
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

        // Header
        if ( ! empty( $header_tiles ) && is_array( $header_tiles ) ) {
            ob_start();
            echo '<header class="olo-site-header olo-header-classic" data-olo-zone="header">';
            $renderer->render_tiles_array( $header_tiles, $page_settings );
            echo '</header>';
            $parts[] = ob_get_clean();
        }

        // Body
        if ( ! empty( $tiles ) && is_array( $tiles ) ) {
            ob_start();
            echo '<main data-olo-zone="body">';
            $renderer->render_tiles_array( $tiles, $page_settings );
            echo '</main>';
            $parts[] = ob_get_clean();
        }

        // Footer
        if ( ! empty( $footer_tiles ) && is_array( $footer_tiles ) ) {
            ob_start();
            echo '<footer class="olo-site-footer" data-olo-zone="footer">';
            $renderer->render_tiles_array( $footer_tiles, $page_settings );
            echo '</footer>';
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
    public function builder_render_tile( $request ) {
        $body = $request->get_json_params();
        $tile = $body['tile'] ?? null;
        $page_settings = $body['page_settings'] ?? [];
        $template_type = $body['template_type'] ?? 'page';

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
        $counter = 0;

        ob_start();
        echo $renderer->render_node_public( $tile, $manager, 0, $hover_css, $counter );
        $html = ob_get_clean();

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

        return rest_ensure_response( [ 'html' => $html ] );
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
}
