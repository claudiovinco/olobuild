<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Progress_Tile extends Olo_Tile_Base {

    protected $type     = 'progress';
    protected $name     = 'Barra progresso';
    protected $icon     = 'dashicons-chart-bar';
    protected $category = 'marketing';
    protected $defaults = [
        'preset' => 'custom',
        'bars'               => "HTML / CSS|90\nJavaScript|80\nVue.js|75\nPHP / WordPress|85",
        'bar_color'          => '',
        'bar_bg'             => '',
        'text_color'         => '',
        'height'             => '20',
        'show_percentage'    => true,
        'animated'           => true,
        'border_radius'      => '10',
        'layout'             => 'bar',
        'circle_size'        => '120',
        'circle_width'       => '8',
        'inner_text'         => '',
        'animate_counter'    => true,
        'animation_duration' => '1500',
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
            [ 'key' => 'layout',            'type' => 'select',   'label' => 'Layout' ],
            [ 'key' => 'bars',              'type' => 'textarea', 'label' => 'Bars (label|value per line, 0-100)' ],
            [ 'key' => 'bar_color',         'type' => 'color',    'label' => 'Bar Color' ],
            [ 'key' => 'bar_bg',            'type' => 'color',    'label' => 'Bar Background' ],
            [ 'key' => 'text_color',        'type' => 'color',    'label' => 'Text Color' ],
            [ 'key' => 'height',            'type' => 'range',    'label' => 'Bar Height' ],
            [ 'key' => 'show_percentage',   'type' => 'toggle',   'label' => 'Show Percentage' ],
            [ 'key' => 'animated',          'type' => 'toggle',   'label' => 'Animated' ],
            [ 'key' => 'border_radius',     'type' => 'range',    'label' => 'Border Radius' ],
            [ 'key' => 'circle_size',       'type' => 'range',    'label' => 'Circle Size' ],
            [ 'key' => 'circle_width',      'type' => 'range',    'label' => 'Circle Width' ],
            [ 'key' => 'inner_text',        'type' => 'text',     'label' => 'Inner Text' ],
            [ 'key' => 'animate_counter',   'type' => 'toggle',   'label' => 'Animate Counter' ],
            [ 'key' => 'animation_duration','type' => 'range',    'label' => 'Animation Duration (ms)' ],
        ];
    }

    public function render( $settings ) {
        $s    = wp_parse_args( $settings, $this->defaults );
        $bars = $this->parse_bars( $s['bars'] );

        $layout = isset( $s['layout'] ) ? $s['layout'] : 'bar';

        ob_start();

        $prog_fg  = $this->safe_color_css( $s['text_color'] );
        $prog_bar = $this->safe_color_css( $s['bar_color'] );
        $prog_bg  = $this->safe_color_css( $s['bar_bg'] );

        $uid = 'olo-prog-' . wp_unique_id();

        if ( $layout === 'circle' ) {
            $this->render_circle( $s, $bars, $prog_fg, $prog_bar, $prog_bg, $uid );
        } else {
            $this->render_bar( $s, $bars, $prog_fg, $prog_bar, $prog_bg, $uid );
        }

        /* Animated counter script */
        $animate_counter = ! empty( $s['animate_counter'] );
        $duration        = max( 100, intval( $s['animation_duration'] ) );
        if ( $animate_counter ) :
        ?>
        <script>
        (function(){
            var wrap = document.getElementById('<?php echo esc_js( $uid ); ?>');
            if(!wrap) return;
            var counters = wrap.querySelectorAll('[data-olo-counter]');
            if(!counters.length) return;
            var animated = false;
            var dur = <?php echo $duration; ?>;
            function animateCounters(){
                if(animated) return;
                animated = true;
                counters.forEach(function(el){
                    var target = parseInt(el.getAttribute('data-olo-counter')) || 0;
                    var inner  = el.getAttribute('data-olo-inner') || '';
                    var start  = 0;
                    var startTime = null;
                    function step(ts){
                        if(!startTime) startTime = ts;
                        var progress = Math.min((ts - startTime) / dur, 1);
                        var current  = Math.round(progress * target);
                        if(inner){
                            el.textContent = inner;
                        } else {
                            el.textContent = current + '%';
                        }
                        if(progress < 1){
                            requestAnimationFrame(step);
                        }
                    }
                    requestAnimationFrame(step);
                });
            }
            if('IntersectionObserver' in window){
                var obs = new IntersectionObserver(function(entries){
                    entries.forEach(function(entry){
                        if(entry.isIntersecting){
                            animateCounters();
                            obs.disconnect();
                        }
                    });
                }, {threshold: 0.2});
                obs.observe(wrap);
            } else {
                animateCounters();
            }
        })();
        </script>
        <?php
        endif;

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

    /**
     * Render circle layout
     */
    private function render_circle( $s, $bars, $prog_fg, $prog_bar, $prog_bg, $uid ) {
        $circle_size  = max( 40, intval( $s['circle_size'] ) );
        $circle_width = max( 1, intval( $s['circle_width'] ) );
        $radius       = ( $circle_size - $circle_width ) / 2;
        $circumference = 2 * 3.14159265 * $radius;
        $cx            = $circle_size / 2;
        $inner_text    = isset( $s['inner_text'] ) ? trim( $s['inner_text'] ) : '';
        $animate       = ! empty( $s['animate_counter'] );
        ?>
        <div id="<?php echo esc_attr( $uid ); ?>" class="olo-progress olo-pr-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?> olo-progress-circle" style="display:flex;flex-wrap:wrap;gap:16px;justify-content:center;padding:16px;">
            <?php foreach ( $bars as $bar ) :
                $val    = min( max( intval( $bar['value'] ), 0 ), 100 );
                $offset = $circumference - ( $circumference * $val / 100 );
                $font_size = round( $circle_size * 0.2 );
            ?>
            <div style="text-align:center;">
                <svg width="<?php echo $circle_size; ?>" height="<?php echo $circle_size; ?>" viewBox="0 0 <?php echo $circle_size; ?> <?php echo $circle_size; ?>">
                    <circle cx="<?php echo $cx; ?>" cy="<?php echo $cx; ?>" r="<?php echo $radius; ?>" fill="none"
                        stroke="<?php echo esc_attr( $prog_bg ? $prog_bg : 'var(--olo-color-secondary, #1F2937)' ); ?>" stroke-width="<?php echo $circle_width; ?>" />
                    <circle cx="<?php echo $cx; ?>" cy="<?php echo $cx; ?>" r="<?php echo $radius; ?>" fill="none"
                        stroke="<?php echo esc_attr( $prog_bar ? $prog_bar : 'var(--olo-color-primary, #e1474f)' ); ?>" stroke-width="<?php echo $circle_width; ?>"
                        stroke-dasharray="<?php echo $circumference; ?>" stroke-dashoffset="<?php echo $offset; ?>"
                        stroke-linecap="round" transform="rotate(-90 <?php echo $cx; ?> <?php echo $cx; ?>)"
                        style="transition:stroke-dashoffset 1s ease;" />
                    <text x="<?php echo $cx; ?>" y="<?php echo $cx; ?>" text-anchor="middle" dominant-baseline="central"
                        fill="<?php echo esc_attr( $prog_fg ? $prog_fg : 'var(--olo-color-border, #E5E7EB)' ); ?>" font-size="<?php echo $font_size; ?>px" font-weight="600"
                        <?php if ( $animate ) : ?>data-olo-counter="<?php echo $val; ?>" data-olo-inner="<?php echo esc_attr( $inner_text ); ?>"<?php endif; ?>><?php
                        echo $inner_text ? esc_html( $inner_text ) : $val . '%';
                    ?></text>
                </svg>
                <div style="margin-top:4px;font-size:11px;<?php if ( $prog_fg ) echo 'color:' . $prog_fg . ';'; ?>"><?php echo esc_html( $bar['label'] ); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

    /**
     * Render bar layout
     */
    private function render_bar( $s, $bars, $prog_fg, $prog_bar, $prog_bg, $uid ) {
        $inner_text = isset( $s['inner_text'] ) ? trim( $s['inner_text'] ) : '';
        $animate    = ! empty( $s['animate_counter'] );
        ?>
        <div id="<?php echo esc_attr( $uid ); ?>" class="olo-progress olo-pr-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" style="padding:16px;display:flex;flex-direction:column;gap:16px;">
            <?php foreach ( $bars as $bar ) :
                $val = min( max( intval( $bar['value'] ), 0 ), 100 );
            ?>
                <div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px;<?php if ( $prog_fg ) echo 'color:' . $prog_fg . ';'; ?>font-size:0.875em;">
                        <span style="font-weight:600;"><?php echo esc_html( $bar['label'] ); ?></span>
                        <?php if ( $s['show_percentage'] ) : ?>
                            <span><?php echo $val; ?>%</span>
                        <?php endif; ?>
                    </div>
                    <?php
                        $rad_raw = $s['border_radius'];
                        if ( is_array( $rad_raw ) ) {
                            $radius_css = sprintf( '%dpx %dpx %dpx %dpx', absint( $rad_raw['tl'] ?? 0 ), absint( $rad_raw['tr'] ?? 0 ), absint( $rad_raw['br'] ?? 0 ), absint( $rad_raw['bl'] ?? 0 ) );
                            $has_radius = true;
                        } else {
                            $radius_css = intval( $rad_raw ) . 'px';
                            $has_radius = intval( $rad_raw ) > 0;
                        }
                        $bar_height = max( 10, intval( $s['height'] ) );
                    ?>
                    <div style="position:relative;<?php if ( $prog_bg ) echo 'background:' . $prog_bg . ';'; ?><?php if ( $has_radius ) echo 'border-radius:' . $radius_css . ';'; ?>height:<?php echo $bar_height; ?>px;overflow:hidden;">
                        <div style="height:100%;width:<?php echo $val; ?>%;<?php if ( $prog_bar ) echo 'background:' . $prog_bar . ';'; ?><?php if ( $has_radius ) echo 'border-radius:' . $radius_css . ';'; ?>transition:width 1s ease;"></div>
                        <?php
                            $show_inner = ( $inner_text !== '' ) || ! empty( $s['show_percentage'] );
                        ?>
                        <?php if ( $show_inner ) : ?>
                            <span style="position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);font-size:10px;font-weight:600;<?php if ( $prog_fg ) echo 'color:' . $prog_fg . ';'; ?>"
                                <?php if ( $animate ) : ?>data-olo-counter="<?php echo $val; ?>" data-olo-inner="<?php echo esc_attr( $inner_text ); ?>"<?php endif; ?>><?php
                                echo $inner_text ? esc_html( $inner_text ) : $val . '%';
                            ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
            $rad_raw2 = $s['border_radius'];
            if ( is_array( $rad_raw2 ) ) {
                $radius_css2 = sprintf( '%dpx %dpx %dpx %dpx', absint( $rad_raw2['tl'] ?? 0 ), absint( $rad_raw2['tr'] ?? 0 ), absint( $rad_raw2['br'] ?? 0 ), absint( $rad_raw2['bl'] ?? 0 ) );
                $has_radius2 = true;
            } else {
                $radius_css2 = intval( $rad_raw2 ) . 'px';
                $has_radius2 = intval( $rad_raw2 ) > 0;
            }
            if ( $prog_bar || $has_radius2 ) :
        ?>
        <style>
            .olo-progress .uk-progress::-webkit-progress-value { <?php if ( $prog_bar ) echo 'background-color:' . $prog_bar . ';'; ?><?php if ( $has_radius2 ) echo 'border-radius:' . $radius_css2 . ';'; ?> }
            .olo-progress .uk-progress::-moz-progress-bar { <?php if ( $prog_bar ) echo 'background-color:' . $prog_bar . ';'; ?><?php if ( $has_radius2 ) echo 'border-radius:' . $radius_css2 . ';'; ?> }
        </style>
        <?php endif; ?>
        <?php
    }

    private function parse_bars( $text ) {
        $bars  = [];
        $text  = is_array( $text ) ? implode( "\n", $text ) : (string) $text;
        $lines = array_filter( array_map( 'trim', explode( "\n", $text ) ) );
        foreach ( $lines as $line ) {
            $parts = explode( '|', $line, 2 );
            if ( count( $parts ) === 2 ) {
                $bars[] = [ 'label' => trim( $parts[0] ), 'value' => trim( $parts[1] ) ];
            }
        }
        return $bars;
    }
}
