<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Woo_Order_Tracking_Tile extends Olobuild_Tile_Base {

    protected $type     = 'woo_order_tracking';
    protected $name     = 'Tracciamento Ordine';
    protected $icon     = 'dashicons-search';
    protected $category = 'woocommerce';
    protected $defaults = [
        'title'        => 'Traccia il tuo ordine',
        'title_tag'    => 'h2',
        'accent_color' => '',
        'text_color'   => '',
        'button_color' => '',
        'button_bg'    => '',
        'form_style'   => 'modern',
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

        $uid = 'olo-woo-ot-' . wp_rand( 10000, 99999 );

        // Prima di rendere altro: l'id del nodo cambia se la pagina contiene template annidati.
        $chiave = $this->chiave_stabile( $settings );
        $esito  = empty( $settings['_builder_mode'] ) ? $this->esito_tracciamento( $chiave ) : null;

        // Il nonce scritto nel modulo vale 12-24 ore; una cache di pagina più lunga (LiteSpeed:
        // una settimana) servirebbe a tutti un nonce scaduto, e ogni invio finirebbe in «ricarica
        // la pagina» senza rimedio, perché ricaricando torna la stessa copia. Si chiede di non
        // mettere in cache la pagina: DONOTCACHEPAGE (cache di Olobuild, WP Rocket, W3TC,
        // LiteSpeed) e l'azione di LiteSpeed. Nel builder non serve.
        if ( empty( $settings['_builder_mode'] ) ) {
            if ( ! defined( 'DONOTCACHEPAGE' ) ) {
                define( 'DONOTCACHEPAGE', true ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- costante standard dei plugin di cache.
            }
            do_action( 'litespeed_control_set_nocache', 'olobuild: nonce tracciamento ordine' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- azione di LiteSpeed Cache.
        }

        // Colors — TOKEN-FIRST: accento/CTA col brand, testo dal tema.
        $accent_color = $this->safe_color_css( $s['accent_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $text_color   = $this->safe_color_css( $s['text_color'] )   ?: 'var(--olo-color-text, #1f2937)';
        $button_color = $this->safe_color_css( $s['button_color'] ) ?: 'var(--olo-color-on-primary, #ffffff)';
        $button_bg    = $this->safe_color_css( $s['button_bg'] )    ?: 'var(--olo-color-primary, #e1474f)';

        // Title tag
        $allowed_tags = [ 'h2', 'h3', 'h4', 'h5' ];
        $title_tag = in_array( $s['title_tag'], $allowed_tags, true ) ? $s['title_tag'] : 'h2';
        $title     = sanitize_text_field( $s['title'] );

        // Form style
        $is_modern = ( $s['form_style'] === 'modern' );

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above (safe_color_css with fixed fallbacks, fixed ternary literals); $uid is an internal generated class name. ?>
        <style>
            .<?php echo $uid; ?> {
                color: <?php echo $text_color; ?>;
            }
            .<?php echo $uid; ?> .olo-ot-title {
                color: <?php echo $text_color; ?>;
                margin: 0 0 24px 0;
                font-size: 24px;
                font-weight: 700;
            }
            .<?php echo $uid; ?> .olo-ot-form {
                display: flex;
                flex-direction: column;
                gap: 16px;
                max-width: 480px;
            }
            .<?php echo $uid; ?> .olo-ot-field {
                display: flex;
                flex-direction: column;
                gap: 6px;
            }
            .<?php echo $uid; ?> .olo-ot-label {
                font-size: 14px;
                font-weight: 600;
                color: <?php echo $text_color; ?>;
            }
            .<?php echo $uid; ?> .olo-ot-input {
                padding: <?php echo $is_modern ? '12px 16px' : '8px 12px'; ?>;
                border: <?php echo $is_modern ? '2px solid var(--olo-color-border, #E5E7EB)' : '1px solid var(--olo-color-border, #E5E7EB)'; ?>;
                border-radius: <?php echo $is_modern ? '10px' : '4px'; ?>;
                font-size: 15px;
                transition: border-color 0.2s ease;
                outline: none;
                width: 100%;
                box-sizing: border-box;
            }
            .<?php echo $uid; ?> .olo-ot-input:focus {
                border-color: <?php echo $accent_color; ?>;
            }
            .<?php echo $uid; ?> .olo-ot-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                padding: <?php echo $is_modern ? '14px 28px' : '10px 20px'; ?>;
                background: <?php echo $button_bg; ?>;
                color: <?php echo $button_color; ?>;
                border: none;
                border-radius: <?php echo $is_modern ? '10px' : '4px'; ?>;
                font-size: 15px;
                font-weight: 600;
                cursor: pointer;
                transition: opacity 0.2s ease;
                margin-top: 4px;
            }
            .<?php echo $uid; ?> .olo-ot-btn:hover {
                opacity: 0.9;
            }
            .<?php echo $uid; ?> .olo-ot-btn:focus-visible,
            .<?php echo $uid; ?> .olo-ot-again:focus-visible {
                outline: 2px solid <?php echo $accent_color; ?>;
                outline-offset: 2px;
            }
            .<?php echo $uid; ?> .olo-ot-error {
                max-width: 480px;
                margin: 0 0 16px;
                padding: 12px 16px;
                border-radius: <?php echo $is_modern ? '10px' : '4px'; ?>;
                border: 1px solid color-mix(in srgb, var(--olo-color-danger, #b42318) 30%, transparent);
                background: color-mix(in srgb, var(--olo-color-danger, #b42318) 8%, transparent);
                color: var(--olo-color-danger, #b42318);
                font-size: 14px;
                line-height: 1.5;
            }
            .<?php echo $uid; ?> .olo-ot-result {
                max-width: 720px;
                font-size: 15px;
                line-height: 1.6;
            }
            .<?php echo $uid; ?> .olo-ot-result .order-info {
                margin: 0 0 24px;
                font-size: 16px;
            }
            .<?php echo $uid; ?> .olo-ot-result mark {
                background: color-mix(in srgb, <?php echo $accent_color; ?> 14%, transparent);
                color: inherit;
                font-weight: 600;
                padding: 0 0.35em;
                border-radius: 4px;
            }
            .<?php echo $uid; ?> .olo-ot-result h2 {
                color: <?php echo $text_color; ?>;
                font-size: 18px;
                font-weight: 700;
                margin: 28px 0 12px;
            }
            .<?php echo $uid; ?> .olo-ot-result table {
                width: 100%;
                border-collapse: collapse;
                font-size: 14px;
            }
            .<?php echo $uid; ?> .olo-ot-result th,
            .<?php echo $uid; ?> .olo-ot-result td {
                padding: 10px 12px;
                text-align: left;
                vertical-align: top;
                border-bottom: 1px solid var(--olo-color-border, #E5E7EB);
            }
            .<?php echo $uid; ?> .olo-ot-result address {
                font-style: normal;
                line-height: 1.6;
            }
            .<?php echo $uid; ?> a.olo-ot-again {
                display: inline-block;
                margin-top: 20px;
                color: <?php echo $accent_color; ?>;
                font-weight: 600;
                text-decoration: none;
            }
            .<?php echo $uid; ?> a.olo-ot-again:hover {
                color: <?php echo $accent_color; ?>;
                text-decoration: underline;
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="<?php echo esc_attr( $uid ); ?>">
            <?php if ( $title !== '' ) : ?>
            <?php list( $ot_cls, $ot_data ) = $this->tfx_attrs( $s, 'title', $title ); ?>
            <<?php echo $title_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $title_tag whitelisted via in_array() above; tfx attrs escaped internally by Olobuild_Text_Effects ?> class="olo-ot-title<?php echo $ot_cls; ?>"<?php echo $ot_data; ?>><?php echo esc_html( $title ); ?></<?php echo $title_tag; ?>>
            <?php endif; ?>
            <?php if ( $esito && $esito['ordine'] ) : ?>
            <?php
            // Come lo shortcode: l'azione per chi registra le consultazioni, poi il template di
            // WooCommerce (stato, aggiornamenti, dettagli), che il tema può sovrascrivere.
            do_action( 'woocommerce_track_order', $esito['ordine']->get_id() );
            ?>
            <div class="woocommerce olo-ot-result">
                <?php wc_get_template( 'order/tracking.php', [ 'order' => $esito['ordine'] ] ); ?>
            </div>
            <?php // href vuoto = la pagina stessa riletta senza invio: si torna al modulo. ?>
            <a class="olo-ot-again" href=""><?php echo esc_html( olobuild_t( 'Traccia un altro ordine' ) ); ?></a>
            <?php else : ?>
            <?php if ( $esito && $esito['errore'] !== '' ) : ?>
            <p class="olo-ot-error" role="alert"><?php echo esc_html( $esito['errore'] ); ?></p>
            <?php endif; ?>
            <?php
            /*
             * Il modulo inviava a «Il mio account», che orderid e order_email non li legge: si
             * tornava al login e nessun ordine veniva cercato. Senza action invia alla pagina
             * stessa (indirizzo e parametri compresi, come il modulo di WooCommerce) e la
             * risposta la dà esito_tracciamento(). olo_ot dice quale tile ha inviato: con due
             * moduli nella pagina risponde solo quello usato.
             */
            ?>
            <form class="olo-ot-form" method="post">
                <div class="olo-ot-field">
                    <label class="olo-ot-label" for="<?php echo esc_attr( $uid ); ?>-order-id"><?php echo esc_html( olobuild_t( 'Numero ordine' ) ); ?></label>
                    <input type="text" class="olo-ot-input" name="orderid" id="<?php echo esc_attr( $uid ); ?>-order-id" placeholder="<?php echo esc_attr( olobuild_t( 'Inserisci qui il numero del tuo ordine' ) ); ?>" value="<?php echo esc_attr( $esito ? $esito['id'] : '' ); ?>" required />
                </div>
                <div class="olo-ot-field">
                    <label class="olo-ot-label" for="<?php echo esc_attr( $uid ); ?>-email"><?php echo esc_html( olobuild_t( 'Email di fatturazione' ) ); ?></label>
                    <input type="email" class="olo-ot-input" name="order_email" id="<?php echo esc_attr( $uid ); ?>-email" placeholder="<?php echo esc_attr( olobuild_t( 'Email usata per l\'ordine' ) ); ?>" value="<?php echo esc_attr( $esito ? $esito['email'] : '' ); ?>" required />
                </div>
                <input type="hidden" name="olo_ot" value="<?php echo esc_attr( $chiave ); ?>" />
                <?php wp_nonce_field( 'woocommerce-order_tracking', 'woocommerce-order-tracking-nonce' ); ?>
                <button type="submit" class="olo-ot-btn" name="track" value="<?php echo esc_attr( olobuild_t( 'Traccia' ) ); ?>">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <span><?php echo esc_html( olobuild_t( 'Traccia ordine' ) ); ?></span>
                </button>
            </form>
            <?php endif; ?>
        </div>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated internally by Olobuild_Text_Effects::css() from sanitized settings.
        $this->tfx_print_script();
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- border CSS generated by Olobuild_Tile_Base::build_border_*() helpers (intval sizes, fixed templates); $uid is an internal generated class name.
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}";
            echo $border_hover_css . $border_effect_css . '</style>';
            // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
        }
        return ob_get_clean();
    }

    /**
     * La risposta al modulo inviato da QUESTA tile, con la logica dello shortcode
     * [woocommerce_order_tracking] (WC_Shortcode_Order_Tracking::output()): stesso nonce, stesso
     * filtro sul numero (i plugin dei numeri d'ordine progressivi lo usano per risalire all'id),
     * stesso confronto con l'email di fatturazione. Il messaggio d'errore non dice quale dei due
     * dati non torna, come WooCommerce: non si scopre se un numero d'ordine esiste.
     *
     * @param string $chiave chiave_stabile() della tile, scritta nel campo nascosto olo_ot.
     * @return array|null null se il modulo di questa tile non è stato inviato;
     *                    altrimenti [ 'ordine' => WC_Order|null, 'errore' => string, 'id' => string, 'email' => string ].
     */
    private function esito_tracciamento( $chiave ) {
        // phpcs:disable WordPress.Security.NonceVerification.Missing -- il nonce si verifica qui sotto, prima di leggere i dati dell'ordine; olo_ot serve solo a riconoscere la tile.
        if ( ! isset( $_POST['orderid'], $_POST['olo_ot'] ) || ! is_string( $_POST['olo_ot'] ) ) {
            return null;
        }
        if ( sanitize_key( wp_unslash( $_POST['olo_ot'] ) ) !== $chiave ) {
            return null;
        }
        $nonce = isset( $_POST['woocommerce-order-tracking-nonce'] ) && is_string( $_POST['woocommerce-order-tracking-nonce'] )
            ? sanitize_text_field( wp_unslash( $_POST['woocommerce-order-tracking-nonce'] ) )
            : '';
        $id    = is_string( $_POST['orderid'] ) ? ltrim( wc_clean( wp_unslash( $_POST['orderid'] ) ), '#' ) : '';
        $email = isset( $_POST['order_email'] ) && is_string( $_POST['order_email'] )
            ? sanitize_email( wp_unslash( $_POST['order_email'] ) )
            : '';
        // phpcs:enable WordPress.Security.NonceVerification.Missing

        $esito = [ 'ordine' => null, 'errore' => '', 'id' => $id, 'email' => $email ];

        if ( ! wp_verify_nonce( $nonce, 'woocommerce-order_tracking' ) ) {
            $esito['errore'] = olobuild_t( 'La pagina era aperta da troppo tempo: ricaricala e invia di nuovo il modulo.' );
        } elseif ( '' === $id ) {
            $esito['errore'] = olobuild_t( 'Inserisci un numero d\'ordine valido.' );
        } elseif ( '' === $email ) {
            $esito['errore'] = olobuild_t( 'Inserisci un indirizzo email valido.' );
        } else {
            $ordine = wc_get_order( apply_filters( 'woocommerce_shortcode_order_tracking_order_id', $id ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- filtro di WooCommerce, lo stesso dello shortcode.
            if ( $ordine && is_a( $ordine, 'WC_Order' ) && $ordine->get_id() && strtolower( $ordine->get_billing_email() ) === strtolower( $email ) ) {
                $esito['ordine'] = $ordine;
            } else {
                $esito['errore'] = olobuild_t( 'Non troviamo un ordine con questo numero e questa email. Controlla i dati, o scrivici se non riesci a trovarlo.' );
            }
        }
        return $esito;
    }
}
