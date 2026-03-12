<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Woo_Product_Bundle_Tile extends Olo_Tile_Base {

    protected $type     = 'woo_product_bundle';
    protected $name     = 'Bundle Prodotti WC';
    protected $icon     = 'dashicons-screenoptions';
    protected $category = 'woocommerce';
    protected $defaults = [
        'product_ids'      => '',
        'discount_percent' => 10,
        'bundle_title'     => 'Acquista insieme e risparmia',
        'show_savings'     => true,
        'show_image'       => true,
        'show_price'       => true,
        'show_description' => false,
        'card_style'       => 'border',
        'layout'           => 'horizontal',
        'title_color'      => '',
        'price_color'      => '',
        'discount_color'   => '#059669',
        'savings_bg'       => '#ECFDF5',
        'button_bg'        => '#6366F1',
        'button_color'     => '#FFFFFF',
        'bg_color'         => '#FFFFFF',
        'border_color'     => '#E5E7EB',
        'border_radius'    => 12,
        'button_text'      => 'Aggiungi bundle al carrello',
        'gap'              => 16,
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

        // Parse product IDs
        $raw_ids = sanitize_text_field( $s['product_ids'] );
        if ( empty( $raw_ids ) ) {
            return '<div style="padding:30px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);font-size:14px">'
                 . esc_html( olo_t( 'Nessun prodotto nel bundle. Inserisci gli ID dei prodotti nelle impostazioni.' ) )
                 . '</div>';
        }

        $product_ids = array_filter( array_map( 'absint', explode( ',', $raw_ids ) ) );
        if ( empty( $product_ids ) ) {
            return '';
        }

        // Load products
        $products   = [];
        $total_price = 0;
        foreach ( $product_ids as $pid ) {
            $product = wc_get_product( $pid );
            if ( ! $product ) {
                continue;
            }
            if ( $product->get_status() !== 'publish' ) {
                continue;
            }
            $price = (float) $product->get_price();
            $total_price += $price;
            $products[] = [
                'product'    => $product,
                'id'         => $pid,
                'title'      => $product->get_name(),
                'url'        => get_permalink( $pid ),
                'image_id'   => get_post_thumbnail_id( $pid ),
                'price'      => $price,
                'price_html' => $product->get_price_html(),
                'excerpt'    => $product->get_short_description(),
                'add_url'    => $product->add_to_cart_url(),
            ];
        }

        if ( empty( $products ) ) {
            return '<div style="padding:30px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);font-size:14px">'
                 . esc_html( olo_t( 'Nessun prodotto valido nel bundle.' ) )
                 . '</div>';
        }

        $uid = 'olo-woo-bundle-' . wp_rand( 10000, 99999 );

        // Discount calculation
        $discount_pct   = max( 0, min( 100, absint( $s['discount_percent'] ) ) );
        $discount_amount = $total_price * ( $discount_pct / 100 );
        $bundle_price    = $total_price - $discount_amount;

        // Colors
        $title_color    = $this->safe_color_css( $s['title_color'] ) ?: 'var(--olo-color-text, #374151)';
        $price_color    = $this->safe_color_css( $s['price_color'] ) ?: 'var(--olo-color-text, #374151)';
        $discount_color = $this->safe_color_css( $s['discount_color'] ) ?: '#059669';
        $savings_bg     = $this->safe_color_css( $s['savings_bg'] ) ?: '#ECFDF5';
        $btn_bg         = $this->safe_color_css( $s['button_bg'] ) ?: '#6366F1';
        $btn_color      = $this->safe_color_css( $s['button_color'] ) ?: '#FFFFFF';
        $bg_color       = $this->safe_color_css( $s['bg_color'] ) ?: '#FFFFFF';
        $border_color   = $this->safe_color_css( $s['border_color'] ) ?: '#E5E7EB';
        $radius         = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $gap            = absint( $s['gap'] );
        $is_horizontal  = ( $s['layout'] === 'horizontal' );

        $bundle_title = sanitize_text_field( $s['bundle_title'] );
        $btn_text     = esc_html( $s['button_text'] ?: olo_t( 'Aggiungi bundle al carrello' ) );

        // Card style
        $card_extra = '';
        if ( $s['card_style'] === 'shadow' ) {
            $card_extra = 'box-shadow:0 1px 3px rgba(0,0,0,0.1);';
        } elseif ( $s['card_style'] === 'border' ) {
            $card_extra = 'border:1px solid ' . $border_color . ';';
        }

        // Plus sign SVG
        $plus_svg = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--olo-color-text-muted, #9CA3AF)" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>';

        // Product IDs for JS (comma-separated)
        $js_product_ids = implode( ',', array_column( $products, 'id' ) );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                background: <?php echo $bg_color; ?>;
                border: 1px solid <?php echo $border_color; ?>;
                border-radius: <?php echo $radius; ?>;
                padding: 24px;
            }
            .<?php echo $uid; ?>-title {
                font-size: 20px;
                font-weight: 700;
                color: <?php echo $title_color; ?>;
                margin: 0 0 20px;
            }
            .<?php echo $uid; ?>-items {
                display: flex;
                <?php if ( $is_horizontal ) : ?>
                flex-direction: row;
                flex-wrap: wrap;
                <?php else : ?>
                flex-direction: column;
                <?php endif; ?>
                align-items: center;
                gap: <?php echo $gap; ?>px;
            }
            .<?php echo $uid; ?>-item {
                <?php echo $card_extra; ?>
                border-radius: 8px;
                overflow: hidden;
                background: var(--olo-color-background, #FFFFFF);
                text-align: center;
                <?php if ( $is_horizontal ) : ?>
                flex: 1;
                min-width: 120px;
                max-width: 200px;
                <?php else : ?>
                width: 100%;
                display: flex;
                align-items: center;
                gap: 16px;
                text-align: left;
                <?php endif; ?>
            }
            .<?php echo $uid; ?>-item-img {
                <?php if ( $is_horizontal ) : ?>
                width: 100%;
                <?php else : ?>
                width: 80px;
                height: 80px;
                flex-shrink: 0;
                <?php endif; ?>
                overflow: hidden;
            }
            .<?php echo $uid; ?>-item-img img {
                width: 100%;
                height: <?php echo $is_horizontal ? 'auto' : '80px'; ?>;
                object-fit: cover;
                display: block;
            }
            .<?php echo $uid; ?>-item-info {
                padding: <?php echo $is_horizontal ? '10px' : '0'; ?>;
                <?php if ( ! $is_horizontal ) : ?>flex: 1;<?php endif; ?>
            }
            .<?php echo $uid; ?>-item-name {
                font-size: 13px;
                font-weight: 600;
                color: <?php echo $title_color; ?>;
                margin: 0 0 4px;
                line-height: 1.3;
            }
            .<?php echo $uid; ?>-item-name a {
                color: inherit;
                text-decoration: none;
            }
            .<?php echo $uid; ?>-item-name a:hover {
                text-decoration: underline;
            }
            .<?php echo $uid; ?>-item-price {
                font-size: 14px;
                font-weight: 700;
                color: <?php echo $price_color; ?>;
            }
            .<?php echo $uid; ?>-item-desc {
                font-size: 12px;
                color: var(--olo-color-text-muted, #9CA3AF);
                margin-top: 4px;
                line-height: 1.4;
            }
            .<?php echo $uid; ?>-plus {
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }
            .<?php echo $uid; ?>-summary {
                margin-top: 24px;
                padding-top: 20px;
                border-top: 1px solid <?php echo $border_color; ?>;
            }
            .<?php echo $uid; ?>-price-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 8px;
                font-size: 14px;
                color: var(--olo-color-text-muted, #9CA3AF);
            }
            .<?php echo $uid; ?>-price-row .olo-bundle-val {
                font-weight: 600;
                color: <?php echo $price_color; ?>;
            }
            .<?php echo $uid; ?>-savings {
                background: <?php echo $savings_bg; ?>;
                color: <?php echo $discount_color; ?>;
                padding: 10px 14px;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 600;
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 16px;
            }
            .<?php echo $uid; ?>-total {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 16px;
                font-size: 18px;
                font-weight: 700;
                color: <?php echo $title_color; ?>;
            }
            .<?php echo $uid; ?>-atc-btn {
                display: block;
                width: 100%;
                padding: 14px 24px;
                background: <?php echo $btn_bg; ?>;
                color: <?php echo $btn_color; ?>;
                border: none;
                border-radius: 8px;
                font-size: 15px;
                font-weight: 700;
                text-align: center;
                cursor: pointer;
                transition: opacity 0.2s ease;
                text-decoration: none;
            }
            .<?php echo $uid; ?>-atc-btn:hover {
                opacity: 0.9;
            }
            @media (max-width: 640px) {
                .<?php echo $uid; ?>-items {
                    flex-direction: column;
                }
                .<?php echo $uid; ?>-item {
                    max-width: 100%;
                    width: 100%;
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    text-align: left;
                }
                .<?php echo $uid; ?>-item-img {
                    width: 70px;
                    height: 70px;
                    flex-shrink: 0;
                }
                .<?php echo $uid; ?>-item-img img {
                    height: 70px;
                }
            }
        </style>

        <div class="<?php echo esc_attr( $uid ); ?>">
            <?php if ( $bundle_title !== '' ) : ?>
            <h3 class="<?php echo esc_attr( $uid ); ?>-title"><?php echo esc_html( $bundle_title ); ?></h3>
            <?php endif; ?>

            <div class="<?php echo esc_attr( $uid ); ?>-items">
                <?php
                $count = count( $products );
                foreach ( $products as $idx => $p ) :
                    $prod = $p['product'];
                ?>
                <div class="<?php echo esc_attr( $uid ); ?>-item">
                    <?php if ( ! empty( $s['show_image'] ) ) : ?>
                    <div class="<?php echo esc_attr( $uid ); ?>-item-img">
                        <?php if ( $p['image_id'] ) : ?>
                        <a href="<?php echo esc_url( $p['url'] ); ?>">
                            <?php echo wp_get_attachment_image( $p['image_id'], 'woocommerce_thumbnail' ); ?>
                        </a>
                        <?php else : ?>
                        <div style="background:var(--olo-color-muted, #F3F4F6);height:100%;display:flex;align-items:center;justify-content:center">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--olo-color-border, #E5E7EB)" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <div class="<?php echo esc_attr( $uid ); ?>-item-info">
                        <div class="<?php echo esc_attr( $uid ); ?>-item-name">
                            <a href="<?php echo esc_url( $p['url'] ); ?>"><?php echo esc_html( $p['title'] ); ?></a>
                        </div>
                        <?php if ( ! empty( $s['show_price'] ) ) : ?>
                        <div class="<?php echo esc_attr( $uid ); ?>-item-price"><?php echo $p['price_html']; ?></div>
                        <?php endif; ?>
                        <?php if ( ! empty( $s['show_description'] ) ) : ?>
                        <?php if ( $p['excerpt'] ) : ?>
                        <div class="<?php echo esc_attr( $uid ); ?>-item-desc"><?php echo wp_kses_post( wp_trim_words( $p['excerpt'], 15 ) ); ?></div>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ( $idx < $count - 1 ) : ?>
                <div class="<?php echo esc_attr( $uid ); ?>-plus"><?php echo $plus_svg; ?></div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <div class="<?php echo esc_attr( $uid ); ?>-summary">
                <div class="<?php echo esc_attr( $uid ); ?>-price-row">
                    <span><?php echo esc_html( olo_t( 'Prezzo totale' ) ); ?></span>
                    <span class="olo-bundle-val"><?php echo wc_price( $total_price ); ?></span>
                </div>

                <?php if ( ! empty( $s['show_savings'] ) ) : ?>
                <?php if ( $discount_pct > 0 ) : ?>
                <div class="<?php echo esc_attr( $uid ); ?>-savings">
                    <span><?php echo esc_html( olo_t( 'Risparmi' ) ); ?> (<?php echo absint( $discount_pct ); ?>%)</span>
                    <span>-<?php echo wc_price( $discount_amount ); ?></span>
                </div>
                <?php endif; ?>
                <?php endif; ?>

                <div class="<?php echo esc_attr( $uid ); ?>-total">
                    <span><?php echo esc_html( olo_t( 'Prezzo bundle' ) ); ?></span>
                    <span><?php echo wc_price( $bundle_price ); ?></span>
                </div>

                <button type="button" class="<?php echo esc_attr( $uid ); ?>-atc-btn" data-olo-bundle-atc><?php echo $btn_text; ?></button>
            </div>
        </div>

        <script>
        (function(){
            var wrap = document.querySelector('.<?php echo $uid; ?>');
            if(!wrap){return}
            var atcBtn = wrap.querySelector('[data-olo-bundle-atc]');
            if(!atcBtn){return}

            var productIds = '<?php echo esc_js( $js_product_ids ); ?>'.split(',');
            var addUrl = '<?php echo esc_js( wc_get_cart_url() ); ?>';

            atcBtn.addEventListener('click', function(){
                atcBtn.disabled = true;
                atcBtn.textContent = '<?php echo esc_js( olo_t( 'Aggiunta in corso...' ) ); ?>';

                var added = 0;
                var total = productIds.length;

                function addNext(){
                    if(added >= total){
                        window.location.href = addUrl;
                        return;
                    }
                    var pid = productIds[added];
                    var url = '<?php echo esc_js( home_url( '/' ) ); ?>?add-to-cart=' + pid;
                    fetch(url, { method: 'GET', credentials: 'same-origin' })
                    .then(function(){
                        added++;
                        addNext();
                    })
                    .catch(function(){
                        added++;
                        addNext();
                    });
                }
                addNext();
            });
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
