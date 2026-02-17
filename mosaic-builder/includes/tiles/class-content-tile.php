<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Content_Tile extends Olo_Tile_Base {

    protected $type     = 'content';
    protected $name     = 'Contenuto';
    protected $icon     = 'dashicons-text-page';
    protected $category = 'content';
    protected $defaults = [
        'heading' => 'Section Title',
        'text'    => 'Add your content here. This is a simple text block that you can customize.',
        'image'   => '',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'heading', 'type' => 'text',     'label' => 'Heading' ],
            [ 'key' => 'text',    'type' => 'textarea', 'label' => 'Content' ],
            [ 'key' => 'image',   'type' => 'image',    'label' => 'Image' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );
        ob_start();
        ?>
        <div class="olo-content uk-panel" style="padding: 40px;">
            <?php if ( ! empty( $s['image'] ) ) : ?>
                <img src="<?php echo esc_url( $s['image'] ); ?>" alt="" style="max-width: 100%; height: auto; margin-bottom: 1em;" />
            <?php endif; ?>
            <h2 class="uk-heading-small" style="margin-bottom: 0.5em;"><?php echo wp_kses_post( $s['heading'] ); ?></h2>
            <div><?php echo wp_kses_post( $s['text'] ); ?></div>
        </div>
        <?php
        return ob_get_clean();
    }
}
