<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Panel_Tile extends Olo_Tile_Base {

    protected $type     = 'panel';
    protected $name     = 'Pannello';
    protected $icon     = 'dashicons-id-alt';
    protected $category = 'content';
    protected $defaults = [
        'style'         => 'default',
        'title'         => 'Panel Title',
        'meta'          => 'Written by Author',
        'content'       => 'Panel content goes here. Add your text, images, or any other content.',
        'image'         => '',
        'link_url'      => '',
        'link_target'   => '_self',
        'title_element' => 'h3',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'style', 'type' => 'select', 'label' => 'Style', 'options' => [
                'default'   => 'Default',
                'primary'   => 'Primary',
                'secondary' => 'Secondary',
                'hover'     => 'Hover',
            ]],
            [ 'key' => 'image',         'type' => 'image',  'label' => 'Image' ],
            [ 'key' => 'title',         'type' => 'text',   'label' => 'Title' ],
            [ 'key' => 'meta',          'type' => 'text',   'label' => 'Meta' ],
            [ 'key' => 'content',       'type' => 'textarea', 'label' => 'Content' ],
            [ 'key' => 'link_url',      'type' => 'text',   'label' => 'Link URL' ],
            [ 'key' => 'link_target',   'type' => 'select', 'label' => 'Link Target', 'options' => [
                '_self'  => 'Same Window',
                '_blank' => 'New Window',
            ]],
            [ 'key' => 'title_element', 'type' => 'select', 'label' => 'Title Element', 'options' => [
                'h2'  => 'H2',
                'h3'  => 'H3',
                'h4'  => 'H4',
                'div' => 'DIV',
            ]],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $style      = esc_attr( $s['style'] );
        $card_class = "uk-card uk-card-{$style} uk-card-body";
        $tag        = in_array( $s['title_element'], [ 'h2', 'h3', 'h4', 'div' ], true ) ? $s['title_element'] : 'h3';
        $target     = $s['link_target'] === '_blank' ? ' target="_blank" rel="noopener"' : '';

        ob_start();
        ?>
        <div class="olo-panel <?php echo esc_attr( $card_class ); ?>">
            <?php if ( ! empty( $s['image'] ) ) : ?>
                <div class="uk-card-media-top">
                    <img src="<?php echo esc_url( $s['image'] ); ?>" alt="" style="width: 100%; display: block;" />
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $s['title'] ) ) : ?>
                <<?php echo $tag; ?> class="uk-card-title"><?php
                    if ( ! empty( $s['link_url'] ) ) {
                        echo '<a href="' . esc_url( $s['link_url'] ) . '"' . $target . '>';
                    }
                    echo wp_kses_post( $s['title'] );
                    if ( ! empty( $s['link_url'] ) ) {
                        echo '</a>';
                    }
                ?></<?php echo $tag; ?>>
            <?php endif; ?>

            <?php if ( ! empty( $s['meta'] ) ) : ?>
                <p class="uk-text-meta"><?php echo esc_html( $s['meta'] ); ?></p>
            <?php endif; ?>

            <?php if ( ! empty( $s['content'] ) ) : ?>
                <div><?php echo wp_kses_post( $s['content'] ); ?></div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
