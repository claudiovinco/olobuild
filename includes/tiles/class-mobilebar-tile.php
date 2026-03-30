<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Olo_Mobilebar_Tile extends Olo_Tile_Base {

    protected $type     = 'mobilebar';
    protected $name     = 'Mobile Bar';
    protected $icon     = 'dashicons-smartphone';
    protected $category = 'navigation';
    protected $defaults = [
        'breakpoint'         => '1024',
        'logo_image'         => '',
        'logo_width'         => '120',
        'logo_link'          => '',
        'bar_bg'             => '#1a3a5c',
        'bar_height'         => '56',
        'bar_shadow'         => true,
        'bar_padding'        => '12',
        'hamburger_style'    => 'classic',
        'hamburger_size'     => '28',
        'hamburger_color'    => '#ffffff',
        'menu_id'            => '',
        'panel_bg'           => '#ffffff',
        'panel_text_color'   => '#222222',
        'panel_active_color' => '',
        'panel_font_size'    => '17',
        'panel_item_padding' => '16',
        'panel_separator'    => true,
        'panel_chevron_color'=> '#999999',
        'search_enabled'     => true,
        'search_icon_color'  => '#ffffff',
        'search_placeholder' => 'Cerca...',
    ];

    public function get_controls() { return []; }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-mb-' . wp_rand( 10000, 99999 );

        $bp          = intval( $s['breakpoint'] ) ?: 1024;
        $bar_h       = intval( $s['bar_height'] ) ?: 56;
        $bar_bg      = $this->safe_color_css( $s['bar_bg'] ) ?: '#1a3a5c';
        $bar_pad     = intval( $s['bar_padding'] );
        $bar_shadow  = ! empty( $s['bar_shadow'] );
        $ham_color   = $this->safe_color_css( $s['hamburger_color'] ) ?: '#fff';
        $ham_size    = intval( $s['hamburger_size'] ) ?: 28;
        $ham_style   = esc_attr( $s['hamburger_style'] ?: 'classic' );
        $logo_img    = esc_url( $s['logo_image'] );
        $logo_w      = intval( $s['logo_width'] ) ?: 120;
        $logo_link   = $s['logo_link'] ? esc_url( $s['logo_link'] ) : esc_url( home_url( '/' ) );
        $menu_id     = intval( $s['menu_id'] );
        $p_bg        = $this->safe_color_css( $s['panel_bg'] ) ?: '#ffffff';
        $p_color     = $this->safe_color_css( $s['panel_text_color'] ) ?: '#222';
        $p_active    = $this->safe_color_css( $s['panel_active_color'] ) ?: 'var(--olo-color-primary, #e74c3c)';
        $p_fs        = intval( $s['panel_font_size'] ) ?: 17;
        $p_pad       = intval( $s['panel_item_padding'] ) ?: 16;
        $p_sep       = ! empty( $s['panel_separator'] );
        $p_chev      = $this->safe_color_css( $s['panel_chevron_color'] ) ?: '#999';
        $search_on   = ! empty( $s['search_enabled'] );
        $search_ic   = $this->safe_color_css( $s['search_icon_color'] ) ?: '#fff';
        $search_ph   = esc_attr( $s['search_placeholder'] ?: 'Cerca...' );

        ob_start();

        // ─── CSS ───
        $this->render_css( $uid, $bp, $bar_h, $bar_bg, $bar_pad, $bar_shadow, $ham_color, $ham_size, $p_bg, $p_color, $p_active, $p_fs, $p_pad, $p_sep, $p_chev, $search_ic );

        // ─── HTML ───
        $this->render_html( $uid, $logo_img, $logo_w, $logo_link, $ham_style, $search_on, $search_ph, $menu_id, $bar_h );

        // ─── JS ───
        $this->render_js( $uid, $bar_h );

        return ob_get_clean();
    }

    /* ═══════════════════════════════════════════
       CSS
       ═══════════════════════════════════════════ */

    private function render_css( $uid, $bp, $bar_h, $bar_bg, $bar_pad, $bar_shadow, $ham_color, $ham_size, $p_bg, $p_color, $p_active, $p_fs, $p_pad, $p_sep, $p_chev, $search_ic ) {
        ?>
        <style>
        /* ── Mobilebar: hidden on desktop ── */
        .<?php echo $uid; ?> {
            display: none;
            position: sticky;
            top: 0;
            z-index: 1050;
            width: 100%;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        @media (max-width: <?php echo $bp; ?>px) {
            .<?php echo $uid; ?> { display: block; }
        }
        .admin-bar .<?php echo $uid; ?> {
            top: var(--wp-admin--admin-bar--height, 32px);
        }
        @media (max-width: 600px) {
            .admin-bar .<?php echo $uid; ?> { top: 0; }
        }

        /* ── Bar ── */
        .<?php echo $uid; ?> .olo-mb-bar {
            display: flex;
            align-items: center;
            height: <?php echo $bar_h; ?>px;
            padding: 0 <?php echo $bar_pad; ?>px;
            background: <?php echo $bar_bg; ?>;
            position: relative;
            z-index: 2;
            <?php if ( $bar_shadow ) : ?>box-shadow: 0 2px 8px rgba(0,0,0,.15);<?php endif; ?>
        }

        /* ── Logo ── */
        .<?php echo $uid; ?> .olo-mb-logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            flex-shrink: 0;
        }
        .<?php echo $uid; ?> .olo-mb-logo img {
            height: auto;
            max-height: <?php echo $bar_h - 16; ?>px;
            width: auto;
            display: block;
        }

        /* ── Spacer ── */
        .<?php echo $uid; ?> .olo-mb-spacer { flex: 1; }

        /* ── Icon buttons (search + hamburger) ── */
        .<?php echo $uid; ?> .olo-mb-icon-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            outline: none;
        }
        .<?php echo $uid; ?> .olo-mb-icon-btn:focus,
        .<?php echo $uid; ?> .olo-mb-icon-btn:active {
            background: none !important;
            border: none !important;
            box-shadow: none !important;
            outline: none !important;
        }

        /* Search icon */
        .<?php echo $uid; ?> .olo-mb-search-btn svg {
            width: 22px;
            height: 22px;
            fill: none;
            stroke: <?php echo $search_ic; ?>;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* ── Hamburger ── */
        .<?php echo $uid; ?> .olo-mb-hamburger {
            width: <?php echo $ham_size; ?>px;
            height: <?php echo $ham_size; ?>px;
            position: relative;
            flex-direction: column;
            justify-content: center;
            gap: <?php echo max( 4, round( $ham_size / 6 ) ); ?>px;
        }
        .<?php echo $uid; ?> .olo-mb-hamburger span {
            display: block;
            width: 100%;
            height: 2px;
            background: <?php echo $ham_color; ?>;
            border-radius: 2px;
            transition: transform .3s ease, opacity .3s ease, width .3s ease;
            transform-origin: center;
        }

        /* Classic → X */
        .<?php echo $uid; ?>.olo-mb-open .olo-mb-hamburger.olo-mb-ham-classic span:nth-child(1) {
            transform: translateY(<?php echo max( 4, round( $ham_size / 6 ) ) + 2; ?>px) rotate(45deg);
        }
        .<?php echo $uid; ?>.olo-mb-open .olo-mb-hamburger.olo-mb-ham-classic span:nth-child(2) {
            opacity: 0;
        }
        .<?php echo $uid; ?>.olo-mb-open .olo-mb-hamburger.olo-mb-ham-classic span:nth-child(3) {
            transform: translateY(-<?php echo max( 4, round( $ham_size / 6 ) ) + 2; ?>px) rotate(-45deg);
        }

        /* Squeeze → X (lines squeeze together then rotate) */
        .<?php echo $uid; ?>.olo-mb-open .olo-mb-hamburger.olo-mb-ham-squeeze span:nth-child(1) {
            transform: translateY(<?php echo max( 4, round( $ham_size / 6 ) ) + 2; ?>px) rotate(45deg);
            width: 70%;
        }
        .<?php echo $uid; ?>.olo-mb-open .olo-mb-hamburger.olo-mb-ham-squeeze span:nth-child(2) {
            opacity: 0; width: 0;
        }
        .<?php echo $uid; ?>.olo-mb-open .olo-mb-hamburger.olo-mb-ham-squeeze span:nth-child(3) {
            transform: translateY(-<?php echo max( 4, round( $ham_size / 6 ) ) + 2; ?>px) rotate(-45deg);
            width: 70%;
        }

        /* Arrow → ← */
        .<?php echo $uid; ?>.olo-mb-open .olo-mb-hamburger.olo-mb-ham-arrow span:nth-child(1) {
            transform: translateY(3px) rotate(-40deg); width: 55%;
        }
        .<?php echo $uid; ?>.olo-mb-open .olo-mb-hamburger.olo-mb-ham-arrow span:nth-child(3) {
            transform: translateY(-3px) rotate(40deg); width: 55%;
        }

        /* Minimal (2 lines) */
        .<?php echo $uid; ?> .olo-mb-hamburger.olo-mb-ham-minimal span:nth-child(3) {
            display: none;
        }
        .<?php echo $uid; ?> .olo-mb-hamburger.olo-mb-ham-minimal span:nth-child(1) { width: 70%; }
        .<?php echo $uid; ?>.olo-mb-open .olo-mb-hamburger.olo-mb-ham-minimal span:nth-child(1) {
            transform: translateY(<?php echo max( 4, round( $ham_size / 6 ) ) + 2; ?>px) rotate(45deg); width: 100%;
        }
        .<?php echo $uid; ?>.olo-mb-open .olo-mb-hamburger.olo-mb-ham-minimal span:nth-child(2) {
            transform: translateY(0) rotate(-45deg);
        }

        /* Dot Grid (3x3 dots) */
        .<?php echo $uid; ?> .olo-mb-hamburger.olo-mb-ham-dot-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 3px;
            padding: 4px;
        }
        .<?php echo $uid; ?> .olo-mb-hamburger.olo-mb-ham-dot-grid span {
            width: 4px; height: 4px;
            border-radius: 50%;
            background: <?php echo $ham_color; ?>;
            transition: transform .3s ease, opacity .3s ease;
        }
        .<?php echo $uid; ?>.olo-mb-open .olo-mb-hamburger.olo-mb-ham-dot-grid span { opacity: 0; }
        .<?php echo $uid; ?>.olo-mb-open .olo-mb-hamburger.olo-mb-ham-dot-grid span:nth-child(1) {
            opacity: 1; transform: translate(8px,8px) rotate(45deg) scaleX(3);
        }
        .<?php echo $uid; ?>.olo-mb-open .olo-mb-hamburger.olo-mb-ham-dot-grid span:nth-child(9) {
            opacity: 1; transform: translate(-8px,-8px) rotate(45deg) scaleX(3);
        }
        .<?php echo $uid; ?>.olo-mb-open .olo-mb-hamburger.olo-mb-ham-dot-grid span:nth-child(3) {
            opacity: 1; transform: translate(-8px,8px) rotate(-45deg) scaleX(3);
        }
        .<?php echo $uid; ?>.olo-mb-open .olo-mb-hamburger.olo-mb-ham-dot-grid span:nth-child(7) {
            opacity: 1; transform: translate(8px,-8px) rotate(-45deg) scaleX(3);
        }

        /* ── Dropdown Panel ── */
        .<?php echo $uid; ?> .olo-mb-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: <?php echo $p_bg; ?>;
            max-height: 0;
            overflow: hidden;
            transition: max-height .4s cubic-bezier(.4,0,.2,1);
            z-index: 1;
            box-shadow: 0 4px 16px rgba(0,0,0,.1);
        }
        .<?php echo $uid; ?>.olo-mb-open .olo-mb-dropdown {
            max-height: calc(100vh - <?php echo $bar_h; ?>px);
            overflow-y: auto;
        }

        /* ── Nav items ── */
        .<?php echo $uid; ?> .olo-mb-nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .<?php echo $uid; ?> .olo-mb-nav > ul > li {
            <?php if ( $p_sep ) : ?>border-bottom: 1px solid rgba(0,0,0,.08);<?php endif; ?>
        }
        .<?php echo $uid; ?> .olo-mb-item {
            display: flex;
            align-items: center;
        }
        .<?php echo $uid; ?> .olo-mb-item a,
        .<?php echo $uid; ?> .olo-mb-nav > ul > li > a {
            display: block;
            flex: 1;
            padding: <?php echo $p_pad; ?>px <?php echo $p_pad + 8; ?>px;
            color: <?php echo $p_color; ?>;
            text-decoration: none;
            font-size: <?php echo $p_fs; ?>px;
            font-weight: 500;
            line-height: 1.4;
        }
        .<?php echo $uid; ?> .olo-mb-nav > ul > li > a:hover,
        .<?php echo $uid; ?> .olo-mb-item a:hover {
            color: <?php echo $p_active; ?>;
        }
        .<?php echo $uid; ?> .olo-mb-nav li.olo-mb-active > a,
        .<?php echo $uid; ?> .olo-mb-nav li.olo-mb-active > .olo-mb-item > a {
            color: <?php echo $p_active; ?>;
        }

        /* ── Chevron ── */
        .<?php echo $uid; ?> .olo-mb-chevron {
            background: none;
            border: none;
            cursor: pointer;
            padding: <?php echo $p_pad; ?>px;
            color: <?php echo $p_chev; ?>;
            font-size: 20px;
            line-height: 1;
            transition: transform .3s ease;
            display: flex;
            align-items: center;
            flex-shrink: 0;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            outline: none;
        }
        .<?php echo $uid; ?> .olo-mb-chevron:focus {
            outline: none !important;
            box-shadow: none !important;
        }
        .<?php echo $uid; ?> .olo-mb-chevron svg {
            width: 20px; height: 20px;
            fill: currentColor;
            transition: transform .3s ease;
        }
        .<?php echo $uid; ?> li.olo-mb-sub-open > .olo-mb-item > .olo-mb-chevron svg {
            transform: rotate(180deg);
        }

        /* ── Sub-menu (accordion) ── */
        .<?php echo $uid; ?> .olo-mb-sub {
            max-height: 0;
            overflow: hidden;
            transition: max-height .35s cubic-bezier(.4,0,.2,1);
            background: rgba(0,0,0,.02);
        }
        .<?php echo $uid; ?> li.olo-mb-sub-open > .olo-mb-sub {
            max-height: 500px;
        }
        .<?php echo $uid; ?> .olo-mb-sub li a {
            display: block;
            padding: <?php echo max( 8, $p_pad - 4 ); ?>px <?php echo $p_pad + 8; ?>px <?php echo max( 8, $p_pad - 4 ); ?>px <?php echo $p_pad + 28; ?>px;
            color: <?php echo $p_color; ?>;
            text-decoration: none;
            font-size: <?php echo max( 14, $p_fs - 2 ); ?>px;
            font-weight: 400;
            opacity: .85;
        }
        .<?php echo $uid; ?> .olo-mb-sub li a:hover { opacity: 1; color: <?php echo $p_active; ?>; }

        /* 3rd level */
        .<?php echo $uid; ?> .olo-mb-sub .olo-mb-sub li a {
            padding-left: <?php echo $p_pad + 48; ?>px;
            font-size: <?php echo max( 13, $p_fs - 3 ); ?>px;
        }

        /* ── Search panel ── */
        .<?php echo $uid; ?> .olo-mb-search-panel {
            max-height: 0;
            overflow: hidden;
            transition: max-height .3s ease;
            background: <?php echo $p_bg; ?>;
            border-bottom: 1px solid rgba(0,0,0,.08);
        }
        .<?php echo $uid; ?>.olo-mb-search-open .olo-mb-search-panel {
            max-height: 80px;
        }
        .<?php echo $uid; ?> .olo-mb-search-form {
            display: flex;
            padding: 10px <?php echo $bar_pad; ?>px;
            gap: 8px;
        }
        .<?php echo $uid; ?> .olo-mb-search-form input {
            flex: 1;
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
            outline: none;
            background: #f9f9f9;
        }
        .<?php echo $uid; ?> .olo-mb-search-form input:focus {
            border-color: <?php echo $p_active; ?>;
        }
        .<?php echo $uid; ?> .olo-mb-search-form button {
            background: <?php echo $bar_bg; ?>;
            border: none;
            border-radius: 6px;
            padding: 0 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
        }
        .<?php echo $uid; ?> .olo-mb-search-form button svg {
            width: 18px; height: 18px;
            stroke: #fff; fill: none;
            stroke-width: 2;
        }
        </style>
        <?php
    }

    /* ═══════════════════════════════════════════
       HTML
       ═══════════════════════════════════════════ */

    private function render_html( $uid, $logo_img, $logo_w, $logo_link, $ham_style, $search_on, $search_ph, $menu_id, $bar_h ) {
        $current_url = trailingslashit( home_url( add_query_arg( [], false ) ) );
        $search_svg  = '<svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>';
        $chevron_svg = '<svg viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>';

        // Determine hamburger span count
        $ham_spans = ( $ham_style === 'dot-grid' ) ? 9 : 3;
        ?>
        <div class="olo-mobilebar <?php echo esc_attr( $uid ); ?>" data-uid="<?php echo esc_attr( $uid ); ?>">

            <!-- Bar -->
            <div class="olo-mb-bar">
                <a class="olo-mb-logo" href="<?php echo $logo_link; ?>">
                    <?php if ( $logo_img ) : ?>
                        <img src="<?php echo $logo_img; ?>" alt="Logo" style="max-width:<?php echo $logo_w; ?>px;">
                    <?php else : ?>
                        <span style="color:#fff;font-weight:700;font-size:18px;">
                            <?php echo esc_html( get_bloginfo( 'name' ) ); ?>
                        </span>
                    <?php endif; ?>
                </a>

                <div class="olo-mb-spacer"></div>

                <?php if ( $search_on ) : ?>
                <button class="olo-mb-icon-btn olo-mb-search-btn" aria-label="Cerca" type="button">
                    <?php echo $search_svg; ?>
                </button>
                <?php endif; ?>

                <button class="olo-mb-icon-btn olo-mb-hamburger olo-mb-ham-<?php echo $ham_style; ?>" aria-label="Menu" aria-expanded="false" type="button">
                    <?php for ( $i = 0; $i < $ham_spans; $i++ ) : ?><span></span><?php endfor; ?>
                </button>
            </div>

            <!-- Search Panel -->
            <?php if ( $search_on ) : ?>
            <div class="olo-mb-search-panel">
                <form class="olo-mb-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search">
                    <input type="search" name="s" placeholder="<?php echo $search_ph; ?>" autocomplete="off">
                    <button type="submit" aria-label="Cerca"><?php echo $search_svg; ?></button>
                </form>
            </div>
            <?php endif; ?>

            <!-- Dropdown Menu Panel -->
            <div class="olo-mb-dropdown">
                <nav class="olo-mb-nav" aria-label="Mobile navigation">
                    <?php $this->render_menu_items( $menu_id, $current_url, $chevron_svg ); ?>
                </nav>
            </div>
        </div>
        <?php
    }

    /* ─── Menu Items ─── */

    private function render_menu_items( $menu_id, $current_url, $chevron_svg ) {
        if ( ! $menu_id ) {
            echo '<ul><li><a href="/">Home</a></li><li><a href="#">Seleziona un menu nelle impostazioni</a></li></ul>';
            return;
        }

        $items = wp_get_nav_menu_items( $menu_id );
        if ( ! $items ) {
            echo '<ul><li><a href="#">Menu vuoto</a></li></ul>';
            return;
        }

        // Build hierarchy
        $tree     = [];
        $children = [];
        foreach ( $items as $item ) {
            if ( $item->menu_item_parent == 0 ) {
                $tree[] = $item;
            } else {
                $children[ $item->menu_item_parent ][] = $item;
            }
        }

        echo '<ul>';
        foreach ( $tree as $item ) {
            $subs       = $children[ $item->ID ] ?? [];
            $is_current = trailingslashit( $item->url ) === $current_url;
            $li_class   = [];
            if ( $is_current ) $li_class[] = 'olo-mb-active';
            if ( ! empty( $subs ) ) $li_class[] = 'olo-mb-has-children';
            $cls = $li_class ? ' class="' . esc_attr( implode( ' ', $li_class ) ) . '"' : '';

            echo '<li' . $cls . '>';

            if ( ! empty( $subs ) ) {
                echo '<div class="olo-mb-item">';
                echo '<a href="' . esc_url( $item->url ) . '">' . esc_html( $item->title ) . '</a>';
                echo '<button class="olo-mb-chevron" type="button" aria-label="Espandi">' . $chevron_svg . '</button>';
                echo '</div>';

                // Sub-menu
                echo '<ul class="olo-mb-sub">';
                foreach ( $subs as $sub ) {
                    $sub_subs    = $children[ $sub->ID ] ?? [];
                    $sub_current = trailingslashit( $sub->url ) === $current_url;
                    $sub_cls     = [];
                    if ( $sub_current ) $sub_cls[] = 'olo-mb-active';
                    if ( ! empty( $sub_subs ) ) $sub_cls[] = 'olo-mb-has-children';
                    $sc = $sub_cls ? ' class="' . esc_attr( implode( ' ', $sub_cls ) ) . '"' : '';

                    echo '<li' . $sc . '>';
                    if ( ! empty( $sub_subs ) ) {
                        echo '<div class="olo-mb-item">';
                        echo '<a href="' . esc_url( $sub->url ) . '">' . esc_html( $sub->title ) . '</a>';
                        echo '<button class="olo-mb-chevron" type="button" aria-label="Espandi">' . $chevron_svg . '</button>';
                        echo '</div>';
                        echo '<ul class="olo-mb-sub">';
                        foreach ( $sub_subs as $ss ) {
                            $ss_current = trailingslashit( $ss->url ) === $current_url;
                            echo '<li' . ( $ss_current ? ' class="olo-mb-active"' : '' ) . '>';
                            echo '<a href="' . esc_url( $ss->url ) . '">' . esc_html( $ss->title ) . '</a>';
                            echo '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<a href="' . esc_url( $sub->url ) . '">' . esc_html( $sub->title ) . '</a>';
                    }
                    echo '</li>';
                }
                echo '</ul>';
            } else {
                echo '<a href="' . esc_url( $item->url ) . '">' . esc_html( $item->title ) . '</a>';
            }

            echo '</li>';
        }
        echo '</ul>';
    }

    /* ═══════════════════════════════════════════
       JavaScript
       ═══════════════════════════════════════════ */

    private function render_js( $uid, $bar_h ) {
        ?>
        <script>
        (function(){
            var uid = "<?php echo esc_js( $uid ); ?>";
            var wrapper = document.querySelector("." + uid);
            if (!wrapper) return;

            var hamburger = wrapper.querySelector(".olo-mb-hamburger");
            var searchBtn = wrapper.querySelector(".olo-mb-search-btn");

            /* ── Toggle menu ── */
            if (hamburger) {
                hamburger.addEventListener("click", function() {
                    var isOpen = wrapper.classList.contains("olo-mb-open");
                    if (isOpen) {
                        wrapper.classList.remove("olo-mb-open");
                        hamburger.setAttribute("aria-expanded", "false");
                        document.body.style.overflow = "";
                    } else {
                        wrapper.classList.remove("olo-mb-search-open");
                        wrapper.classList.add("olo-mb-open");
                        hamburger.setAttribute("aria-expanded", "true");
                        document.body.style.overflow = "hidden";
                    }
                });
            }

            /* ── Toggle search ── */
            if (searchBtn) {
                searchBtn.addEventListener("click", function() {
                    var isSearchOpen = wrapper.classList.contains("olo-mb-search-open");
                    if (isSearchOpen) {
                        wrapper.classList.remove("olo-mb-search-open");
                    } else {
                        wrapper.classList.remove("olo-mb-open");
                        hamburger.setAttribute("aria-expanded", "false");
                        document.body.style.overflow = "";
                        wrapper.classList.add("olo-mb-search-open");
                        var inp = wrapper.querySelector(".olo-mb-search-form input");
                        if (inp) {
                            setTimeout(function(){ inp.focus(); }, 350);
                        }
                    }
                });
            }

            /* ── Accordion chevrons ── */
            wrapper.querySelectorAll(".olo-mb-chevron").forEach(function(btn) {
                btn.addEventListener("click", function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var li = btn.closest("li");
                    if (li) {
                        li.classList.toggle("olo-mb-sub-open");
                    }
                });
            });

            /* ── Close on link click ── */
            wrapper.querySelectorAll(".olo-mb-nav a").forEach(function(link) {
                link.addEventListener("click", function() {
                    wrapper.classList.remove("olo-mb-open");
                    if (hamburger) { hamburger.setAttribute("aria-expanded", "false"); }
                    document.body.style.overflow = "";
                });
            });

        })();
        </script>
        <?php
    }
}
