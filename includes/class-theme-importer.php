<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Olobuild_Theme_Importer {

    public static function get_themes() {
        $themes_dir = OLOBUILD_PATH . 'assets/data/themes/';
        $themes = [];
        if ( ! is_dir( $themes_dir ) ) return $themes;

        foreach ( glob( $themes_dir . '*/theme.json' ) as $file ) {
            $data = json_decode( file_get_contents( $file ), true );
            if ( ! $data || empty( $data['id'] ) ) continue;
            $dir = dirname( $file );
            $theme_id = basename( $dir );
            // Anteprima (screenshot): servita dalla LIBRERIA REMOTA su olotheme.com, così lo
            // zip del plugin resta leggero (le immagini NON sono nel pacchetto wp.org). Il
            // contenuto dei template resta locale → l'import funziona anche offline. Base URL
            // filtrabile via 'olobuild_library_url'; impostandola a '' si torna agli screenshot
            // locali se presenti (dev/air-gapped). Il theme-picker ha comunque un fallback ad
            // anteprima sintetica dai token se l'immagine manca o non carica.
            $screenshot = '';
            $lib = rtrim( (string) apply_filters( 'olobuild_library_url', 'https://olotheme.com/olobuild-library' ), '/' );
            if ( $lib !== '' ) {
                $screenshot = $lib . '/themes/' . $theme_id . '/screenshot.jpg';
            } else {
                foreach ( [ 'screenshot.jpg', 'screenshot.png', 'screenshot.webp' ] as $ext ) {
                    if ( file_exists( $dir . '/' . $ext ) ) {
                        $screenshot = OLOBUILD_URL . 'assets/data/themes/' . $theme_id . '/' . $ext;
                        break;
                    }
                }
            }
            $themes[] = array_merge( [
                'id' => $data['id'], 'name' => $data['name'] ?? $theme_id,
                'description' => $data['description'] ?? '', 'author' => $data['author'] ?? '',
                'version' => $data['version'] ?? '1.0', 'screenshot' => $screenshot,
                'tags' => $data['tags'] ?? [], 'dir' => $dir,
            ], self::build_preview( $data, $theme_id ) );
        }
        return $themes;
    }

    /**
     * Campi "visivi" per la mini-anteprima del modale Importa Temi (redesign).
     * Derivati dai token del tema (styles.colors / styles.typography), con
     * fallback robusti: temi privi di questi dati restano comunque importabili.
     * Le chiavi salvate del tema NON vengono modificate.
     */
    private static function build_preview( $data, $theme_id ) {
        $styles = ( isset( $data['styles'] ) && is_array( $data['styles'] ) ) ? $data['styles'] : [];
        $colors = ( isset( $styles['colors'] ) && is_array( $styles['colors'] ) ) ? $styles['colors'] : [];
        $typo   = ( isset( $styles['typography'] ) && is_array( $styles['typography'] ) ) ? $styles['typography'] : [];

        $bg        = $colors['background'] ?? '#0e1626';
        $accent    = $colors['primary'] ?? '#e8622a';
        $text      = $colors['text'] ?? '#c9d2e0';
        $secondary = $colors['secondary'] ?? $accent;
        $muted     = $colors['muted'] ?? '#16223a';

        $lum   = self::rel_luminance( $bg );
        $light = ( $lum !== null && $lum > 0.55 );
        $ink   = ( isset( $data['preview']['ink'] ) && $data['preview']['ink'] )
            ? $data['preview']['ink']
            : self::pick_ink( $bg, $text );

        $font = $typo['font_family_heading'] ?? ( $typo['font_family'] ?? 'Inter, system-ui, sans-serif' );

        $pal = [];
        foreach ( [ $accent, $secondary, $muted, $ink ] as $c ) { if ( $c ) $pal[] = $c; }

        $gfonts = ( isset( $styles['google_fonts'] ) && is_array( $styles['google_fonts'] ) )
            ? array_values( $styles['google_fonts'] ) : [];

        // Link alla pagina del tema. Predisposto: vuoto finché olotheme.com non avrà un
        // catalogo. Quando ci sarà, basta il filtro `olobuild_theme_catalog_url` (o un `url` in
        // theme.json) → il link compare in automatico su tutte le card, zero altre modifiche.
        $catalog = apply_filters( 'olobuild_theme_catalog_url', '' );
        $url = ( isset( $data['url'] ) && $data['url'] ) ? $data['url']
            : ( $catalog ? rtrim( $catalog, '/' ) . '/' . $theme_id . '/' : '' );

        return [
            'category'     => ( isset( $data['category'] ) && $data['category'] !== '' ) ? $data['category'] : self::theme_category( $theme_id, $data ),
            'zone'         => $data['zone'] ?? self::theme_zone( $theme_id ),
            'accent'       => $accent,
            'bg'           => $bg,
            'ink'          => $ink,
            'font'         => $font,
            'light'        => $light,
            'pal'          => $pal,
            'google_fonts' => $gfonts,
            'url'          => $url,
        ];
    }

    /** #rgb / #rrggbb → [r,g,b] (0-255), oppure null se non è un hex valido. */
    private static function hex_rgb( $hex ) {
        $hex = ltrim( (string) $hex, '#' );
        if ( strlen( $hex ) === 3 ) { $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2]; }
        if ( strlen( $hex ) < 6 || ! ctype_xdigit( substr( $hex, 0, 6 ) ) ) return null;
        return [ hexdec( substr( $hex, 0, 2 ) ), hexdec( substr( $hex, 2, 2 ) ), hexdec( substr( $hex, 4, 2 ) ) ];
    }

    /** Luminanza relativa WCAG (0..1), null se il colore non è hex. */
    private static function rel_luminance( $hex ) {
        $rgb = self::hex_rgb( $hex );
        if ( ! $rgb ) return null;
        $c = array_map( function ( $v ) {
            $v /= 255;
            return $v <= 0.03928 ? $v / 12.92 : pow( ( $v + 0.055 ) / 1.055, 2.4 );
        }, $rgb );
        return 0.2126 * $c[0] + 0.7152 * $c[1] + 0.0722 * $c[2];
    }

    /** Colore "ink" (titolo dell'anteprima): forte contrasto sullo sfondo, fedele al tema quando possibile. */
    private static function pick_ink( $bg, $text ) {
        $lbg = self::rel_luminance( $bg );
        if ( $lbg === null ) $lbg = 0.05; // assume scuro
        $ltx = self::rel_luminance( $text );
        if ( $lbg > 0.55 ) { // sfondo chiaro → ink scuro
            return ( $ltx !== null && $ltx <= 0.42 ) ? $text : '#17171b';
        }
        // sfondo scuro → ink chiaro
        return ( $ltx !== null && $ltx >= 0.62 ) ? $text : '#f3f5fb';
    }

    /** Categoria canonica dei 50 OLOtheme (override via theme.json `category`, fallback al primo tag). */
    private static function theme_category( $theme_id, $data ) {
        static $map = [
            'atelier' => 'Beauty & Fashion', 'aurora' => 'Events', 'bloom' => 'Beauty & Fashion',
            'brewline' => 'Food & Drink', 'cadence' => 'Health & Fitness', 'canvas' => 'Artist',
            'capital-row' => 'Consulting & Finance', 'carrello' => 'E-commerce', 'circuit' => 'Software & Tech',
            'contour' => 'Health & Fitness', 'datafold' => 'Software & Tech', 'dispatch' => 'Media & News',
            'fieldco' => 'E-commerce', 'fiori' => 'Wedding', 'fjordline' => 'Travel', 'forge' => 'Software & Tech',
            'frame' => 'Media & News', 'gazette' => 'Media & News', 'hearth' => 'Home & Living',
            'honeycomb' => 'Food & Drink', 'kiln' => 'Artist', 'ledger' => 'Consulting & Finance',
            'linea' => 'Beauty & Fashion', 'loft' => 'Home & Living', 'lumen' => 'Beauty & Fashion',
            'maison' => 'Home & Living', 'mercato' => 'E-commerce', 'meridian' => 'Consulting & Finance',
            'mono' => 'Creative', 'nimbus' => 'Software & Tech', 'pasaje' => 'Travel', 'prisma' => 'Creative',
            'pulse' => 'Health & Fitness', 'relayos' => 'Software & Tech', 'saffron' => 'Food & Drink',
            'signal' => 'Media & News', 'soundwave' => 'Artist', 'sterling' => 'Consulting & Finance',
            'synapse' => 'Software & Tech', 'tavola' => 'Food & Drink', 'terra' => 'Home & Living',
            'vela' => 'Creative', 'velour' => 'Beauty & Fashion', 'verdano' => 'Health & Fitness',
            'verde' => 'Food & Drink', 'vinea' => 'Food & Drink', 'vitalis' => 'Health & Fitness',
            'vows' => 'Wedding', 'voyage' => 'Travel', 'wander' => 'Travel',
        ];
        if ( isset( $map[ $theme_id ] ) ) return $map[ $theme_id ];
        $tags = $data['tags'] ?? [];
        if ( ! empty( $tags[0] ) ) return ucwords( str_replace( [ '-', '_' ], ' ', $tags[0] ) );
        return 'Tema';
    }

    /** Badge "zona interattiva" dei temi che ne hanno una (override via theme.json `zone`). */
    private static function theme_zone( $theme_id ) {
        static $map = [
            'atelier' => 'Finder', 'bloom' => 'Routine', 'brewline' => 'Builder', 'cadence' => 'Finder',
            'canvas' => 'Mixer', 'capital-row' => 'Projector', 'carrello' => 'Builder', 'circuit' => 'Builder',
            'contour' => 'Finder', 'fieldco' => 'Builder', 'fjordline' => 'Finder', 'forge' => 'Contrast',
            'hearth' => 'Finder', 'honeycomb' => 'Builder', 'kiln' => 'Mixer', 'ledger' => 'Projector',
            'linea' => 'Finder', 'loft' => 'Mixer', 'lumen' => 'Finder', 'maison' => 'Finder',
            'mercato' => 'Builder', 'meridian' => 'Finder', 'mono' => 'Type tester', 'nimbus' => 'Projector',
            'pasaje' => 'Finder', 'prisma' => 'Mixer', 'pulse' => 'Finder', 'relayos' => 'Finder',
            'saffron' => 'Finder', 'soundwave' => 'Sequencer', 'sterling' => 'Projector', 'synapse' => 'Projector',
            'tavola' => 'Builder', 'terra' => 'Finder', 'vela' => 'Finder', 'velour' => 'Mixer',
            'verdano' => 'Builder', 'verde' => 'Builder', 'vinea' => 'Finder', 'vitalis' => 'Finder',
            'voyage' => 'Route',
        ];
        return $map[ $theme_id ] ?? '';
    }

    public static function import_theme( $theme_id ) {
        $themes = self::get_themes();
        $theme = null;
        foreach ( $themes as $t ) { if ( $t['id'] === $theme_id ) { $theme = $t; break; } }
        if ( ! $theme ) return new WP_Error( 'not_found', 'Tema non trovato: ' . $theme_id );

        $dir = $theme['dir'];
        $theme_json = json_decode( file_get_contents( $dir . '/theme.json' ), true );
        $db = new Olobuild_Database();
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
                // Svuota le voci esistenti: senza questo il menu cresce a ogni re-import (voci duplicate).
                $existing_items = wp_get_nav_menu_items( $menu_id );
                if ( is_array( $existing_items ) ) {
                    foreach ( $existing_items as $mi ) {
                        wp_delete_post( (int) $mi->ID, true );
                    }
                }
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

            // Replace menu_id "auto" with actual menu ID.
            // Regex tollerante agli spazi: i JSON pretty-printed hanno '"menu_id": "auto"'
            // (con spazio) → uno str_replace senza spazio fallirebbe e lascerebbe menu_id="auto"
            // (absint("auto")=0 → megamenu "Seleziona un menu"). Vedi gotcha header Atelier.
            if ( $menu_id ) {
                $json_str = preg_replace( '/"menu_id"\s*:\s*"auto"/', '"menu_id":' . intval( $menu_id ), $json_str );
            }

            $content = json_decode( $json_str, true );
            if ( ! $content ) continue;

            // Formato file: lista di nodi (classico) OPPURE oggetto {settings, content}
            // (esteso: porta anche i page settings del template, es. effetti di pagina
            // page_grain_* / page_crt_* — tema "Clod — Evoluzione").
            $tpl_settings = [];
            if ( isset( $content['content'] ) && is_array( $content['content'] ) && ! isset( $content[0] ) ) {
                $tpl_settings = ( isset( $content['settings'] ) && is_array( $content['settings'] ) ) ? $content['settings'] : [];
                $content      = $content['content'];
            }

            // Regenerate all IDs
            self::regenerate_ids( $content );

            $type  = $tpl_meta['type'] ?? 'page';
            $title = $tpl_meta['title'] ?? ucfirst( $key );

            $new_id = $db->create_template( [
                'title'    => $title,
                'type'     => $type,
                'content'  => $content,
                'settings' => $tpl_settings,
                'status'   => 'published',
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
                update_option( 'olobuild_active_header', $id_map[ $act['header'] ] );
                $results['activated'][] = 'header';
            }
            if ( ! empty( $act['footer'] ) && isset( $id_map[ $act['footer'] ] ) ) {
                update_option( 'olobuild_active_footer', $id_map[ $act['footer'] ] );
                $results['activated'][] = 'footer';
            }
            if ( ! empty( $act['404'] ) && isset( $id_map[ $act['404'] ] ) ) {
                update_option( 'olobuild_active_404', $id_map[ $act['404'] ] );
                $results['activated'][] = '404';
            }
        }

        // ── Step 5: Apply global styles ──
        if ( ! empty( $theme_json['styles'] ) ) {
            $current = get_option( 'olobuild_styles', [] );
            $merged = wp_parse_args( $theme_json['styles'], is_array( $current ) ? $current : [] );
            update_option( 'olobuild_styles', $merged );
            $results['styles'] = true;

            // Allinea le palette derivate ai colori del tema: senza questo i
            // olo_global_colors[core] (che VINCONO in generate_css) e i dark_colors restano
            // placeholder e SOVRASCRIVONO il tema (primario verde/indaco invece del brand).
            if ( ! empty( $merged['colors'] ) && is_array( $merged['colors'] ) && class_exists( 'Olobuild_Style_System' ) ) {
                $ss = Olobuild_Style_System::instance();
                $ss->sync_global_palette( $merged['colors'] );
                // Allinea i ruoli brand del dark SOLO se il tema non porta una propria palette dark.
                if ( empty( $theme_json['styles']['dark_colors'] ) ) {
                    $ss->sync_dark_palette( $merged['colors'] );
                }
            }
        }

        // ── Step 5b: Global feature — cursore custom (Olobuild_Magnetic_Cursor) ──
        // Il tema può attivare/configurare il cursore neon (anello+dot, blend, pull).
        // Se la chiave 'cursor' manca, il cursore resta com'è (nessuna regressione).
        if ( ! empty( $theme_json['cursor'] ) && is_array( $theme_json['cursor'] ) ) {
            if ( ! class_exists( 'Olobuild_Magnetic_Cursor' ) ) {
                require_once OLOBUILD_PATH . 'includes/class-magnetic-cursor.php';
            }
            update_option( 'olobuild_magnetic_cursor', Olobuild_Magnetic_Cursor::sanitize( $theme_json['cursor'] ) );
            $results['cursor'] = true;
        }

        // ── Step 5c: Global feature — HUD mirino (Olobuild_Cursor_Hud) ──
        // Crosshair + coordinate + label sezione corrente (tema "sala di regia").
        // Se la chiave 'cursor_hud' manca, l'HUD resta com'è (nessuna regressione).
        if ( ! empty( $theme_json['cursor_hud'] ) && is_array( $theme_json['cursor_hud'] ) ) {
            if ( ! class_exists( 'Olobuild_Cursor_Hud' ) ) {
                require_once OLOBUILD_PATH . 'includes/class-cursor-hud.php';
            }
            update_option( 'olobuild_cursor_hud', Olobuild_Cursor_Hud::sanitize( $theme_json['cursor_hud'] ) );
            $results['cursor_hud'] = true;
        }

        // ── Step 6: Create pages and assign templates ──
        // Idempotente: per la homepage RIUSA la static front page esistente (aggiorna
        // _olo_template_id in-place) invece di creare una nuova pagina "Home" ad ogni
        // import — evita la proliferazione di duplicati e l'ambiguità su quale pagina sia
        // davvero la home. Stessa logica per la pagina blog (page_for_posts).
        if ( ! empty( $theme_json['pages'] ) ) {
            foreach ( $theme_json['pages'] as $page_key => $page_meta ) {
                $tpl_key = $page_meta['template'] ?? $page_key;
                if ( ! isset( $id_map[ $tpl_key ] ) ) continue;

                $is_home = ! empty( $page_meta['set_as_homepage'] );
                $is_blog = ! empty( $page_meta['set_as_blog'] );
                $title   = $page_meta['title'] ?? ucfirst( $page_key );

                // Slug dichiarato dal tema (facoltativo). Senza, WordPress lo ricava dal
                // titolo, e quello che ne esce non e' sempre quello che il menu del tema
                // si aspetta: sanitize_title("L'ora di lezione") da' "lora-di-lezione",
                // mentre il menu punta a "/l-ora-di-lezione/" — cioe' un 404 sulla prima
                // voce, da correggere a mano dopo ogni import.
                $slug = isset( $page_meta['slug'] ) ? sanitize_title( $page_meta['slug'] ) : '';

                // Riuso della pagina già assegnata a quel ruolo (home/blog), se esiste ancora.
                $page_id = 0;
                if ( $is_home ) {
                    $existing = (int) get_option( 'page_on_front' );
                    if ( $existing && get_post( $existing ) && get_post_type( $existing ) === 'page' ) {
                        $page_id = $existing;
                    }
                } elseif ( $is_blog ) {
                    $existing = (int) get_option( 'page_for_posts' );
                    if ( $existing && get_post( $existing ) && get_post_type( $existing ) === 'page' ) {
                        $page_id = $existing;
                    }
                }

                // Chi dichiara uno slug sta dicendo «questa pagina vive a QUESTO
                // indirizzo»: se ci abita gia' una pagina, si aggiorna quella. Senza
                // questo, il secondo import produrrebbe "…-2" e lo slug dichiarato non
                // servirebbe a niente proprio dalla seconda volta in poi. Vale solo per
                // chi lo dichiara: i temi che non hanno la chiave si comportano come prima.
                if ( ! $page_id && $slug !== '' ) {
                    $gia = get_page_by_path( $slug );
                    if ( $gia && 'page' === $gia->post_type ) {
                        $page_id = (int) $gia->ID;
                    }
                }

                if ( $page_id ) {
                    // Lo slug di una pagina che esisteva gia' non si tocca: e' un indirizzo
                    // pubblicato, e cambiarlo romperebbe i collegamenti di chi ce l'ha.
                    wp_update_post( [ 'ID' => $page_id, 'post_title' => $title, 'post_status' => 'publish' ] );
                } else {
                    $nuova = [
                        'post_title'   => $title,
                        'post_status'  => 'publish',
                        'post_type'    => 'page',
                        'post_content' => '',
                    ];
                    if ( $slug !== '' ) {
                        $nuova['post_name'] = $slug;
                    }
                    $page_id = wp_insert_post( $nuova );
                }

                if ( $page_id && ! is_wp_error( $page_id ) ) {
                    update_post_meta( $page_id, '_olo_template_id', $id_map[ $tpl_key ] );
                    if ( $is_home ) {
                        update_option( 'page_on_front', $page_id );
                        update_option( 'show_on_front', 'page' );
                    }
                    if ( $is_blog ) {
                        update_option( 'page_for_posts', $page_id );
                    }
                }
            }
        }

        // Mark setup complete
        update_option( 'olobuild_setup_complete', true );

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
