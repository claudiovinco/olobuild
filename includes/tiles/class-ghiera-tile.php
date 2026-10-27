<?php
/**
 * Tile Ghiera — la ghiera dei modi di una fotocamera, da girare per scegliere fra molte voci (i servizi di uno
 * studio, i menù, le collezioni). Attorno al disco le sigle delle voci (una o due lettere, come M, A, S, P), la
 * zigrinatura sul bordo, le tacche fra una sigla e l'altra, l'indice fisso in alto; al centro il numero della voce.
 * Accanto, il pannello della voce scelta: foto o video, titolo, testo, prezzo e un pulsante.
 *
 * Si gira trascinando (col dito o col mouse, il disco segue l'angolo del puntatore e si ferma sulla tacca più vicina,
 * con una vibrazione breve sul telefono a ogni scatto), con la rotellina (solo se il puntatore ci è arrivato
 * muovendosi), con le frecce della tastiera (il disco è uno slider: Inizio e Fine vanno alla prima e all'ultima) o
 * toccando una sigla. La rotazione è cumulativa: si va sempre per la via più corta. Le voci cambiano con una
 * dissolvenza e un piccolo scorrimento; i video partono solo sulla voce scelta. Con «riduci movimento» il disco salta
 * alla tacca senza girare. Si adatta alla colonna (container query): ghiera accanto al pannello, o sopra.
 * Nata per il tema di uno studio fotografico con molti servizi (27 ott 2026). Config: src/config/elements/ghiera.js.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Ghiera_Tile extends Olobuild_Tile_Base {

    protected $type     = 'ghiera';
    protected $name     = 'Ghiera';
    protected $icon     = 'dashicons-marker';
    protected $category = 'interactive';

    protected $defaults = [
        // contenuto
        'voci'                  => [
            [ 'sigla' => 'M', 'titolo' => 'Matrimoni', 'testo' => 'Tutta la giornata, dai preparativi all’ultimo ballo: racconto, non pose.', 'prezzo' => 'da 1.900 €', 'immagine' => '', 'video' => '', 'link' => '', 'testo_link' => 'Prenota' ],
            [ 'sigla' => 'R', 'titolo' => 'Ritratti', 'testo' => 'In studio o all’aperto, un’ora e venti foto scelte insieme.', 'prezzo' => 'da 180 €', 'immagine' => '', 'video' => '', 'link' => '', 'testo_link' => 'Prenota' ],
            [ 'sigla' => 'B', 'titolo' => 'Brand', 'testo' => 'Le foto della tua attività per il sito e i social, in una mattina.', 'prezzo' => 'da 450 €', 'immagine' => '', 'video' => '', 'link' => '', 'testo_link' => 'Prenota' ],
            [ 'sigla' => 'E', 'titolo' => 'Eventi', 'testo' => 'Feste, inaugurazioni, concerti: arrivo presto e me ne vado per ultimo.', 'prezzo' => 'da 90 € l’ora', 'immagine' => '', 'video' => '', 'link' => '', 'testo_link' => 'Prenota' ],
        ],
        'partenza'              => 1,
        'titolo_centro'         => 'Servizi',
        'mostra_numero'         => true,
        'etichetta'             => 'Scegli il servizio',
        // stile
        'stile'                 => 'metallo',
        'colore_ghiera'         => '',
        'colore_sigle'          => '',
        'colore_indice'         => '',
        'sfondo_pannello'       => '',
        'colore_pulsante'       => '',
        'colore_testo_pulsante' => '',
        'ombra'                 => 'none',
        'ombra_h'               => '0',
        'ombra_v'               => '14',
        'ombra_blur'            => '40',
        'ombra_spread'          => '0',
        'ombra_color'           => 'rgba(0,0,0,0.22)',
        'ombra_inset'           => false,
        'sigla_font_family'     => '',
        'sigla_font_size'       => 18,
        'sigla_font_weight'     => '600',
        'titolo_font_family'    => '',
        'titolo_font_size'      => 40,
        'titolo_font_size_tablet' => '',
        'titolo_font_size_mobile' => 30,
        'titolo_font_weight'    => '',
        'titolo_line_height'    => 1.1,
        'titolo_color'          => '',
        'testo_font_family'     => '',
        'testo_font_size'       => 16,
        'testo_font_weight'     => '',
        'testo_line_height'     => 1.65,
        'testo_color'           => '',
        'prezzo_font_family'    => '',
        'prezzo_font_size'      => 15,
        'prezzo_font_weight'    => '600',
        'prezzo_color'          => '',
        'misura'                => 400,
        'raggio_media'          => [ 'tl' => 12, 'tr' => 12, 'br' => 12, 'bl' => 12 ],
        'raggio_pulsante'       => [ 'tl' => 999, 'tr' => 999, 'br' => 999, 'bl' => 999 ],
        'proporzione'           => '4/5',
        'disposizione'          => 'lato',
        'gap'                   => 56,
    ];

    private static $uid_usate = [];

    public function get_controls() {
        return [];
    }

    private static function url_di( $v ) {
        if ( is_array( $v ) ) {
            $v = $v['url'] ?? '';
        }
        return trim( (string) $v );
    }

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
        $css = '' !== $d ? "{$selettore}{{$d}}" : '';
        foreach ( [ 'tablet' => 960, 'mobile' => 480 ] as $disp => $px ) {
            $v = (float) ( $s[ $k . '_font_size_' . $disp ] ?? 0 );
            if ( $v > 0 ) {
                $css .= '@media (max-width:' . $px . 'px){' . $selettore . '{font-size:' . $v . 'px}}';
            }
        }
        return $css;
    }

    public function render( $settings ) {
        $s       = wp_parse_args( $settings, $this->defaults );
        $uid     = 'ogh-' . $this->chiave_stabile( $settings );
        $base    = $uid;
        $copia_n = 2;
        while ( isset( self::$uid_usate[ $uid ] ) ) {
            $uid = $base . '-' . $copia_n++;
        }
        self::$uid_usate[ $uid ] = true;
        $sel     = '#' . $uid;
        $builder = ! empty( $s['_builder_mode'] );

        // ── Voci ──
        $voci = [];
        foreach ( ( is_array( $s['voci'] ) ? $s['voci'] : [] ) as $v ) {
            if ( ! is_array( $v ) ) {
                continue;
            }
            $titolo = trim( wp_strip_all_tags( (string) ( $v['titolo'] ?? '' ) ) );
            $sigla  = trim( wp_strip_all_tags( (string) ( $v['sigla'] ?? '' ) ) );
            if ( '' === $titolo && '' === $sigla ) {
                continue;
            }
            if ( '' === $sigla ) {
                $sigla = function_exists( 'mb_substr' ) ? mb_strtoupper( mb_substr( $titolo, 0, 1, 'UTF-8' ), 'UTF-8' ) : strtoupper( substr( $titolo, 0, 1 ) );
            }
            $voci[] = [
                'sigla'  => function_exists( 'mb_substr' ) ? mb_substr( $sigla, 0, 3, 'UTF-8' ) : substr( $sigla, 0, 3 ),
                'titolo' => $titolo,
                'testo'  => trim( (string) ( $v['testo'] ?? '' ) ),
                'prezzo' => trim( wp_strip_all_tags( (string) ( $v['prezzo'] ?? '' ) ) ),
                'img'    => esc_url_raw( self::url_di( $v['immagine'] ?? '' ) ),
                'video'  => esc_url_raw( self::url_di( $v['video'] ?? '' ) ),
                'link'   => esc_url_raw( self::url_di( $v['link'] ?? '' ) ),
                'tlink'  => trim( wp_strip_all_tags( (string) ( $v['testo_link'] ?? '' ) ) ),
            ];
        }
        $n = count( $voci );
        if ( $n < 2 ) {
            if ( ! $builder ) {
                return '';
            }
            return '<div class="olo-ghiera-vuota" style="padding:1.5rem;border:1px dashed currentColor;opacity:.7;font-size:14px">' . esc_html__( 'La ghiera ha bisogno di almeno due voci: aggiungile nel Contenuto.', 'olobuilder' ) . '</div>';
        }
        $passo    = 360 / $n;
        $partenza = max( 0, min( $n - 1, (int) $s['partenza'] - 1 ) );

        // ── Misure e colori ──
        $d        = max( 240, min( 640, (int) $s['misura'] ) );
        $gap      = max( 0, min( 140, (int) $s['gap'] ) );
        $metallo  = 'piatto' !== (string) $s['stile'];
        $ghiera   = $this->safe_color_css( $s['colore_ghiera'] ) ?: 'var(--olo-color-dark, #1d1b19)';
        $sigle    = $this->safe_color_css( $s['colore_sigle'] ) ?: ( Olobuild_Tile_Utils::testo_su_fondo( $ghiera, 'var(--olo-color-light, #ffffff)' ) ?: 'var(--olo-color-light, #ffffff)' );
        $indice   = $this->safe_color_css( $s['colore_indice'] ) ?: 'var(--olo-color-primary)';
        $pannello = $this->safe_color_css( $s['sfondo_pannello'] );
        $btn      = $this->safe_color_css( $s['colore_pulsante'] ) ?: 'var(--olo-color-primary)';
        $btn_t    = $this->safe_color_css( $s['colore_testo_pulsante'] ) ?: ( $this->safe_color_css( $s['colore_pulsante'] ) ? ( Olobuild_Tile_Utils::testo_su_fondo( $btn, '#ffffff' ) ?: '#ffffff' ) : 'var(--olo-color-primary-contrast, #ffffff)' );
        $ombra    = Olobuild_Tile_Utils::shadow_value( $s, 'ombra' );
        $r_media  = Olobuild_Tile_Utils::border_radius( $s['raggio_media'] );
        $r_btn    = Olobuild_Tile_Utils::border_radius( $s['raggio_pulsante'] );
        $prop     = preg_match( '#^\d{1,2}/\d{1,2}$#', (string) $s['proporzione'] ) ? (string) $s['proporzione'] : '4/5';
        $sopra    = 'sopra' === (string) $s['disposizione'];
        $mix      = static function ( $a, $p, $b ) {
            return 'color-mix(in srgb, ' . $a . ' ' . (int) $p . '%, ' . $b . ')';
        };

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS da valori filtrati (safe_color_css, interi, raggi e ombre dagli helper), markup con esc_*().
        echo '<style>';
        echo "{$sel}{--ogh-d:{$d}px;--ogh-g:{$ghiera};--ogh-s:{$sigle};--ogh-i:{$indice};container-type:inline-size;container-name:olo-ghiera;display:grid;grid-template-columns:min(var(--ogh-d),100%) minmax(0,1fr);gap:{$gap}px;align-items:center}";
        if ( $sopra ) {
            echo "{$sel}{grid-template-columns:minmax(0,1fr);justify-items:center}";
        }
        echo "{$sel} .ogh-ruota-wrap{position:relative;width:min(var(--ogh-d),100%);aspect-ratio:1;padding-top:0;justify-self:center}";
        // indice fisso in alto
        echo "{$sel} .ogh-indice{position:absolute;left:50%;top:-14px;width:0;height:0;transform:translateX(-50%);border:8px solid transparent;border-top:12px solid var(--ogh-i);border-bottom:0;z-index:3}";
        echo "{$sel} .ogh-indice::after{content:'';position:absolute;left:-1px;top:-2px;width:2px;height:26px;background:var(--ogh-i);transform:translateY(0)}";
        // il disco
        echo "{$sel} .ogh-ruota{position:absolute;inset:0;border-radius:50%;touch-action:none;cursor:grab;outline:none;-webkit-tap-highlight-color:transparent;user-select:none;-webkit-user-select:none}";
        echo "{$sel} .ogh-ruota.is-presa{cursor:grabbing}";
        echo "{$sel} .ogh-ruota:focus-visible{box-shadow:0 0 0 3px var(--olo-color-background,#fff),0 0 0 5px var(--ogh-i)}";
        $faccia = $metallo
            ? 'radial-gradient(circle at 35% 28%,' . $mix( 'var(--ogh-g)', 70, '#ffffff' ) . ' 0%,var(--ogh-g) 42%,' . $mix( 'var(--ogh-g)', 82, '#000000' ) . ' 100%)'
            : 'var(--ogh-g)';
        $ombra_disco = $ombra ? $ombra : ( $metallo ? '0 18px 44px rgba(0,0,0,.28),0 2px 6px rgba(0,0,0,.2)' : 'none' );
        echo "{$sel} .ogh-disco{position:absolute;inset:0;border-radius:50%;background:{$faccia};box-shadow:{$ombra_disco};transform:rotate(var(--ogh-rot,0deg));transition:transform .55s cubic-bezier(.2,.9,.25,1.12)}";
        echo "{$sel} .ogh-ruota.is-presa .ogh-disco{transition:none}";
        $z1 = $metallo ? $mix( 'var(--ogh-g)', 55, '#ffffff' ) : $mix( 'var(--ogh-g)', 80, '#ffffff' );
        $z2 = $mix( 'var(--ogh-g)', 70, '#000000' );
        $anello = 'radial-gradient(circle closest-side,transparent calc(100% - 15px),#000 calc(100% - 14px))';
        echo "{$sel} .ogh-zigrino{position:absolute;inset:0;border-radius:50%;background:repeating-conic-gradient(from 0deg,{$z1} 0deg 1.4deg,{$z2} 1.4deg 2.8deg);-webkit-mask:{$anello};mask:{$anello}}";
        $tacche = 'radial-gradient(circle closest-side,transparent calc(100% - 34px),#000 calc(100% - 33px),#000 calc(100% - 25px),transparent calc(100% - 24px))';
        $mezzo  = round( $passo / 2, 3 );
        $c_tac  = $mix( 'var(--ogh-s)', 55, 'transparent' );
        echo "{$sel} .ogh-tacche{position:absolute;inset:0;border-radius:50%;background:repeating-conic-gradient(from " . round( $mezzo - 0.6, 3 ) . "deg,{$c_tac} 0deg 1.2deg,transparent 1.2deg " . round( $passo, 3 ) . "deg);-webkit-mask:{$tacche};mask:{$tacche}}";
        // ogni sigla sta in un livello grande quanto il disco, ruotato del suo angolo: in alto al centro di quel
        // livello è sul raggio giusto a qualunque misura della ghiera
        echo "{$sel} .ogh-pos{position:absolute;inset:0;transform:rotate(var(--a));pointer-events:none}";
        echo "{$sel} .ogh-sigla{position:absolute;left:50%;top:36px;width:52px;height:52px;transform:translateX(-50%);padding:0;border:0;border-radius:50%;background:transparent;color:var(--ogh-s);font:inherit;font-size:18px;font-weight:600;letter-spacing:.04em;line-height:52px;text-align:center;cursor:pointer;pointer-events:auto;transition:color .3s,opacity .3s;opacity:.7}";
        echo "{$sel} .ogh-sigla.is-su{color:var(--ogh-i);opacity:1}";
        echo "{$sel} .ogh-sigla:hover{opacity:1}";
        // il cappello al centro, fermo
        $cap = $metallo
            ? 'radial-gradient(circle at 40% 30%,' . $mix( 'var(--ogh-g)', 82, '#ffffff' ) . ',' . $mix( 'var(--ogh-g)', 88, '#000000' ) . ')'
            : $mix( 'var(--ogh-g)', 88, '#000000' );
        echo "{$sel} .ogh-centro{position:absolute;left:50%;top:50%;width:38%;aspect-ratio:1;transform:translate(-50%,-50%);border-radius:50%;background:{$cap};box-shadow:inset 0 2px 6px rgba(0,0,0,.35),0 1px 0 rgba(255,255,255,.08);display:flex;flex-direction:column;align-items:center;justify-content:center;color:var(--ogh-s);pointer-events:none;text-align:center}";
        echo "{$sel} .ogh-num{font-size:clamp(24px,calc(var(--ogh-d) * .1),46px);line-height:1;font-weight:600;font-variant-numeric:tabular-nums}";
        echo "{$sel} .ogh-num small{font-size:.42em;opacity:.6;margin-left:.15em;font-weight:400}";
        echo "{$sel} .ogh-centro-t{margin-top:.5em;font-size:10px;letter-spacing:.24em;text-transform:uppercase;opacity:.7}";
        // il pannello della voce
        echo "{$sel} .ogh-pannello{display:grid;grid-template-columns:minmax(0,1.05fr) minmax(0,1fr);gap:clamp(1.25rem,3vw,2.75rem);align-items:center;min-width:0" . ( $pannello ? ";background:{$pannello};padding:clamp(1.1rem,2.5vw,2rem);border-radius:{$r_media}" : '' ) . '}';
        echo "{$sel} .ogh-pile{display:grid;min-width:0}{$sel} .ogh-pile > *{grid-area:1/1;min-width:0}";
        // dopo la regola del pannello, se no a parità di peso vincerebbe lei: colonna stretta = foto sopra il testo
        echo "@container olo-ghiera (max-width:640px){{$sel} .ogh-pannello{grid-template-columns:minmax(0,1fr)}}";
        echo "{$sel} .ogh-media{position:relative;aspect-ratio:{$prop};overflow:hidden;border-radius:{$r_media};background:" . $mix( 'currentColor', 8, 'transparent' ) . ';opacity:0;transform:scale(1.04);transition:opacity .5s ease,transform .9s cubic-bezier(.2,.8,.2,1),visibility 0s .5s;visibility:hidden}';
        echo "{$sel} .ogh-media img,{$sel} .ogh-media video{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:block}";
        echo "{$sel} .ogh-testo{opacity:0;transform:translateY(14px);transition:opacity .4s ease,transform .6s cubic-bezier(.2,.8,.2,1),visibility 0s .4s;visibility:hidden}";
        echo "{$sel} .is-attiva{opacity:1;transform:none;visibility:visible;transition-delay:0s}";
        echo "{$sel} .ogh-testo.is-attiva{transition-delay:.08s}";
        echo "{$sel} .ogh-sigla-g{display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:34px;padding:0 .5rem;box-sizing:border-box;border:1.5px solid var(--ogh-i);color:var(--ogh-i);border-radius:99em;font-size:13px;font-weight:700;letter-spacing:.06em;margin-bottom:1.1rem}";
        echo "{$sel} .ogh-titolo{margin:0 0 .75rem;font-size:40px;line-height:1.1}";
        echo "{$sel} .ogh-desc{margin:0 0 1.1rem}{$sel} .ogh-desc p{margin:0 0 .6em}";
        echo "{$sel} .ogh-prezzo{display:block;margin-bottom:1.4rem;font-weight:600}";
        echo "{$sel} .ogh-link{display:inline-flex;align-items:center;gap:.6rem;padding:.8rem 1.5rem;background:{$btn};color:{$btn_t};border-radius:{$r_btn};font-weight:600;font-size:14px;text-decoration:none;transition:transform .2s,filter .2s}";
        echo "{$sel} .ogh-link:hover{transform:translateY(-2px);filter:brightness(1.06);color:{$btn_t}}";
        echo "{$sel} .ogh-link:focus-visible{outline:2px solid {$btn};outline-offset:3px}";
        echo "{$sel} .ogh-link svg{width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:2}";
        echo "{$sel} .ogh-sr{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap}";
        echo '@media (max-width:959px){' . "{$sel}{grid-template-columns:minmax(0,1fr);justify-items:stretch}{$sel} .ogh-ruota-wrap{width:min(var(--ogh-d),88vw)}" . '}';
        echo "@media (prefers-reduced-motion:reduce){{$sel} .ogh-disco,{$sel} .ogh-media,{$sel} .ogh-testo{transition:none}}";
        $fam_sig = $this->resolve_font_family( $s['sigla_font_family'] );
        $fs_sig  = (float) $s['sigla_font_size'];
        $fw_sig  = $this->font_weight_css( $s['sigla_font_weight'] );
        if ( '' !== $fam_sig || $fs_sig > 0 || $fw_sig ) {
            echo "{$sel} .ogh-sigla,{$sel} .ogh-num{" . ( '' !== $fam_sig ? 'font-family:' . $fam_sig . ';' : '' ) . ( $fw_sig ? 'font-weight:' . $fw_sig . ';' : '' ) . '}';
            if ( $fs_sig > 0 ) {
                echo "{$sel} .ogh-sigla{font-size:" . min( 36, $fs_sig ) . 'px}';
            }
        }
        echo $this->css_testo( $s, 'titolo', "{$sel} .ogh-titolo" );
        echo $this->css_testo( $s, 'testo', "{$sel} .ogh-desc" );
        echo $this->css_testo( $s, 'prezzo', "{$sel} .ogh-prezzo" );
        echo '</style>';

        $etichetta = trim( wp_strip_all_tags( (string) $s['etichetta'] ) );
        $etichetta = '' !== $etichetta ? $etichetta : __( 'Scegli il servizio', 'olobuilder' );
        $cfg       = [ 'n' => $n, 'i' => $partenza, 'passo' => $passo, 'titoli' => array_column( $voci, 'titolo' ) ];
        $tot       = str_pad( (string) $n, 2, '0', STR_PAD_LEFT );
        $freccia   = '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';

        echo '<div id="' . esc_attr( $uid ) . '" class="olo-ghiera" data-ogh="' . esc_attr( wp_json_encode( $cfg ) ) . '">';
        echo '<div class="ogh-ruota-wrap">';
        echo '<span class="ogh-indice" aria-hidden="true"></span>';
        echo '<div class="ogh-ruota" role="slider" tabindex="0" aria-label="' . esc_attr( $etichetta ) . '" aria-valuemin="1" aria-valuemax="' . (int) $n . '" aria-valuenow="' . ( $partenza + 1 ) . '" aria-valuetext="' . esc_attr( $voci[ $partenza ]['titolo'] ) . '" aria-describedby="' . esc_attr( $uid ) . '-aiuto" data-olo-interactive data-olo-own-drag>';
        echo '<div class="ogh-disco" style="--ogh-rot:' . round( -$partenza * $passo, 3 ) . 'deg"><span class="ogh-zigrino"></span><span class="ogh-tacche"></span>';
        foreach ( $voci as $i => $v ) {
            echo '<span class="ogh-pos" style="--a:' . round( $i * $passo, 3 ) . 'deg"><button type="button" class="ogh-sigla' . ( $i === $partenza ? ' is-su' : '' ) . '" tabindex="-1" aria-hidden="true" data-i="' . (int) $i . '" title="' . esc_attr( $v['titolo'] ) . '">' . esc_html( $v['sigla'] ) . '</button></span>';
        }
        echo '</div>';
        echo '<div class="ogh-centro" aria-hidden="true">';
        if ( ! empty( $s['mostra_numero'] ) ) {
            echo '<span class="ogh-num"><b class="ogh-n">' . esc_html( str_pad( (string) ( $partenza + 1 ), 2, '0', STR_PAD_LEFT ) ) . '</b><small>/' . esc_html( $tot ) . '</small></span>';
        }
        $tc = trim( wp_strip_all_tags( (string) $s['titolo_centro'] ) );
        if ( '' !== $tc ) {
            echo '<span class="ogh-centro-t">' . esc_html( $tc ) . '</span>';
        }
        echo '</div></div></div>';
        echo '<p class="ogh-sr" id="' . esc_attr( $uid ) . '-aiuto">' . esc_html__( 'Frecce per girare la ghiera, Inizio e Fine per la prima e l\'ultima voce. Si può anche trascinare.', 'olobuilder' ) . '</p>';

        echo '<div class="ogh-pannello">';
        echo '<div class="ogh-pile ogh-pile-media">';
        foreach ( $voci as $i => $v ) {
            $att = $i === $partenza ? ' is-attiva' : '';
            echo '<div class="ogh-media' . $att . '" data-i="' . (int) $i . '">';
            if ( '' !== $v['video'] ) {
                echo '<video muted loop playsinline preload="' . ( $i === $partenza ? 'auto' : 'none' ) . '"' . ( '' !== $v['img'] ? ' poster="' . esc_url( $v['img'] ) . '"' : '' ) . ' src="' . esc_url( $v['video'] ) . '"' . ( $i === $partenza ? ' autoplay' : '' ) . '></video>';
            } elseif ( '' !== $v['img'] ) {
                echo '<img src="' . esc_url( $v['img'] ) . '" alt="' . esc_attr( $v['titolo'] ) . '" loading="lazy" decoding="async">';
            }
            echo '</div>';
        }
        echo '</div>';
        echo '<div class="ogh-pile ogh-pile-testo">';
        foreach ( $voci as $i => $v ) {
            $att = $i === $partenza ? ' is-attiva' : '';
            echo '<div class="ogh-testo' . $att . '" data-i="' . (int) $i . '"' . ( $i === $partenza ? '' : ' aria-hidden="true"' ) . '>';
            echo '<span class="ogh-sigla-g" aria-hidden="true">' . esc_html( $v['sigla'] ) . '</span>';
            if ( '' !== $v['titolo'] ) {
                echo '<h3 class="ogh-titolo">' . esc_html( $v['titolo'] ) . '</h3>';
            }
            if ( '' !== trim( wp_strip_all_tags( $v['testo'] ) ) ) {
                echo '<div class="ogh-desc">' . wpautop( wp_kses( $v['testo'], [ 'strong' => [], 'em' => [], 'b' => [], 'i' => [], 'br' => [], 'p' => [] ] ) ) . '</div>';
            }
            if ( '' !== $v['prezzo'] ) {
                echo '<span class="ogh-prezzo">' . esc_html( $v['prezzo'] ) . '</span>';
            }
            if ( '' !== $v['link'] ) {
                $tl = '' !== $v['tlink'] ? $v['tlink'] : __( 'Scopri', 'olobuilder' );
                echo '<a class="ogh-link" href="' . esc_url( $v['link'] ) . '"' . ( $i === $partenza ? '' : ' tabindex="-1"' ) . '>' . esc_html( $tl ) . $freccia . '</a>';
            }
            echo '</div>';
        }
        echo '</div>';
        echo '<span class="ogh-sr ogh-eco" aria-live="polite"></span>';
        echo '</div></div>';

        echo '<script>' . $this->script() . '[].forEach.call(document.querySelectorAll("[data-ogh]"),function(x){window.oloGhiera(x);});</script>';
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }

    /**
     * Il disco, il trascinamento e il cambio di voce. Definito una volta (if(!window.oloGhiera)), ogni istanza si avvia
     * una volta sola (data-ogh-ok). Niente «&» (wptexturize): condizioni annidate.
     */
    private function script() {
        return <<<'JS'
if(!window.oloGhiera){window.oloGhiera=function(el){
if(!el){return;}
if(el.getAttribute("data-ogh-ok")==="1"){return;}
el.setAttribute("data-ogh-ok","1");
var C={};try{C=JSON.parse(el.getAttribute("data-ogh")||"{}");}catch(e){}
var n=C.n||0,P=C.passo||45;if(n<2){return;}
var ruota=el.querySelector(".ogh-ruota"),disco=el.querySelector(".ogh-disco"),sig=el.querySelectorAll(".ogh-sigla");
var med=el.querySelectorAll(".ogh-media"),tes=el.querySelectorAll(".ogh-testo"),num=el.querySelector(".ogh-n"),eco=el.querySelector(".ogh-eco");
var rid=false;try{rid=window.matchMedia("(prefers-reduced-motion: reduce)").matches;}catch(e){}
var i=C.i||0,rot=-i*P;
function mod(a){return((a%n)+n)%n;}
function giri(r){disco.style.setProperty("--ogh-rot",r.toFixed(3)+"deg");}
function mostra(k,annuncia){k=mod(k);if(k===i){if(!annuncia){return;}}
var prima=i;i=k;
for(var a=0;a<sig.length;a++){sig[a].classList.toggle("is-su",a===i);}
for(var b=0;b<med.length;b++){var on=(b===i);med[b].classList.toggle("is-attiva",on);var v=med[b].querySelector("video");
if(v){if(on){v.preload="auto";try{var pr=v.play();if(pr){if(pr.catch){pr.catch(function(){});}}}catch(e){}}else{try{v.pause();}catch(e){}}}}
for(var c=0;c<tes.length;c++){var t=(c===i);tes[c].classList.toggle("is-attiva",t);if(t){tes[c].removeAttribute("aria-hidden");}else{tes[c].setAttribute("aria-hidden","true");}
var l=tes[c].querySelector(".ogh-link");if(l){if(t){l.removeAttribute("tabindex");}else{l.setAttribute("tabindex","-1");}}}
if(num){num.textContent=(i+1<10?"0":"")+(i+1);}
ruota.setAttribute("aria-valuenow",String(i+1));ruota.setAttribute("aria-valuetext",(C.titoli||[])[i]||"");
if(annuncia){if(eco){eco.textContent=(C.titoli||[])[i]||"";}}
if(prima!==i){try{if(navigator.vibrate){navigator.vibrate(6);}}catch(e){}}}
function vai(k,annuncia){var dest=-k*P,d=dest-rot;d=((d%360)+540)%360-180;rot=rot+d;giri(rot);mostra(k,annuncia);}
function angolo(e){var r=ruota.getBoundingClientRect(),cx=r.left+r.width/2,cy=r.top+r.height/2;return Math.atan2(e.clientY-cy,e.clientX-cx)*180/Math.PI;}
var presa=false,a0=0,r0=0,mosso=0,pid=null,giu=null;
ruota.addEventListener("pointerdown",function(e){if(e.button){if(e.button!==0){return;}}
presa=true;mosso=0;pid=e.pointerId;giu=e.target;a0=angolo(e);r0=rot;ruota.classList.add("is-presa");try{ruota.setPointerCapture(pid);}catch(x){}});
ruota.addEventListener("pointermove",function(e){if(!presa){return;}var a=angolo(e),d=a-a0;d=((d%360)+540)%360-180;a0=a;r0+=d;mosso+=Math.abs(d);rot=r0;giri(rot);mostra(Math.round(-rot/P),false);});
function lascia(e){if(!presa){return;}presa=false;ruota.classList.remove("is-presa");try{ruota.releasePointerCapture(pid);}catch(x){}
if(mosso<3){var t=giu;if(t){if(t.classList){if(t.classList.contains("ogh-sigla")){vai(parseInt(t.getAttribute("data-i"),10)||0,true);return;}}}}
var k=Math.round(-rot/P);rot=-k*P;giri(rot);mostra(k,true);}
ruota.addEventListener("pointerup",lascia);ruota.addEventListener("pointercancel",lascia);
ruota.addEventListener("keydown",function(e){var k=e.key,d=0;
if(k==="ArrowRight"){d=1;}else if(k==="ArrowDown"){d=1;}else if(k==="ArrowLeft"){d=-1;}else if(k==="ArrowUp"){d=-1;}
else if(k==="PageDown"){d=1;}else if(k==="PageUp"){d=-1;}
else if(k==="Home"){e.preventDefault();vai(0,true);return;}else if(k==="End"){e.preventDefault();vai(n-1,true);return;}
if(d){e.preventDefault();vai(i+d,true);}});
var arrivato=false,tRot=0;
ruota.addEventListener("pointerenter",function(){arrivato=false;});
ruota.addEventListener("mousemove",function(){arrivato=true;});
ruota.addEventListener("wheel",function(e){if(!arrivato){return;}e.preventDefault();var now=Date.now();if(now-tRot<160){return;}tRot=now;vai(i+(e.deltaY>0?1:-1),true);},{passive:false});
if(rid){disco.style.transition="none";}
giri(rot);
};}
JS;
    }
}
