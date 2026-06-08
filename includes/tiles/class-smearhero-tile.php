<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Hero — Smear (Canvas) : hero da galleria d'artista. Sfondo caldo scuro con
 * glow radiale ambra, eyebrow uppercase a tracking largo, H1 gigante serif (Gilda
 * Display) con parola-accento in corsivo, paragrafo cream, hint in basso. La meccanica
 * firma è il "paint smear": muovendo il cursore sulla hero si depositano pennellate di
 * pigmento colorato che sfumano (palette configurabile). Runtime JS minimo, scoped al
 * proprio $uid (nessun `&&`/`||` inline). Render == Vue (SmearHeroTile.vue).
 * Estratta dal blueprint OLOthemes "Canvas — Jonah Veld".
 */
class Olo_SmearHero_Tile extends Olo_Tile_Base {

    protected $type     = 'smearhero';
    protected $name     = 'Hero — Smear (Canvas)';
    protected $icon     = 'dashicons-art';
    protected $category = 'marketing';
    protected $defaults = [
        'eyebrow_text'    => 'Painter · oil & pigment',
        'headline_text'   => 'Jonah',
        'accent_text'     => 'Veld',
        'subhead'         => 'Large-scale abstracts about colour, weather and memory. Move your cursor — leave a mark.',
        'hint_text'       => '↑ drag the colour around',
        'bg_color'        => '#1c1a17',
        'glow_color'      => 'rgba(224,177,58,0.08)',
        'eyebrow_color'   => '#e0b13a',
        'text_color'      => '#f0ebe1',
        'accent_color'    => '',
        'sub_color'       => '#f0ebe1',
        'hint_color'      => '#857c6d',
        'smear_palette'   => '#e0b13a,#cc5b3f,#5b86b8,#f0ebe1',
        'smear_enabled'   => true,
        'min_height'      => 72,

        // Spaziatura (gated): padding di base responsivo clamp(48px,9vh,90px) 30px.
        // Override attivo SOLO se pad_custom=true → no-op coi default.
        'pad_custom'      => false,
        'content_padding' => [ 'top' => 90, 'right' => 30, 'bottom' => 90, 'left' => 30 ],

        // Forma: raggio del contenitore hero (full-bleed) — default 0 = no-op.
        'container_radius' => [ 'tl' => 0, 'tr' => 0, 'br' => 0, 'bl' => 0 ],

        // KIT standard OLObuild — additivi, no-op coi default (sfondo none, ombra none, bordo 0)
        'bg'                      => [ 'type' => 'none' ],
        'shadow'                  => 'none',
        'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'oshero-' . wp_rand( 10000, 99999 );

        $bg      = $this->safe_color_css( $s['bg_color'] ) ?: '#1c1a17';
        $glow    = $this->safe_color_css( $s['glow_color'] ) ?: 'rgba(224,177,58,0.08)';
        $eyebrow = $this->safe_color_css( $s['eyebrow_color'] ) ?: 'var(--olo-color-primary, #e0b13a)';
        $txt     = $this->safe_color_css( $s['text_color'] ) ?: '#f0ebe1';
        $accent  = $this->safe_color_css( $s['accent_color'] ?? '' ) ?: 'var(--olo-color-primary, #e0b13a)';
        $sub     = $this->safe_color_css( $s['sub_color'] ) ?: '#f0ebe1';
        $hint    = $this->safe_color_css( $s['hint_color'] ) ?: '#857c6d';
        $mh      = max( 40, min( 100, intval( $s['min_height'] ) ) );
        $smear   = ! empty( $s['smear_enabled'] );

        $disp    = "var(--olo-font-family-heading, 'Gilda Display',Georgia,serif)";
        $sans    = "var(--olo-font-family, 'Mulish',-apple-system,sans-serif)";

        // ── Spaziatura (gated): default = padding responsivo invariato; override solo se pad_custom ──
        $in_pad = 'clamp(48px,9vh,90px) 30px';
        if ( ! empty( $s['pad_custom'] ) && is_array( $s['content_padding'] ?? null ) ) {
            $cp = $s['content_padding'];
            $pt = intval( $cp['top'] ?? 0 );
            $pr = intval( $cp['right'] ?? 0 );
            $pb = intval( $cp['bottom'] ?? 0 );
            $pl = intval( $cp['left'] ?? 0 );
            $in_pad = "{$pt}px {$pr}px {$pb}px {$pl}px";
        }

        // ── Forma: raggio contenitore (no-op se 0) via helper standard ──
        $radius_decl = $this->build_border_radius_css( $s['container_radius'] ?? [] );
        $radius_css  = $radius_decl !== '' ? ( 'border-radius:' . $radius_decl . ';' ) : '';

        // ── KIT standard: sfondo completo (override del bg di base SOLO se valorizzato) ──
        // Blocco background di default = colore base; se l'utente imposta il KIT "bg"
        // (solid/gradient/image/pattern/mesh/glow/crt) sostituisce l'intero blocco.
        $bg_block = 'background:' . $bg . ';';
        $bg_obj   = $s['bg'] ?? null;
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olo_CSS_Builder' ) ) {
            $bg_decl = ( new Olo_CSS_Builder() )->get_bg_inline_css( $bg_obj );
            if ( $bg_decl !== '' ) {
                $bg_block = rtrim( trim( $bg_decl ), ';' ) . ';';
            }
        }

        // ── KIT standard: ombra + bordo (sul contenitore principale .$uid) ──
        $shadow_css        = $this->build_shadow_decl( $s );
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        $kit_decl          = $border_css;
        if ( $shadow_css !== '' ) {
            $kit_decl .= 'box-shadow:' . $shadow_css . ';';
        }

        // Palette → array di colori validi per il runtime smear.
        $cols = [];
        foreach ( explode( ',', (string) $s['smear_palette'] ) as $c ) {
            $c = $this->safe_color_css( trim( $c ) );
            if ( $c ) { $cols[] = $c; }
        }
        if ( empty( $cols ) ) { $cols = [ '#e0b13a', '#cc5b3f', '#5b86b8', '#f0ebe1' ]; }
        $cols_json = wp_json_encode( array_values( $cols ) );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?>{position:relative;overflow:hidden;min-height:<?php echo $mh; ?>vh;display:flex;align-items:center;color:<?php echo $txt; ?>;font-family:<?php echo $sans; ?>;<?php echo $bg_block; ?><?php echo $radius_css; ?><?php echo $kit_decl; ?>}
            .<?php echo $uid; ?>::before{content:"";position:absolute;inset:0;z-index:1;background:radial-gradient(80% 90% at 70% 20%,<?php echo $glow; ?>,transparent 60%);pointer-events:none;}
            .<?php echo $uid; ?> .sh-smear{position:absolute;inset:0;z-index:2;overflow:hidden;pointer-events:auto;}
            .<?php echo $uid; ?> .sh-blob{position:absolute;border-radius:50%;transform:translate(-50%,-50%) scale(1);pointer-events:none;mix-blend-mode:screen;filter:blur(2px);transition:opacity .9s ease,transform .9s ease;}
            .<?php echo $uid; ?> .sh-in{position:relative;z-index:3;max-width:760px;margin:0 auto;text-align:center;padding:<?php echo $in_pad; ?>;pointer-events:none;}
            .<?php echo $uid; ?> .sh-eyebrow{display:block;margin-bottom:22px;font-family:<?php echo $sans; ?>;font-weight:700;font-size:12px;letter-spacing:.24em;text-transform:uppercase;color:<?php echo $eyebrow; ?>;}
            .<?php echo $uid; ?> .sh-h{font-family:<?php echo $disp; ?>;font-weight:400;font-size:clamp(52px,10vw,140px);line-height:.96;letter-spacing:.01em;color:<?php echo $txt; ?>;margin:0;}
            .<?php echo $uid; ?> .sh-acc{font-style:italic;color:<?php echo $accent; ?>;}
            .<?php echo $uid; ?> .sh-sub{font-size:18px;line-height:1.7;color:<?php echo $sub; ?>;max-width:460px;margin:24px auto 0;}
            .<?php echo $uid; ?> .sh-hint{position:absolute;bottom:22px;left:50%;transform:translateX(-50%);z-index:3;font-family:<?php echo $sans; ?>;font-size:12px;letter-spacing:.1em;text-transform:uppercase;color:<?php echo $hint; ?>;pointer-events:none;}
            .<?php echo $uid; ?> a:focus-visible,.<?php echo $uid; ?> button:focus-visible{outline:2px solid <?php echo $accent; ?>;outline-offset:3px;}
            <?php echo $border_hover_css; ?><?php echo $border_effect_css; ?>
        </style>
        <section class="olo-smearhero <?php echo esc_attr( $uid ); ?>">
            <?php if ( $smear ) : ?><div class="sh-smear" data-sh-zone aria-hidden="true"></div><?php endif; ?>
            <div class="sh-in">
                <?php if ( ! empty( $s['eyebrow_text'] ) ) : ?><span class="sh-eyebrow"><?php echo esc_html( $s['eyebrow_text'] ); ?></span><?php endif; ?>
                <h1 class="sh-h"><?php echo esc_html( $s['headline_text'] ); ?><?php if ( ! empty( $s['accent_text'] ) ) : ?> <em class="sh-acc"><?php echo esc_html( $s['accent_text'] ); ?></em><?php endif; ?></h1>
                <?php if ( ! empty( $s['subhead'] ) ) : ?><p class="sh-sub"><?php echo esc_html( $s['subhead'] ); ?></p><?php endif; ?>
            </div>
            <?php if ( ! empty( $s['hint_text'] ) ) : ?><span class="sh-hint"><?php echo esc_html( $s['hint_text'] ); ?></span><?php endif; ?>
        </section>
        <?php if ( $smear ) : ?>
        <script>
        (function(){
            var root=document.currentScript.previousElementSibling;
            if(!root){return;}
            var zone=root.querySelector('[data-sh-zone]');
            if(!zone){return;}
            var cols=<?php echo $cols_json; ?>;
            var fine=window.matchMedia('(pointer:fine)').matches;
            var motion=!window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if(!fine){return;}
            if(!motion){return;}
            var last=0;
            zone.addEventListener('pointermove',function(e){
                var now=Date.now();
                if(now-last<36){return;}
                last=now;
                var r=zone.getBoundingClientRect();
                var b=document.createElement('span');
                b.className='sh-blob';
                b.style.left=(e.clientX-r.left)+'px';
                b.style.top=(e.clientY-r.top)+'px';
                b.style.background=cols[Math.floor(Math.random()*cols.length)];
                var sz=24+Math.random()*40;
                b.style.width=sz+'px';
                b.style.height=sz+'px';
                zone.appendChild(b);
                setTimeout(function(){ b.style.opacity=0; b.style.transform='translate(-50%,-50%) scale(2.2)'; },20);
                setTimeout(function(){ b.remove(); },900);
            });
        })();
        </script>
        <?php endif; ?>
        <?php
        return ob_get_clean();
    }

    /**
     * Restituisce la dichiarazione box-shadow (valore, senza "box-shadow:")
     * dal setting shadow (preset sm/md/lg/xl o custom). '' se none.
     */
    private function build_shadow_decl( $s ) {
        $preset = $s['shadow'] ?? 'none';
        if ( $preset === 'none' || $preset === '' ) {
            return '';
        }
        if ( $preset === 'custom' ) {
            $h     = intval( $s['shadow_h'] ?? 0 );
            $v     = intval( $s['shadow_v'] ?? 4 );
            $blur  = max( 0, intval( $s['shadow_blur'] ?? 10 ) );
            $spread = intval( $s['shadow_spread'] ?? 0 );
            $color = $this->safe_color_css( $s['shadow_color'] ?? '' ) ?: 'rgba(0,0,0,0.15)';
            $inset = ! empty( $s['shadow_inset'] ) ? 'inset ' : '';
            return "{$inset}{$h}px {$v}px {$blur}px {$spread}px {$color}";
        }
        $map = [
            'sm' => '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
            'md' => '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
            'lg' => '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
            'xl' => '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
        ];
        return $map[ $preset ] ?? '';
    }
}
