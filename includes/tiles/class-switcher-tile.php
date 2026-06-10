<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Switcher_Tile extends Olo_Tile_Base {

    protected $type     = 'switcher';
    protected $name     = 'Switcher';
    protected $icon     = 'dashicons-welcome-widgets-menus';
    protected $category = 'interactive';
    protected $defaults = [
        'items'     => [
            [ 'title' => 'Prima scheda', 'content' => 'Contenuto della prima scheda.' ],
            [ 'title' => 'Seconda scheda', 'content' => 'Contenuto della seconda scheda.' ],
        ],
        'preset'             => 'pill-slide',
        'nav_style'          => 'tab',
        'animation'          => 'fade',
        'animation_duration' => '250',
        'vertical'           => false,
        'tab_padding_y'      => '10',
        'tab_padding_x'      => '18',
        'tab_font_size'      => '14',
        'tab_font_weight'    => '500',
        'tab_gap'            => '4',
        'tab_radius'         => '8',
        'container_bg'       => '#f1f5f9',
        'container_padding'  => '4',
        'container_radius'   => '10',
        'active_bg'          => '#ffffff',
        'active_color'       => '#1e293b',
        'inactive_color'     => '#64748b',
        'hover_bg'           => '',
        'indicator_type'     => 'none',
        'indicator_color'    => '',
        'content_bg'         => '',
        'content_color'      => '#1e293b',
        'content_padding_y'  => '20',
        'content_padding_x'  => '0',
        'shadow'             => 'none',
        'effect_color'       => '',
        'effect_intensity'   => 'medium',
        'effect_speed'       => 0,
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
    private function get_preset_extra_css( $preset_id, $uid, $vertical, $s = [] ) {
        // @deprecated v1.0.73 — refactor profondo: i preset audaci ora settano direttamente
        // i field standard tramite TILE_PRESETS in BuilderInspector.vue, e i field wow_* via
        // build_wow_effects_css(). Nessun !important, ogni proprietà personalizzabile.
        return '';
    }

    /**
     * V3.23.0 — Curated visual presets for the Switcher tile, inspired by
     * common modern design patterns (Linear, Stripe, Spotify, Apple).
     */
    private function get_preset_styles( $preset_id ) {
        $presets = [
            'pill-slide' => [
                'tab_padding_y'     => 10, 'tab_padding_x' => 18,
                'tab_font_size'     => 14, 'tab_font_weight' => '500',
                'tab_gap'           => 4,  'tab_radius' => 8,
                'container_bg'      => '#f1f5f9',
                'container_padding' => 4,
                'container_radius'  => 10,
                'active_bg'         => '#ffffff',
                'active_color'      => '#1e293b',
                'inactive_color'    => '#64748b',
                'hover_bg'          => 'rgba(0,0,0,0.03)',
                'indicator_type'    => 'pill',
                'indicator_color'   => '#e1474f',
                'shadow'            => 'none',
            ],
            'underline-animated' => [
                'tab_padding_y'     => 12, 'tab_padding_x' => 20,
                'tab_font_size'     => 15, 'tab_font_weight' => '600',
                'tab_gap'           => 8,  'tab_radius' => 0,
                'container_bg'      => '',
                'container_padding' => 0,
                'container_radius'  => 0,
                'active_bg'         => '',
                'active_color'      => '#e1474f',
                'inactive_color'    => '#64748b',
                'hover_bg'          => '',
                'indicator_type'    => 'underline',
                'indicator_color'   => '#e1474f',
                'shadow'            => 'none',
            ],
            'card-tabs' => [
                'tab_padding_y'     => 10, 'tab_padding_x' => 18,
                'tab_font_size'     => 14, 'tab_font_weight' => '500',
                'tab_gap'           => 8,  'tab_radius' => 8,
                'container_bg'      => '',
                'container_padding' => 0,
                'container_radius'  => 0,
                'active_bg'         => '#fdf2ec',
                'active_color'      => '#b04217',
                'inactive_color'    => '#64748b',
                'hover_bg'          => '#f8fafc',
                'indicator_type'    => 'none',
                'indicator_color'   => '#e1474f',
                'shadow'            => 'sm',
            ],
            'minimal-text' => [
                'tab_padding_y'     => 8,  'tab_padding_x' => 14,
                'tab_font_size'     => 14, 'tab_font_weight' => '500',
                'tab_gap'           => 4,  'tab_radius' => 6,
                'container_bg'      => '',
                'container_padding' => 0,
                'container_radius'  => 0,
                'active_bg'         => '#fdf2ec',
                'active_color'      => '#b04217',
                'inactive_color'    => '#64748b',
                'hover_bg'          => 'rgba(0,0,0,0.03)',
                'indicator_type'    => 'none',
                'indicator_color'   => '#e1474f',
                'shadow'            => 'none',
            ],
            'vertical-sidebar' => [
                'tab_padding_y'     => 10, 'tab_padding_x' => 14,
                'tab_font_size'     => 14, 'tab_font_weight' => '500',
                'tab_gap'           => 2,  'tab_radius' => 6,
                'container_bg'      => '',
                'container_padding' => 0,
                'container_radius'  => 0,
                'active_bg'         => '#fdf2ec',
                'active_color'      => '#b04217',
                'inactive_color'    => '#64748b',
                'hover_bg'          => 'rgba(0,0,0,0.03)',
                'indicator_type'    => 'left-bar',
                'indicator_color'   => '#e1474f',
                'shadow'            => 'none',
                'vertical'          => true,
            ],
        ];
        return $presets[ $preset_id ] ?? null;
    }

    public function render( $settings ) {
        $s     = wp_parse_args( $settings, $this->defaults );
        $items = $this->parse_items( $s['items'] );
        $count = count( $items );

        if ( $count === 0 ) {
            return '';
        }

        // V3.23.1 — preset is applied JS-side at the moment the user picks it
        // (BuilderInspector.applyPreset). The PHP renderer just reads the
        // already-populated fields, so manual edits on top of a preset win.
        $preset_id = $s['preset'] ?? 'pill-slide';

        $vertical  = ! empty( $s['vertical'] );
        $duration  = max( 80, intval( $s['animation_duration'] ?? 250 ) );

        // Build switcher attribute (UIkit animation)
        $switcher_attr = '';
        if ( ! empty( $s['animation'] ) ) {
            $switcher_attr = 'animation: uk-animation-' . esc_attr( $s['animation'] );
        }

        $uid = 'olo-sw-' . wp_rand( 10000, 99999 );

        // ── Color helpers ──
        $tab_bg_active   = $this->safe_color_css( $s['active_bg'] ) ?: '';
        $tab_color_act   = $this->safe_color_css( $s['active_color'] ) ?: 'var(--olo-color-text, #1e293b)';
        $tab_color_inact = $this->safe_color_css( $s['inactive_color'] ) ?: '#64748b';
        $tab_hover_bg    = $this->safe_color_css( $s['hover_bg'] ) ?: '';
        $container_bg    = $this->safe_color_css( $s['container_bg'] ) ?: '';
        $indicator_clr   = $this->safe_color_css( $s['indicator_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $content_bg      = $this->safe_color_css( $s['content_bg'] ) ?: '';
        $content_color   = $this->safe_color_css( $s['content_color'] ) ?: '';

        $tab_pad_y = max( 0, intval( $s['tab_padding_y'] ?? 10 ) );
        $tab_pad_x = max( 0, intval( $s['tab_padding_x'] ?? 18 ) );
        $tab_fs    = max( 10, intval( $s['tab_font_size'] ?? 14 ) );
        $tab_fw    = preg_match( '/^[1-9]00$/', (string) ($s['tab_font_weight'] ?? '500') ) ? $s['tab_font_weight'] : '500';
        $tab_gap   = max( 0, intval( $s['tab_gap'] ?? 4 ) );
        // Dual-format: Number legacy E oggetto {tl,tr,br,bl} (build_border_radius_css ritorna '' se zero/vuoto).
        $tab_rad_css  = $this->build_border_radius_css( $s['tab_radius'] ?? 8 ) ?: '0px';
        $cont_pad  = max( 0, intval( $s['container_padding'] ?? 4 ) );
        $cont_rad_css = $this->build_border_radius_css( $s['container_radius'] ?? 10 );
        $content_pad_y = max( 0, intval( $s['content_padding_y'] ?? 20 ) );
        $content_pad_x = max( 0, intval( $s['content_padding_x'] ?? 0 ) );
        $indicator = $s['indicator_type'] ?? 'none';

        $shadow_css = '';
        if ( ($s['shadow'] ?? 'none') === 'sm' ) {
            $shadow_css = 'box-shadow: 0 1px 2px rgba(16,24,40,0.06), 0 1px 3px rgba(16,24,40,0.08);';
        } elseif ( ($s['shadow'] ?? 'none') === 'md' ) {
            $shadow_css = 'box-shadow: 0 4px 6px rgba(16,24,40,0.08), 0 2px 4px rgba(16,24,40,0.06);';
        } elseif ( ($s['shadow'] ?? 'none') === 'lg' ) {
            $shadow_css = 'box-shadow: 0 12px 24px rgba(16,24,40,0.10), 0 4px 8px rgba(16,24,40,0.08);';
        }

        ob_start();
        ?>
        <style>
            /* ═══ Switcher V3.23.0 — preset: <?php echo esc_html( $preset_id ); ?> ═══ */
            .<?php echo esc_attr( $uid ); ?> { margin: 0; }
            .<?php echo esc_attr( $uid ); ?> .olo-sw-nav,
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab,
            .<?php echo esc_attr( $uid ); ?> ul.uk-subnav,
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab-left {
                margin: 0;
                padding: <?php echo $cont_pad; ?>px;
                <?php if ( $container_bg ) : ?>background: <?php echo $container_bg; ?>;<?php endif; ?>
                <?php if ( $cont_rad_css ) : ?>border-radius: <?php echo $cont_rad_css; ?>;<?php endif; ?>
                <?php echo $shadow_css; ?>
                gap: <?php echo $tab_gap; ?>px;
                list-style: none;
                display: flex;
                <?php if ( $vertical ) : ?>flex-direction: column;<?php endif; ?>
                border: 0;
                <?php if ( $indicator === 'underline' && ! $vertical ) : ?>
                border-bottom: 1px solid #e5e7eb;
                gap: 0;
                padding: 0;
                <?php endif; ?>
            }
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab > *,
            .<?php echo esc_attr( $uid ); ?> ul.uk-subnav > *,
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab-left > * {
                padding: 0;
                margin: 0;
                position: relative;
                <?php if ( $indicator === 'underline' && ! $vertical ) : ?>margin-bottom: -1px;<?php endif; ?>
            }
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab > * > a,
            .<?php echo esc_attr( $uid ); ?> ul.uk-subnav > * > a,
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab-left > * > a {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: <?php echo $tab_pad_y; ?>px <?php echo $tab_pad_x; ?>px;
                font-size: <?php echo $tab_fs; ?>px;
                font-weight: <?php echo $tab_fw; ?>;
                line-height: 1.4;
                color: <?php echo $tab_color_inact; ?>;
                text-transform: none;
                text-decoration: none;
                border-radius: <?php echo $tab_rad_css; ?>;
                transition: background-color <?php echo $duration; ?>ms ease, color <?php echo $duration; ?>ms ease, box-shadow <?php echo $duration; ?>ms ease;
                white-space: nowrap;
                <?php if ( $vertical ) : ?>
                justify-content: flex-start;
                width: 100%;
                <?php endif; ?>
                <?php if ( $indicator === 'underline' && ! $vertical ) : ?>
                border-bottom: 2px solid transparent;
                border-radius: 0;
                padding-bottom: <?php echo max( 0, $tab_pad_y - 2 ); ?>px;
                <?php endif; ?>
                <?php if ( $indicator === 'overline' && ! $vertical ) : ?>
                border-top: 2px solid transparent;
                border-radius: 0;
                padding-top: <?php echo max( 0, $tab_pad_y - 2 ); ?>px;
                <?php endif; ?>
                <?php if ( $indicator === 'left-bar' && $vertical ) : ?>
                border-left: 2px solid transparent;
                padding-left: <?php echo max( 0, $tab_pad_x - 2 ); ?>px;
                border-radius: 0 6px 6px 0;
                <?php endif; ?>
            }
            <?php if ( $tab_hover_bg ) : ?>
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab > *:not(.uk-active) > a:hover,
            .<?php echo esc_attr( $uid ); ?> ul.uk-subnav > *:not(.uk-active) > a:hover,
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab-left > *:not(.uk-active) > a:hover {
                background: <?php echo $tab_hover_bg; ?>;
                color: <?php echo $tab_color_act; ?>;
            }
            <?php endif; ?>
            /* a11y: anello di focus visibile da tastiera sul tab */
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab > * > a:focus-visible,
            .<?php echo esc_attr( $uid ); ?> ul.uk-subnav > * > a:focus-visible,
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab-left > * > a:focus-visible {
                outline: none;
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
            }
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab > .uk-active > a,
            .<?php echo esc_attr( $uid ); ?> ul.uk-subnav > .uk-active > a,
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab-left > .uk-active > a {
                color: <?php echo $tab_color_act; ?>;
                <?php if ( $tab_bg_active ) : ?>background: <?php echo $tab_bg_active; ?>;<?php endif; ?>
                <?php if ( $indicator === 'pill' ) : ?>
                box-shadow: 0 1px 2px rgba(16,24,40,0.06), 0 1px 3px rgba(16,24,40,0.05);
                <?php endif; ?>
                <?php if ( $indicator === 'underline' && ! $vertical ) : ?>
                border-bottom-color: <?php echo $indicator_clr; ?>;
                <?php endif; ?>
                <?php if ( $indicator === 'overline' && ! $vertical ) : ?>
                border-top-color: <?php echo $indicator_clr; ?>;
                <?php endif; ?>
                <?php if ( $indicator === 'left-bar' && $vertical ) : ?>
                border-left-color: <?php echo $indicator_clr; ?>;
                <?php endif; ?>
            }
            /* UIkit kills its own ::before/::after pseudo-elements that we don't need */
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab::before,
            .<?php echo esc_attr( $uid ); ?> ul.uk-subnav::before {
                content: none !important;
            }
            .<?php echo esc_attr( $uid ); ?> ul.uk-tab > * > a::before,
            .<?php echo esc_attr( $uid ); ?> ul.uk-subnav > * > a::before {
                content: none !important;
            }
            /* Content panel */
            .<?php echo esc_attr( $uid ); ?> .uk-switcher,
            .<?php echo esc_attr( $uid ); ?> .olo-switcher-content {
                margin: 0;
                padding: 0;
                list-style: none;
            }
            .<?php echo esc_attr( $uid ); ?> .uk-switcher > li,
            .<?php echo esc_attr( $uid ); ?> .olo-switcher-content > li {
                padding: <?php echo $content_pad_y; ?>px <?php echo $content_pad_x; ?>px;
                <?php if ( $content_bg ) : ?>background: <?php echo $content_bg; ?>;<?php endif; ?>
                <?php if ( $content_color ) : ?>color: <?php echo $content_color; ?>;<?php endif; ?>
                line-height: 1.65;
                font-size: 14px;
            }
            <?php if ( $vertical ) : ?>
            .<?php echo esc_attr( $uid ); ?>.olo-switcher--vert {
                display: grid;
                grid-template-columns: minmax(160px, 220px) 1fr;
                gap: 24px;
                align-items: stretch;
            }
            /* V3.23.2 — vertical layout: nav distributes evenly to match the
               content panel height, so the left column never looks shorter
               than the right one. Each tab keeps a sensible min-height. */
            .<?php echo esc_attr( $uid ); ?>.olo-switcher--vert ul.uk-tab-left {
                height: 100%;
                align-self: stretch;
            }
            .<?php echo esc_attr( $uid ); ?>.olo-switcher--vert ul.uk-tab-left > li {
                flex: 1 1 auto;
                min-height: 40px;
                display: flex;
            }
            .<?php echo esc_attr( $uid ); ?>.olo-switcher--vert ul.uk-tab-left > li > a {
                width: 100%;
                height: 100%;
                align-items: center;
            }
            <?php endif; ?>
            <?php
            // V3.23.2 — emit preset-specific CSS for the audacious presets
            // v1.0.73 — refactor profondo: get_preset_extra_css svuotato, ora i preset audaci
            // settano i field standard tramite TILE_PRESETS.switcher + helper wow_*.
            echo $this->build_wow_effects_css( $s, '.' . esc_attr( $uid ), '.olo-switcher-title' );
            ?>
        </style>
        <?php

        if ( $vertical ) :
            ?>
            <div class="olo-switcher olo-switcher--vert <?php echo esc_attr( $uid ); ?>">
                <ul class="uk-tab-left olo-sw-nav" uk-tab="connect: .<?php echo esc_attr( $uid ); ?>-content; <?php echo esc_attr( $switcher_attr ); ?>">
                    <?php foreach ( $items as $i => $item ) : ?>
                    <li<?php echo $i === 0 ? ' class="uk-active"' : ''; ?>><?php list( $swt_cls, $swt_data ) = $this->tfx_attrs( $s, "title", wp_strip_all_tags( $item["title"] ) ); ?><a href="#" class="<?php echo trim( $swt_cls ); ?>"<?php echo $swt_data; ?>><?php echo esc_html( wp_strip_all_tags( $item["title"] ) ); ?></a></li>
                    <?php endforeach; ?>
                </ul>
                <ul class="uk-switcher olo-switcher-content <?php echo esc_attr( $uid ); ?>-content">
                    <?php foreach ( $items as $i => $item ) : ?>
                    <?php list( $swc_cls, $swc_data ) = $this->tfx_attrs( $s, "content", wp_strip_all_tags( $item["content"] ) ); $widget_html = $this->render_widget_template( $item['widget_template_id'] ?? 0 ); $active_cls = $i === 0 ? ' uk-active' : ''; ?><li class="<?php echo trim( $swc_cls . $active_cls ); ?>"<?php echo $swc_data; ?>><?php if ( $widget_html ) echo '<div class="olo-item-widget">' . $widget_html . '</div>'; echo nl2br( esc_html( wp_strip_all_tags( $item["content"] ) ) ); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php
        else :
            // Horizontal — usa uk-tab per il connect (e per il toggle automatico via .uk-active)
            ?>
            <div class="olo-switcher <?php echo esc_attr( $uid ); ?>">
                <ul class="uk-tab olo-sw-nav" uk-tab="connect: .<?php echo esc_attr( $uid ); ?>-content; <?php echo esc_attr( $switcher_attr ); ?>">
                    <?php foreach ( $items as $i => $item ) : ?>
                    <li<?php echo $i === 0 ? ' class="uk-active"' : ''; ?>><?php list( $swt_cls, $swt_data ) = $this->tfx_attrs( $s, "title", wp_strip_all_tags( $item["title"] ) ); ?><a href="#" class="<?php echo trim( $swt_cls ); ?>"<?php echo $swt_data; ?>><?php echo esc_html( wp_strip_all_tags( $item["title"] ) ); ?></a></li>
                    <?php endforeach; ?>
                </ul>
                <ul class="uk-switcher olo-switcher-content <?php echo esc_attr( $uid ); ?>-content">
                    <?php foreach ( $items as $i => $item ) : ?>
                    <?php list( $swc_cls, $swc_data ) = $this->tfx_attrs( $s, "content", wp_strip_all_tags( $item["content"] ) ); $widget_html = $this->render_widget_template( $item['widget_template_id'] ?? 0 ); $active_cls = $i === 0 ? ' uk-active' : ''; ?><li class="<?php echo trim( $swc_cls . $active_cls ); ?>"<?php echo $swc_data; ?>><?php if ( $widget_html ) echo '<div class="olo-item-widget">' . $widget_html . '</div>'; echo nl2br( esc_html( wp_strip_all_tags( $item["content"] ) ) ); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php
        endif;

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

    /**
     * Parse items from array format.
     */
    private function parse_items( $raw ) {
        if ( is_array( $raw ) ) {
            $items = [];
            foreach ( $raw as $item ) {
                if ( is_array( $item ) && ! empty( $item['title'] ) ) {
                    $items[] = [
                        'title'              => $item['title'],
                        'content'            => $item['content'] ?? '',
                        'widget_template_id' => absint( $item['widget_template_id'] ?? 0 ),
                    ];
                }
            }
            return $items;
        }
        return [];
    }
}
