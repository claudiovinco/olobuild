<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Woo_Price_Tile extends Olo_Tile_Base {

    protected $type     = 'woo_price';
    protected $name     = 'Prezzo Prodotto';
    protected $icon     = 'dashicons-tag';
    protected $category = 'woocommerce';
    protected $defaults = [
        'show_regular'  => true,
        'show_sale'     => true,
        'show_suffix'   => false,
        'price_color'   => '',
        'sale_color'    => '#EF4444',
        'regular_color' => '#9CA3AF',
        'font_size'     => 24,
        'font_weight'   => '700',
        'text_align'    => 'left',
        'prefix'        => '',
        'suffix'        => '',
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return '<div style="padding:40px;text-align:center;color:#92400E;background:#FEF3C7;border:1px solid #F59E0B;border-radius:8px;">'
                 . esc_html( olo_t( 'WooCommerce non attivo. Installa e attiva WooCommerce per utilizzare questo elemento.' ) )
                 . '</div>';
        }

        $s = wp_parse_args( $settings, $this->defaults );

        // Get the current product
        global $product;
        if ( ! is_a( $product, 'WC_Product' ) ) {
            $product = wc_get_product( get_the_ID() );
        }
        if ( ! $product ) {
            return '<div style="padding:20px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);font-size:14px;">'
                 . esc_html( olo_t( 'Nessun prodotto disponibile in questo contesto' ) )
                 . '</div>';
        }

        $uid = 'olo-woo-price-' . wp_rand( 10000, 99999 );

        // Colors
        $price_color   = $this->safe_color_css( $s['price_color'] );
        $sale_color    = $this->safe_color_css( $s['sale_color'] );
        $regular_color = $this->safe_color_css( $s['regular_color'] );

        // Font
        $font_size   = max( 12, min( 72, absint( $s['font_size'] ) ) );
        $font_weight = in_array( $s['font_weight'], [ '400', '600', '700', '800' ], true ) ? $s['font_weight'] : '700';
        $text_align  = in_array( $s['text_align'], [ 'left', 'center', 'right' ], true ) ? $s['text_align'] : 'left';

        // Prices
        $on_sale      = $product->is_on_sale();
        $regular_raw  = $product->get_regular_price();
        $sale_raw     = $product->get_sale_price();

        $prefix = sanitize_text_field( $s['prefix'] );
        $suffix = sanitize_text_field( $s['suffix'] );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                text-align: <?php echo $text_align; ?>;
                padding: 4px 0;
            }
            .<?php echo $uid; ?> .olo-woo-price-prefix {
                color: <?php echo $price_color; ?>;
                font-size: <?php echo round( $font_size * 0.65 ); ?>px;
                margin-right: 6px;
            }
            .<?php echo $uid; ?> .olo-woo-price-regular {
                color: <?php echo $regular_color; ?>;
                text-decoration: line-through;
                font-size: <?php echo round( $font_size * 0.75 ); ?>px;
                font-weight: 400;
                margin-right: 8px;
            }
            .<?php echo $uid; ?> .olo-woo-price-regular.no-sale {
                color: <?php echo $price_color; ?>;
                text-decoration: none;
                font-size: <?php echo $font_size; ?>px;
                font-weight: <?php echo $font_weight; ?>;
            }
            .<?php echo $uid; ?> .olo-woo-price-sale {
                color: <?php echo $sale_color; ?>;
                font-size: <?php echo $font_size; ?>px;
                font-weight: <?php echo $font_weight; ?>;
            }
            .<?php echo $uid; ?> .olo-woo-price-suffix {
                color: <?php echo $price_color; ?>;
                font-size: <?php echo round( $font_size * 0.55 ); ?>px;
                margin-left: 4px;
                opacity: 0.7;
            }
        </style>
        <div class="<?php echo esc_attr( $uid ); ?>">
            <?php if ( $prefix !== '' ) : ?>
            <span class="olo-woo-price-prefix"><?php echo esc_html( $prefix ); ?></span>
            <?php endif; ?>
            <?php if ( $on_sale ) : ?>
                <?php if ( ! empty( $s['show_regular'] ) ) : ?>
                <span class="olo-woo-price-regular"><?php echo wc_price( $regular_raw ); ?></span>
                <?php endif; ?>
                <?php if ( ! empty( $s['show_sale'] ) ) : ?>
                <span class="olo-woo-price-sale"><?php echo wc_price( $sale_raw ); ?></span>
                <?php endif; ?>
            <?php else : ?>
                <?php if ( ! empty( $s['show_regular'] ) ) : ?>
                <span class="olo-woo-price-regular no-sale"><?php echo wc_price( $regular_raw ); ?></span>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ( ! empty( $s['show_suffix'] ) ) : ?>
            <span class="olo-woo-price-suffix"><?php echo esc_html( $suffix ?: $product->get_price_suffix() ); ?></span>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
