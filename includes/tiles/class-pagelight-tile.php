<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Luce di pagina — atmosfera: due layer fissi dietro al contenuto (fondo
 * opzionale + alone radiale mascherato). Il colore dell'alone segue la
 * sezione visibile: sezioni con data-olo-light (campo "Colore luce") via
 * IntersectionObserver, gruppi "Cover orizzontale" via evento olo:hgroup.
 * Il cross-fade è una transition CSS su background-color. I layer vengono
 * spostati in cima a document.body (i wrapper del template creerebbero un
 * containing block e uno stacking order sbagliato).
 */
class Olobuild_Pagelight_Tile extends Olobuild_Tile_Base {

    protected $type     = 'pagelight';
    protected $name     = 'Luce di pagina';
    protected $icon     = 'dashicons-lightbulb';
    protected $category = 'atmosphere';
    protected $defaults = [
        'light_color'   => '',
        'base_color'    => '',
        'forma'         => 'alone',
        'position'      => 'center',
        'size'          => 90,
        'intensity'     => 26,
        'transition_ms' => 800,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'forma',         'type' => 'select', 'label' => 'Forma della luce', 'options' => [ 'alone' => 'Alone', 'tinta' => 'Tutta la pagina' ] ],
            [ 'key' => 'light_color',   'type' => 'color',  'label' => 'Colore luce di partenza' ],
            [ 'key' => 'base_color',    'type' => 'color',  'label' => 'Fondo pagina (opzionale)' ],
            [ 'key' => 'position',      'type' => 'select', 'label' => 'Posizione alone', 'options' => [ 'center' => 'Centro', 'top' => 'Alto', 'top-left' => 'Alto a sinistra', 'top-right' => 'Alto a destra', 'spread' => 'Diffusa' ] ],
            [ 'key' => 'size',          'type' => 'range',  'label' => 'Ampiezza alone (%)', 'min' => 40, 'max' => 140, 'step' => 5 ],
            [ 'key' => 'intensity',     'type' => 'range',  'label' => 'Intensità (%)', 'min' => 5, 'max' => 70, 'step' => 1 ],
            [ 'key' => 'transition_ms', 'type' => 'range',  'label' => 'Velocità transizione (ms)', 'min' => 100, 'max' => 2500, 'step' => 100 ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $uid       = 'olo-pagelight-' . wp_unique_id();
        $light     = $this->safe_color_css( $s['light_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $base      = $this->safe_color_css( $s['base_color'] );
        $size      = max( 40, min( 140, absint( $s['size'] ) ) );
        $intensity = max( 5, min( 70, absint( $s['intensity'] ) ) ) / 100;
        $ms        = max( 100, min( 2500, absint( $s['transition_ms'] ) ) );
        // «Tutta la pagina» (1.4.957): il colore della sezione in vista riempie tutto il fondo e, se la sezione lo dice
        // («Colore testo con la luce»), il testo cambia con lui: i token del fondo e del testo dei template della pagina
        // (testata e piè di pagina compresi) seguono la sezione, con la stessa transizione.
        $tinta     = 'tinta' === ( $s['forma'] ?? 'alone' );

        // Hotspot per preset (x%, y%, scala ampiezza) — stessi punti del bg glow.
        $spots_map = [
            'center'    => [ [ 50, 42, 1.1 ] ],
            'top'       => [ [ 50, -8, 1.25 ] ],
            'top-left'  => [ [ 8, 4, 1.2 ] ],
            'top-right' => [ [ 92, 6, 1.2 ] ],
            'spread'    => [ [ 12, 6, 1.15 ], [ 88, 30, 0.9 ] ],
        ];
        $spots = $spots_map[ $s['position'] ] ?? $spots_map['center'];

        // La maschera disegna la forma dell'alone; il COLORE è un semplice
        // background-color con transition → il cross-fade lo fa il browser.
        $masks = [];
        foreach ( $spots as $sp ) {
            $stop    = (int) round( $size * $sp[2] );
            $core    = 'rgba(0,0,0,' . $intensity . ')';
            $mid     = 'rgba(0,0,0,' . round( $intensity * 0.45, 3 ) . ')';
            $midpos  = (int) round( $stop * 0.42 );
            $masks[] = "radial-gradient(circle at {$sp[0]}% {$sp[1]}%, {$core} 0%, {$mid} {$midpos}%, transparent {$stop}%)";
        }
        $mask_css = implode( ', ', $masks );

        ob_start();
        ?>
        <div id="<?php echo esc_attr( $uid ); ?>" class="olo-pagelight" aria-hidden="true"></div>
        <style>
        /* z-index NEGATIVO dentro uno stacking context forzato sul grid: il
           layer si dipinge sopra lo sfondo del grid ma sotto TUTTO il suo
           contenuto (anche quello statico — nel canvas del builder i wrapper
           non hanno z-index e un layer a z:0 coprirebbe i testi). */
        #<?php echo esc_attr( $uid ); ?>{position:fixed;inset:0;pointer-events:none;z-index:-1;}
        .olo-frontend-grid:has(> .olo-pagelight){position:relative;z-index:0;}
        /* Il wrapper del template full-bleed usa transform: creerebbe un containing
           block che "sgancia" il layer fixed dalla viewport. Il container (olo-tpl)
           invece resta: container-type inline-size non aggancia i fixed (misurato), e
           toglierlo spegneva ogni @container della pagina, come «Impila su mobile». */
        .olo-template:has(.olo-pagelight){transform:none;left:auto;margin-left:0;width:100%;}
        <?php if ( $base ) : ?>
        #<?php echo esc_attr( $uid ); ?>::before{content:'';position:absolute;inset:0;background:<?php echo esc_html( $base ); ?>;}
        <?php endif; ?>
        #<?php echo esc_attr( $uid ); ?>::after{content:'';position:absolute;inset:0;
          background-color:<?php echo esc_html( $light ); ?>;
          <?php if ( ! $tinta ) : ?>
          -webkit-mask-image:<?php echo esc_html( $mask_css ); ?>;
          mask-image:<?php echo esc_html( $mask_css ); ?>;
          <?php endif; ?>
          transition:background-color <?php echo (int) $ms; ?>ms ease;}
        <?php if ( $tinta ) : ?>
        @property --olo-color-background{syntax:'<color>';inherits:true;initial-value:#ffffff}
        @property --olo-color-text{syntax:'<color>';inherits:true;initial-value:#111111}
        @property --olo-color-text-muted{syntax:'<color>';inherits:true;initial-value:#555555}
        @property --olo-color-border{syntax:'<color>';inherits:true;initial-value:#dddddd}
        @property --olo-color-link{syntax:'<color>';inherits:true;initial-value:#111111}
        .olo-template.olo-pl-tinta{transition:--olo-color-background <?php echo (int) $ms; ?>ms ease,--olo-color-text <?php echo (int) $ms; ?>ms ease,--olo-color-text-muted <?php echo (int) $ms; ?>ms ease,--olo-color-border <?php echo (int) $ms; ?>ms ease,--olo-color-link <?php echo (int) $ms; ?>ms ease}
        @media (prefers-reduced-motion: reduce){.olo-template.olo-pl-tinta,#<?php echo esc_attr( $uid ); ?>::after{transition:none}}
        <?php endif; ?>
        </style>
        <script>
        (function(){
            var el=document.getElementById('<?php echo esc_js( $uid ); ?>');
            if(!el)return;
            /* Portal: primo figlio del grid del template — sopra lo sfondo di
               pagina (dipinto prima dei figli), sotto tutto il contenuto che
               segue nel DOM. A inizio body verrebbe coperto dai wrapper
               posizionati che portano lo sfondo globale. */
            var grid=el.closest('.olo-frontend-grid')||document.body;
            if(grid.firstChild!==el){grid.insertBefore(el,grid.firstChild);}
            /* Il colore dell'::after si pilota con una custom property. */
            var css=document.createElement('style');
            css.textContent='#<?php echo esc_js( $uid ); ?>::after{background-color:var(--pl,<?php echo esc_js( $light ); ?>) !important;}';
            document.head.appendChild(css);
            var tinta=<?php echo $tinta ? 'true' : 'false'; ?>;
            /* Un colore scritto come token si risolve prima: dentro i token che cambia, var(--olo-color-…) girerebbe su se stesso. */
            function vero(c){ if(!c){ return ''; } var p=document.createElement('i'); p.style.color=c; el.appendChild(p); var v=getComputedStyle(p).color; el.removeChild(p); return v; }
            function apply(c, txt){
                if(!c){ return; }
                el.style.setProperty('--pl', c);
                if(!tinta){ return; }
                var fondo=vero(c), testo=vero(txt);
                Array.prototype.forEach.call(document.querySelectorAll('.olo-template'), function(t){
                    t.classList.add('olo-pl-tinta');
                    t.style.setProperty('--olo-color-background', fondo);
                    if(testo){
                        t.style.setProperty('--olo-color-text', testo);
                        t.style.setProperty('--olo-color-text-muted', 'color-mix(in srgb, '+testo+' 84%, '+fondo+')');
                        t.style.setProperty('--olo-color-link', testo);
                        t.style.setProperty('--olo-color-border', 'color-mix(in srgb, '+testo+' 22%, '+fondo+')');
                    } else {
                        ['--olo-color-text','--olo-color-text-muted','--olo-color-border','--olo-color-link'].forEach(function(k){ t.style.removeProperty(k); });
                    }
                });
            }
            /* Sezioni verticali con data-olo-light. Si cercano a pagina caricata: lo
               script gira mentre il browser legge la sezione della tile, e prima
               trovava solo quella (le sezioni dopo non erano ancora nel DOM) — la
               luce non cambiava mai. */
            function osserva(){
                var lit=Array.prototype.slice.call(document.querySelectorAll('section[data-olo-light]'));
                if(!lit.length){ return; }
                /* Vince la sezione che sta sotto la linea di metà schermo: con la sola fascia centrale due sezioni la
                   toccavano insieme (una finisce, l'altra comincia) e vinceva l'ultima letta, a volte quella sbagliata.
                   Le sezioni nei gruppi Cover orizzontale le governa l'evento olo:hgroup: qui solo le pagine verticali. */
                var ultima=null, giro=0;
                function scegli(){
                    giro=0;
                    var c=window.innerHeight/2, scelta=null;
                    lit.forEach(function(sec){
                        var r=sec.getBoundingClientRect();
                        if(r.top<=c){ if(r.bottom>c){ scelta=sec; } }
                    });
                    if(scelta){ if(scelta!==ultima){ if(!scelta.closest('.olo-h-group')){ ultima=scelta; apply(scelta.getAttribute('data-olo-light'), scelta.getAttribute('data-olo-light-text')); } } }
                }
                var io=new IntersectionObserver(scegli,{rootMargin:'-40% 0px -40% 0px'});
                window.addEventListener('scroll',function(){ if(!giro){ giro=requestAnimationFrame(scegli); } },{passive:true});
                lit.forEach(function(sec){ io.observe(sec); });
            }
            if(document.readyState==='loading'){ document.addEventListener('DOMContentLoaded', osserva); } else { osserva(); }
            /* Gruppi Cover orizzontale: il motore annuncia la fermata attiva */
            window.addEventListener('olo:hgroup',function(ev){
                var d=ev.detail||{};
                if(!d.group)return;
                var track=d.group.querySelector('.olo-h-track');
                if(!track)return;
                var sec=track.children[d.index];
                if(sec){
                    var c=sec.getAttribute('data-olo-light');
                    if(c){ apply(c, sec.getAttribute('data-olo-light-text')); }
                }
            });
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
