<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_BookingPicker_Tile extends Olo_Tile_Base {

    protected $type     = 'bookingpicker';
    protected $name     = 'Booking Servizio';
    protected $icon     = 'dashicons-list-view';
    protected $category = 'booking';
    protected $defaults = [
        'service_id'           => '',
        'primary_color'        => '#6366F1',
        'show_price'           => true,
        'show_duration'        => true,
        'widget_max_width'     => 480,
        'widget_bg'            => '#FFFFFF',
        'widget_border_radius' => 12,
        'widget_border_color'  => '#E5E7EB',
        'widget_shadow'        => 'sm',
        'btn_bg'               => '#6366F1',
        'btn_color'            => '#FFFFFF',
        'btn_radius'           => 8,
        'available_color'      => '#6366F1',
        'full_color'           => '#EF4444',
        'slot_border_radius'   => 8,
        'title_size'           => 18,
        'title_weight'         => '700',
        'title_color'          => '',
        'meta_color'           => '#6B7280',
        'success_color'        => '#10B981',
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        if ( ! class_exists( 'Olo_Booking_Frontend' ) ) {
            return '<div style="padding:32px;text-align:center;color:#9ca3af;background:#f9fafb;border-radius:8px">'
                 . '<p style="font-size:1.1em;margin:0">Installa e attiva il plugin <strong>Olo Booking</strong> per visualizzare il widget prenotazioni.</p>'
                 . '</div>';
        }

        $service_id = $s['service_id'];

        // No service selected → show all
        if ( empty( $service_id ) ) {
            $service_id = '';
        }

        $uid = 'olo-bkp-' . wp_rand( 10000, 99999 );

        $config = [
            'serviceId' => $service_id ? absint( $service_id ) : '',
        ];

        $css = $this->build_scoped_css( $uid, $s );

        $frontend     = new Olo_Booking_Frontend();
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

        $max_w  = absint( $s['widget_max_width'] ) ?: 480;
        $bg     = $this->safe_color( $s['widget_bg'] );
        $radius = absint( $s['widget_border_radius'] );
        $border = $this->safe_color( $s['widget_border_color'] );
        $shadow = Olo_Tile_Utils::shadow( $s['widget_shadow'] ?? 'none' );

        $css .= "{$u} .olob-widget{max-width:{$max_w}px";
        if ( $bg )     $css .= ";background:{$bg}";
        if ( $radius ) $css .= ";border-radius:{$radius}px";
        if ( $border ) $css .= ";border-color:{$border}";
        if ( $shadow !== 'none' ) $css .= ";box-shadow:{$shadow}";
        $css .= "}";

        $primary = $this->safe_color( $s['primary_color'] );
        if ( $primary ) {
            $css .= "{$u} .olob-widget{--olob-primary:{$primary}}";
        }

        $btn_bg    = $this->safe_color( $s['btn_bg'] );
        $btn_color = $this->safe_color( $s['btn_color'] );
        $btn_r     = absint( $s['btn_radius'] );
        if ( $btn_bg )    $css .= "{$u} .olob-btn{background:{$btn_bg}}";
        if ( $btn_color ) $css .= "{$u} .olob-btn{color:{$btn_color}}";
        if ( $btn_r )     $css .= "{$u} .olob-btn{border-radius:{$btn_r}px}";

        $avail = $this->safe_color( $s['available_color'] );
        $full  = $this->safe_color( $s['full_color'] );
        if ( $avail ) $css .= "{$u} .olob-day--available{background:{$avail}1a;color:{$avail}}";
        if ( $full )  $css .= "{$u} .olob-day--full{background:{$full}1a;color:{$full}}";

        $slot_r = absint( $s['slot_border_radius'] );
        if ( $slot_r ) $css .= "{$u} .olob-slot{border-radius:{$slot_r}px}";

        $t_size   = absint( $s['title_size'] );
        $t_weight = esc_attr( $s['title_weight'] );
        $t_color  = $this->safe_color( $s['title_color'] );
        if ( $t_size )   $css .= "{$u} .olob-service-name{font-size:{$t_size}px}";
        if ( $t_weight ) $css .= "{$u} .olob-service-name{font-weight:{$t_weight}}";
        if ( $t_color )  $css .= "{$u} .olob-service-name{color:{$t_color}}";

        $meta_color = $this->safe_color( $s['meta_color'] );
        if ( $meta_color ) $css .= "{$u} .olob-service-meta{color:{$meta_color}}";

        $success = $this->safe_color( $s['success_color'] );
        if ( $success ) $css .= "{$u} .olob-success{color:{$success}}";

        return $css;
    }
}
