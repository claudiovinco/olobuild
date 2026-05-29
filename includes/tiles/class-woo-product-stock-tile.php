<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Woo_Product_Stock_Tile extends Olo_Tile_Base {

    protected $type     = 'woo_product_stock';
    protected $name     = 'Stock Prodotto';
    protected $icon     = 'dashicons-archive';
    protected $category = 'woocommerce';
    protected $defaults = [
        'show_quantity'     => true,
        'show_icon'         => true,
        'in_stock_color'    => '',
        'out_of_stock_color' => '',
        'low_stock_color'   => '',
        'low_stock_threshold' => 5,
        'font_size'         => 14,
        'font_weight'       => '500',
        'text_align'        => 'left',
        'icon_size'         => 10,
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

        $uid = 'olo-woo-pstock-' . wp_rand( 10000, 99999 );

        // Stock status
        $stock_status = $product->get_stock_status();
        $stock_qty    = $product->get_stock_quantity();
        $is_in_stock  = $product->is_in_stock();
        $manages_stock = $product->managing_stock();

        // Determine color and text
        $low_threshold = max( 1, absint( $s['low_stock_threshold'] ) );
        // TOKEN-FIRST stati semantici: disponibile=success, esaurito=error, scorte basse=warning.
        $in_stock_color     = $this->safe_color_css( $s['in_stock_color'] )     ?: 'var(--olo-color-success, #15803d)';
        $out_of_stock_color = $this->safe_color_css( $s['out_of_stock_color'] ) ?: 'var(--olo-color-error, #b42318)';
        $low_stock_color    = $this->safe_color_css( $s['low_stock_color'] )    ?: 'var(--olo-color-warning, #b45309)';

        if ( $stock_status === 'onbackorder' ) {
            $status_text  = olo_t( 'Disponibile su ordinazione' );
            $status_color = $low_stock_color;
        } elseif ( ! $is_in_stock ) {
            $status_text  = olo_t( 'Non disponibile' );
            $status_color = $out_of_stock_color;
        } elseif ( $manages_stock ) {
            if ( $stock_qty !== null ) {
                if ( $stock_qty <= $low_threshold ) {
                    $status_text  = sprintf( olo_t( 'Solo %d rimasti' ), $stock_qty );
                    $status_color = $low_stock_color;
                } else {
                    $status_text  = olo_t( 'Disponibile' );
                    $status_color = $in_stock_color;
                    if ( ! empty( $s['show_quantity'] ) ) {
                        $status_text .= ' (' . $stock_qty . ')';
                    }
                }
            } else {
                $status_text  = olo_t( 'Disponibile' );
                $status_color = $in_stock_color;
            }
        } else {
            $status_text  = olo_t( 'Disponibile' );
            $status_color = $in_stock_color;
        }

        // Font
        $font_size   = max( 11, min( 24, absint( $s['font_size'] ) ) );
        $font_weight = in_array( $s['font_weight'], [ '400', '500', '600', '700' ], true ) ? $s['font_weight'] : '500';
        $text_align  = in_array( $s['text_align'], [ 'left', 'center', 'right' ], true ) ? $s['text_align'] : 'left';
        $icon_size   = max( 6, min( 16, absint( $s['icon_size'] ) ) );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: <?php echo $font_size; ?>px;
                font-weight: <?php echo $font_weight; ?>;
                color: <?php echo $status_color; ?>;
                <?php if ( $text_align === 'center' ) : ?>
                justify-content: center;
                <?php elseif ( $text_align === 'right' ) : ?>
                justify-content: flex-end;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-woo-stock-dot {
                width: <?php echo $icon_size; ?>px;
                height: <?php echo $icon_size; ?>px;
                border-radius: 50%;
                background: <?php echo $status_color; ?>;
                flex-shrink: 0;
            }
        </style>
        <div class="<?php echo esc_attr( $uid ); ?>">
            <?php if ( ! empty( $s['show_icon'] ) ) : ?>
            <span class="olo-woo-stock-dot"></span>
            <?php endif; ?>
            <span><?php echo esc_html( $status_text ); ?></span>
        </div>
        <?php
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}";
            echo $border_hover_css . $border_effect_css . '</style>';
        }
        return ob_get_clean();
    }
}
