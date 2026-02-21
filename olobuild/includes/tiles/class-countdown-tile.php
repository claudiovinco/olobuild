<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Countdown_Tile extends Olo_Tile_Base {

    protected $type     = 'countdown';
    protected $name     = 'Conto alla rovescia';
    protected $icon     = 'dashicons-clock';
    protected $category = 'content';
    protected $defaults = [
        'countdown_style'     => 'custom',
        'target_date'         => '2026-12-31T23:59',
        'show_days'           => true,
        'show_hours'          => true,
        'show_minutes'        => true,
        'show_seconds'        => true,
        'expired_message'     => 'Event has started!',
        'label_days'          => 'Days',
        'label_hours'         => 'Hours',
        'label_minutes'       => 'Minutes',
        'label_seconds'       => 'Seconds',
        'separator'           => ':',
        'bg_color'            => '#111827',
        'text_color'          => '#F3F4F6',
        'accent_color'        => '#6366F1',
        'number_font_size'    => '48',
        'number_font_weight'  => '700',
        'label_font_size'     => '12',
        'label_font_weight'   => '500',
        'separator_font_size' => '32',
        'item_min_width'      => '70',
        'padding'             => '32',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'target_date',     'type' => 'text',   'label' => 'Target Date (YYYY-MM-DDTHH:MM)' ],
            [ 'key' => 'show_days',       'type' => 'toggle', 'label' => 'Show Days' ],
            [ 'key' => 'show_hours',      'type' => 'toggle', 'label' => 'Show Hours' ],
            [ 'key' => 'show_minutes',    'type' => 'toggle', 'label' => 'Show Minutes' ],
            [ 'key' => 'show_seconds',    'type' => 'toggle', 'label' => 'Show Seconds' ],
            [ 'key' => 'expired_message', 'type' => 'text',   'label' => 'Expired Message' ],
            [ 'key' => 'label_days',      'type' => 'text',   'label' => 'Label Days' ],
            [ 'key' => 'label_hours',     'type' => 'text',   'label' => 'Label Hours' ],
            [ 'key' => 'label_minutes',   'type' => 'text',   'label' => 'Label Minutes' ],
            [ 'key' => 'label_seconds',   'type' => 'text',   'label' => 'Label Seconds' ],
            [ 'key' => 'separator',       'type' => 'text',   'label' => 'Separator' ],
            [ 'key' => 'bg_color',        'type' => 'color',  'label' => 'Background' ],
            [ 'key' => 'text_color',      'type' => 'color',  'label' => 'Text Color' ],
            [ 'key' => 'accent_color',    'type' => 'color',  'label' => 'Accent Color' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );
        if ( ( $s['countdown_style'] ?? 'custom' ) === 'uikit' ) {
            return $this->render_uikit( $s );
        }
        return $this->render_custom( $s );
    }

    private function render_custom( $s ) {
        $uid = 'mcd-' . wp_rand( 10000, 99999 );

        $num_fs  = absint( $s['number_font_size'] );
        $num_fw  = absint( $s['number_font_weight'] );
        $lbl_fs  = absint( $s['label_font_size'] );
        $lbl_fw  = absint( $s['label_font_weight'] );
        $sep_fs  = absint( $s['separator_font_size'] );
        $min_w   = absint( $s['item_min_width'] );
        $pad     = absint( $s['padding'] );
        $bg      = esc_attr( $s['bg_color'] );
        $fg      = esc_attr( $s['text_color'] );
        $accent  = esc_attr( $s['accent_color'] );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                display: flex;
                justify-content: center;
                align-items: center;
                flex-wrap: wrap;
                gap: 8px;
                padding: <?php echo $pad; ?>px;
                background: <?php echo $bg; ?>;
                color: <?php echo $fg; ?>;
            }
            .<?php echo $uid; ?> .mcd-item {
                text-align: center;
                min-width: <?php echo $min_w; ?>px;
            }
            .<?php echo $uid; ?> .mcd-num {
                font-size: <?php echo $num_fs; ?>px;
                font-weight: <?php echo $num_fw; ?>;
                line-height: 1.15;
                color: <?php echo $accent; ?>;
            }
            .<?php echo $uid; ?> .mcd-label {
                font-size: <?php echo $lbl_fs; ?>px;
                font-weight: <?php echo $lbl_fw; ?>;
                opacity: 0.7;
                margin-top: 4px;
                text-transform: uppercase;
                letter-spacing: 0.08em;
            }
            .<?php echo $uid; ?> .mcd-sep {
                font-size: <?php echo $sep_fs; ?>px;
                font-weight: 700;
                opacity: 0.45;
                line-height: 1;
                align-self: flex-start;
                padding-top: <?php echo max( 0, round( $num_fs * 0.15 ) ); ?>px;
            }
        </style>
        <div id="<?php echo esc_attr( $uid ); ?>" class="olo-countdown <?php echo esc_attr( $uid ); ?>" data-target="<?php echo esc_attr( $s['target_date'] ); ?>" data-expired="<?php echo esc_attr( $s['expired_message'] ); ?>">
            <?php
            $units = [];
            if ( $s['show_days'] )    $units[] = [ 'key' => 'days',    'label' => $s['label_days'] ];
            if ( $s['show_hours'] )   $units[] = [ 'key' => 'hours',   'label' => $s['label_hours'] ];
            if ( $s['show_minutes'] ) $units[] = [ 'key' => 'minutes', 'label' => $s['label_minutes'] ];
            if ( $s['show_seconds'] ) $units[] = [ 'key' => 'seconds', 'label' => $s['label_seconds'] ];

            foreach ( $units as $i => $unit ) :
                if ( $i > 0 && ! empty( $s['separator'] ) ) : ?>
                    <div class="mcd-sep"><?php echo esc_html( $s['separator'] ); ?></div>
                <?php endif; ?>
                <div class="mcd-item">
                    <div class="mcd-num" data-unit="<?php echo esc_attr( $unit['key'] ); ?>">00</div>
                    <div class="mcd-label"><?php echo esc_html( $unit['label'] ); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <script>
        (function(){
            var el=document.getElementById('<?php echo esc_js( $uid ); ?>');
            if(!el)return;
            var target=new Date(el.dataset.target).getTime();
            var vals=el.querySelectorAll('.mcd-num');
            function tick(){
                var now=Date.now(),diff=target-now;
                if(diff<=0){el.innerHTML='<div style="text-align:center;font-size:1.5em;padding:20px;">'+el.dataset.expired+'</div>';return;}
                var d=Math.floor(diff/86400000),h=Math.floor((diff%86400000)/3600000),m=Math.floor((diff%3600000)/60000),sc=Math.floor((diff%60000)/1000);
                var map={days:d,hours:h,minutes:m,seconds:sc};
                vals.forEach(function(v){var u=v.dataset.unit;if(map[u]!==undefined)v.textContent=String(map[u]).padStart(2,'0');});
            }
            tick();setInterval(tick,1000);
        })();
        </script>
        <?php
        return ob_get_clean();
    }

    private function render_uikit( $s ) {
        $uid = 'mcd-uk-' . wp_rand( 10000, 99999 );

        // Complete ISO date: "2026-12-31T23:59" → "2026-12-31T23:59:00+00:00"
        $date = $s['target_date'];
        if ( strlen( $date ) === 16 ) {
            $date .= ':00+00:00';
        } elseif ( strlen( $date ) === 19 && strpos( $date, '+' ) === false && strpos( $date, 'Z' ) === false ) {
            $date .= '+00:00';
        }

        $num_fs  = absint( $s['number_font_size'] );
        $num_fw  = absint( $s['number_font_weight'] );
        $lbl_fs  = absint( $s['label_font_size'] );
        $lbl_fw  = absint( $s['label_font_weight'] );
        $sep_fs  = absint( $s['separator_font_size'] );
        $pad     = absint( $s['padding'] );
        $bg      = esc_attr( $s['bg_color'] );
        $fg      = esc_attr( $s['text_color'] );
        $accent  = esc_attr( $s['accent_color'] );

        $units = [];
        if ( $s['show_days'] )    $units[] = [ 'cls' => 'uk-countdown-days',    'label' => $s['label_days'] ];
        if ( $s['show_hours'] )   $units[] = [ 'cls' => 'uk-countdown-hours',   'label' => $s['label_hours'] ];
        if ( $s['show_minutes'] ) $units[] = [ 'cls' => 'uk-countdown-minutes', 'label' => $s['label_minutes'] ];
        if ( $s['show_seconds'] ) $units[] = [ 'cls' => 'uk-countdown-seconds', 'label' => $s['label_seconds'] ];

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                padding: <?php echo $pad; ?>px;
                background: <?php echo $bg; ?>;
                color: <?php echo $fg; ?>;
            }
            .<?php echo $uid; ?> .uk-countdown-number {
                font-size: <?php echo $num_fs; ?>px;
                font-weight: <?php echo $num_fw; ?>;
                line-height: 1.15;
                color: <?php echo $accent; ?>;
            }
            .<?php echo $uid; ?> .olo-cd-label {
                font-size: <?php echo $lbl_fs; ?>px;
                font-weight: <?php echo $lbl_fw; ?>;
                opacity: 0.7;
                margin-top: 4px;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                text-align: center;
            }
            .<?php echo $uid; ?> .uk-countdown-separator {
                font-size: <?php echo $sep_fs; ?>px;
                font-weight: 700;
                opacity: 0.45;
            }
        </style>
        <div class="olo-countdown <?php echo esc_attr( $uid ); ?>" uk-countdown="date: <?php echo esc_attr( $date ); ?>">
            <div class="uk-grid-small uk-child-width-auto uk-flex-center" uk-grid>
                <?php foreach ( $units as $i => $unit ) :
                    if ( $i > 0 && ! empty( $s['separator'] ) ) : ?>
                        <div><div class="uk-countdown-separator uk-countdown-number"><?php echo esc_html( $s['separator'] ); ?></div></div>
                    <?php endif; ?>
                    <div>
                        <div class="uk-countdown-number <?php echo esc_attr( $unit['cls'] ); ?>"></div>
                        <div class="olo-cd-label"><?php echo esc_html( $unit['label'] ); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
