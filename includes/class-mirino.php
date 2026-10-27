<?php
/**
 * Olobuild_Mirino — il mirino di una fotocamera sopra la scena della tile Hero.
 *
 * Gli angoli del fotogramma, la griglia dei terzi, la croce al centro e i dati di scatto in basso (modo, tempo,
 * diaframma, ISO, l'esposimetro e le pose rimaste). Il punto di messa a fuoco segue il puntatore: attorno resta
 * nitido, il resto della foto o del video si sfoca (backdrop-filter con una maschera radiale, il testo della hero
 * sta sopra e resta netto). Muovendosi il fuoco «cerca» (la zona nitida si stringe), fermo si aggancia: le staffe
 * si stringono e prendono il colore d'accento, l'ago dell'esposimetro torna allo zero. Senza mouse (telefono) o
 * fermo da qualche secondo, il fuoco va da solo da un punto all'altro.
 *
 * Il clic sulla scena (o il pulsante tondo, anche da tastiera) scatta: le sei lamelle del diaframma si chiudono e
 * si riaprono, il fotogramma di quell'istante (del video o della foto) si sviluppa in un provino che entra nella
 * striscia accanto al pulsante, e le pose scendono; finito il rullino, lo scatto dopo lo riavvolge. Nel builder
 * scatta solo il pulsante (il clic sulla tile la seleziona). Con «riduci movimento» niente lamelle né fuoco che
 * vaga: lo scatto è un lampo breve.
 *
 * Chiavi `vf_*` della tile, tutte senza effetto finché `vf_on` è spento (la hero resta com'era).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Mirino {

    const DEFAULTS = [
        'vf_on'            => false,
        // contenuto
        'vf_readout'       => 'M · 1/125 · f/8 · ISO 100',
        'vf_frames'        => 36,
        'vf_hint'          => 'Clicca per scattare',
        'vf_shutter_label' => 'Scatta',
        'vf_focus'         => true,
        'vf_shutter'       => true,
        'vf_strip'         => true,
        'vf_grid'          => true,
        'vf_meter'         => true,
        'vf_x'             => 66,
        'vf_y'             => 42,
        // stile
        'vf_color'         => '',
        'vf_accent'        => '',
        'vf_blur'          => 8,
        'vf_focus_size'    => 26,
        'vf_font_family'   => '',
    ];

    /** Il mirino è acceso? */
    public static function attivo( $s ) {
        return ! empty( $s['vf_on'] );
    }

    private static function num( $v, $min, $max, $def ) {
        if ( ! is_numeric( $v ) ) {
            return $def;
        }
        return max( $min, min( $max, (float) $v ) );
    }

    /**
     * Le regole del mirino. $sel = selettore della hero; $inset = il bordo della scena (la cornice fotografica);
     * $linea, $accento = colori già sicuri; $font = famiglia dei dati già risolta ('' = il mono del tema).
     */
    public static function css( $s, $sel, $inset, $linea, $accento, $font ) {
        $blur  = (int) self::num( $s['vf_blur'] ?? 8, 0, 30, 8 );
        $fuoco = self::num( $s['vf_focus_size'] ?? 26, 8, 60, 26 );
        $fam   = '' !== $font ? $font : 'var(--olo-font-family-mono, ui-monospace, SFMono-Regular, Menlo, monospace)';
        $l22   = 'color-mix(in srgb, currentColor 24%, transparent)';
        $terzi = 'transparent calc(33.333% - .5px),' . $l22 . ' calc(33.333% - .5px) calc(33.333% + .5px),transparent calc(33.333% + .5px) calc(66.666% - .5px),' . $l22 . ' calc(66.666% - .5px) calc(66.666% + .5px),transparent calc(66.666% + .5px)';
        $c = '';
        $c .= '@property --olo-vfr{syntax:"<length>";inherits:true;initial-value:0px}';
        $c .= '@property --olo-vfi{syntax:"<length>";inherits:true;initial-value:0px}';
        $c .= "{$sel}.olo-vf-on{overflow:hidden}";
        $c .= "{$sel}.olo-vf-on.olo-vf-scatta{cursor:crosshair}";
        $c .= "{$sel} .olo-vf,{$sel} .olo-vf-ui{position:absolute;inset:{$inset};pointer-events:none;color:{$linea}}";
        $c .= "{$sel} .olo-vf{z-index:1;--vfx:66%;--vfy:42%}";
        $c .= "{$sel} .olo-vf-ui{z-index:3;font-family:{$fam};font-size:11px;line-height:1;letter-spacing:.14em;text-transform:uppercase;font-variant-numeric:tabular-nums}";
        // sfocatura fuori dal fuoco: nitido dentro il 55% del raggio, sfocato del tutto al raggio
        if ( $blur > 0 ) {
            $m = 'radial-gradient(circle at var(--vfx) var(--vfy),transparent calc(var(--olo-vfr) * .5),#000 var(--olo-vfr))';
            $c .= "{$sel} .olo-vf-blur{position:absolute;inset:0;--olo-vfr:{$fuoco}vmin;-webkit-backdrop-filter:blur({$blur}px);backdrop-filter:blur({$blur}px);-webkit-mask:{$m};mask:{$m};transition:--olo-vfr .55s cubic-bezier(.2,.7,.2,1)}";
            $c .= "{$sel} .olo-vf.is-caccia .olo-vf-blur{--olo-vfr:" . round( $fuoco * 0.5, 2 ) . 'vmin;transition-duration:.25s}';
        }
        $c .= "{$sel} .olo-vf-grid{position:absolute;inset:0;background:linear-gradient(90deg,{$terzi}),linear-gradient(180deg,{$terzi})}";
        // angoli del fotogramma e croce al centro
        $c .= "{$sel} .olo-vf-c{position:absolute;width:26px;height:26px;border:0 solid currentColor;opacity:.92}";
        $c .= "{$sel} .olo-vf-c1{top:18px;left:18px;border-top-width:1.5px;border-left-width:1.5px}";
        $c .= "{$sel} .olo-vf-c2{top:18px;right:18px;border-top-width:1.5px;border-right-width:1.5px}";
        $c .= "{$sel} .olo-vf-c3{bottom:18px;left:18px;border-bottom-width:1.5px;border-left-width:1.5px}";
        $c .= "{$sel} .olo-vf-c4{bottom:18px;right:18px;border-bottom-width:1.5px;border-right-width:1.5px}";
        $c .= "{$sel} .olo-vf-x{position:absolute;left:50%;top:50%;width:14px;height:14px;margin:-7px 0 0 -7px;opacity:.55;background:linear-gradient(currentColor,currentColor) 50% 0/1px 100% no-repeat,linear-gradient(currentColor,currentColor) 0 50%/100% 1px no-repeat}";
        // le staffe del fuoco
        $c .= "{$sel} .olo-vf-af{position:absolute;left:var(--vfx);top:var(--vfy);width:78px;height:58px;transform:translate(-50%,-50%);transition:transform .2s cubic-bezier(.2,.7,.2,1),color .2s}";
        $c .= "{$sel} .olo-vf-af i{position:absolute;width:13px;height:13px;border:0 solid currentColor}";
        $c .= "{$sel} .olo-vf-af i:nth-child(1){top:0;left:0;border-top-width:1.5px;border-left-width:1.5px}";
        $c .= "{$sel} .olo-vf-af i:nth-child(2){top:0;right:0;border-top-width:1.5px;border-right-width:1.5px}";
        $c .= "{$sel} .olo-vf-af i:nth-child(3){bottom:0;left:0;border-bottom-width:1.5px;border-left-width:1.5px}";
        $c .= "{$sel} .olo-vf-af i:nth-child(4){bottom:0;right:0;border-bottom-width:1.5px;border-right-width:1.5px}";
        $c .= "{$sel} .olo-vf-af b{position:absolute;left:50%;top:50%;width:4px;height:4px;margin:-2px 0 0 -2px;border-radius:50%;background:currentColor;opacity:0;transition:opacity .2s}";
        $c .= "{$sel} .olo-vf.is-fuoco .olo-vf-af{color:{$accento};transform:translate(-50%,-50%) scale(.84);animation:olo-vf-aggancio .36s steps(2,jump-none) 1}";
        $c .= "{$sel} .olo-vf.is-fuoco .olo-vf-af b{opacity:1}";
        $c .= '@keyframes olo-vf-aggancio{0%,100%{opacity:1}50%{opacity:.25}}';
        $c .= "{$sel} .olo-vf-hint{position:absolute;left:var(--vfx);top:calc(var(--vfy) + 44px);transform:translateX(-50%);font-family:{$fam};font-size:10px;letter-spacing:.22em;text-transform:uppercase;white-space:nowrap;text-shadow:0 1px 3px rgba(0,0,0,.45);transition:opacity .5s}";
        $c .= "{$sel} .olo-vf.is-scattato .olo-vf-hint{opacity:0}";
        // la barra dei dati
        $c .= "{$sel} .olo-vf-bar{position:absolute;left:52px;right:52px;bottom:20px;display:flex;align-items:center;gap:22px;text-shadow:0 1px 3px rgba(0,0,0,.4)}";
        $c .= "{$sel} .olo-vf-mode{display:inline-flex;align-items:center;justify-content:center;min-width:18px;height:18px;padding:0 4px;border:1px solid currentColor;box-sizing:border-box}";
        $c .= "{$sel} .olo-vf-meter{position:relative;margin:0 auto;width:132px;height:16px;flex:none;background:linear-gradient(currentColor,currentColor) 50% 100%/1px 7px no-repeat,repeating-linear-gradient(90deg,currentColor 0 1px,transparent 1px 16.5px) 0 100%/100% 4px no-repeat;opacity:.95}";
        $c .= "{$sel} .olo-vf-meter span{position:absolute;top:-1px;font-size:9px;letter-spacing:0}";
        $c .= "{$sel} .olo-vf-meter span:first-child{left:-2px}{$sel} .olo-vf-meter span:nth-child(2){right:-2px}";
        $c .= "{$sel} .olo-vf-ago{position:absolute;bottom:6px;left:calc(50% + var(--olo-vfe, 0) * 25%);width:0;height:0;margin-left:-4px;border:4px solid transparent;border-top:5px solid {$accento};border-bottom:0;transition:left .3s cubic-bezier(.2,.7,.2,1)}";
        $c .= "{$sel} .olo-vf-count{display:inline-flex;align-items:center;gap:7px;margin-left:auto}";
        $c .= "{$sel} .olo-vf-meter + .olo-vf-count{margin-left:0}";
        $c .= "{$sel} .olo-vf-count svg{width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:1.5}";
        $c .= "{$sel} .olo-vf-ui.is-fine .olo-vf-count{color:{$accento}}";
        // pulsante di scatto e striscia dei provini
        $c .= "{$sel} .olo-vf-btn{position:absolute;right:26px;bottom:52px;width:58px;height:58px;margin:0;padding:0;border:1.5px solid currentColor;border-radius:50%;background:transparent;color:inherit;cursor:pointer;pointer-events:auto;-webkit-tap-highlight-color:transparent}";
        $c .= "{$sel} .olo-vf-btn span{position:absolute;inset:6px;border-radius:50%;background:currentColor;opacity:.94;transition:transform .15s,background-color .2s}";
        $c .= "{$sel} .olo-vf-btn:hover span{transform:scale(.9)}{$sel} .olo-vf-btn:active span{transform:scale(.78)}";
        $c .= "{$sel} .olo-vf-btn:focus-visible{outline:2px solid {$accento};outline-offset:4px}";
        $c .= "{$sel} .olo-vf-strip{position:absolute;right:100px;bottom:50px;display:flex;align-items:flex-end;gap:10px}";
        $c .= "{$sel} .olo-vf-t{display:block;width:84px;padding:3px 3px 12px;box-sizing:border-box;background:#f5f3ee;box-shadow:0 8px 22px rgba(0,0,0,.38);transform:rotate(var(--r,0deg));animation:olo-vf-entra .7s cubic-bezier(.2,.8,.2,1) both;position:relative}";
        $c .= "{$sel} .olo-vf-t canvas{display:block;width:100%;height:auto;animation:olo-vf-sviluppo 2.2s ease-out both}";
        $c .= "{$sel} .olo-vf-t small{position:absolute;left:4px;bottom:2px;font-size:7px;letter-spacing:.12em;color:#8a8780}";
        $c .= '@keyframes olo-vf-entra{from{opacity:0;transform:translateY(-34px) scale(1.25) rotate(0deg)}}';
        $c .= '@keyframes olo-vf-sviluppo{from{filter:brightness(2.4) saturate(0) contrast(.6)}60%{filter:brightness(1.15) saturate(.7) contrast(.95)}to{filter:none}}';
        // il diaframma: sei lamelle (semipiani) il cui bordo dista --olo-vfi dal centro; 0 = chiuso
        $c .= "{$sel} .olo-vf-iris{position:absolute;inset:0;overflow:hidden;visibility:hidden;--olo-vfi:90vmax}";
        $c .= "{$sel} .olo-vf-iris i{position:absolute;left:50%;top:50%;width:300vmax;height:300vmax;margin-left:-150vmax;transform-origin:50% 0;background:linear-gradient(180deg,#1b1b1a,#060606 18%);box-shadow:inset 0 1px 0 rgba(255,255,255,.12);transform:rotate(var(--a)) translateY(var(--olo-vfi)) rotate(22deg)}";
        foreach ( [ 0, 60, 120, 180, 240, 300 ] as $k => $a ) {
            $c .= "{$sel} .olo-vf-iris i:nth-child(" . ( $k + 1 ) . "){--a:{$a}deg}";
        }
        $c .= "{$sel} .olo-vf-flash{position:absolute;inset:0;background:#fff;opacity:0}";
        $c .= "{$sel} .olo-vf-ui.is-scatto .olo-vf-iris{visibility:visible;animation:olo-vf-diaframma .42s cubic-bezier(.5,0,.3,1) both}";
        $c .= "{$sel} .olo-vf-ui.is-scatto .olo-vf-flash{animation:olo-vf-lampo .55s ease-out both}";
        $c .= '@keyframes olo-vf-diaframma{0%{--olo-vfi:70vmax}46%,54%{--olo-vfi:0px}100%{--olo-vfi:70vmax}}';
        $c .= '@keyframes olo-vf-lampo{0%,48%{opacity:0}56%{opacity:.42}100%{opacity:0}}';
        $c .= "{$sel} .olo-vf-sr{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap}";
        // telefono: meno dati, provini più piccoli
        $c .= "@media (max-width:640px){{$sel} .olo-vf-bar{left:30px;right:30px;bottom:16px;gap:14px}{$sel} .olo-vf-meter,{$sel} .olo-vf-data .olo-vf-d:nth-child(n+3){display:none}{$sel} .olo-vf-c{width:18px;height:18px}{$sel} .olo-vf-c1,{$sel} .olo-vf-c2{top:12px}{$sel} .olo-vf-c3,{$sel} .olo-vf-c4{bottom:12px}{$sel} .olo-vf-c1,{$sel} .olo-vf-c3{left:12px}{$sel} .olo-vf-c2,{$sel} .olo-vf-c4{right:12px}{$sel} .olo-vf-btn{width:50px;height:50px;right:18px;bottom:44px}{$sel} .olo-vf-strip{right:80px;bottom:44px;gap:7px}{$sel} .olo-vf-t{width:58px;padding:2px 2px 9px}{$sel} .olo-vf-t:nth-last-child(n+4){display:none}{$sel} .olo-vf-af{width:62px;height:46px}{$sel} .olo-vf-count{margin-left:auto}{$sel} .olo-vf-hint{display:none}}";
        $c .= "{$sel} .olo-vf-data{display:inline-flex;gap:18px}";
        $c .= "@media (prefers-reduced-motion:reduce){{$sel} .olo-vf-blur,{$sel} .olo-vf-af,{$sel} .olo-vf-ago{transition:none}{$sel} .olo-vf.is-fuoco .olo-vf-af{animation:none}{$sel} .olo-vf-ui.is-scatto .olo-vf-iris{animation:none;visibility:hidden}{$sel} .olo-vf-t,{$sel} .olo-vf-t canvas{animation:none}}";
        return $c;
    }

    /** Il markup: lo strato del fuoco (sotto il testo) e quello dei comandi (sopra). */
    public static function markup( $s, $uid, $builder = false ) {
        $frames  = (int) self::num( $s['vf_frames'] ?? 36, 0, 99, 36 );
        $focus   = ! empty( $s['vf_focus'] );
        $shutter = ! empty( $s['vf_shutter'] );
        $strip   = $shutter && ! empty( $s['vf_strip'] );
        $hint    = $shutter ? trim( wp_strip_all_tags( (string) ( $s['vf_hint'] ?? '' ) ) ) : '';
        $label   = trim( wp_strip_all_tags( (string) ( $s['vf_shutter_label'] ?? '' ) ) );
        $label   = '' !== $label ? $label : __( 'Scatta', 'olobuilder' );
        $dati    = array_values( array_filter( array_map( 'trim', preg_split( '/\s*[·|]\s*/u', wp_strip_all_tags( (string) ( $s['vf_readout'] ?? '' ) ) ) ), 'strlen' ) );
        $cfg     = [
            'frames'  => $frames,
            'shutter' => $shutter,
            'strip'   => $strip,
            'focus'   => $focus,
            'builder' => (bool) $builder,
            'ui'      => $uid . '-vfui',
            'x'       => self::num( $s['vf_x'] ?? 66, 5, 95, 66 ) / 100,
            'y'       => self::num( $s['vf_y'] ?? 42, 5, 95, 42 ) / 100,
            /* translators: 1: numero dello scatto, 2: pose del rullino */
            'detto'   => __( 'Scatto %1$d di %2$d', 'olobuilder' ),
            'fine'    => __( 'Rullino finito: lo scatto dopo lo riavvolge.', 'olobuilder' ),
            'riavvolto' => __( 'Rullino riavvolto.', 'olobuilder' ),
        ];

        $h  = '<div class="olo-vf" id="' . esc_attr( $uid ) . '-vf" aria-hidden="true" data-olo-vf="' . esc_attr( wp_json_encode( $cfg ) ) . '">';
        if ( $focus ) {
            $h .= '<div class="olo-vf-blur"></div>';
        }
        if ( ! empty( $s['vf_grid'] ) ) {
            $h .= '<div class="olo-vf-grid"></div>';
        }
        $h .= '<span class="olo-vf-c olo-vf-c1"></span><span class="olo-vf-c olo-vf-c2"></span><span class="olo-vf-c olo-vf-c3"></span><span class="olo-vf-c olo-vf-c4"></span>';
        $h .= '<span class="olo-vf-x"></span>';
        $h .= '<span class="olo-vf-af"><i></i><i></i><i></i><i></i><b></b></span>';
        if ( '' !== $hint ) {
            $h .= '<span class="olo-vf-hint">' . esc_html( $hint ) . '</span>';
        }
        $h .= '</div>';

        $h .= '<div class="olo-vf-ui" id="' . esc_attr( $uid ) . '-vfui">';
        $h .= '<div class="olo-vf-bar" aria-hidden="true">';
        if ( $dati ) {
            $primo = array_shift( $dati );
            $h    .= '<span class="olo-vf-data"><span class="olo-vf-d olo-vf-mode">' . esc_html( $primo ) . '</span>';
            foreach ( $dati as $d ) {
                $h .= '<span class="olo-vf-d">' . esc_html( $d ) . '</span>';
            }
            $h .= '</span>';
        }
        if ( ! empty( $s['vf_meter'] ) ) {
            $h .= '<span class="olo-vf-meter"><span>−</span><span>+</span><em class="olo-vf-ago"></em></span>';
        }
        if ( $frames > 0 ) {
            $h .= '<span class="olo-vf-count"><svg viewBox="0 0 16 16" aria-hidden="true" focusable="false"><rect x="2.5" y="2.5" width="11" height="11" rx="1"/><path d="M5 2.5v11M11 2.5v11"/></svg><b class="olo-vf-n">' . (int) $frames . '</b></span>';
        }
        $h .= '</div>';
        if ( $strip ) {
            $h .= '<div class="olo-vf-strip" aria-hidden="true"></div>';
        }
        if ( $shutter ) {
            $h .= '<button type="button" class="olo-vf-btn" aria-label="' . esc_attr( $label ) . '" title="' . esc_attr( $label ) . '" data-olo-interactive><span></span></button>';
            $h .= '<span class="olo-vf-sr" role="status" aria-live="polite"></span>';
        }
        $h .= '<div class="olo-vf-iris" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i></div>';
        $h .= '<div class="olo-vf-flash" aria-hidden="true"></div>';
        $h .= '</div>';
        return $h;
    }

    /**
     * Lo script: definito una volta (if(!window.oloMirino)), avvia ogni mirino della pagina una volta sola.
     * Niente «&» (wptexturize lo riscriverebbe fra un «<» e un «>»): condizioni annidate.
     */
    public static function script() {
        return <<<'JS'
if(!window.oloMirino){window.oloMirino=function(vf){
if(!vf){return;}
if(vf.getAttribute("data-vf-ok")==="1"){return;}
vf.setAttribute("data-vf-ok","1");
var C={};try{C=JSON.parse(vf.getAttribute("data-olo-vf")||"{}");}catch(e){}
var hero=vf.parentNode,ui=document.getElementById(C.ui);if(!ui){return;}
var rid=false;try{rid=window.matchMedia("(prefers-reduced-motion: reduce)").matches;}catch(e){}
var fino=false;try{fino=window.matchMedia("(hover: hover)").matches;}catch(e){}
var strip=ui.querySelector(".olo-vf-strip"),nEl=ui.querySelector(".olo-vf-n"),sr=ui.querySelector(".olo-vf-sr"),btn=ui.querySelector(".olo-vf-btn");
var N=parseInt(C.frames,10)||0,left=N,shot=0,busy=false;
var x=-1,y=-1,tx=0,ty=0,raf=0,tLock=0,tVaga=0,ultimo=0,visibile=true;
function rect(){return vf.getBoundingClientRect();}
function metti(){vf.style.setProperty("--vfx",x.toFixed(1)+"px");vf.style.setProperty("--vfy",y.toFixed(1)+"px");}
function passo(){raf=0;var dx=tx-x,dy=ty-y;x+=dx*0.22;y+=dy*0.22;
if(Math.abs(dx)+Math.abs(dy)<0.6){x=tx;y=ty;}else{raf=requestAnimationFrame(passo);}metti();}
function verso(px,py,subito){var r=rect();tx=Math.max(8,Math.min(r.width-8,px));ty=Math.max(8,Math.min(r.height-8,py));
if(x<0){x=tx;y=ty;}
if(subito){x=tx;y=ty;metti();}else if(!raf){raf=requestAnimationFrame(passo);}
var e=Math.max(-2,Math.min(2,(0.5-ty/Math.max(1,r.height))*4+(tx/Math.max(1,r.width)-0.5)));
ui.style.setProperty("--olo-vfe",e.toFixed(2));
vf.classList.add("is-caccia");vf.classList.remove("is-fuoco");clearTimeout(tLock);tLock=setTimeout(aggancia,rid?0:300);}
function aggancia(){vf.classList.remove("is-caccia");vf.classList.add("is-fuoco");ui.style.setProperty("--olo-vfe","0");}
function vaga(){clearTimeout(tVaga);if(rid){return;}
tVaga=setTimeout(function(){if(visibile){if(Date.now()-ultimo>3000){var r=rect();verso(r.width*(0.3+Math.random()*0.48),r.height*(0.26+Math.random()*0.4));}}vaga();},2600+Math.random()*900);}
var r0=rect();verso(r0.width*(C.x||0.66),r0.height*(C.y||0.42),true);aggancia();
hero.addEventListener("pointermove",function(e){if(e.pointerType==="touch"){return;}ultimo=Date.now();var r=rect();verso(e.clientX-r.left,e.clientY-r.top);});
vaga();
try{var io=new IntersectionObserver(function(en){visibile=en[0].isIntersecting;});io.observe(hero);}catch(e){}
var bgImg=null;
(function(){var m=hero.querySelector(".olo-hero-media");if(!m){return;}
var bi=getComputedStyle(m).backgroundImage||"",u=bi.match(/url\(["']?([^"')]+)["']?\)/);
if(u){bgImg=new Image();bgImg.decoding="async";bgImg.src=u[1];}})();
function sorgente(){var v=hero.querySelector(".olo-hero-media video");
if(v){if(v.readyState>1){if(v.videoWidth){return{el:v,w:v.videoWidth,h:v.videoHeight};}}}
var im=hero.querySelector(".olo-hero-media img");if(im){if(im.naturalWidth){return{el:im,w:im.naturalWidth,h:im.naturalHeight};}}
if(bgImg){if(bgImg.naturalWidth){return{el:bgImg,w:bgImg.naturalWidth,h:bgImg.naturalHeight};}}
return null;}
function provino(){var r=rect(),W=200,H=Math.max(60,Math.round(W*r.height/Math.max(1,r.width)));
var cv=document.createElement("canvas");cv.width=W;cv.height=H;var g=cv.getContext("2d");if(!g){return cv;}
var src=sorgente();
if(!src){g.fillStyle="#2a2926";g.fillRect(0,0,W,H);return cv;}
var k=Math.max(W/src.w,H/src.h),dw=src.w*k,dh=src.h*k,dx=(W-dw)/2,dy=(H-dh)/2;
try{if(C.focus){g.filter="blur(2.2px)";}g.drawImage(src.el,dx,dy,dw,dh);
if(C.focus){g.filter="none";g.save();g.beginPath();g.arc(x/Math.max(1,r.width)*W,y/Math.max(1,r.height)*H,Math.min(W,H)*0.3,0,Math.PI*2);g.clip();g.drawImage(src.el,dx,dy,dw,dh);g.restore();}}
catch(e){g.fillStyle="#2a2926";g.fillRect(0,0,W,H);}
return cv;}
function dici(t){if(sr){sr.textContent=t;}}
function riavvolgi(){left=N;shot=0;if(nEl){nEl.textContent=String(N);}ui.classList.remove("is-fine");if(strip){strip.innerHTML="";}dici(C.riavvolto||"");}
function sviluppa(){
if(N>0){if(left<=0){riavvolgi();return;}left--;shot++;if(nEl){nEl.textContent=String(left);}}else{shot++;}
if(strip){var t=document.createElement("span");t.className="olo-vf-t";t.style.setProperty("--r",(Math.random()*6-3).toFixed(1)+"deg");
t.appendChild(provino());var sm=document.createElement("small");sm.textContent=String(shot)+(shot%2?"":"A");t.appendChild(sm);
strip.appendChild(t);while(strip.children.length>4){strip.removeChild(strip.firstChild);}}
if(N>0){if(left===0){ui.classList.add("is-fine");dici(C.fine||"");}else{dici((C.detto||"").replace("%1$d",String(shot)).replace("%2$d",String(N)));}}}
function scatta(px,py){if(busy){return;}busy=true;
if(typeof px==="number"){verso(px,py,true);aggancia();}
vf.classList.add("is-scattato");ui.classList.remove("is-scatto");void ui.offsetWidth;ui.classList.add("is-scatto");
setTimeout(sviluppa,rid?0:200);
setTimeout(function(){busy=false;ui.classList.remove("is-scatto");},rid?120:440);}
if(btn){btn.addEventListener("click",function(e){e.preventDefault();e.stopPropagation();scatta();});}
if(C.shutter){if(!C.builder){hero.classList.add("olo-vf-scatta");
hero.addEventListener("click",function(e){var t=e.target;
if(t.closest){if(t.closest("a,button,input,select,textarea,label,[data-olo-interactive]")){return;}}
var r=rect();ultimo=Date.now();scatta(e.clientX-r.left,e.clientY-r.top);});}}
};}
JS;
    }
}
