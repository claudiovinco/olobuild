<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Audio_Tile extends Olo_Tile_Base {

    protected $type     = 'audio';
    protected $name     = 'Audio';
    protected $icon     = 'dashicons-format-audio';
    protected $category = 'media';
    protected $defaults = [
        'source_type'    => 'file',
        'file_url'       => '',
        'audio_url'      => '',
        'autoplay'       => false,
        'loop'           => false,
        'muted'          => false,
        'show_controls'  => true,
        'player_style'   => 'default',
        'accent_color'   => '',
        'bg_color'       => '',
        'text_color'     => '',
        'border_radius'  => '8',
        'title'          => '',
        'artist'         => '',
        'cover_image'             => '',
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
        return [
            [ 'key' => 'source_type',    'type' => 'select', 'label' => 'Source', 'options' => [
                'file' => 'File', 'url' => 'URL',
            ]],
            [ 'key' => 'file_url',       'type' => 'media',  'label' => 'Audio File' ],
            [ 'key' => 'audio_url',      'type' => 'text',   'label' => 'Audio URL' ],
            [ 'key' => 'autoplay',       'type' => 'toggle', 'label' => 'Autoplay' ],
            [ 'key' => 'loop',           'type' => 'toggle', 'label' => 'Loop' ],
            [ 'key' => 'muted',          'type' => 'toggle', 'label' => 'Muted' ],
            [ 'key' => 'show_controls',  'type' => 'toggle', 'label' => 'Show Controls' ],
            [ 'key' => 'player_style',   'type' => 'select', 'label' => 'Player Style', 'options' => [
                'default' => 'Default', 'minimal' => 'Minimal', 'custom' => 'Custom',
            ]],
            [ 'key' => 'accent_color',   'type' => 'color',  'label' => 'Accent Color' ],
            [ 'key' => 'bg_color',       'type' => 'color',  'label' => 'Background' ],
            [ 'key' => 'text_color',     'type' => 'color',  'label' => 'Text Color' ],
            [ 'key' => 'border_radius',  'type' => 'range',  'label' => 'Border Radius' ],
            [ 'key' => 'title',          'type' => 'text',   'label' => 'Title' ],
            [ 'key' => 'artist',         'type' => 'text',   'label' => 'Artist' ],
            [ 'key' => 'cover_image',    'type' => 'image',  'label' => 'Cover Image' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $this->_audio_uid = 'olo-audio-' . wp_unique_id();

        $src = '';
        if ( $s['source_type'] === 'url' ) {
            $src = trim( $s['audio_url'] );
        } else {
            $src = trim( $s['file_url'] );
        }

        $style = $s['player_style'] ?: 'default';

        if ( $style === 'minimal' ) {
            $html = $this->render_minimal( $s, $src );
        } elseif ( $style === 'custom' ) {
            $html = $this->render_custom( $s, $src );
        } else {
            $html = $this->render_default( $s, $src );
        }

        $tfx_css = $this->tfx_css( $s, '.olo-audio' );
        if ( $tfx_css ) {
            $html .= '<style>' . $tfx_css . '</style>';
            ob_start(); $this->tfx_print_script(); $html .= ob_get_clean();
        }
        // Border system
        $a_uid = $this->_audio_uid;
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( "#{$a_uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( "#{$a_uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            $html .= '<style>';
            if ( $border_css ) $html .= "#{$a_uid}{{$border_css}}";
            $html .= $border_hover_css . $border_effect_css . '</style>';
        }
        return $html;
    }

    // =========================================================================
    // Default: standard HTML5 <audio> with styling wrapper
    // =========================================================================

    private function render_default( $s, $src ) {
        $bg_color     = $this->safe_color_css( $s['bg_color'] ) ?: 'var(--olo-color-muted, #F3F4F6)';
        $radius       = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );
        $accent       = $this->safe_color_css( $s['accent_color'] );
        $text_color   = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text, #374151)';

        $uid = $this->_audio_uid ?? ( 'olo-audio-' . wp_unique_id() );

        ob_start();
        ?>
        <?php if ( $radius_hover_css !== '' ) : ?>
        <style>#<?php echo esc_attr( $uid ); ?>{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}#<?php echo esc_attr( $uid ); ?>:hover{border-radius:<?php echo $radius_hover_css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- integer px list from Olo_Tile_Utils::radius_force_css() (absint-built) ?> !important}</style>
        <?php endif; ?>
        <div id="<?php echo esc_attr( $uid ); ?>" class="olo-audio olo-audio-default" style="background:<?php echo $bg_color; ?>;border-radius:<?php echo $radius; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- colour from the safe_color_css() whitelist (may be a var() token, not esc_attr-safe inside style attr), radius integer px from Olo_Tile_Utils::border_radius() ?>;padding:16px;">
            <?php if ( ! empty( $s['title'] ) || ! empty( $s['artist'] ) ) : ?>
            <div style="margin-bottom:8px;<?php echo 'color:' . $text_color . ';'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- colour from the safe_color_css() whitelist (may be a var() token, not esc_attr-safe inside style attr) ?>">
                <?php if ( ! empty( $s['title'] ) ) : ?>
                    <?php list( $at_cls, $at_data ) = $this->tfx_attrs( $s, 'title', $s['title'] ); ?>
                    <div class="olo-audio-title<?php echo $at_cls; ?>" style="font-weight:600;font-size:14px;"<?php echo $at_data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally by Olo_Text_Effects (sanitize_html_class/esc_attr); title escaped inline ?>><?php echo esc_html( $s['title'] ); ?></div>
                <?php endif; ?>
                <?php if ( ! empty( $s['artist'] ) ) : ?>
                    <div style="font-size:12px;opacity:0.7;"><?php echo esc_html( $s['artist'] ); ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if ( ! empty( $src ) ) : ?>
            <audio
                <?php if ( ! empty( $s['show_controls'] ) || ! empty( $s['autoplay'] ) ) echo 'controls'; // WCAG 1.4.2: con autoplay garantisci sempre un meccanismo di stop accessibile (controlli nativi). ?>
                <?php if ( ! empty( $s['autoplay'] ) ) echo 'autoplay'; ?>
                <?php if ( ! empty( $s['loop'] ) ) echo 'loop'; ?>
                <?php if ( ! empty( $s['muted'] ) ) echo 'muted'; ?>
                preload="metadata"
                style="width:100%;display:block;"
            >
                <source src="<?php echo esc_url( $src ); ?>" type="<?php echo esc_attr( $this->get_audio_mime( $src ) ); ?>">
            </audio>
            <?php else : ?>
            <div style="color:<?php echo $text_color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- colour from the safe_color_css() whitelist (may be a var() token, not esc_attr-safe inside style attr) ?>;opacity:0.5;font-size:13px;text-align:center;padding:12px 0;">
                Seleziona un file audio
            </div>
            <?php endif; ?>
        </div>
        <?php
        if ( $accent ) :
        ?>
        <style>
            #<?php echo esc_attr( $uid ); ?> audio::-webkit-media-controls-play-button { color: <?php echo $accent; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- colour from the safe_color_css() whitelist (inline CSS, not esc_attr-safe) ?>; }
        </style>
        <?php
        endif;

        return ob_get_clean();
    }

    // =========================================================================
    // Minimal: play/pause button + progress bar
    // =========================================================================

    private function render_minimal( $s, $src ) {
        $bg_color   = $this->safe_color_css( $s['bg_color'] ) ?: 'var(--olo-color-muted, #F3F4F6)';
        $text_color = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text, #374151)';
        $accent     = $this->safe_color_css( $s['accent_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $radius     = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );

        $uid = $this->_audio_uid ?? ( 'olo-audio-' . wp_unique_id() );

        ob_start();
        ?>
        <?php if ( $radius_hover_css !== '' ) : ?>
        <style>#<?php echo esc_attr( $uid ); ?>{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}#<?php echo esc_attr( $uid ); ?>:hover{border-radius:<?php echo $radius_hover_css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- integer px list from Olo_Tile_Utils::radius_force_css() (absint-built) ?> !important}</style>
        <?php endif; ?>
        <div id="<?php echo esc_attr( $uid ); ?>" class="olo-audio olo-audio-minimal" style="background:<?php echo $bg_color; ?>;border-radius:<?php echo $radius; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- colour from the safe_color_css() whitelist (may be a var() token, not esc_attr-safe inside style attr), radius integer px from Olo_Tile_Utils::border_radius() ?>;padding:12px 16px;display:flex;align-items:center;gap:12px;">
            <button type="button" class="olo-audio-playbtn" aria-label="<?php echo esc_attr__( 'Riproduci', 'olobuilder' ); ?>" style="background:none;border:none;cursor:pointer;padding:0;flex-shrink:0;width:32px;height:32px;display:flex;align-items:center;justify-content:center;">
                <svg class="olo-audio-icon-play" aria-hidden="true" focusable="false" width="24" height="24" viewBox="0 0 24 24"><polygon points="6,4 6,20 20,12" fill="<?php echo esc_attr( $accent ); ?>"/></svg>
                <svg class="olo-audio-icon-pause" aria-hidden="true" focusable="false" width="24" height="24" viewBox="0 0 24 24" style="display:none;"><rect x="5" y="4" width="4" height="16" rx="1" fill="<?php echo esc_attr( $accent ); ?>"/><rect x="15" y="4" width="4" height="16" rx="1" fill="<?php echo esc_attr( $accent ); ?>"/></svg>
            </button>

            <div class="olo-audio-progress-wrap" tabindex="0" role="slider" aria-label="<?php echo esc_attr__( 'Avanzamento', 'olobuilder' ); ?>" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" style="flex:1;height:6px;background:var(--olo-color-border, #E5E7EB);border-radius:3px;cursor:pointer;position:relative;">
                <div class="olo-audio-progress-bar" style="height:100%;width:0%;background:<?php echo esc_attr( $accent ); ?>;border-radius:3px;transition:width 0.1s linear;"></div>
            </div>

            <span class="olo-audio-time" style="font-size:11px;color:<?php echo esc_attr( $text_color ); ?>;opacity:0.7;flex-shrink:0;min-width:32px;text-align:right;">0:00</span>

            <?php if ( ! empty( $src ) ) : ?>
            <audio preload="metadata"
                <?php if ( ! empty( $s['autoplay'] ) ) echo 'autoplay'; ?>
                <?php if ( ! empty( $s['loop'] ) ) echo 'loop'; ?>
                <?php if ( ! empty( $s['muted'] ) ) echo 'muted'; ?>
            >
                <source src="<?php echo esc_url( $src ); ?>" type="<?php echo esc_attr( $this->get_audio_mime( $src ) ); ?>">
            </audio>
            <?php endif; ?>
        </div>
        <?php if ( ! empty( $src ) ) : ?>
        <script>
        (function(){
            var wrap = document.getElementById('<?php echo esc_js( $uid ); ?>');
            if(!wrap) return;
            var audio = wrap.querySelector('audio');
            var btn = wrap.querySelector('.olo-audio-playbtn');
            var iconPlay = wrap.querySelector('.olo-audio-icon-play');
            var iconPause = wrap.querySelector('.olo-audio-icon-pause');
            var progWrap = wrap.querySelector('.olo-audio-progress-wrap');
            var progBar = wrap.querySelector('.olo-audio-progress-bar');
            var timeEl = wrap.querySelector('.olo-audio-time');

            if(!audio) return;

            function formatTime(sec){
                var m = Math.floor(sec/60);
                var s = Math.floor(sec%60);
                return m + ':' + (s < 10 ? '0' : '') + s;
            }

            btn.addEventListener('click', function(){
                if(audio.paused){
                    audio.play();
                } else {
                    audio.pause();
                }
            });

            audio.addEventListener('play', function(){
                iconPlay.style.display = 'none';
                iconPause.style.display = 'block';
                btn.setAttribute('aria-label', <?php echo wp_json_encode( __( 'Pausa', 'olobuilder' ) ); ?>);
            });
            audio.addEventListener('pause', function(){
                iconPlay.style.display = 'block';
                iconPause.style.display = 'none';
                btn.setAttribute('aria-label', <?php echo wp_json_encode( __( 'Riproduci', 'olobuilder' ) ); ?>);
            });
            audio.addEventListener('timeupdate', function(){
                if(audio.duration){
                    var pct = (audio.currentTime / audio.duration) * 100;
                    progBar.style.width = pct + '%';
                    if(progWrap){ progWrap.setAttribute('aria-valuenow', Math.round(pct)); }
                }
                timeEl.textContent = formatTime(audio.currentTime);
            });

            progWrap.addEventListener('click', function(e){
                if(!audio.duration) return;
                var rect = progWrap.getBoundingClientRect();
                var x = e.clientX - rect.left;
                var pct = x / rect.width;
                audio.currentTime = pct * audio.duration;
            });

            progWrap.addEventListener('keydown', function(e){
                if(!audio.duration) return;
                var step = audio.duration / 20;
                if(e.key === 'ArrowRight' || e.key === 'ArrowUp'){
                    audio.currentTime = Math.min(audio.duration, audio.currentTime + step);
                    e.preventDefault();
                } else if(e.key === 'ArrowLeft' || e.key === 'ArrowDown'){
                    audio.currentTime = Math.max(0, audio.currentTime - step);
                    e.preventDefault();
                } else if(e.key === 'Home'){
                    audio.currentTime = 0;
                    e.preventDefault();
                } else if(e.key === 'End'){
                    audio.currentTime = audio.duration;
                    e.preventDefault();
                }
            });
        })();
        </script>
        <?php endif; ?>
        <?php

        return ob_get_clean();
    }

    // =========================================================================
    // Custom: full player with cover, title, artist, progress, volume, time
    // =========================================================================

    private function render_custom( $s, $src ) {
        $bg_color   = $this->safe_color_css( $s['bg_color'] ) ?: 'var(--olo-color-muted, #F3F4F6)';
        $text_color = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text, #374151)';
        $accent     = $this->safe_color_css( $s['accent_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $radius     = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );

        $uid = $this->_audio_uid ?? ( 'olo-audio-' . wp_unique_id() );

        ob_start();
        ?>
        <?php if ( $radius_hover_css !== '' ) : ?>
        <style>#<?php echo esc_attr( $uid ); ?>{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}#<?php echo esc_attr( $uid ); ?>:hover{border-radius:<?php echo $radius_hover_css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- integer px list from Olo_Tile_Utils::radius_force_css() (absint-built) ?> !important}</style>
        <?php endif; ?>
        <div id="<?php echo esc_attr( $uid ); ?>" class="olo-audio olo-audio-custom" style="background:<?php echo $bg_color; ?>;border-radius:<?php echo $radius; ?>;padding:16px;color:<?php echo $text_color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- colours from the safe_color_css() whitelist (may be var() tokens, not esc_attr-safe inside style attr), radius integer px from Olo_Tile_Utils::border_radius() ?>;">
            <div style="display:flex;gap:16px;align-items:center;">
                <!-- Cover image -->
                <?php if ( ! empty( $s['cover_image'] ) ) : ?>
                <div style="flex-shrink:0;width:64px;height:64px;border-radius:8px;overflow:hidden;">
                    <img src="<?php echo esc_url( $s['cover_image'] ); ?>" alt="" style="width:100%;height:100%;object-fit:cover;display:block;" loading="lazy" />
                </div>
                <?php endif; ?>

                <div style="flex:1;min-width:0;">
                    <!-- Title & Artist -->
                    <?php if ( ! empty( $s['title'] ) ) : ?>
                    <?php list( $ac_cls, $ac_data ) = $this->tfx_attrs( $s, 'title', $s['title'] ); ?>
                    <div class="olo-audio-title<?php echo $ac_cls; ?>" style="font-weight:600;font-size:14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"<?php echo $ac_data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally by Olo_Text_Effects (sanitize_html_class/esc_attr); title escaped inline ?>><?php echo esc_html( $s['title'] ); ?></div>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['artist'] ) ) : ?>
                    <div style="font-size:12px;opacity:0.7;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:8px;"><?php echo esc_html( $s['artist'] ); ?></div>
                    <?php endif; ?>

                    <!-- Controls row -->
                    <div style="display:flex;align-items:center;gap:10px;">
                        <button type="button" class="olo-audio-playbtn" aria-label="<?php echo esc_attr__( 'Riproduci', 'olobuilder' ); ?>" style="background:<?php echo esc_attr( $accent ); ?>;border:none;cursor:pointer;padding:0;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg class="olo-audio-icon-play" aria-hidden="true" focusable="false" width="16" height="16" viewBox="0 0 24 24"><polygon points="8,5 8,19 19,12" fill="#fff"/></svg>
                            <svg class="olo-audio-icon-pause" aria-hidden="true" focusable="false" width="16" height="16" viewBox="0 0 24 24" style="display:none;"><rect x="6" y="5" width="4" height="14" rx="1" fill="#fff"/><rect x="14" y="5" width="4" height="14" rx="1" fill="#fff"/></svg>
                        </button>

                        <span class="olo-audio-time-current" style="font-size:11px;opacity:0.7;min-width:32px;">0:00</span>

                        <div class="olo-audio-progress-wrap" tabindex="0" role="slider" aria-label="<?php echo esc_attr__( 'Avanzamento', 'olobuilder' ); ?>" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" style="flex:1;height:6px;background:var(--olo-color-border, #E5E7EB);border-radius:3px;cursor:pointer;position:relative;">
                            <div class="olo-audio-progress-bar" style="height:100%;width:0%;background:<?php echo esc_attr( $accent ); ?>;border-radius:3px;transition:width 0.1s linear;"></div>
                        </div>

                        <span class="olo-audio-time-total" style="font-size:11px;opacity:0.7;min-width:32px;">0:00</span>

                        <!-- Volume -->
                        <div style="display:flex;align-items:center;gap:4px;flex-shrink:0;">
                            <svg aria-hidden="true" focusable="false" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M11 5L6 9H2v6h4l5 4V5z" fill="<?php echo esc_attr( $text_color ); ?>" opacity="0.7"/>
                            </svg>
                            <input type="range" class="olo-audio-volume" aria-label="<?php echo esc_attr__( 'Volume', 'olobuilder' ); ?>" min="0" max="1" step="0.05" value="1" style="width:60px;accent-color:<?php echo esc_attr( $accent ); ?>;" />
                        </div>
                    </div>
                </div>
            </div>

            <?php if ( ! empty( $src ) ) : ?>
            <audio preload="metadata"
                <?php if ( ! empty( $s['autoplay'] ) ) echo 'autoplay'; ?>
                <?php if ( ! empty( $s['loop'] ) ) echo 'loop'; ?>
                <?php if ( ! empty( $s['muted'] ) ) echo 'muted'; ?>
            >
                <source src="<?php echo esc_url( $src ); ?>" type="<?php echo esc_attr( $this->get_audio_mime( $src ) ); ?>">
            </audio>
            <?php endif; ?>
        </div>
        <?php if ( ! empty( $src ) ) : ?>
        <script>
        (function(){
            var wrap = document.getElementById('<?php echo esc_js( $uid ); ?>');
            if(!wrap) return;
            var audio = wrap.querySelector('audio');
            var btn = wrap.querySelector('.olo-audio-playbtn');
            var iconPlay = wrap.querySelector('.olo-audio-icon-play');
            var iconPause = wrap.querySelector('.olo-audio-icon-pause');
            var progWrap = wrap.querySelector('.olo-audio-progress-wrap');
            var progBar = wrap.querySelector('.olo-audio-progress-bar');
            var timeCurrent = wrap.querySelector('.olo-audio-time-current');
            var timeTotal = wrap.querySelector('.olo-audio-time-total');
            var volumeSlider = wrap.querySelector('.olo-audio-volume');

            if(!audio) return;

            function formatTime(sec){
                if(isNaN(sec)) return '0:00';
                var m = Math.floor(sec/60);
                var s = Math.floor(sec%60);
                return m + ':' + (s < 10 ? '0' : '') + s;
            }

            btn.addEventListener('click', function(){
                if(audio.paused){
                    audio.play();
                } else {
                    audio.pause();
                }
            });

            audio.addEventListener('play', function(){
                iconPlay.style.display = 'none';
                iconPause.style.display = 'block';
                btn.setAttribute('aria-label', <?php echo wp_json_encode( __( 'Pausa', 'olobuilder' ) ); ?>);
            });
            audio.addEventListener('pause', function(){
                iconPlay.style.display = 'block';
                iconPause.style.display = 'none';
                btn.setAttribute('aria-label', <?php echo wp_json_encode( __( 'Riproduci', 'olobuilder' ) ); ?>);
            });
            audio.addEventListener('loadedmetadata', function(){
                timeTotal.textContent = formatTime(audio.duration);
            });
            audio.addEventListener('timeupdate', function(){
                if(audio.duration){
                    var pct = (audio.currentTime / audio.duration) * 100;
                    progBar.style.width = pct + '%';
                    if(progWrap){ progWrap.setAttribute('aria-valuenow', Math.round(pct)); }
                }
                timeCurrent.textContent = formatTime(audio.currentTime);
            });

            progWrap.addEventListener('click', function(e){
                if(!audio.duration) return;
                var rect = progWrap.getBoundingClientRect();
                var x = e.clientX - rect.left;
                var pct = x / rect.width;
                audio.currentTime = pct * audio.duration;
            });

            progWrap.addEventListener('keydown', function(e){
                if(!audio.duration) return;
                var step = audio.duration / 20;
                if(e.key === 'ArrowRight' || e.key === 'ArrowUp'){
                    audio.currentTime = Math.min(audio.duration, audio.currentTime + step);
                    e.preventDefault();
                } else if(e.key === 'ArrowLeft' || e.key === 'ArrowDown'){
                    audio.currentTime = Math.max(0, audio.currentTime - step);
                    e.preventDefault();
                } else if(e.key === 'Home'){
                    audio.currentTime = 0;
                    e.preventDefault();
                } else if(e.key === 'End'){
                    audio.currentTime = audio.duration;
                    e.preventDefault();
                }
            });

            if(volumeSlider){
                volumeSlider.addEventListener('input', function(){
                    audio.volume = parseFloat(this.value);
                });
            }
        })();
        </script>
        <?php endif; ?>
        <?php

        return ob_get_clean();
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    private function get_audio_mime( $url ) {
        $ext = strtolower( pathinfo( parse_url( $url, PHP_URL_PATH ) ?: '', PATHINFO_EXTENSION ) );
        $map = [
            'mp3'  => 'audio/mpeg',
            'ogg'  => 'audio/ogg',
            'wav'  => 'audio/wav',
            'flac' => 'audio/flac',
            'aac'  => 'audio/aac',
            'webm' => 'audio/webm',
            'm4a'  => 'audio/mp4',
        ];
        return $map[ $ext ] ?? 'audio/mpeg';
    }
}
