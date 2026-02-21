<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Icon_Tile extends Olo_Tile_Base {

    protected $type     = 'icon';
    protected $name     = 'Icona';
    protected $icon     = 'dashicons-star-filled';
    protected $category = 'content';
    protected $defaults = [
        'icon'        => 'star',
        'size'        => 40,
        'color'       => '',
        'link_url'    => '',
        'link_target' => '_self',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'icon',        'type' => 'text',   'label' => 'Icon Name (UIkit)' ],
            [ 'key' => 'size',        'type' => 'range',  'label' => 'Size (px)', 'min' => 16, 'max' => 120, 'step' => 4 ],
            [ 'key' => 'color',       'type' => 'color',  'label' => 'Color' ],
            [ 'key' => 'link_url',    'type' => 'text',   'label' => 'Link URL' ],
            [ 'key' => 'link_target', 'type' => 'select', 'label' => 'Link Target', 'options' => [
                '_self'  => 'Same Window',
                '_blank' => 'New Window',
            ]],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $icon_name = esc_attr( $s['icon'] );
        $size      = absint( $s['size'] );
        $ratio     = $size > 0 ? round( $size / 20, 2 ) : 2;
        $color     = ! empty( $s['color'] ) ? esc_attr( $s['color'] ) : '';
        $target    = $s['link_target'] === '_blank' ? ' target="_blank" rel="noopener"' : '';

        $icon_style = $color ? ' style="color:' . $color . ';"' : '';

        ob_start();
        ?>
        <div class="olo-icon uk-text-center">
            <?php if ( ! empty( $s['link_url'] ) ) : ?>
                <a href="<?php echo esc_url( $s['link_url'] ); ?>"<?php echo $target; ?>>
                    <span<?php echo $icon_style; ?> uk-icon="icon: <?php echo $icon_name; ?>; ratio: <?php echo $ratio; ?>"></span>
                </a>
            <?php else : ?>
                <span<?php echo $icon_style; ?> uk-icon="icon: <?php echo $icon_name; ?>; ratio: <?php echo $ratio; ?>"></span>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
