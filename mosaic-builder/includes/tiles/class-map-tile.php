<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Map_Tile extends Olo_Tile_Base {

    protected $type     = 'map';
    protected $name     = 'Mappa';
    protected $icon     = 'dashicons-location';
    protected $category = 'media';
    protected $defaults = [
        'mode'                 => 'single',
        'address'              => 'Roma, Italia',
        'zoom'                 => '13',
        'height'               => '400',
        'loc_post_type'        => 'location',
        'loc_osm_field'        => 'location_map',
        'loc_taxonomy'         => '',
        'loc_show_filters'     => false,
        'loc_filter_style'     => 'pills',
        'loc_cluster'          => true,
        'loc_fit_bounds'       => true,
        'loc_default_zoom'     => '13',
        'loc_default_center'   => '41.9028, 12.4964',
        'loc_popup_show_image'   => true,
        'loc_popup_show_excerpt' => true,
        'loc_popup_show_link'    => true,
        'loc_max_locations'    => '100',
        'loc_tile_layer'       => 'osm',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'address', 'type' => 'text',  'label' => 'Indirizzo o coordinate' ],
            [ 'key' => 'zoom',    'type' => 'range', 'label' => 'Livello zoom', 'min' => 1, 'max' => 19, 'step' => 1 ],
            [ 'key' => 'height',  'type' => 'range', 'label' => 'Altezza (px)', 'min' => 150, 'max' => 800, 'step' => 10 ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        if ( ( $s['mode'] ?? 'single' ) === 'locations' ) {
            return $this->render_locations( $s );
        }

        return $this->render_single( $s );
    }

    /**
     * Original single-address iframe mode.
     */
    private function render_single( $s ) {
        $address = $s['address'];
        $zoom    = absint( $s['zoom'] );
        $height  = absint( $s['height'] );

        $coords = $this->parse_coords( $address );

        if ( $coords ) {
            $src = "https://www.openstreetmap.org/export/embed.html?bbox="
                 . ( $coords['lng'] - 0.02 ) . ',' . ( $coords['lat'] - 0.01 ) . ','
                 . ( $coords['lng'] + 0.02 ) . ',' . ( $coords['lat'] + 0.01 )
                 . "&layer=mapnik&marker=" . $coords['lat'] . ',' . $coords['lng'];
        } else {
            $bbox = $this->address_to_bbox( $address, $zoom );
            $src = "https://www.openstreetmap.org/export/embed.html?bbox=" . esc_attr( $bbox ) . "&layer=mapnik";
        }

        ob_start();
        ?>
        <div class="olo-map" style="border-radius: 8px; overflow: hidden;">
            <iframe
                src="<?php echo esc_url( $src ); ?>"
                style="width: 100%; height: <?php echo $height; ?>px; border: 0;"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Locations mode: Leaflet map with markers from CPT.
     */
    private function render_locations( $s ) {
        $height = absint( $s['height'] );
        $map_id = 'olo-map-' . wp_unique_id();

        $locations = $this->query_locations( $s );
        $terms     = $this->get_taxonomy_terms( $s, $locations );

        // Parse default center
        $center = [ 41.9028, 12.4964 ];
        $center_match = $this->parse_coords( $s['loc_default_center'] );
        if ( $center_match ) {
            $center = [ $center_match['lat'], $center_match['lng'] ];
        }

        $config = [
            'locations'  => $locations,
            'center'     => $center,
            'zoom'       => absint( $s['loc_default_zoom'] ),
            'tileLayer'  => $s['loc_tile_layer'],
            'cluster'    => (bool) $s['loc_cluster'],
            'fitBounds'  => (bool) $s['loc_fit_bounds'],
            'popupImage'   => (bool) $s['loc_popup_show_image'],
            'popupExcerpt' => (bool) $s['loc_popup_show_excerpt'],
            'popupLink'    => (bool) $s['loc_popup_show_link'],
        ];

        ob_start();
        ?>
        <div class="olo-map olo-map-locations">
            <?php
            if ( ! empty( $s['loc_show_filters'] ) && ! empty( $terms ) ) {
                $this->render_filters( $terms, $s['loc_filter_style'], $map_id );
            }
            ?>
            <div
                id="<?php echo esc_attr( $map_id ); ?>"
                class="olo-map-canvas"
                style="height: <?php echo $height; ?>px; border-radius: 8px; overflow: hidden;"
                data-map-config="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"
            ></div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Query locations from CPT using ACF OSM field.
     */
    private function query_locations( $s ) {
        $post_type = sanitize_key( $s['loc_post_type'] );
        $osm_field = sanitize_key( $s['loc_osm_field'] );
        $max       = min( 500, absint( $s['loc_max_locations'] ) );

        if ( ! post_type_exists( $post_type ) ) {
            return [];
        }

        $query = new WP_Query( [
            'post_type'      => $post_type,
            'posts_per_page' => $max,
            'post_status'    => 'publish',
            'no_found_rows'  => true,
        ] );

        $locations = [];
        $taxonomy  = sanitize_key( $s['loc_taxonomy'] );

        foreach ( $query->posts as $post ) {
            // Read raw meta to get the array (get_field may return rendered HTML)
            $field_value = get_post_meta( $post->ID, $osm_field, true );

            if ( empty( $field_value ) || ! is_array( $field_value ) || ! isset( $field_value['lat'], $field_value['lng'] ) ) {
                continue;
            }

            $lat = floatval( $field_value['lat'] );
            $lng = floatval( $field_value['lng'] );

            if ( $lat === 0.0 && $lng === 0.0 ) {
                continue;
            }

            $location = [
                'id'      => $post->ID,
                'title'   => get_the_title( $post ),
                'lat'     => $lat,
                'lng'     => $lng,
                'address' => $field_value['address'] ?? '',
            ];

            // Thumbnail
            $thumb = get_the_post_thumbnail_url( $post->ID, 'medium' );
            if ( $thumb ) {
                $location['image'] = $thumb;
            }

            // Excerpt
            $excerpt = has_excerpt( $post->ID )
                ? get_the_excerpt( $post->ID )
                : wp_trim_words( $post->post_content, 20, '&hellip;' );
            if ( $excerpt ) {
                $location['excerpt'] = $excerpt;
            }

            // Permalink
            $location['url'] = get_permalink( $post->ID );

            // Extra meta fields
            $meta_fields = [
                'location_phone'       => 'phone',
                'location_email'       => 'email',
                'location_website'     => 'website',
                'location_hours'       => 'hours',
                'location_price_range' => 'price',
                'location_ticket_url'  => 'ticket_url',
            ];
            foreach ( $meta_fields as $meta_key => $json_key ) {
                $val = get_post_meta( $post->ID, $meta_key, true );
                if ( $val ) {
                    $location[ $json_key ] = $val;
                }
            }
            $rating = get_post_meta( $post->ID, 'location_rating', true );
            if ( $rating ) {
                $location['rating'] = floatval( $rating );
            }
            // Accessibility as array
            $access = get_post_meta( $post->ID, 'location_accessibility', false );
            if ( ! empty( $access ) ) {
                $location['accessibility'] = $access;
            }

            // Gallery (first 4 for popup strip — thumb + full URL)
            $gallery_ids = get_post_meta( $post->ID, 'location_gallery', true );
            if ( ! empty( $gallery_ids ) && is_array( $gallery_ids ) ) {
                $gallery_items = [];
                foreach ( array_slice( $gallery_ids, 0, 4 ) as $att_id ) {
                    $thumb = wp_get_attachment_image_url( $att_id, 'thumbnail' );
                    $full  = wp_get_attachment_image_url( $att_id, 'large' );
                    if ( $thumb && $full ) {
                        $gallery_items[] = [ 'thumb' => $thumb, 'full' => $full ];
                    }
                }
                if ( $gallery_items ) {
                    $location['gallery']       = $gallery_items;
                    $location['gallery_count'] = count( $gallery_ids );
                }
            }

            // Rental fields (only if present)
            $rental_fields = [
                'rental_bedrooms'    => 'bedrooms',
                'rental_bathrooms'   => 'bathrooms',
                'rental_guests'      => 'guests',
                'rental_sqm'         => 'sqm',
                'rental_price_night' => 'price_night',
                'rental_price_week'  => 'price_week',
                'rental_booking_url' => 'booking_url',
                'rental_availability'=> 'availability',
            ];
            foreach ( $rental_fields as $meta_key => $json_key ) {
                $val = get_post_meta( $post->ID, $meta_key, true );
                if ( $val !== '' && $val !== false ) {
                    $location[ $json_key ] = is_numeric( $val ) ? floatval( $val ) : $val;
                }
            }

            // Taxonomy terms + marker color
            if ( $taxonomy && taxonomy_exists( $taxonomy ) ) {
                $post_terms = get_the_terms( $post->ID, $taxonomy );
                if ( $post_terms && ! is_wp_error( $post_terms ) ) {
                    $location['terms'] = wp_list_pluck( $post_terms, 'slug' );
                    // Use marker_color from first term
                    $color = get_term_meta( $post_terms[0]->term_id, 'marker_color', true );
                    if ( $color ) {
                        $location['color'] = $color;
                    }
                }
            }

            $locations[] = $location;
        }

        wp_reset_postdata();

        return $locations;
    }

    /**
     * Collect unique taxonomy terms from queried locations.
     */
    private function get_taxonomy_terms( $s, $locations ) {
        $taxonomy = sanitize_key( $s['loc_taxonomy'] );
        if ( ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
            return [];
        }

        $terms = get_terms( [
            'taxonomy'   => $taxonomy,
            'hide_empty' => true,
        ] );

        if ( is_wp_error( $terms ) || empty( $terms ) ) {
            return [];
        }

        $result = [];
        foreach ( $terms as $term ) {
            $entry = [
                'slug' => $term->slug,
                'name' => $term->name,
            ];
            $color = get_term_meta( $term->term_id, 'marker_color', true );
            if ( $color ) {
                $entry['color'] = $color;
            }
            $result[] = $entry;
        }

        return $result;
    }

    /**
     * Render filter UI (pills or dropdown).
     */
    private function render_filters( $terms, $style, $map_id ) {
        ?>
        <div class="olo-map-filters" data-map-target="<?php echo esc_attr( $map_id ); ?>">
            <?php if ( $style === 'dropdown' ) : ?>
                <select class="olo-map-filter-select uk-select" data-filter-select>
                    <option value=""><?php esc_html_e( 'Tutti', 'olobuilder' ); ?></option>
                    <?php foreach ( $terms as $term ) : ?>
                        <option value="<?php echo esc_attr( $term['slug'] ); ?>">
                            <?php echo esc_html( $term['name'] ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php else : ?>
                <button class="olo-map-filter-pill olo-map-filter-active" data-filter="">
                    <?php esc_html_e( 'Tutti', 'olobuilder' ); ?>
                </button>
                <?php foreach ( $terms as $term ) : ?>
                    <button class="olo-map-filter-pill" data-filter="<?php echo esc_attr( $term['slug'] ); ?>">
                        <?php if ( ! empty( $term['color'] ) ) : ?>
                            <span class="olo-map-filter-dot" style="background:<?php echo esc_attr( $term['color'] ); ?>"></span>
                        <?php endif; ?>
                        <?php echo esc_html( $term['name'] ); ?>
                    </button>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php
    }

    private function parse_coords( $address ) {
        if ( preg_match( '/^(-?\d+\.?\d*)\s*,\s*(-?\d+\.?\d*)$/', trim( $address ), $m ) ) {
            return [ 'lat' => floatval( $m[1] ), 'lng' => floatval( $m[2] ) ];
        }
        return null;
    }

    private function address_to_bbox( $address, $zoom ) {
        $lat = 41.9028;
        $lng = 12.4964;
        $delta = 0.05 / max( 1, $zoom / 10 );

        return ( $lng - $delta ) . ',' . ( $lat - $delta ) . ',' . ( $lng + $delta ) . ',' . ( $lat + $delta );
    }
}
