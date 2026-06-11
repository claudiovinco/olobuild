<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Woo_Rating_Tile extends Olo_Tile_Base {

    protected $type     = 'woo_rating';
    protected $name     = 'Valutazione Prodotto';
    protected $icon     = 'dashicons-star-filled';
    protected $category = 'woocommerce';
    protected $defaults = [
        'show_count'       => true,
        'show_average'     => true,
        'star_color'       => '',
        'empty_star_color' => '',
        'text_color'       => '',
        'star_size'        => 20,
        'text_size'        => 14,
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return '<div style="padding:20px;text-align:center;color:var(--olo-color-warning, #b45309);background:color-mix(in srgb, var(--olo-color-warning, #b45309) 12%, #fff);border:1px solid var(--olo-color-warning, #b45309);border-radius:8px;">'
                 . esc_html( olo_t( 'WooCommerce non attivo.' ) )
                 . '</div>';
        }

        global $product;
        if ( ! is_a( $product, 'WC_Product' ) ) {
            $product = wc_get_product( get_the_ID() );
        }
        if ( ! $product ) {
            return '<div style="padding:20px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF)">'
                 . esc_html( olo_t( 'Nessun prodotto trovato' ) )
                 . '</div>';
        }

        $s = wp_parse_args( $settings, $this->defaults );

        $avg    = (float) $product->get_average_rating();
        $count  = (int) $product->get_review_count();
        $size   = max( 12, min( 48, absint( $s['star_size'] ) ) );
        $t_size = max( 10, min( 24, absint( $s['text_size'] ) ) );
        // TOKEN-FIRST: stelle = accento (ambra), vuote = bordo, testo = neutro soft.
        $fill   = $this->safe_color_css( $s['star_color'] )       ?: 'var(--olo-color-accent, #f4a23b)';
        $empty  = $this->safe_color_css( $s['empty_star_color'] ) ?: 'var(--olo-color-border, #e5e7eb)';
        $txt    = $this->safe_color_css( $s['text_color'] )       ?: 'var(--olo-color-text-soft, #6b7280)';

        $full_stars  = floor( $avg );
        $half_star   = ( ( $avg - $full_stars ) >= 0.5 ) ? 1 : 0;
        $empty_stars = 5 - $full_stars - $half_star;

        $star_path = 'M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z';

        ob_start();
        echo '<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">';
        echo '<div style="display:flex;gap:2px;align-items:center">';
        for ( $i = 0; $i < $full_stars; $i++ ) {
            echo '<svg width="' . (int) $size . '" height="' . (int) $size . '" viewBox="0 0 24 24" fill="' . $fill . '" stroke="none"><path d="' . $star_path . '"/></svg>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $fill safe_color_css()'d above; $star_path is a static literal
        }
        if ( $half_star ) {
            echo '<svg width="' . (int) $size . '" height="' . (int) $size . '" viewBox="0 0 24 24" stroke="none"><defs><linearGradient id="olo-half-' . (int) $size . '"><stop offset="50%" stop-color="' . $fill . '"/><stop offset="50%" stop-color="' . $empty . '"/></linearGradient></defs><path d="' . $star_path . '" fill="url(#olo-half-' . (int) $size . ')"/></svg>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $fill/$empty safe_color_css()'d above; $star_path is a static literal
        }
        for ( $i = 0; $i < $empty_stars; $i++ ) {
            echo '<svg width="' . (int) $size . '" height="' . (int) $size . '" viewBox="0 0 24 24" fill="' . $empty . '" stroke="none"><path d="' . $star_path . '"/></svg>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $empty safe_color_css()'d above; $star_path is a static literal
        }
        echo '</div>';

        $meta_parts = [];
        if ( ! empty( $s['show_average'] ) ) {
            $meta_parts[] = number_format( $avg, 1 );
        }
        if ( ! empty( $s['show_count'] ) ) {
            $meta_parts[] = '(' . $count . ' ' . esc_html( olo_t( 'recensioni' ) ) . ')';
        }
        if ( ! empty( $meta_parts ) ) {
            echo '<span style="color:' . $txt . ';font-size:' . (int) $t_size . 'px">' . implode( ' ', $meta_parts ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $txt safe_color_css()'d above; $meta_parts built from number_format(), (int) count and esc_html()'d label
        }
        echo '</div>';

        return ob_get_clean();
    }
}
