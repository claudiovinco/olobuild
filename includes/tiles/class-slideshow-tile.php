<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Slideshow_Tile extends Olo_Tile_Base {

    protected $type     = 'slideshow';
    protected $name     = 'Slideshow';
    protected $icon     = 'dashicons-slides';
    protected $category = 'media';
    protected $defaults = [
        'slides' => [
            [ 'id' => 's-1', 'image' => '', 'title' => 'Prima slide', 'subtitle' => 'Prima slide', 'link' => '' ],
        ],
        'autoplay'       => true,
        'autoplay_speed'  => '5000',
        'show_arrows'    => true,
        'show_dots'      => true,
        'slide_height'   => '400',
        'overlay_color'  => '#000000',
        'text_color'     => '#FFFFFF',
        'transition'     => 'slide',
        'shadow'         => 'none',
        'border_width'   => '0',
        'border_color'   => '',
        'border_radius'  => '0',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'autoplay',       'type' => 'toggle', 'label' => 'Autoplay' ],
            [ 'key' => 'autoplay_speed', 'type' => 'range',  'label' => 'Speed (ms)' ],
            [ 'key' => 'show_arrows',    'type' => 'toggle', 'label' => 'Show Arrows' ],
            [ 'key' => 'show_dots',      'type' => 'toggle', 'label' => 'Show Dots' ],
            [ 'key' => 'slide_height',   'type' => 'range',  'label' => 'Height (px)' ],
            [ 'key' => 'overlay_color',  'type' => 'color',  'label' => 'Overlay Color' ],
            [ 'key' => 'text_color',     'type' => 'color',  'label' => 'Text Color' ],
            [ 'key' => 'transition',     'type' => 'select', 'label' => 'Transition' ],
        ];
    }

    public function render( $settings ) {
        $s  = wp_parse_args( $settings, $this->defaults );
        $id = 'olo-ss-' . wp_unique_id();

        $slides = is_array( $s['slides'] ) ? $s['slides'] : [];
        if ( empty( $slides ) ) return '<div class="olo-slideshow" style="padding:40px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);">Nessuna slide aggiunta</div>';

        $h     = absint( $s['slide_height'] );
        $count = count( $slides );
        $speed = absint( $s['autoplay_speed'] );
        $transition = in_array( $s['transition'], [ 'slide', 'fade', 'scale' ], true ) ? $s['transition'] : 'slide';

        ob_start();
        ?>
        <div id="<?php echo esc_attr( $id ); ?>" class="olo-slideshow" uk-slideshow="autoplay: <?php echo $s['autoplay'] ? 'true' : 'false'; ?>; autoplay-interval: <?php echo $speed; ?>; animation: <?php echo esc_attr( $transition ); ?>" style="height:<?php echo $h; ?>px;">
            <div class="uk-slideshow-items" style="height:<?php echo $h; ?>px;">
                <?php foreach ( $slides as $slide ) : ?>
                    <div>
                        <?php if ( ! empty( $slide['image'] ) ) : ?>
                            <?php echo Olo_Tile_Utils::img_srcset( absint( $slide['image_id'] ?? 0 ), $slide['image'], $slide['title'] ?? '', '', 'full', 'uk-cover' ); ?>
                        <?php else : ?>
                            <div style="position:absolute;inset:0;background:var(--olo-color-secondary, #1F2937);" uk-cover></div>
                        <?php endif; ?>
                        <?php $sl_bg = $this->safe_color_css( $s['overlay_color'] ); $sl_fg = $this->safe_color_css( $s['text_color'] ); ?>
                        <div class="uk-position-cover" style="<?php if ( $sl_bg ) echo 'background:' . $sl_bg . ';'; ?>opacity:0.45;"></div>
                        <?php
                        list( $sst_cls, $sst_data ) = $this->tfx_attrs( $s, 'title', $slide['title'] ?? '' );
                        list( $sss_cls, $sss_data ) = $this->tfx_attrs( $s, 'subtitle', $slide['subtitle'] ?? '' );
                        ?>
                        <div class="uk-position-center uk-text-center" style="<?php if ( $sl_fg ) echo 'color:' . $sl_fg . ';'; ?>z-index:1;padding:24px;">
                            <?php if ( ! empty( $slide['title'] ) ) : ?>
                                <div class="olo-ss-title<?php echo $sst_cls; ?>" style="font-size:2em;font-weight:700;margin-bottom:8px;"<?php echo $sst_data; ?>><?php echo esc_html( $slide['title'] ); ?></div>
                            <?php endif; ?>
                            <?php if ( ! empty( $slide['subtitle'] ) ) : ?>
                                <div class="olo-ss-sub<?php echo $sss_cls; ?>" style="font-size:1.1em;opacity:0.85;"<?php echo $sss_data; ?>><?php echo esc_html( $slide['subtitle'] ); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ( $s['show_arrows'] && $count > 1 ) : ?>
                <a class="uk-slidenav-large uk-position-center-left uk-position-small" href uk-slidenav-previous uk-slideshow-item="previous"></a>
                <a class="uk-slidenav-large uk-position-center-right uk-position-small" href uk-slideshow-item="next" uk-slidenav-next></a>
            <?php endif; ?>

            <?php if ( $s['show_dots'] && $count > 1 ) : ?>
                <ul class="uk-slideshow-nav uk-dotnav uk-flex-center uk-margin"></ul>
            <?php endif; ?>
        </div>
        <?php
        $tfx_css = $this->tfx_css( $s, '#' . $id );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();
        return ob_get_clean();
    }
}
