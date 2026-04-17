<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Database {

    /**
     * Singleton instance.
     */
    private static $instance = null;

    /**
     * Get singleton instance.
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Cache group for all Olo queries.
     */
    private $cache_group = 'olo';

    /**
     * Cache TTL in seconds (1 hour).
     */
    private $cache_ttl = 3600;

    private function table_templates() {
        global $wpdb;
        return $wpdb->prefix . 'olo_templates';
    }

    private function table_revisions() {
        global $wpdb;
        return $wpdb->prefix . 'olo_revisions';
    }

    public function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql_templates = "CREATE TABLE {$this->table_templates()} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            title VARCHAR(255) NOT NULL DEFAULT '',
            type VARCHAR(50) NOT NULL DEFAULT 'page',
            content LONGTEXT NOT NULL,
            settings LONGTEXT NOT NULL,
            thumbnail VARCHAR(500) DEFAULT '',
            status VARCHAR(20) NOT NULL DEFAULT 'draft',
            author_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_status (status),
            KEY idx_type (type),
            KEY idx_author_id (author_id)
        ) $charset_collate;";

        $sql_revisions = "CREATE TABLE {$this->table_revisions()} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            template_id BIGINT(20) UNSIGNED NOT NULL,
            content LONGTEXT NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_template_id (template_id),
            KEY idx_template_created (template_id, created_at)
        ) $charset_collate;";

        // Form submissions table
        $submissions = $wpdb->prefix . 'olo_submissions';
        $sql_submissions = "CREATE TABLE $submissions (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            form_id varchar(100) NOT NULL DEFAULT '',
            data longtext NOT NULL,
            ip_address varchar(100) DEFAULT '',
            created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            PRIMARY KEY (id),
            KEY form_id (form_id),
            KEY created_at (created_at)
        ) $charset_collate;";

        // Global widgets table
        $global_widgets = $wpdb->prefix . 'olo_global_widgets';
        $sql_global_widgets = "CREATE TABLE $global_widgets (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL DEFAULT '',
            tile_data longtext NOT NULL,
            created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            PRIMARY KEY (id),
            KEY idx_name (name)
        ) $charset_collate;";

        // A/B tests table
        $ab_tests = $wpdb->prefix . 'olo_ab_tests';
        $sql_ab_tests = "CREATE TABLE $ab_tests (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            tile_id varchar(100) NOT NULL,
            template_id bigint(20) unsigned NOT NULL,
            variants longtext NOT NULL,
            goal_type varchar(50) NOT NULL DEFAULT 'click',
            goal_selector varchar(255) DEFAULT '',
            status varchar(20) NOT NULL DEFAULT 'draft',
            views_a int(11) DEFAULT 0,
            views_b int(11) DEFAULT 0,
            conversions_a int(11) DEFAULT 0,
            conversions_b int(11) DEFAULT 0,
            started_at datetime DEFAULT NULL,
            ended_at datetime DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY template_id (template_id),
            KEY status (status)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql_templates );
        dbDelta( $sql_revisions );
        dbDelta( $sql_submissions );
        dbDelta( $sql_global_widgets );
        dbDelta( $sql_ab_tests );
    }

    public function create_template( $data ) {
        global $wpdb;
        $wpdb->insert(
            $this->table_templates(),
            [
                'title'     => sanitize_text_field( $data['title'] ?? '' ),
                'type'      => sanitize_text_field( $data['type'] ?? 'page' ),
                'content'   => wp_json_encode( $data['content'] ?? [] ),
                'settings'  => wp_json_encode( ! empty( $data['settings'] ) ? $data['settings'] : new stdClass ),
                'thumbnail' => esc_url_raw( $data['thumbnail'] ?? '' ),
                'status'    => sanitize_text_field( $data['status'] ?? 'draft' ),
                'author_id' => get_current_user_id(),
            ],
            [ '%s', '%s', '%s', '%s', '%s', '%s', '%d' ]
        );

        $this->flush_list_cache();

        return $wpdb->insert_id;
    }

    public function get_template( $id ) {
        $cache_key = 'olo_template_' . (int) $id;
        $cached    = wp_cache_get( $cache_key, $this->cache_group );
        if ( false !== $cached ) {
            return $cached;
        }

        global $wpdb;
        $row = $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM {$this->table_templates()} WHERE id = %d", $id ),
            ARRAY_A
        );
        if ( $row ) {
            $row['content']  = json_decode( $row['content'], true );
            if ( ! is_array( $row['content'] ) ) {
                if ( json_last_error() !== JSON_ERROR_NONE ) {
                    error_log( '[Olobuild] JSON decode error for template ' . $id . ' content: ' . json_last_error_msg() );
                }
                $row['content'] = [];
            }
            $row['settings'] = json_decode( $row['settings'], true );
            if ( ! is_array( $row['settings'] ) ) {
                $row['settings'] = [];
            }
        }

        wp_cache_set( $cache_key, $row, $this->cache_group, $this->cache_ttl );
        return $row;
    }

    public function update_template( $id, $data ) {
        global $wpdb;
        $update = [];
        $format = [];

        if ( isset( $data['title'] ) ) {
            $update['title'] = sanitize_text_field( $data['title'] );
            $format[] = '%s';
        }
        if ( isset( $data['type'] ) ) {
            $update['type'] = sanitize_text_field( $data['type'] );
            $format[] = '%s';
        }
        if ( isset( $data['content'] ) ) {
            $update['content'] = wp_json_encode( $data['content'] );
            $format[] = '%s';
        }
        if ( isset( $data['settings'] ) ) {
            $update['settings'] = wp_json_encode( ! empty( $data['settings'] ) ? $data['settings'] : new stdClass );
            $format[] = '%s';
        }
        if ( isset( $data['thumbnail'] ) ) {
            $update['thumbnail'] = esc_url_raw( $data['thumbnail'] );
            $format[] = '%s';
        }
        if ( isset( $data['status'] ) ) {
            $update['status'] = sanitize_text_field( $data['status'] );
            $format[] = '%s';
        }

        if ( empty( $update ) ) {
            return false;
        }

        $result = $wpdb->update(
            $this->table_templates(),
            $update,
            [ 'id' => $id ],
            $format,
            [ '%d' ]
        );

        if ( false !== $result ) {
            wp_cache_delete( 'olo_template_' . (int) $id, $this->cache_group );
            $this->flush_list_cache();
        }

        return $result;
    }

    public function delete_template( $id ) {
        global $wpdb;
        // Use transaction to ensure atomicity (revisions + template deleted together)
        $wpdb->query( 'START TRANSACTION' );
        $wpdb->delete( $this->table_revisions(), [ 'template_id' => $id ], [ '%d' ] );
        $result = $wpdb->delete( $this->table_templates(), [ 'id' => $id ], [ '%d' ] );

        if ( false !== $result ) {
            $wpdb->query( 'COMMIT' );
            wp_cache_delete( 'olo_template_' . (int) $id, $this->cache_group );
            $this->flush_list_cache();
        } else {
            $wpdb->query( 'ROLLBACK' );
        }

        return $result;
    }

    public function list_templates( $args = [] ) {
        $defaults = [
            'status'   => '',
            'type'     => '',
            'per_page' => 20,
            'page'     => 1,
            'orderby'  => 'updated_at',
            'order'    => 'DESC',
        ];
        $args = wp_parse_args( $args, $defaults );

        $cache_key = 'olo_list_' . md5( serialize( $args ) );
        $cached    = wp_cache_get( $cache_key, $this->cache_group );
        if ( false !== $cached ) {
            return $cached;
        }

        global $wpdb;

        $where = '1=1';
        $params = [];

        if ( ! empty( $args['status'] ) ) {
            $where .= ' AND status = %s';
            $params[] = $args['status'];
        }
        if ( ! empty( $args['type'] ) ) {
            $where .= ' AND type = %s';
            $params[] = $args['type'];
        }

        $allowed_orderby = [ 'id', 'title', 'created_at', 'updated_at' ];
        $orderby = in_array( $args['orderby'], $allowed_orderby, true ) ? $args['orderby'] : 'updated_at';
        $order = strtoupper( $args['order'] ) === 'ASC' ? 'ASC' : 'DESC';

        $offset = max( 0, ( (int) $args['page'] - 1 ) * (int) $args['per_page'] );
        $limit  = (int) $args['per_page'];

        $sql = "SELECT * FROM {$this->table_templates()} WHERE $where ORDER BY $orderby $order LIMIT %d OFFSET %d";
        $params[] = $limit;
        $params[] = $offset;

        $rows = $wpdb->get_results(
            $wpdb->prepare( $sql, ...$params ),
            ARRAY_A
        );

        foreach ( $rows as &$row ) {
            $row['content']  = json_decode( $row['content'], true ) ?? [];
            $row['settings'] = json_decode( $row['settings'], true ) ?? [];
        }

        // Get total count
        $count_sql = "SELECT COUNT(*) FROM {$this->table_templates()} WHERE $where";
        if ( ! empty( array_slice( $params, 0, -2 ) ) ) {
            $total = (int) $wpdb->get_var( $wpdb->prepare( $count_sql, ...array_slice( $params, 0, -2 ) ) );
        } else {
            $total = (int) $wpdb->get_var( $count_sql );
        }

        $result = [
            'items' => $rows,
            'total' => $total,
            'pages' => ceil( $total / $limit ),
        ];

        wp_cache_set( $cache_key, $result, $this->cache_group, $this->cache_ttl );

        // Track this cache key so we can flush all list caches on write operations.
        $this->track_list_cache_key( $cache_key );

        return $result;
    }

    /**
     * Track a list cache key so it can be flushed on write operations.
     */
    private function track_list_cache_key( $cache_key ) {
        $keys = wp_cache_get( 'olo_list_keys', $this->cache_group );
        if ( ! is_array( $keys ) ) {
            $keys = [];
        }
        if ( ! in_array( $cache_key, $keys, true ) ) {
            $keys[] = $cache_key;
            wp_cache_set( 'olo_list_keys', $keys, $this->cache_group, $this->cache_ttl );
        }
    }

    /**
     * Flush all tracked list caches (called on create/update/delete).
     */
    private function flush_list_cache() {
        $keys = wp_cache_get( 'olo_list_keys', $this->cache_group );
        if ( is_array( $keys ) ) {
            foreach ( $keys as $key ) {
                wp_cache_delete( $key, $this->cache_group );
            }
        }
        wp_cache_delete( 'olo_list_keys', $this->cache_group );
    }

    public function create_revision( $template_id, $content ) {
        global $wpdb;
        $wpdb->insert(
            $this->table_revisions(),
            [
                'template_id' => $template_id,
                'content'     => wp_json_encode( $content ),
            ],
            [ '%d', '%s' ]
        );
        $insert_id = $wpdb->insert_id;

        // Prune old revisions — keep max 50 per template
        $max_revisions = apply_filters( 'olo_max_revisions', 50 );
        $table = $this->table_revisions();
        $wpdb->query( $wpdb->prepare(
            "DELETE FROM $table WHERE template_id = %d AND id NOT IN (
                SELECT id FROM (SELECT id FROM $table WHERE template_id = %d ORDER BY created_at DESC LIMIT %d) AS keep_rows
            )",
            $template_id,
            $template_id,
            $max_revisions
        ) );

        return $insert_id;
    }

    /**
     * Get all revisions for a template, ordered by newest first.
     */
    public function get_revisions( $template_id, $limit = 50 ) {
        global $wpdb;
        $table = $this->table_revisions();
        $rows  = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, template_id, LENGTH(content) AS content_size, created_at FROM $table WHERE template_id = %d ORDER BY created_at DESC LIMIT %d",
                $template_id,
                $limit
            ),
            ARRAY_A
        );
        return $rows ?: [];
    }

    /**
     * Get a single revision by ID.
     */
    public function get_revision( $revision_id ) {
        global $wpdb;
        $table = $this->table_revisions();
        $row   = $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $revision_id ),
            ARRAY_A
        );
        if ( $row && isset( $row['content'] ) ) {
            $row['content'] = json_decode( $row['content'], true );
        }
        return $row;
    }

    /**
     * Clean up orphaned revisions (revisions whose template no longer exists).
     * Called by WP cron weekly.
     */
    public function cleanup_orphaned_revisions() {
        global $wpdb;
        $revisions_table = $this->table_revisions();
        $templates_table = $this->table_templates();
        $deleted = $wpdb->query(
            "DELETE r FROM $revisions_table r LEFT JOIN $templates_table t ON r.template_id = t.id WHERE t.id IS NULL"
        );
        if ( $deleted > 0 ) {
            error_log( '[Olobuild] Cleaned up ' . $deleted . ' orphaned revisions.' );
        }
        return $deleted;
    }
}
