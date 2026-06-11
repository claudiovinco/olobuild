<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Woo_Checkout_Tile extends Olo_Tile_Base {

    protected $type     = 'woo_checkout';
    protected $name     = 'Checkout';
    protected $icon     = 'dashicons-clipboard';
    protected $category = 'woocommerce';
    protected $defaults = [
        'layout'           => 'two_columns',
        'show_order_notes' => true,
        'accent_color'     => '',
        'text_color'       => '',
        'form_style'       => 'modern',
        'heading_color'    => '',
        'border_color'     => '',
        'button_color'     => '',
        'button_bg'        => '',
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

        $uid = 'olo-woo-co-' . wp_rand( 10000, 99999 );

        // Colors
        $accent_color  = $this->safe_color_css( $s['accent_color'] );
        $text_color    = $this->safe_color_css( $s['text_color'] );
        $heading_color = $this->safe_color_css( $s['heading_color'] );
        $border_color  = $this->safe_color_css( $s['border_color'] );
        $btn_color     = $this->safe_color_css( $s['button_color'] );
        $btn_bg        = $this->safe_color_css( $s['button_bg'] );

        $layout    = in_array( $s['layout'], [ 'one_column', 'two_columns' ], true ) ? $s['layout'] : 'two_columns';
        $form_style = in_array( $s['form_style'], [ 'modern', 'classic' ], true ) ? $s['form_style'] : 'modern';

        // Hide order notes if disabled
        if ( empty( $s['show_order_notes'] ) ) {
            add_filter( 'woocommerce_enable_order_comments', '__return_false' );
        }

        $border_radius = ( $form_style === 'modern' ) ? '8px' : '4px';
        $input_padding = ( $form_style === 'modern' ) ? '12px 16px' : '8px 12px';

        ob_start();
        ?>
<?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from safe_color_css()-validated colors, fixed literal ternaries ($border_radius/$input_padding) gated by in_array() whitelists, and the internally generated $uid. Column 0 + closing tag so this line emits zero bytes. ?>
        <style>
            .<?php echo $uid; ?> .woocommerce {
                color: <?php echo $text_color; ?>;
            }
            <?php if ( $layout === 'two_columns' ) : ?>
            .<?php echo $uid; ?> .woocommerce .col2-set {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 32px;
            }
            @media (max-width: 768px) {
                .<?php echo $uid; ?> .woocommerce .col2-set {
                    grid-template-columns: 1fr;
                }
            }
            <?php else : ?>
            .<?php echo $uid; ?> .woocommerce .col2-set {
                max-width: 640px;
            }
            <?php endif; ?>
            .<?php echo $uid; ?> .woocommerce h3 {
                font-size: 20px;
                font-weight: 700;
                color: <?php echo $heading_color; ?>;
                margin: 0 0 20px;
                padding-bottom: 12px;
                border-bottom: 2px solid <?php echo $accent_color; ?>;
            }
            .<?php echo $uid; ?> .woocommerce .form-row label {
                display: block;
                font-size: 13px;
                font-weight: 600;
                color: <?php echo $heading_color; ?>;
                margin-bottom: 6px;
            }
            .<?php echo $uid; ?> .woocommerce .form-row input[type="text"],
            .<?php echo $uid; ?> .woocommerce .form-row input[type="email"],
            .<?php echo $uid; ?> .woocommerce .form-row input[type="tel"],
            .<?php echo $uid; ?> .woocommerce .form-row select,
            .<?php echo $uid; ?> .woocommerce .form-row textarea {
                width: 100%;
                padding: <?php echo $input_padding; ?>;
                border: 1px solid <?php echo $border_color; ?>;
                border-radius: <?php echo $border_radius; ?>;
                font-size: 14px;
                color: <?php echo $text_color; ?>;
                transition: border-color 0.2s ease;
                box-sizing: border-box;
            }
            .<?php echo $uid; ?> .woocommerce .form-row input:focus-visible,
            .<?php echo $uid; ?> .woocommerce .form-row select:focus-visible,
            .<?php echo $uid; ?> .woocommerce .form-row textarea:focus-visible {
                outline: none;
                border-color: <?php echo $accent_color; ?>;
                box-shadow: 0 0 0 3px color-mix(in srgb, <?php echo $accent_color; ?> 25%, transparent);
            }
            .<?php echo $uid; ?> .woocommerce .form-row {
                margin-bottom: 16px;
            }
            .<?php echo $uid; ?> .woocommerce #order_review_heading {
                font-size: 20px;
                font-weight: 700;
                color: <?php echo $heading_color; ?>;
                margin: 32px 0 20px;
                padding-bottom: 12px;
                border-bottom: 2px solid <?php echo $accent_color; ?>;
            }
            .<?php echo $uid; ?> .woocommerce table.shop_table {
                width: 100%;
                border-collapse: collapse;
                border: 1px solid <?php echo $border_color; ?>;
                border-radius: <?php echo $border_radius; ?>;
                overflow: hidden;
                margin-bottom: 24px;
            }
            .<?php echo $uid; ?> .woocommerce table.shop_table th {
                background: var(--olo-color-muted, #F3F4F6);
                color: <?php echo $heading_color; ?>;
                font-weight: 600;
                font-size: 13px;
                padding: 12px 16px;
                text-align: left;
                border-bottom: 1px solid <?php echo $border_color; ?>;
            }
            .<?php echo $uid; ?> .woocommerce table.shop_table td {
                padding: 12px 16px;
                border-bottom: 1px solid <?php echo $border_color; ?>;
                font-size: 14px;
            }
            .<?php echo $uid; ?> .woocommerce table.shop_table .order-total td {
                font-size: 18px;
                font-weight: 700;
                color: <?php echo $heading_color; ?>;
            }
            .<?php echo $uid; ?> .woocommerce #place_order {
                display: block;
                width: 100%;
                padding: 14px 24px;
                background: <?php echo $btn_bg; ?>;
                color: <?php echo $btn_color; ?>;
                border: none;
                border-radius: <?php echo $border_radius; ?>;
                font-size: 16px;
                font-weight: 700;
                cursor: pointer;
                transition: opacity 0.2s ease;
                margin-top: 16px;
            }
            .<?php echo $uid; ?> .woocommerce #place_order:hover {
                opacity: 0.9;
            }
            .<?php echo $uid; ?> .woocommerce .woocommerce-checkout-payment {
                background: var(--olo-color-muted, #F3F4F6);
                border: 1px solid <?php echo $border_color; ?>;
                border-radius: <?php echo $border_radius; ?>;
                padding: 20px;
            }
            .<?php echo $uid; ?> .woocommerce .wc_payment_methods {
                list-style: none;
                padding: 0;
                margin: 0 0 16px;
            }
            .<?php echo $uid; ?> .woocommerce .wc_payment_methods li {
                padding: 12px 0;
                border-bottom: 1px solid <?php echo $border_color; ?>;
            }
            .<?php echo $uid; ?> .woocommerce .wc_payment_methods li:last-child {
                border-bottom: none;
            }
            .<?php echo $uid; ?> .woocommerce .wc_payment_methods li label {
                font-weight: 600;
                color: <?php echo $heading_color; ?>;
                cursor: pointer;
            }
        </style>
<?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped -- column 0 + closing tag so this line emits zero bytes ?>
        <div class="<?php echo esc_attr( $uid ); ?>">
            <?php echo do_shortcode( '[woocommerce_checkout]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- checkout form HTML generated by the WooCommerce [woocommerce_checkout] shortcode ?>
        </div>
        <?php

        // Remove filter after rendering
        if ( empty( $s['show_order_notes'] ) ) {
            remove_filter( 'woocommerce_enable_order_comments', '__return_false' );
        }

                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS built by Olo_Tile_Base::build_border_css() from sanitized border settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS built by Olo_Tile_Base border helpers from sanitized border settings
        }
        return ob_get_clean();
    }
}
