<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_PostGrid_Tile extends Olo_Tile_Base {

    protected $type     = 'postgrid';
    protected $name     = 'Griglia articoli';
    protected $icon     = 'dashicons-grid-view';
    protected $category = 'dynamic';
    protected $defaults = [
        'post_type'       => 'post',
        'posts_per_page'  => '12',
        'orderby'         => 'date',
        'order'           => 'DESC',
        'meta_key'        => '',
        'taxonomy'        => '',
        'show_filters'    => false,
        'filter_style'    => 'pills',
        'filter_align'    => 'left',
        'show_sort'       => false,
        'sort_options'    => 'date|title',
        'columns'         => '3',
        'columns_mobile'  => '1',
        'gap'             => 'medium',
        'match_height'    => false,
        'masonry'         => false,
        'card_style'      => 'default',
        'card_primary_bg' => '#6366F1',
        'image_height'    => '200',
        'image_radius'    => '0',
        'card_radius'     => '4',
        'show_image'      => true,
        'show_category'   => true,
        'show_excerpt'    => true,
        'excerpt_length'  => '20',
        'show_meta'       => true,
        'show_price'      => false,
        'price_field'     => 'rental_price_night',
        'price_prefix'    => '€',
        'price_suffix'    => '/notte',
        'link_text'       => 'Vedi',
        'link_style'      => 'button',
        'hover_effect'    => 'none',
        'ribbon_field'    => '',
        'ribbon_position' => 'top-right',
        'ribbon_bg'       => '#e11d48',
        'ribbon_color'    => '#ffffff',
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $post_type = sanitize_key( $s['post_type'] );
        if ( ! post_type_exists( $post_type ) ) {
            return '<p style="color:#999;text-align:center;">Tipo di contenuto "' . esc_html( $post_type ) . '" non trovato.</p>';
        }

        // Query
        $query_args = [
            'post_type'      => $post_type,
            'posts_per_page' => min( 100, absint( $s['posts_per_page'] ) ),
            'post_status'    => 'publish',
            'no_found_rows'  => true,
            'orderby'        => sanitize_key( $s['orderby'] ),
            'order'          => strtoupper( $s['order'] ) === 'ASC' ? 'ASC' : 'DESC',
        ];

        if ( in_array( $s['orderby'], [ 'meta_value_num', 'meta_value' ], true ) && ! empty( $s['meta_key'] ) ) {
            $query_args['meta_key'] = sanitize_key( $s['meta_key'] );
        }

        $query = new WP_Query( $query_args );

        if ( ! $query->have_posts() ) {
            wp_reset_postdata();
            return '<p style="color:#999;text-align:center;">Nessun articolo trovato.</p>';
        }

        // Collect posts data
        $taxonomy = sanitize_key( $s['taxonomy'] );
        $posts    = [];

        foreach ( $query->posts as $post ) {
            $item = [
                'id'        => $post->ID,
                'title'     => get_the_title( $post ),
                'url'       => get_permalink( $post->ID ),
                'date'      => get_the_date( 'Y-m-d', $post ),
                'date_fmt'  => get_the_date( '', $post ),
                'author'    => get_the_author_meta( 'display_name', $post->post_author ),
            ];

            // Image
            if ( ! empty( $s['show_image'] ) ) {
                $item['image'] = get_the_post_thumbnail_url( $post->ID, 'medium_large' ) ?: '';
            }

            // Excerpt
            if ( ! empty( $s['show_excerpt'] ) ) {
                $excerpt_length = absint( $s['excerpt_length'] ) ?: 20;
                $item['excerpt'] = has_excerpt( $post->ID )
                    ? wp_trim_words( get_the_excerpt( $post->ID ), $excerpt_length, '&hellip;' )
                    : wp_trim_words( $post->post_content, $excerpt_length, '&hellip;' );
            }

            // Price
            if ( ! empty( $s['show_price'] ) && ! empty( $s['price_field'] ) ) {
                $price_val = get_post_meta( $post->ID, sanitize_key( $s['price_field'] ), true );
                if ( $price_val !== '' && $price_val !== false ) {
                    $item['price'] = is_numeric( $price_val ) ? floatval( $price_val ) : $price_val;
                }
            }

            // Ribbon
            if ( ! empty( $s['ribbon_field'] ) ) {
                $ribbon_val = get_post_meta( $post->ID, sanitize_key( $s['ribbon_field'] ), true );
                if ( ! empty( $ribbon_val ) ) {
                    $item['ribbon'] = $ribbon_val;
                }
            }

            // Taxonomy terms
            if ( $taxonomy && taxonomy_exists( $taxonomy ) ) {
                $post_terms = get_the_terms( $post->ID, $taxonomy );
                if ( $post_terms && ! is_wp_error( $post_terms ) ) {
                    $item['terms']      = wp_list_pluck( $post_terms, 'slug' );
                    $item['term_names'] = wp_list_pluck( $post_terms, 'name' );
                } else {
                    $item['terms']      = [];
                    $item['term_names'] = [];
                }
            }

            $posts[] = $item;
        }

        wp_reset_postdata();

        // Taxonomy terms for filters
        $terms = [];
        if ( ! empty( $s['show_filters'] ) && $taxonomy && taxonomy_exists( $taxonomy ) ) {
            $terms = $this->get_taxonomy_terms( $taxonomy );
        }

        // Grid classes
        $gap_map = [
            'collapse' => 'uk-grid-collapse',
            'small'    => 'uk-grid-small',
            'default'  => '',
            'medium'   => 'uk-grid-medium',
            'large'    => 'uk-grid-large',
        ];
        $gap_class = $gap_map[ $s['gap'] ] ?? '';
        $columns   = max( 1, min( 6, absint( $s['columns'] ) ) );
        $cols_mob  = max( 1, min( 2, absint( $s['columns_mobile'] ?? 1 ) ) );

        // Card style
        $card_style_map = [
            'default' => 'uk-card-default',
            'hover'   => 'uk-card-default uk-card-hover',
            'primary' => 'uk-card-primary',
            'minimal' => 'minimal',
        ];
        $card_class    = $card_style_map[ $s['card_style'] ] ?? 'uk-card-default';
        $is_minimal_card = $s['card_style'] === 'minimal';

        $uid          = 'olo-postgrid-' . wp_rand( 10000, 99999 );
        $image_height = absint( $s['image_height'] ) ?: 200;
        $image_radius = absint( $s['image_radius'] ?? 0 );
        $card_radius  = absint( $s['card_radius'] ?? 4 );

        // Sort config for JS
        $sort_enabled = ! empty( $s['show_sort'] );
        $config       = [
            'sortEnabled' => $sort_enabled,
        ];

        // Hover effect
        $hover_effect = $s['hover_effect'] ?? 'none';
        $img_class    = 'olo-pg-img';
        if ( $hover_effect !== 'none' ) {
            $img_class .= ' olo-pg-hover-' . esc_attr( $hover_effect );
        }

        // Ribbon
        $ribbon_field    = $s['ribbon_field'] ?? '';
        $ribbon_position = $s['ribbon_position'] ?? 'top-right';
        $ribbon_bg       = $s['ribbon_bg'] ?? '#e11d48';
        $ribbon_color    = $s['ribbon_color'] ?? '#ffffff';

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> .olo-pg-img { transition: transform 0.5s ease, filter 0.5s ease; width: 100%; height: <?php echo $image_height; ?>px; object-fit: cover; display: block; border-radius: <?php echo $image_radius; ?>px; }
            .<?php echo $uid; ?> .olo-card-minimal__img { border-radius: <?php echo $image_radius; ?>px; }
            .<?php echo $uid; ?> .uk-card-media-top { border-radius: <?php echo $image_radius; ?>px <?php echo $image_radius; ?>px 0 0; overflow: hidden; }
            .<?php echo $uid; ?> .uk-card { border-radius: <?php echo $card_radius; ?>px; overflow: hidden; }
            .<?php echo $uid; ?> .olo-card-minimal { border-radius: <?php echo $card_radius; ?>px; overflow: hidden; }
            .<?php echo $uid; ?> .uk-card:hover .olo-pg-hover-zoom, .<?php echo $uid; ?> .olo-card-minimal:hover .olo-pg-hover-zoom { transform: scale(1.08); }
            .<?php echo $uid; ?> .uk-card:hover .olo-pg-hover-zoom-rotate, .<?php echo $uid; ?> .olo-card-minimal:hover .olo-pg-hover-zoom-rotate { transform: scale(1.08) rotate(2deg); }
            .<?php echo $uid; ?> .olo-pg-hover-brightness { filter: brightness(0.7); }
            .<?php echo $uid; ?> .uk-card:hover .olo-pg-hover-brightness, .<?php echo $uid; ?> .olo-card-minimal:hover .olo-pg-hover-brightness { filter: brightness(1); }
            .<?php echo $uid; ?> .olo-pg-hover-desaturate { filter: grayscale(100%); }
            .<?php echo $uid; ?> .uk-card:hover .olo-pg-hover-desaturate, .<?php echo $uid; ?> .olo-card-minimal:hover .olo-pg-hover-desaturate { filter: grayscale(0%); }
            .<?php echo $uid; ?> .olo-pg-hover-blur-in { filter: blur(3px); }
            .<?php echo $uid; ?> .uk-card:hover .olo-pg-hover-blur-in, .<?php echo $uid; ?> .olo-card-minimal:hover .olo-pg-hover-blur-in { filter: blur(0); }
            .<?php echo $uid; ?> .olo-pg-ribbon { position: absolute; z-index: 2; font-size: 11px; font-weight: 700; padding: 4px 12px; text-transform: uppercase; letter-spacing: 0.5px; background: <?php echo esc_attr( $ribbon_bg ); ?>; color: <?php echo esc_attr( $ribbon_color ); ?>; }
            .<?php echo $uid; ?> .olo-pg-ribbon--top-right { top: 0; right: 14px; border-radius: 0 0 4px 4px; }
            .<?php echo $uid; ?> .olo-pg-ribbon--top-left { top: 0; left: 14px; border-radius: 0 0 4px 4px; }
            <?php if ( ! empty( $s['match_height'] ) ) : ?>
            .<?php echo $uid; ?> .olo-postgrid-item > .uk-card { height: 100%; display: flex; flex-direction: column; }
            .<?php echo $uid; ?> .olo-postgrid-item > .uk-card > .uk-card-body { flex: 1; }
            <?php endif; ?>
            <?php if ( $s['card_style'] === 'primary' ) :
                $primary_bg = esc_attr( $s['card_primary_bg'] ?? '#6366F1' );
            ?>
            .<?php echo $uid; ?> .uk-card-primary { background-color: <?php echo $primary_bg; ?> !important; }
            .<?php echo $uid; ?> .uk-card-primary .uk-card-title,
            .<?php echo $uid; ?> .uk-card-primary .uk-card-body,
            .<?php echo $uid; ?> .uk-card-primary .olo-postgrid-meta,
            .<?php echo $uid; ?> .uk-card-primary .olo-postgrid-excerpt,
            .<?php echo $uid; ?> .uk-card-primary .olo-postgrid-price,
            .<?php echo $uid; ?> .uk-card-primary .olo-postgrid-link { color: #fff !important; }
            .<?php echo $uid; ?> .uk-card-primary .uk-button-primary { background: rgba(255,255,255,0.2); border-color: #fff; }
            <?php endif; ?>
        </style>
        <div class="olo-postgrid <?php echo $uid; ?>" id="<?php echo esc_attr( $uid ); ?>"
             data-postgrid-config="<?php echo esc_attr( wp_json_encode( $config ) ); ?>">

            <?php if ( ! empty( $terms ) || $sort_enabled ) : ?>
            <div class="olo-postgrid-toolbar">
                <?php if ( ! empty( $terms ) ) : ?>
                    <?php $this->render_filters( $terms, $s['filter_style'], $uid, $s['filter_align'] ?? 'left' ); ?>
                <?php endif; ?>

                <?php if ( $sort_enabled ) : ?>
                    <?php $this->render_sort_select( $s ); ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="olo-postgrid-grid uk-child-width-1-<?php echo $cols_mob; ?> uk-child-width-1-<?php echo $columns; ?>@m <?php echo esc_attr( $gap_class ); ?>" uk-grid<?php if ( ! empty( $s['masonry'] ) ) echo '="masonry: true"'; ?> <?php if ( ! empty( $s['match_height'] ) ) echo 'uk-height-match'; ?>>
                <?php foreach ( $posts as $item ) : ?>
                <div class="olo-postgrid-item"
                     <?php if ( ! empty( $item['terms'] ) ) : ?>data-terms="<?php echo esc_attr( implode( ',', $item['terms'] ) ); ?>"<?php endif; ?>
                     data-price="<?php echo esc_attr( $item['price'] ?? '0' ); ?>"
                     data-date="<?php echo esc_attr( $item['date'] ); ?>"
                     data-title="<?php echo esc_attr( $item['title'] ); ?>">
                    <?php if ( $is_minimal_card ) : ?>
                    <div class="olo-card-minimal">
                        <?php if ( ! empty( $s['show_image'] ) && ! empty( $item['image'] ) ) : ?>
                        <div class="olo-card-minimal__media" style="position:relative;overflow:hidden;">
                            <?php if ( $s['link_style'] === 'card' ) : ?>
                                <a href="<?php echo esc_url( $item['url'] ); ?>">
                            <?php endif; ?>
                            <img src="<?php echo esc_url( $item['image'] ); ?>"
                                 alt="<?php echo esc_attr( $item['title'] ); ?>"
                                 class="olo-card-minimal__img <?php echo esc_attr( $img_class ); ?>"
                                 loading="lazy">
                            <?php if ( $s['link_style'] === 'card' ) : ?>
                                </a>
                            <?php endif; ?>
                            <?php if ( ! empty( $s['show_category'] ) && ! empty( $item['term_names'] ) ) : ?>
                                <span class="olo-postgrid-badge"><?php echo esc_html( $item['term_names'][0] ); ?></span>
                            <?php endif; ?>
                            <?php if ( ! empty( $item['ribbon'] ) ) : ?>
                                <span class="olo-pg-ribbon olo-pg-ribbon--<?php echo esc_attr( $ribbon_position ); ?>"><?php echo esc_html( $item['ribbon'] ); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <h3 class="olo-card-minimal__title">
                            <?php if ( $s['link_style'] === 'card' ) : ?>
                                <a href="<?php echo esc_url( $item['url'] ); ?>" class="uk-link-reset"><?php echo esc_html( $item['title'] ); ?></a>
                            <?php else : ?>
                                <?php echo esc_html( $item['title'] ); ?>
                            <?php endif; ?>
                        </h3>

                        <?php if ( ! empty( $s['show_meta'] ) ) : ?>
                        <div class="olo-postgrid-meta">
                            <?php echo esc_html( $item['date_fmt'] ); ?> &middot; <?php echo esc_html( $item['author'] ); ?>
                        </div>
                        <?php endif; ?>

                        <?php if ( ! empty( $s['show_excerpt'] ) && ! empty( $item['excerpt'] ) ) : ?>
                        <p class="olo-card-minimal__text"><?php echo wp_kses_post( $item['excerpt'] ); ?></p>
                        <?php endif; ?>

                        <?php if ( ! empty( $s['show_price'] ) && isset( $item['price'] ) ) : ?>
                        <div class="olo-postgrid-price">
                            <?php echo esc_html( $s['price_prefix'] . $item['price'] . $s['price_suffix'] ); ?>
                        </div>
                        <?php endif; ?>

                        <?php if ( $s['link_style'] === 'button' ) : ?>
                            <a href="<?php echo esc_url( $item['url'] ); ?>" class="uk-button uk-button-primary uk-button-small"><?php echo esc_html( $s['link_text'] ); ?></a>
                        <?php elseif ( $s['link_style'] === 'text' ) : ?>
                            <a href="<?php echo esc_url( $item['url'] ); ?>" class="olo-postgrid-link"><?php echo esc_html( $s['link_text'] ); ?> &rarr;</a>
                        <?php endif; ?>
                    </div>
                    <?php else : ?>
                    <div class="uk-card <?php echo esc_attr( $card_class ); ?>">
                        <?php if ( ! empty( $s['show_image'] ) && ! empty( $item['image'] ) ) : ?>
                        <div class="uk-card-media-top" style="position:relative;overflow:hidden;">
                            <?php if ( $s['link_style'] === 'card' ) : ?>
                                <a href="<?php echo esc_url( $item['url'] ); ?>">
                            <?php endif; ?>
                            <img src="<?php echo esc_url( $item['image'] ); ?>"
                                 alt="<?php echo esc_attr( $item['title'] ); ?>"
                                 class="<?php echo esc_attr( $img_class ); ?>"
                                 loading="lazy">
                            <?php if ( $s['link_style'] === 'card' ) : ?>
                                </a>
                            <?php endif; ?>
                            <?php if ( ! empty( $s['show_category'] ) && ! empty( $item['term_names'] ) ) : ?>
                                <span class="olo-postgrid-badge"><?php echo esc_html( $item['term_names'][0] ); ?></span>
                            <?php endif; ?>
                            <?php if ( ! empty( $item['ribbon'] ) ) : ?>
                                <span class="olo-pg-ribbon olo-pg-ribbon--<?php echo esc_attr( $ribbon_position ); ?>"><?php echo esc_html( $item['ribbon'] ); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <div class="uk-card-body">
                            <h3 class="uk-card-title">
                                <?php if ( $s['link_style'] === 'card' ) : ?>
                                    <a href="<?php echo esc_url( $item['url'] ); ?>" class="uk-link-reset"><?php echo esc_html( $item['title'] ); ?></a>
                                <?php else : ?>
                                    <?php echo esc_html( $item['title'] ); ?>
                                <?php endif; ?>
                            </h3>

                            <?php if ( ! empty( $s['show_meta'] ) ) : ?>
                            <div class="olo-postgrid-meta">
                                <?php echo esc_html( $item['date_fmt'] ); ?> &middot; <?php echo esc_html( $item['author'] ); ?>
                            </div>
                            <?php endif; ?>

                            <?php if ( ! empty( $s['show_excerpt'] ) && ! empty( $item['excerpt'] ) ) : ?>
                            <p class="olo-postgrid-excerpt"><?php echo wp_kses_post( $item['excerpt'] ); ?></p>
                            <?php endif; ?>

                            <?php if ( ! empty( $s['show_price'] ) && isset( $item['price'] ) ) : ?>
                            <div class="olo-postgrid-price">
                                <?php echo esc_html( $s['price_prefix'] . $item['price'] . $s['price_suffix'] ); ?>
                            </div>
                            <?php endif; ?>

                            <?php if ( $s['link_style'] === 'button' ) : ?>
                                <a href="<?php echo esc_url( $item['url'] ); ?>" class="uk-button uk-button-primary uk-button-small"><?php echo esc_html( $s['link_text'] ); ?></a>
                            <?php elseif ( $s['link_style'] === 'text' ) : ?>
                                <a href="<?php echo esc_url( $item['url'] ); ?>" class="olo-postgrid-link"><?php echo esc_html( $s['link_text'] ); ?> &rarr;</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="olo-postgrid-empty" style="display:none;">
                <p>Nessun risultato trovato.</p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function get_taxonomy_terms( $taxonomy ) {
        $terms = get_terms( [
            'taxonomy'   => $taxonomy,
            'hide_empty' => true,
        ] );

        if ( is_wp_error( $terms ) || empty( $terms ) ) {
            return [];
        }

        $result = [];
        foreach ( $terms as $term ) {
            $result[] = [
                'slug' => $term->slug,
                'name' => $term->name,
            ];
        }
        return $result;
    }

    private function render_filters( $terms, $style, $grid_id, $align = 'left' ) {
        $fa_cls = $align === 'center' ? ' olo-filter-center' : ( $align === 'right' ? ' olo-filter-right' : '' );
        ?>
        <div class="olo-postgrid-filters<?php echo $fa_cls; ?>" data-postgrid-target="<?php echo esc_attr( $grid_id ); ?>">
            <?php if ( $style === 'dropdown' ) : ?>
                <select class="olo-postgrid-filter-select uk-select" data-postgrid-filter-select>
                    <option value=""><?php esc_html_e( 'Tutti', 'olobuilder' ); ?></option>
                    <?php foreach ( $terms as $term ) : ?>
                        <option value="<?php echo esc_attr( $term['slug'] ); ?>">
                            <?php echo esc_html( $term['name'] ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php elseif ( $style === 'minimal' ) : ?>
                <button class="olo-postgrid-pill olo-postgrid-pill--minimal olo-postgrid-pill-active" data-filter="">
                    <?php esc_html_e( 'Tutti', 'olobuilder' ); ?>
                </button>
                <?php foreach ( $terms as $term ) : ?>
                    <button class="olo-postgrid-pill olo-postgrid-pill--minimal" data-filter="<?php echo esc_attr( $term['slug'] ); ?>">
                        <?php echo esc_html( $term['name'] ); ?>
                    </button>
                <?php endforeach; ?>
            <?php else : ?>
                <button class="olo-postgrid-pill olo-postgrid-pill-active" data-filter="">
                    <?php esc_html_e( 'Tutti', 'olobuilder' ); ?>
                </button>
                <?php foreach ( $terms as $term ) : ?>
                    <button class="olo-postgrid-pill" data-filter="<?php echo esc_attr( $term['slug'] ); ?>">
                        <?php echo esc_html( $term['name'] ); ?>
                    </button>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php
    }

    private function render_sort_select( $s ) {
        $options_str = $s['sort_options'] ?? 'date|title';
        $available   = array_map( 'trim', explode( '|', $options_str ) );

        $all_options = [
            'date'  => [ 'date-desc' => 'Più recenti', 'date-asc' => 'Meno recenti' ],
            'price' => [ 'price-asc' => 'Prezzo ↑', 'price-desc' => 'Prezzo ↓' ],
            'title' => [ 'title-asc' => 'A → Z', 'title-desc' => 'Z → A' ],
        ];
        ?>
        <select class="olo-postgrid-sort uk-select" data-postgrid-sort>
            <option value="default"><?php esc_html_e( 'Ordina per…', 'olobuilder' ); ?></option>
            <?php foreach ( $available as $group ) :
                if ( isset( $all_options[ $group ] ) ) :
                    foreach ( $all_options[ $group ] as $val => $label ) : ?>
                        <option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $label ); ?></option>
                    <?php endforeach;
                endif;
            endforeach; ?>
        </select>
        <?php
    }
}
