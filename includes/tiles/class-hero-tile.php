<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Hero_Tile extends Olobuild_Tile_Base {

    protected $type     = 'hero';
    protected $name     = 'Hero';
    protected $icon     = 'dashicons-cover-image';
    protected $category = 'layout';
    protected $defaults = [
        // Sfondo: gestito dal wrapper esterno via tab Stile → Sfondo (style.bg).
        // Bordo, ombra, larghezza piena: gestiti anch'essi dal tab Stile.
        // La hero gestisce internamente solo testo, layout interno, CTA.

        'typography_preset' => '',
        'preset'            => 'custom',

        // Contenuto
        'title'      => 'Benvenuto nel nostro sito',
        'subtitle'   => 'Scopri qualcosa di straordinario',
        'text_color' => '',

        // Scena (unificazione hero, Fase 1) — tutti default no-op, parità col JS.
        'eyebrow_text'    => '',
        'eyebrow_dot'     => false,
        'media_bg'        => [ 'type' => 'none' ],
        'overlay_color'   => '',
        'overlay_top'     => 0,
        'overlay_bottom'  => 0,
        'overlay_sides'   => false,
        'glow_on'         => false,
        'glow_color'      => '',
        'glow_w'          => 760,
        'glow_h'          => 560,
        'glow_blur'       => 100,
        'glow_x'          => 50,
        'glow_y'          => 20,
        'arch'            => false,
        'frame_on'        => false,
        'frame_inset'     => 24,
        'watermark_text'  => '',
        'watermark_color' => '',
        'accent'          => '',
        'meta_text'       => '',
        'scroll_hint'     => '',

        // Modulo sotto il contenuto (unificazione hero, Fase 1) — '' = no-op.
        'module'             => '',
        'strip_items'        => [
            [ 'image' => '', 'caption' => 'dettaglio — 01' ],
            [ 'image' => '', 'caption' => 'dettaglio — 02' ],
            [ 'image' => '', 'caption' => 'dettaglio — 03' ],
        ],
        'strip_offset'       => 28,
        'strip_radius'       => 200,
        'search_placeholder' => 'Cerca…',
        'search_button'      => 'Cerca',
        'search_url'         => '',
        'search_chips'       => '',
        'mock_mode'          => 'media',
        'mock_media'         => [ 'type' => 'none' ],
        'mock_label'         => 'screenshot prodotto — 16/8.5',
        'mock_url'           => 'app.tuoprodotto.com',
        'mock_kpis'          => [
            [ 'label' => 'MRR', 'value' => '€48.2k', 'delta' => '+12% sul mese', 'down' => false ],
            [ 'label' => 'Utenti attivi', 'value' => '3.204', 'delta' => '+8%', 'down' => false ],
            [ 'label' => 'Churn', 'value' => '1,9%', 'delta' => '−0,4%', 'down' => true ],
        ],
        'mock_chart_title'   => 'Revenue',
        'mock_chart_meta'    => 'ultimi 12 mesi',
        'mock_bars'          => [
            [ 'h' => 34, 'label' => 'G', 'alt' => false ], [ 'h' => 52, 'label' => 'F', 'alt' => false ],
            [ 'h' => 44, 'label' => 'M', 'alt' => true ],  [ 'h' => 68, 'label' => 'A', 'alt' => false ],
            [ 'h' => 58, 'label' => 'M', 'alt' => false ], [ 'h' => 80, 'label' => 'G', 'alt' => true ],
            [ 'h' => 72, 'label' => 'L', 'alt' => false ], [ 'h' => 92, 'label' => 'A', 'alt' => false ],
        ],
        'chat_label'         => 'workspace',
        'chat_messages'      => [
            [ 'side' => 'you', 'text' => 'Riassumi le chiamate della settimana e segnala i temi sul prezzo.' ],
            [ 'side' => 'ai', 'text' => 'Su 9 chiamate: 3 citano il prezzo — due chiedono la fatturazione annuale. Ho preparato una bozza di follow-up per ciascuna.' ],
            [ 'side' => 'you', 'text' => 'Perfetto, aggiungile al CRM.' ],
        ],

        // Titolo tipografia
        'title_tag'            => 'h1',
        'title_font_family'    => '',
        'title_font_size'      => '',
        'title_font_weight'    => '700',
        'title_letter_spacing' => '0',
        'title_line_height'    => '1.2',
        'title_text_transform' => 'none',
        'title_color'          => '',
        'title_text_shadow'    => '',

        // Sottotitolo tipografia
        'subtitle_font_size'      => '',
        'subtitle_font_weight'    => '400',
        'subtitle_letter_spacing' => '0',
        'subtitle_color'          => '',
        'subtitle_max_width'      => '',

        // Layout
        'min_height'        => '500px',
        'content_max_width' => '700',
        'vertical_align'    => 'center',
        'horizontal_align'  => 'center',
        'text_align'        => 'center',
        'tile_padding'      => [ 'top' => 60, 'right' => 20, 'bottom' => 60, 'left' => 20 ],

        // CTA Primario
        'cta_text'       => 'Inizia ora',
        'cta_url'        => '#',
        'cta_target'     => '_self',
        'cta_bg_color'   => '',
        'cta_text_color' => '',
        'cta_radius'     => [ 'tl' => 6, 'tr' => 6, 'br' => 6, 'bl' => 6 ],
        'cta_size'       => '15',
        'cta_style'      => 'filled',

        // CTA Secondario
        'cta2_text'       => '',
        'cta2_url'        => '#',
        'cta2_target'     => '_self',
        'cta2_bg_color'   => '',
        'cta2_text_color' => '',
        'cta2_style'      => 'outline',
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-hero-' . wp_rand( 10000, 99999 );

        $fg = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-on-primary, #FFFFFF)';

        // Sanitize rich text — permetti inline tag sicuri (in particolare <br> per a-capo).
        // L'utente può scrivere "Riga 1<br>Riga 2" nel titolo/sottotitolo dell'inspector.
        $allowed_inline = [
            'br'     => [],
            'strong' => [],
            'em'     => [],
            'b'      => [],
            'i'      => [],
            'u'      => [],
            'span'   => [
                'class' => true,
                'style' => true,
                // Consenti il pilotaggio inline del Text-FX su una singola parola del
                // titolo (es. ultima riga "scramble" che cicla data-fx-phrases) senza
                // applicare l'effetto all'intero titolo. Attributi letti da class-text-effects.js.
                'data-olo-text-fx'    => true,
                'data-fx-phrases'     => true,
                'data-fx-loop'        => true,
                'data-fx-speed'       => true,
                'data-fx-delay'       => true,
                'data-fx-pause'       => true,
            ],
            'sup'    => [],
            'sub'    => [],
            'mark'   => [],
            'small'  => [],
        ];
        $title    = wp_kses( (string) $s['title'],    $allowed_inline );
        $subtitle = wp_kses( (string) $s['subtitle'], $allowed_inline );
        list( $h_tfx_cls, $h_tfx_data ) = $this->tfx_attrs( $s, 'title', $title );
        list( $s_tfx_cls, $s_tfx_data ) = $this->tfx_attrs( $s, 'subtitle', $subtitle );

        // Layout values
        $min_height    = esc_attr( $s['min_height'] ?: '500px' );
        $max_w         = intval( $s['content_max_width'] ) ?: 700;
        $v_align       = $this->map_align( $s['vertical_align'], 'v' );
        $h_align       = $this->map_align( $s['horizontal_align'], 'h' );
        $text_align    = in_array( $s['text_align'], [ 'left', 'center', 'right' ], true ) ? $s['text_align'] : 'center';

        $_tp = $s['tile_padding'] ?? null;
        if ( is_array( $_tp ) ) {
            $pad_t = intval( $_tp['top']    ?? 60 );
            $pad_r = intval( $_tp['right']  ?? 20 );
            $pad_b = intval( $_tp['bottom'] ?? 60 );
            $pad_l = intval( $_tp['left']   ?? 20 );
        } else {
            $pad_t = $pad_b = 60;
            $pad_r = $pad_l = 20;
        }

        // ── Scena (unificazione hero, Fase 1) — media + glow + velo + watermark ──
        // Tutti i valori sono no-op coi default: $has_scene false → CSS/markup identici a prima.
        $accent_set  = $this->safe_color_css( $s['accent'] ?? '' );
        $accent_css  = $accent_set ?: 'var(--olo-color-primary, #e1474f)';
        $mb          = $this->bg_media_parts( $s['media_bg'] ?? null, $uid );
        $o_top       = max( 0, min( 1, floatval( $s['overlay_top'] ?? 0 ) ) );
        $o_bot       = max( 0, min( 1, floatval( $s['overlay_bottom'] ?? 0 ) ) );
        $has_overlay = ( $o_top > 0 || $o_bot > 0 );
        $glow_on     = ! empty( $s['glow_on'] );
        $wm_text     = trim( (string) ( $s['watermark_text'] ?? '' ) );
        $has_scene   = ( $mb['has'] || $glow_on || $has_overlay || $wm_text !== '' );

        // color-mix: velo/glow su QUALSIASI colore, token inclusi (parità col JS).
        $mix = static function ( $color, $alpha ) {
            return 'color-mix(in srgb, ' . $color . ' ' . round( max( 0, min( 1, $alpha ) ) * 100 ) . '%, transparent)';
        };

        $veil_grad = '';
        if ( $has_overlay ) {
            $oc     = $this->safe_color_css( $s['overlay_color'] ?? '' ) ?: 'var(--olo-color-dark, #16263d)';
            $o_mid  = round( $o_top * 0.6, 3 );
            $grad_v = 'linear-gradient(180deg, ' . $mix( $oc, $o_top ) . ' 0%, ' . $mix( $oc, $o_mid ) . ' 38%, ' . $mix( $oc, $o_bot ) . ' 100%)';
            if ( ! empty( $s['overlay_sides'] ) ) {
                $s_top     = round( $o_top * 0.4, 3 );
                $s_bot     = round( $o_bot * 0.7, 3 );
                $veil_grad = 'linear-gradient(90deg, ' . $mix( $oc, $o_bot ) . ' 0%, ' . $mix( $oc, $s_top ) . ' 52%, ' . $mix( $oc, $s_bot ) . ' 100%), ' . $grad_v;
            } else {
                $veil_grad = $grad_v;
            }
        }

        $glow_color = $this->safe_color_css( $s['glow_color'] ?? '' ) ?: $mix( $accent_css, 0.2 );
        $glow_w     = max( 100, intval( $s['glow_w'] ?? 760 ) ?: 760 );
        $glow_h     = max( 100, intval( $s['glow_h'] ?? 560 ) ?: 560 );
        $glow_blur  = max( 0, intval( $s['glow_blur'] ?? 100 ) );
        $glow_x     = is_numeric( $s['glow_x'] ?? null ) ? max( 0, min( 100, intval( $s['glow_x'] ) ) ) : 50;
        $glow_y     = is_numeric( $s['glow_y'] ?? null ) ? max( -50, min( 100, intval( $s['glow_y'] ) ) ) : 20;

        $frame_on    = ! empty( $s['frame_on'] );
        $frame_inset = max( 0, absint( $s['frame_inset'] ?? 24 ) );
        $scene_inset = $frame_on ? ( $frame_inset . 'px' ) : '0';
        $arch_mask   = ! empty( $s['arch'] ) ? 'radial-gradient(150% 125% at 50% 0%, #000 87%, transparent 87.5%)' : '';
        $wm_color    = $this->safe_color_css( $s['watermark_color'] ?? '' ) ?: 'rgba(255,255,255,.06)';

        // ── Modulo sotto il contenuto (unificazione hero, Fase 1) ──
        $module      = in_array( ( $s['module'] ?? '' ), [ 'strip', 'search', 'mockup', 'chat' ], true ) ? $s['module'] : '';
        $strip_items = ( 'strip' === $module && is_array( $s['strip_items'] ?? null ) ) ? $s['strip_items'] : [];
        $strip_off   = max( 0, intval( $s['strip_offset'] ?? 28 ) );
        $strip_rad   = max( 0, intval( $s['strip_radius'] ?? 200 ) );
        $chips       = ( 'search' === $module )
            ? array_filter( array_map( 'trim', explode( ',', (string) ( $s['search_chips'] ?? '' ) ) ), 'strlen' )
            : [];
        $search_url  = trim( (string) ( $s['search_url'] ?? '' ) );
        if ( 'search' === $module && '' === $search_url ) {
            $search_url = home_url( '/' );
        }
        // mockup + chat: pannelli in color-mix(currentColor) → si adattano a scene chiare/scure.
        $mixc       = static function ( $pct ) {
            return 'color-mix(in srgb, currentColor ' . intval( $pct ) . '%, transparent)';
        };
        $mock_mode  = ( ( $s['mock_mode'] ?? 'media' ) === 'dashboard' ) ? 'dashboard' : 'media';
        $mock_media = ( 'mockup' === $module ) ? $this->bg_media_parts( $s['mock_media'] ?? null, $uid . '-mock' ) : [ 'has' => false, 'css' => '', 'markup' => '' ];
        $mock_kpis  = ( 'mockup' === $module && is_array( $s['mock_kpis'] ?? null ) ) ? array_values( $s['mock_kpis'] ) : [];
        $mock_bars  = ( 'mockup' === $module && is_array( $s['mock_bars'] ?? null ) ) ? array_values( $s['mock_bars'] ) : [];
        $chat_msgs  = ( 'chat' === $module && is_array( $s['chat_messages'] ?? null ) ) ? $s['chat_messages'] : [];
        $has_module = ( ( 'strip' === $module && ! empty( $strip_items ) ) || in_array( $module, [ 'search', 'mockup', 'chat' ], true ) );

        // CTA sizing (range value = font-size in px, padding proportional)
        $cta_fs       = intval( $s['cta_size'] ) ?: 15;
        $cta_pad_y    = round( $cta_fs * 0.8 );
        $cta_pad_x    = round( $cta_fs * 2.1 );
        $cta_size_css = "padding:{$cta_pad_y}px {$cta_pad_x}px;font-size:{$cta_fs}px;";

        // CTA radius — sistema border-radius unificato (oggetto {tl,tr,br,bl})
        $cta_radius_val       = $this->build_border_radius_css( $s['cta_radius'] ?? null );
        $cta_radius_css       = $cta_radius_val ? "border-radius:{$cta_radius_val};" : '';
        $cta_radius_hover_css = Olobuild_Tile_Utils::radius_force_css( $s['cta_radius_hover'] ?? null );

        // CTA Primary colors — outline/ghost fallback to hero text color (visible)
        $cta_bg          = $this->safe_color_css( $s['cta_bg_color'] ) ?: 'var(--olo-color-on-primary, #FFFFFF)';
        $cta_fg_explicit = $this->safe_color_css( $s['cta_text_color'] );
        if ( $cta_fg_explicit ) {
            $cta_fg = $cta_fg_explicit;
        } elseif ( $s['cta_style'] === 'filled' ) {
            $cta_fg = 'var(--olo-color-primary, #e1474f)';
        } else {
            $cta_fg = $fg;
        }

        // CTA Secondary colors
        $cta2_bg = $this->safe_color_css( $s['cta2_bg_color'] ) ?: 'transparent';
        $cta2_fg = $this->safe_color_css( $s['cta2_text_color'] ) ?: 'var(--olo-color-on-primary, #FFFFFF)';

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: colors via the safe_color_css() whitelist (with token fallbacks), min-height esc_attr()'d at assignment, integers via intval()/round(), alignments from fixed maps/in_array() whitelists, CTA declarations via build_cta_css()/build_border_radius_css()/Olobuild_Tile_Utils radius helpers; scene values via bg_media_parts() (Olobuild_CSS_Builder), the $mix() closure (safe_color_css colours + clamped floats), intval()/absint()/is_numeric() clamps and fixed mask/inset literals; $uid is internally generated. ?>
        <style>
            .<?php echo $uid; ?> {
                position: relative;
                min-height: <?php echo $min_height; ?>;
                display: flex;
                color: <?php echo $fg; ?>;
                <?php if ( $has_scene ) : ?>overflow: hidden;<?php endif; ?>
                <?php if ( $has_module ) : ?>flex-direction: column;<?php endif; ?>
            }
            <?php if ( $has_module ) : ?>
            .<?php echo $uid; ?> .olo-hero-modwrap{position:relative;z-index:1;width:100%;padding:0 30px clamp(40px,6vh,64px);}
            <?php endif; ?>
            <?php if ( 'strip' === $module && ! empty( $strip_items ) ) : ?>
            .<?php echo $uid; ?> .olo-hero-strip{display:flex;gap:14px;justify-content:center;flex-wrap:wrap;max-width:1180px;margin:0 auto;}
            .<?php echo $uid; ?> .olo-hero-stripmedia{position:relative;overflow:hidden;width:clamp(150px,22vw,240px);aspect-ratio:3/4;border-radius:<?php echo (int) $strip_rad; ?>px <?php echo (int) $strip_rad; ?>px 8px 8px;background-color:rgba(255,255,255,.06);background-image:repeating-linear-gradient(135deg, rgba(243,233,239,.05) 0 16px, transparent 16px 32px);background-size:cover;background-position:center center;}
            <?php if ( $strip_off > 0 ) : ?>.<?php echo $uid; ?> .olo-hero-stripmedia:nth-child(2){margin-top:-<?php echo (int) $strip_off; ?>px;}<?php endif; ?>
            .<?php echo $uid; ?> .olo-hero-stripcap{position:absolute;left:14px;bottom:12px;right:14px;font-size:10.5px;letter-spacing:.1em;text-transform:uppercase;color:rgba(243,233,239,.4);}
            @media(max-width:600px){.<?php echo $uid; ?> .olo-hero-strip .olo-hero-stripmedia:nth-child(3){display:none;}}
            <?php endif; ?>
            <?php if ( 'search' === $module ) :
                $sb_margin   = ( $text_align === 'center' ) ? '0 auto' : ( ( $text_align === 'right' ) ? '0 0 0 auto' : '0' );
                $chip_margin = ( $text_align === 'center' ) ? '18px auto 0' : '18px 0 0';
                $chip_just   = ( $text_align === 'center' ) ? 'center' : ( ( $text_align === 'right' ) ? 'flex-end' : 'flex-start' );
            ?>
            .<?php echo $uid; ?> .olo-hero-searchbox{display:flex;gap:8px;align-items:center;max-width:560px;margin:<?php echo $sb_margin; ?>;background:rgba(255,255,255,.07);border:1px solid <?php echo $mix( $accent_css, 0.4 ); ?>;border-radius:14px;padding:8px;}
            .<?php echo $uid; ?> .olo-hero-searchbox input{flex:1;background:transparent;border:0;padding:12px 14px;font-size:15px;color:<?php echo $fg; ?>;min-width:0;font-family:inherit;}
            .<?php echo $uid; ?> .olo-hero-searchbox input::placeholder{color:<?php echo $fg; ?>;opacity:.55;}
            .<?php echo $uid; ?> .olo-hero-searchbox input:focus{outline:none;}
            .<?php echo $uid; ?> .olo-hero-searchbox input:focus-visible{outline:2px solid <?php echo $accent_css; ?>;outline-offset:2px;border-radius:8px;}
            .<?php echo $uid; ?> .olo-hero-searchbtn{cursor:pointer;white-space:nowrap;font-family:inherit;}
            .<?php echo $uid; ?> .olo-hero-searchbtn:focus-visible{outline:2px solid <?php echo $accent_css; ?>;outline-offset:2px;}
            .<?php echo $uid; ?> .olo-hero-chips{display:flex;gap:8px;flex-wrap:wrap;max-width:560px;margin:<?php echo $chip_margin; ?>;justify-content:<?php echo $chip_just; ?>;}
            .<?php echo $uid; ?> .olo-hero-chip{font-size:13px;font-weight:600;opacity:.85;border:1px solid rgba(255,255,255,.16);border-radius:999px;padding:7px 15px;text-decoration:none;color:inherit;transition:border-color .15s,opacity .15s;}
            .<?php echo $uid; ?> .olo-hero-chip:hover{border-color:<?php echo $accent_css; ?>;opacity:1;}
            .<?php echo $uid; ?> .olo-hero-chip:focus-visible{outline:2px solid <?php echo $accent_css; ?>;outline-offset:2px;}
            <?php endif; ?>
            <?php
            $mono_ff = "var(--olo-font-family-mono, ui-monospace,'SF Mono',Menlo,monospace)";
            if ( 'mockup' === $module || 'chat' === $module ) : ?>
            .<?php echo $uid; ?> .olo-hero-winbar{display:flex;align-items:center;gap:7px;padding:13px 16px;border-bottom:1px solid <?php echo $mixc( 10 ); ?>;background:<?php echo $mixc( 7 ); ?>;}
            .<?php echo $uid; ?> .olo-hero-windot{width:11px;height:11px;border-radius:50%;background:<?php echo $mixc( 18 ); ?>;flex:none;}
            .<?php echo $uid; ?> .olo-hero-winlabel{margin-left:13px;font-family:<?php echo $mono_ff; ?>;font-size:11px;opacity:.6;}
            <?php endif; ?>
            <?php if ( 'mockup' === $module ) : ?>
            .<?php echo $uid; ?> .olo-hero-mockframe{max-width:1020px;margin:0 auto;border:1px solid <?php echo $mixc( 10 ); ?>;border-radius:16px 16px 0 0;background:<?php echo $mixc( 5 ); ?>;overflow:hidden;text-align:left;box-shadow:0 -10px 80px -20px <?php echo $mix( $accent_css, 0.4 ); ?>;}
            .<?php echo $uid; ?> .olo-hero-mockmedia{position:relative;overflow:hidden;aspect-ratio:16/8.5;background-image:repeating-linear-gradient(135deg, rgba(255,255,255,.035) 0 16px, transparent 16px 32px);background-size:cover;background-position:center center;<?php echo $mock_media['has'] ? $mock_media['css'] : ''; ?>}
            .<?php echo $uid; ?> .olo-hero-mocklabel{position:absolute;left:14px;bottom:12px;right:14px;font-family:<?php echo $mono_ff; ?>;font-size:10.5px;letter-spacing:.03em;text-transform:uppercase;opacity:.45;text-align:left;}
            .<?php echo $uid; ?> .olo-hero-mockbody{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;padding:20px;text-align:left;}
            .<?php echo $uid; ?> .olo-hero-kpi{background:<?php echo $mixc( 5 ); ?>;border:1px solid <?php echo $mixc( 10 ); ?>;border-radius:11px;padding:16px;}
            .<?php echo $uid; ?> .olo-hero-kpi .k{font-family:<?php echo $mono_ff; ?>;font-size:10.5px;letter-spacing:.06em;text-transform:uppercase;opacity:.6;}
            .<?php echo $uid; ?> .olo-hero-kpi .v{font-weight:700;font-size:26px;margin:7px 0 4px;line-height:1.1;}
            .<?php echo $uid; ?> .olo-hero-kpi .t{font-family:<?php echo $mono_ff; ?>;font-size:11px;color:<?php echo $accent_css; ?>;}
            .<?php echo $uid; ?> .olo-hero-kpi .t.dn{color:inherit;opacity:.6;}
            .<?php echo $uid; ?> .olo-hero-chart{grid-column:1/-1;background:<?php echo $mixc( 5 ); ?>;border:1px solid <?php echo $mixc( 10 ); ?>;border-radius:11px;padding:18px 18px 10px;}
            .<?php echo $uid; ?> .olo-hero-chhead{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;font-size:14px;}
            .<?php echo $uid; ?> .olo-hero-chhead span{font-family:<?php echo $mono_ff; ?>;font-size:11px;opacity:.6;}
            .<?php echo $uid; ?> .olo-hero-chbars{display:flex;align-items:flex-end;gap:7px;height:120px;}
            .<?php echo $uid; ?> .olo-hero-chcol{flex:1;display:flex;flex-direction:column;justify-content:flex-end;gap:3px;height:100%;}
            .<?php echo $uid; ?> .olo-hero-chcol i{display:block;width:100%;border-radius:3px 3px 0 0;background:linear-gradient(180deg,<?php echo $accent_css; ?>,<?php echo $mix( $accent_css, 0.25 ); ?>);min-height:4px;}
            .<?php echo $uid; ?> .olo-hero-chcol i.alt{background:linear-gradient(180deg,<?php echo $mixc( 50 ); ?>,<?php echo $mixc( 15 ); ?>);}
            .<?php echo $uid; ?> .olo-hero-chcol span{font-family:<?php echo $mono_ff; ?>;font-size:9.5px;text-align:center;opacity:.55;}
            @media(max-width:680px){.<?php echo $uid; ?> .olo-hero-mockbody{grid-template-columns:1fr 1fr;}}
            <?php endif; ?>
            <?php if ( 'chat' === $module ) : ?>
            .<?php echo $uid; ?> .olo-hero-chatwin{max-width:760px;margin:0 auto;border:1px solid <?php echo $mixc( 10 ); ?>;border-radius:16px 16px 0 0;background:<?php echo $mixc( 5 ); ?>;overflow:hidden;text-align:left;box-shadow:0 -10px 90px -24px <?php echo $mix( $accent_css, 0.4 ); ?>;}
            .<?php echo $uid; ?> .olo-hero-chatbody{padding:22px;display:flex;flex-direction:column;gap:16px;}
            .<?php echo $uid; ?> .olo-hero-msg{max-width:80%;padding:13px 16px;border-radius:14px;font-size:14.5px;line-height:1.5;}
            .<?php echo $uid; ?> .olo-hero-msg.you{align-self:flex-end;background:<?php echo $accent_css; ?>;color:#fff;border-bottom-right-radius:4px;}
            .<?php echo $uid; ?> .olo-hero-msg.ai{align-self:flex-start;background:<?php echo $mixc( 8 ); ?>;border:1px solid <?php echo $mixc( 10 ); ?>;border-bottom-left-radius:4px;opacity:.92;}
            @media(max-width:680px){.<?php echo $uid; ?> .olo-hero-msg{max-width:92%;}}
            <?php endif; ?>
            <?php if ( $has_scene ) : ?>
            .<?php echo $uid; ?> .olo-hero-scene{position:absolute;inset:<?php echo $scene_inset; ?>;z-index:0;overflow:hidden;pointer-events:none;<?php if ( $arch_mask ) : ?>-webkit-mask:<?php echo $arch_mask; ?>;mask:<?php echo $arch_mask; ?>;<?php endif; ?>}
            <?php if ( $mb['has'] ) : ?>
            .<?php echo $uid; ?> .olo-hero-media{position:absolute;inset:0;<?php echo $mb['css']; ?>}
            <?php endif; ?>
            <?php if ( $glow_on ) : ?>
            .<?php echo $uid; ?> .olo-hero-glow{position:absolute;top:<?php echo (int) $glow_y; ?>%;left:<?php echo (int) $glow_x; ?>%;transform:translate(-50%,-30%);width:<?php echo (int) $glow_w; ?>px;height:<?php echo (int) $glow_h; ?>px;border-radius:50%;filter:blur(<?php echo (int) $glow_blur; ?>px);background:radial-gradient(circle, <?php echo $glow_color; ?>, transparent 70%);}
            <?php endif; ?>
            <?php if ( $has_overlay ) : ?>
            .<?php echo $uid; ?> .olo-hero-veil{position:absolute;inset:0;background:<?php echo $veil_grad; ?>;}
            <?php endif; ?>
            <?php if ( $wm_text !== '' ) : ?>
            .<?php echo $uid; ?> .olo-hero-wm{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:clamp(120px,26vw,380px);font-weight:800;line-height:1;letter-spacing:-0.02em;color:<?php echo $wm_color; ?>;user-select:none;white-space:nowrap;}
            <?php endif; ?>
            <?php endif; ?>
            <?php if ( $accent_set ) : ?>
            .<?php echo $uid; ?> .olo-hero-title em{color:<?php echo $accent_css; ?>;}
            <?php endif; ?>
            <?php if ( ! empty( $s['eyebrow_text'] ) ) : ?>
            .<?php echo $uid; ?> .olo-hero-eyebrow{display:inline-flex;align-items:center;gap:9px;font-weight:600;font-size:11.5px;letter-spacing:.28em;text-transform:uppercase;color:<?php echo $accent_css; ?>;margin:0 0 18px;}
            .<?php echo $uid; ?> .olo-hero-eyedot{width:6px;height:6px;border-radius:50%;background:<?php echo $accent_css; ?>;box-shadow:0 0 8px <?php echo $accent_css; ?>;flex:none;}
            <?php endif; ?>
            <?php if ( ! empty( $s['meta_text'] ) ) : ?>
            .<?php echo $uid; ?> .olo-hero-meta{margin-top:22px;font-size:14px;letter-spacing:.16em;text-transform:uppercase;opacity:.85;}
            <?php endif; ?>
            <?php if ( ! empty( $s['scroll_hint'] ) ) : ?>
            .<?php echo $uid; ?> .olo-hero-scrollhint{position:absolute;bottom:28px;left:50%;transform:translateX(-50%);z-index:2;font-size:11px;letter-spacing:.16em;text-transform:uppercase;opacity:.7;}
            <?php endif; ?>

            .<?php echo $uid; ?> .olo-hero-content {
                position: relative;
                z-index: 2;
                display: flex;
                flex: 1;
                width: 100%;
                align-items: <?php echo $v_align; ?>;
                justify-content: <?php echo $h_align; ?>;
                padding: <?php echo (int) $pad_t; ?>px <?php echo (int) $pad_r; ?>px <?php echo (int) $pad_b; ?>px <?php echo (int) $pad_l; ?>px;
            }

            .<?php echo $uid; ?> .olo-hero-inner {
                max-width: <?php echo (int) $max_w; ?>px;
                width: 100%;
                text-align: <?php echo $text_align; ?>;
            }

            .<?php echo $uid; ?> .olo-hero-inner h1 {
                margin: 0 0 0.4em;
                color: <?php echo $fg; ?>;
            }

            .<?php echo $uid; ?> .olo-hero-inner .olo-hero-sub {
                font-size: 1.25em;
                margin-bottom: 1.5em;
                opacity: 0.9;
                color: <?php echo $fg; ?>;
            }

            .<?php echo $uid; ?> .olo-hero-cta-wrap {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                <?php
                $jc = 'center';
                if ( $text_align === 'left' ) $jc = 'flex-start';
                if ( $text_align === 'right' ) $jc = 'flex-end';
                ?>
                justify-content: <?php echo $jc; ?>;
            }

            /* CTA Primary */
            .<?php echo $uid; ?> .olo-hero-cta1 {
                display: inline-block;
                font-weight: 600;
                text-decoration: none !important;
                <?php echo $cta_radius_css; ?>
                <?php echo $cta_size_css; ?>
                transition: opacity .2s, transform .2s;
                <?php echo $this->build_cta_css( $s['cta_style'], $cta_bg, $cta_fg ); ?>
            }
            <?php if ( $cta_radius_hover_css !== '' ) : ?>.<?php echo $uid; ?> .olo-hero-cta1{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?> .olo-hero-cta1:hover{border-radius:<?php echo $cta_radius_hover_css; ?> !important}<?php endif; ?>
            .<?php echo $uid; ?> .olo-hero-cta1:hover {
                opacity: .85;
                transform: translateY(-1px);
                color: <?php echo $cta_fg; ?> !important;
                text-decoration: none !important;
            }

            /* CTA Secondary */
            <?php if ( ! empty( $s['cta2_text'] ) ) : ?>
            .<?php echo $uid; ?> .olo-hero-cta2 {
                display: inline-block;
                font-weight: 600;
                text-decoration: none !important;
                <?php echo $cta_radius_css; ?>
                <?php echo $cta_size_css; ?>
                transition: opacity .2s, transform .2s;
                <?php echo $this->build_cta_css( $s['cta2_style'], $cta2_bg, $cta2_fg ); ?>
            }
            .<?php echo $uid; ?> .olo-hero-cta2:hover {
                opacity: .85;
                transform: translateY(-1px);
                color: <?php echo $cta2_fg; ?> !important;
                text-decoration: none !important;
            }
            <?php endif; ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-hero <?php echo esc_attr( $uid ); ?> olo-hero-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>">
            <?php if ( $has_scene ) : ?>
            <div class="olo-hero-scene">
                <?php if ( $mb['has'] ) : ?><div class="olo-hero-media"><?php if ( $mb['markup'] !== '' ) { echo $mb['markup']; } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- media markup generated by Olobuild_CSS_Builder::get_bg_html_markup() which escapes its own attributes ?></div><?php endif; ?>
                <?php if ( $glow_on ) : ?><span class="olo-hero-glow"></span><?php endif; ?>
                <?php if ( $has_overlay ) : ?><div class="olo-hero-veil"></div><?php endif; ?>
                <?php if ( $wm_text !== '' ) : ?><span class="olo-hero-wm" aria-hidden="true"><?php echo esc_html( $wm_text ); ?></span><?php endif; ?>
            </div>
            <?php endif; ?>
            <div class="olo-hero-content">
                <div class="olo-hero-inner">
                    <?php if ( ! empty( $s['eyebrow_text'] ) ) : ?>
                    <span class="olo-hero-eyebrow"><?php if ( ! empty( $s['eyebrow_dot'] ) ) : ?><span class="olo-hero-eyedot"></span><?php endif; ?><?php echo esc_html( $s['eyebrow_text'] ); ?></span>
                    <?php endif; ?>
                    <?php
                    // Title inline style
                    $title_css = '';
                    if ( ! empty( $s['title_font_family'] ) ) {
                        $title_css .= 'font-family:' . esc_attr( $s['title_font_family'] ) . ';';
                    }
                    if ( ! empty( $s['title_font_size'] ) ) {
                        $title_css .= 'font-size:' . intval( $s['title_font_size'] ) . 'px;';
                    }
                    $title_css .= 'font-weight:' . esc_attr( $s['title_font_weight'] ?: '700' ) . ';';
                    $title_css .= 'line-height:' . esc_attr( $s['title_line_height'] ?: '1.2' ) . ';';
                    if ( ! empty( $s['title_letter_spacing'] ) && floatval( $s['title_letter_spacing'] ) != 0 ) {
                        $title_css .= 'letter-spacing:' . floatval( $s['title_letter_spacing'] ) . 'px;';
                    }
                    if ( ! empty( $s['title_text_transform'] ) && $s['title_text_transform'] !== 'none' ) {
                        $title_css .= 'text-transform:' . esc_attr( $s['title_text_transform'] ) . ';';
                    }
                    if ( ! empty( $s['title_color'] ) ) {
                        $title_css .= 'color:' . $this->safe_color_css( $s['title_color'] ) . ';';
                    }
                    // Title text-shadow (preset rapidi). Per ombre custom: tab Stile → Effetti → Ombra testo.
                    if ( ! empty( $s['title_text_shadow'] ) ) {
                        $title_css .= 'text-shadow:' . esc_attr( $s['title_text_shadow'] ) . ';';
                    }
                    $title_css .= 'margin:0 0 12px 0;';
                    $title_tag = in_array( $s['title_tag'], [ 'h1', 'h2', 'h3', 'p', 'span' ], true ) ? $s['title_tag'] : 'h1';
                    ?>
                    <<?php echo $title_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tag from the in_array() whitelist above; title wp_kses()'d with a fixed inline-tag set; style attr built from esc_attr()/intval()/floatval()/safe_color_css() parts; fx class/data from Olobuild_Text_Effects helpers ?> class="olo-hero-title<?php echo $h_tfx_cls; ?>" style="<?php echo $title_css; ?>"<?php echo $h_tfx_data; ?>><?php echo $title; ?></<?php echo $title_tag; ?>>
                    <?php
                    // Subtitle inline style
                    $sub_css = 'opacity:0.9;margin:0 0 24px 0;';
                    if ( ! empty( $s['subtitle_font_size'] ) ) {
                        $sub_css .= 'font-size:' . intval( $s['subtitle_font_size'] ) . 'px;';
                    }
                    if ( ! empty( $s['subtitle_font_weight'] ) ) {
                        $sub_css .= 'font-weight:' . esc_attr( $s['subtitle_font_weight'] ) . ';';
                    }
                    if ( ! empty( $s['subtitle_letter_spacing'] ) && floatval( $s['subtitle_letter_spacing'] ) != 0 ) {
                        $sub_css .= 'letter-spacing:' . floatval( $s['subtitle_letter_spacing'] ) . 'px;';
                    }
                    if ( ! empty( $s['subtitle_color'] ) ) {
                        $sub_css .= 'color:' . $this->safe_color_css( $s['subtitle_color'] ) . ';opacity:1;';
                    }
                    if ( ! empty( $s['subtitle_max_width'] ) ) {
                        $sub_css .= 'max-width:' . intval( $s['subtitle_max_width'] ) . 'px;';
                    }
                    ?>
                    <div class="olo-hero-sub<?php echo $s_tfx_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- subtitle wp_kses()'d with a fixed inline-tag set; style attr built from esc_attr()/intval()/floatval()/safe_color_css() parts; fx class/data from Olobuild_Text_Effects helpers ?>" style="<?php echo $sub_css; ?>"<?php echo $s_tfx_data; ?>><?php echo $subtitle; ?></div>

                    <?php if ( ! empty( $s['cta_text'] ) || ! empty( $s['cta2_text'] ) ) : ?>
                        <div class="olo-hero-cta-wrap">
                            <?php if ( ! empty( $s['cta_text'] ) ) : ?>
                                <a href="<?php echo esc_url( $s['cta_url'] ); ?>"
                                   class="olo-hero-cta1"
                                   <?php if ( $s['cta_target'] === '_blank' ) echo 'target="_blank" rel="noopener"'; ?>>
                                    <?php echo esc_html( wp_strip_all_tags( $s['cta_text'] ) ); ?>
                                </a>
                            <?php endif; ?>
                            <?php if ( ! empty( $s['cta2_text'] ) ) : ?>
                                <a href="<?php echo esc_url( $s['cta2_url'] ); ?>"
                                   class="olo-hero-cta2"
                                   <?php if ( $s['cta2_target'] === '_blank' ) echo 'target="_blank" rel="noopener"'; ?>>
                                    <?php echo esc_html( wp_strip_all_tags( $s['cta2_text'] ) ); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['meta_text'] ) ) : ?>
                        <div class="olo-hero-meta"><?php echo esc_html( $s['meta_text'] ); ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ( $has_module ) : ?>
            <div class="olo-hero-modwrap">
                <?php if ( 'strip' === $module ) : ?>
                <div class="olo-hero-strip">
                    <?php foreach ( $strip_items as $it ) :
                        $img  = isset( $it['image'] ) ? trim( (string) $it['image'] ) : '';
                        $cap  = isset( $it['caption'] ) ? (string) $it['caption'] : '';
                        $isty = $img !== '' ? ' style="background-image:url(' . esc_url( $img ) . ')"' : '';
                        ?>
                        <div class="olo-hero-stripmedia"<?php echo $isty; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- style attribute built above with esc_url()'d image URL, or empty string ?>><?php if ( $cap !== '' ) : ?><span class="olo-hero-stripcap"><?php echo esc_html( $cap ); ?></span><?php endif; ?></div>
                    <?php endforeach; ?>
                </div>
                <?php elseif ( 'search' === $module ) : ?>
                <form class="olo-hero-searchbox" role="search" method="get" action="<?php echo esc_url( $search_url ); ?>">
                    <input type="search" name="s" placeholder="<?php echo esc_attr( $s['search_placeholder'] ); ?>" aria-label="<?php echo esc_attr( $s['search_placeholder'] ?: 'Cerca' ); ?>"/>
                    <?php if ( ! empty( $s['search_button'] ) ) : ?>
                        <button type="submit" class="olo-hero-cta1 olo-hero-searchbtn"><?php echo esc_html( $s['search_button'] ); ?></button>
                    <?php endif; ?>
                </form>
                <?php if ( ! empty( $chips ) ) : ?>
                <div class="olo-hero-chips">
                    <?php foreach ( $chips as $chip ) : ?>
                        <a class="olo-hero-chip" href="<?php echo esc_url( add_query_arg( 's', $chip, $search_url ) ); ?>"><?php echo esc_html( $chip ); ?></a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <?php elseif ( 'mockup' === $module ) : ?>
                <div class="olo-hero-mockframe">
                    <div class="olo-hero-winbar"><span class="olo-hero-windot"></span><span class="olo-hero-windot"></span><span class="olo-hero-windot"></span><?php if ( ! empty( $s['mock_url'] ) ) : ?><span class="olo-hero-winlabel"><?php echo esc_html( $s['mock_url'] ); ?></span><?php endif; ?></div>
                    <?php if ( 'media' === $mock_mode ) : ?>
                    <div class="olo-hero-mockmedia"><?php if ( $mock_media['has'] && $mock_media['markup'] !== '' ) { echo $mock_media['markup']; } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- media markup generated by Olobuild_CSS_Builder::get_bg_html_markup() which escapes its own attributes ?><?php if ( ! $mock_media['has'] && ! empty( $s['mock_label'] ) ) : ?><span class="olo-hero-mocklabel"><?php echo esc_html( $s['mock_label'] ); ?></span><?php endif; ?></div>
                    <?php else : ?>
                    <div class="olo-hero-mockbody">
                        <?php foreach ( $mock_kpis as $k ) :
                            $kl = isset( $k['label'] ) ? (string) $k['label'] : '';
                            $kv = isset( $k['value'] ) ? (string) $k['value'] : '';
                            $kt = isset( $k['delta'] ) ? (string) $k['delta'] : '';
                            $kd = ! empty( $k['down'] ) ? ' dn' : '';
                            ?>
                        <div class="olo-hero-kpi"><div class="k"><?php echo esc_html( $kl ); ?></div><div class="v"><?php echo esc_html( $kv ); ?></div><?php if ( $kt !== '' ) : ?><div class="t<?php echo esc_attr( $kd ); ?>"><?php echo esc_html( $kt ); ?></div><?php endif; ?></div>
                        <?php endforeach; ?>
                        <div class="olo-hero-chart">
                            <div class="olo-hero-chhead"><b><?php echo esc_html( $s['mock_chart_title'] ); ?></b><span><?php echo esc_html( $s['mock_chart_meta'] ); ?></span></div>
                            <div class="olo-hero-chbars">
                                <?php foreach ( $mock_bars as $b ) :
                                    $bh = max( 0, min( 100, intval( $b['h'] ?? 0 ) ) );
                                    $bl = isset( $b['label'] ) ? (string) $b['label'] : '';
                                    $b2 = ! empty( $b['alt'] ) ? ' class="alt"' : '';
                                    ?>
                                <div class="olo-hero-chcol"><i<?php echo $b2; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed class-attribute literal from the ternary above ?> style="height:<?php echo (int) $bh; ?>%"></i><?php if ( $bl !== '' ) : ?><span><?php echo esc_html( $bl ); ?></span><?php endif; ?></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php elseif ( 'chat' === $module ) : ?>
                <div class="olo-hero-chatwin">
                    <div class="olo-hero-winbar"><span class="olo-hero-windot"></span><span class="olo-hero-windot"></span><span class="olo-hero-windot"></span><?php if ( ! empty( $s['chat_label'] ) ) : ?><span class="olo-hero-winlabel"><?php echo esc_html( $s['chat_label'] ); ?></span><?php endif; ?></div>
                    <div class="olo-hero-chatbody">
                        <?php foreach ( $chat_msgs as $m ) {
                            $mtext = isset( $m['text'] ) ? (string) $m['text'] : '';
                            if ( $mtext === '' ) { continue; }
                            $side = ( isset( $m['side'] ) && $m['side'] === 'you' ) ? 'you' : 'ai';
                            echo '<div class="olo-hero-msg ' . esc_attr( $side ) . '">' . esc_html( $mtext ) . '</div>';
                        } ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php if ( ! empty( $s['scroll_hint'] ) ) : ?>
                <span class="olo-hero-scrollhint"><?php echo esc_html( $s['scroll_hint'] ); ?></span>
            <?php endif; ?>
        </div>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Text_Effects::css() from a fixed effect map, scoped to the internal uid selector
        $this->tfx_print_script();
        return ob_get_clean();
    }

    /**
     * Map alignment values to CSS flex properties.
     */
    private function map_align( $value, $axis ) {
        $map = [
            'top'    => 'flex-start',
            'center' => 'center',
            'bottom' => 'flex-end',
            'left'   => 'flex-start',
            'right'  => 'flex-end',
        ];
        return $map[ $value ] ?? 'center';
    }

    /**
     * Build CTA style CSS for filled/outline/ghost variants.
     */
    private function build_cta_css( $style, $bg, $fg ) {
        switch ( $style ) {
            case 'outline':
                return "background: transparent !important; color: {$fg} !important; border: 2px solid {$fg};";
            case 'ghost':
                return "background: transparent !important; color: {$fg} !important; border: none;";
            default: // filled
                return "background: {$bg} !important; color: {$fg} !important; border: none;";
        }
    }
}
