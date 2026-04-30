<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Woo_Addtocart_Tile extends Olo_Tile_Base {

    protected $type     = 'woo_addtocart';
    protected $name     = 'Aggiungi al Carrello';
    protected $icon     = 'dashicons-cart';
    protected $category = 'woocommerce';
    protected $defaults = [
        'button_text'    => 'Aggiungi al carrello',
        'show_quantity'  => true,
        'show_icon'      => true,
        'icon'           => 'cart',
        'style'          => 'filled',
        'size'           => 'medium',
        'full_width'     => false,
        'bg_color'       => '#6366F1',
        'text_color'     => '#FFFFFF',
        'hover_bg'       => '#4F46E5',
        'hover_text'     => '#FFFFFF',
        'border_radius'  => 6,
        'quantity_style'  => 'input',
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

        // Get the current product
        global $product;
        if ( ! is_a( $product, 'WC_Product' ) ) {
            $product = wc_get_product( get_the_ID() );
        }
        if ( ! $product ) {
            return '<div style="padding:20px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);font-size:14px;">'
                 . esc_html( olo_t( 'Nessun prodotto disponibile in questo contesto' ) )
                 . '</div>';
        }

        $uid = 'olo-woo-atc-' . wp_rand( 10000, 99999 );

        // Colors
        $bg_color   = $this->safe_color_css( $s['bg_color'] );
        $text_color = $this->safe_color_css( $s['text_color'] );
        $hover_bg   = $this->safe_color_css( $s['hover_bg'] );
        $hover_text = $this->safe_color_css( $s['hover_text'] );
        $radius     = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );

        // Size
        $size_map = [
            'small'  => [ 'py' => 8,  'px' => 16, 'fs' => 13 ],
            'medium' => [ 'py' => 12, 'px' => 24, 'fs' => 15 ],
            'large'  => [ 'py' => 16, 'px' => 32, 'fs' => 17 ],
        ];
        $sz = isset( $size_map[ $s['size'] ] ) ? $size_map[ $s['size'] ] : $size_map['medium'];

        // Style
        $btn_style = in_array( $s['style'], [ 'filled', 'outline', 'text' ], true ) ? $s['style'] : 'filled';

        // Icon SVGs
        $icon_svgs = [
            'cart' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>',
            'bag'  => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>',
            'plus' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>',
        ];
        $icon_svg = isset( $icon_svgs[ $s['icon'] ] ) ? $icon_svgs[ $s['icon'] ] : $icon_svgs['cart'];

        $btn_text = esc_html( $s['button_text'] ?: olo_t( 'Aggiungi al carrello' ) );
        $full_w   = ! empty( $s['full_width'] );
        $product_id = $product->get_id();

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
            }
            .<?php echo $uid; ?> .olo-atc-btn {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                justify-content: center;
                padding: <?php echo $sz['py']; ?>px <?php echo $sz['px']; ?>px;
                font-size: <?php echo $sz['fs']; ?>px;
                font-weight: 600;
                border-radius: <?php echo $radius; ?>;
                cursor: pointer;
                text-decoration: none;
                transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
                <?php if ( $full_w ) : ?>width: 100%;<?php endif; ?>
                <?php if ( $btn_style === 'filled' ) : ?>
                background: <?php echo $bg_color; ?>;
                color: <?php echo $text_color; ?>;
                border: none;
                <?php elseif ( $btn_style === 'outline' ) : ?>
                background: transparent;
                color: <?php echo $bg_color; ?>;
                border: 2px solid <?php echo $bg_color; ?>;
                <?php else : ?>
                background: transparent;
                color: <?php echo $bg_color; ?>;
                border: none;
                padding: <?php echo round( $sz['py'] / 2 ); ?>px 4px;
                <?php endif; ?>
            }
            <?php if ( $radius_hover_css !== '' ) : ?>.<?php echo $uid; ?> .olo-atc-btn{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?> .olo-atc-btn:hover{border-radius:<?php echo $radius_hover_css; ?> !important}<?php endif; ?>
            .<?php echo $uid; ?> .olo-atc-btn:hover {
                <?php if ( $btn_style === 'filled' ) : ?>
                background: <?php echo $hover_bg; ?>;
                color: <?php echo $hover_text; ?>;
                <?php elseif ( $btn_style === 'outline' ) : ?>
                background: <?php echo $bg_color; ?>;
                color: <?php echo $text_color; ?>;
                <?php else : ?>
                opacity: 0.8;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-atc-qty-wrap {
                display: flex;
                align-items: center;
            }
            .<?php echo $uid; ?> .olo-atc-qty-input {
                width: 55px;
                height: <?php echo ( $sz['py'] * 2 + $sz['fs'] ); ?>px;
                text-align: center;
                border: 1px solid var(--olo-color-border, #E5E7EB);
                border-radius: <?php echo $radius; ?>;
                font-size: <?php echo $sz['fs']; ?>px;
                padding: 0 4px;
            }
            .<?php echo $uid; ?> .olo-atc-stepper-btn {
                width: <?php echo ( $sz['py'] * 2 + $sz['fs'] ); ?>px;
                height: <?php echo ( $sz['py'] * 2 + $sz['fs'] ); ?>px;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 1px solid var(--olo-color-border, #E5E7EB);
                background: var(--olo-color-muted, #F3F4F6);
                font-size: <?php echo $sz['fs']; ?>px;
                cursor: pointer;
            }
            .<?php echo $uid; ?> .olo-atc-stepper-val {
                width: 36px;
                text-align: center;
                font-size: <?php echo $sz['fs']; ?>px;
                border-top: 1px solid var(--olo-color-border, #E5E7EB);
                border-bottom: 1px solid var(--olo-color-border, #E5E7EB);
                height: <?php echo ( $sz['py'] * 2 + $sz['fs'] ); ?>px;
                line-height: <?php echo ( $sz['py'] * 2 + $sz['fs'] ); ?>px;
            }
        </style>
        <div class="<?php echo esc_attr( $uid ); ?>">
            <?php if ( ! empty( $s['show_quantity'] ) ) : ?>
            <div class="olo-atc-qty-wrap">
                <?php if ( $s['quantity_style'] === 'stepper' ) : ?>
                <button type="button" class="olo-atc-stepper-btn" data-dir="minus" aria-label="<?php echo esc_attr( olo_t( 'Diminuisci' ) ); ?>">-</button>
                <span class="olo-atc-stepper-val" data-qty-val>1</span>
                <button type="button" class="olo-atc-stepper-btn" data-dir="plus" aria-label="<?php echo esc_attr( olo_t( 'Aumenta' ) ); ?>">+</button>
                <input type="hidden" name="quantity" value="1" class="olo-atc-qty-hidden" />
                <?php else : ?>
                <input type="number" name="quantity" value="1" min="1" max="<?php echo esc_attr( $product->get_max_purchase_quantity() > 0 ? $product->get_max_purchase_quantity() : '' ); ?>" class="olo-atc-qty-input" />
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if ( $product->is_type( 'variable' ) ) : ?>
                <a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="olo-atc-btn">
                    <?php if ( ! empty( $s['show_icon'] ) ) : ?>
                    <?php echo $icon_svg; ?>
                    <?php endif; ?>
                    <span><?php echo esc_html( olo_t( 'Seleziona opzioni' ) ); ?></span>
                </a>
            <?php else : ?>
                <a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
                   class="olo-atc-btn ajax_add_to_cart add_to_cart_button"
                   data-product_id="<?php echo absint( $product_id ); ?>"
                   data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
                   data-quantity="1"
                >
                    <?php if ( ! empty( $s['show_icon'] ) ) : ?>
                    <?php echo $icon_svg; ?>
                    <?php endif; ?>
                    <span><?php echo $btn_text; ?></span>
                </a>
            <?php endif; ?>
        </div>
        <?php
        // Stepper JS — NO && in inline scripts!
        if ( $s['quantity_style'] === 'stepper' ) :
        ?>
        <script>
        (function(){
            var wrap = document.querySelector('.<?php echo $uid; ?>');
            if(!wrap){return}
            var btns = wrap.querySelectorAll('.olo-atc-stepper-btn');
            var valEl = wrap.querySelector('[data-qty-val]');
            var hidden = wrap.querySelector('.olo-atc-qty-hidden');
            var atcBtn = wrap.querySelector('.olo-atc-btn');
            btns.forEach(function(btn){
                btn.addEventListener('click', function(){
                    var dir = btn.getAttribute('data-dir');
                    var cur = parseInt(valEl.textContent) || 1;
                    if(dir === 'plus'){
                        cur++;
                    } else {
                        if(cur > 1){ cur--; }
                    }
                    valEl.textContent = cur;
                    if(hidden){ hidden.value = cur; }
                    if(atcBtn){ atcBtn.setAttribute('data-quantity', cur); }
                });
            });
        })();
        </script>
        <?php
        endif;

        return ob_get_clean();
    }
}
