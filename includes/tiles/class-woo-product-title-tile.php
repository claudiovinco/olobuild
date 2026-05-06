<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Woo_Product_Title_Tile extends Olo_Tile_Base {

    protected $type     = 'woo_product_title';
    protected $name     = 'Titolo Prodotto';
    protected $icon     = 'dashicons-heading';
    protected $category = 'woocommerce';
    protected $defaults = [
        'tag'         => 'h1',
        'text_align'  => 'left',
        'color'       => '',
        'font_size'   => 32,
        'font_weight' => '700',
        'line_height' => '1.2',
        'link_to_product' => false,
        'link_color_hover' => '#6366F1',
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
            return '<div style="padding:20px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);font-size:14px;">'
                 . esc_html( olo_t( 'Nessun prodotto disponibile in questo contesto' ) )
                 . '</div>';
        }

        $uid = 'olo-woo-ptitle-' . wp_rand( 10000, 99999 );

        // Allowed tags
        $allowed_tags = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ];
        $tag = in_array( $s['tag'], $allowed_tags, true ) ? $s['tag'] : 'h1';

        // Colors
        $color       = $this->safe_color_css( $s['color'] );
        $hover_color = $this->safe_color_css( $s['link_color_hover'] );

        // Font
        $font_size   = max( 12, min( 96, absint( $s['font_size'] ) ) );
        $font_weight = in_array( $s['font_weight'], [ '300', '400', '500', '600', '700', '800', '900' ], true ) ? $s['font_weight'] : '700';
        $text_align  = in_array( $s['text_align'], [ 'left', 'center', 'right' ], true ) ? $s['text_align'] : 'left';
        $line_height = in_array( $s['line_height'], [ '1', '1.1', '1.2', '1.3', '1.4', '1.5', '1.6' ], true ) ? $s['line_height'] : '1.2';

        $title     = get_the_title();
        $permalink = get_permalink();

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                margin: 0;
                padding: 0;
                text-align: <?php echo $text_align; ?>;
                color: <?php echo $color; ?>;
                font-size: <?php echo $font_size; ?>px;
                font-weight: <?php echo $font_weight; ?>;
                line-height: <?php echo $line_height; ?>;
            }
            .<?php echo $uid; ?> a {
                color: inherit;
                text-decoration: none;
                transition: color 0.2s ease;
            }
            .<?php echo $uid; ?> a:hover {
                color: <?php echo $hover_color; ?>;
            }
        </style>
        <<?php echo $tag; ?> class="<?php echo esc_attr( $uid ); ?>">
            <?php if ( ! empty( $s['link_to_product'] ) ) : ?>
            <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
            <?php else : ?>
            <?php echo esc_html( $title ); ?>
            <?php endif; ?>
        </<?php echo $tag; ?>>
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
