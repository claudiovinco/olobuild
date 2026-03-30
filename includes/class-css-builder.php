<?php
/**
 * Olobuild CSS Builder
 *
 * Pure CSS generation utilities extracted from Olo_Frontend_Renderer.
 * All methods are stateless — they take a config array and return CSS strings or arrays.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_CSS_Builder {

    private $shadow_map = [
        'sm' => '0 1px 2px 0 rgba(0,0,0,0.05)',
        'md' => '0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1)',
        'lg' => '0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1)',
        'xl' => '0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1)',
    ];

    private $drop_shadow_map = [
        'sm' => 'drop-shadow(0 1px 2px rgba(0,0,0,0.05))',
        'md' => 'drop-shadow(0 4px 6px rgba(0,0,0,0.1)) drop-shadow(0 2px 4px rgba(0,0,0,0.1))',
        'lg' => 'drop-shadow(0 10px 15px rgba(0,0,0,0.1)) drop-shadow(0 4px 6px rgba(0,0,0,0.1))',
        'xl' => 'drop-shadow(0 20px 25px rgba(0,0,0,0.1)) drop-shadow(0 8px 10px rgba(0,0,0,0.1))',
    ];

    /**
     * Resolve effective background config from a style array.
     *
     * @param array $style Tile style.
     * @return array Background config with 'type' key.
     */
    public function get_effective_bg( $style ) {
        if ( ! empty( $style['bg'] ) && ! empty( $style['bg']['type'] ) && $style['bg']['type'] !== 'none' ) {
            return $style['bg'];
        }
        if ( ! empty( $style['bg_color'] ) ) {
            return [ 'type' => 'solid', 'color' => $style['bg_color'] ];
        }
        return [ 'type' => 'none' ];
    }

    /**
     * Generate inline background CSS from a bg config.
     *
     * @param array $bg Background config.
     * @return string CSS declaration or empty string.
     */
    public function get_bg_inline_css( $bg ) {
        if ( $bg['type'] === 'solid' && ! empty( $bg['color'] ) ) {
            $color   = $bg['color'];
            $opacity = isset( $bg['color_opacity'] ) ? intval( $bg['color_opacity'] ) : 100;
            if ( $opacity < 100 && preg_match( '/^#([0-9a-fA-F]{6})$/', $color, $m ) ) {
                $r = hexdec( substr( $m[1], 0, 2 ) );
                $g = hexdec( substr( $m[1], 2, 2 ) );
                $b = hexdec( substr( $m[1], 4, 2 ) );
                $a = round( $opacity / 100, 2 );
                return "background-color: rgba({$r}, {$g}, {$b}, {$a})";
            }
            return 'background-color: ' . esc_attr( $color );
        }
        if ( $bg['type'] === 'gradient' ) {
            return $this->build_gradient_css( $bg );
        }
        return '';
    }

    /**
     * Build gradient CSS with multi-stop support.
     *
     * @param array $bg Background config with gradient settings.
     * @return string CSS background declaration.
     */
    public function build_gradient_css( $bg ) {
        $gradient_type = $bg['gradient_type'] ?? 'linear';

        // New format: bg.gradient = { type, angle, stops: [{color, position}, ...] }
        $gradient_obj = $bg['gradient'] ?? null;
        if ( is_array( $gradient_obj ) && ! empty( $gradient_obj['stops'] ) ) {
            $gradient_type = $gradient_obj['type'] ?? 'linear';
            $bg['gradient_stops'] = $gradient_obj['stops'];
            $bg['gradient_angle'] = $gradient_obj['angle'] ?? 180;
        }

        // Multi-stop gradient
        if ( ! empty( $bg['gradient_stops'] ) && is_array( $bg['gradient_stops'] ) ) {
            $stops = [];
            foreach ( $bg['gradient_stops'] as $stop ) {
                $color = esc_attr( $stop['color'] ?? '#000000' );
                $pos   = isset( $stop['position'] ) ? intval( $stop['position'] ) : null;
                $stops[] = $pos !== null ? "{$color} {$pos}%" : $color;
            }
            $stop_str = implode( ', ', $stops );

            if ( $gradient_type === 'radial' ) {
                return 'background: radial-gradient(circle, ' . $stop_str . ')';
            }
            $angle = intval( $bg['gradient_angle'] ?? 180 );
            return 'background: linear-gradient(' . $angle . 'deg, ' . $stop_str . ')';
        }

        // Fallback: simple two-color gradient
        $from  = esc_attr( $bg['gradient_from'] ?? '#ffffff' );
        $to    = esc_attr( $bg['gradient_to'] ?? '#000000' );

        if ( $gradient_type === 'radial' ) {
            return 'background: radial-gradient(circle, ' . $from . ', ' . $to . ')';
        }
        $angle = intval( $bg['gradient_angle'] ?? 180 );
        return "background: linear-gradient({$angle}deg, {$from}, {$to})";
    }

    /**
     * Build border-radius CSS value from number or {tl,tr,br,bl} array.
     *
     * @param array|int $val Border radius value.
     * @return string CSS declaration.
     */
    public function build_border_radius_css( $val ) {
        if ( is_array( $val ) ) {
            return sprintf( 'border-radius: %dpx %dpx %dpx %dpx',
                intval( $val['tl'] ?? 0 ), intval( $val['tr'] ?? 0 ),
                intval( $val['br'] ?? 0 ), intval( $val['bl'] ?? 0 ) );
        }
        return "border-radius: {$val}px";
    }

    /**
     * Build flex container CSS declarations from settings array.
     *
     * @param array $settings Section/container settings.
     * @return array Array of CSS declarations.
     */
    public function build_flex_container_css( $settings ) {
        $decls = [];
        $fd = $settings['flex_direction'] ?? '';
        $fj = $settings['flex_justify'] ?? '';
        $fa = $settings['flex_align'] ?? '';
        $fw = $settings['flex_wrap'] ?? '';
        $fcg = $settings['flex_column_gap'] ?? '';
        $frg = $settings['flex_row_gap'] ?? '';
        $fg  = $settings['flex_gap'] ?? ''; // legacy
        $has_flex_gap = ( $fcg && intval( $fcg ) > 0 ) || ( $frg && intval( $frg ) > 0 ) || ( $fg && intval( $fg ) > 0 );
        $has_flex = ( $fd && $fd !== 'row' ) || ( $fj && $fj !== 'flex-start' ) || ( $fa && $fa !== 'stretch' ) || ( $fw && $fw !== 'nowrap' ) || $has_flex_gap;
        if ( $has_flex ) {
            $decls[] = 'display: flex';
            if ( $fd && $fd !== 'row' )         $decls[] = 'flex-direction: ' . esc_attr( $fd );
            if ( $fj && $fj !== 'flex-start' )  $decls[] = 'justify-content: ' . esc_attr( $fj );
            if ( $fa && $fa !== 'stretch' )     $decls[] = 'align-items: ' . esc_attr( $fa );
            if ( $fw && $fw !== 'nowrap' )      $decls[] = 'flex-wrap: ' . esc_attr( $fw );
            if ( ( $fcg && intval( $fcg ) > 0 ) || ( $frg && intval( $frg ) > 0 ) ) {
                if ( $fcg && intval( $fcg ) > 0 ) $decls[] = 'column-gap: ' . intval( $fcg ) . 'px';
                if ( $frg && intval( $frg ) > 0 ) $decls[] = 'row-gap: ' . intval( $frg ) . 'px';
            } elseif ( $fg && intval( $fg ) > 0 ) {
                $decls[] = 'gap: ' . intval( $fg ) . 'px';
            }
        }
        return $decls;
    }

    /**
     * Build CSS Grid layout declarations from settings.
     *
     * @param array $settings Section/container settings.
     * @return array Array of CSS declarations.
     */
    public function build_css_grid_css( $settings ) {
        $decls = [];
        $mode = $settings['layout_mode'] ?? 'flex';
        if ( $mode !== 'grid' ) {
            return $decls;
        }
        $decls[] = 'display: grid';
        $cols = $settings['grid_columns'] ?? '';
        if ( $cols ) {
            $decls[] = 'grid-template-columns: ' . esc_attr( $cols );
        }
        $rows = $settings['grid_rows'] ?? '';
        if ( $rows && $rows !== 'auto' ) {
            $decls[] = 'grid-template-rows: ' . esc_attr( $rows );
        }
        $gap = intval( $settings['grid_gap'] ?? 0 );
        $col_gap = intval( $settings['grid_column_gap'] ?? 0 );
        $row_gap = intval( $settings['grid_row_gap'] ?? 0 );
        if ( $col_gap > 0 || $row_gap > 0 ) {
            $decls[] = 'column-gap: ' . ( $col_gap > 0 ? $col_gap : $gap ) . 'px';
            $decls[] = 'row-gap: ' . ( $row_gap > 0 ? $row_gap : $gap ) . 'px';
        } elseif ( $gap > 0 ) {
            $decls[] = 'gap: ' . $gap . 'px';
        }
        $ai = $settings['grid_align_items'] ?? 'stretch';
        if ( $ai && $ai !== 'stretch' ) {
            $decls[] = 'align-items: ' . esc_attr( $ai );
        }
        $ji = $settings['grid_justify_items'] ?? 'stretch';
        if ( $ji && $ji !== 'stretch' ) {
            $decls[] = 'justify-items: ' . esc_attr( $ji );
        }
        return $decls;
    }

    /**
     * Build CSS transform declarations from style array.
     *
     * @param array $style Tile style.
     * @return array Array of CSS declarations (transform, transform-origin).
     */
    public function build_transform_css( $style ) {
        $parts = [];

        // Support nested transform object from FieldTransform
        $tf = isset( $style['transform'] ) && is_array( $style['transform'] ) ? $style['transform'] : null;

        $rotate = $tf ? floatval( $tf['rotate'] ?? 0 ) : ( isset( $style['transform_rotate'] ) ? floatval( $style['transform_rotate'] ) : 0 );
        if ( $rotate != 0 ) {
            $parts[] = 'rotate(' . $rotate . 'deg)';
        }
        $rotateX = isset( $style['transform_rotateX'] ) ? floatval( $style['transform_rotateX'] ) : 0;
        if ( $rotateX != 0 ) {
            $parts[] = 'rotate3d(1,0,0,' . $rotateX . 'deg)';
        }
        $rotateY = isset( $style['transform_rotateY'] ) ? floatval( $style['transform_rotateY'] ) : 0;
        if ( $rotateY != 0 ) {
            $parts[] = 'rotate3d(0,1,0,' . $rotateY . 'deg)';
        }
        $scale = $tf ? floatval( $tf['scale'] ?? 1 ) : ( isset( $style['transform_scale'] ) ? floatval( $style['transform_scale'] ) : 1 );
        if ( $scale != 1 ) {
            $parts[] = 'scale(' . $scale . ')';
        }
        $tx = $tf ? floatval( $tf['translateX'] ?? 0 ) : ( isset( $style['transform_translateX'] ) ? floatval( $style['transform_translateX'] ) : 0 );
        if ( $tx != 0 ) {
            $parts[] = 'translateX(' . $tx . 'px)';
        }
        $ty = $tf ? floatval( $tf['translateY'] ?? 0 ) : ( isset( $style['transform_translateY'] ) ? floatval( $style['transform_translateY'] ) : 0 );
        if ( $ty != 0 ) {
            $parts[] = 'translateY(' . $ty . 'px)';
        }
        $skewX = $tf ? floatval( $tf['skewX'] ?? 0 ) : ( isset( $style['transform_skewX'] ) ? floatval( $style['transform_skewX'] ) : 0 );
        if ( $skewX != 0 ) {
            $parts[] = 'skewX(' . $skewX . 'deg)';
        }
        $skewY = $tf ? floatval( $tf['skewY'] ?? 0 ) : ( isset( $style['transform_skewY'] ) ? floatval( $style['transform_skewY'] ) : 0 );
        if ( $skewY != 0 ) {
            $parts[] = 'skewY(' . $skewY . 'deg)';
        }

        $origin = $tf ? ( $tf['origin'] ?? '' ) : ( $style['transform_origin'] ?? '' );

        if ( empty( $parts ) ) {
            if ( $origin !== '' && $origin !== 'center' && $origin !== 'center center' ) {
                return [ 'transform-origin: ' . esc_attr( $origin ) ];
            }
            return [];
        }

        $decls = [];
        $decls[] = 'transform: ' . implode( ' ', $parts );

        if ( $origin !== '' && $origin !== 'center' && $origin !== 'center center' ) {
            $decls[] = 'transform-origin: ' . esc_attr( $origin );
        }

        return $decls;
    }

    /**
     * Build box-shadow CSS declaration from style array.
     *
     * @param array $style Tile style.
     * @return string CSS declaration or empty string.
     */
    public function build_box_shadow_css( $style ) {
        $shadow = $style['shadow'] ?? 'none';
        if ( ! $shadow || $shadow === 'none' ) {
            return '';
        }

        if ( $shadow === 'custom' ) {
            $h      = intval( $style['shadow_h'] ?? 0 );
            $v      = intval( $style['shadow_v'] ?? 0 );
            $blur   = intval( $style['shadow_blur'] ?? 0 );
            $spread = intval( $style['shadow_spread'] ?? 0 );
            $color  = esc_attr( $style['shadow_color'] ?? 'rgba(0,0,0,0.15)' );
            $inset  = ! empty( $style['shadow_inset'] ) ? 'inset ' : '';
            return "box-shadow: {$inset}{$h}px {$v}px {$blur}px {$spread}px {$color}";
        }

        if ( isset( $this->shadow_map[ $shadow ] ) ) {
            return 'box-shadow: ' . $this->shadow_map[ $shadow ];
        }

        // Legacy shadow_type support
        $shadow_type = $style['shadow_type'] ?? '';
        if ( $shadow_type === 'custom' ) {
            $h      = intval( $style['shadow_h'] ?? 0 );
            $v      = intval( $style['shadow_v'] ?? 0 );
            $blur   = intval( $style['shadow_blur'] ?? 0 );
            $spread = intval( $style['shadow_spread'] ?? 0 );
            $color  = esc_attr( $style['shadow_color'] ?? 'rgba(0,0,0,0.2)' );
            $inset  = ! empty( $style['shadow_inset'] ) ? 'inset ' : '';
            return "box-shadow: {$inset}{$h}px {$v}px {$blur}px {$spread}px {$color}";
        }

        return '';
    }

    /**
     * Build filter: drop-shadow() CSS declaration from style array.
     * Used instead of box-shadow when the element has a mask/clip-path.
     *
     * @param array $style Tile style.
     * @return string CSS filter value or empty string.
     */
    public function build_drop_shadow_css( $style ) {
        $shadow = $style['shadow'] ?? 'none';
        if ( ! $shadow || $shadow === 'none' ) {
            return '';
        }

        if ( $shadow === 'custom' ) {
            $h     = intval( $style['shadow_h'] ?? 0 );
            $v     = intval( $style['shadow_v'] ?? 0 );
            $blur  = intval( $style['shadow_blur'] ?? 0 );
            $color = esc_attr( $style['shadow_color'] ?? 'rgba(0,0,0,0.15)' );
            return "drop-shadow({$h}px {$v}px {$blur}px {$color})";
        }

        if ( isset( $this->drop_shadow_map[ $shadow ] ) ) {
            return $this->drop_shadow_map[ $shadow ];
        }

        // Legacy
        $shadow_type = $style['shadow_type'] ?? '';
        if ( $shadow_type === 'custom' ) {
            $h     = intval( $style['shadow_h'] ?? 0 );
            $v     = intval( $style['shadow_v'] ?? 0 );
            $blur  = intval( $style['shadow_blur'] ?? 0 );
            $color = esc_attr( $style['shadow_color'] ?? 'rgba(0,0,0,0.2)' );
            return "drop-shadow({$h}px {$v}px {$blur}px {$color})";
        }

        return '';
    }

    /**
     * Build text-shadow CSS declaration from style array.
     *
     * @param array $style Tile style.
     * @return string CSS declaration or empty string.
     */
    public function build_text_shadow_css( $style ) {
        $h_val = $style['text_shadow_h'] ?? null;
        $v_val = $style['text_shadow_v'] ?? null;
        $blur_val = $style['text_shadow_blur'] ?? null;
        if ( $h_val === null && $v_val === null && $blur_val === null && empty( $style['text_shadow_enabled'] ) ) {
            if ( empty( $style['text_shadow_color'] ) ) {
                return '';
            }
        }
        if ( isset( $style['text_shadow_enabled'] ) && empty( $style['text_shadow_enabled'] ) ) {
            return '';
        }
        $h = intval( $h_val ?? 0 );
        $v = intval( $v_val ?? 0 );
        $b = intval( $blur_val ?? 0 );
        if ( $h === 0 && $v === 0 && $b === 0 ) {
            return '';
        }
        $color = esc_attr( $style['text_shadow_color'] ?? 'rgba(0,0,0,0.3)' );
        return "text-shadow: {$h}px {$v}px {$b}px {$color}";
    }

    /**
     * Build backdrop-filter CSS declarations from style array.
     *
     * @param array $style Tile style.
     * @return array Array with standard + webkit prefix, or empty array.
     */
    public function build_backdrop_filter_css( $style ) {
        $parts = [];
        $blur       = isset( $style['backdrop_blur'] ) ? intval( $style['backdrop_blur'] ) : 0;
        $brightness = isset( $style['backdrop_brightness'] ) ? intval( $style['backdrop_brightness'] ) : 100;
        $contrast   = isset( $style['backdrop_contrast'] ) ? intval( $style['backdrop_contrast'] ) : 100;
        $saturate   = isset( $style['backdrop_saturate'] ) ? intval( $style['backdrop_saturate'] ) : 100;

        if ( $blur != 0 )        $parts[] = 'blur(' . $blur . 'px)';
        if ( $brightness != 100 ) $parts[] = 'brightness(' . $brightness . '%)';
        if ( $contrast != 100 )   $parts[] = 'contrast(' . $contrast . '%)';
        if ( $saturate != 100 )   $parts[] = 'saturate(' . $saturate . '%)';

        if ( empty( $parts ) ) {
            return [];
        }

        $val = implode( ' ', $parts );
        return [
            '-webkit-backdrop-filter: ' . $val,
            'backdrop-filter: ' . $val,
        ];
    }

    /**
     * Build infinite animation CSS (keyframes + animation rule).
     *
     * @param array  $settings Section/element settings.
     * @param string $css_id   CSS ID for unique keyframe name.
     * @return string CSS string or empty string.
     */
    public function build_infinite_animation_css( $settings, $css_id ) {
        $anim = $settings['infinite_animation'] ?? '';
        if ( $anim === '' || $anim === 'none' ) {
            return '';
        }

        $keyframes_map = [
            'float'   => '0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)}',
            'pulse'   => '0%,100%{transform:scale(1)} 50%{transform:scale(1.05)}',
            'spin'    => '0%{transform:rotate(0)} 100%{transform:rotate(360deg)}',
            'wiggle'  => '0%,100%{transform:rotate(0)} 25%{transform:rotate(-5deg)} 75%{transform:rotate(5deg)}',
            'bounce'  => '0%,100%{transform:translateY(0)} 50%{transform:translateY(-15px)}',
            'swing'   => '0%,100%{transform:rotate(0)} 25%{transform:rotate(10deg)} 75%{transform:rotate(-10deg)}',
            'breathe' => '0%,100%{opacity:1} 50%{opacity:0.6}',
        ];

        if ( ! isset( $keyframes_map[ $anim ] ) ) {
            return '';
        }

        $speed = isset( $settings['infinite_speed'] ) ? intval( $settings['infinite_speed'] ) : 5;
        $speed = max( 1, min( 10, $speed ) );
        $duration = 10 - $speed + 1;

        $direction = $settings['infinite_direction'] ?? 'normal';
        if ( ! in_array( $direction, [ 'normal', 'reverse', 'alternate', 'alternate-reverse' ], true ) ) {
            $direction = 'normal';
        }

        $safe_id = preg_replace( '/[^a-zA-Z0-9_-]/', '', $css_id );
        $kf_name = 'olo-inf-' . $safe_id;

        $css  = '@keyframes ' . $kf_name . '{' . $keyframes_map[ $anim ] . '} ';
        $css .= '#' . esc_attr( $css_id ) . '{animation:' . $kf_name . ' ' . $duration . 's ' . $direction . ' infinite}';

        return $css;
    }

    /**
     * Build mask CSS declarations from style array.
     *
     * @param array $style Tile style.
     * @return array Array of CSS declarations with standard + webkit prefix, or empty array.
     */
    public function build_mask_css( $style ) {
        $mask_type = $style['mask_type'] ?? ( $style['mask'] ?? '' );
        if ( $mask_type === '' || $mask_type === 'none' ) {
            return [];
        }

        $svg_map = [
            'circle'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="black"/></svg>',
            'ellipse'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 80"><ellipse cx="50" cy="40" rx="50" ry="40" fill="black"/></svg>',
            'triangle' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><polygon points="50,0 100,100 0,100" fill="black"/></svg>',
            'hexagon'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><polygon points="50,0 93.3,25 93.3,75 50,100 6.7,75 6.7,25" fill="black"/></svg>',
            'star'     => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><polygon points="50,0 61,35 98,35 68,57 79,91 50,70 21,91 32,57 2,35 39,35" fill="black"/></svg>',
            'diamond'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><polygon points="50,0 100,50 50,100 0,50" fill="black"/></svg>',
            'blob'     => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><path d="M44.7,-76.4C58.8,-69.2,71.8,-59.1,79.6,-45.8C87.4,-32.6,90,-16.3,88.5,-0.9C87,14.6,81.4,29.1,73.1,41.8C64.8,54.4,53.8,65.2,40.8,72.4C27.8,79.6,12.8,83.3,-1.6,86.1C-16,88.8,-32,90.6,-44.6,83.7C-57.2,76.8,-66.4,61.2,-74.2,45.7C-82,30.2,-88.4,14.8,-87.9,0.3C-87.4,-14.2,-80,-28.5,-71,-40.7C-62,-53,-51.4,-63.3,-39,-71.1C-26.6,-78.9,-12.3,-84.2,1.8,-87.4C15.8,-90.6,30.6,-83.6,44.7,-76.4Z" transform="translate(100 100)" fill="black"/></svg>',
            'wave'     => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path d="M0,30 Q25,0 50,30 T100,30 L100,100 L0,100 Z" fill="black"/></svg>',
        ];

        if ( ! isset( $svg_map[ $mask_type ] ) ) {
            return [];
        }

        $svg_encoded = 'data:image/svg+xml,' . rawurlencode( $svg_map[ $mask_type ] );
        $size     = esc_attr( $style['mask_size'] ?? 'contain' );
        $position = esc_attr( $style['mask_position'] ?? 'center' );
        $repeat   = esc_attr( $style['mask_repeat'] ?? 'no-repeat' );

        return [
            '-webkit-mask-image: url("' . $svg_encoded . '")',
            '-webkit-mask-size: ' . $size,
            '-webkit-mask-position: ' . $position,
            '-webkit-mask-repeat: ' . $repeat,
            'mask-image: url("' . $svg_encoded . '")',
            'mask-size: ' . $size,
            'mask-position: ' . $position,
            'mask-repeat: ' . $repeat,
        ];
    }

    /**
     * Generate a unique ID for CSS selectors.
     *
     * @return string Unique identifier.
     */
    public function generate_id() {
        if ( function_exists( 'wp_generate_uuid4' ) ) {
            return wp_generate_uuid4();
        }
        return 'tile-' . uniqid() . '-' . substr( md5( mt_rand() ), 0, 8 );
    }
}
