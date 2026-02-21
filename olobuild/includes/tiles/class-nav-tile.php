<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Nav_Tile extends Olo_Tile_Base {

    protected $type     = 'nav';
    protected $name     = 'Nav';
    protected $icon     = 'dashicons-menu';
    protected $category = 'content';
    protected $defaults = [
        'items'      => [
            [ 'title' => 'Home', 'content' => '#', 'tag' => '' ],
            [ 'title' => 'About', 'content' => '#', 'tag' => '' ],
            [ 'title' => 'Contact', 'content' => '#', 'tag' => '' ],
        ],
        'style'      => 'default',
        'show_icons' => false,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'items',      'type' => 'custom', 'label' => 'Items' ],
            [ 'key' => 'style',      'type' => 'select', 'label' => 'Style' ],
            [ 'key' => 'show_icons', 'type' => 'toggle', 'label' => 'Show Icons' ],
        ];
    }

    public function render( $settings ) {
        $s     = wp_parse_args( $settings, $this->defaults );
        $items = $this->parse_items( $s['items'] );
        $count = count( $items );

        if ( $count === 0 ) {
            return '';
        }

        $style      = $s['style'] ?: 'default';
        $show_icons = ! empty( $s['show_icons'] );

        ob_start();
        ?>
        <ul class="uk-nav uk-nav-<?php echo esc_attr( $style ); ?>">
            <?php foreach ( $items as $item ) :
                $url   = ! empty( $item['url'] ) ? $item['url'] : '#';
                $label = ! empty( $item['label'] ) ? $item['label'] : '';
                $icon  = ! empty( $item['icon'] ) ? $item['icon'] : '';
            ?>
            <li>
                <a href="<?php echo esc_url( $url ); ?>">
                    <?php if ( $show_icons && $icon ) : ?>
                    <span uk-icon="icon: <?php echo esc_attr( $icon ); ?>"></span>
                    <?php endif; ?>
                    <?php echo esc_html( $label ); ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php
        return ob_get_clean();
    }

    /**
     * Parse items from array format.
     * Panel format uses title/content/tag mapped to label/url/icon.
     */
    private function parse_items( $raw ) {
        if ( is_array( $raw ) ) {
            $items = [];
            foreach ( $raw as $item ) {
                if ( is_array( $item ) ) {
                    $items[] = [
                        'label' => $item['title'] ?? ( $item['label'] ?? '' ),
                        'url'   => $item['content'] ?? ( $item['url'] ?? '#' ),
                        'icon'  => $item['tag'] ?? ( $item['icon'] ?? '' ),
                    ];
                }
            }
            return $items;
        }
        return [];
    }
}
