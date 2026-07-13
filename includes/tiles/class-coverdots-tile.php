<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * CoverDots — pallini di navigazione per i gruppi "Sticky → Cover orizzontale".
 * Un pallino per sezione del gruppo: l'attivo segue lo scroll (CustomEvent
 * 'olo:hgroup' emesso dal motore cover-h del frontend renderer), il click
 * salta alla fermata. Se la pagina non ha gruppi cover-h la tile si nasconde
 * (comportamento pensato per i template header condivisi).
 */
class Olobuild_Coverdots_Tile extends Olobuild_Tile_Base {

    protected $type     = 'coverdots';
    protected $name     = 'Pallini Cover';
    protected $icon     = 'dashicons-ellipsis';
    protected $category = 'interactive';
    protected $defaults = [
        'items'              => [],
        'hide_without_group' => true,
        'dot_size'           => 34,
        'dot_size_mobile'    => 0, // 0 = come desktop
        'dot_gap'            => 4,
        'dot_inner'          => 9,
        'border_color'       => '',
        'dot_bg'             => '',
        'dot_color'          => '',
        'active_glow'        => true,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'items',              'type' => 'items',  'label' => 'Fermate' ],
            [ 'key' => 'hide_without_group', 'type' => 'toggle', 'label' => 'Nascondi senza gruppo Cover orizzontale' ],
            [ 'key' => 'dot_size',           'type' => 'range',  'label' => 'Diametro cerchio (px)',  'min' => 20, 'max' => 48, 'step' => 1 ],
            [ 'key' => 'dot_gap',            'type' => 'range',  'label' => 'Distanza fra cerchi (px)', 'min' => 0, 'max' => 16, 'step' => 1 ],
            [ 'key' => 'dot_inner',          'type' => 'range',  'label' => 'Diametro pallino (px)',  'min' => 5, 'max' => 16, 'step' => 1 ],
            [ 'key' => 'border_color',       'type' => 'color',  'label' => 'Colore bordo cerchio' ],
            [ 'key' => 'dot_bg',             'type' => 'color',  'label' => 'Sfondo cerchio' ],
            [ 'key' => 'dot_color',          'type' => 'color',  'label' => 'Colore pallino' ],
            [ 'key' => 'active_glow',        'type' => 'toggle', 'label' => 'Bagliore sul pallino attivo' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $uid    = 'olo-coverdots-' . wp_unique_id();
        $size   = max( 20, min( 48, absint( $s['dot_size'] ) ) );
        $gap    = max( 0, min( 16, absint( $s['dot_gap'] ) ) );
        $inner  = max( 5, min( 16, absint( $s['dot_inner'] ) ) );
        $border = $this->safe_color_css( $s['border_color'] ) ?: 'var(--olo-color-border, rgba(128,128,128,.35))';
        $bg     = $this->safe_color_css( $s['dot_bg'] ) ?: 'transparent';
        $color  = $this->safe_color_css( $s['dot_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $glow   = ! empty( $s['active_glow'] );
        $hide   = ! empty( $s['hide_without_group'] );
        $items  = is_array( $s['items'] ) ? array_values( $s['items'] ) : [];

        // Items serializzati per il runtime (colore per-pallino + tooltip +
        // url per la modalità LINK sulle pagine senza sezioni cover).
        $items_js = [];
        foreach ( $items as $it ) {
            $items_js[] = [
                'label' => sanitize_text_field( $it['label'] ?? '' ),
                'color' => $this->safe_color_css( $it['color'] ?? '' ),
                'url'   => esc_url_raw( $it['url'] ?? '' ),
            ];
        }
        $size_mobile = max( 0, min( 48, absint( $s['dot_size_mobile'] ) ) );

        ob_start();
        ?>
        <nav id="<?php echo esc_attr( $uid ); ?>" class="olo-coverdots"
             aria-label="<?php esc_attr_e( 'Sezioni della pagina', 'olobuild' ); ?>"
             style="--cd-size:<?php echo (int) $size; ?>px;--cd-gap:<?php echo (int) $gap; ?>px;--cd-inner:<?php echo (int) $inner; ?>px;--cd-border:<?php echo esc_attr( $border ); ?>;--cd-bg:<?php echo esc_attr( $bg ); ?>;--cd-color:<?php echo esc_attr( $color ); ?>;display:none;">
        </nav>
        <style>
        .olo-coverdots{align-items:center;gap:var(--cd-gap);}
        .olo-coverdots button{width:var(--cd-size);height:var(--cd-size);border-radius:50%;border:1px solid var(--cd-border);
          background:var(--cd-bg);cursor:pointer;padding:0;display:flex;align-items:center;justify-content:center;transition:all .18s;}
        .olo-coverdots button span{width:var(--cd-inner);height:var(--cd-inner);border-radius:50%;
          background:var(--dc,var(--cd-color));display:block;opacity:.55;transition:all .18s;}
        .olo-coverdots button:hover span,.olo-coverdots button.olo-cd-on span{opacity:1;<?php if ( $glow ) : ?>box-shadow:0 0 10px var(--dc,var(--cd-color));<?php endif; ?>}
        .olo-coverdots button:focus-visible{outline:2px solid var(--dc,var(--cd-color));outline-offset:2px;}
        <?php if ( $size_mobile > 0 ) : ?>
        /* !important: le --cd-* di base stanno nello style inline del nav,
           che altrimenti vincerebbe sulla media query. */
        @media (max-width: 959px){
            #<?php echo esc_attr( $uid ); ?>{
                --cd-size:<?php echo (int) $size_mobile; ?>px !important;
                --cd-gap:2px !important;
                --cd-inner:<?php echo (int) max( 4, round( $inner * $size_mobile / max( 1, $size ) ) ); ?>px !important;
            }
        }
        <?php endif; ?>
        </style>
        <script>
        (function(){
            var nav=document.getElementById('<?php echo esc_js( $uid ); ?>');
            if(!nav)return;
            var items=<?php echo wp_json_encode( $items_js ); ?>;
            var hide=<?php echo $hide ? 'true' : 'false'; ?>;
            var group=null,btns=[],secs=[],secObs=null;
            function makeBtns(n){
                nav.innerHTML='';btns=[];
                for(var i=0;i<n;i++){
                    var it=items[i]||{};
                    var b=document.createElement('button');
                    b.type='button';
                    b.setAttribute('aria-label',it.label||('<?php echo esc_js( __( 'Sezione', 'olobuild' ) ); ?> '+(i+1)));
                    if(it.label){b.title=it.label;}
                    if(it.color){b.style.setProperty('--dc',it.color);}
                    var s=document.createElement('span');b.appendChild(s);
                    (function(idx){b.addEventListener('click',function(){jump(idx);});})(i);
                    nav.appendChild(b);btns.push(b);
                }
                nav.style.display='inline-flex';
            }
            var linkMode=false;
            function build(){
                group=document.querySelector('.olo-h-group');
                if(group){
                    var n=parseInt(group.dataset.oloCount)||0;
                    if(n<2)return false;
                    secs=[];linkMode=false;
                    if(secObs){secObs.disconnect();secObs=null;}
                    makeBtns(n);
                    mark(parseInt(group.dataset.oloActive)||0);
                    return true;
                }
                if(document.querySelector('.olo-h-marker')){
                    /* Pagina CON sezioni cover: sul desktop il gruppo nasce al load
                       (si aspetta); sotto il breakpoint di stacking (960px) le
                       sezioni restano in flusso e i pallini saltano lì. */
                    if(window.matchMedia('(min-width: 960px)').matches){
                        if(!hide){nav.style.display='inline-flex';}
                        return false;
                    }
                    secs=Array.prototype.slice.call(document.querySelectorAll('.olo-sticky-cover-h'));
                    if(secs.length<2){ if(!hide){nav.style.display='inline-flex';} return false; }
                    linkMode=false;
                    var hdr=document.querySelector('header.olo-site-header');
                    var off=(hdr?hdr.offsetHeight:0)+10;
                    secs.forEach(function(sec){sec.style.scrollMarginTop=off+'px';});
                    makeBtns(secs.length);
                    if(secObs){secObs.disconnect();}
                    secObs=new IntersectionObserver(function(entries){
                        entries.forEach(function(e){
                            if(e.isIntersecting&&e.intersectionRatio>0){mark(secs.indexOf(e.target));}
                        });
                    },{rootMargin:'-40% 0px -55% 0px'});
                    secs.forEach(function(sec){secObs.observe(sec);});
                    mark(0);
                    return true;
                }
                /* Pagina SENZA sezioni cover (schede prodotto, manuali…): se le
                   fermate hanno un URL i pallini restano nel menu come LINK —
                   il viaggio è sempre a un tocco di distanza. */
                var withUrl=items.filter(function(it){return it&&it.url;});
                if(withUrl.length>=2){
                    linkMode=true;secs=[];
                    if(secObs){secObs.disconnect();secObs=null;}
                    makeBtns(items.length);
                    return true;
                }
                if(!hide){nav.style.display='inline-flex';}
                return false;
            }
            function jump(i){
                if(group){
                    var n=btns.length;
                    var top=group.getBoundingClientRect().top+window.scrollY;
                    var stickyTop=parseInt(group.dataset.stickyTop)||0;
                    var travel=group.offsetHeight-(window.innerHeight-stickyTop);
                    var y=top+(n>1?travel*(i/(n-1)):0);
                    window.scrollTo({top:Math.round(y),behavior:'instant'});
                    return;
                }
                if(linkMode){
                    var it=items[i];
                    if(it&&it.url){window.location=it.url;}
                    return;
                }
                if(secs[i]){secs[i].scrollIntoView({behavior:'smooth',block:'start'});}
            }
            function mark(i){
                btns.forEach(function(b,k){b.classList.toggle('olo-cd-on',k===i);});
            }
            window.addEventListener('olo:hgroup',function(e){
                if(!group){btns=[];build();}
                if(e.detail&&typeof e.detail.index==='number'){mark(e.detail.index);}
            });
            /* Rotazione/resize oltre il breakpoint: il motore raggruppa, i pallini
               si riagganciano al gruppo (l'evento olo:hgroup fa il resto). */
            var mq=window.matchMedia('(min-width: 960px)');
            var onCh=function(){btns=[];group=null;setTimeout(build,150);};
            if(mq.addEventListener){mq.addEventListener('change',onCh);}
            else if(mq.addListener){mq.addListener(onCh);}
            /* Il gruppo/le sezioni nascono su DOMContentLoaded/load: riprova brevemente. */
            var tries=0;
            var t=setInterval(function(){
                tries++;
                if(build()||tries>20){clearInterval(t);}
            },250);
            if(document.readyState==='complete'){build();}
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
