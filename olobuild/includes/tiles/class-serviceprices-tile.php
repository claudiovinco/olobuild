<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_ServicePrices_Tile extends Olo_Tile_Base {

    protected $type     = 'serviceprices';
    protected $name     = 'Prezzi Servizio';
    protected $icon     = 'dashicons-money-alt';
    protected $category = 'booking';
    protected $defaults = [
        'meta_prefix'   => '_olo_service_',
        'columns'       => 3,
        'gap'           => 16,
        'card_radius'   => 12,
        'card_border'   => '#E5E7EB',
        'card_padding'  => 20,
        'accent_color'  => '',
        'amount_size'   => 26,
        'label_size'    => 14,
        'label_color'   => '#6B7280',
        'hover_border'  => true,
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        global $post;
        if ( ! $post || ! is_singular() ) {
            return '<div style="padding:24px;text-align:center;color:#9ca3af;background:#f9fafb;border-radius:8px">'
                 . '<p style="margin:0">Inserisci in un template single.</p></div>';
        }

        $pid = $post->ID;
        $pfx = rtrim( $s['meta_prefix'], '_' ) . '_';

        $color  = get_post_meta( $pid, $pfx . 'color', true ) ?: '#6366F1';
        $accent = ! empty( $s['accent_color'] ) ? $s['accent_color'] : $color;

        $prices = [];
        $price_base = get_post_meta( $pid, $pfx . 'price', true );

        if ( $price_base ) $prices[] = [ number_format( (float) $price_base, 2, ',', '.' ), olo_t( '/ notte' ) ];

        if ( empty( $prices ) ) {
            return '';
        }

        $cols   = absint( $s['columns'] ) ?: 3;
        $gap    = absint( $s['gap'] );
        $radius = absint( $s['card_radius'] );
        $uid    = 'olo-sprices-' . wp_rand( 10000, 99999 );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> { display:grid; grid-template-columns:repeat(<?php echo $cols; ?>,1fr); gap:<?php echo $gap; ?>px; }
            .<?php echo $uid; ?> .olo-sp-card { border:2px solid <?php echo esc_attr( $s['card_border'] ); ?>; border-radius:<?php echo $radius; ?>px; padding:<?php echo absint( $s['card_padding'] ); ?>px 16px; text-align:center; transition:border-color 0.2s; }
            <?php if ( ! empty( $s['hover_border'] ) ) : ?>
            .<?php echo $uid; ?> .olo-sp-card:hover { border-color:<?php echo esc_attr( $accent ); ?>; }
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-sp-amount { font-size:<?php echo absint( $s['amount_size'] ); ?>px; font-weight:700; color:<?php echo esc_attr( $accent ); ?>; }
            .<?php echo $uid; ?> .olo-sp-label { font-size:<?php echo absint( $s['label_size'] ); ?>px; color:<?php echo esc_attr( $s['label_color'] ); ?>; margin-top:4px; }
            @media (max-width:640px) { .<?php echo $uid; ?> { grid-template-columns:1fr; } }
        </style>
        <div class="<?php echo esc_attr( $uid ); ?>">
            <?php foreach ( $prices as $p ) : ?>
            <div class="olo-sp-card">
                <div class="olo-sp-amount">&euro; <?php echo esc_html( $p[0] ); ?></div>
                <div class="olo-sp-label"><?php echo esc_html( $p[1] ); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
