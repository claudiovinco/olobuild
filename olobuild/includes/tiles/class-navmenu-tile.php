<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_NavMenu_Tile extends Olo_Tile_Base {

    protected $type     = 'navmenu';
    protected $name     = 'Menu Nav';
    protected $icon     = 'dashicons-menu';
    protected $category = 'header';
    protected $defaults = [
        'menu_id'        => 0,
        'style'          => 'navbar',
        'alignment'      => 'left',
        'mobile_toggle'  => true,
        'mobile_style'   => 'offcanvas',
        'text_color'     => '',
        'hover_color'    => '',
        'active_color'   => '',
        'font_size'      => 15,
        'font_weight'    => 'normal',
        'text_transform' => 'none',
        'letter_spacing' => 0,
        'gap'            => 'medium',
        'dropdown_bg'    => '',
        'dropdown_color' => '',
        // Header
        'header_mode'       => 'overlay',
        // Sticky Header
        'sticky'            => false,
        'sticky_show_on_up' => false,
        'sticky_bg'         => '',
        'sticky_shadow'     => true,
        // Mega Menu
        'mega_menu'    => 'none',
        'mega_columns' => 3,
        // Button Items
        'button_items' => 'none',
        'button_style' => 'primary',
        'button_size'  => 'small',
        // Search in menu
        'append_search'      => false,
        'search_mode'        => 'modal',
        'search_placeholder' => 'Cerca...',
        'search_post_types'  => 'post,page',
        'search_max_results' => 10,
        // Stile ricerca mega menu
        'search_input_bg'      => '#ffffff',
        'search_input_color'   => '#374151',
        'search_icon_color'    => '#9ca3af',
        'search_border_color'  => '#e5e7eb',
        'search_input_height'  => 36,
        'search_results_bg'    => '#ffffff',
        'search_hover_bg'      => '#f3f4f6',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'menu_id', 'type' => 'select', 'label' => 'Menu' ],
            [ 'key' => 'style', 'type' => 'select', 'label' => 'Style', 'options' => [
                'navbar' => 'Horizontal Navbar',
                'subnav' => 'Subnav Links',
            ]],
            [ 'key' => 'alignment', 'type' => 'select', 'label' => 'Alignment', 'options' => [
                'left'   => 'Left',
                'center' => 'Center',
                'right'  => 'Right',
            ]],
            [ 'key' => 'text_color', 'type' => 'color', 'label' => 'Link Color' ],
            [ 'key' => 'hover_color', 'type' => 'color', 'label' => 'Hover Color' ],
            [ 'key' => 'active_color', 'type' => 'color', 'label' => 'Active Color' ],
            [ 'key' => 'font_size', 'type' => 'range', 'label' => 'Font Size', 'min' => 11, 'max' => 24 ],
            [ 'key' => 'font_weight', 'type' => 'select', 'label' => 'Font Weight', 'options' => [
                'normal' => 'Normal', '500' => 'Medium', '600' => 'Semi Bold', 'bold' => 'Bold',
            ]],
            [ 'key' => 'text_transform', 'type' => 'select', 'label' => 'Text Transform', 'options' => [
                'none' => 'None', 'uppercase' => 'Uppercase', 'capitalize' => 'Capitalize',
            ]],
            [ 'key' => 'letter_spacing', 'type' => 'range', 'label' => 'Letter Spacing', 'min' => 0, 'max' => 5, 'step' => 0.5 ],
            [ 'key' => 'gap', 'type' => 'select', 'label' => 'Items Gap', 'options' => [
                'small' => 'Small', 'medium' => 'Medium', 'large' => 'Large',
            ]],
            [ 'key' => 'dropdown_bg', 'type' => 'color', 'label' => 'Dropdown Background' ],
            [ 'key' => 'dropdown_color', 'type' => 'color', 'label' => 'Dropdown Text' ],
            [ 'key' => 'sticky', 'type' => 'toggle', 'label' => 'Sticky Header' ],
            [ 'key' => 'sticky_show_on_up', 'type' => 'toggle', 'label' => 'Show on Scroll Up' ],
            [ 'key' => 'sticky_bg', 'type' => 'color', 'label' => 'Sticky Background' ],
            [ 'key' => 'sticky_shadow', 'type' => 'toggle', 'label' => 'Sticky Shadow' ],
            [ 'key' => 'mega_menu', 'type' => 'select', 'label' => 'Mega Menu', 'options' => [
                'none' => 'Disabled', 'auto' => 'Auto', 'class' => 'CSS Class',
            ]],
            [ 'key' => 'mega_columns', 'type' => 'select', 'label' => 'Mega Columns', 'options' => [
                2 => '2 Columns', 3 => '3 Columns', 4 => '4 Columns',
            ]],
            [ 'key' => 'button_items', 'type' => 'select', 'label' => 'Button Items', 'options' => [
                'none' => 'None', 'last' => 'Last', 'last-2' => 'Last 2', 'css-class' => 'CSS Class',
            ]],
            [ 'key' => 'button_style', 'type' => 'select', 'label' => 'Button Style', 'options' => [
                'default' => 'Default', 'primary' => 'Primary', 'secondary' => 'Secondary',
            ]],
            [ 'key' => 'button_size', 'type' => 'select', 'label' => 'Button Size', 'options' => [
                'small' => 'Small', 'default' => 'Default',
            ]],
            [ 'key' => 'mobile_toggle', 'type' => 'toggle', 'label' => 'Mobile Hamburger' ],
            [ 'key' => 'mobile_style', 'type' => 'select', 'label' => 'Mobile Style', 'options' => [
                'offcanvas' => 'Offcanvas Panel',
                'dropdown'  => 'Dropdown',
            ]],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $menu_id = absint( $s['menu_id'] );
        if ( ! $menu_id ) {
            return '<div class="olo-navmenu"><p class="uk-text-muted uk-text-center">Select a menu in the Inspector panel.</p></div>';
        }

        $items = wp_get_nav_menu_items( $menu_id );
        if ( ! $items || ! is_array( $items ) ) {
            return '<div class="olo-navmenu"><p class="uk-text-muted uk-text-center">Menu is empty or not found.</p></div>';
        }

        // Build 3-level hierarchy
        $tree          = [];
        $children      = [];
        $grandchildren = [];
        $top_ids       = [];
        $child_ids     = [];

        // Level 1: top-level items (parent = 0)
        foreach ( $items as $item ) {
            if ( (int) $item->menu_item_parent === 0 ) {
                $tree[]               = $item;
                $top_ids[ $item->ID ] = true;
            }
        }
        // Level 2: children of top-level items
        foreach ( $items as $item ) {
            $pid = (int) $item->menu_item_parent;
            if ( $pid !== 0 && isset( $top_ids[ $pid ] ) ) {
                $children[ $pid ][]     = $item;
                $child_ids[ $item->ID ] = true;
            }
        }
        // Level 3: grandchildren (children of level-2 items)
        foreach ( $items as $item ) {
            $pid = (int) $item->menu_item_parent;
            if ( $pid !== 0 && ! isset( $top_ids[ $pid ] ) && isset( $child_ids[ $pid ] ) ) {
                $grandchildren[ $pid ][] = $item;
            }
        }

        $style     = $s['style'];
        $alignment = $s['alignment'];
        $mobile    = ! empty( $s['mobile_toggle'] );
        $mob_style = $s['mobile_style'];
        $nav_id    = 'olo-nav-' . $menu_id . '-' . mt_rand( 100, 999 );

        // Build scoped CSS for custom styles
        $scoped_css = $this->build_scoped_css( $s, $nav_id );

        ob_start();

        if ( $scoped_css ) {
            echo '<style>' . $scoped_css . '</style>';
        }

        if ( $style === 'subnav' ) {
            $this->render_subnav( $tree, $alignment, $s, $nav_id );
        } else {
            $this->render_navbar( $tree, $children, $grandchildren, $alignment, $mobile, $mob_style, $nav_id, $s );
        }

        // Header mode + sticky script
        $this->render_header_script( $s );

        return ob_get_clean();
    }

    /**
     * Build scoped CSS from style settings.
     */
    private function build_scoped_css( $s, $nav_id ) {
        $rules   = [];
        $sel     = '.olo-navmenu--' . esc_attr( $nav_id );
        $font_size      = absint( $s['font_size'] );
        $font_weight    = esc_attr( $s['font_weight'] );
        $text_transform = esc_attr( $s['text_transform'] );
        $letter_spacing = floatval( $s['letter_spacing'] );
        $text_color     = $this->safe_color( $s['text_color'] );
        $hover_color    = $this->safe_color( $s['hover_color'] );
        $active_color   = $this->safe_color( $s['active_color'] );
        $dropdown_bg    = $this->safe_color( $s['dropdown_bg'] );
        $dropdown_color = $this->safe_color( $s['dropdown_color'] );
        $gap            = $s['gap'];

        // Base link styles
        $link_decls = [];
        if ( $font_size && $font_size !== 15 ) $link_decls[] = "font-size: {$font_size}px";
        if ( $font_weight && $font_weight !== 'normal' ) $link_decls[] = "font-weight: {$font_weight}";
        if ( $text_transform && $text_transform !== 'none' ) $link_decls[] = "text-transform: {$text_transform}";
        if ( $letter_spacing > 0 ) $link_decls[] = "letter-spacing: {$letter_spacing}px";
        if ( $text_color ) $link_decls[] = "color: {$text_color}";

        if ( ! empty( $link_decls ) ) {
            $rules[] = "{$sel} .uk-navbar-nav > li > a, {$sel} .uk-subnav > li > a { " . implode( '; ', $link_decls ) . "; }";
        }

        // Hover color
        if ( $hover_color ) {
            $rules[] = "{$sel} .uk-navbar-nav > li > a:hover, {$sel} .uk-navbar-nav > li:hover > a, {$sel} .uk-subnav > li > a:hover { color: {$hover_color}; }";
        }

        // Active/current page color
        if ( $active_color ) {
            $rules[] = "{$sel} .uk-navbar-nav > li.uk-active > a, {$sel} .uk-navbar-nav > li.current-menu-item > a, {$sel} .uk-subnav > li.uk-active > a { color: {$active_color}; }";
        }

        // Gap between items
        $gap_map = [ 'small' => '5px', 'medium' => '15px', 'large' => '30px' ];
        $gap_val = $gap_map[ $gap ] ?? '15px';
        $rules[] = "{$sel} .uk-navbar-nav > li > a { padding: 0 {$gap_val}; }";

        // Dropdown styles
        if ( $dropdown_bg ) {
            $rules[] = "{$sel} .olo-nav-dropdown { background: {$dropdown_bg}; }";
            $rules[] = "{$sel} .olo-mega-panel { background: {$dropdown_bg}; }";
        }
        if ( $dropdown_color ) {
            $rules[] = "{$sel} .olo-nav-dropdown .uk-nav > li > a { color: {$dropdown_color}; }";
            $rules[] = "{$sel} .olo-mega-panel .uk-nav > li > a { color: {$dropdown_color}; }";
            $rules[] = "{$sel} .olo-mega-panel .uk-nav-header { color: {$dropdown_color}; opacity: 0.7; }";
        }

        // Toggle icon color inherits text_color
        if ( $text_color ) {
            $rules[] = "{$sel} .olo-nav-toggle { color: {$text_color}; }";
        }

        // Sticky header styles (global, not scoped to nav_id)
        if ( ! empty( $s['sticky'] ) ) {
            $sticky_bg = $this->safe_color( $s['sticky_bg'] );
            $sticky_decls = [];
            if ( $sticky_bg ) {
                $sticky_decls[] = "background-color: {$sticky_bg}";
            }
            if ( ! empty( $s['sticky_shadow'] ) ) {
                $sticky_decls[] = "box-shadow: 0 2px 12px rgba(0,0,0,0.12)";
            }
            if ( ! empty( $sticky_decls ) ) {
                $rules[] = ".olo-header-sticky { " . implode( '; ', $sticky_decls ) . "; }";
            }
        }

        // Button items — remove default padding from button li
        if ( ! empty( $s['button_items'] ) && $s['button_items'] !== 'none' ) {
            $rules[] = "{$sel} .olo-nav-btn-item > a { padding: 0; }";
        }

        return ! empty( $rules ) ? implode( ' ', $rules ) : '';
    }

    /**
     * Render as UIkit navbar (CSS-only, no uk-navbar JS component).
     */
    private function render_navbar( $tree, $children, $grandchildren, $alignment, $mobile, $mob_style, $nav_id, $s ) {
        $flex_class = 'uk-flex-left';
        if ( $alignment === 'center' ) $flex_class = 'uk-flex-center';
        if ( $alignment === 'right' )  $flex_class = 'uk-flex-right';

        // Detect current page for active state
        $current_url = trailingslashit( home_url( add_query_arg( [], false ) ) );

        $total = count( $tree );
        ?>
        <div class="olo-navmenu olo-navmenu--<?php echo esc_attr( $nav_id ); ?>">
            <nav class="olo-nav-bar">
                <?php if ( $mobile ) : ?>
                    <a id="<?php echo esc_attr( $nav_id ); ?>-btn" class="uk-hidden@m olo-nav-toggle" href="#<?php echo esc_attr( $nav_id ); ?>" uk-toggle uk-icon="icon: menu; ratio: 1.2"></a>
                <?php endif; ?>
                <ul class="uk-navbar-nav uk-visible@m <?php echo esc_attr( $flex_class ); ?>">
                    <?php foreach ( $tree as $idx => $item ) : ?>
                        <?php
                        $subs       = $children[ $item->ID ] ?? [];
                        $is_current = trailingslashit( $item->url ) === $current_url;
                        $is_button  = $this->is_button_item( $item, $idx, $total, $s );
                        $is_mega    = ! $is_button && $this->is_mega_item( $item, $subs, $s );

                        $li_classes = [];
                        if ( $is_current ) $li_classes[] = 'uk-active current-menu-item';
                        if ( $is_button )  $li_classes[] = 'olo-nav-btn-item';
                        $li_attr = ! empty( $li_classes ) ? ' class="' . esc_attr( implode( ' ', $li_classes ) ) . '"' : '';
                        ?>
                        <li<?php echo $li_attr; ?>>
                            <?php if ( $is_button ) : ?>
                                <?php $this->render_button_link( $item, $s ); ?>
                            <?php else : ?>
                                <a href="<?php echo esc_url( $item->url ); ?>"<?php echo $item->target ? ' target="' . esc_attr( $item->target ) . '"' : ''; ?>>
                                    <?php echo esc_html( $item->title ); ?>
                                </a>
                                <?php if ( $is_mega && ! empty( $subs ) ) : ?>
                                    <?php $this->render_mega_dropdown( $subs, $grandchildren, $s ); ?>
                                <?php elseif ( ! empty( $subs ) ) : ?>
                                    <div uk-drop="mode: hover; delay-show: 0; delay-hide: 200; pos: bottom-left; offset: 0; animation: uk-animation-slide-top-small; duration: 150">
                                        <div class="uk-card uk-card-body uk-card-default uk-card-small olo-nav-dropdown">
                                            <ul class="uk-nav uk-nav-default">
                                                <?php foreach ( $subs as $sub ) : ?>
                                                    <li>
                                                        <a href="<?php echo esc_url( $sub->url ); ?>"<?php echo $sub->target ? ' target="' . esc_attr( $sub->target ) . '"' : ''; ?>>
                                                            <?php echo esc_html( $sub->title ); ?>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                    <?php if ( ! empty( $s['append_search'] ) ) : ?>
                        <?php $this->render_navbar_search( $s ); ?>
                    <?php endif; ?>
                </ul>
            </nav>

            <?php if ( $mobile ) : ?>
                <?php if ( $mob_style === 'offcanvas' ) : ?>
                    <div id="<?php echo esc_attr( $nav_id ); ?>" uk-offcanvas="overlay: true">
                        <div class="uk-offcanvas-bar">
                            <button class="uk-offcanvas-close" type="button" uk-close></button>
                            <ul class="uk-nav uk-nav-default uk-nav-parent-icon" uk-nav>
                                <?php $this->render_mobile_items( $tree, $children, $grandchildren ); ?>
                                <?php if ( ! empty( $s['append_search'] ) ) $this->render_mobile_search( $s ); ?>
                            </ul>
                        </div>
                    </div>
                <?php else : ?>
                    <div id="<?php echo esc_attr( $nav_id ); ?>" class="uk-hidden" uk-drop="mode: click; toggle: #<?php echo esc_attr( $nav_id ); ?>-btn; pos: bottom-left; offset: 0">
                        <div class="uk-card uk-card-body uk-card-default uk-card-small">
                            <ul class="uk-nav uk-nav-default uk-nav-parent-icon" uk-nav>
                                <?php $this->render_mobile_items( $tree, $children, $grandchildren ); ?>
                                <?php if ( ! empty( $s['append_search'] ) ) $this->render_mobile_search( $s ); ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Check if a menu item should use mega menu dropdown.
     */
    private function is_mega_item( $item, $subs, $s ) {
        $mode = $s['mega_menu'] ?? 'none';
        if ( $mode === 'none' || empty( $subs ) ) {
            return false;
        }
        if ( $mode === 'auto' ) {
            return true; // All items with children get mega menu
        }
        if ( $mode === 'class' ) {
            $classes = is_array( $item->classes ) ? $item->classes : [];
            return in_array( 'mega-menu', $classes, true );
        }
        return false;
    }

    /**
     * Check if a menu item should render as a button.
     */
    private function is_button_item( $item, $idx, $total, $s ) {
        $mode = $s['button_items'] ?? 'none';
        if ( $mode === 'none' ) {
            return false;
        }
        if ( $mode === 'last' ) {
            return $idx === $total - 1;
        }
        if ( $mode === 'last-2' ) {
            return $idx >= $total - 2;
        }
        if ( $mode === 'css-class' ) {
            $classes = is_array( $item->classes ) ? $item->classes : [];
            return in_array( 'olo-btn', $classes, true );
        }
        return false;
    }

    /**
     * Render a menu item as a UIkit button link.
     */
    private function render_button_link( $item, $s ) {
        $style = esc_attr( $s['button_style'] ?? 'primary' );
        $size  = $s['button_size'] ?? 'small';

        $classes = [ 'uk-button', 'uk-button-' . $style ];
        if ( $size === 'small' ) {
            $classes[] = 'uk-button-small';
        }

        echo '<a href="' . esc_url( $item->url ) . '" class="' . esc_attr( implode( ' ', $classes ) ) . '"';
        if ( $item->target ) {
            echo ' target="' . esc_attr( $item->target ) . '"';
        }
        echo '>' . esc_html( $item->title ) . '</a>';
    }

    /**
     * Render mega menu dropdown with multi-column layout.
     */
    private function render_mega_dropdown( $subs, $grandchildren, $s ) {
        $columns = absint( $s['mega_columns'] ?? 3 );
        if ( $columns < 2 ) $columns = 2;
        if ( $columns > 4 ) $columns = 4;

        // Check if any sub has grandchildren
        $has_grandchildren = false;
        foreach ( $subs as $sub ) {
            if ( ! empty( $grandchildren[ $sub->ID ] ) ) {
                $has_grandchildren = true;
                break;
            }
        }
        ?>
        <div class="olo-mega-drop" uk-drop="mode: hover; delay-show: 0; delay-hide: 200; pos: bottom-left; offset: 0; animation: uk-animation-slide-top-small; duration: 150">
            <div class="uk-card uk-card-body uk-card-default olo-mega-panel">
                <?php if ( $has_grandchildren ) : ?>
                    <?php // Subs become column headers, grandchildren become items ?>
                    <div class="uk-child-width-1-<?php echo $columns; ?>@m" uk-grid>
                        <?php foreach ( $subs as $sub ) : ?>
                            <div>
                                <h6 class="uk-nav-header">
                                    <?php if ( $sub->url && $sub->url !== '#' ) : ?>
                                        <a href="<?php echo esc_url( $sub->url ); ?>" class="olo-mega-header-link"><?php echo esc_html( $sub->title ); ?></a>
                                    <?php else : ?>
                                        <?php echo esc_html( $sub->title ); ?>
                                    <?php endif; ?>
                                </h6>
                                <?php $gc = $grandchildren[ $sub->ID ] ?? []; ?>
                                <?php if ( ! empty( $gc ) ) : ?>
                                    <ul class="uk-nav uk-nav-default">
                                        <?php foreach ( $gc as $gci ) : ?>
                                            <li>
                                                <a href="<?php echo esc_url( $gci->url ); ?>"<?php echo $gci->target ? ' target="' . esc_attr( $gci->target ) . '"' : ''; ?>>
                                                    <?php echo esc_html( $gci->title ); ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <?php // No grandchildren: auto-distribute subs into columns ?>
                    <?php $chunks = array_chunk( $subs, max( 1, ceil( count( $subs ) / $columns ) ) ); ?>
                    <div class="uk-child-width-1-<?php echo $columns; ?>@m" uk-grid>
                        <?php foreach ( $chunks as $chunk ) : ?>
                            <div>
                                <ul class="uk-nav uk-nav-default">
                                    <?php foreach ( $chunk as $sub ) : ?>
                                        <li>
                                            <a href="<?php echo esc_url( $sub->url ); ?>"<?php echo $sub->target ? ' target="' . esc_attr( $sub->target ) . '"' : ''; ?>>
                                                <?php echo esc_html( $sub->title ); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php if ( ! empty( $s['append_search'] ) ) : ?>
                    <div class="olo-mega-search" style="border-top:1px solid #e5e7eb;padding:12px 16px 4px;margin-top:8px">
                        <?php $this->render_mega_search( $s ); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render mobile menu items with 3-level support.
     */
    private function render_mobile_items( $tree, $children, $grandchildren ) {
        foreach ( $tree as $item ) {
            $subs = $children[ $item->ID ] ?? [];
            if ( ! empty( $subs ) ) {
                echo '<li class="uk-parent">';
                echo '<a href="' . esc_url( $item->url ) . '">' . esc_html( $item->title ) . '</a>';
                echo '<ul class="uk-nav-sub">';
                foreach ( $subs as $sub ) {
                    $gc = $grandchildren[ $sub->ID ] ?? [];
                    if ( ! empty( $gc ) ) {
                        echo '<li class="uk-parent">';
                        echo '<a href="' . esc_url( $sub->url ) . '">' . esc_html( $sub->title ) . '</a>';
                        echo '<ul class="uk-nav-sub">';
                        foreach ( $gc as $gci ) {
                            echo '<li><a href="' . esc_url( $gci->url ) . '">' . esc_html( $gci->title ) . '</a></li>';
                        }
                        echo '</ul>';
                        echo '</li>';
                    } else {
                        echo '<li><a href="' . esc_url( $sub->url ) . '">' . esc_html( $sub->title ) . '</a></li>';
                    }
                }
                echo '</ul>';
                echo '</li>';
            } else {
                echo '<li><a href="' . esc_url( $item->url ) . '">' . esc_html( $item->title ) . '</a></li>';
            }
        }
    }

    /**
     * Render the appended search widget as the last <li> in the navbar.
     * Delegates to Olo_LiveSearch_Tile for the actual HTML.
     */
    private function render_navbar_search( $s ) {
        $livesearch_tile = Olo_Tile_Manager::instance()->get_tile( 'livesearch' );
        if ( ! $livesearch_tile ) {
            return;
        }

        // Build settings for the embedded LiveSearch
        $search_settings = [
            'mode'            => $s['search_mode'] ?: 'modal',
            'placeholder'     => $s['search_placeholder'] ?: 'Cerca...',
            'post_types'      => $s['search_post_types'] ?: 'post,page',
            'max_results'     => $s['search_max_results'] ?: 10,
            'show_thumbnail'  => true,
            'input_height'    => min( intval( $s['font_size'] ) + 26, 44 ),
            'input_font_size' => $s['font_size'] ?: 14,
            // Inherit navbar colors
            'icon_color'      => $s['text_color'] ?: '#9ca3af',
        ];

        // Enqueue LiveSearch assets
        wp_enqueue_style( 'olo-livesearch-css', OLO_URL . 'assets/css/olo-livesearch.css', [], OLO_VERSION );
        wp_enqueue_script( 'olo-livesearch-js', OLO_URL . 'assets/js/olo-livesearch.js', [], OLO_VERSION, true );

        $html = $livesearch_tile->render( $search_settings );

        echo '<li class="olo-nav-search-item">';
        echo $html;
        echo '</li>';
    }

    /**
     * Render a search field inside mobile menu.
     */
    private function render_mobile_search( $s ) {
        $livesearch_tile = Olo_Tile_Manager::instance()->get_tile( 'livesearch' );
        if ( ! $livesearch_tile ) {
            return;
        }

        $height = intval( $s['search_input_height'] ?: 36 );

        $search_settings = [
            'mode'                 => 'inline',
            'placeholder'          => $s['search_placeholder'] ?: 'Cerca...',
            'post_types'           => $s['search_post_types'] ?: 'post,page',
            'max_results'          => $s['search_max_results'] ?: 10,
            'show_thumbnail'       => true,
            'input_height'         => $height,
            'input_font_size'      => max( 12, $height - 22 ),
            'input_bg'             => $s['search_input_bg'] ?: '#ffffff',
            'input_color'          => $s['search_input_color'] ?: '#374151',
            'icon_color'           => $s['search_icon_color'] ?: '#9ca3af',
            'results_border_color' => $s['search_border_color'] ?: '#e5e7eb',
            'results_bg'           => $s['search_results_bg'] ?: '#ffffff',
            'item_hover_bg'        => $s['search_hover_bg'] ?: '#f3f4f6',
        ];

        echo '<li class="olo-nav-search-mobile">';
        echo $livesearch_tile->render( $search_settings );
        echo '</li>';
    }

    /**
     * Render an inline search field inside mega menu panels.
     */
    private function render_mega_search( $s ) {
        $livesearch_tile = Olo_Tile_Manager::instance()->get_tile( 'livesearch' );
        if ( ! $livesearch_tile ) {
            return;
        }

        $height = intval( $s['search_input_height'] ?: 36 );

        $search_settings = [
            'mode'                 => 'inline',
            'placeholder'          => $s['search_placeholder'] ?: 'Cerca...',
            'post_types'           => $s['search_post_types'] ?: 'post,page',
            'max_results'          => $s['search_max_results'] ?: 10,
            'show_thumbnail'       => true,
            'input_height'         => $height,
            'input_font_size'      => max( 12, $height - 22 ),
            'input_bg'             => $s['search_input_bg'] ?: '#ffffff',
            'input_color'          => $s['search_input_color'] ?: '#374151',
            'icon_color'           => $s['search_icon_color'] ?: '#9ca3af',
            'results_border_color' => $s['search_border_color'] ?: '#e5e7eb',
            'results_bg'           => $s['search_results_bg'] ?: '#ffffff',
            'item_hover_bg'        => $s['search_hover_bg'] ?: '#f3f4f6',
        ];

        wp_enqueue_style( 'olo-livesearch-css', OLO_URL . 'assets/css/olo-livesearch.css', [], OLO_VERSION );
        wp_enqueue_script( 'olo-livesearch-js', OLO_URL . 'assets/js/olo-livesearch.js', [], OLO_VERSION, true );

        echo $livesearch_tile->render( $search_settings );
    }

    /**
     * Output inline script for header mode (overlay/classic) and sticky behavior.
     */
    private function render_header_script( $s ) {
        $mode       = esc_js( $s['header_mode'] ?? 'overlay' );
        $sticky     = ! empty( $s['sticky'] ) ? 'true' : 'false';
        $show_on_up = ! empty( $s['sticky_show_on_up'] ) ? 'true' : 'false';
        ?>
        <script>
        (function() {
            var mode = "<?php echo $mode; ?>";
            var stickyEnabled = <?php echo $sticky; ?>;
            var showOnUp = <?php echo $show_on_up; ?>;
            function init() {
                var header = document.querySelector("header.olo-site-header");
                if (!header) return;
                // Apply header mode class
                header.classList.add("olo-header-" + mode);
                // Sticky scroll listener
                if (stickyEnabled) {
                    var lastY = 0;
                    var hidden = false;
                    window.addEventListener("scroll", function() {
                        var y = window.pageYOffset || document.documentElement.scrollTop;
                        if (y > 10) {
                            header.classList.add("olo-header-sticky");
                        } else {
                            header.classList.remove("olo-header-sticky");
                        }
                        if (showOnUp) {
                            if (y > lastY && y > 200) {
                                if (!hidden) { header.style.transform = "translateY(-100%)"; hidden = true; }
                            } else {
                                if (hidden) { header.style.transform = ""; hidden = false; }
                            }
                        }
                        lastY = y;
                    }, { passive: true });
                }
            }
            if (document.readyState === "loading") {
                document.addEventListener("DOMContentLoaded", init);
            } else {
                init();
            }
        })();
        </script>
        <?php
    }

    /**
     * Render as UIkit subnav.
     */
    private function render_subnav( $tree, $alignment, $s, $nav_id ) {
        $align_class = '';
        if ( $alignment === 'center' ) $align_class = ' uk-flex-center';
        if ( $alignment === 'right' )  $align_class = ' uk-flex-right';
        ?>
        <div class="olo-navmenu olo-navmenu--<?php echo esc_attr( $nav_id ); ?>">
            <ul class="uk-subnav<?php echo esc_attr( $align_class ); ?>">
                <?php foreach ( $tree as $item ) : ?>
                    <li>
                        <a href="<?php echo esc_url( $item->url ); ?>"<?php echo $item->target ? ' target="' . esc_attr( $item->target ) . '"' : ''; ?>>
                            <?php echo esc_html( $item->title ); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
    }
}
