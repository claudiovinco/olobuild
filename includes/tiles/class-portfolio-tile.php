<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Portfolio_Tile extends Olobuild_Tile_Base {

    protected $type     = 'portfolio';
    protected $name     = 'Portfolio';
    protected $icon     = 'dashicons-portfolio';
    protected $category = 'dynamic';
    protected $defaults = [
        'preset'              => 'editorial-magazine',
        'source'              => 'manual',
        'post_type'           => 'post',
        'taxonomy'            => 'category',
        'posts_per_page'      => 12,
        'columns'             => 3,
        'gap'                 => 20,
        'filter_bar'          => true,
        'filter_style'        => 'buttons',
        'filter_all_label'    => 'Tutti',
        'filter_color'        => '',
        'filter_active_color' => '',
        'layout'              => 'grid',
        'hover_effect'        => 'fade',
        'caption_position'    => 'overlay',
        'caption_corner'      => 'bottom-left',
        'show_title'          => true,
        'show_category'       => true,
        'show_excerpt'        => false,
        'title_color'         => '',
        'text_color'          => '',
        'bg_color'            => '',
        'accent_color'        => '',
        'overlay_color'       => '#000000',
        'overlay_opacity'     => 80,
        'image_ratio'         => '4:3',
        'object_position'     => 'center center',
        'border_radius'       => 8,
        'animation'           => 'fade',
        'font_family'         => 'inherit',
        'font_weight'         => '500',
        'text_transform'      => 'none',
        'letter_spacing'      => 0,
        'enable_search'       => false,
        'search_placeholder'  => 'Cerca progetto…',
        'cursor_label_enabled' => false,
        'cursor_label_text'   => 'Vedi progetto',
        'stagger_entrance'    => false,
        'stagger_delay'       => 80,
        'dim_others'          => false,
        'featured_ribbon'     => false,
        'featured_ribbon_text' => 'In evidenza',
        'year_stamp'          => false,
        'index_numbering'     => false,
        'external_link_badge' => false,
        'grayscale_default'   => false,
        'carousel_speed'      => 30,
        'carousel_pause_on_hover' => true,
        'container_padding'   => [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 ],
        'container_radius'    => [],
        'effect_color'        => '',
        'effect_intensity'    => 'medium',
        'effect_speed'        => 0,
        'wow_disable'           => false,
        'wow_backdrop_blur'     => 0,
        'wow_backdrop_saturate' => 100,
        'wow_border_style'      => 'solid',
        'wow_font_family'       => 'inherit',
        'wow_rotation'          => 0,
        'wow_perspective'       => 0,
        'wow_tilt_x'            => 0,
        'wow_glow_pulse'        => false,
        'wow_title_glow'        => false,
        'wow_scanlines'         => false,

        'wow_terminal_prompt' => false,
        'items'               => [],
        'shadow'              => 'none',
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
        return [];
    }

    private function font_family_css( $val ) {
        switch ( $val ) {
            case 'sans':  return 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif';
            case 'serif': return 'Georgia, "Times New Roman", Times, serif';
            case 'mono':  return 'ui-monospace, "SF Mono", Menlo, Consolas, monospace';
            default:      return 'inherit';
        }
    }

    /**
     * CSS per layout speciali (oltre grid/masonry classico).
     */
    private function get_layout_css( $layout, $uid, $cols, $gap ) {
        switch ( $layout ) {
            case 'bento':
                return ".{$uid}-grid{display:grid;grid-template-columns:repeat({$cols},1fr);grid-auto-rows:minmax(160px,auto);gap:{$gap}px}"
                     . ".{$uid}-grid .olo-pf-item:nth-child(7n+1){grid-column:span 2;grid-row:span 2}"
                     . ".{$uid}-grid .olo-pf-item:nth-child(7n+4){grid-column:span 2}"
                     . ".{$uid}-grid .olo-pf-item:nth-child(7n+6){grid-row:span 2}"
                     . ".{$uid}-grid .olo-pf-item .olo-pf-img-wrap{padding-top:0 !important;height:100%}"
                     . ".{$uid}-grid .olo-pf-item img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}";
            case 'magazine':
                return ".{$uid}-grid{display:grid;grid-template-columns:2fr 1fr 1fr;grid-auto-rows:minmax(120px,auto);gap:{$gap}px}"
                     . ".{$uid}-grid .olo-pf-item:nth-child(1){grid-column:span 1;grid-row:span 2}"
                     . ".{$uid}-grid .olo-pf-item:nth-child(1) .olo-pf-img-wrap{padding-top:0 !important;height:100%}"
                     . ".{$uid}-grid .olo-pf-item:nth-child(1) img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}"
                     . "@media(max-width:768px){.{$uid}-grid{grid-template-columns:1fr}.{$uid}-grid .olo-pf-item:nth-child(1){grid-column:span 1;grid-row:auto}}";
            case 'masonry-pin':
                return ".{$uid}-grid{column-count:{$cols};column-gap:{$gap}px}"
                     . ".{$uid}-grid .olo-pf-item{break-inside:avoid;margin-bottom:{$gap}px;display:inline-block;width:100%}"
                     . ".{$uid}-grid .olo-pf-item .olo-pf-img-wrap{padding-top:0 !important}"
                     . ".{$uid}-grid .olo-pf-item img{position:relative !important;height:auto !important;width:100%;display:block}";
            case 'mosaic':
                return ".{$uid}-grid{display:grid;grid-template-columns:repeat({$cols},1fr);grid-auto-flow:dense;grid-auto-rows:200px;gap:{$gap}px}"
                     . ".{$uid}-grid .olo-pf-item:nth-child(5n+1){grid-row:span 2}"
                     . ".{$uid}-grid .olo-pf-item:nth-child(5n+3){grid-column:span 2}"
                     . ".{$uid}-grid .olo-pf-item .olo-pf-img-wrap{padding-top:0 !important;height:100%}"
                     . ".{$uid}-grid .olo-pf-item img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}";
            case 'split-index':
                return ".{$uid}-grid{display:grid;grid-template-columns:280px 1fr;gap:40px;min-height:480px}"
                     . ".{$uid}-grid .olo-pf-list{display:flex;flex-direction:column;gap:0}"
                     . ".{$uid}-grid .olo-pf-item{display:grid;grid-template-columns:1fr;padding:14px 0;border-bottom:1px solid rgba(0,0,0,0.08);cursor:pointer;transition:padding 200ms ease}"
                     . ".{$uid}-grid .olo-pf-item:hover{padding-left:8px}"
                     . ".{$uid}-grid .olo-pf-item .olo-pf-img-wrap{display:none}"
                     . ".{$uid}-grid .olo-pf-preview{position:sticky;top:24px;height:480px;border-radius:12px;overflow:hidden;background:#f8fafc}"
                     . ".{$uid}-grid .olo-pf-preview img{width:100%;height:100%;object-fit:cover;opacity:0;transition:opacity 350ms ease;position:absolute;inset:0}"
                     . ".{$uid}-grid .olo-pf-preview img.is-active{opacity:1}"
                     . "@media(max-width:768px){.{$uid}-grid{grid-template-columns:1fr}.{$uid}-grid .olo-pf-preview{display:none}.{$uid}-grid .olo-pf-item .olo-pf-img-wrap{display:block}}";
            case 'carousel':
                return ".{$uid}-grid{display:flex;gap:{$gap}px;overflow:hidden;width:100%;mask-image:linear-gradient(90deg,transparent,#000 5%,#000 95%,transparent)}"
                     . ".{$uid}-grid .olo-pf-track{display:flex;gap:{$gap}px;animation:olo-pf-tape-{$uid} var(--olo-pf-speed,30s) linear infinite;will-change:transform}"
                     . ".{$uid}-grid .olo-pf-item{flex-shrink:0;width:280px}"
                     . "@keyframes olo-pf-tape-{$uid}{from{transform:translateX(0)}to{transform:translateX(calc(-50% - {$gap}px / 2))}}";
            case 'polaroid':
                return ".{$uid}-grid{display:grid;grid-template-columns:repeat({$cols},1fr);gap:{$gap}px;padding:20px}"
                     . ".{$uid}-grid .olo-pf-item{background:#fff;padding:14px 14px 36px;box-shadow:0 6px 18px rgba(15,23,42,0.12);transition:transform 250ms cubic-bezier(0.68,-0.55,0.265,1.55)}"
                     . ".{$uid}-grid .olo-pf-item:nth-child(4n+1){transform:rotate(-1.5deg)}"
                     . ".{$uid}-grid .olo-pf-item:nth-child(4n+2){transform:rotate(0.8deg)}"
                     . ".{$uid}-grid .olo-pf-item:nth-child(4n+3){transform:rotate(-0.6deg)}"
                     . ".{$uid}-grid .olo-pf-item:nth-child(4n+4){transform:rotate(1.2deg)}"
                     . ".{$uid}-grid .olo-pf-item:hover{transform:rotate(0) scale(1.06);z-index:5;box-shadow:0 12px 28px rgba(15,23,42,0.18)}"
                     . ".{$uid}-grid .olo-pf-item .olo-pf-text{padding:10px 4px 0;font-family:'Caveat','Comic Sans MS',cursive;font-size:18px;text-align:center}"
                     . ".{$uid}-grid .olo-pf-item .olo-pf-cat{display:none}";
            case 'postcard-stack':
                return ".{$uid}-grid{display:grid;grid-template-columns:repeat({$cols},1fr);gap:{$gap}px;perspective:1200px}"
                     . ".{$uid}-grid .olo-pf-item{position:relative;transition:transform 350ms cubic-bezier(0.34,1.56,0.64,1);transform-style:preserve-3d}"
                     . ".{$uid}-grid .olo-pf-item::before,.{$uid}-grid .olo-pf-item::after{content:'';position:absolute;inset:0;border-radius:inherit;background:#fff;box-shadow:0 4px 12px rgba(15,23,42,0.08);z-index:-1;transition:transform 350ms ease}"
                     . ".{$uid}-grid .olo-pf-item::before{transform:translate(6px,6px) rotate(2deg)}"
                     . ".{$uid}-grid .olo-pf-item::after{transform:translate(12px,12px) rotate(-2deg)}"
                     . ".{$uid}-grid .olo-pf-item:hover{transform:translateY(-4px) rotateX(-3deg)}"
                     . ".{$uid}-grid .olo-pf-item:hover::before{transform:translate(10px,4px) rotate(3deg)}"
                     . ".{$uid}-grid .olo-pf-item:hover::after{transform:translate(20px,8px) rotate(-3deg)}";
            default:
                return '';
        }
    }

    /**
     * CSS per hover effects creativi (oltre i base zoom/fade/slide-up/overlay).
     */
    private function get_hover_effect_css( $fx, $uid, $accent, $caption_corner = 'bottom-left' ) {
        $sel_card = ".{$uid}-grid .olo-pf-item";
        switch ( $fx ) {
            case 'reveal-mask':
                return "{$sel_card} .olo-pf-img-wrap{position:relative}"
                     . "{$sel_card} .olo-pf-img-wrap::after{content:'';position:absolute;inset:0;background:rgba(0,0,0,0.6);clip-path:circle(0% at 50% 50%);transition:clip-path 600ms cubic-bezier(0.65,0,0.35,1);z-index:1}"
                     . "{$sel_card}:hover .olo-pf-img-wrap::after{clip-path:circle(120% at 50% 50%)}"
                     . "{$sel_card} .olo-pf-overlay{z-index:2;pointer-events:none;opacity:0;transition:opacity 400ms ease 150ms}"
                     . "{$sel_card}:hover .olo-pf-overlay{opacity:1}";
            case 'tilt-3d':
                return "{$sel_card}{transform-style:preserve-3d;perspective:1000px;transition:transform 200ms ease}"
                     . "{$sel_card}:hover{transform:rotateY(-6deg) rotateX(4deg) translateZ(20px)}"
                     . "{$sel_card} .olo-pf-overlay{transform:translateZ(40px);transition:opacity 300ms ease}"
                     . "{$sel_card} .olo-pf-text{transform:translateZ(30px)}"
                     . "{$sel_card}:hover .olo-pf-img-wrap img{transform:scale(1.04)}";
            case 'color-splash':
                return "{$sel_card} .olo-pf-img-wrap img{filter:grayscale(100%);transition:filter 600ms ease}"
                     . "{$sel_card}:hover .olo-pf-img-wrap img{filter:grayscale(0%)}";
            case 'glitch-rgb':
                return "@keyframes olo-pf-glitch-{$uid}{0%,100%{text-shadow:none;clip-path:inset(0 0 0 0)}20%{text-shadow:-2px 0 #ff006e,2px 0 #00f5d4;clip-path:inset(20% 0 60% 0)}40%{text-shadow:2px 0 #ff006e,-2px 0 #00f5d4;clip-path:inset(60% 0 20% 0)}60%{text-shadow:-2px 0 #ff006e,2px 0 #00f5d4;clip-path:inset(40% 0 40% 0)}80%{text-shadow:0 0 0;clip-path:inset(0 0 0 0)}}"
                     . "{$sel_card}:hover .olo-pf-img-wrap img{animation:none}"
                     . "{$sel_card}:hover .olo-pf-img-wrap{position:relative}"
                     . "{$sel_card}:hover .olo-pf-img-wrap::before,{$sel_card}:hover .olo-pf-img-wrap::after{content:'';position:absolute;inset:0;background-image:inherit;background-size:cover;background-position:center;mix-blend-mode:screen;animation:olo-pf-glitch-{$uid} 700ms steps(2) infinite}"
                     . "{$sel_card}:hover .olo-pf-title{animation:olo-pf-glitch-{$uid} 700ms steps(2) infinite}"
                     . "{$sel_card}:hover .olo-pf-img-wrap img{filter:contrast(1.2) saturate(1.4)}";
            case 'cinemagraph':
                return "@keyframes olo-pf-cine-{$uid}{0%,100%{transform:scale(1.04)}50%{transform:scale(1.07)}}"
                     . "{$sel_card}:hover .olo-pf-img-wrap img{animation:olo-pf-cine-{$uid} 4500ms ease-in-out infinite}"
                     . "{$sel_card}:hover .olo-pf-img-wrap::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at center,transparent 50%,rgba(0,0,0,0.4) 100%);pointer-events:none;z-index:1}";
            case 'image-cycle':
                return "{$sel_card}:hover .olo-pf-img-wrap img{animation:olo-pf-cycle-{$uid} 1500ms ease-in-out infinite}"
                     . "@keyframes olo-pf-cycle-{$uid}{0%,100%{filter:hue-rotate(0deg)}33%{filter:hue-rotate(40deg)}66%{filter:hue-rotate(-40deg)}}";
            case 'caption-corner':
                $pos = [
                    'bottom-left'  => 'left:0;bottom:0;transform:translateY(100%);transform-origin:left bottom',
                    'bottom-right' => 'right:0;bottom:0;transform:translateY(100%);transform-origin:right bottom',
                    'top-left'     => 'left:0;top:0;transform:translateY(-100%);transform-origin:left top',
                    'top-right'    => 'right:0;top:0;transform:translateY(-100%);transform-origin:right top',
                ];
                $css = isset( $pos[ $caption_corner ] ) ? $pos[ $caption_corner ] : $pos['bottom-left'];
                $hidden_to = strpos( $caption_corner, 'top' ) !== false ? '-100%' : '100%';
                return "{$sel_card} .olo-pf-overlay{position:absolute;{$css};width:auto;height:auto;max-width:80%;background:#fff;color:#0f172a;padding:14px 18px;display:block;text-align:left;transition:transform 300ms cubic-bezier(0.34,1.56,0.64,1);box-shadow:0 8px 20px rgba(15,23,42,0.18);border-radius:6px;margin:12px;opacity:1}"
                     . "{$sel_card}:hover .olo-pf-overlay{transform:translateY(0)}";
        }
        return '';
    }

    /**
     * Magic features CSS (ribbon, year stamp, index, badge, dim others, stagger).
     */
    private function get_magic_css( $s, $uid, $accent ) {
        $css = '';
        // Featured ribbon
        if ( ! empty( $s['featured_ribbon'] ) ) {
            $css .= ".{$uid}-grid .olo-pf-item.is-featured{position:relative}";
            $css .= ".{$uid}-grid .olo-pf-item.is-featured::after{content:attr(data-ribbon);position:absolute;top:14px;left:-32px;background:{$accent};color:#fff;padding:4px 32px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;transform:rotate(-45deg);box-shadow:0 4px 8px rgba(0,0,0,0.15);z-index:3;pointer-events:none}";
        }
        // Year stamp
        if ( ! empty( $s['year_stamp'] ) ) {
            $css .= ".{$uid}-grid .olo-pf-img-wrap .olo-pf-year{position:absolute;right:8px;bottom:0;font-size:7em;font-weight:900;color:rgba(255,255,255,0.18);line-height:1;pointer-events:none;font-family:Georgia,serif;z-index:2;letter-spacing:-0.04em}";
        }
        // Index numbering
        if ( ! empty( $s['index_numbering'] ) ) {
            $css .= ".{$uid}-grid .olo-pf-item .olo-pf-index{position:absolute;top:12px;left:16px;font-size:11px;font-weight:600;letter-spacing:2px;color:{$accent};font-family:ui-monospace,monospace;z-index:3;background:rgba(255,255,255,0.9);padding:3px 8px;border-radius:3px}";
        }
        // External link badge
        if ( ! empty( $s['external_link_badge'] ) ) {
            $css .= ".{$uid}-grid .olo-pf-item.is-external::before{content:'↗';position:absolute;top:10px;right:10px;width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,0.95);color:#0f172a;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;z-index:3;box-shadow:0 2px 6px rgba(15,23,42,0.15);transition:transform 200ms ease}";
            $css .= ".{$uid}-grid .olo-pf-item.is-external:hover::before{transform:rotate(45deg) scale(1.15)}";
        }
        // Dim others on hover
        if ( ! empty( $s['dim_others'] ) ) {
            $css .= ".{$uid}-grid:hover .olo-pf-item{transition:opacity 280ms ease,filter 280ms ease;opacity:0.4;filter:blur(1px)}";
            $css .= ".{$uid}-grid:hover .olo-pf-item:hover{opacity:1;filter:none;z-index:5}";
        }
        // Stagger entrance
        if ( ! empty( $s['stagger_entrance'] ) ) {
            $delay = max( 30, min( 300, absint( $s['stagger_delay'] ) ) );
            $css .= "@keyframes olo-pf-fadein-{$uid}{from{opacity:0;transform:translateY(28px)}to{opacity:1;transform:translateY(0)}}";
            $css .= ".{$uid}-grid .olo-pf-item{opacity:0;animation:olo-pf-fadein-{$uid} 600ms cubic-bezier(0.4,0,0.2,1) forwards}";
            for ( $i = 1; $i <= 12; $i++ ) {
                $d = $delay * $i;
                $css .= ".{$uid}-grid .olo-pf-item:nth-child({$i}){animation-delay:{$d}ms}";
            }
        }
        // Grayscale default
        if ( ! empty( $s['grayscale_default'] ) ) {
            $css .= ".{$uid}-grid .olo-pf-img-wrap img{filter:grayscale(80%);transition:filter 500ms ease}";
            $css .= ".{$uid}-grid .olo-pf-item:hover .olo-pf-img-wrap img{filter:grayscale(0%)}";
        }
        // Cursor label
        if ( ! empty( $s['cursor_label_enabled'] ) ) {
            $css .= ".{$uid}-cursor{position:fixed;pointer-events:none;background:#0f172a;color:#fff;padding:10px 18px;border-radius:999px;font-size:12px;font-weight:600;letter-spacing:0.5px;opacity:0;transform:translate(-50%,-50%) scale(0.6);transition:opacity 200ms ease,transform 200ms ease;z-index:9999;white-space:nowrap;will-change:transform}";
            $css .= ".{$uid}-cursor.is-active{opacity:1;transform:translate(-50%,-50%) scale(1)}";
        }
        return $css;
    }

    /**
     * Preset-specific extra CSS (tipografia, colori, decorazioni firmate).
     */
    private function get_preset_extra_css( $preset_id, $uid, $s ) {
        // @deprecated v1.0.73 — refactor profondo: i preset audaci ora settano direttamente
        // i field standard tramite TILE_PRESETS in BuilderInspector.vue, e i field wow_* via
        // build_wow_effects_css(). Nessun !important, ogni proprietà personalizzabile.
        return '';
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $uid       = 'olo-pf-' . wp_rand( 10000, 99999 );
        $preset_id = sanitize_key( $s['preset'] ?? 'custom' );
        $source    = in_array( $s['source'], [ 'manual', 'posts', 'custom_taxonomy' ], true ) ? $s['source'] : 'manual';
        $cols      = max( 1, min( 6, absint( $s['columns'] ) ) );
        $gap       = absint( $s['gap'] );
        $radius    = $this->build_border_radius_css( $s['border_radius'] ?? [] );
        if ( ! $radius ) $radius = is_numeric( $s['border_radius'] ) ? absint( $s['border_radius'] ) . 'px' : '8px';

        $allowed_layouts = [ 'grid','masonry','masonry-pin','bento','magazine','mosaic','split-index','carousel','polaroid','postcard-stack' ];
        $layout = in_array( $s['layout'], $allowed_layouts, true ) ? $s['layout'] : 'grid';

        $allowed_fx = [ 'none','zoom','fade','slide-up','overlay','reveal-mask','tilt-3d','color-splash','glitch-rgb','cinemagraph','image-cycle','caption-corner' ];
        $fx = in_array( $s['hover_effect'], $allowed_fx, true ) ? $s['hover_effect'] : 'fade';

        $caption_corner = in_array( $s['caption_corner'], [ 'bottom-left','bottom-right','top-left','top-right' ], true ) ? $s['caption_corner'] : 'bottom-left';
        $caption_pos    = in_array( $s['caption_position'], [ 'below','overlay','always','diagonal' ], true ) ? $s['caption_position'] : 'overlay';
        $anim     = in_array( $s['animation'], [ 'fade', 'scale', 'slide' ], true ) ? $s['animation'] : 'fade';
        $filter_style = in_array( $s['filter_style'], [ 'buttons', 'pills', 'underline', 'dropdown' ], true ) ? $s['filter_style'] : 'buttons';

        $ov_color = $this->safe_color_css( $s['overlay_color'] ) ?: '#000000';
        $ov_opa   = max( 0, min( 100, absint( $s['overlay_opacity'] ) ) );
        $title_c  = $this->safe_color_css( $s['title_color'] ) ?: 'var(--olo-color-text, #374151)';
        $text_c   = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text-soft, #6b7280)';
        $bg_c     = $this->safe_color_css( $s['bg_color'] );
        $accent_c = $this->safe_color_css( $s['accent_color'] ) ?: ( $this->safe_color_css( $s['effect_color'] ) ?: 'var(--olo-color-primary, #e1474f)' );
        $flt_c    = $this->safe_color_css( $s['filter_color'] ) ?: 'var(--olo-color-text-soft, #6b7280)';
        $flt_ac   = $this->safe_color_css( $s['filter_active_color'] ) ?: $accent_c;
        $all_label = esc_html( $s['filter_all_label'] ?: olobuild_t( 'Tutti' ) );

        $font_family = $this->font_family_css( $s['font_family'] ?? 'inherit' );
        $font_weight = in_array( $s['font_weight'], [ '300','400','500','600','700' ], true ) ? $s['font_weight'] : '500';
        $tt          = in_array( $s['text_transform'], [ 'none','uppercase','lowercase','capitalize' ], true ) ? $s['text_transform'] : 'none';
        $ls          = floatval( $s['letter_spacing'] ?? 0 );

        $cp = $s['container_padding'] ?? [];
        $cpt = is_array( $cp ) ? absint( $cp['top']    ?? 0 ) : 0;
        $cpr = is_array( $cp ) ? absint( $cp['right']  ?? 0 ) : 0;
        $cpb = is_array( $cp ) ? absint( $cp['bottom'] ?? 0 ) : 0;
        $cpl = is_array( $cp ) ? absint( $cp['left']   ?? 0 ) : 0;
        $container_radius_css = $this->build_border_radius_css( $s['container_radius'] ?? [] );

        $ratio_map = [ '1:1'=>'100%', '4:3'=>'75%', '16:9'=>'56.25%', '3:2'=>'66.67%', '3:4'=>'133.33%', 'auto'=>'0' ];
        $ratio = $s['image_ratio'];
        $ratio_css = isset( $ratio_map[ $ratio ] ) ? $ratio_map[ $ratio ] : '75%';

        // Punto focale (object-position) globale a livello tile, applicato a ogni immagine.
        $obj_pos = trim( (string) ( $s['object_position'] ?? 'center center' ) );
        if ( $obj_pos === '' ) { $obj_pos = 'center center'; }

        // Carousel speed
        $car_speed   = max( 10, min( 120, absint( $s['carousel_speed'] ) ) );
        $car_pause   = ! empty( $s['carousel_pause_on_hover'] );

        // ── Gather items ─────────────────────────────────────
        $items      = [];
        $categories = [];

        if ( $source === 'manual' ) {
            $raw_items = is_array( $s['items'] ) ? $s['items'] : [];
            foreach ( $raw_items as $item ) {
                if ( ! is_array( $item ) ) continue;
                $cat = trim( $item['category'] ?? '' );
                $items[] = [
                    'title'       => $item['title'] ?? '',
                    'image'       => $item['image_url'] ?? '',
                    'category'    => $cat,
                    'description' => $item['description'] ?? '',
                    'link'        => $item['link_url'] ?? '',
                    'year'        => $item['year'] ?? '',
                    'featured'    => ! empty( $item['featured'] ),
                ];
                if ( $cat !== '' ) {
                    $categories[ $cat ] = true;
                }
            }
        } else {
            $post_type = sanitize_key( $s['post_type'] ?: 'post' );
            $taxonomy  = sanitize_key( $s['taxonomy'] ?: 'category' );
            $per_page  = max( 1, min( 50, absint( $s['posts_per_page'] ) ) );

            $query = new WP_Query( [
                'post_type'      => $post_type,
                'posts_per_page' => $per_page,
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'DESC',
            ] );
            if ( $query->have_posts() ) {
                while ( $query->have_posts() ) {
                    $query->the_post();
                    $terms = get_the_terms( get_the_ID(), $taxonomy );
                    $cats  = [];
                    if ( is_array( $terms ) ) {
                        foreach ( $terms as $term ) {
                            $cats[] = $term->name;
                            $categories[ $term->name ] = true;
                        }
                    }
                    $items[] = [
                        'title'       => get_the_title(),
                        'image'       => get_the_post_thumbnail_url( get_the_ID(), 'medium_large' ) ?: '',
                        'category'    => implode( ',', $cats ),
                        'description' => get_the_excerpt(),
                        'link'        => get_permalink(),
                        'year'        => get_the_date( 'Y' ),
                        'featured'    => has_term( 'featured', 'post_tag' ),
                    ];
                }
                wp_reset_postdata();
            }
        }

        $categories = array_keys( $categories );

        if ( empty( $items ) ) {
            return '<div style="padding:40px;text-align:center;color:#9CA3AF;">' . esc_html( olobuild_t( 'Nessun elemento nel portfolio' ) ) . '</div>';
        }

        $total_items = count( $items );

        // Wrap container styles
        $wrap_styles = "font-family:{$font_family};font-weight:{$font_weight};text-transform:{$tt};";
        if ( $ls > 0 ) $wrap_styles .= "letter-spacing:{$ls}px;";
        if ( $bg_c ) $wrap_styles .= "background:{$bg_c};";
        $wrap_styles .= "padding:{$cpt}px {$cpr}px {$cpb}px {$cpl}px;";
        if ( $container_radius_css ) $wrap_styles .= "border-radius:{$container_radius_css};";
        $wrap_styles .= "color:{$text_c};";

        // Effect speed CSS variable
        $car_speed_css = "--olo-pf-speed:{$car_speed}s";

        // Site URL host (per external link badge)
        $site_host = wp_parse_url( home_url(), PHP_URL_HOST );

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist for every colour, absint()/floatval() clamps for numbers, in_array() whitelists for enums, fixed ratio maps, build_border_radius_css() and the internally generated $uid (get_layout_css/get_hover_effect_css/get_magic_css only interpolate those same sanitized values).
        ?>
        <style>
            /* ── Base layout ── */
            <?php $layout_css = $this->get_layout_css( $layout, $uid, $cols, $gap ); ?>
            <?php if ( $layout_css ) : ?>
            <?php echo $layout_css; ?>
            <?php elseif ( $layout === 'masonry' ) : ?>
            .<?php echo $uid; ?>-grid { column-count: <?php echo $cols; ?>; column-gap: <?php echo $gap; ?>px; }
            .<?php echo $uid; ?>-grid .olo-pf-item { break-inside: avoid; margin-bottom: <?php echo $gap; ?>px; }
            <?php else : ?>
            .<?php echo $uid; ?>-grid { display: grid; grid-template-columns: repeat(<?php echo $cols; ?>, 1fr); gap: <?php echo $gap; ?>px; }
            <?php endif; ?>

            /* ── Card base ── */
            .<?php echo $uid; ?>-grid .olo-pf-item {
                position: relative;
                border-radius: <?php echo $radius; ?>;
                overflow: hidden;
                transition: opacity 0.4s ease, transform 0.4s ease;
            }
            .<?php echo $uid; ?>-grid .olo-pf-item.olo-pf-hidden {
                opacity: 0;
                position: absolute;
                width: 0;
                height: 0;
                overflow: hidden;
                margin: 0;
                padding: 0;
                <?php if ( $anim === 'scale' ) : ?>transform: scale(0.8);<?php elseif ( $anim === 'slide' ) : ?>transform: translateY(20px);<?php endif; ?>
            }

            /* ── Image area ── */
            .<?php echo $uid; ?>-grid .olo-pf-img-wrap {
                position: relative;
                overflow: hidden;
                border-radius: inherit;
                <?php if ( $ratio !== 'auto' && ! in_array( $layout, [ 'masonry-pin','split-index','postcard-stack','polaroid' ], true ) ) : ?>
                padding-top: <?php echo $ratio_css; ?>;
                <?php endif; ?>
            }
            .<?php echo $uid; ?>-grid .olo-pf-img-wrap img {
                <?php if ( $ratio !== 'auto' && ! in_array( $layout, [ 'masonry-pin','split-index','postcard-stack','polaroid' ], true ) ) : ?>
                position: absolute;
                inset: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
                <?php else : ?>
                width: 100%;
                height: auto;
                <?php endif; ?>
                display: block;
                transition: transform 0.5s cubic-bezier(.25,.46,.45,.94);
            }

            /* ── Hover: zoom ── */
            <?php if ( $fx === 'zoom' ) : ?>
            .<?php echo $uid; ?>-grid .olo-pf-item:hover .olo-pf-img-wrap img { transform: scale(1.1); }
            <?php endif; ?>

            /* ── Overlay base ── */
            .<?php echo $uid; ?>-grid .olo-pf-overlay {
                position: absolute;
                inset: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
                padding: 16px;
                background: <?php echo $ov_color; ?>;
                color: #FFFFFF;
                pointer-events: none;
                transition: opacity 0.4s ease, transform 0.4s ease;
                <?php if ( in_array( $fx, [ 'fade', 'overlay', 'reveal-mask', 'tilt-3d' ], true ) ) : ?>
                opacity: 0;
                <?php elseif ( $fx === 'slide-up' ) : ?>
                opacity: 0;
                transform: translateY(100%);
                <?php elseif ( in_array( $fx, [ 'none', 'zoom', 'color-splash', 'glitch-rgb', 'cinemagraph', 'image-cycle' ], true ) ) : ?>
                display: none;
                <?php endif; ?>
            }
            .<?php echo $uid; ?>-grid .olo-pf-item:hover .olo-pf-overlay {
                <?php if ( in_array( $fx, [ 'fade', 'overlay' ], true ) ) : ?>
                opacity: <?php echo $ov_opa / 100; ?>;
                <?php elseif ( $fx === 'slide-up' ) : ?>
                opacity: <?php echo $ov_opa / 100; ?>;
                transform: translateY(0);
                <?php endif; ?>
            }

            /* ── Hover effect creativi ── */
            <?php $fx_css = $this->get_hover_effect_css( $fx, $uid, $accent_c, $caption_corner ); ?>
            <?php if ( $fx_css ) echo $fx_css; ?>

            /* ── Filter bar ── */
            .<?php echo $uid; ?>-filter {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
                margin-bottom: 16px;
                align-items: center;
            }
            .<?php echo $uid; ?>-filter button {
                font-size: 13px; font-weight: 500; cursor: pointer;
                transition: all 0.25s ease; border: none; outline: none;
                color: <?php echo $flt_c; ?>; background: transparent;
                <?php if ( $filter_style === 'buttons' ) : ?>padding:6px 16px;border-radius:4px;background:rgba(255,255,255,0.06);
                <?php elseif ( $filter_style === 'pills' ) : ?>padding:6px 18px;border-radius:50px;background:rgba(255,255,255,0.06);
                <?php elseif ( $filter_style === 'underline' ) : ?>padding:6px 10px;border-bottom:2px solid transparent;
                <?php elseif ( $filter_style === 'dropdown' ) : ?>padding:8px 16px;
                <?php endif; ?>
            }
            .<?php echo $uid; ?>-filter button:hover, .<?php echo $uid; ?>-filter button.active {
                color: var(--olo-color-on-primary, #FFFFFF);
                <?php if ( $filter_style === 'buttons' || $filter_style === 'pills' || $filter_style === 'dropdown' ) : ?>background: <?php echo $flt_ac; ?>;
                <?php elseif ( $filter_style === 'underline' ) : ?>color:<?php echo $flt_ac; ?>;border-bottom-color:<?php echo $flt_ac; ?>;
                <?php endif; ?>
            }
            .<?php echo $uid; ?>-filter button:focus-visible {
                outline: none;
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
            }
            .<?php echo $uid; ?>-grid .olo-pf-item:focus-visible {
                outline: none;
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
            }

            /* ── Search ── */
            .<?php echo $uid; ?>-search {
                width: 100%; padding: 10px 14px; border: 1px solid rgba(0,0,0,0.15);
                border-radius: 8px; font-size: 14px; box-sizing: border-box;
                margin-bottom: 12px; background: rgba(255,255,255,0.6);
            }
            .<?php echo $uid; ?>-search:focus { outline: none; border-color: <?php echo $accent_c; ?>; }

            /* ── Text below image ── */
            .<?php echo $uid; ?>-grid .olo-pf-text { padding: 12px 4px 4px; }
            .<?php echo $uid; ?>-grid .olo-pf-cat { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: <?php echo $accent_c; ?>; margin-bottom: 2px; }
            .<?php echo $uid; ?>-grid .olo-pf-title { font-size: 15px; font-weight: 600; color: <?php echo $title_c; ?>; line-height: 1.3; }
            .<?php echo $uid; ?>-grid .olo-pf-desc { font-size: 13px; color: <?php echo $text_c; ?>; margin-top: 4px; line-height: 1.5; }

            /* ── Caption position diagonal ── */
            <?php if ( $caption_pos === 'diagonal' ) : ?>
            .<?php echo $uid; ?>-grid .olo-pf-text { position: absolute; left: 0; bottom: 0; padding: 14px 18px; transform: rotate(-2deg); transform-origin: left bottom; background: #fff; box-shadow: 0 4px 16px rgba(0,0,0,0.12); margin: 12px; border-radius: 6px; z-index: 2; }
            <?php endif; ?>

            /* ── Magic features ── */
            <?php $magic_css = $this->get_magic_css( $s, $uid, $accent_c ); ?>
            <?php if ( $magic_css ) echo $magic_css; ?>

            /* ── Mobile ── */
            @media (max-width: 640px) {
                <?php if ( $layout === 'masonry' || $layout === 'masonry-pin' ) : ?>
                .<?php echo $uid; ?>-grid { column-count: 1; }
                <?php elseif ( in_array( $layout, [ 'grid','bento','mosaic','polaroid','postcard-stack' ], true ) ) : ?>
                .<?php echo $uid; ?>-grid { grid-template-columns: 1fr; }
                <?php endif; ?>
            }
            @media (min-width: 641px) and (max-width: 1024px) {
                <?php if ( $layout === 'masonry' || $layout === 'masonry-pin' ) : ?>
                .<?php echo $uid; ?>-grid { column-count: 2; }
                <?php elseif ( in_array( $layout, [ 'grid','bento','mosaic','polaroid','postcard-stack' ], true ) ) : ?>
                .<?php echo $uid; ?>-grid { grid-template-columns: repeat(2, 1fr); }
                <?php endif; ?>
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <?php // v1.0.73 — refactor profondo: get_preset_extra_css svuotato, ora i preset audaci
        // settano i field standard tramite TILE_PRESETS.portfolio + helper wow_*.
        $preset_css = $this->build_wow_effects_css( $s, '.' . $uid, '.olo-portfolio-item-title' ); ?>
        <?php if ( $preset_css ) echo '<style>' . $preset_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_wow_effects_css() from sanitized wow_* settings ?>

        <div class="olo-pf-wrap olo-pf-preset-<?php echo esc_attr( $preset_id ); ?> olo-pf-layout-<?php echo esc_attr( $layout ); ?> <?php echo esc_attr( $uid ); ?>" style="<?php echo esc_attr( $wrap_styles ); ?>">

            <?php if ( ! empty( $s['enable_search'] ) ) : ?>
                <input type="text" class="<?php echo esc_attr( $uid ); ?>-search" placeholder="<?php echo esc_attr( $s['search_placeholder'] ); ?>" />
            <?php endif; ?>

            <?php if ( ! empty( $s['filter_bar'] ) ) : ?>
            <div class="<?php echo esc_attr( $uid ); ?>-filter" id="<?php echo esc_attr( $uid ); ?>-filter">
                <button class="active" data-filter="*"><?php echo $all_label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped with esc_html() at assignment above ?></button>
                <?php foreach ( $categories as $cat ) : ?>
                <button data-filter="<?php echo esc_attr( sanitize_title( $cat ) ); ?>"><?php echo esc_html( $cat ); ?></button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if ( $layout === 'split-index' ) : ?>
                <div class="<?php echo esc_attr( $uid ); ?>-grid" id="<?php echo esc_attr( $uid ); ?>-grid">
                    <div class="olo-pf-list">
                        <?php foreach ( $items as $i => $item ) :
                            $cat_slugs = [];
                            $cat_names = array_map( 'trim', explode( ',', $item['category'] ) );
                            foreach ( $cat_names as $cn ) { if ( $cn !== '' ) $cat_slugs[] = sanitize_title( $cn ); }
                            $cat_data = implode( ',', $cat_slugs );
                            $is_ext = ! empty( $item['link'] ) && $site_host && ( wp_parse_url( $item['link'], PHP_URL_HOST ) !== $site_host );
                            $extra_class = $item['featured'] ? ' is-featured' : '';
                            if ( $is_ext && ! empty( $s['external_link_badge'] ) ) $extra_class .= ' is-external';
                        ?>
                            <a href="<?php echo esc_url( $item['link'] ?: '#' ); ?>" class="olo-pf-item<?php echo esc_attr( $extra_class ); ?>" data-categories="<?php echo esc_attr( $cat_data ); ?>" data-search="<?php echo esc_attr( strtolower( $item['title'] ) ); ?>" data-img="<?php echo esc_url( $item['image'] ); ?>" data-ribbon="<?php echo esc_attr( $s['featured_ribbon_text'] ); ?>" style="text-decoration:none;color:inherit">
                                <?php if ( ! empty( $s['index_numbering'] ) ) : ?><span class="olo-pf-index"><?php echo esc_html( str_pad( $i + 1, 2, '0', STR_PAD_LEFT ) ); ?>/<?php echo esc_html( str_pad( $total_items, 2, '0', STR_PAD_LEFT ) ); ?></span><?php endif; ?>
                                <?php if ( ! empty( $s['show_category'] ) && ! empty( $cat_names[0] ) ) : ?><div class="olo-pf-cat"><?php echo esc_html( $cat_names[0] ); ?></div><?php endif; ?>
                                <div class="olo-pf-title"><?php echo esc_html( $item['title'] ); ?></div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <div class="olo-pf-preview">
                        <?php foreach ( $items as $i => $item ) : ?>
                            <?php if ( ! empty( $item['image'] ) ) : ?>
                            <img src="<?php echo esc_url( $item['image'] ); ?>" alt="" data-i="<?php echo (int) $i; ?>" class="<?php echo $i === 0 ? 'is-active' : ''; ?>" style="object-position:<?php echo esc_attr( $obj_pos ); ?>;" />
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php elseif ( $layout === 'carousel' ) : ?>
                <div class="<?php echo esc_attr( $uid ); ?>-grid" id="<?php echo esc_attr( $uid ); ?>-grid" style="<?php echo esc_attr( $car_speed_css ); ?>">
                    <div class="olo-pf-track">
                        <?php for ( $rep = 0; $rep < 2; $rep++ ) : ?>
                            <?php foreach ( $items as $i => $item ) :
                                $this->render_item( $item, $i, $total_items, $s, $fx, $caption_pos, $site_host );
                            endforeach; ?>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php else : ?>
                <div class="<?php echo esc_attr( $uid ); ?>-grid" id="<?php echo esc_attr( $uid ); ?>-grid">
                    <?php foreach ( $items as $i => $item ) :
                        $this->render_item( $item, $i, $total_items, $s, $fx, $caption_pos, $site_host );
                    endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $s['cursor_label_enabled'] ) ) : ?>
                <div class="<?php echo esc_attr( $uid ); ?>-cursor"><?php echo esc_html( $s['cursor_label_text'] ); ?> →</div>
            <?php endif; ?>
        </div>

        <?php // ── JS interactions ── ?>
        <script>
        (function(){
            var root = document.querySelector('.<?php echo esc_js( $uid ); ?>');
            if(!root){return}
            var grid = document.getElementById('<?php echo esc_js( $uid ); ?>-grid');

            <?php if ( ! empty( $s['filter_bar'] ) ) : ?>
            var filterBar = document.getElementById('<?php echo esc_js( $uid ); ?>-filter');
            if(filterBar){
                var buttons = filterBar.querySelectorAll('button');
                buttons.forEach(function(btn){
                    btn.addEventListener('click', function(){
                        buttons.forEach(function(b){ b.classList.remove('active'); });
                        btn.classList.add('active');
                        var f = btn.getAttribute('data-filter');
                        var items = grid.querySelectorAll('.olo-pf-item');
                        items.forEach(function(item){
                            var cats = (item.getAttribute('data-categories') || '').split(',');
                            if(f === '*'){ item.classList.remove('olo-pf-hidden'); return; }
                            var match = false;
                            for(var i=0;i<cats.length;i++){ if(cats[i]===f){ match=true; } }
                            if(match){ item.classList.remove('olo-pf-hidden'); }
                            else { item.classList.add('olo-pf-hidden'); }
                        });
                    });
                });
            }
            <?php endif; ?>

            <?php if ( ! empty( $s['enable_search'] ) ) : ?>
            var search = root.querySelector('.<?php echo esc_js( $uid ); ?>-search');
            if(search){
                search.addEventListener('input', function(){
                    var q = (search.value || '').toLowerCase().trim();
                    var items = grid.querySelectorAll('.olo-pf-item');
                    items.forEach(function(it){
                        var s = it.getAttribute('data-search') || '';
                        if(q === ''){ it.classList.remove('olo-pf-hidden'); }
                        else if(s.indexOf(q) === -1){ it.classList.add('olo-pf-hidden'); }
                        else { it.classList.remove('olo-pf-hidden'); }
                    });
                });
            }
            <?php endif; ?>

            <?php if ( $layout === 'split-index' ) : ?>
            var listItems = grid.querySelectorAll('.olo-pf-list .olo-pf-item');
            var previews = grid.querySelectorAll('.olo-pf-preview img');
            listItems.forEach(function(it, idx){
                it.addEventListener('mouseenter', function(){
                    previews.forEach(function(p){ p.classList.remove('is-active'); });
                    if(previews[idx]){ previews[idx].classList.add('is-active'); }
                });
            });
            <?php endif; ?>

            <?php if ( $layout === 'carousel' && $car_pause ) : ?>
            var track = root.querySelector('.olo-pf-track');
            if(track){
                root.addEventListener('mouseenter', function(){ track.style.animationPlayState = 'paused'; });
                root.addEventListener('mouseleave', function(){ track.style.animationPlayState = 'running'; });
            }
            <?php endif; ?>

            <?php if ( ! empty( $s['cursor_label_enabled'] ) ) : ?>
            var cursor = root.querySelector('.<?php echo esc_js( $uid ); ?>-cursor');
            if(cursor){
                var raf = null, mx = 0, my = 0;
                function move(){ cursor.style.left = mx + 'px'; cursor.style.top = my + 'px'; raf = null; }
                grid.addEventListener('mousemove', function(e){
                    mx = e.clientX; my = e.clientY;
                    if(!raf){ raf = requestAnimationFrame(move); }
                });
                var hot = grid.querySelectorAll('.olo-pf-item');
                hot.forEach(function(el){
                    el.addEventListener('mouseenter', function(){ cursor.classList.add('is-active'); });
                    el.addEventListener('mouseleave', function(){ cursor.classList.remove('is-active'); });
                });
            }
            <?php endif; ?>
        })();
        </script>

        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Text_Effects::css() from fixed effect definitions
        $this->tfx_print_script();

        // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS built by Olobuild_Tile_Base::build_border_css() from sanitized border settings
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS built by Olobuild_Tile_Base border helpers from sanitized border settings
        }
        return ob_get_clean();
    }

    private function render_item( $item, $i, $total_items, $s, $fx, $caption_pos, $site_host ) {
        $cat_slugs = [];
        $cat_names = array_map( 'trim', explode( ',', $item['category'] ) );
        foreach ( $cat_names as $cn ) { if ( $cn !== '' ) $cat_slugs[] = sanitize_title( $cn ); }
        $cat_data = implode( ',', $cat_slugs );
        $has_link = ! empty( $item['link'] );
        $is_ext = $has_link && $site_host && ( wp_parse_url( $item['link'], PHP_URL_HOST ) !== $site_host );

        $extra_class = '';
        if ( ! empty( $item['featured'] ) ) $extra_class .= ' is-featured';
        if ( $is_ext && ! empty( $s['external_link_badge'] ) ) $extra_class .= ' is-external';

        $tag_open = $has_link
            ? '<a href="' . esc_url( $item['link'] ) . '" class="olo-pf-item' . $extra_class . '" data-categories="' . esc_attr( $cat_data ) . '" data-search="' . esc_attr( strtolower( $item['title'] ) ) . '" data-ribbon="' . esc_attr( $s['featured_ribbon_text'] ) . '" style="display:block;text-decoration:none;color:inherit;"' . ( $is_ext ? ' target="_blank" rel="noopener"' : '' ) . '>'
            : '<div class="olo-pf-item' . $extra_class . '" data-categories="' . esc_attr( $cat_data ) . '" data-search="' . esc_attr( strtolower( $item['title'] ) ) . '" data-ribbon="' . esc_attr( $s['featured_ribbon_text'] ) . '">';
        $tag_close = $has_link ? '</a>' : '</div>';

        echo $tag_open; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup built above with esc_url()/esc_attr(); $extra_class holds fixed class literals only

        // Index numbering
        if ( ! empty( $s['index_numbering'] ) ) {
            echo '<span class="olo-pf-index">' . esc_html( str_pad( $i + 1, 2, '0', STR_PAD_LEFT ) ) . '/' . esc_html( str_pad( $total_items, 2, '0', STR_PAD_LEFT ) ) . '</span>';
        }

        // Punto focale globale (object-position), applicato a ogni immagine.
        $obj_pos = trim( (string) ( $s['object_position'] ?? 'center center' ) );
        if ( $obj_pos === '' ) { $obj_pos = 'center center'; }

        echo '<div class="olo-pf-img-wrap">';
        if ( ! empty( $item['image'] ) ) {
            echo '<img src="' . esc_url( $item['image'] ) . '" alt="' . esc_attr( $item['title'] ) . '" loading="lazy" style="object-position:' . esc_attr( $obj_pos ) . ';" />';
        } else {
            echo '<div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:#1F2937;min-height:120px;"><span style="font-size:32px;opacity:0.3;">&#x1F5BC;</span></div>';
        }

        // Year stamp
        if ( ! empty( $s['year_stamp'] ) && ! empty( $item['year'] ) ) {
            echo '<span class="olo-pf-year">' . esc_html( $item['year'] ) . '</span>';
        }

        // Overlay
        if ( $fx !== 'none' && $fx !== 'color-splash' && $fx !== 'glitch-rgb' && $fx !== 'cinemagraph' && $fx !== 'image-cycle' ) {
            echo '<div class="olo-pf-overlay">';
            if ( ! empty( $s['show_title'] ) ) {
                echo '<span style="font-weight:600;font-size:14px;">' . esc_html( $item['title'] ) . '</span>';
            }
            echo '</div>';
        }

        echo '</div>'; // close img-wrap

        // Text below image
        if ( $caption_pos !== 'overlay' || $fx === 'color-splash' || $fx === 'glitch-rgb' || $fx === 'cinemagraph' || $fx === 'image-cycle' ) {
            $show_text = ! empty( $s['show_title'] ) || ! empty( $s['show_category'] ) || ! empty( $s['show_excerpt'] );
            if ( $show_text ) {
                echo '<div class="olo-pf-text">';
                if ( ! empty( $s['show_category'] ) && ! empty( $cat_names[0] ) ) {
                    echo '<div class="olo-pf-cat">' . esc_html( $cat_names[0] ) . '</div>';
                }
                if ( ! empty( $s['show_title'] ) ) {
                    list( $pft_cls, $pft_data ) = $this->tfx_attrs( $s, 'title', $item['title'] );
                    echo '<div class="olo-pf-title' . $pft_cls . '"' . $pft_data . '>' . esc_html( $item['title'] ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- class/data attrs generated and escaped by Olobuild_Text_Effects helpers; title escaped with esc_html()
                }
                if ( ! empty( $s['show_excerpt'] ) && ! empty( $item['description'] ) ) {
                    list( $pfd_cls, $pfd_data ) = $this->tfx_attrs( $s, 'description', wp_strip_all_tags( $item['description'] ) );
                    echo '<div class="olo-pf-desc' . $pfd_cls . '"' . $pfd_data . '>' . wp_kses_post( $item['description'] ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attrs escaped by Olobuild_Text_Effects helpers; content sanitized via wp_kses_post()
                }
                echo '</div>';
            }
        }

        echo $tag_close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed literal '</a>' or '</div>' from ternary above
    }
}
