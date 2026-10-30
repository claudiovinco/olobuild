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

    /** Pagina di uno spettacolo nella demo: /demo/<tema>/spettacoli/<titolo>/ (la demo è nella lingua del tema). */
    const BASE_SPETTACOLI = 'spettacoli';

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

    /** @var string Chiave della pagina mostrata (theme.json → pages), oppure '404', o 'spettacolo'. */
    private static $pagina = '';

    /** @var array|null La voce del Cartellone del tema di cui si mostra la pagina (segnaposto ancora da risolvere). */
    private static $spettacolo = null;

    /**
     * @var WP_Post|null Il contenuto vero del sito di cui si mostra la scheda del tema (theme.json → singles), a
     * /demo/<tema>/<base>/<slug>/: per esempio una struttura di OLObooking nella demo di un suo tema.
     */
    private static $singolo = null;

    /** @var string Il template della scheda di $singolo (chiave di theme.json → templates). */
    private static $singolo_tpl = '';

    /** @var string[] [ base, slug ] della scheda chiesta: si cerca a wp_loaded, quando i tipi di contenuto esistono. */
    private static $singolo_chiesto = [];

    /** @var bool L'indirizzo è la demo di un tema acceso che qui non si è ancora trovato: si riprova coi temi dei plugin. */
    private static $riprova = false;

    /** @var array<int,array> Template virtuali già preparati in questa richiesta. */
    private static $template = [];

    /** @var bool Guardia: sanitize() di cursore e HUD rilegge l'opzione che stiamo filtrando. */
    private static $dentro = false;

    /** Le palette da provare sui temi (bollini del visore della Vetrina temi): versione chiara e scura. */
    const FILE_PALETTE = 'assets/data/palette-temi.json';

    /** @var string La palette scelta nel visore (?palette=), '' = i colori del tema. */
    private static $palette = '';

    /**
     * Chiamata all'avvio del plugin, prima che le classi leggano le opzioni: se la richiesta è una
     * demo, i filtri sono già al loro posto quando lo stile del sito verrebbe letto.
     */
    public static function avvia() {
        $richiesta = self::leggi_richiesta();
        if ( ! $richiesta ) {
            self::woo_dalla_demo();
            add_action( 'template_redirect', [ __CLASS__, 'prodotto_nella_demo' ] );
            // Un tema che vive in un plugin (olobuild_external_theme_dirs) si trova solo quando i plugin sono tutti
            // inclusi: si riprova a plugins_loaded, prima che partano (e prima che OLOlang scelga la lingua).
            if ( self::$riprova ) {
                add_action( 'plugins_loaded', [ __CLASS__, 'avvia_tema_di_plugin' ], 0 );
            }
            return;
        }
        self::aggancia( $richiesta );
    }

    /**
     * Le chiamate AJAX di WooCommerce (?wc-ajax=…, aggiunta al carrello, frammenti del mini carrello) e le rotte REST
     * delle tile del negozio partono dall'indirizzo del sito, non da quello della demo: la tendina del mini carrello
     * tornava coi link al carrello del sito. Se arrivano da una pagina della demo di un negozio, carrello, cassa e
     * scritta dei prodotti senza prezzo restano quelli del tema.
     */
    private static function woo_dalla_demo() {
        // Anche le rotte REST delle tile del negozio (anteprima veloce, confronto, preferiti).
        $uri  = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- solo confrontato con una stringa fissa
        $rest = (bool) preg_match( '#(/|%2F)olobuild(/|%2F)v1(/|%2F)(woo-|livesearch)#i', $uri );
        if ( ( empty( $_GET['wc-ajax'] ) && ! $rest ) || empty( $_SERVER['HTTP_REFERER'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- si guarda solo se c'è
            return;
        }
        $percorso = (string) wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ), PHP_URL_PATH );
        $casa     = trailingslashit( (string) wp_parse_url( (string) get_option( 'home' ), PHP_URL_PATH ) );
        if ( ! preg_match( '#^' . preg_quote( $casa . self::BASE . '/', '#' ) . '([a-z0-9-]+)/#', $percorso, $m ) || ! self::acceso( $m[1] ) ) {
            return;
        }
        $dir  = self::cartella( $m[1] );
        $json = '' !== $dir ? json_decode( (string) file_get_contents( $dir . '/theme.json' ), true ) : null; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- file del tema
        if ( ! is_array( $json ) || empty( $json['woo'] ) ) {
            return;
        }
        self::$tema = [ 'id' => $m[1], 'dir' => $dir, 'json' => $json ];
        add_filter( 'woocommerce_get_cart_url', [ __CLASS__, 'carrello_della_demo' ], 99 );
        add_filter( 'woocommerce_get_checkout_url', [ __CLASS__, 'cassa_della_demo' ], 99 );
        add_filter( 'pre_option_olobuild_woo_senza_prezzo', [ __CLASS__, 'senza_prezzo_della_demo' ] );
        self::solo_il_negozio_del_tema();
    }

    /**
     * Più negozi dimostrativi sullo stesso sito (Fulmine, Tacet…): nella demo di uno, le tile vedono solo i suoi
     * prodotti e le sue categorie (meta _olo_demo = id del tema, scritto dagli script dei prodotti di prova). Prima
     * le griglie e la ricerca di Fulmine avrebbero mostrato anche le sculture di Tacet.
     */
    private static function solo_il_negozio_del_tema() {
        add_action( 'pre_get_posts', [ __CLASS__, 'prodotti_del_tema' ] );
        add_filter( 'get_terms_args', [ __CLASS__, 'categorie_del_tema' ], 10, 2 );
    }

    /** Le ricerche di prodotti nella demo: solo quelli del tema. */
    public static function prodotti_del_tema( $q ) {
        $tipi = (array) $q->get( 'post_type' );
        if ( ! in_array( 'product', $tipi, true ) || empty( self::$tema['id'] ) ) {
            return;
        }
        $mq   = (array) $q->get( 'meta_query' );
        $mq[] = [ 'key' => '_olo_demo', 'value' => (string) self::$tema['id'] ];
        $q->set( 'meta_query', $mq );
    }

    /** Le categorie dei prodotti nella demo: solo quelle del tema. */
    public static function categorie_del_tema( $args, $tassonomie ) {
        if ( ! in_array( 'product_cat', (array) $tassonomie, true ) || empty( self::$tema['id'] ) ) {
            return $args;
        }
        $mq                 = isset( $args['meta_query'] ) && is_array( $args['meta_query'] ) ? $args['meta_query'] : [];
        $mq[]               = [ 'key' => '_olo_demo', 'value' => (string) self::$tema['id'] ];
        $args['meta_query'] = $mq;
        return $args;
    }

    /** Nella demo di un negozio, la scritta dei prodotti senza prezzo del tema (theme.json → woo → senza_prezzo). */
    public static function senza_prezzo_della_demo( $valore ) {
        $testo = self::$tema['json']['woo']['senza_prezzo'] ?? null;
        return is_string( $testo ) ? $testo : $valore;
    }

    /**
     * Fuori dalle demo: un prodotto di WooCommerce dei dati d'esempio di un tema (meta _olo_demo = id del tema),
     * aperto dal suo indirizzo di sempre, porta alla sua scheda dentro la demo, se il sito la mostra e il tema ha
     * la scheda dei prodotti (theme.json → singles → product). Come le strutture d'esempio di OLObooking: i
     * prodotti di prova vivono nella demo, col negozio del tema attorno.
     */
    public static function prodotto_nella_demo() {
        if ( ! is_singular( 'product' ) ) {
            return;
        }
        $post = get_queried_object();
        if ( ! ( $post instanceof WP_Post ) ) {
            return;
        }
        $tema = (string) get_post_meta( $post->ID, '_olo_demo', true );
        $url  = '' !== $tema ? self::indirizzo_singolo( $tema, $post ) : '';
        if ( '' !== $url ) {
            wp_safe_redirect( $url, 302 );
            exit;
        }
    }

    /**
     * Nella demo di un negozio: il carrello e la cassa di WooCommerce sono le pagine del tema che theme.json → woo
     * indica ({"cart": "carrello", "checkout": "cassa"}, chiavi di pages). Senza, «Vai al carrello» usciva dalla
     * demo e apriva il carrello del sito che la ospita, con la sua grafica.
     */
    public static function carrello_della_demo( $url ) {
        return self::pagina_woo( 'cart', $url );
    }

    /** Come carrello_della_demo(), per la cassa. */
    public static function cassa_della_demo( $url ) {
        return self::pagina_woo( 'checkout', $url );
    }

    /**
     * La pagina della demo indicata come cassa (theme.json → woo → checkout) è la cassa per WooCommerce: le tile
     * della cassa la riconoscono e WooCommerce carica i suoi script. Inviare l'ordine resta impossibile: nella
     * demo i moduli non partono (moduli_fermi()).
     */
    public static function e_la_cassa( $si ) {
        $chiave = (string) ( self::$tema['json']['woo']['checkout'] ?? '' );
        return ( '' !== $chiave && self::$pagina === $chiave ) ? true : $si;
    }

    /** L'indirizzo intero della pagina del tema che theme.json → woo → $ruolo indica; $url se non ce n'è una. */
    private static function pagina_woo( $ruolo, $url ) {
        $chiave = (string) ( self::$tema['json']['woo'][ $ruolo ] ?? '' );
        $pagina = ( '' !== $chiave ) ? ( self::$tema['json']['pages'][ $chiave ] ?? null ) : null;
        if ( ! is_array( $pagina ) ) {
            return $url;
        }
        $pezzo = empty( $pagina['set_as_homepage'] ) ? self::slug( $chiave, $pagina ) . '/' : '';
        return self::con_palette( home_url( '/' . self::BASE . '/' . self::$tema['id'] . '/' . $pezzo ) );
    }

    /** La seconda prova di avvia(), coi temi portati dai plugin. */
    public static function avvia_tema_di_plugin() {
        $richiesta = self::leggi_richiesta();
        if ( $richiesta ) {
            self::aggancia( $richiesta );
        }
    }

    /** I filtri della demo, per il tema e la pagina della richiesta. */
    private static function aggancia( $richiesta ) {
        list( self::$tema, self::$pagina ) = $richiesta;
        self::$palette = self::palette_richiesta();

        // La scheda di un contenuto vero si cerca quando i tipi di contenuto esistono; nella demo i suoi
        // collegamenti restano nella demo.
        if ( 'singolo' === self::$pagina ) {
            add_action( 'wp_loaded', [ __CLASS__, 'risolvi_singolo' ], 1 );
        }
        add_filter( 'post_type_link', [ __CLASS__, 'link_singolo' ], 99, 2 );
        if ( ! empty( self::$tema['json']['woo'] ) ) {
            add_filter( 'woocommerce_get_cart_url', [ __CLASS__, 'carrello_della_demo' ], 99 );
            add_filter( 'woocommerce_get_checkout_url', [ __CLASS__, 'cassa_della_demo' ], 99 );
            add_filter( 'woocommerce_is_checkout', [ __CLASS__, 'e_la_cassa' ] );
            add_filter( 'pre_option_olobuild_woo_senza_prezzo', [ __CLASS__, 'senza_prezzo_della_demo' ] );
            self::solo_il_negozio_del_tema();
        }

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
        if ( ! preg_match( '#^' . preg_quote( $casa . self::BASE . '/', '#' ) . '([a-z0-9-]+)(?:/([a-z0-9-]+))?(?:/([a-z0-9-]+))?/?$#', $percorso, $m ) ) {
            return null;
        }
        $id = $m[1];
        if ( ! self::acceso( $id ) ) {
            return null;
        }
        $dir = self::cartella( $id );
        self::$riprova = '' === $dir;
        if ( '' === $dir ) {
            return null;
        }
        $file = $dir . '/theme.json';
        $json = json_decode( (string) file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- file del plugin
        if ( ! is_array( $json ) || empty( $json['templates'] ) || empty( $json['pages'] ) || ! is_array( $json['pages'] ) ) {
            return null;
        }
        // La pagina di uno spettacolo: il template singolo del tema (theme.json → singles) con la voce del Cartellone
        // che ha quel titolo. Uno spettacolo che il tema non ha mostra la 404 del tema.
        if ( isset( $m[3] ) && '' !== $m[3] ) {
            if ( self::BASE_SPETTACOLI === ( $m[2] ?? '' ) ) {
                $voce = ! empty( $json['singles']['olo_spettacolo'] ) ? self::voce_del_tema( $dir, $json, $m[3] ) : null;
                if ( $voce ) {
                    self::$spettacolo = $voce;
                    return [ [ 'id' => $id, 'dir' => $dir, 'json' => $json ], 'spettacolo' ];
                }
            } elseif ( ! empty( $json['singles'] ) && is_array( $json['singles'] ) ) {
                // La scheda di un contenuto vero del sito (/demo/<tema>/<base>/<slug>/, come il suo indirizzo
                // fuori dalla demo): il contenuto si cerca a wp_loaded, e se non c'è si mostra la 404 del tema.
                self::$singolo_chiesto = [ (string) $m[2], (string) $m[3] ];
                return [ [ 'id' => $id, 'dir' => $dir, 'json' => $json ], 'singolo' ];
            }
            return empty( $json['activate']['404'] ) ? null : [ [ 'id' => $id, 'dir' => $dir, 'json' => $json ], '404' ];
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

    /**
     * La cartella da cui la demo disegna il tema, '' se su questo sito il tema non ha una demo: i temi inclusi
     * nel plugin sì; un tema del catalogo remoto (che vive in un plugin che il sito non ha) no. La Vetrina temi
     * mostra solo i temi che hanno una cartella qui.
     */
    public static function cartella( $id ) {
        $id = (string) $id;
        if ( ! preg_match( '/^[a-z0-9-]+$/', $id ) ) {
            return '';
        }
        $dir = OLOBUILD_PATH . 'assets/data/themes/' . $id;
        if ( is_readable( $dir . '/theme.json' ) ) {
            return $dir;
        }
        // I temi dei plugin, come nel selettore dei temi (olobuild_external_theme_dirs): una cartella col
        // theme.json di un tema, oppure una che contiene le cartelle dei temi.
        foreach ( (array) apply_filters( 'olobuild_external_theme_dirs', [] ) as $extra ) {
            $extra = rtrim( wp_normalize_path( (string) $extra ), '/' );
            if ( '' === $extra || ! is_dir( $extra ) ) {
                continue;
            }
            if ( is_readable( $extra . '/theme.json' ) ) {
                $json = json_decode( (string) file_get_contents( $extra . '/theme.json' ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- file del plugin che porta il tema
                if ( is_array( $json ) && ( $json['id'] ?? '' ) === $id ) {
                    return $extra;
                }
                continue;
            }
            if ( is_readable( $extra . '/' . $id . '/theme.json' ) ) {
                return $extra . '/' . $id;
            }
        }
        return '';
    }

    /**
     * L'indirizzo della scheda di un contenuto vero nella demo di un tema, da qualunque richiesta: '' se qui il
     * tema non ha la demo accesa o non ha una scheda per quel contenuto. Per i plugin che portano temi: OLObooking
     * manda qui le sue strutture d'esempio quando il sito mostra la demo del tema ma non ne usa la scheda.
     */
    public static function indirizzo_singolo( $tema, $post ) {
        $tema = (string) $tema;
        if ( ! ( $post instanceof WP_Post ) || 'publish' !== $post->post_status || ! self::acceso( $tema ) ) {
            return '';
        }
        $dir  = self::cartella( $tema );
        $json = '' !== $dir ? json_decode( (string) file_get_contents( $dir . '/theme.json' ), true ) : null; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- file del tema
        if ( ! is_array( $json ) || '' === self::scheda_in( $json, $post ) ) {
            return '';
        }
        return self::indirizzo_contenuto( $tema, $post );
    }

    /**
     * Il template della scheda che un tema ha per un contenuto: la chiave di theme.json → singles è il tipo di
     * contenuto, o quella che dice il plugin del contenuto (filtro olobuild_theme_single_key: OLObooking risponde
     * «accommodation» per le sue strutture). '' se il tema non ha una scheda per lui.
     */
    private static function scheda_in( $json, $post ) {
        $chiave = (string) apply_filters( 'olobuild_theme_single_key', $post->post_type, $post );
        $tpl    = (string) ( $json['singles'][ $chiave ] ?? '' );
        return ( '' !== $tpl && isset( $json['templates'][ $tpl ] ) ) ? $tpl : '';
    }

    /** /demo/<tema>/<base>/<slug>/, con la base dell'indirizzo del tipo di contenuto (la sua rewrite, o il nome). */
    private static function indirizzo_contenuto( $tema, $post ) {
        $tipo = get_post_type_object( $post->post_type );
        if ( ! $tipo || '' === (string) $post->post_name ) {
            return '';
        }
        $rw   = is_array( $tipo->rewrite ) ? (string) ( $tipo->rewrite['slug'] ?? '' ) : '';
        $base = '' !== $rw ? $rw : $tipo->name;
        if ( ! preg_match( '/^[a-z0-9-]+$/', $base ) || ! preg_match( '/^[a-z0-9-]+$/', (string) $post->post_name ) ) {
            return '';
        }
        return home_url( '/' . self::BASE . '/' . sanitize_title( $tema ) . '/' . $base . '/' . $post->post_name . '/' );
    }

    /**
     * wp_loaded: la scheda chiesta è un contenuto pubblicato di un tipo che ha <base> come indirizzo, e il tema
     * ha una scheda per lui. Altrimenti la pagina diventa la 404 del tema.
     */
    public static function risolvi_singolo() {
        $base = (string) ( self::$singolo_chiesto[0] ?? '' );
        $slug = (string) ( self::$singolo_chiesto[1] ?? '' );
        foreach ( get_post_types( [ 'public' => true ], 'objects' ) as $tipo ) {
            $rw = is_array( $tipo->rewrite ) ? (string) ( $tipo->rewrite['slug'] ?? '' ) : '';
            if ( ( '' !== $rw ? $rw : $tipo->name ) !== $base ) {
                continue;
            }
            $post = get_page_by_path( $slug, OBJECT, $tipo->name );
            $tpl  = ( $post instanceof WP_Post && 'publish' === $post->post_status ) ? self::scheda_in( self::$tema['json'], $post ) : '';
            if ( '' !== $tpl ) {
                self::$singolo     = $post;
                self::$singolo_tpl = $tpl;
                return;
            }
        }
        self::$pagina = '404';
    }

    /** Il contenuto vero di cui la richiesta mostra la scheda nella demo (null fuori da una scheda). */
    public static function singolo() {
        return self::$singolo instanceof WP_Post ? self::$singolo : null;
    }

    /** post_type_link nella demo: i contenuti che il tema mostra con la sua scheda restano dentro la demo. */
    public static function link_singolo( $link, $post ) {
        if ( ! ( $post instanceof WP_Post ) || 'publish' !== $post->post_status || '' === self::scheda_in( self::$tema['json'], $post ) ) {
            return $link;
        }
        $url = self::indirizzo_contenuto( self::$tema['id'], $post );
        return '' !== $url ? self::con_palette( $url ) : $link;
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

    /** Le voci dei Cartelloni del tema, una volta ciascuna (per id), lette dai suoi file. */
    private static function voci_del_tema( $dir, $json ) {
        $voci = [];
        $giro = static function ( $nodi ) use ( &$giro, &$voci ) {
            foreach ( (array) $nodi as $n ) {
                if ( ! is_array( $n ) ) {
                    continue;
                }
                if ( 'cartellone' === ( $n['type'] ?? '' ) ) {
                    foreach ( (array) ( $n['settings']['voci'] ?? [] ) as $v ) {
                        $k = is_array( $v ) ? (string) ( $v['id'] ?? '' ) : '';
                        if ( '' !== $k && ! isset( $voci[ $k ] ) ) {
                            $voci[ $k ] = $v;
                        }
                    }
                }
                if ( ! empty( $n['children'] ) ) {
                    $giro( $n['children'] );
                }
            }
        };
        foreach ( (array) ( $json['templates'] ?? [] ) as $meta ) {
            $file = $dir . '/' . basename( (string) ( $meta['file'] ?? '' ) );
            if ( ! is_readable( $file ) ) {
                continue;
            }
            $testo = (string) file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- file del plugin
            if ( false === strpos( $testo, '"cartellone"' ) ) {
                continue;
            }
            $dati = json_decode( $testo, true );
            if ( is_array( $dati ) ) {
                $giro( isset( $dati['content'] ) && ! isset( $dati[0] ) ? $dati['content'] : $dati );
            }
        }
        return $voci;
    }

    /** La voce del tema col titolo di quello slug, o null. */
    private static function voce_del_tema( $dir, $json, $slug ) {
        foreach ( self::voci_del_tema( $dir, $json ) as $v ) {
            if ( sanitize_title( (string) ( $v['titolo'] ?? '' ) ) === $slug ) {
                return $v;
            }
        }
        return null;
    }

    /** La voce dello spettacolo mostrato, coi segnaposto risolti come nelle pagine (foto della libreria, link della demo). */
    private static function spettacolo() {
        if ( ! is_array( self::$spettacolo ) ) {
            return null;
        }
        $v = self::prepara( [ self::$spettacolo ] );
        return isset( $v[0] ) && is_array( $v[0] ) ? $v[0] : null;
    }

    /** Indirizzo (relativo) della pagina di uno spettacolo nella demo, '' se il tema non ha la scheda dello spettacolo. */
    public static function indirizzo_spettacolo( $titolo ) {
        $slug = sanitize_title( (string) $titolo );
        if ( empty( self::$tema['json']['singles']['olo_spettacolo'] ) || '' === $slug ) {
            return '';
        }
        return self::con_palette( wp_make_link_relative( home_url( '/' . self::BASE . '/' . self::$tema['id'] . '/' . self::BASE_SPETTACOLI . '/' . $slug . '/' ) ) );
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
        return self::con_palette( wp_make_link_relative( home_url( '/' . self::BASE . '/' . self::$tema['id'] . '/' . $pezzo ) ) );
    }

    /* ── Palette da provare (bollini del visore) ── */

    /**
     * Le palette del file dati. Una palette non porta colori fissi ma famiglie: per primario, secondario e
     * accento la tinta (gradi) e la saturazione (%) dei toni scuri e di quelli chiari; per i neutri chiari
     * e scuri la tinta e la saturazione minima e massima. Ogni ruolo del tema prende la tinta della sua
     * famiglia e tiene la SUA luminanza (mappa_colori()): i contrasti restano quelli del tema, qualunque
     * combinazione di ruoli il tema usi. Nomi non tradotti: si legge anche all'avvio, prima che le
     * traduzioni si possano caricare.
     */
    private static function dati_palette() {
        static $pal = null;
        if ( null === $pal ) {
            $pal  = [];
            $file = OLOBUILD_PATH . self::FILE_PALETTE;
            $json = is_readable( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : null; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- file del plugin
            foreach ( is_array( $json ) ? $json : [] as $id => $p ) {
                $ok = is_array( $p );
                foreach ( [ 'primary', 'secondary', 'accent', 'neutri_chiari', 'neutri_scuri' ] as $k ) {
                    $ok = $ok && isset( $p[ $k ] ) && is_array( $p[ $k ] ) && 3 === count( array_filter( $p[ $k ], 'is_numeric' ) );
                }
                if ( $ok ) {
                    $pal[ sanitize_key( (string) $id ) ] = $p;
                }
            }
        }
        return $pal;
    }

    /**
     * Le palette per i bollini del visore, col nome nella lingua di chi guarda e i due colori del bollino
     * (primario e accento) per i temi chiari e per quelli scuri.
     */
    public static function palette() {
        $out = [];
        foreach ( self::dati_palette() as $id => $p ) {
            $out[ $id ] = [
                'nome'   => olobuild_t( (string) ( $p['nome'] ?? $id ) ),
                'chiaro' => [ 'primary' => self::tono( $p, 'primary', 0.16 ), 'accent' => self::tono( $p, 'accent', 0.2 ) ],
                'scuro'  => [ 'primary' => self::tono( $p, 'primary', 0.45 ), 'accent' => self::tono( $p, 'accent', 0.4 ) ],
            ];
        }
        return $out;
    }

    /** Il colore pieno di una famiglia della palette alla luminanza data (bollini). */
    private static function tono( $p, $fam, $y ) {
        return self::con_luminanza( (float) $p[ $fam ][0], self::saturazione( $p, $fam, $y ), $y );
    }

    /** Saturazione della famiglia: quella dei toni scuri fino a 0,08 di luminanza, quella dei chiari da 0,45. */
    private static function saturazione( $p, $fam, $y ) {
        $t = min( 1.0, max( 0.0, ( $y - 0.08 ) / 0.37 ) );
        return ( (float) $p[ $fam ][1] + ( (float) $p[ $fam ][2] - (float) $p[ $fam ][1] ) * $t ) / 100;
    }

    /** Luminanza relativa (WCAG) di un colore [ r, g, b ] coi canali fra 0 e 1. */
    private static function luminanza( $rgb ) {
        $l = 0.0;
        foreach ( [ 0.2126, 0.7152, 0.0722 ] as $i => $peso ) {
            $v  = (float) $rgb[ $i ];
            $l += $peso * ( $v <= 0.03928 ? $v / 12.92 : pow( ( $v + 0.055 ) / 1.055, 2.4 ) );
        }
        return $l;
    }

    /** I canali lineari dei 256 valori di un canale a 8 bit (per provare in fretta gli arrotondamenti). */
    private static function lineari() {
        static $t = null;
        if ( null === $t ) {
            $t = [];
            for ( $i = 0; $i < 256; $i++ ) {
                $v       = $i / 255;
                $t[ $i ] = $v <= 0.03928 ? $v / 12.92 : pow( ( $v + 0.055 ) / 1.055, 2.4 );
            }
        }
        return $t;
    }

    /** Da tinta, luce e saturazione (fra 0 e 1, nell'ordine di colorsys) a [ r, g, b ] fra 0 e 1. */
    private static function hsl_rgb( $h, $l, $s ) {
        if ( 0.0 === (float) $s ) {
            return [ $l, $l, $l ];
        }
        $m2 = $l <= 0.5 ? $l * ( 1.0 + $s ) : $l + $s - ( $l * $s );
        $m1 = 2.0 * $l - $m2;
        $v  = static function ( $t ) use ( $m1, $m2 ) {
            $t -= floor( $t );
            if ( $t < 1.0 / 6.0 ) {
                return $m1 + ( $m2 - $m1 ) * $t * 6.0;
            }
            if ( $t < 0.5 ) {
                return $m2;
            }
            if ( $t < 2.0 / 3.0 ) {
                return $m1 + ( $m2 - $m1 ) * ( 2.0 / 3.0 - $t ) * 6.0;
            }
            return $m1;
        };
        return [ $v( $h + 1.0 / 3.0 ), $v( $h ), $v( $h - 1.0 / 3.0 ) ];
    }

    /**
     * Il colore di tinta $h (gradi) e saturazione $s (0-1) con luminanza $y: in HSL la luminanza cresce con
     * la luce, che si cerca per bisezione. Fra gli arrotondamenti a 8 bit vicini vince il più vicino al
     * colore ideale DAL LATO che non toglie contrasto: più chiaro per i colori chiari (sopra 0,1791,
     * dove il contrasto col bianco e col nero si equivale), più scuro per gli altri. Senza aggiungere
     * tinta: la distanza fra il canale più alto e il più basso resta quella del colore ideale (due unità di
     * scarto), se no un grigio scuro diventerebbe verdognolo.
     */
    private static function con_luminanza( $h, $s, $y ) {
        $lo = 0.0;
        $hi = 1.0;
        for ( $i = 0; $i < 40; $i++ ) {
            $m = ( $lo + $hi ) / 2;
            if ( self::luminanza( self::hsl_rgb( $h / 360, $m, $s ) ) < $y ) {
                $lo = $m;
            } else {
                $hi = $m;
            }
        }
        $rgb    = self::hsl_rgb( $h / 360, ( $lo + $hi ) / 2, $s );
        $base   = [ (int) round( $rgb[0] * 255 ), (int) round( $rgb[1] * 255 ), (int) round( $rgb[2] * 255 ) ];
        $lin      = self::lineari();
        $chiaro   = $y >= 0.1791;
        $ampiezza = ( max( $rgb ) - min( $rgb ) ) * 255;
        $meglio   = null;
        $scelto   = $base;
        for ( $a = -4; $a <= 4; $a++ ) {
            for ( $b = -4; $b <= 4; $b++ ) {
                for ( $c = -4; $c <= 4; $c++ ) {
                    $q = [ min( 255, max( 0, $base[0] + $a ) ), min( 255, max( 0, $base[1] + $b ) ), min( 255, max( 0, $base[2] + $c ) ) ];
                    if ( abs( ( max( $q ) - min( $q ) ) - $ampiezza ) > 2 ) {
                        continue;
                    }
                    $lq = 0.2126 * $lin[ $q[0] ] + 0.7152 * $lin[ $q[1] ] + 0.0722 * $lin[ $q[2] ];
                    if ( $chiaro ? $lq < $y : $lq > $y ) {
                        continue;
                    }
                    // Vince il più vicino al colore ideale, poi alla luminanza: cercando prima la luminanza
                    // esatta si poteva ripescare il colore del tema (stessa luminanza, tinta di prima).
                    $e1 = abs( $lq - $y );
                    $e2 = abs( $a ) + abs( $b ) + abs( $c );
                    if ( null === $meglio || $e2 < $meglio[1] || ( $e2 === $meglio[1] && $e1 < $meglio[0] ) ) {
                        $meglio = [ $e1, $e2 ];
                        $scelto = $q;
                    }
                }
            }
        }
        return sprintf( '#%02X%02X%02X', $scelto[0], $scelto[1], $scelto[2] );
    }

    /** [ r, g, b ] fra 0 e 1 di un esadecimale (#abc o #aabbcc), null se non lo è. */
    private static function rgb( $hex ) {
        $hex = ltrim( (string) sanitize_hex_color( (string) $hex ), '#' );
        if ( 3 === strlen( $hex ) ) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if ( 6 !== strlen( $hex ) ) {
            return null;
        }
        return [ hexdec( substr( $hex, 0, 2 ) ) / 255, hexdec( substr( $hex, 2, 2 ) ) / 255, hexdec( substr( $hex, 4, 2 ) ) / 255 ];
    }

    /**
     * Un colore del tema nella famiglia della palette, con la sua luminanza. Primario, secondario e accento
     * prendono la saturazione della famiglia, ridotta se nel tema il ruolo è quasi grigio (un secondario
     * nero resta quasi nero) ma mai sotto quella dei neutri; i neutri tengono la loro saturazione dentro i
     * limiti della palette.
     */
    private static function mappa_colore( $hex, $fam, $p ) {
        $rgb = self::rgb( $hex );
        if ( null === $rgb ) {
            return $hex;
        }
        $y     = self::luminanza( $rgb );
        $max   = max( $rgb );
        $min   = min( $rgb );
        $banda = $p[ $y > 0.5 ? 'neutri_chiari' : 'neutri_scuri' ];
        if ( in_array( $fam, [ 'primary', 'secondary', 'accent' ], true ) ) {
            $k = min( 1.0, ( $max - $min ) / 0.2 );
            return self::con_luminanza( (float) $p[ $fam ][0], max( self::saturazione( $p, $fam, $y ) * $k, (float) $banda[1] / 100 ), $y );
        }
        $somma = $max + $min;
        $s     = $max === $min ? 0.0 : ( $somma / 2 <= 0.5 ? ( $max - $min ) / $somma : ( $max - $min ) / ( 2.0 - $somma ) );
        return self::con_luminanza( (float) $banda[0], min( (float) $banda[2] / 100, max( (float) $banda[1] / 100, $s ) ), $y );
    }

    /** I ruoli del tema (anche un elenco parziale, come i colori della modalità scura) nella palette. */
    private static function mappa_colori( $colori, $p ) {
        $out = [];
        foreach ( $colori as $ruolo => $hex ) {
            if ( 'link' !== $ruolo ) {
                $out[ $ruolo ] = self::mappa_colore( (string) $hex, in_array( $ruolo, [ 'primary', 'secondary', 'accent' ], true ) ? $ruolo : 'neutro', $p );
            }
        }
        // Il link segue il ruolo di cui ha il colore (di solito il primario), se no la famiglia del primario.
        if ( isset( $colori['link'] ) ) {
            $out['link'] = self::come_un_ruolo( (string) $colori['link'], $colori, $out ) ?? self::mappa_colore( (string) $colori['link'], 'primary', $p );
        }
        return $out;
    }

    /** Il colore già nella palette del ruolo che nel tema ha lo stesso colore, null se nessuno. */
    private static function come_un_ruolo( $hex, $colori, $mappati, $ruoli = [ 'primary', 'accent', 'secondary', 'text', 'dark' ] ) {
        foreach ( $ruoli as $r ) {
            if ( isset( $colori[ $r ], $mappati[ $r ] ) && 0 === strcasecmp( (string) $colori[ $r ], (string) $hex ) ) {
                return $mappati[ $r ];
            }
        }
        return null;
    }

    /**
     * Un colore del tema fuori dai ruoli (campi dei moduli, cursore, HUD), esadecimale o dentro un valore
     * con rgba(): segue il ruolo di cui ha il colore, se no la famiglia $fam con la sua luminanza.
     */
    private static function in_palette( $valore, $fam ) {
        $orig  = (array) ( self::$tema['json']['styles']['colors'] ?? [] );
        $ruoli = array_unique( array_merge( [ 'primary', 'accent', 'secondary', 'link', 'text', 'background', 'light', 'muted', 'border', 'dark', 'text_muted' ], array_keys( $orig ) ) );
        $p     = self::dati_palette()[ self::$palette ];
        $uno   = static function ( $hex ) use ( $orig, $ruoli, $p, $fam ) {
            return self::come_un_ruolo( $hex, $orig, self::colori(), $ruoli ) ?? self::mappa_colore( $hex, $fam, $p );
        };
        $valore = trim( (string) $valore );
        if ( sanitize_hex_color( $valore ) ) {
            return $uno( $valore );
        }
        return (string) preg_replace_callback(
            '/rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*(,\s*[\d.]+\s*)?\)/',
            static function ( $m ) use ( $uno ) {
                $hex = sprintf( '#%02X%02X%02X', min( 255, (int) $m[1] ), min( 255, (int) $m[2] ), min( 255, (int) $m[3] ) );
                return 'rgba(' . implode( ',', sscanf( $uno( $hex ), '#%02x%02x%02x' ) ) . ( isset( $m[4] ) ? $m[4] : ',1' ) . ')';
            },
            $valore
        );
    }

    /** La palette chiesta con ?palette= (sola lettura, scelta fra quelle del file), '' se nessuna. */
    private static function palette_richiesta() {
        $id = isset( $_GET['palette'] ) ? sanitize_key( wp_unslash( $_GET['palette'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- sola lettura: sceglie una delle palette del file dati
        return ( '' !== $id && isset( self::dati_palette()[ $id ] ) ) ? $id : '';
    }

    /** L'indirizzo con la palette scelta: navigando nella demo i colori provati restano. */
    private static function con_palette( $url ) {
        return '' === self::$palette ? $url : add_query_arg( 'palette', self::$palette, $url );
    }

    /**
     * I colori del tema, nella palette scelta se c'è: ogni ruolo con la tinta della sua famiglia e la sua
     * luminanza. Tutto il resto del tema (miscele, veli, sfumature) parte dai ruoli e li segue; i colori
     * scritti nelle tile restano, e con le luminanze invariate restano leggibili sui ruoli come prima.
     */
    private static function colori() {
        static $c = null;
        if ( null !== $c ) {
            return $c;
        }
        $colori = (array) ( self::$tema['json']['styles']['colors'] ?? [] );
        if ( '' === self::$palette ) {
            return $colori;
        }
        $c = self::mappa_colori( $colori, self::dati_palette()[ self::$palette ] );
        return $c;
    }

    /* ── Stile del tema ── */

    public static function stili( $valore ) {
        $s = self::$tema['json']['styles'] ?? null;
        if ( ! is_array( $s ) ) {
            return $valore;
        }
        // Palette provata nel visore: i ruoli del tema cambiano tutti insieme, e con la stessa regola i
        // colori della modalità scura del tema e quelli dei campi dei moduli.
        $c  = self::colori();
        $dc = (array) ( $s['dark_colors'] ?? [] );
        if ( '' !== self::$palette ) {
            foreach ( [ 'field_bg' => 'neutro', 'field_border_color' => 'neutro', 'focus_border_color' => 'primary', 'focus_shadow' => 'primary' ] as $k => $fam ) {
                if ( isset( $s['forms'][ $k ] ) && is_string( $s['forms'][ $k ] ) ) {
                    $s['forms'][ $k ] = self::in_palette( $s['forms'][ $k ], $fam );
                }
            }
            $s['colors'] = $c;
            $dc          = $dc ? self::mappa_colori( $dc, self::dati_palette()[ self::$palette ] ) : [];
        }
        // Come all'import (Olobuild_Style_System::sync_dark_palette): in modalità scura i ruoli del
        // marchio restano quelli del tema, non l'indaco di serie (vale per i temi con l'interruttore).
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
        // Con una palette i colori del cursore e dell'HUD (quelli di un ruolo del tema) la seguono.
        if ( '' !== self::$palette ) {
            foreach ( $c as $k => $v ) {
                if ( is_string( $v ) && preg_match( '/^\s*(#[0-9a-f]{3}\b|#[0-9a-f]{6}\b|rgba?\()/i', $v ) ) {
                    $c[ $k ] = self::in_palette( $v, 'accent' );
                }
            }
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
        if ( 'spettacolo' === self::$pagina ) {
            return self::id_template( (string) ( self::$tema['json']['singles']['olo_spettacolo'] ?? '' ) );
        }
        if ( 'singolo' === self::$pagina ) {
            return '' !== self::$singolo_tpl ? self::id_template( self::$singolo_tpl ) : 0;
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
        $casa = file_exists( self::$tema['dir'] . '/logo.png' ) ? self::url_cartella( self::$tema['dir'] ) : '';
        $logo = '' !== $casa ? $casa . '/logo.png' : '';
        $json = str_replace( 'LOGO_PLACEHOLDER', $logo, $json );
        $json = (string) preg_replace( '/"menu_id"\s*:\s*"auto"/', '"menu_id":' . self::id_menu(), $json );
        $json = self::risolvi( $json );
        if ( '' !== self::$palette ) {
            $json = self::colori_fissi( $json );
        }
        $nuovi = json_decode( $json, true );
        return is_array( $nuovi ) ? $nuovi : [];
    }

    /**
     * Con una palette, anche i colori scritti fissi nelle tile la seguono (prima restavano quelli del tema: in
     * Moto Perpetuo il verde neon e il blu non cambiavano mai). Esadecimali e rgba(): quello di un ruolo del
     * tema segue il ruolo, un grigio va fra i neutri, gli altri nella famiglia del ruolo di tinta più vicina;
     * la luminanza resta, e con lei i contrasti.
     */
    private static function colori_fissi( $json ) {
        $json = (string) preg_replace_callback(
            '/(?<=[\s"(:,])#(?:[0-9a-fA-F]{6}|[0-9a-fA-F]{3})(?=[\s"),;])/',
            static function ( $m ) {
                return self::colore_fisso( $m[0] );
            },
            $json
        );
        return (string) preg_replace_callback(
            '/rgba?\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*(?:,\s*[\d.]+\s*)?\)/',
            static function ( $m ) {
                return self::colore_fisso( $m[0] );
            },
            $json
        );
    }

    /** Un colore fisso nella palette scelta (vedi colori_fissi()), una volta per colore. */
    private static function colore_fisso( $valore ) {
        static $fatti = [];
        if ( ! isset( $fatti[ $valore ] ) ) {
            $hex = $valore;
            if ( '#' !== $valore[0] && preg_match( '/(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})/', $valore, $c ) ) {
                $hex = sprintf( '#%02X%02X%02X', min( 255, (int) $c[1] ), min( 255, (int) $c[2] ), min( 255, (int) $c[3] ) );
            }
            $fatti[ $valore ] = self::in_palette( $valore, self::famiglia_di( $hex ) );
        }
        return $fatti[ $valore ];
    }

    /** La famiglia di un colore fisso: 'neutro' se quasi grigio, se no il ruolo del tema di tinta più vicina. */
    private static function famiglia_di( $hex ) {
        $tinta = static function ( $rgb ) {
            $max = max( $rgb );
            $min = min( $rgb );
            if ( $max - $min < 0.12 ) {
                return null;
            }
            $d = $max - $min;
            if ( $max === $rgb[0] ) {
                $h = fmod( ( $rgb[1] - $rgb[2] ) / $d, 6 );
            } elseif ( $max === $rgb[1] ) {
                $h = ( $rgb[2] - $rgb[0] ) / $d + 2;
            } else {
                $h = ( $rgb[0] - $rgb[1] ) / $d + 4;
            }
            return fmod( $h * 60 + 360, 360 );
        };
        $rgb = self::rgb( $hex );
        $h   = null === $rgb ? null : $tinta( $rgb );
        if ( null === $h ) {
            return 'neutro';
        }
        $orig   = (array) ( self::$tema['json']['styles']['colors'] ?? [] );
        $meglio = [ 'accent', 999 ];
        foreach ( [ 'primary', 'accent', 'secondary' ] as $ruolo ) {
            $r  = self::rgb( (string) ( $orig[ $ruolo ] ?? '' ) );
            $hr = null === $r ? null : $tinta( $r );
            if ( null === $hr ) {
                continue;
            }
            $dist = abs( $h - $hr );
            $dist = min( $dist, 360 - $dist );
            if ( $dist < $meglio[1] ) {
                $meglio = [ $ruolo, $dist ];
            }
        }
        return $meglio[0];
    }

    /** L'indirizzo di una cartella dentro wp-content: quella dei temi di Olobuild o di un plugin che porta temi. '' fuori. */
    private static function url_cartella( $dir ) {
        $dir = wp_normalize_path( (string) $dir );
        $basi = [
            [ wp_normalize_path( OLOBUILD_PATH ), OLOBUILD_URL ],
            [ trailingslashit( wp_normalize_path( WP_CONTENT_DIR ) ), trailingslashit( content_url() ) ],
        ];
        foreach ( $basi as $b ) {
            if ( 0 === strpos( $dir, $b[0] ) ) {
                return untrailingslashit( $b[1] . ltrim( substr( $dir, strlen( $b[0] ) ), '/' ) );
            }
        }
        return '';
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
        // Le voci possono avere «children» (e i figli i loro): diventano voci con menu_item_parent, come in un
        // menu vero, e il Mega Menu le apre a colonne. «description» è la riga sotto la voce nel pannello.
        $nuove = [];
        $n     = 0;
        $aggiungi = static function ( $voci, $genitore ) use ( &$aggiungi, &$nuove, &$n ) {
            foreach ( array_values( (array) $voci ) as $v ) {
                $id     = self::ID_VOCE + $n;
                $n++;
                $titolo = (string) ( $v['title'] ?? '' );
                $nuove[] = new WP_Post( (object) [
                    'ID'               => $id,
                    'db_id'            => $id,
                    'menu_item_parent' => (string) $genitore,
                    'object_id'        => (string) $id,
                    'object'           => 'custom',
                    'type'             => 'custom',
                    'type_label'       => '',
                    'title'            => $titolo,
                    'url'              => self::risolvi( (string) ( $v['url'] ?? '#' ) ),
                    'target'           => '',
                    'attr_title'       => '',
                    'description'      => (string) ( $v['description'] ?? '' ),
                    'classes'          => [ '' ],
                    'xfn'              => '',
                    'menu_order'       => $n,
                    'post_type'        => 'nav_menu_item',
                    'post_status'      => 'publish',
                    'post_title'       => $titolo,
                    'post_name'        => 'olo-demo-voce-' . $n,
                    'post_parent'      => 0,
                    'post_content'     => '',
                    'post_excerpt'     => '',
                    'filter'           => 'raw',
                ] );
                if ( ! empty( $v['children'] ) && is_array( $v['children'] ) ) {
                    $aggiungi( $v['children'], $id );
                }
            }
        };
        $aggiungi( self::$tema['json']['menu']['items'] ?? [], 0 );
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
        if ( 'singolo' === self::$pagina ) {
            return self::come_il_contenuto( $disegna );
        }
        $p       = self::$tema['json']['pages'][ self::$pagina ] ?? [];
        $voce    = 'spettacolo' === self::$pagina ? self::spettacolo() : null;
        if ( $voce ) {
            // La pagina di uno spettacolo: i campi dinamici della scheda leggono la voce del tema.
            $p = [ 'title' => (string) ( $voce['titolo'] ?? '' ), 'slug' => sanitize_title( (string) ( $voce['titolo'] ?? '' ) ) ];
            if ( class_exists( 'Olobuild_Spettacoli' ) ) {
                Olobuild_Spettacoli::imposta_demo( $voce );
            }
        }
        $ora     = current_time( 'mysql' );
        $ora_gmt = current_time( 'mysql', true );
        $pagina  = new WP_Post( (object) [
            'ID'                    => 0,
            'post_author'           => 0,
            'post_date'             => $ora,
            'post_date_gmt'         => $ora_gmt,
            'post_content'          => '',
            'post_title'            => (string) ( $p['title'] ?? self::$pagina ),
            'post_excerpt'          => $voce ? (string) ( $voce['descrizione'] ?? '' ) : '',
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
            'post_type'             => $voce ? 'olo_spettacolo' : 'page',
            'post_mime_type'        => '',
            'comment_count'         => '0',
            'filter'                => 'raw',
        ] );
        $prima = [];
        foreach ( [ 'is_page', 'is_singular', 'queried_object', 'queried_object_id', 'post', 'posts', 'post_count', 'found_posts' ] as $campo ) {
            $prima[ $campo ] = $wp_query->$campo;
        }
        $post_prima                  = $GLOBALS['post'] ?? null;
        $wp_query->is_page           = ! $voce;
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
            if ( $voce && class_exists( 'Olobuild_Spettacoli' ) ) {
                Olobuild_Spettacoli::imposta_demo( null );
            }
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
     * La scheda di un contenuto vero (una struttura): mentre si disegna, WordPress vede quel contenuto come la
     * pagina chiesta, così le tile della scheda leggono i suoi dati come sul sito importato. Poi torna tutto com'era.
     */
    private static function come_il_contenuto( $disegna ) {
        global $wp_query;
        $post = self::$singolo;
        if ( ! ( $post instanceof WP_Post ) ) {
            return (string) call_user_func( $disegna );
        }
        $prima = [];
        foreach ( [ 'is_single', 'is_page', 'is_singular', 'is_home', 'queried_object', 'queried_object_id', 'post', 'posts', 'post_count', 'found_posts' ] as $campo ) {
            $prima[ $campo ] = $wp_query->$campo;
        }
        $post_prima                  = $GLOBALS['post'] ?? null;
        $wp_query->is_single         = true;
        $wp_query->is_page           = false;
        $wp_query->is_singular       = true;
        $wp_query->is_home           = false;
        $wp_query->queried_object    = $post;
        $wp_query->queried_object_id = (int) $post->ID;
        $wp_query->post              = $post;
        $wp_query->posts             = [ $post ];
        $wp_query->post_count        = 1;
        $wp_query->found_posts       = 1;
        $GLOBALS['post']             = $post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- solo durante il disegno, rimesso subito dopo
        setup_postdata( $post );
        try {
            return (string) call_user_func( $disegna );
        } finally {
            foreach ( $prima as $campo => $valore ) {
                $wp_query->$campo = $valore;
            }
            if ( $post_prima instanceof WP_Post ) {
                $GLOBALS['post'] = $post_prima; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- il valore di prima
                setup_postdata( $post_prima );
            } else {
                unset( $GLOBALS['post'] );
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
        $demo  = esc_url( self::con_palette( self::indirizzo_tema( self::$tema['id'] ) ) );
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
        if ( 'spettacolo' === self::$pagina && is_array( self::$spettacolo ) ) {
            return esc_html( (string) ( self::$spettacolo['titolo'] ?? '' ) . ' — ' . $nome );
        }
        if ( 'singolo' === self::$pagina && self::$singolo instanceof WP_Post ) {
            return esc_html( get_the_title( self::$singolo ) . ' — ' . $nome );
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
        $fondo = sanitize_hex_color( (string) ( self::colori()['background'] ?? '' ) );
        $css   = $fondo ? 'body.olo-demo-tema{background-color:' . $fondo . '}' : '';
        $css  .= '.olo-template.olo-demo-avviso{position:fixed;left:50%;bottom:24px;transform:translateX(-50%);z-index:2147483000;max-width:calc(100% - 32px);padding:12px 18px;border-radius:var(--olo-border-radius);background:var(--olo-color-dark);color:var(--olo-color-light);font:500 15px/1.45 var(--olo-font-family);box-shadow:var(--olo-shadow-large);text-align:center}';
        $css  .= '.olo-template.olo-demo-avviso[hidden]{display:none}';
        echo '<style id="olo-demo-tema-css">' . $css . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS fisso; l'unico valore variabile passa da sanitize_hex_color()
    }

    /**
     * Nella demo nessun modulo invia: ricerca, newsletter e gli altri si fermano qui con un avviso.
     * Il modulo di contatto va avanti da sé: il server riconosce la sua configurazione «demo» e risponde
     * con lo stesso avviso, così si vede anche come il tema mostra un invio riuscito. Passano anche i moduli
     * marcati data-olo-demo-libero, che non inviano dati ma aprono solo un indirizzo (Tracciamento spedizione).
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
        if (f ? (f.matches ? f.matches('form[data-olo-form], form[data-olo-demo-libero]') : false) : false) { return; }
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
