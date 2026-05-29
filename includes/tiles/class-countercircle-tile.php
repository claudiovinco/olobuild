<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Countercircle_Tile extends Olo_Tile_Base {

    protected $type     = 'countercircle';
    protected $name     = 'Counter Circle';
    protected $icon     = 'dashicons-marker';
    protected $category = 'content';
    protected $defaults = [
        'preset' => 'custom',
        'value'          => '75',
        'max_value'      => '100',
        'suffix'         => '%',
        'prefix'         => '',
        'title'          => 'Progresso',
        'size'           => '160',
        'stroke_width'   => '10',
        'stroke_color'   => '',
        'track_color'    => '',
        'text_color'     => '',
        'title_color'    => '',
        'duration'       => '1500',
        'title_position' => 'below',
            'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'value',          'type' => 'range',  'label' => 'Value' ],
            [ 'key' => 'max_value',      'type' => 'range',  'label' => 'Max Value' ],
            [ 'key' => 'suffix',         'type' => 'text',   'label' => 'Suffix' ],
            [ 'key' => 'prefix',         'type' => 'text',   'label' => 'Prefix' ],
            [ 'key' => 'title',          'type' => 'text',   'label' => 'Title' ],
            [ 'key' => 'title_position', 'type' => 'select', 'label' => 'Title Position' ],
            [ 'key' => 'size',           'type' => 'range',  'label' => 'Size (px)' ],
            [ 'key' => 'stroke_width',   'type' => 'range',  'label' => 'Stroke Width' ],
            [ 'key' => 'stroke_color',   'type' => 'color',  'label' => 'Stroke Color' ],
            [ 'key' => 'track_color',    'type' => 'color',  'label' => 'Track Color' ],
            [ 'key' => 'text_color',     'type' => 'color',  'label' => 'Text Color' ],
            [ 'key' => 'title_color',    'type' => 'color',  'label' => 'Title Color' ],
            [ 'key' => 'duration',       'type' => 'range',  'label' => 'Animation Duration (ms)' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $value        = floatval( $s['value'] );
        $max_value    = max( 1, floatval( $s['max_value'] ) );
        $ratio        = min( max( $value / $max_value, 0 ), 1 );
        $display_val  = round( $value );
        $prefix       = esc_html( $s['prefix'] );
        $suffix       = esc_html( $s['suffix'] );
        $title        = esc_html( $s['title'] );
        $title_pos    = in_array( $s['title_position'], [ 'above', 'below', 'inside' ], true ) ? $s['title_position'] : 'below';

        $size         = max( 60, intval( $s['size'] ) );
        $stroke_w     = max( 2, intval( $s['stroke_width'] ) );
        $stroke_color = $this->safe_color_css( $s['stroke_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $track_color  = $this->safe_color_css( $s['track_color'] ) ?: 'var(--olo-color-border, #E5E7EB)';
        $text_color   = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text, #374151)';
        $title_color  = $this->safe_color_css( $s['title_color'] ) ?: 'var(--olo-color-text-muted, #9CA3AF)';
        $duration     = max( 0, intval( $s['duration'] ) );

        $center       = $size / 2;
        $radius       = ( $size - $stroke_w ) / 2;
        $circumference = 2 * 3.14159265 * $radius;
        $dash_offset  = $circumference * ( 1 - $ratio );

        $font_size       = round( $size * 0.18 );
        $title_font_size = round( $size * 0.09 );

        $uid = 'olo-cc-' . wp_rand( 10000, 99999 );

        ob_start();
        ?>
        <div id="<?php echo esc_attr( $uid ); ?>" class="olo-countercircle olo-cc-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:24px 16px;gap:8px;">
            <?php if ( $title_pos === 'above' ) : ?>
            <div style="font-size:<?php echo $title_font_size; ?>px;font-weight:600;color:<?php echo $title_color; ?>;"><?php echo $title; ?></div>
            <?php endif; ?>

            <svg width="<?php echo $size; ?>" height="<?php echo $size; ?>" viewBox="0 0 <?php echo $size; ?> <?php echo $size; ?>">
                <!-- Track -->
                <circle
                    cx="<?php echo $center; ?>" cy="<?php echo $center; ?>" r="<?php echo $radius; ?>"
                    fill="none"
                    stroke="<?php echo $track_color; ?>"
                    stroke-width="<?php echo $stroke_w; ?>"
                />
                <!-- Progress -->
                <circle
                    class="olo-cc-progress"
                    cx="<?php echo $center; ?>" cy="<?php echo $center; ?>" r="<?php echo $radius; ?>"
                    fill="none"
                    stroke="<?php echo $stroke_color; ?>"
                    stroke-width="<?php echo $stroke_w; ?>"
                    stroke-dasharray="<?php echo $circumference; ?>"
                    stroke-dashoffset="<?php echo $circumference; ?>"
                    stroke-linecap="round"
                    transform="rotate(-90 <?php echo $center; ?> <?php echo $center; ?>)"
                    data-target-offset="<?php echo $dash_offset; ?>"
                    style="transition:stroke-dashoffset <?php echo $duration; ?>ms ease;"
                />
                <!-- Value text -->
                <text
                    x="<?php echo $center; ?>"
                    y="<?php echo ( $title_pos === 'inside' ) ? $center - $font_size * 0.35 : $center; ?>"
                    text-anchor="middle"
                    dominant-baseline="central"
                    fill="<?php echo $text_color; ?>"
                    font-size="<?php echo $font_size; ?>px"
                    font-weight="700"
                    class="olo-cc-value"
                    data-olo-cc-target="<?php echo $display_val; ?>"
                    data-olo-cc-prefix="<?php echo esc_attr( $s['prefix'] ); ?>"
                    data-olo-cc-suffix="<?php echo esc_attr( $s['suffix'] ); ?>"
                ><?php echo $prefix; ?>0<?php echo $suffix; ?></text>
                <?php if ( $title_pos === 'inside' ) : ?>
                <!-- Title inside circle -->
                <text
                    x="<?php echo $center; ?>"
                    y="<?php echo $center + $font_size * 0.65; ?>"
                    text-anchor="middle"
                    dominant-baseline="central"
                    fill="<?php echo $title_color; ?>"
                    font-size="<?php echo $title_font_size; ?>px"
                    font-weight="500"
                ><?php echo $title; ?></text>
                <?php endif; ?>
            </svg>

            <?php if ( $title_pos === 'below' ) : ?>
            <?php list( $t_tfx_cls, $t_tfx_data ) = $this->tfx_attrs( $s, 'title', wp_strip_all_tags( $title ) ); ?>
            <div class="olo-cc-title<?php echo $t_tfx_cls; ?>" style="font-size:<?php echo $title_font_size; ?>px;font-weight:600;color:<?php echo $title_color; ?>;"<?php echo $t_tfx_data; ?>><?php echo $title; ?></div>
            <?php endif; ?>
        </div>

        <script>
        (function(){
            var wrap = document.getElementById('<?php echo esc_js( $uid ); ?>');
            if(!wrap) return;

            var circle = wrap.querySelector('.olo-cc-progress');
            var valueEl = wrap.querySelector('.olo-cc-value');
            var targetOffset = parseFloat(circle.getAttribute('data-target-offset'));
            var targetVal = parseInt(valueEl.getAttribute('data-olo-cc-target')) || 0;
            var pfx = valueEl.getAttribute('data-olo-cc-prefix') || '';
            var sfx = valueEl.getAttribute('data-olo-cc-suffix') || '';
            var dur = <?php echo $duration; ?>;
            var animated = false;

            function animateCircle(){
                if(animated) return;
                animated = true;

                /* Animate stroke */
                circle.style.strokeDashoffset = targetOffset;

                /* Animate counter number */
                if(dur > 0){
                    var startTime = null;
                    function step(ts){
                        if(!startTime) startTime = ts;
                        var progress = Math.min((ts - startTime) / dur, 1);
                        var current = Math.round(progress * targetVal);
                        valueEl.textContent = pfx + current + sfx;
                        if(progress < 1){
                            requestAnimationFrame(step);
                        }
                    }
                    requestAnimationFrame(step);
                } else {
                    valueEl.textContent = pfx + targetVal + sfx;
                    circle.style.transition = 'none';
                    circle.style.strokeDashoffset = targetOffset;
                }
            }

            if('IntersectionObserver' in window){
                var obs = new IntersectionObserver(function(entries){
                    entries.forEach(function(entry){
                        if(entry.isIntersecting){
                            animateCircle();
                            obs.disconnect();
                        }
                    });
                }, {threshold: 0.2});
                obs.observe(wrap);
            } else {
                animateCircle();
            }
        })();
        </script>
        <?php
        $tfx_css = $this->tfx_css( $s, '#' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}";
            echo $border_hover_css . $border_effect_css . '</style>';
        }
        return ob_get_clean();
    }
}
