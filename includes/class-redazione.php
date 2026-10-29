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
 *
 * Copia di sicurezza e anteprima: la copia è il salvataggio automatico di WordPress (la revisione «autosave» di chi
 * scrive) con accanto, in META, i campi, le categorie e la foto. L'anteprima di WordPress ne legge titolo, testo e
 * riassunto; prepara_anteprima() le fa leggere anche il resto. Così si vede la pagina vera con le modifiche non salvate
 * senza toccare ciò che è sul sito. Le versioni (revisioni) tengono in META la stessa fotografia di campi e categorie.
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

    /** Sulle revisioni e sulla copia di sicurezza: campi, categorie e tag, foto in evidenza (JSON). */
    const META = '_olo_redazione_modulo';

    /** L'anteprima in corso: [ 'id' => contenuto, 'modulo' => ciò che la copia tiene oltre al testo ]. */
    private static $anteprima = null;

    public static function avvia() {
        add_action( 'rest_api_init', [ __CLASS__, 'rotte' ] );
        add_action( '_wp_put_post_revision', [ __CLASS__, 'fotografa_revisione' ], 10, 2 );
        add_filter( 'wp_save_post_revision_post_has_changed', [ __CLASS__, 'revisione_cambiata' ], 10, 3 );
        if ( is_admin() ) {
            add_action( 'admin_menu', [ __CLASS__, 'menu' ] );
            add_action( 'admin_enqueue_scripts', [ __CLASS__, 'script' ] );
        } else {
            add_action( 'init', [ __CLASS__, 'prepara_anteprima' ], 20 );
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
        // «Solo nelle categorie»: la scheda mostra il campo quando una di queste (figlie comprese) è spuntata.
        $tax_cat = Olobuild_Tipi_Contenuto::tassonomia_categorie( $k );
        $campi   = array_map( function ( $c ) use ( $tax_cat ) {
            $c['tutteCategorie'] = ( '' !== $tax_cat && ! empty( $c['categorie'] ) ) ? Olobuild_Tipi_Contenuto::con_figlie( $tax_cat, $c['categorie'] ) : [];
            return $c;
        }, $t['campi'] );
        return [
            'chiave'     => $k,
            'nome'       => $pto->labels->name,
            'singolare'  => $pto->labels->singular_name,
            'icona'      => ( is_string( $pto->menu_icon ) && 0 === strpos( $pto->menu_icon, 'dashicons-' ) ) ? $pto->menu_icon : 'dashicons-admin-post',
            'campi'      => $campi,
            'tassonomiaCategorie' => $tax_cat,
            'estratto'   => post_type_supports( $k, 'excerpt' ),
            'immagine'   => post_type_supports( $k, 'thumbnail' ),
            'tassonomie' => $tax,
            'crea'       => current_user_can( $pto->cap->create_posts ),
            'pubblica'   => current_user_can( $pto->cap->publish_posts ),
            'versioni'   => post_type_supports( $k, 'revisions' ),
            // OLOsocial: gli account e le scelte di partenza per un contenuto nuovo (null = niente social per questo tipo).
            'social'     => self::social_attivo() ? Olo_Social_Riquadro::per_la_scheda( 0, $k ) : null,
            // Dove si aggiungono i campi del tipo: solo per chi può cambiare le impostazioni.
            'configura'  => current_user_can( 'manage_options' ) ? admin_url( 'admin.php?page=olobuilder-settings&tab=tipicontenuto' ) : '',
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

    /** I valori dei campi come li legge la scheda: i testi formattati coi loro paragrafi. */
    private static function campi_per_scheda( $tipo, $campi ) {
        $t   = Olobuild_Tipi_Contenuto::del_tipo( $tipo );
        $out = [];
        foreach ( $t ? $t['campi'] : [] as $c ) {
            $v                   = (string) ( $campi[ $c['chiave'] ] ?? '' );
            $out[ $c['chiave'] ] = 'formattato' === $c['tipo'] ? self::con_paragrafi( $v ) : $v;
        }
        return $out;
    }

    /** I file che i campi (e la foto in evidenza) nominano, id => media(): la scheda ne mostra miniatura e nome. */
    private static function media_di( $tipo, $campi, $immagine = 0 ) {
        $t   = Olobuild_Tipi_Contenuto::del_tipo( $tipo );
        $ids = $immagine ? [ (int) $immagine ] : [];
        foreach ( $t ? $t['campi'] : [] as $c ) {
            $v = (string) ( $campi[ $c['chiave'] ] ?? '' );
            if ( in_array( $c['tipo'], [ 'immagine', 'galleria', 'allegati' ], true ) ) {
                $ids = array_merge( $ids, explode( ',', $v ) );
            } elseif ( 'foto_social' === $c['tipo'] ) {
                $d   = json_decode( $v, true );
                $ids = array_merge( $ids, is_array( $d ) ? array_values( $d ) : [] );
            } elseif ( 'video' === $c['tipo'] && ctype_digit( $v ) ) {
                $ids[] = $v;
            }
        }
        $media = [];
        foreach ( array_unique( array_filter( array_map( 'absint', $ids ) ) ) as $aid ) {
            $m = self::media( $aid );
            if ( $m ) {
                $media[ $aid ] = $m;
            }
        }
        return $media;
    }

    /** Le voci di ogni tassonomia del contenuto: id per le categorie, nomi per i tag. */
    private static function termini_di( $post ) {
        $termini = [];
        foreach ( self::tassonomie( $post->post_type ) as $x ) {
            $v                   = wp_get_post_terms( $post->ID, $x->name, [ 'fields' => $x->hierarchical ? 'ids' : 'names' ] );
            $v                   = is_array( $v ) ? $v : [];
            $termini[ $x->name ] = $x->hierarchical ? array_map( 'intval', $v ) : array_map( function ( $n ) {
                return html_entity_decode( $n, ENT_QUOTES, 'UTF-8' );
            }, $v );
        }
        return $termini;
    }

    /** Ciò che una versione tiene oltre a titolo, testo e riassunto: i campi del tipo, categorie e tag, la foto. */
    private static function modulo_di( $post ) {
        $t     = Olobuild_Tipi_Contenuto::del_tipo( $post->post_type );
        $campi = [];
        foreach ( $t ? $t['campi'] : [] as $c ) {
            $v                     = get_post_meta( $post->ID, $c['chiave'], true );
            $campi[ $c['chiave'] ] = is_scalar( $v ) ? (string) $v : '';
        }
        return [
            'campi'    => $campi,
            'termini'  => self::termini_di( $post ),
            'immagine' => (int) get_post_thumbnail_id( $post ),
        ];
    }

    /** Due moduli dicono la stessa cosa? Campi vuoti e ordine delle voci non contano. */
    private static function stesso_modulo( $a, $b ) {
        $norma = function ( $m ) {
            $termini = [];
            foreach ( (array) ( $m['termini'] ?? [] ) as $k => $v ) {
                $v = array_map( 'strval', (array) $v );
                sort( $v );
                if ( $v ) {
                    $termini[ $k ] = $v;
                }
            }
            ksort( $termini );
            $campi = array_filter( array_map( 'strval', (array) ( $m['campi'] ?? [] ) ), 'strlen' );
            ksort( $campi );
            return [ $campi, $termini, (int) ( $m['immagine'] ?? 0 ) ];
        };
        return $norma( (array) $a ) === $norma( (array) $b );
    }

    /** Il modulo salvato su una revisione o sulla copia, o null se non c'è (revisioni di prima della 1.4.860). */
    private static function modulo_salvato( $revisione_id ) {
        $m = json_decode( (string) get_metadata( 'post', (int) $revisione_id, self::META, true ), true );
        return is_array( $m ) ? $m : null;
    }

    /** La scheda di un contenuto: tutto ciò che l'app mostra e modifica. */
    private static function scheda( $post ) {
        $motivo = self::motivo_sola_lettura( $post->post_content );
        $modulo = self::modulo_di( $post );
        $evid   = $modulo['immagine'];
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
            'termini'             => (object) $modulo['termini'],
            'campi'               => (object) self::campi_per_scheda( $post->post_type, $modulo['campi'] ),
            'media'               => (object) self::media_di( $post->post_type, $modulo['campi'] ),
            'modificato'          => (int) $mod,
            'modificatoLeggibile' => self::quando( $mod ),
            'autore'              => self::nome_utente( $post->post_author ),
            'bloccato'            => $blocco ? self::nome_utente( $blocco ) : '',
            'link'                => (string) get_permalink( $post ),
            'anteprima'           => 'publish' === $post->post_status ? (string) get_permalink( $post ) : (string) get_preview_post_link( $post ),
            'wpModifica'          => (string) get_edit_post_link( $post->ID, 'raw' ),
            'puoCestinare'        => current_user_can( 'delete_post', $post->ID ),
            'copia'               => self::copia_da_riprendere( $post, $motivo, $modulo ),
            'social'              => self::social_attivo() ? Olo_Social_Riquadro::per_la_scheda( $post->ID ) : null,
            'versioni'            => self::conta_versioni( $post->ID ),
        ];
    }

    /** Quante versioni (revisioni, senza le copie di sicurezza) ha il contenuto. */
    private static function conta_versioni( $post_id ) {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery -- un conteggio: wp_get_post_revisions() leggerebbe tutti i testi
        return (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_parent = %d AND post_type = 'revision' AND post_status = 'inherit' AND post_name NOT LIKE %s",
            $post_id,
            '%' . $wpdb->esc_like( '-autosave-v1' )
        ) );
    }

    /**
     * La copia di sicurezza di chi scrive, se è più recente del contenuto e dice qualcosa di diverso: la scheda propone di
     * riprenderla (per esempio dopo una finestra chiusa senza salvare). Null se non c'è.
     */
    private static function copia_da_riprendere( $post, $motivo, $modulo ) {
        $a = wp_get_post_autosave( $post->ID, get_current_user_id() );
        if ( ! $a ) {
            return null;
        }
        // Più vecchia del contenuto: qualcuno ha salvato dopo, la copia non vale più. Nello stesso secondo conta ancora (il
        // confronto qui sotto scarta quella uguale al contenuto).
        $ts = (int) get_post_timestamp( $a, 'modified' );
        if ( $ts < (int) get_post_timestamp( $post, 'modified' ) ) {
            return null;
        }
        $m       = self::modulo_salvato( $a->ID );
        $diversa = normalize_whitespace( $a->post_title ) !== normalize_whitespace( $post->post_title )
            || normalize_whitespace( $a->post_excerpt ) !== normalize_whitespace( $post->post_excerpt )
            || ( '' === $motivo && normalize_whitespace( $a->post_content ) !== normalize_whitespace( $post->post_content ) )
            || ( $m && ! self::stesso_modulo( $m, $modulo ) );
        if ( ! $diversa ) {
            return null;
        }
        return self::da_riprendere( $post, $a, $m, $motivo ) + [ 'alle' => self::quando( $ts ) ];
    }

    /** Ciò che serve alla scheda per riprendere una versione o la copia: titolo, testo, riassunto e il modulo. */
    private static function da_riprendere( $post, $rev, $m, $motivo ) {
        $ripristinabile = '' === $motivo && '' === self::motivo_sola_lettura( $rev->post_content );
        if ( $m ) {
            $m['campi'] = (object) self::campi_per_scheda( $post->post_type, (array) ( $m['campi'] ?? [] ) );
        }
        return [
            'titolo'              => $rev->post_title,
            'riassunto'           => $rev->post_excerpt,
            'testo'               => $ripristinabile ? self::con_paragrafi( $rev->post_content ) : '',
            'testoRipristinabile' => $ripristinabile,
            'modulo'              => $m,
            'media'               => (object) ( $m ? self::media_di( $post->post_type, (array) $m['campi'], (int) ( $m['immagine'] ?? 0 ) ) : [] ),
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
        register_rest_route( self::NS, '/redazione/(?P<id>\d+)/copia', [
            [
                'methods'             => 'POST',
                'callback'            => [ __CLASS__, 'rest_copia' ],
                'permission_callback' => [ __CLASS__, 'puo_contenuto' ],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [ __CLASS__, 'rest_togli_copia' ],
                'permission_callback' => [ __CLASS__, 'puo_contenuto' ],
            ],
        ] );
        register_rest_route( self::NS, '/redazione/(?P<id>\d+)/versioni', [
            'methods'             => 'GET',
            'callback'            => [ __CLASS__, 'rest_versioni' ],
            'permission_callback' => [ __CLASS__, 'puo_contenuto' ],
        ] );
        register_rest_route( self::NS, '/redazione/(?P<id>\d+)/versioni/(?P<versione>\d+)', [
            'methods'             => 'GET',
            'callback'            => [ __CLASS__, 'rest_versione' ],
            'permission_callback' => [ __CLASS__, 'puo_contenuto' ],
        ] );
        register_rest_route( self::NS, '/redazione/(?P<id>\d+)/social', [
            'methods'             => 'POST',
            'callback'            => [ __CLASS__, 'rest_social' ],
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
            // Prima campi, categorie e foto: la versione che WordPress salva con l'aggiornamento li fotografa già nuovi.
            self::scrivi_resto( $post->ID, $tipo, $t, $r );
            $dati['ID'] = $post->ID;
            $id         = wp_update_post( wp_slash( $dati ), true );
        } else {
            $dati['post_author'] = get_current_user_id();
            $id                  = wp_insert_post( wp_slash( $dati ), true );
            if ( ! is_wp_error( $id ) ) {
                self::scrivi_resto( $id, $tipo, $t, $r );
            }
        }
        if ( is_wp_error( $id ) ) {
            return $id;
        }
        update_post_meta( $id, '_edit_last', get_current_user_id() );
        wp_set_post_lock( $id );
        // Le scelte social della scheda (dove, quando, i testi per account), come le salva il riquadro di OLOsocial.
        $social = $r['social'];
        $social = is_array( $social ) ? $social : null;
        if ( $social && self::social_attivo() && null !== Olo_Social_Riquadro::per_la_scheda( $id ) ) {
            Olo_Social_Riquadro::salva_scelte( $id, $social );
        }
        // La copia di sicurezza di chi salva non serve più: ciò che teneva ora è il contenuto.
        $copia = wp_get_post_autosave( $id, get_current_user_id() );
        if ( $copia ) {
            wp_delete_post_revision( $copia->ID );
        }

        // OLOsocial: le scelte social partono quando il contenuto esce dalla Redazione (il riquadro, nelle richieste REST,
        // lascia fare a chi salva), o subito con «Condividi adesso» su un contenuto già pubblicato. Una volta sola.
        $adesso = $social && ! empty( $social['adesso'] );
        if ( 'publish' === get_post_status( $id ) && ( 'publish' !== $prima || $adesso ) && class_exists( 'Olo_Social_Riquadro' )
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

    /** Categorie e tag, foto in evidenza e campi del tipo che arrivano dalla scheda. */
    private static function scrivi_resto( $id, $tipo, $t, WP_REST_Request $r ) {
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
    }

    /* ─────────────────────────────────────────────
     * Copia di sicurezza e anteprima
     * ───────────────────────────────────────────── */

    /** Il modulo che arriva dalla scheda, ripulito come lo sarebbe salvandolo. */
    private static function modulo_da_richiesta( $post, WP_REST_Request $r ) {
        $t     = Olobuild_Tipi_Contenuto::del_tipo( $post->post_type );
        $in    = is_array( $r['campi'] ) ? $r['campi'] : [];
        $campi = [];
        foreach ( $t ? $t['campi'] : [] as $c ) {
            $campi[ $c['chiave'] ] = Olobuild_Tipi_Contenuto::pulisci( $c, $in[ $c['chiave'] ] ?? '' );
        }
        $tin     = is_array( $r['termini'] ) ? $r['termini'] : [];
        $termini = [];
        foreach ( self::tassonomie( $post->post_type ) as $x ) {
            $v                   = (array) ( $tin[ $x->name ] ?? [] );
            $termini[ $x->name ] = $x->hierarchical
                ? array_values( array_filter( array_map( 'absint', $v ) ) )
                : array_values( array_filter( array_map( function ( $n ) {
                    return trim( sanitize_text_field( (string) $n ) );
                }, $v ), 'strlen' ) );
        }
        $data = (string) $r['data'];
        return [
            'campi'    => $campi,
            'termini'  => $termini,
            'immagine' => absint( $r['immagine'] ),
            'data'     => preg_match( '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $data ) ? $data : '',
        ];
    }

    /** L'anteprima di WordPress del contenuto, senza la barra di amministrazione (olo_rz: la mostra la Redazione). */
    private static function link_anteprima( $post ) {
        return (string) get_preview_post_link( $post, [
            'preview_id'    => $post->ID,
            'preview_nonce' => wp_create_nonce( 'post_preview_' . $post->ID ),
            'olo_rz'        => 1,
        ] );
    }

    /**
     * Salva la copia di sicurezza di chi scrive: ciò che ha nella scheda, senza toccare il contenuto. È la revisione
     * «autosave» di WordPress (una per utente) più il modulo in META. Serve all'anteprima e, sui contenuti già usciti, al
     * salvataggio automatico.
     */
    public static function rest_copia( WP_REST_Request $r ) {
        $post = get_post( (int) $r['id'] );
        $uid  = get_current_user_id();
        $base = get_post( $post->ID, ARRAY_A );

        $base['post_title'] = (string) $r['titolo'];
        if ( '' === self::motivo_sola_lettura( $post->post_content ) && $r->has_param( 'testo' ) ) {
            $base['post_content'] = (string) $r['testo'];
        }
        if ( post_type_supports( $post->post_type, 'excerpt' ) ) {
            $base['post_excerpt'] = (string) $r['riassunto'];
        }
        // Come wp_create_post_autosave(), che però toglie la copia quando titolo e testo sono quelli del contenuto: qui
        // possono cambiare solo i campi.
        $vecchia = wp_get_post_autosave( $post->ID, $uid );
        if ( $vecchia ) {
            $dati                = _wp_post_revision_data( $base, true );
            $dati['ID']          = $vecchia->ID;
            $dati['post_author'] = $uid;
            $aid                 = wp_update_post( wp_slash( $dati ), true );
        } else {
            $aid = _wp_put_post_revision( $base, true );
        }
        if ( is_wp_error( $aid ) ) {
            return $aid;
        }
        if ( ! $aid ) {
            return new WP_Error( 'olo_redazione_copia', __( 'Non sono riuscito a salvare la copia di sicurezza.', 'olobuild' ), [ 'status' => 500 ] );
        }
        update_metadata( 'post', $aid, self::META, wp_slash( wp_json_encode( self::modulo_da_richiesta( $post, $r ) ) ) );
        return rest_ensure_response( [
            'alle'      => wp_date( get_option( 'time_format' ), (int) get_post_timestamp( $aid, 'modified' ) ),
            'anteprima' => self::link_anteprima( $post ),
        ] );
    }

    /** Toglie la copia di sicurezza di chi scrive; l'anteprima mostra allora il contenuto salvato. */
    public static function rest_togli_copia( WP_REST_Request $r ) {
        $post = get_post( (int) $r['id'] );
        $a    = wp_get_post_autosave( $post->ID, get_current_user_id() );
        if ( $a ) {
            wp_delete_post_revision( $a->ID );
        }
        return rest_ensure_response( [ 'anteprima' => self::link_anteprima( $post ) ] );
    }

    /**
     * Nell'anteprima di un contenuto (link di WordPress con preview_id e preview_nonce validi) i campi, le categorie e la
     * foto vengono dalla copia di sicurezza di chi guarda, come il testo che WordPress prende da lì (_set_preview()).
     */
    public static function prepara_anteprima() {
        // phpcs:disable WordPress.Security.NonceVerification.Recommended -- il nonce dell'anteprima si verifica qui sotto
        if ( empty( $_GET['preview_id'] ) || empty( $_GET['preview_nonce'] ) ) {
            return;
        }
        $id    = absint( $_GET['preview_id'] );
        $nonce = sanitize_text_field( wp_unslash( $_GET['preview_nonce'] ) );
        $rz    = ! empty( $_GET['olo_rz'] );
        // phpcs:enable WordPress.Security.NonceVerification.Recommended
        if ( ! wp_verify_nonce( $nonce, 'post_preview_' . $id ) || ! current_user_can( 'edit_post', $id ) ) {
            return;
        }
        if ( $rz ) {
            add_filter( 'show_admin_bar', '__return_false' );
        }
        $a = wp_get_post_autosave( $id, get_current_user_id() );
        $m = $a ? self::modulo_salvato( $a->ID ) : null;
        if ( ! $m ) {
            return;
        }
        self::$anteprima = [ 'id' => $id, 'modulo' => $m ];
        add_filter( 'get_post_metadata', [ __CLASS__, 'meta_in_anteprima' ], 20, 4 );
        add_filter( 'get_the_terms', [ __CLASS__, 'termini_in_anteprima' ], 20, 3 );
    }

    public static function meta_in_anteprima( $valore, $object_id, $chiave, $singolo ) {
        if ( ! self::$anteprima || (int) $object_id !== self::$anteprima['id'] || '' === (string) $chiave ) {
            return $valore;
        }
        $m = self::$anteprima['modulo'];
        if ( '_thumbnail_id' === $chiave && isset( $m['immagine'] ) ) {
            $v = $m['immagine'] ? (string) (int) $m['immagine'] : '';
        } elseif ( is_array( $m['campi'] ?? null ) && array_key_exists( $chiave, $m['campi'] ) ) {
            $v = (string) $m['campi'][ $chiave ];
        } else {
            return $valore;
        }
        // Come get_metadata(): un valore solo in un array; un campo vuoto è «nessun valore».
        if ( '' === $v ) {
            return $singolo ? [ '' ] : [];
        }
        return [ $v ];
    }

    public static function termini_in_anteprima( $termini, $post_id, $tassonomia ) {
        if ( ! self::$anteprima || (int) $post_id !== self::$anteprima['id'] ) {
            return $termini;
        }
        $scelti = self::$anteprima['modulo']['termini'] ?? null;
        if ( ! is_array( $scelti ) || ! array_key_exists( $tassonomia, $scelti ) ) {
            return $termini;
        }
        $a_cartelle = is_taxonomy_hierarchical( $tassonomia );
        $out        = [];
        foreach ( (array) $scelti[ $tassonomia ] as $v ) {
            // I tag nuovi, che nascono salvando, nell'anteprima non ci sono ancora.
            $x = $a_cartelle ? get_term( (int) $v, $tassonomia ) : get_term_by( 'name', (string) $v, $tassonomia );
            if ( $x instanceof WP_Term ) {
                $out[] = $x;
            }
        }
        return $out ? $out : false;
    }

    /* ─────────────────────────────────────────────
     * Versioni
     * ───────────────────────────────────────────── */

    /** Ogni versione salvata tiene la fotografia di campi, categorie e foto (le copie di sicurezza la scrivono da sé). */
    public static function fotografa_revisione( $revisione_id, $post_id = 0 ) {
        if ( wp_is_post_autosave( $revisione_id ) ) {
            return;
        }
        $post = get_post( $post_id ? $post_id : wp_get_post_parent_id( $revisione_id ) );
        if ( ! $post || ! Olobuild_Tipi_Contenuto::del_tipo( $post->post_type ) ) {
            return;
        }
        update_metadata( 'post', $revisione_id, self::META, wp_slash( wp_json_encode( self::modulo_di( $post ) ) ) );
    }

    /**
     * WordPress salva una versione solo se cambiano titolo, testo o riassunto: qui conta anche un campo, una categoria o
     * la foto. L'ultima versione senza fotografia (di prima della 1.4.860) ne prende una alla prima occasione.
     */
    public static function revisione_cambiata( $cambiata, $ultima, $post ) {
        if ( $cambiata || ! $post || ! Olobuild_Tipi_Contenuto::del_tipo( $post->post_type ) ) {
            return $cambiata;
        }
        $prima = self::modulo_salvato( $ultima->ID );
        return ! $prima || ! self::stesso_modulo( $prima, self::modulo_di( $post ) );
    }

    /** Un testo per il confronto: una riga per paragrafo, senza tag; le foto come [Foto]. */
    private static function testo_piano( $html ) {
        $html  = preg_replace( '#<!--.*?-->#s', '', (string) $html );
        $html  = preg_replace( '#<img\b[^>]*>#i', "\n[" . __( 'Foto', 'olobuild' ) . "]\n", $html );
        $html  = preg_replace( '#</(p|h[1-6]|li|blockquote|div|figure)>|<br\s*/?>#i', "\n", $html );
        $righe = array_map( 'trim', explode( "\n", html_entity_decode( wp_strip_all_tags( $html ), ENT_QUOTES, 'UTF-8' ) ) );
        return implode( "\n", array_filter( $righe, 'strlen' ) );
    }

    public static function rest_versioni( WP_REST_Request $r ) {
        $post = get_post( (int) $r['id'] );
        $out  = [];
        foreach ( wp_get_post_revisions( $post->ID, [ 'posts_per_page' => 60 ] ) as $v ) {
            if ( wp_is_post_autosave( $v ) ) {
                continue;
            }
            // La prima è di solito il contenuto com'è adesso (WordPress ne salva una a ogni aggiornamento).
            $adesso = ! $out
                && normalize_whitespace( $v->post_title ) === normalize_whitespace( $post->post_title )
                && normalize_whitespace( $v->post_content ) === normalize_whitespace( $post->post_content )
                && normalize_whitespace( $v->post_excerpt ) === normalize_whitespace( $post->post_excerpt );
            $out[]  = [
                'id'     => $v->ID,
                'quando' => self::quando( get_post_timestamp( $v, 'modified' ) ),
                'autore' => self::nome_utente( $v->post_author ),
                'titolo' => html_entity_decode( $v->post_title, ENT_QUOTES, 'UTF-8' ),
                'adesso' => $adesso,
            ];
        }
        return rest_ensure_response( $out );
    }

    /** Una versione: cosa cambia rispetto a ora (il testo riga per riga, l'elenco del resto) e ciò che serve a riprenderla. */
    public static function rest_versione( WP_REST_Request $r ) {
        $post = get_post( (int) $r['id'] );
        $rid  = (int) $r['versione']; // wp_get_post_revision() vuole una variabile (per riferimento)
        $v    = wp_get_post_revision( $rid );
        if ( ! $v || (int) $v->post_parent !== $post->ID || wp_is_post_autosave( $v ) ) {
            return new WP_Error( 'olo_redazione_versione', __( 'Questa versione non c\'è più.', 'olobuild' ), [ 'status' => 404 ] );
        }
        $motivo = self::motivo_sola_lettura( $post->post_content );
        $m      = self::modulo_salvato( $v->ID );
        $ora    = self::modulo_di( $post );
        $cambia = [];
        if ( normalize_whitespace( $v->post_title ) !== normalize_whitespace( $post->post_title ) ) {
            $cambia[] = __( 'Titolo', 'olobuild' );
        }
        if ( post_type_supports( $post->post_type, 'excerpt' ) && normalize_whitespace( $v->post_excerpt ) !== normalize_whitespace( $post->post_excerpt ) ) {
            $cambia[] = __( 'Riassunto', 'olobuild' );
        }
        if ( $m ) {
            $t = Olobuild_Tipi_Contenuto::del_tipo( $post->post_type );
            foreach ( $t ? $t['campi'] : [] as $c ) {
                if ( (string) ( $m['campi'][ $c['chiave'] ] ?? '' ) !== (string) ( $ora['campi'][ $c['chiave'] ] ?? '' ) ) {
                    $cambia[] = $c['etichetta'];
                }
            }
            foreach ( self::tassonomie( $post->post_type ) as $x ) {
                $a = array_map( 'strval', (array) ( $m['termini'][ $x->name ] ?? [] ) );
                $b = array_map( 'strval', (array) ( $ora['termini'][ $x->name ] ?? [] ) );
                sort( $a );
                sort( $b );
                if ( $a !== $b ) {
                    $cambia[] = $x->labels->name;
                }
            }
            if ( post_type_supports( $post->post_type, 'thumbnail' ) && (int) ( $m['immagine'] ?? 0 ) !== $ora['immagine'] ) {
                $cambia[] = __( 'Immagine in evidenza', 'olobuild' );
            }
        }
        // Il testo: a sinistra la versione, a destra adesso; vuoto = uguale.
        $differenze = (string) wp_text_diff( self::testo_piano( $v->post_content ), self::testo_piano( $post->post_content ), [ 'show_split_view' => false ] );
        return rest_ensure_response( self::da_riprendere( $post, $v, $m, $motivo ) + [
            'id'          => $v->ID,
            'quando'      => self::quando( get_post_timestamp( $v, 'modified' ) ),
            'autore'      => self::nome_utente( $v->post_author ),
            'cambia'      => $cambia,
            'differenze'  => $differenze,
            'fotografia'  => (bool) $m,
            'wpConfronta' => admin_url( 'revision.php?revision=' . $v->ID ),
        ] );
    }

    /* ─────────────────────────────────────────────
     * Social (OLOsocial)
     * ───────────────────────────────────────────── */

    /** OLOsocial c'è, nella versione che lascia leggere e salvare le scelte da un'altra scheda (0.4.0 in su)? */
    private static function social_attivo() {
        return class_exists( 'Olo_Social_Riquadro' ) && method_exists( 'Olo_Social_Riquadro', 'per_la_scheda' )
            && method_exists( 'Olo_Social_Riquadro', 'salva_scelte' );
    }

    /** Dopo l'uscita: «riprova» una rete non riuscita, o «pubblica» la bozza social. Restituisce la sezione aggiornata. */
    public static function rest_social( WP_REST_Request $r ) {
        $id = (int) $r['id'];
        if ( ! self::social_attivo() || null === Olo_Social_Riquadro::per_la_scheda( $id ) ) {
            return new WP_Error( 'olo_redazione_social', __( 'I social non sono attivi per questo contenuto.', 'olobuild' ), [ 'status' => 400 ] );
        }
        $azione = sanitize_key( (string) $r['azione'] );
        $fatto  = false;
        if ( 'riprova' === $azione ) {
            $fatto = Olo_Social_Riquadro::riprova( $id, absint( $r['target'] ) );
        } elseif ( 'pubblica' === $azione ) {
            $fatto = Olo_Social_Riquadro::pubblica_bozza( $id );
        }
        if ( ! $fatto ) {
            return new WP_Error( 'olo_redazione_social', __( 'Non è stato possibile: ricarica la scheda e riprova.', 'olobuild' ), [ 'status' => 409 ] );
        }
        return rest_ensure_response( Olo_Social_Riquadro::per_la_scheda( $id ) );
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
