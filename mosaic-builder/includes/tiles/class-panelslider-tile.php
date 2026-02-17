<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_PanelSlider_Tile extends Olo_Tile_Base {

    protected $type     = 'panelslider';
    protected $name     = 'Panel Slider';
    protected $icon     = 'dashicons-slides';
    protected $category = 'content';
    protected $defaults = [
        'panels' => [
            [ 'id' => 'ps-1', 'title' => 'Card 1', 'content' => 'Content...', 'image' => '' ],
            [ 'id' => 'ps-2', 'title' => 'Card 2', 'content' => 'Content...', 'image' => '' ],
            [ 'id' => 'ps-3', 'title' => 'Card 3', 'content' => 'Content...', 'image' => '' ],
        ],
        'columns'            => '3',
        'gap'                => 'default',
        'card_style'         => 'default',
        'show_arrows'        => true,
        'autoplay'           => false,
        'autoplay_interval'  => '5000',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'panels',            'type' => 'panels', 'label' => 'Panels' ],
            [ 'key' => 'columns',           'type' => 'select', 'label' => 'Columns' ],
            [ 'key' => 'gap',               'type' => 'select', 'label' => 'Gap' ],
            [ 'key' => 'card_style',        'type' => 'select', 'label' => 'Card Style' ],
            [ 'key' => 'show_arrows',       'type' => 'toggle', 'label' => 'Show Arrows' ],
            [ 'key' => 'autoplay',          'type' => 'toggle', 'label' => 'Autoplay' ],
            [ 'key' => 'autoplay_interval', 'type' => 'range',  'label' => 'Autoplay Interval (ms)' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $panels = is_array( $s['panels'] ) ? $s['panels'] : [];
        if ( empty( $panels ) ) {
            return '<div class="olo-panelslider" style="padding:40px;text-align:center;color:#6b7280;">No panels added</div>';
        }

        $columns  = absint( $s['columns'] ) ?: 3;
        $gap      = in_array( $s['gap'], [ 'collapse', 'small', 'default', 'medium', 'large' ], true ) ? $s['gap'] : 'default';
        $style    = in_array( $s['card_style'], [ 'default', 'primary', 'secondary', 'hover' ], true ) ? $s['card_style'] : 'default';
        $autoplay = ! empty( $s['autoplay'] ) ? 'true' : 'false';
        $interval = absint( $s['autoplay_interval'] ) ?: 5000;

        $gap_class = $gap === 'collapse' ? 'uk-grid-collapse' : 'uk-grid-' . $gap;

        ob_start();
        ?>
        <div class="olo-panelslider" uk-slider="autoplay: <?php echo $autoplay; ?>; autoplay-interval: <?php echo $interval; ?>">
            <div class="uk-position-relative">
                <div class="uk-slider-container">
                    <ul class="uk-slider-items uk-child-width-1-<?php echo $columns; ?>@m uk-grid <?php echo esc_attr( $gap_class ); ?>">
                        <?php foreach ( $panels as $panel ) : ?>
                            <li>
                                <div class="uk-card uk-card-<?php echo esc_attr( $style ); ?> uk-card-body">
                                    <?php if ( ! empty( $panel['image'] ) ) : ?>
                                        <div class="uk-card-media-top">
                                            <img src="<?php echo esc_url( $panel['image'] ); ?>" alt="" uk-img>
                                        </div>
                                    <?php endif; ?>
                                    <h3 class="uk-card-title"><?php echo esc_html( $panel['title'] ?? '' ); ?></h3>
                                    <p><?php echo wp_kses_post( $panel['content'] ?? '' ); ?></p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <?php if ( ! empty( $s['show_arrows'] ) && count( $panels ) > $columns ) : ?>
                    <a class="uk-position-center-left-out" href uk-slidenav-previous uk-slider-item="previous"></a>
                    <a class="uk-position-center-right-out" href uk-slidenav-next uk-slider-item="next"></a>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
