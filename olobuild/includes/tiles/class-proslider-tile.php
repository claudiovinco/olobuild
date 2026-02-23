<?php
/**
 * ProSlider Tile — Professional slider with animated layers.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_ProSlider_Tile extends Olo_Tile_Base {

    protected $type     = 'proslider';
    protected $name     = 'Pro Slider';
    protected $icon     = 'dashicons-slides';
    protected $category = 'media';
    protected $defaults = [
        'slides'             => [
            [
                'id'         => 'ps-default-1',
                'background' => [
                    'type'           => 'color',
                    'image'          => '',
                    'video'          => '',
                    'color'          => '#1e293b',
                    'gradientFrom'   => '#1e293b',
                    'gradientTo'     => '#0f172a',
                    'gradientAngle'  => 180,
                    'kenBurns'       => false,
                    'kenBurnsScale'  => 1.2,
                    'kenBurnsDuration' => 8000,
                    'overlay'        => '#000000',
                    'overlayOpacity' => 0.3,
                ],
                'duration' => 5000,
                'layers'   => [
                    [
                        'id'             => 'ps-layer-1',
                        'type'           => 'text',
                        'content'        => 'Heading Text',
                        'tag'            => 'h2',
                        'imageSrc'       => '',
                        'iconName'       => 'star',
                        'buttonUrl'      => '',
                        'buttonTarget'   => '_self',
                        'x'              => 10,
                        'y'              => 30,
                        'width'          => 80,
                        'height'         => 'auto',
                        'fontSize'       => 48,
                        'fontWeight'     => '700',
                        'color'          => '#ffffff',
                        'textAlign'      => 'left',
                        'bgColor'        => '',
                        'borderRadius'   => 0,
                        'padding'        => 0,
                        'opacity'        => 100,
                        'animIn'         => 'fadeInUp',
                        'animInDuration' => 800,
                        'animInDelay'    => 200,
                        'animOut'        => 'fadeOutDown',
                        'animOutDuration' => 600,
                        'animOutDelay'   => 0,
                        'animEasing'     => 'ease',
                    ],
                ],
            ],
        ],
        'height'             => 600,
        'autoplay'           => true,
        'autoplaySpeed'      => 5000,
        'pauseOnHover'       => true,
        'loop'               => true,
        'transition'         => 'fade',
        'transitionDuration' => 800,
        'showArrows'         => true,
        'showDots'           => true,
        'keyboard'           => true,
        'swipe'              => true,
        'globalBackground'   => [
            'type'           => 'color',
            'image'          => '',
            'video'          => '',
            'color'          => '#1e293b',
            'gradientFrom'   => '#1e293b',
            'gradientTo'     => '#0f172a',
            'gradientAngle'  => 180,
            'kenBurns'       => false,
            'kenBurnsScale'  => 1.2,
            'kenBurnsDuration' => 8000,
        ],
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $slides = is_array( $s['slides'] ) ? $s['slides'] : [];
        if ( empty( $slides ) ) {
            return '<div class="olo-proslider" style="padding:60px;text-align:center;color:#6b7280;">No slides configured — open the Slider Editor</div>';
        }

        $uid        = 'mps-' . wp_unique_id();
        $height     = absint( $s['height'] );
        $transition = in_array( $s['transition'], [ 'fade', 'slide', 'zoom' ], true ) ? $s['transition'] : 'fade';
        $trans_dur  = absint( $s['transitionDuration'] );

        // Build config JSON for the frontend JS runtime.
        $config = [
            'autoplay'      => (bool) $s['autoplay'],
            'autoplaySpeed' => absint( $s['autoplaySpeed'] ),
            'pauseOnHover'  => (bool) $s['pauseOnHover'],
            'loop'          => (bool) $s['loop'],
            'transition'    => $transition,
            'transDuration' => $trans_dur,
            'keyboard'      => (bool) $s['keyboard'],
            'swipe'         => (bool) $s['swipe'],
            'total'         => count( $slides ),
        ];

        // Global background
        $global_bg = isset( $s['globalBackground'] ) && is_array( $s['globalBackground'] ) ? $s['globalBackground'] : [];

        ob_start();
        ?>
        <div
            class="olo-proslider"
            id="<?php echo esc_attr( $uid ); ?>"
            data-transition="<?php echo esc_attr( $transition ); ?>"
            data-proslider="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"
            style="height:<?php echo $height; ?>px;--mps-trans-dur:<?php echo $trans_dur; ?>ms;"
        >
            <?php if ( ! empty( $global_bg ) && ( $global_bg['type'] ?? 'color' ) !== 'color' || ! empty( $global_bg['color'] ) ) : ?>
                <div class="olo-proslider-global-bg"><?php echo $this->render_bg( $global_bg ); ?></div>
            <?php endif; ?>
            <div class="olo-proslider-track">
                <?php foreach ( $slides as $si => $slide ) : ?>
                    <?php echo $this->render_slide( $slide, $si ); ?>
                <?php endforeach; ?>
            </div>

            <?php if ( $s['showArrows'] && count( $slides ) > 1 ) : ?>
                <button class="olo-proslider-arrow olo-proslider-prev" aria-label="Previous">&lsaquo;</button>
                <button class="olo-proslider-arrow olo-proslider-next" aria-label="Next">&rsaquo;</button>
            <?php endif; ?>

            <?php if ( $s['showDots'] && count( $slides ) > 1 ) : ?>
                <div class="olo-proslider-dots">
                    <?php for ( $d = 0; $d < count( $slides ); $d++ ) : ?>
                        <button class="olo-proslider-dot<?php echo $d === 0 ? ' mps-dot-active' : ''; ?>" data-slide="<?php echo $d; ?>" aria-label="Slide <?php echo $d + 1; ?>"></button>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /* ─── Slide ───────────────────────────────────────────── */

    private function render_slide( $slide, $index ) {
        $bg = isset( $slide['background'] ) && is_array( $slide['background'] ) ? $slide['background'] : [];
        $layers = isset( $slide['layers'] ) && is_array( $slide['layers'] ) ? $slide['layers'] : [];

        $active_class = $index === 0 ? ' mps-active' : '';
        $persist_for  = 0;
        if ( ! empty( $slide['persistFor'] ) ) {
            $persist_for = absint( $slide['persistFor'] );
        } elseif ( ! empty( $slide['persistent'] ) ) {
            // Backward compat: boolean persistent → treat as 99
            $persist_for = 99;
        }

        $slide_duration = absint( $slide['duration'] ?? 0 );

        ob_start();
        ?>
        <div class="olo-proslider-slide<?php echo $active_class; ?>"
             data-slide-index="<?php echo (int) $index; ?>"
             <?php echo $persist_for > 0 ? 'data-persist-for="' . $persist_for . '"' : ''; ?>
             <?php echo $slide_duration > 0 ? 'data-duration="' . $slide_duration . '"' : ''; ?>
        >
            <!-- Background -->
            <div class="olo-proslider-bg">
                <?php echo $this->render_bg( $bg ); ?>
                <?php
                $bg_type    = $bg['type'] ?? 'color';
                $ov_color   = $this->safe_color( $bg['overlay'] ?? '#000000' );
                $ov_opacity = isset( $bg['overlayOpacity'] ) ? floatval( $bg['overlayOpacity'] ) : 0.3;
                if ( $bg_type !== 'transparent' && $ov_opacity > 0 && $ov_color ) :
                ?>
                    <div class="olo-proslider-overlay" style="background:<?php echo $ov_color; ?>;opacity:<?php echo $ov_opacity; ?>"></div>
                <?php endif; ?>
            </div>

            <!-- Layers -->
            <?php foreach ( $layers as $layer ) : ?>
                <?php echo $this->render_layer( $layer ); ?>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /* ─── Background ──────────────────────────────────────── */

    private function render_bg( $bg ) {
        $type = $bg['type'] ?? 'color';

        // Transparent
        if ( $type === 'transparent' ) {
            return '';
        }

        // Image
        if ( $type === 'image' && ! empty( $bg['image'] ) ) {
            $kb       = ! empty( $bg['kenBurns'] );
            $kb_class = $kb ? ' olo-proslider-kenburns' : '';
            $kb_style = '';
            if ( $kb ) {
                $scale = floatval( $bg['kenBurnsScale'] ?? 1.2 );
                $dur   = absint( $bg['kenBurnsDuration'] ?? 8000 );
                $kb_style = '--mps-kb-scale:' . $scale . ';--mps-kb-dur:' . $dur . 'ms;';
            }
            return '<img src="' . esc_url( $bg['image'] ) . '" alt="Slide background" class="olo-proslider-bg-img' . $kb_class . '" style="' . esc_attr( $kb_style ) . '" draggable="false" loading="lazy" />';
        }

        // Video — self hosted
        if ( $type === 'video' && ! empty( $bg['video'] ) ) {
            $url = $bg['video'];
            if ( preg_match( '/youtube\.com|youtu\.be/i', $url ) ) {
                $vid_id = $this->extract_youtube_id( $url );
                if ( $vid_id ) {
                    return '<iframe src="https://www.youtube-nocookie.com/embed/' . esc_attr( $vid_id ) . '?autoplay=1&mute=1&loop=1&playlist=' . esc_attr( $vid_id ) . '&controls=0&showinfo=0&rel=0&modestbranding=1&playsinline=1" allow="autoplay; encrypted-media" allowfullscreen loading="lazy"></iframe>';
                }
            }
            return '<video src="' . esc_url( $url ) . '" autoplay muted loop playsinline></video>';
        }

        // Gradient
        if ( $type === 'gradient' ) {
            $from  = $this->safe_color( $bg['gradientFrom'] ?? '#1e293b' );
            $to    = $this->safe_color( $bg['gradientTo'] ?? '#0f172a' );
            $angle = absint( $bg['gradientAngle'] ?? 180 );
            return '<div style="width:100%;height:100%;background:linear-gradient(' . $angle . 'deg,' . $from . ',' . $to . ');"></div>';
        }

        // Color fallback
        $color = $this->safe_color( $bg['color'] ?? '#1e293b' );
        return '<div style="width:100%;height:100%;background:' . ( $color ?: '#1e293b' ) . ';"></div>';
    }

    /* ─── Layer ────────────────────────────────────────────── */

    private function render_layer( $layer ) {
        $type = $layer['type'] ?? 'text';

        // Position style
        $x = floatval( $layer['x'] ?? 10 );
        $y = floatval( $layer['y'] ?? 30 );
        $w = $layer['width'] ?? 'auto';
        $h = $layer['height'] ?? 'auto';

        $style = 'left:' . $x . '%;top:' . $y . '%;';
        $style .= $w !== 'auto' ? 'width:' . floatval( $w ) . '%;' : '';
        $style .= $h !== 'auto' ? 'height:' . floatval( $h ) . '%;' : '';

        // Animation data attrs
        $anim_attrs = '';
        $anim_attrs .= ' data-anim-in="' . esc_attr( $layer['animIn'] ?? 'fadeInUp' ) . '"';
        $anim_attrs .= ' data-anim-in-duration="' . absint( $layer['animInDuration'] ?? 800 ) . '"';
        $anim_attrs .= ' data-anim-in-delay="' . absint( $layer['animInDelay'] ?? 200 ) . '"';
        $anim_attrs .= ' data-anim-out="' . esc_attr( $layer['animOut'] ?? 'fadeOutDown' ) . '"';
        $anim_attrs .= ' data-anim-out-duration="' . absint( $layer['animOutDuration'] ?? 600 ) . '"';
        $anim_attrs .= ' data-anim-out-delay="' . absint( $layer['animOutDelay'] ?? 0 ) . '"';
        $anim_attrs .= ' data-anim-easing="' . esc_attr( $layer['animEasing'] ?? 'ease' ) . '"';

        ob_start();
        ?>
        <div class="olo-proslider-layer" style="<?php echo esc_attr( $style ); ?>"<?php echo $anim_attrs; ?>>
            <?php echo $this->render_layer_content( $layer, $type ); ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private function render_layer_content( $layer, $type ) {
        $font_size   = absint( $layer['fontSize'] ?? 24 ) . 'px';
        $font_weight = esc_attr( $layer['fontWeight'] ?? '700' );
        $color       = $this->safe_color( $layer['color'] ?? '#ffffff' );
        $text_align  = esc_attr( $layer['textAlign'] ?? 'left' );
        $bg_color    = $this->safe_color( $layer['bgColor'] ?? '' );
        $radius      = absint( $layer['borderRadius'] ?? 0 ) . 'px';
        $padding     = absint( $layer['padding'] ?? 0 ) . 'px';
        $opacity     = isset( $layer['opacity'] ) ? floatval( $layer['opacity'] ) / 100 : 1;

        $base_style = 'font-size:' . $font_size . ';font-weight:' . $font_weight . ';color:' . $color . ';text-align:' . $text_align . ';';
        if ( $bg_color ) {
            $base_style .= 'background:' . $bg_color . ';';
        }
        $base_style .= 'border-radius:' . $radius . ';padding:' . $padding . ';';
        if ( $opacity < 1 ) {
            $base_style .= 'opacity:' . $opacity . ';';
        }

        switch ( $type ) {
            case 'text':
                $tag     = in_array( $layer['tag'] ?? 'h2', [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'span', 'div' ], true ) ? $layer['tag'] : 'h2';
                $content = wp_kses_post( $layer['content'] ?? '' );
                return '<' . $tag . ' style="' . esc_attr( $base_style ) . 'margin:0;line-height:1.2;">' . $content . '</' . $tag . '>';

            case 'image':
                $src = esc_url( $layer['imageSrc'] ?? '' );
                if ( ! $src ) return '';
                return '<img src="' . $src . '" alt="Slide image" style="width:100%;height:100%;object-fit:cover;border-radius:' . $radius . ';" draggable="false" loading="lazy" />';

            case 'button':
                $url    = esc_url( $layer['buttonUrl'] ?? '#' );
                $target = esc_attr( $layer['buttonTarget'] ?? '_self' );
                $label  = esc_html( $layer['content'] ?? 'Button' );
                $btn_style = $base_style . 'display:inline-block;text-decoration:none;cursor:pointer;';
                return '<a href="' . $url . '" target="' . $target . '" style="' . esc_attr( $btn_style ) . '">' . $label . '</a>';

            case 'icon':
                $icon_name = esc_attr( $layer['iconName'] ?? 'star' );
                $ratio     = max( 1, round( absint( $layer['fontSize'] ?? 24 ) / 20 ) );
                return '<span style="color:' . esc_attr( $color ) . ';" uk-icon="icon: ' . $icon_name . '; ratio: ' . $ratio . '"></span>';

            case 'video':
                $video_src = $layer['videoSrc'] ?? '';
                if ( empty( $video_src ) ) return '';
                $video_style = 'width:100%;height:100%;object-fit:cover;border-radius:' . $radius . ';';
                $autoplay = ! empty( $layer['videoAutoplay'] ) ? ' autoplay' : '';
                $muted    = ! empty( $layer['videoMuted'] ) ? ' muted' : '';
                $loop     = ! empty( $layer['videoLoop'] ) ? ' loop' : '';
                if ( preg_match( '/youtube\.com|youtu\.be/i', $video_src ) ) {
                    $vid_id = $this->extract_youtube_id( $video_src );
                    if ( $vid_id ) {
                        $yt_params = 'autoplay=' . ( ! empty( $layer['videoAutoplay'] ) ? '1' : '0' );
                        $yt_params .= '&mute=' . ( ! empty( $layer['videoMuted'] ) ? '1' : '0' );
                        $yt_params .= '&loop=' . ( ! empty( $layer['videoLoop'] ) ? '1' : '0' );
                        $yt_params .= '&playlist=' . esc_attr( $vid_id ) . '&controls=0&showinfo=0&rel=0&modestbranding=1&playsinline=1';
                        return '<iframe src="https://www.youtube-nocookie.com/embed/' . esc_attr( $vid_id ) . '?' . $yt_params . '" allow="autoplay; encrypted-media" allowfullscreen loading="lazy" style="' . esc_attr( $video_style ) . 'border:0;"></iframe>';
                    }
                }
                return '<video src="' . esc_url( $video_src ) . '"' . $autoplay . $muted . $loop . ' playsinline style="' . esc_attr( $video_style ) . '"></video>';

            case 'shape':
                $shape_style = 'width:100%;height:100%;min-width:40px;min-height:40px;';
                $shape_style .= $bg_color ? 'background:' . $bg_color . ';' : 'background:#3b82f6;';
                $shape_style .= 'border-radius:' . $radius . ';';
                if ( $opacity < 1 ) {
                    $shape_style .= 'opacity:' . $opacity . ';';
                }
                return '<div style="' . esc_attr( $shape_style ) . '"></div>';

            default:
                return '';
        }
    }

    /* ─── Helpers ──────────────────────────────────────────── */

    private function extract_youtube_id( $url ) {
        if ( preg_match( '/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([^&?\s]{11})/', $url, $m ) ) {
            return $m[1];
        }
        return '';
    }
}
