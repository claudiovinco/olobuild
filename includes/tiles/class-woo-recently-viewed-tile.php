<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Woo_Recently_Viewed_Tile extends Olo_Tile_Base {

    protected $type     = 'woo_recently_viewed';
    protected $name     = 'Visti di Recente WC';
    protected $icon     = 'dashicons-clock';
    protected $category = 'woocommerce';
    protected $defaults = [
        'columns'         => 4,
        'columns_tablet'  => 2,
        'columns_mobile'  => 1,
        'limit'           => 8,
        'gap'             => 24,
        'card_style'      => 'shadow',
        'show_image'      => true,
        'show_title'      => true,
        'show_price'      => true,
        'show_rating'     => false,
        'show_add_to_cart' => true,
        'hover_effect'    => 'zoom',
        'image_ratio'     => '4-3',
        'title_color'     => '',
        'price_color'     => '',
        'sale_color'      => '',
        'button_bg'       => '',
        'button_color'    => '',
        'heading'         => 'Visti di recente',
        'heading_size'    => 20,
        'heading_color'   => '',
        'empty_text'      => '',
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

        // Get recently viewed product IDs from WooCommerce cookie.
        // Read-only render: the pipe-separated cookie is sanitized to a list of
        // positive integers via sanitize_text_field() + per-element absint().
        $viewed_products = [];
        if ( isset( $_COOKIE['woocommerce_recently_viewed'] ) ) {
            $rv_cookie       = sanitize_text_field( wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) );
            $viewed_products = array_filter( array_map( 'absint', explode( '|', $rv_cookie ) ) );
        }

        if ( empty( $viewed_products ) ) {
            $empty_text = sanitize_text_field( $s['empty_text'] );
            if ( $empty_text !== '' ) {
                return '<div style="padding:30px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);font-size:14px">'
                     . esc_html( $empty_text )
                     . '</div>';
            }
            return '';
        }

        $limit = max( 1, min( 24, absint( $s['limit'] ) ) );
        $viewed_products = array_slice( $viewed_products, 0, $limit );

        $uid = 'olo-woo-rv-' . wp_rand( 10000, 99999 );

        // Grid settings
        $cols   = max( 1, min( 6, absint( $s['columns'] ) ) );
        $cols_t = max( 1, min( 4, absint( $s['columns_tablet'] ) ) );
        $cols_m = max( 1, min( 2, absint( $s['columns_mobile'] ) ) );
        $gap    = absint( $s['gap'] );

        // Colors
        $title_color = $this->safe_color_css( $s['title_color'] ) ?: 'var(--olo-color-text, #374151)';
        $price_color = $this->safe_color_css( $s['price_color'] ) ?: 'var(--olo-color-text, #374151)';
        $sale_color  = $this->safe_color_css( $s['sale_color'] ) ?: 'var(--olo-color-error, #b42318)';
        $btn_bg      = $this->safe_color_css( $s['button_bg'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $btn_color   = $this->safe_color_css( $s['button_color'] ) ?: 'var(--olo-color-on-primary, #ffffff)';
        $heading_col = $this->safe_color_css( $s['heading_color'] ) ?: 'var(--olo-color-text, #374151)';
        $heading_sz  = max( 14, min( 40, absint( $s['heading_size'] ) ) );

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
        $card_extra = '';
        if ( $s['card_style'] === 'shadow' ) {
            $card_extra = 'box-shadow:0 1px 3px rgba(0,0,0,0.1),0 1px 2px rgba(0,0,0,0.06);';
        } elseif ( $s['card_style'] === 'border' ) {
            $card_extra = 'border:1px solid var(--olo-color-border, #E5E7EB);';
        }

        $hover_effect = $s['hover_effect'];
        $heading_text = sanitize_text_field( $s['heading'] );

        // Star SVGs
        $star_full  = '<svg width="14" height="14" viewBox="0 0 24 24" fill="var(--olo-color-warning, #F59E0B)" stroke="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
        $star_empty = '<svg width="14" height="14" viewBox="0 0 24 24" fill="var(--olo-color-border, #E5E7EB)" stroke="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist for every colour, absint()/max()/min() clamps for columns/gap/sizes, fixed literals for card style and ratio (hardcoded map), and the internally generated $uid. ?>
        <style>
            .<?php echo $uid; ?>-heading {
                font-size: <?php echo $heading_sz; ?>px;
                font-weight: 700;
                color: <?php echo $heading_col; ?>;
                margin: 0 0 20px;
            }
            .<?php echo $uid; ?> {
                display: grid;
                grid-template-columns: repeat(<?php echo $cols; ?>, 1fr);
                gap: <?php echo $gap; ?>px;
            }
            .<?php echo $uid; ?> .olo-rv-card {
                background: var(--olo-color-background, #fff);
                border-radius: 8px;
                overflow: hidden;
                <?php echo $card_extra; ?>
                transition: box-shadow 0.3s ease, transform 0.3s ease;
            }
            <?php if ( $hover_effect === 'shadow' ) : ?>
            .<?php echo $uid; ?> .olo-rv-card:hover { box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-rv-card-img {
                position: relative;
                overflow: hidden;
                <?php if ( ! $auto_h ) : ?>padding-top: <?php echo $ratio_val; ?>;<?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-rv-card-img img {
                <?php if ( ! $auto_h ) : ?>
                position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;
                <?php else : ?>
                width: 100%; height: auto; display: block;
                <?php endif; ?>
                transition: transform 0.4s ease;
            }
            <?php if ( $hover_effect === 'zoom' ) : ?>
            .<?php echo $uid; ?> .olo-rv-card:hover .olo-rv-card-img img { transform: scale(1.06); }
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-rv-card-body { padding: 14px; }
            .<?php echo $uid; ?> .olo-rv-card-title {
                margin: 0 0 6px; font-size: 15px; font-weight: 600; line-height: 1.3;
            }
            .<?php echo $uid; ?> .olo-rv-card-title a {
                color: <?php echo $title_color; ?>; text-decoration: none;
            }
            .<?php echo $uid; ?> .olo-rv-card-title a:hover { text-decoration: underline; }
            .<?php echo $uid; ?> .olo-rv-card-rating {
                display: flex; align-items: center; gap: 2px; margin-bottom: 6px;
            }
            .<?php echo $uid; ?> .olo-rv-card-price {
                font-size: 15px; font-weight: 700; color: <?php echo $price_color; ?>; margin-bottom: 10px;
            }
            .<?php echo $uid; ?> .olo-rv-card-price del {
                color: var(--olo-color-text-muted, #9CA3AF); font-weight: 400; font-size: 13px; margin-right: 6px;
            }
            .<?php echo $uid; ?> .olo-rv-card-price ins {
                text-decoration: none; color: <?php echo $sale_color; ?>;
            }
            .<?php echo $uid; ?> .olo-rv-btn {
                display: inline-block; width: 100%; padding: 9px 16px;
                background: <?php echo $btn_bg; ?>; color: <?php echo $btn_color; ?>;
                border: none; border-radius: 4px;
                font-size: 13px; font-weight: 600; text-align: center; text-decoration: none;
                cursor: pointer; transition: opacity 0.2s ease;
            }
            .<?php echo $uid; ?> .olo-rv-btn:hover { opacity: 0.9; }
            @media (max-width: 960px) {
                .<?php echo $uid; ?> { grid-template-columns: repeat(<?php echo $cols_t; ?>, 1fr); }
            }
            @media (max-width: 640px) {
                .<?php echo $uid; ?> { grid-template-columns: repeat(<?php echo $cols_m; ?>, 1fr); }
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <?php if ( $heading_text !== '' ) : ?>
        <?php list( $rv_cls, $rv_data ) = $this->tfx_attrs( $s, 'heading', $heading_text ); ?>
        <h3 class="<?php echo esc_attr( $uid ); ?>-heading<?php echo $rv_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); heading escaped inline via esc_html() ?>"<?php echo $rv_data; ?>><?php echo esc_html( $heading_text ); ?></h3>
        <?php endif; ?>

        <div class="<?php echo esc_attr( $uid ); ?>">
        <?php
        foreach ( $viewed_products as $pid ) :
            $product = wc_get_product( $pid );
            if ( ! $product ) {
                continue;
            }
            if ( $product->get_status() !== 'publish' ) {
                continue;
            }

            $permalink  = get_permalink( $pid );
            $title      = $product->get_name();
            $price_html = $product->get_price_html();
            $avg_rating = $product->get_average_rating();
            $thumb_id   = get_post_thumbnail_id( $pid );
        ?>
            <div class="olo-rv-card">
                <?php if ( ! empty( $s['show_image'] ) ) : ?>
                <div class="olo-rv-card-img">
                    <?php if ( $thumb_id ) : ?>
                    <a href="<?php echo esc_url( $permalink ); ?>">
                        <?php echo wp_get_attachment_image( $thumb_id, 'woocommerce_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() returns escaped HTML built by WordPress core ?>
                    </a>
                    <?php else : ?>
                    <a href="<?php echo esc_url( $permalink ); ?>" style="display:block;background:var(--olo-color-muted, #F3F4F6);<?php if ( ! $auto_h ) : ?>position:absolute;inset:0;<?php else : ?>padding:40px 0;<?php endif; ?>display:flex;align-items:center;justify-content:center;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--olo-color-border, #D1D5DB)" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <div class="olo-rv-card-body">
                    <?php if ( ! empty( $s['show_title'] ) ) : ?>
                    <div class="olo-rv-card-title">
                        <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
                    </div>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['show_rating'] ) ) : ?>
                    <div class="olo-rv-card-rating">
                        <?php
                        $full  = floor( (float) $avg_rating );
                        $empty = 5 - $full;
                        for ( $i = 0; $i < $full; $i++ ) { echo $star_full; } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG literal defined above
                        for ( $i = 0; $i < $empty; $i++ ) { echo $star_empty; } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG literal defined above
                        ?>
                    </div>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['show_price'] ) ) : ?>
                    <div class="olo-rv-card-price"><?php echo $price_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- price HTML generated by WooCommerce WC_Product::get_price_html() (escaped by WooCommerce) ?></div>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['show_add_to_cart'] ) ) : ?>
                    <?php
                    $add_url  = $product->add_to_cart_url();
                    $add_text = $product->is_type( 'simple' ) ? olo_t( 'Aggiungi al carrello' ) : olo_t( 'Seleziona opzioni' );
                    ?>
                    <a href="<?php echo esc_url( $add_url ); ?>" class="olo-rv-btn"
                       data-product_id="<?php echo absint( $pid ); ?>"
                       data-quantity="1"
                       <?php if ( $product->is_type( 'simple' ) ) : ?>
                       data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
                       <?php endif; ?>
                    ><?php echo esc_html( $add_text ); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Text_Effects::css() from fixed effect definitions
        $this->tfx_print_script();
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_css() (integer-forced widths) for the internally generated uid
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by the Olo_Tile_Base::build_border_hover_css()/build_border_effect_css() shared helpers
        }
        return ob_get_clean();
    }
}
