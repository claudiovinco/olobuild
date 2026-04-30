<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Popover_Tile extends Olo_Tile_Base {

    protected $type     = 'popover';
    protected $name     = 'Popover';
    protected $icon     = 'dashicons-location-alt';
    protected $category = 'interactive';
    protected $defaults = [
        'image'              => '',
        'markers'            => [
            [ 'id' => 'mk-1', 'x' => 25, 'y' => 30, 'title' => 'Point 1', 'content' => 'Description...', 'image' => '' ],
            [ 'id' => 'mk-2', 'x' => 70, 'y' => 60, 'title' => 'Point 2', 'content' => 'Description...', 'image' => '' ],
        ],
        'image_alt'          => '',
        'image_height'       => '0',
        'marker_color'       => '',
        'popup_bg'           => '#ffffff',
        'popup_color'        => '#333333',
        'popup_radius'       => '8',
        'popup_img_height'   => '120',
        'popup_hover_effect' => 'none',
        'popup_hover_color'  => '',
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $markers          = is_array( $s['markers'] ) ? $s['markers'] : [];
        $color            = $this->safe_color_css( $s['marker_color'] ?? '' ) ?: 'var(--olo-color-primary, #6366F1)';
        $image_height     = absint( $s['image_height'] ?? 0 );
        $popup_bg         = $this->safe_color_css( $s['popup_bg'] ?? '#ffffff' );
        $popup_color      = $this->safe_color_css( $s['popup_color'] ?? '#333333' );
        $popup_radius     = $this->build_border_radius_css( $s["popup_radius"] ?? 8 );
        $popup_radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['popup_radius_hover'] ?? null );
        $popup_img_height = absint( $s['popup_img_height'] ?? 120 );
        $hover_effect     = $s['popup_hover_effect'] ?? 'none';
        $hover_color      = $this->safe_color_css( $s['popup_hover_color'] ?? '' ) ?: 'var(--olo-color-primary, #6366F1)';

        $uid = 'olo-pop-' . wp_rand( 10000, 99999 );

        // Image style
        $img_style = 'width:100%;display:block;';
        if ( $image_height > 0 ) {
            $img_style .= 'height:' . $image_height . 'px;object-fit:cover;';
        }

        // Popup image top radius
        $img_top_radius = $popup_radius > 0 ? $popup_radius . 'px ' . $popup_radius . 'px 0 0' : '0';

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> .olo-popover-drop {
                background: <?php echo $popup_bg; ?>;
                color: <?php echo $popup_color; ?>;
                border-radius: <?php echo $popup_radius; ?>;
                box-shadow: 0 5px 20px rgba(0,0,0,0.15);
                overflow: hidden;
                min-width: 240px;
                max-width: 320px;
            }
            <?php if ( $popup_radius_hover_css !== '' ) : ?>.<?php echo $uid; ?> .olo-popover-drop{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?> .olo-popover-drop:hover{border-radius:<?php echo $popup_radius_hover_css; ?> !important}<?php endif; ?>
            .<?php echo $uid; ?> .olo-popover-drop h4 { color: <?php echo $popup_color; ?>; }
            .<?php echo $uid; ?> .olo-popover-drop__body { padding: 16px 20px; }
            .<?php echo $uid; ?> .olo-popover-drop__media {
                position: relative;
                overflow: hidden;
                height: <?php echo $popup_img_height; ?>px;
            }
            .<?php echo $uid; ?> .olo-popover-drop__img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
                transition: transform 0.5s ease, filter 0.5s ease;
            }
            <?php if ( $hover_effect === 'zoom' ) : ?>
            .<?php echo $uid; ?> .olo-popover-drop:hover .olo-popover-drop__img { transform: scale(1.08); }
            <?php elseif ( $hover_effect === 'zoom-rotate' ) : ?>
            .<?php echo $uid; ?> .olo-popover-drop:hover .olo-popover-drop__img { transform: scale(1.08) rotate(2deg); }
            <?php elseif ( $hover_effect === 'brightness' ) : ?>
            .<?php echo $uid; ?> .olo-popover-drop__img { filter: brightness(0.7); }
            .<?php echo $uid; ?> .olo-popover-drop:hover .olo-popover-drop__img { filter: brightness(1); }
            <?php elseif ( $hover_effect === 'desaturate' ) : ?>
            .<?php echo $uid; ?> .olo-popover-drop__img { filter: grayscale(100%); }
            .<?php echo $uid; ?> .olo-popover-drop:hover .olo-popover-drop__img { filter: grayscale(0%); }
            <?php elseif ( $hover_effect === 'blur-in' ) : ?>
            .<?php echo $uid; ?> .olo-popover-drop__img { filter: blur(3px); }
            .<?php echo $uid; ?> .olo-popover-drop:hover .olo-popover-drop__img { filter: blur(0); }
            <?php elseif ( $hover_effect === 'color-overlay' ) : ?>
            .<?php echo $uid; ?> .olo-popover-drop__media::after {
                content: '';
                position: absolute;
                inset: 0;
                background: <?php echo $hover_color; ?>;
                opacity: 0;
                transition: opacity 0.4s ease;
                mix-blend-mode: multiply;
                pointer-events: none;
            }
            .<?php echo $uid; ?> .olo-popover-drop:hover .olo-popover-drop__media::after { opacity: 0.45; }
            <?php endif; ?>
        </style>
        <div class="olo-popover uk-inline <?php echo $uid; ?>" style="width:100%;">
            <?php if ( ! empty( $s['image'] ) ) : ?>
                <?php echo Olo_Tile_Utils::img_srcset( absint( $s['image_id'] ?? 0 ), $s['image'], $s['image_alt'] ?? '', '', 'full', 'style="' . esc_attr( $img_style ) . '"' ); ?>
            <?php else : ?>
                <div style="width:100%;<?php echo $image_height > 0 ? 'height:' . $image_height . 'px;' : 'padding-bottom:56.25%;'; ?>background:var(--olo-color-secondary, #1F2937);"></div>
            <?php endif; ?>

            <?php foreach ( $markers as $i => $marker ) :
                $x = floatval( $marker['x'] ?? 50 );
                $y = floatval( $marker['y'] ?? 50 );
                $marker_img = $marker['image'] ?? '';
            ?>
                <a class="uk-position-absolute" href="#" style="left:<?php echo esc_attr( $x ); ?>%;top:<?php echo esc_attr( $y ); ?>%;transform:translate(-50%,-50%);width:20px;height:20px;border-radius:50%;background:<?php echo $color; ?>;display:block;box-shadow:0 0 0 3px rgba(255,255,255,0.4);" aria-label="<?php echo esc_attr( $marker['title'] ?? '' ); ?>"></a>
                <div uk-drop="mode: click; pos: top-center">
                    <div class="olo-popover-drop">
                        <?php if ( ! empty( $marker_img ) ) : ?>
                        <div class="olo-popover-drop__media">
                            <img src="<?php echo esc_url( $marker_img ); ?>" alt="<?php echo esc_attr( $marker['title'] ?? '' ); ?>" class="olo-popover-drop__img" loading="lazy">
                        </div>
                        <?php endif; ?>
                        <?php
                        list( $pvt_cls, $pvt_data ) = $this->tfx_attrs( $s, 'title', $marker['title'] ?? '' );
                        list( $pvc_cls, $pvc_data ) = $this->tfx_attrs( $s, 'content', wp_strip_all_tags( $marker['content'] ?? '' ) );
                        ?>
                        <div class="olo-popover-drop__body">
                            <?php if ( ! empty( $marker['title'] ) ) : ?>
                                <h4 class="uk-margin-small-bottom<?php echo $pvt_cls; ?>"<?php echo $pvt_data; ?>><?php echo esc_html( $marker['title'] ); ?></h4>
                            <?php endif; ?>
                            <?php if ( ! empty( $marker['content'] ) ) : ?>
                                <p class="uk-margin-remove<?php echo $pvc_cls; ?>"<?php echo $pvc_data; ?>><?php echo wp_kses_post( $marker['content'] ); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();
        return ob_get_clean();
    }
}
