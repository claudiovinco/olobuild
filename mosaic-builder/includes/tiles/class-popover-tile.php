<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Popover_Tile extends Olo_Tile_Base {

    protected $type     = 'popover';
    protected $name     = 'Popover';
    protected $icon     = 'dashicons-location-alt';
    protected $category = 'content';
    protected $defaults = [
        'image'        => '',
        'markers'      => [
            [ 'id' => 'mk-1', 'x' => 25, 'y' => 30, 'title' => 'Point 1', 'content' => 'Description...' ],
            [ 'id' => 'mk-2', 'x' => 70, 'y' => 60, 'title' => 'Point 2', 'content' => 'Description...' ],
        ],
        'image_alt'    => '',
        'marker_color' => '#6366F1',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'image',        'type' => 'image',  'label' => 'Image' ],
            [ 'key' => 'markers',      'type' => 'panels', 'label' => 'Markers' ],
            [ 'key' => 'image_alt',    'type' => 'text',   'label' => 'Image Alt Text' ],
            [ 'key' => 'marker_color', 'type' => 'color',  'label' => 'Marker Color' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $markers = is_array( $s['markers'] ) ? $s['markers'] : [];
        $color   = esc_attr( $s['marker_color'] ?: '#6366F1' );

        ob_start();
        ?>
        <div class="olo-popover uk-inline" style="width:100%;">
            <?php if ( ! empty( $s['image'] ) ) : ?>
                <img src="<?php echo esc_url( $s['image'] ); ?>" alt="<?php echo esc_attr( $s['image_alt'] ); ?>" style="width:100%;display:block;">
            <?php else : ?>
                <div style="width:100%;padding-bottom:56.25%;background:#1f2937;"></div>
            <?php endif; ?>

            <?php foreach ( $markers as $i => $marker ) :
                $x = floatval( $marker['x'] ?? 50 );
                $y = floatval( $marker['y'] ?? 50 );
                $marker_id = 'olo-popover-drop-' . wp_unique_id();
            ?>
                <a class="uk-position-absolute" href="#" style="left:<?php echo esc_attr( $x ); ?>%;top:<?php echo esc_attr( $y ); ?>%;transform:translate(-50%,-50%);width:20px;height:20px;border-radius:50%;background:<?php echo $color; ?>;display:block;box-shadow:0 0 0 3px rgba(255,255,255,0.4);" aria-label="<?php echo esc_attr( $marker['title'] ?? '' ); ?>"></a>
                <div uk-drop="mode: click; pos: top-center">
                    <div class="uk-card uk-card-body uk-card-default uk-card-small uk-border-rounded">
                        <?php if ( ! empty( $marker['title'] ) ) : ?>
                            <h4 class="uk-margin-small-bottom"><?php echo esc_html( $marker['title'] ); ?></h4>
                        <?php endif; ?>
                        <?php if ( ! empty( $marker['content'] ) ) : ?>
                            <p class="uk-margin-remove"><?php echo wp_kses_post( $marker['content'] ); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
