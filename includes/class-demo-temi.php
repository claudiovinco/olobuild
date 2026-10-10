<?php
/**
 * Olobuild_Demo_Temi — le demo dal vivo dei temi inclusi nel plugin.
 *
 * A /demo/<tema>/ (e /demo/<tema>/<pagina>/) un tema si vede come un sito vero, disegnato al volo dai
 * suoi file in assets/data/themes/<tema>/: pagine, testata, piè di pagina, menu, colori, caratteri,
 * cursore e HUD. Non si importa niente: nessuna riga nel database, nessuna pagina e nessun menu di
 * WordPress, e lo stile del sito resta com'è. I valori del tema valgono solo in quella richiesta, con
 * gli stessi filtri pre_option del banco dei temi su cui sono stati controllati i 52 temi (ottobre 2026).
 *
 * Spenta di serie. Si accende con l'opzione `olobuild_demo_temi`: 'tutti', oppure l'elenco degli id dei
 * temi da mostrare. Nelle demo i moduli non inviano (anche lato server: il modulo di contatto porta il
 * segno «demo» nella sua configurazione firmata), e il sito non aggiunge popup, banner dei cookie,
 * codice personalizzato, statistiche né metadati SEO: la demo è un altro sito, e resta fuori dai motori.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Demo_Temi {

    const OPZIONE = 'olobuild_demo_temi';

    /** Primo segmento dell'indirizzo: /demo/<tema>/<pagina>/. */
    const BASE = 'demo';

    /**
     * Template, menu e voci virtuali hanno id da qui in su: oltre ogni id vero (tabelle e termini
     * partono da 1) e sotto 2^31, così passano da absint() e da %d. Template: un id per tema e
     * template (crc32), stabile fra le richieste, perché il CSS in cache porta l'id nel nome.
     */
    const ID_TEMPLATE = 2000000000;
    const ID_MENU     = 2100000000;
    const ID_VOCE     = 2120000000;

    /** @var array|null Il tema della richiesta: id, cartella, theme.json. */
    private static $tema = null;

    /** @var string Chiave della pagina mostrata (theme.json → pages), oppure '404'. */
    private static $pagina = '';

    /** @var array<int,array> Template virtuali già preparati in questa richiesta. */
    private static $template = [];

    /** @var bool Guardia: sanitize() di cursore e HUD rilegge l'opzione che stiamo filtrando. */
    private static $dentro = false;

    /**
     * Chiamata all'avvio del plugin, prima che le classi leggano le opzioni: se la richiesta è una
     * demo, i filtri sono già al loro posto quando lo stile del sito verrebbe letto.
     */
    public static function avvia() {
        $richiesta = self::leggi_richiesta();
        if ( ! $richiesta ) {
            return;
        }
        list( self::$tema, self::$pagina ) = $richiesta;

        // Stile del tema al posto di quello del sito.
        add_filter( 'pre_option_olobuild_styles', [ __CLASS__, 'stili' ] );
        add_filter( 'pre_option_olobuild_global_colors', [ __CLASS__, 'vuoto' ] );
        add_filter( 'pre_option_olobuild_global_typography', [ __CLASS__, 'vuoto' ] );
        add_filter( 'pre_option_olobuild_magnetic_cursor', [ __CLASS__, 'cursore' ] );
        add_filter( 'pre_option_olobuild_cursor_hud', [ __CLASS__, 'hud' ] );

        // Testata, piè di pagina e 404 del tema, letti dai suoi file.
        add_filter( 'pre_option_olobuild_active_header', [ __CLASS__, 'testata' ] );
        add_filter( 'pre_option_olobuild_active_footer', [ __CLASS__, 'piede' ] );
        add_filter( 'pre_option_olobuild_active_404', [ __CLASS__, 'pagina_404' ] );
        add_filter( 'olobuild_resolve_template_id', [ __CLASS__, 'zona' ], 99, 2 );
        add_filter( 'olobuild_template_virtuale', [ __CLASS__, 'template' ], 10, 2 );
        add_filter( 'wp_get_nav_menu_object', [ __CLASS__, 'menu' ], 10, 2 );
        add_filter( 'wp_get_nav_menu_items', [ __CLASS__, 'voci_menu' ], 10, 2 );

        // Le aggiunte del sito restano fuori: la demo è un altro sito.
        add_filter( 'pre_option_olobuild_global_popups', [ __CLASS__, 'vuoto' ] );
        add_filter( 'pre_option_olobuild_cookie_settings', [ __CLASS__, 'senza_banner' ] );
        add_action( 'wp', [ __CLASS__, 'togli_aggiunte_del_sito' ] );
        add_filter( 'show_admin_bar', '__return_false' );
        // UIkit e frontend.css interi: il sottoinsieme appreso e il CSS per tile restano quelli del sito.
        add_filter( 'option_olobuild_performance', [ __CLASS__, 'prestazioni' ] );

        // OLOlang: la demo resta nella lingua principale (i testi del tema non si «traducono» a pezzi
        // con le stringhe globali del sito) e il cookie della lingua del visitatore non si tocca:
        // tornando al sito, chi naviga in inglese resta in inglese.
        add_filter( 'olo_lang_current', [ __CLASS__, 'lingua_principale' ], 1000 );
        add_filter( 'olo_lang_browser_redirect_enabled', '__return_false' );
        add_action( 'init', [ __CLASS__, 'lascia_il_cookie_della_lingua' ], 1 );

        // La pagina: niente query di WordPress, il modello della demo, fuori dai motori.
        add_filter( 'do_parse_request', [ __CLASS__, 'niente_query' ], 1000 );
        add_filter( 'template_include', [ __CLASS__, 'modello' ], 1000 );
        add_filter( 'wp_robots', [ __CLASS__, 'robots' ], 1000 );
        add_action( 'send_headers', [ __CLASS__, 'intestazioni' ] );
        add_filter( 'pre_get_document_title', [ __CLASS__, 'titolo' ], 1000 );
        add_filter( 'body_class', [ __CLASS__, 'classi' ] );
        add_action( 'wp_head', [ __CLASS__, 'stile_pagina' ], 100 );
        add_action( 'wp_footer', [ __CLASS__, 'moduli_fermi' ], 100 );
    }

    /** La richiesta in corso è una demo? (per le parti che si comportano diversamente, come i moduli) */
    public static function in_corso() {
        return ! empty( self::$tema );
    }

    /**
     * Il tema della demo usa una tile di questo tipo, in uno qualunque dei suoi file? Per le parti del
     * sito che ospita le demo che altrimenti varrebbero anche lì (la modalità scura scelta dal
     * visitatore: un tema senza interruttore non ha una veste scura disegnata).
     */
    public static function ha_tile( $tipo ) {
        static $visti = [];
        if ( empty( self::$tema ) ) {
            return false;
        }
        $chiave = self::$tema['id'] . '|' . $tipo;
        if ( ! isset( $visti[ $chiave ] ) ) {
            $visti[ $chiave ] = false;
            foreach ( (array) ( self::$tema['json']['templates'] ?? [] ) as $meta ) {
                $file = self::$tema['dir'] . '/' . basename( (string) ( $meta['file'] ?? '' ) );
                // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- file del plugin, sul disco
                if ( is_readable( $file ) && preg_match( '/"type"\s*:\s*"' . preg_quote( (string) $tipo, '/' ) . '"/', (string) file_get_contents( $file ) ) ) {
                    $visti[ $chiave ] = true;
                    break;
                }
            }
        }
        return $visti[ $chiave ];
    }

    /**
     * [ tema, chiave della pagina ] se l'indirizzo è /demo/<tema>/… di un tema acceso, altrimenti null.
     * Una pagina che il tema non ha mostra la sua 404; senza 404 del tema decide WordPress.
     */
    private static function leggi_richiesta() {
        if ( is_admin() || wp_doing_ajax() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
            return null;
        }
        $metodo = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( sanitize_key( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) ) : 'GET';
        if ( ! in_array( $metodo, [ 'GET', 'HEAD' ], true ) ) {
            return null;
        }
        $uri      = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- solo confrontato con un'espressione regolare che accetta [a-z0-9-]
        $percorso = (string) wp_parse_url( $uri, PHP_URL_PATH );
        // L'indirizzo del sito senza filtri di lingua: home_url() può già portare il prefisso di OLOlang.
        $casa = trailingslashit( (string) wp_parse_url( (string) get_option( 'home' ), PHP_URL_PATH ) );
        if ( ! preg_match( '#^' . preg_quote( $casa . self::BASE . '/', '#' ) . '([a-z0-9-]+)(?:/([a-z0-9-]+))?/?$#', $percorso, $m ) ) {
            return null;
        }
        $id = $m[1];
        if ( ! self::acceso( $id ) ) {
            return null;
        }
        $dir  = OLOBUILD_PATH . 'assets/data/themes/' . $id;
        $file = $dir . '/theme.json';
        if ( ! is_readable( $file ) ) {
            return null;
        }
        $json = json_decode( (string) file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- file del plugin
        if ( ! is_array( $json ) || empty( $json['templates'] ) || empty( $json['pages'] ) || ! is_array( $json['pages'] ) ) {
            return null;
        }
        $chiave = self::pagina_da_indirizzo( $json['pages'], $m[2] ?? '' );
        if ( '' === $chiave ) {
            if ( empty( $json['activate']['404'] ) ) {
                return null;
            }
            $chiave = '404';
        }
        return [ [ 'id' => $id, 'dir' => $dir, 'json' => $json ], $chiave ];
    }

    /** Il tema si può mostrare? L'opzione vale 'tutti' oppure l'elenco degli id. */
    public static function acceso( $id ) {
        $accesi = get_option( self::OPZIONE, '' );
        if ( 'tutti' === $accesi ) {
            return true;
        }
        return is_array( $accesi ) && in_array( $id, $accesi, true );
    }

    /** Il sito mostra almeno una demo? (la tile Vetrina temi si registra solo così) */
    public static function accese() {
        $accesi = get_option( self::OPZIONE, '' );
        return 'tutti' === $accesi || ( is_array( $accesi ) && ! empty( $accesi ) );
    }

    /** Indirizzo (relativo) della home della demo di un tema, da qualunque richiesta. */
    public static function indirizzo_tema( $id ) {
        return wp_make_link_relative( home_url( '/' . self::BASE . '/' . sanitize_title( (string) $id ) . '/' ) );
    }

    /** Chiave della pagina per il pezzo d'indirizzo dopo il tema ('' = la home), '' se non c'è. */
    private static function pagina_da_indirizzo( $pagine, $pezzo ) {
        foreach ( $pagine as $chiave => $p ) {
            $home = ! empty( $p['set_as_homepage'] );
            if ( '' === $pezzo ? $home : ( ! $home && self::slug( $chiave, $p ) === $pezzo ) ) {
                return (string) $chiave;
            }
        }
        if ( '' === $pezzo ) {
            // Un tema senza home dichiarata apre la prima pagina.
            $prima = array_key_first( $pagine );
            return null === $prima ? '' : (string) $prima;
        }
        return '';
    }

    /** Lo slug di una pagina del tema: quello dichiarato o, come fa WordPress all'import, dal nome. */
    private static function slug( $chiave, $p ) {
        return sanitize_title( isset( $p['slug'] ) ? (string) $p['slug'] : (string) $chiave );
    }

    /** Indirizzo (relativo, come all'import) della pagina del tema con quella chiave; home se non c'è. */
    public static function indirizzo( $chiave ) {
        $pagine = self::$tema['json']['pages'];
        $pezzo  = '';
        if ( isset( $pagine[ $chiave ] ) && empty( $pagine[ $chiave ]['set_as_homepage'] ) ) {
            $pezzo = self::slug( $chiave, $pagine[ $chiave ] ) . '/';
        }
        return wp_make_link_relative( home_url( '/' . self::BASE . '/' . self::$tema['id'] . '/' . $pezzo ) );
    }

    /* ── Stile del tema ── */

    public static function stili( $valore ) {
        $s = self::$tema['json']['styles'] ?? null;
        if ( ! is_array( $s ) ) {
            return $valore;
        }
        // Come all'import (Olobuild_Style_System::sync_dark_palette): in modalità scura i ruoli del
        // marchio restano quelli del tema, non l'indaco di serie (vale per i temi con l'interruttore).
        $c  = (array) ( $s['colors'] ?? [] );
        $dc = (array) ( $s['dark_colors'] ?? [] );
        foreach ( [ 'primary', 'primary_contrast', 'secondary', 'secondary_contrast', 'link' ] as $k ) {
            if ( ! isset( $dc[ $k ] ) && isset( $c[ $k ] ) ) {
                $dc[ $k ] = $c[ $k ];
            }
        }
        if ( $dc ) {
            $s['dark_colors'] = $dc;
        }
        return $s;
    }

    public static function vuoto() {
        return [];
    }

    public static function cursore( $valore ) {
        return self::funzione_globale( $valore, 'cursor', 'Olobuild_Magnetic_Cursor' );
    }

    public static function hud( $valore ) {
        return self::funzione_globale( $valore, 'cursor_hud', 'Olobuild_Cursor_Hud' );
    }

    /** Cursore e HUD del tema; spenti se il tema non li ha. */
    private static function funzione_globale( $valore, $chiave, $classe ) {
        if ( self::$dentro ) {
            return $valore;
        }
        $c = self::$tema['json'][ $chiave ] ?? null;
        if ( ! is_array( $c ) ) {
            return [ 'enabled' => false ];
        }
        if ( ! class_exists( $classe ) ) {
            return $c;
        }
        self::$dentro = true;
        $pulito       = $classe::sanitize( $c );
        self::$dentro = false;
        return $pulito;
    }

    /* ── Template virtuali ── */

    public static function testata() {
        return self::id_zona( 'header' );
    }

    public static function piede() {
        return self::id_zona( 'footer' );
    }

    public static function pagina_404() {
        return self::id_zona( '404' );
    }

    /**
     * olobuild_resolve_template_id: testata e piè di pagina del tema al posto delle regole del sito.
     * Un tema senza testata non ne mostra nessuna: 0 ricade sull'opzione, che qui vale 0 anche lei.
     */
    public static function zona( $id, $zona ) {
        return self::id_zona( (string) $zona );
    }

    /** Id del template che il tema attiva in quella zona (header, footer, 404), 0 se nessuno. */
    public static function id_zona( $zona ) {
        $chiave = (string) ( self::$tema['json']['activate'][ $zona ] ?? '' );
        return ( '' !== $chiave && isset( self::$tema['json']['templates'][ $chiave ] ) ) ? self::id_template( $chiave ) : 0;
    }

    /** Id del template della pagina mostrata. */
    public static function id_pagina() {
        if ( '404' === self::$pagina ) {
            return self::id_zona( '404' );
        }
        $p = self::$tema['json']['pages'][ self::$pagina ] ?? [];
        return self::id_template( (string) ( $p['template'] ?? self::$pagina ) );
    }

    private static function id_template( $chiave ) {
        return self::ID_TEMPLATE + ( abs( crc32( self::$tema['id'] . '/' . $chiave ) ) % 100000000 );
    }

    private static function id_menu() {
        return self::ID_MENU + ( abs( crc32( 'menu/' . self::$tema['id'] ) ) % 10000000 );
    }

    /** olobuild_template_virtuale (Olobuild_Database::get_template): i template del tema, dai suoi file. */
    public static function template( $tpl, $id ) {
        if ( is_array( $tpl ) ) {
            return $tpl;
        }
        $id = (int) $id;
        if ( isset( self::$template[ $id ] ) ) {
            return self::$template[ $id ];
        }
        foreach ( self::$tema['json']['templates'] as $chiave => $meta ) {
            if ( self::id_template( (string) $chiave ) === $id ) {
                $creato = self::crea_template( (string) $chiave, (array) $meta, $id );
                if ( $creato ) {
                    self::$template[ $id ] = $creato;
                }
                return $creato;
            }
        }
        return $tpl;
    }

    private static function crea_template( $chiave, $meta, $id ) {
        $file = self::$tema['dir'] . '/' . basename( (string) ( $meta['file'] ?? $chiave . '.json' ) );
        if ( ! is_readable( $file ) ) {
            return null;
        }
        $dati = json_decode( (string) file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- file del plugin
        if ( ! is_array( $dati ) ) {
            return null;
        }
        // Formato esteso {settings, content}, come all'import.
        $impostazioni = [];
        if ( isset( $dati['content'] ) && is_array( $dati['content'] ) && ! isset( $dati[0] ) ) {
            $impostazioni = ( isset( $dati['settings'] ) && is_array( $dati['settings'] ) ) ? $dati['settings'] : [];
            $dati         = $dati['content'];
        }
        $quando = gmdate( 'Y-m-d H:i:s', (int) filemtime( $file ) );
        return [
            'id'         => $id,
            'title'      => (string) ( $meta['title'] ?? $chiave ),
            'type'       => (string) ( $meta['type'] ?? 'page' ),
            'content'    => self::prepara( $dati ),
            'settings'   => $impostazioni,
            'thumbnail'  => '',
            'status'     => 'published',
            'author_id'  => 0,
            'created_at' => $quando,
            'updated_at' => $quando,
        ];
    }

    /**
     * I segnaposto del tema, risolti come fa l'import ma senza copiare niente: le foto restano sulla
     * libreria remota, i collegamenti «{{PAGINA:chiave}}» portano alle pagine della demo e il menu
     * «auto» è quello virtuale del tema.
     */
    private static function prepara( $nodi ) {
        $json = (string) wp_json_encode( $nodi, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        $lib  = class_exists( 'Olobuild_Template_Library' ) ? Olobuild_Template_Library::library_url() : '';
        $json = str_replace( [ '{{OLOBUILD_URL}}', '{{OLOBUILD_LIBRARY}}' ], [ OLOBUILD_URL, $lib ], $json );
        $logo = file_exists( self::$tema['dir'] . '/logo.png' ) ? OLOBUILD_URL . 'assets/data/themes/' . self::$tema['id'] . '/logo.png' : '';
        $json = str_replace( 'LOGO_PLACEHOLDER', $logo, $json );
        $json = (string) preg_replace( '/"menu_id"\s*:\s*"auto"/', '"menu_id":' . self::id_menu(), $json );
        $json = self::risolvi( $json );
        $nuovi = json_decode( $json, true );
        return is_array( $nuovi ) ? $nuovi : [];
    }

    /** «{{PAGINA:chiave}}» → indirizzo della pagina nella demo. */
    private static function risolvi( $testo ) {
        return (string) preg_replace_callback( '/\{\{PAGINA:([a-z0-9_-]+)\}\}/i', function ( $m ) {
            return self::indirizzo( $m[1] );
        }, (string) $testo );
    }

    /* ── Menu virtuale ── */

    /** wp_get_nav_menu_object: il menu del tema esiste solo come oggetto di questa richiesta. */
    public static function menu( $oggetto, $menu ) {
        $id = is_object( $menu ) ? (int) ( $menu->term_id ?? 0 ) : ( is_numeric( $menu ) ? (int) $menu : 0 );
        if ( self::id_menu() !== $id ) {
            return $oggetto;
        }
        $m = self::$tema['json']['menu'] ?? [];
        return new WP_Term( (object) [
            'term_id'          => $id,
            'name'             => (string) ( $m['name'] ?? self::$tema['json']['name'] ?? self::$tema['id'] ),
            'slug'             => 'olo-demo-' . self::$tema['id'],
            'term_group'       => 0,
            'term_taxonomy_id' => $id,
            'taxonomy'         => 'nav_menu',
            'description'      => '',
            'parent'           => 0,
            'count'            => count( (array) ( $m['items'] ?? [] ) ),
            'filter'           => 'raw',
        ] );
    }

    /** wp_get_nav_menu_items: le voci del menu del tema, con i collegamenti alle pagine della demo. */
    public static function voci_menu( $voci, $menu ) {
        if ( ! is_object( $menu ) || self::id_menu() !== (int) ( $menu->term_id ?? 0 ) ) {
            return $voci;
        }
        $nuove = [];
        foreach ( array_values( (array) ( self::$tema['json']['menu']['items'] ?? [] ) ) as $n => $v ) {
            $id     = self::ID_VOCE + $n;
            $titolo = (string) ( $v['title'] ?? '' );
            $nuove[] = new WP_Post( (object) [
                'ID'               => $id,
                'db_id'            => $id,
                'menu_item_parent' => '0',
                'object_id'        => (string) $id,
                'object'           => 'custom',
                'type'             => 'custom',
                'type_label'       => '',
                'title'            => $titolo,
                'url'              => self::risolvi( (string) ( $v['url'] ?? '#' ) ),
                'target'           => '',
                'attr_title'       => '',
                'description'      => '',
                'classes'          => [ '' ],
                'xfn'              => '',
                'menu_order'       => $n + 1,
                'post_type'        => 'nav_menu_item',
                'post_status'      => 'publish',
                'post_title'       => $titolo,
                'post_name'        => 'olo-demo-voce-' . ( $n + 1 ),
                'post_parent'      => 0,
                'post_content'     => '',
                'post_excerpt'     => '',
                'filter'           => 'raw',
            ] );
        }
        return $nuove;
    }

    /* ── Il sito resta fuori ── */

    public static function senza_banner() {
        return [ 'enabled' => false ];
    }

    public static function prestazioni( $valore ) {
        if ( is_array( $valore ) ) {
            $valore['uikit_subset']         = false;
            $valore['css_per_tile']         = false;
            $valore['critical_css_enabled'] = false;
        }
        return $valore;
    }

    /** Metadati SEO, statistiche, test A/B e codice personalizzato del sito: non sono della demo. */
    public static function togli_aggiunte_del_sito() {
        if ( class_exists( 'Olobuild_Seo_Head' ) ) {
            $seo = Olobuild_Seo_Head::instance();
            remove_action( 'wp_head', [ $seo, 'output_meta_tags' ], 1 );
            remove_action( 'wp_head', [ $seo, 'output_verification_tags' ], 2 );
            remove_action( 'wp_head', [ $seo, 'output_og_tags' ], 5 );
            remove_action( 'wp_head', [ $seo, 'output_jsonld' ], 6 );
        }
        remove_action( 'wp_head', [ 'Olobuild_Analytics_Tracking', 'output_tracking_scripts' ], 1 );
        remove_action( 'wp_footer', [ 'Olobuild_Analytics_Tracking', 'output_event_listeners' ], 99 );
        remove_action( 'wp_body_open', [ 'Olobuild_Analytics_Tracking', 'output_gtm_noscript' ], 1 );
        remove_action( 'wp_footer', [ 'Olobuild_AB_Testing', 'output_tracking_script' ], 98 );
        remove_action( 'wp_head', [ 'Olobuild_Custom_Code', 'output_head_code' ], 99 );
        remove_action( 'wp_body_open', [ 'Olobuild_Custom_Code', 'output_body_open_code' ], 1 );
        remove_action( 'wp_footer', [ 'Olobuild_Custom_Code', 'output_footer_code' ], 99 );
    }

    /* ── OLOlang ── */

    /** olo_lang_current: la lingua principale del sito, quella in cui OLOlang non cambia niente. */
    public static function lingua_principale( $lingua ) {
        return class_exists( 'Olo_Lang_Language' ) ? Olo_Lang_Language::get_default_lang() : $lingua;
    }

    /** OLOlang riscrive il cookie con la lingua della richiesta: nella demo sarebbe quella principale. */
    public static function lascia_il_cookie_della_lingua() {
        remove_action( 'init', [ 'Olo_Lang_Cache_Integration', 'ensure_cookie' ], 5 );
    }

    /**
     * Le poche scritte della demo che non sono del tema (l'avviso dei moduli, il titolo della 404)
     * nella lingua del visitatore: quella del parametro o del cookie di OLOlang, letta dal catalogo
     * del plugin. Senza OLOlang, o per la lingua principale, la traduzione del sito.
     */
    private static function testo_ospite( $testo ) {
        if ( ! class_exists( 'Olo_Lang_Language' ) ) {
            return olobuild_t( $testo );
        }
        // phpcs:disable WordPress.Security.NonceVerification.Recommended -- sola lettura della lingua scelta dal visitatore
        $codice = isset( $_GET['lang'] ) ? sanitize_key( wp_unslash( $_GET['lang'] ) ) : '';
        if ( '' === $codice ) {
            $codice = isset( $_COOKIE['olo_lang'] ) ? sanitize_key( wp_unslash( $_COOKIE['olo_lang'] ) ) : '';
        }
        // phpcs:enable WordPress.Security.NonceVerification.Recommended
        $locale = '';
        foreach ( (array) Olo_Lang_Language::get_active_languages() as $l ) {
            if ( is_array( $l ) && ( $l['code'] ?? '' ) === $codice ) {
                $locale = (string) ( $l['locale'] ?? '' );
            }
        }
        $file = OLOBUILD_PATH . 'languages/olobuild-' . sanitize_file_name( $locale ) . '.mo';
        if ( '' === $locale || 0 === strpos( $locale, 'it' ) || ! is_readable( $file ) ) {
            return olobuild_t( $testo );
        }
        static $cataloghi = [];
        if ( ! isset( $cataloghi[ $locale ] ) ) {
            if ( ! class_exists( 'MO' ) ) {
                require_once ABSPATH . WPINC . '/pomo/mo.php';
            }
            $cataloghi[ $locale ] = new MO();
            $cataloghi[ $locale ]->import_from_file( $file );
        }
        return $cataloghi[ $locale ]->translate( $testo );
    }

    /* ── La pagina ── */

    /** do_parse_request: l'indirizzo non è di WordPress, nessuna query (e quindi nessun 404). */
    public static function niente_query() {
        return false;
    }

    public static function modello() {
        return OLOBUILD_PATH . 'templates/demo-tema-template.php';
    }

    /**
     * Disegna il contenuto come su una pagina vera: mentre si disegna, WordPress vede la pagina del tema
     * (titolo e slug come all'import), così la barra del titolo mostra il suo titolo e le briciole
     * «Home › titolo» invece del nome del sito. Vale solo per il contenuto: poi tutto torna com'era.
     */
    public static function come_una_pagina( $disegna ) {
        global $wp_query;
        if ( '404' === self::$pagina || ! ( $wp_query instanceof WP_Query ) ) {
            return (string) call_user_func( $disegna );
        }
        $p       = self::$tema['json']['pages'][ self::$pagina ] ?? [];
        $ora     = current_time( 'mysql' );
        $ora_gmt = current_time( 'mysql', true );
        $pagina  = new WP_Post( (object) [
            'ID'                    => 0,
            'post_author'           => 0,
            'post_date'             => $ora,
            'post_date_gmt'         => $ora_gmt,
            'post_content'          => '',
            'post_title'            => (string) ( $p['title'] ?? self::$pagina ),
            'post_excerpt'          => '',
            'post_status'           => 'publish',
            'comment_status'        => 'closed',
            'ping_status'           => 'closed',
            'post_password'         => '',
            'post_name'             => self::slug( self::$pagina, $p ),
            'to_ping'               => '',
            'pinged'                => '',
            'post_modified'         => $ora,
            'post_modified_gmt'     => $ora_gmt,
            'post_content_filtered' => '',
            'post_parent'           => 0,
            'guid'                  => '',
            'menu_order'            => 0,
            'post_type'             => 'page',
            'post_mime_type'        => '',
            'comment_count'         => '0',
            'filter'                => 'raw',
        ] );
        $prima = [];
        foreach ( [ 'is_page', 'is_singular', 'queried_object', 'queried_object_id', 'post', 'posts', 'post_count', 'found_posts' ] as $campo ) {
            $prima[ $campo ] = $wp_query->$campo;
        }
        $post_prima                  = $GLOBALS['post'] ?? null;
        $wp_query->is_page           = true;
        $wp_query->is_singular       = true;
        $wp_query->queried_object    = $pagina;
        $wp_query->queried_object_id = 0;
        $wp_query->post              = $pagina;
        $wp_query->posts             = [ $pagina ];
        $wp_query->post_count        = 1;
        $wp_query->found_posts       = 1;
        $GLOBALS['post']             = $pagina; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- solo durante il disegno, rimesso subito dopo
        try {
            return (string) call_user_func( $disegna );
        } finally {
            foreach ( $prima as $campo => $valore ) {
                $wp_query->$campo = $valore;
            }
            if ( null === $post_prima ) {
                unset( $GLOBALS['post'] );
            } else {
                $GLOBALS['post'] = $post_prima; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- il valore di prima
            }
        }
    }

    /**
     * I collegamenti alla home del sito (il logo in testata e nel piè di pagina, «Home» nelle briciole)
     * portano alla home della demo: sul sito importato la home è quella del tema.
     */
    public static function casa_della_demo( $html ) {
        $parti = wp_parse_url( home_url( '/' ) );
        $host  = preg_quote( ( $parti['host'] ?? '' ) . ( isset( $parti['port'] ) ? ':' . $parti['port'] : '' ), '#' );
        $via   = preg_quote( rtrim( (string) ( $parti['path'] ?? '' ), '/' ), '#' );
        $casa  = '(?:https?://' . $host . $via . '/?|' . ( '' === $via ? '/' : $via . '/?' ) . ')';
        $demo  = esc_url( self::indirizzo_tema( self::$tema['id'] ) );
        return (string) preg_replace( '#(\shref=)(["\'])' . $casa . '\2#i', '$1$2' . $demo . '$2', (string) $html );
    }

    public static function robots() {
        return [ 'noindex' => true, 'nofollow' => true ];
    }

    public static function intestazioni() {
        if ( ! headers_sent() ) {
            header( 'X-Robots-Tag: noindex, nofollow', true );
        }
        if ( '404' === self::$pagina ) {
            status_header( 404 );
        }
    }

    /** «Shop — Denim District»; la home col solo nome del tema. */
    public static function titolo() {
        $nome = (string) ( self::$tema['json']['name'] ?? self::$tema['id'] );
        if ( '404' === self::$pagina ) {
            return esc_html( self::testo_ospite( 'Pagina non trovata' ) . ' — ' . $nome );
        }
        $p = self::$tema['json']['pages'][ self::$pagina ] ?? [];
        return esc_html( empty( $p['set_as_homepage'] ) && ! empty( $p['title'] ) ? $p['title'] . ' — ' . $nome : $nome );
    }

    public static function classi( $classi ) {
        $classi[] = 'olo-demo-tema';
        $classi[] = 'olo-demo-tema--' . sanitize_html_class( self::$tema['id'] );
        return $classi;
    }

    /** Lingua dei testi del tema (i temi inclusi sono scritti in italiano). */
    public static function lingua() {
        return (string) ( self::$tema['json']['lang'] ?? 'it' );
    }

    /**
     * Il fondo della pagina è quello del tema (nessuna striscia chiara sotto un tema scuro quando la
     * pagina è più corta della finestra) e lo stile dell'avviso dei moduli.
     */
    public static function stile_pagina() {
        $fondo = sanitize_hex_color( (string) ( self::$tema['json']['styles']['colors']['background'] ?? '' ) );
        $css   = $fondo ? 'body.olo-demo-tema{background-color:' . $fondo . '}' : '';
        $css  .= '.olo-template.olo-demo-avviso{position:fixed;left:50%;bottom:24px;transform:translateX(-50%);z-index:2147483000;max-width:calc(100% - 32px);padding:12px 18px;border-radius:var(--olo-border-radius);background:var(--olo-color-dark);color:var(--olo-color-light);font:500 15px/1.45 var(--olo-font-family);box-shadow:var(--olo-shadow-large);text-align:center}';
        $css  .= '.olo-template.olo-demo-avviso[hidden]{display:none}';
        echo '<style id="olo-demo-tema-css">' . $css . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS fisso; l'unico valore variabile passa da sanitize_hex_color()
    }

    /**
     * Nella demo nessun modulo invia: ricerca, newsletter e gli altri si fermano qui con un avviso.
     * Il modulo di contatto va avanti da sé: il server riconosce la sua configurazione «demo» e risponde
     * con lo stesso avviso, così si vede anche come il tema mostra un invio riuscito.
     */
    public static function moduli_fermi() {
        $testo = self::testo_ospite( 'Questa è una demo: i moduli non inviano nulla.' );
        ?>
<div class="olo-template olo-demo-avviso" role="status" aria-live="polite" hidden></div>
<script>
(function(){
    var box = document.querySelector('.olo-demo-avviso'), timer = 0;
    if (!box) { return; }
    document.addEventListener('submit', function (e) {
        var f = e.target;
        if (f ? (f.matches ? f.matches('form[data-olo-form]') : false) : false) { return; }
        e.preventDefault();
        e.stopImmediatePropagation();
        box.textContent = <?php echo wp_json_encode( $testo ); ?>;
        box.hidden = false;
        clearTimeout(timer);
        timer = setTimeout(function () { box.hidden = true; }, 4500);
    }, true);
})();
</script>
        <?php
    }
}
