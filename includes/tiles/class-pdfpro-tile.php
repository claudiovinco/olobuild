<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_PdfPro_Tile extends Olo_Tile_Base {

    protected $type     = 'pdfpro';
    protected $name     = 'PDF Pro';
    protected $icon     = 'dashicons-media-document';
    protected $category = 'media';

    protected $defaults = [
        'pdf_url'         => '',
        'mode'            => 'flipbook',
        'viewer_height'   => '600',
        'start_page'      => '1',
        'initial_zoom'    => 'fit-width',
        'theme'           => 'light',
        'bg_color'        => '#f5f5f5',
        'show_toolbar'    => true,
        'show_page_nav'   => true,
        'show_zoom'       => true,
        'show_fullscreen' => true,
        'show_download'   => true,
        'show_print'      => true,
        'show_search'     => false,
        'show_thumbnails' => false,
        // Barra inferiore (slider pagine + opzionale zoom/fullscreen)
        'show_bottombar'            => true,
        'show_bottombar_pages'      => true,
        'show_bottombar_zoom'       => false,
        'show_bottombar_fullscreen' => false,
        'hotspots'        => [],
        'hotspot_color'   => '#EF4444',
        'hotspot_size'    => '14',
        'hotspot_pulse'   => true,
        'border_width'    => '0',
        'border_color'    => '#e5e7eb',
        'border_radius'   => [ 'tl' => 0, 'tr' => 0, 'br' => 0, 'bl' => 0 ],
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $pdf_url = esc_url( $s['pdf_url'] );
        if ( empty( $pdf_url ) ) {
            return '';
        }

        $uid    = 'olo-pdfpro-' . wp_unique_id();
        $height = max( 200, (int) $s['viewer_height'] );
        $bg     = $this->safe_color_css( $s['bg_color'] );
        $theme  = in_array( $s['theme'], [ 'light', 'dark' ], true ) ? $s['theme'] : 'light';
        $mode   = in_array( $s['mode'], [ 'flipbook', 'single', 'double', 'scroll' ], true ) ? $s['mode'] : 'flipbook';

        // Border
        $bw      = absint( $s['border_width'] );
        $bc      = $this->safe_color_css( $s['border_color'] );
        $rad_raw = $s['border_radius'];
        $rad_css = '0px';
        if ( is_array( $rad_raw ) ) {
            $rad_css = sprintf(
                '%dpx %dpx %dpx %dpx',
                absint( $rad_raw['tl'] ?? 0 ),
                absint( $rad_raw['tr'] ?? 0 ),
                absint( $rad_raw['br'] ?? 0 ),
                absint( $rad_raw['bl'] ?? 0 )
            );
        }

        // JSON config for data attribute
        $config = [
            'url'       => $pdf_url,
            'mode'      => $mode,
            'startPage' => max( 1, (int) $s['start_page'] ),
            'zoom'      => sanitize_text_field( $s['initial_zoom'] ),
            'theme'     => $theme,
            'toolbar'   => [
                'enabled'    => (bool) $s['show_toolbar'],
                'pageNav'    => (bool) $s['show_page_nav'],
                'zoom'       => (bool) $s['show_zoom'],
                'fullscreen' => (bool) $s['show_fullscreen'],
                'download'   => (bool) $s['show_download'],
                'print'      => (bool) $s['show_print'],
                'search'     => (bool) $s['show_search'],
                'thumbnails' => (bool) $s['show_thumbnails'],
            ],
            'bottombar' => [
                'enabled'    => (bool) $s['show_bottombar'],
                'pages'      => (bool) $s['show_bottombar_pages'],
                'zoom'       => (bool) $s['show_bottombar_zoom'],
                'fullscreen' => (bool) $s['show_bottombar_fullscreen'],
            ],
            'hotspots'     => $this->sanitize_hotspots( $s['hotspots'] ),
            'hotspotColor' => $this->safe_color_css( $s['hotspot_color'] ),
            'hotspotSize'  => max( 8, min( 30, absint( $s['hotspot_size'] ) ) ),
            'hotspotPulse' => (bool) $s['hotspot_pulse'],
        ];

        $style_parts = [
            'height:'        . $height . 'px',
            'background:'    . $bg,
            'border:'        . $bw . 'px solid ' . $bc,
            'border-radius:' . $rad_css,
            'overflow:hidden',
            'position:relative',
        ];

        ob_start();
        ?>
        <div class="<?php echo esc_attr( $uid ); ?>"
             data-olo-pdfpro='<?php echo wp_json_encode( $config ); ?>'
             style="<?php echo esc_attr( implode( ';', $style_parts ) ); ?>">
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Sanitize hotspots array.
     */
    private function sanitize_hotspots( $hotspots ) {
        if ( ! is_array( $hotspots ) ) {
            return [];
        }

        $clean = [];
        foreach ( $hotspots as $hs ) {
            if ( ! is_array( $hs ) ) {
                continue;
            }
            $clean[] = [
                'page'       => max( 1, absint( $hs['page'] ?? 1 ) ),
                'x'          => max( 0, min( 100, floatval( $hs['x'] ?? 50 ) ) ),
                'y'          => max( 0, min( 100, floatval( $hs['y'] ?? 50 ) ) ),
                'title'      => sanitize_text_field( $hs['title'] ?? '' ),
                'color'      => $this->safe_color_css( $hs['color'] ?? '' ),
                'icon'       => sanitize_text_field( $hs['icon'] ?? '' ),
                'description'=> wp_kses_post( $hs['description'] ?? '' ),
                'image_url'  => esc_url( $hs['image_url'] ?? '' ),
                'video_url'  => esc_url( $hs['video_url'] ?? '' ),
                'btn_label'        => sanitize_text_field( $hs['btn_label'] ?? '' ),
                'btn_url'          => esc_url( $hs['btn_url'] ?? '' ),
                'btn_target'       => (bool) ( $hs['btn_target'] ?? false ),
                'btn_font_size'    => absint( $hs['btn_font_size'] ?? 0 ),
                'btn_font_weight'  => sanitize_text_field( $hs['btn_font_weight'] ?? '' ),
                'btn_letter_spacing'=> floatval( $hs['btn_letter_spacing'] ?? 0 ),
                'btn_text_transform'=> sanitize_text_field( $hs['btn_text_transform'] ?? '' ),
                'btn_bg'           => $this->safe_color_css( $hs['btn_bg'] ?? '' ),
                'btn_color'        => $this->safe_color_css( $hs['btn_color'] ?? '' ),
                'btn_padding_v'    => absint( $hs['btn_padding_v'] ?? 0 ),
                'btn_padding_h'    => absint( $hs['btn_padding_h'] ?? 0 ),
                'btn_radius'       => Olo_Tile_Utils::border_radius( $hs['btn_radius'] ?? 0 ),
                'btn_border_width' => absint( $hs['btn_border_width'] ?? 0 ),
                'btn_border_color' => $this->safe_color_css( $hs['btn_border_color'] ?? '' ),
                'btn_border_style' => sanitize_text_field( $hs['btn_border_style'] ?? 'solid' ),
                'btn_align'        => sanitize_text_field( $hs['btn_align'] ?? '' ),
            ];
        }
        return $clean;
    }
}
