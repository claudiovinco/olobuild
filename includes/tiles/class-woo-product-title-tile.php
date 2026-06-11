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
        'link_color_hover' => '',
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

        $uid = 'olo-woo-ptitle-' . wp_rand( 10000, 99999 );

        // Allowed tags
        $allowed_tags = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ];
        $tag = in_array( $s['tag'], $allowed_tags, true ) ? $s['tag'] : 'h1';

        // Colors — TOKEN-FIRST: titolo eredita il testo del tema, hover col primario brand.
        $color       = $this->safe_color_css( $s['color'] ) ?: 'var(--olo-color-text, #1f2937)';
        $hover_color = $this->safe_color_css( $s['link_color_hover'] ) ?: 'var(--olo-color-primary, #e1474f)';

        // Font
        $font_size   = max( 12, min( 96, absint( $s['font_size'] ) ) );
        $font_weight = in_array( $s['font_weight'], [ '300', '400', '500', '600', '700', '800', '900' ], true ) ? $s['font_weight'] : '700';
        $text_align  = in_array( $s['text_align'], [ 'left', 'center', 'right' ], true ) ? $s['text_align'] : 'left';
        $line_height = in_array( $s['line_height'], [ '1', '1.1', '1.2', '1.3', '1.4', '1.5', '1.6' ], true ) ? $s['line_height'] : '1.2';

        $title     = get_the_title();
        $permalink = get_permalink();

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: colours via the safe_color_css() whitelist (with fixed var() fallbacks), font-size via absint() with min()/max() clamp, align/weight/line-height via in_array() whitelists; $uid is internally generated. ?>
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
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <<?php echo tag_escape( $tag ); ?> class="<?php echo esc_attr( $uid ); ?>">
            <?php if ( ! empty( $s['link_to_product'] ) ) : ?>
            <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
            <?php else : ?>
            <?php echo esc_html( $title ); ?>
            <?php endif; ?>
        </<?php echo tag_escape( $tag ); ?>>
        <?php
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }
}
