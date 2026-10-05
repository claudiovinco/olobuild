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

    /**
     * Le tile si istanziano a ogni richiesta (plugins_loaded): qui la registrazione arriva anche
     * alle richieste che non rendono la tile (REST, wc-ajax). Fatta solo nel render, la rotta non
     * esisteva proprio nelle chiamate AJAX della tile e rispondeva 404.
     */
    public function __construct() {
        $this->register_qv_endpoint();
    }

    public function get_controls() {
        return [];
    }

    /**
     * Un interruttore «Mostra…» dell'inspector. Alcuni hanno un nome diverso dalla chiave storica
     * del PHP (show_description ↔ show_excerpt, show_sku e show_categories ↔ show_meta): vince la
     * chiave dell'inspector se è salvata, altrimenti quella storica, altrimenti acceso.
     */
    private function mostra( $s, $chiave, $storica = '' ) {
        if ( isset( $s[ $chiave ] ) ) {
            return ! empty( $s[ $chiave ] );
        }
        if ( $storica !== '' && isset( $s[ $storica ] ) ) {
            return ! empty( $s[ $storica ] );
        }
        return true;
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
        $img_w     = max( 30, min( 70, absint( $s['image_width'] ) ) );
        $btn_text  = $s['button_text'] ?: olobuild_t( 'Vista rapida' );

        /*
         * I colori dell'inspector (velo, finestra, titolo, prezzo, pulsante, chiudi) non li leggeva
         * nessuno, e nemmeno quelli che il PHP leggeva arrivavano alla finestra: il contenuto dal
         * REST aveva classi diverse dal CSS della tile e stili in linea fissi. Ora il contenuto ha
         * solo classi e questo CSS lo veste.
         */
        $overlay   = $this->safe_color_css( $s['overlay_color'] ?? '' ) ?: 'rgba(0,0,0,0.55)';
        $modal_bg  = $this->safe_color_css( $s['modal_bg'] ?? '' ) ?: 'var(--olo-color-background, #FFFFFF)';
        $title_col = $this->safe_color_css( $s['title_color'] ?? '' ) ?: $text_col;
        $price_col = $this->safe_color_css( $s['price_color'] ?? '' ) ?: $text_col;
        $close_col = $this->safe_color_css( $s['close_color'] ?? '' ) ?: 'var(--olo-color-text, #374151)';
        $btn_bg    = $this->safe_color_css( $s['button_bg'] ?? '' );
        $btn_fg    = $this->safe_color_css( $s['button_color'] ?? '' );

        // «Dimensione modale» dell'inspector; senza scelta la larghezza storica (modal_width).
        $modal_size = (string) ( $s['modal_size'] ?? '' );
        $misure     = [ 'small' => '560px', 'large' => '1040px', 'full' => '100%' ];
        $modal_max  = $misure[ $modal_size ] ?? ( max( 400, min( 1200, absint( $s['modal_width'] ) ) ) . 'px' );

        /*
         * «Stile pulsante»: l'inspector offriva default/outline/icon-only/text, il PHP capiva
         * filled/outline/text. 'default' si è sempre disegnato contornato e resta così.
         */
        $btn_style = (string) $s['button_style'];
        if ( 'default' === $btn_style ) {
            $btn_style = 'outline';
        }
        if ( ! in_array( $btn_style, [ 'filled', 'outline', 'text', 'icon-only' ], true ) ) {
            $btn_style = 'outline';
        }
        // Sfondo chiaro velato su foto: dal token dello sfondo del tema, non bianco fisso (su un tema
        // scuro il testo chiaro restava su un bottone bianco).
        $velo_chiaro = 'color-mix(in srgb, var(--olo-color-background, #FFFFFF) 95%, transparent)';
        if ( 'filled' === $btn_style ) {
            $trig_bg     = $btn_bg ?: $accent;
            $trig_fg     = $btn_fg ?: 'var(--olo-color-primary-contrast, #FFFFFF)';
            $trig_border = 'none';
        } elseif ( 'text' === $btn_style ) {
            $trig_bg     = $btn_bg ?: 'transparent';
            $trig_fg     = $btn_fg ?: $accent;
            $trig_border = 'none';
        } else {
            $trig_bg     = $btn_bg ?: $velo_chiaro;
            $trig_fg     = $btn_fg ?: $text_col;
            $trig_border = '1px solid var(--olo-color-border, #E5E7EB)';
        }

        // Parti della finestra spente dagli interruttori «Mostra…» (prima non agiva nessuno).
        $nascondi = [];
        if ( ! $this->mostra( $s, 'show_gallery' ) ) {
            $nascondi[] = '.olo-qv-thumbs';
        }
        if ( ! $this->mostra( $s, 'show_price' ) ) {
            $nascondi[] = '.olo-qv-price';
        }
        if ( ! $this->mostra( $s, 'show_rating' ) ) {
            $nascondi[] = '.olo-qv-stars';
        }
        if ( ! $this->mostra( $s, 'show_description', 'show_excerpt' ) ) {
            $nascondi[] = '.olo-qv-excerpt';
        }
        if ( ! $this->mostra( $s, 'show_add_to_cart' ) ) {
            $nascondi[] = '.olo-qv-atc';
        }
        $con_sku = $this->mostra( $s, 'show_sku', 'show_meta' );
        $con_cat = $this->mostra( $s, 'show_categories', 'show_meta' );
        if ( ! $con_sku ) {
            $nascondi[] = '.olo-qv-sku';
        }
        if ( ! $con_cat ) {
            $nascondi[] = '.olo-qv-cats';
        }
        if ( ! $con_sku && ! $con_cat ) {
            $nascondi[] = '.olo-qv-meta';
        }

        // Icona dello stile «Solo icona» (occhio, set SVG a tratto 2).
        $occhio = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';

        // Register REST endpoint
        $this->register_qv_endpoint();

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() with token fallbacks for colours, absint()+max/min clamps and a fixed map for sizes, in_array() whitelist for the button style, fixed class names for the hidden parts, internal wp_rand() uid. ?>
        <style>
            .<?php echo $uid; ?>-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: <?php echo $overlay; ?>;
                z-index: 99999;
                justify-content: center;
                align-items: center;
                padding: 20px;
            }
            .<?php echo $uid; ?>-overlay.is-open {
                display: flex;
            }
            .<?php echo $uid; ?>-modal {
                background: <?php echo $modal_bg; ?>;
                color: <?php echo $text_col; ?>;
                border-radius: 12px;
                max-width: <?php echo $modal_max; ?>;
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
                color: <?php echo $close_col; ?>;
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
            .<?php echo $uid; ?>-body .olo-qv-grid {
                display: grid;
                grid-template-columns: minmax(0, <?php echo (int) $img_w; ?>%) minmax(0, 1fr);
                gap: 30px;
            }
            @media (max-width: 640px) {
                .<?php echo $uid; ?>-body .olo-qv-grid {
                    grid-template-columns: minmax(0, 1fr);
                }
            }
            .<?php echo $uid; ?>-body .olo-qv-main-img {
                width: 100%;
                height: auto;
                border-radius: 8px;
                display: block;
            }
            .<?php echo $uid; ?>-body .olo-qv-thumbs {
                display: flex;
                gap: 6px;
                margin-top: 8px;
                overflow-x: auto;
            }
            .<?php echo $uid; ?>-body .olo-qv-thumb {
                padding: 0;
                border: 2px solid transparent;
                border-radius: 6px;
                background: none;
                line-height: 0;
                cursor: pointer;
                transition: border-color 0.2s;
                flex-shrink: 0;
            }
            .<?php echo $uid; ?>-body .olo-qv-thumb img {
                width: 56px;
                height: 56px;
                object-fit: cover;
                border-radius: 4px;
                display: block;
            }
            .<?php echo $uid; ?>-body .olo-qv-thumb:hover,
            .<?php echo $uid; ?>-body .olo-qv-thumb.is-active {
                border-color: <?php echo $accent; ?>;
            }
            .<?php echo $uid; ?>-body .olo-qv-title {
                font-size: 22px;
                font-weight: 700;
                margin: 0 0 10px;
                color: <?php echo $title_col; ?>;
            }
            .<?php echo $uid; ?>-body .olo-qv-price {
                font-size: 20px;
                font-weight: 600;
                margin-bottom: 12px;
                color: <?php echo $price_col; ?>;
            }
            .<?php echo $uid; ?>-body .olo-qv-price del {
                opacity: 0.5;
                font-size: 16px;
            }
            .<?php echo $uid; ?>-body .olo-qv-price ins {
                text-decoration: none;
                background: none;
                color: inherit;
            }
            .<?php echo $uid; ?>-body .olo-qv-stars {
                display: flex;
                align-items: center;
                gap: 2px;
                margin-bottom: 12px;
            }
            .<?php echo $uid; ?>-body .olo-qv-star {
                font-size: 16px;
                color: var(--olo-color-border, #E5E7EB);
            }
            .<?php echo $uid; ?>-body .olo-qv-star.is-on {
                color: var(--olo-color-warning, #F59E0B);
            }
            .<?php echo $uid; ?>-body .olo-qv-reviews {
                font-size: 12px;
                color: var(--olo-color-text-muted, #9CA3AF);
                margin-left: 4px;
            }
            .<?php echo $uid; ?>-body .olo-qv-excerpt {
                font-size: 14px;
                line-height: 1.6;
                color: var(--olo-color-text-muted, #9CA3AF);
                margin-bottom: 16px;
            }
            .<?php echo $uid; ?>-body .olo-qv-atc {
                display: flex;
                gap: 10px;
                align-items: center;
                margin-top: 16px;
            }
            .<?php echo $uid; ?>-body .olo-qv-qty {
                width: 60px;
                height: 40px;
                text-align: center;
                border: 1px solid var(--olo-color-border, #E5E7EB);
                border-radius: 6px;
                font-size: 14px;
            }
            .<?php echo $uid; ?>-body .olo-qv-atc-btn {
                display: inline-block;
                padding: 10px 24px;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                font-weight: 600;
                font-size: 14px;
                line-height: 1.4;
                text-decoration: none;
                background: <?php echo $accent; ?>;
                color: var(--olo-color-primary-contrast, #FFFFFF);
                transition: opacity 0.2s;
            }
            .<?php echo $uid; ?>-body .olo-qv-atc-btn:hover {
                opacity: 0.9;
            }
            .<?php echo $uid; ?>-body .olo-qv-stock-out {
                color: var(--olo-color-danger, #EF4444);
                font-weight: 600;
                margin-top: 12px;
                font-size: 14px;
            }
            .<?php echo $uid; ?>-body .olo-qv-meta {
                font-size: 12px;
                color: var(--olo-color-text-muted, #9CA3AF);
                margin-top: 16px;
                line-height: 1.8;
            }
            .<?php echo $uid; ?>-body .olo-qv-link {
                display: inline-block;
                margin-top: 16px;
                font-size: 13px;
                color: <?php echo $accent; ?>;
                text-decoration: none;
            }
            .<?php echo $uid; ?>-body .olo-qv-link:hover {
                text-decoration: underline;
            }
            .<?php echo $uid; ?>-close:focus-visible,
            .<?php echo $uid; ?>-body .olo-qv-thumb:focus-visible,
            .<?php echo $uid; ?>-body .olo-qv-atc-btn:focus-visible,
            .<?php echo $uid; ?>-body .olo-qv-link:focus-visible {
                outline: 2px solid <?php echo $accent; ?>;
                outline-offset: 2px;
            }
            <?php if ( $nascondi ) : ?>
            <?php echo '.' . $uid . '-body ' . implode( ', .' . $uid . '-body ', $nascondi ); ?> {
                display: none;
            }
            <?php endif; ?>
            /*
             * Pulsante sulle card prodotto. Prima la regola era su .olo-qv-trigger, uguale per
             * tutte le istanze: l'ultima tile della pagina decideva lo stile di ogni pulsante.
             * Ora ognuno porta la classe della sua tile.
             */
            .<?php echo $uid; ?>-trigger {
                background: <?php echo $trig_bg; ?>;
                color: <?php echo $trig_fg; ?>;
                border: <?php echo $trig_border; ?>;
                padding: 6px 16px;
                border-radius: 6px;
                font-family: inherit;
                font-size: 12px;
                font-weight: 600;
                line-height: 1.4;
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
            <?php if ( 'icon-only' === $btn_style ) : ?>
            .<?php echo $uid; ?>-trigger {
                padding: 8px;
                border-radius: 50%;
                line-height: 0;
            }
            <?php endif; ?>
            .olo-woo-card-img:hover > .<?php echo $uid; ?>-trigger,
            .<?php echo $uid; ?>-trigger:focus-visible {
                opacity: 1;
            }
            .<?php echo $uid; ?>-trigger:focus-visible {
                outline: 2px solid <?php echo $accent; ?>;
                outline-offset: 2px;
            }
            /* Senza puntatore (telefono, tablet) non c'è passaggio del mouse: il pulsante resta visibile. */
            @media (hover: none) {
                .<?php echo $uid; ?>-trigger {
                    opacity: 1;
                }
            }
            @media (prefers-reduced-motion: reduce) {
                .<?php echo $uid; ?>-modal,
                .<?php echo $uid; ?>-loading svg {
                    animation: none;
                }
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <?php // Resta nella tile anche quando la finestra va nel body: dice in quale sezione sta l'istanza. ?>
        <span class="<?php echo esc_attr( $uid ); ?>-ancora" data-olo-qv-ancora="<?php echo esc_attr( $uid ); ?>" hidden></span>
        <!-- Quick View Modal Shell -->
        <div class="<?php echo esc_attr( $uid ); ?>-overlay" data-olo-qv-overlay>
            <div class="<?php echo esc_attr( $uid ); ?>-modal" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr( olobuild_t( 'Vista rapida prodotto' ) ); ?>" tabindex="-1" data-olo-qv-dialog>
                <button type="button" class="<?php echo esc_attr( $uid ); ?>-close" data-olo-qv-close aria-label="<?php echo esc_attr( olobuild_t( 'Chiudi' ) ); ?>">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
                <div class="<?php echo esc_attr( $uid ); ?>-body">
                    <div class="<?php echo esc_attr( $uid ); ?>-loading" data-olo-qv-loading>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
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
            <?php if ( empty( $settings['_builder_mode'] ) ) : ?>
            /* Dentro il template (transform) il velo copriva il template intero e la finestra stava
               al suo centro: all'apertura la pagina saltava lì. Sul sito il velo va nel body. */
            (<?php echo self::js_nel_body(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- costante JS interna ?>)(overlay);
            <?php endif; ?>
            var OWNER = '<?php echo esc_js( $uid ); ?>';
            var BTN_TEXT = <?php echo wp_json_encode( $btn_text ); ?>;
            var BTN_ICON = <?php echo 'icon-only' === $btn_style ? wp_json_encode( $occhio ) : "''"; ?>;
            var loading = overlay.querySelector('[data-olo-qv-loading]');
            var content = overlay.querySelector('[data-olo-qv-content]');
            var closeBtn = overlay.querySelector('[data-olo-qv-close]');
            var dialog = overlay.querySelector('[data-olo-qv-dialog]');
            var lastTrigger = null;
            var restBase = '<?php echo esc_js( rest_url( 'olobuild/v1/woo-quickview/' ) ); ?>';

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
                if(lastTrigger){ if(typeof lastTrigger.focus === 'function'){ lastTrigger.focus(); } }
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
                        if(titleEl){
                            if(dialog){
                                if(!titleEl.id){ titleEl.id = 'olo-qv-title-' + pid; }
                                dialog.setAttribute('aria-labelledby', titleEl.id);
                            }
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

                        /* ATC handler. Niente e commerciale scritta negli script in linea (WordPress la
                           riscrive in entità e il codice si rompe): \x26 nella stringa. */
                        var atcBtn = content.querySelector('[data-olo-qv-atc-btn]');
                        if(atcBtn){
                            atcBtn.addEventListener('click', function(){
                                var qty = content.querySelector('.olo-qv-qty');
                                var q = qty ? qty.value : 1;
                                window.location.href = '?add-to-cart=' + pid + '\x26quantity=' + q;
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

            /*
             * Il pulsante «Vista rapida» sulle card prodotto ([data-product-id] con .olo-woo-card-img).
             * Prima le card si cercavano una volta sola, all'avvio: quelle arrivate dopo (una griglia
             * sotto la piega, che nasce da un <template>, o rifatta nel canvas) restavano senza, e
             * una tile partita dopo vestiva le card di un'altra. Ora si decorano anche le card che
             * entrano più tardi, e ognuna riceve UN pulsante, dalla tile Quick View della sua
             * sezione o, se lì non ce n'è, dalla prima della pagina. Il clic si ascolta sul
             * documento, così funziona anche sulle card clonate.
             */
            function proprietario(card){
                var sez = card.closest('section');
                var a = sez ? sez.querySelector('[data-olo-qv-ancora]') : null;
                if(!a){ a = document.querySelector('[data-olo-qv-ancora]'); }
                return a ? a.getAttribute('data-olo-qv-ancora') : OWNER;
            }
            function decora(card){
                var gia = card.getAttribute('data-olo-qv');
                if(gia === OWNER){return}
                var imgWrap = card.querySelector('.olo-woo-card-img');
                if(!imgWrap){return}
                if(!card.getAttribute('data-product-id')){return}
                if(proprietario(card) !== OWNER){return}
                if(gia){
                    /* La card l'aveva vestita un'altra tile, partita prima di questa. */
                    var altri = card.querySelectorAll('.olo-qv-trigger');
                    for(var k = 0; k < altri.length; k++){ altri[k].parentNode.removeChild(altri[k]); }
                }
                card.setAttribute('data-olo-qv', OWNER);
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'olo-qv-trigger ' + OWNER + '-trigger';
                btn.setAttribute('data-olo-qv-owner', OWNER);
                btn.setAttribute('aria-haspopup', 'dialog');
                if(BTN_ICON){
                    btn.innerHTML = BTN_ICON;
                    btn.setAttribute('aria-label', BTN_TEXT);
                    btn.title = BTN_TEXT;
                } else {
                    btn.textContent = BTN_TEXT;
                }
                if(getComputedStyle(imgWrap).position === 'static'){ imgWrap.style.position = 'relative'; }
                imgWrap.appendChild(btn);
            }
            function decoraIn(root){
                if(!root){return}
                if(root.nodeType !== 1){return}
                if(root.hasAttribute('data-product-id')){ decora(root); }
                var cards = root.querySelectorAll('[data-product-id]');
                for(var i = 0; i < cards.length; i++){ decora(cards[i]); }
            }
            /* Pulsanti di una tile che non c'è più (nel canvas la tile ridisegnata ne crea una
               nuova): si tolgono e la card torna libera. true se ne ha tolto almeno uno. */
            function liberaOrfani(){
                var tolto = false;
                var vecchi = document.querySelectorAll('.olo-qv-trigger[data-olo-qv-owner]');
                for(var i = 0; i < vecchi.length; i++){
                    var o = vecchi[i].getAttribute('data-olo-qv-owner');
                    if(o === OWNER){continue}
                    if(document.querySelector('.' + o + '-overlay')){continue}
                    var card = vecchi[i].closest('[data-olo-qv]');
                    if(card){ card.removeAttribute('data-olo-qv'); }
                    if(vecchi[i].parentNode){ vecchi[i].parentNode.removeChild(vecchi[i]); }
                    tolto = true;
                }
                return tolto;
            }
            document.addEventListener('click', function(e){
                if(!overlay.isConnected){return}
                var b = e.target.closest ? e.target.closest('.' + OWNER + '-trigger') : null;
                if(!b){return}
                var card = b.closest('[data-product-id]');
                if(!card){return}
                e.preventDefault();
                e.stopPropagation();
                openQuickView(card.getAttribute('data-product-id'), b);
            }, true);

            liberaOrfani();
            decoraIn(document.body);
            if(window.MutationObserver){
                var mo = new MutationObserver(function(muts){
                    if(!overlay.isConnected){ mo.disconnect(); return; }
                    var tolti = false;
                    for(var i = 0; i < muts.length; i++){
                        var add = muts[i].addedNodes;
                        for(var j = 0; j < add.length; j++){ decoraIn(add[j]); }
                        if(muts[i].removedNodes.length){ tolti = true; }
                    }
                    if(tolti){ if(liberaOrfani()){ decoraIn(document.body); } }
                });
                mo.observe(document.body, { childList: true, subtree: true });
            }
        })();
        </script>
        <?php
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}-modal", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}-modal", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}-modal{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- border CSS from base-class build_border_css() (intval'd widths, safe_color_css()'d colours), internal wp_rand() uid.
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
            register_rest_route( 'olobuild/v1', '/woo-quickview/(?P<id>\d+)', [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_quickview_data' ],
                'permission_callback' => '__return_true',
            ] );
        } );
    }

    /**
     * REST callback: return product quick-view HTML.
     *
     * Solo classi, nessuno stile in linea: la tile che apre la finestra la veste col suo CSS
     * (colori, larghezza dell'immagine, colonna unica sul telefono) e spegne le parti che
     * l'inspector nasconde. Con gli stili in linea fissi nessuno di quei controlli arrivava qui.
     */
    public function get_quickview_data( $request ) {
        $pid     = intval( $request['id'] );
        $product = wc_get_product( $pid );
        // La rotta è pubblica: senza questo controllo chiunque leggeva nome, prezzo, SKU e foto
        // dei prodotti in bozza, privati, in attesa o protetti da password. Come la wishlist,
        // solo i pubblicati (per una variazione vale anche il prodotto da cui viene).
        $genitore = $product ? (int) $product->get_parent_id() : 0;
        if ( ! $product || 'publish' !== $product->get_status() || post_password_required( $pid )
            || ( $genitore && ( 'publish' !== get_post_status( $genitore ) || post_password_required( $genitore ) ) ) ) {
            return new WP_Error( 'not_found', 'Prodotto non trovato', [ 'status' => 404 ] );
        }

        $image   = get_the_post_thumbnail_url( $pid, 'large' ) ?: wc_placeholder_img_src();
        $gallery = $product->get_gallery_image_ids();
        $rating  = (float) $product->get_average_rating();

        ob_start();
        ?>
        <div class="olo-qv-grid">
            <div class="olo-qv-media">
                <img data-olo-qv-main-img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" class="olo-qv-main-img" />
                <?php if ( ! empty( $gallery ) ) : ?>
                <div class="olo-qv-thumbs">
                    <?php foreach ( array_slice( $gallery, 0, 6 ) as $gid ) :
                        $thumb_url = wp_get_attachment_image_url( $gid, 'thumbnail' );
                        $large_url = wp_get_attachment_image_url( $gid, 'large' );
                        if ( ! $thumb_url ) { continue; }
                    ?>
                    <button type="button" class="olo-qv-thumb" data-olo-qv-thumb-src="<?php echo esc_url( $large_url ); ?>" aria-label="<?php echo esc_attr( sprintf( olobuild_t( 'Mostra immagine: %s' ), $product->get_name() ) ); ?>">
                        <img src="<?php echo esc_url( $thumb_url ); ?>" alt="" />
                    </button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="olo-qv-info">
                <h3 data-olo-qv-title class="olo-qv-title"><?php echo esc_html( $product->get_name() ); ?></h3>
                <div class="olo-qv-price"><?php echo $product->get_price_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- price HTML generated and escaped by WooCommerce (WC_Product::get_price_html()). ?></div>
                <?php if ( $rating > 0 ) : ?>
                <div class="olo-qv-stars" role="img" aria-label="<?php echo esc_attr( sprintf( olobuild_t( 'Valutazione %s su 5' ), number_format_i18n( $rating, 1 ) ) ); ?>">
                    <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                    <span class="olo-qv-star<?php echo $i <= round( $rating ) ? ' is-on' : ''; ?>" aria-hidden="true">&#9733;</span>
                    <?php endfor; ?>
                    <span class="olo-qv-reviews" aria-hidden="true">(<?php echo absint( $product->get_review_count() ); ?>)</span>
                </div>
                <?php endif; ?>
                <?php if ( $product->get_short_description() ) : ?>
                <div class="olo-qv-excerpt"><?php echo wp_kses_post( $product->get_short_description() ); ?></div>
                <?php endif; ?>
                <?php if ( ! $product->is_in_stock() ) : ?>
                <div class="olo-qv-stock-out"><?php echo esc_html( olobuild_t( 'Esaurito' ) ); ?></div>
                <?php elseif ( $product->is_type( 'simple' ) ) : ?>
                    <?php if ( $product->is_purchasable() ) : ?>
                    <div class="olo-qv-atc">
                        <input type="number" class="olo-qv-qty" value="1" min="1" max="<?php echo (int) ( $product->get_stock_quantity() ?: 99 ); ?>" aria-label="<?php echo esc_attr( olobuild_t( 'Quantità' ) ); ?>" />
                        <button type="button" class="olo-qv-atc-btn" data-olo-qv-atc-btn><?php echo esc_html( olobuild_t( 'Aggiungi al carrello' ) ); ?></button>
                    </div>
                    <?php endif; ?>
                <?php else : ?>
                <?php
                // Variabili, raggruppati, esterni: il «?add-to-cart=» del semplice non basta (manca la
                // variante) e WooCommerce rispondeva con un errore. Si porta dove si sceglie, col testo
                // e l'indirizzo che WooCommerce dà a quel tipo di prodotto.
                ?>
                <div class="olo-qv-atc">
                    <a class="olo-qv-atc-btn" href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"><?php echo esc_html( $product->add_to_cart_text() ); ?></a>
                </div>
                <?php endif; ?>
                <div class="olo-qv-meta">
                    <?php if ( $product->get_sku() ) : ?>
                    <div class="olo-qv-sku">SKU: <?php echo esc_html( $product->get_sku() ); ?></div>
                    <?php endif; ?>
                    <?php
                    $cats = wc_get_product_category_list( $pid );
                    if ( $cats ) : ?>
                    <div class="olo-qv-cats"><?php echo esc_html( olobuild_t( 'Categorie' ) ); ?>: <?php echo $cats; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- category links HTML built and escaped by WooCommerce core (wc_get_product_category_list()). ?></div>
                    <?php endif; ?>
                </div>
                <a class="olo-qv-link" href="<?php echo esc_url( get_permalink( $pid ) ); ?>"><?php echo esc_html( olobuild_t( 'Vedi dettagli completi' ) ); ?> &rarr;</a>
            </div>
        </div>
        <?php
        $html = ob_get_clean();

        return rest_ensure_response( [ 'html' => $html ] );
    }
}
