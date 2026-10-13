<?php
/**
 * Olobuild_Sipario — il sipario di velluto che si apre sopra una scena (oggi: la tile Hero).
 *
 * Tutto il movimento dipende da UNA variabile CSS, --osip-p (0 = chiuso, 1 = aperto): i teli,
 * la luce che filtra nello spacco, le scritte, la mantovana e le nappe la leggono con calc().
 * Coi modi a tempo (all'apertura, quando entra nello schermo, al clic) la variabile passa da 0
 * a 1 con una transizione CSS (@property); scorrendo la scrive lo script, punto per punto.
 *
 * Tre aperture, coi nomi del teatro:
 *  - all'italiana: i teli si raccolgono in diagonale verso gli angoli alti (un arco), legati da
 *    una nappa; sotto la nappa resta una quinta che scende fino a terra;
 *  - alla greca: i teli scorrono ai lati, le pieghe si stringono;
 *  - alla tedesca: il telo sale dritto dietro la mantovana.
 * Dopo l'apertura possono restare le quinte (e la mantovana), solo la mantovana, o niente.
 *
 * Chiavi `curtain_*` della tile, tutte senza effetto finché `curtain_on` è spento.
 * Niente movimento per chi chiede di ridurlo (si apre subito); nel builder il sipario resta
 * fermo, aperto o chiuso secondo «Nel builder mostra».
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Sipario {

    const DEFAULTS = [
        'curtain_on'          => false,
        // comportamento (tab Contenuto)
        'curtain_opening'     => 'italiana',
        'curtain_trigger'     => 'load',
        'curtain_delay'       => 0.8,
        'curtain_duration'    => 2.8,
        'curtain_scroll_run'  => 45,
        'curtain_after'       => 'quinte',
        'curtain_repeat'      => false,
        'curtain_builder'     => 'aperto',
        'curtain_eyebrow'     => '',
        'curtain_title'       => '',
        'curtain_invite'      => '',
        'curtain_emblem'      => 'stemma',
        'curtain_monogram'    => '',
        'curtain_icon'        => '',
        'curtain_image'       => '',
        // aspetto (tab Stile)
        'curtain_color'       => '',
        'curtain_sheen'       => 40,
        'curtain_folds'       => 13,
        'curtain_fold_depth'  => 60,
        'curtain_gold'        => '',
        'curtain_fringe'      => true,
        'curtain_fringe_h'    => 26,
        'curtain_valance'     => true,
        'curtain_swags'       => 5,
        'curtain_valance_h'   => 120,
        'curtain_quinte'      => 11,
        'curtain_tiebacks'    => true,
        'curtain_footlights'  => true,
        'curtain_gap_light'   => true,
        'curtain_light_color' => '',
        'curtain_text_color'  => '',
        'curtain_emblem_size' => 110,
        'curtain_title_font_family'    => '',
        'curtain_title_font_size'      => 64,
        'curtain_title_font_weight'    => '',
        'curtain_title_letter_spacing' => '',
        'curtain_title_line_height'    => '1.05',
        'curtain_title_text_transform' => '',
        'curtain_title_italic'         => false,
        'curtain_title_color'          => '',
        'curtain_eyebrow_font_size'    => 13,
        'curtain_eyebrow_letter_spacing' => '',
        'curtain_eyebrow_color'        => '',
        'curtain_invite_font_size'     => 14,
        'curtain_invite_color'         => '',
    ];

    const SOGLIE = [ 'tablet_landscape' => 1200, 'tablet' => 960, 'mobile_landscape' => 640, 'mobile' => 480 ];

    /** Il sipario è acceso? */
    public static function attivo( $s ) {
        return ! empty( $s['curtain_on'] );
    }

    /** Colore sicuro (token, hex, rgb, color-mix) o ''. */
    private static function colore( $v ) {
        $v = trim( (string) $v );
        if ( '' === $v ) {
            return '';
        }
        if ( preg_match( '/^#[0-9a-f]{3,8}$/i', $v ) ) {
            return $v;
        }
        if ( preg_match( '/^(?:rgba?|hsla?|color-mix|var)\([^;{}<>]*\)$/i', $v ) ) {
            return $v;
        }
        if ( preg_match( '/^[a-z]+$/i', $v ) ) {
            return $v;
        }
        return '';
    }

    private static function num( $v, $min, $max, $def ) {
        if ( ! is_numeric( $v ) ) {
            return $def;
        }
        return max( $min, min( $max, (float) $v ) );
    }

    /** Punto di una curva di Bézier quadratica. */
    private static function bez( $a, $k, $b, $t ) {
        $u = 1 - $t;
        return [ $u * $u * $a[0] + 2 * $u * $t * $k[0] + $t * $t * $b[0], $u * $u * $a[1] + 2 * $u * $t * $k[1] + $t * $t * $b[1] ];
    }

    /**
     * Geometria dell'apertura all'italiana sulla tela SINISTRA (x, y in % della tela).
     * Chiusa è un rettangolo; aperta il bordo interno (lo spigolo in basso al centro) sale in
     * diagonale fino alla nappa e disegna l'arco; sotto la nappa la quinta scende fino a terra.
     * Stesso numero di punti nei due stati: ogni punto si muove in linea retta con --osip-p.
     *
     * @param float $q   Larghezza della quinta a terra, frazione della tela (0 = sparisce).
     * @return array [ 'punti' => [ [chiuso, aperto], … ], 'nappa' => [x, y] ]
     */
    private static function italiana( $q ) {
        $q = max( 0.0, min( 0.6, (float) $q ) ) * 100;
        if ( $q < 0.5 ) {
            // nessuna quinta: il telo si ritira tutto nell'angolo alto, sotto la mantovana
            $t = [ 0, 9 ];
            $k = [ 34, 0 ];
            $d = [ 0, 55 ];
            $q = 0;
        } else {
            $t = [ max( 3.5, $q * 0.6 ), 63 ];
            $k = [ 40, 1 ];
            $d = [ $t[0] * 0.72, 84 ];
        }
        $p   = [];
        $p[] = [ [ 0, 0 ], [ 0, 0 ] ];
        $p[] = [ [ 100, 0 ], [ 100, 0 ] ];
        for ( $i = 1; $i <= 10; $i++ ) {
            $f   = $i / 10;
            $p[] = [ [ 100, 100 * $f ], self::bez( [ 100, 0 ], $k, $t, $f ) ];
        }
        for ( $j = 1; $j <= 6; $j++ ) {
            $f   = $j / 6;
            $p[] = [ [ 100 - ( 100 - $q ) * $f, 100 ], self::bez( $t, $d, [ $q, 100 ], $f ) ];
        }
        $p[] = [ [ 0, 100 ], [ 0, 100 ] ];
        return [ 'punti' => $p, 'nappa' => $t ];
    }

    /** polygon() con ogni coordinata = chiuso + --osip-p × (aperto − chiuso). */
    private static function poligono( $punti ) {
        $c = static function ( $a, $b ) {
            $a = round( $a, 2 );
            $d = round( $b - $a, 2 );
            if ( abs( $d ) < 0.01 ) {
                return $a . '%';
            }
            return 'calc(' . $a . '% + var(--osip-p) * ' . $d . '%)';
        };
        $out = [];
        foreach ( $punti as $pp ) {
            $out[] = $c( $pp[0][0], $pp[1][0] ) . ' ' . $c( $pp[0][1], $pp[1][1] );
        }
        return 'polygon(' . implode( ',', $out ) . ')';
    }

    /**
     * Quinta per dispositivo (in % della larghezza della scena, 0-30). I valori per dispositivo li
     * legge la tile (chiavi curtain_quinte_<dispositivo>) e li passa in $disp.
     */
    private static function quinte_per_dispositivo( $s, $disp ) {
        $out = [ '' => self::num( $s['curtain_quinte'] ?? 11, 0, 30, 11 ) ];
        foreach ( self::SOGLIE as $d => $px ) {
            $v = $disp[ $d ] ?? '';
            if ( '' !== $v && null !== $v && is_numeric( $v ) ) {
                $out[ $d ] = self::num( $v, 0, 30, 11 );
            }
        }
        return $out;
    }

    /**
     * CSS del sipario, già con le regole per dispositivo, scopato su $sel (classe della scena).
     *
     * @param array  $s    Settings della tile (wp_parse_args coi DEFAULTS).
     * @param string $sel  Selettore della scena (es. '.olo-hero-abc123').
     * @param array  $opz  [ 'font_titolo' => famiglia CSS già risolta dalla tile, 'quinte' / 'titolo' => valori per dispositivo ].
     */
    public static function css( $s, $sel, $opz = [] ) {
        $s       = wp_parse_args( $s, self::DEFAULTS );
        $o       = $sel . ' .osip';
        $apert   = in_array( $s['curtain_opening'], [ 'italiana', 'greca', 'tedesca' ], true ) ? $s['curtain_opening'] : 'italiana';
        $dopo    = in_array( $s['curtain_after'], [ 'quinte', 'mantovana', 'via' ], true ) ? $s['curtain_after'] : 'quinte';
        $velluto = self::colore( $s['curtain_color'] ) ?: 'color-mix(in srgb, var(--olo-color-primary, #8e1f3a) 80%, var(--olo-color-dark, #14090d))';
        $oro     = self::colore( $s['curtain_gold'] ) ?: 'var(--olo-color-accent, #c9a45c)';
        $luce    = self::colore( $s['curtain_light_color'] ) ?: 'color-mix(in srgb, var(--olo-color-accent, #c9a45c) 55%, var(--olo-color-light, #fff8ec))';
        $testo   = self::colore( $s['curtain_text_color'] ) ?: $oro;
        $lucent  = self::num( $s['curtain_sheen'], 0, 100, 40 );
        $prof    = self::num( $s['curtain_fold_depth'], 0, 100, 60 );
        $pieghe  = (int) self::num( $s['curtain_folds'], 4, 40, 13 );
        $festoni = (int) self::num( $s['curtain_swags'], 2, 12, 5 );
        $mh      = (int) self::num( $s['curtain_valance_h'], 40, 320, 120 );
        $fr      = (int) self::num( $s['curtain_fringe_h'], 6, 70, 26 );
        $durata  = self::num( $s['curtain_duration'], 0.6, 8, 2.8 );
        $emb     = (int) self::num( $s['curtain_emblem_size'], 32, 240, 96 );

        // ombra e luce delle pieghe: più profondità = ombre più scure, più lucentezza = creste più chiare
        $scuro  = 'color-mix(in srgb, ' . $velluto . ' ' . round( 100 - $prof * 0.62 ) . '%, #000)';
        $chiaro = 'color-mix(in srgb, ' . $velluto . ' ' . round( 100 - $lucent * 0.34 ) . '%, #fff)';
        $orod   = 'color-mix(in srgb, ' . $oro . ' 58%, #000)';
        $quinte = self::quinte_per_dispositivo( $s, isset( $opz['quinte'] ) ? (array) $opz['quinte'] : [] );
        $resta  = ( 'quinte' === $dopo );

        $css  = '@property --osip-p{syntax:"<number>";inherits:true;initial-value:0}';
        $css .= $o . '{position:absolute;inset:0;z-index:6;pointer-events:none;overflow:hidden;--osip-p:0;'
            . '--osip-v:' . $velluto . ';--osip-vd:' . $scuro . ';--osip-vl:' . $chiaro . ';--osip-oro:' . $oro . ';--osip-orod:' . $orod . ';--osip-luce:' . $luce . ';'
            . '--osip-n:' . $pieghe . ';--osip-sw:' . $festoni . ';--osip-mh:min(' . $mh . 'px,' . round( $mh / 12, 2 ) . 'vw);--osip-fr:min(' . $fr . 'px,' . round( $fr / 11, 2 ) . 'vw);'
            . 'transition:--osip-p ' . $durata . 's cubic-bezier(.68,0,.3,1);}';
        $css .= $o . '.osip--aperto{--osip-p:1}';
        $css .= $o . '.osip--subito,' . $o . '.osip--scrub,' . $o . '.osip--fermo{transition:none}';
        $css .= $o . '.osip--clic:not(.osip--aperto){pointer-events:auto;cursor:pointer}';
        // rete di sicurezza: se lo script non parte, il sipario si apre da sé dopo qualche secondo
        $css .= $o . ':not(.osip--vivo):not(.osip--fermo){animation:osip-sicuro 1.2s ease 6s forwards}@keyframes osip-sicuro{to{--osip-p:1}}';

        // luce nello spacco: si accende appena i teli si separano, poi si spegne
        if ( ! empty( $s['curtain_gap_light'] ) && 'tedesca' !== $apert ) {
            $css .= $o . ' .osip-luce{position:absolute;left:50%;top:0;bottom:0;width:46%;transform:translateX(-50%) scaleX(calc(.25 + var(--osip-p) * 1.4));'
                . 'background:radial-gradient(ellipse 34% 62% at 50% 58%,color-mix(in srgb, var(--osip-luce) 70%, transparent),transparent 72%);opacity:calc(min(1, var(--osip-p) * 6) * (1 - var(--osip-p)))}';
        }

        // teli
        $css .= $o . ' .osip-tela{position:absolute;top:0;bottom:0;width:50.6%;filter:drop-shadow(0 1.2rem 1.6rem rgba(0,0,0,.45))}';
        $css .= $o . ' .osip-sx{left:0}' . $o . ' .osip-dx{right:0}';
        $css .= $o . ' .osip-specchio,' . $o . ' .osip-stoffa,' . $o . ' .osip-pieghe{position:absolute;inset:0}';
        $css .= $o . ' .osip-dx .osip-specchio{transform:scaleX(-1)}';
        $strati = [];
        if ( ! empty( $s['curtain_footlights'] ) ) {
            $strati[] = 'linear-gradient(0deg,color-mix(in srgb, var(--osip-luce) 16%, transparent) 0,transparent 24%)';
        }
        $strati[] = 'radial-gradient(120% 70% at 22% 30%,rgba(255,255,255,' . round( $lucent / 900, 3 ) . '),transparent 62%)';
        $strati[] = 'linear-gradient(180deg,rgba(0,0,0,.45) 0,rgba(0,0,0,0) 20%,rgba(0,0,0,0) 72%,rgba(0,0,0,.32) 100%)';
        $strati[] = 'linear-gradient(90deg,rgba(0,0,0,.3) 0,rgba(0,0,0,0) 10%,rgba(0,0,0,0) 86%,rgba(0,0,0,.42) 100%)';
        // Le pieghe sono strisce ripetute su tutta la tela (niente tessere: con l'angolo che cambia
        // le tessere mostrerebbero le cuciture). All'italiana il telo tirato verso l'angolo alto le
        // inclina e le stringe: l'angolo e il passo seguono --osip-p.
        if ( 'italiana' === $apert ) {
            $ang = 'calc(90deg - var(--osip-p) * 9deg)';
            $n   = '(var(--osip-n) * (1 + var(--osip-p) * .35))';
        } else {
            $ang = '90deg';
            $n   = 'var(--osip-n)';
        }
        $passo = static function ( $pct, $molt = '1' ) use ( $n ) {
            return 'calc(' . $pct . '% / (' . $n . ' * ' . $molt . '))';
        };
        $strati[] = 'repeating-linear-gradient(' . $ang . ',rgba(0,0,0,.16) 0,transparent ' . $passo( 46, '.61' ) . ',rgba(255,255,255,.05) ' . $passo( 72, '.61' ) . ',transparent ' . $passo( 100, '.61' ) . ')';
        $strati[] = 'repeating-linear-gradient(' . $ang . ',var(--osip-vd) 0,var(--osip-v) ' . $passo( 22 ) . ',var(--osip-vl) ' . $passo( 40 ) . ',var(--osip-v) ' . $passo( 58 ) . ',var(--osip-vd) ' . $passo( 84 ) . ',var(--osip-vd) ' . $passo( 100 ) . ')';
        $css .= $o . ' .osip-pieghe{background:' . implode( ',', $strati ) . ',var(--osip-v)}';
        if ( ! empty( $s['curtain_fringe'] ) ) {
            $css .= $o . ' .osip-frangia{position:absolute;left:0;right:0;bottom:0;height:var(--osip-fr);'
                . 'background:linear-gradient(180deg,var(--osip-orod) 0,var(--osip-oro) 12%,var(--osip-orod) 26%,transparent 26%),repeating-linear-gradient(90deg,var(--osip-oro) 0 .09rem,var(--osip-orod) .09rem .2rem);'
                . '-webkit-mask:linear-gradient(180deg,#000 82%,transparent);mask:linear-gradient(180deg,#000 82%,transparent)}';
        }

        // il movimento di ogni apertura, con la quinta del dispositivo
        $regola = static function ( $q ) use ( $o, $apert, $resta ) {
            $frazione = $resta ? ( $q / 50.6 ) : 0;
            if ( 'italiana' === $apert ) {
                $g = self::italiana( $frazione );
                $r = $o . ' .osip-stoffa{clip-path:' . self::poligono( $g['punti'] ) . '}';
                $r .= $o . ' .osip-nappa{left:calc(' . round( $g['nappa'][0] * 0.506, 2 ) . '% - 1.4rem);top:calc(' . round( $g['nappa'][1], 2 ) . '% - 1.5rem)}';
                $r .= $o . ' .osip-nappa.osip-n-dx{left:auto;right:calc(' . round( $g['nappa'][0] * 0.506, 2 ) . '% - 1.4rem)}';
                return $r;
            }
            if ( 'greca' === $apert ) {
                return $o . ' .osip-stoffa{transform:scaleX(calc(1 - var(--osip-p) * ' . round( 1 - $frazione, 4 ) . '))}';
            }
            return $o . ' .osip-quinta{width:' . round( $q, 2 ) . '%}';
        };
        $css .= $regola( $quinte[''] );
        foreach ( self::SOGLIE as $d => $px ) {
            if ( isset( $quinte[ $d ] ) ) {
                $css .= '@media (max-width:' . $px . 'px){' . $regola( $quinte[ $d ] ) . '}';
            }
        }
        if ( 'italiana' === $apert ) {
            if ( $resta && ! empty( $s['curtain_tiebacks'] ) ) {
                $css .= $o . ' .osip-nappa{position:absolute;z-index:3;width:2.8rem;height:5.8rem;color:var(--osip-oro);opacity:calc((var(--osip-p) - .72) * 3.6);filter:drop-shadow(0 .3rem .4rem rgba(0,0,0,.5))}';
                $css .= $o . ' .osip-nappa svg{width:100%;height:100%;display:block}';
                $css .= $o . ' .osip-n-dx svg{transform:scaleX(-1)}';
            }
        } elseif ( 'greca' === $apert ) {
            $css .= $o . ' .osip-stoffa{transform-origin:0 50%}';
            if ( ! $resta ) {
                $css .= $o . ' .osip-tela{opacity:calc((1 - var(--osip-p)) * 14)}';
            }
        } else {
            $css .= $o . ' .osip-tela{transform:translateY(calc(var(--osip-p) * -101%))}';
            if ( $resta ) {
                $css .= $o . ' .osip-quinta{position:absolute;top:0;bottom:0;z-index:2;filter:drop-shadow(0 0 1.2rem rgba(0,0,0,.5))}';
                $css .= $o . ' .osip-q-sx{left:0}' . $o . ' .osip-q-dx{right:0}';
                $css .= $o . ' .osip-q-dx .osip-pieghe{transform:scaleX(-1)}';
            }
        }

        // scritte sul sipario chiuso (spariscono nei primi istanti dell'apertura)
        $font_t = isset( $opz['font_titolo'] ) && '' !== $opz['font_titolo'] ? $opz['font_titolo'] : "var(--olo-font-family-heading, Georgia, 'Times New Roman', serif)";
        $tcol   = self::colore( $s['curtain_title_color'] ) ?: $testo;
        $ecol   = self::colore( $s['curtain_eyebrow_color'] ) ?: $testo;
        $icol   = self::colore( $s['curtain_invite_color'] ) ?: $testo;
        $tfs    = (int) self::num( $s['curtain_title_font_size'], 18, 160, 64 );
        $efs    = (int) self::num( $s['curtain_eyebrow_font_size'], 9, 30, 13 );
        $ifs    = (int) self::num( $s['curtain_invite_font_size'], 10, 30, 14 );
        $tw     = preg_replace( '/[^0-9a-z]/i', '', (string) $s['curtain_title_font_weight'] );
        $tls    = is_numeric( $s['curtain_title_letter_spacing'] ) ? (float) $s['curtain_title_letter_spacing'] : null;
        $els    = is_numeric( $s['curtain_eyebrow_letter_spacing'] ) ? (float) $s['curtain_eyebrow_letter_spacing'] : null;
        $tlh    = is_numeric( $s['curtain_title_line_height'] ) ? (float) $s['curtain_title_line_height'] : 1.05;
        $ttt    = in_array( $s['curtain_title_text_transform'], [ 'uppercase', 'lowercase', 'capitalize', 'none' ], true ) ? $s['curtain_title_text_transform'] : '';
        $css .= $o . ' .osip-scritte{position:absolute;left:50%;top:calc(50% + var(--osip-mh) * .45);z-index:3;width:min(88%,46rem);display:flex;flex-direction:column;align-items:center;gap:.85rem;text-align:center;color:' . $testo . ';'
            . 'transform:translate(-50%,calc(-50% + var(--osip-p) * -2.4rem)) scale(calc(1 + var(--osip-p) * .07));opacity:calc(1 - var(--osip-p) * 5);text-shadow:0 .1rem 1.2rem rgba(0,0,0,.45)}';
        $css .= $o . '.osip--aperto .osip-scritte{visibility:hidden;transition:visibility 0s 1.4s}';
        $css .= $o . ' .osip-emblema{display:block;width:' . $emb . 'px;max-width:28vw;color:var(--osip-oro);filter:drop-shadow(0 .2rem .5rem rgba(0,0,0,.45))}';
        $css .= $o . ' .osip-emblema svg,' . $o . ' .osip-emblema img{display:block;width:100%;height:auto}';
        $css .= $o . ' .osip-emblema .olo-lucide-icon,' . $o . ' .osip-emblema .olo-lucide-icon svg{width:100%;height:100%}';
        $css .= $o . ' .osip-emblema text{font-family:' . $font_t . '}';
        $css .= $o . ' .osip-occhiello{font-size:' . $efs . 'px;letter-spacing:' . ( null !== $els ? $els . 'px' : '.32em' ) . ';text-transform:uppercase;font-weight:600;color:' . $ecol . '}';
        $css .= $o . ' .osip-titolo{margin:0;font-family:' . $font_t . ';font-size:clamp(28px,' . round( $tfs / 13, 2 ) . 'vw,' . $tfs . 'px);line-height:' . $tlh . ';color:' . $tcol . ';'
            . ( '' !== $tw ? 'font-weight:' . $tw . ';' : 'font-weight:400;' ) . ( null !== $tls ? 'letter-spacing:' . $tls . 'px;' : '' ) . ( '' !== $ttt ? 'text-transform:' . $ttt . ';' : '' )
            . ( ! empty( $s['curtain_title_italic'] ) ? 'font-style:italic;' : '' ) . '}';
        $tdisp = isset( $opz['titolo'] ) ? (array) $opz['titolo'] : [];
        foreach ( self::SOGLIE as $d => $px ) {
            $v = $tdisp[ $d ] ?? '';
            if ( '' !== $v && is_numeric( $v ) ) {
                $css .= '@media (max-width:' . $px . 'px){' . $o . ' .osip-titolo{font-size:' . (int) self::num( $v, 16, 160, 40 ) . 'px}}';
            }
        }
        $css .= $o . ' .osip-invito{font-size:' . $ifs . 'px;letter-spacing:.2em;text-transform:uppercase;color:' . $icol . ';opacity:.9}';
        $css .= $o . ' .osip-apri{font:inherit;font-size:' . $ifs . 'px;letter-spacing:.2em;text-transform:uppercase;color:' . $icol . ';background:transparent;border:1px solid currentColor;border-radius:999px;padding:.85em 1.8em;cursor:pointer;transition:background-color .3s,color .3s}';
        $css .= $o . ' .osip-apri:hover{background:color-mix(in srgb, ' . $icol . ' 16%, transparent)}';
        $css .= $o . ' .osip-apri:focus-visible{outline:2px solid ' . $icol . ';outline-offset:4px}';
        $css .= $o . ' .osip-invito i{display:block;width:1px;height:2.2rem;margin:.7rem auto 0;background:currentColor;transform-origin:top;animation:osip-filo 2.4s ease-in-out infinite}@keyframes osip-filo{0%{transform:scaleY(0)}50%{transform:scaleY(1);transform-origin:top}51%{transform-origin:bottom}100%{transform:scaleY(0);transform-origin:bottom}}';

        // mantovana a festoni: velluto curvo, gallone d'oro e frangia lungo il bordo
        if ( ! empty( $s['curtain_valance'] ) ) {
            $maschera = 'linear-gradient(#000 0 0) 0 0/100% 34% no-repeat,radial-gradient(ellipse 50% 100% at 50% 0,#000 98.6%,transparent 100%) 0 0/calc(100% / var(--osip-sw)) 100% repeat-x';
            $css .= $o . ' .osip-mantovana{position:absolute;left:0;right:0;top:0;z-index:4;height:calc(var(--osip-mh) + var(--osip-fr));filter:drop-shadow(0 .7rem .9rem rgba(0,0,0,.5))';
            $css .= ( 'via' === $dopo ) ? ';transform:translateY(calc(max(0, var(--osip-p) - .55) * -245%))}' : '}';
            $css .= $o . ' .osip-mantovana i{position:absolute;left:0;right:0;top:0;display:block;-webkit-mask:' . $maschera . ';mask:' . $maschera . '}';
            $css .= $o . ' .osip-m-fr{height:100%;background:repeating-linear-gradient(90deg,var(--osip-oro) 0 .09rem,var(--osip-orod) .09rem .2rem)}';
            $css .= $o . ' .osip-m-ga{height:calc(var(--osip-mh) + .22rem);background:linear-gradient(180deg,var(--osip-oro),var(--osip-orod))}';
            $css .= $o . ' .osip-m-fa{position:absolute;left:0;right:0;top:0;display:block;height:calc(var(--osip-mh) * .34);'
                . 'background:linear-gradient(180deg,rgba(0,0,0,.5),rgba(0,0,0,0) 62%,rgba(0,0,0,.28)),repeating-linear-gradient(90deg,var(--osip-vd) 0,var(--osip-v) .8rem,var(--osip-vl) 1.4rem,var(--osip-v) 2rem,var(--osip-vd) 2.8rem),var(--osip-v);'
                . 'box-shadow:inset 0 -.22rem 0 var(--osip-oro),0 .35rem .6rem rgba(0,0,0,.4)}';
            $css .= $o . ' .osip-m-ve{height:var(--osip-mh);background:linear-gradient(180deg,rgba(0,0,0,.42),rgba(0,0,0,0) 40%),'
                . 'url("' . self::pieghe_festone() . '") 0 0/calc(100% / var(--osip-sw)) 100% repeat-x,var(--osip-v)}';
            if ( empty( $s['curtain_fringe'] ) ) {
                $css .= $o . ' .osip-m-fr{display:none}';
            }
        }

        // ribalta: la luce calda dal basso, più forte a sipario chiuso
        if ( ! empty( $s['curtain_footlights'] ) ) {
            $css .= $o . ' .osip-ribalta{position:absolute;left:0;right:0;bottom:0;height:24%;z-index:5;background:linear-gradient(0deg,color-mix(in srgb, var(--osip-luce) 60%, transparent),transparent);mix-blend-mode:overlay;opacity:calc(.8 - var(--osip-p) * .45)}';
        }
        $css .= '@media (prefers-reduced-motion:reduce){' . $o . '{transition:none}' . $o . ' .osip-invito i{animation:none}}';
        return $css;
    }

    /**
     * Le pieghe di un festone: curve appese ai due angoli alti, sempre più profonde, in ombra e luce
     * (nero e bianco trasparenti sopra il colore del velluto, che resta un token). Data URI svg.
     */
    private static function pieghe_festone() {
        $g = '';
        $n = 5;
        for ( $i = 1; $i <= $n; $i++ ) {
            $picco = 34 + 66 * pow( $i / $n, 0.9 );   // fondo della piega al centro del festone
            $ctrl  = round( 2 * $picco - 34, 1 );
            $w     = round( 4 + $i * 1.1, 1 );
            $a     = round( -3 + $i * 1.6, 1 );
            $b     = round( 103 - $i * 1.6, 1 );
            $g    .= '<path d="M' . $a . ' 34Q50 ' . $ctrl . ' ' . $b . ' 34" stroke="rgba(0,0,0,.26)" stroke-width="' . $w . '"/>';
            $g    .= '<path d="M' . $a . ' 34Q50 ' . round( $ctrl - $w * 1.5, 1 ) . ' ' . $b . ' 34" stroke="rgba(255,255,255,.09)" stroke-width="' . round( $w * 0.5, 1 ) . '"/>';
        }
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><g fill="none">' . $g . '</g></svg>';
        return 'data:image/svg+xml,' . rawurlencode( $svg );
    }

    /** Stemma di serie: due rami d'alloro e le iniziali al centro. */
    private static function stemma( $sigla ) {
        $foglie = '';
        foreach ( [ -1, 1 ] as $lato ) {
            for ( $i = 0; $i < 9; $i++ ) {
                $f   = $i / 8;
                $ang = deg2rad( 104 + $f * 118 );
                $x   = 60 + 41 * cos( $ang );
                $y   = 60 + 41 * sin( $ang );
                $rot = rad2deg( $ang ) + 90 + ( $i % 2 ? 26 : -26 );
                $off = ( $i % 2 ) ? 4.2 : -4.2;
                $x  += $off * cos( $ang );
                $y  += $off * sin( $ang );
                if ( 1 === $lato ) {
                    $x   = 120 - $x;
                    $rot = 180 - $rot;
                }
                $rx      = 6.4 - $f * 1.6;
                $foglie .= '<ellipse cx="' . round( $x, 2 ) . '" cy="' . round( $y, 2 ) . '" rx="' . round( $rx, 2 ) . '" ry="' . round( $rx * 0.42, 2 ) . '" transform="rotate(' . round( $rot, 1 ) . ' ' . round( $x, 2 ) . ' ' . round( $y, 2 ) . ')"/>';
            }
        }
        $a0  = deg2rad( 104 );
        $a1  = deg2rad( 222 );
        $ram = '<path d="M' . round( 60 + 41 * cos( $a0 ), 2 ) . ' ' . round( 60 + 41 * sin( $a0 ), 2 ) . 'A41 41 0 0 1 ' . round( 60 + 41 * cos( $a1 ), 2 ) . ' ' . round( 60 + 41 * sin( $a1 ), 2 ) . '"/>'
            . '<path d="M' . round( 60 - 41 * cos( $a0 ), 2 ) . ' ' . round( 60 + 41 * sin( $a0 ), 2 ) . 'A41 41 0 0 0 ' . round( 60 - 41 * cos( $a1 ), 2 ) . ' ' . round( 60 + 41 * sin( $a1 ), 2 ) . '"/>';
        $fio = '<path d="M50 104c4-4 8-4 10 0 2-4 6-4 10 0M60 104l-7 10M60 104l7 10"/>';
        $txt = '' !== $sigla ? '<text x="60" y="71" text-anchor="middle" font-size="' . ( mb_strlen( $sigla ) > 2 ? 24 : 32 ) . '" letter-spacing="1" fill="currentColor" stroke="none">' . esc_html( $sigla ) . '</text>' : '';
        return '<svg viewBox="0 0 120 120" aria-hidden="true" focusable="false"><g fill="currentColor">' . $foglie . '</g><g fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">' . $ram . $fio . '</g>'
            . '<circle cx="60" cy="60" r="27" fill="none" stroke="currentColor" stroke-width=".8" opacity=".55"/>' . $txt . '</svg>';
    }

    /** Nappa d'oro: il cordone che lega il telo e il fiocco con la frangia. */
    private static function nappa() {
        return '<svg viewBox="0 0 44 92" aria-hidden="true" focusable="false"><g fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round">'
            . '<path d="M4 6c10 4 22 4 34 0M6 10c9 10 22 10 30 0"/><path d="M22 14v14"/></g><g fill="currentColor"><circle cx="22" cy="32" r="6"/>'
            . '<path d="M14 40h16l5 34H9z" opacity=".95"/></g><g stroke="currentColor" stroke-width="1.1" opacity=".75"><path d="M11 74v12M15 74v15M19 74v13M23 74v16M27 74v13M31 74v15M34 74v11"/></g></svg>';
    }

    /**
     * Markup del sipario (da mettere come ULTIMO figlio della scena, che deve avere position:relative).
     *
     * @param array  $s        Settings della tile.
     * @param string $emblema  HTML dell'icona o dell'immagine già preparato dalla tile ('' = stemma di serie o niente).
     * @param bool   $builder  Siamo nel canvas del builder: il sipario resta fermo.
     */
    public static function markup( $s, $emblema = '', $builder = false ) {
        $s      = wp_parse_args( $s, self::DEFAULTS );
        $apert  = in_array( $s['curtain_opening'], [ 'italiana', 'greca', 'tedesca' ], true ) ? $s['curtain_opening'] : 'italiana';
        $dopo   = in_array( $s['curtain_after'], [ 'quinte', 'mantovana', 'via' ], true ) ? $s['curtain_after'] : 'quinte';
        $quando = in_array( $s['curtain_trigger'], [ 'load', 'view', 'scroll', 'click' ], true ) ? $s['curtain_trigger'] : 'load';
        $cfg    = [
            'quando'  => $quando,
            'ritardo' => self::num( $s['curtain_delay'], 0, 10, 0.8 ),
            'corsa'   => self::num( $s['curtain_scroll_run'], 15, 100, 45 ),
            'ripeti'  => ! empty( $s['curtain_repeat'] ),
        ];
        $chiuso = $builder && 'chiuso' === $s['curtain_builder'];
        $classi = 'osip osip--' . $apert . ( $builder ? ' osip--fermo' . ( $chiuso ? '' : ' osip--aperto' ) : '' );
        $stile  = $builder ? ' style="--osip-p:' . ( $chiuso ? '0' : '1' ) . '"' : '';

        $tela = static function ( $lato ) use ( $s ) {
            return '<div class="osip-tela osip-' . $lato . '"><div class="osip-specchio"><div class="osip-stoffa"><div class="osip-pieghe"></div>'
                . ( ! empty( $s['curtain_fringe'] ) ? '<i class="osip-frangia"></i>' : '' ) . '</div></div></div>';
        };

        $h  = '<div class="' . esc_attr( $classi ) . '" data-osip="' . esc_attr( wp_json_encode( $cfg ) ) . '"' . $stile . '>';
        if ( ! empty( $s['curtain_gap_light'] ) && 'tedesca' !== $apert ) {
            $h .= '<div class="osip-luce" aria-hidden="true"></div>';
        }
        $h .= '<div aria-hidden="true">' . $tela( 'sx' ) . $tela( 'dx' ) . '</div>';
        if ( 'tedesca' === $apert && 'quinte' === $dopo ) {
            $h .= '<div class="osip-quinta osip-q-sx" aria-hidden="true"><div class="osip-pieghe"></div></div><div class="osip-quinta osip-q-dx" aria-hidden="true"><div class="osip-pieghe"></div></div>';
        }
        if ( 'italiana' === $apert && 'quinte' === $dopo && ! empty( $s['curtain_tiebacks'] ) ) {
            $n  = self::nappa();
            $h .= '<span class="osip-nappa osip-n-sx" aria-hidden="true">' . $n . '</span><span class="osip-nappa osip-n-dx" aria-hidden="true">' . $n . '</span>';
        }

        // scritte sul sipario chiuso
        $em = '';
        if ( 'stemma' === $s['curtain_emblem'] ) {
            $em = self::stemma( trim( (string) $s['curtain_monogram'] ) );
        } elseif ( in_array( $s['curtain_emblem'], [ 'icona', 'immagine' ], true ) ) {
            $em = (string) $emblema;
        }
        $occ = trim( (string) $s['curtain_eyebrow'] );
        $tit = trim( (string) $s['curtain_title'] );
        $inv = trim( (string) $s['curtain_invite'] );
        if ( '' !== $em || '' !== $occ || '' !== $tit || '' !== $inv || 'click' === $quando ) {
            $h .= '<div class="osip-scritte">';
            if ( '' !== $em ) {
                $h .= '<span class="osip-emblema">' . $em . '</span>';
            }
            if ( '' !== $occ ) {
                $h .= '<span class="osip-occhiello">' . esc_html( $occ ) . '</span>';
            }
            if ( '' !== $tit ) {
                $h .= '<span class="osip-titolo">' . wp_kses( $tit, [ 'br' => [], 'em' => [], 'i' => [], 'strong' => [], 'b' => [] ] ) . '</span>';
            }
            if ( 'click' === $quando ) {
                $h .= '<button type="button" class="osip-apri">' . esc_html( '' !== $inv ? $inv : __( 'Apri il sipario', 'olobuilder' ) ) . '</button>';
            } elseif ( '' !== $inv ) {
                $h .= '<span class="osip-invito" aria-hidden="true">' . esc_html( $inv ) . ( 'scroll' === $quando ? '<i></i>' : '' ) . '</span>';
            }
            $h .= '</div>';
        }
        if ( ! empty( $s['curtain_valance'] ) ) {
            $h .= '<div class="osip-mantovana" aria-hidden="true"><i class="osip-m-fr"></i><i class="osip-m-ga"></i><i class="osip-m-ve"></i><b class="osip-m-fa"></b></div>';
        }
        if ( ! empty( $s['curtain_footlights'] ) ) {
            $h .= '<div class="osip-ribalta" aria-hidden="true"></div>';
        }
        $h .= '</div>';
        if ( ! $builder ) {
            $h .= '<noscript><style>.osip{--osip-p:1!important}</style></noscript>';
            $h .= self::script();
        }
        return $h;
    }

    /**
     * Lo script: la funzione una volta per pagina, poi una chiamata per ogni sipario (il sipario è
     * l'elemento subito prima dello script). Niente && negli script in linea (wptexturize).
     */
    private static function script() {
        static $funzione = false;
        $js = '';
        if ( ! $funzione ) {
            $funzione = true;
            $js      .= 'if(!window.oloSipario){window.oloSipario=function(el){'
                . 'if(!el||el.getAttribute("data-osip-vivo")){return;}el.setAttribute("data-osip-vivo","1");'
                . 'var c={};try{c=JSON.parse(el.getAttribute("data-osip")||"{}");}catch(e){}'
                . 'var scena=el.parentElement;var cont=scena?scena.querySelector(".olo-hero-content"):null;'
                . 'el.classList.add("osip--vivo");var aperto=false;'
                . 'var rid=window.matchMedia?window.matchMedia("(prefers-reduced-motion: reduce)").matches:false;'
                . 'function apri(subito){if(aperto){return;}aperto=true;if(subito){el.classList.add("osip--subito");}el.style.removeProperty("--osip-p");el.classList.add("osip--aperto");if(cont){cont.removeAttribute("inert");}}'
                . 'function chiudi(){if(!aperto){return;}aperto=false;el.classList.remove("osip--aperto");}'
                . 'if(rid){apri(true);return;}'
                . 'var q=c.quando||"load";var rit=Math.max(0,Number(c.ritardo)||0)*1000;'
                . 'if(q==="load"){setTimeout(function(){apri(false);},rit);return;}'
                . 'if(q==="click"){el.classList.add("osip--clic");if(cont){cont.setAttribute("inert","");}'
                . 'el.addEventListener("click",function(){var era=aperto;apri(false);if(era){return;}var f=cont?cont.querySelector("a[href],button,input,[tabindex]"):null;if(f){setTimeout(function(){try{f.focus({preventScroll:true});}catch(e){}},500);}});return;}'
                . 'if(q==="view"){if(!("IntersectionObserver" in window)){apri(true);return;}var t=0;'
                . 'new IntersectionObserver(function(es){es.forEach(function(e){if(e.isIntersecting){if(!aperto){clearTimeout(t);t=setTimeout(function(){apri(false);},rit);}}else{clearTimeout(t);if(c.ripeti){chiudi();}}});},{threshold:0.45}).observe(scena||el);return;}'
                . 'if(q==="scroll"){el.classList.add("osip--scrub");var bx=scena||el;var corsa=Math.max(0.15,Math.min(1,(Number(c.corsa)||45)/100));var raf=0;var inizio=0;'
                . 'function misura(){var r=bx.getBoundingClientRect();var vh=window.innerHeight||800;var top=r.top+(window.pageYOffset||0);inizio=top-Math.max(0,Math.min(vh-r.height,top));}'
                . 'function giro(){raf=0;if(el.classList.contains("osip--fermo")){return;}var h=bx.offsetHeight||1;var p=((window.pageYOffset||0)-inizio)/(h*corsa);p=Math.max(0,Math.min(1,p));el.style.setProperty("--osip-p",p.toFixed(4));'
                . 'if(p>=1){if(cont){cont.removeAttribute("inert");}}}'
                . 'function chiedi(){if(!raf){raf=requestAnimationFrame(giro);}}'
                . 'misura();giro();window.addEventListener("scroll",chiedi,{passive:true});window.addEventListener("resize",function(){misura();chiedi();});return;}'
                . 'apri(false);};}';
        }
        $js .= '(function(s){var e=s?s.previousElementSibling:null;while(e){if(e.classList){if(e.classList.contains("osip")){break;}}e=e.previousElementSibling;}if(window.oloSipario){window.oloSipario(e);}})(document.currentScript);';
        return '<script>' . $js . '</script>';
    }
}
