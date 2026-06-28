<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_RelatedPosts_Tile extends Olobuild_Tile_Base {

    protected $type     = 'relatedposts';
    protected $name     = 'Articoli Correlati';
    protected $icon     = 'dashicons-screenoptions';
    protected $category = 'dynamic';
    protected $defaults = [
        'preset' => 'custom',
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
        'title_color'        => '',
        'text_color'         => '',
        'date_color'         => '',
        'card_background'    => '',
        'card_padding'       => '16',
        'card_border_radius' => '8',
        'hover_effect'       => 'shadow',
        'fallback_text'      => 'Nessun articolo correlato',
        'orderby'            => 'rand',
            'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
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
            // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in -- esclusione post necessaria alla funzione del tile; query a volume limitato
            'post__not_in'           => [ $post_id ],
            'post_status'            => 'publish',
            'tax_query'              => $tax_queries, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- query per articoli correlati (categorie/tag condivisi); tax query necessaria alla funzione del tile, volume limitato (max 12 post)
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
        $card_padding = Olobuild_Tile_Utils::spacing_css( $s['tile_padding'] ?? $s['card_padding'] ?? 16, 16 );
        $card_radius   = Olobuild_Tile_Utils::border_radius( $s['card_border_radius'] ?? 0 );
        $card_radius_hover_css = Olobuild_Tile_Utils::radius_force_css( $s['card_border_radius_hover'] ?? null );
        // TOKEN-FIRST: neutri → token tema (vuoto = default brand)
        $title_color   = $this->safe_color_css( $s['title_color'] ) ?: 'var(--olo-color-text, #374151)';
        $text_color    = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text-faint, #9CA3AF)';
        $date_color    = $this->safe_color_css( $s['date_color'] ) ?: 'var(--olo-color-text-soft, #6B7280)';
        $image_ratio   = in_array( $s['image_ratio'], [ '16/9', '4/3', '1/1', 'auto' ], true ) ? $s['image_ratio'] : '16/9';
        $hover_effect  = $s['hover_effect'];
        $excerpt_len   = max( 5, min( 50, absint( $s['excerpt_length'] ) ) );

        $allowed_tags = [ 'h3', 'h4', 'h5', 'p' ];
        $title_tag    = in_array( $s['title_tag'], $allowed_tags, true ) ? $s['title_tag'] : 'h4';
        $title_size   = $title_tag === 'h3' ? '1.1em' : '1em';

        $uid = 'olo-rp-' . wp_rand( 10000, 99999 );

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: absint() clamps for columns/gap, safe_color_css() for the card background, Olobuild_Tile_Utils radius helpers and the internally generated $uid. ?>
        <style>
            .<?php echo $uid; ?> { display: grid; grid-template-columns: repeat(<?php echo $columns; ?>, 1fr); gap: <?php echo $gap; ?>px; }
            @media (max-width: 640px) { .<?php echo $uid; ?> { grid-template-columns: 1fr; } }
            .<?php echo $uid; ?> .olo-rp-card { background: <?php echo $card_bg; ?>; border-radius: <?php echo $card_radius; ?>; overflow: hidden; transition: transform 0.3s ease, box-shadow 0.3s ease, border-radius 400ms cubic-bezier(.4,0,.2,1); text-decoration: none; display: block; }
            <?php if ( $card_radius_hover_css !== '' ) : ?>.<?php echo $uid; ?> .olo-rp-card:hover{border-radius:<?php echo $card_radius_hover_css; ?> !important}<?php endif; ?>
            .<?php echo $uid; ?> .olo-rp-card:hover { text-decoration: none; }
            .<?php echo $uid; ?> .olo-rp-card:focus-visible { outline: none; box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent); }
            <?php if ( $hover_effect === 'shadow' ) : ?>
            .<?php echo $uid; ?> .olo-rp-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.3); }
            <?php elseif ( $hover_effect === 'scale' ) : ?>
            .<?php echo $uid; ?> .olo-rp-card:hover { transform: scale(1.03); }
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-rp-card img { width: 100%; height: 100%; object-fit: cover; display: block; }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-relatedposts <?php echo esc_attr( $uid ); ?> olo-rp-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>">
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
            <a href="<?php the_permalink(); ?>" class="olo-rp-card">
                <?php if ( ! empty( $s['show_image'] ) ) : ?>
                <div style="<?php echo $image_ratio !== 'auto' ? 'aspect-ratio:' . esc_attr( $image_ratio ) . ';' : 'height:160px;'; ?>overflow:hidden;background:#1F2937;">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <?php the_post_thumbnail( 'medium_large', [ 'style' => 'width:100%;height:100%;object-fit:cover;display:block;' ] ); ?>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div style="padding: <?php echo $card_padding; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- spacing built by Olobuild_Tile_Utils::spacing_css() from intval()'d values ?>;">
                    <?php if ( ! empty( $s['show_category'] ) ) :
                        $post_cats = get_the_category();
                        if ( $post_cats ) :
                    ?>
                        <span style="font-size:11px;text-transform:uppercase;letter-spacing:0.5px;opacity:0.7;color:<?php echo $text_color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- safe_color_css() whitelisted color or fixed var() fallback ?>;"><?php echo esc_html( $post_cats[0]->name ); ?></span>
                    <?php endif; endif; ?>

                    <<?php echo tag_escape( $title_tag ); ?> style="margin:4px 0 0;font-weight:600;line-height:1.3;font-size:<?php echo $title_size; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed literal ternary ('1.1em'/'1em'); color via safe_color_css() or fixed var() fallback ?>;color:<?php echo $title_color; ?>;">
                        <?php the_title(); ?>
                    </<?php echo tag_escape( $title_tag ); ?>>

                    <?php if ( ! empty( $s['show_date'] ) ) : ?>
                    <div style="margin-top:6px;font-size:0.85em;color:<?php echo $date_color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- safe_color_css() whitelisted color or fixed var() fallback ?>;">
                        <?php echo get_the_date(); ?>
                    </div>
                    <?php endif; ?>

                    <?php if ( ! empty( $s['show_excerpt'] ) ) : ?>
                    <p style="margin:8px 0 0;font-size:0.9em;line-height:1.5;color:<?php echo $text_color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- safe_color_css() whitelisted color or fixed var() fallback ?>;">
                        <?php echo wp_trim_words( get_the_excerpt(), $excerpt_len, '&hellip;' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_trim_words() strips all tags internally and appends the literal &hellip; entity ?>
                    </p>
                    <?php endif; ?>
                </div>
            </a>
            <?php endwhile; ?>
        </div>
        <?php
        wp_reset_postdata();
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }

    /**
     * Render fallback message when no related posts found.
     */
    private function render_fallback( $s ) {
        $fallback = $s['fallback_text'] ?? 'Nessun articolo correlato';
        $text_color = $this->safe_color_css( $s['text_color'] );
        return '<p class="olo-relatedposts-empty" style="text-align:center;color:' . $text_color . ';padding:20px;opacity:0.7;">' . esc_html( olobuild_t( $fallback ) ) . '</p>';
    }
}
