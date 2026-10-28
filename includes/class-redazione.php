<?php
/**
 * Olobuild_Redazione — la Redazione: una pagina sola per scrivere gli Articoli e i Tipi di contenuto (Notizie,
 * Comunicati, le schede dei temi…), con l'elenco e la scheda: testo, campi, categorie e tag, immagine, pubblicazione.
 * È una voce di primo livello nel menu di WordPress per chi può scrivere in almeno un tipo: il menu di OLObuild lo vedono
 * solo gli amministratori.
 *
 * WordPress resta il motore: permessi del tipo, revisioni, programmazione, blocco di modifica, cestino. Le schede di
 * WordPress restano la riserva per ciò che l'editor della Redazione non sa tenere (testi a blocchi, HTML con stili).
 * App Vue in src/redazione/ (bundle assets/js/redazione.js, vite.config.redazione.js), rotte olobuild/v1/redazione.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Redazione {

    const SLUG = 'olo-redazione';
    const NS   = 'olobuild/v1';

    /** I tag che l'editor della Redazione sa tenere: con altri tag, commenti o stili in linea il testo resta in sola lettura. */
    const TAG_TESTO = [ 'p', 'br', 'h2', 'h3', 'h4', 'strong', 'b', 'em', 'i', 'a', 'ul', 'ol', 'li', 'blockquote', 'img', 'hr' ];

    const STATI = [ 'publish', 'future', 'draft', 'pending', 'private' ];

    public static function avvia() {
        add_action( 'rest_api_init', [ __CLASS__, 'rotte' ] );
        if ( is_admin() ) {
            add_action( 'admin_menu', [ __CLASS__, 'menu' ] );
            add_action( 'admin_enqueue_scripts', [ __CLASS__, 'script' ] );
        }
    }

    /** I tipi in cui l'utente può scrivere, chiave => impostazioni: gli Articoli e i Tipi di contenuto (anche di sistema). */
    public static function tipi() {
        $out = [];
        foreach ( array_merge( [ Olobuild_Tipi_Contenuto::impostazioni()['articoli'] ], Olobuild_Tipi_Contenuto::tipi() ) as $t ) {
            $pto = get_post_type_object( $t['chiave'] );
            if ( $pto && current_user_can( $pto->cap->edit_posts ) ) {
                $out[ $t['chiave'] ] = $t;
            }
        }
        return $out;
    }

    /* ─────────────────────────────────────────────
     * Pagina
     * ───────────────────────────────────────────── */

    public static function menu() {
        $tipi = self::tipi();
        if ( ! $tipi ) {
            return;
        }
        $pto = get_post_type_object( (string) array_key_first( $tipi ) );
        add_menu_page( __( 'Redazione', 'olobuild' ), __( 'Redazione', 'olobuild' ), $pto->cap->edit_posts, self::SLUG, [ __CLASS__, 'pagina' ], 'dashicons-edit-page', 3 );
    }

    public static function pagina() {
        echo '<div class="wrap olo-rz-wrap"><h1 class="screen-reader-text">' . esc_html__( 'Redazione', 'olobuild' ) . '</h1><div id="olo-redazione"></div></div>';
    }

    public static function script( $hook ) {
        if ( 'toplevel_page_' . self::SLUG !== $hook ) {
            return;
        }
        // La libreria media e il ritaglio di WordPress: foto, allegati e le foto per i social inquadrate.
        wp_enqueue_media();
        wp_enqueue_script( 'imgareaselect' );
        wp_enqueue_style( 'imgareaselect' );
        wp_enqueue_script( 'olobuild-redazione', OLOBUILD_URL . 'assets/js/redazione.js', [ 'jquery', 'media-views', 'imgareaselect' ], OLOBUILD_VERSION, true );
        $formati = [];
        foreach ( Olobuild_Tipi_Contenuto::FORMATI_SOCIAL as $k => $f ) {
            $formati[ (string) $k ] = [ 'nome' => __( $f['nome'], 'olobuild' ), 'w' => $f['w'], 'h' => $f['h'] ]; // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText -- i nomi dei formati sono nel catalogo
        }
        wp_localize_script( 'olobuild-redazione', 'oloData', [
            'restUrl'      => esc_url_raw( rest_url( self::NS ) . '/' ),
            'nonce'        => wp_create_nonce( 'wp_rest' ),
            'adminUrl'     => admin_url(),
            'siteUrl'      => home_url( '/' ),
            'version'      => OLOBUILD_VERSION,
            'locale'       => olobuild_current_locale(),
            'translations' => olobuild_builder_translations(),
            'redazione'    => [
                'tipi'    => array_values( array_map( [ __CLASS__, 'descrivi_tipo' ], self::tipi() ) ),
                'formati' => $formati,
            ],
        ] );
    }

    /** Ciò che l'app sa di un tipo: nomi, campi, tassonomie, cosa l'utente può fare. */
    public static function descrivi_tipo( $t ) {
        $k   = $t['chiave'];
        $pto = get_post_type_object( $k );
        $tax = [];
        foreach ( self::tassonomie( $k ) as $x ) {
            $tax[] = [
                'nome'       => $x->name,
                'etichetta'  => $x->labels->name,
                'singolare'  => $x->labels->singular_name,
                'gerarchica' => (bool) $x->hierarchical,
                'assegna'    => current_user_can( $x->cap->assign_terms ),
                'crea'       => current_user_can( $x->cap->edit_terms ),
            ];
        }
        return [
            'chiave'     => $k,
            'nome'       => $pto->labels->name,
            'singolare'  => $pto->labels->singular_name,
            'icona'      => ( is_string( $pto->menu_icon ) && 0 === strpos( $pto->menu_icon, 'dashicons-' ) ) ? $pto->menu_icon : 'dashicons-admin-post',
            'campi'      => $t['campi'],
            'estratto'   => post_type_supports( $k, 'excerpt' ),
            'immagine'   => post_type_supports( $k, 'thumbnail' ),
            'tassonomie' => $tax,
            'crea'       => current_user_can( $pto->cap->create_posts ),
            'pubblica'   => current_user_can( $pto->cap->publish_posts ),
            'nuovoWp'    => admin_url( 'post-new.php' . ( 'post' === $k ? '' : '?post_type=' . $k ) ),
        ];
    }

    /** Le tassonomie di un tipo che si scelgono nella scheda (niente formati dei post). */
    private static function tassonomie( $tipo ) {
        return array_filter( get_object_taxonomies( $tipo, 'objects' ), function ( $x ) {
            return $x->show_ui && 'post_format' !== $x->name;
        } );
    }

    /* ─────────────────────────────────────────────
     * Testo
     * ───────────────────────────────────────────── */

    /**
     * Perché il testo non si può scrivere nella Redazione: 'blocchi' (scritto con l'editor a blocchi), 'html' (tag, commenti
     * o stili che l'editor perderebbe), '' = si può. Il testo resta allora com'è e si cambia nella scheda di WordPress.
     */
    public static function motivo_sola_lettura( $testo ) {
        $testo = (string) $testo;
        if ( false !== strpos( $testo, '<!-- wp:' ) ) {
            return 'blocchi';
        }
        if ( false !== strpos( $testo, '<!--' ) || preg_match( '/\sstyle\s*=|\[caption/i', $testo ) ) {
            return 'html';
        }
        preg_match_all( '#</?([a-z][a-z0-9]*)\b#i', $testo, $m );
        foreach ( array_unique( array_map( 'strtolower', $m[1] ) ) as $tag ) {
            if ( ! in_array( $tag, self::TAG_TESTO, true ) ) {
                return 'html';
            }
        }
        return '';
    }

    /** Un testo dell'editor classico (paragrafi separati da righe vuote) con i suoi <p>, come lo legge l'editor. */
    private static function con_paragrafi( $testo ) {
        $testo = (string) $testo;
        return ( '' !== trim( $testo ) && false === stripos( $testo, '<p' ) ) ? wpautop( $testo ) : $testo;
    }

    /* ─────────────────────────────────────────────
     * Dati per l'app
     * ───────────────────────────────────────────── */

    private static function nome_utente( $id ) {
        $u = get_userdata( (int) $id );
        return $u ? $u->display_name : '';
    }

    private static function quando( $ts ) {
        return $ts ? wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $ts ) : '';
    }

    /** La prima tassonomia a cartelle del tipo (le categorie): la mostra la riga dell'elenco e la filtra la sua tendina. */
    private static function tassonomia_principale( $tipo ) {
        foreach ( self::tassonomie( $tipo ) as $x ) {
            if ( $x->hierarchical ) {
                return $x->name;
            }
        }
        return '';
    }

    /** Un file della libreria per l'app: titolo, indirizzi, estensione e peso. */
    private static function media( $aid ) {
        $p = get_post( (int) $aid );
        if ( ! $p || 'attachment' !== $p->post_type ) {
            return null;
        }
        $file = get_attached_file( $p->ID );
        return [
            'id'     => $p->ID,
            'titolo' => html_entity_decode( get_the_title( $p ), ENT_QUOTES, 'UTF-8' ),
            'url'    => (string) wp_get_attachment_url( $p->ID ),
            'mini'   => wp_attachment_is_image( $p->ID ) ? (string) wp_get_attachment_image_url( $p->ID, 'medium' ) : '',
            'est'    => strtoupper( (string) pathinfo( (string) $file, PATHINFO_EXTENSION ) ),
            'peso'   => ( $file && file_exists( $file ) ) ? size_format( filesize( $file ) ) : '',
        ];
    }

    /** Una riga dell'elenco. */
    private static function voce( $post ) {
        $tax     = self::tassonomia_principale( $post->post_type );
        $termini = $tax ? wp_get_post_terms( $post->ID, $tax, [ 'fields' => 'names' ] ) : [];
        $blocco  = wp_check_post_lock( $post->ID );
        return [
            'id'         => $post->ID,
            'titolo'     => html_entity_decode( get_the_title( $post ), ENT_QUOTES, 'UTF-8' ),
            'stato'      => $post->post_status,
            'data'       => self::quando( get_post_timestamp( $post ) ),
            'modificato' => self::quando( get_post_timestamp( $post, 'modified' ) ),
            'autore'     => self::nome_utente( $post->post_author ),
            'miniatura'  => (string) get_the_post_thumbnail_url( $post, 'thumbnail' ),
            'termini'    => is_array( $termini ) ? array_map( function ( $n ) {
                return html_entity_decode( $n, ENT_QUOTES, 'UTF-8' );
            }, $termini ) : [],
            'bloccato'   => $blocco ? self::nome_utente( $blocco ) : '',
            'link'       => 'publish' === $post->post_status ? (string) get_permalink( $post ) : '',
        ];
    }

    /** La scheda di un contenuto: tutto ciò che l'app mostra e modifica. */
    private static function scheda( $post ) {
        $t      = Olobuild_Tipi_Contenuto::del_tipo( $post->post_type );
        $motivo = self::motivo_sola_lettura( $post->post_content );
        $campi  = [];
        $media  = [];
        foreach ( $t ? $t['campi'] : [] as $c ) {
            $v   = get_post_meta( $post->ID, $c['chiave'], true );
            $v   = is_scalar( $v ) ? (string) $v : '';
            $ids = [];
            if ( 'formattato' === $c['tipo'] ) {
                $v = self::con_paragrafi( $v );
            } elseif ( in_array( $c['tipo'], [ 'immagine', 'galleria', 'allegati' ], true ) ) {
                $ids = explode( ',', $v );
            } elseif ( 'foto_social' === $c['tipo'] ) {
                $d   = json_decode( $v, true );
                $ids = is_array( $d ) ? array_values( $d ) : [];
            }
            $campi[ $c['chiave'] ] = $v;
            foreach ( array_filter( array_map( 'absint', $ids ) ) as $aid ) {
                $m = self::media( $aid );
                if ( $m ) {
                    $media[ $aid ] = $m;
                }
            }
        }
        $termini = [];
        foreach ( self::tassonomie( $post->post_type ) as $x ) {
            $v                   = wp_get_post_terms( $post->ID, $x->name, [ 'fields' => $x->hierarchical ? 'ids' : 'names' ] );
            $v                   = is_array( $v ) ? $v : [];
            $termini[ $x->name ] = $x->hierarchical ? array_map( 'intval', $v ) : array_map( function ( $n ) {
                return html_entity_decode( $n, ENT_QUOTES, 'UTF-8' );
            }, $v );
        }
        $evid   = (int) get_post_thumbnail_id( $post );
        $blocco = wp_check_post_lock( $post->ID );
        $ts     = get_post_timestamp( $post );
        $mod    = get_post_timestamp( $post, 'modified' );
        return [
            'id'                  => $post->ID,
            'tipo'                => $post->post_type,
            'titolo'              => $post->post_title,
            'testo'               => '' === $motivo ? self::con_paragrafi( $post->post_content ) : '',
            'solaLettura'         => $motivo,
            'anteprimaTesto'      => '' !== $motivo ? wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 60 ) : '',
            'riassunto'           => $post->post_excerpt,
            'stato'               => $post->post_status,
            // Una bozza senza data fissata esce quando la si pubblica (post_date_gmt vuota).
            'dataFissata'         => '0000-00-00 00:00:00' !== $post->post_date_gmt,
            'data'                => $ts ? wp_date( 'Y-m-d\TH:i', $ts ) : '',
            'dataLeggibile'       => self::quando( $ts ),
            'immagine'            => $evid ? self::media( $evid ) : null,
            'termini'             => (object) $termini,
            'campi'               => (object) $campi,
            'media'               => (object) $media,
            'modificato'          => (int) $mod,
            'modificatoLeggibile' => self::quando( $mod ),
            'autore'              => self::nome_utente( $post->post_author ),
            'bloccato'            => $blocco ? self::nome_utente( $blocco ) : '',
            'link'                => (string) get_permalink( $post ),
            'anteprima'           => 'publish' === $post->post_status ? (string) get_permalink( $post ) : (string) get_preview_post_link( $post ),
            'wpModifica'          => (string) get_edit_post_link( $post->ID, 'raw' ),
            'puoCestinare'        => current_user_can( 'delete_post', $post->ID ),
        ];
    }

    /* ─────────────────────────────────────────────
     * REST
     * ───────────────────────────────────────────── */

    public static function rotte() {
        register_rest_route( self::NS, '/redazione', [
            [
                'methods'             => 'GET',
                'callback'            => [ __CLASS__, 'rest_elenco' ],
                'permission_callback' => [ __CLASS__, 'puo_tipo' ],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [ __CLASS__, 'rest_crea' ],
                'permission_callback' => [ __CLASS__, 'puo_tipo' ],
            ],
        ] );
        register_rest_route( self::NS, '/redazione/(?P<id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [ __CLASS__, 'rest_leggi' ],
                'permission_callback' => [ __CLASS__, 'puo_contenuto' ],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [ __CLASS__, 'rest_salva' ],
                'permission_callback' => [ __CLASS__, 'puo_contenuto' ],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [ __CLASS__, 'rest_cestina' ],
                'permission_callback' => [ __CLASS__, 'puo_contenuto' ],
            ],
        ] );
        register_rest_route( self::NS, '/redazione/(?P<id>\d+)/blocco', [
            'methods'             => 'POST',
            'callback'            => [ __CLASS__, 'rest_blocco' ],
            'permission_callback' => [ __CLASS__, 'puo_contenuto' ],
        ] );
        register_rest_route( self::NS, '/redazione/termini', [
            [
                'methods'             => 'GET',
                'callback'            => [ __CLASS__, 'rest_termini' ],
                'permission_callback' => [ __CLASS__, 'puo_tassonomia' ],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [ __CLASS__, 'rest_nuovo_termine' ],
                'permission_callback' => [ __CLASS__, 'puo_tassonomia' ],
            ],
        ] );
    }

    public static function puo_tipo( WP_REST_Request $r ) {
        return isset( self::tipi()[ sanitize_key( (string) $r['tipo'] ) ] );
    }

    public static function puo_contenuto( WP_REST_Request $r ) {
        $post = get_post( (int) $r['id'] );
        return $post && isset( self::tipi()[ $post->post_type ] ) && current_user_can( 'edit_post', $post->ID );
    }

    public static function puo_tassonomia( WP_REST_Request $r ) {
        $tax = get_taxonomy( sanitize_key( (string) $r['tassonomia'] ) );
        if ( ! $tax ) {
            return false;
        }
        return (bool) array_intersect( (array) $tax->object_type, array_keys( self::tipi() ) );
    }

    /** Le funzioni del blocco di modifica stanno in wp-admin, che le richieste REST non caricano. */
    private static function carica_admin() {
        require_once ABSPATH . 'wp-admin/includes/post.php';
    }

    public static function rest_elenco( WP_REST_Request $r ) {
        self::carica_admin();
        $tipo    = sanitize_key( (string) $r['tipo'] );
        $pto     = get_post_type_object( $tipo );
        $stato   = sanitize_key( (string) $r['stato'] );
        $proprio = ! current_user_can( $pto->cap->edit_others_posts );
        $args    = [
            'post_type'           => $tipo,
            'post_status'         => in_array( $stato, self::STATI, true ) ? $stato : self::STATI,
            'perm'                => 'readable',
            'posts_per_page'      => 30,
            'paged'               => max( 1, (int) $r['pagina'] ),
            'orderby'             => 'modified',
            'order'               => 'DESC',
            'ignore_sticky_posts' => true,
        ];
        $cerca = sanitize_text_field( (string) $r['cerca'] );
        if ( '' !== $cerca ) {
            $args['s'] = $cerca;
        }
        // Chi non può correggere i contenuti degli altri vede i suoi.
        if ( $proprio ) {
            $args['author'] = get_current_user_id();
        }
        $tassonomia = sanitize_key( (string) $r['tassonomia'] );
        $termine    = absint( $r['termine'] );
        if ( $termine && $tassonomia && is_object_in_taxonomy( $tipo, $tassonomia ) ) {
            $args['tax_query'] = [ [ 'taxonomy' => $tassonomia, 'terms' => $termine ] ]; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- il filtro per categoria dell'elenco
        }
        $q        = new WP_Query( $args );
        $conteggi = [];
        foreach ( self::STATI as $s ) {
            $c = [ 'post_type' => $tipo, 'post_status' => $s, 'perm' => 'readable', 'posts_per_page' => 1, 'fields' => 'ids' ];
            if ( $proprio ) {
                $c['author'] = get_current_user_id();
            }
            $conteggi[ $s ] = (int) ( new WP_Query( $c ) )->found_posts;
        }
        return rest_ensure_response( [
            'voci'     => array_map( [ __CLASS__, 'voce' ], $q->posts ),
            'totale'   => (int) $q->found_posts,
            'pagine'   => (int) $q->max_num_pages,
            'conteggi' => $conteggi,
        ] );
    }

    public static function rest_leggi( WP_REST_Request $r ) {
        self::carica_admin();
        $post = get_post( (int) $r['id'] );
        if ( ! wp_check_post_lock( $post->ID ) ) {
            wp_set_post_lock( $post->ID );
        }
        return rest_ensure_response( self::scheda( $post ) );
    }

    public static function rest_crea( WP_REST_Request $r ) {
        $tipo = sanitize_key( (string) $r['tipo'] );
        if ( ! current_user_can( get_post_type_object( $tipo )->cap->create_posts ) ) {
            return new WP_Error( 'olo_redazione_permesso', __( 'Non puoi creare contenuti di questo tipo.', 'olobuild' ), [ 'status' => 403 ] );
        }
        return self::scrivi( null, $tipo, $r );
    }

    public static function rest_salva( WP_REST_Request $r ) {
        self::carica_admin();
        $post  = get_post( (int) $r['id'] );
        $forza = ! empty( $r['forza'] );
        $altro = wp_check_post_lock( $post->ID );
        if ( $altro && ! $forza ) {
            return new WP_Error( 'olo_redazione_bloccato', sprintf(
                /* translators: %s: nome di chi sta modificando il contenuto */
                __( '%s lo sta modificando adesso.', 'olobuild' ),
                self::nome_utente( $altro )
            ), [ 'status' => 409 ] );
        }
        // Salvato da qualcun altro dopo che la scheda è stata aperta: non si scrive sopra senza chiederlo.
        $visto = (int) $r['modificato'];
        $mod   = (int) get_post_timestamp( $post, 'modified' );
        if ( $visto && $mod > $visto && ! $forza ) {
            return new WP_Error( 'olo_redazione_conflitto', sprintf(
                /* translators: 1: nome di chi ha salvato, 2: data e ora del salvataggio */
                __( '%1$s l\'ha salvato il %2$s, dopo che l\'hai aperto.', 'olobuild' ),
                self::nome_utente( get_post_meta( $post->ID, '_edit_last', true ) ),
                self::quando( $mod )
            ), [ 'status' => 409 ] );
        }
        return self::scrivi( $post, $post->post_type, $r );
    }

    /** Crea o aggiorna il contenuto con ciò che arriva dalla scheda, poi lo rilegge. */
    private static function scrivi( $post, $tipo, WP_REST_Request $r ) {
        self::carica_admin();
        $pto      = get_post_type_object( $tipo );
        $t        = Olobuild_Tipi_Contenuto::del_tipo( $tipo );
        $prima    = $post ? $post->post_status : '';
        $pubblica = current_user_can( $pto->cap->publish_posts );
        $azione   = sanitize_key( (string) $r['azione'] );
        $data     = (string) $r['data'];
        $data     = preg_match( '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $data ) ? str_replace( 'T', ' ', $data ) . ':00' : '';

        switch ( $azione ) {
            case 'pubblica':
                $stato = $pubblica ? 'publish' : 'pending';
                break;
            case 'programma':
                $stato = ( $pubblica && '' !== $data ) ? 'future' : ( $pubblica ? 'publish' : 'pending' );
                break;
            case 'revisione':
                $stato = 'pending';
                break;
            case 'bozza':
                $stato = 'draft';
                break;
            default:
                $stato = ( $post && 'auto-draft' !== $prima ) ? $prima : 'draft';
        }

        $dati = [
            'post_type'   => $tipo,
            'post_title'  => (string) $r['titolo'],
            'post_status' => $stato,
        ];
        // Il testo che l'editor della Redazione non sa tenere resta com'è.
        if ( ! $post || '' === self::motivo_sola_lettura( $post->post_content ) ) {
            $dati['post_content'] = (string) $r['testo'];
        }
        if ( post_type_supports( $tipo, 'excerpt' ) ) {
            $dati['post_excerpt'] = (string) $r['riassunto'];
        }
        // La data: quella scelta; «pubblica» senza data su un contenuto mai uscito = adesso; senza scelta la bozza resta
        // senza data e WordPress le dà quella dell'uscita.
        if ( '' !== $data ) {
            $dati['post_date']     = $data;
            $dati['post_date_gmt'] = get_gmt_from_date( $data );
            $dati['edit_date']     = true;
        } elseif ( 'publish' === $stato && ! in_array( $prima, [ 'publish', 'private' ], true ) ) {
            $dati['post_date']     = current_time( 'mysql' );
            $dati['post_date_gmt'] = current_time( 'mysql', true );
            $dati['edit_date']     = true;
        }

        if ( $post ) {
            $dati['ID'] = $post->ID;
            $id         = wp_update_post( wp_slash( $dati ), true );
        } else {
            $dati['post_author'] = get_current_user_id();
            $id                  = wp_insert_post( wp_slash( $dati ), true );
        }
        if ( is_wp_error( $id ) ) {
            return $id;
        }

        $termini = $r['termini'];
        $termini = is_array( $termini ) ? $termini : [];
        foreach ( self::tassonomie( $tipo ) as $x ) {
            if ( ! array_key_exists( $x->name, $termini ) || ! current_user_can( $x->cap->assign_terms ) ) {
                continue;
            }
            $v = (array) $termini[ $x->name ];
            if ( $x->hierarchical ) {
                $v = array_values( array_filter( array_map( 'absint', $v ), function ( $tid ) use ( $x ) {
                    return $tid && term_exists( $tid, $x->name );
                } ) );
                // Come la scheda di WordPress: un articolo senza categoria va nella categoria predefinita.
                if ( ! $v && 'category' === $x->name ) {
                    $v = [ (int) get_option( 'default_category' ) ];
                }
            } else {
                $v = array_values( array_filter( array_map( function ( $n ) {
                    return trim( sanitize_text_field( (string) $n ) );
                }, $v ), 'strlen' ) );
            }
            wp_set_object_terms( $id, $v, $x->name );
        }

        if ( post_type_supports( $tipo, 'thumbnail' ) && $r->has_param( 'immagine' ) ) {
            $aid = absint( $r['immagine'] );
            if ( $aid && wp_attachment_is_image( $aid ) ) {
                set_post_thumbnail( $id, $aid );
            } else {
                delete_post_thumbnail( $id );
            }
        }

        if ( $t && is_array( $r['campi'] ) ) {
            Olobuild_Tipi_Contenuto::salva_campi( $id, $t, $r['campi'] );
        }
        update_post_meta( $id, '_edit_last', get_current_user_id() );
        wp_set_post_lock( $id );

        // OLOsocial: le scelte del riquadro Social (fatte nella scheda di WordPress) partono anche quando il contenuto esce
        // dalla Redazione; il riquadro, nelle richieste REST, lascia fare a chi salva.
        if ( 'publish' === get_post_status( $id ) && 'publish' !== $prima && class_exists( 'Olo_Social_Riquadro' )
            && method_exists( 'Olo_Social_Riquadro', 'condividi' ) && Olo_Social_Riquadro::attivo_per( $tipo )
            && is_array( get_post_meta( $id, Olo_Social_Riquadro::META, true ) ) ) {
            Olo_Social_Riquadro::condividi( $id );
        }

        $scheda = self::scheda( get_post( $id ) );
        if ( 'pending' === $scheda['stato'] && in_array( $azione, [ 'pubblica', 'programma' ], true ) ) {
            $scheda['avviso'] = __( 'Inviato in revisione: lo pubblica chi può farlo.', 'olobuild' );
        }
        return rest_ensure_response( $scheda );
    }

    public static function rest_cestina( WP_REST_Request $r ) {
        $id = (int) $r['id'];
        if ( ! current_user_can( 'delete_post', $id ) ) {
            return new WP_Error( 'olo_redazione_permesso', __( 'Non puoi spostarlo nel cestino.', 'olobuild' ), [ 'status' => 403 ] );
        }
        if ( ! wp_trash_post( $id ) ) {
            return new WP_Error( 'olo_redazione_cestino', __( 'Non sono riuscito a spostarlo nel cestino.', 'olobuild' ), [ 'status' => 500 ] );
        }
        return rest_ensure_response( [ 'ok' => true ] );
    }

    /** Tiene il blocco di modifica mentre la scheda è aperta; con «prendi» lo toglie a chi lo aveva. */
    public static function rest_blocco( WP_REST_Request $r ) {
        self::carica_admin();
        $id    = (int) $r['id'];
        $altro = wp_check_post_lock( $id );
        if ( $altro && empty( $r['prendi'] ) ) {
            return rest_ensure_response( [ 'bloccato' => self::nome_utente( $altro ) ] );
        }
        wp_set_post_lock( $id );
        return rest_ensure_response( [ 'bloccato' => '' ] );
    }

    private static function termine( $x ) {
        return [
            'id'       => (int) $x->term_id,
            'nome'     => html_entity_decode( $x->name, ENT_QUOTES, 'UTF-8' ),
            'genitore' => (int) $x->parent,
            'conta'    => (int) $x->count,
        ];
    }

    public static function rest_termini( WP_REST_Request $r ) {
        $termini = get_terms( [
            'taxonomy'   => sanitize_key( (string) $r['tassonomia'] ),
            'hide_empty' => false,
            'number'     => 1000,
            'orderby'    => 'name',
        ] );
        return rest_ensure_response( is_array( $termini ) ? array_map( [ __CLASS__, 'termine' ], $termini ) : [] );
    }

    public static function rest_nuovo_termine( WP_REST_Request $r ) {
        $tax = get_taxonomy( sanitize_key( (string) $r['tassonomia'] ) );
        if ( ! current_user_can( $tax->cap->edit_terms ) ) {
            return new WP_Error( 'olo_redazione_permesso', __( 'Non puoi aggiungere voci qui.', 'olobuild' ), [ 'status' => 403 ] );
        }
        $nome = sanitize_text_field( (string) $r['nome'] );
        if ( '' === $nome ) {
            return new WP_Error( 'olo_redazione_nome', __( 'Scrivi il nome.', 'olobuild' ), [ 'status' => 400 ] );
        }
        $genitore = $tax->hierarchical ? absint( $r['genitore'] ) : 0;
        $esiste   = term_exists( $nome, $tax->name, $genitore );
        $fatto    = $esiste ? $esiste : wp_insert_term( $nome, $tax->name, [ 'parent' => $genitore ] );
        if ( is_wp_error( $fatto ) ) {
            return $fatto;
        }
        return rest_ensure_response( self::termine( get_term( (int) $fatto['term_id'], $tax->name ) ) );
    }
}
