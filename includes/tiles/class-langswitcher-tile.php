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
            '--olsb-size:%dpx;--olsb-gap:%dpx;--olsb-radius:%s;--olsb-bg:%s;--olsb-color:%s;--olsb-active-bg:%s;--olsb-active-color:%s;--olsb-border:%s;',
            $size, $gap, $radius,
            $this->safe_color_css( $s['bg'] ) ?: 'var(--olo-color-surface, #ffffff)',
            $this->safe_color_css( $s['color'] ) ?: 'var(--olo-color-text, #374151)',
            $this->safe_color_css( $s['active_bg'] ) ?: 'var(--olo-color-primary, #e1474f)',
            $this->safe_color_css( $s['active_color'] ) ?: 'var(--olo-color-primary-contrast, #ffffff)',
            $this->safe_color_css( $s['border_color'] ) ?: 'var(--olo-color-border, #e5e7eb)'
        );

        $wrapper_class = 'olsb-switcher olsb-style--' . $style . ' olsb-layout--' . $layout;
        if ( $layout === 'floating' ) {
            $wrapper_class .= ' olsb-float--' . sanitize_key( $s['floating_pos'] );
        }
        if ( $shape === 'rounded' ) {
            $wrapper_class .= ' olsb-shape--rounded';
        }
        if ( $compact ) {
            $wrapper_class .= ' olsb-compact';
        }

        $ols_uid = 'olsb-uid-' . wp_unique_id();
        $wrapper_class .= ' ' . $ols_uid;

        ob_start();

        // CSS base del selettore (look moderno, consuma le --olsb-*). Emesso una volta.
        $this->base_styles();

        // Scrittura cookie `olo_lang` al click (gemello del JS di OLOlang). Senza questa,
        // tornando alla lingua DEFAULT (URL senza prefisso) il cookie residuo della lingua
        // precedente farebbe da fallback lato server e il redirect ti riporterebbe indietro.
        $this->switch_cookie_script();

        // a11y tastiera: anello di focus visibile su voci/linguette/trigger lingua
        echo '<style>.olsb-switcher .olsb-item:focus-visible,.olsb-switcher .olsb-trigger:focus-visible,.olsb-switcher .olsb-option:focus-visible,.olsb-tab:focus-visible{outline:none;box-shadow:0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);}</style>';

        // Emit base styles for new features (circle flags, compact)
        if ( $style === 'flags_circle' || $compact ) {
            echo '<style>';
            if ( $style === 'flags_circle' ) {
                echo '.olsb-circle-flag{display:inline-flex;align-items:center;justify-content:center;border-radius:50%;border:2px solid;line-height:1;flex-shrink:0;}';
            }
            if ( $compact ) {
                echo '.olsb-compact .olsb-item{padding:3px 6px !important;font-size:11px !important;gap:3px !important;}';
                echo '.olsb-compact .olsb-trigger{padding:3px 6px !important;font-size:11px !important;gap:3px !important;}';
                echo '.olsb-compact .olsb-code{font-size:10px !important;}';
                echo '.olsb-compact .olsb-flag{font-size:16px !important;}';
            }
            echo '</style>';
        }

        if ( $radius_hover_css !== '' ) {
            // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS built from the internally generated $ols_uid and the absint()-based Olo_Tile_Utils::radius_force_css() value.
            echo '<style>'
                . '.' . $ols_uid . ' .olsb-item,.' . $ols_uid . ' .olsb-trigger{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}'
                . '.' . $ols_uid . ' .olsb-item:hover,.' . $ols_uid . ' .olsb-trigger:hover{border-radius:' . $radius_hover_css . ' !important}'
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
     * SVG bandiera inline (gemello PHP di src/utils/flagSvg.js — TENERE ALLINEATI).
     * Le flag-emoji non esistono su Windows → SVG. Ritorna '' se non disponibile.
     */
    private static function flag_inner( $key ) {
        static $f = null;
        if ( null === $f ) {
            $f = [
                'it' => '<rect width="3" height="2" fill="#fff"/><rect width="1" height="2" fill="#009246"/><rect x="2" width="1" height="2" fill="#ce2b37"/>',
                'fr' => '<rect width="3" height="2" fill="#fff"/><rect width="1" height="2" fill="#002654"/><rect x="2" width="1" height="2" fill="#ce1126"/>',
                'de' => '<rect width="5" height="1" fill="#000"/><rect width="5" height="1" y="1" fill="#d00"/><rect width="5" height="1" y="2" fill="#ffce00"/>',
                'es' => '<rect width="3" height="2" fill="#c60b1e"/><rect width="3" height="1" y="0.5" fill="#ffc400"/>',
                'pt' => '<rect width="30" height="20" fill="#da291c"/><rect width="12" height="20" fill="#046a38"/><circle cx="12" cy="10" r="3.2" fill="none" stroke="#ffe600" stroke-width="1.1"/>',
                'nl' => '<rect width="3" height="2" fill="#fff"/><rect width="3" height="0.667" fill="#ae1c28"/><rect width="3" height="0.667" y="1.333" fill="#21468b"/>',
                'ru' => '<rect width="3" height="2" fill="#fff"/><rect width="3" height="0.667" y="0.667" fill="#0039a6"/><rect width="3" height="0.667" y="1.333" fill="#d52b1e"/>',
                'pl' => '<rect width="8" height="5" fill="#fff"/><rect width="8" height="2.5" y="2.5" fill="#dc143c"/>',
                'ja' => '<rect width="3" height="2" fill="#fff"/><circle cx="1.5" cy="1" r="0.6" fill="#bc002d"/>',
                'gb' => '<clipPath id="uks"><path d="M0,0 v30 h60 v-30 z"/></clipPath><clipPath id="ukt"><path d="M30,15 h30 v15 z v15 h-30 z h-30 v-15 z v-15 h30 z"/></clipPath><g clip-path="url(#uks)"><path d="M0,0 v30 h60 v-30 z" fill="#012169"/><path d="M0,0 L60,30 M60,0 L0,30" stroke="#fff" stroke-width="6"/><path d="M0,0 L60,30 M60,0 L0,30" clip-path="url(#ukt)" stroke="#C8102E" stroke-width="4"/><path d="M30,0 v30 M0,15 h60" stroke="#fff" stroke-width="10"/><path d="M30,0 v30 M0,15 h60" stroke="#C8102E" stroke-width="6"/></g>',
                'us' => '<rect width="60" height="32" fill="#fff"/><g fill="#b22234"><rect width="60" height="2.46"/><rect y="4.92" width="60" height="2.46"/><rect y="9.84" width="60" height="2.46"/><rect y="14.76" width="60" height="2.46"/><rect y="19.68" width="60" height="2.46"/><rect y="24.6" width="60" height="2.46"/><rect y="29.52" width="60" height="2.46"/></g><rect width="24" height="17.22" fill="#3c3b6e"/><g fill="#fff"><circle cx="3" cy="2.4" r="0.7"/><circle cx="7.2" cy="2.4" r="0.7"/><circle cx="11.4" cy="2.4" r="0.7"/><circle cx="15.6" cy="2.4" r="0.7"/><circle cx="19.8" cy="2.4" r="0.7"/><circle cx="3" cy="6.7" r="0.7"/><circle cx="7.2" cy="6.7" r="0.7"/><circle cx="11.4" cy="6.7" r="0.7"/><circle cx="15.6" cy="6.7" r="0.7"/><circle cx="19.8" cy="6.7" r="0.7"/><circle cx="3" cy="11" r="0.7"/><circle cx="7.2" cy="11" r="0.7"/><circle cx="11.4" cy="11" r="0.7"/><circle cx="15.6" cy="11" r="0.7"/><circle cx="19.8" cy="11" r="0.7"/><circle cx="3" cy="15.3" r="0.7"/><circle cx="7.2" cy="15.3" r="0.7"/><circle cx="11.4" cy="15.3" r="0.7"/><circle cx="15.6" cy="15.3" r="0.7"/><circle cx="19.8" cy="15.3" r="0.7"/></g>',
                'zh' => '<rect width="30" height="20" fill="#de2910"/><path d="M5,2.2 6.18,5.83 10,5.83 6.9,8.07 8.09,11.7 5,9.46 1.91,11.7 3.1,8.07 0,5.83 3.82,5.83 z" fill="#ffde00"/><g fill="#ffde00"><circle cx="10" cy="2" r="0.9"/><circle cx="12" cy="4" r="0.9"/><circle cx="12" cy="7" r="0.9"/><circle cx="10" cy="9" r="0.9"/></g>',
                'ko' => '<rect width="36" height="24" fill="#fff"/><circle cx="18" cy="12" r="5" fill="#cd2e3a"/><path d="M18,7 a2.5,2.5 0 0,1 0,5 a2.5,2.5 0 0,0 0,5 a5,5 0 0,1 0,-10" fill="#0047a0"/><g fill="#000"><g transform="translate(5,5)"><rect width="5" height="0.7"/><rect y="1.3" width="5" height="0.7"/><rect y="2.6" width="5" height="0.7"/></g><g transform="translate(26,5)"><rect width="5" height="0.7"/><rect y="1.3" width="2.1" height="0.7"/><rect x="2.9" y="1.3" width="2.1" height="0.7"/><rect y="2.6" width="5" height="0.7"/></g><g transform="translate(5,15.7)"><rect width="2.1" height="0.7"/><rect x="2.9" width="2.1" height="0.7"/><rect y="1.3" width="5" height="0.7"/><rect y="2.6" width="2.1" height="0.7"/><rect x="2.9" y="2.6" width="2.1" height="0.7"/></g><g transform="translate(26,15.7)"><rect width="2.1" height="0.7"/><rect x="2.9" width="2.1" height="0.7"/><rect y="1.3" width="2.1" height="0.7"/><rect x="2.9" y="1.3" width="2.1" height="0.7"/><rect y="2.6" width="2.1" height="0.7"/><rect x="2.9" y="2.6" width="2.1" height="0.7"/></g></g>',
                'ar' => '<rect width="36" height="24" fill="#006c35"/><rect x="7" y="15.4" width="22" height="1.4" rx="0.7" fill="#fff"/><path d="M7,16.1 l-2.6,-1.3 0,2.6 z" fill="#fff"/>',
                'cs' => '<rect width="36" height="12" fill="#fff"/><rect y="12" width="36" height="12" fill="#d7141a"/><path d="M0,0 L18,12 L0,24 z" fill="#11457e"/>',
                'sv' => '<rect width="16" height="10" fill="#006aa7"/><rect x="5" width="2" height="10" fill="#fecc00"/><rect y="4" width="16" height="2" fill="#fecc00"/>',
                'da' => '<rect width="16" height="12" fill="#c8102e"/><rect x="5" width="2" height="12" fill="#fff"/><rect y="5" width="16" height="2" fill="#fff"/>',
                'fi' => '<rect width="18" height="11" fill="#fff"/><rect x="5" width="3" height="11" fill="#003580"/><rect y="4" width="18" height="3" fill="#003580"/>',
                'no' => '<rect width="22" height="16" fill="#ef2b2d"/><rect x="6" width="4" height="16" fill="#fff"/><rect y="6" width="22" height="4" fill="#fff"/><rect x="7" width="2" height="16" fill="#002868"/><rect y="7" width="22" height="2" fill="#002868"/>',
                'hu' => '<rect width="3" height="2" fill="#fff"/><rect width="3" height="0.667" fill="#ce2939"/><rect width="3" height="0.667" y="1.333" fill="#477050"/>',
                'ro' => '<rect width="3" height="2" fill="#fcd116"/><rect width="1" height="2" fill="#002b7f"/><rect x="2" width="1" height="2" fill="#ce1126"/>',
                'bg' => '<rect width="3" height="2" fill="#fff"/><rect width="3" height="0.667" y="0.667" fill="#00966e"/><rect width="3" height="0.667" y="1.333" fill="#d62612"/>',
                'uk' => '<rect width="3" height="2" fill="#ffd500"/><rect width="3" height="1" fill="#005bbb"/>',
                'el' => '<rect width="27" height="18" fill="#0d5eaf"/><g fill="#fff"><rect y="2" width="27" height="2"/><rect y="6" width="27" height="2"/><rect y="10" width="27" height="2"/><rect y="14" width="27" height="2"/></g><rect width="10" height="10" fill="#0d5eaf"/><rect x="4" width="2" height="10" fill="#fff"/><rect y="4" width="10" height="2" fill="#fff"/>',
                'tr' => '<rect width="30" height="20" fill="#e30a17"/><circle cx="11" cy="10" r="5" fill="#fff"/><circle cx="12.5" cy="10" r="4" fill="#e30a17"/><path d="M18,7.6 l0.9,2 2.2,0.2 -1.7,1.4 0.5,2.1 -1.9,-1.1 -1.9,1.1 0.5,-2.1 -1.7,-1.4 2.2,-0.2 z" fill="#fff"/>',
                'hr' => '<rect width="3" height="2" fill="#fff"/><rect width="3" height="0.667" fill="#ff0000"/><rect width="3" height="0.667" y="1.333" fill="#171796"/>',
                'sk' => '<rect width="3" height="2" fill="#fff"/><rect width="3" height="0.667" y="0.667" fill="#0b4ea2"/><rect width="3" height="0.667" y="1.333" fill="#ee1c25"/>',
                'sl' => '<rect width="3" height="2" fill="#fff"/><rect width="3" height="0.667" y="0.667" fill="#0000c6"/><rect width="3" height="0.667" y="1.333" fill="#ed1c24"/>',
            ];
        }
        return $f[ $key ] ?? '';
    }

    private function flag_svg( $code, $aria = '' ) {
        $alias = [ 'en' => 'gb', 'en-gb' => 'gb', 'en-us' => 'us', 'pt-br' => 'pt', 'cn' => 'zh', 'jp' => 'ja', 'ua' => 'uk' ];
        $c   = strtolower( (string) $code );
        $key = $alias[ $c ] ?? $c;
        $inner = self::flag_inner( $key );
        if ( '' === $inner ) {
            return '';
        }
        $vb_map = [
            'it' => '0 0 3 2', 'fr' => '0 0 3 2', 'de' => '0 0 5 3', 'es' => '0 0 3 2',
            'pt' => '0 0 30 20', 'nl' => '0 0 3 2', 'ru' => '0 0 3 2', 'pl' => '0 0 8 5',
            'ja' => '0 0 3 2', 'gb' => '0 0 60 30', 'us' => '0 0 60 32', 'zh' => '0 0 30 20',
            'ko' => '0 0 36 24', 'ar' => '0 0 36 24', 'cs' => '0 0 36 24',
            'sv' => '0 0 16 10', 'da' => '0 0 16 12', 'fi' => '0 0 18 11', 'no' => '0 0 22 16',
            'hu' => '0 0 3 2', 'ro' => '0 0 3 2', 'bg' => '0 0 3 2', 'uk' => '0 0 3 2',
            'el' => '0 0 27 18', 'tr' => '0 0 30 20', 'hr' => '0 0 3 2', 'sk' => '0 0 3 2', 'sl' => '0 0 3 2',
        ];
        $vb   = $vb_map[ $key ] ?? '0 0 3 2';
        $a11y = $aria ? 'role="img" aria-label="' . esc_attr( $aria ) . '"' : 'aria-hidden="true"';
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="' . $vb . '" preserveAspectRatio="xMidYMid slice" ' . $a11y . ' focusable="false" style="width:100%;height:100%;display:block">' . $inner . '</svg>';
    }

    /**
     * Bandiera "a moneta"/rettangolo arrotondato (look moderno), con anello sottile.
     * Fallback a badge col codice (es. "KO") se la bandiera non è disponibile.
     */
    private function render_flag( $code, $name, $s ) {
        $style     = $s['style'];
        $is_circle = ( 'flags_circle' === $style );
        $circle    = $is_circle || ( ( $s['flag_shape'] ?? 'circle' ) === 'circle' && 'flags_text' !== $style );
        $sz        = $is_circle ? max( 20, intval( $s['circle_size'] ) ) : absint( $s['flag_size'] );
        if ( $sz < 12 ) { $sz = 24; }
        $w  = $circle ? $sz : (int) round( $sz * 1.38 );
        $br = $circle ? '50%' : max( 3, (int) round( $sz * 0.22 ) ) . 'px';
        $extra = '';
        if ( $is_circle ) {
            $cbg = $this->safe_color_css( $s['circle_bg'] ?? '' );
            $cbd = $this->safe_color_css( $s['circle_border'] ?? '' );
            if ( $cbg ) { $extra .= 'background:' . $cbg . ';'; }
            if ( $cbd ) { $extra .= 'box-shadow:0 0 0 2px ' . $cbd . ';'; }
        }
        $svg   = $this->flag_svg( $code, $name );
        $inner = $svg ? $svg : '<span class="olsb-flag-code">' . esc_html( strtoupper( $code ) ) . '</span>';
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $svg is internal fixed SVG markup; $w/$sz/$br ints/literals; $extra from safe_color_css() whitelist; code escaped.
        echo '<span class="olsb-flag" style="width:' . (int) $w . 'px;height:' . (int) $sz . 'px;border-radius:' . $br . ';' . $extra . '">' . $inner . '</span>';
    }

    /**
     * CSS base del selettore (emesso UNA volta per pagina). Mancava del tutto sul
     * frontend → gli item erano <a> nudi ("grezzo"). Consuma le --olsb-* di render().
     */
    private function base_styles() {
        static $done = false;
        if ( $done ) { return; }
        $done = true;
        echo '<style id="olsb-base">'
            . '.olsb-switcher{display:inline-flex;align-items:center;gap:var(--olsb-gap,8px);flex-wrap:wrap;font-family:inherit;line-height:1}'
            . '.olsb-switcher .olsb-item,.olsb-switcher .olsb-trigger{display:inline-flex;align-items:center;gap:7px;padding:6px 12px;background:var(--olsb-bg);color:var(--olsb-color);border:1px solid var(--olsb-border);border-radius:var(--olsb-radius,8px);font-size:13px;font-weight:500;text-decoration:none;line-height:1;cursor:pointer;font-family:inherit;transition:transform .15s,box-shadow .15s,background .15s,color .15s}'
            . '.olsb-switcher .olsb-trigger{outline:none}'
            . '.olsb-switcher .olsb-item:hover,.olsb-switcher .olsb-trigger:hover{transform:translateY(-1px);box-shadow:0 3px 8px rgba(16,24,40,.12)}'
            . '.olsb-switcher .olsb-item.olsb-active{background:var(--olsb-active-bg);color:var(--olsb-active-color);border-color:var(--olsb-active-bg)}'
            . '.olsb-flag{display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;background:#fff;box-shadow:0 0 0 1px rgba(16,24,40,.12),0 1px 2px rgba(16,24,40,.18)}'
            . '.olsb-flag svg{display:block;width:100%;height:100%}'
            . '.olsb-flag-code{display:inline-flex;align-items:center;justify-content:center;width:100%;height:100%;font-size:10px;font-weight:800;letter-spacing:.3px;color:var(--olo-color-text,#374151);background:var(--olo-color-surface-alt,#f1f3f5)}'
            . '.olsb-code{font-weight:700;font-size:12px;letter-spacing:.5px}.olsb-name{font-weight:500}.olsb-label{font-size:10px;opacity:.8;margin-left:2px}.olsb-arrow{opacity:.55;margin-left:2px}'
            . '.olsb-dropdown{position:relative;display:inline-block}'
            . '.olsb-menu{position:absolute;top:calc(100% + 6px);left:0;min-width:160px;display:flex;flex-direction:column;gap:2px;padding:6px;background:var(--olo-color-surface,#fff);border:1px solid var(--olo-color-border,#e5e7eb);border-radius:10px;box-shadow:0 8px 24px rgba(16,24,40,.16);opacity:0;visibility:hidden;transform:translateY(-4px);transition:opacity .15s,transform .15s,visibility .15s;z-index:2147483000}'
            . '.olsb-menu.olsb-open{opacity:1;visibility:visible;transform:translateY(0)}'
            . '.olsb-option{display:inline-flex;align-items:center;gap:7px;padding:6px 10px;border-radius:7px;text-decoration:none;color:var(--olo-color-text,#1f2937);font-size:13px;white-space:nowrap;transition:background .12s}'
            . '.olsb-option:hover{background:var(--olo-color-surface-alt,#f1f3f5)}'
            . '.olsb-layout--floating{position:fixed;z-index:10050}'
            . '.olsb-float--bottom-right{bottom:20px;right:20px}.olsb-float--bottom-left{bottom:20px;left:20px}.olsb-float--top-right{top:20px;right:20px}.olsb-float--top-left{top:20px;left:20px}.olsb-float--middle-right{top:50%;right:20px;transform:translateY(-50%)}.olsb-float--middle-left{top:50%;left:20px;transform:translateY(-50%)}'
            . '</style>';
    }

    /**
     * Scrive il cookie `olo_lang` al click su una voce del selettore (gemello del JS di
     * OLOlang assets/js/frontend.js). Indispensabile per tornare alla lingua DEFAULT:
     * senza, il cookie della lingua precedente resta e il redirect lato server rimanda
     * indietro. Delega su document in fase di cattura → funziona anche per il menu del
     * dropdown che viene spostato nel <body>. Emesso UNA volta per pagina.
     */
    private function switch_cookie_script() {
        static $done = false;
        if ( $done ) { return; }
        $done = true;
        echo '<script>(function(){'
            . 'function setLang(c){if(!c)return;var s=location.protocol==="https:"?"; Secure":"";'
            . 'document.cookie="olo_lang="+encodeURIComponent(c)+"; path=/; max-age=31536000; SameSite=Lax"+s;}'
            . 'document.addEventListener("click",function(e){'
            . 'var t=e.target&&e.target.closest?e.target.closest(".olsb-item[hreflang],.olsb-option[hreflang],.olsb-tab[hreflang]"):null;'
            . 'if(t){setLang(t.getAttribute("hreflang"));}'
            . '},true);'
            . '})();</script>';
    }

    /**
     * Render a single language item's inner content (flag/circle/code/name).
     */
    private function render_item_content( $lang, $flags, $s ) {
        $style_attr = $s['style'];
        $code       = $lang['code'];

        // Bandiera SVG "a moneta"/rettangolo (no emoji: invisibili su Windows).
        if ( in_array( $style_attr, [ 'flags', 'flags_text', 'flags_circle' ], true ) ) {
            $this->render_flag( $code, $lang['name'], $s );
        }
        if ( $style_attr === 'codes' || $style_attr === 'flags_text' ) {
            echo '<span class="olsb-code">' . esc_html( strtoupper( $code ) ) . '</span>';
        }
        if ( $style_attr === 'names' ) {
            echo '<span class="olsb-name">' . esc_html( $lang['name'] ) . '</span>';
        }
        if ( ! empty( $s['show_label'] ) && ( $style_attr === 'flags' || $style_attr === 'flags_circle' ) ) {
            $label = $s['label_format'] === 'code' ? strtoupper( $code ) : $lang['name'];
            echo '<span class="olsb-label">' . esc_html( $label ) . '</span>';
        }
    }

    private function render_inline( $languages, $current, $flags, $s, $wrapper_class, $css_vars ) {
        echo '<div class="' . esc_attr( $wrapper_class ) . '" style="' . $css_vars . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $css_vars is sprintf()-built in render() from %d integers, the absint()-based Olo_Tile_Utils::border_radius() value and safe_color_css() whitelisted colors only
        foreach ( $languages as $lang ) {
            $code   = $lang['code'];
            $url    = Olo_Lang_Language::get_language_url( $code );
            $active = $code === $current ? ' olsb-active' : '';

            echo '<a href="' . esc_url( $url ) . '" class="olsb-item' . $active . '" hreflang="' . esc_attr( $code ) . '" title="' . esc_attr( $lang['name'] ) . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $active is a fixed ' olsb-active'/'' literal from the ternary above; url/code/name escaped inline
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

        $uid = 'olsb-tab-' . substr( md5( wp_json_encode( $s ) ), 0, 6 );

        // Size multipliers
        $pad_map  = [ 'tiny' => '3px 5px', 'small' => '5px 8px', 'normal' => '7px 12px' ];
        $font_map = [ 'tiny' => '10px', 'small' => '12px', 'normal' => '14px' ];
        $flag_sz  = [ 'tiny' => 14, 'small' => 18, 'normal' => absint( $s['flag_size'] ) ];
        $pad      = $pad_map[ $t_size ] ?? $pad_map['normal'];
        $font     = $font_map[ $t_size ] ?? $font_map['normal'];
        $fsz      = $flag_sz[ $t_size ] ?? $flag_sz['normal'];
        // La bandiera SVG nelle linguette segue la dimensione del tab.
        $s['flag_size'] = $fsz;
        if ( 'flags_circle' === $s['style'] ) {
            $s['circle_size'] = ( 'tiny' === $t_size ) ? 22 : ( ( 'small' === $t_size ) ? 28 : max( 20, intval( $s['circle_size'] ) ) );
        }

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
            echo ".{$uid} .olsb-tab{border-radius:0 0 6px 6px;border-top:none;}";
        } elseif ( $edge === 'right' ) {
            echo ".{$uid}{top:{$offset}px;right:0;flex-direction:column;}";
            echo ".{$uid} .olsb-tab{border-radius:6px 0 0 6px;border-right:none;}";
        } else {
            echo ".{$uid}{top:{$offset}px;left:0;flex-direction:column;}";
            echo ".{$uid} .olsb-tab{border-radius:0 6px 6px 0;border-left:none;}";
        }

        echo ".{$uid} .olsb-tab{display:flex;align-items:center;justify-content:center;gap:3px;";
        echo "padding:{$pad};font-size:{$font};font-weight:600;text-decoration:none;";
        echo "background:{$bg_color};color:{$text_color};border:1px solid {$border_color};";
        echo "transition:all .2s;cursor:pointer;}";

        echo ".{$uid} .olsb-tab:hover{opacity:.8;}";
        echo ".{$uid} .olsb-tab.olsb-active{background:{$active_bg};color:{$active_color};border-color:{$active_bg};}";
        echo ".{$uid} .olsb-tab .olsb-flag{font-size:{$fsz}px;line-height:1;}";

        // Circle flags inside tabs
        if ( $s['style'] === 'flags_circle' ) {
            $csz = $t_size === 'tiny' ? 22 : ( $t_size === 'small' ? 28 : max( 24, intval( $s['circle_size'] ) ) );
            $c_bg  = $this->safe_color_css( $s['circle_bg'] ?? '' ) ?: 'rgba(255,255,255,0.2)';
            $c_brd = $this->safe_color_css( $s['circle_border'] ?? '' ) ?: 'rgba(255,255,255,0.3)';
            echo ".{$uid} .olsb-circle-flag{width:{$csz}px;height:{$csz}px;font-size:" . round( $csz * 0.55 ) . "px;";
            echo "border-radius:50%;border:2px solid {$c_brd};background:{$c_bg};display:inline-flex;align-items:center;justify-content:center;line-height:1;}";
        }

        echo '</style>';
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped

        echo '<div class="' . esc_attr( $uid ) . '">';
        foreach ( $languages as $lang ) {
            $code   = $lang['code'];
            $url    = Olo_Lang_Language::get_language_url( $code );
            $active = $code === $current ? ' olsb-active' : '';

            echo '<a href="' . esc_url( $url ) . '" class="olsb-tab' . $active . '" hreflang="' . esc_attr( $code ) . '" title="' . esc_attr( $lang['name'] ) . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $active is a fixed ' olsb-active'/'' literal from the ternary above; url/code/name escaped inline
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
        echo '<div class="olsb-dropdown">';

        echo '<button type="button" class="olsb-trigger" aria-haspopup="true" aria-expanded="false">';
        $this->render_item_content( $current_lang, $flags, $s );
        if ( ! empty( $s['show_dropdown_arrow'] ) ) {
            echo '<svg class="olsb-arrow" width="12" height="12" viewBox="0 0 12 12"><path d="M2 4l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>';
        }
        echo '</button>';

        echo '<div class="olsb-menu">';
        foreach ( $languages as $lang ) {
            if ( $lang['code'] === $current ) continue;
            $code = $lang['code'];
            $url  = Olo_Lang_Language::get_language_url( $code );

            echo '<a href="' . esc_url( $url ) . '" class="olsb-option" hreflang="' . esc_attr( $code ) . '">';
            $this->render_item_content( $lang, $flags, $s );
            echo '</a>';
        }
        echo '</div>';
        echo '</div>';
        echo '</div>';

        // Il menu viene PORTATO nel <body> all\'apertura (position:fixed sotto il
        // trigger): così esce da qualsiasi antenato con overflow:hidden / stacking
        // context (header, sezioni con sfondo) — niente menu tagliato o nascosto.
        echo '<script>
        (function(){
            function place(menu, trigger){
                var r = trigger.getBoundingClientRect();
                menu.style.position = "fixed";
                menu.style.top = (r.bottom + 6) + "px";
                menu.style.left = r.left + "px";
                menu.style.minWidth = Math.max(r.width, 160) + "px";
            }
            document.querySelectorAll(".olsb-dropdown").forEach(function(el){
                if(el._olsInit) return; el._olsInit = true;
                var trigger = el.querySelector(".olsb-trigger");
                var menu = el.querySelector(".olsb-menu");
                if(!trigger || !menu) return;
                function open(){
                    document.body.appendChild(menu);
                    place(menu, trigger);
                    el.classList.add("olsb-open"); menu.classList.add("olsb-open");
                    trigger.setAttribute("aria-expanded", "true");
                }
                function close(){
                    el.classList.remove("olsb-open"); menu.classList.remove("olsb-open");
                    trigger.setAttribute("aria-expanded", "false");
                    if(menu.parentNode === document.body){
                        el.appendChild(menu);
                        menu.style.position = ""; menu.style.top = ""; menu.style.left = ""; menu.style.minWidth = "";
                    }
                }
                trigger.addEventListener("click", function(e){
                    e.stopPropagation();
                    el.classList.contains("olsb-open") ? close() : open();
                });
                document.addEventListener("click", function(e){ if(!menu.contains(e.target) && !trigger.contains(e.target)) close(); });
                document.addEventListener("keydown", function(e){ if((e.key === "Escape" || e.key === "Esc") && el.classList.contains("olsb-open")){ close(); trigger.focus(); } });
                window.addEventListener("scroll", function(){ if(el.classList.contains("olsb-open")) place(menu, trigger); }, true);
                window.addEventListener("resize", function(){ if(el.classList.contains("olsb-open")) place(menu, trigger); });
            });
        })();
        </script>';
    }
}
