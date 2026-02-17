<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Gallery_Tile extends Olo_Tile_Base {

    protected $type     = 'gallery';
    protected $name     = 'Galleria';
    protected $icon     = 'dashicons-format-gallery';
    protected $category = 'media';
    protected $defaults = [
        'images'              => [],
        'columns'             => '3',
        'gap'                 => '8',
        'img_height'          => '200px',
        'object_fit'          => 'cover',
        'lightbox_animation'  => 'slide',
        'show_caption'        => false,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'images',     'type' => 'gallery', 'label' => 'Images' ],
            [ 'key' => 'columns',    'type' => 'select',  'label' => 'Columns', 'options' => [ '2' => '2', '3' => '3', '4' => '4', '5' => '5' ] ],
            [ 'key' => 'gap',        'type' => 'range',   'label' => 'Gap (px)', 'min' => 0, 'max' => 32, 'step' => 2 ],
            [ 'key' => 'img_height', 'type' => 'text',    'label' => 'Image Height' ],
            [ 'key' => 'object_fit', 'type' => 'select',  'label' => 'Fit Mode', 'options' => [ 'cover' => 'Cover', 'contain' => 'Contain' ] ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $images = is_array( $s['images'] ) ? $s['images'] : [];
        $gap_class = 'uk-grid-small';
        if ( absint( $s['gap'] ) > 16 ) {
            $gap_class = 'uk-grid-medium';
        } elseif ( absint( $s['gap'] ) === 0 ) {
            $gap_class = 'uk-grid-collapse';
        }

        $lb_animation = esc_attr( $s['lightbox_animation'] ?? 'slide' );
        $show_caption = ! empty( $s['show_caption'] );

        ob_start();
        ?>
        <div class="olo-gallery uk-grid uk-child-width-1-<?php echo absint( $s['columns'] ); ?>@m <?php echo esc_attr( $gap_class ); ?>" uk-grid uk-lightbox="animation: <?php echo $lb_animation; ?>">
            <?php if ( empty( $images ) ) : ?>
                <div class="uk-width-1-1" style="text-align: center; color: #9ca3af; padding: 40px;">
                    Add images to the gallery
                </div>
            <?php else : ?>
                <?php foreach ( $images as $img ) :
                    $url     = is_array( $img ) ? ( $img['url'] ?? '' ) : $img;
                    $alt     = is_array( $img ) ? ( $img['alt'] ?? '' ) : '';
                    $caption = is_array( $img ) ? ( $img['caption'] ?? '' ) : '';
                    $caption_attr = ( $show_caption && ! empty( $caption ) ) ? ' data-caption="' . esc_attr( $caption ) . '"' : '';
                    ?>
                    <div>
                        <a href="<?php echo esc_url( $url ); ?>"<?php echo $caption_attr; ?>>
                            <img
                                src="<?php echo esc_url( $url ); ?>"
                                alt="<?php echo esc_attr( $alt ); ?>"
                                style="
                                    width: 100%;
                                    height: <?php echo esc_attr( $s['img_height'] ); ?>;
                                    object-fit: <?php echo esc_attr( $s['object_fit'] ); ?>;
                                    border-radius: 4px;
                                    display: block;
                                "
                            />
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
