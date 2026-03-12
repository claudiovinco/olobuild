<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Shared utility constants and helpers for all Olobuild tiles.
 * Centralizes shadow maps, border helpers, and color utilities
 * to avoid duplication across 80+ tile renderers.
 */
class Olo_Tile_Utils {

    /**
     * Standard shadow presets (card / element level).
     */
    const SHADOW_MAP = [
        'none' => 'none',
        'sm'   => '0 1px 2px rgba(0,0,0,.08)',
        'md'   => '0 4px 6px rgba(0,0,0,.12)',
        'lg'   => '0 10px 15px rgba(0,0,0,.12)',
        'xl'   => '0 20px 25px rgba(0,0,0,.15)',
    ];

    /**
     * Photo / avatar shadow presets (slightly more opaque).
     */
    const SHADOW_PHOTO = [
        'none' => 'none',
        'sm'   => '0 2px 6px rgba(0,0,0,.2)',
        'md'   => '0 4px 12px rgba(0,0,0,.3)',
        'lg'   => '0 8px 24px rgba(0,0,0,.4)',
    ];

    /**
     * Panel / dropdown shadow presets (larger, softer).
     */
    const SHADOW_PANEL = [
        'none' => 'none',
        'sm'   => '0 4px 12px rgba(0,0,0,.08)',
        'md'   => '0 8px 30px rgba(0,0,0,.12)',
        'lg'   => '0 16px 48px rgba(0,0,0,.18)',
    ];

    /**
     * Get a box-shadow CSS value from a preset key.
     *
     * @param string $key     Shadow key (none|sm|md|lg|xl).
     * @param string $variant Map variant: 'standard', 'photo', 'panel'.
     * @return string CSS box-shadow value.
     */
    public static function shadow( $key, $variant = 'standard' ) {
        switch ( $variant ) {
            case 'photo':
                return self::SHADOW_PHOTO[ $key ] ?? 'none';
            case 'panel':
                return self::SHADOW_PANEL[ $key ] ?? 'none';
            default:
                return self::SHADOW_MAP[ $key ] ?? 'none';
        }
    }

    /**
     * Get a box-shadow CSS value from tile style array.
     * Supports preset (sm/md/lg/xl) and custom (prefix_h/v/blur/spread/color/inset).
     *
     * @param array  $style   Tile style array.
     * @param string $prefix  Field prefix (e.g. 'shadow', 'card_shadow', 'widget_shadow').
     * @param string $variant Map variant for presets: 'standard', 'photo', 'panel'.
     * @return string CSS box-shadow value.
     */
    public static function shadow_value( $style, $prefix = 'shadow', $variant = 'standard' ) {
        $key = $style[ $prefix ] ?? 'none';
        if ( ! $key || $key === 'none' ) {
            return 'none';
        }
        if ( $key === 'custom' ) {
            $h      = intval( $style[ "{$prefix}_h" ] ?? 0 );
            $v      = intval( $style[ "{$prefix}_v" ] ?? 0 );
            $blur   = intval( $style[ "{$prefix}_blur" ] ?? 0 );
            $spread = intval( $style[ "{$prefix}_spread" ] ?? 0 );
            $color  = esc_attr( $style[ "{$prefix}_color" ] ?? 'rgba(0,0,0,0.15)' );
            $inset  = ! empty( $style[ "{$prefix}_inset" ] ) ? 'inset ' : '';
            return "{$inset}{$h}px {$v}px {$blur}px {$spread}px {$color}";
        }
        return self::shadow( $key, $variant );
    }

    /**
     * Build a border CSS string from width, style, color.
     *
     * @param int|string $width Border width in px.
     * @param string     $color Border color (hex).
     * @param string     $style Border style (solid, dashed, dotted).
     * @return string CSS border value or empty string.
     */
    public static function border( $width, $color, $style = 'solid' ) {
        $w = absint( $width );
        if ( $w < 1 || empty( $color ) ) {
            return '';
        }
        return $w . 'px ' . esc_attr( $style ) . ' ' . esc_attr( $color );
    }

    /**
     * Build a border-radius CSS string.
     * Accepts a single value (int/string) or an associative array with tl, tr, br, bl keys.
     *
     * @param mixed $value Single px value or array [ 'tl' => int, 'tr' => int, 'br' => int, 'bl' => int ].
     * @return string CSS border-radius value or empty string.
     */
    public static function border_radius( $value ) {
        if ( is_array( $value ) ) {
            $tl = absint( $value['tl'] ?? 0 );
            $tr = absint( $value['tr'] ?? 0 );
            $br = absint( $value['br'] ?? 0 );
            $bl = absint( $value['bl'] ?? 0 );
            if ( $tl === 0 && $tr === 0 && $br === 0 && $bl === 0 ) {
                return '';
            }
            return "{$tl}px {$tr}px {$br}px {$bl}px";
        }
        $v = absint( $value );
        return $v > 0 ? $v . 'px' : '';
    }

    /**
     * Sanitize a hex color. Returns empty string if invalid.
     *
     * @param string $color Raw color input.
     * @return string Sanitized color or empty string.
     */
    public static function safe_color( $color ) {
        $c = trim( (string) $color );
        return $c !== '' ? esc_attr( $c ) : '';
    }

    /**
     * Convert a hex color + opacity (0-100) to rgba().
     *
     * @param string    $hex     Hex color (e.g. #ff0000).
     * @param int|float $opacity Opacity 0-100.
     * @return string rgba() CSS value or empty string.
     */
    public static function hex_to_rgba( $hex, $opacity = 100 ) {
        $hex = ltrim( trim( (string) $hex ), '#' );
        if ( strlen( $hex ) === 3 ) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if ( strlen( $hex ) !== 6 ) {
            return '';
        }
        // Use intval to avoid the falsy-zero bug: parseInt("00",16) = 0 which is valid
        $r = intval( substr( $hex, 0, 2 ), 16 );
        $g = intval( substr( $hex, 2, 2 ), 16 );
        $b = intval( substr( $hex, 4, 2 ), 16 );
        $a = max( 0, min( 1, floatval( $opacity ) / 100 ) );

        if ( $a >= 1 ) {
            return '#' . $hex;
        }
        return "rgba({$r},{$g},{$b},{$a})";
    }

    /**
     * Build an <img> tag with srcset/sizes from a WordPress attachment ID.
     * Falls back to a simple <img> with just the URL if attachment not found.
     *
     * @param int    $attachment_id WP attachment ID.
     * @param string $url          Fallback image URL.
     * @param string $alt          Alt text.
     * @param string $class        CSS classes.
     * @param string $size         WP image size for src (default 'full').
     * @param string $extra_attrs  Additional HTML attributes string.
     * @return string <img> HTML tag.
     */
    public static function img_srcset( $attachment_id, $url, $alt = '', $class = '', $size = 'full', $extra_attrs = '' ) {
        $att_id = absint( $attachment_id );
        $src    = esc_url( $url );
        $alt_s  = esc_attr( $alt );
        $cls    = $class ? ' class="' . esc_attr( $class ) . '"' : '';
        $extra  = $extra_attrs ? ' ' . $extra_attrs : '';

        // If no attachment ID, try to resolve from URL
        if ( $att_id <= 0 && $src ) {
            $resolved = attachment_url_to_postid( $url );
            if ( $resolved ) {
                $att_id = $resolved;
            }
        }

        if ( $att_id > 0 ) {
            $srcset = wp_get_attachment_image_srcset( $att_id, $size );
            $sizes  = wp_get_attachment_image_sizes( $att_id, $size );
            $meta   = wp_get_attachment_metadata( $att_id );
            $w_attr = '';
            $h_attr = '';
            if ( ! empty( $meta['width'] ) ) {
                $w_attr = ' width="' . intval( $meta['width'] ) . '"';
                $h_attr = ' height="' . intval( $meta['height'] ) . '"';
            }

            if ( $srcset ) {
                // Use responsive sizes that adapt to container width
                $responsive_sizes = $sizes ?: '(max-width: 480px) 100vw, (max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw';
                return '<img src="' . $src . '" srcset="' . esc_attr( $srcset ) . '" sizes="' . esc_attr( $responsive_sizes ) . '" alt="' . $alt_s . '"' . $cls . $w_attr . $h_attr . ' loading="lazy" decoding="async"' . $extra . ' />';
            }

            // Has attachment but no srcset (e.g. SVG) — still add dimensions
            return '<img src="' . $src . '" alt="' . $alt_s . '"' . $cls . $w_attr . $h_attr . ' loading="lazy" decoding="async"' . $extra . ' />';
        }

        return '<img src="' . $src . '" alt="' . $alt_s . '"' . $cls . ' loading="lazy" decoding="async"' . $extra . ' />';
    }
}
