<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Woo_Products_Tile extends Olo_Tile_Base {

    protected $type     = 'woo_products';
    protected $name     = 'Prodotti WC';
    protected $icon     = 'dashicons-products';
    protected $category = 'woocommerce';
    protected $defaults = [
        'posts_per_page'  => 12,
        'columns'         => 4,
        'orderby'         => 'date',
        'order'           => 'DESC',
        'category'        => '',
        'tag'             => '',
        'on_sale'         => false,
        'featured'        => false,
        'show_image'      => true,
        'show_title'      => true,
        'show_price'      => true,
        'show_rating'     => false,
        'show_add_to_cart' => true,
        'show_badge'      => true,
        'image_ratio'     => '4-3',
        'gap'             => 24,
        'card_style'      => 'shadow',
        'hover_effect'    => 'zoom',
        'title_color'     => '',
        'price_color'     => '',
        'sale_color'      => '',
        'button_color'    => '',
        'button_bg'       => '',
        'badge_bg'        => '',
        'show_compare'    => false,
        'pagination'      => false,
        'columns_tablet'  => 2,
        'columns_mobile'  => 1,
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
        if ( ! class_exists( 'WooCommerce' ) ) {
            return '<div style="padding:40px;text-align:center;color:var(--olo-color-warning, #b45309);background:color-mix(in srgb, var(--olo-color-warning, #b45309) 12%, #fff);border:1px solid var(--olo-color-warning, #b45309);border-radius:8px;">'
                 . esc_html( olo_t( 'WooCommerce non attivo. Installa e attiva WooCommerce per utilizzare questo elemento.' ) )
                 . '</div>';
        }

        $s = wp_parse_args( $settings, $this->defaults );

        $cols         = max( 1, min( 6, absint( $s['columns'] ) ) );
        $cols_tablet  = max( 1, min( 4, absint( $s['columns_tablet'] ) ) );
        $cols_mobile  = max( 1, min( 2, absint( $s['columns_mobile'] ) ) );
        $gap          = absint( $s['gap'] );
        $uid          = 'olo-woo-prod-' . wp_rand( 10000, 99999 );

        // Colors
        $title_color  = $this->safe_color_css( $s['title_color'] );
        $price_color  = $this->safe_color_css( $s['price_color'] );
        $sale_color   = $this->safe_color_css( $s['sale_color'] );
        $btn_color    = $this->safe_color_css( $s['button_color'] );
        $btn_bg       = $this->safe_color_css( $s['button_bg'] );
        $badge_bg     = $this->safe_color_css( $s['badge_bg'] );

        // Ratio map
        $ratio_map = [
            '1-1'  => '100%',
            '4-3'  => '75%',
            '3-4'  => '133.33%',
            '16-9' => '56.25%',
            'auto' => '0',
        ];
        $ratio     = $s['image_ratio'];
        $ratio_val = isset( $ratio_map[ $ratio ] ) ? $ratio_map[ $ratio ] : '75%';
        $auto_h    = ( $ratio === 'auto' );

        // Card style
        $card_style = $s['card_style'];
        $card_extra = '';
        if ( $card_style === 'shadow' ) {
            $card_extra = 'box-shadow:0 1px 3px rgba(0,0,0,0.1),0 1px 2px rgba(0,0,0,0.06);';
        } elseif ( $card_style === 'border' ) {
            $card_extra = 'border:1px solid var(--olo-color-border, #E5E7EB);';
        }

        // Hover effect
        $hover_effect = $s['hover_effect'];

        // WP_Query args
        $query_args = [
            'post_type'      => 'product',
            'posts_per_page' => absint( $s['posts_per_page'] ),
            'post_status'    => 'publish',
            'orderby'        => sanitize_text_field( $s['orderby'] ),
            'order'          => in_array( $s['order'], [ 'ASC', 'DESC' ], true ) ? $s['order'] : 'DESC',
        ];

        // Orderby mapping for WooCommerce
        if ( $s['orderby'] === 'price' ) {
            $query_args['meta_key'] = '_price';
            $query_args['orderby']  = 'meta_value_num';
        } elseif ( $s['orderby'] === 'popularity' ) {
            $query_args['meta_key'] = 'total_sales';
            $query_args['orderby']  = 'meta_value_num';
        } elseif ( $s['orderby'] === 'rating' ) {
            $query_args['meta_key'] = '_wc_average_rating';
            $query_args['orderby']  = 'meta_value_num';
        }

        // Tax query
        $tax_query = [ 'relation' => 'AND' ];
        $cat_slug  = sanitize_text_field( $s['category'] );
        if ( $cat_slug !== '' ) {
            $tax_query[] = [
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => array_map( 'trim', explode( ',', $cat_slug ) ),
            ];
        }
        $tag_slug = sanitize_text_field( $s['tag'] );
        if ( $tag_slug !== '' ) {
            $tax_query[] = [
                'taxonomy' => 'product_tag',
                'field'    => 'slug',
                'terms'    => array_map( 'trim', explode( ',', $tag_slug ) ),
            ];
        }
        if ( ! empty( $s['featured'] ) ) {
            $tax_query[] = [
                'taxonomy' => 'product_visibility',
                'field'    => 'name',
                'terms'    => 'featured',
            ];
        }
        if ( count( $tax_query ) > 1 ) {
            $query_args['tax_query'] = $tax_query;
        }

        // On sale
        if ( ! empty( $s['on_sale'] ) ) {
            $sale_ids = wc_get_product_ids_on_sale();
            if ( ! empty( $sale_ids ) ) {
                $query_args['post__in'] = $sale_ids;
            } else {
                // No products on sale
                return '<div style="padding:40px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF)">'
                     . esc_html( olo_t( 'Nessun prodotto in saldo' ) )
                     . '</div>';
            }
        }

        // Pagination
        if ( ! empty( $s['pagination'] ) ) {
            $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
            $query_args['paged'] = $paged;
        }

        $products = new WP_Query( $query_args );

        if ( ! $products->have_posts() ) {
            return '<div style="padding:40px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF)">'
                 . esc_html( olo_t( 'Nessun prodotto trovato' ) )
                 . '</div>';
        }

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above (safe_color_css/absint/fixed maps/generated uid). ?>
        <style>
            .<?php echo $uid; ?> {
                display: grid;
                grid-template-columns: repeat(<?php echo (int) $cols; ?>, 1fr);
                gap: <?php echo (int) $gap; ?>px;
            }
            .<?php echo $uid; ?> .olo-woo-card {
                background: var(--olo-color-background, #FFFFFF);
                border-radius: 8px;
                overflow: hidden;
                <?php echo $card_extra; ?>
                transition: box-shadow 0.3s ease, transform 0.3s ease;
            }
            <?php if ( $hover_effect === 'shadow' ) : ?>
            .<?php echo $uid; ?> .olo-woo-card:hover {
                box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            }
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-woo-card-img {
                position: relative;
                overflow: hidden;
                <?php if ( ! $auto_h ) : ?>
                padding-top: <?php echo $ratio_val; ?>;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-woo-card-img img {
                <?php if ( ! $auto_h ) : ?>
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
                <?php else : ?>
                width: 100%;
                height: auto;
                display: block;
                <?php endif; ?>
                transition: transform 0.4s ease;
            }
            <?php if ( $hover_effect === 'zoom' ) : ?>
            .<?php echo $uid; ?> .olo-woo-card:hover .olo-woo-card-img img {
                transform: scale(1.06);
            }
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-woo-card-badge {
                position: absolute;
                top: 8px;
                left: 8px;
                background: <?php echo $badge_bg; ?>;
                color: #fff;
                font-size: 11px;
                font-weight: 700;
                padding: 3px 8px;
                border-radius: 4px;
                z-index: 2;
            }
            .<?php echo $uid; ?> .olo-woo-card-body {
                padding: 14px;
            }
            .<?php echo $uid; ?> .olo-woo-card-title {
                margin: 0 0 6px;
                font-size: 15px;
                font-weight: 600;
                line-height: 1.3;
            }
            .<?php echo $uid; ?> .olo-woo-card-title a {
                color: <?php echo $title_color; ?>;
                text-decoration: none;
            }
            .<?php echo $uid; ?> .olo-woo-card-title a:hover {
                text-decoration: underline;
            }
            .<?php echo $uid; ?> .olo-woo-card-rating {
                display: flex;
                align-items: center;
                gap: 2px;
                margin-bottom: 6px;
            }
            .<?php echo $uid; ?> .olo-woo-card-price {
                font-size: 15px;
                font-weight: 700;
                color: <?php echo $price_color; ?>;
                margin-bottom: 10px;
            }
            .<?php echo $uid; ?> .olo-woo-card-price del {
                color: var(--olo-color-text-muted, #9CA3AF);
                font-weight: 400;
                font-size: 13px;
                margin-right: 6px;
            }
            .<?php echo $uid; ?> .olo-woo-card-price ins {
                text-decoration: none;
                color: <?php echo $sale_color; ?>;
            }
            .<?php echo $uid; ?> .olo-woo-btn {
                display: inline-block;
                width: 100%;
                padding: 9px 16px;
                background: <?php echo $btn_bg; ?>;
                color: <?php echo $btn_color; ?>;
                border: none;
                border-radius: 4px;
                font-size: 13px;
                font-weight: 600;
                text-align: center;
                text-decoration: none;
                cursor: pointer;
                transition: opacity 0.2s ease;
            }
            .<?php echo $uid; ?> .olo-woo-btn:hover {
                opacity: 0.9;
            }
            @media (max-width: 960px) {
                .<?php echo $uid; ?> {
                    grid-template-columns: repeat(<?php echo (int) $cols_tablet; ?>, 1fr);
                }
            }
            @media (max-width: 640px) {
                .<?php echo $uid; ?> {
                    grid-template-columns: repeat(<?php echo (int) $cols_mobile; ?>, 1fr);
                }
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="<?php echo esc_attr( $uid ); ?>">
        <?php
        while ( $products->have_posts() ) :
            $products->the_post();
            global $product;
            if ( ! is_a( $product, 'WC_Product' ) ) {
                $product = wc_get_product( get_the_ID() );
            }
            if ( ! $product ) {
                continue;
            }

            $permalink  = get_permalink();
            $title      = get_the_title();
            $price_html = $product->get_price_html();
            $on_sale    = $product->is_on_sale();
            $avg_rating = $product->get_average_rating();
            $thumb_id   = get_post_thumbnail_id();
        ?>
            <div class="olo-woo-card" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
                <?php if ( ! empty( $s['show_image'] ) ) : ?>
                <div class="olo-woo-card-img">
                    <?php if ( $thumb_id ) : ?>
                    <a href="<?php echo esc_url( $permalink ); ?>">
                        <?php echo wp_get_attachment_image( $thumb_id, 'woocommerce_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() returns escaped HTML built by WordPress core ?>
                    </a>
                    <?php else : ?>
                    <a href="<?php echo esc_url( $permalink ); ?>" style="display:block;background:var(--olo-color-muted, #F3F4F6);<?php if ( ! $auto_h ) : ?>position:absolute;inset:0;<?php else : ?>padding:40px 0;<?php endif; ?>display:flex;align-items:center;justify-content:center;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--olo-color-border, #E5E7EB)" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                    </a>
                    <?php endif; ?>
                    <?php
                    if ( ! empty( $s['show_badge'] ) ) {
                        if ( $on_sale ) {
                            $regular = (float) $product->get_regular_price();
                            $sale    = (float) $product->get_sale_price();
                            if ( $regular > 0 ) {
                                $pct = round( ( ( $regular - $sale ) / $regular ) * 100 );
                                echo '<div class="olo-woo-card-badge">-' . absint( $pct ) . '%</div>';
                            }
                        }
                    }
                    ?>
                </div>
                <?php endif; ?>
                <div class="olo-woo-card-body">
                    <?php if ( ! empty( $s['show_title'] ) ) : ?>
                    <div class="olo-woo-card-title">
                        <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
                    </div>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['show_rating'] ) ) : ?>
                    <div class="olo-woo-card-rating">
                        <?php
                        $full  = floor( (float) $avg_rating );
                        $half  = ( ( (float) $avg_rating - $full ) >= 0.5 ) ? 1 : 0;
                        $empty = 5 - $full - $half;
                        $star_svg_full  = '<svg width="14" height="14" viewBox="0 0 24 24" fill="var(--olo-color-warning, #F59E0B)" stroke="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
                        $star_svg_half  = '<svg width="14" height="14" viewBox="0 0 24 24" stroke="none"><defs><linearGradient id="h"><stop offset="50%" stop-color="var(--olo-color-warning, #F59E0B)"/><stop offset="50%" stop-color="var(--olo-color-border, #E5E7EB)"/></linearGradient></defs><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="url(#h)"/></svg>';
                        $star_svg_empty = '<svg width="14" height="14" viewBox="0 0 24 24" fill="var(--olo-color-border, #E5E7EB)" stroke="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
                        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup defined as literals above, no dynamic data
                        for ( $i = 0; $i < $full; $i++ ) { echo $star_svg_full; }
                        for ( $i = 0; $i < $half; $i++ ) { echo $star_svg_half; }
                        for ( $i = 0; $i < $empty; $i++ ) { echo $star_svg_empty; }
                        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
                        ?>
                    </div>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['show_price'] ) ) : ?>
                    <div class="olo-woo-card-price"><?php echo $price_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WooCommerce price HTML from WC_Product::get_price_html(), escaped by WooCommerce core ?></div>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['show_add_to_cart'] ) ) : ?>
                    <?php
                    $add_url = $product->add_to_cart_url();
                    $add_text = $product->is_type( 'simple' ) ? olo_t( 'Aggiungi al carrello' ) : olo_t( 'Seleziona opzioni' );
                    ?>
                    <a href="<?php echo esc_url( $add_url ); ?>" class="olo-woo-btn"
                       data-product_id="<?php echo absint( $product->get_id() ); ?>"
                       data-quantity="1"
                       <?php if ( $product->is_type( 'simple' ) ) : ?>
                       data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
                       class="olo-woo-btn ajax_add_to_cart add_to_cart_button"
                       <?php endif; ?>
                    ><?php echo esc_html( $add_text ); ?></a>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['show_compare'] ) ) : ?>
                    <button type="button" class="olo-woo-btn olo-woo-compare-btn" style="margin-top:6px;background:transparent;border:1px solid <?php echo esc_attr( $btn_bg ); ?>;color:<?php echo esc_attr( $btn_bg ); ?>" onclick="var id=<?php echo absint( $product->get_id() ); ?>;var K='olo_compare_ids';var ids;try{ids=JSON.parse(localStorage.getItem(K)||'[]')}catch(e){ids=[]}if(ids.indexOf(id)===-1){ids.push(id);localStorage.setItem(K,JSON.stringify(ids));this.textContent='<?php echo esc_js( olo_t( 'Aggiunto!' ) ); ?>';var b=this;setTimeout(function(){b.textContent='<?php echo esc_js( olo_t( 'Confronta' ) ); ?>'},1500)}">
                        <?php echo esc_html( olo_t( 'Confronta' ) ); ?>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
        </div>
        <?php
        // Pagination
        if ( ! empty( $s['pagination'] ) ) {
            $big = 999999999;
            echo '<div style="text-align:center;margin-top:20px;">';
            echo paginate_links( [ // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- paginate_links() returns escaped pagination HTML built by WordPress core
                'base'    => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                'format'  => '?paged=%#%',
                'current' => max( 1, get_query_var( 'paged' ) ),
                'total'   => $products->max_num_pages,
            ] );
            echo '</div>';
        }

        wp_reset_postdata();
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS built by Olo_Tile_Base::build_border_css() from sanitized values (intval/safe color whitelist)
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS built by Olo_Tile_Base border helpers from sanitized values
        }
        return ob_get_clean();
    }
}
