<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Woo_Product_Filter_Tile extends Olobuild_Tile_Base {

    protected $type     = 'woo_product_filter';
    protected $name     = 'Filtro Prodotti WC';
    protected $icon     = 'dashicons-filter';
    protected $category = 'woocommerce';
    protected $defaults = [
        'show_price'       => true,
        'show_categories'  => true,
        'show_attributes'  => true,
        'show_stock'       => true,
        'show_on_sale'     => false,
        'show_sort'        => false,
        'senza_ricarica'   => true,
        'show_active'      => true,
        'price_min'        => 0,
        'price_max'        => 1000,
        'price_step'       => 10,
        'attributes'       => 'pa_color,pa_size',
        'collapsed'        => false,
        'heading_color'    => '',
        'text_color'       => '',
        'accent_color'     => '',
        'bg_color'         => '',
        'border_color'     => '',
        'border_radius'    => 8,
        'button_text'      => 'Filtra',
        'reset_text'       => 'Resetta',
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
        $uid = 'olo-woo-filter-' . wp_rand( 10000, 99999 );

        // L'inspector salva chiavi che il PHP non leggeva (show_price_range, filter_style,
        // collapsible, show_count, apply_button, label_color, active_color, button_*): metà dei
        // controlli non faceva niente. Si legge prima la chiave del config, poi quella storica
        // del PHP, così i template importati con le vecchie chiavi restano come sono.
        $show_price  = $this->scelta( $s, 'show_price_range', 'show_price' );
        $show_count  = filter_var( $s['show_count'] ?? true, FILTER_VALIDATE_BOOLEAN );
        $apply_btn   = filter_var( $s['apply_button'] ?? true, FILTER_VALIDATE_BOOLEAN );
        $collapsible = filter_var( $s['collapsible'] ?? true, FILTER_VALIDATE_BOOLEAN );
        $style       = (string) ( $s['filter_style'] ?? 'sidebar' );
        if ( ! in_array( $style, [ 'sidebar', 'horizontal', 'dropdown' ], true ) ) {
            $style = 'sidebar';
        }
        $is_dropdown = ( $style === 'dropdown' );
        // A tendina le sezioni partono chiuse e si aprono una alla volta; altrove «collapsed»
        // (chiave storica) le fa partire chiuse, ma solo se si possono riaprire.
        $collapsed = $is_dropdown || ( $collapsible && ! empty( $s['collapsed'] ) );
        $can_fold  = $is_dropdown || $collapsible;

        // Colors
        $label_color   = $this->safe_color_css( $s['label_color'] ?? '' );
        $heading_color = $label_color ?: ( $this->safe_color_css( $s['heading_color'] ) ?: 'var(--olo-color-text, #374151)' );
        $text_color    = $label_color ?: ( $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text, #1f2937)' );
        $accent_color  = $this->safe_color_css( $s['active_color'] ?? '' ) ?: ( $this->safe_color_css( $s['accent_color'] ) ?: 'var(--olo-color-primary, #e1474f)' );
        $btn_bg        = $this->safe_color_css( $s['button_bg'] ?? '' ) ?: $accent_color;
        $btn_color     = $this->safe_color_css( $s['button_color'] ?? '' ) ?: 'var(--olo-color-primary-contrast, #FFFFFF)';
        $bg_color      = $this->safe_color_css( $s['bg_color'] ) ?: 'var(--olo-color-surface, #ffffff)';
        $border_color  = $this->safe_color_css( $s['border_color'] ) ?: 'var(--olo-color-border, #e5e7eb)';
        $radius        = Olobuild_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $radius_hover_css = Olobuild_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );
        $radius_raw    = Olobuild_Tile_Utils::radius_int( $s['border_radius'] ?? 0 );

        // Il cursore va dal prezzo più basso al più alto dei prodotti che chi visita può trovare (fascia_prezzi()),
        // arrotondati al passo: «€10 – €490», non «€14 – €487». Senza prodotti con un prezzo, i valori salvati.
        $price_min  = floatval( $s['price_min'] );
        $price_max  = floatval( $s['price_max'] );
        $price_step = max( 1, absint( $s['price_step'] ) );
        $fascia     = $this->fascia_prezzi();
        if ( $fascia ) {
            list( $piu_basso, $piu_alto ) = $fascia;
            // Un passo che lascerebbe meno di 5 posizioni (passo 10 in un negozio da 3 a 18 euro) si stringe a uno tondo.
            if ( $piu_alto > $piu_basso && ( $piu_alto - $piu_basso ) / $price_step < 5 ) {
                $price_step = $this->passo_tondo( ( $piu_alto - $piu_basso ) / 20 );
            }
            $price_min = floor( $piu_basso / $price_step ) * $price_step;
            $price_max = ceil( $piu_alto / $price_step ) * $price_step;
            if ( $price_max <= $price_min ) {
                $price_max = $price_min + $price_step;
            }
        }

        $currency = function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '&euro;';

        // Categories
        $categories = [];
        if ( ! empty( $s['show_categories'] ) ) {
            $terms = get_terms( [ 'taxonomy' => 'product_cat', 'hide_empty' => true ] );
            if ( ! is_wp_error( $terms ) ) {
                $categories = $terms;
            }
        }

        // Attributes
        $attribute_sections = [];
        if ( ! empty( $s['show_attributes'] ) ) {
            // Gli attributi erano fissi (pa_color,pa_size, nessun controllo): in un negozio con
            // altri attributi la sezione non compariva mai. Ora si scrivono nell'inspector; vuoto =
            // tutti gli attributi del negozio. I template salvati tengono il loro elenco.
            $attr_raw   = trim( sanitize_text_field( (string) $s['attributes'] ) );
            $attr_slugs = $attr_raw === ''
                ? ( function_exists( 'wc_get_attribute_taxonomy_names' ) ? wc_get_attribute_taxonomy_names() : [] )
                : array_map( 'trim', explode( ',', $attr_raw ) );
            foreach ( $attr_slugs as $attr_slug ) {
                $attr_slug = sanitize_text_field( $attr_slug );
                // «color» vale come «pa_color»: nell'inspector si scrive il nome dell'attributo.
                if ( $attr_slug !== '' && strpos( $attr_slug, 'pa_' ) !== 0 && taxonomy_exists( 'pa_' . $attr_slug ) ) {
                    $attr_slug = 'pa_' . $attr_slug;
                }
                if ( ! taxonomy_exists( $attr_slug ) ) {
                    continue;
                }
                $label = wc_attribute_label( str_replace( 'pa_', '', $attr_slug ) );
                $terms = get_terms( [ 'taxonomy' => $attr_slug, 'hide_empty' => true ] );
                if ( ! is_wp_error( $terms ) ) {
                    if ( ! empty( $terms ) ) {
                        $attribute_sections[] = [
                            'taxonomy' => $attr_slug,
                            'label'    => ucfirst( $label ),
                            'terms'    => $terms,
                        ];
                    }
                }
            }
        }

        $btn_text   = esc_html( $s['button_text'] ?: olobuild_t( 'Filtra' ) );
        $reset_text = esc_html( $s['reset_text'] ?: olobuild_t( 'Resetta' ) );

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist colors, Olobuild_Tile_Utils radius helpers (absint-based) and the internally generated $uid.
        ?>
        <style>
            .<?php echo $uid; ?> {
                background: <?php echo $bg_color; ?>;
                border: 1px solid <?php echo $border_color; ?>;
                border-radius: <?php echo $radius; ?>;
                padding: 20px;
                font-size: 14px;
                color: <?php echo $text_color; ?>;
            }
            <?php if ( $radius_hover_css !== '' ) : ?>.<?php echo $uid; ?>{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?>:hover{border-radius:<?php echo $radius_hover_css; ?> !important}<?php endif; ?>
            .<?php echo $uid; ?> .olo-pf-section {
                border-bottom: 1px solid <?php echo $border_color; ?>;
                padding-bottom: 16px;
                margin-bottom: 16px;
            }
            .<?php echo $uid; ?> .olo-pf-section:last-of-type {
                border-bottom: none;
                padding-bottom: 0;
                margin-bottom: 0;
            }
            .<?php echo $uid; ?> .olo-pf-heading {
                display: flex;
                align-items: center;
                justify-content: space-between;
                width: 100%;
                background: none;
                border: none;
                text-align: left;
                font-family: inherit;
                line-height: inherit;
                appearance: none;
                cursor: pointer;
                margin: 0 0 12px;
                padding: 0;
                font-size: 14px;
                font-weight: 700;
                color: <?php echo $heading_color; ?>;
                user-select: none;
            }
            .<?php echo $uid; ?> .olo-pf-heading svg {
                flex-shrink: 0;
                transition: transform 0.2s ease;
            }
            .<?php echo $uid; ?> .olo-pf-heading.is-collapsed svg {
                transform: rotate(-90deg);
            }
            .<?php echo $uid; ?> .olo-pf-body {
                overflow: hidden;
                transition: max-height 0.3s ease;
            }
            .<?php echo $uid; ?> .olo-pf-body.is-collapsed {
                max-height: 0 !important;
                padding: 0;
                margin: 0;
            }
            .<?php echo $uid; ?> .olo-pf-price-row {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 13px;
                margin-bottom: 6px;
            }
            .<?php echo $uid; ?> .olo-pf-price-row input[type="range"] {
                flex: 1;
                accent-color: <?php echo $accent_color; ?>;
            }
            .<?php echo $uid; ?> .olo-pf-check-list {
                max-height: 200px;
                overflow-y: auto;
                padding: 2px 0;
            }
            .<?php echo $uid; ?> .olo-pf-check-item {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 4px 0;
                cursor: pointer;
                font-size: 13px;
            }
            .<?php echo $uid; ?> .olo-pf-check-item input[type="checkbox"] {
                accent-color: <?php echo $accent_color; ?>;
                width: 16px;
                height: 16px;
                cursor: pointer;
                flex-shrink: 0;
            }
            .<?php echo $uid; ?> .olo-pf-price-row input[type="range"]:focus-visible,
            .<?php echo $uid; ?> .olo-pf-check-item input[type="checkbox"]:focus-visible {
                outline: none;
                box-shadow: 0 0 0 3px color-mix(in srgb, <?php echo $accent_color; ?> 30%, transparent);
            }
            .<?php echo $uid; ?> .olo-pf-count {
                margin-left: auto;
                color: var(--olo-color-text-muted, #9CA3AF);
                font-size: 12px;
            }
            .<?php echo $uid; ?> .olo-pf-toggle-wrap {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 4px 0;
            }
            .<?php echo $uid; ?> .olo-pf-ordina {
                display: flex;
                align-items: center;
                gap: .6rem;
            }
            .<?php echo $uid; ?> .olo-pf-ordina-lab {
                font-size: 13px;
                color: var(--olo-color-text-muted, #6B7280);
            }
            .<?php echo $uid; ?> .olo-pf-ordina select {
                font: inherit;
                font-size: 13px;
                color: <?php echo $text_color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- colore già passato da safe_color_css() ?>;
                background: transparent;
                border: 1px solid <?php echo $border_color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- colore già passato da safe_color_css() ?>;
                border-radius: 100vmax;
                padding: .5em 1em;
                cursor: pointer;
            }
            .<?php echo $uid; ?> .olo-pf-ordina select:focus-visible {
                outline: 2px solid <?php echo $accent_color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- colore già passato da safe_color_css() ?>;
                outline-offset: 2px;
            }
            .<?php echo $uid; ?> .olo-pf-toggle {
                position: relative;
                width: 40px;
                height: 22px;
                padding: 0;
                border: none;
                appearance: none;
                background: var(--olo-color-border, #E5E7EB);
                border-radius: 11px;
                cursor: pointer;
                transition: background 0.2s ease;
                flex-shrink: 0;
            }
            .<?php echo $uid; ?> .olo-pf-toggle.is-active {
                background: <?php echo $accent_color; ?>;
            }
            .<?php echo $uid; ?> .olo-pf-toggle::after {
                content: '';
                position: absolute;
                top: 2px;
                left: 2px;
                width: 18px;
                height: 18px;
                border-radius: 50%;
                background: var(--olo-color-background, #FFFFFF);
                transition: transform 0.2s ease;
                box-shadow: 0 1px 2px rgba(0,0,0,0.15);
            }
            .<?php echo $uid; ?> .olo-pf-toggle.is-active::after {
                transform: translateX(18px);
            }
            .<?php echo $uid; ?> .olo-pf-active-wrap {
                display: none;
                margin-bottom: 16px;
                padding-bottom: 16px;
                border-bottom: 1px solid <?php echo $border_color; ?>;
            }
            .<?php echo $uid; ?> .olo-pf-active-tags {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
                margin-top: 8px;
            }
            .<?php echo $uid; ?> .olo-pf-active-tag {
                background: var(--olo-color-muted, #F3F4F6);
                padding: 3px 10px;
                border-radius: 4px;
                font-size: 12px;
                display: inline-block;
            }
            .<?php echo $uid; ?> .olo-pf-clear-all {
                font-size: 12px;
                color: <?php echo $accent_color; ?>;
                text-decoration: none;
                margin-top: 8px;
                display: inline-block;
                cursor: pointer;
            }
            .<?php echo $uid; ?> .olo-pf-clear-all:hover {
                text-decoration: underline;
            }
            .<?php echo $uid; ?> .olo-pf-actions {
                display: flex;
                gap: 8px;
                margin-top: 20px;
            }
            .<?php echo $uid; ?> .olo-pf-btn {
                flex: 1;
                padding: 10px 16px;
                border: none;
                border-radius: <?php echo (int) max( 4, $radius_raw - 2 ); ?>px;
                font-size: 13px;
                font-weight: 600;
                cursor: pointer;
                text-align: center;
                transition: opacity 0.2s ease;
            }
            .<?php echo $uid; ?> .olo-pf-btn:hover {
                opacity: 0.9;
            }
            .<?php echo $uid; ?> .olo-pf-btn-primary {
                background: <?php echo $btn_bg; ?>;
                color: <?php echo $btn_color; ?>;
            }
            .<?php echo $uid; ?> .olo-pf-btn-secondary {
                background: var(--olo-color-muted, #F3F4F6);
                color: <?php echo $text_color; ?>;
            }
            .<?php echo $uid; ?> .olo-pf-btn:focus-visible,
            .<?php echo $uid; ?> .olo-pf-heading:focus-visible,
            .<?php echo $uid; ?> .olo-pf-toggle:focus-visible {
                outline: 2px solid <?php echo $accent_color; ?>;
                outline-offset: 2px;
            }
            /* Sezioni non richiudibili («Sezioni richiudibili» spento): titolo fermo, senza freccia. */
            .<?php echo $uid; ?> .olo-pf-heading.is-static {
                cursor: default;
            }
            <?php if ( $style === 'horizontal' ) : ?>
            /* Orizzontale: le sezioni in fila, separate da un filetto verticale; i pulsanti sotto a
               destra. Sotto i 640 px resta la colonna della barra laterale. Spazi in em: seguono il
               testo del pannello (14 px → 1.75em ≈ 24 px). */
            @media (min-width: 640px) {
                .<?php echo $uid; ?> {
                    display: flex;
                    flex-wrap: wrap;
                    align-items: flex-start;
                    column-gap: 1.75em;
                }
                .<?php echo $uid; ?> > .olo-pf-active-wrap,
                .<?php echo $uid; ?> > .olo-pf-actions {
                    flex: 1 1 100%;
                }
                .<?php echo $uid; ?> > .olo-pf-actions {
                    justify-content: flex-end;
                }
                .<?php echo $uid; ?> > .olo-pf-actions .olo-pf-btn {
                    flex: 0 0 auto;
                }
                .<?php echo $uid; ?> .olo-pf-section,
                .<?php echo $uid; ?> .olo-pf-section:last-of-type {
                    flex: 1 1 180px;
                    min-width: 0;
                    margin: 0;
                    padding: 0 1.75em 0 0;
                    border-bottom: none;
                    border-right: 1px solid <?php echo $border_color; ?>;
                }
                .<?php echo $uid; ?> .olo-pf-section:nth-last-child(2) {
                    border-right: none;
                    padding-right: 0;
                }
            }
            <?php elseif ( $is_dropdown ) : ?>
            /* A tendina: una fila di pulsanti, ognuno apre il suo pannello sopra la pagina. Spazi in
               em, come quelli di un pulsante: seguono il testo. */
            .<?php echo $uid; ?> {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 0.5em;
            }
            .<?php echo $uid; ?> > .olo-pf-active-wrap {
                flex: 1 1 100%;
            }
            .<?php echo $uid; ?> .olo-pf-section,
            .<?php echo $uid; ?> .olo-pf-section:last-of-type {
                position: relative;
                margin: 0;
                padding: 0;
                border-bottom: none;
            }
            .<?php echo $uid; ?> .olo-pf-heading {
                width: auto;
                gap: 0.6em;
                margin: 0;
                padding: 0.6em 1.1em;
                border: 1px solid <?php echo $border_color; ?>;
                border-radius: <?php echo (int) max( 4, $radius_raw - 2 ); ?>px;
                background: <?php echo $bg_color; ?>;
                font-size: 13px;
            }
            .<?php echo $uid; ?> .olo-pf-heading[aria-expanded="true"] {
                border-color: <?php echo $accent_color; ?>;
            }
            /* Freccia in giù da chiuso, in su da aperto (come ogni menu a tendina). */
            .<?php echo $uid; ?> .olo-pf-heading.is-collapsed svg { transform: none; }
            .<?php echo $uid; ?> .olo-pf-heading[aria-expanded="true"] svg { transform: rotate(180deg); }
            .<?php echo $uid; ?> .olo-pf-body {
                position: absolute;
                top: calc(100% + 6px);
                left: 0;
                z-index: 30;
                min-width: 240px;
                max-width: calc(100vw - 32px);
                max-height: none !important;
                overflow: visible;
                padding: 1em;
                background: <?php echo $bg_color; ?>;
                border: 1px solid <?php echo $border_color; ?>;
                border-radius: <?php echo $radius ?: '0'; ?>;
                box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            }
            .<?php echo $uid; ?> .olo-pf-body.is-collapsed {
                display: none;
            }
            /* Sul telefono il pannello ancorato al suo pulsante usciva dallo schermo (un pulsante
               nella metà destra + 240 px): lì si ancora all'intera barra e ne prende la larghezza. */
            @media (max-width: 639px) {
                .<?php echo $uid; ?> {
                    position: relative;
                }
                .<?php echo $uid; ?> .olo-pf-section,
                .<?php echo $uid; ?> .olo-pf-section:last-of-type {
                    position: static;
                }
                .<?php echo $uid; ?> .olo-pf-body {
                    left: 0;
                    right: 0;
                    min-width: 0;
                    max-width: none;
                }
            }
            .<?php echo $uid; ?> > .olo-pf-actions {
                margin: 0 0 0 auto;
            }
            .<?php echo $uid; ?> > .olo-pf-actions .olo-pf-btn {
                flex: 0 0 auto;
            }
            <?php endif; ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="<?php echo esc_attr( $uid ); ?> olo-pf--<?php echo esc_attr( $style ); ?>">

            <?php if ( ! empty( $s['show_active'] ) ) : ?>
            <div class="olo-pf-active-wrap" data-olo-pf-active>
                <div style="font-weight:600;font-size:13px"><?php echo esc_html( olobuild_t( 'Filtri attivi' ) ); ?></div>
                <div class="olo-pf-active-tags" data-olo-pf-active-tags></div>
                <a href="#" class="olo-pf-clear-all" data-olo-pf-clear-all><?php echo esc_html( olobuild_t( 'Rimuovi tutti' ) ); ?></a>
            </div>
            <?php endif; ?>

            <?php /* --- PRICE RANGE --- */ ?>
            <?php if ( $show_price ) : ?>
            <div class="olo-pf-section">
                <?php echo $this->intestazione( olobuild_t( 'Prezzo' ), $uid . '-body-price', $collapsed, $can_fold ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup built by intestazione() with esc_html()/esc_attr() on every dynamic part ?>
                <div class="olo-pf-body<?php echo $collapsed ? ' is-collapsed' : ''; ?>" id="<?php echo esc_attr( $uid ); ?>-body-price" style="max-height:200px">
                    <div class="olo-pf-price-row">
                        <span><?php echo $currency; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- currency symbol from get_woocommerce_currency_symbol() or the static &euro; entity; esc_html() would double-encode the entity ?><span data-olo-pf-min-label><?php echo intval( $price_min ); ?></span></span>
                        <input type="range" class="olo-pf-price-min" aria-label="<?php echo esc_attr( olobuild_t( 'Prezzo minimo' ) ); ?>" min="<?php echo (float) $price_min; ?>" max="<?php echo (float) $price_max; ?>" step="<?php echo (int) $price_step; ?>" value="<?php echo (float) $price_min; ?>" />
                    </div>
                    <div class="olo-pf-price-row">
                        <span><?php echo $currency; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- currency symbol from get_woocommerce_currency_symbol() or the static &euro; entity; esc_html() would double-encode the entity ?><span data-olo-pf-max-label><?php echo intval( $price_max ); ?></span></span>
                        <input type="range" class="olo-pf-price-max" aria-label="<?php echo esc_attr( olobuild_t( 'Prezzo massimo' ) ); ?>" min="<?php echo (float) $price_min; ?>" max="<?php echo (float) $price_max; ?>" step="<?php echo (int) $price_step; ?>" value="<?php echo (float) $price_max; ?>" />
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php /* --- CATEGORIES --- */ ?>
            <?php if ( ! empty( $s['show_categories'] ) ) : ?>
            <?php if ( ! empty( $categories ) ) : ?>
            <div class="olo-pf-section">
                <?php echo $this->intestazione( olobuild_t( 'Categorie' ), $uid . '-body-categories', $collapsed, $can_fold ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup built by intestazione() with esc_html()/esc_attr() on every dynamic part ?>
                <div class="olo-pf-body<?php echo $collapsed ? ' is-collapsed' : ''; ?>" id="<?php echo esc_attr( $uid ); ?>-body-categories" style="max-height:400px">
                    <div class="olo-pf-check-list">
                        <?php foreach ( $categories as $cat ) : ?>
                        <label class="olo-pf-check-item">
                            <input type="checkbox" class="olo-pf-cat" value="<?php echo esc_attr( $cat->slug ); ?>" />
                            <span><?php echo esc_html( $cat->name ); ?></span>
                            <?php if ( $show_count ) : ?>
                            <span class="olo-pf-count">(<?php echo absint( $cat->count ); ?>)</span>
                            <?php endif; ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php endif; ?>

            <?php /* --- ATTRIBUTES --- */ ?>
            <?php foreach ( $attribute_sections as $attr ) : ?>
            <?php $attr_body_id = $uid . '-body-attr-' . sanitize_html_class( $attr['taxonomy'] ); ?>
            <div class="olo-pf-section">
                <?php echo $this->intestazione( $attr['label'], $attr_body_id, $collapsed, $can_fold ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup built by intestazione() with esc_html()/esc_attr() on every dynamic part ?>
                <div class="olo-pf-body<?php echo $collapsed ? ' is-collapsed' : ''; ?>" id="<?php echo esc_attr( $attr_body_id ); ?>" style="max-height:400px">
                    <div class="olo-pf-check-list">
                        <?php foreach ( $attr['terms'] as $term ) : ?>
                        <label class="olo-pf-check-item">
                            <input type="checkbox" class="olo-pf-attr" data-taxonomy="<?php echo esc_attr( $attr['taxonomy'] ); ?>" value="<?php echo esc_attr( $term->slug ); ?>" />
                            <span><?php echo esc_html( $term->name ); ?></span>
                            <?php if ( $show_count ) : ?>
                            <span class="olo-pf-count">(<?php echo absint( $term->count ); ?>)</span>
                            <?php endif; ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <?php /* --- IN-STOCK TOGGLE --- */ ?>
            <?php if ( ! empty( $s['show_stock'] ) ) : ?>
            <div class="olo-pf-section">
                <?php echo $this->intestazione( olobuild_t( 'Disponibilità' ), $uid . '-body-stock', $collapsed, $can_fold ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup built by intestazione() with esc_html()/esc_attr() on every dynamic part ?>
                <div class="olo-pf-body<?php echo $collapsed ? ' is-collapsed' : ''; ?>" id="<?php echo esc_attr( $uid ); ?>-body-stock" style="max-height:200px">
                    <div class="olo-pf-toggle-wrap">
                        <span><?php echo esc_html( olobuild_t( 'Solo prodotti disponibili' ) ); ?></span>
                        <button type="button" class="olo-pf-toggle" data-olo-pf-stock-toggle role="switch" aria-checked="false" aria-label="<?php echo esc_attr( olobuild_t( 'Solo prodotti disponibili' ) ); ?>"></button>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php /* --- IN OFFERTA --- */ ?>
            <?php if ( ! empty( $s['show_on_sale'] ) ) : ?>
            <div class="olo-pf-section">
                <?php echo $this->intestazione( olobuild_t( 'Offerte' ), $uid . '-body-sale', $collapsed, $can_fold ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup di intestazione() con esc_html()/esc_attr()
                ?>
                <div class="olo-pf-body<?php echo $collapsed ? ' is-collapsed' : ''; ?>" id="<?php echo esc_attr( $uid ); ?>-body-sale" style="max-height:200px">
                    <div class="olo-pf-toggle-wrap">
                        <span><?php echo esc_html( olobuild_t( 'Solo prodotti in offerta' ) ); ?></span>
                        <button type="button" class="olo-pf-toggle" data-olo-pf-sale-toggle role="switch" aria-checked="false" aria-label="<?php echo esc_attr( olobuild_t( 'Solo prodotti in offerta' ) ); ?>"></button>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php /* --- ORDINAMENTO PER CHI VISITA (orderby di WooCommerce, letto da Prodotti WC) --- */ ?>
            <?php
            if ( ! empty( $s['show_sort'] ) ) :
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- sola lettura dell'ordinamento pubblico scelto nell'indirizzo
                $ord_ora = isset( $_GET['orderby'] ) && is_scalar( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : '';
                $ordini  = [
                    ''           => olobuild_t( 'Come nel negozio' ),
                    'date'       => olobuild_t( 'Più recenti' ),
                    'popularity' => olobuild_t( 'Più venduti' ),
                    'rating'     => olobuild_t( 'Più votati' ),
                    'price'      => olobuild_t( 'Prezzo crescente' ),
                    'price-desc' => olobuild_t( 'Prezzo decrescente' ),
                    'title'      => olobuild_t( 'Nome dalla A alla Z' ),
                ];
                ?>
            <div class="olo-pf-section olo-pf-ordina">
                <label class="olo-pf-ordina-lab" for="<?php echo esc_attr( $uid ); ?>-ordina"><?php echo esc_html( olobuild_t( 'Ordina' ) ); ?></label>
                <select id="<?php echo esc_attr( $uid ); ?>-ordina" data-olo-pf-sort>
                    <?php foreach ( $ordini as $v => $l ) : ?>
                    <option value="<?php echo esc_attr( $v ); ?>"<?php selected( $ord_ora, $v ); ?>><?php echo esc_html( $l ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="olo-pf-actions">
                <?php if ( $apply_btn ) : // senza il pulsante i filtri si applicano a ogni scelta (script sotto) ?>
                <button type="button" class="olo-pf-btn olo-pf-btn-primary" data-olo-pf-apply><?php echo $btn_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped via esc_html() at assignment above ?></button>
                <?php endif; ?>
                <button type="button" class="olo-pf-btn olo-pf-btn-secondary" data-olo-pf-reset><?php echo $reset_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped via esc_html() at assignment above ?></button>
            </div>
        </div>

        <script>
        (function(){
            var root = document.querySelector('.<?php echo esc_js( $uid ); ?>');
            if(!root){return}
            var dropdown  = <?php echo $is_dropdown ? 'true' : 'false'; ?>;
            var autoApply = <?php echo $apply_btn ? 'false' : 'true'; ?>;
            var ajax      = <?php echo ! empty( $s['senza_ricarica'] ) ? 'true' : 'false'; ?>;
            var usatoAjax = false;
            var L = {
                min: '<?php echo esc_js( olobuild_t( 'Prezzo min' ) ); ?>',
                max: '<?php echo esc_js( olobuild_t( 'Prezzo max' ) ); ?>',
                cat: '<?php echo esc_js( olobuild_t( 'Categorie' ) ); ?>',
                stock: '<?php echo esc_js( olobuild_t( 'Solo disponibili' ) ); ?>',
                sale: '<?php echo esc_js( olobuild_t( 'In offerta' ) ); ?>'
            };
            /* I parametri che il filtro scrive (nomi di WooCommerce): «Rimuovi tutti» e «Resetta»
               tolgono solo questi, non gli altri dell'indirizzo (lingua, tracciamenti…). */
            function isFilterParam(k){
                if(k === 'min_price'){return true}
                if(k === 'max_price'){return true}
                if(k === 'product_cat'){return true}
                if(k === 'in_stock'){return true}
                if(k === 'on_sale'){return true}
                if(k === 'paged'){return true}
                return k.indexOf('filter_') === 0;
            }
            /* SENZA RICARICARE: si scarica la stessa pagina coi parametri nuovi e si sostituiscono le tile Prodotti WC
               (la tile intera, per id, col suo CSS). Se una griglia manca, o la rete sbaglia, si ricarica come prima. */
            function griglie(){
                var out = [];
                document.querySelectorAll('[data-olo-woo-griglia]').forEach(function(el){
                    var w = el.closest('.olo-frontend-tile[id]');
                    if(w){ if(out.indexOf(w) < 0){ out.push(w); } }
                });
                return out;
            }
            function trovaIn(doc, id){
                var el = doc.getElementById(id);
                if(el){ return el; }
                var ts = doc.querySelectorAll('template');
                for(var i = 0; i < ts.length; i++){
                    var f = ts[i].content.querySelector('[id="' + id + '"]');
                    if(f){ return f; }
                }
                return null;
            }
            function go(params){
                /* Filtri nuovi, si riparte dalla prima pagina della griglia (pagina 2 di 18 prodotti è vuota su 7). */
                params.delete('product-page');
                params.delete('paged');
                var q   = params.toString();
                var url = window.location.pathname + (q ? '?' + q : '');
                var gs  = ajax ? griglie() : [];
                if(!gs.length || !window.fetch || !window.DOMParser){ window.location.href = url; return; }
                gs.forEach(function(w){ w.setAttribute('aria-busy', 'true'); w.style.transition = 'opacity .2s ease'; w.style.opacity = '.45'; });
                fetch(url, { credentials: 'same-origin' })
                .then(function(r){ if(!r.ok){ throw new Error(String(r.status)); } return r.text(); })
                .then(function(html){
                    var doc = new DOMParser().parseFromString(html, 'text/html');
                    var nuovi = gs.map(function(w){ return trovaIn(doc, w.id); });
                    if(nuovi.indexOf(null) > -1){ window.location.href = url; return; }
                    gs.forEach(function(w, i){
                        var el = document.importNode(nuovi[i], true);
                        w.parentNode.replaceChild(el, w);
                        try { document.dispatchEvent(new CustomEvent('olo:lazy-hydrated', { detail: { target: el } })); } catch(e){}
                    });
                    usatoAjax = true;
                    if(history.pushState){ history.pushState({ oloFiltro: 1 }, '', url); }
                    mostraAttivi(new URLSearchParams(window.location.search));
                    var primo = griglie()[0];
                    if(primo){ if(primo.getBoundingClientRect().top < 0){ primo.scrollIntoView({ behavior: 'smooth', block: 'start' }); } }
                })
                .catch(function(){ window.location.href = url; });
            }
            /* Indietro e avanti dopo un filtro sul posto: la pagina giusta si ricarica dall'indirizzo. */
            window.addEventListener('popstate', function(){ if(usatoAjax){ window.location.reload(); } });
            function cleanParams(){
                var params = new URLSearchParams(window.location.search);
                var keys = [];
                params.forEach(function(v, k){ if(isFilterParam(k)){ keys.push(k); } });
                keys.forEach(function(k){ params.delete(k); });
                return params;
            }
            function findBox(cls, value){
                var list = root.querySelectorAll(cls);
                for(var i = 0; i < list.length; i++){ if(list[i].value === value){ return list[i]; } }
                return null;
            }

            /* --- Collapsible sections (a tendina: una aperta alla volta) --- */
            var toggles = root.querySelectorAll('[data-olo-pf-toggle]');
            /* A tendina, su schermo largo: un pulsante vicino al bordo destro apriva il pannello
               fuori dalla finestra; allora lo si allinea al bordo destro del pulsante. Sotto i 640 px
               ci pensa il CSS (pannello largo quanto la barra). */
            function fitPanel(body){
                body.style.left = '';
                body.style.right = '';
                if(!window.matchMedia('(min-width: 640px)').matches){return}
                var r = body.getBoundingClientRect();
                if(r.right > document.documentElement.clientWidth - 8){
                    body.style.left = 'auto';
                    body.style.right = '0';
                }
            }
            function setOpen(h, open){
                var body = h.nextElementSibling;
                if(open){
                    h.classList.remove('is-collapsed');
                    if(body){ body.classList.remove('is-collapsed'); }
                    if(dropdown){ if(body){ fitPanel(body); } }
                    h.setAttribute('aria-expanded', 'true');
                } else {
                    h.classList.add('is-collapsed');
                    if(body){ body.classList.add('is-collapsed'); }
                    h.setAttribute('aria-expanded', 'false');
                }
            }
            toggles.forEach(function(h){
                h.addEventListener('click', function(){
                    var willOpen = h.classList.contains('is-collapsed');
                    if(dropdown){
                        toggles.forEach(function(o){ if(o !== h){ setOpen(o, false); } });
                    }
                    setOpen(h, willOpen);
                });
            });
            if(dropdown){
                document.addEventListener('click', function(e){
                    if(root.contains(e.target)){return}
                    toggles.forEach(function(o){ setOpen(o, false); });
                });
                root.addEventListener('keydown', function(e){
                    if(e.key !== 'Escape'){return}
                    toggles.forEach(function(o){
                        if(o.getAttribute('aria-expanded') === 'true'){ setOpen(o, false); o.focus(); }
                    });
                });
            }

            /* --- Price range sync --- */
            var pMin = root.querySelector('.olo-pf-price-min');
            var pMax = root.querySelector('.olo-pf-price-max');
            var pMinLabel = root.querySelector('[data-olo-pf-min-label]');
            var pMaxLabel = root.querySelector('[data-olo-pf-max-label]');
            if(pMin){
                pMin.addEventListener('input', function(){ if(pMinLabel){pMinLabel.textContent=pMin.value} });
            }
            if(pMax){
                pMax.addEventListener('input', function(){ if(pMaxLabel){pMaxLabel.textContent=pMax.value} });
            }

            /* --- Stock toggle --- */
            var stockToggle = root.querySelector('[data-olo-pf-stock-toggle]');
            if(stockToggle){
                stockToggle.addEventListener('click', function(){
                    if(stockToggle.classList.contains('is-active')){
                        stockToggle.classList.remove('is-active');
                        stockToggle.setAttribute('aria-checked', 'false');
                    } else {
                        stockToggle.classList.add('is-active');
                        stockToggle.setAttribute('aria-checked', 'true');
                    }
                    if(autoApply){ apply(); }
                });
            }

            /* --- In offerta (come «Solo disponibili») --- */
            var saleToggle = root.querySelector('[data-olo-pf-sale-toggle]');
            if(saleToggle){
                saleToggle.addEventListener('click', function(){
                    var on = !saleToggle.classList.contains('is-active');
                    saleToggle.classList.toggle('is-active', on);
                    saleToggle.setAttribute('aria-checked', on ? 'true' : 'false');
                    if(autoApply){ apply(); }
                });
            }
            /* --- Ordinamento: si applica subito e resta anche azzerando i filtri --- */
            var sortSel = root.querySelector('[data-olo-pf-sort]');
            if(sortSel){
                sortSel.addEventListener('change', function(){
                    var p = new URLSearchParams(window.location.search);
                    if(sortSel.value){ p.set('orderby', sortSel.value); } else { p.delete('orderby'); }
                    p.delete('paged');
                    go(p);
                });
            }

            /* --- Scelte già nell'indirizzo: si ripropongono sempre, anche senza «Filtri attivi» --- */
            var sp = new URLSearchParams(window.location.search);
            var catParam = sp.get('product_cat');
            if(catParam){
                catParam.split(',').forEach(function(slug){
                    var cb = findBox('.olo-pf-cat', slug);
                    if(cb){ cb.checked = true; }
                });
            }
            sp.forEach(function(v, k){
                if(k.indexOf('filter_') !== 0){return}
                var tax = 'pa_' + k.substring(7);
                v.split(',').forEach(function(slug){
                    root.querySelectorAll('.olo-pf-attr').forEach(function(cb){
                        if(cb.getAttribute('data-taxonomy') === tax){ if(cb.value === slug){ cb.checked = true; } }
                    });
                });
            });
            if(saleToggle){ if(sp.get('on_sale')){ saleToggle.classList.add('is-active'); saleToggle.setAttribute('aria-checked', 'true'); } }
            if(sp.get('in_stock')){
                if(stockToggle){ stockToggle.classList.add('is-active'); stockToggle.setAttribute('aria-checked', 'true'); }
            }
            if(pMin){ if(sp.get('min_price')){ pMin.value = sp.get('min_price'); if(pMinLabel){pMinLabel.textContent=pMin.value} } }
            if(pMax){ if(sp.get('max_price')){ pMax.value = sp.get('max_price'); if(pMaxLabel){pMaxLabel.textContent=pMax.value} } }

            /* --- Active filters from URL ---
               I valori vengono dall'indirizzo: si scrivono come testo (textContent), mai come HTML. */
            var activeWrap = root.querySelector('[data-olo-pf-active]');
            var activeTags = root.querySelector('[data-olo-pf-active-tags]');
            var clearAll   = root.querySelector('[data-olo-pf-clear-all]');
            function mostraAttivi(sp){
                if(!activeWrap){ return; }
                var catParam = sp.get('product_cat');
                var tags = [];
                if(sp.get('min_price')){ tags.push(L.min + ': ' + sp.get('min_price')); }
                if(sp.get('max_price')){ tags.push(L.max + ': ' + sp.get('max_price')); }
                /* Il nome scritto accanto alla casella («Capsule»), non lo slug dell'indirizzo («capsule»). */
                function nomi(sel, valore){
                    return String(valore).split(',').map(function(slug){
                        var box = Array.prototype.find.call(root.querySelectorAll(sel), function(b){ return b.value === slug; });
                        var nome = box && box.parentNode.querySelector('span:not(.olo-pf-count)');
                        return nome ? nome.textContent.trim() : slug;
                    }).join(', ');
                }
                if(catParam){ tags.push(L.cat + ': ' + nomi('.olo-pf-cat', catParam)); }
                if(sp.get('in_stock')){ tags.push(L.stock); }
                if(sp.get('on_sale')){ tags.push(L.sale); }
                sp.forEach(function(v, k){
                    if(k.indexOf('filter_') === 0){
                        var tassonomia = 'pa_' + k.replace('filter_', '');
                        var sezione = root.querySelector('.olo-pf-attr[data-taxonomy="' + tassonomia + '"]');
                        var titolo = sezione && sezione.closest('.olo-pf-section') && sezione.closest('.olo-pf-section').querySelector('.olo-pf-heading span');
                        tags.push((titolo ? titolo.textContent.trim() : k.replace('filter_', '')) + ': ' + nomi('.olo-pf-attr[data-taxonomy="' + tassonomia + '"]', v));
                    }
                });
                activeWrap.style.display = tags.length > 0 ? 'block' : 'none';
                if(activeTags){
                    activeTags.textContent = '';
                    tags.forEach(function(t){
                        var el = document.createElement('span');
                        el.className = 'olo-pf-active-tag';
                        el.textContent = t;
                        activeTags.appendChild(el);
                    });
                }
            }
            mostraAttivi(sp);
            if(activeWrap){
                if(clearAll){
                    clearAll.addEventListener('click', function(e){
                        e.preventDefault();
                        go(cleanParams());
                    });
                }
            }

            /* --- Apply filters via URL params ---
               Nomi di WooCommerce (product_cat, filter_<attributo>, min_price/max_price): li legge
               la tile Prodotti WC della pagina, e negli archivi del negozio WooCommerce stesso. */
            function apply(){
                var params = cleanParams();

                /* Price: solo se il cursore è stato spostato. Il browser arrotonda il massimo al passo
                   sotto (min 89, max 3099, passo 20 → 3089): scritto sempre, anche filtrando per sola
                   categoria, toglieva in silenzio i prodotti più cari, e min_price quelli senza prezzo.
                   L'ultimo passo raggiungibile vale come estremo. */
                if(pMin){
                    if(Number(pMin.value) > Number(pMin.min)){ params.set('min_price', pMin.value); }
                }
                if(pMax){
                    if(Number(pMax.value) + Number(pMax.step) <= Number(pMax.max)){ params.set('max_price', pMax.value); }
                }

                /* Categories */
                var cats = [];
                root.querySelectorAll('.olo-pf-cat:checked').forEach(function(c){ cats.push(c.value); });
                if(cats.length > 0){ params.set('product_cat', cats.join(',')); }

                /* Attributes */
                var attrs = {};
                root.querySelectorAll('.olo-pf-attr:checked').forEach(function(c){
                    var tax = c.getAttribute('data-taxonomy');
                    if(!attrs[tax]){ attrs[tax] = []; }
                    attrs[tax].push(c.value);
                });
                Object.keys(attrs).forEach(function(tax){
                    params.set('filter_' + tax.replace('pa_',''), attrs[tax].join(','));
                });

                /* Stock */
                if(stockToggle){
                    if(stockToggle.classList.contains('is-active')){ params.set('in_stock', '1'); }
                }
                if(saleToggle){ if(saleToggle.classList.contains('is-active')){ params.set('on_sale', '1'); } }

                go(params);
            }
            var applyBtn = root.querySelector('[data-olo-pf-apply]');
            if(applyBtn){ applyBtn.addEventListener('click', apply); }
            /* Senza «Applica» ogni scelta filtra subito (il cursore del prezzo quando lo si lascia). */
            if(autoApply){
                root.querySelectorAll('.olo-pf-cat, .olo-pf-attr, .olo-pf-price-min, .olo-pf-price-max').forEach(function(el){
                    el.addEventListener('change', apply);
                });
            }

            /* --- Reset --- */
            var resetBtn = root.querySelector('[data-olo-pf-reset]');
            if(resetBtn){
                resetBtn.addEventListener('click', function(){
                    /* Uncheck all */
                    root.querySelectorAll('input[type="checkbox"]').forEach(function(c){ c.checked = false; });
                    /* Reset price sliders */
                    if(pMin){ pMin.value = pMin.min; if(pMinLabel){pMinLabel.textContent=pMin.value} }
                    if(pMax){ pMax.value = pMax.max; if(pMaxLabel){pMaxLabel.textContent=pMax.value} }
                    /* Reset stock toggle */
                    if(stockToggle){ stockToggle.classList.remove('is-active'); stockToggle.setAttribute('aria-checked', 'false'); }
                    if(saleToggle){ saleToggle.classList.remove('is-active'); saleToggle.setAttribute('aria-checked', 'false'); }
                    /* Clear URL (solo i filtri) */
                    go(cleanParams());
                });
            }
        })();
        </script>
        <?php
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by build_border_hover_css()/build_border_effect_css() base-class helpers
        }
        return ob_get_clean();
    }

    /**
     * Un interruttore che ha due nomi: quello dell'inspector ($nuova) vince quando c'è, altrimenti
     * vale quello storico del PHP ($vecchia), che i template importati portano ancora.
     */
    private function scelta( $s, $nuova, $vecchia ) {
        $v = array_key_exists( $nuova, $s ) ? $s[ $nuova ] : ( $s[ $vecchia ] ?? false );
        return filter_var( $v, FILTER_VALIDATE_BOOLEAN );
    }

    /**
     * Il titolo di una sezione: pulsante con la freccia se la sezione si apre e chiude, testo fermo
     * se «Sezioni richiudibili» è spento (prima le sezioni erano sempre richiudibili).
     */
    private function intestazione( $testo, $body_id, $chiusa, $richiudibile ) {
        if ( ! $richiudibile ) {
            return '<div class="olo-pf-heading is-static"><span>' . esc_html( $testo ) . '</span></div>';
        }
        // data-olo-interactive: nel canvas del builder iframe-bridge.js ferma ogni clic che non lo
        // porta, e le sezioni (a tendina: tutti i pannelli) non si aprivano mai mentre le si stilava.
        return '<button type="button" class="olo-pf-heading' . ( $chiusa ? ' is-collapsed' : '' ) . '" data-olo-pf-toggle data-olo-interactive'
            . ' aria-expanded="' . ( $chiusa ? 'false' : 'true' ) . '" aria-controls="' . esc_attr( $body_id ) . '">'
            . '<span>' . esc_html( $testo ) . '</span>'
            . '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false"><polyline points="6 9 12 15 18 9"/></svg>'
            . '</button>';
    }

    /**
     * Il prezzo più basso e più alto dei prodotti che chi visita può trovare, [min, max] o null (24 nov 2026): solo i
     * prodotti pubblicati e visibili nel catalogo (senza gli esauriti, se il negozio li nasconde), della categoria
     * aperta quando la pagina è una categoria del negozio. La ricerca passa da WP_Query, così valgono i filtri del sito:
     * nella demo di un tema solo i suoi prodotti, con una lingua solo i prodotti di quella lingua. Prima si leggeva ogni
     * _price del database (varianti, bozze, cestino e i negozi delle altre demo) e in un negozio di borse il cursore
     * andava da 2 a 37992 euro.
     */
    private function fascia_prezzi() {
        if ( ! function_exists( 'wc_get_product_visibility_term_ids' ) ) {
            return null;
        }
        $nascosti = [];
        $termini  = wc_get_product_visibility_term_ids();
        if ( ! empty( $termini['exclude-from-catalog'] ) ) {
            $nascosti[] = (int) $termini['exclude-from-catalog'];
        }
        if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && ! empty( $termini['outofstock'] ) ) {
            $nascosti[] = (int) $termini['outofstock'];
        }
        $tax = [];
        if ( $nascosti ) {
            $tax[] = [ 'taxonomy' => 'product_visibility', 'field' => 'term_taxonomy_id', 'terms' => $nascosti, 'operator' => 'NOT IN' ];
        }
        if ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() ) {
            $aperta = get_queried_object();
            if ( $aperta instanceof WP_Term ) {
                $tax[] = [ 'taxonomy' => $aperta->taxonomy, 'field' => 'term_id', 'terms' => [ (int) $aperta->term_id ] ];
            }
        }
        $args = [
            'post_type'              => 'product',
            'post_status'            => 'publish',
            'fields'                 => 'ids',
            'posts_per_page'         => -1,
            'orderby'                => 'none',
            'no_found_rows'          => true,
            'ignore_sticky_posts'    => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ];
        if ( $tax ) {
            $args['tax_query'] = $tax; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- visibilità del catalogo e categoria aperta, come la griglia del negozio
        }
        static $gia = [];
        $chiave = md5( wp_json_encode( $args ) );
        if ( array_key_exists( $chiave, $gia ) ) {
            return $gia[ $chiave ];
        }
        $ids = array_filter( array_map( 'intval', ( new WP_Query( $args ) )->posts ) );
        $gia[ $chiave ] = null;
        if ( ! $ids ) {
            return null;
        }
        global $wpdb;
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- MIN/MAX di _price sui soli prodotti trovati sopra: gli id sono interi (intval), il resto è testo fisso; tenuto per la richiesta in $gia.
        $riga = $wpdb->get_row(
            "SELECT MIN(CAST(meta_value AS DECIMAL(12,2))) AS piu_basso, MAX(CAST(meta_value AS DECIMAL(12,2))) AS piu_alto
             FROM {$wpdb->postmeta} WHERE meta_key = '_price' AND meta_value <> '' AND post_id IN (" . implode( ',', $ids ) . ')'
        );
        // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        if ( ! $riga || null === $riga->piu_alto ) {
            return null;
        }
        $gia[ $chiave ] = [ (float) $riga->piu_basso, (float) $riga->piu_alto ];
        return $gia[ $chiave ];
    }

    /** Un passo tondo (1, 2, 5, 10, 20, 50…) non più piccolo di $voluto, mai sotto 1. */
    private function passo_tondo( $voluto ) {
        if ( $voluto <= 1 ) {
            return 1;
        }
        $potenza = pow( 10, floor( log10( $voluto ) ) );
        foreach ( [ 1, 2, 5, 10 ] as $m ) {
            if ( $m * $potenza >= $voluto ) {
                return (int) ( $m * $potenza );
            }
        }
        return (int) ( 10 * $potenza );
    }
}
