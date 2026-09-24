<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Style_System {

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
                'primary'            => '#e1474f',
                'primary_contrast'   => '#FFFFFF',
                'secondary'          => '#16263d',
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
                'link'               => '#e1474f',
            ],
            'typography' => [
                'font_family'              => '',
                'font_family_heading'      => '',
                'font_family_mono'         => '',
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
                'border_radius'        => '4px',
                'border_radius_large'  => '8px',
                'container_max_width'  => '1200px',
                'container_narrow'     => '720px',
                'container_wide'       => '1440px',
            ],
            'spacing' => [
                'xs'  => '4px',
                'sm'  => '8px',
                'md'  => '16px',
                'lg'  => '24px',
                'xl'  => '32px',
                '2xl' => '48px',
                '3xl' => '64px',
                '4xl' => '96px',
            ],
            'section_padding' => [
                'compact'  => 'lg',
                'default'  => 'xl',
                'spacious' => '2xl',
                'between'  => 'md',
            ],
            'gutter' => [
                'desktop'      => 32,
                'tablet'       => 24,
                'mobile'       => 16,
                'side_desktop' => 32,
                'side_mobile'  => 16,
            ],
            'fluid_scaling' => [
                'enabled' => false,
                'tablet'  => 0.85,
                'mobile'  => 0.65,
            ],
            // Grain / noise overlay site-wide (texture fine sopra tutta la pagina).
            'grain' => [
                'enabled' => false,
                'opacity' => 6,   // percentuale (0-30 sensata)
                'scale'   => 180, // px del tile di rumore
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
            // Scala neutri + meta dark mode — gestiti dalla pagina admin "Palette colori".
            'neutrals' => [
                'mode'  => 'auto',
                'tint'  => 'zinc',
                'scale' => [ '#FAFAFA', '#F4F4F5', '#E4E4E7', '#A1A1AA', '#52525B', '#27272A', '#09090B' ],
            ],
            'dark_mode' => [
                'enabled'  => true,
                'strategy' => 'auto',
            ],
            // Breakpoint responsive — pagina admin "Breakpoint responsive"
            'breakpoints' => [
                [ 'id' => 'desktop_xl', 'name' => 'Desktop XL', 'min' => '1440', 'max' => '∞',    'icon' => '🖥️', 'is_default' => false ],
                [ 'id' => 'desktop',    'name' => 'Desktop',    'min' => '1200', 'max' => '1439', 'icon' => '🖥️', 'is_default' => true  ],
                [ 'id' => 'laptop',     'name' => 'Laptop',     'min' => '992',  'max' => '1199', 'icon' => '💻', 'is_default' => false ],
                [ 'id' => 'tablet',     'name' => 'Tablet',     'min' => '768',  'max' => '991',  'icon' => '📱', 'is_default' => false ],
                [ 'id' => 'mobile_l',   'name' => 'Mobile L',   'min' => '576',  'max' => '767',  'icon' => '📱', 'is_default' => false ],
                [ 'id' => 'mobile',     'name' => 'Mobile',     'min' => '0',    'max' => '575',  'icon' => '📱', 'is_default' => false ],
            ],
            'breakpoint_strategy' => 'mobile',
        ];
    }

    /**
     * Get saved styles merged with defaults.
     */
    public function get_styles() {
        $saved    = get_option( 'olobuild_styles', [] );
        $defaults = $this->get_defaults();

        return [
            'colors'          => wp_parse_args( $saved['colors'] ?? [], $defaults['colors'] ),
            'typography'      => wp_parse_args( $saved['typography'] ?? [], $defaults['typography'] ),
            'layout'          => wp_parse_args( $saved['layout'] ?? [], $defaults['layout'] ),
            'buttons'         => wp_parse_args( $saved['buttons'] ?? [], $defaults['buttons'] ),
            'forms'           => wp_parse_args( $saved['forms'] ?? [], $defaults['forms'] ),
            'links'           => wp_parse_args( $saved['links'] ?? [], $defaults['links'] ),
            'dark_colors'     => wp_parse_args( $saved['dark_colors'] ?? [], $defaults['dark_colors'] ),
            'google_fonts'    => $saved['google_fonts'] ?? $defaults['google_fonts'],
            'spacing'         => wp_parse_args( $saved['spacing'] ?? [], $defaults['spacing'] ),
            'section_padding' => wp_parse_args( $saved['section_padding'] ?? [], $defaults['section_padding'] ),
            'gutter'          => wp_parse_args( $saved['gutter'] ?? [], $defaults['gutter'] ),
            'fluid_scaling'   => wp_parse_args( $saved['fluid_scaling'] ?? [], $defaults['fluid_scaling'] ),
            'grain'           => wp_parse_args( $saved['grain'] ?? [], $defaults['grain'] ),
            'neutrals'        => wp_parse_args( $saved['neutrals'] ?? [], $defaults['neutrals'] ),
            'dark_mode'       => wp_parse_args( $saved['dark_mode'] ?? [], $defaults['dark_mode'] ),
            'breakpoints'         => ( isset( $saved['breakpoints'] ) && is_array( $saved['breakpoints'] ) ) ? $saved['breakpoints'] : $defaults['breakpoints'],
            'breakpoint_strategy' => $saved['breakpoint_strategy'] ?? $defaults['breakpoint_strategy'],
        ];
    }

    /**
     * Save styles to wp_options.
     *
     * Merge con i valori già salvati per blocco: un PUT parziale (es. solo fluid_scaling)
     * non deve cancellare gli altri blocchi (colors, typography, spacing, ecc.).
     */
    public function save_styles( $styles ) {
        $sanitized = $this->sanitize_styles( $styles );
        $existing  = get_option( 'olobuild_styles', [] );
        if ( ! is_array( $existing ) ) $existing = [];
        $merged    = array_replace( $existing, $sanitized );
        update_option( 'olobuild_styles', $merged, false );

        // Allinea i global color dei ruoli core al valore appena salvato (vedi sync_global_palette).
        if ( isset( $sanitized['colors'] ) && is_array( $sanitized['colors'] ) ) {
            $this->sync_global_palette( $sanitized['colors'] );

            // Un ruolo brand cambiato nel pannello deve cambiare anche in modalità scura:
            // il blocco `html.olo-dark-mode .olo-template` ha specificità maggiore di
            // `.olo-template`, quindi con dark_colors fermi ai valori del tema importato le
            // modifiche ai Colori del brand non si vedevano affatto sui siti in dark mode.
            // Propago SOLO i ruoli toccati adesso, così una palette scura personalizzata
            // sugli altri ruoli resta intatta.
            $before  = ( isset( $existing['colors'] ) && is_array( $existing['colors'] ) ) ? $existing['colors'] : [];
            $touched = [];
            foreach ( [ 'primary', 'primary_contrast', 'secondary', 'secondary_contrast', 'link' ] as $role ) {
                if ( isset( $sanitized['colors'][ $role ] )
                    && ( $before[ $role ] ?? null ) !== $sanitized['colors'][ $role ] ) {
                    $touched[] = $role;
                }
            }
            if ( $touched ) {
                $this->sync_dark_palette( $sanitized['colors'], $touched );
                $merged = get_option( 'olobuild_styles', $merged );
            }
        }

        return $merged;
    }

    /**
     * Allinea olo_global_colors ai colori del tema/palette (ruoli core).
     *
     * In generate_css i olo_global_colors[id core] sono emessi DOPO olo_styles.colors e VINCONO
     * nel CSS: se restano placeholder (es. import tema che scrive solo olo_styles) SOVRASCRIVONO
     * la palette del cliente. Questo li tiene allineati, qualunque sia il flusso (UI, API, import).
     * accent/accent-2 (senza equivalente diretto in colors) seguono primary/secondary. Idempotente.
     *
     * @param array $colors  blocco olo_styles['colors'] (primary/secondary/...).
     */
    public function sync_global_palette( $colors ) {
        if ( ! is_array( $colors ) || empty( $colors ) ) {
            return;
        }
        $fallbacks = self::accenti_della_palette( $colors );
        $gc = get_option( 'olobuild_global_colors', [] );
        if ( ! is_array( $gc ) || ! $gc ) {
            return;
        }
        $changed = false;
        foreach ( $gc as &$g ) {
            $id = isset( $g['id'] ) ? $g['id'] : '';
            if ( '' === $id ) {
                continue;
            }
            $val = $colors[ $id ] ?? ( $fallbacks[ $id ] ?? null );
            if ( null !== $val && ( ! isset( $g['value'] ) || $g['value'] !== $val ) ) {
                $g['value'] = $val;
                $changed    = true;
            }
        }
        unset( $g );
        if ( $changed ) {
            update_option( 'olobuild_global_colors', $gc, false );
        }
    }

    /**
     * I colori globali che seguono la palette senza esserne una chiave: accent
     * segue primary, accent-2 / accent_2 seguono secondary (se lo stile non ne
     * ha di suoi). null = nessun valore da dare.
     *
     * @param array $colors blocco olobuild_styles['colors'].
     * @return array [ id => valore|null ]
     */
    private static function accenti_della_palette( $colors ) {
        $colors = is_array( $colors ) ? $colors : [];
        return [
            'accent'   => $colors['accent']   ?? ( $colors['primary']   ?? null ),
            'accent-2' => $colors['accent-2'] ?? ( $colors['secondary'] ?? null ),
            'accent_2' => $colors['accent_2'] ?? ( $colors['secondary'] ?? null ),
        ];
    }

    /**
     * Allinea i ruoli BRAND della palette in modalità scura (olo_styles['dark_colors']) ai
     * colori del tema. I default generici (indaco/slate) farebbero rendere il primario, gli
     * accenti e i link in indaco quando html.olo-dark-mode è attivo, ignorando il tema. Tocca
     * SOLO i ruoli brand: i neutri dark (background/text/border) restano (dark mode resta scuro).
     * Usato dall'IMPORT di un tema (tutti i ruoli brand) e da save_styles() con $only = i soli
     * ruoli appena modificati nel pannello. Idempotente.
     *
     * @param array      $colors blocco olo_styles['colors'].
     * @param array|null $only   sottoinsieme di ruoli brand da propagare; null = tutti.
     */
    public function sync_dark_palette( $colors, $only = null ) {
        if ( ! is_array( $colors ) || empty( $colors ) ) {
            return;
        }
        $st = get_option( 'olobuild_styles', [] );
        if ( ! is_array( $st ) ) {
            return;
        }
        $dc    = ( isset( $st['dark_colors'] ) && is_array( $st['dark_colors'] ) ) ? $st['dark_colors'] : [];
        $brand = [ 'primary', 'primary_contrast', 'secondary', 'secondary_contrast', 'link' ];
        if ( is_array( $only ) && $only ) {
            $brand = array_values( array_intersect( $brand, $only ) );
        }
        $changed = false;
        foreach ( $brand as $k ) {
            if ( isset( $colors[ $k ] ) && ( ! isset( $dc[ $k ] ) || $dc[ $k ] !== $colors[ $k ] ) ) {
                $dc[ $k ]  = $colors[ $k ];
                $changed   = true;
            }
        }
        if ( $changed ) {
            $st['dark_colors'] = $dc;
            update_option( 'olobuild_styles', $st, false );
        }
    }

    /**
     * Reset to defaults.
     */
    public function reset_styles() {
        delete_option( 'olobuild_styles' );
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
        if ( str_contains( $s, 'px' ) ) return $s;
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

        // Spacing scale (xs..4xl) — pass-through with sanitize_text_field
        if ( isset( $styles['spacing'] ) && is_array( $styles['spacing'] ) ) {
            $sanitized['spacing'] = [];
            foreach ( $styles['spacing'] as $key => $value ) {
                $skey = preg_replace( '/[^a-z0-9_]/', '', strtolower( (string) $key ) );
                if ( $skey !== '' ) {
                    $sanitized['spacing'][ $skey ] = sanitize_text_field( $value );
                }
            }
        }

        // Border radius scale
        if ( isset( $styles['border_radius_scale'] ) && is_array( $styles['border_radius_scale'] ) ) {
            $sanitized['border_radius_scale'] = [];
            foreach ( $styles['border_radius_scale'] as $key => $value ) {
                $sanitized['border_radius_scale'][ sanitize_key( $key ) ] = sanitize_text_field( $value );
            }
        }

        // Shadows
        if ( isset( $styles['shadows'] ) && is_array( $styles['shadows'] ) ) {
            $sanitized['shadows'] = [];
            foreach ( $styles['shadows'] as $key => $value ) {
                $sanitized['shadows'][ sanitize_key( $key ) ] = sanitize_text_field( $value );
            }
        }

        // Section padding (chiavi mappano a spacing scale: xs..4xl)
        if ( isset( $styles['section_padding'] ) && is_array( $styles['section_padding'] ) ) {
            $allowed_tokens = [ 'xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl' ];
            $sanitized['section_padding'] = [];
            foreach ( $styles['section_padding'] as $key => $value ) {
                $skey = sanitize_key( $key );
                $sval = sanitize_text_field( $value );
                if ( in_array( $sval, $allowed_tokens, true ) ) {
                    $sanitized['section_padding'][ $skey ] = $sval;
                }
            }
        }

        // Gutter responsive (numeric px)
        if ( isset( $styles['gutter'] ) && is_array( $styles['gutter'] ) ) {
            $sanitized['gutter'] = [];
            foreach ( $styles['gutter'] as $key => $value ) {
                $skey = sanitize_key( $key );
                $sanitized['gutter'][ $skey ] = max( 0, min( 120, absint( $value ) ) );
            }
        }

        // Fluid scaling
        if ( isset( $styles['fluid_scaling'] ) && is_array( $styles['fluid_scaling'] ) ) {
            $fs = $styles['fluid_scaling'];
            $sanitized['fluid_scaling'] = [
                'enabled' => ! empty( $fs['enabled'] ),
                'tablet'  => max( 0.3, min( 1.0, (float) ( $fs['tablet'] ?? 0.85 ) ) ),
                'mobile'  => max( 0.3, min( 1.0, (float) ( $fs['mobile'] ?? 0.65 ) ) ),
            ];
        }

        // Grain / noise overlay
        if ( isset( $styles['grain'] ) && is_array( $styles['grain'] ) ) {
            $gr = $styles['grain'];
            $sanitized['grain'] = [
                'enabled' => ! empty( $gr['enabled'] ),
                'opacity' => max( 0, min( 30, absint( $gr['opacity'] ?? 6 ) ) ),
                'scale'   => max( 60, min( 400, absint( $gr['scale'] ?? 180 ) ) ),
            ];
        }

        // Neutrals (scala grigi + modalità/tinta) — pagina admin "Palette colori"
        if ( isset( $styles['neutrals'] ) && is_array( $styles['neutrals'] ) ) {
            $nz    = $styles['neutrals'];
            $clean = [];
            if ( isset( $nz['mode'] ) ) {
                $clean['mode'] = in_array( $nz['mode'], [ 'auto', 'manual' ], true ) ? $nz['mode'] : 'auto';
            }
            if ( isset( $nz['tint'] ) ) {
                $tints         = [ 'slate', 'gray', 'zinc', 'neutral', 'stone' ];
                $clean['tint'] = in_array( $nz['tint'], $tints, true ) ? $nz['tint'] : 'zinc';
            }
            if ( isset( $nz['scale'] ) && is_array( $nz['scale'] ) ) {
                $scale = [];
                foreach ( $nz['scale'] as $hex ) {
                    $h = sanitize_hex_color( $hex );
                    if ( $h ) {
                        $scale[] = $h;
                    }
                }
                if ( $scale ) {
                    $clean['scale'] = $scale;
                }
            }
            if ( $clean ) {
                $sanitized['neutrals'] = $clean;
            }
        }

        // Dark mode meta (enabled + strategy) — pagina admin "Palette colori"
        if ( isset( $styles['dark_mode'] ) && is_array( $styles['dark_mode'] ) ) {
            $dm         = $styles['dark_mode'];
            $strategies = [ 'auto', 'manual', 'luminance' ];
            $strategy   = sanitize_text_field( $dm['strategy'] ?? 'auto' );
            $sanitized['dark_mode'] = [
                'enabled'  => ! empty( $dm['enabled'] ),
                'strategy' => in_array( $strategy, $strategies, true ) ? $strategy : 'auto',
            ];
        }

        // Breakpoints (lista device) + strategia — pagina admin "Breakpoint responsive"
        if ( isset( $styles['breakpoints'] ) && is_array( $styles['breakpoints'] ) ) {
            $clean_bps = [];
            foreach ( $styles['breakpoints'] as $bp ) {
                if ( ! is_array( $bp ) ) {
                    continue;
                }
                $clean_bps[] = [
                    'id'         => sanitize_key( $bp['id'] ?? '' ),
                    'name'       => sanitize_text_field( $bp['name'] ?? '' ),
                    'min'        => sanitize_text_field( (string) ( $bp['min'] ?? '0' ) ),
                    'max'        => sanitize_text_field( (string) ( $bp['max'] ?? '∞' ) ), // stringa: accetta '∞'
                    'icon'       => sanitize_text_field( $bp['icon'] ?? '' ),
                    'is_default' => ! empty( $bp['is_default'] ),
                ];
            }
            if ( $clean_bps ) {
                $sanitized['breakpoints'] = $clean_bps;
            }
        }
        if ( isset( $styles['breakpoint_strategy'] ) ) {
            $sanitized['breakpoint_strategy'] = in_array( $styles['breakpoint_strategy'], [ 'mobile', 'desktop' ], true ) ? $styles['breakpoint_strategy'] : 'mobile';
        }

        return $sanitized;
    }

    /**
     * Get global colors from wp_options.
     */
    public function get_global_colors() {
        $colors = get_option( 'olobuild_global_colors', [] );
        return is_array( $colors ) ? $colors : [];
    }

    /**
     * Alias che generate_css() emette SEMPRE nella regola .olo-template, in
     * quest'ordine: i nomi usati dalle tile, mappati sui token del tema (vedi
     * _olo-tokens.scss). Fonte unica anche per id_colori_dello_stile().
     */
    const ALIAS_COLORI = [
        'on-primary'  => 'var(--olo-color-primary-contrast, #ffffff)',
        'text-soft'   => 'var(--olo-color-text-muted, #6b7280)',
        'text-faint'  => 'var(--olo-color-text-muted, #94a3b8)',
        'surface'     => 'var(--olo-color-background, #ffffff)',
        'surface-alt' => 'var(--olo-color-muted, #f6f7f9)',
        'error'       => 'var(--olo-color-danger, #b42318)',
        'info'        => '#2563eb',
    ];

    /**
     * I ruoli GLOBALI delle tile che lo stile non emette: accent, dark, light
     * (GLOBAL in oloTileDefaults.js, TOKEN_MAPPING) e accent-2 / accent_2 (che
     * sync_global_palette tiene allineati). I default delle tile li usano con
     * la riserva, var(--olo-color-accent, #f4a23b): un colore globale con uno di
     * questi id ricolora tutte le tile che non hanno scelto un colore.
     */
    const RUOLI_GLOBALI_TILE = [ 'accent', 'accent-2', 'accent_2', 'dark', 'light' ];

    /**
     * I ruoli che i default delle tile leggono DAVVERO senza salvarli:
     * var(--olo-color-accent, …), var(--olo-color-dark, …) e
     * var(--olo-color-light, …) in decine di renderer di includes/tiles e nei
     * gemelli in src/ (tile Vue, oloTileDefaults.js, preset). Un colore globale
     * con uno di questi id colora ogni tile che non ne ha scelto uno, anche se
     * nessun template lo nomina (uso_colore(): 'ruolo_tile'). accent-2 e
     * accent_2 non li legge nessun default: --olo-color-accent-2 e
     * --olo-color-accent_2 non compaiono nel codice delle tile (25 set 2026).
     */
    const RUOLI_LETTI_DAI_DEFAULT = [ 'accent', 'dark', 'light' ];

    /**
     * Gli id dei colori globali la cui variabile il CSS emette GIÀ dallo stile:
     * le chiavi dei colori dello stile passato più i predefiniti, che
     * get_styles() aggiunge sempre, col nome che prendono nella variabile
     * (text_muted esce --olo-color-text-muted), più gli alias fissi
     * (ALIAS_COLORI: error, info, surface…). Nella regola .olo-template
     * generate_css() (e il gemello in src/stores/styles.js) scrive i colori
     * dello stile, poi i colori globali, poi gli alias. Un colore globale con
     * l'id di una chiave dello stile esce DOPO e la copre. Uno con l'id di un
     * alias esce PRIMA e non agisce (vince l'alias): tenerlo non serve, e fra
     * i colori extra mostrerebbe un valore diverso da quello reso. La forma con
     * «_» (text_muted) non è né l'una né l'altra: un colore globale esce col
     * suo id com'è (--olo-color-text_muted), una variabile diversa che non
     * copre niente.
     *
     * @param array $colors blocco olobuild_styles['colors'].
     * @return array [ id => true ]
     */
    public static function id_colori_dello_stile( $colors ) {
        $defaults = self::instance()->get_defaults();
        $chiavi   = array_merge( array_keys( $defaults['colors'] ), is_array( $colors ) ? array_keys( $colors ) : [] );
        $ids      = [];
        foreach ( $chiavi as $k ) {
            $ids[ str_replace( '_', '-', (string) $k ) ] = true;
        }
        foreach ( array_keys( self::ALIAS_COLORI ) as $alias ) {
            $ids[ $alias ] = true;
        }
        return $ids;
    }

    /**
     * Import (sito): i colori globali del pacchetto più quelli del sito che il
     * pacchetto non porta, NASCOSTI. Le tile e i template già qui che usano
     * var(--olo-color-<id>) non perdono il colore: tolti dalla lista, sparivano
     * dal CSS in silenzio. Non restano:
     * - quelli la cui variabile il CSS emette già dallo stile
     *   (id_colori_dello_stile()): con l'id di una chiave dei colori dello
     *   stile (primary, text-muted…) coprirebbero i colori appena importati;
     *   con quello di un alias fisso (error, info, surface…) non agirebbero
     *   (l'alias esce dopo e vince) e fra gli extra mostrerebbero un valore
     *   diverso da quello reso;
     * - i ruoli globali delle tile (RUOLI_GLOBALI_TILE: accent, accent-2,
     *   accent_2, dark, light) che il contenuto PROPRIO del sito non nomina:
     *   uso_colore() senza i template e i global widget appena importati
     *   (template, widget globali, test A/B, sezioni salvate, stili globali;
     *   gli stili solo se l'import non li ha sostituiti, $stili_importati).
     *   Un colore nascosto resta nel CSS e i default delle tile lo leggono
     *   (var(--olo-color-dark, #16263d), con riserve diverse da tile a tile): i
     *   template importati non renderebbero più come sul sito d'origine, dove
     *   quel colore non c'era e valeva la riserva. Il compromesso: una tile del
     *   sito che il ruolo lo prendeva solo dal suo default, senza nominarlo,
     *   torna alla sua riserva, come faceva l'import prima che tenesse i
     *   colori del sito e come fa il ripristino di una versione dello stile.
     * Un ruolo che il sito nomina resta, nascosto: accent, accent-2 e accent_2
     * col valore che sync_global_palette() darebbe loro coi colori importati
     * (il primary e il secondary del pacchetto: col valore di prima le tile che
     * li usano mostrerebbero la palette vecchia), dark e light col loro. Gli
     * altri colori del sito restano, nascosti, come sono. I colori del
     * pacchetto restano come arrivano. Da chiamare DOPO aver scritto gli stili e
     * i template importati e PRIMA di scrivere la lista.
     *
     * Gli stili globali si leggono come sono in quel momento. Se il pacchetto
     * ne porta, sono già i suoi e quelli del sito non ci sono più (restano
     * solo nell'istantanea dell'import): con $stili_importati non contano,
     * come i template appena importati. Se non ne porta, restano quelli del
     * sito e contano.
     *
     * @param array $importati          Colori globali del pacchetto (già ripuliti).
     * @param int[] $template_importati Id dei template appena creati dall'import.
     * @param int[] $widget_importati   Id dei global widget appena creati dall'import.
     * @param bool  $stili_importati    true = l'import ha appena sostituito gli stili globali.
     * @return array La lista da salvare.
     */
    public function con_colori_del_sito( $importati, $template_importati = [], $widget_importati = [], $stili_importati = false ) {
        $out           = array_values( is_array( $importati ) ? $importati : [] );
        $nel_pacchetto = [];
        foreach ( $out as $c ) {
            if ( is_array( $c ) && ! empty( $c['id'] ) && is_scalar( $c['id'] ) ) {
                $nel_pacchetto[ (string) $c['id'] ] = true;
            }
        }
        $colori  = $this->get_styles()['colors'];
        $ruoli   = self::id_colori_dello_stile( $colori );
        $accenti = self::accenti_della_palette( $colori );
        $attuali = get_option( 'olobuild_global_colors', [] );
        foreach ( is_array( $attuali ) ? $attuali : [] as $c ) {
            if ( ! is_array( $c ) || empty( $c['id'] ) || ! is_scalar( $c['id'] ) ) {
                continue;
            }
            $id = (string) $c['id'];
            if ( isset( $nel_pacchetto[ $id ] ) || isset( $ruoli[ $id ] ) ) {
                continue;
            }
            if ( in_array( $id, self::RUOLI_GLOBALI_TILE, true )
                && ! self::nominato_dal_sito( self::uso_colore( $id, $template_importati, $widget_importati, false ), ! $stili_importati ) ) {
                continue;
            }
            if ( isset( $accenti[ $id ] ) ) {
                $c['value'] = $accenti[ $id ];
            }
            $c['hidden'] = true;
            $out[]       = $c;
        }
        return $out;
    }

    /**
     * Un colore che il contenuto del sito nomina esplicitamente: template,
     * widget globali, test A/B, sezioni salvate o (con $con_stili) stili
     * globali. Non conta 'ruolo_tile' (i default delle tile) né le revisioni.
     *
     * @param array $uso       Risultato di uso_colore().
     * @param bool  $con_stili false = gli stili globali non contano (sono quelli appena importati).
     */
    private static function nominato_dal_sito( $uso, $con_stili = true ) {
        return ( (int) $uso['template_count'] + (int) $uso['widgets'] + (int) $uso['ab_tests'] + (int) $uso['sezioni'] ) > 0
            || ( $con_stili && ! empty( $uso['styles'] ) );
    }

    /**
     * Dove è usato il colore globale $id. Fonte unica della rotta
     * GET global-colors/<id>/usage («Elimina» in Configurazione › Palette) e
     * dell'import del sito (con_colori_del_sito()).
     *
     * Una tile salva il colore scelto come var(--olo-color-<id>): eliminare il
     * colore toglie la variabile dal CSS e quelle tile perdono il colore, in
     * silenzio, su pagine che nessuno sta guardando. Il token si cerca nei
     * template (contenuto e impostazioni di pagina; header, footer e popup sono
     * template anche loro), nei global widget, nei test A/B, nelle sezioni
     * salvate della libreria (option olobuild_user_templates) e negli stili
     * globali. Le revisioni si contano a parte (non sono in 'total'):
     * ripristinarne una riporta il token. 'ruolo_tile' dice che i default delle
     * tile leggono quel colore anche dove nessuno lo nomina
     * (RUOLI_LETTI_DAI_DEFAULT): conta 1 in 'total'.
     *
     * Il LIKE trova anche gli id che iniziano allo stesso modo (accent in
     * accent-2, c1 in c10): il confine dell'id si controlla dopo, sul testo.
     *
     * @param string $id                   Id del colore globale.
     * @param int[]  $escludi_template_ids Template da non contare (l'import: quelli appena creati).
     * @param int[]  $escludi_widget_ids   Global widget da non contare (l'import: quelli appena creati).
     * @param bool   $con_revisioni        false = le revisioni non si cercano ('revisions' resta 0).
     * @return array [ 'id', 'total', 'template_count', 'templates' (al massimo 10: id, title, type),
     *                 'widgets', 'ab_tests', 'sezioni', 'styles', 'revisions', 'ruolo_tile' ]
     */
    public static function uso_colore( $id, $escludi_template_ids = [], $escludi_widget_ids = [], $con_revisioni = true ) {
        global $wpdb;

        $id  = sanitize_key( (string) $id );
        $uso = [
            'id'             => $id,
            'total'          => 0,
            'template_count' => 0,
            'templates'      => [],
            'widgets'        => 0,
            'ab_tests'       => 0,
            'sezioni'        => 0,
            'styles'         => false,
            'revisions'      => 0,
            'ruolo_tile'     => in_array( $id, self::RUOLI_LETTI_DAI_DEFAULT, true ),
        ];
        if ( '' === $id ) {
            return $uso;
        }

        $token     = '--olo-color-' . $id;
        $like      = '%' . $wpdb->esc_like( $token ) . '%';
        $confine   = '/' . preg_quote( $token, '/' ) . '(?![A-Za-z0-9_-])/';
        $escludi_t = array_fill_keys( array_map( 'intval', (array) $escludi_template_ids ), true );
        $escludi_w = array_fill_keys( array_map( 'intval', (array) $escludi_widget_ids ), true );

        $t_templates = Olobuild_Database::table( 'templates' );
        $t_widgets   = Olobuild_Database::table( 'global_widgets' );
        $t_revisions = Olobuild_Database::table( 'revisions' );
        $t_ab        = Olobuild_Database::table( 'ab_tests' );

        // Tabelle custom del plugin ({prefix}olobuild_*); nessun equivalente WP_Query; conteggio al
        // momento (non cacheabile). Solo nomi tabella da $wpdb->prefix interpolati, il token passa da prepare.
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $righe = $wpdb->get_results(
            $wpdb->prepare( "SELECT id, title, type, content, settings FROM {$t_templates} WHERE content LIKE %s OR settings LIKE %s ORDER BY title", $like, $like ),
            ARRAY_A
        );
        $templates = [];
        foreach ( (array) $righe as $r ) {
            if ( isset( $escludi_t[ (int) $r['id'] ] ) ) {
                continue;
            }
            if ( preg_match( $confine, (string) $r['content'] ) || preg_match( $confine, (string) $r['settings'] ) ) {
                $templates[] = [
                    'id'    => (int) $r['id'],
                    'title' => (string) $r['title'],
                    'type'  => (string) $r['type'],
                ];
            }
        }

        $righe = $wpdb->get_results( $wpdb->prepare( "SELECT id, tile_data FROM {$t_widgets} WHERE tile_data LIKE %s", $like ), ARRAY_A );
        foreach ( (array) $righe as $r ) {
            if ( ! isset( $escludi_w[ (int) $r['id'] ] ) && preg_match( $confine, (string) $r['tile_data'] ) ) {
                $uso['widgets']++;
            }
        }

        // La tabella dei test A/B nasce al primo test: se non c'è, non si interroga.
        if ( strtolower( (string) $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $t_ab ) ) ) ) === strtolower( $t_ab ) ) {
            $righe = $wpdb->get_col( $wpdb->prepare( "SELECT variants FROM {$t_ab} WHERE variants LIKE %s", $like ) );
            foreach ( (array) $righe as $dati ) {
                if ( preg_match( $confine, (string) $dati ) ) {
                    $uso['ab_tests']++;
                }
            }
        }

        // Revisioni: solo un conteggio (possono essere molte e grandi). Il token
        // seguito da ')' ',' o spazio è il confine di un var() scritto da una tile.
        if ( $con_revisioni ) {
            $uso['revisions'] = (int) $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM {$t_revisions} WHERE content LIKE %s OR content LIKE %s OR content LIKE %s",
                    '%' . $wpdb->esc_like( $token . ')' ) . '%',
                    '%' . $wpdb->esc_like( $token . ',' ) . '%',
                    '%' . $wpdb->esc_like( $token . ' ' ) . '%'
                )
            );
        }
        // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter

        // Sezioni salvate nella libreria («Salva come template» di una sezione, fra i
        // «Personali»: Olobuild_Template_Library::save_user_template): stanno in
        // un'option, col contenuto com'è arrivato dal builder (un array di sezioni).
        $sezioni = get_option( 'olobuild_user_templates', [] );
        foreach ( is_array( $sezioni ) ? $sezioni : [] as $sezione ) {
            $contenuto = is_array( $sezione ) ? ( $sezione['content'] ?? null ) : null;
            if ( null !== $contenuto && preg_match( $confine, is_string( $contenuto ) ? $contenuto : (string) wp_json_encode( $contenuto ) ) ) {
                $uso['sezioni']++;
            }
        }

        $uso['styles']         = (bool) preg_match( $confine, (string) wp_json_encode( get_option( 'olobuild_styles', [] ) ) );
        $uso['template_count'] = count( $templates );
        $uso['templates']      = array_slice( $templates, 0, 10 );
        $uso['total']          = $uso['template_count'] + $uso['widgets'] + $uso['ab_tests'] + $uso['sezioni']
            + ( $uso['styles'] ? 1 : 0 ) + ( $uso['ruolo_tile'] ? 1 : 0 );
        return $uso;
    }

    /**
     * Get global typography sets from wp_options.
     */
    public function get_global_typography() {
        $sets = get_option( 'olobuild_global_typography', [] );
        return is_array( $sets ) ? $sets : [];
    }

    /**
     * Generate CSS with custom properties and UIkit overrides.
     */
    public function generate_css() {
        // Memoizzazione per-request: nello stesso page-load il CSS globale viene richiesto
        // piu' volte (renderer + footer + search-results integration). Il ricalcolo e'
        // costoso (font import, risoluzione colori, override UIkit) -> lo si fa una volta sola.
        static $memo = null;
        if ( $memo !== null ) {
            return $memo;
        }

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

        // Famiglie della typography base (testo/titoli/mono): vanno self-hostate
        // anche quando non compaiono in google_fonts (es. scelte dal pannello cfg
        // → Typography, che salva solo il blocco typography). I generici di
        // sistema vengono saltati; Font_Host fallisce comunque in modo pulito
        // sui nomi non-Google.
        $system_families = [ 'system-ui', 'sans-serif', 'serif', 'monospace', 'ui-monospace', 'inherit', 'georgia', 'arial', 'verdana', 'tahoma', 'consolas', 'menlo', 'courier new', 'times new roman' ];
        $typo_families = [];
        foreach ( [ 'font_family', 'font_family_heading', 'font_family_mono' ] as $tk ) {
            $val = trim( (string) ( $t[ $tk ] ?? '' ) );
            if ( $val === '' || strpos( $val, 'var(' ) !== false ) {
                continue;
            }
            $first = trim( explode( ',', $val )[0], " '\"" );
            if ( $first === '' || in_array( strtolower( $first ), $system_families, true ) ) {
                continue;
            }
            $typo_families[] = $first;
        }
        $typo_extra = array_diff( array_unique( $typo_families ), $existing_fonts, $global_font_families );
        if ( ! empty( $typo_extra ) ) {
            $typo_import = $this->generate_google_fonts_import( array_values( $typo_extra ) );
            if ( $typo_import ) {
                $css .= $typo_import . "\n";
            }
        }

        // Custom properties
        $css .= ".olo-template {\n";
        // Colors
        foreach ( $c as $key => $value ) {
            $prop = str_replace( '_', '-', $key );
            $css .= "  --olo-color-{$prop}: {$value};\n";
        }
        // Global custom colors (user-defined swatches)
        $global_colors = $this->get_global_colors();
        foreach ( $global_colors as $gc ) {
            if ( ! empty( $gc['id'] ) && ! empty( $gc['value'] ) ) {
                $css .= "  --olo-color-" . sanitize_html_class( $gc['id'] ) . ": " . esc_attr( $gc['value'] ) . ";\n";
            }
        }
        // Alias di compatibilità: i nomi-pacchetto usati dalle tile mappano sui
        // token del tema (così seguono la palette del cliente). Vedi _olo-tokens.scss.
        foreach ( self::ALIAS_COLORI as $alias => $valore ) {
            $css .= "  --olo-color-{$alias}: {$valore};\n";
        }
        // Typography
        if ( ! empty( $t['font_family'] ) ) {
            $css .= "  --olo-font-family: {$t['font_family']};\n";
        }
        if ( ! empty( $t['font_family_heading'] ) ) {
            $css .= "  --olo-font-family-heading: {$t['font_family_heading']};\n";
        }
        // Ruolo mono: referenziato dalle tile (var(--olo-font-family-mono, fallback))
        // — emesso solo se personalizzato, altrimenti vale il fallback per-tile.
        if ( ! empty( $t['font_family_mono'] ) ) {
            $css .= "  --olo-font-family-mono: {$t['font_family_mono']};\n";
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
        $css .= "  --olo-container-narrow: " . ( $l['container_narrow'] ?? '720px' ) . ";\n";
        $css .= "  --olo-container-wide: " . ( $l['container_wide'] ?? '1440px' ) . ";\n";

        // Spacing scale (xs..4xl)
        $sp = $s['spacing'] ?? [];
        $sp_defaults = [ 'xs' => '4px', 'sm' => '8px', 'md' => '16px', 'lg' => '24px', 'xl' => '32px', '2xl' => '48px', '3xl' => '64px', '4xl' => '96px' ];
        foreach ( $sp_defaults as $sk => $sv ) {
            $val = ! empty( $sp[ $sk ] ) ? $sp[ $sk ] : $sv;
            $css .= "  --olo-space-{$sk}: {$val};\n";
        }

        // Section padding (alias verso i token spacing)
        $secp = $s['section_padding'] ?? [];
        $secp_defaults = [ 'compact' => 'lg', 'default' => 'xl', 'spacious' => '2xl', 'between' => 'md' ];
        foreach ( $secp_defaults as $sk => $sv ) {
            $token = ! empty( $secp[ $sk ] ) ? $secp[ $sk ] : $sv;
            $css .= "  --olo-section-pad-y-{$sk}: var(--olo-space-{$token});\n";
        }

        // Gutter (gap colonne + padding orizzontale container)
        $g = $s['gutter'] ?? [];
        $css .= "  --olo-gutter: " . absint( $g['desktop'] ?? 32 ) . "px;\n";
        $css .= "  --olo-gutter-side: " . absint( $g['side_desktop'] ?? 32 ) . "px;\n";
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
                    // Il valore puo essere un var() di ruolo o uno stack web-safe:
                    // virgolettarlo sempre ne faceva il nome di un font inesistente.
                    $family_css = $family;
                    if ( strpos( $family, 'var(' ) !== 0 && strpos( $family, ',' ) === false
                        && strpos( $family, "'" ) === false && strpos( $family, '"' ) === false ) {
                        $family_css = "'{$family}', sans-serif";
                    }
                    $css .= "  --olo-font-{$id}-family: {$family_css};\n";
                }
                $css .= "  --olo-font-{$id}-weight: {$weight};\n";
                $css .= "  --olo-font-{$id}-transform: {$transform};\n";
                $css .= "  --olo-font-{$id}-line-height: {$line_height};\n";
                // Gli helper restituiscono gia l'unita: "0.5px" + px = dichiarazione scartata.
                $ls_css = preg_match( '/^-?[0-9.]+$/', $letter_spacing ) ? $letter_spacing . 'px' : $letter_spacing;
                $css .= "  --olo-font-{$id}-letter-spacing: {$ls_css};\n";
            }
        }

        $css .= "}\n\n";

        // Regole del preset tipografico: la classe `olo-typo-<id>` sul wrapper di
        // una tile porta lo stile ai suoi discendenti. Prima le cinque proprieta'
        // stavano inline sul wrapper e scendevano per eredita', ma l'eredita' perde
        // contro QUALSIASI regola che tocchi il figlio — e un titolo e' sempre preso
        // da una regola del tema o di UIkit: si sceglieva uno stile e non cambiava
        // niente. La classe e' ripetuta per alzare la specificita' sopra le regole
        // del tema; i valori inline che la tile scrive per le proprieta' scelte
        // dall'utente restano sopra a tutto. `strong`, `b` e `th` non sono
        // nell'elenco: il loro grassetto e' semantico, non tipografia della tile.
        if ( ! empty( $global_typo ) ) {
            $css .= "/* Preset tipografici — applicazione */\n";
            foreach ( $global_typo as $gt ) {
                $id = sanitize_key( $gt['id'] ?? '' );
                if ( ! $id ) continue;
                $sel = ".olo-typo-{$id}.olo-typo-{$id}";
                $css .= "{$sel},\n{$sel} :is(h1,h2,h3,h4,h5,h6,p,li,a,span,em,i,blockquote,figcaption,label,dt,dd,td,button,input,textarea,select) {\n";
                $css .= "  font-family: var(--olo-font-{$id}-family, inherit);\n";
                $css .= "  font-weight: var(--olo-font-{$id}-weight, inherit);\n";
                $css .= "  text-transform: var(--olo-font-{$id}-transform, none);\n";
                $css .= "  line-height: var(--olo-font-{$id}-line-height, inherit);\n";
                $css .= "  letter-spacing: var(--olo-font-{$id}-letter-spacing, normal);\n";
                $css .= "}\n\n";
                // Le tile scrivono la tipografia del proprio titolo INLINE, e
                // l'inline batte qualsiasi regola. Ma la scrivono attraverso le
                // variabili di ruolo: ridefinendole qui sul wrapper, la
                // dichiarazione inline della tile si risolve nel font del preset
                // senza toccare duecento renderer. Il ruolo mono resta fuori: il
                // codice deve restare monospaziato.
                $css .= "{$sel} {\n";
                $css .= "  --olo-font-family: var(--olo-font-{$id}-family, var(--olo-font-family));\n";
                $css .= "  --olo-font-family-heading: var(--olo-font-{$id}-family, var(--olo-font-family-heading));\n";
                $css .= "}\n\n";
            }
        }

        // UIkit overrides - Sections
        // background-color WITHOUT !important so inline styles (custom bg) can override
        // color WITH !important to ensure text contrast with section style
        $css .= "/* UIkit Section overrides */\n";
        // Section default: TRASPARENTE (eredita bg da .olo-template / page_bg).
        // UIkit base imposta uk-section-default { background: #fff } che copre il page_bg.
        // Senza questa regola, ogni section default sovrascrive il bg pagina con bianco.
        $css .= ".olo-template .uk-section-default { background-color: transparent; }\n";
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
        $css .= ".olo-template .uk-container:not(.uk-container-expand) { max-width: var(--olo-container-max-width); padding-left: var(--olo-gutter-side); padding-right: var(--olo-gutter-side); }\n";
        $css .= ".olo-template .olo-container-narrow { max-width: var(--olo-container-narrow); margin-left: auto; margin-right: auto; }\n";
        $css .= ".olo-template .olo-container-wide { max-width: var(--olo-container-wide); margin-left: auto; margin-right: auto; }\n";
        $css .= ".olo-template .olo-container-full { max-width: 100%; }\n";

        // Section rhythm helpers
        $css .= ".olo-template .olo-section-pad-compact  { padding-top: var(--olo-section-pad-y-compact);  padding-bottom: var(--olo-section-pad-y-compact); }\n";
        $css .= ".olo-template .olo-section-pad-default  { padding-top: var(--olo-section-pad-y-default);  padding-bottom: var(--olo-section-pad-y-default); }\n";
        $css .= ".olo-template .olo-section-pad-spacious { padding-top: var(--olo-section-pad-y-spacious); padding-bottom: var(--olo-section-pad-y-spacious); }\n";
        $css .= ".olo-template .olo-section + .olo-section { margin-top: var(--olo-section-pad-y-between, 0); }\n";

        // Gutter responsive (tablet/mobile media queries)
        $g_desk      = absint( $g['desktop']      ?? 32 );
        $g_tab       = absint( $g['tablet']       ?? 24 );
        $g_mob       = absint( $g['mobile']       ?? 16 );
        $g_side_desk = absint( $g['side_desktop'] ?? 32 );
        $g_side_mob  = absint( $g['side_mobile']  ?? 16 );
        if ( $g_tab !== $g_desk || $g_side_desk !== $g_side_mob ) {
            $css .= "\n/* Gutter responsive — tablet */\n";
            $css .= "@media (max-width: 960px) {\n";
            $css .= "  .olo-template { --olo-gutter: {$g_tab}px; }\n";
            $css .= "}\n";
        }
        if ( $g_mob !== $g_desk || $g_side_mob !== $g_side_desk ) {
            $css .= "\n/* Gutter responsive — mobile */\n";
            $css .= "@media (max-width: 640px) {\n";
            $css .= "  .olo-template { --olo-gutter: {$g_mob}px; --olo-gutter-side: {$g_side_mob}px; }\n";
            $css .= "}\n";
        }

        // Fluid scaling — riscala tutti i token spacing su tablet/mobile
        $fs = $s['fluid_scaling'] ?? [];
        if ( ! empty( $fs['enabled'] ) ) {
            $tab_factor = max( 0.3, min( 1.0, (float) ( $fs['tablet'] ?? 0.85 ) ) );
            $mob_factor = max( 0.3, min( 1.0, (float) ( $fs['mobile'] ?? 0.65 ) ) );
            $css .= "\n/* Fluid scaling — tablet */\n";
            $css .= "@media (max-width: 960px) {\n  .olo-template {\n";
            foreach ( $sp_defaults as $sk => $sv ) {
                $val_raw = ! empty( $sp[ $sk ] ) ? $sp[ $sk ] : $sv;
                $num     = (float) preg_replace( '/[^0-9.]/', '', $val_raw );
                $scaled  = round( $num * $tab_factor, 2 );
                $css    .= "    --olo-space-{$sk}: {$scaled}px;\n";
            }
            $css .= "  }\n}\n";
            $css .= "\n/* Fluid scaling — mobile */\n";
            $css .= "@media (max-width: 640px) {\n  .olo-template {\n";
            foreach ( $sp_defaults as $sk => $sv ) {
                $val_raw = ! empty( $sp[ $sk ] ) ? $sp[ $sk ] : $sv;
                $num     = (float) preg_replace( '/[^0-9.]/', '', $val_raw );
                $scaled  = round( $num * $mob_factor, 2 );
                $css    .= "    --olo-space-{$sk}: {$scaled}px;\n";
            }
            $css .= "  }\n}\n";
        }

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

        // Grain / noise overlay (site-wide). SVG fractalNoise inline come data-URI,
        // sopra tutto il contenuto con mix-blend-mode:overlay. Solo se abilitato.
        $grain = $s['grain'] ?? [];
        if ( ! empty( $grain['enabled'] ) ) {
            $g_op    = max( 0, min( 30, absint( $grain['opacity'] ?? 6 ) ) ) / 100;
            $g_scale = max( 60, min( 400, absint( $grain['scale'] ?? 180 ) ) );
            // data-URI: SVG con feTurbulence (fractalNoise). %23 = '#' url-encoded.
            $noise = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E";
            $css .= "\n/* Grain / noise overlay */\n";
            $css .= ".olo-template { position: relative; }\n";
            $css .= ".olo-template::after { content: \"\"; position: fixed; inset: 0; z-index: 9999; pointer-events: none; mix-blend-mode: overlay; opacity: {$g_op}; background-image: url(\"{$noise}\"); background-size: {$g_scale}px {$g_scale}px; }\n";
        }

        $memo = $css;
        return $memo;
    }

    /**
     * Generate self-hosted @font-face CSS for the given Google Font families.
     *
     * I file vengono scaricati una sola volta e serviti da /uploads (vedi
     * Olobuild_Font_Host): nessuna richiesta del visitatore a Google.
     */
    public function generate_google_fonts_import( $fonts = [] ) {
        if ( empty( $fonts ) ) {
            return '';
        }
        if ( class_exists( 'Olobuild_Font_Host' ) ) {
            // I temi possono richiedere pesi extra (es. Big Shoulders 800/900) via
            // olo_styles.google_fonts_weights, formato css2 "300;400;...;900".
            $weights = '300;400;500;600;700';
            $styles  = get_option( 'olobuild_styles', [] );
            if ( is_array( $styles ) && ! empty( $styles['google_fonts_weights'] )
                && preg_match( '/^[0-9;]+$/', (string) $styles['google_fonts_weights'] ) ) {
                $weights = (string) $styles['google_fonts_weights'];
            }
            return Olobuild_Font_Host::get_font_face_css( $fonts, $weights );
        }
        return '';
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
