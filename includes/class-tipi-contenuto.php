<?php
/**
 * Olobuild_Tipi_Contenuto — i tipi di contenuto del sito fatti dalle Impostazioni: Notizie, Comunicati, Rubriche…
 * ognuno con la sua voce nel menu di WordPress, le sue categorie, i suoi campi; e gli Articoli, che si possono
 * rinominare e arricchire di campi.
 *
 * Le impostazioni stanno nell'opzione olobuild_tipi_contenuto (Impostazioni › Tipi di contenuto, rotta
 * olobuild/v1/tipi-contenuto). WordPress fa il resto: ruoli e permessi, revisioni, pubblicazione programmata, cestino.
 * OLObuild li disegna coi template Singolo e Archivio e legge i campi con la fonte dinamica «Campi dei contenuti».
 *
 * I campi si salvano come metadati con la loro chiave così com'è (sottotitolo, allegati…): li leggono anche «Campo
 * personalizzato», «Righe di un campo» e le altre fonti che chiedono una chiave. Foto e allegati sono ID della
 * libreria media (separati da virgola); le foto per i social tengono l'originale e un ritaglio per formato (JSON).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Tipi_Contenuto {

    const OPZIONE = 'olobuild_tipi_contenuto';

    /** Impronta di tipi, indirizzi e tassonomie con cui sono state scritte le regole di riscrittura. */
    const OPZIONE_REGOLE = 'olobuild_tipi_regole';

    const TIPI_CAMPO = [ 'testo', 'testo_lungo', 'formattato', 'numero', 'data', 'ora', 'link', 'scelta', 'sino', 'immagine', 'galleria', 'foto_social', 'testo_social', 'allegati', 'video' ];

    /**
     * A quali reti va un campo «Testo per i social» (OLOsocial): 'tutte' o un elenco di reti di RETI_SOCIALI. Valgono
     * ancora i valori di prima della 1.4.872: 'instagram' e 'altre' (tutte tranne Instagram).
     */
    const RETI_TESTO = [ 'tutte', 'instagram', 'altre' ];

    /** Le reti di OLOsocial (id del connettore => nome), per i campi «Testo per i social» dedicati. */
    const RETI_SOCIALI = [
        'facebook'       => 'Facebook',
        'instagram'      => 'Instagram',
        'linkedin'       => 'LinkedIn',
        'threads'        => 'Threads',
        'x'              => 'X',
        'pinterest'      => 'Pinterest',
        'googlebusiness' => 'Google Business Profile',
        'tumblr'         => 'Tumblr',
        'reddit'         => 'Reddit',
        'telegram'       => 'Telegram',
        'viber'          => 'Viber',
        'discord'        => 'Discord',
        'slack'          => 'Slack',
        'teams'          => 'Microsoft Teams',
        'googlechat'     => 'Google Chat',
        'bluesky'        => 'Bluesky',
        'mastodon'       => 'Mastodon',
        'pixelfed'       => 'Pixelfed',
        'misskey'        => 'Misskey',
        'lemmy'          => 'Lemmy',
        'youtube'        => 'YouTube',
        'vimeo'          => 'Vimeo',
    ];

    /** I formati delle foto per i social: proporzione e misura del ritaglio (mai ingrandito oltre l'originale). */
    const FORMATI_SOCIAL = [
        '191' => [ 'nome' => 'Link (Facebook, LinkedIn)', 'w' => 1200, 'h' => 630 ],
        '45'  => [ 'nome' => 'Post Instagram', 'w' => 1080, 'h' => 1350 ],
        '11'  => [ 'nome' => 'Quadrato', 'w' => 1080, 'h' => 1080 ],
        '916' => [ 'nome' => 'Storie e reel', 'w' => 1080, 'h' => 1920 ],
    ];

    const ICONE = [ 'dashicons-megaphone', 'dashicons-media-document', 'dashicons-admin-post', 'dashicons-format-aside',
        'dashicons-calendar-alt', 'dashicons-portfolio', 'dashicons-art', 'dashicons-format-gallery', 'dashicons-microphone',
        'dashicons-video-alt3', 'dashicons-book', 'dashicons-welcome-learn-more', 'dashicons-building', 'dashicons-location',
        'dashicons-businessperson', 'dashicons-groups', 'dashicons-heart', 'dashicons-star-filled', 'dashicons-awards',
        'dashicons-clipboard', 'dashicons-paperclip', 'dashicons-store', 'dashicons-admin-appearance', 'dashicons-lightbulb' ];

    /** Chiavi che WordPress, WooCommerce o OLObuild usano già: un tipo non le può prendere. */
    const RISERVATI = [ 'post', 'page', 'attachment', 'revision', 'nav_menu_item', 'custom_css', 'customize_changeset',
        'oembed_cache', 'user_request', 'wp_block', 'wp_template', 'wp_template_part', 'wp_global_styles', 'wp_navigation',
        'wp_font_family', 'wp_font_face', 'action', 'author', 'order', 'theme', 'type', 'name', 'term', 'taxonomy',
        'category', 'tag', 'product', 'product_variation', 'shop_order', 'shop_coupon', 'olo_spettacolo', 'olo_tema', 'search', 'feed' ];

    /** @var array|null Le impostazioni normalizzate di questa richiesta. */
    private static $cache = null;

    public static function avvia() {
        add_action( 'init', [ __CLASS__, 'registra' ], 9 );
        add_action( 'wp_loaded', [ __CLASS__, 'regole' ] );
        add_action( 'rest_api_init', [ __CLASS__, 'rotte' ] );
        add_filter( 'use_block_editor_for_post_type', [ __CLASS__, 'editor_a_blocchi' ], 20, 2 );
        add_filter( 'post_type_labels_post', [ __CLASS__, 'etichette_articoli' ] );
        // OLOsocial (riquadro Social della scheda): per ogni rete la foto inquadrata nel suo formato.
        add_filter( 'olo_social_foto_rete', [ __CLASS__, 'foto_per_rete' ], 10, 3 );
        add_filter( 'olo_social_testo_rete', [ __CLASS__, 'testo_per_rete' ], 10, 3 );
        add_filter( 'olo_social_video', [ __CLASS__, 'video_per_social' ], 10, 2 );
        if ( is_admin() ) {
            add_action( 'add_meta_boxes', [ __CLASS__, 'riquadri' ], 10, 2 );
            add_action( 'save_post', [ __CLASS__, 'salva' ], 10, 2 );
            add_action( 'admin_enqueue_scripts', [ __CLASS__, 'script' ] );
            add_filter( 'tiny_mce_before_init', [ __CLASS__, 'barra_semplice' ], 20, 2 );
            add_action( 'admin_menu', [ __CLASS__, 'menu_articoli' ], 99 );
        }
    }

    /* ─────────────────────────────────────────────
     * Impostazioni
     * ───────────────────────────────────────────── */

    /** Le impostazioni normalizzate: [ 'articoli' => [...], 'tipi' => [ [...], … ] ]. */
    public static function impostazioni() {
        if ( null === self::$cache ) {
            self::$cache = self::normalizza( get_option( self::OPZIONE, [] ) );
        }
        return self::$cache;
    }

    /**
     * I tipi creati dalle Impostazioni, più quelli «di sistema» che un modulo di OLObuild aggiunge col filtro
     * olobuild_tipi_di_sistema (le schede dei temi della Vetrina): stessi campi, scheda e fonti dinamiche, ma non si
     * modificano dal pannello e non finiscono nell'opzione. 'pubblico' => false = niente pagina propria.
     */
    public static function tipi() {
        return array_merge( self::impostazioni()['tipi'], self::tipi_di_sistema() );
    }

    private static function tipi_di_sistema() {
        $out = [];
        foreach ( (array) apply_filters( 'olobuild_tipi_di_sistema', [] ) as $t ) {
            if ( ! is_array( $t ) ) {
                continue;
            }
            $chiave = sanitize_key( (string) ( $t['chiave'] ?? '' ) );
            // La normalizzazione dei tipi scarta le chiavi riservate: il tipo di sistema la passa col nome provvisorio.
            $n = self::normalizza( [ 'tipi' => [ array_merge( $t, [ 'chiave' => 'olo_sistema_tmp' ] ) ] ] )['tipi'];
            if ( '' === $chiave || ! $n ) {
                continue;
            }
            $out[] = array_merge( $n[0], [ 'chiave' => $chiave, 'sistema' => true, 'pubblico' => ! isset( $t['pubblico'] ) || ! empty( $t['pubblico'] ) ] );
        }
        return $out;
    }

    /** Le impostazioni di un tipo (gli Articoli compresi, chiave 'post'), o null. */
    public static function del_tipo( $tipo ) {
        $tipo = (string) $tipo;
        if ( 'post' === $tipo ) {
            return self::impostazioni()['articoli'];
        }
        foreach ( self::tipi() as $t ) {
            if ( $t['chiave'] === $tipo ) {
                return $t;
            }
        }
        return null;
    }

    public static function normalizza( $raw ) {
        $raw  = is_array( $raw ) ? $raw : [];
        $art  = is_array( $raw['articoli'] ?? null ) ? $raw['articoli'] : [];
        $out  = [
            'articoli' => [
                'chiave'    => 'post',
                'nome'      => self::testo( $art['nome'] ?? '', 40 ),
                'singolare' => self::testo( $art['singolare'] ?? '', 40 ),
                'editor'    => 'semplice' === ( $art['editor'] ?? '' ) ? 'semplice' : 'blocchi',
                'campi'     => self::normalizza_campi( $art['campi'] ?? [] ),
            ],
            'tipi'     => [],
        ];
        $prese = [];
        foreach ( (array) ( $raw['tipi'] ?? [] ) as $t ) {
            if ( ! is_array( $t ) ) {
                continue;
            }
            $chiave = substr( sanitize_key( (string) ( $t['chiave'] ?? '' ) ), 0, 20 );
            $nome   = self::testo( $t['nome'] ?? '', 40 );
            if ( '' === $chiave && '' !== $nome ) {
                $chiave = substr( str_replace( '-', '_', sanitize_title( $nome ) ), 0, 20 );
            }
            if ( '' === $chiave || '' === $nome || in_array( $chiave, self::RISERVATI, true ) || isset( $prese[ $chiave ] ) ) {
                continue;
            }
            $prese[ $chiave ] = true;
            $singolare        = self::testo( $t['singolare'] ?? '', 40 );
            $indirizzo        = substr( sanitize_title( (string) ( $t['indirizzo'] ?? '' ) ), 0, 40 );
            $icona            = (string) ( $t['icona'] ?? '' );
            $out['tipi'][]    = [
                'chiave'     => $chiave,
                'nome'       => $nome,
                'singolare'  => '' !== $singolare ? $singolare : $nome,
                'indirizzo'  => '' !== $indirizzo ? $indirizzo : sanitize_title( $nome ),
                'icona'      => in_array( $icona, self::ICONE, true ) ? $icona : 'dashicons-admin-post',
                'posizione'  => max( 5, min( 100, absint( $t['posizione'] ?? 21 ) ?: 21 ) ),
                'archivio'   => ! isset( $t['archivio'] ) || ! empty( $t['archivio'] ),
                'categorie'  => ! empty( $t['categorie'] ),
                'tag'        => ! empty( $t['tag'] ),
                'editor'     => 'blocchi' === ( $t['editor'] ?? '' ) ? 'blocchi' : 'semplice',
                'estratto'   => ! isset( $t['estratto'] ) || ! empty( $t['estratto'] ),
                'immagine'   => ! isset( $t['immagine'] ) || ! empty( $t['immagine'] ),
                'autore'     => ! empty( $t['autore'] ),
                'commenti'   => ! empty( $t['commenti'] ),
                'permessi'   => 'propri' === ( $t['permessi'] ?? '' ) ? 'propri' : 'articoli',
                'campi'      => self::normalizza_campi( $t['campi'] ?? [] ),
            ];
        }
        return $out;
    }

    private static function normalizza_campi( $campi ) {
        $out   = [];
        $prese = [];
        foreach ( (array) $campi as $c ) {
            if ( ! is_array( $c ) ) {
                continue;
            }
            $etichetta = self::testo( $c['etichetta'] ?? '', 60 );
            $chiave    = substr( sanitize_key( (string) ( $c['chiave'] ?? '' ) ), 0, 40 );
            if ( '' === $chiave && '' !== $etichetta ) {
                $chiave = substr( str_replace( '-', '_', sanitize_title( $etichetta ) ), 0, 40 );
            }
            $chiave = ltrim( $chiave, '_' );
            $tipo   = (string) ( $c['tipo'] ?? 'testo' );
            if ( '' === $chiave || '' === $etichetta || isset( $prese[ $chiave ] ) || ! in_array( $tipo, self::TIPI_CAMPO, true ) ) {
                continue;
            }
            $prese[ $chiave ] = true;
            $larghezza        = (string) ( $c['larghezza'] ?? 'piena' );
            $categorie        = array_slice( array_values( array_unique( array_filter( array_map( 'absint', (array) ( $c['categorie'] ?? [] ) ) ) ) ), 0, 50 );
            $formati          = array_values( array_intersect( array_map( 'strval', (array) ( $c['formati'] ?? array_keys( self::FORMATI_SOCIAL ) ) ), array_keys( self::FORMATI_SOCIAL ) ) );
            $opzioni          = array_slice( array_values( array_filter( array_map( 'trim', explode( "\n", str_replace( "\r", '', (string) ( $c['opzioni'] ?? '' ) ) ) ), 'strlen' ) ), 0, 50 );
            $out[]            = [
                'chiave'    => $chiave,
                'etichetta' => $etichetta,
                'tipo'      => $tipo,
                'larghezza' => in_array( $larghezza, [ 'piena', 'meta', 'terzo' ], true ) ? $larghezza : 'piena',
                'aiuto'     => self::testo( $c['aiuto'] ?? '', 300 ),
                'opzioni'   => implode( "\n", array_map( 'sanitize_text_field', $opzioni ) ),
                'formati'   => $formati ? $formati : array_keys( self::FORMATI_SOCIAL ),
                'reti'      => self::reti_campo( $c['reti'] ?? 'tutte' ),
                // Vuoto = in tutti i contenuti del tipo; altrimenti solo in quelli di queste categorie (o delle figlie).
                'categorie' => $categorie,
            ];
        }
        return array_slice( $out, 0, 60 );
    }

    private static function testo( $v, $max ) {
        return mb_substr( sanitize_text_field( is_scalar( $v ) ? (string) $v : '' ), 0, $max );
    }

    /** Le reti di un campo «Testo per i social»: un elenco di reti conosciute (nel loro ordine), o 'tutte', 'instagram', 'altre'. */
    private static function reti_campo( $v ) {
        if ( is_array( $v ) ) {
            $v = array_values( array_intersect( array_keys( self::RETI_SOCIALI ), array_map( 'sanitize_key', array_filter( $v, 'is_string' ) ) ) );
            return $v ? $v : 'tutte';
        }
        return in_array( $v, self::RETI_TESTO, true ) ? $v : 'tutte';
    }

    /**
     * Quanto un campo «Testo per i social» è preciso per una rete: meno reti copre, prima vale (null = non vale per lei).
     * Gemello JS: precisione() in src/redazione/Social.vue.
     */
    public static function precisione_rete( $reti, $rete ) {
        if ( is_array( $reti ) ) {
            return in_array( $rete, $reti, true ) ? count( $reti ) : null;
        }
        if ( 'instagram' === $reti ) {
            return 'instagram' === $rete ? 1 : null;
        }
        if ( 'altre' === $reti ) {
            return 'instagram' !== $rete ? 100 : null;
        }
        return 1000;
    }

    /* ─────────────────────────────────────────────
     * Registrazione
     * ───────────────────────────────────────────── */

    public static function registra() {
        foreach ( self::tipi() as $t ) {
            $k     = $t['chiave'];
            $caps  = 'propri' === $t['permessi']
                ? [ 'capability_type' => [ $k, $k . 's' ], 'map_meta_cap' => true ]
                : [ 'capability_type' => 'post', 'map_meta_cap' => true ];
            $tassonomie = [];
            if ( $t['categorie'] ) {
                $tassonomie[] = self::tassonomia( $t, 'categorie' );
            }
            if ( $t['tag'] ) {
                $tassonomie[] = self::tassonomia( $t, 'tag' );
            }
            $supporti = [ 'title', 'editor', 'revisions' ];
            foreach ( [ 'estratto' => 'excerpt', 'immagine' => 'thumbnail', 'autore' => 'author', 'commenti' => 'comments' ] as $chiave => $wp ) {
                if ( $t[ $chiave ] ) {
                    $supporti[] = $wp;
                }
            }
            $pubblico = ! isset( $t['pubblico'] ) || $t['pubblico'];
            register_post_type( $k, array_merge( $caps, [
                'labels'        => self::etichette( $t['nome'], $t['singolare'] ),
                'public'        => true,
                // Senza pagina propria (le schede dei temi): niente indirizzi né ricerca, la scheda resta in bacheca.
                'publicly_queryable'  => $pubblico,
                'exclude_from_search' => ! $pubblico,
                'show_in_rest'  => true,
                'menu_position' => $t['posizione'],
                'menu_icon'     => $t['icona'],
                'supports'      => $supporti,
                'has_archive'   => $t['archivio'] ? $t['indirizzo'] : false,
                'rewrite'       => [ 'slug' => $t['indirizzo'], 'with_front' => false ],
                'taxonomies'    => $tassonomie,
            ] ) );
            if ( $t['categorie'] ) {
                self::registra_tassonomia( $t, 'categorie' );
            }
            if ( $t['tag'] ) {
                self::registra_tassonomia( $t, 'tag' );
            }
        }
    }

    /** Il nome della tassonomia di un tipo: notizia_cat, notizia_tag. */
    public static function tassonomia( $t, $quale ) {
        return substr( $t['chiave'], 0, 20 ) . ( 'tag' === $quale ? '_tag' : '_cat' );
    }

    private static function registra_tassonomia( $t, $quale ) {
        $cat    = 'categorie' === $quale;
        $plur   = $t['chiave'] . 's';
        $capi   = 'propri' === $t['permessi']
            ? [ 'manage_terms' => 'manage_categories', 'edit_terms' => 'manage_categories', 'delete_terms' => 'manage_categories', 'assign_terms' => 'edit_' . $plur ]
            : [ 'manage_terms' => 'manage_categories', 'edit_terms' => 'manage_categories', 'delete_terms' => 'manage_categories', 'assign_terms' => 'edit_posts' ];
        register_taxonomy( self::tassonomia( $t, $quale ), [ $t['chiave'] ], [
            'labels'            => [
                'name'          => $cat ? __( 'Categorie', 'olobuilder' ) : __( 'Tag', 'olobuilder' ),
                'singular_name' => $cat ? __( 'Categoria', 'olobuilder' ) : __( 'Tag', 'olobuilder' ),
                'menu_name'     => $cat ? __( 'Categorie', 'olobuilder' ) : __( 'Tag', 'olobuilder' ),
                'all_items'     => $cat ? __( 'Tutte le categorie', 'olobuilder' ) : __( 'Tutti i tag', 'olobuilder' ),
                'add_new_item'  => $cat ? __( 'Aggiungi una categoria', 'olobuilder' ) : __( 'Aggiungi un tag', 'olobuilder' ),
                'parent_item'   => __( 'Categoria madre', 'olobuilder' ),
                'search_items'  => $cat ? __( 'Cerca nelle categorie', 'olobuilder' ) : __( 'Cerca nei tag', 'olobuilder' ),
                'not_found'     => $cat ? __( 'Nessuna categoria.', 'olobuilder' ) : __( 'Nessun tag.', 'olobuilder' ),
            ],
            'hierarchical'      => $cat,
            'public'            => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'capabilities'      => $capi,
            // categoria-notizie/sport/, tag-notizie/derby/: accanto al tipo, senza scontrarsi coi suoi contenuti.
            'rewrite'           => [ 'slug' => ( $cat ? 'categoria-' : 'tag-' ) . $t['indirizzo'], 'with_front' => false, 'hierarchical' => $cat ],
        ] );
    }

    private static function etichette( $nome, $singolare ) {
        /* translators: %s: nome singolare del tipo di contenuto (Notizia) */
        $agg = sprintf( __( 'Aggiungi: %s', 'olobuilder' ), $singolare );
        return [
            'name'                  => $nome,
            'singular_name'         => $singolare,
            'menu_name'             => $nome,
            /* translators: %s: nome plurale del tipo di contenuto (Notizie) */
            'all_items'             => sprintf( __( 'Tutte: %s', 'olobuilder' ), $nome ),
            'add_new'               => $agg,
            'add_new_item'          => $agg,
            /* translators: %s: nome singolare del tipo di contenuto */
            'edit_item'             => sprintf( __( 'Modifica: %s', 'olobuilder' ), $singolare ),
            /* translators: %s: nome singolare del tipo di contenuto */
            'new_item'              => sprintf( __( 'Nuovo: %s', 'olobuilder' ), $singolare ),
            /* translators: %s: nome singolare del tipo di contenuto */
            'view_item'             => sprintf( __( 'Vedi: %s', 'olobuilder' ), $singolare ),
            /* translators: %s: nome plurale del tipo di contenuto */
            'view_items'            => sprintf( __( 'Vedi: %s', 'olobuilder' ), $nome ),
            /* translators: %s: nome plurale del tipo di contenuto */
            'search_items'          => sprintf( __( 'Cerca: %s', 'olobuilder' ), $nome ),
            'not_found'             => __( 'Ancora niente qui.', 'olobuilder' ),
            'not_found_in_trash'    => __( 'Niente nel cestino.', 'olobuilder' ),
            /* translators: %s: nome plurale del tipo di contenuto */
            'archives'              => sprintf( __( 'Archivio: %s', 'olobuilder' ), $nome ),
            'item_published'        => __( 'Pubblicato.', 'olobuilder' ),
            'item_updated'          => __( 'Aggiornato.', 'olobuilder' ),
            'item_scheduled'        => __( 'Programmato.', 'olobuilder' ),
        ];
    }

    /** Gli Articoli col nome scelto nelle Impostazioni (Notizie, Blog…): tutte le etichette, non solo il menu. */
    public static function etichette_articoli( $labels ) {
        $a = self::impostazioni()['articoli'];
        if ( '' === $a['nome'] ) {
            return $labels;
        }
        $nuove = self::etichette( $a['nome'], '' !== $a['singolare'] ? $a['singolare'] : $a['nome'] );
        foreach ( $nuove as $k => $v ) {
            $labels->$k = $v;
        }
        $labels->name_admin_bar = $labels->singular_name;
        return $labels;
    }

    /** Il menu laterale prende il nome degli Articoli prima che le etichette arrivino (admin_menu le ha già lette). */
    public static function menu_articoli() {
        global $menu;
        $a = self::impostazioni()['articoli'];
        if ( '' === $a['nome'] || ! is_array( $menu ) ) {
            return;
        }
        foreach ( $menu as $i => $voce ) {
            if ( isset( $voce[2] ) && 'edit.php' === $voce[2] ) {
                $menu[ $i ][0] = esc_html( $a['nome'] ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- rinomina la voce «Articoli» del menu laterale
            }
        }
    }

    /**
     * Le regole di riscrittura si riscrivono una volta, quando cambiano tipi, indirizzi o tassonomie: su wp_loaded, dopo
     * che tutti i plugin hanno registrato i loro tipi (a init le loro pagine sparirebbero).
     */
    public static function regole() {
        $impronta = md5( wp_json_encode( array_map( function ( $t ) {
            return [ $t['chiave'], $t['indirizzo'], $t['archivio'], $t['categorie'], $t['tag'] ];
        }, self::tipi() ) ) );
        if ( get_option( self::OPZIONE_REGOLE ) !== $impronta ) {
            update_option( self::OPZIONE_REGOLE, $impronta, false );
            flush_rewrite_rules( false );
        }
    }

    /**
     * Permessi propri: amministratori e redattori possono tutto sul tipo, e il ruolo «Redazione <nome>» scrive,
     * pubblica e corregge solo quello (più la libreria media). Con i permessi degli Articoli il ruolo sparisce.
     */
    public static function aggiorna_ruoli( $prima, $dopo ) {
        $restano = [];
        foreach ( $dopo['tipi'] as $t ) {
            if ( 'propri' !== $t['permessi'] ) {
                continue;
            }
            $restano[ $t['chiave'] ] = true;
            $capi = self::capacita( $t['chiave'] );
            foreach ( [ 'administrator', 'editor' ] as $r ) {
                $ruolo = get_role( $r );
                if ( $ruolo ) {
                    foreach ( $capi as $c ) {
                        $ruolo->add_cap( $c );
                    }
                }
            }
            $nome  = 'olo_redazione_' . $t['chiave'];
            /* translators: %s: nome plurale del tipo di contenuto */
            $label = sprintf( __( 'Redazione %s', 'olobuilder' ), $t['nome'] );
            remove_role( $nome );
            add_role( $nome, $label, array_fill_keys( array_merge( [ 'read', 'upload_files' ], $capi ), true ) );
        }
        foreach ( (array) ( $prima['tipi'] ?? [] ) as $t ) {
            if ( empty( $restano[ $t['chiave'] ] ) ) {
                remove_role( 'olo_redazione_' . $t['chiave'] );
            }
        }
    }

    private static function capacita( $k ) {
        $p = $k . 's';
        return [ "edit_{$p}", "edit_others_{$p}", "edit_published_{$p}", "edit_private_{$p}", "publish_{$p}",
            "delete_{$p}", "delete_others_{$p}", "delete_published_{$p}", "delete_private_{$p}", "read_private_{$p}" ];
    }

    /* ─────────────────────────────────────────────
     * Editor semplice
     * ───────────────────────────────────────────── */

    /** «Editor semplice»: l'editor visuale classico al posto di quello a blocchi. */
    public static function editor_a_blocchi( $usa, $tipo ) {
        $t = self::del_tipo( $tipo );
        return ( $t && 'semplice' === $t['editor'] ) ? false : $usa;
    }

    /** La barra dell'editor semplice: titoli, grassetto, corsivo, link, elenchi, citazione. Il resto non serve a chi scrive. */
    public static function barra_semplice( $init, $editor_id = '' ) {
        if ( 'content' !== $editor_id || ! function_exists( 'get_current_screen' ) ) {
            return $init;
        }
        $schermo = get_current_screen();
        $t       = $schermo ? self::del_tipo( $schermo->post_type ) : null;
        if ( ! $t || 'semplice' !== $t['editor'] ) {
            return $init;
        }
        $init['toolbar1']      = 'formatselect,bold,italic,link,unlink,bullist,numlist,blockquote,undo,redo';
        $init['toolbar2']      = '';
        $init['block_formats'] = __( 'Paragrafo', 'olobuilder' ) . '=p;' . __( 'Titolo', 'olobuilder' ) . '=h2;' . __( 'Sottotitolo', 'olobuilder' ) . '=h3';
        return $init;
    }

    /* ─────────────────────────────────────────────
     * Campi nella scheda
     * ───────────────────────────────────────────── */

    public static function riquadri( $tipo, $post = null ) {
        $t = self::del_tipo( $tipo );
        if ( ! $t || ! $t['campi'] ) {
            return;
        }
        add_meta_box( 'olobuild-campi', __( 'Dettagli', 'olobuilder' ), [ __CLASS__, 'riquadro' ], $tipo, 'normal', 'high', [ 'tipo' => $t ] );
    }

    public static function riquadro( $post, $box ) {
        $t = $box['args']['tipo'];
        wp_nonce_field( 'olobuild_campi', 'olobuild_campi_nonce' );
        echo '<div class="olo-tc">';
        foreach ( $t['campi'] as $c ) {
            self::campo_html( $c, get_post_meta( $post->ID, $c['chiave'], true ), $post );
        }
        echo '</div>';
    }

    private static function campo_html( $c, $v, $post = null ) {
        $k      = $c['chiave'];
        $id     = 'olo-tc-' . $k;
        $nome   = 'olo_tc[' . $k . ']';
        $classe = 'olo-tc-campo olo-tc--' . $c['larghezza'] . ' olo-tc--' . $c['tipo'];
        $v      = is_scalar( $v ) ? (string) $v : '';
        // «Solo nelle categorie»: assets/js/olo-tipi-categorie.js lo mostra e lo nasconde mentre si spuntano le categorie.
        $tax    = ( $post && ! empty( $c['categorie'] ) ) ? self::tassonomia_categorie( $post->post_type ) : '';
        $dati   = '';
        if ( '' !== $tax ) {
            $x     = get_taxonomy( $tax );
            $dati  = ' data-tassonomia="' . esc_attr( $tax ) . '" data-rest="' . esc_attr( ( $x && $x->rest_base ) ? $x->rest_base : $tax ) . '"'
                . ' data-categorie="' . esc_attr( implode( ',', self::con_figlie( $tax, $c['categorie'] ) ) ) . '"'
                . ( self::campo_vale( $c, $post->ID ) ? '' : ' hidden' );
        }
        echo '<div class="' . esc_attr( $classe ) . '"' . $dati . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributi già protetti qui sopra
        if ( 'sino' !== $c['tipo'] ) {
            echo '<label class="olo-tc-etichetta" for="' . esc_attr( $id ) . '">' . esc_html( $c['etichetta'] ) . '</label>';
        }
        switch ( $c['tipo'] ) {
            case 'testo_lungo':
            case 'testo_social':
                echo '<textarea id="' . esc_attr( $id ) . '" name="' . esc_attr( $nome ) . '" rows="4">' . esc_textarea( $v ) . '</textarea>';
                break;
            case 'formattato':
                wp_editor( $v, 'olotc' . preg_replace( '/[^a-z0-9]/', '', $k ), [
                    'textarea_name' => $nome,
                    'media_buttons' => false,
                    'teeny'         => true,
                    'textarea_rows' => 6,
                    'quicktags'     => false,
                ] );
                break;
            case 'scelta':
                echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $nome ) . '"><option value="">—</option>';
                foreach ( explode( "\n", $c['opzioni'] ) as $o ) {
                    echo '<option value="' . esc_attr( $o ) . '"' . selected( $v, $o, false ) . '>' . esc_html( $o ) . '</option>';
                }
                echo '</select>';
                break;
            case 'sino':
                echo '<label class="olo-tc-sino"><input type="checkbox" id="' . esc_attr( $id ) . '" name="' . esc_attr( $nome ) . '" value="1"' . checked( '1', $v, false ) . '> ' . esc_html( $c['etichetta'] ) . '</label>';
                break;
            case 'immagine':
            case 'galleria':
            case 'allegati':
                $multi = 'immagine' !== $c['tipo'];
                echo '<div class="olo-tc-media" data-tipo="' . esc_attr( $c['tipo'] ) . '">';
                echo '<input type="hidden" id="' . esc_attr( $id ) . '" name="' . esc_attr( $nome ) . '" value="' . esc_attr( $v ) . '">';
                echo '<ul class="olo-tc-voci">';
                foreach ( array_filter( array_map( 'absint', explode( ',', $v ) ) ) as $aid ) {
                    self::voce_media( $aid, $c['tipo'] );
                }
                echo '</ul><button type="button" class="button olo-tc-scegli">'
                    . esc_html( 'allegati' === $c['tipo'] ? __( 'Aggiungi allegati', 'olobuilder' ) : ( $multi ? __( 'Aggiungi foto', 'olobuilder' ) : __( 'Scegli la foto', 'olobuilder' ) ) )
                    . '</button></div>';
                break;
            case 'video':
                // Un video della libreria (il suo ID) o un link: YouTube, Vimeo o un file .mp4 su un altro sito.
                $aid = ctype_digit( $v ) ? (int) $v : 0;
                echo '<div class="olo-tc-video">';
                echo '<input type="hidden" name="' . esc_attr( $nome ) . '" value="' . esc_attr( $v ) . '">';
                echo '<span class="olo-tc-file olo-tc-video-file"' . ( $aid ? '' : ' hidden' ) . '>' . esc_html( $aid ? get_the_title( $aid ) : '' ) . '</span>';
                echo '<input type="url" id="' . esc_attr( $id ) . '" class="olo-tc-video-link" value="' . esc_attr( $aid ? '' : $v ) . '" placeholder="https://www.youtube.com/watch?v=…"' . ( $aid ? ' hidden' : '' ) . '>';
                echo '<span class="olo-tc-video-azioni"><button type="button" class="button olo-tc-video-scegli">' . esc_html__( 'Scegli dalla libreria', 'olobuilder' ) . '</button>';
                echo '<button type="button" class="button-link olo-tc-video-togli"' . ( '' === $v ? ' hidden' : '' ) . '>' . esc_html__( 'Togli', 'olobuilder' ) . '</button></span>';
                echo '</div>';
                break;
            case 'foto_social':
                $dati = json_decode( $v, true );
                $dati = is_array( $dati ) ? $dati : [];
                echo '<div class="olo-tc-social">';
                echo '<input type="hidden" id="' . esc_attr( $id ) . '" name="' . esc_attr( $nome ) . '" value="' . esc_attr( $v ) . '">';
                echo '<div class="olo-tc-sorgente">';
                $src = absint( $dati['sorgente'] ?? 0 );
                echo $src ? wp_get_attachment_image( $src, 'thumbnail' ) : '<span class="olo-tc-vuoto"></span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup di wp_get_attachment_image()
                echo '<button type="button" class="button olo-tc-originale">' . esc_html__( 'Scegli la foto', 'olobuilder' ) . '</button></div><ul class="olo-tc-formati">';
                foreach ( $c['formati'] as $f ) {
                    $fmt = self::FORMATI_SOCIAL[ $f ];
                    $rit = absint( $dati[ $f ] ?? 0 );
                    echo '<li data-formato="' . esc_attr( $f ) . '" data-w="' . (int) $fmt['w'] . '" data-h="' . (int) $fmt['h'] . '" style="--olo-tc-r:' . (int) $fmt['w'] . '/' . (int) $fmt['h'] . '">';
                    echo '<span class="olo-tc-anteprima">' . ( $rit ? wp_get_attachment_image( $rit, 'medium' ) : ( $src ? wp_get_attachment_image( $src, 'medium' ) : '' ) ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup di wp_get_attachment_image()
                    echo '<span class="olo-tc-formato">' . esc_html( __( $fmt['nome'], 'olobuilder' ) ) . ' · ' . (int) $fmt['w'] . '×' . (int) $fmt['h'] . '</span>'; // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText -- i nomi dei formati sono nel catalogo
                    echo '<span class="olo-tc-stato">' . esc_html( $rit ? __( 'Inquadrata', 'olobuilder' ) : __( 'Centro della foto', 'olobuilder' ) ) . '</span>';
                    echo '<button type="button" class="button button-small olo-tc-inquadra"' . ( $src ? '' : ' disabled' ) . '>' . esc_html__( 'Inquadra', 'olobuilder' ) . '</button></li>';
                }
                echo '</ul></div>';
                break;
            default:
                $tipi = [ 'numero' => 'number', 'data' => 'date', 'ora' => 'time', 'link' => 'url' ];
                $tipo = $tipi[ $c['tipo'] ] ?? 'text';
                echo '<input type="' . esc_attr( $tipo ) . '" id="' . esc_attr( $id ) . '" name="' . esc_attr( $nome ) . '" value="' . esc_attr( $v ) . '"' . ( 'number' === $tipo ? ' step="any"' : '' ) . '>';
        }
        if ( '' !== $c['aiuto'] ) {
            echo '<p class="description">' . esc_html( $c['aiuto'] ) . '</p>';
        }
        echo '</div>';
    }

    /** Una voce di foto, galleria o allegati nella scheda (anche come modello per lo script). */
    private static function voce_media( $aid, $tipo ) {
        $post = get_post( $aid );
        if ( ! $post || 'attachment' !== $post->post_type ) {
            return;
        }
        echo '<li data-id="' . (int) $aid . '">';
        if ( 'allegati' === $tipo ) {
            $file = get_attached_file( $aid );
            $peso = $file && file_exists( $file ) ? size_format( filesize( $file ) ) : '';
            echo '<span class="olo-tc-file">' . esc_html( get_the_title( $aid ) ) . ' <em>' . esc_html( strtoupper( (string) pathinfo( (string) $file, PATHINFO_EXTENSION ) ) . ( $peso ? ' · ' . $peso : '' ) ) . '</em></span>';
        } else {
            echo wp_get_attachment_image( $aid, 'thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup di wp_get_attachment_image()
        }
        echo '<button type="button" class="olo-tc-su" aria-label="' . esc_attr__( 'Sposta prima', 'olobuilder' ) . '">↑</button>';
        echo '<button type="button" class="olo-tc-togli" aria-label="' . esc_attr__( 'Togli', 'olobuilder' ) . '">×</button></li>';
    }

    public static function salva( $post_id, $post ) {
        $nonce = isset( $_POST['olobuild_campi_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['olobuild_campi_nonce'] ) ) : '';
        if ( '' === $nonce || ! wp_verify_nonce( $nonce, 'olobuild_campi' ) ) {
            return;
        }
        if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        $t = self::del_tipo( $post->post_type );
        if ( ! $t ) {
            return;
        }
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- ogni valore passa da pulisci() in salva_campi()
        $in = ( isset( $_POST['olo_tc'] ) && is_array( $_POST['olo_tc'] ) ) ? wp_unslash( $_POST['olo_tc'] ) : [];
        self::salva_campi( $post_id, $t, $in );
    }

    /** I campi del tipo $t con i valori di $in (chiave => valore), ripuliti; un campo vuoto si toglie. Anche dalla Redazione. */
    public static function salva_campi( $post_id, $t, $in ) {
        foreach ( $t['campi'] as $c ) {
            $v = self::pulisci( $c, $in[ $c['chiave'] ] ?? '' );
            if ( '' === $v ) {
                delete_post_meta( $post_id, $c['chiave'] );
            } else {
                update_post_meta( $post_id, $c['chiave'], wp_slash( $v ) );
            }
        }
    }

    /** Il valore di un campo come va salvato ('' = niente). */
    public static function pulisci( $c, $v ) {
        $v = is_scalar( $v ) ? (string) $v : '';
        switch ( $c['tipo'] ) {
            case 'testo_lungo':
            case 'testo_social':
                return sanitize_textarea_field( $v );
            case 'formattato':
                return trim( wp_kses_post( $v ) );
            case 'numero':
                return is_numeric( $v ) ? (string) ( 0 + $v ) : '';
            case 'data':
                return preg_match( '/^\d{4}-\d{2}-\d{2}$/', $v ) ? $v : '';
            case 'ora':
                return preg_match( '/^\d{2}:\d{2}$/', $v ) ? $v : '';
            case 'link':
                return esc_url_raw( $v );
            case 'scelta':
                return in_array( $v, explode( "\n", $c['opzioni'] ), true ) ? $v : '';
            case 'sino':
                return '1' === $v ? '1' : '';
            case 'immagine':
            case 'galleria':
            case 'allegati':
                $ids = [];
                foreach ( array_filter( array_map( 'absint', explode( ',', $v ) ) ) as $aid ) {
                    if ( 'attachment' === get_post_type( $aid ) && ( 'allegati' === $c['tipo'] || wp_attachment_is_image( $aid ) ) ) {
                        $ids[] = $aid;
                    }
                }
                $ids = array_values( array_unique( $ids ) );
                return implode( ',', 'immagine' === $c['tipo'] ? array_slice( $ids, 0, 1 ) : array_slice( $ids, 0, 100 ) );
            case 'foto_social':
                $dati = json_decode( $v, true );
                $out  = [];
                foreach ( array_merge( [ 'sorgente' ], array_keys( self::FORMATI_SOCIAL ) ) as $chiave ) {
                    $aid = absint( is_array( $dati ) ? ( $dati[ $chiave ] ?? 0 ) : 0 );
                    if ( $aid && wp_attachment_is_image( $aid ) ) {
                        $out[ $chiave ] = $aid;
                    }
                }
                return empty( $out['sorgente'] ) ? '' : (string) wp_json_encode( $out );
            case 'video':
                $v = trim( $v );
                if ( ctype_digit( $v ) ) {
                    return wp_attachment_is( 'video', (int) $v ) ? (string) (int) $v : '';
                }
                return preg_match( '~^https?://~i', $v ) ? esc_url_raw( $v ) : '';
            default:
                return sanitize_text_field( $v );
        }
    }

    /** L'indirizzo del video di un campo Video: il file della libreria o il link così com'è ('' = nessuno). */
    public static function indirizzo_video( $v ) {
        $v = trim( (string) $v );
        if ( ctype_digit( $v ) ) {
            return (string) wp_get_attachment_url( (int) $v );
        }
        return $v;
    }

    public static function script( $hook ) {
        if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) || ! function_exists( 'get_current_screen' ) ) {
            return;
        }
        $schermo = get_current_screen();
        $t       = $schermo ? self::del_tipo( $schermo->post_type ) : null;
        if ( ! $t || ! $t['campi'] ) {
            return;
        }
        wp_enqueue_style( 'olobuild-tipi-contenuto', OLOBUILD_URL . 'assets/css/olo-tipi-contenuto.css', [], OLOBUILD_VERSION );
        if ( array_filter( array_column( $t['campi'], 'categorie' ) ) ) {
            wp_enqueue_script( 'olobuild-tipi-categorie', OLOBUILD_URL . 'assets/js/olo-tipi-categorie.js', [], OLOBUILD_VERSION, true );
        }
        $media = array_intersect( array_column( $t['campi'], 'tipo' ), [ 'immagine', 'galleria', 'allegati', 'foto_social', 'video' ] );
        if ( ! $media ) {
            return;
        }
        wp_enqueue_media();
        wp_enqueue_script( 'imgareaselect' );
        wp_enqueue_style( 'imgareaselect' );
        wp_enqueue_script( 'olobuild-tipi-contenuto', OLOBUILD_URL . 'assets/js/olo-tipi-contenuto.js', [ 'jquery', 'media-views', 'imgareaselect' ], OLOBUILD_VERSION, true );
        wp_localize_script( 'olobuild-tipi-contenuto', 'oloTipiContenuto', [
            'testi' => [
                'foto'        => __( 'Scegli la foto', 'olobuilder' ),
                'fotoMolte'   => __( 'Scegli le foto', 'olobuilder' ),
                'allegati'    => __( 'Scegli gli allegati', 'olobuilder' ),
                'video'       => __( 'Scegli il video', 'olobuilder' ),
                'usa'         => __( 'Usa', 'olobuilder' ),
                'inquadra'    => __( 'Inquadra', 'olobuilder' ),
                'inquadrata'  => __( 'Inquadrata', 'olobuilder' ),
                'centro'      => __( 'Centro della foto', 'olobuilder' ),
                'su'          => __( 'Sposta prima', 'olobuilder' ),
                'togli'       => __( 'Togli', 'olobuilder' ),
                'errore'      => __( 'Ritaglio non riuscito: riprova.', 'olobuilder' ),
            ],
        ] );
    }

    /* ─────────────────────────────────────────────
     * Campi per OLObuild (sorgente dinamica «Campi dei contenuti»)
     * ───────────────────────────────────────────── */

    /** Le voci della fonte dinamica: un campo per voce, con il tipo da cui viene; le foto social, un formato per voce. */
    public static function campi_dinamici() {
        $voci  = [];
        $gruppi = array_merge( [ self::impostazioni()['articoli'] ], self::tipi() );
        foreach ( $gruppi as $t ) {
            $nome = 'post' === $t['chiave'] ? ( '' !== $t['nome'] ? $t['nome'] : __( 'Articoli', 'olobuilder' ) ) : $t['nome'];
            foreach ( $t['campi'] as $c ) {
                $tipo = in_array( $c['tipo'], [ 'immagine', 'galleria', 'foto_social' ], true ) ? 'image' : ( in_array( $c['tipo'], [ 'link', 'video' ], true ) ? 'url' : ( 'formattato' === $c['tipo'] ? 'html' : 'text' ) );
                if ( 'foto_social' === $c['tipo'] ) {
                    foreach ( $c['formati'] as $f ) {
                        $voci[] = [ 'key' => $c['chiave'] . ':' . $f, 'label' => $nome . ' › ' . $c['etichetta'] . ' (' . __( self::FORMATI_SOCIAL[ $f ]['nome'], 'olobuilder' ) . ')', 'type' => 'image' ]; // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText -- i nomi dei formati sono nel catalogo
                    }
                    continue;
                }
                $voci[] = [ 'key' => $c['chiave'], 'label' => $nome . ' › ' . $c['etichetta'], 'type' => $tipo ];
            }
        }
        return $voci;
    }

    /**
     * Il valore di un campo da mostrare: testo, HTML (formattato), indirizzo di una foto, data nel formato del sito, elenco
     * degli allegati. «chiave:formato» per una foto social (il ritaglio, o l'originale se non è stata inquadrata).
     */
    public static function campo( $campo, $post_id = 0 ) {
        $post_id = $post_id ? (int) $post_id : (int) get_the_ID();
        list( $chiave, $formato ) = array_pad( explode( ':', (string) $campo, 2 ), 2, '' );
        $t = self::del_tipo( get_post_type( $post_id ) );
        $c = null;
        foreach ( $t ? $t['campi'] : [] as $x ) {
            if ( $x['chiave'] === $chiave ) {
                $c = $x;
            }
        }
        if ( $c && ! self::campo_vale( $c, $post_id ) ) {
            return '';
        }
        $v = get_post_meta( $post_id, sanitize_key( $chiave ), true );
        if ( ! $c || '' === $v || null === $v ) {
            return is_scalar( $v ) ? (string) $v : '';
        }
        switch ( $c['tipo'] ) {
            case 'formattato':
                return wpautop( (string) $v );
            case 'data':
                $ts = strtotime( (string) $v );
                return $ts ? date_i18n( get_option( 'date_format' ), $ts ) : '';
            case 'sino':
                return '1' === (string) $v ? __( 'Sì', 'olobuilder' ) : '';
            case 'immagine':
            case 'galleria':
                $aid = absint( strtok( (string) $v, ',' ) );
                return $aid ? (string) wp_get_attachment_image_url( $aid, 'large' ) : '';
            case 'foto_social':
                $dati = json_decode( (string) $v, true );
                $aid  = absint( is_array( $dati ) ? ( $dati[ $formato ] ?? ( $dati['sorgente'] ?? 0 ) ) : 0 );
                return $aid ? (string) wp_get_attachment_image_url( $aid, 'full' ) : '';
            case 'allegati':
                $nomi = [];
                foreach ( array_filter( array_map( 'absint', explode( ',', (string) $v ) ) ) as $aid ) {
                    $nomi[] = get_the_title( $aid );
                }
                return implode( ', ', $nomi );
            case 'video':
                // L'indirizzo che la tile Video sa mostrare: il file (.mp4) o il link di YouTube e Vimeo.
                return self::indirizzo_video( $v );
            default:
                return (string) $v;
        }
    }

    /**
     * OLOsocial: la foto di un contenuto per una rete, dal primo campo «Foto per i social» del suo tipo che ne ha una. Il
     * ritaglio nel formato che la rete preferisce (Instagram 4:5, le altre 1,91:1), poi gli altri formati, poi
     * l'originale. Senza il campo resta la foto che OLOsocial ha già (l'immagine in evidenza).
     */
    public static function foto_per_rete( $foto, $post_id, $rete ) {
        $t = self::del_tipo( get_post_type( (int) $post_id ) );
        foreach ( $t ? $t['campi'] : [] as $c ) {
            if ( 'foto_social' !== $c['tipo'] || ! self::campo_vale( $c, $post_id ) ) {
                continue;
            }
            $dati = json_decode( (string) get_post_meta( (int) $post_id, $c['chiave'], true ), true );
            if ( ! is_array( $dati ) || empty( $dati['sorgente'] ) ) {
                continue;
            }
            foreach ( 'instagram' === $rete ? [ '45', '11', '191' ] : [ '191', '11', '45' ] as $f ) {
                if ( ! empty( $dati[ $f ] ) ) {
                    return (int) $dati[ $f ];
                }
            }
            return (int) $dati['sorgente'];
        }
        return $foto;
    }

    /**
     * OLOsocial: il video da caricare su YouTube e Vimeo, dal primo campo Video del contenuto che ha un file della libreria
     * (un link a YouTube o Vimeo non si ricarica). Senza, resta quello che OLOsocial ha già (nessuno).
     */
    public static function video_per_social( $video, $post_id ) {
        $t = self::del_tipo( get_post_type( (int) $post_id ) );
        foreach ( $t ? $t['campi'] : [] as $c ) {
            if ( 'video' !== $c['tipo'] || ! self::campo_vale( $c, $post_id ) ) {
                continue;
            }
            $v = trim( (string) get_post_meta( (int) $post_id, $c['chiave'], true ) );
            if ( ctype_digit( $v ) && wp_attachment_is( 'video', (int) $v ) ) {
                return (int) $v;
            }
        }
        return $video;
    }

    /* ─────────────────────────────────────────────
     * Campi «Solo nelle categorie»
     * ───────────────────────────────────────────── */

    /** La tassonomia delle categorie di un tipo ('category' per gli Articoli), '' se il tipo non ne ha. */
    public static function tassonomia_categorie( $tipo ) {
        if ( 'post' === $tipo ) {
            return 'category';
        }
        $t = self::del_tipo( $tipo );
        if ( ! $t || empty( $t['categorie'] ) ) {
            return '';
        }
        $x = self::tassonomia( $t, 'categorie' );
        return taxonomy_exists( $x ) ? $x : '';
    }

    /** Le categorie scelte con tutte le loro figlie: un campo per «Eventi» vale anche in «Eventi › Concerti». */
    public static function con_figlie( $tassonomia, $ids ) {
        $out = array_map( 'absint', (array) $ids );
        foreach ( $out as $id ) {
            $figlie = get_term_children( $id, $tassonomia );
            if ( is_array( $figlie ) ) {
                $out = array_merge( $out, array_map( 'intval', $figlie ) );
            }
        }
        return array_values( array_unique( array_filter( $out ) ) );
    }

    /**
     * Un campo con «Solo nelle categorie» vale per un contenuto solo se il contenuto sta in una di loro (o in una
     * figlia): altrimenti la scheda non lo mostra e il sito, OLOsocial compreso, non lo usa. Il valore resta salvato e
     * torna se il contenuto torna in quella categoria.
     */
    public static function campo_vale( $c, $post_id ) {
        if ( empty( $c['categorie'] ) ) {
            return true;
        }
        $tax = self::tassonomia_categorie( (string) get_post_type( (int) $post_id ) );
        return '' === $tax || has_term( self::con_figlie( $tax, $c['categorie'] ), $tax, (int) $post_id );
    }

    /** Come campo_vale(), dalla chiave: un valore che non è un campo del tipo vale sempre. */
    public static function chiave_vale( $chiave, $post_id ) {
        $t = self::del_tipo( (string) get_post_type( (int) $post_id ) );
        foreach ( $t ? $t['campi'] : [] as $c ) {
            if ( $c['chiave'] === $chiave ) {
                return self::campo_vale( $c, $post_id );
            }
        }
        return true;
    }

    /** Per le Impostazioni: le categorie di ogni tipo, col percorso (Eventi › Concerti), da scegliere sui campi. */
    private static function categorie_per_tipo() {
        $out = [];
        foreach ( array_merge( [ self::impostazioni()['articoli'] ], self::impostazioni()['tipi'] ) as $t ) {
            $tax = self::tassonomia_categorie( $t['chiave'] );
            if ( '' === $tax ) {
                continue;
            }
            $termini = get_terms( [ 'taxonomy' => $tax, 'hide_empty' => false, 'number' => 500 ] );
            if ( ! is_array( $termini ) || ! $termini ) {
                continue;
            }
            $per_id = [];
            foreach ( $termini as $x ) {
                $per_id[ $x->term_id ] = $x;
            }
            $voci = [];
            foreach ( $termini as $x ) {
                $nomi  = [ $x->name ];
                $padre = (int) $x->parent;
                $giri  = 0;
                while ( $padre && isset( $per_id[ $padre ] ) && $giri++ < 10 ) {
                    array_unshift( $nomi, $per_id[ $padre ]->name );
                    $padre = (int) $per_id[ $padre ]->parent;
                }
                $voci[] = [ 'value' => (int) $x->term_id, 'label' => html_entity_decode( implode( ' › ', $nomi ), ENT_QUOTES, 'UTF-8' ) ];
            }
            usort( $voci, function ( $a, $b ) {
                return strcasecmp( $a['label'], $b['label'] );
            } );
            $out[ $t['chiave'] ] = $voci;
        }
        return $out;
    }

    /**
     * OLOsocial: il testo di partenza di una rete dai campi «Testo per i social» del contenuto: il campo pieno più preciso
     * per quella rete (meno reti copre, prima vale; a pari merito il primo), poi quello per tutte; vuoti, resta il modello.
     */
    public static function testo_per_rete( $testo, $post_id, $rete ) {
        $t      = self::del_tipo( get_post_type( (int) $post_id ) );
        $meglio = null;
        $valore = '';
        foreach ( $t ? $t['campi'] : [] as $c ) {
            if ( 'testo_social' !== $c['tipo'] || ! self::campo_vale( $c, $post_id ) ) {
                continue;
            }
            $p = self::precisione_rete( $c['reti'] ?? 'tutte', (string) $rete );
            if ( null === $p || ( null !== $meglio && $p >= $meglio ) ) {
                continue;
            }
            $v = trim( (string) get_post_meta( (int) $post_id, $c['chiave'], true ) );
            if ( '' !== $v ) {
                $meglio = $p;
                $valore = $v;
            }
        }
        return '' !== $valore ? $valore : $testo;
    }

    /** I campi Allegati di tutti i tipi, per la tile Allegati › «Dal contenuto» (una voce per chiave). */
    public static function scelte_allegati() {
        $voci = [ [ 'value' => '', 'label' => __( 'Elenco qui sotto', 'olobuilder' ) ] ];
        $viste = [];
        foreach ( array_merge( [ self::impostazioni()['articoli'] ], self::tipi() ) as $t ) {
            $nome = 'post' === $t['chiave'] ? ( '' !== $t['nome'] ? $t['nome'] : __( 'Articoli', 'olobuilder' ) ) : $t['nome'];
            foreach ( $t['campi'] as $c ) {
                if ( 'allegati' === $c['tipo'] && empty( $viste[ $c['chiave'] ] ) ) {
                    $viste[ $c['chiave'] ] = true;
                    $voci[] = [ 'value' => $c['chiave'], 'label' => $nome . ' › ' . $c['etichetta'] ];
                }
            }
        }
        return $voci;
    }

    /**
     * Gli allegati di un contenuto, per la tile Allegati: titolo, indirizzo, estensione, peso, tipo.
     *
     * @return array[]
     */
    public static function allegati( $chiave, $post_id = 0 ) {
        $post_id = $post_id ? (int) $post_id : (int) get_the_ID();
        $out     = [];
        if ( ! self::chiave_vale( sanitize_key( $chiave ), $post_id ) ) {
            return $out;
        }
        foreach ( array_filter( array_map( 'absint', explode( ',', (string) get_post_meta( $post_id, sanitize_key( $chiave ), true ) ) ) ) as $aid ) {
            if ( 'attachment' !== get_post_type( $aid ) ) {
                continue;
            }
            $file  = get_attached_file( $aid );
            $out[] = [
                'titolo' => get_the_title( $aid ),
                'url'    => (string) wp_get_attachment_url( $aid ),
                'est'    => strtoupper( (string) pathinfo( (string) $file, PATHINFO_EXTENSION ) ),
                'peso'   => ( $file && file_exists( $file ) ) ? size_format( filesize( $file ) ) : '',
                'mime'   => (string) get_post_mime_type( $aid ),
            ];
        }
        return $out;
    }

    /* ─────────────────────────────────────────────
     * REST (Impostazioni › Tipi di contenuto)
     * ───────────────────────────────────────────── */

    public static function rotte() {
        // olobuild/v1 è quella che la scheda delle Impostazioni chiama (oloData.restUrl); olo/v1 resta per chi la usava.
        foreach ( [ 'olobuild/v1', 'olo/v1' ] as $ns ) {
            register_rest_route( $ns, '/tipi-contenuto', [
                [
                    'methods'             => 'GET',
                    'callback'            => [ __CLASS__, 'rest_leggi' ],
                    'permission_callback' => [ __CLASS__, 'puo' ],
                ],
                [
                    'methods'             => 'POST',
                    'callback'            => [ __CLASS__, 'rest_salva' ],
                    'permission_callback' => [ __CLASS__, 'puo' ],
                ],
            ] );
        }
    }

    public static function puo() {
        return current_user_can( 'manage_options' );
    }

    public static function rest_leggi() {
        $formati = [];
        foreach ( self::FORMATI_SOCIAL as $k => $f ) {
            $formati[] = [ 'value' => (string) $k, 'label' => __( $f['nome'], 'olobuilder' ) . ' · ' . $f['w'] . '×' . $f['h'] ]; // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText -- i nomi dei formati sono nel catalogo
        }
        $altri = [];
        foreach ( get_post_types( [ 'public' => true ], 'names' ) as $pt ) {
            if ( ! self::del_tipo( $pt ) || 'post' === $pt ) {
                $altri[] = $pt;
            }
        }
        $conteggi = [];
        foreach ( self::impostazioni()['tipi'] as $t ) {
            $n                          = wp_count_posts( $t['chiave'] );
            $conteggi[ $t['chiave'] ] = $n ? (int) array_sum( (array) $n ) : 0;
        }
        $reti = [];
        foreach ( self::RETI_SOCIALI as $k => $nome ) {
            $reti[] = [ 'value' => $k, 'label' => $nome ];
        }
        return rest_ensure_response( [
            'impostazioni' => self::impostazioni(),
            'icone'        => self::ICONE,
            'formati'      => $formati,
            'reti'         => $reti,
            'categorie'    => (object) self::categorie_per_tipo(),
            'riservati'    => array_values( array_unique( array_merge( self::RISERVATI, $altri ) ) ),
            'conteggi'     => $conteggi,
        ] );
    }

    public static function rest_salva( WP_REST_Request $request ) {
        $dati  = $request->get_json_params();
        $prima = self::impostazioni();
        $dopo  = self::normalizza( is_array( $dati['impostazioni'] ?? null ) ? $dati['impostazioni'] : [] );
        // Un tipo non può prendere la chiave di un tipo che un altro plugin ha già registrato.
        $dopo['tipi'] = array_values( array_filter( $dopo['tipi'], function ( $t ) {
            return ! post_type_exists( $t['chiave'] ) || self::del_tipo( $t['chiave'] );
        } ) );
        update_option( self::OPZIONE, $dopo );
        self::$cache = null;
        self::aggiorna_ruoli( $prima, $dopo );
        return self::rest_leggi();
    }
}
