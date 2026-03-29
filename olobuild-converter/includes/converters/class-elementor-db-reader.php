<?php
/**
 * Reads Elementor data from WordPress database.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Elementor_Db_Reader {

    /**
     * Check if Elementor is installed and active.
     */
    public static function is_installed() {
        return class_exists( '\Elementor\Plugin' )
            || defined( 'ELEMENTOR_VERSION' )
            || is_plugin_active( 'elementor/elementor.php' );
    }

    /**
     * Get list of posts/pages that have Elementor content.
     * @return array [ post_id => title, ... ]
     */
    public static function get_available_pages() {
        global $wpdb;

        $results = $wpdb->get_results(
            "SELECT p.ID, p.post_title, p.post_type, p.post_status
             FROM {$wpdb->postmeta} pm
             JOIN {$wpdb->posts} p ON p.ID = pm.post_id
             WHERE pm.meta_key = '_elementor_data'
               AND pm.meta_value != '[]'
               AND pm.meta_value != ''
               AND p.post_status IN ('publish', 'draft', 'private')
               AND p.post_type IN ('page', 'post', 'elementor_library')
             ORDER BY p.post_title ASC",
            ARRAY_A
        );

        $pages = [];
        foreach ( $results as $row ) {
            $label = sprintf(
                '%s [%s - %s]',
                $row['post_title'] ?: '(senza titolo)',
                $row['post_type'],
                $row['post_status']
            );
            $pages[ (int) $row['ID'] ] = $label;
        }

        return $pages;
    }

    /**
     * Read Elementor content data for a specific post.
     * @param  int    $post_id
     * @return array  Decoded Elementor content tree.
     */
    public static function read( $post_id ) {
        $data = get_post_meta( $post_id, '_elementor_data', true );

        if ( empty( $data ) ) {
            return [];
        }

        if ( is_string( $data ) ) {
            $data = json_decode( $data, true );
        }

        return is_array( $data ) ? $data : [];
    }

    /**
     * Read Elementor page-level settings.
     * @param  int   $post_id
     * @return array
     */
    public static function read_page_settings( $post_id ) {
        $settings = get_post_meta( $post_id, '_elementor_page_settings', true );
        return is_array( $settings ) ? $settings : [];
    }
}
