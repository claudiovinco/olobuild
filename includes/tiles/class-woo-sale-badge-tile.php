<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Woo_Sale_Badge_Tile extends Olo_Tile_Base {

    protected $type     = 'woo_sale_badge';
    protected $name     = 'Badge Offerta';
    protected $icon     = 'dashicons-awards';
    protected $category = 'woocommerce';
    protected $defaults = [
        'badge_text'   => 'auto',
        'custom_text'  => 'Offerta!',
        'badge_bg'     => '#EF4444',
        'badge_color'  => '#FFFFFF',
        'badge_shape'  => 'pill',
        'position'     => 'top-left',
        'font_size'    => 14,
        'font_weight'  => '700',
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
            return '<div style="padding:20px;text-align:center;color:var(--olo-color-text-muted, #6B7280);font-size:14px;">'
                 . esc_html( olo_t( 'Nessun prodotto disponibile in questo contesto' ) )
                 . '</div>';
        }

        // Only show if product is on sale
        if ( ! $product->is_on_sale() ) {
            return '';
        }

        $uid = 'olo-woo-sb-' . wp_rand( 10000, 99999 );

        // Colors
        $badge_bg    = $this->safe_color_css( $s['badge_bg'] );
        $badge_color = $this->safe_color_css( $s['badge_color'] );

        // Font
        $font_size   = max( 10, min( 32, absint( $s['font_size'] ) ) );
        $font_weight = in_array( $s['font_weight'], [ '400', '600', '700', '800' ], true ) ? $s['font_weight'] : '700';

        // Badge text
        $badge_text_mode = in_array( $s['badge_text'], [ 'auto', '%', 'custom' ], true ) ? $s['badge_text'] : 'auto';
        $badge_label     = '';

        if ( $badge_text_mode === 'auto' ) {
            // Calculate discount percentage
            $regular = floatval( $product->get_regular_price() );
            $sale    = floatval( $product->get_sale_price() );
            if ( $regular > 0 ) {
                $discount = round( ( ( $regular - $sale ) / $regular ) * 100 );
                $badge_label = '-' . $discount . '%';
            } else {
                $badge_label = olo_t( 'Offerta!' );
            }
        } elseif ( $badge_text_mode === '%' ) {
            $regular = floatval( $product->get_regular_price() );
            $sale    = floatval( $product->get_sale_price() );
            if ( $regular > 0 ) {
                $discount = round( ( ( $regular - $sale ) / $regular ) * 100 );
                $badge_label = '-' . $discount . '%';
            } else {
                $badge_label = olo_t( 'Offerta!' );
            }
        } else {
            $badge_label = sanitize_text_field( $s['custom_text'] ) ?: olo_t( 'Offerta!' );
        }

        // Shape
        $shape_map = [
            'circle'    => '50%',
            'pill'      => '999px',
            'rectangle' => '4px',
        ];
        $shape = isset( $s['badge_shape'] ) ? $s['badge_shape'] : 'pill';
        $border_radius = isset( $shape_map[ $shape ] ) ? $shape_map[ $shape ] : '999px';

        // Position
        $pos_map = [
            'top-left'     => 'top:8px;left:8px;',
            'top-right'    => 'top:8px;right:8px;',
            'bottom-left'  => 'bottom:8px;left:8px;',
            'bottom-right'  => 'bottom:8px;right:8px;',
        ];
        $position = in_array( $s['position'], array_keys( $pos_map ), true ) ? $s['position'] : 'top-left';
        $pos_css  = $pos_map[ $position ];

        // Circle shape needs equal width/height
        $is_circle = ( $shape === 'circle' );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                position: relative;
                display: inline-block;
            }
            .<?php echo $uid; ?> .olo-sb-badge {
                position: absolute;
                <?php echo $pos_css; ?>
                background: <?php echo $badge_bg; ?>;
                color: <?php echo $badge_color; ?>;
                font-size: <?php echo $font_size; ?>px;
                font-weight: <?php echo $font_weight; ?>;
                border-radius: <?php echo $border_radius; ?>;
                z-index: 5;
                line-height: 1;
                text-align: center;
                white-space: nowrap;
                <?php if ( $is_circle ) : ?>
                width: <?php echo ( $font_size * 3 ); ?>px;
                height: <?php echo ( $font_size * 3 ); ?>px;
                display: flex;
                align-items: center;
                justify-content: center;
                <?php else : ?>
                padding: <?php echo round( $font_size * 0.4 ); ?>px <?php echo round( $font_size * 0.8 ); ?>px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                <?php endif; ?>
            }
            .<?php echo $uid; ?>.olo-sb-inline .olo-sb-badge {
                position: static;
            }
        </style>
        <div class="<?php echo esc_attr( $uid ); ?> olo-sb-inline">
            <span class="olo-sb-badge"><?php echo esc_html( $badge_label ); ?></span>
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
