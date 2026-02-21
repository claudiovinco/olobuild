<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_ServiceInfo_Tile extends Olo_Tile_Base {

    protected $type     = 'serviceinfo';
    protected $name     = 'Info Servizio';
    protected $icon     = 'dashicons-info-outline';
    protected $category = 'booking';
    protected $defaults = [
        'acc_hero_height'        => 400,
        'acc_max_width'          => 900,
        'acc_card_radius'        => 16,
        'acc_card_shadow'        => 'lg',
        'acc_body_padding'       => 32,
        'acc_title_size'         => 30,
        'acc_show_description'   => true,
        'acc_show_amenities'     => true,
        'acc_show_checkin'       => true,
        'acc_show_booking_btn'   => false,
        'acc_amenity_cols_desktop' => 4,
        'acc_show_gallery'       => true,
        'acc_gallery_columns'    => 4,
        'acc_gallery_height'     => 180,
        'acc_accent_color'       => '',
        'acc_section_order'      => 'gallery,stats,prices,description,amenities,checkin',
    ];

    private $amenity_labels = [
        'wifi'       => 'Wi-Fi',
        'heating'    => 'Riscaldamento',
        'aircon'     => 'Aria condizionata',
        'fireplace'  => 'Camino / Stufa',
        'tv'         => 'TV',
        'pets'       => 'Animali ammessi',
        'smoking'    => 'Fumatori',
        'elevator'   => 'Ascensore',
        'accessible' => 'Accesso disabili',
        'kitchen'    => 'Cucina attrezzata',
        'oven'       => 'Forno',
        'microwave'  => 'Microonde',
        'dishwasher' => 'Lavastoviglie',
        'fridge'     => 'Frigorifero',
        'coffee'     => 'Macchina caffè',
        'kettle'     => 'Bollitore',
        'washer'     => 'Lavatrice',
        'dryer'      => 'Asciugatrice',
        'iron'       => 'Ferro da stiro',
        'hairdryer'  => 'Asciugacapelli',
        'bathtub'    => 'Vasca da bagno',
        'parking'    => 'Parcheggio',
        'garage'     => 'Garage',
        'garden'     => 'Giardino',
        'terrace'    => 'Terrazza / Balcone',
        'bbq'        => 'Barbecue',
        'pool'       => 'Piscina',
        'hottub'     => 'Vasca idromassaggio',
        'ski'        => 'Vicino piste da sci',
        'bikes'      => 'Biciclette disponibili',
        'playground' => 'Area giochi bambini',
        'sauna'      => 'Sauna',
        'hiking'     => 'Sentieri escursionistici',
        'linens'     => 'Biancheria inclusa',
        'towels'     => 'Asciugamani inclusi',
        'cleaning'   => 'Pulizia finale inclusa',
        'crib'       => 'Culla disponibile',
        'highchair'  => 'Seggiolone',
        'safe'       => 'Cassaforte',
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        global $post;
        if ( ! $post || $post->post_type !== 'olo_service' ) {
            return '<div style="padding:24px;text-align:center;color:#9ca3af;background:#f9fafb;border-radius:8px">'
                 . '<p style="margin:0">Questo tile mostra le informazioni del servizio corrente. '
                 . 'Usalo in un template single per <strong>olo_service</strong>.</p>'
                 . '</div>';
        }

        $svc_id = $post->ID;
        $title  = get_the_title( $svc_id );
        $thumb  = get_the_post_thumbnail_url( $svc_id, 'full' );
        $color  = get_post_meta( $svc_id, '_olo_service_color', true ) ?: '#6366F1';
        $accent = ! empty( $s['acc_accent_color'] ) ? $s['acc_accent_color'] : $color;

        // Property data
        $valley     = get_post_meta( $svc_id, '_olo_service_valley', true );
        $address    = get_post_meta( $svc_id, '_olo_service_address', true );
        $bedrooms   = get_post_meta( $svc_id, '_olo_service_bedrooms', true );
        $beds       = get_post_meta( $svc_id, '_olo_service_beds', true );
        $bathrooms  = get_post_meta( $svc_id, '_olo_service_bathrooms', true );
        $capacity   = get_post_meta( $svc_id, '_olo_service_capacity', true );
        $sqm        = get_post_meta( $svc_id, '_olo_service_sqm', true );

        // Price
        $price_base = get_post_meta( $svc_id, '_olo_service_price', true );

        // Check-in/out
        $checkin_time  = get_post_meta( $svc_id, '_olo_service_checkin_time', true );
        $checkout_time = get_post_meta( $svc_id, '_olo_service_checkout_time', true );

        // Amenities
        $amenities = get_post_meta( $svc_id, '_olo_service_amenities', true );
        if ( is_string( $amenities ) ) {
            $amenities = maybe_unserialize( $amenities );
        }
        if ( ! is_array( $amenities ) ) {
            $amenities = [];
        }

        // Gallery
        $gallery_ids = get_post_meta( $svc_id, '_olo_service_gallery', true );
        if ( is_string( $gallery_ids ) ) {
            $gallery_ids = maybe_unserialize( $gallery_ids );
        }
        if ( ! is_array( $gallery_ids ) ) {
            $gallery_ids = [];
        }

        // Content
        $content = apply_filters( 'the_content', $post->post_content );

        // Settings
        $hero_h    = absint( $s['acc_hero_height'] ) ?: 400;
        $max_w     = absint( $s['acc_max_width'] ) ?: 900;
        $radius    = absint( $s['acc_card_radius'] );
        $shadow_map = [
            'none' => 'none',
            'sm'   => '0 1px 3px rgba(0,0,0,0.1)',
            'md'   => '0 4px 12px rgba(0,0,0,0.12)',
            'lg'   => '0 8px 30px rgba(0,0,0,0.12)',
        ];
        $shadow    = $shadow_map[ $s['acc_card_shadow'] ] ?? $shadow_map['lg'];
        $padding   = absint( $s['acc_body_padding'] ) ?: 32;
        $title_sz  = absint( $s['acc_title_size'] ) ?: 30;
        $amenity_cols = absint( $s['acc_amenity_cols_desktop'] ) ?: 4;
        $gallery_cols = absint( $s['acc_gallery_columns'] ) ?: 4;
        $gallery_h    = absint( $s['acc_gallery_height'] ) ?: 180;

        $uid = 'olo-si-' . wp_rand( 10000, 99999 );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> { max-width: <?php echo $max_w; ?>px; margin: 0 auto; }
            .<?php echo $uid; ?> .olo-si-hero { position: relative; height: <?php echo $hero_h; ?>px; overflow: hidden; border-radius: <?php echo $radius; ?>px <?php echo $radius; ?>px 0 0; }
            .<?php echo $uid; ?> .olo-si-hero img { width: 100%; height: 100%; object-fit: cover; display: block; }
            .<?php echo $uid; ?> .olo-si-hero-overlay { position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0,0,0,0.02) 50%, rgba(0,0,0,0.35) 100%); }
            .<?php echo $uid; ?> .olo-si-badges { position: absolute; top: 16px; right: 16px; display: flex; gap: 8px; z-index: 2; }
            .<?php echo $uid; ?> .olo-si-badge { background: rgba(30,41,59,0.75); backdrop-filter: blur(6px); color: #fff; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 5px; }
            .<?php echo $uid; ?> .olo-si-body { padding: <?php echo $padding; ?>px; background: #fff; box-shadow: <?php echo $shadow; ?>; border-radius: 0 0 <?php echo $radius; ?>px <?php echo $radius; ?>px; }
            .<?php echo $uid; ?> .olo-si-title { font-size: <?php echo $title_sz; ?>px; font-weight: 700; color: #1F2937; margin: 0 0 24px; line-height: 1.2; }
            .<?php echo $uid; ?> .olo-si-stats { display: grid; gap: 16px; margin-bottom: 28px; text-align: center; }
            .<?php echo $uid; ?> .olo-si-stat-icon { font-size: 22px; margin-bottom: 4px; color: #6B7280; }
            .<?php echo $uid; ?> .olo-si-stat-num { font-size: 22px; font-weight: 700; color: #1F2937; }
            .<?php echo $uid; ?> .olo-si-stat-label { font-size: 12px; text-transform: uppercase; color: #6B7280; letter-spacing: 0.5px; font-weight: 600; }
            .<?php echo $uid; ?> .olo-si-prices { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 32px; }
            .<?php echo $uid; ?> .olo-si-price-card { border: 2px solid #E5E7EB; border-radius: 12px; padding: 20px 16px; text-align: center; transition: border-color 0.2s; }
            .<?php echo $uid; ?> .olo-si-price-card:hover { border-color: <?php echo esc_attr( $accent ); ?>; }
            .<?php echo $uid; ?> .olo-si-price-amount { font-size: 26px; font-weight: 700; color: <?php echo esc_attr( $accent ); ?>; }
            .<?php echo $uid; ?> .olo-si-price-label { font-size: 14px; color: #6B7280; margin-top: 4px; }
            .<?php echo $uid; ?> .olo-si-section-title { font-size: 18px; font-weight: 700; color: #1F2937; margin: 0 0 16px; font-style: italic; }
            .<?php echo $uid; ?> .olo-si-description { font-size: 15px; line-height: 1.75; color: #374151; margin-bottom: 32px; }
            .<?php echo $uid; ?> .olo-si-description p { margin: 0 0 16px; }
            .<?php echo $uid; ?> .olo-si-amenities { display: grid; grid-template-columns: repeat(<?php echo $amenity_cols; ?>, 1fr); gap: 10px 24px; margin-bottom: 32px; }
            .<?php echo $uid; ?> .olo-si-amenity { display: flex; align-items: center; gap: 8px; font-size: 14px; color: #374151; padding: 4px 0; }
            .<?php echo $uid; ?> .olo-si-amenity-check { color: <?php echo esc_attr( $accent ); ?>; font-size: 16px; flex-shrink: 0; }
            .<?php echo $uid; ?> .olo-si-checkin-bar { background: #F9FAFB; border-top: 1px solid #E5E7EB; margin: 0 -<?php echo $padding; ?>px -<?php echo $padding; ?>px; padding: 16px <?php echo $padding; ?>px; border-radius: 0 0 <?php echo $radius; ?>px <?php echo $radius; ?>px; font-size: 14px; color: #374151; }
            .<?php echo $uid; ?> .olo-si-checkin-bar strong { color: #1F2937; }
            .<?php echo $uid; ?> .olo-si-gallery { display: grid; grid-template-columns: repeat(<?php echo $gallery_cols; ?>, 1fr); gap: 8px; margin-bottom: 32px; }
            .<?php echo $uid; ?> .olo-si-gallery a { display: block; border-radius: 8px; overflow: hidden; height: <?php echo $gallery_h; ?>px; }
            .<?php echo $uid; ?> .olo-si-gallery img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.3s ease; }
            .<?php echo $uid; ?> .olo-si-gallery a:hover img { transform: scale(1.05); }
            @media (max-width: 640px) {
                .<?php echo $uid; ?> .olo-si-gallery { grid-template-columns: repeat(2, 1fr); }
                .<?php echo $uid; ?> .olo-si-stats { grid-template-columns: repeat(2, 1fr); }
                .<?php echo $uid; ?> .olo-si-prices { grid-template-columns: 1fr; }
                .<?php echo $uid; ?> .olo-si-amenities { grid-template-columns: repeat(2, 1fr); }
            }
        </style>

        <div class="<?php echo esc_attr( $uid ); ?>">
            <?php // ── Hero Image ── ?>
            <?php if ( $thumb ) : ?>
            <div class="olo-si-hero">
                <img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $title ); ?>" />
                <div class="olo-si-hero-overlay"></div>
                <?php if ( $valley || $address ) : ?>
                <div class="olo-si-badges">
                    <?php if ( $valley ) : ?>
                        <span class="olo-si-badge">&#9968; <?php echo esc_html( $valley ); ?></span>
                    <?php endif; ?>
                    <?php if ( $address ) : ?>
                        <span class="olo-si-badge">&#9737; <?php echo esc_html( $address ); ?></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="olo-si-body">
                <?php // ── Title ── ?>
                <h1 class="olo-si-title"><?php echo esc_html( $title ); ?></h1>

                <?php // ── Gallery ── ?>
                <?php if ( ! empty( $s['acc_show_gallery'] ) && ! empty( $gallery_ids ) ) : ?>
                <div class="olo-si-gallery" uk-lightbox="animation: slide">
                    <?php foreach ( $gallery_ids as $att_id ) :
                        $full  = wp_get_attachment_image_url( $att_id, 'large' );
                        $thumb_url = wp_get_attachment_image_url( $att_id, 'medium' );
                        $alt   = get_post_meta( $att_id, '_wp_attachment_image_alt', true ) ?: $title;
                        if ( ! $full ) continue;
                    ?>
                    <a href="<?php echo esc_url( $full ); ?>" data-caption="<?php echo esc_attr( $alt ); ?>">
                        <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" />
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php // ── Stats Row ── ?>
                <?php
                $stats = [];
                if ( $bedrooms !== '' && $bedrooms !== '0' && $bedrooms ) $stats[] = [ 'icon' => '🛏️', 'num' => $bedrooms, 'label' => 'Camere' ];
                if ( $beds )                                              $stats[] = [ 'icon' => '🛌', 'num' => $beds, 'label' => 'Posti letto' ];
                if ( $bathrooms )                                         $stats[] = [ 'icon' => '🚿', 'num' => $bathrooms, 'label' => 'Bagni' ];
                if ( $capacity )                                          $stats[] = [ 'icon' => '👥', 'num' => $capacity, 'label' => 'Ospiti' ];
                if ( $sqm )                                               $stats[] = [ 'icon' => '📐', 'num' => $sqm . ' m²', 'label' => 'Superficie' ];
                ?>
                <?php if ( ! empty( $stats ) ) : ?>
                <div class="olo-si-stats" style="grid-template-columns: repeat(<?php echo min( count( $stats ), 5 ); ?>, 1fr)">
                    <?php foreach ( $stats as $stat ) : ?>
                    <div>
                        <div class="olo-si-stat-icon"><?php echo $stat['icon']; ?></div>
                        <div class="olo-si-stat-num"><?php echo esc_html( $stat['num'] ); ?></div>
                        <div class="olo-si-stat-label"><?php echo esc_html( $stat['label'] ); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php // ── Pricing ── ?>
                <?php if ( $price_base ) : ?>
                <div class="olo-si-prices" style="grid-template-columns: 1fr">
                    <div class="olo-si-price-card">
                        <div class="olo-si-price-amount">&euro; <?php echo esc_html( number_format( (float) $price_base, 2, ',', '.' ) ); ?></div>
                        <div class="olo-si-price-label"><?php echo esc_html( olo_t( '/ notte' ) ); ?></div>
                    </div>
                </div>
                <?php endif; ?>

                <?php // ── Description ── ?>
                <?php if ( ! empty( $s['acc_show_description'] ) && ! empty( $content ) ) : ?>
                <h3 class="olo-si-section-title"><?php echo esc_html( olo_t( 'Descrizione' ) ); ?></h3>
                <div class="olo-si-description"><?php echo $content; ?></div>
                <?php endif; ?>

                <?php // ── Amenities ── ?>
                <?php if ( ! empty( $s['acc_show_amenities'] ) && ! empty( $amenities ) ) : ?>
                <h3 class="olo-si-section-title"><?php echo esc_html( olo_t( 'Servizi e comfort' ) ); ?></h3>
                <div class="olo-si-amenities">
                    <?php foreach ( $amenities as $key ) :
                        $label = $this->amenity_labels[ $key ] ?? ucfirst( str_replace( '_', ' ', $key ) );
                    ?>
                    <div class="olo-si-amenity">
                        <span class="olo-si-amenity-check">&#10003;</span>
                        <span><?php echo esc_html( $label ); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php // ── Check-in / Check-out Bar ── ?>
                <?php if ( ! empty( $s['acc_show_checkin'] ) && ( $checkin_time || $checkout_time ) ) : ?>
                <div class="olo-si-checkin-bar">
                    <?php if ( $checkin_time ) : ?>
                        <strong><?php echo esc_html( olo_t( 'Check-in:' ) ); ?></strong> <?php echo esc_html( olo_t( 'dalle ' ) . $checkin_time ); ?>
                    <?php endif; ?>
                    <?php if ( $checkout_time ) : ?>
                        &nbsp;| &nbsp;<strong><?php echo esc_html( olo_t( 'Check-out:' ) ); ?></strong> <?php echo esc_html( olo_t( 'entro ' ) . $checkout_time ); ?>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
