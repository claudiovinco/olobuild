<?php
/**
 * I colori dei disegni SVG del Pro Slider (1.4.1433).
 *
 * Utente, 24 nov 2026: «nessun CSS o cose hardcoded… amplia il software per gestire tutte le eccezioni che trovi».
 * 85 disegni dei temi (il cruscotto e le spie di Cric, la cassaforte di Combinazione, i cartamodelli di Sbieco) avevano i
 * colori fissi dentro il file: un'immagine che non segue la palette. Ora il livello ha «Colori del disegno»: ogni colore
 * del file si sostituisce con uno della palette.
 *
 * - Da dove: un file del sito (uploads, il plugin) o della libreria dei temi; nient'altro. Letto una volta e tenuto per
 *   30 giorni (transient).
 * - Pulito: solo gli elementi e gli attributi di un disegno (niente script, eventi, oggetti esterni, link fuori dal file).
 * - Ricolorato come IMMAGINE (data URI), coi valori della palette già scritti: i disegni hanno animazioni e stili
 *   interni coi nomi corti («@keyframes b», «svg{animation…}») che, stampati nella pagina, avrebbero animato ogni SVG del
 *   sito. Da immagine restano isolati e le loro animazioni girano lo stesso.
 * Senza DOMDocument (estensione xml) o col file irraggiungibile il livello resta un'immagine coi colori del file.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Olobuild_Disegni {

    const ELEMENTI = [ 'svg', 'g', 'defs', 'symbol', 'use', 'title', 'desc', 'path', 'rect', 'circle', 'ellipse', 'line', 'polyline',
        'polygon', 'text', 'tspan', 'textpath', 'lineargradient', 'radialgradient', 'stop', 'clippath', 'mask', 'pattern', 'marker',
        'filter', 'fegaussianblur', 'feoffset', 'feblend', 'fecolormatrix', 'fecomposite', 'feflood', 'femerge', 'femergenode',
        'femorphology', 'fedropshadow', 'feturbulence', 'fedisplacementmap', 'fecomponenttransfer', 'fefunca', 'fefuncr', 'fefuncg',
        'fefuncb', 'style', 'switch' ];
    const ATTRIBUTI_COLORE = [ 'fill', 'stroke', 'stop-color', 'flood-color', 'lighting-color', 'color' ];
    const HEX = '/#(?:[0-9a-fA-F]{6}|[0-9a-fA-F]{3})(?![0-9a-zA-Z])/';

    /** Il disegno pulito, o '' (indirizzo non ammesso, file irraggiungibile, non è un SVG). */
    public static function testo( $url ) {
        $url = esc_url_raw( trim( (string) $url ) );
        if ( '' === $url || ! preg_match( '/\.svg(\?.*)?$/i', $url ) || ! class_exists( 'DOMDocument' ) ) {
            return '';
        }
        $chiave = 'olo_disegno_' . md5( $url );
        $c      = get_transient( $chiave );
        if ( is_string( $c ) ) {
            return $c;
        }
        $grezzo = self::leggi( $url );
        $pulito = '' !== $grezzo ? self::pulisci( $grezzo ) : '';
        set_transient( $chiave, $pulito, '' === $pulito ? HOUR_IN_SECONDS : 30 * DAY_IN_SECONDS );
        return $pulito;
    }

    /** Il file: dal disco se sta nel sito (uploads, plugin), se no solo dalla libreria dei temi. */
    private static function leggi( $url ) {
        $senza = preg_replace( '/[?#].*$/', '', $url );
        $up    = wp_get_upload_dir();
        $posti = [
            [ (string) ( $up['baseurl'] ?? '' ), (string) ( $up['basedir'] ?? '' ) ],
            [ untrailingslashit( OLOBUILD_URL ), untrailingslashit( OLOBUILD_PATH ) ],
        ];
        foreach ( $posti as $p ) {
            $base = preg_replace( '#^https?:#', '', $p[0] );
            $qui  = preg_replace( '#^https?:#', '', $senza );
            if ( '' !== $base && 0 === strpos( $qui, $base . '/' ) ) {
                $file = realpath( $p[1] . substr( $qui, strlen( $base ) ) );
                $radice = realpath( $p[1] );
                if ( $file && $radice && 0 === strpos( $file, $radice ) && is_readable( $file ) && filesize( $file ) < 2 * MB_IN_BYTES ) {
                    return (string) file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- file locale del sito, già controllato sotto la sua radice
                }
                return '';
            }
        }
        $lib = class_exists( 'Olobuild_Template_Library' ) ? Olobuild_Template_Library::library_url() : '';
        if ( '' === $lib || 0 !== strpos( $senza, $lib . '/' ) ) {
            return '';
        }
        $r = wp_remote_get( $url, [ 'timeout' => 8, 'redirection' => 2 ] );
        if ( is_wp_error( $r ) || 200 !== (int) wp_remote_retrieve_response_code( $r ) ) {
            return '';
        }
        $corpo = (string) wp_remote_retrieve_body( $r );
        return strlen( $corpo ) < 2 * MB_IN_BYTES ? $corpo : '';
    }

    /** Solo gli elementi e gli attributi di un disegno. */
    public static function pulisci( $svg ) {
        $svg = preg_replace( '/<!DOCTYPE[^>]*>/i', '', (string) $svg );
        if ( false === stripos( $svg, '<svg' ) ) {
            return '';
        }
        $prima = libxml_use_internal_errors( true );
        $doc   = new DOMDocument();
        $ok    = $doc->loadXML( $svg, LIBXML_NONET | LIBXML_NOBLANKS );
        libxml_clear_errors();
        libxml_use_internal_errors( $prima );
        if ( ! $ok || ! $doc->documentElement || 'svg' !== strtolower( $doc->documentElement->localName ) ) {
            return '';
        }
        self::pulisci_nodo( $doc->documentElement );
        $out = $doc->saveXML( $doc->documentElement );
        return is_string( $out ) ? $out : '';
    }

    private static function pulisci_nodo( DOMElement $el ) {
        foreach ( iterator_to_array( $el->childNodes ) as $n ) {
            if ( $n instanceof DOMElement ) {
                if ( ! in_array( strtolower( $n->localName ), self::ELEMENTI, true ) ) {
                    $el->removeChild( $n );
                    continue;
                }
                self::pulisci_nodo( $n );
            } elseif ( $n instanceof DOMComment || $n instanceof DOMProcessingInstruction ) {
                $el->removeChild( $n );
            }
        }
        foreach ( iterator_to_array( $el->attributes ) as $a ) {
            $nome = strtolower( $a->nodeName );
            $val  = (string) $a->nodeValue;
            $via  = 0 === strpos( $nome, 'on' )
                || preg_match( '/javascript:|vbscript:|data:(?!image\/(png|jpe?g|gif|webp))|expression\s*\(|@import/i', $val )
                || ( in_array( $nome, [ 'href', 'xlink:href' ], true ) && '#' !== substr( trim( $val ), 0, 1 ) )
                || ( preg_match( '/url\s*\(\s*[\'"]?\s*(?!#)/i', $val ) );
            if ( $via ) {
                $el->removeAttributeNode( $a );
            }
        }
        if ( 'style' === strtolower( $el->localName ) && preg_match( '/@import|url\s*\(\s*[\'"]?\s*(?!#)|expression\s*\(|javascript:/i', $el->textContent ) ) {
            $el->textContent = '';
        }
    }

    /** I colori del disegno, dal più usato: [ [ 'colore' => '#B7C6D1', 'volte' => 68 ], … ]. */
    public static function colori( $svg ) {
        $conta = [];
        if ( preg_match_all( self::HEX, (string) $svg, $m ) ) {
            foreach ( $m[0] as $h ) {
                $h           = self::norm( $h );
                $conta[ $h ] = ( $conta[ $h ] ?? 0 ) + 1;
            }
        }
        arsort( $conta );
        $out = [];
        foreach ( $conta as $c => $n ) {
            $out[] = [ 'colore' => $c, 'volte' => $n ];
        }
        return $out;
    }

    public static function norm( $h ) {
        $h = strtoupper( ltrim( (string) $h, '#' ) );
        if ( 3 === strlen( $h ) ) {
            $h = $h[0] . $h[0] . $h[1] . $h[1] . $h[2] . $h[2];
        }
        return '#' . $h;
    }

    /**
     * Un colore della palette scritto per DENTRO un file: var(--olo-color-x[, riserva]) → il valore di quel colore
     * (anche dentro color-mix). Il disegno ricolorato è un'immagine a sé, dove le variabili della pagina non arrivano.
     */
    public static function risolvi( $valore, $valori ) {
        $v = (string) $valore;
        for ( $i = 0; $i < 6 && false !== strpos( $v, 'var(' ); $i++ ) {
            $v = preg_replace_callback( '/var\(\s*--olo-color-([a-z0-9_-]+)\s*(?:,\s*([^()]*(?:\([^()]*\))?[^()]*))?\)/i', function ( $m ) use ( $valori ) {
                $id = strtolower( $m[1] );
                if ( isset( $valori[ $id ] ) && '' !== $valori[ $id ] ) {
                    return $valori[ $id ];
                }
                return isset( $m[2] ) && '' !== trim( $m[2] ) ? trim( $m[2] ) : 'currentColor';
            }, $v );
        }
        return $v;
    }

    /**
     * Il disegno coi colori nuovi, come immagine (data URI): isolato dalla pagina, così le sue animazioni e i suoi
     * stili interni (@keyframes «b», «svg{…}») non toccano il resto del sito, e gli id non si scontrano.
     *
     * @param string $svg    Disegno pulito (testo()).
     * @param array  $mappa  [ '#B7C6D1' => 'var(--olo-color-secondary)', … ] (valori già controllati).
     * @param array  $valori I colori della palette (Olobuild_Style_System::valori_colori()).
     * @return string data:image/svg+xml;base64,… o ''.
     */
    public static function immagine( $svg, $mappa, $valori ) {
        $map = [];
        foreach ( (array) $mappa as $da => $a ) {
            $r = self::risolvi( $a, $valori );
            if ( '' !== $r && ! preg_match( '/[;{}<>"]/', $r ) ) {
                $map[ self::norm( $da ) ] = $r;
            }
        }
        if ( ! $map ) {
            return '';
        }
        $out = self::ricolora( $svg, $map );
        return '' !== $out ? 'data:image/svg+xml;base64,' . base64_encode( $out ) : ''; // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- un disegno SVG pulito come immagine, non codice nascosto
    }

    /** Sostituisce i colori nel disegno: attributi di colore (spostati nello stile), stili in linea e <style>. */
    public static function ricolora( $svg, $map ) {
        $prima = libxml_use_internal_errors( true );
        $doc   = new DOMDocument();
        $ok    = $doc->loadXML( (string) $svg, LIBXML_NONET );
        libxml_clear_errors();
        libxml_use_internal_errors( $prima );
        if ( ! $ok || ! $doc->documentElement ) {
            return '';
        }
        $sost = function ( $testo ) use ( $map ) {
            return preg_replace_callback( self::HEX, function ( $m ) use ( $map ) {
                $h = self::norm( $m[0] );
                return $map[ $h ] ?? $m[0];
            }, (string) $testo );
        };
        $xp = new DOMXPath( $doc );
        foreach ( $xp->query( '//*' ) as $el ) {
            /** @var DOMElement $el */
            if ( 'style' === strtolower( $el->localName ) ) {
                $el->textContent = $sost( $el->textContent );
                continue;
            }
            $stile = (string) $el->getAttribute( 'style' );
            foreach ( self::ATTRIBUTI_COLORE as $at ) {
                if ( ! $el->hasAttribute( $at ) ) {
                    continue;
                }
                $v = trim( (string) $el->getAttribute( $at ) );
                if ( preg_match( self::HEX, $v ) && isset( $map[ self::norm( $v ) ] ) ) {
                    // nello stile, dove color-mix() vale di sicuro (negli attributi di presentazione dipende dal browser)
                    $el->removeAttribute( $at );
                    $stile = rtrim( $stile, '; ' ) . ( '' !== $stile ? ';' : '' ) . $at . ':' . $map[ self::norm( $v ) ];
                }
            }
            if ( '' !== $stile ) {
                $el->setAttribute( 'style', $sost( $stile ) );
            }
        }
        $out = $doc->saveXML( $doc->documentElement );
        return is_string( $out ) ? $out : '';
    }

    /** La rotta dell'editor: il disegno pulito e i suoi colori (per «Colori del disegno» e per la tela). */
    public static function rest( WP_REST_Request $request ) {
        $src = (string) $request->get_param( 'src' );
        $svg = self::testo( $src );
        if ( '' === $svg ) {
            return new WP_Error( 'olobuild_disegno', __( 'Questo disegno non si può leggere: resta coi suoi colori.', 'olobuild' ), [ 'status' => 404 ] );
        }
        return rest_ensure_response( [ 'svg' => $svg, 'colori' => self::colori( $svg ) ] );
    }
}
