<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Database {

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
            KEY idx_template_id (template_id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql_templates );
        dbDelta( $sql_revisions );
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
        return $wpdb->insert_id;
    }

    public function get_template( $id ) {
        global $wpdb;
        $row = $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM {$this->table_templates()} WHERE id = %d", $id ),
            ARRAY_A
        );
        if ( $row ) {
            $row['content']  = json_decode( $row['content'], true );
            $row['settings'] = json_decode( $row['settings'], true ) ?: [];
        }
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

        return $wpdb->update(
            $this->table_templates(),
            $update,
            [ 'id' => $id ],
            $format,
            [ '%d' ]
        );
    }

    public function delete_template( $id ) {
        global $wpdb;
        // Delete revisions first
        $wpdb->delete( $this->table_revisions(), [ 'template_id' => $id ], [ '%d' ] );
        return $wpdb->delete( $this->table_templates(), [ 'id' => $id ], [ '%d' ] );
    }

    public function list_templates( $args = [] ) {
        global $wpdb;

        $defaults = [
            'status'   => '',
            'type'     => '',
            'per_page' => 20,
            'page'     => 1,
            'orderby'  => 'updated_at',
            'order'    => 'DESC',
        ];
        $args = wp_parse_args( $args, $defaults );

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
            $row['content']  = json_decode( $row['content'], true );
            $row['settings'] = json_decode( $row['settings'], true ) ?: [];
        }

        // Get total count
        $count_sql = "SELECT COUNT(*) FROM {$this->table_templates()} WHERE $where";
        if ( ! empty( array_slice( $params, 0, -2 ) ) ) {
            $total = (int) $wpdb->get_var( $wpdb->prepare( $count_sql, ...array_slice( $params, 0, -2 ) ) );
        } else {
            $total = (int) $wpdb->get_var( $count_sql );
        }

        return [
            'items' => $rows,
            'total' => $total,
            'pages' => ceil( $total / $limit ),
        ];
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
        return $wpdb->insert_id;
    }
}
