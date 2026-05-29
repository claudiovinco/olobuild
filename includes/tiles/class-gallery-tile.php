<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Gallery_Tile extends Olo_Tile_Base {

    protected $type     = 'gallery';
    protected $name     = 'Galleria';
    protected $icon     = 'dashicons-format-gallery';
    protected $category = 'media';
    protected $defaults = [
        'preset' => 'custom',
        'images'              => [],
        'layout'              => 'grid',
        'filter_bar'          => false,
        'random_order'        => false,
        'columns'             => 3,
        'rows'                => 0,
        'gap'                 => 8,
        'img_height'          => '200px',
        'object_fit'          => 'cover',
        'thumb_radius'        => 8,
        'lightbox_animation'  => 'slide',
        'show_caption'        => false,
        // Effects — automatic
        'fx_kenburns'         => false,
        'fx_kenburns_speed'   => 20,
        'fx_kenburns_scale'   => 1.12,
        // Effects — hover
        'fx_hover_zoom'       => true,
        'fx_hover_zoom_scale' => 1.08,
        'fx_hover_tilt'       => false,
        'fx_hover_tilt_angle' => 8,
        // Effects — visual filters
        'fx_vignette'          => false,
        'fx_vignette_strength' => 40,
        'fx_grain'             => false,
        'fx_grain_opacity'     => 6,
        'fx_tint'              => false,
        'fx_tint_color'        => '#1E3A5F',
        'fx_tint_opacity'      => 10,
        'fx_tint_blend'        => 'multiply',
        // "+N" overlay
        'more_bg'       => 'rgba(0,0,0,0.55)',
        'more_color'    => '#FFFFFF',
        'more_size'     => 28,
        // Mobile
        'mobile_columns' => 2,
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
        $images = is_array( $s['images'] ) ? $s['images'] : [];

        if ( empty( $images ) ) {
            return '<div style="padding:40px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF)">Aggiungi immagini alla galleria</div>';
        }

        // Random order
        if ( ! empty( $s['random_order'] ) ) {
            shuffle( $images );
        }

        $layout   = in_array( $s['layout'], [ 'grid', 'masonry', 'justified' ], true ) ? $s['layout'] : 'grid';
        $cols     = max( 2, min( 12, absint( $s['columns'] ) ) );
        $rows     = absint( $s['rows'] );
        $gap      = absint( $s['gap'] );
        $radius   = Olo_Tile_Utils::border_radius( $s['thumb_radius'] ?? 0 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['thumb_radius_hover'] ?? null );
        $uid      = 'olo-gal-' . wp_rand( 10000, 99999 );
        $mob_cols = max( 1, min( 4, absint( $s['mobile_columns'] ) ) );

        $total       = count( $images );
        $max_visible = ( $rows > 0 ) ? $cols * $rows : $total;
        $extra       = max( 0, $total - $max_visible );

        $img_height  = esc_attr( $s['img_height'] ?: '200px' );
        $object_fit  = esc_attr( $s['object_fit'] ?: 'cover' );
        $lb_anim     = esc_attr( $s['lightbox_animation'] ?? 'slide' );
        $show_caption = ! empty( $s['show_caption'] );

        // Effects
        $kb_speed  = max( 10, absint( $s['fx_kenburns_speed'] ) );
        $kb_scale  = max( 1.05, min( 1.3, floatval( $s['fx_kenburns_scale'] ) ) );
        $hz_scale  = max( 1.02, min( 1.2, floatval( $s['fx_hover_zoom_scale'] ) ) );
        $tilt_ang  = max( 3, min( 15, absint( $s['fx_hover_tilt_angle'] ) ) );
        $vig_str   = max( 15, min( 60, absint( $s['fx_vignette_strength'] ) ) );
        $grain_opa = max( 3, min( 20, absint( $s['fx_grain_opacity'] ) ) );
        $tint_opa  = max( 5, min( 50, absint( $s['fx_tint_opacity'] ) ) );
        $more_size = max( 16, min( 48, absint( $s['more_size'] ) ) );

        ob_start();
        ?>
        <style>
            <?php if ( $layout === 'masonry' ) : ?>
            .<?php echo $uid; ?> {
                column-count: <?php echo $cols; ?>;
                column-gap: <?php echo $gap; ?>px;
            }
            .<?php echo $uid; ?> .olo-gal-item {
                position: relative;
                display: block;
                border-radius: <?php echo $radius; ?>;
                overflow: hidden;
                cursor: pointer;
                break-inside: avoid;
                margin-bottom: <?php echo $gap; ?>px;
            }
            <?php if ( $radius_hover_css !== '' ) : ?>.<?php echo $uid; ?> .olo-gal-item{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?> .olo-gal-item:hover{border-radius:<?php echo $radius_hover_css; ?> !important}<?php endif; ?>
            .<?php echo $uid; ?> .olo-gal-item img {
                width: 100%;
                height: auto;
                object-fit: <?php echo $object_fit; ?>;
                display: block;
                transition: transform 0.5s cubic-bezier(.25,.46,.45,.94), filter 0.5s ease;
                will-change: transform;
            }
            <?php elseif ( $layout === 'justified' ) : ?>
            .<?php echo $uid; ?> {
                display: flex;
                flex-wrap: wrap;
                gap: <?php echo $gap; ?>px;
            }
            .<?php echo $uid; ?> .olo-gal-item {
                position: relative;
                display: block;
                border-radius: <?php echo $radius; ?>;
                overflow: hidden;
                height: <?php echo $img_height; ?>;
                cursor: pointer;
                flex-grow: 1;
                min-width: 120px;
            }
            .<?php echo $uid; ?> .olo-gal-item img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
                transition: transform 0.5s cubic-bezier(.25,.46,.45,.94), filter 0.5s ease;
                will-change: transform;
            }
            <?php else : ?>
            .<?php echo $uid; ?> {
                display: grid;
                grid-template-columns: repeat(<?php echo $cols; ?>, 1fr);
                gap: <?php echo $gap; ?>px;
            }
            .<?php echo $uid; ?> .olo-gal-item {
                position: relative;
                display: block;
                border-radius: <?php echo $radius; ?>;
                overflow: hidden;
                height: <?php echo $img_height; ?>;
                cursor: pointer;
            }
            .<?php echo $uid; ?> .olo-gal-item img {
                width: 100%;
                height: 100%;
                object-fit: <?php echo $object_fit; ?>;
                display: block;
                transition: transform 0.5s cubic-bezier(.25,.46,.45,.94), filter 0.5s ease;
                will-change: transform;
            }
            <?php endif; ?>

            /* Filter bar */
            .<?php echo $uid; ?>-filter {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
                margin-bottom: 12px;
            }
            .<?php echo $uid; ?>-filter button {
                padding: 4px 14px;
                border: 1px solid var(--olo-color-border, #E5E7EB);
                border-radius: 4px;
                background: transparent;
                font-size: 13px;
                cursor: pointer;
                transition: background 0.2s, color 0.2s;
                color: var(--olo-color-text, #374151);
            }
            .<?php echo $uid; ?>-filter button:hover,
            .<?php echo $uid; ?>-filter button.active {
                background: var(--olo-color-secondary, #1F2937);
                color: var(--olo-color-secondary-contrast, #FFFFFF);
                border-color: var(--olo-color-secondary, #1F2937);
            }

            /* Ken Burns */
            <?php if ( ! empty( $s['fx_kenburns'] ) ) : ?>
            @keyframes <?php echo $uid; ?>-kb {
                0%   { transform: scale(1) translate(0,0); }
                33%  { transform: scale(<?php echo $kb_scale; ?>) translate(-1.5%,-1%); }
                66%  { transform: scale(<?php echo $kb_scale - 0.03; ?>) translate(1%,0.5%); }
                100% { transform: scale(1) translate(0,0); }
            }
            .<?php echo $uid; ?> .olo-gal-item img {
                animation: <?php echo $uid; ?>-kb <?php echo $kb_speed; ?>s ease-in-out infinite;
            }
            .<?php echo $uid; ?> .olo-gal-item:nth-child(2n) img { animation-delay: -<?php echo round( $kb_speed / 3 ); ?>s; }
            .<?php echo $uid; ?> .olo-gal-item:nth-child(3n) img { animation-delay: -<?php echo round( $kb_speed * 2 / 3 ); ?>s; }
            <?php endif; ?>

            /* Hover zoom — applied to container (no tilt) */
            <?php if ( ! empty( $s['fx_hover_zoom'] ) && empty( $s['fx_hover_tilt'] ) ) : ?>
            .<?php echo $uid; ?> .olo-gal-item {
                transition: transform 0.4s ease, box-shadow 0.4s ease;
            }
            .<?php echo $uid; ?> .olo-gal-item:hover {
                transform: scale(<?php echo $hz_scale; ?>);
                box-shadow: 0 8px 25px rgba(0,0,0,0.3);
                z-index: 2;
            }
            <?php endif; ?>

            /* Tilt 3D (JS-driven) — base styles */
            <?php if ( ! empty( $s['fx_hover_tilt'] ) ) : ?>
            .<?php echo $uid; ?> .olo-gal-item {
                transform-style: preserve-3d;
                will-change: transform;
                transition: transform 0.4s ease, box-shadow 0.4s ease;
            }
            <?php endif; ?>

            /* Vignette */
            <?php if ( ! empty( $s['fx_vignette'] ) ) : ?>
            .<?php echo $uid; ?> .olo-gal-item::after {
                content: '';
                position: absolute;
                inset: 0;
                border-radius: <?php echo $radius; ?>;
                box-shadow: inset 0 0 <?php echo $vig_str * 2; ?>px <?php echo $vig_str; ?>px rgba(0,0,0,0.<?php echo min( 45, $vig_str ); ?>);
                pointer-events: none;
                z-index: 2;
            }
            <?php endif; ?>

            /* Grain */
            <?php if ( ! empty( $s['fx_grain'] ) ) : ?>
            .<?php echo $uid; ?> .olo-gal-grain {
                position: absolute;
                inset: 0;
                z-index: 2;
                opacity: 0.<?php echo str_pad( $grain_opa, 2, '0', STR_PAD_LEFT ); ?>;
                pointer-events: none;
                background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
                background-repeat: repeat;
                background-size: 128px 128px;
                mix-blend-mode: overlay;
            }
            <?php endif; ?>

            /* Tint */
            <?php if ( ! empty( $s['fx_tint'] ) ) : ?>
            .<?php echo $uid; ?> .olo-gal-tint {
                position: absolute;
                inset: 0;
                z-index: 1;
                background: <?php echo $this->safe_color_css( $s['fx_tint_color'] ); ?>;
                opacity: 0.<?php echo str_pad( $tint_opa, 2, '0', STR_PAD_LEFT ); ?>;
                mix-blend-mode: <?php echo esc_attr( $s['fx_tint_blend'] ); ?>;
                pointer-events: none;
                transition: opacity 0.4s ease;
            }
            .<?php echo $uid; ?> .olo-gal-item:hover .olo-gal-tint {
                opacity: 0.<?php echo str_pad( max( 2, $tint_opa - 5 ), 2, '0', STR_PAD_LEFT ); ?>;
            }
            <?php endif; ?>

            /* "+N" overlay */
            .<?php echo $uid; ?> .olo-gal-more {
                position: absolute;
                inset: 0;
                z-index: 5;
                display: flex;
                align-items: center;
                justify-content: center;
                background: <?php echo $this->safe_color_css( $s['more_bg'] ); ?>;
                color: <?php echo $this->safe_color_css( $s['more_color'] ); ?>;
                font-size: <?php echo $more_size; ?>px;
                font-weight: 700;
                letter-spacing: -0.5px;
                pointer-events: none;
                transition: background 0.3s ease;
                border-radius: <?php echo $radius; ?>;
            }
            .<?php echo $uid; ?> .olo-gal-item:hover .olo-gal-more {
                background: rgba(0,0,0,0.4);
            }

            /* Hidden lightbox items */
            .<?php echo $uid; ?> .olo-gal-hidden {
                position: absolute;
                width: 0;
                height: 0;
                overflow: hidden;
                pointer-events: none;
                opacity: 0;
            }

            /* Mobile */
            @media (max-width: 640px) {
                <?php if ( $layout === 'masonry' ) : ?>
                .<?php echo $uid; ?> {
                    column-count: <?php echo $mob_cols; ?>;
                }
                <?php elseif ( $layout === 'justified' ) : ?>
                .<?php echo $uid; ?> .olo-gal-item {
                    min-width: 80px;
                    height: <?php echo intval( $img_height ) > 0 ? max( 100, intval( $img_height ) - 60 ) . 'px' : '140px'; ?>;
                }
                <?php else : ?>
                .<?php echo $uid; ?> {
                    grid-template-columns: repeat(<?php echo $mob_cols; ?>, 1fr);
                }
                <?php endif; ?>
            }
        </style>
        <?php
        // Build categories from image alt text for filter bar
        $categories = [];
        if ( ! empty( $s['filter_bar'] ) ) {
            foreach ( $images as $img ) {
                $cat = is_array( $img ) ? trim( $img['category'] ?? $img['alt'] ?? '' ) : '';
                if ( $cat !== '' ) {
                    $categories[ $cat ] = true;
                }
            }
            $categories = array_keys( $categories );
        }
        ?>
        <?php if ( ! empty( $s['filter_bar'] ) ) : ?>
        <div class="<?php echo esc_attr( $uid ); ?>-filter" id="<?php echo esc_attr( $uid ); ?>-filter">
            <button class="active" data-filter="*"><?php echo esc_html( olo_t( 'Tutti' ) ); ?></button>
            <?php foreach ( $categories as $cat ) : ?>
            <button data-filter="<?php echo esc_attr( sanitize_title( $cat ) ); ?>"><?php echo esc_html( $cat ); ?></button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <div class="<?php echo esc_attr( $uid ); ?> olo-gallery-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" id="<?php echo esc_attr( $uid ); ?>" uk-lightbox="animation: <?php echo $lb_anim; ?>">
            <?php
            $i = 0;
            foreach ( $images as $img ) :
                $url     = is_array( $img ) ? ( $img['url'] ?? '' ) : $img;
                $alt     = is_array( $img ) ? ( $img['alt'] ?? '' ) : '';
                $caption = is_array( $img ) ? ( $img['caption'] ?? '' ) : '';
                $att_id  = is_array( $img ) ? absint( $img['id'] ?? 0 ) : 0;
                $cat_slug = ! empty( $s['filter_bar'] ) ? sanitize_title( is_array( $img ) ? trim( $img['category'] ?? $img['alt'] ?? '' ) : '' ) : '';
                if ( ! $url ) continue;
                $i++;

                $is_visible  = ( $i <= $max_visible );
                $is_last_vis = ( $i === $max_visible && $extra > 0 );
                $caption_attr = ( $show_caption && ! empty( $caption ) )
                    ? ' data-caption="' . esc_attr( $caption ) . '"'
                    : ( $extra > 0 ? ' data-caption="' . $i . '/' . $total . '"' : '' );
                $cat_attr = $cat_slug ? ' data-category="' . esc_attr( $cat_slug ) . '"' : '';
            ?>
                <?php if ( $is_visible ) : ?>
                <a class="olo-gal-item" href="<?php echo esc_url( $url ); ?>"<?php echo $caption_attr . $cat_attr; ?>>
                    <?php echo Olo_Tile_Utils::img_srcset( $att_id, $url, $alt ); ?>
                    <?php if ( ! empty( $s['fx_tint'] ) ) : ?><div class="olo-gal-tint"></div><?php endif; ?>
                    <?php if ( ! empty( $s['fx_grain'] ) ) : ?><div class="olo-gal-grain"></div><?php endif; ?>
                    <?php if ( $is_last_vis ) : ?>
                        <div class="olo-gal-more">+<?php echo $extra; ?></div>
                    <?php endif; ?>
                </a>
                <?php else : ?>
                <a class="olo-gal-hidden" href="<?php echo esc_url( $url ); ?>"<?php echo $caption_attr . $cat_attr; ?>></a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php if ( ! empty( $s['filter_bar'] ) ) : ?>
        <script>
        (function(){
            var filterBar = document.getElementById('<?php echo $uid; ?>-filter');
            var gallery = document.getElementById('<?php echo $uid; ?>');
            if(!filterBar){return}
            if(!gallery){return}
            var buttons = filterBar.querySelectorAll('button');
            buttons.forEach(function(btn){
                btn.addEventListener('click', function(){
                    buttons.forEach(function(b){ b.classList.remove('active'); });
                    btn.classList.add('active');
                    var f = btn.getAttribute('data-filter');
                    var items = gallery.querySelectorAll('.olo-gal-item');
                    items.forEach(function(item){
                        if(f === '*'){
                            item.style.display = '';
                        } else {
                            var cat = item.getAttribute('data-category');
                            if(cat === f){
                                item.style.display = '';
                            } else {
                                item.style.display = 'none';
                            }
                        }
                    });
                });
            });
        })();
        </script>
        <?php endif; ?>
        <?php if ( ! empty( $s['fx_hover_tilt'] ) ) : ?>
        <script>
        (function(){
            var gallery = document.getElementById('<?php echo $uid; ?>');
            if(!gallery){return}
            var doZoom = <?php echo ! empty( $s['fx_hover_zoom'] ) ? 'true' : 'false'; ?>;
            var zoomScale = <?php echo $hz_scale; ?>;
            var items = gallery.querySelectorAll('.olo-gal-item');
            items.forEach(function(el){
                el.addEventListener('mouseenter', function(){
                    el.style.transition = 'transform 0.15s ease, box-shadow 0.15s ease';
                });
                el.addEventListener('mouseleave', function(){
                    el.style.transition = 'transform 0.4s ease, box-shadow 0.4s ease';
                    el.style.transform = '';
                    el.style.boxShadow = '';
                    el.style.zIndex = '';
                });
                el.addEventListener('mousemove', function(e){
                    var rect = el.getBoundingClientRect();
                    var x = (e.clientX - rect.left) / rect.width - 0.5;
                    var y = (e.clientY - rect.top) / rect.height - 0.5;
                    var rotY = x * 20;
                    var rotX = -y * 20;
                    var t = 'perspective(600px) rotateX(' + rotX + 'deg) rotateY(' + rotY + 'deg)';
                    if(doZoom){ t += ' scale(' + zoomScale + ')'; }
                    el.style.transform = t;
                    el.style.boxShadow = '0 8px 25px rgba(0,0,0,0.3)';
                    el.style.zIndex = '2';
                });
            });
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
