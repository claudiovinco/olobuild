<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Quotation_Tile extends Olo_Tile_Base {

    protected $type     = 'quotation';
    protected $name     = 'Citazione';
    protected $icon     = 'dashicons-format-quote';
    protected $category = 'text';
    protected $defaults = [
        'content'   => 'Life is what happens when you\'re busy making other plans.',
        'author'    => 'John Lennon',
        'style'     => 'default',
        'alignment' => 'left',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'content',   'type' => 'textarea', 'label' => 'Quote' ],
            [ 'key' => 'author',    'type' => 'text',     'label' => 'Author' ],
            [ 'key' => 'style',     'type' => 'select',   'label' => 'Style', 'options' => [
                'default' => 'Default',
                'footer'  => 'Footer Citation',
            ]],
            [ 'key' => 'alignment', 'type' => 'select',   'label' => 'Alignment', 'options' => [
                'left'   => 'Left',
                'center' => 'Center',
                'right'  => 'Right',
            ]],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $align_class = 'uk-text-' . esc_attr( $s['alignment'] );

        ob_start();
        ?>
        <blockquote class="olo-quotation <?php echo $align_class; ?>">
            <p><?php echo nl2br( esc_html( wp_strip_all_tags( $s['content'] ) ) ); ?></p>
            <?php if ( ! empty( $s['author'] ) ) : ?>
                <?php if ( $s['style'] === 'footer' ) : ?>
                    <footer><cite><?php echo esc_html( $s['author'] ); ?></cite></footer>
                <?php else : ?>
                    <p class="uk-text-meta">&mdash; <?php echo esc_html( $s['author'] ); ?></p>
                <?php endif; ?>
            <?php endif; ?>
        </blockquote>
        <?php
        return ob_get_clean();
    }
}
