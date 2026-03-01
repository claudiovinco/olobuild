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

    private $align_map = [
        'stretch' => 'stretch',
        'start'   => 'flex-start',
        'center'  => 'center',
        'end'     => 'flex-end',
    ];

    public function init() {
        add_shortcode( 'olo_template', [ $this, 'render_shortcode' ] );
        add_shortcode( 'mosaic_template', [ $this, 'render_shortcode' ] ); // backward compat
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_styles' ] );
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
                true
            );
            wp_enqueue_script(
                'uikit-icons-js',
                OLO_URL . 'assets/vendor/uikit/js/uikit-icons.min.js',
                [ 'uikit-js' ],
                '3.21.16',
                true
            );

            // Olobuilder custom overrides (loaded after UIkit)
            wp_enqueue_style(
                'olo-frontend-css',
                OLO_URL . 'assets/css/frontend.css',
                [ 'uikit-css' ],
                OLO_VERSION
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
            $angle = intval( $bg['gradient_angle'] ?? 180 );
            $from  = esc_attr( $bg['gradient_from'] ?? '#ffffff' );
            $to    = esc_attr( $bg['gradient_to'] ?? '#000000' );
            return "background: linear-gradient({$angle}deg, {$from}, {$to})";
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

    private function generate_id() {
        if ( function_exists( 'wp_generate_uuid4' ) ) {
            return wp_generate_uuid4();
        }
        return 'tile-' . uniqid() . '-' . substr( md5( mt_rand() ), 0, 8 );
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
    private function should_render_node( $node ) {
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

        return true;
    }

    /**
     * Render a node (recursive dispatcher).
     */
    private function render_node( $node, $manager, $template_id, &$hover_css_rules, &$tile_counter ) {
        if ( ! $this->should_render_node( $node ) ) return '';

        $type = $node['type'] ?? '';
        switch ( $type ) {
            case 'section':
                return $this->render_section_node( $node, $manager, $template_id, $hover_css_rules, $tile_counter );
            case 'row':
                return $this->render_row_node( $node, $manager, $template_id, $hover_css_rules, $tile_counter );
            case 'column':
                return $this->render_column_node( $node, $manager, $template_id, $hover_css_rules, $tile_counter );
            case 'inner-columns':
                return $this->render_inner_columns_node( $node, $manager, $template_id, $hover_css_rules, $tile_counter );
            case 'inner-column':
                return $this->render_inner_column_node( $node, $manager, $template_id, $hover_css_rules, $tile_counter );
            default:
                return $this->render_element_node( $node, $manager, $template_id, $hover_css_rules, $tile_counter );
        }
    }

    /**
     * Render a Section container using UIkit classes.
     */
    private function render_section_node( $node, $manager, $template_id, &$hover_css_rules, &$tile_counter ) {
        $s = $node['settings'] ?? [];
        $style    = $node['style'] ?? [];
        $advanced = $node['advanced'] ?? [];

        // Sticky effect
        $sticky_effect = $s['sticky_effect'] ?? 'none';
        $is_sticky_v = in_array( $sticky_effect, [ 'cover', 'reveal' ], true );
        $is_sticky_h = in_array( $sticky_effect, [ 'cover-h', 'reveal-h' ], true );
        $is_sticky = $is_sticky_v || $is_sticky_h;
        $sticky_top = intval( $s['sticky_top'] ?? 0 );

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
        $has_bg_image = ( $tile_bg['type'] === 'image' && ! empty( $tile_bg['image_url'] ) );
        $has_bg_video = ( $tile_bg['type'] === 'video' && ! empty( $tile_bg['video_url'] ) );
        $has_bg_any   = ( $tile_bg['type'] !== 'none' );
        $has_overlay  = ( $has_bg_any && ! empty( $tile_bg['overlay_opacity'] ) && intval( $tile_bg['overlay_opacity'] ) > 0 );

        if ( $has_bg_image || $has_bg_video ) {
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

        // Overflow clip needed for border-radius clipping (clip instead of hidden to preserve sticky)
        if ( ! empty( $style['border_radius'] ) ) {
            $inline_styles[] = 'overflow: clip';
        }

        if ( ! empty( $advanced['custom_css'] ) ) {
            $inline_styles[] = $advanced['custom_css'];
        }

        // HTML ID (always generate for hover CSS support)
        $tile_counter++;
        $css_id  = ! empty( $advanced['html_id'] ) ? $advanced['html_id'] : 'ms-' . $template_id . '-' . $tile_counter;
        $id_attr = ' id="' . esc_attr( $css_id ) . '"';

        // Hover CSS rules
        $this->collect_hover_css( $style, $css_id, false, $hover_css_rules );
        $this->collect_responsive_css( $style, $css_id );

        // Scrollspy & element parallax attributes
        $scrollspy_attr = $this->build_scrollspy_attr( $advanced );
        $el_parallax_attr = $this->build_element_parallax_attr( $advanced );

        $html = '<div class="' . esc_attr( implode( ' ', $classes ) ) . '"' . $id_attr;
        if ( $inline_styles ) {
            $html .= ' style="' . esc_attr( implode( '; ', $inline_styles ) ) . '"';
        }
        $html .= $scrollspy_attr . $el_parallax_attr . '>';

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
            $vid_cover  = ( ! empty( $tile_bg['cover_height'] ) && intval( $tile_bg['cover_height'] ) > 0 ) ? intval( $tile_bg['cover_height'] ) : 0;
            $vid_scale  = ( ! empty( $tile_bg['video_scale'] ) && intval( $tile_bg['video_scale'] ) > 100 ) ? intval( $tile_bg['video_scale'] ) / 100 : 0;
            $scale_css  = $vid_scale ? '; transform: scale(' . $vid_scale . '); transform-origin: ' . $vid_pos : '';
            if ( $vid_cover ) {
                $html .= '<video style="position: absolute; top: 0; left: 0; width: 100%; height: ' . $vid_cover . 'px; object-fit: cover; object-position: ' . $vid_pos . '; pointer-events: none' . $scale_css . '" autoplay muted loop playsinline';
            } else {
                $html .= '<video class="uk-position-cover" style="object-fit: cover; object-position: ' . $vid_pos . '; pointer-events: none' . $scale_css . '" autoplay muted loop playsinline';
            }
            if ( $vid_poster ) $html .= ' poster="' . $vid_poster . '"';
            $html .= '><source src="' . $vid_url . '" type="' . $this->get_video_mime( $vid_url ) . '"></video>';
        }

        // Overlay layer
        if ( $has_overlay ) {
            $ov_color   = esc_attr( $tile_bg['overlay_color'] ?? '#000000' );
            $ov_opacity = intval( $tile_bg['overlay_opacity'] ) / 100;
            $html .= '<div class="uk-position-cover" style="background-color: ' . $ov_color . '; opacity: ' . $ov_opacity . '; pointer-events: none"></div>';
        }

        // Container width wrapper (relative for z-index above bg/overlay)
        $width = $s['width'] ?? 'default';

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

        if ( $has_bg_image || $has_bg_video || $has_overlay ) {
            $container_class .= ' uk-position-relative';
            $html .= '<div class="' . esc_attr( $container_class ) . '" style="z-index: 1">';
        } else {
            $html .= '<div class="' . esc_attr( $container_class ) . '">';
        }

        foreach ( $node['children'] ?? [] as $child ) {
            $html .= $this->render_node( $child, $manager, $template_id, $hover_css_rules, $tile_counter );
        }

        $html .= '</div></div>';

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
        $stack  = ! empty( $s['stack_mobile'] );

        // Background handling
        $tile_bg      = $this->get_effective_bg( $style );
        $has_bg_image = ( $tile_bg['type'] === 'image' && ! empty( $tile_bg['image_url'] ) );
        $has_bg_video = ( $tile_bg['type'] === 'video' && ! empty( $tile_bg['video_url'] ) );
        $has_bg_any   = ( $tile_bg['type'] !== 'none' );
        $has_overlay  = ( $has_bg_any && ! empty( $tile_bg['overlay_opacity'] ) && intval( $tile_bg['overlay_opacity'] ) > 0 );

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

        // Wrapper for row background or spacing
        $has_border_radius = ! empty( $style['border_radius'] );
        $has_border = ! empty( $style['border_width'] ) && intval( $style['border_width'] ) > 0;
        $has_opacity = ! empty( $style['opacity'] ) && intval( $style['opacity'] ) < 100;
        $has_shadow = ! empty( $style['shadow'] );
        $has_spacing = ! empty( $row_spacing_styles );
        $has_hover   = ! empty( $style['hover'] ) && is_array( $style['hover'] ) && array_filter( $style['hover'], function( $v ) { return $v !== null && $v !== '' && $v !== false; } );
        $needs_wrapper = $has_bg_image || $has_bg_video || $has_overlay || ( $tile_bg['type'] !== 'none' ) || $has_spacing || $has_border_radius || $has_border || $has_opacity || $has_shadow || $has_hover;

        // ID for hover CSS support
        $tile_counter++;
        $row_css_id = ! empty( $advanced['html_id'] ) ? $advanced['html_id'] : 'mr-' . $template_id . '-' . $tile_counter;

        // Hover CSS rules
        $this->collect_hover_css( $style, $row_css_id, false, $hover_css_rules );
        $this->collect_responsive_css( $style, $row_css_id );

        $wrapper_styles = [];
        $wrapper_classes = [];

        if ( $needs_wrapper ) {
            if ( $has_bg_image || $has_bg_video ) {
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

        // Scrollspy & element parallax attributes for row
        $row_scrollspy_attr = $this->build_scrollspy_attr( $advanced );
        $row_el_parallax_attr = $this->build_element_parallax_attr( $advanced );

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
                $vid_cover  = ( ! empty( $tile_bg['cover_height'] ) && intval( $tile_bg['cover_height'] ) > 0 ) ? intval( $tile_bg['cover_height'] ) : 0;
                $vid_scale  = ( ! empty( $tile_bg['video_scale'] ) && intval( $tile_bg['video_scale'] ) > 100 ) ? intval( $tile_bg['video_scale'] ) / 100 : 0;
                $scale_css  = $vid_scale ? '; transform: scale(' . $vid_scale . '); transform-origin: ' . $vid_pos : '';
                if ( $vid_cover ) {
                    $html .= '<video style="position: absolute; top: 0; left: 0; width: 100%; height: ' . $vid_cover . 'px; object-fit: cover; object-position: ' . $vid_pos . '; pointer-events: none' . $scale_css . '" autoplay muted loop playsinline';
                } else {
                    $html .= '<video class="uk-position-cover" style="object-fit: cover; object-position: ' . $vid_pos . '; pointer-events: none' . $scale_css . '" autoplay muted loop playsinline';
                }
                if ( $vid_poster ) $html .= ' poster="' . $vid_poster . '"';
                $html .= '><source src="' . $vid_url . '" type="' . $this->get_video_mime( $vid_url ) . '"></video>';
            }

            // Overlay layer
            if ( $has_overlay ) {
                $ov_color   = esc_attr( $tile_bg['overlay_color'] ?? '#000000' );
                $ov_opacity = intval( $tile_bg['overlay_opacity'] ) / 100;
                $html .= '<div class="uk-position-cover" style="background-color: ' . $ov_color . '; opacity: ' . $ov_opacity . '; pointer-events: none"></div>';
            }
        }

        // Grid — if no wrapper, put scrollspy/parallax on the grid div itself
        $grid_extra_attrs = $needs_wrapper ? '' : ( $row_scrollspy_attr . $row_el_parallax_attr );
        if ( $needs_wrapper && ( $has_bg_image || $has_bg_video || $has_overlay ) ) {
            $html .= '<div' . $class_attr . ' ' . $uk_grid . ' style="position: relative; z-index: 1">';
        } else {
            $html .= '<div' . $class_attr . ' ' . $uk_grid . $grid_extra_attrs . '>';
        }

        foreach ( $node['children'] ?? [] as $child ) {
            $html .= $this->render_node( $child, $manager, $template_id, $hover_css_rules, $tile_counter );
        }

        $html .= '</div>';

        // Close row wrapper
        if ( $needs_wrapper ) {
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * Render a Column using UIkit width classes.
     */
    private function render_column_node( $node, $manager, $template_id, &$hover_css_rules, &$tile_counter ) {
        $s        = $node['settings'] ?? [];
        $style    = $node['style'] ?? [];
        $advanced = $node['advanced'] ?? [];

        // UIkit width classes per breakpoint: uk-width-{fraction}@{breakpoint}
        $classes = [];

        $width_custom  = $s['width_custom'] ?? '';
        $width_default = $s['width_default'] ?? '';
        $width_small   = $s['width_small'] ?? '';
        $width_medium  = $s['width_medium'] ?? '';
        $width_large   = $s['width_large'] ?? '';

        if ( $width_custom !== '' && floatval( $width_custom ) > 0 ) {
            // Custom percentage column: use 1-1 for mobile stack, desktop width handled by parent row <style>
            $classes[] = 'uk-width-1-1';
        } else {
            // Map fraction notation to UIkit: '1-2' → 'uk-width-1-2', '2-3' → 'uk-width-2-3'
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

            // Fallback: if no classes set, use auto-expand
            if ( empty( $classes ) ) {
                $classes[] = 'uk-width-expand';
            }
        }

        // Column margin/padding
        $inline_styles = [];
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

        // ID for hover CSS support
        $tile_counter++;
        $col_css_id = ! empty( $advanced['html_id'] ) ? $advanced['html_id'] : 'mc-' . $template_id . '-' . $tile_counter;

        // Hover CSS rules
        $this->collect_hover_css( $style, $col_css_id, false, $hover_css_rules );
        $this->collect_responsive_css( $style, $col_css_id );

        // Scrollspy & element parallax attributes for column
        $col_scrollspy_attr = $this->build_scrollspy_attr( $advanced );
        $col_el_parallax_attr = $this->build_element_parallax_attr( $advanced );

        $html = '<div id="' . esc_attr( $col_css_id ) . '" class="' . esc_attr( implode( ' ', $classes ) ) . '"';
        if ( ! empty( $inline_styles ) ) {
            $html .= ' style="' . esc_attr( implode( '; ', $inline_styles ) ) . '"';
        }
        $html .= $col_scrollspy_attr . $col_el_parallax_attr . '>';

        foreach ( $node['children'] ?? [] as $child ) {
            $html .= $this->render_node( $child, $manager, $template_id, $hover_css_rules, $tile_counter );
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
        $type = $node['type'] ?? '';
        $tile_instance = $manager->get_tile( $type );
        if ( ! $tile_instance ) return '';

        $settings = $node['settings'] ?? [];
        $style    = $node['style'] ?? [];
        $advanced = $node['advanced'] ?? [];

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
                if ( strpos( $field_key, '_' ) === 0 ) continue; // skip _query, _itemMap
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

        // UIkit shadow classes
        $shadow_class = '';
        if ( ! empty( $style['shadow'] ) ) {
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
        if ( $has_bg_image || $has_bg_video || $has_overlay ) { $classes[] = 'uk-position-relative'; $inline_styles[] = 'overflow: clip'; }
        if ( ! empty( $style['border_radius'] ) ) $inline_styles[] = 'overflow: clip';
        if ( ! empty( $advanced['css_classes'] ) ) {
            $classes[] = esc_attr( $advanced['css_classes'] );
        }

        // UIkit visibility classes
        if ( isset( $advanced['visible_desktop'] ) && $advanced['visible_desktop'] === false ) {
            $classes[] = 'uk-hidden@l';
        }
        if ( isset( $advanced['visible_tablet'] ) && $advanced['visible_tablet'] === false ) {
            // Hide between s and l breakpoints (tricky - use custom class)
            $classes[] = 'olo-hidden-tablet';
        }
        if ( isset( $advanced['visible_mobile'] ) && $advanced['visible_mobile'] === false ) {
            $classes[] = 'uk-visible@s';
        }

        // HTML ID
        $tile_counter++;
        $css_id  = ! empty( $advanced['html_id'] ) ? $advanced['html_id'] : 'mt-' . $template_id . '-' . $tile_counter;
        $id_attr = ' id="' . esc_attr( $css_id ) . '"';

        // Hover CSS rules
        $this->collect_hover_css( $style, $css_id, $is_fullwidth, $hover_css_rules );
        $this->collect_responsive_css( $style, $css_id );

        // Scrollspy & element parallax attributes
        $elem_scrollspy_attr = $this->build_scrollspy_attr( $advanced );
        $elem_el_parallax_attr = $this->build_element_parallax_attr( $advanced );

        // Render
        ob_start();
        ?>
        <div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php echo $id_attr; ?> style="<?php echo esc_attr( $style_attr ); ?>"<?php echo $elem_scrollspy_attr . $elem_el_parallax_attr; ?>>
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
            ?>
                <video class="uk-position-cover" style="object-fit: cover; object-position: <?php echo $vid_pos; ?>; pointer-events: none" autoplay muted loop playsinline<?php if ( $vid_poster ) echo ' poster="' . $vid_poster . '"'; ?>><source src="<?php echo $vid_url; ?>" type="<?php echo esc_attr( $this->get_video_mime( $vid_url ) ); ?>"></video>
            <?php endif; ?>

            <?php if ( $has_overlay ) :
                $ov_color   = esc_attr( $tile_bg['overlay_color'] ?? '#000000' );
                $ov_opacity = intval( $tile_bg['overlay_opacity'] ) / 100;
            ?>
                <div class="uk-position-cover" style="background-color: <?php echo $ov_color; ?>; opacity: <?php echo $ov_opacity; ?>; pointer-events: none"></div>
            <?php endif; ?>

            <?php if ( $has_bg_image || $has_bg_video || $has_overlay ) : ?>
                <div class="uk-position-relative" style="z-index: 1">
                    <?php echo $tile_instance->render( $settings ); ?>
                </div>
            <?php else : ?>
                <?php echo $tile_instance->render( $settings ); ?>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Collect hover CSS rules for an element.
     */
    private function collect_hover_css( $style, $css_id, $is_fullwidth, &$hover_css_rules ) {
        $hover = $style['hover'] ?? [];
        $transition_cfg = $style['transition'] ?? [];
        $has_hover = false;
        $hover_decls = [];

        if ( ! empty( $hover['bg_color'] ) ) {
            $hover_decls[] = 'background-color: ' . esc_attr( $hover['bg_color'] );
            $has_hover = true;
        }
        if ( ! empty( $hover['border_color'] ) ) {
            $hover_decls[] = 'border-color: ' . esc_attr( $hover['border_color'] );
            $has_hover = true;
        }
        if ( ! empty( $hover['shadow'] ) && isset( $this->shadow_map[ $hover['shadow'] ] ) ) {
            $hover_decls[] = 'box-shadow: ' . $this->shadow_map[ $hover['shadow'] ];
            $has_hover = true;
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
        if ( isset( $hover['transform_translateY'] ) && $hover['transform_translateY'] !== null ) {
            $h_transforms[] = 'translateY(' . intval( $hover['transform_translateY'] ) . 'px)';
            $has_hover = true;
        }
        if ( isset( $hover['transform_rotate'] ) && $hover['transform_rotate'] !== null ) {
            $h_transforms[] = 'rotate(' . intval( $hover['transform_rotate'] ) . 'deg)';
            $has_hover = true;
        }
        if ( ! empty( $h_transforms ) ) {
            $hover_decls[] = 'transform: ' . implode( ' ', $h_transforms );
        }

        if ( $has_hover ) {
            $dur  = intval( $transition_cfg['duration'] ?? 300 );
            $ease = esc_attr( $transition_cfg['easing'] ?? 'ease' );
            $trans_props = [ 'background-color', 'border-color', 'box-shadow', 'opacity', 'transform' ];
            $trans_val = implode( ', ', array_map( function( $p ) use ( $dur, $ease ) {
                return "{$p} {$dur}ms {$ease}";
            }, $trans_props ) );

            $sel = '#' . esc_attr( $css_id );
            $hover_css_rules[] = "{$sel} { transition: {$trans_val}; }";
            $hover_css_rules[] = "{$sel}:hover { " . implode( '; ', $hover_decls ) . "; }";
        }
    }

    /**
     * Collect responsive spacing CSS rules for tablet and mobile.
     * Reads style keys like margin_top_tablet, padding_left_mobile, etc.
     */
    private function collect_responsive_css( $style, $css_id ) {
        foreach ( [ 'tablet' => '960px', 'mobile' => '640px' ] as $bp => $max_width ) {
            $decls = [];
            foreach ( [ 'margin', 'padding' ] as $prop ) {
                foreach ( [ 'top', 'right', 'bottom', 'left' ] as $side ) {
                    $key = "{$prop}_{$side}_{$bp}";
                    if ( ! empty( $style[ $key ] ) ) {
                        $decls[] = "{$prop}-{$side}: " . intval( $style[ $key ] ) . 'px';
                    }
                }
            }
            if ( ! empty( $decls ) ) {
                $sel = '#' . esc_attr( $css_id );
                $this->responsive_css_rules[ $max_width ][] = "{$sel} { " . implode( '; ', $decls ) . "; }";
            }
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

        // Page settings
        $page_settings     = $template['settings'] ?? [];
        $page_settings     = apply_filters( 'olo_template_settings', $page_settings, $id );
        $content_max_width = intval( $page_settings['content_max_width'] ?? 1200 );
        $page_bg           = $page_settings['page_bg'] ?? [ 'type' => 'none' ];

        // Post Grid detection (recursive)
        if ( $this->check_postgrid_recursive( $tiles ) ) {
            wp_enqueue_script(
                'olo-postgrid-js',
                OLO_URL . 'assets/js/olo-postgrid.js',
                [],
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
                [ 'leaflet-js', 'leaflet-markercluster-js' ],
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
                [],
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
                [ 'leaflet-js', 'leaflet-markercluster-js' ],
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

        $manager = Olo_Tile_Manager::instance();

        $page_bg_css = $this->get_bg_inline_css( $page_bg );
        $hover_css_rules = [];
        $this->responsive_css_rules = [];
        $tile_counter = 0;

        ob_start();
        ?>
        <div class="olo-template olo-template-<?php echo esc_attr( $id ); ?>"<?php
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
            ?>
                <video class="olo-tile-bg" style="object-fit: cover; object-position: <?php echo $vid_pos; ?>; pointer-events: none" autoplay muted loop playsinline<?php if ( $vid_poster ) echo ' poster="' . $vid_poster . '"'; ?>><source src="<?php echo esc_url( $page_bg['video_url'] ); ?>" type="<?php echo esc_attr( $this->get_video_mime( $page_bg['video_url'] ) ); ?>"></video>
            <?php endif; ?>
            <?php
            // Page overlay
            if ( $page_bg['type'] !== 'none' && ! empty( $page_bg['overlay_opacity'] ) && intval( $page_bg['overlay_opacity'] ) > 0 ) :
                $ov_color   = esc_attr( $page_bg['overlay_color'] ?? '#000000' );
                $ov_opacity = intval( $page_bg['overlay_opacity'] ) / 100;
            ?>
                <div class="olo-tile-overlay" style="background-color: <?php echo $ov_color; ?>; opacity: <?php echo $ov_opacity; ?>"></div>
            <?php endif; ?>

            <div class="olo-frontend-grid olo-tile-content" style="--olo-content-width: <?php echo $content_max_width >= 9999 ? '100%' : $content_max_width . 'px'; ?>; --olo-container-max-width: <?php echo $content_max_width >= 9999 ? 'none' : $content_max_width . 'px'; ?>"><?php // per-template override of global container width ?>
                <?php
                foreach ( $tiles as $section ) {
                    echo $this->render_node( $section, $manager, $id, $hover_css_rules, $tile_counter );
                }
                ?>
            </div>
            <?php if ( ! empty( $hover_css_rules ) || ! empty( $this->responsive_css_rules ) ) : ?>
                <style class="olo-hover-styles"><?php
                    echo implode( ' ', $hover_css_rules );
                    foreach ( $this->responsive_css_rules as $max_w => $rules ) {
                        echo ' @media(max-width:' . $max_w . '){' . implode( ' ', $rules ) . '}';
                    }
                ?></style>
            <?php endif; ?>
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
                    function findSibling(siblings,from,dir){
                        var i=from+dir;
                        while(i>=0&&i<siblings.length){
                            var s=siblings[i];
                            if(getSec(s))return s;
                            i+=dir;
                        }
                        return null;
                    }
                    function initHGroups(){
                        document.querySelectorAll('.olo-h-marker').forEach(function(marker){
                            if(marker.dataset.oloHDone)return;
                            marker.dataset.oloHDone='1';
                            var mode=marker.dataset.stickyH;
                            var stickyTop=parseInt(marker.dataset.stickyTop)||0;
                            var mySec=getSec(marker);
                            if(!mySec)return;
                            var parent=marker.parentNode;
                            var siblings=Array.from(parent.children);
                            var idx=siblings.indexOf(marker);
                            /* cover-h: this + next; reveal-h: prev + this */
                            var pairEl=findSibling(siblings,idx,mode==='cover-h'?1:-1);
                            if(!pairEl)return;
                            var pairSec=getSec(pairEl);
                            if(!pairSec)return;
                            var secA=mode==='cover-h'?mySec:pairSec;
                            var secB=mode==='cover-h'?pairSec:mySec;
                            /* Mark pair as processed too */
                            if(pairEl.dataset)pairEl.dataset.oloHDone='1';
                            /* Build horizontal scroll group */
                            var viewH=window.innerHeight-stickyTop;
                            var group=document.createElement('div');
                            group.className='olo-h-group';
                            group.style.height=(viewH*2)+'px';
                            var viewport=document.createElement('div');
                            viewport.className='olo-h-viewport';
                            viewport.style.top=stickyTop+'px';
                            viewport.style.height=viewH+'px';
                            var track=document.createElement('div');
                            track.className='olo-h-track';
                            track.style.height='100%';
                            var cA=secA.cloneNode(true);
                            var cB=secB.cloneNode(true);
                            var panelCSS='flex:0 0 100%;width:100%;min-height:'+viewH+'px;box-sizing:border-box;position:relative;overflow:hidden';
                            cA.style.cssText+=';'+panelCSS;
                            cB.style.cssText+=';'+panelCSS;
                            track.appendChild(cA);
                            track.appendChild(cB);
                            viewport.appendChild(track);
                            group.appendChild(viewport);
                            /* Insert and hide originals */
                            var first=mode==='cover-h'?marker:pairEl;
                            first.parentNode.insertBefore(group,first);
                            marker.style.display='none';
                            pairEl.style.display='none';
                            /* Scroll-linked translateX */
                            var ticking=false;
                            function onScroll(){
                                if(!ticking){ticking=true;requestAnimationFrame(function(){
                                    var rect=group.getBoundingClientRect();
                                    var scrolled=-rect.top;
                                    var travel=group.offsetHeight-viewH;
                                    var p=Math.max(0,Math.min(1,scrolled/travel));
                                    track.style.transform='translateX('+ (-p*100) +'%)';
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
        </div>
        <?php
        return ob_get_clean();
    }
}
