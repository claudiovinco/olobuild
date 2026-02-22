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
        'loc_filter_align'     => 'left',
        'loc_cluster'          => true,
        'loc_fit_bounds'       => true,
        'loc_default_zoom'     => '13',
        'loc_default_center'   => '41.9028, 12.4964',
        'loc_popup_show_image'   => true,
        'loc_popup_show_excerpt' => true,
        'loc_popup_show_link'    => true,
        'loc_max_locations'    => '100',
        'loc_tile_layer'       => 'osm',
        // Services mode
        'svc_booking_mode'         => 'accommodation',
        'svc_show_altitude_filter'  => true,
        'svc_altitude_ranges'       => '0-1000,1000-1500,1500-2000,2000-9999',
        'svc_show_locality_filter'  => true,
        'svc_show_guests_filter'    => true,
        'svc_guests_ranges'         => '1-2,3-5,6-8,9-99',
        'svc_show_price_filter'     => true,
        'svc_price_ranges'          => '0-100,100-150,150-200,200-9999',
        'svc_show_bedrooms_filter'  => true,
        'svc_show_amenities_filter' => true,
        'svc_amenities_list'        => 'wifi,fireplace,sauna,hottub,pets,ski,bbq,garden',
        'svc_filter_style'          => 'default',
        'svc_filter_position'       => 'top',
        'svc_tile_layer'           => 'positron',
        'svc_default_center'       => '46.07, 11.12',
        'svc_default_zoom'         => '10',
        'svc_fit_bounds'           => true,
        'svc_cluster'              => true,
        'svc_popup_show_image'     => true,
        'svc_popup_show_excerpt'   => true,
        'svc_popup_show_price'     => true,
        'svc_popup_show_altitude'  => true,
        'border_radius'        => '8',
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

        $mode = $s['mode'] ?? 'single';

        if ( $mode === 'dynamic_service' ) {
            return $this->render_dynamic_service( $s );
        }

        if ( $mode === 'locations' ) {
            return $this->render_locations( $s );
        }

        if ( $mode === 'services' ) {
            return $this->render_services( $s );
        }

        return $this->render_single( $s );
    }

    /**
     * Dynamic service mode: reads GPS from current olo_service post meta.
     */
    private function render_dynamic_service( $s ) {
        global $post;

        $lat = 0;
        $lng = 0;
        $title = '';

        if ( $post && $post->post_type === 'olo_service' ) {
            $lat = floatval( get_post_meta( $post->ID, '_olo_service_latitude', true ) );
            $lng = floatval( get_post_meta( $post->ID, '_olo_service_longitude', true ) );
            $title = get_the_title( $post->ID );
        }

        if ( ! $lat && ! $lng ) {
            return '<div style="padding:24px;text-align:center;color:#9ca3af;background:#f9fafb;border-radius:8px">'
                 . '<p style="margin:0">Nessuna coordinata GPS impostata per questo servizio.</p>'
                 . '</div>';
        }

        $zoom   = absint( $s['zoom'] ) ?: 13;
        $height = absint( $s['height'] ) ?: 400;
        $radius = absint( $s['border_radius'] ?? 8 );

        $src = "https://www.openstreetmap.org/export/embed.html?bbox="
             . ( $lng - 0.02 ) . ',' . ( $lat - 0.01 ) . ','
             . ( $lng + 0.02 ) . ',' . ( $lat + 0.01 )
             . "&layer=mapnik&marker=" . $lat . ',' . $lng;

        ob_start();
        ?>
        <div class="olo-map olo-map-dynamic" style="border-radius: <?php echo $radius; ?>px; overflow: hidden;">
            <iframe
                src="<?php echo esc_url( $src ); ?>"
                style="width: 100%; height: <?php echo $height; ?>px; border: 0;"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                title="<?php echo esc_attr( $title ); ?>"
            ></iframe>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Original single-address iframe mode.
     */
    private function render_single( $s ) {
        $address = $s['address'];
        $zoom    = absint( $s['zoom'] );
        $height  = absint( $s['height'] );
        $radius  = absint( $s['border_radius'] ?? 8 );

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
        <div class="olo-map" style="border-radius: <?php echo $radius; ?>px; overflow: hidden;">
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
        $radius = absint( $s['border_radius'] ?? 8 );
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
                $this->render_filters( $terms, $s['loc_filter_style'], $map_id, $s['loc_filter_align'] ?? 'left' );
            }
            ?>
            <div
                id="<?php echo esc_attr( $map_id ); ?>"
                class="olo-map-canvas"
                style="height: <?php echo $height; ?>px; border-radius: <?php echo $radius; ?>px; overflow: hidden;"
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
    private function render_filters( $terms, $style, $map_id, $align = 'left' ) {
        $align_cls = $align === 'center' ? ' olo-filter-center' : ( $align === 'right' ? ' olo-filter-right' : '' );
        ?>
        <div class="olo-map-filters<?php echo $align_cls; ?>" data-map-target="<?php echo esc_attr( $map_id ); ?>">
            <?php if ( $style === 'dropdown' ) : ?>
                <select class="olo-map-filter-select uk-select" data-filter-select>
                    <option value=""><?php esc_html_e( 'Tutti', 'olobuilder' ); ?></option>
                    <?php foreach ( $terms as $term ) : ?>
                        <option value="<?php echo esc_attr( $term['slug'] ); ?>">
                            <?php echo esc_html( $term['name'] ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php elseif ( $style === 'minimal' ) : ?>
                <button class="olo-map-filter-pill olo-map-filter-pill--minimal olo-map-filter-active" data-filter="">
                    <?php esc_html_e( 'Tutti', 'olobuilder' ); ?>
                </button>
                <?php foreach ( $terms as $term ) : ?>
                    <button class="olo-map-filter-pill olo-map-filter-pill--minimal" data-filter="<?php echo esc_attr( $term['slug'] ); ?>">
                        <?php echo esc_html( $term['name'] ); ?>
                    </button>
                <?php endforeach; ?>
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

    /**
     * Services mode: Leaflet map with markers from olo_service CPT.
     */
    private function render_services( $s ) {
        $height = absint( $s['height'] );
        $radius = absint( $s['border_radius'] ?? 8 );
        $map_id = 'olo-map-' . wp_unique_id();

        $locations = $this->query_services( $s );

        // Parse default center
        $center = [ 46.07, 11.12 ];
        $center_match = $this->parse_coords( $s['svc_default_center'] );
        if ( $center_match ) {
            $center = [ $center_match['lat'], $center_match['lng'] ];
        }

        $config = [
            'mode'           => 'services',
            'locations'      => $locations,
            'center'         => $center,
            'zoom'           => absint( $s['svc_default_zoom'] ),
            'tileLayer'      => $s['svc_tile_layer'],
            'cluster'        => (bool) $s['svc_cluster'],
            'fitBounds'      => (bool) $s['svc_fit_bounds'],
            'popupImage'     => (bool) $s['svc_popup_show_image'],
            'popupExcerpt'   => (bool) $s['svc_popup_show_excerpt'],
            'popupPrice'     => (bool) $s['svc_popup_show_price'],
            'popupAltitude'  => (bool) $s['svc_popup_show_altitude'],
            'popupLink'      => true,
        ];

        ob_start();
        ?>
        <?php
        $has_filters = ! empty( $locations ) && (
            ! empty( $s['svc_show_altitude_filter'] )
            || ! empty( $s['svc_show_locality_filter'] )
            || ! empty( $s['svc_show_guests_filter'] )
            || ! empty( $s['svc_show_price_filter'] )
            || ! empty( $s['svc_show_bedrooms_filter'] )
            || ! empty( $s['svc_show_amenities_filter'] )
        );
        $filter_style = $s['svc_filter_style'] ?? 'default';
        $filter_pos   = $s['svc_filter_position'] ?? 'top';
        $is_side      = in_array( $filter_pos, [ 'left', 'right' ], true );
        ?>
        <div class="olo-map olo-map-services<?php echo $is_side && $has_filters ? ' olo-map-layout-side olo-map-layout-' . esc_attr( $filter_pos ) : ''; ?>">
            <?php if ( $has_filters && ( $filter_pos === 'top' || $filter_pos === 'left' ) ) : ?>
                <?php $this->render_filter_bar( $filter_style, $s, $locations, $map_id ); ?>
            <?php endif; ?>
            <div class="olo-map-canvas-wrap"<?php echo $is_side ? ' style="flex:1;min-width:0"' : ''; ?>>
                <div
                    id="<?php echo esc_attr( $map_id ); ?>"
                    class="olo-map-canvas"
                    style="height: <?php echo $height; ?>px; border-radius: <?php echo $radius; ?>px; overflow: hidden;"
                    data-map-config="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"
                ></div>
            </div>
            <?php if ( $has_filters && ( $filter_pos === 'bottom' || $filter_pos === 'right' ) ) : ?>
                <?php $this->render_filter_bar( $filter_style, $s, $locations, $map_id ); ?>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Query olo_service posts with GPS coordinates.
     */
    private function query_services( $s ) {
        if ( ! post_type_exists( 'olo_service' ) ) {
            return [];
        }

        $args = [
            'post_type'      => 'olo_service',
            'posts_per_page' => 200,
            'post_status'    => 'publish',
            'no_found_rows'  => true,
        ];

        // Filter by service type if specified
        $booking_mode = $s['svc_booking_mode'] ?? '';
        if ( $booking_mode ) {
            $args['meta_query'] = [
                [
                    'key'   => '_olo_service_type',
                    'value' => $booking_mode,
                ],
            ];
        }

        $query = new WP_Query( $args );
        $locations = [];

        foreach ( $query->posts as $post ) {
            $lat = floatval( get_post_meta( $post->ID, '_olo_service_latitude', true ) );
            $lng = floatval( get_post_meta( $post->ID, '_olo_service_longitude', true ) );

            if ( $lat === 0.0 && $lng === 0.0 ) {
                continue;
            }

            $location = [
                'id'    => $post->ID,
                'title' => get_the_title( $post ),
                'lat'   => $lat,
                'lng'   => $lng,
                'url'   => get_permalink( $post->ID ),
            ];

            // Altitude
            $altitude = get_post_meta( $post->ID, '_olo_service_altitude', true );
            if ( $altitude !== '' && $altitude !== false ) {
                $location['altitude'] = intval( $altitude );
            }

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

            // Service-specific meta
            $meta_map = [
                '_olo_service_price'         => 'price',
                '_olo_service_capacity'      => 'capacity',
                '_olo_service_valley'        => 'valley',
                '_olo_service_bedrooms'      => 'bedrooms',
                '_olo_service_beds'          => 'beds',
                '_olo_service_bathrooms'     => 'bathrooms',
                '_olo_service_sqm'           => 'sqm',
                '_olo_service_type'          => 'service_type',
            ];

            foreach ( $meta_map as $meta_key => $json_key ) {
                $val = get_post_meta( $post->ID, $meta_key, true );
                if ( $val !== '' && $val !== false ) {
                    $location[ $json_key ] = is_numeric( $val ) ? floatval( $val ) : $val;
                }
            }

            // Amenities (stored as serialized array)
            $raw_amenities = get_post_meta( $post->ID, '_olo_service_amenities', true );
            if ( $raw_amenities ) {
                $amenities = maybe_unserialize( $raw_amenities );
                if ( is_array( $amenities ) && ! empty( $amenities ) ) {
                    $location['amenities'] = array_values( $amenities );
                }
            }

            $locations[] = $location;
        }

        wp_reset_postdata();

        return $locations;
    }

    /**
     * Render the filter bar (dispatches to compact or pills based on style).
     */
    private function render_filter_bar( $filter_style, $s, $locations, $map_id ) {
        if ( $filter_style === 'compact' ) :
        ?>
        <div class="olo-map-filters-bar olo-map-filters-bar--compact" data-map-target="<?php echo esc_attr( $map_id ); ?>">
            <?php $this->render_compact_filters( $s, $locations, $map_id ); ?>
            <div class="olo-map-svc-filter-counter" data-map-target="<?php echo esc_attr( $map_id ); ?>">
                <span data-svc-count><?php echo count( $locations ); ?></span> / <?php echo count( $locations ); ?> risultati
            </div>
        </div>
        <?php else : ?>
        <div class="olo-map-filters-bar olo-map-filters-bar--<?php echo esc_attr( $filter_style ); ?>" data-map-target="<?php echo esc_attr( $map_id ); ?>">
            <?php
            if ( ! empty( $s['svc_show_altitude_filter'] ) ) {
                $this->render_altitude_filter( $s['svc_altitude_ranges'], $map_id );
            }
            if ( ! empty( $s['svc_show_locality_filter'] ) ) {
                $this->render_locality_filter( $locations, $map_id );
            }
            if ( ! empty( $s['svc_show_guests_filter'] ) ) {
                $this->render_range_filter( 'guests', $s['svc_guests_ranges'], $map_id );
            }
            if ( ! empty( $s['svc_show_price_filter'] ) ) {
                $this->render_range_filter( 'price', $s['svc_price_ranges'], $map_id );
            }
            if ( ! empty( $s['svc_show_bedrooms_filter'] ) ) {
                $this->render_bedrooms_filter( $locations, $map_id );
            }
            if ( ! empty( $s['svc_show_amenities_filter'] ) ) {
                $this->render_amenities_filter( $s['svc_amenities_list'], $map_id );
            }
            ?>
            <div class="olo-map-svc-filter-counter" data-map-target="<?php echo esc_attr( $map_id ); ?>">
                <span data-svc-count><?php echo count( $locations ); ?></span> / <?php echo count( $locations ); ?> risultati
            </div>
        </div>
        <?php
        endif;
    }

    /**
     * Render compact dropdown filters.
     */
    private function render_compact_filters( $s, $locations, $map_id ) {
        ?>
        <div class="olo-map-compact-row">
        <?php
        // Altitude
        if ( ! empty( $s['svc_show_altitude_filter'] ) ) {
            $ranges = array_filter( array_map( 'trim', explode( ',', $s['svc_altitude_ranges'] ) ) );
            if ( $ranges ) {
                ?>
                <select class="olo-map-compact-select" data-filter-type="altitude" data-map-target="<?php echo esc_attr( $map_id ); ?>">
                    <option value="">Altitudine</option>
                    <?php foreach ( $ranges as $range ) :
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
                <?php
            }
        }

        // Locality / Valley
        if ( ! empty( $s['svc_show_locality_filter'] ) ) {
            $localities = [];
            foreach ( $locations as $loc ) {
                if ( ! empty( $loc['valley'] ) ) $localities[ $loc['valley'] ] = true;
            }
            if ( $localities ) {
                $names = array_keys( $localities );
                sort( $names );
                ?>
                <select class="olo-map-compact-select" data-filter-type="valley" data-map-target="<?php echo esc_attr( $map_id ); ?>">
                    <option value="">Localit&agrave;</option>
                    <?php foreach ( $names as $n ) : ?>
                    <option value="<?php echo esc_attr( $n ); ?>"><?php echo esc_html( $n ); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php
            }
        }

        // Guests
        if ( ! empty( $s['svc_show_guests_filter'] ) ) {
            $ranges = array_filter( array_map( 'trim', explode( ',', $s['svc_guests_ranges'] ) ) );
            if ( $ranges ) {
                ?>
                <select class="olo-map-compact-select" data-filter-type="guests" data-map-target="<?php echo esc_attr( $map_id ); ?>">
                    <option value="">Ospiti</option>
                    <?php foreach ( $ranges as $range ) :
                        $parts = explode( '-', $range );
                        if ( count( $parts ) !== 2 ) continue;
                        $min = intval( $parts[0] );
                        $max = intval( $parts[1] );
                        $label = $max >= 99 ? $min . '+' : $min . '-' . $max;
                    ?>
                    <option value="<?php echo esc_attr( $range ); ?>"><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php
            }
        }

        // Price
        if ( ! empty( $s['svc_show_price_filter'] ) ) {
            $ranges = array_filter( array_map( 'trim', explode( ',', $s['svc_price_ranges'] ) ) );
            if ( $ranges ) {
                ?>
                <select class="olo-map-compact-select" data-filter-type="price" data-map-target="<?php echo esc_attr( $map_id ); ?>">
                    <option value=""><?php echo esc_html( olo_t( 'Prezzo / notte' ) ); ?></option>
                    <?php foreach ( $ranges as $range ) :
                        $parts = explode( '-', $range );
                        if ( count( $parts ) !== 2 ) continue;
                        $min = intval( $parts[0] );
                        $max = intval( $parts[1] );
                        if ( $min === 0 ) $label = '< €' . $max;
                        elseif ( $max >= 9000 ) $label = '€' . $min . '+';
                        else $label = '€' . $min . ' - €' . $max;
                    ?>
                    <option value="<?php echo esc_attr( $range ); ?>"><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php
            }
        }

        // Bedrooms
        if ( ! empty( $s['svc_show_bedrooms_filter'] ) ) {
            $has_bedrooms = false;
            foreach ( $locations as $loc ) {
                if ( isset( $loc['bedrooms'] ) ) { $has_bedrooms = true; break; }
            }
            if ( $has_bedrooms ) {
                ?>
                <select class="olo-map-compact-select" data-filter-type="bedrooms" data-map-target="<?php echo esc_attr( $map_id ); ?>">
                    <option value="">Camere</option>
                    <option value="1">1 camera</option>
                    <option value="2">2 camere</option>
                    <option value="3">3 camere</option>
                    <option value="4+">4+ camere</option>
                </select>
                <?php
            }
        }
        ?>
        </div>
        <?php
    }

    /**
     * Render altitude filter pills.
     */
    private function render_altitude_filter( $ranges_str, $map_id ) {
        $ranges = array_filter( array_map( 'trim', explode( ',', $ranges_str ) ) );
        if ( empty( $ranges ) ) {
            return;
        }

        $labels = [];
        foreach ( $ranges as $range ) {
            $parts = explode( '-', $range );
            if ( count( $parts ) !== 2 ) continue;
            $min = intval( $parts[0] );
            $max = intval( $parts[1] );

            if ( $min === 0 ) {
                $label = '< ' . number_format( $max, 0, ',', '.' ) . 'm';
            } elseif ( $max >= 9000 ) {
                $label = '> ' . number_format( $min, 0, ',', '.' ) . 'm';
            } else {
                $label = number_format( $min, 0, ',', '.' ) . ' - ' . number_format( $max, 0, ',', '.' ) . 'm';
            }
            $labels[] = [ 'range' => $range, 'label' => $label ];
        }
        ?>
        <div class="olo-map-svc-filter-group" data-filter-type="altitude" data-map-target="<?php echo esc_attr( $map_id ); ?>">
            <span class="olo-map-svc-filter-label">Altitudine</span>
            <div class="olo-map-svc-filter-pills">
                <button class="olo-map-filter-pill olo-map-filter-active" data-svc-filter="">Tutte</button>
                <?php foreach ( $labels as $item ) : ?>
                    <button class="olo-map-filter-pill" data-svc-filter="<?php echo esc_attr( $item['range'] ); ?>">
                        <?php echo esc_html( $item['label'] ); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render locality filter pills (dynamic from unique localities).
     */
    private function render_locality_filter( $locations, $map_id ) {
        $localities = [];
        foreach ( $locations as $loc ) {
            if ( ! empty( $loc['valley'] ) ) {
                $localities[ $loc['valley'] ] = true;
            }
        }
        if ( empty( $localities ) ) {
            return;
        }
        $locality_names = array_keys( $localities );
        sort( $locality_names );
        ?>
        <div class="olo-map-svc-filter-group" data-filter-type="valley" data-map-target="<?php echo esc_attr( $map_id ); ?>">
            <span class="olo-map-svc-filter-label">Localit&agrave;</span>
            <div class="olo-map-svc-filter-pills">
                <button class="olo-map-filter-pill olo-map-filter-active" data-svc-filter="">Tutte</button>
                <?php foreach ( $locality_names as $loc_name ) : ?>
                    <button class="olo-map-filter-pill" data-svc-filter="<?php echo esc_attr( $loc_name ); ?>">
                        <?php echo esc_html( $loc_name ); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render range filter pills (reusable for guests and price).
     */
    private function render_range_filter( $type, $ranges_str, $map_id ) {
        $ranges = array_filter( array_map( 'trim', explode( ',', $ranges_str ) ) );
        if ( empty( $ranges ) ) {
            return;
        }

        $labels_map = [
            'guests' => olo_t( 'Ospiti' ),
            'price'  => olo_t( 'Prezzo / notte' ),
        ];
        $group_label = $labels_map[ $type ] ?? ucfirst( $type );

        $labels = [];
        foreach ( $ranges as $range ) {
            $parts = explode( '-', $range );
            if ( count( $parts ) !== 2 ) continue;
            $min = intval( $parts[0] );
            $max = intval( $parts[1] );

            if ( $type === 'price' ) {
                if ( $min === 0 ) {
                    $label = '< &euro;' . $max;
                } elseif ( $max >= 9000 ) {
                    $label = '&euro;' . $min . '+';
                } else {
                    $label = '&euro;' . $min . ' - &euro;' . $max;
                }
            } else {
                // guests
                if ( $max >= 99 ) {
                    $label = $min . '+';
                } else {
                    $label = $min . '-' . $max;
                }
            }
            $labels[] = [ 'range' => $range, 'label' => $label ];
        }
        ?>
        <div class="olo-map-svc-filter-group" data-filter-type="<?php echo esc_attr( $type ); ?>" data-map-target="<?php echo esc_attr( $map_id ); ?>">
            <span class="olo-map-svc-filter-label"><?php echo esc_html( $group_label ); ?></span>
            <div class="olo-map-svc-filter-pills">
                <button class="olo-map-filter-pill olo-map-filter-active" data-svc-filter="">Tutti</button>
                <?php foreach ( $labels as $item ) : ?>
                    <button class="olo-map-filter-pill" data-svc-filter="<?php echo esc_attr( $item['range'] ); ?>">
                        <?php echo $item['label']; ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render bedrooms filter pills (1, 2, 3, 4+).
     */
    private function render_bedrooms_filter( $locations, $map_id ) {
        $has_bedrooms = false;
        foreach ( $locations as $loc ) {
            if ( isset( $loc['bedrooms'] ) ) {
                $has_bedrooms = true;
                break;
            }
        }
        if ( ! $has_bedrooms ) {
            return;
        }
        ?>
        <div class="olo-map-svc-filter-group" data-filter-type="bedrooms" data-map-target="<?php echo esc_attr( $map_id ); ?>">
            <span class="olo-map-svc-filter-label">Camere</span>
            <div class="olo-map-svc-filter-pills">
                <button class="olo-map-filter-pill olo-map-filter-active" data-svc-filter="">Tutte</button>
                <button class="olo-map-filter-pill" data-svc-filter="1">1</button>
                <button class="olo-map-filter-pill" data-svc-filter="2">2</button>
                <button class="olo-map-filter-pill" data-svc-filter="3">3</button>
                <button class="olo-map-filter-pill" data-svc-filter="4+">4+</button>
            </div>
        </div>
        <?php
    }

    /**
     * Render amenities filter pills (multi-select toggle).
     */
    private function render_amenities_filter( $amenities_str, $map_id ) {
        $amenities = array_filter( array_map( 'trim', explode( ',', $amenities_str ) ) );
        if ( empty( $amenities ) ) {
            return;
        }

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
                'bikes' => 'Biciclette', 'elevator' => 'Ascensore', 'accessible' => 'Accessibile',
            ];

        $amenity_icons = [
            'wifi'      => '&#128246;',
            'fireplace' => '&#128293;',
            'sauna'     => '&#9832;',
            'hottub'    => '&#128704;',
            'pets'      => '&#128054;',
            'ski'       => '&#9975;',
            'bbq'       => '&#127830;',
            'garden'    => '&#127793;',
        ];
        ?>
        <div class="olo-map-svc-filter-group olo-map-svc-filter-group--amenities" data-filter-type="amenities" data-map-target="<?php echo esc_attr( $map_id ); ?>">
            <span class="olo-map-svc-filter-label"><?php echo esc_html( olo_t( 'Servizi' ) ); ?></span>
            <div class="olo-map-svc-filter-pills">
                <?php foreach ( $amenities as $slug ) :
                    $label = $amenity_labels[ $slug ] ?? ucfirst( $slug );
                    $icon  = $amenity_icons[ $slug ] ?? '';
                ?>
                    <button class="olo-map-filter-pill olo-map-filter-pill--amenity" data-svc-filter="<?php echo esc_attr( $slug ); ?>">
                        <?php if ( $icon ) : ?><span class="olo-map-amenity-icon"><?php echo $icon; ?></span><?php endif; ?>
                        <?php echo esc_html( $label ); ?>
                    </button>
                <?php endforeach; ?>
            </div>
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
