<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Switcher_Tile extends Olo_Tile_Base {

    protected $type     = 'switcher';
    protected $name     = 'Switcher';
    protected $icon     = 'dashicons-welcome-widgets-menus';
    protected $category = 'content';
    protected $defaults = [
        'items'     => [
            [ 'title' => 'Tab 1', 'content' => 'Tab content for the first item.' ],
            [ 'title' => 'Tab 2', 'content' => 'Tab content for the second item.' ],
        ],
        'nav_style' => 'tab',
        'animation' => '',
        'vertical'  => false,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'items',     'type' => 'custom', 'label' => 'Items' ],
            [ 'key' => 'nav_style', 'type' => 'select', 'label' => 'Navigation Style' ],
            [ 'key' => 'animation', 'type' => 'select', 'label' => 'Animation' ],
            [ 'key' => 'vertical',  'type' => 'toggle', 'label' => 'Vertical' ],
        ];
    }

    public function render( $settings ) {
        $s     = wp_parse_args( $settings, $this->defaults );
        $items = $this->parse_items( $s['items'] );
        $count = count( $items );

        if ( $count === 0 ) {
            return '';
        }

        // Build switcher attribute
        $switcher_attr = '';
        if ( ! empty( $s['animation'] ) ) {
            $switcher_attr = 'animation: uk-animation-' . esc_attr( $s['animation'] );
        }

        // Determine nav tag/class
        $nav_style = $s['nav_style'] ?: 'tab';
        $vertical  = ! empty( $s['vertical'] );

        ob_start();

        if ( $vertical ) :
            // Vertical layout
            ?>
            <div class="olo-switcher" uk-grid>
                <div class="uk-width-auto">
                    <ul class="uk-tab-left" uk-tab="connect: .olo-switcher-content; <?php echo esc_attr( $switcher_attr ); ?>">
                        <?php foreach ( $items as $i => $item ) : ?>
                        <li<?php echo $i === 0 ? ' class="uk-active"' : ''; ?>><a href="#"><?php echo wp_kses_post( $item['title'] ); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="uk-width-expand">
                    <ul class="uk-switcher olo-switcher-content">
                        <?php foreach ( $items as $item ) : ?>
                        <li><?php echo wp_kses_post( $item['content'] ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php
        else :
            // Horizontal layout
            $nav_class = 'uk-' . esc_attr( $nav_style );
            // For subnav styles, the connect attribute goes on the nav element
            if ( $nav_style === 'tab' ) :
                ?>
                <div class="olo-switcher">
                    <ul class="<?php echo esc_attr( $nav_class ); ?>" uk-tab="connect: .olo-switcher-content; <?php echo esc_attr( $switcher_attr ); ?>">
                        <?php foreach ( $items as $i => $item ) : ?>
                        <li<?php echo $i === 0 ? ' class="uk-active"' : ''; ?>><a href="#"><?php echo wp_kses_post( $item['title'] ); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                    <ul class="uk-switcher olo-switcher-content">
                        <?php foreach ( $items as $item ) : ?>
                        <li><?php echo wp_kses_post( $item['content'] ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php
            else :
                // subnav / subnav-pill
                ?>
                <div class="olo-switcher">
                    <ul class="<?php echo esc_attr( $nav_class ); ?>" uk-switcher="<?php echo esc_attr( $switcher_attr ); ?>">
                        <?php foreach ( $items as $i => $item ) : ?>
                        <li<?php echo $i === 0 ? ' class="uk-active"' : ''; ?>><a href="#"><?php echo wp_kses_post( $item['title'] ); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                    <ul class="uk-switcher">
                        <?php foreach ( $items as $item ) : ?>
                        <li><?php echo wp_kses_post( $item['content'] ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php
            endif;
        endif;

        return ob_get_clean();
    }

    /**
     * Parse items from array format.
     */
    private function parse_items( $raw ) {
        if ( is_array( $raw ) ) {
            $items = [];
            foreach ( $raw as $item ) {
                if ( is_array( $item ) && ! empty( $item['title'] ) ) {
                    $items[] = [
                        'title'   => $item['title'],
                        'content' => $item['content'] ?? '',
                    ];
                }
            }
            return $items;
        }
        return [];
    }
}
