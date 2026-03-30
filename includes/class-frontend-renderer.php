<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Frontend_Renderer {

    private $fraction_map = [
        '1-1' => 100, '1-2' => 50, '1-3' => 33.33, '2-3' => 66.66,
        '1-4' => 25, '3-4' => 75, '1-5' => 20, '2-5' => 40,
        '3-5' => 60, '4-5' => 80, '1-6' => 16.66, '5-6' => 83.33,
    ];

    private $shadow_map = [
        'sm' => '0 1px 2px 0 rgba(0,0,0,0.05)',
        'md' => '0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1)',
        'lg' => '0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1)',
        'xl' => '0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1)',
    ];

    /** drop-shadow filter equivalents — per elementi con mask/clip-path */
    private $drop_shadow_map = [
        'sm' => 'drop-shadow(0 1px 2px rgba(0,0,0,0.05))',
        'md' => 'drop-shadow(0 4px 6px rgba(0,0,0,0.1)) drop-shadow(0 2px 4px rgba(0,0,0,0.1))',
        'lg' => 'drop-shadow(0 10px 15px rgba(0,0,0,0.1)) drop-shadow(0 4px 6px rgba(0,0,0,0.1))',
        'xl' => 'drop-shadow(0 20px 25px rgba(0,0,0,0.1)) drop-shadow(0 8px 10px rgba(0,0,0,0.1))',
    ];

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

    /**
     * Static tile registry — stores all tile nodes from templates being rendered
     * so that tiles can look up other tiles' settings (e.g., navmenu referencing a search tile).
     */
    private static $tile_registry = [];

    /**
     * Tile IDs that are referenced by other tiles (e.g., search tiles used by navmenu/megamenu).
     * These tiles should not render in their original position — they are rendered inline by the referencing tile.
     */
    private static $referenced_tile_ids = [];

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
        add_shortcode( 'olo_template', [ $this, 'render_shortcode' ] );
        add_shortcode( 'mosaic_template', [ $this, 'render_shortcode' ] ); // backward compat
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_styles' ] );
        add_action( 'template_redirect', [ $this, 'add_security_headers' ] );
    }

    /**
     * Add security headers on pages that use an Olobuild template.
     * No strict CSP (script-src) to avoid breaking inline scripts and UIkit.
     */
    public function add_security_headers() {
        if ( headers_sent() || is_admin() ) {
            return;
        }

        // Check if current page uses an Olobuild template
        global $post;
        $has_olo = is_a( $post, 'WP_Post' ) && (
            has_shortcode( $post->post_content, 'olo_template' ) ||
            get_post_meta( $post->ID, '_olo_template_id', true )
        );

        // Also check single CPT pages with active single templates
        if ( ! $has_olo && is_singular() && ! is_page() ) {
            $pt     = get_post_type();
            $tpl_id = (int) get_option( "olo_active_single_{$pt}", 0 );
            if ( $tpl_id ) {
                $has_olo = true;
            }
        }

        // Also check header/footer templates
        if ( ! $has_olo ) {
            $header_id = (int) get_option( 'olo_active_header', 0 );
            $footer_id = (int) get_option( 'olo_active_footer', 0 );
            if ( $header_id || $footer_id ) {
                $has_olo = true;
            }
        }

        // Also check archive/404/search templates
        if ( ! $has_olo ) {
            if ( is_archive() || is_home() ) {
                $pt = get_query_var( 'post_type' );
                if ( is_array( $pt ) ) {
                    $pt = reset( $pt );
                }
                if ( ! $pt ) {
                    $pt = 'post';
                }
                $tpl_id = (int) get_option( "olo_active_archive_{$pt}", 0 );
                if ( ! $tpl_id ) {
                    $tpl_id = (int) get_option( 'olo_active_archive', 0 );
                }
                if ( $tpl_id ) {
                    $has_olo = true;
                }
            }
            if ( is_404() ) {
                $tpl_id = (int) get_option( 'olo_active_404', 0 );
                if ( $tpl_id ) {
                    $has_olo = true;
                }
            }
            if ( is_search() ) {
                $tpl_id = (int) get_option( 'olo_active_search', 0 );
                if ( $tpl_id ) {
                    $has_olo = true;
                }
            }
        }

        if ( ! $has_olo ) {
            return;
        }

        header( 'X-Content-Type-Options: nosniff' );
        header( 'X-Frame-Options: SAMEORIGIN' );
        header( 'Referrer-Policy: strict-origin-when-cross-origin' );
    }

    public function enqueue_frontend_styles() {
        global $post;
        $has_olo = is_a( $post, 'WP_Post' ) && (
            has_shortcode( $post->post_content, 'olo_template' ) ||
            get_post_meta( $post->ID, '_olo_template_id', true )
        );

        // Also enqueue for single CPT pages with active single templates
        if ( ! $has_olo && is_singular() && ! is_page() ) {
            $pt     = get_post_type();
            $tpl_id = (int) get_option( "olo_active_single_{$pt}", 0 );
            if ( $tpl_id ) {
                $has_olo = true;
            }
        }

        if ( $has_olo ) {
            // UIkit 3 (CSS + JS + Icons) - only on frontend, NOT in builder admin
            wp_enqueue_style(
                'uikit-css',
                OLO_URL . 'assets/vendor/uikit/css/uikit.min.css',
                [],
                '3.21.16'
            );
            wp_enqueue_script(
                'uikit-js',
                OLO_URL . 'assets/vendor/uikit/js/uikit.min.js',
                [],
                '3.21.16',
                array( 'in_footer' => true, 'strategy' => 'defer' )
            );
            wp_enqueue_script(
                'uikit-icons-js',
                OLO_URL . 'assets/vendor/uikit/js/uikit-icons.min.js',
                array( 'uikit-js' ),
                '3.21.16',
                array( 'in_footer' => true, 'strategy' => 'defer' )
            );

            // Olobuilder custom overrides (loaded after UIkit)
            wp_enqueue_style(
                'olo-frontend-css',
                OLO_URL . 'assets/css/frontend.css',
                [ 'uikit-css' ],
                OLO_VERSION
            );

            // Print styles (caricato solo per media print)
            wp_enqueue_style(
                'olo-print',
                OLO_URL . 'assets/css/olo-print.css',
                array( 'olo-frontend-css' ),
                OLO_VERSION,
                'print'
            );

            // Style System CSS (custom properties + UIkit overrides)
            $style_css = Olo_Style_System::instance()->generate_css();
            if ( ! empty( $style_css ) ) {
                wp_add_inline_style( 'olo-frontend-css', $style_css );
            }
        }
    }

    /**
     * Get effective background config for a tile.
     */
    private function get_effective_bg( $style ) {
        if ( ! empty( $style['bg'] ) && ! empty( $style['bg']['type'] ) && $style['bg']['type'] !== 'none' ) {
            return $style['bg'];
        }
        if ( ! empty( $style['bg_color'] ) ) {
            return [ 'type' => 'solid', 'color' => $style['bg_color'] ];
        }
        return [ 'type' => 'none' ];
    }

    /**
     * Generate inline background CSS from a bg config.
     */
    private function get_bg_inline_css( $bg ) {
        if ( $bg['type'] === 'solid' && ! empty( $bg['color'] ) ) {
            $color   = $bg['color'];
            $opacity = isset( $bg['color_opacity'] ) ? intval( $bg['color_opacity'] ) : 100;
            if ( $opacity < 100 && preg_match( '/^#([0-9a-fA-F]{6})$/', $color, $m ) ) {
                $r = hexdec( substr( $m[1], 0, 2 ) );
                $g = hexdec( substr( $m[1], 2, 2 ) );
                $b = hexdec( substr( $m[1], 4, 2 ) );
                $a = round( $opacity / 100, 2 );
                return "background-color: rgba({$r}, {$g}, {$b}, {$a})";
            }
            return 'background-color: ' . esc_attr( $color );
        }
        if ( $bg['type'] === 'gradient' ) {
            return $this->build_gradient_css( $bg );
        }
        return '';
    }

    /**
     * Build uk-parallax attribute string from a multi-stop parallax object.
     * Supports property keys: x, y, scale, rotate, opacity, blur, bgx, bgy.
     * Each property has an array of {value, position} stops.
     */
    private function build_parallax_attr_from_object( $parallax ) {
        $prop_keys = [ 'x', 'y', 'scale', 'rotate', 'opacity', 'blur', 'bgx', 'bgy' ];
        $parts = [];

        foreach ( $prop_keys as $key ) {
            if ( empty( $parallax[ $key ] ) || ! is_array( $parallax[ $key ] ) ) {
                continue;
            }

            $stops = $parallax[ $key ];
            // Sort by position
            usort( $stops, function( $a, $b ) {
                return ( $a['position'] ?? 0 ) - ( $b['position'] ?? 0 );
            });

            $count = count( $stops );
            if ( $count === 0 ) {
                continue;
            }

            // Build value segments — append % when position differs from default (first=0, last=100)
            $segments = [];
            foreach ( $stops as $i => $s ) {
                $v   = $this->format_parallax_value( $s['value'] );
                $pos = intval( $s['position'] ?? ( $i === $count - 1 ? 100 : 0 ) );

                $need_pct = false;
                if ( $i === 0 && $pos !== 0 ) {
                    $need_pct = true;
                } elseif ( $i === $count - 1 && $pos !== 100 ) {
                    $need_pct = true;
                } elseif ( $i > 0 && $i < $count - 1 ) {
                    $need_pct = true;
                }

                $segments[] = $need_pct ? $v . ' ' . $pos . '%' : $v;
            }
            $parts[] = $key . ': ' . implode( ',', $segments );
        }

        if ( empty( $parts ) ) {
            return '';
        }

        // Advanced options
        if ( ! empty( $parallax['start'] ) ) {
            $parts[] = 'start: ' . esc_attr( $parallax['start'] );
        }
        if ( ! empty( $parallax['end'] ) ) {
            $parts[] = 'end: ' . esc_attr( $parallax['end'] );
        }
        if ( isset( $parallax['easing'] ) && $parallax['easing'] !== null && $parallax['easing'] !== '' ) {
            $parts[] = 'easing: ' . floatval( $parallax['easing'] );
        }

        $nomobile = $parallax['nomobile'] ?? true;
        if ( $nomobile ) {
            $parts[] = 'media: @m';
        }

        return ' uk-parallax="' . implode( '; ', $parts ) . '"';
    }

    /**
     * Format a parallax value: integers as int, floats with minimal decimals.
     */
    private function format_parallax_value( $val ) {
        $f = floatval( $val );
        if ( $f == intval( $f ) ) {
            return strval( intval( $f ) );
        }
        return rtrim( rtrim( number_format( $f, 4, '.', '' ), '0' ), '.' );
    }

    /**
     * Build uk-parallax attribute value from a bg config.
     * Returns empty string if parallax is not enabled.
     */
    private function build_uk_parallax_attr( $bg ) {
        if ( empty( $bg['parallax'] ) ) {
            return '';
        }

        // New multi-stop format: parallax is an object/array
        if ( is_array( $bg['parallax'] ) ) {
            return $this->build_parallax_attr_from_object( $bg['parallax'] );
        }

        // Legacy boolean format
        $parts = [];

        $bgy = isset( $bg['parallax_bgy'] ) ? intval( $bg['parallax_bgy'] ) : -200;
        if ( $bgy !== 0 ) {
            $parts[] = 'bgy: ' . $bgy;
        }

        $bgx = isset( $bg['parallax_bgx'] ) ? intval( $bg['parallax_bgx'] ) : 0;
        if ( $bgx !== 0 ) {
            $parts[] = 'bgx: ' . $bgx;
        }

        if ( ! empty( $bg['parallax_opacity'] ) ) {
            $start = floatval( $bg['parallax_opacity_start'] ?? 0.3 );
            $end   = floatval( $bg['parallax_opacity_end'] ?? 1 );
            $parts[] = 'opacity: ' . $start . ',' . $end;
        }

        if ( ! empty( $bg['parallax_scale'] ) ) {
            $start = floatval( $bg['parallax_scale_start'] ?? 1 );
            $end   = floatval( $bg['parallax_scale_end'] ?? 1.2 );
            $parts[] = 'scale: ' . $start . ',' . $end;
        }

        if ( ! empty( $bg['parallax_blur'] ) ) {
            $start = intval( $bg['parallax_blur_start'] ?? 5 );
            $end   = intval( $bg['parallax_blur_end'] ?? 0 );
            $parts[] = 'blur: ' . $start . ',' . $end;
        }

        $nomobile = $bg['parallax_nomobile'] ?? true;
        if ( $nomobile ) {
            $parts[] = 'media: @m';
        }

        if ( empty( $parts ) ) {
            return '';
        }

        return ' uk-parallax="' . implode( '; ', $parts ) . '"';
    }

    /**
     * Build uk-scrollspy attribute from advanced config.
     * Supports stagger mode: animates direct children with incremental delay.
     */
    private function build_scrollspy_attr( $advanced ) {
        $animation = $advanced['scrollspy_animation'] ?? '';
        if ( empty( $animation ) ) {
            return '';
        }

        $stagger = intval( $advanced['scrollspy_stagger'] ?? 0 );

        $parts = [ 'cls: uk-animation-' . esc_attr( $animation ) ];

        $delay = intval( $advanced['scrollspy_delay'] ?? 0 );
        if ( $delay > 0 ) {
            $parts[] = 'delay: ' . $delay;
        }

        $repeat = ! empty( $advanced['scrollspy_repeat'] );
        if ( $repeat ) {
            $parts[] = 'repeat: true';
        }

        // Stagger mode: target direct children for sequential animation
        if ( $stagger > 0 ) {
            $parts[] = 'target: > *';
            $parts[] = 'delay: ' . $stagger;
        }

        return ' uk-scrollspy="' . implode( '; ', $parts ) . '"';
    }

    /**
     * Build uk-parallax attribute for element-level parallax (translate, opacity, scale, etc.).
     */
    private function build_element_parallax_attr( $advanced ) {
        // New multi-stop format: parallax is an object/array
        if ( isset( $advanced['parallax'] ) && is_array( $advanced['parallax'] ) ) {
            return $this->build_parallax_attr_from_object( $advanced['parallax'] );
        }

        // Legacy flat keys format
        $parts = [];

        $y_start = intval( $advanced['parallax_y_start'] ?? 0 );
        $y_end   = intval( $advanced['parallax_y_end'] ?? 0 );
        if ( $y_start !== 0 || $y_end !== 0 ) {
            $parts[] = 'y: ' . $y_start . ',' . $y_end;
        }

        $op_start = $advanced['parallax_opacity_start'] ?? '';
        $op_end   = $advanced['parallax_opacity_end'] ?? '';
        if ( $op_start !== '' && $op_end !== '' ) {
            $parts[] = 'opacity: ' . floatval( $op_start ) . ',' . floatval( $op_end );
        }

        $sc_start = $advanced['parallax_scale_start'] ?? '';
        $sc_end   = $advanced['parallax_scale_end'] ?? '';
        if ( $sc_start !== '' && $sc_end !== '' ) {
            $parts[] = 'scale: ' . floatval( $sc_start ) . ',' . floatval( $sc_end );
        }

        if ( empty( $parts ) ) {
            return '';
        }

        $nomobile = $advanced['parallax_nomobile'] ?? true;
        if ( $nomobile !== false ) {
            $parts[] = 'media: @m';
        }

        return ' uk-parallax="' . implode( '; ', $parts ) . '"';
    }

    /**
     * Build data attributes for mouse tracking effects (3D tilt + cursor follow).
     */
    private function build_mouse_attrs( $advanced ) {
        $attrs = '';
        if ( ! empty( $advanced['mouse_tilt'] ) ) {
            $intensity = intval( $advanced['mouse_tilt_intensity'] ?? 15 );
            $attrs .= ' data-olo-tilt="' . $intensity . '"';
        }
        if ( ! empty( $advanced['mouse_track'] ) ) {
            $speed = intval( $advanced['mouse_track_speed'] ?? 3 );
            $attrs .= ' data-olo-track="' . $speed . '"';
        }
        return $attrs;
    }

    /**
     * Build inline CSS for infinite (looping) animation (legacy element-level).
     */
    private function build_inline_animation_css( $advanced ) {
        $anim = $advanced['infinite_animation'] ?? 'none';
        if ( $anim === 'none' || $anim === '' ) {
            return '';
        }
        $speed = floatval( $advanced['infinite_speed'] ?? 3 );
        $dir   = $advanced['infinite_direction'] ?? 'normal';
        $name  = 'olo-anim-' . sanitize_html_class( $anim );
        return "animation: {$name} {$speed}s {$dir} infinite";
    }

    /**
     * Build clip-path CSS for mask/shape effects (legacy element-level).
     */
    private function build_inline_mask_css( $advanced ) {
        $mask = $advanced['mask_type'] ?? 'none';
        if ( $mask === 'none' || $mask === '' ) {
            return '';
        }
        $shapes = [
            'circle'   => 'circle(50% at 50% 50%)',
            'ellipse'  => 'ellipse(50% 40% at 50% 50%)',
            'triangle' => 'polygon(50% 0%, 100% 100%, 0% 100%)',
            'hexagon'  => 'polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%)',
            'star'     => 'polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%)',
            'diamond'  => 'polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%)',
            'blob'     => 'polygon(30% 0%, 70% 0%, 100% 30%, 100% 70%, 70% 100%, 30% 100%, 0% 70%, 0% 30%)',
        ];
        if ( $mask === 'custom' ) {
            $custom = $advanced['mask_custom'] ?? '';
            if ( $custom === '' ) return '';
            return 'clip-path: ' . $custom;
        }
        if ( ! isset( $shapes[ $mask ] ) ) return '';
        return 'clip-path: ' . $shapes[ $mask ];
    }

    /**
     * Build border-radius CSS value from number or {tl,tr,br,bl} array.
     */
    private function build_border_radius_css( $val ) {
        if ( is_array( $val ) ) {
            return sprintf( 'border-radius: %dpx %dpx %dpx %dpx',
                intval( $val['tl'] ?? 0 ), intval( $val['tr'] ?? 0 ),
                intval( $val['br'] ?? 0 ), intval( $val['bl'] ?? 0 ) );
        }
        return "border-radius: {$val}px";
    }

    /**
     * Build flex container CSS declarations from settings array.
     */
    private function build_flex_container_css( $settings ) {
        $decls = [];
        $fd = $settings['flex_direction'] ?? '';
        $fj = $settings['flex_justify'] ?? '';
        $fa = $settings['flex_align'] ?? '';
        $fw = $settings['flex_wrap'] ?? '';
        $fcg = $settings['flex_column_gap'] ?? '';
        $frg = $settings['flex_row_gap'] ?? '';
        $fg  = $settings['flex_gap'] ?? ''; // legacy
        $has_flex_gap = ( $fcg && intval( $fcg ) > 0 ) || ( $frg && intval( $frg ) > 0 ) || ( $fg && intval( $fg ) > 0 );
        // Emit display:flex only when at least one property differs from CSS flex defaults
        $has_flex = ( $fd && $fd !== 'row' ) || ( $fj && $fj !== 'flex-start' ) || ( $fa && $fa !== 'stretch' ) || ( $fw && $fw !== 'nowrap' ) || $has_flex_gap;
        if ( $has_flex ) {
            $decls[] = 'display: flex';
            if ( $fd && $fd !== 'row' )         $decls[] = 'flex-direction: ' . esc_attr( $fd );
            if ( $fj && $fj !== 'flex-start' )  $decls[] = 'justify-content: ' . esc_attr( $fj );
            if ( $fa && $fa !== 'stretch' )     $decls[] = 'align-items: ' . esc_attr( $fa );
            if ( $fw && $fw !== 'nowrap' )      $decls[] = 'flex-wrap: ' . esc_attr( $fw );
            // Separate column/row gap, fallback to legacy flex_gap
            if ( ( $fcg && intval( $fcg ) > 0 ) || ( $frg && intval( $frg ) > 0 ) ) {
                if ( $fcg && intval( $fcg ) > 0 ) $decls[] = 'column-gap: ' . intval( $fcg ) . 'px';
                if ( $frg && intval( $frg ) > 0 ) $decls[] = 'row-gap: ' . intval( $frg ) . 'px';
            } elseif ( $fg && intval( $fg ) > 0 ) {
                $decls[] = 'gap: ' . intval( $fg ) . 'px';
            }
        }
        return $decls;
    }

    /**
     * Build CSS Grid layout declarations from settings.
     */
    private function build_css_grid_css( $settings ) {
        $decls = [];
        $mode = $settings['layout_mode'] ?? 'flex';
        if ( $mode !== 'grid' ) {
            return $decls;
        }
        $decls[] = 'display: grid';
        $cols = $settings['grid_columns'] ?? '';
        if ( $cols ) {
            $decls[] = 'grid-template-columns: ' . esc_attr( $cols );
        }
        $rows = $settings['grid_rows'] ?? '';
        if ( $rows && $rows !== 'auto' ) {
            $decls[] = 'grid-template-rows: ' . esc_attr( $rows );
        }
        $gap = intval( $settings['grid_gap'] ?? 0 );
        $col_gap = intval( $settings['grid_column_gap'] ?? 0 );
        $row_gap = intval( $settings['grid_row_gap'] ?? 0 );
        if ( $col_gap > 0 || $row_gap > 0 ) {
            $decls[] = 'column-gap: ' . ( $col_gap > 0 ? $col_gap : $gap ) . 'px';
            $decls[] = 'row-gap: ' . ( $row_gap > 0 ? $row_gap : $gap ) . 'px';
        } elseif ( $gap > 0 ) {
            $decls[] = 'gap: ' . $gap . 'px';
        }
        $ai = $settings['grid_align_items'] ?? 'stretch';
        if ( $ai && $ai !== 'stretch' ) {
            $decls[] = 'align-items: ' . esc_attr( $ai );
        }
        $ji = $settings['grid_justify_items'] ?? 'stretch';
        if ( $ji && $ji !== 'stretch' ) {
            $decls[] = 'justify-items: ' . esc_attr( $ji );
        }
        return $decls;
    }

    /**
     * Build CSS transform declarations from style array.
     * Returns array of CSS declarations (transform, transform-origin).
     */
    private function build_transform_css( $style ) {
        $parts = [];

        // Support nested transform object from FieldTransform
        $tf = isset( $style['transform'] ) && is_array( $style['transform'] ) ? $style['transform'] : null;

        $rotate = $tf ? floatval( $tf['rotate'] ?? 0 ) : ( isset( $style['transform_rotate'] ) ? floatval( $style['transform_rotate'] ) : 0 );
        if ( $rotate != 0 ) {
            $parts[] = 'rotate(' . $rotate . 'deg)';
        }
        $rotateX = isset( $style['transform_rotateX'] ) ? floatval( $style['transform_rotateX'] ) : 0;
        if ( $rotateX != 0 ) {
            $parts[] = 'rotate3d(1,0,0,' . $rotateX . 'deg)';
        }
        $rotateY = isset( $style['transform_rotateY'] ) ? floatval( $style['transform_rotateY'] ) : 0;
        if ( $rotateY != 0 ) {
            $parts[] = 'rotate3d(0,1,0,' . $rotateY . 'deg)';
        }
        $scale = $tf ? floatval( $tf['scale'] ?? 1 ) : ( isset( $style['transform_scale'] ) ? floatval( $style['transform_scale'] ) : 1 );
        if ( $scale != 1 ) {
            $parts[] = 'scale(' . $scale . ')';
        }
        $tx = $tf ? floatval( $tf['translateX'] ?? 0 ) : ( isset( $style['transform_translateX'] ) ? floatval( $style['transform_translateX'] ) : 0 );
        if ( $tx != 0 ) {
            $parts[] = 'translateX(' . $tx . 'px)';
        }
        $ty = $tf ? floatval( $tf['translateY'] ?? 0 ) : ( isset( $style['transform_translateY'] ) ? floatval( $style['transform_translateY'] ) : 0 );
        if ( $ty != 0 ) {
            $parts[] = 'translateY(' . $ty . 'px)';
        }
        $skewX = $tf ? floatval( $tf['skewX'] ?? 0 ) : ( isset( $style['transform_skewX'] ) ? floatval( $style['transform_skewX'] ) : 0 );
        if ( $skewX != 0 ) {
            $parts[] = 'skewX(' . $skewX . 'deg)';
        }
        $skewY = $tf ? floatval( $tf['skewY'] ?? 0 ) : ( isset( $style['transform_skewY'] ) ? floatval( $style['transform_skewY'] ) : 0 );
        if ( $skewY != 0 ) {
            $parts[] = 'skewY(' . $skewY . 'deg)';
        }

        // Determine transform-origin
        $origin = $tf ? ( $tf['origin'] ?? '' ) : ( $style['transform_origin'] ?? '' );

        if ( empty( $parts ) ) {
            if ( $origin !== '' && $origin !== 'center' && $origin !== 'center center' ) {
                return [ 'transform-origin: ' . esc_attr( $origin ) ];
            }
            return [];
        }

        $decls = [];
        $decls[] = 'transform: ' . implode( ' ', $parts );

        if ( $origin !== '' && $origin !== 'center' && $origin !== 'center center' ) {
            $decls[] = 'transform-origin: ' . esc_attr( $origin );
        }

        return $decls;
    }

    /**
     * Build box-shadow CSS declaration from style array.
     * Supports: preset (sm/md/lg/xl), custom (shadow_h/v/blur/spread/color/inset), none.
     */
    private function build_box_shadow_css( $style ) {
        $shadow = $style['shadow'] ?? 'none';
        if ( ! $shadow || $shadow === 'none' ) {
            return '';
        }

        // Custom shadow — flat fields (shadow_h, shadow_v, shadow_blur, shadow_spread, shadow_color, shadow_inset)
        if ( $shadow === 'custom' ) {
            $h      = intval( $style['shadow_h'] ?? 0 );
            $v      = intval( $style['shadow_v'] ?? 0 );
            $blur   = intval( $style['shadow_blur'] ?? 0 );
            $spread = intval( $style['shadow_spread'] ?? 0 );
            $color  = esc_attr( $style['shadow_color'] ?? 'rgba(0,0,0,0.15)' );
            $inset  = ! empty( $style['shadow_inset'] ) ? 'inset ' : '';
            return "box-shadow: {$inset}{$h}px {$v}px {$blur}px {$spread}px {$color}";
        }

        // Preset (sm/md/lg/xl)
        if ( isset( $this->shadow_map[ $shadow ] ) ) {
            return 'box-shadow: ' . $this->shadow_map[ $shadow ];
        }

        // Legacy shadow_type support
        $shadow_type = $style['shadow_type'] ?? '';
        if ( $shadow_type === 'custom' ) {
            $h      = intval( $style['shadow_h'] ?? 0 );
            $v      = intval( $style['shadow_v'] ?? 0 );
            $blur   = intval( $style['shadow_blur'] ?? 0 );
            $spread = intval( $style['shadow_spread'] ?? 0 );
            $color  = esc_attr( $style['shadow_color'] ?? 'rgba(0,0,0,0.2)' );
            $inset  = ! empty( $style['shadow_inset'] ) ? 'inset ' : '';
            return "box-shadow: {$inset}{$h}px {$v}px {$blur}px {$spread}px {$color}";
        }

        return '';
    }

    /**
     * Build filter: drop-shadow() CSS declaration from style array.
     * Usato al posto di box-shadow quando l'elemento ha una mask/clip-path,
     * perché drop-shadow segue la forma visibile.
     * Nota: inset e spread vengono ignorati (non supportati da drop-shadow).
     */
    private function build_drop_shadow_css( $style ) {
        $shadow = $style['shadow'] ?? 'none';
        if ( ! $shadow || $shadow === 'none' ) {
            return '';
        }

        // Custom
        if ( $shadow === 'custom' ) {
            $h     = intval( $style['shadow_h'] ?? 0 );
            $v     = intval( $style['shadow_v'] ?? 0 );
            $blur  = intval( $style['shadow_blur'] ?? 0 );
            $color = esc_attr( $style['shadow_color'] ?? 'rgba(0,0,0,0.15)' );
            return "drop-shadow({$h}px {$v}px {$blur}px {$color})";
        }

        // Preset
        if ( isset( $this->drop_shadow_map[ $shadow ] ) ) {
            return $this->drop_shadow_map[ $shadow ];
        }

        // Legacy
        $shadow_type = $style['shadow_type'] ?? '';
        if ( $shadow_type === 'custom' ) {
            $h     = intval( $style['shadow_h'] ?? 0 );
            $v     = intval( $style['shadow_v'] ?? 0 );
            $blur  = intval( $style['shadow_blur'] ?? 0 );
            $color = esc_attr( $style['shadow_color'] ?? 'rgba(0,0,0,0.2)' );
            return "drop-shadow({$h}px {$v}px {$blur}px {$color})";
        }

        return '';
    }

    /**
     * Build text-shadow CSS declaration from style array.
     */
    private function build_text_shadow_css( $style ) {
        $h_val = $style['text_shadow_h'] ?? null;
        $v_val = $style['text_shadow_v'] ?? null;
        $blur_val = $style['text_shadow_blur'] ?? null;
        // If none of the text shadow values are set, return empty
        if ( $h_val === null && $v_val === null && $blur_val === null && empty( $style['text_shadow_enabled'] ) ) {
            // Also check if any value is non-zero
            if ( empty( $style['text_shadow_color'] ) ) {
                return '';
            }
        }
        // Support legacy text_shadow_enabled flag
        if ( isset( $style['text_shadow_enabled'] ) && empty( $style['text_shadow_enabled'] ) ) {
            return '';
        }
        // If at least one value is non-zero/non-default, generate CSS
        $h = intval( $h_val ?? 0 );
        $v = intval( $v_val ?? 0 );
        $b = intval( $blur_val ?? 0 );
        if ( $h === 0 && $v === 0 && $b === 0 ) {
            return '';
        }
        $color = esc_attr( $style['text_shadow_color'] ?? 'rgba(0,0,0,0.3)' );
        return "text-shadow: {$h}px {$v}px {$b}px {$color}";
    }

    /**
     * Build backdrop-filter CSS declarations from style array.
     * Returns array with standard + webkit prefix.
     */
    private function build_backdrop_filter_css( $style ) {
        $parts = [];
        $blur       = isset( $style['backdrop_blur'] ) ? intval( $style['backdrop_blur'] ) : 0;
        $brightness = isset( $style['backdrop_brightness'] ) ? intval( $style['backdrop_brightness'] ) : 100;
        $contrast   = isset( $style['backdrop_contrast'] ) ? intval( $style['backdrop_contrast'] ) : 100;
        $saturate   = isset( $style['backdrop_saturate'] ) ? intval( $style['backdrop_saturate'] ) : 100;

        if ( $blur != 0 )        $parts[] = 'blur(' . $blur . 'px)';
        if ( $brightness != 100 ) $parts[] = 'brightness(' . $brightness . '%)';
        if ( $contrast != 100 )   $parts[] = 'contrast(' . $contrast . '%)';
        if ( $saturate != 100 )   $parts[] = 'saturate(' . $saturate . '%)';

        if ( empty( $parts ) ) {
            return [];
        }

        $val = implode( ' ', $parts );
        return [
            '-webkit-backdrop-filter: ' . $val,
            'backdrop-filter: ' . $val,
        ];
    }

    /**
     * Build infinite animation CSS (keyframes + animation rule).
     * Returns CSS string to be injected into <style> block, or empty string.
     */
    private function build_infinite_animation_css( $settings, $css_id ) {
        $anim = $settings['infinite_animation'] ?? '';
        if ( $anim === '' || $anim === 'none' ) {
            return '';
        }

        $keyframes_map = [
            'float'   => '0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)}',
            'pulse'   => '0%,100%{transform:scale(1)} 50%{transform:scale(1.05)}',
            'spin'    => '0%{transform:rotate(0)} 100%{transform:rotate(360deg)}',
            'wiggle'  => '0%,100%{transform:rotate(0)} 25%{transform:rotate(-5deg)} 75%{transform:rotate(5deg)}',
            'bounce'  => '0%,100%{transform:translateY(0)} 50%{transform:translateY(-15px)}',
            'swing'   => '0%,100%{transform:rotate(0)} 25%{transform:rotate(10deg)} 75%{transform:rotate(-10deg)}',
            'breathe' => '0%,100%{opacity:1} 50%{opacity:0.6}',
        ];

        if ( ! isset( $keyframes_map[ $anim ] ) ) {
            return '';
        }

        $speed = isset( $settings['infinite_speed'] ) ? intval( $settings['infinite_speed'] ) : 5;
        $speed = max( 1, min( 10, $speed ) );
        $duration = 10 - $speed + 1; // speed 1 = 10s, speed 10 = 1s

        $direction = $settings['infinite_direction'] ?? 'normal';
        if ( ! in_array( $direction, [ 'normal', 'reverse', 'alternate', 'alternate-reverse' ], true ) ) {
            $direction = 'normal';
        }

        $safe_id = preg_replace( '/[^a-zA-Z0-9_-]/', '', $css_id );
        $kf_name = 'olo-inf-' . $safe_id;

        $css  = '@keyframes ' . $kf_name . '{' . $keyframes_map[ $anim ] . '} ';
        $css .= '#' . esc_attr( $css_id ) . '{animation:' . $kf_name . ' ' . $duration . 's ' . $direction . ' infinite}';

        return $css;
    }

    /**
     * Build mask CSS declarations from style array.
     * Returns array of CSS declarations with standard + webkit prefix.
     */
    private function build_mask_css( $style ) {
        $mask_type = $style['mask_type'] ?? ( $style['mask'] ?? '' );
        if ( $mask_type === '' || $mask_type === 'none' ) {
            return [];
        }

        $svg_map = [
            'circle'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="black"/></svg>',
            'ellipse'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 80"><ellipse cx="50" cy="40" rx="50" ry="40" fill="black"/></svg>',
            'triangle' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><polygon points="50,0 100,100 0,100" fill="black"/></svg>',
            'hexagon'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><polygon points="50,0 93.3,25 93.3,75 50,100 6.7,75 6.7,25" fill="black"/></svg>',
            'star'     => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><polygon points="50,0 61,35 98,35 68,57 79,91 50,70 21,91 32,57 2,35 39,35" fill="black"/></svg>',
            'diamond'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><polygon points="50,0 100,50 50,100 0,50" fill="black"/></svg>',
            'blob'     => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><path d="M44.7,-76.4C58.8,-69.2,71.8,-59.1,79.6,-45.8C87.4,-32.6,90,-16.3,88.5,-0.9C87,14.6,81.4,29.1,73.1,41.8C64.8,54.4,53.8,65.2,40.8,72.4C27.8,79.6,12.8,83.3,-1.6,86.1C-16,88.8,-32,90.6,-44.6,83.7C-57.2,76.8,-66.4,61.2,-74.2,45.7C-82,30.2,-88.4,14.8,-87.9,0.3C-87.4,-14.2,-80,-28.5,-71,-40.7C-62,-53,-51.4,-63.3,-39,-71.1C-26.6,-78.9,-12.3,-84.2,1.8,-87.4C15.8,-90.6,30.6,-83.6,44.7,-76.4Z" transform="translate(100 100)" fill="black"/></svg>',
            'wave'     => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path d="M0,30 Q25,0 50,30 T100,30 L100,100 L0,100 Z" fill="black"/></svg>',
        ];

        if ( ! isset( $svg_map[ $mask_type ] ) ) {
            return [];
        }

        $svg_encoded = 'data:image/svg+xml,' . rawurlencode( $svg_map[ $mask_type ] );
        $size     = esc_attr( $style['mask_size'] ?? 'contain' );
        $position = esc_attr( $style['mask_position'] ?? 'center' );
        $repeat   = esc_attr( $style['mask_repeat'] ?? 'no-repeat' );

        return [
            '-webkit-mask-image: url("' . $svg_encoded . '")',
            '-webkit-mask-size: ' . $size,
            '-webkit-mask-position: ' . $position,
            '-webkit-mask-repeat: ' . $repeat,
            'mask-image: url("' . $svg_encoded . '")',
            'mask-size: ' . $size,
            'mask-position: ' . $position,
            'mask-repeat: ' . $repeat,
        ];
    }

    /**
     * Build gradient CSS with multi-stop support.
     * Falls back to simple gradient_from/gradient_to if no gradient_stops.
     */
    private function build_gradient_css( $bg ) {
        $gradient_type = $bg['gradient_type'] ?? 'linear';

        // New format: bg.gradient = { type, angle, stops: [{color, position}, ...] }
        $gradient_obj = $bg['gradient'] ?? null;
        if ( is_array( $gradient_obj ) && ! empty( $gradient_obj['stops'] ) ) {
            $gradient_type = $gradient_obj['type'] ?? 'linear';
            $bg['gradient_stops'] = $gradient_obj['stops'];
            $bg['gradient_angle'] = $gradient_obj['angle'] ?? 180;
        }

        // Multi-stop gradient
        if ( ! empty( $bg['gradient_stops'] ) && is_array( $bg['gradient_stops'] ) ) {
            $stops = [];
            foreach ( $bg['gradient_stops'] as $stop ) {
                $color = esc_attr( $stop['color'] ?? '#000000' );
                $pos   = isset( $stop['position'] ) ? intval( $stop['position'] ) : null;
                $stops[] = $pos !== null ? "{$color} {$pos}%" : $color;
            }
            $stop_str = implode( ', ', $stops );

            if ( $gradient_type === 'radial' ) {
                return 'background: radial-gradient(circle, ' . $stop_str . ')';
            }
            $angle = intval( $bg['gradient_angle'] ?? 180 );
            return 'background: linear-gradient(' . $angle . 'deg, ' . $stop_str . ')';
        }

        // Fallback: simple two-color gradient
        $from  = esc_attr( $bg['gradient_from'] ?? '#ffffff' );
        $to    = esc_attr( $bg['gradient_to'] ?? '#000000' );

        if ( $gradient_type === 'radial' ) {
            return 'background: radial-gradient(circle, ' . $from . ', ' . $to . ')';
        }
        $angle = intval( $bg['gradient_angle'] ?? 180 );
        return "background: linear-gradient({$angle}deg, {$from}, {$to})";
    }

    private function generate_id() {
        if ( function_exists( 'wp_generate_uuid4' ) ) {
            return wp_generate_uuid4();
        }
        return 'tile-' . uniqid() . '-' . substr( md5( mt_rand() ), 0, 8 );
    }

    /**
     * Render gallery background slideshow.
     */
    private function render_bg_gallery( $bg ) {
        $images = $bg['gallery_images'] ?? [];
        if ( empty( $images ) || ! is_array( $images ) ) return '';

        $size       = esc_attr( $bg['image_size'] ?? 'cover' );
        $pos        = esc_attr( $bg['image_position'] ?? 'center center' );
        $loop       = ( $bg['gallery_loop'] ?? true ) !== false;
        $duration   = intval( $bg['gallery_duration'] ?? 5000 );
        $transition = esc_attr( $bg['gallery_transition'] ?? 'fade' );
        $trans_ms   = intval( $bg['gallery_transition_ms'] ?? 500 );
        $lazyload   = ( $bg['gallery_lazyload'] ?? true ) !== false;
        $kenburns   = ! empty( $bg['gallery_kenburns'] );
        $kb_dir     = esc_attr( $bg['gallery_kenburns_dir'] ?? 'in' );

        $config = wp_json_encode( [
            'duration'   => $duration,
            'transition' => $transition,
            'transMs'    => $trans_ms,
            'loop'       => $loop,
            'kenburns'   => $kenburns,
            'kbDir'      => $kb_dir,
        ] );

        $kb_class = $kenburns ? ' olo-bg-gallery--kb-' . $kb_dir : '';
        $kb_dur   = $kenburns ? ( $duration + $trans_ms ) : 0;

        $style_parts = [];
        if ( $kb_dur ) $style_parts[] = '--olo-kb-dur:' . $kb_dur . 'ms';
        $style_parts[] = '--olo-gallery-trans-ms:' . $trans_ms . 'ms';
        $style_attr = ' style="' . implode( ';', $style_parts ) . '"';

        $html = '<div class="olo-bg-gallery' . $kb_class . '" data-olo-bg-gallery=\'' . $config . '\''
              . ' data-transition="' . $transition . '"' . $style_attr . '>';

        foreach ( $images as $i => $img ) {
            $url   = esc_url( $img['url'] ?? '' );
            $alt   = esc_attr( $img['alt'] ?? '' );
            $active = $i === 0 ? ' olo-bg-gallery--active' : '';
            $even   = ( $i % 2 === 0 ) ? '' : ' olo-bg-gallery--even';
            $lazy   = ( $i > 0 ) ? ( $lazyload ? ' loading="lazy"' : '' ) : '';
            $html .= '<img class="olo-bg-gallery-slide' . $active . $even . '" src="' . $url . '" alt="' . $alt . '"'
                   . ' style="object-fit:' . $size . ';object-position:' . $pos . '"' . $lazy . '>';
        }

        $html .= '</div>';

        // Enqueue gallery slideshow script once
        if ( ! self::$gallery_script_enqueued ) {
            self::$gallery_script_enqueued = true;
            add_action( 'wp_footer', [ __CLASS__, 'print_gallery_script' ], 99 );
        }

        return $html;
    }

    private static $gallery_script_enqueued = false;

    /**
     * Print gallery background slideshow script in footer (once).
     */
    public static function print_gallery_script() {
        ?>
        <script>
        (function(){
          document.querySelectorAll('[data-olo-bg-gallery]').forEach(function(el){
            var cfg=JSON.parse(el.getAttribute('data-olo-bg-gallery'));
            var slides=el.querySelectorAll('.olo-bg-gallery-slide');
            if(slides.length<2)return;
            var idx=0,dur=cfg.duration||5000,transMs=cfg.transMs||500;
            var loop=cfg.loop!==false,trans=cfg.transition||'fade';
            var hasSlide=trans==='slide'||trans==='slide-up';
            function next(){
              var prev=idx;
              idx=(idx+1)%slides.length;
              if(!loop){if(idx===0)return}
              if(hasSlide){slides[prev].classList.add('olo-bg-gallery--leaving')}
              slides[prev].classList.remove('olo-bg-gallery--active');
              slides[idx].classList.add('olo-bg-gallery--active');
              if(hasSlide){setTimeout(function(){slides[prev].classList.remove('olo-bg-gallery--leaving')},transMs+50)}
            }
            setInterval(next,dur);
          });
        })();
        </script>
        <?php
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
                        'id'       => $this->generate_id(),
                        'type'     => 'column',
                        'settings' => [ 'width_default' => '', 'width_small' => '', 'width_medium' => $w, 'width_large' => '' ],
                        'style'    => [],
                        'advanced' => [],
                        'children' => $children,
                    ];
                }

                $row = [
                    'id'       => $tile['id'] ?? $this->generate_id(),
                    'type'     => 'row',
                    'settings' => $settings,
                    'style'    => $tile['style'] ?? [],
                    'advanced' => $tile['advanced'] ?? [],
                    'children' => $columns,
                ];

                $sections[] = [
                    'id'       => $this->generate_id(),
                    'type'     => 'section',
                    'settings' => [ 'style' => 'default', 'width' => 'default', 'padding' => 'default' ],
                    'style'    => [],
                    'advanced' => [],
                    'children' => [ $row ],
                ];
            } else {
                // Wrap element in Section > Row > Column(1/1)
                $column = [
                    'id'       => $this->generate_id(),
                    'type'     => 'column',
                    'settings' => [ 'width_default' => '', 'width_small' => '', 'width_medium' => '1-1', 'width_large' => '' ],
                    'style'    => [],
                    'advanced' => [],
                    'children' => [ $tile ],
                ];
                $row = [
                    'id'       => $this->generate_id(),
                    'type'     => 'row',
                    'settings' => [ 'layout' => '100', 'gap' => '16', 'column_gap' => 'default', 'vertical_align' => 'stretch', 'stack_mobile' => true ],
                    'style'    => [],
                    'advanced' => [],
                    'children' => [ $column ],
                ];
                $sections[] = [
                    'id'       => $this->generate_id(),
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
                return ( strpos( $_SERVER['REQUEST_URI'], $str ) !== false );
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
                $referrer = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';
                return ( strpos( $referrer, $cond_ref ) !== false );
            case 'browser':
                $cond_browser = strtolower( $settings['cond_browser'] ?? '' );
                if ( $cond_browser === '' ) return true;
                $ua = strtolower( isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '' );
                return ( strpos( $ua, $cond_browser ) !== false );
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
                return ( strpos( (string) get_field( $acf_key ), $acf_value ) !== false );
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
                return ( isset( $_GET[ $qs_key ] ) && $_GET[ $qs_key ] === $qs_value );

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
     * Render a node (recursive dispatcher).
     */
    private function render_node( $node, $manager, $template_id, &$hover_css_rules, &$tile_counter, $parent_is_grid = false ) {
        if ( ! $this->should_render_node( $node ) ) return '';

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

        return $html;
    }

    /**
     * Wrap non-interactive tiles in a lazy container for deferred rendering.
     * The IntersectionObserver script in the footer will move <template> content into the DOM when visible.
     * The first 3 element tiles are rendered immediately (above the fold).
     */
    private function maybe_lazy_wrap( $html, $type, $advanced = [] ) {
        // Builder mode: no lazy loading (iframe needs all tiles visible)
        if ( $this->builder_mode ) return $html;

        // Types that must NOT be lazy-loaded (interactive, form-based, or map)
        static $no_lazy = [
            'form', 'map', 'search', 'livesearch', 'servicesearch', 'booking',
            'bookingpicker', 'calendar', 'loginform', 'scrollprogress',
            'popup', 'megamenu', 'navmenu', 'togglebtn',
        ];
        if ( in_array( $type, $no_lazy, true ) ) {
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

        return '<div data-olo-lazy><template class="olo-lazy-content">' . $html . '</template><div class="olo-lazy-ph" style="min-height:50px"></div></div>';
    }

    /**
     * Render a Section container using UIkit classes.
     */
    private function render_section_node( $node, $manager, $template_id, &$hover_css_rules, &$tile_counter ) {
        // Floating panel bypass: if section contains only a floatingpanel (inside row>column),
        // render the floatingpanel directly without section/row/column wrappers to avoid empty gap.
        if ( $this->section_has_only_floatingpanel( $node ) ) {
            return $this->extract_and_render_floatingpanel( $node, $manager, $template_id, $hover_css_rules, $tile_counter );
        }

        $s = $node['settings'] ?? [];
        $style    = $node['style'] ?? [];
        $advanced = $node['advanced'] ?? [];

        // Sticky effect
        $sticky_effect = $s['sticky_effect'] ?? 'none';
        $is_sticky_v = in_array( $sticky_effect, [ 'cover', 'reveal' ], true );
        $is_sticky_h = in_array( $sticky_effect, [ 'cover-h', 'reveal-h' ], true );
        $is_sticky = $is_sticky_v || $is_sticky_h;
        $sticky_top = intval( $s['sticky_top'] ?? 0 );

        // Scroll snap
        $scroll_snap = ! empty( $s['scroll_snap'] );
        $snap_dots   = $scroll_snap && ! empty( $s['snap_dots'] );

        // Section classes — position-relative needed for absolute-positioned children
        // Sticky sections use position:sticky instead (handled via CSS class)
        $classes = [ 'uk-section' ];
        if ( $is_sticky_v ) {
            $classes[] = 'olo-sticky-' . $sticky_effect;
        } elseif ( $is_sticky_h ) {
            $classes[] = 'olo-sticky-' . $sticky_effect;
        } else {
            $classes[] = 'uk-position-relative';
        }
        $section_style = $s['style'] ?? 'default';
        $style_map = [
            'muted'     => 'uk-section-muted',
            'primary'   => 'uk-section-primary',
            'secondary' => 'uk-section-secondary',
        ];
        if ( isset( $style_map[ $section_style ] ) ) {
            $classes[] = $style_map[ $section_style ];
        } else {
            $classes[] = 'uk-section-default';
        }

        // Padding
        $padding = $s['padding'] ?? 'default';
        $padding_map = [
            'small'           => 'uk-section-small',
            'large'           => 'uk-section-large',
            'xlarge'          => 'uk-section-xlarge',
            'remove-vertical' => 'uk-padding-remove-vertical',
        ];
        if ( isset( $padding_map[ $padding ] ) ) {
            $classes[] = $padding_map[ $padding ];
        }

        // Custom CSS classes
        if ( ! empty( $advanced['css_classes'] ) ) {
            $classes[] = esc_attr( $advanced['css_classes'] );
        }

        // Sticky top offset (inline overrides CSS top:0) — only for vertical sticky
        $inline_styles = [];
        if ( $is_sticky_v && $sticky_top > 0 ) {
            $inline_styles[] = "top: {$sticky_top}px";
        }

        // Background handling
        $tile_bg = $this->get_effective_bg( $style );
        $has_bg_image   = ( $tile_bg['type'] === 'image' && ! empty( $tile_bg['image_url'] ) );
        $has_bg_video   = ( $tile_bg['type'] === 'video' && ! empty( $tile_bg['video_url'] ) );
        $has_bg_gallery = ( $tile_bg['type'] === 'gallery' && ! empty( $tile_bg['gallery_images'] ) && is_array( $tile_bg['gallery_images'] ) );
        $has_bg_any     = ( $tile_bg['type'] !== 'none' );
        $has_overlay    = ( $has_bg_any && ! empty( $tile_bg['overlay_opacity'] ) && intval( $tile_bg['overlay_opacity'] ) > 0 );

        if ( $has_bg_image || $has_bg_video || $has_bg_gallery ) {
            $classes[] = 'uk-position-relative';
            $inline_styles[] = 'overflow: clip';
        } elseif ( $tile_bg['type'] !== 'none' ) {
            $bg_css = $this->get_bg_inline_css( $tile_bg );
            if ( $bg_css ) $inline_styles[] = $bg_css;
        }

        // Video cover height
        if ( $has_bg_video && ! empty( $tile_bg['cover_height'] ) && intval( $tile_bg['cover_height'] ) > 0 ) {
            $inline_styles[] = 'min-height: ' . intval( $tile_bg['cover_height'] ) . 'px';
        }

        // Margin & Padding
        if ( ! empty( $style['margin_top'] ) )    $inline_styles[] = "margin-top: {$style['margin_top']}px";
        if ( ! empty( $style['margin_right'] ) )  $inline_styles[] = "margin-right: {$style['margin_right']}px";
        if ( ! empty( $style['margin_bottom'] ) ) $inline_styles[] = "margin-bottom: {$style['margin_bottom']}px";
        if ( ! empty( $style['margin_left'] ) )   $inline_styles[] = "margin-left: {$style['margin_left']}px";
        if ( ! empty( $style['padding_top'] ) )    $inline_styles[] = "padding-top: {$style['padding_top']}px";
        if ( ! empty( $style['padding_right'] ) )  $inline_styles[] = "padding-right: {$style['padding_right']}px";
        if ( ! empty( $style['padding_bottom'] ) ) $inline_styles[] = "padding-bottom: {$style['padding_bottom']}px";
        if ( ! empty( $style['padding_left'] ) )   $inline_styles[] = "padding-left: {$style['padding_left']}px";

        // Border radius
        if ( ! empty( $style['border_radius'] ) )  $inline_styles[] = $this->build_border_radius_css( $style['border_radius'] );

        // Border
        if ( ! empty( $style['border_width'] ) && intval( $style['border_width'] ) > 0 ) {
            $bw = intval( $style['border_width'] );
            $bs = $style['border_style'] ?? 'solid';
            $bc = $style['border_color'] ?? '#374151';
            $inline_styles[] = "border: {$bw}px {$bs} {$bc}";
        }

        // Shadow
        if ( ! empty( $style['shadow'] ) ) {
            $uk_shadow_map = [
                'sm' => 'uk-box-shadow-small',
                'md' => 'uk-box-shadow-medium',
                'lg' => 'uk-box-shadow-large',
                'xl' => 'uk-box-shadow-xlarge',
            ];
            if ( isset( $uk_shadow_map[ $style['shadow'] ] ) ) {
                $classes[] = $uk_shadow_map[ $style['shadow'] ];
            }
        }

        // Opacity
        if ( ! empty( $style['opacity'] ) && intval( $style['opacity'] ) < 100 ) {
            $opacity = intval( $style['opacity'] ) / 100;
            $inline_styles[] = "opacity: {$opacity}";
        }

        // Flex container settings
        $flex_css = $this->build_flex_container_css( $s );
        foreach ( $flex_css as $decl ) {
            $inline_styles[] = $decl;
        }

        // CSS Grid layout (overrides flex if layout_mode=grid)
        $grid_css = $this->build_css_grid_css( $s );
        foreach ( $grid_css as $decl ) {
            $inline_styles[] = $decl;
        }

        // Overflow clip needed for border-radius clipping (clip instead of hidden to preserve sticky)
        if ( ! empty( $style['border_radius'] ) ) {
            $inline_styles[] = 'overflow: clip';
        }

        // CSS Transform (normal state)
        $transform_css = $this->build_transform_css( $style );
        if ( $transform_css ) {
            foreach ( $transform_css as $decl ) {
                $inline_styles[] = $decl;
            }
        }

        // Box shadow (segue border-radius del div)
        $box_shadow = $this->build_box_shadow_css( $style );
        if ( $box_shadow ) {
            $inline_styles[] = $box_shadow;
        }

        // Text shadow
        $text_shadow = $this->build_text_shadow_css( $style );
        if ( $text_shadow ) {
            $inline_styles[] = $text_shadow;
        }

        // Backdrop filter
        $backdrop = $this->build_backdrop_filter_css( $style );
        if ( $backdrop ) {
            foreach ( $backdrop as $decl ) {
                $inline_styles[] = $decl;
            }
        }

        // Overflow
        if ( ! empty( $style['overflow'] ) && $style['overflow'] !== 'visible' ) {
            $inline_styles[] = 'overflow: ' . esc_attr( $style['overflow'] );
        }

        // Mask
        $mask_css = $this->build_mask_css( $style );
        if ( $mask_css ) {
            foreach ( $mask_css as $decl ) {
                $inline_styles[] = $decl;
            }
        }

        if ( ! empty( $advanced['custom_css'] ) ) {
            $inline_styles[] = $advanced['custom_css'];
        }

        // Positioning (absolute/fixed/relative) for sections
        $pos_mode = $advanced['position_mode'] ?? 'static';
        if ( $pos_mode && $pos_mode !== 'static' ) {
            $inline_styles[] = 'position: ' . esc_attr( $pos_mode );
            foreach ( [ 'top', 'left', 'bottom', 'right' ] as $dir ) {
                $val = $advanced[ 'position_' . $dir ] ?? '';
                if ( $val !== '' ) {
                    $inline_styles[] = $dir . ': ' . ( is_numeric( $val ) ? $val . 'px' : esc_attr( $val ) );
                }
            }
            $w = $advanced['position_width'] ?? '';
            if ( $w !== '' ) {
                $inline_styles[] = 'width: ' . ( is_numeric( $w ) ? $w . 'px' : esc_attr( $w ) );
            }
            $z = $advanced['position_zindex'] ?? '';
            if ( $z !== '' ) {
                $inline_styles[] = 'z-index: ' . intval( $z );
            }
        }

        // HTML ID (always generate for hover CSS support)
        $tile_counter++;
        $css_id  = ! empty( $advanced['html_id'] ) ? $advanced['html_id'] : 'ms-' . $template_id . '-' . $tile_counter;
        $id_attr = ' id="' . esc_attr( $css_id ) . '"';

        // Hover CSS rules
        $this->collect_hover_css( $style, $css_id, false, $hover_css_rules );
        $this->collect_responsive_css( $style, $css_id, $advanced );

        // Infinite animation
        $inf_anim_css = $this->build_infinite_animation_css( $s, $css_id );
        if ( $inf_anim_css ) {
            $hover_css_rules[] = $inf_anim_css;
        }

        // Custom CSS per sezione (campo settings.custom_css)
        $this->collect_custom_css( $s, $css_id, $hover_css_rules );

        // Scroll snap: add full-screen height + snap alignment
        if ( $scroll_snap ) {
            $inline_styles[] = 'height: 100vh';
            $inline_styles[] = 'scroll-snap-align: start';
            $inline_styles[] = 'box-sizing: border-box';
        }

        // Entrance animation (olo-entrance-*)
        $entrance = $s['entrance_animation'] ?? 'none';
        if ( $entrance && $entrance !== 'none' ) {
            $classes[] = 'olo-entrance-' . sanitize_html_class( $entrance );
            if ( ! empty( $s['entrance_stagger'] ) ) {
                $stagger_delay = intval( $s['entrance_stagger_delay'] ?? 100 );
                $stagger_delay = max( 25, min( 500, $stagger_delay ) );
                $classes[] = 'olo-stagger-parent';
                $inline_styles[] = '--olo-stagger-delay: ' . $stagger_delay . 'ms';
            }
        }

        // Scrollspy & element parallax attributes
        $scrollspy_attr = $this->build_scrollspy_attr( $advanced );
        $el_parallax_attr = $this->build_element_parallax_attr( $advanced );
        $mouse_attrs = $this->build_mouse_attrs( $advanced );

        // Infinite animation & mask (inline style for elements)
        $inf_anim_css = $this->build_inline_animation_css( $advanced );
        if ( $inf_anim_css ) $inline_styles[] = $inf_anim_css;
        $mask_css = $this->build_inline_mask_css( $advanced );
        if ( $mask_css ) $inline_styles[] = $mask_css;

        // Snap dots data attributes
        $snap_data_attr = '';
        if ( $snap_dots ) {
            $dot_color        = sanitize_hex_color( $s['snap_dot_color'] ?? '' ) ?: '#ffffff';
            $dot_active_color = sanitize_hex_color( $s['snap_dot_active_color'] ?? '' );
            $dot_position     = ( $s['snap_dot_position'] ?? 'right' ) === 'left' ? 'left' : 'right';
            $snap_data_attr   = ' data-olo-snap-section';
            $snap_data_attr  .= ' data-snap-dot-color="' . esc_attr( $dot_color ) . '"';
            if ( $dot_active_color ) {
                $snap_data_attr .= ' data-snap-dot-active="' . esc_attr( $dot_active_color ) . '"';
            }
            $snap_data_attr .= ' data-snap-dot-pos="' . esc_attr( $dot_position ) . '"';
        }

        $html = '<section role="region" class="' . esc_attr( implode( ' ', $classes ) ) . '"' . $id_attr;
        if ( $inline_styles ) {
            $html .= ' style="' . esc_attr( implode( '; ', $inline_styles ) ) . '"';
        }
        $html .= $scrollspy_attr . $el_parallax_attr . $snap_data_attr . $mouse_attrs . '>';

        // Background image layer (with optional UIkit parallax)
        if ( $has_bg_image ) {
            $bg_size = esc_attr( $tile_bg['image_size'] ?? 'cover' );
            $bg_pos  = esc_attr( $tile_bg['image_position'] ?? 'center center' );

            $html .= '<div class="uk-position-cover" style="background-image: url(' . esc_url( $tile_bg['image_url'] ) . '); background-size: ' . $bg_size . '; background-position: ' . $bg_pos . '; background-repeat: no-repeat"';
            $html .= $this->build_uk_parallax_attr( $tile_bg );
            $html .= '></div>';
        }

        // Video background layer
        if ( $has_bg_video ) {
            $vid_url    = esc_url( $tile_bg['video_url'] );
            $vid_poster = ! empty( $tile_bg['video_poster'] ) ? esc_url( $tile_bg['video_poster'] ) : '';
            $vid_pos    = esc_attr( $tile_bg['image_position'] ?? 'center center' );
            $vid_fit    = esc_attr( $tile_bg['video_fit'] ?? 'cover' );
            $vid_cover  = ( ! empty( $tile_bg['cover_height'] ) && intval( $tile_bg['cover_height'] ) > 0 ) ? intval( $tile_bg['cover_height'] ) : 0;
            $vid_scale  = ( ! empty( $tile_bg['video_scale'] ) && intval( $tile_bg['video_scale'] ) > 100 ) ? intval( $tile_bg['video_scale'] ) / 100 : 0;
            $scale_css  = $vid_scale ? '; transform: scale(' . $vid_scale . '); transform-origin: ' . $vid_pos : '';
            if ( $vid_cover ) {
                $html .= '<video aria-hidden="true" style="position: absolute; top: 0; left: 0; width: 100%; height: ' . $vid_cover . 'px; object-fit: ' . $vid_fit . '; object-position: ' . $vid_pos . '; pointer-events: none' . $scale_css . '" autoplay muted loop playsinline';
            } else {
                $html .= '<video aria-hidden="true" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: ' . $vid_fit . '; object-position: ' . $vid_pos . '; pointer-events: none' . $scale_css . '" autoplay muted loop playsinline';
            }
            if ( $vid_poster ) $html .= ' poster="' . $vid_poster . '"';
            $html .= '><source src="' . $vid_url . '" type="' . $this->get_video_mime( $vid_url ) . '"></video>';
        }

        // Gallery background slideshow
        if ( $has_bg_gallery ) {
            $html .= $this->render_bg_gallery( $tile_bg );
        }

        // Overlay layer
        if ( $has_overlay ) {
            $ov_color   = esc_attr( $tile_bg['overlay_color'] ?? '#000000' );
            $ov_opacity = intval( $tile_bg['overlay_opacity'] ) / 100;
            $html .= '<div class="uk-position-cover" style="background-color: ' . $ov_color . '; opacity: ' . $ov_opacity . '; pointer-events: none" aria-hidden="true"></div>';
        }

        // Container width wrapper (relative for z-index above bg/overlay)
        $width = $s['width'] ?? $s['section_width'] ?? 'default';

        if ( $width === 'fullbleed' ) {
            // Edge-to-edge: no uk-container, no padding
            $container_class = 'olo-section-fullbleed';
        } else {
            $container_class = 'uk-container';
            $width_map = [
                'small'  => 'uk-container-small',
                'large'  => 'uk-container-large',
                'xlarge' => 'uk-container-xlarge',
                'expand' => 'uk-container-expand',
            ];
            if ( isset( $width_map[ $width ] ) ) {
                $container_class .= ' ' . $width_map[ $width ];
            }
        }

        if ( $has_bg_image || $has_bg_video || $has_bg_gallery || $has_overlay ) {
            $container_class .= ' uk-position-relative';
            $html .= '<div class="' . esc_attr( $container_class ) . '" style="z-index: 1">';
        } else {
            $html .= '<div class="' . esc_attr( $container_class ) . '">';
        }

        foreach ( $node['children'] ?? [] as $child ) {
            $html .= $this->render_node( $child, $manager, $template_id, $hover_css_rules, $tile_counter );
        }

        $html .= '</div></section>';

        // Reveal sections: wrap in a container that limits the sticky range.
        // JS will set wrapper height = 2×section height and margin-top = -section height
        // so the section sits behind the previous one and unsticks once fully revealed.
        if ( $sticky_effect === 'reveal' ) {
            $html = '<div class="olo-reveal-wrapper" data-sticky-top="' . $sticky_top . '">' . $html . '</div>';
        }

        // Horizontal sticky: add data attribute for JS grouping
        if ( $is_sticky_h ) {
            // Mark section with data for JS to build the horizontal scroll group
            $html = '<div class="olo-h-marker" data-sticky-h="' . esc_attr( $sticky_effect ) . '" data-sticky-top="' . $sticky_top . '" style="display:contents">' . $html . '</div>';
        }

        return $html;
    }

    /**
     * Render a Row using UIkit grid.
     */
    private function render_row_node( $node, $manager, $template_id, &$hover_css_rules, &$tile_counter ) {
        $s = $node['settings'] ?? [];
        $style    = $node['style'] ?? [];
        $advanced = $node['advanced'] ?? [];
        $gap    = absint( $s['gap'] ?? 16 );
        $valign = $s['vertical_align'] ?? 'stretch';
        $stack        = ! empty( $s['stack_mobile'] );
        $stack_tablet = ! empty( $s['stack_tablet'] );

        // Background handling
        $tile_bg      = $this->get_effective_bg( $style );
        $has_bg_image   = ( $tile_bg['type'] === 'image' && ! empty( $tile_bg['image_url'] ) );
        $has_bg_video   = ( $tile_bg['type'] === 'video' && ! empty( $tile_bg['video_url'] ) );
        $has_bg_gallery = ( $tile_bg['type'] === 'gallery' && ! empty( $tile_bg['gallery_images'] ) && is_array( $tile_bg['gallery_images'] ) );
        $has_bg_any     = ( $tile_bg['type'] !== 'none' );
        $has_overlay    = ( $has_bg_any && ! empty( $tile_bg['overlay_opacity'] ) && intval( $tile_bg['overlay_opacity'] ) > 0 );

        // Row margin/padding
        $row_spacing_styles = [];
        if ( ! empty( $style['margin_top'] ) )    $row_spacing_styles[] = "margin-top: {$style['margin_top']}px";
        if ( ! empty( $style['margin_right'] ) )  $row_spacing_styles[] = "margin-right: {$style['margin_right']}px";
        if ( ! empty( $style['margin_bottom'] ) ) $row_spacing_styles[] = "margin-bottom: {$style['margin_bottom']}px";
        if ( ! empty( $style['margin_left'] ) )   $row_spacing_styles[] = "margin-left: {$style['margin_left']}px";
        if ( ! empty( $style['padding_top'] ) )    $row_spacing_styles[] = "padding-top: {$style['padding_top']}px";
        if ( ! empty( $style['padding_right'] ) )  $row_spacing_styles[] = "padding-right: {$style['padding_right']}px";
        if ( ! empty( $style['padding_bottom'] ) ) $row_spacing_styles[] = "padding-bottom: {$style['padding_bottom']}px";
        if ( ! empty( $style['padding_left'] ) )   $row_spacing_styles[] = "padding-left: {$style['padding_left']}px";

        // Border radius
        if ( ! empty( $style['border_radius'] ) )  $row_spacing_styles[] = $this->build_border_radius_css( $style['border_radius'] );

        // Border
        if ( ! empty( $style['border_width'] ) && intval( $style['border_width'] ) > 0 ) {
            $bw = intval( $style['border_width'] );
            $bs = $style['border_style'] ?? 'solid';
            $bc = $style['border_color'] ?? '#374151';
            $row_spacing_styles[] = "border: {$bw}px {$bs} {$bc}";
        }

        // Opacity
        if ( ! empty( $style['opacity'] ) && intval( $style['opacity'] ) < 100 ) {
            $opacity = intval( $style['opacity'] ) / 100;
            $row_spacing_styles[] = "opacity: {$opacity}";
        }

        // Video cover height
        if ( $has_bg_video && ! empty( $tile_bg['cover_height'] ) && intval( $tile_bg['cover_height'] ) > 0 ) {
            $row_spacing_styles[] = 'min-height: ' . intval( $tile_bg['cover_height'] ) . 'px';
        }

        // Positioning (absolute/fixed/relative) for rows
        $pos_mode = $advanced['position_mode'] ?? 'static';
        if ( $pos_mode && $pos_mode !== 'static' ) {
            $row_spacing_styles[] = 'position: ' . esc_attr( $pos_mode );
            foreach ( [ 'top', 'left', 'bottom', 'right' ] as $dir ) {
                $val = $advanced[ 'position_' . $dir ] ?? '';
                if ( $val !== '' ) {
                    $row_spacing_styles[] = $dir . ': ' . ( is_numeric( $val ) ? $val . 'px' : esc_attr( $val ) );
                }
            }
            $w = $advanced['position_width'] ?? '';
            if ( $w !== '' ) {
                $row_spacing_styles[] = 'width: ' . ( is_numeric( $w ) ? $w . 'px' : esc_attr( $w ) );
            }
            $z = $advanced['position_zindex'] ?? '';
            if ( $z !== '' ) {
                $row_spacing_styles[] = 'z-index: ' . intval( $z );
            }
        }

        // Wrapper for row background or spacing
        $has_border_radius = ! empty( $style['border_radius'] );
        $has_border = ! empty( $style['border_width'] ) && intval( $style['border_width'] ) > 0;
        $has_opacity = ! empty( $style['opacity'] ) && intval( $style['opacity'] ) < 100;
        $has_shadow = ! empty( $style['shadow'] );
        $has_spacing = ! empty( $row_spacing_styles );
        $has_positioning = $pos_mode && $pos_mode !== 'static';
        $has_hover   = ! empty( $style['hover'] ) && is_array( $style['hover'] ) && array_filter( $style['hover'], function( $v ) { return $v !== null && $v !== '' && $v !== false; } );
        $needs_wrapper = $has_bg_image || $has_bg_video || $has_bg_gallery || $has_overlay || ( $tile_bg['type'] !== 'none' ) || $has_spacing || $has_border_radius || $has_border || $has_opacity || $has_shadow || $has_hover || $has_positioning;

        // ID for hover CSS support
        $tile_counter++;
        $row_css_id = ! empty( $advanced['html_id'] ) ? $advanced['html_id'] : 'mr-' . $template_id . '-' . $tile_counter;

        // Hover CSS rules
        $this->collect_hover_css( $style, $row_css_id, false, $hover_css_rules );
        $this->collect_responsive_css( $style, $row_css_id, $advanced );

        // Custom CSS per riga (campo settings.custom_css)
        $this->collect_custom_css( $s, $row_css_id, $hover_css_rules );

        $wrapper_styles = [];
        $wrapper_classes = [];

        if ( $needs_wrapper ) {
            if ( $has_bg_image || $has_bg_video || $has_bg_gallery ) {
                $wrapper_classes[] = 'uk-position-relative';
                $wrapper_styles[] = 'overflow: clip';
            } elseif ( $tile_bg['type'] !== 'none' ) {
                $bg_css = $this->get_bg_inline_css( $tile_bg );
                if ( $bg_css ) $wrapper_styles[] = $bg_css;
            }
            if ( $has_spacing ) {
                $wrapper_styles = array_merge( $wrapper_styles, $row_spacing_styles );
            }
            // Shadow class on wrapper
            if ( $has_shadow ) {
                $uk_shadow_map = [
                    'sm' => 'uk-box-shadow-small',
                    'md' => 'uk-box-shadow-medium',
                    'lg' => 'uk-box-shadow-large',
                    'xl' => 'uk-box-shadow-xlarge',
                ];
                if ( isset( $uk_shadow_map[ $style['shadow'] ] ) ) {
                    $wrapper_classes[] = $uk_shadow_map[ $style['shadow'] ];
                }
            }
            // Overflow clip for border-radius clipping (clip instead of hidden to preserve sticky)
            if ( $has_border_radius ) {
                $wrapper_styles[] = 'overflow: clip';
            }
            if ( ! empty( $advanced['custom_css'] ) ) {
                $wrapper_styles[] = $advanced['custom_css'];
            }
        }

        // UIkit grid classes
        $classes = [];

        // Gap mapping to UIkit column-gap
        $gap_map = [
            0  => 'uk-grid-collapse',
            4  => 'uk-grid-small',
            8  => 'uk-grid-small',
            16 => '', // default gap
            24 => 'uk-grid-medium',
            32 => 'uk-grid-medium',
            48 => 'uk-grid-large',
        ];
        // Find closest gap
        $closest_gap = '';
        $min_diff = PHP_INT_MAX;
        foreach ( $gap_map as $g => $cls ) {
            $diff = abs( $gap - $g );
            if ( $diff < $min_diff ) {
                $min_diff = $diff;
                $closest_gap = $cls;
            }
        }
        if ( $closest_gap ) $classes[] = $closest_gap;

        // Vertical alignment
        $valign_map = [
            'start'  => 'uk-flex-top',
            'center' => 'uk-flex-middle',
            'end'    => 'uk-flex-bottom',
        ];
        if ( isset( $valign_map[ $valign ] ) ) {
            $classes[] = $valign_map[ $valign ];
        }

        // Custom class will be added after we know it
        $pre_class_attr_classes = $classes;

        // No-stack class for mobile
        $nostack_class = '';
        if ( ! $stack ) {
            $nostack_class = 'olo-nostack-' . substr( md5( $node['id'] ?? wp_rand() ), 0, 6 );
            $classes[] = $nostack_class;
            $pre_class_attr_classes[] = $nostack_class;
        }

        // uk-grid attribute with options
        $grid_opts = [];
        if ( $stack ) {
            $grid_opts[] = 'margin: uk-margin-small-top';
        }
        $uk_grid = 'uk-grid';

        $html = '';

        // No-stack CSS: prevent columns from stacking on mobile
        if ( ! $stack && $nostack_class ) {
            $html .= '<style>';
            $html .= '.' . $nostack_class . '{flex-wrap:nowrap!important}';
            $html .= '.' . $nostack_class . '>*{flex:1 1 auto}';
            $html .= '.' . $nostack_class . '>[class*="uk-width-expand"]{flex:1 1 0%}';
            $html .= '</style>';
        }

        // Stack on tablet: force columns to 100% width between 960px and 1200px
        if ( $stack_tablet ) {
            $stack_tab_class = $nostack_class ?: ( 'olo-nostack-' . substr( md5( $node['id'] ?? wp_rand() ), 0, 6 ) );
            if ( ! $nostack_class ) {
                $classes[] = $stack_tab_class;
                $pre_class_attr_classes[] = $stack_tab_class;
            }
            $html .= '<style>';
            $html .= '@container olo-tpl (max-width:1199px){';
            $html .= '.' . $stack_tab_class . '{flex-wrap:wrap!important}';
            $html .= '.' . $stack_tab_class . '>*{width:100%!important;flex:0 0 100%!important}';
            $html .= '}';
            $html .= '</style>';
        }

        // Custom widths: generate scoped <style> block
        $is_custom_layout = ( ( $s['layout'] ?? '' ) === 'custom' && ! empty( $s['custom_widths'] ) );
        $custom_class = '';
        if ( $is_custom_layout ) {
            $custom_id = substr( md5( ( $node['id'] ?? '' ) . $s['custom_widths'] ), 0, 8 );
            $custom_class = 'olo-cw-' . $custom_id;
            $widths = array_filter( array_map( 'floatval', explode( ',', $s['custom_widths'] ) ), function( $v ) { return $v > 0; } );
            if ( ! empty( $widths ) ) {
                $html .= '<style>';
                // When nostack is active, apply custom widths at ALL breakpoints
                if ( ! $stack ) {
                    foreach ( $widths as $i => $w ) {
                        $nth = $i + 1;
                        $html .= '.' . $custom_class . '>:nth-child(' . $nth . '){width:' . $w . '%!important}';
                    }
                } else {
                    $html .= '@container olo-tpl (min-width:960px){';
                    foreach ( $widths as $i => $w ) {
                        $nth = $i + 1;
                        $html .= '.' . $custom_class . '>:nth-child(' . $nth . '){width:' . $w . '%!important}';
                    }
                    $html .= '}';
                }
                $html .= '</style>';
            }
        }

        // Build class attribute for grid div (after custom class is known)
        if ( $custom_class ) {
            $pre_class_attr_classes[] = $custom_class;
        }
        $class_attr = ! empty( $pre_class_attr_classes ) ? ' class="' . esc_attr( implode( ' ', $pre_class_attr_classes ) ) . '"' : '';

        // Entrance animation (olo-entrance-*) for row
        $entrance = $s['entrance_animation'] ?? 'none';
        if ( $entrance && $entrance !== 'none' ) {
            $wrapper_classes[] = 'olo-entrance-' . sanitize_html_class( $entrance );
            if ( ! empty( $s['entrance_stagger'] ) ) {
                $stagger_delay = intval( $s['entrance_stagger_delay'] ?? 100 );
                $stagger_delay = max( 25, min( 500, $stagger_delay ) );
                $wrapper_classes[] = 'olo-stagger-parent';
                $wrapper_styles[] = '--olo-stagger-delay: ' . $stagger_delay . 'ms';
            }
        }

        // Scrollspy & element parallax attributes for row
        $row_scrollspy_attr = $this->build_scrollspy_attr( $advanced );
        $row_el_parallax_attr = $this->build_element_parallax_attr( $advanced );
        $row_mouse_attrs = $this->build_mouse_attrs( $advanced );

        // Open row wrapper (for background)
        if ( $needs_wrapper ) {
            $html .= '<div id="' . esc_attr( $row_css_id ) . '" class="' . esc_attr( implode( ' ', $wrapper_classes ) ) . '"';
            if ( $wrapper_styles ) {
                $html .= ' style="' . esc_attr( implode( '; ', $wrapper_styles ) ) . '"';
            }
            $html .= $row_scrollspy_attr . $row_el_parallax_attr . '>';

            // Background image layer (with optional UIkit parallax)
            if ( $has_bg_image ) {
                $bg_size = esc_attr( $tile_bg['image_size'] ?? 'cover' );
                $bg_pos  = esc_attr( $tile_bg['image_position'] ?? 'center center' );

                $html .= '<div class="uk-position-cover" style="background-image: url(' . esc_url( $tile_bg['image_url'] ) . '); background-size: ' . $bg_size . '; background-position: ' . $bg_pos . '; background-repeat: no-repeat"';
                $html .= $this->build_uk_parallax_attr( $tile_bg );
                $html .= '></div>';
            }

            // Video background layer
            if ( $has_bg_video ) {
                $vid_url    = esc_url( $tile_bg['video_url'] );
                $vid_poster = ! empty( $tile_bg['video_poster'] ) ? esc_url( $tile_bg['video_poster'] ) : '';
                $vid_pos    = esc_attr( $tile_bg['image_position'] ?? 'center center' );
                $vid_fit    = esc_attr( $tile_bg['video_fit'] ?? 'cover' );
                $vid_cover  = ( ! empty( $tile_bg['cover_height'] ) && intval( $tile_bg['cover_height'] ) > 0 ) ? intval( $tile_bg['cover_height'] ) : 0;
                $vid_scale  = ( ! empty( $tile_bg['video_scale'] ) && intval( $tile_bg['video_scale'] ) > 100 ) ? intval( $tile_bg['video_scale'] ) / 100 : 0;
                $scale_css  = $vid_scale ? '; transform: scale(' . $vid_scale . '); transform-origin: ' . $vid_pos : '';
                if ( $vid_cover ) {
                    $html .= '<video style="position: absolute; top: 0; left: 0; width: 100%; height: ' . $vid_cover . 'px; object-fit: ' . $vid_fit . '; object-position: ' . $vid_pos . '; pointer-events: none' . $scale_css . '" autoplay muted loop playsinline';
                } else {
                    $html .= '<video style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: ' . $vid_fit . '; object-position: ' . $vid_pos . '; pointer-events: none' . $scale_css . '" autoplay muted loop playsinline';
                }
                if ( $vid_poster ) $html .= ' poster="' . $vid_poster . '"';
                $html .= '><source src="' . $vid_url . '" type="' . $this->get_video_mime( $vid_url ) . '"></video>';
            }

            // Gallery background slideshow (row)
            if ( $has_bg_gallery ) {
                $html .= $this->render_bg_gallery( $tile_bg );
            }

            // Overlay layer
            if ( $has_overlay ) {
                $ov_color   = esc_attr( $tile_bg['overlay_color'] ?? '#000000' );
                $ov_opacity = intval( $tile_bg['overlay_opacity'] ) / 100;
                $html .= '<div class="uk-position-cover" style="background-color: ' . $ov_color . '; opacity: ' . $ov_opacity . '; pointer-events: none"></div>';
            }
        }

        // Flex container overrides for the row grid (direction, justify, align, wrap, gap)
        $row_flex_styles = [];
        $rfd = $s['flex_direction'] ?? '';
        if ( $rfd && $rfd !== 'row' )         $row_flex_styles[] = 'flex-direction: ' . esc_attr( $rfd );
        $rfj = $s['flex_justify'] ?? '';
        if ( $rfj && $rfj !== 'flex-start' )  $row_flex_styles[] = 'justify-content: ' . esc_attr( $rfj );
        $rfa = $s['flex_align'] ?? '';
        if ( $rfa && $rfa !== 'stretch' )     $row_flex_styles[] = 'align-items: ' . esc_attr( $rfa );
        $rfw = $s['flex_wrap'] ?? '';
        if ( $rfw && $rfw !== 'nowrap' )      $row_flex_styles[] = 'flex-wrap: ' . esc_attr( $rfw );
        $rfcg = $s['flex_column_gap'] ?? '';
        $rfrg = $s['flex_row_gap'] ?? '';
        $rfg  = $s['flex_gap'] ?? ''; // legacy
        if ( ( $rfcg && intval( $rfcg ) > 0 ) || ( $rfrg && intval( $rfrg ) > 0 ) ) {
            if ( $rfcg && intval( $rfcg ) > 0 ) $row_flex_styles[] = 'column-gap: ' . intval( $rfcg ) . 'px';
            if ( $rfrg && intval( $rfrg ) > 0 ) $row_flex_styles[] = 'row-gap: ' . intval( $rfrg ) . 'px';
        } elseif ( $rfg && intval( $rfg ) > 0 ) {
            $row_flex_styles[] = 'gap: ' . intval( $rfg ) . 'px';
        }

        // Grid — if no wrapper, put scrollspy/parallax on the grid div itself
        $grid_extra_attrs = $needs_wrapper ? '' : ( $row_scrollspy_attr . $row_el_parallax_attr );
        $grid_style_parts = $row_flex_styles;
        if ( $needs_wrapper && ( $has_bg_image || $has_bg_video || $has_bg_gallery || $has_overlay ) ) {
            $grid_style_parts[] = 'position: relative';
            $grid_style_parts[] = 'z-index: 1';
        }
        // === CSS Grid mode ===
        $is_css_grid = ( ( $s['layout_mode'] ?? '' ) === 'grid' );
        if ( $is_css_grid ) {
            $grid_css_parts = [];
            $grid_css_parts[] = 'display: grid';
            if ( ! empty( $s['grid_columns'] ) ) {
                $grid_css_parts[] = 'grid-template-columns: ' . esc_attr( $s['grid_columns'] );
            }
            if ( ! empty( $s['grid_rows'] ) ) {
                $grid_css_parts[] = 'grid-template-rows: ' . esc_attr( $s['grid_rows'] );
            }
            // Separate column/row gaps or unified gap
            $g_col_gap = $s['grid_column_gap'] ?? '';
            $g_row_gap = $s['grid_row_gap'] ?? '';
            if ( $g_col_gap !== '' && $g_row_gap !== '' ) {
                $grid_css_parts[] = 'column-gap: ' . intval( $g_col_gap ) . 'px';
                $grid_css_parts[] = 'row-gap: ' . intval( $g_row_gap ) . 'px';
            } elseif ( $g_col_gap !== '' ) {
                $grid_css_parts[] = 'column-gap: ' . intval( $g_col_gap ) . 'px';
                $grid_css_parts[] = 'row-gap: ' . $gap . 'px';
            } elseif ( $g_row_gap !== '' ) {
                $grid_css_parts[] = 'column-gap: ' . $gap . 'px';
                $grid_css_parts[] = 'row-gap: ' . intval( $g_row_gap ) . 'px';
            } else {
                $grid_css_parts[] = 'gap: ' . $gap . 'px';
            }
            // Grid auto-flow (direction + density)
            $g_auto_flow = $s['grid_auto_flow'] ?? 'row';
            if ( ! empty( $s['grid_auto_flow_dense'] ) ) {
                $g_auto_flow .= ' dense';
            }
            if ( $g_auto_flow !== 'row' ) {
                $grid_css_parts[] = 'grid-auto-flow: ' . esc_attr( $g_auto_flow );
            }
            // Justify content
            $g_jc = $s['grid_justify_content'] ?? '';
            if ( $g_jc && $g_jc !== 'stretch' ) {
                $grid_css_parts[] = 'justify-content: ' . esc_attr( $g_jc );
            }
            // Align items
            $g_ai = $s['grid_align_items'] ?? $valign;
            if ( $g_ai && $g_ai !== 'stretch' ) {
                $grid_css_parts[] = 'align-items: ' . esc_attr( $g_ai );
            }
            // Align content
            $g_ac = $s['grid_align_content'] ?? '';
            if ( $g_ac && $g_ac !== 'stretch' ) {
                $grid_css_parts[] = 'align-content: ' . esc_attr( $g_ac );
            }
            if ( $needs_wrapper && ( $has_bg_image || $has_bg_video || $has_bg_gallery || $has_overlay ) ) {
                $grid_css_parts[] = 'position: relative';
                $grid_css_parts[] = 'z-index: 1';
            }
            $grid_extra_attrs = $needs_wrapper ? '' : ( $row_scrollspy_attr . $row_el_parallax_attr );
            $grid_class_list = [];
            if ( $stack ) $grid_class_list[] = 'olo-grid-stack';
            $grid_class_attr = ! empty( $grid_class_list ) ? ' class="' . esc_attr( implode( ' ', $grid_class_list ) ) . '"' : '';
            $html .= '<div' . $grid_class_attr . ' style="' . esc_attr( implode( '; ', $grid_css_parts ) ) . '"' . $grid_extra_attrs . '>';

            // Loop mode: repeat children for each post from WP_Query
            $loop_enabled = ! empty( $s['loop_enabled'] );
            if ( $loop_enabled ) {
                $loop_posts = $this->run_row_loop_query( $s );
                if ( ! empty( $loop_posts ) ) {
                    global $post;
                    $old_post = $post;
                    foreach ( $loop_posts as $loop_post ) {
                        $post = $loop_post;
                        setup_postdata( $post );
                        foreach ( $node['children'] ?? [] as $child ) {
                            $html .= $this->render_node( $child, $manager, $template_id, $hover_css_rules, $tile_counter, true );
                        }
                    }
                    $post = $old_post;
                    if ( $old_post ) { setup_postdata( $old_post ); } else { wp_reset_postdata(); }
                }
            } else {
                foreach ( $node['children'] ?? [] as $child ) {
                    $html .= $this->render_node( $child, $manager, $template_id, $hover_css_rules, $tile_counter, true );
                }
            }

            $html .= '</div>';

            // Stack on mobile: override grid to 1 column
            if ( $stack ) {
                $grid_id = 'olo-g-' . substr( md5( $node['id'] ?? wp_rand() ), 0, 6 );
                $html = str_replace( '<div' . $grid_class_attr, '<div class="' . esc_attr( trim( implode( ' ', $grid_class_list ) . ' ' . $grid_id ) ) . '"', $html );
                $bp_mobile = intval( $this->breakpoints['tablet'] ?? 960 );
                $html .= '<style>@media(max-width:' . $bp_mobile . 'px){.' . $grid_id . '{grid-template-columns:1fr!important;grid-template-rows:auto!important}.' . $grid_id . '>*{grid-column:auto!important;grid-row:auto!important}}</style>';
            }
        } else {
            // === Classic Flexbox mode ===
            $grid_style_attr = ! empty( $grid_style_parts ) ? ' style="' . esc_attr( implode( '; ', $grid_style_parts ) ) . '"' : '';
            $grid_extra_attrs = $needs_wrapper ? '' : ( $row_scrollspy_attr . $row_el_parallax_attr );
            $html .= '<div' . $class_attr . ' ' . $uk_grid . $grid_style_attr . $grid_extra_attrs . '>';

            // Loop mode: repeat children for each post from WP_Query
            $loop_enabled_flex = ! empty( $s['loop_enabled'] );
            if ( $loop_enabled_flex ) {
                $loop_posts_flex = $this->run_row_loop_query( $s );
                if ( ! empty( $loop_posts_flex ) ) {
                    global $post;
                    $old_post_flex = $post;
                    foreach ( $loop_posts_flex as $loop_post ) {
                        $post = $loop_post;
                        setup_postdata( $post );
                        foreach ( $node['children'] ?? [] as $child ) {
                            $html .= $this->render_node( $child, $manager, $template_id, $hover_css_rules, $tile_counter );
                        }
                    }
                    $post = $old_post_flex;
                    if ( $old_post_flex ) { setup_postdata( $old_post_flex ); } else { wp_reset_postdata(); }
                }
            } else {
                foreach ( $node['children'] ?? [] as $child ) {
                    $html .= $this->render_node( $child, $manager, $template_id, $hover_css_rules, $tile_counter );
                }
            }

            $html .= '</div>';
        }

        // Close row wrapper
        if ( $needs_wrapper ) {
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * Build and run a WP_Query for row loop mode.
     *
     * @param array $s  Row settings containing loop_* keys.
     * @return WP_Post[]  Array of post objects, or empty array.
     */
    private function run_row_loop_query( $s ) {
        $post_type = sanitize_key( $s['loop_post_type'] ?? 'post' );
        if ( ! post_type_exists( $post_type ) ) {
            $post_type = 'post';
        }

        $args = [
            'post_type'      => $post_type,
            'posts_per_page' => absint( $s['loop_posts_per_page'] ?? 6 ),
            'orderby'        => sanitize_key( $s['loop_orderby'] ?? 'date' ),
            'order'          => strtoupper( $s['loop_order'] ?? 'DESC' ) === 'ASC' ? 'ASC' : 'DESC',
            'post_status'    => 'publish',
        ];

        // Offset
        $offset = absint( $s['loop_offset'] ?? 0 );
        if ( $offset > 0 ) {
            $args['offset'] = $offset;
        }

        // Exclude current post
        if ( ! empty( $s['loop_exclude_current'] ) ) {
            $current_id = get_the_ID();
            if ( $current_id ) {
                $args['post__not_in'] = [ $current_id ];
            }
        }

        // Taxonomy include filter
        $taxonomy  = sanitize_text_field( $s['loop_taxonomy'] ?? '' );
        $terms_str = sanitize_text_field( $s['loop_terms'] ?? '' );
        $tax_query = [];
        if ( $taxonomy !== '' ) {
            if ( $terms_str !== '' ) {
                $term_slugs = array_map( 'trim', explode( ',', $terms_str ) );
                $tax_query[] = [
                    'taxonomy' => $taxonomy,
                    'field'    => 'slug',
                    'terms'    => $term_slugs,
                    'operator' => 'IN',
                ];
            }
            // Taxonomy exclude filter
            $terms_exclude = sanitize_text_field( $s['loop_terms_exclude'] ?? '' );
            if ( $terms_exclude !== '' ) {
                $exclude_slugs = array_map( 'trim', explode( ',', $terms_exclude ) );
                $tax_query[] = [
                    'taxonomy' => $taxonomy,
                    'field'    => 'slug',
                    'terms'    => $exclude_slugs,
                    'operator' => 'NOT IN',
                ];
            }
            if ( count( $tax_query ) > 1 ) {
                $tax_query['relation'] = 'AND';
            }
        }
        if ( ! empty( $tax_query ) ) {
            $args['tax_query'] = $tax_query;
        }

        // Meta query
        $meta_key = sanitize_text_field( $s['loop_meta_key'] ?? '' );
        if ( $meta_key !== '' ) {
            $meta_value   = sanitize_text_field( $s['loop_meta_value'] ?? '' );
            $meta_compare = $s['loop_meta_compare'] ?? '=';
            $valid_cmp    = [ '=', '!=', '>', '<', 'LIKE', 'EXISTS', 'NOT EXISTS' ];
            if ( ! in_array( $meta_compare, $valid_cmp, true ) ) $meta_compare = '=';

            $mq = [
                'key'     => $meta_key,
                'compare' => $meta_compare,
            ];
            if ( ! in_array( $meta_compare, [ 'EXISTS', 'NOT EXISTS' ], true ) ) {
                $mq['value'] = $meta_value;
            }
            $args['meta_query'] = [ $mq ];

            // Orderby meta
            $orderby = $s['loop_orderby'] ?? 'date';
            if ( in_array( $orderby, [ 'meta_value', 'meta_value_num' ], true ) ) {
                $args['meta_key'] = $meta_key;
            }
        }

        $query = new WP_Query( $args );
        return $query->posts;
    }

    /**
     * Render a Column using UIkit width classes.
     */
    private function render_column_node( $node, $manager, $template_id, &$hover_css_rules, &$tile_counter, $parent_is_grid = false ) {
        $s        = $node['settings'] ?? [];
        $style    = $node['style'] ?? [];
        $advanced = $node['advanced'] ?? [];

        $classes = [];
        $inline_styles = [];

        if ( $parent_is_grid ) {
            // === CSS Grid cell: use grid-column / grid-row placement ===
            if ( ! empty( $s['grid_column'] ) ) {
                $inline_styles[] = 'grid-column: ' . esc_attr( $s['grid_column'] );
            }
            if ( ! empty( $s['grid_row'] ) ) {
                $inline_styles[] = 'grid-row: ' . esc_attr( $s['grid_row'] );
            }
            $inline_styles[] = 'min-width: 0';
        } else {
            // === Classic Flexbox: UIkit width classes ===
            $width_custom  = $s['width_custom'] ?? '';
            $width_default = $s['width_default'] ?? '';
            $width_small   = $s['width_small'] ?? '';
            $width_medium  = $s['width_medium'] ?? '';
            $width_large   = $s['width_large'] ?? '';

            if ( $width_custom !== '' && floatval( $width_custom ) > 0 ) {
                $classes[] = 'uk-width-1-1';
            } else {
                if ( $width_default && isset( $this->fraction_map[ $width_default ] ) ) {
                    $classes[] = 'uk-width-' . $width_default;
                }
                if ( $width_small && isset( $this->fraction_map[ $width_small ] ) ) {
                    $classes[] = 'uk-width-' . $width_small . '@s';
                }
                if ( $width_medium && isset( $this->fraction_map[ $width_medium ] ) ) {
                    $classes[] = 'uk-width-' . $width_medium . '@m';
                }
                if ( $width_large && isset( $this->fraction_map[ $width_large ] ) ) {
                    $classes[] = 'uk-width-' . $width_large . '@l';
                }

                if ( empty( $classes ) ) {
                    $classes[] = 'uk-width-expand';
                }
            }
        }

        // Column margin/padding
        if ( ! empty( $style['margin_top'] ) )    $inline_styles[] = "margin-top: {$style['margin_top']}px";
        if ( ! empty( $style['margin_right'] ) )  $inline_styles[] = "margin-right: {$style['margin_right']}px";
        if ( ! empty( $style['margin_bottom'] ) ) $inline_styles[] = "margin-bottom: {$style['margin_bottom']}px";
        if ( ! empty( $style['margin_left'] ) )   $inline_styles[] = "margin-left: {$style['margin_left']}px";
        if ( ! empty( $style['padding_top'] ) )    $inline_styles[] = "padding-top: {$style['padding_top']}px";
        if ( ! empty( $style['padding_right'] ) )  $inline_styles[] = "padding-right: {$style['padding_right']}px";
        if ( ! empty( $style['padding_bottom'] ) ) $inline_styles[] = "padding-bottom: {$style['padding_bottom']}px";
        if ( ! empty( $style['padding_left'] ) )   $inline_styles[] = "padding-left: {$style['padding_left']}px";

        // Border radius
        if ( ! empty( $style['border_radius'] ) )  $inline_styles[] = $this->build_border_radius_css( $style['border_radius'] );

        // Border
        if ( ! empty( $style['border_width'] ) && intval( $style['border_width'] ) > 0 ) {
            $bw = intval( $style['border_width'] );
            $bs = $style['border_style'] ?? 'solid';
            $bc = $style['border_color'] ?? '#374151';
            $inline_styles[] = "border: {$bw}px {$bs} {$bc}";
        }

        // Shadow
        if ( ! empty( $style['shadow'] ) ) {
            $uk_shadow_map = [
                'sm' => 'uk-box-shadow-small',
                'md' => 'uk-box-shadow-medium',
                'lg' => 'uk-box-shadow-large',
                'xl' => 'uk-box-shadow-xlarge',
            ];
            if ( isset( $uk_shadow_map[ $style['shadow'] ] ) ) {
                $classes[] = $uk_shadow_map[ $style['shadow'] ];
            }
        }

        // Opacity
        if ( ! empty( $style['opacity'] ) && intval( $style['opacity'] ) < 100 ) {
            $opacity = intval( $style['opacity'] ) / 100;
            $inline_styles[] = "opacity: {$opacity}";
        }

        // CSS Transform (normal state)
        $transform_css = $this->build_transform_css( $style );
        if ( $transform_css ) {
            foreach ( $transform_css as $decl ) {
                $inline_styles[] = $decl;
            }
        }

        // Box shadow (segue border-radius del div)
        $box_shadow = $this->build_box_shadow_css( $style );
        if ( $box_shadow ) {
            $inline_styles[] = $box_shadow;
        }

        // Text shadow
        $text_shadow = $this->build_text_shadow_css( $style );
        if ( $text_shadow ) {
            $inline_styles[] = $text_shadow;
        }

        // Backdrop filter
        $backdrop = $this->build_backdrop_filter_css( $style );
        if ( $backdrop ) {
            foreach ( $backdrop as $decl ) {
                $inline_styles[] = $decl;
            }
        }

        // Overflow
        if ( ! empty( $style['overflow'] ) && $style['overflow'] !== 'visible' ) {
            $inline_styles[] = 'overflow: ' . esc_attr( $style['overflow'] );
        }

        // Mask
        $mask_css = $this->build_mask_css( $style );
        if ( $mask_css ) {
            foreach ( $mask_css as $decl ) {
                $inline_styles[] = $decl;
            }
        }

        // Background handling for column
        $col_bg      = $this->get_effective_bg( $style );
        $has_col_bg_image = ( $col_bg['type'] === 'image' && ! empty( $col_bg['image_url'] ) );
        $has_col_bg_video = ( $col_bg['type'] === 'video' && ! empty( $col_bg['video_url'] ) );
        $has_col_bg_any   = ( $col_bg['type'] !== 'none' );
        $has_col_overlay  = ( $has_col_bg_any && ! empty( $col_bg['overlay_opacity'] ) && intval( $col_bg['overlay_opacity'] ) > 0 );

        if ( ! $has_col_bg_image && ! $has_col_bg_video && $col_bg['type'] !== 'none' ) {
            $bg_css = $this->get_bg_inline_css( $col_bg );
            if ( $bg_css ) $inline_styles[] = $bg_css;
        }
        if ( $has_col_bg_image || $has_col_bg_video ) {
            $classes[] = 'uk-position-relative';
            $inline_styles[] = 'overflow: clip';
        }

        // Positioning (absolute/fixed/relative) for columns
        $pos_mode = $advanced['position_mode'] ?? 'static';
        if ( $pos_mode && $pos_mode !== 'static' ) {
            $inline_styles[] = 'position: ' . esc_attr( $pos_mode );
            foreach ( [ 'top', 'left', 'bottom', 'right' ] as $dir ) {
                $val = $advanced[ 'position_' . $dir ] ?? '';
                if ( $val !== '' ) {
                    $inline_styles[] = $dir . ': ' . ( is_numeric( $val ) ? $val . 'px' : esc_attr( $val ) );
                }
            }
            $w = $advanced['position_width'] ?? '';
            if ( $w !== '' ) {
                $inline_styles[] = 'width: ' . ( is_numeric( $w ) ? $w . 'px' : esc_attr( $w ) );
            }
            $z = $advanced['position_zindex'] ?? '';
            if ( $z !== '' ) {
                $inline_styles[] = 'z-index: ' . intval( $z );
            }
        }

        // ID for hover CSS support
        $tile_counter++;
        $col_css_id = ! empty( $advanced['html_id'] ) ? $advanced['html_id'] : 'mc-' . $template_id . '-' . $tile_counter;

        // Hover CSS rules
        $this->collect_hover_css( $style, $col_css_id, false, $hover_css_rules );
        $this->collect_responsive_css( $style, $col_css_id, $advanced );

        // Custom CSS per colonna (campo settings.custom_css)
        $this->collect_custom_css( $s, $col_css_id, $hover_css_rules );

        // Entrance animation (olo-entrance-*) for column
        $entrance = $s['entrance_animation'] ?? 'none';
        if ( $entrance && $entrance !== 'none' ) {
            $classes[] = 'olo-entrance-' . sanitize_html_class( $entrance );
            if ( ! empty( $s['entrance_stagger'] ) ) {
                $stagger_delay = intval( $s['entrance_stagger_delay'] ?? 100 );
                $stagger_delay = max( 25, min( 500, $stagger_delay ) );
                $classes[] = 'olo-stagger-parent';
                $inline_styles[] = '--olo-stagger-delay: ' . $stagger_delay . 'ms';
            }
        }

        // Scrollspy & element parallax attributes for column
        $col_scrollspy_attr = $this->build_scrollspy_attr( $advanced );
        $col_el_parallax_attr = $this->build_element_parallax_attr( $advanced );

        $html = '<div id="' . esc_attr( $col_css_id ) . '" class="' . esc_attr( implode( ' ', $classes ) ) . '"';
        if ( ! empty( $inline_styles ) ) {
            $html .= ' style="' . esc_attr( implode( '; ', $inline_styles ) ) . '"';
        }
        $html .= $col_scrollspy_attr . $col_el_parallax_attr . '>';

        // Background image cover for column
        if ( $has_col_bg_image ) {
            $bg_size = esc_attr( $col_bg['image_size'] ?? 'cover' );
            $bg_pos  = esc_attr( $col_bg['image_position'] ?? 'center center' );
            $html .= '<div class="uk-position-cover" style="background-image: url(' . esc_url( $col_bg['image_url'] ) . '); background-size: ' . $bg_size . '; background-position: ' . $bg_pos . '; background-repeat: no-repeat"';
            $html .= $this->build_uk_parallax_attr( $col_bg );
            $html .= '></div>';
        }
        // Background video cover for column
        if ( $has_col_bg_video ) {
            $vid_url    = esc_url( $col_bg['video_url'] );
            $vid_poster = ! empty( $col_bg['video_poster'] ) ? esc_url( $col_bg['video_poster'] ) : '';
            $vid_fit    = esc_attr( $col_bg['video_fit'] ?? 'cover' );
            $vid_pos    = esc_attr( $col_bg['image_position'] ?? 'center center' );
            $html .= '<video aria-hidden="true" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: ' . $vid_fit . '; object-position: ' . $vid_pos . '; pointer-events: none" autoplay muted loop playsinline';
            if ( $vid_poster ) $html .= ' poster="' . $vid_poster . '"';
            $html .= '><source src="' . $vid_url . '" type="' . $this->get_video_mime( $vid_url ) . '"></video>';
        }
        // Overlay for column
        if ( $has_col_overlay ) {
            $ov_color   = esc_attr( $col_bg['overlay_color'] ?? '#000000' );
            $ov_opacity = intval( $col_bg['overlay_opacity'] ) / 100;
            $html .= '<div class="uk-position-cover" style="background-color: ' . $ov_color . '; opacity: ' . $ov_opacity . '; pointer-events: none" aria-hidden="true"></div>';
        }

        // Column children content (z-index above bg if needed)
        if ( $has_col_bg_image || $has_col_bg_video || $has_col_overlay ) {
            $html .= '<div style="position: relative; z-index: 1">';
        }
        foreach ( $node['children'] ?? [] as $child ) {
            $html .= $this->render_node( $child, $manager, $template_id, $hover_css_rules, $tile_counter );
        }
        if ( $has_col_bg_image || $has_col_bg_video || $has_col_overlay ) {
            $html .= '</div>';
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Render an inner-columns container (flex row with sub-columns).
     */
    private function render_inner_columns_node( $node, $manager, $template_id, &$hover_css_rules, &$tile_counter ) {
        $s        = $node['settings'] ?? [];
        $style    = $node['style'] ?? [];
        $advanced = $node['advanced'] ?? [];
        $gap      = absint( $s['gap'] ?? 16 );
        $valign   = $s['vertical_align'] ?? 'stretch';
        $stack    = ! empty( $s['stack_mobile'] );

        $align_css = $this->align_map[ $valign ] ?? 'stretch';

        $inline_styles = [
            'display: flex',
            'gap: ' . $gap . 'px',
            'align-items: ' . $align_css,
        ];

        if ( ! $stack ) {
            $inline_styles[] = 'flex-wrap: nowrap';
        } else {
            $inline_styles[] = 'flex-wrap: wrap';
        }

        // Margin & Padding from style tab
        if ( ! empty( $style['margin_top'] ) )    $inline_styles[] = "margin-top: {$style['margin_top']}px";
        if ( ! empty( $style['margin_right'] ) )  $inline_styles[] = "margin-right: {$style['margin_right']}px";
        if ( ! empty( $style['margin_bottom'] ) ) $inline_styles[] = "margin-bottom: {$style['margin_bottom']}px";
        if ( ! empty( $style['margin_left'] ) )   $inline_styles[] = "margin-left: {$style['margin_left']}px";
        if ( ! empty( $style['padding_top'] ) )    $inline_styles[] = "padding-top: {$style['padding_top']}px";
        if ( ! empty( $style['padding_right'] ) )  $inline_styles[] = "padding-right: {$style['padding_right']}px";
        if ( ! empty( $style['padding_bottom'] ) ) $inline_styles[] = "padding-bottom: {$style['padding_bottom']}px";
        if ( ! empty( $style['padding_left'] ) )   $inline_styles[] = "padding-left: {$style['padding_left']}px";

        // Background
        $tile_bg = $this->get_effective_bg( $style );
        if ( $tile_bg['type'] !== 'none' && $tile_bg['type'] !== 'image' && $tile_bg['type'] !== 'video' ) {
            $bg_css = $this->get_bg_inline_css( $tile_bg );
            if ( $bg_css ) $inline_styles[] = $bg_css;
        }

        // Border radius
        if ( ! empty( $style['border_radius'] ) ) $inline_styles[] = $this->build_border_radius_css( $style['border_radius'] );

        // Border
        if ( ! empty( $style['border_width'] ) && intval( $style['border_width'] ) > 0 ) {
            $bw = intval( $style['border_width'] );
            $bs = $style['border_style'] ?? 'solid';
            $bc = $style['border_color'] ?? '#374151';
            $inline_styles[] = "border: {$bw}px {$bs} {$bc}";
        }

        $classes = [ 'olo-inner-columns' ];
        if ( ! empty( $advanced['css_classes'] ) ) {
            $classes[] = esc_attr( $advanced['css_classes'] );
        }

        // ID for hover CSS support
        $tile_counter++;
        $ic_css_id = ! empty( $advanced['html_id'] ) ? $advanced['html_id'] : 'mic-' . $template_id . '-' . $tile_counter;

        // Hover CSS rules
        $this->collect_hover_css( $style, $ic_css_id, false, $hover_css_rules );
        $this->collect_responsive_css( $style, $ic_css_id );

        $html = '';

        // Stack on mobile: responsive CSS
        if ( $stack ) {
            $ic_class = 'olo-ic-' . substr( md5( $node['id'] ?? wp_rand() ), 0, 6 );
            $classes[] = $ic_class;
            $html .= '<style>@container olo-tpl (max-width:640px){.' . $ic_class . '{flex-direction:column}.' . $ic_class . '>*{width:100%!important}}</style>';
        }

        $html .= '<div id="' . esc_attr( $ic_css_id ) . '" class="' . esc_attr( implode( ' ', $classes ) ) . '" style="' . esc_attr( implode( '; ', $inline_styles ) ) . '">';

        foreach ( $node['children'] ?? [] as $child ) {
            $html .= $this->render_node( $child, $manager, $template_id, $hover_css_rules, $tile_counter );
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Render an inner-column (single sub-column within inner-columns).
     */
    private function render_inner_column_node( $node, $manager, $template_id, &$hover_css_rules, &$tile_counter ) {
        $s        = $node['settings'] ?? [];
        $style    = $node['style'] ?? [];
        $advanced = $node['advanced'] ?? [];

        $width = floatval( $s['width'] ?? 50 );

        $inline_styles = [
            'width: ' . $width . '%',
            'min-width: 0',
            'box-sizing: border-box',
        ];

        // Margin & Padding
        if ( ! empty( $style['margin_top'] ) )    $inline_styles[] = "margin-top: {$style['margin_top']}px";
        if ( ! empty( $style['margin_right'] ) )  $inline_styles[] = "margin-right: {$style['margin_right']}px";
        if ( ! empty( $style['margin_bottom'] ) ) $inline_styles[] = "margin-bottom: {$style['margin_bottom']}px";
        if ( ! empty( $style['margin_left'] ) )   $inline_styles[] = "margin-left: {$style['margin_left']}px";
        if ( ! empty( $style['padding_top'] ) )    $inline_styles[] = "padding-top: {$style['padding_top']}px";
        if ( ! empty( $style['padding_right'] ) )  $inline_styles[] = "padding-right: {$style['padding_right']}px";
        if ( ! empty( $style['padding_bottom'] ) ) $inline_styles[] = "padding-bottom: {$style['padding_bottom']}px";
        if ( ! empty( $style['padding_left'] ) )   $inline_styles[] = "padding-left: {$style['padding_left']}px";

        // Background
        $tile_bg = $this->get_effective_bg( $style );
        if ( $tile_bg['type'] !== 'none' && $tile_bg['type'] !== 'image' && $tile_bg['type'] !== 'video' ) {
            $bg_css = $this->get_bg_inline_css( $tile_bg );
            if ( $bg_css ) $inline_styles[] = $bg_css;
        }

        // Border radius
        if ( ! empty( $style['border_radius'] ) ) $inline_styles[] = $this->build_border_radius_css( $style['border_radius'] );

        // Border
        if ( ! empty( $style['border_width'] ) && intval( $style['border_width'] ) > 0 ) {
            $bw = intval( $style['border_width'] );
            $bs = $style['border_style'] ?? 'solid';
            $bc = $style['border_color'] ?? '#374151';
            $inline_styles[] = "border: {$bw}px {$bs} {$bc}";
        }

        // Sticky column support
        if ( ! empty( $s['sticky'] ) ) {
            $sticky_offset = intval( $s['sticky_offset'] ?? 20 );
            $inline_styles[] = 'position: sticky';
            $inline_styles[] = 'top: ' . $sticky_offset . 'px';
            $inline_styles[] = 'align-self: flex-start';
        }

        // ID for hover CSS support
        $tile_counter++;
        $icol_css_id = ! empty( $advanced['html_id'] ) ? $advanced['html_id'] : 'mci-' . $template_id . '-' . $tile_counter;

        // Hover CSS rules
        $this->collect_hover_css( $style, $icol_css_id, false, $hover_css_rules );
        $this->collect_responsive_css( $style, $icol_css_id );

        $html = '<div id="' . esc_attr( $icol_css_id ) . '" class="olo-inner-column" style="' . esc_attr( implode( '; ', $inline_styles ) ) . '">';

        foreach ( $node['children'] ?? [] as $child ) {
            $html .= $this->render_node( $child, $manager, $template_id, $hover_css_rules, $tile_counter );
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Render a floating panel container node.
     * Uses the tile's render() for the opening wrapper, then injects children, then render_closing().
     */
    private function render_floatingpanel_node( $node, $manager, $template_id, &$hover_css_rules, &$tile_counter ) {
        $tile_instance = $manager->get_tile( 'floatingpanel' );
        if ( ! $tile_instance ) return '';

        $settings = $node['settings'] ?? [];

        // Render opening wrapper (panel div with styles, trigger button, close button)
        $html = $tile_instance->render( $settings );

        // Render children inside the panel
        foreach ( $node['children'] ?? [] as $child ) {
            $html .= $this->render_node( $child, $manager, $template_id, $hover_css_rules, $tile_counter );
        }

        // Render closing wrapper + JS
        $html .= $tile_instance->render_closing( $settings );

        return $html;
    }

    /**
     * Check if a section node contains only a single floatingpanel tile
     * (inside row > column), with no other content.
     */
    private function section_has_only_floatingpanel( $node ) {
        $rows = $node['children'] ?? [];
        if ( count( $rows ) !== 1 ) return false;

        $row = $rows[0];
        if ( ( $row['type'] ?? '' ) !== 'row' ) return false;

        $cols = $row['children'] ?? [];
        if ( count( $cols ) !== 1 ) return false;

        $col = $cols[0];
        if ( ( $col['type'] ?? '' ) !== 'column' ) return false;

        $tiles = $col['children'] ?? [];
        if ( count( $tiles ) !== 1 ) return false;

        return ( $tiles[0]['type'] ?? '' ) === 'floatingpanel';
    }

    /**
     * Extract and render only the floatingpanel from a section>row>column structure,
     * skipping all parent wrappers to avoid empty section gap.
     */
    private function extract_and_render_floatingpanel( $node, $manager, $template_id, &$hover_css_rules, &$tile_counter ) {
        $fp_node = $node['children'][0]['children'][0]['children'][0];
        return $this->render_floatingpanel_node( $fp_node, $manager, $template_id, $hover_css_rules, $tile_counter );
    }

    /**
     * Render an element (leaf tile) with full wrapper (bg, margin, padding, hover).
     * Uses UIkit utility classes where possible.
     */
    /**
     * Map element type to its items key in settings.
     */
    private function get_items_key( $type ) {
        $map = [
            'accordion'     => 'panels',
            'panelslider'   => 'panels',
            'slideshow'     => 'slides',
            'overlayslider' => 'slides',
            'popover'       => 'markers',
        ];
        return $map[ $type ] ?? 'items';
    }

    private function render_element_node( $node, $manager, $template_id, &$hover_css_rules, &$tile_counter ) {
        // Resolve global widget
        if ( ! empty( $node['global_id'] ) ) {
            global $wpdb;
            $gw = $wpdb->get_row( $wpdb->prepare(
                "SELECT tile_data FROM {$wpdb->prefix}olo_global_widgets WHERE id = %d",
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
        $tile_instance = $manager->get_tile( $type );
        if ( ! $tile_instance ) return '';

        $settings = $node['settings'] ?? [];
        $style    = $node['style'] ?? [];
        $advanced = $node['advanced'] ?? [];

        // Conditional visibility check — skip rendering entirely if condition not met
        if ( ! $this->check_conditions( $settings ) ) {
            return '';
        }

        // A/B Testing: check if this tile has an active test
        $ab_test_data = null;
        $tile_id = $node['id'] ?? '';
        if ( class_exists( 'Olo_AB_Testing' ) ) {
            if ( ! empty( $tile_id ) ) {
                if ( ! empty( $template_id ) ) {
                    $ab_test_data = Olo_AB_Testing::get_active_test_for_tile( $tile_id, $template_id );
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
            $dc = new Olo_Dynamic_Content();
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

        $tile_bg      = $this->get_effective_bg( $style );
        $is_fullwidth = ! empty( $style['full_width'] );
        $has_bg_image = ( $tile_bg['type'] === 'image' && ! empty( $tile_bg['image_url'] ) );
        $has_bg_video = ( $tile_bg['type'] === 'video' && ! empty( $tile_bg['video_url'] ) );
        $has_bg_any   = ( $tile_bg['type'] !== 'none' );
        $has_overlay  = ( $has_bg_any && ! empty( $tile_bg['overlay_opacity'] ) && intval( $tile_bg['overlay_opacity'] ) > 0 );

        // Build inline styles (custom values that UIkit can't handle)
        $inline_styles = [];

        if ( ! empty( $style['margin_top'] ) )    $inline_styles[] = "margin-top: {$style['margin_top']}px";
        if ( ! empty( $style['margin_right'] ) )  $inline_styles[] = "margin-right: {$style['margin_right']}px";
        if ( ! empty( $style['margin_bottom'] ) ) $inline_styles[] = "margin-bottom: {$style['margin_bottom']}px";
        if ( ! empty( $style['margin_left'] ) )   $inline_styles[] = "margin-left: {$style['margin_left']}px";

        if ( ! empty( $style['padding_top'] ) )    $inline_styles[] = "padding-top: {$style['padding_top']}px";
        if ( ! empty( $style['padding_right'] ) )  $inline_styles[] = "padding-right: {$style['padding_right']}px";
        if ( ! empty( $style['padding_bottom'] ) ) $inline_styles[] = "padding-bottom: {$style['padding_bottom']}px";
        if ( ! empty( $style['padding_left'] ) )   $inline_styles[] = "padding-left: {$style['padding_left']}px";

        if ( ! $has_bg_image && ! $has_bg_video ) {
            $bg_css = $this->get_bg_inline_css( $tile_bg );
            if ( $bg_css ) $inline_styles[] = $bg_css;
        }

        if ( ! empty( $style['border_radius'] ) )  $inline_styles[] = $this->build_border_radius_css( $style['border_radius'] );

        if ( ! empty( $style['border_width'] ) && intval( $style['border_width'] ) > 0 ) {
            $bw = intval( $style['border_width'] );
            $bs = $style['border_style'] ?? 'solid';
            $bc = $style['border_color'] ?? '#374151';
            $inline_styles[] = "border: {$bw}px {$bs} {$bc}";
        }

        // UIkit shadow classes — solo per elementi con sfondo (box-shadow segue border-radius)
        // Per elementi trasparenti il drop-shadow viene applicato via inline filter
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

        if ( ! empty( $style['opacity'] ) && intval( $style['opacity'] ) < 100 ) {
            $opacity = intval( $style['opacity'] ) / 100;
            $inline_styles[] = "opacity: {$opacity}";
        }

        // Flex container settings (for elements that use flexContainerFields)
        $flex_css = $this->build_flex_container_css( $settings );
        foreach ( $flex_css as $decl ) {
            $inline_styles[] = $decl;
        }

        // CSS Transform (normal state)
        $transform_css = $this->build_transform_css( $style );
        if ( $transform_css ) {
            foreach ( $transform_css as $decl ) {
                $inline_styles[] = $decl;
            }
        }

        // Shadow — box-shadow per elementi con sfondo (segue border-radius),
        // filter: drop-shadow per elementi trasparenti (segue forma del contenuto: SVG, icone)
        if ( $has_bg_any ) {
            $box_shadow = $this->build_box_shadow_css( $style );
            if ( $box_shadow ) {
                $inline_styles[] = $box_shadow;
            }
        } else {
            $drop_shadow = $this->build_drop_shadow_css( $style );
            if ( $drop_shadow ) {
                $inline_styles[] = 'filter: ' . $drop_shadow;
            }
        }

        // Text shadow
        $text_shadow = $this->build_text_shadow_css( $style );
        if ( $text_shadow ) {
            $inline_styles[] = $text_shadow;
        }

        // Backdrop filter
        $backdrop = $this->build_backdrop_filter_css( $style );
        if ( $backdrop ) {
            foreach ( $backdrop as $decl ) {
                $inline_styles[] = $decl;
            }
        }

        // Overflow
        if ( ! empty( $style['overflow'] ) && $style['overflow'] !== 'visible' ) {
            $inline_styles[] = 'overflow: ' . esc_attr( $style['overflow'] );
        }

        // Mask
        $mask_css = $this->build_mask_css( $style );
        if ( $mask_css ) {
            foreach ( $mask_css as $decl ) {
                $inline_styles[] = $decl;
            }
        }

        if ( ! empty( $advanced['custom_css'] ) ) {
            $inline_styles[] = $advanced['custom_css'];
        }

        // Positioning (absolute/fixed/relative)
        $pos_mode = $advanced['position_mode'] ?? 'static';
        if ( $pos_mode && $pos_mode !== 'static' ) {
            $inline_styles[] = 'position: ' . esc_attr( $pos_mode );
            foreach ( [ 'top', 'left', 'bottom', 'right' ] as $dir ) {
                $val = $advanced[ 'position_' . $dir ] ?? '';
                if ( $val !== '' ) {
                    $inline_styles[] = $dir . ': ' . ( is_numeric( $val ) ? $val . 'px' : esc_attr( $val ) );
                }
            }
            $w = $advanced['position_width'] ?? '';
            if ( $w !== '' ) {
                $inline_styles[] = 'width: ' . ( is_numeric( $w ) ? $w . 'px' : esc_attr( $w ) );
            }
            $z = $advanced['position_zindex'] ?? '';
            if ( $z !== '' ) {
                $inline_styles[] = 'z-index: ' . intval( $z );
            }
        }

        $style_attr = implode( '; ', $inline_styles );

        // Build classes
        $classes = [ 'olo-frontend-tile' ];
        if ( $shadow_class ) $classes[] = $shadow_class;
        if ( $is_fullwidth ) $classes[] = 'olo-tile-fullwidth';
        if ( $has_bg_image || $has_bg_video || $has_bg_gallery || $has_overlay ) { $classes[] = 'uk-position-relative'; $inline_styles[] = 'overflow: clip'; }
        if ( ! empty( $style['border_radius'] ) ) $inline_styles[] = 'overflow: clip';
        if ( ! empty( $advanced['css_classes'] ) ) {
            $classes[] = esc_attr( $advanced['css_classes'] );
        }

        // Entrance animation
        $entrance = $settings['entrance_animation'] ?? 'none';
        if ( $entrance && $entrance !== 'none' ) {
            $classes[] = 'olo-entrance-' . sanitize_html_class( $entrance );
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

        // Scrollspy & element parallax attributes (skip for fixed tiles — handled by sentinel JS)
        $elem_scrollspy_attr = ( $pos_mode === 'fixed' ) ? '' : $this->build_scrollspy_attr( $advanced );
        $elem_el_parallax_attr = $this->build_element_parallax_attr( $advanced );

        // Sticky attribute (UIkit uk-sticky) — skip if tile is already fixed-positioned
        // Also skip for megamenu tiles — they handle header stickiness via their own JS
        $elem_sticky_attr = '';
        if ( ! empty( $settings['sticky'] ) && $pos_mode !== 'fixed' && $type !== 'megamenu' ) {
            $sticky_pos    = esc_attr( $settings['sticky_position'] ?? 'top' );
            $sticky_offset = intval( $settings['sticky_offset'] ?? 0 );
            $sticky_mobile = $settings['sticky_on_mobile'] ?? true;
            $sticky_media  = $sticky_mobile ? '' : '; media: @s';
            $elem_sticky_attr = ' uk-sticky="position: ' . $sticky_pos . '; offset: ' . $sticky_offset . $sticky_media . '"';
        }

        // Mouse effects data attributes
        $elem_mouse_attrs = '';
        if ( ! empty( $settings['mouse_tilt'] ) ) {
            $tilt_intensity = intval( $settings['mouse_tilt_intensity'] ?? 15 );
            $elem_mouse_attrs .= ' data-olo-tilt="' . $tilt_intensity . '"';
        }
        if ( ! empty( $settings['mouse_track'] ) ) {
            $track_speed = intval( $settings['mouse_track_speed'] ?? 3 );
            $elem_mouse_attrs .= ' data-olo-track="' . $track_speed . '"';
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
        if ( ! empty( $scroll_fx ) ) {
            $elem_scroll_fx_attr = " data-olo-scroll-fx='" . esc_attr( wp_json_encode( $scroll_fx ) ) . "'";
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
        do_action( 'olo_before_tile_render', $node, $settings, $type );

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
                if ( strpos( $pair, '=' ) !== false ) {
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
        <div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php echo $id_attr; ?> style="<?php echo esc_attr( $style_attr ); ?>"<?php echo $elem_scrollspy_attr . $elem_el_parallax_attr . $elem_sticky_attr . $elem_mouse_attrs . $elem_scroll_fx_attr . $ab_test_attrs . $seo_attrs; ?>>
            <?php if ( $has_bg_image ) :
                $bg_size = esc_attr( $tile_bg['image_size'] ?? 'cover' );
                $bg_pos  = esc_attr( $tile_bg['image_position'] ?? 'center center' );
                $parallax_attr = $this->build_uk_parallax_attr( $tile_bg );
            ?>
                <div class="uk-position-cover"
                    style="background-image: url(<?php echo esc_url( $tile_bg['image_url'] ); ?>); background-size: <?php echo $bg_size; ?>; background-position: <?php echo $bg_pos; ?>; background-repeat: no-repeat"
                    <?php echo $parallax_attr; ?>
                ></div>
            <?php endif; ?>

            <?php if ( $has_bg_video ) :
                $vid_url    = esc_url( $tile_bg['video_url'] );
                $vid_poster = ! empty( $tile_bg['video_poster'] ) ? esc_url( $tile_bg['video_poster'] ) : '';
                $vid_pos    = esc_attr( $tile_bg['image_position'] ?? 'center center' );
                $vid_fit    = esc_attr( $tile_bg['video_fit'] ?? 'cover' );
            ?>
                <video aria-hidden="true" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: <?php echo $vid_fit; ?>; object-position: <?php echo $vid_pos; ?>; pointer-events: none" autoplay muted loop playsinline<?php if ( $vid_poster ) echo ' poster="' . $vid_poster . '"'; ?>><source src="<?php echo $vid_url; ?>" type="<?php echo esc_attr( $this->get_video_mime( $vid_url ) ); ?>"></video>
            <?php endif; ?>

            <?php if ( $has_overlay ) :
                $ov_color   = esc_attr( $tile_bg['overlay_color'] ?? '#000000' );
                $ov_opacity = intval( $tile_bg['overlay_opacity'] ) / 100;
            ?>
                <div class="uk-position-cover" style="background-color: <?php echo $ov_color; ?>; opacity: <?php echo $ov_opacity; ?>; pointer-events: none" aria-hidden="true"></div>
            <?php endif; ?>

            <?php if ( $has_bg_image || $has_bg_video || $has_bg_gallery || $has_overlay ) : ?>
                <div class="uk-position-relative" style="z-index: 1">
                    <?php echo $tile_instance->render( $settings ); ?>
                </div>
            <?php else : ?>
                <?php echo $tile_instance->render( $settings ); ?>
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
        do_action( 'olo_after_tile_render', $node, $settings, $type, $html );

        // Accessibility: ARIA enhancements
        $html = apply_filters( 'olo_tile_output', $html, $type, $settings );

        // Resolve inline dynamic tokens ({post_title}, {current_year}, etc.)
        $html = Olo_Dynamic_Content::resolve_tokens( $html );
        return $html;
    }

    /**
     * Collect hover CSS rules for an element.
     */
    private function collect_hover_css( $style, $css_id, $is_fullwidth, &$hover_css_rules, $advanced = [] ) {
        $hover = $style['hover'] ?? [];
        $transition_cfg = $style['transition'] ?? [];
        $has_hover = false;
        $hover_decls = [];

        if ( ! empty( $hover['bg_color'] ) ) {
            $hover_decls[] = 'background-color: ' . esc_attr( $hover['bg_color'] );
            $has_hover = true;
        }
        if ( ! empty( $hover['text_color'] ) ) {
            $hover_decls[] = 'color: ' . esc_attr( $hover['text_color'] );
            $has_hover = true;
        }
        if ( ! empty( $hover['border_color'] ) ) {
            $hover_decls[] = 'border-color: ' . esc_attr( $hover['border_color'] );
            $has_hover = true;
        }
        if ( isset( $hover['border_radius'] ) && $hover['border_radius'] !== '' && $hover['border_radius'] !== null ) {
            $br = $hover['border_radius'];
            if ( is_array( $br ) ) {
                $hover_decls[] = sprintf( 'border-radius: %dpx %dpx %dpx %dpx',
                    intval( $br['tl'] ?? 0 ), intval( $br['tr'] ?? 0 ),
                    intval( $br['br'] ?? 0 ), intval( $br['bl'] ?? 0 ) );
            } else {
                $hover_decls[] = 'border-radius: ' . intval( $br ) . 'px';
            }
            $has_hover = true;
        }
        // Hover shadow — box-shadow
        $hover_drop_shadow = '';
        if ( ! empty( $hover['shadow'] ) ) {
            if ( $hover['shadow'] === 'custom' ) {
                $hh  = intval( $hover['shadow_h'] ?? 0 );
                $hv  = intval( $hover['shadow_v'] ?? 0 );
                $hbl = intval( $hover['shadow_blur'] ?? 0 );
                $hsp = intval( $hover['shadow_spread'] ?? 0 );
                $hco = esc_attr( $hover['shadow_color'] ?? 'rgba(0,0,0,0.15)' );
                $hin = ! empty( $hover['shadow_inset'] ) ? 'inset ' : '';
                $hover_decls[] = "box-shadow: {$hin}{$hh}px {$hv}px {$hbl}px {$hsp}px {$hco}";
                $has_hover = true;
            } elseif ( isset( $this->shadow_map[ $hover['shadow'] ] ) ) {
                $hover_decls[] = 'box-shadow: ' . $this->shadow_map[ $hover['shadow'] ];
                $has_hover = true;
            } elseif ( $hover['shadow'] === 'none' ) {
                $hover_decls[] = 'box-shadow: none';
                $has_hover = true;
            }
        }
        if ( isset( $hover['opacity'] ) && $hover['opacity'] !== null && $hover['opacity'] !== '' ) {
            $hover_decls[] = 'opacity: ' . ( intval( $hover['opacity'] ) / 100 );
            $has_hover = true;
        }

        $h_transforms = [];
        if ( isset( $hover['transform_scale'] ) && $hover['transform_scale'] !== null ) {
            $h_transforms[] = 'scale(' . floatval( $hover['transform_scale'] ) . ')';
            $has_hover = true;
        }
        if ( isset( $hover['transform_translateX'] ) && $hover['transform_translateX'] !== null ) {
            $h_transforms[] = 'translateX(' . intval( $hover['transform_translateX'] ) . 'px)';
            $has_hover = true;
        }
        if ( isset( $hover['transform_translateY'] ) && $hover['transform_translateY'] !== null ) {
            $h_transforms[] = 'translateY(' . intval( $hover['transform_translateY'] ) . 'px)';
            $has_hover = true;
        }
        if ( isset( $hover['transform_rotate'] ) && $hover['transform_rotate'] !== null ) {
            $h_transforms[] = 'rotate(' . intval( $hover['transform_rotate'] ) . 'deg)';
            $has_hover = true;
        }
        if ( isset( $hover['transform_skewX'] ) && $hover['transform_skewX'] !== null ) {
            $h_transforms[] = 'skewX(' . intval( $hover['transform_skewX'] ) . 'deg)';
            $has_hover = true;
        }
        if ( isset( $hover['transform_skewY'] ) && $hover['transform_skewY'] !== null ) {
            $h_transforms[] = 'skewY(' . intval( $hover['transform_skewY'] ) . 'deg)';
            $has_hover = true;
        }
        if ( ! empty( $h_transforms ) ) {
            $hover_decls[] = 'transform: ' . implode( ' ', $h_transforms );
        }

        // Text shadow on hover
        $ts_h_val = $hover['text_shadow_h'] ?? null;
        $ts_v_val = $hover['text_shadow_v'] ?? null;
        $ts_blur_val = $hover['text_shadow_blur'] ?? null;
        if ( ! empty( $hover['text_shadow_enabled'] ) || $ts_h_val !== null || $ts_v_val !== null || $ts_blur_val !== null ) {
            $ts_h     = intval( $ts_h_val ?? 0 );
            $ts_v     = intval( $ts_v_val ?? 0 );
            $ts_blur  = intval( $ts_blur_val ?? 0 );
            if ( $ts_h !== 0 || $ts_v !== 0 || $ts_blur !== 0 ) {
                $ts_color = esc_attr( $hover['text_shadow_color'] ?? 'rgba(0,0,0,0.3)' );
                $hover_decls[] = "text-shadow: {$ts_h}px {$ts_v}px {$ts_blur}px {$ts_color}";
                $has_hover = true;
            }
        }

        // Backdrop filter on hover
        $h_bd_parts = [];
        if ( isset( $hover['backdrop_blur'] ) && intval( $hover['backdrop_blur'] ) != 0 ) {
            $h_bd_parts[] = 'blur(' . intval( $hover['backdrop_blur'] ) . 'px)';
        }
        if ( isset( $hover['backdrop_brightness'] ) && intval( $hover['backdrop_brightness'] ) != 100 ) {
            $h_bd_parts[] = 'brightness(' . intval( $hover['backdrop_brightness'] ) . '%)';
        }
        if ( isset( $hover['backdrop_contrast'] ) && intval( $hover['backdrop_contrast'] ) != 100 ) {
            $h_bd_parts[] = 'contrast(' . intval( $hover['backdrop_contrast'] ) . '%)';
        }
        if ( isset( $hover['backdrop_saturate'] ) && intval( $hover['backdrop_saturate'] ) != 100 ) {
            $h_bd_parts[] = 'saturate(' . intval( $hover['backdrop_saturate'] ) . '%)';
        }
        if ( ! empty( $h_bd_parts ) ) {
            $h_bd_val = implode( ' ', $h_bd_parts );
            $hover_decls[] = '-webkit-backdrop-filter: ' . $h_bd_val;
            $hover_decls[] = 'backdrop-filter: ' . $h_bd_val;
            $has_hover = true;
        }

        // CSS filters on hover — combina con drop-shadow se mask attiva
        $h_filter_parts = [];
        if ( ! empty( $hover_drop_shadow ) ) {
            $h_filter_parts[] = $hover_drop_shadow;
        }
        if ( isset( $hover['filter_blur'] ) && intval( $hover['filter_blur'] ) != 0 ) {
            $h_filter_parts[] = 'blur(' . intval( $hover['filter_blur'] ) . 'px)';
        }
        if ( isset( $hover['filter_brightness'] ) && intval( $hover['filter_brightness'] ) != 100 ) {
            $h_filter_parts[] = 'brightness(' . intval( $hover['filter_brightness'] ) . '%)';
        }
        if ( isset( $hover['filter_contrast'] ) && intval( $hover['filter_contrast'] ) != 100 ) {
            $h_filter_parts[] = 'contrast(' . intval( $hover['filter_contrast'] ) . '%)';
        }
        if ( isset( $hover['filter_saturate'] ) && intval( $hover['filter_saturate'] ) != 100 ) {
            $h_filter_parts[] = 'saturate(' . intval( $hover['filter_saturate'] ) . '%)';
        }
        if ( isset( $hover['filter_grayscale'] ) && intval( $hover['filter_grayscale'] ) != 0 ) {
            $h_filter_parts[] = 'grayscale(' . intval( $hover['filter_grayscale'] ) . '%)';
        }
        if ( isset( $hover['filter_sepia'] ) && intval( $hover['filter_sepia'] ) != 0 ) {
            $h_filter_parts[] = 'sepia(' . intval( $hover['filter_sepia'] ) . '%)';
        }
        if ( ! empty( $h_filter_parts ) ) {
            $hover_decls[] = 'filter: ' . implode( ' ', $h_filter_parts );
            $has_hover = true;
        }

        if ( $has_hover ) {
            $dur  = intval( $transition_cfg['duration'] ?? 300 );
            $ease = esc_attr( $transition_cfg['easing'] ?? 'ease' );
            $trans_props = [ 'color', 'background-color', 'border-color', 'border-radius', 'box-shadow', 'opacity', 'transform', 'filter', 'text-shadow', 'backdrop-filter', '-webkit-backdrop-filter' ];
            $trans_val = implode( ', ', array_map( function( $p ) use ( $dur, $ease ) {
                return "{$p} {$dur}ms {$ease}";
            }, $trans_props ) );

            $sel = '#' . esc_attr( $css_id );
            $hover_css_rules[] = "{$sel} { transition: {$trans_val}; }";
            $hover_css_rules[] = "{$sel}:hover { " . implode( '; ', $hover_decls ) . "; }";
        }
    }

    /**
     * Collect responsive CSS rules for tablet and mobile breakpoints.
     *
     * Supported responsive overrides (suffixed with _tablet / _mobile):
     * - margin_top, margin_right, margin_bottom, margin_left
     * - padding_top, padding_right, padding_bottom, padding_left
     * - font_size
     * - gap
     * - border_radius (simple numeric or {tl,tr,br,bl} array)
     */
    private function collect_responsive_css( $style, $css_id, $advanced = [] ) {
        // Widescreen breakpoint uses min-width, others use max-width
        $widescreen_decls = [];
        $ws_props = [ 'margin', 'padding' ];
        foreach ( $ws_props as $prop ) {
            foreach ( [ 'top', 'right', 'bottom', 'left' ] as $side ) {
                $key = "{$prop}_{$side}_widescreen";
                if ( isset( $style[ $key ] ) && $style[ $key ] !== '' && $style[ $key ] !== null ) {
                    $widescreen_decls[] = "{$prop}-{$side}: " . intval( $style[ $key ] ) . 'px';
                }
            }
        }
        $ws_fs = 'font_size_widescreen';
        if ( isset( $style[ $ws_fs ] ) && $style[ $ws_fs ] !== '' && $style[ $ws_fs ] !== null ) {
            $widescreen_decls[] = 'font-size: ' . intval( $style[ $ws_fs ] ) . 'px';
        }
        $ws_gap = 'gap_widescreen';
        if ( isset( $style[ $ws_gap ] ) && $style[ $ws_gap ] !== '' && $style[ $ws_gap ] !== null ) {
            $widescreen_decls[] = 'gap: ' . intval( $style[ $ws_gap ] ) . 'px';
        }
        $ws_w = 'width_widescreen';
        if ( isset( $style[ $ws_w ] ) && $style[ $ws_w ] !== '' && $style[ $ws_w ] !== null ) {
            $w_val = $style[ $ws_w ];
            $widescreen_decls[] = 'width: ' . ( is_numeric( $w_val ) ? intval( $w_val ) . 'px' : esc_attr( $w_val ) );
        }
        if ( ! empty( $widescreen_decls ) ) {
            $ws_px = intval( $this->breakpoints['widescreen'] ?? 1400 );
            $this->responsive_css .= "@media (min-width:{$ws_px}px){#{$css_id}{" . implode( ';', $widescreen_decls ) . "}}";
        }

        $bp_map = [
            'tablet_landscape' => intval( $this->breakpoints['tablet_landscape'] ?? 1200 ) . 'px',
            'tablet'           => intval( $this->breakpoints['tablet'] ?? 960 ) . 'px',
            'mobile_landscape' => intval( $this->breakpoints['mobile_landscape'] ?? 640 ) . 'px',
            'mobile'           => intval( $this->breakpoints['mobile'] ?? 480 ) . 'px',
        ];
        foreach ( $bp_map as $bp => $max_width ) {
            $decls = [];

            // Margin & Padding (4 sides each)
            foreach ( [ 'margin', 'padding' ] as $prop ) {
                foreach ( [ 'top', 'right', 'bottom', 'left' ] as $side ) {
                    $key = "{$prop}_{$side}_{$bp}";
                    if ( isset( $style[ $key ] ) && $style[ $key ] !== '' && $style[ $key ] !== null ) {
                        $decls[] = "{$prop}-{$side}: " . intval( $style[ $key ] ) . 'px';
                    }
                }
            }

            // Font size
            $fs_key = "font_size_{$bp}";
            if ( isset( $style[ $fs_key ] ) && $style[ $fs_key ] !== '' && $style[ $fs_key ] !== null ) {
                $decls[] = 'font-size: ' . intval( $style[ $fs_key ] ) . 'px';
            }

            // Gap
            $gap_key = "gap_{$bp}";
            if ( isset( $style[ $gap_key ] ) && $style[ $gap_key ] !== '' && $style[ $gap_key ] !== null ) {
                $decls[] = 'gap: ' . intval( $style[ $gap_key ] ) . 'px';
            }

            // Border radius
            $br_key = "border_radius_{$bp}";
            if ( isset( $style[ $br_key ] ) && $style[ $br_key ] !== '' && $style[ $br_key ] !== null ) {
                $br_val = $style[ $br_key ];
                if ( is_array( $br_val ) ) {
                    $decls[] = sprintf( 'border-radius: %dpx %dpx %dpx %dpx',
                        intval( $br_val['tl'] ?? 0 ), intval( $br_val['tr'] ?? 0 ),
                        intval( $br_val['br'] ?? 0 ), intval( $br_val['bl'] ?? 0 ) );
                } else {
                    $decls[] = 'border-radius: ' . intval( $br_val ) . 'px';
                }
            }

            // Transform responsive overrides
            $resp_t_parts = [];
            $t_scale_key = "transform_scale_{$bp}";
            if ( isset( $style[ $t_scale_key ] ) && $style[ $t_scale_key ] !== '' && $style[ $t_scale_key ] !== null ) {
                $resp_t_parts[] = 'scale(' . floatval( $style[ $t_scale_key ] ) . ')';
            }
            $t_tx_key = "transform_translateX_{$bp}";
            if ( isset( $style[ $t_tx_key ] ) && $style[ $t_tx_key ] !== '' && $style[ $t_tx_key ] !== null ) {
                $resp_t_parts[] = 'translateX(' . floatval( $style[ $t_tx_key ] ) . 'px)';
            }
            $t_ty_key = "transform_translateY_{$bp}";
            if ( isset( $style[ $t_ty_key ] ) && $style[ $t_ty_key ] !== '' && $style[ $t_ty_key ] !== null ) {
                $resp_t_parts[] = 'translateY(' . floatval( $style[ $t_ty_key ] ) . 'px)';
            }
            if ( ! empty( $resp_t_parts ) ) {
                $decls[] = 'transform: ' . implode( ' ', $resp_t_parts );
            }

            // Width override
            $w_key = "width_{$bp}";
            if ( isset( $style[ $w_key ] ) && $style[ $w_key ] !== '' && $style[ $w_key ] !== null ) {
                $w_val = $style[ $w_key ];
                $decls[] = 'width: ' . ( is_numeric( $w_val ) ? intval( $w_val ) . 'px' : esc_attr( $w_val ) );
            }

            // Height override
            $h_key = "height_{$bp}";
            if ( isset( $style[ $h_key ] ) && $style[ $h_key ] !== '' && $style[ $h_key ] !== null ) {
                $h_val = $style[ $h_key ];
                $decls[] = 'height: ' . ( is_numeric( $h_val ) ? intval( $h_val ) . 'px' : esc_attr( $h_val ) );
            }

            // Display override (responsive visibility)
            $disp_key = "display_{$bp}";
            if ( isset( $style[ $disp_key ] ) && $style[ $disp_key ] !== '' && $style[ $disp_key ] !== null ) {
                $decls[] = 'display: ' . esc_attr( $style[ $disp_key ] );
            }

            // Flex direction override
            $fd_key = "flex_direction_{$bp}";
            if ( isset( $style[ $fd_key ] ) && $style[ $fd_key ] !== '' && $style[ $fd_key ] !== null ) {
                $decls[] = 'flex-direction: ' . esc_attr( $style[ $fd_key ] );
            }

            // Text align override
            $ta_key = "text_align_{$bp}";
            if ( isset( $style[ $ta_key ] ) && $style[ $ta_key ] !== '' && $style[ $ta_key ] !== null ) {
                $decls[] = 'text-align: ' . esc_attr( $style[ $ta_key ] );
            }

            // Positioning overrides (from advanced)
            if ( ! empty( $advanced ) ) {
                foreach ( [ 'top', 'left', 'bottom', 'right' ] as $dir ) {
                    $pk = "position_{$dir}_{$bp}";
                    if ( isset( $advanced[ $pk ] ) && $advanced[ $pk ] !== '' && $advanced[ $pk ] !== null ) {
                        $pv = $advanced[ $pk ];
                        $decls[] = $dir . ': ' . ( is_numeric( $pv ) ? $pv . 'px' : esc_attr( $pv ) );
                    }
                }
                $pw_key = "position_width_{$bp}";
                if ( isset( $advanced[ $pw_key ] ) && $advanced[ $pw_key ] !== '' && $advanced[ $pw_key ] !== null ) {
                    $pw_val = $advanced[ $pw_key ];
                    $decls[] = 'width: ' . ( is_numeric( $pw_val ) ? $pw_val . 'px' : esc_attr( $pw_val ) );
                }
                $pz_key = "position_zindex_{$bp}";
                if ( isset( $advanced[ $pz_key ] ) && $advanced[ $pz_key ] !== '' && $advanced[ $pz_key ] !== null ) {
                    $decls[] = 'z-index: ' . intval( $advanced[ $pz_key ] );
                }
            }

            if ( ! empty( $decls ) ) {
                $sel = '#' . esc_attr( $css_id );
                $this->responsive_css_rules[ $max_width ][] = "{$sel} { " . implode( '; ', $decls ) . "; }";
            }
        }
    }

    /**
     * Collect custom CSS rules for an element.
     * Replaces the placeholder "selector" with the actual CSS selector (#css_id).
     */
    private function collect_custom_css( $settings, $css_id, &$hover_css_rules ) {
        $custom_css = trim( $settings['custom_css'] ?? '' );
        if ( $custom_css !== '' ) {
            $selector = '#' . esc_attr( $css_id );
            $custom_css = str_replace( 'selector', $selector, $custom_css );
            $hover_css_rules[] = $custom_css;
        }
    }

    /**
     * Check for postgrid element usage recursively.
     */
    private function check_postgrid_recursive( $nodes ) {
        foreach ( $nodes as $node ) {
            if ( ( $node['type'] ?? '' ) === 'postgrid' ) {
                return true;
            }
            if ( ! empty( $node['children'] ) && is_array( $node['children'] ) ) {
                if ( $this->check_postgrid_recursive( $node['children'] ) ) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check for Leaflet map (mode: locations) usage recursively.
     */
    private function check_leaflet_map_recursive( $nodes ) {
        foreach ( $nodes as $node ) {
            $map_mode = $node['settings']['mode'] ?? 'single';
            if ( ( $node['type'] ?? '' ) === 'map' && in_array( $map_mode, [ 'locations', 'services' ], true ) ) {
                return true;
            }
            if ( ! empty( $node['children'] ) && is_array( $node['children'] ) ) {
                if ( $this->check_leaflet_map_recursive( $node['children'] ) ) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check for proslider element usage recursively.
     */
    private function check_proslider_recursive( $nodes ) {
        foreach ( $nodes as $node ) {
            if ( ( $node['type'] ?? '' ) === 'proslider' ) {
                return true;
            }
            if ( ! empty( $node['children'] ) && is_array( $node['children'] ) ) {
                if ( $this->check_proslider_recursive( $node['children'] ) ) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Generic recursive check for a specific tile type.
     */
    private function check_tile_recursive( $nodes, $tile_type ) {
        foreach ( $nodes as $node ) {
            if ( ( $node['type'] ?? '' ) === $tile_type ) {
                return true;
            }
            if ( ! empty( $node['children'] ) && is_array( $node['children'] ) ) {
                if ( $this->check_tile_recursive( $node['children'], $tile_type ) ) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check for progallery with custom lightbox (thumbs) recursively.
     */
    private function check_progallery_lightbox_recursive( $nodes ) {
        foreach ( $nodes as $node ) {
            if ( ( $node['type'] ?? '' ) === 'progallery' ) {
                $thumbs = $node['settings']['lightbox_thumbs'] ?? 'none';
                if ( in_array( $thumbs, [ 'bottom', 'right', 'left' ], true ) ) {
                    return true;
                }
            }
            if ( ! empty( $node['children'] ) && is_array( $node['children'] ) ) {
                if ( $this->check_progallery_lightbox_recursive( $node['children'] ) ) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get video MIME type from URL.
     */
    private function get_video_mime( $url ) {
        $ext = strtolower( pathinfo( parse_url( $url, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
        $map = [ 'mp4' => 'video/mp4', 'webm' => 'video/webm', 'ogg' => 'video/ogg' ];
        return $map[ $ext ] ?? 'video/mp4';
    }

    // =========================================================================
    // Shortcode entry point
    // =========================================================================

    /**
     * Shortcode: [olo_template id="123"]
     */
    public function render_shortcode( $atts ) {
        $atts = shortcode_atts( [
            'id' => 0,
        ], $atts, 'olo_template' );

        $id = absint( $atts['id'] );
        if ( ! $id ) {
            return '<!-- Olobuilder: No template ID provided -->';
        }

        $db       = new Olo_Database();
        $template = $db->get_template( $id );

        if ( ! $template ) {
            return '<!-- Olobuilder: Template not found -->';
        }

        if ( $template['status'] !== 'published' && $template['status'] !== 'draft' ) {
            return '<!-- Olobuilder: Template not available -->';
        }

        $tiles = $template['content'];
        if ( empty( $tiles ) || ! is_array( $tiles ) ) {
            return '<!-- Olobuilder: Empty template -->';
        }

        // Migrate legacy flat content to tree format
        $tiles = $this->maybe_migrate_content( $tiles );

        // Filtro per traduzioni multilingua (usato da Olo Lang)
        $tiles = apply_filters( 'olo_template_content', $tiles, $id );

        // Index all tiles for cross-tile lookup (e.g., navmenu → search tile)
        self::index_tiles( $tiles );

        // Page settings
        $page_settings     = $template['settings'] ?? [];
        $page_settings     = apply_filters( 'olo_template_settings', $page_settings, $id );
        $content_max_width = intval( $page_settings['content_max_width'] ?? 1200 );
        $page_bg           = $page_settings['page_bg'] ?? [ 'type' => 'none' ];

        // Custom responsive breakpoints
        $this->breakpoints = wp_parse_args( $page_settings['breakpoints'] ?? [], [
            'widescreen'       => 1400,
            'tablet_landscape' => 1200,
            'tablet'           => 960,
            'mobile_landscape' => 640,
            'mobile'           => 480,
        ] );

        // Shared utilities (escHtml etc.) — loaded before all olo-*.js scripts
        wp_enqueue_script(
            'olo-utils',
            OLO_URL . 'assets/js/olo-utils.js',
            [],
            OLO_VERSION,
            true
        );

        // Post Grid detection (recursive)
        if ( $this->check_postgrid_recursive( $tiles ) ) {
            wp_enqueue_script(
                'olo-postgrid-js',
                OLO_URL . 'assets/js/olo-postgrid.js',
                [ 'olo-utils' ],
                OLO_VERSION,
                true
            );
        }

        // Leaflet map detection (recursive)
        if ( $this->check_leaflet_map_recursive( $tiles ) ) {
            $vendor_url = OLO_URL . 'assets/vendor/leaflet/';

            wp_enqueue_style( 'leaflet-css', $vendor_url . 'leaflet.css', [], '1.9.4' );
            wp_enqueue_style( 'leaflet-markercluster-css', $vendor_url . 'leaflet.markercluster.css', [ 'leaflet-css' ], '1.5.3' );
            wp_enqueue_style( 'leaflet-markercluster-default-css', $vendor_url . 'leaflet.markercluster-default.css', [ 'leaflet-markercluster-css' ], '1.5.3' );

            wp_enqueue_script( 'leaflet-js', $vendor_url . 'leaflet.js', [], '1.9.4', true );
            wp_enqueue_script( 'leaflet-markercluster-js', $vendor_url . 'leaflet.markercluster.js', [ 'leaflet-js' ], '1.5.3', true );
            wp_enqueue_script(
                'olo-map-js',
                OLO_URL . 'assets/js/olo-map.js',
                [ 'olo-utils', 'leaflet-js', 'leaflet-markercluster-js' ],
                OLO_VERSION,
                true
            );

        }

        // ProSlider detection (recursive)
        if ( $this->check_proslider_recursive( $tiles ) ) {
            wp_enqueue_style(
                'olo-proslider-css',
                OLO_URL . 'assets/css/olo-proslider.css',
                [],
                OLO_VERSION
            );
            wp_enqueue_script(
                'olo-proslider-js',
                OLO_URL . 'assets/js/olo-proslider.js',
                [],
                OLO_VERSION,
                true
            );
        }

        // LiveSearch detection (recursive)
        if ( $this->check_tile_recursive( $tiles, 'livesearch' ) ) {
            wp_enqueue_style(
                'olo-livesearch-css',
                OLO_URL . 'assets/css/olo-livesearch.css',
                [],
                OLO_VERSION
            );
            wp_enqueue_script(
                'olo-livesearch-js',
                OLO_URL . 'assets/js/olo-livesearch.js',
                [ 'olo-utils' ],
                OLO_VERSION,
                true
            );
        }

        // ServiceSearch detection (recursive)
        if ( $this->check_tile_recursive( $tiles, 'servicesearch' ) ) {
            wp_enqueue_script(
                'olo-servicesearch-js',
                OLO_URL . 'assets/js/olo-servicesearch.js',
                [],
                OLO_VERSION,
                true
            );
        }

        // ServiceResults detection (recursive)
        if ( $this->check_tile_recursive( $tiles, 'serviceresults' ) ) {
            $vendor_url = OLO_URL . 'assets/vendor/leaflet/';

            wp_enqueue_style( 'leaflet-css', $vendor_url . 'leaflet.css', [], '1.9.4' );
            wp_enqueue_style( 'leaflet-markercluster-css', $vendor_url . 'leaflet.markercluster.css', [ 'leaflet-css' ], '1.5.3' );
            wp_enqueue_style( 'leaflet-markercluster-default-css', $vendor_url . 'leaflet.markercluster-default.css', [ 'leaflet-markercluster-css' ], '1.5.3' );

            wp_enqueue_script( 'leaflet-js', $vendor_url . 'leaflet.js', [], '1.9.4', true );
            wp_enqueue_script( 'leaflet-markercluster-js', $vendor_url . 'leaflet.markercluster.js', [ 'leaflet-js' ], '1.5.3', true );
            wp_enqueue_script(
                'olo-serviceresults-js',
                OLO_URL . 'assets/js/olo-serviceresults.js',
                [ 'olo-utils', 'leaflet-js', 'leaflet-markercluster-js' ],
                OLO_VERSION,
                true
            );
        }

        // ProGallery custom lightbox detection (recursive)
        if ( $this->check_progallery_lightbox_recursive( $tiles ) ) {
            wp_enqueue_script(
                'olo-progallery-lightbox-js',
                OLO_URL . 'assets/js/olo-progallery-lightbox.js',
                [],
                OLO_VERSION,
                true
            );
        }

        // PdfViewer detection (recursive)
        if ( $this->check_tile_recursive( $tiles, 'pdfviewer' ) ) {
            wp_enqueue_script(
                'pdfjs',
                OLO_URL . 'assets/vendor/pdfjs/pdf.min.js',
                [],
                '3.11.174',
                true
            );
            wp_enqueue_script(
                'pageflip-js',
                OLO_URL . 'assets/vendor/pageflip/page-flip.browser.js',
                [],
                '2.0.7',
                true
            );
            wp_enqueue_style(
                'olo-pdfviewer-css',
                OLO_URL . 'assets/css/olo-pdfviewer.css',
                [],
                OLO_VERSION
            );
            wp_enqueue_script(
                'olo-pdfviewer-js',
                OLO_URL . 'assets/js/olo-pdfviewer.js',
                [ 'pdfjs', 'pageflip-js' ],
                OLO_VERSION,
                true
            );
            wp_localize_script( 'olo-pdfviewer-js', 'oloPdfViewerData', [
                'workerUrl' => OLO_URL . 'assets/vendor/pdfjs/pdf.worker.min.js',
            ] );
        }

        // PdfPro detection (recursive) — shares pdfjs/pageflip vendors with PdfViewer
        if ( $this->check_tile_recursive( $tiles, 'pdfpro' ) ) {
            wp_enqueue_script(
                'pdfjs',
                OLO_URL . 'assets/vendor/pdfjs/pdf.min.js',
                [],
                '3.11.174',
                true
            );
            wp_enqueue_script(
                'pageflip-js',
                OLO_URL . 'assets/vendor/pageflip/page-flip.browser.js',
                [],
                '2.0.7',
                true
            );
            wp_enqueue_script(
                'olo-pdfpro-js',
                OLO_URL . 'assets/js/olo-pdfpro.js',
                [ 'pdfjs', 'pageflip-js' ],
                OLO_VERSION,
                true
            );
            wp_localize_script( 'olo-pdfpro-js', 'oloPdfProData', [
                'workerUrl' => OLO_URL . 'assets/vendor/pdfjs/pdf.worker.min.js',
            ] );
        }

        // SVG Animator
        if ( $this->check_tile_recursive( $tiles, 'svganimator' ) ) {
            wp_enqueue_style( 'olo-svganimator-css', OLO_URL . 'assets/css/olo-svganimator.css', [], OLO_VERSION );
            wp_enqueue_script( 'olo-svganimator-js', OLO_URL . 'assets/js/olo-svganimator.js', [], OLO_VERSION, true );
        }

        // Viewer 360
        if ( $this->check_tile_recursive( $tiles, 'viewer360' ) ) {
            wp_enqueue_script( 'olo-viewer360-js', OLO_URL . 'assets/js/olo-viewer360.js', [], OLO_VERSION, true );
        }

        $manager = Olo_Tile_Manager::instance();

        $page_bg_css = $this->get_bg_inline_css( $page_bg );
        $hover_css_rules = [];
        $this->responsive_css_rules = [];
        $tile_counter = 0;

        ob_start();
        ?>
        <div id="olo-main-content" role="main" class="olo-template olo-template-<?php echo esc_attr( $id ); ?>"<?php
            if ( ( $page_bg['type'] === 'image' && ! empty( $page_bg['image_url'] ) ) || ( $page_bg['type'] === 'video' && ! empty( $page_bg['video_url'] ) ) ) {
                echo ' style="position: relative; overflow: clip"';
            } elseif ( $page_bg_css ) {
                echo ' style="' . esc_attr( $page_bg_css ) . '"';
            }
        ?>>
            <?php
            // Page background image layer
            if ( $page_bg['type'] === 'image' && ! empty( $page_bg['image_url'] ) ) :
                $pg_size = esc_attr( $page_bg['image_size'] ?? 'cover' );
                $pg_pos  = esc_attr( $page_bg['image_position'] ?? 'center center' );
                $pg_parallax_attr = $this->build_uk_parallax_attr( $page_bg );
            ?>
                <div class="olo-tile-bg"
                    style="background-image: url(<?php echo esc_url( $page_bg['image_url'] ); ?>); background-size: <?php echo $pg_size; ?>; background-position: <?php echo $pg_pos; ?>; background-repeat: no-repeat"
                    <?php echo $pg_parallax_attr; ?>
                ></div>
            <?php endif; ?>
            <?php
            // Page background video layer
            if ( $page_bg['type'] === 'video' && ! empty( $page_bg['video_url'] ) ) :
                $vid_poster = ! empty( $page_bg['video_poster'] ) ? esc_url( $page_bg['video_poster'] ) : '';
                $vid_pos    = esc_attr( $page_bg['image_position'] ?? 'center center' );
                $vid_fit    = esc_attr( $page_bg['video_fit'] ?? 'cover' );
            ?>
                <video aria-hidden="true" class="olo-tile-bg" style="object-fit: <?php echo $vid_fit; ?>; object-position: <?php echo $vid_pos; ?>; pointer-events: none" autoplay muted loop playsinline<?php if ( $vid_poster ) echo ' poster="' . $vid_poster . '"'; ?>><source src="<?php echo esc_url( $page_bg['video_url'] ); ?>" type="<?php echo esc_attr( $this->get_video_mime( $page_bg['video_url'] ) ); ?>"></video>
            <?php endif; ?>
            <?php
            // Page overlay
            if ( $page_bg['type'] !== 'none' && ! empty( $page_bg['overlay_opacity'] ) && intval( $page_bg['overlay_opacity'] ) > 0 ) :
                $ov_color   = esc_attr( $page_bg['overlay_color'] ?? '#000000' );
                $ov_opacity = intval( $page_bg['overlay_opacity'] ) / 100;
            ?>
                <div class="olo-tile-overlay" style="background-color: <?php echo $ov_color; ?>; opacity: <?php echo $ov_opacity; ?>" aria-hidden="true"></div>
            <?php endif; ?>

            <div class="olo-frontend-grid olo-tile-content" style="--olo-content-width: <?php echo $content_max_width >= 9999 ? '100%' : $content_max_width . 'px'; ?>; --olo-container-max-width: <?php echo $content_max_width >= 9999 ? 'none' : $content_max_width . 'px'; ?>"><?php // per-template override of global container width ?>
                <?php
                foreach ( $tiles as $section ) {
                    echo $this->render_node( $section, $manager, $id, $hover_css_rules, $tile_counter );
                }
                ?>
            </div>
            <?php if ( ! empty( $hover_css_rules ) || ! empty( $this->responsive_css_rules ) ) :
                $all_css = implode( ' ', $hover_css_rules );
                foreach ( $this->responsive_css_rules as $max_w => $rules ) {
                    $all_css .= ' @media(max-width:' . $max_w . '){' . implode( ' ', $rules ) . '}';
                }
                if ( class_exists( 'Olo_Asset_Optimizer' ) ) {
                    echo Olo_Asset_Optimizer::serve_css( $all_css, $id );
                } else {
                    echo '<style class="olo-hover-styles">' . $all_css . '</style>';
                }
            endif; ?>
            <?php
            // Detect scroll-snap sections
            $has_any_snap = false;
            foreach ( $tiles as $sec ) {
                if ( ! empty( $sec['settings']['scroll_snap'] ) ) {
                    $has_any_snap = true;
                    break;
                }
            }
            ?>
            <?php
            // Sticky Effect: JS for reveal wrappers + horizontal scroll groups
            $has_any_sticky_v = false;
            $has_any_sticky_h = false;
            foreach ( $tiles as $sec ) {
                $eff = $sec['settings']['sticky_effect'] ?? 'none';
                if ( $eff === 'cover' || $eff === 'reveal' ) $has_any_sticky_v = true;
                if ( $eff === 'cover-h' || $eff === 'reveal-h' ) $has_any_sticky_h = true;
            }
            if ( $has_any_sticky_v || $has_any_sticky_h ) : ?>
                <script>
                (function(){
                    <?php if ( $has_any_sticky_v ) : ?>
                    function initReveal(){
                        document.querySelectorAll('.olo-reveal-wrapper').forEach(function(wrap){
                            var sec=wrap.querySelector('.olo-sticky-reveal');
                            if(!sec)return;
                            var top=parseInt(wrap.dataset.stickyTop)||0;
                            wrap.style.height='';wrap.style.marginTop='';wrap.style.marginBottom='';
                            var h=sec.offsetHeight;
                            wrap.style.height=(h*2+top)+'px';
                            wrap.style.marginTop='-'+h+'px';
                            if(top)wrap.style.marginBottom='-'+top+'px';
                        });
                    }
                    <?php endif; ?>
                    <?php if ( $has_any_sticky_h ) : ?>
                    function getSec(el){
                        if(el.classList.contains('uk-section'))return el;
                        return el.querySelector('.uk-section');
                    }
                    function initHGroups(){
                        /* Collect all h-markers and group consecutive runs */
                        var markers=Array.from(document.querySelectorAll('.olo-h-marker'));
                        if(!markers.length)return;
                        var runs=[];
                        var currentRun=[];
                        for(var i=0;i<markers.length;i++){
                            var m=markers[i];
                            if(m.dataset.oloHDone)continue;
                            /* Check if this marker is adjacent to the previous one */
                            if(currentRun.length>0){
                                var prev=currentRun[currentRun.length-1];
                                var prevNext=prev.nextElementSibling;
                                /* Skip non-section siblings (text nodes, etc.) */
                                while(prevNext){
                                    if(prevNext===m){break;}
                                    if(prevNext.classList){
                                        if(prevNext.classList.contains('olo-h-marker')){break;}
                                        if(getSec(prevNext)){break;}
                                    }
                                    prevNext=prevNext.nextElementSibling;
                                }
                                if(prevNext===m){
                                    currentRun.push(m);
                                }else{
                                    if(currentRun.length>=2){runs.push(currentRun);}
                                    currentRun=[m];
                                }
                            }else{
                                currentRun=[m];
                            }
                        }
                        if(currentRun.length>=2){runs.push(currentRun);}
                        /* Also support single marker that pairs with next non-h sibling */
                        if(currentRun.length===1){runs.push(currentRun);}
                        runs.forEach(function(run){
                            var stickyTop=parseInt(run[0].dataset.stickyTop)||0;
                            var sections=[];
                            run.forEach(function(m){
                                var sec=getSec(m);
                                if(sec)sections.push({marker:m,sec:sec});
                            });
                            var n=sections.length;
                            if(n<2)return;
                            var viewH=window.innerHeight-stickyTop;
                            /* Group height = viewH * n (1 screen per panel for scroll travel) */
                            var group=document.createElement('div');
                            group.className='olo-h-group';
                            group.style.height=(viewH*n)+'px';
                            var viewport=document.createElement('div');
                            viewport.className='olo-h-viewport';
                            viewport.style.top=stickyTop+'px';
                            viewport.style.height=viewH+'px';
                            var track=document.createElement('div');
                            track.className='olo-h-track';
                            track.style.height='100%';
                            var panelCSS='flex:0 0 100%;width:100%;min-height:'+viewH+'px;box-sizing:border-box;position:relative;overflow:hidden';
                            sections.forEach(function(item){
                                var clone=item.sec.cloneNode(true);
                                clone.style.cssText+=';'+panelCSS;
                                track.appendChild(clone);
                            });
                            viewport.appendChild(track);
                            group.appendChild(viewport);
                            /* Insert before the first marker and hide all originals */
                            run[0].parentNode.insertBefore(group,run[0]);
                            run.forEach(function(m){
                                m.dataset.oloHDone='1';
                                m.style.display='none';
                            });
                            /* Scroll-linked translateX: travel across (n-1) panels */
                            var ticking=false;
                            var maxShift=(n-1)*100;
                            function onScroll(){
                                if(!ticking){ticking=true;requestAnimationFrame(function(){
                                    var rect=group.getBoundingClientRect();
                                    var scrolled=-rect.top;
                                    var travel=group.offsetHeight-viewH;
                                    if(travel<=0){ticking=false;return;}
                                    var p=Math.max(0,Math.min(1,scrolled/travel));
                                    track.style.transform='translateX('+ (-p*maxShift) +'%)';
                                    ticking=false;
                                });}
                            }
                            window.addEventListener('scroll',onScroll,{passive:true});
                            onScroll();
                        });
                    }
                    <?php endif; ?>
                    function initAll(){
                        <?php if ( $has_any_sticky_v ) : ?>initReveal();<?php endif; ?>
                        <?php if ( $has_any_sticky_h ) : ?>initHGroups();<?php endif; ?>
                    }
                    if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',initAll)}
                    else{initAll()}
                    window.addEventListener('load',initAll);
                })();
                </script>
            <?php endif; ?>
            <script>
            (function(){
              if(window.__oloEntranceInit) return;
              window.__oloEntranceInit = true;
              function initEntrance(){
                var els = document.querySelectorAll('[class*="olo-entrance-"]');
                if(!els.length) return;
                if(window.matchMedia('(prefers-reduced-motion: reduce)').matches){
                  els.forEach(function(el){ el.classList.add('olo-visible'); });
                  return;
                }
                var obs = new IntersectionObserver(function(entries){
                  entries.forEach(function(e){
                    if(e.isIntersecting){
                      if(e.target.classList.contains('olo-stagger-parent')){
                        var delay = parseInt(getComputedStyle(e.target).getPropertyValue('--olo-stagger-delay')) || 100;
                        var children = e.target.querySelectorAll('.olo-frontend-tile');
                        if(!children.length){
                          children = e.target.querySelectorAll('[uk-grid] > *, .uk-slider-items > *, .uk-accordion > li, .uk-list > li, .olo-tl-item, .olo-postgrid-item, .olo-gal-item, .olo-car-slide, .olo-pl-item, .olo-test-card');
                        }
                        if(children.length){
                          children.forEach(function(child, i){
                            child.style.transitionDelay = (i * delay) + 'ms';
                            child.style.animationDelay = (i * delay) + 'ms';
                          });
                        }
                      }
                      requestAnimationFrame(function(){
                        e.target.classList.add('olo-visible');
                      });
                      obs.unobserve(e.target);
                    }
                  });
                }, {threshold: 0.1});
                els.forEach(function(el){ obs.observe(el); });
              }
              if(document.readyState === 'complete'){
                requestAnimationFrame(initEntrance);
              } else {
                window.addEventListener('load', function(){ requestAnimationFrame(initEntrance); });
              }
            })();
            </script>
            <script>
            (function(){
              var lazys = document.querySelectorAll('[data-olo-lazy]');
              if(!lazys.length) return;
              var obs = new IntersectionObserver(function(entries){
                entries.forEach(function(e){
                  if(e.isIntersecting){
                    var tpl = e.target.querySelector('template');
                    if(tpl){
                      var ph = e.target.querySelector('.olo-lazy-ph');
                      if(ph) ph.remove();
                      var frag = document.importNode(tpl.content, true);
                      var scripts = frag.querySelectorAll('script');
                      var pending = [];
                      scripts.forEach(function(s){ pending.push(s); });
                      pending.forEach(function(s){ s.parentNode.removeChild(s); });
                      e.target.appendChild(frag);
                      tpl.remove();
                      (function runScripts(list, parent){
                        if(!list.length) return;
                        var old = list.shift();
                        var ns = document.createElement('script');
                        Array.from(old.attributes).forEach(function(a){ ns.setAttribute(a.name, a.value); });
                        if(old.src){
                          ns.onload = ns.onerror = function(){ runScripts(list, parent); };
                          parent.appendChild(ns);
                        } else {
                          ns.textContent = old.textContent;
                          parent.appendChild(ns);
                          runScripts(list, parent);
                        }
                      })(pending, e.target);
                    }
                    obs.unobserve(e.target);
                  }
                });
              }, {rootMargin: '200px'});
              lazys.forEach(function(el){ obs.observe(el); });
            })();
            </script>
            <script>
            (function(){
              if(!window.matchMedia('(min-width:960px)').matches) return;
              var tilts = document.querySelectorAll('[data-olo-tilt]');
              var tracks = document.querySelectorAll('[data-olo-track]');
              if(!tilts.length){ if(!tracks.length) return; }
              tilts.forEach(function(el){
                el.style.willChange = 'transform';
                el.style.transition = 'transform 0.15s ease-out';
              });
              tracks.forEach(function(el){
                el.style.willChange = 'transform';
                el.style.transition = 'transform 0.2s ease-out';
              });
              document.addEventListener('mousemove', function(e){
                tilts.forEach(function(el){
                  var rect = el.getBoundingClientRect();
                  var cx = rect.left + rect.width / 2;
                  var cy = rect.top + rect.height / 2;
                  var dx = e.clientX - cx;
                  var dy = e.clientY - cy;
                  if(Math.abs(dx) > rect.width / 2 + 50) return;
                  if(Math.abs(dy) > rect.height / 2 + 50) return;
                  var intensity = parseInt(el.dataset.oloTilt) || 15;
                  var rx = -(dy / (rect.height / 2)) * intensity;
                  var ry = (dx / (rect.width / 2)) * intensity;
                  rx = Math.max(-intensity, Math.min(intensity, rx));
                  ry = Math.max(-intensity, Math.min(intensity, ry));
                  el.style.transform = 'perspective(1000px) rotateX(' + rx + 'deg) rotateY(' + ry + 'deg)';
                });
                tracks.forEach(function(el){
                  var rect = el.getBoundingClientRect();
                  var cx = rect.left + rect.width / 2;
                  var cy = rect.top + rect.height / 2;
                  var dx = e.clientX - cx;
                  var dy = e.clientY - cy;
                  var speed = parseInt(el.dataset.oloTrack) || 3;
                  var tx = (dx / rect.width) * speed * 10;
                  var ty = (dy / rect.height) * speed * 10;
                  el.style.transform = 'translate(' + tx + 'px, ' + ty + 'px)';
                });
              });
              document.addEventListener('mouseleave', function(){
                tilts.forEach(function(el){ el.style.transform = 'none'; });
                tracks.forEach(function(el){ el.style.transform = 'none'; });
              });
              tilts.forEach(function(el){
                el.addEventListener('mouseleave', function(){ el.style.transform = 'none'; });
              });
            })();
            </script>
            <script>
            (function(){
              var els = document.querySelectorAll('[data-olo-scroll-fx]');
              if(!els.length) return;
              var ticking = false;
              els.forEach(function(el){ el.style.willChange = 'transform, opacity'; });
              function update(){
                var vh = window.innerHeight || document.documentElement.clientHeight;
                els.forEach(function(el){
                  var rect = el.getBoundingClientRect();
                  var elemH = rect.height;
                  var elemTop = rect.top;
                  var viewportBottom = vh;
                  var denom = viewportBottom + elemH;
                  if(denom === 0) return;
                  var progress = (viewportBottom - elemTop) / denom;
                  if(progress < 0) progress = 0;
                  if(progress > 1) progress = 1;
                  var fx;
                  try { fx = JSON.parse(el.dataset.oloScrollFx); } catch(e){ return; }
                  var transforms = [];
                  if(fx.opacity){
                    var oStart = fx.opacity[0], oEnd = fx.opacity[1];
                    el.style.opacity = oStart + progress * (oEnd - oStart);
                  }
                  if(fx.scale){
                    var sStart = fx.scale[0], sEnd = fx.scale[1];
                    var sv = sStart + progress * (sEnd - sStart);
                    transforms.push('scale(' + sv + ')');
                  }
                  if(fx.rotate){
                    var rStart = fx.rotate[0], rEnd = fx.rotate[1];
                    var rv = rStart + progress * (rEnd - rStart);
                    transforms.push('rotate(' + rv + 'deg)');
                  }
                  if(fx.translatex){
                    var xStart = fx.translatex[0], xEnd = fx.translatex[1];
                    var xv = xStart + progress * (xEnd - xStart);
                    transforms.push('translateX(' + xv + 'px)');
                  }
                  if(transforms.length){
                    el.style.transform = transforms.join(' ');
                  }
                });
                ticking = false;
              }
              function onScroll(){
                if(!ticking){
                  ticking = true;
                  requestAnimationFrame(update);
                }
              }
              window.addEventListener('scroll', onScroll, {passive: true});
              window.addEventListener('resize', onScroll, {passive: true});
              update();
            })();
            </script>
            <?php if ( $has_any_snap ) : ?>
            <script>
            (function(){
              var sections = document.querySelectorAll('[data-olo-snap-section]');
              if (!sections.length) return;

              /* Apply scroll-snap-type to the scroll container (html element) */
              var html = document.documentElement;
              html.style.scrollSnapType = 'y proximity';
              html.style.overflowY = 'scroll';
              html.style.height = '100vh';

              /* Make header/footer reachable — give snap-align to non-snap siblings */
              var header = document.querySelector('.olo-header-wrap');
              if (header) { header.style.scrollSnapAlign = 'start'; }
              var footer = document.querySelector('.olo-footer-wrap');
              if (footer) { footer.style.scrollSnapAlign = 'end'; }

              /* Build dots navigation */
              var dotColor = sections[0].dataset.snapDotColor || '#ffffff';
              var dotActive = sections[0].dataset.snapDotActive || '';
              var dotPos = sections[0].dataset.snapDotPos || 'right';

              var nav = document.createElement('div');
              nav.className = 'olo-snap-dots';
              nav.setAttribute('aria-label', 'Navigazione sezioni');
              nav.style.cssText = 'position:fixed;top:50%;transform:translateY(-50%);z-index:9990;display:flex;flex-direction:column;gap:12px;padding:8px;' + dotPos + ':16px';

              var dots = [];
              sections.forEach(function(sec, i) {
                var dot = document.createElement('button');
                dot.type = 'button';
                dot.setAttribute('aria-label', 'Vai alla sezione ' + (i + 1));
                dot.style.cssText = 'width:12px;height:12px;border-radius:50%;border:2px solid ' + dotColor + ';background:transparent;cursor:pointer;padding:0;transition:background 0.3s,transform 0.3s;outline:none';
                dot.addEventListener('click', function() {
                  sec.scrollIntoView({ behavior: 'smooth' });
                });
                nav.appendChild(dot);
                dots.push(dot);
              });

              document.body.appendChild(nav);

              /* IntersectionObserver to highlight active dot */
              var activeIdx = 0;
              function setActive(idx) {
                if (idx === activeIdx) {
                  if (dots[idx]) {
                    /* ensure first load paints correctly */
                  }
                }
                activeIdx = idx;
                dots.forEach(function(d, j) {
                  if (j === idx) {
                    d.style.background = dotActive || dotColor;
                    d.style.borderColor = dotActive || dotColor;
                    d.style.transform = 'scale(1.3)';
                  } else {
                    d.style.background = 'transparent';
                    d.style.borderColor = dotColor;
                    d.style.transform = 'scale(1)';
                  }
                });
              }
              setActive(0);

              var obs = new IntersectionObserver(function(entries) {
                entries.forEach(function(e) {
                  if (e.isIntersecting) {
                    if (e.intersectionRatio >= 0.5) {
                      var idx = Array.prototype.indexOf.call(sections, e.target);
                      if (idx >= 0) setActive(idx);
                    }
                  }
                });
              }, { threshold: 0.5 });

              sections.forEach(function(sec) { obs.observe(sec); });
            })();
            </script>
            <?php endif; ?>
        </div>
        <?php

        // Signal that Olobuild frontend content was rendered (used by a11y scripts)
        do_action( 'olo_frontend_rendered', $id );

        $html = ob_get_clean();

        // Post-process: add srcset to images missing it
        $html = $this->add_srcset_to_images( $html );

        return $html;
    }

    /**
     * Post-process HTML: find <img> tags without srcset and add it
     * by resolving the attachment ID from the src URL.
     */
    private function add_srcset_to_images( $html ) {
        if ( ! str_contains( $html, '<img' ) ) {
            return $html;
        }

        return preg_replace_callback( '/<img\b([^>]*?)\/?\s*>/i', function ( $match ) {
            $tag = $match[0];

            // Skip if already has srcset
            if ( stripos( $tag, 'srcset=' ) !== false ) {
                return $tag;
            }

            // Extract src
            if ( ! preg_match( '/\bsrc=["\']([^"\']+)["\']/i', $tag, $src_m ) ) {
                return $tag;
            }

            $src = $src_m[1];

            // Only process local uploads
            if ( ! str_contains( $src, '/wp-content/uploads/' ) ) {
                return $tag;
            }

            $att_id = attachment_url_to_postid( $src );
            if ( ! $att_id ) {
                return $tag;
            }

            $srcset = wp_get_attachment_image_srcset( $att_id, 'full' );
            if ( ! $srcset ) {
                return $tag;
            }

            $sizes = wp_get_attachment_image_sizes( $att_id, 'full' ) ?: '(max-width: 480px) 100vw, (max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw';

            // Add width/height if missing
            $extra = '';
            if ( stripos( $tag, 'width=' ) === false ) {
                $meta = wp_get_attachment_metadata( $att_id );
                if ( ! empty( $meta['width'] ) ) {
                    $extra .= ' width="' . intval( $meta['width'] ) . '" height="' . intval( $meta['height'] ) . '"';
                }
            }

            // Insert srcset before closing
            $insert = ' srcset="' . esc_attr( $srcset ) . '" sizes="' . esc_attr( $sizes ) . '"' . $extra;

            return str_replace( '<img', '<img' . $insert, $tag );
        }, $html );
    }

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
            OLO_URL . 'assets/vendor/uikit/css/uikit.min.css',
            OLO_URL . 'assets/css/frontend.css?v=' . OLO_VERSION,
            OLO_URL . 'assets/css/olo-proslider.css?v=' . OLO_VERSION,
        ];

        $inline_css = '';
        if ( class_exists( 'Olo_Style_System' ) ) {
            $inline_css = Olo_Style_System::instance()->generate_css();
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

        $manager = Olo_Tile_Manager::instance();
        $hover_css_rules = [];
        $this->responsive_css_rules = [];
        $tile_counter = 0;

        echo '<div class="olo-frontend-grid olo-tile-content" style="--olo-content-width: ' . ( $content_max_width >= 9999 ? '100%' : $content_max_width . 'px' ) . '; --olo-container-max-width: ' . ( $content_max_width >= 9999 ? 'none' : $content_max_width . 'px' ) . '">';
        foreach ( $tiles as $section ) {
            echo $this->render_node( $section, $manager, 0, $hover_css_rules, $tile_counter );
        }
        echo '</div>';

        if ( ! empty( $hover_css_rules ) || ! empty( $this->responsive_css_rules ) ) {
            $all_css = implode( ' ', $hover_css_rules );
            foreach ( $this->responsive_css_rules as $max_w => $rules ) {
                $all_css .= ' @media(max-width:' . $max_w . '){' . implode( ' ', $rules ) . '}';
            }
            echo '<style class="olo-hover-styles">' . $all_css . '</style>';
        }
    }
}
