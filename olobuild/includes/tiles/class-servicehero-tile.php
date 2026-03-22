<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_ServiceHero_Tile extends Olo_Tile_Base {

    protected $type     = 'servicehero';
    protected $name     = 'Hero Servizio';
    protected $icon     = 'dashicons-format-image';
    protected $category = 'booking';
    protected $defaults = [
        'meta_prefix'       => '_olo_service_',
        'hero_height'       => 400,
        'hero_radius'       => 16,
        // Overlay
        'hero_overlay'      => 'gradient',
        'overlay_color'     => '#000000',
        'overlay_opacity'   => 45,
        // Badge
        'show_badges'       => true,
        'badge_bg'          => 'rgba(30,41,59,0.75)',
        'badge_color'       => '#FFFFFF',
        // Title
        'show_title'        => true,
        'title_size'        => 30,
        'title_color'       => '#FFFFFF',
        'title_position'    => 'bottom',
        // Opening ribbon
        'show_opening'      => true,
        'opening_bg'        => '#059669',
        'opening_color'     => '#FFFFFF',
        // Effects — automatic
        'fx_kenburns'       => false,
        'fx_kenburns_speed' => 25,
        'fx_kenburns_scale' => 1.15,
        'fx_shimmer'        => false,
        'fx_shimmer_speed'  => 6,
        // Effects — hover
        'fx_hover_zoom'     => true,
        'fx_hover_zoom_scale' => 1.06,
        'fx_hover_tilt'     => false,
        // Effects — visual filters
        'fx_vignette'       => false,
        'fx_vignette_strength' => 50,
        'fx_grain'          => false,
        'fx_grain_opacity'  => 8,
        'fx_tint'           => false,
        'fx_tint_color'     => '#1E3A5F',
        'fx_tint_opacity'   => 15,
        'fx_tint_blend'     => 'multiply',
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        global $post;
        if ( ! $post || ! is_singular() ) {
            return '<div style="padding:24px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);background:var(--olo-color-muted, #F3F4F6);border-radius:8px">'
                 . '<p style="margin:0">Inserisci in un template single.</p></div>';
        }

        $pid  = $post->ID;
        $pfx  = rtrim( $s['meta_prefix'], '_' ) . '_';

        $title    = get_the_title( $pid );
        $thumb    = get_the_post_thumbnail_url( $pid, 'full' );
        $locality = get_post_meta( $pid, $pfx . 'locality', true );
        if ( ! $locality ) {
            $locality = get_post_meta( $pid, $pfx . 'valley', true );
        }
        $altitude = get_post_meta( $pid, $pfx . 'altitude', true );
        $opening  = get_post_meta( $pid, $pfx . 'opening', true );

        $h      = absint( $s['hero_height'] ) ?: 400;
        $radius_css = $this->build_border_radius_css( $s["hero_radius"] );
        $uid    = 'olo-shero-' . wp_rand( 10000, 99999 );

        // Overlay
        $overlay_css = '';
        if ( $s['hero_overlay'] === 'gradient' ) {
            $oc   = $this->safe_color_css( $s['overlay_color'] );
            $opa  = max( 0, min( 100, absint( $s['overlay_opacity'] ) ) );
            $overlay_css = "background:linear-gradient(to bottom,{$oc}05 40%,{$oc}" . dechex( (int) round( $opa * 2.55 ) ) . " 100%)";
        } elseif ( $s['hero_overlay'] === 'dark' ) {
            $opa  = max( 0, min( 100, absint( $s['overlay_opacity'] ) ) ) / 100;
            $overlay_css = "background:rgba(0,0,0,{$opa})";
        } elseif ( $s['hero_overlay'] === 'radial' ) {
            $overlay_css = 'background:radial-gradient(ellipse at center,rgba(0,0,0,0) 30%,rgba(0,0,0,0.5) 100%)';
        }

        // Ken Burns
        $kb_speed = max( 10, absint( $s['fx_kenburns_speed'] ) );
        $kb_scale = max( 1.05, min( 1.4, floatval( $s['fx_kenburns_scale'] ) ) );

        // Shimmer
        $shimmer_speed = max( 3, absint( $s['fx_shimmer_speed'] ) );

        // Hover zoom
        $hz_scale = max( 1.02, min( 1.2, floatval( $s['fx_hover_zoom_scale'] ) ) );

        // Vignette
        $vig_str = max( 20, min( 80, absint( $s['fx_vignette_strength'] ) ) );

        // Grain
        $grain_opa = max( 3, min( 20, absint( $s['fx_grain_opacity'] ) ) );

        // Tint
        $tint_opa = max( 5, min( 50, absint( $s['fx_tint_opacity'] ) ) );

        // Image CSS classes
        $img_classes = 'olo-shero-img';
        if ( ! empty( $s['fx_kenburns'] ) )    $img_classes .= ' olo-shero-kb';
        if ( ! empty( $s['fx_hover_zoom'] ) )  $img_classes .= ' olo-shero-hz';

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> { position:relative; height:<?php echo $h; ?>px; overflow:hidden; border-radius:<?php echo $radius_css; ?>; }
            .<?php echo $uid; ?> .olo-shero-img { width:100%; height:100%; object-fit:cover; display:block; transition:transform 0.6s cubic-bezier(.25,.46,.45,.94),filter 0.6s ease; will-change:transform; }

            /* Ken Burns */
            <?php if ( ! empty( $s['fx_kenburns'] ) ) : ?>
            @keyframes <?php echo $uid; ?>-kb {
                0%   { transform:scale(1) translate(0,0); }
                25%  { transform:scale(<?php echo $kb_scale; ?>) translate(-1%,-1%); }
                50%  { transform:scale(<?php echo $kb_scale - 0.03; ?>) translate(1%,0.5%); }
                75%  { transform:scale(<?php echo $kb_scale; ?>) translate(0.5%,-0.5%); }
                100% { transform:scale(1) translate(0,0); }
            }
            .<?php echo $uid; ?> .olo-shero-kb { animation:<?php echo $uid; ?>-kb <?php echo $kb_speed; ?>s ease-in-out infinite; }
            <?php endif; ?>

            /* Hover zoom */
            <?php if ( ! empty( $s['fx_hover_zoom'] ) ) : ?>
            .<?php echo $uid; ?>:hover .olo-shero-hz { transform:scale(<?php echo $hz_scale; ?>); }
            <?php endif; ?>

            /* Hover tilt (perspective) */
            <?php if ( ! empty( $s['fx_hover_tilt'] ) ) : ?>
            .<?php echo $uid; ?> { perspective:800px; }
            .<?php echo $uid; ?>:hover .olo-shero-img { transform:scale(1.03) rotateX(1deg) rotateY(-1deg); }
            <?php endif; ?>

            /* Overlay */
            .<?php echo $uid; ?> .olo-shero-overlay { position:absolute; inset:0; <?php echo $overlay_css; ?>; pointer-events:none; z-index:1; }

            /* Vignette */
            <?php if ( ! empty( $s['fx_vignette'] ) ) : ?>
            .<?php echo $uid; ?>::after { content:''; position:absolute; inset:0; border-radius:<?php echo $radius_css; ?>; box-shadow:inset 0 0 <?php echo $vig_str * 2; ?>px <?php echo $vig_str; ?>px rgba(0,0,0,0.<?php echo min( 60, $vig_str ); ?>); pointer-events:none; z-index:1; }
            <?php endif; ?>

            /* Shimmer */
            <?php if ( ! empty( $s['fx_shimmer'] ) ) : ?>
            @keyframes <?php echo $uid; ?>-shimmer {
                0%   { transform:translateX(-100%) rotate(25deg); }
                100% { transform:translateX(200%) rotate(25deg); }
            }
            .<?php echo $uid; ?> .olo-shero-shimmer { position:absolute; inset:-50%; z-index:2; background:linear-gradient(90deg,transparent 30%,rgba(255,255,255,0.12) 50%,transparent 70%); animation:<?php echo $uid; ?>-shimmer <?php echo $shimmer_speed; ?>s ease-in-out infinite; pointer-events:none; }
            <?php endif; ?>

            /* Grain */
            <?php if ( ! empty( $s['fx_grain'] ) ) : ?>
            .<?php echo $uid; ?> .olo-shero-grain { position:absolute; inset:0; z-index:2; opacity:0.<?php echo str_pad( $grain_opa, 2, '0', STR_PAD_LEFT ); ?>; pointer-events:none; background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E"); background-repeat:repeat; background-size:128px 128px; mix-blend-mode:overlay; }
            <?php endif; ?>

            /* Color tint */
            <?php if ( ! empty( $s['fx_tint'] ) ) : ?>
            .<?php echo $uid; ?> .olo-shero-tint { position:absolute; inset:0; z-index:1; background:<?php echo $this->safe_color_css( $s['fx_tint_color'] ); ?>; opacity:0.<?php echo str_pad( $tint_opa, 2, '0', STR_PAD_LEFT ); ?>; mix-blend-mode:<?php echo esc_attr( $s['fx_tint_blend'] ); ?>; pointer-events:none; }
            <?php endif; ?>

            /* Badges */
            .<?php echo $uid; ?> .olo-shero-badges { position:absolute; top:16px; right:16px; display:flex; flex-wrap:wrap; gap:8px; z-index:3; justify-content:flex-end; }
            .<?php echo $uid; ?> .olo-shero-badge { background:<?php echo $this->safe_color_css( $s['badge_bg'] ); ?>; backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px); color:<?php echo $this->safe_color_css( $s['badge_color'] ); ?>; padding:6px 14px; border-radius:20px; font-size:13px; font-weight:600; display:flex; align-items:center; gap:5px; transition:transform 0.3s ease,box-shadow 0.3s ease; }
            .<?php echo $uid; ?>:hover .olo-shero-badge { transform:translateY(-2px); box-shadow:0 4px 12px rgba(0,0,0,0.2); }
            .<?php echo $uid; ?> .olo-shero-badge--opening { background:<?php echo $this->safe_color_css( $s['opening_bg'] ); ?>; color:<?php echo $this->safe_color_css( $s['opening_color'] ); ?>; }

            /* Title */
            .<?php echo $uid; ?> .olo-shero-title { position:absolute; bottom:24px; left:24px; right:24px; z-index:3; font-size:<?php echo absint( $s['title_size'] ); ?>px; font-weight:700; color:<?php echo $this->safe_color_css( $s['title_color'] ); ?>; margin:0; line-height:1.2; text-shadow:0 2px 12px rgba(0,0,0,0.4); transition:transform 0.4s ease,text-shadow 0.4s ease; }
            .<?php echo $uid; ?>:hover .olo-shero-title { transform:translateY(-3px); text-shadow:0 4px 20px rgba(0,0,0,0.5); }
            <?php if ( $s['title_position'] === 'center' ) : ?>
            .<?php echo $uid; ?> .olo-shero-title { bottom:auto; top:50%; left:50%; transform:translate(-50%,-50%); text-align:center; width:80%; }
            .<?php echo $uid; ?>:hover .olo-shero-title { transform:translate(-50%,-52%); }
            <?php endif; ?>
        </style>
        <div class="<?php echo esc_attr( $uid ); ?>">
            <?php if ( $thumb ) : ?>
                <img class="<?php echo esc_attr( $img_classes ); ?>" src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $title ); ?>" />
            <?php else : ?>
                <div class="olo-shero-img" style="width:100%;height:100%;background:linear-gradient(135deg,#0891B240,#0891B290)"></div>
            <?php endif; ?>

            <div class="olo-shero-overlay"></div>

            <?php if ( ! empty( $s['fx_tint'] ) ) : ?>
                <div class="olo-shero-tint"></div>
            <?php endif; ?>

            <?php if ( ! empty( $s['fx_shimmer'] ) ) : ?>
                <div class="olo-shero-shimmer"></div>
            <?php endif; ?>

            <?php if ( ! empty( $s['fx_grain'] ) ) : ?>
                <div class="olo-shero-grain"></div>
            <?php endif; ?>

            <?php
            $has_badges  = ! empty( $s['show_badges'] ) && ( $locality || $altitude );
            $has_opening = ! empty( $s['show_opening'] ) && $opening;
            if ( $has_badges || $has_opening ) : ?>
            <div class="olo-shero-badges">
                <?php if ( $has_opening ) : ?>
                    <span class="olo-shero-badge olo-shero-badge--opening"><?php echo esc_html( $opening ); ?></span>
                <?php endif; ?>
                <?php if ( $has_badges && $locality ) : ?>
                    <span class="olo-shero-badge">&#9737; <?php echo esc_html( $locality ); ?></span>
                <?php endif; ?>
                <?php if ( $has_badges && $altitude ) : ?>
                    <span class="olo-shero-badge">&#9968; <?php echo esc_html( $altitude ); ?> m</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if ( ! empty( $s['show_title'] ) ) : ?>
                <h1 class="olo-shero-title"><?php echo esc_html( $title ); ?></h1>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
