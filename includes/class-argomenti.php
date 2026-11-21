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

    /* ─────────────────────────────────────────────
     * Le lingue (OLOlang): ogni argomento ha una copia per lingua (_olo_lang_source sulla copia); l'albero, i vicini,
     * gli antenati e la rotta pubblica danno le copie nella lingua di chi legge, gli originali dove manca la copia.
     * ───────────────────────────────────────────── */

    /**
     * La lingua in cui leggere: quella chiesta (o quella di chi guarda, per OLOlang) se il sito la ha; per una lingua che
     * il sito non ha, l'inglese se c'è, che per chi non legge l'italiano serve più dell'originale. Senza OLOlang ''.
     *
     * @param string|null $chiesta Codice («en») o locale («de_DE»); null = la lingua di chi guarda.
     */
    public static function lingua( $chiesta = null ) {
        if ( ! class_exists( 'Olo_Lang_Language' ) ) {
            return '';
        }
        $principale = Olo_Lang_Language::get_default_lang();
        $attive     = array_map( 'strval', array_column( Olo_Lang_Language::get_active_languages(), 'code' ) );
        if ( null === $chiesta ) {
            $chiesta = Olo_Lang_Language::detect_current_lang();
        }
        $chiesta = strtolower( (string) $chiesta );
        if ( in_array( $chiesta, $attive, true ) ) {
            return $chiesta;
        }
        $corta = substr( str_replace( '-', '_', $chiesta ), 0, 2 );
        if ( in_array( $corta, $attive, true ) ) {
            return $corta;
        }
        return ( '' !== $corta && $corta !== $principale && in_array( 'en', $attive, true ) ) ? 'en' : $principale;
    }

    /** L'originale di un contenuto (lui stesso se non è una traduzione). */
    public static function originale( $id ) {
        $o = (int) get_post_meta( (int) $id, '_olo_lang_source', true );
        return $o ? $o : (int) $id;
    }

    /** Una copia si legge se è pubblicata, o privata per chi la può leggere (come gli originali nell'albero). */
    private static function leggibile( $p ) {
        return $p && ( 'publish' === $p->post_status || ( 'private' === $p->post_status && current_user_can( 'read_post', $p->ID ) ) );
    }

    /**
     * Il contenuto nella lingua: la sua copia se c'è e si legge, se no l'originale.
     *
     * @param int    $id     Un originale o una sua copia.
     * @param string $lingua Codice della lingua ('' o la principale = l'originale).
     * @return int
     */
    public static function versione( $id, $lingua ) {
        $orig = self::originale( $id );
        if ( '' === (string) $lingua || ! class_exists( 'Olo_Lang_Language' ) || Olo_Lang_Language::get_default_lang() === $lingua ) {
            return $orig;
        }
        $copie = (array) ( get_post_meta( $orig, '_olo_lang_translations', true ) ?: [] );
        $c     = ! empty( $copie[ $lingua ] ) ? get_post( (int) $copie[ $lingua ] ) : null;
        return ( $c && self::originale( $c->ID ) === $orig && self::leggibile( $c ) ) ? (int) $c->ID : $orig;
    }

    /** Il link di un contenuto nella sua lingua (una traduzione con ?lang= o /en/, anche dall'amministrazione). */
    public static function link( $p ) {
        $url = (string) get_permalink( $p );
        return class_exists( 'Olobuild_Redazione_Lingue' ) ? Olobuild_Redazione_Lingue::nella_lingua( $url, is_object( $p ) ? $p->ID : (int) $p ) : $url;
    }

    /**
     * I contenuti di un tipo in ordine di lettura: [ [ 'id', 'genitore', 'titolo', 'livello', 'figli', 'post' ], … ].
     * Un contenuto il cui genitore non è fra quelli letti (in bozza, nel cestino) sale in cima. Con le lingue di OLOlang
     * ogni voce è la copia nella lingua (sotto la versione nella lingua del genitore dell'originale), o l'originale se la
     * copia manca; le copie delle altre lingue restano fuori.
     *
     * @param string      $tipo   Tipo di contenuto.
     * @param array       $args   Argomenti in più per WP_Query (stati…).
     * @param string|null $lingua Codice della lingua; null = quella di chi guarda.
     */
    public static function albero( $tipo, $args = [], $lingua = null ) {
        if ( ! self::ha_argomenti( $tipo ) ) {
            return [];
        }
        $lingua     = null === $lingua ? self::lingua() : (string) $lingua;
        $principale = class_exists( 'Olo_Lang_Language' ) ? Olo_Lang_Language::get_default_lang() : '';
        // Senza stati scelti vale la regola di WordPress: i pubblicati, più i privati per chi può leggerli (così chi
        // prepara un manuale lo vede intero sul sito prima di pubblicarlo; visitatori e rotta pubblica, solo i pubblicati).
        // olo_lang_tutte: originali e copie come sono, la scelta della lingua si fa qui sotto (OLOlang la farebbe con la
        // lingua del visitatore, anche quando se ne chiede un'altra, e lascerebbe le copie sotto i genitori sbagliati).
        $q = new WP_Query( array_merge( [
            'post_type'              => $tipo,
            'posts_per_page'         => self::MASSIMO,
            'orderby'                => [ 'menu_order' => 'ASC', 'title' => 'ASC' ],
            'no_found_rows'          => true,
            'ignore_sticky_posts'    => true,
            'update_post_term_cache' => false,
            'olo_lang_tutte'         => true,
        ], $args ) );
        // Una voce per originale: la sua copia nella lingua se c'è, se no lui (chiave = id dell'originale).
        $scelti = [];
        foreach ( $q->posts as $p ) {
            $orig = (int) get_post_meta( $p->ID, '_olo_lang_source', true );
            if ( ! $orig ) {
                if ( ! isset( $scelti[ $p->ID ] ) ) {
                    $scelti[ $p->ID ] = $p;
                }
            } elseif ( '' !== $lingua && $lingua !== $principale && (string) get_post_meta( $p->ID, '_olo_lang_lang', true ) === $lingua ) {
                $scelti[ $orig ] = $p;
            }
        }
        // Il genitore di ogni voce: la versione scelta del genitore dell'originale.
        $padre = [];
        foreach ( $scelti as $orig => $p ) {
            $o              = (int) $p->ID === (int) $orig ? $p : get_post( $orig );
            $g              = $o ? (int) $o->post_parent : 0;
            $padre[ $p->ID ] = ( $g && isset( $scelti[ $g ] ) ) ? (int) $scelti[ $g ]->ID : 0;
        }
        $figli = [];
        foreach ( $scelti as $p ) {
            $figli[ $padre[ $p->ID ] ][] = $p;
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

    /**
     * Gli antenati di un contenuto, dal più alto al genitore (id), nella lingua: quelli dell'originale, ognuno nella sua
     * versione (la copia di una traduzione può stare ancora sotto il genitore italiano).
     *
     * @param WP_Post|int $post   Il contenuto (originale o copia).
     * @param string|null $lingua Codice della lingua; null = quella del contenuto, se è una copia, o di chi guarda.
     */
    public static function antenati( $post, $lingua = null ) {
        $post = get_post( $post );
        if ( ! $post ) {
            return [];
        }
        if ( null === $lingua ) {
            $sua    = (string) get_post_meta( $post->ID, '_olo_lang_lang', true );
            $lingua = '' !== $sua ? $sua : self::lingua();
        }
        $su = array_reverse( array_map( 'intval', get_post_ancestors( self::originale( $post->ID ) ) ) );
        return array_map( function ( $id ) use ( $lingua ) {
            return self::versione( $id, $lingua );
        }, $su );
    }

    /**
     * Il contenuto prima e quello dopo nell'ordine dell'indice: [ WP_Post|null, WP_Post|null ].
     *
     * @param WP_Post     $post   Il contenuto aperto.
     * @param string|null $lingua Codice della lingua; null = quella del contenuto, se è una copia, o di chi guarda.
     */
    public static function vicini( $post, $lingua = null ) {
        if ( null === $lingua ) {
            $sua    = (string) get_post_meta( $post->ID, '_olo_lang_lang', true );
            $lingua = '' !== $sua ? $sua : self::lingua();
        }
        $albero = self::albero( $post->post_type, [], $lingua );
        $qui    = self::versione( $post->ID, $lingua );
        foreach ( $albero as $i => $v ) {
            if ( $v['id'] === $qui ) {
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
    public static function indice_dati( $tipo, $lingua = null ) {
        $pto    = get_post_type_object( $tipo );
        $lingua = null === $lingua ? self::lingua() : self::lingua( $lingua );
        $casa   = self::casa( $tipo );
        if ( '' !== $lingua && class_exists( 'Olo_Lang_Language' ) && Olo_Lang_Language::get_default_lang() !== $lingua ) {
            $casa = Olo_Lang_Language::get_language_url( $lingua, $casa );
        }
        return [
            'tipo'   => $tipo,
            'nome'   => $pto ? $pto->labels->name : '',
            'sito'   => home_url( '/' ),
            'casa'   => $casa,
            'lingua' => $lingua,
            'voci'   => array_map( function ( $v ) {
                return [
                    'id'       => $v['id'],
                    'genitore' => $v['genitore'],
                    'titolo'   => $v['titolo'],
                    'livello'  => $v['livello'],
                    'figli'    => $v['figli'],
                    'link'     => self::link( $v['post'] ),
                ];
            }, self::albero( $tipo, [], $lingua ) ),
        ];
    }

    /**
     * Un argomento: titolo, sottotitolo, testo, video, link, antenati, sottoargomenti, precedente e successivo, nella
     * lingua chiesta (la copia di quella lingua al posto dell'argomento dato, se c'è).
     */
    public static function argomento_dati( $post, $lingua = null ) {
        $lingua = null === $lingua ? self::lingua() : self::lingua( $lingua );
        $post   = get_post( self::versione( $post->ID, $lingua ) );
        $tipo   = $post->post_type;
        $albero = self::albero( $tipo, [], $lingua );
        list( $prima, $dopo ) = self::vicini( $post, $lingua );
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
            'link'        => self::link( $post ),
            'lingua'      => $lingua,
            'antenati'    => array_map( function ( $id ) {
                return [ 'id' => $id, 'titolo' => self::titolo( $id ) ];
            }, self::antenati( $post, $lingua ) ),
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

    /** La lingua chiesta alla rotta pubblica (?lang=de, o il locale della bacheca che chiede: ?lang=de_DE). */
    private static function lingua_chiesta( WP_REST_Request $r ) {
        $l = sanitize_text_field( (string) $r->get_param( 'lang' ) );
        return self::lingua( '' !== $l ? $l : null );
    }

    public static function rest_indice( WP_REST_Request $r ) {
        $risposta = rest_ensure_response( self::indice_dati( sanitize_key( (string) $r['tipo'] ), self::lingua_chiesta( $r ) ) );
        $risposta->header( 'Cache-Control', 'public, max-age=600' );
        return $risposta;
    }

    public static function rest_argomento( WP_REST_Request $r ) {
        $tipo = sanitize_key( (string) $r['tipo'] );
        $post = get_post( (int) $r['id'] );
        if ( ! $post || $post->post_type !== $tipo || 'publish' !== $post->post_status || post_password_required( $post ) ) {
            return new WP_Error( 'olo_argomento', __( 'Argomento non trovato.', 'olobuild' ), [ 'status' => 404 ] );
        }
        $risposta = rest_ensure_response( self::argomento_dati( $post, self::lingua_chiesta( $r ) ) );
        $risposta->header( 'Cache-Control', 'public, max-age=600' );
        return $risposta;
    }
}
