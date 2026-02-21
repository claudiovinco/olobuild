<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Booking_Rest_API {

    public function init() {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    public function register_routes() {
        $ns = 'olo-booking/v1';

        // ── Services ──
        register_rest_route( $ns, '/services', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_services' ],
            'permission_callback' => '__return_true',
        ] );

        register_rest_route( $ns, '/services/(?P<id>\d+)', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_service' ],
            'permission_callback' => '__return_true',
        ] );

        // ── Availability ──
        register_rest_route( $ns, '/services/(?P<id>\d+)/slots', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_slots' ],
            'permission_callback' => '__return_true',
            'args'                => [
                'date' => [ 'type' => 'string', 'required' => true ],
            ],
        ] );

        register_rest_route( $ns, '/services/(?P<id>\d+)/month', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_month_availability' ],
            'permission_callback' => '__return_true',
            'args'                => [
                'month' => [ 'type' => 'string', 'required' => true ],
            ],
        ] );

        // ── Bookings ──
        register_rest_route( $ns, '/bookings', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_bookings' ],
                'permission_callback' => [ $this, 'can_manage' ],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'create_booking' ],
                'permission_callback' => '__return_true',
            ],
        ] );

        register_rest_route( $ns, '/bookings/(?P<id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_booking' ],
                'permission_callback' => [ $this, 'can_manage' ],
            ],
            [
                'methods'             => 'PUT,PATCH',
                'callback'            => [ $this, 'update_booking' ],
                'permission_callback' => [ $this, 'can_manage' ],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [ $this, 'delete_booking' ],
                'permission_callback' => [ $this, 'can_manage' ],
            ],
        ] );

        register_rest_route( $ns, '/bookings/(?P<id>\d+)/status', [
            'methods'             => 'PATCH',
            'callback'            => [ $this, 'update_status' ],
            'permission_callback' => [ $this, 'can_manage' ],
        ] );

        // ── Stats ──
        register_rest_route( $ns, '/stats', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_stats' ],
            'permission_callback' => [ $this, 'can_manage' ],
        ] );
    }

    public function can_manage() {
        return current_user_can( 'manage_olo_bookings' ) || current_user_can( 'manage_options' );
    }

    /* ══════════════════════ Services ══════════════════════ */

    public function get_services( $request ) {
        $args = [
            'post_type'      => 'olo_service',
            'post_status'    => 'publish',
            'posts_per_page' => 100,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ];

        if ( is_user_logged_in() ) {
            $ids = Olo_Role_Manager::get_user_service_ids();
            if ( is_array( $ids ) ) {
                if ( empty( $ids ) ) return new WP_REST_Response( [], 200 );
                $args['post__in'] = $ids;
            }
        }

        $query    = new WP_Query( $args );
        $services = [];
        foreach ( $query->posts as $post ) {
            $services[] = $this->format_service( $post );
        }
        return new WP_REST_Response( $services, 200 );
    }

    public function get_service( $request ) {
        $post = get_post( $request['id'] );
        if ( ! $post || $post->post_type !== 'olo_service' ) {
            return new WP_REST_Response( [ 'message' => 'Servizio non trovato.' ], 404 );
        }
        return new WP_REST_Response( $this->format_service( $post, true ), 200 );
    }

    private function format_service( $post, $full = false ) {
        $data = [
            'id'           => $post->ID,
            'title'        => $post->post_title,
            'excerpt'      => get_the_excerpt( $post ),
            'service_type' => get_post_meta( $post->ID, '_olo_service_type', true ) ?: 'accommodation',
            'duration'     => (int) ( get_post_meta( $post->ID, '_olo_service_duration', true ) ?: 60 ),
            'price'        => get_post_meta( $post->ID, '_olo_service_price', true ) ?: '',
            'color'        => get_post_meta( $post->ID, '_olo_service_color', true ) ?: '#6366F1',
            'image'        => get_the_post_thumbnail_url( $post->ID, 'medium' ) ?: '',
        ];

        if ( $full ) {
            $data['buffer']       = (int) ( get_post_meta( $post->ID, '_olo_service_buffer', true ) ?: 0 );
            $data['max_bookings'] = (int) ( get_post_meta( $post->ID, '_olo_service_max_bookings', true ) ?: 1 );
            $data['availability'] = get_post_meta( $post->ID, '_olo_service_availability', true ) ?: [];
            $data['exceptions']   = get_post_meta( $post->ID, '_olo_service_exceptions', true ) ?: [];
            $data['description']  = apply_filters( 'the_content', $post->post_content );
        }

        return $data;
    }

    /* ══════════════════════ Availability ══════════════════════ */

    public function get_slots( $request ) {
        // Endpoint mantenuto per compatibilità — ritorna vuoto per accommodation
        return new WP_REST_Response( [], 200 );
    }

    public function get_month_availability( $request ) {
        $service_id = (int) $request['id'];
        $month      = sanitize_text_field( $request['month'] );

        $post = get_post( $service_id );
        if ( ! $post || $post->post_type !== 'olo_service' ) {
            return new WP_REST_Response( [ 'message' => 'Servizio non trovato.' ], 404 );
        }

        $start = $month . '-01';
        $end   = date( 'Y-m-t', strtotime( $start ) );

        $days = $this->compute_month_availability( $service_id, $start, $end );

        return new WP_REST_Response( $days, 200 );
    }

    /**
     * Calcola disponibilità giorno per giorno in un range di date.
     * Supporta il modello accommodation (checkin/checkout date range).
     */
    private function compute_month_availability( $service_id, $start, $end ) {
        // Prenotazioni attive che si sovrappongono al mese
        $bookings = Olo_Booking_DB::query( [
            'service_id' => $service_id,
            'status'     => [ 'pending', 'confirmed' ],
            'date_from'  => $start,
            'date_to'    => $end,
        ] );

        // Chiusure
        $closures = get_post_meta( $service_id, '_olo_service_closures', true ) ?: [];

        // Stagioni (periodi apertura)
        $seasons = get_post_meta( $service_id, '_olo_service_seasons', true ) ?: [];

        $days    = [];
        $current = $start;
        $today   = current_time( 'Y-m-d' );

        while ( $current <= $end ) {
            // 1. Passato → closed
            if ( $current < $today ) {
                $days[ $current ] = [ 'status' => 'closed', 'total_slots' => 0, 'available_slots' => 0 ];
                $current = date( 'Y-m-d', strtotime( $current . ' +1 day' ) );
                continue;
            }

            // 2. Chiusura esplicita
            $is_closed = false;
            foreach ( $closures as $c ) {
                $c_start = $c['start'] ?? $c['date'] ?? '';
                $c_end   = $c['end'] ?? $c['date'] ?? '';
                if ( $c_start && $c_end && $current >= $c_start && $current <= $c_end ) {
                    $is_closed = true;
                    break;
                }
            }
            if ( $is_closed ) {
                $days[ $current ] = [ 'status' => 'closed', 'total_slots' => 0, 'available_slots' => 0 ];
                $current = date( 'Y-m-d', strtotime( $current . ' +1 day' ) );
                continue;
            }

            // 3. Se ci sono stagioni, verifica che la data rientri in almeno una
            if ( ! empty( $seasons ) ) {
                $in_season = false;
                foreach ( $seasons as $s ) {
                    $s_from = $s['date_from'] ?? $s['start'] ?? '';
                    $s_to   = $s['date_to']   ?? $s['end']   ?? '';
                    if ( $s_from && $s_to && $current >= $s_from && $current <= $s_to ) {
                        $in_season = true;
                        break;
                    }
                }
                if ( ! $in_season ) {
                    $days[ $current ] = [ 'status' => 'closed', 'total_slots' => 0, 'available_slots' => 0 ];
                    $current = date( 'Y-m-d', strtotime( $current . ' +1 day' ) );
                    continue;
                }
            }

            // 4. Verifica sovrapposizione prenotazioni (checkin_date <= current < checkout_date)
            $is_occupied = false;
            foreach ( $bookings as $b ) {
                if ( $b['checkin_date'] <= $current && $b['checkout_date'] > $current ) {
                    $is_occupied = true;
                    break;
                }
            }

            $days[ $current ] = [
                'status'          => $is_occupied ? 'full' : 'available',
                'total_slots'     => 1,
                'available_slots' => $is_occupied ? 0 : 1,
            ];

            $current = date( 'Y-m-d', strtotime( $current . ' +1 day' ) );
        }

        return $days;
    }

    /* ══════════════════════ Bookings ══════════════════════ */

    public function get_bookings( $request ) {
        $args = [];

        $service_ids = Olo_Role_Manager::get_user_service_ids();
        if ( is_array( $service_ids ) ) {
            $args['service_ids'] = $service_ids;
        }

        if ( $request->get_param( 'service_id' ) ) {
            $sid = (int) $request->get_param( 'service_id' );
            if ( is_array( $service_ids ) && ! in_array( $sid, $service_ids, true ) ) {
                return new WP_REST_Response( [ 'message' => 'Non autorizzato.' ], 403 );
            }
            $args['service_id'] = $sid;
        }

        if ( $request->get_param( 'status' ) ) {
            $args['status'] = sanitize_text_field( $request->get_param( 'status' ) );
        }
        if ( $request->get_param( 'start' ) ) {
            $args['date_from'] = substr( $request->get_param( 'start' ), 0, 10 );
        }
        if ( $request->get_param( 'end' ) ) {
            $args['date_to'] = substr( $request->get_param( 'end' ), 0, 10 );
        }

        $bookings = Olo_Booking_DB::query( $args );
        $events   = [];
        foreach ( $bookings as $b ) {
            $events[] = $this->format_booking_event( $b );
        }
        return new WP_REST_Response( $events, 200 );
    }

    public function get_booking( $request ) {
        $booking = Olo_Booking_DB::get( $request['id'] );
        if ( ! $booking ) {
            return new WP_REST_Response( [ 'message' => 'Prenotazione non trovata.' ], 404 );
        }

        $service_ids = Olo_Role_Manager::get_user_service_ids();
        if ( is_array( $service_ids ) && ! in_array( (int) $booking['service_id'], $service_ids, true ) ) {
            return new WP_REST_Response( [ 'message' => 'Non autorizzato.' ], 403 );
        }

        return new WP_REST_Response( $this->format_booking_detail( $booking ), 200 );
    }

    public function create_booking( $request ) {
        $params = $request->get_json_params();

        $required = [ 'service_id', 'guest_name', 'checkin_date', 'checkout_date' ];
        foreach ( $required as $field ) {
            if ( empty( $params[ $field ] ) ) {
                return new WP_REST_Response( [ 'message' => "Campo obbligatorio mancante: {$field}" ], 400 );
            }
        }

        $service_id = (int) $params['service_id'];
        $post       = get_post( $service_id );
        if ( ! $post || $post->post_type !== 'olo_service' ) {
            return new WP_REST_Response( [ 'message' => 'Servizio non trovato.' ], 404 );
        }

        $manager_id = (int) ( get_post_meta( $service_id, '_olo_service_manager', true ) ?: 0 );

        $id = Olo_Booking_DB::insert( [
            'service_id'     => $service_id,
            'guest_name'     => sanitize_text_field( $params['guest_name'] ),
            'guest_email'    => sanitize_email( $params['guest_email'] ?? '' ),
            'guest_phone'    => sanitize_text_field( $params['guest_phone'] ?? '' ),
            'guest_count'    => absint( $params['guest_count'] ?? 1 ),
            'checkin_date'   => sanitize_text_field( $params['checkin_date'] ),
            'checkout_date'  => sanitize_text_field( $params['checkout_date'] ),
            'price_total'    => floatval( $params['price_total'] ?? 0 ),
            'status'         => sanitize_text_field( $params['status'] ?? 'pending' ),
            'source'         => sanitize_text_field( $params['source'] ?? 'admin' ),
            'notes'          => sanitize_textarea_field( $params['notes'] ?? '' ),
            'manager_id'     => $manager_id,
        ] );

        if ( ! $id ) {
            return new WP_REST_Response( [ 'message' => 'Errore nella creazione.' ], 500 );
        }

        $booking = Olo_Booking_DB::get( $id );
        do_action( 'olo_booking_created', $booking, $post );

        return new WP_REST_Response( $this->format_booking_detail( $booking ), 201 );
    }

    public function update_booking( $request ) {
        $booking = Olo_Booking_DB::get( $request['id'] );
        if ( ! $booking ) {
            return new WP_REST_Response( [ 'message' => 'Prenotazione non trovata.' ], 404 );
        }

        $service_ids = Olo_Role_Manager::get_user_service_ids();
        if ( is_array( $service_ids ) && ! in_array( (int) $booking['service_id'], $service_ids, true ) ) {
            return new WP_REST_Response( [ 'message' => 'Non autorizzato.' ], 403 );
        }

        $params    = $request->get_json_params();
        $data      = [];
        $updatable = [ 'guest_name', 'guest_email', 'guest_phone', 'guest_count', 'checkin_date', 'checkout_date', 'price_total', 'status', 'notes', 'service_id' ];
        foreach ( $updatable as $key ) {
            if ( isset( $params[ $key ] ) ) $data[ $key ] = $params[ $key ];
        }

        Olo_Booking_DB::update( $request['id'], $data );

        $updated = Olo_Booking_DB::get( $request['id'] );
        if ( isset( $params['status'] ) && $params['status'] !== $booking['status'] ) {
            do_action( 'olo_booking_status_changed', $updated, $booking['status'], $params['status'] );
        }

        return new WP_REST_Response( $this->format_booking_detail( $updated ), 200 );
    }

    public function update_status( $request ) {
        $booking = Olo_Booking_DB::get( $request['id'] );
        if ( ! $booking ) {
            return new WP_REST_Response( [ 'message' => 'Prenotazione non trovata.' ], 404 );
        }

        $params = $request->get_json_params();
        if ( empty( $params['status'] ) ) {
            return new WP_REST_Response( [ 'message' => 'Stato mancante.' ], 400 );
        }

        $old_status = $booking['status'];
        Olo_Booking_DB::update( $request['id'], [ 'status' => sanitize_text_field( $params['status'] ) ] );
        $updated = Olo_Booking_DB::get( $request['id'] );
        do_action( 'olo_booking_status_changed', $updated, $old_status, $params['status'] );

        return new WP_REST_Response( $this->format_booking_detail( $updated ), 200 );
    }

    public function delete_booking( $request ) {
        $booking = Olo_Booking_DB::get( $request['id'] );
        if ( ! $booking ) {
            return new WP_REST_Response( [ 'message' => 'Prenotazione non trovata.' ], 404 );
        }

        $service_ids = Olo_Role_Manager::get_user_service_ids();
        if ( is_array( $service_ids ) && ! in_array( (int) $booking['service_id'], $service_ids, true ) ) {
            return new WP_REST_Response( [ 'message' => 'Non autorizzato.' ], 403 );
        }

        Olo_Booking_DB::delete( $request['id'] );
        return new WP_REST_Response( [ 'success' => true ], 200 );
    }

    /* ══════════════════════ Stats ══════════════════════ */

    public function get_stats( $request ) {
        $service_ids = Olo_Role_Manager::get_user_service_ids();
        return new WP_REST_Response( Olo_Booking_DB::get_stats( $service_ids ), 200 );
    }

    /* ══════════════════════ Formatters ══════════════════════ */

    private function format_booking_event( $b ) {
        $service       = get_post( $b['service_id'] );
        $service_name  = $service ? $service->post_title : 'Servizio #' . $b['service_id'];
        $service_color = get_post_meta( $b['service_id'], '_olo_service_color', true ) ?: '#6366F1';

        return [
            'id'             => (int) $b['id'],
            'service_id'     => (int) $b['service_id'],
            'service_name'   => $service_name,
            'service_color'  => $service_color,
            'guest_name'     => $b['guest_name'],
            'guest_email'    => $b['guest_email'] ?? '',
            'guest_phone'    => $b['guest_phone'] ?? '',
            'guest_count'    => (int) ( $b['guest_count'] ?? 1 ),
            'checkin_date'   => $b['checkin_date'],
            'checkout_date'  => $b['checkout_date'],
            'nights'         => (int) ( $b['nights'] ?? 1 ),
            'price_total'    => (float) ( $b['price_total'] ?? 0 ),
            'status'         => $b['status'],
            'source'         => $b['source'] ?? '',
            'notes'          => $b['notes'] ?? '',
        ];
    }

    private function format_booking_detail( $b ) {
        $service       = get_post( $b['service_id'] );
        $service_color = get_post_meta( $b['service_id'], '_olo_service_color', true ) ?: '#6366F1';

        return [
            'id'             => (int) $b['id'],
            'service_id'     => (int) $b['service_id'],
            'service_name'   => $service ? $service->post_title : '',
            'service_color'  => $service_color,
            'guest_name'     => $b['guest_name'],
            'guest_email'    => $b['guest_email'] ?? '',
            'guest_phone'    => $b['guest_phone'] ?? '',
            'guest_count'    => (int) ( $b['guest_count'] ?? 1 ),
            'checkin_date'   => $b['checkin_date'],
            'checkout_date'  => $b['checkout_date'],
            'nights'         => (int) ( $b['nights'] ?? 1 ),
            'price_total'    => (float) ( $b['price_total'] ?? 0 ),
            'status'         => $b['status'],
            'source'         => $b['source'] ?? '',
            'notes'          => $b['notes'] ?? '',
            'created_at'     => $b['created_at'],
        ];
    }
}
