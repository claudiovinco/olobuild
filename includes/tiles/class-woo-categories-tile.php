<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Woo_Categories_Tile extends Olo_Tile_Base {

    protected $type     = 'woo_categories';
    protected $name     = 'Categorie Prodotti';
    protected $icon     = 'dashicons-category';
    protected $category = 'woocommerce';
    protected $defaults = [
        'columns'          => 4,
        'show_image'       => true,
        'show_count'       => true,
        'show_description' => false,
        'hide_empty'       => true,
        'orderby'          => 'name',
        'parent_only'      => false,
        'overlay'          => true,
        'overlay_color'    => 'rgba(0,0,0,0.4)',
        'text_color'       => '#FFFFFF',
        'title_tag'        => 'h3',
        'gap'              => 24,
        'image_ratio'      => '1-1',
        'border_radius'    => 8,
        'hover_effect'     => 'zoom',
        'columns_tablet'   => 2,
        'columns_mobile'   => 1,
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
                 . esc_html( olo_t( 'WooCommerce non attivo.' ) )
                 . '</div>';
        }

        $s = wp_parse_args( $settings, $this->defaults );

        $args = [
            'taxonomy'   => 'product_cat',
            'orderby'    => sanitize_text_field( $s['orderby'] ),
            'order'      => 'ASC',
            'hide_empty' => ! empty( $s['hide_empty'] ),
        ];
        if ( ! empty( $s['parent_only'] ) ) {
            $args['parent'] = 0;
        }

        $categories = get_terms( $args );
        if ( is_wp_error( $categories ) || empty( $categories ) ) {
            return '<div style="padding:40px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF)">'
                 . esc_html( olo_t( 'Nessuna categoria trovata' ) )
                 . '</div>';
        }

        $cols        = max( 1, min( 6, absint( $s['columns'] ) ) );
        $cols_tablet = max( 1, min( 4, absint( $s['columns_tablet'] ) ) );
        $cols_mobile = max( 1, min( 2, absint( $s['columns_mobile'] ) ) );
        $gap         = absint( $s['gap'] );
        $radius      = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );
        $uid         = 'olo-woo-cats-' . wp_rand( 10000, 99999 );
        $tag         = in_array( $s['title_tag'], [ 'h2', 'h3', 'h4', 'h5' ], true ) ? $s['title_tag'] : 'h3';
        $txt_color   = $this->safe_color_css( $s['text_color'] );
        $ov_color    = $s['overlay'] ? esc_attr( $s['overlay_color'] ) : 'transparent';

        $ratio_map = [ '1-1' => '100%', '4-3' => '75%', '3-4' => '133.33%', '16-9' => '56.25%' ];
        $ratio_val = isset( $ratio_map[ $s['image_ratio'] ] ) ? $ratio_map[ $s['image_ratio'] ] : '100%';

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?>{display:grid;grid-template-columns:repeat(<?php echo $cols; ?>,1fr);gap:<?php echo $gap; ?>px}
            .<?php echo $uid; ?> .olo-wcat{position:relative;overflow:hidden;border-radius:<?php echo $radius; ?>;transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}
            <?php if ( $radius_hover_css !== '' ) : ?>.<?php echo $uid; ?> .olo-wcat:hover{border-radius:<?php echo $radius_hover_css; ?> !important}<?php endif; ?>
            .<?php echo $uid; ?> .olo-wcat-img{position:relative;padding-top:<?php echo $ratio_val; ?>;background:var(--olo-color-muted, #F3F4F6);overflow:hidden}
            .<?php echo $uid; ?> .olo-wcat-img img{position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;transition:transform .4s ease}
            <?php if ( $s['hover_effect'] === 'zoom' ) : ?>
            .<?php echo $uid; ?> .olo-wcat:hover .olo-wcat-img img{transform:scale(1.06)}
            <?php elseif ( $s['hover_effect'] === 'darken' ) : ?>
            .<?php echo $uid; ?> .olo-wcat:hover .olo-wcat-ov{background:rgba(0,0,0,0.6)!important}
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-wcat-ov{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;background:<?php echo $ov_color; ?>;transition:background .3s ease;text-align:center;padding:16px}
            .<?php echo $uid; ?> .olo-wcat-ov <?php echo $tag; ?>{margin:0;color:<?php echo $txt_color; ?>;font-size:18px;font-weight:700}
            .<?php echo $uid; ?> .olo-wcat-ov .olo-wcat-count{color:<?php echo $txt_color; ?>;opacity:.8;font-size:13px;margin-top:4px}
            .<?php echo $uid; ?> .olo-wcat-ov .olo-wcat-desc{color:<?php echo $txt_color; ?>;opacity:.7;font-size:12px;margin-top:6px}
            .<?php echo $uid; ?> a{text-decoration:none}
            @media(max-width:960px){.<?php echo $uid; ?>{grid-template-columns:repeat(<?php echo $cols_tablet; ?>,1fr)}}
            @media(max-width:640px){.<?php echo $uid; ?>{grid-template-columns:repeat(<?php echo $cols_mobile; ?>,1fr)}}
        </style>
        <div class="<?php echo esc_attr( $uid ); ?>">
        <?php foreach ( $categories as $cat ) :
            $thumb_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
            $link     = get_term_link( $cat );
        ?>
            <a href="<?php echo esc_url( $link ); ?>" class="olo-wcat">
                <div class="olo-wcat-img">
                    <?php if ( $thumb_id ) : ?>
                        <?php echo wp_get_attachment_image( $thumb_id, 'medium_large' ); ?>
                    <?php endif; ?>
                    <div class="olo-wcat-ov">
                        <<?php echo $tag; ?>><?php echo esc_html( $cat->name ); ?></<?php echo $tag; ?>>
                        <?php if ( ! empty( $s['show_count'] ) ) : ?>
                        <div class="olo-wcat-count"><?php echo absint( $cat->count ); ?> <?php echo esc_html( olo_t( 'prodotti' ) ); ?></div>
                        <?php endif; ?>
                        <?php if ( ! empty( $s['show_description'] ) && $cat->description ) : ?>
                        <div class="olo-wcat-desc"><?php echo esc_html( wp_trim_words( $cat->description, 12 ) ); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
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
