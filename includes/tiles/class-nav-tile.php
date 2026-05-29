<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Nav_Tile extends Olo_Tile_Base {

    protected $type     = 'nav';
    protected $name     = 'Nav';
    protected $icon     = 'dashicons-menu';
    protected $category = 'navigation';
    protected $defaults = [
        'items'            => [],
        'style'            => 'default',
        'direction'        => 'vertical',
        'alignment'        => 'left',
        'show_icons'       => true,
        'icon_position'    => 'left',
        'icon_size'        => '16',
        'font_size'        => '14',
        'font_weight'      => '400',
        'text_transform'   => 'none',
        'letter_spacing'   => '0',
        'link_color'       => '',
        'link_hover_color' => '',
        'active_color'     => '',
        'icon_color'       => '',
        'separator'        => false,
        'separator_color'  => '',
        'gap'              => '4',
        'padding_x'        => '12',
        'padding_y'        => '8',
        'border_radius'    => '6',
        'active_style'     => 'left-border',
        'active_bg'        => '',
        'hover_bg'         => '',
        'hover_effect'     => 'none',
        'open_in_new_tab'  => false,
        'nofollow'         => false,
            'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s     = wp_parse_args( $settings, $this->defaults );
        $items = $this->parse_items( $s['items'] );
        $count = count( $items );

        if ( $count === 0 ) {
            return '';
        }

        $is_horizontal = $s['direction'] === 'horizontal';
        $show_icons    = ! empty( $s['show_icons'] );
        $show_sep      = ! empty( $s['separator'] );
        $target        = ! empty( $s['open_in_new_tab'] ) ? ' target="_blank"' : '';
        $rel_parts     = [];
        if ( ! empty( $s['open_in_new_tab'] ) ) {
            $rel_parts[] = 'noopener';
        }
        if ( ! empty( $s['nofollow'] ) ) {
            $rel_parts[] = 'nofollow';
        }
        $rel_attr = $rel_parts ? ' rel="' . esc_attr( implode( ' ', $rel_parts ) ) . '"' : '';

        // Colors with fallbacks
        $link_color  = $s['link_color'] ?: 'var(--olo-color-text-muted, #9CA3AF)';
        $hover_color = $s['link_hover_color'] ?: 'var(--olo-color-border, #E5E7EB)';
        $active_color = $s['active_color'] ?: 'var(--olo-color-primary, #e1474f)';
        $icon_color  = $s['icon_color'] ?: '';
        $hover_bg    = $s['hover_bg'] ?: '';
        $active_bg   = $s['active_bg'] ?: '';
        $sep_color   = $s['separator_color'] ?: 'var(--olo-color-text, #374151)';

        // Dimensions
        $px     = intval( $s['padding_x'] );
        $py     = intval( $s['padding_y'] );
        $gap    = intval( $s['gap'] );
        $radius = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );
        $fs     = intval( $s['font_size'] );
        $fw     = esc_attr( $s['font_weight'] );
        $tt     = esc_attr( $s['text_transform'] );
        $ls     = floatval( $s['letter_spacing'] );
        $icon_s = intval( $s['icon_size'] );

        // Unique ID for scoped CSS
        $uid = 'olo-nav-' . wp_unique_id();

        // Build inline CSS
        $css = '';

        // Container
        $direction_css = $is_horizontal ? 'flex-direction:row;flex-wrap:wrap;align-items:center' : 'flex-direction:column';
        $align_map = [ 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end', 'stretch' => 'stretch' ];
        $align_prop = $is_horizontal ? 'justify-content' : 'align-items';
        $align_val  = $align_map[ $s['alignment'] ] ?? 'flex-start';

        $css .= "#{$uid}{display:flex;{$direction_css};gap:{$gap}px;{$align_prop}:{$align_val};list-style:none;padding:0;margin:0}";

        // Items base
        $css .= "#{$uid} .olo-nav-item{display:flex;align-items:center;gap:8px;padding:{$py}px {$px}px;";
        $css .= "border-radius:{$radius};font-size:{$fs}px;font-weight:{$fw};text-transform:{$tt};";
        $css .= "letter-spacing:{$ls}px;color:{$link_color};text-decoration:none;transition:all .2s ease;position:relative}";

        // a11y tastiera: anello di focus visibile sulle voci di menu
        $css .= "#{$uid} .olo-nav-item:focus-visible{outline:none;box-shadow:0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent)}";

        // Stretch
        if ( $s['alignment'] === 'stretch' && ! $is_horizontal ) {
            $css .= "#{$uid} .olo-nav-item{width:100%}";
        }

        // Hover
        $hover_rules = "color:{$hover_color}";
        if ( $hover_bg ) {
            $hover_rules .= ";background:{$hover_bg}";
        } elseif ( $s['hover_effect'] === 'slide-bg' ) {
            $hover_rules .= ';background:rgba(0,0,0,.04)';
        }
        if ( $s['hover_effect'] === 'lift' ) {
            $hover_rules .= ';transform:translateY(-1px)';
        }
        if ( $radius_hover_css !== '' ) {
            $hover_rules .= ";border-radius:{$radius_hover_css} !important";
        }
        if ( $s['hover_effect'] === 'underline' ) {
            $hover_rules .= ';text-decoration:underline;text-underline-offset:4px';
        }
        $css .= "#{$uid} .olo-nav-item:hover{{$hover_rules}}";

        // Active
        $active_rules = "color:{$active_color}";
        $ast = $s['active_style'];
        if ( $ast === 'left-border' ) {
            $active_rules .= ";border-left:3px solid {$active_color}";
        } elseif ( $ast === 'bottom-border' ) {
            $active_rules .= ";border-bottom:2px solid {$active_color}";
        } elseif ( $ast === 'background' ) {
            // Tinta soft token-safe (color-mix invece di concat hex-alpha, incompatibile con var())
            $bg = $active_bg ?: "color-mix(in srgb, {$active_color} 8%, transparent)";
            $active_rules .= ";background:{$bg}";
        } elseif ( $ast === 'bold' ) {
            $active_rules .= ';font-weight:700';
        }
        $css .= "#{$uid} .olo-nav-item.uk-active,.olo-nav-current{{$active_rules}}";

        // Style preset overrides
        $style = $s['style'];
        if ( $style === 'pill' ) {
            $css .= "#{$uid} .olo-nav-item{border-radius:999px}";
            $active_pill_bg = $active_bg ?: "color-mix(in srgb, {$active_color} 12%, transparent)";
            $css .= "#{$uid} .olo-nav-item.uk-active{background:{$active_pill_bg}}";
        } elseif ( $style === 'boxed' ) {
            $css .= "#{$uid} .olo-nav-item{border:1px solid rgba(0,0,0,.08)}";
            $active_box_bg = $active_bg ?: "color-mix(in srgb, {$active_color} 6%, transparent)";
            $css .= "#{$uid} .olo-nav-item.uk-active{border-color:{$active_color};background:{$active_box_bg}}";
        } elseif ( $style === 'underline' ) {
            $css .= "#{$uid} .olo-nav-item{border-radius:0;border-bottom:2px solid transparent}";
            $css .= "#{$uid} .olo-nav-item.uk-active{border-bottom:2px solid {$active_color}}";
        } elseif ( $style === 'minimal' ) {
            $min_px = max( $px - 4, 4 );
            $css .= "#{$uid} .olo-nav-item{padding:{$py}px {$min_px}px}";
        }

        // Icon color
        if ( $icon_color ) {
            $css .= "#{$uid} .olo-nav-icon{color:{$icon_color}}";
        }
        $css .= "#{$uid} .olo-nav-item.uk-active .olo-nav-icon{color:{$active_color}}";

        // Separator
        if ( $show_sep ) {
            if ( $is_horizontal ) {
                $css .= "#{$uid} .olo-nav-sep{width:1px;align-self:stretch;background:{$sep_color}}";
            } else {
                $css .= "#{$uid} .olo-nav-sep{height:1px;background:{$sep_color};margin:0}";
            }
        }

        // Detect current URL for active state
        $current_url = isset( $_SERVER['REQUEST_URI'] ) ? home_url( $_SERVER['REQUEST_URI'] ) : '';
        $current_path = isset( $_SERVER['REQUEST_URI'] ) ? rtrim( strtok( $_SERVER['REQUEST_URI'], '?' ), '/' ) : '';

        $tag = $is_horizontal ? 'div' : 'ul';

        ob_start();
        echo '<style>' . $css . '</style>';
        ?>
        <nav role="navigation" aria-label="<?php echo esc_attr__( 'Navigation', 'olobuild' ); ?>">
        <<?php echo $tag; ?> id="<?php echo esc_attr( $uid ); ?>">
            <?php foreach ( $items as $idx => $item ) :
                $url   = ! empty( $item['url'] ) ? $item['url'] : '#';
                $label = ! empty( $item['label'] ) ? $item['label'] : '';
                $icon  = $show_icons && ! empty( $item['icon'] ) ? $item['icon'] : '';

                // Determine active state
                $item_path = rtrim( wp_parse_url( $url, PHP_URL_PATH ) ?: '', '/' );
                $is_active = ( $current_path !== '' && $item_path !== '' && $current_path === $item_path );
                $active_class = $is_active ? ' uk-active' : '';

                // Separator
                if ( $idx > 0 && $show_sep ) {
                    echo $is_horizontal
                        ? '<span class="olo-nav-sep" aria-hidden="true"></span>'
                        : '<li class="olo-nav-sep" aria-hidden="true"></li>';
                }

                $item_tag = $is_horizontal ? 'a' : 'li';
                if ( ! $is_horizontal ) {
                    echo '<li>';
                }
            ?>
                <a class="olo-nav-item<?php echo $active_class; ?>" href="<?php echo esc_url( $url ); ?>"<?php echo $target . $rel_attr; ?>>
                    <?php if ( $s['active_style'] === 'dot' && $is_active ) : ?>
                    <span style="width:6px;height:6px;border-radius:50%;background:currentColor;flex-shrink:0"></span>
                    <?php endif; ?>
                    <?php if ( $icon && $s['icon_position'] !== 'right' ) : ?>
                    <span class="olo-nav-icon" uk-icon="icon: <?php echo esc_attr( $icon ); ?>; width: <?php echo $icon_s; ?>; height: <?php echo $icon_s; ?>"></span>
                    <?php endif; ?>
                    <?php list( $nv_cls, $nv_data ) = $this->tfx_attrs( $s, 'title', $label ); ?>
                    <span class="olo-nav-text<?php echo $nv_cls; ?>"<?php echo $nv_data; ?>><?php echo esc_html( $label ); ?></span>
                    <?php if ( $icon && $s['icon_position'] === 'right' ) : ?>
                    <span class="olo-nav-icon" uk-icon="icon: <?php echo esc_attr( $icon ); ?>; width: <?php echo $icon_s; ?>; height: <?php echo $icon_s; ?>"></span>
                    <?php endif; ?>
                </a>
            <?php
                if ( ! $is_horizontal ) {
                    echo '</li>';
                }
            endforeach; ?>
        </<?php echo $tag; ?>>
        </nav>
        <?php
        $tfx_css = $this->tfx_css( $s, '#' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}";
            echo $border_hover_css . $border_effect_css . '</style>';
        }
        return ob_get_clean();
    }

    /**
     * Parse items from array format.
     */
    private function parse_items( $raw ) {
        if ( is_array( $raw ) ) {
            $items = [];
            foreach ( $raw as $item ) {
                if ( is_array( $item ) ) {
                    $items[] = [
                        'label' => $item['title'] ?? ( $item['label'] ?? '' ),
                        'url'   => $item['content'] ?? ( $item['url'] ?? '#' ),
                        'icon'  => $item['tag'] ?? ( $item['icon'] ?? '' ),
                    ];
                }
            }
            return $items;
        }
        return [];
    }
}
