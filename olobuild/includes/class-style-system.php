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
                'font_family'              => '',
                'font_family_heading'      => '',
                'font_size_base'           => '16px',
                'font_size_h1'             => '2.5rem',
                'font_size_h2'             => '2rem',
                'font_size_h3'             => '1.75rem',
                'font_size_h4'             => '1.5rem',
                'font_size_h5'             => '1.25rem',
                'font_size_h6'             => '1rem',
                'line_height'              => '1.6',
                'font_weight_heading'      => '700',
                'letter_spacing'           => '0',
                'font_weight_body'         => '400',
                'font_size_h1_tablet'      => '',
                'font_size_h2_tablet'      => '',
                'font_size_h3_tablet'      => '',
                'font_size_h1_mobile'      => '',
                'font_size_h2_mobile'      => '',
                'font_size_h3_mobile'      => '',
                'heading_line_height'      => '1.3',
                'heading_letter_spacing'   => '0',
                'heading_text_transform'   => 'none',
            ],
            'layout' => [
                'border_radius'       => '4px',
                'border_radius_large' => '8px',
                'container_max_width' => '1200px',
            ],
            'buttons' => [
                'font_size'        => '14px',
                'font_weight'      => '600',
                'padding_x'        => '24px',
                'padding_y'        => '10px',
                'border_radius'    => '4px',
                'text_transform'   => 'none',
                'letter_spacing'   => '0',
                'hover_brightness' => '90',
            ],
            'forms' => [
                'field_bg'            => '#ffffff',
                'field_border_color'  => '#d1d5db',
                'field_border_width'  => '1',
                'field_border_radius' => '4px',
                'field_padding'       => '10px 14px',
                'field_font_size'     => '14px',
                'focus_border_color'  => '#6366F1',
                'focus_shadow'        => '0 0 0 3px rgba(99,102,241,0.15)',
                'label_font_size'     => '14px',
                'label_font_weight'   => '500',
                'label_margin_bottom' => '4px',
            ],
            'links' => [
                'color'            => '',
                'hover_color'      => '',
                'decoration'       => 'none',
                'hover_decoration' => 'underline',
            ],
            'dark_colors' => [
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
            'buttons'      => wp_parse_args( $saved['buttons'] ?? [], $defaults['buttons'] ),
            'forms'        => wp_parse_args( $saved['forms'] ?? [], $defaults['forms'] ),
            'links'        => wp_parse_args( $saved['links'] ?? [], $defaults['links'] ),
            'dark_colors'  => wp_parse_args( $saved['dark_colors'] ?? [], $defaults['dark_colors'] ),
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

        // Buttons
        if ( isset( $styles['buttons'] ) && is_array( $styles['buttons'] ) ) {
            $sanitized['buttons'] = [];
            foreach ( $styles['buttons'] as $key => $value ) {
                $sanitized['buttons'][ sanitize_key( $key ) ] = sanitize_text_field( $value );
            }
        }

        // Forms
        if ( isset( $styles['forms'] ) && is_array( $styles['forms'] ) ) {
            $sanitized['forms'] = [];
            foreach ( $styles['forms'] as $key => $value ) {
                $sanitized['forms'][ sanitize_key( $key ) ] = sanitize_text_field( $value );
            }
        }

        // Links
        if ( isset( $styles['links'] ) && is_array( $styles['links'] ) ) {
            $sanitized['links'] = [];
            foreach ( $styles['links'] as $key => $value ) {
                $skey = sanitize_key( $key );
                $clean = sanitize_hex_color( $value );
                if ( $clean ) {
                    $sanitized['links'][ $skey ] = $clean;
                } else {
                    $sanitized['links'][ $skey ] = sanitize_text_field( $value );
                }
            }
        }

        // Dark Colors
        if ( isset( $styles['dark_colors'] ) && is_array( $styles['dark_colors'] ) ) {
            $sanitized['dark_colors'] = [];
            foreach ( $styles['dark_colors'] as $key => $value ) {
                $clean = sanitize_hex_color( $value );
                if ( $clean ) {
                    $sanitized['dark_colors'][ sanitize_key( $key ) ] = $clean;
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
     * Get global colors from wp_options.
     */
    public function get_global_colors() {
        $colors = get_option( 'olo_global_colors', [] );
        return is_array( $colors ) ? $colors : [];
    }

    /**
     * Get global typography sets from wp_options.
     */
    public function get_global_typography() {
        $sets = get_option( 'olo_global_typography', [] );
        return is_array( $sets ) ? $sets : [];
    }

    /**
     * Generate CSS with custom properties and UIkit overrides.
     */
    public function generate_css() {
        $s = $this->get_styles();
        $c = $s['colors'];
        $t = $s['typography'];
        $l = $s['layout'];
        $b = $s['buttons'];
        $f = $s['forms'];
        $lk = $s['links'];

        $css = '';

        // Google Fonts import
        $fonts_import = $this->generate_google_fonts_import( $s['google_fonts'] );
        if ( $fonts_import ) {
            $css .= $fonts_import . "\n";
        }

        // Global typography Google Fonts import
        $global_typo = $this->get_global_typography();
        $global_font_families = [];
        foreach ( $global_typo as $set ) {
            $family = $set['family'] ?? '';
            if ( $family !== '' ) {
                $global_font_families[] = $family;
            }
        }
        $global_font_families = array_unique( $global_font_families );
        // Filter out families already in main google_fonts
        $existing_fonts = $s['google_fonts'] ?? [];
        $extra_families = array_diff( $global_font_families, $existing_fonts );
        if ( ! empty( $extra_families ) ) {
            $extra_import = $this->generate_google_fonts_import( array_values( $extra_families ) );
            if ( $extra_import ) {
                $css .= $extra_import . "\n";
            }
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
        // Typography enhancements
        $css .= "  --olo-letter-spacing: {$t['letter_spacing']};\n";
        $css .= "  --olo-font-weight-body: {$t['font_weight_body']};\n";
        $css .= "  --olo-heading-line-height: {$t['heading_line_height']};\n";
        $css .= "  --olo-heading-letter-spacing: {$t['heading_letter_spacing']};\n";
        $css .= "  --olo-heading-text-transform: {$t['heading_text_transform']};\n";
        // Responsive typography vars (for media queries)
        if ( ! empty( $t['font_size_h1_tablet'] ) ) {
            $css .= "  --olo-font-size-h1-tablet: {$t['font_size_h1_tablet']};\n";
        }
        if ( ! empty( $t['font_size_h2_tablet'] ) ) {
            $css .= "  --olo-font-size-h2-tablet: {$t['font_size_h2_tablet']};\n";
        }
        if ( ! empty( $t['font_size_h3_tablet'] ) ) {
            $css .= "  --olo-font-size-h3-tablet: {$t['font_size_h3_tablet']};\n";
        }
        if ( ! empty( $t['font_size_h1_mobile'] ) ) {
            $css .= "  --olo-font-size-h1-mobile: {$t['font_size_h1_mobile']};\n";
        }
        if ( ! empty( $t['font_size_h2_mobile'] ) ) {
            $css .= "  --olo-font-size-h2-mobile: {$t['font_size_h2_mobile']};\n";
        }
        if ( ! empty( $t['font_size_h3_mobile'] ) ) {
            $css .= "  --olo-font-size-h3-mobile: {$t['font_size_h3_mobile']};\n";
        }
        // Layout
        $css .= "  --olo-border-radius: " . $this->css_border_radius( $l['border_radius'], '4px' ) . ";\n";
        $css .= "  --olo-border-radius-large: " . $this->css_border_radius( $l['border_radius_large'], '8px' ) . ";\n";
        $css .= "  --olo-container-max-width: {$l['container_max_width']};\n";
        // Shadows
        $css .= "  --olo-shadow-small: 0 1px 2px 0 rgba(0,0,0,0.05);\n";
        $css .= "  --olo-shadow-medium: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1);\n";
        $css .= "  --olo-shadow-large: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1);\n";

        // Button custom properties
        $css .= "  /* Button tokens */\n";
        $css .= "  --olo-btn-font-size: {$b['font_size']};\n";
        $css .= "  --olo-btn-font-weight: {$b['font_weight']};\n";
        $css .= "  --olo-btn-padding: {$b['padding_y']} {$b['padding_x']};\n";
        $css .= "  --olo-btn-border-radius: " . $this->css_border_radius( $b['border_radius'], '4px' ) . ";\n";
        $css .= "  --olo-btn-text-transform: {$b['text_transform']};\n";
        $css .= "  --olo-btn-letter-spacing: {$b['letter_spacing']};\n";
        $css .= "  --olo-btn-hover-brightness: {$b['hover_brightness']}%;\n";

        // Form custom properties
        $css .= "  /* Form tokens */\n";
        $css .= "  --olo-form-field-bg: {$f['field_bg']};\n";
        $css .= "  --olo-form-field-border: {$f['field_border_width']}px solid {$f['field_border_color']};\n";
        $css .= "  --olo-form-field-radius: " . $this->css_border_radius( $f['field_border_radius'], '4px' ) . ";\n";
        $css .= "  --olo-form-field-padding: {$f['field_padding']};\n";
        $css .= "  --olo-form-field-font-size: {$f['field_font_size']};\n";
        $css .= "  --olo-form-focus-border: {$f['focus_border_color']};\n";
        $css .= "  --olo-form-focus-shadow: {$f['focus_shadow']};\n";
        $css .= "  --olo-form-label-font-size: {$f['label_font_size']};\n";
        $css .= "  --olo-form-label-font-weight: {$f['label_font_weight']};\n";
        $css .= "  --olo-form-label-margin-bottom: {$f['label_margin_bottom']};\n";

        // Link custom properties
        if ( ! empty( $lk['color'] ) ) {
            $css .= "  --olo-color-link-custom: {$lk['color']};\n";
        }
        if ( ! empty( $lk['hover_color'] ) ) {
            $css .= "  --olo-color-link-hover: {$lk['hover_color']};\n";
        }
        $css .= "  --olo-link-decoration: {$lk['decoration']};\n";
        $css .= "  --olo-link-hover-decoration: {$lk['hover_decoration']};\n";

        // Global Colors
        $global_colors = $this->get_global_colors();
        if ( ! empty( $global_colors ) ) {
            $css .= "  /* Global Color Palette */\n";
            foreach ( $global_colors as $gc ) {
                $id    = sanitize_key( $gc['id'] ?? '' );
                $value = sanitize_text_field( $gc['value'] ?? '' );
                if ( $id && $value ) {
                    $css .= "  --olo-color-{$id}: {$value};\n";
                }
            }
        }

        // Global Typography
        if ( ! empty( $global_typo ) ) {
            $css .= "  /* Global Typography Sets */\n";
            foreach ( $global_typo as $gt ) {
                $id = sanitize_key( $gt['id'] ?? '' );
                if ( ! $id ) continue;
                $family = sanitize_text_field( $gt['family'] ?? '' );
                $weight = sanitize_text_field( $gt['weight'] ?? '400' );
                $transform = sanitize_text_field( $gt['transform'] ?? 'none' );
                $line_height = sanitize_text_field( $gt['line_height'] ?? '1.5' );
                $letter_spacing = sanitize_text_field( $gt['letter_spacing'] ?? '0' );

                if ( $family ) {
                    $css .= "  --olo-font-{$id}-family: '{$family}', sans-serif;\n";
                }
                $css .= "  --olo-font-{$id}-weight: {$weight};\n";
                $css .= "  --olo-font-{$id}-transform: {$transform};\n";
                $css .= "  --olo-font-{$id}-line-height: {$line_height};\n";
                $css .= "  --olo-font-{$id}-letter-spacing: {$letter_spacing}px;\n";
            }
        }

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
        $css .= ".olo-template { background-color: var(--olo-color-background); font-size: var(--olo-font-size-base); line-height: var(--olo-line-height); color: var(--olo-color-text); letter-spacing: var(--olo-letter-spacing); font-weight: var(--olo-font-weight-body); }\n";
        if ( ! empty( $t['font_family'] ) ) {
            $css .= ".olo-template { font-family: var(--olo-font-family); }\n";
        }
        $css .= ".olo-template h1, .olo-template .uk-h1 { font-size: var(--olo-font-size-h1); }\n";
        $css .= ".olo-template h2, .olo-template .uk-h2 { font-size: var(--olo-font-size-h2); }\n";
        $css .= ".olo-template h3, .olo-template .uk-h3 { font-size: var(--olo-font-size-h3); }\n";
        $css .= ".olo-template h4, .olo-template .uk-h4 { font-size: var(--olo-font-size-h4); }\n";
        $css .= ".olo-template h5, .olo-template .uk-h5 { font-size: var(--olo-font-size-h5); }\n";
        $css .= ".olo-template h6, .olo-template .uk-h6 { font-size: var(--olo-font-size-h6); }\n";
        $css .= ".olo-template h1, .olo-template h2, .olo-template h3, .olo-template h4, .olo-template h5, .olo-template h6 { font-weight: var(--olo-font-weight-heading); line-height: var(--olo-heading-line-height); letter-spacing: var(--olo-heading-letter-spacing); text-transform: var(--olo-heading-text-transform); }\n";
        if ( ! empty( $t['font_family_heading'] ) ) {
            $css .= ".olo-template h1, .olo-template h2, .olo-template h3, .olo-template h4, .olo-template h5, .olo-template h6 { font-family: var(--olo-font-family-heading); }\n";
        }

        // Responsive heading typography
        $has_tablet = ! empty( $t['font_size_h1_tablet'] ) || ! empty( $t['font_size_h2_tablet'] ) || ! empty( $t['font_size_h3_tablet'] );
        $has_mobile = ! empty( $t['font_size_h1_mobile'] ) || ! empty( $t['font_size_h2_mobile'] ) || ! empty( $t['font_size_h3_mobile'] );

        if ( $has_tablet ) {
            $css .= "\n/* Responsive typography — tablet */\n";
            $css .= "@media (max-width: 960px) {\n";
            if ( ! empty( $t['font_size_h1_tablet'] ) ) {
                $css .= "  .olo-template h1, .olo-template .uk-h1 { font-size: var(--olo-font-size-h1-tablet); }\n";
            }
            if ( ! empty( $t['font_size_h2_tablet'] ) ) {
                $css .= "  .olo-template h2, .olo-template .uk-h2 { font-size: var(--olo-font-size-h2-tablet); }\n";
            }
            if ( ! empty( $t['font_size_h3_tablet'] ) ) {
                $css .= "  .olo-template h3, .olo-template .uk-h3 { font-size: var(--olo-font-size-h3-tablet); }\n";
            }
            $css .= "}\n";
        }

        if ( $has_mobile ) {
            $css .= "\n/* Responsive typography — mobile */\n";
            $css .= "@media (max-width: 640px) {\n";
            if ( ! empty( $t['font_size_h1_mobile'] ) ) {
                $css .= "  .olo-template h1, .olo-template .uk-h1 { font-size: var(--olo-font-size-h1-mobile); }\n";
            }
            if ( ! empty( $t['font_size_h2_mobile'] ) ) {
                $css .= "  .olo-template h2, .olo-template .uk-h2 { font-size: var(--olo-font-size-h2-mobile); }\n";
            }
            if ( ! empty( $t['font_size_h3_mobile'] ) ) {
                $css .= "  .olo-template h3, .olo-template .uk-h3 { font-size: var(--olo-font-size-h3-mobile); }\n";
            }
            $css .= "}\n";
        }

        // Links
        $css .= "\n/* Link overrides */\n";
        if ( ! empty( $lk['color'] ) ) {
            $css .= ".olo-template a { color: var(--olo-color-link-custom); text-decoration: var(--olo-link-decoration); }\n";
        } else {
            $css .= ".olo-template a { color: var(--olo-color-link); text-decoration: var(--olo-link-decoration); }\n";
        }
        if ( ! empty( $lk['hover_color'] ) ) {
            $css .= ".olo-template a:hover { color: var(--olo-color-link-hover); text-decoration: var(--olo-link-hover-decoration); }\n";
        } else {
            $css .= ".olo-template a:hover { text-decoration: var(--olo-link-hover-decoration); }\n";
        }
        $css .= ".olo-template .uk-text-muted { color: var(--olo-color-text-muted) !important; }\n";

        // Buttons
        $css .= "\n/* Button overrides */\n";
        $css .= ".olo-template .uk-button { font-size: var(--olo-btn-font-size); font-weight: var(--olo-btn-font-weight); padding: var(--olo-btn-padding); border-radius: var(--olo-btn-border-radius); text-transform: var(--olo-btn-text-transform); letter-spacing: var(--olo-btn-letter-spacing); }\n";
        $css .= ".olo-template .uk-button:hover { filter: brightness(var(--olo-btn-hover-brightness)); }\n";
        $css .= ".olo-template .uk-button-primary { background-color: var(--olo-color-primary) !important; color: var(--olo-color-primary-contrast) !important; }\n";
        $css .= ".olo-template .uk-button-secondary { background-color: var(--olo-color-secondary) !important; color: var(--olo-color-secondary-contrast) !important; }\n";
        $css .= ".olo-template .uk-button-danger { background-color: var(--olo-color-danger) !important; color: #fff !important; }\n";
        $css .= ".olo-template .uk-button-default { border-radius: var(--olo-btn-border-radius); }\n";

        // Form fields
        $css .= "\n/* Form field overrides */\n";
        $css .= ".olo-template input[type=\"text\"],\n.olo-template input[type=\"email\"],\n.olo-template input[type=\"tel\"],\n.olo-template input[type=\"number\"],\n.olo-template input[type=\"password\"],\n.olo-template input[type=\"url\"],\n.olo-template input[type=\"date\"],\n.olo-template input[type=\"time\"],\n.olo-template textarea,\n.olo-template select {\n";
        $css .= "  background: var(--olo-form-field-bg);\n";
        $css .= "  border: var(--olo-form-field-border);\n";
        $css .= "  border-radius: var(--olo-form-field-radius);\n";
        $css .= "  padding: var(--olo-form-field-padding);\n";
        $css .= "  font-size: var(--olo-form-field-font-size);\n";
        $css .= "}\n";
        $css .= ".olo-template input:focus, .olo-template textarea:focus, .olo-template select:focus {\n";
        $css .= "  border-color: var(--olo-form-focus-border);\n";
        $css .= "  box-shadow: var(--olo-form-focus-shadow);\n";
        $css .= "  outline: none;\n";
        $css .= "}\n";
        $css .= ".olo-template label {\n";
        $css .= "  font-size: var(--olo-form-label-font-size);\n";
        $css .= "  font-weight: var(--olo-form-label-font-weight);\n";
        $css .= "  margin-bottom: var(--olo-form-label-margin-bottom);\n";
        $css .= "}\n";

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
        $css .= ".olo-template .uk-container:not(.uk-container-expand) { max-width: var(--olo-container-max-width); }\n";

        // Dark Mode — override color variables when html.olo-dark-mode is active
        $dc = $s['dark_colors'] ?? [];
        $has_dark = false;
        foreach ( $dc as $v ) {
            if ( ! empty( $v ) ) { $has_dark = true; break; }
        }
        if ( $has_dark ) {
            $css .= "\n/* Dark Mode color overrides */\n";
            $css .= "html.olo-dark-mode .olo-template {\n";
            foreach ( $dc as $key => $value ) {
                if ( empty( $value ) ) continue;
                $prop = str_replace( '_', '-', $key );
                $css .= "  --olo-color-{$prop}: {$value};\n";
            }
            $css .= "}\n";
        }

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
            'ocean' => [
                'name' => 'Ocean',
                'colors' => [
                    'primary'            => '#0891B2',
                    'primary_contrast'   => '#FFFFFF',
                    'secondary'          => '#164E63',
                    'secondary_contrast' => '#ECFEFF',
                    'muted'              => '#ECFEFF',
                    'muted_contrast'     => '#164E63',
                    'success'            => '#14B8A6',
                    'warning'            => '#F59E0B',
                    'danger'             => '#EF4444',
                    'text'               => '#0F172A',
                    'text_muted'         => '#64748B',
                    'background'         => '#F0FDFA',
                    'border'             => '#99F6E4',
                    'link'               => '#0891B2',
                ],
                'typography' => $base_typography,
                'layout' => $base_layout,
            ],
            'portfolio' => [
                'name' => 'Portfolio',
                'colors' => [
                    'primary'            => '#F97316',
                    'primary_contrast'   => '#FFFFFF',
                    'secondary'          => '#1E293B',
                    'secondary_contrast' => '#F8FAFC',
                    'muted'              => '#F8FAFC',
                    'muted_contrast'     => '#0F172A',
                    'success'            => '#22C55E',
                    'warning'            => '#FBBF24',
                    'danger'             => '#EF4444',
                    'text'               => '#0F172A',
                    'text_muted'         => '#64748B',
                    'background'         => '#FFFFFF',
                    'border'             => '#E2E8F0',
                    'link'               => '#F97316',
                ],
                'typography' => $base_typography,
                'layout' => $base_layout,
            ],
            'blog' => [
                'name' => 'Blog',
                'colors' => [
                    'primary'            => '#8B5CF6',
                    'primary_contrast'   => '#FFFFFF',
                    'secondary'          => '#6D28D9',
                    'secondary_contrast' => '#F5F3FF',
                    'muted'              => '#FAFAF9',
                    'muted_contrast'     => '#1C1917',
                    'success'            => '#10B981',
                    'warning'            => '#F59E0B',
                    'danger'             => '#EF4444',
                    'text'               => '#292524',
                    'text_muted'         => '#78716C',
                    'background'         => '#FFFBEB',
                    'border'             => '#E7E5E4',
                    'link'               => '#7C3AED',
                ],
                'typography' => $base_typography,
                'layout' => $base_layout,
            ],
            'landing' => [
                'name' => 'Landing Page',
                'colors' => [
                    'primary'            => '#2563EB',
                    'primary_contrast'   => '#FFFFFF',
                    'secondary'          => '#7C3AED',
                    'secondary_contrast' => '#FFFFFF',
                    'muted'              => '#EFF6FF',
                    'muted_contrast'     => '#1E3A5F',
                    'success'            => '#16A34A',
                    'warning'            => '#EAB308',
                    'danger'             => '#DC2626',
                    'text'               => '#111827',
                    'text_muted'         => '#6B7280',
                    'background'         => '#FFFFFF',
                    'border'             => '#DBEAFE',
                    'link'               => '#2563EB',
                ],
                'typography' => $base_typography,
                'layout' => $base_layout,
            ],
            'ecommerce' => [
                'name' => 'E-commerce',
                'colors' => [
                    'primary'            => '#E11D48',
                    'primary_contrast'   => '#FFFFFF',
                    'secondary'          => '#0F172A',
                    'secondary_contrast' => '#F1F5F9',
                    'muted'              => '#F9FAFB',
                    'muted_contrast'     => '#111827',
                    'success'            => '#059669',
                    'warning'            => '#D97706',
                    'danger'             => '#DC2626',
                    'text'               => '#111827',
                    'text_muted'         => '#6B7280',
                    'background'         => '#FFFFFF',
                    'border'             => '#E5E7EB',
                    'link'               => '#E11D48',
                ],
                'typography' => $base_typography,
                'layout' => $base_layout,
            ],
            'luxury' => [
                'name' => 'Luxury',
                'colors' => [
                    'primary'            => '#B45309',
                    'primary_contrast'   => '#FFFFFF',
                    'secondary'          => '#1C1917',
                    'secondary_contrast' => '#FEF3C7',
                    'muted'              => '#1C1917',
                    'muted_contrast'     => '#D6D3D1',
                    'success'            => '#15803D',
                    'warning'            => '#CA8A04',
                    'danger'             => '#B91C1C',
                    'text'               => '#F5F5F4',
                    'text_muted'         => '#A8A29E',
                    'background'         => '#0C0A09',
                    'border'             => '#44403C',
                    'link'               => '#D97706',
                ],
                'typography' => $base_typography,
                'layout' => $base_layout,
            ],
        ];
    }
}
