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
        /*
         * «Su richiesta» (23 nov 2026): la finestra la apre un'altra tile, con window.oloQvApri( id, elemento ) — la
         * Pro Gallery coi prodotti di WooCommerce. In questo modo la tile non veste le card della tile Prodotti e non
         * lascia l'ancora della sezione, così non toglie le card a una Quick View vera della pagina.
         */
        $solo = ! empty( $settings['_solo_su_richiesta'] );

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
            $nascondi[] = '.olo-qv-varianti';
            $nascondi[] = '.olo-qv-stock';
            $nascondi[] = '.olo-qv-esito';
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
            .<?php echo $uid; ?>-body .olo-qv-attr {
                margin: 0 0 .9rem;
            }
            .<?php echo $uid; ?>-body .olo-qv-attr-tit {
                font-size: 13px;
                font-weight: 600;
                margin: 0 0 .45rem;
            }
            .<?php echo $uid; ?>-body .olo-qv-attr-val {
                font-weight: 400;
                color: var(--olo-color-text-muted, #9CA3AF);
            }
            .<?php echo $uid; ?>-body .olo-qv-attr-opz {
                display: flex;
                flex-wrap: wrap;
                gap: .45rem;
            }
            .<?php echo $uid; ?>-body .olo-qv-opz {
                min-width: 2.6rem;
                padding: .45rem .8rem;
                border: 1px solid var(--olo-color-border, #E5E7EB);
                border-radius: .375rem;
                background: transparent;
                color: inherit;
                font: inherit;
                font-size: 13px;
                line-height: 1.2;
                cursor: pointer;
                transition: border-color .2s, background-color .2s, color .2s;
            }
            .<?php echo $uid; ?>-body .olo-qv-opz:hover {
                border-color: <?php echo $accent; ?>;
            }
            .<?php echo $uid; ?>-body .olo-qv-opz.is-scelto {
                border-color: <?php echo $accent; ?>;
                background: <?php echo $accent; ?>;
                color: var(--olo-color-primary-contrast, #FFFFFF);
            }
            .<?php echo $uid; ?>-body .olo-qv-opz-colore {
                width: 1.9rem;
                height: 1.9rem;
                padding: 0;
                border: 2px solid <?php echo $modal_bg; ?>;
                border-radius: 50%;
                background: var(--c);
                box-shadow: 0 0 0 1px var(--olo-color-border, #E5E7EB);
                cursor: pointer;
                transition: box-shadow .2s;
            }
            .<?php echo $uid; ?>-body .olo-qv-opz-colore:hover,
            .<?php echo $uid; ?>-body .olo-qv-opz-colore.is-scelto {
                box-shadow: 0 0 0 2px <?php echo $accent; ?>;
            }
            .<?php echo $uid; ?>-body .olo-qv-stock {
                font-size: 13px;
                font-weight: 600;
                margin-top: .5rem;
                color: var(--olo-color-success, #16A34A);
            }
            .<?php echo $uid; ?>-body .olo-qv-stock.is-no {
                color: var(--olo-color-danger, #EF4444);
            }
            .<?php echo $uid; ?>-body .olo-qv-passi {
                display: inline-flex;
                align-items: stretch;
                height: 2.5rem;
                border: 1px solid var(--olo-color-border, #E5E7EB);
                border-radius: .375rem;
                overflow: hidden;
            }
            .<?php echo $uid; ?>-body .olo-qv-passi button {
                width: 2.25rem;
                padding: 0;
                border: 0;
                background: var(--olo-color-muted, #F3F4F6);
                color: inherit;
                font-size: 1rem;
                cursor: pointer;
            }
            .<?php echo $uid; ?>-body .olo-qv-passi .olo-qv-qty {
                width: 3rem;
                height: auto;
                border: 0;
                border-radius: 0;
                background: transparent;
                color: inherit;
                -moz-appearance: textfield;
                appearance: textfield;
            }
            .<?php echo $uid; ?>-body .olo-qv-passi .olo-qv-qty::-webkit-inner-spin-button,
            .<?php echo $uid; ?>-body .olo-qv-passi .olo-qv-qty::-webkit-outer-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
            .<?php echo $uid; ?>-body .olo-qv-atc-btn:disabled {
                opacity: .45;
                cursor: not-allowed;
            }
            .<?php echo $uid; ?>-body .olo-qv-atc-btn.is-attesa {
                opacity: .7;
                cursor: progress;
            }
            .<?php echo $uid; ?>-body .olo-qv-atc-btn.is-fatto {
                background: var(--olo-color-success, #16A34A);
                opacity: 1;
            }
            .<?php echo $uid; ?>-body .olo-qv-esito {
                font-size: 13px;
                margin: .5rem 0 0;
                min-height: 1.2em;
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
            .<?php echo $uid; ?>-body .olo-qv-opz:focus-visible,
            .<?php echo $uid; ?>-body .olo-qv-opz-colore:focus-visible,
            .<?php echo $uid; ?>-body .olo-qv-passi button:focus-visible,
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
        <?php if ( ! $solo ) : ?>
        <span class="<?php echo esc_attr( $uid ); ?>-ancora" data-olo-qv-ancora="<?php echo esc_attr( $uid ); ?>" hidden></span>
        <?php endif; ?>
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

                if(!window.oloQvAcquisto){
                    /* Varianti, quantità e carrello. Niente e commerciale scritta negli script in linea
                       (WordPress la riscrive in entità e il codice si rompe): condizioni annidate. */
                    window.oloQvAcquisto = function(box, pid, mainImg){
                        var btn = box.querySelector('[data-olo-qv-atc-btn]');
                        var qty = box.querySelector('.olo-qv-qty');
                        var esito = box.querySelector('.olo-qv-esito');
                        var prezzo = box.querySelector('.olo-qv-price');
                        var stock = box.querySelector('[data-olo-qv-stock]');
                        var varBox = box.querySelector('[data-olo-qv-var]');
                        var scelta = {}, varianti = [], vid = 0;
                        var imgOrig = mainImg ? mainImg.getAttribute('src') : '';
                        box.querySelectorAll('[data-olo-qv-passo]').forEach(function(b){
                            b.addEventListener('click', function(){
                                if(!qty){ return; }
                                var v = (parseInt(qty.value, 10) || 1) + parseInt(b.getAttribute('data-olo-qv-passo'), 10);
                                qty.value = Math.max(1, Math.min(parseInt(qty.max || '99', 10), v));
                            });
                        });
                        function cerca(){
                            for(var i = 0; i < varianti.length; i++){
                                var v = varianti[i], ok = true;
                                Object.keys(scelta).forEach(function(k){ var a = v.attributi[k]; if(a){ if(a !== scelta[k]){ ok = false; } } });
                                if(ok){ return v; }
                            }
                            return null;
                        }
                        function aggiorna(){
                            var tutte = varBox ? varBox.querySelectorAll('[data-olo-qv-attr]').length === Object.keys(scelta).length : true;
                            var v = varBox ? cerca() : null;
                            if(varBox){
                                vid = v ? (tutte ? v.id : 0) : 0;
                                if(v){ if(v.prezzo){ if(prezzo){ prezzo.innerHTML = v.prezzo; } } }
                                if(mainImg){ mainImg.setAttribute('src', (v ? v.foto : '') || imgOrig); }
                                if(stock){
                                    stock.hidden = !v;
                                    if(v){ stock.textContent = v.disponibile ? (v.disponibilita || stock.getAttribute('data-si')) : stock.getAttribute('data-no'); stock.classList.toggle('is-no', !v.disponibile); }
                                }
                                if(qty){ if(v){ if(v.max){ qty.max = v.max; } } }
                                if(btn){ btn.disabled = !vid || (v ? !v.disponibile : false); }
                                if(esito){ esito.textContent = (v ? false : tutte) ? esito.getAttribute('data-nessuna') : ''; }
                            }
                        }
                        if(varBox){
                            try { varianti = JSON.parse(varBox.getAttribute('data-olo-qv-var') || '[]'); } catch(e){ varianti = []; }
                            varBox.querySelectorAll('[data-olo-qv-attr]').forEach(function(g){
                                var nome = g.getAttribute('data-olo-qv-attr'), etich = g.querySelector('.olo-qv-attr-val');
                                g.querySelectorAll('[data-valore]').forEach(function(o){
                                    o.addEventListener('click', function(){
                                        scelta[nome] = o.getAttribute('data-valore');
                                        g.querySelectorAll('[data-valore]').forEach(function(x){ var on = x === o; x.classList.toggle('is-scelto', on); x.setAttribute('aria-pressed', on ? 'true' : 'false'); });
                                        if(etich){ etich.textContent = o.getAttribute('data-etichetta') || ''; }
                                        aggiorna();
                                    });
                                    if(o.classList.contains('is-scelto')){ scelta[nome] = o.getAttribute('data-valore'); if(etich){ etich.textContent = o.getAttribute('data-etichetta') || ''; } }
                                });
                            });
                            aggiorna();
                        }
                        if(!btn || btn.tagName !== 'BUTTON'){ return; }
                        btn.addEventListener('click', function(){
                            var id = varBox ? vid : parseInt(btn.getAttribute('data-pid') || pid, 10);
                            if(!id){ return; }
                            var wcp = window.wc_add_to_cart_params || {};
                            var base = btn.getAttribute('data-ajax') || (wcp.wc_ajax_url ? wcp.wc_ajax_url.replace('%%endpoint%%', 'add_to_cart') : '/?wc-ajax=add_to_cart');
                            var dati = new URLSearchParams();
                            dati.set('product_id', id);
                            dati.set('quantity', qty ? qty.value : 1);
                            Object.keys(scelta).forEach(function(k){ dati.set(k, scelta[k]); });
                            btn.classList.add('is-attesa'); btn.disabled = true;
                            fetch(base, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: dati.toString() })
                            .then(function(r){ return r.json(); })
                            .then(function(r){
                                btn.classList.remove('is-attesa');
                                if(!r || r.error){
                                    btn.disabled = false;
                                    if(esito){ esito.textContent = esito.getAttribute('data-errore'); }
                                    if(r){ if(r.product_url){ window.location.href = r.product_url; } }
                                    return;
                                }
                                if(r.fragments){
                                    Object.keys(r.fragments).forEach(function(sel){ document.querySelectorAll(sel).forEach(function(el){ el.outerHTML = r.fragments[sel]; }); });
                                }
                                if(window.jQuery){ window.jQuery(document.body).trigger('added_to_cart', [r.fragments, r.cart_hash, window.jQuery(btn)]); }
                                btn.classList.add('is-fatto');
                                if(esito){ esito.textContent = esito.getAttribute('data-fatto'); }
                                setTimeout(function(){ btn.classList.remove('is-fatto'); btn.disabled = false; }, 2200);
                            })
                            .catch(function(){ btn.classList.remove('is-attesa'); btn.disabled = false; if(esito){ esito.textContent = esito.getAttribute('data-errore'); } });
                        });
                    };
                }
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

                        oloQvAcquisto(content, pid, mainImg);
                    }
                })
                .catch(function(){
                    content.innerHTML = '<p style="text-align:center;color:var(--olo-color-danger, #EF4444);padding:30px">' + '<?php echo esc_js( olobuild_t( 'Errore nel caricamento.' ) ); ?>' + '</p>';
                    loading.style.display = 'none';
                    content.style.display = 'block';
                });
            }

            <?php if ( $solo ) : ?>
            /* Su richiesta: la apre chi la chiama (la Pro Gallery), le card non si vestono. */
            window.oloQvApri = function(pid, trigger){ if(overlay.isConnected){ openQuickView(pid, trigger); } };
            <?php else : ?>
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
                /* La griglia ha spento la Vista rapida per le sue card (opzione della tile Prodotti). */
                if(gia === 'no'){return}
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
            <?php endif; ?>
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
                <?php echo $this->acquisto_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- acquisto_html() escapa ogni testo e attributo; il prezzo è HTML di WooCommerce. ?>
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

    /**
     * LA PARTE D'ACQUISTO della finestra: disponibilità, scelta delle varianti, quantità, «Aggiungi al carrello».
     *
     * Prima un prodotto a varianti portava alla sua pagina («Scegli le opzioni»): ora colore, taglia e le altre
     * scelte stanno nella finestra (i colori a pallini, il resto a pillole), e prezzo, foto e disponibilità
     * seguono la variante. L'aggiunta passa da wc-ajax=add_to_cart: niente ricarica, e il mini carrello si aggiorna
     * coi frammenti di WooCommerce. Raggruppati ed esterni portano ancora alla loro pagina.
     *
     * @param WC_Product $product il prodotto
     * @return string HTML
     */
    private function acquisto_html( $product ) {
        $esito = '<p class="olo-qv-esito" aria-live="polite" data-fatto="' . esc_attr( olobuild_t( 'Aggiunto al carrello' ) ) . '" data-errore="' . esc_attr( olobuild_t( 'Non è stato possibile aggiungerlo: riprova' ) ) . '" data-nessuna="' . esc_attr( olobuild_t( 'Questa combinazione non c’è' ) ) . '"></p>';
        $passo = function ( $max ) {
            return '<div class="olo-qv-passi"><button type="button" data-olo-qv-passo="-1" aria-label="' . esc_attr( olobuild_t( 'Uno in meno' ) ) . '">&minus;</button>'
                . '<input type="number" class="olo-qv-qty" value="1" min="1" max="' . (int) $max . '" aria-label="' . esc_attr( olobuild_t( 'Quantità' ) ) . '">'
                . '<button type="button" data-olo-qv-passo="1" aria-label="' . esc_attr( olobuild_t( 'Uno in più' ) ) . '">+</button></div>';
        };
        $ajax = class_exists( 'WC_AJAX' ) ? ' data-ajax="' . esc_url( WC_AJAX::get_endpoint( 'add_to_cart' ) ) . '"' : '';
        if ( ! $product->is_in_stock() ) {
            return '<div class="olo-qv-stock-out">' . esc_html( olobuild_t( 'Esaurito' ) ) . '</div>';
        }
        if ( $product->is_type( 'simple' ) && $product->is_purchasable() ) {
            $disp = wp_strip_all_tags( (string) ( $product->get_availability()['availability'] ?? '' ) );
            return '<div class="olo-qv-stock" data-olo-qv-stock>' . esc_html( '' !== $disp ? $disp : olobuild_t( 'Disponibile' ) ) . '</div>'
                . '<div class="olo-qv-atc">' . $passo( $product->get_stock_quantity() ?: 99 )
                . '<button type="button" class="olo-qv-atc-btn" data-olo-qv-atc-btn data-pid="' . (int) $product->get_id() . '"' . $ajax . '>' . esc_html( olobuild_t( 'Aggiungi al carrello' ) ) . '</button></div>' . $esito;
        }
        if ( $product->is_type( 'variable' ) && $product->is_purchasable() ) {
            $varianti = [];
            foreach ( $product->get_available_variations( 'objects' ) as $v ) {
                if ( ! $v || ! $v->variation_is_visible() ) {
                    continue;
                }
                $foto = $v->get_image_id() ? wp_get_attachment_image_url( $v->get_image_id(), 'large' ) : '';
                $varianti[] = [
                    'id'            => $v->get_id(),
                    'attributi'     => array_combine( array_map( function ( $k ) { return 'attribute_' . $k; }, array_keys( $v->get_attributes() ) ), array_values( $v->get_attributes() ) ),
                    'prezzo'        => $v->get_price_html(),
                    'foto'          => $foto ? $foto : '',
                    'disponibile'   => $v->is_in_stock(),
                    'disponibilita' => wp_strip_all_tags( (string) ( $v->get_availability()['availability'] ?? '' ) ),
                    'max'           => $v->get_max_purchase_quantity() > 0 ? $v->get_max_purchase_quantity() : 99,
                ];
            }
            if ( ! $varianti ) {
                return '<div class="olo-qv-stock-out">' . esc_html( olobuild_t( 'Esaurito' ) ) . '</div>';
            }
            $predef = $product->get_default_attributes();
            $h      = '<div class="olo-qv-varianti" data-olo-qv-var="' . esc_attr( wp_json_encode( $varianti ) ) . '">';
            foreach ( $product->get_variation_attributes() as $nome => $valori ) {
                $tax     = taxonomy_exists( $nome );
                $colore  = $tax && preg_match( '/colou?re?|color/i', $nome );
                $chiave  = 'attribute_' . sanitize_title( $nome );
                $scelto  = (string) ( $predef[ sanitize_title( $nome ) ] ?? '' );
                $termini = $tax ? wc_get_product_terms( $product->get_id(), $nome, [ 'fields' => 'all' ] ) : [];
                $voci    = [];
                if ( $tax ) {
                    foreach ( $termini as $t ) {
                        if ( in_array( $t->slug, $valori, true ) ) {
                            $voci[] = [ $t->slug, $t->name, $colore ? Olobuild_Tile_Utils::colore_termine( $t ) : '' ];
                        }
                    }
                } else {
                    foreach ( $valori as $val ) {
                        $voci[] = [ $val, $val, '' ];
                    }
                }
                if ( '' === $scelto && $voci ) {
                    $scelto = $voci[0][0];
                }
                $h .= '<div class="olo-qv-attr" data-olo-qv-attr="' . esc_attr( $chiave ) . '"><div class="olo-qv-attr-tit">' . esc_html( wc_attribute_label( $nome, $product ) ) . ' <span class="olo-qv-attr-val"></span></div><div class="olo-qv-attr-opz">';
                foreach ( $voci as $voce ) {
                    list( $val, $etich, $c ) = $voce;
                    $on = $val === $scelto;
                    $h .= '<button type="button" class="' . ( $c ? 'olo-qv-opz-colore' : 'olo-qv-opz' ) . ( $on ? ' is-scelto' : '' ) . '" aria-pressed="' . ( $on ? 'true' : 'false' ) . '" data-valore="' . esc_attr( $val ) . '" data-etichetta="' . esc_attr( $etich ) . '"'
                        . ( $c ? ' style="--c:' . esc_attr( $c ) . '" aria-label="' . esc_attr( $etich ) . '" title="' . esc_attr( $etich ) . '">' : '>' . esc_html( $etich ) ) . '</button>';
                }
                $h .= '</div></div>';
            }
            $h .= '</div>';
            $h .= '<div class="olo-qv-stock" data-olo-qv-stock data-si="' . esc_attr( olobuild_t( 'Disponibile' ) ) . '" data-no="' . esc_attr( olobuild_t( 'Esaurito' ) ) . '" hidden></div>';
            $h .= '<div class="olo-qv-atc">' . $passo( 99 ) . '<button type="button" class="olo-qv-atc-btn" data-olo-qv-atc-btn' . $ajax . ' disabled>' . esc_html( olobuild_t( 'Aggiungi al carrello' ) ) . '</button></div>' . $esito;
            return $h;
        }
        return '<div class="olo-qv-atc"><a class="olo-qv-atc-btn" href="' . esc_url( $product->add_to_cart_url() ) . '">' . esc_html( $product->add_to_cart_text() ) . '</a></div>';
    }
}
