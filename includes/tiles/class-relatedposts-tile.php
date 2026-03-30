<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_RelatedPosts_Tile extends Olo_Tile_Base {

    protected $type     = 'relatedposts';
    protected $name     = 'Articoli Correlati';
    protected $icon     = 'dashicons-screenoptions';
    protected $category = 'dynamic';
    protected $defaults = [
        'source'             => 'categories',
        'count'              => '3',
        'columns'            => '3',
        'show_image'         => true,
        'show_date'          => true,
        'show_excerpt'       => false,
        'show_category'      => false,
        'excerpt_length'     => '20',
        'image_ratio'        => '16/9',
        'gap'                => '20',
        'title_tag'          => 'h4',
        'title_color'        => '#F3F4F6',
        'text_color'         => '#9CA3AF',
        'date_color'         => '#6B7280',
        'card_background'    => '',
        'card_padding'       => '16',
        'card_border_radius' => '8',
        'hover_effect'       => 'shadow',
        'fallback_text'      => 'Nessun articolo correlato',
        'orderby'            => 'rand',
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $post_id = get_the_ID();
        if ( ! $post_id ) {
            return $this->render_fallback( $s );
        }

        // Build tax_query based on source
        $tax_queries = [];

        $source = $s['source'];
        if ( $source === 'categories' || $source === 'both' ) {
            $cats = wp_get_post_categories( $post_id, [ 'fields' => 'ids' ] );
            if ( ! empty( $cats ) ) {
                $tax_queries[] = [
                    'taxonomy' => 'category',
                    'field'    => 'term_id',
                    'terms'    => $cats,
                ];
            }
        }

        if ( $source === 'tags' || $source === 'both' ) {
            $tags = wp_get_post_tags( $post_id, [ 'fields' => 'ids' ] );
            if ( ! empty( $tags ) ) {
                $tax_queries[] = [
                    'taxonomy' => 'post_tag',
                    'field'    => 'term_id',
                    'terms'    => $tags,
                ];
            }
        }

        if ( empty( $tax_queries ) ) {
            return $this->render_fallback( $s );
        }

        // If both, use OR relation
        if ( count( $tax_queries ) > 1 ) {
            $tax_queries['relation'] = 'OR';
        }

        $count   = max( 1, min( 12, absint( $s['count'] ) ) );
        $orderby = in_array( $s['orderby'], [ 'rand', 'date', 'title' ], true ) ? $s['orderby'] : 'rand';
        $order   = $orderby === 'title' ? 'ASC' : 'DESC';

        $query_args = [
            'post_type'              => get_post_type( $post_id ),
            'posts_per_page'         => $count,
            'post__not_in'           => [ $post_id ],
            'post_status'            => 'publish',
            'tax_query'              => $tax_queries,
            'orderby'                => $orderby,
            'order'                  => $order,
            'no_found_rows'          => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => true,
        ];

        $query = new WP_Query( $query_args );

        if ( ! $query->have_posts() ) {
            wp_reset_postdata();
            return $this->render_fallback( $s );
        }

        update_post_thumbnail_cache( $query );

        // Layout values
        $columns       = max( 1, min( 4, absint( $s['columns'] ) ) );
        $gap           = max( 0, min( 40, absint( $s['gap'] ) ) );
        $card_bg       = $this->safe_color_css( $s['card_background'] );
        $card_padding = Olo_Tile_Utils::spacing_css( $s['tile_padding'] ?? $s['card_padding'] ?? 16, 16 );
        $card_radius   = Olo_Tile_Utils::border_radius( $s['card_border_radius'] ?? 0 );
        $title_color   = $this->safe_color_css( $s['title_color'] );
        $text_color    = $this->safe_color_css( $s['text_color'] );
        $date_color    = $this->safe_color_css( $s['date_color'] );
        $image_ratio   = in_array( $s['image_ratio'], [ '16/9', '4/3', '1/1', 'auto' ], true ) ? $s['image_ratio'] : '16/9';
        $hover_effect  = $s['hover_effect'];
        $excerpt_len   = max( 5, min( 50, absint( $s['excerpt_length'] ) ) );

        $allowed_tags = [ 'h3', 'h4', 'h5', 'p' ];
        $title_tag    = in_array( $s['title_tag'], $allowed_tags, true ) ? $s['title_tag'] : 'h4';
        $title_size   = $title_tag === 'h3' ? '1.1em' : '1em';

        $uid = 'olo-rp-' . wp_rand( 10000, 99999 );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> { display: grid; grid-template-columns: repeat(<?php echo $columns; ?>, 1fr); gap: <?php echo $gap; ?>px; }
            @media (max-width: 640px) { .<?php echo $uid; ?> { grid-template-columns: 1fr; } }
            .<?php echo $uid; ?> .olo-rp-card { background: <?php echo $card_bg; ?>; border-radius: <?php echo $card_radius; ?>; overflow: hidden; transition: transform 0.3s ease, box-shadow 0.3s ease; text-decoration: none; display: block; }
            .<?php echo $uid; ?> .olo-rp-card:hover { text-decoration: none; }
            <?php if ( $hover_effect === 'shadow' ) : ?>
            .<?php echo $uid; ?> .olo-rp-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.3); }
            <?php elseif ( $hover_effect === 'scale' ) : ?>
            .<?php echo $uid; ?> .olo-rp-card:hover { transform: scale(1.03); }
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-rp-card img { width: 100%; height: 100%; object-fit: cover; display: block; }
        </style>
        <div class="olo-relatedposts <?php echo $uid; ?>">
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
            <a href="<?php the_permalink(); ?>" class="olo-rp-card">
                <?php if ( ! empty( $s['show_image'] ) ) : ?>
                <div style="<?php echo $image_ratio !== 'auto' ? 'aspect-ratio:' . esc_attr( $image_ratio ) . ';' : 'height:160px;'; ?>overflow:hidden;background:var(--olo-color-secondary, #1F2937);">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <?php the_post_thumbnail( 'medium_large', [ 'style' => 'width:100%;height:100%;object-fit:cover;display:block;' ] ); ?>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div style="padding: <?php echo $card_padding; ?>;">
                    <?php if ( ! empty( $s['show_category'] ) ) :
                        $post_cats = get_the_category();
                        if ( $post_cats ) :
                    ?>
                        <span style="font-size:11px;text-transform:uppercase;letter-spacing:0.5px;opacity:0.7;color:<?php echo $text_color; ?>;"><?php echo esc_html( $post_cats[0]->name ); ?></span>
                    <?php endif; endif; ?>

                    <<?php echo $title_tag; ?> style="margin:4px 0 0;font-weight:600;line-height:1.3;font-size:<?php echo $title_size; ?>;color:<?php echo $title_color; ?>;">
                        <?php the_title(); ?>
                    </<?php echo $title_tag; ?>>

                    <?php if ( ! empty( $s['show_date'] ) ) : ?>
                    <div style="margin-top:6px;font-size:0.85em;color:<?php echo $date_color; ?>;">
                        <?php echo get_the_date(); ?>
                    </div>
                    <?php endif; ?>

                    <?php if ( ! empty( $s['show_excerpt'] ) ) : ?>
                    <p style="margin:8px 0 0;font-size:0.9em;line-height:1.5;color:<?php echo $text_color; ?>;">
                        <?php echo wp_trim_words( get_the_excerpt(), $excerpt_len, '&hellip;' ); ?>
                    </p>
                    <?php endif; ?>
                </div>
            </a>
            <?php endwhile; ?>
        </div>
        <?php
        wp_reset_postdata();
        return ob_get_clean();
    }

    /**
     * Render fallback message when no related posts found.
     */
    private function render_fallback( $s ) {
        $fallback = $s['fallback_text'] ?? 'Nessun articolo correlato';
        $text_color = $this->safe_color_css( $s['text_color'] );
        return '<p class="olo-relatedposts-empty" style="text-align:center;color:' . $text_color . ';padding:20px;opacity:0.7;">' . esc_html( olo_t( $fallback ) ) . '</p>';
    }
}
