<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Videoplaylist_Tile extends Olo_Tile_Base {

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
        $active_color  = $this->safe_color_css( $s['active_color'] ) ?: 'var(--olo-color-primary, #6366F1)';
        $show_duration = ! empty( $s['show_duration'] );
        $autoplay_next = ! empty( $s['autoplay_next'] );

        // Parse first video for initial embed
        $first_url   = trim( $videos[0]['url'] ?? '' );
        $first_embed = $this->get_player_html( $first_url );

        $flex_dir = 'row';
        if ( $layout === 'below' ) {
            $flex_dir = 'column';
        } elseif ( $layout === 'sidebar-left' ) {
            $flex_dir = 'row-reverse';
        }

        ob_start();
        ?>
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

        <div class="<?php echo esc_attr( $uid ); ?>" id="<?php echo esc_attr( $uid ); ?>">
            <!-- Player -->
            <div class="olo-vp-player" id="<?php echo esc_attr( $uid ); ?>-player">
                <?php echo $first_embed; ?>
            </div>

            <!-- Sidebar / Playlist -->
            <div class="olo-vp-sidebar">
                <?php foreach ( $videos as $idx => $video ) :
                    $v_url   = esc_attr( trim( $video['url'] ?? '' ) );
                    $v_title = esc_html( $video['title'] ?? 'Video ' . ( $idx + 1 ) );
                    $v_dur   = esc_html( $video['duration'] ?? '' );
                    $v_thumb = $video['thumbnail'] ?? '';
                    $is_first = ( $idx === 0 );
                ?>
                <div class="olo-vp-item<?php echo $is_first ? ' is-active' : ''; ?>"
                     data-url="<?php echo $v_url; ?>"
                     data-idx="<?php echo $idx; ?>">
                    <div class="olo-vp-thumb">
                        <?php if ( $v_thumb ) : ?>
                            <img src="<?php echo esc_url( $v_thumb ); ?>" alt="" loading="lazy" />
                        <?php else : ?>
                            <span class="olo-vp-num"><?php echo $idx + 1; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="olo-vp-info">
                        <div class="olo-vp-title"><?php echo $v_title; ?></div>
                        <?php if ( $show_duration ) : ?>
                            <?php if ( $v_dur ) : ?>
                                <div class="olo-vp-duration"><?php echo $v_dur; ?></div>
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
            var videoData = <?php echo wp_json_encode( array_map( function( $v ) {
                return [ 'url' => $v['url'] ?? '' ];
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

            function buildEmbed(url) {
                var info = parseVideoUrl(url);
                if(info.type === 'youtube'){
                    return '<iframe src="https://www.youtube-nocookie.com/embed/' + info.id + '?autoplay=1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                }
                if(info.type === 'vimeo'){
                    return '<iframe src="https://player.vimeo.com/video/' + info.id + '?autoplay=1&dnt=1" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
                }
                return '<video controls autoplay><source src="' + info.id + '" type="video/mp4"></video>';
            }

            function setActive(idx) {
                var items = wrap.querySelectorAll('.olo-vp-item');
                items.forEach(function(it){ it.classList.remove('is-active'); });
                if(items[idx]){
                    items[idx].classList.add('is-active');
                }
            }

            function playVideo(idx) {
                if(idx < 0){return}
                if(idx >= totalVideos){return}
                currentIdx = idx;
                var url = videoData[idx].url;
                if(!url){return}
                playerEl.innerHTML = buildEmbed(url);
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
        })();
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * Build initial player embed HTML.
     */
    private function get_player_html( $url ) {
        $url = trim( $url );
        if ( empty( $url ) ) {
            return '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:var(--olo-color-text-muted, #9CA3AF);font-size:14px;">Nessun video</div>';
        }

        // YouTube
        if ( preg_match( '/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $m ) ) {
            return '<iframe src="https://www.youtube-nocookie.com/embed/' . esc_attr( $m[1] ) . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe>';
        }

        // Vimeo
        if ( preg_match( '/vimeo\.com\/(\d+)/', $url, $m ) ) {
            return '<iframe src="https://player.vimeo.com/video/' . esc_attr( $m[1] ) . '?dnt=1" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen loading="lazy"></iframe>';
        }

        // Direct file
        return '<video controls preload="metadata"><source src="' . esc_url( $url ) . '" type="video/mp4"></video>';
    }
}
