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

    /** @var bool Has hero image been marked with fetchpriority */
    private $hero_marked = false;

    /** @var int Number of <video> tags converted to lazy by the output buffer */
    private $lazy_video_count = 0;

    /** @var bool Feature attive nel buffer di output */
    private $buffer_lazy_videos  = false;
    private $buffer_uikit_subset = false;

    /** @var array Fonts that need preloading */
    private $preload_fonts = [];

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init() {
        $opt = class_exists( 'Olobuild_Performance_Settings' )
            ? Olobuild_Performance_Settings::get_option()
            : [
                'resource_hints' => true, 'font_preload' => true, 'fetchpriority' => true,
                'video_facade' => true, 'lazy_images' => true,
            ];

        if ( ! empty( $opt['resource_hints'] ) ) {
            add_action( 'wp_head', [ $this, 'output_resource_hints' ], 2 );
        }

        if ( ! empty( $opt['font_preload'] ) ) {
            add_action( 'wp_head', [ $this, 'output_font_preload' ], 3 );
        }

        if ( ! empty( $opt['fetchpriority'] ) ) {
            add_filter( 'olo_image_attributes', [ $this, 'add_fetchpriority' ], 10, 2 );
        }

        if ( ! empty( $opt['video_facade'] ) ) {
            add_filter( 'olo_video_embed', [ $this, 'video_facade' ], 10, 2 );
        }

        // Lazy loading: filter to add/remove loading="lazy" on below-fold images.
        if ( ! empty( $opt['lazy_images'] ) ) {
            add_filter( 'olo_image_attributes', [ $this, 'add_lazy_loading' ], 9, 2 );
        }

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

    /**
     * Add loading="lazy" to below-fold images (preserves fetchpriority hero override).
     */
    public function add_lazy_loading( $attrs, $context = [] ) {
        if ( $this->hero_marked && empty( $attrs['loading'] ) ) {
            $attrs['loading'] = 'lazy';
        }
        return $attrs;
    }

    /* ─────────────────────────────────────────────
     * Resource Hints
     * ───────────────────────────────────────────── */

    public function output_resource_hints() {
        $hints = [];

        // Nota: nessun hint verso fonts.googleapis.com / fonts.gstatic.com.
        // I Google Fonts sono self-hosted (Olobuild_Font_Host), serviti da /uploads.

        // YouTube/Vimeo preconnect only if video tiles detected
        if ( $this->page_has_video_tile() ) {
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

        echo implode( "\n", $hints ) . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- <link> hint tags built above from fixed literals plus esc_attr()/esc_url()'d user domains
    }

    /* ─────────────────────────────────────────────
     * Font Preload
     * ───────────────────────────────────────────── */

    public function output_font_preload() {
        // Preload custom fonts that are used in the global style system
        $styles = get_option( 'olobuild_styles', [] );
        if ( ! is_array( $styles ) ) {
            return;
        }

        $body_font    = $styles['body_font'] ?? '';
        $heading_font = $styles['heading_font'] ?? '';

        // If custom fonts are woff2 URLs, preload them
        $custom_fonts = get_option( 'olobuild_custom_fonts', [] );
        if ( is_array( $custom_fonts ) ) {
            foreach ( $custom_fonts as $font ) {
                $name = $font['name'] ?? '';
                $url  = $font['url'] ?? '';
                if ( empty( $url ) ) {
                    continue;
                }
                // Only preload fonts that are actually used as body or heading
                if ( $name === $body_font || $name === $heading_font ) {
                    $type = 'font/woff2';
                    if ( str_contains( $url, '.woff2' ) ) {
                        $type = 'font/woff2';
                    } elseif ( str_contains( $url, '.woff' ) ) {
                        $type = 'font/woff';
                    } elseif ( str_contains( $url, '.ttf' ) ) {
                        $type = 'font/ttf';
                    }
                    echo '<link rel="preload" href="' . esc_url( $url ) . '" as="font" type="' . esc_attr( $type ) . '" crossorigin />' . "\n";
                }
            }
        }
    }

    /* ─────────────────────────────────────────────
     * fetchpriority for hero images
     * ───────────────────────────────────────────── */

    /**
     * Add fetchpriority="high" to the first (hero) image on the page.
     * Remove loading="lazy" from above-fold images.
     *
     * @param array $attrs Image attributes
     * @param array $context ['position' => int, 'is_hero' => bool]
     * @return array Modified attributes
     */
    public function add_fetchpriority( $attrs, $context = [] ) {
        $is_hero = ! empty( $context['is_hero'] );
        $pos     = $context['position'] ?? 99;

        // First image or explicitly hero
        if ( ! $this->hero_marked ) {
            if ( $is_hero || $pos <= 1 ) {
                $attrs['fetchpriority'] = 'high';
                // Remove lazy loading from above-fold content
                unset( $attrs['loading'] );
                $this->hero_marked = true;
            }
        }

        return $attrs;
    }

    /* ─────────────────────────────────────────────
     * Video Facade (lazy-load iframes)
     * ───────────────────────────────────────────── */

    /**
     * Replace video iframe with a facade (thumbnail + play button).
     * The iframe loads only when user clicks play.
     *
     * @param string $html   Original iframe HTML
     * @param array  $settings Video settings (url, thumbnail, etc.)
     * @return string Facade HTML or original
     */
    public function video_facade( $html, $settings = [] ) {
        $url = $settings['url'] ?? '';
        if ( empty( $url ) ) {
            return $html;
        }

        // Only facade YouTube and Vimeo
        $thumb = '';
        $embed_url = '';

        if ( preg_match( '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $m ) ) {
            $video_id  = $m[1];
            $thumb     = "https://i.ytimg.com/vi/{$video_id}/hqdefault.jpg";
            $embed_url = "https://www.youtube.com/embed/{$video_id}?autoplay=1";
        } elseif ( preg_match( '/vimeo\.com\/(\d+)/', $url, $m ) ) {
            $video_id = $m[1];
            // Vimeo requires API call for thumbnail, use placeholder
            $embed_url = "https://player.vimeo.com/video/{$video_id}?autoplay=1";
        }

        // If custom thumbnail provided, use it
        if ( ! empty( $settings['thumbnail'] ) ) {
            $thumb = $settings['thumbnail'];
        }

        // If no thumbnail available, return original iframe
        if ( empty( $thumb ) || empty( $embed_url ) ) {
            return $html;
        }

        $uid = 'olo-vf-' . wp_unique_id();

        ob_start();
        ?>
        <div id="<?php echo esc_attr( $uid ); ?>" class="olo-video-facade" style="position:relative;cursor:pointer;aspect-ratio:16/9;background:#000;overflow:hidden" role="button" aria-label="<?php echo esc_attr__( 'Riproduci video', 'olobuild' ); ?>" tabindex="0">
            <img src="<?php echo esc_url( $thumb ); ?>" alt="" loading="lazy" style="width:100%;height:100%;object-fit:cover;opacity:.85" />
            <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center">
                <svg width="68" height="48" viewBox="0 0 68 48" aria-hidden="true"><path d="M66.52 7.74c-.78-2.93-2.49-5.41-5.42-6.19C55.79.13 34 0 34 0S12.21.13 6.9 1.55C3.97 2.33 2.27 4.81 1.48 7.74.06 13.05 0 24 0 24s.06 10.95 1.48 16.26c.78 2.93 2.49 5.41 5.42 6.19C12.21 47.87 34 48 34 48s21.79-.13 27.1-1.55c2.93-.78 4.64-3.26 5.42-6.19C67.94 34.95 68 24 68 24s-.06-10.95-1.48-16.26z" fill="red"/><path d="M45 24L27 14v20" fill="white"/></svg>
            </div>
        </div>
        <script>
        (function(){
            var el=document.getElementById('<?php echo esc_js( $uid ); ?>');
            if(!el)return;
            function load(){
                var iframe=document.createElement('iframe');
                iframe.src='<?php echo esc_js( $embed_url ); ?>';
                iframe.style.cssText='position:absolute;inset:0;width:100%;height:100%;border:0';
                iframe.allow='accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture';
                iframe.allowFullscreen=true;
                el.innerHTML='';
                el.style.position='relative';
                el.appendChild(iframe);
            }
            el.addEventListener('click',load);
            el.addEventListener('keydown',function(e){if(e.key==='Enter'){load()}});
        })();
        </script>
        <?php
        return ob_get_clean();
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
        global $post;
        if ( ! is_a( $post, 'WP_Post' ) ) {
            return false;
        }

        // Quick check in post content for video-related shortcodes/meta
        $template_id = get_post_meta( $post->ID, '_olo_template_id', true );
        if ( ! $template_id ) {
            return false;
        }

        // Check cached flag (set during rendering)
        $has_video = get_transient( "olo_has_video_{$template_id}" );
        return ! empty( $has_video );
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
