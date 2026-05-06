<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Woo_Product_Image_Tile extends Olo_Tile_Base {

    protected $type     = 'woo_product_image';
    protected $name     = 'Immagine Prodotto';
    protected $icon     = 'dashicons-format-image';
    protected $category = 'woocommerce';
    protected $defaults = [
        'show_gallery'     => true,
        'gallery_position' => 'bottom',
        'lightbox'         => false,
        'zoom_on_hover'    => true,
        'image_ratio'      => '1-1',
        'border_radius'    => 8,
        'thumb_size'       => 64,
        'thumb_gap'        => 8,
        'thumb_border_radius' => 4,
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

        $uid = 'olo-woo-pimg-' . wp_rand( 10000, 99999 );

        // Image ratio
        $ratio_map = [
            '1-1'  => '100%',
            '4-3'  => '75%',
            '3-4'  => '133.33%',
            '3-2'  => '66.66%',
            '16-9' => '56.25%',
            'auto' => '0',
        ];
        $ratio     = $s['image_ratio'];
        $ratio_val = isset( $ratio_map[ $ratio ] ) ? $ratio_map[ $ratio ] : '100%';
        $auto_h    = ( $ratio === 'auto' );

        // Settings
        $border_radius       = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $border_radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );
        $thumb_size          = max( 40, min( 120, absint( $s['thumb_size'] ) ) );
        $thumb_gap           = max( 4, min( 16, absint( $s['thumb_gap'] ) ) );
        $thumb_border_radius = Olo_Tile_Utils::border_radius( $s['thumb_border_radius'] ?? 0 );
        $thumb_border_radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['thumb_border_radius_hover'] ?? null );

        $gallery_pos = in_array( $s['gallery_position'], [ 'bottom', 'left' ], true ) ? $s['gallery_position'] : 'bottom';

        // Main image
        $thumb_id   = get_post_thumbnail_id( $product->get_id() );
        $gallery_ids = $product->get_gallery_image_ids();

        // All images (main + gallery)
        $all_images = [];
        if ( $thumb_id ) {
            $all_images[] = $thumb_id;
        }
        if ( ! empty( $s['show_gallery'] ) ) {
            foreach ( $gallery_ids as $gid ) {
                $all_images[] = $gid;
            }
        }

        if ( empty( $all_images ) ) {
            return '<div style="padding:60px 20px;text-align:center;background:var(--olo-color-muted, #F3F4F6);border-radius:' . $border_radius . ';color:var(--olo-color-text-muted, #9CA3AF);">'
                 . '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 8px;display:block;"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>'
                 . esc_html( olo_t( 'Nessuna immagine prodotto' ) )
                 . '</div>';
        }

        $main_img_id = $all_images[0];
        $main_src    = wp_get_attachment_image_url( $main_img_id, 'woocommerce_single' );
        $main_alt    = get_post_meta( $main_img_id, '_wp_attachment_image_alt', true );
        if ( ! $main_alt ) {
            $main_alt = $product->get_name();
        }

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                display: flex;
                <?php if ( $gallery_pos === 'left' ) : ?>
                flex-direction: row;
                gap: <?php echo $thumb_gap; ?>px;
                <?php else : ?>
                flex-direction: column;
                gap: <?php echo $thumb_gap; ?>px;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-woo-pimg-main {
                position: relative;
                overflow: hidden;
                border-radius: <?php echo $border_radius; ?>;
                <?php if ( $gallery_pos === 'left' ) : ?>
                flex: 1;
                <?php endif; ?>
                <?php if ( ! $auto_h ) : ?>
                padding-top: <?php echo $ratio_val; ?>;
                <?php endif; ?>
                background: var(--olo-color-muted, #F3F4F6);
            }
            <?php if ( $border_radius_hover_css !== '' ) : ?>.<?php echo $uid; ?> .olo-woo-pimg-main{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?> .olo-woo-pimg-main:hover{border-radius:<?php echo $border_radius_hover_css; ?> !important}<?php endif; ?>
            .<?php echo $uid; ?> .olo-woo-pimg-main img {
                <?php if ( ! $auto_h ) : ?>
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
                <?php else : ?>
                width: 100%;
                height: auto;
                display: block;
                <?php endif; ?>
                transition: transform 0.4s ease;
            }
            <?php if ( ! empty( $s['zoom_on_hover'] ) ) : ?>
            .<?php echo $uid; ?> .olo-woo-pimg-main:hover img {
                transform: scale(1.06);
            }
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-woo-pimg-thumbs {
                display: flex;
                <?php if ( $gallery_pos === 'left' ) : ?>
                flex-direction: column;
                width: <?php echo $thumb_size; ?>px;
                order: -1;
                <?php else : ?>
                flex-direction: row;
                <?php endif; ?>
                gap: <?php echo $thumb_gap; ?>px;
                flex-wrap: wrap;
            }
            .<?php echo $uid; ?> .olo-woo-pimg-thumb {
                width: <?php echo $thumb_size; ?>px;
                height: <?php echo $thumb_size; ?>px;
                border-radius: <?php echo $thumb_border_radius; ?>;
                overflow: hidden;
                cursor: pointer;
                border: 2px solid transparent;
                transition: border-color 0.2s ease;
            }
            <?php if ( $thumb_border_radius_hover_css !== '' ) : ?>.<?php echo $uid; ?> .olo-woo-pimg-thumb{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?> .olo-woo-pimg-thumb:hover{border-radius:<?php echo $thumb_border_radius_hover_css; ?> !important}<?php endif; ?>
            .<?php echo $uid; ?> .olo-woo-pimg-thumb.active,
            .<?php echo $uid; ?> .olo-woo-pimg-thumb:hover {
                border-color: var(--olo-color-primary, #6366F1);
            }
            .<?php echo $uid; ?> .olo-woo-pimg-thumb img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }
        </style>
        <div class="<?php echo esc_attr( $uid ); ?>">
            <div class="olo-woo-pimg-main" id="<?php echo esc_attr( $uid ); ?>-main">
                <?php if ( ! empty( $s['lightbox'] ) ) : ?>
                <a href="<?php echo esc_url( wp_get_attachment_image_url( $main_img_id, 'full' ) ); ?>" data-lightbox="<?php echo esc_attr( $uid ); ?>">
                    <img src="<?php echo esc_url( $main_src ); ?>" alt="<?php echo esc_attr( $main_alt ); ?>" />
                </a>
                <?php else : ?>
                <img src="<?php echo esc_url( $main_src ); ?>" alt="<?php echo esc_attr( $main_alt ); ?>" />
                <?php endif; ?>
            </div>
            <?php if ( ! empty( $s['show_gallery'] ) ) : ?>
                <?php if ( count( $all_images ) > 1 ) : ?>
                <div class="olo-woo-pimg-thumbs">
                    <?php foreach ( $all_images as $idx => $img_id ) :
                        $tsrc = wp_get_attachment_image_url( $img_id, 'thumbnail' );
                        $fsrc = wp_get_attachment_image_url( $img_id, 'woocommerce_single' );
                        $talt = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
                    ?>
                    <div class="olo-woo-pimg-thumb<?php echo $idx === 0 ? ' active' : ''; ?>"
                         onclick="var m=document.getElementById('<?php echo esc_js( $uid ); ?>-main');if(m){var i=m.querySelector('img');if(i){i.src='<?php echo esc_js( $fsrc ); ?>';}};this.parentNode.querySelectorAll('.olo-woo-pimg-thumb').forEach(function(t){t.classList.remove('active');});this.classList.add('active');">
                        <img src="<?php echo esc_url( $tsrc ); ?>" alt="<?php echo esc_attr( $talt ); ?>" />
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
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
