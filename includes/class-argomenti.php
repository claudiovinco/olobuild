<?php
/**
 * Olobuild_Argomenti — i contenuti di un tipo con «Argomenti e sottoargomenti» (Impostazioni › Tipi di contenuto), e le
 * Pagine: un contenuto sta sotto un altro, come i capitoli di un manuale. Qui l'ordine di lettura dell'albero, che
 * leggono la Redazione (elenco e «Sta sotto»), la tile «Indice argomenti», «Navigazione articoli» (precedente e
 * successivo nell'ordine dell'indice) e la Guida della bacheca (rotta pubblica olobuild/v1/argomenti).
 *
 * Ordine: prima i numeri d'ordine più bassi (menu_order), a parità il titolo; i figli subito dopo il loro genitore.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Argomenti {

    const NS = 'olobuild/v1';

    /** Quanti contenuti legge al massimo un albero (un manuale intero ci sta largo). */
    const MASSIMO = 2000;

    public static function avvia() {
        add_action( 'rest_api_init', [ __CLASS__, 'rotte' ] );
        add_filter( 'request', [ __CLASS__, 'pagina_con_lo_stesso_indirizzo' ] );
        add_filter( 'private_title_format', [ __CLASS__, 'titolo_senza_privato' ], 10, 2 );
    }

    /**
     * Gli argomenti privati senza «Privato:» davanti al titolo: un manuale si prepara privato e si rilegge così come
     * uscirà (titolo, briciole, precedente e successivo). Lo stato si vede nella Redazione.
     */
    public static function titolo_senza_privato( $formato, $post = null ) {
        $post = get_post( $post );
        return ( $post && 'page' !== $post->post_type && self::ha_argomenti( $post->post_type ) ) ? '%s' : $formato;
    }

    /**
     * Un manuale può vivere sotto una pagina con lo stesso indirizzo: /guida/ è una pagina (la porta d'ingresso), e
     * /guida/olobuild/… sono gli argomenti. Le regole del tipo prenderebbero anche le altre sottopagine di quella pagina
     * (/guida/primi-passi/): se l'indirizzo non è un argomento ma è una pagina, si apre la pagina.
     */
    public static function pagina_con_lo_stesso_indirizzo( $q ) {
        $tipo = isset( $q['post_type'] ) && is_string( $q['post_type'] ) ? $q['post_type'] : '';
        if ( is_admin() || '' === $tipo || 'page' === $tipo || empty( $q['name'] ) || ! self::ha_argomenti( $tipo ) ) {
            return $q;
        }
        if ( get_page_by_path( $q['name'], OBJECT, $tipo ) ) {
            return $q;
        }
        $pto  = get_post_type_object( $tipo );
        $base = is_array( $pto->rewrite ) && ! empty( $pto->rewrite['slug'] ) ? trim( $pto->rewrite['slug'], '/' ) : '';
        if ( '' === $base || ! get_page_by_path( $base . '/' . $q['name'] ) ) {
            return $q;
        }
        $pagina = [ 'pagename' => $base . '/' . $q['name'] ];
        foreach ( [ 'page', 'paged', 'preview', 'olo_lang' ] as $k ) {
            if ( isset( $q[ $k ] ) ) {
                $pagina[ $k ] = $q[ $k ];
            }
        }
        return $pagina;
    }

    /** La porta d'ingresso di un manuale: la pagina con l'indirizzo del tipo, o la sua pagina «tutti i contenuti». */
    public static function casa( $tipo ) {
        $pto  = get_post_type_object( $tipo );
        $base = $pto && is_array( $pto->rewrite ) && ! empty( $pto->rewrite['slug'] ) ? trim( $pto->rewrite['slug'], '/' ) : '';
        $pag  = '' !== $base ? get_page_by_path( $base ) : null;
        if ( $pag && 'publish' === $pag->post_status ) {
            return (string) get_permalink( $pag );
        }
        $arch = get_post_type_archive_link( $tipo );
        return $arch ? (string) $arch : home_url( '/' );
    }

    /** Il tipo ha argomenti e sottoargomenti (anche le Pagine). */
    public static function ha_argomenti( $tipo ) {
        return '' !== (string) $tipo && post_type_exists( $tipo ) && is_post_type_hierarchical( $tipo );
    }

    /**
     * I contenuti di un tipo in ordine di lettura: [ [ 'id', 'genitore', 'titolo', 'livello', 'figli', 'post' ], … ].
     * Un contenuto il cui genitore non è fra quelli letti (in bozza, nel cestino) sale in cima.
     *
     * @param string $tipo Tipo di contenuto.
     * @param array  $args Argomenti in più per WP_Query (stati, lingue…).
     */
    public static function albero( $tipo, $args = [] ) {
        if ( ! self::ha_argomenti( $tipo ) ) {
            return [];
        }
        // Senza stati scelti vale la regola di WordPress: i pubblicati, più i privati per chi può leggerli (così chi
        // prepara un manuale lo vede intero sul sito prima di pubblicarlo; visitatori e rotta pubblica, solo i pubblicati).
        $q = new WP_Query( array_merge( [
            'post_type'              => $tipo,
            'posts_per_page'         => self::MASSIMO,
            'orderby'                => [ 'menu_order' => 'ASC', 'title' => 'ASC' ],
            'no_found_rows'          => true,
            'ignore_sticky_posts'    => true,
            'update_post_term_cache' => false,
        ], $args ) );
        $presenti = [];
        foreach ( $q->posts as $p ) {
            $presenti[ $p->ID ] = true;
        }
        $figli = [];
        foreach ( $q->posts as $p ) {
            $g             = isset( $presenti[ $p->post_parent ] ) ? (int) $p->post_parent : 0;
            $figli[ $g ][] = $p;
        }
        $out  = [];
        $giu  = function ( $g, $livello ) use ( &$giu, &$out, $figli ) {
            foreach ( $figli[ $g ] ?? [] as $p ) {
                $out[] = [
                    'id'       => (int) $p->ID,
                    'genitore' => $g,
                    // Il titolo senza «Privato:» (un manuale in preparazione si guarda così com'è), coi filtri del titolo.
                    'titolo'   => html_entity_decode( apply_filters( 'the_title', $p->post_title, $p->ID ), ENT_QUOTES, 'UTF-8' ),
                    'livello'  => $livello,
                    'figli'    => count( $figli[ $p->ID ] ?? [] ),
                    'post'     => $p,
                ];
                $giu( $p->ID, $livello + 1 );
            }
        };
        $giu( 0, 0 );
        return $out;
    }

    /** Gli id di un contenuto e di tutti quelli sotto di lui, nell'albero dato. */
    public static function ramo( $albero, $id ) {
        $dentro = [ (int) $id => true ];
        foreach ( $albero as $v ) {
            if ( isset( $dentro[ $v['genitore'] ] ) ) {
                $dentro[ $v['id'] ] = true;
            }
        }
        return array_keys( $dentro );
    }

    /** Gli antenati di un contenuto, dal più alto al genitore (id). */
    public static function antenati( $post ) {
        return array_reverse( array_map( 'intval', get_post_ancestors( $post ) ) );
    }

    /**
     * Il contenuto prima e quello dopo nell'ordine dell'indice: [ WP_Post|null, WP_Post|null ].
     *
     * @param WP_Post $post Il contenuto aperto.
     */
    public static function vicini( $post ) {
        $albero = self::albero( $post->post_type );
        foreach ( $albero as $i => $v ) {
            if ( $v['id'] === (int) $post->ID ) {
                return [
                    $i > 0 ? $albero[ $i - 1 ]['post'] : null,
                    isset( $albero[ $i + 1 ] ) ? $albero[ $i + 1 ]['post'] : null,
                ];
            }
        }
        return [ null, null ];
    }

    /* ─────────────────────────────────────────────
     * Rotta pubblica: l'indice e gli argomenti, per la Guida della bacheca di ogni sito OLObuild
     * ───────────────────────────────────────────── */

    public static function rotte() {
        register_rest_route( self::NS, '/argomenti/(?P<tipo>[a-z0-9_-]+)', [
            'methods'             => 'GET',
            'callback'            => [ __CLASS__, 'rest_indice' ],
            'permission_callback' => [ __CLASS__, 'pubblico' ],
        ] );
        register_rest_route( self::NS, '/argomenti/(?P<tipo>[a-z0-9_-]+)/(?P<id>\d+)', [
            'methods'             => 'GET',
            'callback'            => [ __CLASS__, 'rest_argomento' ],
            'permission_callback' => [ __CLASS__, 'pubblico' ],
        ] );
    }

    /** Solo i tipi con argomenti che hanno una pagina pubblica (le Pagine no: non sono un manuale). */
    public static function pubblico( WP_REST_Request $r ) {
        $tipo = sanitize_key( (string) $r['tipo'] );
        $pto  = get_post_type_object( $tipo );
        return 'page' !== $tipo && $pto && $pto->public && $pto->publicly_queryable && self::ha_argomenti( $tipo );
    }

    /** Il titolo di un contenuto senza «Privato:» davanti, coi filtri del titolo. */
    public static function titolo( $p ) {
        $p = get_post( $p );
        return $p ? html_entity_decode( apply_filters( 'the_title', $p->post_title, $p->ID ), ENT_QUOTES, 'UTF-8' ) : '';
    }

    /**
     * L'indice di un tipo: [ tipo, nome, sito, voci: [ id, genitore, titolo, livello, figli, link ] ]. Lo leggono la rotta
     * pubblica (solo i pubblicati) e la Guida del sito che scrive il manuale (anche i privati, per chi li può leggere).
     */
    public static function indice_dati( $tipo ) {
        $pto = get_post_type_object( $tipo );
        return [
            'tipo' => $tipo,
            'nome' => $pto ? $pto->labels->name : '',
            'sito' => home_url( '/' ),
            'casa' => self::casa( $tipo ),
            'voci' => array_map( function ( $v ) {
                return [
                    'id'       => $v['id'],
                    'genitore' => $v['genitore'],
                    'titolo'   => $v['titolo'],
                    'livello'  => $v['livello'],
                    'figli'    => $v['figli'],
                    'link'     => (string) get_permalink( $v['post'] ),
                ];
            }, self::albero( $tipo ) ),
        ];
    }

    /** Un argomento: titolo, sottotitolo, testo, video, link, antenati, sottoargomenti, precedente e successivo. */
    public static function argomento_dati( $post ) {
        $tipo   = $post->post_type;
        $albero = self::albero( $tipo );
        list( $prima, $dopo ) = self::vicini( $post );
        $breve = function ( $p ) {
            return $p ? [ 'id' => (int) $p->ID, 'titolo' => self::titolo( $p ) ] : null;
        };
        $video = '';
        $t     = class_exists( 'Olobuild_Tipi_Contenuto' ) ? Olobuild_Tipi_Contenuto::del_tipo( $tipo ) : null;
        foreach ( (array) ( $t['campi'] ?? [] ) as $c ) {
            if ( 'video' === ( $c['tipo'] ?? '' ) ) {
                $video = (string) get_post_meta( $post->ID, $c['chiave'], true );
                break;
            }
        }
        return [
            'id'          => (int) $post->ID,
            'titolo'      => self::titolo( $post ),
            'sottotitolo' => $post->post_excerpt,
            'html'        => wpautop( $post->post_content ),
            'video'       => $video,
            'link'        => (string) get_permalink( $post ),
            'antenati'    => array_map( function ( $id ) {
                return [ 'id' => $id, 'titolo' => self::titolo( $id ) ];
            }, self::antenati( $post ) ),
            'figli'       => array_values( array_map( function ( $v ) {
                return [ 'id' => $v['id'], 'titolo' => $v['titolo'] ];
            }, array_filter( $albero, function ( $v ) use ( $post ) {
                return $v['genitore'] === (int) $post->ID;
            } ) ) ),
            'prima'       => $breve( $prima ),
            'dopo'        => $breve( $dopo ),
            'modificato'  => get_post_modified_time( 'c', true, $post ),
        ];
    }

    public static function rest_indice( WP_REST_Request $r ) {
        $risposta = rest_ensure_response( self::indice_dati( sanitize_key( (string) $r['tipo'] ) ) );
        $risposta->header( 'Cache-Control', 'public, max-age=600' );
        return $risposta;
    }

    public static function rest_argomento( WP_REST_Request $r ) {
        $tipo = sanitize_key( (string) $r['tipo'] );
        $post = get_post( (int) $r['id'] );
        if ( ! $post || $post->post_type !== $tipo || 'publish' !== $post->post_status || post_password_required( $post ) ) {
            return new WP_Error( 'olo_argomento', __( 'Argomento non trovato.', 'olobuild' ), [ 'status' => 404 ] );
        }
        $risposta = rest_ensure_response( self::argomento_dati( $post ) );
        $risposta->header( 'Cache-Control', 'public, max-age=600' );
        return $risposta;
    }
}
