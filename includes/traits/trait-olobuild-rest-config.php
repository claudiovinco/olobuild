<?php
/**
 * Olobuild_Rest_Config_Trait — endpoint configurazione e asset: font, icone, global widgets/colors/typography, code, maintenance, revisions, temi, libreria, tokens, analytics, critical CSS, site export/import.
 *
 * Estratto verbatim da class-rest-api.php (dieta monoliti v1.4.390):
 * stessi metodi, stessa visibilita', zero cambi alle chiamate ($this/self
 * risolvono nella classe che usa il trait).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

trait Olobuild_Rest_Config_Trait {
    public function get_fonts() {
        return new WP_REST_Response( Olobuild_Custom_Fonts::get_fonts(), 200 );
    }

    public function upload_font( $request ) {
        $name   = sanitize_text_field( $request->get_param( 'font_name' ) ?? '' );
        $weight = sanitize_text_field( $request->get_param( 'font_weight' ) ?? '400' );
        $style  = sanitize_text_field( $request->get_param( 'font_style' ) ?? 'normal' );

        if ( empty( $name ) ) {
            return new WP_REST_Response( [ 'message' => 'Nome font obbligatorio.' ], 400 );
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- callback REST (register_rest_route con permission_callback manage_options): il nonce X-WP-Nonce è verificato da WordPress; qui è una semplice verifica di presenza del file.
        if ( empty( $_FILES['font_file'] ) ) {
            return new WP_REST_Response( [ 'message' => 'Nessun file caricato.' ], 400 );
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- callback REST (permission_callback manage_options, nonce X-WP-Nonce verificato da WP); il file viene validato a valle in Olobuild_Custom_Fonts::upload_font_file (allowlist estensioni woff2/woff/ttf/otf + limite 5MB + is_uploaded_file su tmp_name + sanitize_file_name sul nome).
        $url = Olobuild_Custom_Fonts::upload_font_file( $_FILES['font_file'] );
        if ( is_wp_error( $url ) ) {
            return new WP_REST_Response( [ 'message' => $url->get_error_message() ], 400 );
        }

        // Add or update font in the list
        $fonts = Olobuild_Custom_Fonts::get_fonts();
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

        Olobuild_Custom_Fonts::save_fonts( $fonts );
        return new WP_REST_Response( [ 'success' => true, 'fonts' => $fonts ], 200 );
    }

    public function delete_font( $request ) {
        $id = sanitize_text_field( $request->get_param( 'id' ) );
        Olobuild_Custom_Fonts::delete_font( $id );
        return new WP_REST_Response( [ 'success' => true, 'fonts' => Olobuild_Custom_Fonts::get_fonts() ], 200 );
    }

    // === Custom Icons ===

    public function get_custom_icons() {
        $icons = get_option( 'olobuild_custom_icons', [] );
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
        $svg_content = olobuild_sanitize_svg( $svg_content );
        if ( empty( $svg_content ) ) {
            return new WP_Error( 'invalid_svg', 'Il file SVG non è valido o contiene elementi non sicuri', [ 'status' => 400 ] );
        }

        $name = sanitize_file_name( pathinfo( $file['name'], PATHINFO_FILENAME ) );
        $name = preg_replace( '/[^a-zA-Z0-9_-]/', '', $name );

        $icons = get_option( 'olobuild_custom_icons', [] );
        $icons[ $name ] = $svg_content;
        update_option( 'olobuild_custom_icons', $icons );

        return new WP_REST_Response( [ 'success' => true, 'name' => $name, 'svg' => $svg_content, 'icons' => $icons ], 200 );
    }

    public function delete_custom_icon( $request ) {
        $name = sanitize_text_field( $request->get_param( 'name' ) );
        $icons = get_option( 'olobuild_custom_icons', [] );
        unset( $icons[ $name ] );
        update_option( 'olobuild_custom_icons', $icons );
        return new WP_REST_Response( [ 'success' => true, 'icons' => $icons ], 200 );
    }

    // === Global Widgets ===

    public function get_global_widgets() {
        global $wpdb;
        $table = $wpdb->prefix . 'olobuild_global_widgets';
        // Tabella custom del plugin ({prefix}olo_global_widgets); nessun equivalente WP_Query.
        // Safe: $table è costruito da $wpdb->prefix (non input utente); nessun valore interpolato.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $rows = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY name ASC", ARRAY_A );
        return new WP_REST_Response( $rows ?: [], 200 );
    }

    public function create_global_widget( $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'olobuild_global_widgets';
        $name = sanitize_text_field( $request->get_param( 'name' ) ?? 'Widget globale' );
        $tile_data = $request->get_param( 'tile_data' );
        // I widget globali finiscono nel render frontend: stesso gate kses dei template.
        // Deve essere un albero di tile (array): una stringa JSON che NON decodifica in
        // array verrebbe altrimenti salvata RAW, bypassando wp_kses_post (stored XSS).
        if ( is_string( $tile_data ) ) {
            $decoded   = json_decode( $tile_data, true );
            $tile_data = is_array( $decoded ) ? $decoded : null;
        }
        if ( ! is_array( $tile_data ) ) {
            return new WP_REST_Response( [ 'message' => 'tile_data non valido.' ], 400 );
        }
        $tile_data = wp_json_encode( $this->sanitize_unfiltered_tile_fields( $tile_data ) );
        // Tabella custom del plugin ({prefix}olo_global_widgets); insert via API $wpdb con
        // format array (valori escaped da WP). Scrittura → niente cache.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
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
        $table = $wpdb->prefix . 'olobuild_global_widgets';
        $id = absint( $request->get_param( 'id' ) );
        $data = [];
        $formats = [];
        if ( $request->get_param( 'name' ) !== null ) {
            $data['name'] = sanitize_text_field( $request->get_param( 'name' ) );
            $formats[] = '%s';
        }
        if ( $request->get_param( 'tile_data' ) !== null ) {
            $td = $request->get_param( 'tile_data' );
            if ( is_string( $td ) ) {
                $decoded = json_decode( $td, true );
                $td      = is_array( $decoded ) ? $decoded : null;
            }
            if ( ! is_array( $td ) ) {
                return new WP_REST_Response( [ 'message' => 'tile_data non valido.' ], 400 );
            }
            $data['tile_data'] = wp_json_encode( $this->sanitize_unfiltered_tile_fields( $td ) );
            $formats[] = '%s';
        }
        if ( empty( $data ) ) {
            return new WP_REST_Response( [ 'message' => 'Nessun dato da aggiornare.' ], 400 );
        }
        $data['updated_at'] = current_time( 'mysql' );
        $formats[] = '%s';
        // Tabella custom del plugin ({prefix}olo_global_widgets); update via API $wpdb con
        // format array (valori escaped da WP). Scrittura → niente cache.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $wpdb->update( $table, $data, [ 'id' => $id ], $formats, [ '%d' ] );
        return new WP_REST_Response( [ 'success' => true ], 200 );
    }

    public function delete_global_widget( $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'olobuild_global_widgets';
        $id = absint( $request->get_param( 'id' ) );
        // Tabella custom del plugin ({prefix}olo_global_widgets); delete via API $wpdb con
        // format array. Scrittura → niente cache.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $wpdb->delete( $table, [ 'id' => $id ], [ '%d' ] );
        return new WP_REST_Response( [ 'success' => true ], 200 );
    }

    // === Global Colors ===

    public function get_global_colors() {
        $colors = get_option( 'olobuild_global_colors', [] );
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

        update_option( 'olobuild_global_colors', $sanitized, false );

        return rest_ensure_response( $sanitized );
    }

    // === Global Typography ===

    public function get_global_typography() {
        $sets = get_option( 'olobuild_global_typography', [] );
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

        update_option( 'olobuild_global_typography', $sanitized, false );

        return rest_ensure_response( $sanitized );
    }

    // === Stock Media (comportamento + chiavi API provider) ===

    public function get_stockmedia_behavior() {
        return rest_ensure_response( olobuild_stockmedia_behavior() );
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
        update_option( 'olobuild_stockmedia_behavior', $s, false );
        return rest_ensure_response( $s );
    }

    public function get_api_keys() {
        return rest_ensure_response( [
            'olobuild_unsplash_api_key'  => (string) get_option( 'olobuild_unsplash_api_key', '' ),
            'olobuild_pexels_api_key'    => (string) get_option( 'olobuild_pexels_api_key', '' ),
            'olobuild_pixabay_api_key'   => (string) get_option( 'olobuild_pixabay_api_key', '' ),
            'olobuild_freesound_api_key' => (string) get_option( 'olobuild_freesound_api_key', '' ),
        ] );
    }

    public function save_api_keys( $request ) {
        $b    = $request->get_json_params();
        $keys = [ 'olobuild_unsplash_api_key', 'olobuild_pexels_api_key', 'olobuild_pixabay_api_key', 'olobuild_freesound_api_key' ];
        foreach ( $keys as $k ) {
            if ( is_array( $b ) && array_key_exists( $k, $b ) ) {
                update_option( $k, sanitize_text_field( $b[ $k ] ?? '' ), false );
            }
        }
        return $this->get_api_keys();
    }

    // === Cursore magnetico globale ===

    public function get_magnetic_cursor() {
        if ( ! class_exists( 'Olobuild_Magnetic_Cursor' ) ) {
            require_once OLOBUILD_PATH . 'includes/class-magnetic-cursor.php';
        }
        return rest_ensure_response( Olobuild_Magnetic_Cursor::get_settings() );
    }

    public function save_magnetic_cursor( $request ) {
        if ( ! class_exists( 'Olobuild_Magnetic_Cursor' ) ) {
            require_once OLOBUILD_PATH . 'includes/class-magnetic-cursor.php';
        }
        $b = $request->get_json_params();
        if ( ! is_array( $b ) ) {
            return new WP_Error( 'invalid_data', __( 'Dati non validi.', 'olobuild' ), [ 'status' => 400 ] );
        }
        // Merge sull'esistente: il pannello può inviare anche solo un sottoinsieme
        // di chiavi (es. il toggle enabled) senza azzerare le altre.
        $merged = array_merge( Olobuild_Magnetic_Cursor::get_settings(), $b );
        $clean  = Olobuild_Magnetic_Cursor::sanitize( $merged );
        update_option( Olobuild_Magnetic_Cursor::OPT, $clean, false );
        return rest_ensure_response( $clean );
    }

    // === HUD mirino globale (crosshair + coordinate) ===

    public function get_cursor_hud() {
        if ( ! class_exists( 'Olobuild_Cursor_Hud' ) ) {
            require_once OLOBUILD_PATH . 'includes/class-cursor-hud.php';
        }
        return rest_ensure_response( Olobuild_Cursor_Hud::get_settings() );
    }

    public function save_cursor_hud( $request ) {
        if ( ! class_exists( 'Olobuild_Cursor_Hud' ) ) {
            require_once OLOBUILD_PATH . 'includes/class-cursor-hud.php';
        }
        $b = $request->get_json_params();
        if ( ! is_array( $b ) ) {
            return new WP_Error( 'invalid_data', __( 'Dati non validi.', 'olobuild' ), [ 'status' => 400 ] );
        }
        // Merge sull'esistente: il pannello può inviare anche solo `enabled`.
        $merged = array_merge( Olobuild_Cursor_Hud::get_settings(), $b );
        $clean  = Olobuild_Cursor_Hud::sanitize( $merged );
        update_option( Olobuild_Cursor_Hud::OPT, $clean, false );
        return rest_ensure_response( $clean );
    }

    // === Custom Code Snippets ===

    public function get_custom_code() {
        return rest_ensure_response( [
            'head'   => get_option( 'olobuild_custom_code_head', '' ),
            'body'   => get_option( 'olobuild_custom_code_body', '' ),
            'footer' => get_option( 'olobuild_custom_code_footer', '' ),
        ] );
    }

    public function save_custom_code( $request ) {
        // Difesa in profondità: gli snippet sono emessi raw nel frontend.
        if ( ! current_user_can( 'unfiltered_html' ) ) {
            return new WP_Error( 'olo_forbidden', __( 'Permessi insufficienti per salvare codice personalizzato.', 'olobuild' ), [ 'status' => 403 ] );
        }

        $body = $request->get_json_params();

        if ( isset( $body['head'] ) ) {
            update_option( 'olobuild_custom_code_head', $body['head'], false );
        }
        if ( isset( $body['body'] ) ) {
            update_option( 'olobuild_custom_code_body', $body['body'], false );
        }
        if ( isset( $body['footer'] ) ) {
            update_option( 'olobuild_custom_code_footer', $body['footer'], false );
        }

        return rest_ensure_response( [
            'head'   => get_option( 'olobuild_custom_code_head', '' ),
            'body'   => get_option( 'olobuild_custom_code_body', '' ),
            'footer' => get_option( 'olobuild_custom_code_footer', '' ),
        ] );
    }

    // === Maintenance Mode ===

    public function get_maintenance() {
        return rest_ensure_response( [
            'mode'                   => get_option( 'olobuild_maintenance_mode', 'off' ),
            'template_id'            => (int) get_option( 'olobuild_maintenance_template_id', 0 ),
            'coming_soon_template_id' => (int) get_option( 'olobuild_coming_soon_template_id', 0 ),
            'bypass_roles'           => get_option( 'olobuild_maintenance_bypass_roles', [ 'administrator' ] ),
            'bypass_secret'          => get_option( 'olobuild_maintenance_bypass_secret', '' ),
        ] );
    }

    public function save_maintenance( $request ) {
        $body = $request->get_json_params();

        if ( isset( $body['mode'] ) ) {
            $allowed = [ 'off', 'maintenance', 'coming_soon' ];
            $mode = in_array( $body['mode'], $allowed, true ) ? $body['mode'] : 'off';
            update_option( 'olobuild_maintenance_mode', $mode, false );
        }

        if ( isset( $body['template_id'] ) ) {
            update_option( 'olobuild_maintenance_template_id', absint( $body['template_id'] ), false );
        }

        if ( isset( $body['coming_soon_template_id'] ) ) {
            update_option( 'olobuild_coming_soon_template_id', absint( $body['coming_soon_template_id'] ), false );
        }

        if ( isset( $body['bypass_roles'] ) ) {
            $roles = [];
            if ( is_array( $body['bypass_roles'] ) ) {
                foreach ( $body['bypass_roles'] as $role ) {
                    $roles[] = sanitize_key( $role );
                }
            }
            update_option( 'olobuild_maintenance_bypass_roles', $roles, false );
        }

        if ( isset( $body['bypass_secret'] ) ) {
            update_option( 'olobuild_maintenance_bypass_secret', sanitize_text_field( $body['bypass_secret'] ), false );
        }

        return rest_ensure_response( [
            'mode'                   => get_option( 'olobuild_maintenance_mode', 'off' ),
            'template_id'            => (int) get_option( 'olobuild_maintenance_template_id', 0 ),
            'coming_soon_template_id' => (int) get_option( 'olobuild_coming_soon_template_id', 0 ),
            'bypass_roles'           => get_option( 'olobuild_maintenance_bypass_roles', [ 'administrator' ] ),
            'bypass_secret'          => get_option( 'olobuild_maintenance_bypass_secret', '' ),
        ] );
    }

    // === Revisions ===

    public function get_revisions( $request ) {
        $db  = new Olobuild_Database();
        $id  = (int) $request['id'];
        $tpl = $db->get_template( $id );

        if ( ! $tpl ) {
            return new WP_Error( 'not_found', 'Template non trovato.', [ 'status' => 404 ] );
        }

        $revisions = $db->get_revisions( $id, 50 );

        return rest_ensure_response( $revisions );
    }

    public function get_revision( $request ) {
        $db  = new Olobuild_Database();
        $rev = $db->get_revision( (int) $request['rev_id'] );

        if ( ! $rev ) {
            return new WP_Error( 'not_found', 'Revisione non trovata.', [ 'status' => 404 ] );
        }

        return rest_ensure_response( $rev );
    }

    // === Themes ===

    public function get_themes() {
        require_once OLOBUILD_PATH . 'includes/class-theme-importer.php';
        return rest_ensure_response( Olobuild_Theme_Importer::get_themes() );
    }

    public function import_theme( $request ) {
        if ( olobuild_imports_disabled() ) return olobuild_imports_disabled_error();
        require_once OLOBUILD_PATH . 'includes/class-theme-importer.php';
        $result = Olobuild_Theme_Importer::import_theme( $request['theme_id'] );
        if ( is_wp_error( $result ) ) return $result;
        return rest_ensure_response( $result );
    }

    // === Design Presets ===

    public function get_design_presets() {
        $presets = get_option( 'olobuild_design_presets', [] );
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

        $presets = get_option( 'olobuild_design_presets', [] );
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
        update_option( 'olobuild_design_presets', $presets, false );

        return rest_ensure_response( $new_preset );
    }

    public function update_design_preset( $request ) {
        $id   = sanitize_text_field( $request['id'] );
        $body = $request->get_json_params();

        $presets = get_option( 'olobuild_design_presets', [] );
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

        update_option( 'olobuild_design_presets', $presets, false );

        return rest_ensure_response( [ 'success' => true ] );
    }

    public function delete_design_preset( $request ) {
        $id = sanitize_text_field( $request['id'] );

        $presets = get_option( 'olobuild_design_presets', [] );
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

        update_option( 'olobuild_design_presets', $new_presets, false );

        return rest_ensure_response( [ 'success' => true ] );
    }

    // === Template Library ===

    public function get_template_library( $request ) {
        $lib = Olobuild_Template_Library::instance();
        $category = sanitize_text_field( $request->get_param( 'category' ) ?? '' );
        $templates = $lib->get_all_templates();
        if ( $category ) {
            $templates = array_values( array_filter( $templates, function( $t ) use ( $category ) {
                return ( $t['category'] ?? '' ) === $category;
            } ) );
        }
        // Metadati per la lista. Per i BLOCCHI-sezione includiamo anche il `content`
        // così il modale può disegnare l'anteprima SVG strutturale reale (con colori
        // token e superfici), invece del segnaposto generico. Le PAGINE intere (pesanti)
        // restano solo-metadati e usano il thumbnail quando presente.
        $list = array_map( function( $t ) {
            $row = [
                'id'                  => $t['id'] ?? '',
                'name'                => $t['name'] ?? '',
                'category'            => $t['category'] ?? '',
                'preview_description' => $t['preview_description'] ?? '',
                'is_user'             => ! empty( $t['is_user'] ),
            ];
            if ( ! empty( $t['thumbnail'] ) ) {
                $row['thumbnail'] = $t['thumbnail'];
            }
            if ( ( $t['category'] ?? '' ) !== 'page' && isset( $t['content'] ) ) {
                $row['content'] = $t['content'];
            }
            return $row;
        }, $templates );
        return rest_ensure_response( $list );
    }

    public function get_library_template( $request ) {
        $id  = sanitize_text_field( $request['id'] );
        $lib = Olobuild_Template_Library::instance();
        // Check built-in first
        $tpl = $lib->get_template( $id );
        if ( ! $tpl ) {
            // Check user templates
            $user = get_option( 'olobuild_user_templates', [] );
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
        $lib = Olobuild_Template_Library::instance();
        $id  = $lib->save_user_template( $name, $category, $content );
        return rest_ensure_response( [ 'id' => $id, 'success' => true ] );
    }

    public function delete_user_template( $request ) {
        $id  = sanitize_text_field( $request['id'] );
        $lib = Olobuild_Template_Library::instance();
        $ok  = $lib->delete_user_template( $id );
        if ( ! $ok ) {
            return new WP_Error( 'not_found', 'Template non trovato.', [ 'status' => 404 ] );
        }
        return rest_ensure_response( [ 'success' => true ] );
    }

    // === Built-in Design Presets ===

    public function get_builtin_presets() {
        $style_system = Olobuild_Style_System::instance();
        return rest_ensure_response( $style_system->get_presets() );
    }

    // === Design Tokens Export ===

    public function export_design_tokens() {
        $style_system = Olobuild_Style_System::instance();
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
        $global_colors = get_option( 'olobuild_global_colors', [] );
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
            'ga_measurement_id' => get_option( 'olobuild_ga_measurement_id', '' ),
            'fb_pixel_id'       => get_option( 'olobuild_fb_pixel_id', '' ),
            'gtm_container_id'  => get_option( 'olobuild_gtm_container_id', '' ),
        ] );
    }

    public function save_analytics( $request ) {
        $body = $request->get_json_params();

        if ( isset( $body['ga_measurement_id'] ) ) {
            update_option( 'olobuild_ga_measurement_id', sanitize_text_field( $body['ga_measurement_id'] ), false );
        }
        if ( isset( $body['fb_pixel_id'] ) ) {
            update_option( 'olobuild_fb_pixel_id', sanitize_text_field( $body['fb_pixel_id'] ), false );
        }
        if ( isset( $body['gtm_container_id'] ) ) {
            update_option( 'olobuild_gtm_container_id', sanitize_text_field( $body['gtm_container_id'] ), false );
        }

        return rest_ensure_response( [
            'ga_measurement_id' => get_option( 'olobuild_ga_measurement_id', '' ),
            'fb_pixel_id'       => get_option( 'olobuild_fb_pixel_id', '' ),
            'gtm_container_id'  => get_option( 'olobuild_gtm_container_id', '' ),
        ] );
    }

    // === Critical CSS ===

    public function generate_critical_css( $request ) {
        $body    = $request->get_json_params();
        $post_id = absint( $body['post_id'] ?? 0 );

        if ( ! $post_id ) {
            return new WP_Error( 'missing_post_id', 'post_id obbligatorio.', [ 'status' => 400 ] );
        }

        if ( ! class_exists( 'Olobuild_Critical_CSS' ) ) {
            return new WP_Error( 'not_available', 'Critical CSS non disponibile.', [ 'status' => 500 ] );
        }

        $css = Olobuild_Critical_CSS::generate_critical_css( $post_id );

        return rest_ensure_response( [
            'post_id' => $post_id,
            'css'     => $css,
            'size'    => strlen( $css ),
        ] );
    }

    public function regenerate_all_critical_css() {
        if ( ! class_exists( 'Olobuild_Critical_CSS' ) ) {
            return new WP_Error( 'not_available', 'Critical CSS non disponibile.', [ 'status' => 500 ] );
        }

        $result = Olobuild_Critical_CSS::regenerate_all();

        return rest_ensure_response( $result );
    }

    public function purge_critical_css() {
        if ( ! class_exists( 'Olobuild_Critical_CSS' ) ) {
            return new WP_Error( 'not_available', 'Critical CSS non disponibile.', [ 'status' => 500 ] );
        }

        $purged = Olobuild_Critical_CSS::purge_all();

        return rest_ensure_response( [ 'purged' => $purged ] );
    }

    public function get_critical_css_status() {
        if ( ! class_exists( 'Olobuild_Critical_CSS' ) ) {
            return rest_ensure_response( [ 'enabled' => false, 'cached_count' => 0 ] );
        }

        return rest_ensure_response( Olobuild_Critical_CSS::get_status() );
    }

    /**
     * Export all Olobuild site data as JSON.
     */
    public function site_export() {
        if ( ! class_exists( 'Olobuild_Site_Export' ) ) {
            require_once OLOBUILD_PATH . 'includes/class-site-export.php';
        }

        $data = Olobuild_Site_Export::export_site();

        return rest_ensure_response( $data );
    }

    /**
     * Import Olobuild site data from JSON body.
     */
    public function site_import( $request ) {
        if ( ! class_exists( 'Olobuild_Site_Export' ) ) {
            require_once OLOBUILD_PATH . 'includes/class-site-export.php';
        }

        $body = $request->get_json_params();
        if ( empty( $body ) ) {
            return new WP_Error( 'invalid_data', 'Empty or invalid JSON body.', [ 'status' => 400 ] );
        }

        $result = Olobuild_Site_Export::import_site( $body );

        return rest_ensure_response( $result );
    }
}
