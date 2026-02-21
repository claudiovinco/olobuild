<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_LangSwitcher_Tile extends Olo_Tile_Base {

    protected $type     = 'langswitcher';
    protected $name     = 'Selettore lingua';
    protected $icon     = 'dashicons-translation';
    protected $category = 'header';
    protected $defaults = [
        'style'          => 'flags',
        'flag_shape'     => 'circle',
        'flag_size'      => 24,
        'show_label'     => false,
        'label_format'   => 'name',
        'layout'         => 'inline',
        'floating_pos'   => 'bottom-right',
        'gap'            => 8,
        'active_bg'      => '#6366F1',
        'active_color'   => '#ffffff',
        'bg'             => '#ffffff',
        'color'          => '#374151',
        'border_color'   => '#e5e7eb',
        'border_radius'  => 8,
        'show_dropdown_arrow' => true,
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        // Se olo-lang non è attivo, non renderizzare
        if ( ! class_exists( 'Olo_Lang_Language' ) ) {
            return '<p style="color:#999;text-align:center;font-size:12px;">Plugin Olo Lang non attivo.</p>';
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
        $radius  = absint( $s['border_radius'] );

        // Flag emoji map
        $flags = [
            'it' => '🇮🇹', 'en' => '🇬🇧', 'de' => '🇩🇪', 'fr' => '🇫🇷',
            'es' => '🇪🇸', 'pt' => '🇵🇹', 'nl' => '🇳🇱', 'ru' => '🇷🇺',
            'zh' => '🇨🇳', 'ja' => '🇯🇵', 'ko' => '🇰🇷', 'ar' => '🇸🇦',
            'pl' => '🇵🇱', 'cs' => '🇨🇿', 'sv' => '🇸🇪', 'da' => '🇩🇰',
            'fi' => '🇫🇮', 'no' => '🇳🇴', 'hu' => '🇭🇺', 'ro' => '🇷🇴',
            'el' => '🇬🇷', 'tr' => '🇹🇷', 'hr' => '🇭🇷', 'sk' => '🇸🇰',
            'sl' => '🇸🇮', 'bg' => '🇧🇬', 'uk' => '🇺🇦',
        ];

        // CSS custom properties
        $css_vars = sprintf(
            '--ols-size:%dpx;--ols-gap:%dpx;--ols-radius:%dpx;--ols-bg:%s;--ols-color:%s;--ols-active-bg:%s;--ols-active-color:%s;--ols-border:%s;',
            $size, $gap, $radius,
            esc_attr( $s['bg'] ), esc_attr( $s['color'] ),
            esc_attr( $s['active_bg'] ), esc_attr( $s['active_color'] ),
            esc_attr( $s['border_color'] )
        );

        // Classi wrapper
        $wrapper_class = 'ols-switcher ols-style--' . $style . ' ols-layout--' . $layout;
        if ( $layout === 'floating' ) {
            $wrapper_class .= ' ols-float--' . sanitize_key( $s['floating_pos'] );
        }
        if ( $shape === 'rounded' ) {
            $wrapper_class .= ' ols-shape--rounded';
        }

        ob_start();

        if ( $layout === 'dropdown' ) {
            $this->render_dropdown( $languages, $current, $flags, $s, $wrapper_class, $css_vars );
        } else {
            $this->render_inline( $languages, $current, $flags, $s, $wrapper_class, $css_vars );
        }

        return ob_get_clean();
    }

    private function render_inline( $languages, $current, $flags, $s, $wrapper_class, $css_vars ) {
        $style_attr = $s['style'];
        echo '<div class="' . esc_attr( $wrapper_class ) . '" style="' . $css_vars . '">';
        foreach ( $languages as $lang ) {
            $code   = $lang['code'];
            $url    = Olo_Lang_Language::get_language_url( $code );
            $active = $code === $current ? ' ols-active' : '';
            $flag   = $flags[ $code ] ?? '';

            echo '<a href="' . esc_url( $url ) . '" class="ols-item' . $active . '" hreflang="' . esc_attr( $code ) . '" title="' . esc_attr( $lang['name'] ) . '">';

            if ( $style_attr === 'flags' || $style_attr === 'flags_text' ) {
                echo '<span class="ols-flag" style="font-size:' . absint( $s['flag_size'] ) . 'px">' . $flag . '</span>';
            }
            if ( $style_attr === 'codes' || $style_attr === 'flags_text' ) {
                echo '<span class="ols-code">' . esc_html( strtoupper( $code ) ) . '</span>';
            }
            if ( $style_attr === 'names' ) {
                echo '<span class="ols-name">' . esc_html( $lang['name'] ) . '</span>';
            }
            if ( ! empty( $s['show_label'] ) && $style_attr === 'flags' ) {
                $label = $s['label_format'] === 'code' ? strtoupper( $code ) : $lang['name'];
                echo '<span class="ols-label">' . esc_html( $label ) . '</span>';
            }

            echo '</a>';
        }
        echo '</div>';
    }

    private function render_dropdown( $languages, $current, $flags, $s, $wrapper_class, $css_vars ) {
        $style_attr   = $s['style'];
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

        $flag = $flags[ $current_lang['code'] ] ?? '';

        echo '<div class="' . esc_attr( $wrapper_class ) . '" style="' . $css_vars . '">';
        echo '<div class="ols-dropdown">';

        // Trigger button
        echo '<button type="button" class="ols-trigger">';
        if ( $style_attr === 'flags' || $style_attr === 'flags_text' ) {
            echo '<span class="ols-flag" style="font-size:' . absint( $s['flag_size'] ) . 'px">' . $flag . '</span>';
        }
        if ( $style_attr === 'codes' || $style_attr === 'flags_text' ) {
            echo '<span class="ols-code">' . esc_html( strtoupper( $current_lang['code'] ) ) . '</span>';
        }
        if ( $style_attr === 'names' ) {
            echo '<span class="ols-name">' . esc_html( $current_lang['name'] ) . '</span>';
        }
        if ( ! empty( $s['show_dropdown_arrow'] ) ) {
            echo '<svg class="ols-arrow" width="12" height="12" viewBox="0 0 12 12"><path d="M2 4l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>';
        }
        echo '</button>';

        // Menu
        echo '<div class="ols-menu">';
        foreach ( $languages as $lang ) {
            if ( $lang['code'] === $current ) {
                continue;
            }
            $code = $lang['code'];
            $url  = Olo_Lang_Language::get_language_url( $code );
            $f    = $flags[ $code ] ?? '';

            echo '<a href="' . esc_url( $url ) . '" class="ols-option" hreflang="' . esc_attr( $code ) . '">';
            if ( $style_attr === 'flags' || $style_attr === 'flags_text' ) {
                echo '<span class="ols-flag" style="font-size:' . absint( $s['flag_size'] ) . 'px">' . $f . '</span>';
            }
            if ( $style_attr !== 'flags' ) {
                $label = $style_attr === 'names' ? $lang['name'] : strtoupper( $code );
                echo '<span class="ols-name">' . esc_html( $label ) . '</span>';
            }
            echo '</a>';
        }
        echo '</div>';

        echo '</div>';
        echo '</div>';

        // JS per aprire/chiudere il dropdown
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
