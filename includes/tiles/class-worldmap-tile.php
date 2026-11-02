<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Mappa del mondo — i confini degli stati e nient'altro: ogni stato è trasparente, nel primo colore
 * («In arrivo», giallo) o nel secondo («Coperto», verde). Si sceglie nel builder, Contenuto → Stati, cliccando
 * sulla cartina: un clic il primo colore, due il secondo, tre di nuovo trasparente. Nata per mostrare quanti
 * stati copre il programma OLOfounder (13 ott 2026), vale per qualunque «dove siamo».
 *
 * Dati: assets/data/mappa-mondo.json (scripts/mappa-mondo.mjs): Natural Earth 1:110m in proiezione Natural Earth I,
 * un tracciato per stato in un viewBox largo 1000, Antartide esclusa, nomi CLDR in it/en/fr/de/es; gli stati che a
 * questa scala non hanno contorno (San Marino, Malta, Singapore…) sono puntini. Lo stesso file lo legge il
 * controllo del builder: una fonte sola. Salvato: `stati` = { "IT": 2, "FR": 1 } (codici ISO a due lettere).
 */
class Olobuild_WorldMap_Tile extends Olobuild_Tile_Base {

    protected $type     = 'worldmap';
    protected $name     = 'Mappa del mondo';
    protected $icon     = 'dashicons-admin-site-alt3';
    protected $category = 'media';
    protected $defaults = [
        'stati'               => [],
        'mostra_legenda'      => true,
        'mostra_numeri'       => true,
        'etichetta_1'         => 'In arrivo',
        'etichetta_2'         => 'Coperto',
        'colore_1'            => 'var(--olo-color-warning, #F59E0B)',
        'colore_2'            => 'var(--olo-color-success, #10B981)',
        'colore_vuoto'        => '',
        'colore_confini'      => '',
        'spessore_confini'    => 0.6,
        'inquadratura'        => 'mondo',
        'legenda_posizione'   => 'sotto',
        'legenda_allineamento' => 'center',
        'legenda_font_family' => '',
        'legenda_font_size'   => '',
        'legenda_font_weight' => '',
        'legenda_color'       => '',
    ];

    const LINGUE = [ 'it', 'en', 'fr', 'de', 'es' ];

    public function get_controls() { return []; }

    /** I dati della cartina (cache nella richiesta); [] se il file manca. */
    private static function dati() {
        static $dati = null;
        if ( null === $dati ) {
            $file = OLOBUILD_PATH . 'assets/data/mappa-mondo.json';
            $dati = is_readable( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : null; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- file del plugin
            if ( ! is_array( $dati ) || empty( $dati['stati'] ) ) {
                $dati = [];
            }
        }
        return $dati;
    }

    /** Lingua dei nomi degli stati: quella di OLOlang se c'è, se no quella del sito; fuori dalle cinque, l'inglese. */
    private function lingua() {
        if ( class_exists( 'Olo_Lang_Language' ) ) {
            $l = (string) Olo_Lang_Language::detect_current_lang();
        } else {
            $l = function_exists( 'determine_locale' ) ? determine_locale() : get_locale();
        }
        $l = strtolower( substr( (string) $l, 0, 2 ) );
        return in_array( $l, self::LINGUE, true ) ? $l : 'en';
    }

    /** Gli stati scelti, puliti: codice di due lettere → 1 o 2 (gli altri valori si scartano). */
    private function stati_scelti( $stati ) {
        $out = [];
        foreach ( is_array( $stati ) ? $stati : [] as $c => $v ) {
            $c = strtoupper( (string) $c );
            $v = (int) $v;
            if ( preg_match( '/^[A-Z]{2}$/', $c ) && ( 1 === $v || 2 === $v ) ) {
                $out[ $c ] = $v;
            }
        }
        return $out;
    }

    /**
     * Il riquadro dei paesi colorati (1.4.947, «Inquadratura: solo i paesi colorati»): per ogni stato il pezzo di
     * territorio più grande e le isole vicine, non i territori d'oltremare (con la Guyana la Francia allargava la
     * cartina fino al Sud America); più i puntini colorati. Un margine attorno, una larghezza minima, proporzioni
     * fra 1,6:1 e 2,6:1. Null se non c'è niente di colorato.
     */
    private static function riquadro( $dati, $scelti ) {
        $box = null;
        $unisci = static function ( $b ) use ( &$box ) {
            $box = null === $box ? $b : [ min( $box[0], $b[0] ), min( $box[1], $b[1] ), max( $box[2], $b[2] ), max( $box[3], $b[3] ) ];
        };
        foreach ( $dati['stati'] as $st ) {
            if ( empty( $scelti[ (string) ( $st['c'] ?? '' ) ] ) ) {
                continue;
            }
            // i pezzi del tracciato: M assoluto, l relativi, z chiude
            $pezzi = [];
            foreach ( preg_split( '/(?=M)/', (string) $st['d'], -1, PREG_SPLIT_NO_EMPTY ) as $p ) {
                preg_match_all( '/-?\d*\.?\d+(?:e-?\d+)?/i', $p, $m );
                $n = array_map( 'floatval', $m[0] );
                if ( count( $n ) < 2 ) {
                    continue;
                }
                $x = $n[0];
                $y = $n[1];
                $b = [ $x, $y, $x, $y ];
                for ( $i = 2; $i + 1 < count( $n ); $i += 2 ) {
                    $x += $n[ $i ];
                    $y += $n[ $i + 1 ];
                    $b = [ min( $b[0], $x ), min( $b[1], $y ), max( $b[2], $x ), max( $b[3], $y ) ];
                }
                $pezzi[] = $b;
            }
            if ( ! $pezzi ) {
                continue;
            }
            usort( $pezzi, static function ( $a, $b ) {
                return ( ( $b[2] - $b[0] ) * ( $b[3] - $b[1] ) ) <=> ( ( $a[2] - $a[0] ) * ( $a[3] - $a[1] ) );
            } );
            $grande = $pezzi[0];
            $unisci( $grande );
            $vicino = max( 12, 0.6 * max( $grande[2] - $grande[0], $grande[3] - $grande[1] ) );
            foreach ( array_slice( $pezzi, 1 ) as $b ) {
                if ( $b[0] < $grande[2] + $vicino && $b[2] > $grande[0] - $vicino && $b[1] < $grande[3] + $vicino && $b[3] > $grande[1] - $vicino ) {
                    $unisci( $b );
                }
            }
        }
        foreach ( (array) ( $dati['puntini'] ?? [] ) as $st ) {
            if ( ! empty( $scelti[ (string) ( $st['c'] ?? '' ) ] ) ) {
                $unisci( [ floatval( $st['x'] ), floatval( $st['y'] ), floatval( $st['x'] ), floatval( $st['y'] ) ] );
            }
        }
        if ( null === $box ) {
            return null;
        }
        list( , , $vw, $vh ) = array_map( 'floatval', preg_split( '/\s+/', trim( (string) $dati['viewBox'] ) ) );
        $w  = max( 70, ( $box[2] - $box[0] ) * 1.24 + 8 );
        $h  = max( 30, ( $box[3] - $box[1] ) * 1.24 + 8 );
        $w  = max( $w, $h * 1.6 );
        $h  = max( $h, $w / 2.6 );
        $cx = ( $box[0] + $box[2] ) / 2;
        $cy = ( $box[1] + $box[3] ) / 2;
        $w  = min( $w, $vw );
        $h  = min( $h, $vh );
        $x  = max( 0, min( $vw - $w, $cx - $w / 2 ) );
        $y  = max( 0, min( $vh - $h, $cy - $h / 2 ) );
        return [ round( $x, 1 ), round( $y, 1 ), round( $w, 1 ), round( $h, 1 ) ];
    }

    public function render( $settings, $style = [] ) {
        $s    = wp_parse_args( $settings, $this->defaults );
        $dati = self::dati();
        if ( empty( $dati ) ) {
            return ! empty( $s['_builder_mode'] ) ? '<p>' . esc_html( olobuild_t( 'I dati della mappa del mondo non sono stati trovati.' ) ) . '</p>' : '';
        }
        $uid    = 'owm-' . $this->chiave_stabile( $settings );
        $lingua = $this->lingua();
        $scelti = $this->stati_scelti( $s['stati'] );

        // Contati solo gli stati che la cartina disegna (un codice che non c'è non fa numero)
        $conta = [ 1 => 0, 2 => 0 ];
        $voce  = function ( $st ) use ( $scelti, $lingua, &$conta ) {
            $c = (string) ( $st['c'] ?? '' );
            $v = $scelti[ $c ] ?? 0;
            if ( $v ) {
                $conta[ $v ]++;
            }
            $nome = (string) ( $st['n'][ $lingua ] ?? ( $st['n']['en'] ?? $c ) );
            return [ $v, $nome, $c ];
        };
        $tracciati = '';
        foreach ( $dati['stati'] as $st ) {
            list( $v, $nome, $c ) = $voce( $st );
            $tracciati .= '<path class="owm-s' . ( $v ? ' owm-' . $v : '' ) . '" data-c="' . esc_attr( $c ) . '" d="' . esc_attr( (string) $st['d'] ) . '"><title>' . esc_html( $nome ) . '</title></path>';
        }
        // «Solo i paesi colorati»: la cartina si stringe sul loro riquadro; i puntini restano grandi come prima a vedersi
        $vista  = (string) $dati['viewBox'];
        $raggio = 2.6;
        if ( 'colorati' === (string) $s['inquadratura'] ) {
            $rq = self::riquadro( $dati, $scelti );
            if ( $rq ) {
                $vista  = implode( ' ', $rq );
                $raggio = round( 2.6 * max( 0.25, $rq[2] / 1000 ), 2 );
            }
        }
        $puntini = '';
        foreach ( (array) ( $dati['puntini'] ?? [] ) as $st ) {
            list( $v, $nome, $c ) = $voce( $st );
            $puntini .= '<circle class="owm-s owm-p' . ( $v ? ' owm-' . $v : '' ) . '" data-c="' . esc_attr( $c ) . '" cx="' . esc_attr( (string) floatval( $st['x'] ) ) . '" cy="' . esc_attr( (string) floatval( $st['y'] ) ) . '" r="' . $raggio . '"><title>' . esc_html( $nome ) . '</title></circle>';
        }

        $et1 = trim( (string) $s['etichetta_1'] );
        $et2 = trim( (string) $s['etichetta_2'] );
        // Per chi usa un lettore di schermo la cartina è un'immagine: il suo nome dice i numeri della legenda
        $parti = [];
        if ( $conta[2] ) {
            $parti[] = ( '' !== $et2 ? $et2 : olobuild_t( 'Secondo colore' ) ) . ': ' . $conta[2];
        }
        if ( $conta[1] ) {
            $parti[] = ( '' !== $et1 ? $et1 : olobuild_t( 'Primo colore' ) ) . ': ' . $conta[1];
        }
        $titolo = olobuild_t( 'Mappa del mondo' ) . ( $parti ? ' — ' . implode( ', ', $parti ) : '' );

        $c1     = $this->safe_color_css( $s['colore_1'] ) ?: 'var(--olo-color-warning, #F59E0B)';
        $c2     = $this->safe_color_css( $s['colore_2'] ) ?: 'var(--olo-color-success, #10B981)';
        $vuoto  = $this->safe_color_css( $s['colore_vuoto'] ) ?: 'transparent';
        // Confini di serie: il colore del testo, velato (chiari su fondo scuro, scuri su fondo chiaro)
        $confini  = $this->safe_color_css( $s['colore_confini'] ) ?: 'color-mix(in srgb, currentColor 40%, transparent)';
        $spessore = max( 0, min( 3, floatval( $s['spessore_confini'] ) ) );
        $allinea  = in_array( $s['legenda_allineamento'], [ 'left', 'center', 'right' ], true ) ? $s['legenda_allineamento'] : 'center';
        $flex     = [ 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' ][ $allinea ];
        $sopra    = 'sopra' === $s['legenda_posizione'];

        $leg_decl = [];
        $ff       = $this->resolve_font_family( (string) $s['legenda_font_family'] );
        if ( '' !== $ff ) {
            $leg_decl[] = 'font-family:' . $ff;
        }
        $fs = Olobuild_Tile_Utils::css_len( $s['legenda_font_size'] );
        if ( '' !== $fs ) {
            $leg_decl[] = 'font-size:' . $fs;
        }
        $fw = $this->font_weight_css( $s['legenda_font_weight'] );
        if ( '' !== $fw ) {
            $leg_decl[] = 'font-weight:' . $fw;
        }
        $lc = $this->safe_color_css( $s['legenda_color'] );
        if ( '' !== $lc ) {
            $leg_decl[] = 'color:' . $lc;
        }

        $legenda = '';
        if ( ! empty( $s['mostra_legenda'] ) && ( $conta[1] || $conta[2] ) ) {
            $legenda = '<ul class="owm-legenda">';
            foreach ( [ 2 => $et2, 1 => $et1 ] as $n => $etichetta ) {
                if ( ! $conta[ $n ] ) {
                    continue;
                }
                $legenda .= '<li><span class="owm-chiave owm-k' . $n . '" aria-hidden="true"></span>' . esc_html( $etichetta )
                    . ( ! empty( $s['mostra_numeri'] ) ? ' <strong>' . esc_html( number_format_i18n( $conta[ $n ] ) ) . '</strong>' : '' ) . '</li>';
            }
            $legenda .= '</ul>';
        }

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist with fixed var() fallbacks, floatval() for the stroke width, a fixed map for the alignment, resolve_font_family()/css_len()/font_weight_css() for the legend; $uid is an internal md5-based id. ?>
        <style>
            .<?php echo $uid; ?>{--owm-1:<?php echo $c1; ?>;--owm-2:<?php echo $c2; ?>;--owm-0:<?php echo $vuoto; ?>;--owm-b:<?php echo $confini; ?>;display:flex;flex-direction:column;gap:0.75rem;}
            <?php // ritagliata sui paesi colorati taglia al suo bordo: il resto del mondo si allargava fuori dalla cartina ?>
            .<?php echo $uid; ?> .owm-mappa{display:block;width:100%;height:auto;overflow:<?php echo $vista === (string) $dati['viewBox'] ? 'visible' : 'hidden'; ?>;}
            .<?php echo $uid; ?> .owm-s{fill:var(--owm-0);stroke:var(--owm-b);stroke-width:<?php echo $spessore; ?>px;vector-effect:non-scaling-stroke;stroke-linejoin:round;transition:fill .2s ease;}
            .<?php echo $uid; ?> .owm-1{fill:var(--owm-1);}
            .<?php echo $uid; ?> .owm-2{fill:var(--owm-2);}
            <?php // Al passaggio uno stato vuoto prende un velo del colore dei confini (non del testo): coi confini chiari scelti su una pagina scura, col testo ereditato scuro, il velo restava invisibile ?>
            .<?php echo $uid; ?> .owm-s:hover{fill:color-mix(in srgb, var(--owm-b) 45%, var(--owm-0));}
            .<?php echo $uid; ?> .owm-1:hover{fill:color-mix(in srgb, var(--owm-1) 80%, currentColor);}
            .<?php echo $uid; ?> .owm-2:hover{fill:color-mix(in srgb, var(--owm-2) 80%, currentColor);}
            .<?php echo $uid; ?> .owm-legenda{list-style:none;margin:0;padding:0;display:flex;flex-wrap:wrap;justify-content:<?php echo $flex; ?>;gap:0.5rem 1.5rem;line-height:1.4;<?php echo implode( ';', $leg_decl ); ?>}
            .<?php echo $uid; ?> .owm-legenda li{display:inline-flex;align-items:center;gap:0.5em;margin:0;}
            .<?php echo $uid; ?> .owm-chiave{flex:none;width:0.8em;height:0.8em;border-radius:50%;}
            .<?php echo $uid; ?> .owm-k1{background:var(--owm-1);}
            .<?php echo $uid; ?> .owm-k2{background:var(--owm-2);}
            .<?php echo $uid; ?> .owm-legenda strong{font-weight:700;font-variant-numeric:tabular-nums;}
        </style>
        <div class="olo-worldmap <?php echo $uid; ?>">
            <?php if ( $sopra ) echo $legenda; ?>
            <svg class="owm-mappa" viewBox="<?php echo esc_attr( $vista ); ?>" role="img" aria-labelledby="<?php echo $uid; ?>-t" preserveAspectRatio="xMidYMid meet">
                <title id="<?php echo $uid; ?>-t"><?php echo esc_html( $titolo ); ?></title>
                <g class="owm-stati"><?php echo $tracciati; ?></g>
                <g class="owm-puntini"><?php echo $puntini; ?></g>
            </svg>
            <?php if ( ! $sopra ) echo $legenda; ?>
        </div>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php
        return ob_get_clean();
    }
}
