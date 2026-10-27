<?php
/**
 * Tile Montaggio — una timeline di montaggio vera: il monitor col video, la barra col play e il timecode
 * (ore:minuti:secondi:fotogrammi), il righello, la traccia video con le clip in fila (lunghe quanto durano, coi
 * fotogrammi della copertina in striscia) e la traccia audio con la forma d'onda. La testina rossa scorre mentre i
 * video vanno uno dopo l'altro; si trascina (dito o mouse, sul righello o sulle tracce), un clic su una clip salta al
 * suo inizio, e accanto al monitor cambiano il tipo, il titolo, il cliente e il pulsante della clip in onda.
 *
 * Tastiera, come in un programma di montaggio: la timeline è uno slider; frecce un fotogramma (con Maiuscolo un
 * secondo), Inizio e Fine, Spazio o K ferma e riparte, L avanti, J indietro di cinque secondi. Parte da sola quando
 * entra nello schermo (senza audio) e si ferma quando esce; con «riduci movimento» non parte da sola. Il tempo segue
 * il video in onda (niente deriva); finita una clip si passa alla seguente, finita l'ultima si ricomincia.
 * Nata per il tema di una casa di produzione video (27 ott 2026). Config: src/config/elements/montaggio.js.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Montaggio_Tile extends Olobuild_Tile_Base {

    protected $type     = 'montaggio';
    protected $name     = 'Montaggio';
    protected $icon     = 'dashicons-video-alt2';
    protected $category = 'media';

    protected $defaults = [
        'clip'                => [
            [ 'titolo' => 'Bianco', 'cliente' => 'Atelier Neve', 'anno' => '2026', 'tipo' => 'Fashion film', 'video' => '', 'immagine' => '', 'durata' => 8, 'link' => '', 'testo_link' => 'Guarda il film', 'colore' => '' ],
            [ 'titolo' => 'Strada bianca', 'cliente' => 'Auto Ferri', 'anno' => '2026', 'tipo' => 'Spot', 'video' => '', 'immagine' => '', 'durata' => 6, 'link' => '', 'testo_link' => 'Guarda lo spot', 'colore' => '' ],
            [ 'titolo' => 'Rosso', 'cliente' => 'I Fari', 'anno' => '2025', 'tipo' => 'Videoclip', 'video' => '', 'immagine' => '', 'durata' => 7, 'link' => '', 'testo_link' => 'Guarda il video', 'colore' => '' ],
        ],
        'nome_sequenza'       => 'SHOWREEL_2026_v12',
        'fps'                 => 24,
        'parti_da_sola'       => true,
        'mostra_audio'        => true,
        'mostra_info'         => true,
        // stile
        'colore_fondo'        => '',
        'colore_testo'        => '',
        'colore_testina'      => '',
        'colore_clip'         => '',
        'colore_audio'        => '',
        'titolo_font_family'  => '',
        'titolo_font_size'    => 34,
        'titolo_font_size_mobile' => 24,
        'titolo_font_weight'  => '',
        'titolo_color'        => '',
        'dati_font_family'    => '',
        'raggio'              => [ 'tl' => 10, 'tr' => 10, 'br' => 10, 'bl' => 10 ],
        'proporzione'         => '16/9',
        'altezza_traccia'     => 56,
        'posizione_info'      => 'lato',
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

    public function render( $settings ) {
        $s       = wp_parse_args( $settings, $this->defaults );
        $uid     = 'omt-' . $this->chiave_stabile( $settings );
        $base    = $uid;
        $copia_n = 2;
        while ( isset( self::$uid_usate[ $uid ] ) ) {
            $uid = $base . '-' . $copia_n++;
        }
        self::$uid_usate[ $uid ] = true;
        $sel     = '#' . $uid;
        $builder = ! empty( $s['_builder_mode'] );

        $clip = [];
        foreach ( ( is_array( $s['clip'] ) ? $s['clip'] : [] ) as $c ) {
            if ( ! is_array( $c ) ) {
                continue;
            }
            $video = esc_url_raw( self::url_di( $c['video'] ?? '' ) );
            $img   = esc_url_raw( self::url_di( $c['immagine'] ?? '' ) );
            if ( '' === $video && '' === $img ) {
                continue;
            }
            $clip[] = [
                'titolo'  => trim( wp_strip_all_tags( (string) ( $c['titolo'] ?? '' ) ) ),
                'cliente' => trim( wp_strip_all_tags( (string) ( $c['cliente'] ?? '' ) ) ),
                'anno'    => trim( wp_strip_all_tags( (string) ( $c['anno'] ?? '' ) ) ),
                'tipo'    => trim( wp_strip_all_tags( (string) ( $c['tipo'] ?? '' ) ) ),
                'video'   => $video,
                'img'     => $img,
                'durata'  => max( 1, min( 600, (float) ( $c['durata'] ?? 8 ) ) ),
                'link'    => esc_url_raw( self::url_di( $c['link'] ?? '' ) ),
                'tlink'   => trim( wp_strip_all_tags( (string) ( $c['testo_link'] ?? '' ) ) ),
                'colore'  => $this->safe_color_css( $c['colore'] ?? '' ),
            ];
        }
        if ( ! $clip ) {
            if ( ! $builder ) {
                return '';
            }
            return '<div style="padding:1.5rem;border:1px dashed currentColor;opacity:.7;font-size:14px">' . esc_html__( 'Aggiungi almeno una clip con un video o una foto nel Contenuto.', 'olobuilder' ) . '</div>';
        }

        $fps     = max( 12, min( 60, (int) $s['fps'] ) );
        $totale  = array_sum( array_column( $clip, 'durata' ) );
        $fondo   = $this->safe_color_css( $s['colore_fondo'] ) ?: 'var(--olo-color-dark, #111111)';
        $testo   = $this->safe_color_css( $s['colore_testo'] ) ?: ( Olobuild_Tile_Utils::testo_su_fondo( $fondo, 'var(--olo-color-light, #f5f5f5)' ) ?: 'var(--olo-color-light, #f5f5f5)' );
        $testina = $this->safe_color_css( $s['colore_testina'] ) ?: 'var(--olo-color-primary)';
        $c_clip  = $this->safe_color_css( $s['colore_clip'] ) ?: 'var(--olo-color-secondary, #4b5563)';
        $c_audio = $this->safe_color_css( $s['colore_audio'] ) ?: 'color-mix(in srgb, ' . $testina . ' 70%, ' . $testo . ')';
        $raggio  = Olobuild_Tile_Utils::border_radius( $s['raggio'] ) ?: '0';
        $prop    = preg_match( '#^\d{1,2}/\d{1,2}$#', (string) $s['proporzione'] ) ? (string) $s['proporzione'] : '16/9';
        $h_tr    = max( 36, min( 120, (int) $s['altezza_traccia'] ) );
        $lato    = 'sotto' !== (string) $s['posizione_info'];
        $info    = ! empty( $s['mostra_info'] );
        $audio   = ! empty( $s['mostra_audio'] );
        $mono    = $this->resolve_font_family( $s['dati_font_family'] ) ?: 'var(--olo-font-family-mono, ui-monospace, SFMono-Regular, Menlo, monospace)';

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS da valori filtrati (safe_color_css, interi, raggi), markup con esc_*().
        echo '<style>';
        echo "{$sel}{--omt-f:{$fondo};--omt-t:{$testo};--omt-p:{$testina};--omt-c:{$c_clip};--omt-a:{$c_audio};--omt-h:{$h_tr}px;container-type:inline-size;container-name:olo-montaggio;background:var(--omt-f);color:var(--omt-t);border-radius:{$raggio};padding:clamp(.75rem,2vw,1.25rem);box-sizing:border-box;overflow:hidden}";
        echo "{$sel} .omt-alto{display:grid;grid-template-columns:" . ( $info && $lato ? 'minmax(0,2.2fr) minmax(0,1fr)' : 'minmax(0,1fr)' ) . ";gap:clamp(.75rem,2vw,1.5rem);align-items:end}";
        echo "@container olo-montaggio (max-width:760px){{$sel} .omt-alto{grid-template-columns:minmax(0,1fr)}}";
        echo "{$sel} .omt-monitor{position:relative;aspect-ratio:{$prop};background:#000;overflow:hidden;border-radius:calc({$raggio} * .6)}";
        echo "{$sel} .omt-monitor video,{$sel} .omt-monitor img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:0;transition:opacity .25s}";
        echo "{$sel} .omt-monitor .is-su{opacity:1}";
        echo "{$sel} .omt-rec{position:absolute;left:.75rem;top:.6rem;font:600 11px/1 {$mono};letter-spacing:.12em;color:#fff;text-shadow:0 1px 3px rgba(0,0,0,.6);display:flex;gap:.5em;align-items:center}";
        echo "{$sel} .omt-rec i{width:7px;height:7px;border-radius:50%;background:var(--omt-p);display:inline-block}";
        echo "{$sel}.is-play .omt-rec i{animation:omt-lampeggia 1s steps(2,jump-none) infinite}";
        echo '@keyframes omt-lampeggia{50%{opacity:.15}}';
        echo "{$sel} .omt-info{position:relative;display:grid;min-height:8rem}";
        echo "{$sel} .omt-voce{grid-area:1/1;opacity:0;transform:translateY(8px);transition:opacity .35s,transform .45s cubic-bezier(.2,.8,.2,1),visibility 0s .35s;visibility:hidden;align-self:end}";
        echo "{$sel} .omt-voce.is-su{opacity:1;transform:none;visibility:visible;transition-delay:0s}";
        echo "{$sel} .omt-tipo{font:500 11px/1.2 {$mono};letter-spacing:.14em;text-transform:uppercase;color:var(--omt-p);margin:0 0 .6rem}";
        echo "{$sel} .omt-titolo{margin:0 0 .4rem;font-size:34px;line-height:1.05;color:inherit}";
        echo "{$sel} .omt-chi{margin:0 0 1rem;font:400 12px/1.4 {$mono};letter-spacing:.06em;opacity:.7}";
        echo "{$sel} .omt-link{display:inline-flex;gap:.5rem;align-items:center;color:inherit;text-decoration:none;font-size:13px;font-weight:600;border-bottom:1px solid color-mix(in srgb,currentColor 40%,transparent);padding-bottom:.2rem}";
        echo "{$sel} .omt-link:hover{border-color:var(--omt-p)}";
        echo "{$sel} .omt-link:focus-visible{outline:2px solid var(--omt-p);outline-offset:3px}";
        // barra dei comandi
        echo "{$sel} .omt-barra{display:flex;align-items:center;gap:1rem;margin:1rem 0 .6rem;font:500 12px/1 {$mono};letter-spacing:.06em}";
        echo "{$sel} .omt-play{width:38px;height:38px;flex:none;border-radius:50%;border:1px solid color-mix(in srgb,currentColor 35%,transparent);background:transparent;color:inherit;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;padding:0}";
        echo "{$sel} .omt-play:hover{border-color:var(--omt-p);color:var(--omt-p)}";
        echo "{$sel} .omt-play:focus-visible{outline:2px solid var(--omt-p);outline-offset:3px}";
        echo "{$sel} .omt-play svg{width:14px;height:14px;fill:currentColor}";
        echo "{$sel} .omt-play .omt-pausa,{$sel}.is-play .omt-play .omt-avvia{display:none}{$sel}.is-play .omt-play .omt-pausa{display:block}";
        echo "{$sel} .omt-tc{font-size:15px;font-variant-numeric:tabular-nums;color:var(--omt-p)}";
        echo "{$sel} .omt-tot{opacity:.5;font-variant-numeric:tabular-nums}";
        echo "{$sel} .omt-seq{margin-left:auto;opacity:.5;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}";
        // la timeline
        echo "{$sel} .omt-tl{position:relative;display:grid;grid-template-columns:2.4rem minmax(0,1fr);row-gap:.25rem;user-select:none;-webkit-user-select:none;touch-action:pan-y;cursor:ew-resize;outline:none}";
        echo "{$sel} .omt-tl:focus-visible{box-shadow:0 0 0 2px var(--omt-p)}";
        echo "{$sel} .omt-et{font:600 10px/1 {$mono};letter-spacing:.1em;opacity:.55;display:flex;align-items:center}";
        echo "{$sel} .omt-righello{position:relative;height:22px;background:repeating-linear-gradient(90deg,color-mix(in srgb,currentColor 35%,transparent) 0 1px,transparent 1px var(--omt-passo)) 0 100%/100% 6px no-repeat}";
        echo "{$sel} .omt-righello span{position:absolute;top:0;font:500 9px/1 {$mono};opacity:.6;transform:translateX(3px);white-space:nowrap}";
        echo "{$sel} .omt-traccia{position:relative;height:var(--omt-h);background:color-mix(in srgb,currentColor 6%,transparent);border-radius:.25rem;overflow:hidden}";
        echo "{$sel} .omt-clip{position:absolute;top:2px;bottom:2px;border-radius:.25rem;overflow:hidden;background-color:var(--omt-c);background-size:auto 100%;background-repeat:repeat-x;box-shadow:inset 0 0 0 1px color-mix(in srgb,#fff 18%,transparent);border-left:3px solid var(--omt-k,var(--omt-p));box-sizing:border-box;cursor:pointer;transition:filter .2s}";
        echo "{$sel} .omt-clip::after{content:'';position:absolute;inset:0;background:linear-gradient(180deg,rgba(0,0,0,.55),rgba(0,0,0,.05) 60%)}";
        echo "{$sel} .omt-clip b{position:absolute;left:.45rem;top:.35rem;z-index:1;font:600 10px/1.2 {$mono};letter-spacing:.06em;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:calc(100% - .9rem)}";
        echo "{$sel} .omt-clip.is-su{filter:brightness(1.15);box-shadow:inset 0 0 0 1.5px var(--omt-p)}";
        echo "{$sel} .omt-onda{position:absolute;inset:4px 0;display:flex;align-items:center;gap:1px}";
        echo "{$sel} .omt-onda i{flex:1;min-width:1px;background:var(--omt-a);border-radius:1px;opacity:.75}";
        echo "{$sel} .omt-testina{position:absolute;top:0;bottom:0;left:calc(2.4rem + (100% - 2.4rem) * var(--omt-x,0));width:2px;margin-left:-1px;background:var(--omt-p);pointer-events:none;z-index:3}";
        echo "{$sel} .omt-testina::before{content:'';position:absolute;top:0;left:50%;width:.75rem;height:.75rem;margin-left:-.375rem;background:var(--omt-p);clip-path:polygon(0 0,100% 0,100% 55%,50% 100%,0 55%)}";
        echo "{$sel} .omt-sr{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap}";
        echo '@media (max-width:640px){' . "{$sel} .omt-seq{display:none}{$sel} .omt-clip b{font-size:9px}" . '}';
        echo "@media (prefers-reduced-motion:reduce){{$sel} .omt-voce,{$sel} .omt-monitor video,{$sel} .omt-monitor img{transition:none}{$sel}.is-play .omt-rec i{animation:none}}";
        $fam = $this->resolve_font_family( $s['titolo_font_family'] );
        $fs  = (float) $s['titolo_font_size'];
        $fw  = $this->font_weight_css( $s['titolo_font_weight'] );
        $col = $this->safe_color_css( $s['titolo_color'] );
        $d   = ( '' !== $fam ? 'font-family:' . $fam . ';' : '' ) . ( $fs > 0 ? 'font-size:' . $fs . 'px;' : '' ) . ( $fw ? 'font-weight:' . $fw . ';' : '' ) . ( $col ? 'color:' . $col . ';' : '' );
        if ( '' !== $d ) {
            echo "{$sel} .omt-titolo{{$d}}";
        }
        $fsm = (float) ( $s['titolo_font_size_mobile'] ?? 0 );
        if ( $fsm > 0 ) {
            echo '@media (max-width:480px){' . "{$sel} .omt-titolo{font-size:{$fsm}px}" . '}';
        }
        echo '</style>';

        // il righello: un segno ogni secondo (ogni 5 se la sequenza è lunga), un numero ogni 5 o 10
        $passo_s = $totale > 90 ? 5 : 1;
        $etich_s = $totale > 90 ? 15 : ( $totale > 40 ? 10 : 5 );
        $cfg = [
            'fps'    => $fps,
            'auto'   => ! empty( $s['parti_da_sola'] ) && ! $builder,
            'durate' => array_column( $clip, 'durata' ),
            'titoli' => array_column( $clip, 'titolo' ),
        ];
        $tc = function ( $t ) use ( $fps ) {
            $f = (int) floor( $t * $fps + 0.0001 );
            return sprintf( '%02d:%02d:%02d:%02d', floor( $f / ( 3600 * $fps ) ), floor( $f / ( 60 * $fps ) ) % 60, floor( $f / $fps ) % 60, $f % $fps );
        };

        echo '<div id="' . esc_attr( $uid ) . '" class="olo-montaggio" data-omt="' . esc_attr( wp_json_encode( $cfg ) ) . '">';
        echo '<div class="omt-alto">';
        echo '<div class="omt-monitor">';
        foreach ( $clip as $i => $c ) {
            $su = 0 === $i ? ' is-su' : '';
            if ( '' !== $c['video'] ) {
                echo '<video class="omt-v' . $su . '" data-i="' . (int) $i . '" muted playsinline preload="' . ( 0 === $i ? 'auto' : 'none' ) . '"' . ( '' !== $c['img'] ? ' poster="' . esc_url( $c['img'] ) . '"' : '' ) . ' src="' . esc_url( $c['video'] ) . '" aria-hidden="true"></video>';
            } else {
                echo '<img class="omt-v' . $su . '" data-i="' . (int) $i . '" src="' . esc_url( $c['img'] ) . '" alt="" loading="lazy" decoding="async">';
            }
        }
        echo '<span class="omt-rec" aria-hidden="true"><i></i>PROGRAM</span>';
        echo '</div>';
        if ( $info ) {
            echo '<div class="omt-info" aria-live="polite">';
            foreach ( $clip as $i => $c ) {
                echo '<div class="omt-voce' . ( 0 === $i ? ' is-su' : '' ) . '" data-i="' . (int) $i . '"' . ( 0 === $i ? '' : ' aria-hidden="true"' ) . '>';
                if ( '' !== $c['tipo'] ) {
                    echo '<p class="omt-tipo">' . esc_html( sprintf( '%02d', $i + 1 ) . ' · ' . $c['tipo'] ) . '</p>';
                }
                if ( '' !== $c['titolo'] ) {
                    echo '<h3 class="omt-titolo">' . esc_html( $c['titolo'] ) . '</h3>';
                }
                $chi = trim( $c['cliente'] . ( '' !== $c['anno'] ? ' · ' . $c['anno'] : '' ), ' ·' );
                if ( '' !== $chi ) {
                    echo '<p class="omt-chi">' . esc_html( $chi ) . '</p>';
                }
                if ( '' !== $c['link'] ) {
                    echo '<a class="omt-link" href="' . esc_url( $c['link'] ) . '"' . ( 0 === $i ? '' : ' tabindex="-1"' ) . '>' . esc_html( '' !== $c['tlink'] ? $c['tlink'] : __( 'Guarda il progetto', 'olobuilder' ) ) . ' <span aria-hidden="true">→</span></a>';
                }
                echo '</div>';
            }
            echo '</div>';
        }
        echo '</div>';

        echo '<div class="omt-barra">';
        echo '<button type="button" class="omt-play" aria-label="' . esc_attr__( 'Riproduci o ferma', 'olobuilder' ) . '" data-olo-interactive>'
            . '<svg class="omt-avvia" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path d="M4 2.5v11l9-5.5z"/></svg>'
            . '<svg class="omt-pausa" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path d="M3.5 2.5h3v11h-3zM9.5 2.5h3v11h-3z"/></svg></button>';
        echo '<span class="omt-tc">' . esc_html( $tc( 0 ) ) . '</span><span class="omt-tot">/ ' . esc_html( $tc( $totale ) ) . '</span>';
        $seq = trim( wp_strip_all_tags( (string) $s['nome_sequenza'] ) );
        if ( '' !== $seq ) {
            echo '<span class="omt-seq">' . esc_html( $seq ) . ' · ' . (int) $fps . ' fps</span>';
        }
        echo '</div>';

        $passo_pct = round( 100 * $passo_s / $totale, 4 );
        echo '<div class="omt-tl" role="slider" tabindex="0" aria-label="' . esc_attr__( 'Timeline del montaggio', 'olobuilder' ) . '" aria-valuemin="0" aria-valuemax="' . esc_attr( round( $totale, 2 ) ) . '" aria-valuenow="0" aria-valuetext="' . esc_attr( $tc( 0 ) ) . '" aria-describedby="' . esc_attr( $uid ) . '-aiuto" data-olo-interactive data-olo-own-drag style="--omt-passo:' . $passo_pct . '%">';
        echo '<span class="omt-et"></span><div class="omt-righello" aria-hidden="true">';
        for ( $t = 0; $t < $totale; $t += $etich_s ) {
            echo '<span style="left:' . round( 100 * $t / $totale, 3 ) . '%">' . esc_html( substr( $tc( $t ), 3, 5 ) ) . '</span>';
        }
        echo '</div>';
        echo '<span class="omt-et" aria-hidden="true">V1</span><div class="omt-traccia">';
        $inizio = 0;
        foreach ( $clip as $i => $c ) {
            $st = 'left:' . round( 100 * $inizio / $totale, 4 ) . '%;width:' . round( 100 * $c['durata'] / $totale, 4 ) . '%;';
            if ( '' !== $c['img'] ) {
                $st .= 'background-image:url(' . esc_url( $c['img'] ) . ');';
            }
            if ( '' !== $c['colore'] ) {
                $st .= '--omt-k:' . $c['colore'] . ';';
            }
            echo '<span class="omt-clip' . ( 0 === $i ? ' is-su' : '' ) . '" data-i="' . (int) $i . '" style="' . esc_attr( $st ) . '" title="' . esc_attr( $c['titolo'] ) . '"><b>' . esc_html( $c['titolo'] ) . '</b></span>';
            $inizio += $c['durata'];
        }
        echo '</div>';
        if ( $audio ) {
            // la forma d'onda: barre fisse (pseudo-casuali ma uguali a ogni caricamento), più alte a metà di ogni clip
            echo '<span class="omt-et" aria-hidden="true">A1</span><div class="omt-traccia" aria-hidden="true"><span class="omt-onda">';
            $n_barre = 120;
            $seme    = 7;
            for ( $b = 0; $b < $n_barre; $b++ ) {
                $seme = ( $seme * 1103515245 + 12345 ) % 2147483648;
                $h    = 18 + ( $seme % 70 );
                echo '<i style="height:' . (int) $h . '%"></i>';
            }
            echo '</span></div>';
        }
        echo '<span class="omt-testina" aria-hidden="true"></span>';
        echo '</div>';
        echo '<p class="omt-sr" id="' . esc_attr( $uid ) . '-aiuto">' . esc_html__( 'Frecce per un fotogramma, con Maiuscolo un secondo; Spazio o K ferma e riparte, L avanti, J indietro di cinque secondi.', 'olobuilder' ) . '</p>';
        echo '</div>';
        echo '<script>' . $this->script() . '[].forEach.call(document.querySelectorAll("[data-omt]"),function(x){window.oloMontaggio(x);});</script>';
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }

    /** La testina, i video in fila, il trascinamento e i tasti. Niente «&» (wptexturize): condizioni annidate. */
    private function script() {
        return <<<'JS'
if(!window.oloMontaggio){window.oloMontaggio=function(el){
if(!el){return;}
if(el.getAttribute("data-omt-ok")==="1"){return;}
el.setAttribute("data-omt-ok","1");
var C={};try{C=JSON.parse(el.getAttribute("data-omt")||"{}");}catch(e){}
var D=C.durate||[],n=D.length;if(!n){return;}
var fps=C.fps||24,inizi=[],tot=0;for(var a=0;a<n;a++){inizi.push(tot);tot+=D[a];}
var tl=el.querySelector(".omt-tl"),tc=el.querySelector(".omt-tc"),play=el.querySelector(".omt-play");
var vv=el.querySelectorAll(".omt-v"),voci=el.querySelectorAll(".omt-voce"),clips=el.querySelectorAll(".omt-clip");
var rid=false;try{rid=window.matchMedia("(prefers-reduced-motion: reduce)").matches;}catch(e){}
var t=0,cur=0,suona=false,raf=0,ult=0,presa=false,eraSu=false;
function pad(x){return(x<10?"0":"")+x;}
function codice(s){var f=Math.floor(s*fps+0.0001);return pad(Math.floor(f/(3600*fps)))+":"+pad(Math.floor(f/(60*fps))%60)+":"+pad(Math.floor(f/fps)%60)+":"+pad(f%fps);}
function clipDi(s){for(var k=n-1;k>=0;k--){if(s>=inizi[k]){return k;}}return 0;}
function video(k){var v=vv[k];return(v)?(v.tagName==="VIDEO"?v:null):null;}
function mostra(k){if(k===cur){return;}var vec=video(cur);if(vec){try{vec.pause();}catch(e){}}
cur=k;for(var i=0;i<vv.length;i++){vv[i].classList.toggle("is-su",i===k);}
for(var j=0;j<clips.length;j++){clips[j].classList.toggle("is-su",j===k);}
for(var q=0;q<voci.length;q++){var on=(q===k);voci[q].classList.toggle("is-su",on);if(on){voci[q].removeAttribute("aria-hidden");}else{voci[q].setAttribute("aria-hidden","true");}
var l=voci[q].querySelector(".omt-link");if(l){if(on){l.removeAttribute("tabindex");}else{l.setAttribute("tabindex","-1");}}}
var nx=video((k+1)%n);if(nx){if(nx.preload==="none"){nx.preload="auto";}}}
function disegna(){el.style.setProperty("--omt-x",(t/tot).toFixed(5));tc.textContent=codice(t);
tl.setAttribute("aria-valuenow",t.toFixed(2));tl.setAttribute("aria-valuetext",codice(t)+" "+((C.titoli||[])[cur]||""));}
function vai(s,cerca){s=((s%tot)+tot)%tot;t=s;var k=clipDi(s);mostra(k);var v=video(k);
if(v){if(cerca){var loc=s-inizi[k];try{if(v.duration){loc=loc%v.duration;}v.currentTime=loc;}catch(e){}}if(suona){var p=v.play();if(p){if(p.catch){p.catch(function(){});}}}}disegna();}
function passo(ts){raf=0;if(!suona){return;}var v=video(cur);
if(v){if(!v.paused){var loc=v.currentTime;if(loc>=D[cur]-0.04){vai(inizi[(cur+1)%n],true);}else{t=inizi[cur]+loc;disegna();}}
else{t+=((ts-ult)||16)/1000;if(t>=inizi[cur]+D[cur]){vai(inizi[(cur+1)%n],true);}else{disegna();}}}
else{t+=((ts-ult)||16)/1000;if(t>=inizi[cur]+D[cur]){vai(inizi[(cur+1)%n],true);}else{disegna();}}
ult=ts;raf=requestAnimationFrame(passo);}
function avvia(){if(suona){return;}suona=true;el.classList.add("is-play");vai(t,true);ult=performance.now();if(!raf){raf=requestAnimationFrame(passo);}}
function ferma(){suona=false;el.classList.remove("is-play");var v=video(cur);if(v){try{v.pause();}catch(e){}}}
play.addEventListener("click",function(){if(suona){ferma();}else{avvia();}});
function daX(e){var r=tl.getBoundingClientRect(),et=tl.querySelector(".omt-et"),l=et?et.getBoundingClientRect().width:0;return Math.max(0,Math.min(0.9999,(e.clientX-r.left-l)/Math.max(1,r.width-l)))*tot;}
tl.addEventListener("pointerdown",function(e){if(e.button){if(e.button!==0){return;}}var c=e.target.closest?e.target.closest(".omt-clip"):null;
presa=true;eraSu=suona;if(suona){ferma();}try{tl.setPointerCapture(e.pointerId);}catch(x){}
if(c){vai(inizi[parseInt(c.getAttribute("data-i"),10)||0],true);}else{vai(daX(e),true);}});
tl.addEventListener("pointermove",function(e){if(presa){vai(daX(e),true);}});
function lascia(){if(!presa){return;}presa=false;if(eraSu){avvia();}}
tl.addEventListener("pointerup",lascia);tl.addEventListener("pointercancel",lascia);
tl.addEventListener("keydown",function(e){var k=e.key,d=0;
if(k==="ArrowRight"){d=e.shiftKey?1:1/fps;}else if(k==="ArrowLeft"){d=e.shiftKey?-1:-1/fps;}
else if(k==="Home"){e.preventDefault();vai(0,true);return;}else if(k==="End"){e.preventDefault();vai(tot-1/fps,true);return;}
else if(k===" "){e.preventDefault();if(suona){ferma();}else{avvia();}return;}else if(k==="k"){e.preventDefault();if(suona){ferma();}else{avvia();}return;}else if(k==="K"){e.preventDefault();if(suona){ferma();}else{avvia();}return;}
else if(k==="l"){e.preventDefault();avvia();return;}else if(k==="L"){e.preventDefault();avvia();return;}
else if(k==="j"){d=-5;}else if(k==="J"){d=-5;}
if(d){e.preventDefault();if(suona){ferma();}vai(t+d,true);}});
disegna();
if(C.auto){if(!rid){try{var io=new IntersectionObserver(function(en){if(en[0].isIntersecting){avvia();}else{ferma();}},{threshold:0.35});io.observe(el);}catch(e){}}}
};}
JS;
    }
}
