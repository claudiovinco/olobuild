<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_ServiceSearch_Tile extends Olo_Tile_Base {

    protected $type     = 'servicesearch';
    protected $name     = 'Cerca strutture';
    protected $icon     = 'dashicons-search';
    protected $category = 'dynamic';
    protected $defaults = [
        'results_url'       => '',
        'layout'            => 'horizontal',
        'visible_filters'   => 'valley,checkin,guests,bedrooms,altitude',
        'altitude_ranges'   => '0-1000,1000-1500,1500-2000,2000-9999',
        'guests_ranges'     => '1-2,3-4,5-6,7-99',
        'amenities_list'    => 'wifi,fireplace,parking,sauna,hottub,bbq,garden,pets',
        'button_text'       => 'Cerca',
        'button_bg'         => '',
        'button_color'      => '#ffffff',
        'filter_bg'         => '#ffffff',
        'filter_border'     => '#e5e7eb',
        'border_radius'     => '8',
        'overlap'           => false,
        'overlap_offset'    => '60',
        'overlap_shadow'    => true,
        'overlap_max_width' => '1100',
        // Larghezza campi (%)
        'width_valley'   => '',
        'width_checkin'  => '',
        'width_checkout' => '',
        'width_guests'   => '',
        'width_bedrooms' => '',
        'width_altitude' => '',
        'width_type'     => '',
        'width_club'     => '',
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $search_id   = 'olo-svsearch-' . wp_unique_id();
        $results_url = $s['results_url'] ?: home_url( '/cercatutto/' );
        $layout      = $s['layout'] === 'vertical' ? 'vertical' : 'horizontal';

        // Parse visible filters CSV → set for fast lookup
        $vf = array_flip( array_filter( array_map( 'trim', explode( ',', $s['visible_filters'] ?? '' ) ) ) );

        // Collect valleys from DB
        $valleys = $this->get_valleys();

        // Club categories
        $club_cats = isset( $vf['club'] ) ? $this->get_club_categories() : [];

        // Altitude ranges
        $alt_ranges = array_filter( array_map( 'trim', explode( ',', $s['altitude_ranges'] ) ) );

        // Guests ranges
        $guests_ranges = array_filter( array_map( 'trim', explode( ',', $s['guests_ranges'] ) ) );

        // Amenities
        $amenities = array_filter( array_map( 'trim', explode( ',', $s['amenities_list'] ) ) );

        $amenity_labels = class_exists( 'Olo_Amenities_Catalog' )
            ? Olo_Amenities_Catalog::get_all_labels()
            : [
                'wifi' => 'WiFi', 'fireplace' => 'Camino', 'parking' => 'Parcheggio',
                'kitchen' => 'Cucina', 'tv' => 'TV', 'bbq' => 'BBQ', 'terrace' => 'Terrazza',
                'heating' => 'Riscaldamento', 'sauna' => 'Sauna', 'hottub' => 'Idromassaggio',
                'ski' => 'Sci', 'garden' => 'Giardino', 'pets' => 'Animali',
                'washer' => 'Lavatrice', 'highchair' => 'Seggiolone', 'aircon' => 'Aria Cond.',
                'dishwasher' => 'Lavastoviglie', 'linens' => 'Biancheria',
                'towels' => 'Asciugamani', 'pool' => 'Piscina', 'hiking' => 'Escursioni',
            ];

        $border_radius = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $filter_bg     = $s['filter_bg'] ?: '#ffffff';
        $filter_border = $s['filter_border'] ?: '#e5e7eb';
        $is_overlap    = ! empty( $s['overlap'] );
        $overlap_offset = max( 20, min( 200, absint( $s['overlap_offset'] ?? 60 ) ) );
        $overlap_shadow = ! empty( $s['overlap_shadow'] );
        $overlap_max_w  = max( 600, min( 1400, absint( $s['overlap_max_width'] ?? 1100 ) ) );

        $wrap_style = 'border-radius:' . $border_radius . ';';
        $wrap_style .= 'background:' . esc_attr( $filter_bg ) . ';';
        $wrap_style .= 'border:1px solid ' . esc_attr( $filter_border ) . ';';

        if ( $is_overlap ) {
            $wrap_style .= 'position:relative;z-index:10;';
            $wrap_style .= 'margin-top:-' . $overlap_offset . 'px;';
            $wrap_style .= 'max-width:' . $overlap_max_w . 'px;';
            $wrap_style .= 'margin-left:auto;margin-right:auto;';
            if ( $overlap_shadow ) {
                $wrap_style .= 'box-shadow:0 8px 30px rgba(0,0,0,0.15);';
            }
        }

        // Field widths: build inline style for flex-basis
        $field_widths = [];
        foreach ( [ 'valley', 'checkin', 'checkout', 'guests', 'bedrooms', 'altitude', 'type', 'club' ] as $fk ) {
            $w = absint( $s[ 'width_' . $fk ] ?? 0 );
            if ( $w > 0 && $layout === 'horizontal' ) {
                $field_widths[ $fk ] = 'flex:0 0 calc(' . $w . '% - 10px);min-width:0;';
            }
        }

        $config = [
            'resultsUrl' => esc_url( $results_url ),
        ];

        ob_start();
        ?>
        <div id="<?php echo esc_attr( $search_id ); ?>"
             class="olo-svsearch olo-svsearch--<?php echo esc_attr( $layout ); ?>"
             style="<?php echo $wrap_style; ?>"
             data-svsearch-config="<?php echo base64_encode( wp_json_encode( $config ) ); ?>">

            <div class="olo-svsearch-fields">

                <?php if ( isset( $vf['valley'] ) && ! empty( $valleys ) ) : ?>
                <div class="olo-svsearch-field"<?php if ( ! empty( $field_widths['valley'] ) ) echo ' style="' . esc_attr( $field_widths['valley'] ) . '"'; ?>>
                    <label class="olo-svsearch-label"><?php echo esc_html( olo_t( 'Località' ) ); ?></label>
                    <select class="olo-svsearch-select" data-param="valley">
                        <option value=""><?php echo esc_html( olo_t( 'Tutte le località' ) ); ?></option>
                        <?php foreach ( $valleys as $v ) : ?>
                        <option value="<?php echo esc_attr( $v ); ?>"><?php echo esc_html( $v ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <?php if ( isset( $vf['checkin'] ) ) : ?>
                <div class="olo-svsearch-field"<?php if ( ! empty( $field_widths['checkin'] ) ) echo ' style="' . esc_attr( $field_widths['checkin'] ) . '"'; ?>>
                    <label class="olo-svsearch-label"><?php echo esc_html( olo_t( 'Check-in' ) ); ?></label>
                    <input type="date" class="olo-svsearch-input" data-param="checkin">
                </div>
                <div class="olo-svsearch-field"<?php if ( ! empty( $field_widths['checkout'] ) ) echo ' style="' . esc_attr( $field_widths['checkout'] ) . '"'; ?>>
                    <label class="olo-svsearch-label"><?php echo esc_html( olo_t( 'Check-out' ) ); ?></label>
                    <input type="date" class="olo-svsearch-input" data-param="checkout">
                </div>
                <?php endif; ?>

                <?php if ( isset( $vf['guests'] ) && ! empty( $guests_ranges ) ) : ?>
                <div class="olo-svsearch-field"<?php if ( ! empty( $field_widths['guests'] ) ) echo ' style="' . esc_attr( $field_widths['guests'] ) . '"'; ?>>
                    <label class="olo-svsearch-label"><?php echo esc_html( olo_t( 'Ospiti' ) ); ?></label>
                    <select class="olo-svsearch-select" data-param="guests">
                        <option value=""><?php echo esc_html( olo_t( 'Qualsiasi' ) ); ?></option>
                        <?php foreach ( $guests_ranges as $range ) :
                            $parts = explode( '-', $range );
                            if ( count( $parts ) !== 2 ) continue;
                            $min = intval( $parts[0] );
                            $max = intval( $parts[1] );
                            $label = $max >= 99 ? $min . '+' : $min . '-' . $max;
                        ?>
                        <option value="<?php echo esc_attr( $range ); ?>"><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <?php if ( isset( $vf['bedrooms'] ) ) : ?>
                <div class="olo-svsearch-field"<?php if ( ! empty( $field_widths['bedrooms'] ) ) echo ' style="' . esc_attr( $field_widths['bedrooms'] ) . '"'; ?>>
                    <label class="olo-svsearch-label"><?php echo esc_html( olo_t( 'Camere' ) ); ?></label>
                    <select class="olo-svsearch-select" data-param="bedrooms">
                        <option value=""><?php echo esc_html( olo_t( 'Qualsiasi' ) ); ?></option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4+">4+</option>
                    </select>
                </div>
                <?php endif; ?>

                <?php if ( isset( $vf['altitude'] ) && ! empty( $alt_ranges ) ) : ?>
                <div class="olo-svsearch-field"<?php if ( ! empty( $field_widths['altitude'] ) ) echo ' style="' . esc_attr( $field_widths['altitude'] ) . '"'; ?>>
                    <label class="olo-svsearch-label"><?php echo esc_html( olo_t( 'Altitudine' ) ); ?></label>
                    <select class="olo-svsearch-select" data-param="altitude">
                        <option value=""><?php echo esc_html( olo_t( 'Qualsiasi' ) ); ?></option>
                        <?php foreach ( $alt_ranges as $range ) :
                            $parts = explode( '-', $range );
                            if ( count( $parts ) !== 2 ) continue;
                            $min = intval( $parts[0] );
                            $max = intval( $parts[1] );
                            if ( $min === 0 ) $label = '< ' . number_format( $max, 0, ',', '.' ) . 'm';
                            elseif ( $max >= 9000 ) $label = '> ' . number_format( $min, 0, ',', '.' ) . 'm';
                            else $label = number_format( $min, 0, ',', '.' ) . ' - ' . number_format( $max, 0, ',', '.' ) . 'm';
                        ?>
                        <option value="<?php echo esc_attr( $range ); ?>"><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <?php if ( isset( $vf['type'] ) ) : ?>
                <div class="olo-svsearch-field"<?php if ( ! empty( $field_widths['type'] ) ) echo ' style="' . esc_attr( $field_widths['type'] ) . '"'; ?>>
                    <label class="olo-svsearch-label"><?php echo esc_html( olo_t( 'Tipologia' ) ); ?></label>
                    <select class="olo-svsearch-select" data-param="type">
                        <option value=""><?php echo esc_html( olo_t( 'Tutte' ) ); ?></option>
                        <option value="baita"><?php echo esc_html( olo_t( 'Baita' ) ); ?></option>
                        <option value="apartment"><?php echo esc_html( olo_t( 'Appartamento' ) ); ?></option>
                        <option value="room"><?php echo esc_html( olo_t( 'Camera' ) ); ?></option>
                        <option value="chalet"><?php echo esc_html( olo_t( 'Chalet' ) ); ?></option>
                    </select>
                </div>
                <?php endif; ?>

                <?php if ( isset( $vf['club'] ) && ! empty( $club_cats ) ) : ?>
                <div class="olo-svsearch-field"<?php if ( ! empty( $field_widths['club'] ) ) echo ' style="' . esc_attr( $field_widths['club'] ) . '"'; ?>>
                    <label class="olo-svsearch-label"><?php echo esc_html( olo_t( 'Club di Prodotto' ) ); ?></label>
                    <select class="olo-svsearch-select" data-param="club">
                        <option value=""><?php echo esc_html( olo_t( 'Tutti i club' ) ); ?></option>
                        <?php foreach ( $club_cats as $cc ) : ?>
                        <option value="<?php echo esc_attr( $cc ); ?>"><?php echo esc_html( $cc ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <?php if ( isset( $vf['amenities'] ) && ! empty( $amenities ) ) : ?>
                <div class="olo-svsearch-field olo-svsearch-field--amenities">
                    <label class="olo-svsearch-label"><?php echo esc_html( olo_t( 'Servizi' ) ); ?></label>
                    <div class="olo-svsearch-amenities">
                        <?php foreach ( $amenities as $slug ) :
                            $label = $amenity_labels[ $slug ] ?? ucfirst( $slug );
                        ?>
                        <label class="olo-svsearch-amenity">
                            <input type="checkbox" value="<?php echo esc_attr( $slug ); ?>" data-param="amenities">
                            <span><?php echo esc_html( $label ); ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <div class="olo-svsearch-actions">
                <button type="button" class="olo-svsearch-btn"
                    <?php if ( ! empty( $s['button_bg'] ) ) : ?>
                    style="background:<?php echo $this->safe_color_css( $s['button_bg'] ); ?>;color:<?php echo $this->safe_color_css( $s['button_color'] ); ?>;"
                    <?php endif; ?>
                >
                    <?php echo esc_html( $s['button_text'] ?: olo_t( 'Cerca' ) ); ?>
                </button>
            </div>

        </div>
        <?php
        return ob_get_clean();
    }

    private function get_club_categories() {
        if ( ! post_type_exists( 'olo_service' ) ) {
            return [];
        }
        global $wpdb;
        $cats = [];

        // New multi-club array: _olo_service_clubs (serialized)
        $rows = $wpdb->get_col(
            "SELECT pm.meta_value
             FROM {$wpdb->postmeta} pm
             INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
             WHERE pm.meta_key = '_olo_service_clubs'
               AND pm.meta_value != ''
               AND p.post_type = 'olo_service'
               AND p.post_status = 'publish'"
        );
        foreach ( $rows as $raw ) {
            $arr = maybe_unserialize( $raw );
            if ( is_array( $arr ) ) {
                foreach ( $arr as $club ) {
                    $cat = trim( $club['category'] ?? '' );
                    if ( $cat !== '' ) {
                        $cats[ $cat ] = true;
                    }
                }
            }
        }

        // Legacy: _olo_service_club_category
        $legacy = $wpdb->get_col(
            "SELECT DISTINCT pm.meta_value
             FROM {$wpdb->postmeta} pm
             INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
             WHERE pm.meta_key = '_olo_service_club_category'
               AND pm.meta_value != ''
               AND p.post_type = 'olo_service'
               AND p.post_status = 'publish'"
        );
        foreach ( $legacy as $val ) {
            $val = trim( $val );
            if ( $val !== '' ) {
                $cats[ $val ] = true;
            }
        }

        $result = array_keys( $cats );
        sort( $result );
        return $result;
    }

    private function get_valleys() {
        if ( ! post_type_exists( 'olo_service' ) ) {
            return [];
        }
        global $wpdb;
        $vals = $wpdb->get_col(
            "SELECT DISTINCT pm.meta_value
             FROM {$wpdb->postmeta} pm
             INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
             WHERE pm.meta_key = '_olo_service_valley'
               AND pm.meta_value != ''
               AND p.post_type = 'olo_service'
               AND p.post_status = 'publish'
             ORDER BY pm.meta_value ASC"
        );
        return $vals ?: [];
    }
}
