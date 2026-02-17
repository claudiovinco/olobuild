<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Testimonial_Tile extends Olo_Tile_Base {

    protected $type     = 'testimonial';
    protected $name     = 'Testimonianza';
    protected $icon     = 'dashicons-format-quote';
    protected $category = 'content';
    protected $defaults = [
        'quote'       => 'This is an amazing product! Highly recommended.',
        'author_name' => 'John Doe',
        'author_role' => 'CEO, Company',
        'avatar'      => '',
        'rating'      => '5',
        'bg_color'    => '#1F2937',
        'text_color'  => '#F3F4F6',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'quote',       'type' => 'textarea', 'label' => 'Quote' ],
            [ 'key' => 'author_name', 'type' => 'text',     'label' => 'Author Name' ],
            [ 'key' => 'author_role', 'type' => 'text',     'label' => 'Author Role' ],
            [ 'key' => 'avatar',      'type' => 'image',    'label' => 'Avatar' ],
            [ 'key' => 'rating',      'type' => 'select',   'label' => 'Rating', 'options' => [ '0' => 'None', '1' => '1 Star', '2' => '2 Stars', '3' => '3 Stars', '4' => '4 Stars', '5' => '5 Stars' ] ],
            [ 'key' => 'bg_color',    'type' => 'color',    'label' => 'Background Color' ],
            [ 'key' => 'text_color',  'type' => 'color',    'label' => 'Text Color' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $rating = absint( $s['rating'] );

        ob_start();
        ?>
        <div class="olo-testimonial uk-card uk-card-body uk-card-default" style="<?php echo $this->build_style( [ 'background' => $s['bg_color'], 'color' => $s['text_color'] ] ); ?>">
            <?php if ( $rating > 0 ) : ?>
                <div style="margin-bottom: 12px; font-size: 1.25em;">
                    <?php echo str_repeat( '&#11088;', $rating ); ?>
                </div>
            <?php endif; ?>
            <blockquote style="font-size: 1.1em; font-style: italic; margin: 0 0 20px 0; line-height: 1.6;">
                <?php echo wp_kses_post( $s['quote'] ); ?>
            </blockquote>
            <div class="uk-grid-small uk-flex-middle" uk-grid>
                <?php if ( ! empty( $s['avatar'] ) ) : ?>
                    <div class="uk-width-auto">
                        <img src="<?php echo esc_url( $s['avatar'] ); ?>" alt="" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;" />
                    </div>
                <?php endif; ?>
                <div class="uk-width-expand">
                    <div style="font-weight: 600;"><?php echo wp_kses_post( $s['author_name'] ); ?></div>
                    <?php if ( ! empty( $s['author_role'] ) ) : ?>
                        <div style="font-size: 0.875em; opacity: 0.7;"><?php echo wp_kses_post( $s['author_role'] ); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
