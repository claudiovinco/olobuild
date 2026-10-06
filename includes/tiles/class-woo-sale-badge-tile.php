<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Woo_Sale_Badge_Tile extends Olobuild_Tile_Base {

    protected $type     = 'woo_sale_badge';
    protected $name     = 'Badge Offerta';
    protected $icon     = 'dashicons-awards';
    protected $category = 'woocommerce';
    protected $defaults = [
        'badge_text'   => 'auto',
        'custom_text'  => 'Offerta!',
        'badge_bg'     => '',
        'badge_color'  => '',
        'badge_shape'  => 'pill',
        'position'     => 'top-left',
        'font_size'    => 14,
        'font_weight'  => '700',
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
            return '<div style="padding:20px;text-align:center;color:var(--olo-color-text-muted, #6B7280);font-size:14px;">'
                 . esc_html( olobuild_t( 'Nessun prodotto disponibile in questo contesto' ) )
                 . '</div>';
        }

        // Only show if product is on sale
        if ( ! $product->is_on_sale() ) {
            return '';
        }

        $uid = 'olo-woo-sb-' . wp_rand( 10000, 99999 );

        // Colors — vuoti nei default: senza riserva uscivano «background: ;» e «color: ;», che il
        // browser scarta, e il badge restava testo nudo senza pillola. La riserva è quella della
        // partenza del config: pillola nel colore del sito col suo contrasto.
        $badge_bg    = $this->safe_color_css( $s['badge_bg'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $badge_color = $this->safe_color_css( $s['badge_color'] ) ?: 'var(--olo-color-primary-contrast, #ffffff)';

        // Font
        $font_size   = max( 10, min( 32, absint( $s['font_size'] ) ) );
        $font_weight = in_array( $s['font_weight'], [ '400', '600', '700', '800' ], true ) ? $s['font_weight'] : '700';

        // Badge text
        $badge_text_mode = in_array( $s['badge_text'], [ 'auto', '%', 'custom' ], true ) ? $s['badge_text'] : 'auto';
        $badge_label     = '';

        if ( $badge_text_mode === 'auto' ) {
            // Calculate discount percentage
            $regular = floatval( $product->get_regular_price() );
            $sale    = floatval( $product->get_sale_price() );
            if ( $regular > 0 ) {
                $discount = round( ( ( $regular - $sale ) / $regular ) * 100 );
                $badge_label = '-' . $discount . '%';
            } else {
                $badge_label = olobuild_t( 'Offerta!' );
            }
        } elseif ( $badge_text_mode === '%' ) {
            $regular = floatval( $product->get_regular_price() );
            $sale    = floatval( $product->get_sale_price() );
            if ( $regular > 0 ) {
                $discount = round( ( ( $regular - $sale ) / $regular ) * 100 );
                $badge_label = '-' . $discount . '%';
            } else {
                $badge_label = olobuild_t( 'Offerta!' );
            }
        } else {
            $badge_label = sanitize_text_field( $s['custom_text'] ) ?: olobuild_t( 'Offerta!' );
        }

        // Shape
        $shape_map = [
            'circle'    => '50%',
            'pill'      => '999px',
            'rectangle' => '4px',
        ];
        $shape = isset( $s['badge_shape'] ) ? $s['badge_shape'] : 'pill';
        $border_radius = isset( $shape_map[ $shape ] ) ? $shape_map[ $shape ] : '999px';

        // Posizione: l'angolo della cella in cui sta il badge. Prima la tendina non agiva: le
        // coordinate assolute erano spente da .olo-sb-inline e il badge restava in alto a sinistra.
        // Ora la cella (.olo-frontend-tile) lo allinea: destra/sinistra sempre, alto/basso quando
        // la cella è più alta del badge (altezza minima del Contenitore). Con uno sfondo immagine,
        // video, galleria o sovrapposizione nel Contenitore il renderer mette la tile in un
        // .uk-position-relative interno: il secondo selettore lo prende, e quel contenitore
        // diventa la voce flex che si stringe sul badge. [ verticale, orizzontale ]
        $pos_map = [
            'top-left'     => [ 'flex-start', 'flex-start' ],
            'top-right'    => [ 'flex-start', 'flex-end' ],
            'bottom-left'  => [ 'flex-end', 'flex-start' ],
            'bottom-right' => [ 'flex-end', 'flex-end' ],
        ];
        $position = in_array( $s['position'], array_keys( $pos_map ), true ) ? $s['position'] : 'top-left';
        $pos_v    = $pos_map[ $position ][0];
        $pos_h    = $pos_map[ $position ][1];

        // Circle shape needs equal width/height
        $is_circle = ( $shape === 'circle' );

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist colors, absint() clamped font size, in_array() whitelists, hardcoded shape/position maps and the internally generated $uid.
        ?>
        <style>
            .<?php echo $uid; ?> {
                position: relative;
                display: inline-block;
            }
            .olo-frontend-tile:has(> .<?php echo $uid; ?>),
            .olo-frontend-tile:has(> .uk-position-relative > .<?php echo $uid; ?>) {
                display: flex;
                flex-direction: column;
                justify-content: <?php echo $pos_v; ?>;
                align-items: <?php echo $pos_h; ?>;
            }
            .olo-frontend-tile.olo-tile-inline:has(> .<?php echo $uid; ?>),
            .olo-frontend-tile.olo-tile-inline:has(> .uk-position-relative > .<?php echo $uid; ?>) {
                display: inline-flex;
            }
            .<?php echo $uid; ?> .olo-sb-badge {
                background: <?php echo $badge_bg; ?>;
                color: <?php echo $badge_color; ?>;
                font-size: <?php echo (int) $font_size; ?>px;
                font-weight: <?php echo $font_weight; ?>;
                border-radius: <?php echo $border_radius; ?>;
                z-index: 5;
                line-height: 1;
                text-align: center;
                white-space: nowrap;
                <?php if ( $is_circle ) : ?>
                /* Il cerchio si allarga col testo (che va a capo dentro), sempre tondo: a misura
                   fissa un'etichetta lunga come «Ultimi pezzi» usciva dal bollo. */
                min-width: <?php echo (int) ( $font_size * 3 ); ?>px;
                max-width: <?php echo (int) ( $font_size * 6 ); ?>px;
                aspect-ratio: 1;
                padding: <?php echo (int) round( $font_size * 0.5 ); ?>px;
                box-sizing: border-box;
                white-space: normal;
                line-height: 1.05;
                display: flex;
                align-items: center;
                justify-content: center;
                <?php else : ?>
                padding: <?php echo (int) round( $font_size * 0.4 ); ?>px <?php echo (int) round( $font_size * 0.8 ); ?>px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                <?php endif; ?>
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="<?php echo esc_attr( $uid ); ?> olo-sb-inline">
            <span class="olo-sb-badge"><?php echo esc_html( $badge_label ); ?></span>
        </div>
        <?php
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by build_border_hover_css()/build_border_effect_css() base-class helpers
        }
        return ob_get_clean();
    }
}
