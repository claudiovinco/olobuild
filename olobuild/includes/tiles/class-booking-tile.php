<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Booking_Tile extends Olo_Tile_Base {

    protected $type     = 'booking';
    protected $name     = 'Booking Struttura';
    protected $icon     = 'dashicons-clipboard';
    protected $category = 'booking';
    protected $defaults = [
        'service_id'           => 'auto',
        'primary_color'        => '',
        'show_price'           => true,
        'show_duration'        => true,
        'widget_max_width'     => 480,
        'widget_bg'            => '#FFFFFF',
        'widget_border_radius' => 12,
        'widget_border_color'  => '',
        'widget_shadow'        => 'sm',
        'btn_bg'               => '',
        'btn_color'            => '',
        'btn_radius'           => 8,
        'available_color'      => '',
        'full_color'           => '',
        'slot_border_radius'   => 8,
        'title_size'           => 18,
        'title_weight'         => '700',
        'title_color'          => '',
        'meta_color'           => '',
        'success_color'        => '',
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        // Check if Olo Booking plugin is active
        if ( ! class_exists( 'Olo_Booking_Frontend' ) ) {
            return '<div style="padding:32px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);background:var(--olo-color-muted, #F3F4F6);border-radius:8px">'
                 . '<p style="font-size:1.1em;margin:0">Installa e attiva il plugin <strong>Olo Booking</strong> per visualizzare il widget prenotazioni.</p>'
                 . '</div>';
        }

        // Resolve service_id
        $service_id = $s['service_id'];
        if ( $service_id === 'auto' ) {
            global $post;
            if ( $post && $post->post_type === 'olo_service' ) {
                $service_id = $post->ID;
            } else {
                $service_id = '';
            }
        } elseif ( $service_id === 'all' ) {
            $service_id = '';
        }

        $uid = 'olo-bk-t-' . wp_rand( 10000, 99999 );

        // Build config for frontend
        $config = [
            'serviceId' => $service_id ? absint( $service_id ) : '',
        ];

        // Build scoped CSS
        $css = $this->build_scoped_css( $uid, $s );

        $frontend = new Olo_Booking_Frontend();
        $booking_html = $frontend->render_booking( $config );

        ob_start();
        if ( $css ) {
            echo '<style>' . $css . '</style>';
        }
        echo '<div class="' . esc_attr( $uid ) . '">';
        echo $booking_html;
        echo '</div>';

        return ob_get_clean();
    }

    private function build_scoped_css( $uid, $s ) {
        $css = '';
        $u   = ".{$uid}";

        // Widget container
        $max_w  = absint( $s['widget_max_width'] ) ?: 480;
        $bg     = $this->safe_color_css( $s['widget_bg'] );
        $radius = Olo_Tile_Utils::border_radius( $s['widget_border_radius'] ?? 0 );
        $border = $this->safe_color_css( $s['widget_border_color'] );
        $shadow = Olo_Tile_Utils::shadow( $s['widget_shadow'] ?? 'none' );

        $css .= "{$u} .olob-widget{max-width:{$max_w}px";
        if ( $bg )     $css .= ";background:{$bg}";
        if ( $radius && $radius !== '0px' ) $css .= ";border-radius:{$radius}";
        if ( $border ) $css .= ";border-color:{$border}";
        if ( $shadow !== 'none' ) $css .= ";box-shadow:{$shadow}";
        $css .= "}";

        // Primary color override
        $primary = $this->safe_color_css( $s['primary_color'] );
        if ( $primary ) {
            $css .= "{$u} .olob-widget{--olob-primary:{$primary}}";
        }

        // Button
        $btn_bg    = $this->safe_color_css( $s['btn_bg'] );
        $btn_color = $this->safe_color_css( $s['btn_color'] );
        $btn_r     = Olo_Tile_Utils::border_radius( $s['btn_radius'] ?? 0 );
        if ( $btn_bg )    $css .= "{$u} .olob-btn{background:{$btn_bg}}";
        if ( $btn_color ) $css .= "{$u} .olob-btn{color:{$btn_color}}";
        if ( $btn_r && $btn_r !== '0px' ) $css .= "{$u} .olob-btn{border-radius:{$btn_r}}";

        // Available/full colors
        $avail = $this->safe_color_css( $s['available_color'] );
        $full  = $this->safe_color_css( $s['full_color'] );
        if ( $avail ) $css .= "{$u} .olob-day--available{background:{$avail}1a;color:{$avail}}";
        if ( $full )  $css .= "{$u} .olob-day--full{background:{$full}1a;color:{$full}}";

        // Slot radius
        $slot_r = Olo_Tile_Utils::border_radius( $s['slot_border_radius'] ?? 0 );
        if ( $slot_r && $slot_r !== '0px' ) $css .= "{$u} .olob-slot{border-radius:{$slot_r}}";

        // Title
        $t_size   = absint( $s['title_size'] );
        $t_weight = esc_attr( $s['title_weight'] );
        $t_color  = $this->safe_color_css( $s['title_color'] );
        if ( $t_size )   $css .= "{$u} .olob-service-name{font-size:{$t_size}px}";
        if ( $t_weight ) $css .= "{$u} .olob-service-name{font-weight:{$t_weight}}";
        if ( $t_color )  $css .= "{$u} .olob-service-name{color:{$t_color}}";

        // Meta
        $meta_color = $this->safe_color_css( $s['meta_color'] );
        if ( $meta_color ) $css .= "{$u} .olob-service-meta{color:{$meta_color}}";

        // Success
        $success = $this->safe_color_css( $s['success_color'] );
        if ( $success ) $css .= "{$u} .olob-success{color:{$success}}";

        return $css;
    }
}
