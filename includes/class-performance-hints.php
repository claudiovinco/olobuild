<?php
/**
 * Olobuild_Performance_Hints — Resource hints (preload, preconnect, dns-prefetch),
 * fetchpriority for hero images, video facade, font preloading, srcset helper.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Performance_Hints {

    private static $instance = null;

    /** @var int Number of <video> tags converted to lazy by the output buffer */
    private $lazy_video_count = 0;

    /** @var bool Feature attive nel buffer di output */
    private $buffer_lazy_videos  = false;
    private $buffer_uikit_subset = false;

    /** @var array Fonts that need preloading */
    private $preload_fonts = [];

    /** @var bool DNS prefetch automatico per i video della pagina */
    private $hint_automatici = true;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init() {
        $opt = class_exists( 'Olobuild_Performance_Settings' )
            ? Olobuild_Performance_Settings::get_option()
            : [ 'resource_hints' => true, 'font_preload' => true ];

        // I domini scritti a mano escono sempre; l'interruttore comanda la parte
        // automatica (YouTube e Vimeo quando la pagina li contiene).
        add_action( 'wp_head', [ $this, 'output_resource_hints' ], 2 );
        $this->hint_automatici = ! empty( $opt['resource_hints'] );

        if ( ! empty( $opt['font_preload'] ) ) {
            add_action( 'wp_head', [ $this, 'output_font_preload' ], 3 );
        }

        // «fetchpriority hero image» agisce nel renderer (Olobuild_Tile_Utils::arma_lcp),
        // la facciata dei video sta nella tile Video, le immagini sono già pigre di serie:
        // i filtri olo_image_attributes / olo_video_embed non li chiamava nessuno.

        // Buffer dell'output frontend, condiviso da due feature:
        // - lazy_videos: <video autoplay muted> → preload="none" + IntersectionObserver
        // - uikit_subset: apprendimento famiglie uk-* usate + auto-guarigione
        $this->buffer_lazy_videos  = ! empty( $opt['lazy_videos'] );
        $this->buffer_uikit_subset = ! empty( $opt['uikit_subset'] ) && class_exists( 'Olobuild_Uikit_Subset' );
        if ( $this->buffer_lazy_videos || $this->buffer_uikit_subset ) {
            add_action( 'template_redirect', [ $this, 'start_lazy_video_buffer' ], 1 );
        }

        // CSS static file output filter — sempre attivo, gating interno via css_cache_files in cache_css()
        add_filter( 'olo_template_css_output', [ $this, 'css_to_file' ], 10, 2 );

        // Head cleanup
        if ( ! empty( $opt['remove_jquery_migrate'] ) ) {
            add_action( 'wp_default_scripts', function ( $scripts ) {
                if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
                    $deps = $scripts->registered['jquery']->deps;
                    $scripts->registered['jquery']->deps = array_diff( $deps, [ 'jquery-migrate' ] );
                }
            } );
        }
        if ( ! empty( $opt['remove_emoji_scripts'] ) ) {
            remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
            remove_action( 'wp_print_styles', 'print_emoji_styles' );
            remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
            remove_action( 'admin_print_styles', 'print_emoji_styles' );
            remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
            remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
        }
        if ( ! empty( $opt['remove_block_css'] ) ) {
            add_action( 'wp_enqueue_scripts', function () {
                wp_dequeue_style( 'wp-block-library' );
                wp_dequeue_style( 'wp-block-library-theme' );
                wp_dequeue_style( 'global-styles' );
            }, 100 );
        }
        if ( ! empty( $opt['remove_classic_theme'] ) ) {
            add_action( 'wp_enqueue_scripts', function () {
                wp_dequeue_style( 'classic-theme-styles' );
            }, 100 );
        }
    }

    /* ─────────────────────────────────────────────
     * Resource Hints
     * ───────────────────────────────────────────── */

    public function output_resource_hints() {
        $hints = [];

        // Nota: nessun hint verso fonts.googleapis.com / fonts.gstatic.com.
        // I Google Fonts sono self-hosted (Olobuild_Font_Host), serviti da /uploads.

        // YouTube/Vimeo preconnect only if video tiles detected
        if ( $this->hint_automatici && $this->page_has_video_tile() ) {
            $hints[] = '<link rel="dns-prefetch" href="//www.youtube.com" />';
            $hints[] = '<link rel="dns-prefetch" href="//player.vimeo.com" />';
            $hints[] = '<link rel="dns-prefetch" href="//i.ytimg.com" />';
        }

        // Domini custom configurati dall'utente
        $opt = class_exists( 'Olobuild_Performance_Settings' ) ? Olobuild_Performance_Settings::get_option() : [];
        $dns = preg_split( '/\r\n|\r|\n/', (string) ( $opt['dns_prefetch_domains'] ?? '' ) );
        foreach ( $dns as $d ) {
            $d = trim( $d );
            if ( $d ) $hints[] = '<link rel="dns-prefetch" href="' . esc_attr( $d ) . '" />';
        }
        $pre = preg_split( '/\r\n|\r|\n/', (string) ( $opt['preconnect_domains'] ?? '' ) );
        foreach ( $pre as $d ) {
            $d = trim( $d );
            if ( $d ) $hints[] = '<link rel="preconnect" href="' . esc_url( $d ) . '" crossorigin />';
        }

        if ( ! $hints ) {
            return;
        }
        echo implode( "\n", $hints ) . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- <link> hint tags built above from fixed literals plus esc_attr()/esc_url()'d user domains
    }

    /* ─────────────────────────────────────────────
     * Font Preload
     * ───────────────────────────────────────────── */

    /**
     * Precarica i file dei due caratteri principali (testo e titoli), al peso usato.
     * Prima leggeva olobuild_styles['body_font'], che nessuno scrive: non usciva mai
     * niente. I file sono quelli self-hosted che lo Style System scrive nel CSS
     * globale (blocco «latin»), oppure quelli dei font caricati a mano. Massimo 2.
     */
    public function output_font_preload() {
        if ( ! wp_style_is( 'olo-frontend-css', 'enqueued' ) || ! class_exists( 'Olobuild_Style_System' ) ) {
            return;
        }
        $ss  = Olobuild_Style_System::instance();
        $t   = $ss->get_styles()['typography'] ?? [];
        $css = $ss->generate_css();
        $set = [];
        foreach ( (array) $ss->get_global_typography() as $g ) {
            if ( ! empty( $g['id'] ) && ! empty( $g['family'] ) ) {
                $set[ $g['id'] ] = $g['family'];
            }
        }
        $urls = [];
        $coppie = [
            [ $t['font_family'] ?? '', (int) ( $t['font_weight_body'] ?? 400 ) ],
            [ $t['font_family_heading'] ?? '', (int) ( $t['font_weight_heading'] ?? 700 ) ],
        ];
        foreach ( $coppie as $c ) {
            $valore = (string) $c[0];
            $peso   = $c[1] ?: 400;
            if ( preg_match( '/--olo-font-([a-z0-9_-]+)-family/', $valore, $m ) && isset( $set[ $m[1] ] ) ) {
                $valore = $set[ $m[1] ];
            }
            $fam = trim( explode( ',', $valore )[0], " '\"" );
            if ( '' === $fam || false !== strpos( $fam, 'var(' ) ) {
                continue;
            }
            $u = self::woff2_latin( $css, $fam, $peso );
            if ( ! $u ) {
                $u = self::woff2_caricato( $fam, $peso );
            }
            if ( $u ) {
                $urls[ $u ] = true;
            }
        }
        foreach ( array_slice( array_keys( $urls ), 0, 2 ) as $u ) {
            echo '<link rel="preload" href="' . esc_url( $u ) . '" as="font" type="font/woff2" crossorigin />' . "\n";
        }
    }

    /** Il woff2 «latin», non corsivo, col peso più vicino, fra gli @font-face del CSS globale. */
    private static function woff2_latin( $css, $fam, $peso ) {
        preg_match_all( '#/\*\s*latin\s*\*/\s*@font-face\s*\{([^}]*)\}#i', (string) $css, $m );
        $best = '';
        $dist = PHP_INT_MAX;
        foreach ( $m[1] as $b ) {
            if ( ! preg_match( '/font-family:\s*[\'"]?([^;\'"]+)/i', $b, $f ) || 0 !== strcasecmp( trim( $f[1] ), $fam ) ) {
                continue;
            }
            if ( false !== stripos( $b, 'italic' ) || ! preg_match( '/url\(([^)]+\.woff2)\)/i', $b, $u ) ) {
                continue;
            }
            preg_match( '/font-weight:\s*(\d+)(?:\s+(\d+))?/', $b, $w );
            $lo = (int) ( $w[1] ?? 400 );
            $hi = (int) ( $w[2] ?? $lo );
            $d  = ( $peso >= $lo && $peso <= $hi ) ? 0 : min( abs( $peso - $lo ), abs( $peso - $hi ) );
            if ( $d < $dist ) {
                $dist = $d;
                $best = trim( $u[1], '\'"' );
            }
        }
        return $best;
    }

    /** Fra i font caricati a mano (Olobuild_Custom_Fonts), la variante woff2 col peso più vicino. */
    private static function woff2_caricato( $fam, $peso ) {
        if ( ! class_exists( 'Olobuild_Custom_Fonts' ) ) {
            return '';
        }
        $best = '';
        $dist = PHP_INT_MAX;
        foreach ( (array) Olobuild_Custom_Fonts::get_fonts() as $font ) {
            if ( 0 !== strcasecmp( (string) ( $font['name'] ?? '' ), $fam ) ) {
                continue;
            }
            foreach ( (array) ( $font['variants'] ?? [] ) as $v ) {
                $file = (string) ( $v['file'] ?? $v['url'] ?? '' );
                if ( '' === $file || ! preg_match( '/\.woff2(\?|$)/i', $file ) || 'italic' === ( $v['style'] ?? '' ) ) {
                    continue;
                }
                $d = abs( (int) ( $v['weight'] ?? 400 ) - $peso );
                if ( $d < $dist ) {
                    $dist = $d;
                    $best = $file;
                }
            }
        }
        return $best;
    }

    /* ─────────────────────────────────────────────
     * Lazy <video> self-hosted (output buffer)
     * ───────────────────────────────────────────── */

    /**
     * Start the output buffer that converts decorative autoplay videos to lazy.
     * Hooked on template_redirect (frontend only).
     */
    public function start_lazy_video_buffer() {
        if ( is_feed() || is_robots() || is_embed() || is_customize_preview() ) {
            return;
        }
        ob_start( [ $this, 'lazy_videos_html' ] );
    }

    /**
     * Convert decorative <video autoplay muted> tags to lazy-loading:
     * strip autoplay, force preload="none", mark with data-olo-lazyvid.
     * NB: il marcatore NON è data-olo-lazy — quell'attributo appartiene al
     * lazy-render delle tile (template.olo-lazy-content nel renderer).
     * The olo-lazy-video.js runtime (injected before </body> only when needed)
     * plays/pauses them via IntersectionObserver.
     *
     * Skipped: videos with controls (user-initiated), inside <script>/<noscript>,
     * inside SVG foreignObject (xmlns attribute — no JS reachable in some contexts),
     * or explicitly marked data-olo-eager.
     *
     * @param string $html Full page HTML.
     * @return string
     */
    public function lazy_videos_html( $html ) {
        // UIkit subset: apprendi le famiglie usate / inietta fallback se servono
        if ( $this->buffer_uikit_subset && is_string( $html ) ) {
            $html = Olobuild_Uikit_Subset::learn_and_heal( $html );
        }

        if ( ! $this->buffer_lazy_videos || ! is_string( $html ) || stripos( $html, '<video' ) === false ) {
            return $html;
        }

        // Non toccare i tag dentro <script>/<noscript> (stringhe JS, fallback no-js).
        $parts = preg_split( '/(<script\b.*?<\/script>|<noscript\b.*?<\/noscript>)/is', $html, -1, PREG_SPLIT_DELIM_CAPTURE );
        if ( false === $parts ) {
            return $html;
        }

        $this->lazy_video_count = 0;
        foreach ( $parts as $i => $part ) {
            if ( $i % 2 === 1 || stripos( $part, '<video' ) === false ) {
                continue;
            }
            $parts[ $i ] = preg_replace_callback(
                '/<video\b[^>]*>/i',
                [ $this, 'lazy_video_tag' ],
                $part
            );
        }
        $html = implode( '', $parts );

        if ( $this->lazy_video_count > 0 ) {
            // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript -- helper lazy-video iniettato da un filtro di output-buffer che gira dopo wp_head/wp_footer; in questa fase l'enqueue non è più possibile
            $script = '<script src="' . esc_url( OLOBUILD_URL . 'assets/js/olo-lazy-video.js' ) . '?ver=' . rawurlencode( OLOBUILD_VERSION ) . '" defer></script>';
            $pos    = strripos( $html, '</body>' );
            $html   = ( false !== $pos ) ? substr_replace( $html, $script, $pos, 0 ) : $html . $script;
        }

        return $html;
    }

    /**
     * Transform a single <video ...> opening tag (preg_replace_callback).
     *
     * @param array $m Regex match.
     * @return string
     */
    public function lazy_video_tag( $m ) {
        $tag = $m[0];

        // Solo video decorativi: autoplay + muted, senza controls.
        if ( ! preg_match( '/\sautoplay\b/i', $tag )
            || ! preg_match( '/\smuted\b/i', $tag )
            || preg_match( '/\scontrols\b/i', $tag ) ) {
            return $tag;
        }
        // Opt-out esplicito, già processato, o dentro SVG foreignObject.
        if ( preg_match( '/\sdata-olo-(eager|lazyvid)\b/i', $tag ) || false !== stripos( $tag, 'xmlns=' ) ) {
            return $tag;
        }

        $this->lazy_video_count++;

        $tag = preg_replace( '/\sautoplay\b(=("[^"]*"|\'[^\']*\'))?/i', '', $tag, 1 );
        if ( preg_match( '/\spreload=/i', $tag ) ) {
            $tag = preg_replace( '/\spreload=("[^"]*"|\'[^\']*\'|[^\s>]+)/i', ' preload="none"', $tag, 1 );
        } else {
            $tag = preg_replace( '/^<video\b/i', '<video preload="none"', $tag, 1 );
        }

        return preg_replace( '/^<video\b/i', '<video data-olo-lazyvid data-olo-autoplay', $tag, 1 );
    }

    /* ─────────────────────────────────────────────
     * CSS to static file
     * ───────────────────────────────────────────── */

    /**
     * Convert inline CSS to a cached static file.
     *
     * @param string $css         Raw CSS string
     * @param int    $template_id Template ID
     * @return string URL of CSS file or empty string to use inline fallback
     */
    public function css_to_file( $css, $template_id ) {
        if ( empty( $css ) || empty( $template_id ) ) {
            return '';
        }

        $url = Olobuild_Asset_Optimizer::cache_css( $css, $template_id );
        return $url ? $url : '';
    }

    /* ─────────────────────────────────────────────
     * Helpers
     * ───────────────────────────────────────────── */

    /**
     * Check if current page has video tiles (for preconnect hints).
     */
    private function page_has_video_tile() {
        if ( ! is_singular() ) {
            return false;
        }
        $pid = get_queried_object_id();
        $db  = new Olobuild_Database();
        $ids = array_filter( [
            (int) get_post_meta( $pid, '_olo_template_id', true ),
            (int) get_option( 'olobuild_active_single_' . get_post_type( $pid ), 0 ),
        ] );
        foreach ( $ids as $id ) {
            $t = $db->get_template( $id );
            if ( $t && preg_match( '#youtu(?:\.be|be\.com)|vimeo\.com#i', (string) wp_json_encode( $t['content'] ?? [] ) ) ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Generate responsive srcset string for an image URL.
     *
     * @param int    $attachment_id WP attachment ID
     * @param string $sizes        Sizes attribute value
     * @return array ['srcset' => string, 'sizes' => string]
     */
    public static function get_responsive_image_attrs( $attachment_id, $sizes = '100vw' ) {
        if ( ! $attachment_id ) {
            return [];
        }

        $srcset = wp_get_attachment_image_srcset( $attachment_id, 'full' );
        if ( ! $srcset ) {
            return [];
        }

        return [
            'srcset' => $srcset,
            'sizes'  => $sizes,
        ];
    }
}
