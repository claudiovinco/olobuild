<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Subnav_Tile extends Olo_Tile_Base {

    protected $type     = 'subnav';
    protected $name     = 'Subnav';
    protected $icon     = 'dashicons-ellipsis';
    protected $category = 'content';
    protected $defaults = [
        'items'   => [
            [ 'title' => 'Item 1', 'content' => '#' ],
            [ 'title' => 'Item 2', 'content' => '#' ],
            [ 'title' => 'Item 3', 'content' => '#' ],
        ],
        'style'   => 'default',
        'divider' => false,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'items',   'type' => 'custom', 'label' => 'Items' ],
            [ 'key' => 'style',   'type' => 'select', 'label' => 'Style' ],
            [ 'key' => 'divider', 'type' => 'toggle', 'label' => 'Divider' ],
        ];
    }

    public function render( $settings ) {
        $s     = wp_parse_args( $settings, $this->defaults );
        $items = $this->parse_items( $s['items'] );
        $count = count( $items );

        if ( $count === 0 ) {
            return '';
        }

        // Build class list
        $classes = 'uk-subnav';
        if ( $s['style'] === 'pill' ) {
            $classes .= ' uk-subnav-pill';
        }
        if ( ! empty( $s['divider'] ) ) {
            $classes .= ' uk-subnav-divider';
        }

        ob_start();
        ?>
        <ul class="<?php echo esc_attr( $classes ); ?>">
            <?php foreach ( $items as $item ) :
                $url   = ! empty( $item['url'] ) ? $item['url'] : '#';
                $label = ! empty( $item['label'] ) ? $item['label'] : '';
            ?>
            <li><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a></li>
            <?php endforeach; ?>
        </ul>
        <?php
        return ob_get_clean();
    }

    /**
     * Parse items from array format.
     * Panel format uses title/content mapped to label/url.
     */
    private function parse_items( $raw ) {
        if ( is_array( $raw ) ) {
            $items = [];
            foreach ( $raw as $item ) {
                if ( is_array( $item ) ) {
                    $items[] = [
                        'label' => $item['title'] ?? ( $item['label'] ?? '' ),
                        'url'   => $item['content'] ?? ( $item['url'] ?? '#' ),
                    ];
                }
            }
            return $items;
        }
        return [];
    }
}
