<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Subnav_Tile extends Olobuild_Tile_Base {

    protected $type     = 'subnav';
    protected $name     = 'Subnav';
    protected $icon     = 'dashicons-ellipsis';
    protected $category = 'navigation';
    protected $defaults = [
        'source'            => 'manual',
        'items'             => [],
        'menu_id'           => 0,
        'menu_depth'        => 'top',
        'parent_item'       => '0',
        'style'             => 'default',
        'divider'           => false,
        'alignment'         => 'left',
        'gap'               => '8',
        'font_size'         => '14',
        'font_weight'       => '400',
        'text_transform'    => 'none',
        'link_color'        => '',
        'hover_color'       => '',
        'active_color'      => '',
        'active_style'      => 'none',
        'bg_color'          => '',
        'hover_bg'          => '',
        'active_bg'         => '',
        'border_radius'     => '4',
        'padding_x'         => '12',
        'padding_y'         => '6',
        'highlight_current' => true,
            'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() { return []; }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        // Get items from source
        if ( $s['source'] === 'wp_menu' ) {
            $items = $this->get_wp_menu_items( $s );
        } else {
            $items = $this->parse_manual_items( $s['items'] );
        }

        if ( empty( $items ) ) {
            return '';
        }

        $uid = 'olo-sn-' . wp_unique_id();
        $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
        $current_url = $request_uri !== '' ? rtrim( strtok( $request_uri, '?' ), '/' ) : '';
        $highlight   = ! empty( $s['highlight_current'] );

        // Colors (safe_color_css: solo formati colore CSS validi, '' altrimenti)
        $link_c   = $this->safe_color_css( $s['link_color'] ) ?: 'var(--olo-color-text-muted, #9CA3AF)';
        $hover_c  = $this->safe_color_css( $s['hover_color'] ) ?: '';
        $active_c = $this->safe_color_css( $s['active_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $bg_c     = $this->safe_color_css( $s['bg_color'] ) ?: '';
        $hover_bg = $this->safe_color_css( $s['hover_bg'] ) ?: '';
        $active_bg = $this->safe_color_css( $s['active_bg'] ) ?: '';

        // Dimensions
        $gap    = intval( $s['gap'] );
        $px     = intval( $s['padding_x'] );
        $py     = intval( $s['padding_y'] );
        $radius = Olobuild_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $radius_hover_css = Olobuild_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );
        $fs     = intval( $s['font_size'] );
        $fw     = esc_attr( $s['font_weight'] );
        $tt     = esc_attr( $s['text_transform'] );
        $style  = $s['style'];
        $ast    = $s['active_style'];

        // Alignment
        $align_map = [ 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end', 'stretch' => 'space-around' ];
        $justify   = $align_map[ $s['alignment'] ] ?? 'flex-start';

        // CSS
        $css = "#{$uid}{display:flex;flex-wrap:wrap;align-items:center;gap:{$gap}px;justify-content:{$justify};list-style:none;padding:0;margin:0}";
        $css .= "#{$uid} a{display:inline-block;padding:{$py}px {$px}px;border-radius:{$radius};font-size:{$fs}px;font-weight:{$fw};text-transform:{$tt};color:{$link_c};text-decoration:none;transition:all .2s ease";
        if ( $bg_c ) {
            $css .= ";background:{$bg_c}";
        }
        $css .= '}';
        // a11y tastiera: anello di focus visibile sulle voci di sotto-navigazione
        $css .= "#{$uid} a:focus-visible{outline:none;box-shadow:0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent)}";
        if ( $radius_hover_css !== '' ) $css .= "#{$uid} a:hover{border-radius:{$radius_hover_css} !important}";

        // Hover
        $hover_rules = $hover_c ? "color:{$hover_c}" : '';
        if ( $hover_bg ) {
            $hover_rules .= ( $hover_rules ? ';' : '' ) . "background:{$hover_bg}";
        }
        if ( $hover_rules ) {
            $css .= "#{$uid} a:hover{{$hover_rules}}";
        }

        // Active
        $active_rules = "color:{$active_c}";
        if ( $ast === 'underline' ) {
            $active_rules .= ";border-bottom:2px solid {$active_c}";
        } elseif ( $ast === 'background' ) {
            // Tinta soft token-safe (color-mix invece di concat hex-alpha, incompatibile con var())
            $abg = $active_bg ?: "color-mix(in srgb, {$active_c} 8%, transparent)";
            $active_rules .= ";background:{$abg}";
        } elseif ( $ast === 'bold' ) {
            $active_rules .= ';font-weight:700';
        }
        if ( $active_bg && $ast !== 'background' ) {
            $active_rules .= ";background:{$active_bg}";
        }
        $css .= "#{$uid} .olo-sn-active{{$active_rules}}";

        // Style presets
        if ( $style === 'pill' ) {
            $css .= "#{$uid} a{border-radius:999px}";
            $pill_bg = $active_bg ?: "color-mix(in srgb, {$active_c} 12%, transparent)";
            $css .= "#{$uid} .olo-sn-active{background:{$pill_bg}}";
        } elseif ( $style === 'underline' ) {
            $css .= "#{$uid} a{border-radius:0;border-bottom:2px solid transparent}";
            $css .= "#{$uid} .olo-sn-active{border-bottom:2px solid {$active_c}}";
        } elseif ( $style === 'boxed' ) {
            $css .= "#{$uid} a{border:1px solid rgba(0,0,0,.08)}";
            $box_bg = $active_bg ?: "color-mix(in srgb, {$active_c} 6%, transparent)";
            $css .= "#{$uid} .olo-sn-active{border-color:{$active_c};background:{$box_bg}}";
        }

        // Divider
        $divider = ! empty( $s['divider'] );
        if ( $divider ) {
            $css .= "#{$uid} .olo-sn-div{width:1px;align-self:stretch;background:rgba(0,0,0,.15)}";
        }

        ob_start();
        echo '<style>' . $css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS built exclusively from values sanitized above: safe_color_css()'d colors, intval()'d sizes, esc_attr()'d font props, whitelist-mapped alignment, Olobuild_Tile_Utils radius helpers; $uid is internally generated
        ?>
        <nav aria-label="<?php echo esc_attr( olobuild_t( 'Sotto-navigazione', 'olobuilder' ) ); ?>">
        <ul id="<?php echo esc_attr( $uid ); ?>">
            <?php foreach ( $items as $idx => $item ) :
                $url   = $item['url'] ?: '#';
                $label = $item['label'] ?: '';

                // Active detection
                $item_path = rtrim( wp_parse_url( $url, PHP_URL_PATH ) ?: '', '/' );
                $is_active = $highlight && $current_url !== '' && $item_path !== '' && $current_url === $item_path;
                $cls = $is_active ? ' class="olo-sn-active"' : '';
                $aria_current = $is_active ? ' aria-current="page"' : '';

                if ( $idx > 0 && $divider ) {
                    echo '<li class="olo-sn-div" aria-hidden="true"></li>';
                }
            ?>
            <li><a<?php echo $cls . $aria_current; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $cls and $aria_current are fixed internal literals (' class="olo-sn-active"' / ' aria-current="page"' or '') ?> href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a></li>
            <?php endforeach; ?>
        </ul>
        </nav>
        <?php
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }

    /**
     * Get items from WordPress menu.
     */
    private function get_wp_menu_items( $s ) {
        $menu_id = absint( $s['menu_id'] );
        if ( ! $menu_id ) return [];

        $all_items = wp_get_nav_menu_items( $menu_id );
        if ( ! $all_items || ! is_array( $all_items ) ) return [];

        $depth = $s['menu_depth'] ?: 'top';

        if ( $depth === 'top' ) {
            // Only top-level items
            $items = [];
            foreach ( $all_items as $item ) {
                if ( (int) $item->menu_item_parent === 0 ) {
                    $items[] = [
                        'label' => $item->title,
                        'url'   => $item->url,
                    ];
                }
            }
            return $items;
        }

        if ( $depth === 'children' ) {
            // Children of a specific menu item
            $parent_id = absint( $s['parent_item'] );
            if ( ! $parent_id ) return [];

            $items = [];
            foreach ( $all_items as $item ) {
                if ( (int) $item->menu_item_parent === $parent_id ) {
                    $items[] = [
                        'label' => $item->title,
                        'url'   => $item->url,
                    ];
                }
            }
            return $items;
        }

        if ( $depth === 'auto' ) {
            // Children of the current page's menu item
            $request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
            $current_url  = home_url( $request_uri );
            $current_path = rtrim( strtok( wp_parse_url( $current_url, PHP_URL_PATH ) ?: '', '?' ), '/' );

            // Find current item in menu
            $current_item_id = 0;
            foreach ( $all_items as $item ) {
                $item_path = rtrim( wp_parse_url( $item->url, PHP_URL_PATH ) ?: '', '/' );
                if ( $item_path === $current_path ) {
                    $current_item_id = $item->ID;
                    break;
                }
            }

            if ( ! $current_item_id ) return [];

            // Check if current item has children
            $children = [];
            foreach ( $all_items as $item ) {
                if ( (int) $item->menu_item_parent === $current_item_id ) {
                    $children[] = [
                        'label' => $item->title,
                        'url'   => $item->url,
                    ];
                }
            }

            // If no children, show siblings (children of parent)
            if ( empty( $children ) ) {
                $parent_id = 0;
                foreach ( $all_items as $item ) {
                    if ( $item->ID === $current_item_id ) {
                        $parent_id = (int) $item->menu_item_parent;
                        break;
                    }
                }
                if ( $parent_id ) {
                    foreach ( $all_items as $item ) {
                        if ( (int) $item->menu_item_parent === $parent_id ) {
                            $children[] = [
                                'label' => $item->title,
                                'url'   => $item->url,
                            ];
                        }
                    }
                }
            }

            return $children;
        }

        return [];
    }

    /**
     * Parse manual items.
     */
    private function parse_manual_items( $raw ) {
        if ( ! is_array( $raw ) ) return [];
        $items = [];
        foreach ( $raw as $item ) {
            if ( is_array( $item ) ) {
                $items[] = [
                    'label' => $item['title'] ?? ( $item['label'] ?? '' ),
                    'url'   => $item['content'] ?? ( $item['url'] ?? '#' ),
                ];
            }
        }
        return $items;
    }
}
