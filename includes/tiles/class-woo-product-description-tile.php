<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Woo_Product_Description_Tile extends Olo_Tile_Base {

    protected $type     = 'woo_product_description';
    protected $name     = 'Descrizione Prodotto';
    protected $icon     = 'dashicons-text-page';
    protected $category = 'woocommerce';
    protected $defaults = [
        'content_type' => 'full',
        'text_color'   => '#374151',
        'font_size'    => 16,
        'line_height'  => '1.6',
        'text_align'   => 'left',
        'max_lines'    => 0,
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

        $uid = 'olo-woo-pdesc-' . wp_rand( 10000, 99999 );

        // Get content
        $content_type = in_array( $s['content_type'], [ 'full', 'short' ], true ) ? $s['content_type'] : 'full';
        if ( $content_type === 'short' ) {
            $content = $product->get_short_description();
        } else {
            $content = $product->get_description();
        }

        if ( empty( $content ) ) {
            $label = $content_type === 'short'
                ? olo_t( 'Nessuna descrizione breve disponibile' )
                : olo_t( 'Nessuna descrizione disponibile' );
            return '<div style="padding:20px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);font-size:14px;font-style:italic;">'
                 . esc_html( $label )
                 . '</div>';
        }

        // Apply wptexturize and wpautop for proper formatting
        $content = apply_filters( 'the_content', $content );

        // Colors and styles
        $text_color  = $this->safe_color_css( $s['text_color'] );
        $font_size   = max( 12, min( 32, absint( $s['font_size'] ) ) );
        $text_align  = in_array( $s['text_align'], [ 'left', 'center', 'right', 'justify' ], true ) ? $s['text_align'] : 'left';
        $line_height = in_array( $s['line_height'], [ '1.2', '1.4', '1.5', '1.6', '1.8', '2' ], true ) ? $s['line_height'] : '1.6';
        $max_lines   = absint( $s['max_lines'] );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                color: <?php echo $text_color; ?>;
                font-size: <?php echo $font_size; ?>px;
                line-height: <?php echo $line_height; ?>;
                text-align: <?php echo $text_align; ?>;
            }
            .<?php echo $uid; ?> p {
                margin: 0 0 1em;
            }
            .<?php echo $uid; ?> p:last-child {
                margin-bottom: 0;
            }
            <?php if ( $max_lines > 0 ) : ?>
            .<?php echo $uid; ?> {
                display: -webkit-box;
                -webkit-line-clamp: <?php echo $max_lines; ?>;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            <?php endif; ?>
        </style>
        <div class="<?php echo esc_attr( $uid ); ?>">
            <?php echo $content; ?>
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
