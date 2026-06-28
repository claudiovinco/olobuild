<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Projector — "zona interattiva" (slider → valore calcolato live).
 * Estratta dai demo OLOthemes (setupProjector). Token-first: un solo controllo
 * colore `zone_accent`; on/line/soft/thumb derivati. Render == Vue (ProjectorTile.vue).
 */
class Olobuild_Projector_Tile extends Olobuild_Tile_Base {

    protected $type     = 'projector';
    protected $name     = 'Projector';
    protected $icon     = 'dashicons-chart-line';
    protected $category = 'interactive';
    protected $defaults = [
        'eyebrow'      => 'Una stima',
        'heading'      => 'La pazienza <em>compone</em>',
        'intro'        => 'Imposta quanto metti da parte ogni anno.',
        'min'          => '2000',
        'max'          => '50000',
        'step'         => '1000',
        'value'        => '12000',
        'rate'         => '0.06',
        'years'        => '20',
        'currency'     => '€',
        'input_label'  => 'Investito ogni anno',
        'out_caption'  => 'Proiezione finale',
        'note'         => 'Solo illustrativo. Capitale a rischio.',
        'show_contrib' => true,
        'zone_accent'  => '',
        'align'        => 'left',
        'tile_padding' => [ 'top' => 48, 'right' => 48, 'bottom' => 48, 'left' => 48 ],
        'border_radius'=> '16',
        'shadow'       => 'sm',
        'border'       => [],
        'border_hover' => [],
        'border_hover_duration' => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() {
        // L'inspector è guidato dal config JS (src/config/elements/projector.js).
        return [];
    }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'opj-' . wp_rand( 10000, 99999 );

        $accent = $this->safe_color_css( $s['zone_accent'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $center = ( ( $s['align'] ?? 'left' ) === 'center' );

        // box-model
        $tp = is_array( $s['tile_padding'] ?? null ) ? $s['tile_padding'] : [];
        $pad = intval( $tp['top'] ?? 48 ) . 'px ' . intval( $tp['right'] ?? 48 ) . 'px ' . intval( $tp['bottom'] ?? 48 ) . 'px ' . intval( $tp['left'] ?? 48 ) . 'px';
        $br_val = $this->build_border_radius_css( $s['border_radius'] ?? null );
        $radius_css = $br_val ? 'border-radius:' . $br_val . ';' : '';
        $shadow_val = Olobuild_Tile_Utils::shadow_value( $s, 'shadow' );
        $shadow_css = ( $shadow_val && $shadow_val !== 'none' ) ? 'box-shadow:' . $shadow_val . ';' : '';

        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );

        // data per slider/calcolo
        $min  = is_numeric( $s['min'] ) ? $s['min'] : '0';
        $max  = is_numeric( $s['max'] ) ? $s['max'] : '100';
        $step = is_numeric( $s['step'] ) ? $s['step'] : '1';
        $val  = is_numeric( $s['value'] ) ? $s['value'] : $min;
        $rate = $s['rate'];
        $years= $s['years'];
        $cur  = $s['currency'];

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: $accent via the safe_color_css() whitelist (or fixed var() fallback), $pad from intval()'d sides, radius/shadow via build_border_radius_css()/Olobuild_Tile_Utils::shadow_value(), border via build_border_css(); $uid is internally generated. ?>
        <style>
            .<?php echo $uid; ?>{
                --pj-accent: <?php echo $accent; ?>;
                --pj-line: var(--olo-color-border, #e5e7eb);
                --pj-surface: var(--olo-color-surface, #ffffff);
                display:grid; grid-template-columns:1.15fr .85fr; gap:clamp(28px,4vw,64px); align-items:center;
                color:var(--olo-color-text, #1f2937);
                background:var(--olo-color-surface-alt, #f6f7f9);
                border:1px solid var(--olo-color-border, #e5e7eb);
                padding:<?php echo $pad; ?>; <?php echo $radius_css . $shadow_css; ?>
                <?php if ( $border_css ) echo $border_css; ?>
            }
            <?php if ( $center ) : ?>.<?php echo $uid; ?>{text-align:center;}<?php endif; ?>
            @media(max-width:820px){.<?php echo $uid; ?>{grid-template-columns:1fr;}}
            .<?php echo $uid; ?> .opj-eyebrow{font-size:12px;font-weight:600;letter-spacing:.18em;text-transform:uppercase;color:var(--pj-accent);}
            .<?php echo $uid; ?> .opj-h{font-size:clamp(28px,4vw,46px);line-height:1.1;margin:14px 0 0;color:var(--olo-color-text,#1f2937);}
            .<?php echo $uid; ?> .opj-h em{font-style:italic;color:var(--pj-accent);}
            .<?php echo $uid; ?> .opj-intro{font-size:15.5px;line-height:1.6;opacity:.85;margin:14px 0 26px;max-width:460px;}
            .<?php echo $uid; ?> .opj-inlabel{font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;opacity:.7;margin-bottom:12px;}
            .<?php echo $uid; ?> .opj-range{-webkit-appearance:none;appearance:none;width:100%;height:6px;border-radius:99px;cursor:pointer;background:linear-gradient(to right,var(--pj-accent) var(--pct,50%),var(--pj-line) var(--pct,50%));}
            .<?php echo $uid; ?> .opj-range::-webkit-slider-thumb{-webkit-appearance:none;width:20px;height:20px;border-radius:50%;background:var(--pj-surface);border:2px solid var(--pj-accent);box-shadow:0 1px 4px rgba(16,24,40,.3);cursor:pointer;}
            .<?php echo $uid; ?> .opj-range::-moz-range-thumb{width:20px;height:20px;border-radius:50%;background:var(--pj-surface);border:2px solid var(--pj-accent);cursor:pointer;}
            .<?php echo $uid; ?> .opj-range:focus-visible{outline:2px solid var(--pj-accent);outline-offset:4px;}
            .<?php echo $uid; ?> .opj-contrib{font-size:26px;font-weight:600;margin-top:14px;color:var(--olo-color-text,#1f2937);}
            .<?php echo $uid; ?> .opj-r{text-align:center;border-left:1px solid var(--pj-line);padding-left:clamp(18px,4vw,48px);}
            <?php if ( $center ) : ?>.<?php echo $uid; ?> .opj-r{border-left:0;}<?php endif; ?>
            @media(max-width:820px){.<?php echo $uid; ?> .opj-r{border-left:0;border-top:1px solid var(--pj-line);padding-left:0;padding-top:28px;}}
            .<?php echo $uid; ?> .opj-caption{font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;opacity:.7;}
            .<?php echo $uid; ?> .opj-out{font-size:clamp(36px,5vw,60px);font-weight:700;line-height:1.05;color:var(--pj-accent);margin:10px 0 14px;font-variant-numeric:tabular-nums;letter-spacing:-.01em;}
            .<?php echo $uid; ?> .opj-note{font-size:11.5px;line-height:1.5;opacity:.6;max-width:300px;margin:0 auto;}
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php if ( $border_hover_css || $border_effect_css ) : ?><style><?php echo $border_hover_css . $border_effect_css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings ?></style><?php endif; ?>
        <div class="olo-projector <?php echo esc_attr( $uid ); ?>" data-project data-rate="<?php echo esc_attr( $rate ); ?>" data-years="<?php echo esc_attr( $years ); ?>" data-currency="<?php echo esc_attr( $cur ); ?>">
            <div class="opj-l">
                <?php if ( $s['eyebrow'] !== '' ) : ?><span class="opj-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></span><?php endif; ?>
                <?php if ( $s['heading'] !== '' ) : ?><h2 class="opj-h"><?php echo wp_kses_post( $s['heading'] ); ?></h2><?php endif; ?>
                <?php if ( $s['intro'] !== '' ) : ?><p class="opj-intro"><?php echo esc_html( $s['intro'] ); ?></p><?php endif; ?>
                <?php if ( $s['input_label'] !== '' ) : ?><div class="opj-inlabel"><?php echo esc_html( $s['input_label'] ); ?></div><?php endif; ?>
                <input class="opj-range" type="range" data-project-input min="<?php echo esc_attr( $min ); ?>" max="<?php echo esc_attr( $max ); ?>" step="<?php echo esc_attr( $step ); ?>" value="<?php echo esc_attr( $val ); ?>" aria-label="<?php echo esc_attr( $s['input_label'] ?: 'slider' ); ?>"/>
                <?php if ( ! empty( $s['show_contrib'] ) ) : ?><div class="opj-contrib"><span data-project-contrib></span></div><?php endif; ?>
            </div>
            <div class="opj-r">
                <?php if ( $s['out_caption'] !== '' ) : ?><div class="opj-caption"><?php echo esc_html( $s['out_caption'] ); ?></div><?php endif; ?>
                <div class="opj-out" data-project-out aria-live="polite">—</div>
                <?php if ( $s['note'] !== '' ) : ?><div class="opj-note"><?php echo esc_html( $s['note'] ); ?></div><?php endif; ?>
            </div>
        </div>
        <script>
        (function(){
            var p=document.querySelector('.<?php echo esc_js( $uid ); ?>[data-project]'); if(!p){return;}
            var input=p.querySelector('[data-project-input]'),out=p.querySelector('[data-project-out]'),cc=p.querySelector('[data-project-contrib]');
            var ra=p.getAttribute('data-rate'),ya=p.getAttribute('data-years'),ca=p.getAttribute('data-currency');
            var rate=(ra==null||ra==='')?0:parseFloat(ra),years=(ya==null||ya==='')?1:parseFloat(ya),cur=(ca==null)?'':ca;
            var fmt=new Intl.NumberFormat('en-US',{maximumFractionDigits:0});
            function render(){
                var c=parseFloat(input.value)||0;
                var fv=rate===0?c*years:c*(Math.pow(1+rate,years)-1)/rate;
                if(out){out.textContent=cur+fmt.format(Math.round(fv));}
                if(cc){cc.textContent=cur+fmt.format(c);}
                var mn=parseFloat(input.min)||0,mx=parseFloat(input.max)||100;
                input.style.setProperty('--pct',(mx===mn?0:(input.value-mn)/(mx-mn)*100)+'%');
            }
            if(input){input.addEventListener('input',render);render();}
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
