<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_FlipCard_Tile extends Olo_Tile_Base {

    protected $type     = 'flipcard';
    protected $name     = 'FlipCard';
    protected $icon     = 'dashicons-image-flip-horizontal';
    protected $category = 'content';
    protected $defaults = [
        // Fronte
        'front_image'      => '',
        'front_video'      => '',
        'front_icon'       => 'star',
        'front_icon_size'  => '40',
        'front_icon_color' => '',
        'front_title'      => 'Titolo fronte',
        'front_description'=> 'Descrizione della card visibile.',
        'front_bg'         => '#1e1e2e',
        'front_overlay'    => 'rgba(0,0,0,0.3)',
        'front_text_color' => '#F3F4F6',
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
        'back_bg'          => '#6366F1',
        'back_overlay'     => '',
        'back_text_color'  => '#FFFFFF',
        'back_text_align'  => 'center',
        'back_valign'      => 'center',
        'back_cta_text'    => 'Scopri di più',
        'back_cta_url'     => '',
        'back_cta_target'  => false,
        'back_cta_bg'      => '#FFFFFF',
        'back_cta_color'   => '#6366F1',
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
        'card_border_width'  => '0',
        'card_border_color'  => '#374151',
        'card_padding'       => '24',

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
        $radius   = intval( $s['card_border_radius'] );
        $shadow   = $this->get_shadow( $s['card_shadow'] );
        $bw       = intval( $s['card_border_width'] );
        $bc       = $this->safe_color( $s['card_border_color'] ) ?: '#374151';
        $padding  = intval( $s['card_padding'] );
        $halfH    = round( $height / 2 );

        // Tipografia
        $title_size   = intval( $s['title_size'] ) ?: 22;
        $title_weight = intval( $s['title_weight'] ) ?: 600;
        $desc_size    = intval( $s['desc_size'] ) ?: 14;

        // Transforms
        $back_initial  = $this->get_back_transform( $dir, $halfH );
        $flip_transform = $this->get_flip_transform( $dir, $halfH );

        // Front face
        $front_fg      = $this->safe_color( $s['front_text_color'] ) ?: '#F3F4F6';
        $front_bg      = $this->safe_color( $s['front_bg'] ) ?: '#1e1e2e';
        $front_align   = in_array( $s['front_text_align'], [ 'left', 'center', 'right' ] ) ? $s['front_text_align'] : 'center';
        $front_valign  = $this->valign_css( $s['front_valign'] );

        // Back face
        $back_fg       = $this->safe_color( $s['back_text_color'] ) ?: '#FFFFFF';
        $back_bg       = $this->safe_color( $s['back_bg'] ) ?: '#6366F1';
        $back_align    = in_array( $s['back_text_align'], [ 'left', 'center', 'right' ] ) ? $s['back_text_align'] : 'center';
        $back_valign   = $this->valign_css( $s['back_valign'] );

        // CTA
        $cta_text   = wp_kses_post( $s['back_cta_text'] );
        $cta_url    = esc_url( $s['back_cta_url'] );
        $cta_target = ! empty( $s['back_cta_target'] ) ? ' target="_blank" rel="noopener"' : '';
        $cta_bg     = $this->safe_color( $s['back_cta_bg'] ) ?: '#FFFFFF';
        $cta_color  = $this->safe_color( $s['back_cta_color'] ) ?: '#6366F1';
        $cta_radius = intval( $s['back_cta_radius'] );

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
                height: <?php echo $height; ?>px;
                <?php if ( $radius > 0 ) : ?>border-radius: <?php echo $radius; ?>px;<?php endif; ?>
                cursor: pointer;
            }
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
                <?php if ( $radius > 0 ) : ?>border-radius: <?php echo $radius; ?>px;<?php endif; ?>
                <?php if ( $shadow ) : ?>box-shadow: <?php echo $shadow; ?>;<?php endif; ?>
                <?php if ( $bw > 0 ) : ?>border: <?php echo $bw; ?>px solid <?php echo $bc; ?>;<?php endif; ?>
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
                padding: <?php echo $padding; ?>px;
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
                border-radius: <?php echo $cta_radius; ?>px;
                text-decoration: none !important;
                font-weight: 600;
                font-size: <?php echo $desc_size; ?>px;
                transition: opacity .2s, transform .2s;
            }
            .<?php echo $uid; ?> .olo-fc-cta:hover {
                opacity: .85;
                transform: translateY(-1px);
                color: <?php echo $cta_color; ?> !important;
                text-decoration: none !important;
            }

            @media (max-width: 767px) {
                .<?php echo $uid; ?>:hover .olo-fc-inner {
                    transform: none !important;
                }
                .<?php echo $uid; ?>.olo-fc-flipped .olo-fc-inner {
                    transform: <?php echo $flip_transform; ?> !important;
                }
            }
        </style>
        <div class="olo-fc <?php echo esc_attr( $uid ); ?>" data-trigger="<?php echo esc_attr( $trigger ); ?>">
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
            $html .= '<img class="olo-fc-bg" src="' . esc_url( $s[ $prefix . 'image' ] ) . '" alt="" loading="lazy" />';
        }

        // Background video
        if ( ! empty( $s[ $prefix . 'video' ] ) ) {
            $html .= '<video class="olo-fc-bg" autoplay muted loop playsinline><source src="' . esc_url( $s[ $prefix . 'video' ] ) . '" type="video/mp4"></video>';
        }

        // Overlay
        $overlay = $this->safe_color( $s[ $prefix . 'overlay' ] );
        if ( $overlay && ! empty( $s[ $prefix . 'image' ] ) ) {
            $html .= '<div class="olo-fc-overlay" style="background:' . $overlay . '"></div>';
        }

        // Content
        $html .= '<div class="olo-fc-content">';

        // Icon
        $icon = trim( $s[ $prefix . 'icon' ] ?? '' );
        if ( $icon ) {
            $icon_size  = intval( $s[ $prefix . 'icon_size' ] ) ?: 40;
            $icon_color = $this->safe_color( $s[ $prefix . 'icon_color' ] );
            $icon_style = "font-size:{$icon_size}px;";
            if ( $icon_color ) {
                $icon_style .= "color:{$icon_color};";
            }
            $html .= '<div class="olo-fc-icon"><span uk-icon="icon: ' . esc_attr( $icon ) . '; width: ' . $icon_size . '; height: ' . $icon_size . '" style="' . esc_attr( $icon_style ) . '"></span></div>';
        }

        // Title
        $title = $this->sanitize_richtext( $s[ $prefix . 'title' ] );
        if ( $title ) {
            $html .= '<div class="olo-fc-title">' . $title . '</div>';
        }

        // Description
        $desc = $this->sanitize_richtext( $s[ $prefix . 'description' ] );
        if ( $desc ) {
            $html .= '<div class="olo-fc-desc">' . $desc . '</div>';
        }

        // CTA (back only)
        if ( $side === 'back' && ! empty( $s['back_cta_text'] ) ) {
            $url    = esc_url( $s['back_cta_url'] );
            $target = ! empty( $s['back_cta_target'] ) ? ' target="_blank" rel="noopener"' : '';
            $tag    = $url ? 'a' : 'span';
            $href   = $url ? ' href="' . $url . '"' : '';
            $html  .= '<div><' . $tag . ' class="olo-fc-cta"' . $href . $target . '>' . wp_kses_post( $s['back_cta_text'] ) . '</' . $tag . '></div>';
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

    /**
     * Map shadow size to CSS box-shadow.
     */
    private function get_shadow( $size ) {
        $shadows = [
            'none' => '',
            'sm'   => '0 1px 2px rgba(0,0,0,0.05)',
            'md'   => '0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1)',
            'lg'   => '0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1)',
            'xl'   => '0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1)',
        ];
        return $shadows[ $size ] ?? $shadows['md'];
    }

}
