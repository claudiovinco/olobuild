<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_MegaMenu_Tile extends Olobuild_Tile_Base {

    protected $type     = 'megamenu';
    protected $name     = 'Mega Menu';
    protected $icon     = 'dashicons-menu-alt3';
    protected $category = 'header';
    protected $defaults = [
        // Menu
        'menu_id'            => 0,
        // Panel templates mapping { "menu_item_id": template_id }
        'panel_templates'    => [],
        // Logo
        'logo_image'         => '',
        'logo_text'          => '',
        'logo_dot'           => false,
        'logo_dot_color'     => '',
        'logo_dot_position'  => 'before',
        'logo_text_color'    => '',
        'logo_text_size'     => '19',
        'logo_crest'         => '',
        'logo_crest_bg'      => '',
        'logo_crest_color'   => '',
        'nav_phone'          => '',
        'nav_phone_url'      => '',
        'nav_phone_color'    => '',
        'logo_width'         => '140',
        'logo_min_height'    => '0',
        'logo_position'      => 'left',
        'logo_gap'           => '4',
        'logo_link'          => '',
        'logo_sticky'        => '',
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
        // Bar Spacing
        'bar_width'          => 'full',
        'bar_padding'        => [ 'top' => 16, 'right' => 16, 'bottom' => 16, 'left' => 16 ],
        'bar_gap'            => '20',
        'logo_margin_right'  => [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 ],
        // Hover Effects
        'hover_effect'       => 'none',
        'hover_effect_color' => '',
        'hover_effect_height'=> '2',
        'hover_effect_padding'=> [ 'top' => 8, 'right' => 8, 'bottom' => 8, 'left' => 8 ],
        // Mega Panel
        'mega_mode'          => 'auto',
        'panel_width'        => 'container',
        'panel_columns'      => '4',
        'panel_bg'           => '#FFFFFF',
        'panel_shadow'       => 'md',
        'panel_radius'       => [ 'tl' => 8, 'tr' => 8, 'br' => 8, 'bl' => 8 ],
        'panel_padding'      => [ 'top' => 32, 'right' => 32, 'bottom' => 32, 'left' => 32 ],
        'panel_border_top'   => '3',
        'panel_border_color' => '',
        'panel_animation'    => 'fade',
        'panel_max_width'    => '900',
        'panel_offset_top'   => '0',
        'panel_origin'       => 'nav',
        'panel_size'         => 'auto',
        'panel_open_animation' => 'fade',
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
        'btn_color'          => '',
        'btn_radius'         => [ 'tl' => 6, 'tr' => 6, 'br' => 6, 'bl' => 6 ],
        'btn_hover_bg'       => '',
        // NB: 'btn_padding' (unificato, 4 lati) NON va nei default: se assente il
        // render lo ricompone dai legacy btn_padding_v/h (template già salvati).
        'btn_padding_v'      => [ 'top' => 8, 'right' => 0, 'bottom' => 8, 'left' => 0 ],
        'btn_padding_h'      => [ 'top' => 0, 'right' => 20, 'bottom' => 0, 'left' => 20 ],
        'btn_margin_left'    => [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 ],
        'btn_margin_right'   => [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 ],
        'btn_border_width'   => '0',
        'btn_border_color'   => '',
        'btn_font_size'      => '0',
        'btn_font_weight'    => '600',
        'btn_transform'      => 'none',
        'btn_letter_spacing' => '0',
        'btn_hover_color'    => '',
        'btn_shadow'         => 'none',
        'btn_hover_effect'   => 'lift',
        // Search
        'search_icon'        => true,
        'search_position'    => 'navbar',
        'search_style'       => 'expand',
        'search_icon_style'  => 'lens',
        // Lingua — tile langswitcher referenziata (stesso pattern della search tile
        // del navmenu): la tile vive nel template, il megamenu la rende nella barra.
        'lang_tile_id'       => '',
        // Mobile
        'mobile_breakpoint'  => '1024',
        'mobile_style'       => 'offcanvas',
        'mobile_side'        => 'left',
        'mobile_slide_direction' => 'left',
        'offcanvas_fullscreen'   => false,
        'fullscreen_animation'   => 'fade',
        'menu_items_animation'   => 'none',
        'menu_items_stagger'     => '80',
        'hamburger_style'    => 'classic',
        'hamburger_size'     => '28',
        'hamburger_color'    => '',
        'mobile_bg'          => 'var(--olo-color-dark, #1e1e2e)',
        'mobile_text_color'  => '#FFFFFF',
        'mobile_heading_color' => '',
        'mobile_accent_color'  => '',
        'mob_separator_style' => 'line',
        'mob_toggle_style'   => 'chevron',
        'mob_toggle_position'=> 'right',
        'mob_toggle_size'    => '20',
        'mob_toggle_color'   => '',
        'mobile_font_size'   => '17',
        'mobile_link_font'   => 'inherit',
        'mobile_link_size'   => 0,
        'mobile_numbers'     => false,
        'mobile_footer_text' => '',
        'mobile_footer_cta_text' => '',
        'mobile_footer_cta_url'  => '',
        'mobile_item_padding'=> [ 'top' => 16, 'right' => 16, 'bottom' => 16, 'left' => 16 ],
        'mobile_logo'        => '',
        'mobile_logo_height' => '36',
        'mobile_bar_logo'    => true,
        'mobile_search'      => true,
        'mobile_search_overlay' => false,
        // Social Icons
        // Extra links
        'extra_link_1_label' => '',
        'extra_link_1_url'   => '',
        'extra_link_2_label' => '',
        'extra_link_2_url'   => '',
        'extra_link_3_label' => '',
        'extra_link_3_url'   => '',
        'extra_link_4_label' => '',
        'extra_link_4_url'   => '',
        'extra_link_1_blank' => false,
        'extra_link_2_blank' => false,
        'extra_link_3_blank' => false,
        'extra_link_4_blank' => false,
        'extra_link_1_button' => false,
        'extra_link_2_button' => false,
        // Voce = carrello WooCommerce: URL automatico + conteggio articoli "(n)" live
        'extra_link_1_cart'  => false,
        'extra_link_2_cart'  => false,
        'extra_link_3_cart'  => false,
        'extra_link_4_cart'  => false,
        'extra_links_right'  => false,
        // Timecode & progresso scroll ("sala di regia") — default off = barra invariata.
        'show_timecode'      => false,
        'timecode_duration'  => 90,
        'timecode_color'     => '',
        'scroll_progress'    => false,
        'progress_color'     => '',
        'progress_height'    => 2,
        // Social Icons
        // Predefiniti '#' SOLO sui principali (FB/IG/X/LinkedIn) come punto di partenza;
        // gli altri vuoti (nessuna icona finché non si inserisce l'URL).
        'social_facebook'    => '#',
        'social_instagram'   => '#',
        'social_x'           => '#',
        'social_linkedin'    => '#',
        'social_youtube'     => '',
        'social_tiktok'      => '',
        'social_pinterest'   => '',
        'social_whatsapp'    => '',
        'social_position'    => 'bar-right+mobile-bottom',
        'social_in_navbar'   => true,
        'social_navbar_side' => 'right',
        'social_in_topbar'   => false,
        'social_topbar_side' => 'right',
        'social_in_mobile'   => true,
        'social_mobile_pos'  => 'bottom',
        'social_size'        => '20',
        'social_color'       => '',
        'social_hover_color' => '',
        'social_style'       => 'plain',
        // Header
        'header_mode'        => 'overlay',
        'sticky'             => false,
        'sticky_show_on_up'  => false,
        'sticky_bg'          => '',
        'sticky_shadow'      => true,
        'sticky_shrink'      => false,
        'sticky_text_color'  => '',
        // Top Bar
        'topbar_enabled'       => false,
        // Token-first (v1.4.11): superficie scura "decisa" → dark; testo su scuro → light.
        // Hex storici come fallback → resa identica se il token non è definito.
        'topbar_bg'            => 'var(--olo-color-dark, #1F2937)',
        'topbar_text_color'    => 'var(--olo-color-light, #9CA3AF)',
        'topbar_link_color'    => '#FFFFFF',
        'topbar_height'        => '40',
        'topbar_font_size'     => '13',
        'topbar_hide_mobile'   => true,
        'topbar_hide_sticky'   => true,
        'topbar_left_content'  => 'none',
        'topbar_left_text'     => '',
        'topbar_left_menu_id'  => 0,
        'topbar_ticker_items'  => '',
        'topbar_ticker_label'  => 'TRENDING:',
        'topbar_ticker_speed'  => '5',
        'topbar_right_social'  => true,
        'topbar_right_search'  => true,
        'topbar_right_cart'    => false,
        'topbar_right_text'    => '',
        'topbar_right_cta_label' => '',
        'topbar_right_cta_url'  => '',
        'topbar_right_cta_bg'   => '',
        'topbar_right_cta_color'=> 'var(--olo-color-light, #FFFFFF)',
        'topbar_border_bottom' => true,
        'topbar_border_color'  => '',
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

        $menu_id = absint( $s['menu_id'] );
        if ( ! $menu_id ) {
            return '<div class="olo-megamenu"><p style="text-align:center;color:#999;padding:20px 0">' . esc_html( olobuild_t( 'Seleziona un menu nell\'Inspector.' ) ) . '</p></div>';
        }

        $items = wp_get_nav_menu_items( $menu_id );
        if ( ! $items || ! is_array( $items ) ) {
            return '<div class="olo-megamenu"><p style="text-align:center;color:#999;padding:20px 0">' . esc_html( olobuild_t( 'Menu vuoto o non trovato.' ) ) . '</p></div>';
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
        $this->render_scroll_fx_js( $s, $uid );
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from intval'd widths and structured border settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() helpers
        }
        return ob_get_clean();
    }

    /* ─── CSS ─── */

    /**
     * Normalizza un valore "spacing" in array {top,right,bottom,left}.
     * Accetta:
     *   - array { top, right, bottom, left }
     *   - scalar (es. '16') → uniform su tutti i lati
     *   - vuoto → default uniforme su tutti i lati
     */
    private function pad_obj( $value, $default = 0 ) {
        if ( is_array( $value ) ) {
            return [
                'top'    => intval( $value['top']    ?? $default ),
                'right'  => intval( $value['right']  ?? $default ),
                'bottom' => intval( $value['bottom'] ?? $default ),
                'left'   => intval( $value['left']   ?? $default ),
            ];
        }
        $v = ( $value === '' || $value === null ) ? $default : intval( $value );
        return [ 'top' => $v, 'right' => $v, 'bottom' => $v, 'left' => $v ];
    }

    /**
     * Restituisce un singolo intero da un setting "spacing" o scalare.
     * Strategia per shorthand legacy: prende il valore "top" (= scalar legacy se
     * uniformemente impostato).
     */
    private function pad_int( $value, $default = 0 ) {
        $obj = $this->pad_obj( $value, $default );
        return $obj['top'];
    }

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
        $p_radius    = Olobuild_Tile_Utils::radius_int( $s['panel_radius'] );
        $p_pad       = $this->pad_int( $s['panel_padding'] ?? null, 32 ) ?: 32;
        $p_bt        = intval( $s['panel_border_top'] );
        $p_bc        = $this->safe_color( $s['panel_border_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $p_anim      = $s['panel_animation'] === 'slide-down' ? 'slide' : 'fade';
        $p_shadow    = Olobuild_Tile_Utils::shadow( $s['panel_shadow'] ?? 'none', 'panel' );
        $dividers    = ! empty( $s['show_dividers'] );

        // Panel typography — neutri nudi → token tema
        $h_color     = $this->safe_color( $s['heading_color'] ) ?: 'var(--olo-color-text, #111827)';
        $h_size      = intval( $s['heading_size'] ) ?: 14;
        $h_weight    = intval( $s['heading_weight'] ) ?: 600;
        $h_tt        = esc_attr( $s['heading_transform'] ?: 'none' );
        $l_color     = $this->safe_color( $s['link_color'] ) ?: 'var(--olo-color-text, #4B5563)';
        $l_hcolor    = $this->safe_color( $s['link_hover_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $l_size      = intval( $s['link_size'] ) ?: 14;
        $l_spacing   = intval( $s['link_spacing'] ) ?: 8;
        $desc_color  = $this->safe_color( $s['desc_color'] ) ?: 'var(--olo-color-text-faint, #9CA3AF)';

        // Buttons
        $btn_bg      = $this->safe_color( $s['btn_bg'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $btn_color   = $this->safe_color( $s['btn_color'] ) ?: 'var(--olo-color-primary-contrast, #FFFFFF)';
        // 4 angoli reali (prima radius_int appiattiva al massimo) — coerenza col canvas.
        $btn_radius  = Olobuild_Tile_Utils::border_radius( $s['btn_radius'] ) ?: '0px';
        $btn_hbg     = $this->safe_color( $s['btn_hover_bg'] );
        // Padding interno: chiave unificata btn_padding (4 lati); fallback per i
        // template salvati con i legacy btn_padding_v/h — di cui pad_int leggeva
        // solo 'top', azzerando di fatto il padding orizzontale.
        if ( is_array( $s['btn_padding'] ?? null ) ) {
            $btn_pad = $this->pad_obj( $s['btn_padding'], 8 );
        } else {
            $pv      = $this->pad_obj( $s['btn_padding_v'] ?? null, 8 );
            $ph      = $this->pad_obj( $s['btn_padding_h'] ?? null, 20 );
            $btn_pad = [ 'top' => $pv['top'], 'right' => $ph['right'], 'bottom' => $pv['bottom'], 'left' => $ph['left'] ];
        }
        $btn_ml      = $this->pad_int( $s['btn_margin_left'] ?? null, 0 );
        $btn_mr      = $this->pad_int( $s['btn_margin_right'] ?? null, 0 );
        $btn_bw      = intval( $s['btn_border_width'] ?? 0 );
        $btn_bc      = $this->safe_color( $s['btn_border_color'] ?? '' );
        $btn_fs      = intval( $s['btn_font_size'] ?? 0 );
        $btn_fw      = esc_attr( $s['btn_font_weight'] ?? '600' ) ?: '600';
        $btn_tt      = esc_attr( $s['btn_transform'] ?? 'none' ) ?: 'none';
        $btn_lsp     = floatval( $s['btn_letter_spacing'] ?? 0 );
        $btn_hc      = $this->safe_color( $s['btn_hover_color'] ?? '' );
        $btn_sh      = Olobuild_Tile_Utils::shadow( $s['btn_shadow'] ?? 'none', 'button' );
        $btn_hfx     = $s['btn_hover_effect'] ?? 'lift';

        // Mobile
        $mob_side    = $s['mobile_side'] === 'right' ? 'right' : 'left';
        $ham_color   = $this->safe_color( $s['hamburger_color'] ) ?: $tc;
        $mob_bg      = $this->safe_color( $s['mobile_bg'] ) ?: 'var(--olo-color-dark, #1e1e2e)';
        $mob_tc      = $this->safe_color( $s['mobile_text_color'] ) ?: '#FFFFFF';
        $mob_hc      = $this->safe_color( $s['mobile_heading_color'] ) ?: 'rgba(255,255,255,.5)';
        $mob_acc     = $this->safe_color( $s['mobile_accent_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $mob_logo_h  = intval( $s['mobile_logo_height'] ) ?: 36;

        // Toggle sottomenu
        $tgl_style   = $s['mob_toggle_style'] ?? 'chevron';
        $tgl_pos     = $s['mob_toggle_position'] ?? 'right';
        $tgl_size    = intval( $s['mob_toggle_size'] ?? 20 );
        $tgl_color   = $this->safe_color( $s['mob_toggle_color'] ?? '' );
        $sep_style   = $s['mob_separator_style'] ?? 'line';

        // Bar spacing
        $bar_pad     = $this->pad_int( $s['bar_padding'] ?? null, 16 );
        $bar_gap_val = intval( $s['bar_gap'] ?? 20 );
        $logo_mr     = $this->pad_int( $s['logo_margin_right'] ?? null, 0 );

        // Panel extras
        $p_max_w     = intval( $s['panel_max_width'] ?? 900 );
        $p_offset    = intval( $s['panel_offset_top'] ?? 0 );
        $p_origin    = $s['panel_origin'] ?? 'nav';
        $p_size      = $s['panel_size'] ?? 'auto';
        $p_open_anim = $s['panel_open_animation'] ?? 'fade';

        // Fullscreen animation
        $fs_anim     = $s['fullscreen_animation'] ?? 'fade';

        // Menu items stagger
        $items_anim    = $s['menu_items_animation'] ?? 'none';
        $items_stagger = intval( $s['menu_items_stagger'] ?? 80 );

        // Offcanvas extras
        $mob_slide_dir   = $s['mobile_slide_direction'] ?? 'left';
        $oc_fullscreen   = ! empty( $s['offcanvas_fullscreen'] );
        // Fullscreen menu: tipografia link + numeri + footer (pixel-perfect overlay temi)
        $mob_link_font   = $s['mobile_link_font'] ?? 'inherit';
        // Valori legacy ('heading'/'body') → stack storici della tile; 'inherit' = non
        // settare font-family (ramo font-weight 600); valori nuovi (type 'font-family')
        // → CSS pronto via resolver condiviso.
        $mob_font_legacy = [ 'heading' => 'var(--olo-font-family-heading, Georgia, serif)', 'body' => 'var(--olo-font-family, -apple-system, sans-serif)' ];
        $mob_link_fam    = ( $mob_link_font === 'inherit' ) ? '' : $this->resolve_font_family( $mob_link_font, $mob_font_legacy );
        $mob_link_size   = intval( $s['mobile_link_size'] ?? 0 );
        $mob_numbers     = ! empty( $s['mobile_numbers'] );
        $mob_foot_text   = trim( (string) ( $s['mobile_footer_text'] ?? '' ) );
        $mob_foot_cta    = trim( (string) ( $s['mobile_footer_cta_text'] ?? '' ) );
        $mob_foot_url    = trim( (string) ( $s['mobile_footer_cta_url'] ?? '' ) );

        // Social
        $soc_size    = intval( $s['social_size'] ?? 20 );
        $soc_color   = $this->safe_color( $s['social_color'] ) ?: 'inherit';
        $soc_hcolor  = $this->safe_color( $s['social_hover_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $soc_style   = $s['social_style'] ?? 'plain';
        $soc_pos     = $s['social_position'] ?? 'menu-footer';
        // New toggle-based social positions
        $soc_in_navbar  = ! empty( $s['social_in_navbar'] );
        $soc_navbar_side = $s['social_navbar_side'] ?? 'right';
        $soc_in_topbar  = ! empty( $s['social_in_topbar'] );
        $soc_topbar_side = $s['social_topbar_side'] ?? 'right';
        $soc_in_mobile  = ! empty( $s['social_in_mobile'] );
        $soc_mobile_pos = $s['social_mobile_pos'] ?? 'bottom';

        // Sticky
        $sticky_bg   = $this->safe_color( $s['sticky_bg'] );
        $sticky_shad = ! empty( $s['sticky_shadow'] );

        // Animation transform
        $anim_from = $p_anim === 'slide' ? 'translateY(-12px)' : 'translateY(0)';
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: colors via safe_color()/safe_color_css(), integers via intval()/absint()/floatval()/pad_int()/radius_int(), enums via fixed-literal ternaries/if-else branches, shadows via Olobuild_Tile_Utils fixed maps, font stacks via resolve_font_family() whitelist; $uid is internally generated.
        ?>
        <style>
        /* === Force parent section to not clip megamenu panels === */
        section:has(.<?php echo $uid; ?>) {
            overflow: visible !important;
        }
        header.olo-site-header {
            overflow: visible !important;
        }
        /* === Navbar Container === */
        .<?php echo $uid; ?> {
            position: relative;
            z-index: 1000;
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
            <?php
            $bar_w = $s['bar_width'] ?? 'full';
            if ( $bar_w === 'wide' ) : ?>
            padding: 0 max(<?php echo $bar_pad; ?>px, calc(50% - 700px));
            <?php elseif ( $bar_w === 'classic' ) : ?>
            padding: 0 max(<?php echo $bar_pad; ?>px, calc(50% - 600px));
            <?php else : ?>
            padding: 0 <?php echo $bar_pad; ?>px;
            <?php endif; ?>
            gap: <?php echo $bar_gap_val; ?>px;
            width: 100%;
            box-sizing: border-box;
            <?php if ( $nav_bg ) : ?>background: <?php echo $nav_bg; ?>;<?php endif; ?>
            transition: background .3s, box-shadow .3s;
        }
        <?php if ( $logo_mr > 0 ) : ?>
        .<?php echo $uid; ?> .olo-mm-logo { margin-right: <?php echo $logo_mr; ?>px; }
        <?php endif; ?>
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
        .<?php echo $uid; ?> .olo-mm-nav.olo-mm-nav-utils { flex: 0 0 auto; }
        .<?php echo $uid; ?> .olo-mm-nav > li,
        .<?php echo $uid; ?> .olo-mm-nav-left > li,
        .<?php echo $uid; ?> .olo-mm-nav-right > li {
            position: relative;
        }
        .<?php echo $uid; ?> .olo-mm-nav > li > a,
        .<?php echo $uid; ?> .olo-mm-nav-left > li > a,
        .<?php echo $uid; ?> .olo-mm-nav-right > li > a {
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
        <?php
        $he       = $s['hover_effect'] ?? 'none';
        $he_color = $this->safe_color_css( $s['hover_effect_color'] ?? '' ) ?: ( $ac ?: 'var(--olo-color-primary, #e1474f)' );
        $he_h     = max( 1, intval( $s['hover_effect_height'] ?? 2 ) );
        if ( $he === 'none' || $he === '' ) : // Default underline (backward compat)
        ?>
        .<?php echo $uid; ?> .olo-mm-nav > li > a::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 2px;
            background: <?php echo $he_color; ?>;
            transform: scaleX(0);
            transition: transform .25s ease;
        }
        .<?php echo $uid; ?> .olo-mm-nav > li.olo-mm-active > a::after,
        .<?php echo $uid; ?> .olo-mm-nav > li > a:hover::after { transform: scaleX(1); }
        <?php elseif ( $he === 'underline' ) : ?>
        .<?php echo $uid; ?> .olo-mm-nav > li > a::after {
            content: ''; position: absolute; bottom: 0; left: 0; right: 0;
            height: <?php echo $he_h; ?>px; background: <?php echo $he_color; ?>;
            transform: scaleX(0); transition: transform .3s ease; transform-origin: center;
        }
        .<?php echo $uid; ?> .olo-mm-nav > li.olo-mm-active > a::after,
        .<?php echo $uid; ?> .olo-mm-nav > li > a:hover::after { transform: scaleX(1); }
        <?php elseif ( $he === 'overline' ) : ?>
        .<?php echo $uid; ?> .olo-mm-nav > li > a::after {
            content: ''; position: absolute; top: 0; left: 0; right: 0;
            height: <?php echo $he_h; ?>px; background: <?php echo $he_color; ?>;
            transform: scaleX(0); transition: transform .3s ease; transform-origin: center;
        }
        .<?php echo $uid; ?> .olo-mm-nav > li.olo-mm-active > a::after,
        .<?php echo $uid; ?> .olo-mm-nav > li > a:hover::after { transform: scaleX(1); }
        <?php elseif ( $he === 'double-line' ) : ?>
        .<?php echo $uid; ?> .olo-mm-nav > li > a::before,
        .<?php echo $uid; ?> .olo-mm-nav > li > a::after {
            content: ''; position: absolute; left: 0; right: 0;
            height: <?php echo $he_h; ?>px; background: <?php echo $he_color; ?>;
            transform: scaleX(0); transition: transform .3s ease;
        }
        .<?php echo $uid; ?> .olo-mm-nav > li > a::before { top: 0; }
        .<?php echo $uid; ?> .olo-mm-nav > li > a::after { bottom: 0; }
        .<?php echo $uid; ?> .olo-mm-nav > li.olo-mm-active > a::before,
        .<?php echo $uid; ?> .olo-mm-nav > li.olo-mm-active > a::after,
        .<?php echo $uid; ?> .olo-mm-nav > li > a:hover::before,
        .<?php echo $uid; ?> .olo-mm-nav > li > a:hover::after { transform: scaleX(1); }
        <?php elseif ( $he === 'background' ) : ?>
        .<?php echo $uid; ?> .olo-mm-nav > li > a {
            padding: 8px 14px; border-radius: 6px;
            transition: color .2s, background .2s;
        }
        .<?php echo $uid; ?> .olo-mm-nav > li > a::after { display: none; }
        .<?php echo $uid; ?> .olo-mm-nav > li.olo-mm-active > a,
        .<?php echo $uid; ?> .olo-mm-nav > li > a:hover {
            background: <?php echo $he_color; ?>; color: #fff;
        }
        <?php elseif ( $he === 'framed' ) : ?>
        .<?php echo $uid; ?> .olo-mm-nav > li > a {
            padding: 6px 14px;
            border: <?php echo $he_h; ?>px solid transparent;
            border-radius: 4px;
            transition: color .2s, border-color .3s;
        }
        .<?php echo $uid; ?> .olo-mm-nav > li > a::after { display: none; }
        .<?php echo $uid; ?> .olo-mm-nav > li.olo-mm-active > a,
        .<?php echo $uid; ?> .olo-mm-nav > li > a:hover {
            border-color: <?php echo $he_color; ?>;
        }
        <?php elseif ( $he === 'dot' ) : ?>
        .<?php echo $uid; ?> .olo-mm-nav > li > a::after {
            content: ''; position: absolute; bottom: -4px; left: 50%; transform: translateX(-50%) scale(0);
            width: 6px; height: 6px; border-radius: 50%; background: <?php echo $he_color; ?>;
            transition: transform .3s ease;
        }
        .<?php echo $uid; ?> .olo-mm-nav > li.olo-mm-active > a::after,
        .<?php echo $uid; ?> .olo-mm-nav > li > a:hover::after { transform: translateX(-50%) scale(1); }
        <?php elseif ( $he === 'bracket' ) : ?>
        .<?php echo $uid; ?> .olo-mm-nav > li > a { padding: 6px 12px; }
        .<?php echo $uid; ?> .olo-mm-nav > li > a::before,
        .<?php echo $uid; ?> .olo-mm-nav > li > a::after {
            content: ''; position: absolute; width: 8px; height: 100%; top: 0;
            border: <?php echo $he_h; ?>px solid <?php echo $he_color; ?>;
            transition: opacity .3s; opacity: 0;
        }
        .<?php echo $uid; ?> .olo-mm-nav > li > a::before { left: 0; border-right: none; }
        .<?php echo $uid; ?> .olo-mm-nav > li > a::after { right: 0; border-left: none; }
        .<?php echo $uid; ?> .olo-mm-nav > li.olo-mm-active > a::before,
        .<?php echo $uid; ?> .olo-mm-nav > li.olo-mm-active > a::after,
        .<?php echo $uid; ?> .olo-mm-nav > li > a:hover::before,
        .<?php echo $uid; ?> .olo-mm-nav > li > a:hover::after { opacity: 1; }
        <?php endif; ?>
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

        /* === Logo in Navbar === */
        <?php
        $logo_w   = intval( $s['logo_width'] ?? 140 ) ?: 140;
        $logo_pos = $s['logo_position'] ?? 'left';
        ?>
        .<?php echo $uid; ?> .olo-mm-logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            flex-shrink: 0;
            <?php if ( $logo_pos === 'left' ) : ?>margin-right: <?php echo $gap * 2; ?>px; order: -1;
            <?php elseif ( $logo_pos === 'right' ) : ?>margin-left: <?php echo $gap * 2; ?>px; order: 99;
            <?php elseif ( $logo_pos === 'center' ) : ?>position: absolute; left: 50%; transform: translateX(-50%);
            <?php elseif ( $logo_pos === 'stacked' ) : ?>order: -1;
            <?php elseif ( $logo_pos === 'split' ) : ?>order: 50; margin: 0 <?php echo $gap * 2; ?>px;
            <?php endif; ?>
        }
        .<?php echo $uid; ?> .olo-mm-logo--text { gap: 10px; }
        .<?php echo $uid; ?> .olo-mm-logo-text { font-family: var(--olo-font-family-heading, 'DM Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif); font-weight: 700; letter-spacing: -0.02em; line-height: 1; color: inherit; white-space: nowrap; }
        .<?php echo $uid; ?> .olo-mm-logo-dot { width: 9px; height: 9px; border-radius: 50%; background: currentColor; flex: none; }
        .<?php echo $uid; ?> .olo-mm-crest { display: inline-grid; place-items: center; width: 34px; height: 38px; font-family: var(--olo-font-family-heading, 'Archivo',sans-serif); font-weight: 900; font-size: 13px; letter-spacing: .02em; flex: none; border-radius: 14px 14px 16px 16px/14px 14px 22px 22px; box-shadow: inset 0 0 0 2px rgba(255,255,255,.2); }
        .<?php echo $uid; ?> .olo-mm-tel-li { display: flex; align-items: center; }
        .<?php echo $uid; ?> .olo-mm-tel { font-weight: 700; font-size: 14px; letter-spacing: .02em; white-space: nowrap; }
        .<?php echo $uid; ?> .olo-mm-tel-li::after { display: none !important; }
        <?php if ( ! empty( $s['show_timecode'] ) ) :
            // Timecode "sala di regia": mono 12px tabular-nums, visibile solo >=880px.
            $tc_col = $this->safe_color_css( $s['timecode_color'] ?? '' ) ?: 'var(--olo-color-primary, #C6F24E)';
        ?>
        .<?php echo $uid; ?> .olo-mm-tc-li { display: none; align-items: center; }
        .<?php echo $uid; ?> .olo-mm-tc-li::after { display: none !important; }
        .<?php echo $uid; ?> .olo-mm-tc {
            font-family: var(--olo-font-family-mono, 'Space Mono', ui-monospace, monospace);
            font-size: 12px;
            letter-spacing: .06em;
            text-transform: uppercase;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
            color: <?php echo $tc_col; ?>;
        }
        @media (min-width: 880px) {
            .<?php echo $uid; ?> .olo-mm-tc-li { display: flex; }
        }
        <?php endif; ?>
        <?php if ( ! empty( $s['scroll_progress'] ) ) :
            // Hairline di progresso scroll sul bordo inferiore della barra (var --olo-mm-p dal JS).
            $pg_col = $this->safe_color_css( $s['progress_color'] ?? '' ) ?: 'var(--olo-color-primary, #C6F24E)';
            $pg_raw = $s['progress_height'] ?? 2;
            $pg_h   = ( $pg_raw === '' || $pg_raw === null ) ? 2 : max( 1, min( 8, absint( $pg_raw ) ) );
        ?>
        .<?php echo $uid; ?> .olo-mm-bar { position: relative; }
        .<?php echo $uid; ?> .olo-mm-bar::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -1px;
            height: <?php echo $pg_h; ?>px;
            width: calc(var(--olo-mm-p, 0) * 100%);
            background: <?php echo $pg_col; ?>;
            pointer-events: none;
        }
        <?php endif; ?>
        <?php $logo_min_h = intval( $s['logo_min_height'] ?? 0 ); ?>
        .<?php echo $uid; ?> .olo-mm-logo img {
            <?php if ( $logo_min_h > 0 ) : ?>
            max-width: <?php echo $logo_w; ?>px;
            height: <?php echo $logo_min_h; ?>px;
            width: auto;
            <?php else : ?>
            width: <?php echo $logo_w; ?>px;
            height: auto;
            <?php endif; ?>
            display: block;
            transition: width .3s ease, height .3s ease;
            object-fit: contain;
        }
        <?php if ( $logo_pos === 'center' ) : ?>
        .<?php echo $uid; ?> .olo-mm-bar { position: relative; justify-content: space-between; }
        <?php endif; ?>
        <?php if ( $logo_pos === 'stacked' ) : ?>
        .<?php echo $uid; ?> .olo-mm-bar {
            flex-wrap: wrap;
        }
        <?php $logo_gap = intval( $s['logo_gap'] ?? 4 ); ?>
        .<?php echo $uid; ?> .olo-mm-logo {
            width: 100%;
            justify-content: center;
            padding: 12px 0 <?php echo $logo_gap; ?>px;
        }
        .<?php echo $uid; ?> .olo-mm-logo img {
            max-height: none;
        }
        .<?php echo $uid; ?> .olo-mm-nav {
            justify-content: center;
            width: 100%;
            padding-bottom: 8px;
        }
        <?php endif; ?>
        <?php if ( $logo_pos === 'split' ) : ?>
        .<?php echo $uid; ?> .olo-mm-nav:not(.olo-mm-nav-left):not(.olo-mm-nav-right) {
            display: none !important;
        }
        .<?php echo $uid; ?> .olo-mm-nav.olo-mm-nav-left,
        .<?php echo $uid; ?> .olo-mm-nav.olo-mm-nav-right {
            flex: none;
            width: auto;
        }
        .<?php echo $uid; ?> .olo-mm-bar {
            justify-content: center;
        }
        .<?php echo $uid; ?> .olo-mm-nav-left { order: 1; }
        .<?php echo $uid; ?> .olo-mm-logo { order: 2; }
        .<?php echo $uid; ?> .olo-mm-nav-right { order: 3; }
        <?php endif; ?>

        /* Logo sticky swap */
        <?php if ( ! empty( $s['logo_sticky'] ) ) : ?>
        .olo-header-sticky .<?php echo $uid; ?> .olo-mm-logo img.olo-mm-logo-default { display: none; }
        .olo-header-sticky .<?php echo $uid; ?> .olo-mm-logo img.olo-mm-logo-sticky { display: block !important; }
        <?php endif; ?>

        /* === Search Icon === */
        .<?php echo $uid; ?> .olo-mm-search-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 8px;
            background: none;
            border: none;
            color: <?php echo $tc; ?>;
            transition: color .2s;
            order: 90;
            -webkit-appearance: none;
        }
        .<?php echo $uid; ?> .olo-mm-search-icon:hover { color: <?php echo $hc ?: $ac ?: 'var(--olo-color-primary)'; ?>; }
        .<?php echo $uid; ?> .olo-mm-search-icon svg {
            width: 20px; height: 20px; fill: none; stroke: currentColor;
            stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
        }

        /* === Selettore lingua referenziato (lang_tile_id) === */
        .<?php echo $uid; ?> .olo-mm-lang { display: flex; align-items: center; order: 89; margin-left: 4px; }
        @media (max-width: <?php echo intval( $s['mobile_breakpoint'] ) ?: 1024; ?>px) {
            .<?php echo $uid; ?> .olo-mm-bar .olo-mm-lang { display: none; }
        }
        .<?php echo $uid; ?> .olo-mm-search-expand {
            max-width: 0; overflow: hidden; transition: max-width .3s ease, padding .3s ease;
            display: flex; align-items: center; order: 91;
        }
        .<?php echo $uid; ?> .olo-mm-search-expand.olo-mm-search-open {
            max-width: 300px; padding-left: 8px;
        }
        .<?php echo $uid; ?> .olo-mm-search-expand input {
            border: 1px solid #ddd; border-radius: 4px; padding: 6px 10px;
            font-size: 14px; width: 200px; outline: none;
        }
        .<?php echo $uid; ?> .olo-mm-search-expand input:focus { border-color: <?php echo $ac ?: 'var(--olo-color-primary)'; ?>; }
        <?php
        $search_pos = $s['search_position'] ?? 'navbar';
        if ( $search_pos === 'topbar' ) : ?>
        .<?php echo $uid; ?> .olo-mm-search-icon,
        .<?php echo $uid; ?> .olo-mm-search-expand { display: none !important; }
        <?php endif; ?>

        /* === E1: Ricerca overlay / command palette (search_style: overlay|command) === */
        .<?php echo $uid; ?> .olo-mm-search-overlay {
            position: fixed; inset: 0; z-index: 99999;
            display: flex; align-items: flex-start; justify-content: center;
            padding: 12vh 16px 16px;
        }
        .<?php echo $uid; ?> .olo-mm-search-overlay[hidden] { display: none; }
        .<?php echo $uid; ?> .olo-mm-search-overlay--cmd { align-items: center; padding-top: 16px; }
        .<?php echo $uid; ?> .olo-mm-search-overlay-backdrop {
            position: absolute; inset: 0;
            background: rgba(10,12,20,.55);
            backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);
            animation: olo-mm-so-fade .2s ease;
        }
        .<?php echo $uid; ?> .olo-mm-search-box {
            position: relative; width: 100%; max-width: 600px;
            background: <?php echo $p_bg; ?>; border-radius: 14px;
            box-shadow: 0 24px 70px rgba(0,0,0,.35); overflow: hidden;
            animation: olo-mm-so-pop .22s cubic-bezier(.2,.8,.2,1);
        }
        .<?php echo $uid; ?> .olo-mm-search-overlay--cmd .olo-mm-search-box { max-width: 560px; }
        .<?php echo $uid; ?> .olo-mm-search-form {
            display: flex; align-items: center; gap: 10px;
            padding: 16px 18px; border-bottom: 1px solid rgba(0,0,0,.08);
        }
        .<?php echo $uid; ?> .olo-mm-search-box-icon { display: flex; color: <?php echo $ac ?: 'var(--olo-color-primary, #e1474f)'; ?>; }
        .<?php echo $uid; ?> .olo-mm-search-box-icon svg { width: 22px; height: 22px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
        .<?php echo $uid; ?> .olo-mm-search-input {
            flex: 1; border: none; outline: none; background: none;
            font-size: 18px; color: var(--olo-color-text, #111827); min-width: 0;
        }
        .<?php echo $uid; ?> .olo-mm-search-kbd {
            font-size: 11px; font-weight: 600; color: var(--olo-color-text-faint, #9ca3af);
            border: 1px solid rgba(0,0,0,.15); border-radius: 5px; padding: 2px 7px; background: rgba(0,0,0,.03);
            flex-shrink: 0;
        }
        .<?php echo $uid; ?> .olo-mm-search-results { max-height: 46vh; overflow-y: auto; }
        .<?php echo $uid; ?> .olo-mm-search-result {
            display: block; padding: 12px 18px; text-decoration: none;
            color: var(--olo-color-text, #1f2937); font-size: 15px;
            border-bottom: 1px solid rgba(0,0,0,.05);
        }
        .<?php echo $uid; ?> .olo-mm-search-result:hover { background: rgba(0,0,0,.04); color: <?php echo $ac ?: 'var(--olo-color-primary, #e1474f)'; ?>; }
        .<?php echo $uid; ?> .olo-mm-search-empty { padding: 16px 18px; color: var(--olo-color-text-faint, #9ca3af); font-size: 14px; }
        /* Gli stili form globali (.olo-template input:focus) aggiungono il ring
           --olo-form-focus-shadow: l'input dell'overlay è "nudo" per design. */
        .<?php echo $uid; ?> .olo-mm-search-input:focus { border: none; box-shadow: none; outline: none; }
        @media (max-width: 640px) {
            .<?php echo $uid; ?> .olo-mm-search-overlay { padding: 9vh 24px 16px; }
            .<?php echo $uid; ?> .olo-mm-search-form { padding: 13px 16px; }
            .<?php echo $uid; ?> .olo-mm-search-input { font-size: 16px; }
            .<?php echo $uid; ?> .olo-mm-search-kbd { display: none; }
        }
        @keyframes olo-mm-so-fade { from { opacity: 0; } to { opacity: 1; } }
        @keyframes olo-mm-so-pop { from { opacity: 0; transform: translateY(-10px) scale(.98); } to { opacity: 1; transform: translateY(0) scale(1); } }

        /* === E2: Colonna promo/immagine nel mega panel (voce-colonna con classe `mega-promo`) === */
        .<?php echo $uid; ?> .olo-mm-col-promo { display: flex; }
        .<?php echo $uid; ?> .olo-mm-promo {
            display: flex; flex-direction: column; width: 100%;
            text-decoration: none; border-radius: 12px; overflow: hidden;
            background: rgba(0,0,0,.03); transition: transform .25s ease, box-shadow .25s ease;
        }
        .<?php echo $uid; ?> .olo-mm-promo:hover { transform: translateY(-3px); box-shadow: 0 14px 34px rgba(0,0,0,.16); }
        .<?php echo $uid; ?> .olo-mm-promo-media {
            display: block; width: 100%; aspect-ratio: 16 / 10; background-size: cover; background-position: center;
            background-image: linear-gradient(135deg, var(--olo-color-primary, #e1474f), var(--olo-color-dark, #1a1a2e));
        }
        .<?php echo $uid; ?> .olo-mm-promo-body { padding: 14px 16px; display: flex; flex-direction: column; gap: 4px; }
        .<?php echo $uid; ?> .olo-mm-promo-title { font-weight: 700; font-size: 15px; color: var(--olo-color-text, #111827); }
        .<?php echo $uid; ?> .olo-mm-promo-desc { font-size: 13px; color: var(--olo-color-text-faint, #6b7280); line-height: 1.4; }
        .<?php echo $uid; ?> .olo-mm-promo-cta { margin-top: 4px; font-size: 13px; font-weight: 600; color: <?php echo $ac ?: 'var(--olo-color-primary, #e1474f)'; ?>; }

        /* === E3: Badge per-voce (classe `badge-<label>`, es. badge-new → NEW) === */
        .<?php echo $uid; ?> .olo-mm-badge {
            display: inline-block; vertical-align: super;
            margin-left: 5px; padding: 1px 6px;
            font-size: 9px; font-weight: 700; letter-spacing: .04em; line-height: 1.5;
            text-transform: uppercase; border-radius: 999px; white-space: nowrap;
            background: var(--olo-color-primary, #e1474f); color: #fff;
        }
        .<?php echo $uid; ?> .olo-mm-btn .olo-mm-badge { background: rgba(255,255,255,.25); }

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
        <?php
        // Panel open animation initial state
        $pa_init_opacity  = '0';
        $pa_init_transform = '';
        $pa_init_clip = '';
        $pa_extra_transition = '';
        if ( $p_open_anim === 'fade' ) {
            // default opacity 0 → 1
        } elseif ( $p_open_anim === 'slide-down' ) {
            $pa_init_transform = 'translateY(-12px)';
        } elseif ( $p_open_anim === 'slide-up' ) {
            $pa_init_transform = 'translateY(12px)';
        } elseif ( $p_open_anim === 'scale' ) {
            $pa_init_transform = 'scaleY(0.85)';
        } elseif ( $p_open_anim === 'scale-center' ) {
            $pa_init_transform = 'scale(0.9)';
        } elseif ( $p_open_anim === 'flip' ) {
            $pa_init_transform = 'rotateX(-15deg)';
        } elseif ( $p_open_anim === 'reveal' ) {
            $pa_init_clip = 'inset(0 0 100% 0)';
            $pa_init_opacity = '1';
            $pa_extra_transition = ', clip-path .35s cubic-bezier(.4,0,.2,1)';
        } elseif ( $p_open_anim === 'blur' ) {
            $pa_extra_transition = ', filter .3s ease';
        }
        ?>
        .<?php echo $uid; ?> .olo-mm-panel {
            position: absolute;
            top: calc(100% + <?php echo $p_offset; ?>px);
            <?php if ( $p_origin === 'section' ) : ?>
            min-width: 0; max-width: <?php echo $p_max_w; ?>px;
            <?php else : ?>
            <?php if ( $p_size === 'centered' ) : ?>
            left: 50% !important; transform: translateX(-50%);
            min-width: 600px; max-width: <?php echo $p_max_w; ?>px;
            <?php elseif ( $p_size === 'section' ) : ?>
            left: 0; right: 0; min-width: 0; max-width: none;
            <?php elseif ( $p_size === 'viewport' ) : ?>
            min-width: 0; max-width: none;
            <?php elseif ( $p_size === 'container' ) : ?>
            min-width: 0; max-width: none;
            <?php else : /* auto */ ?>
            <?php if ( $s['panel_width'] !== 'full' ) : ?>
            left: 0; min-width: 600px; max-width: <?php echo $p_max_w; ?>px;
            <?php endif; ?>
            <?php endif; ?>
            <?php endif; ?>
            z-index: 99999;
            background: <?php echo $p_bg; ?>;
            border-radius: <?php echo $p_radius; ?>px;
            padding: <?php echo $p_pad; ?>px;
            <?php if ( $p_bt > 0 ) : ?>border-top: <?php echo $p_bt; ?>px solid <?php echo $p_bc; ?>;<?php endif; ?>
            <?php if ( $p_shadow !== 'none' ) : ?>box-shadow: <?php echo $p_shadow; ?>;<?php endif; ?>
            opacity: <?php echo $pa_init_opacity; ?>;
            <?php if ( $pa_init_transform ) : ?>transform: <?php echo $pa_init_transform; ?><?php echo ( $p_size === 'centered' && $p_origin !== 'section' ) ? ' translateX(-50%)' : ''; ?>;<?php endif; ?>
            <?php if ( $pa_init_clip ) : ?>clip-path: <?php echo $pa_init_clip; ?>;<?php endif; ?>
            <?php if ( $p_open_anim === 'blur' ) : ?>filter: blur(8px);<?php endif; ?>
            visibility: hidden;
            transition: opacity .3s ease, transform .3s ease, visibility .3s<?php echo $pa_extra_transition; ?>;
            pointer-events: none;
            <?php if ( in_array( $p_open_anim, ['scale', 'flip'] ) ) : ?>transform-origin: top center;<?php endif; ?>
            <?php if ( $p_open_anim === 'flip' ) : ?>perspective: 800px;<?php endif; ?>
        }
        <?php if ( $p_open_anim === 'flip' ) : ?>
        .<?php echo $uid; ?> .olo-mm-nav > li { perspective: 800px; }
        <?php endif; ?>
        .<?php echo $uid; ?> .olo-mm-nav > li.olo-mm-open > .olo-mm-panel {
            opacity: 1;
            <?php if ( $p_size === 'centered' && $p_origin !== 'section' ) : ?>
            transform: translateX(-50%);
            <?php else : ?>
            transform: none;
            <?php endif; ?>
            <?php if ( $pa_init_clip ) : ?>clip-path: inset(0 0 0 0);<?php endif; ?>
            <?php if ( $p_open_anim === 'blur' ) : ?>filter: blur(0);<?php endif; ?>
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
        /* a11y tastiera: anello di focus visibile su link/CTA del megamenu */
        .<?php echo $uid; ?> .olo-mm-link:focus-visible,
        .<?php echo $uid; ?> .olo-mm-btn:focus-visible,
        .<?php echo $uid; ?> .uk-navbar-nav > li > a:focus-visible {
            outline: none;
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
            border-radius: 3px;
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
            top: calc(100% + <?php echo $p_offset; ?>px);
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
            z-index: 99999;
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
            padding: <?php echo $btn_pad['top']; ?>px <?php echo $btn_pad['right']; ?>px <?php echo $btn_pad['bottom']; ?>px <?php echo $btn_pad['left']; ?>px !important;
            margin-left: <?php echo $btn_ml; ?>px;
            margin-right: <?php echo $btn_mr; ?>px;
            background: <?php echo $btn_bg; ?>;
            color: <?php echo $btn_color; ?> !important;
            border-radius: <?php echo $btn_radius; ?>;
            font-size: <?php echo $btn_fs ?: $fs; ?>px;
            font-weight: <?php echo $btn_fw; ?>;
            <?php if ( $btn_tt !== 'none' ) : ?>text-transform: <?php echo $btn_tt; ?>;<?php endif; ?>
            <?php if ( $btn_lsp ) : ?>letter-spacing: <?php echo $btn_lsp; ?>px;<?php endif; ?>
            <?php if ( $btn_sh !== 'none' ) : ?>box-shadow: <?php echo $btn_sh; ?>;<?php endif; ?>
            text-decoration: none;
            transition: background .2s, transform .15s, box-shadow .2s, color .2s;
            white-space: nowrap;
            <?php if ( $btn_bw > 0 ) : ?>border: <?php echo $btn_bw; ?>px solid <?php echo $btn_bc ?: $btn_color; ?>;<?php endif; ?>
        }
        .<?php echo $uid; ?> .olo-mm-btn:hover {
            <?php if ( $btn_hbg ) : ?>background: <?php echo $btn_hbg; ?>;<?php elseif ( $btn_hfx !== 'none' ) : ?>filter: brightness(1.1);<?php endif; ?>
            <?php if ( $btn_hc ) : ?>color: <?php echo $btn_hc; ?> !important;<?php endif; ?>
            <?php if ( $btn_hfx === 'lift' ) : ?>transform: translateY(-1px);<?php endif; ?>
            <?php if ( $btn_hfx === 'scale' ) : ?>transform: scale(1.04);<?php endif; ?>
            <?php if ( $btn_hfx === 'glow' ) : ?>box-shadow: 0 6px 20px rgba(15,23,42,.28);<?php endif; ?>
        }
        .<?php echo $uid; ?> .olo-mm-btn::after { display: none; }

        /* === Hamburger (SVG) === */
        <?php
        $ham_style = esc_attr( $s['hamburger_style'] ?? 'classic' );
        $ham_sz    = intval( $s['hamburger_size'] ?? 28 ) ?: 28;
        ?>
        .<?php echo $uid; ?> .olo-mm-hamburger {
            display: none;
            align-items: center;
            justify-content: center;
            width: <?php echo $ham_sz; ?>px;
            height: <?php echo $ham_sz; ?>px;
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
        .<?php echo $uid; ?> .olo-mm-ham-svg {
            width: 100%; height: 100%;
            color: <?php echo $ham_color; ?>;
        }
        /* SVG lines/paths transitions */
        .<?php echo $uid; ?> .olo-mm-ham-svg line,
        .<?php echo $uid; ?> .olo-mm-ham-svg path,
        .<?php echo $uid; ?> .olo-mm-ham-svg circle {
            transition: transform .35s ease, opacity .25s ease, cx .35s ease, cy .35s ease, r .35s ease;
            transform-origin: 12px 12px;
        }

        /* ── Classic: 3 linee → X ── */
        .<?php echo $uid; ?> .olo-mm-ham-classic.olo-mm-ham-open .olo-mm-ham-top { transform: translateY(6px) rotate(45deg); }
        .<?php echo $uid; ?> .olo-mm-ham-classic.olo-mm-ham-open .olo-mm-ham-mid { opacity: 0; }
        .<?php echo $uid; ?> .olo-mm-ham-classic.olo-mm-ham-open .olo-mm-ham-bot { transform: translateY(-6px) rotate(-45deg); }

        /* ── Squeeze: linee si restringono poi X ── */
        .<?php echo $uid; ?> .olo-mm-ham-squeeze .olo-mm-ham-svg line { transition: transform .35s ease, opacity .2s ease, x2 .25s ease; }
        .<?php echo $uid; ?> .olo-mm-ham-squeeze.olo-mm-ham-open .olo-mm-ham-top { transform: translateY(6px) rotate(45deg); }
        .<?php echo $uid; ?> .olo-mm-ham-squeeze.olo-mm-ham-open .olo-mm-ham-mid { opacity: 0; transform: scaleX(0); }
        .<?php echo $uid; ?> .olo-mm-ham-squeeze.olo-mm-ham-open .olo-mm-ham-bot { transform: translateY(-6px) rotate(-45deg); }

        /* ── Arrow: freccia indietro ← ── */
        .<?php echo $uid; ?> .olo-mm-ham-arrow.olo-mm-ham-open .olo-mm-ham-top { transform: rotate(-40deg) translate(-4px, 3px) scaleX(0.6); transform-origin: 3px 6px; }
        .<?php echo $uid; ?> .olo-mm-ham-arrow.olo-mm-ham-open .olo-mm-ham-mid { /* rimane */ }
        .<?php echo $uid; ?> .olo-mm-ham-arrow.olo-mm-ham-open .olo-mm-ham-bot { transform: rotate(40deg) translate(-4px, -3px) scaleX(0.6); transform-origin: 3px 18px; }

        /* ── Minimal: 2 linee asimmetriche → X ── */
        .<?php echo $uid; ?> .olo-mm-ham-minimal.olo-mm-ham-open .olo-mm-ham-top { transform: translate(0, 4px) rotate(45deg); transform-origin: 12px 8px; }
        .<?php echo $uid; ?> .olo-mm-ham-minimal.olo-mm-ham-open .olo-mm-ham-bot { transform: translate(0, -4px) rotate(-45deg); transform-origin: 12px 16px; }

        /* ── Dot Grid: 9 punti → X ── */
        .<?php echo $uid; ?> .olo-mm-ham-dot-grid .olo-mm-ham-svg circle { transition: opacity .3s ease, transform .4s ease, cx .4s ease, cy .4s ease; }
        .<?php echo $uid; ?> .olo-mm-ham-dot-grid.olo-mm-ham-open .olo-mm-ham-svg circle { opacity: 0; }
        .<?php echo $uid; ?> .olo-mm-ham-dot-grid.olo-mm-ham-open .olo-mm-ham-d1 { opacity: 1; transform: translate(7px, 7px); }
        .<?php echo $uid; ?> .olo-mm-ham-dot-grid.olo-mm-ham-open .olo-mm-ham-d9 { opacity: 1; transform: translate(-7px, -7px); }
        .<?php echo $uid; ?> .olo-mm-ham-dot-grid.olo-mm-ham-open .olo-mm-ham-d3 { opacity: 1; transform: translate(-7px, 7px); }
        .<?php echo $uid; ?> .olo-mm-ham-dot-grid.olo-mm-ham-open .olo-mm-ham-d7 { opacity: 1; transform: translate(7px, -7px); }

        /* ── Collapse: linea centrale scompare, le altre convergono → X ── */
        .<?php echo $uid; ?> .olo-mm-ham-collapse .olo-mm-ham-mid { transition: transform .2s ease, opacity .15s ease; }
        .<?php echo $uid; ?> .olo-mm-ham-collapse.olo-mm-ham-open .olo-mm-ham-top { transform: translateY(6px) rotate(45deg); }
        .<?php echo $uid; ?> .olo-mm-ham-collapse.olo-mm-ham-open .olo-mm-ham-mid { transform: scaleX(0); opacity: 0; }
        .<?php echo $uid; ?> .olo-mm-ham-collapse.olo-mm-ham-open .olo-mm-ham-bot { transform: translateY(-6px) rotate(-45deg); }

        /* ── Rotate: container ruota 180° poi X ── */
        .<?php echo $uid; ?> .olo-mm-ham-rotate .olo-mm-ham-svg { transition: transform .4s ease; }
        .<?php echo $uid; ?> .olo-mm-ham-rotate.olo-mm-ham-open .olo-mm-ham-svg { transform: rotate(180deg); }
        .<?php echo $uid; ?> .olo-mm-ham-rotate.olo-mm-ham-open .olo-mm-ham-top { transform: translateY(6px) rotate(45deg); }
        .<?php echo $uid; ?> .olo-mm-ham-rotate.olo-mm-ham-open .olo-mm-ham-mid { opacity: 0; }
        .<?php echo $uid; ?> .olo-mm-ham-rotate.olo-mm-ham-open .olo-mm-ham-bot { transform: translateY(-6px) rotate(-45deg); }

        /* ── Elastic: bounce easing ── */
        .<?php echo $uid; ?> .olo-mm-ham-elastic .olo-mm-ham-svg line { transition: transform .5s cubic-bezier(.68,-.55,.27,1.55), opacity .3s ease; }
        .<?php echo $uid; ?> .olo-mm-ham-elastic.olo-mm-ham-open .olo-mm-ham-top { transform: translateY(6px) rotate(45deg); }
        .<?php echo $uid; ?> .olo-mm-ham-elastic.olo-mm-ham-open .olo-mm-ham-mid { opacity: 0; transform: scaleX(0); }
        .<?php echo $uid; ?> .olo-mm-ham-elastic.olo-mm-ham-open .olo-mm-ham-bot { transform: translateY(-6px) rotate(-45deg); }

        /* ── Morph: paths si deformano fluidamente → X ── */
        .<?php echo $uid; ?> .olo-mm-ham-morph .olo-mm-ham-svg path { transition: d .4s cubic-bezier(.25,.1,.25,1), opacity .3s ease; }
        .<?php echo $uid; ?> .olo-mm-ham-morph.olo-mm-ham-open .olo-mm-ham-top { d: path("M4 4L20 20"); }
        .<?php echo $uid; ?> .olo-mm-ham-morph.olo-mm-ham-open .olo-mm-ham-mid { opacity: 0; }
        .<?php echo $uid; ?> .olo-mm-ham-morph.olo-mm-ham-open .olo-mm-ham-bot { d: path("M4 20L20 4"); }

        /* ── Magnetic: keyframe a 2 step (convergenza poi X) ── */
        @keyframes olo-mm-mag-top-<?php echo $uid; ?> {
            0% { transform: none; } 50% { transform: translateY(3px); } 100% { transform: translateY(6px) rotate(45deg); }
        }
        @keyframes olo-mm-mag-bot-<?php echo $uid; ?> {
            0% { transform: none; } 50% { transform: translateY(-3px); } 100% { transform: translateY(-6px) rotate(-45deg); }
        }
        .<?php echo $uid; ?> .olo-mm-ham-magnetic.olo-mm-ham-open .olo-mm-ham-top { animation: olo-mm-mag-top-<?php echo $uid; ?> .4s ease forwards; }
        .<?php echo $uid; ?> .olo-mm-ham-magnetic.olo-mm-ham-open .olo-mm-ham-mid { opacity: 0; transition: opacity .15s ease; }
        .<?php echo $uid; ?> .olo-mm-ham-magnetic.olo-mm-ham-open .olo-mm-ham-bot { animation: olo-mm-mag-bot-<?php echo $uid; ?> .4s ease forwards; }

        /* Hamburger open: paint above overlay/offcanvas, stay in place */
        .<?php echo $uid; ?> .olo-mm-bar {
            position: relative;
            z-index: 99999;
        }

        /* === Off-Canvas Mobile === */
        .<?php echo $uid; ?> .olo-mm-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 99998;
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
            z-index: 99999;
            transform: translateX(<?php echo $mob_side === 'left' ? '-100%' : '100%'; ?>);
            transition: transform .35s cubic-bezier(.4,0,.2,1);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            display: none;
            pointer-events: none;
            flex-direction: column;
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
            opacity: 1;
            transition: opacity .2s;
            z-index: 10;
            position: relative;
        }
        .<?php echo $uid; ?> .olo-mm-oc-close svg {
            display: block;
            stroke: <?php echo $mob_tc; ?>;
        }
        .<?php echo $uid; ?> .olo-mm-oc-close:hover {
            opacity: 1;
        }
        .<?php echo $uid; ?> .olo-mm-oc-actions {
            display: flex; align-items: center; gap: 12px;
        }
        .<?php echo $uid; ?> .olo-mm-oc-search-btn {
            background: none; border: none; cursor: pointer; padding: 4px;
            color: <?php echo $mob_tc; ?>; display: flex; align-items: center;
        }
        .<?php echo $uid; ?> .olo-mm-oc-search-btn svg {
            width: 22px; height: 22px; fill: none; stroke: <?php echo $mob_tc; ?>;
            stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
        }
        .<?php echo $uid; ?> .olo-mm-oc-search {
            display: none;
            padding: 12px 20px; border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .<?php echo $uid; ?> .olo-mm-oc-search.olo-mm-oc-search-vis {
            display: block;
        }
        .<?php echo $uid; ?> .olo-mm-oc-search form {
            display: flex; gap: 8px;
        }
        .<?php echo $uid; ?> .olo-mm-oc-search input {
            flex: 1; padding: 10px 14px; background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.15); border-radius: 6px;
            color: <?php echo $mob_tc; ?>; font-size: 15px; outline: none;
        }
        .<?php echo $uid; ?> .olo-mm-oc-search input:focus {
            border-color: <?php echo $mob_acc; ?>;
        }
        .<?php echo $uid; ?> .olo-mm-oc-search button {
            background: none; border: none; color: <?php echo $mob_tc; ?>; cursor: pointer; padding: 4px;
            display: flex; align-items: center;
        }
        .<?php echo $uid; ?> .olo-mm-oc-search button svg {
            width: 20px; height: 20px; fill: none; stroke: <?php echo $mob_tc; ?>;
            stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
        }
        /* Mobile nav list */
        .<?php echo $uid; ?> .olo-mm-mob-nav,
        .<?php echo $uid; ?> .olo-mm-mob-nav li {
            list-style: none !important;
            margin: 0;
        }
        .<?php echo $uid; ?> .olo-mm-mob-nav {
            padding: 12px 0;
        }
        <?php if ( $sep_style !== 'none' ) : ?>
        .<?php echo $uid; ?> .olo-mm-mob-nav > li {
            <?php if ( $sep_style === 'gradient' ) : ?>
            border-bottom: none;
            position: relative;
            <?php else : ?>
            border-bottom: 1px <?php echo $sep_style === 'line' ? 'solid' : esc_attr( $sep_style ); ?> rgba(255,255,255,.08);
            <?php endif; ?>
        }
        <?php if ( $sep_style === 'gradient' ) : ?>
        .<?php echo $uid; ?> .olo-mm-mob-nav > li::after {
            content: ''; display: block; height: 1px;
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,.12) 20%, rgba(255,255,255,.12) 80%, transparent 100%);
        }
        <?php endif; ?>
        <?php endif; ?>
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
        .<?php echo $uid; ?> .olo-mm-mob-cta-item {
            padding: 8px 20px;
        }
        .<?php echo $uid; ?> .olo-mm-mob-cta-link {
            display: block !important;
            text-align: center;
            padding: <?php echo $btn_pad['top']; ?>px <?php echo $btn_pad['right']; ?>px <?php echo $btn_pad['bottom']; ?>px <?php echo $btn_pad['left']; ?>px !important;
            background: <?php echo $btn_bg; ?> !important;
            color: <?php echo $btn_color; ?> !important;
            border-radius: <?php echo $btn_radius; ?>;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
        }
        .<?php echo $uid; ?> .olo-mm-mob-chevron {
            display: flex; align-items: center; flex-shrink: 0;
            <?php if ( $tgl_color ) : ?>color: <?php echo $tgl_color; ?>;<?php endif; ?>
            <?php if ( $tgl_pos === 'left' ) : ?>order: -1; margin-right: 8px;<?php endif; ?>
        }
        .<?php echo $uid; ?> .olo-mm-mob-chevron .olo-mm-toggle-icon {
            display: flex; transition: transform .3s ease;
        }
        .<?php echo $uid; ?> .olo-mm-mob-chevron .olo-mm-toggle-icon svg,
        .<?php echo $uid; ?> .olo-mm-mob-chevron .olo-mm-toggle-open svg,
        .<?php echo $uid; ?> .olo-mm-mob-chevron .olo-mm-toggle-close svg {
            display: block; width: 100%; height: 100%;
        }
        .<?php echo $uid; ?> .olo-mm-mob-chevron .olo-mm-toggle-open,
        .<?php echo $uid; ?> .olo-mm-mob-chevron .olo-mm-toggle-close { display: flex; }
        <?php if ( in_array( $tgl_style, [ 'chevron', 'arrow', 'caret', 'bracket' ] ) ) : ?>
        .<?php echo $uid; ?> .olo-mm-mob-open > .olo-mm-mob-toggle .olo-mm-mob-chevron .olo-mm-toggle-icon {
            transform: rotate(90deg);
        }
        <?php else : ?>
        .<?php echo $uid; ?> .olo-mm-mob-open > .olo-mm-mob-toggle .olo-mm-mob-chevron .olo-mm-toggle-open { display: none !important; }
        .<?php echo $uid; ?> .olo-mm-mob-open > .olo-mm-mob-toggle .olo-mm-mob-chevron .olo-mm-toggle-close { display: flex !important; }
        <?php endif; ?>
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
        <?php $mob_style = $s['mobile_style'] ?? 'offcanvas'; ?>
        @media (max-width: <?php echo $bp; ?>px) {
            .<?php echo $uid; ?> .olo-mm-nav,
            .<?php echo $uid; ?> .olo-mm-nav-left,
            .<?php echo $uid; ?> .olo-mm-nav-right {
                display: none !important;
            }
            .<?php echo $uid; ?> .olo-mm-search-icon,
            .<?php echo $uid; ?> .olo-mm-search-expand { display: none !important; }
            .<?php echo $uid; ?> .olo-mm-hamburger {
                display: flex !important;
                order: 999;
            }
            <?php if ( ! empty( $s['mobile_search'] ) ) : ?>
            .<?php echo $uid; ?> .olo-mm-mobile-search { display: flex !important; }
            <?php endif; ?>
            <?php if ( ( $s['button_mode'] ?? 'none' ) !== 'none' ) : ?>
            .<?php echo $uid; ?> .olo-mm-mobile-cta { display: flex !important; }
            <?php endif; ?>
            <?php if ( ! empty( $s['mobile_bar_logo'] ) ) : ?>
            .<?php echo $uid; ?> .olo-mm-mobile-logo { display: flex !important; }
            .<?php echo $uid; ?> .olo-mm-logo { display: none !important; }
            <?php endif; ?>
            <?php if ( $mob_style === 'offcanvas' ) : ?>
            .<?php echo $uid; ?> .olo-mm-overlay { display: block; }
            .<?php echo $uid; ?> .olo-mm-offcanvas { display: flex; }
            <?php elseif ( $mob_style === 'dropdown' ) : ?>
            .<?php echo $uid; ?> .olo-mm-dropdown-panel { display: block; }
            .<?php echo $uid; ?> .olo-mm-overlay,
            .<?php echo $uid; ?> .olo-mm-offcanvas { display: none !important; }
            <?php elseif ( $mob_style === 'fullscreen' ) : ?>
            .<?php echo $uid; ?> .olo-mm-fullscreen { display: block; }
            .<?php echo $uid; ?> .olo-mm-overlay,
            .<?php echo $uid; ?> .olo-mm-offcanvas { display: none !important; }
            <?php endif; ?>
        }

        /* === Mobile Dropdown Panel === */
        <?php
        $mob_fs  = intval( $s['mobile_font_size'] ?? 17 ) ?: 17;
        $mob_ip  = $this->pad_int( $s['mobile_item_padding'] ?? null, 16 ) ?: 16;
        $mob_sep = ! empty( $s['mobile_separator'] ?? true );
        $mob_drop_bg = $this->safe_color_css( $s['panel_bg'] ?? '' ) ?: '#ffffff';
        $mob_drop_tc = $this->safe_color_css( $s['mobile_text_color'] ?? '' ) ?: '#222';
        ?>
        .<?php echo $uid; ?> .olo-mm-dropdown-panel {
            display: none;
            position: absolute; top: 100%; left: 0; right: 0;
            background: <?php echo $mob_drop_bg; ?>;
            max-height: 0; overflow: hidden;
            transition: max-height .4s cubic-bezier(.4,0,.2,1);
            z-index: 99999; box-shadow: 0 4px 16px rgba(0,0,0,.1);
        }
        .<?php echo $uid; ?>.olo-mm-mob-active .olo-mm-dropdown-panel {
            display: block !important;
            max-height: calc(100vh - <?php echo $nav_h ?: 60; ?>px);
            overflow-y: auto;
        }
        .<?php echo $uid; ?> .olo-mm-dropdown-panel .olo-mm-dp-nav {
            list-style: none; margin: 0; padding: 0;
        }
        .<?php echo $uid; ?> .olo-mm-dropdown-panel .olo-mm-dp-nav > li {
            <?php if ( $sep_style !== 'none' ) : ?>
            <?php if ( $sep_style === 'gradient' ) : ?>
            position: relative;
            <?php else : ?>
            border-bottom: 1px <?php echo $sep_style === 'line' ? 'solid' : esc_attr( $sep_style ); ?> rgba(0,0,0,.08);
            <?php endif; ?>
            <?php endif; ?>
        }
        <?php if ( $sep_style === 'gradient' ) : ?>
        .<?php echo $uid; ?> .olo-mm-dropdown-panel .olo-mm-dp-nav > li::after {
            content: ''; display: block; height: 1px;
            background: linear-gradient(90deg, transparent 0%, rgba(0,0,0,.08) 20%, rgba(0,0,0,.08) 80%, transparent 100%);
        }
        <?php endif; ?>
        .<?php echo $uid; ?> .olo-mm-dp-item {
            display: flex; align-items: center;
        }
        .<?php echo $uid; ?> .olo-mm-dp-item a,
        .<?php echo $uid; ?> .olo-mm-dp-nav > li > a {
            display: block; flex: 1;
            padding: <?php echo $mob_ip; ?>px <?php echo $mob_ip + 8; ?>px;
            color: <?php echo $mob_drop_tc; ?>; text-decoration: none;
            font-size: <?php echo $mob_fs; ?>px; font-weight: 500; line-height: 1.4;
        }
        .<?php echo $uid; ?> .olo-mm-dp-nav li.olo-mm-dp-active > a,
        .<?php echo $uid; ?> .olo-mm-dp-nav li.olo-mm-dp-active > .olo-mm-dp-item > a {
            color: <?php echo $mob_acc; ?>;
        }
        .<?php echo $uid; ?> .olo-mm-dp-chevron {
            background: none; border: none; cursor: pointer;
            padding: <?php echo $mob_ip; ?>px;
            color: <?php echo $tgl_color ?: '#999'; ?>;
            display: flex; align-items: center; flex-shrink: 0;
            -webkit-appearance: none; outline: none;
            <?php if ( $tgl_pos === 'left' ) : ?>order: -1;<?php endif; ?>
        }
        .<?php echo $uid; ?> .olo-mm-dp-chevron:focus { outline: none !important; box-shadow: none !important; }
        .<?php echo $uid; ?> .olo-mm-dp-chevron .olo-mm-toggle-icon {
            display: flex; transition: transform .3s ease;
        }
        .<?php echo $uid; ?> .olo-mm-dp-chevron .olo-mm-toggle-icon svg,
        .<?php echo $uid; ?> .olo-mm-dp-chevron .olo-mm-toggle-open svg,
        .<?php echo $uid; ?> .olo-mm-dp-chevron .olo-mm-toggle-close svg {
            display: block;
        }
        .<?php echo $uid; ?> .olo-mm-dp-chevron .olo-mm-toggle-open,
        .<?php echo $uid; ?> .olo-mm-dp-chevron .olo-mm-toggle-close { display: flex; }
        <?php if ( in_array( $tgl_style, [ 'chevron', 'arrow', 'caret', 'bracket' ] ) ) : ?>
        .<?php echo $uid; ?> li.olo-mm-dp-sub-open > .olo-mm-dp-item > .olo-mm-dp-chevron .olo-mm-toggle-icon {
            transform: rotate(90deg);
        }
        <?php else : ?>
        .<?php echo $uid; ?> li.olo-mm-dp-sub-open > .olo-mm-dp-item > .olo-mm-dp-chevron .olo-mm-toggle-open { display: none !important; }
        .<?php echo $uid; ?> li.olo-mm-dp-sub-open > .olo-mm-dp-item > .olo-mm-dp-chevron .olo-mm-toggle-close { display: flex !important; }
        <?php endif; ?>
        .<?php echo $uid; ?> .olo-mm-dp-sub {
            max-height: 0; overflow: hidden;
            transition: max-height .35s cubic-bezier(.4,0,.2,1);
            background: rgba(0,0,0,.02);
            list-style: none !important; padding-left: 0;
        }
        .<?php echo $uid; ?> li.olo-mm-dp-sub-open > .olo-mm-dp-sub { max-height: 500px; }
        .<?php echo $uid; ?> .olo-mm-dp-sub li a {
            padding: <?php echo max(8,$mob_ip-4); ?>px <?php echo $mob_ip+8; ?>px <?php echo max(8,$mob_ip-4); ?>px <?php echo $mob_ip+28; ?>px;
            color: <?php echo $mob_drop_tc; ?>; text-decoration: none;
            font-size: <?php echo max(14,$mob_fs-2); ?>px; font-weight: 400; opacity: .85; display: block;
        }
        .<?php echo $uid; ?> .olo-mm-dp-sub li a:hover { opacity: 1; color: <?php echo $mob_acc; ?>; }

        /* === Fullscreen Mobile === */
        .<?php echo $uid; ?> .olo-mm-fullscreen {
            display: none;
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: <?php echo $mob_bg; ?>;
            z-index: 99999;
            opacity: 0; visibility: hidden;
            transition: opacity .4s ease, visibility .4s ease;
            overflow-y: auto;
            padding: 0;
        }
        .<?php echo $uid; ?>.olo-mm-mob-active .olo-mm-fullscreen {
            display: block !important; opacity: 1; visibility: visible;
        }
        /* Fullscreen header: logo + close */
        .<?php echo $uid; ?> .olo-mm-fs-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 24px; border-bottom: 1px solid rgba(255,255,255,.1);
        }
        .<?php echo $uid; ?> .olo-mm-fs-logo img {
            max-height: 40px; width: auto;
        }
        .<?php echo $uid; ?> .olo-mm-fs-close {
            background: none; border: none; color: <?php echo $mob_tc; ?>; cursor: pointer; padding: 4px;
        }
        /* Fullscreen search */
        .<?php echo $uid; ?> .olo-mm-fs-search {
            padding: 16px 24px;
        }
        .<?php echo $uid; ?> .olo-mm-fs-search form {
            display: flex; background: rgba(255,255,255,.1); border-radius: 8px; overflow: hidden;
        }
        .<?php echo $uid; ?> .olo-mm-fs-search input {
            flex: 1; padding: 12px 16px; background: transparent; border: none; color: <?php echo $mob_tc; ?>; font-size: 16px; outline: none;
        }
        .<?php echo $uid; ?> .olo-mm-fs-search input::placeholder { color: rgba(255,255,255,.4); }
        .<?php echo $uid; ?> .olo-mm-fs-search button {
            background: none; border: none; padding: 12px 16px; cursor: pointer;
        }
        .<?php echo $uid; ?> .olo-mm-fs-search svg {
            width: 20px; height: 20px; stroke: <?php echo $mob_tc; ?>; fill: none; stroke-width: 2;
        }
        .<?php echo $uid; ?> .olo-mm-fullscreen .olo-mm-fs-nav {
            list-style: none; margin: 0; padding: 0 24px; counter-reset: olommfs;
        }
        .<?php echo $uid; ?> .olo-mm-fs-nav > li > a,
        .<?php echo $uid; ?> .olo-mm-fs-nav > li > .olo-mm-dp-item > a {
            display: block; padding: 14px 0;
            color: <?php echo $mob_tc; ?>; text-decoration: none;
            font-size: <?php echo $mob_link_size > 0 ? $mob_link_size : 22; ?>px;
            <?php if ( $mob_link_fam !== '' ) : ?>font-family: <?php echo $mob_link_fam; ?>; font-weight: 400; letter-spacing: .02em;<?php else : ?>font-weight: 600;<?php endif; ?>
            border-bottom: 1px solid rgba(255,255,255,.1);
        }
        .<?php echo $uid; ?> .olo-mm-numbered .olo-mm-fs-nav > li { counter-increment: olommfs; }
        .<?php echo $uid; ?> .olo-mm-numbered .olo-mm-fs-nav > li > a::before,
        .<?php echo $uid; ?> .olo-mm-numbered .olo-mm-fs-nav > li > .olo-mm-dp-item > a::before {
            content: counter(olommfs, decimal-leading-zero);
            color: <?php echo $mob_acc; ?>; font-family: var(--olo-font-family, sans-serif);
            font-size: 12px; font-weight: 600; letter-spacing: .1em; margin-right: 16px; vertical-align: middle;
        }
        .<?php echo $uid; ?> .olo-mm-fs-foot {
            margin-top: auto; padding: 24px; border-top: 1px solid rgba(255,255,255,.1);
            display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;
        }
        .<?php echo $uid; ?> .olo-mm-fs-foot span { color: <?php echo $mob_hc; ?>; font-size: 12px; letter-spacing: .04em; }
        .<?php echo $uid; ?> .olo-mm-fs-foot a { color: <?php echo $mob_acc; ?>; font-weight: 600; font-size: 13px; text-decoration: none; }
        .<?php echo $uid; ?>.olo-mm-mob-active .olo-mm-fullscreen { display: flex !important; flex-direction: column; }
        .<?php echo $uid; ?> .olo-mm-fullscreen .olo-mm-dp-chevron { color: rgba(255,255,255,.5); }
        .<?php echo $uid; ?> .olo-mm-fullscreen .olo-mm-dp-sub {
            background: rgba(255,255,255,.05);
        }
        .<?php echo $uid; ?> .olo-mm-fullscreen .olo-mm-dp-sub li a {
            color: <?php echo $mob_tc; ?>; opacity: .7;
            padding-left: 20px; font-size: 18px;
        }

        /* === Mobile bar extras (logo + search) === */
        .<?php echo $uid; ?> .olo-mm-mobile-logo {
            display: none; align-items: center; order: -1; margin-right: auto;
        }
        .<?php echo $uid; ?> .olo-mm-mobile-logo img {
            /* height esplicita, non solo max-height: gli SVG senza width/height
               intrinseci (solo viewBox) collasserebbero a 0 con il solo max-height. */
            height: <?php echo $mob_logo_h; ?>px; max-height: <?php echo $mob_logo_h; ?>px; width: auto; display: block;
        }
        .<?php echo $uid; ?> .olo-mm-mobile-search {
            display: none; align-items: center; padding: 8px; cursor: pointer;
            background: none; border: none; -webkit-appearance: none; order: 998;
        }
        .<?php echo $uid; ?> .olo-mm-mobile-search svg {
            width: 22px; height: 22px; fill: none; stroke: <?php echo $ham_color; ?>;
            stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
        }
        .<?php echo $uid; ?> .olo-mm-mobile-cta {
            display: none; align-items: center; gap: 8px; order: 85;
        }
        .<?php echo $uid; ?> .olo-mm-mobile-cta .olo-mm-mob-btn {
            font-size: 13px;
            /* !important: deve vincere sul padding !important della base .olo-mm-btn */
            padding: <?php echo max( 4, $btn_pad['top'] - 2 ); ?>px <?php echo max( 8, $btn_pad['right'] - 4 ); ?>px <?php echo max( 4, $btn_pad['bottom'] - 2 ); ?>px <?php echo max( 8, $btn_pad['left'] - 4 ); ?>px !important;
        }
        .<?php echo $uid; ?> .olo-mm-mob-search-panel {
            display: none; padding: 10px <?php echo $this->pad_int($s['bar_padding'] ?? null, 12); ?>px;
            background: <?php echo $mob_drop_bg; ?>;
        }
        .<?php echo $uid; ?>.olo-mm-search-active .olo-mm-mob-search-panel { display: flex; }
        .<?php echo $uid; ?> .olo-mm-mob-search-panel form {
            display: flex; gap: 8px; width: 100%;
        }
        .<?php echo $uid; ?> .olo-mm-mob-search-panel input {
            flex: 1; padding: 10px 14px; border: 1px solid #ddd;
            border-radius: 6px; font-size: 15px; outline: none;
        }
        .<?php echo $uid; ?> .olo-mm-mob-search-panel button {
            background: <?php echo $nav_bg ?: $mob_bg; ?>;
            border: none; border-radius: 6px; padding: 0 14px; cursor: pointer;
        }
        .<?php echo $uid; ?> .olo-mm-mob-search-panel button svg {
            width: 18px; height: 18px; stroke: #fff; fill: none; stroke-width: 2;
        }

        /* === Sticky === */
        <?php if ( ! empty( $s['sticky'] ) ) : ?>
        .olo-header-sticky .<?php echo $uid; ?> .olo-mm-bar {
            <?php if ( $sticky_bg ) : ?>background: <?php echo $sticky_bg; ?>;<?php endif; ?>
            <?php $stc = $this->safe_color_css( $s['sticky_text_color'] ?? '' ); if ( $stc ) : ?>color: <?php echo $stc; ?>;<?php endif; ?>
        }
        <?php if ( $stc ) : ?>
        .olo-header-sticky .<?php echo $uid; ?> .olo-mm-nav > li > a { color: <?php echo $stc; ?>; }
        <?php endif; ?>
        <?php if ( ! empty( $s['sticky_shrink'] ) ) : ?>
        .olo-header-sticky .<?php echo $uid; ?> .olo-mm-bar {
            min-height: <?php echo max( 40, ( $nav_h ?: 60 ) - 16 ); ?>px;
            transition: min-height .3s ease, background .3s ease;
        }
        .olo-header-sticky .<?php echo $uid; ?> .olo-mm-logo img {
            max-width: <?php echo max( 80, $logo_w - 30 ); ?>px;
        }
        <?php endif; ?>
        <?php endif; ?>

        <?php /* Hamburger styles are now SVG-based, defined above */ ?>

        <?php
        // ── HOVER EFFECTS (nuovi 6) ──
        $he = $s['hover_effect'] ?? 'none';
        $he_color = $this->safe_color( $s['hover_effect_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $he_h = intval( $s['hover_effect_height'] ?? 2 );
        $he_pad = $this->pad_int( $s['hover_effect_padding'] ?? null, 8 );
        ?>
        <?php if ( $he === 'highlight' ) : ?>
        .<?php echo $uid; ?> .olo-mm-nav > li > a::after {
            content: ''; position: absolute; left: -<?php echo $he_pad; ?>px; bottom: 0; width: calc(100% + <?php echo $he_pad * 2; ?>px); height: 100%;
            background: <?php echo $he_color; ?>; opacity: .12; border-radius: 4px;
            transform: scaleX(0); transform-origin: left; transition: transform .3s ease;
            z-index: -1;
        }
        .<?php echo $uid; ?> .olo-mm-nav > li > a:hover::after,
        .<?php echo $uid; ?> .olo-mm-nav > li.olo-mm-active > a::after { transform: scaleX(1); }
        <?php elseif ( $he === 'fill-up' ) : ?>
        .<?php echo $uid; ?> .olo-mm-nav > li > a::after {
            content: ''; position: absolute; left: -<?php echo $he_pad; ?>px; bottom: 0; width: calc(100% + <?php echo $he_pad * 2; ?>px); height: 0;
            background: <?php echo $he_color; ?>; opacity: .15; border-radius: 4px;
            transition: height .3s ease; z-index: -1;
        }
        .<?php echo $uid; ?> .olo-mm-nav > li > a:hover::after,
        .<?php echo $uid; ?> .olo-mm-nav > li.olo-mm-active > a::after { height: 100%; }
        <?php elseif ( $he === 'flip' ) : ?>
        .<?php echo $uid; ?> .olo-mm-nav > li > a { perspective: 600px; }
        .<?php echo $uid; ?> .olo-mm-nav > li > a:hover { transform: rotateX(360deg); transition: transform .5s ease; }
        <?php elseif ( $he === 'glitch' ) : ?>
        .<?php echo $uid; ?> .olo-mm-nav > li > a { position: relative; }
        .<?php echo $uid; ?> .olo-mm-nav > li > a::before,
        .<?php echo $uid; ?> .olo-mm-nav > li > a::after {
            content: attr(data-text); position: absolute; left: 0; top: 0;
            width: 100%; height: 100%; overflow: hidden;
            opacity: 0; transition: opacity .2s;
        }
        .<?php echo $uid; ?> .olo-mm-nav > li > a::before { color: #ff0000; transform: translate(-2px, -1px); }
        .<?php echo $uid; ?> .olo-mm-nav > li > a::after { color: #0000ff; transform: translate(2px, 1px); }
        .<?php echo $uid; ?> .olo-mm-nav > li > a:hover::before,
        .<?php echo $uid; ?> .olo-mm-nav > li > a:hover::after { opacity: .7; }
        <?php elseif ( $he === 'magnetic' ) : ?>
        .<?php echo $uid; ?> .olo-mm-nav > li > a {
            transition: transform .2s ease;
            transform: translate(var(--mm-mx, 0), var(--mm-my, 0));
        }
        <?php elseif ( $he === 'underline-grow' ) : ?>
        .<?php echo $uid; ?> .olo-mm-nav > li > a::after {
            content: ''; position: absolute; bottom: 0; left: 0; width: 100%;
            height: 1px; background: <?php echo $he_color; ?>;
            transition: height .25s ease, background .25s ease;
        }
        .<?php echo $uid; ?> .olo-mm-nav > li > a:hover::after,
        .<?php echo $uid; ?> .olo-mm-nav > li.olo-mm-active > a::after {
            height: <?php echo max(3, $he_h + 1); ?>px;
        }
        <?php endif; ?>

        <?php
        // ── FULLSCREEN ANIMATIONS ──
        if ( $fs_anim === 'slide-left' ) : ?>
        .<?php echo $uid; ?> .olo-mm-fullscreen { transform: translateX(-100%); opacity: 1; visibility: hidden; transition: transform .5s cubic-bezier(.4,0,.2,1), visibility .5s; }
        .<?php echo $uid; ?>.olo-mm-mob-active .olo-mm-fullscreen { transform: translateX(0); visibility: visible; }
        <?php elseif ( $fs_anim === 'slide-right' ) : ?>
        .<?php echo $uid; ?> .olo-mm-fullscreen { transform: translateX(100%); opacity: 1; visibility: hidden; transition: transform .5s cubic-bezier(.4,0,.2,1), visibility .5s; }
        .<?php echo $uid; ?>.olo-mm-mob-active .olo-mm-fullscreen { transform: translateX(0); visibility: visible; }
        <?php elseif ( $fs_anim === 'slide-up' ) : ?>
        .<?php echo $uid; ?> .olo-mm-fullscreen { transform: translateY(100%); opacity: 1; visibility: hidden; transition: transform .5s cubic-bezier(.4,0,.2,1), visibility .5s; }
        .<?php echo $uid; ?>.olo-mm-mob-active .olo-mm-fullscreen { transform: translateY(0); visibility: visible; }
        <?php elseif ( $fs_anim === 'curtain' ) : ?>
        .<?php echo $uid; ?> .olo-mm-fullscreen { opacity: 1; visibility: hidden; }
        .<?php echo $uid; ?> .olo-mm-fullscreen::before,
        .<?php echo $uid; ?> .olo-mm-fullscreen::after {
            content: ''; position: fixed; left: 0; width: 100%; height: 50%;
            background: <?php echo $mob_bg; ?>; z-index: -1;
            transform: scaleY(0); transition: transform .5s cubic-bezier(.4,0,.2,1);
        }
        .<?php echo $uid; ?> .olo-mm-fullscreen::before { top: 0; transform-origin: top; }
        .<?php echo $uid; ?> .olo-mm-fullscreen::after { bottom: 0; transform-origin: bottom; }
        .<?php echo $uid; ?>.olo-mm-mob-active .olo-mm-fullscreen { visibility: visible; }
        .<?php echo $uid; ?>.olo-mm-mob-active .olo-mm-fullscreen::before,
        .<?php echo $uid; ?>.olo-mm-mob-active .olo-mm-fullscreen::after { transform: scaleY(1); }
        <?php elseif ( $fs_anim === 'circular' ) : ?>
        .<?php echo $uid; ?> .olo-mm-fullscreen {
            clip-path: circle(0% at var(--burger-x, 95%) var(--burger-y, 5%));
            opacity: 1; visibility: hidden;
            transition: clip-path .6s cubic-bezier(.4,0,.2,1), visibility .6s;
        }
        .<?php echo $uid; ?>.olo-mm-mob-active .olo-mm-fullscreen {
            clip-path: circle(150% at var(--burger-x, 95%) var(--burger-y, 5%));
            visibility: visible;
        }
        <?php elseif ( $fs_anim === 'diagonal' ) : ?>
        .<?php echo $uid; ?> .olo-mm-fullscreen {
            clip-path: polygon(0 0, 0 0, 0 100%, 0 100%);
            opacity: 1; visibility: hidden;
            transition: clip-path .5s cubic-bezier(.4,0,.2,1), visibility .5s;
        }
        .<?php echo $uid; ?>.olo-mm-mob-active .olo-mm-fullscreen {
            clip-path: polygon(0 0, 200% 0, 100% 100%, 0 100%);
            visibility: visible;
        }
        <?php endif; ?>

        <?php
        // ── STAGGER VOCI MENU ──
        if ( $items_anim !== 'none' ) :
            $init_transform = '';
            $init_filter = '';
            if ( $items_anim === 'fade-down' ) $init_transform = 'translateY(-20px)';
            elseif ( $items_anim === 'fade-up' ) $init_transform = 'translateY(20px)';
            elseif ( $items_anim === 'slide-left' ) $init_transform = 'translateX(-30px)';
            elseif ( $items_anim === 'slide-right' ) $init_transform = 'translateX(30px)';
            elseif ( $items_anim === 'scale' ) $init_transform = 'scale(0.8)';
            elseif ( $items_anim === 'blur' ) $init_filter = 'blur(10px)';
        ?>
        .<?php echo $uid; ?> .olo-mm-fs-nav > li,
        .<?php echo $uid; ?> .olo-mm-dp-nav > li {
            opacity: 0;
            <?php if ( $init_transform ) : ?>transform: <?php echo $init_transform; ?>;<?php endif; ?>
            <?php if ( $init_filter ) : ?>filter: <?php echo $init_filter; ?>;<?php endif; ?>
            transition: opacity .4s ease, transform .4s ease<?php echo $init_filter ? ', filter .4s ease' : ''; ?>;
        }
        .<?php echo $uid; ?>.olo-mm-mob-active .olo-mm-fs-nav > li,
        .<?php echo $uid; ?>.olo-mm-mob-active .olo-mm-dp-nav > li {
            opacity: 1; transform: none; filter: none;
        }
        <?php for ( $i = 1; $i <= 15; $i++ ) :
            $delay = ( $i - 1 ) * $items_stagger;
        ?>
        .<?php echo $uid; ?>.olo-mm-mob-active .olo-mm-fs-nav > li:nth-child(<?php echo $i; ?>),
        .<?php echo $uid; ?>.olo-mm-mob-active .olo-mm-dp-nav > li:nth-child(<?php echo $i; ?>) { transition-delay: <?php echo $delay; ?>ms; }
        <?php endfor; ?>
        <?php endif; ?>

        <?php
        // ── OFFCANVAS DIREZIONI EXTRA ──
        if ( $mob_slide_dir === 'top' ) : ?>
        .<?php echo $uid; ?> .olo-mm-offcanvas {
            left: 0; right: 0; top: 0; bottom: auto;
            width: 100%; height: 100vh; max-width: none;
            transform: translateY(-100%);
        }
        .<?php echo $uid; ?> .olo-mm-offcanvas.olo-mm-vis { transform: translateY(0); }
        <?php endif; ?>
        <?php if ( $oc_fullscreen ) : ?>
        .<?php echo $uid; ?> .olo-mm-offcanvas {
            width: 100vw; height: 100vh; max-width: none;
            left: 0; right: 0;
            <?php if ( $fs_anim === 'fade' ) : ?>
            transform: none; opacity: 0;
            transition: opacity .4s ease, visibility .4s;
            <?php elseif ( $fs_anim === 'slide-left' ) : ?>
            transform: translateX(-100%);
            transition: transform .5s cubic-bezier(.4,0,.2,1), visibility .5s;
            <?php elseif ( $fs_anim === 'slide-right' ) : ?>
            transform: translateX(100%);
            transition: transform .5s cubic-bezier(.4,0,.2,1), visibility .5s;
            <?php elseif ( $fs_anim === 'slide-up' ) : ?>
            transform: translateY(100%);
            transition: transform .5s cubic-bezier(.4,0,.2,1), visibility .5s;
            <?php elseif ( $fs_anim === 'circular' ) : ?>
            transform: none;
            clip-path: circle(0% at var(--burger-x, 95%) var(--burger-y, 5%));
            transition: clip-path .6s cubic-bezier(.4,0,.2,1), visibility .6s;
            <?php elseif ( $fs_anim === 'diagonal' ) : ?>
            transform: none;
            clip-path: polygon(0 0, 0 0, 0 100%, 0 100%);
            transition: clip-path .5s cubic-bezier(.4,0,.2,1), visibility .5s;
            <?php elseif ( $fs_anim === 'curtain' ) : ?>
            transform: none; opacity: 1;
            <?php endif; ?>
        }
        .<?php echo $uid; ?> .olo-mm-offcanvas.olo-mm-vis {
            <?php if ( $fs_anim === 'fade' ) : ?>
            opacity: 1;
            <?php elseif ( in_array( $fs_anim, ['slide-left', 'slide-right'] ) ) : ?>
            transform: translateX(0);
            <?php elseif ( $fs_anim === 'slide-up' ) : ?>
            transform: translateY(0);
            <?php elseif ( $fs_anim === 'circular' ) : ?>
            clip-path: circle(150% at var(--burger-x, 95%) var(--burger-y, 5%));
            <?php elseif ( $fs_anim === 'diagonal' ) : ?>
            clip-path: polygon(0 0, 200% 0, 100% 100%, 0 100%);
            <?php endif; ?>
        }
        <?php if ( $fs_anim === 'curtain' ) : ?>
        .<?php echo $uid; ?> .olo-mm-offcanvas::before,
        .<?php echo $uid; ?> .olo-mm-offcanvas::after {
            content: ''; position: fixed; left: 0; width: 100%; height: 50%;
            background: <?php echo $mob_bg; ?>; z-index: -1;
            transform: scaleY(0); transition: transform .5s cubic-bezier(.4,0,.2,1);
        }
        .<?php echo $uid; ?> .olo-mm-offcanvas::before { top: 0; transform-origin: top; }
        .<?php echo $uid; ?> .olo-mm-offcanvas::after { bottom: 0; transform-origin: bottom; }
        .<?php echo $uid; ?> .olo-mm-offcanvas.olo-mm-vis::before,
        .<?php echo $uid; ?> .olo-mm-offcanvas.olo-mm-vis::after { transform: scaleY(1); }
        <?php endif; ?>
        <?php // Stagger voci menu nell'offcanvas fullscreen ?>
        <?php if ( $items_anim !== 'none' ) :
            $oc_init_transform = '';
            $oc_init_filter = '';
            if ( $items_anim === 'fade-down' ) $oc_init_transform = 'translateY(-20px)';
            elseif ( $items_anim === 'fade-up' ) $oc_init_transform = 'translateY(20px)';
            elseif ( $items_anim === 'slide-left' ) $oc_init_transform = 'translateX(-30px)';
            elseif ( $items_anim === 'slide-right' ) $oc_init_transform = 'translateX(30px)';
            elseif ( $items_anim === 'scale' ) $oc_init_transform = 'scale(0.8)';
            elseif ( $items_anim === 'blur' ) $oc_init_filter = 'blur(10px)';
        ?>
        .<?php echo $uid; ?> .olo-mm-mob-nav > li {
            opacity: 0;
            <?php if ( $oc_init_transform ) : ?>transform: <?php echo $oc_init_transform; ?>;<?php endif; ?>
            <?php if ( $oc_init_filter ) : ?>filter: <?php echo $oc_init_filter; ?>;<?php endif; ?>
            transition: opacity .4s ease, transform .4s ease<?php echo $oc_init_filter ? ', filter .4s ease' : ''; ?>;
        }
        .<?php echo $uid; ?> .olo-mm-offcanvas.olo-mm-vis .olo-mm-mob-nav > li {
            opacity: 1; transform: none; filter: none;
        }
        <?php for ( $n = 1; $n <= 15; $n++ ) :
            $d = ( $n - 1 ) * $items_stagger;
        ?>
        .<?php echo $uid; ?> .olo-mm-offcanvas.olo-mm-vis .olo-mm-mob-nav > li:nth-child(<?php echo $n; ?>) { transition-delay: <?php echo $d; ?>ms; }
        <?php endfor; ?>
        <?php endif; ?>
        <?php endif; ?>

        <?php
        // ── SOCIAL ICONS ──
        ?>
        .<?php echo $uid; ?> .olo-mm-social { display: flex; align-items: center; gap: 8px; }
        .<?php echo $uid; ?> .olo-mm-social a {
            display: flex; align-items: center; justify-content: center;
            color: <?php echo $soc_color; ?>; text-decoration: none;
            transition: color .2s, background .2s, transform .2s;
        }
        .<?php echo $uid; ?> .olo-mm-social a:hover {
            color: <?php echo $soc_hcolor; ?>; transform: translateY(-2px);
        }
        .<?php echo $uid; ?> .olo-mm-social svg { width: <?php echo $soc_size; ?>px; height: <?php echo $soc_size; ?>px; fill: currentColor; }
        <?php if ( $soc_style === 'circle' ) : ?>
        .<?php echo $uid; ?> .olo-mm-social a {
            width: <?php echo $soc_size + 14; ?>px; height: <?php echo $soc_size + 14; ?>px;
            border-radius: 50%; background: rgba(128,128,128,.12);
        }
        .<?php echo $uid; ?> .olo-mm-social a:hover { background: rgba(128,128,128,.22); }
        <?php elseif ( $soc_style === 'rounded' ) : ?>
        .<?php echo $uid; ?> .olo-mm-social a {
            padding: 6px; border-radius: 6px; background: rgba(128,128,128,.12);
        }
        .<?php echo $uid; ?> .olo-mm-social a:hover { background: rgba(128,128,128,.22); }
        <?php endif; ?>
        .<?php echo $uid; ?> .olo-mm-social-bar { order: 89; }
        .<?php echo $uid; ?> .olo-mm-social-footer {
            padding: 20px 24px; justify-content: center;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        <?php /* ── Social visibility: toggle-based ── */ ?>
        <?php if ( $soc_navbar_side === 'left' ) : ?>
        .<?php echo $uid; ?> .olo-mm-social-bar { order: -1; }
        <?php endif; ?>
        <?php if ( ! $soc_in_navbar ) : ?>
        .<?php echo $uid; ?> .olo-mm-social-bar { display: none !important; }
        <?php endif; ?>
        <?php if ( $soc_in_mobile && $soc_mobile_pos === 'top' ) : ?>
        .<?php echo $uid; ?> .olo-mm-social-footer { order: -1; border-top: none; border-bottom: 1px solid rgba(255,255,255,.08); }
        <?php endif; ?>
        <?php if ( ! $soc_in_mobile ) : ?>
        .<?php echo $uid; ?> .olo-mm-social-footer { display: none !important; }
        <?php else : ?>
        @media (min-width: <?php echo $bp + 1; ?>px) {
            .<?php echo $uid; ?> .olo-mm-social-footer { display: none !important; }
        }
        <?php endif; ?>

        /* ── Top Bar ── */
        <?php if ( ! empty( $s['topbar_enabled'] ) ) : ?>
        .<?php echo $uid; ?> .olo-mm-topbar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 <?php echo absint($this->pad_int($s['bar_padding'] ?? null, 16)); ?>px;
            height: <?php echo absint($s['topbar_height']); ?>px;
            background: <?php echo esc_attr($s['topbar_bg']); ?>;
            color: <?php echo esc_attr($s['topbar_text_color']); ?>;
            font-size: <?php echo absint($s['topbar_font_size']); ?>px;
            line-height: 1; position: relative; z-index: 100000;
            width: 100vw;
            margin-left: calc(-50vw + 50%);
            padding-left: max(<?php echo absint($this->pad_int($s['bar_padding'] ?? null, 16)); ?>px, calc(50vw - 600px));
            padding-right: max(<?php echo absint($this->pad_int($s['bar_padding'] ?? null, 16)); ?>px, calc(50vw - 600px));
            box-sizing: border-box;
        }
        <?php if ( ! empty( $s['topbar_border_bottom'] ) ) : ?>
        .<?php echo $uid; ?> .olo-mm-topbar { border-bottom: 1px solid <?php echo $s['topbar_border_color'] ? esc_attr($s['topbar_border_color']) : 'rgba(255,255,255,0.1)'; ?>; }
        <?php endif; ?>
        .<?php echo $uid; ?> .olo-mm-topbar a { color: <?php echo esc_attr($s['topbar_link_color']); ?>; text-decoration: none; transition: opacity 0.2s; }
        .<?php echo $uid; ?> .olo-mm-topbar a:hover { opacity: 0.7; }
        .<?php echo $uid; ?> .olo-mm-topbar-left { display: flex; align-items: center; gap: 16px; position: relative; flex: 1; min-width: 0; overflow: hidden; }
        .<?php echo $uid; ?> .olo-mm-topbar-right { display: flex; align-items: center; gap: 16px; position: relative; flex-shrink: 0; }
        .<?php echo $uid; ?> .olo-mm-topbar-ticker { display: flex; align-items: center; gap: 8px; overflow: hidden; flex: 1; min-width: 0; }
        .<?php echo $uid; ?> .olo-mm-topbar-ticker-label { font-weight: 700; white-space: nowrap; color: <?php echo esc_attr($s['topbar_link_color']); ?>; }
        .<?php echo $uid; ?> .olo-mm-topbar-ticker-wrap { overflow: hidden; flex: 1; }
        .<?php echo $uid; ?> .olo-mm-topbar-ticker-items { display: flex; animation: olo-mm-ticker <?php echo absint($s['topbar_ticker_speed'] ?: 5) * 3; ?>s linear infinite; white-space: nowrap; }
        .<?php echo $uid; ?> .olo-mm-topbar-ticker-items span { padding: 0 24px; }
        @keyframes olo-mm-ticker { from { transform: translateX(0); } to { transform: translateX(-50%); } }
        .<?php echo $uid; ?> .olo-mm-topbar-social { display: flex; gap: 10px; align-items: center; }
        .<?php echo $uid; ?> .olo-mm-topbar-social a { display: flex; }
        .<?php echo $uid; ?> .olo-mm-topbar-social svg { width: 16px; height: 16px; fill: currentColor; }
        .<?php echo $uid; ?> .olo-mm-topbar-search { cursor: pointer; display: flex; align-items: center; }
        .<?php echo $uid; ?> .olo-mm-topbar-cart { display: flex; align-items: center; gap: 4px; background: rgba(255,255,255,0.1); padding: 4px 12px; border-radius: 4px; font-size: 12px; }
        .<?php echo $uid; ?> .olo-mm-topbar-cta { display: inline-flex; padding: 4px 14px; border-radius: 4px; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        .<?php echo $uid; ?> .olo-mm-topbar-dropdown { display: none; position: absolute; top: 100%; left: 0; min-width: 200px; padding: 8px 0; box-shadow: 0 4px 12px rgba(0,0,0,.15); z-index: 10000; border-radius: 0 0 4px 4px; }
        .<?php echo $uid; ?> .olo-mm-topbar-dropdown a { display: block; padding: 8px 20px; }
        .<?php echo $uid; ?> .olo-mm-topbar-dropdown a:hover { background: rgba(255,255,255,0.05); }
        <?php if ( ! empty( $s['topbar_hide_mobile'] ) ) : ?>
        @media (max-width: <?php echo absint($s['mobile_breakpoint']); ?>px) { .<?php echo $uid; ?> .olo-mm-topbar { display: none; } }
        <?php endif; ?>
        <?php if ( ! empty( $s['topbar_hide_sticky'] ) ) : ?>
        .olo-header-sticky .<?php echo $uid; ?> .olo-mm-topbar,
        .olo-sticky-on .<?php echo $uid; ?> .olo-mm-topbar { display: none; }
        <?php endif; ?>
        <?php endif; /* topbar_enabled */ ?>
        </style>
        <?php
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    /* ─── Toggle SVG ─── */

    private function get_toggle_svg( $style, $size = 20 ) {
        $w = intval( $size );
        switch ( $style ) {
            case 'plus-minus':
                return '<span class="olo-mm-toggle-open" style="width:' . $w . 'px;height:' . $w . 'px"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span>'
                     . '<span class="olo-mm-toggle-close" style="width:' . $w . 'px;height:' . $w . 'px;display:none"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/></svg></span>';
            case 'circle-plus':
                return '<span class="olo-mm-toggle-open" style="width:' . $w . 'px;height:' . $w . 'px"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16" stroke-linecap="round"/><line x1="8" y1="12" x2="16" y2="12" stroke-linecap="round"/></svg></span>'
                     . '<span class="olo-mm-toggle-close" style="width:' . $w . 'px;height:' . $w . 'px;display:none"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12" stroke-linecap="round"/></svg></span>';
            case 'arrow':
                return '<span class="olo-mm-toggle-icon" style="width:' . $w . 'px;height:' . $w . 'px"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>';
            case 'caret':
                return '<span class="olo-mm-toggle-icon" style="width:' . $w . 'px;height:' . $w . 'px"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5l10 7-10 7z"/></svg></span>';
            case 'bracket':
                return '<span class="olo-mm-toggle-icon" style="width:' . $w . 'px;height:' . $w . 'px"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 6 15 12 9 18"/></svg></span>';
            case 'dot-line':
                return '<span class="olo-mm-toggle-open" style="width:' . $w . 'px;height:' . $w . 'px"><svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="4"/></svg></span>'
                     . '<span class="olo-mm-toggle-close" style="width:' . $w . 'px;height:' . $w . 'px;display:none"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="6" y1="12" x2="18" y2="12"/></svg></span>';
            case 'none':
                return '';
            case 'chevron':
            default:
                return '<span class="olo-mm-toggle-icon" style="width:' . $w . 'px;height:' . $w . 'px"><svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg></span>';
        }
    }

    /* ─── Search SVG ─── */

    private function get_search_svg( $style = 'lens' ) {
        switch ( $style ) {
            case 'minimal':
                return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/></svg>';
            case 'thin':
                return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="10.5" cy="10.5" r="7.5"/><line x1="16" y1="16" x2="22" y2="22"/></svg>';
            case 'target':
                return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="8"/><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/></svg>';
            case 'binocular':
                return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="14" r="5"/><circle cx="16" cy="14" r="5"/><line x1="8" y1="9" x2="8" y2="4"/><line x1="16" y1="9" x2="16" y2="4"/><line x1="8" y1="4" x2="16" y2="4"/></svg>';
            case 'dot':
                return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="8" r="1.5" fill="currentColor" stroke="none"/><path d="M8 20c0-2.5 1.5-4.5 4-4.5s4 2 4 4.5"/><path d="M9.5 12.5c0 0 .5-2 2.5-2s2.5 2 2.5 2"/></svg>';
            case 'lens':
            default:
                return '<svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>';
        }
    }

    /* ─── Logo wordmark (dot before | dot inline sul ".") ─── */

    /**
     * HTML del logo testuale: pallino prima del testo (comportamento storico)
     * oppure dot "inline" = la prima occorrenza del carattere '.' dentro logo_text
     * avvolta in uno span colorato (es. 'clod.eu' → clod<span>.</span>eu).
     * Escaping: le parti di testo passano da esc_html, nessun HTML utente.
     *
     * @param array  $s            Settings.
     * @param string $logo_text    Testo del logo (raw).
     * @param int    $logo_txt_sz  Dimensione font già clampata.
     * @param string $color_style  Stringa stile extra già escapata (es. ';color:#fff') o ''.
     * @return string HTML sicuro.
     */
    private function logo_wordmark_html( $s, $logo_text, $logo_txt_sz, $color_style = '' ) {
        $logo_dot = ! empty( $s['logo_dot'] );
        $dot_pos  = ( ( $s['logo_dot_position'] ?? 'before' ) === 'inline' ) ? 'inline' : 'before';
        $dot_col  = $this->safe_color_css( $s['logo_dot_color'] ?? '' );

        $html = '';
        if ( $logo_dot && $dot_pos === 'before' ) {
            // Retro-compat: senza colore custom il pallino resta currentColor (CSS base).
            $html .= '<span class="olo-mm-logo-dot"' . ( $dot_col ? ' style="background:' . esc_attr( $dot_col ) . '"' : '' ) . '></span>';
        }

        $text_html = esc_html( $logo_text );
        if ( $logo_dot && $dot_pos === 'inline' ) {
            $dot_at = strpos( $logo_text, '.' );
            if ( $dot_at !== false ) {
                $inline_col = $dot_col ?: 'var(--olo-color-primary, #C6F24E)';
                $text_html  = esc_html( substr( $logo_text, 0, $dot_at ) )
                    . '<span class="olo-mm-logo-dot-inline" style="color:' . esc_attr( $inline_col ) . '">.</span>'
                    . esc_html( substr( $logo_text, $dot_at + 1 ) );
            }
        }

        $html .= '<span class="olo-mm-logo-text" style="font-size:' . (int) $logo_txt_sz . 'px' . $color_style . '">' . $text_html . '</span>';
        return $html;
    }

    /* ─── Extra Links ─── */

    /**
     * Rende la tile langswitcher referenziata da lang_tile_id dentro la barra
     * (o nell'off-canvas mobile). Stesso pattern della search tile del navmenu:
     * la tile vive nel template — configurabile con tutta la sua UI dal builder —
     * e il renderer la sopprime nella posizione originale (referenced_tile_ids).
     *
     * @param array $s      Settings del megamenu.
     * @param bool  $mobile Contesto off-canvas: forza il layout inline della tile.
     */
    private function render_referenced_lang( $s, $mobile = false ) {
        $tile_id = trim( (string) ( $s['lang_tile_id'] ?? '' ) );
        if ( $tile_id === '' ) return;

        $tile_data = Olobuild_Frontend_Renderer::find_tile( $tile_id );
        if ( ! $tile_data || ( $tile_data['type'] ?? '' ) !== 'langswitcher' ) return;

        $manager       = Olobuild_Tile_Manager::instance();
        $tile_instance = $manager->get_tile( 'langswitcher' );
        if ( ! $tile_instance ) return;

        $tile_settings = $tile_data['settings'];
        if ( $mobile ) {
            // Nell'off-canvas un dropdown/floating non ha spazio: sempre inline.
            $tile_settings['layout'] = 'inline';
        }

        echo '<div class="olo-mm-lang">';
        echo $tile_instance->render( $tile_settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML generated by the referenced tile's render() method, which escapes its output internally
        echo '</div>';
    }

    private function render_extra_links( $s, $context = 'desktop' ) {
        // Timecode di scroll "sala di regia" — nella zona destra, PRIMA degli extra link.
        if ( $context !== 'mobile' && ! empty( $s['show_timecode'] ) ) {
            echo '<li class="olo-mm-tc-li"><span class="olo-mm-tc">TC 00:00:00:00</span></li>';
        }
        for ( $i = 1; $i <= 4; $i++ ) {
            $label = trim( $s["extra_link_{$i}_label"] ?? '' );
            $url   = trim( $s["extra_link_{$i}_url"] ?? '' );
            // Voce carrello WooCommerce: URL automatico + conteggio "(n)" aggiornato via cart fragments
            $cart_html = '';
            if ( ! empty( $s["extra_link_{$i}_cart"] ) && function_exists( 'wc_get_cart_url' ) ) {
                if ( $label === '' ) { $label = olobuild_t( 'Carrello' ); }
                $url       = wc_get_cart_url();
                $cart_n    = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
                $cart_html = ' <span class="olo-mm-cart-count">(' . (int) $cart_n . ')</span>';
            }
            if ( $label === '' || $url === '' ) continue;
            $blank = ! empty( $s["extra_link_{$i}_blank"] );
            $tgt   = $blank ? ' target="_blank" rel="noopener noreferrer"' : '';
            $is_btn = ! empty( $s["extra_link_{$i}_button"] );
            if ( $context === 'mobile' ) : ?>
                <li><a href="<?php echo esc_url( $url ); ?>"<?php echo $is_btn ? ' class="olo-mm-btn olo-mm-mob-btn"' : ''; ?><?php echo $tgt; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $tgt is a fixed literal attribute string set above; URL/label escaped inline ?>><?php echo esc_html( $label ) . $cart_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $cart_html built above from a fixed literal + (int) cart count ?></a></li>
            <?php elseif ( $is_btn ) : ?>
                <li><a class="olo-mm-btn" href="<?php echo esc_url( $url ); ?>"<?php echo $tgt; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $tgt is a fixed literal attribute string set above; URL/label escaped inline ?>><?php echo esc_html( $label ) . $cart_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $cart_html built above from a fixed literal + (int) cart count ?></a></li>
            <?php else : ?>
                <li><a href="<?php echo esc_url( $url ); ?>" data-text="<?php echo esc_attr( $label ); ?>"<?php echo $tgt; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $tgt is a fixed literal attribute string set above; URL/label escaped inline ?>><?php echo esc_html( $label ) . $cart_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $cart_html built above from a fixed literal + (int) cart count ?></a></li>
            <?php endif;
        }
        $phone = trim( (string) ( $s['nav_phone'] ?? '' ) );
        if ( $phone !== '' ) {
            $purl = trim( (string) ( $s['nav_phone_url'] ?? '' ) );
            $pcol = $this->safe_color_css( $s['nav_phone_color'] ?? '' ) ?: 'var(--olo-color-primary)';
            $href = $purl !== '' ? $purl : ( 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ) );
            $li   = $context === 'mobile' ? '<li>' : '<li class="olo-mm-tel-li">';
            echo $li . '<a class="olo-mm-tel" href="' . esc_url( $href ) . '" style="color:' . esc_attr( $pcol ) . '">' . esc_html( $phone ) . '</a></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $li is a fixed literal <li> string set above; URL/color/text escaped inline (esc_url/esc_attr/esc_html)
        }
    }

    /* ─── Social Icons ─── */

    private function render_social_icons( $s, $context = 'bar' ) {
        $socials = [
            'facebook'  => 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z',
            'instagram' => 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z',
            'x'         => 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z',
            'linkedin'  => 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z',
            'youtube'   => 'M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z',
            'tiktok'    => 'M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z',
            'pinterest' => 'M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 01.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12.017 24c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641 0 12.017 0z',
            'whatsapp'  => 'M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z',
        ];
        $pos = $s['social_position'] ?? 'menu-footer';
        $has_any = false;
        foreach ( $socials as $key => $path ) {
            if ( ! empty( $s["social_{$key}"] ) ) { $has_any = true; break; }
        }
        if ( ! $has_any ) return;

        echo '<div class="olo-mm-social olo-mm-social-' . esc_attr( $context ) . '">';
        foreach ( $socials as $key => $path ) {
            $url = $s["social_{$key}"] ?? '';
            if ( empty( $url ) ) continue;
            echo '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr( ucfirst( $key ) ) . '">';
            echo '<svg viewBox="0 0 24 24"><path d="' . $path . '"/></svg>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $path is static SVG path data hardcoded in this method
            echo '</a>';
        }
        echo '</div>';
    }

    /* ─── HTML ─── */

    private function render_html( $tree, $children, $grandchildren, $s, $uid ) {
        $current_url = trailingslashit( home_url( add_query_arg( [], false ) ) );
        $total       = count( $tree );
        $show_desc   = ! empty( $s['show_descriptions'] );
        ?>
        <?php
        $logo_img    = esc_url( $s['logo_image'] ?? '' );
        $logo_link   = esc_url( $s['logo_link'] ?: home_url('/') );
        $logo_sticky = esc_url( $s['logo_sticky'] ?? '' );
        $mob_logo    = esc_url( $s['mobile_logo'] ?? '' );
        // Logo testuale (wordmark): usato quando non c'è un'immagine logo
        $logo_text   = trim( (string) ( $s['logo_text'] ?? '' ) );
        $logo_dot    = ! empty( $s['logo_dot'] );
        $ltc_raw     = trim( (string) ( $s['logo_text_color'] ?? '' ) );
        $logo_txt_c  = preg_match( '/^#[0-9a-fA-F]{3,8}$/', $ltc_raw ) || preg_match( '/^(rgb|hsl|var)/i', $ltc_raw ) ? $ltc_raw : '';
        $logo_txt_sz = max( 12, min( 40, absint( $s['logo_text_size'] ?? 19 ) ) );
        $logo_crest  = trim( (string) ( $s['logo_crest'] ?? '' ) );
        $crest_bg    = $this->safe_color_css( $s['logo_crest_bg'] ?? '' ) ?: 'var(--olo-color-primary, #c8ff3c)';
        $crest_col   = $this->safe_color_css( $s['logo_crest_color'] ?? '' ) ?: 'var(--olo-color-primary-contrast, #0a2a1e)';
        $mob_search  = ! empty( $s['mobile_search'] );
        /*
         * Le quattro impostazioni del menu a schermo intero erano lette solo in
         * render_css(): qui dentro non esistevano, e PHP le trattava come null.
         *
         * Il guaio non era l'avviso ma cosa ne seguiva. `null !== ''` in PHP e'
         * VERO, quindi la condizione «c'e' qualcosa da scrivere nel piede»
         * risultava sempre soddisfatta: il menu a schermo intero mostrava una
         * fascia in fondo con un collegamento vuoto che porta a '#'. E
         * $mob_numbers, sempre nullo, spegneva la numerazione delle voci anche
         * quando il docente l'aveva accesa. Contati 252 avvisi nei registri del
         * server il 12 agosto 2026.
         */
        $mob_numbers   = ! empty( $s['mobile_numbers'] );
        $mob_foot_text = trim( (string) ( $s['mobile_footer_text'] ?? '' ) );
        $mob_foot_cta  = trim( (string) ( $s['mobile_footer_cta_text'] ?? '' ) );
        $mob_foot_url  = trim( (string) ( $s['mobile_footer_cta_url'] ?? '' ) );
        $mob_bar_logo= ! empty( $s['mobile_bar_logo'] );
        $search_icon = ! empty( $s['search_icon'] );
        $ham_style_val = $s['hamburger_style'] ?? 'classic';
        $search_svg  = $this->get_search_svg( $s['search_icon_style'] ?? 'lens' );
        $tgl_style   = $s['mob_toggle_style'] ?? 'chevron';
        $tgl_size    = intval( $s['mob_toggle_size'] ?? 20 );
        $toggle_svg  = $this->get_toggle_svg( $tgl_style, $tgl_size );

        // Hamburger SVG icons — ogni stile ha un SVG dedicato per stato aperto/chiuso
        $hamburger_svgs = [
            'classic'  => '<svg class="olo-mm-ham-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line class="olo-mm-ham-top" x1="3" y1="6" x2="21" y2="6"/><line class="olo-mm-ham-mid" x1="3" y1="12" x2="21" y2="12"/><line class="olo-mm-ham-bot" x1="3" y1="18" x2="21" y2="18"/></svg>',
            'squeeze'  => '<svg class="olo-mm-ham-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line class="olo-mm-ham-top" x1="3" y1="6" x2="21" y2="6"/><line class="olo-mm-ham-mid" x1="3" y1="12" x2="21" y2="12"/><line class="olo-mm-ham-bot" x1="3" y1="18" x2="21" y2="18"/></svg>',
            'arrow'    => '<svg class="olo-mm-ham-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line class="olo-mm-ham-top" x1="3" y1="6" x2="21" y2="6"/><line class="olo-mm-ham-mid" x1="3" y1="12" x2="21" y2="12"/><line class="olo-mm-ham-bot" x1="3" y1="18" x2="21" y2="18"/></svg>',
            'minimal'  => '<svg class="olo-mm-ham-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line class="olo-mm-ham-top" x1="3" y1="8" x2="16" y2="8"/><line class="olo-mm-ham-bot" x1="3" y1="16" x2="21" y2="16"/></svg>',
            'dot-grid' => '<svg class="olo-mm-ham-svg" viewBox="0 0 24 24" fill="currentColor"><circle class="olo-mm-ham-d1" cx="5" cy="5" r="2"/><circle class="olo-mm-ham-d2" cx="12" cy="5" r="2"/><circle class="olo-mm-ham-d3" cx="19" cy="5" r="2"/><circle class="olo-mm-ham-d4" cx="5" cy="12" r="2"/><circle class="olo-mm-ham-d5" cx="12" cy="12" r="2"/><circle class="olo-mm-ham-d6" cx="19" cy="12" r="2"/><circle class="olo-mm-ham-d7" cx="5" cy="19" r="2"/><circle class="olo-mm-ham-d8" cx="12" cy="19" r="2"/><circle class="olo-mm-ham-d9" cx="19" cy="19" r="2"/></svg>',
            'collapse' => '<svg class="olo-mm-ham-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line class="olo-mm-ham-top" x1="3" y1="6" x2="21" y2="6"/><line class="olo-mm-ham-mid" x1="3" y1="12" x2="21" y2="12"/><line class="olo-mm-ham-bot" x1="3" y1="18" x2="21" y2="18"/></svg>',
            'rotate'   => '<svg class="olo-mm-ham-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line class="olo-mm-ham-top" x1="3" y1="6" x2="21" y2="6"/><line class="olo-mm-ham-mid" x1="3" y1="12" x2="21" y2="12"/><line class="olo-mm-ham-bot" x1="3" y1="18" x2="21" y2="18"/></svg>',
            'elastic'  => '<svg class="olo-mm-ham-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line class="olo-mm-ham-top" x1="3" y1="6" x2="21" y2="6"/><line class="olo-mm-ham-mid" x1="3" y1="12" x2="21" y2="12"/><line class="olo-mm-ham-bot" x1="3" y1="18" x2="21" y2="18"/></svg>',
            'morph'    => '<svg class="olo-mm-ham-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path class="olo-mm-ham-top" d="M3 6h18"/><path class="olo-mm-ham-mid" d="M3 12h18"/><path class="olo-mm-ham-bot" d="M3 18h18"/></svg>',
            'magnetic' => '<svg class="olo-mm-ham-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line class="olo-mm-ham-top" x1="3" y1="6" x2="21" y2="6"/><line class="olo-mm-ham-mid" x1="3" y1="12" x2="21" y2="12"/><line class="olo-mm-ham-bot" x1="3" y1="18" x2="21" y2="18"/></svg>',
        ];
        $ham_svg = $hamburger_svgs[ $ham_style_val ] ?? $hamburger_svgs['classic'];

        // Collect CTA button items for mobile bar
        $cta_items = [];
        foreach ( $tree as $idx => $item ) {
            if ( $this->is_button_item( $item, $idx, $total, $s ) ) {
                $cta_items[] = $item;
            }
        }
        ?>
        <div class="olo-megamenu <?php echo esc_attr( $uid ); ?>" data-uid="<?php echo esc_attr( $uid ); ?>">
                        <?php if ( ! empty( $s['topbar_enabled'] ) ) : ?>
            <div class="olo-mm-topbar">
                <div class="olo-mm-topbar-left">
                    <?php $tb_left = $s['topbar_left_content'] ?? 'none'; ?>
                    <?php if ( $tb_left === 'hamburger' ) : ?>
                        <button class="olo-mm-topbar-hamburger" style="background:none;border:none;color:inherit;cursor:pointer;padding:4px;position:relative" onclick="var p=this.nextElementSibling;if(p)p.style.display=p.style.display==='block'?'none':'block'" aria-label="<?php echo esc_attr( olobuild_t( 'Menu' ) ); ?>">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
                        </button>
                        <?php
                        $tb_menu_id = absint( $s['topbar_left_menu_id'] ?? 0 );
                        if ( $tb_menu_id ) :
                            $tb_items = wp_get_nav_menu_items( $tb_menu_id );
                            if ( $tb_items ) : ?>
                        <div class="olo-mm-topbar-dropdown" style="background:<?php echo esc_attr($s['topbar_bg']); ?>">
                            <?php foreach ( $tb_items as $ti ) : ?>
                            <a href="<?php echo esc_url( $ti->url ); ?>"><?php echo esc_html( $ti->title ); ?></a>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; endif; ?>
                    <?php elseif ( $tb_left === 'text' ) : ?>
                        <span><?php echo wp_kses_post( $s['topbar_left_text'] ?? '' ); ?></span>
                    <?php elseif ( $tb_left === 'ticker' ) :
                        $ticker_raw = $s['topbar_ticker_items'] ?? '';
                        $ticker_arr = array_filter( array_map( 'trim', explode( "\n", $ticker_raw ) ) );
                        if ( ! empty( $ticker_arr ) ) : ?>
                        <div class="olo-mm-topbar-ticker">
                            <?php if ( ! empty( $s['topbar_ticker_label'] ) ) : ?>
                            <span class="olo-mm-topbar-ticker-label"><?php echo esc_html( $s['topbar_ticker_label'] ); ?></span>
                            <?php endif; ?>
                            <div class="olo-mm-topbar-ticker-wrap">
                                <div class="olo-mm-topbar-ticker-items">
                                    <?php foreach ( $ticker_arr as $ti ) : ?><span><?php echo esc_html( $ti ); ?></span><?php endforeach; ?>
                                    <?php foreach ( $ticker_arr as $ti ) : ?><span><?php echo esc_html( $ti ); ?></span><?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; endif; ?>
                </div>
                <div class="olo-mm-topbar-right">
                    <?php if ( ! empty( $s['topbar_right_text'] ) ) : ?>
                        <span><?php echo wp_kses_post( $s['topbar_right_text'] ); ?></span>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['topbar_right_social'] ) || ! empty( $s['social_in_topbar'] ) ) :
                        $tb_socials = [
                            'facebook'  => ['url' => $s['social_facebook'] ?? '',  'icon' => '<path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>'],
                            'x'         => ['url' => $s['social_x'] ?? '',         'icon' => '<path d="M4 4l6.5 7.8L4 20h2.1l5.6-7 4.5 7H20l-6.8-8.2L19.5 4h-2.1l-5.2 6.5L8 4H4z"/>'],
                            'instagram' => ['url' => $s['social_instagram'] ?? '', 'icon' => '<rect x="2" y="2" width="20" height="20" rx="5" fill="none" stroke="currentColor" stroke-width="1.5"/><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="1.5"/><circle cx="17.5" cy="6.5" r="1.5"/>'],
                            'linkedin'  => ['url' => $s['social_linkedin'] ?? '',  'icon' => '<path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-4 0v7h-4v-7a6 6 0 016-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/>'],
                            'youtube'   => ['url' => $s['social_youtube'] ?? '',   'icon' => '<path d="M22.54 6.42A2.78 2.78 0 0020.6 4.5C18.88 4 12 4 12 4s-6.88 0-8.6.5A2.78 2.78 0 001.46 6.42 29 29 0 001 12a29 29 0 00.46 5.58A2.78 2.78 0 003.4 19.5C5.12 20 12 20 12 20s6.88 0 8.6-.5a2.78 2.78 0 001.94-1.92A29 29 0 0023 12a29 29 0 00-.46-5.58zM9.75 15.02V8.98L15.5 12l-5.75 3.02z"/>'],
                        ];
                    ?>
                        <div class="olo-mm-topbar-social">
                        <?php foreach ( $tb_socials as $sn => $sd ) :
                            if ( ! empty( $sd['url'] ) ) : ?>
                            <a href="<?php echo esc_url( $sd['url'] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $sn ); ?>">
                                <svg viewBox="0 0 24 24"><?php echo $sd['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG icon markup hardcoded in the $tb_socials array above ?></svg>
                            </a>
                        <?php endif; endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php
                    $show_topbar_search = ! empty( $s['topbar_right_search'] ) || in_array( $s['search_position'] ?? 'navbar', ['topbar', 'both'] );
                    if ( $show_topbar_search ) : ?>
                        <div class="olo-mm-topbar-search-wrap" style="display:flex;align-items:center;gap:6px;position:relative">
                            <span class="olo-mm-topbar-search" onclick="var f=this.nextElementSibling;if(f){if(f.style.display==='flex'){f.style.display='none'}else{f.style.display='flex';f.querySelector('input').focus()}}" title="<?php echo esc_attr( olobuild_t( 'Cerca' ) ); ?>" style="cursor:pointer;display:flex">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                            </span>
                            <form class="olo-mm-topbar-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" style="display:none;position:absolute;right:0;top:100%;margin-top:8px;z-index:999;background:<?php echo esc_attr($s['topbar_bg'] ?: '#1F2937'); ?>;padding:8px;border-radius:6px;box-shadow:0 4px 12px rgba(0,0,0,.2)">
                                <input type="search" name="s" placeholder="<?php echo esc_attr( olobuild_t( 'Cerca...' ) ); ?>" style="width:220px;padding:8px 12px;border:1px solid rgba(255,255,255,.2);border-radius:4px;background:rgba(255,255,255,.1);color:<?php echo esc_attr($s['topbar_link_color'] ?: '#fff'); ?>;font-size:13px;outline:none" />
                            </form>
                        </div>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['topbar_right_cart'] ) && function_exists( 'WC' ) ) :
                        $cart_count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0; ?>
                        <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="olo-mm-topbar-cart">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>
                            <span><?php echo (int) $cart_count; ?> Items</span>
                        </a>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['topbar_right_cta_label'] ) && ! empty( $s['topbar_right_cta_url'] ) ) : ?>
                        <a href="<?php echo esc_url( $s['topbar_right_cta_url'] ); ?>" class="olo-mm-topbar-cta" style="background:<?php echo esc_attr( $s['topbar_right_cta_bg'] ?: 'var(--olo-color-primary, #2563EB)' ); ?>;color:<?php echo esc_attr( $s['topbar_right_cta_color'] ); ?>"><?php echo esc_html( $s['topbar_right_cta_label'] ); ?></a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            <div class="olo-mm-bar">
                <?php // Mobile logo (shown only on mobile via CSS) ?>
                <?php if ( $mob_bar_logo ) : ?>
                <a class="olo-mm-mobile-logo" href="<?php echo $logo_link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_url()-escaped at assignment above ?>">
                    <?php if ( $mob_logo ?: $logo_img ) : ?><img src="<?php echo $mob_logo ?: $logo_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- both URLs esc_url()-escaped at assignment above; size absint-clamped, color esc_attr()-escaped inline ?>" alt="<?php echo esc_attr( olobuild_t( 'Logo' ) ); ?>"><?php elseif ( $logo_text !== '' ) : ?><?php echo $this->logo_wordmark_html( $s, $logo_text, $logo_txt_sz, $logo_txt_c ? ';color:' . esc_attr( $logo_txt_c ) : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML built by logo_wordmark_html() with esc_html/esc_attr/safe_color_css ?><?php endif; ?>
                </a>
                <?php endif; ?>

                <?php // Mobile search icon ?>
                <?php if ( $mob_search ) : ?>
                <button class="olo-mm-mobile-search olo-mm-icon-btn" aria-label="<?php echo esc_attr( olobuild_t( 'Cerca' ) ); ?>" type="button"><?php echo $search_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup from get_search_svg() (hardcoded strings) ?></button>
                <?php endif; ?>

                <?php // Mobile CTA buttons (visible only on mobile via CSS) ?>
                <?php if ( ! empty( $cta_items ) ) : ?>
                <div class="olo-mm-mobile-cta">
                    <?php foreach ( $cta_items as $cta ) : ?>
                    <a href="<?php echo esc_url( $cta->url ); ?>" class="olo-mm-btn olo-mm-mob-btn"<?php echo $cta->target ? ' target="' . esc_attr( $cta->target ) . '"' : ''; ?>><?php echo esc_html( $cta->title ); ?></a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <button class="olo-mm-hamburger olo-mm-ham-<?php echo esc_attr( $ham_style_val ); ?>" aria-label="<?php echo esc_attr( olobuild_t( 'Menu' ) ); ?>" aria-expanded="false">
                    <?php echo $ham_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup from the hardcoded $hamburger_svgs map above ?>
                </button>

                <?php // Desktop logo ?>
                <?php
                $logo_pos_val = $s['logo_position'] ?? 'left';
                $is_split = ( $logo_pos_val === 'split' );
                ?>
                <?php if ( $logo_img ) : ?>
                <a class="olo-mm-logo" href="<?php echo $logo_link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_url()-escaped at assignment above ?>">
                    <img class="olo-mm-logo-default" src="<?php echo $logo_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_url()-escaped at assignment above ?>" alt="<?php echo esc_attr( olobuild_t( 'Logo' ) ); ?>">
                    <?php if ( $logo_sticky ) : ?><img class="olo-mm-logo-sticky" src="<?php echo $logo_sticky; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_url()-escaped at assignment above ?>" alt="<?php echo esc_attr( olobuild_t( 'Logo' ) ); ?>" style="display:none;"><?php endif; ?>
                </a>
                <?php elseif ( $logo_text !== '' ) : ?>
                <a class="olo-mm-logo olo-mm-logo--text" href="<?php echo $logo_link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_url()-escaped at assignment above; color esc_attr()-escaped inline ?>"<?php echo $logo_txt_c ? ' style="color:' . esc_attr( $logo_txt_c ) . '"' : ''; ?>>
                    <?php if ( $logo_crest !== '' ) : ?><span class="olo-mm-crest" style="background:<?php echo $crest_bg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- crest colors validated by safe_color_css() whitelist above; size absint-clamped; texts esc_html()-escaped inline ?>;color:<?php echo $crest_col; ?>"><?php echo esc_html( $logo_crest ); ?></span><?php endif; ?><?php echo $this->logo_wordmark_html( $s, $logo_text, $logo_txt_sz ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML built by logo_wordmark_html() with esc_html/esc_attr/safe_color_css ?>
                </a>
                <?php endif; ?>

                <?php if ( $is_split ) : ?>
                <?php
                    // Split nav: divide items into left and right halves
                    $split_point = (int) ceil( $total / 2 );
                    $left_items  = array_slice( $tree, 0, $split_point );
                    $right_items = array_slice( $tree, $split_point );
                ?>
                <ul class="olo-mm-nav olo-mm-nav-left">
                    <?php foreach ( $left_items as $idx => $item ) :
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
                    <li<?php echo $li_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attribute string built with esc_attr() above ?>>
                        <?php $this->render_nav_item_content( $item, $subs, $grandchildren, $s, $show_desc, $is_button, $is_mega, $has_panel_tpl, $has_subs ); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <ul class="olo-mm-nav olo-mm-nav-right">
                    <?php foreach ( $right_items as $idx => $item ) :
                        $real_idx   = $idx + $split_point;
                        $subs       = $children[ $item->ID ] ?? [];
                        $is_current = trailingslashit( $item->url ) === $current_url;
                        $is_button  = $this->is_button_item( $item, $real_idx, $total, $s );
                        $is_mega    = ! $is_button && $this->is_mega_item( $item, $subs, $grandchildren, $s );
                        $has_panel_tpl = ! $is_button && $this->get_panel_template_id( $item->ID, $s ) > 0;
                        $has_subs   = ! $is_button && ( ! empty( $subs ) || $has_panel_tpl );
                        $li_classes = [];
                        if ( $is_current ) $li_classes[] = 'olo-mm-active';
                        $li_attr = ! empty( $li_classes ) ? ' class="' . esc_attr( implode( ' ', $li_classes ) ) . '"' : '';
                    ?>
                    <li<?php echo $li_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attribute string built with esc_attr() above ?>>
                        <?php $this->render_nav_item_content( $item, $subs, $grandchildren, $s, $show_desc, $is_button, $is_mega, $has_panel_tpl, $has_subs ); ?>
                    </li>
                    <?php endforeach; ?>
                    <?php $this->render_extra_links( $s ); ?>
                </ul>
                <?php endif; ?>

                <ul class="olo-mm-nav"<?php echo $is_split ? ' style="display:none!important"' : ''; ?>>
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
                        <li<?php echo $li_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attribute string built with esc_attr() above ?>>
                            <?php if ( $is_button ) : ?>
                                <a href="<?php echo esc_url( $item->url ); ?>" class="olo-mm-btn"<?php echo $item->target ? ' target="' . esc_attr( $item->target ) . '"' : ''; ?>>
                                    <?php echo esc_html( $item->title ); ?>
                                </a>
                            <?php else : ?>
                                <a href="<?php echo esc_url( $item->url ); ?>" data-text="<?php echo esc_attr( $item->title ); ?>"<?php echo $item->target ? ' target="' . esc_attr( $item->target ) . '"' : ''; ?><?php echo $has_subs ? ' aria-haspopup="true" aria-expanded="false"' : ''; ?>>
                                    <?php echo esc_html( $item->title ); ?>
                                    <?php $item_badge = $this->get_item_badge( $item ); if ( $item_badge !== '' ) : ?><span class="olo-mm-badge"><?php echo esc_html( $item_badge ); ?></span><?php endif; ?>
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
                    <?php if ( empty( $s['extra_links_right'] ) ) { $this->render_extra_links( $s ); } ?>
                </ul>
                <?php if ( ! empty( $s['extra_links_right'] ) ) : ?>
                <ul class="olo-mm-nav olo-mm-nav-utils"><?php $this->render_extra_links( $s ); ?></ul>
                <?php endif; ?>

                <?php // Selettore lingua referenziato (dopo gli extra link, prima della search) ?>
                <?php $this->render_referenced_lang( $s ); ?>

                <?php // Desktop search icon — search_style: expand (inline) | overlay | command ?>
                <?php if ( $search_icon ) :
                    $search_style = $s['search_style'] ?? 'expand';
                ?>
                <button class="olo-mm-search-icon olo-mm-icon-btn" data-olo-search-style="<?php echo esc_attr( $search_style ); ?>" aria-label="<?php echo esc_attr( olobuild_t( 'Cerca' ) ); ?>" type="button"><?php echo $search_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup from get_search_svg() (hardcoded strings) ?></button>
                <?php if ( $search_style === 'expand' ) : ?>
                <div class="olo-mm-search-expand">
                    <form action="<?php echo esc_url( home_url('/') ); ?>" method="get" role="search" style="display:flex;">
                        <input type="search" name="s" placeholder="<?php echo esc_attr( olobuild_t( 'Cerca...' ) ); ?>" autocomplete="off">
                    </form>
                </div>
                <?php endif; ?>
                <?php endif; ?>
                <?php $this->render_social_icons( $s, 'bar' ); ?>
            </div>

            <?php // Mobile search panel (shown under bar on mobile) ?>
            <?php if ( $mob_search ) : ?>
            <div class="olo-mm-mob-search-panel">
                <form action="<?php echo esc_url( home_url('/') ); ?>" method="get" role="search">
                    <input type="search" name="s" placeholder="<?php echo esc_attr( olobuild_t( 'Cerca...' ) ); ?>" autocomplete="off">
                    <button type="submit" aria-label="<?php echo esc_attr( olobuild_t( 'Cerca' ) ); ?>"><?php echo $search_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup from get_search_svg() (hardcoded strings) ?></button>
                </form>
            </div>
            <?php endif; ?>

            <!-- Off-Canvas Mobile -->
            <div class="olo-mm-overlay"></div>
            <nav class="olo-mm-offcanvas" aria-label="<?php echo esc_attr( olobuild_t( 'Mobile menu' ) ); ?>">
                <div class="olo-mm-oc-header">
                    <?php if ( ! empty( $s['mobile_logo'] ) ) : ?>
                        <div class="olo-mm-oc-logo"><img src="<?php echo esc_url( $s['mobile_logo'] ); ?>" alt="<?php echo esc_attr( olobuild_t( 'Logo' ) ); ?>" loading="lazy" /></div>
                    <?php else : ?>
                        <div></div>
                    <?php endif; ?>
                    <div class="olo-mm-oc-actions">
                        <?php $this->render_referenced_lang( $s, true ); ?>
                        <?php if ( $mob_search ) : ?>
                        <button class="olo-mm-oc-search-btn olo-mm-icon-btn" type="button" aria-label="<?php echo esc_attr( olobuild_t( 'Cerca' ) ); ?>"><?php echo $search_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup from get_search_svg() (hardcoded strings) ?></button>
                        <?php endif; ?>
                        <button class="olo-mm-oc-close" type="button" aria-label="<?php echo esc_attr( olobuild_t( 'Chiudi' ) ); ?>">
                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>
                </div>
                <?php if ( $mob_search ) : ?>
                <div class="olo-mm-oc-search" style="display:none">
                    <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <input type="search" name="s" placeholder="<?php echo esc_attr( olobuild_t( 'Cerca...' ) ); ?>" autocomplete="off" />
                        <button type="submit" aria-label="<?php echo esc_attr( olobuild_t( 'Cerca' ) ); ?>"><?php echo $search_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup from get_search_svg() (hardcoded strings) ?></button>
                    </form>
                </div>
                <?php endif; ?>
                <ul class="olo-mm-mob-nav">
                    <?php foreach ( $tree as $idx => $item ) :
                        if ( $this->is_button_item( $item, $idx, $total, $s ) ) continue;
                        $subs    = $children[ $item->ID ] ?? [];
                        $has_sub = ! empty( $subs );
                    ?>
                        <li<?php echo $has_sub ? ' class="olo-mm-mob-parent"' : ''; ?>>
                            <?php if ( $has_sub ) : ?>
                                <button class="olo-mm-mob-toggle" type="button" aria-expanded="false">
                                    <?php echo esc_html( $item->title ); ?>
                                    <span class="olo-mm-mob-chevron"><?php echo $toggle_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup from get_toggle_svg() (hardcoded strings + intval size) ?></span>
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
                    <?php $this->render_extra_links( $s, 'mobile' ); ?>
                    <?php
                    // Topbar hamburger menu items — merge into mobile offcanvas
                    $tb_mob_menu_id = absint( $s['topbar_left_menu_id'] ?? 0 );
                    if ( $tb_mob_menu_id && ( $s['topbar_left_content'] ?? 'none' ) === 'hamburger' ) :
                        $tb_mob_items = wp_get_nav_menu_items( $tb_mob_menu_id );
                        if ( $tb_mob_items ) : ?>
                        <li class="olo-mm-mob-sep" style="border-top:1px solid rgba(255,255,255,.1);margin:12px 0"></li>
                        <?php foreach ( $tb_mob_items as $tmi ) : ?>
                        <li><a href="<?php echo esc_url( $tmi->url ); ?>"><?php echo esc_html( $tmi->title ); ?></a></li>
                        <?php endforeach; ?>
                    <?php endif; endif; ?>
                </ul>
                <?php $this->render_social_icons( $s, 'footer' ); ?>
            </nav>

            <?php // Dropdown panel (alternative to offcanvas) ?>
            <?php $mob_style_val = $s['mobile_style'] ?? 'offcanvas'; ?>
            <?php if ( $mob_style_val === 'dropdown' || $mob_style_val === 'fullscreen' ) : ?>
            <div class="olo-mm-<?php echo $mob_style_val === 'fullscreen' ? 'fullscreen' : 'dropdown-panel'; ?><?php echo ( $mob_style_val === 'fullscreen' && $mob_numbers ) ? ' olo-mm-numbered' : ''; ?>">
                <?php if ( $mob_style_val === 'fullscreen' ) : ?>
                <div class="olo-mm-fs-header">
                    <?php if ( $logo_img ) : ?>
                    <a class="olo-mm-fs-logo" href="<?php echo $logo_link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- both URLs esc_url()-escaped at assignment above ?>"><img src="<?php echo $logo_img; ?>" alt="<?php echo esc_attr( olobuild_t( 'Logo' ) ); ?>" loading="lazy" /></a>
                    <?php elseif ( $logo_text !== '' ) : ?>
                    <a class="olo-mm-fs-logo olo-mm-logo--text" href="<?php echo $logo_link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- URL esc_url()-escaped at assignment above; size absint-clamped; color esc_attr()-escaped inline ?>"<?php echo $logo_txt_c ? ' style="color:' . esc_attr( $logo_txt_c ) . '"' : ''; ?>><?php echo $this->logo_wordmark_html( $s, $logo_text, $logo_txt_sz ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML built by logo_wordmark_html() with esc_html/esc_attr/safe_color_css ?></a>
                    <?php else : ?><div></div><?php endif; ?>
                    <button class="olo-mm-fs-close" type="button" aria-label="<?php echo esc_attr( olobuild_t( 'Chiudi' ) ); ?>" onclick="this.closest('.olo-megamenu').classList.remove('olo-mm-mob-active');this.closest('.olo-megamenu').querySelector('.olo-mm-hamburger').classList.remove('olo-mm-ham-open');document.body.style.overflow=''">
                        <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <?php if ( $mob_search ) : ?>
                <div class="olo-mm-fs-search">
                    <form action="<?php echo esc_url( home_url('/') ); ?>" method="get" role="search">
                        <input type="search" name="s" placeholder="<?php echo esc_attr( olobuild_t( 'Cerca...' ) ); ?>" autocomplete="off">
                        <button type="submit" aria-label="<?php echo esc_attr( olobuild_t( 'Cerca' ) ); ?>"><?php echo $search_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup from get_search_svg() (hardcoded strings) ?></button>
                    </form>
                </div>
                <?php endif; ?>
                <?php endif; ?>
                <nav aria-label="<?php echo esc_attr( olobuild_t( 'Mobile menu' ) ); ?>">
                    <ul class="<?php echo $mob_style_val === 'fullscreen' ? 'olo-mm-fs-nav' : 'olo-mm-dp-nav'; ?>">
                        <?php $dp_idx = 0; foreach ( $tree as $item ) :
                            if ( $this->is_button_item( $item, $dp_idx, $total, $s ) ) { $dp_idx++; continue; }
                            $dp_idx++;
                            $subs    = $children[ $item->ID ] ?? [];
                            $has_sub = ! empty( $subs );
                            $is_cur  = trailingslashit( $item->url ) === $current_url;
                            $cls     = [];
                            if ( $is_cur ) $cls[] = 'olo-mm-dp-active';
                            if ( $has_sub ) $cls[] = 'olo-mm-dp-has-children';
                            $li_cls = $cls ? ' class="' . esc_attr( implode(' ', $cls) ) . '"' : '';
                        ?>
                            <li<?php echo $li_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attribute string built with esc_attr() above ?>>
                                <?php if ( $has_sub ) : ?>
                                <div class="olo-mm-dp-item">
                                    <a href="<?php echo esc_url( $item->url ); ?>"><?php echo esc_html( $item->title ); ?></a>
                                    <button class="olo-mm-dp-chevron" type="button" aria-label="<?php echo esc_attr( olobuild_t( 'Espandi' ) ); ?>" aria-expanded="false"><?php echo $toggle_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup from get_toggle_svg() (hardcoded strings + intval size) ?></button>
                                </div>
                                <ul class="olo-mm-dp-sub">
                                    <?php foreach ( $subs as $sub ) :
                                        $gc = $grandchildren[ $sub->ID ] ?? [];
                                        $sub_cur = trailingslashit( $sub->url ) === $current_url;
                                        $sub_cls = [];
                                        if ( $sub_cur ) $sub_cls[] = 'olo-mm-dp-active';
                                        if ( ! empty($gc) ) $sub_cls[] = 'olo-mm-dp-has-children';
                                        $sc = $sub_cls ? ' class="' . esc_attr(implode(' ',$sub_cls)) . '"' : '';
                                    ?>
                                        <li<?php echo $sc; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attribute string built with esc_attr() above ?>>
                                            <?php if ( ! empty($gc) ) : ?>
                                            <div class="olo-mm-dp-item">
                                                <a href="<?php echo esc_url( $sub->url ); ?>"><?php echo esc_html( $sub->title ); ?></a>
                                                <button class="olo-mm-dp-chevron" type="button" aria-label="<?php echo esc_attr( olobuild_t( 'Espandi sottomenu' ) ); ?>" aria-expanded="false"><?php echo $toggle_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup from get_toggle_svg() (hardcoded strings + intval size) ?></button>
                                            </div>
                                            <ul class="olo-mm-dp-sub">
                                                <?php foreach ( $gc as $gci ) : ?>
                                                <li><a href="<?php echo esc_url( $gci->url ); ?>"><?php echo esc_html( $gci->title ); ?></a></li>
                                                <?php endforeach; ?>
                                            </ul>
                                            <?php else : ?>
                                            <a href="<?php echo esc_url( $sub->url ); ?>"><?php echo esc_html( $sub->title ); ?></a>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php else : ?>
                                <a href="<?php echo esc_url( $item->url ); ?>"><?php echo esc_html( $item->title ); ?></a>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                        <?php $this->render_extra_links( $s, 'mobile' ); ?>
                        <?php
                        // Topbar hamburger menu items — merge into mobile panel
                        $tb_mob2_menu_id = absint( $s['topbar_left_menu_id'] ?? 0 );
                        if ( $tb_mob2_menu_id && ( $s['topbar_left_content'] ?? 'none' ) === 'hamburger' ) :
                            $tb_mob2_items = wp_get_nav_menu_items( $tb_mob2_menu_id );
                            if ( $tb_mob2_items ) : ?>
                            <li class="olo-mm-mob-sep" style="border-top:1px solid rgba(255,255,255,.1);margin:12px 0"></li>
                            <?php foreach ( $tb_mob2_items as $tmi2 ) : ?>
                            <li><a href="<?php echo esc_url( $tmi2->url ); ?>"><?php echo esc_html( $tmi2->title ); ?></a></li>
                            <?php endforeach; ?>
                        <?php endif; endif; ?>
                    </ul>
                    <?php $this->render_social_icons( $s, 'footer' ); ?>
                </nav>
                <?php if ( $mob_style_val === 'fullscreen' && ( $mob_foot_text !== '' || $mob_foot_cta !== '' ) ) : ?>
                <div class="olo-mm-fs-foot">
                    <?php if ( $mob_foot_text !== '' ) : ?><span><?php echo esc_html( $mob_foot_text ); ?></span><?php endif; ?>
                    <?php if ( $mob_foot_cta !== '' ) : ?><a href="<?php echo esc_url( $mob_foot_url ?: '#' ); ?>"><?php echo esc_html( $mob_foot_cta ); ?> &rarr;</a><?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php // === E1: Overlay ricerca / command palette === ?>
            <?php
            // L'overlay serve anche alla lente mobile quando "Ricerca mobile a
            // tutta pagina" è attiva, indipendentemente dallo stile desktop.
            $mob_search_overlay = ! empty( $s['mobile_search'] ) && ! empty( $s['mobile_search_overlay'] );
            ?>
            <?php if ( ( ! empty( $s['search_icon'] ) && in_array( ( $s['search_style'] ?? 'expand' ), [ 'overlay', 'command' ], true ) ) || $mob_search_overlay ) :
                $is_cmd = ( ( $s['search_style'] ?? 'expand' ) === 'command' ) && ! empty( $s['search_icon'] );
            ?>
            <div class="olo-mm-search-overlay<?php echo $is_cmd ? ' olo-mm-search-overlay--cmd' : ''; ?>" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr( olobuild_t( 'Cerca' ) ); ?>" hidden>
                <div class="olo-mm-search-overlay-backdrop" data-olo-search-close></div>
                <div class="olo-mm-search-box">
                    <form class="olo-mm-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search">
                        <span class="olo-mm-search-box-icon"><?php echo $search_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup from get_search_svg() (hardcoded strings) ?></span>
                        <input type="search" name="s" class="olo-mm-search-input" placeholder="<?php echo esc_attr( $is_cmd ? olobuild_t( 'Cerca o digita…' ) : olobuild_t( 'Cerca nel sito…' ) ); ?>" autocomplete="off" />
                        <kbd class="olo-mm-search-kbd"><?php echo $is_cmd ? esc_html( '⌘K' ) : esc_html( 'ESC' ); ?></kbd>
                    </form>
                    <div class="olo-mm-search-results" aria-live="polite"></div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }

    private function render_mega_panel( $subs, $grandchildren, $s, $show_desc ) {
        ?>
        <div class="olo-mm-panel">
            <div class="olo-mm-grid">
                <?php foreach ( $subs as $sub ) :
                    $gc = $grandchildren[ $sub->ID ] ?? [];
                    // E2 — colonna "promo/immagine": voce-colonna con classe CSS `mega-promo`.
                    $sub_classes = is_array( $sub->classes ) ? $sub->classes : [];
                    if ( in_array( 'mega-promo', $sub_classes, true ) ) {
                        $this->render_promo_column( $sub );
                        continue;
                    }
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

    /**
     * E2 — Colonna promo nel mega panel. La voce-colonna (livello 2) con classe CSS
     * `mega-promo` diventa una card visuale: immagine (featured image della pagina
     * collegata o URL nella descrizione, altrimenti placeholder a gradiente token),
     * titolo, descrizione e CTA. Nessuna chiave salvata nuova.
     */
    private function render_promo_column( $sub ) {
        $img  = '';
        if ( ! empty( $sub->object_id ) ) {
            $thumb = get_the_post_thumbnail_url( (int) $sub->object_id, 'medium_large' );
            if ( $thumb ) $img = $thumb;
        }
        $desc = trim( (string) ( $sub->description ?? '' ) );
        // Descrizione = URL immagine → usala come media (non come testo).
        if ( ! $img && $desc !== '' && preg_match( '#^https?://#i', $desc ) ) {
            $img  = $desc;
            $desc = '';
        }
        $media_cls   = 'olo-mm-promo-media' . ( $img ? '' : ' olo-mm-promo-media--placeholder' );
        $media_style = $img ? ' style="background-image:url(' . esc_url( $img ) . ')"' : '';
        ?>
        <div class="olo-mm-col olo-mm-col-promo">
            <a class="olo-mm-promo" href="<?php echo esc_url( $sub->url ?: '#' ); ?>"<?php echo $sub->target ? ' target="' . esc_attr( $sub->target ) . '"' : ''; ?>>
                <span class="<?php echo esc_attr( $media_cls ); ?>"<?php echo $media_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attribute string built above from fixed literals + esc_url() ?>></span>
                <span class="olo-mm-promo-body">
                    <span class="olo-mm-promo-title"><?php echo esc_html( $sub->title ); ?></span>
                    <?php if ( $desc !== '' ) : ?>
                        <span class="olo-mm-promo-desc"><?php echo esc_html( $desc ); ?></span>
                    <?php endif; ?>
                    <span class="olo-mm-promo-cta"><?php echo esc_html( olobuild_t( 'Scopri di più' ) ); ?> &rarr;</span>
                </span>
            </a>
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

    /* ─── Timecode + hairline di progresso (scroll, rAF per-istanza) ─── */

    private function render_scroll_fx_js( $s, $uid ) {
        $has_tc = ! empty( $s['show_timecode'] );
        $has_pg = ! empty( $s['scroll_progress'] );
        if ( ! $has_tc && ! $has_pg ) {
            return;
        }
        // Il type 'number' preserva '' → default 90.
        $dur_raw = $s['timecode_duration'] ?? 90;
        $dur     = ( $dur_raw === '' || $dur_raw === null ) ? 90 : max( 1, min( 3600, absint( $dur_raw ) ) );
        // NB: niente `&&` negli script inline (WP lo encoda) → if separati/annidati.
        ?>
        <script>(function(){
            var root = document.querySelector('.<?php echo esc_js( $uid ); ?>');
            if (!root) { return; }
            var tcs = root.querySelectorAll('.olo-mm-tc');
            var dur = <?php echo (int) $dur; ?>;
            var fps = 25;
            var tick = false;
            function pad2(n) { return (n < 10 ? '0' : '') + n; }
            function upd() {
                tick = false;
                var max = document.documentElement.scrollHeight - window.innerHeight;
                var p = 0;
                if (max > 0) { p = Math.max(0, Math.min(1, (window.scrollY || 0) / max)); }
                root.style.setProperty('--olo-mm-p', p.toFixed(4));
                if (tcs.length) {
                    var fr = Math.round(p * dur * fps);
                    var sec = Math.floor(fr / fps);
                    var f = fr % fps;
                    var txt = 'TC 00:' + pad2(Math.floor(sec / 60)) + ':' + pad2(sec % 60) + ':' + pad2(f);
                    tcs.forEach(function (el) { el.textContent = txt; });
                }
            }
            window.addEventListener('scroll', function () {
                if (!tick) { tick = true; requestAnimationFrame(upd); }
            }, { passive: true });
            window.addEventListener('resize', upd);
            upd();
        })();</script>
        <?php
    }

    private function render_js( $s, $uid ) {
        $mode       = esc_js( $s['header_mode'] ?? 'overlay' );
        $sticky     = ! empty( $s['sticky'] ) ? 'true' : 'false';
        $show_on_up = ! empty( $s['sticky_show_on_up'] ) ? 'true' : 'false';
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline JS below only echoes values escaped above or inline (esc_js/esc_url_raw), 'true'/'false' and enum literals from fixed ternaries, and intval() integers.
        ?>
        <script>
        (function(){
            var uid = "<?php echo esc_js( $uid ); ?>";
            var root = document.querySelector("." + uid);
            if (!root) return;

            /* ── Hover Intent Desktop ── */
            var timers = {};
            var isFull = <?php echo $s['panel_width'] === 'full' ? 'true' : 'false'; ?>;
            var navItems = root.querySelectorAll(".olo-mm-nav > li, .olo-mm-nav-left > li, .olo-mm-nav-right > li");

            var panelSize = "<?php echo esc_js( $s['panel_size'] ?? 'auto' ); ?>";
            var panelOrigin = "<?php echo esc_js( $s['panel_origin'] ?? 'nav' ); ?>";

            function posPanel(li) {
                var vw = document.documentElement.clientWidth;
                var liRect = li.getBoundingClientRect();
                var isOriginSection = (panelOrigin === "section");

                /* Trova il panel (qualsiasi tipo) */
                var panel = li.querySelector(".olo-mm-panel-tpl") || li.querySelector(".olo-mm-panel") || li.querySelector(".olo-mm-dropdown");
                if (!panel) return;

                var isTpl = panel.classList.contains("olo-mm-panel-tpl");
                var isMega = panel.classList.contains("olo-mm-panel");
                var isDrop = panel.classList.contains("olo-mm-dropdown");

                /* ── Origin section: tutti i panel diventano fixed sotto l'header ── */
                if (isOriginSection) {
                    if (headerEl) { calcSectionBottom(); }
                    panel.style.position = "fixed";
                    panel.style.top = sectionBottom + "px";

                    var isFullMega = false;
                    if (isFull) { if (isMega) { isFullMega = true; } }
                    if (isTpl || isFullMega) {
                        /* Template o full-width: tutta la larghezza viewport */
                        panel.style.left = "0";
                        panel.style.width = vw + "px";
                    } else if (panelSize === "viewport") {
                        panel.style.left = "0";
                        panel.style.width = vw + "px";
                    } else if (panelSize === "container") {
                        var sect = root.closest("section, .olo-section, .wp-block-group");
                        if (sect) {
                            var sRect = sect.getBoundingClientRect();
                            panel.style.left = sRect.left + "px";
                            panel.style.width = sRect.width + "px";
                        }
                    } else if (panelSize === "section") {
                        var bar = root.querySelector(".olo-mm-bar");
                        if (bar) {
                            var barRect = bar.getBoundingClientRect();
                            panel.style.left = barRect.left + "px";
                            panel.style.width = barRect.width + "px";
                        }
                    } else if (panelSize === "centered") {
                        if (isMega) {
                            /* Centrato sotto la voce — mostra temporaneamente per misurare */
                            panel.style.removeProperty("width");
                            panel.style.visibility = "hidden";
                            panel.style.display = "block";
                            var pw = panel.offsetWidth || 600;
                            panel.style.removeProperty("visibility");
                            panel.style.removeProperty("display");
                            var idealLeft = liRect.left + liRect.width / 2 - pw / 2;
                            if (idealLeft < 8) idealLeft = 8;
                            if (idealLeft + pw > vw - 8) idealLeft = vw - 8 - pw;
                            panel.style.left = idealLeft + "px";
                            panel.style.transform = "none";
                        } else {
                            /* dropdown centrato sotto il li */
                            panel.style.removeProperty("width");
                            panel.style.visibility = "hidden";
                            panel.style.display = "block";
                            panel.style.left = liRect.left + "px";
                            void panel.offsetWidth;
                            var pRect0 = panel.getBoundingClientRect();
                            panel.style.removeProperty("visibility");
                            panel.style.removeProperty("display");
                            if (pRect0.right > vw - 8) { panel.style.left = (vw - 8 - pRect0.width) + "px"; }
                            if (pRect0.left < 8) { panel.style.left = "8px"; }
                        }
                    } else {
                        /* auto / dropdown: allineato sotto il li */
                        panel.style.removeProperty("width");
                        panel.style.visibility = "hidden";
                        panel.style.display = "block";
                        panel.style.left = liRect.left + "px";
                        /* Clamp al viewport */
                        void panel.offsetWidth;
                        var pRect = panel.getBoundingClientRect();
                        panel.style.removeProperty("visibility");
                        panel.style.removeProperty("display");
                        if (pRect.right > vw - 8) {
                            panel.style.left = (vw - 8 - pRect.width) + "px";
                        }
                        if (pRect.left < 8) {
                            panel.style.left = "8px";
                        }
                    }
                    return;
                }

                /* ── Origin nav (default): panel relativi al li padre ── */

                /* Template panel o full-width mega */
                var isFullMega2 = false;
                if (isFull) { if (isMega) { isFullMega2 = true; } }
                if (isTpl || isFullMega2) {
                    panel.style.left = (-liRect.left) + "px";
                    panel.style.width = vw + "px";
                    return;
                }

                if (panelSize === "viewport") {
                    panel.style.left = (-liRect.left) + "px";
                    panel.style.width = vw + "px";
                    return;
                }
                if (panelSize === "container") {
                    var sect2 = root.closest("section, .olo-section, .wp-block-group");
                    if (sect2) {
                        var sRect2 = sect2.getBoundingClientRect();
                        panel.style.left = (sRect2.left - liRect.left) + "px";
                        panel.style.width = sRect2.width + "px";
                    }
                    return;
                }
                if (panelSize === "section") {
                    var bar2 = root.querySelector(".olo-mm-bar");
                    if (bar2) {
                        var barRect2 = bar2.getBoundingClientRect();
                        panel.style.left = (barRect2.left - liRect.left) + "px";
                        panel.style.width = barRect2.width + "px";
                    }
                    return;
                }
                if (panelSize === "centered") {
                    if (isMega) {
                        panel.style.removeProperty("left");
                        panel.style.marginLeft = "0";
                        var pRect3 = panel.getBoundingClientRect();
                        if (pRect3.left < 8) {
                            panel.style.marginLeft = (8 - pRect3.left) + "px";
                        } else if (pRect3.right > vw - 8) {
                            panel.style.marginLeft = (vw - 8 - pRect3.right) + "px";
                        }
                        return;
                    }
                }
                /* auto: mega panels si centrano sotto il li, dropdown allineati a sinistra */
                if (isMega) {
                    panel.style.left = "0";
                    void panel.offsetWidth;
                    var pw4 = panel.offsetWidth;
                    var liCenter = liRect.left + liRect.width / 2;
                    var idealL = liCenter - pw4 / 2 - liRect.left;
                    panel.style.left = idealL + "px";
                    var pRect4 = panel.getBoundingClientRect();
                    if (pRect4.right > vw - 8) {
                        panel.style.left = (idealL - (pRect4.right - vw + 8)) + "px";
                    }
                    pRect4 = panel.getBoundingClientRect();
                    if (pRect4.left < 8) {
                        panel.style.left = (parseFloat(panel.style.left) + (8 - pRect4.left)) + "px";
                    }
                } else {
                    /* dropdown semplice: allineato al li */
                    panel.style.left = "0";
                    var pRect5 = panel.getBoundingClientRect();
                    if (pRect5.right > vw - 8) {
                        panel.style.left = (-(pRect5.right - vw + 8)) + "px";
                    }
                    if (pRect5.left < 8) {
                        panel.style.left = (-pRect5.left + 8) + "px";
                    }
                }
            }

            /* Panel origin: section — posiziona TUTTI i panel sotto la sezione header */
            var sectionBottom = 0;
            var headerEl = null;
            if (panelOrigin === "section") {
                headerEl = root.closest("header, section, .olo-section");
                if (headerEl) {
                    function calcSectionBottom() {
                        sectionBottom = headerEl.getBoundingClientRect().bottom;
                    }
                    calcSectionBottom();
                    window.addEventListener("scroll", calcSectionBottom, { passive: true });
                    window.addEventListener("resize", calcSectionBottom);
                }
            }

            /* aria-expanded vive sul link toggle (a[aria-haspopup]), non sul <li> */
            function mmSetExp(li, val) {
                var t = li.querySelector("a[aria-haspopup]");
                if (t) t.setAttribute("aria-expanded", val);
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
                            mmSetExp(o, "false");
                        }
                    });
                    // Position full-width panels before showing
                    posPanel(li);
                    li.classList.add("olo-mm-open");
                    mmSetExp(li, "true");
                });
                li.addEventListener("mouseleave", function() {
                    timers._last = setTimeout(function() {
                        li.classList.remove("olo-mm-open");
                        mmSetExp(li, "false");
                    }, 200);
                });
            });

            /* ── Keyboard: Escape ── */
            document.addEventListener("keydown", function(e) {
                if (e.key === "Escape") {
                    navItems.forEach(function(li) {
                        li.classList.remove("olo-mm-open");
                        mmSetExp(li, "false");
                    });
                    closeMobile();
                }
            });

            /* ── Mobile Menu ── */
            var hamburger  = root.querySelector(".olo-mm-hamburger");
            var overlay    = root.querySelector(".olo-mm-overlay");
            var offcanvas  = root.querySelector(".olo-mm-offcanvas");
            var mobileStyle = "<?php echo esc_js( $s['mobile_style'] ?? 'offcanvas' ); ?>";

            function openMobile() {
                root.classList.remove("olo-mm-search-active");
                if (mobileStyle === "offcanvas") {
                    if (overlay) overlay.classList.add("olo-mm-vis");
                    if (offcanvas) offcanvas.classList.add("olo-mm-vis");
                } else {
                    root.classList.add("olo-mm-mob-active");
                }
                hamburger.classList.add("olo-mm-ham-open");
                hamburger.setAttribute("aria-expanded", "true");
                document.body.style.overflow = "hidden";
            }
            function closeMobile() {
                var osp = root.querySelector(".olo-mm-oc-search");
                if (osp) osp.classList.remove("olo-mm-oc-search-vis");
                if (mobileStyle === "offcanvas") {
                    if (overlay) overlay.classList.remove("olo-mm-vis");
                    if (offcanvas) offcanvas.classList.remove("olo-mm-vis");
                } else {
                    root.classList.remove("olo-mm-mob-active");
                }
                hamburger.classList.remove("olo-mm-ham-open");
                hamburger.setAttribute("aria-expanded", "false");
                document.body.style.overflow = "";
            }
            function isMobileOpen() {
                if (mobileStyle === "offcanvas") {
                    return offcanvas ? offcanvas.classList.contains("olo-mm-vis") : false;
                }
                return root.classList.contains("olo-mm-mob-active");
            }
            if (hamburger) {
                hamburger.addEventListener("click", function() {
                    isMobileOpen() ? closeMobile() : openMobile();
                });
            }
            if (overlay) overlay.addEventListener("click", closeMobile);
            var ocClose = root.querySelector(".olo-mm-oc-close");
            if (ocClose) ocClose.addEventListener("click", closeMobile);

            /* ── Accordion Mobile (offcanvas) ── */
            root.querySelectorAll(".olo-mm-mob-toggle").forEach(function(btn) {
                btn.addEventListener("click", function() {
                    var open = btn.parentElement.classList.toggle("olo-mm-mob-open");
                    btn.setAttribute("aria-expanded", open ? "true" : "false");
                });
            });

            /* ── Accordion Mobile (dropdown/fullscreen) ── */
            root.querySelectorAll(".olo-mm-dp-chevron").forEach(function(btn) {
                btn.addEventListener("click", function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var li = btn.closest("li");
                    if (li) {
                        var open = li.classList.toggle("olo-mm-dp-sub-open");
                        btn.setAttribute("aria-expanded", open ? "true" : "false");
                    }
                });
            });

            /* Parent items: click on text toggles submenu instead of navigating */
            root.querySelectorAll(".olo-mm-dp-item > a").forEach(function(link) {
                var li = link.closest("li");
                if (li) {
                    link.addEventListener("click", function(e) {
                        e.preventDefault();
                        var open = li.classList.toggle("olo-mm-dp-sub-open");
                        var chev = li.querySelector(".olo-mm-dp-chevron");
                        if (chev) chev.setAttribute("aria-expanded", open ? "true" : "false");
                    });
                }
            });

            /* Close on link click (only leaf links, not parent items) */
            root.querySelectorAll(".olo-mm-dp-nav a, .olo-mm-fs-nav a").forEach(function(link) {
                if (link.closest(".olo-mm-dp-item")) return;
                link.addEventListener("click", function() { closeMobile(); });
            });

            /* ── Desktop search toggle ── */
            var searchBtn = root.querySelector(".olo-mm-search-icon");
            var searchExp = root.querySelector(".olo-mm-search-expand");
            if (searchBtn) {
                searchBtn.addEventListener("click", function() {
                    if (searchExp) {
                        searchExp.classList.toggle("olo-mm-search-open");
                        var inp = searchExp.querySelector("input");
                        if (inp) { if (searchExp.classList.contains("olo-mm-search-open")) { setTimeout(function(){ inp.focus(); }, 300); } }
                    }
                });
            }

            /* ── E1: Search overlay / command palette (search_style overlay|command) ── */
            var soOverlay = root.querySelector(".olo-mm-search-overlay");
            if (soOverlay) {
                var soInput = soOverlay.querySelector(".olo-mm-search-input");
                var soResults = soOverlay.querySelector(".olo-mm-search-results");
                var soIsCmd = soOverlay.classList.contains("olo-mm-search-overlay--cmd");
                var soTimer = null;
                var soRestBase = "<?php echo esc_url_raw( rest_url( 'wp/v2/search' ) ); ?>";
                var soAmp = String.fromCharCode(38); /* evita il letterale & negli script inline */
                var soOpen = function() {
                    soOverlay.removeAttribute("hidden");
                    document.body.style.overflow = "hidden";
                    setTimeout(function(){ if (soInput) soInput.focus(); }, 30);
                };
                var soClose = function() {
                    soOverlay.setAttribute("hidden", "");
                    document.body.style.overflow = "";
                };
                /* La lente DESKTOP apre l'overlay solo se lo stile desktop è
                   overlay/command: con "expand" l'overlay può esistere solo per
                   la ricerca mobile a tutta pagina e il click desktop deve
                   continuare a espandere inline. */
                var soDesktop = searchBtn ? searchBtn.getAttribute("data-olo-search-style") !== "expand" : false;
                if (searchBtn && soDesktop) {
                    searchBtn.addEventListener("click", function(e){ e.preventDefault(); soOpen(); });
                }
                soOverlay.querySelectorAll("[data-olo-search-close]").forEach(function(el){
                    el.addEventListener("click", soClose);
                });
                document.addEventListener("keydown", function(e){
                    var openKey = false;
                    if (e.key === "/" && soDesktop) openKey = true;
                    if (soIsCmd) {
                        if (e.metaKey || e.ctrlKey) {
                            if (e.key === "k" || e.key === "K") openKey = true;
                        }
                    }
                    if (openKey) {
                        var tag = "";
                        if (e.target) { if (e.target.tagName) tag = e.target.tagName.toLowerCase(); }
                        if (tag !== "input") {
                            if (tag !== "textarea") { e.preventDefault(); soOpen(); }
                        }
                    }
                    if (e.key === "Escape") {
                        if (soOverlay.getAttribute("hidden") === null) soClose();
                    }
                });
                if (soInput) {
                    soInput.addEventListener("input", function(){
                        var q = soInput.value.trim();
                        if (soTimer) clearTimeout(soTimer);
                        if (q.length > 1) {
                            soTimer = setTimeout(function(){
                                var soSep = (soRestBase.indexOf("?") === -1) ? "?" : soAmp;
                                var soUrl = soRestBase + soSep + "search=" + encodeURIComponent(q) + soAmp + "per_page=6";
                                fetch(soUrl, { headers: { "Accept": "application/json" } })
                                    .then(function(r){ return r.json(); })
                                    .then(function(items){
                                        if (!soResults) return;
                                        soResults.innerHTML = "";
                                        if (!items) return;
                                        if (!items.length) {
                                            var empty = document.createElement("div");
                                            empty.className = "olo-mm-search-empty";
                                            empty.textContent = "<?php echo esc_js( olobuild_t( 'Nessun risultato' ) ); ?>";
                                            soResults.appendChild(empty);
                                            return;
                                        }
                                        items.forEach(function(it){
                                            var a = document.createElement("a");
                                            a.className = "olo-mm-search-result";
                                            a.href = it.url;
                                            a.textContent = it.title || "";
                                            soResults.appendChild(a);
                                        });
                                    })
                                    .catch(function(){});
                            }, 220);
                        } else {
                            if (soResults) soResults.innerHTML = "";
                        }
                    });
                }
            }

            /* ── Mobile search toggle (bar) ── */
            var mobSearchBtn = root.querySelector(".olo-mm-mobile-search");
            var mobSearchOverlay = <?php echo ( ! empty( $s['mobile_search'] ) && ! empty( $s['mobile_search_overlay'] ) ) ? 'true' : 'false'; ?>;
            if (mobSearchBtn) {
                mobSearchBtn.addEventListener("click", function() {
                    /* "Ricerca mobile a tutta pagina": riusa l'overlay E1.
                       soOpen è una var hoisted di questo scope, definita solo
                       quando l'overlay è presente nel markup. */
                    if (mobSearchOverlay && typeof soOpen === "function") {
                        closeMobile();
                        soOpen();
                        return;
                    }
                    var wasOpen = root.classList.contains("olo-mm-search-active");
                    if (wasOpen) {
                        root.classList.remove("olo-mm-search-active");
                    } else {
                        closeMobile();
                        root.classList.add("olo-mm-search-active");
                        var inp = root.querySelector(".olo-mm-mob-search-panel input");
                        if (inp) setTimeout(function(){ inp.focus(); }, 300);
                    }
                });
            }

            /* ── Offcanvas search toggle ── */
            var ocSearchBtn = root.querySelector(".olo-mm-oc-search-btn");
            var ocSearchPanel = root.querySelector(".olo-mm-oc-search");
            if (ocSearchBtn) {
                ocSearchBtn.addEventListener("click", function() {
                    if (ocSearchPanel) {
                        var vis = ocSearchPanel.classList.contains("olo-mm-oc-search-vis");
                        if (vis) {
                            ocSearchPanel.classList.remove("olo-mm-oc-search-vis");
                        } else {
                            ocSearchPanel.classList.add("olo-mm-oc-search-vis");
                            var inp = ocSearchPanel.querySelector("input");
                            if (inp) setTimeout(function(){ inp.focus(); }, 100);
                        }
                    }
                });
            }

            /* ── Header Mode + Sticky ── */
            var headerMode = "<?php echo $mode; ?>";
            var stickyEnabled = <?php echo $sticky; ?>;
            var showOnUp = <?php echo $show_on_up; ?>;
            var mmBreakpoint = <?php echo intval( $s['mobile_breakpoint'] ) ?: 1024; ?>;
            var header = document.querySelector("header.olo-site-header");
            if (header) {
                header.classList.remove("olo-header-overlay", "olo-header-classic");
                header.classList.add("olo-header-" + headerMode);
                function isDesktop() { return window.innerWidth > mmBreakpoint; }
                if (stickyEnabled) {
                    if (isDesktop()) {
                        header.style.position = "sticky";
                        header.style.top = "0";
                        header.style.zIndex = "1000";
                        header.classList.add("olo-header-sticky");
                    }
                }
                if (stickyEnabled || showOnUp) {
                    var lastY = 0, hidden = false, ticking = false;
                    var isClassic = (headerMode === "classic");
                    var onUpFixed = false;
                    var headerH = header.offsetHeight;
                    if (showOnUp) {
                        header.style.transition = "transform 0.3s ease, top 0.3s ease";
                    }
                    window.addEventListener("resize", function() {
                        if (!isDesktop()) {
                            header.style.position = "";
                            header.style.top = "";
                            header.style.zIndex = "";
                            header.style.transform = "";
                            header.classList.remove("olo-header-sticky");
                            hidden = false;
                            onUpFixed = false;
                        } else {
                            if (stickyEnabled) {
                                header.style.position = "sticky";
                                header.style.top = "0";
                                header.style.zIndex = "1000";
                                header.classList.add("olo-header-sticky");
                            }
                        }
                    });
                    window.addEventListener("scroll", function() {
                        if (ticking) return;
                        if (!isDesktop()) return;
                        ticking = true;
                        requestAnimationFrame(function() {
                            ticking = false;
                            var y = window.pageYOffset || document.documentElement.scrollTop;
                            if (stickyEnabled) {
                                if (y > 10) {
                                    header.classList.add("olo-header-sticky");
                                } else {
                                    header.classList.remove("olo-header-sticky");
                                    if (hidden) { header.style.top = ""; header.style.transform = ""; hidden = false; }
                                }
                            }
                            if (showOnUp) {
                                var delta = y - lastY;
                                if (stickyEnabled) {
                                    if (delta > 5) {
                                        if (y > 300) {
                                            if (!hidden) {
                                                if (isClassic) {
                                                    header.style.top = (-headerH - 10) + "px";
                                                } else {
                                                    header.style.transform = "translateY(-100%)";
                                                }
                                                hidden = true;
                                            }
                                        }
                                    } else if (delta < -5) {
                                        if (hidden) {
                                            if (isClassic) {
                                                header.style.top = "0";
                                            } else {
                                                header.style.transform = "";
                                            }
                                            hidden = false;
                                        }
                                    }
                                } else {
                                    if (y < headerH) {
                                        if (onUpFixed) {
                                            header.style.position = "";
                                            header.style.top = "";
                                            header.style.zIndex = "";
                                            header.style.transform = "";
                                            header.classList.remove("olo-header-sticky");
                                            onUpFixed = false;
                                            hidden = false;
                                        }
                                    } else if (delta > 5) {
                                        if (y > 300) {
                                            if (onUpFixed) {
                                                if (!hidden) {
                                                    header.style.transform = "translateY(-100%)";
                                                    hidden = true;
                                                    setTimeout(function() {
                                                        header.style.transition = "none";
                                                        header.style.position = "";
                                                        header.style.top = "";
                                                        header.style.zIndex = "";
                                                        header.style.transform = "";
                                                        header.classList.remove("olo-header-sticky");
                                                        onUpFixed = false;
                                                        hidden = false;
                                                        setTimeout(function() {
                                                            header.style.transition = "transform 0.3s ease, top 0.3s ease";
                                                        }, 50);
                                                    }, 300);
                                                }
                                            }
                                        }
                                    } else if (delta < -5) {
                                        if (y > headerH) {
                                            if (!onUpFixed) {
                                                header.style.transition = "none";
                                                header.style.position = "fixed";
                                                header.style.top = "0";
                                                header.style.left = "0";
                                                header.style.right = "0";
                                                header.style.zIndex = "1000";
                                                header.style.transform = "translateY(-100%)";
                                                header.classList.add("olo-header-sticky");
                                                onUpFixed = true;
                                                hidden = true;
                                                void header.offsetWidth;
                                                header.style.transition = "transform 0.3s ease";
                                                header.style.transform = "translateY(0)";
                                                hidden = false;
                                            }
                                        }
                                    }
                                }
                            }
                            lastY = y;
                        });
                    }, { passive: true });
                }
            }
            /* ── Magnetic hover effect ── */
            <?php if ( ( $s['hover_effect'] ?? 'none' ) === 'magnetic' ) : ?>
            root.querySelectorAll(".olo-mm-nav > li > a, .olo-mm-nav-left > li > a, .olo-mm-nav-right > li > a").forEach(function(link) {
                link.addEventListener("mousemove", function(e) {
                    var rect = link.getBoundingClientRect();
                    var mx = ((e.clientX - rect.left) / rect.width - 0.5) * 6;
                    var my = ((e.clientY - rect.top) / rect.height - 0.5) * 4;
                    link.style.setProperty("--mm-mx", mx + "px");
                    link.style.setProperty("--mm-my", my + "px");
                });
                link.addEventListener("mouseleave", function() {
                    link.style.setProperty("--mm-mx", "0");
                    link.style.setProperty("--mm-my", "0");
                });
            });
            <?php endif; ?>

            /* ── Circular fullscreen animation: calc burger position ── */
            <?php if ( ( $s['fullscreen_animation'] ?? 'fade' ) === 'circular' ) : ?>
            if (hamburger) {
                var fsPanel = root.querySelector(".olo-mm-fullscreen") || root.querySelector(".olo-mm-offcanvas");
                if (fsPanel) {
                    function updateBurgerPos() {
                        var hRect = hamburger.getBoundingClientRect();
                        var bx = ((hRect.left + hRect.width / 2) / window.innerWidth * 100).toFixed(1);
                        var by = ((hRect.top + hRect.height / 2) / window.innerHeight * 100).toFixed(1);
                        fsPanel.style.setProperty("--burger-x", bx + "%");
                        fsPanel.style.setProperty("--burger-y", by + "%");
                    }
                    hamburger.addEventListener("click", updateBurgerPos);
                    window.addEventListener("resize", updateBurgerPos);
                    updateBurgerPos();
                }
            }
            <?php endif; ?>
        })();
        </script>
        <?php
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
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
            <?php echo do_shortcode( '[olo_template id="' . absint( $tpl_id ) . '"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- template HTML rendered by the [olo_template] shortcode; tile renderers escape their own output ?>
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

    private function render_nav_item_content( $item, $subs, $grandchildren, $s, $show_desc, $is_button, $is_mega, $has_panel_tpl, $has_subs ) {
        if ( $is_button ) : ?>
            <a href="<?php echo esc_url( $item->url ); ?>" class="olo-mm-btn"<?php echo $item->target ? ' target="' . esc_attr( $item->target ) . '"' : ''; ?>>
                <?php echo esc_html( $item->title ); ?>
            </a>
        <?php else : ?>
            <a href="<?php echo esc_url( $item->url ); ?>" data-text="<?php echo esc_attr( $item->title ); ?>"<?php echo $item->target ? ' target="' . esc_attr( $item->target ) . '"' : ''; ?><?php echo $has_subs ? ' aria-haspopup="true" aria-expanded="false"' : ''; ?>>
                <?php echo esc_html( $item->title ); ?>
                <?php $item_badge = $this->get_item_badge( $item ); if ( $item_badge !== '' ) : ?><span class="olo-mm-badge"><?php echo esc_html( $item_badge ); ?></span><?php endif; ?>
                <?php if ( $has_subs ) : ?>
                    <svg class="olo-mm-chevron" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                <?php endif; ?>
            </a>
            <?php
                $panel_tpl_id = $this->get_panel_template_id( $item->ID, $s );
                if ( $panel_tpl_id ) :
                    $this->render_template_panel( $panel_tpl_id );
                elseif ( $is_mega && ! empty( $subs ) ) :
                    $this->render_mega_panel( $subs, $grandchildren, $s, $show_desc );
                elseif ( $has_subs ) :
                    $this->render_simple_dropdown( $subs );
                endif;
            ?>
        <?php endif;
    }

    /**
     * E3 — Badge per-voce. Una classe CSS sulla voce del menu WP nella forma
     * `badge-<label>` (es. `badge-new`, `badge-hot`, `badge-sale`) genera un badge.
     * Il label è derivato dal suffisso (trattini → spazi, maiuscolo): badge-new → NEW.
     * Nessuna chiave salvata nuova.
     */
    private function get_item_badge( $item ) {
        $classes = is_array( $item->classes ) ? $item->classes : [];
        foreach ( $classes as $c ) {
            if ( strpos( (string) $c, 'badge-' ) === 0 ) {
                $label = substr( (string) $c, 6 );
                if ( $label !== '' ) {
                    return strtoupper( str_replace( '-', ' ', $label ) );
                }
            }
        }
        return '';
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

// Aggiorna il conteggio "(n)" delle voci-carrello del megamenu quando WooCommerce
// aggiunge al carrello via ajax (cart fragments) — nessun reload necessario.
if ( ! function_exists( 'olobuild_megamenu_cart_fragment' ) ) {
    function olobuild_megamenu_cart_fragment( $fragments ) {
        $count = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
        $fragments['.olo-mm-cart-count'] = '<span class="olo-mm-cart-count">(' . (int) $count . ')</span>';
        return $fragments;
    }
    add_filter( 'woocommerce_add_to_cart_fragments', 'olobuild_megamenu_cart_fragment' );
}
