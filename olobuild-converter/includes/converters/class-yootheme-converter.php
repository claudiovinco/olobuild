<?php
/**
 * YooTheme Pro → OloBuild converter (stub for Phase 2).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Yootheme_Converter extends Olo_Converter_Interface {

    public function get_source_name() {
        return 'YooTheme Pro';
    }

    public function get_source_slug() {
        return 'yootheme';
    }

    public function is_source_installed() {
        return class_exists( 'YOOtheme\\Application' )
            || ( function_exists( 'wp_get_theme' ) && wp_get_theme()->get( 'Name' ) === 'YOOtheme' );
    }

    public function get_available_pages() {
        if ( ! $this->is_source_installed() ) {
            return [];
        }

        global $wpdb;

        $results = $wpdb->get_results(
            "SELECT p.ID, p.post_title, p.post_type, p.post_status
             FROM {$wpdb->postmeta} pm
             JOIN {$wpdb->posts} p ON p.ID = pm.post_id
             WHERE pm.meta_key = '_yootheme_pagebuilder'
               AND pm.meta_value != ''
               AND p.post_status IN ('publish', 'draft', 'private')
             ORDER BY p.post_title ASC",
            ARRAY_A
        );

        $pages = [];
        foreach ( $results as $row ) {
            $pages[ (int) $row['ID'] ] = sprintf(
                '%s [%s - %s]',
                $row['post_title'] ?: '(senza titolo)',
                $row['post_type'],
                $row['post_status']
            );
        }
        return $pages;
    }

    protected function read_from_db( $post_id ) {
        $data = get_post_meta( $post_id, '_yootheme_pagebuilder', true );
        if ( is_string( $data ) ) {
            $data = json_decode( $data, true );
        }
        return is_array( $data ) ? $data : [];
    }

    protected function parse_file( $file_data ) {
        // YooTheme export has a "children" key at root.
        if ( isset( $file_data['children'] ) ) {
            return $file_data['children'];
        }
        return is_array( $file_data ) ? $file_data : [];
    }

    protected function parse_source_data( $raw ) {
        if ( isset( $raw['children'] ) ) {
            return $raw['children'];
        }
        return is_array( $raw ) ? $raw : [];
    }

    protected function convert_node( $node, Olo_Conversion_Report $report ) {
        // TODO: Phase 2 — implement YooTheme element conversion.
        $type = $node['type'] ?? 'unknown';
        $report->add_skipped( "yootheme:{$type}", 'Converter YooTheme non ancora implementato (Fase 2)' );
        return null;
    }
}
