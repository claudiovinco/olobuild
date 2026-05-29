<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_PanelSlider_Tile extends Olo_Tile_Base {

    protected $type     = 'panelslider';
    protected $name     = 'Panel Slider';
    protected $icon     = 'dashicons-slides';
    protected $category = 'interactive';
    protected $defaults = [
        'panels' => [
            [ 'id' => 'ps-1', 'title' => 'Prima card', 'content' => 'Contenuto della prima card...', 'image' => '' ],
            [ 'id' => 'ps-2', 'title' => 'Seconda card', 'content' => 'Contenuto della seconda card...', 'image' => '' ],
            [ 'id' => 'ps-3', 'title' => 'Terza card', 'content' => 'Contenuto della terza card...', 'image' => '' ],
        ],
        'columns'           => '3',
        'gap'               => 'medium',
        'card_style'        => 'default',
        'card_radius'       => '12',
        'card_padding'      => [ 'top' => 24, 'right' => 24, 'bottom' => 24, 'left' => 24 ],
        'equal_height'      => true,
        'image_ratio'       => '4/3',
        'image_height'      => '',
        'image_fit'         => 'cover',
        'image_zoom'        => true,
        'show_arrows'       => true,
        'arrow_style'       => 'circle',
        'arrow_size'        => '40',
        'arrow_color'       => '',
        'arrow_bg'          => '',
        'show_dots'         => false,
        'autoplay'          => false,
        'autoplay_interval' => '5000',
        'shadow'            => 'sm',

        'preset'              => 'card-modern',

        'card_bg'             => '#ffffff',
        'card_border_color'   => 'transparent',
        'card_border_width'   => 0,
        'card_border_style'   => 'solid',
        'card_image_radius'   => 0,
        'card_image_position' => 'top',

        'hover_lift'   => false,
        'hover_scale'  => false,
        'hover_shadow' => 'none',

        'title_size'           => '',
        'title_color'          => '',
        'title_weight'         => '700',
        'title_letter_spacing' => 0,
        'title_uppercase'      => false,
        'title_align'          => 'left',

        'content_size'        => '',
        'content_color'       => '',
        'content_align'       => 'left',
        'content_lines_clamp' => 0,

        'caption_overlay_color'    => 'rgba(0,0,0,0.55)',
        'caption_overlay_gradient' => true,

        'show_cta' => false,
        'cta_text' => 'Scopri di più',
        'cta_style' => 'underline',

        'effect_color'     => '',
        'effect_intensity' => 'medium',
        'effect_speed'     => 0,
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
     * V3.25.0 — Extra CSS for "audacious" panel-slider presets that need
     * effects beyond what the standard fields can express. V3.25.1: added
     * effect_color / effect_intensity / effect_speed parameters so the user
     * can tweak each effect from the inspector.
     */
    private function get_preset_extra_css( $preset_id, $sel, $s = [] ) {
        // @deprecated v1.0.73 — refactor profondo: i preset audaci ora settano direttamente
        // i field standard tramite TILE_PRESETS in BuilderInspector.vue, e i field wow_* via
        // build_wow_effects_css(). Nessun !important, ogni proprietà personalizzabile.
        return '';
    }

    // V3.26.0 — color_to_rgb / color_lighten / color_darken / preset_uid
    // ora centralizzati in Olo_Tile_Base (ereditati).

    private function presetUid( $sel ) {
        return $this->preset_uid( $sel );
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $panels = is_array( $s['panels'] ) ? $s['panels'] : [];
        if ( empty( $panels ) ) {
            return '<div class="olo-panelslider" style="padding:40px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);">No panels added</div>';
        }

        $uid       = 'ops-' . wp_unique_id();
        $preset_id = $s['preset'] ?? 'card-modern';
        $columns   = absint( $s['columns'] ) ?: 3;
        $gap       = in_array( $s['gap'], [ 'collapse', 'small', 'default', 'medium', 'large' ], true ) ? $s['gap'] : 'medium';
        $autoplay  = ! empty( $s['autoplay'] ) ? 'true' : 'false';
        $interval  = absint( $s['autoplay_interval'] ) ?: 5000;

        $gap_class    = $gap === 'collapse' ? 'uk-grid-collapse' : 'uk-grid-' . $gap;
        $equal_class  = ! empty( $s['equal_height'] ) ? ' uk-grid-match' : '';
        $arrow_style  = $s['arrow_style'] ?? 'circle';
        $show_arrows  = ! empty( $s['show_arrows'] ) && count( $panels ) > $columns;
        $show_dots    = ! empty( $s['show_dots'] );

        $img_position = $s['card_image_position'] ?? 'top';
        $show_cta     = ! empty( $s['show_cta'] );

        // Build scoped CSS
        $css = $this->build_scoped_css( $uid, $s );
        // v1.0.73 — refactor profondo: get_preset_extra_css svuotato, ora i preset audaci
        // settano i field standard tramite TILE_PRESETS.panelslider + helper wow_*.
        $css .= $this->build_wow_effects_css( $s, '.' . $uid, '.uk-card-title' );

        $wrap_classes = 'olo-panelslider ' . esc_attr( $uid )
            . ' olo-ps-arrows-' . esc_attr( $arrow_style )
            . ' olo-ps--preset-' . esc_attr( $preset_id )
            . ' olo-ps--img-' . esc_attr( $img_position );

        ob_start();
        ?>
        <style><?php echo $css; ?></style>
        <div class="<?php echo $wrap_classes; ?>" uk-slider="autoplay: <?php echo $autoplay; ?>; autoplay-interval: <?php echo $interval; ?>; finite: <?php echo $autoplay === 'true' ? 'false' : 'true'; ?>">
            <div class="uk-position-relative">
                <div class="uk-slider-container uk-slider-container-offset">
                    <ul class="uk-slider-items uk-child-width-1-<?php echo $columns; ?>@m uk-grid <?php echo esc_attr( $gap_class . $equal_class ); ?>">
                        <?php foreach ( $panels as $i => $panel ) :
                            $has_link   = ! empty( $panel['link'] );
                            $link_open  = '';
                            $link_close = '';
                            if ( $has_link ) {
                                $tgt = ! empty( $panel['link_target'] ) ? ' target="_blank" rel="noopener noreferrer"' : '';
                                $link_open  = '<a href="' . esc_url( $panel['link'] ) . '"' . $tgt . ' class="olo-ps-link" style="text-decoration:none;color:inherit;display:block;height:100%;">';
                                $link_close = '</a>';
                            }
                            $card_classes = 'olo-ps-card olo-ps-card--' . esc_attr( $img_position );
                        ?>
                            <li>
                                <?php echo $link_open; ?>
                                <div class="<?php echo $card_classes; ?>">
                                    <?php $widget_html = $this->render_widget_template( $panel['widget_template_id'] ?? 0 ); ?>
                                    <?php if ( $widget_html ) : ?>
                                        <div class="olo-item-widget"><?php echo $widget_html; ?></div>
                                    <?php endif; ?>
                                    <?php
                                    list( $pst_cls, $pst_data ) = $this->tfx_attrs( $s, 'title', $panel['title'] ?? '' );
                                    list( $psc_cls, $psc_data ) = $this->tfx_attrs( $s, 'content', wp_strip_all_tags( $panel['content'] ?? '' ) );

                                    $body_html = '<div class="olo-ps-body">';
                                    if ( ! empty( $panel['title'] ) ) {
                                        $body_html .= '<h3 class="olo-ps-title' . $pst_cls . '"' . $pst_data . '>' . esc_html( $panel['title'] ) . '</h3>';
                                    }
                                    if ( ! empty( $panel['content'] ) ) {
                                        $body_html .= '<div class="olo-ps-text' . $psc_cls . '"' . $psc_data . '>' . $this->safe_richtext_content( $panel['content'] ) . '</div>';
                                    }
                                    if ( $show_cta && ! empty( $s['cta_text'] ) ) {
                                        $cta_style = $s['cta_style'] ?? 'underline';
                                        $cta_arrow = $cta_style === 'arrow' ? ' <span class="olo-ps-cta__arrow" aria-hidden="true">&rarr;</span>' : '';
                                        $body_html .= '<span class="olo-ps-cta olo-ps-cta--' . esc_attr( $cta_style ) . '">' . esc_html( $s['cta_text'] ) . $cta_arrow . '</span>';
                                    }
                                    $body_html .= '</div>';

                                    $media_html = '';
                                    if ( ! empty( $panel['image'] ) ) {
                                        $img_html = '<img class="olo-ps-img" src="' . esc_url( $panel['image'] ) . '" alt="' . esc_attr( $panel['title'] ?? '' ) . '" loading="lazy">';
                                        $img_with_hover = $this->render_hover_wrap( $img_html, $panel['hover_image'] ?? '', $panel['hover_video'] ?? '' );
                                        $media_html = '<div class="olo-ps-media">' . $img_with_hover . '</div>';
                                    }

                                    if ( $img_position === 'bg' ) {
                                        // Image is the background, body is overlaid
                                        echo $media_html;
                                        echo '<div class="olo-ps-overlay"></div>';
                                        echo $body_html;
                                    } elseif ( $img_position === 'bottom' ) {
                                        echo $body_html;
                                        echo $media_html;
                                    } elseif ( $img_position === 'side-left' || $img_position === 'side-right' ) {
                                        echo $media_html;
                                        echo $body_html;
                                    } else {
                                        // top (default)
                                        echo $media_html;
                                        echo $body_html;
                                    }
                                    ?>
                                </div>
                                <?php echo $link_close; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <?php if ( $show_arrows ) :
                    echo $this->render_arrows( $arrow_style );
                endif; ?>
            </div>

            <?php if ( $show_dots ) : ?>
            <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>
            <?php endif; ?>
        </div>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();

        // Border system
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
     * Build scoped CSS for the panel slider tile.
     */
    private function build_scoped_css( $uid, $s ) {
        $sel = '.' . $uid;
        $css = '';

        // Card radius + padding
        $radius           = $this->build_border_radius_css( $s['card_radius'] ?? 12 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['card_radius_hover'] ?? null );
        $padding          = Olo_Tile_Utils::spacing_css( $s['card_padding'] ?? 24, 24 );
        $radius_css       = $radius ? 'border-radius:' . $radius . ';' : '';

        // Card colors
        $card_bg     = $this->safe_color_css( $s['card_bg'] ?? '#ffffff' ) ?: '#ffffff';
        $card_bcol   = $this->safe_color_css( $s['card_border_color'] ?? 'transparent' );
        $card_bw     = max( 0, intval( $s['card_border_width'] ?? 0 ) );
        $card_bs     = in_array( $s['card_border_style'] ?? 'solid', [ 'solid', 'dashed', 'dotted', 'double' ], true ) ? $s['card_border_style'] : 'solid';
        $card_border_decl = ( $card_bw > 0 && $card_bcol && $card_bcol !== 'transparent' )
            ? "border: {$card_bw}px {$card_bs} {$card_bcol};"
            : 'border: 0;';

        // Shadow
        $shadow_val = Olo_Tile_Utils::shadow_value( $s, 'shadow' );
        $shadow_css = ( $shadow_val && $shadow_val !== 'none' ) ? 'box-shadow:' . $shadow_val . ';' : '';

        // Hover shadow
        $hover_shadow_val = $s['hover_shadow'] ?? 'none';
        $hover_shadow_map = [
            'sm' => '0 4px 12px rgba(0,0,0,0.08)',
            'md' => '0 8px 24px rgba(0,0,0,0.12)',
            'lg' => '0 16px 36px rgba(0,0,0,0.16)',
            'xl' => '0 28px 56px rgba(0,0,0,0.20)',
        ];
        $hover_shadow_css = isset( $hover_shadow_map[ $hover_shadow_val ] ) ? 'box-shadow:' . $hover_shadow_map[ $hover_shadow_val ] . ';' : '';

        // Allow shadow to be visible: slider items must not clip
        if ( $shadow_css || $hover_shadow_css || ! empty( $s['hover_lift'] ) || ! empty( $s['hover_scale'] ) ) {
            $css .= $sel . ' .uk-slider-items > li{overflow:visible;}';
        }

        // Card base
        $css .= $sel . ' .olo-ps-card{' . $radius_css . $shadow_css . 'background:' . $card_bg . ';' . $card_border_decl . 'overflow:hidden;display:flex;flex-direction:column;height:100%;transition:transform 0.35s cubic-bezier(.4,0,.2,1),box-shadow 0.35s ease,border-radius 400ms cubic-bezier(.4,0,.2,1);position:relative;}';

        if ( $radius_hover_css !== '' ) {
            $css .= $sel . ' .olo-ps-card:hover{border-radius:' . $radius_hover_css . ' !important;}';
        }

        // Hover effects (lift, scale, shadow)
        $hover_transforms = [];
        if ( ! empty( $s['hover_lift'] ) )  $hover_transforms[] = 'translateY(-6px)';
        if ( ! empty( $s['hover_scale'] ) ) $hover_transforms[] = 'scale(1.02)';
        if ( ! empty( $hover_transforms ) || $hover_shadow_css ) {
            $tx = ! empty( $hover_transforms ) ? 'transform:' . implode( ' ', $hover_transforms ) . ';' : '';
            $css .= $sel . ' .olo-ps-card:hover{' . $tx . $hover_shadow_css . '}';
        }

        // Equal-height
        if ( ! empty( $s['equal_height'] ) ) {
            $css .= $sel . ' .uk-slider-items{align-items:stretch;}';
            $css .= $sel . ' .uk-slider-items > li{display:flex;}';
            $css .= $sel . ' .uk-slider-items > li > .olo-ps-link{width:100%;}';
            $css .= $sel . ' .uk-slider-items > li .olo-ps-card{flex:1 1 auto;}';
            $css .= $sel . ' .olo-ps-body{flex:1 1 auto;display:flex;flex-direction:column;}';
        }

        // Image ratio / height / fit
        $img_ratio  = $s['image_ratio'] ?? '4/3';
        $img_height = absint( $s['image_height'] ?? 0 );
        $img_fit    = in_array( $s['image_fit'] ?? 'cover', [ 'cover', 'contain', 'fill' ], true ) ? $s['image_fit'] : 'cover';
        $img_radius = max( 0, intval( $s['card_image_radius'] ?? 0 ) );

        $css .= $sel . ' .olo-ps-media{position:relative;overflow:hidden;width:100%;flex:0 0 auto;}';
        if ( $img_ratio && $img_ratio !== 'auto' ) {
            $css .= $sel . ' .olo-ps-media{aspect-ratio:' . esc_attr( $img_ratio ) . ';}';
        } elseif ( $img_height > 0 ) {
            $css .= $sel . ' .olo-ps-media{height:' . $img_height . 'px;}';
        }
        if ( $img_radius > 0 ) {
            $css .= $sel . ' .olo-ps-media,' . $sel . ' .olo-ps-img{border-radius:' . $img_radius . 'px;}';
        }
        $css .= $sel . ' .olo-ps-img{width:100%;height:100%;object-fit:' . esc_attr( $img_fit ) . ';display:block;transition:transform 0.5s cubic-bezier(.4,0,.2,1);}';

        if ( ! empty( $s['image_zoom'] ) ) {
            $css .= $sel . ' .olo-ps-card:hover .olo-ps-img{transform:scale(1.06);}';
        }

        // Image position layouts
        $img_position = $s['card_image_position'] ?? 'top';
        if ( $img_position === 'side-left' ) {
            $css .= $sel . ' .olo-ps-card{flex-direction:row;}';
            $css .= $sel . ' .olo-ps-card .olo-ps-media{width:45%;flex:0 0 45%;height:100%;}';
            $css .= $sel . ' .olo-ps-card .olo-ps-body{flex:1;}';
            $css .= '@media (max-width:640px){' . $sel . ' .olo-ps-card{flex-direction:column;} ' . $sel . ' .olo-ps-card .olo-ps-media{width:100%;flex:0 0 auto;}}';
        }
        if ( $img_position === 'side-right' ) {
            $css .= $sel . ' .olo-ps-card{flex-direction:row-reverse;}';
            $css .= $sel . ' .olo-ps-card .olo-ps-media{width:45%;flex:0 0 45%;height:100%;}';
            $css .= $sel . ' .olo-ps-card .olo-ps-body{flex:1;}';
            $css .= '@media (max-width:640px){' . $sel . ' .olo-ps-card{flex-direction:column;} ' . $sel . ' .olo-ps-card .olo-ps-media{width:100%;flex:0 0 auto;}}';
        }
        if ( $img_position === 'bg' ) {
            // Image fills the card; body is positioned absolutely on top
            $css .= $sel . ' .olo-ps-card.olo-ps-card--bg{position:relative;}';
            $css .= $sel . ' .olo-ps-card.olo-ps-card--bg .olo-ps-media{position:absolute;inset:0;width:100%;height:100%;z-index:0;}';
            $overlay_color = $this->safe_color_css( $s['caption_overlay_color'] ?? 'rgba(0,0,0,0.55)' );
            $overlay_grad  = ! empty( $s['caption_overlay_gradient'] );
            $overlay_bg    = $overlay_grad
                ? 'linear-gradient(180deg, rgba(0,0,0,0) 0%, ' . $overlay_color . ' 100%)'
                : $overlay_color;
            $css .= $sel . ' .olo-ps-card.olo-ps-card--bg .olo-ps-overlay{position:absolute;inset:0;background:' . $overlay_bg . ';z-index:1;pointer-events:none;}';
            $css .= $sel . ' .olo-ps-card.olo-ps-card--bg .olo-ps-body{position:relative;z-index:2;margin-top:auto;color:#ffffff;}';
            $css .= $sel . ' .olo-ps-card.olo-ps-card--bg .olo-ps-title{color:#ffffff !important;}';
            $css .= $sel . ' .olo-ps-card.olo-ps-card--bg .olo-ps-text{color:rgba(255,255,255,0.9) !important;}';
            // Force a reasonable min-height so the body has room
            $css .= $sel . ' .olo-ps-card.olo-ps-card--bg{min-height:340px;}';
        }

        // Body padding
        $css .= $sel . ' .olo-ps-body{padding:' . $padding . ';gap:8px;}';

        // Title
        $title_size  = absint( $s['title_size'] ?? 0 );
        $title_col   = $s['title_color'] ?? '';
        $title_w     = preg_match( '/^[1-9]00$/', (string) ( $s['title_weight'] ?? '700' ) ) ? $s['title_weight'] : '700';
        $title_ls    = floatval( $s['title_letter_spacing'] ?? 0 );
        $title_upper = ! empty( $s['title_uppercase'] );
        $title_align = in_array( $s['title_align'] ?? 'left', [ 'left', 'center', 'right' ], true ) ? $s['title_align'] : 'left';

        $title_styles = "font-weight:{$title_w};letter-spacing:{$title_ls}em;text-align:{$title_align};";
        if ( $title_size > 0 ) $title_styles .= "font-size:{$title_size}px;";
        if ( $title_col )      $title_styles .= 'color:' . esc_attr( $title_col ) . ';';
        if ( $title_upper )    $title_styles .= 'text-transform:uppercase;';
        $css .= $sel . ' .olo-ps-title{' . $title_styles . 'margin:0;line-height:1.3;}';

        // Content
        $content_size  = absint( $s['content_size'] ?? 0 );
        $content_col   = $s['content_color'] ?? '';
        $content_align = in_array( $s['content_align'] ?? 'left', [ 'left', 'center', 'right' ], true ) ? $s['content_align'] : 'left';
        $content_clamp = max( 0, intval( $s['content_lines_clamp'] ?? 0 ) );

        $content_styles = "text-align:{$content_align};line-height:1.6;margin:0;";
        if ( $content_size > 0 ) $content_styles .= "font-size:{$content_size}px;";
        if ( $content_col )      $content_styles .= 'color:' . esc_attr( $content_col ) . ';';
        if ( $content_clamp > 0 ) {
            $content_styles .= "display:-webkit-box;-webkit-line-clamp:{$content_clamp};-webkit-box-orient:vertical;overflow:hidden;";
        }
        $css .= $sel . ' .olo-ps-text{' . $content_styles . '}';

        // CTA
        $css .= $sel . ' .olo-ps-cta{display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:600;margin-top:auto;padding-top:8px;color:var(--olo-color-primary,#e1474f);transition:all 0.25s ease;width:fit-content;}';
        $css .= $sel . ' .olo-ps-cta--underline{border-bottom:1.5px solid currentColor;padding-bottom:2px;}';
        $css .= $sel . ' .olo-ps-cta--arrow .olo-ps-cta__arrow{transition:transform 0.25s ease;}';
        $css .= $sel . ' .olo-ps-card:hover .olo-ps-cta--arrow .olo-ps-cta__arrow{transform:translateX(4px);}';
        $css .= $sel . ' .olo-ps-cta--pill{background:var(--olo-color-primary,#e1474f);color:#fff;border-radius:999px;padding:8px 18px;}';
        $css .= $sel . ' .olo-ps-cta--text{color:var(--olo-color-primary,#e1474f);}';

        // Arrows + dots
        $arrow_size  = absint( $s['arrow_size'] ?? 40 ) ?: 40;
        $arrow_color = $s['arrow_color'] ?? '';
        $arrow_bg    = $s['arrow_bg'] ?? '';
        $arrow_style = $s['arrow_style'] ?? 'circle';
        $css .= $this->build_arrow_css( $sel, $arrow_style, $arrow_size, $arrow_color, $arrow_bg );

        return $css;
    }

    /**
     * Generate CSS for arrow variants.
     */
    private function build_arrow_css( $sel, $style, $size, $color, $bg ) {
        $css = '';
        $color = $color ? esc_attr( $color ) : '#1f2937';
        $bg    = $bg ? esc_attr( $bg ) : '#ffffff';
        $half  = intval( $size / 2 );

        $css .= $sel . ' .olo-ps-arrow{position:absolute;top:50%;transform:translateY(-50%);z-index:5;display:flex;align-items:center;justify-content:center;cursor:pointer;border:none;outline:none;transition:all 0.25s ease;text-decoration:none;}';
        $css .= $sel . ' .olo-ps-arrow.olo-ps-prev{left:-' . ( $half + 4 ) . 'px;}';
        $css .= $sel . ' .olo-ps-arrow.olo-ps-next{right:-' . ( $half + 4 ) . 'px;}';
        $css .= '@media (max-width:960px){' . $sel . ' .olo-ps-arrow.olo-ps-prev{left:8px;}' . $sel . ' .olo-ps-arrow.olo-ps-next{right:8px;}}';
        $css .= $sel . ' .olo-ps-arrow svg{width:50%;height:50%;display:block;pointer-events:none;}';
        $css .= $sel . ' .olo-ps-arrow:hover{transform:translateY(-50%) scale(1.08);}';

        switch ( $style ) {
            case 'circle':
                $css .= $sel . ' .olo-ps-arrow{width:' . $size . 'px;height:' . $size . 'px;border-radius:50%;background:' . $bg . ';color:' . $color . ';box-shadow:0 4px 12px rgba(0,0,0,0.15);}';
                $css .= $sel . ' .olo-ps-arrow:hover{box-shadow:0 6px 18px rgba(0,0,0,0.22);}';
                break;
            case 'circle-outline':
                $css .= $sel . ' .olo-ps-arrow{width:' . $size . 'px;height:' . $size . 'px;border-radius:50%;background:transparent;color:' . $color . ';border:2px solid ' . $color . ';}';
                $css .= $sel . ' .olo-ps-arrow:hover{background:' . $color . ';color:' . $bg . ';}';
                break;
            case 'square':
                $css .= $sel . ' .olo-ps-arrow{width:' . $size . 'px;height:' . $size . 'px;border-radius:6px;background:' . $bg . ';color:' . $color . ';box-shadow:0 4px 12px rgba(0,0,0,0.15);}';
                break;
            case 'minimal':
                $css .= $sel . ' .olo-ps-arrow{width:' . $size . 'px;height:' . $size . 'px;background:transparent;color:' . $color . ';}';
                $css .= $sel . ' .olo-ps-arrow svg{width:80%;height:80%;}';
                $css .= $sel . ' .olo-ps-arrow:hover{opacity:0.7;}';
                break;
            case 'chevron-bold':
                $css .= $sel . ' .olo-ps-arrow{width:' . $size . 'px;height:' . $size . 'px;background:transparent;color:' . $color . ';}';
                $css .= $sel . ' .olo-ps-arrow svg{width:90%;height:90%;stroke-width:3;}';
                break;
            case 'arrow-long':
                $css .= $sel . ' .olo-ps-arrow{width:' . ( $size + 12 ) . 'px;height:' . $size . 'px;background:' . $bg . ';color:' . $color . ';border-radius:' . intval( $size / 2 ) . 'px;box-shadow:0 4px 12px rgba(0,0,0,0.15);}';
                $css .= $sel . ' .olo-ps-arrow svg{width:55%;height:55%;}';
                break;
            case 'fancy':
                $css .= $sel . ' .olo-ps-arrow{width:' . $size . 'px;height:' . $size . 'px;border-radius:50%;background:linear-gradient(135deg,var(--olo-color-primary, #e1474f),var(--olo-color-accent, #f4a23b));color:#fff;box-shadow:0 6px 20px color-mix(in srgb, var(--olo-color-primary, #e1474f) 35%, transparent);}';
                $css .= $sel . ' .olo-ps-arrow:hover{box-shadow:0 10px 28px color-mix(in srgb, var(--olo-color-primary, #e1474f) 50%, transparent);}';
                break;
            case 'uikit':
            default:
                $css .= $sel . ' .olo-ps-arrow{width:' . $size . 'px;height:' . $size . 'px;color:' . $color . ';}';
                break;
        }

        // Dots
        $css .= $sel . ' .uk-dotnav > * > *{width:10px;height:10px;border:2px solid ' . $color . ';background:transparent;}';
        $css .= $sel . ' .uk-dotnav > .uk-active > *{background:' . $color . ';border-color:' . $color . ';}';

        return $css;
    }

    /**
     * Render arrow markup with correct SVG icons per style.
     */
    private function render_arrows( $style ) {
        $prev_label = esc_attr__( 'Precedente', 'olobuild' );
        $next_label = esc_attr__( 'Successivo', 'olobuild' );

        if ( $style === 'uikit' ) {
            return '<a class="olo-ps-arrow olo-ps-prev" href uk-slidenav-previous uk-slider-item="previous" aria-label="' . $prev_label . '"></a>'
                 . '<a class="olo-ps-arrow olo-ps-next" href uk-slidenav-next uk-slider-item="next" aria-label="' . $next_label . '"></a>';
        }

        $svg_prev = $this->arrow_svg( $style, 'prev' );
        $svg_next = $this->arrow_svg( $style, 'next' );

        return '<button type="button" class="olo-ps-arrow olo-ps-prev" uk-slider-item="previous" aria-label="' . $prev_label . '">' . $svg_prev . '</button>'
             . '<button type="button" class="olo-ps-arrow olo-ps-next" uk-slider-item="next" aria-label="' . $next_label . '">' . $svg_next . '</button>';
    }

    /**
     * Return SVG markup for the arrow style and direction.
     */
    private function arrow_svg( $style, $dir ) {
        $rotate = $dir === 'prev' ? ' style="transform:rotate(180deg);"' : '';

        switch ( $style ) {
            case 'arrow-long':
                return '<svg' . $rotate . ' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg"><line x1="3" y1="12" x2="20" y2="12"/><polyline points="14 6 20 12 14 18"/></svg>';
            case 'chevron-bold':
                return '<svg' . $rotate . ' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg"><polyline points="9 6 15 12 9 18"/></svg>';
            default:
                return '<svg' . $rotate . ' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg"><polyline points="9 6 15 12 9 18"/></svg>';
        }
    }
}
