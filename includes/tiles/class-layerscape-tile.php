<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Paesaggio a strati — un paesaggio disegnato a strati (creste, montagne, colline, bosco, mare di nuvole,
 * onde, dune, città o un'immagine propria) che scorrono a velocità diverse mentre si scorre la pagina: i lontani
 * piano, i vicini insieme alla pagina. Cielo sfumato o in tinta, stelle che si accendono, sole o luna che
 * tramonta o sorge dietro gli strati, nebbia che scorre fra uno strato e l'altro e un testo che può stare DIETRO
 * gli strati vicini: salendo, le montagne lo coprono. Nata per il tema Rifugio Larice (13 ott 2026, «usa
 * parallassi particolari»: nessuna tile faceva una scena a strati), vale per qualunque paesaggio.
 *
 * Le sagome si generano qui da forma + variante + dettaglio (stessi valori = stessa sagoma), in un riquadro largo
 * quattro volte l'altezza dello strato: sul telefono si vede la parte centrale, senza schiacciare le punte.
 * Parallasse di UIkit (solo trasformazioni e opacità, andamento lineare, riferita alla scena intera): vale anche
 * sul telefono e si ferma per chi chiede di ridurre il movimento. Col mouse (puntatore fine) gli strati vicini si
 * spostano anche di lato, più dei lontani.
 */
class Olobuild_Layerscape_Tile extends Olobuild_Tile_Base {

    protected $type     = 'layerscape';
    protected $name     = 'Paesaggio a strati';
    protected $icon     = 'dashicons-image-filter';
    protected $category = 'atmosphere';
    protected $defaults = [
        // Dal più lontano al più vicino. Cosa mostra e come si muove (Contenuto) + aspetto (Stile, specchio).
        'strati'              => [
            [ 'id' => 'ls-1', 'forma' => 'cime', 'variante' => 3, 'dettaglio' => 60, 'immagine' => '', 'profondita' => 90, 'altezza' => 72, 'colore' => 'color-mix(in srgb, var(--olo-color-primary) 40%, var(--olo-color-dark))', 'opacita' => 100, 'sfocatura' => 0 ],
            [ 'id' => 'ls-2', 'forma' => 'montagne', 'variante' => 7, 'dettaglio' => 50, 'immagine' => '', 'profondita' => 62, 'altezza' => 56, 'colore' => 'color-mix(in srgb, var(--olo-color-secondary) 70%, var(--olo-color-dark))', 'opacita' => 100, 'sfocatura' => 0 ],
            [ 'id' => 'ls-3', 'forma' => 'colline', 'variante' => 2, 'dettaglio' => 50, 'immagine' => '', 'profondita' => 34, 'altezza' => 40, 'colore' => 'color-mix(in srgb, var(--olo-color-secondary) 40%, var(--olo-color-dark))', 'opacita' => 100, 'sfocatura' => 0 ],
            [ 'id' => 'ls-4', 'forma' => 'bosco', 'variante' => 5, 'dettaglio' => 55, 'immagine' => '', 'profondita' => 0, 'altezza' => 30, 'colore' => 'var(--olo-color-dark)', 'opacita' => 100, 'sfocatura' => 0 ],
        ],
        'cielo'               => 'sfumato',
        'stelle'              => true,
        'stelle_numero'       => 90,
        'stelle_comparsa'     => 'scorrendo',
        'stelle_scintillio'   => true,
        'astro'               => 'sole',
        'astro_immagine'      => '',
        'astro_x'             => 74,
        'astro_y'             => 50,
        'astro_moto'          => 'tramonta',
        'nebbia'              => true,
        'nebbia_scorre'       => true,
        'occhiello'           => '',
        'titolo'              => 'Dove finisce il sentiero',
        'titolo_tag'          => 'h2',
        'testo'               => '',
        'pulsante_testo'      => '',
        'pulsante_link'       => '',
        'testo_dietro'        => 1,
        'testo_profondita'    => 45,
        'movimento'           => true,
        'intensita'           => 60,
        'mouse'               => true,
        // Stile
        'altezza'             => 640,
        'strati_scala'        => 100,
        'cielo_alto'          => 'color-mix(in srgb, var(--olo-color-dark) 72%, var(--olo-color-secondary))',
        'cielo_basso'         => 'color-mix(in srgb, var(--olo-color-primary) 55%, var(--olo-color-accent))',
        'astro_colore'        => 'var(--olo-color-accent)',
        'astro_misura'        => 110,
        'astro_alone'         => 60,
        'stelle_colore'       => 'var(--olo-color-light)',
        'nebbia_colore'       => 'color-mix(in srgb, var(--olo-color-primary) 25%, var(--olo-color-light))',
        'nebbia_opacita'      => 34,
        'occhiello_font_family' => '',
        'occhiello_font_size' => 13,
        'occhiello_font_weight' => '700',
        'occhiello_uppercase' => true,
        'occhiello_letter_spacing' => 2,
        'occhiello_color'     => 'var(--olo-color-accent)',
        'titolo_font_family'  => 'var(--olo-font-family-heading)',
        'titolo_font_size'    => 72,
        'titolo_font_size_tablet' => 56,
        'titolo_font_size_mobile' => 40,
        'titolo_font_weight'  => '800',
        'titolo_line_height'  => 0.95,
        'titolo_letter_spacing' => '',
        'titolo_color'        => 'var(--olo-color-light)',
        'testo_font_family'   => '',
        'testo_font_size'     => 18,
        'testo_font_weight'   => '',
        'testo_color'         => 'color-mix(in srgb, var(--olo-color-light) 86%, transparent)',
        'pulsante_fondo'      => 'var(--olo-color-primary)',
        'pulsante_colore'     => 'var(--olo-color-primary-contrast)',
        'testo_allineamento'  => 'center',
        'testo_posizione'     => 'alto',
        'testo_larghezza'     => 760,
        'testo_padding'       => [ 'top' => 72, 'right' => 24, 'bottom' => 72, 'left' => 24 ],
        'raggio'              => [ 'tl' => 0, 'tr' => 0, 'br' => 0, 'bl' => 0 ],
    ];

    const FORME = [ 'cime', 'montagne', 'colline', 'bosco', 'nuvole', 'onde', 'dune', 'citta', 'immagine' ];

    /** Larghezza e altezza del riquadro delle sagome: largo quattro volte l'altezza. */
    const VW = 2000;
    const VH = 500;

    public function get_controls() { return []; }

    // ── Sagome ────────────────────────────────────────────────────────────────────────────────

    /** Numeri pseudo-casuali ripetibili (stesso seme = stessa sequenza), in [0, 1). */
    private static function caso( $seme ) {
        $s = ( (int) $seme * 2654435761 + 1013904223 ) & 0x7fffffff;
        return static function () use ( &$s ) {
            $s = ( $s * 1103515245 + 12345 ) & 0x7fffffff;
            return $s / 2147483648;
        };
    }

    /** Un punto del tracciato, in unità intere del riquadro (al massimo 1,2 px a schermo). */
    private static function p( $x, $y ) {
        return round( $x ) . ',' . round( $y );
    }

    /** Punti → tracciato chiuso in basso, a segmenti dritti (rocce, città). */
    private static function spezzata( $punti ) {
        $d = 'M0,' . self::VH;
        foreach ( $punti as $p ) {
            $d .= 'L' . self::p( $p[0], $p[1] );
        }
        return $d . 'L' . self::VW . ',' . self::VH . 'Z';
    }

    /** Punti → tracciato chiuso in basso, morbido (Catmull-Rom in curve di Bézier). */
    private static function morbida( $punti ) {
        $n = count( $punti );
        $d = 'M0,' . self::VH . 'L' . self::p( $punti[0][0], $punti[0][1] );
        for ( $i = 0; $i < $n - 1; $i++ ) {
            $p0 = $punti[ max( 0, $i - 1 ) ];
            $p1 = $punti[ $i ];
            $p2 = $punti[ $i + 1 ];
            $p3 = $punti[ min( $n - 1, $i + 2 ) ];
            $d .= 'C' . self::p( $p1[0] + ( $p2[0] - $p0[0] ) / 6, $p1[1] + ( $p2[1] - $p0[1] ) / 6 )
                . ' ' . self::p( $p2[0] - ( $p3[0] - $p1[0] ) / 6, $p2[1] - ( $p3[1] - $p1[1] ) / 6 )
                . ' ' . self::p( $p2[0], $p2[1] );
        }
        return $d . 'L' . self::VW . ',' . self::VH . 'Z';
    }

    /** Rumore a spostamento del punto medio: 2^$livelli + 1 valori fra -2 e 2 circa, da sommare a un profilo. */
    private static function rumore( $r, $livelli, $ruvidezza ) {
        $n      = 1 << $livelli;
        $v      = array_fill( 0, $n + 1, 0.0 );
        $v[0]   = $r() * 2 - 1;
        $v[ $n ] = $r() * 2 - 1;
        $a      = 1.0;
        for ( $passo = $n; $passo > 1; $passo >>= 1 ) {
            $mezzo = $passo >> 1;
            for ( $i = $mezzo; $i < $n; $i += $passo ) {
                $v[ $i ] = ( $v[ $i - $mezzo ] + $v[ $i + $mezzo ] ) / 2 + ( $r() * 2 - 1 ) * $a;
            }
            $a *= $ruvidezza;
        }
        return $v;
    }

    /** Il tracciato di una sagoma. $dettaglio 0-100: punte, asperità, densità. */
    private static function sagoma( $forma, $variante, $dettaglio ) {
        $r  = self::caso( 1 + absint( $variante ) * 7919 + crc32( (string) $forma ) % 997 );
        $k  = max( 0, min( 100, (int) $dettaglio ) ) / 100;
        $W  = self::VW;
        $H  = self::VH;
        $cl = static function ( $y ) use ( $H ) {
            return max( 2, min( $H - 2, $y ) );
        };

        switch ( $forma ) {
            case 'cime':
                // Dolomiti: gruppi con pareti quasi verticali sopra i ghiaioni, la cresta inclinata, torri e guglie
                // (una più alta delle altre), separati da forcelle poco profonde.
                $punti = [ [ 0, $H * 0.6 ] ];
                $x     = -$r() * 160;
                while ( $x < $W ) {
                    $w     = ( 240 + $r() * 380 ) * ( 1.1 - 0.35 * $k );
                    $top_l = $H * ( 0.12 + $r() * 0.3 );
                    $top_r = $cl( $top_l + $H * ( $r() - 0.5 ) * 0.24 );
                    $valle = $H * ( 0.5 + $r() * 0.16 );
                    $piede = $valle - $H * ( 0.05 + $r() * 0.06 );
                    $punti[] = [ $x, $valle ];
                    $punti[] = [ $x + $w * 0.1, $piede ];
                    $punti[] = [ $x + $w * 0.13, $top_l + ( $piede - $top_l ) * 0.55 ];
                    $punti[] = [ $x + $w * 0.15, $top_l + ( $piede - $top_l ) * 0.5 ];
                    $punti[] = [ $x + $w * 0.17, $top_l + $H * 0.03 * $r() ];
                    $m     = 2 + (int) floor( $r() * ( 2 + 5 * $k ) );
                    $alta  = (int) floor( $r() * $m );
                    $da    = $x + $w * 0.17;
                    $a     = $x + $w * 0.83;
                    $passo = ( $a - $da ) / $m;
                    for ( $j = 0; $j < $m; $j++ ) {
                        $sx   = $da + $passo * ( $j + 0.25 + $r() * 0.5 );
                        $base = $top_l + ( $top_r - $top_l ) * ( $sx - $da ) / ( $a - $da );
                        $cima = $base - $H * ( 0.02 + $r() * ( 0.05 + 0.08 * $k ) + ( $j === $alta ? 0.05 + $r() * 0.07 : 0 ) );
                        $lw   = $passo * ( 0.22 + $r() * 0.2 );
                        $punti[] = [ $sx - $lw, $base + $H * 0.05 * $r() ];
                        $punti[] = [ $sx - $lw * 0.4, $cima + $H * 0.025 ];
                        $punti[] = [ $sx - $lw * 0.1, $cima ];
                        $punti[] = [ $sx + $lw * 0.25, $cima + $H * 0.012 ];
                        $punti[] = [ $sx + $lw, $base + $H * 0.05 * $r() ];
                    }
                    $punti[] = [ $a, $top_r + $H * 0.03 * $r() ];
                    $punti[] = [ $x + $w * 0.85, $top_r + ( $piede - $top_r ) * 0.5 ];
                    $punti[] = [ $x + $w * 0.87, $top_r + ( $piede - $top_r ) * 0.55 ];
                    $punti[] = [ $x + $w * 0.9, $piede ];
                    $punti[] = [ $x + $w, $valle ];
                    $x += $w + $r() * 60;
                }
                return self::spezzata( array_map( static function ( $q ) use ( $cl ) { return [ $q[0], $cl( $q[1] ) ]; }, $punti ) );

            case 'montagne':
                // Picchi che si incontrano nelle selle: fianchi ripidi in cima e più dolci in basso, rocce dal rumore.
                $n      = 3 + (int) round( $r() * 2 + 3 * $k );
                $picchi = [];
                for ( $i = 0; $i < $n; $i++ ) {
                    $picchi[] = [ $W * ( $i + 0.15 + $r() * 0.7 ) / $n, $H * ( 0.06 + $r() * 0.34 ), 0.55 + $r() * 0.5, 0.55 + $r() * 0.5 ];
                }
                $ru    = self::rumore( $r, 8, 0.56 + 0.1 * $k );
                $m     = count( $ru ) - 1;
                $punti = [];
                for ( $i = 0; $i <= $m; $i++ ) {
                    $x = $W * $i / $m;
                    $y = $H * 0.86;
                    foreach ( $picchi as $q ) {
                        $dx = abs( $x - $q[0] );
                        $y  = min( $y, $q[1] + ( $x < $q[0] ? $q[2] : $q[3] ) * ( 0.32 * $dx + 9 * sqrt( $dx ) ) );
                    }
                    $punti[] = [ $x, $cl( $y + $ru[ $i ] * $H * ( 0.03 + 0.04 * $k ) ) ];
                }
                return self::spezzata( $punti );

            case 'dune':
                // Creste affilate: sopravento lungo e dolce, sottovento corto e ripido.
                $x  = -$r() * 240;
                $yt = $H * ( 0.62 + $r() * 0.16 );
                $d  = 'M' . self::p( $x, $H ) . 'L' . self::p( $x, $yt );
                while ( $x < $W ) {
                    $L   = ( 320 + $r() * 420 ) * ( 1.15 - 0.45 * $k );
                    $xc  = $x + $L;
                    $yc  = $H * ( 0.2 + $r() * 0.3 );
                    $d  .= 'C' . self::p( $x + $L * 0.42, $yt ) . ' ' . self::p( $xc - $L * 0.22, $yc + ( $yt - $yc ) * 0.04 ) . ' ' . self::p( $xc, $yc );
                    $S   = $L * ( 0.16 + $r() * 0.14 );
                    $yt2 = $H * ( 0.6 + $r() * 0.2 );
                    $d  .= 'C' . self::p( $xc + $S * 0.3, $yc + ( $yt2 - $yc ) * 0.55 ) . ' ' . self::p( $xc + $S * 0.62, $yt2 ) . ' ' . self::p( $xc + $S, $yt2 );
                    $x   = $xc + $S;
                    $yt  = $yt2;
                }
                return $d . 'L' . self::p( $x, $H ) . 'Z';

            case 'colline':
            case 'onde':
                $onde  = 'onde' === $forma;
                $punti = [];
                $f     = $onde ? [ 6 + $r() * 3, 13 + $r() * 1.5, ( 5 + $r() * 3 ) * 3 ] : [ 1.1 + $r() * 0.8, 2.6 + $r() * 1.4, 6 + $r() * 4 ];
                $ph    = [ $r() * 6.283, $r() * 6.283, $r() * 6.283 ];
                $a     = $onde ? [ 0.035, 0.015, 0.01 * $k ] : [ 0.13, 0.055, 0.02 * ( 0.4 + $k ) ];
                for ( $x = 0; $x <= $W; $x += 40 ) {
                    $t = $x / $W * 6.283;
                    $punti[] = [ $x, $cl( $H * ( $onde ? 0.62 : 0.5 ) + $H * ( $a[0] * sin( $f[0] * $t + $ph[0] ) + $a[1] * sin( $f[1] * $t + $ph[1] ) + $a[2] * sin( $f[2] * $t + $ph[2] ) ) ) ];
                }
                return self::morbida( $punti );

            case 'bosco':
                // Crinale basso e una fila fitta di conifere (larici, abeti) a palchi coi rami che scendono.
                $ph    = $r() * 6.283;
                $suolo = static function ( $x ) use ( $H, $ph, $W ) {
                    return $H * 0.8 + $H * 0.05 * sin( $x / $W * 6.283 * 1.3 + $ph ) + $H * 0.025 * sin( $x / $W * 6.283 * 3.7 + $ph * 2 );
                };
                $terra = [];
                for ( $x = 0; $x <= $W; $x += 50 ) {
                    $terra[] = [ $x, $suolo( $x ) ];
                }
                $d     = self::spezzata( $terra );
                $x     = -20;
                $fitto = 30 - 16 * $k;
                while ( $x < $W + 20 ) {
                    $h      = $H * ( 0.22 + $r() * 0.3 ) * ( $r() < 0.12 ? 1.35 : 1 );
                    $w      = $h * ( 0.34 + $r() * 0.1 );
                    $y0     = $suolo( $x ) + 6;
                    $palchi = $h > $H * 0.4 ? 4 : 3;
                    for ( $q = 0; $q < $palchi; $q++ ) {
                        $alto  = $y0 - $h * ( 0.34 + 0.66 * ( $q + 1 ) / $palchi );
                        $basso = $y0 - $h * 0.5 * $q / $palchi;
                        $mezza = $w / 2 * ( 1 - 0.22 * $q );
                        $curva = $basso - ( $basso - $alto ) * 0.42;
                        $d    .= 'M' . self::p( $x - $mezza, $basso ) . 'Q' . self::p( $x - $mezza * 0.56, $curva ) . ' ' . self::p( $x, $alto )
                            . 'Q' . self::p( $x + $mezza * 0.56, $curva ) . ' ' . self::p( $x + $mezza, $basso ) . 'Z';
                    }
                    $x += $fitto * ( 0.5 + $r() );
                }
                return $d;

            case 'nuvole':
                // Mare di nuvole: bordo a sbuffi di misure diverse su una linea che ondeggia appena.
                $d = 'M0,' . $H . 'L' . self::p( 0, $H * ( 0.5 + $r() * 0.1 ) );
                $x = 0;
                while ( $x < $W ) {
                    $passo = ( 170 - 90 * $k ) * ( 0.5 + $r() * 1.1 );
                    $nx    = min( $W, $x + $passo );
                    $ny    = $cl( $H * ( 0.46 + $r() * 0.16 ) );
                    $rx    = $passo * ( 0.52 + $r() * 0.3 );
                    $d    .= 'A' . round( $rx ) . ',' . round( $rx * ( 0.75 + $r() * 0.35 ) ) . ' 0 0 1 ' . self::p( $nx, $ny );
                    $x     = $nx;
                }
                return $d . 'L' . $W . ',' . $H . 'Z';

            case 'citta':
                // Palazzi di altezze diverse, qualcuno a gradoni, qualche antenna.
                $punti = [];
                $x     = 0;
                while ( $x < $W ) {
                    $w = 30 + $r() * ( 130 - 70 * $k );
                    $y = $cl( $H * ( 0.2 + $r() * 0.6 ) );
                    if ( $r() < 0.25 ) {
                        $y2 = $cl( $y + $H * ( 0.06 + $r() * 0.1 ) );
                        $punti[] = [ $x, $y2 ];
                        $punti[] = [ $x + $w * 0.2, $y2 ];
                        $punti[] = [ $x + $w * 0.2, $y ];
                        $punti[] = [ $x + $w * 0.8, $y ];
                        $punti[] = [ $x + $w * 0.8, $y2 ];
                        $punti[] = [ $x + $w, $y2 ];
                    } else {
                        $punti[] = [ $x, $y ];
                        if ( $r() < 0.2 ) {
                            $m  = $x + $w / 2;
                            $an = $cl( $y - $H * ( 0.08 + $r() * 0.1 ) );
                            $punti[] = [ $m - 3, $y ];
                            $punti[] = [ $m - 3, $an ];
                            $punti[] = [ $m + 3, $an ];
                            $punti[] = [ $m + 3, $y ];
                        }
                        $punti[] = [ $x + $w, $y ];
                    }
                    $x += $w;
                }
                return self::spezzata( $punti );
        }
        return '';
    }

    // ── Resa ──────────────────────────────────────────────────────────────────────────────────

    /** Attributo uk-parallax (vuoto se non c'è movimento). */
    private function parallasse( array $parti, $movimento ) {
        if ( ! $movimento || ! $parti ) {
            return '';
        }
        $parti[] = 'target: !.olo-layerscape';
        $parti[] = 'easing: 0';
        $parti[] = 'media: (prefers-reduced-motion: no-preference)';
        return ' uk-parallax="' . esc_attr( implode( '; ', $parti ) ) . '"';
    }

    /** La scala delle altezze degli strati, da percentuale (40-150) a fattore. */
    private static function scala( $v ) {
        return round( max( 40, min( 150, (float) ( '' === $v || null === $v ? 100 : $v ) ) ) / 100, 3 );
    }

    /** Lo spostamento in verticale di uno strato a profondità $p (0 vicino, 100 lontano): vh, '' se fermo. */
    private static function corsa( $p, $intensita ) {
        $a = round( 22 * max( 0, min( 100, (float) $p ) ) / 100 * max( 0, min( 100, (float) $intensita ) ) / 100, 2 );
        return $a > 0 ? 'y: -' . $a . 'vh,' . $a . 'vh' : '';
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'ols-' . $this->chiave_stabile( $settings );
        $sel = '#' . $uid;

        $strati = [];
        foreach ( is_array( $s['strati'] ) ? $s['strati'] : [] as $st ) {
            if ( is_array( $st ) ) {
                $strati[] = $st;
            }
        }
        $n         = count( $strati );
        $mov       = ! empty( $s['movimento'] );
        $intensita = (float) $s['intensita'];
        $dietro    = max( 0, min( $n, (int) $s['testo_dietro'] ) );

        // Colori e misure (solo valori passati dai filtri)
        $c_alto   = $this->safe_color_css( $s['cielo_alto'] ) ?: 'var(--olo-color-dark)';
        $c_basso  = $this->safe_color_css( $s['cielo_basso'] ) ?: $c_alto;
        $c_astro  = $this->safe_color_css( $s['astro_colore'] ) ?: 'var(--olo-color-accent)';
        $c_stelle = $this->safe_color_css( $s['stelle_colore'] ) ?: 'var(--olo-color-light)';
        $c_nebbia = $this->safe_color_css( $s['nebbia_colore'] ) ?: 'var(--olo-color-light)';
        $altezza  = max( 200, min( 1600, absint( $s['altezza'] ) ) );
        $astro_mm = max( 16, min( 600, absint( $s['astro_misura'] ) ) );
        $alone    = max( 0, min( 100, absint( $s['astro_alone'] ) ) );
        $neb_op   = max( 0, min( 100, absint( $s['nebbia_opacita'] ) ) ) / 100;

        $cielo = (string) $s['cielo'];
        $fondo = 'sfumato' === $cielo ? "linear-gradient(180deg, {$c_alto} 0%, {$c_basso} 100%)" : ( 'tinta' === $cielo ? $c_alto : 'transparent' );

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS da colori passati da safe_color_css(), numeri forzati e selettori generati qui
        echo '<style>';
        echo "{$sel}{position:relative;overflow:hidden;isolation:isolate;--ols-altezza:{$altezza}px;height:var(--ols-altezza);background:{$fondo};" . $this->raggio_css( $s['raggio'] ) . '}';
        echo $this->css_per_dispositivo( $s, 'altezza', $sel, function ( $v ) { return '--ols-altezza:' . max( 200, min( 1600, absint( $v ) ) ) . 'px'; } );
        // Una scala per l'altezza di tutti gli strati: su uno schermo stretto e alto lo strato lontano si abbassa e lascia il cielo.
        echo "{$sel}{--ols-scala:" . self::scala( $s['strati_scala'] ) . '}';
        echo $this->css_per_dispositivo( $s, 'strati_scala', $sel, function ( $v ) { return '--ols-scala:' . self::scala( $v ); } );
        echo "{$sel} .ols-strato{position:absolute;left:0;right:0;bottom:calc(-1 * var(--ols-scorta));height:calc(var(--ols-h) + var(--ols-scorta));--ols-scorta:24vh;pointer-events:none}";
        echo "{$sel} .ols-mano{position:absolute;inset:0;will-change:transform}";
        echo "{$sel} .ols-sagoma{position:absolute;top:0;left:50%;height:var(--ols-h);width:max(100%, calc(var(--ols-h) * 4));transform:translateX(-50%);display:block}";
        echo "{$sel} .ols-sagoma path{fill:currentColor}";
        echo "{$sel} .ols-img{position:absolute;top:0;left:0;width:100%;height:var(--ols-h);object-fit:cover;object-position:center bottom;display:block}";
        echo "{$sel} .ols-base{position:absolute;left:0;right:0;top:calc(var(--ols-h) - 1px);bottom:0;background:currentColor}";
        echo "{$sel} .ols-img + .ols-base{display:none}";
        echo "{$sel} .ols-stelle{position:absolute;left:0;right:0;top:-24vh;height:calc(70% + 24vh);pointer-events:none}";
        echo "{$sel} .ols-stelle svg{width:100%;height:100%;display:block}";
        echo "{$sel} .ols-stelle circle{fill:{$c_stelle}}";
        echo "{$sel} .ols-stelle .ols-sc{animation:ols-sc 3.2s ease-in-out infinite alternate}";
        echo '@keyframes ols-sc{from{opacity:1}to{opacity:.25}}';
        // Posizione e dimensione del sole per dispositivo: variabili sulla scena, una regola per ogni dispositivo che ha un valore.
        $ax0 = max( 0, min( 100, (float) $s['astro_x'] ) );
        $ay0 = max( 0, min( 100, (float) $s['astro_y'] ) );
        echo "{$sel}{--ols-ax:{$ax0}%;--ols-ay:{$ay0}%;--ols-am:{$astro_mm}px}";
        echo $this->css_per_dispositivo( $s, 'astro_x', $sel, function ( $v ) { return '--ols-ax:' . max( 0, min( 100, (float) $v ) ) . '%'; } );
        echo $this->css_per_dispositivo( $s, 'astro_y', $sel, function ( $v ) { return '--ols-ay:' . max( 0, min( 100, (float) $v ) ) . '%'; } );
        echo $this->css_per_dispositivo( $s, 'astro_misura', $sel, function ( $v ) { return '--ols-am:' . max( 16, min( 600, absint( $v ) ) ) . 'px'; } );
        echo "{$sel} .ols-astro{position:absolute;left:var(--ols-ax);top:var(--ols-ay);width:var(--ols-am);height:var(--ols-am);margin:calc(var(--ols-am) / -2) 0 0 calc(var(--ols-am) / -2);pointer-events:none}";
        echo "{$sel} .ols-disco{position:relative;width:100%;height:100%;border-radius:50%;background:{$c_astro}}";
        // Alone con una sfumatura radiale larga quattro volte il disco: un box-shadow così sfocato Chrome lo taglia
        // con un bordo netto (sfocatura oltre il massimo del motore grafico).
        echo "{$sel} .ols-alone{position:absolute;inset:-150%;border-radius:50%;background:radial-gradient(circle closest-side, color-mix(in srgb, {$c_astro} {$alone}%, transparent) 22%, color-mix(in srgb, {$c_astro} " . round( $alone * 0.42 ) . "%, transparent) 38%, color-mix(in srgb, {$c_astro} " . round( $alone * 0.12 ) . '%, transparent) 62%, transparent 100%)}';
        echo "{$sel} .ols-luna .ols-disco{background:radial-gradient(circle at 38% 34%, transparent 0 9%, color-mix(in srgb, {$c_astro} 82%, var(--olo-color-dark)) 10% 15%, transparent 16%), radial-gradient(circle at 64% 60%, transparent 0 12%, color-mix(in srgb, {$c_astro} 85%, var(--olo-color-dark)) 13% 19%, transparent 20%), {$c_astro}}";
        echo "{$sel} .ols-astro img{width:100%;height:100%;object-fit:contain;display:block}";
        echo "{$sel} .ols-nebbia{position:absolute;left:-25%;right:-25%;height:26%;bottom:calc(var(--ols-hf) * var(--ols-scala));pointer-events:none;opacity:{$neb_op};"
            . "background:radial-gradient(ellipse 18% 48% at 12% 55%, {$c_nebbia}, transparent 70%),radial-gradient(ellipse 22% 42% at 38% 50%, {$c_nebbia}, transparent 72%),radial-gradient(ellipse 16% 50% at 62% 58%, {$c_nebbia}, transparent 70%),radial-gradient(ellipse 24% 44% at 86% 52%, {$c_nebbia}, transparent 72%);filter:blur(6px)}";
        echo "{$sel} .ols-nebbia.ols-scorre{animation:ols-neb 46s linear infinite alternate}";
        echo '@keyframes ols-neb{from{transform:translateX(0)}to{transform:translateX(-14%)}}';
        echo '@media (prefers-reduced-motion: reduce){' . "{$sel} .ols-sc,{$sel} .ols-nebbia{animation:none !important}}";

        // Testo
        $pos   = [ 'alto' => 'flex-start', 'centro' => 'center', 'basso' => 'flex-end' ][ (string) $s['testo_posizione'] ] ?? 'flex-start';
        $al    = in_array( (string) $s['testo_allineamento'], [ 'left', 'center', 'right' ], true ) ? (string) $s['testo_allineamento'] : 'center';
        $croce = [ 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' ][ $al ];
        $pad   = Olobuild_Tile_Utils::sides_css( Olobuild_Tile_Utils::spacing_sides( $s['testo_padding'] ?? null, [], [ 72, 24, 72, 24 ] ) );
        $largo = max( 240, min( 1600, absint( $s['testo_larghezza'] ) ) );
        echo "{$sel} .ols-testo{position:absolute;inset:0;display:flex;flex-direction:column;justify-content:{$pos};align-items:{$croce};text-align:{$al};padding:{$pad};box-sizing:border-box}";
        echo "{$sel} .ols-testo-in{max-width:{$largo}px;width:100%;display:flex;flex-direction:column;align-items:{$croce};gap:.6em}";
        // Posizione, allineamento e padding del testo per dispositivo
        $verticale = [ 'alto' => 'flex-start', 'centro' => 'center', 'basso' => 'flex-end' ];
        $di_lato   = [ 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' ];
        echo $this->css_per_dispositivo( $s, 'testo_posizione', "{$sel} .ols-testo", function ( $v ) use ( $verticale ) { return isset( $verticale[ $v ] ) ? 'justify-content:' . $verticale[ $v ] : ''; } );
        echo $this->css_per_dispositivo( $s, 'testo_allineamento', "{$sel} .ols-testo", function ( $v ) use ( $di_lato ) { return isset( $di_lato[ $v ] ) ? 'align-items:' . $di_lato[ $v ] . ';text-align:' . $v : ''; } );
        echo $this->css_per_dispositivo( $s, 'testo_allineamento', "{$sel} .ols-testo-in", function ( $v ) use ( $di_lato ) { return isset( $di_lato[ $v ] ) ? 'align-items:' . $di_lato[ $v ] : ''; } );
        echo $this->css_per_dispositivo( $s, 'testo_padding', "{$sel} .ols-testo", function ( $v ) { return is_array( $v ) ? 'padding:' . Olobuild_Tile_Utils::sides_css( Olobuild_Tile_Utils::spacing_sides( $v, [], [ 72, 24, 72, 24 ] ) ) : ''; } );
        echo $this->css_testo( $s, 'occhiello', "{$sel} .ols-occhiello", 'margin:0;' );
        echo $this->css_testo( $s, 'titolo', "{$sel} .ols-titolo", 'margin:0;text-wrap:balance;' );
        echo $this->css_testo( $s, 'testo', "{$sel} .ols-frase", 'margin:0;max-width:42em;' );
        $pf = $this->safe_color_css( $s['pulsante_fondo'] );
        $pc = $this->safe_color_css( $s['pulsante_colore'] );
        echo "{$sel} .ols-btn{margin-top:.6em;" . ( $pf ? "background:{$pf};border-color:{$pf};" : '' ) . ( $pc ? "color:{$pc};" : '' ) . '}';
        echo "{$sel} .ols-btn:focus-visible{outline:none;box-shadow:0 0 0 3px color-mix(in srgb, var(--olo-color-primary) 35%, transparent)}";
        echo '</style>';
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped

        echo '<div id="' . esc_attr( $uid ) . '" class="olo-layerscape">';

        // Stelle
        if ( ! empty( $s['stelle'] ) ) {
            $r  = self::caso( 4242 );
            $nn = max( 0, min( 400, absint( $s['stelle_numero'] ) ) );
            $cc = '';
            for ( $i = 0; $i < $nn; $i++ ) {
                $scint = ! empty( $s['stelle_scintillio'] ) && 0 === $i % 3;
                $cc   .= '<circle cx="' . round( $r() * 1000, 1 ) . '" cy="' . round( $r() * 1000, 1 ) . '" r="' . round( 0.9 + $r() * 1.8, 2 ) . '"'
                    . ( $scint ? ' class="ols-sc" style="animation-delay:-' . round( $r() * 3.2, 2 ) . 's"' : '' ) . '/>';
            }
            $parti = array_filter( [ $mov ? self::corsa( 100, $intensita ) : '', 'scorrendo' === $s['stelle_comparsa'] ? 'opacity: 0,1,1' : '' ] );
            echo '<div class="ols-stelle" aria-hidden="true"' . $this->parallasse( $parti, $mov || 'scorrendo' === $s['stelle_comparsa'] ) . '><svg viewBox="0 0 1000 1000" preserveAspectRatio="xMidYMid slice">' . $cc . '</svg></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cerchi con numeri generati qui
        }

        // Sole o luna
        $astro = (string) $s['astro'];
        if ( in_array( $astro, [ 'sole', 'luna', 'immagine' ], true ) ) {
            $moto = (string) $s['astro_moto'];
            $giro  = round( 30 * max( 0, min( 100, $intensita ) ) / 100, 2 );
            $corsa_astro = 'tramonta' === $moto ? "y: -{$giro}vh,{$giro}vh" : ( 'sorge' === $moto ? "y: {$giro}vh,-{$giro}vh" : self::corsa( 95, $intensita ) );
            if ( $giro <= 0 && 'fermo' !== $moto ) {
                $corsa_astro = '';
            }
            $dentro = 'immagine' === $astro
                ? ( $s['astro_immagine'] ? '<img src="' . esc_url( $s['astro_immagine'] ) . '" alt="" loading="lazy" decoding="async">' : '' )
                : ( $alone > 0 ? '<div class="ols-alone"></div>' : '' ) . '<div class="ols-disco"></div>';
            if ( '' !== $dentro ) {
                echo '<div class="ols-astro' . ( 'luna' === $astro ? ' ols-luna' : '' ) . '" aria-hidden="true"' . $this->parallasse( $corsa_astro ? [ $corsa_astro ] : [], $mov ) . '>' . $dentro . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- numeri forzati, attributo da parallasse() con esc_attr(), immagine con esc_url()
            }
        }

        // Strati, dal più lontano al più vicino; nebbia sopra ogni strato tranne il più vicino; il testo dietro i $dietro più vicini.
        foreach ( $strati as $i => $st ) {
            $forma = in_array( (string) ( $st['forma'] ?? '' ), self::FORME, true ) ? (string) $st['forma'] : 'montagne';
            $h     = max( 4, min( 100, (float) ( $st['altezza'] ?? 50 ) ) );
            $prof  = max( 0, min( 100, (float) ( $st['profondita'] ?? 50 ) ) );
            $col   = $this->safe_color_css( $st['colore'] ?? '' ) ?: 'var(--olo-color-dark)';
            $op    = max( 0, min( 100, (float) ( $st['opacita'] ?? 100 ) ) ) / 100;
            $blur  = max( 0, min( 20, (float) ( $st['sfocatura'] ?? 0 ) ) );
            $stile = '--ols-h:calc(var(--ols-altezza) * ' . round( $h / 100, 4 ) . ' * var(--ols-scala));z-index:' . ( 10 + 2 * $i ) . ';color:' . $col . ';'
                . ( $op < 1 ? 'opacity:' . $op . ';' : '' ) . ( $blur > 0 ? 'filter:blur(' . $blur . 'px);' : '' );
            if ( 'immagine' === $forma ) {
                $dentro = ! empty( $st['immagine'] ) ? '<img class="ols-img" src="' . esc_url( $st['immagine'] ) . '" alt="" loading="lazy" decoding="async">' : '';
            } else {
                $dentro = '<svg class="ols-sagoma" viewBox="0 0 ' . self::VW . ' ' . self::VH . '" preserveAspectRatio="none" focusable="false"><path d="' . esc_attr( self::sagoma( $forma, $st['variante'] ?? 1, $st['dettaglio'] ?? 50 ) ) . '"/></svg>';
            }
            $mano = ! empty( $s['mouse'] ) ? ' data-ols-mouse="' . round( ( 100 - $prof ) / 100, 2 ) . '"' : '';
            echo '<div class="ols-strato" style="' . esc_attr( $stile ) . '" aria-hidden="true"' . $this->parallasse( array_filter( [ self::corsa( $prof, $intensita ) ] ), $mov ) . '><div class="ols-mano"' . $mano . '>' . $dentro . '<div class="ols-base"></div></div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- stile con esc_attr(), tracciato con esc_attr(), immagine con esc_url(), attributo da parallasse()

            // La nebbia sale dalla valle fra questo strato e quello davanti: parte da metà del crinale davanti
            // (che ne nasconde la parte bassa) e velata il piede di questo; si muove a metà fra i due.
            if ( ! empty( $s['nebbia'] ) && $i < $n - 1 ) {
                $davanti = $strati[ $i + 1 ];
                $h2      = max( 4, min( 100, (float) ( $davanti['altezza'] ?? 50 ) ) );
                $prof2   = max( 0, min( 100, (float) ( $davanti['profondita'] ?? 50 ) ) );
                echo '<div class="ols-nebbia' . ( ! empty( $s['nebbia_scorre'] ) ? ' ols-scorre' : '' ) . '" style="--ols-hf:' . round( $h2 * 0.5, 2 ) . '%;z-index:' . ( 11 + 2 * $i ) . '" aria-hidden="true"' . $this->parallasse( array_filter( [ self::corsa( ( $prof + $prof2 ) / 2, $intensita ) ] ), $mov ) . '></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- numeri forzati, attributo da parallasse()
            }
            if ( $i === $n - 1 - $dietro ) {
                $this->render_testo( $s, 11 + 2 * $i, $mov, $intensita );
            }
        }
        if ( 0 === $n || $dietro >= $n ) {
            $this->render_testo( $s, 9, $mov, $intensita );
        }
        echo '</div>';

        // Col mouse: gli strati vicini si spostano di più (solo puntatore fine, mai con «meno movimento»).
        if ( ! empty( $s['mouse'] ) ) {
            ?>
            <script>
            (function(){
                var s = document.getElementById(<?php echo wp_json_encode( $uid ); ?>);
                if (!s || !window.matchMedia) { return; }
                if (!matchMedia('(hover: hover) and (pointer: fine)').matches) { return; }
                if (matchMedia('(prefers-reduced-motion: reduce)').matches) { return; }
                var mani = [].slice.call(s.querySelectorAll('[data-ols-mouse]'));
                var tx = 0, ty = 0, cx = 0, cy = 0, giro = 0;
                function passo() {
                    cx += (tx - cx) * 0.08; cy += (ty - cy) * 0.08;
                    mani.forEach(function (m) {
                        var f = parseFloat(m.getAttribute('data-ols-mouse')) || 0;
                        m.style.transform = 'translate3d(' + (cx * f).toFixed(2) + 'px,' + (cy * f).toFixed(2) + 'px,0)';
                    });
                    giro = (Math.abs(tx - cx) > 0.05 || Math.abs(ty - cy) > 0.05) ? requestAnimationFrame(passo) : 0;
                }
                s.addEventListener('pointermove', function (e) {
                    var r = s.getBoundingClientRect();
                    tx = ((e.clientX - r.left) / r.width - 0.5) * -36;
                    ty = ((e.clientY - r.top) / r.height - 0.5) * -12;
                    if (!giro) { giro = requestAnimationFrame(passo); }
                });
                s.addEventListener('pointerleave', function () { tx = 0; ty = 0; if (!giro) { giro = requestAnimationFrame(passo); } });
            })();
            </script>
            <?php
        }
        return ob_get_clean();
    }

    /** Il blocco di testo (occhiello, titolo, frase, pulsante) allo z-index dato. */
    private function render_testo( $s, $z, $mov, $intensita ) {
        $occ = trim( (string) $s['occhiello'] );
        $tit = trim( (string) $s['titolo'] );
        $fra = trim( (string) $s['testo'] );
        $btn = trim( (string) $s['pulsante_testo'] );
        if ( '' === $occ && '' === $tit && '' === $fra && '' === $btn ) {
            return;
        }
        $tag = in_array( (string) $s['titolo_tag'], [ 'h1', 'h2', 'h3', 'h4', 'p' ], true ) ? (string) $s['titolo_tag'] : 'h2';
        echo '<div class="ols-testo" style="z-index:' . (int) $z . '"' . $this->parallasse( array_filter( [ self::corsa( $s['testo_profondita'], $intensita ) ] ), $mov ) . '><div class="ols-testo-in">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- intero e attributo da parallasse()
        if ( '' !== $occ ) {
            echo '<p class="ols-occhiello">' . esc_html( $occ ) . '</p>';
        }
        if ( '' !== $tit ) {
            echo '<' . $tag . ' class="ols-titolo">' . nl2br( esc_html( $tit ) ) . '</' . $tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $tag da elenco fisso
        }
        if ( '' !== $fra ) {
            echo '<p class="ols-frase">' . esc_html( $fra ) . '</p>';
        }
        if ( '' !== $btn ) {
            echo '<a class="uk-button uk-button-primary ols-btn" href="' . esc_url( (string) $s['pulsante_link'] ?: '#' ) . '">' . esc_html( $btn ) . '</a>';
        }
        echo '</div></div>';
    }

    /** CSS di un testo dal controllo tipografia (famiglia, misura per dispositivo, peso, maiuscolo, spaziatura, interlinea, colore). */
    private function css_testo( $s, $k, $selettore, $base ) {
        $d   = $base;
        $fam = $this->resolve_font_family( $s[ $k . '_font_family' ] ?? '' );
        $fs  = (float) ( $s[ $k . '_font_size' ] ?? 0 );
        $fw  = $this->font_weight_css( $s[ $k . '_font_weight' ] ?? '' );
        $ls  = $s[ $k . '_letter_spacing' ] ?? '';
        $lh  = $s[ $k . '_line_height' ] ?? '';
        $col = $this->safe_color_css( $s[ $k . '_color' ] ?? '' );
        if ( $fam ) { $d .= "font-family:{$fam};"; }
        if ( $fs > 0 ) { $d .= 'font-size:' . $fs . 'px;'; }
        if ( $fw ) { $d .= "font-weight:{$fw};"; }
        if ( ! empty( $s[ $k . '_uppercase' ] ) ) { $d .= 'text-transform:uppercase;'; }
        if ( is_numeric( $ls ) ) { $d .= 'letter-spacing:' . (float) $ls . 'px;'; }
        if ( is_numeric( $lh ) ) { $d .= 'line-height:' . (float) $lh . ';'; }
        if ( $col ) { $d .= "color:{$col};"; }
        $css = "{$selettore}{{$d}}";
        foreach ( [ 'tablet' => 960, 'mobile' => 480 ] as $disp => $px ) {
            $v = (float) ( $s[ $k . '_font_size_' . $disp ] ?? 0 );
            if ( $v > 0 ) {
                $css .= '@media (max-width:' . $px . 'px){' . $selettore . '{font-size:' . $v . 'px}}';
            }
        }
        return $css;
    }

    /** border-radius della scena dal controllo Raggio ('' se tutti a zero). */
    private function raggio_css( $r ) {
        $v = Olobuild_Tile_Utils::border_radius( $r );
        return ( '' === $v || preg_match( '/^(0(px)?\s*)+$/', trim( (string) $v ) ) ) ? '' : 'border-radius:' . $v . ';';
    }
}
