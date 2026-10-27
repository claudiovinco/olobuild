<?php
/**
 * Tile Pellicole — una foto e le pellicole con cui «svilupparla»: sotto, i rullini da scegliere (il barattolo col
 * colore della pellicola, il nome, una nota); sopra, la stessa foto divisa in due da un divisore da trascinare, lo
 * scatto grezzo a sinistra e lo sviluppo a destra. Ogni look è una ricetta CSS (filtri, una tinta con il suo modo di
 * fusione, la vignettatura, la grana; il digicam anni 2000 ha anche la data arancione stampata nell'angolo).
 *
 * Cambiando pellicola lo sviluppo passa con un lampo di luce breve. Il divisore si trascina (dito o mouse, anche sulla
 * foto), si sposta con le frecce (è uno slider), e la prima volta che la foto entra nello schermo scorre da solo da
 * destra a metà per mostrare la differenza. Con «Confronto» spento lo sviluppo copre tutta la foto. Con «riduci
 * movimento» niente scorrimento né lampo. Nata per il tema di uno studio fotografico attento alle mode (27 ott 2026).
 * Config: src/config/elements/pellicole.js.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Pellicole_Tile extends Olobuild_Tile_Base {

    protected $type     = 'pellicole';
    protected $name     = 'Pellicole';
    protected $icon     = 'dashicons-camera';
    protected $category = 'interactive';

    /**
     * Le ricette: filtro, tinta (colore o sfumatura, fusione, opacità), vignettatura (0–1), colore del barattolo.
     */
    const LOOK = [
        'portra'    => [ 'contrast(.92) saturate(.9) sepia(.14) brightness(1.06)', '#f1b98a', 'soft-light', 0.38, 0.12, '#e8a33d' ],
        'ektar'     => [ 'contrast(1.14) saturate(1.45) brightness(1.02)', '#ff6a2b', 'overlay', 0.1, 0.18, '#d6332b' ],
        'cinestill' => [ 'contrast(1.08) saturate(1.12) hue-rotate(-10deg) brightness(.95)', 'linear-gradient(180deg,#2a6f7f,#ff4a2e)', 'soft-light', 0.42, 0.3, '#b3262c' ],
        'fuji'      => [ 'contrast(.95) saturate(.9) hue-rotate(8deg) brightness(1.05)', '#7cc8b2', 'soft-light', 0.32, 0.1, '#2e9a6b' ],
        'bn'        => [ 'grayscale(1) contrast(1.28) brightness(1.03)', '', 'normal', 0, 0.28, '#2b2b2b' ],
        'seppia'    => [ 'sepia(.62) contrast(1.05) brightness(1.03) saturate(.82)', '#7a4a1e', 'multiply', 0.14, 0.32, '#8a5a2b' ],
        'flash'     => [ 'contrast(1.4) saturate(1.12) brightness(1.15)', '', 'normal', 0, 0.62, '#f3f1ea' ],
        'burro'     => [ 'sepia(.3) saturate(.84) contrast(.9) brightness(1.09)', '#f6d98b', 'multiply', 0.2, 0.08, '#f2c94c' ],
        'notte'     => [ 'contrast(1.14) saturate(1.06) brightness(.92)', 'linear-gradient(180deg,#0f4c5c,#e36414)', 'soft-light', 0.5, 0.36, '#0f4c5c' ],
        'digicam'   => [ 'contrast(1.2) saturate(1.32) brightness(1.1) hue-rotate(-4deg)', '#5ac8fa', 'soft-light', 0.14, 0.2, '#9b5de5' ],
    ];

    protected $defaults = [
        'immagine'           => '',
        'alt'                => '',
        'pellicole'          => [
            [ 'nome' => 'Portra 400', 'nota' => 'La pelle morbida, i verdi caldi', 'look' => 'portra', 'colore' => '' ],
            [ 'nome' => 'Cinestill 800T', 'nota' => 'Le luci della sera, l’alone rosso', 'look' => 'cinestill', 'colore' => '' ],
            [ 'nome' => 'HP5 Plus', 'nota' => 'Bianco e nero di grana', 'look' => 'bn', 'colore' => '' ],
            [ 'nome' => 'Flash diretto', 'nota' => 'Il lampo in faccia, gli angoli scuri', 'look' => 'flash', 'colore' => '' ],
            [ 'nome' => 'Toni burro', 'nota' => 'Giallo morbido, contrasto basso', 'look' => 'burro', 'colore' => '' ],
        ],
        'partenza'           => 1,
        'confronto'          => true,
        'divisore'           => 50,
        'etichetta_grezzo'   => 'Scatto grezzo',
        'mostra_nota'        => true,
        'mostra_etichette'   => true,
        'data_digicam'       => '',
        'grana'              => 30,
        // stile
        'colore_accento'     => '',
        'colore_divisore'    => '',
        'sfondo_rullino'     => '',
        'nome_font_family'   => '',
        'nome_font_size'     => 15,
        'nome_font_weight'   => '600',
        'nome_color'         => '',
        'nota_font_family'   => '',
        'nota_font_size'     => 13,
        'nota_font_weight'   => '',
        'nota_color'         => '',
        'proporzione'        => '3/2',
        'raggio'             => [ 'tl' => 10, 'tr' => 10, 'br' => 10, 'bl' => 10 ],
        'raggio_rullino'     => [ 'tl' => 10, 'tr' => 10, 'br' => 10, 'bl' => 10 ],
        'posizione_rullini'  => 'sotto',
        'gap'                => 18,
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
        if ( $col ) {
            $d .= 'color:' . $col . ';';
        }
        return '' !== $d ? "{$selettore}{{$d}}" : '';
    }

    public function render( $settings ) {
        $s       = wp_parse_args( $settings, $this->defaults );
        $uid     = 'opl-' . $this->chiave_stabile( $settings );
        $base    = $uid;
        $copia_n = 2;
        while ( isset( self::$uid_usate[ $uid ] ) ) {
            $uid = $base . '-' . $copia_n++;
        }
        self::$uid_usate[ $uid ] = true;
        $sel     = '#' . $uid;
        $builder = ! empty( $s['_builder_mode'] );

        $img = esc_url_raw( self::url_di( $s['immagine'] ) );
        $rul = [];
        foreach ( ( is_array( $s['pellicole'] ) ? $s['pellicole'] : [] ) as $p ) {
            if ( ! is_array( $p ) ) {
                continue;
            }
            $look = isset( self::LOOK[ $p['look'] ?? '' ] ) ? $p['look'] : 'portra';
            $nome = trim( wp_strip_all_tags( (string) ( $p['nome'] ?? '' ) ) );
            if ( '' === $nome ) {
                continue;
            }
            $rul[] = [
                'nome'   => $nome,
                'nota'   => trim( wp_strip_all_tags( (string) ( $p['nota'] ?? '' ) ) ),
                'look'   => $look,
                'colore' => $this->safe_color_css( $p['colore'] ?? '' ) ?: self::LOOK[ $look ][5],
            ];
        }
        if ( '' === $img || ! $rul ) {
            if ( ! $builder ) {
                return '';
            }
            return '<div style="padding:1.5rem;border:1px dashed currentColor;opacity:.7;font-size:14px">' . esc_html__( 'Scegli una foto e almeno una pellicola nel Contenuto.', 'olobuilder' ) . '</div>';
        }
        $n        = count( $rul );
        $partenza = max( 0, min( $n - 1, (int) $s['partenza'] - 1 ) );
        $confr    = ! empty( $s['confronto'] );
        $div      = $confr ? max( 0, min( 100, (int) $s['divisore'] ) ) : 0;
        $grana    = max( 0, min( 100, (int) $s['grana'] ) );
        $prop     = preg_match( '#^\d{1,2}/\d{1,2}$#', (string) $s['proporzione'] ) ? (string) $s['proporzione'] : '3/2';
        $acc      = $this->safe_color_css( $s['colore_accento'] ) ?: 'var(--olo-color-primary)';
        $col_div  = $this->safe_color_css( $s['colore_divisore'] ) ?: '#ffffff';
        $sf_rul   = $this->safe_color_css( $s['sfondo_rullino'] ) ?: 'color-mix(in srgb, currentColor 6%, transparent)';
        $raggio   = Olobuild_Tile_Utils::border_radius( $s['raggio'] ) ?: '0';
        $r_rul    = Olobuild_Tile_Utils::border_radius( $s['raggio_rullino'] ) ?: '0';
        $gap      = max( 0, min( 80, (int) $s['gap'] ) );
        $sopra    = 'sopra' === (string) $s['posizione_rullini'];
        $data_dc  = trim( wp_strip_all_tags( (string) $s['data_digicam'] ) );
        if ( '' === $data_dc ) {
            $data_dc = "'" . wp_date( 'y m d' );
        }
        $alt      = trim( wp_strip_all_tags( (string) $s['alt'] ) );

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS da costanti della classe e valori filtrati (safe_color_css, interi, raggi), markup con esc_*().
        echo '<style>';
        echo "{$sel}{margin:0;display:flex;flex-direction:column;gap:{$gap}px;--opl-a:{$acc}}";
        if ( $sopra ) {
            echo "{$sel} .opl-rullini{order:-1}";
        }
        echo "{$sel} .opl-quadro{position:relative;aspect-ratio:{$prop};overflow:hidden;border-radius:{$raggio};touch-action:pan-y;user-select:none;-webkit-user-select:none;background:#111;--opl-x:{$div}%}";
        echo "{$sel} .opl-quadro img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:block;pointer-events:none}";
        echo "{$sel} .opl-sviluppo{position:absolute;inset:0;clip-path:inset(0 0 0 var(--opl-x));-webkit-clip-path:inset(0 0 0 var(--opl-x))}";
        echo "{$sel} .opl-tinta,{$sel} .opl-vign,{$sel} .opl-grana,{$sel} .opl-luce{position:absolute;inset:0;pointer-events:none}";
        echo "{$sel} .opl-vign{background:radial-gradient(ellipse at 50% 50%,transparent 46%,rgba(0,0,0,.9) 100%);opacity:0}";
        if ( $grana > 0 ) {
            $rumore = "url(\"data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='180' height='180'><filter id='g'><feTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='2' stitchTiles='stitch'/><feColorMatrix values='0 0 0 0 .5 0 0 0 0 .5 0 0 0 0 .5 0 0 0 1 0'/></filter><rect width='100%' height='100%' filter='url(%23g)'/></svg>\")";
            echo "{$sel} .opl-grana{background:{$rumore};background-size:180px;mix-blend-mode:overlay;opacity:" . round( $grana / 100 * 0.55, 3 ) . '}';
        }
        echo "{$sel} .opl-luce{background:#fff;opacity:0}";
        echo "{$sel} .opl-sviluppo.is-sviluppa .opl-luce{animation:opl-lampo .7s ease-out}";
        echo '@keyframes opl-lampo{0%{opacity:.55}100%{opacity:0}}';
        echo "{$sel} .opl-data{position:absolute;right:4%;bottom:5%;font-family:'Courier New',ui-monospace,monospace;font-weight:700;font-size:clamp(11px,1.7vw,22px);letter-spacing:.06em;color:#ff8c2b;text-shadow:0 0 6px rgba(255,120,30,.7);display:none}";
        echo "{$sel} .opl-sviluppo[data-look=\"digicam\"] .opl-data{display:block}";
        foreach ( self::LOOK as $k => $l ) {
            echo "{$sel} .opl-sviluppo[data-look=\"{$k}\"] .opl-foto{filter:{$l[0]}}";
            if ( '' !== $l[1] ) {
                echo "{$sel} .opl-sviluppo[data-look=\"{$k}\"] .opl-tinta{background:{$l[1]};mix-blend-mode:{$l[2]};opacity:{$l[3]}}";
            }
            echo "{$sel} .opl-sviluppo[data-look=\"{$k}\"] .opl-vign{opacity:{$l[4]}}";
        }
        echo "{$sel} .opl-foto,{$sel} .opl-tinta,{$sel} .opl-vign{transition:filter .5s ease,opacity .5s ease,background .5s ease}";
        // divisore
        echo "{$sel} .opl-div{position:absolute;top:0;bottom:0;left:var(--opl-x);width:44px;transform:translateX(-50%);cursor:ew-resize;z-index:2;outline:none}";
        echo "{$sel} .opl-div::before{content:'';position:absolute;left:21px;top:0;bottom:0;width:2px;background:{$col_div};box-shadow:0 0 12px rgba(0,0,0,.35)}";
        echo "{$sel} .opl-man{position:absolute;left:50%;top:50%;width:44px;height:44px;transform:translate(-50%,-50%);border-radius:50%;background:{$col_div};box-shadow:0 4px 16px rgba(0,0,0,.3);display:flex;align-items:center;justify-content:center;color:#111;transition:transform .2s}";
        echo "{$sel} .opl-man svg{width:20px;height:20px;fill:none;stroke:currentColor;stroke-width:2}";
        echo "{$sel} .opl-div:hover .opl-man{transform:translate(-50%,-50%) scale(1.08)}";
        echo "{$sel} .opl-div:focus-visible .opl-man{box-shadow:0 0 0 3px var(--opl-a),0 4px 16px rgba(0,0,0,.3)}";
        echo "{$sel} .opl-et{position:absolute;top:14px;padding:.4em .75em;border-radius:99em;background:rgba(0,0,0,.5);color:#fff;font-size:11px;letter-spacing:.14em;text-transform:uppercase;line-height:1;-webkit-backdrop-filter:blur(6px);backdrop-filter:blur(6px);transition:opacity .3s;z-index:1;white-space:nowrap}";
        echo "{$sel} .opl-et-sx{left:14px}{$sel} .opl-et-dx{right:14px}";
        echo "{$sel}.is-poco-sx .opl-et-sx,{$sel}.is-poco-dx .opl-et-dx{opacity:0}";
        // i rullini
        echo "{$sel} .opl-rullini{display:flex;gap:.6rem;overflow-x:auto;scroll-snap-type:x proximity;padding:.25rem .1rem .5rem;margin:0;scrollbar-width:thin}";
        echo "{$sel} .opl-rullino{flex:1 0 150px;scroll-snap-align:start;display:grid;grid-template-columns:auto 1fr;gap:.25rem .75rem;align-items:center;text-align:left;padding:.75rem .9rem;margin:0;border:1.5px solid transparent;border-radius:{$r_rul};background:{$sf_rul};color:inherit;font:inherit;cursor:pointer;transition:border-color .2s,transform .2s,background-color .2s}";
        echo "{$sel} .opl-rullino:hover{transform:translateY(-2px)}";
        echo "{$sel} .opl-rullino[aria-checked=\"true\"]{border-color:var(--opl-a)}";
        echo "{$sel} .opl-rullino:focus-visible{outline:2px solid var(--opl-a);outline-offset:2px}";
        echo "{$sel} .opl-bob{grid-row:span 2;width:20px;height:30px;border-radius:.25rem;background:linear-gradient(180deg,#1d1d1d 0 5px,var(--opl-c) 5px 25px,#1d1d1d 25px);box-shadow:inset 0 0 0 1px rgba(0,0,0,.25),inset 5px 0 6px rgba(255,255,255,.25)}";
        echo "{$sel} .opl-nome{font-size:15px;font-weight:600;line-height:1.2}";
        echo "{$sel} .opl-nota{font-size:13px;line-height:1.35;opacity:.72}";
        echo "@media (prefers-reduced-motion:reduce){{$sel} .opl-sviluppo.is-sviluppa .opl-luce{animation:none}{$sel} .opl-foto,{$sel} .opl-tinta,{$sel} .opl-vign{transition:none}}";
        echo $this->css_testo( $s, 'nome', "{$sel} .opl-nome" );
        echo $this->css_testo( $s, 'nota', "{$sel} .opl-nota" );
        echo '</style>';

        $grezzo = trim( wp_strip_all_tags( (string) $s['etichetta_grezzo'] ) );
        $grezzo = '' !== $grezzo ? $grezzo : __( 'Scatto grezzo', 'olobuilder' );
        $et     = ! empty( $s['mostra_etichette'] );
        $cfg    = [ 'confronto' => $confr, 'x' => $div, 'auto' => $confr ];

        echo '<figure id="' . esc_attr( $uid ) . '" class="olo-pellicole" data-opl="' . esc_attr( wp_json_encode( $cfg ) ) . '">';
        echo '<div class="opl-quadro" data-olo-own-drag>';
        echo '<img class="opl-grezza" src="' . esc_url( $img ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy" decoding="async">';
        echo '<div class="opl-sviluppo" data-look="' . esc_attr( $rul[ $partenza ]['look'] ) . '" aria-hidden="true">';
        echo '<img class="opl-foto" src="' . esc_url( $img ) . '" alt="" loading="lazy" decoding="async">';
        echo '<span class="opl-tinta"></span><span class="opl-vign"></span>' . ( $grana > 0 ? '<span class="opl-grana"></span>' : '' ) . '<span class="opl-data">' . esc_html( $data_dc ) . '</span><span class="opl-luce"></span>';
        echo '</div>';
        if ( $confr ) {
            if ( $et ) {
                echo '<span class="opl-et opl-et-sx" aria-hidden="true">' . esc_html( $grezzo ) . '</span>';
                echo '<span class="opl-et opl-et-dx" aria-hidden="true">' . esc_html( $rul[ $partenza ]['nome'] ) . '</span>';
            }
            echo '<div class="opl-div" role="slider" tabindex="0" aria-label="' . esc_attr__( 'Confronto fra lo scatto grezzo e lo sviluppo', 'olobuilder' ) . '" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' . (int) $div . '" data-olo-interactive>'
                . '<span class="opl-man"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9 6l-6 6 6 6M15 6l6 6-6 6"/></svg></span></div>';
        }
        echo '</div>';
        echo '<div class="opl-rullini" role="radiogroup" aria-label="' . esc_attr__( 'Pellicole', 'olobuilder' ) . '">';
        foreach ( $rul as $i => $r ) {
            $on = $i === $partenza;
            echo '<button type="button" class="opl-rullino" role="radio" aria-checked="' . ( $on ? 'true' : 'false' ) . '" tabindex="' . ( $on ? '0' : '-1' ) . '" data-look="' . esc_attr( $r['look'] ) . '" data-nome="' . esc_attr( $r['nome'] ) . '" style="--opl-c:' . esc_attr( $r['colore'] ) . '" data-olo-interactive>'
                . '<span class="opl-bob" aria-hidden="true"></span><span class="opl-nome">' . esc_html( $r['nome'] ) . '</span>'
                . ( ! empty( $s['mostra_nota'] ) && '' !== $r['nota'] ? '<span class="opl-nota">' . esc_html( $r['nota'] ) . '</span>' : '' )
                . '</button>';
        }
        echo '</div>';
        echo '</figure>';
        echo '<script>' . $this->script() . '[].forEach.call(document.querySelectorAll("[data-opl]"),function(x){window.oloPellicole(x);});</script>';
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }

    /** Divisore, rullini e scorrimento iniziale. Niente «&» (wptexturize): condizioni annidate. */
    private function script() {
        return <<<'JS'
if(!window.oloPellicole){window.oloPellicole=function(el){
if(!el){return;}
if(el.getAttribute("data-opl-ok")==="1"){return;}
el.setAttribute("data-opl-ok","1");
var C={};try{C=JSON.parse(el.getAttribute("data-opl")||"{}");}catch(e){}
var q=el.querySelector(".opl-quadro"),sv=el.querySelector(".opl-sviluppo"),dv=el.querySelector(".opl-div"),edx=el.querySelector(".opl-et-dx");
var rs=el.querySelectorAll(".opl-rullino");
var rid=false;try{rid=window.matchMedia("(prefers-reduced-motion: reduce)").matches;}catch(e){}
var x=typeof C.x==="number"?C.x:50,toccato=false;
function metti(v){x=Math.max(0,Math.min(100,v));q.style.setProperty("--opl-x",x.toFixed(2)+"%");
if(dv){dv.setAttribute("aria-valuenow",String(Math.round(x)));}
el.classList.toggle("is-poco-sx",x<16);el.classList.toggle("is-poco-dx",x>84);}
function scegli(b,fuoco){for(var k=0;k<rs.length;k++){var on=(rs[k]===b);rs[k].setAttribute("aria-checked",on?"true":"false");rs[k].tabIndex=on?0:-1;}
sv.setAttribute("data-look",b.getAttribute("data-look"));if(edx){edx.textContent=b.getAttribute("data-nome");}
if(!rid){sv.classList.remove("is-sviluppa");void sv.offsetWidth;sv.classList.add("is-sviluppa");}
if(fuoco){b.focus();}}
for(var k=0;k<rs.length;k++){(function(b,idx){
b.addEventListener("click",function(){scegli(b,false);});
b.addEventListener("keydown",function(e){var d=0;if(e.key==="ArrowRight"){d=1;}else if(e.key==="ArrowDown"){d=1;}else if(e.key==="ArrowLeft"){d=-1;}else if(e.key==="ArrowUp"){d=-1;}
if(d){e.preventDefault();scegli(rs[(idx+d+rs.length)%rs.length],true);}});})(rs[k],k);}
if(C.confronto){
var presa=false;
function da(e){var r=q.getBoundingClientRect();return (e.clientX-r.left)/Math.max(1,r.width)*100;}
q.addEventListener("pointerdown",function(e){presa=true;toccato=true;metti(da(e));try{q.setPointerCapture(e.pointerId);}catch(x){}});
q.addEventListener("pointermove",function(e){if(presa){metti(da(e));}});
function fine(){presa=false;}
q.addEventListener("pointerup",fine);q.addEventListener("pointercancel",fine);
if(dv){dv.addEventListener("keydown",function(e){var d=0;if(e.key==="ArrowLeft"){d=-5;}else if(e.key==="ArrowRight"){d=5;}else if(e.key==="Home"){e.preventDefault();toccato=true;metti(0);return;}else if(e.key==="End"){e.preventDefault();toccato=true;metti(100);return;}
if(d){e.preventDefault();toccato=true;metti(x+d);}});}
metti(x);
if(C.auto){if(!rid){try{var io=new IntersectionObserver(function(en){if(!en[0].isIntersecting){return;}io.disconnect();if(toccato){return;}
var fin=x,t0=0;metti(92);function pa(t){if(toccato){return;}if(!t0){t0=t;}var p=Math.min(1,(t-t0)/1500),e2=1-Math.pow(1-p,3);metti(92+(fin-92)*e2);if(p<1){requestAnimationFrame(pa);}}
setTimeout(function(){requestAnimationFrame(pa);},350);},{threshold:0.45});io.observe(q);}catch(e){}}}
}
};}
JS;
    }
}
