<?php
/**
 * Le scene dello Slider Pro (1.4.1422): gli Slider Pro dei temi, da cui si parte nell'editor («Scene dei temi»).
 *
 * Utente, 24 nov 2026: «riprogettare la tile pro slider in modo che permetta di costruire tutti i pro slider che hai
 * inserito in tutti i temi… anche l'utente poco addestrato deve capire come utilizzare la pro slider in modo
 * efficiente». Fase 1: si sceglie una scena vera (l'apertura di Cric, di Torchio…) e poi si cambiano testi e foto.
 *
 * - L'elenco: assets/data/scene-slider.json (scripts/indice-scene-slider.py), coi temi che il selettore dei temi
 *   mostra (Olobuild_Theme_Importer::get_themes(): niente temi in lavorazione) e col lucchetto sui temi del Pro
 *   per chi non ha la licenza Pro.
 * - Usare una scena: le impostazioni dello slider lette dalla pagina del tema (nello zip della Free le pagine dei temi
 *   del Pro si scaricano adesso, come all'importazione del tema), con le foto e i video copiati nella Libreria media
 *   (Olobuild_Template_Library::import_file_media) e i link alle pagine del tema ({{PAGINA:…}}) a «#».
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Olobuild_Scene_Slider {

    const INDICE = 'assets/data/scene-slider.json';

    /** Impostazioni del contenitore (sfondo e bordo della tile, stile tipografico del tema): restano quelle del sito. */
    const DEL_CONTENITORE = [ 'bg', 'border', 'border_hover', 'border_hover_duration', 'border_effect', 'border_effect_intensity',
        'border_effect_color2', 'border_effect_angle', 'border_effect_speed', 'typography_preset' ];

    private static function indice() {
        $f = OLOBUILD_PATH . self::INDICE;
        $d = is_readable( $f ) ? json_decode( (string) file_get_contents( $f ), true ) : null;
        return is_array( $d['scene'] ?? null ) ? $d['scene'] : [];
    }

    /** I temi del selettore, per id (solo quelli che vivono in questa installazione). */
    private static function temi() {
        if ( ! class_exists( 'Olobuild_Theme_Importer' ) ) {
            require_once OLOBUILD_PATH . 'includes/class-theme-importer.php';
        }
        $out = [];
        foreach ( Olobuild_Theme_Importer::get_themes() as $t ) {
            if ( empty( $t['id'] ) || ! empty( $t['is_remote_only'] ) || empty( $t['dir'] ) ) {
                continue;
            }
            $out[ (string) $t['id'] ] = $t;
        }
        return $out;
    }

    /** GET olobuild/v1/scene-slider: le card della libreria. */
    public static function rest_elenco() {
        $temi = self::temi();
        $lib  = Olobuild_Template_Library::library_url();
        $out  = [];
        foreach ( self::indice() as $s ) {
            $t = $temi[ (string) ( $s['tema'] ?? '' ) ] ?? null;
            if ( ! $t ) {
                continue;
            }
            $sfondo = str_replace( [ '{{OLOBUILD_LIBRARY}}', '{{OLOBUILD_URL}}' ], [ $lib, OLOBUILD_URL ], (string) ( $s['sfondo'] ?? '' ) );
            $out[]  = [
                'id'          => (string) $s['id'],
                'tema'        => (string) $t['id'],
                'nome'        => (string) ( $t['name'] ?? $t['id'] ),
                'descrizione' => (string) ( $t['description'] ?? '' ),
                'pagina'      => (string) ( $s['pagina'] ?? 'home' ),
                'tipo'        => (string) ( $s['tipo'] ?? 'classica' ),
                'schede'      => ! empty( $s['schede'] ),
                'slide'       => (int) ( $s['slide'] ?? 1 ),
                'livelli'     => (int) ( $s['livelli'] ?? 0 ),
                'titolo'      => (string) ( $s['titolo'] ?? '' ),
                // L'apertura della home si vede nell'anteprima del tema; le altre col loro sfondo.
                'anteprima'   => ! empty( $s['prima'] ) && ! empty( $t['screenshot'] ) ? (string) $t['screenshot'] : $sfondo,
                'in_cima'     => ! empty( $s['prima'] ),
                'video'       => ! empty( $s['video'] ),
                'edizione'    => (string) ( $t['edizione'] ?? '' ),
                'pro_ok'      => ! empty( $t['pro_ok'] ),
                'pro_url'     => (string) ( $t['pro_url'] ?? '' ),
                'created'     => (string) ( $t['created'] ?? '' ),
            ];
        }
        return rest_ensure_response( [ 'scene' => $out ] );
    }

    /** POST olobuild/v1/scene-slider/usa { id }: le impostazioni dello slider, pronte per questo sito. */
    public static function rest_usa( WP_REST_Request $request ) {
        $id    = sanitize_text_field( (string) $request->get_param( 'id' ) );
        $scena = null;
        foreach ( self::indice() as $s ) {
            if ( (string) ( $s['id'] ?? '' ) === $id ) {
                $scena = $s;
                break;
            }
        }
        $temi = self::temi();
        $t    = $scena ? ( $temi[ (string) $scena['tema'] ] ?? null ) : null;
        if ( ! $scena || ! $t ) {
            return new WP_Error( 'olobuild_scena', __( 'Questa scena non c’è più: aggiorna l’elenco.', 'olobuild' ), [ 'status' => 404 ] );
        }
        $dir = (string) $t['dir'];
        if ( 'pro' === ( $t['edizione'] ?? '' ) ) {
            if ( empty( $t['pro_ok'] ) ) {
                return Olobuild_Temi_Pro::errore_pro();
            }
            if ( ! empty( $t['da_scaricare'] ) ) {
                $scheda = json_decode( (string) file_get_contents( $dir . '/theme.json' ), true );
                $giu    = Olobuild_Temi_Pro::scarica( (string) $t['id'], is_array( $scheda ) ? $scheda : [] );
                if ( is_wp_error( $giu ) ) {
                    return $giu;
                }
                $dir = (string) $giu;
            }
        }
        // La pagina del tema: il nome del file sta in theme.json → pages (di solito uguale alla chiave).
        $meta    = json_decode( (string) file_get_contents( $dir . '/theme.json' ), true );
        $pagina  = sanitize_key( (string) $scena['pagina'] );
        $file    = sanitize_file_name( (string) ( $meta['pages'][ $pagina ]['template'] ?? $pagina ) ) . '.json';
        $nodi    = is_readable( $dir . '/' . $file ) ? json_decode( (string) file_get_contents( $dir . '/' . $file ), true ) : null;
        $tile_id = substr( $id, strrpos( $id, '/' ) + 1 );
        $tile    = is_array( $nodi ) ? self::trova( $nodi, $tile_id ) : null;
        if ( ! $tile || ! is_array( $tile['settings'] ?? null ) ) {
            return new WP_Error( 'olobuild_scena', __( 'Non trovo questa scena nelle pagine del tema.', 'olobuild' ), [ 'status' => 404 ] );
        }
        $settings = array_diff_key( $tile['settings'], array_flip( self::DEL_CONTENITORE ) );
        // I link alle pagine del tema qui non portano da nessuna parte: «#», da cambiare nell'editor.
        $settings = self::senza_pagine_del_tema( $settings );
        if ( function_exists( 'set_time_limit' ) ) {
            @set_time_limit( 180 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- alcuni hosting lo vietano: si continua col limite del server
        }
        $media  = Olobuild_Template_Library::instance()->import_file_media( [ 'settings' => $settings ], false );
        $fuori  = self::rimasti_nella_libreria( $media['dati']['settings'] );
        return rest_ensure_response( [
            'settings' => $media['dati']['settings'],
            'nome'     => (string) ( $t['name'] ?? $t['id'] ),
            // «fallite»: le foto e i video rimasti all'indirizzo della libreria. I disegni SVG restano lì apposta (la
            // Libreria media di WordPress non li accetta): contati a parte, non sono un errore (prova su olo03,
            // 24 nov: «9 file non copiati» erano il cruscotto e le spie di Cric).
            'media'    => [ 'copiate' => $media['copiate'], 'riusate' => $media['riusate'], 'fallite' => $fuori['file'], 'svg' => $fuori['svg'] ],
        ] );
    }

    /** I file della libreria remota ancora usati dallo slider: [ 'file' => foto e video, 'svg' => disegni ]. */
    private static function rimasti_nella_libreria( $settings ) {
        $base = Olobuild_Template_Library::library_url() . '/media/';
        $file = [];
        array_walk_recursive( $settings, function ( $v ) use ( &$file, $base ) {
            if ( is_string( $v ) && false !== strpos( $v, $base ) && preg_match_all( '#' . preg_quote( $base, '#' ) . '([A-Za-z0-9._-]+)#', $v, $m ) ) {
                foreach ( $m[1] as $f ) {
                    $file[ $f ] = true;
                }
            }
        } );
        $svg = count( array_filter( array_keys( $file ), function ( $f ) { return (bool) preg_match( '/\.svg$/i', $f ); } ) );
        return [ 'file' => count( $file ) - $svg, 'svg' => $svg ];
    }

    private static function trova( $o, $id ) {
        if ( ! is_array( $o ) ) {
            return null;
        }
        if ( ( $o['id'] ?? null ) === $id && 'proslider' === ( $o['type'] ?? '' ) ) {
            return $o;
        }
        foreach ( $o as $v ) {
            $r = self::trova( $v, $id );
            if ( $r ) {
                return $r;
            }
        }
        return null;
    }

    private static function senza_pagine_del_tema( $v ) {
        if ( is_array( $v ) ) {
            foreach ( $v as $k => $x ) {
                $v[ $k ] = self::senza_pagine_del_tema( $x );
            }
            return $v;
        }
        return is_string( $v ) && false !== strpos( $v, '{{PAGINA:' ) ? (string) preg_replace( '/\{\{PAGINA:[a-z0-9_-]+\}\}[^"\s]*/i', '#', $v ) : $v;
    }
}
