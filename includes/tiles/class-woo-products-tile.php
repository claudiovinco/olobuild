<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Woo_Products_Tile extends Olobuild_Tile_Base {

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
                 . esc_html( olobuild_t( 'WooCommerce non attivo. Installa e attiva WooCommerce per utilizzare questo elemento.' ) )
                 . '</div>';
        }

        $s = wp_parse_args( $settings, $this->defaults );

        $cols         = max( 1, min( 6, absint( $s['columns'] ) ) );
        $cols_tablet  = max( 1, min( 4, absint( $s['columns_tablet'] ) ) );
        $cols_mobile  = max( 1, min( 2, absint( $s['columns_mobile'] ) ) );
        $gap          = absint( $s['gap'] );
        $uid          = 'olo-woo-prod-' . wp_rand( 10000, 99999 );

        // Colors — tutti vuoti nei default: senza riserva uscivano «background: ;» e «color: ;»,
        // che il browser scarta. Il bollo dello sconto restava testo bianco senza fondo (invisibile),
        // il pulsante un link senza fondo, i nomi del colore dei link. Riserve = la partenza del config.
        $title_color  = $this->safe_color_css( $s['title_color'] ) ?: 'var(--olo-color-text, #111827)';
        $price_color  = $this->safe_color_css( $s['price_color'] ) ?: 'var(--olo-color-text, #111827)';
        $sale_color   = $this->safe_color_css( $s['sale_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $btn_color    = $this->safe_color_css( $s['button_color'] ) ?: 'var(--olo-color-primary-contrast, #ffffff)';
        $btn_bg       = $this->safe_color_css( $s['button_bg'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $badge_bg     = $this->safe_color_css( $s['badge_bg'] ) ?: 'var(--olo-color-dark, #111827)';

        // Modalità «Carosello» (config: layout + carousel_*): prima nessuna di queste chiavi era
        // letta e la tile restava sempre una griglia. Il carosello è uno scorrimento orizzontale
        // con aggancio (scroll-snap): si trascina col dito anche senza script, frecce e pallini
        // lo comandano. Gli interruttori assenti valgono come i default del config.
        $is_carousel  = ( ( $s['layout'] ?? 'grid' ) === 'carousel' );
        $car_autoplay = filter_var( $s['carousel_autoplay'] ?? false, FILTER_VALIDATE_BOOLEAN );
        $car_speed    = max( 1000, min( 20000, absint( $s['carousel_speed'] ?? 4000 ) ) );
        $car_loop     = filter_var( $s['carousel_loop'] ?? true, FILTER_VALIDATE_BOOLEAN );
        $car_arrows   = filter_var( $s['carousel_arrows'] ?? true, FILTER_VALIDATE_BOOLEAN );
        $car_dots     = filter_var( $s['carousel_dots'] ?? true, FILTER_VALIDATE_BOOLEAN );

        // Ratio map — traduzione del rapporto in percentuale di padding-top (tecnica
        // storica di questa tile). E' anche la whitelist: cio' che non sta qui ricade
        // sul 4:3 senza dirlo, quindi va tenuta allineata all'elenco del select
        // (woo_products.js → ratioOptions sep:'-'). Il '3-4' resta scritto '133.33%'
        // com'era: rifarlo con più decimali sposterebbe di un pelo le griglie già
        // pubblicate.
        $ratio_map = [
            '1-1'  => '100%',
            '4-3'  => '75%',
            '3-2'  => '66.67%',
            '16-9' => '56.25%',
            '21-9' => '42.86%',
            '3-4'  => '133.33%',
            '4-5'  => '125%',
            '9-16' => '177.78%',
            '2-3'  => '150%',
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
            $query_args['meta_key'] = '_price'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- ordinamento prodotti WooCommerce per prezzo; meta query necessaria alla funzione del tile, volume limitato (posts_per_page)
            $query_args['orderby']  = 'meta_value_num';
        } elseif ( $s['orderby'] === 'popularity' ) {
            $query_args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- ordinamento prodotti WooCommerce per popolarità; meta query necessaria alla funzione del tile, volume limitato (posts_per_page)
            $query_args['orderby']  = 'meta_value_num';
        } elseif ( $s['orderby'] === 'rating' ) {
            $query_args['meta_key'] = '_wc_average_rating'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- ordinamento prodotti WooCommerce per valutazione; meta query necessaria alla funzione del tile, volume limitato (posts_per_page)
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
        // Filtri dell'indirizzo: il «Filtro prodotti WC» li scrive con i nomi di WooCommerce
        // (product_cat, filter_<attributo>, min_price/max_price, più in_stock), ma nessuno li
        // leggeva fuori dagli archivi del negozio: «Filtra» ricaricava la pagina con la stessa
        // griglia. Si sommano (AND) alla query della tile.
        $meta_query = [ 'relation' => 'AND' ];
        $url_cats   = $this->filtro_url_elenco( 'product_cat' );
        if ( ! empty( $url_cats ) ) {
            $tax_query[] = [
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $url_cats,
            ];
        }
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- sola lettura dei nomi dei parametri GET pubblici del filtro prodotti; nessuna modifica di stato; ogni valore passa da filtro_url_elenco().
        foreach ( array_keys( $_GET ) as $param ) {
            if ( ! is_string( $param ) || strpos( $param, 'filter_' ) !== 0 ) {
                continue;
            }
            $attr_tax = 'pa_' . sanitize_title( substr( $param, 7 ) );
            $terms    = $this->filtro_url_elenco( $param );
            if ( ! empty( $terms ) && taxonomy_exists( $attr_tax ) ) {
                $tax_query[] = [
                    'taxonomy' => $attr_tax,
                    'field'    => 'slug',
                    'terms'    => $terms,
                ];
            }
        }
        $url_min = $this->filtro_url( 'min_price' );
        $url_max = $this->filtro_url( 'max_price' );
        if ( is_numeric( $url_min ) ) {
            $meta_query[] = [ 'key' => '_price', 'value' => (float) $url_min, 'compare' => '>=', 'type' => 'DECIMAL(10,2)' ];
        }
        if ( is_numeric( $url_max ) ) {
            $meta_query[] = [ 'key' => '_price', 'value' => (float) $url_max, 'compare' => '<=', 'type' => 'DECIMAL(10,2)' ];
        }
        if ( $this->filtro_url( 'in_stock' ) === '1' ) {
            $meta_query[] = [ 'key' => '_stock_status', 'value' => 'instock' ];
        }
        if ( count( $meta_query ) > 1 ) {
            $query_args['meta_query'] = $meta_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- prezzo e disponibilità scelti nel Filtro prodotti; meta query necessaria alla funzione del tile, volume limitato (posts_per_page)
        }

        if ( count( $tax_query ) > 1 ) {
            $query_args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- filtro prodotti per categoria/tag/featured WooCommerce; tax query necessaria alla funzione del tile, volume limitato (posts_per_page)
        }

        // On sale
        if ( ! empty( $s['on_sale'] ) ) {
            $sale_ids = wc_get_product_ids_on_sale();
            if ( ! empty( $sale_ids ) ) {
                $query_args['post__in'] = $sale_ids;
            } else {
                // No products on sale
                return '<div style="padding:40px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF)">'
                     . esc_html( olobuild_t( 'Nessun prodotto in saldo' ) )
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
                 . esc_html( olobuild_t( 'Nessun prodotto trovato' ) )
                 . '</div>';
        }

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above (safe_color_css/absint/fixed maps/generated uid). ?>
        <style>
            <?php if ( ! $is_carousel ) : ?>
            .<?php echo $uid; ?> {
                display: grid;
                grid-template-columns: repeat(<?php echo (int) $cols; ?>, 1fr);
                gap: <?php echo (int) $gap; ?>px;
            }
            <?php else : ?>
            .<?php echo $uid; ?> .olo-woo-car-stage {
                position: relative;
                display: flow-root;
            }
            /* Spazio sopra e sotto (in em: ~6 e ~28 px), ripreso col margine negativo: lo
               scorrimento orizzontale taglia anche in verticale, e l'ombra delle card (e quella
               dell'hover) sarebbe rimasta mozzata. */
            .<?php echo $uid; ?> .olo-woo-car-viewport {
                overflow-x: auto;
                overflow-y: hidden;
                scroll-snap-type: x mandatory;
                scroll-behavior: smooth;
                overscroll-behavior-x: contain;
                scrollbar-width: none;
                padding: 0.4em 0 1.75em;
                margin: -0.4em 0 -1.75em;
            }
            .<?php echo $uid; ?> .olo-woo-car-viewport::-webkit-scrollbar {
                display: none;
            }
            .<?php echo $uid; ?> .olo-woo-car-viewport:focus-visible {
                outline: 2px solid var(--olo-color-primary, #e1474f);
                outline-offset: 4px;
            }
            .<?php echo $uid; ?> .olo-woo-car-track {
                display: flex;
                gap: <?php echo (int) $gap; ?>px;
            }
            .<?php echo $uid; ?> .olo-woo-car-track > .olo-woo-card {
                flex: 0 0 calc((100% - <?php echo (int) ( $gap * ( $cols - 1 ) ); ?>px) / <?php echo (int) $cols; ?>);
                min-width: 0;
                scroll-snap-align: start;
            }
            .<?php echo $uid; ?> .olo-woo-car-arrow {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                z-index: 3;
                width: 40px;
                height: 40px;
                padding: 0;
                border: 1px solid var(--olo-color-border, #e5e7eb);
                border-radius: 50%;
                background: var(--olo-color-background, #ffffff);
                color: var(--olo-color-text, #111827);
                box-shadow: 0 2px 8px rgba(0,0,0,0.12);
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: opacity 0.2s ease;
            }
            .<?php echo $uid; ?> .olo-woo-car-prev { left: 8px; }
            .<?php echo $uid; ?> .olo-woo-car-next { right: 8px; }
            .<?php echo $uid; ?> .olo-woo-car-arrow:disabled {
                opacity: 0.35;
                cursor: default;
            }
            .<?php echo $uid; ?> .olo-woo-car-arrow:focus-visible,
            .<?php echo $uid; ?> .olo-woo-car-dot:focus-visible {
                outline: 2px solid var(--olo-color-primary, #e1474f);
                outline-offset: 2px;
            }
            .<?php echo $uid; ?> .olo-woo-car-dots {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 0.25em;
                margin-top: 1em;
            }
            .<?php echo $uid; ?> .olo-woo-car-dot {
                width: 24px;
                height: 24px;
                padding: 0;
                border: none;
                border-radius: 50%;
                background: transparent;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
            }
            .<?php echo $uid; ?> .olo-woo-car-dot::before {
                content: "";
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: var(--olo-color-border, #e5e7eb);
                transition: background 0.2s ease, transform 0.2s ease;
            }
            .<?php echo $uid; ?> .olo-woo-car-dot[aria-current="true"]::before {
                background: var(--olo-color-primary, #e1474f);
                transform: scale(1.25);
            }
            @media (prefers-reduced-motion: reduce) {
                .<?php echo $uid; ?> .olo-woo-car-viewport { scroll-behavior: auto; }
            }
            <?php endif; ?>
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
                color: var(--olo-color-light, #ffffff);
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
                <?php if ( $is_carousel ) : ?>
                .<?php echo $uid; ?> .olo-woo-car-track > .olo-woo-card {
                    flex-basis: calc((100% - <?php echo (int) ( $gap * ( $cols_tablet - 1 ) ); ?>px) / <?php echo (int) $cols_tablet; ?>);
                }
                <?php endif; ?>
            }
            @media (max-width: 640px) {
                .<?php echo $uid; ?> {
                    grid-template-columns: repeat(<?php echo (int) $cols_mobile; ?>, 1fr);
                }
                <?php if ( $is_carousel ) : ?>
                .<?php echo $uid; ?> .olo-woo-car-track > .olo-woo-card {
                    flex-basis: calc((100% - <?php echo (int) ( $gap * ( $cols_mobile - 1 ) ); ?>px) / <?php echo (int) $cols_mobile; ?>);
                }
                <?php endif; ?>
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php if ( $is_carousel ) : ?>
        <div class="<?php echo esc_attr( $uid ); ?>" id="<?php echo esc_attr( $uid ); ?>">
        <div class="olo-woo-car-stage">
        <div class="olo-woo-car-viewport" tabindex="0" role="region" aria-roledescription="<?php echo esc_attr( olobuild_t( 'carosello' ) ); ?>" aria-label="<?php echo esc_attr( olobuild_t( 'Prodotti' ) ); ?>">
        <div class="olo-woo-car-track">
        <?php else : ?>
        <div class="<?php echo esc_attr( $uid ); ?>">
        <?php endif; ?>
        <?php
        while ( $products->have_posts() ) :
            $products->the_post();
            global $product;
            if ( ! is_a( $product, 'WC_Product' ) ) {
                // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- global $product di WooCommerce, non un global definito da olobuild
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
                    $add_text = $product->is_type( 'simple' ) ? olobuild_t( 'Aggiungi al carrello' ) : olobuild_t( 'Seleziona opzioni' );
                    // Un solo attributo class: il secondo (con le classi AJAX di WooCommerce) era
                    // ignorato dal browser e il clic ricaricava la pagina invece di aggiungere al
                    // carrello. Le classi AJAX solo dove WooCommerce le usa (semplice, acquistabile,
                    // disponibile): per gli altri il link porta alla scheda.
                    $ajax_ok = $product->is_type( 'simple' ) && $product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock();
                    ?>
                    <a href="<?php echo esc_url( $add_url ); ?>" class="olo-woo-btn<?php echo $ajax_ok ? ' ajax_add_to_cart add_to_cart_button' : ''; ?>"
                       data-product_id="<?php echo absint( $product->get_id() ); ?>"
                       data-quantity="1"
                       <?php if ( $product->is_type( 'simple' ) ) : ?>
                       data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
                       <?php endif; ?>
                    ><?php echo esc_html( $add_text ); ?></a>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['show_compare'] ) ) : ?>
                    <button type="button" class="olo-woo-btn olo-woo-compare-btn" style="margin-top:6px;background:transparent;border:1px solid <?php echo esc_attr( $btn_bg ); ?>;color:<?php echo esc_attr( $btn_bg ); ?>" onclick="var id=<?php echo absint( $product->get_id() ); ?>;var K='olo_compare_ids';var ids;try{ids=JSON.parse(localStorage.getItem(K)||'[]')}catch(e){ids=[]}if(ids.indexOf(id)===-1){ids.push(id);localStorage.setItem(K,JSON.stringify(ids));this.textContent='<?php echo esc_js( olobuild_t( 'Aggiunto!' ) ); ?>';var b=this;setTimeout(function(){b.textContent='<?php echo esc_js( olobuild_t( 'Confronta' ) ); ?>'},1500)}">
                        <?php echo esc_html( olobuild_t( 'Confronta' ) ); ?>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
        <?php if ( $is_carousel ) : ?>
        </div><?php // .olo-woo-car-track ?>
        </div><?php // .olo-woo-car-viewport ?>
        <?php if ( $car_arrows ) : ?>
        <button type="button" class="olo-woo-car-arrow olo-woo-car-prev" data-olo-car-prev data-olo-interactive aria-label="<?php echo esc_attr( olobuild_t( 'Precedente' ) ); ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <button type="button" class="olo-woo-car-arrow olo-woo-car-next" data-olo-car-next data-olo-interactive aria-label="<?php echo esc_attr( olobuild_t( 'Successivo' ) ); ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
        <?php endif; ?>
        </div><?php // .olo-woo-car-stage ?>
        <?php if ( $car_dots ) : ?>
        <div class="olo-woo-car-dots" data-olo-car-dots></div>
        <?php endif; ?>
        </div>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline JS below only echoes esc_js()'d strings, (int) numbers and 'true'/'false' literals from fixed ternaries. ?>
        <script>
        (function(){
            /* Carosello dei Prodotti WC. Le pagine sono gruppi di card visibili insieme (quante
               dipende dalla larghezza: colonne, tablet, mobile), i pallini si rifanno al ridimensionamento.
               Autoplay fermo con prefers-reduced-motion, sotto il mouse, col fuoco dentro e a scheda nascosta. */
            var root = document.getElementById('<?php echo esc_js( $uid ); ?>');
            if(!root){return}
            var vp = root.querySelector('.olo-woo-car-viewport');
            if(!vp){return}
            var track = vp.querySelector('.olo-woo-car-track');
            var prevBtn = root.querySelector('[data-olo-car-prev]');
            var nextBtn = root.querySelector('[data-olo-car-next]');
            var dotsWrap = root.querySelector('[data-olo-car-dots]');
            var loop = <?php echo $car_loop ? 'true' : 'false'; ?>;
            var autoplay = <?php echo $car_autoplay ? 'true' : 'false'; ?>;
            var speed = <?php echo (int) $car_speed; ?>;
            var dotLabel = '<?php echo esc_js( olobuild_t( 'Vai al gruppo' ) ); ?>';
            var reduce = false;
            if(window.matchMedia){ reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches; }
            var timer = null, paused = false, dotCount = -1;

            function cards(){ return track ? track.children : []; }
            function step(){
                var c = cards();
                if(c.length < 2){ return vp.clientWidth || 1; }
                return (c[1].offsetLeft - c[0].offsetLeft) || 1;
            }
            function perView(){
                var c = cards();
                if(!c.length){ return 1; }
                var gap = step() - c[0].offsetWidth;
                return Math.max(1, Math.round((vp.clientWidth + gap) / step()));
            }
            function pageCount(){ return Math.max(1, Math.ceil(cards().length / perView())); }
            function maxLeft(){ return Math.max(0, vp.scrollWidth - vp.clientWidth); }
            function currentPage(){
                if(vp.scrollLeft >= maxLeft() - 2){ return pageCount() - 1; }
                return Math.min(pageCount() - 1, Math.round(vp.scrollLeft / step() / perView()));
            }
            function goPage(p){
                var n = pageCount();
                if(p >= n){ p = loop ? 0 : n - 1; }
                if(p < 0){ p = loop ? n - 1 : 0; }
                var left = Math.min(maxLeft(), p * perView() * step());
                if(vp.scrollTo){ vp.scrollTo({ left: left, behavior: reduce ? 'auto' : 'smooth' }); }
                else { vp.scrollLeft = left; }
            }
            function buildDots(){
                if(!dotsWrap){return}
                var n = pageCount();
                if(n === dotCount){return}
                dotCount = n;
                dotsWrap.innerHTML = '';
                dotsWrap.style.display = n > 1 ? '' : 'none';
                for(var i = 0; i < n; i++){
                    var b = document.createElement('button');
                    b.type = 'button';
                    b.className = 'olo-woo-car-dot';
                    b.setAttribute('data-olo-interactive', '');
                    b.setAttribute('data-page', String(i));
                    b.setAttribute('aria-label', dotLabel + ' ' + (i + 1));
                    dotsWrap.appendChild(b);
                }
            }
            function sync(){
                var p = currentPage(), n = pageCount();
                if(dotsWrap){
                    var d = dotsWrap.children;
                    for(var i = 0; i < d.length; i++){
                        if(i === p){ d[i].setAttribute('aria-current', 'true'); }
                        else { d[i].removeAttribute('aria-current'); }
                    }
                }
                var scrollable = maxLeft() > 2;
                if(prevBtn){ prevBtn.style.display = scrollable ? '' : 'none'; prevBtn.disabled = loop ? false : (vp.scrollLeft <= 2); }
                if(nextBtn){ nextBtn.style.display = scrollable ? '' : 'none'; nextBtn.disabled = loop ? false : (p >= n - 1); }
            }
            if(prevBtn){ prevBtn.addEventListener('click', function(){ goPage(currentPage() - 1); }); }
            if(nextBtn){ nextBtn.addEventListener('click', function(){ goPage(currentPage() + 1); }); }
            if(dotsWrap){
                dotsWrap.addEventListener('click', function(e){
                    var b = e.target.closest('.olo-woo-car-dot');
                    if(!b){return}
                    goPage(parseInt(b.getAttribute('data-page'), 10) || 0);
                });
            }
            var raf = 0;
            vp.addEventListener('scroll', function(){
                if(raf){return}
                raf = window.requestAnimationFrame(function(){ raf = 0; sync(); });
            }, { passive: true });
            var rt;
            window.addEventListener('resize', function(){
                clearTimeout(rt);
                rt = setTimeout(function(){ buildDots(); sync(); }, 150);
            });
            buildDots();
            sync();

            if(autoplay){
                if(!reduce){
                    root.addEventListener('mouseenter', function(){ paused = true; });
                    root.addEventListener('mouseleave', function(){ paused = false; });
                    root.addEventListener('focusin', function(){ paused = true; });
                    root.addEventListener('focusout', function(e){ if(!root.contains(e.relatedTarget)){ paused = false; } });
                    timer = setInterval(function(){
                        if(paused){return}
                        if(document.hidden){return}
                        if(maxLeft() <= 2){return}
                        var p = currentPage();
                        if(p >= pageCount() - 1){
                            if(!loop){ clearInterval(timer); return; }
                        }
                        goPage(p + 1);
                    }, speed);
                }
            }
        })();
        </script>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php else : ?>
        </div>
        <?php endif; ?>
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
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS built by Olobuild_Tile_Base::build_border_css() from sanitized values (intval/safe color whitelist)
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS built by Olobuild_Tile_Base border helpers from sanitized values
        }
        return ob_get_clean();
    }

    /** Un parametro dell'indirizzo come testo pulito ('' se assente o non scalare). */
    private function filtro_url( $nome ) {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- sola lettura dei filtri pubblici scritti dal Filtro prodotti WC nell'indirizzo; nessuna modifica di stato; valore sanitizzato.
        return isset( $_GET[ $nome ] ) && is_scalar( $_GET[ $nome ] ) ? sanitize_text_field( wp_unslash( $_GET[ $nome ] ) ) : '';
    }

    /** Un parametro «a,b,c» dell'indirizzo come elenco di slug (massimo 50). */
    private function filtro_url_elenco( $nome ) {
        $voci = array_filter( array_map( 'sanitize_title', explode( ',', $this->filtro_url( $nome ) ) ) );
        return array_slice( array_values( array_unique( $voci ) ), 0, 50 );
    }
}
