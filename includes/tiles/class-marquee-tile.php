<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Marquee_Tile extends Olo_Tile_Base {

    protected $type     = 'marquee';
    protected $name     = 'Nastro Scorrevole';
    protected $icon     = 'dashicons-slides';
    protected $category = 'media';
    protected $defaults = [
        'content_type'   => 'text',
        'text_items'     => 'Testo scorrevole di esempio',
        'separator'      => ' — ',
        'images'         => [],
        'image_height'   => '40',

        'speed'          => '30',
        'direction'      => 'left',
        'pause_hover'    => true,
        'gap'            => '60',

        // VelocitySkew (reattivo allo scroll) — default OFF: i Marquee esistenti restano invariati
        'velocity_skew'      => false,
        'vskew_base_speed'   => 0.6,
        'vskew_scroll_boost' => 0.6,
        'vskew_max_skew'     => 14,
        'vskew_damping'      => 0.86,

        'bg_color'       => '#1F2937',
        'text_color'     => '#FFFFFF',
        'font_size'      => '16',
        'font_weight'    => '500',
        'letter_spacing' => '1',
        'text_transform' => 'uppercase',
        'font_family'    => '',
        'font_style'     => 'normal',
        'separator_color'=> '',
        'separator_size' => '',
        'height'         => '50',
        'full_width'     => false,
        'border_top'     => '0',
        'border_bottom'  => '0',
        'border_color'   => '',
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

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-mq-' . wp_rand( 10000, 99999 );

        $is_text     = $s['content_type'] !== 'images';
        $speed       = max( 5, intval( $s['speed'] ) );
        $direction   = $s['direction'] === 'right' ? 'right' : 'left';
        $pause       = ! empty( $s['pause_hover'] );
        $gap         = max( 0, intval( $s['gap'] ) );

        // VelocitySkew (reattivo allo scroll) — vedi runtime in fondo
        $vskew     = ! empty( $s['velocity_skew'] );
        $vs_base   = max( 0,   min( 3,    floatval( $s['vskew_base_speed'] ) ) );
        $vs_boost  = max( 0,   min( 2,    floatval( $s['vskew_scroll_boost'] ) ) );
        $vs_max    = max( 0,   min( 30,   intval( $s['vskew_max_skew'] ) ) );
        $vs_damp   = max( 0.5, min( 0.98, floatval( $s['vskew_damping'] ) ) );
        $dir_sign  = $direction === 'right' ? 1 : -1;
        // Bg: preferisce l'oggetto "bg" (Sfondo creativo) — supporta solid/gradient/pattern via CSS Builder.
        // Fallback su bg_color setting (colore semplice).
        $bg_obj  = $s['bg'] ?? null;
        $bg_decl = '';
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olo_CSS_Builder' ) ) {
            $bg_decl = ( new Olo_CSS_Builder() )->get_bg_inline_css( $bg_obj );
        }
        if ( ! $bg_decl ) {
            $color   = $this->safe_color_css( $s['bg_color'] ) ?: '#1F2937';
            $bg_decl = 'background: ' . $color;
        }
        $bg = $bg_decl;
        $height      = max( 20, intval( $s['height'] ) );
        $full_width  = ! empty( $s['full_width'] );
        $bt          = max( 0, intval( $s['border_top'] ) );
        $bb          = max( 0, intval( $s['border_bottom'] ) );
        $bc          = $this->safe_color_css( $s['border_color'] ) ?: 'var(--olo-color-text, #374151)';

        // Text settings
        $text_color  = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-secondary-contrast, #FFFFFF)';
        $font_size   = max( 10, intval( $s['font_size'] ) );
        $font_weight = in_array( $s['font_weight'], [ '400', '500', '600', '700', '900' ] ) ? $s['font_weight'] : '500';
        $ls          = max( 0, intval( $s['letter_spacing'] ) );
        $tt          = in_array( $s['text_transform'], [ 'none', 'uppercase', 'lowercase' ] ) ? $s['text_transform'] : 'uppercase';

        // Font famiglia/stile + separatore (additivi, default no-op).
        $ffkey = $s['font_family'] ?? '';
        if ( $ffkey === 'sans' ) {
            $font_family = "var(--olo-font-family, inherit)";
        } elseif ( $ffkey === 'serif' || $ffkey === 'heading' ) {
            $font_family = "var(--olo-font-family-heading, Georgia, serif)";
        } else {
            $font_family = '';
        }
        $fstyle  = ( ( $s['font_style'] ?? 'normal' ) === 'italic' ) ? 'italic' : 'normal';
        $sepcol  = $this->safe_color_css( $s['separator_color'] ?? '' ) ?: $text_color;
        $sep_op  = ( ( $s['separator_color'] ?? '' ) !== '' ) ? '1' : '0.5';
        $sepsize = intval( $s['separator_size'] ?? 0 );
        $sepsize = $sepsize > 0 ? $sepsize : $font_size;

        // Image settings
        $images      = is_array( $s['images'] ) ? $s['images'] : [];
        $img_height  = max( 20, intval( $s['image_height'] ) );

        // Build the inner content HTML (will be duplicated for seamless loop)
        $inner_html = '';
        if ( $is_text ) {
            $text = esc_html( wp_strip_all_tags( $s['text_items'] ) );
            $sep  = esc_html( $s['separator'] );
            // Repeat text+separator enough times for a wide strip
            for ( $i = 0; $i < 10; $i++ ) {
                $inner_html .= '<span class="olo-mq-text">' . $text . '</span>';
                if ( $sep ) {
                    $inner_html .= '<span class="olo-mq-sep">' . $sep . '</span>';
                }
            }
        } else {
            if ( ! empty( $images ) ) {
                // Gallery returns array of {url, alt, caption} or plain strings
                for ( $loop = 0; $loop < 2; $loop++ ) {
                    foreach ( $images as $img ) {
                        $url = is_array( $img ) ? ( $img['url'] ?? '' ) : $img;
                        $url = esc_url( $url );
                        if ( $url ) {
                            $alt = is_array( $img ) ? esc_attr( $img['alt'] ?? '' ) : '';
                            $inner_html .= '<img class="olo-mq-img" src="' . $url . '" alt="' . $alt . '" loading="lazy" />';
                        }
                    }
                }
            } else {
                $inner_html = '<span class="olo-mq-text" style="opacity:0.5;">Aggiungi immagini...</span>';
            }
        }

        ob_start();
        ?>
        <style>
            @keyframes <?php echo $uid; ?>-scroll {
                0% { transform: translateX(0); }
                100% { transform: translateX(-50%); }
            }

            .<?php echo $uid; ?> {
                display: flex;
                align-items: center;
                <?php echo $bg; ?>;
                height: <?php echo $height; ?>px;
                overflow: hidden;
                width: 100%;
                <?php if ( $full_width ) : ?>
                width: 100vw;
                position: relative;
                left: 50%;
                margin-left: -50vw;
                <?php endif; ?>
            }
            /* Annulla breakout quando il nastro è dentro una cella di griglia */
            .uk-grid > * .<?php echo $uid; ?>,
            [class*="uk-width-"] .<?php echo $uid; ?>,
            .uk-panel .<?php echo $uid; ?> {
                width: 100%;
                left: auto;
                margin-left: 0;
                position: static;
                <?php if ( $bt > 0 ) : ?>border-top: <?php echo $bt; ?>px solid <?php echo $bc; ?>;<?php endif; ?>
                <?php if ( $bb > 0 ) : ?>border-bottom: <?php echo $bb; ?>px solid <?php echo $bc; ?>;<?php endif; ?>
            }

            .<?php echo $uid; ?> .olo-mq-track {
                display: flex;
                align-items: center;
                height: 100%;
                width: max-content;
                gap: <?php echo $gap; ?>px;
                animation: <?php echo $uid; ?>-scroll <?php echo $speed; ?>s linear infinite;
                <?php if ( $direction === 'right' ) : ?>
                animation-direction: reverse;
                <?php endif; ?>
                <?php if ( $vskew ) : ?>
                will-change: transform;
                transition: transform .1s linear;
                <?php endif; ?>
            }

            <?php if ( $pause ) : ?>
            .<?php echo $uid; ?>:hover .olo-mq-track {
                animation-play-state: paused;
            }
            <?php endif; ?>

            <?php if ( $is_text ) : ?>
            .<?php echo $uid; ?> .olo-mq-text {
                color: <?php echo $text_color; ?>;
                font-size: <?php echo $font_size; ?>px;
                font-weight: <?php echo $font_weight; ?>;
                letter-spacing: <?php echo $ls; ?>px;
                text-transform: <?php echo $tt; ?>;
                <?php if ( $font_family ) : ?>font-family: <?php echo $font_family; ?>;<?php endif; ?>
                font-style: <?php echo $fstyle; ?>;
                line-height: 1;
                white-space: nowrap;
            }
            .<?php echo $uid; ?> .olo-mq-text p {
                margin: 0;
                padding: 0;
                display: inline;
                flex-shrink: 0;
            }
            .<?php echo $uid; ?> .olo-mq-sep {
                color: <?php echo $sepcol; ?>;
                font-size: <?php echo $sepsize; ?>px;
                font-weight: <?php echo $font_weight; ?>;
                line-height: 1;
                opacity: <?php echo $sep_op; ?>;
                white-space: nowrap;
                flex-shrink: 0;
            }
            <?php else : ?>
            .<?php echo $uid; ?> .olo-mq-img {
                height: <?php echo $img_height; ?>px;
                width: auto;
                flex-shrink: 0;
                object-fit: contain;
                pointer-events: none;
                -webkit-user-drag: none;
            }
            <?php endif; ?>
        </style>
        <div class="olo-mq <?php echo esc_attr( $uid ); ?>">
            <div class="olo-mq-track">
                <?php echo $inner_html; ?>
            </div>
        </div>

        <?php if ( $vskew ) : ?>
        <script>
        /* Marquee · VelocitySkew — runtime scoped per istanza (rif. 64-tema-pastificio.html) */
        (function(){
            var root = document.querySelector('.<?php echo esc_js( $uid ); ?>');
            if ( ! root ) { return; }
            var track = root.querySelector('.olo-mq-track');
            if ( ! track ) { return; }
            if ( track.dataset.oloVskew ) { return; }   // idempotente: una sola init per istanza
            track.dataset.oloVskew = '1';

            // prefers-reduced-motion → nessun JS: resta il drift base CSS, skew 0
            var rm = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)');
            if ( rm && rm.matches ) { return; }

            // Il JS prende il controllo: spegne l'animazione CSS e guida transform via rAF
            track.style.animation = 'none';

            var BASE  = <?php echo json_encode( $vs_base ); ?>;
            var BOOST = <?php echo json_encode( $vs_boost ); ?>;
            var MAXSK = <?php echo json_encode( $vs_max ); ?>;
            var DAMP  = <?php echo json_encode( $vs_damp ); ?>;
            var DIR   = <?php echo json_encode( $dir_sign ); ?>;

            var x = 0, vel = 0, lastY = window.scrollY || window.pageYOffset || 0;
            var paused = false, running = false, rafId = null;

            window.addEventListener('scroll', function(){
                var y = window.scrollY || window.pageYOffset || 0;
                vel = y - lastY; lastY = y;
            }, { passive: true });

            <?php if ( $pause ) : ?>
            root.addEventListener('mouseenter', function(){ paused = true; });
            root.addEventListener('mouseleave', function(){ paused = false; });
            <?php endif; ?>

            function frame(){
                if ( ! running ) { return; }
                var half = ( track.scrollWidth / 2 ) || 1;
                if ( ! paused ) {
                    // drift costante + spinta dalla velocità di scroll
                    x += ( DIR * BASE ) + ( -vel * BOOST );
                    if ( x <= -half ) { x += half; }
                    if ( x > 0 )      { x -= half; }
                }
                // inclinazione proporzionale alla velocità, clampata
                var sk = vel * BOOST;
                if ( sk >  MAXSK ) { sk =  MAXSK; }
                if ( sk < -MAXSK ) { sk = -MAXSK; }
                track.style.transform = 'translateX(' + x + 'px) skewX(' + sk + 'deg)';
                vel *= DAMP;   // smorzamento → lo skew torna a 0 da fermo
                rafId = requestAnimationFrame( frame );
            }
            function start(){ if ( ! running ) { running = true; rafId = requestAnimationFrame( frame ); } }
            function stop(){ running = false; if ( rafId ) { cancelAnimationFrame( rafId ); rafId = null; } }

            // Performance: gira solo quando il nastro è nel viewport
            if ( 'IntersectionObserver' in window ) {
                var io = new IntersectionObserver(function( entries ){
                    for ( var i = 0; i < entries.length; i++ ) {
                        if ( entries[i].isIntersecting ) { start(); } else { stop(); }
                    }
                }, { threshold: 0 });
                io.observe( root );
            } else {
                start();
            }
        })();
        </script>
        <?php endif; ?>

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
}
