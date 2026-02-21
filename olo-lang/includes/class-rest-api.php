<?php
/**
 * Olo Lang — REST API (namespace olo-lang/v1).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Lang_Rest_Api {

    private $namespace = 'olo-lang/v1';

    public function init() {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    public function register_routes() {
        // Configurazione lingue
        register_rest_route( $this->namespace, '/config', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_config' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'save_config' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
        ] );

        // Catalogo lingue disponibili
        register_rest_route( $this->namespace, '/available-languages', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_available_languages' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );

        // Scansione template per stringhe traducibili
        register_rest_route( $this->namespace, '/scan/(?P<template_id>\d+)', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'scan_template' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );

        // Traduzioni per template + lingua
        register_rest_route( $this->namespace, '/translations/(?P<template_id>\d+)/(?P<lang>[a-z]{2,5})', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_translations' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'save_translations' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [ $this, 'delete_translations' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
        ] );

        // Statistiche
        register_rest_route( $this->namespace, '/stats', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_stats' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );

        register_rest_route( $this->namespace, '/stats/(?P<template_id>\d+)', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_template_stats' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );

        // ---- Stringhe Globali ----

        // Scansiona menu WP
        register_rest_route( $this->namespace, '/scan/menus', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'scan_menus' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );

        // Popola menu WP nel DB
        register_rest_route( $this->namespace, '/scan/menus/populate', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'populate_menus' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );

        // Popola stringhe plugin nel DB
        register_rest_route( $this->namespace, '/scan/plugins/populate', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'populate_plugins' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );

        // Stringhe globali per lingua (GET + PUT)
        register_rest_route( $this->namespace, '/global-strings/(?P<lang>[a-z]{2,5})', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_global_strings' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'save_global_strings' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
        ] );

        // Stringa custom
        register_rest_route( $this->namespace, '/global-strings/custom', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'add_custom_string' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );

        // Elimina stringa globale
        register_rest_route( $this->namespace, '/global-strings/(?P<id>\d+)', [
            'methods'             => 'DELETE',
            'callback'            => [ $this, 'delete_global_string' ],
            'permission_callback' => [ $this, 'check_permission' ],
        ] );
    }

    public function check_permission() {
        return current_user_can( 'edit_posts' );
    }

    public function get_config() {
        return rest_ensure_response( Olo_Lang_Language::get_config() );
    }

    public function save_config( $request ) {
        $body = $request->get_json_params();
        Olo_Lang_Language::save_config( $body );

        // Flush rewrite rules quando cambia url_mode o le lingue
        flush_rewrite_rules();

        return rest_ensure_response( Olo_Lang_Language::get_config() );
    }

    public function get_available_languages() {
        return rest_ensure_response( Olo_Lang_Language::get_available_languages() );
    }

    public function scan_template( $request ) {
        $template_id = absint( $request['template_id'] );

        if ( ! class_exists( 'Olo_Database' ) ) {
            return new WP_Error( 'dependency', 'Olobuild non attivo.', [ 'status' => 500 ] );
        }

        $db       = new Olo_Database();
        $template = $db->get_template( $template_id );

        if ( ! $template ) {
            return new WP_Error( 'not_found', 'Template non trovato.', [ 'status' => 404 ] );
        }

        $strings = Olo_Lang_Scanner::scan_template( $template['content'] );

        return rest_ensure_response( [
            'template_id' => $template_id,
            'title'       => $template['title'],
            'strings'     => $strings,
            'count'       => count( $strings ),
        ] );
    }

    public function get_translations( $request ) {
        $template_id = absint( $request['template_id'] );
        $lang        = sanitize_text_field( $request['lang'] );

        $db           = new Olo_Lang_Database();
        $translations = $db->get_template_translations( $template_id, $lang );

        return rest_ensure_response( [
            'template_id'  => $template_id,
            'lang'         => $lang,
            'translations' => $translations,
        ] );
    }

    public function save_translations( $request ) {
        $template_id  = absint( $request['template_id'] );
        $lang         = sanitize_text_field( $request['lang'] );
        $body         = $request->get_json_params();
        $translations = $body['translations'] ?? [];

        $db    = new Olo_Lang_Database();
        $saved = $db->save_translations_bulk( $template_id, $lang, $translations );

        return rest_ensure_response( [
            'template_id' => $template_id,
            'lang'        => $lang,
            'saved'       => $saved,
        ] );
    }

    public function delete_translations( $request ) {
        $template_id = absint( $request['template_id'] );
        $lang        = sanitize_text_field( $request['lang'] );

        $db = new Olo_Lang_Database();
        $db->delete_template_translations( $template_id, $lang );

        return rest_ensure_response( [ 'deleted' => true ] );
    }

    public function get_stats() {
        $db = new Olo_Lang_Database();
        return rest_ensure_response( $db->get_global_stats() );
    }

    public function get_template_stats( $request ) {
        $template_id = absint( $request['template_id'] );
        $db          = new Olo_Lang_Database();
        return rest_ensure_response( $db->get_template_stats( $template_id ) );
    }

    // =========================================================================
    // Stringhe Globali — callbacks
    // =========================================================================

    /**
     * Scansiona tutti i menu WP e ritorna le voci con titoli.
     */
    public function scan_menus() {
        $menus  = wp_get_nav_menus();
        $result = [];

        foreach ( $menus as $menu ) {
            $items = wp_get_nav_menu_items( $menu->term_id );
            if ( ! $items ) {
                continue;
            }
            $menu_data = [
                'menu_id'   => $menu->term_id,
                'menu_name' => $menu->name,
                'items'     => [],
            ];
            foreach ( $items as $item ) {
                $menu_data['items'][] = [
                    'id'    => $item->ID,
                    'title' => $item->title,
                    'url'   => $item->url,
                    'type'  => $item->type,
                ];
            }
            $result[] = $menu_data;
        }

        return rest_ensure_response( $result );
    }

    /**
     * Popola le stringhe dei menu WP nel DB traduzioni (template_id=0, tile_id='wp-menu').
     */
    public function populate_menus() {
        $menus   = wp_get_nav_menus();
        $db      = new Olo_Lang_Database();
        $config  = Olo_Lang_Language::get_config();
        $langs   = Olo_Lang_Language::get_active_languages();
        $default = $config['default_lang'];
        $count   = 0;

        foreach ( $menus as $menu ) {
            $items = wp_get_nav_menu_items( $menu->term_id );
            if ( ! $items ) {
                continue;
            }
            foreach ( $items as $item ) {
                $title = trim( $item->title );
                if ( empty( $title ) ) {
                    continue;
                }
                foreach ( $langs as $lang ) {
                    if ( $lang['code'] === $default ) {
                        continue;
                    }
                    $db->save_translation( [
                        'template_id' => 0,
                        'tile_id'     => 'wp-menu',
                        'field_path'  => 'menu-item-' . $item->ID,
                        'lang'        => $lang['code'],
                        'original'    => $title,
                        'translation' => '',
                        'status'      => 'bozza',
                    ] );
                    $count++;
                }
            }
        }

        return rest_ensure_response( [ 'populated' => $count ] );
    }

    /**
     * Popola le stringhe dei plugin nel DB traduzioni (template_id=0, tile_id='plugin-{slug}').
     */
    public function populate_plugins() {
        if ( ! class_exists( 'Olo_Lang_Plugin_Scanner' ) ) {
            return new WP_Error( 'dependency', 'Plugin scanner non trovato.', [ 'status' => 500 ] );
        }

        $all     = Olo_Lang_Plugin_Scanner::get_all_strings();
        $db      = new Olo_Lang_Database();
        $config  = Olo_Lang_Language::get_config();
        $langs   = Olo_Lang_Language::get_active_languages();
        $default = $config['default_lang'];
        $count   = 0;

        foreach ( $all as $idx => $s ) {
            $tile_id    = 'plugin-' . ( $s['plugin'] ?? 'unknown' );
            $field_path = $s['context'] . '-' . $idx;

            foreach ( $langs as $lang ) {
                if ( $lang['code'] === $default ) {
                    continue;
                }
                $db->save_translation( [
                    'template_id' => 0,
                    'tile_id'     => $tile_id,
                    'field_path'  => $field_path,
                    'lang'        => $lang['code'],
                    'original'    => $s['string'],
                    'translation' => '',
                    'status'      => 'bozza',
                ] );
                $count++;
            }
        }

        return rest_ensure_response( [ 'populated' => $count ] );
    }

    /**
     * Elenco stringhe globali per lingua.
     */
    public function get_global_strings( $request ) {
        $lang    = sanitize_text_field( $request['lang'] );
        $tile_id = sanitize_text_field( $request->get_param( 'tile_id' ) ?? '' );

        $db      = new Olo_Lang_Database();
        $strings = $db->get_global_strings( $lang, $tile_id );

        return rest_ensure_response( [
            'lang'    => $lang,
            'tile_id' => $tile_id,
            'strings' => $strings,
            'count'   => count( $strings ),
        ] );
    }

    /**
     * Salva bulk stringhe globali.
     */
    public function save_global_strings( $request ) {
        $lang         = sanitize_text_field( $request['lang'] );
        $body         = $request->get_json_params();
        $translations = $body['translations'] ?? [];

        $db    = new Olo_Lang_Database();
        $saved = $db->save_translations_bulk( 0, $lang, $translations );

        return rest_ensure_response( [
            'lang'  => $lang,
            'saved' => $saved,
        ] );
    }

    /**
     * Aggiunge una stringa custom (template_id=0, tile_id='custom').
     */
    public function add_custom_string( $request ) {
        $body     = $request->get_json_params();
        $original = trim( $body['original'] ?? '' );
        $lang     = sanitize_text_field( $body['lang'] ?? '' );
        $trans    = trim( $body['translation'] ?? '' );

        if ( empty( $original ) || empty( $lang ) ) {
            return new WP_Error( 'missing_data', 'Originale e lingua obbligatori.', [ 'status' => 400 ] );
        }

        $db = new Olo_Lang_Database();
        $id = $db->save_translation( [
            'template_id' => 0,
            'tile_id'     => 'custom',
            'field_path'  => 'custom-' . md5( $original ),
            'lang'        => $lang,
            'original'    => $original,
            'translation' => $trans,
            'status'      => $trans ? 'tradotto' : 'bozza',
        ] );

        return rest_ensure_response( [ 'id' => $id ] );
    }

    /**
     * Elimina una stringa globale per ID.
     */
    public function delete_global_string( $request ) {
        $id = absint( $request['id'] );
        $db = new Olo_Lang_Database();
        $db->delete_global_string( $id );

        return rest_ensure_response( [ 'deleted' => true ] );
    }
}
