<?php
/**
 * Olobuild Template Library
 *
 * Provides pre-built section templates that users can insert into their pages.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Olo_Template_Library {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get all available templates grouped by category.
     */
    public function get_templates() {
        $file = OLO_PATH . 'assets/data/template-library.json';
        if ( ! file_exists( $file ) ) {
            return [];
        }
        $json = file_get_contents( $file );
        $data = json_decode( $json, true );
        if ( ! is_array( $data ) ) {
            return [];
        }
        // Support both flat array and {version, templates} wrapper
        if ( isset( $data['templates'] ) && is_array( $data['templates'] ) ) {
            return $data['templates'];
        }
        return $data;
    }

    /**
     * Get templates filtered by category.
     */
    public function get_by_category( $category ) {
        $all = $this->get_templates();
        if ( empty( $category ) ) return $all;
        return array_values( array_filter( $all, function( $tpl ) use ( $category ) {
            return ( $tpl['category'] ?? '' ) === $category;
        } ) );
    }

    /**
     * Get a single template by ID.
     */
    public function get_template( $id ) {
        $all = $this->get_templates();
        foreach ( $all as $tpl ) {
            if ( ( $tpl['id'] ?? '' ) === $id ) {
                return $tpl;
            }
        }
        return null;
    }

    /**
     * Get list of all categories with counts.
     */
    public function get_categories() {
        $all = $this->get_templates();
        $cats = [];
        foreach ( $all as $tpl ) {
            $cat = $tpl['category'] ?? 'other';
            if ( ! isset( $cats[ $cat ] ) ) {
                $cats[ $cat ] = 0;
            }
            $cats[ $cat ]++;
        }
        return $cats;
    }

    /**
     * Save a section as a user template.
     */
    public function save_user_template( $name, $category, $content ) {
        $templates = get_option( 'olo_user_templates', [] );
        if ( ! is_array( $templates ) ) $templates = [];

        $id = 'user-' . wp_generate_password( 8, false );
        $templates[] = [
            'id'         => $id,
            'name'       => sanitize_text_field( $name ),
            'category'   => sanitize_text_field( $category ),
            'content'    => $content,
            'created_at' => current_time( 'mysql' ),
            'is_user'    => true,
        ];

        update_option( 'olo_user_templates', $templates, false );
        return $id;
    }

    /**
     * Delete a user template.
     */
    public function delete_user_template( $id ) {
        $templates = get_option( 'olo_user_templates', [] );
        if ( ! is_array( $templates ) ) return false;

        $found = false;
        $templates = array_values( array_filter( $templates, function( $tpl ) use ( $id, &$found ) {
            if ( ( $tpl['id'] ?? '' ) === $id ) {
                $found = true;
                return false;
            }
            return true;
        } ) );

        if ( $found ) {
            update_option( 'olo_user_templates', $templates, false );
        }
        return $found;
    }

    /**
     * Get all templates (built-in + user) merged.
     */
    public function get_all_templates() {
        $builtin = $this->get_templates();
        $user    = get_option( 'olo_user_templates', [] );
        if ( ! is_array( $user ) ) $user = [];
        return array_merge( $builtin, $user );
    }
}
