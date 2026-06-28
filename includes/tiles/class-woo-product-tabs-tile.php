<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Woo_Product_Tabs_Tile extends Olobuild_Tile_Base {

    protected $type     = 'woo_product_tabs';
    protected $name     = 'Tab Prodotto';
    protected $icon     = 'dashicons-index-card';
    protected $category = 'woocommerce';
    protected $defaults = [
        'show_description' => true,
        'show_additional'  => true,
        'show_reviews'     => true,
        'tab_style'        => 'underline',
        'active_color'     => '',
        'text_color'       => '',
        'border_color'     => '',
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

        global $product;
        if ( ! is_a( $product, 'WC_Product' ) ) {
            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- global $product di WooCommerce, non un global definito da olobuild
            $product = wc_get_product( get_the_ID() );
        }
        if ( ! $product ) {
            return '<div style="padding:20px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);font-size:14px;">'
                 . esc_html( olobuild_t( 'Nessun prodotto disponibile in questo contesto' ) )
                 . '</div>';
        }

        $uid = 'olo-woo-tabs-' . wp_rand( 10000, 99999 );

        // Colors — TOKEN-FIRST: tab attivo col primario brand, testo neutro, bordo dal token.
        $active_color = $this->safe_color_css( $s['active_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $text_color   = $this->safe_color_css( $s['text_color'] )   ?: 'var(--olo-color-text, #1f2937)';
        $border_color = $this->safe_color_css( $s['border_color'] ) ?: 'var(--olo-color-border, #e5e7eb)';

        $tab_style = in_array( $s['tab_style'], [ 'underline', 'pills', 'boxed' ], true ) ? $s['tab_style'] : 'underline';

        // Get WooCommerce product tabs
        setup_postdata( $product->get_id() );
        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- hook di terze parti (WooCommerce / WordPress core / OLOlang), non un hook di olobuild
        $tabs = apply_filters( 'woocommerce_product_tabs', [] );

        // Filter tabs based on settings
        if ( empty( $s['show_description'] ) ) {
            unset( $tabs['description'] );
        }
        if ( empty( $s['show_additional'] ) ) {
            unset( $tabs['additional_information'] );
        }
        if ( empty( $s['show_reviews'] ) ) {
            unset( $tabs['reviews'] );
        }

        if ( empty( $tabs ) ) {
            return '<div style="padding:20px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);font-size:14px;">'
                 . esc_html( olobuild_t( 'Nessuna tab disponibile per questo prodotto' ) )
                 . '</div>';
        }

        ob_start();
        ?>
<?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from safe_color_css()-validated colors with fixed var() fallbacks, gated by an in_array() whitelist on $tab_style; $uid is internally generated. Column 0 + closing tag so this line emits zero bytes. ?>
        <style>
            .<?php echo $uid; ?> .olo-tabs-nav {
                display: flex;
                gap: 0;
                margin-bottom: 24px;
                <?php if ( $tab_style === 'underline' ) : ?>
                border-bottom: 2px solid <?php echo $border_color; ?>;
                <?php endif; ?>
                <?php if ( $tab_style === 'boxed' ) : ?>
                border: 1px solid <?php echo $border_color; ?>;
                border-radius: 8px;
                overflow: hidden;
                <?php endif; ?>
                <?php if ( $tab_style === 'pills' ) : ?>
                gap: 8px;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-tab-btn {
                padding: 12px 20px;
                background: none;
                border: none;
                cursor: pointer;
                font-size: 14px;
                font-weight: 600;
                color: <?php echo $text_color; ?>;
                transition: color 0.2s ease, background 0.2s ease, border-color 0.2s ease;
                position: relative;
                white-space: nowrap;
                <?php if ( $tab_style === 'underline' ) : ?>
                margin-bottom: -2px;
                border-bottom: 2px solid transparent;
                <?php endif; ?>
                <?php if ( $tab_style === 'pills' ) : ?>
                border-radius: 6px;
                <?php endif; ?>
                <?php if ( $tab_style === 'boxed' ) : ?>
                flex: 1;
                text-align: center;
                border-right: 1px solid <?php echo $border_color; ?>;
                <?php endif; ?>
            }
            <?php if ( $tab_style === 'boxed' ) : ?>
            .<?php echo $uid; ?> .olo-tab-btn:last-child {
                border-right: none;
            }
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-tab-btn.active {
                color: <?php echo $active_color; ?>;
                <?php if ( $tab_style === 'underline' ) : ?>
                border-bottom-color: <?php echo $active_color; ?>;
                <?php endif; ?>
                <?php if ( $tab_style === 'pills' ) : ?>
                background: <?php echo $active_color; ?>;
                color: var(--olo-color-primary-contrast, #FFFFFF);
                <?php endif; ?>
                <?php if ( $tab_style === 'boxed' ) : ?>
                background: <?php echo $active_color; ?>;
                color: var(--olo-color-primary-contrast, #FFFFFF);
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-tab-btn:hover {
                <?php if ( $tab_style === 'underline' ) : ?>
                color: <?php echo $active_color; ?>;
                <?php endif; ?>
                <?php if ( $tab_style === 'pills' ) : ?>
                background: color-mix(in srgb, <?php echo $active_color; ?> 13%, transparent);
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-tab-btn.active:hover {
                <?php if ( $tab_style === 'pills' ) : ?>
                background: <?php echo $active_color; ?>;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-tab-panel {
                display: none;
                color: <?php echo $text_color; ?>;
                font-size: 14px;
                line-height: 1.7;
            }
            .<?php echo $uid; ?> .olo-tab-panel.active {
                display: block;
            }
            .<?php echo $uid; ?> .olo-tab-panel h2 {
                font-size: 18px;
                font-weight: 700;
                margin: 0 0 16px;
                color: <?php echo $text_color; ?>;
            }
            .<?php echo $uid; ?> .olo-tab-panel table {
                width: 100%;
                border-collapse: collapse;
            }
            .<?php echo $uid; ?> .olo-tab-panel table th,
            .<?php echo $uid; ?> .olo-tab-panel table td {
                padding: 10px 12px;
                border-bottom: 1px solid <?php echo $border_color; ?>;
                text-align: left;
                font-size: 14px;
            }
            .<?php echo $uid; ?> .olo-tab-panel table th {
                font-weight: 600;
                width: 30%;
            }
        </style>
<?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped -- column 0 + closing tag so this line emits zero bytes ?>
        <div class="<?php echo esc_attr( $uid ); ?>">
            <div class="olo-tabs-nav" role="tablist">
            <?php
            $first = true;
            foreach ( $tabs as $key => $tab ) :
                $active_cls = $first ? ' active' : '';
            ?>
                <button class="olo-tab-btn<?php echo esc_attr( $active_cls ); ?>"
                        role="tab"
                        data-tab="<?php echo esc_attr( $key ); ?>"
                        onclick="(function(el){var w=el.closest('.<?php echo esc_attr( $uid ); ?>');w.querySelectorAll('.olo-tab-btn').forEach(function(b){b.classList.remove('active')});w.querySelectorAll('.olo-tab-panel').forEach(function(p){p.classList.remove('active')});el.classList.add('active');var t=w.querySelector('[data-panel=&quot;'+el.getAttribute('data-tab')+'&quot;]');if(t){t.classList.add('active')}})(this)">
                    <?php echo esc_html( $tab['title'] ); ?>
                </button>
            <?php
                $first = false;
            endforeach;
            ?>
            </div>
            <?php
            $first = true;
            foreach ( $tabs as $key => $tab ) :
                $active_cls = $first ? ' active' : '';
            ?>
            <div class="olo-tab-panel<?php echo esc_attr( $active_cls ); ?>"
                 data-panel="<?php echo esc_attr( $key ); ?>"
                 role="tabpanel">
                <?php
                if ( isset( $tab['callback'] ) ) {
                    call_user_func( $tab['callback'], $key, $tab );
                }
                ?>
            </div>
            <?php
                $first = false;
            endforeach;
            ?>
        </div>
        <?php
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
}
