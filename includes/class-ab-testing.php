<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * A/B Testing Framework for Olobuild.
 *
 * Allows creating experiments where a tile has 2+ variants.
 * The system shows a random variant to visitors and tracks conversions.
 */
class Olo_AB_Testing {

    /**
     * Cookie duration: 30 days.
     */
    const COOKIE_DURATION = 2592000;

    /**
     * Initialize hooks.
     */
    public static function init() {
        self::maybe_create_table();
        add_action( 'rest_api_init', [ __CLASS__, 'register_routes' ] );
        add_action( 'wp_footer', [ __CLASS__, 'output_tracking_script' ], 98 );
    }

    /**
     * Create the ab_tests table if it doesn't exist.
     */
    public static function maybe_create_table() {
        global $wpdb;
        $table = self::table();
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- tabella custom del plugin (olo_ab_tests); SHOW TABLES non supporta placeholder, $table costruito da $wpdb->prefix; controllo esistenza tabella non cacheabile.
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) === $table ) {
            return;
        }
        $charset = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE {$table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            template_id BIGINT UNSIGNED NOT NULL,
            tile_id VARCHAR(100) NOT NULL,
            name VARCHAR(200) NOT NULL DEFAULT '',
            variants LONGTEXT NOT NULL,
            goal_type VARCHAR(50) NOT NULL DEFAULT 'click',
            goal_selector VARCHAR(200) DEFAULT '',
            views_a INT UNSIGNED NOT NULL DEFAULT 0,
            views_b INT UNSIGNED NOT NULL DEFAULT 0,
            conversions_a INT UNSIGNED NOT NULL DEFAULT 0,
            conversions_b INT UNSIGNED NOT NULL DEFAULT 0,
            status VARCHAR(20) NOT NULL DEFAULT 'draft',
            winning_variant VARCHAR(100) DEFAULT NULL,
            started_at DATETIME DEFAULT NULL,
            ended_at DATETIME DEFAULT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_tile_template (tile_id, template_id),
            KEY idx_status (status)
        ) {$charset};";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    /**
     * Get the ab_tests table name.
     */
    private static function table() {
        global $wpdb;
        return $wpdb->prefix . 'olo_ab_tests';
    }

    /**
     * Register REST API routes.
     */
    public static function register_routes() {
        $namespace = 'olo/v1';

        // List all tests
        register_rest_route( $namespace, '/ab-tests', [
            [
                'methods'             => 'GET',
                'callback'            => [ __CLASS__, 'get_tests' ],
                'permission_callback' => [ __CLASS__, 'check_permission' ],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [ __CLASS__, 'create_test' ],
                'permission_callback' => [ __CLASS__, 'check_permission' ],
            ],
        ] );

        // Single test CRUD
        register_rest_route( $namespace, '/ab-tests/(?P<id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [ __CLASS__, 'get_test' ],
                'permission_callback' => [ __CLASS__, 'check_permission' ],
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [ __CLASS__, 'update_test' ],
                'permission_callback' => [ __CLASS__, 'check_permission' ],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [ __CLASS__, 'delete_test' ],
                'permission_callback' => [ __CLASS__, 'check_permission' ],
            ],
        ] );

        // Start/stop test
        register_rest_route( $namespace, '/ab-tests/(?P<id>\d+)/start', [
            'methods'             => 'POST',
            'callback'            => [ __CLASS__, 'start_test' ],
            'permission_callback' => [ __CLASS__, 'check_permission' ],
        ] );

        register_rest_route( $namespace, '/ab-tests/(?P<id>\d+)/stop', [
            'methods'             => 'POST',
            'callback'            => [ __CLASS__, 'stop_test' ],
            'permission_callback' => [ __CLASS__, 'check_permission' ],
        ] );

        // Stats
        register_rest_route( $namespace, '/ab-tests/(?P<id>\d+)/stats', [
            'methods'             => 'GET',
            'callback'            => [ __CLASS__, 'get_stats' ],
            'permission_callback' => [ __CLASS__, 'check_permission' ],
        ] );

        // Track endpoint (public, no auth)
        register_rest_route( $namespace, '/ab-tests/track', [
            'methods'             => 'POST',
            'callback'            => [ __CLASS__, 'track_event' ],
            'permission_callback' => '__return_true',
        ] );
    }

    /**
     * Permission check: requires edit_pages capability.
     */
    public static function check_permission() {
        return current_user_can( 'edit_pages' );
    }

    /**
     * Get all A/B tests.
     */
    public static function get_tests() {
        global $wpdb;
        $table = self::table();
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- tabella custom del plugin (olo_ab_tests); nessun equivalente WP_Query; $table costruito da $wpdb->prefix, nessun valore utente interpolato; risultato non cacheabile.
        $rows  = $wpdb->get_results( "SELECT * FROM `$table` ORDER BY created_at DESC", ARRAY_A );

        if ( ! is_array( $rows ) ) {
            $rows = [];
        }

        foreach ( $rows as &$row ) {
            $row['variants'] = json_decode( $row['variants'], true ) ?: [];
        }
        unset( $row );

        return rest_ensure_response( $rows );
    }

    /**
     * Get a single test.
     */
    public static function get_test( $request ) {
        global $wpdb;
        $table = self::table();
        $id    = absint( $request['id'] );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- tabella custom del plugin (olo_ab_tests); solo $table (da $wpdb->prefix) interpolato, l'id passa da $wpdb->prepare con placeholder %d; risultato non cacheabile.
        $row   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $id ), ARRAY_A );

        if ( ! $row ) {
            return new WP_Error( 'not_found', 'Test A/B non trovato.', [ 'status' => 404 ] );
        }

        $row['variants'] = json_decode( $row['variants'], true ) ?: [];
        return rest_ensure_response( $row );
    }

    /**
     * Create a new A/B test.
     */
    public static function create_test( $request ) {
        global $wpdb;
        $table = self::table();
        $body  = $request->get_json_params();

        $name        = sanitize_text_field( $body['name'] ?? 'Test A/B' );
        $tile_id     = sanitize_text_field( $body['tile_id'] ?? '' );
        $template_id = absint( $body['template_id'] ?? 0 );
        $variants    = $body['variants'] ?? [ 'a' => [], 'b' => [] ];
        $goal_type   = sanitize_text_field( $body['goal_type'] ?? 'click' );
        $goal_sel    = sanitize_text_field( $body['goal_selector'] ?? '' );

        if ( empty( $tile_id ) ) {
            return new WP_Error( 'missing_tile_id', 'Tile ID obbligatorio.', [ 'status' => 400 ] );
        }
        if ( ! $template_id ) {
            return new WP_Error( 'missing_template_id', 'Template ID obbligatorio.', [ 'status' => 400 ] );
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- tabella custom del plugin (olo_ab_tests); $wpdb->insert con formati espliciti; scrittura, nessuna cache da gestire.
        $wpdb->insert( $table, [
            'name'          => $name,
            'tile_id'       => $tile_id,
            'template_id'   => $template_id,
            'variants'      => wp_json_encode( $variants ),
            'goal_type'     => $goal_type,
            'goal_selector' => $goal_sel,
            'status'        => 'draft',
            'created_at'    => current_time( 'mysql' ),
        ], [ '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s' ] );

        $id = $wpdb->insert_id;
        if ( ! $id ) {
            return new WP_Error( 'create_failed', 'Impossibile creare il test.', [ 'status' => 500 ] );
        }

        return rest_ensure_response( [ 'id' => $id, 'name' => $name ] );
    }

    /**
     * Update an A/B test.
     */
    public static function update_test( $request ) {
        global $wpdb;
        $table = self::table();
        $id    = absint( $request['id'] );
        $body  = $request->get_json_params();

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- tabella custom del plugin (olo_ab_tests); solo $table (da $wpdb->prefix) interpolato, l'id passa da $wpdb->prepare con placeholder %d; risultato non cacheabile.
        $existing = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $id ), ARRAY_A );
        if ( ! $existing ) {
            return new WP_Error( 'not_found', 'Test A/B non trovato.', [ 'status' => 404 ] );
        }

        $data    = [];
        $formats = [];

        if ( isset( $body['name'] ) ) {
            $data['name'] = sanitize_text_field( $body['name'] );
            $formats[]    = '%s';
        }
        if ( isset( $body['variants'] ) ) {
            $data['variants'] = wp_json_encode( $body['variants'] );
            $formats[]        = '%s';
        }
        if ( isset( $body['goal_type'] ) ) {
            $data['goal_type'] = sanitize_text_field( $body['goal_type'] );
            $formats[]         = '%s';
        }
        if ( isset( $body['goal_selector'] ) ) {
            $data['goal_selector'] = sanitize_text_field( $body['goal_selector'] );
            $formats[]             = '%s';
        }

        if ( ! empty( $data ) ) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- tabella custom del plugin (olo_ab_tests); $wpdb->update con formati espliciti; scrittura, nessuna cache da gestire.
            $wpdb->update( $table, $data, [ 'id' => $id ], $formats, [ '%d' ] );
        }

        return rest_ensure_response( [ 'success' => true ] );
    }

    /**
     * Delete an A/B test.
     */
    public static function delete_test( $request ) {
        global $wpdb;
        $table = self::table();
        $id    = absint( $request['id'] );

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- tabella custom del plugin (olo_ab_tests); $wpdb->delete con formato esplicito; scrittura, nessuna cache da gestire.
        $wpdb->delete( $table, [ 'id' => $id ], [ '%d' ] );

        return rest_ensure_response( [ 'deleted' => true ] );
    }

    /**
     * Start a test (set status to 'running').
     */
    public static function start_test( $request ) {
        global $wpdb;
        $table = self::table();
        $id    = absint( $request['id'] );

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- tabella custom del plugin (olo_ab_tests); $wpdb->update con formati espliciti; scrittura, nessuna cache da gestire.
        $wpdb->update( $table, [
            'status'     => 'running',
            'started_at' => current_time( 'mysql' ),
        ], [ 'id' => $id ], [ '%s', '%s' ], [ '%d' ] );

        return rest_ensure_response( [ 'status' => 'running' ] );
    }

    /**
     * Stop a test (set status to 'stopped').
     */
    public static function stop_test( $request ) {
        global $wpdb;
        $table = self::table();
        $id    = absint( $request['id'] );

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- tabella custom del plugin (olo_ab_tests); $wpdb->update con formati espliciti; scrittura, nessuna cache da gestire.
        $wpdb->update( $table, [
            'status'   => 'stopped',
            'ended_at' => current_time( 'mysql' ),
        ], [ 'id' => $id ], [ '%s', '%s' ], [ '%d' ] );

        return rest_ensure_response( [ 'status' => 'stopped' ] );
    }

    /**
     * Get stats for a test with statistical significance.
     */
    public static function get_stats( $request ) {
        global $wpdb;
        $table = self::table();
        $id    = absint( $request['id'] );

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- tabella custom del plugin (olo_ab_tests); solo $table (da $wpdb->prefix) interpolato, l'id passa da $wpdb->prepare con placeholder %d; risultato non cacheabile.
        $test = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $id ), ARRAY_A );
        if ( ! $test ) {
            return new WP_Error( 'not_found', 'Test A/B non trovato.', [ 'status' => 404 ] );
        }

        $views_a = intval( $test['views_a'] );
        $views_b = intval( $test['views_b'] );
        $conv_a  = intval( $test['conversions_a'] );
        $conv_b  = intval( $test['conversions_b'] );

        $rate_a = $views_a > 0 ? $conv_a / $views_a : 0;
        $rate_b = $views_b > 0 ? $conv_b / $views_b : 0;

        // Statistical significance: z-test for two proportions
        $p_value    = null;
        $significant = false;
        $winner     = null;

        $total_views = $views_a + $views_b;
        if ( $total_views > 0 ) {
            if ( $views_a >= 100 ) {
                if ( $views_b >= 100 ) {
                    $p_pooled = ( $conv_a + $conv_b ) / $total_views;
                    $se       = sqrt( $p_pooled * ( 1 - $p_pooled ) * ( 1 / $views_a + 1 / $views_b ) );

                    if ( $se > 0 ) {
                        $z       = abs( $rate_a - $rate_b ) / $se;
                        $p_value = self::z_to_p_value( $z );

                        if ( $p_value < 0.05 ) {
                            $significant = true;
                            $winner      = $rate_a > $rate_b ? 'a' : 'b';
                        }
                    }
                }
            }
        }

        return rest_ensure_response( [
            'test_id'     => $id,
            'name'        => $test['name'],
            'status'      => $test['status'],
            'variant_a'   => [
                'views'           => $views_a,
                'conversions'     => $conv_a,
                'conversion_rate' => round( $rate_a * 100, 2 ),
            ],
            'variant_b'   => [
                'views'           => $views_b,
                'conversions'     => $conv_b,
                'conversion_rate' => round( $rate_b * 100, 2 ),
            ],
            'p_value'     => $p_value !== null ? round( $p_value, 4 ) : null,
            'significant' => $significant,
            'winner'      => $winner,
            'started_at'  => $test['started_at'],
            'ended_at'    => $test['ended_at'],
        ] );
    }

    /**
     * Track a view or conversion event (public endpoint).
     * Rate limited: max 1 view per IP per test per hour.
     */
    public static function track_event( $request ) {
        $body    = $request->get_json_params();
        $test_id = absint( $body['test_id'] ?? 0 );
        $variant = sanitize_text_field( $body['variant'] ?? '' );
        $event   = sanitize_text_field( $body['event'] ?? '' );

        if ( ! $test_id ) {
            return new WP_Error( 'missing_test_id', 'test_id obbligatorio.', [ 'status' => 400 ] );
        }
        if ( ! in_array( $variant, [ 'a', 'b' ], true ) ) {
            return new WP_Error( 'invalid_variant', 'variant deve essere "a" o "b".', [ 'status' => 400 ] );
        }
        if ( ! in_array( $event, [ 'view', 'conversion' ], true ) ) {
            return new WP_Error( 'invalid_event', 'event deve essere "view" o "conversion".', [ 'status' => 400 ] );
        }

        // Rate limiting: max 1 per IP per test per event type per hour
        $ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0.0.0.0';
        $key = 'olo_ab_rl_' . md5( $ip . '_' . $test_id . '_' . $event );
        $existing = get_transient( $key );
        if ( $existing ) {
            return rest_ensure_response( [ 'tracked' => false, 'reason' => 'rate_limited' ] );
        }
        set_transient( $key, 1, HOUR_IN_SECONDS );

        global $wpdb;
        $table = self::table();

        // Verify test exists and is running
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- tabella custom del plugin (olo_ab_tests); solo $table (da $wpdb->prefix) interpolato, l'id passa da $wpdb->prepare con placeholder %d; risultato non cacheabile.
        $test = $wpdb->get_row( $wpdb->prepare( "SELECT status FROM $table WHERE id = %d", $test_id ), ARRAY_A );
        if ( ! $test ) {
            return new WP_Error( 'not_found', 'Test non trovato.', [ 'status' => 404 ] );
        }
        if ( $test['status'] !== 'running' ) {
            return rest_ensure_response( [ 'tracked' => false, 'reason' => 'test_not_running' ] );
        }

        // Increment the appropriate counter
        $column = '';
        if ( $event === 'view' ) {
            $column = 'views_' . $variant;
        } else {
            $column = 'conversions_' . $variant;
        }

        // Safe column name (already validated $variant is 'a' or 'b')
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- tabella custom del plugin (olo_ab_tests); $table da $wpdb->prefix e $column whitelist (views_a/views_b/conversions_a/conversions_b, $variant validato 'a'/'b' in_array), l'id passa da $wpdb->prepare con placeholder %d; scrittura, nessuna cache da gestire.
        $wpdb->query( $wpdb->prepare(
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- query su tabella custom del plugin: nome tabella da $wpdb->prefix / colonna whitelist; tutti i valori utente passano da $wpdb->prepare
            "UPDATE $table SET {$column} = {$column} + 1 WHERE id = %d",
            $test_id
        ) );

        return rest_ensure_response( [ 'tracked' => true ] );
    }

    /**
     * Get the variant assigned to current visitor for a given test.
     * Returns 'a' or 'b'. Assigns randomly if no cookie exists.
     *
     * @param int $test_id The test ID.
     * @return string The variant ('a' or 'b').
     */
    public static function get_visitor_variant( $test_id ) {
        $cookie_name = 'olo_ab_' . intval( $test_id );

        if ( isset( $_COOKIE[ $cookie_name ] ) ) {
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only del cookie di assegnazione variante per rendering frontend; nessuna modifica di stato; valore sanitizzato e validato in whitelist.
            $variant = sanitize_text_field( wp_unslash( $_COOKIE[ $cookie_name ] ) );
            if ( in_array( $variant, [ 'a', 'b' ], true ) ) {
                return $variant;
            }
        }

        // Assign random variant (50/50)
        $variant = wp_rand( 0, 1 ) === 0 ? 'a' : 'b';

        // Set cookie for 30 days (HttpOnly + SameSite=Lax for security)
        // NOTA: NO && — uso if annidati
        if ( ! headers_sent() ) {
            setcookie( $cookie_name, $variant, [
                'expires'  => time() + self::COOKIE_DURATION,
                'path'     => '/',
                'domain'   => '',
                'secure'   => is_ssl(),
                'httponly'  => true,
                'samesite'  => 'Lax',
            ] );
        }

        return $variant;
    }

    /**
     * Check if a tile has an active A/B test. If so, return the variant settings.
     *
     * @param string $tile_id The tile ID.
     * @param int    $template_id The template ID.
     * @return array|null Array with test_id, variant, and settings, or null if no active test.
     */
    public static function get_active_test_for_tile( $tile_id, $template_id ) {
        global $wpdb;
        $table = self::table();

        // Guard: skip query if table doesn't exist yet
        $suppress = $wpdb->suppress_errors( true );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- tabella custom del plugin (olo_ab_tests); solo $table (da $wpdb->prefix) interpolato, tile_id/template_id passano da $wpdb->prepare con placeholder %s/%d; risultato non cacheabile.
        $test = $wpdb->get_row( $wpdb->prepare(
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- query su tabella custom del plugin: nome tabella da $wpdb->prefix / colonna whitelist; tutti i valori utente passano da $wpdb->prepare
            "SELECT * FROM $table WHERE tile_id = %s AND template_id = %d AND status = 'running' LIMIT 1",
            $tile_id,
            $template_id
        ), ARRAY_A );
        $wpdb->suppress_errors( $suppress );

        if ( ! $test ) {
            return null;
        }

        $variants = json_decode( $test['variants'], true );
        if ( ! is_array( $variants ) ) {
            return null;
        }

        $variant = self::get_visitor_variant( intval( $test['id'] ) );

        if ( ! isset( $variants[ $variant ] ) ) {
            return null;
        }

        return [
            'test_id'  => intval( $test['id'] ),
            'variant'  => $variant,
            'settings' => $variants[ $variant ],
            'goal_type'     => $test['goal_type'],
            'goal_selector' => $test['goal_selector'],
        ];
    }

    /**
     * Output client-side tracking script for A/B tests.
     * Sends view/conversion events via AJAX.
     * NOTA: NO && — solo if annidati.
     */
    public static function output_tracking_script() {
        if ( is_admin() ) {
            return;
        }

        ?>
<script>
(function(){
    "use strict";

    var abElements = document.querySelectorAll("[data-olo-ab-test]");
    if (!abElements.length) return;

    var restUrl = (typeof oloData !== "undefined") ? oloData.restUrl : "/wp-json/";

    abElements.forEach(function(el) {
        var testId = parseInt(el.getAttribute("data-olo-ab-test"), 10);
        var variant = el.getAttribute("data-olo-ab-variant") || "a";
        var goalType = el.getAttribute("data-olo-ab-goal") || "click";
        var goalSelector = el.getAttribute("data-olo-ab-goal-selector") || "";

        if (!testId) return;

        /* Track view */
        fetch(restUrl + "olo/v1/ab-tests/track", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({test_id: testId, variant: variant, event: "view"})
        }).catch(function(){});

        /* Track conversion */
        if (goalType === "click") {
            var target = goalSelector ? el.querySelector(goalSelector) : el;
            if (target) {
                target.addEventListener("click", function() {
                    fetch(restUrl + "olo/v1/ab-tests/track", {
                        method: "POST",
                        headers: {"Content-Type": "application/json"},
                        body: JSON.stringify({test_id: testId, variant: variant, event: "conversion"})
                    }).catch(function(){});
                }, {once: true});
            }
        }

        if (goalType === "submit") {
            var form = goalSelector ? el.querySelector(goalSelector) : el.querySelector("form");
            if (form) {
                form.addEventListener("submit", function() {
                    fetch(restUrl + "olo/v1/ab-tests/track", {
                        method: "POST",
                        headers: {"Content-Type": "application/json"},
                        body: JSON.stringify({test_id: testId, variant: variant, event: "conversion"})
                    }).catch(function(){});
                }, {once: true});
            }
        }
    });
})();
</script>
        <?php
    }

    /**
     * Approximate z-score to two-tailed p-value.
     * Uses rational approximation for the normal CDF.
     *
     * @param float $z The z-score (absolute value).
     * @return float The two-tailed p-value.
     */
    private static function z_to_p_value( $z ) {
        $z = abs( $z );

        // Approximation of the standard normal CDF using Abramowitz & Stegun
        $t = 1.0 / ( 1.0 + 0.2316419 * $z );
        $d = 0.3989422804014327; // 1 / sqrt(2*pi)
        $p = $d * exp( -$z * $z / 2.0 ) *
             ( $t * ( 0.3193815 + $t * ( -0.3565638 + $t * ( 1.781478 + $t * ( -1.821256 + $t * 1.330274 ) ) ) ) );

        return 2.0 * $p; // Two-tailed
    }
}
