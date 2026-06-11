<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Scaler — input base (porzioni/peso) → quantità scalate live.
 * mode 'scale': qty × (val/base). mode 'percent': val × (qty/100) (baker's %).
 * Render == Vue (ScalerTile.vue). Runtime inline scoped, senza '&&' né '<'/'>'.
 */
class Olo_Scaler_Tile extends Olo_Tile_Base {

    protected $type     = 'scaler';
    protected $name     = 'Scaler';
    protected $icon     = 'dashicons-calculator';
    protected $category = 'interactive';
    protected $defaults = [
        'eyebrow'     => '',
        'heading'     => 'Scala la ricetta',
        'intro'       => '',
        'mode'        => 'scale',
        'base_label'  => 'Porzioni',
        'base_value'  => 4,
        'base_min'    => 1,
        'base_max'    => 12,
        'base_step'   => 1,
        'base_suffix' => '',
        'items'       => [
            [ 'name' => 'Ingrediente A', 'amount' => 200, 'unit' => 'g' ],
            [ 'name' => 'Ingrediente B', 'amount' => 2, 'unit' => '' ],
            [ 'name' => 'Ingrediente C', 'amount' => 50, 'unit' => 'ml' ],
        ],
        'show_total'  => false,
        'total_label' => 'Totale',
        'total_unit'  => 'g',
        'zone_accent' => '',
        'card_bg'     => '',
        'card_border' => '',
        'align'       => 'left',
    ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'osl-' . wp_rand( 10000, 99999 );

        $accent = $this->safe_color_css( $s['zone_accent'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $cardbg = $this->safe_color_css( $s['card_bg'] ?? '' ) ?: 'var(--olo-color-surface-alt, #f6f7f9)';
        $line   = $this->safe_color_css( $s['card_border'] ?? '' ) ?: 'var(--olo-color-border, #e5e7eb)';
        $center = ( ( $s['align'] ?? 'left' ) === 'center' );
        $serif  = "var(--olo-font-family-heading, 'Playfair Display',Georgia,serif)";
        $sans   = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";
        $mode   = ( $s['mode'] ?? 'scale' ) === 'percent' ? 'percent' : 'scale';
        $base   = floatval( $s['base_value'] ?? 4 ) ?: 1;
        $bmin   = floatval( $s['base_min'] ?? 1 );
        $bmax   = floatval( $s['base_max'] ?? 12 );
        $bstep  = floatval( $s['base_step'] ?? 1 ) ?: 1;
        $bsuf   = (string) ( $s['base_suffix'] ?? '' );

        $items = is_array( $s['items'] ) ? array_values( $s['items'] ) : [];
        if ( empty( $items ) ) return '';

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist for every colour, fixed font-stack and alignment literals; $uid is internally generated. ?>
        <style>
            .<?php echo $uid; ?>{ --sl-accent:<?php echo $accent; ?>; font-family:<?php echo $sans; ?>; <?php if ( $center ) echo 'text-align:center;'; ?> }
            .<?php echo $uid; ?> .osl-eyebrow{font-size:12px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--sl-accent);display:block;margin-bottom:10px;}
            .<?php echo $uid; ?> .osl-h{font-family:<?php echo $serif; ?>;font-size:clamp(26px,3.6vw,42px);line-height:1.12;margin:0;color:var(--olo-color-text,#111827);}
            .<?php echo $uid; ?> .osl-intro{font-size:15.5px;line-height:1.6;opacity:.8;margin:14px 0 0;max-width:560px;<?php echo $center ? 'margin-left:auto;margin-right:auto;' : ''; ?>}
            .<?php echo $uid; ?> .osl-panel{margin-top:26px;background:<?php echo $cardbg; ?>;border:1px solid <?php echo $line; ?>;border-radius:16px;padding:clamp(22px,3vw,34px);text-align:left;<?php echo $center ? 'max-width:620px;margin-left:auto;margin-right:auto;' : ''; ?>}
            .<?php echo $uid; ?> .osl-base{display:flex;align-items:baseline;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:6px;}
            .<?php echo $uid; ?> .osl-base__l{font-size:12px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;opacity:.7;}
            .<?php echo $uid; ?> .osl-base__v{font-family:<?php echo $serif; ?>;font-size:26px;color:var(--sl-accent);}
            .<?php echo $uid; ?> .osl-range{-webkit-appearance:none;appearance:none;width:100%;height:6px;border-radius:99px;cursor:pointer;background:linear-gradient(to right,var(--sl-accent) var(--pct,40%),<?php echo $line; ?> var(--pct,40%));margin:8px 0 22px;}
            .<?php echo $uid; ?> .osl-range::-webkit-slider-thumb{-webkit-appearance:none;width:20px;height:20px;border-radius:50%;background:#fff;border:2px solid var(--sl-accent);box-shadow:0 1px 4px rgba(16,24,40,.3);cursor:pointer;}
            .<?php echo $uid; ?> .osl-range::-moz-range-thumb{width:20px;height:20px;border-radius:50%;background:#fff;border:2px solid var(--sl-accent);cursor:pointer;}
            .<?php echo $uid; ?> .osl-row{display:flex;align-items:baseline;justify-content:space-between;gap:14px;padding:12px 0;border-top:1px solid <?php echo $line; ?>;}
            .<?php echo $uid; ?> .osl-row__n{font-size:15px;color:var(--olo-color-text,#111827);}
            .<?php echo $uid; ?> .osl-row__v{font-weight:700;font-variant-numeric:tabular-nums;color:var(--olo-color-text,#111827);white-space:nowrap;}
            .<?php echo $uid; ?> .osl-row__v u{font-weight:400;text-decoration:none;opacity:.6;font-size:13px;margin-left:3px;}
            .<?php echo $uid; ?> .osl-tot{display:flex;align-items:baseline;justify-content:space-between;gap:14px;margin-top:16px;padding-top:14px;border-top:2px solid <?php echo $line; ?>;}
            .<?php echo $uid; ?> .osl-tot b{font-family:<?php echo $serif; ?>;font-size:24px;color:var(--sl-accent);}
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-scaler <?php echo esc_attr( $uid ); ?>" data-scaler data-mode="<?php echo esc_attr( $mode ); ?>" data-base="<?php echo esc_attr( $base ); ?>">
            <?php if ( $s['eyebrow'] !== '' ) : ?><span class="osl-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></span><?php endif; ?>
            <?php if ( $s['heading'] !== '' ) : ?><h2 class="osl-h"><?php echo esc_html( $s['heading'] ); ?></h2><?php endif; ?>
            <?php if ( $s['intro'] !== '' ) : ?><p class="osl-intro"><?php echo esc_html( $s['intro'] ); ?></p><?php endif; ?>
            <div class="osl-panel">
                <div class="osl-base">
                    <span class="osl-base__l"><?php echo esc_html( $s['base_label'] ?? '' ); ?></span>
                    <span class="osl-base__v"><span data-sc-disp><?php echo esc_html( $base ); ?></span><?php echo $bsuf !== '' ? ' ' . esc_html( $bsuf ) : ''; ?></span>
                </div>
                <input class="osl-range" type="range" data-sc-input min="<?php echo esc_attr( $bmin ); ?>" max="<?php echo esc_attr( $bmax ); ?>" step="<?php echo esc_attr( $bstep ); ?>" value="<?php echo esc_attr( $base ); ?>" aria-label="<?php echo esc_attr( $s['base_label'] ?: 'base' ); ?>"/>
                <?php foreach ( $items as $it ) :
                    $amt = floatval( $it['amount'] ?? 0 );
                ?>
                    <div class="osl-row" data-sc-row data-amount="<?php echo esc_attr( $amt ); ?>">
                        <span class="osl-row__n"><?php echo esc_html( $it['name'] ?? '' ); ?></span>
                        <span class="osl-row__v"><span data-sc-out>—</span><?php if ( ! empty( $it['unit'] ) ) : ?><u><?php echo esc_html( $it['unit'] ); ?></u><?php endif; ?></span>
                    </div>
                <?php endforeach; ?>
                <?php if ( ! empty( $s['show_total'] ) ) : ?>
                    <div class="osl-tot">
                        <span class="osl-base__l"><?php echo esc_html( $s['total_label'] ?? 'Totale' ); ?></span>
                        <b><span data-sc-total>—</span> <?php echo esc_html( $s['total_unit'] ?? '' ); ?></b>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <script>
        (function(){
            var root=document.querySelector('.<?php echo esc_js( $uid ); ?>[data-scaler]'); if(!root){return;}
            var mode=root.getAttribute('data-mode')||'scale';
            var base=parseFloat(root.getAttribute('data-base'))||1;
            var input=root.querySelector('[data-sc-input]');
            var disp=root.querySelector('[data-sc-disp]');
            var rows=[].slice.call(root.querySelectorAll('[data-sc-row]'));
            var totalEl=root.querySelector('[data-sc-total]');
            function fmt(n){ var r=Math.round(n*10)/10; return new Intl.NumberFormat('en-US',{maximumFractionDigits:1}).format(r); }
            function recalc(){
                var cur=parseFloat(input.value)||0;
                if(disp){ disp.textContent=cur; }
                var total=0;
                rows.forEach(function(r){
                    var a=parseFloat(r.getAttribute('data-amount'))||0;
                    var v= mode==='percent' ? (cur*a/100) : (a*(base===0?0:(cur/base)));
                    var out=r.querySelector('[data-sc-out]'); if(out){ out.textContent=fmt(v); }
                    total+=v;
                });
                if(totalEl){ totalEl.textContent=fmt(Math.round(total)); }
                var mn=parseFloat(input.min)||0,mx=parseFloat(input.max)||100;
                input.style.setProperty('--pct',(mx===mn?0:((cur-mn)/(mx-mn)*100))+'%');
            }
            if(input){ input.addEventListener('input',recalc); recalc(); }
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
