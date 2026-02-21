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
    }

    public function check_permission() {
        return current_user_can( 'edit_posts' );
    }

    /**
     * Ensure settings is always a JSON object (not array) in REST responses.
     * PHP json_decode('{}', true) returns [], which json_encode turns back to [].
     * JS treats [] as Array, losing non-indexed properties on stringify.
     */
    private function prepare_template( $template ) {
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
            $result['items'] = array_map( [ $this, 'prepare_template' ], $result['items'] );
        }
        return rest_ensure_response( $result );
    }

    public function create_template( $request ) {
        $db   = new Olo_Database();
        $body = $request->get_json_params();

        $id = $db->create_template( [
            'title'    => sanitize_text_field( $body['title'] ?? 'Senza titolo' ),
            'type'     => sanitize_text_field( $body['type'] ?? 'page' ),
            'content'  => $body['content'] ?? [],
            'settings' => $body['settings'] ?? [],
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
            $update_data['content'] = $body['content'];
        }
        if ( isset( $body['settings'] ) ) {
            $update_data['settings'] = $body['settings'];
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

        $db = new Olo_Database();
        $id = $db->create_template( [
            'title'    => sanitize_text_field( $body['title'] ?? 'Importato' ),
            'type'     => sanitize_text_field( $body['type'] ?? 'page' ),
            'content'  => $body['content'] ?? [],
            'settings' => $body['settings'] ?? [],
            'status'   => 'draft',
        ] );

        if ( ! $id ) {
            return new WP_Error( 'import_failed', 'Impossibile importare il template.', [ 'status' => 500 ] );
        }

        $template = $db->get_template( $id );
        return rest_ensure_response( $this->prepare_template( $template ) );
    }
}
