<?php
/**
 * Tile Pianta della sala — la sala di un teatro disegnata da pochi numeri, coi settori di prezzo.
 *
 * La pianta nasce in PHP (svg): il palcoscenico, la buca dell'orchestra, la platea a file di posti e,
 * attorno, gli ordini di palchi (l'ultimo può essere il loggione), in tre forme: a ferro di cavallo
 * (la sala all'italiana), a ventaglio, a scatola. Ogni settore (nome, prezzi, nota, la foto della vista
 * dal posto, un link) raccoglie alcune zone della sala: le file davanti o dietro della platea, i palchi
 * centrali o laterali di ogni ordine, le barcacce accanto al palcoscenico, il loggione. Passando o
 * toccando un settore (sulla pianta o nella legenda) gli altri si attenuano e la scheda mostra prezzi
 * e vista. Nessun servizio esterno: è un disegno con i prezzi, non una biglietteria.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Piantasala_Tile extends Olobuild_Tile_Base {

    protected $type     = 'piantasala';
    protected $name     = 'Pianta della sala';
    protected $icon     = 'dashicons-layout';
    protected $category = 'interactive';

    protected $defaults = [
        // contenuto
        'forma'          => 'ferro',
        'palco_testo'    => 'Palcoscenico',
        'buca'           => true,
        'buca_testo'     => 'Buca',
        'file'           => 14,
        'posti_fila'     => 24,
        'ordini'         => 4,
        'palchi'         => 19,
        'loggione'       => true,
        'palco_centrale' => true,
        'numeri_ordini'  => true,
        'iniziale'       => 'primo',
        'testo_invito'   => 'Passa o tocca un settore: prezzi e vista dal posto.',
        'testo_intero'   => 'Intero',
        'testo_ridotto'  => 'Ridotto',
        'testo_vista'    => 'La vista da qui',
        'testo_link'     => 'Scegli il posto',
        'settori'        => [
            [ 'id' => 'set-1', 'nome' => 'Platea e palchi centrali', 'zone' => 'platea-a,platea-b,o1-c,o2-c', 'prezzo' => '25 €', 'ridotto' => '20 €', 'nota' => '', 'descrizione' => '', 'vista' => '', 'link' => '', 'colore' => '' ],
            [ 'id' => 'set-2', 'nome' => 'Palchi laterali', 'zone' => 'o1-l,o2-l,o3-c,o3-l,barcacce', 'prezzo' => '20 €', 'ridotto' => '16 €', 'nota' => '', 'descrizione' => '', 'vista' => '', 'link' => '', 'colore' => '' ],
            [ 'id' => 'set-3', 'nome' => 'Loggione', 'zone' => 'loggione,o4-c,o4-l', 'prezzo' => '14 €', 'ridotto' => '10 €', 'nota' => '', 'descrizione' => '', 'vista' => '', 'link' => '', 'colore' => '' ],
        ],
        // stile
        'disposizione'      => 'lato',
        'larghezza_pianta'  => 58,
        'gap'               => 40,
        'colore_sala'       => '',
        'colore_palco'      => '',
        'colore_posti'      => '',
        'colore_etichette'  => '',
        'sfondo_scheda'     => '',
        'bordo_scheda'      => [],
        'raggio'            => [ 'tl' => 16, 'tr' => 16, 'br' => 16, 'bl' => 16 ],
        'padding_scheda'    => [ 'top' => 32, 'right' => 32, 'bottom' => 32, 'left' => 32 ],
        'shadow'            => 'none',
        'nome_font_family'  => 'heading',
        'nome_font_size'    => 30,
        'nome_font_weight'  => '400',
        'nome_color'        => '',
        'prezzo_font_family' => 'heading',
        'prezzo_font_size'  => 34,
        'prezzo_color'      => '',
        'descrizione_font_size' => 15,
        'descrizione_line_height' => 1.6,
        'descrizione_color' => '',
        'legenda_font_size' => 14,
        'legenda_font_weight' => '500',
    ];

    public function get_controls() {
        return [];
    }

    /** Zone della sala (per i settori), coi nomi tradotti. */
    public static function zone_nomi() {
        $o = [ 'platea-a' => __( 'Platea, file davanti', 'olobuilder' ), 'platea-b' => __( 'Platea, file dietro', 'olobuilder' ) ];
        $romani = [ 1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V' ];
        foreach ( $romani as $n => $r ) {
            /* translators: %s: roman numeral of the tier */
            $o[ 'o' . $n . '-c' ] = sprintf( __( '%s ordine, palchi centrali', 'olobuilder' ), $r );
            /* translators: %s: roman numeral of the tier */
            $o[ 'o' . $n . '-l' ] = sprintf( __( '%s ordine, palchi laterali', 'olobuilder' ), $r );
        }
        $o['barcacce'] = __( 'Barcacce (palchi accanto al palcoscenico)', 'olobuilder' );
        $o['loggione'] = __( 'Loggione', 'olobuilder' );
        return $o;
    }

    /**
     * La geometria della sala: i bordi degli anelli (0 = bordo della platea, k = fuori dall'ordine k),
     * ognuno come funzione t ∈ [0,1] → punto, dal lato del palcoscenico a sinistra, attorno alla sala,
     * fino al palcoscenico a destra; la linea del boccascena; il palcoscenico.
     */
    private static function geometria( $forma, $anelli ) {
        $w   = 44;
        $cx  = 500;
        $g   = [ 'w' => $w, 'cx' => $cx ];
        if ( 'ventaglio' === $forma ) {
            $fy        = 120;
            $r0        = 320;
            $alfa      = deg2rad( 50 );
            $g['yp']   = 205;
            $g['bordo'] = static function ( $k, $t ) use ( $cx, $fy, $r0, $w, $alfa ) {
                $r  = $r0 + $k * $w;
                $th = -$alfa + 2 * $alfa * $t;
                return [ $cx + $r * sin( $th ), $fy + $r * cos( $th ) ];
            };
            $g['fuoco'] = [ $cx, $fy ];
            $g['r0']    = $r0;
            $g['alfa']  = $alfa;
        } elseif ( 'rettangolo' === $forma ) {
            $l0       = 270;
            $r0       = 730;
            $b0       = 620;
            $yp       = 205;
            $g['yp']  = $yp;
            $g['bordo'] = static function ( $k, $t ) use ( $l0, $r0, $b0, $yp, $w ) {
                $l = $l0 - $k * $w;
                $r = $r0 + $k * $w;
                $b = $b0 + $k * $w;
                $h = $b - $yp;
                $c = $r - $l;
                $p = ( 2 * $h + $c ) * $t;
                if ( $p <= $h ) {
                    return [ $l, $yp + $p ];
                }
                if ( $p <= $h + $c ) {
                    return [ $l + ( $p - $h ), $b ];
                }
                return [ $r, $b - ( $p - $h - $c ) ];
            };
        } else {
            $cy       = 380;
            $a0       = 225;
            $b0       = 262;
            $yp       = $cy - 0.64 * $b0;
            $g['yp']  = $yp;
            $g['bordo'] = static function ( $k, $t ) use ( $cx, $cy, $a0, $b0, $w, $yp ) {
                $a   = $a0 + $k * $w;
                $b   = $b0 + $k * $w;
                $max = acos( max( -1, min( 1, ( $yp - $cy ) / $b ) ) );
                $phi = -$max + 2 * $max * $t;
                return [ $cx + $a * sin( $phi ), $cy + $b * cos( $phi ) ];
            };
        }
        return $g;
    }

    /** Tabella lunghezza d'arco → t, per dividere un anello in palchi uguali. */
    private static function per_lunghezza( $bordo, $k ) {
        $n   = 240;
        $acc = [ 0.0 ];
        $p0  = $bordo( $k, 0 );
        for ( $i = 1; $i <= $n; $i++ ) {
            $p     = $bordo( $k, $i / $n );
            $acc[] = $acc[ $i - 1 ] + hypot( $p[0] - $p0[0], $p[1] - $p0[1] );
            $p0    = $p;
        }
        $tot = $acc[ $n ] ?: 1;
        return static function ( $u ) use ( $acc, $n, $tot ) {
            $obj = $u * $tot;
            $lo  = 0;
            $hi  = $n;
            while ( $hi - $lo > 1 ) {
                $mid = ( $lo + $hi ) >> 1;
                if ( $acc[ $mid ] < $obj ) {
                    $lo = $mid;
                } else {
                    $hi = $mid;
                }
            }
            $seg = $acc[ $hi ] - $acc[ $lo ];
            $f   = $seg > 0 ? ( $obj - $acc[ $lo ] ) / $seg : 0;
            return ( $lo + $f ) / $n;
        };
    }

    private static function pt( $p ) {
        return round( $p[0], 1 ) . ',' . round( $p[1], 1 );
    }

    /** Il riquadro (palco o tratto di anello) fra t1 e t2 dell'anello k: lati curvi, più passi per i tratti lunghi. */
    private static function riquadro( $bordo, $k, $t1, $t2 ) {
        $n  = max( 3, (int) ceil( abs( $t2 - $t1 ) * 90 ) );
        $pp = [];
        for ( $i = 0; $i <= $n; $i++ ) {
            $pp[] = self::pt( $bordo( $k - 1, $t1 + ( $t2 - $t1 ) * $i / $n ) );
        }
        for ( $i = $n; $i >= 0; $i-- ) {
            $pp[] = self::pt( $bordo( $k, $t1 + ( $t2 - $t1 ) * $i / $n ) );
        }
        return implode( ' ', $pp );
    }

    public function render( $settings ) {
        $s       = wp_parse_args( $settings, $this->defaults );
        $uid     = 'opia-' . $this->chiave_stabile( $settings );
        $sel     = '#' . $uid;
        $forma   = in_array( (string) $s['forma'], [ 'ferro', 'ventaglio', 'rettangolo' ], true ) ? (string) $s['forma'] : 'ferro';
        $ordini  = max( 0, min( 5, absint( $s['ordini'] ) ) );
        $logg    = ! empty( $s['loggione'] ) && $ordini > 0;
        $palchi  = max( 5, min( 41, absint( $s['palchi'] ) ) );
        $file    = max( 4, min( 30, absint( $s['file'] ) ) );
        $posti   = max( 8, min( 44, absint( $s['posti_fila'] ) ) );
        $nomi    = self::zone_nomi();

        // ── Settori: ogni zona va al primo settore che la nomina ──
        $tavolozza = [
            'var(--olo-color-primary)', 'var(--olo-color-accent)', 'var(--olo-color-secondary)',
            'color-mix(in srgb, var(--olo-color-primary) 50%, var(--olo-color-accent))',
            'color-mix(in srgb, var(--olo-color-secondary) 50%, var(--olo-color-accent))',
            'color-mix(in srgb, var(--olo-color-primary) 45%, var(--olo-color-secondary))',
            'color-mix(in srgb, var(--olo-color-accent) 55%, var(--olo-color-light, #fff))',
            'color-mix(in srgb, var(--olo-color-primary) 55%, var(--olo-color-light, #fff))',
        ];
        $settori = [];
        $di_zona = [];
        foreach ( array_values( (array) $s['settori'] ) as $i => $st ) {
            $nome = trim( (string) ( $st['nome'] ?? '' ) );
            if ( '' === $nome ) {
                continue;
            }
            $idx       = count( $settori );
            $col       = $this->safe_color_css( $st['colore'] ?? '' ) ?: $tavolozza[ $idx % count( $tavolozza ) ];
            $settori[] = [ 'st' => $st, 'nome' => $nome, 'col' => $col ];
            foreach ( array_filter( array_map( 'trim', explode( ',', (string) ( $st['zone'] ?? '' ) ) ) ) as $z ) {
                if ( isset( $nomi[ $z ] ) && ! isset( $di_zona[ $z ] ) ) {
                    $di_zona[ $z ] = $idx;
                }
            }
        }
        $attr_zona = static function ( $z ) use ( $di_zona ) {
            return isset( $di_zona[ $z ] ) ? ' data-s="' . (int) $di_zona[ $z ] . '"' : '';
        };

        // ── Disegno ──
        $g      = self::geometria( $forma, $ordini );
        $bordo  = $g['bordo'];
        $yp     = $g['yp'];
        $forme  = '';
        $minx   = 1000;
        $maxx   = 0;
        $maxy   = 0;
        $estendi = static function ( $p ) use ( &$minx, &$maxx, &$maxy ) {
            $minx = min( $minx, $p[0] );
            $maxx = max( $maxx, $p[0] );
            $maxy = max( $maxy, $p[1] );
        };
        for ( $i = 0; $i <= 40; $i++ ) {
            $estendi( $bordo( max( 0, $ordini ), $i / 40 ) );
        }
        $estendi( $bordo( 0, 0.5 ) );

        // ordini di palchi (e il loggione)
        $etich = '';
        for ( $k = 1; $k <= $ordini; $k++ ) {
            $mappa = self::per_lunghezza( static function ( $kk, $t ) use ( $bordo, $k ) {
                $a = $bordo( $k - 1, $t );
                $b = $bordo( $k, $t );
                return [ ( $a[0] + $b[0] ) / 2, ( $a[1] + $b[1] ) / 2 ];
            }, $k );
            $ultimo = ( $k === $ordini );
            if ( $ultimo && $logg ) {
                $forme .= '<polygon class="opia-anello"' . $attr_zona( 'loggione' ) . ' points="' . self::riquadro( $bordo, $k, 0, 1 ) . '"/>';
                // la panca del loggione: una fila di puntini a metà anello
                $pu = '';
                $n  = 70;
                for ( $j = 1; $j < $n; $j++ ) {
                    $t   = $mappa( $j / $n );
                    $a   = $bordo( $k - 1, $t );
                    $b   = $bordo( $k, $t );
                    $pu .= '<circle cx="' . round( ( $a[0] + $b[0] ) / 2, 1 ) . '" cy="' . round( ( $a[1] + $b[1] ) / 2, 1 ) . '" r="3.2"/>';
                }
                $forme .= '<g class="opia-panca" aria-hidden="true">' . $pu . '</g>';
            } else {
                $n     = $palchi;
                $c1    = (int) floor( $n / 3 );
                $c2    = $n - $c1;
                $salta = [];
                $j     = 0;
                while ( $j < $n ) {
                    $largo = 1;
                    if ( ! empty( $s['palco_centrale'] ) && 1 === $k && $n >= 9 && $j === (int) floor( $n / 2 ) - 1 ) {
                        $largo = 3;
                    }
                    $t1 = $mappa( $j / $n );
                    $t2 = $mappa( min( $n, $j + $largo ) / $n );
                    if ( 0 === $j || $j + $largo >= $n ) {
                        $z = 'barcacce';
                    } elseif ( $j >= $c1 && $j + $largo <= $c2 ) {
                        $z = 'o' . $k . '-c';
                    } else {
                        $z = 'o' . $k . '-l';
                    }
                    $forme .= '<polygon class="opia-palco' . ( $largo > 1 ? ' opia-reale' : '' ) . '"' . $attr_zona( $z ) . ' data-z="' . esc_attr( $z ) . '" points="' . self::riquadro( $bordo, $k, $t1, $t2 ) . '"/>';
                    $j     += $largo;
                }
            }
            if ( ! empty( $s['numeri_ordini'] ) ) {
                $a      = $bordo( $k - 1, 0 );
                $b      = $bordo( $k, 0 );
                $roman  = [ 1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V' ];
                $lab    = ( $ultimo && $logg ) ? 'L' : $roman[ $k ];
                $etich .= '<text x="' . round( ( $a[0] + $b[0] ) / 2, 1 ) . '" y="' . round( min( $a[1], $b[1] ) - 12, 1 ) . '" text-anchor="middle">' . esc_html( $lab ) . '</text>';
            }
        }

        // platea: file di posti dentro il bordo 0, con il corridoio centrale
        $sedili = [ 'platea-a' => '', 'platea-b' => '' ];
        $inizio = $yp + ( ! empty( $s['buca'] ) ? 62 : 34 );
        if ( 'ventaglio' === $forma ) {
            $f      = $g['fuoco'];
            $r_ini  = $inizio - $f[1];
            $r_fin  = $g['r0'] - 22;
            for ( $i = 0; $i < $file; $i++ ) {
                $r    = $r_ini + ( $r_fin - $r_ini ) * ( $file > 1 ? $i / ( $file - 1 ) : 0 );
                $amax = $g['alfa'] - 0.06;
                $lung = 2 * $amax * $r;
                $n    = max( 4, (int) floor( $lung / max( 14, ( 2 * $g['alfa'] * $r_fin ) / $posti ) ) );
                $z    = $i < $file / 2 ? 'platea-a' : 'platea-b';
                for ( $j = 0; $j < $n; $j++ ) {
                    $th = -$amax + 2 * $amax * ( $j + 0.5 ) / $n;
                    if ( abs( $th * $r ) < 13 ) {
                        continue;
                    }
                    $sedili[ $z ] .= '<circle cx="' . round( $f[0] + $r * sin( $th ), 1 ) . '" cy="' . round( $f[1] + $r * cos( $th ), 1 ) . '" r="4.6"/>';
                }
            }
        } else {
            // il bordo 0 come poligono, per sapere dove finisce ogni fila
            $poli = [];
            for ( $i = 0; $i <= 120; $i++ ) {
                $poli[] = $bordo( 0, $i / 120 );
            }
            $fine = 0;
            foreach ( $poli as $p ) {
                $fine = max( $fine, $p[1] );
            }
            $fine   -= 26;
            $passo   = ( $fine - $inizio ) / max( 1, $file - 1 );
            $largo_m = 0;
            $estremi = [];
            for ( $i = 0; $i < $file; $i++ ) {
                $y   = $inizio + $passo * $i;
                $xs  = [];
                $cnt = count( $poli );
                for ( $q = 0; $q < $cnt - 1; $q++ ) {
                    $a = $poli[ $q ];
                    $b = $poli[ $q + 1 ];
                    if ( ( $a[1] - $y ) * ( $b[1] - $y ) <= 0 && $a[1] !== $b[1] ) {
                        $xs[] = $a[0] + ( $y - $a[1] ) * ( $b[0] - $a[0] ) / ( $b[1] - $a[1] );
                    }
                }
                if ( count( $xs ) < 2 ) {
                    $estremi[ $i ] = null;
                    continue;
                }
                $x1            = min( $xs ) + 22;
                $x2            = max( $xs ) - 22;
                $estremi[ $i ] = [ $x1, $x2, $y ];
                $largo_m       = max( $largo_m, $x2 - $x1 );
            }
            $sp = max( 14, ( $largo_m - 26 ) / $posti );
            foreach ( $estremi as $i => $e ) {
                if ( ! $e ) {
                    continue;
                }
                $z    = $i < $file / 2 ? 'platea-a' : 'platea-b';
                $meta = ( $e[1] - $e[0] - 26 ) / 2;
                $n    = max( 1, (int) floor( $meta / $sp ) );
                for ( $j = 0; $j < $n; $j++ ) {
                    $dx            = 13 + $sp * ( $j + 0.5 );
                    $sedili[ $z ] .= '<circle cx="' . round( $g['cx'] - $dx, 1 ) . '" cy="' . round( $e[2], 1 ) . '" r="4.6"/><circle cx="' . round( $g['cx'] + $dx, 1 ) . '" cy="' . round( $e[2], 1 ) . '" r="4.6"/>';
                }
            }
        }
        $platea = '';
        foreach ( $sedili as $z => $cerchi ) {
            if ( '' !== $cerchi ) {
                $platea .= '<g class="opia-posti"' . $attr_zona( $z ) . ' data-z="' . esc_attr( $z ) . '">' . $cerchi . '</g>';
            }
        }

        // palcoscenico e buca
        $apre  = $bordo( 0, 0 );
        $apre2 = $bordo( 0, 1 );
        $px1   = min( $apre[0], $apre2[0] );
        $px2   = max( $apre[0], $apre2[0] );
        if ( 'ventaglio' === $forma ) {
            $px1 = $g['cx'] - 210;
            $px2 = $g['cx'] + 210;
        }
        $palco  = '<rect class="opia-palcoscenico" x="' . round( $px1, 1 ) . '" y="' . round( $yp - 150, 1 ) . '" width="' . round( $px2 - $px1, 1 ) . '" height="150" rx="6"/>';
        $palco .= '<text class="opia-scritta" x="' . $g['cx'] . '" y="' . round( $yp - 68, 1 ) . '" text-anchor="middle">' . esc_html( (string) $s['palco_testo'] ) . '</text>';
        if ( ! empty( $s['buca'] ) ) {
            $palco .= '<rect class="opia-buca" x="' . round( $g['cx'] - 150, 1 ) . '" y="' . round( $yp + 10, 1 ) . '" width="300" height="34" rx="17"/>';
            $palco .= '<text class="opia-scritta opia-scritta--buca" x="' . $g['cx'] . '" y="' . round( $yp + 32, 1 ) . '" text-anchor="middle">' . esc_html( (string) $s['buca_testo'] ) . '</text>';
        }
        $vb_x = floor( min( $minx, $px1 ) - 30 );
        $vb_w = ceil( max( $maxx, $px2 ) + 30 ) - $vb_x;
        $vb_y = floor( $yp - 150 - 20 );
        $vb_h = ceil( $maxy + 30 ) - $vb_y;

        // ── Colori e misure ──
        $c_sala  = $this->safe_color_css( $s['colore_sala'] ) ?: 'color-mix(in srgb, currentColor 12%, transparent)';
        $c_palco = $this->safe_color_css( $s['colore_palco'] ) ?: 'color-mix(in srgb, currentColor 78%, transparent)';
        $c_posti = $this->safe_color_css( $s['colore_posti'] ) ?: 'color-mix(in srgb, currentColor 30%, transparent)';
        $c_etich = $this->safe_color_css( $s['colore_etichette'] ) ?: 'currentColor';
        $sfondo  = $this->safe_color_css( $s['sfondo_scheda'] );
        $bordo_s = Olobuild_Tile_Utils::border_css( $s['bordo_scheda'] ?? null );
        $raggio  = Olobuild_Tile_Utils::border_radius( $s['raggio'] ) ?: '0';
        $pad     = Olobuild_Tile_Utils::sides_css( Olobuild_Tile_Utils::spacing_sides( $s['padding_scheda'] ?? null, [], [ 32, 32, 32, 32 ] ) );
        $ombra   = Olobuild_Tile_Utils::shadow_value( $s, 'shadow' );
        $lato    = 'sotto' !== (string) $s['disposizione'];
        $larg    = max( 30, min( 75, absint( $s['larghezza_pianta'] ) ) );
        $gap     = max( 0, min( 120, absint( $s['gap'] ) ) );
        $ini     = ( 'nessuno' === (string) $s['iniziale'] || ! $settori ) ? -1 : 0;

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS da colori passati da safe_color_css() e dagli helper di Olobuild_Tile_Utils, numeri forzati, costanti interne e selettori generati qui
        echo '<style>';
        echo "{$sel}{--opia-sala:{$c_sala};--opia-palco:{$c_palco};--opia-posti:{$c_posti};--opia-etich:{$c_etich};position:relative}";
        foreach ( $settori as $i => $x ) {
            echo "{$sel}{--opia-s{$i}:{$x['col']}}";
            echo "{$sel} .opia-svg [data-s=\"{$i}\"]{fill:var(--opia-s{$i})}";
            echo "{$sel} .opia-leg[data-s=\"{$i}\"]{--c:var(--opia-s{$i})}";
            echo "{$sel}[data-attivo=\"{$i}\"] .opia-svg [data-s]:not([data-s=\"{$i}\"]){opacity:.2}";
            echo "{$sel}[data-scheda=\"{$i}\"] .opia-scheda[data-s=\"{$i}\"]{visibility:visible;opacity:1;transform:none;transition:opacity .5s cubic-bezier(.2,.7,.2,1) .1s,transform .5s cubic-bezier(.2,.7,.2,1) .1s,visibility 0s}";
            echo "{$sel}[data-scheda=\"{$i}\"] .opia-scheda[data-s=\"{$i}\"] .opia-vista img{transform:none;transition:transform 1.4s cubic-bezier(.2,.7,.2,1) .1s}";
            echo "{$sel}[data-scheda=\"{$i}\"] .opia-leg[data-s=\"{$i}\"]{background:color-mix(in srgb, var(--c) 14%, transparent);border-color:var(--c)}";
            // Il pulsante della scheda ha il colore del settore come fondo: su un settore chiaro (un giallo, un avorio)
            // il testo chiaro fisso non si leggeva (1,3:1 sul giallo di Fotogramma, 30 ott 2026). Si sceglie il testo
            // che si legge su quel fondo.
            $su_settore = Olobuild_Tile_Utils::testo_su_fondo( $x['col'], 'var(--olo-color-light, #ffffff)' );
            if ( '' !== $su_settore ) {
                echo "{$sel} .opia-scheda[data-s=\"{$i}\"] .opia-link{color:{$su_settore}!important}";
            }
        }
        echo "{$sel} .opia-griglia{display:grid;grid-template-columns:" . ( $lato ? "minmax(0,{$larg}fr) minmax(0," . ( 100 - $larg ) . 'fr)' : 'minmax(0,1fr)' ) . ";gap:{$gap}px;align-items:start}";
        echo '@media (max-width:960px){' . "{$sel} .opia-griglia{grid-template-columns:minmax(0,1fr)}" . '}';
        echo "{$sel} .opia-mappa{position:relative}";
        echo "{$sel} .opia-svg{display:block;width:100%;height:auto;overflow:visible}";
        echo "{$sel} .opia-svg [data-s]{cursor:pointer;transition:opacity .35s,filter .35s}";
        echo "{$sel} .opia-svg [data-s]:hover{filter:brightness(1.12)}";
        echo "{$sel} .opia-palco,{$sel} .opia-anello{fill:var(--opia-sala);stroke:var(--opia-fondo,var(--olo-color-background,#fff));stroke-width:3;stroke-linejoin:round}";
        echo "{$sel} .opia-reale{stroke-width:3}";
        echo "{$sel} .opia-posti{fill:var(--opia-posti)}";
        echo "{$sel} .opia-panca{fill:color-mix(in srgb, var(--olo-color-light, #fff) 55%, transparent);pointer-events:none}";
        echo "{$sel} .opia-palcoscenico{fill:var(--opia-palco)}";
        echo "{$sel} .opia-buca{fill:none;stroke:var(--opia-palco);stroke-width:1.5;stroke-dasharray:4 4}";
        echo "{$sel} .opia-scritta{fill:var(--olo-color-light, #fff);font-size:22px;letter-spacing:.3em;text-transform:uppercase;font-weight:600}";
        echo "{$sel} .opia-scritta--buca{fill:var(--opia-etich);font-size:14px;letter-spacing:.2em;opacity:.75}";
        echo "{$sel} .opia-numeri{fill:var(--opia-etich);font-size:17px;font-weight:600;opacity:.6}";
        echo "{$sel} .opia-tip{position:absolute;z-index:3;pointer-events:none;transform:translate(-50%,calc(-100% - .8rem));padding:.45rem .8rem;border-radius:.6rem;background:var(--olo-color-dark, #111);color:var(--olo-color-light, #fff);font-size:.85rem;white-space:nowrap;opacity:0;transition:opacity .2s}";
        echo "{$sel} .opia-tip.is-on{opacity:1}";
        echo "{$sel} .opia-legenda{list-style:none;margin:1.2rem 0 0;padding:0;display:flex;flex-wrap:wrap;gap:.5rem}";
        echo "{$sel} .opia-leg{display:inline-flex;align-items:center;gap:.55rem;font:inherit;cursor:pointer;color:inherit;background:transparent;border:1px solid color-mix(in srgb, currentColor 22%, transparent);border-radius:5em;padding:.5em 1em;transition:background-color .25s,border-color .25s}";
        echo "{$sel} .opia-leg::before{content:'';width:.8em;height:.8em;border-radius:50%;background:var(--c)}";
        echo "{$sel} .opia-leg:focus-visible,{$sel} .opia-link:focus-visible{outline:2px solid currentColor;outline-offset:3px}";
        // Le schede dei settori stanno tutte nella stessa cella della griglia, quelle non scelte invisibili ma presenti:
        // la scheda è alta quanto la più lunga e non cambia altezza da un settore all'altro (prima, con display:none, la
        // legenda sotto saliva e scendeva a ogni passaggio e la foto della vista cambiava posto). Foto e link stanno in fondo.
        echo "{$sel} .opia-schede{position:relative;display:grid;border-radius:{$raggio};padding:{$pad};" . ( $sfondo ? "background:{$sfondo};" : 'background:color-mix(in srgb, currentColor 4%, transparent);' ) . ( $bordo_s ? "border:{$bordo_s};" : '' ) . ( 'none' !== $ombra ? "box-shadow:{$ombra};" : '' ) . '}';
        echo "{$sel} .opia-schede>*{grid-area:1/1;min-width:0}";
        echo "{$sel} .opia-scheda{display:flex;flex-direction:column;visibility:hidden;opacity:0;transform:translateY(.6rem);transition:opacity .2s,transform .2s,visibility 0s linear .2s}";
        echo "{$sel}:not([data-scheda]) .opia-invito,{$sel}[data-scheda=\"-1\"] .opia-invito{visibility:visible;opacity:.8;transition:opacity .5s .1s,visibility 0s}";
        echo "{$sel} .opia-invito{align-self:center;margin:0;font-size:1.05rem;visibility:hidden;opacity:0;transition:opacity .2s,visibility 0s linear .2s}";
        echo "{$sel} .opia-nome{margin:0 0 .8rem;display:flex;align-items:center;gap:.6rem}";
        echo "{$sel} .opia-nome::before{content:'';flex:none;width:.55em;height:.55em;border-radius:50%;background:var(--c)}";
        echo "{$sel} .opia-prezzi{display:flex;flex-wrap:wrap;gap:.6rem 2rem;margin:0 0 .6rem}";
        echo "{$sel} .opia-prezzi span{display:flex;flex-direction:column}";
        echo "{$sel} .opia-prezzi small{font-size:.75rem;letter-spacing:.16em;text-transform:uppercase;opacity:.7}";
        echo "{$sel} .opia-prezzi b{font-weight:400;line-height:1.1}";
        echo "{$sel} .opia-nota{margin:0 0 .8rem;font-size:.9rem;opacity:.8}";
        echo "{$sel} .opia-desc{margin:0 0 1rem}";
        echo "{$sel} .opia-vista{margin:auto 0 0}";
        echo "{$sel} .opia-vista figcaption{font-size:.75rem;letter-spacing:.16em;text-transform:uppercase;opacity:.7;margin:0 0 .5rem}";
        echo "{$sel} .opia-vista div{overflow:hidden;border-radius:.6rem;aspect-ratio:16/10}";
        echo "{$sel} .opia-vista img{display:block;width:100%;height:100%;object-fit:cover;transform:scale(1.18);transition:transform 0s linear .2s}";
        echo "{$sel} .opia-link{display:inline-flex;align-self:flex-start;margin-top:1.2rem;text-decoration:none!important;background:var(--c);color:var(--olo-color-light, #fff)!important;border-radius:5em;padding:.8em 1.5em;font-size:.85rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase}";
        echo "{$sel} .opia-scheda>:not(.opia-vista)+.opia-link{margin-top:auto}";
        echo '@media (prefers-reduced-motion:reduce){' . "{$sel} .opia-scheda,{$sel} .opia-invito,{$sel} .opia-vista img{transition:none!important;transform:none!important}" . '}';
        echo $this->css_testo( $s, 'nome', "{$sel} .opia-nome" );
        echo $this->css_testo( $s, 'prezzo', "{$sel} .opia-prezzi b" );
        echo $this->css_testo( $s, 'descrizione', "{$sel} .opia-desc" );
        echo $this->css_testo( $s, 'legenda', "{$sel} .opia-leg" );
        echo '</style>';
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped

        $h  = '<div id="' . esc_attr( $uid ) . '" class="olo-piantasala opia"' . ( $ini >= 0 ? ' data-scheda="' . $ini . '"' : '' ) . '>';
        $h .= '<div class="opia-griglia"><div class="opia-mappa">';
        $h .= '<svg class="opia-svg" viewBox="' . $vb_x . ' ' . $vb_y . ' ' . $vb_w . ' ' . $vb_h . '" role="img" aria-label="' . esc_attr__( 'Pianta della sala', 'olobuilder' ) . '">';
        $h .= $palco . $forme . $platea;
        if ( '' !== $etich ) {
            $h .= '<g class="opia-numeri" aria-hidden="true">' . $etich . '</g>';
        }
        $h .= '</svg><span class="opia-tip" aria-hidden="true"></span></div>';

        $h .= '<div class="opia-lato"><div class="opia-schede" aria-live="polite">';
        $h .= '<p class="opia-invito">' . esc_html( (string) $s['testo_invito'] ) . '</p>';
        foreach ( $settori as $i => $x ) {
            $st  = $x['st'];
            $h  .= '<div class="opia-scheda" data-s="' . (int) $i . '" style="--c:var(--opia-s' . (int) $i . ')">';
            $h  .= '<h3 class="opia-nome">' . esc_html( $x['nome'] ) . '</h3>';
            $pr  = trim( (string) ( $st['prezzo'] ?? '' ) );
            $rd  = trim( (string) ( $st['ridotto'] ?? '' ) );
            if ( '' !== $pr || '' !== $rd ) {
                $h .= '<p class="opia-prezzi">' . ( '' !== $pr ? '<span><small>' . esc_html( (string) $s['testo_intero'] ) . '</small><b>' . esc_html( $pr ) . '</b></span>' : '' )
                    . ( '' !== $rd ? '<span><small>' . esc_html( (string) $s['testo_ridotto'] ) . '</small><b>' . esc_html( $rd ) . '</b></span>' : '' ) . '</p>';
            }
            if ( '' !== trim( (string) ( $st['nota'] ?? '' ) ) ) {
                $h .= '<p class="opia-nota">' . esc_html( (string) $st['nota'] ) . '</p>';
            }
            if ( '' !== trim( (string) ( $st['descrizione'] ?? '' ) ) ) {
                $h .= '<p class="opia-desc">' . esc_html( (string) $st['descrizione'] ) . '</p>';
            }
            $vista = trim( (string) ( $st['vista'] ?? '' ) );
            if ( '' !== $vista ) {
                $h .= '<figure class="opia-vista"><figcaption>' . esc_html( (string) $s['testo_vista'] ) . '</figcaption><div><img src="' . esc_url( $vista ) . '" alt="" loading="lazy" decoding="async"></div></figure>';
            }
            $link = trim( (string) ( $st['link'] ?? '' ) );
            if ( '' !== $link ) {
                $h .= '<a class="opia-link" href="' . esc_url( $link ) . '">' . esc_html( (string) $s['testo_link'] ) . '</a>';
            }
            $h .= '</div>';
        }
        $h .= '</div>';
        if ( count( $settori ) > 0 ) {
            $h .= '<ul class="opia-legenda">';
            foreach ( $settori as $i => $x ) {
                $pr = trim( (string) ( $x['st']['prezzo'] ?? '' ) );
                $h .= '<li><button type="button" class="opia-leg" data-s="' . (int) $i . '" aria-pressed="' . ( $i === $ini ? 'true' : 'false' ) . '" data-tip="' . esc_attr( $x['nome'] . ( '' !== $pr ? ' · ' . $pr : '' ) ) . '">' . esc_html( $x['nome'] ) . '</button></li>';
            }
            $h .= '</ul>';
        }
        $h .= '</div></div></div>';
        $h .= $this->script( $uid );
        return $h;
    }

    /** CSS di un testo dal controllo tipografia. */
    private function css_testo( $s, $k, $selettore ) {
        $d   = '';
        $fam = $this->resolve_font_family( $s[ $k . '_font_family' ] ?? '' );
        $fs  = (float) ( $s[ $k . '_font_size' ] ?? 0 );
        $fw  = $this->font_weight_css( $s[ $k . '_font_weight' ] ?? '' );
        $lh  = $s[ $k . '_line_height' ] ?? '';
        $col = $this->safe_color_css( $s[ $k . '_color' ] ?? '' );
        if ( '' !== $fam ) {
            $d .= 'font-family:' . $fam . ';';
        }
        if ( $fs > 0 ) {
            $d .= 'font-size:' . $fs . 'px;';
        }
        if ( $fw ) {
            $d .= 'font-weight:' . $fw . ';';
        }
        if ( is_numeric( $lh ) ) {
            $d .= 'line-height:' . (float) $lh . ';';
        }
        if ( $col ) {
            $d .= 'color:' . $col . ';';
        }
        return '' !== $d ? "{$selettore}{{$d}}" : '';
    }

    /** Lo script: passaggio e tocco su pianta e legenda, il cartellino che segue il puntatore. Niente && (wptexturize). */
    private function script( $uid ) {
        static $funzione = false;
        $js = '';
        if ( ! $funzione ) {
            $funzione = true;
            $js      .= <<<'JS'
if(!window.oloPianta){window.oloPianta=function(el){
if(!el){return;}if(el.getAttribute("data-opia-vivo")){return;}el.setAttribute("data-opia-vivo","1");
var scelta=el.getAttribute("data-scheda");if(scelta===null){scelta="-1";}var fisso=null;
var tip=el.querySelector(".opia-tip");var mappa=el.querySelector(".opia-mappa");
var legs=[].slice.call(el.querySelectorAll(".opia-leg"));
var nomi={};legs.forEach(function(b){nomi[b.getAttribute("data-s")]=b.getAttribute("data-tip")||b.textContent;});
var mostra=function(i){if(i===null){el.removeAttribute("data-attivo");i=scelta;}else{el.setAttribute("data-attivo",i);}el.setAttribute("data-scheda",i);legs.forEach(function(b){b.setAttribute("aria-pressed",b.getAttribute("data-s")===String(i)?"true":"false");});};
var fine=window.matchMedia?window.matchMedia("(hover: hover) and (pointer: fine)").matches:true;
legs.forEach(function(b){b.addEventListener("click",function(){var i=b.getAttribute("data-s");fisso=fisso===i?null:i;if(fisso!==null){scelta=fisso;}mostra(fisso);});if(fine){b.addEventListener("mouseenter",function(){mostra(b.getAttribute("data-s"));});b.addEventListener("mouseleave",function(){mostra(fisso);});}});
var svg=el.querySelector(".opia-svg");if(!svg){return;}
var su=function(e){var t=e.target;while(t){if(t===svg){return null;}if(t.getAttribute){if(t.getAttribute("data-s")!==null){return t;}}t=t.parentNode;}return null;};
svg.addEventListener("click",function(e){var t=su(e);if(t){var i=t.getAttribute("data-s");fisso=fisso===i?null:i;if(fisso!==null){scelta=fisso;}mostra(fisso);}});
if(fine){svg.addEventListener("pointermove",function(e){var t=su(e);if(!t){if(tip){tip.classList.remove("is-on");}mostra(fisso);return;}var i=t.getAttribute("data-s");mostra(i);if(tip){var r=mappa.getBoundingClientRect();tip.textContent=nomi[i]||"";tip.style.left=(e.clientX-r.left)+"px";tip.style.top=(e.clientY-r.top)+"px";tip.classList.add("is-on");}});
svg.addEventListener("pointerleave",function(){if(tip){tip.classList.remove("is-on");}mostra(fisso);});}
};}
JS;
        }
        $js .= '(function(){var e=document.getElementById(' . wp_json_encode( $uid ) . ');if(window.oloPianta){window.oloPianta(e);}})();';
        return '<script>' . $js . '</script>';
    }
}
