<?php
/**
 * Olobuild_Renderer_Css_Trait — utility CSS: sanitizzazione valori, box model, hover, responsive, bordi wrapper.
 *
 * Estratto verbatim da class-frontend-renderer.php (dieta monoliti v1.4.390):
 * stessi metodi, stessa visibilita', zero cambi alle chiamate ($this/self
 * risolvono nella classe che usa il trait).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

trait Olobuild_Renderer_Css_Trait {
    /**
     * Sanitize a border-style value against whitelist.
     */
    private function safe_border_style( $val ) {
        $val = strtolower( trim( $val ?? 'solid' ) );
        return in_array( $val, self::$allowed_border_styles, true ) ? $val : 'solid';
    }

    /**
     * Sanitize a CSS color value — allows hex, rgb, rgba, hsl, hsla, named colors, CSS variables.
     */
    private function safe_border_color( $val ) {
        $val = trim( $val ?? '#374151' );
        // Allow hex, rgb/rgba, hsl/hsla, named colors, CSS custom properties
        if ( preg_match( '/^(#[0-9a-fA-F]{3,8}|rgba?\([^)]+\)|hsla?\([^)]+\)|var\(--[a-zA-Z0-9_-]+\)|[a-zA-Z]+)$/', $val ) ) {
            return $val;
        }
        return '#374151';
    }

    /**
     * Sanitize inline custom CSS — strip dangerous patterns.
     * For inline style="" attributes, only allows CSS property declarations.
     */
    private function safe_inline_css( $css ) {
        $css = trim( $css ?? '' );
        if ( $css === '' ) return '';
        // Remove anything that could break out of style attribute or inject HTML
        $css = str_replace( [ '<', '>', '"', "'", '`' ], '', $css );
        // Remove expression(), url(javascript:), url(data:), @import, behavior:
        $css = preg_replace( '/expression\s*\(/i', '', $css );
        $css = preg_replace( '/url\s*\(\s*(javascript|data)\s*:/i', 'url(blocked:', $css );
        $css = preg_replace( '/@import/i', '', $css );
        $css = preg_replace( '/behavior\s*:/i', '', $css );
        $css = preg_replace( '/-moz-binding\s*:/i', '', $css );
        return $css;
    }

    /**
     * Sanitize a CSS dimension value (width/height/max-width/min-height/etc.).
     * Accetta numeri puri (→ px) e qualunque unità CSS standard: px/%/em/rem/vw/vh/vmin/vmax/ch/ex/fr/cm/mm/in/pt/pc.
     * Supporta anche keyword (auto/none/inherit/initial/unset/fit-content) e funzioni CSS sicure (calc/min/max/clamp/var/env).
     * Tutto il resto viene scartato per evitare CSS injection.
     *
     * @param mixed $value Valore raw (numero o stringa).
     * @return string Valore CSS sanitizzato (es. "49%", "200px", "calc(100% - 10px)") o '' se invalido.
     */
    private function safe_dim_value( $value ) {
        if ( $value === null || $value === '' ) return '';
        $v = trim( (string) $value );
        if ( $v === '' ) return '';
        // Numero puro → px
        if ( is_numeric( $v ) ) return $v . 'px';
        // Keyword consentite
        $keywords = [ 'auto', 'none', 'inherit', 'initial', 'unset', 'revert', 'fit-content', 'max-content', 'min-content' ];
        if ( in_array( strtolower( $v ), $keywords, true ) ) return strtolower( $v );
        // Numero + unità (es. 49%, 200px, 1.5rem, 50vh, calc()/min()/max()/clamp()/var()/env() supportate)
        // Pattern: [numero][unità] oppure funzione CSS con caratteri sicuri.
        if ( preg_match( '/^-?\d*\.?\d+(px|%|em|rem|vw|vh|vmin|vmax|ch|ex|fr|cm|mm|in|pt|pc)$/i', $v ) ) {
            return $v;
        }
        // Funzioni CSS: calc(), min(), max(), clamp(), var(), env() — solo caratteri sicuri.
        if ( preg_match( '/^(calc|min|max|clamp|var|env)\([\w\s\d\.\,\+\-\*\/%\(\)\-]+\)$/i', $v ) ) {
            // Aggiuntiva: blocca espressioni pericolose
            if ( strpos( $v, 'expression' ) !== false ) return '';
            if ( stripos( $v, 'url(' ) !== false )      return '';
            return $v;
        }
        return '';
    }

    /**
     * Applica al `$inline_styles` (by-ref) tutti gli "box styles" che sono
     * identici tra section / row / column / element renderer:
     * margin, padding, border-radius, border, opacity, flex container, transform,
     * box-shadow inline, text-shadow, backdrop-filter, overflow esplicito,
     * dimensions (tile_width/max_width/min_height), mask, custom CSS (advanced),
     * positioning (advanced.position_mode + top/left/right/bottom/width/zindex).
     *
     * Sostituisce ~180 LOC di codice duplicato. L'ordine delle declarations è
     * "canonical" — è sicuro perché ognuna è una proprietà CSS diversa (no
     * cascading conflicts tra di loro).
     *
     * Cose ESCLUSE perché divergenti tra i nodi (i renderer le gestiscono in proprio):
     *  - background (image/video/gallery layers, scope container vs section)
     *  - shadow class `uk-box-shadow-*` (decide il renderer in base a has_bg_any)
     *  - drop-shadow filter (solo element trasparente)
     *  - sticky/scroll_snap (section), grid placement (column), entrance animation
     *  - scrollspy/parallax attrs, hover_css_rules, responsive_css
     *  - text_color (solo element, aggiunto di recente)
     *
     * @param array &$inline_styles Array CSS declarations (by-ref, esteso in-place).
     * @param array  $style         `$node['style']`
     * @param array  $settings      `$node['settings']` (serve per flex container)
     * @param array  $advanced      `$node['advanced']` (custom_css + positioning)
     * @param array  $opts          Flag opzionali (vedi sotto):
     *                              - `apply_box_shadow` (bool, default true): include `build_box_shadow_css`.
     *                                Element trasparente lo skippa e usa drop-shadow filter.
     *                              - `apply_flex` (bool, default true): include `build_flex_container_css`.
     *                                Row lo skippa perché il flex va al `<div uk-grid>` interno,
     *                                non al wrapper esterno.
     */
    private function apply_common_box_styles( array &$inline_styles, array $style, array $settings, array $advanced = [], array $opts = [] ) {
        $apply_box_shadow = $opts['apply_box_shadow'] ?? true;
        $apply_flex       = $opts['apply_flex'] ?? true;
        // Margin & Padding — intval() per prevenire CSS injection via tile settings.
        if ( ! empty( $style['margin_top'] ) )     $inline_styles[] = 'margin-top: ' . intval( $style['margin_top'] ) . 'px';
        if ( ! empty( $style['margin_right'] ) )   $inline_styles[] = 'margin-right: ' . intval( $style['margin_right'] ) . 'px';
        if ( ! empty( $style['margin_bottom'] ) )  $inline_styles[] = 'margin-bottom: ' . intval( $style['margin_bottom'] ) . 'px';
        if ( ! empty( $style['margin_left'] ) )    $inline_styles[] = 'margin-left: ' . intval( $style['margin_left'] ) . 'px';
        if ( ! empty( $style['padding_top'] ) )    $inline_styles[] = 'padding-top: ' . intval( $style['padding_top'] ) . 'px';
        if ( ! empty( $style['padding_right'] ) )  $inline_styles[] = 'padding-right: ' . intval( $style['padding_right'] ) . 'px';
        if ( ! empty( $style['padding_bottom'] ) ) $inline_styles[] = 'padding-bottom: ' . intval( $style['padding_bottom'] ) . 'px';
        if ( ! empty( $style['padding_left'] ) )   $inline_styles[] = 'padding-left: ' . intval( $style['padding_left'] ) . 'px';

        // Border radius
        if ( ! empty( $style['border_radius'] ) )  $inline_styles[] = $this->css->build_border_radius_css( $style['border_radius'] );

        // Border (sistema unificato: oggetto 4-side + fallback legacy 3-key)
        $border_css = $this->build_wrapper_border_css( $style );
        if ( $border_css ) $inline_styles[] = $border_css;

        // Opacity
        if ( ! empty( $style['opacity'] ) && intval( $style['opacity'] ) < 100 ) {
            $opacity = intval( $style['opacity'] ) / 100;
            $inline_styles[] = "opacity: {$opacity}";
        }

        // Flex container settings (tab "Layout Flex" inspector).
        // Row skippa (`apply_flex=false`) perché applica flex al `<div uk-grid>` interno.
        if ( $apply_flex ) {
            $flex_css = $this->css->build_flex_container_css( $settings );
            foreach ( $flex_css as $decl ) $inline_styles[] = $decl;
        }

        // CSS Transform (normal state)
        $transform_css = $this->css->build_transform_css( $style );
        if ( $transform_css ) {
            foreach ( $transform_css as $decl ) $inline_styles[] = $decl;
        }

        // Box shadow inline (separato dalla classe uk-box-shadow-*, decisa dal renderer).
        // Element con bg=trasparente skippa: usa drop-shadow filter, lo gestisce nel renderer.
        if ( $apply_box_shadow ) {
            $box_shadow = $this->css->build_box_shadow_css( $style );
            if ( $box_shadow ) $inline_styles[] = $box_shadow;
        }

        // Text shadow
        $text_shadow = $this->css->build_text_shadow_css( $style );
        if ( $text_shadow ) $inline_styles[] = $text_shadow;

        // Backdrop filter
        $backdrop = $this->css->build_backdrop_filter_css( $style );
        if ( $backdrop ) {
            foreach ( $backdrop as $decl ) $inline_styles[] = $decl;
        }

        // Overflow esplicito
        if ( ! empty( $style['overflow'] ) && $style['overflow'] !== 'visible' ) {
            $inline_styles[] = 'overflow: ' . esc_attr( $style['overflow'] );
        }

        // Blend mode (mix-blend-mode sul wrapper) — whitelist contro CSS injection.
        if ( ! empty( $style['blend_mode'] ) && $style['blend_mode'] !== 'normal' ) {
            $allowed_blend = [ 'multiply', 'screen', 'overlay', 'darken', 'lighten',
                'color-dodge', 'color-burn', 'hard-light', 'soft-light', 'difference',
                'exclusion', 'hue', 'saturation', 'color', 'luminosity' ];
            if ( in_array( $style['blend_mode'], $allowed_blend, true ) ) {
                $inline_styles[] = 'mix-blend-mode: ' . $style['blend_mode'];
            }
        }

        // Dimensions (tile_width / tile_max_width / tile_min_height) — unità libere via safe_dim_value
        $dim_map = [
            'tile_width'      => 'width',
            'tile_max_width'  => 'max-width',
            'tile_min_height' => 'min-height',
        ];
        foreach ( $dim_map as $key => $css_prop ) {
            if ( ! isset( $style[ $key ] ) || $style[ $key ] === '' || $style[ $key ] === null ) continue;
            $v = $this->safe_dim_value( $style[ $key ] );
            if ( $v !== '' ) $inline_styles[] = $css_prop . ': ' . $v;
        }

        // Mask
        $mask_css = $this->css->build_mask_css( $style );
        if ( $mask_css ) {
            foreach ( $mask_css as $decl ) $inline_styles[] = $decl;
        }

        // Custom CSS da advanced
        if ( ! empty( $advanced['custom_css'] ) ) {
            $inline_styles[] = $this->safe_inline_css( $advanced['custom_css'] );
        }

        // Positioning (absolute/fixed/relative)
        $pos_mode = $advanced['position_mode'] ?? 'static';
        if ( $pos_mode && $pos_mode !== 'static' ) {
            $inline_styles[] = 'position: ' . esc_attr( $pos_mode );
            foreach ( [ 'top', 'left', 'bottom', 'right' ] as $dir ) {
                $val = $advanced[ 'position_' . $dir ] ?? '';
                if ( $val !== '' ) {
                    $inline_styles[] = $dir . ': ' . ( is_numeric( $val ) ? $val . 'px' : esc_attr( $val ) );
                }
            }
            $w = $advanced['position_width'] ?? '';
            if ( $w !== '' ) {
                $inline_styles[] = 'width: ' . ( is_numeric( $w ) ? $w . 'px' : esc_attr( $w ) );
            }
            $z = $advanced['position_zindex'] ?? '';
            if ( $z !== '' ) {
                $inline_styles[] = 'z-index: ' . intval( $z );
            }
        }
    }

    /**
     * Sanitize custom CSS for <style> blocks — more permissive than inline but still safe.
     */
    private function safe_block_css( $css ) {
        $css = trim( $css ?? '' );
        if ( $css === '' ) return '';
        // Remove </style> to prevent breaking out of style block
        $css = preg_replace( '/<\/style\s*>/i', '', $css );
        // Remove <script> tags
        $css = preg_replace( '/<script/i', '', $css );
        // Remove expression(), behavior, -moz-binding
        $css = preg_replace( '/expression\s*\(/i', '', $css );
        $css = preg_replace( '/behavior\s*:/i', '', $css );
        $css = preg_replace( '/-moz-binding\s*:/i', '', $css );
        $css = preg_replace( '/url\s*\(\s*(javascript|data)\s*:/i', 'url(blocked:', $css );
        $css = preg_replace( '/@import\s+url/i', '', $css );
        return $css;
    }

    /**
     * Static tile registry — stores all tile nodes from templates being rendered
     * so that tiles can look up other tiles' settings (e.g., navmenu referencing a search tile).
     */
    private static $tile_registry = [];

    /**
     * Tile IDs that are referenced by other tiles (e.g., search tiles used by navmenu/megamenu).
     * These tiles should not render in their original position — they are rendered inline by the referencing tile.
     */
    private static $referenced_tile_ids = [];

    /**
     * Collect hover CSS rules for an element.
     */
    private function collect_hover_css( $style, $css_id, $is_fullwidth, &$hover_css_rules, $advanced = [] ) {
        $hover = $style['hover'] ?? [];
        $transition_cfg = $style['transition'] ?? [];
        $has_hover = false;
        $hover_decls = [];

        if ( ! empty( $hover['bg_color'] ) ) {
            $hover_decls[] = 'background-color: ' . esc_attr( $hover['bg_color'] );
            $has_hover = true;
        }
        if ( ! empty( $hover['text_color'] ) ) {
            $hover_decls[] = 'color: ' . esc_attr( $hover['text_color'] );
            $has_hover = true;
        }
        if ( ! empty( $hover['border_color'] ) ) {
            $hover_decls[] = 'border-color: ' . esc_attr( $hover['border_color'] );
            $has_hover = true;
        }
        // v3.55.17 — hover anche su border_width e border_style (prima solo border_color era hoverable).
        if ( isset( $hover['border_width'] ) && $hover['border_width'] !== '' && $hover['border_width'] !== null ) {
            $hover_decls[] = 'border-width: ' . intval( $hover['border_width'] ) . 'px';
            $has_hover = true;
        }
        if ( ! empty( $hover['border_style'] ) ) {
            $hover_decls[] = 'border-style: ' . esc_attr( $hover['border_style'] );
            $has_hover = true;
        }
        // v3.55.20 — hover per il sistema NUOVO style.border_hover (oggetto 4-side + style + color).
        // Convive con il legacy: il nuovo vince se ha valori (color non vuoto + qualche lato > 0).
        $bh = $style['border_hover'] ?? null;
        if ( is_array( $bh ) ) {
            $bh_color = trim( $bh['color'] ?? '' );
            $bh_style = trim( $bh['style'] ?? '' );
            $bh_t  = intval( $bh['top']    ?? 0 );
            $bh_r  = intval( $bh['right']  ?? 0 );
            $bh_b  = intval( $bh['bottom'] ?? 0 );
            $bh_l  = intval( $bh['left']   ?? 0 );
            $bh_any = $bh_t || $bh_r || $bh_b || $bh_l;
            if ( $bh_color !== '' || $bh_style !== '' || $bh_any ) {
                // Base value (per ereditare i lati non override).
                $base = $style['border'] ?? null;
                $base_d = is_array( $base ) ? [
                    'top'    => max( 0, intval( $base['top']    ?? 0 ) ),
                    'right'  => max( 0, intval( $base['right']  ?? 0 ) ),
                    'bottom' => max( 0, intval( $base['bottom'] ?? 0 ) ),
                    'left'   => max( 0, intval( $base['left']   ?? 0 ) ),
                    'style'  => $base['style'] ?? 'solid',
                    'color'  => $base['color'] ?? '',
                ] : [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'style' => 'solid', 'color' => '' ];

                $eff_c = $bh_color !== '' ? $bh_color : $base_d['color'];
                $eff_s = $bh_style !== '' ? $bh_style : $base_d['style'];
                $eff_t = $bh_t ?: $base_d['top'];
                $eff_r = $bh_r ?: $base_d['right'];
                $eff_b = $bh_b ?: $base_d['bottom'];
                $eff_l = $bh_l ?: $base_d['left'];

                if ( $eff_c !== '' ) {
                    $bs = $this->safe_border_style( $eff_s ?: 'solid' );
                    $bc = $this->safe_border_color( $eff_c );
                    // !important: il bordo base è reso INLINE sull'elemento e un selettore
                    // #id:hover non lo sovrascriverebbe (l'inline ha specificità maggiore).
                    if ( $eff_t === $eff_r && $eff_r === $eff_b && $eff_b === $eff_l ) {
                        $hover_decls[] = "border: {$eff_t}px {$bs} {$bc} !important";
                    } else {
                        if ( $eff_t ) $hover_decls[] = "border-top: {$eff_t}px {$bs} {$bc} !important";
                        if ( $eff_r ) $hover_decls[] = "border-right: {$eff_r}px {$bs} {$bc} !important";
                        if ( $eff_b ) $hover_decls[] = "border-bottom: {$eff_b}px {$bs} {$bc} !important";
                        if ( $eff_l ) $hover_decls[] = "border-left: {$eff_l}px {$bs} {$bc} !important";
                    }
                    $has_hover = true;
                }
            }
        }
        if ( isset( $hover['border_radius'] ) && $hover['border_radius'] !== '' && $hover['border_radius'] !== null ) {
            $br = $hover['border_radius'];
            if ( is_array( $br ) ) {
                $hover_decls[] = sprintf( 'border-radius: %dpx %dpx %dpx %dpx',
                    intval( $br['tl'] ?? 0 ), intval( $br['tr'] ?? 0 ),
                    intval( $br['br'] ?? 0 ), intval( $br['bl'] ?? 0 ) );
            } else {
                $hover_decls[] = 'border-radius: ' . intval( $br ) . 'px';
            }
            $has_hover = true;
        }
        // Hover shadow — box-shadow
        $hover_drop_shadow = '';
        if ( ! empty( $hover['shadow'] ) ) {
            if ( $hover['shadow'] === 'custom' ) {
                $hh  = intval( $hover['shadow_h'] ?? 0 );
                $hv  = intval( $hover['shadow_v'] ?? 0 );
                $hbl = intval( $hover['shadow_blur'] ?? 0 );
                $hsp = intval( $hover['shadow_spread'] ?? 0 );
                $hco = esc_attr( $hover['shadow_color'] ?? 'rgba(0,0,0,0.15)' );
                $hin = ! empty( $hover['shadow_inset'] ) ? 'inset ' : '';
                $hover_decls[] = "box-shadow: {$hin}{$hh}px {$hv}px {$hbl}px {$hsp}px {$hco}";
                $has_hover = true;
            } elseif ( isset( $this->shadow_map[ $hover['shadow'] ] ) ) {
                $hover_decls[] = 'box-shadow: ' . $this->shadow_map[ $hover['shadow'] ];
                $has_hover = true;
            } elseif ( $hover['shadow'] === 'none' ) {
                $hover_decls[] = 'box-shadow: none';
                $has_hover = true;
            }
        }
        if ( isset( $hover['opacity'] ) && $hover['opacity'] !== null && $hover['opacity'] !== '' ) {
            $hover_decls[] = 'opacity: ' . ( intval( $hover['opacity'] ) / 100 );
            $has_hover = true;
        }

        $h_transforms = [];
        if ( isset( $hover['transform_scale'] ) && $hover['transform_scale'] !== null ) {
            $h_transforms[] = 'scale(' . floatval( $hover['transform_scale'] ) . ')';
            $has_hover = true;
        }
        if ( isset( $hover['transform_translateX'] ) && $hover['transform_translateX'] !== null ) {
            $h_transforms[] = 'translateX(' . intval( $hover['transform_translateX'] ) . 'px)';
            $has_hover = true;
        }
        if ( isset( $hover['transform_translateY'] ) && $hover['transform_translateY'] !== null ) {
            $h_transforms[] = 'translateY(' . intval( $hover['transform_translateY'] ) . 'px)';
            $has_hover = true;
        }
        if ( isset( $hover['transform_rotate'] ) && $hover['transform_rotate'] !== null ) {
            $h_transforms[] = 'rotate(' . intval( $hover['transform_rotate'] ) . 'deg)';
            $has_hover = true;
        }
        if ( isset( $hover['transform_skewX'] ) && $hover['transform_skewX'] !== null ) {
            $h_transforms[] = 'skewX(' . intval( $hover['transform_skewX'] ) . 'deg)';
            $has_hover = true;
        }
        if ( isset( $hover['transform_skewY'] ) && $hover['transform_skewY'] !== null ) {
            $h_transforms[] = 'skewY(' . intval( $hover['transform_skewY'] ) . 'deg)';
            $has_hover = true;
        }
        if ( ! empty( $h_transforms ) ) {
            $hover_decls[] = 'transform: ' . implode( ' ', $h_transforms );
        }

        // Text shadow on hover
        $ts_h_val = $hover['text_shadow_h'] ?? null;
        $ts_v_val = $hover['text_shadow_v'] ?? null;
        $ts_blur_val = $hover['text_shadow_blur'] ?? null;
        if ( ! empty( $hover['text_shadow_enabled'] ) || $ts_h_val !== null || $ts_v_val !== null || $ts_blur_val !== null ) {
            $ts_h     = intval( $ts_h_val ?? 0 );
            $ts_v     = intval( $ts_v_val ?? 0 );
            $ts_blur  = intval( $ts_blur_val ?? 0 );
            if ( $ts_h !== 0 || $ts_v !== 0 || $ts_blur !== 0 ) {
                $ts_color = esc_attr( $hover['text_shadow_color'] ?? 'rgba(0,0,0,0.3)' );
                $hover_decls[] = "text-shadow: {$ts_h}px {$ts_v}px {$ts_blur}px {$ts_color}";
                $has_hover = true;
            }
        }

        // Backdrop filter on hover
        $h_bd_parts = [];
        if ( isset( $hover['backdrop_blur'] ) && intval( $hover['backdrop_blur'] ) != 0 ) {
            $h_bd_parts[] = 'blur(' . intval( $hover['backdrop_blur'] ) . 'px)';
        }
        if ( isset( $hover['backdrop_brightness'] ) && intval( $hover['backdrop_brightness'] ) != 100 ) {
            $h_bd_parts[] = 'brightness(' . intval( $hover['backdrop_brightness'] ) . '%)';
        }
        if ( isset( $hover['backdrop_contrast'] ) && intval( $hover['backdrop_contrast'] ) != 100 ) {
            $h_bd_parts[] = 'contrast(' . intval( $hover['backdrop_contrast'] ) . '%)';
        }
        if ( isset( $hover['backdrop_saturate'] ) && intval( $hover['backdrop_saturate'] ) != 100 ) {
            $h_bd_parts[] = 'saturate(' . intval( $hover['backdrop_saturate'] ) . '%)';
        }
        if ( ! empty( $h_bd_parts ) ) {
            $h_bd_val = implode( ' ', $h_bd_parts );
            $hover_decls[] = '-webkit-backdrop-filter: ' . $h_bd_val;
            $hover_decls[] = 'backdrop-filter: ' . $h_bd_val;
            $has_hover = true;
        }

        // CSS filters on hover — combina con drop-shadow se mask attiva
        $h_filter_parts = [];
        if ( ! empty( $hover_drop_shadow ) ) {
            $h_filter_parts[] = $hover_drop_shadow;
        }
        if ( isset( $hover['filter_blur'] ) && intval( $hover['filter_blur'] ) != 0 ) {
            $h_filter_parts[] = 'blur(' . intval( $hover['filter_blur'] ) . 'px)';
        }
        if ( isset( $hover['filter_brightness'] ) && intval( $hover['filter_brightness'] ) != 100 ) {
            $h_filter_parts[] = 'brightness(' . intval( $hover['filter_brightness'] ) . '%)';
        }
        if ( isset( $hover['filter_contrast'] ) && intval( $hover['filter_contrast'] ) != 100 ) {
            $h_filter_parts[] = 'contrast(' . intval( $hover['filter_contrast'] ) . '%)';
        }
        if ( isset( $hover['filter_saturate'] ) && intval( $hover['filter_saturate'] ) != 100 ) {
            $h_filter_parts[] = 'saturate(' . intval( $hover['filter_saturate'] ) . '%)';
        }
        if ( isset( $hover['filter_grayscale'] ) && intval( $hover['filter_grayscale'] ) != 0 ) {
            $h_filter_parts[] = 'grayscale(' . intval( $hover['filter_grayscale'] ) . '%)';
        }
        if ( isset( $hover['filter_sepia'] ) && intval( $hover['filter_sepia'] ) != 0 ) {
            $h_filter_parts[] = 'sepia(' . intval( $hover['filter_sepia'] ) . '%)';
        }
        if ( ! empty( $h_filter_parts ) ) {
            $hover_decls[] = 'filter: ' . implode( ' ', $h_filter_parts );
            $has_hover = true;
        }

        if ( $has_hover ) {
            $dur  = intval( $transition_cfg['duration'] ?? 300 );
            $ease = esc_attr( $transition_cfg['easing'] ?? 'ease' );
            $trans_props = [ 'color', 'background-color', 'border-color', 'border-radius', 'box-shadow', 'opacity', 'transform', 'filter', 'text-shadow', 'backdrop-filter', '-webkit-backdrop-filter' ];
            $trans_val = implode( ', ', array_map( function( $p ) use ( $dur, $ease ) {
                return "{$p} {$dur}ms {$ease}";
            }, $trans_props ) );

            $sel = '#' . esc_attr( $css_id );
            $hover_css_rules[] = "{$sel} { transition: {$trans_val}; }";
            $hover_css_rules[] = "{$sel}:hover { " . implode( '; ', $hover_decls ) . "; }";
        }

        // Effetti bordo avanzati (neon/gradiente) del WRAPPER — letti da style.border_effect*.
        // Agganciati qui perché collect_hover_css è il punto comune a TUTTI i percorsi
        // wrapper (element/section/row/column), con $style + $css_id già disponibili.
        $be_css = $this->build_wrapper_border_effect_css( $css_id, $style );
        if ( $be_css !== '' ) {
            $hover_css_rules[] = $be_css;
        }
    }

    /**
     * Effetti bordo avanzati (neon / neon-pulse / gradiente / gradiente-rotante) per il
     * WRAPPER di un nodo (element/section/row/column). Equivalente lato wrapper di
     * Olobuild_Tile_Base::build_border_effect_css: legge style.border (colore base) +
     * style.border_effect_*. Selettore = #css_id. Stringa CSS senza <style> (o '').
     */
    private function build_wrapper_border_effect_css( $css_id, $style ) {
        $effect = $style['border_effect'] ?? 'none';
        if ( $effect === 'none' || $effect === '' ) return '';

        $border  = $style['border'] ?? null;
        $b_color = is_array( $border ) ? trim( $border['color'] ?? '' ) : '';
        $b_active = is_array( $border ) && $b_color !== '' && (
            intval( $border['top'] ?? 0 ) || intval( $border['right'] ?? 0 ) ||
            intval( $border['bottom'] ?? 0 ) || intval( $border['left'] ?? 0 )
        );
        // neon/neon-pulse richiedono un bordo base con colore; i gradienti no (usano border-image).
        if ( ! $b_active && ! in_array( $effect, [ 'gradient', 'gradient-spin' ], true ) ) return '';

        $uid    = '#' . $css_id;
        $color1 = $b_color !== '' ? $b_color : '#e1474f';
        $color2 = trim( $style['border_effect_color2'] ?? '' ) ?: '#f4a23b';

        switch ( $effect ) {
            case 'neon':
                $levels = [ 'subtle' => [ 4, 8 ], 'medium' => [ 6, 18 ], 'intense' => [ 10, 30 ] ];
                $intensity = $style['border_effect_intensity'] ?? 'medium';
                [ $a, $b ] = $levels[ $intensity ] ?? $levels['medium'];
                $alpha = $this->hex_to_rgba( $color1, 0.45 );
                return "{$uid}{box-shadow:0 0 {$a}px {$color1},0 0 {$b}px {$color1},0 0 " . ( $b * 2 ) . "px {$alpha};}";

            case 'neon-pulse':
                $intensity = $style['border_effect_intensity'] ?? 'medium';
                $anim_id   = 'olo-np-' . substr( md5( $uid . $color1 ), 0, 6 );
                $a1 = $this->hex_to_rgba( $color1, 0.5 );
                $a2 = $this->hex_to_rgba( $color1, 0.8 );
                switch ( $intensity ) {
                    case 'subtle':  [ $s1, $s2, $s3, $s4 ] = [ 3, 6, 5, 12 ]; break;
                    case 'intense': [ $s1, $s2, $s3, $s4 ] = [ 8, 20, 14, 40 ]; break;
                    default:        [ $s1, $s2, $s3, $s4 ] = [ 5, 12, 8, 24 ]; break;
                }
                return "@keyframes {$anim_id}{0%,100%{box-shadow:0 0 {$s1}px {$color1},0 0 {$s2}px {$a1};}50%{box-shadow:0 0 {$s3}px {$color1},0 0 {$s4}px {$a2},0 0 " . ( $s4 * 2 ) . "px {$a1};}}" .
                       "{$uid}{animation:{$anim_id} 2s ease-in-out infinite;}";

            case 'gradient':
                $angle = intval( $style['border_effect_angle'] ?? 135 );
                return "{$uid}{border-image:linear-gradient({$angle}deg,{$color1},{$color2}) 1;}";

            case 'gradient-spin':
                $speed   = max( 1, intval( $style['border_effect_speed'] ?? 4 ) );
                $prop    = '--olo-ba-' . substr( md5( $uid ), 0, 6 );
                $anim_id = 'olo-bs-' . substr( md5( $uid ), 0, 6 );
                return "@property {$prop}{syntax:'<angle>';initial-value:0deg;inherits:false;}" .
                       "@keyframes {$anim_id}{to{{$prop}:360deg;}}" .
                       "{$uid}{border-image:conic-gradient(from var({$prop}),{$color1},{$color2},{$color1}) 1;" .
                       "animation:{$anim_id} {$speed}s linear infinite;}";
        }
        return '';
    }

    /** Converte un colore hex in rgba(r,g,b,alpha); fallback brand se non hex. */
    private function hex_to_rgba( $hex, $alpha ) {
        $hex = ltrim( (string) $hex, '#' );
        if ( strlen( $hex ) === 3 ) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        if ( strlen( $hex ) !== 6 || ! ctype_xdigit( $hex ) ) return "rgba(225,71,79,{$alpha})";
        $r = hexdec( substr( $hex, 0, 2 ) );
        $g = hexdec( substr( $hex, 2, 2 ) );
        $b = hexdec( substr( $hex, 4, 2 ) );
        return "rgba({$r},{$g},{$b},{$alpha})";
    }

    /** Dichiarazione text-shadow da chiavi (text_shadow_h/_v/_blur/_color + $suffix). '' se inattiva.
     *  !important perché il valore desktop è inline e va sovrascritto nelle media query. */
    private function build_text_shadow_decl( $src, $suffix = '' ) {
        $h = $src["text_shadow_h{$suffix}"]    ?? null;
        $v = $src["text_shadow_v{$suffix}"]    ?? null;
        $b = $src["text_shadow_blur{$suffix}"] ?? null;
        if ( $h === null && $v === null && $b === null ) return '';
        $hi = intval( $h ?? 0 ); $vi = intval( $v ?? 0 ); $bi = intval( $b ?? 0 );
        if ( ! $hi && ! $vi && ! $bi ) return '';
        $c = esc_attr( $src["text_shadow_color{$suffix}"] ?? 'rgba(0,0,0,0.3)' );
        return "text-shadow: {$hi}px {$vi}px {$bi}px {$c} !important";
    }

    /** Dichiarazioni backdrop-filter da chiavi (backdrop_blur/_brightness/_saturate + $suffix). [] se inattive. */
    private function build_backdrop_decls( $src, $suffix = '' ) {
        $parts = [];
        if ( isset( $src["backdrop_blur{$suffix}"] ) && intval( $src["backdrop_blur{$suffix}"] ) != 0 ) {
            $parts[] = 'blur(' . intval( $src["backdrop_blur{$suffix}"] ) . 'px)';
        }
        if ( isset( $src["backdrop_brightness{$suffix}"] ) && intval( $src["backdrop_brightness{$suffix}"] ) != 100 ) {
            $parts[] = 'brightness(' . intval( $src["backdrop_brightness{$suffix}"] ) . '%)';
        }
        if ( isset( $src["backdrop_saturate{$suffix}"] ) && intval( $src["backdrop_saturate{$suffix}"] ) != 100 ) {
            $parts[] = 'saturate(' . intval( $src["backdrop_saturate{$suffix}"] ) . '%)';
        }
        if ( empty( $parts ) ) return [];
        $val = implode( ' ', $parts );
        return [ '-webkit-backdrop-filter: ' . $val . ' !important', 'backdrop-filter: ' . $val . ' !important' ];
    }

    /**
     * Collect responsive CSS rules for tablet and mobile breakpoints.
     *
     * Supported responsive overrides (suffixed with _tablet / _mobile):
     * - margin_top, margin_right, margin_bottom, margin_left
     * - padding_top, padding_right, padding_bottom, padding_left
     * - font_size
     * - gap
     * - border_radius (simple numeric or {tl,tr,br,bl} array)
     */
    private function collect_responsive_css( $style, $css_id, $advanced = [] ) {
        // Widescreen breakpoint uses min-width, others use max-width
        $widescreen_decls = [];
        $ws_props = [ 'margin', 'padding' ];
        foreach ( $ws_props as $prop ) {
            foreach ( [ 'top', 'right', 'bottom', 'left' ] as $side ) {
                $key = "{$prop}_{$side}_widescreen";
                if ( isset( $style[ $key ] ) && $style[ $key ] !== '' && $style[ $key ] !== null ) {
                    $widescreen_decls[] = "{$prop}-{$side}: " . intval( $style[ $key ] ) . 'px';
                }
            }
        }
        $ws_fs = 'font_size_widescreen';
        if ( isset( $style[ $ws_fs ] ) && $style[ $ws_fs ] !== '' && $style[ $ws_fs ] !== null ) {
            $widescreen_decls[] = 'font-size: ' . intval( $style[ $ws_fs ] ) . 'px';
        }
        $ws_gap = 'gap_widescreen';
        if ( isset( $style[ $ws_gap ] ) && $style[ $ws_gap ] !== '' && $style[ $ws_gap ] !== null ) {
            $widescreen_decls[] = 'gap: ' . intval( $style[ $ws_gap ] ) . 'px';
        }
        $ws_w = 'width_widescreen';
        if ( isset( $style[ $ws_w ] ) && $style[ $ws_w ] !== '' && $style[ $ws_w ] !== null ) {
            $w_val = $style[ $ws_w ];
            $widescreen_decls[] = 'width: ' . ( is_numeric( $w_val ) ? intval( $w_val ) . 'px' : esc_attr( $w_val ) );
        }
        if ( ! empty( $widescreen_decls ) ) {
            $ws_px = intval( $this->breakpoints['widescreen'] ?? 1400 );
            $this->responsive_css .= "@media (min-width:{$ws_px}px){#{$css_id}{" . implode( ';', $widescreen_decls ) . "}}";
        }

        $bp_map = [
            'tablet_landscape' => intval( $this->breakpoints['tablet_landscape'] ?? 1200 ) . 'px',
            'tablet'           => intval( $this->breakpoints['tablet'] ?? 960 ) . 'px',
            'mobile_landscape' => intval( $this->breakpoints['mobile_landscape'] ?? 640 ) . 'px',
            'mobile'           => intval( $this->breakpoints['mobile'] ?? 480 ) . 'px',
        ];
        foreach ( $bp_map as $bp => $max_width ) {
            $decls = [];

            // Margin & Padding (4 sides each)
            foreach ( [ 'margin', 'padding' ] as $prop ) {
                foreach ( [ 'top', 'right', 'bottom', 'left' ] as $side ) {
                    $key = "{$prop}_{$side}_{$bp}";
                    if ( isset( $style[ $key ] ) && $style[ $key ] !== '' && $style[ $key ] !== null ) {
                        $decls[] = "{$prop}-{$side}: " . intval( $style[ $key ] ) . 'px';
                    }
                }
            }

            // Font size
            $fs_key = "font_size_{$bp}";
            if ( isset( $style[ $fs_key ] ) && $style[ $fs_key ] !== '' && $style[ $fs_key ] !== null ) {
                $decls[] = 'font-size: ' . intval( $style[ $fs_key ] ) . 'px';
            }

            // Gap
            $gap_key = "gap_{$bp}";
            if ( isset( $style[ $gap_key ] ) && $style[ $gap_key ] !== '' && $style[ $gap_key ] !== null ) {
                $decls[] = 'gap: ' . intval( $style[ $gap_key ] ) . 'px';
            }

            // Border radius
            $br_key = "border_radius_{$bp}";
            if ( isset( $style[ $br_key ] ) && $style[ $br_key ] !== '' && $style[ $br_key ] !== null ) {
                $br_val = $style[ $br_key ];
                if ( is_array( $br_val ) ) {
                    $decls[] = sprintf( 'border-radius: %dpx %dpx %dpx %dpx',
                        intval( $br_val['tl'] ?? 0 ), intval( $br_val['tr'] ?? 0 ),
                        intval( $br_val['br'] ?? 0 ), intval( $br_val['bl'] ?? 0 ) );
                } else {
                    $decls[] = 'border-radius: ' . intval( $br_val ) . 'px';
                }
            }

            // Border per-device (oggetto 4-side su border_<bp>). Il controllo "Bordo"
            // di Spazi & Bordi salva un override per breakpoint: stessa policy del
            // desktop (build_wrapper_border_css) — se la chiave esiste è un override
            // esplicito, incluso "border: none" per disattivare il bordo su quel device.
            $bd_key = "border_{$bp}";
            if ( isset( $style[ $bd_key ] ) && is_array( $style[ $bd_key ] ) ) {
                // !important: il bordo desktop è inline, va sovrascritto nella media query.
                $bd_css = $this->build_wrapper_border_css( [ 'border' => $style[ $bd_key ] ], true );
                if ( $bd_css !== '' ) {
                    $decls[] = $bd_css;
                }
            }

            // Transform responsive overrides
            $resp_t_parts = [];
            $t_scale_key = "transform_scale_{$bp}";
            if ( isset( $style[ $t_scale_key ] ) && $style[ $t_scale_key ] !== '' && $style[ $t_scale_key ] !== null ) {
                $resp_t_parts[] = 'scale(' . floatval( $style[ $t_scale_key ] ) . ')';
            }
            $t_tx_key = "transform_translateX_{$bp}";
            if ( isset( $style[ $t_tx_key ] ) && $style[ $t_tx_key ] !== '' && $style[ $t_tx_key ] !== null ) {
                $resp_t_parts[] = 'translateX(' . floatval( $style[ $t_tx_key ] ) . 'px)';
            }
            $t_ty_key = "transform_translateY_{$bp}";
            if ( isset( $style[ $t_ty_key ] ) && $style[ $t_ty_key ] !== '' && $style[ $t_ty_key ] !== null ) {
                $resp_t_parts[] = 'translateY(' . floatval( $style[ $t_ty_key ] ) . 'px)';
            }
            $t_rot_key = "transform_rotate_{$bp}";
            if ( isset( $style[ $t_rot_key ] ) && $style[ $t_rot_key ] !== '' && $style[ $t_rot_key ] !== null ) {
                $resp_t_parts[] = 'rotate(' . intval( $style[ $t_rot_key ] ) . 'deg)';
            }
            $t_skx_key = "transform_skewX_{$bp}";
            if ( isset( $style[ $t_skx_key ] ) && $style[ $t_skx_key ] !== '' && $style[ $t_skx_key ] !== null ) {
                $resp_t_parts[] = 'skewX(' . intval( $style[ $t_skx_key ] ) . 'deg)';
            }
            $t_sky_key = "transform_skewY_{$bp}";
            if ( isset( $style[ $t_sky_key ] ) && $style[ $t_sky_key ] !== '' && $style[ $t_sky_key ] !== null ) {
                $resp_t_parts[] = 'skewY(' . intval( $style[ $t_sky_key ] ) . 'deg)';
            }
            if ( ! empty( $resp_t_parts ) ) {
                $decls[] = 'transform: ' . implode( ' ', $resp_t_parts ) . ' !important';
            }

            // Width override
            $w_key = "width_{$bp}";
            if ( isset( $style[ $w_key ] ) && $style[ $w_key ] !== '' && $style[ $w_key ] !== null ) {
                $w_val = $style[ $w_key ];
                $decls[] = 'width: ' . ( is_numeric( $w_val ) ? intval( $w_val ) . 'px' : esc_attr( $w_val ) );
            }

            // Height override
            $h_key = "height_{$bp}";
            if ( isset( $style[ $h_key ] ) && $style[ $h_key ] !== '' && $style[ $h_key ] !== null ) {
                $h_val = $style[ $h_key ];
                $decls[] = 'height: ' . ( is_numeric( $h_val ) ? intval( $h_val ) . 'px' : esc_attr( $h_val ) );
            }

            // tile_* responsive (style fields del tab "Stile" del builder).
            $resp_dim_map = [
                "tile_width_{$bp}"      => 'width',
                "tile_max_width_{$bp}"  => 'max-width',
                "tile_min_height_{$bp}" => 'min-height',
            ];
            foreach ( $resp_dim_map as $rkey => $rprop ) {
                if ( ! isset( $style[ $rkey ] ) || $style[ $rkey ] === '' || $style[ $rkey ] === null ) continue;
                $rv_dim = $this->safe_dim_value( $style[ $rkey ] );
                if ( $rv_dim !== '' ) $decls[] = $rprop . ': ' . $rv_dim;
            }

            // Display override (responsive visibility)
            $disp_key = "display_{$bp}";
            if ( isset( $style[ $disp_key ] ) && $style[ $disp_key ] !== '' && $style[ $disp_key ] !== null ) {
                $decls[] = 'display: ' . esc_attr( $style[ $disp_key ] );
            }

            // Flex direction override
            $fd_key = "flex_direction_{$bp}";
            if ( isset( $style[ $fd_key ] ) && $style[ $fd_key ] !== '' && $style[ $fd_key ] !== null ) {
                $decls[] = 'flex-direction: ' . esc_attr( $style[ $fd_key ] );
            }

            // Text align override
            $ta_key = "text_align_{$bp}";
            if ( isset( $style[ $ta_key ] ) && $style[ $ta_key ] !== '' && $style[ $ta_key ] !== null ) {
                $decls[] = 'text-align: ' . esc_attr( $style[ $ta_key ] );
            }

            // Positioning overrides (from advanced)
            if ( ! empty( $advanced ) ) {
                foreach ( [ 'top', 'left', 'bottom', 'right' ] as $dir ) {
                    $pk = "position_{$dir}_{$bp}";
                    if ( isset( $advanced[ $pk ] ) && $advanced[ $pk ] !== '' && $advanced[ $pk ] !== null ) {
                        $pv = $advanced[ $pk ];
                        $decls[] = $dir . ': ' . ( is_numeric( $pv ) ? $pv . 'px' : esc_attr( $pv ) );
                    }
                }
                $pw_key = "position_width_{$bp}";
                if ( isset( $advanced[ $pw_key ] ) && $advanced[ $pw_key ] !== '' && $advanced[ $pw_key ] !== null ) {
                    $pw_val = $advanced[ $pw_key ];
                    $decls[] = 'width: ' . ( is_numeric( $pw_val ) ? $pw_val . 'px' : esc_attr( $pw_val ) );
                }
                $pz_key = "position_zindex_{$bp}";
                if ( isset( $advanced[ $pz_key ] ) && $advanced[ $pz_key ] !== '' && $advanced[ $pz_key ] !== null ) {
                    $decls[] = 'z-index: ' . intval( $advanced[ $pz_key ] );
                }
            }

            // ── Effetti per-device — NORMALE (#id): opacity / text-shadow / backdrop ──
            $op_key = "opacity_{$bp}";
            if ( isset( $style[ $op_key ] ) && $style[ $op_key ] !== '' && $style[ $op_key ] !== null ) {
                $decls[] = 'opacity: ' . ( intval( $style[ $op_key ] ) / 100 ) . ' !important';
            }
            $ts_n = $this->build_text_shadow_decl( $style, "_{$bp}" );
            if ( $ts_n !== '' ) $decls[] = $ts_n;
            foreach ( $this->build_backdrop_decls( $style, "_{$bp}" ) as $d ) $decls[] = $d;

            // ── Effetti per-device — HOVER (#id:hover): opacity / transform / text-shadow / backdrop ──
            $h = is_array( $style['hover'] ?? null ) ? $style['hover'] : [];
            $h_decls = [];
            $h_op = $h[ "opacity_{$bp}" ] ?? null;
            if ( $h_op !== null && $h_op !== '' ) $h_decls[] = 'opacity: ' . ( intval( $h_op ) / 100 ) . ' !important';
            $h_t_parts = [];
            $hts = $h["transform_scale_{$bp}"]     ?? null; if ( $hts !== null && $hts !== '' ) $h_t_parts[] = 'scale(' . floatval( $hts ) . ')';
            $htx = $h["transform_translateX_{$bp}"] ?? null; if ( $htx !== null && $htx !== '' ) $h_t_parts[] = 'translateX(' . floatval( $htx ) . 'px)';
            $hty = $h["transform_translateY_{$bp}"] ?? null; if ( $hty !== null && $hty !== '' ) $h_t_parts[] = 'translateY(' . floatval( $hty ) . 'px)';
            $hro = $h["transform_rotate_{$bp}"]     ?? null; if ( $hro !== null && $hro !== '' ) $h_t_parts[] = 'rotate(' . intval( $hro ) . 'deg)';
            $hsx = $h["transform_skewX_{$bp}"]      ?? null; if ( $hsx !== null && $hsx !== '' ) $h_t_parts[] = 'skewX(' . intval( $hsx ) . 'deg)';
            $hsy = $h["transform_skewY_{$bp}"]      ?? null; if ( $hsy !== null && $hsy !== '' ) $h_t_parts[] = 'skewY(' . intval( $hsy ) . 'deg)';
            if ( ! empty( $h_t_parts ) ) $h_decls[] = 'transform: ' . implode( ' ', $h_t_parts ) . ' !important';
            $ts_h = $this->build_text_shadow_decl( $h, "_{$bp}" );
            if ( $ts_h !== '' ) $h_decls[] = $ts_h;
            foreach ( $this->build_backdrop_decls( $h, "_{$bp}" ) as $d ) $h_decls[] = $d;
            if ( ! empty( $h_decls ) ) {
                $this->responsive_css_rules[ $max_width ][] = '#' . esc_attr( $css_id ) . ':hover { ' . implode( '; ', $h_decls ) . '; }';
            }

            if ( ! empty( $decls ) ) {
                $sel = '#' . esc_attr( $css_id );
                $this->responsive_css_rules[ $max_width ][] = "{$sel} { " . implode( '; ', $decls ) . "; }";
            }
        }
    }

    /**
     * Collect custom CSS rules for an element.
     * Replaces the placeholder "selector" with the actual CSS selector (#css_id).
     */
    private function collect_custom_css( $settings, $css_id, &$hover_css_rules ) {
        $custom_css = trim( $settings['custom_css'] ?? '' );
        if ( $custom_css !== '' ) {
            $selector = '#' . esc_attr( $css_id );
            $custom_css = str_replace( 'selector', $selector, $custom_css );
            // Sanitize CSS for <style> block injection prevention
            $hover_css_rules[] = $this->safe_block_css( $custom_css );
        }
    }

    /**
     * Genera il CSS border per il wrapper di un tile, supportando entrambi i sistemi:
     *
     *   1. style.border (NUOVO, FieldBorder oggetto 4-side):
     *      { top, right, bottom, left, style, color, linked }
     *      → border:Npx style color  oppure  border-top/right/bottom/left:Npx style color
     *
     *   2. style.border_width + style.border_style + style.border_color (LEGACY uniforme).
     *      Usato SOLO se la chiave style.border non esiste affatto (template pre-v3.55.20).
     *
     * IMPORTANTE: la sola PRESENZA di array_key_exists('border', $style) significa che
     * l'utente ha toccato il nuovo controllo e quindi il sistema legacy va IGNORATO,
     * anche se il nuovo bordo è "spento" (tutti i lati a 0). Senza questo check, mettere
     * a 0 il nuovo bordo lascia visibile il vecchio salvato in border_width legacy.
     *
     * @param array $style  tile.style flat
     * @return string       CSS declarations (vuoto se nessun border)
     */
    private function build_wrapper_border_css( $style, $important = false ) {
        // $important=true aggiunge !important: serve SOLO al bordo per-device nelle
        // media query (collect_responsive_css), perché il bordo desktop è inline e
        // un selettore #id non lo sovrascriverebbe altrimenti. Inline desktop = false.
        $imp = $important ? ' !important' : '';
        // Sistema NUOVO: oggetto 4-side. Se la chiave esiste, vince sempre sul legacy.
        if ( array_key_exists( 'border', $style ) && is_array( $style['border'] ) ) {
            $b = $style['border'];
            $color = trim( $b['color'] ?? '' );
            $stylename = $b['style'] ?? 'solid';
            $top    = max( 0, intval( $b['top']    ?? 0 ) );
            $right  = max( 0, intval( $b['right']  ?? 0 ) );
            $bottom = max( 0, intval( $b['bottom'] ?? 0 ) );
            $left   = max( 0, intval( $b['left']   ?? 0 ) );
            $any    = $top || $right || $bottom || $left;
            if ( $color !== '' && $any && $stylename !== 'none' ) {
                $bs = $this->safe_border_style( $stylename );
                $bc = $this->safe_border_color( $color );
                if ( $top === $right && $right === $bottom && $bottom === $left ) {
                    return "border: {$top}px {$bs} {$bc}{$imp}";
                }
                $parts = [];
                if ( $top    ) $parts[] = "border-top: {$top}px {$bs} {$bc}{$imp}";
                if ( $right  ) $parts[] = "border-right: {$right}px {$bs} {$bc}{$imp}";
                if ( $bottom ) $parts[] = "border-bottom: {$bottom}px {$bs} {$bc}{$imp}";
                if ( $left   ) $parts[] = "border-left: {$left}px {$bs} {$bc}{$imp}";
                return implode( '; ', $parts );
            }
            // Nuovo sistema esiste ma utente l'ha messo a 0/vuoto → bordo OFF (no fallback).
            // Forza border:none così il legacy non riemerge via cascade CSS del tema.
            return 'border: none' . $imp;
        }

        // Sistema LEGACY: 3 chiavi piatte. Solo se la chiave 'border' nuova non esiste.
        if ( ! empty( $style['border_width'] ) && intval( $style['border_width'] ) > 0 && ( $style['border_style'] ?? 'solid' ) !== 'none' ) {
            $bw = intval( $style['border_width'] );
            $bs = $this->safe_border_style( $style['border_style'] ?? 'solid' );
            $bc = $this->safe_border_color( $style['border_color'] ?? 'transparent' );
            return "border: {$bw}px {$bs} {$bc}";
        }

        return '';
    }

    /**
     * True se il wrapper ha un border attivo. Identica policy di build_wrapper_border_css:
     * la presenza di style.border (oggetto) disattiva sempre il fallback legacy.
     */
    private function wrapper_has_border( $style ) {
        if ( array_key_exists( 'border', $style ) && is_array( $style['border'] ) ) {
            $b = $style['border'];
            $color = trim( $b['color'] ?? '' );
            $any   = intval( $b['top'] ?? 0 ) || intval( $b['right'] ?? 0 ) || intval( $b['bottom'] ?? 0 ) || intval( $b['left'] ?? 0 );
            return ( $color !== '' && $any && ( $b['style'] ?? 'solid' ) !== 'none' );
        }
        return ! empty( $style['border_width'] ) && intval( $style['border_width'] ) > 0 && ( $style['border_style'] ?? 'solid' ) !== 'none';
    }
}
