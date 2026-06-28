<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Woo_Quickview_Tile extends Olobuild_Tile_Base {

    protected $type     = 'woo_quickview';
    protected $name     = 'Quick View WC';
    protected $icon     = 'dashicons-visibility';
    protected $category = 'woocommerce';
    protected $defaults = [
        'button_text'      => 'Vista rapida',
        'button_style'     => 'outline',
        'show_gallery'     => true,
        'show_add_to_cart' => true,
        'show_price'       => true,
        'show_rating'      => true,
        'show_excerpt'     => true,
        'show_meta'        => true,
        'modal_width'      => 800,
        'image_width'      => 50,
        'accent_color'     => '',
        'text_color'       => '',
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
        if ( ! class_exists( 'WooCommerce' ) ) {
            return '<div style="padding:40px;text-align:center;color:var(--olo-color-warning, #b45309);background:color-mix(in srgb, var(--olo-color-warning, #b45309) 12%, #fff);border:1px solid var(--olo-color-warning, #b45309);border-radius:8px;">'
                 . esc_html( olobuild_t( 'WooCommerce non attivo. Installa e attiva WooCommerce per utilizzare questo elemento.' ) )
                 . '</div>';
        }

        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-woo-qv-' . wp_rand( 10000, 99999 );

        $accent    = $this->safe_color_css( $s['accent_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $text_col  = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text, #374151)';
        $modal_w   = max( 400, min( 1200, absint( $s['modal_width'] ) ) );
        $img_w     = max( 30, min( 70, absint( $s['image_width'] ) ) );
        $btn_text  = esc_html( $s['button_text'] ?: olobuild_t( 'Vista rapida' ) );
        $btn_style = in_array( $s['button_style'], [ 'filled', 'outline', 'text' ], true ) ? $s['button_style'] : 'outline';

        // Register REST endpoint
        $this->register_qv_endpoint();

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() with token fallbacks for colours, absint()+max/min clamps for sizes, in_array() whitelist for the button style, internal wp_rand() uid. ?>
        <style>
            .<?php echo $uid; ?>-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.55);
                z-index: 99999;
                justify-content: center;
                align-items: center;
                padding: 20px;
            }
            .<?php echo $uid; ?>-overlay.is-open {
                display: flex;
            }
            .<?php echo $uid; ?>-modal {
                background: var(--olo-color-background, #FFFFFF);
                border-radius: 12px;
                max-width: <?php echo (int) $modal_w; ?>px;
                width: 100%;
                max-height: 90vh;
                overflow-y: auto;
                position: relative;
                box-shadow: 0 25px 50px rgba(0,0,0,0.25);
                animation: oloQvFadeIn 0.2s ease;
            }
            @keyframes oloQvFadeIn {
                from { opacity: 0; transform: scale(0.96); }
                to { opacity: 1; transform: scale(1); }
            }
            .<?php echo $uid; ?>-close {
                position: absolute;
                top: 12px;
                right: 12px;
                width: 32px;
                height: 32px;
                border: none;
                background: var(--olo-color-muted, #F3F4F6);
                border-radius: 50%;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 2;
                transition: background 0.2s;
            }
            .<?php echo $uid; ?>-close:hover {
                background: var(--olo-color-border, #E5E7EB);
            }
            .<?php echo $uid; ?>-body {
                padding: 30px;
            }
            .<?php echo $uid; ?>-loading {
                text-align: center;
                padding: 60px 0;
                color: var(--olo-color-text-muted, #9CA3AF);
                font-size: 14px;
            }
            .<?php echo $uid; ?>-loading svg {
                animation: oloQvSpin 0.8s linear infinite;
            }
            @keyframes oloQvSpin {
                to { transform: rotate(360deg); }
            }
            .<?php echo $uid; ?>-grid {
                display: grid;
                grid-template-columns: <?php echo (int) $img_w; ?>% 1fr;
                gap: 30px;
            }
            @media (max-width: 640px) {
                .<?php echo $uid; ?>-grid {
                    grid-template-columns: 1fr;
                }
            }
            .<?php echo $uid; ?>-grid .olo-qv-main-img {
                width: 100%;
                height: auto;
                border-radius: 8px;
                display: block;
            }
            .<?php echo $uid; ?>-grid .olo-qv-thumbs {
                display: flex;
                gap: 6px;
                margin-top: 8px;
                overflow-x: auto;
            }
            .<?php echo $uid; ?>-grid .olo-qv-thumb {
                width: 56px;
                height: 56px;
                object-fit: cover;
                border-radius: 4px;
                border: 2px solid transparent;
                cursor: pointer;
                transition: border-color 0.2s;
                flex-shrink: 0;
            }
            .<?php echo $uid; ?>-grid .olo-qv-thumb:hover,
            .<?php echo $uid; ?>-grid .olo-qv-thumb.is-active {
                border-color: <?php echo $accent; ?>;
            }
            .<?php echo $uid; ?>-grid .olo-qv-title {
                font-size: 22px;
                font-weight: 700;
                margin: 0 0 10px;
                color: <?php echo $text_col; ?>;
            }
            .<?php echo $uid; ?>-grid .olo-qv-price {
                font-size: 20px;
                font-weight: 600;
                margin-bottom: 12px;
                color: <?php echo $text_col; ?>;
            }
            .<?php echo $uid; ?>-grid .olo-qv-price del {
                opacity: 0.5;
                font-size: 16px;
            }
            .<?php echo $uid; ?>-grid .olo-qv-price ins {
                text-decoration: none;
                color: var(--olo-color-danger, #EF4444);
            }
            .<?php echo $uid; ?>-grid .olo-qv-stars {
                display: flex;
                align-items: center;
                gap: 2px;
                margin-bottom: 12px;
            }
            .<?php echo $uid; ?>-grid .olo-qv-excerpt {
                font-size: 14px;
                line-height: 1.6;
                color: var(--olo-color-text-muted, #9CA3AF);
                margin-bottom: 16px;
            }
            .<?php echo $uid; ?>-grid .olo-qv-atc {
                display: flex;
                gap: 10px;
                align-items: center;
                margin-top: 16px;
            }
            .<?php echo $uid; ?>-grid .olo-qv-qty {
                width: 60px;
                height: 40px;
                text-align: center;
                border: 1px solid var(--olo-color-border, #E5E7EB);
                border-radius: 6px;
                font-size: 14px;
            }
            .<?php echo $uid; ?>-grid .olo-qv-atc-btn {
                padding: 10px 24px;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                font-weight: 600;
                font-size: 14px;
                background: <?php echo $accent; ?>;
                color: var(--olo-color-primary-contrast, #FFFFFF);
                transition: opacity 0.2s;
            }
            .<?php echo $uid; ?>-grid .olo-qv-atc-btn:hover {
                opacity: 0.9;
            }
            .<?php echo $uid; ?>-grid .olo-qv-stock-out {
                color: var(--olo-color-danger, #EF4444);
                font-weight: 600;
                margin-top: 12px;
                font-size: 14px;
            }
            .<?php echo $uid; ?>-grid .olo-qv-meta {
                font-size: 12px;
                color: var(--olo-color-text-muted, #9CA3AF);
                margin-top: 16px;
                line-height: 1.8;
            }
            .<?php echo $uid; ?>-grid .olo-qv-link {
                display: inline-block;
                margin-top: 16px;
                font-size: 13px;
                color: <?php echo $accent; ?>;
                text-decoration: none;
            }
            .<?php echo $uid; ?>-grid .olo-qv-link:hover {
                text-decoration: underline;
            }
            /* Quick View trigger button injected into product cards */
            .olo-qv-trigger {
                <?php if ( $btn_style === 'filled' ) : ?>
                background: <?php echo $accent; ?>;
                color: var(--olo-color-primary-contrast, #FFFFFF);
                border: none;
                <?php elseif ( $btn_style === 'outline' ) : ?>
                background: rgba(255,255,255,0.95);
                color: <?php echo $text_col; ?>;
                border: 1px solid var(--olo-color-border, #E5E7EB);
                <?php else : ?>
                background: transparent;
                color: <?php echo $accent; ?>;
                border: none;
                <?php endif; ?>
                padding: 6px 16px;
                border-radius: 6px;
                font-size: 12px;
                font-weight: 600;
                cursor: pointer;
                position: absolute;
                bottom: 10px;
                left: 50%;
                transform: translateX(-50%);
                opacity: 0;
                transition: opacity 0.2s;
                z-index: 5;
                white-space: nowrap;
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <!-- Quick View Modal Shell -->
        <div class="<?php echo esc_attr( $uid ); ?>-overlay" data-olo-qv-overlay>
            <div class="<?php echo esc_attr( $uid ); ?>-modal" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr( olobuild_t( 'Vista rapida prodotto' ) ); ?>" tabindex="-1" data-olo-qv-dialog>
                <button class="<?php echo esc_attr( $uid ); ?>-close" data-olo-qv-close aria-label="<?php echo esc_attr( olobuild_t( 'Chiudi' ) ); ?>">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2" aria-hidden="true" focusable="false"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
                <div class="<?php echo esc_attr( $uid ); ?>-body">
                    <div class="<?php echo esc_attr( $uid ); ?>-loading" data-olo-qv-loading>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="2"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                        <div style="margin-top:8px"><?php echo esc_html( olobuild_t( 'Caricamento...' ) ); ?></div>
                    </div>
                    <div data-olo-qv-content style="display:none"></div>
                </div>
            </div>
        </div>

        <script>
        (function(){
            var overlay = document.querySelector('.<?php echo $uid; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- internal 'olo-woo-qv-' . wp_rand() identifier. ?>-overlay');
            if(!overlay){return}
            var loading = overlay.querySelector('[data-olo-qv-loading]');
            var content = overlay.querySelector('[data-olo-qv-content]');
            var closeBtn = overlay.querySelector('[data-olo-qv-close]');
            var dialog = overlay.querySelector('[data-olo-qv-dialog]');
            var lastTrigger = null;
            var restBase = '<?php echo esc_js( rest_url( 'olo/v1/woo-quickview/' ) ); ?>';

            /* Focusable elements inside the dialog (for focus trap) */
            function qvFocusables(){
                return Array.prototype.slice.call(
                    overlay.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])')
                ).filter(function(el){ return el.offsetParent !== null; });
            }

            /* Close modal */
            function closeModal(){
                overlay.classList.remove('is-open');
                document.body.style.overflow = '';
                if(lastTrigger && typeof lastTrigger.focus === 'function'){ lastTrigger.focus(); }
                lastTrigger = null;
            }
            if(closeBtn){ closeBtn.addEventListener('click', closeModal); }
            overlay.addEventListener('click', function(e){
                if(e.target === overlay){ closeModal(); }
            });
            document.addEventListener('keydown', function(e){
                if(!overlay.classList.contains('is-open')){ return; }
                if(e.key === 'Escape'){
                    closeModal();
                    return;
                }
                if(e.key === 'Tab'){
                    var f = qvFocusables();
                    if(!f.length){ e.preventDefault(); if(dialog){ dialog.focus(); } return; }
                    var first = f[0];
                    var last = f[f.length - 1];
                    var active = document.activeElement;
                    if(e.shiftKey){
                        if(active === first || !overlay.contains(active)){ e.preventDefault(); last.focus(); }
                    } else {
                        if(active === last || !overlay.contains(active)){ e.preventDefault(); first.focus(); }
                    }
                }
            });

            /* Open modal */
            function openQuickView(pid, trigger){
                lastTrigger = trigger || document.activeElement;
                loading.style.display = 'block';
                content.style.display = 'none';
                overlay.classList.add('is-open');
                document.body.style.overflow = 'hidden';
                if(closeBtn){ closeBtn.focus(); } else if(dialog){ dialog.focus(); }

                fetch(restBase + pid)
                .then(function(r){ return r.json(); })
                .then(function(data){
                    if(data.html){
                        content.innerHTML = data.html;
                        loading.style.display = 'none';
                        content.style.display = 'block';

                        /* Wire dialog accessible name to the product title */
                        var titleEl = content.querySelector('[data-olo-qv-title]');
                        if(titleEl && dialog){
                            if(!titleEl.id){ titleEl.id = 'olo-qv-title-' + pid; }
                            dialog.setAttribute('aria-labelledby', titleEl.id);
                        }

                        /* Thumbnail click handler */
                        var thumbs = content.querySelectorAll('[data-olo-qv-thumb-src]');
                        var mainImg = content.querySelector('[data-olo-qv-main-img]');
                        thumbs.forEach(function(thumb){
                            thumb.addEventListener('click', function(){
                                if(mainImg){ mainImg.src = thumb.getAttribute('data-olo-qv-thumb-src'); }
                                thumbs.forEach(function(t){ t.classList.remove('is-active'); });
                                thumb.classList.add('is-active');
                            });
                        });

                        /* ATC handler — NO && in inline scripts! */
                        var atcBtn = content.querySelector('[data-olo-qv-atc-btn]');
                        if(atcBtn){
                            atcBtn.addEventListener('click', function(){
                                var qty = content.querySelector('.olo-qv-qty');
                                var q = qty ? qty.value : 1;
                                window.location.href = '?add-to-cart=' + pid + '&quantity=' + q;
                            });
                        }
                    }
                })
                .catch(function(){
                    content.innerHTML = '<p style="text-align:center;color:var(--olo-color-danger, #EF4444);padding:30px">' + '<?php echo esc_js( olobuild_t( 'Errore nel caricamento.' ) ); ?>' + '</p>';
                    loading.style.display = 'none';
                    content.style.display = 'block';
                });
            }

            /* Inject Quick View buttons into product cards */
            var cards = document.querySelectorAll('[data-product-id]');
            cards.forEach(function(card){
                var pid = card.getAttribute('data-product-id');
                if(!pid){return}
                var imgWrap = card.querySelector('.olo-woo-card-img');
                if(!imgWrap){return}

                var btn = document.createElement('button');
                btn.className = 'olo-qv-trigger';
                btn.textContent = '<?php echo esc_js( $s['button_text'] ?: olobuild_t( 'Vista rapida' ) ); ?>';
                btn.setAttribute('aria-haspopup', 'dialog');
                btn.addEventListener('click', function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    openQuickView(pid, btn);
                });

                imgWrap.style.position = 'relative';
                imgWrap.appendChild(btn);
                imgWrap.addEventListener('mouseenter', function(){ btn.style.opacity = '1'; });
                imgWrap.addEventListener('mouseleave', function(){ btn.style.opacity = '0'; });
            });
        })();
        </script>
        <?php
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- border CSS from base-class build_border_css() (intval'd widths, safe_color_css()'d colours), internal wp_rand() uid.
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- border CSS from base-class build_border_hover_css()/build_border_effect_css() helpers (intval'd values, safe_color_css()'d colours).
        }
        return ob_get_clean();
    }

    /**
     * Register REST endpoint for quick view data.
     */
    private function register_qv_endpoint() {
        static $registered = false;
        if ( $registered ) {
            return;
        }
        $registered = true;

        add_action( 'rest_api_init', function() {
            register_rest_route( 'olo/v1', '/woo-quickview/(?P<id>\d+)', [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_quickview_data' ],
                'permission_callback' => '__return_true',
            ] );
        } );
    }

    /**
     * REST callback: return product quick-view HTML.
     */
    public function get_quickview_data( $request ) {
        $pid     = intval( $request['id'] );
        $product = wc_get_product( $pid );
        if ( ! $product ) {
            return new WP_Error( 'not_found', 'Prodotto non trovato', [ 'status' => 404 ] );
        }

        $image   = get_the_post_thumbnail_url( $pid, 'large' ) ?: wc_placeholder_img_src();
        $gallery = $product->get_gallery_image_ids();

        ob_start();
        ?>
        <div class="<?php echo 'olo-woo-qv-grid'; ?>" style="display:grid;grid-template-columns:50% 1fr;gap:30px">
            <div>
                <img data-olo-qv-main-img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" class="olo-qv-main-img" style="width:100%;height:auto;border-radius:8px" />
                <?php if ( ! empty( $gallery ) ) : ?>
                <div class="olo-qv-thumbs" style="display:flex;gap:6px;margin-top:8px;overflow-x:auto">
                    <?php foreach ( array_slice( $gallery, 0, 6 ) as $gid ) :
                        $thumb_url = wp_get_attachment_image_url( $gid, 'thumbnail' );
                        $large_url = wp_get_attachment_image_url( $gid, 'large' );
                        if ( ! $thumb_url ) { continue; }
                    ?>
                    <button type="button" class="olo-qv-thumb" data-olo-qv-thumb-src="<?php echo esc_url( $large_url ); ?>" aria-label="<?php echo esc_attr( sprintf( olobuild_t( 'Mostra immagine: %s' ), $product->get_name() ) ); ?>" style="padding:0;border:2px solid transparent;border-radius:4px;cursor:pointer;background:none;line-height:0">
                        <img src="<?php echo esc_url( $thumb_url ); ?>" alt="" style="width:56px;height:56px;object-fit:cover;border-radius:2px;display:block" />
                    </button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <div>
                <h3 data-olo-qv-title style="font-size:22px;font-weight:700;margin:0 0 10px"><?php echo esc_html( $product->get_name() ); ?></h3>
                <div style="font-size:20px;font-weight:600;margin-bottom:12px"><?php echo $product->get_price_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- price HTML generated and escaped by WooCommerce (WC_Product::get_price_html()). ?></div>
                <?php if ( $product->get_average_rating() > 0 ) : ?>
                <div style="display:flex;align-items:center;gap:2px;margin-bottom:12px">
                    <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                    <span style="color:<?php echo $i <= round( $product->get_average_rating() ) ? 'var(--olo-color-warning, #F59E0B)' : 'var(--olo-color-border, #E5E7EB)'; ?>;font-size:16px">&#9733;</span>
                    <?php endfor; ?>
                    <span style="font-size:12px;color:var(--olo-color-text-muted, #9CA3AF);margin-left:4px">(<?php echo absint( $product->get_review_count() ); ?>)</span>
                </div>
                <?php endif; ?>
                <?php if ( $product->get_short_description() ) : ?>
                <div style="font-size:14px;line-height:1.6;color:var(--olo-color-text-muted, #9CA3AF);margin-bottom:16px"><?php echo wp_kses_post( $product->get_short_description() ); ?></div>
                <?php endif; ?>
                <?php if ( $product->is_purchasable() ) : ?>
                    <?php if ( $product->is_in_stock() ) : ?>
                    <div style="display:flex;gap:10px;align-items:center;margin-top:16px">
                        <input type="number" class="olo-qv-qty" value="1" min="1" max="<?php echo (int) ( $product->get_stock_quantity() ?: 99 ); ?>" style="width:60px;height:40px;text-align:center;border:1px solid var(--olo-color-border, #E5E7EB);border-radius:6px;font-size:14px" />
                        <button data-olo-qv-atc-btn style="padding:10px 24px;border:none;border-radius:6px;cursor:pointer;font-weight:600;font-size:14px;background:var(--olo-color-primary,#e1474f);color:var(--olo-color-primary-contrast, #FFFFFF)"><?php echo esc_html( olobuild_t( 'Aggiungi al carrello' ) ); ?></button>
                    </div>
                    <?php else : ?>
                    <div style="color:var(--olo-color-danger, #EF4444);font-weight:600;margin-top:12px"><?php echo esc_html( olobuild_t( 'Esaurito' ) ); ?></div>
                    <?php endif; ?>
                <?php endif; ?>
                <div style="font-size:12px;color:var(--olo-color-text-muted, #9CA3AF);margin-top:16px;line-height:1.8">
                    <?php if ( $product->get_sku() ) : ?>
                    <div>SKU: <?php echo esc_html( $product->get_sku() ); ?></div>
                    <?php endif; ?>
                    <?php
                    $cats = wc_get_product_category_list( $pid );
                    if ( $cats ) : ?>
                    <div><?php echo esc_html( olobuild_t( 'Categorie' ) ); ?>: <?php echo $cats; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- category links HTML built and escaped by WooCommerce core (wc_get_product_category_list()). ?></div>
                    <?php endif; ?>
                </div>
                <a href="<?php echo esc_url( get_permalink( $pid ) ); ?>" style="display:inline-block;margin-top:16px;font-size:13px;color:var(--olo-color-primary,#e1474f);text-decoration:none"><?php echo esc_html( olobuild_t( 'Vedi dettagli completi' ) ); ?> &rarr;</a>
            </div>
        </div>
        <?php
        $html = ob_get_clean();

        return rest_ensure_response( [ 'html' => $html ] );
    }
}
