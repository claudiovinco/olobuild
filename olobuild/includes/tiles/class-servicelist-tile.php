<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_ServiceList_Tile extends Olo_Tile_Base {

    protected $type     = 'servicelist';
    protected $name     = 'Lista Servizi';
    protected $icon     = 'dashicons-grid-view';
    protected $category = 'booking';
    protected $defaults = [
        'columns'           => 3,
        'gap'               => 24,
        'style'             => 'cards',
        'show_image'        => true,
        'show_price'        => true,
        'show_duration'     => true,
        'show_excerpt'      => true,
        'show_booking_btn'  => true,
        'booking_btn_text'  => 'Prenota',
        'link_to'           => 'service_page',
        'booking_page_url'  => '/prenota',
        'card_bg'           => '#FFFFFF',
        'card_border_radius'=> 12,
        'card_border_color' => '#E5E7EB',
        'card_shadow'       => 'sm',
        'card_padding'      => 24,
        'card_hover_shadow' => 'md',
        'title_size'        => 18,
        'title_weight'      => '600',
        'title_color'       => '',
        'price_color'       => '#6366F1',
        'price_size'        => 16,
        'duration_color'    => '#6B7280',
        'excerpt_color'     => '#6B7280',
        'excerpt_size'      => 14,
        'btn_bg'            => '#6366F1',
        'btn_color'         => '#FFFFFF',
        'btn_radius'        => 8,
        'btn_full_width'    => false,
        'image_height'      => 200,
        'image_radius'      => 8,
        'color_bar'         => true,
        'color_bar_height'  => 4,
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        // Check if olo_service CPT exists
        if ( ! post_type_exists( 'olo_service' ) ) {
            return '<div style="padding:32px;text-align:center;color:#9ca3af;background:#f9fafb;border-radius:8px">'
                 . '<p style="font-size:1.1em;margin:0">Installa e attiva il plugin <strong>Olo Booking</strong> per visualizzare la lista servizi.</p>'
                 . '</div>';
        }

        $services = get_posts( [
            'post_type'      => 'olo_service',
            'post_status'    => 'publish',
            'posts_per_page' => 50,
            'orderby'        => 'menu_order title',
            'order'          => 'ASC',
        ] );

        if ( empty( $services ) ) {
            return '<div style="padding:24px;text-align:center;color:#9ca3af">'
                 . '<p style="margin:0">Nessun servizio disponibile.</p>'
                 . '</div>';
        }

        $uid = 'olo-sl-' . wp_rand( 10000, 99999 );

        $shadow_map = [
            'none' => 'none',
            'sm'   => '0 1px 3px rgba(0,0,0,0.1)',
            'md'   => '0 4px 12px rgba(0,0,0,0.1)',
            'lg'   => '0 8px 24px rgba(0,0,0,0.15)',
        ];

        $cols   = absint( $s['columns'] ) ?: 3;
        $gap    = absint( $s['gap'] ) ?: 24;
        $is_list = ( $s['style'] === 'list' );
        $shadow = $shadow_map[ $s['card_shadow'] ] ?? 'none';
        $h_shadow = $shadow_map[ $s['card_hover_shadow'] ] ?? 'none';
        $radius = absint( $s['card_border_radius'] );
        $padding = absint( $s['card_padding'] );
        $bg     = $this->safe_color( $s['card_bg'] );
        $border = $this->safe_color( $s['card_border_color'] );
        $img_h  = absint( $s['image_height'] ) ?: 200;
        $img_r  = absint( $s['image_radius'] );
        $bar_h  = absint( $s['color_bar_height'] ) ?: 4;

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?>{display:<?php echo $is_list ? 'flex;flex-direction:column' : 'grid;grid-template-columns:repeat(' . $cols . ',1fr)'; ?>;gap:<?php echo $gap; ?>px}
            .<?php echo $uid; ?> .olo-sl-card{background:<?php echo $bg ?: '#fff'; ?>;border-radius:<?php echo $radius; ?>px;border:1px solid <?php echo $border ?: '#e5e7eb'; ?>;box-shadow:<?php echo $shadow; ?>;overflow:hidden;transition:box-shadow 0.25s ease,transform 0.25s ease}
            .<?php echo $uid; ?> .olo-sl-card:hover{box-shadow:<?php echo $h_shadow; ?>;transform:translateY(-2px)}
            .<?php echo $uid; ?> .olo-sl-bar{height:<?php echo $bar_h; ?>px}
            .<?php echo $uid; ?> .olo-sl-img{height:<?php echo $img_h; ?>px;position:relative;overflow:hidden;background:#f3f4f6}
            .<?php echo $uid; ?> .olo-sl-img img{width:100%;height:100%;object-fit:cover}
            .<?php echo $uid; ?> .olo-sl-body{padding:<?php echo $padding; ?>px}
            .<?php echo $uid; ?> .olo-sl-title{font-size:<?php echo absint( $s['title_size'] ) ?: 18; ?>px;font-weight:<?php echo esc_attr( $s['title_weight'] ?: '600' ); ?>;color:<?php echo $this->safe_color( $s['title_color'] ) ?: '#1f2937'; ?>;margin:0 0 6px}
            .<?php echo $uid; ?> .olo-sl-meta{display:flex;gap:10px;margin-bottom:8px;font-size:13px}
            .<?php echo $uid; ?> .olo-sl-duration{color:<?php echo $this->safe_color( $s['duration_color'] ) ?: '#6b7280'; ?>}
            .<?php echo $uid; ?> .olo-sl-price{font-size:<?php echo absint( $s['price_size'] ) ?: 16; ?>px;font-weight:600;color:<?php echo $this->safe_color( $s['price_color'] ) ?: '#6366f1'; ?>}
            .<?php echo $uid; ?> .olo-sl-excerpt{font-size:<?php echo absint( $s['excerpt_size'] ) ?: 14; ?>px;color:<?php echo $this->safe_color( $s['excerpt_color'] ) ?: '#6b7280'; ?>;line-height:1.5;margin:0 0 14px}
            .<?php echo $uid; ?> .olo-sl-btn{display:<?php echo ! empty( $s['btn_full_width'] ) ? 'block;width:100%' : 'inline-block'; ?>;padding:10px 20px;background:<?php echo $this->safe_color( $s['btn_bg'] ) ?: '#6366f1'; ?>;color:<?php echo $this->safe_color( $s['btn_color'] ) ?: '#fff'; ?>;border-radius:<?php echo absint( $s['btn_radius'] ); ?>px;text-align:center;font-weight:600;font-size:14px;text-decoration:none;transition:opacity 0.2s}
            .<?php echo $uid; ?> .olo-sl-btn:hover{opacity:0.85}
            @media(max-width:767px){.<?php echo $uid; ?>{grid-template-columns:1fr}}
        </style>

        <div class="<?php echo esc_attr( $uid ); ?>">
            <?php foreach ( $services as $svc ) :
                $svc_id   = $svc->ID;
                $duration = get_post_meta( $svc_id, '_olo_service_duration', true );
                $price    = get_post_meta( $svc_id, '_olo_service_price', true );
                $color    = get_post_meta( $svc_id, '_olo_service_color', true ) ?: '#6366F1';
                $thumb    = get_the_post_thumbnail_url( $svc_id, 'medium_large' );
                $excerpt  = get_the_excerpt( $svc );

                // Link
                $link = '';
                if ( $s['link_to'] === 'service_page' ) {
                    $link = get_permalink( $svc_id );
                } elseif ( $s['link_to'] === 'booking_page' ) {
                    $base = $s['booking_page_url'] ?: '/prenota';
                    $link = $base . ( strpos( $base, '?' ) !== false ? '&' : '?' ) . 'service=' . $svc_id;
                }
            ?>
            <div class="olo-sl-card">
                <?php if ( ! empty( $s['color_bar'] ) ) : ?>
                    <div class="olo-sl-bar" style="background:<?php echo esc_attr( $color ); ?>"></div>
                <?php endif; ?>

                <?php if ( ! empty( $s['show_image'] ) ) : ?>
                    <div class="olo-sl-img"<?php if ( ! $s['color_bar'] && $img_r ) : ?> style="border-radius:<?php echo $img_r; ?>px <?php echo $img_r; ?>px 0 0"<?php endif; ?>>
                        <?php if ( $thumb ) : ?>
                            <img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $svc->post_title ); ?>" loading="lazy" />
                        <?php else : ?>
                            <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:<?php echo esc_attr( $color ); ?>15;color:#9ca3af;font-size:28px">&#128247;</div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="olo-sl-body">
                    <h3 class="olo-sl-title"><?php echo esc_html( $svc->post_title ); ?></h3>

                    <div class="olo-sl-meta">
                        <?php if ( ! empty( $s['show_duration'] ) && $duration ) : ?>
                            <span class="olo-sl-duration">&#9201; <?php echo absint( $duration ); ?> min</span>
                        <?php endif; ?>
                        <?php if ( ! empty( $s['show_price'] ) && $price ) : ?>
                            <span class="olo-sl-price">&euro; <?php echo esc_html( number_format( (float) $price, 2, ',', '.' ) ); ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if ( ! empty( $s['show_excerpt'] ) && $excerpt ) : ?>
                        <p class="olo-sl-excerpt"><?php echo esc_html( wp_trim_words( $excerpt, 20 ) ); ?></p>
                    <?php endif; ?>

                    <?php if ( ! empty( $s['show_booking_btn'] ) ) : ?>
                        <?php if ( $link ) : ?>
                            <a href="<?php echo esc_url( $link ); ?>" class="olo-sl-btn"><?php echo esc_html( $s['booking_btn_text'] ?: 'Prenota' ); ?></a>
                        <?php else : ?>
                            <span class="olo-sl-btn"><?php echo esc_html( $s['booking_btn_text'] ?: 'Prenota' ); ?></span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
