<?php
/**
 * Olobuild_Renderer_Page_Trait — output di pagina: security headers, enqueue frontend, shortcode principale, srcset.
 *
 * Estratto verbatim da class-frontend-renderer.php (dieta monoliti v1.4.390):
 * stessi metodi, stessa visibilita', zero cambi alle chiamate ($this/self
 * risolvono nella classe che usa il trait).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

trait Olobuild_Renderer_Page_Trait {
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
            $tpl_id = (int) get_option( "olobuild_active_single_{$pt}", 0 );
            if ( $tpl_id ) {
                $has_olo = true;
            }
        }

        // Also check header/footer templates
        if ( ! $has_olo ) {
            $header_id = (int) get_option( 'olobuild_active_header', 0 );
            $footer_id = (int) get_option( 'olobuild_active_footer', 0 );
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
                $tpl_id = (int) get_option( "olobuild_active_archive_{$pt}", 0 );
                if ( ! $tpl_id ) {
                    $tpl_id = (int) get_option( 'olobuild_active_archive', 0 );
                }
                if ( $tpl_id ) {
                    $has_olo = true;
                }
            }
            if ( is_404() ) {
                $tpl_id = (int) get_option( 'olobuild_active_404', 0 );
                if ( $tpl_id ) {
                    $has_olo = true;
                }
            }
            if ( is_search() ) {
                $tpl_id = (int) get_option( 'olobuild_active_search', 0 );
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
            $tpl_id = (int) get_option( "olobuild_active_single_{$pt}", 0 );
            if ( $tpl_id ) {
                $has_olo = true;
            }
        }

        if ( $has_olo ) {
            $safe_mode = get_option( 'olobuild_safe_mode', false );

            if ( $safe_mode ) {
                add_filter( 'body_class', function( $classes ) { $classes[] = 'olo-safe-mode'; return $classes; } );
            }

            if ( ! $safe_mode ) {
                // UIkit 3 (CSS + JS + Icons) - only on frontend, NOT in builder admin
                wp_enqueue_style(
                    'uikit-css',
                    OLOBUILD_URL . 'assets/vendor/uikit/css/uikit.min.css',
                    [],
                    '3.21.16'
                );
                wp_enqueue_script(
                    'uikit-js',
                    OLOBUILD_URL . 'assets/vendor/uikit/js/uikit.min.js',
                    [],
                    '3.21.16',
                    array( 'in_footer' => true, 'strategy' => 'defer' )
                );
                wp_enqueue_script(
                    'uikit-icons-js',
                    OLOBUILD_URL . 'assets/vendor/uikit/js/uikit-icons.min.js',
                    array( 'uikit-js' ),
                    '3.21.16',
                    array( 'in_footer' => true, 'strategy' => 'defer' )
                );
            }

            // Olobuilder custom overrides (loaded after UIkit)
            wp_enqueue_style(
                'olo-frontend-css',
                OLOBUILD_URL . 'assets/css/frontend.css',
                $safe_mode ? [] : [ 'uikit-css' ],
                OLOBUILD_VERSION
            );

            // Print styles (caricato solo per media print)
            wp_enqueue_style(
                'olo-print',
                OLOBUILD_URL . 'assets/css/olo-print.css',
                array( 'olo-frontend-css' ),
                OLOBUILD_VERSION,
                'print'
            );

            // Style System CSS (custom properties + UIkit overrides)
            $style_css = Olobuild_Style_System::instance()->generate_css();
            if ( ! empty( $style_css ) ) {
                wp_add_inline_style( 'olo-frontend-css', $style_css );
            }
        }
    }

    /**
     * Get effective background config for a tile.
     */
    // --- CSS builder and animation builder methods moved to Olobuild_CSS_Builder and Olobuild_Animation_Builder ---


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

        $db       = new Olobuild_Database();
        $template = $db->get_template( $id );

        if ( ! $template ) {
            return '<!-- Olobuilder: Template not found -->';
        }

        if ( $template['status'] !== 'published' && $template['status'] !== 'draft' ) {
            return '<!-- Olobuilder: Template not available -->';
        }

        // Fire action after template validation succeeds
        do_action( 'olobuild_template_rendered', $id, $template['title'], $template['type'] );

        $tiles = $template['content'];
        if ( empty( $tiles ) || ! is_array( $tiles ) ) {
            return '<!-- Olobuilder: Empty template -->';
        }

        // Migrate legacy flat content to tree format
        $tiles = $this->maybe_migrate_content( $tiles );

        // Filtro per traduzioni multilingua (usato da Olo Lang)
        $tiles = apply_filters( 'olobuild_template_content', $tiles, $id );

        // Index all tiles for cross-tile lookup (e.g., navmenu → search tile)
        self::index_tiles( $tiles );

        // Page settings
        $page_settings     = $template['settings'] ?? [];
        $page_settings     = apply_filters( 'olobuild_template_settings', $page_settings, $id );
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

        $safe_mode = get_option( 'olobuild_safe_mode', false );

        // Shared utilities (escHtml etc.) — loaded before all olo-*.js scripts
        if ( ! $safe_mode ) {
        wp_enqueue_script(
            'olo-utils',
            OLOBUILD_URL . 'assets/js/olo-utils.js',
            [],
            OLOBUILD_VERSION,
            true
        );

        // Una sola visita dell'albero per raccogliere le signature di tutti i tile
        // (vs 12+ visite separate prima). $sig['types'][$type] è O(1) lookup.
        $sig = $this->collect_node_signatures( $tiles );

        // Post Grid detection (recursive)
        if ( ! empty( $sig['types']['postgrid'] ) ) {
            wp_enqueue_script(
                'olo-postgrid-js',
                OLOBUILD_URL . 'assets/js/olo-postgrid.js',
                [ 'olo-utils' ],
                OLOBUILD_VERSION,
                true
            );
        }

        // Row Loop "Load More" pagination (delegated click handler on body).
        if ( $sig['has_row_loop_load_more'] ) {
            wp_enqueue_script(
                'olo-row-loop-js',
                OLOBUILD_URL . 'assets/js/olo-row-loop.js',
                [],
                OLOBUILD_VERSION,
                true
            );
            wp_add_inline_script(
                'olo-row-loop-js',
                'window.oloFrontendData = window.oloFrontendData || {}; window.oloFrontendData.restUrl = "' . esc_js( esc_url_raw( rest_url( 'olobuild/v1' ) ) ) . '";',
                'before'
            );
        }

        // Leaflet map detection (recursive)
        if ( $sig['has_leaflet_map'] ) {
            $vendor_url = OLOBUILD_URL . 'assets/vendor/leaflet/';

            wp_enqueue_style( 'leaflet-css', $vendor_url . 'leaflet.css', [], '1.9.4' );
            wp_enqueue_style( 'leaflet-markercluster-css', $vendor_url . 'leaflet.markercluster.css', [ 'leaflet-css' ], '1.5.3' );
            wp_enqueue_style( 'leaflet-markercluster-default-css', $vendor_url . 'leaflet.markercluster-default.css', [ 'leaflet-markercluster-css' ], '1.5.3' );

            wp_enqueue_script( 'leaflet-js', $vendor_url . 'leaflet.js', [], '1.9.4', true );
            wp_enqueue_script( 'leaflet-markercluster-js', $vendor_url . 'leaflet.markercluster.js', [ 'leaflet-js' ], '1.5.3', true );
            wp_enqueue_script(
                'olo-map-js',
                OLOBUILD_URL . 'assets/js/olo-map.js',
                [ 'olo-utils', 'leaflet-js', 'leaflet-markercluster-js' ],
                OLOBUILD_VERSION,
                true
            );

        }

        // ProSlider detection (recursive)
        if ( ! empty( $sig['types']['proslider'] ) ) {
            wp_enqueue_style(
                'olo-proslider-css',
                OLOBUILD_URL . 'assets/css/olo-proslider.css',
                [],
                OLOBUILD_VERSION
            );
            wp_enqueue_script(
                'olo-proslider-js',
                OLOBUILD_URL . 'assets/js/olo-proslider.js',
                [],
                OLOBUILD_VERSION,
                true
            );
        }

        // LiveSearch detection (recursive)
        if ( ! empty( $sig['types']['livesearch'] ) ) {
            wp_enqueue_style(
                'olo-livesearch-css',
                OLOBUILD_URL . 'assets/css/olo-livesearch.css',
                [],
                OLOBUILD_VERSION
            );
            wp_enqueue_script(
                'olo-livesearch-js',
                OLOBUILD_URL . 'assets/js/olo-livesearch.js',
                [ 'olo-utils' ],
                OLOBUILD_VERSION,
                true
            );
        }

        // ServiceSearch detection (recursive)
        if ( ! empty( $sig['types']['servicesearch'] ) ) {
            wp_enqueue_script(
                'olo-servicesearch-js',
                OLOBUILD_URL . 'assets/js/olo-servicesearch.js',
                [],
                OLOBUILD_VERSION,
                true
            );
        }

        // ServiceResults detection (recursive)
        if ( ! empty( $sig['types']['serviceresults'] ) ) {
            $vendor_url = OLOBUILD_URL . 'assets/vendor/leaflet/';

            wp_enqueue_style( 'leaflet-css', $vendor_url . 'leaflet.css', [], '1.9.4' );
            wp_enqueue_style( 'leaflet-markercluster-css', $vendor_url . 'leaflet.markercluster.css', [ 'leaflet-css' ], '1.5.3' );
            wp_enqueue_style( 'leaflet-markercluster-default-css', $vendor_url . 'leaflet.markercluster-default.css', [ 'leaflet-markercluster-css' ], '1.5.3' );

            wp_enqueue_script( 'leaflet-js', $vendor_url . 'leaflet.js', [], '1.9.4', true );
            wp_enqueue_script( 'leaflet-markercluster-js', $vendor_url . 'leaflet.markercluster.js', [ 'leaflet-js' ], '1.5.3', true );
            wp_enqueue_script(
                'olo-serviceresults-js',
                OLOBUILD_URL . 'assets/js/olo-serviceresults.js',
                [ 'olo-utils', 'leaflet-js', 'leaflet-markercluster-js' ],
                OLOBUILD_VERSION,
                true
            );
        }

        // ProGallery custom lightbox detection (recursive)
        if ( $sig['has_progallery_lightbox'] ) {
            wp_enqueue_script(
                'olo-progallery-lightbox-js',
                OLOBUILD_URL . 'assets/js/olo-progallery-lightbox.js',
                [],
                OLOBUILD_VERSION,
                true
            );
        }

        // PdfViewer detection (recursive)
        if ( ! empty( $sig['types']['pdfviewer'] ) ) {
            wp_enqueue_script(
                'pdfjs',
                OLOBUILD_URL . 'assets/vendor/pdfjs/pdf.min.js',
                [],
                '3.11.174',
                true
            );
            wp_enqueue_script(
                'pageflip-js',
                OLOBUILD_URL . 'assets/vendor/pageflip/page-flip.browser.js',
                [],
                '2.0.7',
                true
            );
            wp_enqueue_style(
                'olo-pdfviewer-css',
                OLOBUILD_URL . 'assets/css/olo-pdfviewer.css',
                [],
                OLOBUILD_VERSION
            );
            wp_enqueue_script(
                'olo-pdfviewer-js',
                OLOBUILD_URL . 'assets/js/olo-pdfviewer.js',
                [ 'pdfjs', 'pageflip-js' ],
                OLOBUILD_VERSION,
                true
            );
            wp_localize_script( 'olo-pdfviewer-js', 'oloPdfViewerData', [
                'workerUrl' => OLOBUILD_URL . 'assets/vendor/pdfjs/pdf.worker.min.js',
            ] );
        }

        // PdfPro detection (recursive) — shares pdfjs/pageflip vendors with PdfViewer
        if ( ! empty( $sig['types']['pdfpro'] ) ) {
            wp_enqueue_script(
                'pdfjs',
                OLOBUILD_URL . 'assets/vendor/pdfjs/pdf.min.js',
                [],
                '3.11.174',
                true
            );
            wp_enqueue_script(
                'pageflip-js',
                OLOBUILD_URL . 'assets/vendor/pageflip/page-flip.browser.js',
                [],
                '2.0.7',
                true
            );
            wp_enqueue_script(
                'olo-pdfpro-js',
                OLOBUILD_URL . 'assets/js/olo-pdfpro.js',
                [ 'pdfjs', 'pageflip-js' ],
                OLOBUILD_VERSION,
                true
            );
            wp_localize_script( 'olo-pdfpro-js', 'oloPdfProData', [
                'workerUrl' => OLOBUILD_URL . 'assets/vendor/pdfjs/pdf.worker.min.js',
            ] );
        }

        // SVG Animator
        if ( ! empty( $sig['types']['svganimator'] ) ) {
            wp_enqueue_style( 'olo-svganimator-css', OLOBUILD_URL . 'assets/css/olo-svganimator.css', [], OLOBUILD_VERSION );
            wp_enqueue_script( 'olo-svganimator-js', OLOBUILD_URL . 'assets/js/olo-svganimator.js', [], OLOBUILD_VERSION, true );
        }

        // Viewer 360
        if ( ! empty( $sig['types']['viewer360'] ) ) {
            wp_enqueue_script( 'olo-viewer360-js', OLOBUILD_URL . 'assets/js/olo-viewer360.js', [], OLOBUILD_VERSION, true );
        }

        // Bezier path scroll animation
        if ( $sig['has_bezier'] ) {
            wp_enqueue_script( 'olo-bezier-parallax-js', OLOBUILD_URL . 'assets/js/olo-bezier-parallax.js', [], OLOBUILD_VERSION, true );
        }
        } // end if ( ! $safe_mode ) — skip tile JS enqueue

        $manager = Olobuild_Tile_Manager::instance();

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
                // Parallax sfondo pagina: NIENTE uk-parallax qui. Il layer è alto
                // quanto l'intera pagina, quindi UIkit ne diluirebbe il progresso su
                // tutto lo scroll (movimento impercettibile). Lo script dedicato
                // olo-pagebg-parallax.js completa l'animazione sulla prima schermata.
                $pg_par = $page_bg['parallax'] ?? null;
                $pg_par_json = '';
                if ( ! empty( $pg_par ) ) {
                    if ( ! is_array( $pg_par ) ) {
                        // Legacy boolean flat format → converti in stops.
                        $legacy_bgy = intval( $page_bg['parallax_bgy'] ?? -200 );
                        $legacy_bgx = intval( $page_bg['parallax_bgx'] ?? 0 );
                        $pg_par = [ 'nomobile' => ! empty( $page_bg['parallax_nomobile'] ) ];
                        if ( $legacy_bgy !== 0 ) {
                            $pg_par['bgy'] = [ [ 'value' => $legacy_bgy, 'position' => 0 ], [ 'value' => 0, 'position' => 100 ] ];
                        }
                        if ( $legacy_bgx !== 0 ) {
                            $pg_par['bgx'] = [ [ 'value' => $legacy_bgx, 'position' => 0 ], [ 'value' => 0, 'position' => 100 ] ];
                        }
                    }
                    $pg_par_json = wp_json_encode( $pg_par );
                    wp_enqueue_script( 'olo-pagebg-parallax-js', OLOBUILD_URL . 'assets/js/olo-pagebg-parallax.js', [], OLOBUILD_VERSION, true );
                }
            ?>
                <div class="olo-tile-bg"
                    style="background-image: url(<?php echo esc_url( $page_bg['image_url'] ); ?>); background-size: <?php echo $pg_size; ?>; background-position: <?php echo $pg_pos; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $pg_size/$pg_pos esc_attr()'d at assignment above ?>; background-repeat: no-repeat"
                    <?php if ( $pg_par_json ) : ?> data-olo-pagebg-parallax="<?php echo esc_attr( $pg_par_json ); ?>"<?php endif; ?>
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
            <?php
            // Effetti di pagina (CRT / grana): prima venivano emessi solo nella preview
            // builder (render_tiles_array) — ora anche sul frontend reale.
            echo $this->render_page_effects( $page_settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup assembled by render_page_effects() from sanitized settings
            ?>
            <?php if ( ! empty( $hover_css_rules ) || ! empty( $this->responsive_css_rules ) ) :
                $all_css = implode( ' ', $hover_css_rules );
                foreach ( $this->responsive_css_rules as $max_w => $rules ) {
                    $all_css .= ' @media(max-width:' . $max_w . '){' . implode( ' ', $rules ) . '}';
                }
                if ( class_exists( 'Olobuild_Asset_Optimizer' ) ) {
                    echo Olobuild_Asset_Optimizer::serve_css( $all_css, $id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- <link>/<style> markup built by Olobuild_Asset_Optimizer::serve_css() (esc_url internally); CSS collected via collect_hover_css()/collect_responsive_css() (esc_attr/intval) and safe_block_css() for custom CSS
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
                        /* Sotto il breakpoint di stacking delle colonne (uk-width-*@m,
                           960px) i pannelli impilati diventano molto più alti della
                           viewport: il binario orizzontale ne mostrerebbe solo la prima
                           schermata e lo scroll traslerebbe layer enormi. Fallback
                           mobile: niente raggruppamento, sezioni in flusso verticale
                           (la luce di pagina passa all'IntersectionObserver, i pallini
                           coverdots si nascondono da soli senza gruppo). */
                        if(window.matchMedia('(max-width: 959px)').matches)return;
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
                            /* Move (not clone) the real nodes: JS listeners, form state and
                               media playback inside the panels survive the grouping. */
                            sections.forEach(function(item){
                                item.sec.style.cssText+=';'+panelCSS;
                                track.appendChild(item.sec);
                            });
                            viewport.appendChild(track);
                            group.appendChild(viewport);
                            /* Insert before the first marker and hide all originals */
                            run[0].parentNode.insertBefore(group,run[0]);
                            run.forEach(function(m){
                                m.dataset.oloHDone='1';
                                m.style.display='none';
                            });
                            /* Scroll-linked translateX: travel across (n-1) panels.
                               Stato esposto per altre tile (pallini, glow, testi):
                               --olo-hp sul gruppo (progresso 0..1), --olo-pp per
                               sezione (1 = pannello centrato), data-olo-active +
                               CustomEvent 'olo:hgroup' al cambio pannello. */
                            group.dataset.oloCount=n;
                            group.dataset.stickyTop=stickyTop;
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
                                    group.style.setProperty('--olo-hp',p.toFixed(4));
                                    var pos=p*(n-1);
                                    sections.forEach(function(item,i){
                                        var pp=1-Math.min(1,Math.abs(pos-i));
                                        item.sec.style.setProperty('--olo-pp',pp.toFixed(4));
                                    });
                                    var active=Math.round(pos);
                                    if(group._oloActive!==active){
                                        group._oloActive=active;
                                        group.dataset.oloActive=active;
                                        try{window.dispatchEvent(new CustomEvent('olo:hgroup',{detail:{group:group,index:active,count:n,progress:p}}));}catch(e){}
                                    }
                                    ticking=false;
                                });}
                            }
                            window.addEventListener('scroll',onScroll,{passive:true});
                            onScroll();
                        });
                        /* Arrivo da un'ALTRA pagina con #anchor dentro un gruppo
                           (es. pallini header in modalità link): il salto nativo del
                           browser avviene prima del raggruppamento e atterra male —
                           qui si corregge sulla posizione calcolata della fermata. */
                        if(location.hash&&!window.__oloHHashDone){
                            var ht=document.getElementById(location.hash.slice(1));
                            var hg=ht?ht.closest('.olo-h-group'):null;
                            if(hg){
                                window.__oloHHashDone=true;
                                var htrk=hg.querySelector('.olo-h-track');
                                var hsecs=htrk?Array.prototype.slice.call(htrk.children):[];
                                var hidx=-1;
                                for(var hi=0;hi<hsecs.length;hi++){
                                    if(hsecs[hi]===ht||hsecs[hi].contains(ht)){hidx=hi;break;}
                                }
                                if(hidx>=0){
                                    var hst=parseInt(hg.dataset.stickyTop)||0;
                                    var hgTop=hg.getBoundingClientRect().top+window.scrollY;
                                    var htr=hg.offsetHeight-(window.innerHeight-hst);
                                    var hcnt=hsecs.length;
                                    window.scrollTo({top:Math.round(hgTop+(hcnt>1?htr*(hidx/(hcnt-1)):0)),behavior:'instant'});
                                }
                            }
                        }
                        /* Link ad anchor dentro sezioni raggruppate: l'anchor sta nel
                           track traslato, lo scroll nativo del browser porterebbe solo
                           all'inizio del gruppo. Qui il click viene rimappato sulla
                           posizione verticale della fermata corrispondente. */
                        if(!window.__oloHAnchorJump){
                            window.__oloHAnchorJump=true;
                            document.addEventListener('click',function(ev){
                                var a=ev.target.closest?ev.target.closest('a[href*="#"]'):null;
                                if(!a)return;
                                var hash=a.hash||'';
                                if(!hash||hash==='#')return;
                                var target=document.getElementById(hash.slice(1));
                                if(!target)return;
                                var grp=target.closest('.olo-h-group');
                                if(!grp)return;
                                var trk=grp.querySelector('.olo-h-track');
                                if(!trk)return;
                                var secs=Array.prototype.slice.call(trk.children);
                                var idx=-1;
                                for(var i=0;i<secs.length;i++){
                                    if(secs[i]===target||secs[i].contains(target)){idx=i;break;}
                                }
                                if(idx<0)return;
                                ev.preventDefault();
                                var st=parseInt(grp.dataset.stickyTop)||0;
                                var gTop=grp.getBoundingClientRect().top+window.scrollY;
                                var tr=grp.offsetHeight-(window.innerHeight-st);
                                var cnt=secs.length;
                                window.scrollTo({top:Math.round(gTop+(cnt>1?tr*(idx/(cnt-1)):0)),behavior:'smooth'});
                            });
                        }
                    }
                    <?php endif; ?>
                    function initAll(){
                        <?php if ( $has_any_sticky_v ) : ?>initReveal();<?php endif; ?>
                        <?php if ( $has_any_sticky_h ) : ?>initHGroups();<?php endif; ?>
                    }
                    if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',initAll)}
                    else{initAll()}
                    window.addEventListener('load',initAll);
                    <?php if ( $has_any_sticky_h ) : ?>
                    /* Pagina caricata sotto i 960px e poi allargata (rotazione tablet,
                       resize finestra): il raggruppamento è idempotente (oloHDone),
                       si può fare in ritardo. Il percorso inverso (smontare il gruppo)
                       richiederebbe un reload: caso raro, degradazione accettabile. */
                    (function(){
                        var mq=window.matchMedia('(min-width: 960px)');
                        var onCh=function(e){if(e.matches)initHGroups();};
                        if(mq.addEventListener){mq.addEventListener('change',onCh);}
                        else if(mq.addListener){mq.addListener(onCh);}
                    })();
                    <?php endif; ?>
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
              function hydrate(target){
                // ":scope > template" matcha SOLO il template figlio DIRETTO.
                // Senza :scope, il querySelector pescherebbe template di
                // data-olo-lazy ANNIDATI (es. widget dentro switcher), inserendo
                // il loro content nel data-olo-lazy padre sbagliato.
                var tpl = target.querySelector(':scope > template');
                if(!tpl) return;
                var ph = target.querySelector('.olo-lazy-ph');
                if(ph) ph.remove();
                var frag = document.importNode(tpl.content, true);
                var scripts = frag.querySelectorAll('script');
                var pending = [];
                scripts.forEach(function(s){ pending.push(s); });
                pending.forEach(function(s){ s.parentNode.removeChild(s); });
                target.appendChild(frag);
                tpl.remove();
                // Riosserva i data-olo-lazy ANNIDATI appena introdotti.
                // L'observer iniziale non li conosceva (erano dentro <template>),
                // quindi senza questo step restano svuoti per sempre.
                target.querySelectorAll('[data-olo-lazy]').forEach(function(nested){
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
                      target.querySelectorAll('[uk-' + name + '],[data-uk-' + name + ']').forEach(function(el){
                        try { if (UIkit[name]) UIkit[name](el); } catch(_){}
                      });
                    });
                    UIkit.update(target);
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
                })(pending, target);
              }
              var obs = new IntersectionObserver(function(entries){
                entries.forEach(function(e){
                  if(e.isIntersecting){
                    hydrate(e.target);
                    obs.unobserve(e.target);
                  }
                });
              }, {rootMargin: '200px'});
              lazys.forEach(function(el){ obs.observe(el); });
              /* Salti lunghi (trascinamento scrollbar, anchor): i blocchi lazy rimasti
                 SOPRA il viewport non intersecano mai l'observer → resterebbero
                 collassati a 50px, spostando tutto il layout sotto (atterraggi
                 sbagliati, sticky/pin che non agganciano). Allo scroll, idrata
                 subito tutto ciò che è già stato superato. */
              var jtk = false;
              window.addEventListener('scroll', function(){
                if(jtk) return; jtk = true;
                requestAnimationFrame(function(){
                  jtk = false;
                  document.querySelectorAll('[data-olo-lazy]').forEach(function(el){
                    if(!el.querySelector(':scope > template')) return;
                    if(el.getBoundingClientRect().bottom < 0){
                      hydrate(el);
                      obs.unobserve(el);
                    }
                  });
                });
              }, {passive: true});
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
                var core = inner / 100;
                var exp = 2.0 + (soft / 100) * 1.8;
                var stops = C(1) + ' 0%';
                for(var i = 1; i <= 10; i++){
                  var p = i / 10;
                  var a = p <= core ? 1 : Math.pow(1 - (p - core) / (1 - core), exp);
                  if(a < 0.004) a = 0;
                  stops += ', ' + C(+a.toFixed(3)) + ' ' + (p * 100).toFixed(1) + '%';
                }
                var grad = 'radial-gradient(circle, ' + stops + ')';
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
        do_action( 'olobuild_frontend_rendered', $id );

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
}
