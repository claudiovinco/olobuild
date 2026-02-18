<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_OverlaySlider_Tile extends Olo_Tile_Base {

    protected $type     = 'overlayslider';
    protected $name     = 'Overlay Slider';
    protected $icon     = 'dashicons-format-gallery';
    protected $category = 'content';
    protected $defaults = [
        'slides' => [
            [ 'id' => 'os-1', 'image' => '', 'title' => 'Slide 1', 'subtitle' => '', 'link' => '' ],
            [ 'id' => 'os-2', 'image' => '', 'title' => 'Slide 2', 'subtitle' => '', 'link' => '' ],
        ],
        'columns'             => '1',
        'height'              => '400',
        'overlay_position'    => 'bottom',
        'overlay_horizontal'  => 'left',
        'overlay_style'       => 'overlay-primary',
        'overlay_padding'     => 'medium',
        'title_size'          => 'h3',
        'hover_effect'        => 'none',
        'hover_overlay'       => 'always',
        'show_arrows'         => true,
        'show_dots'           => true,
        'ribbon_position'     => 'top-right',
        'ribbon_bg'           => '#e11d48',
        'ribbon_color'        => '#ffffff',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'slides',           'type' => 'slides', 'label' => 'Slides' ],
            [ 'key' => 'columns',          'type' => 'select', 'label' => 'Columns' ],
            [ 'key' => 'height',           'type' => 'range',  'label' => 'Height (px)' ],
            [ 'key' => 'overlay_position', 'type' => 'select', 'label' => 'Overlay Position' ],
            [ 'key' => 'overlay_style',    'type' => 'select', 'label' => 'Overlay Style' ],
            [ 'key' => 'show_arrows',      'type' => 'toggle', 'label' => 'Show Arrows' ],
            [ 'key' => 'show_dots',        'type' => 'toggle', 'label' => 'Show Dots' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $slides = is_array( $s['slides'] ) ? $s['slides'] : [];
        if ( empty( $slides ) ) {
            return '<div class="olo-overlayslider" style="padding:40px;text-align:center;color:#6b7280;">No slides added</div>';
        }

        $columns  = absint( $s['columns'] ) ?: 1;
        $height   = absint( $s['height'] ) ?: 400;
        $position = esc_attr( $s['overlay_position'] ?: 'bottom' );
        $style    = in_array( $s['overlay_style'], [ 'overlay-primary', 'overlay-default' ], true ) ? $s['overlay_style'] : 'overlay-primary';
        $count    = count( $slides );

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
        $ribbon_bg       = esc_attr( $s['ribbon_bg'] ?? '#e11d48' );
        $ribbon_color    = esc_attr( $s['ribbon_color'] ?? '#ffffff' );

        $uid = 'mos-os-' . wp_rand( 10000, 99999 );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> .mos-os-img { transition: transform 0.5s ease, filter 0.5s ease; width: 100%; height: <?php echo $height; ?>px; object-fit: cover; }
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
            .<?php echo $uid; ?> .mos-os-ribbon { position: absolute; z-index: 2; font-size: 11px; font-weight: 700; padding: 4px 12px; text-transform: uppercase; letter-spacing: 0.5px; background: <?php echo $ribbon_bg; ?>; color: <?php echo $ribbon_color; ?>; }
            .<?php echo $uid; ?> .mos-os-ribbon--top-right { top: 0; right: 14px; border-radius: 0 0 4px 4px; }
            .<?php echo $uid; ?> .mos-os-ribbon--top-left { top: 0; left: 14px; border-radius: 0 0 4px 4px; }
        </style>
        <div class="olo-overlayslider <?php echo $uid; ?>" uk-slider>
            <div class="uk-position-relative">
                <div class="uk-slider-container">
                    <ul class="uk-slider-items uk-child-width-1-<?php echo $columns; ?>@m">
                        <?php foreach ( $slides as $slide ) :
                            $has_link   = ! empty( $slide['link'] );
                            $link_url   = $has_link ? esc_url( $slide['link'] ) : '';
                            $toggle_cls = $needs_toggle ? ' uk-transition-toggle' : '';
                        ?>
                            <li>
                                <?php if ( $has_link ) : ?>
                                <a href="<?php echo $link_url; ?>" class="uk-link-reset uk-display-block<?php echo $toggle_cls; ?>" style="overflow:hidden;position:relative;" tabindex="0">
                                <?php else : ?>
                                <div class="uk-panel<?php echo $toggle_cls; ?>" style="overflow:hidden;" tabindex="0">
                                <?php endif; ?>
                                    <?php if ( ! empty( $slide['image'] ) ) : ?>
                                        <?php
                                        $os_img = '<img src="' . esc_url( $slide['image'] ) . '" alt="" class="' . $img_class . '">';
                                        echo $this->render_hover_wrap( $os_img, $slide['hover_image'] ?? '', $slide['hover_video'] ?? '' );
                                        ?>
                                    <?php else : ?>
                                        <div style="height:<?php echo $height; ?>px;background:#1f2937;width:100%;"></div>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $slide['ribbon'] ) ) : ?>
                                        <span class="mos-os-ribbon mos-os-ribbon--<?php echo $ribbon_position; ?>"><?php echo esc_html( $slide['ribbon'] ); ?></span>
                                    <?php endif; ?>
                                    <div class="uk-<?php echo esc_attr( $style ); ?> uk-position-<?php echo $position; ?> uk-panel<?php echo $text_class . $pad_class . $overlay_class; ?>">
                                        <<?php echo $title_tag; ?> class="uk-margin-remove"><?php echo esc_html( $slide['title'] ?? '' ); ?></<?php echo $title_tag; ?>>
                                        <?php if ( ! empty( $slide['subtitle'] ) ) : ?>
                                            <p class="uk-margin-small-top"><?php echo esc_html( $slide['subtitle'] ); ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php echo $has_link ? '</a>' : '</div>'; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <?php if ( ! empty( $s['show_arrows'] ) && $count > $columns ) : ?>
                    <a class="uk-position-center-left-out" href uk-slidenav-previous uk-slider-item="previous"></a>
                    <a class="uk-position-center-right-out" href uk-slidenav-next uk-slider-item="next"></a>
                <?php endif; ?>

                <?php if ( ! empty( $s['show_dots'] ) && $count > $columns ) : ?>
                    <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
