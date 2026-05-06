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
                    'kenBurnsDirection' => 'in',
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
            'kenBurnsDirection' => 'in',
        ],
            'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    /**
     * Normalizza un valore altezza nel formato { mode, value }.
     * Backward compat: numeri semplici → array( 'mode' => 'px', 'value' => N )
     */
    private function normalize_height( $val ) {
        if ( is_array( $val ) && isset( $val['mode'] ) ) {
            return $val;
        }
        if ( is_numeric( $val ) ) {
            return [ 'mode' => 'px', 'value' => absint( $val ) ];
        }
        return null;
    }

    /**
     * Genera CSS inline per un valore altezza.
     * Ritorna anche il valore px approssimativo per il JS.
     */
    private function height_css( $h_obj ) {
        if ( ! $h_obj ) { return [ 'css' => 'height:600px;', 'px' => 600 ]; }
        switch ( $h_obj['mode'] ) {
            case 'vh':
                $v = absint( $h_obj['value'] );
                // px approssimativo per JS layer scaling (assume 900px viewport)
                return [ 'css' => 'height:' . $v . 'vh;', 'px' => intval( $v * 9 ) ];
            case 'ratio':
                $parts = explode( ':', $h_obj['value'] );
                $w     = floatval( $parts[0] ?? 16 );
                $hh    = floatval( $parts[1] ?? 9 );
                if ( $w <= 0 ) { $w = 16; }
                if ( $hh <= 0 ) { $hh = 9; }
                // px approssimativo basato su 1200px design width
                $px = intval( 1200 / $w * $hh );
                return [ 'css' => 'aspect-ratio:' . $w . '/' . $hh . ';height:auto;', 'px' => $px ];
            default:
                $v = absint( $h_obj['value'] ?: 600 );
                return [ 'css' => 'height:' . $v . 'px;', 'px' => $v ];
        }
    }

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $slides = is_array( $s['slides'] ) ? $s['slides'] : [];
        if ( empty( $slides ) ) {
            return '<div class="olo-proslider" style="padding:60px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);">No slides configured — open the Slider Editor</div>';
        }

        $uid        = 'mps-' . wp_unique_id();
        $h_obj      = $this->normalize_height( $s['height'] ) ?: [ 'mode' => 'px', 'value' => 600 ];
        $h_info     = $this->height_css( $h_obj );
        $height     = $h_info['px'];
        $valid_transitions = [ 'fade', 'slide', 'zoom', 'crossFade', 'slideOver', 'fadeThroughDark', 'blur', 'flipH', 'flipV', 'cubeH', 'cubeV', 'push', 'pushDown', 'stack', 'paperCut', 'zoomFade', 'rotateSlide', 'curtain3D', 'slideUp', 'slideDown' ];
        $transition = in_array( $s['transition'], $valid_transitions, true ) ? $s['transition'] : 'fade';
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
            'mouseWheel'    => ! empty( $s['mouseWheel'] ),
            'parallax'      => ! empty( $s['parallax'] ),
            'parallaxType'  => $s['parallaxType'] ?? 'mouse',
            'parallaxIntensity' => absint( $s['parallaxIntensity'] ?? 5 ),
            'scrollEffect'  => $s['scrollEffect'] ?? 'none',
            'carousel'      => ! empty( $s['carousel'] ),
            'carousel3D'    => ! empty( $s['carousel3D'] ),
            'scrollTimeline' => ! empty( $s['scrollTimeline'] ),
            'scrollTimelineDistance' => absint( $s['scrollTimelineDistance'] ?? 2000 ),
        ];

        // Sizing mode
        $sizing = $s['sizingMode'] ?? 'auto';

        // Global background
        $global_bg = isset( $s['globalBackground'] ) && is_array( $s['globalBackground'] ) ? $s['globalBackground'] : [];

        // Genera CSS @keyframes dinamici per layer con timeline
        $global_layers_arr = isset( $s['globalLayers'] ) && is_array( $s['globalLayers'] ) ? $s['globalLayers'] : [];
        $timeline_css = $this->collect_timeline_css( $slides, $height, $global_layers_arr );

        ob_start();
        $responsive_css = $this->generate_responsive_css( $uid, $s, $slides, $height );
        $all_css = $timeline_css . $responsive_css;
        // Icon SVG: custom properties per fill/stroke + evita doppia ombra con drop-shadow
        $all_css .= '.mps-icon-wrap svg polygon,.mps-icon-wrap svg path,.mps-icon-wrap svg circle,.mps-icon-wrap svg rect,.mps-icon-wrap svg ellipse,.mps-icon-wrap svg polyline,.mps-icon-wrap svg line{fill:var(--icon-fill,currentColor);stroke:var(--icon-stroke,currentColor);stroke-width:var(--icon-stroke-width,inherit);stroke-dasharray:var(--icon-stroke-dash,none)}';
        echo '<style>' . $all_css . '</style>';

        // Collect and load Google Fonts used by layers
        $google_fonts = [];
        foreach ( $slides as $sl ) {
            foreach ( ( $sl['layers'] ?? [] ) as $lay ) {
                $ff = sanitize_text_field( $lay['fontFamily'] ?? '' );
                if ( $ff ) { $google_fonts[ $ff ] = true; }
            }
        }
        foreach ( $global_layers_arr as $lay ) {
            $ff = sanitize_text_field( $lay['fontFamily'] ?? '' );
            if ( $ff ) { $google_fonts[ $ff ] = true; }
        }
        if ( $google_fonts ) {
            $families = array_map( function( $f ) { return str_replace( ' ', '+', $f ) . ':wght@300;400;500;600;700;800;900'; }, array_keys( $google_fonts ) );
            echo '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=' . esc_attr( implode( '&family=', $families ) ) . '&display=swap" />';
        }
        ?>
        <?php
        $container_classes = 'olo-proslider';
        if ( $sizing === 'fullwidth' )   { $container_classes .= ' mps-fullwidth'; }
        if ( $sizing === 'fullscreen' )  { $container_classes .= ' mps-fullscreen'; }
        $is_carousel = ! empty( $s['carousel'] );
        if ( $is_carousel ) {
            $container_classes .= ' mps-carousel';
            if ( ! empty( $s['carousel3D'] ) ) { $container_classes .= ' mps-carousel-3d'; }
        }
        $container_style = '--mps-trans-dur:' . $trans_dur . 'ms;--mps-cw:100%;--mps-ch:' . $height . 'px;';
        if ( $is_carousel ) {
            $cw   = absint( $s['carouselWidth'] ?? 80 );
            $cgap = absint( $s['carouselGap'] ?? 10 );
            $css  = floatval( $s['carouselSideScale'] ?? 0.85 );
            $container_style .= '--mps-carousel-width:' . $cw . '%;--mps-carousel-gap:' . $cgap . 'px;--mps-carousel-side-scale:' . $css . ';';
        }
        if ( $sizing === 'fullscreen' ) {
            $container_style .= 'height:100vh;';
        } else {
            $container_style .= $h_info['css'];
        }
        $scroll_fixed = ! empty( $s['scrollTimeline'] );
        $scroll_dist  = absint( $s['scrollTimelineDistance'] ?? 2000 );
        if ( $scroll_fixed ) {
            echo '<div class="mps-scroll-fixed-wrapper" style="height:' . ( $height + $scroll_dist ) . 'px;" data-scroll-distance="' . $scroll_dist . '">';
        }
        ?>
        <div
            class="<?php echo esc_attr( $container_classes ); ?>"
            id="<?php echo esc_attr( $uid ); ?>"
            data-transition="<?php echo esc_attr( $transition ); ?>"
            data-proslider="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"
            data-design-width="1200"
            data-design-height="<?php echo $height; ?>"
            data-height-mode="<?php echo esc_attr( $h_obj['mode'] ); ?>"
            data-height-value="<?php echo esc_attr( $h_obj['value'] ); ?>"
            <?php
            $resp_heights = [];
            foreach ( [ 'notebook' => 1240, 'tablet' => 1024, 'mobile' => 640 ] as $rn => $rw ) {
                $rk = 'height' . ucfirst( $rn );
                if ( ! empty( $s[ $rk ] ) ) {
                    $rh = $this->normalize_height( $s[ $rk ] );
                    if ( $rh ) {
                        // Formato: maxWidth:mode:value (es. "1240:vh:75" o "640:px:400" o "1024:ratio:16:9")
                        $resp_heights[] = $rw . ':' . $rh['mode'] . ':' . $rh['value'];
                    }
                }
            }
            if ( $resp_heights ) { echo 'data-responsive-heights="' . esc_attr( implode( ',', $resp_heights ) ) . '"'; }
            ?>
            style="<?php echo esc_attr( $container_style ); ?>"
        >
            <?php if ( ! empty( $global_bg ) && ( $global_bg['type'] ?? 'color' ) !== 'color' || ! empty( $global_bg['color'] ) ) : ?>
                <div class="olo-proslider-global-bg"><?php echo $this->render_bg( $global_bg ); ?></div>
            <?php endif; ?>
            <?php
            // Global layers (back position — behind slides)
            $global_layers = isset( $s['globalLayers'] ) && is_array( $s['globalLayers'] ) ? $s['globalLayers'] : [];
            $back_layers   = array_filter( $global_layers, function( $gl ) { return ( $gl['globalPosition'] ?? 'front' ) === 'back'; } );
            $front_layers  = array_filter( $global_layers, function( $gl ) { return ( $gl['globalPosition'] ?? 'front' ) !== 'back'; } );
            if ( $back_layers ) :
            ?>
                <div class="olo-proslider-global-layers mps-global-back mps-layers-wrap" style="position:absolute;top:0;left:0;z-index:0;pointer-events:none;">
                    <?php foreach ( $back_layers as $gl ) : echo $this->render_layer( $gl ); endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="olo-proslider-track">
                <?php foreach ( $slides as $si => $slide ) : ?>
                    <?php echo $this->render_slide( $slide, $si ); ?>
                <?php endforeach; ?>
            </div>

            <?php
            // Global layers (front position — above slides)
            if ( $front_layers ) :
            ?>
                <div class="olo-proslider-global-layers mps-global-front mps-layers-wrap" style="position:absolute;top:0;left:0;z-index:5;pointer-events:none;">
                    <?php foreach ( $front_layers as $gl ) : echo $this->render_layer( $gl ); endforeach; ?>
                </div>
            <?php endif; ?>

            <?php
            $arrow_style = isset( $s['arrowStyle'] ) && in_array( $s['arrowStyle'], [ 'minimal', 'rounded', 'boxed', 'outline' ], true ) ? $s['arrowStyle'] : 'minimal';
            if ( $s['showArrows'] && count( $slides ) > 1 ) : ?>
                <button class="olo-proslider-arrow olo-proslider-prev mps-arrow-<?php echo $arrow_style; ?>" aria-label="<?php echo esc_attr( olo_t( 'Previous' ) ); ?>"><?php echo esc_html( olo_t( '&lsaquo;' ) ); ?></button>
                <button class="olo-proslider-arrow olo-proslider-next mps-arrow-<?php echo $arrow_style; ?>" aria-label="<?php echo esc_attr( olo_t( 'Next' ) ); ?>"><?php echo esc_html( olo_t( '&rsaquo;' ) ); ?></button>
            <?php endif; ?>

            <?php
            $dot_style = isset( $s['dotStyle'] ) && in_array( $s['dotStyle'], [ 'circles', 'bars', 'numbers', 'dash' ], true ) ? $s['dotStyle'] : 'circles';
            if ( $s['showDots'] && count( $slides ) > 1 ) : ?>
                <div class="olo-proslider-dots mps-dots-<?php echo $dot_style; ?>">
                    <?php for ( $d = 0; $d < count( $slides ); $d++ ) : ?>
                        <button class="olo-proslider-dot<?php echo $d === 0 ? ' mps-dot-active' : ''; ?>" data-slide="<?php echo $d; ?>" aria-label="Slide <?php echo $d + 1; ?>"><?php echo $dot_style === 'numbers' ? ( $d + 1 ) : ''; ?></button>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $s['showProgressBar'] ) && count( $slides ) > 1 ) :
                $pb_color  = $this->safe_color_css( $s['progressBarColor'] ?? '#3b82f6' ) ?: '#3b82f6';
                $pb_height = absint( $s['progressBarHeight'] ?? 3 );
                if ( $pb_height < 1 ) { $pb_height = 3; }
            ?>
                <div class="olo-proslider-progressbar" style="height:<?php echo $pb_height; ?>px;">
                    <div class="olo-proslider-progressbar-fill" style="background:<?php echo $pb_color; ?>;"></div>
                </div>
            <?php endif; ?>
        </div>
        <?php
        // Close scroll-fixed wrapper
        if ( $scroll_fixed ) {
            echo '</div>';
        }

        // Thumbnail navigation
        if ( ! empty( $s['showThumbs'] ) && count( $slides ) > 1 ) {
            $thumb_pos   = in_array( $s['thumbPosition'] ?? 'bottom', [ 'bottom', 'top', 'left', 'right' ], true ) ? $s['thumbPosition'] : 'bottom';
            $thumb_class = 'olo-proslider-thumbs mps-thumbs-' . $thumb_pos;
            echo '<div class="' . esc_attr( $thumb_class ) . '" data-thumbs-for="' . esc_attr( $uid ) . '">';
            foreach ( $slides as $ti => $tslide ) {
                $tbg   = $tslide['background'] ?? [];
                $timg  = '';
                if ( ( $tbg['type'] ?? '' ) === 'image' && ! empty( $tbg['image'] ) ) {
                    $timg = esc_url( $tbg['image'] );
                }
                $tact = $ti === 0 ? ' mps-thumb-active' : '';
                echo '<button class="olo-proslider-thumb' . $tact . '" data-slide="' . $ti . '">';
                if ( $timg ) {
                    echo '<img src="' . $timg . '" alt="Slide ' . ( $ti + 1 ) . '" loading="lazy" draggable="false" />';
                } else {
                    echo '<span>' . ( $ti + 1 ) . '</span>';
                }
                echo '</button>';
            }
            echo '</div>';
        }

        // Tab navigation
        if ( ! empty( $s['showTabs'] ) && count( $slides ) > 1 ) {
            $tab_pos   = in_array( $s['tabPosition'] ?? 'bottom', [ 'bottom', 'top' ], true ) ? $s['tabPosition'] : 'bottom';
            $tab_class = 'olo-proslider-tabs mps-tabs-' . $tab_pos;
            echo '<div class="' . esc_attr( $tab_class ) . '" data-tabs-for="' . esc_attr( $uid ) . '">';
            foreach ( $slides as $ti => $tslide ) {
                $tlabel = ! empty( $tslide['tabLabel'] ) ? esc_html( $tslide['tabLabel'] ) : 'Slide ' . ( $ti + 1 );
                $tact   = $ti === 0 ? ' mps-tab-active' : '';
                echo '<button class="olo-proslider-tab' . $tact . '" data-slide="' . $ti . '">' . $tlabel . '</button>';
            }
            echo '</div>';
        }

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
                $ov_color   = $this->safe_color_css( $bg['overlay'] ?? '#000000' );
                $ov_opacity = isset( $bg['overlayOpacity'] ) ? floatval( $bg['overlayOpacity'] ) : 0.3;
                if ( $bg_type !== 'transparent' && $ov_opacity > 0 && $ov_color ) :
                ?>
                    <div class="olo-proslider-overlay" style="background:<?php echo $ov_color; ?>;opacity:<?php echo $ov_opacity; ?>"></div>
                <?php endif; ?>
            </div>

            <!-- Layers (scaled wrapper for proportional responsive) -->
            <?php
            $blend_layers  = [];
            $normal_layers = [];
            foreach ( $layers as $layer ) {
                $bm = $layer['blendMode'] ?? 'normal';
                if ( $bm !== 'normal' ) {
                    $blend_layers[] = $layer;
                } else {
                    $normal_layers[] = $layer;
                }
            }
            ?>
            <div class="mps-layers-wrap">
                <?php foreach ( $normal_layers as $layer ) : ?>
                    <?php echo $this->render_layer( $layer ); ?>
                <?php endforeach; ?>
            </div>
            <?php
            // Blend-mode layers fuori dal wrapper scalato (transform crea stacking context isolato)
            foreach ( $blend_layers as $layer ) :
                echo $this->render_layer( $layer, true );
            endforeach;
            ?>
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
            $kb_class = '';
            $kb_style = '';
            if ( $kb ) {
                $scale    = floatval( $bg['kenBurnsScale'] ?? 1.2 );
                $dur      = absint( $bg['kenBurnsDuration'] ?? 8000 );
                $kb_dir   = ( $bg['kenBurnsDirection'] ?? 'in' ) === 'out' ? '-out' : '';
                $pan_x    = floatval( $bg['kenBurnsPanX'] ?? 0 );
                $pan_y    = floatval( $bg['kenBurnsPanY'] ?? 0 );
                $blur_s   = floatval( $bg['kenBurnsBlurStart'] ?? 0 );
                $blur_e   = floatval( $bg['kenBurnsBlurEnd'] ?? 0 );
                $has_adv  = ( $pan_x != 0 || $pan_y != 0 || $blur_s > 0 || $blur_e > 0 );

                if ( $has_adv ) {
                    $kb_class = ' olo-proslider-kenburns-adv';
                    $kb_style = '--mps-kb-scale:' . $scale . ';--mps-kb-dur:' . $dur . 'ms;'
                              . '--mps-kb-pan-x:' . $pan_x . '%;--mps-kb-pan-y:' . $pan_y . '%;'
                              . '--mps-kb-blur-start:' . $blur_s . 'px;--mps-kb-blur-end:' . $blur_e . 'px;';
                } else {
                    $kb_class = ' olo-proslider-kenburns' . $kb_dir;
                    $kb_style = '--mps-kb-scale:' . $scale . ';--mps-kb-dur:' . $dur . 'ms;';
                }
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
            $from  = $this->safe_color_css( $bg['gradientFrom'] ?? '#1e293b' );
            $to    = $this->safe_color_css( $bg['gradientTo'] ?? '#0f172a' );
            $angle = absint( $bg['gradientAngle'] ?? 180 );
            return '<div style="width:100%;height:100%;background:linear-gradient(' . $angle . 'deg,' . $from . ',' . $to . ');"></div>';
        }

        // Color fallback
        $color = $this->safe_color_css( $bg['color'] ?? '#1e293b' );
        return '<div style="width:100%;height:100%;background:' . ( $color ?: '#1e293b' ) . ';"></div>';
    }

    /* ─── Layer ────────────────────────────────────────────── */

    private function render_layer( $layer, $outside_wrap = false ) {
        $type = $layer['type'] ?? 'text';
        $has_timeline = $this->layer_has_timeline( $layer );

        // Position style
        $x = floatval( $layer['x'] ?? 10 );
        $y = floatval( $layer['y'] ?? 30 );
        $w = $layer['width'] ?? 'auto';
        $h = $layer['height'] ?? 'auto';

        if ( $has_timeline ) {
            // Timeline layer: position gestita dal keyframe transform
            $style = 'left:0;top:0;';
        } else {
            $style = 'left:' . $x . '%;top:' . $y . '%;';
        }
        // For image layers with auto dimensions, default to 100% so the image fills the slide
        if ( $type === 'image' && $w === 'auto' && $h === 'auto' ) {
            $style .= 'width:100%;height:100%;';
        } else {
            $style .= $w !== 'auto' ? 'width:' . floatval( $w ) . '%;' : '';
            $style .= $h !== 'auto' ? 'height:' . floatval( $h ) . '%;' : '';
        }

        // Animation data attrs
        $anim_attrs = '';
        if ( $has_timeline ) {
            $tl     = $layer['timeline'];
            $lay_id = preg_replace( '/[^a-zA-Z0-9_-]/', '', $layer['id'] ?? 'layer' );
            $anim_attrs .= ' data-timeline-name="mpsKf_' . esc_attr( $lay_id ) . '"';
            $anim_attrs .= ' data-timeline-dur="' . absint( $tl['duration'] ?? 3000 ) . 'ms"';
            $anim_attrs .= ' data-timeline-delay="' . absint( $tl['delay'] ?? 0 ) . 'ms"';
            $anim_attrs .= ' data-timeline-loop="' . ( ! empty( $tl['loop'] ) ? '1' : '0' ) . '"';
        } else {
            $anim_attrs .= ' data-anim-in="' . esc_attr( $layer['animIn'] ?? 'fadeInUp' ) . '"';
            $anim_attrs .= ' data-anim-in-duration="' . absint( $layer['animInDuration'] ?? 800 ) . '"';
            $anim_attrs .= ' data-anim-in-delay="' . absint( $layer['animInDelay'] ?? 200 ) . '"';
            $anim_attrs .= ' data-anim-out="' . esc_attr( $layer['animOut'] ?? 'fadeOutDown' ) . '"';
            $anim_attrs .= ' data-anim-out-duration="' . absint( $layer['animOutDuration'] ?? 600 ) . '"';
            $anim_attrs .= ' data-anim-out-delay="' . absint( $layer['animOutDelay'] ?? 0 ) . '"';
            $anim_attrs .= ' data-anim-easing="' . esc_attr( $layer['animEasing'] ?? 'ease' ) . '"';

            // Character animation
            if ( ! empty( $layer['charAnim'] ) && is_array( $layer['charAnim'] ) ) {
                $anim_attrs .= ' data-char-anim="1"';
                $anim_attrs .= ' data-char-stagger="' . absint( $layer['charAnim']['stagger'] ?? 30 ) . '"';
            }

            // Loop animation
            $loop_key = $layer['animLoop'] ?? 'none';
            if ( $loop_key !== 'none' ) {
                $anim_attrs .= ' data-anim-loop="' . esc_attr( $loop_key ) . '"';
                $anim_attrs .= ' data-anim-loop-dur="' . absint( $layer['animLoopDuration'] ?? 3000 ) . '"';
                $anim_attrs .= ' data-anim-loop-easing="' . esc_attr( $layer['animLoopEasing'] ?? 'ease-in-out' ) . '"';
            }
        }

        // Layer unique class (used for responsive CSS + hover)
        $lay_id      = preg_replace( '/[^a-zA-Z0-9_-]/', '', $layer['id'] ?? 'layer' );
        $layer_class = ' mps-layer-' . $lay_id;

        // Hover effect CSS
        $hover_css  = '';
        if ( ! empty( $layer['hover'] ) && is_array( $layer['hover'] ) ) {
            $h         = $layer['hover'];
            $h_dur     = absint( $h['duration'] ?? 300 );
            $h_easing  = in_array( $h['easing'] ?? 'ease', ['ease','ease-in','ease-out','ease-in-out','linear'], true ) ? $h['easing'] : 'ease';
            // Transform
            $h_parts   = [];
            if ( ( $h['scale'] ?? 1 ) != 1 ) { $h_parts[] = 'scale(' . floatval( $h['scale'] ) . ')'; }
            if ( ( $h['rotation'] ?? 0 ) != 0 ) { $h_parts[] = 'rotateZ(' . floatval( $h['rotation'] ) . 'deg)'; }
            if ( ( $h['rotateX'] ?? 0 ) != 0 ) { $h_parts[] = 'rotateX(' . floatval( $h['rotateX'] ) . 'deg)'; }
            if ( ( $h['rotateY'] ?? 0 ) != 0 ) { $h_parts[] = 'rotateY(' . floatval( $h['rotateY'] ) . 'deg)'; }
            if ( ( $h['skewX'] ?? 0 ) != 0 ) { $h_parts[] = 'skewX(' . floatval( $h['skewX'] ) . 'deg)'; }
            if ( ( $h['skewY'] ?? 0 ) != 0 ) { $h_parts[] = 'skewY(' . floatval( $h['skewY'] ) . 'deg)'; }
            if ( ( $h['x'] ?? 0 ) != 0 ) { $h_parts[] = 'translateX(' . floatval( $h['x'] ) . '%)'; }
            if ( ( $h['y'] ?? 0 ) != 0 ) { $h_parts[] = 'translateY(' . floatval( $h['y'] ) . '%)'; }
            $h_transform = $h_parts ? 'transform:' . implode( ' ', $h_parts ) . ';' : '';
            // Opacity
            $h_opacity = ( $h['opacity'] ?? 100 ) != 100 ? 'opacity:' . ( floatval( $h['opacity'] ) / 100 ) . ';' : '';
            // Colors
            $h_color_css = '';
            if ( ! empty( $h['color'] ) ) { $h_color_css .= 'color:' . $this->safe_color_css( $h['color'] ) . ';'; }
            if ( ! empty( $h['bgColor'] ) ) { $h_color_css .= 'background-color:' . $this->safe_color_css( $h['bgColor'] ) . ';'; }
            if ( ! empty( $h['borderColor'] ) ) { $h_color_css .= 'border-color:' . $this->safe_color_css( $h['borderColor'] ) . ';'; }
            if ( $h['borderRadius'] ?? '' !== '' ) {
                $h_br = floatval( $h['borderRadius'] );
                if ( $h_br > 0 ) { $h_color_css .= 'border-radius:' . $h_br . 'px;'; }
            }
            // Filters
            $h_filter_parts = [];
            if ( ( $h['blur'] ?? 0 ) > 0 ) { $h_filter_parts[] = 'blur(' . floatval( $h['blur'] ) . 'px)'; }
            if ( ( $h['brightness'] ?? 100 ) != 100 ) { $h_filter_parts[] = 'brightness(' . floatval( $h['brightness'] ) . '%)'; }
            if ( ( $h['grayscale'] ?? 0 ) > 0 ) { $h_filter_parts[] = 'grayscale(' . floatval( $h['grayscale'] ) . '%)'; }
            $h_filter_css = $h_filter_parts ? 'filter:' . implode( ' ', $h_filter_parts ) . ';' : '';
            // Cursor
            $h_cursor = '';
            if ( ! empty( $h['cursor'] ) ) { $h_cursor = 'cursor:' . esc_attr( $h['cursor'] ) . ';'; }
            // Compose
            $hover_props = $h_transform . $h_opacity . $h_color_css . $h_filter_css . $h_cursor;
            if ( $hover_props ) {
                $hover_css = '.mps-layer-' . $lay_id . '{transition:all ' . $h_dur . 'ms ' . $h_easing . ';} .mps-layer-' . $lay_id . ':hover{' . $hover_props . '}';
            }
        }

        // Action data attribute
        $action_attr = '';
        if ( ! empty( $layer['action'] ) && is_array( $layer['action'] ) ) {
            $act = $layer['action'];
            if ( ( $act['type'] ?? 'none' ) !== 'none' ) {
                $action_attr = ' data-action="' . esc_attr( wp_json_encode( $act ) ) . '"';
            }
        }

        // Parallax depth data attribute
        $parallax_attr = '';
        $pdepth = absint( $layer['parallaxDepth'] ?? 0 );
        if ( $pdepth > 0 ) {
            $parallax_attr = ' data-parallax-depth="' . $pdepth . '"';
        }

        // Blend mode
        $blend = $layer['blendMode'] ?? 'normal';
        $valid_blends = [ 'normal', 'multiply', 'screen', 'overlay', 'darken', 'lighten', 'color-dodge', 'color-burn', 'hard-light', 'soft-light', 'difference', 'exclusion', 'hue', 'saturation', 'color', 'luminosity' ];
        if ( in_array( $blend, $valid_blends, true ) && $blend !== 'normal' ) {
            $style .= 'mix-blend-mode:' . $blend . ';';
        }

        // Border — supporta lati individuali
        $valid_border_styles = [ 'solid', 'dashed', 'dotted', 'double' ];
        $bs = in_array( $layer['borderStyle'] ?? 'solid', $valid_border_styles, true ) ? $layer['borderStyle'] : 'solid';
        $bc = $this->safe_color_css( $layer['borderColor'] ?? '#ffffff' ) ?: '#ffffff';
        if ( ! empty( $layer['borderWidthLinked'] ) === false && isset( $layer['borderWidthLinked'] ) && $layer['borderWidthLinked'] === false ) {
            $bwt = absint( $layer['borderWidthTop'] ?? 0 );
            $bwr = absint( $layer['borderWidthRight'] ?? 0 );
            $bwb = absint( $layer['borderWidthBottom'] ?? 0 );
            $bwl = absint( $layer['borderWidthLeft'] ?? 0 );
            if ( $bwt > 0 ) { $style .= 'border-top:' . $bwt . 'px ' . $bs . ' ' . $bc . ';'; }
            if ( $bwr > 0 ) { $style .= 'border-right:' . $bwr . 'px ' . $bs . ' ' . $bc . ';'; }
            if ( $bwb > 0 ) { $style .= 'border-bottom:' . $bwb . 'px ' . $bs . ' ' . $bc . ';'; }
            if ( $bwl > 0 ) { $style .= 'border-left:' . $bwl . 'px ' . $bs . ' ' . $bc . ';'; }
        } else {
            $bw = absint( $layer['borderWidth'] ?? 0 );
            if ( $bw > 0 ) {
                $style .= 'border:' . $bw . 'px ' . $bs . ' ' . $bc . ';';
            }
        }

        // Border radius — supporta angoli individuali
        if ( isset( $layer['borderRadiusLinked'] ) && $layer['borderRadiusLinked'] === false ) {
            $rtl = absint( $layer['borderRadiusTL'] ?? 0 );
            $rtr = absint( $layer['borderRadiusTR'] ?? 0 );
            $rbr = absint( $layer['borderRadiusBR'] ?? 0 );
            $rbl = absint( $layer['borderRadiusBL'] ?? 0 );
            if ( $rtl > 0 || $rtr > 0 || $rbr > 0 || $rbl > 0 ) {
                $style .= 'border-radius:' . $rtl . 'px ' . $rtr . 'px ' . $rbr . 'px ' . $rbl . 'px;overflow:hidden;';
            }
        } else {
            $br = absint( $layer['borderRadius'] ?? 0 );
            if ( $br > 0 ) {
                $style .= 'border-radius:' . $br . 'px;overflow:hidden;';
            }
        }

        // Background sul wrapper
        $layer_bg = $this->safe_color_css( $layer['bgColor'] ?? '' );
        if ( $type === 'shape' ) {
            if ( ! empty( $layer['shapeGradient'] ) && is_array( $layer['shapeGradient'] ) ) {
                $sg = $layer['shapeGradient'];
                $sg_angle = intval( $sg['angle'] ?? 180 );
                $sg_from = $this->safe_color_css( $sg['from'] ?? '#3b82f6' ) ?: '#3b82f6';
                $sg_to = $this->safe_color_css( $sg['to'] ?? '#8b5cf6' ) ?: '#8b5cf6';
                $style .= 'background:linear-gradient(' . $sg_angle . 'deg,' . $sg_from . ',' . $sg_to . ');';
            } else {
                $style .= 'background-color:' . ( $layer_bg ?: '#3b82f6' ) . ';';
            }
        } elseif ( $type === 'button' ) {
            $style .= 'background-color:' . ( $layer_bg ?: '#2563eb' ) . ';';
        } elseif ( $type === 'text' || $type === 'icon' ) {
            if ( $layer_bg ) { $style .= 'background-color:' . $layer_bg . ';'; }
        }

        // Padding — supporta lati individuali
        if ( ! in_array( $type, [ 'image', 'video' ], true ) ) {
            if ( isset( $layer['paddingLinked'] ) && $layer['paddingLinked'] === false ) {
                $pt = absint( $layer['paddingTop'] ?? 0 );
                $pr = absint( $layer['paddingRight'] ?? 0 );
                $pb = absint( $layer['paddingBottom'] ?? 0 );
                $pl = absint( $layer['paddingLeft'] ?? 0 );
                if ( $pt > 0 || $pr > 0 || $pb > 0 || $pl > 0 ) {
                    $style .= 'padding:' . $pt . 'px ' . $pr . 'px ' . $pb . 'px ' . $pl . 'px;';
                }
            } else {
                $layer_pad = absint( $layer['padding'] ?? 0 );
                if ( $layer_pad > 0 ) {
                    if ( $type === 'button' ) {
                        $style .= 'padding:' . $layer_pad . 'px ' . ( $layer_pad * 2 ) . 'px;';
                    } else {
                        $style .= 'padding:' . $layer_pad . 'px;';
                    }
                }
            }
        }

        // Box shadow — drop-shadow per icone trasparenti (segue forma SVG),
        // box-shadow per tutti gli altri (segue border-radius del div)
        if ( ! empty( $layer['boxShadow'] ) && is_array( $layer['boxShadow'] ) ) {
            $sh = $layer['boxShadow'];
            $sh_x = intval( $sh['x'] ?? 0 );
            $sh_y = intval( $sh['y'] ?? 4 );
            $sh_blur = absint( $sh['blur'] ?? 10 );
            $sh_spread = intval( $sh['spread'] ?? 0 );
            $sh_color = $this->safe_color_css( $sh['color'] ?? 'rgba(0,0,0,0.3)' ) ?: 'rgba(0,0,0,0.3)';
            $is_transparent_icon = ( $type === 'icon' && empty( $layer['bgColor'] ) );
            if ( $is_transparent_icon ) {
                $style .= 'filter:drop-shadow(' . $sh_x . 'px ' . $sh_y . 'px ' . $sh_blur . 'px ' . $sh_color . ');';
            } else {
                $style .= 'box-shadow:' . $sh_x . 'px ' . $sh_y . 'px ' . $sh_blur . 'px ' . $sh_spread . 'px ' . $sh_color . ';';
            }
        }

        // CSS filters (image/video)
        if ( in_array( $type, [ 'image', 'video' ], true ) ) {
            $filter_parts = [];
            $fb = floatval( $layer['filterBrightness'] ?? 100 );
            $fc = floatval( $layer['filterContrast'] ?? 100 );
            $fs = floatval( $layer['filterSaturate'] ?? 100 );
            $fg = floatval( $layer['filterGrayscale'] ?? 0 );
            $fh = floatval( $layer['filterHueRotate'] ?? 0 );
            $fbl = floatval( $layer['filterBlur'] ?? 0 );
            $fse = floatval( $layer['filterSepia'] ?? 0 );
            $fin = floatval( $layer['filterInvert'] ?? 0 );
            if ( $fb != 100 ) { $filter_parts[] = 'brightness(' . $fb . '%)'; }
            if ( $fc != 100 ) { $filter_parts[] = 'contrast(' . $fc . '%)'; }
            if ( $fs != 100 ) { $filter_parts[] = 'saturate(' . $fs . '%)'; }
            if ( $fg > 0 ) { $filter_parts[] = 'grayscale(' . $fg . '%)'; }
            if ( $fh > 0 ) { $filter_parts[] = 'hue-rotate(' . $fh . 'deg)'; }
            if ( $fbl > 0 ) { $filter_parts[] = 'blur(' . $fbl . 'px)'; }
            if ( $fse > 0 ) { $filter_parts[] = 'sepia(' . $fse . '%)'; }
            if ( $fin > 0 ) { $filter_parts[] = 'invert(' . $fin . '%)'; }
            if ( $filter_parts ) {
                $style .= 'filter:' . implode( ' ', $filter_parts ) . ';';
            }
        }

        // Backdrop filter (glassmorphism - tutti i tipi)
        $bd_parts = [];
        $bd_blur = floatval( $layer['backdropBlur'] ?? 0 );
        $bd_bright = floatval( $layer['backdropBrightness'] ?? 100 );
        $bd_gray = floatval( $layer['backdropGrayscale'] ?? 0 );
        if ( $bd_blur > 0 ) { $bd_parts[] = 'blur(' . $bd_blur . 'px)'; }
        if ( $bd_bright != 100 ) { $bd_parts[] = 'brightness(' . $bd_bright . '%)'; }
        if ( $bd_gray > 0 ) { $bd_parts[] = 'grayscale(' . $bd_gray . '%)'; }
        if ( $bd_parts ) {
            $style .= 'backdrop-filter:' . implode( ' ', $bd_parts ) . ';-webkit-backdrop-filter:' . implode( ' ', $bd_parts ) . ';';
        }

        // Cursor
        $cursor = $layer['cursor'] ?? 'auto';
        if ( $cursor !== 'auto' ) {
            $style .= 'cursor:' . esc_attr( $cursor ) . ';';
        }

        // SFX block reveal
        $sfx_class = '';
        $sfx_style = '';
        if ( ! empty( $layer['sfx'] ) && is_array( $layer['sfx'] ) ) {
            $sfx        = $layer['sfx'];
            $sfx_effect = $sfx['effect'] ?? 'blockRight';
            $sfx_color  = $this->safe_color_css( $sfx['color'] ?? '#ffffff' ) ?: '#ffffff';
            $sfx_dur    = absint( $sfx['duration'] ?? 800 );
            $sfx_class  = ' mps-sfx-block';
            $sfx_style  = '--mps-sfx-color:' . $sfx_color . ';--mps-sfx-dur:' . $sfx_dur . 'ms;--mps-sfx-effect:' . esc_attr( $sfx_effect ) . ';';
            $style .= $sfx_style;
        }

        ob_start();
        if ( $hover_css ) {
            echo '<style>' . $hover_css . '</style>';
        }
        ?>
        <?php
        $custom_class = sanitize_html_class( $layer['customClass'] ?? '' );
        $custom_id = sanitize_html_class( $layer['customId'] ?? '' );
        $custom_css_inline = sanitize_text_field( $layer['customCSS'] ?? '' );
        if ( $custom_css_inline ) { $style .= $custom_css_inline; }
        ?>
        <div class="olo-proslider-layer<?php echo esc_attr( $layer_class . $sfx_class . ( $custom_class ? ' ' . $custom_class : '' ) . ( $outside_wrap ? ' mps-blend-layer' : '' ) ); ?>"<?php echo $custom_id ? ' id="' . esc_attr( $custom_id ) . '"' : ''; ?> style="<?php echo esc_attr( $style ); ?>"<?php echo $anim_attrs . $action_attr . $parallax_attr; ?><?php echo $sfx_class ? ' data-sfx-effect="' . esc_attr( $layer['sfx']['effect'] ?? 'blockRight' ) . '"' : ''; ?>>
            <?php echo $this->render_layer_content( $layer, $type ); ?>
        </div>
        <?php
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

    private function render_layer_content( $layer, $type ) {
        $font_size   = absint( $layer['fontSize'] ?? 24 ) . 'px';
        $font_weight = esc_attr( $layer['fontWeight'] ?? '700' );
        $font_style  = esc_attr( $layer['fontStyle'] ?? 'normal' );
        $color       = $this->safe_color_css( $layer['color'] ?? '#ffffff' );
        $text_align  = esc_attr( $layer['textAlign'] ?? 'left' );
        $opacity     = isset( $layer['opacity'] ) ? floatval( $layer['opacity'] ) / 100 : 1;

        // Tipografia avanzata
        $line_height    = floatval( $layer['lineHeight'] ?? 1.2 );
        $letter_spacing = floatval( $layer['letterSpacing'] ?? 0 );
        $text_transform = esc_attr( $layer['textTransform'] ?? 'none' );
        $text_decoration = esc_attr( $layer['textDecoration'] ?? 'none' );
        $font_family    = sanitize_text_field( $layer['fontFamily'] ?? '' );
        $text_shadow_css = '';
        if ( ! empty( $layer['textShadow'] ) && is_array( $layer['textShadow'] ) ) {
            $ts = $layer['textShadow'];
            $ts_color = $this->safe_color_css( $ts['color'] ?? '#000000' ) ?: '#000000';
            $text_shadow_css = 'text-shadow:' . intval( $ts['x'] ?? 2 ) . 'px ' . intval( $ts['y'] ?? 2 ) . 'px ' . absint( $ts['blur'] ?? 4 ) . 'px ' . $ts_color . ';';
        }
        // Text stroke
        $text_stroke_css = '';
        $tsw = floatval( $layer['textStrokeWidth'] ?? 0 );
        if ( $tsw > 0 ) {
            $tsc = $this->safe_color_css( $layer['textStrokeColor'] ?? '#000000' ) ?: '#000000';
            $text_stroke_css = '-webkit-text-stroke:' . $tsw . 'px ' . $tsc . ';';
        }

        $base_style = 'font-size:' . $font_size . ';font-weight:' . $font_weight . ';color:' . $color . ';text-align:' . $text_align . ';';
        if ( $font_style !== 'normal' ) {
            $base_style .= 'font-style:' . $font_style . ';';
        }
        if ( $font_family ) {
            $base_style .= "font-family:'" . $font_family . "',sans-serif;";
        }
        if ( $line_height != 1.2 ) {
            $base_style .= 'line-height:' . $line_height . ';';
        }
        if ( $letter_spacing != 0 ) {
            $base_style .= 'letter-spacing:' . $letter_spacing . 'px;';
        }
        if ( $text_transform !== 'none' ) {
            $base_style .= 'text-transform:' . $text_transform . ';';
        }
        if ( $text_decoration !== 'none' ) {
            $base_style .= 'text-decoration:' . $text_decoration . ';';
        }
        $base_style .= $text_shadow_css;
        $base_style .= $text_stroke_css;
        // bg, border-radius e padding sono ora sul wrapper (render_layer) per shadow/border
        if ( $opacity < 1 ) {
            $base_style .= 'opacity:' . $opacity . ';';
        }

        switch ( $type ) {
            case 'text':
                $tag     = in_array( $layer['tag'] ?? 'h2', [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'span', 'div' ], true ) ? $layer['tag'] : 'h2';
                $content = wp_kses_post( $layer['content'] ?? '' );

                // Character animation: split text into spans
                if ( ! empty( $layer['charAnim'] ) && is_array( $layer['charAnim'] ) ) {
                    $content = $this->split_text_for_animation( $content, $layer['charAnim'] );
                }

                $lh = $line_height != 1.2 ? $line_height : 1.2;
                $text_extra = '';
                if ( ! empty( $layer['selectableText'] ) ) {
                    $text_extra .= 'user-select:text;-webkit-user-select:text;';
                }
                return '<' . $tag . ' style="' . esc_attr( $base_style . $text_extra ) . 'margin:0;line-height:' . $lh . ';">' . $content . '</' . $tag . '>';

            case 'image':
                $src = esc_url( $layer['imageSrc'] ?? '' );
                if ( ! $src ) return '';
                $obj_fit = esc_attr( $layer['objectFit'] ?? 'cover' );
                $obj_pos = esc_attr( $layer['objectPosition'] ?? 'center' );
                return '<img src="' . $src . '" alt="Slide image" style="width:100%;height:100%;object-fit:' . $obj_fit . ';object-position:' . $obj_pos . ';" draggable="false" loading="lazy" />';

            case 'button':
                $url    = esc_url( $layer['buttonUrl'] ?? '#' );
                $target = esc_attr( $layer['buttonTarget'] ?? '_self' );
                $label  = esc_html( $layer['content'] ?? 'Button' );
                $btn_style = $base_style . 'display:inline-block;text-decoration:none;cursor:pointer;';
                return '<a href="' . $url . '" target="' . $target . '" style="' . esc_attr( $btn_style ) . '">' . $label . '</a>';

            case 'icon':
                $icon_name = esc_attr( $layer['iconName'] ?? 'star' );
                $ratio     = max( 1, round( absint( $layer['fontSize'] ?? 24 ) / 20 ) );
                $icon_style = 'color:' . $color . ';display:inline-flex;align-items:center;justify-content:center;';
                // SVG icon custom properties
                $icon_fill = sanitize_text_field( $layer['iconFillColor'] ?? '' );
                $icon_stroke = sanitize_text_field( $layer['iconStrokeColor'] ?? '' );
                $icon_sw = floatval( $layer['iconStrokeWidth'] ?? 0 );
                $icon_sd = floatval( $layer['iconStrokeDash'] ?? 0 );
                if ( $icon_fill ) {
                    $icon_style .= '--icon-fill:' . $this->safe_color_css( $icon_fill ) . ';';
                }
                if ( $icon_stroke ) {
                    $icon_style .= '--icon-stroke:' . $this->safe_color_css( $icon_stroke ) . ';';
                }
                if ( $icon_sw > 0 ) {
                    $icon_style .= '--icon-stroke-width:' . $icon_sw . ';';
                }
                if ( $icon_sd > 0 ) {
                    $icon_style .= '--icon-stroke-dash:' . $icon_sd . ';';
                }
                // bgColor, padding, borderRadius sono ora sul wrapper (render_layer)
                return '<span class="mps-icon-wrap" style="' . esc_attr( $icon_style ) . '" uk-icon="icon: ' . $icon_name . '; ratio: ' . $ratio . '"></span>';

            case 'video':
                $video_src = $layer['videoSrc'] ?? '';
                if ( empty( $video_src ) ) return '';
                $video_style = 'width:100%;height:100%;object-fit:cover;';
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
                // bg, border-radius e gradient sono ora sul wrapper (render_layer)
                $shape_style = 'width:100%;height:100%;min-width:40px;min-height:40px;';
                if ( $opacity < 1 ) {
                    $shape_style .= 'opacity:' . $opacity . ';';
                }
                return '<div style="' . esc_attr( $shape_style ) . '"></div>';

            case 'audio':
                $audio_src = $layer['audioSrc'] ?? '';
                if ( empty( $audio_src ) ) return '';
                $a_autoplay = ! empty( $layer['audioAutoplay'] ) ? ' autoplay' : '';
                $a_loop     = ! empty( $layer['audioLoop'] ) ? ' loop' : '';
                return '<audio src="' . esc_url( $audio_src ) . '"' . $a_autoplay . $a_loop . ' preload="auto" style="display:none;"></audio>';

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

    /* ─── Timeline Keyframe CSS Generation ─────────────────── */

    private function layer_has_timeline( $layer ) {
        return ! empty( $layer['timeline'] )
            && is_array( $layer['timeline'] )
            && ! empty( $layer['timeline']['keyframes'] )
            && is_array( $layer['timeline']['keyframes'] )
            && count( $layer['timeline']['keyframes'] ) >= 2;
    }

    private function collect_timeline_css( $slides, $slider_height, $global_layers = [] ) {
        $css = '';
        foreach ( $slides as $slide ) {
            if ( empty( $slide['layers'] ) || ! is_array( $slide['layers'] ) ) continue;
            foreach ( $slide['layers'] as $layer ) {
                if ( $this->layer_has_timeline( $layer ) ) {
                    $css .= $this->generate_timeline_css( $layer, $slider_height );
                }
            }
        }
        // Global layers timeline
        foreach ( $global_layers as $layer ) {
            if ( $this->layer_has_timeline( $layer ) ) {
                $css .= $this->generate_timeline_css( $layer, $slider_height );
            }
        }
        return $css;
    }

    private function generate_timeline_css( $layer, $slider_height ) {
        $tl       = $layer['timeline'];
        $kfs      = $tl['keyframes'];
        $duration = max( 1, absint( $tl['duration'] ?? 3000 ) );
        $lay_id   = preg_replace( '/[^a-zA-Z0-9_-]/', '', $layer['id'] ?? 'layer' );
        $name     = 'mpsKf_' . $lay_id;

        // Sort keyframes by time
        usort( $kfs, function( $a, $b ) {
            return ( $a['time'] ?? 0 ) - ( $b['time'] ?? 0 );
        } );

        $css = '@keyframes ' . $name . " {\n";

        foreach ( $kfs as $kf ) {
            $time = max( 0, min( $duration, absint( $kf['time'] ?? 0 ) ) );
            $pct  = round( ( $time / $duration ) * 100, 2 );
            $p    = isset( $kf['props'] ) && is_array( $kf['props'] ) ? $kf['props'] : [];

            $px  = floatval( $p['x'] ?? 0 );
            $py  = floatval( $p['y'] ?? 0 );
            $s   = floatval( $p['scale'] ?? 1 );
            $r   = floatval( $p['rotation'] ?? 0 );
            $rx  = floatval( $p['rotationX'] ?? 0 );
            $ry  = floatval( $p['rotationY'] ?? 0 );
            $skx = floatval( $p['skewX'] ?? 0 );
            $sky = floatval( $p['skewY'] ?? 0 );
            $o   = floatval( $p['opacity'] ?? 100 ) / 100;
            $b   = floatval( $p['blur'] ?? 0 );
            $ox  = floatval( $p['originX'] ?? 50 );
            $oy  = floatval( $p['originY'] ?? 50 );

            // Build transform (no translate — x/y use left/top for container-relative %)
            $transform = '';
            if ( $rx != 0 || $ry != 0 ) { $transform .= 'perspective(800px) '; }
            $transform .= "scale({$s}) rotate({$r}deg)";
            if ( $rx != 0 ) { $transform .= " rotateX({$rx}deg)"; }
            if ( $ry != 0 ) { $transform .= " rotateY({$ry}deg)"; }
            if ( $skx != 0 ) { $transform .= " skewX({$skx}deg)"; }
            if ( $sky != 0 ) { $transform .= " skewY({$sky}deg)"; }

            $css .= "  {$pct}% {\n";
            $css .= "    left: {$px}%;\n";
            $css .= "    top: {$py}%;\n";
            $css .= "    transform: {$transform};\n";
            $css .= "    transform-origin: {$ox}% {$oy}%;\n";
            $css .= "    opacity: {$o};\n";
            $css .= $b > 0 ? "    filter: blur({$b}px);\n" : "    filter: none;\n";

            // Per-segment easing
            $easing = $kf['easing'] ?? 'ease';
            if ( $easing && $easing !== 'ease' ) {
                $css .= "    animation-timing-function: " . $this->easing_to_css( $easing ) . ";\n";
            }

            $css .= "  }\n";
        }

        $css .= "}\n";
        return $css;
    }

    /**
     * Split text into individual spans for character/word animation.
     */
    private function split_text_for_animation( $text, $char_anim ) {
        $split_mode = $char_anim['split'] ?? 'chars';
        $direction  = $char_anim['direction'] ?? 'forward';
        $stagger    = absint( $char_anim['stagger'] ?? 30 );

        // Strip HTML tags for splitting, preserve plain text
        $plain = wp_strip_all_tags( $text );
        if ( empty( $plain ) ) return $text;

        if ( $split_mode === 'words' ) {
            $units = preg_split( '/(\s+)/', $plain, -1, PREG_SPLIT_DELIM_CAPTURE );
        } elseif ( $split_mode === 'lines' ) {
            $units = explode( "\n", $plain );
        } else {
            // chars: split into individual characters preserving spaces
            $units = preg_split( '//u', $plain, -1, PREG_SPLIT_NO_EMPTY );
        }

        $total = count( $units );
        if ( $total === 0 ) return $text;

        // Build index order based on direction
        $indices = range( 0, $total - 1 );
        switch ( $direction ) {
            case 'backward':
                $indices = array_reverse( $indices );
                break;
            case 'random':
                shuffle( $indices );
                break;
            case 'middletoedge':
                $mid = intval( $total / 2 );
                $ordered = [];
                for ( $i = 0; $i <= $mid; $i++ ) {
                    if ( $mid + $i < $total ) $ordered[] = $mid + $i;
                    if ( $mid - $i >= 0 && $mid - $i !== $mid + $i ) $ordered[] = $mid - $i;
                }
                $indices = $ordered;
                break;
        }

        // Map original index → delay order
        $delay_map = array_fill( 0, $total, 0 );
        foreach ( $indices as $order => $original_idx ) {
            $delay_map[ $original_idx ] = $order;
        }

        $html = '';
        foreach ( $units as $idx => $unit ) {
            if ( $unit === ' ' || ( $split_mode === 'words' && preg_match( '/^\s+$/', $unit ) ) ) {
                $html .= $unit;
                continue;
            }
            $delay_ms = $delay_map[ $idx ] * $stagger;
            $html .= '<span class="mps-char" style="display:inline-block;animation-delay:' . $delay_ms . 'ms">' . esc_html( $unit ) . '</span>';
        }

        return $html;
    }

    /**
     * Mappa nome easing (GSAP-style) al valore CSS corrispondente.
     */
    private function easing_to_css( $name ) {
        static $map = [
            'linear'      => 'linear',
            'ease'        => 'ease',
            'ease-in'     => 'ease-in',
            'ease-out'    => 'ease-out',
            'ease-in-out' => 'ease-in-out',
            'power1.in'    => 'cubic-bezier(0.55, 0.085, 0.68, 0.53)',
            'power1.out'   => 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
            'power1.inOut' => 'cubic-bezier(0.455, 0.03, 0.515, 0.955)',
            'power2.in'    => 'cubic-bezier(0.55, 0.055, 0.675, 0.19)',
            'power2.out'   => 'cubic-bezier(0.215, 0.61, 0.355, 1)',
            'power2.inOut' => 'cubic-bezier(0.645, 0.045, 0.355, 1)',
            'power3.in'    => 'cubic-bezier(0.895, 0.03, 0.685, 0.22)',
            'power3.out'   => 'cubic-bezier(0.165, 0.84, 0.44, 1)',
            'power3.inOut' => 'cubic-bezier(0.77, 0, 0.175, 1)',
            'power4.in'    => 'cubic-bezier(0.755, 0.05, 0.855, 0.06)',
            'power4.out'   => 'cubic-bezier(0.23, 1, 0.32, 1)',
            'power4.inOut' => 'cubic-bezier(0.86, 0, 0.07, 1)',
            'sine.in'      => 'cubic-bezier(0.47, 0, 0.745, 0.715)',
            'sine.out'     => 'cubic-bezier(0.39, 0.575, 0.565, 1)',
            'sine.inOut'   => 'cubic-bezier(0.445, 0.05, 0.55, 0.95)',
            'expo.in'      => 'cubic-bezier(0.95, 0.05, 0.795, 0.035)',
            'expo.out'     => 'cubic-bezier(0.19, 1, 0.22, 1)',
            'expo.inOut'   => 'cubic-bezier(1, 0, 0, 1)',
            'circ.in'      => 'cubic-bezier(0.6, 0.04, 0.98, 0.335)',
            'circ.out'     => 'cubic-bezier(0.075, 0.82, 0.165, 1)',
            'circ.inOut'   => 'cubic-bezier(0.785, 0.135, 0.15, 0.86)',
            'back.in'      => 'cubic-bezier(0.6, -0.28, 0.735, 0.045)',
            'back.out'     => 'cubic-bezier(0.175, 0.885, 0.32, 1.275)',
            'back.inOut'   => 'cubic-bezier(0.68, -0.55, 0.265, 1.55)',
            'elastic.out'  => 'cubic-bezier(0.175, 0.885, 0.32, 1.275)',
            'bounce.out'   => 'cubic-bezier(0.34, 1.56, 0.64, 1)',
        ];
        return $map[ $name ] ?? 'ease';
    }

    /* ─── Responsive Media Queries ───────────────────────── */

    private function generate_responsive_css( $uid, $settings, $slides, $desktop_height ) {
        $breakpoints = [
            'notebook' => 1240,
            'tablet'   => 1024,
            'mobile'   => 640,
        ];
        $css = '';

        foreach ( $breakpoints as $bp_name => $max_width ) {
            $rules = '';

            // Height override per breakpoint
            $height_key = 'height' . ucfirst( $bp_name );
            if ( ! empty( $settings[ $height_key ] ) ) {
                $rh = $this->normalize_height( $settings[ $height_key ] );
                if ( $rh ) {
                    $rh_info = $this->height_css( $rh );
                    $h_css = '';
                    switch ( $rh['mode'] ) {
                        case 'vh':
                            $h_css = 'height:' . absint( $rh['value'] ) . 'vh!important;';
                            break;
                        case 'ratio':
                            $rp = explode( ':', $rh['value'] );
                            $h_css = 'aspect-ratio:' . floatval( $rp[0] ) . '/' . floatval( $rp[1] ) . '!important;height:auto!important;';
                            break;
                        default:
                            $h_css = 'height:' . $rh_info['px'] . 'px!important;';
                    }
                    $rules .= '#' . $uid . '{' . $h_css . '--mps-ch:' . $rh_info['px'] . 'px;}';
                }
            }

            // Layer responsive overrides
            foreach ( $slides as $slide ) {
                if ( empty( $slide['layers'] ) || ! is_array( $slide['layers'] ) ) continue;
                foreach ( $slide['layers'] as $layer ) {
                    $resp = $layer['responsive'] ?? [];
                    $ov   = $resp[ $bp_name ] ?? null;
                    if ( ! $ov || ! is_array( $ov ) ) continue;

                    $layer_id = $layer['id'] ?? '';
                    if ( ! $layer_id ) continue;

                    $props = '';

                    // Visibility
                    if ( isset( $ov['visible'] ) && $ov['visible'] === false ) {
                        $props .= 'display:none!important;';
                    } else {
                        if ( isset( $ov['x'] ) ) {
                            $props .= 'left:' . floatval( $ov['x'] ) . '%;';
                        }
                        if ( isset( $ov['y'] ) ) {
                            $props .= 'top:' . floatval( $ov['y'] ) . '%;';
                        }
                        if ( isset( $ov['width'] ) ) {
                            if ( $ov['width'] === 'auto' ) {
                                $props .= 'width:auto;';
                            } else {
                                $props .= 'width:' . floatval( $ov['width'] ) . '%;';
                            }
                        }
                        if ( isset( $ov['height'] ) ) {
                            if ( $ov['height'] === 'auto' ) {
                                $props .= 'height:auto;';
                            } else {
                                $props .= 'height:' . floatval( $ov['height'] ) . '%;';
                            }
                        }
                        if ( isset( $ov['fontSize'] ) ) {
                            $props .= 'font-size:' . absint( $ov['fontSize'] ) . 'px!important;';
                        }
                    }

                    if ( $props ) {
                        $rules .= '#' . $uid . ' .mps-layer-' . esc_attr( $layer_id ) . '{' . $props . '}';
                    }
                }
            }

            if ( $rules ) {
                $css .= '@media(max-width:' . $max_width . 'px){' . $rules . '}';
            }
        }

        return $css;
    }
}
