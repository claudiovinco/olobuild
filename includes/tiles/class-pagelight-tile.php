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
        'position'      => 'center',
        'size'          => 90,
        'intensity'     => 26,
        'transition_ms' => 800,
    ];

    public function get_controls() {
        return [
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
        /* Il wrapper del template full-bleed usa transform/container: creerebbe
           un containing block che "sgancia" il layer fixed dalla viewport. */
        .olo-template:has(.olo-pagelight){transform:none;container:none;left:auto;margin-left:0;width:100%;}
        <?php if ( $base ) : ?>
        #<?php echo esc_attr( $uid ); ?>::before{content:'';position:absolute;inset:0;background:<?php echo esc_html( $base ); ?>;}
        <?php endif; ?>
        #<?php echo esc_attr( $uid ); ?>::after{content:'';position:absolute;inset:0;
          background-color:<?php echo esc_html( $light ); ?>;
          -webkit-mask-image:<?php echo esc_html( $mask_css ); ?>;
          mask-image:<?php echo esc_html( $mask_css ); ?>;
          transition:background-color <?php echo (int) $ms; ?>ms ease;}
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
            function apply(c){ if(c){ el.style.setProperty('--pl', c); } }
            /* Sezioni verticali con data-olo-light */
            var lit=Array.prototype.slice.call(document.querySelectorAll('section[data-olo-light]'));
            if(lit.length){
                var io=new IntersectionObserver(function(entries){
                    entries.forEach(function(e){
                        /* Le sezioni nei gruppi Cover orizzontale le governa
                           l'evento olo:hgroup: qui solo le pagine verticali.
                           intersectionRatio>0 esclude i tocchi di bordo. */
                        if(e.isIntersecting&&e.intersectionRatio>0&&!e.target.closest('.olo-h-group')){
                            apply(e.target.getAttribute('data-olo-light'));
                        }
                    });
                },{rootMargin:'-40% 0px -40% 0px'});
                lit.forEach(function(sec){ io.observe(sec); });
            }
            /* Gruppi Cover orizzontale: il motore annuncia la fermata attiva */
            window.addEventListener('olo:hgroup',function(ev){
                var d=ev.detail||{};
                if(!d.group)return;
                var track=d.group.querySelector('.olo-h-track');
                if(!track)return;
                var sec=track.children[d.index];
                if(sec){
                    var c=sec.getAttribute('data-olo-light');
                    if(c){ apply(c); }
                }
            });
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
