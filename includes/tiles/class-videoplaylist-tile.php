<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Videoplaylist_Tile extends Olobuild_Tile_Base {

    protected $type     = 'videoplaylist';
    protected $name     = 'Video Playlist';
    protected $icon     = 'dashicons-playlist-video';
    protected $category = 'media';
    protected $defaults = [
        'videos'        => [
            [ 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'title' => 'Primo video', 'duration' => '3:32', 'thumbnail' => '' ],
            [ 'url' => 'https://www.youtube.com/watch?v=9bZkp7q19f0', 'title' => 'Secondo video', 'duration' => '4:12', 'thumbnail' => '' ],
            [ 'url' => 'https://www.youtube.com/watch?v=kJQP7kiw5Fk', 'title' => 'Terzo video', 'duration' => '5:01', 'thumbnail' => '' ],
        ],
        'layout'        => 'sidebar-right',
        'player_height' => '360',
        'sidebar_width' => '280',
        'sidebar_bg'    => '',
        'text_color'    => '',
        'active_color'  => '',
        'show_duration' => true,
        'autoplay_next' => false,
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
            [ 'key' => 'videos',        'type' => 'custom', 'label' => 'Videos' ],
            [ 'key' => 'layout',        'type' => 'select', 'label' => 'Layout' ],
            [ 'key' => 'player_height', 'type' => 'range',  'label' => 'Player Height' ],
            [ 'key' => 'sidebar_width', 'type' => 'range',  'label' => 'Sidebar Width' ],
            [ 'key' => 'sidebar_bg',    'type' => 'color',  'label' => 'Sidebar Background' ],
            [ 'key' => 'text_color',    'type' => 'color',  'label' => 'Text Color' ],
            [ 'key' => 'active_color',  'type' => 'color',  'label' => 'Active Color' ],
            [ 'key' => 'show_duration', 'type' => 'toggle', 'label' => 'Show Duration' ],
            [ 'key' => 'autoplay_next', 'type' => 'toggle', 'label' => 'Autoplay Next' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $videos = is_array( $s['videos'] ) ? $s['videos'] : [];
        if ( empty( $videos ) ) {
            return '<div style="padding:40px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF)">Aggiungi video alla playlist</div>';
        }

        $uid           = 'olo-videoplaylist-' . wp_unique_id();
        $layout        = in_array( $s['layout'], [ 'sidebar-right', 'sidebar-left', 'below' ], true ) ? $s['layout'] : 'sidebar-right';
        $player_height = max( 200, min( 600, intval( $s['player_height'] ) ) );
        $sidebar_width = max( 200, min( 400, intval( $s['sidebar_width'] ) ) );
        $sidebar_bg    = $this->safe_color_css( $s['sidebar_bg'] ) ?: 'var(--olo-color-secondary, #1F2937)';
        $text_color    = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-primary-contrast, #FFFFFF)';
        $active_color  = $this->safe_color_css( $s['active_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $show_duration = ! empty( $s['show_duration'] );
        $autoplay_next = ! empty( $s['autoplay_next'] );

        // Parse first video for initial embed
        $first_url   = trim( $videos[0]['url'] ?? '' );
        $first_title = $videos[0]['title'] ?? 'Video 1';
        $first_embed = $this->get_player_html( $first_url, $first_title );

        $flex_dir = 'row';
        if ( $layout === 'below' ) {
            $flex_dir = 'column';
        } elseif ( $layout === 'sidebar-left' ) {
            $flex_dir = 'row-reverse';
        }

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: colors via the safe_color_css() whitelist (with var() token fallbacks), heights/widths via intval() with min()/max() clamps, flex-direction from fixed literals; $uid is internally generated and esc_attr()'d. ?>
        <style>
            .<?php echo esc_attr( $uid ); ?> {
                display: flex;
                flex-direction: <?php echo $flex_dir; ?>;
                border-radius: 8px;
                overflow: hidden;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-vp-player {
                flex: 1;
                min-width: 0;
                height: <?php echo $player_height; ?>px;
                background: #000000;
                position: relative;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-vp-player iframe,
            .<?php echo esc_attr( $uid ); ?> .olo-vp-player video {
                width: 100%;
                height: 100%;
                display: block;
                border: none;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-vp-sidebar {
                <?php if ( $layout === 'below' ) : ?>
                width: 100%;
                max-height: 240px;
                <?php else : ?>
                width: <?php echo $sidebar_width; ?>px;
                flex-shrink: 0;
                height: <?php echo $player_height; ?>px;
                <?php endif; ?>
                background: <?php echo $sidebar_bg; ?>;
                color: <?php echo $text_color; ?>;
                overflow-y: auto;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-vp-item {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px 12px;
                cursor: pointer;
                border-left: 3px solid transparent;
                transition: background 0.15s, border-color 0.15s;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-vp-item:hover {
                background: rgba(255,255,255,0.04);
            }
            .<?php echo esc_attr( $uid ); ?> .olo-vp-item.is-active {
                border-left-color: <?php echo $active_color; ?>;
                background: rgba(255,255,255,0.06);
            }
            .<?php echo esc_attr( $uid ); ?> .olo-vp-thumb {
                width: 48px;
                height: 32px;
                flex-shrink: 0;
                background: rgba(255,255,255,0.08);
                border-radius: 3px;
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-vp-thumb img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-vp-num {
                font-size: 11px;
                font-weight: 700;
                opacity: 0.5;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-vp-info {
                flex: 1;
                min-width: 0;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-vp-title {
                font-size: 13px;
                font-weight: 600;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .<?php echo esc_attr( $uid ); ?> .olo-vp-duration {
                font-size: 11px;
                opacity: 0.55;
                margin-top: 1px;
            }
            @media (max-width: 640px) {
                .<?php echo esc_attr( $uid ); ?> {
                    flex-direction: column;
                }
                .<?php echo esc_attr( $uid ); ?> .olo-vp-sidebar {
                    width: 100%;
                    height: auto;
                    max-height: 200px;
                }
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <div class="<?php echo esc_attr( $uid ); ?>" id="<?php echo esc_attr( $uid ); ?>">
            <!-- Player -->
            <div class="olo-vp-player" id="<?php echo esc_attr( $uid ); ?>-player">
                <?php echo $first_embed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- embed HTML built by get_player_html() with esc_attr()/esc_url() internally; placeholder is fixed markup ?>
            </div>

            <!-- Sidebar / Playlist -->
            <div class="olo-vp-sidebar" role="list" aria-label="<?php echo esc_attr( olobuild_t( 'Playlist video', 'olobuilder' ) ); ?>">
                <?php foreach ( $videos as $idx => $video ) :
                    $v_url       = esc_attr( trim( $video['url'] ?? '' ) );
                    $v_title_raw = $video['title'] ?? 'Video ' . ( $idx + 1 );
                    $v_title     = esc_html( $v_title_raw );
                    $v_dur   = esc_html( $video['duration'] ?? '' );
                    $v_thumb = $video['thumbnail'] ?? '';
                    $is_first = ( $idx === 0 );
                ?>
                <div class="olo-vp-item<?php echo $is_first ? ' is-active' : ''; ?>"
                     role="button"
                     tabindex="0"
                     aria-label="<?php echo esc_attr( $v_title_raw ); ?>"
                     aria-current="<?php echo $is_first ? 'true' : 'false'; ?>"
                     data-url="<?php echo $v_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped via esc_attr() at assignment above ?>"
                     data-idx="<?php echo (int) $idx; ?>">
                    <div class="olo-vp-thumb">
                        <?php if ( $v_thumb ) : ?>
                            <img src="<?php echo esc_url( $v_thumb ); ?>" alt="" loading="lazy" />
                        <?php else : ?>
                            <span class="olo-vp-num"><?php echo (int) $idx + 1; ?></span>
                        <?php endif; ?>
                    </div>
                    <?php list( $vpt_cls, $vpt_data ) = $this->tfx_attrs( $s, 'title', $v_title ); ?>
                    <div class="olo-vp-info">
                        <div class="olo-vp-title<?php echo $vpt_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); title escaped via esc_html() at assignment above ?>"<?php echo $vpt_data; ?>><?php echo $v_title; ?></div>
                        <?php if ( $show_duration ) : ?>
                            <?php if ( $v_dur ) : ?>
                                <div class="olo-vp-duration"><?php echo $v_dur; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped via esc_html() at assignment above ?></div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <script>
        (function(){
            var wrap = document.getElementById('<?php echo esc_js( $uid ); ?>');
            if(!wrap){return}
            var playerEl = document.getElementById('<?php echo esc_js( $uid ); ?>-player');
            if(!playerEl){return}
            var autoNext = <?php echo $autoplay_next ? 'true' : 'false'; ?>;
            var videoData = <?php echo wp_json_encode( array_map( function( $v ) { // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline JSON in <script> generated by wp_json_encode() (safe in script context)
                return [ 'url' => $v['url'] ?? '', 'title' => $v['title'] ?? '' ];
            }, $videos ), JSON_UNESCAPED_SLASHES ); ?>;
            var currentIdx = 0;
            var totalVideos = videoData.length;

            function parseVideoUrl(url) {
                var yt = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
                if(yt){
                    return {type:'youtube',id:yt[1]};
                }
                var vim = url.match(/vimeo\.com\/(\d+)/);
                if(vim){
                    return {type:'vimeo',id:vim[1]};
                }
                return {type:'file',id:url};
            }

            function escAttr(s) {
                return String(s == null ? '' : s).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
            }

            function buildEmbed(url, title) {
                var info = parseVideoUrl(url);
                var titleAttr = ' title="' + escAttr(title || 'Video') + '"';
                if(info.type === 'youtube'){
                    return '<iframe src="https://www.youtube-nocookie.com/embed/' + info.id + '?autoplay=1"' + titleAttr + ' frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                }
                if(info.type === 'vimeo'){
                    return '<iframe src="https://player.vimeo.com/video/' + info.id + '?autoplay=1&dnt=1"' + titleAttr + ' frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
                }
                return '<video controls autoplay><source src="' + info.id + '" type="video/mp4"></video>';
            }

            function setActive(idx) {
                var items = wrap.querySelectorAll('.olo-vp-item');
                items.forEach(function(it){ it.classList.remove('is-active'); it.setAttribute('aria-current','false'); });
                if(items[idx]){
                    items[idx].classList.add('is-active');
                    items[idx].setAttribute('aria-current','true');
                }
            }

            function playVideo(idx) {
                if(idx < 0){return}
                if(idx >= totalVideos){return}
                currentIdx = idx;
                var url = videoData[idx].url;
                if(!url){return}
                playerEl.innerHTML = buildEmbed(url, videoData[idx].title);
                setActive(idx);

                /* Listen for video end (MP4 only) to autoplay next */
                if(autoNext){
                    var vid = playerEl.querySelector('video');
                    if(vid){
                        vid.addEventListener('ended', function(){
                            var next = currentIdx + 1;
                            if(next < totalVideos){
                                playVideo(next);
                            }
                        });
                    }
                }
            }

            /* Delegated click on sidebar items */
            wrap.addEventListener('click', function(e){
                var item = e.target.closest('.olo-vp-item');
                if(!item){return}
                var idx = parseInt(item.getAttribute('data-idx'), 10);
                if(isNaN(idx)){return}
                playVideo(idx);
            });

            /* Keyboard activation (Enter / Space) for role="button" items */
            wrap.addEventListener('keydown', function(e){
                if(e.key !== 'Enter' && e.key !== ' ' && e.key !== 'Spacebar'){return}
                var item = e.target.closest('.olo-vp-item');
                if(!item){return}
                var idx = parseInt(item.getAttribute('data-idx'), 10);
                if(isNaN(idx)){return}
                e.preventDefault();
                playVideo(idx);
            });
        })();
        </script>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Text_Effects::css() from whitelisted effects, sanitized colors and integer timings
        $this->tfx_print_script();
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized border settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base border helpers from sanitized border settings
        }
        return ob_get_clean();
    }

    /**
     * Build initial player embed HTML.
     */
    private function get_player_html( $url, $title = '' ) {
        $url = trim( $url );
        if ( empty( $url ) ) {
            return '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:var(--olo-color-text-muted, #9CA3AF);font-size:14px;">Nessun video</div>';
        }

        $title_attr = ' title="' . esc_attr( $title !== '' ? $title : olobuild_t( 'Video', 'olobuilder' ) ) . '"';

        // YouTube
        if ( preg_match( '/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $m ) ) {
            return '<iframe src="https://www.youtube-nocookie.com/embed/' . esc_attr( $m[1] ) . '"' . $title_attr . ' frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe>';
        }

        // Vimeo
        if ( preg_match( '/vimeo\.com\/(\d+)/', $url, $m ) ) {
            return '<iframe src="https://player.vimeo.com/video/' . esc_attr( $m[1] ) . '?dnt=1"' . $title_attr . ' frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen loading="lazy"></iframe>';
        }

        // Direct file
        return '<video controls preload="metadata"><source src="' . esc_url( $url ) . '" type="video/mp4"></video>';
    }
}
