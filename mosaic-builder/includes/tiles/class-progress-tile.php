<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Progress_Tile extends Olo_Tile_Base {

    protected $type     = 'progress';
    protected $name     = 'Barra progresso';
    protected $icon     = 'dashicons-chart-bar';
    protected $category = 'content';
    protected $defaults = [
        'bars'            => "HTML / CSS|90\nJavaScript|80\nVue.js|75\nPHP / WordPress|85",
        'bar_color'       => '#6366F1',
        'bar_bg'          => '#1F2937',
        'text_color'      => '#F3F4F6',
        'height'          => '20',
        'show_percentage' => true,
        'animated'        => true,
        'border_radius'   => '10',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'bars',            'type' => 'textarea', 'label' => 'Bars (label|value per line, 0-100)' ],
            [ 'key' => 'bar_color',       'type' => 'color',    'label' => 'Bar Color' ],
            [ 'key' => 'bar_bg',          'type' => 'color',    'label' => 'Bar Background' ],
            [ 'key' => 'text_color',      'type' => 'color',    'label' => 'Text Color' ],
            [ 'key' => 'height',          'type' => 'range',    'label' => 'Bar Height' ],
            [ 'key' => 'show_percentage', 'type' => 'toggle',   'label' => 'Show Percentage' ],
            [ 'key' => 'animated',        'type' => 'toggle',   'label' => 'Animated' ],
            [ 'key' => 'border_radius',   'type' => 'range',    'label' => 'Border Radius' ],
        ];
    }

    public function render( $settings ) {
        $s    = wp_parse_args( $settings, $this->defaults );
        $bars = $this->parse_bars( $s['bars'] );

        ob_start();
        ?>
        <?php
            $prog_fg  = $this->safe_color( $s['text_color'] );
            $prog_bar = $this->safe_color( $s['bar_color'] );
            $prog_bg  = $this->safe_color( $s['bar_bg'] );
        ?>
        <div class="olo-progress" style="padding:16px;display:flex;flex-direction:column;gap:16px;">
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
                    ?>
                    <progress class="uk-progress" value="<?php echo $val; ?>" max="100" style="<?php if ( $prog_bg ) echo 'background:' . $prog_bg . ';'; ?><?php if ( $has_radius ) echo 'border-radius:' . $radius_css . ';'; ?>"></progress>
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
        return ob_get_clean();
    }

    private function parse_bars( $text ) {
        $bars  = [];
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
