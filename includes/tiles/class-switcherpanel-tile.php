<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_SwitcherPanel_Tile extends Olo_Tile_Base {

    protected $type     = 'switcherpanel';
    protected $name     = 'Switcher Panel';
    protected $icon     = 'dashicons-images-alt';
    protected $category = 'interactive';
    protected $defaults = [
        'items' => [
            [ 'id' => 'sp-1', 'nav_label' => 'Chi siamo',       'title' => 'Benvenuti nel nostro mondo', 'text' => 'Lorem ipsum dolor sit amet.', 'button_text' => 'SCOPRI DI PIÙ',     'button_url' => '#', 'image' => '' ],
            [ 'id' => 'sp-2', 'nav_label' => 'Bar & Cocktail',  'title' => "Mixology d'autore",          'text' => 'Lorem ipsum dolor sit amet.', 'button_text' => 'VEDI LA CARTA',     'button_url' => '#', 'image' => '' ],
            [ 'id' => 'sp-3', 'nav_label' => 'Ristorante',      'title' => 'Cucina contemporanea',       'text' => 'Lorem ipsum dolor sit amet.', 'button_text' => 'PRENOTA UN TAVOLO', 'button_url' => '#', 'image' => '' ],
        ],
        'hero_image'      => '',
        'hero_height'     => 400,
        'hero_radius'     => 0,
        'hero_overlay_color'    => 'rgba(0,0,0,0.35)',
        'hero_overlay_gradient' => true,
        'image_position'  => 'right',
        'animation'       => 'fade',
        'animation_duration' => 300,
        'tile_padding'    => [ 'top' => 40, 'right' => 40, 'bottom' => 40, 'left' => 40 ],
        'title_tag'       => 'h3',
        'button_style'    => 'primary',

        'preset'          => 'editorial-overlay',

        'nav_position'    => 'overlay',
        'layout_mode'     => 'split',
        'panel_gap'       => 24,
        'panel_image_width' => 40,
        'panel_image_ratio' => 'auto',

        'nav_padding_y'      => 12,
        'nav_padding_x'      => 18,
        'nav_font_size'      => 12,
        'nav_font_weight'    => '700',
        'nav_letter_spacing' => 0.08,
        'nav_uppercase'      => true,
        'nav_gap'            => 0,
        'nav_radius'         => 0,

        'nav_container_bg'      => 'transparent',
        'nav_container_padding' => 0,
        'nav_container_radius'  => 0,

        'nav_active_bg'         => 'transparent',
        'nav_active_color'      => '#ffffff',
        'nav_inactive_color'    => 'rgba(255,255,255,0.65)',
        'nav_hover_bg'          => 'transparent',
        'nav_indicator_type'    => 'underline',
        'nav_indicator_color'   => '#ffffff',
        'nav_indicator_thickness' => 2,

        'panel_bg'              => '#ffffff',
        'panel_text_color'      => '#1e293b',
        'panel_title_color'     => '#0f172a',
        'panel_title_size'      => 28,
        'panel_title_weight'    => '700',
        'panel_text_size'       => 15,
        'panel_radius'          => 0,
        'panel_image_radius'    => 0,

        'shadow'                => 'none',

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
     * V3.27.0 — Extra CSS for "audacious" presets, parametric on
     * effect_color / effect_intensity / effect_speed.
     */
    private function get_preset_extra_css( $preset_id, $uid, $s = [] ) {
        // @deprecated v1.0.73 — refactor profondo: i preset audaci ora settano direttamente
        // i field standard tramite TILE_PRESETS in BuilderInspector.vue, e i field wow_* via
        // build_wow_effects_css(). Nessun !important, ogni proprietà personalizzabile.
        return '';
    }

    /**
     * Build CSS aspect-ratio from the panel_image_ratio setting.
     */
    private function build_image_ratio_css( $ratio ) {
        switch ( $ratio ) {
            case '16:9': return 'aspect-ratio: 16 / 9; object-fit: cover;';
            case '4:3':  return 'aspect-ratio: 4 / 3; object-fit: cover;';
            case '1:1':  return 'aspect-ratio: 1 / 1; object-fit: cover;';
            case '3:4':  return 'aspect-ratio: 3 / 4; object-fit: cover;';
            default:     return ''; // auto
        }
    }

    public function render( $settings ) {
        $s     = wp_parse_args( $settings, $this->defaults );
        $items = $this->parse_items( $s['items'] );
        $count = count( $items );

        if ( $count === 0 ) {
            return '';
        }

        $uid          = 'olo-sp-' . wp_rand( 10000, 99999 );
        $preset_id    = $s['preset'] ?? 'editorial-overlay';
        $hero_image   = $s['hero_image'] ?? '';
        $hero_height  = max( 100, intval( $s['hero_height'] ?? 400 ) );
        // Dual-format: Number legacy E oggetto {tl,tr,br,bl} (build_border_radius_css ritorna '' se zero/vuoto).
        $hero_rad_css = $this->build_border_radius_css( $s['hero_radius'] ?? 0 );
        $img_position = ( $s['image_position'] ?? 'right' ) === 'left' ? 'left' : 'right';
        $nav_position = $s['nav_position'] ?? 'overlay';
        $animation    = $s['animation'] ?? 'fade';
        $duration     = max( 80, intval( $s['animation_duration'] ?? 300 ) );
        $padding      = Olo_Tile_Utils::spacing_css( $s['tile_padding'] ?? 40, 40 );
        $title_tag    = in_array( $s['title_tag'], [ 'h2', 'h3', 'h4' ], true ) ? $s['title_tag'] : 'h3';
        $btn_style    = $s['button_style'] ?? 'primary';

        // ── Numeric fields ──
        $nav_pad_y    = max( 0, intval( $s['nav_padding_y'] ?? 12 ) );
        $nav_pad_x    = max( 0, intval( $s['nav_padding_x'] ?? 18 ) );
        $nav_fs       = max( 8, intval( $s['nav_font_size'] ?? 12 ) );
        $nav_fw       = preg_match( '/^[1-9]00$/', (string) ( $s['nav_font_weight'] ?? '700' ) ) ? $s['nav_font_weight'] : '700';
        $nav_ls       = floatval( $s['nav_letter_spacing'] ?? 0.08 );
        $nav_uppercase = ! empty( $s['nav_uppercase'] );
        $nav_gap      = max( 0, intval( $s['nav_gap'] ?? 0 ) );
        $nav_rad_css  = $this->build_border_radius_css( $s['nav_radius'] ?? 0 ) ?: '0px';
        $nav_cont_pad = max( 0, intval( $s['nav_container_padding'] ?? 0 ) );
        $nav_cont_rad_css = $this->build_border_radius_css( $s['nav_container_radius'] ?? 0 );
        $ind_thickness = max( 1, intval( $s['nav_indicator_thickness'] ?? 2 ) );
        $panel_gap    = max( 0, intval( $s['panel_gap'] ?? 24 ) );
        $panel_rad_css = $this->build_border_radius_css( $s['panel_radius'] ?? 0 );
        $panel_img_rad_css = $this->build_border_radius_css( $s['panel_image_radius'] ?? 0 );
        $panel_img_w  = max( 20, min( 70, intval( $s['panel_image_width'] ?? 40 ) ) );
        $panel_title_size = max( 12, intval( $s['panel_title_size'] ?? 28 ) );
        $panel_title_weight = preg_match( '/^[1-9]00$/', (string) ( $s['panel_title_weight'] ?? '700' ) ) ? $s['panel_title_weight'] : '700';
        $panel_text_size = max( 10, intval( $s['panel_text_size'] ?? 15 ) );
        $img_ratio_css = $this->build_image_ratio_css( $s['panel_image_ratio'] ?? 'auto' );

        // ── Color helpers ──
        $nav_cont_bg     = $this->safe_color_css( $s['nav_container_bg'] ?? 'transparent' );
        $nav_active_bg   = $this->safe_color_css( $s['nav_active_bg'] ?? 'transparent' );
        $nav_active_clr  = $this->safe_color_css( $s['nav_active_color'] ?? '#ffffff' ) ?: '#ffffff';
        $nav_inactive    = $this->safe_color_css( $s['nav_inactive_color'] ?? 'rgba(255,255,255,0.65)' ) ?: 'rgba(255,255,255,0.65)';
        $nav_hover_bg    = $this->safe_color_css( $s['nav_hover_bg'] ?? 'transparent' );
        $nav_ind_color   = $this->safe_color_css( $s['nav_indicator_color'] ?? '#ffffff' ) ?: '#ffffff';
        $hero_overlay    = $this->safe_color_css( $s['hero_overlay_color'] ?? 'rgba(0,0,0,0.35)' );
        $panel_bg        = $this->safe_color_css( $s['panel_bg'] ?? '#ffffff' ) ?: '#ffffff';
        $panel_text_clr  = $this->safe_color_css( $s['panel_text_color'] ?? '#1e293b' ) ?: '#1e293b';
        $panel_title_clr = $this->safe_color_css( $s['panel_title_color'] ?? '#0f172a' ) ?: '#0f172a';

        $ind_type        = $s['nav_indicator_type'] ?? 'underline';
        $hero_grad_on    = ! empty( $s['hero_overlay_gradient'] );

        $shadow_css = '';
        $shadow_v = $s['shadow'] ?? 'none';
        if ( $shadow_v === 'sm' ) {
            $shadow_css = 'box-shadow: 0 1px 2px rgba(16,24,40,0.06), 0 1px 3px rgba(16,24,40,0.08);';
        } elseif ( $shadow_v === 'md' ) {
            $shadow_css = 'box-shadow: 0 4px 6px rgba(16,24,40,0.08), 0 2px 4px rgba(16,24,40,0.06);';
        } elseif ( $shadow_v === 'lg' ) {
            $shadow_css = 'box-shadow: 0 12px 24px rgba(16,24,40,0.10), 0 4px 8px rgba(16,24,40,0.08);';
        } elseif ( $shadow_v === 'xl' ) {
            $shadow_css = 'box-shadow: 0 25px 50px rgba(16,24,40,0.15), 0 10px 20px rgba(16,24,40,0.10);';
        }

        // Wrapper layout class
        $wrap_class = 'olo-switcherpanel olo-sp--' . esc_attr( $nav_position );
        if ( $img_position === 'left' ) $wrap_class .= ' olo-sp--img-left';
        $wrap_class .= ' olo-sp--preset-' . esc_attr( $preset_id );

        // Button class
        $btn_class_map = [
            'default'   => 'olo-sp-panel__btn olo-sp-panel__btn--default',
            'primary'   => 'olo-sp-panel__btn olo-sp-panel__btn--primary',
            'secondary' => 'olo-sp-panel__btn olo-sp-panel__btn--secondary',
            'text'      => 'olo-sp-panel__btn olo-sp-panel__btn--text',
            'underline' => 'olo-sp-panel__btn olo-sp-panel__btn--underline',
            'pill'      => 'olo-sp-panel__btn olo-sp-panel__btn--pill',
        ];
        $btn_class = $btn_class_map[ $btn_style ] ?? $btn_class_map['primary'];

        $switcher_attr = 'connect: #' . $uid . '-content';
        if ( ! empty( $animation ) ) {
            $switcher_attr .= '; animation: uk-animation-' . esc_attr( $animation );
        }

        // Indicator-specific border resets
        $ind_underline = ( $ind_type === 'underline' );
        $ind_overline  = ( $ind_type === 'overline' );
        $ind_pill      = ( $ind_type === 'pill' );
        $ind_leftbar   = ( $ind_type === 'left-bar' );
        $is_vertical   = ( $nav_position === 'side-left' || $nav_position === 'side-right' );

        ob_start();
        ?>
        <style>
            /* ═══ SwitcherPanel V3.24.0 — preset: <?php echo esc_html( $preset_id ); ?> ═══ */

            /* Wrapper layout: nav position dictates flex direction */
            .<?php echo $uid; ?>.olo-switcherpanel {
                display: flex;
                flex-direction: column;
                gap: 0;
            }
            .<?php echo $uid; ?>.olo-sp--side-left { flex-direction: row; gap: 24px; align-items: flex-start; }
            .<?php echo $uid; ?>.olo-sp--side-right { flex-direction: row-reverse; gap: 24px; align-items: flex-start; }
            .<?php echo $uid; ?>.olo-sp--side-left .olo-sp-nav,
            .<?php echo $uid; ?>.olo-sp--side-right .olo-sp-nav {
                flex-direction: column;
                min-width: 200px;
                max-width: 260px;
                position: static;
            }
            .<?php echo $uid; ?>.olo-sp--side-left .olo-sp-hero,
            .<?php echo $uid; ?>.olo-sp--side-right .olo-sp-hero {
                flex: 1;
                min-width: 0;
            }
            .<?php echo $uid; ?>.olo-sp--bottom { flex-direction: column; }
            .<?php echo $uid; ?>.olo-sp--bottom .olo-sp-nav-wrap { order: 99; }

            /* Hero */
            .<?php echo $uid; ?> .olo-sp-hero {
                position: relative;
                overflow: hidden;
                height: <?php echo $hero_height; ?>px;
                <?php if ( $hero_rad_css ) : ?>border-radius: <?php echo $hero_rad_css; ?>;<?php endif; ?>
                background: #e5e7eb;
            }
            .<?php echo $uid; ?> .olo-sp-hero__img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }
            .<?php echo $uid; ?> .olo-sp-hero__placeholder {
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, #94a3b8, #64748b);
            }
            <?php if ( $nav_position === 'overlay' && $hero_overlay ) : ?>
            .<?php echo $uid; ?> .olo-sp-hero::after {
                content: '';
                position: absolute;
                inset: 0;
                <?php if ( $hero_grad_on ) : ?>
                background: linear-gradient(180deg, rgba(0,0,0,0) 0%, <?php echo $hero_overlay; ?> 100%);
                <?php else : ?>
                background: <?php echo $hero_overlay; ?>;
                <?php endif; ?>
                pointer-events: none;
            }
            <?php endif; ?>

            /* Nav */
            .<?php echo $uid; ?> .olo-sp-nav-wrap {
                <?php if ( $nav_position === 'overlay' ) : ?>
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                z-index: 2;
                padding: 0 16px;
                <?php endif; ?>
            }
            <?php if ( $nav_position === 'top' ) : ?>
            .<?php echo $uid; ?> .olo-sp-nav-wrap--top { margin-bottom: 16px; }
            <?php endif; ?>
            <?php if ( $nav_position === 'bottom' ) : ?>
            .<?php echo $uid; ?> .olo-sp-nav-wrap--bottom { margin-top: 16px; }
            <?php endif; ?>

            .<?php echo $uid; ?> .olo-sp-nav {
                margin: 0;
                padding: <?php echo $nav_cont_pad; ?>px;
                list-style: none;
                display: flex;
                gap: <?php echo $nav_gap; ?>px;
                <?php if ( $is_vertical ) : ?>flex-direction: column;<?php endif; ?>
                <?php if ( $nav_cont_bg ) : ?>background: <?php echo $nav_cont_bg; ?>;<?php endif; ?>
                <?php if ( $nav_cont_rad_css ) : ?>border-radius: <?php echo $nav_cont_rad_css; ?>;<?php endif; ?>
                <?php echo $shadow_css; ?>
            }
            .<?php echo $uid; ?> .olo-sp-nav > li { margin: 0; padding: 0; <?php if ( $ind_underline && ! $is_vertical ) : ?>margin-bottom: -<?php echo $ind_thickness; ?>px;<?php endif; ?> }

            .<?php echo $uid; ?> .olo-sp-nav__btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: <?php echo $nav_pad_y; ?>px <?php echo $nav_pad_x; ?>px;
                font-size: <?php echo $nav_fs; ?>px;
                font-weight: <?php echo $nav_fw; ?>;
                <?php if ( $nav_uppercase ) : ?>text-transform: uppercase;<?php endif; ?>
                letter-spacing: <?php echo $nav_ls; ?>em;
                color: <?php echo $nav_inactive; ?>;
                text-decoration: none;
                cursor: pointer;
                background: transparent;
                border: 0;
                border-radius: <?php echo $nav_rad_css; ?>;
                transition: all <?php echo $duration; ?>ms ease;
                white-space: nowrap;
                <?php if ( $is_vertical ) : ?>justify-content: flex-start; width: 100%;<?php endif; ?>
                <?php if ( $ind_underline && ! $is_vertical ) : ?>border-bottom: <?php echo $ind_thickness; ?>px solid transparent; border-radius: 0; padding-bottom: <?php echo max( 0, $nav_pad_y - $ind_thickness ); ?>px;<?php endif; ?>
                <?php if ( $ind_overline && ! $is_vertical ) : ?>border-top: <?php echo $ind_thickness; ?>px solid transparent; border-radius: 0; padding-top: <?php echo max( 0, $nav_pad_y - $ind_thickness ); ?>px;<?php endif; ?>
                <?php if ( $ind_leftbar && $is_vertical ) : ?>border-left: <?php echo $ind_thickness; ?>px solid transparent; padding-left: <?php echo max( 0, $nav_pad_x - $ind_thickness ); ?>px; border-radius: 0 6px 6px 0;<?php endif; ?>
            }
            <?php if ( $nav_hover_bg && $nav_hover_bg !== 'transparent' ) : ?>
            .<?php echo $uid; ?> .olo-sp-nav__btn:not(.is-active):hover {
                background: <?php echo $nav_hover_bg; ?>;
                color: <?php echo $nav_active_clr; ?>;
            }
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-sp-nav__btn:focus-visible {
                outline: none;
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 35%, transparent);
            }
            .<?php echo $uid; ?> .olo-sp-nav > li.uk-active > .olo-sp-nav__btn {
                color: <?php echo $nav_active_clr; ?>;
                <?php if ( $nav_active_bg && $nav_active_bg !== 'transparent' ) : ?>background: <?php echo $nav_active_bg; ?>;<?php endif; ?>
                <?php if ( $ind_pill ) : ?>box-shadow: 0 1px 2px rgba(16,24,40,0.06), 0 1px 3px rgba(16,24,40,0.05);<?php endif; ?>
                <?php if ( $ind_underline && ! $is_vertical ) : ?>border-bottom-color: <?php echo $nav_ind_color; ?>;<?php endif; ?>
                <?php if ( $ind_overline && ! $is_vertical ) : ?>border-top-color: <?php echo $nav_ind_color; ?>;<?php endif; ?>
                <?php if ( $ind_leftbar && $is_vertical ) : ?>border-left-color: <?php echo $nav_ind_color; ?>;<?php endif; ?>
            }

            /* Panels */
            .<?php echo $uid; ?> .olo-sp-panels {
                margin: 0;
                padding: 0;
                list-style: none;
            }
            .<?php echo $uid; ?> .olo-sp-panel {
                display: flex;
                gap: <?php echo $panel_gap; ?>px;
                padding: <?php echo $padding; ?>;
                background: <?php echo $panel_bg; ?>;
                color: <?php echo $panel_text_clr; ?>;
                <?php if ( $panel_rad_css ) : ?>border-radius: <?php echo $panel_rad_css; ?>;<?php endif; ?>
                align-items: stretch;
            }
            .<?php echo $uid; ?>.olo-sp--img-left .olo-sp-panel { flex-direction: row-reverse; }
            @media (max-width: 767px) {
                .<?php echo $uid; ?> .olo-sp-panel { flex-direction: column; }
                .<?php echo $uid; ?>.olo-sp--img-left .olo-sp-panel { flex-direction: column; }
            }
            .<?php echo $uid; ?> .olo-sp-panel__content {
                flex: 1;
                min-width: 0;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
            .<?php echo $uid; ?> .olo-sp-panel__title {
                font-size: <?php echo $panel_title_size; ?>px;
                font-weight: <?php echo $panel_title_weight; ?>;
                line-height: 1.2;
                color: <?php echo $panel_title_clr; ?>;
                margin: 0 0 12px;
            }
            .<?php echo $uid; ?> .olo-sp-panel__text {
                font-size: <?php echo $panel_text_size; ?>px;
                line-height: 1.65;
                margin: 0 0 20px;
                color: <?php echo $panel_text_clr; ?>;
            }
            .<?php echo $uid; ?> .olo-sp-panel__media {
                flex: 0 0 <?php echo $panel_img_w; ?>%;
                max-width: <?php echo $panel_img_w; ?>%;
                <?php if ( $panel_img_rad_css ) : ?>border-radius: <?php echo $panel_img_rad_css; ?>; overflow: hidden;<?php endif; ?>
            }
            @media (max-width: 767px) {
                .<?php echo $uid; ?> .olo-sp-panel__media { max-width: 100%; flex: 1 1 auto; }
            }
            .<?php echo $uid; ?> .olo-sp-panel__img {
                width: 100%;
                height: 100%;
                <?php echo $img_ratio_css; ?>
                display: block;
                <?php if ( $panel_img_rad_css ) : ?>border-radius: <?php echo $panel_img_rad_css; ?>;<?php endif; ?>
            }

            /* Buttons */
            .<?php echo $uid; ?> .olo-sp-panel__btn {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                font-size: 13px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                padding: 12px 24px;
                border: 1px solid currentColor;
                color: <?php echo $panel_title_clr; ?>;
                background: transparent;
                text-decoration: none;
                transition: all <?php echo $duration; ?>ms ease;
                width: fit-content;
                cursor: pointer;
            }
            .<?php echo $uid; ?> .olo-sp-panel__btn--default { background: #fff; border-color: #e5e7eb; color: #1e293b; }
            .<?php echo $uid; ?> .olo-sp-panel__btn--default:hover { background: #f8fafc; }
            .<?php echo $uid; ?> .olo-sp-panel__btn--primary { background: var(--olo-color-primary, #e1474f); border-color: var(--olo-color-primary, #e1474f); color: #fff; }
            .<?php echo $uid; ?> .olo-sp-panel__btn--primary:hover { background: transparent; color: var(--olo-color-primary, #e1474f); }
            .<?php echo $uid; ?> .olo-sp-panel__btn--secondary { background: #1e293b; border-color: #1e293b; color: #fff; }
            .<?php echo $uid; ?> .olo-sp-panel__btn--secondary:hover { background: transparent; color: #1e293b; }
            .<?php echo $uid; ?> .olo-sp-panel__btn--text { background: transparent; border: 0; color: var(--olo-color-primary, #e1474f); padding: 0; text-transform: none; letter-spacing: 0; font-weight: 600; }
            .<?php echo $uid; ?> .olo-sp-panel__btn--text:hover { text-decoration: underline; }
            .<?php echo $uid; ?> .olo-sp-panel__btn--underline { background: transparent; border: 0; border-bottom: 2px solid currentColor; padding: 4px 0; color: <?php echo $panel_title_clr; ?>; border-radius: 0; }
            .<?php echo $uid; ?> .olo-sp-panel__btn--underline:hover { transform: translateX(4px); }
            .<?php echo $uid; ?> .olo-sp-panel__btn--pill { border-radius: 999px; background: var(--olo-color-primary, #e1474f); border-color: var(--olo-color-primary, #e1474f); color: #fff; }
            .<?php echo $uid; ?> .olo-sp-panel__btn--pill:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(232,98,42,0.3); }

            <?php
            // v1.0.73 — refactor profondo: get_preset_extra_css svuotato, ora i preset audaci
            // settano i field standard tramite TILE_PRESETS.switcherpanel + helper wow_*.
            echo $this->build_wow_effects_css( $s, '.' . $uid, '.olo-switcherpanel-title' );
            ?>
        </style>

        <div class="<?php echo esc_attr( $wrap_class ); ?> <?php echo $uid; ?>">
            <?php if ( $nav_position === 'overlay' ) : ?>
                <div class="olo-sp-hero">
                    <?php if ( ! empty( $hero_image ) ) : ?>
                        <img src="<?php echo esc_url( $hero_image ); ?>" alt="<?php echo esc_attr( $s['hero_alt'] ?? '' ); ?>" class="olo-sp-hero__img" loading="lazy">
                    <?php else : ?>
                        <div class="olo-sp-hero__placeholder"></div>
                    <?php endif; ?>

                    <div class="olo-sp-nav-wrap olo-sp-nav-wrap--overlay">
                        <ul class="olo-sp-nav" uk-switcher="<?php echo esc_attr( $switcher_attr ); ?>">
                            <?php foreach ( $items as $i => $item ) : ?>
                            <li<?php echo $i === 0 ? ' class="uk-active"' : ''; ?>>
                                <a href="#" class="olo-sp-nav__btn" data-olo-interactive><?php echo esc_html( $item['nav_label'] ); ?></a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php elseif ( $nav_position === 'top' ) : ?>
                <div class="olo-sp-nav-wrap olo-sp-nav-wrap--top">
                    <ul class="olo-sp-nav" uk-switcher="<?php echo esc_attr( $switcher_attr ); ?>">
                        <?php foreach ( $items as $i => $item ) : ?>
                        <li<?php echo $i === 0 ? ' class="uk-active"' : ''; ?>>
                            <a href="#" class="olo-sp-nav__btn" data-olo-interactive><?php echo esc_html( $item['nav_label'] ); ?></a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php if ( ! empty( $hero_image ) ) : ?>
                <div class="olo-sp-hero"><img src="<?php echo esc_url( $hero_image ); ?>" alt="" class="olo-sp-hero__img" loading="lazy"></div>
                <?php endif; ?>
            <?php elseif ( $nav_position === 'side-left' || $nav_position === 'side-right' ) : ?>
                <ul class="olo-sp-nav" uk-switcher="<?php echo esc_attr( $switcher_attr ); ?>">
                    <?php foreach ( $items as $i => $item ) : ?>
                    <li<?php echo $i === 0 ? ' class="uk-active"' : ''; ?>>
                        <a href="#" class="olo-sp-nav__btn" data-olo-interactive><?php echo esc_html( $item['nav_label'] ); ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php if ( ! empty( $hero_image ) ) : ?>
                <div class="olo-sp-hero"><img src="<?php echo esc_url( $hero_image ); ?>" alt="" class="olo-sp-hero__img" loading="lazy"></div>
                <?php endif; ?>
            <?php else : /* bottom */ ?>
                <?php if ( ! empty( $hero_image ) ) : ?>
                <div class="olo-sp-hero"><img src="<?php echo esc_url( $hero_image ); ?>" alt="" class="olo-sp-hero__img" loading="lazy"></div>
                <?php endif; ?>
            <?php endif; ?>

            <ul id="<?php echo esc_attr( $uid . '-content' ); ?>" class="uk-switcher olo-sp-panels">
                <?php foreach ( $items as $idx => $item ) : ?>
                <li>
                    <div class="olo-sp-panel">
                        <div class="olo-sp-panel__content">
                            <?php
                            list( $spt_cls, $spt_data ) = $this->tfx_attrs( $s, 'title', wp_strip_all_tags( $item['title'] ) );
                            list( $spx_cls, $spx_data ) = $this->tfx_attrs( $s, 'text', wp_strip_all_tags( $item['text'] ) );
                            $widget_html = $this->render_widget_template( $item['widget_template_id'] ?? 0 );
                            ?>
                            <?php if ( $widget_html ) : ?>
                                <div class="olo-item-widget"><?php echo $widget_html; ?></div>
                            <?php endif; ?>
                            <<?php echo $title_tag; ?> class="olo-sp-panel__title<?php echo $spt_cls; ?>"<?php echo $spt_data; ?>><?php echo esc_html( wp_strip_all_tags( $item['title'] ) ); ?></<?php echo $title_tag; ?>>
                            <div class="olo-sp-panel__text<?php echo $spx_cls; ?>"<?php echo $spx_data; ?>><?php echo nl2br( esc_html( wp_strip_all_tags( $item['text'] ) ) ); ?></div>
                            <?php if ( ! empty( $item['button_text'] ) ) : ?>
                                <a href="<?php echo esc_url( $item['button_url'] ); ?>" class="<?php echo esc_attr( $btn_class ); ?>">
                                    <?php echo esc_html( $item['button_text'] ); ?>
                                    <?php if ( $btn_style === 'underline' ) : ?>
                                        <span aria-hidden="true">&rarr;</span>
                                    <?php endif; ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php if ( ! empty( $item['image'] ) ) : ?>
                        <div class="olo-sp-panel__media">
                            <?php
                            $sp_img = '<img src="' . esc_url( $item['image'] ) . '" alt="' . esc_attr( wp_strip_all_tags( $item['title'] ) ) . '" class="olo-sp-panel__img" loading="lazy">';
                            echo $this->render_hover_wrap( $sp_img, $item['hover_image'] ?? '', $item['hover_video'] ?? '' );
                            ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>

            <?php if ( $nav_position === 'bottom' ) : ?>
            <div class="olo-sp-nav-wrap olo-sp-nav-wrap--bottom">
                <ul class="olo-sp-nav" uk-switcher="<?php echo esc_attr( $switcher_attr ); ?>">
                    <?php foreach ( $items as $i => $item ) : ?>
                    <li<?php echo $i === 0 ? ' class="uk-active"' : ''; ?>>
                        <a href="#" class="olo-sp-nav__btn" data-olo-interactive><?php echo esc_html( $item['nav_label'] ); ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();

        // Border system (wrapper)
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}";
            echo $border_hover_css . $border_effect_css . '</style>';
        }
        return ob_get_clean();
    }

    private function parse_items( $raw ) {
        if ( ! is_array( $raw ) ) {
            return [];
        }
        $items = [];
        foreach ( $raw as $item ) {
            if ( is_array( $item ) && ! empty( $item['nav_label'] ) ) {
                $items[] = [
                    'nav_label'          => $item['nav_label'] ?? '',
                    'title'              => $item['title'] ?? '',
                    'text'               => $item['text'] ?? '',
                    'button_text'        => $item['button_text'] ?? '',
                    'button_url'         => $item['button_url'] ?? '#',
                    'image'              => $item['image'] ?? '',
                    'hover_image'        => $item['hover_image'] ?? '',
                    'hover_video'        => $item['hover_video'] ?? '',
                    'widget_template_id' => absint( $item['widget_template_id'] ?? 0 ),
                ];
            }
        }
        return $items;
    }
}
