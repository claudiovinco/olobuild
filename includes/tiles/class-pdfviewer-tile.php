<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_PdfViewer_Tile extends Olo_Tile_Base {

    protected $type     = 'pdfviewer';
    protected $name     = 'PDF Viewer';
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
        'show_bottombar'            => true,
        'show_bottombar_pages'      => true,
        'show_bottombar_zoom'       => false,
        'show_bottombar_fullscreen' => false,
        'border_width'    => '0',
        'border_color'    => '',
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

        $uid    = 'olo-pdfv-' . wp_unique_id();
        $height = max( 200, (int) $s['viewer_height'] );
        $bg     = $this->safe_color_css( $s['bg_color'] );
        $theme  = in_array( $s['theme'], [ 'light', 'dark' ], true ) ? $s['theme'] : 'light';
        $mode   = in_array( $s['mode'], [ 'flipbook', 'single', 'double', 'scroll' ], true ) ? $s['mode'] : 'flipbook';

        // Border
        $bw      = absint( $s['border_width'] );
        $bc      = $this->safe_color_css( $s['border_color'] ) ?: 'var(--olo-color-border, #E5E7EB)';
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
             data-olo-pdfviewer='<?php echo wp_json_encode( $config ); ?>'
             style="<?php echo esc_attr( implode( ';', $style_parts ) ); ?>">
        </div>
        <?php
        return ob_get_clean();
    }
}
