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
     * Sostituisce i dynamic tag (es. {{site_name}}, {{current_year}}) nei testi
     * dei tile con i valori reali al momento del rendering.
     *
     * Inserito dall'editor rich text via il dropdown "campo dinamico".
     * Applicato globalmente in Olo_Frontend_Renderer dopo il render di ogni tile.
     *
     * @param string $text HTML/testo del tile.
     * @return string Testo con tag sostituiti.
     */
    public static function process_dynamic_tags( $text ) {
        if ( ! is_string( $text ) || strpos( $text, '{{' ) === false ) {
            return $text;
        }

        $replacements = [
            '{{site_name}}'    => get_bloginfo( 'name' ),
            '{{site_tagline}}' => get_bloginfo( 'description' ),
            '{{site_url}}'     => home_url( '/' ),
            '{{current_year}}' => date( 'Y' ),
            '{{current_date}}' => date_i18n( get_option( 'date_format' ) ),
            '{{current_time}}' => date_i18n( get_option( 'time_format' ) ),
        ];

        // Tag context-aware (post corrente) — solo se esiste un post in queried object
        $post = get_post();
        if ( $post ) {
            $replacements['{{post_title}}']   = get_the_title( $post );
            $replacements['{{post_excerpt}}'] = wp_strip_all_tags( get_the_excerpt( $post ) );
            $replacements['{{author_name}}']  = get_the_author_meta( 'display_name', $post->post_author );
            $replacements['{{page_url}}']     = get_permalink( $post );
        } else {
            $replacements['{{post_title}}']   = '';
            $replacements['{{post_excerpt}}'] = '';
            $replacements['{{author_name}}']  = '';
            $replacements['{{page_url}}']     = '';
        }

        return strtr( $text, $replacements );
    }

    /**
     * Standard shadow presets (card / element level).
     * Lingua d'ombra unica "claude design": due strati navy-tinted (un contact
     * shadow stretto + un ambient morbido) invece del nero piatto a strato singolo.
     * sm/md sono i valori esatti dei REFERENCE_*.html; lg/xl ne sono l'estensione coerente.
     */
    const SHADOW_MAP = [
        'none' => 'none',
        'sm'   => '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
        'md'   => '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
        'lg'   => '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
        'xl'   => '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
    ];

    /**
     * Photo / avatar shadow presets (slightly more present). Stesso tono navy.
     */
    const SHADOW_PHOTO = [
        'none' => 'none',
        'sm'   => '0 2px 6px -1px rgba(16,24,40,.16)',
        'md'   => '0 6px 16px -4px rgba(16,24,40,.24)',
        'lg'   => '0 12px 28px -6px rgba(22,38,61,.30)',
    ];

    /**
     * Panel / dropdown shadow presets (larger, softer). Stesso tono navy.
     */
    const SHADOW_PANEL = [
        'none' => 'none',
        'sm'   => '0 4px 12px -2px rgba(16,24,40,.10)',
        'md'   => '0 8px 30px -6px rgba(16,24,40,.16)',
        'lg'   => '0 16px 48px -12px rgba(22,38,61,.22)',
    ];

    /**
     * Button shadow presets — più visibili degli standard, pensate per essere
     * visibili sotto un bottone con sfondo colorato pieno (dove sm/md della
     * mappa standard a 8-15% di alpha quasi spariscono).
     */
    const SHADOW_BUTTON = [
        'none' => 'none',
        'sm'   => '0 1px 2px rgba(15,23,42,.12), 0 4px 12px -4px rgba(15,23,42,.22)',
        'md'   => '0 2px 4px rgba(15,23,42,.14), 0 8px 20px -6px rgba(15,23,42,.30)',
        'lg'   => '0 4px 8px -2px rgba(15,23,42,.16), 0 14px 30px -8px rgba(15,23,42,.36)',
        'xl'   => '0 8px 16px -4px rgba(15,23,42,.18), 0 24px 48px -12px rgba(15,23,42,.42)',
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
            case 'button':
                return self::SHADOW_BUTTON[ $key ] ?? 'none';
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
     * Coerce a border-radius value (int OR { tl, tr, br, bl } array) to a single int.
     * Used by legacy callers that expect a scalar radius. For 4-corner objects
     * returns the max corner so symmetric usages keep their visual weight.
     *
     * @param mixed $value Raw radius input.
     * @return int Numeric radius in px.
     */
    public static function radius_int( $value ) {
        if ( is_array( $value ) ) {
            return max(
                absint( $value['tl'] ?? 0 ),
                absint( $value['tr'] ?? 0 ),
                absint( $value['br'] ?? 0 ),
                absint( $value['bl'] ?? 0 )
            );
        }
        return absint( $value );
    }

    /**
     * Tell whether a hover-radius value is "set" — i.e. the user has touched it.
     * 4-corner arrays count as set even if all corners are 0 (so the user can
     * animate to "square" via hover).
     *
     * @param mixed $value Raw hover input.
     * @return bool
     */
    public static function has_radius_hover( $value ) {
        return is_array( $value ) || ( is_string( $value ) && $value !== '' ) || ( is_int( $value ) && $value > 0 );
    }

    /**
     * Build a border-radius CSS value, NEVER short-circuiting on all-zeros.
     * Required for hover animations where the target is `0 0 0 0` and we need the
     * declaration to actually be emitted so the transition starts from the base.
     *
     * @param mixed $value Single px or array { tl, tr, br, bl }.
     * @return string CSS value (e.g. "0px 0px 0px 0px") or empty string if input is null/''.
     */
    public static function radius_force_css( $value ) {
        if ( ! self::has_radius_hover( $value ) ) return '';
        if ( is_array( $value ) ) {
            $tl = absint( $value['tl'] ?? 0 );
            $tr = absint( $value['tr'] ?? 0 );
            $br = absint( $value['br'] ?? 0 );
            $bl = absint( $value['bl'] ?? 0 );
            return "{$tl}px {$tr}px {$br}px {$bl}px";
        }
        return absint( $value ) . 'px';
    }

    /**
    /**
     * Sanitize a hex color. Returns empty string if invalid.
     *
     * @param string $color Raw color input.
     * @return string Sanitized color or empty string.
     */
    public static function safe_color( $color ) {
        $c = trim( (string) $color );
        if ( $c === '' ) {
            return '';
        }
        // Difesa-in-profondita per il contesto CSS: un valore colore legittimo
        // (hex / rgb() / hsl() / var() / color-mix() / nome / currentColor / transparent)
        // non contiene MAI caratteri di breakout CSS. Se presenti, scarta il valore:
        // esc_attr da solo non protegge dentro un blocco <style> ( ; { } @ ecc.).
        if ( preg_match( '#[;{}<>@\\\\"\']|/\*|\*/|[\x00-\x1f]#', $c ) ) {
            return '';
        }
        return esc_attr( $c );
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
    public static function img_srcset( $attachment_id, $url, $alt = '', $class = '', $size = 'full', $extra_attrs = '', $options = [] ) {
        $att_id = absint( $attachment_id );
        $src    = esc_url( $url );
        $alt_s  = esc_attr( $alt );
        $cls    = $class ? ' class="' . esc_attr( $class ) . '"' : '';
        $extra  = $extra_attrs ? ' ' . $extra_attrs : '';

        // Loading strategy & fetch priority
        $loading   = ! empty( $options['loading'] ) ? $options['loading'] : 'lazy';
        $priority  = ! empty( $options['fetchpriority'] ) ? ' fetchpriority="' . esc_attr( $options['fetchpriority'] ) . '"' : '';
        $load_attr = ' loading="' . esc_attr( $loading ) . '" decoding="async"' . $priority;

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
                $responsive_sizes = $sizes ?: '(max-width: 480px) 100vw, (max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw';
                return '<img src="' . $src . '" srcset="' . esc_attr( $srcset ) . '" sizes="' . esc_attr( $responsive_sizes ) . '" alt="' . $alt_s . '"' . $cls . $w_attr . $h_attr . $load_attr . $extra . ' />';
            }

            return '<img src="' . $src . '" alt="' . $alt_s . '"' . $cls . $w_attr . $h_attr . $load_attr . $extra . ' />';
        }

        return '<img src="' . $src . '" alt="' . $alt_s . '"' . $cls . $load_attr . $extra . ' />';
    }

    /**
     * Parse a spacing value (from FieldSpacing) to CSS shorthand.
     * Accepts: array {top,right,bottom,left}, string 'N', int N.
     * Falls back to $fallback (int) if value is missing/invalid.
     *
     * @param  mixed  $val      The spacing value from tile settings.
     * @param  int    $fallback Default uniform value if $val is empty.
     * @return string CSS shorthand, e.g. "16px 20px 16px 20px".
     */
    public static function spacing_css( $val, $fallback = 0 ) {
        if ( is_array( $val ) ) {
            $t = intval( $val['top'] ?? $fallback );
            $r = intval( $val['right'] ?? $fallback );
            $b = intval( $val['bottom'] ?? $fallback );
            $l = intval( $val['left'] ?? $fallback );
            return "{$t}px {$r}px {$b}px {$l}px";
        }
        $n = ( $val !== '' && $val !== null ) ? intval( $val ) : $fallback;
        return "{$n}px";
    }
}
