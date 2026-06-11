<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Woo_Notices_Tile extends Olo_Tile_Base {

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
                 . esc_html( olo_t( 'WooCommerce non attivo. Installa e attiva WooCommerce per utilizzare questo elemento.' ) )
                 . '</div>';
        }

        $s = wp_parse_args( $settings, $this->defaults );

        $uid = 'olo-woo-ntc-' . wp_rand( 10000, 99999 );

        // Styles
        $radius    = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );
        $font_size = max( 10, min( 24, absint( $s['font_size'] ) ) );

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: the internally generated $uid, Olo_Tile_Utils radius helpers (absint-based) and the absint()/min()/max() clamped $font_size.
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
            <?php if ( $radius_hover_css !== '' ) : ?>.<?php echo $uid; ?> .woocommerce-info{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?> .woocommerce-info:hover{border-radius:<?php echo $radius_hover_css; ?> !important}<?php endif; ?>
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

            // If no notices, check for stored notices
            $all_notices = WC()->session ? WC()->session->get( 'wc_notices', [] ) : [];
            $has_notices = false;
            foreach ( $all_notices as $type => $notices ) {
                if ( ! empty( $notices ) ) {
                    $has_notices = true;
                    break;
                }
            }

            // If truly no notices, output hidden placeholder so tile is visible in editor
            if ( ! $has_notices ) :
            ?>
            <div class="olo-woo-notices-empty" style="padding:20px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);font-size:<?php echo (int) $font_size; ?>px;border:1px dashed var(--olo-color-border, #E5E7EB);border-radius:<?php echo esc_attr( $radius ); ?>;">
                <?php echo esc_html( olo_t( 'Le notifiche WooCommerce appariranno qui quando presenti' ) ); ?>
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
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }
}
