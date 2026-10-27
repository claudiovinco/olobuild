<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Olobuild_Theme_Importer {

    public static function get_themes() {
        $themes_dir = OLOBUILD_PATH . 'assets/data/themes/';
        $themes = [];

        // Elenco dei theme.json da scansionare: quelli del plugin + quelli portati da plugin
        // esterni (es. olo-booking) via filtro `olobuild_external_theme_dirs`. Il filtro
        // riceve un array vuoto e torna cartelle assolute: ciascuna può contenere direttamente
        // un theme.json (singolo tema) o cartelle figlio con un theme.json ciascuna (bundle di
        // più temi dallo stesso plugin). Vedi README-themes.md del plugin esterno.
        $files = [];
        if ( is_dir( $themes_dir ) ) {
            $local = glob( $themes_dir . '*/theme.json' );
            if ( $local ) $files = $local;
        }
        $external_dirs = (array) apply_filters( 'olobuild_external_theme_dirs', [] );
        foreach ( $external_dirs as $extra ) {
            $extra = rtrim( (string) $extra, '/\\' );
            if ( '' === $extra || ! is_dir( $extra ) ) continue;
            if ( file_exists( $extra . '/theme.json' ) ) {
                $files[] = $extra . '/theme.json';
                continue;
            }
            $extra_files = glob( $extra . '/*/theme.json' );
            if ( $extra_files ) $files = array_merge( $files, $extra_files );
        }

        foreach ( $files as $file ) {
            $data = json_decode( file_get_contents( $file ), true );
            if ( ! $data || empty( $data['id'] ) ) continue;
            // Tema non ancora finito (theme.json → in_lavorazione): la sua demo si apre dall'indirizzo
            // (Olobuild_Demo_Temi legge la cartella), ma il selettore, la Vetrina e l'importazione non lo vedono.
            if ( ! empty( $data['in_lavorazione'] ) ) continue;
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
                // Dalla versione 2 l'anteprima ha un file suo (screenshot-<preview_v>.jpg): rifarla sulla
                // libreria remota non cambia quella dei siti col plugin di prima, che leggono ancora
                // screenshot.jpg e importano i temi che quell'immagine mostra.
                $pv   = isset( $data['preview_v'] ) ? (string) $data['preview_v'] : '';
                $file = ( '' === $pv || '1' === $pv ) ? 'screenshot.jpg' : 'screenshot-' . sanitize_file_name( $pv ) . '.jpg';
                $screenshot = $lib . '/themes/' . $theme_id . '/' . $file . '?v=' . rawurlencode( (string) ( $data['preview_v'] ?? ( $data['version'] ?? '1' ) ) );
            } else {
                foreach ( [ 'screenshot.jpg', 'screenshot.png', 'screenshot.webp' ] as $ext ) {
                    if ( file_exists( $dir . '/' . $ext ) ) {
                        $screenshot = OLOBUILD_URL . 'assets/data/themes/' . $theme_id . '/' . $ext;
                        break;
                    }
                }
            }
            // Anteprima del telefono (la home a 390 px): file suo sulla libreria remota,
            // screenshot-mobile-<preview_mobile_v>.jpg. Senza il campo nel tema, o senza libreria,
            // resta vuota e la Vetrina temi mostra solo quella del computer.
            $screenshot_mobile = '';
            $pm = isset( $data['preview_mobile_v'] ) ? sanitize_file_name( (string) $data['preview_mobile_v'] ) : '';
            if ( $lib !== '' && $pm !== '' ) {
                $screenshot_mobile = $lib . '/themes/' . $theme_id . '/screenshot-mobile-' . $pm . '.jpg?v=' . rawurlencode( $pm );
            }
            $themes[] = array_merge( [
                'id' => $data['id'], 'name' => $data['name'] ?? $theme_id,
                'description' => $data['description'] ?? '', 'author' => $data['author'] ?? '',
                'version' => $data['version'] ?? '1.0', 'screenshot' => $screenshot,
                'screenshot_mobile' => $screenshot_mobile,
                'tags' => $data['tags'] ?? [], 'dir' => $dir,
                // Dipendenza da un plugin esterno (schema: vedi check_requires_plugin).
                // Null = tema autonomo (99/100). Il selettore mostra badge + «Scopri» quando
                // ne serve uno che il sito non ha; i temi locali del plugin esterno (dir presente)
                // sono comunque importabili se il plugin è attivo, perché la cartella c'è.
                'requires_plugin' => isset( $data['requires_plugin'] ) && is_array( $data['requires_plugin'] )
                    ? $data['requires_plugin'] : null,
                'is_remote_only' => false,
            ], self::build_preview( $data, $theme_id ) );
        }

        // Merge con il catalog remoto: temi che non vivono dentro questa installazione (perché
        // il loro plugin non è installato) → card informativa con «Scopri X» invece di
        // «Importa tema». Dedup per id: un tema locale (anche portato da plugin esterno via
        // olobuild_external_theme_dirs) vince sulla sua controparte remota.
        if ( ! class_exists( 'Olobuild_Theme_Remote_Catalog' ) ) {
            require_once OLOBUILD_PATH . 'includes/class-theme-remote-catalog.php';
        }
        $existing = wp_list_pluck( $themes, 'id' );
        foreach ( Olobuild_Theme_Remote_Catalog::fetch() as $remote ) {
            if ( empty( $remote['id'] ) || in_array( $remote['id'], $existing, true ) ) continue;
            $themes[] = self::normalize_remote_theme( $remote );
        }

        // Valutazione requires_plugin → requires_plugin_ok (boolean). Il selettore mostra
        // «Scopri {label}» (link al purchase_url) invece di «Importa tema» quando è false.
        // requires_plugin_status per la riga «Requisiti» della card: ok, old (plugin attivo ma
        // più vecchio di min_version, o tema che arriva con una sua versione successiva),
        // missing (plugin non attivo). Un plugin più vecchio di min_version non importa il
        // tema: le sue tile potrebbero non esserci ancora.
        foreach ( $themes as &$t ) {
            if ( empty( $t['requires_plugin'] ) ) {
                $t['requires_plugin_ok']     = true;
                $t['requires_plugin_status'] = '';
                continue;
            }
            $req      = $t['requires_plugin'];
            $attivo   = self::check_requires_plugin( $req );
            $min      = isset( $req['min_version'] ) ? trim( (string) $req['min_version'] ) : '';
            $versione = ( $attivo && '' !== $min ) ? self::installed_plugin_version( $req ) : '';
            $vecchio  = '' !== $versione && version_compare( $versione, $min, '<' );
            $t['requires_plugin_ok']     = $attivo && ! $vecchio;
            $t['requires_plugin_status'] = ! $attivo ? 'missing' : ( ( $vecchio || ! empty( $t['is_remote_only'] ) ) ? 'old' : 'ok' );
            $t['requires_plugin']        = self::plugin_badge( $req );
        }
        unset( $t );

        return $themes;
    }

    /**
     * Un payload del catalog remoto nella stessa forma dei temi locali: niente `dir`
     * (non importabile: template non ci sono), `is_remote_only=true`, screenshot
     * dai campi `screenshot/screenshot_mobile` del JSON remoto o costruiti dalla
     * library remota con la stessa convenzione dei locali (preview_v).
     */
    private static function normalize_remote_theme( $r ) {
        $theme_id = (string) $r['id'];
        $screenshot        = isset( $r['screenshot'] ) ? (string) $r['screenshot'] : '';
        $screenshot_mobile = isset( $r['screenshot_mobile'] ) ? (string) $r['screenshot_mobile'] : '';
        if ( '' === $screenshot ) {
            $lib = rtrim( (string) apply_filters( 'olobuild_library_url', 'https://olotheme.com/olobuild-library' ), '/' );
            if ( '' !== $lib ) {
                $pv   = isset( $r['preview_v'] ) ? (string) $r['preview_v'] : '';
                $file = ( '' === $pv || '1' === $pv ) ? 'screenshot.jpg' : 'screenshot-' . sanitize_file_name( $pv ) . '.jpg';
                $screenshot = $lib . '/themes/' . $theme_id . '/' . $file . '?v=' . rawurlencode( (string) ( $r['preview_v'] ?? ( $r['version'] ?? '1' ) ) );
                $pm = isset( $r['preview_mobile_v'] ) ? sanitize_file_name( (string) $r['preview_mobile_v'] ) : '';
                if ( '' !== $pm ) {
                    $screenshot_mobile = $lib . '/themes/' . $theme_id . '/screenshot-mobile-' . $pm . '.jpg?v=' . rawurlencode( $pm );
                }
            }
        }
        return array_merge( [
            'id' => $theme_id, 'name' => $r['name'] ?? $theme_id,
            'description' => $r['description'] ?? '', 'author' => $r['author'] ?? '',
            'version' => $r['version'] ?? '1.0',
            'screenshot' => $screenshot, 'screenshot_mobile' => $screenshot_mobile,
            'tags' => $r['tags'] ?? [], 'dir' => '',
            'requires_plugin' => isset( $r['requires_plugin'] ) && is_array( $r['requires_plugin'] )
                ? $r['requires_plugin'] : null,
            'is_remote_only' => true,
        ], self::build_preview( $r, $theme_id ) );
    }

    /**
     * Valuta se la dipendenza plugin è soddisfatta su questo sito. Il campo `check`
     * del manifest è una stringa interpretata così:
     *   - constant:NOME_COSTANTE   → defined()
     *   - function:nome_funzione   → function_exists()
     *   - class:Nome_Classe        → class_exists()
     *   - plugin:cartella/file.php → is_plugin_active()
     * Senza `check` esplicito, se c'è `slug` fallback su is_plugin_active() con
     * «{slug}/{slug}.php». Qualsiasi errore di parsing → false (prudenza: meglio
     * mostrare «Scopri X» di un «Importa» che poi fallisce).
     */
    public static function check_requires_plugin( $req ) {
        if ( ! is_array( $req ) ) return false;
        $check = isset( $req['check'] ) ? (string) $req['check'] : '';
        if ( '' !== $check && false !== strpos( $check, ':' ) ) {
            list( $kind, $target ) = array_pad( explode( ':', $check, 2 ), 2, '' );
            $kind   = strtolower( trim( $kind ) );
            $target = trim( $target );
            if ( '' === $target ) return false;
            if ( 'constant' === $kind ) return defined( $target );
            if ( 'function' === $kind ) return function_exists( $target );
            if ( 'class' === $kind )    return class_exists( $target );
            if ( 'plugin' === $kind ) {
                if ( ! function_exists( 'is_plugin_active' ) ) {
                    require_once ABSPATH . 'wp-admin/includes/plugin.php';
                }
                return is_plugin_active( $target );
            }
            return false;
        }
        $slug = isset( $req['slug'] ) ? sanitize_key( (string) $req['slug'] ) : '';
        if ( '' === $slug ) return false;
        if ( ! function_exists( 'is_plugin_active' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        return is_plugin_active( $slug . '/' . $slug . '.php' );
    }

    /**
     * Versione installata del plugin richiesto, '' se non si sa. Prima il valore della
     * costante del `check`, se è un numero di versione (OLObooking: OLO_BOOK_ACCOMMODATION_THEMES
     * vale OLO_BOOK_VERSION); poi l'intestazione Version del file del plugin (`check`
     * plugin:cartella/file.php, oppure {slug}/{slug}.php). Versione ignota = nessun blocco.
     */
    private static function installed_plugin_version( $req ) {
        $check = isset( $req['check'] ) ? trim( (string) $req['check'] ) : '';
        if ( 0 === stripos( $check, 'constant:' ) ) {
            $nome = trim( substr( $check, 9 ) );
            $v    = ( '' !== $nome && defined( $nome ) ) ? constant( $nome ) : '';
            if ( is_string( $v ) && preg_match( '/^\d+(\.\d+)+/', $v ) ) return $v;
        }
        $file = 0 === stripos( $check, 'plugin:' ) ? trim( substr( $check, 7 ) ) : '';
        if ( '' === $file && ! empty( $req['slug'] ) ) {
            $slug = sanitize_key( (string) $req['slug'] );
            $file = '' !== $slug ? $slug . '/' . $slug . '.php' : '';
        }
        // validate_file: niente «..» né percorsi assoluti (il manifest può venire dal catalogo remoto).
        if ( '' === $file || 0 !== validate_file( $file ) || ! defined( 'WP_PLUGIN_DIR' ) ) return '';
        $path = WP_PLUGIN_DIR . '/' . $file;
        if ( ! is_readable( $path ) ) return '';
        if ( ! function_exists( 'get_plugin_data' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $dati = get_plugin_data( $path, false, false );
        return isset( $dati['Version'] ) ? (string) $dati['Version'] : '';
    }

    /**
     * Il bollino del plugin richiesto: il logo per le superfici chiare (`logo`) e quello per le
     * scure (`logo_light`), nella riga «Requisiti» delle card del selettore e della Vetrina.
     * I plugin della famiglia OLO hanno il loro in assets/img/plugins/; un plugin esterno dà il
     * suo col filtro `olobuild_requires_plugin_badge` ( [ 'logo' => url, 'logo_light' => url,
     * 'edition' => nome ], slug, requires_plugin ). Un logo scritto nel manifest del tema vince su tutti e due.
     *
     * Edizioni: un tema di OLObooking dice il modulo che usa (`module`), e il bollino mostra il logo di
     * quell'edizione, col nome del modulo sotto «booking». `edition` è quel nome: testo alternativo,
     * titolo e misura del bollino (il logo ha due righe e va disegnato più alto).
     */
    private static function plugin_badge( $req ) {
        $slug  = isset( $req['slug'] ) ? sanitize_key( (string) $req['slug'] ) : '';
        $noti  = [
            'olo-booking' => [ 'logo' => 'olo-booking.png', 'logo_light' => 'olo-booking-w.png' ],
        ];
        // modulo => nome scritto sul logo dell'edizione (assets/img/plugins/{slug}-{modulo}.png e -w.png)
        $edizioni = [
            'olo-booking' => [
                'accommodation' => 'accommodation',
                'appointments'  => 'appointments',
                'events'        => 'events',
                'real-estate'   => 'real-estate',
                'rentals'       => 'rentals',
                'restaurants'   => 'restaurant',
            ],
        ];
        $modulo = isset( $req['module'] ) ? sanitize_key( (string) $req['module'] ) : '';
        $badge  = [];
        if ( '' !== $modulo && isset( $edizioni[ $slug ][ $modulo ] ) ) {
            $badge = [
                'logo'       => OLOBUILD_URL . 'assets/img/plugins/' . $slug . '-' . $modulo . '.png',
                'logo_light' => OLOBUILD_URL . 'assets/img/plugins/' . $slug . '-' . $modulo . '-w.png',
                'edition'    => $edizioni[ $slug ][ $modulo ],
            ];
        } elseif ( isset( $noti[ $slug ] ) ) {
            foreach ( $noti[ $slug ] as $chiave => $png ) {
                $badge[ $chiave ] = OLOBUILD_URL . 'assets/img/plugins/' . $png;
            }
        }
        $badge = (array) apply_filters( 'olobuild_requires_plugin_badge', $badge, $slug, $req );
        // Un logo del manifest è un altro disegno: l'edizione vale solo se la scrive anche il manifest.
        $logo_proprio = ! empty( $req['logo'] ) || ! empty( $req['logo_light'] );
        $edizione     = isset( $req['edition'] ) ? (string) $req['edition'] : ( $logo_proprio ? '' : (string) ( $badge['edition'] ?? '' ) );
        $edizione     = sanitize_text_field( $edizione );
        if ( '' !== $edizione ) {
            $req['edition'] = $edizione;
        } else {
            unset( $req['edition'] );
        }
        foreach ( [ 'logo', 'logo_light' ] as $chiave ) {
            $url = isset( $req[ $chiave ] ) ? esc_url_raw( (string) $req[ $chiave ] ) : '';
            if ( '' === $url && isset( $badge[ $chiave ] ) ) {
                $url = esc_url_raw( (string) $badge[ $chiave ] );
            }
            if ( '' !== $url ) {
                $req[ $chiave ] = $url;
            } else {
                unset( $req[ $chiave ] );
            }
        }
        return $req;
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
            'category'     => olobuild_t( self::theme_category( $data ) ),
            'zone'         => isset( $data['zone'] ) ? olobuild_t( (string) $data['zone'] ) : '',
            'pages'        => isset( $data['pages'] ) && is_array( $data['pages'] ) ? count( $data['pages'] ) : 0,
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

    /** Categoria del tema (theme.json `category`), altrimenti il primo tag. */
    private static function theme_category( $data ) {
        if ( isset( $data['category'] ) && '' !== (string) $data['category'] ) {
            return (string) $data['category'];
        }
        $tags = $data['tags'] ?? [];
        if ( ! empty( $tags[0] ) ) return ucwords( str_replace( [ '-', '_' ], ' ', $tags[0] ) );
        return 'Tema';
    }

    public static function import_theme( $theme_id ) {
        $themes = self::get_themes();
        $theme = null;
        foreach ( $themes as $t ) { if ( $t['id'] === $theme_id ) { $theme = $t; break; } }
        if ( ! $theme ) return new WP_Error( 'not_found', 'Tema non trovato: ' . $theme_id );

        // Tema del catalog remoto senza plugin fornitore installato: niente template
        // in locale → non importabile. Il selettore mostra già «Scopri {label}»;
        // chi aggira la UI riceve l'errore qui.
        if ( ! empty( $theme['is_remote_only'] ) || empty( $theme['dir'] ) ) {
            $req   = ( is_array( $theme['requires_plugin'] ?? null ) ) ? $theme['requires_plugin'] : [];
            $label = ! empty( $req['label'] ) ? (string) $req['label'] : ( $req['slug'] ?? 'il plugin richiesto' );
            return new WP_Error( 'requires_plugin', sprintf( 'Per importare questo tema serve %s.', $label ), [ 'requires_plugin' => $req ] );
        }
        // Dipendenza dichiarata ma non ancora soddisfatta (plugin disattivato dopo registrazione):
        // guard di sicurezza, lo stato è ricalcolato in get_themes().
        if ( ! empty( $theme['requires_plugin'] ) && empty( $theme['requires_plugin_ok'] ) ) {
            $req   = $theme['requires_plugin'];
            $label = ! empty( $req['label'] ) ? (string) $req['label'] : ( $req['slug'] ?? 'il plugin richiesto' );
            if ( 'old' === ( $theme['requires_plugin_status'] ?? '' ) && ! empty( $req['min_version'] ) ) {
                return new WP_Error( 'requires_plugin', sprintf( 'Aggiorna %1$s alla %2$s o successiva prima di importare questo tema.', $label, (string) $req['min_version'] ), [ 'requires_plugin' => $req ] );
            }
            return new WP_Error( 'requires_plugin', sprintf( 'Attiva %s prima di importare questo tema.', $label ), [ 'requires_plugin' => $req ] );
        }

        $dir = $theme['dir'];
        $theme_json = json_decode( file_get_contents( $dir . '/theme.json' ), true );
        $db = new Olobuild_Database();
        $results = [ 'templates' => [], 'styles' => false, 'activated' => [], 'media' => [ 'copiate' => 0, 'riusate' => 0, 'fallite' => 0 ] ];

        // Le foto e i video del tema si copiano nella Libreria media (vedi Step 3): con
        // decine di file i 30 secondi di default possono non bastare.
        if ( function_exists( 'set_time_limit' ) ) {
            @set_time_limit( 300 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- alcuni hosting lo vietano: si continua col limite del server
        }

        // ── Step 0: istantanea dello stile del sito, PRIMA di cambiarlo ──
        // Header, footer e 404 attivi, stili, colori globali, cursori e pagina
        // iniziale: «Ripristina» (toast dopo l'import, Configurazione › Palette ›
        // Versioni dello stile) li rimette com'erano. Vale anche per il wizard.
        $snap_id = class_exists( 'Olobuild_Style_System' )
            ? Olobuild_Style_System::instance()->take_snapshot( 'import_tema', $theme_json['name'] ?? ( $theme['name'] ?? $theme_id ) )
            : '';
        $pagine_prima = [];

        // ── Step 1: Copy logos to uploads ──
        // Un file per tema: col nome unico il logo del tema importato dopo
        // sovrascriveva quello dei template già importati, anche dell'header che
        // «Ripristina» rimette in uso.
        $upload_dir = wp_upload_dir();
        $logo_url = '';
        $logo_light_url = '';
        $logo_slug = sanitize_file_name( $theme_id );
        foreach ( [ 'logo.png' => 'olobuild-logo-' . $logo_slug . '.png', 'logo-light.png' => 'olobuild-logo-' . $logo_slug . '-light.png' ] as $src_file => $dest_file ) {
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
                // Le voci si aggiungono dopo lo Step 6: «{{PAGINA:chiave}}» punta a una pagina
                // che il tema crea più avanti.
                $results['menu'] = [ 'id' => $menu_id, 'name' => $menu_name ];
            }
        }

        // ── Step 3: Import templates with placeholders already replaced ──
        $template_files = $theme_json['templates'] ?? [];
        $id_map = [];
        $con_pagine = [];
        $pagine_tema = [];
        $voci_spettacoli = [];

        foreach ( $template_files as $key => $tpl_meta ) {
            $tpl_file = $dir . '/' . ( $tpl_meta['file'] ?? $key . '.json' );
            if ( ! file_exists( $tpl_file ) ) continue;

            // Load JSON as string first for placeholder replacement
            $json_str = file_get_contents( $tpl_file );

            // Replace logo placeholders
            if ( $logo_url ) {
                $json_str = str_replace( 'LOGO_PLACEHOLDER', $logo_url, $json_str );
            }
            // Percorsi fissi dei loghi col nome di prima (il footer di tutor-clod punta a
            // olobuild-logo-light.png): vanno al file di QUESTO tema, che ha il suo nome.
            foreach ( [ 'olobuild-logo-light.png' => $logo_light_url, 'olobuild-logo.png' => $logo_url ] as $vecchio => $url_tema ) {
                if ( $url_tema ) {
                    $json_str = str_replace( '"/wp-content/uploads/' . $vecchio . '"', wp_json_encode( $url_tema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ), $json_str );
                }
            }

            // Foto e video dalla libreria remota di Olobuild, come nei Blocchi & Pagine:
            // {{OLOBUILD_URL}} = il plugin, {{OLOBUILD_LIBRARY}} = la libreria (copiati sotto).
            if ( false !== strpos( $json_str, '{{OLOBUILD_' ) ) {
                $lib_url  = class_exists( 'Olobuild_Template_Library' ) ? Olobuild_Template_Library::library_url() : '';
                $json_str = str_replace( [ '{{OLOBUILD_URL}}', '{{OLOBUILD_LIBRARY}}' ], [ OLOBUILD_URL, $lib_url ], $json_str );
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

            // Le foto della libreria si copiano nella Libreria media del sito (una volta sola:
            // un file già copiato, anche da un altro tema o blocco, si riusa).
            if ( class_exists( 'Olobuild_Template_Library' ) ) {
                $media   = Olobuild_Template_Library::instance()->import_media( $content );
                $content = $media['content'];
                foreach ( [ 'copiate', 'riusate', 'fallite' ] as $k ) {
                    $results['media'][ $k ] += (int) ( $media[ $k ] ?? 0 );
                }
            }

            // Cartelloni con «Spettacoli del sito»: le loro voci diventeranno spettacoli (Step 8) e la tile le legge da lì.
            if ( class_exists( 'Olobuild_Spettacoli' ) ) {
                $voci_spettacoli = array_merge( $voci_spettacoli, Olobuild_Spettacoli::raccogli( $content ) );
            }

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
                // Collegamenti alle pagine del tema: si risolvono dopo lo Step 6.
                if ( false !== strpos( (string) wp_json_encode( $content ), '{{PAGINA:' ) ) {
                    $con_pagine[ $new_id ] = $content;
                }
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
        $pagine_woo = self::pagine_woo_del_tema( $theme_json );
        if ( ! empty( $theme_json['pages'] ) ) {
            foreach ( $theme_json['pages'] as $page_key => $page_meta ) {
                $tpl_key = $page_meta['template'] ?? $page_key;
                if ( ! isset( $id_map[ $tpl_key ] ) ) continue;

                // Negozio, carrello, cassa e account del tema sono le pagine di WooCommerce: il template ci va da
                // Template WooCommerce (Step 8) e i collegamenti del tema portano lì. Creata a parte, la pagina
                // «Carrello» del tema non era il carrello per WooCommerce (e prendeva l'indirizzo «carrello-2»).
                if ( isset( $pagine_woo[ $page_key ] ) ) {
                    $pagine_tema[ $page_key ] = $pagine_woo[ $page_key ];
                    continue;
                }

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
                // La home solo se non ha un contenuto suo: il template va DAVANTI al
                // contenuto della pagina (Page_Integration lo accoda), e ne usciva mezza
                // pagina del tema e mezza del sito. Altrimenti il tema crea la sua home e
                // la mette come pagina iniziale («Ripristina» rimette quella di prima).
                $page_id = 0;
                if ( $is_home ) {
                    $existing = (int) get_option( 'page_on_front' );
                    if ( $existing && get_post( $existing ) && get_post_type( $existing ) === 'page' && self::senza_contenuto( $existing ) ) {
                        $page_id = $existing;
                    }
                } elseif ( $is_blog ) {
                    $existing = (int) get_option( 'page_for_posts' );
                    if ( $existing && get_post( $existing ) && get_post_type( $existing ) === 'page' ) {
                        $page_id = $existing;
                    }
                }

                // La pagina che QUESTO tema ha creato per questa voce in un import di prima
                // (segno «tema:voce»): si aggiorna quella, anche quando all'indirizzo
                // dichiarato abita una pagina del sito e la sua è finita a «…-2».
                $segno = sanitize_key( $theme_id ) . ':' . sanitize_key( $page_key );
                if ( ! $page_id ) {
                    $sue = get_posts( [ 'post_type' => 'page', 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids', 'meta_key' => '_olo_pagina_tema', 'meta_value' => $segno ] ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key, WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- una ricerca per pagina del tema, solo durante l'import
                    if ( $sue && self::senza_contenuto( (int) $sue[0] ) ) {
                        $page_id = (int) $sue[0];
                    }
                }

                // Chi dichiara uno slug sta dicendo «questa pagina vive a QUESTO
                // indirizzo»: se ci abita gia' una pagina nata da un tema, si aggiorna
                // quella (il secondo import non produce "…-2"). Una pagina del sito no:
                // la «Prenota» di OLObooking ([olo_booking]) diventava la Prenota del tema,
                // col calendario sotto. Allora il tema crea la sua, WordPress le dà un
                // indirizzo libero e menu e collegamenti del tema la seguono (Step 7).
                if ( ! $page_id && $slug !== '' ) {
                    $gia = get_page_by_path( $slug );
                    if ( $gia && 'page' === $gia->post_type && get_post_meta( $gia->ID, '_olo_pagina_tema', true ) && self::senza_contenuto( $gia->ID ) ) {
                        $page_id = (int) $gia->ID;
                    }
                }

                if ( $page_id ) {
                    // La pagina c'era già: com'era prima (template, titolo, stato) va
                    // nell'istantanea, altrimenti «Ripristina» lascerebbe la home del
                    // sito sul template del tema.
                    if ( $snap_id && ! isset( $pagine_prima[ $page_id ] ) ) {
                        $stato = Olobuild_Style_System::stato_pagina( $page_id );
                        if ( $stato ) {
                            $pagine_prima[ $page_id ] = $stato;
                        }
                    }
                    // Lo slug di una pagina che esisteva gia' non si tocca: e' un indirizzo
                    // pubblicato, e cambiarlo romperebbe i collegamenti di chi ce l'ha.
                    wp_update_post( [ 'ID' => $page_id, 'post_title' => $title, 'post_status' => 'publish' ] );
                    // Header e footer assegnati a questa pagina vincono su quelli attivi:
                    // la home riusata teneva la testata di prima sopra il tema nuovo.
                    // L'istantanea li ha registrati, «Ripristina» li rimette.
                    delete_post_meta( $page_id, '_olo_header_id' );
                    delete_post_meta( $page_id, '_olo_footer_id' );
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
                    // Nata dal tema: un import successivo la ritrova (segno) o la riusa al suo
                    // indirizzo. Le pagine riusate non si segnano: dopo «Ripristina» tornano
                    // del sito e un altro tema non deve poterle prendere.
                    if ( $page_id && ! is_wp_error( $page_id ) ) {
                        update_post_meta( $page_id, '_olo_pagina_tema', $segno );
                    }
                }

                if ( $page_id && ! is_wp_error( $page_id ) ) {
                    $pagine_tema[ $page_key ] = (int) $page_id;
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

        if ( $snap_id && $pagine_prima ) {
            Olobuild_Style_System::instance()->amend_snapshot_pages( $snap_id, $pagine_prima );
        }

        // ── Step 7: menu e collegamenti alle pagine del tema ──
        // «{{PAGINA:chiave}}» (chiave di theme.json → pages) diventa l'indirizzo della pagina
        // creata, come percorso relativo (/chi-siamo/): resta giusto se il sito cambia dominio.
        // Una voce di menu che è solo il segnaposto diventa una voce «Pagina» di WordPress:
        // segue la pagina anche se poi se ne cambia l'indirizzo.
        $indirizzi = [];
        foreach ( $pagine_tema as $k => $pid ) {
            $indirizzi[ $k ] = wp_make_link_relative( get_permalink( $pid ) );
        }
        $risolvi = function ( $testo ) use ( $indirizzi ) {
            return preg_replace_callback( '/\{\{PAGINA:([a-z0-9_-]+)\}\}/i', function ( $m ) use ( $indirizzi ) {
                return $indirizzi[ $m[1] ] ?? wp_make_link_relative( home_url( '/' ) );
            }, (string) $testo );
        };
        if ( $menu_id ) {
            // Una voce può avere «children» (e i figli i loro): sottovoci col genitore giusto, che il Mega Menu
            // apre a colonne. «description» è la riga sotto la voce nel pannello.
            $aggiungi = static function ( $voci, $genitore ) use ( &$aggiungi, $menu_id, $pagine_tema, $risolvi ) {
                foreach ( (array) $voci as $item ) {
                    $url  = (string) ( $item['url'] ?? '#' );
                    $voce = [
                        'menu-item-title'     => $item['title'] ?? '',
                        'menu-item-status'    => 'publish',
                        'menu-item-parent-id' => (int) $genitore,
                    ];
                    if ( ! empty( $item['description'] ) ) {
                        $voce['menu-item-description'] = sanitize_text_field( (string) $item['description'] );
                    }
                    if ( preg_match( '/^\{\{PAGINA:([a-z0-9_-]+)\}\}$/i', $url, $m ) && isset( $pagine_tema[ $m[1] ] ) ) {
                        $voce['menu-item-type']      = 'post_type';
                        $voce['menu-item-object']    = 'page';
                        $voce['menu-item-object-id'] = $pagine_tema[ $m[1] ];
                    } else {
                        $voce['menu-item-type'] = 'custom';
                        $voce['menu-item-url']  = $risolvi( $url );
                    }
                    $nuova = wp_update_nav_menu_item( $menu_id, 0, $voce );
                    if ( ! is_wp_error( $nuova ) && $nuova && ! empty( $item['children'] ) && is_array( $item['children'] ) ) {
                        $aggiungi( $item['children'], (int) $nuova );
                    }
                }
            };
            $aggiungi( $theme_json['menu']['items'] ?? [], 0 );
        }
        foreach ( $con_pagine as $tpl_id => $tpl_content ) {
            $nuovo = json_decode( $risolvi( wp_json_encode( $tpl_content ) ), true );
            if ( is_array( $nuovo ) ) {
                $db->update_template( $tpl_id, [ 'content' => $nuovo ] );
            }
        }

        // ── Step 8: spettacoli e pagine dei singoli ──
        // Le voci dei Cartelloni diventano spettacoli, se il sito non ne ha ancora (i link alle pagine del tema
        // già risolti); il template «singolo» del tema disegna la pagina di ogni contenuto di quel tipo.
        if ( $voci_spettacoli && class_exists( 'Olobuild_Spettacoli' ) ) {
            $results['spettacoli'] = Olobuild_Spettacoli::semina( $voci_spettacoli, $risolvi );
        }
        $con_woo = class_exists( 'WooCommerce' );
        foreach ( (array) ( $theme_json['singles'] ?? [] ) as $tipo => $chiave ) {
            $tipo = sanitize_key( (string) $tipo );
            // La scheda dei prodotti la disegna Template WooCommerce (qui sotto), non l'integrazione dei singoli.
            if ( 'product' === $tipo && $con_woo ) {
                continue;
            }
            if ( '' !== $tipo && isset( $id_map[ (string) $chiave ] ) ) {
                update_option( "olobuild_active_single_{$tipo}", (int) $id_map[ (string) $chiave ] );
                $results['activated'][] = 'single_' . $tipo;
            }
        }
        // Template WooCommerce (Impostazioni → Template WooCommerce): la scheda dei prodotti (singles → product) e
        // le pagine del negozio che il tema indica in theme.json → woo, coi ruoli di quelle impostazioni.
        if ( $con_woo ) {
            $ruoli = (array) ( $theme_json['woo'] ?? [] );
            if ( ! empty( $theme_json['singles']['product'] ) ) {
                $ruoli['product_single'] = $theme_json['singles']['product'];
            }
            foreach ( self::RUOLI_WOO as $ruolo ) {
                $chiave = (string) ( $ruoli[ $ruolo ] ?? '' );
                if ( '' !== $chiave && isset( $id_map[ $chiave ] ) ) {
                    update_option( "olobuild_woo_tpl_{$ruolo}", (int) $id_map[ $chiave ], false );
                    $results['activated'][] = 'woo_' . $ruolo;
                }
            }
            // La scritta dei prodotti senza prezzo (theme.json → woo → senza_prezzo): «Prezzo riservato».
            if ( isset( $ruoli['senza_prezzo'] ) && is_string( $ruoli['senza_prezzo'] ) && '' !== trim( $ruoli['senza_prezzo'] ) ) {
                update_option( 'olobuild_woo_senza_prezzo', mb_substr( sanitize_text_field( $ruoli['senza_prezzo'] ), 0, 80 ) );
            }
        }

        // Mark setup complete
        update_option( 'olobuild_setup_complete', true );

        // Chi importa può non poter ripristinare (le versioni dello stile sono da
        // amministratore): il toast del builder lo dice invece di un 403.
        if ( $snap_id ) {
            $results['snapshot'] = [
                'id'          => $snap_id,
                'can_restore' => current_user_can( 'manage_options' ),
            ];
        }

        return $results;
    }

    /** I ruoli di Template WooCommerce (option olobuild_woo_tpl_<ruolo>) che un tema può occupare. */
    const RUOLI_WOO = [ 'product_single', 'product_archive', 'product_category', 'cart', 'checkout', 'myaccount' ];

    /**
     * Le pagine del tema che sono pagine di WooCommerce (theme.json → woo: product_archive, cart, checkout,
     * myaccount → chiave del template): chiave della pagina → id della pagina di WooCommerce. Vuoto senza
     * WooCommerce o se il sito non ha quella pagina (allora il tema crea la sua, come prima).
     */
    private static function pagine_woo_del_tema( $theme_json ) {
        if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'wc_get_page_id' ) || empty( $theme_json['woo'] ) ) {
            return [];
        }
        $di_woo = [ 'product_archive' => 'shop', 'cart' => 'cart', 'checkout' => 'checkout', 'myaccount' => 'myaccount' ];
        $esito  = [];
        foreach ( $di_woo as $ruolo => $pagina_wc ) {
            $tpl = (string) ( $theme_json['woo'][ $ruolo ] ?? '' );
            $id  = (int) wc_get_page_id( $pagina_wc );
            if ( '' === $tpl || $id <= 0 || 'page' !== get_post_type( $id ) ) {
                continue;
            }
            foreach ( (array) ( $theme_json['pages'] ?? [] ) as $page_key => $page_meta ) {
                if ( ( $page_meta['template'] ?? $page_key ) === $tpl && empty( $page_meta['set_as_homepage'] ) ) {
                    $esito[ $page_key ] = $id;
                }
            }
        }
        return $esito;
    }

    /**
     * La pagina non ha un contenuto suo? (testo, shortcode, media, una pagina di Elementor).
     * Commenti dei blocchi e paragrafi vuoti non contano. Nel dubbio dice di no: una pagina
     * vera del sito non va mai sotto il template di un tema.
     */
    private static function senza_contenuto( $post_id ) {
        if ( 'builder' === get_post_meta( $post_id, '_elementor_edit_mode', true ) ) {
            return false;
        }
        $html = (string) get_post_field( 'post_content', $post_id );
        if ( preg_match( '#<(img|iframe|video|audio|svg|object|embed|canvas|form|input)\b#i', $html ) ) {
            return false;
        }
        $html = preg_replace( '/<!--.*?-->/s', '', $html );
        if ( null === $html ) {
            return false;
        }
        $testo = str_replace( [ '&nbsp;', "\xc2\xa0" ], ' ', wp_strip_all_tags( $html ) );
        return '' === trim( $testo );
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
