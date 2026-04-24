<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_ServiceVideo_Tile extends Olo_Tile_Base {

    protected $type     = 'servicevideo';
    protected $name     = 'Video Servizio';
    protected $icon     = 'dashicons-video-alt3';
    protected $category = 'booking';
    protected $defaults = [
        'meta_prefix'    => '_olo_service_',
        'video_slot'     => '1',
        // Player
        'autoplay'       => false,
        'loop'           => true,
        'muted'          => true,
        'controls'       => true,
        'playsinline'    => true,
        // Layout
        'aspect_ratio'   => '16/9',
        'max_width'      => '',
        'border_radius'  => 12,
        // Poster
        'poster_from_thumb' => true,
        // Overlay play button
        'show_play_btn'  => true,
        'play_btn_size'  => 64,
        'play_btn_bg'    => 'rgba(0,0,0,0.5)',
        'play_btn_color' => '#FFFFFF',
    ];

    public function get_controls() {
        return [];
    }

    /**
     * Detect video type from URL.
     */
    private function detect_type( $url ) {
        if ( preg_match( '/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $m ) ) {
            return [ 'youtube', $m[1] ];
        }
        if ( preg_match( '/vimeo\.com\/(?:video\/)?(\d+)/', $url, $m ) ) {
            return [ 'vimeo', $m[1] ];
        }
        return [ 'file', $url ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        global $post;
        if ( ! $post || ! is_singular() ) {
            return '<div style="padding:24px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);background:var(--olo-color-muted, #F3F4F6);border-radius:8px">'
                 . '<p style="margin:0">Inserisci in un template single.</p></div>';
        }

        $pid = $post->ID;
        $pfx = rtrim( $s['meta_prefix'], '_' ) . '_';
        $slot = max( 1, min( 3, absint( $s['video_slot'] ) ) );

        $video_url = get_post_meta( $pid, $pfx . 'video_' . $slot, true );
        if ( empty( $video_url ) ) {
            return '<div style="padding:24px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);background:var(--olo-color-muted, #F3F4F6);border-radius:8px">'
                 . '<p style="margin:0">Video ' . $slot . ' non configurato.</p></div>';
        }

        $uid    = 'olo-svid-' . wp_rand( 10000, 99999 );
        $radius = $this->build_border_radius_css( $s["border_radius"] );
        $ratio  = esc_attr( $s['aspect_ratio'] ?: '16/9' );
        $max_w  = ! empty( $s['max_width'] ) ? 'max-width:' . absint( $s['max_width'] ) . 'px;margin:0 auto;' : '';

        list( $type, $id_or_url ) = $this->detect_type( $video_url );

        // Build attributes
        $autoplay    = ! empty( $s['autoplay'] );
        $loop        = ! empty( $s['loop'] );
        $muted       = ! empty( $s['muted'] );
        $controls    = ! empty( $s['controls'] );
        $playsinline = ! empty( $s['playsinline'] );

        $play_btn_size  = max( 32, min( 96, absint( $s['play_btn_size'] ) ) );
        $play_btn_bg    = $this->safe_color_css( $s['play_btn_bg'] );
        $play_btn_color = $this->safe_color_css( $s['play_btn_color'] );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                position: relative;
                border-radius: <?php echo $radius; ?>;
                overflow: hidden;
                aspect-ratio: <?php echo $ratio; ?>;
                <?php echo $max_w; ?>
                background: #000;
            }
            .<?php echo $uid; ?> video,
            .<?php echo $uid; ?> iframe {
                width: 100%;
                height: 100%;
                display: block;
                border: 0;
                border-radius: <?php echo $radius; ?>;
            }
            <?php if ( ! empty( $s['show_play_btn'] ) && $type === 'file' ) : ?>
            .<?php echo $uid; ?> .olo-svid-play {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%,-50%);
                z-index: 3;
                width: <?php echo $play_btn_size; ?>px;
                height: <?php echo $play_btn_size; ?>px;
                border-radius: 50%;
                background: <?php echo $play_btn_bg; ?>;
                backdrop-filter: blur(8px);
                -webkit-backdrop-filter: blur(8px);
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: transform 0.3s ease, background 0.3s ease;
                border: 0;
                padding: 0;
            }
            .<?php echo $uid; ?> .olo-svid-play:hover {
                transform: translate(-50%,-50%) scale(1.1);
                background: rgba(0,0,0,0.7);
            }
            .<?php echo $uid; ?> .olo-svid-play svg {
                width: <?php echo round( $play_btn_size * 0.4 ); ?>px;
                height: <?php echo round( $play_btn_size * 0.4 ); ?>px;
                margin-left: <?php echo round( $play_btn_size * 0.05 ); ?>px;
            }
            .<?php echo $uid; ?>.olo-svid-playing .olo-svid-play { display: none; }
            <?php endif; ?>
        </style>
        <div class="<?php echo esc_attr( $uid ); ?>">
        <?php if ( $type === 'youtube' ) :
            $yt_params = [];
            if ( $autoplay )    $yt_params[] = 'autoplay=1';
            if ( $loop )        $yt_params[] = 'loop=1&playlist=' . esc_attr( $id_or_url );
            if ( $muted )       $yt_params[] = 'mute=1';
            if ( ! $controls )  $yt_params[] = 'controls=0';
            $yt_params[] = 'playsinline=1';
            $yt_params[] = 'rel=0';
            $yt_src = 'https://www.youtube-nocookie.com/embed/' . esc_attr( $id_or_url ) . '?' . implode( '&', $yt_params );
        ?>
            <iframe src="<?php echo esc_url( $yt_src ); ?>" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen loading="lazy"></iframe>
        <?php elseif ( $type === 'vimeo' ) :
            $vm_params = [];
            if ( $autoplay )    $vm_params[] = 'autoplay=1';
            if ( $loop )        $vm_params[] = 'loop=1';
            if ( $muted )       $vm_params[] = 'muted=1';
            if ( ! $controls )  $vm_params[] = 'controls=0';
            $vm_params[] = 'playsinline=1';
            $vm_src = 'https://player.vimeo.com/video/' . esc_attr( $id_or_url ) . '?' . implode( '&', $vm_params );
        ?>
            <iframe src="<?php echo esc_url( $vm_src ); ?>" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen loading="lazy"></iframe>
        <?php else :
            // Self-hosted / MP4
            $poster_url = '';
            if ( ! empty( $s['poster_from_thumb'] ) ) {
                $poster_url = get_the_post_thumbnail_url( $pid, 'large' );
            }
            $vid_attrs = '';
            if ( $controls )    $vid_attrs .= ' controls';
            if ( $loop )        $vid_attrs .= ' loop';
            if ( $muted )       $vid_attrs .= ' muted';
            if ( $playsinline ) $vid_attrs .= ' playsinline';
            if ( $autoplay )    $vid_attrs .= ' autoplay';
            if ( $poster_url )  $vid_attrs .= ' poster="' . esc_url( $poster_url ) . '"';
        ?>
            <video<?php echo $vid_attrs; ?> preload="metadata">
                <source src="<?php echo esc_url( $id_or_url ); ?>" type="<?php echo esc_attr( $this->get_mime( $id_or_url ) ); ?>">
            </video>
            <?php if ( ! empty( $s['show_play_btn'] ) && ! $autoplay ) : ?>
            <?php self::enqueue_delegated_events(); ?>
            <button class="olo-svid-play" aria-label="<?php echo esc_attr( olo_t( 'Riproduci' ) ); ?>" data-olo-svid-play="1">
                <svg viewBox="0 0 24 24" fill="<?php echo $play_btn_color; ?>"><polygon points="6,3 20,12 6,21"/></svg>
            </button>
            <?php endif; ?>
        <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private function get_mime( $url ) {
        $ext = strtolower( pathinfo( wp_parse_url( $url, PHP_URL_PATH ) ?: '', PATHINFO_EXTENSION ) );
        $map = [ 'mp4' => 'video/mp4', 'webm' => 'video/webm', 'ogg' => 'video/ogg', 'mov' => 'video/mp4' ];
        return $map[ $ext ] ?? 'video/mp4';
    }
}
