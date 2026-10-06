<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Woo_Price_Tile extends Olobuild_Tile_Base {

    protected $type     = 'woo_price';
    protected $name     = 'Prezzo Prodotto';
    protected $icon     = 'dashicons-tag';
    protected $category = 'woocommerce';
    protected $defaults = [
        'show_regular'  => true,
        'show_sale'     => true,
        'show_suffix'   => false,
        'price_color'   => '',
        'sale_color'    => '',
        'regular_color' => '',
        'font_size'     => 24,
        'font_weight'   => '700',
        'text_align'    => 'left',
        'prefix'        => '',
        'suffix'        => '',
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

        // Il prodotto: quello di «ID prodotto», se no quello della pagina (vuoto o 0, come prima).
        // Senza il campo la tile funzionava solo nella scheda di un prodotto: in una pagina di
        // lancio non c'era modo di dirle quale. Il prodotto scelto resta una variabile della tile:
        // il $product globale è quello della pagina e serve alle tile che seguono.
        $pid     = absint( $s['product_id'] ?? 0 );
        $product = $pid ? wc_get_product( $pid ) : null;
        if ( ! $product ) {
            global $product;
            if ( ! is_a( $product, 'WC_Product' ) ) {
                // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- global $product di WooCommerce, non un global definito da olobuild
                $product = wc_get_product( get_the_ID() );
            }
        }
        if ( ! $product ) {
            // L'avviso è per chi costruisce la pagina (canvas del builder, utenti che possono
            // modificare): il visitatore se lo trovava scritto in pagina. A lui, niente.
            if ( empty( $s['_builder_mode'] ) && ! current_user_can( 'edit_posts' ) ) {
                return '';
            }
            return '<div style="padding:20px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);font-size:14px;">'
                 . esc_html( olobuild_t( 'Nessun prodotto disponibile in questo contesto' ) )
                 . '</div>';
        }

        $uid = 'olo-woo-price-' . wp_rand( 10000, 99999 );

        // Colors — TOKEN-FIRST: prezzo = testo, saldo = stato (rosso), barrato = neutro soft.
        $price_color   = $this->safe_color_css( $s['price_color'] )   ?: 'var(--olo-color-text, #1f2937)';
        $sale_color    = $this->safe_color_css( $s['sale_color'] )    ?: 'var(--olo-color-error, #b42318)';
        $regular_color = $this->safe_color_css( $s['regular_color'] ) ?: 'var(--olo-color-text-soft, #6b7280)';

        // Font
        $font_size   = max( 12, min( 72, absint( $s['font_size'] ) ) );
        // Tutti i pesi del controllo tipografia (100–900), anche salvati come numero.
        $font_weight = $this->font_weight_css( $s['font_weight'] ) ?: '700';
        $text_align  = in_array( $s['text_align'], [ 'left', 'center', 'right' ], true ) ? $s['text_align'] : 'left';

        // Prices
        $on_sale      = $product->is_on_sale();
        $html_barrato = '';
        $html_prezzo  = '';

        if ( $product instanceof WC_Product_Variable ) {
            // Il genitore variabile non ha un prezzo proprio (regolare e saldo vuoti →
            // wc_price('') = 0,00): come WooCommerce, forchetta delle varianti, oppure
            // barrato + saldo se tutte hanno lo stesso prezzo pieno. Senza suffisso:
            // quello lo governa la tile.
            $prezzi = $product->get_variation_prices( true );
            if ( ! empty( $prezzi['price'] ) ) {
                $min     = reset( $prezzi['price'] );
                $max     = end( $prezzi['price'] );
                $min_reg = reset( $prezzi['regular_price'] );
                $max_reg = end( $prezzi['regular_price'] );
                if ( $min !== $max ) {
                    $html_prezzo = wc_format_price_range( $min, $max );
                } elseif ( $on_sale && $min_reg === $max_reg ) {
                    $html_barrato = wc_price( $max_reg );
                    $html_prezzo  = wc_price( $min );
                } else {
                    $html_prezzo = wc_price( $min );
                }
            }
        } elseif ( $product instanceof WC_Product_Grouped ) {
            // Il raggruppato non ha prezzo proprio: forchetta dei figli visibili, come WooCommerce.
            $prezzi_figli = [];
            $figli        = array_filter( array_map( 'wc_get_product', $product->get_children() ), 'wc_products_array_filter_visible_grouped' );
            foreach ( $figli as $figlio ) {
                if ( '' !== $figlio->get_price() ) {
                    $prezzi_figli[] = wc_get_price_to_display( $figlio );
                }
            }
            if ( ! empty( $prezzi_figli ) ) {
                $min         = min( $prezzi_figli );
                $max         = max( $prezzi_figli );
                $html_prezzo = $min !== $max ? wc_format_price_range( $min, $max ) : wc_price( $min );
            }
        } else {
            // Semplici, esterni, singole varianti: invariato.
            $regular_raw  = $product->get_regular_price();
            $sale_raw     = $product->get_sale_price();
            $html_barrato = $on_sale ? wc_price( $regular_raw ) : '';
            $html_prezzo  = wc_price( $on_sale ? $sale_raw : $regular_raw );
        }

        $prefix = sanitize_text_field( $s['prefix'] );
        $suffix = sanitize_text_field( $s['suffix'] );

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: colours via the safe_color_css() whitelist, $font_size via absint()+min()/max() clamps (round() products), $font_weight via the font_weight_css() whitelist, $text_align via an in_array() whitelist; $uid is internally generated.
        ?>
        <style>
            .<?php echo $uid; ?> {
                text-align: <?php echo $text_align; ?>;
                padding: 4px 0;
            }
            .<?php echo $uid; ?> .olo-woo-price-prefix {
                color: <?php echo $price_color; ?>;
                font-size: <?php echo round( $font_size * 0.65 ); ?>px;
                margin-right: 6px;
            }
            .<?php echo $uid; ?> .olo-woo-price-regular {
                color: <?php echo $regular_color; ?>;
                text-decoration: line-through;
                font-size: <?php echo round( $font_size * 0.75 ); ?>px;
                font-weight: 400;
                margin-right: 8px;
            }
            .<?php echo $uid; ?> .olo-woo-price-regular.no-sale {
                color: <?php echo $price_color; ?>;
                text-decoration: none;
                font-size: <?php echo $font_size; ?>px;
                font-weight: <?php echo $font_weight; ?>;
            }
            .<?php echo $uid; ?> .olo-woo-price-sale {
                color: <?php echo $sale_color; ?>;
                font-size: <?php echo $font_size; ?>px;
                font-weight: <?php echo $font_weight; ?>;
            }
            .<?php echo $uid; ?> .olo-woo-price-suffix {
                color: <?php echo $price_color; ?>;
                font-size: <?php echo round( $font_size * 0.55 ); ?>px;
                margin-left: 4px;
                opacity: 0.7;
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="<?php echo esc_attr( $uid ); ?>">
            <?php if ( $prefix !== '' ) : ?>
            <span class="olo-woo-price-prefix"><?php echo esc_html( $prefix ); ?></span>
            <?php endif; ?>
            <?php if ( $on_sale ) : ?>
                <?php if ( ! empty( $s['show_regular'] ) && '' !== $html_barrato ) : ?>
                <span class="olo-woo-price-regular"><?php echo $html_barrato; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built above only by wc_price(), escaped WooCommerce price HTML ?></span>
                <?php endif; ?>
                <?php if ( ! empty( $s['show_sale'] ) && '' !== $html_prezzo ) : ?>
                <span class="olo-woo-price-sale"><?php echo $html_prezzo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built above only by wc_price()/wc_format_price_range(), escaped WooCommerce price HTML ?></span>
                <?php endif; ?>
            <?php else : ?>
                <?php if ( ! empty( $s['show_regular'] ) && '' !== $html_prezzo ) : ?>
                <span class="olo-woo-price-regular no-sale"><?php echo $html_prezzo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built above only by wc_price()/wc_format_price_range(), escaped WooCommerce price HTML ?></span>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ( ! empty( $s['show_suffix'] ) ) : ?>
            <span class="olo-woo-price-suffix"><?php echo $suffix ? esc_html( $suffix ) : wp_kses_post( $product->get_price_suffix() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tile text via esc_html(); WooCommerce's suffix is HTML (<small class="woocommerce-price-suffix">), filtered by wp_kses_post() ?></span>
            <?php endif; ?>
        </div>
        <?php
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized border settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() shared helpers
        }
        return ob_get_clean();
    }
}
