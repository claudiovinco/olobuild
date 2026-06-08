<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Chart_Tile extends Olo_Tile_Base {

    protected $type     = 'chart';
    protected $name     = 'Grafico';
    protected $icon     = 'dashicons-chart-area';
    protected $category = 'interactive';
    protected $defaults = [
        'chart_type'           => 'bar',
        'items'                => [
            [ 'id' => 'c-1', 'label' => 'Gen', 'value' => '65', 'color' => '#e1474f' ],
            [ 'id' => 'c-2', 'label' => 'Feb', 'value' => '45', 'color' => '#16263d' ],
            [ 'id' => 'c-3', 'label' => 'Mar', 'value' => '80', 'color' => '#f4a23b' ],
            [ 'id' => 'c-4', 'label' => 'Apr', 'value' => '55', 'color' => '#15803d' ],
        ],
        'chart_height'         => '400',
        'show_legend'          => true,
        'legend_position'      => 'bottom',
        'legend_align'         => 'center',
        'legend_color'         => '',
        'legend_font_size'     => '12',
        'legend_font_weight'   => '400',
        'legend_box_width'     => '40',
        'legend_padding'       => '10',
        'legend_point_style'   => false,
        'show_title'           => false,
        'chart_title'          => '',
        'title_color'          => '',
        'title_font_size'      => '16',
        'title_font_weight'    => '700',
        'title_padding'        => '10',
        'show_subtitle'        => false,
        'chart_subtitle'       => '',
        'subtitle_color'       => '',
        'subtitle_font_size'   => '12',
        'tooltip_enabled'      => true,
        'tooltip_bg'           => '#000000',
        'tooltip_text_color'   => '#ffffff',
        'tooltip_border_color' => '',
        'tooltip_border_width' => '0',
        'tooltip_corner_radius'=> '6',
        'tooltip_font_size'    => '12',
        'tooltip_padding'      => '8',
        'animate'              => true,
        'bg_color'             => 'transparent',
        'border_width'         => '2',
        'border_color_override'=> '',
        'bar_radius'           => '0',
        'bar_percentage'       => '0.8',
        'category_percentage'  => '0.8',
        'fill_area'            => false,
        'point_radius'         => '4',
        'point_hover_radius'   => '6',
        'point_style'          => 'circle',
        'tension'              => '0.4',
        'doughnut_cutout'      => '50',
        'grid_color'           => '#374151',
        'grid_line_width'      => '1',
        'axis_color'           => '',
        'text_color'           => '#9CA3AF',
        'tick_font_size'       => '11',
        'show_x_grid'          => true,
        'show_y_grid'          => true,
        'show_x_border'        => true,
        'show_y_border'        => true,
        'begin_at_zero'        => true,
        'y_min'                => '',
        'y_max'                => '',
        'y_step_size'          => '',
        'index_axis'           => 'x',
        'dataset_label'        => '',
        'stacked'              => false,
        'stepped_line'         => '',
        'x_label'              => '',
        'y_label'              => '',
        'tooltip_prefix'       => '',
        'tooltip_suffix'       => '',
        'number_format'        => false,
            'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    private static $chartjs_enqueued = false;

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-chart-' . wp_unique_id();

        $items = $this->parse_items( $s['items'] );
        if ( empty( $items ) ) {
            return '';
        }

        $chart_type = in_array( $s['chart_type'], [ 'bar', 'line', 'pie', 'doughnut', 'radar', 'polarArea' ], true ) ? $s['chart_type'] : 'bar';
        $dataset_label = esc_js( trim( $s['dataset_label'] ) );
        $stacked       = ! empty( $s['stacked'] );
        $stepped_line  = in_array( $s['stepped_line'], [ 'before', 'after', 'middle' ], true ) ? $s['stepped_line'] : '';
        $x_label       = esc_js( trim( $s['x_label'] ) );
        $y_label       = esc_js( trim( $s['y_label'] ) );
        $tt_prefix     = esc_js( $s['tooltip_prefix'] );
        $tt_suffix     = esc_js( $s['tooltip_suffix'] );
        $num_format    = ! empty( $s['number_format'] );
        $chart_height = max( 100, intval( $s['chart_height'] ) );
        $bg_color     = $this->safe_color_css( $s['bg_color'] );
        $text_color   = $this->safe_color_css( $s['text_color'] ) ?: '#9CA3AF';
        $grid_color   = $this->safe_color_css( $s['grid_color'] ) ?: '#374151';
        $axis_color   = $this->safe_color_css( $s['axis_color'] ) ?: $grid_color;

        $border_override = $this->safe_color_css( $s['border_color_override'] );

        // Data arrays
        $labels = [];
        $values = [];
        $bg_colors = [];
        $border_colors = [];
        foreach ( $items as $item ) {
            $labels[]        = esc_js( $item['label'] );
            $values[]        = floatval( $item['value'] );
            $item_color      = $this->safe_color_css( $item['color'] ) ?: '#e1474f';
            $bg_colors[]     = $item_color;
            $border_colors[] = $border_override ?: ( $this->safe_color_css( $item['border_color'] ?? '' ) ?: $item_color );
        }

        $labels_js  = '["' . implode( '","', $labels ) . '"]';
        $values_js  = '[' . implode( ',', $values ) . ']';
        $bg_js      = '["' . implode( '","', $bg_colors ) . '"]';
        $border_js  = '["' . implode( '","', $border_colors ) . '"]';

        $has_grid    = in_array( $chart_type, [ 'bar', 'line' ], true );
        $data_bw     = max( 0, intval( $s['border_width'] ) );
        $animate     = ! empty( $s['animate'] );
        $tension     = max( 0, min( 1, floatval( $s['tension'] ) ) );
        $index_axis  = $s['index_axis'] === 'y' ? 'y' : 'x';

        // Legend
        $show_legend     = ! empty( $s['show_legend'] );
        $legend_position = in_array( $s['legend_position'], [ 'top', 'bottom', 'left', 'right' ], true ) ? $s['legend_position'] : 'bottom';
        $legend_align    = in_array( $s['legend_align'], [ 'start', 'center', 'end' ], true ) ? $s['legend_align'] : 'center';
        $legend_color    = $this->safe_color_css( $s['legend_color'] ) ?: $text_color;
        $legend_fs       = max( 8, intval( $s['legend_font_size'] ) );
        $legend_fw       = esc_js( $s['legend_font_weight'] ?: '400' );
        $legend_bw       = max( 10, intval( $s['legend_box_width'] ) );
        $legend_pad      = max( 0, intval( $s['legend_padding'] ) );
        $legend_ps       = ! empty( $s['legend_point_style'] );

        // Title
        $show_title   = ! empty( $s['show_title'] );
        $chart_title  = esc_js( $s['chart_title'] );
        $title_color  = $this->safe_color_css( $s['title_color'] ) ?: $text_color;
        $title_fs     = max( 10, intval( $s['title_font_size'] ) );
        $title_fw     = esc_js( $s['title_font_weight'] ?: '700' );
        $title_pad    = max( 0, intval( $s['title_padding'] ) );

        // Subtitle
        $show_sub     = ! empty( $s['show_subtitle'] );
        $subtitle     = esc_js( $s['chart_subtitle'] );
        $sub_color    = $this->safe_color_css( $s['subtitle_color'] ) ?: $text_color;
        $sub_fs       = max( 8, intval( $s['subtitle_font_size'] ) );

        // Tooltip
        $tt_enabled   = $s['tooltip_enabled'] !== false;
        $tt_bg        = $this->safe_color_css( $s['tooltip_bg'] ) ?: '#000000';
        $tt_text      = $this->safe_color_css( $s['tooltip_text_color'] ) ?: '#ffffff';
        $tt_bc        = $this->safe_color_css( $s['tooltip_border_color'] ) ?: 'transparent';
        $tt_bw        = max( 0, intval( $s['tooltip_border_width'] ) );
        $tt_cr        = max( 0, Olo_Tile_Utils::radius_int( $s['tooltip_corner_radius'] ) );
        $tt_fs        = max( 8, intval( $s['tooltip_font_size'] ) );
        $tt_pad       = max( 4, intval( $s['tooltip_padding'] ) );

        // Bar
        $bar_radius   = max( 0, Olo_Tile_Utils::radius_int( $s['bar_radius'] ) );
        $bar_pct      = max( 0.1, min( 1, floatval( $s['bar_percentage'] ) ) );
        $cat_pct      = max( 0.1, min( 1, floatval( $s['category_percentage'] ) ) );

        // Line/Radar
        $fill_area    = ! empty( $s['fill_area'] );
        $point_r      = max( 0, intval( $s['point_radius'] ) );
        $point_hr     = max( 0, intval( $s['point_hover_radius'] ) );
        $point_style  = esc_js( $s['point_style'] ?: 'circle' );

        // Doughnut
        $cutout       = max( 10, min( 90, intval( $s['doughnut_cutout'] ) ) );

        // Grid
        $grid_lw      = max( 0, floatval( $s['grid_line_width'] ) );
        $tick_fs      = max( 8, intval( $s['tick_font_size'] ) );
        $show_x_grid  = $s['show_x_grid'] !== false;
        $show_y_grid  = $s['show_y_grid'] !== false;
        $show_x_bdr   = $s['show_x_border'] !== false;
        $show_y_bdr   = $s['show_y_border'] !== false;
        $begin_zero   = $s['begin_at_zero'] !== false;
        $y_min        = $s['y_min'];
        $y_max        = $s['y_max'];
        $y_step       = $s['y_step_size'];

        // Enqueue Chart.js
        if ( ! self::$chartjs_enqueued ) {
            self::$chartjs_enqueued = true;
            wp_enqueue_script( 'chartjs', OLO_URL . 'assets/vendor/chartjs/chart.umd.min.js', [], '4.5.0', true );
        }

        ob_start();
        ?>
        <div class="olo-chart" style="<?php if ( $bg_color ) echo 'background:' . $bg_color . ';'; ?>padding:16px;">
            <canvas id="<?php echo esc_attr( $uid ); ?>" style="width:100%;height:<?php echo $chart_height; ?>px;max-height:<?php echo $chart_height; ?>px;"></canvas>
        </div>
        <script>
        (function(){
            function initChart(){
                if(typeof Chart === 'undefined'){
                    setTimeout(initChart, 100);
                    return;
                }
                var canvas = document.getElementById('<?php echo esc_js( $uid ); ?>');
                if(!canvas) return;

                var created = false;

                function createChart(){
                    if(created) return;
                    created = true;

                    var ctx = canvas.getContext('2d');
                    var chartType = '<?php echo esc_js( $chart_type ); ?>';
                    var labels = <?php echo $labels_js; ?>;
                    var values = <?php echo $values_js; ?>;
                    var bgColors = <?php echo $bg_js; ?>;
                    var borderColors = <?php echo $border_js; ?>;

                    var dataset = {
                        data: values,
                        backgroundColor: bgColors,
                        borderColor: borderColors,
                        borderWidth: <?php echo $data_bw; ?>
                    };

                    <?php if ( $chart_type === 'bar' ) : ?>
                    dataset.borderRadius = <?php echo $bar_radius; ?>;
                    dataset.barPercentage = <?php echo $bar_pct; ?>;
                    dataset.categoryPercentage = <?php echo $cat_pct; ?>;
                    <?php endif; ?>

                    <?php if ( $chart_type === 'line' ) : ?>
                    dataset.fill = <?php echo $fill_area ? 'true' : 'false'; ?>;
                    dataset.tension = <?php echo $tension; ?>;
                    dataset.pointBackgroundColor = bgColors;
                    dataset.pointRadius = <?php echo $point_r; ?>;
                    dataset.pointHoverRadius = <?php echo $point_hr; ?>;
                    dataset.pointStyle = '<?php echo $point_style; ?>';
                    <?php if ( $stepped_line ) : ?>
                    dataset.stepped = '<?php echo esc_js( $stepped_line ); ?>';
                    <?php endif; ?>
                    <?php endif; ?>

                    <?php if ( $chart_type === 'radar' ) : ?>
                    dataset.fill = <?php echo $fill_area ? 'true' : 'false'; ?>;
                    dataset.tension = <?php echo $tension; ?>;
                    dataset.pointBackgroundColor = bgColors;
                    dataset.pointRadius = <?php echo $point_r; ?>;
                    dataset.pointHoverRadius = <?php echo $point_hr; ?>;
                    dataset.pointStyle = '<?php echo $point_style; ?>';
                    <?php endif; ?>

                    <?php if ( in_array( $chart_type, [ 'bar', 'line', 'radar' ], true ) ) : ?>
                    dataset.label = '<?php echo $dataset_label; ?>';
                    <?php endif; ?>

                    var config = {
                        type: chartType,
                        data: {
                            labels: labels,
                            datasets: [dataset]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            <?php if ( $chart_type === 'bar' ) : ?>
                            indexAxis: '<?php echo $index_axis; ?>',
                            <?php endif; ?>
                            <?php if ( $chart_type === 'doughnut' ) : ?>
                            cutout: '<?php echo $cutout; ?>%',
                            <?php endif; ?>
                            <?php if ( ! $animate ) : ?>
                            animation: false,
                            <?php endif; ?>
                            plugins: {
                                legend: {
                                    display: <?php echo $show_legend ? 'true' : 'false'; ?>,
                                    position: '<?php echo esc_js( $legend_position ); ?>',
                                    align: '<?php echo esc_js( $legend_align ); ?>',
                                    labels: {
                                        color: '<?php echo esc_js( $legend_color ); ?>',
                                        font: { size: <?php echo $legend_fs; ?>, weight: '<?php echo $legend_fw; ?>' },
                                        boxWidth: <?php echo $legend_bw; ?>,
                                        padding: <?php echo $legend_pad; ?>
                                        <?php if ( $legend_ps ) : ?>
                                        ,usePointStyle: true,
                                        pointStyleWidth: 12
                                        <?php endif; ?>
                                    }
                                },
                                title: {
                                    display: <?php echo $show_title ? 'true' : 'false'; ?>,
                                    text: '<?php echo $chart_title; ?>',
                                    color: '<?php echo esc_js( $title_color ); ?>',
                                    font: { size: <?php echo $title_fs; ?>, weight: '<?php echo $title_fw; ?>' },
                                    padding: { bottom: <?php echo $title_pad; ?> }
                                },
                                subtitle: {
                                    display: <?php echo $show_sub ? 'true' : 'false'; ?>,
                                    text: '<?php echo $subtitle; ?>',
                                    color: '<?php echo esc_js( $sub_color ); ?>',
                                    font: { size: <?php echo $sub_fs; ?> }
                                },
                                tooltip: {
                                    enabled: <?php echo $tt_enabled ? 'true' : 'false'; ?>,
                                    backgroundColor: '<?php echo esc_js( $tt_bg ); ?>',
                                    titleColor: '<?php echo esc_js( $tt_text ); ?>',
                                    bodyColor: '<?php echo esc_js( $tt_text ); ?>',
                                    borderColor: '<?php echo esc_js( $tt_bc ); ?>',
                                    borderWidth: <?php echo $tt_bw; ?>,
                                    cornerRadius: <?php echo $tt_cr; ?>,
                                    titleFont: { size: <?php echo $tt_fs; ?> },
                                    bodyFont: { size: <?php echo $tt_fs; ?> },
                                    padding: <?php echo $tt_pad; ?>
                                    <?php if ( $tt_prefix || $tt_suffix || $num_format ) : ?>
                                    ,callbacks: {
                                        label: function(ctx) {
                                            var v = ctx.parsed.y !== undefined ? ctx.parsed.y : ctx.parsed;
                                            <?php if ( $num_format ) : ?>
                                            v = v.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                            <?php endif; ?>
                                            var lbl = ctx.dataset.label ? ctx.dataset.label + ': ' : '';
                                            return lbl + '<?php echo $tt_prefix; ?>' + v + '<?php echo $tt_suffix; ?>';
                                        }
                                    }
                                    <?php endif; ?>
                                }
                            }
                            <?php if ( $has_grid ) : ?>
                            ,scales: {
                                x: {
                                    <?php if ( $stacked ) : ?>stacked: true,<?php endif; ?>
                                    ticks: { color: '<?php echo esc_js( $text_color ); ?>', font: { size: <?php echo $tick_fs; ?> } },
                                    grid: { display: <?php echo $show_x_grid ? 'true' : 'false'; ?>, color: '<?php echo esc_js( $grid_color ); ?>', lineWidth: <?php echo $grid_lw; ?> },
                                    border: { display: <?php echo $show_x_bdr ? 'true' : 'false'; ?>, color: '<?php echo esc_js( $axis_color ); ?>' }
                                    <?php if ( $x_label ) : ?>,title: { display: true, text: '<?php echo $x_label; ?>', color: '<?php echo esc_js( $text_color ); ?>' }<?php endif; ?>
                                },
                                y: {
                                    <?php if ( $stacked ) : ?>stacked: true,<?php endif; ?>
                                    ticks: { color: '<?php echo esc_js( $text_color ); ?>', font: { size: <?php echo $tick_fs; ?> }<?php if ( $y_step !== '' ) echo ', stepSize: ' . floatval( $y_step ); ?> },
                                    grid: { display: <?php echo $show_y_grid ? 'true' : 'false'; ?>, color: '<?php echo esc_js( $grid_color ); ?>', lineWidth: <?php echo $grid_lw; ?> },
                                    border: { display: <?php echo $show_y_bdr ? 'true' : 'false'; ?>, color: '<?php echo esc_js( $axis_color ); ?>' },
                                    beginAtZero: <?php echo $begin_zero ? 'true' : 'false'; ?>
                                    <?php if ( $y_min !== '' ) echo ', min: ' . floatval( $y_min ); ?>
                                    <?php if ( $y_max !== '' ) echo ', max: ' . floatval( $y_max ); ?>
                                    <?php if ( $y_label ) : ?>,title: { display: true, text: '<?php echo $y_label; ?>', color: '<?php echo esc_js( $text_color ); ?>' }<?php endif; ?>
                                }
                            }
                            <?php endif; ?>
                            <?php if ( $chart_type === 'radar' ) : ?>
                            ,scales: {
                                r: {
                                    ticks: { color: '<?php echo esc_js( $text_color ); ?>', font: { size: <?php echo $tick_fs; ?> }, backdropColor: 'transparent' },
                                    grid: { color: '<?php echo esc_js( $grid_color ); ?>', lineWidth: <?php echo $grid_lw; ?> },
                                    angleLines: { color: '<?php echo esc_js( $grid_color ); ?>' },
                                    pointLabels: { color: '<?php echo esc_js( $text_color ); ?>', font: { size: <?php echo $tick_fs; ?> } }
                                }
                            }
                            <?php endif; ?>
                        }
                    };

                    new Chart(ctx, config);
                }

                if('IntersectionObserver' in window){
                    var obs = new IntersectionObserver(function(entries){
                        entries.forEach(function(entry){
                            if(entry.isIntersecting){
                                createChart();
                                obs.disconnect();
                            }
                        });
                    }, {threshold: 0.1});
                    obs.observe(canvas);
                } else {
                    createChart();
                }
            }

            if(document.readyState === 'loading'){
                document.addEventListener('DOMContentLoaded', initChart);
            } else {
                initChart();
            }
        })();
        </script>
        <?php

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

    private function parse_items( $items ) {
        if ( ! is_array( $items ) ) {
            return [];
        }

        $parsed = [];
        foreach ( $items as $item ) {
            if ( ! is_array( $item ) ) continue;
            $parsed[] = [
                'label'        => isset( $item['label'] ) ? trim( $item['label'] ) : '',
                'value'        => isset( $item['value'] ) ? trim( $item['value'] ) : '0',
                'color'        => isset( $item['color'] ) ? trim( $item['color'] ) : '#e1474f',
                'border_color' => isset( $item['border_color'] ) ? trim( $item['border_color'] ) : '',
            ];
        }
        return $parsed;
    }
}
