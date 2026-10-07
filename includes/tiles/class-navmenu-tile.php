<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_NavMenu_Tile extends Olobuild_Tile_Base {

    protected $type     = 'navmenu';
    protected $name     = 'Menu Nav';
    protected $icon     = 'dashicons-menu';
    protected $category = 'navigation';
    protected $defaults = [
        'menu_id'        => 0,
        'style'          => 'navbar',
        'alignment'      => 'left',
        'mobile_toggle'  => true,
        'mobile_style'   => 'offcanvas',
        // Menu a schermo intero: vuoti = la resa di sempre (fondo nero, voci bianche al centro)
        'fs_logo'                 => '',
        'fs_numbers'              => false,
        'fs_descriptions'         => false,
        'fs_bg'                   => '',
        'fs_align'                => 'center',
        'fs_color'                => '',
        'fs_color_hover'          => '',
        'fs_color_hover_duration' => 200,
        'fs_font_family'          => '',
        'fs_font_size'            => '',
        'fs_font_weight'          => '',
        'fs_uppercase'            => false,
        'fs_letter_spacing'       => '',
        'fs_number_color'         => '',
        'fs_desc_color'           => '',
        'fs_dividers'             => false,
        'fs_divider_color'        => '',
        'fs_close_color'          => '',
        'text_color'     => '',
        'hover_color'    => '',
        'active_color'   => '',
        'font_size'      => 15,
        'font_weight'    => 'normal',
        'text_transform' => 'none',
        'letter_spacing' => 0,
        'gap'            => 'medium',
        // Menu pointer
        'menu_pointer'           => 'none',
        'menu_pointer_animation' => 'fade',
        'menu_pointer_color'     => '',
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
        // Search tile reference
        'search_tile_id'     => '',
        'search_position'    => 'after',
        // Search in menu (legacy)
        'append_search'      => false,
        'search_mode'        => 'modal',
        'search_placeholder' => 'Cerca...',
        'search_post_types'  => 'post,page',
        'search_max_results' => 10,
        // Stile ricerca mega menu
        'search_input_bg'      => '#ffffff',
        'search_input_color'   => '',
        'search_icon_color'    => '',
        'search_border_color'  => '',
        'search_input_height'  => 36,
        'search_results_bg'    => '#ffffff',
        'search_hover_bg'      => '',
        // Vertical mode
        'v_show_icons'       => false,
        'v_icon_style'       => 'line',
        'v_icon_size'        => 20,
        'v_icon_color'       => '',
        'v_item_spacing'     => 4,
        'v_item_padding'     => 10,
        'v_separator'        => false,
        'v_separator_color'  => '',
        'v_active_indicator' => 'left-border',
        'v_active_bg'        => '',
        'v_hover_bg'         => '',
        'v_border_radius'    => 6,
        'v_expand_subs'      => true,
        // Mobile advanced
        'mobile_type'        => 'dropdown',
        'mobile_breakpoint'  => 960,
        'hamburger_style'    => 'default',
        'hamburger_position' => 'inline',
        'hamburger_offset_x' => 16,
        'hamburger_offset_y' => 16,
        'hamburger_bg'       => '',
        'hamburger_color'    => '',
        'hamburger_size'     => 24,
        'menu_badge_support' => false,
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
                'offcanvas'  => 'Offcanvas Panel',
                'dropdown'   => 'Dropdown',
                'fullscreen' => 'Fullscreen',
            ]],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $menu_id = absint( $s['menu_id'] );
        if ( ! $menu_id ) {
            return '<div class="olo-navmenu"><p class="uk-text-muted uk-text-center">' . esc_html( olobuild_t( 'Scegli un menu nelle impostazioni della tile.' ) ) . '</p></div>';
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
        $nav_id    = 'olo-nav-' . $menu_id . '-' . wp_rand( 100, 999 );

        // Build scoped CSS for custom styles
        $scoped_css = $this->build_scoped_css( $s, $nav_id );

        ob_start();

        if ( $scoped_css ) {
            echo '<style>' . $scoped_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS built by build_scoped_css() exclusively from sanitized values (safe_color_css() colors, intval()/absint()/floatval() numerics, esc_attr()'d enums, fixed maps) and the internally generated $nav_id
        }

        // When inside off-canvas, always render vertical (no hamburger/offcanvas nesting)
        if ( ! empty( $GLOBALS['olo_in_offcanvas'] ) ) {
            $this->render_vertical( $tree, $children, $grandchildren, $s, $nav_id );
        } elseif ( $style === 'vertical' ) {
            $this->render_vertical( $tree, $children, $grandchildren, $s, $nav_id );
        } elseif ( $style === 'subnav' ) {
            $this->render_subnav( $tree, $alignment, $s, $nav_id );
        } else {
            $this->render_navbar( $tree, $children, $grandchildren, $alignment, $mobile, $mob_style, $nav_id, $s );
        }

        // Header mode + sticky script
        $this->render_header_script( $s, $nav_id );

                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".olo-navmenu--{$nav_id}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".olo-navmenu--{$nav_id}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".olo-navmenu--{$nav_id}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS built by Olobuild_Tile_Base::build_border_css() from sanitized border settings
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS built by Olobuild_Tile_Base border helpers from sanitized border settings
        }
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
        $text_color     = $this->safe_color_css( $s['text_color'] );
        $hover_color    = $this->safe_color_css( $s['hover_color'] );
        $active_color   = $this->safe_color_css( $s['active_color'] );
        $dropdown_bg    = $this->safe_color_css( $s['dropdown_bg'] );
        $dropdown_color = $this->safe_color_css( $s['dropdown_color'] );
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

        // a11y tastiera: anello di focus visibile sulle voci di menu orizzontale
        $rules[] = "{$sel} .uk-navbar-nav > li > a:focus-visible, {$sel} .uk-subnav > li > a:focus-visible { outline:none; box-shadow:0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent); border-radius:3px; }";

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

        // Hamburger alignment (when only toggle is visible)
        $align = $s['alignment'] ?? 'left';
        $toggle_justify = 'flex-start';
        if ( $align === 'center' ) $toggle_justify = 'center';
        if ( $align === 'right' )  $toggle_justify = 'flex-end';
        $rules[] = "{$sel} .olo-nav-bar { display:flex; flex-wrap:wrap; align-items:center; justify-content:{$toggle_justify}; }";

        // Hamburger trigger styling
        $h_pos    = $s['hamburger_position'] ?? 'inline';
        $h_size   = max( 16, intval( $s['hamburger_size'] ) );
        $h_bg     = $this->safe_color_css( $s['hamburger_bg'] ?? '' );
        $h_color  = $this->safe_color_css( $s['hamburger_color'] ?? '' );
        $h_ox     = max( 0, intval( $s['hamburger_offset_x'] ) );
        $h_oy     = max( 0, intval( $s['hamburger_offset_y'] ) );

        $h_decls = [];
        if ( $h_color ) $h_decls[] = "color:{$h_color}";
        if ( $h_bg )    $h_decls[] = "background:{$h_bg};border-radius:6px;padding:8px";
        if ( ! empty( $h_decls ) ) {
            $rules[] = "{$sel} .olo-nav-toggle { " . implode( ';', $h_decls ) . "; }";
        }
        // SVG size
        if ( $h_size !== 24 ) {
            $rules[] = "{$sel} .olo-nav-toggle svg { width:{$h_size}px; height:{$h_size}px; }";
        }

        // Fixed position
        if ( $h_pos !== 'inline' ) {
            $pos_css = "position:fixed;z-index:10050;";
            if ( str_contains( $h_pos, 'top' ) )    $pos_css .= "top:{$h_oy}px;";
            if ( str_contains( $h_pos, 'bottom' ) ) $pos_css .= "bottom:{$h_oy}px;";
            if ( str_contains( $h_pos, 'left' ) )   $pos_css .= "left:{$h_ox}px;";
            if ( str_contains( $h_pos, 'right' ) )  $pos_css .= "right:{$h_ox}px;";
            $rules[] = "{$sel} .olo-nav-toggle { {$pos_css} }";
        }

        // Sfondo dell'header agganciato: solo l'header che contiene QUESTO menu (prima la regola
        // valeva per ogni .olo-header-sticky, anche con il menu nel corpo della pagina). L'header
        // lo segna lo script (olo-hdr-<id>): un :has() sulla nav si perderebbe quando il renderer
        // sposta nel body una tile a «Posizione fissa».
        if ( ! empty( $s['sticky'] ) ) {
            $sticky_bg = $this->safe_color_css( $s['sticky_bg'] );
            $sticky_decls = [];
            if ( $sticky_bg ) {
                $sticky_decls[] = "background-color: {$sticky_bg}";
            }
            if ( ! empty( $sticky_decls ) ) {
                $rules[] = '.olo-header-sticky.olo-hdr-' . esc_attr( $nav_id ) . ' { ' . implode( '; ', $sticky_decls ) . '; }';
            }
        }

        // Button items — remove default padding from button li
        if ( ! empty( $s['button_items'] ) && $s['button_items'] !== 'none' ) {
            $rules[] = "{$sel} .olo-nav-btn-item > a { padding: 0; }";
        }

        // Menu pointer styles
        $pointer = $s['menu_pointer'] ?? 'none';
        if ( $pointer !== 'none' ) {
            $pointer_color = $this->safe_color_css( $s['menu_pointer_color'] ?? '' ) ?: ( $active_color ?: 'currentColor' );
            $pointer_anim  = $s['menu_pointer_animation'] ?? 'fade';

            // Base: position relative on li > a
            $rules[] = "{$sel} .uk-navbar-nav > li > a { position: relative; }";

            if ( $pointer === 'underline' ) {
                $rules[] = "{$sel} .uk-navbar-nav > li > a::after { content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 2px; background: {$pointer_color}; transform: scaleX(0); transition: transform 0.3s ease; }";
                $rules[] = "{$sel} .uk-navbar-nav > li > a:hover::after, {$sel} .uk-navbar-nav > li.uk-active > a::after { transform: scaleX(1); }";
                if ( $pointer_anim === 'slide' ) {
                    $rules[] = "{$sel} .uk-navbar-nav > li > a::after { transform-origin: left center; }";
                } elseif ( $pointer_anim === 'grow' ) {
                    $rules[] = "{$sel} .uk-navbar-nav > li > a::after { transform-origin: center center; }";
                } elseif ( $pointer_anim === 'drop' ) {
                    $rules[] = "{$sel} .uk-navbar-nav > li > a::after { transform: scaleX(0) translateY(-10px); }";
                    $rules[] = "{$sel} .uk-navbar-nav > li > a:hover::after, {$sel} .uk-navbar-nav > li.uk-active > a::after { transform: scaleX(1) translateY(0); }";
                }
            } elseif ( $pointer === 'overline' ) {
                $rules[] = "{$sel} .uk-navbar-nav > li > a::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 2px; background: {$pointer_color}; transform: scaleX(0); transition: transform 0.3s ease; }";
                $rules[] = "{$sel} .uk-navbar-nav > li > a:hover::before, {$sel} .uk-navbar-nav > li.uk-active > a::before { transform: scaleX(1); }";
            } elseif ( $pointer === 'framed' ) {
                $rules[] = "{$sel} .uk-navbar-nav > li > a::after { content: ''; position: absolute; inset: 4px 0; border: 2px solid {$pointer_color}; border-radius: 4px; opacity: 0; transition: opacity 0.3s ease; }";
                $rules[] = "{$sel} .uk-navbar-nav > li > a:hover::after, {$sel} .uk-navbar-nav > li.uk-active > a::after { opacity: 1; }";
            } elseif ( $pointer === 'background' ) {
                $rules[] = "{$sel} .uk-navbar-nav > li > a::after { content: ''; position: absolute; inset: 4px 0; background: {$pointer_color}; border-radius: 4px; opacity: 0; transition: opacity 0.3s ease; z-index: -1; }";
                $rules[] = "{$sel} .uk-navbar-nav > li > a:hover::after, {$sel} .uk-navbar-nav > li.uk-active > a::after { opacity: 0.12; }";
            } elseif ( $pointer === 'double-line' ) {
                $rules[] = "{$sel} .uk-navbar-nav > li > a::before, {$sel} .uk-navbar-nav > li > a::after { content: ''; position: absolute; left: 0; width: 100%; height: 2px; background: {$pointer_color}; transform: scaleX(0); transition: transform 0.3s ease; }";
                $rules[] = "{$sel} .uk-navbar-nav > li > a::before { top: 0; }";
                $rules[] = "{$sel} .uk-navbar-nav > li > a::after { bottom: 0; }";
                $rules[] = "{$sel} .uk-navbar-nav > li > a:hover::before, {$sel} .uk-navbar-nav > li > a:hover::after, {$sel} .uk-navbar-nav > li.uk-active > a::before, {$sel} .uk-navbar-nav > li.uk-active > a::after { transform: scaleX(1); }";
            }
        }

        // Custom mobile breakpoint
        $mobile_bp = intval( $s['mobile_breakpoint'] ?? 960 );
        if ( $mobile_bp !== 960 ) {
            // Override UIkit @m breakpoint with custom value
            $rules[] = "@media (max-width: {$mobile_bp}px) { {$sel} .uk-visible\\@m { display: none !important; } {$sel} .uk-hidden\\@m { display: inline-block !important; } }";
            $rules[] = "@media (min-width: " . ( $mobile_bp + 1 ) . "px) { {$sel} .uk-visible\\@m { display: flex !important; } {$sel} .uk-hidden\\@m { display: none !important; } }";
        }

        // Fullscreen mobile overlay
        if ( self::mobile_fullscreen( $s ) ) {
            $fs     = '.olo-nav-fullscreen';
            $fs_bg  = $this->safe_color_css( $s['fs_bg'] ?? '' ) ?: 'rgba(0,0,0,0.95)';
            $fs_col = $this->safe_color_css( $s['fs_color'] ?? '' ) ?: 'var(--olo-color-primary-contrast, #FFFFFF)';
            $fs_hov = $this->safe_color_css( $s['fs_color_hover'] ?? '' );
            $fs_dur = max( 0, intval( $s['fs_color_hover_duration'] ?? 200 ) );
            $fs_sx  = ( $s['fs_align'] ?? 'center' ) === 'left';
            $fs_fam = $this->resolve_font_family( $s['fs_font_family'] ?? '' );
            $fs_sz  = floatval( $s['fs_font_size'] ?? 0 );
            $fs_w   = preg_match( '/^[1-9]00$/', (string) ( $s['fs_font_weight'] ?? '' ) ) ? (string) $s['fs_font_weight'] : '';
            $fs_ls  = is_numeric( $s['fs_letter_spacing'] ?? '' ) ? floatval( $s['fs_letter_spacing'] ) : null;
            $mono   = "var(--olo-font-family-mono, ui-monospace, 'SF Mono', Menlo, Consolas, monospace)";

            $voce  = "color: {$fs_col}; text-align: " . ( $fs_sx ? 'left' : 'center' ) . '; padding: 0.33em 0;';
            $voce .= ' font-size: ' . ( $fs_sz > 0 ? $fs_sz . 'px' : '1.5rem' ) . ';';
            if ( $fs_fam ) { $voce .= " font-family: {$fs_fam};"; }
            if ( $fs_w ) { $voce .= " font-weight: {$fs_w};"; }
            if ( ! empty( $s['fs_uppercase'] ) ) { $voce .= ' text-transform: uppercase;'; }
            if ( null !== $fs_ls ) { $voce .= " letter-spacing: {$fs_ls}px;"; }
            if ( $fs_hov ) { $voce .= " transition: color {$fs_dur}ms ease;"; }

            $rules[] = "{$sel} {$fs} { position: fixed; inset: 0; z-index: 9999; background: {$fs_bg}; display: flex; align-items: center; justify-content: center; overflow-y: auto; opacity: 0; visibility: hidden; transition: opacity 0.3s ease, visibility 0.3s ease; }";
            $rules[] = "{$sel} {$fs}.uk-open { opacity: 1; visibility: visible; }";
            $rules[] = "{$sel} {$fs} .olo-nav-fs-corpo { width: min(100% - 48px, 560px); margin: auto 0; padding: 5.25rem 0 3rem; }";
            $rules[] = "{$sel} {$fs} .uk-nav > li > a { {$voce} }";
            if ( $fs_hov ) {
                $rules[] = "{$sel} {$fs} .uk-nav > li:not(.olo-nav-fs-btn) > a:hover, {$sel} {$fs} .uk-nav > li:not(.olo-nav-fs-btn) > a:focus-visible, {$sel} {$fs} .uk-nav > li.uk-active:not(.olo-nav-fs-btn) > a { color: {$fs_hov}; }";
            }
            $chiudi = $this->safe_color_css( $s['fs_close_color'] ?? '' ) ?: $fs_col;
            $rules[] = "{$sel} {$fs} .uk-close { position: absolute; top: 20px; right: 20px; color: {$chiudi}; }";
            $rules[] = "{$sel} {$fs} .olo-nav-fs-logo { position: absolute; top: 22px; left: 24px; height: 30px; width: auto; max-width: 55%; object-fit: contain; }";
            // Voce a numeri o con descrizione: griglia (numero | nome, descrizione sotto al nome)
            $rules[] = "{$sel} {$fs} .olo-nav-fs-nome, {$sel} {$fs} .olo-nav-fs-desc { display: block; }";
            $rules[] = "{$sel} {$fs} .olo-nav-fs-desc { font-family: {$mono}; font-size: 11px; font-weight: 400; letter-spacing: 0.14em; text-transform: uppercase; line-height: 1.5; margin-top: 0.35em; color: " . ( $this->safe_color_css( $s['fs_desc_color'] ?? '' ) ?: 'currentColor' ) . '; opacity: ' . ( $this->safe_color_css( $s['fs_desc_color'] ?? '' ) ? '1' : '0.65' ) . '; }';
            if ( ! empty( $s['fs_numbers'] ) ) {
                $num = $this->safe_color_css( $s['fs_number_color'] ?? '' ) ?: 'currentColor';
                $rules[] = "{$sel} {$fs} .uk-nav { counter-reset: olo-fs; }";
                $rules[] = "{$sel} {$fs} .uk-nav > li:not(.olo-nav-fs-btn) > a { display: grid; grid-template-columns: auto 1fr; column-gap: 0.75em; align-items: baseline; }";
                $rules[] = "{$sel} {$fs} .uk-nav > li:not(.olo-nav-fs-btn) > a::before { counter-increment: olo-fs; content: counter(olo-fs, decimal-leading-zero); grid-row: 1 / span 2; font-family: {$mono}; font-size: 12px; font-weight: 600; letter-spacing: 0.1em; color: {$num}; }";
                $rules[] = "{$sel} {$fs} .uk-nav > li:not(.olo-nav-fs-btn) > a > span { grid-column: 2; }";
            }
            if ( ! empty( $s['fs_dividers'] ) ) {
                $linea = $this->safe_color_css( $s['fs_divider_color'] ?? '' ) ?: 'color-mix(in srgb, currentColor 18%, transparent)';
                $rules[] = "{$sel} {$fs} .uk-nav > li:not(.olo-nav-fs-btn) { border-bottom: 1px solid {$linea}; }";
                $rules[] = "{$sel} {$fs} .uk-nav > li:not(.olo-nav-fs-btn) > a { padding: 0.45em 0; }";
            }
            // Voce pulsante (Voci pulsante): un pulsante vero anche qui, coi colori del tema
            $rules[] = "{$sel} {$fs} .uk-nav > li.olo-nav-fs-btn { margin-top: 1.75rem; text-align: " . ( $fs_sx ? 'left' : 'center' ) . '; }';
            $rules[] = "{$sel} {$fs} .uk-nav > li.olo-nav-fs-btn > a { display: inline-flex; align-items: center; justify-content: center; padding: 1.1em 2.15em; border-radius: 0.45em; background: var(--olo-color-primary, #e1474f); color: var(--olo-color-on-primary, #ffffff); font-family: {$mono}; font-size: 13px; font-weight: 700; letter-spacing: 0.16em; text-transform: uppercase; }";
        }

        // --- Vertical mode styles ---
        if ( ( $s['style'] ?? '' ) === 'vertical' ) {
            $v_spacing    = max( 0, intval( $s['v_item_spacing'] ) );
            // «Padding voce» è il controllo a 4 lati: un numero (valore storico o lati collegati)
            // dà la resa di sempre, N sopra e sotto e N+4 ai lati; un oggetto dà i 4 lati scelti.
            // Prima intval() dell'oggetto valeva 1 → la voce prendeva sempre 4px.
            $v_pad_raw    = $s['v_item_padding'] ?? 10;
            if ( is_array( $v_pad_raw ) ) {
                $v_sides   = Olobuild_Tile_Utils::spacing_sides( $v_pad_raw, [], [ 10, 14, 10, 14 ] );
                $v_padding = max( 4, $v_sides['top'] ); // base delle voci annidate
                $v_pad_css = Olobuild_Tile_Utils::sides_css( $v_sides );
            } else {
                $v_padding = max( 4, intval( $v_pad_raw ) );
                $v_pad_css = $v_padding . 'px ' . ( $v_padding + 4 ) . 'px';
            }
            $v_radius     = max( 0, Olobuild_Tile_Utils::radius_int( $s['v_border_radius'] ) );
            $v_rad_h      = Olobuild_Tile_Utils::radius_hover( $s, 'v_border_radius_hover' );
            $v_hover_bg   = $this->safe_color_css( $s['v_hover_bg'] ?? '' );
            $v_active_bg  = $this->safe_color_css( $s['v_active_bg'] ?? '' );
            $v_icon_color = $this->safe_color_css( $s['v_icon_color'] ?? '' );
            $v_sep_color  = $this->safe_color_css( $s['v_separator_color'] ?? '' ) ?: 'rgba(255,255,255,0.08)';
            $v_indicator  = $s['v_active_indicator'] ?? 'left-border';
            $v_icon_size  = max( 14, intval( $s['v_icon_size'] ) );

            $rules[] = "{$sel} .olo-vnav-list { list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:{$v_spacing}px; }";
            // «Durata» dello Sfondo in hover (senza chiave .15s come sempre)
            $v_bg_dur = Olobuild_Tile_Utils::durata_hover( $s, 'v_active_bg_hover_duration', '.15s' );
            $rules[] = "{$sel} .olo-vnav-link { display:flex; align-items:center; gap:8px; padding:{$v_pad_css}; border-radius:{$v_radius}px; text-decoration:none; transition:background {$v_bg_dur},color .15s" . ( $v_rad_h ? ', ' . $v_rad_h['transition'] : '' ) . "; }";
            // a11y tastiera: anello di focus visibile sulle voci di menu verticale
            $rules[] = "{$sel} .olo-vnav-link:focus-visible { outline:none; box-shadow:0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent); }";

            // Link typography (reuse existing vars)
            $v_link_decls = [];
            if ( $font_size && $font_size !== 15 ) $v_link_decls[] = "font-size:{$font_size}px";
            if ( $font_weight && $font_weight !== 'normal' ) $v_link_decls[] = "font-weight:{$font_weight}";
            if ( $text_transform && $text_transform !== 'none' ) $v_link_decls[] = "text-transform:{$text_transform}";
            if ( $letter_spacing > 0 ) $v_link_decls[] = "letter-spacing:{$letter_spacing}px";
            if ( $text_color ) $v_link_decls[] = "color:{$text_color}";
            if ( ! empty( $v_link_decls ) ) {
                $rules[] = "{$sel} .olo-vnav-link { " . implode( ';', $v_link_decls ) . "; }";
            }

            // Hover
            if ( $v_rad_h ) {
                $rules[] = "{$sel} .olo-vnav-link:hover { border-radius:{$v_rad_h['css']} !important; }";
            }
            if ( $v_hover_bg ) {
                $rules[] = "{$sel} .olo-vnav-link:hover { background:{$v_hover_bg}; }";
            } else {
                $rules[] = "{$sel} .olo-vnav-link:hover { background:rgba(255,255,255,0.04); }";
            }
            if ( $hover_color ) {
                $rules[] = "{$sel} .olo-vnav-link:hover { color:{$hover_color}; }";
            }

            // Active indicator — TOKEN-FIRST: primario brand (era #e1474f / rgba indaco off-brand)
            $active_c = $active_color ?: 'var(--olo-color-primary, #e1474f)';
            if ( $v_indicator === 'left-border' ) {
                $rules[] = "{$sel} .olo-vnav-item--active > .olo-vnav-link { border-left:3px solid {$active_c}; color:{$active_c}; background:" . ( $v_active_bg ?: "color-mix(in srgb, {$active_c} 8%, transparent)" ) . "; }";
            } elseif ( $v_indicator === 'background' ) {
                $rules[] = "{$sel} .olo-vnav-item--active > .olo-vnav-link { color:{$active_c}; background:" . ( $v_active_bg ?: "color-mix(in srgb, {$active_c} 10%, transparent)" ) . "; }";
            } elseif ( $v_indicator === 'bold' ) {
                $rules[] = "{$sel} .olo-vnav-item--active > .olo-vnav-link { color:{$active_c}; font-weight:700; }";
            } elseif ( $v_indicator === 'none' ) {
                $rules[] = "{$sel} .olo-vnav-item--active > .olo-vnav-link { color:{$active_c}; }";
            }

            // Separator
            if ( ! empty( $s['v_separator'] ) ) {
                $rules[] = "{$sel} .olo-vnav-item + .olo-vnav-item { border-top:1px solid {$v_sep_color}; }";
            }

            // Icon
            $rules[] = "{$sel} .olo-vnav-icon { display:inline-flex; align-items:center; justify-content:center; flex-shrink:0; width:{$v_icon_size}px; height:{$v_icon_size}px; }";
            if ( $v_icon_color ) {
                $rules[] = "{$sel} .olo-vnav-icon { color:{$v_icon_color}; }";
            }

            // Chevron
            $rules[] = "{$sel} .olo-vnav-chev { margin-left:auto; opacity:.5; cursor:pointer; transition:transform .2s; flex-shrink:0; }";

            // Sub items
            $rules[] = "{$sel} .olo-vnav-sub { list-style:none; margin:0; padding:0 0 0 " . ( $v_padding + 12 ) . "px; }";
            $rules[] = "{$sel} .olo-vnav-sub .olo-vnav-link { padding:" . max( 4, $v_padding - 3 ) . "px " . ( $v_padding + 4 ) . "px; font-size:" . max( 12, $font_size - 1 ) . "px; opacity:.85; }";
            $rules[] = "{$sel} .olo-vnav-sub .olo-vnav-link:hover { opacity:1; }";
            $rules[] = "{$sel} .olo-vnav-sub--deep { padding-left:" . ( $v_padding + 8 ) . "px; }";
        }

        // Nel corpo della pagina la tendina aperta finiva sotto le sezioni dopo: il suo z-index
        // vale solo dentro il contesto che la contiene (un'Ombra sul riquadro della tile è un
        // filter, una sezione con sfondo ha il contenitore a z-index 1) e la sezione successiva le
        // disegnava sopra; una sezione con immagine di sfondo (overflow: clip) la tagliava. Solo da
        // aperta: da chiusa la sezione resta com'è, sfondi compresi. I pannelli fissi (schermo
        // intero, off-canvas) non sono tagliati dalla sezione: a loro basta lo z-index, così il
        // ritaglio degli sfondi resta. Limite: mentre la tendina è aperta la sezione non ritaglia
        // gli strati di sfondo più grandi di lei (parallax, video in scala) né i suoi angoli
        // arrotondati; si risolve spostando il ritaglio sugli strati, nel renderer della sezione.
        $rules[] = "section:has({$sel} .uk-drop.uk-open), section:has({$sel} .olo-nav-search-item:focus-within) { z-index:100000; overflow:visible !important; }";
        // Pannelli fissi in vista: aperti, in entrata o in uscita (UIkit toglie uk-open all'inizio
        // della chiusura e tiene uk-togglable-leave fino alla fine; lo schermo intero tiene
        // olo-nav-uscita per la dissolvenza).
        $fissi = [
            "{$sel} .olo-nav-fullscreen.uk-open",
            "{$sel} .olo-nav-fullscreen.olo-nav-uscita",
            "{$sel} .uk-offcanvas.uk-open",
            "{$sel} .uk-offcanvas.uk-togglable-enter",
            "{$sel} .uk-offcanvas.uk-togglable-leave",
        ];
        $rules[] = 'section:has(' . implode( '), section:has(', $fissi ) . ') { z-index:100000; }';
        // L'Ombra del riquadro della tile, su un wrapper trasparente, è un filter, e il filtro
        // sfondo degli Effetti un backdrop-filter: entrambi fanno da blocco contenitore ai
        // position:fixed, e l'off-canvas o lo schermo intero restavano grandi quanto la barra (su
        // telefono 390x24). Si sospendono solo mentre un pannello fisso è in vista: copre la pagina,
        // l'effetto mancante non si vede; da chiuso torna com'era.
        $rules[] = '.olo-frontend-tile:has(' . implode( '), .olo-frontend-tile:has(', $fissi ) . ') { filter:none !important; -webkit-backdrop-filter:none !important; backdrop-filter:none !important; }';

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
            <nav class="olo-nav-bar" role="navigation" aria-label="<?php echo esc_attr__( 'Main menu', 'olobuild' ); ?>">
                <?php if ( $mobile ) :
                    $h_sz = max( 16, intval( $s['hamburger_size'] ) );
                    // «Schermo intero» lo apre il suo script: con uk-toggle lo stesso clic metteva anche
                    // `hidden` sul pannello (uk-open + hidden = invisibile) e il menu non si apriva mai.
                    $fullscreen = self::mobile_fullscreen( $s );
                ?>
                    <a id="<?php echo esc_attr( $nav_id ); ?>-btn" class="uk-hidden@m olo-nav-toggle" href="#<?php echo esc_attr( $nav_id ); ?>"<?php echo $fullscreen ? ' aria-controls="' . esc_attr( $nav_id ) . '" aria-haspopup="dialog"' : ' uk-toggle'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributi fissi, $nav_id passato da esc_attr() ?> aria-label="<?php echo esc_attr__( 'Open menu', 'olobuild' ); ?>" aria-expanded="false">
                        <svg width="<?php echo (int) $h_sz; ?>" height="<?php echo (int) $h_sz; ?>" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                    </a>
                <?php endif; ?>
                <?php
                $search_pos    = $s['search_position'] ?? 'after';
                $has_search_ref = ! empty( $s['search_tile_id'] );
                $has_search_legacy = ! $has_search_ref && ! empty( $s['append_search'] );
                ?>
                <ul class="uk-navbar-nav uk-visible@m <?php echo esc_attr( $flex_class ); ?>">
                    <?php if ( $has_search_ref && $search_pos === 'before' ) : ?>
                        <?php $this->render_referenced_search( $s['search_tile_id'], $s ); ?>
                    <?php endif; ?>
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
                        <li<?php echo $li_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attribute built above with an esc_attr()'d class list ?>>
                            <?php if ( $is_button ) : ?>
                                <?php $this->render_button_link( $item, $s ); ?>
                            <?php else : ?>
                                <?php $has_dropdown = ! empty( $subs ); $drop_id = $nav_id . '-drop-' . $idx; ?>
                                <a href="<?php echo esc_url( $item->url ); ?>"<?php echo $item->target ? ' target="' . esc_attr( $item->target ) . '"' : ''; ?><?php echo $is_current ? ' aria-current="page"' : ''; ?><?php echo $has_dropdown ? ' aria-haspopup="true" aria-expanded="false" aria-controls="' . esc_attr( $drop_id ) . '"' : ''; ?>>
                                    <?php echo esc_html( $item->title ); ?>
                                </a>
                                <?php if ( $is_mega && ! empty( $subs ) ) : ?>
                                    <?php $this->render_mega_dropdown( $subs, $grandchildren, $s, $drop_id ); ?>
                                <?php elseif ( ! empty( $subs ) ) : ?>
                                    <div id="<?php echo esc_attr( $drop_id ); ?>" uk-drop="mode: hover; delay-show: 0; delay-hide: 200; pos: bottom-left; offset: 0; animation: uk-animation-slide-top-small; duration: 150">
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
                    <?php if ( $has_search_ref && $search_pos !== 'before' ) : ?>
                        <?php $this->render_referenced_search( $s['search_tile_id'], $s ); ?>
                    <?php elseif ( $has_search_legacy ) : ?>
                        <?php $this->render_navbar_search( $s ); ?>
                    <?php endif; ?>
                </ul>
            </nav>

            <?php if ( $mobile ) : ?>
                <?php
                // Fullscreen mobile menu
                if ( self::mobile_fullscreen( $s ) ) : ?>
                    <div id="<?php echo esc_attr( $nav_id ); ?>" class="olo-nav-fullscreen" role="dialog" aria-modal="true" tabindex="-1" aria-label="<?php echo esc_attr__( 'Mobile menu', 'olobuild' ); ?>">
                        <button class="uk-close uk-close-large" type="button" uk-close data-olo-nav-close aria-label="<?php echo esc_attr__( 'Close menu', 'olobuild' ); ?>"></button>
                        <?php if ( ! empty( $s['fs_logo'] ) ) : ?>
                            <img class="olo-nav-fs-logo" src="<?php echo esc_url( $s['fs_logo'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
                        <?php endif; ?>
                        <div class="olo-nav-fs-corpo">
                            <ul class="uk-nav uk-nav-default uk-nav-parent-icon" uk-nav>
                                <?php $this->render_mobile_items( $tree, $children, $grandchildren, [ 'fs' => true, 's' => $s ] ); ?>
                                <?php if ( $has_search_ref ) { $this->render_referenced_search( $s['search_tile_id'], $s, true ); } elseif ( $has_search_legacy ) { $this->render_mobile_search( $s ); } ?>
                            </ul>
                        </div>
                    </div>
                    <script>
                    (function(){
                        var btn = document.getElementById('<?php echo esc_js( $nav_id ); ?>-btn');
                        var panel = document.getElementById('<?php echo esc_js( $nav_id ); ?>');
                        if (!btn) { return; }
                        if (!panel) { return; }
                        var aperto = false;
                        var html = document.documentElement;
                        /* Un dialog: il fuoco entra (sulla X), Tab resta dentro, Esc o la X chiudono
                           e il fuoco torna all'hamburger; la pagina sotto non scorre. */
                        function focusabili() {
                            return Array.prototype.filter.call(panel.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), select, textarea, [tabindex="0"]'), function (el) {
                                return el.getClientRects().length > 0;
                            });
                        }
                        function apri() {
                            aperto = true;
                            panel.classList.add('uk-open');
                            btn.setAttribute('aria-expanded', 'true');
                            /* L'hamburger fisso (z-index sopra il pannello) finiva sopra la X. */
                            btn.style.visibility = 'hidden';
                            html.style.overflow = 'hidden';
                            document.body.style.overflow = 'hidden';
                            var x = panel.querySelector('[data-olo-nav-close]');
                            setTimeout(function () { (x || panel).focus(); }, 60);
                        }
                        /* Durante la dissolvenza di chiusura resta olo-nav-uscita, che tiene sospesa
                           l'Ombra del riquadro (CSS della tile): se tornasse subito, il pannello in
                           chiusura ricadrebbe dentro la barra. Durata = transizione del pannello. */
                        var uscitaTimer = null;
                        function durataUscita() {
                            var cs = getComputedStyle(panel);
                            var dd = String(cs.transitionDuration || '0s').split(',');
                            var rr = String(cs.transitionDelay || '0s').split(',');
                            var max = 0;
                            for (var i = 0; i < dd.length; i++) {
                                var t = (parseFloat(dd[i]) || 0) + (parseFloat(rr[i % rr.length]) || 0);
                                if (t > max) { max = t; }
                            }
                            return Math.ceil(max * 1000) + 50;
                        }
                        function chiudi(ridai) {
                            if (!aperto) { return; }
                            aperto = false;
                            if (uscitaTimer) { clearTimeout(uscitaTimer); }
                            panel.classList.add('olo-nav-uscita');
                            panel.classList.remove('uk-open');
                            uscitaTimer = setTimeout(function () {
                                uscitaTimer = null;
                                panel.classList.remove('olo-nav-uscita');
                            }, durataUscita());
                            btn.setAttribute('aria-expanded', 'false');
                            btn.style.visibility = '';
                            html.style.overflow = '';
                            document.body.style.overflow = '';
                            if (ridai) { btn.focus(); }
                        }
                        btn.addEventListener('click', function (e) {
                            e.preventDefault();
                            if (aperto) { chiudi(true); } else { apri(); }
                        });
                        panel.addEventListener('click', function (e) {
                            if (e.target.closest('[data-olo-nav-close]')) { chiudi(true); return; }
                            /* Una voce scelta chiude il menu (anche i link ad ancore della stessa pagina). */
                            if (e.target.closest('a[href]')) { chiudi(false); }
                        });
                        document.addEventListener('keydown', function (e) {
                            if (!aperto) { return; }
                            if (e.key === 'Escape') { chiudi(true); return; }
                            if (e.key !== 'Tab') { return; }
                            var f = focusabili();
                            if (!f.length) { return; }
                            var primo = f[0], ultimo = f[f.length - 1];
                            if (e.shiftKey) {
                                if (document.activeElement === primo) { e.preventDefault(); ultimo.focus(); }
                            } else if (document.activeElement === ultimo) { e.preventDefault(); primo.focus(); }
                        });
                    })();
                    </script>
                <?php elseif ( $mob_style === 'offcanvas' ) : ?>
                    <div id="<?php echo esc_attr( $nav_id ); ?>" uk-offcanvas="overlay: true" role="dialog" aria-label="<?php echo esc_attr__( 'Mobile menu', 'olobuild' ); ?>">
                        <div class="uk-offcanvas-bar">
                            <button class="uk-offcanvas-close" type="button" uk-close aria-label="<?php echo esc_attr__( 'Close menu', 'olobuild' ); ?>"></button>
                            <ul class="uk-nav uk-nav-default uk-nav-parent-icon" uk-nav>
                                <?php $this->render_mobile_items( $tree, $children, $grandchildren ); ?>
                                <?php if ( $has_search_ref ) { $this->render_referenced_search( $s['search_tile_id'], $s, true ); } elseif ( $has_search_legacy ) { $this->render_mobile_search( $s ); } ?>
                            </ul>
                        </div>
                    </div>
                <?php else : ?>
                    <?php // Niente `uk-hidden` qui: è display:none !important, e con uk-open il menu restava 0×0. Il drop chiuso lo nasconde già UIkit (.uk-drop). ?>
                    <div id="<?php echo esc_attr( $nav_id ); ?>" uk-drop="mode: click; toggle: #<?php echo esc_attr( $nav_id ); ?>-btn; pos: bottom-left; offset: 0">
                        <div class="uk-card uk-card-body uk-card-default uk-card-small">
                            <ul class="uk-nav uk-nav-default uk-nav-parent-icon" uk-nav>
                                <?php $this->render_mobile_items( $tree, $children, $grandchildren ); ?>
                                <?php if ( $has_search_ref ) { $this->render_referenced_search( $s['search_tile_id'], $s, true ); } elseif ( $has_search_legacy ) { $this->render_mobile_search( $s ); } ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <script>
        (function(){
            var nav = document.querySelector('.olo-navmenu--<?php echo esc_js( $nav_id ); ?>');
            <?php if ( empty( $s['_builder_mode'] ) ) : ?>
            /* Nel corpo della pagina il transform del template intrappolava hamburger fisso e
               offcanvas (alti quanto la pagina, dal suo inizio). Sul sito il template si sgancia. */
            (<?php echo self::js_sgancia_template(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- costante JS interna ?>)(nav);
            <?php endif; ?>
            if (!nav) return;
            /* UIkit arriva in fondo alla pagina (defer): qui non c'è ancora, e il vecchio
               `if (!window.UIkit) return;` saltava tutto quello che segue. Si aspetta il DOM. */
            function lega() {
                if (!window.UIkit) { return; }
                /* aria-expanded segue l'hamburger di Tendina e Pannello laterale (lo Schermo intero lo
                   tiene già il suo script) e le voci con sottomenu. Sugli eventi conclusivi: all'evento
                   hide la tendina di UIkit toglie l'attributo, dopo i gestori di qui. */
                var hb = nav.querySelector('.olo-nav-toggle:not([aria-haspopup="dialog"])');
                var mob = hb ? document.getElementById(hb.getAttribute('href').slice(1)) : null;
                if (mob) {
                    UIkit.util.on(mob, 'shown', function(e){ if (e.target === mob) { hb.setAttribute('aria-expanded', 'true'); } });
                    UIkit.util.on(mob, 'hidden', function(e){ if (e.target === mob) { hb.setAttribute('aria-expanded', 'false'); } });
                }
                nav.querySelectorAll('[aria-haspopup="true"][aria-controls]').forEach(function(trigger){
                    var drop = document.getElementById(trigger.getAttribute('aria-controls'));
                    if (!drop) return;
                    UIkit.util.on(drop, 'shown', function(e){ if (e.target === drop) { trigger.setAttribute('aria-expanded', 'true'); } });
                    UIkit.util.on(drop, 'hidden', function(e){ if (e.target === drop) { trigger.setAttribute('aria-expanded', 'false'); } });
                });
            }
            if (window.UIkit) { lega(); } else { document.addEventListener('DOMContentLoaded', lega); }
        })();
        </script>
        <?php
    }

    /**
     * Menu mobile «Schermo intero». Prima stava in `mobile_type` (un secondo select che contava
     * solo per questo valore); dal 1.4.570 l'inspector ha un solo select, `mobile_style`, che
     * ricopia la scelta anche su `mobile_type`. Vale l'uno o l'altro: i template salvati prima
     * restano a schermo intero.
     */
    private static function mobile_fullscreen( $s ) {
        return ( $s['mobile_type'] ?? '' ) === 'fullscreen' || ( $s['mobile_style'] ?? '' ) === 'fullscreen';
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
    private function render_mega_dropdown( $subs, $grandchildren, $s, $drop_id = '' ) {
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
        <div class="olo-mega-drop"<?php echo $drop_id ? ' id="' . esc_attr( $drop_id ) . '"' : ''; ?> uk-drop="mode: hover; delay-show: 0; delay-hide: 200; pos: bottom-left; offset: 0; animation: uk-animation-slide-top-small; duration: 150">
            <div class="uk-card uk-card-body uk-card-default olo-mega-panel">
                <?php if ( $has_grandchildren ) : ?>
                    <?php // Subs become column headers, grandchildren become items ?>
                    <div class="uk-child-width-1-<?php echo (int) $columns; ?>@m" uk-grid>
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
                    <div class="uk-child-width-1-<?php echo (int) $columns; ?>@m" uk-grid>
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
                    <div class="olo-mega-search" style="border-top:1px solid var(--olo-color-border, #E5E7EB);padding:12px 16px 4px;margin-top:8px">
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
    /**
     * Voci del menu mobile. Il target della voce resta (prima «Provalo» in una nuova scheda su
     * telefono si apriva nella stessa) e la voce della pagina corrente è marcata. Nello schermo
     * intero ($opz['fs']) il nome sta in uno span, con la descrizione della voce sotto se
     * «Mostra le descrizioni» è acceso, e le Voci pulsante prendono la classe del pulsante.
     */
    private function render_mobile_items( $tree, $children, $grandchildren, $opz = [] ) {
        $fs    = ! empty( $opz['fs'] );
        $s     = $opz['s'] ?? [];
        $desc  = $fs && ! empty( $s['fs_descriptions'] );
        $total = count( $tree );
        $qui   = trailingslashit( home_url( add_query_arg( [], false ) ) );
        $link  = function ( $it ) use ( $fs, $desc, $qui ) {
            $html = '<a href="' . esc_url( $it->url ) . '"';
            if ( $it->target ) {
                $html .= ' target="' . esc_attr( $it->target ) . '"' . ( '_blank' === $it->target ? ' rel="noopener"' : '' );
            }
            if ( trailingslashit( $it->url ) === $qui ) { $html .= ' aria-current="page"'; }
            $html .= '>';
            if ( $fs ) {
                $html .= '<span class="olo-nav-fs-nome">' . esc_html( $it->title ) . '</span>';
                if ( $desc && '' !== trim( (string) ( $it->description ?? '' ) ) ) {
                    $html .= '<span class="olo-nav-fs-desc">' . esc_html( $it->description ) . '</span>';
                }
            } else {
                $html .= esc_html( $it->title );
            }
            return $html . '</a>';
        };
        foreach ( array_values( $tree ) as $idx => $item ) {
            $subs = $children[ $item->ID ] ?? [];
            $cls  = [];
            if ( ! empty( $subs ) ) { $cls[] = 'uk-parent'; }
            if ( trailingslashit( $item->url ) === $qui ) { $cls[] = 'uk-active'; }
            if ( $fs && $s && $this->is_button_item( $item, $idx, $total, $s ) ) { $cls[] = 'olo-nav-fs-btn'; }
            echo '<li' . ( $cls ? ' class="' . esc_attr( implode( ' ', $cls ) ) . '"' : '' ) . '>' . $link( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $link() escapes url, target and text.
            if ( ! empty( $subs ) ) {
                echo '<ul class="uk-nav-sub">';
                foreach ( $subs as $sub ) {
                    $gc = $grandchildren[ $sub->ID ] ?? [];
                    echo '<li' . ( ! empty( $gc ) ? ' class="uk-parent"' : '' ) . '>' . $link( $sub ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $link() escapes.
                    if ( ! empty( $gc ) ) {
                        echo '<ul class="uk-nav-sub">';
                        foreach ( $gc as $gci ) {
                            echo '<li>' . $link( $gci ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $link() escapes.
                        }
                        echo '</ul>';
                    }
                    echo '</li>';
                }
                echo '</ul>';
            }
            echo '</li>';
        }
    }

    /**
     * Render the appended search widget as the last <li> in the navbar.
     * Delegates to Olobuild_LiveSearch_Tile for the actual HTML.
     */
    private function render_navbar_search( $s ) {
        $livesearch_tile = Olobuild_Tile_Manager::instance()->get_tile( 'livesearch' );
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
            'icon_color'      => $s['text_color'] ?: 'var(--olo-color-text-muted, #9CA3AF)',
        ];

        // Enqueue LiveSearch assets
        wp_enqueue_style( 'olo-livesearch-css', OLOBUILD_URL . 'assets/css/olo-livesearch.css', [], OLOBUILD_VERSION );
        wp_enqueue_script( 'olo-livesearch-js', OLOBUILD_URL . 'assets/js/olo-livesearch.js', [], OLOBUILD_VERSION, true );

        $html = $livesearch_tile->render( $search_settings );

        echo '<li class="olo-nav-search-item">';
        echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML generated by Olobuild_LiveSearch_Tile::render(), which escapes its output internally
        echo '</li>';
    }

    /**
     * Render a search field inside mobile menu.
     */
    private function render_mobile_search( $s ) {
        $livesearch_tile = Olobuild_Tile_Manager::instance()->get_tile( 'livesearch' );
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
            'input_color'          => $s['search_input_color'] ?: 'var(--olo-color-text, #374151)',
            'icon_color'           => $s['search_icon_color'] ?: 'var(--olo-color-text-muted, #9CA3AF)',
            'results_border_color' => $s['search_border_color'] ?: 'var(--olo-color-border, #E5E7EB)',
            'results_bg'           => $s['search_results_bg'] ?: '#ffffff',
            'item_hover_bg'        => $s['search_hover_bg'] ?: 'var(--olo-color-muted, #F3F4F6)',
        ];

        echo '<li class="olo-nav-search-mobile">';
        echo $livesearch_tile->render( $search_settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML generated by Olobuild_LiveSearch_Tile::render(), which escapes its output internally
        echo '</li>';
    }

    /**
     * Render an inline search field inside mega menu panels.
     */
    private function render_mega_search( $s ) {
        $livesearch_tile = Olobuild_Tile_Manager::instance()->get_tile( 'livesearch' );
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
            'input_color'          => $s['search_input_color'] ?: 'var(--olo-color-text, #374151)',
            'icon_color'           => $s['search_icon_color'] ?: 'var(--olo-color-text-muted, #9CA3AF)',
            'results_border_color' => $s['search_border_color'] ?: 'var(--olo-color-border, #E5E7EB)',
            'results_bg'           => $s['search_results_bg'] ?: '#ffffff',
            'item_hover_bg'        => $s['search_hover_bg'] ?: 'var(--olo-color-muted, #F3F4F6)',
        ];

        wp_enqueue_style( 'olo-livesearch-css', OLOBUILD_URL . 'assets/css/olo-livesearch.css', [], OLOBUILD_VERSION );
        wp_enqueue_script( 'olo-livesearch-js', OLOBUILD_URL . 'assets/js/olo-livesearch.js', [], OLOBUILD_VERSION, true );

        echo $livesearch_tile->render( $search_settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML generated by Olobuild_LiveSearch_Tile::render(), which escapes its output internally
    }

    /**
     * Render a search tile referenced by ID from the same template.
     * Looks up tile settings via the frontend renderer's static registry.
     *
     * @param string $tile_id  The referenced tile UUID.
     * @param array  $s        NavMenu settings (for inheriting colors).
     * @param bool   $mobile   Whether rendering in mobile context.
     */
    private function render_referenced_search( $tile_id, $s, $mobile = false ) {
        if ( empty( $tile_id ) ) return;

        $tile_data = Olobuild_Frontend_Renderer::find_tile( $tile_id );
        if ( ! $tile_data ) return;

        $tile_type = $tile_data['type'];
        $tile_settings = $tile_data['settings'];

        $manager = Olobuild_Tile_Manager::instance();
        $tile_instance = $manager->get_tile( $tile_type );
        if ( ! $tile_instance ) return;

        // Enqueue assets
        if ( $tile_type === 'livesearch' ) {
            wp_enqueue_style( 'olo-livesearch-css', OLOBUILD_URL . 'assets/css/olo-livesearch.css', [], OLOBUILD_VERSION );
            wp_enqueue_script( 'olo-livesearch-js', OLOBUILD_URL . 'assets/js/olo-livesearch.js', [], OLOBUILD_VERSION, true );
        }

        if ( $mobile ) {
            // Force inline mode for mobile
            $tile_settings['mode'] = 'inline';
        }

        echo '<li class="olo-nav-search-item">';
        echo $tile_instance->render( $tile_settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML generated by the referenced tile's render() method, which escapes its output internally
        echo '</li>';
    }

    /**
     * Output inline script for header mode (overlay/classic) and sticky behavior.
     */
    private function render_header_script( $s, $nav_id ) {
        $mode       = esc_js( $s['header_mode'] ?? 'overlay' );
        $sticky     = ! empty( $s['sticky'] ) ? 'true' : 'false';
        $show_on_up = ! empty( $s['sticky_show_on_up'] ) ? 'true' : 'false';
        ?>
        <script>
        (function() {
            var mode = "<?php echo $mode; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped with esc_js() at assignment above ?>";
            var stickyEnabled = <?php echo $sticky; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed 'true'/'false' literal from ternary above ?>;
            var showOnUp = <?php echo $show_on_up; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed 'true'/'false' literal from ternary above ?>;
            /* Solo l'header del sito che CONTIENE il menu. Con querySelector un menu messo nel
               corpo della pagina aggiungeva olo-header-overlay all'header del sito (fisso, e nei
               temi a blocchi spariva il primo blocco della pagina) e lo rendeva sticky.
               Si cerca SUBITO, non a DOMContentLoaded: lo script segue il markup della nav e gira
               prima che il renderer sposti nel body le tile a «Posizione fissa»; dopo, la nav non
               sta più nell'header e lo sticky non partiva. La modalità è del primo menu
               dell'header, come la sceglie il server (detect_header_mode): un secondo menu
               aggiungeva la sua accanto all'altra. */
            var nav = null, header = null, primo = false;
            function trova() {
                nav = document.querySelector(".olo-navmenu--<?php echo esc_js( $nav_id ); ?>");
                header = nav ? nav.closest("header.olo-site-header") : null;
                primo = header ? header.querySelector(".olo-navmenu, .olo-megamenu") === nav : false;
            }
            trova();
            function init() {
                if (!header) { trova(); }
                if (!header) return;
                if (primo) {
                    header.classList.add("olo-header-" + mode);
                }
                // Sticky scroll listener
                if (stickyEnabled) {
                    header.classList.add("olo-sticky-on");
                    /* Segna l'header di QUESTO menu per lo «Sfondo sticky»: un :has() sulla nav
                       non lo troverebbe con la tile spostata nel body. */
                    header.classList.add("olo-hdr-<?php echo esc_js( $nav_id ); ?>");
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
     * Render as vertical navigation list.
     */
    private function render_vertical( $tree, $children, $grandchildren, $s, $nav_id ) {
        $current_url   = trailingslashit( home_url( add_query_arg( [], false ) ) );
        $show_icons    = ! empty( $s['v_show_icons'] );
        $expand_subs   = ! empty( $s['v_expand_subs'] );
        $icon_style    = $s['v_icon_style'] ?: 'line';
        $icon_size     = max( 14, intval( $s['v_icon_size'] ) );
        $alignment     = $s['alignment'] ?: 'left';
        ?>
        <nav class="olo-navmenu olo-navmenu--<?php echo esc_attr( $nav_id ); ?> olo-vnav" role="navigation" aria-label="<?php echo esc_attr__( 'Vertical menu', 'olobuild' ); ?>">
            <ul class="olo-vnav-list" style="text-align:<?php echo esc_attr( $alignment ); ?>">
                <?php foreach ( $tree as $item ) :
                    $subs       = $children[ $item->ID ] ?? [];
                    $is_current = trailingslashit( $item->url ) === $current_url;
                    $li_cls     = 'olo-vnav-item';
                    if ( $is_current ) $li_cls .= ' olo-vnav-item--active';
                    if ( ! empty( $subs ) ) $li_cls .= ' olo-vnav-item--parent';
                ?>
                    <li class="<?php echo esc_attr( $li_cls ); ?>">
                        <a href="<?php echo esc_url( $item->url ); ?>" class="olo-vnav-link"<?php echo $item->target ? ' target="' . esc_attr( $item->target ) . '"' : ''; ?><?php echo $is_current ? ' aria-current="page"' : ''; ?>>
                            <?php if ( $show_icons ) : ?>
                                <span class="olo-vnav-icon"><?php echo $this->get_vnav_icon( $icon_style, $icon_size ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup generated by get_vnav_icon() with an intval()'d size ?></span>
                            <?php endif; ?>
                            <span class="olo-vnav-label"><?php echo esc_html( $item->title ); ?></span>
                            <?php if ( ! empty( $subs ) && $expand_subs ) : ?>
                                <button type="button" class="olo-vnav-chev" aria-expanded="true" aria-label="<?php echo esc_attr( olobuild_t( 'Apri o chiudi le sottovoci' ) ); ?>"><svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" focusable="false"><path d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"/></svg></button>
                            <?php endif; ?>
                        </a>
                        <?php
                        // Con «Sottovoci espandibili» spento le sottovoci erano nascoste (display:none) e
                        // nessun comando le apriva: irraggiungibili, con una freccia che non faceva niente.
                        // Spento = sempre in vista, senza freccia; acceso = freccia che apre e chiude.
                        if ( ! empty( $subs ) ) : ?>
                            <ul class="olo-vnav-sub">
                                <?php foreach ( $subs as $sub ) :
                                    $gc = $grandchildren[ $sub->ID ] ?? [];
                                    $sub_current = trailingslashit( $sub->url ) === $current_url;
                                    $sub_cls = 'olo-vnav-item olo-vnav-item--sub';
                                    if ( $sub_current ) $sub_cls .= ' olo-vnav-item--active';
                                ?>
                                    <li class="<?php echo esc_attr( $sub_cls ); ?>">
                                        <a href="<?php echo esc_url( $sub->url ); ?>" class="olo-vnav-link"<?php echo $sub->target ? ' target="' . esc_attr( $sub->target ) . '"' : ''; ?><?php echo $sub_current ? ' aria-current="page"' : ''; ?>>
                                            <?php if ( $show_icons ) : ?>
                                                <span class="olo-vnav-icon"><?php echo $this->get_vnav_icon( $icon_style, max( 12, $icon_size - 4 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup generated by get_vnav_icon() with an intval()'d size ?></span>
                                            <?php endif; ?>
                                            <span class="olo-vnav-label"><?php echo esc_html( $sub->title ); ?></span>
                                        </a>
                                        <?php if ( ! empty( $gc ) ) : ?>
                                            <ul class="olo-vnav-sub olo-vnav-sub--deep">
                                                <?php foreach ( $gc as $gci ) : ?>
                                                    <li class="olo-vnav-item olo-vnav-item--sub">
                                                        <a href="<?php echo esc_url( $gci->url ); ?>" class="olo-vnav-link"<?php echo $gci->target ? ' target="' . esc_attr( $gci->target ) . '"' : ''; ?>>
                                                            <span class="olo-vnav-label"><?php echo esc_html( $gci->title ); ?></span>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <?php if ( $expand_subs ) : ?>
        <script>
        (function(){
            var nav = document.querySelector('.olo-navmenu--<?php echo esc_js( $nav_id ); ?>');
            if (!nav) return;
            nav.querySelectorAll('.olo-vnav-item--parent > .olo-vnav-link').forEach(function(link){
                var chev = link.querySelector('.olo-vnav-chev');
                if (!chev) return;
                chev.addEventListener('click', function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    var li = link.parentElement;
                    var sub = li.querySelector('.olo-vnav-sub');
                    if (sub) {
                        var open = sub.style.display !== 'none';
                        sub.style.display = open ? 'none' : '';
                        chev.style.transform = open ? 'rotate(-90deg)' : '';
                        if (chev.hasAttribute('aria-expanded')) {
                            chev.setAttribute('aria-expanded', open ? 'false' : 'true');
                        }
                    }
                });
            });
        })();
        </script>
        <?php endif; ?>
        <?php
    }

    /**
     * Get icon SVG for vertical nav items.
     */
    private function get_vnav_icon( $style, $size ) {
        $s = intval( $size );
        switch ( $style ) {
            case 'filled':
                return '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 20 20" fill="currentColor"><circle cx="10" cy="10" r="4"/></svg>';
            case 'circle':
                return '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="7" fill="currentColor" opacity="0.12"/><circle cx="10" cy="10" r="3" fill="currentColor"/></svg>';
            default: // line
                return '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="10" cy="10" r="4"/></svg>';
        }
    }

    /**
     * Render as UIkit subnav.
     */
    private function render_subnav( $tree, $alignment, $s, $nav_id ) {
        $align_class = '';
        if ( $alignment === 'center' ) $align_class = ' uk-flex-center';
        if ( $alignment === 'right' )  $align_class = ' uk-flex-right';
        $current_url = trailingslashit( home_url( add_query_arg( [], false ) ) );
        ?>
        <nav class="olo-navmenu olo-navmenu--<?php echo esc_attr( $nav_id ); ?>" role="navigation" aria-label="<?php echo esc_attr__( 'Sub navigation', 'olobuild' ); ?>">
            <ul class="uk-subnav<?php echo esc_attr( $align_class ); ?>">
                <?php foreach ( $tree as $item ) :
                    $is_current = trailingslashit( $item->url ) === $current_url;
                ?>
                    <li<?php echo $is_current ? ' class="uk-active"' : ''; ?>>
                        <a href="<?php echo esc_url( $item->url ); ?>"<?php echo $item->target ? ' target="' . esc_attr( $item->target ) . '"' : ''; ?><?php echo $is_current ? ' aria-current="page"' : ''; ?>>
                            <?php echo esc_html( $item->title ); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <?php
    }
}
