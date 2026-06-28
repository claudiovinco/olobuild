<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Woo_Price_Tile extends Olobuild_Tile_Base {

    protected $type     = 'woo_price';
    protected $name     = 'Prezzo Prodotto';
    protected $icon     = 'dashicons-tag';
    protected $category = 'woocommerce';
    protected $defaults = [
        'show_regular'  => true,
        'show_sale'     => true,
        'show_suffix'   => false,
        'price_color'   => '',
        'sale_color'    => '',
        'regular_color' => '',
        'font_size'     => 24,
        'font_weight'   => '700',
        'text_align'    => 'left',
        'prefix'        => '',
        'suffix'        => '',
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

        // Get the current product
        global $product;
        if ( ! is_a( $product, 'WC_Product' ) ) {
            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- global $product di WooCommerce, non un global definito da olobuild
            $product = wc_get_product( get_the_ID() );
        }
        if ( ! $product ) {
            return '<div style="padding:20px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);font-size:14px;">'
                 . esc_html( olobuild_t( 'Nessun prodotto disponibile in questo contesto' ) )
                 . '</div>';
        }

        $uid = 'olo-woo-price-' . wp_rand( 10000, 99999 );

        // Colors — TOKEN-FIRST: prezzo = testo, saldo = stato (rosso), barrato = neutro soft.
        $price_color   = $this->safe_color_css( $s['price_color'] )   ?: 'var(--olo-color-text, #1f2937)';
        $sale_color    = $this->safe_color_css( $s['sale_color'] )    ?: 'var(--olo-color-error, #b42318)';
        $regular_color = $this->safe_color_css( $s['regular_color'] ) ?: 'var(--olo-color-text-soft, #6b7280)';

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
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: colours via the safe_color_css() whitelist, $font_size via absint()+min()/max() clamps (round() products), $font_weight/$text_align via in_array() whitelists; $uid is internally generated.
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
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="<?php echo esc_attr( $uid ); ?>">
            <?php if ( $prefix !== '' ) : ?>
            <span class="olo-woo-price-prefix"><?php echo esc_html( $prefix ); ?></span>
            <?php endif; ?>
            <?php if ( $on_sale ) : ?>
                <?php if ( ! empty( $s['show_regular'] ) ) : ?>
                <span class="olo-woo-price-regular"><?php echo wc_price( $regular_raw ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wc_price() returns escaped WooCommerce price HTML ?></span>
                <?php endif; ?>
                <?php if ( ! empty( $s['show_sale'] ) ) : ?>
                <span class="olo-woo-price-sale"><?php echo wc_price( $sale_raw ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wc_price() returns escaped WooCommerce price HTML ?></span>
                <?php endif; ?>
            <?php else : ?>
                <?php if ( ! empty( $s['show_regular'] ) ) : ?>
                <span class="olo-woo-price-regular no-sale"><?php echo wc_price( $regular_raw ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wc_price() returns escaped WooCommerce price HTML ?></span>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ( ! empty( $s['show_suffix'] ) ) : ?>
            <span class="olo-woo-price-suffix"><?php echo esc_html( $suffix ?: $product->get_price_suffix() ); ?></span>
            <?php endif; ?>
        </div>
        <?php
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized border settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() shared helpers
        }
        return ob_get_clean();
    }
}
