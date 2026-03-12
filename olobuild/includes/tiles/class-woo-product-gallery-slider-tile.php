<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Woo_Product_Gallery_Slider_Tile extends Olo_Tile_Base {

    protected $type     = 'woo_product_gallery_slider';
    protected $name     = 'Gallery Prodotto WC';
    protected $icon     = 'dashicons-format-gallery';
    protected $category = 'woocommerce';
    protected $defaults = [
        'show_thumbnails'        => true,
        'thumbnail_position'     => 'bottom',
        'enable_zoom'            => true,
        'enable_lightbox'        => true,
        'main_height'            => '500px',
        'thumbnail_size'         => 72,
        'thumbnail_gap'          => 8,
        'arrows'                 => true,
        'border_radius'          => 8,
        'main_bg'                => '#F9FAFB',
        'thumbnail_border'       => '#E5E7EB',
        'thumbnail_active_border' => '#6366F1',
        'arrow_color'            => '',
        'arrow_bg'               => 'rgba(255,255,255,0.9)',
        'show_badge'             => true,
        'badge_bg'               => '#EF4444',
        'max_width'              => 600,
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return '<div style="padding:40px;text-align:center;color:#92400E;background:#FEF3C7;border:1px solid #F59E0B;border-radius:8px;">'
                 . esc_html( olo_t( 'WooCommerce non attivo. Installa e attiva WooCommerce per utilizzare questo elemento.' ) )
                 . '</div>';
        }

        $s = wp_parse_args( $settings, $this->defaults );

        // Get product
        global $product;
        if ( ! is_a( $product, 'WC_Product' ) ) {
            $product = wc_get_product( get_the_ID() );
        }
        if ( ! $product ) {
            return '<div style="padding:20px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);font-size:14px;">'
                 . esc_html( olo_t( 'Nessun prodotto disponibile in questo contesto' ) )
                 . '</div>';
        }

        // Collect gallery images: featured first, then gallery
        $image_ids = [];
        $featured_id = $product->get_image_id();
        if ( $featured_id ) {
            $image_ids[] = $featured_id;
        }
        $gallery_ids = $product->get_gallery_image_ids();
        if ( ! empty( $gallery_ids ) ) {
            $image_ids = array_merge( $image_ids, $gallery_ids );
        }

        if ( empty( $image_ids ) ) {
            return '<div style="padding:40px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);font-size:14px;background:var(--olo-color-muted, #F3F4F6);border-radius:8px;">'
                 . '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--olo-color-border, #E5E7EB)" stroke-width="1.5" style="margin:0 auto 12px;display:block"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>'
                 . esc_html( olo_t( 'Nessuna immagine nella gallery del prodotto' ) )
                 . '</div>';
        }

        $uid = 'olo-woo-gs-' . wp_rand( 10000, 99999 );

        // Settings
        $main_h      = sanitize_text_field( $s['main_height'] );
        $thumb_size  = max( 40, min( 120, absint( $s['thumbnail_size'] ) ) );
        $thumb_gap   = max( 2, min( 20, absint( $s['thumbnail_gap'] ) ) );
        $radius      = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $radius_raw  = absint( $s['border_radius'] ?? 0 );
        $max_width   = max( 200, min( 1200, absint( $s['max_width'] ) ) );
        $show_thumbs = ! empty( $s['show_thumbnails'] );
        $thumb_pos   = in_array( $s['thumbnail_position'], [ 'bottom', 'left' ], true ) ? $s['thumbnail_position'] : 'bottom';
        $enable_zoom = ! empty( $s['enable_zoom'] );
        $enable_lb   = ! empty( $s['enable_lightbox'] );
        $show_arrows = ! empty( $s['arrows'] );
        $show_badge  = ! empty( $s['show_badge'] );
        $is_left     = ( $thumb_pos === 'left' );
        $on_sale     = $product->is_on_sale();

        // Colors
        $main_bg   = $this->safe_color_css( $s['main_bg'] ) ?: '#F9FAFB';
        $tb        = $this->safe_color_css( $s['thumbnail_border'] ) ?: '#E5E7EB';
        $tab       = $this->safe_color_css( $s['thumbnail_active_border'] ) ?: '#6366F1';
        $arrow_c   = $this->safe_color_css( $s['arrow_color'] ) ?: 'var(--olo-color-text, #374151)';
        $arrow_bg  = $s['arrow_bg'] ?: 'rgba(255,255,255,0.9)';
        $badge_bg  = $this->safe_color_css( $s['badge_bg'] ) ?: '#EF4444';

        // Build image data for template and JS
        $images = [];
        foreach ( $image_ids as $img_id ) {
            $full  = wp_get_attachment_image_url( $img_id, 'full' );
            $large = wp_get_attachment_image_url( $img_id, 'large' );
            $thumb = wp_get_attachment_image_url( $img_id, 'thumbnail' );
            $alt   = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
            if ( $large ) {
                $images[] = [
                    'full'  => $full ?: $large,
                    'large' => $large,
                    'thumb' => $thumb ?: $large,
                    'alt'   => $alt ?: $product->get_name(),
                ];
            }
        }

        if ( empty( $images ) ) {
            return '';
        }

        $total = count( $images );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                max-width: <?php echo $max_width; ?>px;
            }
            .<?php echo $uid; ?>-wrap {
                display: flex;
                <?php if ( $is_left ) : ?>
                flex-direction: row;
                <?php else : ?>
                flex-direction: column;
                <?php endif; ?>
                gap: <?php echo $thumb_gap; ?>px;
            }
            .<?php echo $uid; ?>-main {
                position: relative;
                overflow: hidden;
                border-radius: <?php echo $radius; ?>;
                background: <?php echo $main_bg; ?>;
                <?php if ( $is_left ) : ?>
                flex: 1;
                order: 2;
                <?php endif; ?>
            }
            .<?php echo $uid; ?>-track {
                display: flex;
                transition: transform 0.4s ease;
                height: <?php echo $main_h; ?>;
            }
            .<?php echo $uid; ?>-slide {
                min-width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .<?php echo $uid; ?>-slide img {
                max-width: 100%;
                max-height: 100%;
                object-fit: contain;
                transition: transform 0.15s ease-out;
            }
            <?php if ( $enable_zoom ) : ?>
            .<?php echo $uid; ?>-slide img {
                cursor: zoom-in;
            }
            .<?php echo $uid; ?>-main.is-zooming .<?php echo $uid; ?>-slide img {
                transform: scale(2);
                cursor: zoom-out;
            }
            <?php elseif ( $enable_lb ) : ?>
            .<?php echo $uid; ?>-slide img {
                cursor: pointer;
            }
            <?php endif; ?>
            .<?php echo $uid; ?>-badge {
                position: absolute;
                top: 10px;
                left: 10px;
                background: <?php echo $badge_bg; ?>;
                color: #fff;
                font-size: 12px;
                font-weight: 700;
                padding: 4px 10px;
                border-radius: 4px;
                z-index: 3;
                pointer-events: none;
            }
            .<?php echo $uid; ?>-arrow {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                width: 36px;
                height: 36px;
                background: <?php echo $arrow_bg; ?>;
                border: none;
                border-radius: 50%;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 3;
                box-shadow: 0 2px 6px rgba(0,0,0,0.12);
                transition: background 0.2s, opacity 0.2s;
            }
            .<?php echo $uid; ?>-arrow:hover { opacity: 0.85; }
            .<?php echo $uid; ?>-prev { left: 10px; }
            .<?php echo $uid; ?>-next { right: 10px; }
            .<?php echo $uid; ?>-thumbs {
                display: flex;
                gap: <?php echo $thumb_gap; ?>px;
                scrollbar-width: thin;
                <?php if ( $is_left ) : ?>
                flex-direction: column;
                order: 1;
                max-height: <?php echo $main_h; ?>;
                overflow-y: auto;
                overflow-x: hidden;
                <?php else : ?>
                flex-direction: row;
                overflow-x: auto;
                overflow-y: hidden;
                <?php endif; ?>
            }
            .<?php echo $uid; ?>-thumb {
                width: <?php echo $thumb_size; ?>px;
                height: <?php echo $thumb_size; ?>px;
                flex-shrink: 0;
                border-radius: <?php echo max( 2, intval( $radius_raw / 2 ) ); ?>px;
                overflow: hidden;
                cursor: pointer;
                border: 2px solid <?php echo $tb; ?>;
                transition: border-color 0.2s ease, opacity 0.2s ease;
                opacity: 0.65;
            }
            .<?php echo $uid; ?>-thumb:hover { opacity: 1; }
            .<?php echo $uid; ?>-thumb.is-active {
                border-color: <?php echo $tab; ?>;
                opacity: 1;
            }
            .<?php echo $uid; ?>-thumb img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }
            /* Lightbox */
            .<?php echo $uid; ?>-lb {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.92);
                z-index: 999999;
                justify-content: center;
                align-items: center;
                padding: 40px;
            }
            .<?php echo $uid; ?>-lb.is-open { display: flex; }
            .<?php echo $uid; ?>-lb img {
                max-width: 92vw;
                max-height: 88vh;
                object-fit: contain;
                border-radius: 4px;
            }
            .<?php echo $uid; ?>-lb-close {
                position: absolute;
                top: 16px;
                right: 16px;
                width: 40px;
                height: 40px;
                background: rgba(255,255,255,0.15);
                border: none;
                border-radius: 50%;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: background 0.2s;
            }
            .<?php echo $uid; ?>-lb-close:hover { background: rgba(255,255,255,0.3); }
            .<?php echo $uid; ?>-lb-arrow {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                width: 44px;
                height: 44px;
                background: rgba(255,255,255,0.15);
                border: none;
                border-radius: 50%;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: background 0.2s;
            }
            .<?php echo $uid; ?>-lb-arrow:hover { background: rgba(255,255,255,0.3); }
            .<?php echo $uid; ?>-lb-prev { left: 16px; }
            .<?php echo $uid; ?>-lb-next { right: 16px; }
            .<?php echo $uid; ?>-lb-counter {
                position: absolute;
                bottom: 16px;
                left: 50%;
                transform: translateX(-50%);
                color: rgba(255,255,255,0.7);
                font-size: 13px;
            }
            @media (max-width: 640px) {
                .<?php echo $uid; ?>-wrap {
                    flex-direction: column !important;
                }
                .<?php echo $uid; ?>-thumbs {
                    flex-direction: row !important;
                    overflow-x: auto !important;
                    overflow-y: visible !important;
                    max-height: none !important;
                    order: 2 !important;
                }
                .<?php echo $uid; ?>-main {
                    order: 1 !important;
                }
                .<?php echo $uid; ?>-track {
                    height: 300px;
                }
            }
        </style>

        <div class="<?php echo esc_attr( $uid ); ?>">
            <div class="<?php echo esc_attr( $uid ); ?>-wrap">

                <?php /* Thumbnails (left position) */ ?>
                <?php if ( $show_thumbs ) { if ( $is_left ) { if ( $total > 1 ) { ?>
                <div class="<?php echo esc_attr( $uid ); ?>-thumbs">
                    <?php foreach ( $images as $i => $img ) : ?>
                    <div class="<?php echo esc_attr( $uid ); ?>-thumb<?php echo $i === 0 ? ' is-active' : ''; ?>" data-olo-gs-thumb="<?php echo $i; ?>">
                        <img src="<?php echo esc_url( $img['thumb'] ); ?>" alt="<?php echo esc_attr( $img['alt'] ); ?>" loading="lazy" />
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php } } } ?>

                <!-- Main Image Area -->
                <div class="<?php echo esc_attr( $uid ); ?>-main" data-olo-gs-main>
                    <div class="<?php echo esc_attr( $uid ); ?>-track" data-olo-gs-track>
                        <?php foreach ( $images as $img ) : ?>
                        <div class="<?php echo esc_attr( $uid ); ?>-slide">
                            <img src="<?php echo esc_url( $img['large'] ); ?>" alt="<?php echo esc_attr( $img['alt'] ); ?>" data-full="<?php echo esc_url( $img['full'] ); ?>" loading="lazy" />
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php
                    if ( $show_badge ) {
                        if ( $on_sale ) {
                            $regular = (float) $product->get_regular_price();
                            $sale    = (float) $product->get_sale_price();
                            if ( $regular > 0 ) {
                                $pct = round( ( ( $regular - $sale ) / $regular ) * 100 );
                                echo '<div class="' . esc_attr( $uid ) . '-badge">-' . absint( $pct ) . '%</div>';
                            }
                        }
                    }
                    ?>
                    <?php if ( $show_arrows ) { if ( $total > 1 ) { ?>
                    <button class="<?php echo esc_attr( $uid ); ?>-arrow <?php echo esc_attr( $uid ); ?>-prev" data-olo-gs-prev>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="<?php echo $arrow_c; ?>" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    </button>
                    <button class="<?php echo esc_attr( $uid ); ?>-arrow <?php echo esc_attr( $uid ); ?>-next" data-olo-gs-next>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="<?php echo $arrow_c; ?>" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                    <?php } } ?>
                </div>

                <?php /* Thumbnails (bottom position) */ ?>
                <?php if ( $show_thumbs ) { if ( ! $is_left ) { if ( $total > 1 ) { ?>
                <div class="<?php echo esc_attr( $uid ); ?>-thumbs">
                    <?php foreach ( $images as $i => $img ) : ?>
                    <div class="<?php echo esc_attr( $uid ); ?>-thumb<?php echo $i === 0 ? ' is-active' : ''; ?>" data-olo-gs-thumb="<?php echo $i; ?>">
                        <img src="<?php echo esc_url( $img['thumb'] ); ?>" alt="<?php echo esc_attr( $img['alt'] ); ?>" loading="lazy" />
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php } } } ?>
            </div>
        </div>

        <?php if ( $enable_lb ) : ?>
        <!-- Lightbox -->
        <div class="<?php echo esc_attr( $uid ); ?>-lb" data-olo-gs-lb>
            <button class="<?php echo esc_attr( $uid ); ?>-lb-close" data-olo-gs-lb-close>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            <?php if ( $total > 1 ) : ?>
            <button class="<?php echo esc_attr( $uid ); ?>-lb-arrow <?php echo esc_attr( $uid ); ?>-lb-prev" data-olo-gs-lb-prev>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <button class="<?php echo esc_attr( $uid ); ?>-lb-arrow <?php echo esc_attr( $uid ); ?>-lb-next" data-olo-gs-lb-next>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
            <?php endif; ?>
            <img src="" alt="" data-olo-gs-lb-img />
            <div class="<?php echo esc_attr( $uid ); ?>-lb-counter" data-olo-gs-lb-counter></div>
        </div>
        <?php endif; ?>

        <script>
        (function(){
            var root = document.querySelector('.<?php echo $uid; ?>');
            if(!root){return}

            var images  = <?php echo wp_json_encode( $images ); ?>;
            var total   = images.length;
            var current = 0;
            var track   = root.querySelector('[data-olo-gs-track]');
            var mainEl  = root.querySelector('[data-olo-gs-main]');
            var thumbs  = root.querySelectorAll('[data-olo-gs-thumb]');
            var prevBtn = root.querySelector('[data-olo-gs-prev]');
            var nextBtn = root.querySelector('[data-olo-gs-next]');
            var enableZoom = <?php echo $enable_zoom ? 'true' : 'false'; ?>;
            var enableLb   = <?php echo $enable_lb ? 'true' : 'false'; ?>;

            function goTo(idx){
                if(idx < 0){ idx = total - 1; }
                if(idx >= total){ idx = 0; }
                current = idx;
                if(track){ track.style.transform = 'translateX(-' + (idx * 100) + '%)'; }
                thumbs.forEach(function(t){
                    var ti = parseInt(t.getAttribute('data-olo-gs-thumb'));
                    if(ti === idx){ t.classList.add('is-active'); }
                    else { t.classList.remove('is-active'); }
                });
                /* Scroll thumb into view */
                if(thumbs[idx]){
                    thumbs[idx].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' });
                }
            }

            /* Thumbnail clicks */
            thumbs.forEach(function(t){
                t.addEventListener('click', function(){
                    goTo(parseInt(t.getAttribute('data-olo-gs-thumb')));
                });
            });

            /* Arrow clicks */
            if(prevBtn){ prevBtn.addEventListener('click', function(e){ e.stopPropagation(); goTo(current - 1); }); }
            if(nextBtn){ nextBtn.addEventListener('click', function(e){ e.stopPropagation(); goTo(current + 1); }); }

            /* Zoom on hover */
            if(enableZoom){
                if(mainEl){
                    var isZooming = false;
                    var slides = root.querySelectorAll('.<?php echo $uid; ?>-slide img');

                    mainEl.addEventListener('mousemove', function(e){
                        if(!isZooming){return}
                        var rect = mainEl.getBoundingClientRect();
                        var x = ((e.clientX - rect.left) / rect.width) * 100;
                        var y = ((e.clientY - rect.top) / rect.height) * 100;
                        var img = slides[current];
                        if(img){
                            img.style.transformOrigin = x + '% ' + y + '%';
                            img.style.transform = 'scale(2)';
                        }
                    });

                    mainEl.addEventListener('mouseenter', function(){
                        isZooming = true;
                        mainEl.classList.add('is-zooming');
                    });

                    mainEl.addEventListener('mouseleave', function(){
                        isZooming = false;
                        mainEl.classList.remove('is-zooming');
                        slides.forEach(function(img){
                            img.style.transform = '';
                            img.style.transformOrigin = 'center center';
                        });
                    });

                    /* Click opens lightbox (if enabled) */
                    if(enableLb){
                        mainEl.addEventListener('click', function(e){
                            if(e.target.closest('[data-olo-gs-prev]')){return}
                            if(e.target.closest('[data-olo-gs-next]')){return}
                            openLightbox(current);
                        });
                    }
                }
            } else if(enableLb){
                /* No zoom but lightbox on click */
                if(mainEl){
                    mainEl.addEventListener('click', function(e){
                        if(e.target.closest('[data-olo-gs-prev]')){return}
                        if(e.target.closest('[data-olo-gs-next]')){return}
                        openLightbox(current);
                    });
                }
            }

            <?php if ( $enable_lb ) : ?>
            /* Lightbox logic */
            var lb      = document.querySelector('.<?php echo $uid; ?>-lb');
            var lbImg   = lb ? lb.querySelector('[data-olo-gs-lb-img]') : null;
            var lbClose = lb ? lb.querySelector('[data-olo-gs-lb-close]') : null;
            var lbPrev  = lb ? lb.querySelector('[data-olo-gs-lb-prev]') : null;
            var lbNext  = lb ? lb.querySelector('[data-olo-gs-lb-next]') : null;
            var lbCount = lb ? lb.querySelector('[data-olo-gs-lb-counter]') : null;
            var lbIdx   = 0;

            function openLightbox(idx){
                if(!lb){return}
                lbIdx = idx;
                if(lbImg){ lbImg.src = images[idx].full; }
                if(lbCount){ lbCount.textContent = (idx + 1) + ' / ' + total; }
                lb.classList.add('is-open');
                document.body.style.overflow = 'hidden';
            }
            function closeLightbox(){
                if(!lb){return}
                lb.classList.remove('is-open');
                document.body.style.overflow = '';
            }
            function lbGoTo(idx){
                if(idx < 0){ idx = total - 1; }
                if(idx >= total){ idx = 0; }
                lbIdx = idx;
                if(lbImg){ lbImg.src = images[idx].full; }
                if(lbCount){ lbCount.textContent = (idx + 1) + ' / ' + total; }
            }

            if(lbClose){ lbClose.addEventListener('click', closeLightbox); }
            if(lb){
                lb.addEventListener('click', function(e){
                    if(e.target === lb){ closeLightbox(); }
                });
            }
            if(lbPrev){ lbPrev.addEventListener('click', function(e){ e.stopPropagation(); lbGoTo(lbIdx - 1); }); }
            if(lbNext){ lbNext.addEventListener('click', function(e){ e.stopPropagation(); lbGoTo(lbIdx + 1); }); }

            document.addEventListener('keydown', function(e){
                if(!lb){return}
                if(!lb.classList.contains('is-open')){return}
                if(e.key === 'Escape'){ closeLightbox(); }
                if(e.key === 'ArrowLeft'){ lbGoTo(lbIdx - 1); }
                if(e.key === 'ArrowRight'){ lbGoTo(lbIdx + 1); }
            });
            <?php endif; ?>

            /* Swipe support for touch devices */
            if(mainEl){
                var touchStartX = 0;
                var touchEndX = 0;
                mainEl.addEventListener('touchstart', function(e){
                    touchStartX = e.changedTouches[0].screenX;
                }, { passive: true });
                mainEl.addEventListener('touchend', function(e){
                    touchEndX = e.changedTouches[0].screenX;
                    var diff = touchStartX - touchEndX;
                    if(Math.abs(diff) > 50){
                        if(diff > 0){ goTo(current + 1); }
                        else { goTo(current - 1); }
                    }
                }, { passive: true });
            }
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
