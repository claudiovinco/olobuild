<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Style_System {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    /**
     * Default style values.
     */
    public function get_defaults() {
        return [
            'colors' => [
                'primary'            => '#6366F1',
                'primary_contrast'   => '#FFFFFF',
                'secondary'          => '#1F2937',
                'secondary_contrast' => '#FFFFFF',
                'muted'              => '#F3F4F6',
                'muted_contrast'     => '#374151',
                'success'            => '#10B981',
                'warning'            => '#F59E0B',
                'danger'             => '#EF4444',
                'text'               => '#374151',
                'text_muted'         => '#9CA3AF',
                'background'         => '#FFFFFF',
                'border'             => '#E5E7EB',
                'link'               => '#6366F1',
            ],
            'typography' => [
                'font_family'         => '',
                'font_family_heading' => '',
                'font_size_base'      => '16px',
                'font_size_h1'        => '2.5rem',
                'font_size_h2'        => '2rem',
                'font_size_h3'        => '1.75rem',
                'font_size_h4'        => '1.5rem',
                'font_size_h5'        => '1.25rem',
                'font_size_h6'        => '1rem',
                'line_height'         => '1.6',
                'font_weight_heading' => '700',
            ],
            'layout' => [
                'border_radius'       => '4px',
                'border_radius_large' => '8px',
                'container_max_width' => '1200px',
            ],
            'google_fonts' => [],
        ];
    }

    /**
     * Get saved styles merged with defaults.
     */
    public function get_styles() {
        $saved    = get_option( 'olo_styles', [] );
        $defaults = $this->get_defaults();

        return [
            'colors'       => wp_parse_args( $saved['colors'] ?? [], $defaults['colors'] ),
            'typography'   => wp_parse_args( $saved['typography'] ?? [], $defaults['typography'] ),
            'layout'       => wp_parse_args( $saved['layout'] ?? [], $defaults['layout'] ),
            'google_fonts' => $saved['google_fonts'] ?? $defaults['google_fonts'],
        ];
    }

    /**
     * Save styles to wp_options.
     */
    public function save_styles( $styles ) {
        $sanitized = $this->sanitize_styles( $styles );
        update_option( 'olo_styles', $sanitized, false );
        return $sanitized;
    }

    /**
     * Reset to defaults.
     */
    public function reset_styles() {
        delete_option( 'olo_styles' );
        return $this->get_defaults();
    }

    /**
     * Convert border-radius value (number or {tl,tr,br,bl} object) to CSS string.
     */
    private function css_border_radius( $val, $fallback = '4px' ) {
        if ( is_array( $val ) ) {
            return sprintf( '%dpx %dpx %dpx %dpx',
                intval( $val['tl'] ?? 0 ), intval( $val['tr'] ?? 0 ),
                intval( $val['br'] ?? 0 ), intval( $val['bl'] ?? 0 ) );
        }
        $s = strval( $val );
        if ( strpos( $s, 'px' ) !== false ) return $s;
        if ( $s !== '' && $s !== '0' ) return $s . 'px';
        return $fallback;
    }

    /**
     * Sanitize all style values.
     */
    public function sanitize_styles( $styles ) {
        $sanitized = [];

        // Colors
        if ( isset( $styles['colors'] ) && is_array( $styles['colors'] ) ) {
            $sanitized['colors'] = [];
            foreach ( $styles['colors'] as $key => $value ) {
                $clean = sanitize_hex_color( $value );
                if ( $clean ) {
                    $sanitized['colors'][ sanitize_key( $key ) ] = $clean;
                }
            }
        }

        // Typography
        if ( isset( $styles['typography'] ) && is_array( $styles['typography'] ) ) {
            $sanitized['typography'] = [];
            foreach ( $styles['typography'] as $key => $value ) {
                $sanitized['typography'][ sanitize_key( $key ) ] = sanitize_text_field( $value );
            }
        }

        // Layout
        if ( isset( $styles['layout'] ) && is_array( $styles['layout'] ) ) {
            $sanitized['layout'] = [];
            foreach ( $styles['layout'] as $key => $value ) {
                $skey = sanitize_key( $key );
                if ( is_array( $value ) ) {
                    // border_radius as {tl, tr, br, bl}
                    $sanitized['layout'][ $skey ] = array_map( 'absint', $value );
                } else {
                    $sanitized['layout'][ $skey ] = sanitize_text_field( $value );
                }
            }
        }

        // Google Fonts
        if ( isset( $styles['google_fonts'] ) && is_array( $styles['google_fonts'] ) ) {
            $sanitized['google_fonts'] = array_map( 'sanitize_text_field', $styles['google_fonts'] );
            $sanitized['google_fonts'] = array_values( array_unique( $sanitized['google_fonts'] ) );
        }

        return $sanitized;
    }

    /**
     * Generate CSS with custom properties and UIkit overrides.
     */
    public function generate_css() {
        $s = $this->get_styles();
        $c = $s['colors'];
        $t = $s['typography'];
        $l = $s['layout'];

        $css = '';

        // Google Fonts import
        $fonts_import = $this->generate_google_fonts_import( $s['google_fonts'] );
        if ( $fonts_import ) {
            $css .= $fonts_import . "\n";
        }

        // Custom properties
        $css .= ".olo-template {\n";
        // Colors
        foreach ( $c as $key => $value ) {
            $prop = str_replace( '_', '-', $key );
            $css .= "  --olo-color-{$prop}: {$value};\n";
        }
        // Typography
        if ( ! empty( $t['font_family'] ) ) {
            $css .= "  --olo-font-family: {$t['font_family']};\n";
        }
        if ( ! empty( $t['font_family_heading'] ) ) {
            $css .= "  --olo-font-family-heading: {$t['font_family_heading']};\n";
        }
        $css .= "  --olo-font-size-base: {$t['font_size_base']};\n";
        for ( $i = 1; $i <= 6; $i++ ) {
            $css .= "  --olo-font-size-h{$i}: {$t['font_size_h' . $i]};\n";
        }
        $css .= "  --olo-line-height: {$t['line_height']};\n";
        $css .= "  --olo-font-weight-heading: {$t['font_weight_heading']};\n";
        // Layout
        $css .= "  --olo-border-radius: " . $this->css_border_radius( $l['border_radius'], '4px' ) . ";\n";
        $css .= "  --olo-border-radius-large: " . $this->css_border_radius( $l['border_radius_large'], '8px' ) . ";\n";
        $css .= "  --olo-container-max-width: {$l['container_max_width']};\n";
        // Shadows
        $css .= "  --olo-shadow-small: 0 1px 2px 0 rgba(0,0,0,0.05);\n";
        $css .= "  --olo-shadow-medium: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1);\n";
        $css .= "  --olo-shadow-large: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1);\n";
        $css .= "}\n\n";

        // UIkit overrides - Sections
        // background-color WITHOUT !important so inline styles (custom bg) can override
        // color WITH !important to ensure text contrast with section style
        $css .= "/* UIkit Section overrides */\n";
        $css .= ".olo-template .uk-section-primary { background-color: var(--olo-color-primary); color: var(--olo-color-primary-contrast) !important; }\n";
        $css .= ".olo-template .uk-section-primary :where(a) { color: var(--olo-color-primary-contrast) !important; }\n";
        $css .= ".olo-template .uk-section-secondary { background-color: var(--olo-color-secondary); color: var(--olo-color-secondary-contrast) !important; }\n";
        $css .= ".olo-template .uk-section-secondary :where(a) { color: var(--olo-color-secondary-contrast) !important; }\n";
        $css .= ".olo-template .uk-section-muted { background-color: var(--olo-color-muted); color: var(--olo-color-muted-contrast) !important; }\n";

        // Typography
        $css .= "\n/* Typography overrides */\n";
        $css .= ".olo-template { background-color: var(--olo-color-background); font-size: var(--olo-font-size-base); line-height: var(--olo-line-height); color: var(--olo-color-text); }\n";
        if ( ! empty( $t['font_family'] ) ) {
            $css .= ".olo-template { font-family: var(--olo-font-family); }\n";
        }
        $css .= ".olo-template h1, .olo-template .uk-h1 { font-size: var(--olo-font-size-h1); }\n";
        $css .= ".olo-template h2, .olo-template .uk-h2 { font-size: var(--olo-font-size-h2); }\n";
        $css .= ".olo-template h3, .olo-template .uk-h3 { font-size: var(--olo-font-size-h3); }\n";
        $css .= ".olo-template h4, .olo-template .uk-h4 { font-size: var(--olo-font-size-h4); }\n";
        $css .= ".olo-template h5, .olo-template .uk-h5 { font-size: var(--olo-font-size-h5); }\n";
        $css .= ".olo-template h6, .olo-template .uk-h6 { font-size: var(--olo-font-size-h6); }\n";
        $css .= ".olo-template h1, .olo-template h2, .olo-template h3, .olo-template h4, .olo-template h5, .olo-template h6 { font-weight: var(--olo-font-weight-heading); }\n";
        if ( ! empty( $t['font_family_heading'] ) ) {
            $css .= ".olo-template h1, .olo-template h2, .olo-template h3, .olo-template h4, .olo-template h5, .olo-template h6 { font-family: var(--olo-font-family-heading); }\n";
        }

        // Links
        $css .= "\n/* Link overrides */\n";
        $css .= ".olo-template a { color: var(--olo-color-link); }\n";
        $css .= ".olo-template .uk-text-muted { color: var(--olo-color-text-muted) !important; }\n";

        // Buttons
        $css .= "\n/* Button overrides */\n";
        $css .= ".olo-template .uk-button-primary { background-color: var(--olo-color-primary) !important; color: var(--olo-color-primary-contrast) !important; border-radius: var(--olo-border-radius); }\n";
        $css .= ".olo-template .uk-button-secondary { background-color: var(--olo-color-secondary) !important; color: var(--olo-color-secondary-contrast) !important; border-radius: var(--olo-border-radius); }\n";
        $css .= ".olo-template .uk-button-danger { background-color: var(--olo-color-danger) !important; color: #fff !important; border-radius: var(--olo-border-radius); }\n";
        $css .= ".olo-template .uk-button-default { border-radius: var(--olo-border-radius); }\n";

        // Alerts
        $css .= "\n/* Alert overrides */\n";
        $css .= ".olo-template .uk-alert-success { color: var(--olo-color-success); }\n";
        $css .= ".olo-template .uk-alert-warning { color: var(--olo-color-warning); }\n";
        $css .= ".olo-template .uk-alert-danger { color: var(--olo-color-danger); }\n";

        // Borders & Shadows
        $css .= "\n/* Border & Shadow overrides */\n";
        $css .= ".olo-template .uk-card { border-radius: var(--olo-border-radius-large); }\n";
        $css .= ".olo-template .uk-card-default { border-color: var(--olo-color-border); }\n";
        $css .= ".olo-template .uk-box-shadow-small { box-shadow: var(--olo-shadow-small) !important; }\n";
        $css .= ".olo-template .uk-box-shadow-medium { box-shadow: var(--olo-shadow-medium) !important; }\n";
        $css .= ".olo-template .uk-box-shadow-large { box-shadow: var(--olo-shadow-large) !important; }\n";

        // Container max-width
        $css .= "\n/* Container overrides */\n";
        $css .= ".olo-template .uk-container { max-width: var(--olo-container-max-width); }\n";

        return $css;
    }

    /**
     * Generate Google Fonts @import statement.
     */
    public function generate_google_fonts_import( $fonts = [] ) {
        if ( empty( $fonts ) ) {
            return '';
        }

        $families = [];
        foreach ( $fonts as $font ) {
            $families[] = str_replace( ' ', '+', $font ) . ':wght@300;400;500;600;700';
        }

        return '@import url("https://fonts.googleapis.com/css2?family=' . implode( '&family=', $families ) . '&display=swap");';
    }

    /**
     * Get built-in presets.
     */
    public function get_presets() {
        $defaults = $this->get_defaults();
        $base_typography = $defaults['typography'];
        $base_layout     = $defaults['layout'];

        return [
            'default' => [
                'name' => 'Default',
                'colors' => $defaults['colors'],
                'typography' => $base_typography,
                'layout' => $base_layout,
            ],
            'corporate' => [
                'name' => 'Corporate',
                'colors' => [
                    'primary'            => '#1E40AF',
                    'primary_contrast'   => '#FFFFFF',
                    'secondary'          => '#0F172A',
                    'secondary_contrast' => '#FFFFFF',
                    'muted'              => '#F1F5F9',
                    'muted_contrast'     => '#1E293B',
                    'success'            => '#059669',
                    'warning'            => '#D97706',
                    'danger'             => '#DC2626',
                    'text'               => '#1E293B',
                    'text_muted'         => '#64748B',
                    'background'         => '#FFFFFF',
                    'border'             => '#CBD5E1',
                    'link'               => '#1E40AF',
                ],
                'typography' => $base_typography,
                'layout' => $base_layout,
            ],
            'creative' => [
                'name' => 'Creative',
                'colors' => [
                    'primary'            => '#EC4899',
                    'primary_contrast'   => '#FFFFFF',
                    'secondary'          => '#7C3AED',
                    'secondary_contrast' => '#FFFFFF',
                    'muted'              => '#FDF4FF',
                    'muted_contrast'     => '#1F2937',
                    'success'            => '#10B981',
                    'warning'            => '#F59E0B',
                    'danger'             => '#EF4444',
                    'text'               => '#1F2937',
                    'text_muted'         => '#9CA3AF',
                    'background'         => '#FFFBEB',
                    'border'             => '#E9D5FF',
                    'link'               => '#EC4899',
                ],
                'typography' => $base_typography,
                'layout' => $base_layout,
            ],
            'minimal' => [
                'name' => 'Minimal',
                'colors' => [
                    'primary'            => '#000000',
                    'primary_contrast'   => '#FFFFFF',
                    'secondary'          => '#525252',
                    'secondary_contrast' => '#FFFFFF',
                    'muted'              => '#F5F5F5',
                    'muted_contrast'     => '#171717',
                    'success'            => '#22C55E',
                    'warning'            => '#EAB308',
                    'danger'             => '#EF4444',
                    'text'               => '#171717',
                    'text_muted'         => '#737373',
                    'background'         => '#FFFFFF',
                    'border'             => '#E5E5E5',
                    'link'               => '#000000',
                ],
                'typography' => $base_typography,
                'layout' => $base_layout,
            ],
            'dark' => [
                'name' => 'Dark',
                'colors' => [
                    'primary'            => '#818CF8',
                    'primary_contrast'   => '#FFFFFF',
                    'secondary'          => '#312E81',
                    'secondary_contrast' => '#E0E7FF',
                    'muted'              => '#1E293B',
                    'muted_contrast'     => '#CBD5E1',
                    'success'            => '#34D399',
                    'warning'            => '#FBBF24',
                    'danger'             => '#F87171',
                    'text'               => '#E2E8F0',
                    'text_muted'         => '#94A3B8',
                    'background'         => '#0F172A',
                    'border'             => '#334155',
                    'link'               => '#818CF8',
                ],
                'typography' => $base_typography,
                'layout' => $base_layout,
            ],
            'nature' => [
                'name' => 'Nature',
                'colors' => [
                    'primary'            => '#059669',
                    'primary_contrast'   => '#FFFFFF',
                    'secondary'          => '#064E3B',
                    'secondary_contrast' => '#D1FAE5',
                    'muted'              => '#ECFDF5',
                    'muted_contrast'     => '#1F2937',
                    'success'            => '#10B981',
                    'warning'            => '#F59E0B',
                    'danger'             => '#EF4444',
                    'text'               => '#1F2937',
                    'text_muted'         => '#6B7280',
                    'background'         => '#F0FDF4',
                    'border'             => '#A7F3D0',
                    'link'               => '#059669',
                ],
                'typography' => $base_typography,
                'layout' => $base_layout,
            ],
        ];
    }
}
