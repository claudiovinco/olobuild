<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Woo_Product_Meta_Tile extends Olo_Tile_Base {

    protected $type     = 'woo_product_meta';
    protected $name     = 'Meta Prodotto';
    protected $icon     = 'dashicons-info-outline';
    protected $category = 'woocommerce';
    protected $defaults = [
        'show_sku'        => true,
        'show_categories' => true,
        'show_tags'       => true,
        'layout'          => 'stacked',
        'separator'       => '|',
        'text_color'      => '#6B7280',
        'label_color'     => '',
        'link_color'      => '#6366F1',
        'font_size'       => 14,
        'label_weight'    => '600',
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

        $uid = 'olo-woo-pmeta-' . wp_rand( 10000, 99999 );

        // Colors
        $text_color  = $this->safe_color_css( $s['text_color'] );
        $label_color = $this->safe_color_css( $s['label_color'] );
        $link_color  = $this->safe_color_css( $s['link_color'] );

        // Font
        $font_size    = max( 11, min( 24, absint( $s['font_size'] ) ) );
        $label_weight = in_array( $s['label_weight'], [ '400', '500', '600', '700' ], true ) ? $s['label_weight'] : '600';
        $layout       = in_array( $s['layout'], [ 'stacked', 'inline' ], true ) ? $s['layout'] : 'stacked';
        $separator    = sanitize_text_field( $s['separator'] );

        // Collect meta items
        $items = [];

        if ( ! empty( $s['show_sku'] ) ) {
            $sku = $product->get_sku();
            if ( $sku ) {
                $items[] = [
                    'label' => olo_t( 'SKU' ),
                    'value' => esc_html( $sku ),
                ];
            }
        }

        if ( ! empty( $s['show_categories'] ) ) {
            $cats = wc_get_product_category_list( $product->get_id(), ', ' );
            if ( $cats ) {
                $items[] = [
                    'label' => olo_t( 'Categoria' ),
                    'value' => $cats,
                    'is_html' => true,
                ];
            }
        }

        if ( ! empty( $s['show_tags'] ) ) {
            $tags = wc_get_product_tag_list( $product->get_id(), ', ' );
            if ( $tags ) {
                $items[] = [
                    'label' => olo_t( 'Tag' ),
                    'value' => $tags,
                    'is_html' => true,
                ];
            }
        }

        if ( empty( $items ) ) {
            return '<div style="padding:20px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);font-size:14px;font-style:italic;">'
                 . esc_html( olo_t( 'Nessuna informazione meta disponibile' ) )
                 . '</div>';
        }

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                font-size: <?php echo $font_size; ?>px;
                color: <?php echo $text_color; ?>;
            }
            .<?php echo $uid; ?>.layout-stacked {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }
            .<?php echo $uid; ?>.layout-inline {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 6px;
            }
            .<?php echo $uid; ?> .olo-woo-meta-label {
                color: <?php echo $label_color; ?>;
                font-weight: <?php echo $label_weight; ?>;
                margin-right: 6px;
            }
            .<?php echo $uid; ?> .olo-woo-meta-sep {
                color: <?php echo $text_color; ?>;
                opacity: 0.4;
                margin: 0 4px;
            }
            .<?php echo $uid; ?> a {
                color: <?php echo $link_color; ?>;
                text-decoration: none;
            }
            .<?php echo $uid; ?> a:hover {
                text-decoration: underline;
            }
        </style>
        <div class="<?php echo esc_attr( $uid ); ?> layout-<?php echo $layout; ?>">
            <?php foreach ( $items as $idx => $item ) : ?>
                <?php if ( $layout === 'inline' ) : ?>
                    <?php if ( $idx > 0 ) : ?>
                    <span class="olo-woo-meta-sep"><?php echo esc_html( $separator ); ?></span>
                    <?php endif; ?>
                <?php endif; ?>
                <span class="olo-woo-meta-item">
                    <span class="olo-woo-meta-label"><?php echo esc_html( $item['label'] ); ?>:</span>
                    <?php if ( ! empty( $item['is_html'] ) ) : ?>
                        <?php echo $item['value']; ?>
                    <?php else : ?>
                        <?php echo $item['value']; ?>
                    <?php endif; ?>
                </span>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
