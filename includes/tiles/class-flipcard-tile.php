<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_FlipCard_Tile extends Olo_Tile_Base {

    protected $type     = 'flipcard';
    protected $name     = 'FlipCard';
    protected $icon     = 'dashicons-image-flip-horizontal';
    protected $category = 'marketing';
    protected $defaults = [
        'preset' => 'custom',
        // Fronte
        'front_image'      => '',
        'front_video'      => '',
        'front_icon'       => 'star',
        'front_icon_size'  => '40',
        'front_icon_color' => '',
        'front_title'      => 'Titolo fronte',
        'front_description'=> 'Descrizione della card visibile.',
        'front_bg'         => '#1e1e2e',
        'front_overlay'    => '',
        'front_image_fit'     => 'cover',
        'front_image_padding' => '0',
        'front_image_radius'  => '0',
        'front_text_color' => '',
        'front_text_align' => 'center',
        'front_valign'     => 'center',

        // Retro
        'back_image'       => '',
        'back_video'       => '',
        'back_icon'        => '',
        'back_icon_size'   => '40',
        'back_icon_color'  => '',
        'back_title'       => 'Titolo retro',
        'back_description' => 'Contenuto retro con dettagli.',
        'back_bg'          => '',
        'back_overlay'     => '',
        'back_image_fit'      => 'cover',
        'back_image_padding'  => '0',
        'back_image_radius'   => '0',
        'back_text_color'  => '#FFFFFF',
        'back_text_align'  => 'center',
        'back_valign'      => 'center',
        'back_cta_text'    => 'Scopri di più',
        'back_cta_url'     => '',
        'back_cta_target'  => false,
        'back_cta_bg'      => '',
        'back_cta_color'   => '',
        'back_cta_radius'  => '6',

        // Animazione
        'flip_direction'   => 'horizontal',
        'flip_duration'    => '600',
        'flip_trigger'     => 'hover',
        'flip_easing'      => 'ease-in-out',

        // Card
        'card_height'        => '350',
        'card_border_radius' => '12',
        'card_shadow'        => 'md',
        'card_border_width'       => '0',
        'card_border_color'       => '',
        'card_padding'            => '24',
        'card_border'                => [],
        'card_border_hover'          => [],
        'card_border_hover_duration' => 300,
        'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,

        // Tipografia
        'title_size'   => '22',
        'title_weight' => '600',
        'desc_size'    => '14',
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-fc-' . wp_rand( 10000, 99999 );

        $dir      = in_array( $s['flip_direction'], [ 'horizontal', 'vertical', 'diagonal', 'cube', 'slide-flip', 'zoom-flip' ] ) ? $s['flip_direction'] : 'horizontal';
        $duration = intval( $s['flip_duration'] ) ?: 600;
        $easing   = esc_attr( $s['flip_easing'] ?: 'ease-in-out' );
        $trigger  = in_array( $s['flip_trigger'], [ 'hover', 'click' ] ) ? $s['flip_trigger'] : 'hover';
        $height   = intval( $s['card_height'] ) ?: 350;
        $radius   = Olo_Tile_Utils::border_radius( $s['card_border_radius'] ?? 0 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['card_border_radius_hover'] ?? null );
        $shadow   = Olo_Tile_Utils::shadow_value( $s, 'card_shadow' );
        $bw       = intval( $s['card_border_width'] );
        $bc       = $this->safe_color_css( $s['card_border_color'] ) ?: 'var(--olo-color-text, #374151)';
        $padding = Olo_Tile_Utils::spacing_css( $s['tile_padding'] ?? $s['card_padding'] ?? 24, 24 );
        $halfH    = round( $height / 2 );

        // Tipografia
        $title_size   = intval( $s['title_size'] ) ?: 22;
        $title_weight = intval( $s['title_weight'] ) ?: 600;
        $desc_size    = intval( $s['desc_size'] ) ?: 14;

        // Transforms
        $back_initial  = $this->get_back_transform( $dir, $halfH );
        $flip_transform = $this->get_flip_transform( $dir, $halfH );

        // Front face
        $front_fg      = $this->safe_color_css( $s['front_text_color'] ) ?: 'var(--olo-color-muted, #F3F4F6)';
        $front_bg      = $this->safe_color_css( $s['front_bg'] ) ?: '#1e1e2e';
        $front_align   = in_array( $s['front_text_align'], [ 'left', 'center', 'right' ] ) ? $s['front_text_align'] : 'center';
        $front_valign  = $this->valign_css( $s['front_valign'] );

        // Back face
        $back_fg       = $this->safe_color_css( $s['back_text_color'] ) ?: '#FFFFFF';
        $back_bg       = $this->safe_color_css( $s['back_bg'] ) ?: 'var(--olo-color-primary, #6366F1)';
        $back_align    = in_array( $s['back_text_align'], [ 'left', 'center', 'right' ] ) ? $s['back_text_align'] : 'center';
        $back_valign   = $this->valign_css( $s['back_valign'] );

        // CTA
        $cta_text   = esc_html( wp_strip_all_tags( $s['back_cta_text'] ) );
        $cta_url    = esc_url( $s['back_cta_url'] );
        $cta_target = ! empty( $s['back_cta_target'] ) ? ' target="_blank" rel="noopener"' : '';
        $cta_bg     = $this->safe_color_css( $s['back_cta_bg'] ) ?: '#FFFFFF';
        $cta_color  = $this->safe_color_css( $s['back_cta_color'] ) ?: 'var(--olo-color-primary, #6366F1)';
        $cta_radius = Olo_Tile_Utils::border_radius( $s['back_cta_radius'] ?? 0 );
        $cta_radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['back_cta_radius_hover'] ?? null );

        // Cube: front needs translateZ, back rotated on side
        $front_extra = '';
        if ( $dir === 'cube' ) {
            $front_extra = "transform: translateZ({$halfH}px);";
        }

        // Slide-flip: use larger perspective for smoother slide
        $perspective = ( $dir === 'slide-flip' || $dir === 'cube' ) ? 2000 : 1200;

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                perspective: <?php echo $perspective; ?>px;
                width: 100%;
                box-sizing: border-box;
                height: <?php echo $height; ?>px;
                overflow: hidden;
                <?php if ( $radius && $radius !== '0px' ) : ?>border-radius: <?php echo $radius; ?>;<?php endif; ?>
                cursor: pointer;
            }
            <?php if ( $radius_hover_css !== '' ) : ?>.<?php echo $uid; ?>{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?>:hover{border-radius:<?php echo $radius_hover_css; ?> !important}<?php endif; ?>
            .<?php echo $uid; ?> .olo-fc-inner {
                position: relative;
                width: 100%;
                height: 100%;
                transform-style: preserve-3d;
                transition: transform <?php echo $duration; ?>ms <?php echo $easing; ?>;
                <?php if ( $dir === 'cube' ) : ?>
                transform-origin: center center -<?php echo $halfH; ?>px;
                <?php endif; ?>
            }
            <?php if ( $trigger === 'hover' ) : ?>
            .<?php echo $uid; ?>:hover .olo-fc-inner {
                transform: <?php echo $flip_transform; ?>;
            }
            <?php else : ?>
            .<?php echo $uid; ?>.olo-fc-flipped .olo-fc-inner {
                transform: <?php echo $flip_transform; ?>;
            }
            <?php endif; ?>

            .<?php echo $uid; ?> .olo-fc-front,
            .<?php echo $uid; ?> .olo-fc-back {
                position: absolute;
                inset: 0;
                backface-visibility: hidden;
                -webkit-backface-visibility: hidden;
                overflow: hidden;
                display: flex;
                flex-direction: column;
                <?php if ( $radius && $radius !== '0px' ) : ?>border-radius: <?php echo $radius; ?>;<?php endif; ?>
                <?php if ( $shadow && $shadow !== 'none' ) : ?>box-shadow: <?php echo $shadow; ?>;<?php endif; ?>
                <?php
                // V3.21: nuovo card_border (4 lati hoverable) ha priorità sul legacy bw/bc.
                $_card_border_css = $this->build_border_css( $s['card_border'] ?? [] );
                if ( $_card_border_css ) {
                    echo $_card_border_css;
                } elseif ( $bw > 0 ) {
                    echo "border:{$bw}px solid {$bc};";
                }
                ?>
            }

            .<?php echo $uid; ?> .olo-fc-front {
                background-color: <?php echo $front_bg; ?>;
                color: <?php echo $front_fg; ?>;
                <?php echo $front_extra; ?>
            }
            .<?php echo $uid; ?> .olo-fc-back {
                background-color: <?php echo $back_bg; ?>;
                color: <?php echo $back_fg; ?>;
                transform: <?php echo $back_initial; ?>;
            }

            .<?php echo $uid; ?> .olo-fc-bg {
                position: absolute;
                inset: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
                z-index: 0;
            }
            .<?php echo $uid; ?> .olo-fc-overlay {
                position: absolute;
                inset: 0;
                z-index: 1;
            }
            .<?php echo $uid; ?> .olo-fc-content {
                position: relative;
                z-index: 2;
                padding: <?php echo $padding; ?>;
                flex: 1;
                display: flex;
                flex-direction: column;
                justify-content: <?php echo $front_valign; ?>;
                text-align: <?php echo $front_align; ?>;
            }
            .<?php echo $uid; ?> .olo-fc-back .olo-fc-content {
                justify-content: <?php echo $back_valign; ?>;
                text-align: <?php echo $back_align; ?>;
            }

            .<?php echo $uid; ?> .olo-fc-icon {
                line-height: 1;
                margin-bottom: 12px;
            }
            .<?php echo $uid; ?> .olo-fc-title {
                font-size: <?php echo $title_size; ?>px;
                font-weight: <?php echo $title_weight; ?>;
                margin: 0 0 8px;
                line-height: 1.3;
            }
            .<?php echo $uid; ?> .olo-fc-desc {
                font-size: <?php echo $desc_size; ?>px;
                line-height: 1.5;
                margin: 0;
                opacity: 0.9;
            }

            .<?php echo $uid; ?> .olo-fc-cta {
                display: inline-block;
                margin-top: 16px;
                padding: 10px 24px;
                background: <?php echo $cta_bg; ?>;
                color: <?php echo $cta_color; ?> !important;
                border-radius: <?php echo $cta_radius; ?>;
                text-decoration: none !important;
                font-weight: 600;
                font-size: <?php echo $desc_size; ?>px;
                transition: opacity .2s, transform .2s;
            }
            <?php if ( $cta_radius_hover_css !== '' ) : ?>.<?php echo $uid; ?> .olo-fc-cta{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?> .olo-fc-cta:hover{border-radius:<?php echo $cta_radius_hover_css; ?> !important}<?php endif; ?>
            .<?php echo $uid; ?> .olo-fc-cta:hover {
                opacity: .85;
                transform: translateY(-1px);
                color: <?php echo $cta_color; ?> !important;
                text-decoration: none !important;
            }

            @media (max-width: 959px) {
                .<?php echo $uid; ?> {
                    height: <?php echo max( 200, round( $height * 0.75 ) ); ?>px;
                }
            }
            @media (max-width: 767px) {
                .<?php echo $uid; ?> {
                    height: <?php echo max( 180, round( $height * 0.6 ) ); ?>px;
                }
                .<?php echo $uid; ?>:hover .olo-fc-inner {
                    transform: none !important;
                }
                .<?php echo $uid; ?>.olo-fc-flipped .olo-fc-inner {
                    transform: <?php echo $flip_transform; ?> !important;
                }
            }
        </style>
        <div class="olo-fc <?php echo esc_attr( $uid ); ?> olo-fc-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" data-trigger="<?php echo esc_attr( $trigger ); ?>">
            <div class="olo-fc-inner">
                <div class="olo-fc-front">
                    <?php echo $this->render_face( $s, 'front' ); ?>
                </div>
                <div class="olo-fc-back">
                    <?php echo $this->render_face( $s, 'back' ); ?>
                </div>
            </div>
        </div>
        <script>
        (function(){
            document.querySelectorAll('.<?php echo $uid; ?>').forEach(function(fc){
                var t = fc.dataset.trigger;
                if (t === 'click' || ('ontouchstart' in window)) {
                    fc.addEventListener('click', function(e) {
                        if (e.target.closest('a')) return;
                        fc.classList.toggle('olo-fc-flipped');
                    });
                    <?php if ( $trigger === 'hover' ) : ?>
                    if ('ontouchstart' in window) {
                        fc.addEventListener('mouseenter', function(e) { e.preventDefault(); });
                    }
                    <?php endif; ?>
                }
            });
        })();
        </script>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();

        // Border system — wrapper tile
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );

        // Border system — card (front + back)
        $card_border_sel       = ".{$uid} .olo-fc-front, .{$uid} .olo-fc-back";
        $card_border_hover_css = $this->build_border_hover_css( $card_border_sel, $s['card_border'] ?? [], $s['card_border_hover'] ?? [], intval( $s['card_border_hover_duration'] ?? 300 ) );

        if ( $border_css || $border_hover_css || $border_effect_css || $card_border_hover_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}";
            echo $border_hover_css . $border_effect_css;
            echo $card_border_hover_css . '</style>';
        }

        return ob_get_clean();
    }

    /**
     * Render a single face (front or back).
     */
    private function render_face( $s, $side ) {
        $prefix = $side . '_';
        $html   = '';

        // Background image
        if ( ! empty( $s[ $prefix . 'image' ] ) ) {
            $img_pad = intval( $s[ $prefix . 'image_padding' ] ?? 0 );
            $img_rad = intval( $s[ $prefix . 'image_radius' ] ?? 0 );
            $img_fit = in_array( $s[ $prefix . 'image_fit' ] ?? 'cover', [ 'cover', 'contain', 'fill' ] ) ? $s[ $prefix . 'image_fit' ] : 'cover';
            $img_obj_fit = "object-fit:{$img_fit};";
            if ( $img_fit === 'contain' ) {
                $img_obj_fit .= 'object-position:center;background:inherit;';
            }
            if ( $img_pad > 0 || $img_rad > 0 ) {
                $wrap_style = 'position:absolute;z-index:0;overflow:hidden;';
                if ( $img_pad > 0 ) {
                    $wrap_style .= "inset:{$img_pad}px;";
                } else {
                    $wrap_style .= 'inset:0;';
                }
                if ( $img_rad > 0 ) {
                    $wrap_style .= "border-radius:{$img_rad}px;";
                }
                $html .= '<div style="' . esc_attr( $wrap_style ) . '">';
                $html .= Olo_Tile_Utils::img_srcset( absint( $s[ $prefix . 'image_id' ] ?? 0 ), $s[ $prefix . 'image' ], ucfirst( $side ) . ' side', 'olo-fc-bg', 'full', 'style="position:absolute;inset:0;width:100%;height:100%;' . esc_attr( $img_obj_fit ) . '"' );
                $html .= '</div>';
            } else {
                $html .= Olo_Tile_Utils::img_srcset( absint( $s[ $prefix . 'image_id' ] ?? 0 ), $s[ $prefix . 'image' ], ucfirst( $side ) . ' side', 'olo-fc-bg', 'full', 'style="' . esc_attr( $img_obj_fit ) . '"' );
            }
        }

        // Background video
        if ( ! empty( $s[ $prefix . 'video' ] ) ) {
            $html .= '<video class="olo-fc-bg" autoplay muted loop playsinline><source src="' . esc_url( $s[ $prefix . 'video' ] ) . '" type="video/mp4"></video>';
        }

        // Overlay
        $overlay = $this->safe_color_css( $s[ $prefix . 'overlay' ] );
        if ( $overlay && ! empty( $s[ $prefix . 'image' ] ) ) {
            $html .= '<div class="olo-fc-overlay" style="background:' . $overlay . '"></div>';
        }

        // Content
        $html .= '<div class="olo-fc-content">';

        // Icon
        $icon = trim( $s[ $prefix . 'icon' ] ?? '' );
        if ( $icon ) {
            $icon_size  = intval( $s[ $prefix . 'icon_size' ] ) ?: 40;
            $icon_color = $this->safe_color_css( $s[ $prefix . 'icon_color' ] );
            $icon_style = "font-size:{$icon_size}px;";
            if ( $icon_color ) {
                $icon_style .= "color:{$icon_color};";
            }
            $html .= '<div class="olo-fc-icon"><span uk-icon="icon: ' . esc_attr( $icon ) . '; width: ' . $icon_size . '; height: ' . $icon_size . '" style="' . esc_attr( $icon_style ) . '"></span></div>';
        }

        // Title
        $title = esc_html( wp_strip_all_tags( $s[ $prefix . 'title' ] ) );
        if ( $title ) {
            list( $tt_cls, $tt_data ) = $this->tfx_attrs( $s, $prefix . 'title', $title );
            $html .= '<div class="olo-fc-title' . $tt_cls . '"' . $tt_data . '>' . $title . '</div>';
        }

        // Description
        $desc_plain = wp_strip_all_tags( $s[ $prefix . 'description' ] );
        $desc = nl2br( esc_html( $desc_plain ) );
        if ( $desc ) {
            list( $td_cls, $td_data ) = $this->tfx_attrs( $s, $prefix . 'description', $desc_plain );
            $html .= '<div class="olo-fc-desc' . $td_cls . '"' . $td_data . '>' . $desc . '</div>';
        }

        // CTA (back only)
        if ( $side === 'back' && ! empty( $s['back_cta_text'] ) ) {
            $url    = esc_url( $s['back_cta_url'] );
            $target = ! empty( $s['back_cta_target'] ) ? ' target="_blank" rel="noopener"' : '';
            $tag    = $url ? 'a' : 'span';
            $href   = $url ? ' href="' . $url . '"' : '';
            $html  .= '<div><' . $tag . ' class="olo-fc-cta"' . $href . $target . '>' . esc_html( wp_strip_all_tags( $s['back_cta_text'] ) ) . '</' . $tag . '></div>';
        }

        $html .= '</div>'; // .olo-fc-content

        return $html;
    }

    /**
     * Get the initial transform for the back face based on direction.
     */
    private function get_back_transform( $dir, $halfH ) {
        switch ( $dir ) {
            case 'vertical':   return 'rotateX(180deg)';
            case 'diagonal':   return 'rotateX(180deg) rotateY(180deg)';
            case 'cube':       return "rotateY(-90deg) translateZ({$halfH}px)";
            case 'slide-flip': return 'rotateY(180deg)';
            case 'zoom-flip':  return 'rotateY(180deg)';
            default:           return 'rotateY(180deg)'; // horizontal
        }
    }

    /**
     * Get the flip transform for the inner container.
     */
    private function get_flip_transform( $dir, $halfH ) {
        switch ( $dir ) {
            case 'vertical':   return 'rotateX(180deg)';
            case 'diagonal':   return 'rotateX(180deg) rotateY(180deg)';
            case 'cube':       return 'rotateY(90deg)';
            case 'slide-flip': return 'translateX(-10%) rotateY(-180deg)';
            case 'zoom-flip':  return 'scale(1.05) rotateY(180deg)';
            default:           return 'rotateY(180deg)'; // horizontal
        }
    }

    /**
     * Map vertical alignment to CSS justify-content.
     */
    private function valign_css( $val ) {
        $map = [ 'top' => 'flex-start', 'center' => 'center', 'bottom' => 'flex-end' ];
        return $map[ $val ] ?? 'center';
    }

}
