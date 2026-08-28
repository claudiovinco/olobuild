<?php
/**
 * Olobuild_Rest_Activation_Trait — attivazione header/footer/single/archive/404/search + export/import template e bundle.
 *
 * Estratto verbatim da class-rest-api.php (dieta monoliti v1.4.390):
 * stessi metodi, stessa visibilita', zero cambi alle chiamate ($this/self
 * risolvono nella classe che usa il trait).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

trait Olobuild_Rest_Activation_Trait {
    public function activate_header( $request ) {
        $body = $request->get_json_params();
        $id   = absint( $body['id'] ?? 0 );

        if ( ! $id ) {
            return new WP_Error( 'missing_id', 'ID template obbligatorio.', [ 'status' => 400 ] );
        }

        // Verify template exists and is of type header
        $db  = new Olobuild_Database();
        $tpl = $db->get_template( $id );
        if ( ! $tpl ) {
            return new WP_Error( 'not_found', 'Template non trovato.', [ 'status' => 404 ] );
        }

        update_option( 'olobuild_active_header', $id );

        return rest_ensure_response( [ 'active_header' => $id ] );
    }

    public function deactivate_header() {
        delete_option( 'olobuild_active_header' );
        return rest_ensure_response( [ 'active_header' => 0 ] );
    }

    public function get_active_header() {
        return rest_ensure_response( [
            'active_header' => (int) get_option( 'olobuild_active_header', 0 ),
        ] );
    }

    public function activate_footer( $request ) {
        $body = $request->get_json_params();
        $id   = absint( $body['id'] ?? 0 );

        if ( ! $id ) {
            return new WP_Error( 'missing_id', 'ID template obbligatorio.', [ 'status' => 400 ] );
        }

        // Verify template exists
        $db  = new Olobuild_Database();
        $tpl = $db->get_template( $id );
        if ( ! $tpl ) {
            return new WP_Error( 'not_found', 'Template non trovato.', [ 'status' => 404 ] );
        }

        update_option( 'olobuild_active_footer', $id );

        return rest_ensure_response( [ 'active_footer' => $id ] );
    }

    public function deactivate_footer() {
        delete_option( 'olobuild_active_footer' );
        return rest_ensure_response( [ 'active_footer' => 0 ] );
    }

    public function get_active_footer() {
        return rest_ensure_response( [
            'active_footer' => (int) get_option( 'olobuild_active_footer', 0 ),
        ] );
    }

    public function activate_single( $request ) {
        $body      = $request->get_json_params();
        $id        = absint( $body['id'] ?? 0 );
        $post_type = sanitize_key( $body['post_type'] ?? '' );

        if ( ! $id || ! $post_type ) {
            return new WP_Error( 'missing_params', 'Template ID and post_type are required.', [ 'status' => 400 ] );
        }

        $db  = new Olobuild_Database();
        $tpl = $db->get_template( $id );
        if ( ! $tpl ) {
            return new WP_Error( 'not_found', 'Template non trovato.', [ 'status' => 404 ] );
        }

        update_option( "olobuild_active_single_{$post_type}", $id );

        return rest_ensure_response( [ 'post_type' => $post_type, 'template_id' => $id ] );
    }

    public function deactivate_single( $request ) {
        $body      = $request->get_json_params();
        $post_type = sanitize_key( $body['post_type'] ?? '' );

        if ( ! $post_type ) {
            return new WP_Error( 'missing_params', 'post_type is required.', [ 'status' => 400 ] );
        }

        delete_option( "olobuild_active_single_{$post_type}" );

        return rest_ensure_response( [ 'post_type' => $post_type, 'template_id' => 0 ] );
    }

    public function get_active_singles() {
        $post_types = get_post_types( [ 'public' => true ], 'names' );
        $result     = [];

        foreach ( $post_types as $pt ) {
            if ( in_array( $pt, [ 'page', 'attachment' ], true ) ) continue;
            $tpl_id = (int) get_option( "olobuild_active_single_{$pt}", 0 );
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

        $db  = new Olobuild_Database();
        $tpl = $db->get_template( $id );
        if ( ! $tpl ) {
            return new WP_Error( 'not_found', 'Template non trovato.', [ 'status' => 404 ] );
        }

        if ( $post_type ) {
            // Post-type-specific archive template
            update_option( "olobuild_active_archive_{$post_type}", $id );
            return rest_ensure_response( [ 'post_type' => $post_type, 'template_id' => $id ] );
        }

        // Generic archive template (fallback)
        update_option( 'olobuild_active_archive', $id );
        return rest_ensure_response( [ 'active_archive' => $id ] );
    }

    public function deactivate_archive( $request ) {
        $body      = $request->get_json_params();
        $post_type = sanitize_key( $body['post_type'] ?? '' );

        if ( $post_type ) {
            delete_option( "olobuild_active_archive_{$post_type}" );
            return rest_ensure_response( [ 'post_type' => $post_type, 'template_id' => 0 ] );
        }

        delete_option( 'olobuild_active_archive' );
        return rest_ensure_response( [ 'active_archive' => 0 ] );
    }

    public function get_active_archives() {
        $post_types = get_post_types( [ 'public' => true ], 'names' );
        $result     = [];

        // Generic archive fallback
        $generic = (int) get_option( 'olobuild_active_archive', 0 );
        if ( $generic ) {
            $result['_generic'] = $generic;
        }

        // Per-post-type archive templates
        foreach ( $post_types as $pt ) {
            if ( in_array( $pt, [ 'page', 'attachment' ], true ) ) continue;
            $tpl_id = (int) get_option( "olobuild_active_archive_{$pt}", 0 );
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

        $db  = new Olobuild_Database();
        $tpl = $db->get_template( $id );
        if ( ! $tpl ) {
            return new WP_Error( 'not_found', 'Template non trovato.', [ 'status' => 404 ] );
        }

        update_option( 'olobuild_active_404', $id );

        return rest_ensure_response( [ 'active_404' => $id ] );
    }

    public function deactivate_404() {
        delete_option( 'olobuild_active_404' );
        return rest_ensure_response( [ 'active_404' => 0 ] );
    }

    public function get_active_404() {
        return rest_ensure_response( [
            'active_404' => (int) get_option( 'olobuild_active_404', 0 ),
        ] );
    }

    // ─── Search results template activation ───────────────────────────

    public function activate_search( $request ) {
        $body = $request->get_json_params();
        $id   = absint( $body['id'] ?? 0 );

        if ( ! $id ) {
            return new WP_Error( 'missing_id', 'ID template obbligatorio.', [ 'status' => 400 ] );
        }

        $db  = new Olobuild_Database();
        $tpl = $db->get_template( $id );
        if ( ! $tpl ) {
            return new WP_Error( 'not_found', 'Template non trovato.', [ 'status' => 404 ] );
        }

        update_option( 'olobuild_active_search', $id );

        return rest_ensure_response( [ 'active_search' => $id ] );
    }

    public function deactivate_search() {
        delete_option( 'olobuild_active_search' );
        return rest_ensure_response( [ 'active_search' => 0 ] );
    }

    public function get_active_search() {
        return rest_ensure_response( [
            'active_search' => (int) get_option( 'olobuild_active_search', 0 ),
        ] );
    }

    public function export_template( $request ) {
        $db       = new Olobuild_Database();
        $template = $db->get_template( (int) $request['id'] );

        if ( ! $template ) {
            return new WP_Error( 'not_found', 'Template non trovato.', [ 'status' => 404 ] );
        }

        $slug = sanitize_title( $template['title'] ?: 'template' );

        $export = [
            'olo_export' => 'template',
            'version'       => OLOBUILD_VERSION,
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
        if ( olobuild_imports_disabled() ) return olobuild_imports_disabled_error();
        $body = $request->get_json_params();

        if ( empty( $body['olo_export'] ) || $body['olo_export'] !== 'template' ) {
            return new WP_Error( 'invalid_file', 'File non valido: non è un export Olobuild template.', [ 'status' => 400 ] );
        }

        // Validate content/settings on import too
        $import_content = $body['content'] ?? [];
        if ( ! is_array( $import_content ) ) {
            return new WP_Error( 'invalid_content', 'Il campo content deve essere un array.', [ 'status' => 400 ] );
        }
        // Stesso gate kses di create/update_template: senza, l'import permetteva
        // a chi NON ha unfiltered_html di far entrare html_content/shortcode raw.
        $import_content = $this->sanitize_unfiltered_tile_fields( $import_content );
        $import_settings = $body['settings'] ?? [];
        if ( ! is_array( $import_settings ) && ! is_object( $import_settings ) ) {
            return new WP_Error( 'invalid_settings', 'Il campo settings deve essere un oggetto.', [ 'status' => 400 ] );
        }
        if ( is_object( $import_settings ) ) {
            $import_settings = (array) $import_settings;
        }

        $db = new Olobuild_Database();
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

        $db            = new Olobuild_Database();
        $active_header = (int) get_option( 'olobuild_active_header', 0 );
        $active_footer = (int) get_option( 'olobuild_active_footer', 0 );
        $active_404    = (int) get_option( 'olobuild_active_404', 0 );

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
            'version'     => OLOBUILD_VERSION,
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
        if ( olobuild_imports_disabled() ) return olobuild_imports_disabled_error();
        $body = $request->get_json_params();
        if ( empty( $body['olo_export'] ) || $body['olo_export'] !== 'theme-bundle' ) {
            return new WP_Error( 'invalid_file', 'File non valido: non è un tema Olobuild (theme-bundle).', [ 'status' => 400 ] );
        }
        $templates = ( isset( $body['templates'] ) && is_array( $body['templates'] ) ) ? $body['templates'] : [];
        if ( empty( $templates ) ) {
            return new WP_Error( 'empty', 'Il tema non contiene template.', [ 'status' => 400 ] );
        }

        $db      = new Olobuild_Database();
        $id_map  = [];
        $created = [];
        foreach ( $templates as $tpl ) {
            if ( ! is_array( $tpl ) ) continue;
            $content  = ( isset( $tpl['content'] ) && is_array( $tpl['content'] ) ) ? $tpl['content'] : [];
            $content  = $this->sanitize_unfiltered_tile_fields( $content ); // gate kses anche sui bundle
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
        if ( ! empty( $activate['header'] ) && isset( $id_map[ $activate['header'] ] ) ) { update_option( 'olobuild_active_header', $id_map[ $activate['header'] ] ); $activated[] = 'header'; }
        if ( ! empty( $activate['footer'] ) && isset( $id_map[ $activate['footer'] ] ) ) { update_option( 'olobuild_active_footer', $id_map[ $activate['footer'] ] ); $activated[] = 'footer'; }
        if ( ! empty( $activate['404'] ) && isset( $id_map[ $activate['404'] ] ) )       { update_option( 'olobuild_active_404', $id_map[ $activate['404'] ] ); $activated[] = '404'; }

        return rest_ensure_response( [
            'success'   => true,
            'imported'  => count( $created ),
            'templates' => $created,
            'activated' => $activated,
        ] );
    }
}
