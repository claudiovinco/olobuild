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
        'preset' => 'custom',
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
        $s = wp_parse_args( $settings, $this->defaults );

        $uid    = 'olo-v360-' . wp_rand( 10000, 99999 );
        $is_vid = $s['source_type'] === 'video';
        $src    = $is_vid ? $s['video_url'] : $s['image_url'];

        if ( empty( $src ) ) {
            return '<div style="padding:40px;text-align:center;color:var(--olo-color-text-muted,#9CA3AF);background:var(--olo-color-muted,#F3F4F6);border-radius:12px">'
                 . '<svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 8px;display:block"><circle cx="12" cy="12" r="9"/><ellipse cx="12" cy="12" rx="4" ry="9"/><path d="M3 12h18"/></svg>'
                 . '<p style="font-size:1em;margin:0">Inserisci una foto o video 360° equirectangular.</p>'
                 . '</div>';
        }

        $height = absint( $s['height'] ) ?: 400;
        $radius_css = $this->build_border_radius_css( $s['border_radius'] ?? 0 );
        $radius_css_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );

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
        <?php if ( $radius_css_hover_css !== '' ) : ?>
        <style>#<?php echo esc_attr( $uid ); ?>{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}#<?php echo esc_attr( $uid ); ?>:hover{border-radius:<?php echo $radius_css_hover_css; ?> !important}</style>
        <?php endif; ?>
        <div class="olo-v360 olo-v3-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" id="<?php echo esc_attr( $uid ); ?>"
             style="height:<?php echo $height; ?>px;<?php if ( $radius_css ) echo 'border-radius:' . $radius_css . ';'; ?>overflow:hidden;position:relative;background:#111"
             data-olo-v360='<?php echo esc_attr( wp_json_encode( $config ) ); ?>'>
            <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px">
                <span><?php echo esc_html( olo_t( 'Caricamento 360°...' ) ); ?></span>
            </div>
        </div>
        <?php if ( ! empty( $s['caption'] ) ) : ?>
            <?php list( $vc_cls, $vc_data ) = $this->tfx_attrs( $s, 'caption', $s['caption'] ); ?>
            <p class="olo-v360-caption<?php echo $vc_cls; ?>" style="text-align:center;font-size:0.875em;color:var(--olo-color-text-muted,#9CA3AF);margin:8px 0 0"<?php echo $vc_data; ?>><?php echo esc_html( $s['caption'] ); ?></p>
        <?php endif; ?>
        <?php
        $tfx_css = $this->tfx_css( $s, '#' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();
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
