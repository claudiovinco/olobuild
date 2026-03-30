<?php
/**
 * Olo_Template_Conditions — Advanced display conditions for templates.
 *
 * Allows multiple conditions with AND/OR logic per template assignment.
 * Extends the simple "one template per post type" system.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Template_Conditions {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init() {
        // REST API for managing template conditions
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );

        // Override single template selection with conditions
        add_filter( 'olo_resolve_template_id', [ $this, 'resolve_by_conditions' ], 10, 2 );
    }

    /* ─────────────────────────────────────────────
     * Template Resolution with Conditions
     * ───────────────────────────────────────────── */

    /**
     * Resolve template ID based on advanced conditions.
     * Falls back to simple post_type option if no conditions match.
     *
     * @param int    $template_id Current template ID (from simple system)
     * @param string $context     'single', 'archive', 'header', 'footer'
     * @return int Template ID
     */
    public function resolve_by_conditions( $template_id, $context = 'single' ) {
        $assignments = get_option( 'olo_template_conditions', [] );
        if ( empty( $assignments ) || ! is_array( $assignments ) ) {
            return $template_id;
        }

        // Filter assignments by context type
        $relevant = array_filter( $assignments, function( $a ) use ( $context ) {
            return ( $a['context'] ?? '' ) === $context && ! empty( $a['enabled'] );
        } );

        // Sort by priority (lower = higher priority)
        usort( $relevant, function( $a, $b ) {
            return ( $a['priority'] ?? 100 ) - ( $b['priority'] ?? 100 );
        } );

        foreach ( $relevant as $assignment ) {
            $tpl_id = intval( $assignment['template_id'] ?? 0 );
            if ( ! $tpl_id ) {
                continue;
            }

            $conditions = $assignment['conditions'] ?? [];
            $logic      = $assignment['conditions_logic'] ?? 'AND';

            if ( $this->evaluate_conditions( $conditions, $logic ) ) {
                return $tpl_id;
            }
        }

        return $template_id;
    }

    /**
     * Evaluate a set of conditions with AND/OR logic.
     */
    private function evaluate_conditions( $conditions, $logic = 'AND' ) {
        if ( empty( $conditions ) ) {
            return true;
        }

        $results = [];
        foreach ( $conditions as $cond ) {
            $results[] = $this->evaluate_single( $cond );
        }

        if ( $logic === 'OR' ) {
            return in_array( true, $results, true );
        }

        // AND: all must be true
        return ! in_array( false, $results, true );
    }

    private function evaluate_single( $cond ) {
        $type   = $cond['type'] ?? '';
        $value  = $cond['value'] ?? '';
        $negate = ! empty( $cond['negate'] );

        $result = false;

        switch ( $type ) {
            case 'entire_site':
                $result = true;
                break;

            case 'front_page':
                $result = is_front_page();
                break;

            case 'singular':
                $result = is_singular();
                break;

            case 'page':
                $result = empty( $value ) || $value === 'all' ? is_page() : is_page( intval( $value ) );
                break;

            case 'post':
                $result = empty( $value ) || $value === 'all'
                    ? is_singular( 'post' )
                    : ( is_singular( 'post' ) && get_the_ID() === intval( $value ) );
                break;

            case 'post_type':
                $result = is_singular( sanitize_text_field( $value ) );
                break;

            case 'archive':
                $result = empty( $value ) || $value === 'all'
                    ? is_archive()
                    : is_post_type_archive( sanitize_text_field( $value ) );
                break;

            case 'category':
                if ( is_singular() ) {
                    $result = has_category( $value ? intval( $value ) : null );
                } else {
                    $result = is_category( $value ? intval( $value ) : null );
                }
                break;

            case 'tag':
                if ( is_singular() ) {
                    $result = has_tag( sanitize_text_field( $value ) );
                } else {
                    $result = is_tag( sanitize_text_field( $value ) );
                }
                break;

            case 'taxonomy':
                $parts = explode( ':', $value );
                if ( count( $parts ) >= 2 ) {
                    $result = has_term( $parts[1], $parts[0] );
                } elseif ( count( $parts ) === 1 ) {
                    $result = is_tax( $parts[0] );
                }
                break;

            case 'author':
                $result = is_singular() && (int) get_the_author_meta( 'ID' ) === (int) $value;
                break;

            case 'user_logged_in':
                $result = is_user_logged_in();
                break;

            case 'user_logged_out':
                $result = ! is_user_logged_in();
                break;

            case 'user_role':
                if ( is_user_logged_in() ) {
                    $user   = wp_get_current_user();
                    $result = in_array( sanitize_text_field( $value ), $user->roles, true );
                }
                break;

            case 'has_template':
                // Post has a specific Olobuild template assigned
                $result = is_singular() && get_post_meta( get_the_ID(), '_olo_template_id', true ) == intval( $value );
                break;

            case 'post_format':
                $result = is_singular() && has_post_format( sanitize_text_field( $value ) );
                break;

            case '404':
                $result = is_404();
                break;

            case 'search':
                $result = is_search();
                break;

            case 'date_before':
                $result = time() < strtotime( $value );
                break;

            case 'date_after':
                $result = time() > strtotime( $value );
                break;

            // WooCommerce
            case 'woo_shop':
                $result = function_exists( 'is_shop' ) && is_shop();
                break;
            case 'woo_product':
                $result = function_exists( 'is_product' ) && is_product();
                break;
            case 'woo_product_cat':
                if ( function_exists( 'is_product' ) && is_product() ) {
                    $result = has_term( sanitize_text_field( $value ), 'product_cat' );
                }
                break;
            case 'woo_cart':
                $result = function_exists( 'is_cart' ) && is_cart();
                break;
            case 'woo_checkout':
                $result = function_exists( 'is_checkout' ) && is_checkout();
                break;
            case 'woo_account':
                $result = function_exists( 'is_account_page' ) && is_account_page();
                break;
        }

        return $negate ? ! $result : $result;
    }

    /* ─────────────────────────────────────────────
     * REST API
     * ───────────────────────────────────────────── */

    public function register_routes() {
        register_rest_route( 'olo/v1', '/template-conditions', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_conditions' ],
                'permission_callback' => function () {
                    return current_user_can( 'edit_posts' );
                },
            ],
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'save_conditions' ],
                'permission_callback' => function () {
                    return current_user_can( 'edit_posts' );
                },
            ],
        ] );
    }

    public function get_conditions( $request ) {
        return rest_ensure_response( get_option( 'olo_template_conditions', [] ) );
    }

    public function save_conditions( $request ) {
        $data = $request->get_json_params();
        if ( ! is_array( $data ) ) {
            return new WP_Error( 'invalid', 'Dati non validi', [ 'status' => 400 ] );
        }

        $clean = [];
        foreach ( $data as $item ) {
            $clean[] = [
                'enabled'          => ! empty( $item['enabled'] ),
                'name'             => sanitize_text_field( $item['name'] ?? '' ),
                'template_id'      => intval( $item['template_id'] ?? 0 ),
                'context'          => sanitize_text_field( $item['context'] ?? 'single' ),
                'priority'         => max( 1, intval( $item['priority'] ?? 10 ) ),
                'conditions'       => $this->sanitize_conditions_arr( $item['conditions'] ?? [] ),
                'conditions_logic' => in_array( $item['conditions_logic'] ?? 'AND', [ 'AND', 'OR' ], true ) ? $item['conditions_logic'] : 'AND',
            ];
        }

        update_option( 'olo_template_conditions', $clean, false );
        return rest_ensure_response( [ 'success' => true ] );
    }

    private function sanitize_conditions_arr( $conditions ) {
        if ( ! is_array( $conditions ) ) {
            return [];
        }
        $clean = [];
        foreach ( $conditions as $c ) {
            $clean[] = [
                'type'   => sanitize_text_field( $c['type'] ?? '' ),
                'value'  => sanitize_text_field( $c['value'] ?? '' ),
                'negate' => ! empty( $c['negate'] ),
            ];
        }
        return $clean;
    }
}
