<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Olo_Paymentbuttons_Tile extends Olo_Tile_Base {
    protected $type     = 'paymentbuttons';
    protected $name     = 'Pulsanti Pagamento';
    protected $icon     = 'dashicons-money-alt';
    protected $category = 'marketing';

    public function get_controls() { return []; }

    public function render( $settings, $id = '' ) {
        $provider    = $settings['provider'] ?? 'stripe';
        $uid = 'olo-pb-' . wp_rand( 10000, 99999 );
        $amount      = esc_attr( $settings['amount'] ?? '29.99' );
        $currency    = esc_attr( $settings['currency'] ?? 'EUR' );
        $description = esc_attr( $settings['description'] ?? '' );
        $btn_text    = esc_html( $settings['button_text'] ?? 'Paga ora' );
        $success_url = esc_url( $settings['success_url'] ?? '' );
        $cancel_url  = esc_url( $settings['cancel_url'] ?? '' );
        $alignment   = esc_attr( $settings['alignment'] ?? 'center' );
        $bg_color    = $settings['bg_color'] ?: 'var(--olo-color-primary, #6366F1)';
        $text_color  = $settings['text_color'] ?: '#ffffff';
        $radius      = Olo_Tile_Utils::border_radius( $settings['border_radius'] ?? 8 );
        $font_size   = intval( $settings['font_size'] ?? 16 );
        $full_width  = ! empty( $settings['full_width'] );
        $stripe_key  = esc_attr( $settings['stripe_key'] ?? '' );
        $stripe_price = esc_attr( $settings['stripe_price_id'] ?? '' );
        $paypal_id   = esc_attr( $settings['paypal_client_id'] ?? '' );
        $paypal_style = esc_attr( $settings['paypal_style'] ?? 'rect' );

        $symbols = [ 'EUR' => '€', 'USD' => '$', 'GBP' => '£' ];
        $sym = $symbols[ $currency ] ?? '';

        $btn_style = "background-color:{$bg_color};color:{$text_color};border-radius:{$radius};font-size:{$font_size}px;padding:12px 32px;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;font-weight:600;";
        if ( $full_width ) $btn_style .= 'width:100%;justify-content:center;';

        $html = '<div class="olo-paymentbuttons ' . esc_attr( $uid ) . '" style="text-align:' . $alignment . '">';

        if ( $provider === 'stripe' || $provider === 'both' ) {
            $html .= '<button class="olo-pay-btn olo-pay-btn--stripe" style="' . esc_attr( $btn_style ) . '"';
            if ( $stripe_key ) {
                $html .= ' data-stripe-key="' . $stripe_key . '"';
                $html .= ' data-stripe-price="' . $stripe_price . '"';
                $html .= ' data-success="' . $success_url . '"';
                $html .= ' data-cancel="' . $cancel_url . '"';
            }
            $html .= '>';
            $html .= '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>';
            $html .= $btn_text . '</button>';
        }

        if ( $provider === 'paypal' || $provider === 'both' ) {
            $pp_style = $btn_style . 'background-color:#0070ba;';
            if ( $provider === 'both' ) $pp_style .= 'margin-left:12px;';
            $html .= '<div id="olo-paypal-' . esc_attr( $id ) . '"';
            $html .= ' class="olo-pay-paypal-container"';
            if ( $paypal_id ) {
                $html .= ' data-paypal-id="' . $paypal_id . '"';
                $html .= ' data-amount="' . $amount . '"';
                $html .= ' data-currency="' . $currency . '"';
                $html .= ' data-description="' . $description . '"';
                $html .= ' data-style="' . $paypal_style . '"';
            }
            $html .= '></div>';
        }

        $html .= '<div class="olo-pay-amount" style="margin-top:8px;font-size:13px;opacity:0.7;">' . $sym . $amount . ' ' . $currency . '</div>';
        $html .= '</div>';

                $border_css        = $this->build_border_css( $settings['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( '.' . $uid, $settings['border'] ?? [], $settings['border_hover'] ?? [], intval( $settings['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( '.' . $uid, $settings['border'] ?? [], $settings );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            $html .= '<style>';
            if ( $border_css ) $html .= '.' . $uid . '{' . $border_css . '}';
            $html .= $border_hover_css . $border_effect_css . '</style>';
        }
        return $html;
    }
}
