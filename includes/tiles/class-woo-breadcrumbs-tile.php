<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Woo_Breadcrumbs_Tile extends Olo_Tile_Base {

    protected $type     = 'woo_breadcrumbs';
    protected $name     = 'Breadcrumbs WooCommerce';
    protected $icon     = 'dashicons-admin-links';
    protected $category = 'woocommerce';
    protected $defaults = [
        'separator'  => '/',
        'text_color' => '',
        'link_color' => '',
        'font_size'  => 14,
        'alignment'  => 'left',
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

        $uid = 'olo-woo-bc-' . wp_rand( 10000, 99999 );

        // Colors — TOKEN-FIRST: testo neutro soft, link col token link del tema.
        $text_color = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text-soft, #6b7280)';
        $link_color = $this->safe_color_css( $s['link_color'] ) ?: 'var(--olo-color-link, #e1474f)';

        // Font
        $font_size = max( 10, min( 24, absint( $s['font_size'] ) ) );
        $alignment = in_array( $s['alignment'], [ 'left', 'center', 'right' ], true ) ? $s['alignment'] : 'left';

        // Separator
        $sep_map = [
            '/'  => ' / ',
            '>'  => ' &gt; ',
            '-'  => ' - ',
            '>>' => ' &raquo; ',
        ];
        $separator = isset( $sep_map[ $s['separator'] ] ) ? $sep_map[ $s['separator'] ] : ' / ';

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                text-align: <?php echo $alignment; ?>;
                font-size: <?php echo $font_size; ?>px;
                padding: 8px 0;
            }
            .<?php echo $uid; ?> .woocommerce-breadcrumb {
                color: <?php echo $text_color; ?>;
                font-size: <?php echo $font_size; ?>px;
                margin: 0;
                padding: 0;
            }
            .<?php echo $uid; ?> .woocommerce-breadcrumb a {
                color: <?php echo $link_color; ?>;
                text-decoration: none;
                transition: opacity 0.2s ease;
            }
            .<?php echo $uid; ?> .woocommerce-breadcrumb a:hover {
                opacity: 0.7;
            }
        </style>
        <div class="<?php echo esc_attr( $uid ); ?>">
            <?php
            woocommerce_breadcrumb( [
                'delimiter'   => '<span class="olo-bc-sep">' . $separator . '</span>',
                'wrap_before' => '<nav class="woocommerce-breadcrumb" aria-label="' . esc_attr( olo_t( 'Breadcrumb' ) ) . '">',
                'wrap_after'  => '</nav>',
                'before'      => '<span>',
                'after'       => '</span>',
                'home'        => olo_t( 'Home' ),
            ] );
            ?>
        </div>
        <?php
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
}
