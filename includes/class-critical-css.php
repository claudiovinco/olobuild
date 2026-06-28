<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Critical CSS Generation for Olobuild.
 *
 * Generates and caches above-the-fold CSS for pages with Olobuild templates.
 * Stores in transients with 7-day TTL, invalidated on template save.
 */
class Olo_Critical_CSS {

    /**
     * Transient TTL: 7 days.
     */
    const CACHE_TTL = 604800;

    /**
     * Initialize hooks.
     */
    public static function init() {
        if ( ! get_option( 'olo_critical_css_enabled', false ) ) {
            return;
        }

        add_action( 'wp_head', [ __CLASS__, 'output_critical_css' ], 2 );
        add_filter( 'style_loader_tag', [ __CLASS__, 'defer_main_stylesheet' ], 10, 4 );

        // Invalidate cache when a template is saved
        add_action( 'olo_template_saved', [ __CLASS__, 'invalidate_cache' ], 10, 1 );
    }

    /**
     * Generate critical CSS for a given post ID.
     *
     * @param int $post_id The post ID.
     * @return string The minified critical CSS.
     */
    public static function generate_critical_css( $post_id ) {
        $post_id = absint( $post_id );
        if ( ! $post_id ) {
            return '';
        }

        // Get the template ID for this post
        $template_id = (int) get_post_meta( $post_id, '_olo_template_id', true );
        if ( ! $template_id ) {
            // Check if it's a single CPT with active template
            $post_type = get_post_type( $post_id );
            if ( $post_type ) {
                $template_id = (int) get_option( "olo_active_single_{$post_type}", 0 );
            }
        }

        if ( ! $template_id ) {
            return '';
        }

        // Load the template JSON
        $db       = new Olo_Database();
        $template = $db->get_template( $template_id );
        if ( ! $template || empty( $template['content'] ) ) {
            return '';
        }

        $content = $template['content'];
        if ( ! is_array( $content ) ) {
            return '';
        }

        $css_parts = [];

        // Base critical CSS reset
        $css_parts[] = self::get_base_critical_css();

        // Take only the first 2 section/row nodes (above the fold)
        $above_fold_nodes = array_slice( $content, 0, 2 );

        // Generate CSS for above-fold nodes
        foreach ( $above_fold_nodes as $node ) {
            $node_css = self::generate_node_css( $node );
            if ( ! empty( $node_css ) ) {
                $css_parts[] = $node_css;
            }
        }

        // Include @font-face for fonts used in above-fold
        $font_css = self::get_font_face_css( $above_fold_nodes );
        if ( ! empty( $font_css ) ) {
            array_unshift( $css_parts, $font_css );
        }

        $critical_css = implode( "\n", $css_parts );

        // Minify
        $critical_css = self::minify_css( $critical_css );

        // Save in transient
        set_transient( 'olo_critical_css_' . $post_id, $critical_css, self::CACHE_TTL );

        return $critical_css;
    }

    /**
     * Output critical CSS inline in <head>.
     */
    public static function output_critical_css() {
        if ( is_admin() ) {
            return;
        }

        $post_id = get_the_ID();
        if ( ! $post_id ) {
            return;
        }

        // Try to get from transient
        $css = get_transient( 'olo_critical_css_' . $post_id );

        // If not cached, generate on the fly
        if ( false === $css ) {
            $css = self::generate_critical_css( $post_id );
        }

        if ( ! empty( $css ) ) {
            echo '<style id="olo-critical-css">' . $css . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS built by generate_critical_css() exclusively from values escaped at build time (esc_attr/esc_url/intval) plus static rules, then cached in a transient
        }
    }

    /**
     * Defer the main Olobuild stylesheet by converting it to preload.
     *
     * @param string $html The link tag HTML.
     * @param string $handle The stylesheet handle.
     * @param string $href The stylesheet URL.
     * @param string $media The media attribute.
     * @return string Modified HTML.
     */
    public static function defer_main_stylesheet( $html, $handle, $href, $media ) {
        // Only defer our main frontend CSS
        if ( $handle !== 'olo-frontend-css' ) {
            return $html;
        }

        // Only defer if we have critical CSS for this page
        $post_id = get_the_ID();
        if ( ! $post_id ) {
            return $html;
        }

        $has_critical = get_transient( 'olo_critical_css_' . $post_id );
        if ( false === $has_critical ) {
            return $html;
        }

        // Convert to preload with onload fallback
        // NOTA: NO && — uso if annidati
        $preload  = '<link rel="preload" href="' . esc_url( $href ) . '" as="style"';
        $preload .= ' onload="this.onload=null;this.rel=\'stylesheet\'">';
        $preload .= '<noscript>' . $html . '</noscript>';

        return $preload;
    }

    /**
     * Invalidate critical CSS cache for a post.
     *
     * @param int $post_id The post ID (or template ID).
     */
    public static function invalidate_cache( $post_id ) {
        $post_id = absint( $post_id );
        delete_transient( 'olo_critical_css_' . $post_id );

        // Also invalidate any posts that use this template
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- lookup mirato per meta_key/meta_value su postmeta per invalidare i transient legati al template; valore passato via $wpdb->prepare; risultato puntuale non cacheabile (cache gestita via transient).
        $posts = $wpdb->get_col( $wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_olo_template_id' AND meta_value = %s",
            (string) $post_id
        ) );

        if ( is_array( $posts ) ) {
            foreach ( $posts as $pid ) {
                delete_transient( 'olo_critical_css_' . intval( $pid ) );
            }
        }
    }

    /**
     * Regenerate critical CSS for all pages with Olobuild templates.
     *
     * @return array Status info.
     */
    public static function regenerate_all() {
        global $wpdb;

        // Find all posts with Olobuild template assignments
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- scansione una-tantum di postmeta per il batch di rigenerazione; nessun valore utente in SQL (solo literal); operazione di manutenzione non cacheabile.
        $post_ids = $wpdb->get_col(
            "SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_olo_template_id' AND meta_value != '0' AND meta_value != ''"
        );

        $generated = 0;
        $failed    = 0;

        if ( is_array( $post_ids ) ) {
            foreach ( $post_ids as $post_id ) {
                $css = self::generate_critical_css( intval( $post_id ) );
                if ( ! empty( $css ) ) {
                    $generated++;
                } else {
                    $failed++;
                }
            }
        }

        return [
            'generated' => $generated,
            'failed'    => $failed,
            'total'     => count( $post_ids ),
        ];
    }

    /**
     * Purge all critical CSS transients.
     *
     * @return int Number of purged entries.
     */
    public static function purge_all() {
        global $wpdb;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- purge in blocco dei transient del Critical CSS via pattern LIKE su options (nessuna API WP per cancellazione bulk per prefisso); valori LIKE passati via $wpdb->prepare; operazione di scrittura non cacheabile.
        $count = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
                '_transient_olo_critical_css_%',
                '_transient_timeout_olo_critical_css_%'
            )
        );

        return intval( $count / 2 ); // Each transient has 2 rows (value + timeout)
    }

    /**
     * Get status info about critical CSS cache.
     *
     * @return array Status info.
     */
    public static function get_status() {
        global $wpdb;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- conteggio dei transient Critical CSS via pattern LIKE su options (nessuna API WP per enumerazione per prefisso); valori LIKE passati via $wpdb->prepare; statistica live non cacheabile.
        $cached_count = (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE %s AND option_name NOT LIKE %s",
                '_transient_olo_critical_css_%',
                '_transient_timeout_%'
            )
        );

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- lettura del timeout transient più recente via pattern LIKE su options (nessuna API WP equivalente); valore LIKE passato via $wpdb->prepare; statistica live non cacheabile.
        $last_generated = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT option_value FROM {$wpdb->options} WHERE option_name LIKE %s ORDER BY option_value DESC LIMIT 1",
                '_transient_timeout_olo_critical_css_%'
            )
        );

        return [
            'enabled'        => (bool) get_option( 'olo_critical_css_enabled', false ),
            'cached_count'   => $cached_count,
            'last_generated' => $last_generated ? wp_date( 'Y-m-d H:i:s', intval( $last_generated ) - self::CACHE_TTL ) : null,
        ];
    }

    /**
     * Base critical CSS (layout reset, section, row essentials).
     */
    private static function get_base_critical_css() {
        return '
            *,*::before,*::after{box-sizing:border-box}
            body{margin:0;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif}
            .uk-section{padding:40px 0;position:relative}
            .uk-container{max-width:1200px;margin:0 auto;padding:0 15px}
            .uk-grid{display:flex;flex-wrap:wrap;margin:0;padding:0;list-style:none}
            .uk-grid>*{padding-left:0}
            .uk-width-1-1{width:100%}
            .uk-width-1-2{width:50%}
            .uk-width-1-3{width:33.333%}
            .uk-width-2-3{width:66.666%}
            .uk-width-1-4{width:25%}
            .uk-width-3-4{width:75%}
            .uk-position-relative{position:relative}
            .olo-template{overflow-x:hidden}
            img{max-width:100%;height:auto}
        ';
    }

    /**
     * Generate CSS for a single node (section/row) and its children.
     */
    private static function generate_node_css( $node ) {
        if ( ! is_array( $node ) ) {
            return '';
        }

        $css    = '';
        $style  = $node['style'] ?? [];
        $id     = $node['id'] ?? '';
        $type   = $node['type'] ?? '';

        // Background
        if ( ! empty( $style['bg'] ) ) {
            $bg = $style['bg'];
            if ( $id ) {
                $selector = '#olo-' . esc_attr( $id );

                if ( ! empty( $bg['color'] ) ) {
                    if ( $bg['type'] === 'solid' ) {
                        $css .= $selector . '{background-color:' . esc_attr( $bg['color'] ) . '}';
                    }
                }

                if ( $bg['type'] === 'gradient' ) {
                    $angle = intval( $bg['gradient_angle'] ?? 180 );
                    $from  = esc_attr( $bg['gradient_from'] ?? '#fff' );
                    $to    = esc_attr( $bg['gradient_to'] ?? '#000' );
                    $css  .= $selector . '{background:linear-gradient(' . $angle . 'deg,' . $from . ',' . $to . ')}';
                }

                if ( $bg['type'] === 'image' ) {
                    if ( ! empty( $bg['image_url'] ) ) {
                        $css .= $selector . '{background-image:url(' . esc_url( $bg['image_url'] ) . ');background-size:cover;background-position:center}';
                    }
                }
            }
        }

        // Custom padding
        if ( ! empty( $style['padding_top'] ) ) {
            if ( $id ) {
                $css .= '#olo-' . esc_attr( $id ) . '{padding-top:' . intval( $style['padding_top'] ) . 'px}';
            }
        }

        // Recurse into children
        if ( ! empty( $node['children'] ) ) {
            if ( is_array( $node['children'] ) ) {
                foreach ( $node['children'] as $child ) {
                    $child_css = self::generate_node_css( $child );
                    if ( ! empty( $child_css ) ) {
                        $css .= $child_css;
                    }
                }
            }
        }

        return $css;
    }

    /**
     * Extract font families from above-fold nodes and generate @font-face rules.
     */
    private static function get_font_face_css( $nodes ) {
        $fonts = [];
        self::collect_fonts( $nodes, $fonts );

        if ( empty( $fonts ) ) {
            return '';
        }

        $css = '';
        $custom_fonts = [];

        // Check if custom fonts class exists
        if ( class_exists( 'Olo_Custom_Fonts' ) ) {
            $custom_fonts = Olo_Custom_Fonts::get_fonts();
        }

        foreach ( $fonts as $font_family ) {
            foreach ( $custom_fonts as $cf ) {
                if ( strcasecmp( $cf['name'], $font_family ) === 0 ) {
                    if ( ! empty( $cf['variants'] ) ) {
                        foreach ( $cf['variants'] as $variant ) {
                            $weight = $variant['weight'] ?? '400';
                            $style  = $variant['style'] ?? 'normal';
                            $file   = $variant['file'] ?? '';
                            if ( $file ) {
                                $css .= '@font-face{';
                                $css .= 'font-family:"' . esc_attr( $cf['name'] ) . '";';
                                $css .= 'font-weight:' . esc_attr( $weight ) . ';';
                                $css .= 'font-style:' . esc_attr( $style ) . ';';
                                $css .= 'src:url("' . esc_url( $file ) . '");';
                                $css .= 'font-display:swap';
                                $css .= '}';
                            }
                        }
                    }
                }
            }
        }

        return $css;
    }

    /**
     * Recursively collect font families from node settings.
     */
    private static function collect_fonts( $nodes, &$fonts ) {
        if ( ! is_array( $nodes ) ) {
            return;
        }

        foreach ( $nodes as $node ) {
            if ( ! is_array( $node ) ) {
                continue;
            }

            $settings = $node['settings'] ?? [];
            $font_keys = [ 'font_family', 'title_font_family', 'heading_font_family', 'tl_title_family', 'tl_text_family', 'tl_yr_family' ];

            foreach ( $font_keys as $key ) {
                if ( ! empty( $settings[ $key ] ) ) {
                    $ff = $settings[ $key ];
                    if ( ! in_array( $ff, $fonts, true ) ) {
                        $fonts[] = $ff;
                    }
                }
            }

            if ( ! empty( $node['children'] ) ) {
                self::collect_fonts( $node['children'], $fonts );
            }
        }
    }

    /**
     * Minify CSS: remove comments, extra whitespace.
     */
    private static function minify_css( $css ) {
        // Remove comments
        $css = preg_replace( '/\/\*.*?\*\//s', '', $css );
        // Remove extra whitespace
        $css = preg_replace( '/\s+/', ' ', $css );
        // Remove spaces around : ; { }
        $css = preg_replace( '/\s*([:{;}])\s*/', '$1', $css );
        // Remove trailing semicolons before }
        $css = str_replace( ';}', '}', $css );

        return trim( $css );
    }
}
