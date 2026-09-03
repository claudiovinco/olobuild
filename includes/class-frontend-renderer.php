<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
require_once __DIR__ . '/traits/trait-olobuild-renderer-css.php';
require_once __DIR__ . '/traits/trait-olobuild-renderer-structure.php';
require_once __DIR__ . '/traits/trait-olobuild-renderer-page.php';

class Olobuild_Frontend_Renderer {
    use Olobuild_Renderer_Css_Trait;
    use Olobuild_Renderer_Structure_Trait;
    use Olobuild_Renderer_Page_Trait;

    private $fraction_map = [
        '1-1' => 100, '1-2' => 50, '1-3' => 33.33, '2-3' => 66.66,
        '1-4' => 25, '3-4' => 75, '1-5' => 20, '2-5' => 40,
        '3-5' => 60, '4-5' => 80, '1-6' => 16.66, '5-6' => 83.33,
    ];

    // shadow_map and drop_shadow_map moved to Olobuild_CSS_Builder

    private $align_map = [
        'stretch' => 'stretch',
        'start'   => 'flex-start',
        'center'  => 'center',
        'end'     => 'flex-end',
    ];

    /**
     * Builder mode: adds data-olo-tile-id attributes for iframe live preview.
     */
    public $builder_mode = false;

    /** @var Olobuild_CSS_Builder */
    private $css;

    /** @var Olobuild_Animation_Builder */
    private $anim;

    /** @var array Breakpoint definitions (popolato da render_for_builder). */
    public $breakpoints = [];

    /** @var array Responsive CSS rules collected during render. */
    public $responsive_css_rules = [];

    /**
     * Whitelist of allowed CSS border-style values.
     */
    private static $allowed_border_styles = [ 'none', 'solid', 'dashed', 'dotted', 'double', 'groove', 'ridge', 'inset', 'outset' ];

    public function __construct() {
        $this->css  = new Olobuild_CSS_Builder();
        $this->anim = new Olobuild_Animation_Builder();
        if ( ! has_action( 'wp_footer', [ __CLASS__, 'print_sticky_offset_script' ] ) ) {
            add_action( 'wp_footer', [ __CLASS__, 'print_sticky_offset_script' ], 99 );
        }
    }


    /**
     * Index all tile nodes from a content tree into the static registry.
     * Also collects referenced tile IDs (search_tile_id from navmenu/megamenu).
     */
    private static function index_tiles( $nodes ) {
        if ( ! is_array( $nodes ) ) return;
        foreach ( $nodes as $node ) {
            if ( ! empty( $node['id'] ) && ! empty( $node['type'] ) ) {
                self::$tile_registry[ $node['id'] ] = [
                    'type'     => $node['type'],
                    'settings' => $node['settings'] ?? [],
                ];
                // Collect referenced tile IDs
                $ref_id = $node['settings']['search_tile_id'] ?? '';
                if ( $ref_id !== '' ) {
                    self::$referenced_tile_ids[ $ref_id ] = true;
                }
                // Langswitcher referenziato dal megamenu (lang_tile_id): stessa
                // soppressione in-loco della search tile.
                $lang_ref = $node['settings']['lang_tile_id'] ?? '';
                if ( $lang_ref !== '' ) {
                    self::$referenced_tile_ids[ $lang_ref ] = true;
                }
            }
            if ( ! empty( $node['children'] ) && is_array( $node['children'] ) ) {
                self::index_tiles( $node['children'] );
            }
        }
    }

    /**
     * Find a tile's data by ID from the registry.
     *
     * @param string $tile_id Tile UUID (e.g., "tile-abc123").
     * @return array|null Array with 'type' and 'settings', or null.
     */
    public static function find_tile( $tile_id ) {
        return self::$tile_registry[ $tile_id ] ?? null;
    }

    public function init() {
        add_shortcode( 'olobuild_template', [ $this, 'render_shortcode' ] );
        add_shortcode( 'olo_template', [ $this, 'render_shortcode' ] );       // backward compat (contenuti esistenti)
        add_shortcode( 'mosaic_template', [ $this, 'render_shortcode' ] );    // backward compat (storico)
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_styles' ] );
        add_action( 'template_redirect', [ $this, 'add_security_headers' ] );
    }


    // =========================================================================
    // Migration: legacy flat format → tree (Section > Row > Column > Element)
    // =========================================================================

    /**
     * Check if content is in legacy flat format.
     */
    private function is_legacy_format( $content ) {
        if ( ! is_array( $content ) || empty( $content ) ) return false;
        foreach ( $content as $node ) {
            if ( ( $node['type'] ?? '' ) !== 'section' ) return true;
        }
        return false;
    }

    /**
     * Migrate legacy content to tree format.
     */
    private function maybe_migrate_content( $content ) {
        if ( ! $this->is_legacy_format( $content ) ) {
            return $content;
        }

        $sections = [];
        $layout_widths = [
            '100'         => ['1-1'],
            '50-50'       => ['1-2', '1-2'],
            '33-33-33'    => ['1-3', '1-3', '1-3'],
            '25-50-25'    => ['1-4', '1-2', '1-4'],
            '25-25-25-25' => ['1-4', '1-4', '1-4', '1-4'],
            '66-33'       => ['2-3', '1-3'],
            '33-66'       => ['1-3', '2-3'],
        ];

        foreach ( $content as $tile ) {
            $type = $tile['type'] ?? '';

            if ( $type === 'section' ) {
                $sections[] = $tile;
                continue;
            }

            if ( $type === 'row' ) {
                $settings     = $tile['settings'] ?? [];
                $columns_data = $settings['columns_data'] ?? [];
                $layout       = $settings['layout'] ?? '50-50';
                $widths       = $layout_widths[ $layout ] ?? ['1-2', '1-2'];

                unset( $settings['columns_data'] );

                $columns = [];
                foreach ( $widths as $i => $w ) {
                    $col_data  = $columns_data[ $i ] ?? [ 'tiles' => [] ];
                    $children  = is_array( $col_data['tiles'] ?? null ) ? $col_data['tiles'] : [];
                    $columns[] = [
                        'id'       => $this->css->generate_id(),
                        'type'     => 'column',
                        'settings' => [ 'width_default' => '', 'width_small' => '', 'width_medium' => $w, 'width_large' => '' ],
                        'style'    => [],
                        'advanced' => [],
                        'children' => $children,
                    ];
                }

                $row = [
                    'id'       => $tile['id'] ?? $this->css->generate_id(),
                    'type'     => 'row',
                    'settings' => $settings,
                    'style'    => $tile['style'] ?? [],
                    'advanced' => $tile['advanced'] ?? [],
                    'children' => $columns,
                ];

                $sections[] = [
                    'id'       => $this->css->generate_id(),
                    'type'     => 'section',
                    'settings' => [ 'style' => 'default', 'width' => 'default', 'padding' => 'default' ],
                    'style'    => [],
                    'advanced' => [],
                    'children' => [ $row ],
                ];
            } else {
                // Wrap element in Section > Row > Column(1/1)
                $column = [
                    'id'       => $this->css->generate_id(),
                    'type'     => 'column',
                    'settings' => [ 'width_default' => '', 'width_small' => '', 'width_medium' => '1-1', 'width_large' => '' ],
                    'style'    => [],
                    'advanced' => [],
                    'children' => [ $tile ],
                ];
                $row = [
                    'id'       => $this->css->generate_id(),
                    'type'     => 'row',
                    'settings' => [ 'layout' => '100', 'gap' => '16', 'column_gap' => 'default', 'vertical_align' => 'stretch', 'stack_mobile' => true ],
                    'style'    => [],
                    'advanced' => [],
                    'children' => [ $column ],
                ];
                $sections[] = [
                    'id'       => $this->css->generate_id(),
                    'type'     => 'section',
                    'settings' => [ 'style' => 'default', 'width' => 'default', 'padding' => 'default' ],
                    'style'    => [],
                    'advanced' => [],
                    'children' => [ $row ],
                ];
            }
        }

        return $sections;
    }

    // =========================================================================
    // Recursive rendering
    // =========================================================================

    /**
     * Check conditional visibility rules. Returns false if node should be hidden.
     */
    /**
     * Check element-level conditional visibility (settings-based).
     * Returns true if the element should be rendered.
     */
    private function check_conditions( $settings ) {
        $result = $this->check_conditions_result( $settings );
        // cond_negate: inverte l'esito della condizione (es. "mostra ovunque
        // TRANNE che sulla front page").
        if ( ! empty( $settings['cond_negate'] ) && ( $settings['cond_type'] ?? '' ) !== '' ) {
            return ! $result;
        }
        return $result;
    }

    private function check_conditions_result( $settings ) {
        $cond_type = $settings['cond_type'] ?? '';
        if ( $cond_type === '' ) return true;

        // OR logic: if cond_logic is 'or', check first condition OR second condition
        $cond_logic = $settings['cond_logic'] ?? 'and';
        if ( $cond_logic === 'or' ) {
            $cond_2_type = $settings['cond_2_type'] ?? '';
            $first_result = $this->evaluate_single_condition( $cond_type, $settings );
            if ( $cond_2_type === '' ) {
                return $first_result;
            }
            $second_result = $this->evaluate_single_condition( $cond_2_type, $settings, '2' );
            return $first_result || $second_result;
        }

        return $this->evaluate_single_condition( $cond_type, $settings );
    }

    /**
     * Evaluate a single condition by type.
     * $prefix is '' for primary condition, '2' for secondary (OR logic).
     */
    private function evaluate_single_condition( $cond_type, $settings, $prefix = '' ) {
        // For secondary conditions, settings keys may be prefixed (cond_2_date, etc.)
        // But most share the same setting keys — caller passes the type separately.

        switch ( $cond_type ) {
            case 'logged_in':
                return is_user_logged_in();
            case 'logged_out':
                return ! is_user_logged_in();
            case 'role':
                $role = $settings['cond_role'] ?? '';
                if ( $role === '' ) return true;
                return current_user_can( $role );
            case 'mobile':
                return wp_is_mobile();
            case 'desktop':
                return ! wp_is_mobile();
            case 'date_after':
                $date = $settings['cond_date'] ?? '';
                if ( $date === '' ) return true;
                return current_time( 'Y-m-d' ) >= $date;
            case 'date_before':
                $date = $settings['cond_date'] ?? '';
                if ( $date === '' ) return true;
                return current_time( 'Y-m-d' ) <= $date;
            case 'has_featured_image':
                return has_post_thumbnail();
            case 'is_front_page':
                return is_front_page();
            case 'is_single':
                return is_single();
            case 'is_page':
                return is_page();
            case 'is_archive':
                return is_archive();
            case 'is_search':
                return is_search();
            case 'is_404':
                return is_404();
            case 'post_type':
                $pt = $settings['cond_post_type'] ?? '';
                if ( $pt === '' ) return true;
                return get_post_type() === $pt;
            case 'has_children':
                $children = get_pages( array( 'child_of' => get_the_ID() ) );
                return ( count( $children ) > 0 );
            case 'is_author':
                return is_author();
            case 'url_contains':
                $str = $settings['cond_url_contains'] ?? '';
                if ( $str === '' ) return true;
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per filtro di visibilità (display condition url_contains); nessuna modifica di stato; valore sanitizzato.
                $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
                return ( str_contains( $request_uri, $str ) );
            case 'day_of_week':
                $cond_day = strtolower( $settings['cond_day'] ?? '' );
                if ( $cond_day === '' ) return true;
                $today = strtolower( wp_date( 'l' ) );
                return $today === $cond_day;
            case 'time_range':
                $time_from = $settings['cond_time_from'] ?? '';
                $time_to   = $settings['cond_time_to'] ?? '';
                if ( $time_from === '' || $time_to === '' ) return true;
                $now = wp_date( 'H:i' );
                return ( $now >= $time_from ) && ( $now <= $time_to );
            case 'referrer_url':
                $cond_ref = $settings['cond_referrer'] ?? '';
                if ( $cond_ref === '' ) return true;
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per filtro di visibilità (display condition referrer_url); nessuna modifica di stato; valore sanitizzato.
                $referrer = isset( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
                return ( str_contains( $referrer, $cond_ref ) );
            case 'browser':
                $cond_browser = strtolower( $settings['cond_browser'] ?? '' );
                if ( $cond_browser === '' ) return true;
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per filtro di visibilità (display condition browser); nessuna modifica di stato; valore sanitizzato.
                $ua = strtolower( isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '' );
                return ( str_contains( $ua, $cond_browser ) );
            case 'woo_cart_empty':
                if ( ! function_exists( 'WC' ) ) return true;
                $wc = WC();
                if ( ! $wc || ! $wc->cart ) return true;
                return $wc->cart->get_cart_contents_count() === 0;
            case 'woo_cart_has_items':
                if ( ! function_exists( 'WC' ) ) return false;
                $wc = WC();
                if ( ! $wc || ! $wc->cart ) return false;
                return $wc->cart->get_cart_contents_count() > 0;
            case 'custom_field_equals':
                $cf_key   = $settings['cond_custom_field_key'] ?? '';
                $cf_value = $settings['cond_custom_field_value'] ?? '';
                if ( $cf_key === '' ) return true;
                global $post;
                if ( ! is_a( $post, 'WP_Post' ) ) return false;
                return get_post_meta( $post->ID, $cf_key, true ) === $cf_value;
            case 'acf_field_equals':
                $acf_key   = $settings['cond_acf_field'] ?? '';
                $acf_value = $settings['cond_acf_value'] ?? '';
                if ( $acf_key === '' ) return true;
                if ( ! function_exists( 'get_field' ) ) return true;
                return (string) get_field( $acf_key ) === $acf_value;
            case 'acf_field_not_empty':
                $acf_key = $settings['cond_acf_field'] ?? '';
                if ( $acf_key === '' ) return true;
                if ( ! function_exists( 'get_field' ) ) return true;
                $val = get_field( $acf_key );
                return ! empty( $val );
            case 'acf_field_empty':
                $acf_key = $settings['cond_acf_field'] ?? '';
                if ( $acf_key === '' ) return true;
                if ( ! function_exists( 'get_field' ) ) return true;
                $val = get_field( $acf_key );
                return empty( $val );
            case 'acf_field_contains':
                $acf_key   = $settings['cond_acf_field'] ?? '';
                $acf_value = $settings['cond_acf_value'] ?? '';
                if ( $acf_key === '' ) return true;
                if ( ! function_exists( 'get_field' ) ) return true;
                return ( str_contains( (string) get_field( $acf_key ), $acf_value ) );
            case 'acf_field_greater':
                $acf_key   = $settings['cond_acf_field'] ?? '';
                $acf_value = $settings['cond_acf_value'] ?? '';
                if ( $acf_key === '' ) return true;
                if ( ! function_exists( 'get_field' ) ) return true;
                return floatval( get_field( $acf_key ) ) > floatval( $acf_value );
            case 'acf_field_less':
                $acf_key   = $settings['cond_acf_field'] ?? '';
                $acf_value = $settings['cond_acf_value'] ?? '';
                if ( $acf_key === '' ) return true;
                if ( ! function_exists( 'get_field' ) ) return true;
                return floatval( get_field( $acf_key ) ) < floatval( $acf_value );

            // ── Advanced conditions ──────────────────────

            case 'taxonomy_has_term':
                $tax  = $settings['cond_taxonomy'] ?? '';
                $term = $settings['cond_term'] ?? '';
                if ( $tax === '' || $term === '' ) return true;
                global $post;
                if ( ! is_a( $post, 'WP_Post' ) ) return false;
                return has_term( $term, $tax, $post->ID );

            case 'taxonomy_not_has_term':
                $tax  = $settings['cond_taxonomy'] ?? '';
                $term = $settings['cond_term'] ?? '';
                if ( $tax === '' || $term === '' ) return true;
                global $post;
                if ( ! is_a( $post, 'WP_Post' ) ) return true;
                return ! has_term( $term, $tax, $post->ID );

            case 'is_child_of':
                $parent_id = absint( $settings['cond_parent_id'] ?? 0 );
                if ( $parent_id === 0 ) return true;
                global $post;
                if ( ! is_a( $post, 'WP_Post' ) ) return false;
                $ancestors = get_post_ancestors( $post->ID );
                return in_array( $parent_id, $ancestors, false );

            case 'page_template':
                $tpl = $settings['cond_page_template'] ?? '';
                if ( $tpl === '' ) return true;
                return get_page_template_slug() === $tpl;

            case 'is_taxonomy_archive':
                $tax = $settings['cond_taxonomy'] ?? '';
                if ( $tax === '' ) return is_tax() || is_category() || is_tag();
                return is_tax( $tax ) || ( $tax === 'category' ? is_category() : false ) || ( $tax === 'post_tag' ? is_tag() : false );

            case 'woo_product_category':
                if ( ! function_exists( 'is_product' ) ) return true;
                $cat = $settings['cond_woo_category'] ?? '';
                if ( $cat === '' ) return true;
                global $post;
                if ( ! is_a( $post, 'WP_Post' ) ) return false;
                return has_term( $cat, 'product_cat', $post->ID );

            case 'woo_cart_value_above':
                if ( ! function_exists( 'WC' ) ) return true;
                $wc = WC();
                if ( ! $wc || ! $wc->cart ) return true;
                $min = floatval( $settings['cond_cart_value'] ?? 0 );
                return floatval( $wc->cart->get_cart_contents_total() ) > $min;

            case 'woo_cart_value_below':
                if ( ! function_exists( 'WC' ) ) return true;
                $wc = WC();
                if ( ! $wc || ! $wc->cart ) return true;
                $max = floatval( $settings['cond_cart_value'] ?? 0 );
                return floatval( $wc->cart->get_cart_contents_total() ) < $max;

            case 'user_role_is':
                $role = $settings['cond_user_role_exact'] ?? '';
                if ( $role === '' ) return true;
                if ( ! is_user_logged_in() ) return false;
                $user = wp_get_current_user();
                return in_array( $role, $user->roles, true );

            case 'user_role_is_not':
                $role = $settings['cond_user_role_exact'] ?? '';
                if ( $role === '' ) return true;
                if ( ! is_user_logged_in() ) return true;
                $user = wp_get_current_user();
                return ! in_array( $role, $user->roles, true );

            case 'post_has_tag':
                $tag = $settings['cond_tag'] ?? '';
                if ( $tag === '' ) return true;
                return has_tag( $tag );

            case 'query_string_equals':
                $qs_key   = $settings['cond_qs_key'] ?? '';
                $qs_value = $settings['cond_qs_value'] ?? '';
                if ( $qs_key === '' ) return true;
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per filtro di visibilità (display condition query_string_equals); nessuna modifica di stato; valore sanitizzato.
                $qs_actual = isset( $_GET[ $qs_key ] ) ? sanitize_text_field( wp_unslash( $_GET[ $qs_key ] ) ) : null;
                return ( $qs_actual !== null && $qs_actual === $qs_value );

            default:
                return true;
        }
    }

    private function should_render_node( $node ) {
        // Skip tiles that are referenced by other tiles (rendered inline elsewhere)
        $node_id = $node['id'] ?? '';
        if ( $node_id !== '' && isset( self::$referenced_tile_ids[ $node_id ] ) ) {
            return false;
        }

        $adv = $node['advanced'] ?? [];

        // User role condition
        $role_cond = $adv['cond_user_role'] ?? '';
        if ( $role_cond !== '' ) {
            if ( $role_cond === 'logged_in' && ! is_user_logged_in() ) return false;
            if ( $role_cond === 'logged_out' && is_user_logged_in() ) return false;
            if ( ! in_array( $role_cond, [ 'logged_in', 'logged_out' ], true ) ) {
                // Specific role check
                $user = wp_get_current_user();
                if ( ! in_array( $role_cond, $user->roles ?? [], true ) ) return false;
            }
        }

        // Time-based conditions
        $now = current_time( 'mysql' );
        $show_from = $adv['cond_show_from'] ?? '';
        if ( $show_from !== '' && $now < str_replace( 'T', ' ', $show_from ) ) return false;

        $show_until = $adv['cond_show_until'] ?? '';
        if ( $show_until !== '' && $now > str_replace( 'T', ' ', $show_until ) ) return false;

        // Per-post condition (show only on specific posts)
        $cond_post_ids = $adv['cond_post_ids'] ?? [];
        if ( ! empty( $cond_post_ids ) && is_array( $cond_post_ids ) ) {
            $current_post_id = (string) get_the_ID();
            if ( ! in_array( $current_post_id, $cond_post_ids, true ) ) return false;
        }

        // Taxonomy condition
        $cond_taxonomy = $adv['cond_taxonomy'] ?? '';
        $cond_term     = $adv['cond_term'] ?? '';
        if ( $cond_taxonomy !== '' && $cond_term !== '' ) {
            $pid = get_the_ID();
            if ( $pid ) {
                if ( ! has_term( $cond_term, $cond_taxonomy, $pid ) ) return false;
            }
        }

        // Post type condition
        $cond_post_type = $adv['cond_post_type'] ?? '';
        if ( $cond_post_type !== '' ) {
            if ( get_post_type() !== $cond_post_type ) return false;
        }

        return true;
    }

    /**
     * True se il sottoalbero contiene almeno un tile-leaf (non strutturale).
     * Usato in builder mode per marcare i wrapper vuoti con data-olo-empty="1".
     */
    private function has_leaf_descendant( $node ) {
        static $structural = [ 'section', 'row', 'column', 'inner-columns', 'inner-column' ];
        if ( empty( $node['children'] ) || ! is_array( $node['children'] ) ) return false;
        foreach ( $node['children'] as $child ) {
            $ctype = $child['type'] ?? '';
            if ( $ctype && ! in_array( $ctype, $structural, true ) ) return true;
            if ( $this->has_leaf_descendant( $child ) ) return true;
        }
        return false;
    }

    /**
     * Render a node (recursive dispatcher).
     */
    /** Profondità di annidamento delle chiamate render_node: serve a localizzare i link UNA sola volta (sul nodo radice). */
    private $link_depth = 0;

    private function render_node( $node, $manager, $template_id, &$hover_css_rules, &$tile_counter, $parent_is_grid = false ) {
        if ( ! $this->should_render_node( $node ) ) return '';
        $this->link_depth++;

        $type = $node['type'] ?? '';
        switch ( $type ) {
            case 'section':
                $html = $this->render_section_node( $node, $manager, $template_id, $hover_css_rules, $tile_counter );
                break;
            case 'row':
                $html = $this->render_row_node( $node, $manager, $template_id, $hover_css_rules, $tile_counter );
                break;
            case 'column':
                $html = $this->render_column_node( $node, $manager, $template_id, $hover_css_rules, $tile_counter, $parent_is_grid );
                break;
            case 'inner-columns':
                $html = $this->render_inner_columns_node( $node, $manager, $template_id, $hover_css_rules, $tile_counter );
                break;
            case 'inner-column':
                $html = $this->render_inner_column_node( $node, $manager, $template_id, $hover_css_rules, $tile_counter );
                break;
            case 'floatingpanel':
                $html = $this->render_floatingpanel_node( $node, $manager, $template_id, $hover_css_rules, $tile_counter );
                break;
            default:
                $html = $this->render_element_node( $node, $manager, $template_id, $hover_css_rules, $tile_counter );
                $adv  = $node['advanced'] ?? [];
                $html = $this->maybe_lazy_wrap( $html, $type, $adv );
                break;
        }

        // In builder mode, inject data-olo-tile-id on the first HTML tag
        if ( $this->builder_mode && ! empty( $node['id'] ) && $html ) {
            $tile_id_attr = ' data-olo-tile-id="' . esc_attr( $node['id'] ) . '" data-olo-tile-type="' . esc_attr( $type ) . '"';
            // Mark structural containers without leaf descendants so the iframe CSS
            // can give them a visible min-height (otherwise they collapse to 0 and
            // are invisible in the canvas).
            $structural_types = [ 'section', 'row', 'column', 'inner-columns', 'inner-column' ];
            if ( in_array( $type, $structural_types, true ) && ! $this->has_leaf_descendant( $node ) ) {
                $tile_id_attr .= ' data-olo-empty="1"';
            }
            $html = preg_replace( '/^(\s*<\w+)/', '$1' . $tile_id_attr, $html, 1 );

            // Add data-olo-editable to text elements for inline editing
            $editable_map = [
                'headline'    => [ 'h1|h2|h3|h4|h5|h6' => 'heading' ],
                'text'        => [ 'p' => 'content' ],
                'button'      => [ 'a|button' => 'text' ],
                'iconbox'     => [ 'h3|h4|h5' => 'title', 'p' => 'description' ],
                'testimonial' => [ 'blockquote|q|p.olo-testi-quote' => 'quote' ],
                'counter'     => [ 'span.olo-counter-label|p' => 'label' ],
                'newsletter'  => [ 'h3' => 'title' ],
            ];
            if ( isset( $editable_map[ $type ] ) ) {
                foreach ( $editable_map[ $type ] as $tags => $field ) {
                    foreach ( explode( '|', $tags ) as $tag ) {
                        // Add data-olo-editable to first matching tag
                        $pattern = '/(<' . preg_quote( $tag, '/' ) . '(?:\s[^>]*)?)>/i';
                        $html = preg_replace( $pattern, '$1 data-olo-editable="' . $field . '">', $html, 1 );
                    }
                }
            }
        }

        $this->link_depth--;
        if ( $this->link_depth === 0 ) {
            $html = $this->localize_internal_links( $html );
        }

        return $html;
    }

    /**
     * Prefissa il base path dell'installazione (es. /olobuild) ai link interni
     * root-relative (href="/...") — necessario quando WordPress è installato in una
     * SOTTOCARTELLA. No-op se il sito è in root. Idempotente: salta i link che già
     * iniziano col base path, gli URL assoluti/protocol-relative, le ancore e mailto/tel.
     */
    private function localize_internal_links( $html ) {
        if ( ! is_string( $html ) || $html === '' ) return $html;
        $base = rtrim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
        if ( $base === '' || $base === '/' ) return $html; // sito in root → nessuna modifica
        return preg_replace_callback(
            '/\shref="(\/(?!\/)[^"]*)"/i',
            function ( $m ) use ( $base ) {
                $url = $m[1];
                if ( strpos( $url, $base . '/' ) === 0 || $url === $base ) return $m[0]; // già col base
                return ' href="' . $base . $url . '"';
            },
            $html
        );
    }

    /**
     * Wrap non-interactive tiles in a lazy container for deferred rendering.
     * The IntersectionObserver script in the footer will move <template> content into the DOM when visible.
     * The first 3 element tiles are rendered immediately (above the fold).
     */
    private function maybe_lazy_wrap( $html, $type, $advanced = [] ) {
        // Builder mode: no lazy loading (iframe needs all tiles visible)
        if ( $this->builder_mode ) return $html;

        // Types that must NOT be lazy-loaded (interactive, form-based, map, or relying
        // on inline scripts that won't re-run when cloned from <template>).
        static $no_lazy = [
            'form', 'map', 'search', 'livesearch', 'servicesearch', 'booking',
            'bookingpicker', 'calendar', 'loginform', 'scrollprogress',
            'popup', 'megamenu', 'navmenu', 'togglebtn',
            'blendtext', 'textmask', 'shatteredimage', 'svganimator',
            // filmreel (scroll_mode pin): riserva l'altezza della corsa al load — col
            // placeholder lazy da 50px un salto da scrollbar atterra oltre la sezione
            // prima che esista e il pin non aggancia. evonotes: layer globale che si
            // ancora alle sezioni della pagina, deve nascere al load.
            'filmreel', 'evonotes',
            // menuanchor: il punto di ancoraggio deve esistere nel DOM al load,
            // altrimenti i link #anchor (navigazione, salti alle fermate cover-h)
            // non trovano il bersaglio finché la sezione non viene materializzata.
            'menuanchor',
            // bottombar: barra fixed "sempre visibile" (credits). Il position:fixed è
            // inline nel tile (non via advanced.position_mode), quindi il check fixed più
            // sotto non lo intercetta: senza questo apparirebbe solo scrollando fino al
            // footer e poi resterebbe fisso. Deve nascere nel DOM al load.
            'bottombar',
            // marquee: con vskew o drag_scroll il runtime è uno script inline che non
            // ri-gira quando la tile viene clonata dal <template> lazy — nastro senza
            // skew né trascinamento. Tile leggera (testo/loghi): sempre eager.
            'marquee',
        ];
        if ( in_array( $type, $no_lazy, true ) ) {
            return $html;
        }

        // Famiglia OLOX (replica olotheme.com): tile scroll-driven, interattive o
        // fixed con runtime inizializzato al DOMContentLoaded (olox.js) — mai lazy.
        if ( strpos( $type, 'olox' ) === 0 ) {
            return $html;
        }

        // Effetti "tutta la pagina" (overlay fisso, es. ParticleFX/Goo con Ambito=Pagina):
        // devono attivarsi al caricamento, non quando lo scroll raggiunge il tile.
        if ( strpos( $html, '"scope":"page"' ) !== false ) {
            return $html;
        }

        // Fixed/sticky positioned tiles: placeholder won't be in viewport flow
        $pos = $advanced['position_mode'] ?? 'static';
        if ( in_array( $pos, [ 'fixed', 'sticky' ], true ) ) {
            return $html;
        }

        // Skip lazy for the first 3 element tiles (above the fold)
        static $element_counter = 0;
        $element_counter++;
        if ( $element_counter <= 3 ) {
            return $html;
        }

        // Il wrapper eredita la larghezza "Contenuto" dell'elemento: senza, un
        // div block romperebbe l'affiancamento delle tile inline (la tile lazy
        // andrebbe a capo da sola anche con tile_width=inline).
        $lazy_class = ( ( $advanced['tile_width'] ?? 'full' ) === 'inline' ) ? ' class="olo-tile-inline"' : '';
        return '<div data-olo-lazy' . $lazy_class . '><template class="olo-lazy-content">' . $html . '</template><div class="olo-lazy-ph" style="min-height:50px"></div></div>';
    }


    private function render_element_node( $node, $manager, $template_id, &$hover_css_rules, &$tile_counter ) {
        // Resolve global widget
        if ( ! empty( $node['global_id'] ) ) {
            global $wpdb;
            $t_widgets = Olobuild_Database::table( 'global_widgets' );
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- tabella custom del plugin (olobuild_global_widgets); nessun equivalente WP_Query; lookup per id su PK, valore passato via $wpdb->prepare con placeholder %d; risultato non cacheabile (può cambiare salvando il widget globale).
            $gw = $wpdb->get_row( $wpdb->prepare(
                "SELECT tile_data FROM {$t_widgets} WHERE id = %d",
                absint( $node['global_id'] )
            ) );
            if ( $gw ) {
                $resolved = json_decode( $gw->tile_data, true );
                if ( is_array( $resolved ) ) {
                    $node = array_merge( $node, $resolved );
                    // Keep the global_id for reference
                    $node['global_id'] = absint( $node['global_id'] );
                }
            }
        }

        $type = $node['type'] ?? '';
        $settings = $node['settings'] ?? [];

        // Legacy tile migration filter: permette ad altri plugin (es. olo-booking)
        // di remappare type+settings di tile legacy ai nuovi equivalenti senza
        // duplicare le classi PHP. Il filter riceve [type, settings] e può
        // restituire la stessa coppia modificata.
        // Esempio in olo-booking:
        //   add_filter( 'olobuild_tile_legacy_migrate', function( $tile, $node ) {
        //       if ( $tile['type'] === 'servicegallery' ) {
        //           $tile['type']     = 'ac-gallery';
        //           $tile['settings'] = $remapped;
        //       }
        //       return $tile;
        //   }, 10, 2 );
        $migrated = apply_filters(
            'olobuild_tile_legacy_migrate',
            [ 'type' => $type, 'settings' => $settings ],
            $node
        );
        if ( is_array( $migrated ) ) {
            if ( isset( $migrated['type'] ) && is_string( $migrated['type'] ) ) {
                $type = $migrated['type'];
            }
            if ( isset( $migrated['settings'] ) && is_array( $migrated['settings'] ) ) {
                $settings = $migrated['settings'];
            }
        }

        $tile_instance = $manager->get_tile( $type );
        if ( ! $tile_instance ) return '';

        $style    = $node['style'] ?? [];
        $advanced = $node['advanced'] ?? [];

        // Builder mode flag: i tile possono leggere $settings['_builder_mode'] per
        // disabilitare comportamenti pesanti durante l'editing (es. video autoplay,
        // form submit, mapJS init, ecc.).
        if ( $this->builder_mode ) {
            $settings['_builder_mode'] = true;
        }

        // Conditional visibility check — skip rendering entirely if condition not met
        if ( ! $this->check_conditions( $settings ) ) {
            return '';
        }

        // A/B Testing: check if this tile has an active test
        $ab_test_data = null;
        $tile_id = $node['id'] ?? '';
        if ( class_exists( 'Olobuild_AB_Testing' ) ) {
            if ( ! empty( $tile_id ) ) {
                if ( ! empty( $template_id ) ) {
                    $ab_test_data = Olobuild_AB_Testing::get_active_test_for_tile( $tile_id, $template_id );
                    if ( $ab_test_data ) {
                        // Override tile settings with the assigned variant settings
                        $variant_settings = $ab_test_data['settings'];
                        if ( is_array( $variant_settings ) ) {
                            $settings = array_merge( $settings, $variant_settings );
                        }
                    }
                }
            }
        }

        // Dynamic content resolution
        $dynamic = $node['dynamic'] ?? [];
        if ( ! empty( $dynamic ) ) {
            $dc = new Olobuild_Dynamic_Content();
            $post_id = get_the_ID();

            // Multi-item query
            if ( ! empty( $dynamic['_query']['enabled'] ) ) {
                $items_key = $this->get_items_key( $type );
                $posts = $dc->resolve_query( $dynamic['_query'] );
                $item_map = $dynamic['_itemMap'] ?? [];
                if ( ! empty( $posts ) && ! empty( $item_map ) ) {
                    $settings[ $items_key ] = $dc->build_items_from_query( $posts, $item_map );
                }
            }

            // Single field bindings
            foreach ( $dynamic as $field_key => $binding ) {
                if ( ! is_array( $binding ) ) continue;
                if ( str_starts_with( $field_key, '_' ) ) continue; // skip _query, _itemMap
                $source = $binding['source'] ?? '';
                $field  = $binding['field'] ?? '';
                if ( $source && $field ) {
                    $resolved = $dc->resolve_field( $source, $field, $post_id );
                    if ( $resolved !== null ) {
                        $settings[ $field_key ] = $resolved;
                    }
                }
            }
        }

        // Migrazione legacy hero: prima della v3.55.13 la tile hero aveva il proprio
        // sistema di sfondo nei settings (bg_type/bg_color/bg_image/bg_video/overlay_*).
        // Ora usa style.bg come tutti gli altri tile. Convertiamo on-the-fly i template
        // salvati prima della migrazione, così wrapper e overlay vengono renderizzati.
        if ( $type === 'hero' && empty( $style['bg'] ) && empty( $settings['bg'] ) && ! empty( $settings['bg_type'] ) ) {
            $settings['bg'] = $this->migrate_legacy_hero_bg( $settings );
        }

        // Per element tile il field "bg" (Sfondo creativo) è dichiarato in fields[] del config,
        // quindi viene salvato in node.settings — non in node.style come per le sezioni.
        // Merge: se settings.bg/bg_color è settato e style non lo è, usa quello dei settings.
        $bg_source = $style;
        if ( empty( $bg_source['bg'] ) && ! empty( $settings['bg'] ) )            $bg_source['bg']       = $settings['bg'];
        if ( empty( $bg_source['bg_color'] ) && ! empty( $settings['bg_color'] ) ) $bg_source['bg_color'] = $settings['bg_color'];

        // v1.0.55 — Tile ATOMICHE (button/icon/divider/spacer/togglebtn): wrapper SEMPRE
        // trasparente, ignora qualsiasi bg in style/settings (il bg appartiene all'elemento
        // interno, non al wrapper). Specchio della guardia ATOMIC_TILE_TYPES nel JS
        // useBackgroundStyle.js — regola HARD: nessun pulsante colora lo spazio circostante.
        $ATOMIC_TILES = [ 'button', 'icon', 'divider', 'spacer', 'togglebtn', 'badge' ];
        if ( in_array( $type, $ATOMIC_TILES, true ) ) {
            $bg_source = [ 'bg' => [ 'type' => 'none' ] ];
        }

        $tile_bg      = $this->css->get_effective_bg( $bg_source );
        $is_fullwidth = ! empty( $style['full_width'] );
        $has_bg_image   = ( $tile_bg['type'] === 'image' && ! empty( $tile_bg['image_url'] ) );
        $has_bg_video   = ( $tile_bg['type'] === 'video' && ! empty( $tile_bg['video_url'] ) );
        $has_bg_gallery = ( $tile_bg['type'] === 'gallery' && ! empty( $tile_bg['gallery_images'] ) && is_array( $tile_bg['gallery_images'] ) );
        $has_bg_any     = ( $tile_bg['type'] !== 'none' );
        $has_overlay    = ( $has_bg_any && ! empty( $tile_bg['overlay_opacity'] ) && intval( $tile_bg['overlay_opacity'] ) > 0 );

        // Build inline styles (custom values that UIkit can't handle)
        $inline_styles = [];

        // Background base (solo se non c'è image/video — quelli sono layer separati)
        if ( ! $has_bg_image && ! $has_bg_video ) {
            $bg_css = $this->css->get_bg_inline_css( $tile_bg );
            if ( $bg_css ) $inline_styles[] = $bg_css;
        }

        // v1.0.55 — Per tile atomiche il wrapper NON eredita text_color/border_radius/shadow:
        // quei valori vanno SOLO sull'elemento interno, non sull'area circostante.
        $is_atomic_tile = in_array( $type, $ATOMIC_TILES, true );

        // Text color (preset stilistici applicano color al wrapper → i discendenti ereditano).
        if ( ! $is_atomic_tile && ! empty( $style['text_color'] ) ) {
            $inline_styles[] = 'color: ' . esc_attr( $style['text_color'] );
        }

        // UIkit shadow class — solo per elementi con sfondo (box-shadow segue border-radius).
        // Per elementi trasparenti, drop-shadow filter viene applicato dopo l'helper.
        $shadow_class = '';
        if ( ! empty( $style['shadow'] ) && $has_bg_any ) {
            $uk_shadow_map = [
                'sm' => 'uk-box-shadow-small',
                'md' => 'uk-box-shadow-medium',
                'lg' => 'uk-box-shadow-large',
                'xl' => 'uk-box-shadow-xlarge',
            ];
            if ( isset( $uk_shadow_map[ $style['shadow'] ] ) ) {
                $shadow_class = $uk_shadow_map[ $style['shadow'] ];
            }
        }

        // $pos_mode è usato anche più sotto (scrollspy_attr, sticky_attr, fixed-position
        // body-mount) — lo calcoliamo qui per averlo disponibile fuori dall'helper.
        $pos_mode = $advanced['position_mode'] ?? 'static';

        // Helper unificato: margin/padding/border-radius/border/opacity/flex/transform/
        // box-shadow inline/text-shadow/backdrop/overflow/dimensions/mask/custom_css/position.
        // `apply_box_shadow=false` per element trasparenti — usano drop-shadow filter sotto.
        // v1.0.55 — Per atomic: passiamo style filtrato (no border_radius/border/shadow), così
        // l'helper applica solo margin/padding/dimensions/flex/transform/etc. al wrapper.
        $box_style = $style;
        if ( $is_atomic_tile ) {
            unset( $box_style['border_radius'], $box_style['border'], $box_style['shadow'],
                   $box_style['box_shadow_h'], $box_style['box_shadow_v'], $box_style['box_shadow_blur'],
                   $box_style['box_shadow_spread'], $box_style['box_shadow_color'], $box_style['box_shadow_inset'] );
        }
        $this->apply_common_box_styles(
            $inline_styles, $box_style, $settings, $advanced,
            [ 'apply_box_shadow' => (bool) $has_bg_any && ! $is_atomic_tile ]
        );

        // Drop-shadow filter per element trasparenti (segue forma SVG/icone via clip-path).
        // Non per atomic: il drop-shadow apparirebbe attorno all'area del wrapper.
        if ( ! $has_bg_any && ! $is_atomic_tile ) {
            $drop_shadow = $this->css->build_drop_shadow_css( $style );
            if ( $drop_shadow ) {
                $inline_styles[] = 'filter: ' . $drop_shadow;
            }
        }

        $style_attr = implode( '; ', $inline_styles );

        // Build classes
        $classes = [ 'olo-frontend-tile' ];
        if ( $shadow_class ) $classes[] = $shadow_class;
        if ( $is_fullwidth ) $classes[] = 'olo-tile-fullwidth';
        // Larghezza adattata al contenuto (Avanzate → Posizionamento): tile
        // inline-block, più tile "adattate" consecutive si affiancano.
        if ( ( $advanced['tile_width'] ?? 'full' ) === 'inline' ) $classes[] = 'olo-tile-inline';
        if ( $has_bg_image || $has_bg_video || $has_bg_gallery || $has_overlay ) { $classes[] = 'uk-position-relative'; $inline_styles[] = 'overflow: clip'; }
        if ( ! empty( $style['border_radius'] ) ) $inline_styles[] = 'overflow: clip';
        if ( ! empty( $advanced['css_classes'] ) ) {
            $classes[] = esc_attr( $advanced['css_classes'] );
        }

        // Entrance animation
        $entrance = $settings['entrance_animation'] ?? 'none';
        if ( $entrance && $entrance !== 'none' ) {
            $classes[] = 'olo-entrance-' . sanitize_html_class( $entrance );
            $classes[] = 'olo-visible'; // applicata subito: l'animation parte al page-load (no IntersectionObserver dependency)
            // Stagger: animate children sequentially
            if ( ! empty( $settings['entrance_stagger'] ) ) {
                $stagger_delay = intval( $settings['entrance_stagger_delay'] ?? 100 );
                $stagger_delay = max( 25, min( 500, $stagger_delay ) );
                $classes[] = 'olo-stagger-parent';
                $inline_styles[] = '--olo-stagger-delay: ' . $stagger_delay . 'ms';
            }
        }

        // Responsive visibility — 5 breakpoints
        if ( isset( $advanced['visible_desktop'] ) && $advanced['visible_desktop'] === false ) {
            $classes[] = 'olo-hidden-desktop';
        }
        if ( isset( $advanced['visible_tablet_landscape'] ) && $advanced['visible_tablet_landscape'] === false ) {
            $classes[] = 'olo-hidden-tablet-landscape';
        }
        if ( isset( $advanced['visible_tablet'] ) && $advanced['visible_tablet'] === false ) {
            $classes[] = 'olo-hidden-tablet';
        }
        if ( isset( $advanced['visible_mobile_landscape'] ) && $advanced['visible_mobile_landscape'] === false ) {
            $classes[] = 'olo-hidden-mobile-landscape';
        }
        if ( isset( $advanced['visible_mobile'] ) && $advanced['visible_mobile'] === false ) {
            $classes[] = 'olo-hidden-mobile';
        }

        // HTML ID
        $tile_counter++;
        $css_id  = ! empty( $advanced['html_id'] ) ? $advanced['html_id'] : 'mt-' . $template_id . '-' . $tile_counter;
        $id_attr = ' id="' . esc_attr( $css_id ) . '"';

        // Hover CSS rules
        $this->collect_hover_css( $style, $css_id, $is_fullwidth, $hover_css_rules, $advanced );
        $this->collect_responsive_css( $style, $css_id, $advanced );

        // Custom CSS per elemento (campo settings.custom_css)
        $this->collect_custom_css( $settings, $css_id, $hover_css_rules );

        // Infinite/loop animation (Galleggiamento, ecc.) — Avanzate → "Animazione continua".
        // Le sezioni la rendono già; qui la abilitiamo anche per le TILE (es. badge flottante
        // su un'immagine). Usa il builder per-id parametrico (ampiezza/ritardo/reduced-motion).
        $elem_inf_anim_css = $this->css->build_infinite_animation_css( $advanced, $css_id );
        if ( $elem_inf_anim_css ) {
            $hover_css_rules[] = $elem_inf_anim_css;
        }

        // Scrollspy & element parallax attributes (skip for fixed tiles — handled by sentinel JS)
        $elem_scrollspy_attr = ( $pos_mode === 'fixed' ) ? '' : $this->anim->build_scrollspy_attr( $advanced );
        $elem_el_parallax_attr = $this->anim->build_element_parallax_attr( $advanced );

        // Sticky — skip if tile is already fixed-positioned or is a megamenu
        //
        // v3.55.47 — switch da `uk-sticky` JS (sticky GLOBALE, non si sblocca mai)
        // a CSS nativo `position: sticky` che è LIMITATO al parent: quando il
        // container genitore termina, l'elemento si sblocca e torna a scorrere
        // naturalmente. È il comportamento atteso per layout immagine+testo.
        //
        // Requisiti `position: sticky` CSS:
        //  1. align-self: start (no stretch) — sticky non funziona su elementi stretched
        //  2. il parent non deve avere overflow: hidden (clip è ok)
        //  3. il parent deve essere più alto dell'elemento (altrimenti niente range scroll)
        //  4. !important sull'align-self perché UIkit applica `align-self: stretch`
        //     di default a `.uk-grid > *` con specificity più alta dell'inline.
        $elem_sticky_inline_css = '';
        $sticky_on              = ! empty( $advanced['sticky'] ) || ! empty( $settings['sticky'] );
        if ( $sticky_on && $pos_mode !== 'fixed' && $type !== 'megamenu' ) {
            $sticky_pos    = $advanced['sticky_position'] ?? $settings['sticky_position'] ?? 'top';
            $sticky_offset = intval( $advanced['sticky_offset'] ?? $settings['sticky_offset'] ?? 0 );
            $sticky_mobile = $advanced['sticky_on_mobile'] ?? $settings['sticky_on_mobile'] ?? true;
            $pos_prop      = $sticky_pos === 'bottom' ? 'bottom' : 'top';
            $elem_sticky_inline_css = 'position: sticky; ' . $pos_prop . ': ' . $sticky_offset
                . 'px; align-self: start; z-index: 10';
            if ( ! $sticky_mobile ) {
                // CSS in frontend.css → @media (max-width:640px) { .olo-sticky-desktop-only{position:static!important} }
                $classes[] = 'olo-sticky-desktop-only';
            }
        }
        // Append sticky CSS a $style_attr (già assemblato a riga ~2629).
        if ( $elem_sticky_inline_css !== '' ) {
            $style_attr = $style_attr ? $style_attr . '; ' . $elem_sticky_inline_css : $elem_sticky_inline_css;
        }
        // Variabile mantenuta per compatibilità riga 2811, ora sempre vuota.
        $elem_sticky_attr = '';

        // Mouse effects data attributes (leggi da settings OPPURE advanced — il pannello
        // "Effetti mouse" del builder salva in advanced, lo styleField _shared in settings).
        $elem_mouse_attrs = '';
        if ( ! empty( $settings['mouse_tilt'] ) || ! empty( $advanced['mouse_tilt'] ) ) {
            $tilt_intensity = intval( $settings['mouse_tilt_intensity'] ?? $advanced['mouse_tilt_intensity'] ?? 15 );
            // target 'items': il tilt va sulle foto/media INTERNI (gallerie, griglie)
            // invece che sul blocco — il runtime espande l'attr sugli img/video figli.
            $tilt_target = $settings['mouse_tilt_target'] ?? $advanced['mouse_tilt_target'] ?? 'block';
            $tilt_attr   = ( 'items' === $tilt_target ) ? 'data-olo-tilt-items' : 'data-olo-tilt';
            $elem_mouse_attrs .= ' ' . $tilt_attr . '="' . $tilt_intensity . '"';
        }
        if ( ! empty( $settings['mouse_track'] ) || ! empty( $advanced['mouse_track'] ) ) {
            $track_speed = intval( $settings['mouse_track_speed'] ?? $advanced['mouse_track_speed'] ?? 3 );
            $elem_mouse_attrs .= ' data-olo-track="' . $track_speed . '"';
        }

        // Spotlight cursore — riusabile su section/column/row/element (vedi Olobuild_Animation_Builder::build_spotlight_attr)
        $elem_spotlight_attr = $this->anim->build_spotlight_attr( $advanced );

        // Bezier path scroll animation
        $elem_bezier_attr = '';
        if ( ! empty( $advanced['bezier_path'] ) && is_array( $advanced['bezier_path'] ) ) {
            $elem_bezier_attr = " data-olo-bezier-parallax='" . wp_json_encode( $advanced['bezier_path'] ) . "'";
        }

        // Scroll-linked effects data attribute
        $elem_scroll_fx_attr = '';
        $scroll_fx = [];
        if ( ! empty( $settings['scroll_effect_opacity'] ) ) {
            $scroll_fx['opacity'] = [
                intval( $settings['scroll_opacity_start'] ?? 0 ) / 100,
                intval( $settings['scroll_opacity_end'] ?? 100 ) / 100,
            ];
        }
        if ( ! empty( $settings['scroll_effect_scale'] ) ) {
            $scroll_fx['scale'] = [
                intval( $settings['scroll_scale_start'] ?? 80 ) / 100,
                intval( $settings['scroll_scale_end'] ?? 100 ) / 100,
            ];
        }
        if ( ! empty( $settings['scroll_effect_rotate'] ) ) {
            $scroll_fx['rotate'] = [
                intval( $settings['scroll_rotate_start'] ?? -15 ),
                intval( $settings['scroll_rotate_end'] ?? 0 ),
            ];
        }
        if ( ! empty( $settings['scroll_effect_translatex'] ) ) {
            $scroll_fx['translatex'] = [
                intval( $settings['scroll_translatex_start'] ?? -50 ),
                intval( $settings['scroll_translatex_end'] ?? 0 ),
            ];
        }
        if ( ! empty( $settings['scroll_effect_fill'] ) ) {
            $scroll_fx['fill'] = [
                intval( $settings['scroll_fill_start'] ?? 0 ),
                intval( $settings['scroll_fill_end'] ?? 100 ),
            ];
        }
        if ( ! empty( $scroll_fx ) ) {
            $elem_scroll_fx_attr = " data-olo-scroll-fx='" . esc_attr( wp_json_encode( $scroll_fx ) ) . "'";
        }

        // ScrollAssembly (preset Parallax multi-target): genitore (data-olo-assembly) +
        // parti figlie (data-olo-part) che si "montano" su UN unico progress del genitore.
        $elem_assembly_attr = '';
        if ( ! empty( $advanced['scroll_assembly'] ) ) {
            $elem_assembly_attr .= ' data-olo-assembly';
        }
        if ( ! empty( $advanced['assembly_part'] ) ) {
            $part = [
                'x'     => intval( $advanced['assembly_from_x'] ?? 0 ),
                'y'     => intval( $advanced['assembly_from_y'] ?? 0 ),
                's'     => floatval( $advanced['assembly_from_scale'] ?? 1.2 ),
                'r'     => intval( $advanced['assembly_from_rotate'] ?? 0 ),
                'start' => max( 0, min( 1, floatval( $advanced['assembly_start'] ?? 0 ) / 100 ) ),
                'end'   => max( 0, min( 1, floatval( $advanced['assembly_end'] ?? 60 ) / 100 ) ),
            ];
            $elem_assembly_attr .= " data-olo-part='" . esc_attr( wp_json_encode( $part ) ) . "'";
        }

        // A/B test data attributes for frontend tracking
        $ab_test_attrs = '';
        if ( $ab_test_data ) {
            $ab_test_attrs .= ' data-olo-ab-test="' . intval( $ab_test_data['test_id'] ) . '"';
            $ab_test_attrs .= ' data-olo-ab-variant="' . esc_attr( $ab_test_data['variant'] ) . '"';
            $ab_test_attrs .= ' data-olo-ab-goal="' . esc_attr( $ab_test_data['goal_type'] ) . '"';
            if ( ! empty( $ab_test_data['goal_selector'] ) ) {
                $ab_test_attrs .= ' data-olo-ab-goal-selector="' . esc_attr( $ab_test_data['goal_selector'] ) . '"';
            }
        }

        // Developer hook: before tile render
        do_action( 'olobuild_before_tile_render', $node, $settings, $type );

        // SEO & Accessibility attributes
        $seo_attrs = '';
        if ( ! empty( $advanced['aria_label'] ) ) {
            $seo_attrs .= ' aria-label="' . esc_attr( $advanced['aria_label'] ) . '"';
        }
        if ( ! empty( $advanced['aria_role'] ) && $advanced['aria_role'] !== 'none' ) {
            $seo_attrs .= ' role="' . esc_attr( $advanced['aria_role'] ) . '"';
        } elseif ( ! empty( $advanced['aria_role'] ) && $advanced['aria_role'] === 'none' ) {
            $seo_attrs .= ' role="presentation" aria-hidden="true"';
        }
        if ( ! empty( $advanced['data_attrs'] ) ) {
            foreach ( explode( ',', $advanced['data_attrs'] ) as $pair ) {
                $pair = trim( $pair );
                if ( str_contains( $pair, '=' ) ) {
                    list( $dk, $dv ) = array_map( 'trim', explode( '=', $pair, 2 ) );
                    $seo_attrs .= ' data-' . esc_attr( $dk ) . '="' . esc_attr( $dv ) . '"';
                }
            }
        }

        // Schema.org
        if ( ! empty( $advanced['schema_type'] ) ) {
            $seo_attrs .= ' itemscope itemtype="https://schema.org/' . esc_attr( $advanced['schema_type'] ) . '"';
        }

        // Pass loading/fetchpriority to tile via settings override
        if ( ! empty( $advanced['img_loading'] ) && $advanced['img_loading'] !== 'lazy' ) {
            $settings['_img_loading'] = $advanced['img_loading'];
        }
        if ( ! empty( $advanced['fetch_priority'] ) && $advanced['fetch_priority'] !== 'auto' ) {
            $settings['_fetch_priority'] = $advanced['fetch_priority'];
        }
        if ( ! empty( $advanced['link_rel'] ) ) {
            $settings['_link_rel'] = $advanced['link_rel'];
        }
        if ( ! empty( $advanced['link_title'] ) ) {
            $settings['_link_title'] = $advanced['link_title'];
        }

        // Render
        ob_start();
        ?>
        <div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php echo $id_attr; ?> style="<?php echo esc_attr( $style_attr ); ?>"<?php echo $elem_scrollspy_attr . $elem_el_parallax_attr . $elem_sticky_attr . $elem_mouse_attrs . $elem_bezier_attr . $elem_scroll_fx_attr . $elem_spotlight_attr . $elem_assembly_attr . $ab_test_attrs . $seo_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $id_attr esc_attr()'d at assignment; attribute fragments built above with esc_attr()/wp_json_encode()/intval() or by Olobuild_Animation_Builder helpers from clamped numerics ?>>
            <?php if ( $has_bg_image ) :
                $bg_size = esc_attr( $tile_bg['image_size'] ?? 'cover' );
                $bg_pos  = esc_attr( $tile_bg['image_position'] ?? 'center center' );
                $parallax_attr = $this->anim->build_uk_parallax_attr( $tile_bg );
            ?>
                <div class="uk-position-cover"
                    style="background-image: url(<?php echo esc_url( $tile_bg['image_url'] ); ?>); background-size: <?php echo $bg_size; ?>; background-position: <?php echo $bg_pos; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $bg_size/$bg_pos esc_attr()'d at assignment above ?>; background-repeat: no-repeat"
                    <?php echo $parallax_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- uk-parallax attribute built by Olobuild_Animation_Builder::build_uk_parallax_attr() from intval()/floatval() values ?>
                ></div>
            <?php endif; ?>

            <?php if ( $has_bg_video ) :
                $vid_url    = esc_url( $tile_bg['video_url'] );
                $vid_poster = ! empty( $tile_bg['video_poster'] ) ? esc_url( $tile_bg['video_poster'] ) : '';
                $vid_pos    = esc_attr( $tile_bg['image_position'] ?? 'center center' );
                $vid_fit    = esc_attr( $tile_bg['video_fit'] ?? 'cover' );
            ?>
                <video aria-hidden="true" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: <?php echo $vid_fit; ?>; object-position: <?php echo $vid_pos; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $vid_fit/$vid_pos esc_attr()'d and $vid_poster/$vid_url esc_url()'d at assignment above ?>; pointer-events: none" autoplay muted loop playsinline<?php if ( $vid_poster ) echo ' poster="' . $vid_poster . '"'; ?>><source src="<?php echo $vid_url; ?>" type="<?php echo esc_attr( $this->get_video_mime( $vid_url ) ); ?>"></video>
            <?php endif; ?>

            <?php if ( $has_overlay ) :
                $ov_color   = esc_attr( $tile_bg['overlay_color'] ?? '#000000' );
                $ov_opacity = intval( $tile_bg['overlay_opacity'] ) / 100;
            ?>
                <div class="uk-position-cover" style="background-color: <?php echo $ov_color; ?>; opacity: <?php echo $ov_opacity; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $ov_color esc_attr()'d at assignment above; $ov_opacity is intval()/100 ?>; pointer-events: none" aria-hidden="true"></div>
            <?php endif; ?>

            <?php if ( $this->builder_mode ) $settings['_builder_mode'] = true; ?>
            <?php if ( $has_bg_image || $has_bg_video || $has_bg_gallery || $has_overlay ) : ?>
                <div class="uk-position-relative" style="z-index: 1">
                    <?php echo Olobuild_Tile_Utils::process_dynamic_tags( $tile_instance->render( $settings, $node['style'] ?? [] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tile HTML assembled by the tile's own render() (each tile escapes its output); process_dynamic_tags() substitutes sanitized dynamic values ?>
                </div>
            <?php else : ?>
                <?php echo Olobuild_Tile_Utils::process_dynamic_tags( $tile_instance->render( $settings, $node['style'] ?? [] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tile HTML assembled by the tile's own render() (each tile escapes its output); process_dynamic_tags() substitutes sanitized dynamic values ?>
            <?php endif; ?>
        </div>
        <?php
        $html = ob_get_clean();

        // Fixed-position tiles: move to <body> (escape overflow:clip).
        // Navigation tiles (megamenu, navmenu) are always visible.
        // Other tiles start hidden and appear when scrolling past sentinel.
        if ( $pos_mode === 'fixed' ) {
            $js_id = esc_js( $css_id );
            $always_visible = in_array( $type, [ 'megamenu', 'navmenu', 'togglebtn' ], true );

            if ( $always_visible ) {
                // Menu tiles: just move to body, always visible
                $html .= '<script>(function(){'
                    . 'var el=document.getElementById("' . $js_id . '");'
                    . 'if(el){document.body.appendChild(el)}'
                    . '})()</script>';
            } else {
                // Other tiles: sentinel-based scroll show
                $sid = esc_attr( $css_id ) . '-sentinel';
                $html .= '<div id="' . $sid . '" style="height:1px;margin:-1px 0 0 0" aria-hidden="true"></div>';
                $js_sid = esc_js( $sid );
                $spy_anim = $advanced['scrollspy_animation'] ?? '';
                $anim_cls = $spy_anim ? 'uk-animation-' . esc_js( $spy_anim ) : 'uk-animation-fade';
                $spy_repeat = ! empty( $advanced['scrollspy_repeat'] );
                $repeat_js = $spy_repeat ? 'true' : 'false';
                $hide_at = trim( $advanced['position_hide_at'] ?? '' );
                $hide_at_js = $hide_at ? esc_js( ltrim( $hide_at, '#' ) ) : '';
                $html .= '<script>(function(){'
                    . 'var el=document.getElementById("' . $js_id . '");'
                    . 'if(!el)return;'
                    . 'el.style.visibility="hidden";'
                    . 'document.body.appendChild(el);'
                    . 'var sn=document.getElementById("' . $js_sid . '");'
                    . 'if(!sn)return;'
                    . 'var shown=false;var rep=' . $repeat_js . ';'
                    . 'var cls="' . $anim_cls . '";'
                    . 'var grid=sn.closest(".olo-frontend-grid,.olo-tile-content");'
                    . 'var triggerY=0;'
                    . 'if(grid){'
                    .   'var secs=grid.querySelectorAll(":scope>section");'
                    .   'var mySec=sn.closest("section");'
                    .   'for(var i=0;i<secs.length;i++){'
                    .     'if(secs[i]===mySec)break;'
                    .     'triggerY+=secs[i].scrollHeight'
                    .   '}'
                    . '}'
                    . ( $hide_at_js
                        ? 'var hideTarget=document.getElementById("' . $hide_at_js . '");'
                        : 'var hideTarget=null;' )
                    . 'function check(){'
                    .   'var sy=window.scrollY;'
                    .   'var pastStart=sy>=triggerY;'
                    .   'var pastEnd=false;'
                    .   'if(hideTarget){'
                    .     'var hr=hideTarget.getBoundingClientRect();'
                    .     'pastEnd=hr.top<=window.innerHeight*0.5'
                    .   '}'
                    .   'if(pastStart){if(!pastEnd){'
                    .     'if(!shown){shown=true;el.style.visibility="visible";el.classList.add(cls)}'
                    .   '}else{'
                    .     'if(shown){shown=false;el.style.visibility="hidden";el.classList.remove(cls)}'
                    .   '}}else{'
                    .     'if(shown){if(rep){shown=false;el.style.visibility="hidden";el.classList.remove(cls)}}'
                    .   '}'
                    . '}'
                    . 'check();'
                    . 'window.addEventListener("scroll",check,{passive:true})'
                    . '})()</script>';
            }
        }

        // Custom JS per tile (from advanced settings)
        $custom_js = trim( $advanced['custom_js'] ?? '' );
        if ( $custom_js ) {
            $el_selector = '.olo-el-' . esc_js( $node['id'] );
            $html .= '<script>(function(){var el=document.querySelector("' . $el_selector . '");if(el){' . $custom_js . '}})()</script>';
        }

        // Developer hook: after tile render
        do_action( 'olobuild_after_tile_render', $node, $settings, $type, $html );

        // Accessibility: ARIA enhancements
        $html = apply_filters( 'olobuild_tile_output', $html, $type, $settings );

        // Resolve inline dynamic tokens ({post_title}, {current_year}, etc.)
        $html = Olobuild_Dynamic_Content::resolve_tokens( $html );
        return $html;
    }

    /**
     * Migra i campi legacy bg_type/bg_color/bg_image/bg_video/overlay_* della tile hero
     * (formato pre-v3.55.13) all'oggetto bg unificato di BackgroundControls.
     * Chiamata on-the-fly al render — non modifica il template salvato.
     *
     * @param array $s Settings flat legacy.
     * @return array Bg object: { type, color|gradient_*|image_*|video_*, overlay_* }
     */
    private function migrate_legacy_hero_bg( $s ) {
        $type = $s['bg_type'] ?? 'solid';
        $bg   = [ 'type' => 'none' ];

        switch ( $type ) {
            case 'color':
                $bg = [ 'type' => 'solid', 'color' => $s['bg_color'] ?: '#1F2937' ];
                break;
            case 'gradient':
                $bg = [
                    'type'           => 'gradient',
                    'gradient_from'  => $s['bg_gradient_from'] ?: '#6366F1',
                    'gradient_to'    => $s['bg_gradient_to']   ?: '#8B5CF6',
                    'gradient_angle' => intval( $s['bg_gradient_angle'] ?: 135 ),
                ];
                break;
            case 'image':
                $pos_map = [
                    'center' => 'center center', 'top' => 'top center', 'bottom' => 'bottom center',
                    'left'   => 'center left',   'right' => 'center right',
                ];
                $pos = $s['bg_position'] ?? 'center';
                $bg = [
                    'type'           => 'image',
                    'image_url'      => $s['bg_image'] ?: '',
                    'image_size'     => $s['bg_size']  ?: 'cover',
                    'image_position' => $pos_map[ $pos ] ?? 'center center',
                    'image_parallax' => ! empty( $s['bg_fixed'] ),
                ];
                break;
            case 'video':
                $bg = [
                    'type'       => 'video',
                    'video_url'  => $s['bg_video'] ?: '',
                    'video_size' => $s['bg_size']  ?: 'cover',
                ];
                break;
            default:
                $bg = [ 'type' => 'solid', 'color' => '#1F2937' ];
        }

        if ( ! empty( $s['overlay'] ) ) {
            $bg['overlay_color']   = $s['overlay_color'] ?: '#000000';
            $bg['overlay_opacity'] = intval( $s['overlay_opacity'] ?: 50 );
        }

        return $bg;
    }


    /**
     * Single-pass node tree scan: ritorna tutti i "signature" usati per decidere
     * gli script da enqueue. Sostituisce 7 funzioni `check_*_recursive` che
     * facevano 12+ visite separate dell'albero (ognuna O(N)).
     *
     * Output:
     *   [
     *     'types'          => ['postgrid' => true, 'pdfviewer' => true, ...],
     *     'has_leaflet_map'        => bool (map con mode=locations|services)
     *     'has_row_loop_load_more' => bool
     *     'has_bezier'             => bool (advanced.bezier_path settato su qualsiasi tile)
     *     'has_progallery_lightbox'=> bool (progallery con lightbox_thumbs ∈ bottom/right/left)
     *   ]
     *
     * Costo: 1 visita O(N) invece di 12. Per template con 200 tile: ~200 confronti
     * vs ~2400 con i metodi singoli.
     */
    private function collect_node_signatures( $tiles ) {
        $sig = [
            'types'                   => [],
            'has_leaflet_map'         => false,
            'has_row_loop_load_more'  => false,
            'has_bezier'              => false,
            'has_progallery_lightbox' => false,
        ];
        $this->walk_signatures( $tiles, $sig );
        return $sig;
    }

    private function walk_signatures( $nodes, &$sig ) {
        if ( ! is_array( $nodes ) ) return;
        foreach ( $nodes as $node ) {
            $type = $node['type'] ?? '';
            if ( $type ) $sig['types'][ $type ] = true;

            // map: distingue mode (locations/services richiede leaflet)
            if ( $type === 'map' ) {
                $mode = $node['settings']['mode'] ?? 'single';
                if ( $mode === 'locations' || $mode === 'services' ) {
                    $sig['has_leaflet_map'] = true;
                }
            }

            // row con loop "load more"
            if ( $type === 'row' ) {
                $s = $node['settings'] ?? [];
                if ( ! empty( $s['loop_enabled'] ) && ( $s['loop_pagination'] ?? 'none' ) === 'load_more' ) {
                    $sig['has_row_loop_load_more'] = true;
                }
            }

            // bezier_path è in advanced (qualsiasi tile)
            if ( ! empty( $node['advanced']['bezier_path'] ) ) {
                $sig['has_bezier'] = true;
            }

            // progallery con thumbs custom
            if ( $type === 'progallery' ) {
                $thumbs = $node['settings']['lightbox_thumbs'] ?? 'none';
                if ( $thumbs === 'bottom' || $thumbs === 'right' || $thumbs === 'left' ) {
                    $sig['has_progallery_lightbox'] = true;
                }
            }

            if ( ! empty( $node['children'] ) ) {
                $this->walk_signatures( $node['children'], $sig );
            }
        }
    }

    /**
     * Get video MIME type from URL.
     */
    private function get_video_mime( $url ) {
        $ext = strtolower( pathinfo( wp_parse_url( $url, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
        $map = [ 'mp4' => 'video/mp4', 'webm' => 'video/webm', 'ogg' => 'video/ogg' ];
        return $map[ $ext ] ?? 'video/mp4';
    }

    // =========================================================================
    // Shortcode entry point
    // =========================================================================


    /**
     * Public wrapper for render_node (used by REST API for single tile rendering).
     */
    public function render_node_public( $node, $manager, $template_id, &$hover_css_rules, &$tile_counter, $parent_is_grid = false ) {
        return $this->render_node( $node, $manager, $template_id, $hover_css_rules, $tile_counter, $parent_is_grid );
    }

    /**
     * Render for builder iframe: sets builder_mode and returns HTML + CSS.
     */
    public function render_for_builder( $tiles, $page_settings = [] ) {
        $this->builder_mode = true;

        ob_start();
        $this->render_tiles_array( $tiles, $page_settings );
        $html = ob_get_clean();

        $css_urls = [
            OLOBUILD_URL . 'assets/vendor/uikit/css/uikit.min.css',
            OLOBUILD_URL . 'assets/css/frontend.css?v=' . OLOBUILD_VERSION,
            OLOBUILD_URL . 'assets/css/olo-proslider.css?v=' . OLOBUILD_VERSION,
        ];

        $inline_css = '';
        if ( class_exists( 'Olobuild_Style_System' ) ) {
            $inline_css = Olobuild_Style_System::instance()->generate_css();
        }

        $this->builder_mode = false;

        return [
            'html'       => $html,
            'css'        => $css_urls,
            'inline_css' => $inline_css,
        ];
    }

    /**
     * Render an array of tiles (used for builder preview of header/footer).
     * Outputs HTML directly (use with ob_start/ob_get_clean).
     */
    /**
     * Effetti di pagina (Impostazioni Pagina → Effetti di pagina): decoratori
     * full-viewport renderizzati DOPO il grid. Unico punto di verità, chiamato
     * sia dal render frontend del template sia da render_tiles_array (preview).
     *
     *   - page_crt_*   → Overlay CRT (scanline + vignetta), Olobuild_Crtoverlay_Tile::render()
     *   - page_grain_* → Grana pellicola, Olobuild_Crtoverlay_Tile::render_grain()
     *
     * @param array $page_settings
     * @return string HTML ('' se nessun effetto attivo)
     */
    private function render_page_effects( $page_settings ) {
        if ( ! is_array( $page_settings ) || ! class_exists( 'Olobuild_Crtoverlay_Tile' ) ) {
            return '';
        }
        $out = '';

        if ( ! empty( $page_settings['page_crt_enabled'] ) ) {
            $out .= ( new Olobuild_Crtoverlay_Tile() )->render( [
                'scanline_opacity' => intval( $page_settings['page_crt_scanline_opacity'] ?? 50 ),
                'scanline_gap'     => intval( $page_settings['page_crt_scanline_gap'] ?? 3 ),
                'vignette'         => intval( $page_settings['page_crt_vignette'] ?? 55 ),
                'blend_mode'       => $page_settings['page_crt_blend_mode'] ?? 'overlay',
                'flicker'          => ! empty( $page_settings['page_crt_flicker'] ),
                'flicker_speed'    => intval( $page_settings['page_crt_flicker_speed'] ?? 8 ),
                'z_index'          => intval( $page_settings['page_crt_z_index'] ?? 200 ),
            ] );
        }

        if ( ! empty( $page_settings['page_grain_enabled'] ) ) {
            $out .= Olobuild_Crtoverlay_Tile::render_grain( [
                'opacity' => intval( $page_settings['page_grain_opacity'] ?? 7 ),
                'size'    => intval( $page_settings['page_grain_size'] ?? 240 ),
                'z_index' => intval( $page_settings['page_grain_z_index'] ?? 95 ),
                'animate' => array_key_exists( 'page_grain_animate', $page_settings ) ? ! empty( $page_settings['page_grain_animate'] ) : true,
                'mobile'  => ! empty( $page_settings['page_grain_mobile'] ),
            ] );
        }

        return $out;
    }

    public function render_tiles_array( $tiles, $page_settings = [] ) {
        if ( empty( $tiles ) || ! is_array( $tiles ) ) {
            return;
        }

        $tiles = $this->maybe_migrate_content( $tiles );

        $content_max_width = intval( $page_settings['content_max_width'] ?? 1200 );

        $this->breakpoints = wp_parse_args( $page_settings['breakpoints'] ?? [], [
            'widescreen'       => 1400,
            'tablet_landscape' => 1200,
            'tablet'           => 960,
            'mobile_landscape' => 640,
            'mobile'           => 480,
        ] );

        $manager = Olobuild_Tile_Manager::instance();
        $hover_css_rules = [];
        $this->responsive_css_rules = [];
        $tile_counter = 0;

        echo '<div class="olo-frontend-grid olo-tile-content" style="--olo-content-width: ' . ( $content_max_width >= 9999 ? '100%' : (int) $content_max_width . 'px' ) . '; --olo-container-max-width: ' . ( $content_max_width >= 9999 ? 'none' : (int) $content_max_width . 'px' ) . '">';
        foreach ( $tiles as $section ) {
            echo $this->render_node( $section, $manager, 0, $hover_css_rules, $tile_counter ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- section/row/tile HTML assembled by render_node(); escaping is performed by the node renderers and each tile's render()
        }
        echo '</div>';

        // Effetti di pagina (Impostazioni Pagina → Effetti di pagina).
        echo $this->render_page_effects( $page_settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup assembled by render_page_effects() from sanitized settings

        if ( ! empty( $hover_css_rules ) || ! empty( $this->responsive_css_rules ) ) {
            $all_css = implode( ' ', $hover_css_rules );
            foreach ( $this->responsive_css_rules as $max_w => $rules ) {
                $all_css .= ' @media(max-width:' . $max_w . '){' . implode( ' ', $rules ) . '}';
            }
            echo '<style class="olo-hover-styles">' . $all_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS collected via collect_hover_css()/collect_responsive_css() (esc_attr/intval-sanitized declarations) and safe_block_css() for custom CSS
        }
    }

}
