<?php
/**
 * Divi → OloBuild converter (stub for Phase 3).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Divi_Converter extends Olo_Converter_Interface {

    public function get_source_name() {
        return 'Divi';
    }

    public function get_source_slug() {
        return 'divi';
    }

    public function is_source_installed() {
        return class_exists( 'ET_Builder_Module' )
            || defined( 'ET_BUILDER_VERSION' );
    }

    public function get_available_pages() {
        if ( ! $this->is_source_installed() ) {
            return [];
        }

        global $wpdb;

        $results = $wpdb->get_results(
            "SELECT ID, post_title, post_type, post_status
             FROM {$wpdb->posts}
             WHERE post_content LIKE '%[et_pb_section%'
               AND post_status IN ('publish', 'draft', 'private')
               AND post_type IN ('page', 'post', 'et_pb_layout')
             ORDER BY post_title ASC",
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
        return get_post_field( 'post_content', $post_id );
    }

    protected function parse_file( $file_data ) {
        // Divi export: { "shortcode": "..." } or { "layouts": [...] }
        if ( isset( $file_data['shortcode'] ) ) {
            return $file_data['shortcode'];
        }
        if ( isset( $file_data['layouts'][0]['post_content'] ) ) {
            return $file_data['layouts'][0]['post_content'];
        }
        return '';
    }

    protected function parse_source_data( $raw ) {
        // TODO: Phase 3 — parse Divi shortcodes into tree.
        // For now, return empty.
        return [];
    }

    protected function convert_node( $node, Olo_Conversion_Report $report ) {
        // TODO: Phase 3 — implement Divi module conversion.
        $type = $node['type'] ?? 'unknown';
        $report->add_skipped( "divi:{$type}", 'Converter Divi non ancora implementato (Fase 3)' );
        return null;
    }
}
