<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_ProGallery_Tile extends Olo_Tile_Base {

    protected $type     = 'progallery';
    protected $name     = 'Pro Gallery';
    protected $icon     = 'dashicons-images-alt2';
    protected $category = 'media';

    protected $defaults = [
        'images'              => [],
        'layout'              => 'grid',
        'puzzle_style'        => 'classic',
        'columns'             => 3,
        'gap'                 => 8,
        'img_height'          => '250px',
        'object_fit'          => 'cover',
        'thumb_radius'        => 8,
        'rows'                => 0,
        'mobile_columns'      => 2,
        'entrance'            => 'none',
        'entrance_stagger'    => 120,
        'entrance_duration'   => 600,
        'hover_effect'        => 'zoom',
        'hover_zoom_scale'    => 1.08,
        'hover_tilt_angle'    => 10,
        'hover_magnetic_strength' => 24,
        'hover_caption'       => 'none',
        'hover_caption_bg'    => 'rgba(0,0,0,0.6)',
        'hover_caption_color' => '#ffffff',
        'continuous'          => 'none',
        'continuous_speed'    => 20,
        'filter'              => 'none',
        'filter_hover_restore' => false,
        'duotone_dark'        => '#1a1a2e',
        'duotone_light'       => '#e94560',
        'frame'               => 'none',
        'frame_color'         => '#ffffff',
        'lightbox'            => true,
        'lightbox_animation'  => 'slide',
        'show_caption'        => false,
        'more_bg'             => 'rgba(0,0,0,0.55)',
        'more_color'          => '#ffffff',
        'more_size'           => 28,
        'shadow'              => 'none',
        'border_width'        => '0',
        'border_color'        => '#e5e7eb',
        'border_radius'       => [ 'tl' => 0, 'tr' => 0, 'br' => 0, 'bl' => 0 ],
    ];

    public function get_controls() {
        return [];
    }

    private function seeded_random( $seed ) {
        $x = sin( $seed * 127.1 + 311.7 ) * 43758.5453123;
        return $x - floor( $x );
    }

    private static $script_output = false;

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $images = is_array( $s['images'] ) ? $s['images'] : [];
        $images = array_filter( $images, function( $img ) {
            return is_array( $img ) ? ! empty( $img['url'] ) : ! empty( $img );
        });
        $images = array_values( $images );

        if ( empty( $images ) ) {
            return '<div style="padding:40px;text-align:center;color:#9ca3af">Aggiungi immagini alla Pro Gallery</div>';
        }

        $layout       = $s['layout'] ?: 'grid';
        $cols         = max( 2, min( 6, absint( $s['columns'] ) ) );
        $gap          = max( 0, min( 24, absint( $s['gap'] ) ) );
        $radius       = max( 0, min( 32, absint( $s['thumb_radius'] ) ) );
        $img_height   = esc_attr( $s['img_height'] ?: '250px' );
        $object_fit   = esc_attr( $s['object_fit'] ?: 'cover' );
        $rows         = absint( $s['rows'] );
        $mob_cols     = max( 1, min( 4, absint( $s['mobile_columns'] ) ) );
        $uid          = 'olo-pg-' . wp_unique_id();

        // Visible images
        $total       = count( $images );
        $max_visible = ( $rows > 0 ) ? $cols * $rows : $total;
        $extra       = max( 0, $total - $max_visible );

        // Entrance
        $entrance    = $s['entrance'] ?: 'none';
        $ent_stagger = max( 80, min( 400, absint( $s['entrance_stagger'] ) ) );
        $ent_dur     = max( 300, min( 1200, absint( $s['entrance_duration'] ) ) );

        // Hover
        $hover       = $s['hover_effect'] ?: 'none';
        $hz_scale    = max( 1.05, min( 1.30, floatval( $s['hover_zoom_scale'] ) ) );
        $tilt_angle  = max( 5, min( 20, absint( $s['hover_tilt_angle'] ) ) );
        $mag_str     = max( 8, min( 60, absint( $s['hover_magnetic_strength'] ) ) );
        $hcaption    = $s['hover_caption'] ?: 'none';
        $hcap_bg     = $this->safe_color( $s['hover_caption_bg'] ) ?: 'rgba(0,0,0,0.6)';
        $hcap_color  = $this->safe_color( $s['hover_caption_color'] ) ?: '#ffffff';

        // Continuous
        $continuous  = $s['continuous'] ?: 'none';
        $cont_speed  = max( 10, min( 40, absint( $s['continuous_speed'] ) ) );

        // Filter
        $filter      = $s['filter'] ?: 'none';
        $filter_hover = ! empty( $s['filter_hover_restore'] );
        $duo_dark    = $this->safe_color( $s['duotone_dark'] ) ?: '#1a1a2e';
        $duo_light   = $this->safe_color( $s['duotone_light'] ) ?: '#e94560';

        // Frame
        $frame       = $s['frame'] ?: 'none';
        $frame_color = $this->safe_color( $s['frame_color'] ) ?: '#ffffff';

        // Lightbox
        $lightbox    = ! empty( $s['lightbox'] );
        $lb_anim     = esc_attr( $s['lightbox_animation'] ?? 'slide' );
        $show_cap    = ! empty( $s['show_caption'] );

        // +N
        $more_bg     = esc_attr( $s['more_bg'] ?: 'rgba(0,0,0,0.55)' );
        $more_color  = esc_attr( $s['more_color'] ?: '#ffffff' );
        $more_size   = max( 16, min( 48, absint( $s['more_size'] ) ) );

        // Shadow / border
        $shadow_map = [
            'none' => 'none', 'sm' => '0 1px 3px rgba(0,0,0,.12)',
            'md' => '0 4px 12px rgba(0,0,0,.12)', 'lg' => '0 10px 30px rgba(0,0,0,.18)',
            'xl' => '0 20px 50px rgba(0,0,0,.25)',
        ];
        $shadow = $shadow_map[ $s['shadow'] ] ?? 'none';
        $bw     = max( 0, min( 10, absint( $s['border_width'] ) ) );
        $bc     = $this->safe_color( $s['border_color'] ) ?: '#e5e7eb';

        // CSS filter map
        $css_filter = '';
        $filter_map = [
            'grayscale'     => 'grayscale(100%)',
            'sepia'         => 'sepia(80%)',
            'high-contrast' => 'contrast(140%) saturate(120%)',
            'warm'          => 'sepia(25%) saturate(130%) hue-rotate(-10deg)',
            'cool'          => 'saturate(80%) hue-rotate(20deg) brightness(105%)',
            'vintage'       => 'sepia(40%) contrast(90%) brightness(95%) saturate(80%)',
            'duotone'       => 'grayscale(100%) contrast(110%)',
        ];
        if ( $filter !== 'none' && isset( $filter_map[ $filter ] ) ) {
            $css_filter = $filter_map[ $filter ];
        }

        ob_start();

        // ─── STYLE ───
        echo '<style>';

        // Base layout
        if ( $layout === 'masonry' ) {
            echo ".{$uid}{column-count:{$cols};column-gap:{$gap}px}";
            echo ".{$uid} .olo-pg-item{break-inside:avoid;margin-bottom:{$gap}px;position:relative;overflow:hidden;border-radius:{$radius}px}";
            echo ".{$uid} .olo-pg-item img{width:100%;display:block;object-fit:cover}";
        } elseif ( $layout === 'scattered' ) {
            echo ".{$uid}{position:relative;min-height:400px;overflow:visible}";
            echo ".{$uid} .olo-pg-item{position:absolute;overflow:hidden;border-radius:{$radius}px;box-shadow:0 4px 12px rgba(0,0,0,.15)}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block}";
        } elseif ( $layout === 'filmstrip' ) {
            echo ".{$uid}{display:flex;gap:{$gap}px;overflow-x:auto;scroll-behavior:smooth;-webkit-overflow-scrolling:touch;padding-bottom:4px}";
            echo ".{$uid} .olo-pg-item{flex:0 0 auto;width:280px;height:{$img_height};position:relative;overflow:hidden;border-radius:{$radius}px}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block}";
        } elseif ( $layout === 'honeycomb' ) {
            echo ".{$uid}{display:flex;flex-wrap:wrap;gap:{$gap}px;justify-content:center}";
            echo ".{$uid} .olo-pg-item{width:180px;height:200px;clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);overflow:hidden;position:relative}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:cover;display:block}";
        } elseif ( $layout === 'hexgrid' ) {
            // Tessellating hex grid — positions computed inline per item
            echo ".{$uid}{position:relative;width:100%;overflow:visible}";
            echo ".{$uid} .olo-pg-item{position:absolute;clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);overflow:hidden}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:cover;display:block}";
        } elseif ( $layout === 'puzzle' ) {
            $p_h_px = intval( $img_height ) ?: 250;
            $p_rows_total = (int) ceil( min( $max_visible, $total ) / $cols );
            $p_container_h = $p_rows_total * $p_h_px;
            echo ".{$uid}{position:relative;width:100%;height:{$p_container_h}px;overflow:visible}";
            echo ".{$uid} .olo-pg-item{position:absolute}";
            echo ".{$uid} .olo-pg-item img{position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;display:block}";
        } elseif ( $layout === 'collage' ) {
            echo ".{$uid}{display:grid;grid-template-columns:repeat({$cols},1fr);grid-auto-rows:{$img_height};gap:{$gap}px}";
            echo ".{$uid} .olo-pg-item{position:relative;overflow:hidden;border-radius:{$radius}px}";
            echo ".{$uid} .olo-pg-item:first-child{grid-column:span 2;grid-row:span 2}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block}";
        } elseif ( $layout === 'mosaic' ) {
            echo ".{$uid}{display:grid;grid-template-columns:repeat({$cols},1fr);grid-auto-rows:{$img_height};gap:{$gap}px}";
            echo ".{$uid} .olo-pg-item{position:relative;overflow:hidden;border-radius:{$radius}px}";
            echo ".{$uid} .olo-pg-item:nth-child(5n+1){grid-column:span 2;grid-row:span 2}";
            echo ".{$uid} .olo-pg-item:nth-child(5n+4){grid-column:span 2}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block}";
        } elseif ( $layout === 'diagonal' ) {
            echo ".{$uid}{display:grid;grid-template-columns:repeat({$cols},1fr);gap:{$gap}px}";
            echo ".{$uid} .olo-pg-item{position:relative;overflow:hidden;border-radius:{$radius}px;height:{$img_height}}";
            echo ".{$uid} .olo-pg-item:nth-child(odd){clip-path:polygon(0 0,100% 8%,100% 100%,0 92%)}";
            echo ".{$uid} .olo-pg-item:nth-child(even){clip-path:polygon(0 8%,100% 0,100% 92%,0 100%)}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block}";
        } else {
            // Default: grid
            echo ".{$uid}{display:grid;grid-template-columns:repeat({$cols},1fr);gap:{$gap}px}";
            echo ".{$uid} .olo-pg-item{position:relative;overflow:hidden;border-radius:{$radius}px;height:{$img_height}}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block;transition:transform .5s cubic-bezier(.25,.46,.45,.94),filter .5s ease}";
        }

        // ─── CSS Filter ───
        if ( $css_filter ) {
            echo ".{$uid} .olo-pg-item img{filter:{$css_filter}}";
            if ( $filter_hover ) {
                echo ".{$uid} .olo-pg-item:hover img{filter:none}";
            }
        }

        // ─── Duotone pseudo-element ───
        if ( $filter === 'duotone' ) {
            echo ".{$uid} .olo-pg-item{position:relative}";
            echo ".{$uid} .olo-pg-item::before{content:'';position:absolute;inset:0;z-index:1;pointer-events:none;background:linear-gradient(to bottom,{$duo_dark},{$duo_light});mix-blend-mode:color;border-radius:{$radius}px}";
            if ( $filter_hover ) {
                echo ".{$uid} .olo-pg-item:hover::before{opacity:0;transition:opacity .4s ease}";
            }
        }

        // ─── Hover effects ───
        if ( $hover === 'zoom' ) {
            echo ".{$uid} .olo-pg-item img{transition:transform .5s cubic-bezier(.25,.46,.45,.94)}";
            echo ".{$uid} .olo-pg-item:hover img{transform:scale({$hz_scale})}";
        } elseif ( $hover === 'lift' ) {
            echo ".{$uid} .olo-pg-item{transition:transform .4s ease,box-shadow .4s ease}";
            echo ".{$uid} .olo-pg-item:hover{transform:translateY(-6px);box-shadow:0 12px 28px rgba(0,0,0,.2)}";
        } elseif ( $hover === 'tilt3d' ) {
            echo ".{$uid} .olo-pg-item{perspective:600px;transform-style:preserve-3d;transition:transform .4s ease}";
            echo ".{$uid} .olo-pg-item img{transition:transform .4s ease}";
            // JS tilt handled by shared script
        } elseif ( $hover === 'glow' ) {
            echo ".{$uid} .olo-pg-item{transition:box-shadow .4s ease}";
            echo ".{$uid} .olo-pg-item:hover{box-shadow:0 0 20px 4px rgba(99,102,241,.4)}";
        } elseif ( $hover === 'blur-peers' ) {
            echo ".{$uid}:hover .olo-pg-item{filter:blur(3px);transition:filter .4s ease}";
            echo ".{$uid}:hover .olo-pg-item:hover{filter:none}";
            if ( $css_filter ) {
                echo ".{$uid}:hover .olo-pg-item:hover img{filter:none}";
            }
        } elseif ( $hover === 'magnetic' ) {
            echo ".{$uid} .olo-pg-item{transition:transform .3s ease}";
            // JS magnetic handled by shared script
        }

        // ─── Hover caption ───
        if ( $hcaption !== 'none' ) {
            echo ".{$uid} .olo-pg-cap{position:absolute;left:0;right:0;bottom:0;padding:8px 12px;font-size:13px;line-height:1.3;color:{$hcap_color};background:{$hcap_bg};z-index:3;pointer-events:none}";
            if ( $hcaption === 'slide-up' ) {
                echo ".{$uid} .olo-pg-cap{transform:translateY(100%);transition:transform .35s ease}";
                echo ".{$uid} .olo-pg-item:hover .olo-pg-cap{transform:translateY(0)}";
            } elseif ( $hcaption === 'fade' ) {
                echo ".{$uid} .olo-pg-cap{opacity:0;transition:opacity .35s ease}";
                echo ".{$uid} .olo-pg-item:hover .olo-pg-cap{opacity:1}";
            } elseif ( $hcaption === 'overlay' ) {
                echo ".{$uid} .olo-pg-cap{inset:0;display:flex;align-items:center;justify-content:center;text-align:center;opacity:0;transition:opacity .35s ease}";
                echo ".{$uid} .olo-pg-item:hover .olo-pg-cap{opacity:1}";
            }
        }

        // ─── Continuous animation keyframes ───
        if ( $continuous !== 'none' ) {
            if ( $continuous === 'float' ) {
                echo "@keyframes {$uid}-float{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}";
                echo ".{$uid} .olo-pg-item{animation:{$uid}-float {$cont_speed}s ease-in-out infinite}";
            } elseif ( $continuous === 'drift' ) {
                echo "@keyframes {$uid}-drift{0%{transform:translate(0,0)}25%{transform:translate(4px,-3px)}50%{transform:translate(-3px,2px)}75%{transform:translate(2px,4px)}100%{transform:translate(0,0)}}";
                echo ".{$uid} .olo-pg-item{animation:{$uid}-drift {$cont_speed}s ease-in-out infinite}";
            } elseif ( $continuous === 'breathe' ) {
                echo "@keyframes {$uid}-breathe{0%,100%{transform:scale(1)}50%{transform:scale(1.03)}}";
                echo ".{$uid} .olo-pg-item img{animation:{$uid}-breathe {$cont_speed}s ease-in-out infinite}";
            } elseif ( $continuous === 'rotate-slow' ) {
                echo "@keyframes {$uid}-rotslow{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}";
                echo ".{$uid} .olo-pg-item img{animation:{$uid}-rotslow {$cont_speed}s linear infinite}";
            } elseif ( $continuous === 'kenburns' ) {
                echo "@keyframes {$uid}-kb{0%{transform:scale(1) translate(0,0)}33%{transform:scale(1.12) translate(-2%,-1%)}66%{transform:scale(1.08) translate(1%,0.5%)}100%{transform:scale(1) translate(0,0)}}";
                echo ".{$uid} .olo-pg-item img{animation:{$uid}-kb {$cont_speed}s ease-in-out infinite}";
            }
            // nth-child delay stagger for variety
            for ( $d = 1; $d <= 6; $d++ ) {
                $delay = round( $cont_speed / 6 * $d, 1 );
                echo ".{$uid} .olo-pg-item:nth-child({$d}n) img,.{$uid} .olo-pg-item:nth-child({$d}n){animation-delay:-{$delay}s}";
            }
        }

        // ─── Frame styles ───
        if ( $frame === 'polaroid' ) {
            echo ".{$uid} .olo-pg-item{background:{$frame_color};padding:8px 8px 32px 8px;border-radius:2px;box-shadow:0 2px 8px rgba(0,0,0,.12)}";
            echo ".{$uid} .olo-pg-item img{border-radius:0}";
        } elseif ( $frame === 'rounded' ) {
            echo ".{$uid} .olo-pg-item{border-radius:50%;overflow:hidden}";
            echo ".{$uid} .olo-pg-item img{border-radius:50%}";
        } elseif ( $frame === 'shadow-box' ) {
            echo ".{$uid} .olo-pg-item{background:{$frame_color};padding:6px;box-shadow:0 4px 16px rgba(0,0,0,.18),inset 0 1px 0 rgba(255,255,255,.5);border-radius:" . ( $radius + 2 ) . "px}";
        } elseif ( $frame === 'torn' ) {
            echo ".{$uid} .olo-pg-item{clip-path:polygon(0 2%,4% 0,10% 3%,18% 0,24% 2%,32% 0,40% 1%,48% 0,56% 2%,64% 0,72% 1%,80% 0,88% 2%,94% 0,100% 3%,100% 97%,96% 100%,90% 98%,82% 100%,76% 98%,68% 100%,60% 99%,52% 100%,44% 98%,36% 100%,28% 99%,20% 100%,12% 98%,6% 100%,0 97%)}";
        } elseif ( $frame === 'tape' ) {
            echo ".{$uid} .olo-pg-item{position:relative}";
            echo ".{$uid} .olo-pg-item::after{content:'';position:absolute;top:-6px;left:50%;transform:translateX(-50%);width:50px;height:18px;background:rgba(255,255,200,.7);border-radius:2px;z-index:4;box-shadow:0 1px 3px rgba(0,0,0,.1)}";
        }

        // ─── "+N" overlay ───
        echo ".{$uid} .olo-pg-more{position:absolute;inset:0;z-index:5;display:flex;align-items:center;justify-content:center;background:{$more_bg};color:{$more_color};font-size:{$more_size}px;font-weight:700;pointer-events:none;border-radius:{$radius}px}";

        // ─── Hidden lightbox items ───
        echo ".{$uid} .olo-pg-hidden{position:absolute;width:0;height:0;overflow:hidden;pointer-events:none;opacity:0}";

        // ─── Entrance animation ───
        if ( $entrance !== 'none' ) {
            $dur_s = number_format( $ent_dur / 1000, 2 );
            $initial_transform = 'translateY(24px)';
            $initial_extra = '';

            if ( $entrance === 'fade-up' ) {
                $initial_transform = 'translateY(24px)';
            } elseif ( $entrance === 'fade-scale' ) {
                $initial_transform = 'translateY(12px) scale(.92)';
            } elseif ( $entrance === 'flip' ) {
                $initial_transform = 'perspective(600px) rotateX(60deg)';
                $initial_extra = 'transform-origin:center bottom;';
            } elseif ( $entrance === 'slide-in' ) {
                $initial_transform = 'translateX(-30px)';
            } elseif ( $entrance === 'blur-in' ) {
                $initial_transform = 'translateY(8px)';
                $initial_extra = 'filter:blur(8px);';
            }

            echo ".{$uid} .olo-pg-item{opacity:0;transform:{$initial_transform};{$initial_extra}transition:opacity {$dur_s}s ease,transform {$dur_s}s ease,filter {$dur_s}s ease}";
            echo ".{$uid}.olo-pg-visible .olo-pg-item{opacity:1;transform:none;filter:none}";

            // Stagger delays
            $vis_count = min( $max_visible, $total );
            for ( $i = 0; $i < $vis_count && $i < 30; $i++ ) {
                $delay_s = number_format( ( $ent_stagger * $i ) / 1000, 2 );
                echo ".{$uid} .olo-pg-item:nth-child(" . ( $i + 1 ) . "){transition-delay:{$delay_s}s}";
            }
        }

        // ─── Mobile ───
        if ( $layout === 'masonry' ) {
            echo "@media(max-width:640px){.{$uid}{column-count:{$mob_cols}}}";
        } elseif ( $layout !== 'filmstrip' && $layout !== 'scattered' && $layout !== 'honeycomb' && $layout !== 'hexgrid' && $layout !== 'puzzle' ) {
            echo "@media(max-width:640px){.{$uid}{grid-template-columns:repeat({$mob_cols},1fr)}}";
        }

        // ─── Reduced motion ───
        echo "@media(prefers-reduced-motion:reduce){";
        echo ".{$uid} .olo-pg-item,.{$uid} .olo-pg-item img{animation:none!important;transition:none!important}";
        if ( $entrance !== 'none' ) {
            echo ".{$uid} .olo-pg-item{opacity:1!important;transform:none!important;filter:none!important}";
        }
        echo "}";

        echo '</style>';

        // ─── Shared JS (once per page) ───
        $needs_tilt3d   = ( $hover === 'tilt3d' );
        $needs_magnetic = ( $hover === 'magnetic' );
        $needs_entrance = ( $entrance !== 'none' );
        $needs_filmstrip = ( $layout === 'filmstrip' );
        self::maybe_output_script( $needs_tilt3d, $needs_magnetic, $needs_entrance, $needs_filmstrip );

        // ─── Container attrs ───
        $container_class = esc_attr( $uid );
        $data_attrs = '';
        if ( $needs_entrance ) {
            $data_attrs .= ' data-pg-reveal="1"';
        }
        if ( $needs_tilt3d ) {
            $data_attrs .= ' data-pg-tilt="' . $tilt_angle . '"';
        }
        if ( $needs_magnetic ) {
            $data_attrs .= ' data-pg-magnetic="' . $mag_str . '"';
        }
        if ( $needs_filmstrip ) {
            $data_attrs .= ' data-pg-filmstrip="1"';
        }

        // Lightbox wrapper
        $lb_attr = $lightbox ? ' uk-lightbox="animation: ' . $lb_anim . '"' : '';

        // Hexgrid container needs padding-bottom for aspect ratio
        $hex_inline = '';
        if ( $layout === 'hexgrid' ) {
            $hex_w     = 100 / ( $cols + 0.5 );
            $hex_h     = $hex_w * 1.1547;
            $row_step  = $hex_h * 0.75;
            $hex_rows  = (int) ceil( min( $max_visible, $total ) / $cols );
            $container_h = round( $row_step * ( $hex_rows - 1 ) + $hex_h, 2 );
            $hex_inline = ' style="padding-bottom:' . $container_h . '%"';
        }

        echo '<div class="' . $container_class . '"' . $lb_attr . $data_attrs . $hex_inline . '>';

        // ─── Render items ───
        $i = 0;
        foreach ( $images as $img ) {
            $url     = is_array( $img ) ? ( $img['url'] ?? '' ) : $img;
            $alt     = is_array( $img ) ? ( $img['alt'] ?? '' ) : '';
            $caption = is_array( $img ) ? ( $img['caption'] ?? '' ) : '';
            $att_id  = is_array( $img ) ? absint( $img['id'] ?? 0 ) : 0;
            if ( ! $url ) continue;
            $i++;

            $is_visible  = ( $i <= $max_visible );
            $is_last_vis = ( $i === $max_visible && $extra > 0 );
            $caption_attr = '';
            if ( $lightbox ) {
                if ( $show_cap && ! empty( $caption ) ) {
                    $caption_attr = ' data-caption="' . esc_attr( $caption ) . '"';
                } elseif ( $extra > 0 ) {
                    $caption_attr = ' data-caption="' . $i . '/' . $total . '"';
                }
            }

            // Scattered layout: inline position styles
            $inline_style = '';
            if ( $layout === 'scattered' && $is_visible ) {
                $total_vis = min( $max_visible, $total );
                $cols_s  = max( 2, (int) ceil( sqrt( $total_vis ) ) );
                $rows_s  = (int) ceil( $total_vis / $cols_s );
                $col_i   = ( $i - 1 ) % $cols_s;
                $row_i   = (int) floor( ( $i - 1 ) / $cols_s );
                $cw      = 100 / $cols_s;
                $ch      = 100 / $rows_s;
                $ox      = ( $this->seeded_random( $i ) - 0.5 ) * 10;
                $oy      = ( $this->seeded_random( $i + 13 ) - 0.5 ) * 10;
                $rot     = ( $this->seeded_random( $i + 7 ) - 0.5 ) * 12;
                $left    = round( $col_i * $cw + $ox, 1 );
                $top     = round( $row_i * $ch + $oy, 1 );
                $w       = round( $cw * 0.8, 1 );
                $h_s     = round( $ch * 0.8, 1 );
                $inline_style = "left:{$left}%;top:{$top}%;width:{$w}%;height:{$h_s}%;transform:rotate({$rot}deg)";
            }

            // Hex grid: compute absolute position for tessellating hexagons
            if ( $layout === 'hexgrid' && $is_visible ) {
                $hex_w    = 100 / ( $cols + 0.5 );
                $hex_h    = $hex_w * 1.1547;
                $row_step = $hex_h * 0.75;
                $hex_row  = (int) floor( ( $i - 1 ) / $cols );
                $hex_col  = ( $i - 1 ) % $cols;
                $offset_x = ( $hex_row % 2 ) ? $hex_w * 0.5 : 0;
                $hex_rows_total = (int) ceil( min( $max_visible, $total ) / $cols );
                $container_h    = $row_step * ( $hex_rows_total - 1 ) + $hex_h;
                $l = round( $hex_col * $hex_w + $offset_x, 2 );
                $t = round( $hex_row * $row_step / $container_h * 100, 2 );
                $w = round( $hex_w, 2 );
                $h = round( $hex_h / $container_h * 100, 2 );
                $inline_style = "left:{$l}%;top:{$t}%;width:{$w}%;height:{$h}%";
            }

            // Puzzle: compute position + clip-path per piece (jigsaw interlocking)
            // Each piece's bounding box is 126% of cell (13% padding per side) so pieces overlap.
            // All clip-path coords remapped: pz(v) = (v + 13) / 1.26
            $puzzle_clip = '';
            if ( $layout === 'puzzle' && $is_visible ) {
                $puzzle_style = $s['puzzle_style'] ?: 'classic';

                // PZ_PAD dinamico in base allo stile
                if ( in_array( $puzzle_style, [ 'zigzag', 'wave', 'castle' ], true ) ) {
                    $PZ_PAD = 14;
                } elseif ( $puzzle_style === 'fir' ) {
                    $PZ_PAD = 20;
                } else {
                    $PZ_PAD = 23;
                }
                $PZ_SC = 100 + 2 * $PZ_PAD;
                $pz = function( $v ) use ( $PZ_PAD, $PZ_SC ) {
                    return round( ( $v + $PZ_PAD ) / $PZ_SC * 100, 1 );
                };

                $p_h_px  = intval( $img_height ) ?: 250;
                $p_row   = (int) floor( ( $i - 1 ) / $cols );
                $p_col   = ( $i - 1 ) % $cols;
                $p_rows  = (int) ceil( min( $max_visible, $total ) / $cols );
                $cellW   = 100 / $cols;
                $padW    = $cellW * $PZ_PAD / 100;
                $padH    = $p_h_px * $PZ_PAD / 100;
                $p_left  = round( $p_col * $cellW - $padW, 2 );
                $p_top   = round( $p_row * $p_h_px - $padH, 1 );
                $p_w     = round( $cellW + 2 * $padW, 2 );
                $p_ht    = round( $p_h_px + 2 * $padH, 1 );
                $inline_style = "left:{$p_left}%;top:{$p_top}px;width:{$p_w}%;height:{$p_ht}px";

                $isEven  = ( $p_row + $p_col ) % 2 === 0;
                $e_top    = $p_row === 0           ? 'flat' : ( $isEven ? 'blank' : 'tab' );
                $e_right  = $p_col === $cols - 1   ? 'flat' : ( $isEven ? 'tab' : 'blank' );
                $e_bottom = $p_row >= $p_rows - 1  ? 'flat' : ( $isEven ? 'blank' : 'tab' );
                $e_left   = $p_col === 0           ? 'flat' : ( $isEven ? 'tab' : 'blank' );

                $pts = [ $pz(0).'% '.$pz(0).'%' ];

                // ── Classic: knob rotondi jigsaw ──
                $classic_edge = function( $edge, $type ) use ( $pz ) {
                    $d  = $type === 'tab' ? 1 : -1;
                    $R  = 9; $CY = 13; $NW = 5.5; $NH = 6; $N = 16;
                    $arc = [];
                    for ( $k = 0; $k <= $N; $k++ ) {
                        $rad = deg2rad( 225 - $k * 270 / $N );
                        $arc[] = [ 50 + $R * cos( $rad ), $CY + $R * sin( $rad ) ];
                    }
                    if ( $edge === 'top' ) {
                        $pts = [ $pz(30).'% '.$pz(0).'%', $pz(36).'% '.$pz(0).'%' ];
                        $pts[] = $pz(50-$NW).'% '.$pz(-$d*1).'%';
                        $pts[] = $pz(50-$NW).'% '.$pz(-$d*$NH).'%';
                        foreach ( $arc as list($ax,$ay) ) { $pts[] = $pz($ax).'% '.$pz(-$d*$ay).'%'; }
                        $pts[] = $pz(50+$NW).'% '.$pz(-$d*$NH).'%';
                        $pts[] = $pz(50+$NW).'% '.$pz(-$d*1).'%';
                        $pts[] = $pz(64).'% '.$pz(0).'%';
                        $pts[] = $pz(70).'% '.$pz(0).'%';
                        return $pts;
                    }
                    if ( $edge === 'right' ) {
                        $pts = [ $pz(100).'% '.$pz(30).'%', $pz(100).'% '.$pz(36).'%' ];
                        $pts[] = $pz(100+$d*1).'% '.$pz(50-$NW).'%';
                        $pts[] = $pz(100+$d*$NH).'% '.$pz(50-$NW).'%';
                        foreach ( array_reverse( $arc ) as list($ax,$ay) ) { $pts[] = $pz(100+$d*$ay).'% '.$pz(100-$ax).'%'; }
                        $pts[] = $pz(100+$d*$NH).'% '.$pz(50+$NW).'%';
                        $pts[] = $pz(100+$d*1).'% '.$pz(50+$NW).'%';
                        $pts[] = $pz(100).'% '.$pz(64).'%';
                        $pts[] = $pz(100).'% '.$pz(70).'%';
                        return $pts;
                    }
                    if ( $edge === 'bottom' ) {
                        $pts = [ $pz(70).'% '.$pz(100).'%', $pz(64).'% '.$pz(100).'%' ];
                        $pts[] = $pz(50+$NW).'% '.$pz(100+$d*1).'%';
                        $pts[] = $pz(50+$NW).'% '.$pz(100+$d*$NH).'%';
                        foreach ( $arc as list($ax,$ay) ) { $pts[] = $pz(100-$ax).'% '.$pz(100+$d*$ay).'%'; }
                        $pts[] = $pz(50-$NW).'% '.$pz(100+$d*$NH).'%';
                        $pts[] = $pz(50-$NW).'% '.$pz(100+$d*1).'%';
                        $pts[] = $pz(36).'% '.$pz(100).'%';
                        $pts[] = $pz(30).'% '.$pz(100).'%';
                        return $pts;
                    }
                    if ( $edge === 'left' ) {
                        $pts = [ $pz(0).'% '.$pz(70).'%', $pz(0).'% '.$pz(64).'%' ];
                        $pts[] = $pz(-$d*1).'% '.$pz(50+$NW).'%';
                        $pts[] = $pz(-$d*$NH).'% '.$pz(50+$NW).'%';
                        foreach ( $arc as list($ax,$ay) ) { $pts[] = $pz(-$d*$ay).'% '.$pz(100-$ax).'%'; }
                        $pts[] = $pz(-$d*$NH).'% '.$pz(50-$NW).'%';
                        $pts[] = $pz(-$d*1).'% '.$pz(50-$NW).'%';
                        $pts[] = $pz(0).'% '.$pz(36).'%';
                        $pts[] = $pz(0).'% '.$pz(30).'%';
                        return $pts;
                    }
                    return [];
                };

                // ── Zigzag: 4 denti triangolari per lato ──
                $zigzag_edge = function( $edge, $type ) use ( $pz ) {
                    $d = $type === 'tab' ? 1 : -1;
                    $amp = 12; $teeth = 4;
                    $start = 25; $end = 75;
                    $span = $end - $start;
                    $step = $span / $teeth;
                    if ( $edge === 'top' ) {
                        $pts = [ $pz($start).'% '.$pz(0).'%' ];
                        for ( $t = 0; $t < $teeth; $t++ ) {
                            $x0 = $start + $t * $step;
                            $pts[] = $pz($x0 + $step/2).'% '.$pz(-$d * $amp).'%';
                            $pts[] = $pz($x0 + $step).'% '.$pz(0).'%';
                        }
                        return $pts;
                    }
                    if ( $edge === 'right' ) {
                        $pts = [ $pz(100).'% '.$pz($start).'%' ];
                        for ( $t = 0; $t < $teeth; $t++ ) {
                            $y0 = $start + $t * $step;
                            $pts[] = $pz(100 + $d * $amp).'% '.$pz($y0 + $step/2).'%';
                            $pts[] = $pz(100).'% '.$pz($y0 + $step).'%';
                        }
                        return $pts;
                    }
                    if ( $edge === 'bottom' ) {
                        $pts = [ $pz($end).'% '.$pz(100).'%' ];
                        for ( $t = 0; $t < $teeth; $t++ ) {
                            $x0 = $end - $t * $step;
                            $pts[] = $pz($x0 - $step/2).'% '.$pz(100 + $d * $amp).'%';
                            $pts[] = $pz($x0 - $step).'% '.$pz(100).'%';
                        }
                        return $pts;
                    }
                    if ( $edge === 'left' ) {
                        $pts = [ $pz(0).'% '.$pz($end).'%' ];
                        for ( $t = 0; $t < $teeth; $t++ ) {
                            $y0 = $end - $t * $step;
                            $pts[] = $pz(-$d * $amp).'% '.$pz($y0 - $step/2).'%';
                            $pts[] = $pz(0).'% '.$pz($y0 - $step).'%';
                        }
                        return $pts;
                    }
                    return [];
                };

                // ── Wave: 2 cicli sinusoidali ──
                $wave_edge = function( $edge, $type ) use ( $pz ) {
                    $d = $type === 'tab' ? 1 : -1;
                    $amp = 10; $N = 32;
                    $start = 20; $end = 80;
                    $span = $end - $start;
                    if ( $edge === 'top' ) {
                        $pts = [];
                        for ( $j = 0; $j <= $N; $j++ ) {
                            $t = $j / $N;
                            $x = $start + $t * $span;
                            $perp = $d * $amp * sin( $t * 4 * M_PI );
                            $pts[] = $pz($x).'% '.$pz(-$perp).'%';
                        }
                        return $pts;
                    }
                    if ( $edge === 'right' ) {
                        $pts = [];
                        for ( $j = 0; $j <= $N; $j++ ) {
                            $t = $j / $N;
                            $y = $start + $t * $span;
                            $perp = $d * $amp * sin( $t * 4 * M_PI );
                            $pts[] = $pz(100 + $perp).'% '.$pz($y).'%';
                        }
                        return $pts;
                    }
                    if ( $edge === 'bottom' ) {
                        $pts = [];
                        for ( $j = 0; $j <= $N; $j++ ) {
                            $t = $j / $N;
                            $x = $end - $t * $span;
                            $perp = -$d * $amp * sin( $t * 4 * M_PI );
                            $pts[] = $pz($x).'% '.$pz(100 + $perp).'%';
                        }
                        return $pts;
                    }
                    if ( $edge === 'left' ) {
                        $pts = [];
                        for ( $j = 0; $j <= $N; $j++ ) {
                            $t = $j / $N;
                            $y = $end - $t * $span;
                            $perp = -$d * $amp * sin( $t * 4 * M_PI );
                            $pts[] = $pz(-$perp).'% '.$pz($y).'%';
                        }
                        return $pts;
                    }
                    return [];
                };

                // ── Castle: 3 merli rettangolari ──
                $castle_edge = function( $edge, $type ) use ( $pz ) {
                    $d = $type === 'tab' ? 1 : -1;
                    $h = 10; $w = 8; $merlons = 3;
                    $start = 25; $end = 75;
                    $span = $end - $start;
                    $merlonSpan = $span / $merlons;
                    $gap = ( $merlonSpan - $w ) / 2;
                    if ( $edge === 'top' ) {
                        $pts = [ $pz($start).'% '.$pz(0).'%' ];
                        for ( $m = 0; $m < $merlons; $m++ ) {
                            $x0 = $start + $m * $merlonSpan + $gap;
                            $pts[] = $pz($x0).'% '.$pz(0).'%';
                            $pts[] = $pz($x0).'% '.$pz(-$d * $h).'%';
                            $pts[] = $pz($x0 + $w).'% '.$pz(-$d * $h).'%';
                            $pts[] = $pz($x0 + $w).'% '.$pz(0).'%';
                        }
                        $pts[] = $pz($end).'% '.$pz(0).'%';
                        return $pts;
                    }
                    if ( $edge === 'right' ) {
                        $pts = [ $pz(100).'% '.$pz($start).'%' ];
                        for ( $m = 0; $m < $merlons; $m++ ) {
                            $y0 = $start + $m * $merlonSpan + $gap;
                            $pts[] = $pz(100).'% '.$pz($y0).'%';
                            $pts[] = $pz(100 + $d * $h).'% '.$pz($y0).'%';
                            $pts[] = $pz(100 + $d * $h).'% '.$pz($y0 + $w).'%';
                            $pts[] = $pz(100).'% '.$pz($y0 + $w).'%';
                        }
                        $pts[] = $pz(100).'% '.$pz($end).'%';
                        return $pts;
                    }
                    if ( $edge === 'bottom' ) {
                        $pts = [ $pz($end).'% '.$pz(100).'%' ];
                        for ( $m = 0; $m < $merlons; $m++ ) {
                            $x0 = $end - $m * $merlonSpan - $gap;
                            $pts[] = $pz($x0).'% '.$pz(100).'%';
                            $pts[] = $pz($x0).'% '.$pz(100 + $d * $h).'%';
                            $pts[] = $pz($x0 - $w).'% '.$pz(100 + $d * $h).'%';
                            $pts[] = $pz($x0 - $w).'% '.$pz(100).'%';
                        }
                        $pts[] = $pz($start).'% '.$pz(100).'%';
                        return $pts;
                    }
                    if ( $edge === 'left' ) {
                        $pts = [ $pz(0).'% '.$pz($end).'%' ];
                        for ( $m = 0; $m < $merlons; $m++ ) {
                            $y0 = $end - $m * $merlonSpan - $gap;
                            $pts[] = $pz(0).'% '.$pz($y0).'%';
                            $pts[] = $pz(-$d * $h).'% '.$pz($y0).'%';
                            $pts[] = $pz(-$d * $h).'% '.$pz($y0 - $w).'%';
                            $pts[] = $pz(0).'% '.$pz($y0 - $w).'%';
                        }
                        $pts[] = $pz(0).'% '.$pz($start).'%';
                        return $pts;
                    }
                    return [];
                };

                // ── Fir: 2 abeti a 3 livelli per lato ──
                $fir_edge = function( $edge, $type ) use ( $pz ) {
                    $d = $type === 'tab' ? 1 : -1;
                    $tw = 1.5; $th = 3.75;
                    $t1w = 8.25; $t1pw = 3; $t1h = 8.25;
                    $t2w = 6; $t2pw = 2.25; $t2h = 12.75;
                    $t3w = 4.5; $peakH = 18;
                    $P = [
                        [-$tw,0],[-$tw,$th],[-$t1w,$th],[-$t1pw,$t1h],[-$t2w,$t1h],[-$t2pw,$t2h],[-$t3w,$t2h],
                        [0,$peakH],
                        [$t3w,$t2h],[$t2pw,$t2h],[$t2w,$t1h],[$t1pw,$t1h],[$t1w,$th],[$tw,$th],[$tw,0]
                    ];
                    if ( $edge === 'top' ) {
                        $pts = [ $pz(25).'% '.$pz(0).'%' ];
                        foreach ( [37.5, 62.5] as $cx ) {
                            foreach ( $P as list($ao, $po) ) {
                                $pts[] = $pz($cx + $ao).'% '.$pz(-$d * $po).'%';
                            }
                        }
                        $pts[] = $pz(75).'% '.$pz(0).'%';
                        return $pts;
                    }
                    if ( $edge === 'right' ) {
                        $pts = [ $pz(100).'% '.$pz(25).'%' ];
                        foreach ( [37.5, 62.5] as $cy ) {
                            foreach ( $P as list($ao, $po) ) {
                                $pts[] = $pz(100 + $d * $po).'% '.$pz($cy + $ao).'%';
                            }
                        }
                        $pts[] = $pz(100).'% '.$pz(75).'%';
                        return $pts;
                    }
                    if ( $edge === 'bottom' ) {
                        $pts = [ $pz(75).'% '.$pz(100).'%' ];
                        foreach ( [62.5, 37.5] as $cx ) {
                            foreach ( $P as list($ao, $po) ) {
                                $pts[] = $pz($cx - $ao).'% '.$pz(100 + $d * $po).'%';
                            }
                        }
                        $pts[] = $pz(25).'% '.$pz(100).'%';
                        return $pts;
                    }
                    if ( $edge === 'left' ) {
                        $pts = [ $pz(0).'% '.$pz(75).'%' ];
                        foreach ( [62.5, 37.5] as $cy ) {
                            foreach ( $P as list($ao, $po) ) {
                                $pts[] = $pz(-$d * $po).'% '.$pz($cy - $ao).'%';
                            }
                        }
                        $pts[] = $pz(0).'% '.$pz(25).'%';
                        return $pts;
                    }
                    return [];
                };

                // Dispatcher
                $knob = $classic_edge;
                if ( $puzzle_style === 'zigzag' ) $knob = $zigzag_edge;
                elseif ( $puzzle_style === 'wave' ) $knob = $wave_edge;
                elseif ( $puzzle_style === 'castle' ) $knob = $castle_edge;
                elseif ( $puzzle_style === 'fir' ) $knob = $fir_edge;

                // Top
                if ( $e_top === 'flat' ) { $pts[] = $pz(100).'% '.$pz(0).'%'; }
                else { $pts = array_merge( $pts, $knob( 'top', $e_top ), [ $pz(100).'% '.$pz(0).'%' ] ); }
                // Right
                if ( $e_right === 'flat' ) { $pts[] = $pz(100).'% '.$pz(100).'%'; }
                else { $pts = array_merge( $pts, $knob( 'right', $e_right ), [ $pz(100).'% '.$pz(100).'%' ] ); }
                // Bottom
                if ( $e_bottom === 'flat' ) { $pts[] = $pz(0).'% '.$pz(100).'%'; }
                else { $pts = array_merge( $pts, $knob( 'bottom', $e_bottom ), [ $pz(0).'% '.$pz(100).'%' ] ); }
                // Left
                if ( $e_left !== 'flat' ) { $pts = array_merge( $pts, $knob( 'left', $e_left ) ); }

                $puzzle_clip = 'clip-path:polygon(' . implode( ',', $pts ) . ')';
            }

            // Tag: <a> if lightbox, <div> otherwise
            $tag = $lightbox ? 'a' : 'div';
            $href = $lightbox ? ' href="' . esc_url( $url ) . '"' : '';

            if ( $is_visible ) {
                $combined_style = trim( $inline_style . ( $puzzle_clip ? ';' . $puzzle_clip : '' ) );
                $style_attr = $combined_style ? ' style="' . esc_attr( $combined_style ) . '"' : '';
                echo '<' . $tag . ' class="olo-pg-item"' . $href . $caption_attr . $style_attr . '>';
                echo Olo_Tile_Utils::img_srcset( $att_id, $url, esc_attr( $alt ) );

                // Hover caption
                if ( $hcaption !== 'none' && ! empty( $caption ) ) {
                    echo '<div class="olo-pg-cap">' . esc_html( $caption ) . '</div>';
                }

                // +N overlay
                if ( $is_last_vis ) {
                    echo '<div class="olo-pg-more">+' . $extra . '</div>';
                }

                echo '</' . $tag . '>';
            } else {
                // Hidden items for lightbox
                if ( $lightbox ) {
                    echo '<a class="olo-pg-hidden" href="' . esc_url( $url ) . '"' . $caption_attr . '></a>';
                }
            }
        }

        echo '</div>';

        return ob_get_clean();
    }

    /**
     * Output shared JS once per page for: tilt3d, magnetic, entrance reveal, filmstrip auto-scroll.
     */
    private static function maybe_output_script( $tilt, $magnetic, $entrance, $filmstrip ) {
        if ( self::$script_output ) return;
        if ( ! $tilt && ! $magnetic && ! $entrance && ! $filmstrip ) return;
        self::$script_output = true;

        echo '<script>';
        echo '(function(){';
        echo 'if(window._oloPGscript)return;window._oloPGscript=1;';
        echo 'var rm=window.matchMedia("(prefers-reduced-motion:reduce)").matches;';

        // ── Entrance reveal (IntersectionObserver) ──
        echo 'function initReveal(){';
        echo 'var els=document.querySelectorAll("[data-pg-reveal]");';
        echo 'if(!els.length)return;';
        echo 'if(rm){els.forEach(function(el){el.classList.add("olo-pg-visible")});return}';
        echo 'var obs=new IntersectionObserver(function(entries){';
        echo 'entries.forEach(function(e){if(e.isIntersecting){e.target.classList.add("olo-pg-visible");obs.unobserve(e.target)}});';
        echo '},{threshold:0.1});';
        echo 'els.forEach(function(el){obs.observe(el)});';
        echo '}';

        // ── Tilt 3D (mousemove) ──
        echo 'function initTilt(){';
        echo 'if(rm)return;';
        echo 'document.querySelectorAll("[data-pg-tilt]").forEach(function(container){';
        echo 'var angle=parseInt(container.dataset.pgTilt)||10;';
        echo 'container.querySelectorAll(".olo-pg-item").forEach(function(item){';
        echo 'item.addEventListener("mousemove",function(e){';
        echo 'var r=item.getBoundingClientRect();';
        echo 'var x=(e.clientX-r.left)/r.width-.5;';
        echo 'var y=(e.clientY-r.top)/r.height-.5;';
        echo 'item.style.transform="perspective(600px) rotateY("+(x*angle)+"deg) rotateX("+(-y*angle)+"deg) scale(1.02)";';
        echo '});';
        echo 'item.addEventListener("mouseleave",function(){item.style.transform=""});';
        echo '});});';
        echo '}';

        // ── Magnetic (mousemove) ──
        echo 'function initMagnetic(){';
        echo 'if(rm)return;';
        echo 'document.querySelectorAll("[data-pg-magnetic]").forEach(function(container){';
        echo 'var str=parseInt(container.dataset.pgMagnetic)||24;';
        echo 'container.querySelectorAll(".olo-pg-item").forEach(function(item){';
        echo 'item.addEventListener("mousemove",function(e){';
        echo 'var r=item.getBoundingClientRect();';
        echo 'var x=(e.clientX-r.left)/r.width-.5;';
        echo 'var y=(e.clientY-r.top)/r.height-.5;';
        echo 'var sc=1+str/600;';
        echo 'item.style.transform="translate("+(x*str)+"px,"+(y*str)+"px) scale("+sc+")";';
        echo '});';
        echo 'item.addEventListener("mouseleave",function(){item.style.transform=""});';
        echo '});});';
        echo '}';

        // ── Filmstrip auto-scroll ──
        echo 'function initFilmstrip(){';
        echo 'if(rm)return;';
        echo 'document.querySelectorAll("[data-pg-filmstrip]").forEach(function(el){';
        echo 'var speed=0.5,paused=false;';
        echo 'el.addEventListener("mouseenter",function(){paused=true});';
        echo 'el.addEventListener("mouseleave",function(){paused=false});';
        echo 'function tick(){if(!paused){el.scrollLeft+=speed;if(el.scrollLeft>=el.scrollWidth-el.clientWidth)el.scrollLeft=0}requestAnimationFrame(tick)}';
        echo 'requestAnimationFrame(tick);';
        echo '});';
        echo '}';

        // ── Init ──
        echo 'function init(){initReveal();initTilt();initMagnetic();initFilmstrip()}';
        echo 'if(document.readyState==="loading"){document.addEventListener("DOMContentLoaded",init)}else{init()}';
        echo '})();';
        echo '</script>';
    }
}
