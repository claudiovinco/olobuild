<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Mixer — "zona interattiva": seleziona swatch → preview media RGB live.
 * Estratto dai demo OLOthemes (setupMixer). Render == Vue (MixerTile.vue).
 * Blend = media RGB (no color-mix). Runtime inline scoped, senza '&&' né '<'/'>'.
 */
class Olo_Mixer_Tile extends Olo_Tile_Base {

    protected $type     = 'mixer';
    protected $name     = 'Mixer';
    protected $icon     = 'dashicons-art';
    protected $category = 'interactive';
    protected $defaults = [
        'eyebrow'     => 'Prova',
        'heading'     => 'Componi la tua tinta',
        'intro'       => '',
        'max'         => 3,
        'empty_label' => 'Tocca i campioni per fondere',
        'items'       => [
            [ 'name' => 'Ocra', 'color' => '#caa44a' ],
            [ 'name' => 'Terra', 'color' => '#9c6b4a' ],
            [ 'name' => 'Crema', 'color' => '#efe5da' ],
            [ 'name' => 'Inchiostro', 'color' => '#1a1a1a' ],
        ],
        'zone_accent' => '',
        'zone_on'     => '#ffffff',
        'card_bg'     => '',
        'card_border' => '',
        'align'       => 'left',
    ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'omx-' . wp_rand( 10000, 99999 );

        $accent = $this->safe_color_css( $s['zone_accent'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $cardbg = $this->safe_color_css( $s['card_bg'] ?? '' ) ?: 'var(--olo-color-surface-alt, #f6f7f9)';
        $cardbd = $this->safe_color_css( $s['card_border'] ?? '' ) ?: 'var(--olo-color-border, #e5e7eb)';
        $center = ( ( $s['align'] ?? 'left' ) === 'center' );
        $serif  = "var(--olo-font-family-heading, 'Playfair Display',Georgia,serif)";
        $sans   = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";
        $max    = max( 1, intval( $s['max'] ?? 3 ) );
        $empty  = (string) ( $s['empty_label'] ?? '' );

        $items = is_array( $s['items'] ) ? array_values( $s['items'] ) : [];
        if ( empty( $items ) ) return '';

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?>{ --mx-accent:<?php echo $accent; ?>; font-family:<?php echo $sans; ?>; <?php if ( $center ) echo 'text-align:center;'; ?> }
            .<?php echo $uid; ?> .omx-eyebrow{font-size:12px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--mx-accent);display:block;margin-bottom:10px;}
            .<?php echo $uid; ?> .omx-h{font-family:<?php echo $serif; ?>;font-size:clamp(26px,3.6vw,42px);line-height:1.12;margin:0;color:var(--olo-color-text,#111827);}
            .<?php echo $uid; ?> .omx-intro{font-size:15.5px;line-height:1.6;opacity:.8;margin:14px 0 0;max-width:560px;<?php echo $center ? 'margin-left:auto;margin-right:auto;' : ''; ?>}
            .<?php echo $uid; ?> .omx-panel{margin-top:26px;display:grid;grid-template-columns:1.1fr .9fr;gap:clamp(20px,3vw,40px);align-items:center;background:<?php echo $cardbg; ?>;border:1px solid <?php echo $cardbd; ?>;border-radius:16px;padding:clamp(22px,3vw,34px);text-align:left;<?php echo $center ? 'max-width:760px;margin-left:auto;margin-right:auto;' : ''; ?>}
            @media(max-width:740px){.<?php echo $uid; ?> .omx-panel{grid-template-columns:1fr;}}
            .<?php echo $uid; ?> .omx-swatches{display:flex;flex-wrap:wrap;gap:12px;}
            .<?php echo $uid; ?> .omx-sw{width:64px;height:64px;border-radius:14px;border:2px solid transparent;cursor:pointer;transition:transform .15s,border-color .15s,box-shadow .15s;position:relative;}
            .<?php echo $uid; ?> .omx-sw:hover{transform:translateY(-2px);}
            .<?php echo $uid; ?> .omx-sw.on{border-color:var(--mx-accent);box-shadow:0 0 0 3px color-mix(in srgb, var(--mx-accent) 24%, transparent);}
            .<?php echo $uid; ?> .omx-sw:focus-visible{outline:2px solid var(--mx-accent);outline-offset:3px;}
            .<?php echo $uid; ?> .omx-prevwrap{text-align:center;}
            .<?php echo $uid; ?> .omx-preview{width:100%;height:150px;border-radius:14px;border:1px solid <?php echo $cardbd; ?>;background:transparent;transition:background .35s;}
            .<?php echo $uid; ?> .omx-out{margin-top:14px;font-family:<?php echo $serif; ?>;font-size:18px;color:var(--olo-color-text,#111827);min-height:1.4em;}
        </style>
        <div class="olo-mixer <?php echo esc_attr( $uid ); ?>" data-mixer data-max="<?php echo esc_attr( $max ); ?>" data-empty="<?php echo esc_attr( $empty ); ?>">
            <?php if ( $s['eyebrow'] !== '' ) : ?><span class="omx-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></span><?php endif; ?>
            <?php if ( $s['heading'] !== '' ) : ?><h2 class="omx-h"><?php echo esc_html( $s['heading'] ); ?></h2><?php endif; ?>
            <?php if ( $s['intro'] !== '' ) : ?><p class="omx-intro"><?php echo esc_html( $s['intro'] ); ?></p><?php endif; ?>
            <div class="omx-panel">
                <div class="omx-swatches">
                    <?php foreach ( $items as $it ) :
                        $c = $this->safe_color_css( $it['color'] ?? '' ) ?: '#cccccc';
                    ?>
                        <button type="button" class="omx-sw" data-mx="<?php echo esc_attr( $c ); ?>" data-mx-name="<?php echo esc_attr( $it['name'] ?? '' ); ?>" style="background:<?php echo esc_attr( $c ); ?>" aria-label="<?php echo esc_attr( $it['name'] ?? '' ); ?>"></button>
                    <?php endforeach; ?>
                </div>
                <div class="omx-prevwrap">
                    <div class="omx-preview" data-mx-preview></div>
                    <div class="omx-out" data-mx-out><?php echo esc_html( $empty ); ?></div>
                </div>
            </div>
        </div>
        <script>
        (function(){
            var root=document.querySelector('.<?php echo $uid; ?>[data-mixer]'); if(!root){return;}
            var max=parseInt(root.getAttribute('data-max'))||3;
            var empty=root.getAttribute('data-empty')||'';
            var sw=[].slice.call(root.querySelectorAll('[data-mx]'));
            var prev=root.querySelector('[data-mx-preview]');
            var out=root.querySelector('[data-mx-out]');
            var sel=[];
            function hexToRgb(h){ h=String(h).replace('#',''); if(h.length===3){ h=h.charAt(0)+h.charAt(0)+h.charAt(1)+h.charAt(1)+h.charAt(2)+h.charAt(2); } return [parseInt(h.substr(0,2),16)||0,parseInt(h.substr(2,2),16)||0,parseInt(h.substr(4,2),16)||0]; }
            function toHex(n){ var v=Math.round(n).toString(16); return v.length===1?('0'+v):v; }
            function render(){
                sw.forEach(function(el){ el.classList.toggle('on', sel.indexOf(el)!==-1); });
                if(sel.length===0){ if(prev){prev.style.background='transparent';} if(out){out.textContent=empty;} return; }
                var r=0,g=0,b=0;
                sel.forEach(function(el){ var c=hexToRgb(el.getAttribute('data-mx')); r+=c[0]; g+=c[1]; b+=c[2]; });
                var n=sel.length;
                if(prev){ prev.style.background='#'+toHex(r/n)+toHex(g/n)+toHex(b/n); }
                if(out){ out.textContent=sel.map(function(el){ return el.getAttribute('data-mx-name')||''; }).join(' + '); }
            }
            sw.forEach(function(el){
                el.addEventListener('click',function(){
                    var idx=sel.indexOf(el);
                    if(idx===-1){ if(sel.length===max){ sel.shift(); } sel.push(el); }
                    else { sel.splice(idx,1); }
                    render();
                });
            });
            render();
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
