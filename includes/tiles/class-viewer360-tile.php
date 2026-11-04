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
        // Video 360° (1.4.975): versione leggera per il telefono, copertina 360° prima del play, partenza da solo
        // (muto, solo mentre si vede), ripetizione e pulsante dell'audio.
        'video_url_mobile'  => '',
        'video_poster'      => '',
        'video_autoplay'    => false,
        'video_loop'        => true,
        'video_audio'       => true,
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
        // Altezza sul telefono (1.4.977): vuoto = come sul computer.
        'height_mobile'     => '',
        'border_radius'     => 0,
        'stage_bg'          => '',
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
                 . '<p style="font-size:1em;margin:0">' . esc_html( olobuild_t( 'Inserisci una foto o video 360° equirectangular.' ) ) . '</p>'
                 . '</div>';
        }

        $height = absint( $s['height'] ) ?: 400;
        $height_mobile = absint( $s['height_mobile'] ?? 0 );
        $height_mobile = $height_mobile ? max( 160, min( 1200, $height_mobile ) ) : 0;
        // Sfondo del riquadro: le foto prodotto su fondo bianco restavano un quadrato chiaro dentro il riquadro scuro;
        // col colore del fondo delle foto il prodotto si fonde. Vuoto = il colore scuro della Palette, come prima.
        $stage_bg = $this->safe_color_css( $s['stage_bg'] ?? '' ) ?: 'var(--olo-color-dark, #111111)';
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
        if ( $is_vid ) {
            $config['srcMobile'] = esc_url( (string) ( $s['video_url_mobile'] ?? '' ) );
            $config['poster']    = esc_url( (string) ( $s['video_poster'] ?? '' ) );
            $config['autoplay']  = ! empty( $s['video_autoplay'] );
            $config['loop']      = ! empty( $s['video_loop'] );
            $config['audio']     = ! empty( $s['video_audio'] );
            $config['labels']    = [
                'start'          => olobuild_t( 'Guarda a 360°' ),
                'play'           => olobuild_t( 'Riproduci' ),
                'pause'          => olobuild_t( 'Pausa' ),
                'unmute'         => olobuild_t( 'Attiva l’audio' ),
                'mute'           => olobuild_t( 'Togli l’audio' ),
                'seek'           => olobuild_t( 'Posizione nel video' ),
                'fullscreen'     => olobuild_t( 'Schermo intero' ),
                'exitFullscreen' => olobuild_t( 'Esci dallo schermo intero' ),
                'error'          => olobuild_t( 'Il video non si riesce a caricare' ),
            ];
        }
        }

        ob_start();
        ?>
        <?php if ( $radius_css_hover_css !== '' ) : ?>
        <style>#<?php echo esc_attr( $uid ); ?>{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}#<?php echo esc_attr( $uid ); ?>:hover{border-radius:<?php echo $radius_css_hover_css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- integer px list from Olobuild_Tile_Utils::radius_force_css() (absint-built) ?> !important}</style>
        <?php endif; ?>
        <?php if ( $height_mobile ) : ?>
        <style>@media (max-width:639px){#<?php echo esc_attr( $uid ); ?>{height:<?php echo (int) $height_mobile; ?>px!important}}</style>
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
        <?php if ( $is_vid ) : ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS fisso; $uid è generato internamente ('olo-v360-' . wp_rand()). ?>
        <style>
        #<?php echo $uid; ?> .olo-v360-pano,#<?php echo $uid; ?> .olo-v360-sfera{position:absolute;inset:0}
        #<?php echo $uid; ?> .pnlm-container{background:transparent}
        #<?php echo $uid; ?> .olo-v360-sorgente{position:absolute;left:0;top:0;width:1px;height:1px;opacity:0;pointer-events:none}
        #<?php echo $uid; ?> .olo-v360-ui{position:absolute;left:12px;right:12px;bottom:12px;z-index:4;display:flex;align-items:center;gap:.5rem;padding:.3125rem .875rem .3125rem .3125rem;border-radius:999em;background:color-mix(in srgb,var(--olo-color-dark,#111111) 58%,transparent);-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);color:var(--olo-color-light,#ffffff);opacity:0;visibility:hidden;transform:translateY(6px);transition:opacity .35s,transform .35s,visibility .35s}
        #<?php echo $uid; ?>.is-partito .olo-v360-ui{opacity:1;visibility:visible;transform:none}
        #<?php echo $uid; ?> .olo-v360-btn{flex:none;width:36px;height:36px;padding:0;border:0;border-radius:50%;background:transparent;color:inherit;display:grid;place-items:center;cursor:pointer;transition:background-color .2s}
        #<?php echo $uid; ?> .olo-v360-btn svg{width:20px;height:20px}
        #<?php echo $uid; ?> .olo-v360-btn:hover{background:color-mix(in srgb,var(--olo-color-light,#ffffff) 16%,transparent)}
        #<?php echo $uid; ?> .olo-v360-btn:focus-visible,#<?php echo $uid; ?> .olo-v360-barra:focus-visible,#<?php echo $uid; ?> .olo-v360-avvia:focus-visible .olo-v360-avvia-cerchio{outline:2px solid var(--olo-color-primary,#e1474f);outline-offset:2px}
        #<?php echo $uid; ?> .olo-v360-barra{flex:1;min-width:40px;height:22px;position:relative;cursor:pointer;touch-action:none;outline:0;border-radius:.25rem}
        #<?php echo $uid; ?> .olo-v360-barra::before{content:'';position:absolute;left:0;right:0;top:50%;height:4px;margin-top:-2px;border-radius:2px;background:color-mix(in srgb,var(--olo-color-light,#ffffff) 30%,transparent)}
        #<?php echo $uid; ?> .olo-v360-fatto{position:absolute;left:0;top:50%;width:0;height:4px;margin-top:-2px;border-radius:2px;background:var(--olo-color-primary,#e1474f)}
        #<?php echo $uid; ?> .olo-v360-fatto::after{content:'';position:absolute;right:-6px;top:50%;width:12px;height:12px;margin-top:-.375rem;border-radius:50%;background:var(--olo-color-light,#ffffff)}
        #<?php echo $uid; ?> .olo-v360-tempo{flex:none;font:500 12px/1 ui-monospace,SFMono-Regular,Menlo,monospace;font-variant-numeric:tabular-nums;white-space:nowrap;opacity:.9}
        #<?php echo $uid; ?> .olo-v360-avvia{position:absolute;left:50%;top:50%;z-index:5;transform:translate(-50%,-50%);display:flex;flex-direction:column;align-items:center;gap:.75rem;padding:.5rem;border:0;background:transparent;color:var(--olo-color-light,#ffffff);font-family:inherit;font-size:15px;font-weight:600;line-height:1.2;letter-spacing:.02em;cursor:pointer;text-shadow:0 1px 10px color-mix(in srgb,var(--olo-color-dark,#111111) 80%,transparent)}
        #<?php echo $uid; ?> .olo-v360-avvia-cerchio{width:76px;height:76px;border-radius:50%;display:grid;place-items:center;background:color-mix(in srgb,var(--olo-color-dark,#111111) 45%,transparent);-webkit-backdrop-filter:blur(8px);backdrop-filter:blur(8px);border:1.5px solid color-mix(in srgb,var(--olo-color-light,#ffffff) 70%,transparent);transition:transform .25s,background-color .25s}
        #<?php echo $uid; ?> .olo-v360-avvia-cerchio svg{width:30px;height:30px;margin-left:.25rem}
        #<?php echo $uid; ?> .olo-v360-avvia:hover .olo-v360-avvia-cerchio{transform:scale(1.06);background:color-mix(in srgb,var(--olo-color-primary,#e1474f) 70%,transparent)}
        #<?php echo $uid; ?> .olo-v360-avvia-testo{padding:.35em .9em;border-radius:999em;background:color-mix(in srgb,var(--olo-color-dark,#111111) 55%,transparent);-webkit-backdrop-filter:blur(8px);backdrop-filter:blur(8px);text-shadow:none}
        #<?php echo $uid; ?>.is-partito .olo-v360-avvia{display:none}
        #<?php echo $uid; ?>.is-ambiente .olo-v360-barra,#<?php echo $uid; ?>.is-ambiente .olo-v360-tempo{display:none}
        #<?php echo $uid; ?>.is-ambiente .olo-v360-ui{left:auto;padding:.3125rem}
        <?php /* Lo zoom di Pannellum (quadratini bianchi con le icone nere della libreria) come i comandi della barra:
                 tondi, vetro scuro, segni chiari (1.4.976). */ ?>
        #<?php echo $uid; ?> .pnlm-controls-container{top:12px;left:12px}
        #<?php echo $uid; ?> .pnlm-zoom-controls{width:auto;height:auto;margin:0;border:0;border-radius:0;background:none;box-shadow:none;display:flex;flex-direction:column;gap:.375rem}
        #<?php echo $uid; ?> .pnlm-zoom-controls .pnlm-control{position:static;width:36px;height:36px;margin:0;border:0;border-radius:50%;background:color-mix(in srgb,var(--olo-color-dark,#111111) 58%,transparent) none!important;-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);color:var(--olo-color-light,#ffffff);display:grid;place-items:center;font:400 22px/1 system-ui,sans-serif;transition:background-color .2s}
        #<?php echo $uid; ?> .pnlm-zoom-controls .pnlm-control:hover{background-color:color-mix(in srgb,var(--olo-color-dark,#111111) 78%,transparent)}
        #<?php echo $uid; ?> .pnlm-zoom-in::before{content:'+'}
        #<?php echo $uid; ?> .pnlm-zoom-out::before{content:'\2212'}
        #<?php echo $uid; ?>.is-errore .olo-v360-avvia-cerchio{display:none}
        @media (prefers-reduced-motion:reduce){#<?php echo $uid; ?> .olo-v360-ui,#<?php echo $uid; ?> .olo-v360-avvia-cerchio{transition:none}}
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php endif; ?>
        <?php /* Fondo: «Sfondo del riquadro» ($stage_bg), vuoto = il colore scuro della Palette (prima #111 fisso). */ ?>
        <div class="olo-v360 <?php echo $is_object ? 'olo-v3-obj ' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed class-name literal from the ternary ?>olo-v3-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" id="<?php echo esc_attr( $uid ); ?>"
             <?php if ( $is_object ) : ?>tabindex="0" role="img" aria-label="<?php echo esc_attr( ( $s['caption'] ?? '' ) ?: olobuild_t( 'Oggetto girevole 360°' ) ); ?>"<?php endif; ?>
             style="height:<?php echo (int) $height; ?>px;<?php if ( $radius_css ) echo 'border-radius:' . $radius_css . ';'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- integer px list from Olobuild_Tile_Base::build_border_radius_css() (intval-built) ?>overflow:hidden;position:relative;background:<?php echo esc_attr( $stage_bg ); ?>"
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
            <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:var(--olo-color-light, #ffffff);font-size:14px">
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
        $border_hover_css  = $this->build_border_hover_css( "#{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( "#{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo "#{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }
}
