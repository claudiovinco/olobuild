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
        'card_primary_bg' => '',
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
        'hover_effect'      => 'none',
        'hover_image_field' => '',
        'hover_video_field' => '',
        'ribbon_field'      => '',
        'ribbon_position' => 'top-right',
        'ribbon_bg'       => '#e11d48',
        'ribbon_color'    => '#ffffff',
        'meta_filter'     => '',
        // Stile testo
        'body_padding'     => '15',
        'title_size'       => '1',
        'excerpt_size'     => '0.92',
        'title_color'      => '',
        'excerpt_color'    => '',
        'meta_color'       => '',
        'body_bg'          => '',
        'body_bg_opacity'  => '100',
        // Paginazione
        'pagination'       => false,
        'items_per_page'   => '6',
        'pagination_style' => 'dots',
        // Ken Burns
        'fx_kenburns'       => false,
        'fx_kenburns_speed' => '20',
        'fx_kenburns_scale' => '1.12',
        // Overlay gradient
        'overlay_gradient'  => false,
        'overlay_color'     => '#000000',
        'overlay_opacity'   => '50',
        'overlay_direction' => 'bottom',
        'overlay_height'    => '50',
        // Dati servizio
        'show_service_stats'   => false,
        'show_service_club'    => false,
        'show_service_opening' => false,
        'opening_bg_annual'    => '#059669',
        'opening_bg_seasonal'  => '#d97706',
        'opening_size'         => '11',
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $post_type = sanitize_key( $s['post_type'] );
        if ( ! post_type_exists( $post_type ) ) {
            return '<p style="color:var(--olo-color-text-muted, #9CA3AF);text-align:center;">Tipo di contenuto "' . esc_html( $post_type ) . '" non trovato.</p>';
        }

        // Query
        $query_args = [
            'post_type'              => $post_type,
            'posts_per_page'         => min( 100, absint( $s['posts_per_page'] ) ),
            'post_status'            => 'publish',
            'no_found_rows'          => true,
            'update_post_meta_cache' => true,
            'update_post_term_cache' => true,
            'orderby'                => sanitize_key( $s['orderby'] ),
            'order'                  => strtoupper( $s['order'] ) === 'ASC' ? 'ASC' : 'DESC',
        ];

        if ( in_array( $s['orderby'], [ 'meta_value_num', 'meta_value' ], true ) && ! empty( $s['meta_key'] ) ) {
            $query_args['meta_key'] = sanitize_key( $s['meta_key'] );
        }

        // Meta filter: "key=value" pre-filters query
        if ( ! empty( $s['meta_filter'] ) && strpos( $s['meta_filter'], '=' ) !== false ) {
            list( $mf_key, $mf_val ) = array_map( 'trim', explode( '=', $s['meta_filter'], 2 ) );
            if ( $mf_key && $mf_val ) {
                $query_args['meta_query'] = [
                    [ 'key' => sanitize_key( $mf_key ), 'value' => sanitize_text_field( $mf_val ) ],
                ];
            }
        }

        $query = new WP_Query( $query_args );

        if ( ! $query->have_posts() ) {
            wp_reset_postdata();
            return '<p style="color:var(--olo-color-text-muted, #9CA3AF);text-align:center;">Nessun articolo trovato.</p>';
        }

        // Batch pre-fetch: thumbnails, meta, and terms in single queries
        // instead of N+1 individual queries per post in the loop below.
        update_post_thumbnail_cache( $query );
        update_post_caches( $query->posts, $post_type, true, true );

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
                $item['image']    = get_the_post_thumbnail_url( $post->ID, 'medium_large' ) ?: '';
                $item['image_id'] = (int) get_post_thumbnail_id( $post->ID );
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

            // Hover media
            if ( ! empty( $s['hover_image_field'] ) ) {
                $hi_val = get_post_meta( $post->ID, sanitize_key( $s['hover_image_field'] ), true );
                if ( ! empty( $hi_val ) ) {
                    $item['hover_image'] = is_array( $hi_val ) ? ( $hi_val['url'] ?? '' ) : $hi_val;
                }
            }
            if ( ! empty( $s['hover_video_field'] ) ) {
                $hv_val = get_post_meta( $post->ID, sanitize_key( $s['hover_video_field'] ), true );
                if ( ! empty( $hv_val ) ) {
                    $item['hover_video'] = $hv_val;
                }
            }

            // Ribbon
            if ( ! empty( $s['ribbon_field'] ) ) {
                $ribbon_val = get_post_meta( $post->ID, sanitize_key( $s['ribbon_field'] ), true );
                if ( ! empty( $ribbon_val ) ) {
                    $item['ribbon'] = $ribbon_val;
                }
            }

            // Service stats (only for olo_service)
            if ( $post_type === 'olo_service' ) {
                if ( ! empty( $s['show_service_stats'] ) ) {
                    $item['service_capacity']  = get_post_meta( $post->ID, '_olo_service_capacity', true );
                    $item['service_bedrooms']  = get_post_meta( $post->ID, '_olo_service_bedrooms', true );
                    $item['service_bathrooms'] = get_post_meta( $post->ID, '_olo_service_bathrooms', true );
                    $item['service_altitude']  = get_post_meta( $post->ID, '_olo_service_altitude', true );
                }
                if ( ! empty( $s['show_service_club'] ) ) {
                    $item['service_club_group']    = get_post_meta( $post->ID, '_olo_service_club_group', true );
                    $item['service_club_category'] = get_post_meta( $post->ID, '_olo_service_club_category', true );
                }
                if ( ! empty( $s['show_service_opening'] ) ) {
                    $item['service_opening'] = get_post_meta( $post->ID, '_olo_service_opening', true );
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
        $image_radius = $this->build_border_radius_css( $s["image_radius"] ?? 0 );
        $card_radius  = $this->build_border_radius_css( $s["card_radius"] ?? 4 );

        // Sort config for JS
        $sort_enabled      = ! empty( $s['show_sort'] );
        $pagination_on     = ! empty( $s['pagination'] );
        $items_per_page    = max( 2, min( 24, absint( $s['items_per_page'] ) ) );
        $pagination_style  = sanitize_key( $s['pagination_style'] ?? 'dots' );
        $config            = [
            'sortEnabled'      => $sort_enabled,
            'paginationEnabled' => $pagination_on,
            'itemsPerPage'     => $items_per_page,
            'paginationStyle'  => $pagination_style,
        ];

        // Hover effect
        $hover_effect = $s['hover_effect'] ?? 'none';
        $img_class    = 'olo-pg-img';
        if ( $hover_effect !== 'none' ) {
            $img_class .= ' olo-pg-hover-' . esc_attr( $hover_effect );
        }

        // Ken Burns
        $kenburns_on    = ! empty( $s['fx_kenburns'] );
        $kenburns_speed = max( 10, min( 40, absint( $s['fx_kenburns_speed'] ?? 20 ) ) );
        $kenburns_scale = max( 1.05, min( 1.25, floatval( $s['fx_kenburns_scale'] ?? 1.12 ) ) );
        if ( $kenburns_on ) {
            $img_class .= ' olo-pg-kenburns';
        }

        // Overlay gradient
        $overlay_on        = ! empty( $s['overlay_gradient'] );
        $overlay_color     = $s['overlay_color'] ?? '#000000';
        $overlay_opacity   = max( 10, min( 90, absint( $s['overlay_opacity'] ?? 50 ) ) );
        $overlay_direction = $s['overlay_direction'] ?? 'bottom';
        $overlay_height    = max( 20, min( 100, absint( $s['overlay_height'] ?? 50 ) ) );

        // Stile testo
        $body_padding = Olo_Tile_Utils::spacing_css( $s['tile_padding'] ?? $s['body_padding'] ?? 15, 15 );
        $title_size      = max( 0.7, min( 2.5, floatval( $s['title_size'] ?? 1 ) ) );
        $excerpt_size    = max( 0.7, min( 1.5, floatval( $s['excerpt_size'] ?? 0.92 ) ) );
        $title_color     = $s['title_color'] ?? '';
        $excerpt_color   = $s['excerpt_color'] ?? '';
        $meta_color      = $s['meta_color'] ?? '';
        $body_bg         = $s['body_bg'] ?? '';
        $body_bg_opacity = max( 0, min( 100, absint( $s['body_bg_opacity'] ?? 100 ) ) );

        // Ribbon
        $ribbon_field    = $s['ribbon_field'] ?? '';
        $ribbon_position = $s['ribbon_position'] ?? 'top-right';
        $ribbon_bg       = $this->safe_color_css( $s['ribbon_bg'] ?? '#e11d48' );
        $ribbon_color    = $this->safe_color_css( $s['ribbon_color'] ?? '#ffffff' );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> .olo-pg-img { transition: transform 0.5s ease, filter 0.5s ease; width: 100%; height: <?php echo $image_height; ?>px; object-fit: cover; display: block; border-radius: <?php echo $image_radius; ?>; }
            .<?php echo $uid; ?> .olo-card-minimal__img { border-radius: <?php echo $image_radius; ?>; }
            .<?php echo $uid; ?> .uk-card-media-top { border-radius: <?php echo $image_radius; ?>; overflow: hidden; }
            .<?php echo $uid; ?> .uk-card { border-radius: <?php echo $card_radius; ?>; overflow: hidden; }
            .<?php echo $uid; ?> .olo-card-minimal { border-radius: <?php echo $card_radius; ?>; overflow: hidden; }
            .<?php echo $uid; ?> .uk-card:hover .olo-pg-hover-zoom, .<?php echo $uid; ?> .olo-card-minimal:hover .olo-pg-hover-zoom { transform: scale(1.08); }
            .<?php echo $uid; ?> .uk-card:hover .olo-pg-hover-zoom-rotate, .<?php echo $uid; ?> .olo-card-minimal:hover .olo-pg-hover-zoom-rotate { transform: scale(1.08) rotate(2deg); }
            .<?php echo $uid; ?> .olo-pg-hover-brightness { filter: brightness(0.7); }
            .<?php echo $uid; ?> .uk-card:hover .olo-pg-hover-brightness, .<?php echo $uid; ?> .olo-card-minimal:hover .olo-pg-hover-brightness { filter: brightness(1); }
            .<?php echo $uid; ?> .olo-pg-hover-desaturate { filter: grayscale(100%); }
            .<?php echo $uid; ?> .uk-card:hover .olo-pg-hover-desaturate, .<?php echo $uid; ?> .olo-card-minimal:hover .olo-pg-hover-desaturate { filter: grayscale(0%); }
            .<?php echo $uid; ?> .olo-pg-hover-blur-in { filter: blur(3px); }
            .<?php echo $uid; ?> .uk-card:hover .olo-pg-hover-blur-in, .<?php echo $uid; ?> .olo-card-minimal:hover .olo-pg-hover-blur-in { filter: blur(0); }
            .<?php echo $uid; ?> .olo-pg-ribbon { position: absolute; z-index: 2; font-size: 11px; font-weight: 700; padding: 4px 12px; text-transform: uppercase; letter-spacing: 0.5px; background: <?php echo $ribbon_bg; ?>; color: <?php echo $ribbon_color; ?>; }
            .<?php echo $uid; ?> .olo-pg-ribbon--top-right { top: 0; right: 14px; border-radius: 0 0 4px 4px; }
            .<?php echo $uid; ?> .olo-pg-ribbon--top-left { top: 0; left: 14px; border-radius: 0 0 4px 4px; }
            /* Stile testo */
            .<?php echo $uid; ?> .uk-card-body { padding: <?php echo $body_padding; ?>; }
            .<?php echo $uid; ?> .olo-card-minimal__body { padding: <?php echo $body_padding; ?>; }
            .<?php echo $uid; ?> .uk-card-title { font-size: <?php echo $title_size; ?>em; }
            .<?php echo $uid; ?> .olo-card-minimal__title { font-size: <?php echo $title_size; ?>em; }
            .<?php echo $uid; ?> .olo-postgrid-excerpt { font-size: <?php echo $excerpt_size; ?>em; }
            .<?php echo $uid; ?> .olo-card-minimal__text { font-size: <?php echo $excerpt_size; ?>em; }
            <?php if ( $title_color ) : ?>
            .<?php echo $uid; ?> .uk-card-title, .<?php echo $uid; ?> .uk-card-title a { color: <?php echo $this->safe_color_css( $title_color ); ?> !important; }
            .<?php echo $uid; ?> .olo-card-minimal__title, .<?php echo $uid; ?> .olo-card-minimal__title a { color: <?php echo $this->safe_color_css( $title_color ); ?> !important; }
            <?php endif; ?>
            <?php if ( $excerpt_color ) : ?>
            .<?php echo $uid; ?> .olo-postgrid-excerpt { color: <?php echo $this->safe_color_css( $excerpt_color ); ?> !important; }
            .<?php echo $uid; ?> .olo-card-minimal__text { color: <?php echo $this->safe_color_css( $excerpt_color ); ?> !important; }
            <?php endif; ?>
            <?php if ( $meta_color ) : ?>
            .<?php echo $uid; ?> .olo-postgrid-meta { color: <?php echo $this->safe_color_css( $meta_color ); ?> !important; }
            <?php endif; ?>
            <?php if ( $body_bg ) :
                $bg_r = hexdec( substr( $body_bg, 1, 2 ) );
                $bg_g = hexdec( substr( $body_bg, 3, 2 ) );
                $bg_b = hexdec( substr( $body_bg, 5, 2 ) );
                $bg_a = round( $body_bg_opacity / 100, 2 );
            ?>
            .<?php echo $uid; ?> .uk-card-body { background: rgba(<?php echo "$bg_r,$bg_g,$bg_b,$bg_a"; ?>); }
            .<?php echo $uid; ?> .olo-card-minimal__body { background: rgba(<?php echo "$bg_r,$bg_g,$bg_b,$bg_a"; ?>); }
            <?php endif; ?>
            /* Nuovi effetti hover */
            .<?php echo $uid; ?> .uk-card:hover .olo-pg-hover-slide-up, .<?php echo $uid; ?> .olo-card-minimal:hover .olo-pg-hover-slide-up { transform: translateY(-8px) scale(1.02); }
            .<?php echo $uid; ?> .olo-pg-hover-glow { filter: brightness(1); }
            .<?php echo $uid; ?> .uk-card:hover .olo-pg-hover-glow, .<?php echo $uid; ?> .olo-card-minimal:hover .olo-pg-hover-glow { filter: brightness(1.15) saturate(1.2); box-shadow: 0 0 20px rgba(255,255,255,0.3); }
            .<?php echo $uid; ?> .uk-card { perspective: 800px; }
            .<?php echo $uid; ?> .olo-card-minimal { perspective: 800px; }
            .<?php echo $uid; ?> .uk-card:hover .olo-pg-hover-tilt, .<?php echo $uid; ?> .olo-card-minimal:hover .olo-pg-hover-tilt { transform: rotateY(4deg) rotateX(2deg) scale(1.03); }
            <?php if ( $kenburns_on ) : ?>
            /* Ken Burns */
            @keyframes olo-kb-<?php echo $uid; ?> {
                0% { transform: scale(1); }
                50% { transform: scale(<?php echo $kenburns_scale; ?>); }
                100% { transform: scale(1); }
            }
            .<?php echo $uid; ?> .olo-pg-kenburns { animation: olo-kb-<?php echo $uid; ?> <?php echo $kenburns_speed; ?>s ease-in-out infinite; }
            .<?php echo $uid; ?> .olo-postgrid-item:nth-child(2n) .olo-pg-kenburns { animation-delay: -<?php echo round( $kenburns_speed / 3, 1 ); ?>s; }
            .<?php echo $uid; ?> .olo-postgrid-item:nth-child(3n) .olo-pg-kenburns { animation-delay: -<?php echo round( $kenburns_speed * 2 / 3, 1 ); ?>s; }
            <?php endif; ?>
            <?php if ( $overlay_on ) : ?>
            /* Overlay gradient */
            .<?php echo $uid; ?> .olo-pg-overlay { position: absolute; inset: 0; pointer-events: none; z-index: 1; }
            <?php endif; ?>
            <?php if ( ! empty( $s['match_height'] ) ) : ?>
            .<?php echo $uid; ?> .olo-postgrid-item > .uk-card { height: 100%; display: flex; flex-direction: column; }
            .<?php echo $uid; ?> .olo-postgrid-item > .uk-card > .uk-card-body { flex: 1; }
            <?php endif; ?>
            <?php if ( $s['card_style'] === 'primary' ) :
                $primary_bg = $this->safe_color_css( $s['card_primary_bg'] ?? '' ) ?: 'var(--olo-color-primary, #6366F1)';
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
                            <?php
                            $pg_min_img = Olo_Tile_Utils::img_srcset( $item['image_id'] ?? 0, $item['image'], $item['title'], 'olo-card-minimal__img ' . $img_class );
                            echo $this->render_hover_wrap( $pg_min_img, $item['hover_image'] ?? '', $item['hover_video'] ?? '' );
                            ?>
                            <?php if ( $s['link_style'] === 'card' ) : ?>
                                </a>
                            <?php endif; ?>
                            <?php if ( $overlay_on ) : ?>
                                <?php echo $this->render_overlay_gradient( $overlay_color, $overlay_opacity, $overlay_direction, $overlay_height ); ?>
                            <?php endif; ?>
                            <?php if ( ! empty( $s['show_category'] ) && ! empty( $item['term_names'] ) ) : ?>
                                <span class="olo-postgrid-badge"><?php echo esc_html( $item['term_names'][0] ); ?></span>
                            <?php endif; ?>
                            <?php if ( ! empty( $item['ribbon'] ) ) : ?>
                                <span class="olo-pg-ribbon olo-pg-ribbon--<?php echo esc_attr( $ribbon_position ); ?>"><?php echo esc_html( $item['ribbon'] ); ?></span>
                            <?php endif; ?>
                            <?php if ( ! empty( $item['service_opening'] ) ) : ?>
                                <?php
                                    $op_bg = ( stripos( $item['service_opening'], 'stagionale' ) !== false )
                                        ? $s['opening_bg_seasonal']
                                        : $s['opening_bg_annual'];
                                ?><span class="olo-pg-opening" style="background:<?php echo $this->safe_color_css( $op_bg ); ?>;font-size:<?php echo absint( $s['opening_size'] ); ?>px"><?php echo esc_html( $item['service_opening'] ); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <div class="olo-card-minimal__body">
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

                        <?php if ( ! empty( $s['show_service_stats'] ) ) echo $this->render_service_stats( $item ); ?>
                        <?php if ( ! empty( $s['show_service_club'] ) )  echo $this->render_service_club( $item ); ?>

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
                        </div><!-- /.olo-card-minimal__body -->
                    </div>
                    <?php else : ?>
                    <div class="uk-card <?php echo esc_attr( $card_class ); ?>">
                        <?php if ( ! empty( $s['show_image'] ) && ! empty( $item['image'] ) ) : ?>
                        <div class="uk-card-media-top" style="position:relative;overflow:hidden;">
                            <?php if ( $s['link_style'] === 'card' ) : ?>
                                <a href="<?php echo esc_url( $item['url'] ); ?>">
                            <?php endif; ?>
                            <?php
                            $pg_img = Olo_Tile_Utils::img_srcset( $item['image_id'] ?? 0, $item['image'], $item['title'], $img_class );
                            echo $this->render_hover_wrap( $pg_img, $item['hover_image'] ?? '', $item['hover_video'] ?? '' );
                            ?>
                            <?php if ( $s['link_style'] === 'card' ) : ?>
                                </a>
                            <?php endif; ?>
                            <?php if ( $overlay_on ) : ?>
                                <?php echo $this->render_overlay_gradient( $overlay_color, $overlay_opacity, $overlay_direction, $overlay_height ); ?>
                            <?php endif; ?>
                            <?php if ( ! empty( $s['show_category'] ) && ! empty( $item['term_names'] ) ) : ?>
                                <span class="olo-postgrid-badge"><?php echo esc_html( $item['term_names'][0] ); ?></span>
                            <?php endif; ?>
                            <?php if ( ! empty( $item['ribbon'] ) ) : ?>
                                <span class="olo-pg-ribbon olo-pg-ribbon--<?php echo esc_attr( $ribbon_position ); ?>"><?php echo esc_html( $item['ribbon'] ); ?></span>
                            <?php endif; ?>
                            <?php if ( ! empty( $item['service_opening'] ) ) : ?>
                                <?php
                                    $op_bg = ( stripos( $item['service_opening'], 'stagionale' ) !== false )
                                        ? $s['opening_bg_seasonal']
                                        : $s['opening_bg_annual'];
                                ?><span class="olo-pg-opening" style="background:<?php echo $this->safe_color_css( $op_bg ); ?>;font-size:<?php echo absint( $s['opening_size'] ); ?>px"><?php echo esc_html( $item['service_opening'] ); ?></span>
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

                            <?php if ( ! empty( $s['show_service_stats'] ) ) echo $this->render_service_stats( $item ); ?>
                            <?php if ( ! empty( $s['show_service_club'] ) )  echo $this->render_service_club( $item ); ?>

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

            <?php if ( $pagination_on ) : ?>
                <?php if ( $pagination_style === 'infinite' ) : ?>
                <?php
                    $total_items = count( $posts );
                    $total_pages = (int) ceil( $total_items / $items_per_page );
                    $has_more    = $total_pages > 1;
                ?>
                <?php if ( $has_more ) : ?>
                <div class="olo-pg-sentinel" data-olo-pg-sentinel
                     data-total-pages="<?php echo $total_pages; ?>"
                     data-items-per-page="<?php echo $items_per_page; ?>"
                     data-grid-id="<?php echo esc_attr( $uid ); ?>"
                     style="display:flex;justify-content:center;padding:20px 0;">
                    <div class="olo-pg-spinner" style="width:32px;height:32px;border:3px solid rgba(150,150,150,0.2);border-top-color:#888;border-radius:50%;animation:olo-pg-spin 0.8s linear infinite;display:none;"></div>
                </div>
                <style>
                    @keyframes olo-pg-spin { to { transform: rotate(360deg); } }
                </style>
                <script>
                (function(){
                    var sentinel = document.querySelector('#<?php echo esc_attr( $uid ); ?> .olo-pg-sentinel');
                    if(!sentinel) return;
                    var grid = document.querySelector('#<?php echo esc_attr( $uid ); ?> .olo-postgrid-grid');
                    if(!grid) return;
                    var allItems = grid.querySelectorAll('.olo-postgrid-item');
                    var perPage = <?php echo $items_per_page; ?>;
                    var totalItems = allItems.length;
                    var shown = perPage;
                    var spinner = sentinel.querySelector('.olo-pg-spinner');

                    /* Nasconde gli item oltre la prima pagina */
                    for(var i = perPage; i < totalItems; i++){
                        allItems[i].style.display = 'none';
                    }

                    if(shown >= totalItems){
                        sentinel.style.display = 'none';
                        return;
                    }

                    var loading = false;
                    var observer = new IntersectionObserver(function(entries){
                        if(!entries[0].isIntersecting) return;
                        if(loading) return;
                        loading = true;
                        if(spinner){ spinner.style.display = 'block'; }

                        setTimeout(function(){
                            var end = shown + perPage;
                            if(end > totalItems){ end = totalItems; }
                            for(var j = shown; j < end; j++){
                                allItems[j].style.display = '';
                            }
                            shown = end;
                            if(spinner){ spinner.style.display = 'none'; }
                            loading = false;

                            if(shown >= totalItems){
                                observer.disconnect();
                                sentinel.style.display = 'none';
                            }
                        }, 300);
                    }, { rootMargin: '200px' });

                    observer.observe(sentinel);
                })();
                </script>
                <?php endif; ?>
                <?php else : ?>
            <div class="olo-pg-pagination" data-pagination-style="<?php echo esc_attr( $pagination_style ); ?>">
                <?php if ( $pagination_style === 'arrows' ) : ?>
                    <button class="olo-pg-page-btn olo-pg-prev" aria-label="Pagina precedente" disabled>&lsaquo;</button>
                    <span class="olo-pg-page-info"></span>
                    <button class="olo-pg-page-btn olo-pg-next" aria-label="Pagina successiva">&rsaquo;</button>
                <?php elseif ( $pagination_style === 'loadmore' ) : ?>
                    <button class="olo-pg-loadmore">Carica altri</button>
                <?php else : ?>
                    <!-- dots/numbers generati via JS -->
                <?php endif; ?>
            </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="olo-postgrid-empty" style="display:none;">
                <p>Nessun risultato trovato.</p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function render_service_stats( $item ) {
        $stats = [];
        if ( ! empty( $item['service_capacity'] ) )  $stats[] = '<span class="olo-pg-stat" title="Ospiti"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="9" cy="7" r="3" stroke="currentColor" stroke-width="1.8"/><path d="M3 20C3 16.6863 5.68629 14 9 14C12.3137 14 15 16.6863 15 20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="17" cy="8" r="2" stroke="currentColor" stroke-width="1.8" opacity="0.6"/><path d="M17 14C19.2091 14 21 15.7909 21 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" opacity="0.6"/></svg> ' . esc_html( $item['service_capacity'] ) . '</span>';
        if ( ! empty( $item['service_bedrooms'] ) )  $stats[] = '<span class="olo-pg-stat" title="Camere"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 18V12C3 10.3431 4.34315 9 6 9H18C19.6569 9 21 10.3431 21 12V18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M3 18V20M21 18V20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M7 9V7C7 5.89543 7.89543 5 9 5H15C16.1046 5 17 5.89543 17 7V9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="8.5" cy="7.5" r="1.5" fill="currentColor" opacity="0.5"/></svg> ' . esc_html( $item['service_bedrooms'] ) . '</span>';
        if ( ! empty( $item['service_bathrooms'] ) ) $stats[] = '<span class="olo-pg-stat" title="Bagni"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 12H20V15C20 17.2091 18.2091 19 16 19H8C5.79086 19 4 17.2091 4 15V12Z" stroke="currentColor" stroke-width="1.8"/><path d="M4 12V5C4 4.44772 4.44772 4 5 4H7C7.55228 4 8 4.44772 8 5V12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M7 19V21M17 19V21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg> ' . esc_html( $item['service_bathrooms'] ) . '</span>';
        if ( ! empty( $item['service_altitude'] ) )  $stats[] = '<span class="olo-pg-stat" title="Altitudine"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 3L20 19H4L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M12 8V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" opacity="0.5"/></svg> ' . esc_html( $item['service_altitude'] ) . 'm</span>';

        if ( empty( $stats ) ) return '';
        return '<div class="olo-pg-service-stats">' . implode( '', $stats ) . '</div>';
    }

    private function render_service_club( $item ) {
        $parts = [];
        if ( ! empty( $item['service_club_group'] ) )    $parts[] = esc_html( $item['service_club_group'] );
        if ( ! empty( $item['service_club_category'] ) ) $parts[] = esc_html( $item['service_club_category'] );
        if ( empty( $parts ) ) return '';
        return '<div class="olo-pg-service-club">' . implode( ' · ', $parts ) . '</div>';
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

    private function render_overlay_gradient( $color, $opacity, $direction, $height ) {
        $r = hexdec( substr( $color, 1, 2 ) );
        $g = hexdec( substr( $color, 3, 2 ) );
        $b = hexdec( substr( $color, 5, 2 ) );
        $a = round( $opacity / 100, 2 );

        $dir_map = [ 'bottom' => 'to top', 'top' => 'to bottom', 'left' => 'to right', 'right' => 'to left' ];
        $css_dir = $dir_map[ $direction ] ?? 'to top';

        // Position the gradient at the correct edge
        $pos_map = [ 'bottom' => 'bottom:0;left:0;right:0;', 'top' => 'top:0;left:0;right:0;', 'left' => 'top:0;left:0;bottom:0;', 'right' => 'top:0;right:0;bottom:0;' ];
        $pos     = $pos_map[ $direction ] ?? 'bottom:0;left:0;right:0;';
        $dim     = in_array( $direction, [ 'left', 'right' ], true )
                   ? "width:{$height}%;height:100%;"
                   : "width:100%;height:{$height}%;";

        return '<div class="olo-pg-overlay" style="position:absolute;' . $pos . $dim . 'pointer-events:none;z-index:1;background:linear-gradient(' . $css_dir . ',rgba(' . $r . ',' . $g . ',' . $b . ',' . $a . '),transparent);"></div>';
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
