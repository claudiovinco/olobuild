<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Viewer360_Tile extends Olobuild_Tile_Base {

    protected $type     = 'viewer360';
    protected $name     = 'Viewer 360°';
    protected $icon     = 'dashicons-admin-site-alt3';
    protected $category = 'media';
    protected $defaults = [
        'preset' => 'custom',
        'mode'              => 'hdri',
        'object_image'      => '',
        'object_frames'     => [],
        'spin_inertia'      => 0.97,
        'drag_sensitivity'  => 0.55,
        'show_angle'        => true,
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
        $mode      = $s['mode'] ?? 'hdri';
        $is_object = strpos( (string) $mode, 'object' ) === 0;
        $obj_sub   = $mode === 'object-frames' ? 'frames' : 'rotate';
        $frames    = [];
        if ( $is_object && $obj_sub === 'frames' && is_array( $s['object_frames'] ?? null ) ) {
            foreach ( $s['object_frames'] as $f ) {
                $u = is_array( $f ) ? ( $f['url'] ?? '' ) : $f;
                if ( $u ) { $frames[] = $u; }
            }
        }
        $is_vid = ! $is_object && $s['source_type'] === 'video';
        if ( $is_object ) {
            $src = $obj_sub === 'frames' ? ( $frames[0] ?? '' ) : ( $s['object_image'] ?? '' );
        } else {
            $src = $is_vid ? $s['video_url'] : $s['image_url'];
        }

        if ( empty( $src ) ) {
            return '<div style="padding:40px;text-align:center;color:var(--olo-color-text-muted,#9CA3AF);background:var(--olo-color-muted,#F3F4F6);border-radius:12px">'
                 . '<svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 8px;display:block"><circle cx="12" cy="12" r="9"/><ellipse cx="12" cy="12" rx="4" ry="9"/><path d="M3 12h18"/></svg>'
                 . '<p style="font-size:1em;margin:0">Inserisci una foto o video 360° equirectangular.</p>'
                 . '</div>';
        }

        $height = absint( $s['height'] ) ?: 400;
        $radius_css = $this->build_border_radius_css( $s['border_radius'] ?? 0 );
        $radius_css_hover_css = Olobuild_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );

        // Build config
        if ( $is_object ) {
            $config = [
                'mode'      => 'object',
                'sub'       => $obj_sub,
                'src'       => esc_url( $src ),
                'frames'    => array_map( 'esc_url', $frames ),
                'autospin'  => ! empty( $s['autorotate'] ),
                'arSpeed'   => floatval( $s['autorotate_speed'] ),
                'inertia'   => max( 0.5, min( 0.999, floatval( $s['spin_inertia'] ) ) ),
                'dragSens'  => max( 0.05, min( 3, floatval( $s['drag_sensitivity'] ) ) ),
                'drag'      => ! empty( $s['mouse_drag'] ),
                'touch'     => ! empty( $s['touch_drag'] ),
                'showAngle' => ! empty( $s['show_angle'] ),
                'caption'   => (string) ( $s['caption'] ?? '' ),
            ];
        } else {
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
        }

        ob_start();
        ?>
        <?php if ( $radius_css_hover_css !== '' ) : ?>
        <style>#<?php echo esc_attr( $uid ); ?>{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}#<?php echo esc_attr( $uid ); ?>:hover{border-radius:<?php echo $radius_css_hover_css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- integer px list from Olobuild_Tile_Utils::radius_force_css() (absint-built) ?> !important}</style>
        <?php endif; ?>
        <?php if ( $is_object ) : ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is fixed literals; $uid is internally generated ('olo-v360-' . wp_rand()). ?>
        <style>
        #<?php echo $uid; ?>.olo-v3-obj{perspective:1200px;cursor:grab;touch-action:pan-y}
        #<?php echo $uid; ?> .olo-v360-stage{position:absolute;inset:0;display:flex;align-items:center;justify-content:center}
        #<?php echo $uid; ?> .olo-v360-frame{max-width:90%;max-height:90%;object-fit:contain;will-change:transform;-webkit-user-drag:none;user-select:none}
        #<?php echo $uid; ?> .olo-v360-arrow{position:absolute;top:50%;transform:translateY(-50%);width:40px;height:40px;border-radius:50%;border:none;background:rgba(0,0,0,.5);color:#fff;font-size:22px;line-height:1;cursor:pointer;z-index:3;display:flex;align-items:center;justify-content:center}
        #<?php echo $uid; ?> .olo-v360-prev{left:10px}#<?php echo $uid; ?> .olo-v360-next{right:10px}
        #<?php echo $uid; ?> .olo-v360-arrow:focus-visible{outline:2px solid var(--olo-color-primary,#e1474f);outline-offset:2px}
        #<?php echo $uid; ?> .olo-v360-angle{position:absolute;bottom:10px;left:50%;transform:translateX(-50%);font:600 12px/1 ui-monospace,monospace;color:#fff;background:rgba(0,0,0,.5);padding:5px 11px;border-radius:99px;z-index:3;letter-spacing:.05em}
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php endif; ?>
        <div class="olo-v360 <?php echo $is_object ? 'olo-v3-obj ' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed class-name literal from the ternary ?>olo-v3-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" id="<?php echo esc_attr( $uid ); ?>"
             <?php if ( $is_object ) : ?>tabindex="0" role="img" aria-label="<?php echo esc_attr( ( $s['caption'] ?? '' ) ?: olobuild_t( 'Oggetto girevole 360°' ) ); ?>"<?php endif; ?>
             style="height:<?php echo (int) $height; ?>px;<?php if ( $radius_css ) echo 'border-radius:' . $radius_css . ';'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- integer px list from Olobuild_Tile_Base::build_border_radius_css() (intval-built) ?>overflow:hidden;position:relative;background:#111"
             data-olo-v360='<?php echo esc_attr( wp_json_encode( $config ) ); ?>'>
            <?php if ( $is_object ) : ?>
            <div class="olo-v360-stage">
                <img class="olo-v360-frame" src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( $s['caption'] ?? '' ); ?>" draggable="false" loading="lazy" />
            </div>
            <button type="button" class="olo-v360-arrow olo-v360-prev" aria-label="<?php echo esc_attr( olobuild_t( 'Ruota a sinistra' ) ); ?>">&#8249;</button>
            <button type="button" class="olo-v360-arrow olo-v360-next" aria-label="<?php echo esc_attr( olobuild_t( 'Ruota a destra' ) ); ?>">&#8250;</button>
            <?php if ( ! empty( $s['show_angle'] ) ) : ?>
            <div class="olo-v360-angle" aria-hidden="true">0&deg;</div>
            <?php endif; ?>
            <?php else : ?>
            <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px">
                <span><?php echo esc_html( olobuild_t( 'Caricamento 360°...' ) ); ?></span>
            </div>
            <?php endif; ?>
        </div>
        <?php if ( ! empty( $s['caption'] ) ) : ?>
            <?php list( $vc_cls, $vc_data ) = $this->tfx_attrs( $s, 'caption', $s['caption'] ); ?>
            <p class="olo-v360-caption<?php echo $vc_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally by Olobuild_Text_Effects (sanitize_html_class/esc_attr); caption escaped inline ?>" style="text-align:center;font-size:0.875em;color:var(--olo-color-text-muted,#9CA3AF);margin:8px 0 0"<?php echo $vc_data; ?>><?php echo esc_html( $s['caption'] ); ?></p>
        <?php endif; ?>
        <?php
        $tfx_css = $this->tfx_css( $s, '#' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Text_Effects::css() from whitelisted effects, sanitized colors and integer timings
        $this->tfx_print_script();
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }
}
