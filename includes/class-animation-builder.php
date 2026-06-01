<?php
/**
 * Olobuild Animation Builder
 *
 * Parallax, scrollspy, mouse tracking, and animation utilities
 * extracted from Olo_Frontend_Renderer.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Animation_Builder {

    /**
     * Format a parallax value: integers as int, floats with minimal decimals.
     *
     * @param mixed $val Numeric value.
     * @return string Formatted value.
     */
    public function format_parallax_value( $val ) {
        $f = floatval( $val );
        if ( $f == intval( $f ) ) {
            return strval( intval( $f ) );
        }
        return rtrim( rtrim( number_format( $f, 4, '.', '' ), '0' ), '.' );
    }

    /**
     * Build uk-parallax attribute string from a multi-stop parallax object.
     * Supports property keys: x, y, scale, rotate, opacity, blur, bgx, bgy.
     *
     * @param array $parallax Parallax config with property stops.
     * @return string Attribute string or empty.
     */
    public function build_parallax_attr_from_object( $parallax ) {
        $prop_keys = [ 'x', 'y', 'scale', 'rotate', 'opacity', 'blur', 'bgx', 'bgy' ];
        $parts = [];

        foreach ( $prop_keys as $key ) {
            if ( empty( $parallax[ $key ] ) || ! is_array( $parallax[ $key ] ) ) {
                continue;
            }

            $stops = $parallax[ $key ];
            usort( $stops, function( $a, $b ) {
                return ( $a['position'] ?? 0 ) - ( $b['position'] ?? 0 );
            });

            $count = count( $stops );
            if ( $count === 0 ) {
                continue;
            }

            $segments = [];
            foreach ( $stops as $i => $s ) {
                $v   = $this->format_parallax_value( $s['value'] );
                $pos = intval( $s['position'] ?? ( $i === $count - 1 ? 100 : 0 ) );

                $need_pct = false;
                if ( $i === 0 && $pos !== 0 ) {
                    $need_pct = true;
                } elseif ( $i === $count - 1 && $pos !== 100 ) {
                    $need_pct = true;
                } elseif ( $i > 0 && $i < $count - 1 ) {
                    $need_pct = true;
                }

                $segments[] = $need_pct ? $v . ' ' . $pos . '%' : $v;
            }
            $parts[] = $key . ': ' . implode( ',', $segments );
        }

        if ( empty( $parts ) ) {
            return '';
        }

        if ( ! empty( $parallax['start'] ) ) {
            $parts[] = 'start: ' . esc_attr( $parallax['start'] );
        }
        if ( ! empty( $parallax['end'] ) ) {
            $parts[] = 'end: ' . esc_attr( $parallax['end'] );
        }
        if ( isset( $parallax['easing'] ) && $parallax['easing'] !== null && $parallax['easing'] !== '' ) {
            $parts[] = 'easing: ' . floatval( $parallax['easing'] );
        }

        $nomobile = $parallax['nomobile'] ?? true;
        if ( $nomobile ) {
            $parts[] = 'media: @m';
        }

        return ' uk-parallax="' . implode( '; ', $parts ) . '"';
    }

    /**
     * Build uk-parallax attribute value from a background config.
     *
     * @param array $bg Background config.
     * @return string Attribute string or empty.
     */
    public function build_uk_parallax_attr( $bg ) {
        if ( empty( $bg['parallax'] ) ) {
            return '';
        }

        // New multi-stop format
        if ( is_array( $bg['parallax'] ) ) {
            return $this->build_parallax_attr_from_object( $bg['parallax'] );
        }

        // Legacy boolean format
        $parts = [];

        $bgy = isset( $bg['parallax_bgy'] ) ? intval( $bg['parallax_bgy'] ) : -200;
        if ( $bgy !== 0 ) {
            $parts[] = 'bgy: ' . $bgy;
        }

        $bgx = isset( $bg['parallax_bgx'] ) ? intval( $bg['parallax_bgx'] ) : 0;
        if ( $bgx !== 0 ) {
            $parts[] = 'bgx: ' . $bgx;
        }

        if ( ! empty( $bg['parallax_opacity'] ) ) {
            $start = floatval( $bg['parallax_opacity_start'] ?? 0.3 );
            $end   = floatval( $bg['parallax_opacity_end'] ?? 1 );
            $parts[] = 'opacity: ' . $start . ',' . $end;
        }

        if ( ! empty( $bg['parallax_scale'] ) ) {
            $start = floatval( $bg['parallax_scale_start'] ?? 1 );
            $end   = floatval( $bg['parallax_scale_end'] ?? 1.2 );
            $parts[] = 'scale: ' . $start . ',' . $end;
        }

        if ( ! empty( $bg['parallax_blur'] ) ) {
            $start = intval( $bg['parallax_blur_start'] ?? 5 );
            $end   = intval( $bg['parallax_blur_end'] ?? 0 );
            $parts[] = 'blur: ' . $start . ',' . $end;
        }

        $nomobile = $bg['parallax_nomobile'] ?? true;
        if ( $nomobile ) {
            $parts[] = 'media: @m';
        }

        if ( empty( $parts ) ) {
            return '';
        }

        return ' uk-parallax="' . implode( '; ', $parts ) . '"';
    }

    /**
     * Build uk-scrollspy attribute from advanced config.
     *
     * @param array $advanced Advanced settings.
     * @return string Attribute string or empty.
     */
    public function build_scrollspy_attr( $advanced ) {
        $animation = $advanced['scrollspy_animation'] ?? '';
        if ( empty( $animation ) ) {
            return '';
        }

        $stagger = intval( $advanced['scrollspy_stagger'] ?? 0 );

        $parts = [ 'cls: uk-animation-' . esc_attr( $animation ) ];

        $delay = intval( $advanced['scrollspy_delay'] ?? 0 );
        if ( $delay > 0 ) {
            $parts[] = 'delay: ' . $delay;
        }

        $repeat = ! empty( $advanced['scrollspy_repeat'] );
        if ( $repeat ) {
            $parts[] = 'repeat: true';
        }

        if ( $stagger > 0 ) {
            $parts[] = 'target: > *';
            $parts[] = 'delay: ' . $stagger;
        }

        return ' uk-scrollspy="' . implode( '; ', $parts ) . '"';
    }

    /**
     * Build uk-parallax attribute for element-level parallax.
     *
     * @param array $advanced Advanced settings.
     * @return string Attribute string or empty.
     */
    public function build_element_parallax_attr( $advanced ) {
        // New multi-stop format
        if ( isset( $advanced['parallax'] ) && is_array( $advanced['parallax'] ) ) {
            return $this->build_parallax_attr_from_object( $advanced['parallax'] );
        }

        // Legacy flat keys format
        $parts = [];

        $y_start = intval( $advanced['parallax_y_start'] ?? 0 );
        $y_end   = intval( $advanced['parallax_y_end'] ?? 0 );
        if ( $y_start !== 0 || $y_end !== 0 ) {
            $parts[] = 'y: ' . $y_start . ',' . $y_end;
        }

        $op_start = $advanced['parallax_opacity_start'] ?? '';
        $op_end   = $advanced['parallax_opacity_end'] ?? '';
        if ( $op_start !== '' && $op_end !== '' ) {
            $parts[] = 'opacity: ' . floatval( $op_start ) . ',' . floatval( $op_end );
        }

        $sc_start = $advanced['parallax_scale_start'] ?? '';
        $sc_end   = $advanced['parallax_scale_end'] ?? '';
        if ( $sc_start !== '' && $sc_end !== '' ) {
            $parts[] = 'scale: ' . floatval( $sc_start ) . ',' . floatval( $sc_end );
        }

        if ( empty( $parts ) ) {
            return '';
        }

        $nomobile = $advanced['parallax_nomobile'] ?? true;
        if ( $nomobile !== false ) {
            $parts[] = 'media: @m';
        }

        return ' uk-parallax="' . implode( '; ', $parts ) . '"';
    }

    /**
     * Build data attributes for mouse tracking effects (3D tilt + cursor follow).
     *
     * @param array $advanced Advanced settings.
     * @return string HTML attributes string.
     */
    public function build_mouse_attrs( $advanced ) {
        $attrs = '';
        if ( ! empty( $advanced['mouse_tilt'] ) ) {
            $intensity = intval( $advanced['mouse_tilt_intensity'] ?? 15 );
            $attrs .= ' data-olo-tilt="' . $intensity . '"';
        }
        if ( ! empty( $advanced['mouse_track'] ) ) {
            $speed = intval( $advanced['mouse_track_speed'] ?? 3 );
            $attrs .= ' data-olo-track="' . $speed . '"';
        }
        return $attrs;
    }

    /**
     * Build inline CSS for infinite (looping) animation (legacy element-level).
     *
     * @param array $advanced Advanced settings.
     * @return string CSS declaration or empty.
     */
    public function build_inline_animation_css( $advanced ) {
        $anim = $advanced['infinite_animation'] ?? 'none';
        if ( $anim === 'none' || $anim === '' ) {
            return '';
        }
        // NB: ritorna SOLO la dichiarazione `animation:` inline; i @keyframes globali
        // (olo-anim-float, ecc.) sono definiti in frontend.css. Storicamente mancavano
        // per float/float-rot → ora aggiunti al foglio. Direzione + (additive) speed.
        $valid = [ 'float', 'float-rot', 'pulse', 'spin', 'wiggle', 'bounce', 'swing', 'breathe' ];
        if ( ! in_array( $anim, $valid, true ) ) {
            return '';
        }
        $speed = floatval( $advanced['infinite_speed'] ?? 3 );
        if ( $speed <= 0 ) $speed = 3;
        $dir   = $advanced['infinite_direction'] ?? 'normal';
        if ( ! in_array( $dir, [ 'normal', 'reverse', 'alternate', 'alternate-reverse' ], true ) ) {
            $dir = 'normal';
        }
        $delay = max( 0, min( 5000, intval( $advanced['infinite_delay'] ?? 0 ) ) );
        $delay_css = $delay > 0 ? ' ' . $delay . 'ms' : '';
        $name  = 'olo-anim-' . sanitize_html_class( $anim );
        return "animation: {$name} {$speed}s ease-in-out{$delay_css} {$dir} infinite";
    }

    /**
     * Build clip-path CSS for mask/shape effects (legacy element-level).
     *
     * @param array $advanced Advanced settings.
     * @return string CSS declaration or empty.
     */
    public function build_inline_mask_css( $advanced ) {
        $mask = $advanced['mask_type'] ?? 'none';
        if ( $mask === 'none' || $mask === '' ) {
            return '';
        }
        $shapes = [
            'circle'   => 'circle(50% at 50% 50%)',
            'ellipse'  => 'ellipse(50% 40% at 50% 50%)',
            'triangle' => 'polygon(50% 0%, 100% 100%, 0% 100%)',
            'hexagon'  => 'polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%)',
            'star'     => 'polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%)',
            'diamond'  => 'polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%)',
            'blob'     => 'polygon(30% 0%, 70% 0%, 100% 30%, 100% 70%, 70% 100%, 30% 100%, 0% 70%, 0% 30%)',
        ];
        if ( $mask === 'custom' ) {
            $custom = $advanced['mask_custom'] ?? '';
            if ( $custom === '' ) return '';
            return 'clip-path: ' . $custom;
        }
        if ( ! isset( $shapes[ $mask ] ) ) return '';
        return 'clip-path: ' . $shapes[ $mask ];
    }
}
