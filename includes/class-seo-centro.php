<?php
/**
 * Olobuild_Seo_Centro — il punto unico della SEO (Impostazioni › SEO & Privacy › SEO, 1.4.1265).
 *
 * La scheda SEO delle Impostazioni guida la prima configurazione in passi, ognuno col suo stato, e da lì si
 * sistemano anche le pagine una per una. Qui le due letture che le servono:
 * - GET olobuild/v1/seo/riepilogo: i tipi di chi pubblica il sito, i tipi di contenuto, gli indirizzi utili
 *   (sitemap, robots.txt, llms.txt), i conteggi delle pagine da sistemare, dei redirect e delle pagine non trovate;
 * - GET olobuild/v1/seo/pagine: i contenuti pubblicati con il titolo e la descrizione che escono su Google, e cosa
 *   c'è da sistemare. Titolo, descrizione ed esclusione si salvano con la rotta di sempre (page-seo/{id}).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Seo_Centro {

    /** Oltre questo numero di contenuti l'elenco si ferma (i conteggi lo dicono). */
    const MASSIMO = 1500;

    public static function init() {
        add_action( 'rest_api_init', [ __CLASS__, 'rotte' ] );
    }

    public static function rotte() {
        $chi = function () {
            return current_user_can( 'manage_options' );
        };
        register_rest_route( 'olobuild/v1', '/seo/riepilogo', [
            'methods'             => 'GET',
            'callback'            => [ __CLASS__, 'rest_riepilogo' ],
            'permission_callback' => $chi,
        ] );
        register_rest_route( 'olobuild/v1', '/seo/pagine', [
            'methods'             => 'GET',
            'callback'            => [ __CLASS__, 'rest_pagine' ],
            'permission_callback' => $chi,
        ] );
        register_rest_route( 'olobuild/v1', '/seo/indirizzi', [
            'methods'             => 'GET',
            'callback'            => [ __CLASS__, 'rest_indirizzi' ],
            'permission_callback' => $chi,
        ] );
    }

    /**
     * Gli indirizzi che somigliano a quello che si sta scrivendo, con CAP, città, provincia, paese e coordinate
     * (Impostazioni › SEO › «Chi pubblica il sito», 1.4.1270). Da Photon (photon.komoot.io), il motore di ricerca sui
     * dati di OpenStreetMap fatto per i suggerimenti mentre si scrive: Nominatim, che usa la tile Mappa, lo vieta. La
     * richiesta parte dal server, una volta al giorno per testo (risposte tenute 24 ore), senza dati di chi scrive.
     */
    public static function rest_indirizzi( $req ) {
        $q = trim( preg_replace( '/\s+/u', ' ', sanitize_text_field( (string) $req->get_param( 'q' ) ) ) );
        $lun = function_exists( 'mb_strlen' ) ? mb_strlen( $q, 'UTF-8' ) : strlen( $q );
        if ( $lun < 3 || $lun > 200 ) {
            return rest_ensure_response( [ 'indirizzi' => [] ] );
        }
        // Photon conosce solo de, en, fr e «default» (i nomi del posto: in Italia in italiano)
        $lingua = substr( (string) get_user_locale(), 0, 2 );
        if ( ! in_array( $lingua, [ 'en', 'de', 'fr' ], true ) ) {
            $lingua = 'default';
        }
        $chiave = 'olo_seo_ind_' . md5( $lingua . '|' . strtolower( $q ) );
        $tenuto = get_transient( $chiave );
        if ( is_array( $tenuto ) ) {
            return rest_ensure_response( [ 'indirizzi' => $tenuto ] );
        }
        $r = wp_remote_get( 'https://photon.komoot.io/api/?' . http_build_query( [ 'q' => $q, 'limit' => 6, 'lang' => $lingua ] ), [
            'timeout'    => 6,
            'user-agent' => 'OLObuild/' . OLOBUILD_VERSION . ' (WordPress)',
        ] );
        if ( is_wp_error( $r ) || 200 !== (int) wp_remote_retrieve_response_code( $r ) ) {
            return new WP_Error( 'olo_indirizzi', __( 'La ricerca degli indirizzi non risponde: scrivilo a mano o riprova fra poco.', 'olobuild' ), [ 'status' => 502 ] );
        }
        $dati  = json_decode( (string) wp_remote_retrieve_body( $r ), true );
        $out   = [];
        $visti = [];
        foreach ( (array) ( $dati['features'] ?? [] ) as $f ) {
            $p  = (array) ( $f['properties'] ?? [] );
            $xy = (array) ( $f['geometry']['coordinates'] ?? [] );
            if ( count( $xy ) < 2 ) {
                continue;
            }
            $via = '';
            if ( ! empty( $p['street'] ) ) {
                $via = $p['street'] . ( ! empty( $p['housenumber'] ) ? ' ' . $p['housenumber'] : '' );
            } elseif ( ! empty( $p['name'] ) ) {
                $via = $p['name'] . ( ! empty( $p['housenumber'] ) ? ' ' . $p['housenumber'] : '' );
            }
            $citta     = (string) ( $p['city'] ?? ( $p['town'] ?? ( $p['village'] ?? ( $p['locality'] ?? '' ) ) ) );
            // «Provincia di Trento», «Città metropolitana di Milano» → il nome
            $provincia = trim( (string) preg_replace( '/^(Provincia autonoma di|Provincia di|Libero consorzio comunale di|Città metropolitana di)\s+/iu', '',
                (string) ( $p['county'] ?? ( $p['state'] ?? '' ) ) ) );
            $cap       = (string) ( $p['postcode'] ?? '' );
            $paese     = strtoupper( (string) ( $p['countrycode'] ?? '' ) );
            $nome_luogo = ( ! empty( $p['name'] ) && ! empty( $p['street'] ) && $p['name'] !== $p['street'] ) ? (string) $p['name'] : '';
            // OpenStreetMap divide le vie lunghe in tratti: lo stesso indirizzo una volta sola
            $doppio = strtolower( $via . '|' . $nome_luogo . '|' . $cap . '|' . $citta );
            if ( isset( $visti[ $doppio ] ) ) {
                continue;
            }
            $visti[ $doppio ] = true;
            $out[] = [
                'via'       => $via,
                'cap'       => $cap,
                'citta'     => $citta,
                'provincia' => $provincia,
                'paese'     => $paese,
                'nome'      => $nome_luogo,
                'dove'      => implode( ', ', array_filter( [ trim( $cap . ' ' . $citta ), $provincia, (string) ( $p['country'] ?? '' ) ] ) ),
                'lat'       => round( (float) $xy[1], 6 ),
                'lng'       => round( (float) $xy[0], 6 ),
            ];
        }
        set_transient( $chiave, $out, DAY_IN_SECONDS );
        return rest_ensure_response( [ 'indirizzi' => $out ] );
    }

    /** I tipi di contenuto che hanno pagine pubbliche: slug => nome al plurale. */
    public static function tipi_contenuto() {
        $out = [];
        foreach ( get_post_types( [ 'public' => true ], 'objects' ) as $slug => $o ) {
            if ( 'attachment' === $slug || ! is_post_type_viewable( $o ) ) {
                continue;
            }
            $out[ $slug ] = $o->labels->name;
        }
        return $out;
    }

    public static function rest_riepilogo() {
        global $wpdb;
        $tipi = [];
        foreach ( self::tipi_contenuto() as $slug => $nome ) {
            $n = (int) ( wp_count_posts( $slug )->publish ?? 0 );
            // Un tipo senza contenuti pubblicati non si mostra (le Pagine sì)
            if ( $n || 'page' === $slug ) {
                $tipi[] = [ 'slug' => $slug, 'label' => $nome, 'count' => $n ];
            }
        }
        $home_path = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );

        // Redirect e pagine non trovate (Impostazioni › Redirect & 404)
        $redirect = [ 'attivi' => 0, 'n404' => 0 ];
        $rt = $wpdb->prefix . 'olobuild_redirects';
        $lt = $wpdb->prefix . 'olo_404_log';
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- tabelle del plugin; solo i nomi tabella (da $wpdb->prefix) sono interpolati; conteggi letti al volo nell'admin
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$rt'" ) === $rt ) {
            $redirect['attivi'] = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $rt" );
        }
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$lt'" ) === $lt ) {
            $redirect['n404'] = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $lt" );
        }
        // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter

        $elenco = self::righe();
        return rest_ensure_response( [
            'nome_sito'      => html_entity_decode( get_bloginfo( 'name' ), ENT_QUOTES, 'UTF-8' ),
            'motto'          => html_entity_decode( get_bloginfo( 'description' ), ENT_QUOTES, 'UTF-8' ),
            'host'           => (string) wp_parse_url( home_url(), PHP_URL_HOST ),
            'tipi_chi'       => Olobuild_Seo_Settings::tipi_chi(),
            'tipi_contenuto' => $tipi,
            'tassonomie'     => [
                'category' => (int) wp_count_terms( [ 'taxonomy' => 'category', 'hide_empty' => true ] ) > 0,
                'post_tag' => (int) wp_count_terms( [ 'taxonomy' => 'post_tag', 'hide_empty' => true ] ) > 0,
            ],
            'sottocartella'  => '' !== trim( $home_path, '/' ),
            'url'            => [
                'sitemap' => function_exists( 'get_sitemap_url' ) ? (string) get_sitemap_url( 'index' ) : home_url( '/wp-sitemap.xml' ),
                'robots'  => home_url( '/robots.txt' ),
                'llms'    => Olobuild_Seo_Head::url_llms(),
                'lettura' => admin_url( 'options-reading.php' ),
            ],
            'bot'            => [ 'addestramento' => Olobuild_Seo_Head::BOT_ADDESTRAMENTO, 'risposte' => Olobuild_Seo_Head::BOT_RISPOSTE ],
            'pagine'         => $elenco['conteggi'],
            'redirect'       => $redirect,
            'home'           => self::home(),
            'altro_plugin'   => self::altro_plugin_seo(),
            // Il logo del tema: esce quando «Chi pubblica il sito» non ne ha uno suo (e per i link condivisi senza immagine)
            'logo_tema'      => ( get_theme_mod( 'custom_logo' ) ? (string) wp_get_attachment_image_url( (int) get_theme_mod( 'custom_logo' ), 'full' ) : '' ),
        ] );
    }

    public static function rest_pagine( $req ) {
        $tipo   = sanitize_key( (string) $req->get_param( 'tipo' ) );
        $cerca  = function_exists( 'mb_strtolower' ) ? mb_strtolower( trim( (string) $req->get_param( 'cerca' ) ), 'UTF-8' ) : strtolower( trim( (string) $req->get_param( 'cerca' ) ) );
        $filtro = sanitize_key( (string) $req->get_param( 'filtro' ) );
        $per    = max( 5, min( 100, (int) ( $req->get_param( 'per' ) ?: 25 ) ) );
        $pagina = max( 1, (int) ( $req->get_param( 'pagina' ) ?: 1 ) );

        $elenco = self::righe();
        $righe  = array_values( array_filter( $elenco['righe'], function ( $r ) use ( $tipo, $cerca, $filtro ) {
            if ( $tipo && $r['tipo'] !== $tipo ) {
                return false;
            }
            if ( 'da-sistemare' === $filtro && ! in_array( $r['stato'], [ 'errore', 'attenzione' ], true ) ) {
                return false;
            }
            if ( 'escluse' === $filtro && ! $r['noindex'] ) {
                return false;
            }
            if ( '' !== $cerca ) {
                $dove = function_exists( 'mb_strtolower' ) ? mb_strtolower( $r['titolo'] . ' ' . $r['percorso'] . ' ' . $r['titolo_google'], 'UTF-8' ) : strtolower( $r['titolo'] . ' ' . $r['percorso'] );
                if ( false === strpos( $dove, $cerca ) ) {
                    return false;
                }
            }
            return true;
        } ) );

        return rest_ensure_response( [
            'righe'    => array_slice( $righe, ( $pagina - 1 ) * $per, $per ),
            'trovate'  => count( $righe ),
            'pagina'   => $pagina,
            'per'      => $per,
            'conteggi' => $elenco['conteggi'],
        ] );
    }

    /**
     * Tutti i contenuti pubblicati, ognuno con titolo e descrizione come escono su Google e il suo stato:
     * errore (nessuna descrizione), attenzione (titolo lungo o uguale a un altro, descrizione tua troppo lunga o
     * corta), ok, escluso (noindex). La home per prima, poi per tipo e titolo.
     */
    private static function righe() {
        $tipi = self::tipi_contenuto();
        $adv  = Olobuild_Seo_Settings::get( Olobuild_Seo_Settings::OPT_ADVANCED );
        $home = (int) get_option( 'page_on_front' );
        $nascosto = ! get_option( 'blog_public' );
        $redazione = class_exists( 'Olobuild_Redazione' ) ? Olobuild_Redazione::tipi() : [];
        // La descrizione che esce quando una pagina non ne ha (Olobuild_Seo_Head::get_seo_description): quella del sito o il motto
        $titoli    = Olobuild_Seo_Settings::get( Olobuild_Seo_Settings::OPT_TITLES );
        $desc_sito = trim( (string) ( $titoli['description'] ?? '' ) );
        if ( '' === $desc_sito ) {
            $desc_sito = trim( (string) get_bloginfo( 'description' ) );
        }

        $posts = get_posts( [
            'post_type'        => array_keys( $tipi ),
            'post_status'      => 'publish',
            'posts_per_page'   => self::MASSIMO,
            'orderby'          => [ 'type' => 'ASC', 'menu_order' => 'ASC', 'title' => 'ASC' ],
            'no_found_rows'    => true,
            'suppress_filters' => false,
        ] );
        update_meta_cache( 'post', wp_list_pluck( $posts, 'ID' ) );

        $righe = [];
        $per_titolo = [];
        foreach ( $posts as $p ) {
            list( $titolo_google, $titolo_suo ) = Olobuild_Seo_Head::titolo_per( $p );
            $sua  = false;
            $desc = Olobuild_Seo_Head::descrizione_per( $p, $sua );
            $noindex_suo = (bool) get_post_meta( $p->ID, '_olo_seo_noindex', true );
            $noindex_tipo = ! empty( $adv['noindex_pt'][ $p->post_type ] );
            $template = (int) get_post_meta( $p->ID, '_olo_template_id', true );
            if ( $template ) {
                $modifica = admin_url( 'admin.php?page=olobuilder-templates&template_id=' . $template . '&post_id=' . $p->ID );
            } elseif ( isset( $redazione[ $p->post_type ] ) ) {
                $modifica = admin_url( 'admin.php?page=' . Olobuild_Redazione::SLUG ) . '#' . $p->post_type . '/' . $p->ID;
            } else {
                $modifica = (string) get_edit_post_link( $p->ID, 'raw' );
            }
            $url = (string) get_permalink( $p );
            $righe[] = [
                'id'            => $p->ID,
                'tipo'          => $p->post_type,
                'tipo_nome'     => $tipi[ $p->post_type ] ?? $p->post_type,
                'home'          => $p->ID === $home,
                'titolo'        => html_entity_decode( get_the_title( $p ), ENT_QUOTES, 'UTF-8' ),
                'url'           => $url,
                'percorso'      => (string) wp_parse_url( $url, PHP_URL_PATH ),
                // L'indirizzo si cambia dall'elenco solo coi permalink «belli» e mai per la home
                'slug'          => ( false === strpos( $url, '?' ) && $p->ID !== $home ) ? $p->post_name : '',
                'base'          => substr( $url, 0, (int) strrpos( rtrim( $url, '/' ), '/' ) + 1 ),
                'seo_title'     => (string) get_post_meta( $p->ID, '_olo_seo_title', true ),
                'titolo_google' => $titolo_google,
                'titolo_suo'    => $titolo_suo,
                'descrizione'   => $sua ? $desc : '',
                'descrizione_auto' => $sua ? Olobuild_Seo_Head::descrizione_automatica( $p ) : $desc,
                'noindex'       => $noindex_suo || $noindex_tipo,
                'noindex_tipo'  => $noindex_tipo,
                'modifica'      => $modifica,
                'problemi'      => [],
                'stato'         => 'ok',
            ];
            $chiave = function_exists( 'mb_strtolower' ) ? mb_strtolower( $titolo_google, 'UTF-8' ) : strtolower( $titolo_google );
            $per_titolo[ $chiave ][] = count( $righe ) - 1;
        }

        $lun = function ( $s ) {
            return function_exists( 'mb_strlen' ) ? mb_strlen( $s, 'UTF-8' ) : strlen( $s );
        };
        $conteggi = [ 'totale' => count( $righe ), 'da_sistemare' => 0, 'senza_descrizione' => 0, 'escluse' => 0, 'scritte' => 0, 'oltre' => count( $posts ) >= self::MASSIMO, 'sito_nascosto' => $nascosto ];
        foreach ( $righe as $i => &$r ) {
            if ( $r['noindex'] ) {
                $r['stato'] = 'escluso';
                $r['problemi'][] = $r['noindex_tipo'] ? __( 'Il suo tipo di contenuto è escluso dai motori', 'olobuild' ) : __( 'Esclusa dai motori', 'olobuild' );
                $conteggi['escluse']++;
                continue;
            }
            $errore = false;
            $att    = false;
            $desc   = '' !== $r['descrizione'] ? $r['descrizione'] : $r['descrizione_auto'];
            // Senza una descrizione sua né un riassunto, la pagina esce con quella del sito (get_seo_description):
            // per la home va bene, per le altre pagine è la stessa frase dappertutto.
            if ( '' === $desc && '' !== $desc_sito && ! $r['home'] ) {
                $r['problemi'][] = __( 'Usa la descrizione del sito', 'olobuild' );
                $att = true;
                $conteggi['senza_descrizione']++;
            } elseif ( '' === $desc && '' === $desc_sito ) {
                $r['problemi'][] = __( 'Nessuna descrizione', 'olobuild' );
                $errore = true;
                $conteggi['senza_descrizione']++;
            } elseif ( '' !== $r['descrizione'] ) {
                $conteggi['scritte']++;
                $l = $lun( $r['descrizione'] );
                if ( $l > 160 ) {
                    /* translators: %d: caratteri */
                    $r['problemi'][] = sprintf( __( 'Descrizione lunga: %d caratteri', 'olobuild' ), $l );
                    $att = true;
                } elseif ( $l < 50 ) {
                    /* translators: %d: caratteri */
                    $r['problemi'][] = sprintf( __( 'Descrizione corta: %d caratteri', 'olobuild' ), $l );
                    $att = true;
                }
            }
            $lt = $lun( $r['titolo_google'] );
            if ( $lt > 60 ) {
                /* translators: %d: caratteri */
                $r['problemi'][] = sprintf( __( 'Titolo lungo: %d caratteri', 'olobuild' ), $lt );
                $att = true;
            }
            $chiave = function_exists( 'mb_strtolower' ) ? mb_strtolower( $r['titolo_google'], 'UTF-8' ) : strtolower( $r['titolo_google'] );
            $doppi = array_filter( $per_titolo[ $chiave ] ?? [], function ( $j ) use ( $righe, $i ) {
                return $j !== $i && ! $righe[ $j ]['noindex'];
            } );
            $r['doppio'] = (bool) $doppi;
            if ( $doppi ) {
                $r['problemi'][] = __( 'Stesso titolo di un\'altra pagina', 'olobuild' );
                $att = true;
            }
            $r['stato'] = $errore ? 'errore' : ( $att ? 'attenzione' : 'ok' );
            if ( 'ok' !== $r['stato'] ) {
                $conteggi['da_sistemare']++;
            }
        }
        unset( $r );

        // La home per prima, poi le pagine, gli articoli e gli altri tipi
        $peso = function ( $r ) {
            return $r['home'] ? 0 : ( 'page' === $r['tipo'] ? 1 : ( 'post' === $r['tipo'] ? 2 : 3 ) );
        };
        uksort( $righe, function ( $i, $j ) use ( $righe, $peso ) {
            return [ $peso( $righe[ $i ] ), $righe[ $i ]['tipo_nome'], $i ] <=> [ $peso( $righe[ $j ] ), $righe[ $j ]['tipo_nome'], $j ];
        } );
        $righe = array_values( $righe );
        return [ 'righe' => $righe, 'conteggi' => $conteggi ];
    }

    /** La home: il titolo e la descrizione sue, per l'anteprima di Google della scheda (passo 3). */
    private static function home() {
        $id = (int) get_option( 'page_on_front' );
        $p  = $id ? get_post( $id ) : null;
        if ( ! $p ) {
            return null;
        }
        return [
            'id'               => $id,
            'titolo'           => html_entity_decode( get_the_title( $p ), ENT_QUOTES, 'UTF-8' ),
            'seo_title'        => (string) get_post_meta( $id, '_olo_seo_title', true ),
            'descrizione'      => (string) get_post_meta( $id, '_olo_seo_description', true ),
            'descrizione_auto' => Olobuild_Seo_Head::descrizione_automatica( $p ),
            'url'              => (string) get_permalink( $p ),
        ];
    }

    /** Un altro plugin SEO attivo prende il posto di OLObuild nella testata (Olobuild_Seo_Head::seo_plugin_active). */
    private static function altro_plugin_seo() {
        if ( defined( 'WPSEO_VERSION' ) ) return 'Yoast SEO';
        if ( class_exists( 'RankMath' ) ) return 'Rank Math';
        if ( defined( 'AIOSEO_VERSION' ) ) return 'All in One SEO';
        if ( defined( 'SEOPRESS_VERSION' ) ) return 'SEOPress';
        return '';
    }
}
