<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_OverlayGrid_Tile extends Olo_Tile_Base {

    protected $type     = 'overlaygrid';
    protected $name     = 'Overlay Grid';
    protected $icon     = 'dashicons-grid-view';
    protected $category = 'interactive';
    protected $defaults = [
        'items' => [
            [ 'id' => 'og-1', 'image' => '', 'title' => 'Elemento 1', 'subtitle' => '', 'link' => '' ],
            [ 'id' => 'og-2', 'image' => '', 'title' => 'Elemento 2', 'subtitle' => '', 'link' => '' ],
            [ 'id' => 'og-3', 'image' => '', 'title' => 'Elemento 3', 'subtitle' => '', 'link' => '' ],
        ],
        'columns'             => '3',
        'columns_mobile'      => '1',
        'gap'                 => 'medium',
        'height'              => '320',
        'match_height'        => true,
        'overlay_position'    => 'bottom',
        'overlay_horizontal'  => 'left',
        'overlay_style'       => 'overlay-primary',
        'overlay_padding'     => 'medium',
        'title_size'          => 'h3',
        'hover_effect'        => 'zoom',
        'hover_overlay'       => 'always',
        'ribbon_position'     => 'top-right',
        'ribbon_bg'           => '#e11d48',
        'ribbon_color'        => '#ffffff',
        'shadow'              => 'sm',

        'preset'              => 'editorial-grid',
        'item_radius'         => 12,
        'overlay_color'       => 'rgba(0,0,0,0.45)',
        'overlay_gradient'    => true,
        'title_color'         => '#ffffff',
        'title_weight'        => '700',
        'title_letter_spacing'=> 0,
        'title_uppercase'     => false,
        'subtitle_color'      => 'rgba(255,255,255,0.85)',
        'subtitle_size'       => 14,
        'show_cta'            => false,
        'cta_text'            => 'Scopri',
        'cta_style'           => 'arrow',

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

    private function color_to_rgba( $color, $alpha ) {
        $rgb = $this->color_to_rgb( $color );
        return "rgba({$rgb},{$alpha})";
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

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $items = is_array( $s['items'] ) ? $s['items'] : [];
        if ( empty( $items ) ) {
            return '<div class="olo-overlaygrid" style="padding:40px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);">No items added</div>';
        }

        $columns   = absint( $s['columns'] ) ?: 3;
        $cols_mob  = absint( $s['columns_mobile'] ) ?: 1;
        $height    = absint( $s['height'] ) ?: 300;
        $position  = esc_attr( $s['overlay_position'] ?: 'bottom' );
        $style     = in_array( $s['overlay_style'], [ 'overlay-primary', 'overlay-default' ], true ) ? $s['overlay_style'] : 'overlay-primary';

        $preset_id = $s['preset'] ?? 'editorial-grid';

        // New granular controls
        $item_radius  = max( 0, intval( $s['item_radius'] ?? 12 ) );
        $overlay_clr  = $this->safe_color_css( $s['overlay_color'] ?? 'rgba(0,0,0,0.45)' );
        $overlay_grad = ! empty( $s['overlay_gradient'] );
        $title_clr    = $this->safe_color_css( $s['title_color'] ?? '#ffffff' ) ?: '#ffffff';
        $title_w      = preg_match( '/^[1-9]00$/', (string) ($s['title_weight'] ?? '700') ) ? $s['title_weight'] : '700';
        $title_ls     = floatval( $s['title_letter_spacing'] ?? 0 );
        $title_upper  = ! empty( $s['title_uppercase'] );
        $subtitle_clr = $this->safe_color_css( $s['subtitle_color'] ?? 'rgba(255,255,255,0.85)' ) ?: 'rgba(255,255,255,0.85)';
        $subtitle_sz  = max( 10, intval( $s['subtitle_size'] ?? 14 ) );
        $show_cta     = ! empty( $s['show_cta'] );
        $cta_text     = $s['cta_text'] ?? '';
        $cta_style    = $s['cta_style'] ?? 'arrow';

        // Gap
        $gap = $s['gap'] ?? 'medium';
        $gap_class = 'uk-grid-' . ( $gap === 'collapse' ? 'collapse' : esc_attr( $gap ) );

        // Match height
        $grid_attr = 'uk-grid';
        if ( ! empty( $s['match_height'] ) ) {
            $grid_attr .= ' uk-height-match';
        }

        // Text alignment
        $text_align = $s['overlay_horizontal'] ?? 'left';
        $text_class = $text_align !== 'left' ? ' uk-text-' . esc_attr( $text_align ) : '';

        // Padding
        $pad = $s['overlay_padding'] ?? 'medium';
        $pad_class = '';
        if ( $pad === 'small' )  $pad_class = ' uk-padding-small';
        if ( $pad === 'large' )  $pad_class = ' uk-padding';

        // Title tag
        $title_tag = in_array( $s['title_size'] ?? 'h3', [ 'h1', 'h2', 'h3', 'h4' ], true ) ? $s['title_size'] : 'h3';

        // Hover effects
        $hover_effect  = $s['hover_effect'] ?? 'none';
        $hover_overlay = $s['hover_overlay'] ?? 'always';

        $img_class = 'mos-og-img';
        if ( $hover_effect !== 'none' ) {
            $img_class .= ' mos-og-hover-' . esc_attr( $hover_effect );
        }

        $overlay_class = '';
        $needs_toggle  = false;
        if ( $hover_overlay !== 'always' ) {
            $needs_toggle  = true;
            $overlay_class = ' uk-transition-' . esc_attr( $hover_overlay );
        }

        // Ribbon
        $ribbon_position = esc_attr( $s['ribbon_position'] ?? 'top-right' );
        $ribbon_bg       = $this->safe_color_css( $s['ribbon_bg'] ?? '#e11d48' );
        $ribbon_color    = $this->safe_color_css( $s['ribbon_color'] ?? '#ffffff' );

        $uid = 'mos-og-' . wp_rand( 10000, 99999 );

        $wrap_class = 'olo-overlaygrid olo-og--preset-' . esc_attr( $preset_id ) . ' ' . $uid;

        // Shadow
        $shadow_v = $s['shadow'] ?? 'none';
        $shadow_map = [
            'sm' => '0 4px 12px rgba(0,0,0,.08)',
            'md' => '0 8px 24px rgba(0,0,0,.12)',
            'lg' => '0 16px 48px rgba(0,0,0,.18)',
            'xl' => '0 28px 64px rgba(0,0,0,.22)',
        ];
        $shadow_css = isset( $shadow_map[ $shadow_v ] ) ? 'box-shadow:' . $shadow_map[ $shadow_v ] . ';' : '';

        // CTA classes
        $cta_class_map = [
            'underline' => 'olo-og-cta olo-og-cta--underline',
            'arrow'     => 'olo-og-cta olo-og-cta--arrow',
            'pill'      => 'olo-og-cta olo-og-cta--pill',
            'text'      => 'olo-og-cta olo-og-cta--text',
        ];
        $cta_class = $cta_class_map[ $cta_style ] ?? $cta_class_map['arrow'];

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> .mos-og-img { transition: transform 0.5s ease, filter 0.5s ease; width: 100%; height: <?php echo $height; ?>px; object-fit: cover; }
            .<?php echo $uid; ?> > div > div > .uk-panel,
            .<?php echo $uid; ?> > div > div > a {
                <?php if ( $item_radius ) : ?>border-radius: <?php echo $item_radius; ?>px;<?php endif; ?>
                <?php echo $shadow_css; ?>
            }
            .<?php echo $uid; ?> .mos-og-hover-zoom:hover,
            .<?php echo $uid; ?> .uk-transition-toggle:hover .mos-og-hover-zoom { transform: scale(1.08); }
            .<?php echo $uid; ?> .mos-og-hover-zoom-rotate:hover,
            .<?php echo $uid; ?> .uk-transition-toggle:hover .mos-og-hover-zoom-rotate { transform: scale(1.08) rotate(2deg); }
            .<?php echo $uid; ?> .mos-og-hover-brightness { filter: brightness(0.7); }
            .<?php echo $uid; ?> .mos-og-hover-brightness:hover,
            .<?php echo $uid; ?> .uk-transition-toggle:hover .mos-og-hover-brightness { filter: brightness(1); }
            .<?php echo $uid; ?> .mos-og-hover-desaturate { filter: grayscale(100%); }
            .<?php echo $uid; ?> .mos-og-hover-desaturate:hover,
            .<?php echo $uid; ?> .uk-transition-toggle:hover .mos-og-hover-desaturate { filter: grayscale(0%); }
            .<?php echo $uid; ?> .mos-og-hover-blur-in { filter: blur(3px); }
            .<?php echo $uid; ?> .mos-og-hover-blur-in:hover,
            .<?php echo $uid; ?> .uk-transition-toggle:hover .mos-og-hover-blur-in { filter: blur(0); }

            /* Custom overlay */
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
            .<?php echo $uid; ?> .uk-overlay h4 {
                color: <?php echo $title_clr; ?>;
                font-weight: <?php echo $title_w; ?>;
                letter-spacing: <?php echo $title_ls; ?>em;
                <?php if ( $title_upper ) : ?>text-transform: uppercase;<?php endif; ?>
                margin: 0;
            }
            .<?php echo $uid; ?> .uk-overlay p {
                color: <?php echo $subtitle_clr; ?>;
                font-size: <?php echo $subtitle_sz; ?>px;
                margin: 6px 0 0;
            }

            /* CTA */
            .<?php echo $uid; ?> .olo-og-cta { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; margin-top: 10px; color: <?php echo $title_clr; ?>; transition: all 0.25s ease; }
            .<?php echo $uid; ?> .olo-og-cta--underline { border-bottom: 1.5px solid currentColor; padding-bottom: 2px; }
            .<?php echo $uid; ?> .olo-og-cta--arrow .olo-og-cta__arrow { transition: transform 0.25s ease; }
            .<?php echo $uid; ?> .uk-panel:hover .olo-og-cta--arrow .olo-og-cta__arrow,
            .<?php echo $uid; ?> a:hover .olo-og-cta--arrow .olo-og-cta__arrow { transform: translateX(4px); }
            .<?php echo $uid; ?> .olo-og-cta--pill { background: var(--olo-color-primary, #e8622a); color: #fff; border-radius: 999px; padding: 8px 18px; }

            /* Ribbon */
            .<?php echo $uid; ?> .mos-og-ribbon { position: absolute; z-index: 2; font-size: 11px; font-weight: 700; padding: 4px 12px; text-transform: uppercase; letter-spacing: 0.5px; background: <?php echo $ribbon_bg; ?>; color: <?php echo $ribbon_color; ?>; }
            .<?php echo $uid; ?> .mos-og-ribbon--top-right { top: 0; right: 14px; border-radius: 0 0 4px 4px; }
            .<?php echo $uid; ?> .mos-og-ribbon--top-left { top: 0; left: 14px; border-radius: 0 0 4px 4px; }

            <?php
            // V3.26.0 — preset extras
            echo $this->get_preset_extra_css( $preset_id, '.' . $uid, $s );
            echo $this->build_wow_effects_css( $s, '.' . $uid, '.olo-grid-title' );
            ?>
        </style>
        <div class="<?php echo $wrap_class; ?>">
            <div class="uk-child-width-1-<?php echo $cols_mob; ?> uk-child-width-1-<?php echo $columns; ?>@m <?php echo $gap_class; ?>" <?php echo $grid_attr; ?>>
                <?php foreach ( $items as $item ) :
                    $has_link    = ! empty( $item['link'] );
                    $link_url    = $has_link ? esc_url( $item['link'] ) : '';
                    $toggle_cls  = $needs_toggle ? ' uk-transition-toggle' : '';
                    $wrapper_tag = $has_link ? 'a' : 'div';
                    $wrapper_attr = $has_link
                        ? 'href="' . $link_url . '" class="uk-link-reset uk-display-block' . $toggle_cls . '" style="overflow:hidden;position:relative;" tabindex="0"'
                        : 'class="uk-panel' . $toggle_cls . '" style="overflow:hidden;position:relative;" tabindex="0"';
                ?>
                    <div>
                        <<?php echo $wrapper_tag; ?> <?php echo $wrapper_attr; ?>>
                            <?php if ( ! empty( $item['image'] ) ) : ?>
                                <?php
                                $og_img = Olo_Tile_Utils::img_srcset( absint( $item['image_id'] ?? 0 ), $item['image'], $item['title'] ?? '', $img_class );
                                echo $this->render_hover_wrap( $og_img, $item['hover_image'] ?? '', $item['hover_video'] ?? '' );
                                ?>
                            <?php else : ?>
                                <div style="height:<?php echo $height; ?>px;background:#1F2937;width:100%;"></div>
                            <?php endif; ?>
                            <?php if ( ! empty( $item['ribbon'] ) ) : ?>
                                <span class="mos-og-ribbon mos-og-ribbon--<?php echo $ribbon_position; ?>"><?php echo esc_html( $item['ribbon'] ); ?></span>
                            <?php endif; ?>
                            <?php
                            list( $ogt_cls, $ogt_data ) = $this->tfx_attrs( $s, 'title', $item['title'] ?? '' );
                            list( $ogs_cls, $ogs_data ) = $this->tfx_attrs( $s, 'subtitle', $item['subtitle'] ?? '' );
                            // Override per-item: titolo/sottotitolo possono avere colore custom che batte il globale
                            $item_t_clr = $this->safe_color_css( $item['item_title_color'] ?? '' );
                            $item_s_clr = $this->safe_color_css( $item['item_subtitle_color'] ?? '' );
                            $item_t_style = $item_t_clr ? ' style="color:' . esc_attr( $item_t_clr ) . ' !important;"' : '';
                            $item_s_style = $item_s_clr ? ' style="color:' . esc_attr( $item_s_clr ) . ' !important;"' : '';
                            ?>
                            <div class="uk-<?php echo esc_attr( $style ); ?> uk-position-<?php echo $position; ?> uk-panel<?php echo $text_class . $pad_class . $overlay_class; ?>">
                                <<?php echo $title_tag; ?> class="uk-margin-remove<?php echo $ogt_cls; ?>"<?php echo $ogt_data; ?><?php echo $item_t_style; ?>><?php echo esc_html( $item['title'] ?? '' ); ?></<?php echo $title_tag; ?>>
                                <?php if ( ! empty( $item['subtitle'] ) ) : ?>
                                    <p class="uk-margin-small-top<?php echo $ogs_cls; ?>"<?php echo $ogs_data; ?><?php echo $item_s_style; ?>><?php echo esc_html( $item['subtitle'] ); ?></p>
                                <?php endif; ?>
                                <?php if ( $show_cta && ! empty( $cta_text ) ) : ?>
                                    <span class="<?php echo esc_attr( $cta_class ); ?>"><?php echo esc_html( $cta_text ); ?><?php if ( $cta_style === 'arrow' ) : ?> <span class="olo-og-cta__arrow" aria-hidden="true">&rarr;</span><?php endif; ?></span>
                                <?php endif; ?>
                            </div>
                        </<?php echo $wrapper_tag; ?>>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();

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
}
