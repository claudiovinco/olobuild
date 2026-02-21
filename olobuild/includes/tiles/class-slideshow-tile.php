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
            [ 'id' => 's-1', 'image' => '', 'title' => 'Slide One', 'subtitle' => 'First slide description', 'link' => '' ],
            [ 'id' => 's-2', 'image' => '', 'title' => 'Slide Two', 'subtitle' => 'Second slide description', 'link' => '' ],
            [ 'id' => 's-3', 'image' => '', 'title' => 'Slide Three', 'subtitle' => 'Third slide description', 'link' => '' ],
        ],
        'autoplay'       => true,
        'autoplay_speed'  => '5000',
        'show_arrows'    => true,
        'show_dots'      => true,
        'slide_height'   => '400',
        'overlay_color'  => '#000000',
        'text_color'     => '#FFFFFF',
        'transition'     => 'slide',
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
        if ( empty( $slides ) ) return '<div class="olo-slideshow" style="padding:40px;text-align:center;color:#6b7280;">No slides added</div>';

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
                            <img src="<?php echo esc_url( $slide['image'] ); ?>" alt="" uk-cover>
                        <?php else : ?>
                            <div style="position:absolute;inset:0;background:#1f2937;" uk-cover></div>
                        <?php endif; ?>
                        <?php $sl_bg = $this->safe_color( $s['overlay_color'] ); $sl_fg = $this->safe_color( $s['text_color'] ); ?>
                        <div class="uk-position-cover" style="<?php if ( $sl_bg ) echo 'background:' . $sl_bg . ';'; ?>opacity:0.45;"></div>
                        <div class="uk-position-center uk-text-center" style="<?php if ( $sl_fg ) echo 'color:' . $sl_fg . ';'; ?>z-index:1;padding:24px;">
                            <?php if ( ! empty( $slide['title'] ) ) : ?>
                                <div style="font-size:2em;font-weight:700;margin-bottom:8px;"><?php echo esc_html( $slide['title'] ); ?></div>
                            <?php endif; ?>
                            <?php if ( ! empty( $slide['subtitle'] ) ) : ?>
                                <div style="font-size:1.1em;opacity:0.85;"><?php echo esc_html( $slide['subtitle'] ); ?></div>
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
        return ob_get_clean();
    }
}
