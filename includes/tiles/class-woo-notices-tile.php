<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Woo_Notices_Tile extends Olobuild_Tile_Base {

    protected $type     = 'woo_notices';
    protected $name     = 'Notifiche WooCommerce';
    protected $icon     = 'dashicons-info';
    protected $category = 'woocommerce';
    protected $defaults = [
        'show_success'  => true,
        'show_error'    => true,
        'show_info'     => true,
        'border_radius' => 8,
        'font_size'     => 14,
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

        $uid = 'olo-woo-ntc-' . wp_rand( 10000, 99999 );

        // Styles
        $radius    = Olobuild_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $font_size = max( 10, min( 24, absint( $s['font_size'] ) ) );
        // Raggio in hover su TUTTE le notifiche (prima solo sulle «info»), con la sua Durata.
        $avvisi_sel  = ".{$uid} .woocommerce-message,.{$uid} .woocommerce-error,.{$uid} .woocommerce-info";
        $avvisi_hov  = ".{$uid} .woocommerce-message:hover,.{$uid} .woocommerce-error:hover,.{$uid} .woocommerce-info:hover";
        $radius_hover_rules = Olobuild_Tile_Utils::radius_hover_rules( $avvisi_sel, $s, 'border_radius_hover', '', $avvisi_hov );

        // Le notifiche in attesa vanno contate PRIMA di stamparle: wc_print_notices() le svuota, e
        // il controllo fatto dopo trovava sempre «nessuna» (il riquadro vuoto compariva accanto alle
        // notifiche vere).
        $all_notices = WC()->session ? WC()->session->get( 'wc_notices', [] ) : [];
        $has_notices = false;
        foreach ( (array) $all_notices as $notices ) {
            if ( ! empty( $notices ) ) {
                $has_notices = true;
                break;
            }
        }

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: the internally generated $uid, Olobuild_Tile_Utils radius helpers (absint-based) and the absint()/min()/max() clamped $font_size.
        ?>
        <style>
            .<?php echo $uid; ?> .woocommerce-message,
            .<?php echo $uid; ?> .woocommerce-error,
            .<?php echo $uid; ?> .woocommerce-info {
                border-radius: <?php echo $radius; ?>;
                font-size: <?php echo (int) $font_size; ?>px;
                padding: 12px 18px;
                margin: 0 0 12px 0;
                list-style: none;
            }
            <?php echo $radius_hover_rules; ?>
            <?php if ( empty( $s['show_success'] ) ) : ?>
            .<?php echo $uid; ?> .woocommerce-message { display: none; }
            <?php endif; ?>
            <?php if ( empty( $s['show_error'] ) ) : ?>
            .<?php echo $uid; ?> .woocommerce-error { display: none; }
            <?php endif; ?>
            <?php if ( empty( $s['show_info'] ) ) : ?>
            .<?php echo $uid; ?> .woocommerce-info { display: none; }
            <?php endif; ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="<?php echo esc_attr( $uid ); ?>">
            <?php
            // Output existing WooCommerce notices
            if ( function_exists( 'wc_print_notices' ) ) {
                wc_print_notices();
            }

            // Senza notifiche, il riquadro tratteggiato che spiega la tile serve solo nel builder
            // (così la tile si vede e si seleziona): prima lo vedevano anche i visitatori del sito.
            if ( ! $has_notices && ! empty( $s['_builder_mode'] ) ) :
            ?>
            <div class="olo-woo-notices-empty" style="padding:20px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);font-size:<?php echo (int) $font_size; ?>px;border:1px dashed var(--olo-color-border, #E5E7EB);border-radius:<?php echo esc_attr( $radius ); ?>;">
                <?php echo esc_html( olobuild_t( 'Le notifiche WooCommerce appariranno qui quando presenti' ) ); ?>
            </div>
            <?php endif; ?>
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
