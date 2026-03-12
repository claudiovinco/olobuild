<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Woo_Order_Tracking_Tile extends Olo_Tile_Base {

    protected $type     = 'woo_order_tracking';
    protected $name     = 'Tracciamento Ordine';
    protected $icon     = 'dashicons-search';
    protected $category = 'woocommerce';
    protected $defaults = [
        'title'        => 'Traccia il tuo ordine',
        'title_tag'    => 'h2',
        'accent_color' => '#6366F1',
        'text_color'   => '#374151',
        'button_color' => '#FFFFFF',
        'button_bg'    => '#6366F1',
        'form_style'   => 'modern',
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

        $uid = 'olo-woo-ot-' . wp_rand( 10000, 99999 );

        // Colors
        $accent_color = $this->safe_color_css( $s['accent_color'] );
        $text_color   = $this->safe_color_css( $s['text_color'] );
        $button_color = $this->safe_color_css( $s['button_color'] );
        $button_bg    = $this->safe_color_css( $s['button_bg'] );

        // Title tag
        $allowed_tags = [ 'h2', 'h3', 'h4', 'h5' ];
        $title_tag = in_array( $s['title_tag'], $allowed_tags, true ) ? $s['title_tag'] : 'h2';
        $title     = sanitize_text_field( $s['title'] );

        // Form style
        $is_modern = ( $s['form_style'] === 'modern' );

        // Tracking URL
        $tracking_url = wc_get_endpoint_url( 'order-received', '', wc_get_page_permalink( 'checkout' ) );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                color: <?php echo $text_color; ?>;
            }
            .<?php echo $uid; ?> .olo-ot-title {
                color: <?php echo $text_color; ?>;
                margin: 0 0 24px 0;
                font-size: 24px;
                font-weight: 700;
            }
            .<?php echo $uid; ?> .olo-ot-form {
                display: flex;
                flex-direction: column;
                gap: 16px;
                max-width: 480px;
            }
            .<?php echo $uid; ?> .olo-ot-field {
                display: flex;
                flex-direction: column;
                gap: 6px;
            }
            .<?php echo $uid; ?> .olo-ot-label {
                font-size: 14px;
                font-weight: 600;
                color: <?php echo $text_color; ?>;
            }
            .<?php echo $uid; ?> .olo-ot-input {
                padding: <?php echo $is_modern ? '12px 16px' : '8px 12px'; ?>;
                border: <?php echo $is_modern ? '2px solid var(--olo-color-border, #E5E7EB)' : '1px solid var(--olo-color-border, #E5E7EB)'; ?>;
                border-radius: <?php echo $is_modern ? '10px' : '4px'; ?>;
                font-size: 15px;
                transition: border-color 0.2s ease;
                outline: none;
                width: 100%;
                box-sizing: border-box;
            }
            .<?php echo $uid; ?> .olo-ot-input:focus {
                border-color: <?php echo $accent_color; ?>;
            }
            .<?php echo $uid; ?> .olo-ot-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                padding: <?php echo $is_modern ? '14px 28px' : '10px 20px'; ?>;
                background: <?php echo $button_bg; ?>;
                color: <?php echo $button_color; ?>;
                border: none;
                border-radius: <?php echo $is_modern ? '10px' : '4px'; ?>;
                font-size: 15px;
                font-weight: 600;
                cursor: pointer;
                transition: opacity 0.2s ease;
                margin-top: 4px;
            }
            .<?php echo $uid; ?> .olo-ot-btn:hover {
                opacity: 0.9;
            }
        </style>
        <div class="<?php echo esc_attr( $uid ); ?>">
            <?php if ( $title !== '' ) : ?>
            <<?php echo $title_tag; ?> class="olo-ot-title"><?php echo esc_html( $title ); ?></<?php echo $title_tag; ?>>
            <?php endif; ?>
            <form class="olo-ot-form" method="post" action="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
                <div class="olo-ot-field">
                    <label class="olo-ot-label" for="<?php echo esc_attr( $uid ); ?>-order-id"><?php echo esc_html( olo_t( 'Numero ordine' ) ); ?></label>
                    <input type="text" class="olo-ot-input" name="orderid" id="<?php echo esc_attr( $uid ); ?>-order-id" placeholder="<?php echo esc_attr( olo_t( 'Inserisci qui il numero del tuo ordine' ) ); ?>" required />
                </div>
                <div class="olo-ot-field">
                    <label class="olo-ot-label" for="<?php echo esc_attr( $uid ); ?>-email"><?php echo esc_html( olo_t( 'Email di fatturazione' ) ); ?></label>
                    <input type="email" class="olo-ot-input" name="order_email" id="<?php echo esc_attr( $uid ); ?>-email" placeholder="<?php echo esc_attr( olo_t( 'Email usata per l\'ordine' ) ); ?>" required />
                </div>
                <?php wp_nonce_field( 'woocommerce-order_tracking', 'woocommerce-order-tracking-nonce' ); ?>
                <button type="submit" class="olo-ot-btn" name="track" value="<?php echo esc_attr( olo_t( 'Traccia' ) ); ?>">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <span><?php echo esc_html( olo_t( 'Traccia ordine' ) ); ?></span>
                </button>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
}
