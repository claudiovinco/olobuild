<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Video_Tile extends Olo_Tile_Base {

    protected $type     = 'video';
    protected $name     = 'Video';
    protected $icon     = 'dashicons-video-alt3';
    protected $category = 'media';
    protected $defaults = [
        'source_type'     => 'embed',
        'video_url'       => '',
        'file_url'        => '',
        'display_mode'    => '16:9',
        'cover_height'    => '500',
        'autoplay'        => false,
        'muted'           => false,
        'loop'            => false,
        'controls'        => true,
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
        $embed_url = $this->get_embed_url( $s );
        $padding   = $this->get_aspect_padding( $s['display_mode'] );

        ob_start();
        ?>
        <div class="olo-video uk-responsive-width">
            <div class="uk-border-rounded" style="position: relative; padding-bottom: <?php echo esc_attr( $padding ); ?>; overflow: hidden;">
                <?php if ( $embed_url ) : ?>
                    <iframe
                        src="<?php echo esc_url( $embed_url ); ?>"
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                        loading="lazy"
                    ></iframe>
                <?php else : ?>
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: #1f2937; display: flex; align-items: center; justify-content: center; color: #9ca3af;">
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
            <div class="uk-border-rounded" style="position: relative; padding-bottom: <?php echo esc_attr( $padding ); ?>; overflow: hidden; background: #1f2937;">
                <?php if ( $src ) : ?>
                    <video
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;"
                        <?php echo ! empty( $s['autoplay'] ) ? 'autoplay' : ''; ?>
                        <?php echo ! empty( $s['muted'] ) || ! empty( $s['autoplay'] ) ? 'muted' : ''; ?>
                        <?php echo ! empty( $s['loop'] ) ? 'loop' : ''; ?>
                        <?php echo ! empty( $s['controls'] ) ? 'controls' : ''; ?>
                        playsinline
                    >
                        <source src="<?php echo esc_url( $src ); ?>" type="<?php echo esc_attr( $this->get_video_mime( $src ) ); ?>">
                    </video>
                <?php else : ?>
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #9ca3af;">
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
        $ov_color    = $this->safe_color( $s['overlay_color'] );
        $has_overlay = $ov_opacity > 0 && $ov_color;

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
                    playsinline
                >
                    <source src="<?php echo esc_url( $src ); ?>" type="<?php echo esc_attr( $this->get_video_mime( $src ) ); ?>">
                </video>
            <?php elseif ( $embed ) : ?>
                <iframe
                    src="<?php echo esc_url( $embed ); ?>"
                    style="position: absolute; top: 50%; left: 50%; width: 200%; height: 200%; transform: translate(-50%, -50%); pointer-events: none;"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    loading="lazy"
                ></iframe>
            <?php else : ?>
                <div style="width: 100%; height: 100%; background: #1f2937; display: flex; align-items: center; justify-content: center; color: #9ca3af;">
                    <?php echo esc_html__( 'Seleziona una sorgente video', 'olobuilder' ); ?>
                </div>
            <?php endif; ?>

            <?php if ( $has_overlay ) : ?>
                <div class="uk-position-cover" style="background-color: <?php echo $ov_color; ?>; opacity: <?php echo $ov_opacity / 100; ?>; pointer-events: none;"></div>
            <?php endif; ?>

            <?php if ( ! empty( $s['overlay_text'] ) ) : ?>
                <div class="uk-position-cover uk-flex uk-flex-center uk-flex-middle" style="z-index: 2; pointer-events: none;">
                    <div style="text-align: center; color: #fff; padding: 24px; max-width: 800px; pointer-events: auto;">
                        <?php echo wp_kses_post( $s['overlay_text'] ); ?>
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

    private function get_aspect_padding( $mode ) {
        $map = [ '16:9' => '56.25%', '4:3' => '75%', '1:1' => '100%' ];
        return $map[ $mode ] ?? '56.25%';
    }

    private function render_caption( $s ) {
        if ( ! empty( $s['caption'] ) ) {
            echo '<p class="uk-text-center uk-text-small" style="padding: 8px 0; color: #6b7280;">';
            echo wp_kses_post( $s['caption'] );
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
            $query = ! empty( $params ) ? '?' . implode( '&', $params ) : '';
            return 'https://www.youtube.com/embed/' . $matches[1] . $query;
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
