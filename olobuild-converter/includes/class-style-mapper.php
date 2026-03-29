<?php
/**
 * Shared style mapping utilities for all converters.
 * Handles color normalization, spacing conversion, typography, shadows, borders.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Style_Mapper {

    // ─── Colors ───

    /**
     * Normalize any CSS color value to hex (#RRGGBB).
     *
     * @param  string $value  CSS color (hex, rgb, rgba, named).
     * @return string|null    Hex color or null if unparseable.
     */
    public function map_color( $value ) {
        if ( empty( $value ) || $value === 'transparent' ) {
            return null;
        }

        $value = trim( $value );

        // Already hex.
        if ( preg_match( '/^#([0-9a-f]{3}|[0-9a-f]{6}|[0-9a-f]{8})$/i', $value ) ) {
            // Expand shorthand #RGB to #RRGGBB.
            if ( strlen( $value ) === 4 ) {
                $value = '#' . $value[1] . $value[1] . $value[2] . $value[2] . $value[3] . $value[3];
            }
            // Strip alpha from #RRGGBBAA.
            if ( strlen( $value ) === 9 ) {
                $value = substr( $value, 0, 7 );
            }
            return strtoupper( $value );
        }

        // rgb(r, g, b) or rgba(r, g, b, a).
        if ( preg_match( '/rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/', $value, $m ) ) {
            return sprintf( '#%02X%02X%02X', (int) $m[1], (int) $m[2], (int) $m[3] );
        }

        // Named CSS colors (common ones).
        $named = $this->get_named_colors();
        $lower = strtolower( $value );
        if ( isset( $named[ $lower ] ) ) {
            return $named[ $lower ];
        }

        return null;
    }

    /**
     * Extract alpha from rgba() as 0-1 float.
     */
    public function extract_alpha( $value ) {
        if ( preg_match( '/rgba\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*,\s*([\d.]+)\s*\)/', $value, $m ) ) {
            return (float) $m[1];
        }
        return 1.0;
    }

    // ─── Spacing ───

    /**
     * Convert CSS spacing value to OloBuild pixel string.
     *
     * @param  mixed  $value   CSS value (string "20px", number, or Elementor-style object).
     * @param  int    $base_font_size  Base font size for em/rem conversion.
     * @return string Pixel value as string, e.g. "20".
     */
    public function map_spacing( $value, $base_font_size = 16 ) {
        if ( is_array( $value ) || is_object( $value ) ) {
            return $this->map_spacing_object( $value, $base_font_size );
        }

        if ( is_numeric( $value ) ) {
            return (string) round( (float) $value );
        }

        $value = trim( (string) $value );

        // Extract number and unit.
        if ( preg_match( '/^([\d.]+)\s*(px|em|rem|%|vw|vh)?$/i', $value, $m ) ) {
            $num  = (float) $m[1];
            $unit = strtolower( $m[2] ?? 'px' );

            switch ( $unit ) {
                case 'em':
                case 'rem':
                    return (string) round( $num * $base_font_size );
                case '%':
                    // Can't convert accurately, return reasonable estimate.
                    return (string) round( $num * 12 / 100 ); // 1200px container approx.
                case 'px':
                default:
                    return (string) round( $num );
            }
        }

        return '0';
    }

    /**
     * Handle Elementor-style spacing objects: { top, right, bottom, left, unit, isLinked }.
     */
    private function map_spacing_object( $value, $base_font_size = 16 ) {
        $value = (array) $value;

        if ( isset( $value['size'] ) ) {
            // Elementor single dimension: { size: 20, unit: 'px' }
            $unit = $value['unit'] ?? 'px';
            return $this->map_spacing( $value['size'] . $unit, $base_font_size );
        }

        // Multi-dimension: { top: 10, right: 20, bottom: 10, left: 20, unit: 'px' }
        // Return top value as representative.
        if ( isset( $value['top'] ) ) {
            $unit = $value['unit'] ?? 'px';
            return $this->map_spacing( $value['top'] . $unit, $base_font_size );
        }

        return '0';
    }

    /**
     * Extract all 4 sides from spacing object.
     * @return array [ 'top' => '10', 'right' => '20', 'bottom' => '10', 'left' => '20' ]
     */
    public function map_spacing_sides( $value, $base_font_size = 16 ) {
        $value = (array) $value;
        $unit  = $value['unit'] ?? 'px';

        return [
            'top'    => $this->map_spacing( ( $value['top'] ?? 0 ) . $unit, $base_font_size ),
            'right'  => $this->map_spacing( ( $value['right'] ?? 0 ) . $unit, $base_font_size ),
            'bottom' => $this->map_spacing( ( $value['bottom'] ?? 0 ) . $unit, $base_font_size ),
            'left'   => $this->map_spacing( ( $value['left'] ?? 0 ) . $unit, $base_font_size ),
        ];
    }

    // ─── Typography ───

    /**
     * Map font size to OloBuild string.
     */
    public function map_font_size( $value ) {
        return $this->map_spacing( $value );
    }

    /**
     * Map font weight (pass through numeric, convert names).
     */
    public function map_font_weight( $value ) {
        $names = [
            'thin'       => '100',
            'extra-light' => '200',
            'light'      => '300',
            'normal'     => '400',
            'regular'    => '400',
            'medium'     => '500',
            'semi-bold'  => '600',
            'semibold'   => '600',
            'bold'       => '700',
            'extra-bold' => '800',
            'black'      => '900',
        ];

        $lower = strtolower( trim( (string) $value ) );
        return $names[ $lower ] ?? (string) $value;
    }

    /**
     * Map text transform (direct CSS passthrough).
     */
    public function map_text_transform( $value ) {
        $valid = [ 'none', 'uppercase', 'lowercase', 'capitalize' ];
        $lower = strtolower( trim( (string) $value ) );
        return in_array( $lower, $valid, true ) ? $lower : 'none';
    }

    // ─── Shadows ───

    /**
     * Map CSS box-shadow to OloBuild preset.
     *
     * @param  string $value  CSS box-shadow value.
     * @return string OloBuild shadow preset: none, sm, md, lg, xl.
     */
    public function map_shadow( $value ) {
        if ( empty( $value ) || $value === 'none' ) {
            return 'none';
        }

        // Try to extract blur radius from CSS box-shadow.
        // Format: h-offset v-offset blur spread color
        if ( preg_match( '/[\d.]+\w*\s+[\d.]+\w*\s+([\d.]+)/', $value, $m ) ) {
            $blur = (float) $m[1];
            if ( $blur <= 3 ) return 'sm';
            if ( $blur <= 8 ) return 'md';
            if ( $blur <= 15 ) return 'lg';
            return 'xl';
        }

        return 'none';
    }

    /**
     * Map shadow from Elementor shadow object.
     */
    public function map_shadow_object( $value ) {
        if ( ! is_array( $value ) && ! is_object( $value ) ) {
            return $this->map_shadow( (string) $value );
        }

        $value = (array) $value;
        $blur  = (float) ( $value['blur'] ?? 0 );

        if ( $blur <= 0 ) return 'none';
        if ( $blur <= 3 ) return 'sm';
        if ( $blur <= 8 ) return 'md';
        if ( $blur <= 15 ) return 'lg';
        return 'xl';
    }

    // ─── Borders ───

    /**
     * Map border radius to OloBuild format.
     * OloBuild accepts: string "6" (uniform) or object { tl, tr, br, bl }.
     */
    public function map_border_radius( $value ) {
        if ( is_numeric( $value ) ) {
            return [
                'tl' => (int) $value,
                'tr' => (int) $value,
                'br' => (int) $value,
                'bl' => (int) $value,
            ];
        }

        if ( is_array( $value ) || is_object( $value ) ) {
            $v = (array) $value;
            // Elementor format: { top, right, bottom, left, unit }
            $unit = $v['unit'] ?? 'px';
            return [
                'tl' => (int) $this->map_spacing( ( $v['top'] ?? $v['tl'] ?? 0 ) . $unit ),
                'tr' => (int) $this->map_spacing( ( $v['right'] ?? $v['tr'] ?? 0 ) . $unit ),
                'br' => (int) $this->map_spacing( ( $v['bottom'] ?? $v['br'] ?? 0 ) . $unit ),
                'bl' => (int) $this->map_spacing( ( $v['left'] ?? $v['bl'] ?? 0 ) . $unit ),
            ];
        }

        // CSS shorthand string like "10px" or "10px 20px".
        $parts = preg_split( '/\s+/', trim( (string) $value ) );
        $parts = array_map( function ( $p ) { return (int) $this->map_spacing( $p ); }, $parts );

        switch ( count( $parts ) ) {
            case 1:
                return [ 'tl' => $parts[0], 'tr' => $parts[0], 'br' => $parts[0], 'bl' => $parts[0] ];
            case 2:
                return [ 'tl' => $parts[0], 'tr' => $parts[1], 'br' => $parts[0], 'bl' => $parts[1] ];
            case 3:
                return [ 'tl' => $parts[0], 'tr' => $parts[1], 'br' => $parts[2], 'bl' => $parts[1] ];
            case 4:
                return [ 'tl' => $parts[0], 'tr' => $parts[1], 'br' => $parts[2], 'bl' => $parts[3] ];
            default:
                return [ 'tl' => 0, 'tr' => 0, 'br' => 0, 'bl' => 0 ];
        }
    }

    // ─── Alignment ───

    /**
     * Map alignment value.
     */
    public function map_alignment( $value ) {
        $map = [
            'left'      => 'left',
            'center'    => 'center',
            'right'     => 'right',
            'justified' => 'left',
            'justify'   => 'left',
            'start'     => 'left',
            'end'       => 'right',
        ];
        return $map[ strtolower( trim( (string) $value ) ) ] ?? 'left';
    }

    // ─── Heading tags ───

    /**
     * Map heading tag (h1-h6).
     */
    public function map_heading_tag( $value ) {
        $valid = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'span', 'div' ];
        $lower = strtolower( trim( (string) $value ) );
        return in_array( $lower, $valid, true ) ? $lower : 'h2';
    }

    // ─── Private helpers ───

    private function get_named_colors() {
        return [
            'black'   => '#000000', 'white'   => '#FFFFFF',
            'red'     => '#FF0000', 'green'   => '#008000',
            'blue'    => '#0000FF', 'yellow'  => '#FFFF00',
            'orange'  => '#FFA500', 'purple'  => '#800080',
            'pink'    => '#FFC0CB', 'gray'    => '#808080',
            'grey'    => '#808080', 'silver'  => '#C0C0C0',
            'navy'    => '#000080', 'teal'    => '#008080',
            'maroon'  => '#800000', 'olive'   => '#808000',
            'aqua'    => '#00FFFF', 'fuchsia' => '#FF00FF',
            'lime'    => '#00FF00', 'coral'   => '#FF7F50',
            'indigo'  => '#4B0082', 'violet'  => '#EE82EE',
            'gold'    => '#FFD700', 'crimson' => '#DC143C',
            'tomato'  => '#FF6347', 'salmon'  => '#FA8072',
            'khaki'   => '#F0E68C', 'plum'    => '#DDA0DD',
            'tan'     => '#D2B48C', 'wheat'   => '#F5DEB3',
        ];
    }
}
