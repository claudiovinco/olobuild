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

    // shadow_map and drop_shadow_map moved to Olo_CSS_Builder

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

    /** @var Olo_CSS_Builder */
    private $css;

    /** @var Olo_Animation_Builder */
    private $anim;

    /** @var array Breakpoint definitions (popolato da render_for_builder). */
    public $breakpoints = [];

    /** @var array Responsive CSS rules collected during render. */
    public $responsive_css_rules = [];

    /**
     * Whitelist of allowed CSS border-style values.
     */
    private static $allowed_border_styles = [ 'none', 'solid', 'dashed', 'dotted', 'double', 'groove', 'ridge', 'inset', 'outset' ];

    public function __construct() {
        $this->css  = new Olo_CSS_Builder();
        $this->anim = new Olo_Animation_Builder();
        if ( ! has_action( 'wp_footer', [ __CLASS__, 'print_sticky_offset_script' ] ) ) {
            add_action( 'wp_footer', [ __CLASS__, 'print_sticky_offset_script' ], 99 );
        }
    }

    /**
     * Sanitize a border-style value against whitelist.
     */
    private function safe_border_style( $val ) {
        $val = strtolower( trim( $val ?? 'solid' ) );
        return in_array( $val, self::$allowed_border_styles, true ) ? $val : 'solid';
    }

    /**
     * Sanitize a CSS color value — allows hex, rgb, rgba, hsl, hsla, named colors, CSS variables.
     */
    private function safe_border_color( $val ) {
        $val = trim( $val ?? '#374151' );
        // Allow hex, rgb/rgba, hsl/hsla, named colors, CSS custom properties
        if ( preg_match( '/^(#[0-9a-fA-F]{3,8}|rgba?\([^)]+\)|hsla?\([^)]+\)|var\(--[a-zA-Z0-9_-]+\)|[a-zA-Z]+)$/', $val ) ) {
            return $val;
        }
        return '#374151';
    }

    /**
     * Sanitize inline custom CSS — strip dangerous patterns.
     * For inline style="" attributes, only allows CSS property declarations.
     */
    private function safe_inline_css( $css ) {
        $css = trim( $css ?? '' );
        if ( $css === '' ) return '';
        // Remove anything that could break out of style attribute or inject HTML
        $css = str_replace( [ '<', '>', '"', "'", '`' ], '', $css );
        // Remove expression(), url(javascript:), url(data:), @import, behavior:
        $css = preg_replace( '/expression\s*\(/i', '', $css );
        $css = preg_replace( '/url\s*\(\s*(javascript|data)\s*:/i', 'url(blocked:', $css );
        $css = preg_replace( '/@import/i', '', $css );
        $css = preg_replace( '/behavior\s*:/i', '', $css );
        $css = preg_replace( '/-moz-binding\s*:/i', '', $css );
        return $css;
    }

    /**
     * Sanitize a CSS dimension value (width/height/max-width/min-height/etc.).
     * Accetta numeri puri (→ px) e qualunque unità CSS standard: px/%/em/rem/vw/vh/vmin/vmax/ch/ex/fr/cm/mm/in/pt/pc.
     * Supporta anche keyword (auto/none/inherit/initial/unset/fit-content) e funzioni CSS sicure (calc/min/max/clamp/var/env).
     * Tutto il resto viene scartato per evitare CSS injection.
     *
     * @param mixed $value Valore raw (numero o stringa).
     * @return string Valore CSS sanitizzato (es. "49%", "200px", "calc(100% - 10px)") o '' se invalido.
     */
    private function safe_dim_value( $value ) {
        if ( $value === null || $value === '' ) return '';
        $v = trim( (string) $value );
        if ( $v === '' ) return '';
        // Numero puro → px
        if ( is_numeric( $v ) ) return $v . 'px';
        // Keyword consentite
        $keywords = [ 'auto', 'none', 'inherit', 'initial', 'unset', 'revert', 'fit-content', 'max-content', 'min-content' ];
        if ( in_array( strtolower( $v ), $keywords, true ) ) return strtolower( $v );
        // Numero + unità (es. 49%, 200px, 1.5rem, 50vh, calc()/min()/max()/clamp()/var()/env() supportate)
        // Pattern: [numero][unità] oppure funzione CSS con caratteri sicuri.
        if ( preg_match( '/^-?\d*\.?\d+(px|%|em|rem|vw|vh|vmin|vmax|ch|ex|fr|cm|mm|in|pt|pc)$/i', $v ) ) {
            return $v;
        }
        // Funzioni CSS: calc(), min(), max(), clamp(), var(), env() — solo caratteri sicuri.
        if ( preg_match( '/^(calc|min|max|clamp|var|env)\([\w\s\d\.\,\+\-\*\/%\(\)\-]+\)$/i', $v ) ) {
            // Aggiuntiva: blocca espressioni pericolose
            if ( strpos( $v, 'expression' ) !== false ) return '';
            if ( stripos( $v, 'url(' ) !== false )      return '';
            return $v;
        }
        return '';
    }

    /**
     * Applica al `$inline_styles` (by-ref) tutti gli "box styles" che sono
     * identici tra section / row / column / element renderer:
     * margin, padding, border-radius, border, opacity, flex container, transform,
     * box-shadow inline, text-shadow, backdrop-filter, overflow esplicito,
     * dimensions (tile_width/max_width/min_height), mask, custom CSS (advanced),
     * positioning (advanced.position_mode + top/left/right/bottom/width/zindex).
     *
     * Sostituisce ~180 LOC di codice duplicato. L'ordine delle declarations è
     * "canonical" — è sicuro perché ognuna è una proprietà CSS diversa (no
     * cascading conflicts tra di loro).
     *
     * Cose ESCLUSE perché divergenti tra i nodi (i renderer le gestiscono in proprio):
     *  - background (image/video/gallery layers, scope container vs section)
     *  - shadow class `uk-box-shadow-*` (decide il renderer in base a has_bg_any)
     *  - drop-shadow filter (solo element trasparente)
     *  - sticky/scroll_snap (section), grid placement (column), entrance animation
     *  - scrollspy/parallax attrs, hover_css_rules, responsive_css
     *  - text_color (solo element, aggiunto di recente)
     *
     * @param array &$inline_styles Array CSS declarations (by-ref, esteso in-place).
     * @param array  $style         `$node['style']`
     * @param array  $settings      `$node['settings']` (serve per flex container)
     * @param array  $advanced      `$node['advanced']` (custom_css + positioning)
     * @param array  $opts          Flag opzionali (vedi sotto):
     *                              - `apply_box_shadow` (bool, default true): include `build_box_shadow_css`.
     *                                Element trasparente lo skippa e usa drop-shadow filter.
     *                              - `apply_flex` (bool, default true): include `build_flex_container_css`.
     *                                Row lo skippa perché il flex va al `<div uk-grid>` interno,
     *                                non al wrapper esterno.
     */
    private function apply_common_box_styles( array &$inline_styles, array $style, array $settings, array $advanced = [], array $opts = [] ) {
        $apply_box_shadow = $opts['apply_box_shadow'] ?? true;
        $apply_flex       = $opts['apply_flex'] ?? true;
        // Margin & Padding — intval() per prevenire CSS injection via tile settings.
        if ( ! empty( $style['margin_top'] ) )     $inline_styles[] = 'margin-top: ' . intval( $style['margin_top'] ) . 'px';
        if ( ! empty( $style['margin_right'] ) )   $inline_styles[] = 'margin-right: ' . intval( $style['margin_right'] ) . 'px';
        if ( ! empty( $style['margin_bottom'] ) )  $inline_styles[] = 'margin-bottom: ' . intval( $style['margin_bottom'] ) . 'px';
        if ( ! empty( $style['margin_left'] ) )    $inline_styles[] = 'margin-left: ' . intval( $style['margin_left'] ) . 'px';
        if ( ! empty( $style['padding_top'] ) )    $inline_styles[] = 'padding-top: ' . intval( $style['padding_top'] ) . 'px';
        if ( ! empty( $style['padding_right'] ) )  $inline_styles[] = 'padding-right: ' . intval( $style['padding_right'] ) . 'px';
        if ( ! empty( $style['padding_bottom'] ) ) $inline_styles[] = 'padding-bottom: ' . intval( $style['padding_bottom'] ) . 'px';
        if ( ! empty( $style['padding_left'] ) )   $inline_styles[] = 'padding-left: ' . intval( $style['padding_left'] ) . 'px';

        // Border radius
        if ( ! empty( $style['border_radius'] ) )  $inline_styles[] = $this->css->build_border_radius_css( $style['border_radius'] );

        // Border (sistema unificato: oggetto 4-side + fallback legacy 3-key)
        $border_css = $this->build_wrapper_border_css( $style );
        if ( $border_css ) $inline_styles[] = $border_css;

        // Opacity
        if ( ! empty( $style['opacity'] ) && intval( $style['opacity'] ) < 100 ) {
            $opacity = intval( $style['opacity'] ) / 100;
            $inline_styles[] = "opacity: {$opacity}";
        }

        // Flex container settings (tab "Layout Flex" inspector).
        // Row skippa (`apply_flex=false`) perché applica flex al `<div uk-grid>` interno.
        if ( $apply_flex ) {
            $flex_css = $this->css->build_flex_container_css( $settings );
            foreach ( $flex_css as $decl ) $inline_styles[] = $decl;
        }

        // CSS Transform (normal state)
        $transform_css = $this->css->build_transform_css( $style );
        if ( $transform_css ) {
            foreach ( $transform_css as $decl ) $inline_styles[] = $decl;
        }

        // Box shadow inline (separato dalla classe uk-box-shadow-*, decisa dal renderer).
        // Element con bg=trasparente skippa: usa drop-shadow filter, lo gestisce nel renderer.
        if ( $apply_box_shadow ) {
            $box_shadow = $this->css->build_box_shadow_css( $style );
            if ( $box_shadow ) $inline_styles[] = $box_shadow;
        }

        // Text shadow
        $text_shadow = $this->css->build_text_shadow_css( $style );
        if ( $text_shadow ) $inline_styles[] = $text_shadow;

        // Backdrop filter
        $backdrop = $this->css->build_backdrop_filter_css( $style );
        if ( $backdrop ) {
            foreach ( $backdrop as $decl ) $inline_styles[] = $decl;
        }

        // Overflow esplicito
        if ( ! empty( $style['overflow'] ) && $style['overflow'] !== 'visible' ) {
            $inline_styles[] = 'overflow: ' . esc_attr( $style['overflow'] );
        }

        // Blend mode (mix-blend-mode sul wrapper) — whitelist contro CSS injection.
        if ( ! empty( $style['blend_mode'] ) && $style['blend_mode'] !== 'normal' ) {
            $allowed_blend = [ 'multiply', 'screen', 'overlay', 'darken', 'lighten',
                'color-dodge', 'color-burn', 'hard-light', 'soft-light', 'difference',
                'exclusion', 'hue', 'saturation', 'color', 'luminosity' ];
            if ( in_array( $style['blend_mode'], $allowed_blend, true ) ) {
                $inline_styles[] = 'mix-blend-mode: ' . $style['blend_mode'];
            }
        }

        // Dimensions (tile_width / tile_max_width / tile_min_height) — unità libere via safe_dim_value
        $dim_map = [
            'tile_width'      => 'width',
            'tile_max_width'  => 'max-width',
            'tile_min_height' => 'min-height',
        ];
        foreach ( $dim_map as $key => $css_prop ) {
            if ( ! isset( $style[ $key ] ) || $style[ $key ] === '' || $style[ $key ] === null ) continue;
            $v = $this->safe_dim_value( $style[ $key ] );
            if ( $v !== '' ) $inline_styles[] = $css_prop . ': ' . $v;
        }

        // Mask
        $mask_css = $this->css->build_mask_css( $style );
        if ( $mask_css ) {
            foreach ( $mask_css as $decl ) $inline_styles[] = $decl;
        }

        // Custom CSS da advanced
        if ( ! empty( $advanced['custom_css'] ) ) {
            $inline_styles[] = $this->safe_inline_css( $advanced['custom_css'] );
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
    }

    /**
     * Sanitize custom CSS for <style> blocks — more permissive than inline but still safe.
     */
    private function safe_block_css( $css ) {
        $css = trim( $css ?? '' );
        if ( $css === '' ) return '';
        // Remove </style> to prevent breaking out of style block
        $css = preg_replace( '/<\/style\s*>/i', '', $css );
        // Remove <script> tags
        $css = preg_replace( '/<script/i', '', $css );
        // Remove expression(), behavior, -moz-binding
        $css = preg_replace( '/expression\s*\(/i', '', $css );
        $css = preg_replace( '/behavior\s*:/i', '', $css );
        $css = preg_replace( '/-moz-binding\s*:/i', '', $css );
        $css = preg_replace( '/url\s*\(\s*(javascript|data)\s*:/i', 'url(blocked:', $css );
        $css = preg_replace( '/@import\s+url/i', '', $css );
        return $css;
    }

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
            $safe_mode = get_option( 'olo_safe_mode', false );

            if ( $safe_mode ) {
                add_filter( 'body_class', function( $classes ) { $classes[] = 'olo-safe-mode'; return $classes; } );
            }

            if ( ! $safe_mode ) {
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
            }

            // Olobuilder custom overrides (loaded after UIkit)
            wp_enqueue_style(
                'olo-frontend-css',
                OLO_URL . 'assets/css/frontend.css',
                $safe_mode ? [] : [ 'uikit-css' ],
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
    // --- CSS builder and animation builder methods moved to Olo_CSS_Builder and Olo_Animation_Builder ---


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
    public static $needs_sticky_offset_script = false;

    /**
     * Print sticky-offset script in footer.
     *
     * Lo sticky di colonna usa `top: calc(var(--olo-sticky-top-offset, 0px) + Npx)`.
     * Questo script aggiorna la var con l'altezza dell'header sticky:
     * - se l'header non è sticky (es. nel builder) → var = 0 → colonna parte da Npx
     * - se l'header è sticky → var = offsetHeight → colonna si attacca sotto l'header
     *
     * IMPORTANTE — versione precedente usava MutationObserver su class/style
     * dell'header: durante lo scroll lo script dell'header (megamenu/navmenu)
     * muta classe/style continuamente, ogni mutation triggava measure(), che
     * chiamava getBoundingClientRect() forzando un reflow sincrono → freeze
     * del browser. Qui usiamo solo:
     *  - scroll listener throttled via requestAnimationFrame (1 measure/frame)
     *  - getComputedStyle().position (cached, no reflow forzato)
     *  - offsetHeight (1 reflow netto per frame)
     * Niente MutationObserver. Niente cascata.
     */
    public static function print_sticky_offset_script() {
        if ( ! self::$needs_sticky_offset_script ) return;
        ?>
        <script>
        (function(){
          var root = document.documentElement;
          var header = document.querySelector('header.olo-site-header');
          if (!header) return;
          var lastValue = -1;
          var ticking = false;
          function measure(){
            ticking = false;
            // getComputedStyle è cached: legge il valore già calcolato senza forzare reflow.
            var pos = getComputedStyle(header).position;
            var isSticky = (pos === 'sticky' || pos === 'fixed');
            var v = isSticky ? header.offsetHeight : 0;
            if (v === lastValue) return; // no-op se invariato
            lastValue = v;
            root.style.setProperty('--olo-sticky-top-offset', v + 'px');
          }
          function onScroll(){
            if (ticking) return;
            ticking = true;
            requestAnimationFrame(measure);
          }
          window.addEventListener('scroll', onScroll, { passive: true });
          window.addEventListener('resize', onScroll, { passive: true });
          // Initial measures: il primo subito, gli altri dopo che gli script
          // header (megamenu/navmenu) hanno applicato position:sticky.
          measure();
          setTimeout(measure, 100);
          setTimeout(measure, 500);
        })();
        </script>
        <?php
    }

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
                        'id'       => $this->css->generate_id(),
                        'type'     => 'column',
                        'settings' => [ 'width_default' => '', 'width_small' => '', 'width_medium' => $w, 'width_large' => '' ],
                        'style'    => [],
                        'advanced' => [],
                        'children' => $children,
                    ];
                }

                $row = [
                    'id'       => $tile['id'] ?? $this->css->generate_id(),
                    'type'     => 'row',
                    'settings' => $settings,
                    'style'    => $tile['style'] ?? [],
                    'advanced' => $tile['advanced'] ?? [],
                    'children' => $columns,
                ];

                $sections[] = [
                    'id'       => $this->css->generate_id(),
                    'type'     => 'section',
                    'settings' => [ 'style' => 'default', 'width' => 'default', 'padding' => 'default' ],
                    'style'    => [],
                    'advanced' => [],
                    'children' => [ $row ],
                ];
            } else {
                // Wrap element in Section > Row > Column(1/1)
                $column = [
                    'id'       => $this->css->generate_id(),
                    'type'     => 'column',
                    'settings' => [ 'width_default' => '', 'width_small' => '', 'width_medium' => '1-1', 'width_large' => '' ],
                    'style'    => [],
                    'advanced' => [],
                    'children' => [ $tile ],
                ];
                $row = [
                    'id'       => $this->css->generate_id(),
                    'type'     => 'row',
                    'settings' => [ 'layout' => '100', 'gap' => '16', 'column_gap' => 'default', 'vertical_align' => 'stretch', 'stack_mobile' => true ],
                    'style'    => [],
                    'advanced' => [],
                    'children' => [ $column ],
                ];
                $sections[] = [
                    'id'       => $this->css->generate_id(),
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
                return ( str_contains( $_SERVER['REQUEST_URI'], $str ) );
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
                return ( str_contains( $referrer, $cond_ref ) );
            case 'browser':
                $cond_browser = strtolower( $settings['cond_browser'] ?? '' );
                if ( $cond_browser === '' ) return true;
                $ua = strtolower( isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '' );
                return ( str_contains( $ua, $cond_browser ) );
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
                return ( str_contains( (string) get_field( $acf_key ), $acf_value ) );
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
     * True se il sottoalbero contiene almeno un tile-leaf (non strutturale).
     * Usato in builder mode per marcare i wrapper vuoti con data-olo-empty="1".
     */
    private function has_leaf_descendant( $node ) {
        static $structural = [ 'section', 'row', 'column', 'inner-columns', 'inner-column' ];
        if ( empty( $node['children'] ) || ! is_array( $node['children'] ) ) return false;
        foreach ( $node['children'] as $child ) {
            $ctype = $child['type'] ?? '';
            if ( $ctype && ! in_array( $ctype, $structural, true ) ) return true;
            if ( $this->has_leaf_descendant( $child ) ) return true;
        }
        return false;
    }

    /**
     * Render a node (recursive dispatcher).
     */
    /** Profondità di annidamento delle chiamate render_node: serve a localizzare i link UNA sola volta (sul nodo radice). */
    private $link_depth = 0;

    private function render_node( $node, $manager, $template_id, &$hover_css_rules, &$tile_counter, $parent_is_grid = false ) {
        if ( ! $this->should_render_node( $node ) ) return '';
        $this->link_depth++;

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
            // Mark structural containers without leaf descendants so the iframe CSS
            // can give them a visible min-height (otherwise they collapse to 0 and
            // are invisible in the canvas).
            $structural_types = [ 'section', 'row', 'column', 'inner-columns', 'inner-column' ];
            if ( in_array( $type, $structural_types, true ) && ! $this->has_leaf_descendant( $node ) ) {
                $tile_id_attr .= ' data-olo-empty="1"';
            }
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

        $this->link_depth--;
        if ( $this->link_depth === 0 ) {
            $html = $this->localize_internal_links( $html );
        }

        return $html;
    }

    /**
     * Prefissa il base path dell'installazione (es. /olobuild) ai link interni
     * root-relative (href="/...") — necessario quando WordPress è installato in una
     * SOTTOCARTELLA. No-op se il sito è in root. Idempotente: salta i link che già
     * iniziano col base path, gli URL assoluti/protocol-relative, le ancore e mailto/tel.
     */
    private function localize_internal_links( $html ) {
        if ( ! is_string( $html ) || $html === '' ) return $html;
        $base = rtrim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
        if ( $base === '' || $base === '/' ) return $html; // sito in root → nessuna modifica
        return preg_replace_callback(
            '/\shref="(\/(?!\/)[^"]*)"/i',
            function ( $m ) use ( $base ) {
                $url = $m[1];
                if ( strpos( $url, $base . '/' ) === 0 || $url === $base ) return $m[0]; // già col base
                return ' href="' . $base . $url . '"';
            },
            $html
        );
    }

    /**
     * Wrap non-interactive tiles in a lazy container for deferred rendering.
     * The IntersectionObserver script in the footer will move <template> content into the DOM when visible.
     * The first 3 element tiles are rendered immediately (above the fold).
     */
    private function maybe_lazy_wrap( $html, $type, $advanced = [] ) {
        // Builder mode: no lazy loading (iframe needs all tiles visible)
        if ( $this->builder_mode ) return $html;

        // Types that must NOT be lazy-loaded (interactive, form-based, map, or relying
        // on inline scripts that won't re-run when cloned from <template>).
        static $no_lazy = [
            'form', 'map', 'search', 'livesearch', 'servicesearch', 'booking',
            'bookingpicker', 'calendar', 'loginform', 'scrollprogress',
            'popup', 'megamenu', 'navmenu', 'togglebtn',
            'blendtext', 'textmask', 'shatteredimage', 'svganimator',
        ];
        if ( in_array( $type, $no_lazy, true ) ) {
            return $html;
        }

        // Effetti "tutta la pagina" (overlay fisso, es. ParticleFX/Goo con Ambito=Pagina):
        // devono attivarsi al caricamento, non quando lo scroll raggiunge il tile.
        if ( strpos( $html, '"scope":"page"' ) !== false ) {
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

        // Il wrapper eredita la larghezza "Contenuto" dell'elemento: senza, un
        // div block romperebbe l'affiancamento delle tile inline (la tile lazy
        // andrebbe a capo da sola anche con tile_width=inline).
        $lazy_class = ( ( $advanced['tile_width'] ?? 'full' ) === 'inline' ) ? ' class="olo-tile-inline"' : '';
        return '<div data-olo-lazy' . $lazy_class . '><template class="olo-lazy-content">' . $html . '</template><div class="olo-lazy-ph" style="min-height:50px"></div></div>';
    }

    /**
     * Render a Section container using UIkit classes.
     */
    private function render_section_node( $node, $manager, $template_id, &$hover_css_rules, &$tile_counter ) {
        // Floating panel bypass: if section contains only a floatingpanel (inside row>column),
        // render the floatingpanel directly without section/row/column wrappers to avoid empty gap.
        // Skipped in builder mode: bypass would lose data-olo-tile-id for the floatingpanel
        // (it would inherit the section's id instead), breaking drop-target hit-testing.
        if ( ! $this->builder_mode && $this->section_has_only_floatingpanel( $node ) ) {
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

        // Padding verticale "Personalizzato (px)": valori espliciti sopra/sotto,
        // vincono sul default di .uk-section via inline style.
        if ( 'custom' === $padding ) {
            $pt = max( 0, intval( $s['padding_top_custom'] ?? 70 ) );
            $pb = max( 0, intval( $s['padding_bottom_custom'] ?? 70 ) );
            $inline_styles[] = 'padding-top: ' . $pt . 'px';
            $inline_styles[] = 'padding-bottom: ' . $pb . 'px';
        }

        // Background handling
        // Il field `bg` (type=background) di section è dichiarato in `fields[]` (settings),
        // quindi BuilderInspector lo salva via updateSetting → finisce in $s['bg'], NON in
        // $style['bg']. Il render storicamente leggeva solo da $style: l'utente impostava
        // un colore alla section ma non lo vedeva mai applicato. Fallback su settings.
        $tile_bg = $this->css->get_effective_bg( $style );
        if ( ( $tile_bg['type'] ?? 'none' ) === 'none' && ! empty( $s['bg']['type'] ) && $s['bg']['type'] !== 'none' ) {
            $tile_bg = $this->css->get_effective_bg( [ 'bg' => $s['bg'] ] );
        }
        $has_bg_image   = ( $tile_bg['type'] === 'image' && ! empty( $tile_bg['image_url'] ) );
        $has_bg_video   = ( $tile_bg['type'] === 'video' && ! empty( $tile_bg['video_url'] ) );
        $has_bg_gallery = ( $tile_bg['type'] === 'gallery' && ! empty( $tile_bg['gallery_images'] ) && is_array( $tile_bg['gallery_images'] ) );
        $has_bg_any     = ( $tile_bg['type'] !== 'none' );
        $has_overlay    = ( $has_bg_any && ! empty( $tile_bg['overlay_opacity'] ) && intval( $tile_bg['overlay_opacity'] ) > 0 );

        if ( $has_bg_image || $has_bg_video || $has_bg_gallery ) {
            $classes[] = 'uk-position-relative';
            $inline_styles[] = 'overflow: clip';
        } elseif ( $tile_bg['type'] !== 'none' ) {
            $bg_css = $this->css->get_bg_inline_css( $tile_bg );
            if ( $bg_css ) $inline_styles[] = $bg_css;
        }

        // Marker class: preserva il padding-top della prima section quando ha un
        // background, altrimenti la regola "classic header gap collapse" in frontend.css
        // azzera lo spazio sopra il contenuto e taglia il riquadro colorato.
        if ( $has_bg_any ) {
            $classes[] = 'olo-section-has-bg';
        }

        // Video cover height
        if ( $has_bg_video && ! empty( $tile_bg['cover_height'] ) && intval( $tile_bg['cover_height'] ) > 0 ) {
            $inline_styles[] = 'min-height: ' . intval( $tile_bg['cover_height'] ) . 'px';
        }

        // Shadow CLASS — section applica sempre (no branch has_bg_any come element).
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

        // Helper unificato: margin/padding/border-radius/border/opacity/flex/transform/
        // box-shadow inline/text-shadow/backdrop/overflow/dimensions/mask/custom_css/position.
        $this->apply_common_box_styles( $inline_styles, $style, $s, $advanced );

        // CSS Grid layout (overrides flex se layout_mode=grid) — section-specific.
        $grid_css = $this->css->build_css_grid_css( $s );
        foreach ( $grid_css as $decl ) {
            $inline_styles[] = $decl;
        }

        // overflow:clip per border-radius clipping — section/row hanno questa forzatura
        // perché altrimenti il bg overflow esce dal rounded corner. (clip preserva sticky)
        if ( ! empty( $style['border_radius'] ) ) {
            $inline_styles[] = 'overflow: clip';
        }

        // HTML ID (always generate for hover CSS support)
        $tile_counter++;
        $css_id  = ! empty( $advanced['html_id'] ) ? $advanced['html_id'] : 'ms-' . $template_id . '-' . $tile_counter;
        $id_attr = ' id="' . esc_attr( $css_id ) . '"';

        // Hover CSS rules
        $this->collect_hover_css( $style, $css_id, false, $hover_css_rules );
        $this->collect_responsive_css( $style, $css_id, $advanced );

        // Infinite animation
        $inf_anim_css = $this->css->build_infinite_animation_css( $s, $css_id );
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
            $classes[] = 'olo-visible'; // applicata subito: l'animation parte al page-load (no IntersectionObserver dependency)
            // Override CSS variables per durata/delay/easing custom (via field inspector)
            $e_dur = intval( $s['entrance_duration'] ?? 0 );
            if ( $e_dur > 0 ) $inline_styles[] = '--olo-e-dur: ' . max( 50, min( 5000, $e_dur ) ) . 'ms';
            $e_delay = intval( $s['entrance_delay'] ?? 0 );
            if ( $e_delay > 0 ) $inline_styles[] = '--olo-e-delay: ' . min( 5000, $e_delay ) . 'ms';
            $e_ease = $s['entrance_easing'] ?? 'auto';
            if ( $e_ease && $e_ease !== 'auto' ) {
                // Whitelist: keyword o cubic-bezier
                if ( preg_match( '/^(linear|ease|ease-in|ease-out|ease-in-out|cubic-bezier\([0-9.,\s\-]+\))$/', $e_ease ) ) {
                    $inline_styles[] = '--olo-e-ease: ' . $e_ease;
                }
            }
            $e_int = floatval( $s['entrance_intensity'] ?? 1 );
            if ( $e_int > 0 && abs( $e_int - 1 ) > 0.01 ) {
                $e_int = max( 0.1, min( 5, $e_int ) );
                $inline_styles[] = '--olo-e-int: ' . $e_int;
            }
            if ( ! empty( $s['entrance_stagger'] ) ) {
                $stagger_delay = intval( $s['entrance_stagger_delay'] ?? 100 );
                $stagger_delay = max( 25, min( 500, $stagger_delay ) );
                $classes[] = 'olo-stagger-parent';
                $inline_styles[] = '--olo-stagger-delay: ' . $stagger_delay . 'ms';
            }
        }

        // Scrollspy & element parallax attributes
        $scrollspy_attr = $this->anim->build_scrollspy_attr( $advanced );
        $el_parallax_attr = $this->anim->build_element_parallax_attr( $advanced );
        $mouse_attrs = $this->anim->build_mouse_attrs( $advanced );

        // Infinite animation & mask (inline style for elements)
        $inf_anim_css = $this->anim->build_inline_animation_css( $advanced );
        if ( $inf_anim_css ) $inline_styles[] = $inf_anim_css;
        $mask_css = $this->anim->build_inline_mask_css( $advanced );
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

        // Decide where to place bg/overlay layers: full section (default) or inside container.
        // bg_scope='container' keeps the bg/overlay limited to the container max-width
        // (useful when 'width' = default/small/etc. and the user doesn't want edge-to-edge bg).
        // v1.0.78 — default 'container' (Centrata): la sezione rispetta la larghezza contenuto
        // scelta dall'utente. Dati legacy senza bg_scope vengono trattati come Centrata.
        $bg_scope = ( $s['bg_scope'] ?? 'container' ) === 'section' ? 'section' : 'container';
        $has_any_bg = ( $has_bg_image || $has_bg_video || $has_bg_gallery || $has_overlay );

        // ─── Section outer max-width ─────────────────────────────────────
        // Quando bg_scope='container' E width != fullbleed/expand, anche la `<section>`
        // esterna viene limitata in larghezza con `max-width` + `margin: 0 auto`.
        // Senza questo, il colore/gradiente di sfondo era sempre bordo-a-bordo perché
        // applicato come inline style sull'outer `<section>`, mentre solo il container
        // interno seguiva il width semantico. Risultato per l'utente: scegliendo
        // "Piccolo" o "Grande" la section sembrava sempre uguale (full viewport).
        $width_for_outer = $s['width'] ?? $s['section_width'] ?? 'default';
        $outer_max_width_map = [
            'small'   => 900,
            'default' => 1200,
            'large'   => 1400,
            'xlarge'  => 1600,
        ];
        if ( $bg_scope === 'container' && isset( $outer_max_width_map[ $width_for_outer ] ) ) {
            $inline_styles[] = 'max-width: ' . $outer_max_width_map[ $width_for_outer ] . 'px';
            $inline_styles[] = 'margin-left: auto';
            $inline_styles[] = 'margin-right: auto';
        }

        $html = '<section role="region" class="' . esc_attr( implode( ' ', $classes ) ) . '"' . $id_attr;
        if ( $inline_styles ) {
            $html .= ' style="' . esc_attr( implode( '; ', $inline_styles ) ) . '"';
        }
        $html .= $scrollspy_attr . $el_parallax_attr . $snap_data_attr . $mouse_attrs . $this->anim->build_spotlight_attr( $advanced ) . '>';

        $bg_layers_html = '';
        if ( $has_bg_image ) {
            $bg_size = esc_attr( $tile_bg['image_size'] ?? 'cover' );
            $bg_pos  = esc_attr( $tile_bg['image_position'] ?? 'center center' );
            $bg_layers_html .= '<div class="uk-position-cover" style="background-image: url(' . esc_url( $tile_bg['image_url'] ) . '); background-size: ' . $bg_size . '; background-position: ' . $bg_pos . '; background-repeat: no-repeat"';
            $bg_layers_html .= $this->anim->build_uk_parallax_attr( $tile_bg );
            $bg_layers_html .= '></div>';
        }
        if ( $has_bg_video ) {
            $vid_url    = esc_url( $tile_bg['video_url'] );
            $vid_poster = ! empty( $tile_bg['video_poster'] ) ? esc_url( $tile_bg['video_poster'] ) : '';
            $vid_pos    = esc_attr( $tile_bg['image_position'] ?? 'center center' );
            $vid_fit    = esc_attr( $tile_bg['video_fit'] ?? 'cover' );
            $vid_cover  = ( ! empty( $tile_bg['cover_height'] ) && intval( $tile_bg['cover_height'] ) > 0 ) ? intval( $tile_bg['cover_height'] ) : 0;
            $vid_scale  = ( ! empty( $tile_bg['video_scale'] ) && intval( $tile_bg['video_scale'] ) > 100 ) ? intval( $tile_bg['video_scale'] ) / 100 : 0;
            $scale_css  = $vid_scale ? '; transform: scale(' . $vid_scale . '); transform-origin: ' . $vid_pos : '';
            if ( $vid_cover ) {
                $bg_layers_html .= '<video aria-hidden="true" style="position: absolute; top: 0; left: 0; width: 100%; height: ' . $vid_cover . 'px; object-fit: ' . $vid_fit . '; object-position: ' . $vid_pos . '; pointer-events: none' . $scale_css . '" autoplay muted loop playsinline';
            } else {
                $bg_layers_html .= '<video aria-hidden="true" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: ' . $vid_fit . '; object-position: ' . $vid_pos . '; pointer-events: none' . $scale_css . '" autoplay muted loop playsinline';
            }
            if ( $vid_poster ) $bg_layers_html .= ' poster="' . $vid_poster . '"';
            // Parallax di SOLO sfondo anche per il VIDEO (scale/blur/opacity): trasforma il
            // layer video senza toccare il contenuto della sezione — a differenza del parallax
            // di sezione (element parallax) che trasforma l'intero <section> figli inclusi.
            // bgx/bgy sono no-op su <video> (non hanno background-position).
            $bg_layers_html .= $this->anim->build_uk_parallax_attr( $tile_bg );
            $bg_layers_html .= '><source src="' . $vid_url . '" type="' . $this->get_video_mime( $vid_url ) . '"></video>';
        }
        if ( $has_bg_gallery ) {
            $bg_layers_html .= $this->render_bg_gallery( $tile_bg );
        }
        if ( $has_overlay ) {
            $ov_color   = esc_attr( $tile_bg['overlay_color'] ?? '#000000' );
            $ov_opacity = intval( $tile_bg['overlay_opacity'] ) / 100;
            $bg_layers_html .= '<div class="uk-position-cover" style="background-color: ' . $ov_color . '; opacity: ' . $ov_opacity . '; pointer-events: none" aria-hidden="true"></div>';
        }

        // bg_scope=section: emit bg layers as siblings of the container (full edge-to-edge bg)
        if ( $has_any_bg && $bg_scope === 'section' ) {
            $html .= $bg_layers_html;
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

        if ( $has_any_bg ) {
            $container_class .= ' uk-position-relative';
            $html .= '<div class="' . esc_attr( $container_class ) . '" style="z-index: 1">';
        } else {
            $html .= '<div class="' . esc_attr( $container_class ) . '">';
        }

        // bg_scope=container: emit bg layers INSIDE the container (limited to container width).
        // Wrap them so they align with the content-box (excluding container padding) — otherwise
        // uk-position-cover would extend through the container padding and look wider than uk-grid content.
        if ( $has_any_bg && $bg_scope === 'container' ) {
            $html .= '<div class="olo-bg-in-container">' . $bg_layers_html . '</div>';
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

        // Background handling — vedi commento in render_section_node: fallback su $s['bg'].
        $tile_bg      = $this->css->get_effective_bg( $style );
        if ( ( $tile_bg['type'] ?? 'none' ) === 'none' && ! empty( $s['bg']['type'] ) && $s['bg']['type'] !== 'none' ) {
            $tile_bg = $this->css->get_effective_bg( [ 'bg' => $s['bg'] ] );
        }
        $has_bg_image   = ( $tile_bg['type'] === 'image' && ! empty( $tile_bg['image_url'] ) );
        $has_bg_video   = ( $tile_bg['type'] === 'video' && ! empty( $tile_bg['video_url'] ) );
        $has_bg_gallery = ( $tile_bg['type'] === 'gallery' && ! empty( $tile_bg['gallery_images'] ) && is_array( $tile_bg['gallery_images'] ) );
        $has_bg_any     = ( $tile_bg['type'] !== 'none' );
        $has_overlay    = ( $has_bg_any && ! empty( $tile_bg['overlay_opacity'] ) && intval( $tile_bg['overlay_opacity'] ) > 0 );

        // Row spacing/decorations — apply_flex=false: il flex va sul <div uk-grid>
        // interno (vedi $row_flex_styles più sotto), non sul wrapper esterno.
        // Pre-calc $pos_mode: ci serve per $has_positioning (riga successiva).
        $pos_mode = $advanced['position_mode'] ?? 'static';
        $row_spacing_styles = [];
        $this->apply_common_box_styles( $row_spacing_styles, $style, $s, $advanced, [ 'apply_flex' => false ] );

        // Video cover height — row/section-specific (l'helper non gestisce bg layers).
        if ( $has_bg_video && ! empty( $tile_bg['cover_height'] ) && intval( $tile_bg['cover_height'] ) > 0 ) {
            $row_spacing_styles[] = 'min-height: ' . intval( $tile_bg['cover_height'] ) . 'px';
        }

        // Wrapper for row background or spacing
        $has_border_radius = ! empty( $style['border_radius'] );
        $has_border = $this->wrapper_has_border( $style );
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
                $bg_css = $this->css->get_bg_inline_css( $tile_bg );
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
                $wrapper_styles[] = $this->safe_inline_css( $advanced['custom_css'] );
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
            $wrapper_classes[] = 'olo-visible'; // applicata subito (no IntersectionObserver dependency)
            if ( ! empty( $s['entrance_stagger'] ) ) {
                $stagger_delay = intval( $s['entrance_stagger_delay'] ?? 100 );
                $stagger_delay = max( 25, min( 500, $stagger_delay ) );
                $wrapper_classes[] = 'olo-stagger-parent';
                $wrapper_styles[] = '--olo-stagger-delay: ' . $stagger_delay . 'ms';
            }
        }

        // Scrollspy & element parallax attributes for row
        $row_scrollspy_attr = $this->anim->build_scrollspy_attr( $advanced );
        $row_el_parallax_attr = $this->anim->build_element_parallax_attr( $advanced );
        $row_mouse_attrs = $this->anim->build_mouse_attrs( $advanced );
        $row_spotlight_attr = $this->anim->build_spotlight_attr( $advanced );
        // Tutti gli attributi-effetto della row in un'unica stringa: vanno sul
        // wrapper quando esiste, altrimenti direttamente sul nodo griglia
        // (prima i mouse attrs venivano calcolati ma mai stampati, e le row
        // senza wrapper perdevano anche lo spotlight).
        $row_fx_attrs = $row_scrollspy_attr . $row_el_parallax_attr . $row_mouse_attrs . $row_spotlight_attr;

        // Open row wrapper (for background)
        if ( $needs_wrapper ) {
            $html .= '<div id="' . esc_attr( $row_css_id ) . '" class="' . esc_attr( implode( ' ', $wrapper_classes ) ) . '"';
            if ( $wrapper_styles ) {
                $html .= ' style="' . esc_attr( implode( '; ', $wrapper_styles ) ) . '"';
            }
            $html .= $row_fx_attrs . '>';

            // Background image layer (with optional UIkit parallax)
            if ( $has_bg_image ) {
                $bg_size = esc_attr( $tile_bg['image_size'] ?? 'cover' );
                $bg_pos  = esc_attr( $tile_bg['image_position'] ?? 'center center' );

                $html .= '<div class="uk-position-cover" style="background-image: url(' . esc_url( $tile_bg['image_url'] ) . '); background-size: ' . $bg_size . '; background-position: ' . $bg_pos . '; background-repeat: no-repeat"';
                $html .= $this->anim->build_uk_parallax_attr( $tile_bg );
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

        // Flex container overrides for the row grid (direction, justify, align, wrap, gap).
        // Helper unificato — un eventuale `display: flex` aggiuntivo è no-op perché
        // .uk-grid ce l'ha già; gli altri decls (flex-direction/justify-content/...)
        // sono i veri override.
        $row_flex_styles = $this->css->build_flex_container_css( $s );

        // Grid — if no wrapper, put scrollspy/parallax on the grid div itself
        $grid_extra_attrs = $needs_wrapper ? '' : $row_fx_attrs;
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
            $grid_extra_attrs = $needs_wrapper ? '' : $row_fx_attrs;
            $grid_class_list = [];
            if ( $stack ) $grid_class_list[] = 'olo-grid-stack';
            $grid_class_attr = ! empty( $grid_class_list ) ? ' class="' . esc_attr( implode( ' ', $grid_class_list ) ) . '"' : '';
            $html .= '<div' . $grid_class_attr . ' style="' . esc_attr( implode( '; ', $grid_css_parts ) ) . '"' . $grid_extra_attrs . '>';

            // Loop mode: repeat children for each post from WP_Query
            $loop_enabled = ! empty( $s['loop_enabled'] );
            $loop_pagination_html = '';
            if ( $loop_enabled ) {
                $row_id_short = substr( md5( $node['id'] ?? wp_rand() ), 0, 8 );
                $current_page = isset( $_GET[ 'olo_p_' . $row_id_short ] ) ? max( 1, intval( $_GET[ 'olo_p_' . $row_id_short ] ) ) : 1;
                $loop_query = $this->run_row_loop_query( $s, $current_page, true );
                $html .= $this->render_row_loop_children( $node['children'] ?? [], $loop_query->posts, $manager, $template_id, $hover_css_rules, $tile_counter, true );
                $loop_pagination_html = $this->render_row_loop_pagination( $s, $current_page, intval( $loop_query->max_num_pages ), $row_id_short );
                // Marca il container della row con data-olo-loop-row così il JS Load More
                // sa dove appendere i nuovi children (li appende al wrapper interno).
                if ( ( $s['loop_pagination'] ?? 'none' ) === 'load_more' ) {
                    $html = preg_replace(
                        '/<div(' . preg_quote( $grid_class_attr, '/' ) . ')/',
                        '<div data-olo-loop-row-container="' . esc_attr( $row_id_short ) . '" data-olo-loop-template-id="' . intval( $template_id ) . '"$1',
                        $html, 1
                    );
                }
            } else {
                foreach ( $node['children'] ?? [] as $child ) {
                    $html .= $this->render_node( $child, $manager, $template_id, $hover_css_rules, $tile_counter, true );
                }
            }

            $html .= '</div>';
            $html .= $loop_pagination_html;

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
            $grid_extra_attrs = $needs_wrapper ? '' : $row_fx_attrs;
            $html .= '<div' . $class_attr . ' ' . $uk_grid . $grid_style_attr . $grid_extra_attrs . '>';

            // Loop mode: repeat children for each post from WP_Query
            $loop_enabled_flex = ! empty( $s['loop_enabled'] );
            $loop_pagination_html_flex = '';
            if ( $loop_enabled_flex ) {
                $row_id_short_flex = substr( md5( $node['id'] ?? wp_rand() ), 0, 8 );
                $current_page_flex = isset( $_GET[ 'olo_p_' . $row_id_short_flex ] ) ? max( 1, intval( $_GET[ 'olo_p_' . $row_id_short_flex ] ) ) : 1;
                $loop_query_flex   = $this->run_row_loop_query( $s, $current_page_flex, true );
                $html .= $this->render_row_loop_children( $node['children'] ?? [], $loop_query_flex->posts, $manager, $template_id, $hover_css_rules, $tile_counter, false );
                $loop_pagination_html_flex = $this->render_row_loop_pagination( $s, $current_page_flex, intval( $loop_query_flex->max_num_pages ), $row_id_short_flex );
                // Marca il container per il Load More JS
                if ( ( $s['loop_pagination'] ?? 'none' ) === 'load_more' ) {
                    $html = preg_replace(
                        '/<div(' . preg_quote( $class_attr, '/' ) . ' ' . preg_quote( $uk_grid, '/' ) . ')/',
                        '<div data-olo-loop-row-container="' . esc_attr( $row_id_short_flex ) . '" data-olo-loop-template-id="' . intval( $template_id ) . '"$1',
                        $html, 1
                    );
                }
            } else {
                foreach ( $node['children'] ?? [] as $child ) {
                    $html .= $this->render_node( $child, $manager, $template_id, $hover_css_rules, $tile_counter );
                }
            }

            $html .= '</div>';
            $html .= $loop_pagination_html_flex;
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
     * Reso PUBBLICO per consentire al REST endpoint Load More di riusare
     * la stessa logica di costruzione args.
     *
     * @param array $s            Row settings containing loop_* keys.
     * @param int   $current_page Pagina corrente (1-based) per la paginazione.
     * @param bool  $return_query Se true ritorna l'oggetto WP_Query invece dei soli posts.
     * @return WP_Post[]|WP_Query  Array di post objects (default) oppure l'intero WP_Query.
     */
    public function run_row_loop_query( $s, $current_page = 1, $return_query = false ) {
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
            'paged'          => max( 1, intval( $current_page ) ),
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
        return $return_query ? $query : $query->posts;
    }

    /**
     * Renderizza la paginazione del Row Loop (numerica o bottone Load More).
     * Riusa la classe `.olo-btn-link` del tile button per coerenza visiva del bottone.
     *
     * @param array  $s            Settings della Row.
     * @param int    $current_page Pagina corrente.
     * @param int    $max_pages    Numero totale di pagine.
     * @param string $row_id       Identificatore univoco della Row (per query var + data attr).
     * @return string  HTML della paginazione (vuoto se non applicabile).
     */
    private function render_row_loop_pagination( $s, $current_page, $max_pages, $row_id ) {
        $mode = $s['loop_pagination'] ?? 'none';
        if ( $mode === 'none' || $max_pages <= 1 ) return '';

        $align = in_array( $s['loop_pagination_align'] ?? 'center', [ 'left', 'center', 'right' ], true )
            ? $s['loop_pagination_align'] : 'center';
        $align_css = $align === 'left' ? 'flex-start' : ( $align === 'right' ? 'flex-end' : 'center' );

        $wrapper_style = 'display:flex;justify-content:' . $align_css . ';margin-top:24px;';

        if ( $mode === 'numbers' ) {
            $qvar = 'olo_p_' . $row_id;
            $links = paginate_links( [
                'base'      => add_query_arg( $qvar, '%#%' ),
                'format'    => '',
                'current'   => $current_page,
                'total'     => $max_pages,
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;',
                'type'      => 'array',
            ] );
            if ( empty( $links ) ) return '';
            $items = '';
            foreach ( $links as $lnk ) {
                $items .= '<span class="olo-loop-page-item">' . $lnk . '</span>';
            }
            return '<nav class="olo-loop-pagination olo-loop-pagination--numbers" style="' . esc_attr( $wrapper_style ) . '">'
                . $items . '</nav>';
        }

        if ( $mode === 'load_more' ) {
            // Mostra il bottone solo se ci sono altre pagine da caricare
            if ( $current_page >= $max_pages ) return '';
            $label = sanitize_text_field( $s['loop_load_more_label'] ?? '' ) ?: __( 'Carica altri', 'olobuild' );
            // Riusa la classe `.olo-btn-link` del tile button per coerenza visiva.
            // Wrapper `.olo-button` applica gli stili di centratura/padding del button.
            $btn = '<a href="#" role="button"'
                . ' class="olo-btn-link olo-loop-load-more"'
                . ' data-olo-loop-row="' . esc_attr( $row_id ) . '"'
                . ' data-olo-loop-page="' . intval( $current_page ) . '"'
                . ' data-olo-loop-max="' . intval( $max_pages ) . '"'
                . ' style="display:inline-block;padding:14px 32px;background-color:var(--olo-color-primary,#6366F1);color:var(--olo-color-primary-contrast,#FFFFFF);border-radius:6px;text-decoration:none;font-weight:600;cursor:pointer;transition:opacity .2s ease;">'
                . '<span class="olo-loop-load-more-label">' . esc_html( $label ) . '</span>'
                . '</a>';
            return '<div class="olo-loop-pagination olo-loop-pagination--load-more" style="' . esc_attr( $wrapper_style ) . '">'
                . $btn . '</div>';
        }

        return '';
    }

    /**
     * Renderizza il template del Loop una volta per ogni post.
     *
     * IMPORTANTE: il "template" del Loop è la PRIMA colonna della Row.
     * Le altre colonne eventualmente presenti vengono ignorate quando il loop è
     * attivo. Questo modello (Elementor-style):
     *   - Coerente con come l'utente pensa al loop ("una card si ripete N volte")
     *   - Layout della disposizione gestito dalla Row (es. 33-33-33 + 6 post = 2 righe da 3)
     *   - Coerente col modello mentale "Loop Item = la prima colonna"
     *
     * Usato sia dal render normale che dal REST Load More.
     *
     * @return string  HTML concatenato del template renderizzato per ogni post.
     */
    public function render_row_loop_children( $children, $loop_posts, $manager, $template_id, &$hover_css_rules, &$tile_counter, $parent_is_grid = false ) {
        if ( empty( $loop_posts ) || empty( $children ) ) return '';
        // Solo il primo child viene usato come template del singolo card del loop.
        $template_child = $children[0];
        global $post;
        $old_post = $post;
        $html = '';
        foreach ( $loop_posts as $loop_post ) {
            $post = $loop_post;
            setup_postdata( $post );
            $html .= $this->render_node( $template_child, $manager, $template_id, $hover_css_rules, $tile_counter, $parent_is_grid );
        }
        $post = $old_post;
        if ( $old_post ) { setup_postdata( $old_post ); } else { wp_reset_postdata(); }
        return $html;
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

        // Shadow CLASS — column applica sempre (no branch has_bg_any come element).
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

        // Helper unificato: margin/padding/border-radius/border/opacity/flex/transform/
        // box-shadow inline/text-shadow/backdrop/overflow/dimensions/mask/custom_css/position.
        $this->apply_common_box_styles( $inline_styles, $style, $s, $advanced );

        // Background handling for column (post-helper: gestione layer image/video/overlay).
        // Vedi commento in render_section_node: fallback su $s['bg'].
        $col_bg      = $this->css->get_effective_bg( $style );
        if ( ( $col_bg['type'] ?? 'none' ) === 'none' && ! empty( $s['bg']['type'] ) && $s['bg']['type'] !== 'none' ) {
            $col_bg = $this->css->get_effective_bg( [ 'bg' => $s['bg'] ] );
        }
        $has_col_bg_image = ( $col_bg['type'] === 'image' && ! empty( $col_bg['image_url'] ) );
        $has_col_bg_video = ( $col_bg['type'] === 'video' && ! empty( $col_bg['video_url'] ) );
        $has_col_bg_any   = ( $col_bg['type'] !== 'none' );
        $has_col_overlay  = ( $has_col_bg_any && ! empty( $col_bg['overlay_opacity'] ) && intval( $col_bg['overlay_opacity'] ) > 0 );

        if ( ! $has_col_bg_image && ! $has_col_bg_video && $col_bg['type'] !== 'none' ) {
            $bg_css = $this->css->get_bg_inline_css( $col_bg );
            if ( $bg_css ) $inline_styles[] = $bg_css;
        }
        if ( $has_col_bg_image || $has_col_bg_video ) {
            $classes[] = 'uk-position-relative';
            $inline_styles[] = 'overflow: clip';
        }

        // v3.55.48 — sticky column ri-attivata. Necessaria perché lo sticky della
        // tile element (Avanzate → Sticky) raramente funziona per layout immagine
        // + testo: il parent immediato della tile è la column wrapper, che spesso
        // ha overflow:clip (per bg image) o altezza non stretched. La column invece
        // è child diretto della row (uk-grid) che è sempre flex container con
        // height = max child height. position:sticky sulla column funziona quindi
        // come atteso: si blocca all'offset, si sblocca quando la row termina.
        if ( ! empty( $s['sticky'] ) ) {
            $sticky_offset = max( 0, intval( $s['sticky_offset'] ?? 50 ) );
            $inline_styles[] = 'position: sticky';
            // Top dinamico: --olo-sticky-top-offset viene aggiornata da
            // print_sticky_offset_script() in base all'altezza dell'header sticky.
            // Nel builder la var resta 0 (header forzato a position:relative).
            $inline_styles[] = 'top: calc(var(--olo-sticky-top-offset, 0px) + ' . $sticky_offset . 'px)';
            $inline_styles[] = 'align-self: start';
            $inline_styles[] = 'z-index: 5';
            self::$needs_sticky_offset_script = true;
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
            $classes[] = 'olo-visible'; // applicata subito: l'animation parte al page-load (no IntersectionObserver dependency)
            if ( ! empty( $s['entrance_stagger'] ) ) {
                $stagger_delay = intval( $s['entrance_stagger_delay'] ?? 100 );
                $stagger_delay = max( 25, min( 500, $stagger_delay ) );
                $classes[] = 'olo-stagger-parent';
                $inline_styles[] = '--olo-stagger-delay: ' . $stagger_delay . 'ms';
            }
        }

        // Scrollspy & element parallax attributes for column
        $col_scrollspy_attr = $this->anim->build_scrollspy_attr( $advanced );
        $col_el_parallax_attr = $this->anim->build_element_parallax_attr( $advanced );
        $col_mouse_attrs = $this->anim->build_mouse_attrs( $advanced );

        $html = '<div id="' . esc_attr( $col_css_id ) . '" class="' . esc_attr( implode( ' ', $classes ) ) . '"';
        if ( ! empty( $inline_styles ) ) {
            $html .= ' style="' . esc_attr( implode( '; ', $inline_styles ) ) . '"';
        }
        $html .= $col_scrollspy_attr . $col_el_parallax_attr . $col_mouse_attrs . $this->anim->build_spotlight_attr( $advanced ) . '>';

        // Background image cover for column
        if ( $has_col_bg_image ) {
            $bg_size = esc_attr( $col_bg['image_size'] ?? 'cover' );
            $bg_pos  = esc_attr( $col_bg['image_position'] ?? 'center center' );
            $html .= '<div class="uk-position-cover" style="background-image: url(' . esc_url( $col_bg['image_url'] ) . '); background-size: ' . $bg_size . '; background-position: ' . $bg_pos . '; background-repeat: no-repeat"';
            $html .= $this->anim->build_uk_parallax_attr( $col_bg );
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
        // intval() previene CSS injection via tile settings (es. "10;background:url(...)").
        // I valori margin/padding sono SEMPRE numeri interi (px) — qualsiasi cosa diversa
        // viene troncata a 0.
        if ( ! empty( $style['margin_top'] ) )     $inline_styles[] = 'margin-top: ' . intval( $style['margin_top'] ) . 'px';
        if ( ! empty( $style['margin_right'] ) )   $inline_styles[] = 'margin-right: ' . intval( $style['margin_right'] ) . 'px';
        if ( ! empty( $style['margin_bottom'] ) )  $inline_styles[] = 'margin-bottom: ' . intval( $style['margin_bottom'] ) . 'px';
        if ( ! empty( $style['margin_left'] ) )    $inline_styles[] = 'margin-left: ' . intval( $style['margin_left'] ) . 'px';
        if ( ! empty( $style['padding_top'] ) )    $inline_styles[] = 'padding-top: ' . intval( $style['padding_top'] ) . 'px';
        if ( ! empty( $style['padding_right'] ) )  $inline_styles[] = 'padding-right: ' . intval( $style['padding_right'] ) . 'px';
        if ( ! empty( $style['padding_bottom'] ) ) $inline_styles[] = 'padding-bottom: ' . intval( $style['padding_bottom'] ) . 'px';
        if ( ! empty( $style['padding_left'] ) )   $inline_styles[] = 'padding-left: ' . intval( $style['padding_left'] ) . 'px';

        // Background
        $tile_bg = $this->css->get_effective_bg( $style );
        if ( $tile_bg['type'] !== 'none' && $tile_bg['type'] !== 'image' && $tile_bg['type'] !== 'video' ) {
            $bg_css = $this->css->get_bg_inline_css( $tile_bg );
            if ( $bg_css ) $inline_styles[] = $bg_css;
        }

        // Border radius
        if ( ! empty( $style['border_radius'] ) ) $inline_styles[] = $this->css->build_border_radius_css( $style['border_radius'] );

        // Border (sistema unificato: oggetto 4-side + fallback legacy 3-key)
        $border_css = $this->build_wrapper_border_css( $style );
        if ( $border_css ) $inline_styles[] = $border_css;

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
        // intval() previene CSS injection via tile settings (es. "10;background:url(...)").
        // I valori margin/padding sono SEMPRE numeri interi (px) — qualsiasi cosa diversa
        // viene troncata a 0.
        if ( ! empty( $style['margin_top'] ) )     $inline_styles[] = 'margin-top: ' . intval( $style['margin_top'] ) . 'px';
        if ( ! empty( $style['margin_right'] ) )   $inline_styles[] = 'margin-right: ' . intval( $style['margin_right'] ) . 'px';
        if ( ! empty( $style['margin_bottom'] ) )  $inline_styles[] = 'margin-bottom: ' . intval( $style['margin_bottom'] ) . 'px';
        if ( ! empty( $style['margin_left'] ) )    $inline_styles[] = 'margin-left: ' . intval( $style['margin_left'] ) . 'px';
        if ( ! empty( $style['padding_top'] ) )    $inline_styles[] = 'padding-top: ' . intval( $style['padding_top'] ) . 'px';
        if ( ! empty( $style['padding_right'] ) )  $inline_styles[] = 'padding-right: ' . intval( $style['padding_right'] ) . 'px';
        if ( ! empty( $style['padding_bottom'] ) ) $inline_styles[] = 'padding-bottom: ' . intval( $style['padding_bottom'] ) . 'px';
        if ( ! empty( $style['padding_left'] ) )   $inline_styles[] = 'padding-left: ' . intval( $style['padding_left'] ) . 'px';

        // Background
        $tile_bg = $this->css->get_effective_bg( $style );
        if ( $tile_bg['type'] !== 'none' && $tile_bg['type'] !== 'image' && $tile_bg['type'] !== 'video' ) {
            $bg_css = $this->css->get_bg_inline_css( $tile_bg );
            if ( $bg_css ) $inline_styles[] = $bg_css;
        }

        // Border radius
        if ( ! empty( $style['border_radius'] ) ) $inline_styles[] = $this->css->build_border_radius_css( $style['border_radius'] );

        // Border (sistema unificato: oggetto 4-side + fallback legacy 3-key)
        $border_css = $this->build_wrapper_border_css( $style );
        if ( $border_css ) $inline_styles[] = $border_css;

        // Sticky column support
        if ( ! empty( $s['sticky'] ) ) {
            $sticky_offset = intval( $s['sticky_offset'] ?? 20 );
            $inline_styles[] = 'position: sticky';
            $inline_styles[] = 'top: calc(var(--olo-sticky-top-offset, 0px) + ' . $sticky_offset . 'px)';
            $inline_styles[] = 'align-self: flex-start';
            self::$needs_sticky_offset_script = true;
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

        // In builder mode, force panel always visible, in normal flow, so users can edit it.
        // Also clear placement positioning (top/left/etc.) to keep panel inline.
        if ( $this->builder_mode ) {
            $settings = array_merge( $settings, [
                'trigger_mode'  => 'always',
                'position'      => 'relative',
                'placement'     => 'top-left',
                'offset_x'      => '0',
                'offset_y'      => '0',
                'custom_top'    => '',
                'custom_left'   => '',
                'custom_bottom' => '',
                'custom_right'  => '',
                'width'         => '100%',
                'height'        => '',
                'z_index'       => '0',
                '_builder_mode' => true,
            ] );
        }

        // Render opening wrapper (panel div with styles, trigger button, close button)
        $html = Olo_Tile_Utils::process_dynamic_tags( $tile_instance->render( $settings, $node['style'] ?? [] ) );

        $children = $node['children'] ?? [];

        // Builder mode: identifying banner so users know this is a floating panel
        // (in frontend it would be positioned/floating; in editor it's shown inline).
        if ( $this->builder_mode ) {
            $orig_placement = $node['settings']['placement'] ?? 'bottom-right';
            $orig_position  = $node['settings']['position'] ?? 'fixed';
            $pos_label = ucfirst( str_replace( '-', ' ', $orig_placement ) );
            $mode_label = ucfirst( $orig_position );
            $html .= '<div class="olo-fp-builder-banner" style="display:flex;align-items:center;gap:8px;padding:6px 10px;margin:-8px -8px 12px -8px;background:rgba(232,98,42,0.12);border-radius:6px;font-size:11px;font-weight:600;color:#c2410c;text-transform:uppercase;letter-spacing:0.5px;">'
                   . '<span>📌 ' . esc_html__( 'Pannello flottante', 'olobuild' ) . '</span>'
                   . '<span style="opacity:0.6;font-weight:400;text-transform:none;letter-spacing:0;">→ ' . esc_html( $mode_label ) . ' · ' . esc_html( $pos_label ) . '</span>'
                   . '</div>';
        }

        // Builder mode: when empty, inject a visible drop-zone placeholder so users can
        // see where to drop tiles (the panel is otherwise an empty box).
        if ( $this->builder_mode && empty( $children ) ) {
            $fp_id = esc_attr( $node['id'] ?? '' );
            $html .= '<div class="olo-fp-builder-empty" data-olo-fp-empty="' . $fp_id . '" style="min-height:120px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;border:2px dashed rgba(232,98,42,0.6);border-radius:8px;padding:20px;background:rgba(232,98,42,0.06);color:#e8622a;font-size:13px;font-weight:500;text-align:center;cursor:pointer;">'
                   . '<span style="font-size:32px;font-weight:300;line-height:1;pointer-events:none;">+</span>'
                   . '<span style="pointer-events:none;">' . esc_html__( 'Trascina qui contenuti del pannello', 'olobuild' ) . '</span>'
                   . '<span style="font-size:10px;opacity:0.7;text-transform:uppercase;letter-spacing:0.5px;pointer-events:none;">' . esc_html__( 'O clicca per aprire il finder', 'olobuild' ) . '</span>'
                   . '</div>';
        }

        // Render children inside the panel
        foreach ( $children as $child ) {
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
        $settings = $node['settings'] ?? [];

        // Legacy tile migration filter: permette ad altri plugin (es. olo-booking)
        // di remappare type+settings di tile legacy ai nuovi equivalenti senza
        // duplicare le classi PHP. Il filter riceve [type, settings] e può
        // restituire la stessa coppia modificata.
        // Esempio in olo-booking:
        //   add_filter( 'olo_tile_legacy_migrate', function( $tile, $node ) {
        //       if ( $tile['type'] === 'servicegallery' ) {
        //           $tile['type']     = 'ac-gallery';
        //           $tile['settings'] = $remapped;
        //       }
        //       return $tile;
        //   }, 10, 2 );
        $migrated = apply_filters(
            'olo_tile_legacy_migrate',
            [ 'type' => $type, 'settings' => $settings ],
            $node
        );
        if ( is_array( $migrated ) ) {
            if ( isset( $migrated['type'] ) && is_string( $migrated['type'] ) ) {
                $type = $migrated['type'];
            }
            if ( isset( $migrated['settings'] ) && is_array( $migrated['settings'] ) ) {
                $settings = $migrated['settings'];
            }
        }

        $tile_instance = $manager->get_tile( $type );
        if ( ! $tile_instance ) return '';

        $style    = $node['style'] ?? [];
        $advanced = $node['advanced'] ?? [];

        // Builder mode flag: i tile possono leggere $settings['_builder_mode'] per
        // disabilitare comportamenti pesanti durante l'editing (es. video autoplay,
        // form submit, mapJS init, ecc.).
        if ( $this->builder_mode ) {
            $settings['_builder_mode'] = true;
        }

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

        // Migrazione legacy hero: prima della v3.55.13 la tile hero aveva il proprio
        // sistema di sfondo nei settings (bg_type/bg_color/bg_image/bg_video/overlay_*).
        // Ora usa style.bg come tutti gli altri tile. Convertiamo on-the-fly i template
        // salvati prima della migrazione, così wrapper e overlay vengono renderizzati.
        if ( $type === 'hero' && empty( $style['bg'] ) && empty( $settings['bg'] ) && ! empty( $settings['bg_type'] ) ) {
            $settings['bg'] = $this->migrate_legacy_hero_bg( $settings );
        }

        // Per element tile il field "bg" (Sfondo creativo) è dichiarato in fields[] del config,
        // quindi viene salvato in node.settings — non in node.style come per le sezioni.
        // Merge: se settings.bg/bg_color è settato e style non lo è, usa quello dei settings.
        $bg_source = $style;
        if ( empty( $bg_source['bg'] ) && ! empty( $settings['bg'] ) )            $bg_source['bg']       = $settings['bg'];
        if ( empty( $bg_source['bg_color'] ) && ! empty( $settings['bg_color'] ) ) $bg_source['bg_color'] = $settings['bg_color'];

        // v1.0.55 — Tile ATOMICHE (button/icon/divider/spacer/togglebtn): wrapper SEMPRE
        // trasparente, ignora qualsiasi bg in style/settings (il bg appartiene all'elemento
        // interno, non al wrapper). Specchio della guardia ATOMIC_TILE_TYPES nel JS
        // useBackgroundStyle.js — regola HARD: nessun pulsante colora lo spazio circostante.
        $ATOMIC_TILES = [ 'button', 'icon', 'divider', 'spacer', 'togglebtn' ];
        if ( in_array( $type, $ATOMIC_TILES, true ) ) {
            $bg_source = [ 'bg' => [ 'type' => 'none' ] ];
        }

        $tile_bg      = $this->css->get_effective_bg( $bg_source );
        $is_fullwidth = ! empty( $style['full_width'] );
        $has_bg_image   = ( $tile_bg['type'] === 'image' && ! empty( $tile_bg['image_url'] ) );
        $has_bg_video   = ( $tile_bg['type'] === 'video' && ! empty( $tile_bg['video_url'] ) );
        $has_bg_gallery = ( $tile_bg['type'] === 'gallery' && ! empty( $tile_bg['gallery_images'] ) && is_array( $tile_bg['gallery_images'] ) );
        $has_bg_any     = ( $tile_bg['type'] !== 'none' );
        $has_overlay    = ( $has_bg_any && ! empty( $tile_bg['overlay_opacity'] ) && intval( $tile_bg['overlay_opacity'] ) > 0 );

        // Build inline styles (custom values that UIkit can't handle)
        $inline_styles = [];

        // Background base (solo se non c'è image/video — quelli sono layer separati)
        if ( ! $has_bg_image && ! $has_bg_video ) {
            $bg_css = $this->css->get_bg_inline_css( $tile_bg );
            if ( $bg_css ) $inline_styles[] = $bg_css;
        }

        // v1.0.55 — Per tile atomiche il wrapper NON eredita text_color/border_radius/shadow:
        // quei valori vanno SOLO sull'elemento interno, non sull'area circostante.
        $is_atomic_tile = in_array( $type, $ATOMIC_TILES, true );

        // Text color (preset stilistici applicano color al wrapper → i discendenti ereditano).
        if ( ! $is_atomic_tile && ! empty( $style['text_color'] ) ) {
            $inline_styles[] = 'color: ' . esc_attr( $style['text_color'] );
        }

        // UIkit shadow class — solo per elementi con sfondo (box-shadow segue border-radius).
        // Per elementi trasparenti, drop-shadow filter viene applicato dopo l'helper.
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

        // $pos_mode è usato anche più sotto (scrollspy_attr, sticky_attr, fixed-position
        // body-mount) — lo calcoliamo qui per averlo disponibile fuori dall'helper.
        $pos_mode = $advanced['position_mode'] ?? 'static';

        // Helper unificato: margin/padding/border-radius/border/opacity/flex/transform/
        // box-shadow inline/text-shadow/backdrop/overflow/dimensions/mask/custom_css/position.
        // `apply_box_shadow=false` per element trasparenti — usano drop-shadow filter sotto.
        // v1.0.55 — Per atomic: passiamo style filtrato (no border_radius/border/shadow), così
        // l'helper applica solo margin/padding/dimensions/flex/transform/etc. al wrapper.
        $box_style = $style;
        if ( $is_atomic_tile ) {
            unset( $box_style['border_radius'], $box_style['border'], $box_style['shadow'],
                   $box_style['box_shadow_h'], $box_style['box_shadow_v'], $box_style['box_shadow_blur'],
                   $box_style['box_shadow_spread'], $box_style['box_shadow_color'], $box_style['box_shadow_inset'] );
        }
        $this->apply_common_box_styles(
            $inline_styles, $box_style, $settings, $advanced,
            [ 'apply_box_shadow' => (bool) $has_bg_any && ! $is_atomic_tile ]
        );

        // Drop-shadow filter per element trasparenti (segue forma SVG/icone via clip-path).
        // Non per atomic: il drop-shadow apparirebbe attorno all'area del wrapper.
        if ( ! $has_bg_any && ! $is_atomic_tile ) {
            $drop_shadow = $this->css->build_drop_shadow_css( $style );
            if ( $drop_shadow ) {
                $inline_styles[] = 'filter: ' . $drop_shadow;
            }
        }

        $style_attr = implode( '; ', $inline_styles );

        // Build classes
        $classes = [ 'olo-frontend-tile' ];
        if ( $shadow_class ) $classes[] = $shadow_class;
        if ( $is_fullwidth ) $classes[] = 'olo-tile-fullwidth';
        // Larghezza adattata al contenuto (Avanzate → Posizionamento): tile
        // inline-block, più tile "adattate" consecutive si affiancano.
        if ( ( $advanced['tile_width'] ?? 'full' ) === 'inline' ) $classes[] = 'olo-tile-inline';
        if ( $has_bg_image || $has_bg_video || $has_bg_gallery || $has_overlay ) { $classes[] = 'uk-position-relative'; $inline_styles[] = 'overflow: clip'; }
        if ( ! empty( $style['border_radius'] ) ) $inline_styles[] = 'overflow: clip';
        if ( ! empty( $advanced['css_classes'] ) ) {
            $classes[] = esc_attr( $advanced['css_classes'] );
        }

        // Entrance animation
        $entrance = $settings['entrance_animation'] ?? 'none';
        if ( $entrance && $entrance !== 'none' ) {
            $classes[] = 'olo-entrance-' . sanitize_html_class( $entrance );
            $classes[] = 'olo-visible'; // applicata subito: l'animation parte al page-load (no IntersectionObserver dependency)
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

        // Infinite/loop animation (Galleggiamento, ecc.) — Avanzate → "Animazione continua".
        // Le sezioni la rendono già; qui la abilitiamo anche per le TILE (es. badge flottante
        // su un'immagine). Usa il builder per-id parametrico (ampiezza/ritardo/reduced-motion).
        $elem_inf_anim_css = $this->css->build_infinite_animation_css( $advanced, $css_id );
        if ( $elem_inf_anim_css ) {
            $hover_css_rules[] = $elem_inf_anim_css;
        }

        // Scrollspy & element parallax attributes (skip for fixed tiles — handled by sentinel JS)
        $elem_scrollspy_attr = ( $pos_mode === 'fixed' ) ? '' : $this->anim->build_scrollspy_attr( $advanced );
        $elem_el_parallax_attr = $this->anim->build_element_parallax_attr( $advanced );

        // Sticky — skip if tile is already fixed-positioned or is a megamenu
        //
        // v3.55.47 — switch da `uk-sticky` JS (sticky GLOBALE, non si sblocca mai)
        // a CSS nativo `position: sticky` che è LIMITATO al parent: quando il
        // container genitore termina, l'elemento si sblocca e torna a scorrere
        // naturalmente. È il comportamento atteso per layout immagine+testo.
        //
        // Requisiti `position: sticky` CSS:
        //  1. align-self: start (no stretch) — sticky non funziona su elementi stretched
        //  2. il parent non deve avere overflow: hidden (clip è ok)
        //  3. il parent deve essere più alto dell'elemento (altrimenti niente range scroll)
        //  4. !important sull'align-self perché UIkit applica `align-self: stretch`
        //     di default a `.uk-grid > *` con specificity più alta dell'inline.
        $elem_sticky_inline_css = '';
        $sticky_on              = ! empty( $advanced['sticky'] ) || ! empty( $settings['sticky'] );
        if ( $sticky_on && $pos_mode !== 'fixed' && $type !== 'megamenu' ) {
            $sticky_pos    = $advanced['sticky_position'] ?? $settings['sticky_position'] ?? 'top';
            $sticky_offset = intval( $advanced['sticky_offset'] ?? $settings['sticky_offset'] ?? 0 );
            $sticky_mobile = $advanced['sticky_on_mobile'] ?? $settings['sticky_on_mobile'] ?? true;
            $pos_prop      = $sticky_pos === 'bottom' ? 'bottom' : 'top';
            $elem_sticky_inline_css = 'position: sticky; ' . $pos_prop . ': ' . $sticky_offset
                . 'px; align-self: start; z-index: 10';
            if ( ! $sticky_mobile ) {
                // CSS in frontend.css → @media (max-width:640px) { .olo-sticky-desktop-only{position:static!important} }
                $classes[] = 'olo-sticky-desktop-only';
            }
        }
        // Append sticky CSS a $style_attr (già assemblato a riga ~2629).
        if ( $elem_sticky_inline_css !== '' ) {
            $style_attr = $style_attr ? $style_attr . '; ' . $elem_sticky_inline_css : $elem_sticky_inline_css;
        }
        // Variabile mantenuta per compatibilità riga 2811, ora sempre vuota.
        $elem_sticky_attr = '';

        // Mouse effects data attributes (leggi da settings OPPURE advanced — il pannello
        // "Effetti mouse" del builder salva in advanced, lo styleField _shared in settings).
        $elem_mouse_attrs = '';
        if ( ! empty( $settings['mouse_tilt'] ) || ! empty( $advanced['mouse_tilt'] ) ) {
            $tilt_intensity = intval( $settings['mouse_tilt_intensity'] ?? $advanced['mouse_tilt_intensity'] ?? 15 );
            // target 'items': il tilt va sulle foto/media INTERNI (gallerie, griglie)
            // invece che sul blocco — il runtime espande l'attr sugli img/video figli.
            $tilt_target = $settings['mouse_tilt_target'] ?? $advanced['mouse_tilt_target'] ?? 'block';
            $tilt_attr   = ( 'items' === $tilt_target ) ? 'data-olo-tilt-items' : 'data-olo-tilt';
            $elem_mouse_attrs .= ' ' . $tilt_attr . '="' . $tilt_intensity . '"';
        }
        if ( ! empty( $settings['mouse_track'] ) || ! empty( $advanced['mouse_track'] ) ) {
            $track_speed = intval( $settings['mouse_track_speed'] ?? $advanced['mouse_track_speed'] ?? 3 );
            $elem_mouse_attrs .= ' data-olo-track="' . $track_speed . '"';
        }

        // Spotlight cursore — riusabile su section/column/row/element (vedi Olo_Animation_Builder::build_spotlight_attr)
        $elem_spotlight_attr = $this->anim->build_spotlight_attr( $advanced );

        // Bezier path scroll animation
        $elem_bezier_attr = '';
        if ( ! empty( $advanced['bezier_path'] ) && is_array( $advanced['bezier_path'] ) ) {
            $elem_bezier_attr = " data-olo-bezier-parallax='" . wp_json_encode( $advanced['bezier_path'] ) . "'";
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
        if ( ! empty( $settings['scroll_effect_fill'] ) ) {
            $scroll_fx['fill'] = [
                intval( $settings['scroll_fill_start'] ?? 0 ),
                intval( $settings['scroll_fill_end'] ?? 100 ),
            ];
        }
        if ( ! empty( $scroll_fx ) ) {
            $elem_scroll_fx_attr = " data-olo-scroll-fx='" . esc_attr( wp_json_encode( $scroll_fx ) ) . "'";
        }

        // ScrollAssembly (preset Parallax multi-target): genitore (data-olo-assembly) +
        // parti figlie (data-olo-part) che si "montano" su UN unico progress del genitore.
        $elem_assembly_attr = '';
        if ( ! empty( $advanced['scroll_assembly'] ) ) {
            $elem_assembly_attr .= ' data-olo-assembly';
        }
        if ( ! empty( $advanced['assembly_part'] ) ) {
            $part = [
                'x'     => intval( $advanced['assembly_from_x'] ?? 0 ),
                'y'     => intval( $advanced['assembly_from_y'] ?? 0 ),
                's'     => floatval( $advanced['assembly_from_scale'] ?? 1.2 ),
                'r'     => intval( $advanced['assembly_from_rotate'] ?? 0 ),
                'start' => max( 0, min( 1, floatval( $advanced['assembly_start'] ?? 0 ) / 100 ) ),
                'end'   => max( 0, min( 1, floatval( $advanced['assembly_end'] ?? 60 ) / 100 ) ),
            ];
            $elem_assembly_attr .= " data-olo-part='" . esc_attr( wp_json_encode( $part ) ) . "'";
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
                if ( str_contains( $pair, '=' ) ) {
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
        <div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php echo $id_attr; ?> style="<?php echo esc_attr( $style_attr ); ?>"<?php echo $elem_scrollspy_attr . $elem_el_parallax_attr . $elem_sticky_attr . $elem_mouse_attrs . $elem_bezier_attr . $elem_scroll_fx_attr . $elem_spotlight_attr . $elem_assembly_attr . $ab_test_attrs . $seo_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $id_attr esc_attr()'d at assignment; attribute fragments built above with esc_attr()/wp_json_encode()/intval() or by Olo_Animation_Builder helpers from clamped numerics ?>>
            <?php if ( $has_bg_image ) :
                $bg_size = esc_attr( $tile_bg['image_size'] ?? 'cover' );
                $bg_pos  = esc_attr( $tile_bg['image_position'] ?? 'center center' );
                $parallax_attr = $this->anim->build_uk_parallax_attr( $tile_bg );
            ?>
                <div class="uk-position-cover"
                    style="background-image: url(<?php echo esc_url( $tile_bg['image_url'] ); ?>); background-size: <?php echo $bg_size; ?>; background-position: <?php echo $bg_pos; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $bg_size/$bg_pos esc_attr()'d at assignment above ?>; background-repeat: no-repeat"
                    <?php echo $parallax_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- uk-parallax attribute built by Olo_Animation_Builder::build_uk_parallax_attr() from intval()/floatval() values ?>
                ></div>
            <?php endif; ?>

            <?php if ( $has_bg_video ) :
                $vid_url    = esc_url( $tile_bg['video_url'] );
                $vid_poster = ! empty( $tile_bg['video_poster'] ) ? esc_url( $tile_bg['video_poster'] ) : '';
                $vid_pos    = esc_attr( $tile_bg['image_position'] ?? 'center center' );
                $vid_fit    = esc_attr( $tile_bg['video_fit'] ?? 'cover' );
            ?>
                <video aria-hidden="true" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: <?php echo $vid_fit; ?>; object-position: <?php echo $vid_pos; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $vid_fit/$vid_pos esc_attr()'d and $vid_poster/$vid_url esc_url()'d at assignment above ?>; pointer-events: none" autoplay muted loop playsinline<?php if ( $vid_poster ) echo ' poster="' . $vid_poster . '"'; ?>><source src="<?php echo $vid_url; ?>" type="<?php echo esc_attr( $this->get_video_mime( $vid_url ) ); ?>"></video>
            <?php endif; ?>

            <?php if ( $has_overlay ) :
                $ov_color   = esc_attr( $tile_bg['overlay_color'] ?? '#000000' );
                $ov_opacity = intval( $tile_bg['overlay_opacity'] ) / 100;
            ?>
                <div class="uk-position-cover" style="background-color: <?php echo $ov_color; ?>; opacity: <?php echo $ov_opacity; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $ov_color esc_attr()'d at assignment above; $ov_opacity is intval()/100 ?>; pointer-events: none" aria-hidden="true"></div>
            <?php endif; ?>

            <?php if ( $this->builder_mode ) $settings['_builder_mode'] = true; ?>
            <?php if ( $has_bg_image || $has_bg_video || $has_bg_gallery || $has_overlay ) : ?>
                <div class="uk-position-relative" style="z-index: 1">
                    <?php echo Olo_Tile_Utils::process_dynamic_tags( $tile_instance->render( $settings, $node['style'] ?? [] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tile HTML assembled by the tile's own render() (each tile escapes its output); process_dynamic_tags() substitutes sanitized dynamic values ?>
                </div>
            <?php else : ?>
                <?php echo Olo_Tile_Utils::process_dynamic_tags( $tile_instance->render( $settings, $node['style'] ?? [] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tile HTML assembled by the tile's own render() (each tile escapes its output); process_dynamic_tags() substitutes sanitized dynamic values ?>
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
     * Migra i campi legacy bg_type/bg_color/bg_image/bg_video/overlay_* della tile hero
     * (formato pre-v3.55.13) all'oggetto bg unificato di BackgroundControls.
     * Chiamata on-the-fly al render — non modifica il template salvato.
     *
     * @param array $s Settings flat legacy.
     * @return array Bg object: { type, color|gradient_*|image_*|video_*, overlay_* }
     */
    private function migrate_legacy_hero_bg( $s ) {
        $type = $s['bg_type'] ?? 'solid';
        $bg   = [ 'type' => 'none' ];

        switch ( $type ) {
            case 'color':
                $bg = [ 'type' => 'solid', 'color' => $s['bg_color'] ?: '#1F2937' ];
                break;
            case 'gradient':
                $bg = [
                    'type'           => 'gradient',
                    'gradient_from'  => $s['bg_gradient_from'] ?: '#6366F1',
                    'gradient_to'    => $s['bg_gradient_to']   ?: '#8B5CF6',
                    'gradient_angle' => intval( $s['bg_gradient_angle'] ?: 135 ),
                ];
                break;
            case 'image':
                $pos_map = [
                    'center' => 'center center', 'top' => 'top center', 'bottom' => 'bottom center',
                    'left'   => 'center left',   'right' => 'center right',
                ];
                $pos = $s['bg_position'] ?? 'center';
                $bg = [
                    'type'           => 'image',
                    'image_url'      => $s['bg_image'] ?: '',
                    'image_size'     => $s['bg_size']  ?: 'cover',
                    'image_position' => $pos_map[ $pos ] ?? 'center center',
                    'image_parallax' => ! empty( $s['bg_fixed'] ),
                ];
                break;
            case 'video':
                $bg = [
                    'type'       => 'video',
                    'video_url'  => $s['bg_video'] ?: '',
                    'video_size' => $s['bg_size']  ?: 'cover',
                ];
                break;
            default:
                $bg = [ 'type' => 'solid', 'color' => '#1F2937' ];
        }

        if ( ! empty( $s['overlay'] ) ) {
            $bg['overlay_color']   = $s['overlay_color'] ?: '#000000';
            $bg['overlay_opacity'] = intval( $s['overlay_opacity'] ?: 50 );
        }

        return $bg;
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
        // v3.55.17 — hover anche su border_width e border_style (prima solo border_color era hoverable).
        if ( isset( $hover['border_width'] ) && $hover['border_width'] !== '' && $hover['border_width'] !== null ) {
            $hover_decls[] = 'border-width: ' . intval( $hover['border_width'] ) . 'px';
            $has_hover = true;
        }
        if ( ! empty( $hover['border_style'] ) ) {
            $hover_decls[] = 'border-style: ' . esc_attr( $hover['border_style'] );
            $has_hover = true;
        }
        // v3.55.20 — hover per il sistema NUOVO style.border_hover (oggetto 4-side + style + color).
        // Convive con il legacy: il nuovo vince se ha valori (color non vuoto + qualche lato > 0).
        $bh = $style['border_hover'] ?? null;
        if ( is_array( $bh ) ) {
            $bh_color = trim( $bh['color'] ?? '' );
            $bh_style = trim( $bh['style'] ?? '' );
            $bh_t  = intval( $bh['top']    ?? 0 );
            $bh_r  = intval( $bh['right']  ?? 0 );
            $bh_b  = intval( $bh['bottom'] ?? 0 );
            $bh_l  = intval( $bh['left']   ?? 0 );
            $bh_any = $bh_t || $bh_r || $bh_b || $bh_l;
            if ( $bh_color !== '' || $bh_style !== '' || $bh_any ) {
                // Base value (per ereditare i lati non override).
                $base = $style['border'] ?? null;
                $base_d = is_array( $base ) ? [
                    'top'    => max( 0, intval( $base['top']    ?? 0 ) ),
                    'right'  => max( 0, intval( $base['right']  ?? 0 ) ),
                    'bottom' => max( 0, intval( $base['bottom'] ?? 0 ) ),
                    'left'   => max( 0, intval( $base['left']   ?? 0 ) ),
                    'style'  => $base['style'] ?? 'solid',
                    'color'  => $base['color'] ?? '',
                ] : [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'style' => 'solid', 'color' => '' ];

                $eff_c = $bh_color !== '' ? $bh_color : $base_d['color'];
                $eff_s = $bh_style !== '' ? $bh_style : $base_d['style'];
                $eff_t = $bh_t ?: $base_d['top'];
                $eff_r = $bh_r ?: $base_d['right'];
                $eff_b = $bh_b ?: $base_d['bottom'];
                $eff_l = $bh_l ?: $base_d['left'];

                if ( $eff_c !== '' ) {
                    $bs = $this->safe_border_style( $eff_s ?: 'solid' );
                    $bc = $this->safe_border_color( $eff_c );
                    // !important: il bordo base è reso INLINE sull'elemento e un selettore
                    // #id:hover non lo sovrascriverebbe (l'inline ha specificità maggiore).
                    if ( $eff_t === $eff_r && $eff_r === $eff_b && $eff_b === $eff_l ) {
                        $hover_decls[] = "border: {$eff_t}px {$bs} {$bc} !important";
                    } else {
                        if ( $eff_t ) $hover_decls[] = "border-top: {$eff_t}px {$bs} {$bc} !important";
                        if ( $eff_r ) $hover_decls[] = "border-right: {$eff_r}px {$bs} {$bc} !important";
                        if ( $eff_b ) $hover_decls[] = "border-bottom: {$eff_b}px {$bs} {$bc} !important";
                        if ( $eff_l ) $hover_decls[] = "border-left: {$eff_l}px {$bs} {$bc} !important";
                    }
                    $has_hover = true;
                }
            }
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

        // Effetti bordo avanzati (neon/gradiente) del WRAPPER — letti da style.border_effect*.
        // Agganciati qui perché collect_hover_css è il punto comune a TUTTI i percorsi
        // wrapper (element/section/row/column), con $style + $css_id già disponibili.
        $be_css = $this->build_wrapper_border_effect_css( $css_id, $style );
        if ( $be_css !== '' ) {
            $hover_css_rules[] = $be_css;
        }
    }

    /**
     * Effetti bordo avanzati (neon / neon-pulse / gradiente / gradiente-rotante) per il
     * WRAPPER di un nodo (element/section/row/column). Equivalente lato wrapper di
     * Olo_Tile_Base::build_border_effect_css: legge style.border (colore base) +
     * style.border_effect_*. Selettore = #css_id. Stringa CSS senza <style> (o '').
     */
    private function build_wrapper_border_effect_css( $css_id, $style ) {
        $effect = $style['border_effect'] ?? 'none';
        if ( $effect === 'none' || $effect === '' ) return '';

        $border  = $style['border'] ?? null;
        $b_color = is_array( $border ) ? trim( $border['color'] ?? '' ) : '';
        $b_active = is_array( $border ) && $b_color !== '' && (
            intval( $border['top'] ?? 0 ) || intval( $border['right'] ?? 0 ) ||
            intval( $border['bottom'] ?? 0 ) || intval( $border['left'] ?? 0 )
        );
        // neon/neon-pulse richiedono un bordo base con colore; i gradienti no (usano border-image).
        if ( ! $b_active && ! in_array( $effect, [ 'gradient', 'gradient-spin' ], true ) ) return '';

        $uid    = '#' . $css_id;
        $color1 = $b_color !== '' ? $b_color : '#e1474f';
        $color2 = trim( $style['border_effect_color2'] ?? '' ) ?: '#f4a23b';

        switch ( $effect ) {
            case 'neon':
                $levels = [ 'subtle' => [ 4, 8 ], 'medium' => [ 6, 18 ], 'intense' => [ 10, 30 ] ];
                $intensity = $style['border_effect_intensity'] ?? 'medium';
                [ $a, $b ] = $levels[ $intensity ] ?? $levels['medium'];
                $alpha = $this->hex_to_rgba( $color1, 0.45 );
                return "{$uid}{box-shadow:0 0 {$a}px {$color1},0 0 {$b}px {$color1},0 0 " . ( $b * 2 ) . "px {$alpha};}";

            case 'neon-pulse':
                $intensity = $style['border_effect_intensity'] ?? 'medium';
                $anim_id   = 'olo-np-' . substr( md5( $uid . $color1 ), 0, 6 );
                $a1 = $this->hex_to_rgba( $color1, 0.5 );
                $a2 = $this->hex_to_rgba( $color1, 0.8 );
                switch ( $intensity ) {
                    case 'subtle':  [ $s1, $s2, $s3, $s4 ] = [ 3, 6, 5, 12 ]; break;
                    case 'intense': [ $s1, $s2, $s3, $s4 ] = [ 8, 20, 14, 40 ]; break;
                    default:        [ $s1, $s2, $s3, $s4 ] = [ 5, 12, 8, 24 ]; break;
                }
                return "@keyframes {$anim_id}{0%,100%{box-shadow:0 0 {$s1}px {$color1},0 0 {$s2}px {$a1};}50%{box-shadow:0 0 {$s3}px {$color1},0 0 {$s4}px {$a2},0 0 " . ( $s4 * 2 ) . "px {$a1};}}" .
                       "{$uid}{animation:{$anim_id} 2s ease-in-out infinite;}";

            case 'gradient':
                $angle = intval( $style['border_effect_angle'] ?? 135 );
                return "{$uid}{border-image:linear-gradient({$angle}deg,{$color1},{$color2}) 1;}";

            case 'gradient-spin':
                $speed   = max( 1, intval( $style['border_effect_speed'] ?? 4 ) );
                $prop    = '--olo-ba-' . substr( md5( $uid ), 0, 6 );
                $anim_id = 'olo-bs-' . substr( md5( $uid ), 0, 6 );
                return "@property {$prop}{syntax:'<angle>';initial-value:0deg;inherits:false;}" .
                       "@keyframes {$anim_id}{to{{$prop}:360deg;}}" .
                       "{$uid}{border-image:conic-gradient(from var({$prop}),{$color1},{$color2},{$color1}) 1;" .
                       "animation:{$anim_id} {$speed}s linear infinite;}";
        }
        return '';
    }

    /** Converte un colore hex in rgba(r,g,b,alpha); fallback brand se non hex. */
    private function hex_to_rgba( $hex, $alpha ) {
        $hex = ltrim( (string) $hex, '#' );
        if ( strlen( $hex ) === 3 ) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        if ( strlen( $hex ) !== 6 || ! ctype_xdigit( $hex ) ) return "rgba(225,71,79,{$alpha})";
        $r = hexdec( substr( $hex, 0, 2 ) );
        $g = hexdec( substr( $hex, 2, 2 ) );
        $b = hexdec( substr( $hex, 4, 2 ) );
        return "rgba({$r},{$g},{$b},{$alpha})";
    }

    /** Dichiarazione text-shadow da chiavi (text_shadow_h/_v/_blur/_color + $suffix). '' se inattiva.
     *  !important perché il valore desktop è inline e va sovrascritto nelle media query. */
    private function build_text_shadow_decl( $src, $suffix = '' ) {
        $h = $src["text_shadow_h{$suffix}"]    ?? null;
        $v = $src["text_shadow_v{$suffix}"]    ?? null;
        $b = $src["text_shadow_blur{$suffix}"] ?? null;
        if ( $h === null && $v === null && $b === null ) return '';
        $hi = intval( $h ?? 0 ); $vi = intval( $v ?? 0 ); $bi = intval( $b ?? 0 );
        if ( ! $hi && ! $vi && ! $bi ) return '';
        $c = esc_attr( $src["text_shadow_color{$suffix}"] ?? 'rgba(0,0,0,0.3)' );
        return "text-shadow: {$hi}px {$vi}px {$bi}px {$c} !important";
    }

    /** Dichiarazioni backdrop-filter da chiavi (backdrop_blur/_brightness/_saturate + $suffix). [] se inattive. */
    private function build_backdrop_decls( $src, $suffix = '' ) {
        $parts = [];
        if ( isset( $src["backdrop_blur{$suffix}"] ) && intval( $src["backdrop_blur{$suffix}"] ) != 0 ) {
            $parts[] = 'blur(' . intval( $src["backdrop_blur{$suffix}"] ) . 'px)';
        }
        if ( isset( $src["backdrop_brightness{$suffix}"] ) && intval( $src["backdrop_brightness{$suffix}"] ) != 100 ) {
            $parts[] = 'brightness(' . intval( $src["backdrop_brightness{$suffix}"] ) . '%)';
        }
        if ( isset( $src["backdrop_saturate{$suffix}"] ) && intval( $src["backdrop_saturate{$suffix}"] ) != 100 ) {
            $parts[] = 'saturate(' . intval( $src["backdrop_saturate{$suffix}"] ) . '%)';
        }
        if ( empty( $parts ) ) return [];
        $val = implode( ' ', $parts );
        return [ '-webkit-backdrop-filter: ' . $val . ' !important', 'backdrop-filter: ' . $val . ' !important' ];
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

            // Border per-device (oggetto 4-side su border_<bp>). Il controllo "Bordo"
            // di Spazi & Bordi salva un override per breakpoint: stessa policy del
            // desktop (build_wrapper_border_css) — se la chiave esiste è un override
            // esplicito, incluso "border: none" per disattivare il bordo su quel device.
            $bd_key = "border_{$bp}";
            if ( isset( $style[ $bd_key ] ) && is_array( $style[ $bd_key ] ) ) {
                // !important: il bordo desktop è inline, va sovrascritto nella media query.
                $bd_css = $this->build_wrapper_border_css( [ 'border' => $style[ $bd_key ] ], true );
                if ( $bd_css !== '' ) {
                    $decls[] = $bd_css;
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
            $t_rot_key = "transform_rotate_{$bp}";
            if ( isset( $style[ $t_rot_key ] ) && $style[ $t_rot_key ] !== '' && $style[ $t_rot_key ] !== null ) {
                $resp_t_parts[] = 'rotate(' . intval( $style[ $t_rot_key ] ) . 'deg)';
            }
            $t_skx_key = "transform_skewX_{$bp}";
            if ( isset( $style[ $t_skx_key ] ) && $style[ $t_skx_key ] !== '' && $style[ $t_skx_key ] !== null ) {
                $resp_t_parts[] = 'skewX(' . intval( $style[ $t_skx_key ] ) . 'deg)';
            }
            $t_sky_key = "transform_skewY_{$bp}";
            if ( isset( $style[ $t_sky_key ] ) && $style[ $t_sky_key ] !== '' && $style[ $t_sky_key ] !== null ) {
                $resp_t_parts[] = 'skewY(' . intval( $style[ $t_sky_key ] ) . 'deg)';
            }
            if ( ! empty( $resp_t_parts ) ) {
                $decls[] = 'transform: ' . implode( ' ', $resp_t_parts ) . ' !important';
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

            // tile_* responsive (style fields del tab "Stile" del builder).
            $resp_dim_map = [
                "tile_width_{$bp}"      => 'width',
                "tile_max_width_{$bp}"  => 'max-width',
                "tile_min_height_{$bp}" => 'min-height',
            ];
            foreach ( $resp_dim_map as $rkey => $rprop ) {
                if ( ! isset( $style[ $rkey ] ) || $style[ $rkey ] === '' || $style[ $rkey ] === null ) continue;
                $rv_dim = $this->safe_dim_value( $style[ $rkey ] );
                if ( $rv_dim !== '' ) $decls[] = $rprop . ': ' . $rv_dim;
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

            // ── Effetti per-device — NORMALE (#id): opacity / text-shadow / backdrop ──
            $op_key = "opacity_{$bp}";
            if ( isset( $style[ $op_key ] ) && $style[ $op_key ] !== '' && $style[ $op_key ] !== null ) {
                $decls[] = 'opacity: ' . ( intval( $style[ $op_key ] ) / 100 ) . ' !important';
            }
            $ts_n = $this->build_text_shadow_decl( $style, "_{$bp}" );
            if ( $ts_n !== '' ) $decls[] = $ts_n;
            foreach ( $this->build_backdrop_decls( $style, "_{$bp}" ) as $d ) $decls[] = $d;

            // ── Effetti per-device — HOVER (#id:hover): opacity / transform / text-shadow / backdrop ──
            $h = is_array( $style['hover'] ?? null ) ? $style['hover'] : [];
            $h_decls = [];
            $h_op = $h[ "opacity_{$bp}" ] ?? null;
            if ( $h_op !== null && $h_op !== '' ) $h_decls[] = 'opacity: ' . ( intval( $h_op ) / 100 ) . ' !important';
            $h_t_parts = [];
            $hts = $h["transform_scale_{$bp}"]     ?? null; if ( $hts !== null && $hts !== '' ) $h_t_parts[] = 'scale(' . floatval( $hts ) . ')';
            $htx = $h["transform_translateX_{$bp}"] ?? null; if ( $htx !== null && $htx !== '' ) $h_t_parts[] = 'translateX(' . floatval( $htx ) . 'px)';
            $hty = $h["transform_translateY_{$bp}"] ?? null; if ( $hty !== null && $hty !== '' ) $h_t_parts[] = 'translateY(' . floatval( $hty ) . 'px)';
            $hro = $h["transform_rotate_{$bp}"]     ?? null; if ( $hro !== null && $hro !== '' ) $h_t_parts[] = 'rotate(' . intval( $hro ) . 'deg)';
            $hsx = $h["transform_skewX_{$bp}"]      ?? null; if ( $hsx !== null && $hsx !== '' ) $h_t_parts[] = 'skewX(' . intval( $hsx ) . 'deg)';
            $hsy = $h["transform_skewY_{$bp}"]      ?? null; if ( $hsy !== null && $hsy !== '' ) $h_t_parts[] = 'skewY(' . intval( $hsy ) . 'deg)';
            if ( ! empty( $h_t_parts ) ) $h_decls[] = 'transform: ' . implode( ' ', $h_t_parts ) . ' !important';
            $ts_h = $this->build_text_shadow_decl( $h, "_{$bp}" );
            if ( $ts_h !== '' ) $h_decls[] = $ts_h;
            foreach ( $this->build_backdrop_decls( $h, "_{$bp}" ) as $d ) $h_decls[] = $d;
            if ( ! empty( $h_decls ) ) {
                $this->responsive_css_rules[ $max_width ][] = '#' . esc_attr( $css_id ) . ':hover { ' . implode( '; ', $h_decls ) . '; }';
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
            // Sanitize CSS for <style> block injection prevention
            $hover_css_rules[] = $this->safe_block_css( $custom_css );
        }
    }

    /**
     * Single-pass node tree scan: ritorna tutti i "signature" usati per decidere
     * gli script da enqueue. Sostituisce 7 funzioni `check_*_recursive` che
     * facevano 12+ visite separate dell'albero (ognuna O(N)).
     *
     * Output:
     *   [
     *     'types'          => ['postgrid' => true, 'pdfviewer' => true, ...],
     *     'has_leaflet_map'        => bool (map con mode=locations|services)
     *     'has_row_loop_load_more' => bool
     *     'has_bezier'             => bool (advanced.bezier_path settato su qualsiasi tile)
     *     'has_progallery_lightbox'=> bool (progallery con lightbox_thumbs ∈ bottom/right/left)
     *   ]
     *
     * Costo: 1 visita O(N) invece di 12. Per template con 200 tile: ~200 confronti
     * vs ~2400 con i metodi singoli.
     */
    private function collect_node_signatures( $tiles ) {
        $sig = [
            'types'                   => [],
            'has_leaflet_map'         => false,
            'has_row_loop_load_more'  => false,
            'has_bezier'              => false,
            'has_progallery_lightbox' => false,
        ];
        $this->walk_signatures( $tiles, $sig );
        return $sig;
    }

    private function walk_signatures( $nodes, &$sig ) {
        if ( ! is_array( $nodes ) ) return;
        foreach ( $nodes as $node ) {
            $type = $node['type'] ?? '';
            if ( $type ) $sig['types'][ $type ] = true;

            // map: distingue mode (locations/services richiede leaflet)
            if ( $type === 'map' ) {
                $mode = $node['settings']['mode'] ?? 'single';
                if ( $mode === 'locations' || $mode === 'services' ) {
                    $sig['has_leaflet_map'] = true;
                }
            }

            // row con loop "load more"
            if ( $type === 'row' ) {
                $s = $node['settings'] ?? [];
                if ( ! empty( $s['loop_enabled'] ) && ( $s['loop_pagination'] ?? 'none' ) === 'load_more' ) {
                    $sig['has_row_loop_load_more'] = true;
                }
            }

            // bezier_path è in advanced (qualsiasi tile)
            if ( ! empty( $node['advanced']['bezier_path'] ) ) {
                $sig['has_bezier'] = true;
            }

            // progallery con thumbs custom
            if ( $type === 'progallery' ) {
                $thumbs = $node['settings']['lightbox_thumbs'] ?? 'none';
                if ( $thumbs === 'bottom' || $thumbs === 'right' || $thumbs === 'left' ) {
                    $sig['has_progallery_lightbox'] = true;
                }
            }

            if ( ! empty( $node['children'] ) ) {
                $this->walk_signatures( $node['children'], $sig );
            }
        }
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

        // Fire action after template validation succeeds
        do_action( 'olo_template_rendered', $id, $template['title'], $template['type'] );

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

        $safe_mode = get_option( 'olo_safe_mode', false );

        // Shared utilities (escHtml etc.) — loaded before all olo-*.js scripts
        if ( ! $safe_mode ) {
        wp_enqueue_script(
            'olo-utils',
            OLO_URL . 'assets/js/olo-utils.js',
            [],
            OLO_VERSION,
            true
        );

        // Una sola visita dell'albero per raccogliere le signature di tutti i tile
        // (vs 12+ visite separate prima). $sig['types'][$type] è O(1) lookup.
        $sig = $this->collect_node_signatures( $tiles );

        // Post Grid detection (recursive)
        if ( ! empty( $sig['types']['postgrid'] ) ) {
            wp_enqueue_script(
                'olo-postgrid-js',
                OLO_URL . 'assets/js/olo-postgrid.js',
                [ 'olo-utils' ],
                OLO_VERSION,
                true
            );
        }

        // Row Loop "Load More" pagination (delegated click handler on body).
        if ( $sig['has_row_loop_load_more'] ) {
            wp_enqueue_script(
                'olo-row-loop-js',
                OLO_URL . 'assets/js/olo-row-loop.js',
                [],
                OLO_VERSION,
                true
            );
            wp_add_inline_script(
                'olo-row-loop-js',
                'window.oloFrontendData = window.oloFrontendData || {}; window.oloFrontendData.restUrl = "' . esc_js( esc_url_raw( rest_url( 'olo/v1' ) ) ) . '";',
                'before'
            );
        }

        // Leaflet map detection (recursive)
        if ( $sig['has_leaflet_map'] ) {
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
        if ( ! empty( $sig['types']['proslider'] ) ) {
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
        if ( ! empty( $sig['types']['livesearch'] ) ) {
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
        if ( ! empty( $sig['types']['servicesearch'] ) ) {
            wp_enqueue_script(
                'olo-servicesearch-js',
                OLO_URL . 'assets/js/olo-servicesearch.js',
                [],
                OLO_VERSION,
                true
            );
        }

        // ServiceResults detection (recursive)
        if ( ! empty( $sig['types']['serviceresults'] ) ) {
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
        if ( $sig['has_progallery_lightbox'] ) {
            wp_enqueue_script(
                'olo-progallery-lightbox-js',
                OLO_URL . 'assets/js/olo-progallery-lightbox.js',
                [],
                OLO_VERSION,
                true
            );
        }

        // PdfViewer detection (recursive)
        if ( ! empty( $sig['types']['pdfviewer'] ) ) {
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
        if ( ! empty( $sig['types']['pdfpro'] ) ) {
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
        if ( ! empty( $sig['types']['svganimator'] ) ) {
            wp_enqueue_style( 'olo-svganimator-css', OLO_URL . 'assets/css/olo-svganimator.css', [], OLO_VERSION );
            wp_enqueue_script( 'olo-svganimator-js', OLO_URL . 'assets/js/olo-svganimator.js', [], OLO_VERSION, true );
        }

        // Viewer 360
        if ( ! empty( $sig['types']['viewer360'] ) ) {
            wp_enqueue_script( 'olo-viewer360-js', OLO_URL . 'assets/js/olo-viewer360.js', [], OLO_VERSION, true );
        }

        // Bezier path scroll animation
        if ( $sig['has_bezier'] ) {
            wp_enqueue_script( 'olo-bezier-parallax-js', OLO_URL . 'assets/js/olo-bezier-parallax.js', [], OLO_VERSION, true );
        }
        } // end if ( ! $safe_mode ) — skip tile JS enqueue

        $manager = Olo_Tile_Manager::instance();

        $page_bg_css = $this->css->get_bg_inline_css( $page_bg );
        $hover_css_rules = [];
        $this->responsive_css_rules = [];
        $tile_counter = 0;

        // Quando il template è di tipo 'widget' è renderizzato dentro un altro
        // template (via render_widget_template). Evitiamo l'`id="olo-main-content"`
        // (deve essere unico per pagina) e `role="main"` (semantica per il main
        // wrapper della pagina, non per un sub-template embedded).
        $is_widget = ( $template['type'] ?? '' ) === 'widget';
        $wrapper_id_attr   = $is_widget ? '' : ' id="olo-main-content"';
        $wrapper_role_attr = $is_widget ? '' : ' role="main"';

        ob_start();
        ?>
        <div<?php echo $wrapper_id_attr; ?><?php echo $wrapper_role_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed literal id/role attribute strings set above ?> class="olo-template olo-template-<?php echo esc_attr( $id ); ?>"<?php
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
                $pg_parallax_attr = $this->anim->build_uk_parallax_attr( $page_bg );
            ?>
                <div class="olo-tile-bg"
                    style="background-image: url(<?php echo esc_url( $page_bg['image_url'] ); ?>); background-size: <?php echo $pg_size; ?>; background-position: <?php echo $pg_pos; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $pg_size/$pg_pos esc_attr()'d at assignment above ?>; background-repeat: no-repeat"
                    <?php echo $pg_parallax_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- uk-parallax attribute built by Olo_Animation_Builder::build_uk_parallax_attr() from intval()/floatval() values ?>
                ></div>
            <?php endif; ?>
            <?php
            // Page background video layer
            if ( $page_bg['type'] === 'video' && ! empty( $page_bg['video_url'] ) ) :
                $vid_poster = ! empty( $page_bg['video_poster'] ) ? esc_url( $page_bg['video_poster'] ) : '';
                $vid_pos    = esc_attr( $page_bg['image_position'] ?? 'center center' );
                $vid_fit    = esc_attr( $page_bg['video_fit'] ?? 'cover' );
            ?>
                <video aria-hidden="true" class="olo-tile-bg" style="object-fit: <?php echo $vid_fit; ?>; object-position: <?php echo $vid_pos; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $vid_fit/$vid_pos esc_attr()'d and $vid_poster esc_url()'d at assignment above ?>; pointer-events: none" autoplay muted loop playsinline<?php if ( $vid_poster ) echo ' poster="' . $vid_poster . '"'; ?>><source src="<?php echo esc_url( $page_bg['video_url'] ); ?>" type="<?php echo esc_attr( $this->get_video_mime( $page_bg['video_url'] ) ); ?>"></video>
            <?php endif; ?>
            <?php
            // Page overlay
            if ( $page_bg['type'] !== 'none' && ! empty( $page_bg['overlay_opacity'] ) && intval( $page_bg['overlay_opacity'] ) > 0 ) :
                $ov_color   = esc_attr( $page_bg['overlay_color'] ?? '#000000' );
                $ov_opacity = intval( $page_bg['overlay_opacity'] ) / 100;
            ?>
                <div class="olo-tile-overlay" style="background-color: <?php echo $ov_color; ?>; opacity: <?php echo $ov_opacity; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $ov_color esc_attr()'d at assignment above; $ov_opacity is intval()/100 ?>" aria-hidden="true"></div>
            <?php endif; ?>

            <div class="olo-frontend-grid olo-tile-content" style="--olo-content-width: <?php echo $content_max_width >= 9999 ? '100%' : (int) $content_max_width . 'px'; ?>; --olo-container-max-width: <?php echo $content_max_width >= 9999 ? 'none' : (int) $content_max_width . 'px'; ?>"><?php // per-template override of global container width ?>
                <?php
                foreach ( $tiles as $section ) {
                    echo $this->render_node( $section, $manager, $id, $hover_css_rules, $tile_counter ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- section/row/tile HTML assembled by render_node(); escaping is performed by the node renderers and each tile's render()
                }
                ?>
            </div>
            <?php if ( ! empty( $hover_css_rules ) || ! empty( $this->responsive_css_rules ) ) :
                $all_css = implode( ' ', $hover_css_rules );
                foreach ( $this->responsive_css_rules as $max_w => $rules ) {
                    $all_css .= ' @media(max-width:' . $max_w . '){' . implode( ' ', $rules ) . '}';
                }
                if ( class_exists( 'Olo_Asset_Optimizer' ) ) {
                    echo Olo_Asset_Optimizer::serve_css( $all_css, $id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- <link>/<style> markup built by Olo_Asset_Optimizer::serve_css() (esc_url internally); CSS collected via collect_hover_css()/collect_responsive_css() (esc_attr/intval) and safe_block_css() for custom CSS
                } else {
                    echo '<style class="olo-hover-styles">' . $all_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS collected via collect_hover_css()/collect_responsive_css() (esc_attr/intval-sanitized declarations) and safe_block_css() for custom CSS
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
                    // ":scope > template" matcha SOLO il template figlio DIRETTO.
                    // Senza :scope, il querySelector pescherebbe template di
                    // data-olo-lazy ANNIDATI (es. widget dentro switcher), inserendo
                    // il loro content nel data-olo-lazy padre sbagliato.
                    var tpl = e.target.querySelector(':scope > template');
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
                      // Riosserva i data-olo-lazy ANNIDATI appena introdotti.
                      // L'observer iniziale non li conosceva (erano dentro <template>),
                      // quindi senza questo step restano svuoti per sempre.
                      e.target.querySelectorAll('[data-olo-lazy]').forEach(function(nested){
                        if (!nested.dataset.oloLazyObserved) {
                          nested.dataset.oloLazyObserved = '1';
                          obs.observe(nested);
                        }
                      });
                      // Re-init UIkit components sui nuovi elementi (es. switcher, tab, slider...)
                      // — UIkit usa MutationObserver ma a volte salta gli inserimenti via importNode/template.
                      // Stesso pattern del builder iframe-bridge.js::reinitUIkit().
                      if (window.UIkit && typeof window.UIkit.update === 'function') {
                        try {
                          var ukNames = ['slider','slideshow','lightbox','grid','scrollspy','accordion','tab','switcher','countdown','filter','parallax','sticky','navbar','drop','dropdown'];
                          ukNames.forEach(function(name){
                            e.target.querySelectorAll('[uk-' + name + '],[data-uk-' + name + ']').forEach(function(el){
                              try { if (UIkit[name]) UIkit[name](el); } catch(_){}
                            });
                          });
                          UIkit.update(e.target);
                        } catch(_){}
                      }
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
              /* Tilt "per item": [data-olo-tilt-items] sul wrapper (gallerie, griglie)
                 propaga l'attributo a foto e video interni, che entrano nella raccolta
                 sottostante come qualsiasi altro elemento tilt. */
              document.querySelectorAll('[data-olo-tilt-items]').forEach(function(host){
                var v = host.getAttribute('data-olo-tilt-items') || '15';
                host.querySelectorAll('img, video').forEach(function(it){
                  if(!it.hasAttribute('data-olo-tilt')) it.setAttribute('data-olo-tilt', v);
                });
              });
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
                  /* Clamp a ±speed*10px: senza limite, su elementi piccoli con mouse
                     lontano lo spostamento esplodeva a centinaia di px. */
                  var max = speed * 10;
                  var tx = Math.max(-max, Math.min(max, (dx / rect.width) * max));
                  var ty = Math.max(-max, Math.min(max, (dy / rect.height) * max));
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
            /* Spotlight cursore — disco-torcia confinato all'elemento (effetto puntatore riusabile) */
            (function(){
              function setup(host){
                if(host.dataset.oloSpotReady) return;
                var cfg; try { cfg = JSON.parse(host.dataset.oloSpotlight); } catch(e){ return; }
                host.dataset.oloSpotReady = '1';
                var size = +cfg.size || 300, soft = (cfg.soft != null ? +cfg.soft : 40);
                var blend = cfg.blend || 'difference', color = cfg.color || '#ffffff', ease = +cfg.ease || 0.22;
                var inner = Math.max(0, 100 - soft), half = size / 2;
                /* Falloff a curva: una rampa lineare verso transparent lascia un bordo
                   percepibile (Mach band) anche a morbidezza 100. Gli stop modulano
                   l'alpha del colore con coda dolce che arriva a 0 a derivata ~0. */
                var hx = String(color).replace('#',''); if(hx.length === 3) hx = hx[0]+hx[0]+hx[1]+hx[1]+hx[2]+hx[2];
                var nn = parseInt(hx, 16); if(isNaN(nn)) nn = 16777215;
                var rgb = (nn>>16&255) + ',' + (nn>>8&255) + ',' + (nn&255);
                var C = function(a){ return 'rgba(' + rgb + ',' + a + ')'; };
                var span = 100 - inner;
                var st = function(f){ return (inner + span * f).toFixed(1) + '%'; };
                var grad = 'radial-gradient(circle, ' + C(1) + ' 0%, ' + C(1) + ' ' + inner + '%, ' + C(.82) + ' ' + st(.25) + ', ' + C(.5) + ' ' + st(.5) + ', ' + C(.22) + ' ' + st(.75) + ', ' + C(.07) + ' ' + st(.9) + ', ' + C(0) + ' 100%)';
                var disc = null, tx = 0, ty = 0, cx = 0, cy = 0, running = false, inside = false;
                function build(){          // creazione lazy: solo al primo hover con mouse/pen
                  if(disc) return;
                  if(getComputedStyle(host).position === 'static') host.style.position = 'relative';
                  host.style.overflow = 'hidden';
                  host.style.isolation = 'isolate';     // confina il mix-blend al contenuto del box
                  disc = document.createElement('div');
                  disc.setAttribute('aria-hidden', 'true');
                  disc.style.cssText = 'position:absolute;top:0;left:0;z-index:99999;width:' + size + 'px;height:' + size + 'px;border-radius:50%;pointer-events:none;will-change:transform,opacity;opacity:0;transition:opacity .2s ease;background:' + grad + ';mix-blend-mode:' + blend + ';';
                  host.appendChild(disc);
                }
                function frame(){
                  cx += (tx - cx) * ease; cy += (ty - cy) * ease;
                  if(disc) disc.style.transform = 'translate(' + (cx - half) + 'px,' + (cy - half) + 'px)';
                  if(inside || Math.abs(tx - cx) > 0.5 || Math.abs(ty - cy) > 0.5){ requestAnimationFrame(frame); } else { running = false; }
                }
                function start(){ if(!running){ running = true; requestAnimationFrame(frame); } }
                host.addEventListener('pointerenter', function(e){
                  if(e.pointerType === 'touch') return;   // niente torcia su touch (rilevato per-evento, non via media-query)
                  if(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                  build();
                  var r = host.getBoundingClientRect(); tx = cx = e.clientX - r.left; ty = cy = e.clientY - r.top;
                  inside = true; disc.style.opacity = '1'; start();
                });
                host.addEventListener('pointermove', function(e){
                  if(e.pointerType === 'touch' || !disc) return;
                  var r = host.getBoundingClientRect(); tx = e.clientX - r.left; ty = e.clientY - r.top; start();
                });
                host.addEventListener('pointerleave', function(){ inside = false; if(disc) disc.style.opacity = '0'; });
              }
              function initSpotlights(){
                var hosts = document.querySelectorAll('[data-olo-spotlight]');
                for(var i = 0; i < hosts.length; i++){ setup(hosts[i]); }
              }
              // init multipli: cattura anche host inseriti/idratati dopo il DOMContentLoaded
              if(document.readyState !== 'loading'){ initSpotlights(); }
              document.addEventListener('DOMContentLoaded', initSpotlights);
              window.addEventListener('load', initSpotlights);
              setTimeout(initSpotlights, 800);
              setTimeout(initSpotlights, 2500);
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
                  if(fx.fill){
                    var flStart = fx.fill[0], flEnd = fx.fill[1];
                    el.style.height = (flStart + progress * (flEnd - flStart)) + '%';
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
            <script>
            /* ScrollAssembly — preset Parallax multi-target: piu parti figlie [data-olo-part]
               animate su UN unico progress del genitore [data-olo-assembly]. Additivo: no-op
               senza l'attributo. reduced-motion -> stato finale montato (e=1). */
            (function(){
              var hosts = document.querySelectorAll('[data-olo-assembly]');
              if(!hosts.length) return;
              var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
              var items = [];
              hosts.forEach(function(host){
                var parts = [].slice.call(host.querySelectorAll('[data-olo-part]'));
                parts.forEach(function(el){ el.style.willChange = 'transform,opacity'; });
                items.push({ host: host, parts: parts });
              });
              function frame(){
                var vh = window.innerHeight;
                items.forEach(function(it){
                  var rect = it.host.getBoundingClientRect();
                  var total = (it.host.offsetHeight - vh) || 1;
                  var p = Math.min(1, Math.max(0, (-rect.top) / total));
                  it.parts.forEach(function(el, i){
                    var cfg; try { cfg = JSON.parse(el.dataset.oloPart); } catch(e){ return; }
                    var start = (cfg.start != null ? cfg.start : i * 0.12);
                    var end   = (cfg.end != null ? cfg.end : start + 0.5);
                    var t = Math.min(1, Math.max(0, (p - start) / ((end - start) || 1)));
                    var e = reduce ? 1 : (1 - Math.pow(1 - t, 3));
                    if(cfg.fill != null){ var f0 = cfg.fill[0], f1 = cfg.fill[1]; el.style.height = (f0 + e * (f1 - f0)).toFixed(1) + '%'; return; }
                    var x = (cfg.x || 0) * (1 - e), y = (cfg.y || 0) * (1 - e);
                    var s = 1 + ((cfg.s || 1) - 1) * (1 - e), r = (cfg.r || 0) * (1 - e);
                    el.style.transform = 'translate(' + x + 'px,' + y + 'px) scale(' + s + ') rotate(' + r + 'deg)';
                    if(cfg.fade !== false) el.style.opacity = (0.15 + e * 0.85).toFixed(2);
                  });
                });
              }
              var ticking = false;
              function onScroll(){ if(!ticking){ ticking = true; requestAnimationFrame(function(){ frame(); ticking = false; }); } }
              window.addEventListener('scroll', onScroll, { passive: true });
              window.addEventListener('resize', onScroll, { passive: true });
              frame();
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

        echo '<div class="olo-frontend-grid olo-tile-content" style="--olo-content-width: ' . ( $content_max_width >= 9999 ? '100%' : (int) $content_max_width . 'px' ) . '; --olo-container-max-width: ' . ( $content_max_width >= 9999 ? 'none' : (int) $content_max_width . 'px' ) . '">';
        foreach ( $tiles as $section ) {
            echo $this->render_node( $section, $manager, 0, $hover_css_rules, $tile_counter ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- section/row/tile HTML assembled by render_node(); escaping is performed by the node renderers and each tile's render()
        }
        echo '</div>';

        // Effetto di pagina: Overlay CRT (decoratore di pagina, da Impostazioni Pagina → Effetti di pagina).
        if ( ! empty( $page_settings['page_crt_enabled'] ) && class_exists( 'Olo_Crtoverlay_Tile' ) ) {
            // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- overlay HTML assembled by Olo_Crtoverlay_Tile::render() (escapes its own output) from intval()'d settings; blend mode whitelisted inside the tile.
            echo ( new Olo_Crtoverlay_Tile() )->render( [
                'scanline_opacity' => intval( $page_settings['page_crt_scanline_opacity'] ?? 50 ),
                'scanline_gap'     => intval( $page_settings['page_crt_scanline_gap'] ?? 3 ),
                'vignette'         => intval( $page_settings['page_crt_vignette'] ?? 55 ),
                'blend_mode'       => $page_settings['page_crt_blend_mode'] ?? 'overlay',
                'flicker'          => ! empty( $page_settings['page_crt_flicker'] ),
                'flicker_speed'    => intval( $page_settings['page_crt_flicker_speed'] ?? 8 ),
                'z_index'          => intval( $page_settings['page_crt_z_index'] ?? 200 ),
            ] );
            // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        if ( ! empty( $hover_css_rules ) || ! empty( $this->responsive_css_rules ) ) {
            $all_css = implode( ' ', $hover_css_rules );
            foreach ( $this->responsive_css_rules as $max_w => $rules ) {
                $all_css .= ' @media(max-width:' . $max_w . '){' . implode( ' ', $rules ) . '}';
            }
            echo '<style class="olo-hover-styles">' . $all_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS collected via collect_hover_css()/collect_responsive_css() (esc_attr/intval-sanitized declarations) and safe_block_css() for custom CSS
        }
    }

    /**
     * Genera il CSS border per il wrapper di un tile, supportando entrambi i sistemi:
     *
     *   1. style.border (NUOVO, FieldBorder oggetto 4-side):
     *      { top, right, bottom, left, style, color, linked }
     *      → border:Npx style color  oppure  border-top/right/bottom/left:Npx style color
     *
     *   2. style.border_width + style.border_style + style.border_color (LEGACY uniforme).
     *      Usato SOLO se la chiave style.border non esiste affatto (template pre-v3.55.20).
     *
     * IMPORTANTE: la sola PRESENZA di array_key_exists('border', $style) significa che
     * l'utente ha toccato il nuovo controllo e quindi il sistema legacy va IGNORATO,
     * anche se il nuovo bordo è "spento" (tutti i lati a 0). Senza questo check, mettere
     * a 0 il nuovo bordo lascia visibile il vecchio salvato in border_width legacy.
     *
     * @param array $style  tile.style flat
     * @return string       CSS declarations (vuoto se nessun border)
     */
    private function build_wrapper_border_css( $style, $important = false ) {
        // $important=true aggiunge !important: serve SOLO al bordo per-device nelle
        // media query (collect_responsive_css), perché il bordo desktop è inline e
        // un selettore #id non lo sovrascriverebbe altrimenti. Inline desktop = false.
        $imp = $important ? ' !important' : '';
        // Sistema NUOVO: oggetto 4-side. Se la chiave esiste, vince sempre sul legacy.
        if ( array_key_exists( 'border', $style ) && is_array( $style['border'] ) ) {
            $b = $style['border'];
            $color = trim( $b['color'] ?? '' );
            $stylename = $b['style'] ?? 'solid';
            $top    = max( 0, intval( $b['top']    ?? 0 ) );
            $right  = max( 0, intval( $b['right']  ?? 0 ) );
            $bottom = max( 0, intval( $b['bottom'] ?? 0 ) );
            $left   = max( 0, intval( $b['left']   ?? 0 ) );
            $any    = $top || $right || $bottom || $left;
            if ( $color !== '' && $any && $stylename !== 'none' ) {
                $bs = $this->safe_border_style( $stylename );
                $bc = $this->safe_border_color( $color );
                if ( $top === $right && $right === $bottom && $bottom === $left ) {
                    return "border: {$top}px {$bs} {$bc}{$imp}";
                }
                $parts = [];
                if ( $top    ) $parts[] = "border-top: {$top}px {$bs} {$bc}{$imp}";
                if ( $right  ) $parts[] = "border-right: {$right}px {$bs} {$bc}{$imp}";
                if ( $bottom ) $parts[] = "border-bottom: {$bottom}px {$bs} {$bc}{$imp}";
                if ( $left   ) $parts[] = "border-left: {$left}px {$bs} {$bc}{$imp}";
                return implode( '; ', $parts );
            }
            // Nuovo sistema esiste ma utente l'ha messo a 0/vuoto → bordo OFF (no fallback).
            // Forza border:none così il legacy non riemerge via cascade CSS del tema.
            return 'border: none' . $imp;
        }

        // Sistema LEGACY: 3 chiavi piatte. Solo se la chiave 'border' nuova non esiste.
        if ( ! empty( $style['border_width'] ) && intval( $style['border_width'] ) > 0 && ( $style['border_style'] ?? 'solid' ) !== 'none' ) {
            $bw = intval( $style['border_width'] );
            $bs = $this->safe_border_style( $style['border_style'] ?? 'solid' );
            $bc = $this->safe_border_color( $style['border_color'] ?? 'transparent' );
            return "border: {$bw}px {$bs} {$bc}";
        }

        return '';
    }

    /**
     * True se il wrapper ha un border attivo. Identica policy di build_wrapper_border_css:
     * la presenza di style.border (oggetto) disattiva sempre il fallback legacy.
     */
    private function wrapper_has_border( $style ) {
        if ( array_key_exists( 'border', $style ) && is_array( $style['border'] ) ) {
            $b = $style['border'];
            $color = trim( $b['color'] ?? '' );
            $any   = intval( $b['top'] ?? 0 ) || intval( $b['right'] ?? 0 ) || intval( $b['bottom'] ?? 0 ) || intval( $b['left'] ?? 0 );
            return ( $color !== '' && $any && ( $b['style'] ?? 'solid' ) !== 'none' );
        }
        return ! empty( $style['border_width'] ) && intval( $style['border_width'] ) > 0 && ( $style['border_style'] ?? 'solid' ) !== 'none';
    }
}
