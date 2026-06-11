<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Woo_Cross_Sells_Tile extends Olo_Tile_Base {

    protected $type     = 'woo_cross_sells';
    protected $name     = 'Cross-Sell WC';
    protected $icon     = 'dashicons-randomize';
    protected $category = 'woocommerce';
    protected $defaults = [
        'columns'         => 4,
        'columns_tablet'  => 2,
        'columns_mobile'  => 1,
        'gap'             => 24,
        'card_style'      => 'shadow',
        'show_image'      => true,
        'show_title'      => true,
        'show_price'      => true,
        'show_rating'     => false,
        'show_add_to_cart' => true,
        'show_badge'      => true,
        'hover_effect'    => 'zoom',
        'image_ratio'     => '4-3',
        'title_color'     => '',
        'price_color'     => '',
        'sale_color'      => '',
        'button_bg'       => '',
        'button_color'    => '',
        'badge_bg'        => '',
        'heading'         => 'Potrebbe interessarti anche',
        'heading_size'    => 20,
        'heading_color'   => '',
        'limit'           => 8,
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

        // Get cross-sell product IDs from cart
        $cross_sell_ids = [];
        if ( WC()->cart ) {
            $cross_sell_ids = WC()->cart->get_cross_sells();
        }

        // Fallback: if on a single product, get cross-sells from the product
        if ( empty( $cross_sell_ids ) ) {
            if ( is_singular( 'product' ) ) {
                global $product;
                if ( ! is_a( $product, 'WC_Product' ) ) {
                    $product = wc_get_product( get_the_ID() );
                }
                if ( $product ) {
                    $cross_sell_ids = $product->get_cross_sell_ids();
                }
            }
        }

        if ( empty( $cross_sell_ids ) ) {
            return '';
        }

        $limit = max( 1, min( 24, absint( $s['limit'] ) ) );
        $cross_sell_ids = array_slice( $cross_sell_ids, 0, $limit );

        $uid = 'olo-woo-cs-' . wp_rand( 10000, 99999 );

        // Grid settings
        $cols   = max( 2, min( 6, absint( $s['columns'] ) ) );
        $cols_t = max( 1, min( 4, absint( $s['columns_tablet'] ) ) );
        $cols_m = max( 1, min( 2, absint( $s['columns_mobile'] ) ) );
        $gap    = absint( $s['gap'] );

        // Colors
        $title_color = $this->safe_color_css( $s['title_color'] ) ?: 'var(--olo-color-text, #374151)';
        $price_color = $this->safe_color_css( $s['price_color'] ) ?: 'var(--olo-color-text, #374151)';
        $sale_color  = $this->safe_color_css( $s['sale_color'] ) ?: 'var(--olo-color-error, #b42318)';
        $btn_bg      = $this->safe_color_css( $s['button_bg'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $btn_color   = $this->safe_color_css( $s['button_color'] ) ?: 'var(--olo-color-on-primary, #ffffff)';
        $badge_bg    = $this->safe_color_css( $s['badge_bg'] ) ?: 'var(--olo-color-primary, #e1474f)';
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
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above (safe_color_css/absint clamps/fixed maps/generated uid). ?>
        <style>
            .<?php echo $uid; ?>-heading {
                font-size: <?php echo (int) $heading_sz; ?>px;
                font-weight: 700;
                color: <?php echo $heading_col; ?>;
                margin: 0 0 20px;
            }
            .<?php echo $uid; ?> {
                display: grid;
                grid-template-columns: repeat(<?php echo (int) $cols; ?>, 1fr);
                gap: <?php echo (int) $gap; ?>px;
            }
            .<?php echo $uid; ?> .olo-cs-card {
                background: var(--olo-color-background, #FFFFFF);
                border-radius: 8px;
                overflow: hidden;
                <?php echo $card_extra; ?>
                transition: box-shadow 0.3s ease, transform 0.3s ease;
            }
            <?php if ( $hover_effect === 'shadow' ) : ?>
            .<?php echo $uid; ?> .olo-cs-card:hover { box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-cs-card-img {
                position: relative;
                overflow: hidden;
                <?php if ( ! $auto_h ) : ?>padding-top: <?php echo $ratio_val; ?>;<?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-cs-card-img img {
                <?php if ( ! $auto_h ) : ?>
                position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;
                <?php else : ?>
                width: 100%; height: auto; display: block;
                <?php endif; ?>
                transition: transform 0.4s ease;
            }
            <?php if ( $hover_effect === 'zoom' ) : ?>
            .<?php echo $uid; ?> .olo-cs-card:hover .olo-cs-card-img img { transform: scale(1.06); }
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-cs-badge {
                position: absolute; top: 8px; left: 8px;
                background: <?php echo $badge_bg; ?>; color: #fff;
                font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 4px; z-index: 2;
            }
            .<?php echo $uid; ?> .olo-cs-card-body { padding: 14px; }
            .<?php echo $uid; ?> .olo-cs-card-title {
                margin: 0 0 6px; font-size: 15px; font-weight: 600; line-height: 1.3;
            }
            .<?php echo $uid; ?> .olo-cs-card-title a {
                color: <?php echo $title_color; ?>; text-decoration: none;
            }
            .<?php echo $uid; ?> .olo-cs-card-title a:hover { text-decoration: underline; }
            .<?php echo $uid; ?> .olo-cs-card-rating {
                display: flex; align-items: center; gap: 2px; margin-bottom: 6px;
            }
            .<?php echo $uid; ?> .olo-cs-card-price {
                font-size: 15px; font-weight: 700; color: <?php echo $price_color; ?>; margin-bottom: 10px;
            }
            .<?php echo $uid; ?> .olo-cs-card-price del {
                color: var(--olo-color-text-muted, #9CA3AF); font-weight: 400; font-size: 13px; margin-right: 6px;
            }
            .<?php echo $uid; ?> .olo-cs-card-price ins {
                text-decoration: none; color: <?php echo $sale_color; ?>;
            }
            .<?php echo $uid; ?> .olo-cs-btn {
                display: inline-block; width: 100%; padding: 9px 16px;
                background: <?php echo $btn_bg; ?>; color: <?php echo $btn_color; ?>;
                border: none; border-radius: 4px;
                font-size: 13px; font-weight: 600; text-align: center; text-decoration: none;
                cursor: pointer; transition: opacity 0.2s ease;
            }
            .<?php echo $uid; ?> .olo-cs-btn:hover { opacity: 0.9; }
            @media (max-width: 960px) {
                .<?php echo $uid; ?> { grid-template-columns: repeat(<?php echo (int) $cols_t; ?>, 1fr); }
            }
            @media (max-width: 640px) {
                .<?php echo $uid; ?> { grid-template-columns: repeat(<?php echo (int) $cols_m; ?>, 1fr); }
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <?php if ( $heading_text !== '' ) : ?>
        <?php list( $cs_cls, $cs_data ) = $this->tfx_attrs( $s, 'heading', $heading_text ); ?>
        <h3 class="<?php echo esc_attr( $uid ); ?>-heading<?php echo $cs_cls; ?>"<?php echo $cs_data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attrs built by Olo_Text_Effects (sanitize_html_class/esc_attr applied internally) ?>><?php echo esc_html( $heading_text ); ?></h3>
        <?php endif; ?>

        <div class="<?php echo esc_attr( $uid ); ?>">
        <?php
        foreach ( $cross_sell_ids as $pid ) :
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
            $on_sale    = $product->is_on_sale();
            $avg_rating = $product->get_average_rating();
            $thumb_id   = get_post_thumbnail_id( $pid );
        ?>
            <div class="olo-cs-card">
                <?php if ( ! empty( $s['show_image'] ) ) : ?>
                <div class="olo-cs-card-img">
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
                                echo '<div class="olo-cs-badge">-' . absint( $pct ) . '%</div>';
                            }
                        }
                    }
                    ?>
                </div>
                <?php endif; ?>
                <div class="olo-cs-card-body">
                    <?php if ( ! empty( $s['show_title'] ) ) : ?>
                    <div class="olo-cs-card-title">
                        <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
                    </div>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['show_rating'] ) ) : ?>
                    <div class="olo-cs-card-rating">
                        <?php
                        $full  = floor( (float) $avg_rating );
                        $empty = 5 - $full;
                        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup defined as literals above, no dynamic data
                        for ( $i = 0; $i < $full; $i++ ) { echo $star_full; }
                        for ( $i = 0; $i < $empty; $i++ ) { echo $star_empty; }
                        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
                        ?>
                    </div>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['show_price'] ) ) : ?>
                    <div class="olo-cs-card-price"><?php echo $price_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WooCommerce price HTML from WC_Product::get_price_html(), escaped by WooCommerce core ?></div>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['show_add_to_cart'] ) ) : ?>
                    <?php
                    $add_url  = $product->add_to_cart_url();
                    $add_text = $product->is_type( 'simple' ) ? olo_t( 'Aggiungi al carrello' ) : olo_t( 'Seleziona opzioni' );
                    ?>
                    <a href="<?php echo esc_url( $add_url ); ?>" class="olo-cs-btn"
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
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Text_Effects::css() from sanitized effect settings
        $this->tfx_print_script();
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
