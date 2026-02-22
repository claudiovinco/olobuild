<?php
/**
 * Olo Booking — REST API Manager (v2)
 *
 * Endpoints per il pannello gestore frontend.
 * Namespace: olo-booking/v2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Booking_Rest_API_Manager {

    private $ns = 'olo-booking/v2';

    public function init() {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    public function register_routes() {
        // ── Manager profile ──
        register_rest_route( $this->ns, '/manager/me', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_me' ],
            'permission_callback' => [ $this, 'can_manage' ],
        ] );

        // ── Services (structures) ──
        register_rest_route( $this->ns, '/manager/services', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_services' ],
                'permission_callback' => [ $this, 'can_manage' ],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'create_service' ],
                'permission_callback' => [ $this, 'can_manage' ],
            ],
        ] );

        register_rest_route( $this->ns, '/manager/services/(?P<id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_service' ],
                'permission_callback' => [ $this, 'can_manage' ],
            ],
            [
                'methods'             => 'PUT,PATCH',
                'callback'            => [ $this, 'update_service' ],
                'permission_callback' => [ $this, 'can_manage' ],
            ],
        ] );

        register_rest_route( $this->ns, '/manager/services/(?P<id>\d+)/seasons', [
            'methods'             => 'PUT',
            'callback'            => [ $this, 'update_seasons' ],
            'permission_callback' => [ $this, 'can_manage' ],
        ] );

        register_rest_route( $this->ns, '/manager/services/(?P<id>\d+)/closures', [
            'methods'             => 'PUT',
            'callback'            => [ $this, 'update_closures' ],
            'permission_callback' => [ $this, 'can_manage' ],
        ] );

        register_rest_route( $this->ns, '/manager/services/(?P<id>\d+)/gallery', [
            'methods'             => 'PUT',
            'callback'            => [ $this, 'update_gallery' ],
            'permission_callback' => [ $this, 'can_manage' ],
        ] );

        // ── Availability & Pricing (public) ──
        register_rest_route( $this->ns, '/availability/(?P<id>\d+)/month', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_month_availability' ],
            'permission_callback' => '__return_true',
        ] );

        register_rest_route( $this->ns, '/price/(?P<id>\d+)', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_price' ],
            'permission_callback' => '__return_true',
        ] );

        // ── Bookings ──
        register_rest_route( $this->ns, '/bookings', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_bookings' ],
                'permission_callback' => [ $this, 'can_manage' ],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'create_booking' ],
                'permission_callback' => [ $this, 'can_manage_or_public' ],
            ],
        ] );

        register_rest_route( $this->ns, '/bookings/(?P<id>\d+)', [
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

        register_rest_route( $this->ns, '/bookings/export', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'export_bookings' ],
            'permission_callback' => [ $this, 'can_manage' ],
        ] );

        register_rest_route( $this->ns, '/bookings/import', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'import_bookings' ],
            'permission_callback' => [ $this, 'can_manage' ],
        ] );

        register_rest_route( $this->ns, '/bookings/(?P<id>\d+)/status', [
            'methods'             => 'PATCH',
            'callback'            => [ $this, 'update_booking_status' ],
            'permission_callback' => [ $this, 'can_manage' ],
        ] );

        register_rest_route( $this->ns, '/bookings/(?P<id>\d+)/log', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_booking_log' ],
            'permission_callback' => [ $this, 'can_manage' ],
        ] );

        // ── Stats ──
        register_rest_route( $this->ns, '/stats', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_stats' ],
            'permission_callback' => [ $this, 'can_manage' ],
        ] );

        register_rest_route( $this->ns, '/upcoming', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_upcoming' ],
            'permission_callback' => [ $this, 'can_manage' ],
        ] );

        // ── Public booking ──
        register_rest_route( $this->ns, '/public/book', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'public_book' ],
            'permission_callback' => '__return_true',
        ] );

        // ── Users ──
        register_rest_route( $this->ns, '/manager/users', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_users' ],
                'permission_callback' => [ $this, 'can_manage_users' ],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'create_user' ],
                'permission_callback' => [ $this, 'can_manage_users' ],
            ],
        ] );

        register_rest_route( $this->ns, '/manager/users/(?P<id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_user' ],
                'permission_callback' => [ $this, 'can_manage_users' ],
            ],
            [
                'methods'             => 'PUT,PATCH',
                'callback'            => [ $this, 'update_user' ],
                'permission_callback' => [ $this, 'can_manage_users' ],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [ $this, 'delete_user' ],
                'permission_callback' => [ $this, 'can_manage_users' ],
            ],
        ] );

        // ── Theme (admin only) ──
        register_rest_route( $this->ns, '/manager/theme', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_theme' ],
                'permission_callback' => [ $this, 'is_admin' ],
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'save_theme' ],
                'permission_callback' => [ $this, 'is_admin' ],
            ],
        ] );

        // ── Amenities Catalog (admin only) ──
        register_rest_route( $this->ns, '/manager/amenities-catalog', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_amenities_catalog' ],
                'permission_callback' => [ $this, 'is_admin' ],
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'save_amenities_catalog' ],
                'permission_callback' => [ $this, 'is_admin' ],
            ],
        ] );

        // ── Permissions (admin only) ──
        register_rest_route( $this->ns, '/manager/permissions', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_permissions' ],
                'permission_callback' => [ $this, 'is_admin' ],
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'save_permissions' ],
                'permission_callback' => [ $this, 'is_admin' ],
            ],
        ] );
    }

    /* ─── Permission callbacks ─── */

    public function can_manage() {
        return Olo_Role_Manager::get_access_level() !== 'none';
    }

    public function is_admin() {
        return current_user_can( 'manage_options' );
    }

    public function is_admin_or_supervisor() {
        $level = Olo_Role_Manager::get_access_level();
        return in_array( $level, [ 'admin', 'supervisor' ], true );
    }

    public function can_manage_users() {
        return Olo_Role_Manager::can( 'create_users' )
            || Olo_Role_Manager::can( 'delete_users' )
            || Olo_Role_Manager::can( 'edit_user_profile' )
            || Olo_Role_Manager::can( 'assign_services' );
    }

    public function can_manage_or_public() {
        return true; // create_booking handles auth internally
    }

    private function check_service_access( $service_id ) {
        $ids = Olo_Role_Manager::get_user_service_ids();
        if ( $ids === null ) return true; // admin
        return in_array( (int) $service_id, $ids, true );
    }

    private function get_manager_service_ids() {
        return Olo_Role_Manager::get_user_service_ids();
    }

    /* =========================================================================
     *  Manager Profile
     * ======================================================================= */

    public function get_me( $request ) {
        $user = wp_get_current_user();
        $service_ids  = $this->get_manager_service_ids();
        $access_level = Olo_Role_Manager::get_access_level();

        return new WP_REST_Response( [
            'id'              => $user->ID,
            'name'            => $user->display_name,
            'email'           => $user->user_email,
            'is_admin'        => $access_level === 'admin',
            'access_level'    => $access_level, // admin | supervisor | manager
            'can_filter_type' => Olo_Role_Manager::can_filter_service_type(),
            'service_ids'     => $service_ids, // null = all (admin/supervisor)
        ], 200 );
    }

    /* =========================================================================
     *  Services
     * ======================================================================= */

    public function get_services( $request ) {
        $service_ids = $this->get_manager_service_ids();
        $args = [
            'post_type'      => 'olo_service',
            'post_status'    => 'publish',
            'posts_per_page' => 100,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ];
        if ( is_array( $service_ids ) ) {
            $args['post__in'] = empty( $service_ids ) ? [ 0 ] : $service_ids;
        }

        // Filter by service type (admin/supervisor)
        if ( $request->get_param( 'type' ) && Olo_Role_Manager::can_filter_service_type() ) {
            $args['meta_query'] = [
                [
                    'key'   => '_olo_service_type',
                    'value' => sanitize_text_field( $request->get_param( 'type' ) ),
                ],
            ];
        }

        $query    = new WP_Query( $args );
        $services = [];
        foreach ( $query->posts as $post ) {
            $services[] = $this->format_service_summary( $post );
        }
        return new WP_REST_Response( $services, 200 );
    }

    public function create_service( $request ) {
        if ( ! Olo_Role_Manager::can( 'create_services' ) ) {
            return new WP_REST_Response( [ 'message' => 'Non hai il permesso di creare strutture.' ], 403 );
        }

        $params = $request->get_json_params();

        if ( empty( $params['title'] ) ) {
            return new WP_REST_Response( [ 'message' => 'Il nome della struttura è obbligatorio.' ], 400 );
        }

        $post_id = wp_insert_post( [
            'post_type'   => 'olo_service',
            'post_status' => 'publish',
            'post_title'  => sanitize_text_field( $params['title'] ),
            'post_content' => wp_kses_post( $params['description'] ?? '' ),
            'post_excerpt' => wp_kses_post( $params['excerpt'] ?? '' ),
        ], true );

        if ( is_wp_error( $post_id ) ) {
            return new WP_REST_Response( [ 'message' => $post_id->get_error_message() ], 500 );
        }

        // Service type
        $type = sanitize_text_field( $params['service_type'] ?? 'accommodation' );
        update_post_meta( $post_id, '_olo_service_type', $type );

        // Color
        $color = sanitize_hex_color( $params['color'] ?? '' );
        if ( $color ) {
            update_post_meta( $post_id, '_olo_service_color', $color );
        }

        // Basic meta fields
        $meta_fields = [
            'capacity'      => '_olo_service_capacity',
            'price'         => '_olo_service_price',
            'checkin_time'  => '_olo_service_checkin_time',
            'checkout_time' => '_olo_service_checkout_time',
        ];
        foreach ( $meta_fields as $key => $meta ) {
            if ( isset( $params[ $key ] ) ) {
                update_post_meta( $post_id, $meta, sanitize_text_field( $params[ $key ] ) );
            }
        }

        // Assign manager
        if ( ! empty( $params['manager_id'] ) ) {
            update_post_meta( $post_id, '_olo_service_manager', absint( $params['manager_id'] ) );
        }

        // Initialize empty seasons and closures
        update_post_meta( $post_id, '_olo_service_seasons', [] );
        update_post_meta( $post_id, '_olo_service_closures', [] );
        update_post_meta( $post_id, '_olo_service_gallery', [] );

        $post = get_post( $post_id );
        return new WP_REST_Response( $this->format_service_full( $post ), 201 );
    }

    public function get_service( $request ) {
        $id   = (int) $request['id'];
        $post = get_post( $id );
        if ( ! $post || $post->post_type !== 'olo_service' ) {
            return new WP_REST_Response( [ 'message' => 'Struttura non trovata.' ], 404 );
        }
        if ( ! $this->check_service_access( $id ) ) {
            return new WP_REST_Response( [ 'message' => 'Non autorizzato.' ], 403 );
        }
        return new WP_REST_Response( $this->format_service_full( $post ), 200 );
    }

    public function update_service( $request ) {
        $id = (int) $request['id'];
        if ( ! $this->check_service_access( $id ) ) {
            return new WP_REST_Response( [ 'message' => 'Non autorizzato.' ], 403 );
        }

        $params = $request->get_json_params();

        // Update post title/content
        $post_data = [];
        if ( isset( $params['title'] ) ) $post_data['post_title'] = sanitize_text_field( $params['title'] );
        if ( isset( $params['description'] ) ) $post_data['post_content'] = wp_kses_post( $params['description'] );
        if ( isset( $params['excerpt'] ) ) $post_data['post_excerpt'] = wp_kses_post( $params['excerpt'] );
        if ( $post_data ) {
            $post_data['ID'] = $id;
            wp_update_post( $post_data );
        }

        // Update meta fields
        // Service type (admin/supervisor)
        if ( isset( $params['service_type'] ) && Olo_Role_Manager::can_filter_service_type() ) {
            update_post_meta( $id, '_olo_service_type', sanitize_text_field( $params['service_type'] ) );
        }

        $text_fields = [
            'price'          => '_olo_service_price',
            'capacity'       => '_olo_service_capacity',
            'bedrooms'       => '_olo_service_bedrooms',
            'beds'           => '_olo_service_beds',
            'bathrooms'      => '_olo_service_bathrooms',
            'sqm'            => '_olo_service_sqm',
            'checkin_time'   => '_olo_service_checkin_time',
            'checkout_time'  => '_olo_service_checkout_time',
            'checkin_day'    => '_olo_service_checkin_day',
            'opening'        => '_olo_service_opening',
            'altitude'       => '_olo_service_altitude',
            'mushrooms'      => '_olo_service_mushrooms',
            'club_group'     => '_olo_service_club_group',
            'club_category'  => '_olo_service_club_category',
            'cipat'          => '_olo_service_cipat',
            'address'        => '_olo_service_address',
            'valley'         => '_olo_service_valley',
            'latitude'       => '_olo_service_latitude',
            'longitude'      => '_olo_service_longitude',
            'video_1'        => '_olo_service_video_1',
            'video_2'        => '_olo_service_video_2',
            'video_3'        => '_olo_service_video_3',
        ];
        foreach ( $text_fields as $key => $meta ) {
            if ( isset( $params[ $key ] ) ) {
                update_post_meta( $id, $meta, sanitize_text_field( $params[ $key ] ) );
            }
        }

        $html_fields = [
            'directions' => '_olo_service_directions',
            'rules'      => '_olo_service_rules',
        ];
        foreach ( $html_fields as $key => $meta ) {
            if ( isset( $params[ $key ] ) ) {
                update_post_meta( $id, $meta, wp_kses_post( $params[ $key ] ) );
            }
        }

        // Amenities (array of keys)
        if ( isset( $params['amenities'] ) && is_array( $params['amenities'] ) ) {
            $amenities = array_map( 'sanitize_text_field', $params['amenities'] );
            update_post_meta( $id, '_olo_service_amenities', $amenities );
        }

        // Amenity configuration
        if ( isset( $params['max_amenities'] ) ) {
            update_post_meta( $id, '_olo_service_max_amenities', absint( $params['max_amenities'] ) );
        }
        if ( isset( $params['enabled_amenity_cats'] ) && is_array( $params['enabled_amenity_cats'] ) ) {
            $cats = array_map( 'sanitize_text_field', $params['enabled_amenity_cats'] );
            update_post_meta( $id, '_olo_service_enabled_amenity_cats', $cats );
        }

        // Return updated service
        $post = get_post( $id );
        return new WP_REST_Response( $this->format_service_full( $post ), 200 );
    }

    public function update_seasons( $request ) {
        $id = (int) $request['id'];
        if ( ! $this->check_service_access( $id ) ) {
            return new WP_REST_Response( [ 'message' => 'Non autorizzato.' ], 403 );
        }

        $params  = $request->get_json_params();
        $seasons = [];
        foreach ( ( $params['seasons'] ?? [] ) as $s ) {
            $seasons[] = [
                'name'        => sanitize_text_field( $s['name'] ?? '' ),
                'date_from'   => sanitize_text_field( $s['date_from'] ?? '' ),
                'date_to'     => sanitize_text_field( $s['date_to'] ?? '' ),
                'price_night' => sanitize_text_field( $s['price_night'] ?? '' ),
                'min_nights'  => sanitize_text_field( $s['min_nights'] ?? '1' ),
                'week_only'   => ! empty( $s['week_only'] ) ? '1' : '0',
                'note'        => sanitize_text_field( $s['note'] ?? '' ),
            ];
        }
        update_post_meta( $id, '_olo_service_seasons', $seasons );
        return new WP_REST_Response( [ 'success' => true, 'seasons' => $seasons ], 200 );
    }

    public function update_closures( $request ) {
        $id = (int) $request['id'];
        if ( ! $this->check_service_access( $id ) ) {
            return new WP_REST_Response( [ 'message' => 'Non autorizzato.' ], 403 );
        }

        $params   = $request->get_json_params();
        $closures = [];
        foreach ( ( $params['closures'] ?? [] ) as $c ) {
            $closures[] = [
                'date_from' => sanitize_text_field( $c['date_from'] ?? '' ),
                'date_to'   => sanitize_text_field( $c['date_to'] ?? '' ),
                'reason'    => sanitize_text_field( $c['reason'] ?? '' ),
            ];
        }
        update_post_meta( $id, '_olo_service_closures', $closures );
        return new WP_REST_Response( [ 'success' => true, 'closures' => $closures ], 200 );
    }

    public function update_gallery( $request ) {
        $id = (int) $request['id'];
        if ( ! $this->check_service_access( $id ) ) {
            return new WP_REST_Response( [ 'message' => 'Non autorizzato.' ], 403 );
        }

        $params = $request->get_json_params();
        $ids    = array_map( 'absint', $params['gallery'] ?? [] );
        $ids    = array_filter( $ids );
        update_post_meta( $id, '_olo_service_gallery', $ids );

        // Prima immagine = copertina (featured image)
        if ( ! empty( $ids ) ) {
            set_post_thumbnail( $id, reset( $ids ) );
        } else {
            delete_post_thumbnail( $id );
        }

        // Return URLs
        $images = [];
        foreach ( $ids as $att_id ) {
            $images[] = [
                'id'    => $att_id,
                'thumb' => wp_get_attachment_image_url( $att_id, 'thumbnail' ),
                'full'  => wp_get_attachment_image_url( $att_id, 'large' ),
            ];
        }
        return new WP_REST_Response( [ 'success' => true, 'gallery' => $images ], 200 );
    }

    /* =========================================================================
     *  Availability & Pricing (public)
     * ======================================================================= */

    public function get_month_availability( $request ) {
        $id    = (int) $request['id'];
        $month = sanitize_text_field( $request->get_param( 'month' ) );

        if ( ! preg_match( '/^\d{4}-\d{2}$/', $month ) ) {
            return new WP_REST_Response( [ 'message' => 'Formato mese non valido (YYYY-MM).' ], 400 );
        }

        $days = Olo_Price_Calculator::get_month_availability( $id, $month );
        return new WP_REST_Response( $days, 200 );
    }

    public function get_price( $request ) {
        $id       = (int) $request['id'];
        $checkin  = sanitize_text_field( $request->get_param( 'checkin' ) );
        $checkout = sanitize_text_field( $request->get_param( 'checkout' ) );
        $guests   = (int) ( $request->get_param( 'guests' ) ?: 1 );

        if ( ! $checkin || ! $checkout ) {
            return new WP_REST_Response( [ 'message' => 'Parametri checkin e checkout obbligatori.' ], 400 );
        }

        $result = Olo_Price_Calculator::calculate( $id, $checkin, $checkout, $guests );
        return new WP_REST_Response( $result, $result['success'] ? 200 : 422 );
    }

    /* =========================================================================
     *  Bookings
     * ======================================================================= */

    public function get_bookings( $request ) {
        $args = [];

        $service_ids = $this->get_manager_service_ids();
        if ( is_array( $service_ids ) ) {
            $args['service_ids'] = $service_ids;
        }

        if ( $request->get_param( 'service_id' ) ) {
            $sid = (int) $request->get_param( 'service_id' );
            if ( ! $this->check_service_access( $sid ) ) {
                return new WP_REST_Response( [ 'message' => 'Non autorizzato.' ], 403 );
            }
            $args['service_id'] = $sid;
        }

        if ( $request->get_param( 'status' ) ) {
            $args['status'] = sanitize_text_field( $request->get_param( 'status' ) );
        }
        if ( $request->get_param( 'date_from' ) ) {
            $args['date_from'] = sanitize_text_field( $request->get_param( 'date_from' ) );
        }
        if ( $request->get_param( 'date_to' ) ) {
            $args['date_to'] = sanitize_text_field( $request->get_param( 'date_to' ) );
        }
        if ( $request->get_param( 'search' ) ) {
            $args['search'] = sanitize_text_field( $request->get_param( 'search' ) );
        }

        // Filter by service type (admin/supervisor only)
        if ( $request->get_param( 'service_type' ) && Olo_Role_Manager::can_filter_service_type() ) {
            $type = sanitize_text_field( $request->get_param( 'service_type' ) );
            $type_posts = get_posts( [
                'post_type'      => 'olo_service',
                'posts_per_page' => -1,
                'meta_key'       => '_olo_service_type',
                'meta_value'     => $type,
                'fields'         => 'ids',
            ] );
            if ( empty( $type_posts ) ) {
                return new WP_REST_Response( [], 200 );
            }
            // Intersect with existing service_ids filter if any
            if ( isset( $args['service_ids'] ) ) {
                $args['service_ids'] = array_values( array_intersect( $args['service_ids'], $type_posts ) );
            } else {
                $args['service_ids'] = $type_posts;
            }
        }

        $args['orderby'] = sanitize_text_field( $request->get_param( 'orderby' ) ?: 'checkin_date' );
        $args['order']   = sanitize_text_field( $request->get_param( 'order' ) ?: 'ASC' );
        $args['limit']   = absint( $request->get_param( 'limit' ) ?: 100 );
        $args['offset']  = absint( $request->get_param( 'offset' ) ?: 0 );

        $bookings = Olo_Booking_DB::query( $args );
        $result = [];
        foreach ( $bookings as $b ) {
            $result[] = $this->format_booking( $b );
        }
        return new WP_REST_Response( $result, 200 );
    }

    public function get_booking( $request ) {
        $booking = Olo_Booking_DB::get( $request['id'] );
        if ( ! $booking ) {
            return new WP_REST_Response( [ 'message' => 'Prenotazione non trovata.' ], 404 );
        }
        if ( ! $this->check_service_access( $booking['service_id'] ) ) {
            return new WP_REST_Response( [ 'message' => 'Non autorizzato.' ], 403 );
        }
        return new WP_REST_Response( $this->format_booking( $booking, true ), 200 );
    }

    public function create_booking( $request ) {
        $params = $request->get_json_params();

        $required = [ 'service_id', 'guest_name', 'checkin_date', 'checkout_date' ];
        foreach ( $required as $field ) {
            if ( empty( $params[ $field ] ) ) {
                return new WP_REST_Response( [ 'message' => "Campo obbligatorio: {$field}" ], 400 );
            }
        }

        $service_id = (int) $params['service_id'];
        $post = get_post( $service_id );
        if ( ! $post || $post->post_type !== 'olo_service' ) {
            return new WP_REST_Response( [ 'message' => 'Struttura non trovata.' ], 404 );
        }

        $checkin  = sanitize_text_field( $params['checkin_date'] );
        $checkout = sanitize_text_field( $params['checkout_date'] );
        $guests   = absint( $params['guest_count'] ?? 1 );

        // Calculate price
        $pricing = Olo_Price_Calculator::calculate( $service_id, $checkin, $checkout, $guests );
        if ( ! $pricing['success'] ) {
            return new WP_REST_Response( [ 'message' => implode( ' ', $pricing['errors'] ) ], 422 );
        }

        // Check availability
        if ( ! Olo_Booking_DB::is_available( $service_id, $checkin, $checkout ) ) {
            return new WP_REST_Response( [ 'message' => 'La struttura non è disponibile per le date selezionate.' ], 409 );
        }

        $manager_id = (int) ( get_post_meta( $service_id, '_olo_service_manager', true ) ?: 0 );

        // Allow manager to override price
        $price_total     = isset( $params['price_total'] ) ? floatval( $params['price_total'] ) : $pricing['price_total'];
        $price_per_night = isset( $params['price_per_night'] ) ? floatval( $params['price_per_night'] ) : $pricing['price_per_night'];

        $source = 'website';
        if ( $this->can_manage() && ! empty( $params['source'] ) ) {
            $source = sanitize_text_field( $params['source'] );
        } elseif ( $this->can_manage() ) {
            $source = 'manual';
        }

        $id = Olo_Booking_DB::insert( [
            'service_id'      => $service_id,
            'guest_name'      => sanitize_text_field( $params['guest_name'] ),
            'guest_email'     => sanitize_email( $params['guest_email'] ?? '' ),
            'guest_phone'     => sanitize_text_field( $params['guest_phone'] ?? '' ),
            'guest_count'     => $guests,
            'checkin_date'    => $checkin,
            'checkout_date'   => $checkout,
            'price_per_night' => $price_per_night,
            'price_total'     => $price_total,
            'season_name'     => $pricing['season_name'],
            'status'          => sanitize_text_field( $params['status'] ?? 'pending' ),
            'source'          => $source,
            'notes'           => sanitize_textarea_field( $params['notes'] ?? '' ),
            'internal_notes'  => sanitize_textarea_field( $params['internal_notes'] ?? '' ),
            'manager_id'      => $manager_id,
        ] );

        if ( ! $id ) {
            return new WP_REST_Response( [ 'message' => 'Errore nella creazione.' ], 500 );
        }

        $booking = Olo_Booking_DB::get( $id );
        do_action( 'olo_booking_created', $booking, $post );

        return new WP_REST_Response( $this->format_booking( $booking, true ), 201 );
    }

    public function update_booking( $request ) {
        $booking = Olo_Booking_DB::get( $request['id'] );
        if ( ! $booking ) {
            return new WP_REST_Response( [ 'message' => 'Prenotazione non trovata.' ], 404 );
        }
        if ( ! $this->check_service_access( $booking['service_id'] ) ) {
            return new WP_REST_Response( [ 'message' => 'Non autorizzato.' ], 403 );
        }

        $params = $request->get_json_params();
        $data   = [];
        $updatable = [ 'guest_name', 'guest_email', 'guest_phone', 'guest_count', 'checkin_date', 'checkout_date', 'price_per_night', 'price_total', 'season_name', 'status', 'notes', 'internal_notes', 'source' ];
        foreach ( $updatable as $key ) {
            if ( isset( $params[ $key ] ) ) $data[ $key ] = $params[ $key ];
        }

        // Re-check availability if dates changed
        $checkin  = $data['checkin_date'] ?? $booking['checkin_date'];
        $checkout = $data['checkout_date'] ?? $booking['checkout_date'];
        if ( $checkin !== $booking['checkin_date'] || $checkout !== $booking['checkout_date'] ) {
            if ( ! Olo_Booking_DB::is_available( $booking['service_id'], $checkin, $checkout, (int) $booking['id'] ) ) {
                return new WP_REST_Response( [ 'message' => 'Le nuove date non sono disponibili.' ], 409 );
            }
        }

        Olo_Booking_DB::update( $request['id'], $data );

        $updated = Olo_Booking_DB::get( $request['id'] );
        if ( isset( $params['status'] ) && $params['status'] !== $booking['status'] ) {
            do_action( 'olo_booking_status_changed', $updated, $booking['status'], $params['status'] );
        }

        return new WP_REST_Response( $this->format_booking( $updated, true ), 200 );
    }

    public function update_booking_status( $request ) {
        $booking = Olo_Booking_DB::get( $request['id'] );
        if ( ! $booking ) {
            return new WP_REST_Response( [ 'message' => 'Prenotazione non trovata.' ], 404 );
        }
        if ( ! $this->check_service_access( $booking['service_id'] ) ) {
            return new WP_REST_Response( [ 'message' => 'Non autorizzato.' ], 403 );
        }

        $params = $request->get_json_params();
        if ( empty( $params['status'] ) ) {
            return new WP_REST_Response( [ 'message' => 'Stato mancante.' ], 400 );
        }

        $old_status = $booking['status'];
        Olo_Booking_DB::update_status( $request['id'], sanitize_text_field( $params['status'] ) );
        $updated = Olo_Booking_DB::get( $request['id'] );
        do_action( 'olo_booking_status_changed', $updated, $old_status, $params['status'] );

        return new WP_REST_Response( $this->format_booking( $updated, true ), 200 );
    }

    public function delete_booking( $request ) {
        $booking = Olo_Booking_DB::get( $request['id'] );
        if ( ! $booking ) {
            return new WP_REST_Response( [ 'message' => 'Prenotazione non trovata.' ], 404 );
        }
        if ( ! $this->check_service_access( $booking['service_id'] ) ) {
            return new WP_REST_Response( [ 'message' => 'Non autorizzato.' ], 403 );
        }

        Olo_Booking_DB::delete( $request['id'] );
        return new WP_REST_Response( [ 'success' => true ], 200 );
    }

    public function get_booking_log( $request ) {
        $booking = Olo_Booking_DB::get( $request['id'] );
        if ( ! $booking ) {
            return new WP_REST_Response( [ 'message' => 'Prenotazione non trovata.' ], 404 );
        }
        if ( ! $this->check_service_access( $booking['service_id'] ) ) {
            return new WP_REST_Response( [ 'message' => 'Non autorizzato.' ], 403 );
        }

        $log = Olo_Booking_DB::get_log( $request['id'] );
        return new WP_REST_Response( $log, 200 );
    }

    /* =========================================================================
     *  Export / Import CSV
     * ======================================================================= */

    public function export_bookings( $request ) {
        if ( ! Olo_Role_Manager::can( 'export_bookings' ) ) {
            return new WP_REST_Response( [ 'message' => 'Non hai il permesso di esportare.' ], 403 );
        }

        $args = [];
        $service_ids = $this->get_manager_service_ids();
        if ( is_array( $service_ids ) ) {
            $args['service_ids'] = $service_ids;
        }

        if ( $request->get_param( 'service_id' ) ) {
            $sid = (int) $request->get_param( 'service_id' );
            if ( ! $this->check_service_access( $sid ) ) {
                return new WP_REST_Response( [ 'message' => 'Non autorizzato.' ], 403 );
            }
            $args['service_id'] = $sid;
        }
        if ( $request->get_param( 'status' ) ) {
            $args['status'] = sanitize_text_field( $request->get_param( 'status' ) );
        }
        if ( $request->get_param( 'date_from' ) ) {
            $args['date_from'] = sanitize_text_field( $request->get_param( 'date_from' ) );
        }
        if ( $request->get_param( 'date_to' ) ) {
            $args['date_to'] = sanitize_text_field( $request->get_param( 'date_to' ) );
        }
        if ( $request->get_param( 'service_type' ) && Olo_Role_Manager::can_filter_service_type() ) {
            $type = sanitize_text_field( $request->get_param( 'service_type' ) );
            $type_posts = get_posts( [
                'post_type' => 'olo_service', 'posts_per_page' => -1,
                'meta_key' => '_olo_service_type', 'meta_value' => $type, 'fields' => 'ids',
            ] );
            if ( empty( $type_posts ) ) {
                return new WP_REST_Response( [], 200 );
            }
            if ( isset( $args['service_ids'] ) ) {
                $args['service_ids'] = array_values( array_intersect( $args['service_ids'], $type_posts ) );
            } else {
                $args['service_ids'] = $type_posts;
            }
        }

        $args['orderby'] = 'checkin_date';
        $args['order']   = 'ASC';
        $args['limit']   = 10000;

        $bookings = Olo_Booking_DB::query( $args );
        $result = [];
        foreach ( $bookings as $b ) {
            $result[] = $this->format_booking( $b, true );
        }
        return new WP_REST_Response( $result, 200 );
    }

    public function import_bookings( $request ) {
        if ( ! Olo_Role_Manager::can( 'import_bookings' ) ) {
            return new WP_REST_Response( [ 'message' => 'Non hai il permesso di importare.' ], 403 );
        }

        $params = $request->get_json_params();
        $rows = $params['rows'] ?? [];

        if ( empty( $rows ) ) {
            return new WP_REST_Response( [ 'message' => 'Nessuna riga da importare.' ], 400 );
        }

        // Build service name → ID map
        $services = get_posts( [
            'post_type' => 'olo_service', 'post_status' => 'publish',
            'posts_per_page' => -1,
        ] );
        $svc_map = [];
        foreach ( $services as $s ) {
            $svc_map[ mb_strtolower( trim( $s->post_title ) ) ] = $s->ID;
        }

        $imported = 0;
        $errors = [];

        foreach ( $rows as $i => $row ) {
            $line = $i + 2; // CSV line (1-indexed + header)

            // Resolve service_id
            $service_id = 0;
            if ( ! empty( $row['service_id'] ) ) {
                $service_id = (int) $row['service_id'];
            } elseif ( ! empty( $row['struttura'] ) ) {
                $key = mb_strtolower( trim( $row['struttura'] ) );
                $service_id = $svc_map[ $key ] ?? 0;
            }

            if ( ! $service_id ) {
                $errors[] = "Riga {$line}: struttura non trovata.";
                continue;
            }

            if ( ! $this->check_service_access( $service_id ) ) {
                $errors[] = "Riga {$line}: non hai accesso a questa struttura.";
                continue;
            }

            $checkin  = sanitize_text_field( $row['checkin'] ?? $row['checkin_date'] ?? '' );
            $checkout = sanitize_text_field( $row['checkout'] ?? $row['checkout_date'] ?? '' );

            if ( ! $checkin || ! $checkout ) {
                $errors[] = "Riga {$line}: date check-in/check-out mancanti.";
                continue;
            }

            $guest_name = sanitize_text_field( $row['ospite'] ?? $row['guest_name'] ?? '' );
            if ( ! $guest_name ) {
                $errors[] = "Riga {$line}: nome ospite mancante.";
                continue;
            }

            $status_map = [
                'in attesa' => 'pending', 'confermata' => 'confirmed',
                'annullata' => 'cancelled', 'completata' => 'completed',
                'pending' => 'pending', 'confirmed' => 'confirmed',
                'cancelled' => 'cancelled', 'completed' => 'completed',
            ];
            $raw_status = mb_strtolower( trim( $row['stato'] ?? $row['status'] ?? 'pending' ) );
            $status = $status_map[ $raw_status ] ?? 'pending';

            $manager_id = (int) ( get_post_meta( $service_id, '_olo_service_manager', true ) ?: 0 );

            $id = Olo_Booking_DB::insert( [
                'service_id'      => $service_id,
                'guest_name'      => $guest_name,
                'guest_email'     => sanitize_email( $row['email'] ?? $row['guest_email'] ?? '' ),
                'guest_phone'     => sanitize_text_field( $row['telefono'] ?? $row['guest_phone'] ?? '' ),
                'guest_count'     => absint( $row['ospiti'] ?? $row['guest_count'] ?? 1 ),
                'checkin_date'    => $checkin,
                'checkout_date'   => $checkout,
                'price_per_night' => floatval( $row['prezzo_notte'] ?? $row['price_per_night'] ?? 0 ),
                'price_total'     => floatval( $row['prezzo_totale'] ?? $row['price_total'] ?? 0 ),
                'season_name'     => sanitize_text_field( $row['stagione'] ?? $row['season_name'] ?? '' ),
                'status'          => $status,
                'source'          => sanitize_text_field( $row['origine'] ?? $row['source'] ?? 'import' ),
                'notes'           => sanitize_textarea_field( $row['note'] ?? $row['notes'] ?? '' ),
                'internal_notes'  => sanitize_textarea_field( $row['note_interne'] ?? $row['internal_notes'] ?? '' ),
                'manager_id'      => $manager_id,
            ] );

            if ( $id ) {
                $imported++;
            } else {
                $errors[] = "Riga {$line}: errore nell'inserimento.";
            }
        }

        return new WP_REST_Response( [
            'success'  => true,
            'imported' => $imported,
            'errors'   => $errors,
            'total'    => count( $rows ),
        ], 200 );
    }

    /* =========================================================================
     *  Stats & Upcoming
     * ======================================================================= */

    public function get_stats( $request ) {
        $service_ids = $this->get_manager_service_ids();
        return new WP_REST_Response( Olo_Booking_DB::get_stats( $service_ids ), 200 );
    }

    public function get_upcoming( $request ) {
        $service_ids = $this->get_manager_service_ids();
        $days = absint( $request->get_param( 'days' ) ?: 14 );
        $events = Olo_Booking_DB::get_upcoming( $service_ids, $days );

        // Enrich with service name
        foreach ( $events as &$e ) {
            $svc = get_post( $e['service_id'] );
            $e['service_name'] = $svc ? $svc->post_title : '';
        }

        return new WP_REST_Response( $events, 200 );
    }

    /* =========================================================================
     *  Public Booking
     * ======================================================================= */

    public function public_book( $request ) {
        $params = $request->get_json_params();

        $required = [ 'service_id', 'guest_name', 'guest_email', 'checkin_date', 'checkout_date' ];
        foreach ( $required as $field ) {
            if ( empty( $params[ $field ] ) ) {
                return new WP_REST_Response( [ 'message' => "Campo obbligatorio: {$field}" ], 400 );
            }
        }

        $service_id = (int) $params['service_id'];
        $post = get_post( $service_id );
        if ( ! $post || $post->post_type !== 'olo_service' || $post->post_status !== 'publish' ) {
            return new WP_REST_Response( [ 'message' => 'Struttura non trovata.' ], 404 );
        }

        $checkin  = sanitize_text_field( $params['checkin_date'] );
        $checkout = sanitize_text_field( $params['checkout_date'] );
        $guests   = max( 1, absint( $params['guest_count'] ?? 1 ) );

        // Validate pricing and constraints
        $pricing = Olo_Price_Calculator::calculate( $service_id, $checkin, $checkout, $guests );
        if ( ! $pricing['success'] ) {
            return new WP_REST_Response( [ 'message' => implode( ' ', $pricing['errors'] ) ], 422 );
        }

        // Check availability
        if ( ! Olo_Booking_DB::is_available( $service_id, $checkin, $checkout ) ) {
            return new WP_REST_Response( [ 'message' => 'La struttura non è disponibile per le date selezionate.' ], 409 );
        }

        $manager_id = (int) ( get_post_meta( $service_id, '_olo_service_manager', true ) ?: 0 );

        $id = Olo_Booking_DB::insert( [
            'service_id'      => $service_id,
            'guest_name'      => sanitize_text_field( $params['guest_name'] ),
            'guest_email'     => sanitize_email( $params['guest_email'] ),
            'guest_phone'     => sanitize_text_field( $params['guest_phone'] ?? '' ),
            'guest_count'     => $guests,
            'checkin_date'    => $checkin,
            'checkout_date'   => $checkout,
            'price_per_night' => $pricing['price_per_night'],
            'price_total'     => $pricing['price_total'],
            'season_name'     => $pricing['season_name'],
            'status'          => 'pending',
            'source'          => 'website',
            'notes'           => sanitize_textarea_field( $params['notes'] ?? '' ),
            'manager_id'      => $manager_id,
        ] );

        if ( ! $id ) {
            return new WP_REST_Response( [ 'message' => 'Errore nella creazione della prenotazione.' ], 500 );
        }

        $booking = Olo_Booking_DB::get( $id );
        do_action( 'olo_booking_created', $booking, $post );

        return new WP_REST_Response( [
            'success' => true,
            'booking_id' => $id,
            'message' => 'Prenotazione inviata con successo! Riceverai una conferma via email.',
        ], 201 );
    }

    /* =========================================================================
     *  Users
     * ======================================================================= */

    public function get_users( $request ) {
        $level = Olo_Role_Manager::get_access_level();

        // Admin sees all roles; others see only managers
        $roles = ( $level === 'admin' )
            ? [ 'administrator', 'olo_supervisor', 'olo_manager' ]
            : [ 'olo_manager' ];

        $users = get_users( [
            'role__in' => $roles,
            'orderby'  => 'display_name',
            'order'    => 'ASC',
        ] );

        $result = [];
        foreach ( $users as $u ) {
            $result[] = $this->format_user( $u );
        }
        return new WP_REST_Response( $result, 200 );
    }

    public function get_user( $request ) {
        $user = get_userdata( (int) $request['id'] );
        if ( ! $user ) {
            return new WP_REST_Response( [ 'message' => 'Utente non trovato.' ], 404 );
        }
        return new WP_REST_Response( $this->format_user( $user ), 200 );
    }

    public function create_user( $request ) {
        if ( ! Olo_Role_Manager::can( 'create_users' ) ) {
            return new WP_REST_Response( [ 'message' => 'Non hai il permesso di creare utenti.' ], 403 );
        }

        $params = $request->get_json_params();

        if ( empty( $params['username'] ) || empty( $params['email'] ) || empty( $params['password'] ) ) {
            return new WP_REST_Response( [ 'message' => 'Username, email e password sono obbligatori.' ], 400 );
        }

        $level = Olo_Role_Manager::get_access_level();
        $role  = sanitize_text_field( $params['role'] ?? 'olo_manager' );

        // Non-admin can only create managers
        $allowed_roles = ( $level === 'admin' )
            ? [ 'olo_manager', 'olo_supervisor', 'administrator' ]
            : [ 'olo_manager' ];

        if ( ! in_array( $role, $allowed_roles, true ) ) {
            $role = 'olo_manager';
        }

        $user_id = wp_insert_user( [
            'user_login'   => sanitize_user( $params['username'] ),
            'user_email'   => sanitize_email( $params['email'] ),
            'user_pass'    => $params['password'],
            'display_name' => sanitize_text_field( $params['display_name'] ?? $params['username'] ),
            'role'         => $role,
        ] );

        if ( is_wp_error( $user_id ) ) {
            return new WP_REST_Response( [ 'message' => $user_id->get_error_message() ], 400 );
        }

        // Assign services if provided
        if ( Olo_Role_Manager::can( 'assign_services' ) && ! empty( $params['service_ids'] ) && is_array( $params['service_ids'] ) ) {
            $this->assign_services_to_user( $user_id, $params['service_ids'] );
        }

        $user = get_userdata( $user_id );
        return new WP_REST_Response( $this->format_user( $user ), 201 );
    }

    public function update_user( $request ) {
        $user_id = (int) $request['id'];
        $user    = get_userdata( $user_id );
        if ( ! $user ) {
            return new WP_REST_Response( [ 'message' => 'Utente non trovato.' ], 404 );
        }

        $level  = Olo_Role_Manager::get_access_level();
        $params = $request->get_json_params();

        // Non-admin can only modify managers
        if ( $level !== 'admin' ) {
            $target_level = Olo_Role_Manager::get_access_level( $user_id );
            if ( $target_level !== 'manager' ) {
                return new WP_REST_Response( [ 'message' => 'Puoi modificare solo i gestori.' ], 403 );
            }
        }

        // Prevent editing yourself out of admin
        if ( $user_id === get_current_user_id() ) {
            if ( isset( $params['role'] ) && $params['role'] !== 'administrator' ) {
                return new WP_REST_Response( [ 'message' => 'Non puoi modificare il tuo stesso ruolo.' ], 403 );
            }
        }

        // Profile fields (name, email, password) — require edit_user_profile
        if ( Olo_Role_Manager::can( 'edit_user_profile' ) ) {
            $user_data = [ 'ID' => $user_id ];
            if ( isset( $params['display_name'] ) ) {
                $user_data['display_name'] = sanitize_text_field( $params['display_name'] );
            }
            if ( isset( $params['email'] ) ) {
                $user_data['user_email'] = sanitize_email( $params['email'] );
            }
            if ( ! empty( $params['password'] ) ) {
                $user_data['user_pass'] = $params['password'];
            }
            if ( count( $user_data ) > 1 ) {
                $result = wp_update_user( $user_data );
                if ( is_wp_error( $result ) ) {
                    return new WP_REST_Response( [ 'message' => $result->get_error_message() ], 400 );
                }
            }
        }

        // Manager profile extra fields (bio, languages, contacts, photo)
        if ( Olo_Role_Manager::can( 'edit_user_profile' ) ) {
            if ( isset( $params['bio'] ) ) {
                update_user_meta( $user_id, 'olo_bio', sanitize_textarea_field( $params['bio'] ) );
            }
            if ( isset( $params['public_email'] ) ) {
                update_user_meta( $user_id, 'olo_public_email', sanitize_email( $params['public_email'] ) );
            }
            if ( isset( $params['public_phone'] ) ) {
                update_user_meta( $user_id, 'olo_public_phone', sanitize_text_field( $params['public_phone'] ) );
            }
            if ( isset( $params['languages'] ) && is_array( $params['languages'] ) ) {
                $langs = array_map( 'sanitize_text_field', $params['languages'] );
                update_user_meta( $user_id, 'olo_languages', $langs );
            }
            if ( isset( $params['photo_id'] ) ) {
                update_user_meta( $user_id, 'olo_photo_id', absint( $params['photo_id'] ) );
            }
        }

        // Role — admin only
        if ( $level === 'admin' && isset( $params['role'] ) ) {
            $new_role = sanitize_text_field( $params['role'] );
            if ( in_array( $new_role, [ 'olo_manager', 'olo_supervisor', 'administrator' ], true ) ) {
                $user->set_role( $new_role );
            }
        }

        // Service assignments — require assign_services
        if ( Olo_Role_Manager::can( 'assign_services' ) && isset( $params['service_ids'] ) && is_array( $params['service_ids'] ) ) {
            $this->assign_services_to_user( $user_id, $params['service_ids'] );
        }

        $updated = get_userdata( $user_id );
        return new WP_REST_Response( $this->format_user( $updated ), 200 );
    }

    public function delete_user( $request ) {
        if ( ! Olo_Role_Manager::can( 'delete_users' ) ) {
            return new WP_REST_Response( [ 'message' => 'Non hai il permesso di eliminare utenti.' ], 403 );
        }

        $user_id = (int) $request['id'];

        if ( $user_id === get_current_user_id() ) {
            return new WP_REST_Response( [ 'message' => 'Non puoi eliminare il tuo stesso account.' ], 403 );
        }

        $user = get_userdata( $user_id );
        if ( ! $user ) {
            return new WP_REST_Response( [ 'message' => 'Utente non trovato.' ], 404 );
        }

        // Non-admin can only delete managers
        if ( ! current_user_can( 'manage_options' ) ) {
            $target_level = Olo_Role_Manager::get_access_level( $user_id );
            if ( $target_level !== 'manager' ) {
                return new WP_REST_Response( [ 'message' => 'Puoi eliminare solo i gestori.' ], 403 );
            }
        }

        $this->assign_services_to_user( $user_id, [] );

        require_once ABSPATH . 'wp-admin/includes/user.php';
        wp_delete_user( $user_id );

        return new WP_REST_Response( [ 'success' => true ], 200 );
    }

    /* =========================================================================
     *  Theme
     * ======================================================================= */

    public function get_theme( $request ) {
        $theme = get_option( 'olo_manager_theme', [] );
        return new WP_REST_Response( $theme, 200 );
    }

    public function save_theme( $request ) {
        $params = $request->get_json_params();
        $theme  = get_option( 'olo_manager_theme', [] );

        // ── Colors ──
        if ( isset( $params['colors'] ) ) {
            $colors = [];
            $allowed = [ 'bg', 'card', 'sidebar', 'accent', 'primary', 'primaryDark', 'text', 'textMuted', 'headerBg', 'btnPrimary', 'btnSuccess', 'border' ];
            foreach ( $allowed as $key ) {
                if ( isset( $params['colors'][ $key ] ) ) {
                    $colors[ $key ] = sanitize_hex_color( $params['colors'][ $key ] ) ?: '';
                }
            }
            $theme['colors'] = $colors;
        }

        // ── Login customization ──
        if ( isset( $params['login'] ) ) {
            $login = [];
            if ( isset( $params['login']['logo_url'] ) ) {
                $login['logo_url'] = esc_url_raw( $params['login']['logo_url'] );
            }
            if ( isset( $params['login']['logo_height'] ) ) {
                $login['logo_height'] = max( 20, min( 120, absint( $params['login']['logo_height'] ) ) );
            }
            if ( isset( $params['login']['bg_color'] ) ) {
                $login['bg_color'] = sanitize_hex_color( $params['login']['bg_color'] ) ?: '';
            }
            if ( isset( $params['login']['bg_image_url'] ) ) {
                $login['bg_image_url'] = esc_url_raw( $params['login']['bg_image_url'] );
            }
            if ( isset( $params['login']['btn_color'] ) ) {
                $login['btn_color'] = sanitize_hex_color( $params['login']['btn_color'] ) ?: '';
            }
            if ( isset( $params['login']['slug'] ) ) {
                $raw = sanitize_title( $params['login']['slug'] );
                $login['slug'] = $raw ?: 'gestione';
            }
            $theme['login'] = $login;

            // Flush rewrite rules if slug changed
            $old_slug = ( get_option( 'olo_manager_theme', [] )['login']['slug'] ?? 'gestione' );
            $new_slug = $login['slug'] ?? 'gestione';
            if ( $old_slug !== $new_slug ) {
                // Save first, then flush
                update_option( 'olo_manager_theme', $theme );
                flush_rewrite_rules();
                return new WP_REST_Response( [ 'success' => true, 'slug_changed' => true, 'new_slug' => $new_slug ], 200 );
            }
        }

        update_option( 'olo_manager_theme', $theme );
        return new WP_REST_Response( [ 'success' => true ], 200 );
    }

    /* =========================================================================
     *  Amenities Catalog
     * ======================================================================= */

    public function get_amenities_catalog( $request ) {
        return new WP_REST_Response( Olo_Amenities_Catalog::get_catalog(), 200 );
    }

    public function save_amenities_catalog( $request ) {
        $params = $request->get_json_params();
        $ok = Olo_Amenities_Catalog::save_catalog( $params );
        if ( ! $ok ) {
            return new WP_REST_Response( [ 'message' => 'Dati catalogo non validi.' ], 400 );
        }
        return new WP_REST_Response( [ 'success' => true ], 200 );
    }

    /* =========================================================================
     *  Permissions
     * ======================================================================= */

    public function get_permissions( $request ) {
        $registry = Olo_Role_Manager::get_registry();
        $saved    = Olo_Role_Manager::get_saved_permissions();

        $perms = [];
        foreach ( $registry as $key => $def ) {
            $perms[] = [
                'key'        => $key,
                'label'      => $def['label'],
                'group'      => $def['group'],
                'supervisor' => $saved['supervisor'][ $key ] ?? $def['supervisor'],
                'manager'    => $saved['manager'][ $key ] ?? $def['manager'],
            ];
        }

        return new WP_REST_Response( $perms, 200 );
    }

    public function save_permissions( $request ) {
        $params = $request->get_json_params();
        Olo_Role_Manager::save_permissions( $params );
        return new WP_REST_Response( [ 'success' => true ], 200 );
    }

    /**
     * Assign services to a user (replace all existing assignments).
     */
    private function assign_services_to_user( $user_id, $new_service_ids ) {
        $new_ids = array_map( 'absint', $new_service_ids );

        // Remove user from all services currently assigned
        $current = get_posts( [
            'post_type'      => 'olo_service',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => [
                [ 'key' => '_olo_service_manager', 'value' => $user_id ],
            ],
        ] );

        foreach ( $current as $sid ) {
            if ( ! in_array( $sid, $new_ids, true ) ) {
                delete_post_meta( $sid, '_olo_service_manager' );
            }
        }

        // Assign user to new services
        foreach ( $new_ids as $sid ) {
            update_post_meta( $sid, '_olo_service_manager', $user_id );
        }
    }

    private function format_user( $user ) {
        $roles       = $user->roles;
        $access      = Olo_Role_Manager::get_access_level( $user->ID );
        $service_ids = Olo_Role_Manager::get_user_service_ids( $user->ID );

        // Get assigned service names
        $assigned = [];
        if ( is_array( $service_ids ) ) {
            foreach ( $service_ids as $sid ) {
                $post = get_post( $sid );
                if ( $post ) {
                    $assigned[] = [
                        'id'    => $sid,
                        'title' => $post->post_title,
                        'color' => get_post_meta( $sid, '_olo_service_color', true ) ?: '#6366F1',
                    ];
                }
            }
        }

        $role_labels = [
            'administrator'  => 'Amministratore',
            'olo_supervisor' => 'Supervisore',
            'olo_manager'    => 'Gestore',
        ];

        $primary_role = '';
        foreach ( [ 'administrator', 'olo_supervisor', 'olo_manager' ] as $r ) {
            if ( in_array( $r, $roles, true ) ) {
                $primary_role = $r;
                break;
            }
        }

        // Manager profile extra fields
        $photo_id  = get_user_meta( $user->ID, 'olo_photo_id', true );
        $photo_url = $photo_id ? wp_get_attachment_image_url( (int) $photo_id, 'thumbnail' ) : '';
        $languages = get_user_meta( $user->ID, 'olo_languages', true ) ?: [];
        if ( is_string( $languages ) ) $languages = json_decode( $languages, true ) ?: [];

        return [
            'id'            => $user->ID,
            'username'      => $user->user_login,
            'display_name'  => $user->display_name,
            'email'         => $user->user_email,
            'role'          => $primary_role,
            'role_label'    => $role_labels[ $primary_role ] ?? $primary_role,
            'access_level'  => $access,
            'services'      => $assigned,
            'registered'    => $user->user_registered,
            'photo_id'      => (int) $photo_id,
            'photo_url'     => $photo_url ?: '',
            'languages'     => $languages,
            'bio'           => get_user_meta( $user->ID, 'olo_bio', true ) ?: '',
            'public_email'  => get_user_meta( $user->ID, 'olo_public_email', true ) ?: '',
            'public_phone'  => get_user_meta( $user->ID, 'olo_public_phone', true ) ?: '',
        ];
    }

    /* =========================================================================
     *  Formatters
     * ======================================================================= */

    private function format_service_summary( $post ) {
        $id = $post->ID;
        $stats = Olo_Booking_DB::get_stats( [ $id ] );
        $image = get_the_post_thumbnail_url( $id, 'medium' );

        return [
            'id'              => $id,
            'title'           => $post->post_title,
            'image'           => $image ?: '',
            'service_type'    => get_post_meta( $id, '_olo_service_type', true ) ?: 'service',
            'capacity'        => intval( get_post_meta( $id, '_olo_service_capacity', true ) ),
            'bedrooms'        => intval( get_post_meta( $id, '_olo_service_bedrooms', true ) ),
            'price'           => get_post_meta( $id, '_olo_service_price', true ) ?: '',
            'color'           => get_post_meta( $id, '_olo_service_color', true ) ?: '#6366F1',
            'address'         => get_post_meta( $id, '_olo_service_address', true ) ?: '',
            'mushrooms'       => intval( get_post_meta( $id, '_olo_service_mushrooms', true ) ),
            'active_bookings' => ( $stats['pending'] ?? 0 ) + ( $stats['confirmed'] ?? 0 ),
            'pending'         => $stats['pending'] ?? 0,
        ];
    }

    private function format_service_full( $post ) {
        $id = $post->ID;
        $data = $this->format_service_summary( $post );

        // Add full details
        $data['service_type']  = get_post_meta( $id, '_olo_service_type', true ) ?: 'service';
        $data['description']   = $post->post_content;
        $data['excerpt']       = $post->post_excerpt;
        $data['beds']          = intval( get_post_meta( $id, '_olo_service_beds', true ) );
        $data['bathrooms']     = intval( get_post_meta( $id, '_olo_service_bathrooms', true ) );
        $data['sqm']           = intval( get_post_meta( $id, '_olo_service_sqm', true ) );
        $data['checkin_time']  = get_post_meta( $id, '_olo_service_checkin_time', true ) ?: '15:00';
        $data['checkout_time'] = get_post_meta( $id, '_olo_service_checkout_time', true ) ?: '10:00';
        $data['checkin_day']   = get_post_meta( $id, '_olo_service_checkin_day', true ) ?: '';
        $data['opening']       = get_post_meta( $id, '_olo_service_opening', true ) ?: '';
        $data['altitude']      = get_post_meta( $id, '_olo_service_altitude', true ) ?: '';
        $data['cipat']         = get_post_meta( $id, '_olo_service_cipat', true ) ?: '';
        $data['valley']        = get_post_meta( $id, '_olo_service_valley', true ) ?: '';
        $data['latitude']      = get_post_meta( $id, '_olo_service_latitude', true ) ?: '';
        $data['longitude']     = get_post_meta( $id, '_olo_service_longitude', true ) ?: '';
        $data['directions']    = get_post_meta( $id, '_olo_service_directions', true ) ?: '';
        $data['rules']         = get_post_meta( $id, '_olo_service_rules', true ) ?: '';
        $data['video_1']       = get_post_meta( $id, '_olo_service_video_1', true ) ?: '';
        $data['video_2']       = get_post_meta( $id, '_olo_service_video_2', true ) ?: '';
        $data['video_3']       = get_post_meta( $id, '_olo_service_video_3', true ) ?: '';

        // Club di Prodotto
        $data['club_group']    = get_post_meta( $id, '_olo_service_club_group', true ) ?: '';
        $data['club_category'] = get_post_meta( $id, '_olo_service_club_category', true ) ?: '';

        // Amenities
        $data['amenities'] = get_post_meta( $id, '_olo_service_amenities', true ) ?: [];
        $data['max_amenities'] = (int) ( get_post_meta( $id, '_olo_service_max_amenities', true ) ?: 0 );
        $data['enabled_amenity_cats'] = get_post_meta( $id, '_olo_service_enabled_amenity_cats', true ) ?: [];

        // Seasons
        $data['seasons'] = get_post_meta( $id, '_olo_service_seasons', true ) ?: [];

        // Closures
        $data['closures'] = get_post_meta( $id, '_olo_service_closures', true ) ?: [];

        // Gallery
        $gallery_ids = get_post_meta( $id, '_olo_service_gallery', true ) ?: [];
        $gallery = [];
        foreach ( $gallery_ids as $att_id ) {
            $thumb = wp_get_attachment_image_url( $att_id, 'thumbnail' );
            $full  = wp_get_attachment_image_url( $att_id, 'large' );
            if ( $thumb ) {
                $gallery[] = [ 'id' => (int) $att_id, 'thumb' => $thumb, 'full' => $full ];
            }
        }
        $data['gallery'] = $gallery;

        // Public permalink
        $data['permalink'] = get_permalink( $id );

        return $data;
    }

    private function format_booking( $b, $full = false ) {
        $service = get_post( $b['service_id'] );
        $data = [
            'id'              => (int) $b['id'],
            'service_id'      => (int) $b['service_id'],
            'service_name'    => $service ? $service->post_title : '',
            'service_color'   => get_post_meta( $b['service_id'], '_olo_service_color', true ) ?: '#6366F1',
            'guest_name'      => $b['guest_name'],
            'guest_email'     => $b['guest_email'],
            'guest_phone'     => $b['guest_phone'],
            'guest_count'     => (int) $b['guest_count'],
            'checkin_date'    => $b['checkin_date'],
            'checkout_date'   => $b['checkout_date'],
            'nights'          => (int) $b['nights'],
            'price_per_night' => (float) $b['price_per_night'],
            'price_total'     => (float) $b['price_total'],
            'season_name'     => $b['season_name'],
            'status'          => $b['status'],
            'source'          => $b['source'] ?? 'website',
            'created_at'      => $b['created_at'],
        ];

        if ( $full ) {
            $data['notes']          = $b['notes'];
            $data['internal_notes'] = $b['internal_notes'] ?? '';
            $data['manager_id']     = (int) $b['manager_id'];
            $data['updated_at']     = $b['updated_at'];
        }

        return $data;
    }
}
