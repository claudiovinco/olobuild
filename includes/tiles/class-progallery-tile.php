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
        'video_preview'       => 'poster',
        'layout'              => 'grid',
        'layout_family'       => 'classic',
        'puzzle_style'        => 'classic',
        'columns'             => 3,
        'gap'                 => 8,
        'img_height'          => '250px',
        'object_fit'          => 'cover',
        'thumb_radius'        => 8,
        'rows'                => 0,
        'mobile_columns'      => 2,
        'expand_ratio'        => 4,
        'expand_shrink'       => 0.5,
        'expand_speed'        => 500,
        'parallax_height'     => 1500,
        'parallax_intensity'  => 50,
        // Drift
        'drift_height'        => 1200,
        'drift_intensity'     => 60,
        'drift_rotation'      => 12,
        // Cascade
        'cascade_spread'      => 60,
        'cascade_overlap'     => 40,
        'cascade_rotation'    => 8,
        // Metro
        'metro_cell_height'   => 200,
        'filmstrip_item_width'  => 280,
        'filmstrip_center_zoom' => 1.15,
        'filmstrip_side_tilt'   => 35,
        'filmstrip_autoplay'    => false,
        'filmstrip_speed'       => 4,
        'filmstrip_dots'        => 'dots',
        'filmstrip_dots_color'  => '',
        // Strip (nastro)
        'strip_arrows'          => false,
        'strip_arrows_style'    => 'chevron',
        'strip_arrows_size'     => 36,
        'strip_arrows_color'    => '#ffffff',
        'strip_arrows_bg'       => 'rgba(0,0,0,0.4)',
        'strip_height'          => 280,
        'strip_item_width'      => 300,
        'strip_rows'            => 2,
        'strip_speed'           => 30,
        'strip_pause_hover'     => true,
        'strip_direction'       => 'left',
        'strip_fade_edges'      => true,
        'entrance'            => 'none',
        'entrance_stagger'    => 120,
        'entrance_duration'   => 600,
        'hover_effect'        => 'zoom',
        'hover_zoom_scale'    => 1.08,
        'hover_tilt_angle'    => 10,
        'hover_magnetic_strength' => 24,
        'hover_glow_color'    => '',
        'hover_glow_spread'   => 20,
        'hover_frame_in'      => false,
        'hover_caption'       => 'none',
        'hover_caption_bg'    => 'rgba(0,0,0,0.6)',
        'hover_caption_color' => '#ffffff',
        'hover_caption_weight' => '700',
        'hover_frame_inset'   => 10,
        'continuous'          => '',
        'continuous_speed'    => 20,
        'filter'              => 'none',
        'filter_hover_restore' => false,
        'duotone_dark'        => '#1a1a2e',
        'duotone_light'       => '#e94560',
        'duotone_intensity'   => 80,
        'frame'               => 'none',
        'frame_color'         => '#ffffff',
        'frame_inset_padding' => 10,
        'anim_border'         => 'none',
        'anim_border_color'   => '#ffffff',
        'anim_border_thickness' => 2,
        'anim_border_inset'   => 20,
        'anim_border_speed'   => 3,
        'lightbox'            => true,
        'lightbox_animation'  => 'slide',
        'lightbox_thumbs'     => 'none',
        'lightbox_thumbs_rows' => '1',
        'show_caption'        => false,
        'more_bg'             => 'rgba(0,0,0,0.55)',
        'more_color'          => '#ffffff',
        'more_size'           => 28,
        'shadow'              => 'none',
        'border_width'        => '0',
        'border_color'        => '',
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
            if ( is_array( $img ) && ( $img['type'] ?? '' ) === 'video' ) {
                return ! empty( $img['url'] ) || ! empty( $img['embed'] );
            }
            return is_array( $img ) ? ! empty( $img['url'] ) : ! empty( $img );
        });
        $images = array_values( $images );

        if ( empty( $images ) ) {
            return '<div style="padding:40px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF)">Aggiungi immagini alla Pro Gallery</div>';
        }

        $layout       = $s['layout'] ?: 'grid';
        // Backward compat: filmstrip → strip_coverflow
        if ( $layout === 'filmstrip' ) { $layout = 'strip_coverflow'; }
        $cols         = max( 2, min( 6, absint( $s['columns'] ) ) );
        $gap          = max( 0, min( 24, absint( $s['gap'] ) ) );
        $radius       = Olo_Tile_Utils::border_radius( $s['thumb_radius'] ?? 0 );
        $radius_raw   = is_array( $s["thumb_radius"] ?? 0 ) ? intval( $s["thumb_radius"]["tl"] ?? 0 ) : absint( $s["thumb_radius"] ?? 0 );
        $radius_css   = $this->build_border_radius_css( $s["thumb_radius"] ?? 0 );
        $img_height   = esc_attr( $s['img_height'] ?: '250px' );
        $object_fit   = esc_attr( $s['object_fit'] ?: 'cover' );
        $rows         = absint( $s['rows'] );
        $mob_cols     = max( 1, min( 4, absint( $s['mobile_columns'] ) ) );
        $exp_ratio    = max( 2.0, min( 6.0, floatval( $s['expand_ratio'] ) ) );
        $exp_shrink   = max( 0.2, min( 1.0, floatval( $s['expand_shrink'] ) ) );
        $exp_speed    = max( 200, min( 1000, absint( $s['expand_speed'] ) ) );
        $plx_height   = max( 800, min( 3000, absint( $s['parallax_height'] ) ) );
        $plx_intensity = max( 10, min( 100, absint( $s['parallax_intensity'] ) ) );
        // Drift
        $drift_height    = max( 600, min( 2500, absint( $s['drift_height'] ) ) );
        $drift_intensity = max( 10, min( 100, absint( $s['drift_intensity'] ) ) );
        $drift_rotation  = max( 0, min( 25, absint( $s['drift_rotation'] ) ) );
        // Cascade
        $cascade_spread   = max( 20, min( 100, absint( $s['cascade_spread'] ) ) );
        $cascade_overlap  = max( 10, min( 80, absint( $s['cascade_overlap'] ) ) );
        $cascade_rotation = max( 0, min( 20, absint( $s['cascade_rotation'] ) ) );
        // Metro
        $metro_cell_h     = max( 100, min( 400, absint( $s['metro_cell_height'] ) ) );
        $film_width   = max( 180, min( 450, absint( $s['filmstrip_item_width'] ) ) );
        $film_zoom    = max( 1.0, min( 1.5, floatval( $s['filmstrip_center_zoom'] ) ) );
        $film_tilt    = max( 0, min( 60, absint( $s['filmstrip_side_tilt'] ) ) );
        $film_auto    = ! empty( $s['filmstrip_autoplay'] );
        $film_speed   = max( 2.0, min( 8.0, floatval( $s['filmstrip_speed'] ) ) );
        $film_dots    = in_array( $s['filmstrip_dots'] ?? '', [ 'dots', 'lines', 'progress', 'fraction', 'none' ], true ) ? $s['filmstrip_dots'] : 'dots';
        $film_dots_c  = $this->safe_color_css( $s['filmstrip_dots_color'] ?? '' ) ?: '';
        // Padding verticale per compensare zoom 3D del coverflow
        $film_pad     = min( 80, max( 25, (int) ceil( intval( $img_height ?: 250 ) * ( $film_zoom - 1 ) / 2 ) + 15 ) );
        // Strip settings
        $strip_height   = max( 150, min( 500, absint( $s['strip_height'] ) ) );
        $strip_item_w   = max( 150, min( 500, absint( $s['strip_item_width'] ) ) );
        $strip_rows_n   = max( 2, min( 3, absint( $s['strip_rows'] ) ) );
        $strip_speed    = max( 10, min( 60, absint( $s['strip_speed'] ) ) );
        $strip_pause    = ! empty( $s['strip_pause_hover'] );
        $strip_dir      = ( $s['strip_direction'] === 'right' ) ? 'right' : 'left';
        $strip_fade     = ! empty( $s['strip_fade_edges'] );
        $video_preview  = in_array( $s['video_preview'] ?? '', [ 'poster', 'autoplay' ], true ) ? $s['video_preview'] : 'poster';
        $is_strip       = ( str_starts_with( $layout, 'strip' ) );
        $is_strip_drag  = in_array( $layout, [ 'strip', 'strip_collage', 'strip_multi' ], true );
        $is_strip_auto  = in_array( $layout, [ 'strip_marquee', 'strip_split' ], true );
        $is_coverflow   = ( $layout === 'strip_coverflow' );
        $strip_arrows   = $is_strip && ! $is_coverflow && ! empty( $s['strip_arrows'] );
        $sa_style       = in_array( $s['strip_arrows_style'] ?? '', [ 'chevron', 'arrow', 'circle', 'square', 'pill', 'minimal' ], true ) ? $s['strip_arrows_style'] : 'chevron';
        $sa_size        = max( 24, min( 60, absint( $s['strip_arrows_size'] ) ) );
        $sa_color       = $this->safe_color_css( $s['strip_arrows_color'] ) ?: '#ffffff';
        $sa_bg          = $this->safe_color_css( $s['strip_arrows_bg'] ) ?: 'rgba(0,0,0,0.4)';
        $uid            = 'olo-pg-' . wp_unique_id();

        // Visible images
        $total       = count( $images );
        $max_visible = ( $rows > 0 ) ? $cols * $rows : $total;
        $extra       = max( 0, $total - $max_visible );

        // Scattered grid dimensions (pre-computed for CSS aspect-ratio + item positioning)
        if ( $layout === 'scattered' ) {
            $total_vis = min( $max_visible, $total );
            $cols_s    = max( 2, (int) ceil( sqrt( $total_vis ) ) );
            $rows_s    = (int) ceil( $total_vis / $cols_s );
        }

        // Parallax grid dimensions
        if ( $layout === 'parallax' ) {
            $total_vis_p = min( $max_visible, $total );
            $cols_p      = min( 4, max( 2, (int) ceil( sqrt( $total_vis_p ) ) ) );
            $rows_p      = (int) ceil( $total_vis_p / $cols_p );
        }

        // Drift grid dimensions
        if ( $layout === 'drift' ) {
            $total_vis_d = min( $max_visible, $total );
            $cols_d      = min( 4, max( 2, (int) ceil( sqrt( $total_vis_d ) ) ) );
            $rows_d      = (int) ceil( $total_vis_d / $cols_d );
        }

        // Cascade dimensions
        if ( $layout === 'cascade' ) {
            $total_vis_c = min( $max_visible, $total );
        }

        // Metro pattern: compute grid areas
        if ( $layout === 'metro' ) {
            $total_vis_m = min( $max_visible, $total );
        }

        // Entrance
        $entrance    = $s['entrance'] ?: 'none';
        $ent_stagger = max( 80, min( 400, absint( $s['entrance_stagger'] ) ) );
        $ent_dur     = max( 300, min( 1200, absint( $s['entrance_duration'] ) ) );

        // Hover
        $hover       = $s['hover_effect'] ?: 'none';
        $hz_scale    = max( 1.05, min( 1.30, floatval( $s['hover_zoom_scale'] ) ) );
        $tilt_angle  = max( 5, min( 20, absint( $s['hover_tilt_angle'] ) ) );
        $mag_str     = max( 8, min( 60, absint( $s['hover_magnetic_strength'] ) ) );
        $glow_color  = $this->safe_color_css( $s['hover_glow_color'] ) ?: '#6366f1';
        $glow_spread = max( 8, min( 50, absint( $s['hover_glow_spread'] ) ) );
        $hcaption    = $s['hover_caption'] ?: 'none';
        $hcap_bg     = $this->safe_color_css( $s['hover_caption_bg'] ) ?: 'rgba(0,0,0,0.6)';
        $hcap_color  = $this->safe_color_css( $s['hover_caption_color'] ) ?: '#ffffff';
        $frame_inset = max( 4, min( 40, absint( $s['hover_frame_inset'] ) ) );

        // Continuous (multi — comma-separated string 'float,drift' or legacy array)
        $cont_raw = $s['continuous'] ?: '';
        if ( is_string( $cont_raw ) && $cont_raw !== '' && $cont_raw !== 'none' ) {
            $cont_raw = array_map( 'trim', explode( ',', $cont_raw ) );
        } elseif ( ! is_array( $cont_raw ) ) {
            $cont_raw = [];
        }
        $cont_effects = array_values( array_filter( $cont_raw, function( $v ) {
            return $v && $v !== 'none';
        }));
        $cont_speed  = max( 10, min( 40, absint( $s['continuous_speed'] ) ) );

        // Filter
        $filter      = $s['filter'] ?: 'none';
        $filter_hover = ! empty( $s['filter_hover_restore'] );
        $duo_dark    = $this->safe_color_css( $s['duotone_dark'] ) ?: '#1a1a2e';
        $duo_light   = $this->safe_color_css( $s['duotone_light'] ) ?: '#e94560';
        // Duotone SVG float values (0-1)
        $dr = $dg = $db = $lr = $lg = $lb = '0.000';
        $duo_k2 = '0.800'; $duo_k3 = '0.200';
        if ( $filter === 'duotone' ) {
            $dr = number_format( hexdec( substr( $duo_dark, 1, 2 ) ) / 255, 3, '.', '' );
            $dg = number_format( hexdec( substr( $duo_dark, 3, 2 ) ) / 255, 3, '.', '' );
            $db = number_format( hexdec( substr( $duo_dark, 5, 2 ) ) / 255, 3, '.', '' );
            $lr = number_format( hexdec( substr( $duo_light, 1, 2 ) ) / 255, 3, '.', '' );
            $lg = number_format( hexdec( substr( $duo_light, 3, 2 ) ) / 255, 3, '.', '' );
            $lb = number_format( hexdec( substr( $duo_light, 5, 2 ) ) / 255, 3, '.', '' );
            $intensity = max( 0, min( 100, absint( $s['duotone_intensity'] ) ) ) / 100;
            $duo_k2 = number_format( $intensity, 3, '.', '' );
            $duo_k3 = number_format( 1 - $intensity, 3, '.', '' );
        }

        // Frame
        $frame       = $s['frame'] ?: 'none';
        $frame_color = $this->safe_color_css( $s['frame_color'] ) ?: '#ffffff';

        // Animated border (backward compat: old hover_frame_in toggle → frame-in)
        $anim_border = $s['anim_border'] ?: 'none';
        if ( $anim_border === 'none' && ! empty( $s['hover_frame_in'] ) ) {
            $anim_border = 'frame-in';
        }
        $ab_color    = $this->safe_color_css( $s['anim_border_color'] ) ?: '#ffffff';
        $ab_thick    = max( 1, min( 6, absint( $s['anim_border_thickness'] ) ) );
        $ab_inset    = max( 4, min( 50, absint( $s['anim_border_inset'] ) ) );
        $ab_speed    = max( 1, min( 10, floatval( $s['anim_border_speed'] ) ) );

        // Lightbox
        $lightbox    = ! empty( $s['lightbox'] );
        $lb_anim     = esc_attr( $s['lightbox_animation'] ?? 'slide' );
        $lb_thumbs   = $s['lightbox_thumbs'] ?? 'none';
        $lb_rows     = $s['lightbox_thumbs_rows'] ?? '1';
        $lb_custom   = $lightbox && in_array( $lb_thumbs, [ 'bottom', 'right', 'left' ], true );
        $show_cap    = ! empty( $s['show_caption'] );

        // +N
        $more_bg     = $this->safe_color_css( $s['more_bg'] ?: 'rgba(0,0,0,0.55)' );
        $more_color  = $this->safe_color_css( $s['more_color'] ?: '#ffffff' );
        $more_size   = max( 16, min( 48, absint( $s['more_size'] ) ) );

        // Shadow / border
        $shadow = Olo_Tile_Utils::shadow_value( $s, 'shadow' );
        $bw     = max( 0, min( 10, absint( $s['border_width'] ) ) );
        $bc     = $this->safe_color_css( $s['border_color'] ) ?: 'var(--olo-color-border, #E5E7EB)';

        // CSS filter map
        $css_filter = '';
        $filter_map = [
            'grayscale'     => 'grayscale(100%)',
            'sepia'         => 'sepia(80%)',
            'high-contrast' => 'contrast(140%) saturate(120%)',
            'warm'          => 'sepia(25%) saturate(130%) hue-rotate(-10deg)',
            'cool'          => 'saturate(80%) hue-rotate(20deg) brightness(105%)',
            'vintage'       => 'sepia(40%) contrast(90%) brightness(95%) saturate(80%)',
        ];
        if ( $filter !== 'none' && isset( $filter_map[ $filter ] ) ) {
            $css_filter = $filter_map[ $filter ];
        }

        ob_start();

        // ─── STYLE ───
        echo '<style>';

        // Base layout
        if ( $layout === 'justified' ) {
            echo ".{$uid}{display:flex;flex-wrap:wrap;gap:{$gap}px}";
            echo ".{$uid} .olo-pg-item{position:relative;overflow:hidden;border-radius:{$radius};height:{$img_height};flex-grow:1;min-width:120px}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:cover;display:block}";
        } elseif ( $layout === 'masonry' ) {
            echo ".{$uid}{column-count:{$cols};column-gap:{$gap}px}";
            echo ".{$uid} .olo-pg-item{break-inside:avoid;display:inline-block;width:100%;margin-bottom:{$gap}px;position:relative;overflow:hidden;border-radius:{$radius}}";
            echo ".{$uid} .olo-pg-item img{width:100%;display:block;object-fit:cover}";
        } elseif ( $layout === 'scattered' ) {
            // Bordi animati con box-shadow: contenere l'overflow e aggiungere padding per edge items
            $scatter_uses_shadow = in_array( $anim_border, [ 'neon', 'pulse', 'radar' ], true );
            if ( $scatter_uses_shadow ) {
                $scatter_pad = max( 15, $ab_thick * 4 );
                echo ".{$uid}{position:relative;aspect-ratio:{$cols_s}/{$rows_s};overflow:hidden;padding:{$scatter_pad}px}";
            } else {
                echo ".{$uid}{position:relative;aspect-ratio:{$cols_s}/{$rows_s};overflow:visible}";
            }
            echo ".{$uid} .olo-pg-item{position:absolute;overflow:hidden;border-radius:{$radius};box-shadow:0 4px 12px rgba(0,0,0,.15)}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block}";
        } elseif ( $layout === 'parallax' ) {
            echo ".{$uid}{position:relative;height:{$plx_height}px;overflow:visible}";
            echo ".{$uid} .olo-pg-item{position:absolute;overflow:hidden;border-radius:{$radius};will-change:transform;transition:transform 0.1s linear}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:auto;display:block}";
        } elseif ( $layout === 'drift' ) {
            echo ".{$uid}{position:relative;height:{$drift_height}px;overflow:visible}";
            echo ".{$uid} .olo-pg-item{position:absolute;overflow:hidden;border-radius:{$radius};will-change:transform;transition:transform 0.15s linear}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:auto;display:block}";
        } elseif ( $layout === 'cascade' ) {
            $cascade_h = max( 400, $total_vis_c * 80 + 300 );
            echo ".{$uid}{position:relative;height:{$cascade_h}px;overflow:visible;perspective:1200px}";
            echo ".{$uid} .olo-pg-item{position:absolute;overflow:hidden;border-radius:{$radius};will-change:transform;transition:transform 0.2s ease-out;box-shadow:0 8px 30px rgba(0,0,0,.2)}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block}";
        } elseif ( $layout === 'metro' ) {
            echo ".{$uid}{display:grid;grid-template-columns:repeat({$cols},1fr);grid-auto-rows:{$metro_cell_h}px;grid-auto-flow:dense;gap:{$gap}px}";
            echo ".{$uid} .olo-pg-item{position:relative;overflow:hidden;border-radius:{$radius}}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block}";
            // Pattern: certi item occupano 2x2, 2x1, 1x2
            echo ".{$uid} .olo-pg-item:nth-child(7n+1){grid-column:span 2;grid-row:span 2}";
            echo ".{$uid} .olo-pg-item:nth-child(7n+4){grid-column:span 2}";
            echo ".{$uid} .olo-pg-item:nth-child(7n+6){grid-row:span 2}";
        } elseif ( $is_coverflow ) {
            // Wrapper
            echo ".{$uid}-wrap{position:relative}";
            // Container: flex + scroll-snap + scrollbar invisible
            echo ".{$uid}{display:flex;gap:{$gap}px;overflow-x:auto;scroll-snap-type:x mandatory;scroll-behavior:smooth;-webkit-overflow-scrolling:touch;padding:{$film_pad}px 0;scrollbar-width:none;-ms-overflow-style:none}";
            echo ".{$uid}::-webkit-scrollbar{height:0;background:transparent}";
            echo ".{$uid}::-webkit-scrollbar-thumb{background:transparent}";
            echo ".{$uid}::-webkit-scrollbar-track{background:transparent}";
            echo ".{$uid} .olo-pg-item{flex:0 0 auto;width:{$film_width}px;height:{$img_height};position:relative;overflow:hidden;border-radius:{$radius};scroll-snap-align:center;will-change:transform}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block;-webkit-user-drag:none;user-select:none}";
            echo ".{$uid} .olo-pg-item{-webkit-user-drag:none;user-select:none}";
            // Frecce (z-index 200 — sopra item coverflow che hanno z-index fino a 100)
            echo ".{$uid}-prev,.{$uid}-next{position:absolute;top:50%;transform:translateY(-50%);width:40px;height:40px;border-radius:50%;background:rgba(0,0,0,.45);color:#fff;border:none;font-size:22px;line-height:1;cursor:pointer;z-index:200;display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity .3s ease,background .2s ease;backdrop-filter:blur(4px)}";
            echo ".{$uid}-prev{left:8px}";
            echo ".{$uid}-next{right:8px}";
            echo ".{$uid}-wrap:hover .{$uid}-prev,.{$uid}-wrap:hover .{$uid}-next{opacity:1}";
            echo ".{$uid}-prev:hover,.{$uid}-next:hover{background:rgba(0,0,0,.7)}";
            // Indicatore posizione
            $dot_clr = $film_dots_c ?: '';
            if ( $film_dots === 'dots' ) {
                $da = $dot_clr ?: 'rgba(0,0,0,.65)';
                $di = $dot_clr ?: 'rgba(0,0,0,.22)';
                $di_op = $dot_clr ? 'opacity:.35;' : '';
                echo ".{$uid}-dots{display:flex;justify-content:center;gap:10px;padding:12px 0;width:80%;max-width:600px;margin:0 auto;position:relative;z-index:11}";
                echo ".{$uid}-dots span{width:12px;height:12px;border-radius:50%;background:{$di};{$di_op}cursor:pointer;transition:transform .2s ease,background .2s ease,opacity .2s ease}";
                echo ".{$uid}-dots span.active{background:{$da};opacity:1;transform:scale(1.25)}";
            } elseif ( $film_dots === 'lines' ) {
                $da = $dot_clr ?: 'rgba(0,0,0,.65)';
                $di = $dot_clr ?: 'rgba(0,0,0,.22)';
                $di_op = $dot_clr ? 'opacity:.35;' : '';
                echo ".{$uid}-dots{display:flex;justify-content:center;gap:6px;padding:12px 0;width:80%;max-width:600px;margin:0 auto;position:relative;z-index:11;align-items:center}";
                echo ".{$uid}-dots span{width:20px;height:3px;border-radius:2px;background:{$di};{$di_op}cursor:pointer;transition:width .3s ease,background .3s ease,opacity .3s ease}";
                echo ".{$uid}-dots span.active{width:36px;background:{$da};opacity:1}";
            } elseif ( $film_dots === 'progress' ) {
                $da = $dot_clr ?: 'rgba(0,0,0,.55)';
                $di = $dot_clr ?: 'rgba(0,0,0,.15)';
                $di_op = $dot_clr ? 'opacity:.25;' : '';
                echo ".{$uid}-dots{padding:10px 0;width:60%;max-width:400px;margin:0 auto;position:relative;z-index:11}";
                echo ".{$uid}-dots .pg-prog-track{height:3px;border-radius:2px;background:{$di};{$di_op}overflow:hidden;position:relative;cursor:pointer}";
                echo ".{$uid}-dots .pg-prog-fill{height:100%;border-radius:2px;background:{$da};transition:width .25s ease;width:0}";
            } elseif ( $film_dots === 'fraction' ) {
                echo ".{$uid}-dots{text-align:center;padding:10px 0;font-size:14px;font-weight:600;color:" . ( $dot_clr ?: 'rgba(0,0,0,.55)' ) . ";font-variant-numeric:tabular-nums;z-index:11;position:relative;letter-spacing:0.05em}";
            }
            // Mobile: frecce nascoste
            echo "@media(max-width:640px){.{$uid} .olo-pg-item{width:" . max( 180, $film_width - 60 ) . "px}.{$uid}-prev,.{$uid}-next{display:none}}";
            echo "@media(hover:none){.{$uid}-prev,.{$uid}-next{display:none}}";
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
            echo ".{$uid} .olo-pg-item{position:relative;overflow:hidden;border-radius:{$radius}}";
            echo ".{$uid} .olo-pg-item:first-child{grid-column:span 2;grid-row:span 2}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block}";
        } elseif ( $layout === 'mosaic' ) {
            echo ".{$uid}{display:grid;grid-template-columns:repeat({$cols},1fr);grid-auto-rows:{$img_height};gap:{$gap}px}";
            echo ".{$uid} .olo-pg-item{position:relative;overflow:hidden;border-radius:{$radius}}";
            echo ".{$uid} .olo-pg-item:nth-child(5n+1){grid-column:span 2;grid-row:span 2}";
            echo ".{$uid} .olo-pg-item:nth-child(5n+4){grid-column:span 2}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block}";
        } elseif ( $layout === 'diagonal' ) {
            echo ".{$uid}{display:grid;grid-template-columns:repeat({$cols},1fr);gap:{$gap}px}";
            echo ".{$uid} .olo-pg-item{position:relative;overflow:hidden;border-radius:{$radius};height:{$img_height}}";
            echo ".{$uid} .olo-pg-item:nth-child(odd){clip-path:polygon(0 0,100% 8%,100% 100%,0 92%)}";
            echo ".{$uid} .olo-pg-item:nth-child(even){clip-path:polygon(0 8%,100% 0,100% 92%,0 100%)}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block}";
        } elseif ( $layout === 'expand' ) {
            $exp_rows_count = (int) ceil( min( $max_visible, $total ) / $cols );
            $exp_h_px       = intval( $img_height ) ?: 250;
            $exp_total_h    = $exp_rows_count * $exp_h_px + ( $exp_rows_count - 1 ) * $gap;
            $exp_speed_s    = number_format( $exp_speed / 1000, 2 );
            echo ".{$uid}{display:grid;grid-template-columns:repeat({$cols},1fr);grid-template-rows:repeat({$exp_rows_count},1fr);gap:{$gap}px;height:{$exp_total_h}px;transition:grid-template-columns {$exp_speed_s}s cubic-bezier(.25,.46,.45,.94),grid-template-rows {$exp_speed_s}s cubic-bezier(.25,.46,.45,.94)}";
            echo ".{$uid} .olo-pg-item{position:relative;overflow:hidden;border-radius:{$radius};min-width:0;min-height:0}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:cover;display:block}";
        } elseif ( $layout === 'strip' || $layout === 'strip_collage' ) {
            // Nastro orizzontale (riga singola) — drag-to-scroll con momentum
            $fade_css = $strip_fade ? "mask-image:linear-gradient(to right,transparent,black 6%,black 94%,transparent);-webkit-mask-image:linear-gradient(to right,transparent,black 6%,black 94%,transparent);" : '';
            echo ".{$uid}{display:flex;gap:{$gap}px;overflow-x:auto;align-items:center;scrollbar-width:none;-ms-overflow-style:none;{$fade_css}}";
            echo ".{$uid}::-webkit-scrollbar{display:none}";
            echo ".{$uid} .olo-pg-item{flex:0 0 auto;width:{$strip_item_w}px;height:{$strip_height}px;position:relative;overflow:hidden;border-radius:{$radius}}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block;-webkit-user-drag:none;user-select:none}";
        } elseif ( $layout === 'strip_multi' ) {
            // Nastro multi-riga — CSS Grid con N righe, scrollabile
            $fade_css = $strip_fade ? "mask-image:linear-gradient(to right,transparent,black 6%,black 94%,transparent);-webkit-mask-image:linear-gradient(to right,transparent,black 6%,black 94%,transparent);" : '';
            echo ".{$uid}{display:grid;grid-template-rows:repeat({$strip_rows_n},1fr);grid-auto-flow:column;grid-auto-columns:{$strip_item_w}px;gap:{$gap}px;overflow-x:auto;height:{$strip_height}px;scrollbar-width:none;-ms-overflow-style:none;{$fade_css}}";
            echo ".{$uid}::-webkit-scrollbar{display:none}";
            echo ".{$uid} .olo-pg-item{position:relative;overflow:hidden;border-radius:{$radius}}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block;-webkit-user-drag:none;user-select:none}";
        } elseif ( $layout === 'strip_marquee' ) {
            // Nastro automatico — CSS animation loop infinito
            $fade_css = $strip_fade ? "mask-image:linear-gradient(to right,transparent,black 6%,black 94%,transparent);-webkit-mask-image:linear-gradient(to right,transparent,black 6%,black 94%,transparent);" : '';
            $dir_css = ( $strip_dir === 'right' ) ? 'animation-direction:reverse;' : '';
            echo ".{$uid}{overflow:hidden;{$fade_css}}";
            echo ".{$uid}-track{display:flex;gap:{$gap}px;width:max-content;animation:{$uid}-marquee {$strip_speed}s linear infinite;{$dir_css}}";
            if ( $strip_pause ) {
                echo ".{$uid}:hover .{$uid}-track{animation-play-state:paused}";
            }
            echo ".{$uid} .olo-pg-item{flex:0 0 auto;width:{$strip_item_w}px;height:{$strip_height}px;position:relative;overflow:hidden;border-radius:{$radius}}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block}";
            if ( $lightbox ) {
                echo ".{$uid} .olo-pg-dup{cursor:pointer}";
            } else {
                echo ".{$uid} .olo-pg-dup{pointer-events:none}";
            }
            echo "@keyframes {$uid}-marquee{to{transform:translateX(-50%)}}";
        } elseif ( $layout === 'strip_split' ) {
            // Nastro doppio — due righe in direzioni opposte
            $split_row_h = max( 80, (int) round( ( $strip_height - $gap ) / 2 ) );
            $fade_css = $strip_fade ? "mask-image:linear-gradient(to right,transparent,black 6%,black 94%,transparent);-webkit-mask-image:linear-gradient(to right,transparent,black 6%,black 94%,transparent);" : '';
            echo ".{$uid}{display:flex;flex-direction:column;gap:{$gap}px}";
            echo ".{$uid}-row{overflow:hidden;{$fade_css}}";
            echo ".{$uid}-track{display:flex;gap:{$gap}px;width:max-content;animation:{$uid}-marquee {$strip_speed}s linear infinite}";
            echo ".{$uid}-track-rev{animation-direction:reverse}";
            if ( $strip_pause ) {
                echo ".{$uid}:hover .{$uid}-track{animation-play-state:paused}";
            }
            echo ".{$uid} .olo-pg-item{flex:0 0 auto;width:{$strip_item_w}px;height:{$split_row_h}px;position:relative;overflow:hidden;border-radius:{$radius}}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block}";
            if ( $lightbox ) {
                echo ".{$uid} .olo-pg-dup{cursor:pointer}";
            } else {
                echo ".{$uid} .olo-pg-dup{pointer-events:none}";
            }
            echo "@keyframes {$uid}-marquee{to{transform:translateX(-50%)}}";
        } else {
            // Default: grid
            echo ".{$uid}{display:grid;grid-template-columns:repeat({$cols},1fr);gap:{$gap}px}";
            echo ".{$uid} .olo-pg-item{position:relative;overflow:hidden;border-radius:{$radius};height:{$img_height}}";
            echo ".{$uid} .olo-pg-item img{width:100%;height:100%;object-fit:{$object_fit};display:block;transition:transform .5s cubic-bezier(.25,.46,.45,.94),filter .5s ease}";
        }

        // ─── CSS Filter ───
        if ( $css_filter ) {
            echo ".{$uid} .olo-pg-item img,.{$uid} .olo-pg-item video{filter:{$css_filter}}";
            if ( $filter_hover ) {
                echo ".{$uid} .olo-pg-item:hover img,.{$uid} .olo-pg-item:hover video{filter:none}";
            }
        }

        // ─── Duotone (SVG feComponentTransfer) ───
        if ( $filter === 'duotone' ) {
            echo ".{$uid} .olo-pg-item img,.{$uid} .olo-pg-item video{filter:url(#duo-{$uid});transition:filter .4s ease}";
            if ( $filter_hover ) {
                echo ".{$uid} .olo-pg-item:hover img,.{$uid} .olo-pg-item:hover video{filter:none}";
            }
        }

        // ─── Hover effects ───
        if ( $hover === 'zoom' ) {
            echo ".{$uid} .olo-pg-item img,.{$uid} .olo-pg-item video{transition:transform .5s cubic-bezier(.25,.46,.45,.94)}";
            echo ".{$uid} .olo-pg-item:hover img,.{$uid} .olo-pg-item:hover video{transform:scale({$hz_scale})}";
        } elseif ( $hover === 'lift' ) {
            echo ".{$uid}{overflow:visible}";
            echo ".{$uid} .olo-pg-item{transition:transform .4s ease,box-shadow .4s ease;position:relative;z-index:0}";
            echo ".{$uid} .olo-pg-item:hover{transform:translateY(-8px);box-shadow:0 14px 32px rgba(0,0,0,.25);z-index:2}";
        } elseif ( $hover === 'tilt3d' ) {
            echo ".{$uid} .olo-pg-item{perspective:600px;transform-style:preserve-3d;transition:transform .4s ease}";
            echo ".{$uid} .olo-pg-item img,.{$uid} .olo-pg-item video{transition:transform .4s ease}";
            // JS tilt handled by shared script
        } elseif ( $hover === 'glow' ) {
            // Convert hex to rgba for glow
            $gr = hexdec( substr( $glow_color, 1, 2 ) );
            $gg = hexdec( substr( $glow_color, 3, 2 ) );
            $gb = hexdec( substr( $glow_color, 5, 2 ) );
            echo ".{$uid} .olo-pg-item{box-shadow:0 0 0 0 rgba({$gr},{$gg},{$gb},0);transition:box-shadow .4s ease}";
            echo ".{$uid} .olo-pg-item:hover{box-shadow:0 0 {$glow_spread}px 4px rgba({$gr},{$gg},{$gb},.45)}";
        } elseif ( $hover === 'blur-peers' ) {
            echo ".{$uid} .olo-pg-item{filter:blur(0);transition:filter .4s ease}";
            echo ".{$uid}:hover .olo-pg-item{filter:blur(3px)!important}";
            echo ".{$uid}:hover .olo-pg-item:hover{filter:blur(0)!important}";
            if ( $css_filter ) {
                echo ".{$uid}:hover .olo-pg-item:hover img{filter:none}";
            }
        } elseif ( $hover === 'magnetic' ) {
            echo ".{$uid} .olo-pg-item{transition:transform .15s ease-out}";
            // JS magnetic handled by shared script
        }

        // (hover_frame_in migrata in bordi animati)

        // ─── Hover caption ───
        $hcap_weight = max( 100, min( 900, absint( $s['hover_caption_weight'] ?? 700 ) ) );
        if ( $hcaption !== 'none' ) {
            if ( $hcaption === 'centered' ) {
                echo ".{$uid} .olo-pg-cap{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;text-align:center;color:{$hcap_color};font-size:16px;font-weight:{$hcap_weight};letter-spacing:.03em;text-shadow:0 1px 6px rgba(0,0,0,.7);background:transparent;opacity:0;transition:opacity .35s ease;z-index:3;pointer-events:none;padding:12px}";
                echo ".{$uid} .olo-pg-item:hover .olo-pg-cap{opacity:1}";
            } else {
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
                } elseif ( $hcaption === 'frame' ) {
                    echo ".{$uid} .olo-pg-cap{position:absolute;inset:{$frame_inset}px;display:flex;align-items:center;justify-content:center;text-align:center;border:2px solid rgba(255,255,255,.85);background:transparent;color:{$hcap_color};font-size:14px;font-weight:600;letter-spacing:.05em;opacity:0;transition:opacity .4s ease;z-index:3;pointer-events:none;padding:8px}";
                    echo ".{$uid} .olo-pg-item:hover .olo-pg-cap{opacity:1}";
                }
            }
        }

        // ─── Continuous animation keyframes (multi-effect) ───
        // Categories: item-transform (float|drift), img-transform (breathe|rotate-slow|kenburns), shimmer (::after)
        // One per category max — first wins if conflict
        // NOTE: coverflow uses inline transforms on .olo-pg-item — skip item-level animations
        $skip_item_anim = ( $is_coverflow || $is_strip );
        if ( ! empty( $cont_effects ) ) {
            $item_anim = '';  // animation on .olo-pg-item
            $img_anim  = '';  // animation on .olo-pg-item img
            $has_shimmer = false;

            foreach ( $cont_effects as $ce ) {
                if ( $ce === 'float' && ! $item_anim && ! $skip_item_anim ) {
                    echo "@keyframes {$uid}-float{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}";
                    $item_anim = "{$uid}-float {$cont_speed}s ease-in-out infinite";
                } elseif ( $ce === 'drift' && ! $item_anim && ! $skip_item_anim ) {
                    echo "@keyframes {$uid}-drift{0%{transform:translate(0,0)}25%{transform:translate(4px,-3px)}50%{transform:translate(-3px,2px)}75%{transform:translate(2px,4px)}100%{transform:translate(0,0)}}";
                    $item_anim = "{$uid}-drift {$cont_speed}s ease-in-out infinite";
                } elseif ( $ce === 'breathe' && ! $img_anim ) {
                    echo "@keyframes {$uid}-breathe{0%,100%{transform:scale(1)}50%{transform:scale(1.03)}}";
                    $img_anim = "{$uid}-breathe {$cont_speed}s ease-in-out infinite";
                } elseif ( $ce === 'rotate-slow' && ! $img_anim ) {
                    echo "@keyframes {$uid}-rotslow{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}";
                    $img_anim = "{$uid}-rotslow {$cont_speed}s linear infinite";
                } elseif ( $ce === 'kenburns' && ! $img_anim ) {
                    echo "@keyframes {$uid}-kb{0%{transform:scale(1) translate(0,0)}33%{transform:scale(1.12) translate(-2%,-1%)}66%{transform:scale(1.08) translate(1%,0.5%)}100%{transform:scale(1) translate(0,0)}}";
                    $img_anim = "{$uid}-kb {$cont_speed}s ease-in-out infinite";
                } elseif ( $ce === 'shimmer' && ! $has_shimmer ) {
                    echo "@keyframes {$uid}-shimmer{0%{transform:translateX(-100%) rotate(25deg)}35%{transform:translateX(200%) rotate(25deg)}100%{transform:translateX(200%) rotate(25deg)}}";
                    echo ".{$uid} .olo-pg-item{position:relative;overflow:hidden}";
                    echo ".{$uid} .olo-pg-item::after{content:'';position:absolute;inset:-50%;z-index:2;background:linear-gradient(90deg,transparent 30%,rgba(255,255,255,.12) 50%,transparent 70%);animation:{$uid}-shimmer {$cont_speed}s ease-in-out infinite;pointer-events:none}";
                    $has_shimmer = true;
                }
            }

            if ( $item_anim ) {
                echo ".{$uid} .olo-pg-item{animation:{$item_anim}}";
            }
            if ( $img_anim ) {
                echo ".{$uid} .olo-pg-item img,.{$uid} .olo-pg-item video{animation:{$img_anim}}";
            }

            // nth-child delay stagger for variety
            for ( $d = 1; $d <= 6; $d++ ) {
                $delay = round( $cont_speed / 6 * $d, 1 );
                echo ".{$uid} .olo-pg-item:nth-child({$d}n) img,.{$uid} .olo-pg-item:nth-child({$d}n) video,.{$uid} .olo-pg-item:nth-child({$d}n),.{$uid} .olo-pg-item:nth-child({$d}n)::after{animation-delay:-{$delay}s}";
            }
        }

        // ─── Frame styles ───
        if ( $frame === 'polaroid' ) {
            echo ".{$uid} .olo-pg-item{background:{$frame_color};padding:8px 8px 32px 8px;border-radius:2px;box-shadow:0 2px 8px rgba(0,0,0,.12)}";
            echo ".{$uid} .olo-pg-item img,.{$uid} .olo-pg-item video{border-radius:0}";
        } elseif ( $frame === 'rounded' ) {
            echo ".{$uid} .olo-pg-item{border-radius:50%;overflow:hidden}";
            echo ".{$uid} .olo-pg-item img,.{$uid} .olo-pg-item video{border-radius:50%}";
        } elseif ( $frame === 'shadow-box' ) {
            echo ".{$uid} .olo-pg-item{background:{$frame_color};padding:6px;box-shadow:0 4px 16px rgba(0,0,0,.18),inset 0 1px 0 rgba(255,255,255,.5);border-radius:" . ( $radius_raw + 2 ) . "px}";
        } elseif ( $frame === 'torn' ) {
            echo ".{$uid} .olo-pg-item{clip-path:polygon(0 2%,4% 0,10% 3%,18% 0,24% 2%,32% 0,40% 1%,48% 0,56% 2%,64% 0,72% 1%,80% 0,88% 2%,94% 0,100% 3%,97% 10%,100% 18%,97% 26%,100% 34%,97% 42%,100% 50%,97% 58%,100% 66%,97% 74%,100% 82%,97% 90%,100% 97%,96% 100%,90% 97%,82% 100%,76% 97%,68% 100%,60% 99%,52% 100%,44% 97%,36% 100%,28% 99%,20% 100%,12% 97%,6% 100%,0 97%,3% 90%,0 82%,3% 74%,0 66%,3% 58%,0 50%,3% 42%,0 34%,3% 26%,0 18%,3% 10%)}";
        } elseif ( $frame === 'tape' ) {
            echo ".{$uid} .olo-pg-item{position:relative}";
            echo ".{$uid} .olo-pg-item::after{content:'';position:absolute;top:-6px;left:50%;transform:translateX(-50%);width:50px;height:18px;background:rgba(255,255,200,.7);border-radius:2px;z-index:4;box-shadow:0 1px 3px rgba(0,0,0,.1)}";
        } elseif ( $frame === 'inset' ) {
            $inset_pad = max( 3, min( 40, absint( $s['frame_inset_padding'] ) ) );
            echo ".{$uid} .olo-pg-item{position:relative}";
            echo ".{$uid} .olo-pg-item::before{content:'';position:absolute;inset:{$inset_pad}px;border:1px solid {$frame_color};border-radius:" . max( 0, $radius_raw - $inset_pad ) . "px;z-index:5;pointer-events:none}";
        }

        // ─── Bordi animati ───
        if ( $anim_border === 'frame-in' ) {
            // Cornice entrante — bordo che appare al hover dall'esterno verso l'interno
            echo ".{$uid} .olo-pg-item::before{content:'';position:absolute;inset:0;border:2px solid transparent;z-index:4;pointer-events:none;transition:inset .4s ease,border-color .3s ease;border-radius:{$radius}}";
            echo ".{$uid} .olo-pg-item:hover::before{inset:{$ab_inset}px;border-color:{$ab_color};border-radius:" . max( 0, $radius_raw - $ab_inset ) . "px}";
        } elseif ( $anim_border === 'neon' ) {
            // Bagliore neon pulsante via box-shadow
            echo "@keyframes {$uid}-neon{0%,100%{box-shadow:0 0 {$ab_thick}px {$ab_color},0 0 " . ( $ab_thick * 4 ) . "px {$ab_color},0 0 " . ( $ab_thick * 8 ) . "px {$ab_color}}50%{box-shadow:0 0 " . ( $ab_thick * 2 ) . "px {$ab_color},0 0 " . ( $ab_thick * 6 ) . "px {$ab_color},0 0 " . ( $ab_thick * 12 ) . "px {$ab_color}}}";
            echo ".{$uid} .olo-pg-item{animation:{$uid}-neon {$ab_speed}s ease-in-out infinite}";
        } elseif ( $anim_border === 'ants' ) {
            // Formiche (marching ants) — padding crea spazio, gradient sui 4 lati dell'item
            $dLen = $ab_thick * 6;
            echo "@keyframes {$uid}-ants{100%{background-position:{$dLen}px 0,calc(100% - {$dLen}px) 100%,0 calc(100% - {$dLen}px),100% {$dLen}px}}";
            echo ".{$uid} .olo-pg-item{padding:{$ab_thick}px;background-image:linear-gradient(90deg,{$ab_color} 50%,transparent 50%),linear-gradient(90deg,{$ab_color} 50%,transparent 50%),linear-gradient(0deg,{$ab_color} 50%,transparent 50%),linear-gradient(0deg,{$ab_color} 50%,transparent 50%);background-size:{$dLen}px {$ab_thick}px,{$dLen}px {$ab_thick}px,{$ab_thick}px {$dLen}px,{$ab_thick}px {$dLen}px;background-position:0 0,100% 100%,0 100%,100% 0;background-repeat:repeat-x,repeat-x,repeat-y,repeat-y;animation:{$uid}-ants {$ab_speed}s linear infinite}";
        } elseif ( $anim_border === 'corners' ) {
            // Angoli — padding crea spazio, 8 gradient per 4 angoli L
            $cLen = 20 + $ab_thick * 4;
            echo ".{$uid} .olo-pg-item{padding:{$ab_thick}px;background-image:linear-gradient({$ab_color},{$ab_color}),linear-gradient({$ab_color},{$ab_color}),linear-gradient({$ab_color},{$ab_color}),linear-gradient({$ab_color},{$ab_color}),linear-gradient({$ab_color},{$ab_color}),linear-gradient({$ab_color},{$ab_color}),linear-gradient({$ab_color},{$ab_color}),linear-gradient({$ab_color},{$ab_color});background-size:0 {$ab_thick}px,{$ab_thick}px 0,0 {$ab_thick}px,{$ab_thick}px 0,0 {$ab_thick}px,{$ab_thick}px 0,0 {$ab_thick}px,{$ab_thick}px 0;background-position:0 0,0 0,100% 0,100% 0,100% 100%,100% 100%,0 100%,0 100%;background-repeat:no-repeat;transition:background-size .4s ease}";
            echo ".{$uid} .olo-pg-item:hover{background-size:{$cLen}px {$ab_thick}px,{$ab_thick}px {$cLen}px,{$cLen}px {$ab_thick}px,{$ab_thick}px {$cLen}px,{$cLen}px {$ab_thick}px,{$ab_thick}px {$cLen}px,{$cLen}px {$ab_thick}px,{$ab_thick}px {$cLen}px}";
        } elseif ( $anim_border === 'pulse' ) {
            // Pulsazione — bordo che appare e scompare
            echo "@keyframes {$uid}-pulse{0%,100%{box-shadow:inset 0 0 0 0 transparent}50%{box-shadow:inset 0 0 0 {$ab_thick}px {$ab_color}}}";
            echo ".{$uid} .olo-pg-item{animation:{$uid}-pulse {$ab_speed}s ease-in-out infinite}";
        } elseif ( $anim_border === 'radar' ) {
            // Radar — onda box-shadow che si espande dall'item
            $radar_spread = $ab_thick * 3;
            echo "@keyframes {$uid}-radar{0%{box-shadow:0 0 0 0 {$ab_color}}100%{box-shadow:0 0 0 {$radar_spread}px transparent}}";
            echo ".{$uid} .olo-pg-item{animation:{$uid}-radar {$ab_speed}s ease-out infinite}";
        }

        // ─── "+N" overlay ───
        echo ".{$uid} .olo-pg-more{position:absolute;inset:0;z-index:5;display:flex;align-items:center;justify-content:center;background:{$more_bg};color:{$more_color};font-size:{$more_size}px;font-weight:700;pointer-events:none;border-radius:{$radius}}";

        // ─── Hidden lightbox items ───
        echo ".{$uid} .olo-pg-hidden{position:absolute;width:0;height:0;overflow:hidden;pointer-events:none;opacity:0}";

        // ─── Entrance: overflow visible per permettere animazioni off-screen ───
        if ( $entrance !== 'none' && ! $is_coverflow && ! $is_strip ) {
            echo ".{$uid}{overflow:visible}";
            // Dopo l'animazione, ripristina overflow hidden per evitare scrollbar
            echo ".{$uid}.olo-pg-visible{overflow:hidden}";
        }

        // ─── Entrance animation ───
        if ( $entrance !== 'none' ) {
            $dur_s = number_format( $ent_dur / 1000, 2 );
            $initial_transform = 'translateY(24px)';
            $initial_extra = '';

            $is_special_entrance = in_array( $entrance, [ 'split-sides', 'fall', 'wind', 'zoom-center', 'land' ], true );

            if ( $entrance === 'fade-up' ) {
                $initial_transform = 'translateY(100vh)';
            } elseif ( $entrance === 'fade-scale' ) {
                $initial_transform = 'translateY(60vh) scale(.7)';
            } elseif ( $entrance === 'flip' ) {
                $initial_transform = 'perspective(600px) rotateX(90deg) translateY(40vh)';
                $initial_extra = 'transform-origin:center bottom;';
            } elseif ( $entrance === 'slide-in' ) {
                $initial_transform = 'translateX(-100vw)';
            } elseif ( $entrance === 'blur-in' ) {
                $initial_transform = 'translateY(60vh)';
                $initial_extra = 'filter:blur(12px);';
            } elseif ( $entrance === 'split-sides' ) {
                $initial_transform = 'translateX(-100vw) rotate(-8deg)';
            } elseif ( $entrance === 'fall' ) {
                $initial_transform = 'translateY(-100vh) rotate(12deg) scale(.8)';
            } elseif ( $entrance === 'wind' ) {
                $initial_transform = 'translateX(100vw) rotate(18deg) scale(.85)';
                $initial_extra = 'filter:blur(6px);';
            } elseif ( $entrance === 'zoom-center' ) {
                $initial_transform = 'scale(.08)';
                $initial_extra = 'filter:blur(8px);';
            } elseif ( $entrance === 'land' ) {
                $initial_transform = 'perspective(800px) translateZ(600px) translateY(-60vh) scale(1.8)';
                $initial_extra = 'filter:blur(4px);';
            }

            // Coverflow / Strip: entrance solo con opacity (transform gestito dal JS o dal CSS animation)
            if ( $is_coverflow || $is_strip ) {
                echo ".{$uid} .olo-pg-item{opacity:0;transition:opacity {$dur_s}s ease}";
                echo ".{$uid}.olo-pg-visible .olo-pg-item{opacity:1}";
            } elseif ( $is_special_entrance ) {
                // ─── Split sides ───
                if ( $entrance === 'split-sides' ) {
                    echo ".{$uid} .olo-pg-item{opacity:0;transition:opacity {$dur_s}s ease,transform {$dur_s}s cubic-bezier(.25,.46,.45,.94)}";
                    // Dispari: entrano da sinistra fuori schermo
                    echo ".{$uid} .olo-pg-item:nth-child(odd){transform:translateX(-100vw) rotate(-8deg)}";
                    // Pari: entrano da destra fuori schermo
                    echo ".{$uid} .olo-pg-item:nth-child(even){transform:translateX(100vw) rotate(8deg)}";
                    echo ".{$uid}.olo-pg-visible .olo-pg-item{opacity:1;transform:translateX(0) rotate(0deg)}";
                }
                // ─── Fall (caduta da fuori schermo con bounce) ───
                if ( $entrance === 'fall' ) {
                    $bounce_dur = number_format( $ent_dur / 1000 * 1.5, 2 );
                    echo "@keyframes {$uid}-fall{0%{opacity:0;transform:translateY(-100vh) rotate(12deg) scale(.8)}45%{opacity:1;transform:translateY(12px) rotate(-2deg) scale(1.03)}65%{transform:translateY(-6px) rotate(1deg) scale(1)}80%{transform:translateY(3px) rotate(0deg)}100%{opacity:1;transform:translateY(0) rotate(0deg) scale(1)}}";
                    echo ".{$uid} .olo-pg-item{opacity:0;transform:translateY(-100vh) rotate(12deg) scale(.8)}";
                    echo ".{$uid}.olo-pg-visible .olo-pg-item{animation:{$uid}-fall {$bounce_dur}s cubic-bezier(.22,.68,.36,1) forwards}";
                }
                // ─── Wind (soffiate dal vento da fuori schermo) ───
                if ( $entrance === 'wind' ) {
                    $wind_dur = number_format( $ent_dur / 1000 * 1.4, 2 );
                    echo "@keyframes {$uid}-wind{0%{opacity:0;transform:translateX(100vw) rotate(18deg) scale(.85);filter:blur(6px)}55%{opacity:1;transform:translateX(-12px) rotate(-3deg) scale(1.02);filter:blur(0)}75%{transform:translateX(5px) rotate(1deg) scale(1)}100%{opacity:1;transform:translateX(0) rotate(0deg) scale(1);filter:blur(0)}}";
                    echo ".{$uid} .olo-pg-item{opacity:0;transform:translateX(100vw) rotate(18deg) scale(.85);filter:blur(6px)}";
                    echo ".{$uid}.olo-pg-visible .olo-pg-item{animation:{$uid}-wind {$wind_dur}s cubic-bezier(.25,.46,.45,.94) forwards}";
                }
                // ─── Zoom center (arrivano da un punto distante al centro) ───
                if ( $entrance === 'zoom-center' ) {
                    $zc_dur = number_format( $ent_dur / 1000 * 1.3, 2 );
                    // Ogni item converge dal centro assoluto della galleria verso la sua posizione finale
                    // Usiamo scale molto piccola + translate che porta al centro
                    echo "@keyframes {$uid}-zc{0%{opacity:0;transform:scale(.05);filter:blur(10px)}40%{opacity:.6;filter:blur(3px)}70%{opacity:1;transform:scale(1.04);filter:blur(0)}85%{transform:scale(.98)}100%{opacity:1;transform:scale(1);filter:blur(0)}}";
                    echo ".{$uid} .olo-pg-item{opacity:0;transform:scale(.05);filter:blur(10px);transform-origin:center center}";
                    echo ".{$uid}.olo-pg-visible .olo-pg-item{animation:{$uid}-zc {$zc_dur}s cubic-bezier(.16,1,.3,1) forwards}";
                }
                // ─── Land (atterrano sullo schermo da fuori, con prospettiva 3D) ───
                if ( $entrance === 'land' ) {
                    $land_dur = number_format( $ent_dur / 1000 * 1.6, 2 );
                    echo "@keyframes {$uid}-land{0%{opacity:0;transform:perspective(800px) translateZ(600px) translateY(-80vh) rotateX(-15deg) scale(1.6);filter:blur(6px)}35%{opacity:.8;filter:blur(2px)}55%{opacity:1;transform:perspective(800px) translateZ(0) translateY(6px) rotateX(2deg) scale(1.02);filter:blur(0)}72%{transform:perspective(800px) translateZ(0) translateY(-3px) rotateX(-1deg) scale(1)}86%{transform:perspective(800px) translateZ(0) translateY(1px) rotateX(0) scale(1)}100%{opacity:1;transform:perspective(800px) translateZ(0) translateY(0) rotateX(0) scale(1);filter:blur(0)}}";
                    echo ".{$uid} .olo-pg-item{opacity:0;transform:perspective(800px) translateZ(600px) translateY(-80vh) rotateX(-15deg) scale(1.6);filter:blur(6px)}";
                    echo ".{$uid}.olo-pg-visible .olo-pg-item{animation:{$uid}-land {$land_dur}s cubic-bezier(.22,.68,.36,1) forwards}";
                }
            } else {
                echo ".{$uid} .olo-pg-item{opacity:0;transform:{$initial_transform};{$initial_extra}transition:opacity {$dur_s}s ease,transform {$dur_s}s ease,filter {$dur_s}s ease,box-shadow {$dur_s}s ease}";
                echo ".{$uid}.olo-pg-visible .olo-pg-item{opacity:1;filter:none}";

                // Hover effects that set transform on the item need :not(:hover) so hover transform wins
                $hover_uses_item_transform = in_array( $hover, [ 'lift', 'tilt3d', 'magnetic' ], true );
                if ( $hover_uses_item_transform ) {
                    echo ".{$uid}.olo-pg-visible .olo-pg-item:not(:hover){transform:none}";
                } else {
                    echo ".{$uid}.olo-pg-visible .olo-pg-item{transform:none}";
                }
            }

            // Stagger delays
            $vis_count = min( $max_visible, $total );
            for ( $i = 0; $i < $vis_count && $i < 30; $i++ ) {
                $delay_s = number_format( ( $ent_stagger * $i ) / 1000, 2 );
                // Per fall e wind: animation-delay invece di transition-delay
                if ( in_array( $entrance, [ 'fall', 'wind', 'zoom-center', 'land' ], true ) ) {
                    echo ".{$uid} .olo-pg-item:nth-child(" . ( $i + 1 ) . "){animation-delay:{$delay_s}s}";
                } else {
                    echo ".{$uid} .olo-pg-item:nth-child(" . ( $i + 1 ) . "){transition-delay:{$delay_s}s}";
                }
            }

            // Restore hover transitions after entrance completes (override entrance slow transition)
            if ( $hover === 'lift' ) {
                echo ".{$uid}.olo-pg-visible .olo-pg-item{transition:transform .4s ease,box-shadow .4s ease}";
            } elseif ( $hover === 'tilt3d' ) {
                echo ".{$uid}.olo-pg-visible .olo-pg-item{transition:transform .4s ease}";
            } elseif ( $hover === 'magnetic' ) {
                echo ".{$uid}.olo-pg-visible .olo-pg-item{transition:transform .15s ease-out}";
            } elseif ( $hover === 'glow' ) {
                echo ".{$uid}.olo-pg-visible .olo-pg-item{transition:box-shadow .4s ease}";
            } elseif ( $hover === 'blur-peers' ) {
                echo ".{$uid}.olo-pg-visible .olo-pg-item{transition:filter .4s ease}";
            }
        }

        // ─── Mobile ───
        if ( $layout === 'masonry' ) {
            echo "@media(max-width:640px){.{$uid}{column-count:{$mob_cols}}}";
        } elseif ( $layout === 'expand' ) {
            echo "@media(max-width:640px){.{$uid}{grid-template-columns:repeat({$mob_cols},1fr);grid-template-rows:none;height:auto;transition:none}.{$uid} .olo-pg-item{height:{$img_height}}}";
        } elseif ( $layout === 'justified' ) {
            echo "@media(max-width:640px){.{$uid} .olo-pg-item{min-width:80px;height:" . ( intval( $img_height ) > 0 ? max( 100, intval( $img_height ) - 60 ) . 'px' : '140px' ) . "}}";
        } elseif ( ! $is_coverflow && $layout !== 'scattered' && $layout !== 'parallax' && $layout !== 'drift' && $layout !== 'cascade' && $layout !== 'honeycomb' && $layout !== 'hexgrid' && $layout !== 'puzzle' && ! $is_strip ) {
            echo "@media(max-width:640px){.{$uid}{grid-template-columns:repeat({$mob_cols},1fr)}}";
        }

        // ─── Parallax mobile ───
        if ( $layout === 'parallax' ) {
            $mob_plx_h = max( 600, (int) round( $plx_height * 0.55 ) );
            echo "@media(max-width:640px){.{$uid}{height:{$mob_plx_h}px}}";
        }

        // ─── Drift mobile ───
        if ( $layout === 'drift' ) {
            $mob_drift_h = max( 500, (int) round( $drift_height * 0.55 ) );
            echo "@media(max-width:640px){.{$uid}{height:{$mob_drift_h}px}}";
        }

        // ─── Cascade mobile ───
        if ( $layout === 'cascade' ) {
            echo "@media(max-width:640px){.{$uid}{height:auto;perspective:none}.{$uid} .olo-pg-item{position:relative;width:85%;left:auto;top:auto;margin:12px auto;transform:none!important}}";
        }

        // ─── Strip mobile ───
        if ( $is_strip ) {
            $mob_strip_w = max( 150, $strip_item_w - 80 );
            echo "@media(max-width:640px){.{$uid} .olo-pg-item{width:{$mob_strip_w}px}}";
        }

        // ─── Reduced motion ───
        echo "@media(prefers-reduced-motion:reduce){";
        echo ".{$uid},.{$uid} .olo-pg-item,.{$uid} .olo-pg-item img,.{$uid} .olo-pg-item video,.{$uid}-track{animation:none!important;transition:none!important}";
        if ( $entrance !== 'none' ) {
            echo ".{$uid} .olo-pg-item{opacity:1!important;transform:none!important;filter:none!important}";
        }
        if ( $is_strip_auto ) {
            echo ".{$uid}-track{transform:none!important}";
        }
        if ( $layout === 'parallax' || $layout === 'drift' || $layout === 'cascade' ) {
            echo ".{$uid} .olo-pg-item{will-change:auto!important}";
        }
        echo "}";

        // ─── Video styles ───
        echo ".{$uid} .olo-pg-item video{width:100%;height:100%;object-fit:{$object_fit};display:block}";
        echo ".{$uid} .olo-pg-play{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none;z-index:2}";
        echo ".{$uid} .olo-pg-play::before{content:'';width:44px;height:44px;border-radius:50%;background:rgba(0,0,0,.5) url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white'%3E%3Cpolygon points='9,5 20,12 9,19'/%3E%3C/svg%3E\") center/20px 20px no-repeat}";
        echo ".{$uid} .olo-pg-no-poster{width:100%;height:100%;background:var(--olo-color-secondary, #1F2937);display:flex;align-items:center;justify-content:center}";

        echo '</style>';

        // ─── Shared JS (once per page) ───
        $needs_tilt3d   = ( $hover === 'tilt3d' );
        $needs_magnetic = ( $hover === 'magnetic' );
        $needs_entrance = ( $entrance !== 'none' );
        $needs_filmstrip = $is_coverflow;
        $needs_expand   = ( $layout === 'expand' );
        $needs_strip    = $is_strip_drag;
        $needs_parallax = ( $layout === 'parallax' );
        $needs_drift    = ( $layout === 'drift' );
        $needs_cascade  = ( $layout === 'cascade' );
        self::maybe_output_script( $needs_tilt3d, $needs_magnetic, $needs_entrance, $needs_filmstrip, $needs_expand, $needs_strip, $needs_parallax, $needs_drift, $needs_cascade );

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
            $data_attrs .= ' data-pg-film-zoom="' . $film_zoom . '"';
            $data_attrs .= ' data-pg-film-tilt="' . $film_tilt . '"';
            $data_attrs .= ' data-pg-film-width="' . $film_width . '"';
            $data_attrs .= ' data-pg-film-dots="' . esc_attr( $film_dots ) . '"';
            if ( $film_auto ) {
                $data_attrs .= ' data-pg-film-auto="' . $film_speed . '"';
            }
        }
        if ( $needs_expand ) {
            $data_attrs .= ' data-pg-expand="1" data-pg-expand-ratio="' . $exp_ratio . '" data-pg-expand-shrink="' . $exp_shrink . '"';
        }
        if ( $is_strip_drag ) {
            $data_attrs .= ' data-pg-strip="1"';
            if ( $strip_fade ) {
                $data_attrs .= ' data-pg-strip-fade="1"';
            }
        }
        if ( $needs_parallax ) {
            $data_attrs .= ' data-pg-parallax="1" data-pg-plx-intensity="' . $plx_intensity . '"';
        }
        if ( $needs_drift ) {
            $data_attrs .= ' data-pg-drift="1" data-pg-drift-intensity="' . $drift_intensity . '" data-pg-drift-rotation="' . $drift_rotation . '"';
        }
        if ( $needs_cascade ) {
            $data_attrs .= ' data-pg-cascade="1" data-pg-cascade-spread="' . $cascade_spread . '"';
        }

        // Lightbox wrapper
        if ( $lb_custom ) {
            $lb_json = wp_json_encode( [ 'position' => $lb_thumbs, 'rows' => $lb_rows ] );
            $lb_attr = ' data-olo-lb="' . esc_attr( $lb_json ) . '"';
        } elseif ( $lightbox ) {
            $lb_attr = ' uk-lightbox="animation: ' . $lb_anim . '"';
        } else {
            $lb_attr = '';
        }

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

        // ─── Duotone SVG filter ───
        if ( $filter === 'duotone' ) {
            echo '<svg style="position:absolute;width:0;height:0;overflow:hidden"><filter id="duo-' . $uid . '" color-interpolation-filters="sRGB"><feColorMatrix type="saturate" values="0" in="SourceGraphic" result="gray"/><feComponentTransfer in="gray" result="duotone"><feFuncR type="table" tableValues="' . $dr . ' ' . $lr . '"/><feFuncG type="table" tableValues="' . $dg . ' ' . $lg . '"/><feFuncB type="table" tableValues="' . $db . ' ' . $lb . '"/></feComponentTransfer><feBlend in="duotone" in2="SourceGraphic" mode="multiply" result="blended"/><feComposite in="blended" in2="SourceGraphic" operator="arithmetic" k1="0" k2="' . $duo_k2 . '" k3="' . $duo_k3 . '" k4="0"/></filter></svg>';
        }

        // Coverflow wrapper (outside lightbox container)
        if ( $is_coverflow ) {
            echo '<div class="' . $container_class . '-wrap" tabindex="0">';
        }

        // ─── Strip arrows wrapper ───
        if ( $strip_arrows ) {
            echo '<div class="' . $uid . '-sa-wrap" style="position:relative">';
        }

        // ─── Strip Marquee / Split: rendering speciale con duplicazione ───
        if ( $is_strip_auto ) {
            $visible_imgs = array_slice( $images, 0, $max_visible );
            $hidden_imgs  = array_slice( $images, $max_visible );

            // Helper per output singolo item
            $that = $this;
            $render_strip_item = function( $img, $idx, $is_dup = false ) use ( $that, $lightbox, $lb_custom, $show_cap, $hcaption, $layout, $strip_height ) {
                $is_vid  = $that->is_video_item( $img );
                $is_emb  = $that->is_embed_video( $img );
                $url     = is_array( $img ) ? ( $img['url'] ?? '' ) : $img;
                $alt     = is_array( $img ) ? ( $img['alt'] ?? '' ) : '';
                $caption = is_array( $img ) ? ( $img['caption'] ?? '' ) : '';
                $att_id  = is_array( $img ) ? absint( $img['id'] ?? 0 ) : 0;
                $poster  = is_array( $img ) ? ( $img['poster'] ?? '' ) : '';
                $embed   = is_array( $img ) ? ( $img['embed'] ?? '' ) : '';
                $poster_id = is_array( $img ) ? absint( $img['poster_id'] ?? 0 ) : 0;

                if ( ! $is_vid ) {
                    if ( ! $url ) return;
                } else {
                    if ( ! $url && ! $embed ) return;
                }

                $caption_attr = '';
                if ( $lightbox ) {
                    if ( $show_cap ) {
                        if ( ! empty( $caption ) ) {
                            $caption_attr = ' data-caption="' . esc_attr( $caption ) . '"';
                        }
                    }
                }

                $thumb_attr = '';
                if ( $lb_custom ) {
                    if ( $is_vid ) {
                        if ( $poster ) {
                            $thumb_attr = ' data-thumb="' . esc_url( $poster ) . '"';
                        }
                    } elseif ( $att_id ) {
                        $thumb_url = wp_get_attachment_image_url( $att_id, 'thumbnail' );
                        if ( $thumb_url ) {
                            $thumb_attr = ' data-thumb="' . esc_url( $thumb_url ) . '"';
                        }
                    }
                }

                // Video data attrs for lightbox
                $video_attrs = '';
                if ( $is_vid ) {
                    if ( $is_emb ) {
                        $embed_url = $that->get_gallery_embed_url( $embed );
                        $video_attrs = ' data-type="video" data-video-src="' . esc_url( $embed_url ) . '" data-poster="' . esc_url( $poster ) . '"';
                    } else {
                        $video_attrs = ' data-type="video" data-video-src="' . esc_url( $url ) . '" data-poster="' . esc_url( $poster ) . '"';
                    }
                }

                // Collage height variation per item
                $inline_style = '';
                if ( $layout === 'strip_collage' ) {
                    $seed = $idx + 42;
                    $x = sin( $seed * 127.1 + 311.7 ) * 43758.5453123;
                    $rand = $x - floor( $x );
                    $h = round( $strip_height - 50 + $rand * 100 );
                    $inline_style = ' style="height:' . $h . 'px"';
                }

                // Determine href for lightbox
                $lb_href = $url;
                if ( $is_vid ) {
                    if ( $is_emb ) {
                        $lb_href = $that->get_gallery_embed_url( $embed );
                    } else {
                        $lb_href = $url;
                    }
                }

                if ( $is_dup ) {
                    $dup_data = $lightbox ? ' data-href="' . esc_url( $lb_href ) . '"' : '';
                    echo '<div class="olo-pg-item olo-pg-dup' . ( $is_vid ? ' olo-pg-video' : '' ) . '" aria-hidden="true"' . $dup_data . $video_attrs . $inline_style . '>';
                    // Duplicati marquee: sempre poster per video
                    if ( $is_vid ) {
                        if ( $poster ) {
                            echo '<img src="' . esc_url( $poster ) . '" alt="' . esc_attr( $alt ) . '" />';
                        } else {
                            echo '<div class="olo-pg-no-poster"></div>';
                        }
                        echo '<div class="olo-pg-play"></div>';
                    } else {
                        echo Olo_Tile_Utils::img_srcset( $att_id, $url, esc_attr( $alt ) );
                    }
                    echo '</div>';
                } else {
                    $tag  = $lightbox ? 'a' : 'div';
                    $href = $lightbox ? ' href="' . esc_url( $lb_href ) . '"' : '';
                    echo '<' . $tag . ' class="olo-pg-item' . ( $is_vid ? ' olo-pg-video' : '' ) . '"' . $href . $caption_attr . $thumb_attr . $video_attrs . $inline_style . '>';
                    // Strip marquee/split: video sempre in poster mode
                    if ( $is_vid ) {
                        if ( $poster ) {
                            echo '<img src="' . esc_url( $poster ) . '" alt="' . esc_attr( $alt ) . '" />';
                        } else {
                            echo '<div class="olo-pg-no-poster"></div>';
                        }
                        echo '<div class="olo-pg-play"></div>';
                    } else {
                        echo Olo_Tile_Utils::img_srcset( $att_id, $url, esc_attr( $alt ) );
                    }
                    if ( ! empty( $caption ) ) {
                        if ( $hcaption !== 'none' ) {
                            echo '<div class="olo-pg-cap">' . esc_html( $caption ) . '</div>';
                        }
                    }
                    echo '</' . $tag . '>';
                }
            };

            if ( $layout === 'strip_marquee' ) {
                // Marquee: container → track → items + duplicati
                echo '<div class="' . $container_class . '"' . $lb_attr . $data_attrs . '>';
                echo '<div class="' . $container_class . '-track">';
                foreach ( $visible_imgs as $k => $img ) {
                    $render_strip_item( $img, $k, false );
                }
                // Duplicati per loop seamless
                foreach ( $visible_imgs as $k => $img ) {
                    $render_strip_item( $img, $k, true );
                }
                echo '</div>'; // track
                // Hidden items per lightbox
                if ( $lightbox ) {
                    foreach ( $hidden_imgs as $img ) {
                        if ( $this->is_video_item( $img ) ) {
                            $h_embed = $img['embed'] ?? '';
                            $h_url = $h_embed ? $this->get_gallery_embed_url( $h_embed ) : ( $img['url'] ?? '' );
                            $h_poster = $img['poster'] ?? '';
                            if ( $h_url ) {
                                echo '<a class="olo-pg-hidden" href="' . esc_url( $h_url ) . '" data-type="video" data-video-src="' . esc_url( $h_url ) . '" data-poster="' . esc_url( $h_poster ) . '"></a>';
                            }
                        } else {
                            $url = is_array( $img ) ? ( $img['url'] ?? '' ) : $img;
                            if ( $url ) {
                                echo '<a class="olo-pg-hidden" href="' . esc_url( $url ) . '"></a>';
                            }
                        }
                    }
                }
                echo '</div>'; // container

            } elseif ( $layout === 'strip_split' ) {
                // Split: container → 2 righe con track + items + duplicati
                $row1 = []; $row2 = [];
                foreach ( $visible_imgs as $k => $img ) {
                    if ( $k % 2 === 0 ) { $row1[] = [ 'img' => $img, 'idx' => $k ]; }
                    else                 { $row2[] = [ 'img' => $img, 'idx' => $k ]; }
                }

                echo '<div class="' . $container_class . '"' . $lb_attr . $data_attrs . '>';
                // Row 1 (scorre a sinistra)
                echo '<div class="' . $container_class . '-row">';
                echo '<div class="' . $container_class . '-track">';
                foreach ( $row1 as $item ) { $render_strip_item( $item['img'], $item['idx'], false ); }
                foreach ( $row1 as $item ) { $render_strip_item( $item['img'], $item['idx'], true ); }
                echo '</div></div>';
                // Row 2 (scorre a destra — reverse)
                echo '<div class="' . $container_class . '-row">';
                echo '<div class="' . $container_class . '-track ' . $container_class . '-track-rev">';
                foreach ( $row2 as $item ) { $render_strip_item( $item['img'], $item['idx'], false ); }
                foreach ( $row2 as $item ) { $render_strip_item( $item['img'], $item['idx'], true ); }
                echo '</div></div>';
                // Hidden items per lightbox
                if ( $lightbox ) {
                    foreach ( $hidden_imgs as $img ) {
                        if ( $this->is_video_item( $img ) ) {
                            $h_embed = $img['embed'] ?? '';
                            $h_url = $h_embed ? $this->get_gallery_embed_url( $h_embed ) : ( $img['url'] ?? '' );
                            $h_poster = $img['poster'] ?? '';
                            if ( $h_url ) {
                                echo '<a class="olo-pg-hidden" href="' . esc_url( $h_url ) . '" data-type="video" data-video-src="' . esc_url( $h_url ) . '" data-poster="' . esc_url( $h_poster ) . '"></a>';
                            }
                        } else {
                            $url = is_array( $img ) ? ( $img['url'] ?? '' ) : $img;
                            if ( $url ) {
                                echo '<a class="olo-pg-hidden" href="' . esc_url( $url ) . '"></a>';
                            }
                        }
                    }
                }
                echo '</div>'; // container
            }

            // Strip arrows (auto strip)
            if ( $strip_arrows ) {
                echo $this->strip_arrows_html( $uid, $sa_style, $sa_size, $sa_color, $sa_bg );
                echo '</div>'; // sa-wrap
            }

            // Lightbox click delegation for duplicate items in marquee/split
            if ( $lightbox ) {
                echo '<script>(function(){';
                echo 'if(window._oloPGdup)return;window._oloPGdup=1;';
                echo 'document.addEventListener("click",function(e){';
                echo 'var dup=e.target.closest(".olo-pg-dup[data-href]");';
                echo 'if(!dup)return;';
                echo 'var ct=dup.closest("[uk-lightbox],[data-olo-lb]");';
                echo 'if(!ct)return;';
                echo 'var href=dup.getAttribute("data-href");';
                echo 'if(!href)return;';
                // Trova l'originale <a> con lo stesso href (evita quote escaping nel selector)
                echo 'var links=ct.querySelectorAll("a.olo-pg-item");';
                echo 'for(var i=0;i<links.length;i++){';
                echo 'if(links[i].getAttribute("href")===href){e.preventDefault();e.stopPropagation();links[i].click();break}';
                echo '}';
                echo '})';
                echo '})();</script>';
            }

            return ob_get_clean();
        }

        echo '<div class="' . $container_class . '"' . $lb_attr . $data_attrs . $hex_inline . '>';

        // ─── Render items ───
        $i = 0;
        foreach ( $images as $img ) {
            $is_vid  = $this->is_video_item( $img );
            $is_emb  = $this->is_embed_video( $img );
            $url     = is_array( $img ) ? ( $img['url'] ?? '' ) : $img;
            $alt     = is_array( $img ) ? ( $img['alt'] ?? '' ) : '';
            $caption = is_array( $img ) ? ( $img['caption'] ?? '' ) : '';
            $att_id  = is_array( $img ) ? absint( $img['id'] ?? 0 ) : 0;
            $poster  = is_array( $img ) ? ( $img['poster'] ?? '' ) : '';
            $embed   = is_array( $img ) ? ( $img['embed'] ?? '' ) : '';
            $poster_id = is_array( $img ) ? absint( $img['poster_id'] ?? 0 ) : 0;

            if ( $is_vid ) {
                if ( ! $url && ! $embed ) continue;
            } else {
                if ( ! $url ) continue;
            }
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
            $plx_item_data = '';
            if ( $layout === 'scattered' && $is_visible ) {
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

            // Parallax layout: inline position + depth styles
            if ( $layout === 'parallax' && $is_visible ) {
                $col_i   = ( $i - 1 ) % $cols_p;
                $row_i   = (int) floor( ( $i - 1 ) / $cols_p );
                $cw      = 100 / $cols_p;
                $ch      = 100 / $rows_p;
                $depth   = $this->seeded_random( $i + 3 );
                $size    = 0.50 + $depth * 0.45;
                $ox      = ( $this->seeded_random( $i + 7 ) - 0.5 ) * 15;
                $oy      = ( $this->seeded_random( $i + 13 ) - 0.5 ) * 15;
                $rot     = ( $this->seeded_random( $i + 21 ) - 0.5 ) * 8;
                $left    = round( $col_i * $cw + $ox, 1 );
                $top     = round( $row_i * $ch + $oy, 1 );
                $w       = round( $cw * $size, 1 );
                $shBlur  = (int) round( 4 + $depth * 20 );
                $shHalf  = (int) round( $shBlur / 2 );
                $shAlpha = round( 0.1 + $depth * 0.2, 2 );
                $op      = round( 0.7 + $depth * 0.3, 2 );
                $zi      = (int) round( $depth * 20 );
                $speed   = round( ( $depth - 0.33 ) * ( $plx_intensity / 100 ), 3 );
                $rot_r   = round( $rot, 1 );
                $inline_style = "left:{$left}%;top:{$top}%;width:{$w}%;transform:rotate({$rot_r}deg);box-shadow:0 {$shHalf}px {$shBlur}px rgba(0,0,0,{$shAlpha});opacity:{$op};z-index:{$zi}";
                $plx_item_data = ' data-speed="' . $speed . '" data-base-transform="rotate(' . $rot_r . 'deg)"';
            }

            // Drift layout: multi-directional parallax with X, Y, rotation
            if ( $layout === 'drift' && $is_visible ) {
                $col_i   = ( $i - 1 ) % $cols_d;
                $row_i   = (int) floor( ( $i - 1 ) / $cols_d );
                $cw      = 100 / $cols_d;
                $ch      = 100 / $rows_d;
                $depth   = $this->seeded_random( $i + 5 );
                $size    = 0.45 + $depth * 0.50;
                $ox      = ( $this->seeded_random( $i + 11 ) - 0.5 ) * 18;
                $oy      = ( $this->seeded_random( $i + 17 ) - 0.5 ) * 18;
                $rot     = ( $this->seeded_random( $i + 23 ) - 0.5 ) * 10;
                $left    = round( $col_i * $cw + $ox, 1 );
                $top     = round( $row_i * $ch + $oy, 1 );
                $w       = round( $cw * $size, 1 );
                $shBlur  = (int) round( 4 + $depth * 18 );
                $shHalf  = (int) round( $shBlur / 2 );
                $shAlpha = round( 0.1 + $depth * 0.18, 2 );
                $op      = round( 0.75 + $depth * 0.25, 2 );
                $zi      = (int) round( $depth * 20 );
                // Velocità X e Y diverse per ogni item + direzione casuale
                $speed_y = round( ( $depth - 0.4 ) * ( $drift_intensity / 100 ), 3 );
                $speed_x = round( ( $this->seeded_random( $i + 31 ) - 0.5 ) * ( $drift_intensity / 100 ) * 0.7, 3 );
                $speed_r = round( ( $this->seeded_random( $i + 37 ) - 0.5 ) * ( $drift_rotation / 10 ), 3 );
                $rot_r   = round( $rot, 1 );
                $inline_style = "left:{$left}%;top:{$top}%;width:{$w}%;transform:rotate({$rot_r}deg);box-shadow:0 {$shHalf}px {$shBlur}px rgba(0,0,0,{$shAlpha});opacity:{$op};z-index:{$zi}";
                $plx_item_data = ' data-drift-sx="' . $speed_x . '" data-drift-sy="' . $speed_y . '" data-drift-sr="' . $speed_r . '" data-base-transform="rotate(' . $rot_r . 'deg)"';
            }

            // Cascade layout: stacked cards that spread on scroll
            if ( $layout === 'cascade' && $is_visible ) {
                $stack_offset = ( $i - 1 ) * ( $cascade_overlap / $total_vis_c );
                $rot_c = round( ( $this->seeded_random( $i + 9 ) - 0.5 ) * $cascade_rotation * 2, 1 );
                $shift_x = round( ( $this->seeded_random( $i + 15 ) - 0.5 ) * 30, 1 );
                $zi = $total_vis_c - $i + 1;
                $w_c = max( 35, 60 - ( $total_vis_c > 6 ? ( $i - 1 ) * 2 : 0 ) );
                $h_c = max( 200, 350 - ( $total_vis_c > 6 ? ( $i - 1 ) * 15 : 0 ) );
                $left_c = round( 50 - $w_c / 2 + $shift_x, 1 );
                $top_c = round( $stack_offset, 1 );
                $inline_style = "left:{$left_c}%;top:{$top_c}%;width:{$w_c}%;height:{$h_c}px;z-index:{$zi};transform:rotate({$rot_c}deg)";
                $plx_item_data = ' data-cascade-idx="' . ( $i - 1 ) . '" data-cascade-rot="' . $rot_c . '" data-cascade-sx="' . $shift_x . '"';
            }

            // Strip collage: varying height per item
            if ( $layout === 'strip_collage' && $is_visible ) {
                $rand = $this->seeded_random( $i + 42 );
                $collage_h = round( $strip_height - 50 + $rand * 100 );
                $inline_style = "height:{$collage_h}px";
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

            // Determine href for lightbox
            $lb_href = $url;
            if ( $is_vid ) {
                if ( $is_emb ) {
                    $lb_href = $this->get_gallery_embed_url( $embed );
                }
            }
            $href = $lightbox ? ' href="' . esc_url( $lb_href ) . '"' : '';

            // Thumb URL for custom lightbox
            $thumb_attr = '';
            if ( $lb_custom ) {
                if ( $is_vid ) {
                    if ( $poster ) {
                        $thumb_attr = ' data-thumb="' . esc_url( $poster ) . '"';
                    }
                } elseif ( $att_id ) {
                    $thumb_url = wp_get_attachment_image_url( $att_id, 'thumbnail' );
                    if ( $thumb_url ) {
                        $thumb_attr = ' data-thumb="' . esc_url( $thumb_url ) . '"';
                    }
                }
            }

            // Video data attrs
            $video_attrs = '';
            $item_class = 'olo-pg-item';
            if ( $is_vid ) {
                $item_class .= ' olo-pg-video';
                $vid_src = $is_emb ? $this->get_gallery_embed_url( $embed ) : $url;
                $video_attrs = ' data-type="video" data-video-src="' . esc_url( $vid_src ) . '" data-poster="' . esc_url( $poster ) . '"';
            }

            // Should use autoplay <video> tag or embed iframe?
            $use_autoplay = false;
            $use_embed_autoplay = false;
            if ( $is_vid && $video_preview === 'autoplay' && ! $is_coverflow && ! $is_strip_auto ) {
                if ( $is_emb ) {
                    $use_embed_autoplay = true;
                } else {
                    $use_autoplay = true;
                }
            }

            if ( $is_visible ) {
                $combined_style = trim( $inline_style . ( $puzzle_clip ? ';' . $puzzle_clip : '' ) );
                $style_attr = $combined_style ? ' style="' . esc_attr( $combined_style ) . '"' : '';
                echo '<' . $tag . ' class="' . $item_class . '"' . $href . $caption_attr . $thumb_attr . $video_attrs . $plx_item_data . $style_attr . '>';

                if ( $is_vid ) {
                    if ( $use_autoplay ) {
                        $mime = $this->get_video_mime( $url );
                        $poster_attr = $poster ? ' poster="' . esc_url( $poster ) . '"' : '';
                        echo '<video muted autoplay loop playsinline' . $poster_attr . '><source src="' . esc_url( $url ) . '" type="' . esc_attr( $mime ) . '"></video>';
                    } elseif ( $use_embed_autoplay ) {
                        $embed_auto_url = $this->get_gallery_embed_url( $embed, true );
                        echo '<iframe src="' . esc_url( $embed_auto_url ) . '" style="position:absolute;inset:0;width:100%;height:100%;border:none" allow="autoplay;encrypted-media" allowfullscreen loading="lazy"></iframe>';
                    } else {
                        if ( $poster ) {
                            echo Olo_Tile_Utils::img_srcset( $poster_id, $poster, esc_attr( $alt ) );
                        } else {
                            echo '<div class="olo-pg-no-poster"></div>';
                        }
                        echo '<div class="olo-pg-play"></div>';
                    }
                } else {
                    echo Olo_Tile_Utils::img_srcset( $att_id, $url, esc_attr( $alt ) );
                }

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
                    if ( $is_vid ) {
                        $vid_src = $is_emb ? $this->get_gallery_embed_url( $embed ) : $url;
                        echo '<a class="olo-pg-hidden" href="' . esc_url( $vid_src ) . '"' . $caption_attr . $thumb_attr . ' data-type="video" data-video-src="' . esc_url( $vid_src ) . '" data-poster="' . esc_url( $poster ) . '"></a>';
                    } else {
                        echo '<a class="olo-pg-hidden" href="' . esc_url( $url ) . '"' . $caption_attr . $thumb_attr . '></a>';
                    }
                }
            }
        }

        echo '</div>';

        // Coverflow: frecce + indicatore + chiusura wrapper
        if ( $is_coverflow ) {
            echo '<button class="' . $container_class . '-prev" aria-label="' . esc_attr( olo_t( 'Precedente' ) ) . '">&#8249;</button>';
            echo '<button class="' . $container_class . '-next" aria-label="' . esc_attr( olo_t( 'Successivo' ) ) . '">&#8250;</button>';
            if ( $film_dots !== 'none' ) {
                if ( $film_dots === 'progress' ) {
                    echo '<div class="' . $container_class . '-dots"><div class="pg-prog-track"><div class="pg-prog-fill"></div></div></div>';
                } else {
                    echo '<div class="' . $container_class . '-dots"></div>';
                }
            }
            echo '</div>'; // close -wrap
        }

        // Strip arrows (drag strip)
        if ( $strip_arrows ) {
            echo $this->strip_arrows_html( $uid, $sa_style, $sa_size, $sa_color, $sa_bg );
            echo '</div>'; // sa-wrap
        }

        return ob_get_clean();
    }

    /**
     * Check if gallery item is a video.
     */
    private function is_video_item( $img ) {
        return is_array( $img ) && ( ( $img['type'] ?? '' ) === 'video' );
    }

    /**
     * Check if gallery item is an embed video (YouTube/Vimeo).
     */
    private function is_embed_video( $img ) {
        return is_array( $img ) && ! empty( $img['embed'] );
    }

    /**
     * Get embed URL for YouTube/Vimeo.
     */
    private function get_gallery_embed_url( $url, $muted = false ) {
        if ( empty( $url ) ) return '';

        // YouTube
        if ( preg_match( '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches ) ) {
            $params = 'autoplay=1';
            if ( $muted ) {
                $params .= '&mute=1&loop=1&playlist=' . $matches[1];
            }
            return 'https://www.youtube.com/embed/' . $matches[1] . '?' . $params;
        }

        // Vimeo
        if ( preg_match( '/vimeo\.com\/(\d+)/', $url, $matches ) ) {
            $params = 'autoplay=1';
            if ( $muted ) {
                $params .= '&muted=1&loop=1&background=1';
            }
            return 'https://player.vimeo.com/video/' . $matches[1] . '?' . $params;
        }

        return '';
    }

    /**
     * Get video MIME type from URL extension.
     */
    private function get_video_mime( $url ) {
        $ext = strtolower( pathinfo( parse_url( $url, PHP_URL_PATH ) ?: '', PATHINFO_EXTENSION ) );
        $map = [ 'mp4' => 'video/mp4', 'webm' => 'video/webm', 'ogg' => 'video/ogg', 'ogv' => 'video/ogg' ];
        return $map[ $ext ] ?? 'video/mp4';
    }

    /**
     * Render strip navigation arrows HTML + inline CSS.
     */
    private function strip_arrows_html( $uid, $style, $size, $color, $bg ) {
        $half = (int) round( $size / 2 );
        // SVG icons per stile
        $svg_left = $svg_right = '';
        $icon_vb = '0 0 24 24';
        $stroke_w = 2;
        if ( $style === 'chevron' ) {
            $svg_left  = '<polyline points="15 18 9 12 15 6" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_w . '" stroke-linecap="round" stroke-linejoin="round"/>';
            $svg_right = '<polyline points="9 18 15 12 9 6" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_w . '" stroke-linecap="round" stroke-linejoin="round"/>';
        } elseif ( $style === 'arrow' ) {
            $svg_left  = '<line x1="19" y1="12" x2="5" y2="12" stroke="' . $color . '" stroke-width="' . $stroke_w . '" stroke-linecap="round"/><polyline points="12 19 5 12 12 5" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_w . '" stroke-linecap="round" stroke-linejoin="round"/>';
            $svg_right = '<line x1="5" y1="12" x2="19" y2="12" stroke="' . $color . '" stroke-width="' . $stroke_w . '" stroke-linecap="round"/><polyline points="12 5 19 12 12 19" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_w . '" stroke-linecap="round" stroke-linejoin="round"/>';
        } elseif ( $style === 'minimal' ) {
            $stroke_w = 1.5;
            $svg_left  = '<polyline points="14 17 9 12 14 7" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_w . '" stroke-linecap="round" stroke-linejoin="round"/>';
            $svg_right = '<polyline points="10 7 15 12 10 17" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_w . '" stroke-linecap="round" stroke-linejoin="round"/>';
        } else {
            // circle, square, pill — chevron inside
            $svg_left  = '<polyline points="14 17 9 12 14 7" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_w . '" stroke-linecap="round" stroke-linejoin="round"/>';
            $svg_right = '<polyline points="10 7 15 12 10 17" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_w . '" stroke-linecap="round" stroke-linejoin="round"/>';
        }

        // Border-radius per stile
        $btn_radius = '4px';
        if ( $style === 'circle' ) { $btn_radius = '50%'; }
        elseif ( $style === 'pill' )   { $btn_radius = $size . 'px'; }
        elseif ( $style === 'minimal' ) { $btn_radius = '0'; }

        // Larghezza: pill è più largo
        $btn_w = $size;
        $btn_h = $size;
        if ( $style === 'pill' ) { $btn_w = (int) round( $size * 0.6 ); $btn_h = (int) round( $size * 1.4 ); }

        // Background: minimale non ha sfondo
        $btn_bg = ( $style === 'minimal' ) ? 'transparent' : $bg;
        $btn_shadow = ( $style === 'minimal' ) ? 'none' : '0 2px 8px rgba(0,0,0,.25)';

        $html = '<style>';
        $html .= ".{$uid}-sa-wrap{position:relative}";
        $html .= ".{$uid}-sa-btn{position:absolute;top:50%;transform:translateY(-50%);z-index:6;border:none;padding:0;cursor:pointer;display:flex;align-items:center;justify-content:center;";
        $html .= "width:{$btn_w}px;height:{$btn_h}px;background:{$btn_bg};border-radius:{$btn_radius};box-shadow:{$btn_shadow};opacity:.7;transition:opacity .25s ease}";
        $html .= ".{$uid}-sa-btn:hover{opacity:1}";
        $html .= ".{$uid}-sa-btn svg{width:{$half}px;height:{$half}px}";
        $html .= ".{$uid}-sa-prev{left:8px}";
        $html .= ".{$uid}-sa-next{right:8px}";
        $html .= "@media(max-width:640px){.{$uid}-sa-btn{width:" . max( 28, $btn_w - 8 ) . "px;height:" . max( 28, $btn_h - 8 ) . "px}.{$uid}-sa-prev{left:4px}.{$uid}-sa-next{right:4px}}";
        $html .= '</style>';
        $html .= '<button class="' . $uid . '-sa-btn ' . $uid . '-sa-prev" aria-label="' . esc_attr( olo_t( 'Precedente' ) ) . '" data-sa-dir="-1"><svg viewBox="' . $icon_vb . '">' . $svg_left . '</svg></button>';
        $html .= '<button class="' . $uid . '-sa-btn ' . $uid . '-sa-next" aria-label="' . esc_attr( olo_t( 'Successivo' ) ) . '" data-sa-dir="1"><svg viewBox="' . $icon_vb . '">' . $svg_right . '</svg></button>';
        return $html;
    }

    /**
     * Output shared JS once per page for: tilt3d, magnetic, entrance reveal, filmstrip, expand, strip kinetic.
     */
    private static function maybe_output_script( $tilt, $magnetic, $entrance, $filmstrip, $expand = false, $strip = false, $parallax = false, $drift = false, $cascade = false ) {
        if ( self::$script_output ) return;
        if ( ! $tilt && ! $magnetic && ! $entrance && ! $filmstrip && ! $expand && ! $strip && ! $parallax && ! $drift && ! $cascade ) return;
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

        // ── Filmstrip Coverflow ──
        echo 'function initFilmstrip(){';
        echo 'document.querySelectorAll("[data-pg-filmstrip]").forEach(function(el){';
        echo 'var zoom=parseFloat(el.dataset.pgFilmZoom)||1.15;';
        echo 'var tilt=parseFloat(el.dataset.pgFilmTilt)||8;';
        echo 'var iw=parseFloat(el.dataset.pgFilmWidth)||280;';
        echo 'var autoSec=parseFloat(el.dataset.pgFilmAuto)||0;';
        echo 'var wrap=el.parentElement;';
        echo 'if(!wrap)return;';
        echo 'var items=el.querySelectorAll(".olo-pg-item");';
        echo 'if(!items.length)return;';
        echo 'var cid=el.className.split(" ")[0];';
        echo 'var dotStyle=el.dataset.pgFilmDots||"dots";';
        // Scroll to center item on init
        echo 'var mid=Math.floor(items.length/2);';
        echo 'var midIt=items[mid];';
        echo 'el.scrollLeft=midIt.offsetLeft-el.clientWidth/2+midIt.offsetWidth/2;';
        // Update transforms on scroll
        echo 'var raf=0;';
        // GEOMETRIA CILINDRO REALE:
        // Ogni foto è sulla superficie di un cilindro di raggio R.
        // tilt = angolo tra foto adiacenti → R = step / (2 * tan(tilt/2))
        // Per ogni foto ad angolo θ dal centro:
        //   translateZ = R*(cos(θ)-1)    → profondità sulla superficie circolare
        //   translateX = R*sin(θ) - d    → correzione dalla posizione flex lineare
        //   rotateY = θ                  → tangente alla superficie
        echo 'function updateCoverflow(){';
        echo 'var ctr=el.scrollLeft+el.clientWidth/2;';
        echo 'var gp=parseInt(getComputedStyle(el).gap)||0;';
        echo 'var step=iw+gp;';
        // Angolo tilt in radianti
        echo 'var dRad=tilt*Math.PI/180;';
        echo 'var maxT=75*Math.PI/180;';
        echo 'items.forEach(function(it,idx){';
        echo 'var itCtr=it.offsetLeft+it.offsetWidth/2;';
        echo 'var d=itCtr-ctr;';
        echo 'var n=d/step;';
        echo 'var aN=Math.abs(n);';
        // Angolo sul cilindro: n * tilt_rad, clampato ±75°
        echo 'var theta=n*dRad;';
        echo 'if(theta>maxT)theta=maxT;';
        echo 'if(theta<-maxT)theta=-maxT;';
        // Profondità cilindrica: R=300, tz = R*(cos(θ)-1)
        // Centro tz=0, lati arretrano dolcemente sulla curva del cilindro
        echo 'var tz=300*(Math.cos(theta)-1);';
        // Rotazione tangente alla superficie
        echo 'var ry=theta*180/Math.PI;';
        // Zoom solo al centro (decade a 0 in ~1 item)
        echo 'var sc=1+(zoom-1)*Math.max(0,1-aN);';
        // Z-index: centro davanti
        echo 'var zi=100-Math.round(Math.min(aN,5)*15);';
        echo 'if(rm){it.style.transform="";it.style.zIndex="";return}';
        // Ordine: perspective → rotateY → translateZ → scale
        // rotateY prima di translateZ = l'item ruota, poi arretra in profondità mondo
        echo 'it.style.transform="perspective(800px) rotateY("+ry.toFixed(1)+"deg) translateZ("+tz.toFixed(1)+"px) scale("+sc.toFixed(3)+")";';
        echo 'it.style.zIndex=zi;';
        echo '});';
        // Update indicator
        echo 'var dotEl=wrap.querySelector("."+cid+"-dots");';
        echo 'if(dotEl){';
        echo 'var closest=-1,minD=Infinity;';
        echo 'items.forEach(function(it,idx){var d=Math.abs(it.offsetLeft+it.offsetWidth/2-ctr);if(d<minD){minD=d;closest=idx}});';
        // dots / lines: toggle active class
        echo 'if(dotStyle==="dots"||dotStyle==="lines"){';
        echo 'dotEl.querySelectorAll("span").forEach(function(sp,idx){sp.classList.toggle("active",idx===closest)});';
        echo '}';
        // progress: update fill width
        echo 'if(dotStyle==="progress"){';
        echo 'var fill=dotEl.querySelector(".pg-prog-fill");';
        echo 'if(fill){var pct=items.length>1?(closest/(items.length-1))*100:100;fill.style.width=pct+"%"}';
        echo '}';
        // fraction: update text
        echo 'if(dotStyle==="fraction"){';
        echo 'dotEl.textContent=(closest+1)+" / "+items.length;';
        echo '}';
        echo '}';
        echo '}';
        // Scroll listener with rAF
        echo 'el.addEventListener("scroll",function(){if(!raf){raf=requestAnimationFrame(function(){updateCoverflow();raf=0})}},{passive:true});';
        // Mouse drag-to-scroll (desktop) — NO "&&" perché WP converte in &#038;
        // Logica: pointerdown registra posizione, pointermove avvia drag solo se >5px,
        // pointerup resetta. Click semplice = lightbox. Drag = scroll, no lightbox.
        echo 'var mDown=false,mDrag=false,wasDrag=false,dStartX=0,dScrollL=0;';
        echo 'el.style.cursor="grab";';
        echo 'el.addEventListener("pointerdown",function(e){if(e.pointerType==="mouse"){if(e.button===0){mDown=true;mDrag=false;wasDrag=false;dStartX=e.clientX;dScrollL=el.scrollLeft}}});';
        echo 'el.addEventListener("pointermove",function(e){if(!mDown)return;var dx=e.clientX-dStartX;if(!mDrag){if(Math.abs(dx)>5){mDrag=true;el.setPointerCapture(e.pointerId);el.style.scrollSnapType="none";el.style.cursor="grabbing"}else{return}}e.preventDefault();el.scrollLeft=dScrollL-dx});';
        echo 'el.addEventListener("pointerup",function(e){if(mDrag){el.releasePointerCapture(e.pointerId);wasDrag=true}mDown=false;mDrag=false;el.style.scrollSnapType="";el.style.cursor="grab"});';
        echo 'el.addEventListener("pointercancel",function(){mDown=false;mDrag=false;el.style.scrollSnapType="";el.style.cursor="grab"});';
        // Blocca click su link dopo drag (wasDrag=true), altrimenti lightbox funziona
        echo 'el.addEventListener("click",function(e){if(wasDrag){wasDrag=false;e.preventDefault();e.stopPropagation()}},true);';
        // Generate indicator elements
        echo 'var dotC=wrap.querySelector("."+cid+"-dots");';
        echo 'if(dotC){';
        // dots / lines: create span per item
        echo 'if(dotStyle==="dots"||dotStyle==="lines"){';
        echo 'for(var di=0;di<items.length;di++){';
        echo 'var sp=document.createElement("span");';
        // Pallini più piccoli se tante foto
        echo 'if(dotStyle==="dots"){if(items.length>20){sp.style.width="9px";sp.style.height="9px"}}';
        // Lines più strette se tante foto
        echo 'if(dotStyle==="lines"){if(items.length>20){sp.style.width="12px"}}';
        echo '(function(idx){sp.addEventListener("click",function(){el.scrollTo({left:items[idx].offsetLeft-el.clientWidth/2+items[idx].offsetWidth/2,behavior:"smooth"})})})(di);';
        echo 'dotC.appendChild(sp);';
        echo '}';
        echo '}';
        // progress: wire click-to-seek on track
        echo 'if(dotStyle==="progress"){';
        echo 'var track=dotC.querySelector(".pg-prog-track");';
        echo 'if(track){track.addEventListener("click",function(e){';
        echo 'var r=track.getBoundingClientRect();var pct=(e.clientX-r.left)/r.width;var idx=Math.round(pct*(items.length-1));';
        echo 'if(idx>=0){if(idx<items.length){el.scrollTo({left:items[idx].offsetLeft-el.clientWidth/2+items[idx].offsetWidth/2,behavior:"smooth"})}}';
        echo '})}';
        echo '}';
        // fraction: init text
        echo 'if(dotStyle==="fraction"){';
        echo 'dotC.textContent="1 / "+items.length;';
        echo '}';
        echo '}';
        // Arrow buttons
        echo 'var prevBtn=wrap.querySelector("."+cid+"-prev");';
        echo 'var nextBtn=wrap.querySelector("."+cid+"-next");';
        echo 'var gap=parseInt(getComputedStyle(el).gap)||0;';
        echo 'if(prevBtn)prevBtn.addEventListener("click",function(e){e.stopPropagation();e.preventDefault();el.scrollBy({left:-(iw+gap),behavior:"smooth"})});';
        echo 'if(nextBtn)nextBtn.addEventListener("click",function(e){e.stopPropagation();e.preventDefault();el.scrollBy({left:iw+gap,behavior:"smooth"})});';
        // Keyboard navigation on wrapper
        echo 'wrap.addEventListener("keydown",function(e){';
        echo 'if(e.key==="ArrowLeft"){e.preventDefault();el.scrollBy({left:-(iw+gap),behavior:"smooth"})}';
        echo 'if(e.key==="ArrowRight"){e.preventDefault();el.scrollBy({left:iw+gap,behavior:"smooth"})}';
        echo '});';
        // Auto-advance
        echo 'if(autoSec>0){';
        echo 'var autoId=setInterval(function(){';
        echo 'if(el.scrollLeft>=el.scrollWidth-el.clientWidth-10){el.scrollTo({left:0,behavior:"smooth"})}';
        echo 'else{el.scrollBy({left:iw+gap,behavior:"smooth"})}';
        echo '},autoSec*1000);';
        echo 'wrap.addEventListener("mouseenter",function(){clearInterval(autoId)});';
        echo 'wrap.addEventListener("mouseleave",function(){autoId=setInterval(function(){';
        echo 'if(el.scrollLeft>=el.scrollWidth-el.clientWidth-10){el.scrollTo({left:0,behavior:"smooth"})}';
        echo 'else{el.scrollBy({left:iw+gap,behavior:"smooth"})}';
        echo '},autoSec*1000)});';
        echo 'var touchActive=false;';
        echo 'el.addEventListener("touchstart",function(){clearInterval(autoId);touchActive=true},{passive:true});';
        echo 'el.addEventListener("touchend",function(){touchActive=false;autoId=setInterval(function(){';
        echo 'if(el.scrollLeft>=el.scrollWidth-el.clientWidth-10){el.scrollTo({left:0,behavior:"smooth"})}';
        echo 'else{el.scrollBy({left:iw+gap,behavior:"smooth"})}';
        echo '},autoSec*1000)},{passive:true});';
        echo '}';
        // Initial coverflow update
        echo 'updateCoverflow();';
        echo '});';
        echo '}';

        // ── Expand spotlight ──
        echo 'function initExpand(){';
        echo 'if(rm||window.innerWidth<=640)return;';
        echo 'document.querySelectorAll("[data-pg-expand]").forEach(function(container){';
        echo 'var ratio=parseFloat(container.dataset.pgExpandRatio)||4;';
        echo 'var shrink=parseFloat(container.dataset.pgExpandShrink)||0.5;';
        echo 'var items=container.querySelectorAll(".olo-pg-item");';
        echo 'var cs=getComputedStyle(container);';
        echo 'var colCount=cs.gridTemplateColumns.split(" ").length;';
        echo 'var rowCount=cs.gridTemplateRows.split(" ").length;';
        echo 'items.forEach(function(item,idx){';
        echo 'item.addEventListener("mouseenter",function(){';
        echo 'var col=idx%colCount;var row=Math.floor(idx/colCount);';
        echo 'var cols=[];for(var c=0;c<colCount;c++)cols.push(c===col?ratio+"fr":shrink+"fr");';
        echo 'var rows=[];for(var r=0;r<rowCount;r++)rows.push(r===row?ratio+"fr":shrink+"fr");';
        echo 'container.style.gridTemplateColumns=cols.join(" ");';
        echo 'container.style.gridTemplateRows=rows.join(" ");';
        echo '});';
        echo '});';
        echo 'container.addEventListener("mouseleave",function(){';
        echo 'container.style.gridTemplateColumns="";';
        echo 'container.style.gridTemplateRows="";';
        echo '});';
        echo '});';
        echo '}';

        // ── Strip kinetic drag-to-scroll con momentum ──
        echo 'function initStrip(){';
        echo 'if(rm)return;';
        echo 'document.querySelectorAll("[data-pg-strip]").forEach(function(el){';
        echo 'var pressed=false,startX=0,startScroll=0,vel=0,amp=0,frame=0,ts=0,tgt=0,tick=0,raf=0,wasDrag=false;';
        echo 'var TC=325;';
        echo 'function track(){var now=Date.now();var elapsed=now-ts;var delta=el.scrollLeft-frame;ts=now;frame=el.scrollLeft;var v=1000*delta/(1+elapsed);vel=0.8*v+0.2*vel}';
        echo 'function momentum(){if(amp){var elapsed=Date.now()-ts;var delta=-amp*Math.exp(-elapsed/TC);if(Math.abs(delta)>0.5){el.scrollLeft=tgt+delta;raf=requestAnimationFrame(momentum)}else{el.scrollLeft=tgt}}}';
        echo 'function onMove(e){if(!pressed)return;var dx=e.clientX-startX;if(Math.abs(dx)>5){wasDrag=true;el.style.cursor="grabbing"}el.scrollLeft=startScroll-dx;e.preventDefault()}';
        echo 'function onUp(){if(!pressed)return;pressed=false;document.removeEventListener("pointermove",onMove);document.removeEventListener("pointerup",onUp);clearInterval(tick);el.style.cursor="grab";if(Math.abs(vel)>10){amp=0.8*vel;tgt=Math.round(el.scrollLeft+amp);ts=Date.now();raf=requestAnimationFrame(momentum)}}';
        echo 'el.style.cursor="grab";';
        echo 'el.addEventListener("pointerdown",function(e){if(e.button!==0)return;pressed=true;wasDrag=false;startX=e.clientX;startScroll=el.scrollLeft;vel=0;amp=0;frame=el.scrollLeft;ts=Date.now();if(raf){cancelAnimationFrame(raf);raf=0}clearInterval(tick);tick=setInterval(track,100);document.addEventListener("pointermove",onMove);document.addEventListener("pointerup",onUp)});';
        echo 'el.addEventListener("click",function(e){if(wasDrag){wasDrag=false;e.preventDefault();e.stopPropagation()}},true);';
        echo 'el.addEventListener("wheel",function(e){if(Math.abs(e.deltaY)>Math.abs(e.deltaX)){e.preventDefault();el.scrollLeft+=e.deltaY}},{passive:false});';
        echo '});';
        echo '}';

        // ── Strip arrows click ──
        echo 'function initStripArrows(){';
        echo 'document.querySelectorAll("[data-sa-dir]").forEach(function(btn){';
        echo 'btn.addEventListener("click",function(e){';
        echo 'e.preventDefault();';
        echo 'var wrap=btn.closest("[style]");if(!wrap)return;';
        echo 'var el=wrap.querySelector("[data-pg-strip]");';
        // Per marquee/split senza data-pg-strip, cerchiamo il primo figlio scrollabile
        echo 'if(!el){var ch=wrap.children;for(var i=0;i<ch.length;i++){if(ch[i].tagName!=="BUTTON"){if(ch[i].tagName!=="STYLE"){el=ch[i];break}}}}';
        echo 'if(!el)return;';
        echo 'var dir=parseInt(btn.getAttribute("data-sa-dir"));';
        echo 'var step=el.clientWidth*0.7;';
        echo 'el.scrollBy({left:dir*step,behavior:"smooth"});';
        echo '});});';
        echo '}';

        // ── Parallax (scroll-driven depth) ──
        echo 'function initParallax(){';
        echo 'if(rm)return;';
        echo 'document.querySelectorAll("[data-pg-parallax]").forEach(function(container){';
        echo 'var intensity=parseFloat(container.dataset.pgPlxIntensity)||50;';
        echo 'var items=container.querySelectorAll(".olo-pg-item[data-speed]");';
        echo 'if(!items.length)return;';
        // Salva il baseTransform iniziale
        echo 'items.forEach(function(it){if(!it.dataset.baseTransform){it.dataset.baseTransform=it.style.transform||""}});';
        echo 'var raf=0;';
        echo 'function update(){';
        echo 'var rect=container.getBoundingClientRect();';
        echo 'var ctrY=rect.top+rect.height/2;';
        echo 'var vpCtr=window.innerHeight/2;';
        echo 'var delta=ctrY-vpCtr;';
        echo 'items.forEach(function(it){';
        echo 'var sp=parseFloat(it.dataset.speed)||0;';
        echo 'var ty=Math.round(delta*sp);';
        echo 'var base=it.dataset.baseTransform||"";';
        echo 'it.style.transform=base+" translateY("+ty+"px)";';
        echo '});';
        echo '}';
        echo 'window.addEventListener("scroll",function(){if(!raf){raf=requestAnimationFrame(function(){update();raf=0})}},{passive:true});';
        echo 'update();';
        echo '});';
        echo '}';

        // ── Drift (multi-directional scroll parallax) ──
        echo 'function initDrift(){';
        echo 'if(rm)return;';
        echo 'document.querySelectorAll("[data-pg-drift]").forEach(function(container){';
        echo 'var items=container.querySelectorAll(".olo-pg-item[data-drift-sx]");';
        echo 'if(!items.length)return;';
        echo 'items.forEach(function(it){if(!it.dataset.baseTransform){it.dataset.baseTransform=it.style.transform||""}});';
        echo 'var raf=0;';
        echo 'function update(){';
        echo 'var rect=container.getBoundingClientRect();';
        echo 'var ctrY=rect.top+rect.height/2;';
        echo 'var vpCtr=window.innerHeight/2;';
        echo 'var delta=ctrY-vpCtr;';
        echo 'items.forEach(function(it){';
        echo 'var sx=parseFloat(it.dataset.driftSx)||0;';
        echo 'var sy=parseFloat(it.dataset.driftSy)||0;';
        echo 'var sr=parseFloat(it.dataset.driftSr)||0;';
        echo 'var tx=Math.round(delta*sx);';
        echo 'var ty=Math.round(delta*sy);';
        echo 'var r=Math.round(delta*sr*0.02*10)/10;';
        echo 'var base=it.dataset.baseTransform||"";';
        echo 'it.style.transform="translate("+tx+"px,"+ty+"px) rotate("+r+"deg)";';
        echo '});';
        echo '}';
        echo 'window.addEventListener("scroll",function(){if(!raf){raf=requestAnimationFrame(function(){update();raf=0})}},{passive:true});';
        echo 'update();';
        echo '});';
        echo '}';

        // ── Cascade (stacked cards that spread on scroll) ──
        echo 'function initCascade(){';
        echo 'if(rm)return;';
        echo 'document.querySelectorAll("[data-pg-cascade]").forEach(function(container){';
        echo 'var spread=parseFloat(container.dataset.pgCascadeSpread)||60;';
        echo 'var items=container.querySelectorAll(".olo-pg-item[data-cascade-idx]");';
        echo 'if(!items.length)return;';
        echo 'var total=items.length;';
        echo 'var raf=0;';
        echo 'function update(){';
        echo 'var rect=container.getBoundingClientRect();';
        echo 'var vpH=window.innerHeight;';
        // progress: 0 = top of container at bottom of viewport, 1 = bottom at top
        echo 'var progress=Math.max(0,Math.min(1,(vpH-rect.top)/(vpH+rect.height)));';
        echo 'items.forEach(function(it){';
        echo 'var idx=parseInt(it.dataset.cascadeIdx)||0;';
        echo 'var baseRot=parseFloat(it.dataset.cascadeRot)||0;';
        echo 'var baseSx=parseFloat(it.dataset.cascadeSx)||0;';
        // Ogni carta si sparge in direzione diversa con il progresso dello scroll
        echo 'var spreadY=progress*spread*idx;';
        echo 'var spreadX=progress*baseSx*spread/15;';
        echo 'var rot=baseRot*(0.3+progress*0.7);';
        echo 'it.style.transform="translate("+Math.round(spreadX)+"px,"+Math.round(spreadY)+"px) rotate("+rot.toFixed(1)+"deg)";';
        echo '});';
        echo '}';
        echo 'window.addEventListener("scroll",function(){if(!raf){raf=requestAnimationFrame(function(){update();raf=0})}},{passive:true});';
        echo 'update();';
        echo '});';
        echo '}';

        // ── Init ──
        echo 'function init(){initReveal();initTilt();initMagnetic();initFilmstrip();initExpand();initStrip();initStripArrows();initParallax();initDrift();initCascade()}';
        echo 'if(document.readyState==="loading"){document.addEventListener("DOMContentLoaded",init)}else{init()}';
        echo '})();';
        echo '</script>';
    }
}
