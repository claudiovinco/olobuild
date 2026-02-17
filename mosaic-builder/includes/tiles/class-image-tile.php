<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Image_Tile extends Olo_Tile_Base {

    protected $type     = 'image';
    protected $name     = 'Immagine';
    protected $icon     = 'dashicons-format-image';
    protected $category = 'media';
    protected $defaults = [
        'image_url'  => '',
        'alt_text'   => '',
        'caption'    => '',
        'link_url'   => '',
        'link_target' => '_self',
        'object_fit' => 'cover',
        'height'     => '300px',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'image_url',   'type' => 'image',  'label' => 'Image' ],
            [ 'key' => 'alt_text',    'type' => 'text',   'label' => 'Alt Text' ],
            [ 'key' => 'caption',     'type' => 'text',   'label' => 'Caption' ],
            [ 'key' => 'link_url',    'type' => 'text',   'label' => 'Link URL' ],
            [ 'key' => 'link_target', 'type' => 'select', 'label' => 'Link Target', 'options' => [ '_self' => 'Same Window', '_blank' => 'New Tab' ] ],
            [ 'key' => 'object_fit',  'type' => 'select', 'label' => 'Fit Mode', 'options' => [ 'cover' => 'Cover', 'contain' => 'Contain', 'fill' => 'Fill' ] ],
            [ 'key' => 'height',      'type' => 'text',   'label' => 'Height' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );
        ob_start();
        ?>
        <figure class="olo-image" style="margin: 0;">
            <?php
            $img = sprintf(
                '<img src="%s" alt="%s" uk-img class="uk-border-rounded" style="width: 100%%; height: %s; object-fit: %s; display: block;" />',
                esc_url( $s['image_url'] ),
                esc_attr( $s['alt_text'] ),
                esc_attr( $s['height'] ),
                esc_attr( $s['object_fit'] )
            );

            if ( ! empty( $s['link_url'] ) ) {
                printf(
                    '<a href="%s" target="%s" style="display: block;">%s</a>',
                    esc_url( $s['link_url'] ),
                    esc_attr( $s['link_target'] ),
                    $img
                );
            } else {
                echo $img;
            }
            ?>
            <?php if ( ! empty( $s['caption'] ) ) : ?>
                <figcaption style="padding: 8px 0; font-size: 0.875em; color: #6b7280; text-align: center;">
                    <?php echo wp_kses_post( $s['caption'] ); ?>
                </figcaption>
            <?php endif; ?>
        </figure>
        <?php
        return ob_get_clean();
    }
}
