<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Woo_Product_Gallery_Slider_Tile extends Olobuild_Tile_Base {

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
        'main_bg'                => '',
        'thumbnail_border'       => '',
        'thumbnail_active_border' => '',
        'arrow_color'            => '',
        'arrow_bg'               => 'rgba(255,255,255,0.9)',
        'show_badge'             => true,
        'badge_bg'               => '',
        'max_width'              => 600,
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

        $s = wp_parse_args( $settings, $this->defaults );

        // Get product. Senza un campo prodotto la tile funzionava solo dentro la scheda di un
        // prodotto: altrove (una pagina, una landing) diceva «Nessun prodotto disponibile».
        // «ID prodotto» vuoto = il prodotto della pagina, come prima.
        $prod = null;
        $pid  = absint( $s['product_id'] ?? 0 );
        if ( $pid ) {
            $prod = wc_get_product( $pid );
        }
        if ( ! $prod ) {
            global $product;
            if ( ! is_a( $product, 'WC_Product' ) ) {
                // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- global $product di WooCommerce, non un global definito da olobuild
                $product = wc_get_product( get_the_ID() );
            }
            $prod = $product;
        }
        if ( ! $prod ) {
            // L'avviso è per chi costruisce la pagina (canvas del builder, utenti che possono
            // modificare): il visitatore se lo trovava scritto in pagina. A lui, niente.
            if ( empty( $s['_builder_mode'] ) && ! current_user_can( 'edit_posts' ) ) {
                return '';
            }
            return '<div style="padding:20px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);font-size:14px;">'
                 . esc_html( olobuild_t( 'Nessun prodotto disponibile in questo contesto' ) )
                 . '</div>';
        }

        // Collect gallery images: featured first, then gallery
        $image_ids = [];
        $featured_id = $prod->get_image_id();
        if ( $featured_id ) {
            $image_ids[] = $featured_id;
        }
        $gallery_ids = $prod->get_gallery_image_ids();
        if ( ! empty( $gallery_ids ) ) {
            $image_ids = array_merge( $image_ids, $gallery_ids );
        }

        if ( empty( $image_ids ) ) {
            return '<div style="padding:40px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);font-size:14px;background:var(--olo-color-muted, #F3F4F6);border-radius:8px;">'
                 . '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--olo-color-border, #E5E7EB)" stroke-width="1.5" style="margin:0 auto 12px;display:block"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>'
                 . esc_html( olobuild_t( 'Nessuna immagine nella gallery del prodotto' ) )
                 . '</div>';
        }

        $uid = 'olo-woo-gs-' . wp_rand( 10000, 99999 );

        // Settings
        // CSS-safe height: strip characters that could break out of the declaration/<style> block.
        $main_h      = preg_replace( '/[;{}<>]/', '', sanitize_text_field( $s['main_height'] ) );
        $thumb_size  = max( 40, min( 120, absint( $s['thumbnail_size'] ) ) );
        $thumb_gap   = max( 2, min( 20, absint( $s['thumbnail_gap'] ) ) );
        $radius      = Olobuild_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $radius_hover_css = Olobuild_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );
        $radius_raw  = Olobuild_Tile_Utils::radius_int( $s['border_radius'] ?? 0 );
        // Larghezza massima: nell'inspector non c'era e la galleria restava a 600 px anche in una
        // colonna più larga. Ora è un controllo; 0 = tutta la larghezza della cella. Niente minimo:
        // il vecchio 200 px faceva restare ferme le prime posizioni del cursore (10–190).
        $max_width   = absint( $s['max_width'] );
        $max_width   = $max_width ? min( 1200, $max_width ) . 'px' : 'none';
        $show_thumbs = ! empty( $s['show_thumbnails'] );
        // «Destra» stava nella tendina ma non nell'elenco ammesso: ricadeva in basso.
        $thumb_pos   = in_array( $s['thumbnail_position'], [ 'bottom', 'left', 'right' ], true ) ? $s['thumbnail_position'] : 'bottom';
        $enable_zoom = ! empty( $s['enable_zoom'] );
        $enable_lb   = ! empty( $s['enable_lightbox'] );
        $show_arrows = ! empty( $s['arrows'] );
        $show_badge  = ! empty( $s['show_badge'] );
        $is_left     = ( $thumb_pos === 'left' );
        $is_side     = ( $thumb_pos !== 'bottom' ); // miniature in colonna, a sinistra o a destra
        $on_sale     = $prod->is_on_sale();

        // Transizione, autoplay, intervallo e pallini: nell'inspector da sempre, mai letti dal PHP.
        // Assenti (template importati) valgono come i default del config: slide, niente autoplay,
        // 3 secondi, niente pallini.
        $is_fade     = ( ( $s['transition'] ?? 'slide' ) === 'fade' );
        $autoplay    = filter_var( $s['autoplay'] ?? false, FILTER_VALIDATE_BOOLEAN );
        $auto_speed  = max( 1000, min( 20000, absint( $s['autoplay_speed'] ?? 3000 ) ) );
        $show_dots   = filter_var( $s['dots'] ?? false, FILTER_VALIDATE_BOOLEAN );

        // Colors
        $main_bg   = $this->safe_color_css( $s['main_bg'] ) ?: 'var(--olo-color-surface-alt, #f6f7f9)';
        $tb        = $this->safe_color_css( $s['thumbnail_border'] ) ?: 'var(--olo-color-border, #e5e7eb)';
        $tab       = $this->safe_color_css( $s['thumbnail_active_border'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $arrow_c   = $this->safe_color_css( $s['arrow_color'] ) ?: 'var(--olo-color-text, #374151)';
        $arrow_bg  = $this->safe_color_css( $s['arrow_bg'] ) ?: 'rgba(255,255,255,0.9)';
        $badge_bg  = $this->safe_color_css( $s['badge_bg'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $dot_c     = $this->safe_color_css( $s['dot_color'] ?? '' ) ?: 'var(--olo-color-border, #e5e7eb)';
        $dot_ac    = $this->safe_color_css( $s['dot_active_color'] ?? '' ) ?: 'var(--olo-color-primary, #e1474f)';

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
                    'alt'   => $alt ?: $prod->get_name(),
                ];
            }
        }

        if ( empty( $images ) ) {
            return '';
        }

        $total = count( $images );

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist for every colour, absint()/intval() with min/max clamps for numerics, the breakout-char-stripped $main_h, Olobuild_Tile_Utils radius helpers, and the internally generated $uid.
        ?>
        <style>
            .<?php echo $uid; ?> {
                max-width: <?php echo $max_width; ?>;
            }
            .<?php echo $uid; ?>-wrap {
                display: flex;
                <?php if ( $is_side ) : ?>
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
                <?php if ( $is_side ) : ?>
                flex: 1;
                min-width: 0;
                <?php endif; ?>
                <?php if ( $is_left ) : ?>
                order: 2;
                <?php endif; ?>
            }
            <?php if ( $radius_hover_css !== '' ) : ?>.<?php echo $uid; ?>-main{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?>-main:hover{border-radius:<?php echo $radius_hover_css; ?> !important}<?php endif; ?>
            .<?php echo $uid; ?>-track {
                <?php if ( $is_fade ) : ?>
                display: grid;
                <?php else : ?>
                display: flex;
                transition: transform 0.4s ease;
                <?php endif; ?>
                height: <?php echo $main_h; ?>;
            }
            .<?php echo $uid; ?>-slide {
                min-width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                <?php if ( $is_fade ) : ?>
                /* Dissolvenza: le foto una sull'altra nella stessa cella, si vede quella attiva. */
                grid-area: 1 / 1;
                min-height: 0;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.5s ease, visibility 0s linear 0.5s;
                <?php endif; ?>
            }
            <?php if ( $is_fade ) : ?>
            .<?php echo $uid; ?>-slide.is-active {
                opacity: 1;
                visibility: visible;
                transition: opacity 0.5s ease, visibility 0s;
            }
            <?php endif; ?>
            <?php if ( $show_dots ) : ?>
            .<?php echo $uid; ?>-dots {
                position: absolute;
                left: 0;
                right: 0;
                bottom: 8px;
                z-index: 3;
                display: flex;
                justify-content: center;
                gap: 2px;
            }
            .<?php echo $uid; ?>-dot {
                width: 24px;
                height: 24px;
                padding: 0;
                border: none;
                border-radius: 50%;
                background: transparent;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
            }
            .<?php echo $uid; ?>-dot::before {
                content: "";
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: <?php echo $dot_c; ?>;
                box-shadow: 0 0 0 1px rgba(0,0,0,0.08);
                transition: background 0.2s ease, transform 0.2s ease;
            }
            .<?php echo $uid; ?>-dot[aria-current="true"]::before {
                background: <?php echo $dot_ac; ?>;
                transform: scale(1.25);
            }
            <?php endif; ?>
            .<?php echo $uid; ?>-arrow:focus-visible,
            .<?php echo $uid; ?>-dot:focus-visible {
                outline: 2px solid var(--olo-color-primary, #e1474f);
                outline-offset: 2px;
            }
            @media (prefers-reduced-motion: reduce) {
                .<?php echo $uid; ?>-track,
                .<?php echo $uid; ?>-slide,
                .<?php echo $uid; ?>-slide.is-active { transition: none; }
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
                <?php if ( $is_side ) : ?>
                flex-direction: column;
                <?php if ( $is_left ) : ?>order: 1;<?php endif; ?>
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
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <div class="<?php echo esc_attr( $uid ); ?>">
            <div class="<?php echo esc_attr( $uid ); ?>-wrap">

                <?php /* Thumbnails (left position) */ ?>
                <?php if ( $show_thumbs ) { if ( $is_left ) { if ( $total > 1 ) { ?>
                <div class="<?php echo esc_attr( $uid ); ?>-thumbs">
                    <?php foreach ( $images as $i => $img ) : ?>
                    <div class="<?php echo esc_attr( $uid ); ?>-thumb<?php echo $i === 0 ? ' is-active' : ''; ?>" data-olo-gs-thumb="<?php echo (int) $i; ?>">
                        <img src="<?php echo esc_url( $img['thumb'] ); ?>" alt="<?php echo esc_attr( $img['alt'] ); ?>" loading="lazy" />
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php } } } ?>

                <!-- Main Image Area -->
                <div class="<?php echo esc_attr( $uid ); ?>-main" data-olo-gs-main>
                    <div class="<?php echo esc_attr( $uid ); ?>-track" data-olo-gs-track>
                        <?php foreach ( $images as $si => $img ) : ?>
                        <div class="<?php echo esc_attr( $uid ); ?>-slide<?php echo $si === 0 ? ' is-active' : ''; ?>">
                            <img src="<?php echo esc_url( $img['large'] ); ?>" alt="<?php echo esc_attr( $img['alt'] ); ?>" data-full="<?php echo esc_url( $img['full'] ); ?>" loading="lazy" />
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php
                    if ( $show_badge ) {
                        if ( $on_sale ) {
                            $regular = (float) $prod->get_regular_price();
                            $sale    = (float) $prod->get_sale_price();
                            if ( $regular > 0 ) {
                                $pct = round( ( ( $regular - $sale ) / $regular ) * 100 );
                                echo '<div class="' . esc_attr( $uid ) . '-badge">-' . absint( $pct ) . '%</div>';
                            }
                        }
                    }
                    ?>
                    <?php if ( $show_arrows ) { if ( $total > 1 ) { ?>
                    <button type="button" class="<?php echo esc_attr( $uid ); ?>-arrow <?php echo esc_attr( $uid ); ?>-prev" data-olo-gs-prev aria-label="<?php echo esc_attr( olobuild_t( 'Immagine precedente' ) ); ?>">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="<?php echo esc_attr( $arrow_c ); ?>" stroke-width="2" aria-hidden="true" focusable="false"><polyline points="15 18 9 12 15 6"/></svg>
                    </button>
                    <button type="button" class="<?php echo esc_attr( $uid ); ?>-arrow <?php echo esc_attr( $uid ); ?>-next" data-olo-gs-next aria-label="<?php echo esc_attr( olobuild_t( 'Immagine successiva' ) ); ?>">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="<?php echo esc_attr( $arrow_c ); ?>" stroke-width="2" aria-hidden="true" focusable="false"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                    <?php } } ?>
                    <?php if ( $show_dots ) { if ( $total > 1 ) { ?>
                    <div class="<?php echo esc_attr( $uid ); ?>-dots">
                        <?php foreach ( $images as $di => $img ) : ?>
                        <button type="button" class="<?php echo esc_attr( $uid ); ?>-dot" data-olo-gs-dot="<?php echo (int) $di; ?>" data-olo-interactive<?php echo $di === 0 ? ' aria-current="true"' : ''; ?> aria-label="<?php echo esc_attr( olobuild_t( 'Immagine' ) . ' ' . ( $di + 1 ) ); ?>"></button>
                        <?php endforeach; ?>
                    </div>
                    <?php } } ?>
                </div>

                <?php /* Thumbnails (bottom position) */ ?>
                <?php if ( $show_thumbs ) { if ( ! $is_left ) { if ( $total > 1 ) { ?>
                <div class="<?php echo esc_attr( $uid ); ?>-thumbs">
                    <?php foreach ( $images as $i => $img ) : ?>
                    <div class="<?php echo esc_attr( $uid ); ?>-thumb<?php echo $i === 0 ? ' is-active' : ''; ?>" data-olo-gs-thumb="<?php echo (int) $i; ?>">
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

        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline JS below only echoes the internally generated $uid, wp_json_encode()'d image data and 'true'/'false' literals from fixed ternaries. ?>
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
            var isFade     = <?php echo $is_fade ? 'true' : 'false'; ?>;
            var autoplay   = <?php echo $autoplay ? 'true' : 'false'; ?>;
            var autoSpeed  = <?php echo (int) $auto_speed; ?>;
            var slideEls   = track ? track.children : [];
            var dots       = root.querySelectorAll('[data-olo-gs-dot]');

            function goTo(idx){
                if(idx < 0){ idx = total - 1; }
                if(idx >= total){ idx = 0; }
                current = idx;
                if(isFade){
                    for(var si = 0; si < slideEls.length; si++){
                        if(si === idx){ slideEls[si].classList.add('is-active'); }
                        else { slideEls[si].classList.remove('is-active'); }
                    }
                } else if(track){ track.style.transform = 'translateX(-' + (idx * 100) + '%)'; }
                dots.forEach(function(d){
                    if(parseInt(d.getAttribute('data-olo-gs-dot'), 10) === idx){ d.setAttribute('aria-current', 'true'); }
                    else { d.removeAttribute('aria-current'); }
                });
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

            /* Pallini */
            dots.forEach(function(d){
                d.addEventListener('click', function(e){
                    e.stopPropagation();
                    goTo(parseInt(d.getAttribute('data-olo-gs-dot'), 10) || 0);
                });
            });

            /* Autoplay: fermo con prefers-reduced-motion, sotto il mouse, col fuoco dentro, a scheda
               nascosta e col lightbox aperto. */
            if(autoplay){
                if(total > 1){
                    var reduce = false;
                    if(window.matchMedia){ reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches; }
                    if(!reduce){
                        var paused = false;
                        root.addEventListener('mouseenter', function(){ paused = true; });
                        root.addEventListener('mouseleave', function(){ paused = false; });
                        root.addEventListener('focusin', function(){ paused = true; });
                        root.addEventListener('focusout', function(e){ if(!root.contains(e.relatedTarget)){ paused = false; } });
                        setInterval(function(){
                            if(paused){return}
                            if(document.hidden){return}
                            var lbEl = document.querySelector('.<?php echo $uid; ?>-lb.is-open');
                            if(lbEl){return}
                            goTo(current + 1);
                        }, autoSpeed);
                    }
                }
            }

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
                            if(e.target.closest('[data-olo-gs-dot]')){return}
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
                        if(e.target.closest('[data-olo-gs-dot]')){return}
                        openLightbox(current);
                    });
                }
            }

            <?php if ( $enable_lb ) : ?>
            /* Lightbox logic */
            var lb      = document.querySelector('.<?php echo $uid; ?>-lb');
            <?php if ( empty( $settings['_builder_mode'] ) ) : ?>
            /* Dentro il template (transform) il lightbox copriva il template intero e l'immagine
               stava al suo centro, anche sotto la piega con lo scorrimento bloccato. Sul sito va nel body. */
            (<?php echo self::js_nel_body(); ?>)(lb);
            <?php endif; ?>
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
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }
}
