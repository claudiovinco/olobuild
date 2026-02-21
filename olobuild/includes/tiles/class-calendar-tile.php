<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Calendar_Tile extends Olo_Tile_Base {

    protected $type     = 'calendar';
    protected $name     = 'Calendario Eventi';
    protected $icon     = 'dashicons-calendar-alt';
    protected $category = 'booking';
    protected $defaults = [
        'default_view'       => 'dayGridMonth',
        'calendar_height'    => 'auto',
        'show_toolbar'       => true,
        'locale'             => 'it',

        'show_today_btn'     => true,
        'show_month_view'    => true,
        'show_week_view'     => true,
        'show_day_view'      => true,
        'show_list_view'     => true,

        'show_category_filter' => true,
        'filter_style'       => 'pills',
        'filter_position'    => 'above',
        'filter_gap'         => '8',
        'filter_margin_bottom' => '16',

        'toolbar_bg'              => '',
        'toolbar_border_color'    => '',
        'toolbar_border_width'    => '0',
        'toolbar_padding_x'       => '4',
        'toolbar_padding_y'       => '10',
        'toolbar_margin_bottom'   => '0',
        'toolbar_radius'          => '0',

        'toolbar_title_size'      => '20',
        'toolbar_title_weight'    => '600',
        'toolbar_title_color'     => '',
        'toolbar_title_transform' => 'capitalize',

        'toolbar_btn_bg'             => '',
        'toolbar_btn_color'          => '',
        'toolbar_btn_hover_bg'       => '',
        'toolbar_btn_active_bg'      => '',
        'toolbar_btn_active_color'   => '',
        'toolbar_btn_radius'         => '8',
        'toolbar_btn_padding_x'      => '14',
        'toolbar_btn_padding_y'      => '6',
        'toolbar_btn_font_size'      => '13',
        'toolbar_btn_font_weight'    => '500',
        'toolbar_btn_border_width'   => '0',
        'toolbar_btn_border_color'   => '',
        'toolbar_btn_text_transform' => 'capitalize',
        'toolbar_btn_shadow'         => false,

        'header_bg'          => '',
        'header_color'       => '',
        'today_bg'           => '',
        'day_number_size'    => '13',
        'border_color'       => '',

        'event_radius'       => '4',
        'event_font_size'    => '12',

        'modal_show_image'   => true,
        'modal_show_excerpt' => true,
        'modal_show_link'    => true,
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        if ( ! class_exists( 'Olo_Calendar_Frontend' ) ) {
            return '<div style="padding:32px;text-align:center;color:#9ca3af;background:#f9fafb;border-radius:8px">'
                 . '<p style="font-size:1.1em;margin:0">Installa e attiva il plugin <strong>Olo Calendar</strong> per visualizzare il calendario.</p>'
                 . '</div>';
        }

        $uid = 'olo-cal-t-' . wp_rand( 10000, 99999 );

        // Build config for frontend JS
        $config = [
            'defaultView'        => sanitize_text_field( $s['default_view'] ),
            'height'             => $s['calendar_height'] === 'auto' ? 'auto' : absint( $s['calendar_height'] ),
            'showToolbar'        => (bool) $s['show_toolbar'],
            'categories'         => sanitize_text_field( $s['filter_categories'] ?? '' ),
            'locale'             => sanitize_text_field( $s['locale'] ),
            'restUrl'            => esc_url_raw( rest_url( 'olo-calendar/v1/events' ) ),
            'showCategoryFilter' => (bool) $s['show_category_filter'],
            'filterStyle'        => sanitize_text_field( $s['filter_style'] ),
            'filterPosition'     => sanitize_text_field( $s['filter_position'] ),
            'showToday'          => (bool) $s['show_today_btn'],
            'showMonth'          => (bool) $s['show_month_view'],
            'showWeek'           => (bool) $s['show_week_view'],
            'showDay'            => (bool) $s['show_day_view'],
            'showList'           => (bool) $s['show_list_view'],
            'modalImage'         => (bool) $s['modal_show_image'],
            'modalExcerpt'       => (bool) $s['modal_show_excerpt'],
            'modalLink'          => (bool) $s['modal_show_link'],
        ];

        $css = $this->build_scoped_css( $uid, $s );

        $frontend = new Olo_Calendar_Frontend();
        $calendar_html = $frontend->render_calendar( $config );

        ob_start();
        if ( $css ) {
            echo '<style>' . $css . '</style>';
        }
        echo '<div class="' . esc_attr( $uid ) . '">';
        echo $calendar_html;
        echo '</div>';

        return ob_get_clean();
    }

    private function build_scoped_css( $uid, $s ) {
        $css = '';
        $u = ".{$uid}";

        // ── Toolbar container ──
        $tb_bg     = $this->safe_color( $s['toolbar_bg'] );
        $tb_border = $this->safe_color( $s['toolbar_border_color'] );
        $tb_bw     = absint( $s['toolbar_border_width'] );
        $tb_px     = absint( $s['toolbar_padding_x'] );
        $tb_py     = absint( $s['toolbar_padding_y'] );
        $tb_mb     = absint( $s['toolbar_margin_bottom'] );
        $tb_radius = absint( $s['toolbar_radius'] );

        $toolbar_rules = [];
        if ( $tb_bg )     $toolbar_rules[] = "background-color:{$tb_bg}";
        if ( $tb_border && $tb_bw ) $toolbar_rules[] = "border-bottom:{$tb_bw}px solid {$tb_border}";
        $toolbar_rules[] = "padding:{$tb_py}px {$tb_px}px";
        if ( $tb_mb )     $toolbar_rules[] = "margin-bottom:{$tb_mb}px";
        if ( $tb_radius ) $toolbar_rules[] = "border-radius:{$tb_radius}px";
        $css .= "{$u} .fc .fc-toolbar.fc-header-toolbar{" . implode( ';', $toolbar_rules ) . "}";

        // ── Toolbar title ──
        $tt_size  = absint( $s['toolbar_title_size'] );
        $tt_wt    = esc_attr( $s['toolbar_title_weight'] );
        $tt_color = $this->safe_color( $s['toolbar_title_color'] );
        $tt_trans = esc_attr( $s['toolbar_title_transform'] );

        $title_rules = [];
        if ( $tt_size )  $title_rules[] = "font-size:{$tt_size}px";
        if ( $tt_wt )    $title_rules[] = "font-weight:{$tt_wt}";
        if ( $tt_color ) $title_rules[] = "color:{$tt_color}";
        if ( $tt_trans )  $title_rules[] = "text-transform:{$tt_trans}";
        if ( $title_rules ) $css .= "{$u} .fc .fc-toolbar-title{" . implode( ';', $title_rules ) . "}";

        // ── Toolbar buttons ──
        $btn_bg     = $this->safe_color( $s['toolbar_btn_bg'] );
        $btn_color  = $this->safe_color( $s['toolbar_btn_color'] );
        $btn_hbg    = $this->safe_color( $s['toolbar_btn_hover_bg'] );
        $btn_abg    = $this->safe_color( $s['toolbar_btn_active_bg'] );
        $btn_acolor = $this->safe_color( $s['toolbar_btn_active_color'] );
        $btn_radius = absint( $s['toolbar_btn_radius'] );
        $btn_px     = absint( $s['toolbar_btn_padding_x'] );
        $btn_py     = absint( $s['toolbar_btn_padding_y'] );
        $btn_fs     = absint( $s['toolbar_btn_font_size'] );
        $btn_fw     = esc_attr( $s['toolbar_btn_font_weight'] );
        $btn_bw     = absint( $s['toolbar_btn_border_width'] );
        $btn_bc     = $this->safe_color( $s['toolbar_btn_border_color'] );
        $btn_tt     = esc_attr( $s['toolbar_btn_text_transform'] );
        $btn_shadow = ! empty( $s['toolbar_btn_shadow'] );

        $btn_rules = [];
        if ( $btn_bg )    { $btn_rules[] = "background-color:{$btn_bg}"; $btn_rules[] = "border-color:{$btn_bg}"; }
        if ( $btn_color ) $btn_rules[] = "color:{$btn_color}";
        if ( $btn_radius ) $btn_rules[] = "border-radius:{$btn_radius}px !important";
        $btn_rules[] = "padding:{$btn_py}px {$btn_px}px";
        if ( $btn_fs ) $btn_rules[] = "font-size:{$btn_fs}px";
        if ( $btn_fw ) $btn_rules[] = "font-weight:{$btn_fw}";
        if ( $btn_tt ) $btn_rules[] = "text-transform:{$btn_tt}";
        if ( $btn_bw && $btn_bc ) { $btn_rules[] = "border:{$btn_bw}px solid {$btn_bc}"; }
        if ( $btn_shadow ) $btn_rules[] = "box-shadow:0 2px 6px rgba(0,0,0,0.12)";
        $css .= "{$u} .fc .fc-button{" . implode( ';', $btn_rules ) . "}";

        // Button group radius
        if ( $btn_radius ) {
            $css .= "{$u} .fc .fc-button-group>.fc-button{border-radius:0 !important}";
            $css .= "{$u} .fc .fc-button-group>.fc-button:first-child{border-radius:{$btn_radius}px 0 0 {$btn_radius}px !important}";
            $css .= "{$u} .fc .fc-button-group>.fc-button:last-child{border-radius:0 {$btn_radius}px {$btn_radius}px 0 !important}";
        }

        // Hover
        if ( $btn_hbg ) {
            $css .= "{$u} .fc .fc-button:hover{background-color:{$btn_hbg};border-color:{$btn_hbg}}";
        }

        // Active
        if ( $btn_abg ) {
            $css .= "{$u} .fc .fc-button-active,{$u} .fc .fc-button:active{background-color:{$btn_abg} !important;border-color:{$btn_abg} !important}";
        }
        if ( $btn_acolor ) {
            $css .= "{$u} .fc .fc-button-active,{$u} .fc .fc-button:active{color:{$btn_acolor} !important}";
        }

        // ── Category filter ──
        $f_gap = absint( $s['filter_gap'] );
        $f_mb  = absint( $s['filter_margin_bottom'] );
        if ( $f_gap )  $css .= "{$u} .olo-cal-filters{gap:{$f_gap}px}";
        if ( $f_mb )   $css .= "{$u} .olo-cal-filters{margin-bottom:{$f_mb}px}";

        // ── Calendar grid ──
        $hdr_bg    = $this->safe_color( $s['header_bg'] );
        $hdr_color = $this->safe_color( $s['header_color'] );
        if ( $hdr_bg )    $css .= "{$u} .fc .fc-col-header-cell{background-color:{$hdr_bg}}";
        if ( $hdr_color ) $css .= "{$u} .fc .fc-col-header-cell-cushion{color:{$hdr_color}}";

        $today_bg = $this->safe_color( $s['today_bg'] );
        if ( $today_bg ) $css .= "{$u} .fc .fc-day-today{background-color:{$today_bg} !important}";

        $day_size = absint( $s['day_number_size'] );
        if ( $day_size ) $css .= "{$u} .fc .fc-daygrid-day-number{font-size:{$day_size}px}";

        $border = $this->safe_color( $s['border_color'] );
        if ( $border ) $css .= "{$u} .fc{--fc-border-color:{$border}}";

        // ── Events ──
        $ev_radius = absint( $s['event_radius'] );
        $ev_size   = absint( $s['event_font_size'] );
        if ( $ev_radius ) $css .= "{$u} .fc .fc-daygrid-event{border-radius:{$ev_radius}px}";
        if ( $ev_size )   $css .= "{$u} .fc .fc-daygrid-event{font-size:{$ev_size}px}";

        return $css;
    }
}
