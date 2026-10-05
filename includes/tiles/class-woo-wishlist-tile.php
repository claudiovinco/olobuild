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

    /**
     * Le tile si istanziano a ogni richiesta (plugins_loaded): qui la registrazione arriva anche
     * alle richieste che non rendono la tile (REST, wc-ajax). Fatta solo nel render, la rotta non
     * esisteva proprio nelle chiamate AJAX della tile e rispondeva 404.
     */
    public function __construct() {
        $this->register_wishlist_endpoint();
    }

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

        /*
         * Icona, stile e sette colori dell'inspector non li leggeva nessuno: il PHP usava altre
         * chiavi (btn_bg, btn_color, senza controllo) e disegnava sempre il cuore. Ora ogni colore
         * legge prima la chiave dell'inspector, poi quella storica, poi il token del tema.
         */
        $icon_size   = max( 14, min( 48, absint( $s['icon_size'] ) ) );
        // TOKEN-FIRST: cuore/badge/CTA ereditano il brand se l'utente non sceglie.
        $icon_set    = $this->safe_color_css( $s['icon_color'] );
        $icon_color  = $icon_set ?: 'var(--olo-color-primary, #e1474f)';
        $icon_active = $this->safe_color_css( $s['icon_color_active'] ?? '' ) ?: $icon_color;
        $icon_bg     = $this->safe_color_css( $s['icon_bg'] ) ?: 'transparent';
        $badge_bg    = $this->safe_color_css( $s['badge_bg'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $badge_color = $this->safe_color_css( $s['badge_color'] ) ?: 'var(--olo-color-on-primary, #ffffff)';
        $title_color = $this->safe_color_css( $s['title_color'] ) ?: 'var(--olo-color-text, #1f2937)';
        $price_color = $this->safe_color_css( $s['price_color'] ) ?: 'var(--olo-color-text, #1f2937)';
        $empty_color = $this->safe_color_css( $s['empty_color'] ?? '' ) ?: 'var(--olo-color-text-muted, #9CA3AF)';
        $btn_bg      = $this->safe_color_css( $s['button_bg'] ?? '' ) ?: ( $this->safe_color_css( $s['btn_bg'] ) ?: 'var(--olo-color-primary, #e1474f)' );
        $btn_color   = $this->safe_color_css( $s['button_color'] ?? '' ) ?: ( $this->safe_color_css( $s['btn_color'] ) ?: 'var(--olo-color-on-primary, #ffffff)' );
        $cols        = max( 1, min( 6, absint( $s['columns'] ) ) );
        $cols_t      = max( 1, min( 4, absint( $s['columns_tablet'] ) ) );
        $cols_m      = max( 1, min( 2, absint( $s['columns_mobile'] ) ) );
        $gap         = absint( $s['gap'] );
        $show_grid   = ! empty( $s['show_grid'] );
        // Voci della card: chiavi dell'inspector senza default nel PHP, accese se mancano.
        $card_price  = ! isset( $s['show_price'] ) || ! empty( $s['show_price'] );
        $card_atc    = ! isset( $s['show_add_to_cart'] ) || ! empty( $s['show_add_to_cart'] );
        $card_remove = ! isset( $s['show_remove'] ) || ! empty( $s['show_remove'] );

        // Stile del pulsante. 'outline', il default storico del PHP, non è fra le scelte
        // dell'inspector e si è sempre disegnato come «Solo icona»: resta così.
        $stile = in_array( $s['style'], [ 'icon', 'icon-text', 'button' ], true ) ? $s['style'] : 'icon';
        // Nel pulsante pieno l'icona senza un colore scelto prende quello del testo: col primario
        // su sfondo primario sparirebbe.
        if ( 'button' === $stile ) {
            $icon_color  = $icon_set ?: $btn_color;
            $icon_active = $this->safe_color_css( $s['icon_color_active'] ?? '' ) ?: $icon_color;
        }

        $card_extra = '';
        if ( $s['card_style'] === 'shadow' ) {
            $card_extra = 'box-shadow:0 1px 3px rgba(0,0,0,0.1),0 1px 2px rgba(0,0,0,0.06);';
        } elseif ( $s['card_style'] === 'border' ) {
            $card_extra = 'border:1px solid var(--olo-color-border, #E5E7EB);';
        }

        $empty_text  = esc_html( $s['empty_text'] ?: olobuild_t( 'La tua wishlist è vuota' ) );
        $remove_text = esc_html( $s['remove_text'] ?: olobuild_t( 'Rimuovi' ) );

        // Icone (stessa griglia 24x24, tratto 2): vuota e piena, il colore lo dà il CSS (currentColor).
        $forme = [
            'heart'    => '<path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>',
            'star'     => '<path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>',
            'bookmark' => '<path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>',
        ];
        $forma         = $forme[ (string) $s['icon'] ] ?? $forme['heart'];
        $svg_attr      = 'width="' . $icon_size . '" height="' . $icon_size . '" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"';
        $heart_outline = '<svg ' . $svg_attr . ' fill="none">' . $forma . '</svg>';
        $heart_filled  = '<svg ' . $svg_attr . ' fill="currentColor">' . $forma . '</svg>';

        // Testi del pulsante con etichetta: «Wishlist» fuori dalle pagine prodotto, sulla pagina
        // prodotto lo script mette «Aggiungi alla wishlist» / «Nella wishlist».
        $txt_base = olobuild_t( 'Wishlist' );
        $txt_add  = olobuild_t( 'Aggiungi alla wishlist' );
        $txt_in   = olobuild_t( 'Nella wishlist' );

        // Il prodotto della pagina lo dice WordPress: la classe postid-N del body c'è su ogni
        // articolo, e il cuore cliccato su un post finiva nella wishlist (e nel contatore).
        $pid_pagina = is_singular( 'product' ) ? (int) get_queried_object_id() : 0;

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
            .<?php echo $uid; ?>-btn .olo-wl-heart-outline { color: <?php echo $icon_color; ?>; }
            .<?php echo $uid; ?>-btn .olo-wl-heart-filled { color: <?php echo $icon_active; ?>; }
            <?php if ( 'icon-text' === $stile ) : ?>
            .<?php echo $uid; ?>-btn {
                gap: 8px;
                padding: 6px 10px;
                border-radius: 8px;
                color: var(--olo-color-text, #1f2937);
                font-size: 14px;
                font-weight: 600;
            }
            <?php elseif ( 'button' === $stile ) : ?>
            .<?php echo $uid; ?>-btn {
                gap: 8px;
                padding: 10px 18px;
                border-radius: 8px;
                background: <?php echo $btn_bg; ?>;
                color: <?php echo $btn_color; ?>;
                font-size: 14px;
                font-weight: 600;
                transition: opacity 0.2s ease;
            }
            <?php endif; ?>
            <?php if ( 'icon' !== $stile ) : ?>
            .<?php echo $uid; ?>-btn:hover {
                transform: none;
                opacity: 0.9;
            }
            <?php endif; ?>
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
            /* «.wrap a.»: .olo-template a (colore dei link del tema, anche in hover) pesava di più
               della sola classe, e il testo del pulsante prendeva il colore del suo sfondo. */
            .<?php echo $uid; ?>-wrap a.<?php echo $uid; ?>-card-atc {
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
            .<?php echo $uid; ?>-wrap a.<?php echo $uid; ?>-card-atc:hover { opacity: 0.9; }
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
                color: <?php echo $empty_color; ?>;
                font-size: 14px;
            }
        </style>
<?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped -- column 0 + closing tag so this line emits zero bytes ?>

        <div class="<?php echo esc_attr( $uid ); ?>-wrap">
            <!-- Wishlist Toggle Button -->
            <button type="button" class="<?php echo esc_attr( $uid ); ?>-btn" data-olo-wl-toggle title="<?php echo esc_attr( $txt_base ); ?>"<?php if ( 'icon' === $stile ) : ?> aria-label="<?php echo esc_attr( $txt_base ); ?>"<?php endif; ?><?php if ( $pid_pagina > 0 ) : ?> aria-pressed="false"<?php endif; ?>>
                <span class="olo-wl-heart-outline"><?php echo $heart_outline; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup built above from a clamped absint() size and a fixed shape whitelist ?></span>
                <span class="olo-wl-heart-filled"><?php echo $heart_filled; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup built above from a clamped absint() size and a fixed shape whitelist ?></span>
                <?php if ( 'icon' !== $stile ) : ?>
                <span class="<?php echo esc_attr( $uid ); ?>-label" data-olo-wl-label><?php echo esc_html( $pid_pagina > 0 ? $txt_add : $txt_base ); ?></span>
                <?php endif; ?>
                <?php if ( ! empty( $s['show_count'] ) ) : ?>
                <span class="<?php echo esc_attr( $uid ); ?>-badge" data-olo-wl-count>0</span>
                <?php endif; ?>
            </button>

            <?php if ( $show_grid ) : ?>
            <!-- Wishlist Grid -->
            <div class="<?php echo esc_attr( $uid ); ?>-grid" data-olo-wl-grid style="display:none"></div>
            <div class="<?php echo esc_attr( $uid ); ?>-empty" data-olo-wl-empty style="display:none">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--olo-color-border, #D1D5DB)" stroke-width="1.5" aria-hidden="true" focusable="false"><?php echo $forma; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed SVG path from the $forme whitelist ?></svg>
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
            var labelEl     = wrap.querySelector('[data-olo-wl-label]');
            var pagePid     = <?php echo (int) $pid_pagina; ?>;
            var TXT_BASE    = <?php echo wp_json_encode( $txt_base ); ?>;
            var TXT_ADD     = <?php echo wp_json_encode( $txt_add ); ?>;
            var TXT_IN      = <?php echo wp_json_encode( $txt_in ); ?>;
            var SHOW_PRICE  = <?php echo $card_price ? 'true' : 'false'; ?>;
            var SHOW_ATC    = <?php echo $card_atc ? 'true' : 'false'; ?>;
            var SHOW_REMOVE = <?php echo $card_remove ? 'true' : 'false'; ?>;

            /* Testo dal REST dentro l'HTML della card. \x26 al posto della e commerciale:
               WordPress la riscrive negli script in linea e il codice si rompe. */
            var ESC = { '\x26': '\x26amp;', '<': '\x26lt;', '>': '\x26gt;', '"': '\x26quot;', "'": '\x26#39;' };
            function esc(v){ return String(v == null ? '' : v).replace(/[\x26<>"']/g, function(c){ return ESC[c]; }); }

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

            /* Prodotto della pagina: lo dice il PHP (is_singular product); poi il modulo carrello */
            function getCurrentProductId(){
                if(pagePid > 0){return pagePid}
                var form = document.querySelector('form.cart input[name="add-to-cart"], form.cart button[name="add-to-cart"]');
                if(form){return parseInt(form.value) || 0}
                return 0;
            }

            function updateCount(){
                var list = getWishlist();
                if(countEl){ countEl.textContent = list.length; }
                /* Update toggle button state for current product */
                var pid = getCurrentProductId();
                var dentro = pid > 0 ? list.indexOf(pid) !== -1 : false;
                if(pid > 0){
                    toggleBtn.classList.toggle('is-active', dentro);
                    toggleBtn.setAttribute('aria-pressed', dentro ? 'true' : 'false');
                }
                if(labelEl){
                    labelEl.textContent = pid > 0 ? (dentro ? TXT_IN : TXT_ADD) : TXT_BASE;
                }
            }

            /* Più tile nella stessa pagina (il cuore nell'header e quello del prodotto): chi
               cambia la lista lo dice alle altre, che aggiornano contatore e griglia. */
            document.addEventListener('olo:wishlist', function(e){
                if(e.detail === wrap){return}
                updateCount();
                if(gridEl){ loadGrid(); }
            });
            function avvisaAltre(){
                try { document.dispatchEvent(new CustomEvent('olo:wishlist', { detail: wrap })); } catch(err){}
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
                    avvisaAltre();
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
                    /* «Mostra prezzo / aggiungi al carrello / rimuovi» dell'inspector: prima
                       la card li disegnava sempre. Titolo, link e immagine passano da esc(). */
                    var html = '';
                    products.forEach(function(p){
                        var id = parseInt(p.id) || 0;
                        html += '<div class="<?php echo esc_js( $uid ); ?>-card" data-wl-pid="' + id + '">';
                        if(p.image){ html += '<a href="' + esc(p.url) + '"><img src="' + esc(p.image) + '" alt="' + esc(p.title) + '" /></a>'; }
                        html += '<div class="<?php echo esc_js( $uid ); ?>-card-body">';
                        html += '<div class="<?php echo esc_js( $uid ); ?>-card-title"><a href="' + esc(p.url) + '">' + esc(p.title) + '</a></div>';
                        if(SHOW_PRICE){ html += '<div class="<?php echo esc_js( $uid ); ?>-card-price">' + p.price_html + '</div>'; }
                        if(SHOW_ATC || SHOW_REMOVE){
                            html += '<div class="<?php echo esc_js( $uid ); ?>-card-actions">';
                            /* atcText e removeText arrivano già in entità HTML (esc_js / esc_html). */
                            if(SHOW_ATC){ html += '<a href="' + esc(p.add_to_cart_url) + '" class="<?php echo esc_js( $uid ); ?>-card-atc">' + atcText + '</a>'; }
                            if(SHOW_REMOVE){ html += '<button type="button" class="<?php echo esc_js( $uid ); ?>-card-remove" data-wl-remove="' + id + '">' + removeText + '</button>'; }
                            html += '</div>';
                        }
                        html += '</div></div>';
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
                            avvisaAltre();
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
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}-wrap", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}-wrap", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}-wrap{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS built by Olobuild_Tile_Base::build_border_css() from sanitized border settings; $uid is internally generated
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
