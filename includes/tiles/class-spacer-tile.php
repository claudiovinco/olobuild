<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Spacer_Tile extends Olo_Tile_Base {

    protected $type     = 'spacer';
    protected $name     = 'Spaziatore';
    protected $icon     = 'dashicons-arrows-alt';
    protected $category = 'essential';
    protected $defaults = [
        'height'                    => '60',
        'shape_top'                 => 'none',
        'shape_top_height'          => '80',
        'shape_top_color'           => '#ffffff',
        'shape_top_opacity'         => '100',
        'shape_top_fill'            => 'color',
        'shape_top_fill_image'      => '',
        'shape_top_fill_video'      => '',
        'shape_top_flip'            => false,
        'shape_top_invert'          => false,
        'shape_top_scale_x'         => '100',
        'shape_top_layer2'          => false,
        'shape_top_layer2_color'    => '#000000',
        'shape_top_layer2_opacity'  => '30',
        'shape_bottom'              => 'none',
        'shape_bottom_height'       => '80',
        'shape_bottom_color'        => '#ffffff',
        'shape_bottom_opacity'      => '100',
        'shape_bottom_fill'         => 'color',
        'shape_bottom_fill_image'   => '',
        'shape_bottom_fill_video'   => '',
        'shape_bottom_flip'         => false,
        'shape_bottom_invert'       => false,
        'shape_bottom_scale_x'      => '100',
        'shape_bottom_layer2'       => false,
        'shape_bottom_layer2_color' => '#000000',
        'shape_bottom_layer2_opacity'=> '30',
        'bg_color'                  => '',
        'bg_gradient'               => false,
        'bg_gradient_from'          => '',
        'bg_gradient_to'            => '',
        'bg_gradient_angle'         => '180',
        'full_bleed'                => false,
        'overlap_top'               => '0',
        'overlap_bottom'            => '0',
        'custom_svg'                => '',
        'show_divider'              => false,
        'divider_style'             => 'solid',
        'divider_color'             => '',
        'divider_width'             => '100',
        'divider_thickness'         => '1',
    ];

    public function get_controls() {
        return [];
    }

    private function get_shape_paths() {
        return [
            'wave'       => 'M0,60 C150,110 350,0 600,60 C850,120 1050,0 1200,60 L1200,120 L0,120 Z',
            'wave-rough' => 'M0,50 C60,100 120,20 180,70 C240,120 300,30 360,80 C420,130 480,10 540,60 C600,110 660,20 720,70 C780,120 840,25 900,75 C960,125 1020,15 1080,65 C1140,115 1180,40 1200,55 L1200,120 L0,120 Z',
            'tilt'       => 'M0,0 L1200,120 L0,120 Z',
            'triangle'   => 'M0,120 L600,0 L1200,120 Z',
            'curve'      => 'M0,120 Q600,-20 1200,120 Z',
            'mountains'  => 'M0,120 L100,80 L200,100 L350,30 L500,90 L650,15 L800,70 L950,25 L1100,85 L1200,40 L1200,120 Z',
            'drops'      => 'M0,80 Q75,0 150,80 Q225,0 300,80 Q375,0 450,80 Q525,0 600,80 Q675,0 750,80 Q825,0 900,80 Q975,0 1050,80 Q1125,0 1200,80 L1200,120 L0,120 Z',
            'zigzag'     => 'M0,80 L50,30 L100,80 L150,30 L200,80 L250,30 L300,80 L350,30 L400,80 L450,30 L500,80 L550,30 L600,80 L650,30 L700,80 L750,30 L800,80 L850,30 L900,80 L950,30 L1000,80 L1050,30 L1100,80 L1150,30 L1200,80 L1200,120 L0,120 Z',
            'clouds'     => 'M0,100 C50,100 50,40 100,40 C150,40 150,80 200,80 C250,80 250,20 300,20 C350,20 350,70 400,70 C450,70 450,30 500,30 C550,30 550,60 600,60 C650,60 650,15 700,15 C750,15 750,75 800,75 C850,75 850,35 900,35 C950,35 950,85 1000,85 C1050,85 1050,45 1100,45 C1150,45 1150,90 1200,90 L1200,120 L0,120 Z',
            'brush'      => 'M0,55 C30,35 60,70 100,50 C140,25 180,80 230,52 C280,24 320,78 370,48 C420,18 460,82 510,54 C560,26 600,76 650,46 C700,16 740,84 790,56 C840,28 880,74 930,44 C980,14 1020,82 1070,58 C1120,32 1160,72 1200,50 L1200,120 L0,120 Z',
        ];
    }

    /**
     * Render a single shape block (top or bottom).
     */
    private function render_shape_block( $uid, $s, $prefix, $position ) {
        $shape_key = $s[ $prefix ] ?? 'none';
        if ( $shape_key === 'none' ) {
            return '';
        }

        $paths = $this->get_shape_paths();
        if ( ! isset( $paths[ $shape_key ] ) ) {
            return '';
        }

        $path     = $paths[ $shape_key ];
        $shape_h  = absint( $s[ $prefix . '_height' ] ) ?: 80;
        $fill     = $s[ $prefix . '_fill' ] ?? 'color';
        $color    = $this->safe_color_css( $s[ $prefix . '_color' ] ) ?: '#ffffff';
        $opacity  = max( 0.1, min( 1, absint( $s[ $prefix . '_opacity' ] ) / 100 ) );
        $flip     = ! empty( $s[ $prefix . '_flip' ] );
        $invert   = ! empty( $s[ $prefix . '_invert' ] );
        $scale_x  = absint( $s[ $prefix . '_scale_x' ] ?? 100 ) ?: 100;
        $layer2   = ! empty( $s[ $prefix . '_layer2' ] );
        $l2_color = $this->safe_color_css( $s[ $prefix . '_layer2_color' ] ) ?: '#000000';
        $l2_opacity = max( 0.05, min( 1, absint( $s[ $prefix . '_layer2_opacity' ] ) / 100 ) );
        $fill_image = $s[ $prefix . '_fill_image' ] ?? '';
        $fill_video = $s[ $prefix . '_fill_video' ] ?? '';

        // SVG transforms
        $transforms = [];
        if ( $position === 'bottom' ) {
            $transforms[] = 'scaleY(-1)';
        }
        if ( $flip ) {
            $transforms[] = 'scaleX(-1)';
        }
        if ( $invert ) {
            $transforms[] = ( $position === 'bottom' ) ? 'scaleY(1)' : 'scaleY(-1)';
        }
        if ( $scale_x !== 100 ) {
            $transforms[] = 'scaleX(' . ( $scale_x / 100 ) . ')';
        }
        $transform_css = ! empty( $transforms ) ? 'transform:' . implode( ' ', $transforms ) . ';' : '';

        $pos_css = ( $position === 'top' ) ? 'bottom: 100%;' : 'top: 100%; transform-origin: top center;';
        $block_id = $uid . '-' . $position;

        ob_start();
        ?>
        <div class="olo-sp-shape olo-sp-shape--<?php echo $position; ?>" style="<?php echo $pos_css; ?>">
        <?php if ( $fill === 'video' && ! empty( $fill_video ) ) : ?>
            <svg preserveAspectRatio="none" viewBox="0 0 1200 120" xmlns="http://www.w3.org/2000/svg"
                 style="width:100%;height:<?php echo $shape_h; ?>px;display:block;<?php echo $transform_css; ?>">
                <defs>
                    <clipPath id="<?php echo esc_attr( $block_id ); ?>-clip">
                        <path d="<?php echo esc_attr( $path ); ?>" />
                    </clipPath>
                </defs>
                <?php if ( $layer2 ) : ?>
                <path d="<?php echo esc_attr( $path ); ?>" fill="<?php echo $l2_color; ?>" opacity="<?php echo esc_attr( $l2_opacity ); ?>" transform="translate(-30, 8) scale(1.05, 1)" />
                <?php endif; ?>
                <foreignObject x="0" y="0" width="1200" height="120" clip-path="url(#<?php echo esc_attr( $block_id ); ?>-clip)">
                    <video xmlns="http://www.w3.org/1999/xhtml"
                           src="<?php echo esc_url( $fill_video ); ?>"
                           autoplay muted loop playsinline
                           style="display:block;width:100%;height:100%;object-fit:cover;"></video>
                </foreignObject>
            </svg>
        <?php elseif ( $fill === 'image' && ! empty( $fill_image ) ) : ?>
            <svg preserveAspectRatio="none" viewBox="0 0 1200 120" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                 style="width:100%;height:<?php echo $shape_h; ?>px;display:block;<?php echo $transform_css; ?>">
                <defs>
                    <clipPath id="<?php echo esc_attr( $block_id ); ?>-clip">
                        <path d="<?php echo esc_attr( $path ); ?>" />
                    </clipPath>
                </defs>
                <?php if ( $layer2 ) : ?>
                <path d="<?php echo esc_attr( $path ); ?>" fill="<?php echo $l2_color; ?>" opacity="<?php echo esc_attr( $l2_opacity ); ?>" transform="translate(-30, 8) scale(1.05, 1)" />
                <?php endif; ?>
                <image href="<?php echo esc_url( $fill_image ); ?>" x="0" y="0" width="1200" height="120" preserveAspectRatio="xMidYMid slice" clip-path="url(#<?php echo esc_attr( $block_id ); ?>-clip)" />
            </svg>
        <?php else : ?>
            <svg preserveAspectRatio="none" viewBox="0 0 1200 120" xmlns="http://www.w3.org/2000/svg"
                 style="width:100%;height:<?php echo $shape_h; ?>px;display:block;<?php echo $transform_css; ?>">
                <?php if ( $layer2 ) : ?>
                <path d="<?php echo esc_attr( $path ); ?>" fill="<?php echo $l2_color; ?>" opacity="<?php echo esc_attr( $l2_opacity ); ?>" transform="translate(-30, 8) scale(1.05, 1)" />
                <?php endif; ?>
                <path d="<?php echo esc_attr( $path ); ?>" fill="<?php echo $color; ?>" opacity="<?php echo esc_attr( $opacity ); ?>" />
            </svg>
        <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-sp-' . wp_rand( 10000, 99999 );

        $height      = absint( $s['height'] );
        $overlap_top = absint( $s['overlap_top'] );
        $overlap_bot = absint( $s['overlap_bottom'] );
        $full_bleed  = ! empty( $s['full_bleed'] );

        // Background
        $bg_css = '';
        if ( ! empty( $s['bg_gradient'] ) ) {
            $angle = absint( $s['bg_gradient_angle'] ) % 360;
            $from  = $this->safe_color_css( $s['bg_gradient_from'] ) ?: 'var(--olo-color-primary, #e1474f)';
            $to    = $this->safe_color_css( $s['bg_gradient_to'] ) ?: 'var(--olo-color-accent, #f4a23b)';
            $bg_css = "background: linear-gradient({$angle}deg, {$from}, {$to});";
        } elseif ( ! empty( $s['bg_color'] ) ) {
            $bg_css = 'background-color: ' . ( $this->safe_color_css( $s['bg_color'] ) ) . ';';
        }

        // Divider
        $show_divider = ! empty( $s['show_divider'] );
        $div_style    = in_array( $s['divider_style'], [ 'solid', 'dashed', 'dotted', 'double' ], true ) ? $s['divider_style'] : 'solid';
        $div_color    = $this->safe_color_css( $s['divider_color'] ) ?: 'var(--olo-color-text, #374151)';
        $div_width    = absint( $s['divider_width'] ) ?: 100;
        $div_thick    = absint( $s['divider_thickness'] ) ?: 1;

        // Custom SVG
        $custom_svg = '';
        if ( ! empty( $s['custom_svg'] ) ) {
            $svg_common = [ 'id' => true, 'class' => true, 'style' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'stroke-dasharray' => true, 'stroke-dashoffset' => true, 'opacity' => true, 'fill-opacity' => true, 'stroke-opacity' => true, 'fill-rule' => true, 'clip-rule' => true, 'transform' => true, 'transform-origin' => true, 'filter' => true, 'mask' => true, 'clip-path' => true ];
            $custom_svg = wp_kses( $s['custom_svg'], [
                'svg'      => array_merge( $svg_common, [ 'viewbox' => true, 'xmlns' => true, 'xmlns:xlink' => true, 'width' => true, 'height' => true, 'preserveaspectratio' => true ] ),
                'path'     => array_merge( $svg_common, [ 'd' => true ] ),
                'circle'   => array_merge( $svg_common, [ 'cx' => true, 'cy' => true, 'r' => true ] ),
                'rect'     => array_merge( $svg_common, [ 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'ry' => true ] ),
                'ellipse'  => array_merge( $svg_common, [ 'cx' => true, 'cy' => true, 'rx' => true, 'ry' => true ] ),
                'line'     => array_merge( $svg_common, [ 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true ] ),
                'polyline' => array_merge( $svg_common, [ 'points' => true ] ),
                'polygon'  => array_merge( $svg_common, [ 'points' => true ] ),
                'g'        => $svg_common,
                'defs'     => [],
                'symbol'   => array_merge( $svg_common, [ 'viewbox' => true ] ),
                'clippath' => [ 'id' => true, 'clippathunits' => true ],
                'mask'     => [ 'id' => true, 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'maskunits' => true, 'maskcontentunits' => true ],
                'pattern'  => [ 'id' => true, 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'patternunits' => true, 'patterntransform' => true, 'viewbox' => true ],
                'image'    => [ 'href' => true, 'xlink:href' => true, 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'preserveaspectratio' => true, 'opacity' => true, 'style' => true ],
                'lineargradient' => [ 'id' => true, 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true, 'gradientunits' => true, 'gradienttransform' => true ],
                'radialgradient' => [ 'id' => true, 'cx' => true, 'cy' => true, 'r' => true, 'fx' => true, 'fy' => true, 'gradientunits' => true, 'gradienttransform' => true ],
                'stop'     => [ 'offset' => true, 'stop-color' => true, 'stop-opacity' => true, 'style' => true ],
                'text'     => array_merge( $svg_common, [ 'x' => true, 'y' => true, 'dx' => true, 'dy' => true, 'font-size' => true, 'font-family' => true, 'font-weight' => true, 'text-anchor' => true, 'dominant-baseline' => true, 'letter-spacing' => true ] ),
                'tspan'    => array_merge( $svg_common, [ 'x' => true, 'y' => true, 'dx' => true, 'dy' => true, 'font-size' => true, 'font-family' => true, 'font-weight' => true, 'text-anchor' => true ] ),
                'use'      => [ 'href' => true, 'xlink:href' => true, 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'style' => true ],
                'filter'   => [ 'id' => true, 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'filterunits' => true ],
                'fegaussianblur'   => [ 'in' => true, 'stddeviation' => true, 'result' => true ],
                'feoffset'         => [ 'in' => true, 'dx' => true, 'dy' => true, 'result' => true ],
                'femerge'          => [],
                'femergenode'      => [ 'in' => true ],
                'fecolormatrix'    => [ 'in' => true, 'type' => true, 'values' => true, 'result' => true ],
                'feblend'          => [ 'in' => true, 'in2' => true, 'mode' => true, 'result' => true ],
                'feflood'          => [ 'flood-color' => true, 'flood-opacity' => true, 'result' => true ],
                'fecomposite'      => [ 'in' => true, 'in2' => true, 'operator' => true, 'result' => true ],
                'animate'          => [ 'attributename' => true, 'from' => true, 'to' => true, 'dur' => true, 'repeatcount' => true, 'begin' => true, 'fill' => true, 'values' => true, 'keytimes' => true, 'calcmode' => true ],
                'animatetransform' => [ 'attributename' => true, 'type' => true, 'from' => true, 'to' => true, 'dur' => true, 'repeatcount' => true, 'begin' => true, 'fill' => true, 'values' => true ],
            ] );
        }

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                position: relative;
                overflow: visible;
                z-index: 2;
                height: <?php echo $height; ?>px;
                <?php echo $bg_css; ?>
                <?php if ( $overlap_top > 0 ) : ?>margin-top: -<?php echo $overlap_top; ?>px;<?php endif; ?>
                <?php if ( $overlap_bot > 0 ) : ?>margin-bottom: -<?php echo $overlap_bot; ?>px;<?php endif; ?>
                <?php if ( $full_bleed ) : ?>
                width: 100vw;
                left: 50%;
                margin-left: -50vw;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-sp-shape {
                position: absolute;
                left: 0;
                width: 100%;
                line-height: 0;
                pointer-events: none;
                overflow: hidden;
            }
            .<?php echo $uid; ?> .olo-sp-shape--top { bottom: 100%; }
            .<?php echo $uid; ?> .olo-sp-shape--bottom { top: 100%; }
            <?php if ( ! empty( $custom_svg ) ) : ?>
            .<?php echo $uid; ?> .olo-sp-custom-svg {
                display: flex;
                align-items: center;
                justify-content: center;
                position: absolute;
                inset: 0;
                pointer-events: none;
            }
            .<?php echo $uid; ?> .olo-sp-custom-svg svg { max-width: 100%; max-height: 100%; }
            <?php endif; ?>
        </style>

        <div class="olo-spacer <?php echo esc_attr( $uid ); ?>">
            <?php echo $this->render_shape_block( $uid, $s, 'shape_top', 'top' ); ?>

            <?php if ( $show_divider ) : ?>
            <div style="display: flex; align-items: center; justify-content: center; height: 100%; position: relative; z-index: 20;">
                <hr style="width: <?php echo $div_width; ?>%; border: 0; border-top: <?php echo $div_thick; ?>px <?php echo esc_attr( $div_style ); ?> <?php echo $div_color; ?>; margin: 0;" />
            </div>
            <?php endif; ?>

            <?php if ( ! empty( $custom_svg ) ) : ?>
            <div class="olo-sp-custom-svg"><?php echo $custom_svg; ?></div>
            <?php endif; ?>

            <?php echo $this->render_shape_block( $uid, $s, 'shape_bottom', 'bottom' ); ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
