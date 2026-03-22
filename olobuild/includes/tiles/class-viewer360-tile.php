<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Viewer360_Tile extends Olo_Tile_Base {

    protected $type     = 'viewer360';
    protected $name     = 'Viewer 360°';
    protected $icon     = 'dashicons-admin-site-alt3';
    protected $category = 'media';
    protected $defaults = [
        'source_type'       => 'image',
        'image_url'         => '',
        'video_url'         => '',
        'autorotate'        => true,
        'autorotate_speed'  => '1',
        'mouse_drag'        => true,
        'touch_drag'        => true,
        'scroll_zoom'       => true,
        'gyroscope'         => false,
        'show_controls'     => true,
        'show_fullscreen'   => true,
        'show_zoom'         => true,
        'show_compass'      => false,
        'default_yaw'       => '0',
        'default_pitch'     => '0',
        'default_zoom'      => '50',
        'min_zoom'          => '20',
        'max_zoom'          => '80',
        'height'            => '400',
        'border_radius'     => 0,
        'caption'           => '',
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $uid    = 'olo-v360-' . wp_rand( 10000, 99999 );
        $is_vid = $s['source_type'] === 'video';
        $src    = $is_vid ? $s['video_url'] : $s['image_url'];

        if ( empty( $src ) ) {
            return '<div style="padding:40px;text-align:center;color:var(--olo-color-text-muted,#9CA3AF);background:var(--olo-color-muted,#F3F4F6);border-radius:12px">'
                 . '<p style="font-size:2em;margin:0 0 8px">🌍</p>'
                 . '<p style="font-size:1em;margin:0">Inserisci una foto o video 360° equirectangular.</p>'
                 . '</div>';
        }

        $height = absint( $s['height'] ) ?: 400;
        $radius_css = $this->build_border_radius_css( $s['border_radius'] ?? 0 );

        // Build config
        $config = [
            'src'        => esc_url( $src ),
            'type'       => $is_vid ? 'video' : 'image',
            'autorotate' => ! empty( $s['autorotate'] ),
            'arSpeed'    => floatval( $s['autorotate_speed'] ),
            'drag'       => ! empty( $s['mouse_drag'] ),
            'touch'      => ! empty( $s['touch_drag'] ),
            'zoom'       => ! empty( $s['scroll_zoom'] ),
            'gyro'       => ! empty( $s['gyroscope'] ),
            'controls'   => ! empty( $s['show_controls'] ),
            'fullscreen' => ! empty( $s['show_fullscreen'] ),
            'zoomBtns'   => ! empty( $s['show_zoom'] ),
            'compass'    => ! empty( $s['show_compass'] ),
            'yaw'        => floatval( $s['default_yaw'] ),
            'pitch'      => floatval( $s['default_pitch'] ),
            'fov'        => floatval( $s['default_zoom'] ),
            'minFov'     => floatval( $s['min_zoom'] ),
            'maxFov'     => floatval( $s['max_zoom'] ),
        ];

        ob_start();
        ?>
        <div class="olo-v360" id="<?php echo esc_attr( $uid ); ?>"
             style="height:<?php echo $height; ?>px;<?php if ( $radius_css ) echo 'border-radius:' . $radius_css . ';'; ?>overflow:hidden;position:relative;background:#111"
             data-olo-v360='<?php echo esc_attr( wp_json_encode( $config ) ); ?>'>
            <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px">
                <span>Caricamento 360°...</span>
            </div>
        </div>
        <?php if ( ! empty( $s['caption'] ) ) : ?>
            <p style="text-align:center;font-size:0.875em;color:var(--olo-color-text-muted,#9CA3AF);margin:8px 0 0"><?php echo esc_html( $s['caption'] ); ?></p>
        <?php endif; ?>
        <?php
        return ob_get_clean();
    }
}
