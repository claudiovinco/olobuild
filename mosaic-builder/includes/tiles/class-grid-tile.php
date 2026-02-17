<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Grid_Tile extends Olo_Tile_Base {

    protected $type     = 'grid';
    protected $name     = 'Griglia';
    protected $icon     = 'dashicons-grid-view';
    protected $category = 'content';
    protected $defaults = [
        'items'       => [
            [ 'title' => 'Item 1', 'content' => 'Description for item one.', 'image' => '', 'tag' => 'all' ],
            [ 'title' => 'Item 2', 'content' => 'Description for item two.', 'image' => '', 'tag' => 'all' ],
            [ 'title' => 'Item 3', 'content' => 'Description for item three.', 'image' => '', 'tag' => 'all' ],
        ],
        'columns'      => '3',
        'gap'          => 'default',
        'show_filter'  => false,
        'filter_style' => 'pills',
        'masonry'      => false,
        'card_style'   => 'default',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'items',       'type' => 'custom', 'label' => 'Items' ],
            [ 'key' => 'columns',     'type' => 'select', 'label' => 'Columns' ],
            [ 'key' => 'gap',         'type' => 'select', 'label' => 'Gap' ],
            [ 'key' => 'show_filter', 'type' => 'toggle', 'label' => 'Show Filter' ],
            [ 'key' => 'masonry',     'type' => 'toggle', 'label' => 'Masonry' ],
        ];
    }

    public function render( $settings ) {
        $s     = wp_parse_args( $settings, $this->defaults );
        $items = $this->parse_items( $s['items'] );
        $count = count( $items );

        if ( $count === 0 ) {
            return '';
        }

        $columns = absint( $s['columns'] ) ?: 3;
        if ( $columns > 6 ) {
            $columns = 6;
        }

        // Gap mapping
        $gap_map = [
            'collapse' => 'uk-grid-collapse',
            'small'    => 'uk-grid-small',
            'default'  => '',
            'medium'   => 'uk-grid-medium',
            'large'    => 'uk-grid-large',
        ];
        $gap_class = isset( $gap_map[ $s['gap'] ] ) ? $gap_map[ $s['gap'] ] : '';

        $masonry_attr = ! empty( $s['masonry'] ) ? 'masonry: true' : '';
        $uid          = 'mgrid-' . wp_rand( 10000, 99999 );

        // Collect unique tags for filter
        $tags = [];
        if ( ! empty( $s['show_filter'] ) ) {
            foreach ( $items as $item ) {
                $tag = ! empty( $item['tag'] ) ? sanitize_title( $item['tag'] ) : 'all';
                if ( $tag !== 'all' && ! in_array( $tag, $tags, true ) ) {
                    $tags[] = $tag;
                }
            }
        }

        ob_start();
        ?>
        <div class="olo-grid <?php echo esc_attr( $uid ); ?>"<?php if ( ! empty( $s['show_filter'] ) && ! empty( $tags ) ) : ?> uk-filter="target: .js-filter"<?php endif; ?>>

            <?php if ( ! empty( $s['show_filter'] ) && ! empty( $tags ) ) : ?>
                <?php if ( $s['filter_style'] === 'minimal' ) : ?>
                <div class="olo-filter-minimal">
                    <button class="olo-filter-minimal__btn olo-filter-minimal__btn--active" uk-filter-control>All</button>
                    <?php foreach ( $tags as $tag ) : ?>
                    <button class="olo-filter-minimal__btn" uk-filter-control=".tag-<?php echo esc_attr( $tag ); ?>"><?php echo esc_html( ucfirst( $tag ) ); ?></button>
                    <?php endforeach; ?>
                </div>
                <?php else : ?>
                <ul class="uk-subnav uk-subnav-pill">
                    <li class="uk-active" uk-filter-control><a href="#">All</a></li>
                    <?php foreach ( $tags as $tag ) : ?>
                    <li uk-filter-control=".tag-<?php echo esc_attr( $tag ); ?>"><a href="#"><?php echo esc_html( ucfirst( $tag ) ); ?></a></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            <?php endif; ?>

            <div class="js-filter uk-child-width-1-<?php echo esc_attr( $columns ); ?>@m <?php echo esc_attr( $gap_class ); ?>" uk-grid<?php if ( $masonry_attr ) : ?>="<?php echo esc_attr( $masonry_attr ); ?>"<?php endif; ?>>
                <?php foreach ( $items as $item ) :
                    $tag_class = '';
                    if ( ! empty( $s['show_filter'] ) && ! empty( $item['tag'] ) && $item['tag'] !== 'all' ) {
                        $tag_class = 'tag-' . sanitize_title( $item['tag'] );
                    }
                ?>
                <div<?php if ( $tag_class ) : ?> class="<?php echo esc_attr( $tag_class ); ?>"<?php endif; ?>>
                    <?php if ( $s['card_style'] === 'minimal' ) : ?>
                    <div class="olo-card-minimal">
                        <?php if ( ! empty( $item['image'] ) ) : ?>
                        <img class="olo-card-minimal__img" src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>">
                        <?php endif; ?>
                        <h3 class="olo-card-minimal__title"><?php echo wp_kses_post( $item['title'] ); ?></h3>
                        <p class="olo-card-minimal__text"><?php echo wp_kses_post( $item['content'] ); ?></p>
                    </div>
                    <?php else : ?>
                    <div class="uk-card uk-card-default uk-card-body">
                        <?php if ( ! empty( $item['image'] ) ) : ?>
                        <div class="uk-card-media-top">
                            <img src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>">
                        </div>
                        <?php endif; ?>
                        <h3 class="uk-card-title"><?php echo wp_kses_post( $item['title'] ); ?></h3>
                        <p><?php echo wp_kses_post( $item['content'] ); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Parse items from array or legacy format.
     */
    private function parse_items( $raw ) {
        if ( is_array( $raw ) ) {
            $items = [];
            foreach ( $raw as $item ) {
                if ( is_array( $item ) && ! empty( $item['title'] ) ) {
                    $items[] = [
                        'title'   => $item['title'],
                        'content' => $item['content'] ?? '',
                        'image'   => $item['image'] ?? '',
                        'tag'     => $item['tag'] ?? 'all',
                    ];
                }
            }
            return $items;
        }
        return [];
    }
}
