<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Olo_Theme_Importer {

    public static function get_themes() {
        $themes_dir = OLO_PATH . 'assets/data/themes/';
        $themes = [];
        if ( ! is_dir( $themes_dir ) ) return $themes;

        foreach ( glob( $themes_dir . '*/theme.json' ) as $file ) {
            $data = json_decode( file_get_contents( $file ), true );
            if ( ! $data || empty( $data['id'] ) ) continue;
            $dir = dirname( $file );
            $theme_id = basename( $dir );
            $screenshot = '';
            foreach ( [ 'screenshot.jpg', 'screenshot.png', 'screenshot.webp' ] as $ext ) {
                if ( file_exists( $dir . '/' . $ext ) ) {
                    $screenshot = OLO_URL . 'assets/data/themes/' . $theme_id . '/' . $ext;
                    break;
                }
            }
            $themes[] = [
                'id' => $data['id'], 'name' => $data['name'] ?? $theme_id,
                'description' => $data['description'] ?? '', 'author' => $data['author'] ?? '',
                'version' => $data['version'] ?? '1.0', 'screenshot' => $screenshot,
                'tags' => $data['tags'] ?? [], 'dir' => $dir,
            ];
        }
        return $themes;
    }

    public static function import_theme( $theme_id ) {
        $themes = self::get_themes();
        $theme = null;
        foreach ( $themes as $t ) { if ( $t['id'] === $theme_id ) { $theme = $t; break; } }
        if ( ! $theme ) return new WP_Error( 'not_found', 'Tema non trovato: ' . $theme_id );

        $dir = $theme['dir'];
        $theme_json = json_decode( file_get_contents( $dir . '/theme.json' ), true );
        $db = new Olo_Database();
        $results = [ 'templates' => [], 'styles' => false, 'activated' => [] ];

        // ── Step 1: Copy logos to uploads ──
        $upload_dir = wp_upload_dir();
        $logo_url = '';
        $logo_light_url = '';
        foreach ( [ 'logo.png' => 'olobuild-logo.png', 'logo-light.png' => 'olobuild-logo-light.png' ] as $src_file => $dest_file ) {
            $src = $dir . '/' . $src_file;
            if ( file_exists( $src ) ) {
                $dest = $upload_dir['basedir'] . '/' . $dest_file;
                copy( $src, $dest );
                $url = $upload_dir['baseurl'] . '/' . $dest_file;
                if ( $src_file === 'logo.png' ) $logo_url = $url;
                else $logo_light_url = $url;
            }
        }
        if ( ! $logo_light_url ) $logo_light_url = $logo_url;

        // ── Step 2: Create demo menu ──
        $menu_id = 0;
        if ( ! empty( $theme_json['menu'] ) ) {
            $menu_name = $theme_json['menu']['name'] ?? $theme_json['name'] . ' Menu';
            $menu_id = wp_create_nav_menu( $menu_name );
            if ( is_wp_error( $menu_id ) ) {
                // Menu might already exist — get its ID
                $existing = wp_get_nav_menu_object( $menu_name );
                $menu_id = $existing ? $existing->term_id : 0;
            }
            if ( $menu_id ) {
                foreach ( $theme_json['menu']['items'] ?? [] as $item ) {
                    wp_update_nav_menu_item( $menu_id, 0, [
                        'menu-item-title'  => $item['title'] ?? '',
                        'menu-item-url'    => $item['url'] ?? '#',
                        'menu-item-status' => 'publish',
                        'menu-item-type'   => 'custom',
                    ] );
                }
                $results['menu'] = [ 'id' => $menu_id, 'name' => $menu_name ];
            }
        }

        // ── Step 3: Import templates with placeholders already replaced ──
        $template_files = $theme_json['templates'] ?? [];
        $id_map = [];

        foreach ( $template_files as $key => $tpl_meta ) {
            $tpl_file = $dir . '/' . ( $tpl_meta['file'] ?? $key . '.json' );
            if ( ! file_exists( $tpl_file ) ) continue;

            // Load JSON as string first for placeholder replacement
            $json_str = file_get_contents( $tpl_file );

            // Replace logo placeholders
            if ( $logo_url ) {
                $json_str = str_replace( 'LOGO_PLACEHOLDER', $logo_url, $json_str );
            }

            // Replace menu_id "auto" with actual menu ID
            if ( $menu_id ) {
                $json_str = str_replace( '"menu_id":"auto"', '"menu_id":' . intval( $menu_id ), $json_str );
            }

            $content = json_decode( $json_str, true );
            if ( ! $content ) continue;

            // Regenerate all IDs
            self::regenerate_ids( $content );

            $type  = $tpl_meta['type'] ?? 'page';
            $title = $tpl_meta['title'] ?? ucfirst( $key );

            $new_id = $db->create_template( [
                'title'   => $title,
                'type'    => $type,
                'content' => $content,
                'status'  => 'published',
            ] );

            if ( $new_id ) {
                $id_map[ $key ] = $new_id;
                $results['templates'][] = [ 'key' => $key, 'id' => $new_id, 'title' => $title, 'type' => $type ];
            }
        }

        // ── Step 4: Activate header/footer/404 ──
        if ( ! empty( $theme_json['activate'] ) ) {
            $act = $theme_json['activate'];
            if ( ! empty( $act['header'] ) && isset( $id_map[ $act['header'] ] ) ) {
                update_option( 'olo_active_header', $id_map[ $act['header'] ] );
                $results['activated'][] = 'header';
            }
            if ( ! empty( $act['footer'] ) && isset( $id_map[ $act['footer'] ] ) ) {
                update_option( 'olo_active_footer', $id_map[ $act['footer'] ] );
                $results['activated'][] = 'footer';
            }
            if ( ! empty( $act['404'] ) && isset( $id_map[ $act['404'] ] ) ) {
                update_option( 'olo_active_404', $id_map[ $act['404'] ] );
                $results['activated'][] = '404';
            }
        }

        // ── Step 5: Apply global styles ──
        if ( ! empty( $theme_json['styles'] ) ) {
            $current = get_option( 'olo_styles', [] );
            $merged = wp_parse_args( $theme_json['styles'], is_array( $current ) ? $current : [] );
            update_option( 'olo_styles', $merged );
            $results['styles'] = true;
        }

        // ── Step 6: Create pages and assign templates ──
        if ( ! empty( $theme_json['pages'] ) ) {
            foreach ( $theme_json['pages'] as $page_key => $page_meta ) {
                $tpl_key = $page_meta['template'] ?? $page_key;
                if ( ! isset( $id_map[ $tpl_key ] ) ) continue;

                $page_id = wp_insert_post( [
                    'post_title'  => $page_meta['title'] ?? ucfirst( $page_key ),
                    'post_status' => 'publish',
                    'post_type'   => 'page',
                    'post_content' => '',
                ] );

                if ( $page_id && ! is_wp_error( $page_id ) ) {
                    update_post_meta( $page_id, '_olo_template_id', $id_map[ $tpl_key ] );
                    if ( ! empty( $page_meta['set_as_homepage'] ) ) {
                        update_option( 'page_on_front', $page_id );
                        update_option( 'show_on_front', 'page' );
                    }
                    if ( ! empty( $page_meta['set_as_blog'] ) ) {
                        update_option( 'page_for_posts', $page_id );
                    }
                }
            }
        }

        // Mark setup complete
        update_option( 'olo_setup_complete', true );

        return $results;
    }

    private static function regenerate_ids( &$nodes ) {
        if ( ! is_array( $nodes ) ) return;
        foreach ( $nodes as &$node ) {
            if ( isset( $node['id'] ) ) $node['id'] = wp_generate_uuid4();
            if ( ! empty( $node['children'] ) ) self::regenerate_ids( $node['children'] );
            if ( ! empty( $node['settings']['columns_data'] ) ) {
                foreach ( $node['settings']['columns_data'] as &$col ) {
                    if ( isset( $col['id'] ) ) $col['id'] = wp_generate_uuid4();
                    if ( ! empty( $col['tiles'] ) ) self::regenerate_ids( $col['tiles'] );
                }
            }
        }
    }
}
