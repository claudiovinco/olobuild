<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_LangSwitcher_Tile extends Olo_Tile_Base {

    protected $type     = 'langswitcher';
    protected $name     = 'Selettore lingua';
    protected $icon     = 'dashicons-translation';
    protected $category = 'navigation';
    protected $defaults = [
        'style'          => 'flags',
        'flag_shape'     => 'circle',
        'flag_size'      => 24,
        'show_label'     => false,
        'label_format'   => 'name',
        'layout'         => 'inline',
        'floating_pos'   => 'bottom-right',
        'gap'            => 8,
        'active_bg'      => '',
        'active_color'   => '',
        'bg'             => '',
        'color'          => '',
        'border_color'   => '',
        'border_radius'  => 8,
        'show_dropdown_arrow' => true,
        // Tabs
        'tabs_edge'      => 'top',
        'tabs_offset'    => 20,
        'tabs_size'      => 'normal',
        // Circle badge
        'circle_bg'      => 'rgba(0,0,0,0.06)',
        'circle_border'  => 'rgba(0,0,0,0.1)',
        'circle_size'    => 36,
        // Compact
        'compact'        => false,
            'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        if ( ! class_exists( 'Olo_Lang_Language' ) ) {
            return '<p style="color:var(--olo-color-text-muted, #9CA3AF);text-align:center;font-size:12px;">Plugin Olo Lang non attivo.</p>';
        }

        $s = wp_parse_args( $settings, $this->defaults );

        $languages = Olo_Lang_Language::get_active_languages();
        if ( count( $languages ) < 2 ) {
            return '';
        }

        $current = Olo_Lang_Language::detect_current_lang();
        $style   = sanitize_key( $s['style'] );
        $layout  = sanitize_key( $s['layout'] );
        $shape   = sanitize_key( $s['flag_shape'] );
        $size    = absint( $s['flag_size'] );
        $gap     = absint( $s['gap'] );
        $radius  = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );
        $compact = ! empty( $s['compact'] );

        $flags = [
            'it' => '🇮🇹', 'en' => '🇬🇧', 'de' => '🇩🇪', 'fr' => '🇫🇷',
            'es' => '🇪🇸', 'pt' => '🇵🇹', 'nl' => '🇳🇱', 'ru' => '🇷🇺',
            'zh' => '🇨🇳', 'ja' => '🇯🇵', 'ko' => '🇰🇷', 'ar' => '🇸🇦',
            'pl' => '🇵🇱', 'cs' => '🇨🇿', 'sv' => '🇸🇪', 'da' => '🇩🇰',
            'fi' => '🇫🇮', 'no' => '🇳🇴', 'hu' => '🇭🇺', 'ro' => '🇷🇴',
            'el' => '🇬🇷', 'tr' => '🇹🇷', 'hr' => '🇭🇷', 'sk' => '🇸🇰',
            'sl' => '🇸🇮', 'bg' => '🇧🇬', 'uk' => '🇺🇦',
        ];

        // TOKEN-FIRST: vuoto → token tema (lingua attiva = primario brand, era #e1474f indaco)
        $css_vars = sprintf(
            '--ols-size:%dpx;--ols-gap:%dpx;--ols-radius:%s;--ols-bg:%s;--ols-color:%s;--ols-active-bg:%s;--ols-active-color:%s;--ols-border:%s;',
            $size, $gap, $radius,
            $this->safe_color_css( $s['bg'] ) ?: 'var(--olo-color-surface, #ffffff)',
            $this->safe_color_css( $s['color'] ) ?: 'var(--olo-color-text, #374151)',
            $this->safe_color_css( $s['active_bg'] ) ?: 'var(--olo-color-primary, #e1474f)',
            $this->safe_color_css( $s['active_color'] ) ?: 'var(--olo-color-primary-contrast, #ffffff)',
            $this->safe_color_css( $s['border_color'] ) ?: 'var(--olo-color-border, #e5e7eb)'
        );

        $wrapper_class = 'ols-switcher ols-style--' . $style . ' ols-layout--' . $layout;
        if ( $layout === 'floating' ) {
            $wrapper_class .= ' ols-float--' . sanitize_key( $s['floating_pos'] );
        }
        if ( $shape === 'rounded' ) {
            $wrapper_class .= ' ols-shape--rounded';
        }
        if ( $compact ) {
            $wrapper_class .= ' ols-compact';
        }

        $ols_uid = 'ols-uid-' . wp_unique_id();
        $wrapper_class .= ' ' . $ols_uid;

        ob_start();

        // a11y tastiera: anello di focus visibile su voci/linguette/trigger lingua
        echo '<style>.ols-switcher .ols-item:focus-visible,.ols-switcher .ols-trigger:focus-visible,.ols-switcher .ols-option:focus-visible,.ols-tab:focus-visible{outline:none;box-shadow:0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);}</style>';

        // Emit base styles for new features (circle flags, compact)
        if ( $style === 'flags_circle' || $compact ) {
            echo '<style>';
            if ( $style === 'flags_circle' ) {
                echo '.ols-circle-flag{display:inline-flex;align-items:center;justify-content:center;border-radius:50%;border:2px solid;line-height:1;flex-shrink:0;}';
            }
            if ( $compact ) {
                echo '.ols-compact .ols-item{padding:3px 6px !important;font-size:11px !important;gap:3px !important;}';
                echo '.ols-compact .ols-trigger{padding:3px 6px !important;font-size:11px !important;gap:3px !important;}';
                echo '.ols-compact .ols-code{font-size:10px !important;}';
                echo '.ols-compact .ols-flag{font-size:16px !important;}';
            }
            echo '</style>';
        }

        if ( $radius_hover_css !== '' ) {
            // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS built from the internally generated $ols_uid and the absint()-based Olo_Tile_Utils::radius_force_css() value.
            echo '<style>'
                . '.' . $ols_uid . ' .ols-item,.' . $ols_uid . ' .ols-trigger{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}'
                . '.' . $ols_uid . ' .ols-item:hover,.' . $ols_uid . ' .ols-trigger:hover{border-radius:' . $radius_hover_css . ' !important}'
                . '</style>';
            // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        if ( $layout === 'tabs' ) {
            $this->render_tabs( $languages, $current, $flags, $s, $wrapper_class, $css_vars );
        } elseif ( $layout === 'dropdown' ) {
            $this->render_dropdown( $languages, $current, $flags, $s, $wrapper_class, $css_vars );
        } else {
            $this->render_inline( $languages, $current, $flags, $s, $wrapper_class, $css_vars );
        }

                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$ols_uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$ols_uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$ols_uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_css() from sanitized border settings; $ols_uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base border helpers from sanitized border settings
        }
        return ob_get_clean();
    }

    /**
     * Render a single language item's inner content (flag/circle/code/name).
     */
    private function render_item_content( $lang, $flags, $s ) {
        $style_attr = $s['style'];
        $code       = $lang['code'];
        $flag       = $flags[ $code ] ?? '';

        if ( $style_attr === 'flags_circle' ) {
            $csz   = max( 24, intval( $s['circle_size'] ) );
            $fsz   = round( $csz * 0.55 );
            $c_bg  = $this->safe_color_css( $s['circle_bg'] ?? '' ) ?: 'rgba(0,0,0,0.06)';
            $c_brd = $this->safe_color_css( $s['circle_border'] ?? '' ) ?: 'rgba(0,0,0,0.1)';
            echo '<span class="ols-circle-flag" style="width:' . (int) $csz . 'px;height:' . (int) $csz . 'px;font-size:' . (int) $fsz . 'px;background:' . esc_attr( $c_bg ) . ';border-color:' . esc_attr( $c_brd ) . '">' . esc_html( $flag ) . '</span>';
        }

        if ( $style_attr === 'flags' || $style_attr === 'flags_text' ) {
            echo '<span class="ols-flag" style="font-size:' . absint( $s['flag_size'] ) . 'px">' . esc_html( $flag ) . '</span>';
        }
        if ( $style_attr === 'codes' || $style_attr === 'flags_text' ) {
            echo '<span class="ols-code">' . esc_html( strtoupper( $code ) ) . '</span>';
        }
        if ( $style_attr === 'names' ) {
            echo '<span class="ols-name">' . esc_html( $lang['name'] ) . '</span>';
        }
        if ( ! empty( $s['show_label'] ) && ( $style_attr === 'flags' || $style_attr === 'flags_circle' ) ) {
            $label = $s['label_format'] === 'code' ? strtoupper( $code ) : $lang['name'];
            echo '<span class="ols-label">' . esc_html( $label ) . '</span>';
        }
    }

    private function render_inline( $languages, $current, $flags, $s, $wrapper_class, $css_vars ) {
        echo '<div class="' . esc_attr( $wrapper_class ) . '" style="' . $css_vars . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $css_vars is sprintf()-built in render() from %d integers, the absint()-based Olo_Tile_Utils::border_radius() value and safe_color_css() whitelisted colors only
        foreach ( $languages as $lang ) {
            $code   = $lang['code'];
            $url    = Olo_Lang_Language::get_language_url( $code );
            $active = $code === $current ? ' ols-active' : '';

            echo '<a href="' . esc_url( $url ) . '" class="ols-item' . $active . '" hreflang="' . esc_attr( $code ) . '" title="' . esc_attr( $lang['name'] ) . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $active is a fixed ' ols-active'/'' literal from the ternary above; url/code/name escaped inline
            $this->render_item_content( $lang, $flags, $s );
            echo '</a>';
        }
        echo '</div>';
    }

    /**
     * Render as fixed tabs on page edge.
     */
    private function render_tabs( $languages, $current, $flags, $s, $wrapper_class, $css_vars ) {
        $edge   = in_array( $s['tabs_edge'], [ 'top', 'left', 'right' ], true ) ? $s['tabs_edge'] : 'top';
        $offset = max( 0, intval( $s['tabs_offset'] ) );
        $t_size = $s['tabs_size'] ?? 'normal';

        $uid = 'ols-tab-' . substr( md5( wp_json_encode( $s ) ), 0, 6 );

        // Size multipliers
        $pad_map  = [ 'tiny' => '3px 5px', 'small' => '5px 8px', 'normal' => '7px 12px' ];
        $font_map = [ 'tiny' => '10px', 'small' => '12px', 'normal' => '14px' ];
        $flag_sz  = [ 'tiny' => 14, 'small' => 18, 'normal' => absint( $s['flag_size'] ) ];
        $pad      = $pad_map[ $t_size ] ?? $pad_map['normal'];
        $font     = $font_map[ $t_size ] ?? $font_map['normal'];
        $fsz      = $flag_sz[ $t_size ] ?? $flag_sz['normal'];

        // TOKEN-FIRST: linguetta attiva = primario brand (era #e1474f indaco off-brand)
        $bg_color     = $this->safe_color_css( $s['bg'] ) ?: 'var(--olo-color-surface, #ffffff)';
        $text_color   = $this->safe_color_css( $s['color'] ) ?: 'var(--olo-color-text, #374151)';
        $active_bg    = $this->safe_color_css( $s['active_bg'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $active_color = $this->safe_color_css( $s['active_color'] ) ?: 'var(--olo-color-primary-contrast, #ffffff)';
        $border_color = $this->safe_color_css( $s['border_color'] ) ?: 'transparent';

        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: colors via the safe_color_css() whitelist, integers via intval()/absint()/max()/round(), paddings/font sizes from fixed literal maps; $uid is internally generated.
        echo '<style>';
        echo ".{$uid}{position:fixed;z-index:10050;display:flex;gap:2px;}";

        if ( $edge === 'top' ) {
            echo ".{$uid}{top:0;left:{$offset}px;flex-direction:row;}";
            echo ".{$uid} .ols-tab{border-radius:0 0 6px 6px;border-top:none;}";
        } elseif ( $edge === 'right' ) {
            echo ".{$uid}{top:{$offset}px;right:0;flex-direction:column;}";
            echo ".{$uid} .ols-tab{border-radius:6px 0 0 6px;border-right:none;}";
        } else {
            echo ".{$uid}{top:{$offset}px;left:0;flex-direction:column;}";
            echo ".{$uid} .ols-tab{border-radius:0 6px 6px 0;border-left:none;}";
        }

        echo ".{$uid} .ols-tab{display:flex;align-items:center;justify-content:center;gap:3px;";
        echo "padding:{$pad};font-size:{$font};font-weight:600;text-decoration:none;";
        echo "background:{$bg_color};color:{$text_color};border:1px solid {$border_color};";
        echo "transition:all .2s;cursor:pointer;}";

        echo ".{$uid} .ols-tab:hover{opacity:.8;}";
        echo ".{$uid} .ols-tab.ols-active{background:{$active_bg};color:{$active_color};border-color:{$active_bg};}";
        echo ".{$uid} .ols-tab .ols-flag{font-size:{$fsz}px;line-height:1;}";

        // Circle flags inside tabs
        if ( $s['style'] === 'flags_circle' ) {
            $csz = $t_size === 'tiny' ? 22 : ( $t_size === 'small' ? 28 : max( 24, intval( $s['circle_size'] ) ) );
            $c_bg  = $this->safe_color_css( $s['circle_bg'] ?? '' ) ?: 'rgba(255,255,255,0.2)';
            $c_brd = $this->safe_color_css( $s['circle_border'] ?? '' ) ?: 'rgba(255,255,255,0.3)';
            echo ".{$uid} .ols-circle-flag{width:{$csz}px;height:{$csz}px;font-size:" . round( $csz * 0.55 ) . "px;";
            echo "border-radius:50%;border:2px solid {$c_brd};background:{$c_bg};display:inline-flex;align-items:center;justify-content:center;line-height:1;}";
        }

        echo '</style>';
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped

        echo '<div class="' . esc_attr( $uid ) . '">';
        foreach ( $languages as $lang ) {
            $code   = $lang['code'];
            $url    = Olo_Lang_Language::get_language_url( $code );
            $active = $code === $current ? ' ols-active' : '';

            echo '<a href="' . esc_url( $url ) . '" class="ols-tab' . $active . '" hreflang="' . esc_attr( $code ) . '" title="' . esc_attr( $lang['name'] ) . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $active is a fixed ' ols-active'/'' literal from the ternary above; url/code/name escaped inline
            $this->render_item_content( $lang, $flags, $s );
            echo '</a>';
        }
        echo '</div>';
    }

    private function render_dropdown( $languages, $current, $flags, $s, $wrapper_class, $css_vars ) {
        $current_lang = null;
        foreach ( $languages as $lang ) {
            if ( $lang['code'] === $current ) {
                $current_lang = $lang;
                break;
            }
        }
        if ( ! $current_lang ) {
            $current_lang = $languages[0];
        }

        echo '<div class="' . esc_attr( $wrapper_class ) . '" style="' . $css_vars . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $css_vars is sprintf()-built in render() from %d integers, the absint()-based Olo_Tile_Utils::border_radius() value and safe_color_css() whitelisted colors only
        echo '<div class="ols-dropdown">';

        echo '<button type="button" class="ols-trigger">';
        $this->render_item_content( $current_lang, $flags, $s );
        if ( ! empty( $s['show_dropdown_arrow'] ) ) {
            echo '<svg class="ols-arrow" width="12" height="12" viewBox="0 0 12 12"><path d="M2 4l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>';
        }
        echo '</button>';

        echo '<div class="ols-menu">';
        foreach ( $languages as $lang ) {
            if ( $lang['code'] === $current ) continue;
            $code = $lang['code'];
            $url  = Olo_Lang_Language::get_language_url( $code );

            echo '<a href="' . esc_url( $url ) . '" class="ols-option" hreflang="' . esc_attr( $code ) . '">';
            $this->render_item_content( $lang, $flags, $s );
            echo '</a>';
        }
        echo '</div>';
        echo '</div>';
        echo '</div>';

        echo '<script>
        (function(){
            document.querySelectorAll(".ols-dropdown").forEach(function(el){
                if(el._olsInit) return;
                el._olsInit = true;
                var trigger = el.querySelector(".ols-trigger");
                if(!trigger) return;
                trigger.addEventListener("click", function(e){
                    e.stopPropagation();
                    el.classList.toggle("ols-open");
                });
                document.addEventListener("click", function(){
                    el.classList.remove("ols-open");
                });
            });
        })();
        </script>';
    }
}
