<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Woo_Wishlist_Tile extends Olobuild_Tile_Base {

    protected $type     = 'woo_wishlist';
    protected $name     = 'Wishlist WC';
    protected $icon     = 'dashicons-heart';
    protected $category = 'woocommerce';
    protected $defaults = [
        'icon'            => 'heart',
        'style'           => 'outline',
        'show_count'      => true,
        'show_grid'       => false,
        'columns'         => 4,
        'card_style'      => 'shadow',
        'icon_size'       => 20,
        'icon_color'      => '',
        'icon_bg'         => 'transparent',
        'badge_bg'        => '',
        'badge_color'     => '',
        'title_color'     => '',
        'price_color'     => '',
        'btn_bg'          => '',
        'btn_color'       => '',
        'remove_text'     => 'Rimuovi',
        'empty_text'      => 'La tua wishlist è vuota',
        'columns_tablet'  => 2,
        'columns_mobile'  => 1,
        'gap'             => 20,
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
        $uid = 'olo-woo-wl-' . wp_rand( 10000, 99999 );

        // Settings
        $icon_size   = max( 14, min( 48, absint( $s['icon_size'] ) ) );
        // TOKEN-FIRST: cuore/badge/CTA ereditano il brand se l'utente non sceglie.
        $icon_color  = $this->safe_color_css( $s['icon_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $icon_bg     = $this->safe_color_css( $s['icon_bg'] ) ?: 'transparent';
        $badge_bg    = $this->safe_color_css( $s['badge_bg'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $badge_color = $this->safe_color_css( $s['badge_color'] ) ?: 'var(--olo-color-on-primary, #ffffff)';
        $title_color = $this->safe_color_css( $s['title_color'] ) ?: 'var(--olo-color-text, #1f2937)';
        $price_color = $this->safe_color_css( $s['price_color'] ) ?: 'var(--olo-color-text, #1f2937)';
        $btn_bg      = $this->safe_color_css( $s['btn_bg'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $btn_color   = $this->safe_color_css( $s['btn_color'] ) ?: 'var(--olo-color-on-primary, #ffffff)';
        $cols        = max( 1, min( 6, absint( $s['columns'] ) ) );
        $cols_t      = max( 1, min( 4, absint( $s['columns_tablet'] ) ) );
        $cols_m      = max( 1, min( 2, absint( $s['columns_mobile'] ) ) );
        $gap         = absint( $s['gap'] );
        $show_grid   = ! empty( $s['show_grid'] );

        $card_extra = '';
        if ( $s['card_style'] === 'shadow' ) {
            $card_extra = 'box-shadow:0 1px 3px rgba(0,0,0,0.1),0 1px 2px rgba(0,0,0,0.06);';
        } elseif ( $s['card_style'] === 'border' ) {
            $card_extra = 'border:1px solid var(--olo-color-border, #E5E7EB);';
        }

        $empty_text  = esc_html( $s['empty_text'] ?: olobuild_t( 'La tua wishlist è vuota' ) );
        $remove_text = esc_html( $s['remove_text'] ?: olobuild_t( 'Rimuovi' ) );

        // Heart SVGs
        $heart_outline = '<svg width="' . $icon_size . '" height="' . $icon_size . '" viewBox="0 0 24 24" fill="none" stroke="' . $icon_color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>';
        $heart_filled  = '<svg width="' . $icon_size . '" height="' . $icon_size . '" viewBox="0 0 24 24" fill="' . $icon_color . '" stroke="' . $icon_color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>';

        ob_start();
        ?>
<?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist (with fixed var() fallbacks) for every colour, absint()/max()/min() clamps for integers, fixed literal branches for $card_extra; $uid is internally generated. Column 0 + closing tag so this line emits zero bytes. ?>
        <style>
            .<?php echo $uid; ?>-btn {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                position: relative;
                cursor: pointer;
                background: <?php echo $icon_bg; ?>;
                border: none;
                padding: 6px;
                border-radius: 50%;
                transition: transform 0.2s ease;
                line-height: 1;
            }
            .<?php echo $uid; ?>-btn:hover {
                transform: scale(1.1);
            }
            .<?php echo $uid; ?>-btn:focus-visible,
            .<?php echo $uid; ?>-card-atc:focus-visible,
            .<?php echo $uid; ?>-card-remove:focus-visible {
                outline: none;
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
            }
            .<?php echo $uid; ?>-btn .olo-wl-heart-outline,
            .<?php echo $uid; ?>-btn .olo-wl-heart-filled {
                display: inline-flex;
            }
            .<?php echo $uid; ?>-btn .olo-wl-heart-filled {
                display: none;
            }
            .<?php echo $uid; ?>-btn.is-active .olo-wl-heart-outline {
                display: none;
            }
            .<?php echo $uid; ?>-btn.is-active .olo-wl-heart-filled {
                display: inline-flex;
            }
            .<?php echo $uid; ?>-badge {
                position: absolute;
                top: -4px;
                right: -6px;
                min-width: 16px;
                height: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: <?php echo $badge_bg; ?>;
                color: <?php echo $badge_color; ?>;
                font-size: 10px;
                font-weight: 700;
                border-radius: 8px;
                padding: 0 4px;
                line-height: 1;
            }
            .<?php echo $uid; ?>-grid {
                display: grid;
                grid-template-columns: repeat(<?php echo (int) $cols; ?>, 1fr);
                gap: <?php echo (int) $gap; ?>px;
                margin-top: 20px;
            }
            @media (max-width: 960px) {
                .<?php echo $uid; ?>-grid { grid-template-columns: repeat(<?php echo (int) $cols_t; ?>, 1fr); }
            }
            @media (max-width: 640px) {
                .<?php echo $uid; ?>-grid { grid-template-columns: repeat(<?php echo (int) $cols_m; ?>, 1fr); }
            }
            .<?php echo $uid; ?>-card {
                background: var(--olo-color-background, #fff);
                border-radius: 8px;
                overflow: hidden;
                <?php echo $card_extra; ?>
            }
            .<?php echo $uid; ?>-card img {
                width: 100%;
                height: auto;
                display: block;
            }
            .<?php echo $uid; ?>-card-body {
                padding: 14px;
            }
            .<?php echo $uid; ?>-card-title {
                font-size: 14px;
                font-weight: 600;
                margin: 0 0 6px;
                color: <?php echo $title_color; ?>;
            }
            .<?php echo $uid; ?>-card-title a {
                color: inherit;
                text-decoration: none;
            }
            .<?php echo $uid; ?>-card-title a:hover {
                text-decoration: underline;
            }
            .<?php echo $uid; ?>-card-price {
                font-size: 15px;
                font-weight: 700;
                color: <?php echo $price_color; ?>;
                margin-bottom: 10px;
            }
            .<?php echo $uid; ?>-card-actions {
                display: flex;
                gap: 8px;
            }
            .<?php echo $uid; ?>-card-atc {
                flex: 1;
                padding: 8px 12px;
                background: <?php echo $btn_bg; ?>;
                color: <?php echo $btn_color; ?>;
                border: none;
                border-radius: 4px;
                font-size: 12px;
                font-weight: 600;
                text-align: center;
                text-decoration: none;
                cursor: pointer;
                transition: opacity 0.2s;
            }
            .<?php echo $uid; ?>-card-atc:hover { opacity: 0.9; }
            .<?php echo $uid; ?>-card-remove {
                padding: 8px 12px;
                background: color-mix(in srgb, var(--olo-color-error, #b42318) 12%, #fff);
                color: var(--olo-color-error, #b42318);
                border: none;
                border-radius: 4px;
                font-size: 12px;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.2s;
            }
            .<?php echo $uid; ?>-card-remove:hover { background: color-mix(in srgb, var(--olo-color-error, #b42318) 20%, #fff); }
            .<?php echo $uid; ?>-empty {
                text-align: center;
                padding: 40px 20px;
                color: var(--olo-color-text-muted, #9CA3AF);
                font-size: 14px;
            }
        </style>
<?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped -- column 0 + closing tag so this line emits zero bytes ?>

        <div class="<?php echo esc_attr( $uid ); ?>-wrap">
            <!-- Wishlist Toggle Button -->
            <button class="<?php echo esc_attr( $uid ); ?>-btn" data-olo-wl-toggle title="<?php echo esc_attr( olobuild_t( 'Wishlist' ) ); ?>">
                <span class="olo-wl-heart-outline"><?php echo $heart_outline; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup built above from a clamped absint() size and a safe_color_css()-validated color ?></span>
                <span class="olo-wl-heart-filled"><?php echo $heart_filled; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup built above from a clamped absint() size and a safe_color_css()-validated color ?></span>
                <?php if ( ! empty( $s['show_count'] ) ) : ?>
                <span class="<?php echo esc_attr( $uid ); ?>-badge" data-olo-wl-count>0</span>
                <?php endif; ?>
            </button>

            <?php if ( $show_grid ) : ?>
            <!-- Wishlist Grid -->
            <div class="<?php echo esc_attr( $uid ); ?>-grid" data-olo-wl-grid style="display:none"></div>
            <div class="<?php echo esc_attr( $uid ); ?>-empty" data-olo-wl-empty style="display:none">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--olo-color-border, #D1D5DB)" stroke-width="1.5"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                <div style="margin-top:12px"><?php echo $empty_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped with esc_html() at assignment above ?></div>
            </div>
            <?php endif; ?>
        </div>

        <script>
        (function(){
            var wrap = document.querySelector('.<?php echo esc_js( $uid ); ?>-wrap');
            if(!wrap){return}

            var COOKIE_NAME = 'olo_woo_wishlist';
            var toggleBtn   = wrap.querySelector('[data-olo-wl-toggle]');
            var countEl     = wrap.querySelector('[data-olo-wl-count]');
            var gridEl      = wrap.querySelector('[data-olo-wl-grid]');
            var emptyEl     = wrap.querySelector('[data-olo-wl-empty]');
            var restNonce   = '<?php echo esc_js( wp_create_nonce( 'wp_rest' ) ); ?>';
            var ajaxUrl     = '<?php echo esc_js( rest_url( 'olobuild/v1/woo-wishlist-products' ) ); ?>';
            var removeText  = '<?php echo esc_js( $remove_text ); ?>';
            var atcText     = '<?php echo esc_js( olobuild_t( 'Aggiungi al carrello' ) ); ?>';

            /* Cookie helpers */
            function getWishlist(){
                var match = document.cookie.match(new RegExp('(?:^|;\\s*)' + COOKIE_NAME + '=([^;]*)'));
                if(!match){return []}
                try{ return JSON.parse(decodeURIComponent(match[1])); }catch(e){ return []; }
            }
            function setWishlist(arr){
                var val = encodeURIComponent(JSON.stringify(arr));
                document.cookie = COOKIE_NAME + '=' + val + ';path=/;max-age=' + (365*86400) + ';SameSite=Lax';
            }

            /* Get current product ID from page context */
            function getCurrentProductId(){
                var body = document.body;
                if(!body){return 0}
                var cls = body.className;
                var m = cls.match(/postid-(\d+)/);
                if(m){return parseInt(m[1])}
                /* Fallback: look for add-to-cart form */
                var form = document.querySelector('form.cart input[name="add-to-cart"]');
                if(form){return parseInt(form.value) || 0}
                return 0;
            }

            function updateCount(){
                var list = getWishlist();
                if(countEl){ countEl.textContent = list.length; }
                /* Update toggle button state for current product */
                var pid = getCurrentProductId();
                if(pid > 0){
                    if(list.indexOf(pid) !== -1){
                        toggleBtn.classList.add('is-active');
                    } else {
                        toggleBtn.classList.remove('is-active');
                    }
                }
            }

            /* Toggle current product in/out of wishlist */
            if(toggleBtn){
                toggleBtn.addEventListener('click', function(){
                    var pid = getCurrentProductId();
                    if(!pid){return}
                    var list = getWishlist();
                    var idx  = list.indexOf(pid);
                    if(idx !== -1){
                        list.splice(idx, 1);
                    } else {
                        list.push(pid);
                    }
                    setWishlist(list);
                    updateCount();
                    if(gridEl){ loadGrid(); }
                });
            }

            /* Load wishlist grid */
            function loadGrid(){
                var list = getWishlist();
                if(!gridEl){return}
                if(list.length === 0){
                    gridEl.style.display = 'none';
                    if(emptyEl){ emptyEl.style.display = 'block'; }
                    return;
                }
                if(emptyEl){ emptyEl.style.display = 'none'; }

                fetch(ajaxUrl + '?ids=' + list.join(','), {
                    headers: { 'X-WP-Nonce': restNonce }
                })
                .then(function(r){ return r.json(); })
                .then(function(products){
                    if(!products.length){
                        gridEl.style.display = 'none';
                        if(emptyEl){ emptyEl.style.display = 'block'; }
                        return;
                    }
                    var html = '';
                    products.forEach(function(p){
                        html += '<div class="<?php echo esc_js( $uid ); ?>-card" data-wl-pid="' + p.id + '">';
                        if(p.image){ html += '<a href="' + p.url + '"><img src="' + p.image + '" alt="' + p.title + '" /></a>'; }
                        html += '<div class="<?php echo esc_js( $uid ); ?>-card-body">';
                        html += '<div class="<?php echo esc_js( $uid ); ?>-card-title"><a href="' + p.url + '">' + p.title + '</a></div>';
                        html += '<div class="<?php echo esc_js( $uid ); ?>-card-price">' + p.price_html + '</div>';
                        html += '<div class="<?php echo esc_js( $uid ); ?>-card-actions">';
                        html += '<a href="' + p.add_to_cart_url + '" class="<?php echo esc_js( $uid ); ?>-card-atc">' + atcText + '</a>';
                        html += '<button class="<?php echo esc_js( $uid ); ?>-card-remove" data-wl-remove="' + p.id + '">' + removeText + '</button>';
                        html += '</div></div></div>';
                    });
                    gridEl.innerHTML = html;
                    gridEl.style.display = 'grid';

                    /* Remove buttons */
                    gridEl.querySelectorAll('[data-wl-remove]').forEach(function(btn){
                        btn.addEventListener('click', function(){
                            var rid = parseInt(btn.getAttribute('data-wl-remove'));
                            var list = getWishlist();
                            var idx = list.indexOf(rid);
                            if(idx !== -1){ list.splice(idx, 1); }
                            setWishlist(list);
                            updateCount();
                            loadGrid();
                        });
                    });
                })
                .catch(function(){
                    gridEl.innerHTML = '<div style="padding:20px;color:var(--olo-color-danger, #EF4444)">' + <?php echo wp_json_encode( olobuild_t( 'Errore caricamento wishlist' ) ); ?> + '</div>';
                    gridEl.style.display = 'block';
                });
            }

            /* Init */
            updateCount();
            if(gridEl){ loadGrid(); }
        })();
        </script>
        <?php

        // Register REST endpoint for wishlist products
        $this->register_wishlist_endpoint();

                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS built by Olobuild_Tile_Base::build_border_css() from sanitized border settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS built by Olobuild_Tile_Base border helpers from sanitized border settings
        }
        return ob_get_clean();
    }

    /**
     * Register REST endpoint to fetch wishlist product data.
     */
    private function register_wishlist_endpoint() {
        static $registered = false;
        if ( $registered ) {
            return;
        }
        $registered = true;

        add_action( 'rest_api_init', function() {
            register_rest_route( 'olobuild/v1', '/woo-wishlist-products', [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_wishlist_products' ],
                'permission_callback' => '__return_true',
            ] );
        } );
    }

    /**
     * REST callback: return wishlist product data.
     */
    public function get_wishlist_products( $request ) {
        $ids_raw = sanitize_text_field( $request->get_param( 'ids' ) );
        if ( empty( $ids_raw ) ) {
            return rest_ensure_response( [] );
        }

        $ids      = array_map( 'absint', explode( ',', $ids_raw ) );
        $ids      = array_filter( $ids );
        $products = [];

        foreach ( $ids as $pid ) {
            $product = wc_get_product( $pid );
            if ( ! $product ) {
                continue;
            }
            if ( $product->get_status() !== 'publish' ) {
                continue;
            }
            $products[] = [
                'id'               => $pid,
                'title'            => $product->get_name(),
                'url'              => get_permalink( $pid ),
                'image'            => get_the_post_thumbnail_url( $pid, 'woocommerce_thumbnail' ) ?: '',
                'price_html'       => $product->get_price_html(),
                'add_to_cart_url'  => $product->add_to_cart_url(),
            ];
        }

        return rest_ensure_response( $products );
    }
}
