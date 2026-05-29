<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Woo_Minicart_Tile extends Olo_Tile_Base {

    protected $type     = 'woo_minicart';
    protected $name     = 'Mini Carrello';
    protected $icon     = 'dashicons-cart';
    protected $category = 'woocommerce';
    protected $defaults = [
        'style'          => 'icon-text',
        'icon'           => 'cart',
        'show_count'     => true,
        'show_total'     => true,
        'dropdown'       => true,
        'icon_size'      => 24,
        'text_color'     => '',
        'icon_color'     => '',
        'badge_bg'       => '',
        'badge_color'    => '',
        'dropdown_width' => 320,
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

        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-woo-mc-' . wp_rand( 10000, 99999 );

        $style      = in_array( $s['style'], [ 'icon', 'icon-text', 'text' ], true ) ? $s['style'] : 'icon-text';
        $icon_type  = in_array( $s['icon'], [ 'cart', 'bag', 'basket' ], true ) ? $s['icon'] : 'cart';
        $icon_size  = max( 16, min( 48, absint( $s['icon_size'] ) ) );
        $dd_width   = max( 240, min( 480, absint( $s['dropdown_width'] ) ) );

        // Colors
        $text_color  = $this->safe_color_css( $s['text_color'] );
        $icon_color  = $this->safe_color_css( $s['icon_color'] );
        $badge_bg    = $this->safe_color_css( $s['badge_bg'] );
        $badge_color = $this->safe_color_css( $s['badge_color'] );

        // Cart data
        $count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
        $total = WC()->cart ? WC()->cart->get_cart_total() : '';

        // SVG icons
        $icons = [
            'cart'   => '<svg width="' . $icon_size . '" height="' . $icon_size . '" viewBox="0 0 24 24" fill="none" stroke="' . $icon_color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>',
            'bag'    => '<svg width="' . $icon_size . '" height="' . $icon_size . '" viewBox="0 0 24 24" fill="none" stroke="' . $icon_color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>',
            'basket' => '<svg width="' . $icon_size . '" height="' . $icon_size . '" viewBox="0 0 24 24" fill="none" stroke="' . $icon_color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9h20l-2 11H4L2 9z"/><path d="M8 9V5a4 4 0 018 0v4"/></svg>',
        ];
        $svg = isset( $icons[ $icon_type ] ) ? $icons[ $icon_type ] : $icons['cart'];

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                position: relative;
                cursor: pointer;
            }
            .<?php echo $uid; ?> .olo-mc-icon-wrap {
                position: relative;
                display: inline-flex;
            }
            .<?php echo $uid; ?> .olo-mc-badge {
                position: absolute;
                top: -6px;
                right: -8px;
                min-width: 18px;
                height: 18px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: <?php echo $badge_bg; ?>;
                color: <?php echo $badge_color; ?>;
                font-size: 10px;
                font-weight: 700;
                border-radius: 9px;
                padding: 0 4px;
                line-height: 1;
            }
            .<?php echo $uid; ?> .olo-mc-text {
                color: <?php echo $text_color; ?>;
                font-size: 14px;
                font-weight: 600;
            }
            <?php if ( ! empty( $s['dropdown'] ) ) : ?>
            .<?php echo $uid; ?> .olo-mc-dropdown {
                display: none;
                position: absolute;
                top: 100%;
                right: 0;
                width: <?php echo $dd_width; ?>px;
                background: var(--olo-color-background, #FFFFFF);
                border-radius: 8px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.15);
                z-index: 999;
                padding: 16px;
                margin-top: 8px;
            }
            .<?php echo $uid; ?>:hover .olo-mc-dropdown {
                display: block;
            }
            <?php endif; ?>
        </style>
        <div class="<?php echo esc_attr( $uid ); ?> olo-woo-minicart-wrap">
            <?php if ( $style !== 'text' ) : ?>
            <div class="olo-mc-icon-wrap">
                <?php echo $svg; ?>
                <?php if ( ! empty( $s['show_count'] ) ) : ?>
                <span class="olo-mc-badge olo-mc-badge-count"><?php echo absint( $count ); ?></span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php if ( $style !== 'icon' ) : ?>
            <?php if ( ! empty( $s['show_total'] ) ) : ?>
            <span class="olo-mc-text olo-mc-total"><?php echo $total; ?></span>
            <?php endif; ?>
            <?php endif; ?>
            <?php if ( ! empty( $s['dropdown'] ) ) : ?>
            <div class="olo-mc-dropdown olo-mc-dropdown-content">
                <?php woocommerce_mini_cart(); ?>
            </div>
            <?php endif; ?>
        </div>
        <?php

        // AJAX fragment for cart updates
        $this->register_cart_fragments( $uid, $s );

                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}";
            echo $border_hover_css . $border_effect_css . '</style>';
        }
        return ob_get_clean();
    }

    /**
     * Register WooCommerce AJAX cart fragments for live updates.
     */
    private function register_cart_fragments( $uid, $s ) {
        static $registered = false;
        if ( $registered ) {
            return;
        }
        $registered = true;

        add_filter( 'woocommerce_add_to_cart_fragments', function( $fragments ) {
            // Update badge count
            $count = WC()->cart->get_cart_contents_count();
            $fragments['.olo-mc-badge-count'] = '<span class="olo-mc-badge olo-mc-badge-count">' . absint( $count ) . '</span>';

            // Update total
            $total = WC()->cart->get_cart_total();
            $fragments['.olo-mc-total'] = '<span class="olo-mc-text olo-mc-total">' . $total . '</span>';

            // Update dropdown
            ob_start();
            echo '<div class="olo-mc-dropdown olo-mc-dropdown-content">';
            woocommerce_mini_cart();
            echo '</div>';
            $fragments['.olo-mc-dropdown-content'] = ob_get_clean();

            return $fragments;
        } );
    }
}
