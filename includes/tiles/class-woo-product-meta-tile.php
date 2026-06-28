<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Woo_Product_Meta_Tile extends Olobuild_Tile_Base {

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
        'text_color'      => '',
        'label_color'     => '',
        'link_color'      => '',
        'font_size'       => 14,
        'label_weight'    => '600',
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
                 . esc_html( olobuild_t( 'WooCommerce non attivo. Installa e attiva WooCommerce per utilizzare questo elemento.' ) )
                 . '</div>';
        }

        $s = wp_parse_args( $settings, $this->defaults );

        // Get the current product
        global $product;
        if ( ! is_a( $product, 'WC_Product' ) ) {
            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- global $product di WooCommerce, non un global definito da olobuild
            $product = wc_get_product( get_the_ID() );
        }
        if ( ! $product ) {
            return '<div style="padding:20px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);font-size:14px;">'
                 . esc_html( olobuild_t( 'Nessun prodotto disponibile in questo contesto' ) )
                 . '</div>';
        }

        $uid = 'olo-woo-pmeta-' . wp_rand( 10000, 99999 );

        // Colors — TOKEN-FIRST: valore neutro soft, etichetta testo pieno, link col token link.
        $text_color  = $this->safe_color_css( $s['text_color'] )  ?: 'var(--olo-color-text-soft, #6b7280)';
        $label_color = $this->safe_color_css( $s['label_color'] ) ?: 'var(--olo-color-text, #1f2937)';
        $link_color  = $this->safe_color_css( $s['link_color'] )  ?: 'var(--olo-color-link, #e1474f)';

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
                    'label' => olobuild_t( 'SKU' ),
                    'value' => esc_html( $sku ),
                ];
            }
        }

        if ( ! empty( $s['show_categories'] ) ) {
            $cats = wc_get_product_category_list( $product->get_id(), ', ' );
            if ( $cats ) {
                $items[] = [
                    'label' => olobuild_t( 'Categoria' ),
                    'value' => $cats,
                    'is_html' => true,
                ];
            }
        }

        if ( ! empty( $s['show_tags'] ) ) {
            $tags = wc_get_product_tag_list( $product->get_id(), ', ' );
            if ( $tags ) {
                $items[] = [
                    'label' => olobuild_t( 'Tag' ),
                    'value' => $tags,
                    'is_html' => true,
                ];
            }
        }

        if ( empty( $items ) ) {
            return '<div style="padding:20px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);font-size:14px;font-style:italic;">'
                 . esc_html( olobuild_t( 'Nessuna informazione meta disponibile' ) )
                 . '</div>';
        }

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist for every colour, absint() clamp for the font size, in_array() whitelisted weight and the internally generated $uid. ?>
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
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="<?php echo esc_attr( $uid ); ?> layout-<?php echo esc_attr( $layout ); ?>">
            <?php foreach ( $items as $idx => $item ) : ?>
                <?php if ( $layout === 'inline' ) : ?>
                    <?php if ( $idx > 0 ) : ?>
                    <span class="olo-woo-meta-sep"><?php echo esc_html( $separator ); ?></span>
                    <?php endif; ?>
                <?php endif; ?>
                <span class="olo-woo-meta-item">
                    <span class="olo-woo-meta-label"><?php echo esc_html( $item['label'] ); ?>:</span>
                    <?php if ( ! empty( $item['is_html'] ) ) : ?>
                        <?php echo $item['value']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- term-link HTML from WooCommerce core wc_get_product_category_list()/wc_get_product_tag_list() (escaped internally) ?>
                    <?php else : ?>
                        <?php echo $item['value']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_html()'d at construction above (SKU) ?>
                    <?php endif; ?>
                </span>
            <?php endforeach; ?>
        </div>
        <?php
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }
}
