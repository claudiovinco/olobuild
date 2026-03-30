<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Woo_Product_Navigation_Tile extends Olo_Tile_Base {

    protected $type     = 'woo_product_navigation';
    protected $name     = 'Navigazione Prodotti';
    protected $icon     = 'dashicons-leftright';
    protected $category = 'woocommerce';
    protected $defaults = [
        'show_thumbnail' => true,
        'show_label'     => true,
        'label_prev'     => 'Prodotto precedente',
        'label_next'     => 'Prodotto successivo',
        'text_color'     => '#374151',
        'hover_color'    => '#6366F1',
        'separator_style' => 'line',
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

        // Verify we are on a product
        global $product;
        if ( ! is_a( $product, 'WC_Product' ) ) {
            $product = wc_get_product( get_the_ID() );
        }
        if ( ! $product ) {
            return '<div style="padding:20px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);font-size:14px;">'
                 . esc_html( olo_t( 'Nessun prodotto disponibile in questo contesto' ) )
                 . '</div>';
        }

        $uid = 'olo-woo-pnav-' . wp_rand( 10000, 99999 );

        // Colors
        $text_color  = $this->safe_color_css( $s['text_color'] );
        $hover_color = $this->safe_color_css( $s['hover_color'] );

        // Adjacent products (within same category)
        $prev_product = $this->get_adjacent_product( true );
        $next_product = $this->get_adjacent_product( false );

        // Labels
        $label_prev = sanitize_text_field( $s['label_prev'] );
        $label_next = sanitize_text_field( $s['label_next'] );

        // Separator
        $sep_styles = [
            'line'   => 'border-left:1px solid var(--olo-color-border, #E5E7EB)',
            'dotted' => 'border-left:1px dotted var(--olo-color-border, #E5E7EB)',
            'none'   => 'border:none',
        ];
        $sep_style = isset( $sep_styles[ $s['separator_style'] ] ) ? $sep_styles[ $s['separator_style'] ] : $sep_styles['line'];

        $show_thumb = ! empty( $s['show_thumbnail'] );
        $show_label = ! empty( $s['show_label'] );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                display: flex;
                align-items: stretch;
                justify-content: space-between;
                gap: 0;
            }
            .<?php echo $uid; ?> .olo-pnav-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 16px 0;
                text-decoration: none;
                color: <?php echo $text_color; ?>;
                transition: color 0.2s ease;
                flex: 1;
            }
            .<?php echo $uid; ?> .olo-pnav-item:hover {
                color: <?php echo $hover_color; ?>;
            }
            .<?php echo $uid; ?> .olo-pnav-item.olo-pnav-next {
                text-align: right;
                justify-content: flex-end;
                padding-left: 20px;
                <?php echo $sep_style; ?>;
            }
            .<?php echo $uid; ?> .olo-pnav-item.olo-pnav-prev {
                padding-right: 20px;
            }
            .<?php echo $uid; ?> .olo-pnav-thumb {
                width: 56px;
                height: 56px;
                border-radius: 8px;
                object-fit: cover;
                flex-shrink: 0;
            }
            .<?php echo $uid; ?> .olo-pnav-text {
                display: flex;
                flex-direction: column;
                gap: 2px;
            }
            .<?php echo $uid; ?> .olo-pnav-label {
                font-size: 12px;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                opacity: 0.6;
            }
            .<?php echo $uid; ?> .olo-pnav-name {
                font-size: 15px;
                font-weight: 600;
                line-height: 1.3;
            }
            .<?php echo $uid; ?> .olo-pnav-arrow {
                flex-shrink: 0;
                opacity: 0.4;
            }
            .<?php echo $uid; ?> .olo-pnav-empty {
                flex: 1;
            }
        </style>
        <div class="<?php echo esc_attr( $uid ); ?>">
            <?php if ( $prev_product ) :
                $prev_thumb = get_the_post_thumbnail_url( $prev_product->get_id(), 'thumbnail' );
            ?>
            <a href="<?php echo esc_url( $prev_product->get_permalink() ); ?>" class="olo-pnav-item olo-pnav-prev">
                <span class="olo-pnav-arrow">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                </span>
                <?php if ( $show_thumb ) : ?>
                    <?php if ( $prev_thumb ) : ?>
                    <img src="<?php echo esc_url( $prev_thumb ); ?>" alt="" class="olo-pnav-thumb" />
                    <?php endif; ?>
                <?php endif; ?>
                <span class="olo-pnav-text">
                    <?php if ( $show_label ) : ?>
                    <span class="olo-pnav-label"><?php echo esc_html( $label_prev ); ?></span>
                    <?php endif; ?>
                    <span class="olo-pnav-name"><?php echo esc_html( $prev_product->get_name() ); ?></span>
                </span>
            </a>
            <?php else : ?>
            <span class="olo-pnav-empty"></span>
            <?php endif; ?>

            <?php if ( $next_product ) :
                $next_thumb = get_the_post_thumbnail_url( $next_product->get_id(), 'thumbnail' );
            ?>
            <a href="<?php echo esc_url( $next_product->get_permalink() ); ?>" class="olo-pnav-item olo-pnav-next">
                <span class="olo-pnav-text">
                    <?php if ( $show_label ) : ?>
                    <span class="olo-pnav-label"><?php echo esc_html( $label_next ); ?></span>
                    <?php endif; ?>
                    <span class="olo-pnav-name"><?php echo esc_html( $next_product->get_name() ); ?></span>
                </span>
                <?php if ( $show_thumb ) : ?>
                    <?php if ( $next_thumb ) : ?>
                    <img src="<?php echo esc_url( $next_thumb ); ?>" alt="" class="olo-pnav-thumb" />
                    <?php endif; ?>
                <?php endif; ?>
                <span class="olo-pnav-arrow">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
            </a>
            <?php else : ?>
            <span class="olo-pnav-empty"></span>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Get adjacent product within the same product category.
     *
     * @param bool $previous True for previous, false for next.
     * @return WC_Product|null
     */
    private function get_adjacent_product( $previous = true ) {
        $post = get_adjacent_post( true, '', $previous, 'product_cat' );
        if ( $post ) {
            $adj_product = wc_get_product( $post->ID );
            if ( $adj_product ) {
                if ( $adj_product->is_visible() ) {
                    return $adj_product;
                }
            }
        }
        return null;
    }
}
