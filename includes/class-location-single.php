<?php
/**
 * Renders extra ACF fields (gallery, details, rental info) on single Location posts.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Location_Single {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_filter( 'the_content', [ $this, 'append_location_details' ], 20 );
    }

    public function append_location_details( $content ) {
        if ( ! is_singular() || get_post_type() !== 'location' ) {
            return $content;
        }

        // Skip if a Olobuilder single template is active for locations
        if ( get_option( 'olobuild_active_single_location' ) ) {
            return $content;
        }

        $post_id = get_the_ID();
        $extra   = '';

        // ── Info Cards ──
        $extra .= $this->render_info_cards( $post_id );

        // ── Gallery ──
        $extra .= $this->render_gallery( $post_id );

        // ── Rental Details ──
        $extra .= $this->render_rental( $post_id );

        // ── Map ──
        $extra .= $this->render_single_map( $post_id );

        if ( $extra ) {
            // Enqueue UIkit if not already
            wp_enqueue_style( 'uikit-css', OLOBUILD_URL . 'assets/vendor/uikit/css/uikit.min.css', [], '3.21.16' );
            wp_enqueue_style( 'olo-location-single-css', OLOBUILD_URL . 'assets/css/location-single.css', [], OLOBUILD_VERSION );
        }

        return $content . $extra;
    }

    private function render_info_cards( $post_id ) {
        $phone   = get_post_meta( $post_id, 'location_phone', true );
        $email   = get_post_meta( $post_id, 'location_email', true );
        $website = get_post_meta( $post_id, 'location_website', true );
        $hours   = get_post_meta( $post_id, 'location_hours', true );
        $price   = get_post_meta( $post_id, 'location_price_range', true );
        $rating  = get_post_meta( $post_id, 'location_rating', true );
        $ticket  = get_post_meta( $post_id, 'location_ticket_url', true );
        $address = get_post_meta( $post_id, 'location_address', true );
        $city    = get_post_meta( $post_id, 'location_city', true );
        $highlights = get_post_meta( $post_id, 'location_highlights', true );
        $access  = get_post_meta( $post_id, 'location_accessibility', false );
        $langs   = get_post_meta( $post_id, 'location_languages', false );

        $has_data = $phone || $email || $website || $hours || $address || $highlights;
        if ( ! $has_data ) {
            return '';
        }

        ob_start();
        ?>
        <div class="olo-loc-details">
            <h3>Dettagli</h3>
            <div class="olo-loc-cards">
                <?php if ( $address || $city ) : ?>
                <div class="olo-loc-card">
                    <span class="olo-loc-card-icon">&#128205;</span>
                    <div>
                        <strong>Indirizzo</strong>
                        <span><?php echo esc_html( $address ); ?><?php if ( $city ) echo ', ' . esc_html( $city ); ?></span>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ( $phone ) : ?>
                <div class="olo-loc-card">
                    <span class="olo-loc-card-icon">&#9742;</span>
                    <div>
                        <strong>Telefono</strong>
                        <a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ( $email ) : ?>
                <div class="olo-loc-card">
                    <span class="olo-loc-card-icon">&#9993;</span>
                    <div>
                        <strong>Email</strong>
                        <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ( $website ) : ?>
                <div class="olo-loc-card">
                    <span class="olo-loc-card-icon">&#127760;</span>
                    <div>
                        <strong>Sito web</strong>
                        <a href="<?php echo esc_url( $website ); ?>" target="_blank" rel="noopener"><?php echo esc_html( preg_replace( '#^https?://(www\.)?#', '', $website ) ); ?></a>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ( $hours ) : ?>
                <div class="olo-loc-card">
                    <span class="olo-loc-card-icon">&#128339;</span>
                    <div>
                        <strong>Orari di apertura</strong>
                        <span><?php echo wp_kses_post( $hours ); ?></span>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ( $price ) : ?>
                <div class="olo-loc-card">
                    <span class="olo-loc-card-icon">&#128176;</span>
                    <div>
                        <strong>Fascia di prezzo</strong>
                        <span><?php echo esc_html( $price ); ?></span>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ( $rating ) : ?>
                <div class="olo-loc-card">
                    <span class="olo-loc-card-icon">&#11088;</span>
                    <div>
                        <strong>Valutazione</strong>
                        <span><?php echo esc_html( $rating ); ?> / 5</span>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ( $ticket ) : ?>
                <div class="olo-loc-card">
                    <span class="olo-loc-card-icon">&#127915;</span>
                    <div>
                        <strong>Biglietti</strong>
                        <a href="<?php echo esc_url( $ticket ); ?>" target="_blank" rel="noopener">Prenota online</a>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <?php if ( ! empty( $access ) ) : ?>
            <div class="olo-loc-badges">
                <?php
                $badge_labels = [
                    'wheelchair'   => '&#9855; Sedia a rotelle',
                    'parking'      => '&#127359; Parcheggio',
                    'elevator'     => '&#128715; Ascensore',
                    'audio_guide'  => '&#127911; Audioguida',
                    'pet_friendly' => '&#128062; Animali ammessi',
                ];
                foreach ( $access as $a ) {
                    if ( isset( $badge_labels[ $a ] ) ) {
                        echo '<span class="olo-loc-badge">' . esc_html( $badge_labels[ $a ] ) . '</span>';
                    }
                }
                ?>
            </div>
            <?php endif; ?>

            <?php if ( ! empty( $langs ) ) : ?>
            <div class="olo-loc-langs">
                <strong>Lingue:</strong>
                <?php
                $lang_labels = [ 'it' => '&#127470;&#127481; Italiano', 'en' => '&#127468;&#127463; Inglese', 'fr' => '&#127467;&#127479; Francese', 'de' => '&#127465;&#127466; Tedesco', 'es' => '&#127466;&#127480; Spagnolo', 'zh' => '&#127464;&#127475; Cinese', 'ja' => '&#127471;&#127477; Giapponese' ];
                $lang_names = [];
                foreach ( $langs as $l ) {
                    if ( isset( $lang_labels[ $l ] ) ) {
                        $lang_names[] = $lang_labels[ $l ];
                    }
                }
                echo implode( ' &middot; ', $lang_names ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- values come exclusively from the hardcoded $lang_labels map above
                ?>
            </div>
            <?php endif; ?>

            <?php if ( $highlights ) : ?>
            <div class="olo-loc-highlights">
                <strong>In evidenza</strong>
                <ul>
                <?php
                $lines = preg_split( '/\r\n|\r|\n|<br\s*\/?>/', $highlights );
                foreach ( $lines as $line ) {
                    $line = trim( wp_strip_all_tags( $line ) );
                    if ( $line ) {
                        echo '<li>' . esc_html( $line ) . '</li>';
                    }
                }
                ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private function render_gallery( $post_id ) {
        $gallery_ids = get_post_meta( $post_id, 'location_gallery', true );
        if ( empty( $gallery_ids ) || ! is_array( $gallery_ids ) ) {
            return '';
        }

        ob_start();
        ?>
        <div class="olo-loc-gallery">
            <h3>Galleria fotografica</h3>
            <div class="olo-loc-gallery-grid" uk-lightbox="animation: slide">
                <?php foreach ( $gallery_ids as $att_id ) :
                    $full  = wp_get_attachment_image_url( $att_id, 'large' );
                    $thumb = wp_get_attachment_image_url( $att_id, 'medium' );
                    $alt   = get_post_meta( $att_id, '_wp_attachment_image_alt', true ) ?: get_the_title( $post_id );
                    if ( ! $full ) continue;
                ?>
                    <a href="<?php echo esc_url( $full ); ?>" data-caption="<?php echo esc_attr( $alt ); ?>">
                        <img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" />
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function render_rental( $post_id ) {
        $price_night = get_post_meta( $post_id, 'rental_price_night', true );
        if ( ! $price_night ) {
            return '';
        }

        $bedrooms     = get_post_meta( $post_id, 'rental_bedrooms', true );
        $bathrooms    = get_post_meta( $post_id, 'rental_bathrooms', true );
        $guests       = get_post_meta( $post_id, 'rental_guests', true );
        $sqm          = get_post_meta( $post_id, 'rental_sqm', true );
        $floor        = get_post_meta( $post_id, 'rental_floor', true );
        $price_week   = get_post_meta( $post_id, 'rental_price_week', true );
        $price_month  = get_post_meta( $post_id, 'rental_price_month', true );
        $cleaning     = get_post_meta( $post_id, 'rental_cleaning_fee', true );
        $deposit      = get_post_meta( $post_id, 'rental_deposit', true );
        $min_stay     = get_post_meta( $post_id, 'rental_min_stay', true );
        $checkin      = get_post_meta( $post_id, 'rental_checkin', true );
        $checkout     = get_post_meta( $post_id, 'rental_checkout', true );
        $amenities    = get_post_meta( $post_id, 'rental_amenities', false );
        $rules        = get_post_meta( $post_id, 'rental_rules', false );
        $booking_url  = get_post_meta( $post_id, 'rental_booking_url', true );
        $availability = get_post_meta( $post_id, 'rental_availability', true );

        $amenity_labels = [
            'wifi' => '&#128246; Wi-Fi', 'ac' => '&#10052; Aria condizionata', 'heating' => '&#9832; Riscaldamento',
            'kitchen' => '&#127859; Cucina', 'washing_machine' => '&#129529; Lavatrice', 'dryer' => '&#127744; Asciugatrice',
            'dishwasher' => '&#129348; Lavastoviglie', 'tv' => '&#128250; TV', 'balcony' => '&#127748; Balcone',
            'garden' => '&#127793; Giardino', 'pool' => '&#127946; Piscina', 'parking' => '&#127359; Parcheggio',
            'garage' => '&#128664; Garage', 'elevator' => '&#128715; Ascensore', 'doorman' => '&#128100; Portineria',
            'safe' => '&#128274; Cassaforte', 'iron' => '&#128100; Ferro da stiro', 'hair_dryer' => '&#128168; Asciugacapelli',
            'coffee_machine' => '&#9749; Macchina del caffe', 'bbq' => '&#127830; Barbecue', 'workspace' => '&#128187; Postazione lavoro',
            'baby_crib' => '&#128118; Lettino', 'high_chair' => '&#129681; Seggiolone',
        ];

        $rule_labels = [
            'no_smoking'  => '&#128685; Vietato fumare',
            'no_pets'     => '&#128683; No animali',
            'no_parties'  => '&#128683; No feste',
            'quiet_hours' => '&#128284; Silenzio (22-8)',
        ];

        ob_start();
        ?>
        <div class="olo-loc-rental">
            <h3>Dettagli proprieta</h3>

            <!-- Pricing card -->
            <div class="olo-loc-rental-pricing">
                <div class="olo-loc-rental-price-main">
                    &euro;<?php echo esc_html( $price_night ); ?> <small>/ notte</small>
                </div>
                <div class="olo-loc-rental-price-alt">
                    <?php if ( $price_week ) : ?>
                        <span>&euro;<?php echo esc_html( $price_week ); ?> / settimana</span>
                    <?php endif; ?>
                    <?php if ( $price_month ) : ?>
                        <span>&euro;<?php echo esc_html( $price_month ); ?> / mese</span>
                    <?php endif; ?>
                </div>
                <?php if ( $cleaning || $deposit ) : ?>
                <div class="olo-loc-rental-fees">
                    <?php if ( $cleaning ) : ?>
                        <span>Pulizia: &euro;<?php echo esc_html( $cleaning ); ?></span>
                    <?php endif; ?>
                    <?php if ( $deposit ) : ?>
                        <span>Deposito: &euro;<?php echo esc_html( $deposit ); ?></span>
                    <?php endif; ?>
                    <?php if ( $min_stay ) : ?>
                        <span>Soggiorno minimo: <?php echo esc_html( $min_stay ); ?> notti</span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php if ( $booking_url ) : ?>
                    <a href="<?php echo esc_url( $booking_url ); ?>" target="_blank" rel="noopener" class="olo-loc-rental-book-btn">Prenota ora</a>
                <?php endif; ?>
            </div>

            <!-- Specs -->
            <div class="olo-loc-rental-specs">
                <?php if ( $bedrooms !== '' ) : ?>
                    <div class="olo-loc-rental-spec"><strong><?php echo esc_html( $bedrooms ); ?></strong> Camere</div>
                <?php endif; ?>
                <?php if ( $bathrooms ) : ?>
                    <div class="olo-loc-rental-spec"><strong><?php echo esc_html( $bathrooms ); ?></strong> Bagni</div>
                <?php endif; ?>
                <?php if ( $guests ) : ?>
                    <div class="olo-loc-rental-spec"><strong><?php echo esc_html( $guests ); ?></strong> Ospiti max</div>
                <?php endif; ?>
                <?php if ( $sqm ) : ?>
                    <div class="olo-loc-rental-spec"><strong><?php echo esc_html( $sqm ); ?></strong> m&sup2;</div>
                <?php endif; ?>
                <?php if ( $floor ) : ?>
                    <div class="olo-loc-rental-spec"><strong><?php echo esc_html( $floor ); ?></strong> Piano</div>
                <?php endif; ?>
            </div>

            <!-- Check-in / Check-out -->
            <?php if ( $checkin || $checkout ) : ?>
            <div class="olo-loc-rental-times">
                <?php if ( $checkin ) : ?>
                    <span>Check-in: <strong><?php echo esc_html( $checkin ); ?></strong></span>
                <?php endif; ?>
                <?php if ( $checkout ) : ?>
                    <span>Check-out: <strong><?php echo esc_html( $checkout ); ?></strong></span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Amenities -->
            <?php if ( ! empty( $amenities ) ) : ?>
            <div class="olo-loc-rental-amenities">
                <h4>Servizi</h4>
                <div class="olo-loc-rental-amenities-grid">
                    <?php foreach ( $amenities as $a ) :
                        if ( isset( $amenity_labels[ $a ] ) ) : ?>
                            <span class="olo-loc-badge"><?php echo esc_html( $amenity_labels[ $a ] ); ?></span>
                        <?php endif;
                    endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- House Rules -->
            <?php if ( ! empty( $rules ) ) : ?>
            <div class="olo-loc-rental-rules">
                <h4>Regole della casa</h4>
                <div class="olo-loc-rental-rules-list">
                    <?php foreach ( $rules as $r ) :
                        if ( isset( $rule_labels[ $r ] ) ) : ?>
                            <span class="olo-loc-badge"><?php echo esc_html( $rule_labels[ $r ] ); ?></span>
                        <?php endif;
                    endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private function render_single_map( $post_id ) {
        $map_data = get_post_meta( $post_id, 'location_map', true );
        if ( empty( $map_data ) || ! is_array( $map_data ) || ! isset( $map_data['lat'], $map_data['lng'] ) ) {
            return '';
        }

        $lat = floatval( $map_data['lat'] );
        $lng = floatval( $map_data['lng'] );
        if ( $lat === 0.0 && $lng === 0.0 ) {
            return '';
        }

        $src = "https://www.openstreetmap.org/export/embed.html?bbox="
             . ( $lng - 0.005 ) . ',' . ( $lat - 0.003 ) . ','
             . ( $lng + 0.005 ) . ',' . ( $lat + 0.003 )
             . "&layer=mapnik&marker=" . $lat . ',' . $lng;

        ob_start();
        ?>
        <div class="olo-loc-map">
            <h3>Posizione</h3>
            <iframe
                src="<?php echo esc_url( $src ); ?>"
                style="width: 100%; height: 300px; border: 0; border-radius: 8px;"
                loading="lazy"
            ></iframe>
        </div>
        <?php
        return ob_get_clean();
    }
}
