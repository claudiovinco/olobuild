<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Timeline_Tile extends Olo_Tile_Base {

    protected $type     = 'timeline';
    protected $name     = 'Timeline';
    protected $icon     = 'dashicons-backup';
    protected $category = 'interactive';
    protected $defaults = [
        'items' => [
            [ 'title' => 'Primo evento', 'description' => 'Descrizione del primo evento.', 'date' => '2024', 'image' => '', 'icon' => 'star', 'icon_color' => '' ],
            [ 'title' => 'Secondo evento', 'description' => 'Descrizione del secondo evento.', 'date' => '2025', 'image' => '', 'icon' => 'check', 'icon_color' => '' ],
            [ 'title' => 'Terzo evento', 'description' => 'Descrizione del terzo evento.', 'date' => '2026', 'image' => '', 'icon' => 'heart', 'icon_color' => '' ],
        ],
        'layout'              => 'vertical-center',
        'mobile_layout'       => 'vertical-left',
        'line_color'          => '',
        'line_width'          => '3',
        'line_style'          => 'solid',
        'line_progress'       => false,
        'line_progress_color' => '',
        'line_progress_width' => '',
        'marker_type'         => 'dot',
        'marker_size'         => '20',
        'marker_color'        => '',
        'marker_bg'           => '',
        'marker_border_width' => '3',
        'marker_border_color' => '',
        'marker_shape'        => 'circle',
        'marker_pulse'        => false,
        'card_bg'             => '',
        'card_text_color'     => '',
        'card_padding'        => '20',
        'card_border_radius'  => '12',
        'card_shadow'         => 'md',
        'card_border_width'   => '0',
        'card_border_color'   => '',
        'card_hover'          => 'lift',
        'card_arrow'          => true,
        'card_max_width'      => '',
        'card_media_ratio'    => 'auto',
        'card_media_margin'   => '0',
        'card_media_radius'   => '4',
        'date_position'       => 'outside',
        'date_color'          => '',
        'date_size'           => '14',
        'date_weight'         => '600',
        'title_size'          => '18',
        'title_weight'        => '600',
        'title_color'         => '',
        'description_size'    => '14',
        'description_color'   => '',
        'animation'           => 'fade-up',
        'stagger_delay'       => '150',
        'animation_duration'  => '600',
        'end_marker'          => true,
        'end_marker_icon'     => 'flag',
        'end_marker_color'    => '',
        'end_marker_bg'       => '',
        'end_marker_size'     => '',
        'h_card_width'        => '300',
        'h_visible_items'     => '3',
        'h_gap'               => '24',
        'h_arrow_color'       => '',
        'h_arrow_bg'          => '',
            'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() { return []; }

    public function render( $settings ) {
        $s     = wp_parse_args( $settings, $this->defaults );
        $items = $this->parse_items( $s['items'] );
        $count = count( $items );

        if ( $count === 0 ) {
            return '';
        }

        $uid    = 'olo-tl-' . wp_rand( 10000, 99999 );
        $layout = $s['layout'] ?: 'vertical-center';

        ob_start();

        $this->render_styles( $uid, $s, $layout, $count );

        if ( $layout === 'horizontal' ) {
            $this->render_horizontal( $uid, $items, $s );
        } else {
            $this->render_vertical( $uid, $items, $s, $layout );
        }

        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();
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

    private function parse_items( $raw ) {
        if ( ! is_array( $raw ) ) {
            return [];
        }
        $items = [];
        foreach ( $raw as $item ) {
            if ( is_array( $item ) ) {
                $items[] = [
                    'title'       => $item['title'] ?? '',
                    'description' => $item['description'] ?? '',
                    'date'        => $item['date'] ?? '',
                    'image'       => $item['image'] ?? '',
                    'video'       => $item['video'] ?? '',
                    'icon'        => $item['icon'] ?? '',
                    'icon_color'  => $item['icon_color'] ?? '',
                ];
            }
        }
        return $items;
    }

    /**
     * Render media (image or video) inside a timeline card.
     */
    private function render_media( $item ) {
        $image = $item['image'] ?? '';
        $video = $item['video'] ?? '';

        if ( empty( $image ) && empty( $video ) ) {
            return '';
        }

        $html = '<div class="olo-tl-media">';

        if ( ! empty( $video ) ) {
            $html .= $this->get_video_embed( $video );
        } elseif ( ! empty( $image ) ) {
            $html .= '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( wp_strip_all_tags( $item['title'] ?? '' ) ) . '" loading="lazy" />';
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Convert a video URL to embeddable HTML (YouTube, Vimeo, or direct file).
     */
    private function get_video_embed( $url ) {
        $url = trim( $url );

        // YouTube
        if ( preg_match( '/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $m ) ) {
            return '<iframe src="https://www.youtube-nocookie.com/embed/' . esc_attr( $m[1] ) . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe>';
        }

        // Vimeo
        if ( preg_match( '/vimeo\.com\/(\d+)/', $url, $m ) ) {
            return '<iframe src="https://player.vimeo.com/video/' . esc_attr( $m[1] ) . '?dnt=1" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen loading="lazy"></iframe>';
        }

        // Direct video file
        return '<video controls preload="metadata"><source src="' . esc_url( $url ) . '" type="video/mp4"></video>';
    }

    private function render_styles( $uid, $s, $layout, $count ) {
        $line_w    = intval( $s['line_width'] ) ?: 3;
        $line_clr  = $this->safe_color_css( $s['line_color'] ) ?: 'var(--olo-color-border, #E5E7EB)';
        $line_stl  = in_array( $s['line_style'], [ 'solid', 'dashed', 'dotted' ] ) ? $s['line_style'] : 'solid';
        $mk_size   = intval( $s['marker_size'] ) ?: 20;
        $mk_clr    = $this->safe_color_css( $s['marker_color'] ) ?: 'var(--olo-color-primary, #6366F1)';
        $mk_bg     = $this->safe_color_css( $s['marker_bg'] ) ?: 'var(--olo-color-background, #FFFFFF)';
        $mk_bw     = intval( $s['marker_border_width'] );
        $mk_bc     = $this->safe_color_css( $s['marker_border_color'] ) ?: $mk_clr;
        $mk_shape  = $s['marker_shape'] ?: 'circle';
        $mk_pulse  = ! empty( $s['marker_pulse'] );
        $mk_type   = $s['marker_type'] ?: 'dot';

        $c_bg      = $this->safe_color_css( $s['card_bg'] ) ?: 'var(--olo-color-muted, #F3F4F6)';
        $c_text    = $this->safe_color_css( $s['card_text_color'] ) ?: 'var(--olo-color-text, #374151)';
        $c_pad = Olo_Tile_Utils::spacing_css( $s['tile_padding'] ?? $s['card_padding'] ?? 20, 20 );
        $c_rad     = Olo_Tile_Utils::border_radius( $s['card_border_radius'] ?? 0 );
        $c_rad_hover_css = Olo_Tile_Utils::radius_force_css( $s['card_border_radius_hover'] ?? null );
        $c_bw      = intval( $s['card_border_width'] );
        $c_bc      = $this->safe_color_css( $s['card_border_color'] ) ?: 'var(--olo-color-border, #E5E7EB)';
        $c_hover   = $s['card_hover'] ?: 'none';
        $c_maxw    = intval( $s['card_max_width'] ?? 0 );

        $c_shadow = Olo_Tile_Utils::shadow( $s['card_shadow'] ?? 'none' );

        $d_clr   = $this->safe_color_css( $s['date_color'] ) ?: 'var(--olo-color-text-muted, #9CA3AF)';
        $d_size  = intval( $s['date_size'] ) ?: 14;
        $d_wt    = intval( $s['date_weight'] ) ?: 600;

        $t_size  = intval( $s['title_size'] ) ?: 18;
        $t_wt    = intval( $s['title_weight'] ) ?: 600;
        $t_clr   = $this->safe_color_css( $s['title_color'] );
        $desc_sz = intval( $s['description_size'] ) ?: 14;
        $desc_cl = $this->safe_color_css( $s['description_color'] );

        $anim      = $s['animation'] ?: 'none';
        $anim_dur  = intval( $s['animation_duration'] ) ?: 600;
        $stagger   = intval( $s['stagger_delay'] ) ?: 150;

        $mobile_layout = $s['mobile_layout'] ?: 'vertical-left';

        $progress     = ! empty( $s['line_progress'] );
        $progress_clr = $this->safe_color_css( $s['line_progress_color'] ) ?: 'var(--olo-color-primary, #6366F1)';
        $progress_w   = intval( $s['line_progress_width'] ?? 0 ) ?: ( $line_w + 4 );

        $mk_radius = $mk_shape === 'circle' ? '50%' : '4px';
        $mk_transform = $mk_shape === 'diamond' ? 'rotate(45deg)' : 'none';

        // End marker
        $end_marker = ! empty( $s['end_marker'] );
        $end_icon   = sanitize_text_field( $s['end_marker_icon'] ?? 'flag' );
        $end_clr    = $this->safe_color_css( $s['end_marker_color'] ?? '' ) ?: $mk_clr;
        $end_bg     = $this->safe_color_css( $s['end_marker_bg'] ?? '' ) ?: $mk_bg;
        $end_size   = intval( $s['end_marker_size'] ?? 0 ) ?: $mk_size;

        // Card arrow
        $card_arrow = ! empty( $s['card_arrow'] );

        // Media
        $m_ratio  = $s['card_media_ratio'] ?: 'auto';
        $m_margin = intval( $s['card_media_margin'] ?? 0 );
        $m_radius = Olo_Tile_Utils::border_radius( $s['card_media_radius'] ?? 4 );
        $m_radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['card_media_radius_hover'] ?? null );

        // Horizontal
        $h_gap    = intval( $s['h_gap'] ) ?: 24;
        $h_cw     = intval( $s['h_card_width'] ) ?: 300;
        $h_arr_c  = $this->safe_color_css( $s['h_arrow_color'] ) ?: 'var(--olo-color-muted, #F3F4F6)';
        $h_arr_bg = $this->safe_color_css( $s['h_arrow_bg'] ) ?: 'var(--olo-color-text, #374151)';

        ?>
        <style>
            /* === Container === */
            .<?php echo $uid; ?> {
                position: relative;
            }

            /* === Line === */
            .<?php echo $uid; ?> .olo-tl-line {
                position: absolute;
                <?php if ( $layout === 'horizontal' ) : ?>
                top: <?php echo intval( $mk_size / 2 ); ?>px;
                left: 0; right: 0;
                height: <?php echo $line_w; ?>px;
                <?php if ( $line_stl !== 'solid' ) : ?>
                background: none;
                border-top: <?php echo $line_w; ?>px <?php echo $line_stl; ?> <?php echo $line_clr; ?>;
                height: 0;
                <?php else : ?>
                background: <?php echo $line_clr; ?>;
                <?php endif; ?>
                <?php else : ?>
                top: 0; bottom: 0;
                width: <?php echo $line_w; ?>px;
                <?php if ( $line_stl !== 'solid' ) : ?>
                background: none;
                border-left: <?php echo $line_w; ?>px <?php echo $line_stl; ?> <?php echo $line_clr; ?>;
                width: 0;
                <?php else : ?>
                background: <?php echo $line_clr; ?>;
                <?php endif; ?>
                <?php if ( $layout === 'vertical-center' ) : ?>
                left: 50%;
                transform: translateX(-50%);
                <?php elseif ( $layout === 'vertical-right' ) : ?>
                right: <?php echo intval( $mk_size / 2 - $line_w / 2 ); ?>px;
                <?php else : ?>
                left: <?php echo intval( $mk_size / 2 - $line_w / 2 ); ?>px;
                <?php endif; ?>
                <?php endif; ?>
                z-index: 1;
            }

            /* === Progress line === */
            <?php if ( $progress && $layout !== 'horizontal' ) : ?>
            .<?php echo $uid; ?> .olo-tl-progress {
                position: absolute;
                top: 0;
                height: 0%;
                width: <?php echo $progress_w; ?>px;
                background: <?php echo $progress_clr; ?>;
                border-radius: <?php echo intval( $progress_w / 2 ); ?>px;
                z-index: 0;
                transition: height 0.1s linear;
                <?php if ( $layout === 'vertical-center' ) : ?>
                left: 50%;
                transform: translateX(-50%);
                <?php elseif ( $layout === 'vertical-right' ) : ?>
                right: <?php echo intval( $mk_size / 2 - $progress_w / 2 ); ?>px;
                <?php else : ?>
                left: <?php echo intval( $mk_size / 2 - $progress_w / 2 ); ?>px;
                <?php endif; ?>
            }
            <?php endif; ?>

            /* === Marker === */
            .<?php echo $uid; ?> .olo-tl-marker {
                width: <?php echo $mk_size; ?>px;
                height: <?php echo $mk_size; ?>px;
                border-radius: <?php echo $mk_radius; ?>;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                position: relative;
                z-index: 3;
                font-size: <?php echo intval( $mk_size * 0.45 ); ?>px;
                font-weight: 700;
                color: <?php echo $mk_clr; ?>;
                background: <?php echo $mk_type === 'dot' ? $mk_clr : $mk_bg; ?>;
                transform: <?php echo $mk_transform; ?>;
                <?php if ( $mk_bw > 0 ) : ?>
                border: <?php echo $mk_bw; ?>px solid <?php echo $mk_bc; ?>;
                <?php endif; ?>
            }

            <?php if ( $mk_pulse ) : ?>
            .<?php echo $uid; ?> .olo-tl-marker::after {
                content: '';
                position: absolute;
                inset: -4px;
                border-radius: <?php echo $mk_radius; ?>;
                border: 2px solid <?php echo $mk_clr; ?>;
                animation: olo-tl-pulse-<?php echo $uid; ?> 2s ease-out infinite;
            }
            @keyframes olo-tl-pulse-<?php echo $uid; ?> {
                0% { transform: scale(1); opacity: 0.8; }
                100% { transform: scale(1.8); opacity: 0; }
            }
            <?php endif; ?>

            /* === End marker === */
            <?php if ( $end_marker ) : ?>
            .<?php echo $uid; ?> .olo-tl-end-marker {
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
                z-index: 3;
            }
            .<?php echo $uid; ?> .olo-tl-end-marker .olo-tl-marker {
                width: <?php echo $end_size; ?>px;
                height: <?php echo $end_size; ?>px;
                background: <?php echo $end_bg; ?>;
                color: <?php echo $end_clr; ?>;
                font-size: <?php echo intval( $end_size * 0.5 ); ?>px;
            }
            <?php endif; ?>

            /* === Card === */
            .<?php echo $uid; ?> .olo-tl-card {
                background: <?php echo $c_bg; ?>;
                color: <?php echo $c_text; ?>;
                padding: <?php echo $c_pad; ?>;
                border-radius: <?php echo $c_rad; ?>;
                box-shadow: <?php echo $c_shadow; ?>;
                position: relative;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                <?php if ( $c_bw > 0 ) : ?>
                border: <?php echo $c_bw; ?>px solid <?php echo $c_bc; ?>;
                <?php endif; ?>
                <?php if ( $c_maxw > 0 ) : ?>
                max-width: <?php echo $c_maxw; ?>px;
                <?php endif; ?>
            }
            <?php if ( $c_rad_hover_css !== '' ) : ?>.<?php echo $uid; ?> .olo-tl-card{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?> .olo-tl-card:hover{border-radius:<?php echo $c_rad_hover_css; ?> !important}<?php endif; ?>

            <?php if ( $c_hover === 'lift' ) : ?>
            .<?php echo $uid; ?> .olo-tl-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 8px 24px rgba(0,0,0,.3);
            }
            <?php elseif ( $c_hover === 'glow' ) : ?>
            .<?php echo $uid; ?> .olo-tl-card:hover {
                box-shadow: 0 0 20px color-mix(in srgb, <?php echo $mk_clr; ?> 25%, transparent);
            }
            <?php elseif ( $c_hover === 'scale' ) : ?>
            .<?php echo $uid; ?> .olo-tl-card:hover {
                transform: scale(1.03);
            }
            <?php endif; ?>

            /* === Media === */
            .<?php echo $uid; ?> .olo-tl-media {
                overflow: hidden;
                border-radius: <?php echo $m_radius; ?>;
                margin-bottom: 8px;
                <?php if ( $m_ratio !== 'auto' ) : ?>
                aspect-ratio: <?php echo esc_attr( $m_ratio ); ?>;
                <?php endif; ?>
                <?php if ( $m_margin < 0 ) : ?>
                margin: <?php echo -$c_pad; ?>px <?php echo -$c_pad; ?>px 8px <?php echo -$c_pad; ?>px;
                border-radius: 0;
                <?php elseif ( $m_margin > 0 ) : ?>
                margin: <?php echo $m_margin; ?>px <?php echo $m_margin; ?>px 8px <?php echo $m_margin; ?>px;
                <?php endif; ?>
            }
            <?php if ( $m_radius_hover_css !== '' ) : ?>.<?php echo $uid; ?> .olo-tl-media{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?> .olo-tl-media:hover{border-radius:<?php echo $m_radius_hover_css; ?> !important}<?php endif; ?>
            .<?php echo $uid; ?> .olo-tl-media img,
            .<?php echo $uid; ?> .olo-tl-media video,
            .<?php echo $uid; ?> .olo-tl-media iframe {
                width: 100%;
                <?php if ( $m_ratio !== 'auto' ) : ?>
                height: 100%;
                object-fit: cover;
                <?php endif; ?>
                display: block;
            }

            /* === Card arrow === */
            <?php if ( $card_arrow && $layout !== 'horizontal' ) : ?>
            .<?php echo $uid; ?> .olo-tl-arrow {
                position: absolute;
                top: 14px;
                width: 0; height: 0;
                border-style: solid;
            }
            .<?php echo $uid; ?> .olo-tl-arrow--left {
                left: -8px;
                border-width: 8px 8px 8px 0;
                border-color: transparent <?php echo $c_bg; ?> transparent transparent;
            }
            .<?php echo $uid; ?> .olo-tl-arrow--right {
                right: -8px;
                border-width: 8px 0 8px 8px;
                border-color: transparent transparent transparent <?php echo $c_bg; ?>;
            }
            <?php endif; ?>

            /* === Date === */
            .<?php echo $uid; ?> .olo-tl-date {
                font-size: <?php echo $d_size; ?>px;
                font-weight: <?php echo $d_wt; ?>;
                color: <?php echo $d_clr; ?>;
            }

            /* === Typography === */
            .<?php echo $uid; ?> .olo-tl-title {
                font-size: <?php echo $t_size; ?>px;
                font-weight: <?php echo $t_wt; ?>;
                <?php if ( $t_clr ) : ?>color: <?php echo $t_clr; ?>;<?php endif; ?>
                margin: 0 0 6px;
                line-height: 1.3;
            }
            .<?php echo $uid; ?> .olo-tl-desc {
                font-size: <?php echo $desc_sz; ?>px;
                <?php if ( $desc_cl ) : ?>color: <?php echo $desc_cl; ?>;<?php endif; ?>
                opacity: 0.85;
                line-height: 1.5;
                margin: 0;
            }
            .<?php echo $uid; ?> .olo-tl-desc p { margin: 0 0 0.5em; }
            .<?php echo $uid; ?> .olo-tl-desc p:last-child { margin-bottom: 0; }

            /* === Animation === */
            <?php if ( $anim !== 'none' ) : ?>
            .<?php echo $uid; ?> .olo-tl-item {
                opacity: 0;
            }
            .<?php echo $uid; ?> .olo-tl-item.olo-tl-visible {
                animation: olo-tl-anim-<?php echo $uid; ?> <?php echo $anim_dur; ?>ms ease forwards;
            }
            <?php if ( $anim === 'fade-up' ) : ?>
            @keyframes olo-tl-anim-<?php echo $uid; ?> {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }
            <?php elseif ( $anim === 'fade-in' ) : ?>
            @keyframes olo-tl-anim-<?php echo $uid; ?> {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            <?php elseif ( $anim === 'slide-left' ) : ?>
            @keyframes olo-tl-anim-<?php echo $uid; ?> {
                from { opacity: 0; transform: translateX(-40px); }
                to { opacity: 1; transform: translateX(0); }
            }
            <?php elseif ( $anim === 'slide-right' ) : ?>
            @keyframes olo-tl-anim-<?php echo $uid; ?> {
                from { opacity: 0; transform: translateX(40px); }
                to { opacity: 1; transform: translateX(0); }
            }
            <?php elseif ( $anim === 'zoom-in' ) : ?>
            @keyframes olo-tl-anim-<?php echo $uid; ?> {
                from { opacity: 0; transform: scale(0.8); }
                to { opacity: 1; transform: scale(1); }
            }
            <?php endif; ?>
            <?php endif; ?>

            /* === Vertical layouts === */
            <?php if ( $layout !== 'horizontal' ) : ?>
            .<?php echo $uid; ?> .olo-tl-items {
                display: flex;
                flex-direction: column;
                gap: 24px;
                position: relative;
                z-index: 2;
            }
            .<?php echo $uid; ?> .olo-tl-item {
                display: flex;
                align-items: flex-start;
                gap: 16px;
                position: relative;
            }
            .<?php echo $uid; ?> .olo-tl-marker-wrap {
                flex-shrink: 0;
                display: flex;
                align-items: flex-start;
                justify-content: center;
                position: relative;
                z-index: 3;
            }

            <?php if ( $layout === 'vertical-center' ) : ?>
            .<?php echo $uid; ?> .olo-tl-date-col {
                flex: 1;
                min-width: 0;
                display: flex;
                align-items: flex-start;
                padding-top: 4px;
            }
            .<?php echo $uid; ?> .olo-tl-card-col {
                flex: 1;
                min-width: 0;
            }
            .<?php echo $uid; ?> .olo-tl-item--left {
                flex-direction: row;
            }
            .<?php echo $uid; ?> .olo-tl-item--left .olo-tl-date-col {
                justify-content: flex-end;
                text-align: right;
            }
            .<?php echo $uid; ?> .olo-tl-item--right {
                flex-direction: row-reverse;
            }
            .<?php echo $uid; ?> .olo-tl-item--right .olo-tl-date-col {
                justify-content: flex-start;
                text-align: left;
            }
            <?php elseif ( $layout === 'vertical-right' ) : ?>
            .<?php echo $uid; ?> .olo-tl-item {
                flex-direction: row-reverse;
            }
            .<?php echo $uid; ?> .olo-tl-card-col {
                flex: 1;
                min-width: 0;
            }
            <?php else : /* vertical-left */ ?>
            .<?php echo $uid; ?> .olo-tl-card-col {
                flex: 1;
                min-width: 0;
            }
            <?php endif; ?>
            <?php endif; ?>

            /* === Horizontal === */
            <?php if ( $layout === 'horizontal' ) : ?>
            .<?php echo $uid; ?> .olo-tl-h-wrap {
                position: relative;
            }
            .<?php echo $uid; ?> .olo-tl-h-viewport {
                overflow: hidden;
                position: relative;
                padding: 0 4px;
                max-width: <?php echo intval( $h_vis * $h_cw + ( $h_vis - 1 ) * $h_gap ); ?>px;
                margin: 0 auto;
            }
            .<?php echo $uid; ?> .olo-tl-h-track {
                display: flex;
                gap: <?php echo $h_gap; ?>px;
                transition: transform 0.4s ease;
                position: relative;
                z-index: 2;
            }
            .<?php echo $uid; ?> .olo-tl-h-item {
                width: <?php echo $h_cw; ?>px;
                flex-shrink: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 12px;
            }
            .<?php echo $uid; ?> .olo-tl-h-arrow {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                z-index: 10;
                width: 40px; height: 40px;
                border-radius: 50%;
                border: none;
                cursor: pointer;
                font-size: 22px;
                font-weight: 700;
                display: flex;
                align-items: center;
                justify-content: center;
                color: <?php echo $h_arr_c; ?>;
                background: <?php echo $h_arr_bg; ?>;
                transition: opacity 0.2s;
            }
            .<?php echo $uid; ?> .olo-tl-h-arrow:hover {
                opacity: 0.8;
            }
            .<?php echo $uid; ?> .olo-tl-h-arrow--prev { left: -12px; }
            .<?php echo $uid; ?> .olo-tl-h-arrow--next { right: -12px; }
            <?php endif; ?>

            /* === Mobile responsive === */
            @media (max-width: 767px) {
                <?php if ( $layout === 'vertical-center' ) : ?>
                .<?php echo $uid; ?> .olo-tl-item--left,
                .<?php echo $uid; ?> .olo-tl-item--right {
                    <?php if ( $mobile_layout === 'vertical-right' ) : ?>
                    flex-direction: row-reverse !important;
                    <?php else : ?>
                    flex-direction: row !important;
                    <?php endif; ?>
                }
                .<?php echo $uid; ?> .olo-tl-date-col {
                    display: none !important;
                }
                .<?php echo $uid; ?> .olo-tl-line {
                    left: <?php echo intval( $mk_size / 2 - $line_w / 2 ); ?>px !important;
                    transform: none !important;
                    <?php if ( $mobile_layout === 'vertical-right' ) : ?>
                    left: auto !important;
                    right: <?php echo intval( $mk_size / 2 - $line_w / 2 ); ?>px !important;
                    <?php endif; ?>
                }
                <?php if ( $progress ) : ?>
                .<?php echo $uid; ?> .olo-tl-progress {
                    left: <?php echo intval( $mk_size / 2 - $line_w / 2 ); ?>px !important;
                    transform: none !important;
                    <?php if ( $mobile_layout === 'vertical-right' ) : ?>
                    left: auto !important;
                    right: <?php echo intval( $mk_size / 2 - $line_w / 2 ); ?>px !important;
                    <?php endif; ?>
                }
                <?php endif; ?>
                .<?php echo $uid; ?> .olo-tl-arrow--right {
                    <?php if ( $mobile_layout === 'vertical-right' ) : ?>
                    display: block;
                    <?php else : ?>
                    display: none;
                    <?php endif; ?>
                }
                .<?php echo $uid; ?> .olo-tl-arrow--left {
                    <?php if ( $mobile_layout === 'vertical-right' ) : ?>
                    display: none;
                    <?php else : ?>
                    display: block;
                    <?php endif; ?>
                }
                <?php endif; ?>

                <?php if ( $layout === 'horizontal' ) : ?>
                .<?php echo $uid; ?> .olo-tl-h-desktop { display: none !important; }
                .<?php echo $uid; ?> .olo-tl-h-mobile { display: flex !important; }
                <?php endif; ?>
            }

            <?php if ( $layout === 'horizontal' ) : ?>
            .<?php echo $uid; ?> .olo-tl-h-mobile {
                display: none;
                flex-direction: column;
                gap: 24px;
                position: relative;
            }
            .<?php echo $uid; ?> .olo-tl-h-mobile .olo-tl-line-mob {
                position: absolute;
                top: 0; bottom: 0;
                left: <?php echo intval( $mk_size / 2 - $line_w / 2 ); ?>px;
                width: <?php echo $line_w; ?>px;
                background: <?php echo $line_clr; ?>;
                z-index: 1;
            }
            .<?php echo $uid; ?> .olo-tl-h-mobile .olo-tl-mob-item {
                display: flex;
                align-items: flex-start;
                gap: 16px;
                position: relative;
            }
            .<?php echo $uid; ?> .olo-tl-h-mobile .olo-tl-mob-card {
                flex: 1; min-width: 0;
            }
            <?php endif; ?>
        </style>
        <?php
    }

    private function render_vertical( $uid, $items, $s, $layout ) {
        $count      = count( $items );
        $anim       = $s['animation'] ?: 'none';
        $stagger    = intval( $s['stagger_delay'] ) ?: 150;
        $date_pos   = $s['date_position'] ?: 'outside';
        $card_arrow = ! empty( $s['card_arrow'] );
        $mk_type    = $s['marker_type'] ?: 'dot';
        $progress   = ! empty( $s['line_progress'] );

        $end_marker = ! empty( $s['end_marker'] );
        $end_icon   = sanitize_text_field( $s['end_marker_icon'] ?? 'flag' );
        $mk_clr     = $this->safe_color_css( $s['marker_color'] ) ?: 'var(--olo-color-primary, #6366F1)';
        $mk_bg      = $this->safe_color_css( $s['marker_bg'] ) ?: 'var(--olo-color-background, #FFFFFF)';
        $end_clr    = $this->safe_color_css( $s['end_marker_color'] ?? '' ) ?: $mk_clr;

        $scrollspy = '';
        if ( $anim !== 'none' ) {
            $scrollspy = ' uk-scrollspy="cls: olo-tl-visible; target: .olo-tl-item; delay: ' . $stagger . '; repeat: false"';
        }
        ?>
        <div class="olo-timeline <?php echo esc_attr( $uid ); ?>"<?php echo $scrollspy; ?>>
            <div class="olo-tl-line"></div>
            <?php if ( $progress ) : ?>
            <div class="olo-tl-progress" data-olo-progress></div>
            <?php endif; ?>

            <div class="olo-tl-items">
                <?php foreach ( $items as $i => $item ) :
                    $side_class = '';
                    if ( $layout === 'vertical-center' ) {
                        $side_class = $i % 2 === 0 ? 'olo-tl-item--left' : 'olo-tl-item--right';
                    }
                    $arrow_class = '';
                    if ( $card_arrow ) {
                        if ( $layout === 'vertical-center' ) {
                            $arrow_class = $i % 2 === 0 ? 'olo-tl-arrow--left' : 'olo-tl-arrow--right';
                        } elseif ( $layout === 'vertical-right' ) {
                            $arrow_class = 'olo-tl-arrow--right';
                        } else {
                            $arrow_class = 'olo-tl-arrow--left';
                        }
                    }
                ?>
                <div class="olo-tl-item <?php echo esc_attr( $side_class ); ?>">
                    <?php if ( $layout === 'vertical-center' ) : ?>
                    <div class="olo-tl-date-col">
                        <?php if ( $date_pos === 'outside' && ! empty( $item['date'] ) ) : ?>
                        <span class="olo-tl-date"><?php echo esc_html( $item['date'] ); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <div class="olo-tl-marker-wrap">
                        <div class="olo-tl-marker">
                            <?php if ( $mk_type === 'icon' && ! empty( $item['icon'] ) ) : ?>
                                <span uk-icon="icon: <?php echo esc_attr( $item['icon'] ); ?>" <?php if ( ! empty( $item['icon_color'] ) ) echo 'style="color:' . $this->safe_color_css( $item['icon_color'] ) . '"'; ?>></span>
                            <?php elseif ( $mk_type === 'number' ) : ?>
                                <?php echo $i + 1; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="olo-tl-card-col">
                        <?php if ( $date_pos === 'above' && ! empty( $item['date'] ) ) : ?>
                        <div class="olo-tl-date" style="margin-bottom:6px"><?php echo esc_html( $item['date'] ); ?></div>
                        <?php endif; ?>

                        <div class="olo-tl-card">
                            <?php if ( $card_arrow && ! empty( $arrow_class ) ) : ?>
                            <div class="olo-tl-arrow <?php echo esc_attr( $arrow_class ); ?>"></div>
                            <?php endif; ?>

                            <?php echo $this->render_media( $item ); ?>

                            <?php if ( $date_pos === 'inside' && ! empty( $item['date'] ) ) : ?>
                            <div class="olo-tl-date" style="margin-bottom:4px"><?php echo esc_html( $item['date'] ); ?></div>
                            <?php endif; ?>

                            <?php list( $tlt_cls, $tlt_data ) = $this->tfx_attrs( $s, "title", wp_strip_all_tags( $item["title"] ) ); ?><h4 class="olo-tl-title<?php echo $tlt_cls; ?>"<?php echo $tlt_data; ?>><?php echo esc_html( wp_strip_all_tags( $item["title"] ) ); ?></h4>
                            <?php list( $tld_cls, $tld_data ) = $this->tfx_attrs( $s, "description", wp_strip_all_tags( $item["description"] ) ); ?><div class="olo-tl-desc<?php echo $tld_cls; ?>"<?php echo $tld_data; ?>><?php echo nl2br( esc_html( wp_strip_all_tags( $item["description"] ) ) ); ?></div>
                        </div>

                        <?php if ( $layout !== 'vertical-center' && $date_pos === 'outside' && ! empty( $item['date'] ) ) : ?>
                        <div class="olo-tl-date" style="margin-top:6px"><?php echo esc_html( $item['date'] ); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php if ( $end_marker ) : ?>
                <div class="olo-tl-item" style="display:flex;align-items:center;justify-content:center;gap:16px">
                    <?php if ( $layout === 'vertical-center' ) : ?>
                    <div class="olo-tl-date-col"></div>
                    <?php endif; ?>
                    <div class="olo-tl-end-marker olo-tl-marker-wrap">
                        <div class="olo-tl-marker">
                            <?php if ( ! empty( $end_icon ) ) : ?>
                                <span uk-icon="icon: <?php echo esc_attr( $end_icon ); ?>" style="color:<?php echo $end_clr; ?>"></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ( $layout === 'vertical-center' ) : ?>
                    <div class="olo-tl-card-col"></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ( $progress ) : ?>
        <script>
        (function(){
            var el = document.querySelector('.<?php echo $uid; ?>');
            if (!el) return;
            var prog = el.querySelector('[data-olo-progress]');
            if (!prog) return;
            function update() {
                var rect = el.getBoundingClientRect();
                var mid = window.innerHeight / 2;
                var total = rect.height;
                if (total <= 0) return;
                var scrolled = mid - rect.top;
                var pct = Math.min(100, Math.max(0, (scrolled / total) * 100));
                prog.style.height = pct + '%';
            }
            window.addEventListener('scroll', update, {passive: true});
            update();
        })();
        </script>
        <?php endif; ?>
        <?php
    }

    private function render_horizontal( $uid, $items, $s ) {
        $count    = count( $items );
        $mk_type  = $s['marker_type'] ?: 'dot';
        $date_pos = $s['date_position'] ?: 'outside';
        $anim     = $s['animation'] ?: 'none';
        $stagger  = intval( $s['stagger_delay'] ) ?: 150;
        $h_vis    = intval( $s['h_visible_items'] ) ?: 3;
        $h_cw     = intval( $s['h_card_width'] ) ?: 300;
        $h_gap    = intval( $s['h_gap'] ) ?: 24;
        $mk_size  = intval( $s['marker_size'] ) ?: 20;
        $line_w   = intval( $s['line_width'] ) ?: 3;
        $card_arrow = ! empty( $s['card_arrow'] );

        $end_marker = ! empty( $s['end_marker'] );
        $end_icon   = sanitize_text_field( $s['end_marker_icon'] ?? 'flag' );
        $mk_clr     = $this->safe_color_css( $s['marker_color'] ) ?: 'var(--olo-color-primary, #6366F1)';
        $end_clr    = $this->safe_color_css( $s['end_marker_color'] ?? '' ) ?: $mk_clr;

        $scrollspy = '';
        if ( $anim !== 'none' ) {
            $scrollspy = ' uk-scrollspy="cls: olo-tl-visible; target: .olo-tl-item; delay: ' . $stagger . '; repeat: false"';
        }
        ?>
        <div class="olo-timeline <?php echo esc_attr( $uid ); ?>"<?php echo $scrollspy; ?>>
            <!-- Desktop horizontal -->
            <div class="olo-tl-h-desktop olo-tl-h-wrap">
                <button class="olo-tl-h-arrow olo-tl-h-arrow--prev" data-olo-prev><?php echo esc_html( olo_t( '&lsaquo;' ) ); ?></button>
                <button class="olo-tl-h-arrow olo-tl-h-arrow--next" data-olo-next><?php echo esc_html( olo_t( '&rsaquo;' ) ); ?></button>
                <div class="olo-tl-h-viewport">
                    <div class="olo-tl-line"></div>
                    <div class="olo-tl-h-track" data-olo-track>
                        <?php foreach ( $items as $i => $item ) : ?>
                        <div class="olo-tl-h-item olo-tl-item">
                            <div class="olo-tl-marker">
                                <?php if ( $mk_type === 'icon' && ! empty( $item['icon'] ) ) : ?>
                                    <span uk-icon="icon: <?php echo esc_attr( $item['icon'] ); ?>" <?php if ( ! empty( $item['icon_color'] ) ) echo 'style="color:' . $this->safe_color_css( $item['icon_color'] ) . '"'; ?>></span>
                                <?php elseif ( $mk_type === 'number' ) : ?>
                                    <?php echo $i + 1; ?>
                                <?php endif; ?>
                            </div>
                            <div class="olo-tl-card" style="width:100%">
                                <?php echo $this->render_media( $item ); ?>

                                <?php if ( $date_pos === 'inside' && ! empty( $item['date'] ) ) : ?>
                                <div class="olo-tl-date" style="margin-bottom:4px"><?php echo esc_html( $item['date'] ); ?></div>
                                <?php endif; ?>

                                <?php list( $tlt_cls, $tlt_data ) = $this->tfx_attrs( $s, "title", wp_strip_all_tags( $item["title"] ) ); ?><h4 class="olo-tl-title<?php echo $tlt_cls; ?>"<?php echo $tlt_data; ?>><?php echo esc_html( wp_strip_all_tags( $item["title"] ) ); ?></h4>
                                <?php list( $tld_cls, $tld_data ) = $this->tfx_attrs( $s, "description", wp_strip_all_tags( $item["description"] ) ); ?><div class="olo-tl-desc<?php echo $tld_cls; ?>"<?php echo $tld_data; ?>><?php echo nl2br( esc_html( wp_strip_all_tags( $item["description"] ) ) ); ?></div>
                            </div>
                            <?php if ( $date_pos === 'outside' && ! empty( $item['date'] ) ) : ?>
                            <div class="olo-tl-date"><?php echo esc_html( $item['date'] ); ?></div>
                            <?php elseif ( $date_pos === 'above' && ! empty( $item['date'] ) ) : ?>
                            <div class="olo-tl-date" style="margin-bottom:4px;order:-1"><?php echo esc_html( $item['date'] ); ?></div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>

                        <?php if ( $end_marker ) : ?>
                        <div class="olo-tl-h-item olo-tl-item" style="justify-content:flex-start">
                            <div class="olo-tl-end-marker">
                                <div class="olo-tl-marker">
                                    <?php if ( ! empty( $end_icon ) ) : ?>
                                        <span uk-icon="icon: <?php echo esc_attr( $end_icon ); ?>" style="color:<?php echo $end_clr; ?>"></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Mobile fallback: vertical-left -->
            <div class="olo-tl-h-mobile">
                <div class="olo-tl-line-mob"></div>
                <?php foreach ( $items as $i => $item ) : ?>
                <div class="olo-tl-mob-item olo-tl-item">
                    <div class="olo-tl-marker-wrap">
                        <div class="olo-tl-marker">
                            <?php if ( $mk_type === 'icon' && ! empty( $item['icon'] ) ) : ?>
                                <span uk-icon="icon: <?php echo esc_attr( $item['icon'] ); ?>" <?php if ( ! empty( $item['icon_color'] ) ) echo 'style="color:' . $this->safe_color_css( $item['icon_color'] ) . '"'; ?>></span>
                            <?php elseif ( $mk_type === 'number' ) : ?>
                                <?php echo $i + 1; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="olo-tl-mob-card">
                        <div class="olo-tl-card">
                            <?php echo $this->render_media( $item ); ?>

                            <?php if ( ! empty( $item['date'] ) ) : ?>
                            <div class="olo-tl-date" style="margin-bottom:4px"><?php echo esc_html( $item['date'] ); ?></div>
                            <?php endif; ?>

                            <?php list( $tlt_cls, $tlt_data ) = $this->tfx_attrs( $s, "title", wp_strip_all_tags( $item["title"] ) ); ?><h4 class="olo-tl-title<?php echo $tlt_cls; ?>"<?php echo $tlt_data; ?>><?php echo esc_html( wp_strip_all_tags( $item["title"] ) ); ?></h4>
                            <?php list( $tld_cls, $tld_data ) = $this->tfx_attrs( $s, "description", wp_strip_all_tags( $item["description"] ) ); ?><div class="olo-tl-desc<?php echo $tld_cls; ?>"<?php echo $tld_data; ?>><?php echo nl2br( esc_html( wp_strip_all_tags( $item["description"] ) ) ); ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php if ( $end_marker ) : ?>
                <div class="olo-tl-mob-item olo-tl-item">
                    <div class="olo-tl-end-marker olo-tl-marker-wrap">
                        <div class="olo-tl-marker">
                            <?php if ( ! empty( $end_icon ) ) : ?>
                                <span uk-icon="icon: <?php echo esc_attr( $end_icon ); ?>" style="color:<?php echo $end_clr; ?>"></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="olo-tl-mob-card"></div>
                </div>
                <?php endif; ?>
            </div>

            <script>
            (function(){
                var el = document.querySelector('.<?php echo $uid; ?>');
                if (!el) return;
                var track = el.querySelector('[data-olo-track]');
                var prev = el.querySelector('[data-olo-prev]');
                var next = el.querySelector('[data-olo-next]');
                if (!track || !prev || !next) return;
                var scrollPx = 0;
                var cardW = <?php echo $h_cw; ?>;
                var gap = <?php echo $h_gap; ?>;
                var total = <?php echo $count; ?>;
                var visible = <?php echo $h_vis; ?>;
                var viewportW = visible * cardW + (visible - 1) * gap;
                var trackW = total * cardW + (total - 1) * gap;
                var maxScroll = Math.max(0, trackW - viewportW);
                var step = cardW + gap;
                function move() {
                    track.style.transform = 'translateX(-' + scrollPx + 'px)';
                }
                prev.addEventListener('click', function() { scrollPx = Math.max(0, scrollPx - step); move(); });
                next.addEventListener('click', function() { scrollPx = Math.min(maxScroll, scrollPx + step); move(); });
            })();
            </script>
        </div>
        <?php
    }
}
