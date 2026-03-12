<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Video_Tile extends Olo_Tile_Base {

    protected $type     = 'video';
    protected $name     = 'Video';
    protected $icon     = 'dashicons-video-alt3';
    protected $category = 'essential';
    protected $defaults = [
        'source_type'     => 'embed',
        'video_url'       => '',
        'file_url'        => '',
        'display_mode'    => '16:9',
        'cover_height'    => '500',
        'facade'          => true,
        'autoplay'        => false,
        'muted'           => false,
        'loop'            => false,
        'controls'        => true,
        'start_time'      => '',
        'end_time'        => '',
        'poster_image'    => '',
        'privacy_mode'    => false,
        'show_play_icon'  => true,
        'play_icon_size'  => '80',
        'play_icon_color' => '#ffffff',
        'overlay_text'    => '',
        'overlay_color'   => '#000000',
        'overlay_opacity' => '0',
        'caption'         => '',
        // Legacy compat
        'aspect_ratio'    => '16:9',
        'cover_mode'      => false,
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        // Legacy compat: migrate old fields
        if ( empty( $s['display_mode'] ) || $s['display_mode'] === '16:9' ) {
            if ( ! empty( $s['cover_mode'] ) ) {
                $s['display_mode'] = 'cover';
            } elseif ( ! empty( $s['aspect_ratio'] ) && $s['aspect_ratio'] !== '16:9' ) {
                $s['display_mode'] = $s['aspect_ratio'];
            }
        }

        $is_file = $s['source_type'] === 'file' || $this->is_direct_video( $s['video_url'] );
        $is_cover = $s['display_mode'] === 'cover';

        if ( $is_cover ) {
            return $this->render_cover( $s, $is_file );
        }

        if ( $is_file ) {
            return $this->render_native( $s );
        }

        return $this->render_embed( $s );
    }

    // =========================================================================
    // Standard embed (YouTube / Vimeo iframe)
    // =========================================================================

    private function render_embed( $s ) {
        $embed_url    = $this->get_embed_url( $s );
        $padding      = $this->get_aspect_padding( $s['display_mode'] );
        $facade_on    = ! empty( $s['facade'] );

        // Video Facade: use poster_image if set, otherwise auto-detect YouTube/Vimeo thumbnail
        $poster_url = '';
        if ( $facade_on ) {
            if ( ! empty( $s['poster_image'] ) ) {
                $poster_url = $s['poster_image'];
            } else {
                $poster_url = $this->get_auto_thumbnail( $s['video_url'] ?? '' );
            }
        }
        $has_poster = $facade_on && ! empty( $poster_url ) && $embed_url;

        ob_start();
        ?>
        <div class="olo-video uk-responsive-width">
            <div class="uk-border-rounded" style="position: relative; padding-bottom: <?php echo esc_attr( $padding ); ?>; overflow: hidden;">
                <?php if ( $has_poster ) : ?>
                    <?php
                    $icon_size  = absint( $s['play_icon_size'] ) ?: 80;
                    $icon_color = $this->safe_color_css( $s['play_icon_color'] ) ?: '#fff';
                    $show_icon  = $s['show_play_icon'] !== false;
                    $uid        = 'olo-vp-' . wp_unique_id();
                    ?>
                    <div id="<?php echo esc_attr( $uid ); ?>" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; cursor: pointer;" onclick="(function(el){var p=el.parentNode;el.remove();var f=document.createElement('iframe');f.src='<?php echo esc_url( $embed_url . ( strpos( $embed_url, '?' ) !== false ? '&' : '?' ) . 'autoplay=1' ); ?>';f.style='position:absolute;top:0;left:0;width:100%;height:100%';f.frameBorder='0';f.allow='accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture';f.allowFullscreen=true;p.appendChild(f)})(this)">
                        <img src="<?php echo esc_url( $poster_url ); ?>" alt="" style="width:100%;height:100%;object-fit:cover;display:block;" loading="lazy" />
                        <?php if ( $show_icon ) : ?>
                        <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
                            <svg width="<?php echo $icon_size; ?>" height="<?php echo $icon_size; ?>" viewBox="0 0 80 80">
                                <circle cx="40" cy="40" r="38" fill="rgba(0,0,0,0.5)" stroke-width="2" stroke="<?php echo esc_attr( $icon_color ); ?>"/>
                                <polygon points="32,24 32,56 58,40" fill="<?php echo esc_attr( $icon_color ); ?>"/>
                            </svg>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php elseif ( $embed_url ) : ?>
                    <iframe
                        src="<?php echo esc_url( $embed_url ); ?>"
                        title="<?php echo esc_attr__( 'Embedded video', 'olobuilder' ); ?>"
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                        loading="lazy"
                    ></iframe>
                <?php else : ?>
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: var(--olo-color-secondary, #1F2937); display: flex; align-items: center; justify-content: center; color: var(--olo-color-text-muted, #9CA3AF);">
                        <?php echo esc_html__( 'Inserisci un URL video', 'olobuilder' ); ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php $this->render_caption( $s ); ?>
        </div>
        <?php
        return ob_get_clean();
    }

    // =========================================================================
    // Native <video> (self-hosted file, aspect-ratio mode)
    // =========================================================================

    private function render_native( $s ) {
        $src     = $this->get_file_src( $s );
        $padding = $this->get_aspect_padding( $s['display_mode'] );

        ob_start();
        ?>
        <div class="olo-video uk-responsive-width">
            <div class="uk-border-rounded" style="position: relative; padding-bottom: <?php echo esc_attr( $padding ); ?>; overflow: hidden; background: var(--olo-color-secondary, #1F2937);">
                <?php if ( $src ) : ?>
                    <video
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;"
                        <?php echo ! empty( $s['autoplay'] ) ? 'autoplay' : ''; ?>
                        <?php echo ! empty( $s['muted'] ) || ! empty( $s['autoplay'] ) ? 'muted' : ''; ?>
                        <?php echo ! empty( $s['loop'] ) ? 'loop' : ''; ?>
                        <?php echo ! empty( $s['controls'] ) ? 'controls' : ''; ?>
                        <?php echo ! empty( $s['poster_image'] ) ? 'poster="' . esc_url( $s['poster_image'] ) . '"' : ''; ?>
                        playsinline
                    >
                        <source src="<?php echo esc_url( $src ); ?>" type="<?php echo esc_attr( $this->get_video_mime( $src ) ); ?>">
                    </video>
                <?php else : ?>
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: var(--olo-color-text-muted, #9CA3AF);">
                        <?php echo esc_html__( 'Seleziona un file video', 'olobuilder' ); ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php $this->render_caption( $s ); ?>
        </div>
        <?php
        return ob_get_clean();
    }

    // =========================================================================
    // Cover mode (fixed height, object-fit: cover, optional overlay)
    // =========================================================================

    private function render_cover( $s, $is_file ) {
        $height = absint( $s['cover_height'] ) ?: 500;
        $src    = $is_file ? $this->get_file_src( $s ) : '';
        $embed  = ! $is_file ? $this->get_embed_url( $s ) : '';

        $ov_opacity  = absint( $s['overlay_opacity'] );
        $ov_color    = $this->safe_color_css( $s['overlay_color'] );
        $has_overlay = $ov_opacity > 0 && $ov_color;

        // Video Facade: auto-detect thumbnail for cover mode too (only when facade enabled)
        $facade_on   = ! empty( $s['facade'] );
        $poster_url  = '';
        if ( $facade_on ) {
            $poster_url = ! empty( $s['poster_image'] ) ? $s['poster_image'] : $this->get_auto_thumbnail( $s['video_url'] ?? '' );
        }
        $has_poster  = $facade_on && ! empty( $poster_url );
        $icon_size   = absint( $s['play_icon_size'] ) ?: 80;
        $icon_color  = $this->safe_color_css( $s['play_icon_color'] ) ?: '#fff';
        $show_icon   = $s['show_play_icon'] !== false;

        ob_start();
        ?>
        <div class="olo-video olo-video-cover uk-position-relative uk-overflow-hidden" style="height: <?php echo $height; ?>px;">
            <?php if ( $src ) : ?>
                <video
                    class="uk-position-cover"
                    style="object-fit: cover; width: 100%; height: 100%;"
                    <?php echo ! empty( $s['autoplay'] ) ? 'autoplay' : ''; ?>
                    <?php echo ! empty( $s['muted'] ) || ! empty( $s['autoplay'] ) ? 'muted' : ''; ?>
                    <?php echo ! empty( $s['loop'] ) ? 'loop' : ''; ?>
                    <?php echo ! empty( $s['controls'] ) ? 'controls' : ''; ?>
                    <?php echo $has_poster ? 'poster="' . esc_url( $poster_url ) . '"' : ''; ?>
                    playsinline
                >
                    <source src="<?php echo esc_url( $src ); ?>" type="<?php echo esc_attr( $this->get_video_mime( $src ) ); ?>">
                </video>
            <?php elseif ( $embed ) : ?>
                <?php if ( $has_poster ) : ?>
                    <?php $uid = 'olo-vp-' . wp_unique_id(); ?>
                    <div id="<?php echo esc_attr( $uid ); ?>" style="position:absolute;top:0;left:0;width:100%;height:100%;cursor:pointer;z-index:3;" onclick="(function(el){var p=el.parentNode;el.remove();var f=document.createElement('iframe');f.src='<?php echo esc_url( $embed . ( strpos( $embed, '?' ) !== false ? '&' : '?' ) . 'autoplay=1' ); ?>';f.style='position:absolute;top:50%;left:50%;width:200%;height:200%;transform:translate(-50%,-50%);pointer-events:none';f.frameBorder='0';f.allow='accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture';f.allowFullscreen=true;p.appendChild(f)})(this)">
                        <img src="<?php echo esc_url( $poster_url ); ?>" alt="" style="width:100%;height:100%;object-fit:cover;display:block;" loading="lazy" />
                        <?php if ( $show_icon ) : ?>
                        <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
                            <svg width="<?php echo $icon_size; ?>" height="<?php echo $icon_size; ?>" viewBox="0 0 80 80">
                                <circle cx="40" cy="40" r="38" fill="rgba(0,0,0,0.5)" stroke-width="2" stroke="<?php echo esc_attr( $icon_color ); ?>"/>
                                <polygon points="32,24 32,56 58,40" fill="<?php echo esc_attr( $icon_color ); ?>"/>
                            </svg>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php else : ?>
                    <iframe
                        src="<?php echo esc_url( $embed ); ?>"
                        title="<?php echo esc_attr__( 'Background video', 'olobuilder' ); ?>"
                        style="position: absolute; top: 50%; left: 50%; width: 200%; height: 200%; transform: translate(-50%, -50%); pointer-events: none;"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                        loading="lazy"
                    ></iframe>
                <?php endif; ?>
            <?php else : ?>
                <div style="width: 100%; height: 100%; background: var(--olo-color-secondary, #1F2937); display: flex; align-items: center; justify-content: center; color: var(--olo-color-text-muted, #9CA3AF);">
                    <?php echo esc_html__( 'Seleziona una sorgente video', 'olobuilder' ); ?>
                </div>
            <?php endif; ?>

            <?php if ( $has_overlay ) : ?>
                <div class="uk-position-cover" style="background-color: <?php echo $ov_color; ?>; opacity: <?php echo $ov_opacity / 100; ?>; pointer-events: none;"></div>
            <?php endif; ?>

            <?php if ( ! empty( $s['overlay_text'] ) ) : ?>
                <div class="uk-position-cover uk-flex uk-flex-center uk-flex-middle" style="z-index: 2; pointer-events: none;">
                    <div style="text-align: center; color: #fff; padding: 24px; max-width: 800px; pointer-events: auto;">
                        <?php echo nl2br( esc_html( wp_strip_all_tags( $s['overlay_text'] ) ) ); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php $this->render_caption( $s ); ?>
        <?php
        return ob_get_clean();
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * Auto-detect thumbnail URL for YouTube/Vimeo videos (Video Facade).
     * YouTube: predictable URL pattern. Vimeo: oEmbed API (cached).
     */
    private function get_auto_thumbnail( $url ) {
        if ( empty( $url ) ) return '';

        // YouTube — try maxresdefault first, fallback to hqdefault
        if ( preg_match( '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $m ) ) {
            $video_id  = $m[1];
            $cache_key = 'olo_yt_thumb_' . $video_id;
            $cached    = get_transient( $cache_key );
            if ( $cached ) return $cached;

            // Check if maxresdefault exists (returns 404 for some videos)
            $maxres = 'https://img.youtube.com/vi/' . $video_id . '/maxresdefault.jpg';
            $check  = wp_remote_head( $maxres, [ 'timeout' => 3 ] );
            if ( ! is_wp_error( $check ) ) {
                $code = wp_remote_retrieve_response_code( $check );
                if ( $code === 200 ) {
                    set_transient( $cache_key, $maxres, WEEK_IN_SECONDS );
                    return $maxres;
                }
            }

            // Fallback to hqdefault (always available)
            $hq = 'https://img.youtube.com/vi/' . $video_id . '/hqdefault.jpg';
            set_transient( $cache_key, $hq, WEEK_IN_SECONDS );
            return $hq;
        }

        // Vimeo — use oEmbed with transient cache (1 week)
        if ( preg_match( '/vimeo\.com\/(\d+)/', $url, $m ) ) {
            $cache_key = 'olo_vimeo_thumb_' . $m[1];
            $cached    = get_transient( $cache_key );
            if ( $cached ) return $cached;

            $resp = wp_remote_get( 'https://vimeo.com/api/oembed.json?url=' . urlencode( $url ), [ 'timeout' => 5 ] );
            if ( ! is_wp_error( $resp ) ) {
                $data = json_decode( wp_remote_retrieve_body( $resp ), true );
                if ( ! empty( $data['thumbnail_url'] ) ) {
                    set_transient( $cache_key, $data['thumbnail_url'], WEEK_IN_SECONDS );
                    return $data['thumbnail_url'];
                }
            }
        }

        return '';
    }

    private function get_aspect_padding( $mode ) {
        $map = [ '16:9' => '56.25%', '4:3' => '75%', '1:1' => '100%' ];
        return $map[ $mode ] ?? '56.25%';
    }

    private function render_caption( $s ) {
        if ( ! empty( $s['caption'] ) ) {
            echo '<p class="uk-text-center uk-text-small" style="padding: 8px 0; color: var(--olo-color-text-muted, #9CA3AF);">';
            echo esc_html( wp_strip_all_tags( $s['caption'] ) );
            echo '</p>';
        }
    }

    private function get_file_src( $s ) {
        if ( ! empty( $s['file_url'] ) ) {
            return $s['file_url'];
        }
        if ( $this->is_direct_video( $s['video_url'] ) ) {
            return $s['video_url'];
        }
        return '';
    }

    private function is_direct_video( $url ) {
        if ( empty( $url ) ) return false;
        return (bool) preg_match( '/\.(mp4|webm|ogg)(\?.*)?$/i', $url );
    }

    private function get_video_mime( $url ) {
        $ext = strtolower( pathinfo( parse_url( $url, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
        $map = [ 'mp4' => 'video/mp4', 'webm' => 'video/webm', 'ogg' => 'video/ogg' ];
        return $map[ $ext ] ?? 'video/mp4';
    }

    private function get_embed_url( $s ) {
        $url = $s['video_url'] ?? '';
        if ( empty( $url ) ) return '';

        $autoplay = ! empty( $s['autoplay'] );
        $muted    = ! empty( $s['muted'] ) || ! empty( $s['autoplay'] );
        $loop     = ! empty( $s['loop'] );
        $controls = isset( $s['controls'] ) ? (bool) $s['controls'] : true;
        $cover    = $s['display_mode'] === 'cover';

        // Start/end time
        $start = ! empty( $s['start_time'] ) ? absint( $s['start_time'] ) : 0;
        $end   = ! empty( $s['end_time'] )   ? absint( $s['end_time'] )   : 0;

        // YouTube
        if ( preg_match( '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches ) ) {
            $params = [];
            if ( $autoplay || $cover ) $params[] = 'autoplay=1';
            if ( $muted || $cover )    $params[] = 'mute=1';
            if ( $loop )               $params[] = 'loop=1&playlist=' . $matches[1];
            if ( ! $controls || $cover ) $params[] = 'controls=0';
            if ( $cover ) {
                $params[] = 'showinfo=0';
                $params[] = 'modestbranding=1';
                $params[] = 'rel=0';
            }
            if ( $start ) $params[] = 'start=' . $start;
            if ( $end )   $params[] = 'end=' . $end;
            $query  = ! empty( $params ) ? '?' . implode( '&', $params ) : '';
            $domain = ! empty( $s['privacy_mode'] ) ? 'www.youtube-nocookie.com' : 'www.youtube.com';
            return 'https://' . $domain . '/embed/' . $matches[1] . $query;
        }

        // Vimeo
        if ( preg_match( '/vimeo\.com\/(\d+)/', $url, $matches ) ) {
            $params = [];
            if ( $autoplay || $cover ) $params[] = 'autoplay=1';
            if ( $muted || $cover )    $params[] = 'muted=1';
            if ( $loop )               $params[] = 'loop=1';
            if ( ! $controls || $cover ) $params[] = 'controls=0';
            if ( $cover )              $params[] = 'background=1';
            $query = ! empty( $params ) ? '?' . implode( '&', $params ) : '';
            return 'https://player.vimeo.com/video/' . $matches[1] . $query;
        }

        return '';
    }
}
