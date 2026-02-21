<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Booking_Frontend {

    public function init() {
        add_shortcode( 'olo_booking', [ $this, 'render_shortcode' ] );
    }

    public function render_shortcode( $atts ) {
        $atts = shortcode_atts( [
            'service'       => '',
            'show_price'    => 'true',
            'show_duration' => 'true',
            'primary_color' => '#6366F1',
        ], $atts );

        $service = null;
        if ( is_numeric( $atts['service'] ) ) {
            $service = get_post( (int) $atts['service'] );
        } elseif ( $atts['service'] ) {
            $posts = get_posts( [
                'post_type'      => 'olo_service',
                'name'           => $atts['service'],
                'posts_per_page' => 1,
                'post_status'    => 'publish',
            ] );
            $service = $posts[0] ?? null;
        }

        $this->enqueue_assets();

        $config = [
            'serviceId'    => $service ? $service->ID : 0,
            'serviceType'  => $service ? ( get_post_meta( $service->ID, '_olo_service_type', true ) ?: 'accommodation' ) : 'accommodation',
            'showPrice'    => $atts['show_price'] === 'true',
            'showDuration' => $atts['show_duration'] === 'true',
            'primaryColor' => sanitize_hex_color( $atts['primary_color'] ) ?: '#6366F1',
            'restUrl'      => esc_url_raw( rest_url( 'olo-booking/v1' ) ),
            'nonce'        => wp_create_nonce( 'wp_rest' ),
        ];

        return $this->render_widget( $config, $service );
    }

    /**
     * Programmatic rendering (used by Olobuild tile).
     */
    public function render_booking( $config ) {
        $this->enqueue_assets();
        $service = ! empty( $config['serviceId'] ) ? get_post( $config['serviceId'] ) : null;
        $config['restUrl']     = esc_url_raw( rest_url( 'olo-booking/v1' ) );
        $config['nonce']       = wp_create_nonce( 'wp_rest' );
        $config['serviceType'] = $service ? ( get_post_meta( $service->ID, '_olo_service_type', true ) ?: 'accommodation' ) : 'accommodation';
        return $this->render_widget( $config, $service );
    }

    public function render_widget( $config, $service = null ) {
        $config = wp_parse_args( $config, [
            'serviceId'    => 0,
            'serviceType'  => 'accommodation',
            'showPrice'    => false,
            'showDuration' => false,
            'primaryColor' => '#6366F1',
            'restUrl'      => '',
            'nonce'        => '',
        ] );
        $uid = 'olob-' . wp_rand( 10000, 99999 );

        ob_start();
        ?>
        <div class="olob-widget <?php echo esc_attr( $uid ); ?>" id="<?php echo esc_attr( $uid ); ?>"
             data-config='<?php echo esc_attr( wp_json_encode( $config ) ); ?>'>

            <?php if ( ! $config['serviceId'] ) : ?>
                <div class="olob-service-list" id="<?php echo esc_attr( $uid ); ?>-services">
                    <p class="olob-loading">Caricamento servizi...</p>
                </div>
            <?php else : ?>
                <?php $this->render_service_header( $service, $config ); ?>
            <?php endif; ?>

            <!-- Calendar -->
            <div class="olob-cal-wrap">
                <div class="olob-cal-header">
                    <button type="button" class="olob-cal-nav olob-cal-prev">&lsaquo;</button>
                    <span class="olob-cal-month"></span>
                    <button type="button" class="olob-cal-nav olob-cal-next">&rsaquo;</button>
                </div>
                <div class="olob-cal-weekdays">
                    <span>Lun</span><span>Mar</span><span>Mer</span>
                    <span>Gio</span><span>Ven</span><span>Sab</span><span>Dom</span>
                </div>
                <div class="olob-cal-grid"></div>
            </div>

            <!-- Date summary (accommodation mode) -->
            <div class="olob-date-summary" style="display:none">
                <div class="olob-date-summary-row">
                    <span class="olob-date-label">Check-in:</span>
                    <span class="olob-date-value olob-checkin-val">—</span>
                </div>
                <div class="olob-date-summary-row">
                    <span class="olob-date-label">Check-out:</span>
                    <span class="olob-date-value olob-checkout-val">—</span>
                </div>
                <div class="olob-date-summary-row olob-nights-row" style="display:none">
                    <span class="olob-date-label">Notti:</span>
                    <span class="olob-date-value olob-nights-val">—</span>
                </div>
            </div>

            <!-- Booking Form -->
            <div class="olob-book-form" style="display:none">
                <h4 class="olob-form-title"></h4>
                <div class="olob-form-fields">
                    <div class="olob-form-row">
                        <input type="text" class="olob-input" name="guest_name" placeholder="Il tuo nome *" required />
                    </div>
                    <div class="olob-form-row">
                        <input type="email" class="olob-input" name="guest_email" placeholder="La tua email *" required />
                    </div>
                    <div class="olob-form-row">
                        <input type="tel" class="olob-input" name="guest_phone" placeholder="Telefono (opzionale)" />
                    </div>
                    <div class="olob-form-row">
                        <input type="number" class="olob-input" name="guest_count" min="1" max="20" value="1" placeholder="Numero ospiti" />
                    </div>
                    <div class="olob-form-row">
                        <textarea class="olob-input" name="notes" rows="2" placeholder="Note (opzionale)"></textarea>
                    </div>
                    <div class="olob-form-actions">
                        <button type="button" class="olob-btn olob-btn-back">&#8592; Indietro</button>
                        <button type="button" class="olob-btn olob-btn-primary olob-btn-confirm">Conferma prenotazione</button>
                    </div>
                </div>
            </div>

            <!-- Success -->
            <div class="olob-success" style="display:none">
                <div class="olob-success-icon">&#10003;</div>
                <h3>Prenotazione inviata!</h3>
                <p>Riceverai una conferma via email.</p>
                <button type="button" class="olob-btn olob-btn-primary olob-btn-new">Nuova prenotazione</button>
            </div>
        </div>
        <style>
        .<?php echo esc_attr( $uid ); ?> {
            --olob-primary: <?php echo esc_attr( $config['primaryColor'] ); ?>;
        }
        </style>
        <?php
        return ob_get_clean();
    }

    private function render_service_header( $service, $config ) {
        if ( ! $service ) return;
        $duration = get_post_meta( $service->ID, '_olo_service_duration', true ) ?: '60';
        $price    = get_post_meta( $service->ID, '_olo_service_price', true );
        $image    = get_the_post_thumbnail_url( $service->ID, 'medium' );
        ?>
        <div class="olob-service-header">
            <?php if ( $image ) : ?>
                <img src="<?php echo esc_url( $image ); ?>" alt="" class="olob-service-img" />
            <?php endif; ?>
            <div class="olob-service-info">
                <h3 class="olob-service-name"><?php echo esc_html( $service->post_title ); ?></h3>
                <div class="olob-service-meta">
                    <?php if ( $config['showDuration'] ) : ?>
                        <span class="olob-service-duration">&#9201; <?php echo esc_html( $duration ); ?> min</span>
                    <?php endif; ?>
                    <?php if ( $config['showPrice'] && $price ) : ?>
                        <span class="olob-service-price">&euro; <?php echo esc_html( number_format( (float) $price, 2, ',', '.' ) ); ?></span>
                    <?php endif; ?>
                </div>
                <?php if ( $service->post_excerpt ) : ?>
                    <p class="olob-service-desc"><?php echo esc_html( $service->post_excerpt ); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    private function enqueue_assets() {
        if ( wp_script_is( 'olo-book-front', 'enqueued' ) ) return;

        wp_enqueue_style(
            'olo-book-front',
            OLO_BOOK_URL . 'assets/css/booking-front.css',
            [],
            OLO_BOOK_VERSION
        );

        wp_enqueue_script(
            'olo-book-front',
            OLO_BOOK_URL . 'assets/js/booking-front.js',
            [],
            OLO_BOOK_VERSION,
            true
        );
    }
}
