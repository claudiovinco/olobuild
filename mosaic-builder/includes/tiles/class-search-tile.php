<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Search_Tile extends Olo_Tile_Base {

    protected $type     = 'search';
    protected $name     = 'Ricerca';
    protected $icon     = 'dashicons-search';
    protected $category = 'content';
    protected $defaults = [
        'placeholder' => 'Search...',
        'style'       => 'default',
        'size'        => '',
        'show_icon'   => true,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'placeholder', 'type' => 'text',   'label' => 'Placeholder' ],
            [ 'key' => 'style',       'type' => 'select', 'label' => 'Style' ],
            [ 'key' => 'size',        'type' => 'select', 'label' => 'Size' ],
            [ 'key' => 'show_icon',   'type' => 'toggle', 'label' => 'Show Icon' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $style       = in_array( $s['style'], [ 'default', 'large', 'navbar' ], true ) ? $s['style'] : 'default';
        $placeholder = esc_attr( $s['placeholder'] ?: 'Search...' );
        $size_class  = $s['size'] === 'large' ? ' uk-form-large' : '';
        $show_icon   = ! empty( $s['show_icon'] );

        ob_start();
        ?>
        <form class="olo-search uk-search uk-search-<?php echo esc_attr( $style ); ?>" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
            <?php if ( $show_icon ) : ?>
                <span class="uk-search-icon" uk-search-icon></span>
            <?php endif; ?>
            <input class="uk-search-input<?php echo esc_attr( $size_class ); ?>" type="search" name="s" placeholder="<?php echo $placeholder; ?>" aria-label="Search">
        </form>
        <?php
        return ob_get_clean();
    }
}
