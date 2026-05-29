<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Woo_Product_Filter_Tile extends Olo_Tile_Base {

    protected $type     = 'woo_product_filter';
    protected $name     = 'Filtro Prodotti WC';
    protected $icon     = 'dashicons-filter';
    protected $category = 'woocommerce';
    protected $defaults = [
        'show_price'       => true,
        'show_categories'  => true,
        'show_attributes'  => true,
        'show_stock'       => true,
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
                 . esc_html( olo_t( 'WooCommerce non attivo. Installa e attiva WooCommerce per utilizzare questo elemento.' ) )
                 . '</div>';
        }

        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-woo-filter-' . wp_rand( 10000, 99999 );

        // Colors
        $heading_color = $this->safe_color_css( $s['heading_color'] ) ?: 'var(--olo-color-text, #374151)';
        $text_color    = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text, #1f2937)';
        $accent_color  = $this->safe_color_css( $s['accent_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $bg_color      = $this->safe_color_css( $s['bg_color'] ) ?: 'var(--olo-color-surface, #ffffff)';
        $border_color  = $this->safe_color_css( $s['border_color'] ) ?: 'var(--olo-color-border, #e5e7eb)';
        $radius        = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );
        $radius_raw    = Olo_Tile_Utils::radius_int( $s['border_radius'] ?? 0 );

        // Auto-detect price range from DB
        $price_min  = floatval( $s['price_min'] );
        $price_max  = floatval( $s['price_max'] );
        if ( function_exists( 'wc_get_min_max_price_meta_query' ) ) {
            global $wpdb;
            $actual_min = $wpdb->get_var( "SELECT MIN(CAST(meta_value AS DECIMAL(10,2))) FROM {$wpdb->postmeta} WHERE meta_key = '_price' AND meta_value > 0" );
            $actual_max = $wpdb->get_var( "SELECT MAX(CAST(meta_value AS DECIMAL(10,2))) FROM {$wpdb->postmeta} WHERE meta_key = '_price'" );
            if ( $actual_min ) { $price_min = floor( floatval( $actual_min ) ); }
            if ( $actual_max ) { $price_max = ceil( floatval( $actual_max ) ); }
        }
        $price_step = max( 1, absint( $s['price_step'] ) );

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
            $attr_slugs = array_map( 'trim', explode( ',', sanitize_text_field( $s['attributes'] ) ) );
            foreach ( $attr_slugs as $attr_slug ) {
                $attr_slug = sanitize_text_field( $attr_slug );
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

        $collapsed  = ! empty( $s['collapsed'] );
        $btn_text   = esc_html( $s['button_text'] ?: olo_t( 'Filtra' ) );
        $reset_text = esc_html( $s['reset_text'] ?: olo_t( 'Resetta' ) );

        ob_start();
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
            .<?php echo $uid; ?> .olo-pf-toggle {
                position: relative;
                width: 40px;
                height: 22px;
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
                border-radius: <?php echo max( 4, $radius_raw - 2 ); ?>px;
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
                background: <?php echo $accent_color; ?>;
                color: var(--olo-color-primary-contrast, #FFFFFF);
            }
            .<?php echo $uid; ?> .olo-pf-btn-secondary {
                background: var(--olo-color-muted, #F3F4F6);
                color: <?php echo $text_color; ?>;
            }
        </style>
        <div class="<?php echo esc_attr( $uid ); ?>">

            <?php if ( ! empty( $s['show_active'] ) ) : ?>
            <div class="olo-pf-active-wrap" data-olo-pf-active>
                <div style="font-weight:600;font-size:13px"><?php echo esc_html( olo_t( 'Filtri attivi' ) ); ?></div>
                <div class="olo-pf-active-tags" data-olo-pf-active-tags></div>
                <a href="#" class="olo-pf-clear-all" data-olo-pf-clear-all><?php echo esc_html( olo_t( 'Rimuovi tutti' ) ); ?></a>
            </div>
            <?php endif; ?>

            <?php /* --- PRICE RANGE --- */ ?>
            <?php if ( ! empty( $s['show_price'] ) ) : ?>
            <div class="olo-pf-section">
                <div class="olo-pf-heading<?php echo $collapsed ? ' is-collapsed' : ''; ?>" data-olo-pf-toggle>
                    <span><?php echo esc_html( olo_t( 'Prezzo' ) ); ?></span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="olo-pf-body<?php echo $collapsed ? ' is-collapsed' : ''; ?>" style="max-height:200px">
                    <div class="olo-pf-price-row">
                        <span><?php echo $currency; ?><span data-olo-pf-min-label><?php echo intval( $price_min ); ?></span></span>
                        <input type="range" class="olo-pf-price-min" min="<?php echo $price_min; ?>" max="<?php echo $price_max; ?>" step="<?php echo $price_step; ?>" value="<?php echo $price_min; ?>" />
                    </div>
                    <div class="olo-pf-price-row">
                        <span><?php echo $currency; ?><span data-olo-pf-max-label><?php echo intval( $price_max ); ?></span></span>
                        <input type="range" class="olo-pf-price-max" min="<?php echo $price_min; ?>" max="<?php echo $price_max; ?>" step="<?php echo $price_step; ?>" value="<?php echo $price_max; ?>" />
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php /* --- CATEGORIES --- */ ?>
            <?php if ( ! empty( $s['show_categories'] ) ) : ?>
            <?php if ( ! empty( $categories ) ) : ?>
            <div class="olo-pf-section">
                <div class="olo-pf-heading<?php echo $collapsed ? ' is-collapsed' : ''; ?>" data-olo-pf-toggle>
                    <span><?php echo esc_html( olo_t( 'Categorie' ) ); ?></span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="olo-pf-body<?php echo $collapsed ? ' is-collapsed' : ''; ?>" style="max-height:400px">
                    <div class="olo-pf-check-list">
                        <?php foreach ( $categories as $cat ) : ?>
                        <label class="olo-pf-check-item">
                            <input type="checkbox" class="olo-pf-cat" value="<?php echo esc_attr( $cat->slug ); ?>" />
                            <span><?php echo esc_html( $cat->name ); ?></span>
                            <span class="olo-pf-count">(<?php echo absint( $cat->count ); ?>)</span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php endif; ?>

            <?php /* --- ATTRIBUTES --- */ ?>
            <?php foreach ( $attribute_sections as $attr ) : ?>
            <div class="olo-pf-section">
                <div class="olo-pf-heading<?php echo $collapsed ? ' is-collapsed' : ''; ?>" data-olo-pf-toggle>
                    <span><?php echo esc_html( $attr['label'] ); ?></span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="olo-pf-body<?php echo $collapsed ? ' is-collapsed' : ''; ?>" style="max-height:400px">
                    <div class="olo-pf-check-list">
                        <?php foreach ( $attr['terms'] as $term ) : ?>
                        <label class="olo-pf-check-item">
                            <input type="checkbox" class="olo-pf-attr" data-taxonomy="<?php echo esc_attr( $attr['taxonomy'] ); ?>" value="<?php echo esc_attr( $term->slug ); ?>" />
                            <span><?php echo esc_html( $term->name ); ?></span>
                            <span class="olo-pf-count">(<?php echo absint( $term->count ); ?>)</span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <?php /* --- IN-STOCK TOGGLE --- */ ?>
            <?php if ( ! empty( $s['show_stock'] ) ) : ?>
            <div class="olo-pf-section">
                <div class="olo-pf-heading<?php echo $collapsed ? ' is-collapsed' : ''; ?>" data-olo-pf-toggle>
                    <span><?php echo esc_html( olo_t( 'Disponibilità' ) ); ?></span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="olo-pf-body<?php echo $collapsed ? ' is-collapsed' : ''; ?>" style="max-height:200px">
                    <div class="olo-pf-toggle-wrap">
                        <span><?php echo esc_html( olo_t( 'Solo prodotti disponibili' ) ); ?></span>
                        <div class="olo-pf-toggle" data-olo-pf-stock-toggle></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="olo-pf-actions">
                <button type="button" class="olo-pf-btn olo-pf-btn-primary" data-olo-pf-apply><?php echo $btn_text; ?></button>
                <button type="button" class="olo-pf-btn olo-pf-btn-secondary" data-olo-pf-reset><?php echo $reset_text; ?></button>
            </div>
        </div>

        <script>
        (function(){
            var root = document.querySelector('.<?php echo $uid; ?>');
            if(!root){return}

            /* --- Collapsible sections --- */
            var toggles = root.querySelectorAll('[data-olo-pf-toggle]');
            toggles.forEach(function(h){
                h.addEventListener('click', function(){
                    var body = h.nextElementSibling;
                    if(!body){return}
                    var isCol = h.classList.contains('is-collapsed');
                    if(isCol){
                        h.classList.remove('is-collapsed');
                        body.classList.remove('is-collapsed');
                    } else {
                        h.classList.add('is-collapsed');
                        body.classList.add('is-collapsed');
                    }
                });
            });

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
                    } else {
                        stockToggle.classList.add('is-active');
                    }
                });
            }

            /* --- Active filters from URL --- */
            var activeWrap = root.querySelector('[data-olo-pf-active]');
            var activeTags = root.querySelector('[data-olo-pf-active-tags]');
            var clearAll   = root.querySelector('[data-olo-pf-clear-all]');
            if(activeWrap){
                var sp   = new URLSearchParams(window.location.search);
                var tags = [];
                if(sp.get('min_price')){ tags.push('Prezzo min: ' + sp.get('min_price')); }
                if(sp.get('max_price')){ tags.push('Prezzo max: ' + sp.get('max_price')); }
                if(sp.get('product_cat')){ tags.push('Cat: ' + sp.get('product_cat')); }
                if(sp.get('in_stock')){ tags.push('Solo disponibili'); }
                sp.forEach(function(v, k){
                    if(k.indexOf('filter_') === 0){ tags.push(k.replace('filter_','') + ': ' + v); }
                });
                if(tags.length > 0){
                    activeWrap.style.display = 'block';
                    if(activeTags){
                        activeTags.innerHTML = tags.map(function(t){
                            return '<span class="olo-pf-active-tag">' + t + '</span>';
                        }).join('');
                    }
                    /* Pre-fill checkboxes from URL */
                    var catParam = sp.get('product_cat');
                    if(catParam){
                        catParam.split(',').forEach(function(slug){
                            var cb = root.querySelector('.olo-pf-cat[value="' + slug + '"]');
                            if(cb){ cb.checked = true; }
                        });
                    }
                    if(sp.get('in_stock')){
                        if(stockToggle){ stockToggle.classList.add('is-active'); }
                    }
                    if(pMin){ if(sp.get('min_price')){ pMin.value = sp.get('min_price'); if(pMinLabel){pMinLabel.textContent=pMin.value} } }
                    if(pMax){ if(sp.get('max_price')){ pMax.value = sp.get('max_price'); if(pMaxLabel){pMaxLabel.textContent=pMax.value} } }
                }
                if(clearAll){
                    clearAll.addEventListener('click', function(e){
                        e.preventDefault();
                        window.location.search = '';
                    });
                }
            }

            /* --- Apply filters via URL params --- */
            var applyBtn = root.querySelector('[data-olo-pf-apply]');
            if(applyBtn){
                applyBtn.addEventListener('click', function(){
                    var params = new URLSearchParams(window.location.search);

                    /* Price */
                    if(pMin){ params.set('min_price', pMin.value); }
                    if(pMax){ params.set('max_price', pMax.value); }

                    /* Categories */
                    var cats = [];
                    root.querySelectorAll('.olo-pf-cat:checked').forEach(function(c){ cats.push(c.value); });
                    if(cats.length > 0){ params.set('product_cat', cats.join(',')); } else { params.delete('product_cat'); }

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
                        if(stockToggle.classList.contains('is-active')){
                            params.set('in_stock', '1');
                        } else {
                            params.delete('in_stock');
                        }
                    }

                    window.location.search = params.toString();
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
                    if(stockToggle){ stockToggle.classList.remove('is-active'); }
                    /* Clear URL */
                    window.location.search = '';
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
            if ( $border_css ) echo ".{$uid}{{$border_css}}";
            echo $border_hover_css . $border_effect_css . '</style>';
        }
        return ob_get_clean();
    }
}
