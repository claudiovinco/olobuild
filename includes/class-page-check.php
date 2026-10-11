<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Controllo della pagina allegata a un modulo (la prova d'ingresso OLOfounder, 1.4.685).
 *
 * Il file .json esportato dal builder deve essere una pagina vera, fatta con le tile ammesse e scritta da chi la
 * manda. «Scritta da chi la manda» si misura sui testi: quelli rimasti come arrivano (tile appena trascinate, blocchi
 * della libreria, temi, pagina iniziale della sandbox) si riconoscono dalle impronte in assets/data/impronte-testi.bin,
 * generate a ogni build da scripts/impronte-testi.mjs con la STESSA normalizzazione di parole() e terne().
 *
 * Un testo è «di partenza» se almeno l'80% delle sue terne di parole consecutive è nelle impronte: cambiare una
 * parola in un paragrafo non basta a farlo proprio. La quota dei testi propri pesa i caratteri, così le etichette
 * corte contano poco. Senza il file delle impronte il controllo dei testi non blocca nessuno (restano quelli sulle
 * dimensioni e sulle tile).
 */
class Olobuild_Page_Check {

    /** Le tile della sandbox di try.olotheme.com (olo-sandbox, Olo_Sandbox_Config::allowed_tiles()). */
    const TILE_SANDBOX = [
        'spacer', 'divider', 'headline', 'text-block', 'content', 'button', 'image', 'icon', 'iconbox', 'social',
        'list', 'iconlist', 'desclist', 'gallery', 'lightbox', 'imgcompare', 'video', 'hero', 'accordion', 'panel',
        'switcher', 'flipcard', 'carousel', 'marquee', 'counter', 'countercircle', 'countdown', 'hotspot', 'alert',
        'breadcrumbs', 'section-header', 'cta-banner', 'process-steps', 'info-cards', 'statstrip', 'badge',
        'quotation', 'animatedheading', 'scrubtext', 'slideshow', 'asciiviz', 'pricing', 'testimonial', 'team',
        'progress', 'timeline', 'chart', 'scratchfx', 'particlefx', 'goo',
    ];

    /** I contenitori della griglia: non sono tile. */
    const CONTENITORI = [ 'section', 'row', 'column', 'inner-columns' ];

    /** Un testo è «di partenza» da questa quota di terne riconosciute in su. */
    const QUOTA_DI_PARTENZA = 0.8;

    /** Parole di testo che una pagina deve avere almeno. */
    const PAROLE_MINIME = 40;

    /** Chiavi delle impostazioni che non portano testo da leggere (indirizzi, media, stili, comportamento). */
    const CHIAVI_TECNICHE = '/(^|_)(url|urls|link|links|href|src|image|img|images|video|poster|icon|svg|css|class|classes|id|anchor|font|family|color|colour|bg|background|gradient|shadow|border|radius|padding|margin|width|height|size|align|alignment|position|animation|anim|easing|duration|delay|preset|style|variant|mode|type|layout|effect|target|rel|tag|aspect|ratio|opacity|blend|filter|mask|transform|order|columns|gap|map|lat|lng|zoom|email|phone|tel|date|time|format|unit|direction|speed|trigger|source|query|code|html|json|data)$/i';

    /** @var string|null Impronte caricate (uint32 little-endian, ordinate). */
    private static $impronte = null;

    /**
     * Le parole di un testo, normalizzate come in scripts/impronte-testi.mjs: senza tag ed entità, minuscole,
     * composte (NFC), solo lettere e cifre.
     *
     * @param string $testo
     * @return string[]
     */
    public static function parole( $testo ) {
        $s = preg_replace( '/<[^>]*>/', ' ', (string) $testo );
        $s = preg_replace( '/&[#a-z0-9]+;/i', ' ', $s );
        $s = mb_strtolower( $s, 'UTF-8' );
        if ( class_exists( 'Normalizer' ) ) {
            $n = Normalizer::normalize( $s, Normalizer::FORM_C );
            if ( false !== $n ) {
                $s = $n;
            }
        }
        $s = trim( (string) preg_replace( '/[^\p{L}\p{N}]+/u', ' ', $s ) );
        return '' === $s ? [] : explode( ' ', $s );
    }

    /**
     * Le terne di parole consecutive (la coppia, per i testi di due parole).
     *
     * @param string[] $parole
     * @return string[]
     */
    public static function terne( array $parole ) {
        $n = count( $parole );
        if ( $n < 2 ) {
            return [];
        }
        if ( 2 === $n ) {
            return [ $parole[0] . ' ' . $parole[1] ];
        }
        $out = [];
        for ( $i = 0; $i + 2 < $n; $i++ ) {
            $out[] = $parole[ $i ] . ' ' . $parole[ $i + 1 ] . ' ' . $parole[ $i + 2 ];
        }
        return $out;
    }

    /** Il file delle impronte ('' se manca). */
    private static function impronte() {
        if ( null === self::$impronte ) {
            $file           = OLOBUILD_PATH . 'assets/data/impronte-testi.bin';
            $dati           = is_readable( $file ) ? file_get_contents( $file ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- file binario locale del plugin
            self::$impronte = is_string( $dati ) ? $dati : '';
        }
        return self::$impronte;
    }

    /** L'impronta è fra quelle dei testi di partenza? Ricerca binaria sul file, senza caricarlo in un array. */
    private static function conosciuta( $impronta ) {
        $dati = self::impronte();
        $hi   = intdiv( strlen( $dati ), 4 ) - 1;
        $lo   = 0;
        while ( $lo <= $hi ) {
            $mid = ( $lo + $hi ) >> 1;
            $v   = unpack( 'V', $dati, $mid * 4 )[1];
            if ( $v === $impronta ) {
                return true;
            }
            if ( $v < $impronta ) {
                $lo = $mid + 1;
            } else {
                $hi = $mid - 1;
            }
        }
        return false;
    }

    /** I testi delle impostazioni di una tile, saltando le chiavi tecniche. */
    private static function testi_da( $valore, array &$out ) {
        if ( is_array( $valore ) ) {
            foreach ( $valore as $k => $x ) {
                if ( is_string( $k ) && preg_match( self::CHIAVI_TECNICHE, $k ) ) {
                    continue;
                }
                self::testi_da( $x, $out );
            }
            return;
        }
        if ( ! is_string( $valore ) || strlen( $valore ) < 5 ) {
            return;
        }
        $v = trim( $valore );
        if ( preg_match( '#://|^/|^www\.|^\#[0-9a-f]{3,8}$|^(rgba?|hsla?|var|calc|linear-|radial-|color-mix)\(#i', $v ) ) {
            return;
        }
        $parole = self::parole( $v );
        if ( count( $parole ) >= 2 ) {
            $out[] = [ 'testo' => $v, 'parole' => $parole ];
        }
    }

    /** Tile e sezioni della pagina, e i testi tile per tile. */
    private static function esplora( array $nodi, $profondita, array &$stato ) {
        foreach ( $nodi as $nodo ) {
            if ( ! is_array( $nodo ) ) {
                continue;
            }
            $tipo = isset( $nodo['type'] ) ? (string) $nodo['type'] : '';
            if ( 0 === $profondita ) {
                $stato['sezioni']++;
            }
            if ( '' !== $tipo && ! in_array( $tipo, self::CONTENITORI, true ) ) {
                $stato['tile']++;
                $stato['tipi'][ $tipo ] = true;
                $testi = [];
                self::testi_da( $nodo['settings'] ?? [], $testi );
                foreach ( $testi as $t ) {
                    $stato['testi'][] = $t;
                }
            }
            if ( ! empty( $nodo['children'] ) && is_array( $nodo['children'] ) ) {
                self::esplora( $nodo['children'], $profondita + 1, $stato );
            }
        }
    }

    /** Un testo da citare nel messaggio: senza tag, una riga, al massimo 60 caratteri. */
    private static function citazione( $testo ) {
        $s = html_entity_decode( wp_strip_all_tags( (string) $testo ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
        $s = trim( (string) preg_replace( '/\s+/u', ' ', $s ) );
        if ( mb_strlen( $s ) > 60 ) {
            $s = rtrim( mb_substr( $s, 0, 57 ) ) . '…';
        }
        return '«' . $s . '»';
    }

    /**
     * Controlla il file di una pagina.
     *
     * @param string $percorso File .json caricato.
     * @param array  $opz      ambito ('sandbox'|'all'), min_tile, min_sezioni, min_personale (0-100).
     * @return array ok, problemi (frasi per chi l'ha mandata), riepilogo (per chi valuta), tile, sezioni, personale.
     */
    public static function controlla( $percorso, array $opz ) {
        $esito = [ 'ok' => false, 'problemi' => [], 'riepilogo' => '', 'tile' => 0, 'sezioni' => 0, 'personale' => 0 ];

        $json = is_readable( $percorso ) ? file_get_contents( $percorso ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- file appena caricato dal modulo
        $dati = is_string( $json ) ? json_decode( $json, true ) : null;
        $nodi = is_array( $dati ) && ( $dati['olo_export'] ?? '' ) === 'template' ? ( $dati['content'] ?? null ) : null;
        if ( ! is_array( $nodi ) || empty( $nodi ) ) {
            $esito['problemi'][] = olobuild_t( 'Il file allegato non è una pagina esportata da OLObuild: scaricala dal builder con «Esporta template JSON».' );
            return $esito;
        }

        $stato = [ 'sezioni' => 0, 'tile' => 0, 'tipi' => [], 'testi' => [] ];
        self::esplora( $nodi, 0, $stato );
        $esito['tile']    = $stato['tile'];
        $esito['sezioni'] = $stato['sezioni'];

        // Tile ammesse
        if ( 'all' !== ( $opz['ambito'] ?? 'sandbox' ) ) {
            $fuori = array_values( array_diff( array_keys( $stato['tipi'] ), self::TILE_SANDBOX ) );
            if ( $fuori ) {
                $esito['problemi'][] = sprintf(
                    /* translators: %s: elenco dei tipi di tile */
                    olobuild_t( 'La pagina contiene tile che nella sandbox non ci sono (%s): la pagina va fatta nella sandbox.' ),
                    implode( ', ', array_slice( $fuori, 0, 5 ) )
                );
            }
        }

        // Dimensioni
        $min_tile    = max( 1, absint( $opz['min_tile'] ?? 8 ) );
        $min_sezioni = max( 1, absint( $opz['min_sezioni'] ?? 3 ) );
        if ( $stato['tile'] < $min_tile || $stato['sezioni'] < $min_sezioni ) {
            $esito['problemi'][] = sprintf(
                /* translators: 1: tile della pagina, 2: sezioni della pagina, 3: tile minime, 4: sezioni minime */
                olobuild_t( 'La pagina è troppo corta: ha %1$d tile in %2$d sezioni, ne servono almeno %3$d in %4$d.' ),
                $stato['tile'], $stato['sezioni'], $min_tile, $min_sezioni
            );
        }

        // Testi propri
        $tot       = 0;
        $propri    = 0;
        $parole    = 0;
        $esempi    = [];
        $controlla = '' !== self::impronte();
        foreach ( $stato['testi'] as $t ) {
            $terne = self::terne( $t['parole'] );
            $peso  = mb_strlen( implode( ' ', $t['parole'] ) );
            $tot  += $peso;
            $parole += count( $t['parole'] );
            $note  = 0;
            if ( $controlla ) {
                foreach ( $terne as $x ) {
                    if ( self::conosciuta( crc32( $x ) ) ) {
                        $note++;
                    }
                }
            }
            if ( $terne && $note / count( $terne ) >= self::QUOTA_DI_PARTENZA ) {
                if ( count( $esempi ) < 3 ) {
                    $esempi[] = self::citazione( $t['testo'] );
                }
            } else {
                $propri += $peso;
            }
        }
        $esito['personale'] = $tot > 0 ? (int) floor( 100 * $propri / $tot ) : 0;
        $min_personale      = min( 100, absint( $opz['min_personale'] ?? 50 ) );

        if ( $parole < self::PAROLE_MINIME ) {
            $esito['problemi'][] = olobuild_t( 'La pagina ha troppo poco testo: servono titoli e paragrafi scritti da te.' );
        } elseif ( $esito['personale'] < $min_personale ) {
            $esito['problemi'][] = sprintf(
                /* translators: 1: percentuale dei testi propri, 2: percentuale minima, 3: esempi di testi da riscrivere */
                olobuild_t( 'La pagina è ancora troppo vicina ai contenuti di partenza: solo il %1$d%% dei testi è tuo, ne serve almeno il %2$d%%. Riscrivi con parole tue i testi rimasti come arrivano, per esempio %3$s.' ),
                $esito['personale'], $min_personale, implode( ', ', $esempi )
            );
        }

        $esito['ok']        = empty( $esito['problemi'] );
        $esito['riepilogo'] = sprintf(
            /* translators: 1: tile, 2: sezioni, 3: percentuale dei testi propri */
            olobuild_t( '%1$d tile in %2$d sezioni, %3$d%% di testi propri' ),
            $esito['tile'], $esito['sezioni'], $esito['personale']
        );
        return $esito;
    }
}
