<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_MegaMenu_Tile extends Olo_Tile_Base {

    protected $type     = 'megamenu';
    protected $name     = 'Mega Menu';
    protected $icon     = 'dashicons-menu-alt3';
    protected $category = 'header';
    protected $defaults = [
        // Menu
        'menu_id'            => 0,
        // Panel templates mapping { "menu_item_id": template_id }
        'panel_templates'    => [],
        // Navbar
        'layout'             => 'left',
        'nav_bg'             => '',
        'nav_height'         => '0',
        'text_color'         => '',
        'hover_color'        => '',
        'active_color'       => '',
        'font_size'          => '15',
        'font_weight'        => 'normal',
        'text_transform'     => 'none',
        'letter_spacing'     => '0',
        'item_gap'           => '15',
        // Mega Panel
        'mega_mode'          => 'auto',
        'panel_width'        => 'container',
        'panel_columns'      => '4',
        'panel_bg'           => '#FFFFFF',
        'panel_shadow'       => 'md',
        'panel_radius'       => '8',
        'panel_padding'      => '32',
        'panel_border_top'   => '3',
        'panel_border_color' => '',
        'panel_animation'    => 'fade',
        'show_dividers'      => false,
        // Panel Typography
        'heading_color'      => '',
        'heading_size'       => '14',
        'heading_weight'     => '600',
        'heading_transform'  => 'uppercase',
        'link_color'         => '',
        'link_hover_color'   => '',
        'link_size'          => '14',
        'link_spacing'       => '8',
        'show_descriptions'  => false,
        'desc_color'         => '',
        // Buttons
        'button_mode'        => 'none',
        'btn_bg'             => '',
        'btn_color'          => '#FFFFFF',
        'btn_radius'         => '6',
        'btn_hover_bg'       => '',
        // Mobile
        'mobile_breakpoint'  => '1024',
        'mobile_side'        => 'left',
        'hamburger_color'    => '',
        'mobile_bg'          => '#1e1e2e',
        'mobile_text_color'  => '#FFFFFF',
        'mobile_heading_color' => '',
        'mobile_accent_color'  => '',
        'mobile_logo'        => '',
        'mobile_logo_height' => '36',
        // Header
        'header_mode'        => 'overlay',
        'sticky'             => false,
        'sticky_show_on_up'  => false,
        'sticky_bg'          => '',
        'sticky_shadow'      => true,
    ];

    public function get_controls() { return []; }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $menu_id = absint( $s['menu_id'] );
        if ( ! $menu_id ) {
            return '<div class="olo-megamenu"><p style="text-align:center;color:#999;padding:20px 0">Seleziona un menu nell\'Inspector.</p></div>';
        }

        $items = wp_get_nav_menu_items( $menu_id );
        if ( ! $items || ! is_array( $items ) ) {
            return '<div class="olo-megamenu"><p style="text-align:center;color:#999;padding:20px 0">Menu vuoto o non trovato.</p></div>';
        }

        // Build 3-level hierarchy
        $tree          = [];
        $children      = [];
        $grandchildren = [];
        $top_ids       = [];
        $child_ids     = [];

        foreach ( $items as $item ) {
            if ( (int) $item->menu_item_parent === 0 ) {
                $tree[]               = $item;
                $top_ids[ $item->ID ] = true;
            }
        }
        foreach ( $items as $item ) {
            $pid = (int) $item->menu_item_parent;
            if ( $pid !== 0 && isset( $top_ids[ $pid ] ) ) {
                $children[ $pid ][]     = $item;
                $child_ids[ $item->ID ] = true;
            }
        }
        foreach ( $items as $item ) {
            $pid = (int) $item->menu_item_parent;
            if ( $pid !== 0 && ! isset( $top_ids[ $pid ] ) && isset( $child_ids[ $pid ] ) ) {
                $grandchildren[ $pid ][] = $item;
            }
        }

        $uid = 'olo-mm-' . wp_rand( 10000, 99999 );

        ob_start();
        $this->render_css( $s, $uid );
        $this->render_html( $tree, $children, $grandchildren, $s, $uid );
        $this->render_js( $s, $uid );
        return ob_get_clean();
    }

    /* ─── CSS ─── */

    private function render_css( $s, $uid ) {
        $bp          = intval( $s['mobile_breakpoint'] ) ?: 1024;
        $nav_bg      = $this->safe_color( $s['nav_bg'] );
        $nav_h       = intval( $s['nav_height'] );
        $tc          = $this->safe_color( $s['text_color'] ) ?: 'inherit';
        $hc          = $this->safe_color( $s['hover_color'] );
        $ac          = $this->safe_color( $s['active_color'] );
        $fs          = intval( $s['font_size'] ) ?: 15;
        $fw          = esc_attr( $s['font_weight'] ?: 'normal' );
        $tt          = esc_attr( $s['text_transform'] ?: 'none' );
        $ls          = floatval( $s['letter_spacing'] );
        $gap         = intval( $s['item_gap'] ) ?: 15;

        // Panel
        $p_bg        = $this->safe_color( $s['panel_bg'] ) ?: '#FFFFFF';
        $p_cols      = max( 2, min( 6, intval( $s['panel_columns'] ) ) );
        $p_radius    = intval( $s['panel_radius'] );
        $p_pad       = intval( $s['panel_padding'] ) ?: 32;
        $p_bt        = intval( $s['panel_border_top'] );
        $p_bc        = $this->safe_color( $s['panel_border_color'] ) ?: 'var(--olo-color-primary, #6366F1)';
        $p_anim      = $s['panel_animation'] === 'slide-down' ? 'slide' : 'fade';
        $p_shadow    = Olo_Tile_Utils::shadow( $s['panel_shadow'] ?? 'none', 'panel' );
        $dividers    = ! empty( $s['show_dividers'] );

        // Panel typography
        $h_color     = $this->safe_color( $s['heading_color'] ) ?: '#111827';
        $h_size      = intval( $s['heading_size'] ) ?: 14;
        $h_weight    = intval( $s['heading_weight'] ) ?: 600;
        $h_tt        = esc_attr( $s['heading_transform'] ?: 'none' );
        $l_color     = $this->safe_color( $s['link_color'] ) ?: '#4B5563';
        $l_hcolor    = $this->safe_color( $s['link_hover_color'] ) ?: 'var(--olo-color-primary, #6366F1)';
        $l_size      = intval( $s['link_size'] ) ?: 14;
        $l_spacing   = intval( $s['link_spacing'] ) ?: 8;
        $desc_color  = $this->safe_color( $s['desc_color'] ) ?: '#9CA3AF';

        // Buttons
        $btn_bg      = $this->safe_color( $s['btn_bg'] ) ?: 'var(--olo-color-primary, #6366F1)';
        $btn_color   = $this->safe_color( $s['btn_color'] ) ?: '#FFFFFF';
        $btn_radius  = intval( $s['btn_radius'] );
        $btn_hbg     = $this->safe_color( $s['btn_hover_bg'] );

        // Mobile
        $mob_side    = $s['mobile_side'] === 'right' ? 'right' : 'left';
        $ham_color   = $this->safe_color( $s['hamburger_color'] ) ?: $tc;
        $mob_bg      = $this->safe_color( $s['mobile_bg'] ) ?: '#1e1e2e';
        $mob_tc      = $this->safe_color( $s['mobile_text_color'] ) ?: '#FFFFFF';
        $mob_hc      = $this->safe_color( $s['mobile_heading_color'] ) ?: 'rgba(255,255,255,.5)';
        $mob_acc     = $this->safe_color( $s['mobile_accent_color'] ) ?: 'var(--olo-color-primary, #6366F1)';
        $mob_logo_h  = intval( $s['mobile_logo_height'] ) ?: 36;

        // Sticky
        $sticky_bg   = $this->safe_color( $s['sticky_bg'] );
        $sticky_shad = ! empty( $s['sticky_shadow'] );

        // Animation transform
        $anim_from = $p_anim === 'slide' ? 'translateY(-12px)' : 'translateY(0)';
        ?>
        <style>
        /* === Navbar Container === */
        .<?php echo $uid; ?> {
            position: relative;
            z-index: 100;
            margin: 0;
            padding: 0;
            width: 100%;
            box-sizing: border-box;
            border: none;
            box-shadow: none;
            outline: none;
            background: transparent;
        }
        .<?php echo $uid; ?> .olo-mm-bar {
            display: flex;
            align-items: center;
            <?php if ( $nav_h > 0 ) : ?>min-height: <?php echo $nav_h; ?>px;<?php endif; ?>
            padding: 0;
            width: 100%;
            box-sizing: border-box;
            <?php if ( $nav_bg ) : ?>background: <?php echo $nav_bg; ?>;<?php endif; ?>
            transition: background .3s, box-shadow .3s;
        }
        .<?php echo $uid; ?> .olo-mm-nav {
            display: flex;
            align-items: center;
            gap: <?php echo $gap; ?>px;
            list-style: none;
            margin: 0;
            padding: 0;
            flex: 1;
            <?php if ( $s['layout'] === 'center' ) : ?>justify-content: center;
            <?php elseif ( $s['layout'] === 'right' ) : ?>justify-content: flex-end;
            <?php endif; ?>
        }
        .<?php echo $uid; ?> .olo-mm-nav > li {
            position: relative;
        }
        .<?php echo $uid; ?> .olo-mm-nav > li > a {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 8px 0;
            color: <?php echo $tc; ?>;
            font-size: <?php echo $fs; ?>px;
            font-weight: <?php echo $fw; ?>;
            text-transform: <?php echo $tt; ?>;
            <?php if ( $ls > 0 ) : ?>letter-spacing: <?php echo $ls; ?>px;<?php endif; ?>
            text-decoration: none;
            white-space: nowrap;
            transition: color .2s;
            position: relative;
        }
        /* Active indicator underline */
        .<?php echo $uid; ?> .olo-mm-nav > li > a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: <?php echo $ac ?: 'var(--olo-color-primary, #6366F1)'; ?>;
            transform: scaleX(0);
            transition: transform .25s ease;
        }
        .<?php echo $uid; ?> .olo-mm-nav > li.olo-mm-active > a::after,
        .<?php echo $uid; ?> .olo-mm-nav > li > a:hover::after {
            transform: scaleX(1);
        }
        <?php if ( $hc ) : ?>
        .<?php echo $uid; ?> .olo-mm-nav > li > a:hover {
            color: <?php echo $hc; ?>;
        }
        <?php endif; ?>
        <?php if ( $ac ) : ?>
        .<?php echo $uid; ?> .olo-mm-nav > li.olo-mm-active > a {
            color: <?php echo $ac; ?>;
        }
        <?php endif; ?>
        /* Chevron on items with mega panel */
        .<?php echo $uid; ?> .olo-mm-chevron {
            width: 10px; height: 10px;
            opacity: .5;
            transition: transform .25s;
        }
        .<?php echo $uid; ?> .olo-mm-nav > li.olo-mm-open .olo-mm-chevron {
            transform: rotate(180deg);
        }

        /* === Mega Panel === */
        .<?php echo $uid; ?> .olo-mm-panel {
            position: absolute;
            <?php if ( $s['panel_width'] !== 'full' ) : ?>
            left: 0;
            min-width: 600px;
            max-width: 900px;
            <?php endif; ?>
            top: 100%;
            z-index: 200;
            background: <?php echo $p_bg; ?>;
            border-radius: <?php echo $p_radius; ?>px;
            padding: <?php echo $p_pad; ?>px;
            <?php if ( $p_bt > 0 ) : ?>border-top: <?php echo $p_bt; ?>px solid <?php echo $p_bc; ?>;<?php endif; ?>
            <?php if ( $p_shadow !== 'none' ) : ?>box-shadow: <?php echo $p_shadow; ?>;<?php endif; ?>
            opacity: 0;
            <?php if ( $p_anim === 'slide' ) : ?>
            transform: translateY(-12px);
            <?php endif; ?>
            visibility: hidden;
            transition: opacity .25s ease, transform .25s ease, visibility .25s;
            pointer-events: none;
        }
        .<?php echo $uid; ?> .olo-mm-nav > li.olo-mm-open > .olo-mm-panel {
            opacity: 1;
            transform: none;
            visibility: visible;
            pointer-events: auto;
        }
        .<?php echo $uid; ?> .olo-mm-grid {
            display: grid;
            grid-template-columns: repeat(<?php echo $p_cols; ?>, 1fr);
            gap: <?php echo $p_pad; ?>px;
        }
        <?php if ( $dividers ) : ?>
        .<?php echo $uid; ?> .olo-mm-col:not(:last-child) {
            border-right: 1px solid rgba(0,0,0,.08);
            padding-right: <?php echo $p_pad; ?>px;
        }
        <?php endif; ?>

        /* Panel headings */
        .<?php echo $uid; ?> .olo-mm-heading {
            font-size: <?php echo $h_size; ?>px;
            font-weight: <?php echo $h_weight; ?>;
            color: <?php echo $h_color; ?>;
            text-transform: <?php echo $h_tt; ?>;
            margin: 0 0 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(0,0,0,.06);
            line-height: 1.3;
        }
        .<?php echo $uid; ?> .olo-mm-heading a {
            color: inherit;
            text-decoration: none;
        }
        .<?php echo $uid; ?> .olo-mm-heading a:hover {
            color: <?php echo $l_hcolor; ?>;
        }
        /* Panel links */
        .<?php echo $uid; ?> .olo-mm-links {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .<?php echo $uid; ?> .olo-mm-link {
            display: block;
            padding: <?php echo $l_spacing; ?>px 0;
            color: <?php echo $l_color; ?>;
            font-size: <?php echo $l_size; ?>px;
            text-decoration: none;
            transition: color .15s, padding-left .15s;
            line-height: 1.4;
        }
        .<?php echo $uid; ?> .olo-mm-link:hover {
            color: <?php echo $l_hcolor; ?>;
            padding-left: 4px;
        }
        .<?php echo $uid; ?> .olo-mm-desc {
            display: block;
            font-size: <?php echo max( $l_size - 2, 11 ); ?>px;
            color: <?php echo $desc_color; ?>;
            margin-top: 2px;
            line-height: 1.3;
        }
        /* Simple dropdown (L1 with children but no grandchildren and not mega) */
        .<?php echo $uid; ?> .olo-mm-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            min-width: 200px;
            background: <?php echo $p_bg; ?>;
            border-radius: <?php echo $p_radius; ?>px;
            padding: 8px 0;
            <?php if ( $p_shadow !== 'none' ) : ?>box-shadow: <?php echo $p_shadow; ?>;<?php endif; ?>
            opacity: 0;
            visibility: hidden;
            transition: opacity .2s, visibility .2s;
            pointer-events: none;
            z-index: 200;
        }
        .<?php echo $uid; ?> .olo-mm-nav > li.olo-mm-open > .olo-mm-dropdown {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }
        .<?php echo $uid; ?> .olo-mm-dropdown a {
            display: block;
            padding: 8px 20px;
            color: <?php echo $l_color; ?>;
            font-size: <?php echo $l_size; ?>px;
            text-decoration: none;
            transition: background .15s, color .15s;
        }
        .<?php echo $uid; ?> .olo-mm-dropdown a:hover {
            background: rgba(0,0,0,.04);
            color: <?php echo $l_hcolor; ?>;
        }

        /* Template panel: full viewport width */
        .<?php echo $uid; ?> .olo-mm-panel-tpl {
            max-width: none;
            min-width: 0;
            padding: 0;
            border-radius: 0 0 <?php echo $p_radius; ?>px <?php echo $p_radius; ?>px;
            /* left and width set by JS */
        }
        .<?php echo $uid; ?> .olo-mm-panel-tpl .olo-template {
            margin: 0;
        }

        /* === CTA Buttons === */
        .<?php echo $uid; ?> .olo-mm-btn {
            display: inline-flex;
            align-items: center;
            padding: 8px 20px;
            background: <?php echo $btn_bg; ?>;
            color: <?php echo $btn_color; ?> !important;
            border-radius: <?php echo $btn_radius; ?>px;
            font-size: <?php echo $fs; ?>px;
            font-weight: 600;
            text-decoration: none;
            transition: background .2s, transform .15s;
            white-space: nowrap;
        }
        .<?php echo $uid; ?> .olo-mm-btn:hover {
            <?php if ( $btn_hbg ) : ?>background: <?php echo $btn_hbg; ?>;<?php else : ?>filter: brightness(1.1);<?php endif; ?>
            transform: translateY(-1px);
        }
        .<?php echo $uid; ?> .olo-mm-btn::after { display: none; }

        /* === Hamburger === */
        .<?php echo $uid; ?> .olo-mm-hamburger {
            display: none;
            flex-direction: column;
            justify-content: center;
            gap: 5px;
            width: 28px;
            height: 28px;
            cursor: pointer;
            padding: 0;
            z-index: 10;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }
        .<?php echo $uid; ?> .olo-mm-hamburger,
        .<?php echo $uid; ?> .olo-mm-hamburger:focus,
        .<?php echo $uid; ?> .olo-mm-hamburger:active,
        .<?php echo $uid; ?> .olo-mm-hamburger:hover {
            background: none !important;
            border: none !important;
            box-shadow: none !important;
            outline: none !important;
        }
        .<?php echo $uid; ?> .olo-mm-hamburger span {
            display: block;
            width: 100%;
            height: 2px;
            background: <?php echo $ham_color; ?>;
            border-radius: 2px;
            transition: transform .3s, opacity .3s;
        }
        .<?php echo $uid; ?> .olo-mm-hamburger.olo-mm-ham-open span:nth-child(1) {
            transform: translateY(7px) rotate(45deg);
        }
        .<?php echo $uid; ?> .olo-mm-hamburger.olo-mm-ham-open span:nth-child(2) {
            opacity: 0;
        }
        .<?php echo $uid; ?> .olo-mm-hamburger.olo-mm-ham-open span:nth-child(3) {
            transform: translateY(-7px) rotate(-45deg);
        }
        /* Hamburger open: paint above overlay/offcanvas, stay in place */
        .<?php echo $uid; ?> .olo-mm-bar {
            position: relative;
            z-index: 10001;
        }

        /* === Off-Canvas Mobile === */
        .<?php echo $uid; ?> .olo-mm-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 9998;
            opacity: 0;
            pointer-events: none;
            transition: opacity .3s;
        }
        .<?php echo $uid; ?> .olo-mm-overlay.olo-mm-vis {
            opacity: 1;
            pointer-events: auto;
        }
        .<?php echo $uid; ?> .olo-mm-offcanvas {
            position: fixed;
            top: 0;
            <?php echo $mob_side; ?>: 0;
            bottom: 0;
            width: 320px;
            max-width: 85vw;
            background: <?php echo $mob_bg; ?>;
            z-index: 9999;
            transform: translateX(<?php echo $mob_side === 'left' ? '-100%' : '100%'; ?>);
            transition: transform .35s cubic-bezier(.4,0,.2,1);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            display: none;
            pointer-events: none;
        }
        .<?php echo $uid; ?> .olo-mm-offcanvas.olo-mm-vis {
            transform: translateX(0);
            pointer-events: auto;
        }
        /* Off-canvas header */
        .<?php echo $uid; ?> .olo-mm-oc-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .<?php echo $uid; ?> .olo-mm-oc-logo img {
            height: <?php echo $mob_logo_h; ?>px;
            width: auto;
            display: block;
        }
        .<?php echo $uid; ?> .olo-mm-oc-close {
            background: none;
            border: none;
            color: <?php echo $mob_tc; ?>;
            font-size: 28px;
            cursor: pointer;
            padding: 0;
            line-height: 1;
            opacity: .7;
            transition: opacity .2s;
        }
        .<?php echo $uid; ?> .olo-mm-oc-close:hover {
            opacity: 1;
        }
        /* Mobile nav list */
        .<?php echo $uid; ?> .olo-mm-mob-nav {
            list-style: none;
            margin: 0;
            padding: 12px 0;
        }
        .<?php echo $uid; ?> .olo-mm-mob-nav > li > a,
        .<?php echo $uid; ?> .olo-mm-mob-nav > li > .olo-mm-mob-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            color: <?php echo $mob_tc; ?>;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
            transition: background .15s;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }
        .<?php echo $uid; ?> .olo-mm-mob-nav > li > a:hover,
        .<?php echo $uid; ?> .olo-mm-mob-nav > li > .olo-mm-mob-toggle:hover {
            background: rgba(255,255,255,.05);
        }
        .<?php echo $uid; ?> .olo-mm-mob-chevron {
            width: 12px; height: 12px;
            opacity: .5;
            transition: transform .3s;
            flex-shrink: 0;
        }
        .<?php echo $uid; ?> .olo-mm-mob-open > .olo-mm-mob-toggle .olo-mm-mob-chevron {
            transform: rotate(180deg);
        }
        /* Sub-items */
        .<?php echo $uid; ?> .olo-mm-mob-sub {
            max-height: 0;
            overflow: hidden;
            transition: max-height .35s ease;
            background: rgba(0,0,0,.15);
        }
        .<?php echo $uid; ?> .olo-mm-mob-open > .olo-mm-mob-sub {
            max-height: 800px;
        }
        .<?php echo $uid; ?> .olo-mm-mob-sub .olo-mm-mob-heading {
            padding: 10px 20px 6px 32px;
            color: <?php echo $mob_hc; ?>;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-left: 2px solid <?php echo $mob_acc; ?>;
            margin: 4px 0 0;
        }
        .<?php echo $uid; ?> .olo-mm-mob-sub a {
            display: block;
            padding: 10px 20px 10px 36px;
            color: <?php echo $mob_tc; ?>;
            font-size: 14px;
            text-decoration: none;
            opacity: .85;
            transition: opacity .15s, padding-left .15s;
        }
        .<?php echo $uid; ?> .olo-mm-mob-sub a:hover {
            opacity: 1;
            padding-left: 40px;
        }

        /* === Responsive === */
        @media (max-width: <?php echo $bp; ?>px) {
            .<?php echo $uid; ?> {
                overflow: hidden;
            }
            .<?php echo $uid; ?> .olo-mm-nav {
                display: none !important;
            }
            .<?php echo $uid; ?> .olo-mm-hamburger {
                display: flex;
                margin-left: auto;
            }
            .<?php echo $uid; ?> .olo-mm-overlay,
            .<?php echo $uid; ?> .olo-mm-offcanvas {
                display: block;
            }
        }

        /* === Sticky === */
        <?php if ( ! empty( $s['sticky'] ) ) : ?>
        .olo-header-sticky .<?php echo $uid; ?> .olo-mm-bar {
            <?php if ( $sticky_bg ) : ?>background: <?php echo $sticky_bg; ?>;<?php endif; ?>
            <?php if ( $sticky_shad ) : ?>box-shadow: 0 2px 12px rgba(0,0,0,.12);<?php endif; ?>
        }
        <?php endif; ?>
        </style>
        <?php
    }

    /* ─── HTML ─── */

    private function render_html( $tree, $children, $grandchildren, $s, $uid ) {
        $current_url = trailingslashit( home_url( add_query_arg( [], false ) ) );
        $total       = count( $tree );
        $show_desc   = ! empty( $s['show_descriptions'] );
        ?>
        <div class="olo-megamenu <?php echo esc_attr( $uid ); ?>" data-uid="<?php echo esc_attr( $uid ); ?>">
            <div class="olo-mm-bar">
                <button class="olo-mm-hamburger" aria-label="Menu" aria-expanded="false">
                    <span></span><span></span><span></span>
                </button>
                <ul class="olo-mm-nav" role="menubar">
                    <?php foreach ( $tree as $idx => $item ) :
                        $subs       = $children[ $item->ID ] ?? [];
                        $is_current = trailingslashit( $item->url ) === $current_url;
                        $is_button  = $this->is_button_item( $item, $idx, $total, $s );
                        $is_mega    = ! $is_button && $this->is_mega_item( $item, $subs, $grandchildren, $s );
                        $has_panel_tpl = ! $is_button && $this->get_panel_template_id( $item->ID, $s ) > 0;
                        $has_subs   = ! $is_button && ( ! empty( $subs ) || $has_panel_tpl );

                        $li_classes = [];
                        if ( $is_current ) $li_classes[] = 'olo-mm-active';
                        $li_attr = ! empty( $li_classes ) ? ' class="' . esc_attr( implode( ' ', $li_classes ) ) . '"' : '';
                    ?>
                        <li<?php echo $li_attr; ?> role="none"<?php echo $has_subs ? ' aria-haspopup="true" aria-expanded="false"' : ''; ?>>
                            <?php if ( $is_button ) : ?>
                                <a href="<?php echo esc_url( $item->url ); ?>" class="olo-mm-btn"<?php echo $item->target ? ' target="' . esc_attr( $item->target ) . '"' : ''; ?>>
                                    <?php echo esc_html( $item->title ); ?>
                                </a>
                            <?php else : ?>
                                <a href="<?php echo esc_url( $item->url ); ?>"<?php echo $item->target ? ' target="' . esc_attr( $item->target ) . '"' : ''; ?>>
                                    <?php echo esc_html( $item->title ); ?>
                                    <?php if ( $has_subs ) : ?>
                                        <svg class="olo-mm-chevron" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                                    <?php endif; ?>
                                </a>
                                <?php
                                    $panel_tpl_id = $this->get_panel_template_id( $item->ID, $s );
                                    if ( $panel_tpl_id ) :
                                ?>
                                    <?php $this->render_template_panel( $panel_tpl_id ); ?>
                                <?php elseif ( $is_mega && ! empty( $subs ) ) : ?>
                                    <?php $this->render_mega_panel( $subs, $grandchildren, $s, $show_desc ); ?>
                                <?php elseif ( $has_subs ) : ?>
                                    <?php $this->render_simple_dropdown( $subs ); ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Off-Canvas Mobile -->
            <div class="olo-mm-overlay"></div>
            <nav class="olo-mm-offcanvas" aria-label="Mobile menu">
                <div class="olo-mm-oc-header">
                    <?php if ( ! empty( $s['mobile_logo'] ) ) : ?>
                        <div class="olo-mm-oc-logo"><img src="<?php echo esc_url( $s['mobile_logo'] ); ?>" alt="Logo" loading="lazy" /></div>
                    <?php else : ?>
                        <div></div>
                    <?php endif; ?>
                    <div></div>
                </div>
                <ul class="olo-mm-mob-nav">
                    <?php foreach ( $tree as $idx => $item ) :
                        $subs    = $children[ $item->ID ] ?? [];
                        $has_sub = ! empty( $subs );
                    ?>
                        <li<?php echo $has_sub ? ' class="olo-mm-mob-parent"' : ''; ?>>
                            <?php if ( $has_sub ) : ?>
                                <button class="olo-mm-mob-toggle" type="button">
                                    <?php echo esc_html( $item->title ); ?>
                                    <svg class="olo-mm-mob-chevron" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                                </button>
                                <div class="olo-mm-mob-sub">
                                    <?php // If item itself has a URL, show as first link ?>
                                    <?php if ( $item->url && $item->url !== '#' ) : ?>
                                        <a href="<?php echo esc_url( $item->url ); ?>"><?php echo esc_html( $item->title ); ?></a>
                                    <?php endif; ?>
                                    <?php foreach ( $subs as $sub ) :
                                        $gc = $grandchildren[ $sub->ID ] ?? [];
                                    ?>
                                        <?php if ( ! empty( $gc ) ) : ?>
                                            <div class="olo-mm-mob-heading"><?php echo esc_html( $sub->title ); ?></div>
                                            <?php foreach ( $gc as $gci ) : ?>
                                                <a href="<?php echo esc_url( $gci->url ); ?>"><?php echo esc_html( $gci->title ); ?></a>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <a href="<?php echo esc_url( $sub->url ); ?>"><?php echo esc_html( $sub->title ); ?></a>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php else : ?>
                                <a href="<?php echo esc_url( $item->url ); ?>"><?php echo esc_html( $item->title ); ?></a>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </div>
        <?php
    }

    private function render_mega_panel( $subs, $grandchildren, $s, $show_desc ) {
        ?>
        <div class="olo-mm-panel">
            <div class="olo-mm-grid">
                <?php foreach ( $subs as $sub ) :
                    $gc = $grandchildren[ $sub->ID ] ?? [];
                ?>
                    <div class="olo-mm-col">
                        <div class="olo-mm-heading">
                            <?php if ( $sub->url && $sub->url !== '#' ) : ?>
                                <a href="<?php echo esc_url( $sub->url ); ?>"><?php echo esc_html( $sub->title ); ?></a>
                            <?php else : ?>
                                <?php echo esc_html( $sub->title ); ?>
                            <?php endif; ?>
                        </div>
                        <?php if ( ! empty( $gc ) ) : ?>
                            <ul class="olo-mm-links">
                                <?php foreach ( $gc as $gci ) : ?>
                                    <li>
                                        <a href="<?php echo esc_url( $gci->url ); ?>" class="olo-mm-link"<?php echo $gci->target ? ' target="' . esc_attr( $gci->target ) . '"' : ''; ?>>
                                            <?php echo esc_html( $gci->title ); ?>
                                            <?php if ( $show_desc && ! empty( $gci->description ) ) : ?>
                                                <span class="olo-mm-desc"><?php echo esc_html( $gci->description ); ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    private function render_simple_dropdown( $subs ) {
        ?>
        <div class="olo-mm-dropdown">
            <?php foreach ( $subs as $sub ) : ?>
                <a href="<?php echo esc_url( $sub->url ); ?>"<?php echo $sub->target ? ' target="' . esc_attr( $sub->target ) . '"' : ''; ?>>
                    <?php echo esc_html( $sub->title ); ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php
    }

    /* ─── JavaScript ─── */

    private function render_js( $s, $uid ) {
        $mode       = esc_js( $s['header_mode'] ?? 'overlay' );
        $sticky     = ! empty( $s['sticky'] ) ? 'true' : 'false';
        $show_on_up = ! empty( $s['sticky_show_on_up'] ) ? 'true' : 'false';
        ?>
        <script>
        (function(){
            var uid = "<?php echo esc_js( $uid ); ?>";
            var root = document.querySelector("." + uid);
            if (!root) return;

            /* ── Hover Intent Desktop ── */
            var timers = {};
            var isFull = <?php echo $s['panel_width'] === 'full' ? 'true' : 'false'; ?>;
            var navItems = root.querySelectorAll(".olo-mm-nav > li");

            function posPanel(li) {
                var panel = li.querySelector(".olo-mm-panel-tpl") || (isFull ? li.querySelector(".olo-mm-panel") : null);
                if (panel) {
                    var rect = li.getBoundingClientRect();
                    panel.style.left = (-rect.left) + "px";
                    panel.style.width = document.documentElement.clientWidth + "px";
                    return;
                }
                /* Clamp non-full panels so they don't overflow right */
                var cPanel = li.querySelector(".olo-mm-panel, .olo-mm-dropdown");
                if (!cPanel) return;
                cPanel.style.left = "0";
                var pRect = cPanel.getBoundingClientRect();
                var vw = document.documentElement.clientWidth;
                if (pRect.right > vw - 8) {
                    var shift = pRect.right - vw + 8;
                    cPanel.style.left = (-shift) + "px";
                }
                if (pRect.left < 8) {
                    cPanel.style.left = (-pRect.left + 8) + "px";
                }
            }

            navItems.forEach(function(li) {
                var panel = li.querySelector(".olo-mm-panel, .olo-mm-dropdown");
                if (!panel) return;
                li.addEventListener("mouseenter", function() {
                    clearTimeout(timers[li.id] || timers._last);
                    // Close others first
                    navItems.forEach(function(o) {
                        if (o !== li) {
                            o.classList.remove("olo-mm-open");
                            o.setAttribute("aria-expanded", "false");
                        }
                    });
                    // Position full-width panels before showing
                    posPanel(li);
                    li.classList.add("olo-mm-open");
                    li.setAttribute("aria-expanded", "true");
                });
                li.addEventListener("mouseleave", function() {
                    timers._last = setTimeout(function() {
                        li.classList.remove("olo-mm-open");
                        li.setAttribute("aria-expanded", "false");
                    }, 200);
                });
            });

            /* ── Keyboard: Escape ── */
            document.addEventListener("keydown", function(e) {
                if (e.key === "Escape") {
                    navItems.forEach(function(li) {
                        li.classList.remove("olo-mm-open");
                        li.setAttribute("aria-expanded", "false");
                    });
                    closeMobile();
                }
            });

            /* ── Mobile Off-Canvas ── */
            var hamburger  = root.querySelector(".olo-mm-hamburger");
            var overlay    = root.querySelector(".olo-mm-overlay");
            var offcanvas  = root.querySelector(".olo-mm-offcanvas");

            function openMobile() {
                overlay.classList.add("olo-mm-vis");
                offcanvas.classList.add("olo-mm-vis");
                hamburger.classList.add("olo-mm-ham-open");
                hamburger.setAttribute("aria-expanded", "true");
                document.body.style.overflow = "hidden";
            }
            function closeMobile() {
                overlay.classList.remove("olo-mm-vis");
                offcanvas.classList.remove("olo-mm-vis");
                hamburger.classList.remove("olo-mm-ham-open");
                hamburger.setAttribute("aria-expanded", "false");
                document.body.style.overflow = "";
            }
            if (hamburger) hamburger.addEventListener("click", function() {
                offcanvas.classList.contains("olo-mm-vis") ? closeMobile() : openMobile();
            });
            if (overlay)  overlay.addEventListener("click", closeMobile);

            /* ── Accordion Mobile ── */
            root.querySelectorAll(".olo-mm-mob-toggle").forEach(function(btn) {
                btn.addEventListener("click", function() {
                    var li = btn.parentElement;
                    li.classList.toggle("olo-mm-mob-open");
                });
            });

            /* ── Header Mode + Sticky ── */
            var headerMode = "<?php echo $mode; ?>";
            var stickyEnabled = <?php echo $sticky; ?>;
            var showOnUp = <?php echo $show_on_up; ?>;
            var header = document.querySelector("header.olo-site-header");
            if (header) {
                header.classList.remove("olo-header-overlay", "olo-header-classic");
                header.classList.add("olo-header-" + headerMode);
                if (stickyEnabled) {
                    var lastY = 0, hidden = false, ticking = false;
                    var isClassic = (headerMode === "classic");
                    window.addEventListener("scroll", function() {
                        if (ticking) return;
                        ticking = true;
                        requestAnimationFrame(function() {
                            ticking = false;
                            var y = window.pageYOffset || document.documentElement.scrollTop;
                            if (y > 10) {
                                header.classList.add("olo-header-sticky");
                            } else {
                                header.classList.remove("olo-header-sticky");
                                if (hidden) { header.style.top = ""; header.style.transform = ""; hidden = false; }
                            }
                            if (showOnUp) {
                                var delta = y - lastY;
                                if (delta > 5) {
                                    if (y > 300) {
                                        if (!hidden) {
                                            if (isClassic) {
                                                header.style.top = (-header.offsetHeight - 10) + "px";
                                            } else {
                                                header.style.transform = "translateY(-100%)";
                                            }
                                            hidden = true;
                                        }
                                    }
                                } else if (delta < -5) {
                                    if (hidden) {
                                        header.style.top = "";
                                        header.style.transform = "";
                                        hidden = false;
                                    }
                                }
                            }
                            lastY = y;
                        });
                    }, { passive: true });
                }
            }
        })();
        </script>
        <?php
    }

    /* ─── Helpers ─── */

    private function get_panel_template_id( $item_id, $s ) {
        $map = $s['panel_templates'] ?? [];
        if ( ! is_array( $map ) || empty( $map ) ) return 0;
        return absint( $map[ (string) $item_id ] ?? 0 );
    }

    private function render_template_panel( $tpl_id ) {
        ?>
        <div class="olo-mm-panel olo-mm-panel-tpl">
            <?php echo do_shortcode( '[olo_template id="' . absint( $tpl_id ) . '"]' ); ?>
        </div>
        <?php
    }

    private function is_mega_item( $item, $subs, $grandchildren, $s ) {
        if ( empty( $subs ) ) return false;
        $mode = $s['mega_mode'] ?? 'auto';
        // Check if any sub has grandchildren (true mega)
        $has_gc = false;
        foreach ( $subs as $sub ) {
            if ( ! empty( $grandchildren[ $sub->ID ] ) ) {
                $has_gc = true;
                break;
            }
        }
        if ( ! $has_gc ) return false; // Only mega if L3 items exist

        if ( $mode === 'auto' ) return true;
        if ( $mode === 'css-class' ) {
            $classes = is_array( $item->classes ) ? $item->classes : [];
            return in_array( 'mega-menu', $classes, true );
        }
        return false;
    }

    private function is_button_item( $item, $idx, $total, $s ) {
        $mode = $s['button_mode'] ?? 'none';
        if ( $mode === 'none' ) return false;
        if ( $mode === 'last' )   return $idx === $total - 1;
        if ( $mode === 'last-2' ) return $idx >= $total - 2;
        if ( $mode === 'css-class' ) {
            $classes = is_array( $item->classes ) ? $item->classes : [];
            return in_array( 'olo-btn', $classes, true );
        }
        return false;
    }
}
