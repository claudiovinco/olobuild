<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Shapedivider_Tile extends Olo_Tile_Base {

    protected $type     = 'shapedivider';
    protected $name     = 'Shape Divider';
    protected $icon     = 'dashicons-editor-contract';
    protected $category = 'layout';
    protected $defaults = [
        'shape'                      => 'wave',
        'position'                   => 'bottom',
        'flip_horizontal'            => false,
        'flip_vertical'              => false,
        'width'                      => '100',
        'height'                     => '80',
        'color'                      => '#ffffff',
        'z_index'                    => '1',
        'responsive_height_tablet'   => '',
        'responsive_height_mobile'   => '',
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
            [ 'key' => 'shape',            'type' => 'select', 'label' => 'Shape' ],
            [ 'key' => 'position',         'type' => 'select', 'label' => 'Position' ],
            [ 'key' => 'color',            'type' => 'color',  'label' => 'Color' ],
            [ 'key' => 'height',           'type' => 'range',  'label' => 'Height (px)' ],
            [ 'key' => 'width',            'type' => 'range',  'label' => 'Width (%)' ],
            [ 'key' => 'flip_horizontal',  'type' => 'toggle', 'label' => 'Flip Horizontal' ],
            [ 'key' => 'flip_vertical',    'type' => 'toggle', 'label' => 'Flip Vertical' ],
            [ 'key' => 'z_index',          'type' => 'range',  'label' => 'Z-Index' ],
            [ 'key' => 'responsive_height_tablet', 'type' => 'text', 'label' => 'Tablet Height (px)' ],
            [ 'key' => 'responsive_height_mobile', 'type' => 'text', 'label' => 'Mobile Height (px)' ],
        ];
    }

    /**
     * Get SVG path data for each shape type.
     */
    private function get_shape_paths() {
        return [
            'wave'      => 'M0,0 C300,100 900,0 1200,80 L1200,120 L0,120 Z',
            'wave2'     => 'M0,0 C150,80 350,0 500,50 C650,100 850,20 1000,60 C1100,80 1150,40 1200,50 L1200,120 L0,120 Z',
            'triangle'  => 'M600,0 L1200,120 L0,120 Z',
            'tilt'      => 'M0,0 L1200,120 L0,120 Z',
            'arrow'     => 'M0,120 L600,0 L1200,120 Z',
            'zigzag'    => 'M0,60 L100,0 L200,60 L300,0 L400,60 L500,0 L600,60 L700,0 L800,60 L900,0 L1000,60 L1100,0 L1200,60 L1200,120 L0,120 Z',
            'mountains' => 'M0,60 L300,10 L500,80 L800,20 L1000,70 L1200,30 L1200,120 L0,120 Z',
            'clouds'    => 'M0,80 C100,80 100,30 200,30 C300,30 300,70 400,70 C500,70 500,20 600,20 C700,20 700,60 800,60 C900,60 900,10 1000,10 C1100,10 1100,80 1200,80 L1200,120 L0,120 Z',
            'drops'     => 'M0,80 Q150,0 300,80 Q450,0 600,80 Q750,0 900,80 Q1050,0 1200,80 L1200,120 L0,120 Z',
            'curve'     => 'M0,100 Q600,0 1200,100 L1200,120 L0,120 Z',
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $paths = $this->get_shape_paths();
        $shape = $s['shape'];
        if ( ! isset( $paths[ $shape ] ) ) {
            $shape = 'wave';
        }
        $path = $paths[ $shape ];

        $position = ( $s['position'] === 'top' ) ? 'top' : 'bottom';
        $color    = $this->safe_color_css( $s['color'] ) ?: '#ffffff';
        $height   = max( 10, intval( $s['height'] ) );
        $width    = max( 100, min( 300, intval( $s['width'] ) ) );
        $flip_h   = ! empty( $s['flip_horizontal'] );
        $flip_v   = ! empty( $s['flip_vertical'] );
        $z_index  = max( 0, min( 99, intval( $s['z_index'] ) ) );

        $resp_tablet = trim( $s['responsive_height_tablet'] );
        $resp_mobile = trim( $s['responsive_height_mobile'] );

        // Build CSS transforms
        $transforms = [];
        if ( $flip_h ) {
            $transforms[] = 'scaleX(-1)';
        }
        if ( $flip_v ) {
            $transforms[] = 'scaleY(-1)';
        }
        $transform_css = ! empty( $transforms ) ? 'transform:' . implode( ' ', $transforms ) . ';' : '';

        // Width offset for >100%
        $left_offset = '';
        if ( $width > 100 ) {
            $offset_pct = -( $width - 100 ) / 2;
            $left_offset = 'left:' . $offset_pct . '%;';
        }

        $uid = 'olo-sd-' . wp_rand( 10000, 99999 );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                position: absolute;
                <?php echo $position; ?>: 0;
                <?php echo $left_offset; ?>
                width: <?php echo $width; ?>%;
                z-index: <?php echo $z_index; ?>;
                line-height: 0;
                pointer-events: none;
                overflow: hidden;
            }
            .<?php echo $uid; ?> svg {
                display: block;
                width: 100%;
                height: <?php echo $height; ?>px;
                <?php echo $transform_css; ?>
            }
            <?php if ( $position === 'bottom' ) : ?>
            .<?php echo $uid; ?> {
                transform: translateY(100%);
            }
            <?php else : ?>
            .<?php echo $uid; ?> {
                transform: translateY(-100%);
            }
            <?php endif; ?>
            <?php if ( $resp_tablet !== '' ) : ?>
            @media (max-width: 959px) {
                .<?php echo $uid; ?> svg {
                    height: <?php echo intval( $resp_tablet ); ?>px;
                }
            }
            <?php endif; ?>
            <?php if ( $resp_mobile !== '' ) : ?>
            @media (max-width: 639px) {
                .<?php echo $uid; ?> svg {
                    height: <?php echo intval( $resp_mobile ); ?>px;
                }
            }
            <?php endif; ?>
        </style>
        <div class="olo-shapedivider <?php echo esc_attr( $uid ); ?>">
            <svg preserveAspectRatio="none" viewBox="0 0 1200 120" xmlns="http://www.w3.org/2000/svg">
                <path d="<?php echo esc_attr( $path ); ?>" fill="<?php echo $color; ?>" />
            </svg>
        </div>
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
}
