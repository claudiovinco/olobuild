<?php
/**
 * Olo Booking — Database (v2 — soggiorni con date range)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Booking_DB {

    public static function table_name() {
        global $wpdb;
        return $wpdb->prefix . 'olo_bookings';
    }

    public static function log_table_name() {
        global $wpdb;
        return $wpdb->prefix . 'olo_booking_log';
    }

    /* =========================================================================
     *  Schema + Migration
     * ======================================================================= */

    public static function create_tables() {
        global $wpdb;
        $charset = $wpdb->get_charset_collate();
        $table   = self::table_name();
        $log     = self::log_table_name();

        $sql = "CREATE TABLE {$table} (
            id              BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            service_id      BIGINT(20) UNSIGNED NOT NULL,
            guest_name      VARCHAR(200) NOT NULL,
            guest_email     VARCHAR(200) NOT NULL DEFAULT '',
            guest_phone     VARCHAR(50) NOT NULL DEFAULT '',
            guest_count     TINYINT UNSIGNED NOT NULL DEFAULT 1,
            checkin_date    DATE NOT NULL,
            checkout_date   DATE NOT NULL,
            nights          SMALLINT UNSIGNED NOT NULL DEFAULT 1,
            price_per_night DECIMAL(10,2) NOT NULL DEFAULT 0,
            price_total     DECIMAL(10,2) NOT NULL DEFAULT 0,
            season_name     VARCHAR(100) NOT NULL DEFAULT '',
            status          VARCHAR(20) NOT NULL DEFAULT 'pending',
            source          VARCHAR(20) NOT NULL DEFAULT 'website',
            notes           TEXT,
            internal_notes  TEXT,
            manager_id      BIGINT(20) UNSIGNED DEFAULT 0,
            created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_service (service_id),
            KEY idx_checkin (checkin_date),
            KEY idx_checkout (checkout_date),
            KEY idx_status (status),
            KEY idx_manager (manager_id)
        ) {$charset};";

        $sql .= "CREATE TABLE {$log} (
            id          BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            booking_id  BIGINT(20) UNSIGNED NOT NULL,
            user_id     BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            action      VARCHAR(50) NOT NULL,
            details     TEXT,
            created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_booking (booking_id)
        ) {$charset};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
        update_option( 'olo_book_db_version', OLO_BOOK_DB_VERSION );
    }

    /**
     * Migrate from v1 (appointment-based) to v2 (date-range).
     */
    public static function maybe_upgrade() {
        $installed = get_option( 'olo_book_db_version', '0' );
        if ( version_compare( $installed, OLO_BOOK_DB_VERSION, '<' ) ) {
            self::create_tables();

            // Migrate v1 data if old columns exist
            if ( version_compare( $installed, '2.0.0', '<' ) ) {
                self::migrate_v1_to_v2();
            }
        }
    }

    private static function migrate_v1_to_v2() {
        global $wpdb;
        $table = self::table_name();

        // Check if old columns exist and new columns exist
        $cols = $wpdb->get_col( "SHOW COLUMNS FROM {$table}", 0 );
        $has_old = in_array( 'booking_date', $cols, true ) && in_array( 'time_start', $cols, true );
        $has_new = in_array( 'checkin_date', $cols, true );

        if ( $has_old && $has_new ) {
            // Migrate: booking_date → checkin_date, checkout = booking_date + 1
            $wpdb->query( "
                UPDATE {$table}
                SET checkin_date  = booking_date,
                    checkout_date = DATE_ADD(booking_date, INTERVAL 1 DAY),
                    nights        = 1,
                    guest_name    = COALESCE(customer_name, guest_name, ''),
                    guest_email   = COALESCE(customer_email, guest_email, ''),
                    guest_phone   = COALESCE(customer_phone, guest_phone, '')
                WHERE checkin_date = '0000-00-00' OR checkin_date IS NULL
            " );
        }

        // Also migrate old column customer_name → guest_name if the old column exists
        if ( in_array( 'customer_name', $cols, true ) && in_array( 'guest_name', $cols, true ) ) {
            $wpdb->query( "
                UPDATE {$table}
                SET guest_name  = customer_name,
                    guest_email = customer_email,
                    guest_phone = customer_phone
                WHERE guest_name = '' AND customer_name != ''
            " );
        }
    }

    /* =========================================================================
     *  CRUD
     * ======================================================================= */

    public static function insert( $data ) {
        global $wpdb;

        $nights = self::calc_nights( $data['checkin_date'], $data['checkout_date'] );

        $wpdb->insert( self::table_name(), [
            'service_id'      => absint( $data['service_id'] ),
            'guest_name'      => sanitize_text_field( $data['guest_name'] ),
            'guest_email'     => sanitize_email( $data['guest_email'] ?? '' ),
            'guest_phone'     => sanitize_text_field( $data['guest_phone'] ?? '' ),
            'guest_count'     => absint( $data['guest_count'] ?? 1 ),
            'checkin_date'    => sanitize_text_field( $data['checkin_date'] ),
            'checkout_date'   => sanitize_text_field( $data['checkout_date'] ),
            'nights'          => $nights,
            'price_per_night' => floatval( $data['price_per_night'] ?? 0 ),
            'price_total'     => floatval( $data['price_total'] ?? 0 ),
            'season_name'     => sanitize_text_field( $data['season_name'] ?? '' ),
            'status'          => sanitize_text_field( $data['status'] ?? 'pending' ),
            'source'          => sanitize_text_field( $data['source'] ?? 'website' ),
            'notes'           => sanitize_textarea_field( $data['notes'] ?? '' ),
            'internal_notes'  => sanitize_textarea_field( $data['internal_notes'] ?? '' ),
            'manager_id'      => absint( $data['manager_id'] ?? 0 ),
            'created_at'      => current_time( 'mysql' ),
            'updated_at'      => current_time( 'mysql' ),
        ] );

        $id = $wpdb->insert_id;

        if ( $id ) {
            self::add_log( $id, 'created', 'Prenotazione creata' . ( ! empty( $data['source'] ) ? ' (' . $data['source'] . ')' : '' ) );
        }

        return $id;
    }

    public static function update( $id, $data ) {
        global $wpdb;
        $allowed = [
            'service_id'      => '%d',
            'guest_name'      => '%s',
            'guest_email'     => '%s',
            'guest_phone'     => '%s',
            'guest_count'     => '%d',
            'checkin_date'    => '%s',
            'checkout_date'   => '%s',
            'nights'          => '%d',
            'price_per_night' => '%f',
            'price_total'     => '%f',
            'season_name'     => '%s',
            'status'          => '%s',
            'source'          => '%s',
            'notes'           => '%s',
            'internal_notes'  => '%s',
            'manager_id'      => '%d',
        ];

        $set     = [];
        $formats = [];
        foreach ( $allowed as $key => $fmt ) {
            if ( isset( $data[ $key ] ) ) {
                $set[ $key ] = $data[ $key ];
                $formats[]   = $fmt;
            }
        }

        // Recalculate nights if dates changed
        if ( isset( $data['checkin_date'] ) && isset( $data['checkout_date'] ) ) {
            $set['nights'] = self::calc_nights( $data['checkin_date'], $data['checkout_date'] );
            $formats[]     = '%d';
        }

        $set['updated_at'] = current_time( 'mysql' );
        $formats[]         = '%s';

        $result = $wpdb->update( self::table_name(), $set, [ 'id' => $id ], $formats, [ '%d' ] );

        // Log changes
        $changes = array_keys( $set );
        unset( $changes[ array_search( 'updated_at', $changes ) ] );
        if ( $changes ) {
            self::add_log( $id, 'updated', 'Campi modificati: ' . implode( ', ', $changes ) );
        }

        return $result;
    }

    public static function update_status( $id, $new_status ) {
        $old = self::get( $id );
        $result = self::update( $id, [ 'status' => $new_status ] );
        if ( $old ) {
            self::add_log( $id, 'status_changed', $old['status'] . ' → ' . $new_status );
        }
        return $result;
    }

    public static function delete( $id ) {
        global $wpdb;
        self::add_log( $id, 'deleted', 'Prenotazione eliminata' );
        return $wpdb->delete( self::table_name(), [ 'id' => $id ], [ '%d' ] );
    }

    public static function get( $id ) {
        global $wpdb;
        return $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM " . self::table_name() . " WHERE id = %d", $id
        ), ARRAY_A );
    }

    /* =========================================================================
     *  Query
     * ======================================================================= */

    public static function query( $args = [] ) {
        global $wpdb;
        $table  = self::table_name();
        $where  = [ '1=1' ];
        $values = [];

        if ( ! empty( $args['service_id'] ) ) {
            $where[]  = 'service_id = %d';
            $values[] = $args['service_id'];
        }

        if ( ! empty( $args['service_ids'] ) && is_array( $args['service_ids'] ) ) {
            $ph       = implode( ',', array_fill( 0, count( $args['service_ids'] ), '%d' ) );
            $where[]  = "service_id IN ({$ph})";
            $values   = array_merge( $values, $args['service_ids'] );
        }

        if ( ! empty( $args['status'] ) ) {
            if ( is_array( $args['status'] ) ) {
                $ph       = implode( ',', array_fill( 0, count( $args['status'] ), '%s' ) );
                $where[]  = "status IN ({$ph})";
                $values   = array_merge( $values, $args['status'] );
            } else {
                $where[]  = 'status = %s';
                $values[] = $args['status'];
            }
        }

        // Date range overlap: bookings that overlap with the given range
        if ( ! empty( $args['date_from'] ) && ! empty( $args['date_to'] ) ) {
            $where[]  = 'checkin_date <= %s AND checkout_date >= %s';
            $values[] = $args['date_to'];
            $values[] = $args['date_from'];
        } elseif ( ! empty( $args['date_from'] ) ) {
            $where[]  = 'checkout_date >= %s';
            $values[] = $args['date_from'];
        } elseif ( ! empty( $args['date_to'] ) ) {
            $where[]  = 'checkin_date <= %s';
            $values[] = $args['date_to'];
        }

        // Specific checkin range
        if ( ! empty( $args['checkin_from'] ) ) {
            $where[]  = 'checkin_date >= %s';
            $values[] = $args['checkin_from'];
        }
        if ( ! empty( $args['checkin_to'] ) ) {
            $where[]  = 'checkin_date <= %s';
            $values[] = $args['checkin_to'];
        }

        if ( ! empty( $args['manager_id'] ) ) {
            $where[]  = 'manager_id = %d';
            $values[] = $args['manager_id'];
        }

        if ( ! empty( $args['search'] ) ) {
            $like     = '%' . $wpdb->esc_like( $args['search'] ) . '%';
            $where[]  = '(guest_name LIKE %s OR guest_email LIKE %s OR guest_phone LIKE %s)';
            $values[] = $like;
            $values[] = $like;
            $values[] = $like;
        }

        $orderby = $args['orderby'] ?? 'checkin_date';
        $order   = strtoupper( $args['order'] ?? 'ASC' );
        if ( ! in_array( $order, [ 'ASC', 'DESC' ], true ) ) $order = 'ASC';
        $allowed_orderby = [ 'checkin_date', 'checkout_date', 'created_at', 'guest_name', 'status', 'price_total' ];
        if ( ! in_array( $orderby, $allowed_orderby, true ) ) $orderby = 'checkin_date';

        $limit_clause = '';
        if ( isset( $args['limit'] ) ) {
            $limit_clause = 'LIMIT ' . absint( $args['limit'] );
            if ( isset( $args['offset'] ) ) {
                $limit_clause = 'LIMIT ' . absint( $args['offset'] ) . ', ' . absint( $args['limit'] );
            }
        }

        $sql = "SELECT * FROM {$table} WHERE " . implode( ' AND ', $where ) . " ORDER BY {$orderby} {$order} {$limit_clause}";
        if ( $values ) {
            $sql = $wpdb->prepare( $sql, ...$values );
        }

        return $wpdb->get_results( $sql, ARRAY_A );
    }

    public static function count( $args = [] ) {
        global $wpdb;
        $table  = self::table_name();
        $where  = [ '1=1' ];
        $values = [];

        if ( ! empty( $args['service_id'] ) ) {
            $where[]  = 'service_id = %d';
            $values[] = $args['service_id'];
        }
        if ( ! empty( $args['service_ids'] ) && is_array( $args['service_ids'] ) ) {
            $ph       = implode( ',', array_fill( 0, count( $args['service_ids'] ), '%d' ) );
            $where[]  = "service_id IN ({$ph})";
            $values   = array_merge( $values, $args['service_ids'] );
        }
        if ( ! empty( $args['status'] ) ) {
            $where[]  = 'status = %s';
            $values[] = $args['status'];
        }

        $sql = "SELECT COUNT(*) FROM {$table} WHERE " . implode( ' AND ', $where );
        if ( $values ) {
            $sql = $wpdb->prepare( $sql, ...$values );
        }
        return (int) $wpdb->get_var( $sql );
    }

    /* =========================================================================
     *  Availability check
     * ======================================================================= */

    /**
     * Check if service is available for the given date range.
     * Returns true if no overlapping confirmed/pending bookings exist.
     */
    public static function is_available( $service_id, $checkin, $checkout, $exclude_id = 0 ) {
        global $wpdb;
        $table = self::table_name();

        $count = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table}
             WHERE service_id = %d
             AND status IN ('pending', 'confirmed')
             AND checkin_date < %s
             AND checkout_date > %s
             AND id != %d",
            $service_id, $checkout, $checkin, $exclude_id
        ) );

        return $count === 0;
    }

    /**
     * Get all booked date ranges for a service in a given period.
     */
    public static function get_booked_ranges( $service_id, $from, $to ) {
        global $wpdb;
        $table = self::table_name();

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT id, checkin_date, checkout_date, guest_name, status, guest_count
             FROM {$table}
             WHERE service_id = %d
             AND status IN ('pending', 'confirmed')
             AND checkin_date < %s
             AND checkout_date > %s
             ORDER BY checkin_date ASC",
            $service_id, $to, $from
        ), ARRAY_A );
    }

    /* =========================================================================
     *  Stats
     * ======================================================================= */

    public static function get_stats( $service_ids = null ) {
        global $wpdb;
        $table = self::table_name();
        $where = '';

        if ( is_array( $service_ids ) ) {
            if ( empty( $service_ids ) ) {
                return [ 'total' => 0, 'pending' => 0, 'confirmed' => 0, 'cancelled' => 0, 'today_checkins' => 0, 'today_checkouts' => 0, 'revenue_month' => 0 ];
            }
            $ph    = implode( ',', array_fill( 0, count( $service_ids ), '%d' ) );
            $where = $wpdb->prepare( " AND service_id IN ({$ph})", ...$service_ids );
        }

        $today = current_time( 'Y-m-d' );
        $month_start = current_time( 'Y-m-01' );
        $month_end   = current_time( 'Y-m-t' );

        return [
            'total'           => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE 1=1 {$where}" ),
            'pending'         => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status = 'pending' {$where}" ),
            'confirmed'       => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status = 'confirmed' {$where}" ),
            'cancelled'       => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status = 'cancelled' {$where}" ),
            'today_checkins'  => (int) $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table} WHERE checkin_date = %s AND status IN ('pending','confirmed') {$where}", $today
            ) ),
            'today_checkouts' => (int) $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table} WHERE checkout_date = %s AND status IN ('confirmed','completed') {$where}", $today
            ) ),
            'revenue_month'   => (float) $wpdb->get_var( $wpdb->prepare(
                "SELECT COALESCE(SUM(price_total), 0) FROM {$table} WHERE status IN ('confirmed','completed') AND checkin_date >= %s AND checkin_date <= %s {$where}",
                $month_start, $month_end
            ) ),
        ];
    }

    /**
     * Upcoming arrivals/departures.
     */
    public static function get_upcoming( $service_ids = null, $days = 14 ) {
        global $wpdb;
        $table = self::table_name();
        $today = current_time( 'Y-m-d' );
        $until = date( 'Y-m-d', strtotime( $today . ' +' . absint( $days ) . ' days' ) );

        $where_svc = '';
        $values = [ $today, $until, $today, $until ];

        if ( is_array( $service_ids ) ) {
            if ( empty( $service_ids ) ) return [];
            $ph = implode( ',', array_fill( 0, count( $service_ids ), '%d' ) );
            $where_svc = " AND service_id IN ({$ph})";
            $values = array_merge( $values, $service_ids );
        }

        $sql = "
            (SELECT id, service_id, guest_name, guest_count, checkin_date AS event_date, 'checkin' AS event_type, status, nights, price_total
             FROM {$table} WHERE checkin_date >= %s AND checkin_date <= %s AND status IN ('pending','confirmed') {$where_svc})
            UNION ALL
            (SELECT id, service_id, guest_name, guest_count, checkout_date AS event_date, 'checkout' AS event_type, status, nights, price_total
             FROM {$table} WHERE checkout_date >= %s AND checkout_date <= %s AND status IN ('confirmed','completed') {$where_svc})
            ORDER BY event_date ASC, event_type ASC
            LIMIT 30
        ";

        return $wpdb->get_results( $wpdb->prepare( $sql, ...$values ), ARRAY_A );
    }

    /* =========================================================================
     *  Log
     * ======================================================================= */

    public static function add_log( $booking_id, $action, $details = '' ) {
        global $wpdb;
        $wpdb->insert( self::log_table_name(), [
            'booking_id' => absint( $booking_id ),
            'user_id'    => get_current_user_id(),
            'action'     => sanitize_text_field( $action ),
            'details'    => sanitize_text_field( $details ),
            'created_at' => current_time( 'mysql' ),
        ] );
    }

    public static function get_log( $booking_id ) {
        global $wpdb;
        return $wpdb->get_results( $wpdb->prepare(
            "SELECT l.*, u.display_name AS user_name
             FROM " . self::log_table_name() . " l
             LEFT JOIN {$wpdb->users} u ON u.ID = l.user_id
             WHERE l.booking_id = %d
             ORDER BY l.created_at DESC",
            $booking_id
        ), ARRAY_A );
    }

    /* =========================================================================
     *  Helpers
     * ======================================================================= */

    private static function calc_nights( $checkin, $checkout ) {
        $d1 = new DateTime( $checkin );
        $d2 = new DateTime( $checkout );
        $diff = $d1->diff( $d2 );
        return max( 1, $diff->days );
    }
}
