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
        'image_url'   => '',
        'hover_image' => '',
        'hover_video' => '',
        'alt_text'    => '',
        'caption'     => '',
        'link_url'    => '',
        'link_target' => '_self',
        'object_fit'  => 'cover',
        'height'      => '300px',
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
            $att_id = absint( $s['image_url_id'] ?? 0 );
            $extra  = 'uk-img style="width: 100%; height: ' . esc_attr( $s['height'] ) . '; object-fit: ' . esc_attr( $s['object_fit'] ) . '; display: block;"';
            $img    = Olo_Tile_Utils::img_srcset( $att_id, $s['image_url'], $s['alt_text'], 'uk-border-rounded', 'full', $extra );

            $img = $this->render_hover_wrap( $img, $s['hover_image'] ?? '', $s['hover_video'] ?? '' );

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
