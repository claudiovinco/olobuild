<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Timezone — slider ora (città base) → orari locali + stato lavoro/limite/notte.
 * Estratto dai demo OLOthemes (setupTimezone). Render == Vue (TimezoneTile.vue).
 * Stati orari precomputati lato PHP (stringa 24 char w/o/s) → JS senza '&&' né '<'/'>'.
 */
class Olobuild_Timezone_Tile extends Olobuild_Tile_Base {

    protected $type     = 'timezone';
    protected $name     = 'Timezone';
    protected $icon     = 'dashicons-clock';
    protected $category = 'interactive';
    protected $defaults = [
        'eyebrow'     => '',
        'heading'     => 'Trova un orario che funziona',
        'intro'       => '',
        'base_label'  => 'La tua ora',
        'input_value' => 14,
        'work_start'  => 9,
        'work_end'    => 18,
        'items'       => [
            [ 'city' => 'San Francisco', 'offset' => -7, 'label' => 'PDT' ],
            [ 'city' => 'London', 'offset' => 1, 'label' => 'BST' ],
            [ 'city' => 'Berlin', 'offset' => 2, 'label' => 'CEST' ],
            [ 'city' => 'Singapore', 'offset' => 8, 'label' => 'SGT' ],
        ],
        'zone_accent' => '',
        'work_color'  => '',
        'ok_color'    => '#e0a23a',
        'sleep_color' => '',
        'card_bg'     => '',
        'card_border' => '',
        'align'       => 'left',
    ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'otz-' . wp_rand( 10000, 99999 );

        $accent = $this->safe_color_css( $s['zone_accent'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $work   = $this->safe_color_css( $s['work_color'] ?? '' ) ?: $accent;
        $ok     = $this->safe_color_css( $s['ok_color'] ?? '' ) ?: '#e0a23a';
        $sleep  = $this->safe_color_css( $s['sleep_color'] ?? '' ) ?: 'var(--olo-color-text-muted, #9ca3af)';
        $cardbg = $this->safe_color_css( $s['card_bg'] ?? '' ) ?: 'var(--olo-color-surface, #ffffff)';
        $line   = $this->safe_color_css( $s['card_border'] ?? '' ) ?: 'var(--olo-color-border, #e5e7eb)';
        $center = ( ( $s['align'] ?? 'left' ) === 'center' );
        $serif  = "var(--olo-font-family-heading, 'Playfair Display',Georgia,serif)";
        $sans   = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";

        $items = is_array( $s['items'] ) ? array_values( $s['items'] ) : [];
        if ( empty( $items ) ) return '';
        $base_off = floatval( $items[0]['offset'] ?? 0 );
        $start    = intval( $s['input_value'] ?? 14 );

        // Precompute 24-hour state string (server-side; JS only indexes it).
        $ws = intval( $s['work_start'] ?? 9 );
        $we = intval( $s['work_end'] ?? 18 );
        $states = '';
        for ( $h = 0; $h < 24; $h++ ) {
            if ( $h >= $ws && $h < $we ) {
                $states .= 'w';
            } elseif ( ( $h >= $ws - 3 && $h < $ws ) || ( $h >= $we && $h < $we + 3 ) ) {
                $states .= 'o';
            } else {
                $states .= 's';
            }
        }

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist for every colour, min()/max()/count() integer column count, fixed font-stack and alignment literals; $uid is internally generated. ?>
        <style>
            .<?php echo $uid; ?>{ --tz-accent:<?php echo $accent; ?>; font-family:<?php echo $sans; ?>; <?php if ( $center ) echo 'text-align:center;'; ?> }
            .<?php echo $uid; ?> .otz-eyebrow{font-size:12px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--tz-accent);display:block;margin-bottom:10px;}
            .<?php echo $uid; ?> .otz-h{font-family:<?php echo $serif; ?>;font-size:clamp(26px,3.6vw,42px);line-height:1.12;margin:0;color:var(--olo-color-text,#111827);}
            .<?php echo $uid; ?> .otz-intro{font-size:15.5px;line-height:1.6;opacity:.8;margin:14px 0 22px;max-width:560px;<?php echo $center ? 'margin-left:auto;margin-right:auto;' : ''; ?>}
            .<?php echo $uid; ?> .otz-base{display:flex;align-items:baseline;justify-content:space-between;gap:14px;flex-wrap:wrap;}
            .<?php echo $uid; ?> .otz-base__l{font-size:12px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;opacity:.7;}
            .<?php echo $uid; ?> .otz-base__v{font-family:<?php echo $serif; ?>;font-size:26px;color:var(--tz-accent);}
            .<?php echo $uid; ?> .otz-range{-webkit-appearance:none;appearance:none;width:100%;height:6px;border-radius:99px;cursor:pointer;background:linear-gradient(to right,var(--tz-accent) var(--pct,60%),<?php echo $line; ?> var(--pct,60%));margin:10px 0 24px;}
            .<?php echo $uid; ?> .otz-range::-webkit-slider-thumb{-webkit-appearance:none;width:20px;height:20px;border-radius:50%;background:#fff;border:2px solid var(--tz-accent);box-shadow:0 1px 4px rgba(16,24,40,.3);cursor:pointer;}
            .<?php echo $uid; ?> .otz-range::-moz-range-thumb{width:20px;height:20px;border-radius:50%;background:#fff;border:2px solid var(--tz-accent);cursor:pointer;}
            .<?php echo $uid; ?> .otz-grid{display:grid;grid-template-columns:repeat(<?php echo min( 4, max( 1, count( $items ) ) ); ?>,1fr);gap:12px;}
            @media(max-width:680px){.<?php echo $uid; ?> .otz-grid{grid-template-columns:1fr 1fr;}}
            .<?php echo $uid; ?> .otz-city{background:<?php echo $cardbg; ?>;border:1px solid <?php echo $line; ?>;border-radius:12px;padding:16px;text-align:left;}
            .<?php echo $uid; ?> .otz-city__c{font-weight:600;font-size:14px;color:var(--olo-color-text,#111827);}
            .<?php echo $uid; ?> .otz-city__o{font-size:11px;opacity:.55;letter-spacing:.04em;}
            .<?php echo $uid; ?> .otz-city__t{font-family:<?php echo $serif; ?>;font-size:24px;margin-top:8px;color:var(--olo-color-text,#111827);font-variant-numeric:tabular-nums;display:flex;align-items:center;gap:8px;}
            .<?php echo $uid; ?> .otz-dot{width:9px;height:9px;border-radius:50%;background:<?php echo $sleep; ?>;flex:none;}
            .<?php echo $uid; ?> .otz-city[data-state="w"] .otz-dot{background:<?php echo $work; ?>;}
            .<?php echo $uid; ?> .otz-city[data-state="o"] .otz-dot{background:<?php echo $ok; ?>;}
            .<?php echo $uid; ?> .otz-city[data-state="s"] .otz-dot{background:<?php echo $sleep; ?>;}
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-timezone <?php echo esc_attr( $uid ); ?>" data-timezone data-baseoff="<?php echo esc_attr( $base_off ); ?>" data-states="<?php echo esc_attr( $states ); ?>">
            <?php if ( $s['eyebrow'] !== '' ) : ?><span class="otz-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></span><?php endif; ?>
            <?php if ( $s['heading'] !== '' ) : ?><h2 class="otz-h"><?php echo esc_html( $s['heading'] ); ?></h2><?php endif; ?>
            <?php if ( $s['intro'] !== '' ) : ?><p class="otz-intro"><?php echo esc_html( $s['intro'] ); ?></p><?php endif; ?>
            <div class="otz-base">
                <span class="otz-base__l"><?php echo esc_html( $s['base_label'] ?? '' ); ?> · <?php echo esc_html( $items[0]['city'] ?? '' ); ?></span>
                <span class="otz-base__v" data-tz-disp>—</span>
            </div>
            <input class="otz-range" type="range" data-tz-input min="0" max="23" step="1" value="<?php echo esc_attr( $start ); ?>" aria-label="<?php echo esc_attr( $s['base_label'] ?: 'hour' ); ?>"/>
            <div class="otz-grid">
                <?php foreach ( $items as $it ) : ?>
                    <div class="otz-city" data-tz-city data-tz-off="<?php echo esc_attr( floatval( $it['offset'] ?? 0 ) ); ?>" data-state="s">
                        <div class="otz-city__c"><?php echo esc_html( $it['city'] ?? '' ); ?></div>
                        <?php if ( ! empty( $it['label'] ) ) : ?><div class="otz-city__o"><?php echo esc_html( $it['label'] ); ?></div><?php endif; ?>
                        <div class="otz-city__t"><span class="otz-dot"></span><span data-tz-clock>—</span></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <script>
        (function(){
            var root=document.querySelector('.<?php echo esc_js( $uid ); ?>[data-timezone]'); if(!root){return;}
            var baseOff=parseFloat(root.getAttribute('data-baseoff'))||0;
            var states=root.getAttribute('data-states')||'';
            var input=root.querySelector('[data-tz-input]');
            var disp=root.querySelector('[data-tz-disp]');
            var cities=[].slice.call(root.querySelectorAll('[data-tz-city]'));
            function wrap(n){ n=Math.round(n); return ((n%24)+24)%24; }
            function hh(n){ var h=wrap(n); var x=String(h); return (x.length===1?('0'+x):x)+':00'; }
            function recalc(){
                var h=parseFloat(input.value)||0;
                if(disp){ disp.textContent=hh(h); }
                var utc=h-baseOff;
                cities.forEach(function(c){
                    var off=parseFloat(c.getAttribute('data-tz-off'))||0;
                    var local=wrap(utc+off);
                    var t=c.querySelector('[data-tz-clock]'); if(t){ t.textContent=hh(local); }
                    var st=states.charAt(local)||'s';
                    c.setAttribute('data-state', st);
                });
                var mn=0,mx=23;
                input.style.setProperty('--pct',((h-mn)/(mx-mn)*100)+'%');
            }
            if(input){ input.addEventListener('input',recalc); recalc(); }
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
