<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_OverlaySlider_Tile extends Olobuild_Tile_Base {

    protected $type     = 'overlayslider';
    protected $name     = 'Overlay Slider';
    protected $icon     = 'dashicons-format-gallery';
    protected $category = 'media';
    // Stessi testi di partenza del config (overlayslider.js): neutri, senza foto.
    protected $defaults = [
        'slides' => [
            [ 'id' => 'os-1', 'image' => '', 'title' => 'Benvenuti', 'subtitle' => 'Una breve presentazione di chi siamo', 'link' => '' ],
            [ 'id' => 'os-2', 'image' => '', 'title' => 'I nostri servizi', 'subtitle' => 'Soluzioni pensate per ogni esigenza', 'link' => '' ],
            [ 'id' => 'os-3', 'image' => '', 'title' => 'Parliamone', 'subtitle' => 'Scrivici per un primo incontro senza impegno', 'link' => '' ],
        ],
        'columns'             => '1',
        'gap'                 => 'default',
        'image_ratio'         => '21/9',
        'image_height'        => '400',
        'image_fit'           => 'cover',
        'object_position'     => 'center center',
        'height'              => '400',
        'overlay_position'    => 'bottom',
        'overlay_horizontal'  => 'left',
        'overlay_style'       => 'overlay-primary',
        'overlay_padding'     => 'medium',
        'title_size'          => 'h2',
        'hover_effect'        => 'zoom',
        'hover_overlay'       => 'always',
        'show_arrows'         => true,
        'show_dots'           => true,
        'ribbon_position'     => 'top-right',
        // Vuoti = colori della palette (primario + il suo contrasto), come il config.
        'ribbon_bg'           => '',
        'ribbon_color'        => '',
        'shadow'              => 'lg',

        'preset'              => 'cinematic-overlay',
        'slide_radius'        => 0,
        'overlay_color'       => 'rgba(0,0,0,0.45)',
        'overlay_gradient'    => true,
        'title_color'         => 'var(--olo-color-light, #ffffff)',
        'title_weight'        => '700',
        'title_letter_spacing'=> 0,
        'title_uppercase'     => false,
        'subtitle_color'      => 'rgba(255,255,255,0.85)',
        'subtitle_size'       => 16,
        'show_cta'            => false,
        'cta_text'            => 'Scopri di più',
        'cta_style'           => 'underline',

        'effect_color'        => '',
        'effect_intensity'    => 'medium',
        'effect_speed'        => 0,
        'wow_disable'           => false,
        'wow_backdrop_blur'     => 0,
        'wow_backdrop_saturate' => 100,
        'wow_border_style'      => 'solid',
        'wow_font_family'       => 'inherit',
        'wow_rotation'          => 0,
        'wow_perspective'       => 0,
        'wow_tilt_x'            => 0,
        'wow_glow_pulse'        => false,
        'wow_title_glow'        => false,
        'wow_scanlines'         => false,


        'wow_terminal_prompt' => false,

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

    /**
     * V3.26.0 — Extra CSS for "audacious" presets.
     */
    private function get_preset_extra_css( $preset_id, $sel, $s = [] ) {
        // @deprecated v1.0.73 — refactor profondo: i preset audaci ora settano direttamente
        // i field standard tramite TILE_PRESETS in BuilderInspector.vue, e i field wow_* via
        // build_wow_effects_css(). Nessun !important, ogni proprieta personalizzabile.
        return '';
    }

    /**
     * Helper: convert hex/rgb to "rgba(r,g,b,alpha)" string.
     */
    private function color_to_rgba( $color, $alpha ) {
        $rgb = $this->color_to_rgb( $color );
        return "rgba({$rgb},{$alpha})";
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $slides = is_array( $s['slides'] ) ? $s['slides'] : [];
        if ( empty( $slides ) ) {
            // L'avviso serve solo a chi costruisce la pagina: ai visitatori niente.
            if ( empty( $s['_builder_mode'] ) ) {
                return '';
            }
            return '<div class="olo-overlayslider" style="padding:2.5em;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);">' . esc_html( olobuild_t( 'Nessuna slide aggiunta' ) ) . '</div>';
        }

        $columns  = min( 6, absint( $s['columns'] ) ?: 1 );
        // Colonne per dispositivo (il campo ha il selettore tablet/telefono, che prima
        // salvava valori mai letti: usciva solo la classe desktop @m). Valori validi o
        // '' (= eredita dal dispositivo più largo, come nel canvas).
        $cols_dev = [];
        foreach ( [ 'tablet_landscape', 'tablet', 'mobile_landscape', 'mobile' ] as $dev ) {
            $v = absint( $s[ 'columns_' . $dev ] ?? 0 );
            $cols_dev[ 'columns_' . $dev ] = $v > 0 ? (string) min( 6, $v ) : '';
        }
        $has_cols_dev = (bool) array_filter( $cols_dev );
        $gap      = in_array( $s['gap'] ?? 'default', [ 'collapse', 'small', 'default', 'medium', 'large' ], true ) ? ( $s['gap'] ?? 'default' ) : 'default';
        $img_ratio  = $s['image_ratio'] ?? 'auto';
        $img_fit    = in_array( $s['image_fit'] ?? 'cover', [ 'cover', 'contain', 'fill' ], true ) ? ( $s['image_fit'] ?? 'cover' ) : 'cover';
        $obj_pos    = trim( (string) ( $s['object_position'] ?? 'center center' ) );
        if ( $obj_pos === '' ) { $obj_pos = 'center center'; }
        $height   = absint( $s['image_height'] ?? $s['height'] ?? 400 ) ?: 400;
        $position = esc_attr( $s['overlay_position'] ?: 'bottom' );
        // «Sotto l'immagine»: la didascalia esce dalla foto e segue nel flusso, senza
        // velo (preset «Minimal Caption»). Tutte le altre posizioni restano sovrapposte.
        $below    = ( $position === 'below' );
        $style    = in_array( $s['overlay_style'], [ 'overlay-primary', 'overlay-default' ], true ) ? $s['overlay_style'] : 'overlay-primary';
        $count    = count( $slides );

        $preset_id = $s['preset'] ?? 'cinematic-overlay';

        // Slide radius + custom overlay color/gradient
        // slide_radius dual-format: numero legacy O oggetto {tl,tr,br,bl} (type border-radius).
        $slide_radius_css = $this->build_border_radius_css( $s['slide_radius'] ?? 0 );
        $overlay_clr  = $this->safe_color_css( $s['overlay_color'] ?? 'rgba(0,0,0,0.45)' );
        $overlay_grad = ! empty( $s['overlay_gradient'] );

        $title_clr    = $this->safe_color_css( $s['title_color'] ?? '#ffffff' ) ?: '#ffffff';
        $title_w      = preg_match( '/^[1-9]00$/', (string) ($s['title_weight'] ?? '700') ) ? $s['title_weight'] : '700';
        $title_ls     = floatval( $s['title_letter_spacing'] ?? 0 );
        $title_upper  = ! empty( $s['title_uppercase'] );
        $subtitle_clr = $this->safe_color_css( $s['subtitle_color'] ?? 'rgba(255,255,255,0.85)' ) ?: 'rgba(255,255,255,0.85)';
        $subtitle_sz  = max( 10, intval( $s['subtitle_size'] ?? 16 ) );

        $show_cta  = ! empty( $s['show_cta'] );
        $cta_text  = $s['cta_text'] ?? '';
        $cta_style = $s['cta_style'] ?? 'underline';

        // Text alignment
        $text_align = $s['overlay_horizontal'] ?? 'left';
        $text_class = $text_align !== 'left' ? ' uk-text-' . esc_attr( $text_align ) : '';

        // Padding
        $pad = $s['overlay_padding'] ?? 'medium';
        $pad_class = '';
        if ( $pad === 'small' )  $pad_class = ' uk-padding-small';
        if ( $pad === 'large' )  $pad_class = ' uk-padding';

        // Title tag
        $title_tag = in_array( $s['title_size'] ?? 'h3', [ 'h1', 'h2', 'h3', 'h4' ], true ) ? ( $s['title_size'] ?? 'h3' ) : 'h3';

        // Hover effects
        $hover_effect  = $s['hover_effect'] ?? 'none';
        $hover_overlay = $s['hover_overlay'] ?? 'always';

        $img_class = 'mos-os-img';
        if ( $hover_effect !== 'none' ) {
            $img_class .= ' mos-os-hover-' . esc_attr( $hover_effect );
        }

        $overlay_class = '';
        $needs_toggle  = false;
        if ( $hover_overlay !== 'always' ) {
            $needs_toggle  = true;
            $overlay_class = ' uk-transition-' . esc_attr( $hover_overlay );
        }

        // Ribbon
        $ribbon_position = esc_attr( $s['ribbon_position'] ?? 'top-right' );
        // Vuoto (il default del config) = colori della palette. Prima usciva
        // `background: ;` e il nastrino delle tile nuove restava senza fondo.
        $ribbon_bg       = $this->safe_color_css( $s['ribbon_bg'] ?? '' ) ?: 'var(--olo-color-primary, #e1474f)';
        $ribbon_color    = $this->safe_color_css( $s['ribbon_color'] ?? '' ) ?: 'var(--olo-color-primary-contrast, #ffffff)';

        $uid = 'mos-os-' . wp_rand( 10000, 99999 );

        // ── Colonne per dispositivo ──
        // Sopra i 960px comanda la classe UIkit @m (desktop). Sotto, senza alcun valore
        // per dispositivo, una slide per volta: è la resa di sempre (la slide prendeva la
        // larghezza della foto, cioè tutto lo schermo) e non lascia più collassare le
        // slide senza immagine. Con almeno un valore vale la cascata del canvas: i
        // dispositivi non impostati ereditano dal più largo, fino al desktop.
        $items_sel = '.' . $uid . ' .uk-slider-items>*';
        $nav_sel   = '.' . $uid . ' .olo-os-nav';
        $base_mob  = $has_cols_dev ? $columns : 1;
        $cols_css  = '@media (max-width:959px){' . $items_sel . '{width:calc(100% / ' . (int) $base_mob . ');}}';
        $cols_css .= $this->css_per_dispositivo( $cols_dev, 'columns', $items_sel, static function ( $v ) {
            return 'width:calc(100% / ' . max( 1, min( 6, absint( $v ) ) ) . ');';
        } );
        // Frecce e indicatori: nel markup se su almeno un dispositivo le slide non stanno
        // tutte in vista; nascosti (visibility, che li toglie anche dal tab) dove ci stanno.
        $min_cols = $base_mob;
        foreach ( $cols_dev as $v ) {
            if ( $v !== '' ) {
                $min_cols = min( $min_cols, (int) $v );
            }
        }
        $min_cols  = min( $min_cols, $columns );
        $nav_any   = $count > $min_cols;
        $nav_css   = '';
        if ( $nav_any ) {
            $vis       = function ( $n ) use ( $count ) {
                return $count > (int) $n ? 'visibility:visible;' : 'visibility:hidden;';
            };
            $nav_css  .= $nav_sel . '{' . $vis( $columns ) . '}';
            $nav_css  .= '@media (max-width:959px){' . $nav_sel . '{' . $vis( $base_mob ) . '}}';
            $nav_css  .= $this->css_per_dispositivo( $cols_dev, 'columns', $nav_sel, $vis );
        }

        $wrap_class = 'olo-overlayslider olo-os--preset-' . esc_attr( $preset_id ) . ' ' . $uid;

        // Shadow
        $shadow_v = $s['shadow'] ?? 'none';
        $shadow_map = [
            'sm' => '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
            'md' => '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
            'lg' => '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
            'xl' => '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
        ];
        $shadow_css = isset( $shadow_map[ $shadow_v ] ) ? 'box-shadow:' . $shadow_map[ $shadow_v ] . ';' : '';

        // Build slide-frame sizing CSS based on ratio/height/fit
        $frame_css = '';
        if ( $img_ratio && $img_ratio !== 'auto' ) {
            $frame_css = 'aspect-ratio:' . esc_attr( $img_ratio ) . ';';
        } else {
            $frame_css = 'height:' . $height . 'px;';
        }
        $img_size_css = 'width:100%;height:100%;object-fit:' . esc_attr( $img_fit ) . ';object-position:' . esc_attr( $obj_pos ) . ';display:block;';

        // CTA classes per style
        $cta_class_map = [
            'underline' => 'olo-os-cta olo-os-cta--underline',
            'arrow'     => 'olo-os-cta olo-os-cta--arrow',
            'pill'      => 'olo-os-cta olo-os-cta--pill',
            'text'      => 'olo-os-cta olo-os-cta--text',
        ];
        $cta_class = $cta_class_map[ $cta_style ] ?? $cta_class_map['underline'];

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist colours, absint()/intval()/floatval() sizes, preg_match()/in_array() whitelisted enums, a fixed shadow map, esc_attr()'d ratio/fit tokens, the internally generated $uid and the build_wow_effects_css() base-class helper (clamped ints + whitelists). ?>
        <style>
            .<?php echo $uid; ?> .mos-os-frame {
                position: relative;
                width: 100%;
                <?php echo $frame_css; ?>
                overflow: hidden;
                <?php if ( $slide_radius_css ) : ?>border-radius: <?php echo $slide_radius_css; ?>;<?php endif; ?>
                <?php echo $shadow_css; ?>
            }
            .<?php echo $uid; ?> .mos-os-img { transition: transform 0.5s ease, filter 0.5s ease; <?php echo $img_size_css; ?> }
            .<?php echo $uid; ?> .olo-hover-wrap { width: 100%; height: 100%; }
            .<?php echo $uid; ?> .olo-hover-wrap img,
            .<?php echo $uid; ?> .olo-hover-wrap video { <?php echo $img_size_css; ?> }
            .<?php echo $uid; ?> .mos-os-hover-zoom:hover,
            .<?php echo $uid; ?> .uk-transition-toggle:hover .mos-os-hover-zoom { transform: scale(1.08); }
            .<?php echo $uid; ?> .mos-os-hover-zoom-rotate:hover,
            .<?php echo $uid; ?> .uk-transition-toggle:hover .mos-os-hover-zoom-rotate { transform: scale(1.08) rotate(2deg); }
            .<?php echo $uid; ?> .mos-os-hover-brightness { filter: brightness(0.7); }
            .<?php echo $uid; ?> .mos-os-hover-brightness:hover,
            .<?php echo $uid; ?> .uk-transition-toggle:hover .mos-os-hover-brightness { filter: brightness(1); }
            .<?php echo $uid; ?> .mos-os-hover-desaturate { filter: grayscale(100%); }
            .<?php echo $uid; ?> .mos-os-hover-desaturate:hover,
            .<?php echo $uid; ?> .uk-transition-toggle:hover .mos-os-hover-desaturate { filter: grayscale(0%); }
            .<?php echo $uid; ?> .mos-os-hover-blur-in { filter: blur(3px); }
            .<?php echo $uid; ?> .mos-os-hover-blur-in:hover,
            .<?php echo $uid; ?> .uk-transition-toggle:hover .mos-os-hover-blur-in { filter: blur(0); }

            /* Custom overlay color (works for all preset overlays) */
            .<?php echo $uid; ?> .uk-overlay-primary,
            .<?php echo $uid; ?> .uk-overlay-default {
                <?php if ( $overlay_grad ) : ?>
                background: linear-gradient(180deg, rgba(0,0,0,0) 0%, <?php echo $overlay_clr; ?> 100%);
                <?php else : ?>
                background: <?php echo $overlay_clr; ?>;
                <?php endif; ?>
                color: <?php echo $title_clr; ?>;
            }
            .<?php echo $uid; ?> .uk-overlay h1,
            .<?php echo $uid; ?> .uk-overlay h2,
            .<?php echo $uid; ?> .uk-overlay h3,
            .<?php echo $uid; ?> .uk-overlay h4,
            .<?php echo $uid; ?> .olo-os-caption h1,
            .<?php echo $uid; ?> .olo-os-caption h2,
            .<?php echo $uid; ?> .olo-os-caption h3,
            .<?php echo $uid; ?> .olo-os-caption h4 {
                color: <?php echo $title_clr; ?>;
                font-weight: <?php echo $title_w; ?>;
                letter-spacing: <?php echo (float) $title_ls; ?>em;
                <?php if ( $title_upper ) : ?>text-transform: uppercase;<?php endif; ?>
                margin: 0;
            }
            .<?php echo $uid; ?> .uk-overlay p,
            .<?php echo $uid; ?> .olo-os-caption p {
                color: <?php echo $subtitle_clr; ?>;
                font-size: <?php echo (int) $subtitle_sz; ?>px;
                margin: 6px 0 0;
            }
            /* Didascalia sotto l'immagine: nel flusso, senza velo, sul fondo della pagina */
            .<?php echo $uid; ?> .olo-os-caption--below { position: relative; background: none; color: <?php echo $title_clr; ?>; padding: .9em 0 0; }
            .<?php echo $uid; ?> .olo-os-caption--below.olo-os-caption--pad-small { padding-top: .6em; }
            .<?php echo $uid; ?> .olo-os-caption--below.olo-os-caption--pad-large { padding-top: 1.4em; }
            /* Slide con link: il fuoco da tastiera si vede */
            .<?php echo $uid; ?> .uk-slider-items a.uk-display-block:focus-visible { outline: 2px solid var(--olo-color-primary, #e1474f); outline-offset: -2px; }
            <?php echo $cols_css . $nav_css; ?>

            /* CTA */
            .<?php echo $uid; ?> .olo-os-cta {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-size: 13px;
                font-weight: 600;
                margin-top: 12px;
                color: <?php echo $title_clr; ?>;
                width: fit-content;
                transition: all 0.25s ease;
            }
            .<?php echo $uid; ?> .olo-os-cta--underline { border-bottom: 1.5px solid currentColor; padding-bottom: 2px; }
            .<?php echo $uid; ?> .olo-os-cta--arrow .olo-os-cta__arrow { transition: transform 0.25s ease; }
            .<?php echo $uid; ?> li:hover .olo-os-cta--arrow .olo-os-cta__arrow { transform: translateX(4px); }
            .<?php echo $uid; ?> .olo-os-cta--pill { background: var(--olo-color-primary, #e1474f); color: var(--olo-color-primary-contrast, #fff); border-radius: 999px; padding: 8px 18px; }

            /* Ribbon */
            .<?php echo $uid; ?> .mos-os-ribbon { position: absolute; z-index: 2; font-size: 11px; font-weight: 700; padding: 4px 12px; text-transform: uppercase; letter-spacing: 0.5px; background: <?php echo $ribbon_bg; ?>; color: <?php echo $ribbon_color; ?>; }
            .<?php echo $uid; ?> .mos-os-ribbon--top-right { top: 0; right: 14px; border-radius: 0 0 4px 4px; }
            .<?php echo $uid; ?> .mos-os-ribbon--top-left { top: 0; left: 14px; border-radius: 0 0 4px 4px; }

            <?php
            // V3.26.0 — preset-specific extras (audacious presets)
            echo $this->get_preset_extra_css( $preset_id, '.' . $uid, $s );
            // Il selettore del titolo deve esistere nel markup (classe olo-os-title sul
            // titolo di ogni slide): con '.olo-overlay-title' bagliore e prompt non comparivano.
            echo $this->build_wow_effects_css( $s, '.' . $uid, '.olo-os-title' );
            ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="<?php echo esc_attr( $wrap_class ); ?>" uk-slider>
            <div class="uk-position-relative">
                <div class="uk-slider-container">
                    <?php $gap_class = $gap === 'collapse' ? 'uk-grid-collapse' : 'uk-grid-' . $gap; ?>
                    <ul class="uk-slider-items uk-child-width-1-<?php echo (int) $columns; ?>@m uk-grid <?php echo esc_attr( $gap_class ); ?>">
                        <?php foreach ( $slides as $slide ) :
                            $has_link   = ! empty( $slide['link'] );
                            $link_url   = $has_link ? esc_url( $slide['link'] ) : '';
                            $toggle_cls = $needs_toggle ? ' uk-transition-toggle' : '';
                        ?>
                            <li>
                                <?php if ( $has_link ) : ?>
                                <a href="<?php echo $link_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped via esc_url() at assignment above; toggle class is a fixed internal literal ?>" class="uk-link-reset uk-display-block<?php echo $toggle_cls; ?>" style="overflow:hidden;position:relative;">
                                <?php else : ?>
                                <?php // Senza link la slide non è un comando: niente tabindex. ?>
                                <div class="uk-panel<?php echo esc_attr( $toggle_cls ); ?>" style="overflow:hidden;">
                                <?php endif; ?>
                                    <div class="mos-os-frame">
                                    <?php if ( ! empty( $slide['image'] ) ) : ?>
                                        <?php
                                        $os_img = '<img src="' . esc_url( $slide['image'] ) . '" alt="' . esc_attr( $slide['title'] ?? '' ) . '" class="' . $img_class . '" loading="lazy">';
                                        echo $this->render_hover_wrap( $os_img, $slide['hover_image'] ?? '', $slide['hover_video'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- <img> markup built above with esc_url()/esc_attr(); hover wrapper generated by Olobuild_Tile_Base::render_hover_wrap() with esc_url()'d media
                                        ?>
                                    <?php else : ?>
                                        <div style="width:100%;height:100%;background:var(--olo-color-dark, #1F2937);"></div>
                                    <?php endif; ?>
                                    </div>
                                    <?php if ( ! empty( $slide['ribbon'] ) ) : ?>
                                        <span class="mos-os-ribbon mos-os-ribbon--<?php echo $ribbon_position; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped via esc_attr() at assignment above ?>"><?php echo esc_html( $slide['ribbon'] ); ?></span>
                                    <?php endif; ?>
                                    <?php
                                    list( $ost_cls, $ost_data ) = $this->tfx_attrs( $s, 'title', $slide['title'] ?? '' );
                                    list( $oss_cls, $oss_data ) = $this->tfx_attrs( $s, 'subtitle', $slide['subtitle'] ?? '' );
                                    ?>
                                    <?php if ( $below ) : ?>
                                    <div class="olo-os-caption olo-os-caption--below olo-os-caption--pad-<?php echo esc_attr( in_array( $pad, [ 'small', 'large' ], true ) ? $pad : 'medium' ); ?> uk-panel<?php echo $text_class . $overlay_class; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- text/overlay class fragments escaped via esc_attr() at assignment above ?>">
                                    <?php else : ?>
                                    <div class="uk-<?php echo esc_attr( $style ); ?> uk-position-<?php echo $position; ?> uk-panel<?php echo $text_class . $pad_class . $overlay_class; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- position/text/overlay class fragments escaped via esc_attr() at assignment above; padding class is a fixed internal literal ?>">
                                    <?php endif; ?>
                                        <?php $widget_html = $this->render_widget_template( $slide['widget_template_id'] ?? 0 ); ?>
                                        <?php if ( $widget_html ) : ?>
                                            <div class="olo-item-widget"><?php echo $widget_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- widget template HTML rendered by Olobuild_Tile_Base::render_widget_template(); each tile escapes its own output at build time ?></div>
                                        <?php endif; ?>
                                        <<?php echo $title_tag; ?> class="olo-os-title uk-margin-remove<?php echo $ost_cls; ?>"<?php echo $ost_data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- title tag whitelisted via in_array() above; tfx class/data attrs built by Olobuild_Text_Effects with sanitize_html_class()/esc_attr()'d values ?>><?php echo esc_html( $slide['title'] ?? '' ); ?></<?php echo $title_tag; ?>>
                                        <?php if ( ! empty( $slide['subtitle'] ) ) : ?>
                                            <p class="uk-margin-small-top<?php echo $oss_cls; ?>"<?php echo $oss_data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx class/data attrs built by Olobuild_Text_Effects with sanitize_html_class()/esc_attr()'d values ?>><?php echo esc_html( $slide['subtitle'] ); ?></p>
                                        <?php endif; ?>
                                        <?php if ( $show_cta && ! empty( $cta_text ) ) : ?>
                                            <span class="<?php echo esc_attr( $cta_class ); ?>"><?php echo esc_html( $cta_text ); ?><?php if ( $cta_style === 'arrow' ) : ?> <span class="olo-os-cta__arrow" aria-hidden="true">&rarr;</span><?php endif; ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php echo $has_link ? '</a>' : '</div>'; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <?php // $nav_any: su almeno un dispositivo le slide non stanno tutte in vista ($nav_css nasconde frecce e indicatori dove ci stanno). ?>
                <?php if ( ! empty( $s['show_arrows'] ) && $nav_any ) : ?>
                    <a class="olo-os-nav uk-position-center-left-out" href uk-slidenav-previous uk-slider-item="previous" role="button" aria-label="<?php esc_attr_e( 'Slide precedente', 'olobuild' ); ?>"></a>
                    <a class="olo-os-nav uk-position-center-right-out" href uk-slidenav-next uk-slider-item="next" role="button" aria-label="<?php esc_attr_e( 'Slide successiva', 'olobuild' ); ?>"></a>
                <?php endif; ?>

                <?php if ( ! empty( $s['show_dots'] ) && $nav_any ) : ?>
                    <ul class="olo-os-nav uk-slider-nav uk-dotnav uk-flex-center uk-margin" role="tablist" aria-label="<?php esc_attr_e( 'Navigazione slide', 'olobuild' ); ?>"></ul>
                <?php endif; ?>
            </div>
        </div>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- scoped CSS generated by the Olobuild_Text_Effects::css() helper (sanitized colours, intval()'d timings, fixed keyframes)
        $this->tfx_print_script();

        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() (integer-forced widths) for the internally generated uid
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() shared helpers
        }
        return ob_get_clean();
    }
}
