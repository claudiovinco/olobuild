<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Woo_Upsells_Tile extends Olo_Tile_Base {

    protected $type     = 'woo_upsells';
    protected $name     = 'Prodotti Suggeriti';
    protected $icon     = 'dashicons-star-filled';
    protected $category = 'woocommerce';
    protected $defaults = [
        'columns'         => 4,
        'show_image'      => true,
        'show_title'      => true,
        'show_price'      => true,
        'card_style'      => 'shadow',
        'gap'             => 24,
        'title_text'      => 'Ti potrebbe interessare',
        'show_heading'    => true,
        'columns_tablet'  => 2,
        'columns_mobile'  => 1,
        'title_color'     => '',
        'price_color'     => '',
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

        $s = wp_parse_args( $settings, $this->defaults );

        global $product;
        if ( ! is_a( $product, 'WC_Product' ) ) {
            $product = wc_get_product( get_the_ID() );
        }
        if ( ! $product ) {
            return '<div style="padding:20px;text-align:center;color:var(--olo-color-text-muted, #6B7280);font-size:14px;">'
                 . esc_html( olo_t( 'Nessun prodotto disponibile in questo contesto' ) )
                 . '</div>';
        }

        $upsell_ids = $product->get_upsell_ids();
        if ( empty( $upsell_ids ) ) {
            return '<div style="padding:20px;text-align:center;color:var(--olo-color-text-muted, #6B7280);font-size:14px;">'
                 . esc_html( olo_t( 'Nessun prodotto suggerito configurato' ) )
                 . '</div>';
        }

        $uid          = 'olo-woo-ups-' . wp_rand( 10000, 99999 );
        $cols         = max( 1, min( 6, absint( $s['columns'] ) ) );
        $cols_tablet  = max( 1, min( 4, absint( $s['columns_tablet'] ) ) );
        $cols_mobile  = max( 1, min( 2, absint( $s['columns_mobile'] ) ) );
        $gap          = absint( $s['gap'] );

        // Colors
        $title_color = $this->safe_color_css( $s['title_color'] );
        $price_color = $this->safe_color_css( $s['price_color'] );

        // Card style
        $card_style = $s['card_style'];
        $card_extra = '';
        if ( $card_style === 'shadow' ) {
            $card_extra = 'box-shadow:0 1px 3px rgba(0,0,0,0.1),0 1px 2px rgba(0,0,0,0.06);';
        } elseif ( $card_style === 'border' ) {
            $card_extra = 'border:1px solid var(--olo-color-border, #E5E7EB);';
        }

        $upsells = new WP_Query( [
            'post_type'      => 'product',
            'post__in'       => $upsell_ids,
            'posts_per_page' => count( $upsell_ids ),
            'post_status'    => 'publish',
            'orderby'        => 'post__in',
        ] );

        if ( ! $upsells->have_posts() ) {
            return '<div style="padding:20px;text-align:center;color:var(--olo-color-text-muted, #6B7280);font-size:14px;">'
                 . esc_html( olo_t( 'Nessun prodotto suggerito trovato' ) )
                 . '</div>';
        }

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> .olo-ups-heading {
                font-size: 22px;
                font-weight: 700;
                color: <?php echo $title_color; ?>;
                margin: 0 0 20px;
            }
            .<?php echo $uid; ?> .olo-ups-grid {
                display: grid;
                grid-template-columns: repeat(<?php echo $cols; ?>, 1fr);
                gap: <?php echo $gap; ?>px;
            }
            .<?php echo $uid; ?> .olo-ups-card {
                background: var(--olo-color-background, #fff);
                border-radius: 8px;
                overflow: hidden;
                <?php echo $card_extra; ?>
                transition: box-shadow 0.3s ease;
            }
            .<?php echo $uid; ?> .olo-ups-card:hover {
                box-shadow: 0 6px 20px rgba(0,0,0,0.12);
            }
            .<?php echo $uid; ?> .olo-ups-img {
                position: relative;
                overflow: hidden;
                padding-top: 100%;
            }
            .<?php echo $uid; ?> .olo-ups-img img {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.4s ease;
            }
            .<?php echo $uid; ?> .olo-ups-card:hover .olo-ups-img img {
                transform: scale(1.05);
            }
            .<?php echo $uid; ?> .olo-ups-img a.no-thumb {
                position: absolute;
                inset: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                background: var(--olo-color-muted, #F3F4F6);
            }
            .<?php echo $uid; ?> .olo-ups-body {
                padding: 12px 14px;
            }
            .<?php echo $uid; ?> .olo-ups-title {
                margin: 0 0 6px;
                font-size: 14px;
                font-weight: 600;
                line-height: 1.3;
            }
            .<?php echo $uid; ?> .olo-ups-title a {
                color: <?php echo $title_color; ?>;
                text-decoration: none;
            }
            .<?php echo $uid; ?> .olo-ups-title a:hover {
                text-decoration: underline;
            }
            .<?php echo $uid; ?> .olo-ups-price {
                font-size: 15px;
                font-weight: 700;
                color: <?php echo $price_color; ?>;
            }
            .<?php echo $uid; ?> .olo-ups-price del {
                color: var(--olo-color-text-muted, #9CA3AF);
                font-weight: 400;
                font-size: 13px;
                margin-right: 6px;
            }
            .<?php echo $uid; ?> .olo-ups-price ins {
                text-decoration: none;
                color: <?php echo $price_color; ?>;
            }
            @media (max-width: 960px) {
                .<?php echo $uid; ?> .olo-ups-grid {
                    grid-template-columns: repeat(<?php echo $cols_tablet; ?>, 1fr);
                }
            }
            @media (max-width: 640px) {
                .<?php echo $uid; ?> .olo-ups-grid {
                    grid-template-columns: repeat(<?php echo $cols_mobile; ?>, 1fr);
                }
            }
        </style>
        <div class="<?php echo esc_attr( $uid ); ?>">
            <?php if ( ! empty( $s['show_heading'] ) ) : ?>
            <h2 class="olo-ups-heading"><?php echo esc_html( olo_t( $s['title_text'] ) ); ?></h2>
            <?php endif; ?>
            <div class="olo-ups-grid">
            <?php
            while ( $upsells->have_posts() ) :
                $upsells->the_post();
                $ups_product = wc_get_product( get_the_ID() );
                if ( ! $ups_product ) {
                    continue;
                }
                $permalink  = get_permalink();
                $title      = get_the_title();
                $price_html = $ups_product->get_price_html();
                $thumb_id   = get_post_thumbnail_id();
            ?>
                <div class="olo-ups-card">
                    <?php if ( ! empty( $s['show_image'] ) ) : ?>
                    <div class="olo-ups-img">
                        <?php if ( $thumb_id ) : ?>
                        <a href="<?php echo esc_url( $permalink ); ?>">
                            <?php echo wp_get_attachment_image( $thumb_id, 'woocommerce_thumbnail' ); ?>
                        </a>
                        <?php else : ?>
                        <a href="<?php echo esc_url( $permalink ); ?>" class="no-thumb">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--olo-color-border, #D1D5DB)" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <div class="olo-ups-body">
                        <?php if ( ! empty( $s['show_title'] ) ) : ?>
                        <div class="olo-ups-title">
                            <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
                        </div>
                        <?php endif; ?>
                        <?php if ( ! empty( $s['show_price'] ) ) : ?>
                        <div class="olo-ups-price"><?php echo $price_html; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
            </div>
        </div>
        <?php
        wp_reset_postdata();
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
