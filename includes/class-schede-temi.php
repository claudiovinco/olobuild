<?php
/**
 * Olobuild_Schede_Temi — i temi della Vetrina come contenuti del sito: una scheda per tema (menu «Temi»), dove le demo
 * dei temi sono accese (Olobuild_Demo_Temi::accese(), cioè il sottosito di OLObuild).
 *
 * Nome, categoria, anteprime, colori e demo restano quelli del tema nel plugin (una fonte sola); la scheda aggiunge ciò
 * che il plugin non sa: una riga sotto il nome nella Vetrina, «Non mostrarlo nella Vetrina», le foto e i testi per i
 * social (la serie «Tema della settimana»), gli allegati. È un tipo «di sistema» di Olobuild_Tipi_Contenuto: campi,
 * riquadro Dettagli, ritagli per i social e fonte dinamica vengono da lì.
 *
 * Le schede nascono da sole (sincronizza(), a ogni versione del plugin, alla prima visita in bacheca): la prima volta
 * tutte pubblicate, perché quei temi sono già in Vetrina; poi i temi nuovi arrivano come bozze, e pubblicarne una è
 * annunciare il tema (con OLOsocial il riquadro Social la manda anche sui social). Una scheda non ha una pagina sua:
 * il suo indirizzo è la Vetrina col visore aperto sul tema (#demo-<id>), lo stesso che la Vetrina scrive da sé.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Schede_Temi {

    const TIPO    = 'olo_tema';
    const META_ID = '_olo_tema_id';

    /** [ 'versione' => ultima versione sincronizzata, 'vetrina' => id della pagina con la Vetrina ]. */
    const OPZIONE = 'olobuild_schede_temi';

    /** @var array|null Le schede per id del tema, per questa richiesta. */
    private static $schede = null;

    public static function avvia() {
        add_filter( 'olobuild_tipi_di_sistema', [ __CLASS__, 'tipo' ] );
        add_filter( 'post_type_link', [ __CLASS__, 'indirizzo' ], 10, 2 );
        add_filter( 'olo_social_tipi_riquadro', [ __CLASS__, 'nel_riquadro' ] );
        add_filter( 'olo_social_foto_rete', [ __CLASS__, 'foto' ], 20, 3 );
        add_filter( 'olo_social_testo_rete', [ __CLASS__, 'testo' ], 10, 3 );
        add_action( 'admin_init', [ __CLASS__, 'sincronizza_se_serve' ] );
    }

    private static function accese() {
        return class_exists( 'Olobuild_Demo_Temi' ) && Olobuild_Demo_Temi::accese();
    }

    public static function tipo( $tipi ) {
        if ( ! self::accese() ) {
            return $tipi;
        }
        $tipi[] = [
            'chiave'    => self::TIPO,
            'nome'      => __( 'Temi', 'olobuilder' ),
            'singolare' => __( 'Tema', 'olobuilder' ),
            'indirizzo' => 'tema',
            'icona'     => 'dashicons-admin-appearance',
            'posizione' => 26,
            'archivio'  => false,
            'editor'    => 'semplice',
            'estratto'  => false,
            'immagine'  => true,
            'permessi'  => 'articoli',
            'pubblico'  => false,
            'campi'     => [
                [ 'chiave' => 'sottotitolo', 'etichetta' => __( 'Riga nella Vetrina', 'olobuilder' ), 'tipo' => 'testo', 'larghezza' => 'meta',
                    'aiuto' => __( 'Sotto il nome del tema, nella sua scheda della Vetrina. Vuota: niente.', 'olobuilder' ) ],
                [ 'chiave' => 'nascosto', 'etichetta' => __( 'Non mostrarlo nella Vetrina', 'olobuilder' ), 'tipo' => 'sino', 'larghezza' => 'meta' ],
                [ 'chiave' => 'foto_social', 'etichetta' => __( 'Foto per i social', 'olobuilder' ), 'tipo' => 'foto_social' ],
                [ 'chiave' => 'grafica_fb', 'etichetta' => __( 'Grafica per Facebook, LinkedIn e le altre reti', 'olobuilder' ), 'tipo' => 'immagine', 'larghezza' => 'meta',
                    'aiuto' => __( 'Se diversa da quella di Instagram: per esempio con l\'indirizzo del sito al posto di «link in bio».', 'olobuilder' ) ],
                [ 'chiave' => 'testo_instagram', 'etichetta' => __( 'Testo per Instagram', 'olobuilder' ), 'tipo' => 'testo_lungo', 'larghezza' => 'meta' ],
                [ 'chiave' => 'testo_social', 'etichetta' => __( 'Testo per Facebook, LinkedIn e le altre reti', 'olobuilder' ), 'tipo' => 'testo_lungo', 'larghezza' => 'meta',
                    'aiuto' => __( 'Il link al tema lo aggiunge OLOsocial.', 'olobuilder' ) ],
                [ 'chiave' => 'allegati', 'etichetta' => __( 'Allegati', 'olobuilder' ), 'tipo' => 'allegati' ],
            ],
        ];
        return $tipi;
    }

    /** L'indirizzo di una scheda: la Vetrina col visore aperto sul tema. */
    public static function indirizzo( $link, $post ) {
        if ( ! ( $post instanceof WP_Post ) || self::TIPO !== $post->post_type ) {
            return $link;
        }
        $id      = (string) get_post_meta( $post->ID, self::META_ID, true );
        $o       = get_option( self::OPZIONE, [] );
        $vetrina = ! empty( $o['vetrina'] ) ? get_permalink( (int) $o['vetrina'] ) : '';
        $base    = $vetrina ? $vetrina : home_url( '/temi/' );
        return '' !== $id ? $base . '#demo-' . rawurlencode( $id ) : $base;
    }

    /** Con OLOsocial, le schede hanno sempre il riquadro Social. */
    public static function nel_riquadro( $tipi ) {
        if ( self::accese() ) {
            $tipi[] = self::TIPO;
        }
        return $tipi;
    }

    /** Facebook, LinkedIn e le altre reti prendono la loro grafica, se c'è; Instagram quella del campo Foto per i social. */
    public static function foto( $foto, $post_id, $rete ) {
        if ( self::TIPO !== get_post_type( (int) $post_id ) || 'instagram' === $rete ) {
            return $foto;
        }
        $fb = absint( get_post_meta( (int) $post_id, 'grafica_fb', true ) );
        return $fb ? $fb : $foto;
    }

    /** Il testo di partenza per rete: quello di Instagram, o quello delle altre reti, se scritti nella scheda. */
    public static function testo( $testo, $post_id, $rete ) {
        if ( self::TIPO !== get_post_type( (int) $post_id ) ) {
            return $testo;
        }
        $t = trim( (string) get_post_meta( (int) $post_id, 'instagram' === $rete ? 'testo_instagram' : 'testo_social', true ) );
        return '' !== $t ? $t : $testo;
    }

    /**
     * Le schede per id del tema (bozze comprese, il cestino no): riga nella Vetrina e «non mostrarlo». Le legge la
     * tile Vetrina temi.
     *
     * @return array [ id => [ 'sottotitolo' => string, 'nascosto' => bool ] ]
     */
    public static function schede() {
        if ( null !== self::$schede ) {
            return self::$schede;
        }
        self::$schede = [];
        if ( ! post_type_exists( self::TIPO ) ) {
            return self::$schede;
        }
        $ids = get_posts( [ 'post_type' => self::TIPO, 'post_status' => [ 'publish', 'draft', 'future', 'pending', 'private' ], 'numberposts' => -1, 'fields' => 'ids', 'no_found_rows' => true ] );
        update_meta_cache( 'post', $ids );
        foreach ( $ids as $pid ) {
            $tema = (string) get_post_meta( $pid, self::META_ID, true );
            if ( '' !== $tema ) {
                self::$schede[ $tema ] = [
                    'sottotitolo' => trim( (string) get_post_meta( $pid, 'sottotitolo', true ) ),
                    'nascosto'    => '1' === (string) get_post_meta( $pid, 'nascosto', true ),
                ];
            }
        }
        return self::$schede;
    }

    public static function sincronizza_se_serve() {
        if ( ! self::accese() || ! current_user_can( 'edit_posts' ) || wp_doing_ajax() ) {
            return;
        }
        $o = get_option( self::OPZIONE, [] );
        if ( ( $o['versione'] ?? '' ) !== OLOBUILD_VERSION ) {
            self::sincronizza();
        }
    }

    /**
     * Una scheda per ogni tema della Vetrina che non ce l'ha. La prima volta pubblicate senza passare dai ganci di
     * pubblicazione (quei temi sono già usciti: niente post social a raffica); dopo, bozze da pubblicare.
     *
     * @return int schede create
     */
    public static function sincronizza() {
        if ( ! class_exists( 'Olobuild_Theme_Importer' ) ) {
            require_once OLOBUILD_PATH . 'includes/class-theme-importer.php';
        }
        global $wpdb;
        $o       = get_option( self::OPZIONE, [] );
        $prima   = empty( $o['versione'] );
        $ci_sono = [];
        foreach ( get_posts( [ 'post_type' => self::TIPO, 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids', 'no_found_rows' => true ] ) as $pid ) {
            $ci_sono[ (string) get_post_meta( $pid, self::META_ID, true ) ] = true;
        }
        $creati = 0;
        foreach ( Olobuild_Theme_Importer::get_themes() as $t ) {
            $id = (string) ( $t['id'] ?? '' );
            if ( '' === $id || isset( $ci_sono[ $id ] ) || ! Olobuild_Demo_Temi::acceso( $id ) || '' === Olobuild_Demo_Temi::cartella( $id ) ) {
                continue;
            }
            $pid = wp_insert_post( [
                'post_type'   => self::TIPO,
                'post_status' => 'draft',
                'post_title'  => (string) ( $t['name'] ?? $id ),
                'post_name'   => sanitize_title( $id ),
                'meta_input'  => [ self::META_ID => $id ],
            ] );
            if ( $pid && ! is_wp_error( $pid ) ) {
                if ( $prima ) {
                    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- pubblicata senza i ganci di pubblicazione, vedi sopra
                    $wpdb->update( $wpdb->posts, [ 'post_status' => 'publish' ], [ 'ID' => (int) $pid ] );
                    clean_post_cache( (int) $pid );
                }
                $creati++;
            }
        }
        update_option( self::OPZIONE, [ 'versione' => OLOBUILD_VERSION, 'vetrina' => self::pagina_vetrina() ], false );
        self::$schede = null;
        return $creati;
    }

    /** La pagina pubblicata che mostra la tile Vetrina temi (il suo template), 0 se non c'è. */
    private static function pagina_vetrina() {
        global $wpdb;
        $tab = Olobuild_Database::table( 'templates' );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- tabella del plugin, una volta per versione
        $tpl = $wpdb->get_col( "SELECT id FROM {$tab} WHERE content LIKE '%\"themeshowcase\"%' AND status = 'published'" );
        foreach ( array_map( 'intval', (array) $tpl ) as $id ) {
            $pagine = get_posts( [ 'post_type' => 'page', 'post_status' => 'publish', 'numberposts' => 1, 'fields' => 'ids', 'meta_key' => '_olo_template_id', 'meta_value' => (string) $id ] ); // phpcs:ignore WordPress.DB.SlowDBQuery -- una volta per versione
            if ( $pagine ) {
                return (int) $pagine[0];
            }
        }
        return 0;
    }
}
