<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Woo_Cart_Tile extends Olobuild_Tile_Base {

    protected $type     = 'woo_cart';
    protected $name     = 'Carrello';
    protected $icon     = 'dashicons-cart';
    protected $category = 'woocommerce';
    protected $defaults = [
        'show_thumbnail' => true,
        'show_coupon'    => true,
        'show_totals'    => true,
        'button_color'   => '',
        'button_bg'      => '',
        'text_color'     => '',
        'heading_color'  => '',
        'border_color'   => '',
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

        $uid = 'olo-woo-cart-' . wp_rand( 10000, 99999 );

        // Colors — TOKEN-FIRST: CTA col brand, testo/heading dal tema, bordo dal token.
        $btn_color     = $this->safe_color_css( $s['button_color'] )  ?: 'var(--olo-color-on-primary, #ffffff)';
        $btn_bg        = $this->safe_color_css( $s['button_bg'] )     ?: 'var(--olo-color-primary, #e1474f)';
        $text_color    = $this->safe_color_css( $s['text_color'] )    ?: 'var(--olo-color-text, #1f2937)';
        $heading_color = $this->safe_color_css( $s['heading_color'] ) ?: 'var(--olo-color-text, #1f2937)';
        $border_color  = $this->safe_color_css( $s['border_color'] )  ?: 'var(--olo-color-border, #e5e7eb)';

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: every color via the safe_color_css() whitelist (with var() fallbacks); $uid is internally generated.
        ?>
        <style>
            .<?php echo $uid; ?> .woocommerce {
                color: <?php echo $text_color; ?>;
            }
            .<?php echo $uid; ?> .woocommerce table.shop_table {
                border-collapse: collapse;
                width: 100%;
                border: 1px solid <?php echo $border_color; ?>;
                border-radius: 8px;
                overflow: hidden;
            }
            .<?php echo $uid; ?> .woocommerce table.shop_table th {
                background: var(--olo-color-muted, #F3F4F6);
                color: <?php echo $heading_color; ?>;
                font-weight: 600;
                font-size: 13px;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                padding: 12px 16px;
                border-bottom: 1px solid <?php echo $border_color; ?>;
            }
            .<?php echo $uid; ?> .woocommerce table.shop_table td {
                padding: 16px;
                border-bottom: 1px solid <?php echo $border_color; ?>;
                vertical-align: middle;
                font-size: 14px;
            }
            .<?php echo $uid; ?> .woocommerce table.shop_table img {
                width: 60px;
                height: 60px;
                object-fit: cover;
                border-radius: 6px;
            }
            <?php if ( empty( $s['show_thumbnail'] ) ) : ?>
            .<?php echo $uid; ?> .woocommerce table.shop_table .product-thumbnail {
                display: none;
            }
            <?php endif; ?>
            .<?php echo $uid; ?> .woocommerce .product-name a {
                color: <?php echo $heading_color; ?>;
                text-decoration: none;
                font-weight: 600;
            }
            .<?php echo $uid; ?> .woocommerce .product-name a:hover {
                text-decoration: underline;
            }
            .<?php echo $uid; ?> .woocommerce .quantity .qty {
                width: 60px;
                padding: 6px 8px;
                border: 1px solid <?php echo $border_color; ?>;
                border-radius: 4px;
                font-size: 14px;
                text-align: center;
            }
            .<?php echo $uid; ?> .woocommerce .actions .button,
            .<?php echo $uid; ?> .woocommerce .checkout-button,
            .<?php echo $uid; ?> .woocommerce .wc-proceed-to-checkout a {
                display: inline-block;
                padding: 12px 24px;
                background: <?php echo $btn_bg; ?>;
                color: <?php echo $btn_color; ?>;
                border: none;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 600;
                text-decoration: none;
                cursor: pointer;
                transition: opacity 0.2s ease;
            }
            .<?php echo $uid; ?> .woocommerce .actions .button:hover,
            .<?php echo $uid; ?> .woocommerce .checkout-button:hover,
            .<?php echo $uid; ?> .woocommerce .wc-proceed-to-checkout a:hover {
                opacity: 0.9;
            }
            <?php if ( empty( $s['show_coupon'] ) ) : ?>
            .<?php echo $uid; ?> .woocommerce .coupon {
                display: none;
            }
            <?php endif; ?>
            <?php if ( empty( $s['show_totals'] ) ) : ?>
            .<?php echo $uid; ?> .woocommerce .cart_totals {
                display: none;
            }
            <?php endif; ?>
            .<?php echo $uid; ?> .woocommerce .cart_totals h2 {
                font-size: 18px;
                font-weight: 700;
                color: <?php echo $heading_color; ?>;
                margin: 0 0 16px;
            }
            .<?php echo $uid; ?> .woocommerce .cart_totals table {
                width: 100%;
                border-collapse: collapse;
            }
            .<?php echo $uid; ?> .woocommerce .cart_totals table th,
            .<?php echo $uid; ?> .woocommerce .cart_totals table td {
                padding: 10px 0;
                border-bottom: 1px solid <?php echo $border_color; ?>;
                font-size: 14px;
            }
            .<?php echo $uid; ?> .woocommerce .cart_totals table th {
                font-weight: 600;
                color: <?php echo $heading_color; ?>;
                text-align: left;
                width: 40%;
            }
            .<?php echo $uid; ?> .woocommerce .cart_totals .order-total td {
                font-size: 18px;
                font-weight: 700;
                color: <?php echo $heading_color; ?>;
            }
            .<?php echo $uid; ?> .woocommerce .coupon input[type="text"] {
                padding: 8px 12px;
                border: 1px solid <?php echo $border_color; ?>;
                border-radius: 4px;
                font-size: 14px;
                margin-right: 8px;
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="<?php echo esc_attr( $uid ); ?>">
            <?php echo do_shortcode( '[woocommerce_cart]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WooCommerce core cart shortcode output; WooCommerce templates escape their own output ?>
        </div>
        <?php
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized border settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base border helpers from sanitized border settings
        }
        return ob_get_clean();
    }
}
