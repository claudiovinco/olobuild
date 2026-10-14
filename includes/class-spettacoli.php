<?php
/**
 * Olobuild_Spettacoli — gli spettacoli del sito: si scrivono una volta, li legge tutto il sito.
 *
 * Un tipo di contenuto di WordPress (olo_spettacolo, menu «Spettacoli»). Ogni spettacolo ha titolo, autore e
 * compagnia, genere, data e ora (e la data di fine se resta in scena più giorni), orari, luogo, durata, prezzo,
 * disponibilità, etichette, l'incontro col pubblico, la descrizione breve (l'estratto), il testo lungo (l'editor),
 * la foto (l'immagine in evidenza) e i link. Li leggono:
 *  - la tile Cartellone con «Appuntamenti da: Spettacoli del sito», in ogni sua copia (voci_per_cartellone());
 *  - la pagina di ogni spettacolo (/spettacoli/<titolo>/): il template singolo del tipo, scelto come per gli altri
 *    tipi (opzione olobuild_active_single_olo_spettacolo, Olobuild_Single_Integration), coi campi della sorgente
 *    dinamica «Spettacolo» (campo(): fulmine e segnaposto {spettacolo:…}).
 * Le demo dei temi non toccano il database: lì il Cartellone mostra le voci della tile e la pagina di uno
 * spettacolo si disegna con la voce del tema (imposta_demo()). Importando un tema, le voci dei suoi Cartelloni
 * diventano spettacoli (raccogli() + semina()), se il sito non ne ha già.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Spettacoli {

    const TIPO = 'olo_spettacolo';

    /** Prefisso dei campi: col trattino basso non compaiono fra i Campi personalizzati. */
    const META = '_olo_sp_';

    /** I campi di uno spettacolo, come le voci della tile Cartellone (titolo e descrizione stanno nel post). */
    const CAMPI = [ 'sottotitolo', 'genere', 'data', 'ora', 'data_fine', 'orari', 'luogo', 'durata', 'prezzo', 'stato', 'etichette', 'incontro', 'link_biglietti', 'link_info', 'immagine' ];

    /** Stati della disponibilità, gli stessi del Cartellone. */
    const STATI = [ 'ultimi', 'esaurito', 'annullato', 'gratuito', 'prenotazione', 'rinviato' ];

    /** Il menu «Spettacoli» compare dal primo spettacolo (o da quando un tema li ha portati) e poi resta. */
    const OPZIONE_MENU = 'olobuild_spettacoli_menu';

    /** Indirizzo con cui sono state scritte le regole di riscrittura: se cambia (filtro olobuild_spettacoli_slug), si riscrivono. */
    const OPZIONE_REGOLE = 'olobuild_spettacoli_regole';

    /** @var array|null La voce della demo in corso (la pagina di uno spettacolo nella demo di un tema). */
    private static $demo = null;

    /** @var array|null Gli spettacoli pubblicati, come voci del Cartellone, per questa richiesta. */
    private static $voci = null;

    public static function avvia() {
        add_action( 'init', [ __CLASS__, 'registra' ] );
        add_action( 'wp_loaded', [ __CLASS__, 'regole' ] );
        add_action( 'rest_api_init', [ __CLASS__, 'rotte' ] );
        add_action( 'save_post_' . self::TIPO, [ __CLASS__, 'salva' ], 10, 2 );
        if ( is_admin() ) {
            add_action( 'add_meta_boxes_' . self::TIPO, [ __CLASS__, 'riquadri' ] );
            add_filter( 'enter_title_here', [ __CLASS__, 'segnaposto_titolo' ], 10, 2 );
            add_filter( 'manage_' . self::TIPO . '_posts_columns', [ __CLASS__, 'colonne' ] );
            add_action( 'manage_' . self::TIPO . '_posts_custom_column', [ __CLASS__, 'cella' ], 10, 2 );
            add_filter( 'manage_edit-' . self::TIPO . '_sortable_columns', [ __CLASS__, 'ordinabili' ] );
            add_action( 'pre_get_posts', [ __CLASS__, 'ordine_elenco' ] );
            add_action( 'admin_notices', [ __CLASS__, 'avvisi' ] );
        }
    }

    /**
     * Il pezzo d'indirizzo delle pagine degli spettacoli (/spettacoli/<titolo>/). Fisso, non tradotto: cambiando lingua
     * al sito gli indirizzi già condivisi non cambiano. Si può cambiare col filtro olobuild_spettacoli_slug.
     */
    public static function base() {
        $b = sanitize_title( (string) apply_filters( 'olobuild_spettacoli_slug', 'spettacoli' ) );
        return '' !== $b ? $b : 'spettacoli';
    }

    public static function registra() {
        register_post_type( self::TIPO, [
            'labels'            => [
                'name'                  => __( 'Spettacoli', 'olobuilder' ),
                'singular_name'         => __( 'Spettacolo', 'olobuilder' ),
                'menu_name'             => __( 'Spettacoli', 'olobuilder' ),
                'all_items'             => __( 'Tutti gli spettacoli', 'olobuilder' ),
                'add_new'               => __( 'Aggiungi spettacolo', 'olobuilder' ),
                'add_new_item'          => __( 'Aggiungi uno spettacolo', 'olobuilder' ),
                'edit_item'             => __( 'Modifica lo spettacolo', 'olobuilder' ),
                'new_item'              => __( 'Nuovo spettacolo', 'olobuilder' ),
                'view_item'             => __( 'Vedi lo spettacolo', 'olobuilder' ),
                'view_items'            => __( 'Vedi gli spettacoli', 'olobuilder' ),
                'search_items'          => __( 'Cerca negli spettacoli', 'olobuilder' ),
                'not_found'             => __( 'Nessuno spettacolo.', 'olobuilder' ),
                'not_found_in_trash'    => __( 'Nessuno spettacolo nel cestino.', 'olobuilder' ),
                'featured_image'        => __( 'Foto dello spettacolo', 'olobuilder' ),
                'set_featured_image'    => __( 'Scegli la foto', 'olobuilder' ),
                'remove_featured_image' => __( 'Togli la foto', 'olobuilder' ),
                'use_featured_image'    => __( 'Usa come foto dello spettacolo', 'olobuilder' ),
                'item_published'        => __( 'Spettacolo pubblicato.', 'olobuilder' ),
                'item_updated'          => __( 'Spettacolo aggiornato.', 'olobuilder' ),
            ],
            'public'            => true,
            'show_ui'           => true,
            'show_in_menu'      => self::menu_visibile(),
            'show_in_nav_menus' => true,
            'menu_position'     => 21,
            'menu_icon'         => 'dashicons-tickets-alt',
            'supports'          => [ 'title', 'editor', 'thumbnail', 'revisions' ],
            'has_archive'       => false,
            'rewrite'           => [ 'slug' => self::base(), 'with_front' => false ],
            // Niente editor a blocchi: titolo, testo lungo, foto e la scheda dei dettagli, come una scheda di sala.
            'show_in_rest'      => false,
        ] );
    }

    /**
     * Le regole di riscrittura si riscrivono una volta sola, quando il tipo nasce o il suo indirizzo cambia. Su
     * wp_loaded, dopo che tutti i plugin hanno registrato i loro tipi: a init le loro pagine sparirebbero.
     */
    public static function regole() {
        $base = self::base();
        if ( get_option( self::OPZIONE_REGOLE ) !== $base ) {
            update_option( self::OPZIONE_REGOLE, $base, false );
            flush_rewrite_rules( false );
        }
    }

    /** Il menu si mostra quando il sito ha (o ha avuto) uno spettacolo: sui siti che non ne usano non ingombra. */
    private static function menu_visibile() {
        if ( get_option( self::OPZIONE_MENU ) ) {
            return true;
        }
        if ( ! is_admin() ) {
            return false;
        }
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- un SELECT con LIMIT 1 sull'indice type_status_date, solo in bacheca e finché il sito non ha spettacoli
        $c = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status NOT IN ('auto-draft','trash') LIMIT 1", self::TIPO ) );
        if ( $c ) {
            update_option( self::OPZIONE_MENU, 1 );
            return true;
        }
        return false;
    }

    /** Etichette della disponibilità, tradotte (le stesse del Cartellone). */
    public static function stati() {
        return [
            'ultimi'       => __( 'Ultimi posti', 'olobuilder' ),
            'esaurito'     => __( 'Esaurito', 'olobuilder' ),
            'annullato'    => __( 'Annullato', 'olobuilder' ),
            'gratuito'     => __( 'Ingresso libero', 'olobuilder' ),
            'prenotazione' => __( 'Su prenotazione', 'olobuilder' ),
            'rinviato'     => __( 'Rinviato', 'olobuilder' ),
        ];
    }

    /* ── Valori ── */

    /** I campi di uno spettacolo ('' se mancano). */
    public static function valori( $post_id ) {
        $out = [];
        foreach ( self::CAMPI as $k ) {
            $out[ $k ] = (string) get_post_meta( (int) $post_id, self::META . $k, true );
        }
        return $out;
    }

    private static function data_valida( $d ) {
        if ( ! preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', (string) $d, $m ) ) {
            return false;
        }
        return checkdate( (int) $m[2], (int) $m[3], (int) $m[1] );
    }

    /**
     * Campi puliti e controllati. La data di fine mai prima dell'inizio, né senza inizio. $risolvi (facoltativo)
     * trasforma i link prima di pulirli: all'import, i «{{PAGINA:chiave}}» del tema.
     */
    private static function pulisci( $v, $risolvi = null ) {
        $out = [];
        foreach ( self::CAMPI as $k ) {
            $x = ( isset( $v[ $k ] ) && is_scalar( $v[ $k ] ) ) ? trim( (string) $v[ $k ] ) : '';
            switch ( $k ) {
                case 'data':
                case 'data_fine':
                    $x = self::data_valida( $x ) ? $x : '';
                    break;
                case 'ora':
                    $x = preg_match( '/^([01]?\d|2[0-3]):[0-5]\d$/', $x ) ? $x : '';
                    break;
                case 'stato':
                    $x = in_array( $x, self::STATI, true ) ? $x : '';
                    break;
                case 'link_biglietti':
                case 'link_info':
                case 'immagine':
                    if ( is_callable( $risolvi ) ) {
                        $x = (string) call_user_func( $risolvi, $x );
                    }
                    $x = '' !== $x ? esc_url_raw( $x ) : '';
                    break;
                default:
                    $x = sanitize_text_field( $x );
            }
            $out[ $k ] = $x;
        }
        if ( '' !== $out['data_fine'] && ( '' === $out['data'] || $out['data_fine'] < $out['data'] ) ) {
            $out['data_fine'] = '';
        }
        return $out;
    }

    /** Uno spettacolo come voce del Cartellone (stesse chiavi), più il testo lungo per la sua pagina. */
    private static function voce( $p ) {
        $m   = self::valori( $p->ID );
        $img = get_the_post_thumbnail_url( $p, 'large' );
        return [
            'id'             => 'sp-' . $p->ID,
            'titolo'         => (string) $p->post_title,
            'sottotitolo'    => $m['sottotitolo'],
            'genere'         => $m['genere'],
            'data'           => $m['data'],
            'ora'            => $m['ora'],
            'data_fine'      => $m['data_fine'],
            'orari'          => $m['orari'],
            'luogo'          => $m['luogo'],
            'durata'         => $m['durata'],
            'prezzo'         => $m['prezzo'],
            'stato'          => $m['stato'],
            'etichette'      => $m['etichette'],
            'descrizione'    => (string) $p->post_excerpt,
            'incontro'       => $m['incontro'],
            'immagine'       => $img ? (string) $img : $m['immagine'],
            'link_biglietti' => $m['link_biglietti'],
            'link_info'      => '' !== $m['link_info'] ? $m['link_info'] : (string) get_permalink( $p ),
            'contenuto'      => (string) $p->post_content,
        ];
    }

    /** Gli spettacoli pubblicati, come voci del Cartellone (una sola lettura per richiesta, foto comprese). */
    public static function voci() {
        if ( null === self::$voci ) {
            self::$voci = [];
            $q = new WP_Query( [
                'post_type'      => self::TIPO,
                'post_status'    => 'publish',
                'posts_per_page' => 500,
                'orderby'        => 'date',
                'order'          => 'ASC',
                'no_found_rows'  => true,
            ] );
            update_post_thumbnail_cache( $q );
            foreach ( $q->posts as $p ) {
                self::$voci[] = self::voce( $p );
            }
        }
        return self::$voci;
    }

    /** La richiesta è la demo di un tema? (lì niente database: valgono le voci del tema) */
    public static function in_demo() {
        return class_exists( 'Olobuild_Demo_Temi' ) && Olobuild_Demo_Temi::in_corso();
    }

    /** La demo mostra la pagina di questo spettacolo: i campi dinamici leggono la voce del tema. */
    public static function imposta_demo( $voce ) {
        self::$demo = is_array( $voce ) ? $voce : null;
    }

    /** Id (come voce) dello spettacolo di cui si sta mostrando la pagina, '' altrove. */
    private static function corrente() {
        if ( null !== self::$demo ) {
            return (string) ( self::$demo['id'] ?? '' );
        }
        if ( did_action( 'wp' ) && is_singular( self::TIPO ) ) {
            return 'sp-' . (int) get_queried_object_id();
        }
        return '';
    }

    /**
     * Le voci di un Cartellone con «Spettacoli del sito»: gli spettacoli pubblicati; nelle demo dei temi le voci
     * della tile, ognuna con la pagina dello spettacolo nella demo. Sulla pagina di uno spettacolo, senza lui.
     */
    public static function voci_per_cartellone( $voci_tile ) {
        if ( self::in_demo() ) {
            $voci = [];
            foreach ( (array) $voci_tile as $v ) {
                if ( ! is_array( $v ) ) {
                    continue;
                }
                if ( '' === trim( (string) ( $v['link_info'] ?? '' ) ) ) {
                    $ind = Olobuild_Demo_Temi::indirizzo_spettacolo( (string) ( $v['titolo'] ?? '' ) );
                    if ( '' !== $ind ) {
                        $v['link_info'] = $ind;
                    }
                }
                $voci[] = $v;
            }
        } else {
            $voci = self::voci();
        }
        $corrente = self::corrente();
        if ( '' !== $corrente ) {
            $voci = array_values( array_filter( $voci, static function ( $v ) use ( $corrente ) {
                return (string) ( $v['id'] ?? '' ) !== $corrente;
            } ) );
        }
        return $voci;
    }

    /* ── Campi dinamici (sorgente «Spettacolo»: fulmine e segnaposto {spettacolo:campo}) ── */

    /** I campi offerti dal fulmine. Nomi in italiano come le altre sorgenti: li traduce l'inspector. */
    public static function campi_dinamici() {
        return [
            [ 'key' => 'titolo',         'label' => 'Titolo',                        'type' => 'text' ],
            [ 'key' => 'sottotitolo',    'label' => 'Autore, compagnia, interpreti', 'type' => 'text' ],
            [ 'key' => 'genere',         'label' => 'Genere',                        'type' => 'text' ],
            [ 'key' => 'occhiello',      'label' => 'Genere e date',                 'type' => 'text' ],
            [ 'key' => 'periodo',        'label' => 'Date',                          'type' => 'text' ],
            [ 'key' => 'quando',         'label' => 'Date e orari',                  'type' => 'text' ],
            [ 'key' => 'data_lunga',     'label' => 'Data a parole',                 'type' => 'text' ],
            [ 'key' => 'data_breve',     'label' => 'Data breve',                    'type' => 'text' ],
            [ 'key' => 'ora',            'label' => 'Ora',                           'type' => 'text' ],
            [ 'key' => 'orari',          'label' => 'Orari',                         'type' => 'text' ],
            [ 'key' => 'luogo',          'label' => 'Luogo',                         'type' => 'text' ],
            [ 'key' => 'durata',         'label' => 'Durata',                        'type' => 'text' ],
            [ 'key' => 'prezzo',         'label' => 'Prezzo',                        'type' => 'text' ],
            [ 'key' => 'stato',          'label' => 'Disponibilità',                 'type' => 'text' ],
            [ 'key' => 'ingresso',       'label' => 'Prezzo e disponibilità',        'type' => 'text' ],
            [ 'key' => 'etichette',      'label' => 'Etichette',                     'type' => 'text' ],
            [ 'key' => 'incontro',       'label' => 'Incontro col pubblico',         'type' => 'text' ],
            [ 'key' => 'descrizione',    'label' => 'Descrizione breve',             'type' => 'text' ],
            [ 'key' => 'contenuto',      'label' => 'Testo lungo',                   'type' => 'html' ],
            [ 'key' => 'immagine',       'label' => 'Foto dello spettacolo',         'type' => 'image' ],
            [ 'key' => 'link_biglietti', 'label' => 'Link dei biglietti',            'type' => 'url' ],
            [ 'key' => 'cta_biglietti',  'label' => 'Testo del pulsante dei biglietti', 'type' => 'text' ],
            [ 'key' => 'link_info',      'label' => 'Link della scheda',             'type' => 'url' ],
        ];
    }

    /** Timestamp di data + ora nel fuso del sito (null se la data non è valida), come nel Cartellone. */
    private static function istante( $data, $ora ) {
        if ( ! self::data_valida( $data ) ) {
            return null;
        }
        $ora = trim( (string) $ora );
        if ( ! preg_match( '/^\d{1,2}:\d{2}$/', $ora ) ) {
            $ora = '00:00';
        }
        $d = DateTime::createFromFormat( 'Y-m-d H:i', $data . ' ' . $ora, wp_timezone() );
        return $d ? $d->getTimestamp() : null;
    }

    /** Data a parole nella lingua del sito, minuscola dove mesi e giorni si scrivono minuscoli (come nel Cartellone). */
    private static function a_parole( $formato, $ts ) {
        $t   = wp_date( $formato, $ts );
        $loc = strtolower( substr( (string) determine_locale(), 0, 2 ) );
        if ( in_array( $loc, [ 'it', 'es', 'fr', 'pt', 'ca', 'ro', 'nl', 'pl', 'cs', 'sk', 'sv', 'da', 'nb', 'nn', 'fi', 'hu', 'hr', 'sl', 'gl', 'eu' ], true ) ) {
            $t = function_exists( 'mb_strtolower' ) ? mb_strtolower( $t, 'UTF-8' ) : strtolower( $t );
        }
        return $t;
    }

    /** «sabato 24 ottobre 2026», o «dal 24 ottobre al 3 novembre 2026» per chi resta in scena più giorni. */
    private static function periodo( $v ) {
        $ts = self::istante( $v['data'] ?? '', $v['ora'] ?? '' );
        if ( null === $ts ) {
            return '';
        }
        $fine = self::istante( $v['data_fine'] ?? '', '12:00' );
        if ( null !== $fine && $fine > $ts ) {
            // stesso mese: «dal 24 al 25 ottobre 2026»; stesso anno: «dal 24 ottobre al 3 novembre 2026»
            $da = wp_date( 'Y-m', $ts ) === wp_date( 'Y-m', $fine ) ? 'j' : ( wp_date( 'Y', $ts ) === wp_date( 'Y', $fine ) ? 'j F' : 'j F Y' );
            /* translators: 1: primo giorno, 2: ultimo giorno (anno compreso) */
            return sprintf( __( 'dal %1$s al %2$s', 'olobuilder' ), self::a_parole( $da, $ts ), self::a_parole( 'j F Y', $fine ) );
        }
        return self::a_parole( 'l j F Y', $ts );
    }

    private static function unisci( $parti ) {
        return implode( ' · ', array_filter( array_map( 'trim', array_map( 'strval', $parti ) ), 'strlen' ) );
    }

    /**
     * Valore di un campo dello spettacolo: quello della pagina in corso (o di $post_id), o della voce della demo.
     * '' se la pagina non è uno spettacolo o il campo non c'è.
     */
    public static function campo( $campo, $post_id = 0 ) {
        $v = self::$demo;
        if ( null === $v ) {
            $post_id = $post_id ? (int) $post_id : (int) get_the_ID();
            $p       = $post_id ? get_post( $post_id ) : null;
            if ( ! $p || self::TIPO !== $p->post_type ) {
                return '';
            }
            $v = self::voce( $p );
        }
        $stati = self::stati();
        $stato = (string) ( $v['stato'] ?? '' );
        $ts    = self::istante( $v['data'] ?? '', $v['ora'] ?? '' );
        switch ( (string) $campo ) {
            case 'periodo':
                return self::periodo( $v );
            case 'quando':
                $orari = trim( (string) ( $v['orari'] ?? '' ) );
                return self::unisci( [ self::periodo( $v ), '' !== $orari ? $orari : (string) ( $v['ora'] ?? '' ) ] );
            case 'occhiello':
                return self::unisci( [ (string) ( $v['genere'] ?? '' ), self::periodo( $v ) ] );
            case 'data_lunga':
                return null !== $ts ? self::a_parole( 'l j F Y', $ts ) : '';
            case 'data_breve':
                return null !== $ts ? self::a_parole( 'j M', $ts ) : '';
            case 'stato':
                return $stati[ $stato ] ?? '';
            case 'ingresso':
                return self::unisci( [ (string) ( $v['prezzo'] ?? '' ), $stati[ $stato ] ?? '' ] );
            case 'etichette':
                return self::unisci( explode( ',', (string) ( $v['etichette'] ?? '' ) ) );
            case 'cta_biglietti':
                // Senza link, o a sala esaurita o con lo spettacolo annullato, il pulsante non c'è.
                return ( '' !== trim( (string) ( $v['link_biglietti'] ?? '' ) ) && ! in_array( $stato, [ 'esaurito', 'annullato' ], true ) )
                    ? __( 'Biglietti', 'olobuilder' ) : '';
            case 'contenuto':
                $c = trim( (string) ( $v['contenuto'] ?? '' ) );
                return '' !== $c ? wpautop( $c ) : '';
            case 'titolo':
            case 'sottotitolo':
            case 'genere':
            case 'ora':
            case 'orari':
            case 'luogo':
            case 'durata':
            case 'prezzo':
            case 'incontro':
            case 'descrizione':
            case 'immagine':
            case 'link_biglietti':
            case 'link_info':
            case 'data':
            case 'data_fine':
                return (string) ( $v[ $campo ] ?? '' );
            default:
                return '';
        }
    }

    /* ── Dai temi e dalle voci del Cartellone ── */

    /**
     * Le voci dei Cartelloni con «Spettacoli del sito» in un template (una volta ciascuna), tolte dalla tile:
     * importato il tema, gli appuntamenti stanno negli spettacoli e la tile li legge da lì.
     */
    public static function raccogli( &$nodi ) {
        $voci = [];
        $giro = static function ( &$lista ) use ( &$giro, &$voci ) {
            if ( ! is_array( $lista ) ) {
                return;
            }
            foreach ( $lista as &$n ) {
                if ( ! is_array( $n ) ) {
                    continue;
                }
                if ( 'cartellone' === ( $n['type'] ?? '' ) && 'spettacoli' === ( $n['settings']['fonte'] ?? '' ) && ! empty( $n['settings']['voci'] ) && is_array( $n['settings']['voci'] ) ) {
                    foreach ( $n['settings']['voci'] as $v ) {
                        if ( is_array( $v ) ) {
                            $chiave = (string) ( $v['id'] ?? md5( (string) wp_json_encode( $v ) ) );
                            if ( ! isset( $voci[ $chiave ] ) ) {
                                $voci[ $chiave ] = $v;
                            }
                        }
                    }
                    $n['settings']['voci'] = [];
                }
                if ( ! empty( $n['children'] ) ) {
                    $giro( $n['children'] );
                }
            }
            unset( $n );
        };
        $giro( $nodi );
        return array_values( $voci );
    }

    /** All'import di un tema: le sue voci diventano spettacoli, se il sito non ne ha ancora nessuno. */
    public static function semina( $voci, $risolvi = null ) {
        if ( empty( $voci ) ) {
            return [ 'creati' => 0, 'gia' => 0 ];
        }
        $ci_sono = get_posts( [ 'post_type' => self::TIPO, 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids', 'suppress_filters' => true ] );
        if ( $ci_sono ) {
            return [ 'creati' => 0, 'gia' => count( $voci ) ];
        }
        return self::crea( $voci, $risolvi );
    }

    /**
     * Uno spettacolo pubblicato per voce. Una voce già trasformata (stesso id, nel campo «seme») si salta: il
     * pulsante del Cartellone si può premere due volte senza doppioni. La foto, se è nella Libreria media, diventa
     * l'immagine in evidenza.
     */
    public static function crea( $voci, $risolvi = null ) {
        $creati = 0;
        $gia    = 0;
        foreach ( (array) $voci as $v ) {
            if ( ! is_array( $v ) ) {
                continue;
            }
            $titolo = sanitize_text_field( (string) ( $v['titolo'] ?? '' ) );
            if ( '' === $titolo ) {
                continue;
            }
            $seme = sanitize_key( (string) ( $v['id'] ?? '' ) );
            if ( '' !== $seme ) {
                $esiste = get_posts( [ 'post_type' => self::TIPO, 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids', 'suppress_filters' => true,
                    'meta_key' => self::META . 'seme', 'meta_value' => $seme ] ); // phpcs:ignore WordPress.DB.SlowDBQuery -- una volta per voce, solo quando si trasformano le voci
                if ( $esiste ) {
                    $gia++;
                    continue;
                }
            }
            $id = wp_insert_post( [
                'post_type'    => self::TIPO,
                'post_status'  => 'publish',
                'post_title'   => $titolo,
                'post_excerpt' => sanitize_textarea_field( (string) ( $v['descrizione'] ?? '' ) ),
                'post_content' => '',
            ], true );
            if ( is_wp_error( $id ) || ! $id ) {
                continue;
            }
            $puliti = self::pulisci( $v, $risolvi );
            foreach ( $puliti as $k => $val ) {
                if ( '' !== $val && 'immagine' !== $k ) {
                    update_post_meta( $id, self::META . $k, $val );
                }
            }
            if ( '' !== $puliti['immagine'] ) {
                $foto = attachment_url_to_postid( $puliti['immagine'] );
                if ( $foto ) {
                    set_post_thumbnail( $id, $foto );
                } else {
                    update_post_meta( $id, self::META . 'immagine', $puliti['immagine'] );
                }
            }
            if ( '' !== $seme ) {
                update_post_meta( $id, self::META . 'seme', $seme );
            }
            $creati++;
        }
        if ( $creati ) {
            update_option( self::OPZIONE_MENU, 1 );
            self::$voci = null;
        }
        return [ 'creati' => $creati, 'gia' => $gia ];
    }

    /* ── REST: il pannello «Spettacoli del sito» della tile Cartellone ── */

    public static function rotte() {
        register_rest_route( 'olobuild/v1', '/spettacoli', [
            'methods'             => 'GET',
            'callback'            => [ __CLASS__, 'rest_stato' ],
            'permission_callback' => static function () {
                return current_user_can( 'edit_posts' );
            },
        ] );
        register_rest_route( 'olobuild/v1', '/spettacoli/da-voci', [
            'methods'             => 'POST',
            'callback'            => [ __CLASS__, 'rest_da_voci' ],
            'permission_callback' => static function () {
                return current_user_can( 'publish_posts' );
            },
        ] );
    }

    public static function rest_stato() {
        $n = wp_count_posts( self::TIPO );
        return rest_ensure_response( [
            'pubblicati' => (int) ( $n->publish ?? 0 ),
            'altri'      => (int) ( $n->draft ?? 0 ) + (int) ( $n->pending ?? 0 ) + (int) ( $n->future ?? 0 ) + (int) ( $n->private ?? 0 ),
            'elenco'     => admin_url( 'edit.php?post_type=' . self::TIPO ),
            'nuovo'      => admin_url( 'post-new.php?post_type=' . self::TIPO ),
        ] );
    }

    public static function rest_da_voci( WP_REST_Request $r ) {
        $voci = $r->get_param( 'voci' );
        if ( ! is_array( $voci ) || ! $voci ) {
            return new WP_Error( 'olobuild_voci', __( 'Nessuna voce da trasformare.', 'olobuilder' ), [ 'status' => 400 ] );
        }
        $esito = self::crea( array_slice( array_values( array_filter( $voci, 'is_array' ) ), 0, 200 ) );
        $n     = wp_count_posts( self::TIPO );
        return rest_ensure_response( $esito + [ 'pubblicati' => (int) ( $n->publish ?? 0 ) ] );
    }

    /* ── Bacheca: scheda dello spettacolo, salvataggio, elenco ── */

    public static function segnaposto_titolo( $testo, $post ) {
        return ( $post && self::TIPO === $post->post_type ) ? __( 'Titolo dello spettacolo', 'olobuilder' ) : $testo;
    }

    public static function riquadri() {
        add_meta_box( 'olobuild-spettacolo', __( 'Lo spettacolo', 'olobuilder' ), [ __CLASS__, 'riquadro' ], self::TIPO, 'normal', 'high' );
    }

    /** Generi già usati dagli spettacoli e quelli di un cartellone tipo, per i suggerimenti del campo Genere. */
    private static function generi_suggeriti() {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- solo nella scheda di modifica: i valori distinti di un campo
        $usati = (array) $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value <> '' LIMIT 60", self::META . 'genere' ) );
        $tipo  = [ __( 'Prosa', 'olobuilder' ), __( 'Lirica', 'olobuilder' ), __( 'Danza', 'olobuilder' ), __( 'Musica', 'olobuilder' ), __( 'Ragazzi', 'olobuilder' ), __( 'Incontri', 'olobuilder' ) ];
        return array_values( array_unique( array_merge( $usati, $tipo ) ) );
    }

    public static function riquadro( $post ) {
        $v = self::valori( $post->ID );
        wp_nonce_field( 'olobuild_spettacolo', 'olobuild_spettacolo_nonce' );
        $campo = static function ( $k, $etichetta, $classe, $tipo = 'text', $segnaposto = '', $extra = '' ) use ( $v ) {
            printf(
                '<p class="%1$s"><label for="olo-sp-%2$s">%3$s</label><input type="%4$s" id="olo-sp-%2$s" name="olo_sp[%2$s]" value="%5$s" placeholder="%6$s"%7$s></p>',
                esc_attr( $classe ),
                esc_attr( $k ),
                esc_html( $etichetta ),
                esc_attr( $tipo ),
                esc_attr( $v[ $k ] ),
                esc_attr( $segnaposto ),
                $extra // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributi fissi scritti qui sotto
            );
        };
        echo '<style>.olo-sp{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:4px 16px}.olo-sp p{margin:8px 0}.olo-sp label{display:block;font-weight:600;margin:0 0 4px}'
            . '.olo-sp input,.olo-sp select,.olo-sp textarea{width:100%;max-width:none}.olo-sp .c6{grid-column:span 6}.olo-sp .c3{grid-column:span 3}.olo-sp .c2{grid-column:span 2}'
            . '@media (max-width:782px){.olo-sp .c3,.olo-sp .c2{grid-column:span 6}}</style>';
        echo '<div class="olo-sp">';
        $campo( 'sottotitolo', __( 'Autore, compagnia, interpreti', 'olobuilder' ), 'c6', 'text', __( 'di Carlo Goldoni · Compagnia dell\'Arco', 'olobuilder' ) );
        $campo( 'genere', __( 'Genere', 'olobuilder' ), 'c3', 'text', __( 'Prosa', 'olobuilder' ), ' list="olo-sp-generi" autocomplete="off"' );
        $campo( 'luogo', __( 'Luogo', 'olobuilder' ), 'c3', 'text', __( 'Sala grande', 'olobuilder' ) );
        $campo( 'data', __( 'Data', 'olobuilder' ), 'c2', 'date' );
        $campo( 'ora', __( 'Ora', 'olobuilder' ), 'c2', 'time' );
        $campo( 'data_fine', __( 'Fino al', 'olobuilder' ), 'c2', 'date', '', '' !== $v['data'] ? ' min="' . esc_attr( $v['data'] ) . '"' : '' );
        $campo( 'orari', __( 'Orari', 'olobuilder' ), 'c6', 'text', __( 'ven e sab 20:30 · dom 16:00', 'olobuilder' ) );
        $campo( 'durata', __( 'Durata', 'olobuilder' ), 'c2', 'text', __( '2 h 10 min con intervallo', 'olobuilder' ) );
        $campo( 'prezzo', __( 'Prezzo', 'olobuilder' ), 'c2', 'text', __( 'da 14 €', 'olobuilder' ) );
        echo '<p class="c2"><label for="olo-sp-stato">' . esc_html__( 'Disponibilità', 'olobuilder' ) . '</label><select id="olo-sp-stato" name="olo_sp[stato]">';
        echo '<option value="">' . esc_html__( 'Disponibile', 'olobuilder' ) . '</option>';
        foreach ( self::stati() as $k => $etichetta ) {
            echo '<option value="' . esc_attr( $k ) . '"' . selected( $v['stato'], $k, false ) . '>' . esc_html( $etichetta ) . '</option>';
        }
        echo '</select></p>';
        $campo( 'etichette', __( 'Etichette', 'olobuilder' ), 'c6', 'text', __( 'Prima nazionale, Sopratitoli, In LIS', 'olobuilder' ) );
        $campo( 'incontro', __( 'Incontro col pubblico', 'olobuilder' ), 'c6', 'text', __( 'Alle 19:30 incontro con la compagnia nel Ridotto', 'olobuilder' ) );
        // La descrizione breve è l'estratto del post: la salva WordPress (campo «excerpt»).
        echo '<p class="c6"><label for="excerpt">' . esc_html__( 'Descrizione breve', 'olobuilder' ) . '</label><textarea id="excerpt" name="excerpt" rows="3" placeholder="'
            . esc_attr__( 'Due righe per il cartellone. Il testo lungo va nell\'editor qui sopra.', 'olobuilder' ) . '">' . esc_textarea( (string) $post->post_excerpt ) . '</textarea></p>';
        $campo( 'link_biglietti', __( 'Link dei biglietti', 'olobuilder' ), 'c3', 'url', 'https://' );
        $campo( 'link_info', __( 'Link della scheda', 'olobuilder' ), 'c3', 'url', __( 'Vuoto: la pagina dello spettacolo', 'olobuilder' ) );
        echo '</div><datalist id="olo-sp-generi">';
        foreach ( self::generi_suggeriti() as $g ) {
            echo '<option value="' . esc_attr( $g ) . '"></option>';
        }
        echo '</datalist>';
        // La data di fine non può venire prima dell'inizio (il controllo vero è al salvataggio). Niente && (wptexturize).
        echo '<script>(function(){var d=document.getElementById("olo-sp-data"),f=document.getElementById("olo-sp-data_fine");if(!d||!f){return;}'
            . 'var lega=function(){f.min=d.value||"";if(f.value){if(d.value){if(f.value<d.value){f.value="";}}}};d.addEventListener("change",lega);lega();})();</script>';
    }

    public static function salva( $post_id, $post ) {
        $nonce = isset( $_POST['olobuild_spettacolo_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['olobuild_spettacolo_nonce'] ) ) : '';
        if ( '' === $nonce || ! wp_verify_nonce( $nonce, 'olobuild_spettacolo' ) ) {
            return;
        }
        if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        $grezzi = [];
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- ogni campo passa da pulisci() qui sotto
        $in = ( isset( $_POST['olo_sp'] ) && is_array( $_POST['olo_sp'] ) ) ? wp_unslash( $_POST['olo_sp'] ) : [];
        foreach ( self::CAMPI as $k ) {
            if ( 'immagine' !== $k && isset( $in[ $k ] ) && is_scalar( $in[ $k ] ) ) {
                $grezzi[ $k ] = (string) $in[ $k ];
            }
        }
        $puliti = self::pulisci( $grezzi );
        if ( '' !== trim( (string) ( $grezzi['data_fine'] ?? '' ) ) && '' === $puliti['data_fine'] ) {
            set_transient( 'olobuild_sp_fine_' . get_current_user_id(), 1, 120 );
        }
        foreach ( $puliti as $k => $val ) {
            if ( 'immagine' === $k ) {
                continue;
            }
            if ( '' === $val ) {
                delete_post_meta( $post_id, self::META . $k );
            } else {
                update_post_meta( $post_id, self::META . $k, $val );
            }
        }
        update_option( self::OPZIONE_MENU, 1 );
    }

    public static function avvisi() {
        $schermo = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
        if ( ! $schermo || self::TIPO !== $schermo->post_type ) {
            return;
        }
        $chiave = 'olobuild_sp_fine_' . get_current_user_id();
        if ( get_transient( $chiave ) ) {
            delete_transient( $chiave );
            echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__( 'La data di fine veniva prima dell\'inizio: l\'abbiamo tolta. Controlla le date dello spettacolo.', 'olobuilder' ) . '</p></div>';
        }
    }

    public static function colonne( $c ) {
        $nuove = [];
        foreach ( $c as $k => $etichetta ) {
            if ( 'date' === $k ) {
                continue; // la data di pubblicazione: qui conta quella dello spettacolo
            }
            $nuove[ $k ] = $etichetta;
            if ( 'title' === $k ) {
                $nuove['olo_data']   = __( 'Data', 'olobuilder' );
                $nuove['olo_genere'] = __( 'Genere', 'olobuilder' );
                $nuove['olo_luogo']  = __( 'Luogo', 'olobuilder' );
                $nuove['olo_stato']  = __( 'Disponibilità', 'olobuilder' );
            }
        }
        return $nuove;
    }

    public static function cella( $colonna, $post_id ) {
        $m = self::valori( $post_id );
        switch ( $colonna ) {
            case 'olo_data':
                $ts = self::istante( $m['data'], $m['ora'] );
                if ( null === $ts ) {
                    echo '—';
                    break;
                }
                echo esc_html( self::a_parole( 'D j M Y', $ts ) . ( '' !== $m['ora'] ? ' · ' . $m['ora'] : '' ) );
                $fine = self::istante( $m['data_fine'], '12:00' );
                if ( null !== $fine && $fine > $ts ) {
                    /* translators: %s: ultimo giorno in scena */
                    echo '<br><span class="description">' . esc_html( sprintf( __( 'fino al %s', 'olobuilder' ), self::a_parole( 'j M Y', $fine ) ) ) . '</span>';
                }
                break;
            case 'olo_genere':
                echo esc_html( '' !== $m['genere'] ? $m['genere'] : '—' );
                break;
            case 'olo_luogo':
                echo esc_html( '' !== $m['luogo'] ? $m['luogo'] : '—' );
                break;
            case 'olo_stato':
                $stati = self::stati();
                echo esc_html( $stati[ $m['stato'] ] ?? __( 'Disponibile', 'olobuilder' ) );
                break;
        }
    }

    public static function ordinabili( $c ) {
        $c['olo_data'] = 'olo_data';
        return $c;
    }

    /** L'elenco della bacheca in ordine di data dello spettacolo; quelli senza data restano visibili (in cima). */
    public static function ordine_elenco( $q ) {
        if ( ! is_admin() || ! $q->is_main_query() || self::TIPO !== $q->get( 'post_type' ) ) {
            return;
        }
        $ord = $q->get( 'orderby' );
        if ( '' !== $ord && 'olo_data' !== $ord ) {
            return;
        }
        $verso = 'DESC' === strtoupper( (string) $q->get( 'order' ) ) ? 'DESC' : 'ASC';
        $q->set( 'meta_query', [
            'relation' => 'OR',
            'olo_data' => [ 'key' => self::META . 'data', 'compare' => 'EXISTS' ],
            [ 'key' => self::META . 'data', 'compare' => 'NOT EXISTS' ],
        ] );
        $q->set( 'orderby', [ 'olo_data' => $verso, 'title' => 'ASC' ] );
    }
}
