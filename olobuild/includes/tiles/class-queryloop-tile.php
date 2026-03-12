<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Queryloop_Tile extends Olo_Tile_Base {

    protected $type     = 'queryloop';
    protected $name     = 'Query Loop';
    protected $icon     = 'dashicons-database';
    protected $category = 'dynamic';
    protected $defaults = [
        'post_type'       => 'post',
        'posts_per_page'  => '6',
        'orderby'         => 'date',
        'order'           => 'DESC',
        'taxonomy'        => '',
        'terms'           => '',
        'offset'          => '0',
        'exclude_current' => true,
        'layout'          => 'grid',
        'columns'         => '3',
        'gap'             => '30',
        'show_image'      => true,
        'show_title'      => true,
        'show_excerpt'    => true,
        'show_date'       => true,
        'show_author'     => false,
        'show_category'   => true,
        'show_read_more'  => true,
        'read_more_text'  => 'Leggi tutto',
        'excerpt_length'  => '20',
        'image_ratio'     => '16:9',
        'pagination_type' => 'none',
        'title_tag'       => 'h3',
        'title_color'     => '',
        'text_color'      => '',
        'meta_color'      => '',
        'link_color'      => '',
        'bg_color'        => '',
        'hover_bg'        => '',
        'card_style'      => 'none',
        'loop_template_id' => '',
    ];

    /** Track whether the AJAX handler has been registered. */
    private static $ajax_registered = false;

    public function __construct() {
        if ( ! self::$ajax_registered ) {
            add_action( 'wp_ajax_olo_queryloop_page', [ $this, 'ajax_load_page' ] );
            add_action( 'wp_ajax_nopriv_olo_queryloop_page', [ $this, 'ajax_load_page' ] );
            self::$ajax_registered = true;
        }
    }

    public function get_controls() {
        return [];
    }

    /**
     * Build WP_Query args from settings.
     */
    private function build_query_args( $s, $paged = 1 ) {
        // Post type: support custom post types
        $post_type = sanitize_key( $s['post_type'] ?? 'post' );
        if ( $post_type === 'custom' ) {
            $cpt = sanitize_key( $s['custom_post_type'] ?? '' );
            $post_type = $cpt !== '' ? $cpt : 'post';
        }

        $args = [
            'post_type'      => $post_type,
            'posts_per_page' => absint( $s['posts_per_page'] ),
            'orderby'        => sanitize_key( $s['orderby'] ),
            'order'          => strtoupper( $s['order'] ) === 'ASC' ? 'ASC' : 'DESC',
            'post_status'    => 'publish',
            'paged'          => $paged,
        ];

        // Offset
        $offset = absint( $s['offset'] ?? 0 );
        if ( $offset > 0 ) {
            $args['offset'] = $offset + ( ( $paged - 1 ) * absint( $s['posts_per_page'] ) );
        }

        // Exclude current post
        $not_in = [];
        if ( ! empty( $s['exclude_current'] ) ) {
            $current_id = get_the_ID();
            if ( $current_id ) {
                $not_in[] = $current_id;
            }
        }

        // Exclude specific IDs
        $exclude_ids = trim( $s['exclude_ids'] ?? '' );
        if ( $exclude_ids !== '' ) {
            foreach ( explode( ',', $exclude_ids ) as $eid ) {
                $eid = absint( trim( $eid ) );
                if ( $eid > 0 ) $not_in[] = $eid;
            }
        }
        if ( ! empty( $not_in ) ) {
            $args['post__not_in'] = array_unique( $not_in );
        }

        // Include specific IDs only
        $include_ids = trim( $s['include_ids'] ?? '' );
        if ( $include_ids !== '' ) {
            $ids = array_filter( array_map( function( $v ) { return absint( trim( $v ) ); }, explode( ',', $include_ids ) ) );
            if ( ! empty( $ids ) ) {
                $args['post__in'] = $ids;
            }
        }

        // Author filter
        $author_ids = trim( $s['author_ids'] ?? '' );
        if ( $author_ids !== '' ) {
            $aids = array_filter( array_map( function( $v ) { return absint( trim( $v ) ); }, explode( ',', $author_ids ) ) );
            if ( ! empty( $aids ) ) {
                $args['author__in'] = $aids;
            }
        }

        // Sticky posts handling
        $sticky = $s['sticky_posts'] ?? 'include';
        if ( $sticky === 'exclude' ) {
            $args['ignore_sticky_posts'] = true;
            $sticky_ids = get_option( 'sticky_posts', [] );
            if ( ! empty( $sticky_ids ) ) {
                $args['post__not_in'] = array_unique( array_merge( $args['post__not_in'] ?? [], $sticky_ids ) );
            }
        } elseif ( $sticky === 'only' ) {
            $sticky_ids = get_option( 'sticky_posts', [] );
            if ( ! empty( $sticky_ids ) ) {
                $args['post__in'] = $sticky_ids;
            }
        }

        // Taxonomy filter (primary)
        $taxonomy  = sanitize_text_field( $s['taxonomy'] ?? '' );
        $terms_str = sanitize_text_field( $s['terms'] ?? '' );
        $tax_query = [];
        if ( $taxonomy !== '' && $terms_str !== '' ) {
            $term_slugs = array_map( 'trim', explode( ',', $terms_str ) );
            $operator   = $s['tax_relation'] ?? 'IN';
            $valid_ops  = [ 'IN', 'AND', 'NOT IN' ];
            if ( ! in_array( $operator, $valid_ops, true ) ) $operator = 'IN';
            $tax_query[] = [
                'taxonomy' => $taxonomy,
                'field'    => 'slug',
                'terms'    => $term_slugs,
                'operator' => $operator,
            ];
        }

        // Second taxonomy filter
        $tax2      = sanitize_text_field( $s['second_taxonomy'] ?? '' );
        $terms2    = sanitize_text_field( $s['second_terms'] ?? '' );
        if ( $tax2 !== '' && $terms2 !== '' ) {
            $tax_query[] = [
                'taxonomy' => $tax2,
                'field'    => 'slug',
                'terms'    => array_map( 'trim', explode( ',', $terms2 ) ),
            ];
        }
        if ( count( $tax_query ) > 1 ) {
            $tax_query['relation'] = 'AND';
        }
        if ( ! empty( $tax_query ) ) {
            $args['tax_query'] = $tax_query;
        }

        // Date range filter
        $date_after  = sanitize_text_field( $s['date_after'] ?? '' );
        $date_before = sanitize_text_field( $s['date_before'] ?? '' );
        if ( $date_after !== '' || $date_before !== '' ) {
            $date_query = [];
            if ( $date_after !== '' )  $date_query['after']  = $date_after;
            if ( $date_before !== '' ) $date_query['before'] = $date_before;
            $date_query['inclusive'] = true;
            $args['date_query'] = [ $date_query ];
        }

        // Meta query (ACF / custom field filter)
        $meta_key = sanitize_text_field( $s['meta_key'] ?? '' );
        if ( $meta_key !== '' ) {
            $meta_value   = sanitize_text_field( $s['meta_value'] ?? '' );
            $meta_compare = $s['meta_compare'] ?? '=';
            $meta_type    = $s['meta_type'] ?? 'CHAR';
            $valid_cmp    = [ '=', '!=', '>', '<', '>=', '<=', 'LIKE', 'NOT LIKE', 'EXISTS', 'NOT EXISTS' ];
            if ( ! in_array( $meta_compare, $valid_cmp, true ) ) $meta_compare = '=';
            $valid_types  = [ 'CHAR', 'NUMERIC', 'DATE', 'DECIMAL' ];
            if ( ! in_array( $meta_type, $valid_types, true ) ) $meta_type = 'CHAR';

            $mq = [
                'key'     => $meta_key,
                'compare' => $meta_compare,
                'type'    => $meta_type,
            ];
            if ( ! in_array( $meta_compare, [ 'EXISTS', 'NOT EXISTS' ], true ) ) {
                $mq['value'] = $meta_value;
            }
            $args['meta_query'] = [ $mq ];

            // Support orderby meta_value
            $orderby = $s['orderby'] ?? 'date';
            if ( in_array( $orderby, [ 'meta_value', 'meta_value_num' ], true ) ) {
                $args['meta_key'] = $meta_key;
            }
        }

        // Search keyword
        $search = sanitize_text_field( $s['search_keyword'] ?? '' );
        if ( $search !== '' ) {
            $args['s'] = $search;
        }

        return $args;
    }

    /**
     * Get aspect ratio padding from setting.
     */
    private function get_ratio_padding( $ratio ) {
        $map = [
            '16:9' => '56.25%',
            '4:3'  => '75%',
            '1:1'  => '100%',
            '3:2'  => '66.67%',
            'auto' => '0',
        ];
        return $map[ $ratio ] ?? '56.25%';
    }

    /**
     * Render a single card.
     */
    private function render_card( $post_obj, $s ) {
        $permalink  = get_permalink( $post_obj );
        $title      = get_the_title( $post_obj );
        $title_tag  = in_array( $s['title_tag'], [ 'h2', 'h3', 'h4', 'h5', 'h6' ], true ) ? $s['title_tag'] : 'h3';
        $excerpt_length = absint( $s['excerpt_length'] );

        // Card classes
        $card_classes = [ 'olo-ql-card' ];
        $card_style_type = $s['card_style'];
        if ( $card_style_type === 'shadow' ) {
            $card_classes[] = 'olo-ql-card--shadow';
        } elseif ( $card_style_type === 'border' ) {
            $card_classes[] = 'olo-ql-card--border';
        } elseif ( $card_style_type === 'filled' ) {
            $card_classes[] = 'olo-ql-card--filled';
        }

        // Card inline styles
        $card_styles = [];
        if ( ! empty( $s['bg_color'] ) ) {
            $card_styles[] = 'background-color:' . esc_attr( $s['bg_color'] );
        }

        $hover_attr = '';
        if ( ! empty( $s['hover_bg'] ) ) {
            $hover_attr = ' data-olo-ql-hover="' . esc_attr( $s['hover_bg'] ) . '"';
        }

        ob_start();
        ?>
        <article class="<?php echo esc_attr( implode( ' ', $card_classes ) ); ?>"<?php if ( ! empty( $card_styles ) ) echo ' style="' . esc_attr( implode( ';', $card_styles ) ) . '"'; ?><?php echo $hover_attr; ?>>
            <?php if ( ! empty( $s['show_image'] ) ) :
                $thumb_url = get_the_post_thumbnail_url( $post_obj, 'large' );
                $ratio_padding = $this->get_ratio_padding( $s['image_ratio'] );
                if ( $thumb_url ) :
            ?>
                <a href="<?php echo esc_url( $permalink ); ?>" class="olo-ql-img-link">
                    <?php if ( $s['image_ratio'] === 'auto' ) : ?>
                        <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="olo-ql-img-auto" loading="lazy">
                    <?php else : ?>
                        <div class="olo-ql-img" style="padding-bottom:<?php echo esc_attr( $ratio_padding ); ?>;background-image:url(<?php echo esc_url( $thumb_url ); ?>)"></div>
                    <?php endif; ?>
                </a>
            <?php endif; endif; ?>

            <div class="olo-ql-body">
                <?php if ( ! empty( $s['show_category'] ) ) :
                    $cats = get_the_category( $post_obj->ID );
                    if ( ! empty( $cats ) ) :
                ?>
                    <span class="olo-ql-cat"<?php if ( ! empty( $s['link_color'] ) ) echo ' style="color:' . esc_attr( $s['link_color'] ) . '"'; ?>><?php echo esc_html( $cats[0]->name ); ?></span>
                <?php endif; endif; ?>

                <?php if ( ! empty( $s['show_title'] ) ) : ?>
                    <<?php echo $title_tag; ?> class="olo-ql-title"<?php if ( ! empty( $s['title_color'] ) ) echo ' style="color:' . esc_attr( $s['title_color'] ) . '"'; ?>>
                        <a href="<?php echo esc_url( $permalink ); ?>"<?php if ( ! empty( $s['title_color'] ) ) echo ' style="color:inherit"'; ?>><?php echo esc_html( $title ); ?></a>
                    </<?php echo $title_tag; ?>>
                <?php endif; ?>

                <?php if ( ! empty( $s['show_date'] ) || ! empty( $s['show_author'] ) ) : ?>
                    <div class="olo-ql-meta"<?php if ( ! empty( $s['meta_color'] ) ) echo ' style="color:' . esc_attr( $s['meta_color'] ) . '"'; ?>>
                        <?php if ( ! empty( $s['show_date'] ) ) : ?>
                            <span class="olo-ql-date"><?php echo esc_html( get_the_date( '', $post_obj ) ); ?></span>
                        <?php endif; ?>
                        <?php if ( ! empty( $s['show_author'] ) ) : ?>
                            <span class="olo-ql-author"><?php echo esc_html( get_the_author_meta( 'display_name', $post_obj->post_author ) ); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ( ! empty( $s['show_excerpt'] ) ) :
                    $raw_excerpt = $post_obj->post_excerpt;
                    if ( empty( $raw_excerpt ) ) {
                        $raw_excerpt = $post_obj->post_content;
                    }
                    $excerpt_text = wp_trim_words( wp_strip_all_tags( $raw_excerpt ), $excerpt_length, '...' );
                ?>
                    <p class="olo-ql-excerpt"<?php if ( ! empty( $s['text_color'] ) ) echo ' style="color:' . esc_attr( $s['text_color'] ) . '"'; ?>><?php echo esc_html( $excerpt_text ); ?></p>
                <?php endif; ?>

                <?php if ( ! empty( $s['show_read_more'] ) ) : ?>
                    <a href="<?php echo esc_url( $permalink ); ?>" class="olo-ql-readmore"<?php if ( ! empty( $s['link_color'] ) ) echo ' style="color:' . esc_attr( $s['link_color'] ) . '"'; ?>><?php echo esc_html( $s['read_more_text'] ); ?> &rarr;</a>
                <?php endif; ?>
            </div>
        </article>
        <?php
        return ob_get_clean();
    }

    /**
     * Render cards HTML for a set of posts.
     */
    private function render_cards( $posts, $s ) {
        $loop_tpl_id = absint( $s['loop_template_id'] ?? 0 );
        $html = '';

        foreach ( $posts as $post_obj ) {
            setup_postdata( $post_obj );

            if ( $loop_tpl_id > 0 ) {
                // Render using custom Olobuild template for each item
                $html .= $this->render_loop_item( $post_obj, $loop_tpl_id );
            } else {
                $html .= $this->render_card( $post_obj, $s );
            }
        }
        wp_reset_postdata();
        return $html;
    }

    /**
     * Render a single loop item using an Olobuild template.
     * Sets the global $post so dynamic content resolves to the loop item.
     */
    private function render_loop_item( $post_obj, $template_id ) {
        global $post;
        $old_post = $post;
        $post = $post_obj;
        setup_postdata( $post );

        $renderer = new Olo_Frontend_Renderer();
        $output = $renderer->render_template( $template_id );

        $post = $old_post;
        if ( $old_post ) {
            setup_postdata( $old_post );
        }

        return '<div class="olo-ql-loop-item">' . $output . '</div>';
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        if ( ! post_type_exists( sanitize_key( $s['post_type'] ) ) ) {
            return '<p style="color:var(--olo-color-text-muted, #9CA3AF);text-align:center;">Tipo di contenuto "' . esc_html( $s['post_type'] ) . '" non trovato.</p>';
        }

        $paged = 1;
        if ( $s['pagination_type'] === 'numbers' ) {
            $paged = max( 1, absint( get_query_var( 'paged', 1 ) ) );
        }

        $query_args = $this->build_query_args( $s, $paged );
        $query = new WP_Query( $query_args );

        if ( ! $query->have_posts() ) {
            return '<p class="olo-ql-empty" style="color:var(--olo-color-text-muted, #9CA3AF);text-align:center;">Nessun risultato trovato.</p>';
        }

        $layout  = $s['layout'];
        $columns = absint( $s['columns'] );
        $gap     = absint( $s['gap'] );

        // Unique instance ID
        $instance_id = 'olo-ql-' . wp_unique_id();

        // Container style
        $container_style = '';
        if ( $layout === 'grid' ) {
            $container_style = "display:grid;grid-template-columns:repeat({$columns},1fr);gap:{$gap}px";
        } elseif ( $layout === 'list' ) {
            $container_style = "display:flex;flex-direction:column;gap:{$gap}px";
        } elseif ( $layout === 'masonry' ) {
            $container_style = "column-count:{$columns};column-gap:{$gap}px";
        }

        ob_start();
        ?>
        <div class="olo-queryloop" id="<?php echo esc_attr( $instance_id ); ?>">
            <?php if ( $layout === 'carousel' ) : ?>
                <div uk-slider="autoplay: false; sets: true">
                    <div class="uk-position-relative">
                        <div class="uk-slider-container">
                            <ul class="uk-slider-items uk-grid" style="gap:<?php echo $gap; ?>px">
                                <?php foreach ( $query->posts as $post_obj ) :
                                    setup_postdata( $post_obj );
                                ?>
                                    <li style="min-width:<?php echo esc_attr( round( 100 / $columns, 2 ) ); ?>%;max-width:<?php echo esc_attr( round( 100 / $columns, 2 ) ); ?>%">
                                        <?php echo $this->render_card( $post_obj, $s ); ?>
                                    </li>
                                <?php endforeach; wp_reset_postdata(); ?>
                            </ul>
                        </div>
                        <a class="uk-position-center-left uk-position-small" href="#" uk-slidenav-previous uk-slider-item="previous"></a>
                        <a class="uk-position-center-right uk-position-small" href="#" uk-slidenav-next uk-slider-item="next"></a>
                    </div>
                    <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin-small-top"></ul>
                </div>
            <?php else : ?>
                <div class="olo-ql-container" style="<?php echo esc_attr( $container_style ); ?>">
                    <?php echo $this->render_cards( $query->posts, $s ); ?>
                </div>
            <?php endif; ?>

            <?php
            // Pagination
            if ( $s['pagination_type'] === 'numbers' ) {
                $total_pages = $query->max_num_pages;
                if ( $total_pages > 1 ) {
                    echo '<nav class="olo-ql-pagination">';
                    echo paginate_links( [
                        'total'   => $total_pages,
                        'current' => $paged,
                        'type'    => 'list',
                    ] );
                    echo '</nav>';
                }
            } elseif ( $s['pagination_type'] === 'loadmore' ) {
                if ( $query->max_num_pages > 1 ) {
                    echo '<div class="olo-ql-loadmore-wrap" style="text-align:center;margin-top:' . $gap . 'px">';
                    echo '<button class="olo-ql-loadmore-btn" data-ql-id="' . esc_attr( $instance_id ) . '" data-ql-page="1" data-ql-max="' . esc_attr( $query->max_num_pages ) . '">Carica altro</button>';
                    echo '</div>';
                    $this->enqueue_ajax_script( $instance_id, $s, $query->max_num_pages );
                }
            } elseif ( $s['pagination_type'] === 'infinite' ) {
                if ( $query->max_num_pages > 1 ) {
                    echo '<div class="olo-ql-sentinel" data-ql-id="' . esc_attr( $instance_id ) . '" data-ql-page="1" data-ql-max="' . esc_attr( $query->max_num_pages ) . '" style="height:1px"></div>';
                    $this->enqueue_infinite_script( $instance_id, $s, $query->max_num_pages );
                }
            }
            ?>
        </div>

        <style>
            .olo-queryloop { width: 100%; }
            .olo-ql-card { overflow: hidden; border-radius: 6px; }
            .olo-ql-card--shadow { box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
            .olo-ql-card--border { border: 1px solid var(--olo-color-border, #E5E7EB); }
            .olo-ql-card--filled { background: var(--olo-color-muted, #F3F4F6); }
            .olo-ql-img { width: 100%; background-size: cover; background-position: center; }
            .olo-ql-img-auto { width: 100%; height: auto; display: block; }
            .olo-ql-img-link { display: block; text-decoration: none; }
            .olo-ql-body { padding: 16px; }
            .olo-ql-cat { display: inline-block; font-size: 11px; text-transform: uppercase; font-weight: 600; color: var(--olo-color-primary, #6366F1); margin-bottom: 4px; }
            .olo-ql-title { font-size: 1.1em; font-weight: 600; margin: 0 0 6px; line-height: 1.3; }
            .olo-ql-title a { text-decoration: none; color: inherit; }
            .olo-ql-title a:hover { text-decoration: underline; }
            .olo-ql-meta { font-size: 0.82em; color: var(--olo-color-text-muted, #9CA3AF); margin-bottom: 8px; display: flex; gap: 8px; }
            .olo-ql-excerpt { font-size: 0.92em; color: var(--olo-color-text-muted, #9CA3AF); line-height: 1.6; margin: 0 0 10px; }
            .olo-ql-readmore { font-size: 0.88em; font-weight: 500; text-decoration: none; }
            .olo-ql-readmore:hover { text-decoration: underline; }
            .olo-ql-pagination { margin-top: 24px; text-align: center; }
            .olo-ql-pagination .page-numbers { display: inline-flex; list-style: none; padding: 0; margin: 0; gap: 4px; }
            .olo-ql-pagination .page-numbers li a,
            .olo-ql-pagination .page-numbers li span { display: inline-flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; padding: 0 8px; border-radius: 4px; font-size: 14px; text-decoration: none; background: var(--olo-color-muted, #F3F4F6); color: var(--olo-color-text, #374151); }
            .olo-ql-pagination .page-numbers li .current { background: var(--olo-color-primary, #6366F1); color: var(--olo-color-primary-contrast, #FFFFFF); }
            .olo-ql-loadmore-btn { padding: 10px 28px; font-size: 14px; border: 1px solid var(--olo-color-border, #E5E7EB); border-radius: 6px; background: var(--olo-color-background, #FFFFFF); color: var(--olo-color-text, #374151); cursor: pointer; transition: background 0.2s; }
            .olo-ql-loadmore-btn:hover { background: var(--olo-color-muted, #F3F4F6); }
            .olo-ql-loadmore-btn:disabled { opacity: 0.5; cursor: not-allowed; }
            <?php if ( $layout === 'masonry' ) : ?>
            .olo-ql-container .olo-ql-card { break-inside: avoid; margin-bottom: <?php echo $gap; ?>px; }
            <?php endif; ?>
            @media (max-width: 640px) {
                <?php if ( $layout === 'grid' ) : ?>
                #<?php echo esc_attr( $instance_id ); ?> .olo-ql-container { grid-template-columns: 1fr !important; }
                <?php elseif ( $layout === 'masonry' ) : ?>
                #<?php echo esc_attr( $instance_id ); ?> .olo-ql-container { column-count: 1 !important; }
                <?php endif; ?>
            }
        </style>
        <?php
        return ob_get_clean();
    }

    /**
     * Enqueue inline AJAX script for load-more button.
     * NOTA: NON usare && negli script inline — WordPress converte in entità HTML.
     */
    private function enqueue_ajax_script( $instance_id, $s, $max_pages ) {
        $ajax_url = admin_url( 'admin-ajax.php' );
        $settings_json = wp_json_encode( $s );
        // NOTA CRITICA: usare if annidati al posto di && per evitare che WordPress rompa lo script
        ?>
        <script>
        (function(){
            var container = document.querySelector('#<?php echo esc_js( $instance_id ); ?> .olo-ql-container');
            var btn = document.querySelector('[data-ql-id="<?php echo esc_js( $instance_id ); ?>"].olo-ql-loadmore-btn');
            if(btn){
                btn.addEventListener('click', function(){
                    var page = parseInt(btn.getAttribute('data-ql-page')) + 1;
                    var maxP = parseInt(btn.getAttribute('data-ql-max'));
                    if(page > maxP) return;
                    btn.disabled = true;
                    btn.textContent = 'Caricamento...';
                    var fd = new FormData();
                    fd.append('action', 'olo_queryloop_page');
                    fd.append('page', page);
                    fd.append('settings', '<?php echo esc_js( $settings_json ); ?>');
                    fd.append('nonce', '<?php echo esc_js( wp_create_nonce( 'olo_ql_nonce' ) ); ?>');
                    fetch('<?php echo esc_js( $ajax_url ); ?>', { method: 'POST', body: fd })
                        .then(function(r){ return r.json(); })
                        .then(function(data){
                            if(data.success){
                                if(container){
                                    var tmp = document.createElement('div');
                                    tmp.innerHTML = data.data.html;
                                    while(tmp.firstChild){
                                        container.appendChild(tmp.firstChild);
                                    }
                                }
                                btn.setAttribute('data-ql-page', page);
                                if(page >= maxP){
                                    btn.parentNode.style.display = 'none';
                                } else {
                                    btn.disabled = false;
                                    btn.textContent = 'Carica altro';
                                }
                            }
                        });
                });
            }
        })();
        </script>
        <?php
    }

    /**
     * Enqueue inline script for infinite scroll.
     * NOTA: NON usare && negli script inline — WordPress converte in entità HTML.
     */
    private function enqueue_infinite_script( $instance_id, $s, $max_pages ) {
        $ajax_url = admin_url( 'admin-ajax.php' );
        $settings_json = wp_json_encode( $s );
        // NOTA CRITICA: usare if annidati al posto di && per evitare che WordPress rompa lo script
        ?>
        <script>
        (function(){
            var sentinel = document.querySelector('[data-ql-id="<?php echo esc_js( $instance_id ); ?>"].olo-ql-sentinel');
            var container = document.querySelector('#<?php echo esc_js( $instance_id ); ?> .olo-ql-container');
            if(sentinel){
                var loading = false;
                var observer = new IntersectionObserver(function(entries){
                    var entry = entries[0];
                    if(entry.isIntersecting){
                        if(loading) return;
                        var page = parseInt(sentinel.getAttribute('data-ql-page')) + 1;
                        var maxP = parseInt(sentinel.getAttribute('data-ql-max'));
                        if(page > maxP){
                            observer.disconnect();
                            sentinel.style.display = 'none';
                            return;
                        }
                        loading = true;
                        var fd = new FormData();
                        fd.append('action', 'olo_queryloop_page');
                        fd.append('page', page);
                        fd.append('settings', '<?php echo esc_js( $settings_json ); ?>');
                        fd.append('nonce', '<?php echo esc_js( wp_create_nonce( 'olo_ql_nonce' ) ); ?>');
                        fetch('<?php echo esc_js( $ajax_url ); ?>', { method: 'POST', body: fd })
                            .then(function(r){ return r.json(); })
                            .then(function(data){
                                if(data.success){
                                    if(container){
                                        var tmp = document.createElement('div');
                                        tmp.innerHTML = data.data.html;
                                        while(tmp.firstChild){
                                            container.appendChild(tmp.firstChild);
                                        }
                                    }
                                    sentinel.setAttribute('data-ql-page', page);
                                    if(page >= maxP){
                                        observer.disconnect();
                                        sentinel.style.display = 'none';
                                    }
                                }
                                loading = false;
                            });
                    }
                }, { rootMargin: '200px' });
                observer.observe(sentinel);
            }
        })();
        </script>
        <?php
    }

    /**
     * AJAX handler for loading additional pages.
     */
    public function ajax_load_page() {
        check_ajax_referer( 'olo_ql_nonce', 'nonce' );

        $page = absint( $_POST['page'] ?? 1 );
        $settings_raw = $_POST['settings'] ?? '';
        $s = json_decode( stripslashes( $settings_raw ), true );
        if ( ! is_array( $s ) ) {
            wp_send_json_error( [ 'message' => 'Invalid settings' ] );
        }

        $s = wp_parse_args( $s, $this->defaults );

        $query_args = $this->build_query_args( $s, $page );
        $query = new WP_Query( $query_args );

        if ( ! $query->have_posts() ) {
            wp_send_json_success( [ 'html' => '', 'has_more' => false ] );
        }

        $html = $this->render_cards( $query->posts, $s );
        $has_more = ( $page < $query->max_num_pages );

        wp_send_json_success( [
            'html'     => $html,
            'has_more' => $has_more,
        ] );
    }
}
