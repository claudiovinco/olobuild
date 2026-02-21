<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Rest_Events {

    public function init() {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    public function register_routes() {
        // Public: list events (FullCalendar feed)
        register_rest_route( 'olo-calendar/v1', '/events', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_events' ],
                'permission_callback' => '__return_true',
                'args'                => [
                    'start'      => [ 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ],
                    'end'        => [ 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ],
                    'categories' => [ 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ],
                ],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'create_event' ],
                'permission_callback' => [ $this, 'can_edit' ],
            ],
        ] );

        // Single event
        register_rest_route( 'olo-calendar/v1', '/events/(?P<id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_event' ],
                'permission_callback' => '__return_true',
            ],
            [
                'methods'             => 'PUT,PATCH',
                'callback'            => [ $this, 'update_event' ],
                'permission_callback' => [ $this, 'can_edit' ],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [ $this, 'delete_event' ],
                'permission_callback' => [ $this, 'can_edit' ],
            ],
        ] );

        // Categories
        register_rest_route( 'olo-calendar/v1', '/categories', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_categories' ],
            'permission_callback' => '__return_true',
        ] );
    }

    public function can_edit() {
        return current_user_can( 'edit_posts' );
    }

    /**
     * GET /events — Public feed for FullCalendar.
     */
    public function get_events( $request ) {
        $start = $request->get_param( 'start' );
        $end   = $request->get_param( 'end' );
        $cats  = $request->get_param( 'categories' );

        $args = [
            'post_type'      => 'olo_event',
            'post_status'    => 'publish',
            'posts_per_page' => 500,
            'meta_key'       => '_olo_event_start_date',
            'orderby'        => 'meta_value',
            'order'          => 'ASC',
        ];

        if ( $start && $end ) {
            $args['meta_query'] = [
                'relation' => 'AND',
                [
                    'key'     => '_olo_event_start_date',
                    'value'   => substr( $end, 0, 10 ),
                    'compare' => '<=',
                    'type'    => 'DATE',
                ],
                [
                    'relation' => 'OR',
                    [
                        'key'     => '_olo_event_end_date',
                        'value'   => substr( $start, 0, 10 ),
                        'compare' => '>=',
                        'type'    => 'DATE',
                    ],
                    [
                        'key'     => '_olo_event_end_date',
                        'value'   => '',
                        'compare' => '=',
                    ],
                    [
                        'key'     => '_olo_event_end_date',
                        'compare' => 'NOT EXISTS',
                    ],
                ],
            ];
        }

        if ( $cats ) {
            $cat_slugs = array_map( 'trim', explode( ',', $cats ) );
            $args['tax_query'] = [
                [
                    'taxonomy' => 'olo_event_cat',
                    'field'    => 'slug',
                    'terms'    => $cat_slugs,
                ],
            ];
        }

        $query  = new WP_Query( $args );
        $events = [];
        foreach ( $query->posts as $post ) {
            $events[] = $this->format_event( $post );
        }
        return new WP_REST_Response( $events, 200 );
    }

    /**
     * GET /events/{id}
     */
    public function get_event( $request ) {
        $post = get_post( $request['id'] );
        if ( ! $post || $post->post_type !== 'olo_event' || $post->post_status !== 'publish' ) {
            return new WP_REST_Response( [ 'message' => 'Evento non trovato.' ], 404 );
        }
        return new WP_REST_Response( $this->format_event( $post, true ), 200 );
    }

    /**
     * POST /events — Create event.
     */
    public function create_event( $request ) {
        $params = $request->get_json_params();

        $title = sanitize_text_field( $params['title'] ?? '' );
        if ( empty( $title ) ) {
            return new WP_REST_Response( [ 'message' => 'Il titolo è obbligatorio.' ], 400 );
        }

        $post_id = wp_insert_post( [
            'post_type'    => 'olo_event',
            'post_title'   => $title,
            'post_content' => wp_kses_post( $params['description'] ?? '' ),
            'post_excerpt' => sanitize_textarea_field( $params['excerpt'] ?? '' ),
            'post_status'  => 'publish',
        ] );

        if ( is_wp_error( $post_id ) ) {
            return new WP_REST_Response( [ 'message' => $post_id->get_error_message() ], 500 );
        }

        $this->save_event_meta( $post_id, $params );

        return new WP_REST_Response( $this->format_event( get_post( $post_id ), true ), 201 );
    }

    /**
     * PUT /events/{id} — Update event.
     */
    public function update_event( $request ) {
        $post = get_post( $request['id'] );
        if ( ! $post || $post->post_type !== 'olo_event' ) {
            return new WP_REST_Response( [ 'message' => 'Evento non trovato.' ], 404 );
        }

        $params = $request->get_json_params();

        $update = [ 'ID' => $post->ID ];
        if ( isset( $params['title'] ) )       $update['post_title']   = sanitize_text_field( $params['title'] );
        if ( isset( $params['description'] ) )  $update['post_content'] = wp_kses_post( $params['description'] );
        if ( isset( $params['excerpt'] ) )      $update['post_excerpt'] = sanitize_textarea_field( $params['excerpt'] );

        wp_update_post( $update );
        $this->save_event_meta( $post->ID, $params );

        return new WP_REST_Response( $this->format_event( get_post( $post->ID ), true ), 200 );
    }

    /**
     * DELETE /events/{id}
     */
    public function delete_event( $request ) {
        $post = get_post( $request['id'] );
        if ( ! $post || $post->post_type !== 'olo_event' ) {
            return new WP_REST_Response( [ 'message' => 'Evento non trovato.' ], 404 );
        }

        wp_trash_post( $post->ID );
        return new WP_REST_Response( [ 'success' => true, 'message' => 'Evento eliminato.' ], 200 );
    }

    /**
     * GET /categories
     */
    public function get_categories( $request ) {
        $terms = get_terms( [
            'taxonomy'   => 'olo_event_cat',
            'hide_empty' => false,
        ] );

        if ( is_wp_error( $terms ) ) {
            return new WP_REST_Response( [], 200 );
        }

        $result = [];
        foreach ( $terms as $term ) {
            $result[] = [
                'id'    => $term->term_id,
                'slug'  => $term->slug,
                'name'  => $term->name,
                'color' => get_term_meta( $term->term_id, '_event_color', true ) ?: '',
                'count' => $term->count,
            ];
        }
        return new WP_REST_Response( $result, 200 );
    }

    /* ── Helpers ── */

    private function save_event_meta( $post_id, $params ) {
        $text_map = [
            'start_date'  => '_olo_event_start_date',
            'start_time'  => '_olo_event_start_time',
            'end_date'    => '_olo_event_end_date',
            'end_time'    => '_olo_event_end_time',
            'location'    => '_olo_event_location',
        ];
        foreach ( $text_map as $key => $meta_key ) {
            if ( isset( $params[ $key ] ) ) {
                update_post_meta( $post_id, $meta_key, sanitize_text_field( $params[ $key ] ) );
            }
        }

        $url_map = [
            'location_url' => '_olo_event_location_url',
            'event_url'    => '_olo_event_url',
        ];
        foreach ( $url_map as $key => $meta_key ) {
            if ( isset( $params[ $key ] ) ) {
                update_post_meta( $post_id, $meta_key, esc_url_raw( $params[ $key ] ) );
            }
        }

        if ( isset( $params['color'] ) ) {
            update_post_meta( $post_id, '_olo_event_color', sanitize_hex_color( $params['color'] ) );
        }

        if ( isset( $params['all_day'] ) ) {
            update_post_meta( $post_id, '_olo_event_all_day', $params['all_day'] ? '1' : '' );
        }

        // Category
        if ( isset( $params['category_id'] ) ) {
            $cat_id = absint( $params['category_id'] );
            if ( $cat_id ) {
                wp_set_post_terms( $post_id, [ $cat_id ], 'olo_event_cat' );
            } else {
                wp_set_post_terms( $post_id, [], 'olo_event_cat' );
            }
        }
    }

    private function format_event( $post, $full = false ) {
        $start_date = get_post_meta( $post->ID, '_olo_event_start_date', true );
        $start_time = get_post_meta( $post->ID, '_olo_event_start_time', true );
        $end_date   = get_post_meta( $post->ID, '_olo_event_end_date', true );
        $end_time   = get_post_meta( $post->ID, '_olo_event_end_time', true );
        $all_day    = get_post_meta( $post->ID, '_olo_event_all_day', true );
        $location   = get_post_meta( $post->ID, '_olo_event_location', true );
        $loc_url    = get_post_meta( $post->ID, '_olo_event_location_url', true );
        $color      = get_post_meta( $post->ID, '_olo_event_color', true );
        $event_url  = get_post_meta( $post->ID, '_olo_event_url', true );

        $start_iso = $start_date;
        if ( ! $all_day && $start_time ) $start_iso .= 'T' . $start_time;

        $end_iso = '';
        if ( $end_date ) {
            $end_iso = $end_date;
            if ( ! $all_day && $end_time ) $end_iso .= 'T' . $end_time;
            elseif ( $all_day ) $end_iso = date( 'Y-m-d', strtotime( $end_date . ' +1 day' ) );
        }

        if ( empty( $color ) ) {
            $terms = wp_get_post_terms( $post->ID, 'olo_event_cat' );
            if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
                $cat_color = get_term_meta( $terms[0]->term_id, '_event_color', true );
                if ( $cat_color ) $color = $cat_color;
            }
        }

        $categories = [];
        $category_id = 0;
        $terms = wp_get_post_terms( $post->ID, 'olo_event_cat' );
        if ( ! is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
                $categories[] = [
                    'id'    => $term->term_id,
                    'slug'  => $term->slug,
                    'name'  => $term->name,
                    'color' => get_term_meta( $term->term_id, '_event_color', true ) ?: '',
                ];
                if ( ! $category_id ) $category_id = $term->term_id;
            }
        }

        $event = [
            'id'     => $post->ID,
            'title'  => $post->post_title,
            'start'  => $start_iso,
            'allDay' => (bool) $all_day,
            'color'  => $color ?: '',
            'url'    => '',
            'extendedProps' => [
                'location'    => $location ?: '',
                'locationUrl' => $loc_url ?: '',
                'eventUrl'    => $event_url ?: '',
                'excerpt'     => get_the_excerpt( $post ),
                'categories'  => $categories,
                'categoryId'  => $category_id,
                'image'       => get_the_post_thumbnail_url( $post->ID, 'medium' ) ?: '',
                'permalink'   => get_permalink( $post->ID ),
                'startDate'   => $start_date,
                'startTime'   => $start_time ?: '',
                'endDate'     => $end_date ?: '',
                'endTime'     => $end_time ?: '',
                'eventColor'  => get_post_meta( $post->ID, '_olo_event_color', true ) ?: '',
            ],
        ];

        if ( $end_iso ) $event['end'] = $end_iso;

        if ( $full ) {
            $event['extendedProps']['content']   = apply_filters( 'the_content', $post->post_content );
            $event['extendedProps']['imageLarge'] = get_the_post_thumbnail_url( $post->ID, 'large' ) ?: '';
        }

        return $event;
    }
}
