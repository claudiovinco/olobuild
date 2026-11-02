<?php
/**
 * Olobuild_Redazione_Lingue — le traduzioni nella Redazione, con OLOlang (1.4.952).
 *
 * OLOlang traduce un contenuto con una copia per lingua (_olo_lang_source sulla copia, _olo_lang_translations
 * sull'originale): sul sito, in tedesco, la copia tedesca prende il posto dell'originale negli elenchi e nella sua
 * pagina. La Redazione apre la copia nella stessa scheda, con accanto il testo italiano: si traducono titolo, riassunto,
 * testo e i campi di testo; date, foto, video, link, scelte, categorie e immagine in evidenza restano quelli
 * dell'originale e qui si tengono allineati a ogni salvataggio dell'originale (anche dalla scheda di WordPress).
 *
 * Senza OLOlang, o con la sola lingua principale attiva, non cambia niente.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Redazione_Lingue {

    /** I campi dei Tipi di contenuto che si traducono: gli altri tipi (data, link, foto, video, scelta…) sono comuni. */
    const TRADUCIBILI = [ 'testo', 'testo_lungo', 'formattato' ];

    /** I campi degli spettacoli (Olobuild_Spettacoli::CAMPI) che si traducono; gli altri sono comuni. */
    const SPETTACOLO_TRADUCIBILI = [ 'sottotitolo', 'genere', 'orari', 'luogo', 'durata', 'prezzo', 'etichette', 'incontro' ];

    public static function avvia() {
        add_filter( 'olo_lang_enabled_post_types', [ __CLASS__, 'tipi_da_tradurre' ] );
        add_action( 'wp_after_insert_post', [ __CLASS__, 'dopo_il_salvataggio' ], 30, 2 );
        add_action( 'wp_trash_post', [ __CLASS__, 'cestina_copie' ] );
        add_action( 'untrashed_post', [ __CLASS__, 'ripristina_copie' ] );
    }

    /** OLOlang c'è, con le copie per lingua e almeno una lingua attiva oltre la principale. */
    public static function attive() {
        return class_exists( 'Olo_Lang_Language' ) && class_exists( 'Olo_Lang_Post_Translation' )
            && method_exists( 'Olo_Lang_Post_Translation', 'duplicate_post' ) && count( Olo_Lang_Language::get_active_languages() ) > 1;
    }

    /** Il nome di una lingua nella lingua stessa (Deutsch, Français), dal catalogo di OLOlang; se no il nome salvato. */
    private static function nome( $l ) {
        static $catalogo = null;
        if ( null === $catalogo ) {
            $catalogo = method_exists( 'Olo_Lang_Language', 'get_available_languages' ) ? array_column( Olo_Lang_Language::get_available_languages(), null, 'code' ) : [];
        }
        $n = (string) ( $catalogo[ $l['code'] ]['native'] ?? ( $l['name'] ?? $l['code'] ) );
        return html_entity_decode( $n, ENT_QUOTES, 'UTF-8' );
    }

    /** [ 'principale' => [ codice, nome ], 'altre' => [ [ codice, nome ], … ] ], o null senza lingue. */
    public static function per_la_redazione() {
        if ( ! self::attive() ) {
            return null;
        }
        $principale = Olo_Lang_Language::get_default_lang();
        $out        = [ 'principale' => null, 'altre' => [] ];
        foreach ( Olo_Lang_Language::get_active_languages() as $l ) {
            $v = [ 'codice' => (string) $l['code'], 'nome' => self::nome( $l ) ];
            if ( $l['code'] === $principale ) {
                $out['principale'] = $v;
            } else {
                $out['altre'][] = $v;
            }
        }
        if ( ! $out['principale'] ) {
            $out['principale'] = [ 'codice' => $principale, 'nome' => strtoupper( $principale ) ];
        }
        return $out;
    }

    /** I tipi della Redazione e gli spettacoli si traducono con le copie di OLOlang. */
    public static function tipi_da_tradurre( $tipi ) {
        $tipi = (array) $tipi;
        if ( class_exists( 'Olobuild_Tipi_Contenuto' ) ) {
            foreach ( Olobuild_Tipi_Contenuto::tipi() as $t ) {
                $tipi[] = $t['chiave'];
            }
        }
        if ( class_exists( 'Olobuild_Spettacoli' ) ) {
            $tipi[] = Olobuild_Spettacoli::TIPO;
        }
        $tipi[] = 'post';
        return array_values( array_unique( $tipi ) );
    }

    public static function traducibile( $tipo ) {
        return self::attive() && in_array( $tipo, (array) apply_filters( 'olo_lang_enabled_post_types', [ 'post', 'page' ] ), true );
    }

    /* ─────────────────────────────────────────────
     * Originali e copie
     * ───────────────────────────────────────────── */

    /** L'id dell'originale se il contenuto è una copia tradotta, se no 0. */
    public static function originale_di( $post_id ) {
        return (int) get_post_meta( (int) $post_id, '_olo_lang_source', true );
    }

    public static function lingua_di( $post_id ) {
        return self::originale_di( $post_id ) ? (string) get_post_meta( (int) $post_id, '_olo_lang_lang', true ) : '';
    }

    /** Le copie di un originale che esistono ancora (fuori dal cestino): lingua => id. */
    public static function copie( $post_id ) {
        $out = [];
        foreach ( (array) ( get_post_meta( (int) $post_id, '_olo_lang_translations', true ) ?: [] ) as $l => $id ) {
            $p = get_post( (int) $id );
            if ( $p && 'trash' !== $p->post_status && self::originale_di( $p->ID ) === (int) $post_id ) {
                $out[ (string) $l ] = $p->ID;
            }
        }
        return $out;
    }

    /** Per ogni lingua attiva oltre la principale: la copia (id, stato) o niente (id 0). */
    public static function traduzioni( $post_id ) {
        $lingue = self::per_la_redazione();
        if ( ! $lingue ) {
            return [];
        }
        $copie = self::copie( $post_id );
        $out   = [];
        foreach ( $lingue['altre'] as $l ) {
            $id    = (int) ( $copie[ $l['codice'] ] ?? 0 );
            $out[] = $l + [ 'id' => $id, 'stato' => $id ? get_post_status( $id ) : '' ];
        }
        return $out;
    }

    /**
     * La copia nella lingua, creata se manca: con titolo, testo e campi dell'originale da tradurre, in bozza, con la data
     * dell'originale (negli elenchi ordinati per data sta al suo posto) e un indirizzo che dice la lingua.
     */
    public static function crea_copia( $post_id, $lingua ) {
        $post   = get_post( (int) $post_id );
        $lingue = self::per_la_redazione();
        if ( ! $post || ! $lingue || ! in_array( $lingua, array_column( $lingue['altre'], 'codice' ), true ) ) {
            return new WP_Error( 'olo_redazione_lingua', __( 'Questa lingua non è attiva.', 'olobuild' ), [ 'status' => 400 ] );
        }
        if ( self::originale_di( $post->ID ) ) {
            return new WP_Error( 'olo_redazione_lingua', __( 'Questa è già una traduzione.', 'olobuild' ), [ 'status' => 400 ] );
        }
        $copie = self::copie( $post->ID );
        if ( ! empty( $copie[ $lingua ] ) ) {
            return (int) $copie[ $lingua ];
        }
        $id = ( new Olo_Lang_Post_Translation() )->duplicate_post( $post->ID, $lingua );
        if ( is_wp_error( $id ) ) {
            return $id;
        }
        wp_update_post( [
            'ID'            => $id,
            'post_title'    => wp_slash( $post->post_title ), // OLOlang aggiunge « [DE]» al titolo
            'post_name'     => ( $post->post_name ? $post->post_name : sanitize_title( $post->post_title ) ) . '-' . $lingua,
            'post_date'     => $post->post_date,
            'post_date_gmt' => $post->post_date_gmt,
            'edit_date'     => true,
            'menu_order'    => $post->menu_order,
        ] );
        // Una traduzione non va sui social da sola: ci va l'originale (il riquadro di OLOsocial vuoto = niente).
        if ( class_exists( 'Olo_Social_Riquadro' ) ) {
            update_post_meta( $id, Olo_Social_Riquadro::META, [] );
        }
        delete_post_meta( $id, '_edit_lock' );
        return (int) $id;
    }

    /* ─────────────────────────────────────────────
     * Ciò che resta comune
     * ───────────────────────────────────────────── */

    /** Le chiavi dei campi che le copie prendono dall'originale. */
    public static function campi_comuni( $tipo ) {
        $chiavi = [ '_thumbnail_id' ];
        $t      = class_exists( 'Olobuild_Tipi_Contenuto' ) ? Olobuild_Tipi_Contenuto::del_tipo( $tipo ) : null;
        foreach ( $t ? $t['campi'] : [] as $c ) {
            if ( ! in_array( $c['tipo'], self::TRADUCIBILI, true ) ) {
                $chiavi[] = $c['chiave'];
            }
        }
        if ( class_exists( 'Olobuild_Spettacoli' ) && Olobuild_Spettacoli::TIPO === $tipo ) {
            foreach ( Olobuild_Spettacoli::CAMPI as $k ) {
                if ( ! in_array( $k, self::SPETTACOLO_TRADUCIBILI, true ) ) {
                    $chiavi[] = Olobuild_Spettacoli::META . $k;
                }
            }
            $chiavi[] = Olobuild_Spettacoli::META . 'seme';
        }
        return array_values( array_unique( (array) apply_filters( 'olobuild_lingue_campi_comuni', $chiavi, $tipo ) ) );
    }

    /** I campi del tipo che la scheda di una traduzione scrive: solo quelli di testo. */
    public static function campi_della_copia( $t ) {
        $t['campi'] = array_values( array_filter( $t['campi'], function ( $c ) {
            return in_array( $c['tipo'], self::TRADUCIBILI, true );
        } ) );
        return $t;
    }

    /** Porta sulle copie ciò che non si traduce: campi comuni, categorie e tag, data e ordine. */
    public static function allinea( $post_id ) {
        $post  = get_post( (int) $post_id );
        $copie = $post ? self::copie( $post->ID ) : [];
        if ( ! $copie ) {
            return;
        }
        $comuni = self::campi_comuni( $post->post_type );
        $tax    = get_object_taxonomies( $post->post_type );
        foreach ( $copie as $cid ) {
            foreach ( $comuni as $k ) {
                $v = get_post_meta( $post->ID, $k, true );
                if ( '' === $v || null === $v || false === $v || [] === $v ) {
                    delete_post_meta( $cid, $k );
                } else {
                    update_post_meta( $cid, $k, wp_slash( $v ) );
                }
            }
            foreach ( $tax as $x ) {
                if ( 'post_format' === $x || 'post_translations' === $x ) {
                    continue;
                }
                $ids = wp_get_object_terms( $post->ID, $x, [ 'fields' => 'ids' ] );
                if ( ! is_wp_error( $ids ) ) {
                    wp_set_object_terms( $cid, array_map( 'intval', $ids ), $x );
                }
            }
            $c = get_post( $cid );
            if ( $c && ( $c->post_date !== $post->post_date || (int) $c->menu_order !== (int) $post->menu_order ) ) {
                global $wpdb;
                // Senza wp_update_post: niente versioni, niente hook di salvataggio, la data della copia non è un'uscita.
                $wpdb->update( $wpdb->posts, [
                    'post_date'     => $post->post_date,
                    'post_date_gmt' => $post->post_date_gmt,
                    'menu_order'    => (int) $post->menu_order,
                ], [ 'ID' => $cid ] ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
                clean_post_cache( $cid );
            }
        }
    }

    /** Dopo ogni salvataggio di un originale tradotto, dalla Redazione o dalla scheda di WordPress. */
    public static function dopo_il_salvataggio( $post_id, $post ) {
        if ( ! $post || wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) || self::originale_di( $post_id ) ) {
            return;
        }
        if ( ! self::traducibile( $post->post_type ) ) {
            return;
        }
        self::allinea( $post_id );
    }

    /** L'originale nel cestino porta con sé le sue traduzioni; tornando indietro tornano anche loro. */
    public static function cestina_copie( $post_id ) {
        if ( self::originale_di( $post_id ) ) {
            return;
        }
        foreach ( self::copie( $post_id ) as $cid ) {
            wp_trash_post( $cid );
        }
    }

    public static function ripristina_copie( $post_id ) {
        if ( self::originale_di( $post_id ) ) {
            return;
        }
        foreach ( (array) ( get_post_meta( (int) $post_id, '_olo_lang_translations', true ) ?: [] ) as $cid ) {
            if ( 'trash' === get_post_status( (int) $cid ) ) {
                wp_untrash_post( (int) $cid );
            }
        }
    }
}
