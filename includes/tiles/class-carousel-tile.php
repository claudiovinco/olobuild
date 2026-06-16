<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Carousel_Tile extends Olo_Tile_Base {

    protected $type     = 'carousel';
    protected $name     = 'Carosello immagini';
    protected $icon     = 'dashicons-format-image';
    protected $category = 'media';
    protected $defaults = [
        'slides'           => [],
        'slides_to_show'   => '3',
        'gap'              => '16',
        'autoplay'         => false,
        'autoplay_speed'   => '4000',
        'show_arrows'      => true,
        'show_dots'        => true,
        'loop'             => true,
        'pause_on_hover'   => true,
        'slide_height'     => 'auto',
        'fixed_height'     => '300',
        'border_radius'    => '8',
        'arrow_color'      => '#FFFFFF',
        'arrow_bg'         => 'rgba(0,0,0,0.5)',
        'dot_color'        => '',
        'dot_inactive_color' => '',
        'show_caption'     => false,
        'caption_color'    => '#FFFFFF',
        'caption_bg'       => 'rgba(0,0,0,0.6)',
        'object_fit'       => 'cover',
        'object_position'  => 'center center',
        'mobile_slides'           => '1',
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
        $s = wp_parse_args( $settings, $this->defaults );

        $slides = is_array( $s['slides'] ) ? $s['slides'] : [];
        if ( empty( $slides ) ) {
            return '<div class="olo-carousel" style="padding:40px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF)">' . olo_t( 'Aggiungi immagini al carosello' ) . '</div>';
        }

        $uid        = 'olo-car-' . wp_rand( 10000, 99999 );
        $total      = count( $slides );
        $show       = max( 1, min( 6, absint( $s['slides_to_show'] ) ) );
        $mob_show   = max( 1, min( 3, absint( $s['mobile_slides'] ) ) );
        $gap        = absint( $s['gap'] );
        $radius     = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );
        $autoplay   = filter_var( $s['autoplay'], FILTER_VALIDATE_BOOLEAN );
        $speed      = max( 1000, absint( $s['autoplay_speed'] ) );
        $loop       = filter_var( $s['loop'], FILTER_VALIDATE_BOOLEAN );
        $pause      = filter_var( $s['pause_on_hover'], FILTER_VALIDATE_BOOLEAN );
        $arrows     = filter_var( $s['show_arrows'], FILTER_VALIDATE_BOOLEAN );
        $dots       = filter_var( $s['show_dots'], FILTER_VALIDATE_BOOLEAN );
        $captions   = filter_var( $s['show_caption'] ?? false, FILTER_VALIDATE_BOOLEAN );
        $obj_fit    = in_array( $s['object_fit'], [ 'cover', 'contain' ], true ) ? $s['object_fit'] : 'cover';
        $obj_pos    = trim( (string) ( $s['object_position'] ?? 'center center' ) );
        if ( $obj_pos === '' || ! preg_match( '/^[a-z0-9 %.\-]+$/i', $obj_pos ) ) {
            $obj_pos = 'center center';
        }

        $arrow_col  = $this->safe_color_css( $s['arrow_color'] ) ?: '#FFFFFF';
        $arrow_bg   = $this->safe_color_css( $s['arrow_bg'] ) ?: 'rgba(0,0,0,0.5)';
        $dot_col    = $this->safe_color_css( $s['dot_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $dot_inact  = $this->safe_color_css( $s['dot_inactive_color'] ) ?: 'var(--olo-color-border, #E5E7EB)';
        $cap_col    = $this->safe_color_css( $s['caption_color'] ) ?: '#FFFFFF';
        $cap_bg     = $this->safe_color_css( $s['caption_bg'] ) ?: 'rgba(0,0,0,0.6)';

        $height_mode = $s['slide_height'] === 'fixed' ? 'fixed' : 'auto';
        $fixed_h     = max( 150, absint( $s['fixed_height'] ) );

        $dot_count = (int) ceil( $total / $show );

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: colors via the safe_color_css() whitelist, integers via absint() with min()/max() clamps, radius via Olo_Tile_Utils border_radius()/radius_force_css(), object-fit via in_array() whitelist, object-position via strict regex whitelist + esc_attr(); $uid is internally generated.
        ?>
        <style>
            .<?php echo $uid; ?> {
                position: relative;
                overflow: hidden;
            }
            .<?php echo $uid; ?>-track {
                display: flex;
                gap: <?php echo $gap; ?>px;
                transition: transform 0.5s cubic-bezier(.25,.46,.45,.94);
                will-change: transform;
            }
            .<?php echo $uid; ?>-track.olo-car-grabbing {
                cursor: grabbing;
                transition: none;
            }
            .<?php echo $uid; ?> .olo-car-slide {
                flex: 0 0 calc((100% - <?php echo $gap * ( $show - 1 ); ?>px) / <?php echo $show; ?>);
                min-width: 0;
                position: relative;
                border-radius: <?php echo $radius; ?>;
                overflow: hidden;
            }
            <?php if ( $radius_hover_css !== '' ) : ?>.<?php echo $uid; ?> .olo-car-slide{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?> .olo-car-slide:hover{border-radius:<?php echo $radius_hover_css; ?> !important}<?php endif; ?>
            .<?php echo $uid; ?> .olo-car-slide img {
                width: 100%;
                display: block;
                object-fit: <?php echo $obj_fit; ?>;
                object-position: <?php echo esc_attr( $obj_pos ); ?>;
                border-radius: <?php echo $radius; ?>;
                <?php if ( $height_mode === 'fixed' ) : ?>
                height: <?php echo $fixed_h; ?>px;
                <?php else : ?>
                height: auto;
                aspect-ratio: 16 / 10;
                <?php endif; ?>
            }
            <?php if ( $captions ) : ?>
            .<?php echo $uid; ?> .olo-car-caption {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                padding: 6px 10px;
                font-size: 13px;
                color: <?php echo $cap_col; ?>;
                background: <?php echo $cap_bg; ?>;
                border-radius: <?php echo $radius; ?>; border-top-left-radius: 0; border-top-right-radius: 0;
            }
            <?php endif; ?>
            <?php if ( $arrows ) : ?>
            .<?php echo $uid; ?> .olo-car-arrow {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                width: 36px;
                height: 36px;
                border-radius: 50%;
                border: none;
                background: <?php echo $arrow_bg; ?>;
                color: <?php echo $arrow_col; ?>;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 2;
                transition: opacity 0.2s;
                padding: 0;
            }
            .<?php echo $uid; ?> .olo-car-arrow:hover {
                opacity: 0.85;
            }
            .<?php echo $uid; ?> .olo-car-prev { left: 10px; }
            .<?php echo $uid; ?> .olo-car-next { right: 10px; }
            <?php endif; ?>
            <?php if ( $dots ) : ?>
            .<?php echo $uid; ?>-dots {
                display: flex;
                justify-content: center;
                gap: 8px;
                margin-top: 12px;
            }
            .<?php echo $uid; ?>-dots .olo-car-dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                border: none;
                background: <?php echo $dot_inact; ?>;
                cursor: pointer;
                padding: 0;
                transition: background 0.2s;
            }
            .<?php echo $uid; ?>-dots .olo-car-dot.active {
                background: <?php echo $dot_col; ?>;
            }
            <?php endif; ?>
            @media (max-width: 640px) {
                .<?php echo $uid; ?> .olo-car-slide {
                    flex: 0 0 calc((100% - <?php echo $gap * ( $mob_show - 1 ); ?>px) / <?php echo $mob_show; ?>);
                }
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-carousel <?php echo esc_attr( $uid ); ?>" id="<?php echo esc_attr( $uid ); ?>" data-slides="<?php echo (int) $show; ?>" data-total="<?php echo (int) $total; ?>" data-gap="<?php echo (int) $gap; ?>">
            <div class="<?php echo esc_attr( $uid ); ?>-track olo-car-track" id="<?php echo esc_attr( $uid ); ?>-track">
                <?php foreach ( $slides as $slide ) :
                    $url = is_array( $slide ) ? ( $slide['image_url'] ?? '' ) : '';
                    $alt = is_array( $slide ) ? ( $slide['image_alt'] ?? '' ) : '';
                    $link = is_array( $slide ) ? ( $slide['link_url'] ?? '' ) : '';
                    $caption = is_array( $slide ) ? ( $slide['caption'] ?? '' ) : '';
                    $widget_id = is_array( $slide ) ? absint( $slide['widget_template_id'] ?? 0 ) : 0;
                    // Skip slide solo se NÉ image NÉ widget sono presenti
                    if ( ! $url && ! $widget_id ) continue;
                    $widget_html = $this->render_widget_template( $widget_id );
                ?>
                <div class="olo-car-slide">
                    <?php if ( $widget_html ) : ?>
                    <div class="olo-item-widget"><?php echo $widget_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- widget HTML rendered by Olo_Tile_Base::render_widget_template() through the frontend renderer (each tile escapes its own output) ?></div>
                    <?php endif; ?>
                    <?php if ( $url ) : ?>
                        <?php if ( $link ) : ?>
                        <a href="<?php echo esc_url( $link ); ?>">
                            <img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" />
                        </a>
                        <?php else : ?>
                        <img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" />
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ( $captions && $caption ) : ?>
                    <?php list( $cc_cls, $cc_data ) = $this->tfx_attrs( $s, 'caption', $caption ); ?>
                    <div class="olo-car-caption<?php echo $cc_cls; ?>"<?php echo $cc_data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); caption escaped inline ?>><?php echo esc_html( $caption ); ?></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if ( $arrows && $total > $show ) : ?>
            <button class="olo-car-arrow olo-car-prev" data-dir="prev" aria-label="<?php echo esc_attr( olo_t( 'Precedente' ) ); ?>">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M10 3L5 8l5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button class="olo-car-arrow olo-car-next" data-dir="next" aria-label="<?php echo esc_attr( olo_t( 'Successivo' ) ); ?>">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <?php endif; ?>
        </div>

        <?php if ( $dots && $total > $show ) : ?>
        <div class="<?php echo esc_attr( $uid ); ?>-dots" id="<?php echo esc_attr( $uid ); ?>-dots">
            <?php for ( $d = 0; $d < $dot_count; $d++ ) : ?>
            <button class="olo-car-dot<?php echo $d === 0 ? ' active' : ''; ?>" data-index="<?php echo (int) $d; ?>" aria-label="<?php echo esc_attr( olo_t( 'Vai a gruppo' ) . ' ' . ( $d + 1 ) ); ?>"></button>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

        <script>
        (function(){
            var root = document.getElementById('<?php echo esc_js( $uid ); ?>');
            if(!root){return}
            var track = document.getElementById('<?php echo esc_js( $uid ); ?>-track');
            if(!track){return}
            var dotsWrap = document.getElementById('<?php echo esc_js( $uid ); ?>-dots');
            var slides = track.querySelectorAll('.olo-car-slide');
            var total = slides.length;
            var show = <?php echo (int) $show; ?>;
            var gap = <?php echo (int) $gap; ?>;
            var current = 0;
            var maxIndex = Math.max(0, total - show);
            var loop = <?php echo $loop ? 'true' : 'false'; ?>;
            var autoplay = <?php echo $autoplay ? 'true' : 'false'; ?>;
            var speed = <?php echo (int) $speed; ?>;
            var pauseOnHover = <?php echo $pause ? 'true' : 'false'; ?>;
            var timer = null;

            function getSlideWidth(){
                if(total < 1){return 0}
                var w = root.offsetWidth;
                return (w - gap * (show - 1)) / show;
            }

            function goTo(idx){
                if(loop){
                    if(idx > maxIndex){idx = 0}
                    if(idx < 0){idx = maxIndex}
                } else {
                    if(idx > maxIndex){idx = maxIndex}
                    if(idx < 0){idx = 0}
                }
                current = idx;
                var sw = getSlideWidth();
                var offset = current * (sw + gap);
                track.style.transform = 'translateX(-' + offset + 'px)';
                updateDots();
            }

            function updateDots(){
                if(!dotsWrap){return}
                var dots = dotsWrap.querySelectorAll('.olo-car-dot');
                var dotIdx = Math.floor(current / show);
                for(var i = 0; i < dots.length; i++){
                    if(i === dotIdx){
                        dots[i].classList.add('active');
                    } else {
                        dots[i].classList.remove('active');
                    }
                }
            }

            function startAuto(){
                if(!autoplay){return}
                if(total <= show){return}
                timer = setInterval(function(){ goTo(current + 1); }, speed);
            }

            function stopAuto(){
                if(timer){ clearInterval(timer); timer = null; }
            }

            /* Event delegation for arrows */
            root.addEventListener('click', function(e){
                var btn = e.target.closest('.olo-car-arrow');
                if(!btn){return}
                e.preventDefault();
                var dir = btn.getAttribute('data-dir');
                if(dir === 'prev'){ goTo(current - 1); }
                if(dir === 'next'){ goTo(current + 1); }
                stopAuto();
                startAuto();
            });

            /* Event delegation for dots */
            if(dotsWrap){
                dotsWrap.addEventListener('click', function(e){
                    var dot = e.target.closest('.olo-car-dot');
                    if(!dot){return}
                    e.preventDefault();
                    var idx = parseInt(dot.getAttribute('data-index'));
                    if(!isNaN(idx)){ goTo(idx * show); }
                    stopAuto();
                    startAuto();
                });
            }

            /* Pause on hover */
            if(pauseOnHover){
                root.addEventListener('mouseenter', function(){ stopAuto(); });
                root.addEventListener('mouseleave', function(){ startAuto(); });
            }

            /* Touch / swipe support */
            var startX = 0;
            var isDragging = false;
            track.addEventListener('touchstart', function(e){
                startX = e.touches[0].clientX;
                isDragging = true;
                stopAuto();
            }, {passive: true});
            track.addEventListener('touchend', function(e){
                if(!isDragging){return}
                isDragging = false;
                var diff = startX - e.changedTouches[0].clientX;
                if(Math.abs(diff) > 40){
                    if(diff > 0){ goTo(current + 1); }
                    else { goTo(current - 1); }
                }
                startAuto();
            }, {passive: true});

            /* Responsive: recalculate on resize */
            var resizeTimeout;
            window.addEventListener('resize', function(){
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(function(){ goTo(current); }, 150);
            });

            startAuto();
        })();
        </script>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Text_Effects::css() from fixed effect definitions
        $this->tfx_print_script();

        // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_css() from sanitized border settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base border helpers from sanitized border settings
        }

        return ob_get_clean();
    }
}
