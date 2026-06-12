<?php
/**
 * Olo_Seo_Head — JSON-LD schema markup, Open Graph, Twitter Cards, canonical URL, robots meta.
 *
 * Reads settings from Olo_Seo_Settings options. Outputs structured data
 * and social meta tags in wp_head for all pages.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Seo_Head {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init() {
        // Meta description + robots
        add_action( 'wp_head', [ $this, 'output_meta_tags' ], 1 );
        // Webmaster verification codes
        add_action( 'wp_head', [ $this, 'output_verification_tags' ], 2 );
        // Open Graph + Twitter Cards + Canonical
        add_action( 'wp_head', [ $this, 'output_og_tags' ], 5 );
        // JSON-LD schema
        add_action( 'wp_head', [ $this, 'output_jsonld' ], 6 );
        // REST API for heading checker
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
        // Sitemap XML
        add_action( 'init', [ $this, 'register_sitemap' ] );
        // Head cleanup
        add_action( 'init', [ $this, 'cleanup_head' ] );
        // Robots.txt customization
        add_filter( 'robots_txt', [ $this, 'custom_robots_txt' ], 99 );
        // Breadcrumb shortcode
        add_shortcode( 'olo_breadcrumb', [ $this, 'shortcode_breadcrumb' ] );
        // <title> tag filter
        add_filter( 'pre_get_document_title', [ $this, 'filter_title' ], 20 );
        add_filter( 'document_title_parts', [ $this, 'filter_title_parts' ], 20 );
    }

    /* ─── Helper to read settings ─── */

    private function opt( $option_key, $field = null, $default = '' ) {
        if ( class_exists( 'Olo_Seo_Settings' ) ) {
            return Olo_Seo_Settings::get( $option_key, $field, $default );
        }
        $opts = get_option( $option_key, [] );
        if ( ! is_array( $opts ) ) return $default;
        if ( $field === null ) return $opts;
        return $opts[ $field ] ?? $default;
    }

    /* ═══════════════════════════════════════════════════
     * <title> tag
     * ═══════════════════════════════════════════════════ */

    public function filter_title( $title ) {
        if ( $this->seo_plugin_active() ) {
            return $title;
        }

        global $post;

        // Per-page custom title from meta box
        if ( is_singular() && $post ) {
            $custom = get_post_meta( $post->ID, '_olo_seo_title', true );
            if ( $custom ) {
                return $custom;
            }
        }

        return $title;
    }

    public function filter_title_parts( $parts ) {
        if ( $this->seo_plugin_active() ) {
            return $parts;
        }

        $titles = $this->opt( 'olo_seo_titles' );
        $sep    = $titles['separator'] ?? '-';

        // Replace the separator
        $parts['sep'] = $sep;

        // Apply templates based on context
        $template = '';
        if ( is_front_page() || is_home() ) {
            $template = $titles['homepage_title'] ?? '';
        } elseif ( is_singular( 'post' ) ) {
            $template = $titles['post_title'] ?? '';
        } elseif ( is_page() ) {
            $template = $titles['page_title'] ?? '';
        } elseif ( is_category() ) {
            $template = $titles['category_title'] ?? '';
        } elseif ( is_tag() ) {
            $template = $titles['tag_title'] ?? '';
        } elseif ( is_author() ) {
            $template = $titles['author_title'] ?? '';
        } elseif ( is_search() ) {
            $template = $titles['search_title'] ?? '';
        } elseif ( is_404() ) {
            $template = $titles['404_title'] ?? '';
        }

        if ( $template ) {
            $resolved = $this->resolve_title_vars( $template, $sep );
            if ( $resolved ) {
                // Override the title completely
                $parts['title'] = $resolved;
                unset( $parts['site'], $parts['tagline'] );
            }
        }

        return $parts;
    }

    private function resolve_title_vars( $template, $sep ) {
        global $post;

        $vars = [
            '%sitename%'             => get_bloginfo( 'name' ),
            '%tagline%'              => get_bloginfo( 'description' ),
            '%sep%'                  => $sep,
            '%title%'                => is_singular() && $post ? get_the_title( $post ) : '',
            '%excerpt%'              => is_singular() && $post ? wp_trim_words( wp_strip_all_tags( get_the_excerpt( $post ) ), 15 ) : '',
            '%category%'             => is_category() ? single_cat_title( '', false ) : '',
            '%category_description%' => is_category() ? category_description() : '',
            '%tag%'                  => is_tag() ? single_tag_title( '', false ) : '',
            '%search_query%'         => get_search_query(),
            '%author%'               => is_author() ? get_the_author() : '',
            '%date%'                 => is_date() ? get_the_date() : '',
            '%page%'                 => get_query_var( 'paged' ) ? 'Pagina ' . get_query_var( 'paged' ) : '',
        ];

        $result = str_replace( array_keys( $vars ), array_values( $vars ), $template );
        return trim( $result );
    }

    /* ═══════════════════════════════════════════════════
     * Meta Description + Robots
     * ═══════════════════════════════════════════════════ */

    public function output_meta_tags() {
        if ( $this->seo_plugin_active() ) {
            return;
        }

        // Meta description
        $desc = $this->get_seo_description();
        if ( $desc ) {
            echo '<meta name="description" content="' . esc_attr( $desc ) . '" />' . "\n";
        }

        // Robots meta
        $robots = $this->get_robots_directives();
        if ( ! empty( $robots ) ) {
            echo '<meta name="robots" content="' . esc_attr( implode( ', ', $robots ) ) . '" />' . "\n";
        }
    }

    private function get_robots_directives() {
        global $post;
        $directives = [];
        $adv = $this->opt( 'olo_seo_advanced' );

        // Per-page override
        if ( is_singular() && $post ) {
            $noindex  = get_post_meta( $post->ID, '_olo_seo_noindex', true );
            $nofollow = get_post_meta( $post->ID, '_olo_seo_nofollow', true );
            if ( $noindex )  $directives[] = 'noindex';
            if ( $nofollow ) $directives[] = 'nofollow';
            if ( ! empty( $directives ) ) {
                return $directives;
            }
        }

        // Global noindex settings
        if ( is_category() && ! empty( $adv['noindex_categories'] ) ) {
            $directives[] = 'noindex';
        }
        if ( is_tag() && ! empty( $adv['noindex_tags'] ) ) {
            $directives[] = 'noindex';
        }
        if ( is_author() && ! empty( $adv['noindex_author'] ) ) {
            $directives[] = 'noindex';
        }
        if ( is_date() && ! empty( $adv['noindex_date'] ) ) {
            $directives[] = 'noindex';
        }
        if ( is_search() && ! empty( $adv['noindex_search'] ) ) {
            $directives[] = 'noindex';
        }

        // Post type noindex
        if ( is_singular() && $post ) {
            $pt = get_post_type( $post );
            if ( ! empty( $adv['noindex_pt'][ $pt ] ) ) {
                $directives[] = 'noindex';
            }
        }

        if ( in_array( 'noindex', $directives, true ) ) {
            $directives[] = 'follow';
        }

        return $directives;
    }

    /* ═══════════════════════════════════════════════════
     * Webmaster Verification Tags
     * ═══════════════════════════════════════════════════ */

    public function output_verification_tags() {
        if ( $this->seo_plugin_active() ) {
            return;
        }

        $wm = $this->opt( 'olo_seo_webmaster' );

        if ( ! empty( $wm['google'] ) ) {
            echo '<meta name="google-site-verification" content="' . esc_attr( $wm['google'] ) . '" />' . "\n";
        }
        if ( ! empty( $wm['bing'] ) ) {
            echo '<meta name="msvalidate.01" content="' . esc_attr( $wm['bing'] ) . '" />' . "\n";
        }
        if ( ! empty( $wm['pinterest'] ) ) {
            echo '<meta name="p:domain_verify" content="' . esc_attr( $wm['pinterest'] ) . '" />' . "\n";
        }
        if ( ! empty( $wm['yandex'] ) ) {
            echo '<meta name="yandex-verification" content="' . esc_attr( $wm['yandex'] ) . '" />' . "\n";
        }
    }

    /* ═══════════════════════════════════════════════════
     * Open Graph + Twitter + Canonical
     * ═══════════════════════════════════════════════════ */

    public function output_og_tags() {
        if ( $this->seo_plugin_active() ) {
            return;
        }

        global $post;

        $title       = $this->get_og_title();
        $description = $this->get_og_description();
        $url         = $this->get_canonical_url();
        $image       = $this->get_og_image();
        $site_name   = get_bloginfo( 'name' );
        $type        = is_singular() ? 'article' : 'website';
        $locale      = get_locale();

        $social = $this->opt( 'olo_seo_social' );

        // Canonical
        if ( $url ) {
            echo '<link rel="canonical" href="' . esc_url( $url ) . '" />' . "\n";
        }

        // Open Graph
        echo '<meta property="og:type" content="' . esc_attr( $type ) . '" />' . "\n";
        echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
        if ( $description ) {
            echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
        }
        if ( $url ) {
            echo '<meta property="og:url" content="' . esc_url( $url ) . '" />' . "\n";
        }
        echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '" />' . "\n";
        echo '<meta property="og:locale" content="' . esc_attr( $locale ) . '" />' . "\n";

        if ( $image ) {
            echo '<meta property="og:image" content="' . esc_url( $image ) . '" />' . "\n";
            echo '<meta property="og:image:width" content="1200" />' . "\n";
            echo '<meta property="og:image:height" content="630" />' . "\n";
        }

        // Facebook App ID
        if ( ! empty( $social['fb_app_id'] ) ) {
            echo '<meta property="fb:app_id" content="' . esc_attr( $social['fb_app_id'] ) . '" />' . "\n";
        }

        // Article dates
        if ( is_singular() && $post ) {
            echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( 'c', $post ) ) . '" />' . "\n";
            echo '<meta property="article:modified_time" content="' . esc_attr( get_the_modified_date( 'c', $post ) ) . '" />' . "\n";
        }

        // Twitter Card
        $tw_card_type = $social['twitter_card_type'] ?? ( $image ? 'summary_large_image' : 'summary' );
        echo '<meta name="twitter:card" content="' . esc_attr( $tw_card_type ) . '" />' . "\n";

        // Twitter username
        if ( ! empty( $social['twitter_user'] ) ) {
            $tw_user = ltrim( $social['twitter_user'], '@' );
            echo '<meta name="twitter:site" content="@' . esc_attr( $tw_user ) . '" />' . "\n";
        }

        // Twitter title/desc (fallback to OG)
        $tw_title = $title;
        $tw_desc  = $description;
        if ( is_singular() && $post ) {
            $custom_tw_title = get_post_meta( $post->ID, '_olo_seo_tw_title', true );
            $custom_tw_desc  = get_post_meta( $post->ID, '_olo_seo_tw_description', true );
            if ( $custom_tw_title ) $tw_title = $custom_tw_title;
            if ( $custom_tw_desc )  $tw_desc  = $custom_tw_desc;
        }

        echo '<meta name="twitter:title" content="' . esc_attr( $tw_title ) . '" />' . "\n";
        if ( $tw_desc ) {
            echo '<meta name="twitter:description" content="' . esc_attr( $tw_desc ) . '" />' . "\n";
        }
        if ( $image ) {
            echo '<meta name="twitter:image" content="' . esc_url( $image ) . '" />' . "\n";
        }
    }

    /* ═══════════════════════════════════════════════════
     * JSON-LD Structured Data
     * ═══════════════════════════════════════════════════ */

    public function output_jsonld() {
        if ( $this->seo_plugin_active() ) {
            return;
        }

        global $post;

        $schemas = [];

        // 1. WebSite schema (homepage)
        if ( is_front_page() || is_home() ) {
            $schemas[] = $this->schema_website();
        }

        // 2. Organization or Person (from Knowledge Graph settings)
        $schemas[] = $this->schema_knowledge_graph();

        // 3. BreadcrumbList (non homepage)
        if ( ! is_front_page() ) {
            $breadcrumb = $this->schema_breadcrumb();
            if ( $breadcrumb ) {
                $schemas[] = $breadcrumb;
            }
        }

        // 4. Per-page schema type override
        $custom_schema = is_singular() && $post ? get_post_meta( $post->ID, '_olo_seo_schema_type', true ) : '';

        if ( $custom_schema === 'none' ) {
            // User explicitly disabled schema for this page
        } elseif ( $custom_schema && $custom_schema !== '' ) {
            $schemas[] = $this->schema_custom_type( $custom_schema );
        } else {
            // Auto-detect
            if ( is_singular( 'post' ) ) {
                $schemas[] = $this->schema_article();
            } elseif ( is_page() ) {
                $schemas[] = $this->schema_webpage();
            }

            // WooCommerce Product
            if ( function_exists( 'is_product' ) && is_product() ) {
                $product_schema = $this->schema_product();
                if ( $product_schema ) {
                    $schemas[] = $product_schema;
                }
            }
        }

        // FAQ Schema (from per-page meta box)
        if ( is_singular() && $post ) {
            $faq_schema = $this->schema_faq( $post->ID );
            if ( $faq_schema ) {
                $schemas[] = $faq_schema;
            }
        }

        // LocalBusiness (from settings)
        $lb = $this->schema_local_business();
        if ( $lb ) {
            $schemas[] = $lb;
        }

        // Social profiles sameAs
        $this->add_same_as( $schemas );

        foreach ( $schemas as $schema ) {
            if ( ! empty( $schema ) ) {
                echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-LD payload safely encoded via wp_json_encode() inside a fixed <script type="application/ld+json"> wrapper
            }
        }

        // 5. JSON-LD custom per-page (textarea utente, validato come JSON parsabile).
        if ( is_singular() && $post ) {
            $extra = get_post_meta( $post->ID, '_olo_seo_extra_jsonld', true );
            if ( is_string( $extra ) && trim( $extra ) !== '' ) {
                $clean = trim( $extra );
                // L'utente può aver incluso il wrapping <script> per copia/incolla — lo rimuoviamo.
                $clean = preg_replace( '#</?script[^>]*>#i', '', $clean );
                $decoded = json_decode( $clean, true );
                if ( is_array( $decoded ) ) {
                    echo '<script type="application/ld+json">' . wp_json_encode( $decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- user JSON-LD is json_decode()-validated then re-encoded via wp_json_encode() (script tags stripped above), inside a fixed <script type="application/ld+json"> wrapper
                }
            }
        }
    }

    /* ─── Schema generators ─── */

    private function schema_website() {
        return [
            '@context'        => 'https://schema.org',
            '@type'           => 'WebSite',
            'name'            => get_bloginfo( 'name' ),
            'description'     => get_bloginfo( 'description' ),
            'url'             => home_url( '/' ),
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => home_url( '/?s={search_term_string}' ),
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    private function schema_knowledge_graph() {
        $titles = $this->opt( 'olo_seo_titles' );
        $type   = $titles['kg_type'] ?? 'Organization';
        $name   = $titles['kg_name'] ?? get_bloginfo( 'name' );
        $logo   = $titles['kg_logo'] ?? '';

        $schema = [
            '@context' => 'https://schema.org',
            '@type'    => $type,
            'name'     => $name,
            'url'      => home_url( '/' ),
        ];

        if ( ! $logo ) {
            $logo_id = get_theme_mod( 'custom_logo' );
            if ( $logo_id ) {
                $logo = wp_get_attachment_image_url( $logo_id, 'full' );
            }
        }

        if ( $logo ) {
            if ( $type === 'Person' ) {
                $schema['image'] = $logo;
            } else {
                $schema['logo'] = $logo;
            }
        }

        return $schema;
    }

    private function schema_breadcrumb() {
        global $post;
        $items = [];
        $pos   = 1;

        $items[] = [
            '@type'    => 'ListItem',
            'position' => $pos++,
            'name'     => 'Home',
            'item'     => home_url( '/' ),
        ];

        if ( is_singular() && $post ) {
            if ( is_singular( 'post' ) ) {
                $cats = get_the_category( $post->ID );
                if ( ! empty( $cats ) ) {
                    $cat = $cats[0];
                    $items[] = [
                        '@type'    => 'ListItem',
                        'position' => $pos++,
                        'name'     => $cat->name,
                        'item'     => get_category_link( $cat->term_id ),
                    ];
                }
            }

            if ( is_page() ) {
                $ancestors = array_reverse( get_post_ancestors( $post ) );
                foreach ( $ancestors as $anc_id ) {
                    $items[] = [
                        '@type'    => 'ListItem',
                        'position' => $pos++,
                        'name'     => get_the_title( $anc_id ),
                        'item'     => get_permalink( $anc_id ),
                    ];
                }
            }

            $items[] = [
                '@type'    => 'ListItem',
                'position' => $pos,
                'name'     => get_the_title( $post->ID ),
                'item'     => get_permalink( $post->ID ),
            ];
        } elseif ( is_category() ) {
            $items[] = [
                '@type'    => 'ListItem',
                'position' => $pos,
                'name'     => single_cat_title( '', false ),
                'item'     => get_category_link( get_queried_object_id() ),
            ];
        } elseif ( is_tag() ) {
            $items[] = [
                '@type'    => 'ListItem',
                'position' => $pos,
                'name'     => single_tag_title( '', false ),
            ];
        } elseif ( is_search() ) {
            $items[] = [
                '@type'    => 'ListItem',
                'position' => $pos,
                'name'     => 'Risultati ricerca',
            ];
        }

        if ( count( $items ) < 2 ) {
            return null;
        }

        return [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    private function schema_article() {
        global $post;
        if ( ! $post ) return [];

        $schema = [
            '@context'      => 'https://schema.org',
            '@type'         => 'Article',
            'headline'      => get_the_title( $post ),
            'url'           => get_permalink( $post ),
            'datePublished' => get_the_date( 'c', $post ),
            'dateModified'  => get_the_modified_date( 'c', $post ),
            'author'        => [
                '@type' => 'Person',
                'name'  => get_the_author_meta( 'display_name', $post->post_author ),
            ],
            'publisher'     => [
                '@type' => 'Organization',
                'name'  => get_bloginfo( 'name' ),
            ],
        ];

        $image = get_the_post_thumbnail_url( $post, 'large' );
        if ( $image ) {
            $schema['image'] = $image;
        }

        $excerpt = get_the_excerpt( $post );
        if ( $excerpt ) {
            $schema['description'] = wp_strip_all_tags( $excerpt );
        }

        $logo_id = get_theme_mod( 'custom_logo' );
        if ( $logo_id ) {
            $logo_url = wp_get_attachment_image_url( $logo_id, 'full' );
            if ( $logo_url ) {
                $schema['publisher']['logo'] = [
                    '@type' => 'ImageObject',
                    'url'   => $logo_url,
                ];
            }
        }

        return $schema;
    }

    private function schema_webpage() {
        global $post;
        if ( ! $post ) return [];

        return [
            '@context'      => 'https://schema.org',
            '@type'         => 'WebPage',
            'name'          => get_the_title( $post ),
            'url'           => get_permalink( $post ),
            'datePublished' => get_the_date( 'c', $post ),
            'dateModified'  => get_the_modified_date( 'c', $post ),
            'description'   => $this->get_seo_description(),
        ];
    }

    private function schema_custom_type( $type ) {
        global $post;
        if ( ! $post ) return [];

        $schema = [
            '@context'      => 'https://schema.org',
            '@type'         => $type,
            'name'          => get_the_title( $post ),
            'url'           => get_permalink( $post ),
            'datePublished' => get_the_date( 'c', $post ),
            'dateModified'  => get_the_modified_date( 'c', $post ),
            'description'   => $this->get_seo_description(),
        ];

        $image = get_the_post_thumbnail_url( $post, 'large' );
        if ( $image ) {
            $schema['image'] = $image;
        }

        if ( in_array( $type, [ 'Article', 'BlogPosting', 'NewsArticle' ], true ) ) {
            $schema['author'] = [
                '@type' => 'Person',
                'name'  => get_the_author_meta( 'display_name', $post->post_author ),
            ];
            $schema['publisher'] = [
                '@type' => 'Organization',
                'name'  => get_bloginfo( 'name' ),
            ];
        }

        return $schema;
    }

    private function schema_product() {
        global $post;
        if ( ! $post || ! function_exists( 'wc_get_product' ) ) {
            return null;
        }

        $product = wc_get_product( $post->ID );
        if ( ! $product ) {
            return null;
        }

        $schema = [
            '@context'    => 'https://schema.org',
            '@type'       => 'Product',
            'name'        => $product->get_name(),
            'url'         => get_permalink( $post ),
            'description' => wp_strip_all_tags( $product->get_short_description() ),
        ];

        $image = get_the_post_thumbnail_url( $post, 'large' );
        if ( $image ) {
            $schema['image'] = $image;
        }

        if ( $product->get_sku() ) {
            $schema['sku'] = $product->get_sku();
        }

        $schema['offers'] = [
            '@type'         => 'Offer',
            'price'         => $product->get_price(),
            'priceCurrency' => get_woocommerce_currency(),
            'availability'  => $product->is_in_stock()
                ? 'https://schema.org/InStock'
                : 'https://schema.org/OutOfStock',
            'url'           => get_permalink( $post ),
        ];

        $count  = $product->get_review_count();
        $rating = $product->get_average_rating();
        if ( $count > 0 ) {
            $schema['aggregateRating'] = [
                '@type'       => 'AggregateRating',
                'ratingValue' => $rating,
                'reviewCount' => $count,
            ];
        }

        return $schema;
    }

    private function schema_local_business() {
        $biz = $this->opt( 'olo_seo_local_business' );
        if ( empty( $biz ) || empty( $biz['name'] ) ) {
            return null;
        }

        $schema = [
            '@context'    => 'https://schema.org',
            '@type'       => ! empty( $biz['type'] ) ? $biz['type'] : 'LocalBusiness',
            'name'        => $biz['name'],
            'url'         => home_url( '/' ),
        ];

        if ( ! empty( $biz['description'] ) ) {
            $schema['description'] = $biz['description'];
        }

        if ( ! empty( $biz['address'] ) ) {
            $addr = [
                '@type'           => 'PostalAddress',
                'streetAddress'   => $biz['address']['street'] ?? '',
                'addressLocality' => $biz['address']['city'] ?? '',
                'postalCode'      => $biz['address']['zip'] ?? '',
                'addressCountry'  => $biz['address']['country'] ?? 'IT',
            ];
            if ( ! empty( $biz['address']['state'] ) ) {
                $addr['addressRegion'] = $biz['address']['state'];
            }
            $schema['address'] = $addr;
        }

        if ( ! empty( $biz['phone'] ) ) {
            $schema['telephone'] = $biz['phone'];
        }
        if ( ! empty( $biz['email'] ) ) {
            $schema['email'] = $biz['email'];
        }
        if ( ! empty( $biz['price_range'] ) ) {
            $schema['priceRange'] = $biz['price_range'];
        }

        // Geo coordinates
        if ( ! empty( $biz['geo_lat'] ) && ! empty( $biz['geo_lng'] ) ) {
            $schema['geo'] = [
                '@type'     => 'GeoCoordinates',
                'latitude'  => floatval( $biz['geo_lat'] ),
                'longitude' => floatval( $biz['geo_lng'] ),
            ];
        }

        // Business image
        if ( ! empty( $biz['image'] ) ) {
            $schema['image'] = $biz['image'];
        } else {
            $logo_id = get_theme_mod( 'custom_logo' );
            if ( $logo_id ) {
                $logo_url = wp_get_attachment_image_url( $logo_id, 'full' );
                if ( $logo_url ) {
                    $schema['logo'] = $logo_url;
                }
            }
        }

        // Opening hours
        if ( ! empty( $biz['hours'] ) ) {
            $day_map = [
                'monday'    => 'Monday',
                'tuesday'   => 'Tuesday',
                'wednesday' => 'Wednesday',
                'thursday'  => 'Thursday',
                'friday'    => 'Friday',
                'saturday'  => 'Saturday',
                'sunday'    => 'Sunday',
            ];

            $hours_specs = [];
            foreach ( $biz['hours'] as $day => $times ) {
                if ( ! empty( $times['open'] ) && ! empty( $times['close'] ) && isset( $day_map[ $day ] ) ) {
                    $hours_specs[] = [
                        '@type'     => 'OpeningHoursSpecification',
                        'dayOfWeek' => $day_map[ $day ],
                        'opens'     => $times['open'],
                        'closes'    => $times['close'],
                    ];
                }
            }
            if ( ! empty( $hours_specs ) ) {
                $schema['openingHoursSpecification'] = $hours_specs;
            }
        }

        return $schema;
    }

    /**
     * Add sameAs social profiles to Organization/Person schema.
     */
    private function add_same_as( &$schemas ) {
        $social = $this->opt( 'olo_seo_social' );
        $urls   = [];

        $keys = [ 'facebook_url', 'instagram_url', 'linkedin_url', 'youtube_url', 'pinterest_url', 'tiktok_url' ];
        foreach ( $keys as $key ) {
            if ( ! empty( $social[ $key ] ) ) {
                $urls[] = $social[ $key ];
            }
        }
        if ( ! empty( $social['twitter_user'] ) ) {
            $tw = ltrim( $social['twitter_user'], '@' );
            $urls[] = 'https://x.com/' . $tw;
        }

        if ( empty( $urls ) ) {
            return;
        }

        // Add sameAs to the Organization/Person schema
        foreach ( $schemas as &$schema ) {
            if ( isset( $schema['@type'] ) && in_array( $schema['@type'], [ 'Organization', 'Person' ], true ) ) {
                $schema['sameAs'] = $urls;
                break;
            }
        }
    }

    /**
     * FAQ Schema — generates FAQPage markup from per-page FAQ data.
     */
    private function schema_faq( $post_id ) {
        $faq_items = get_post_meta( $post_id, '_olo_seo_faq', true );
        if ( ! is_array( $faq_items ) || empty( $faq_items ) ) {
            return null;
        }

        $main_entity = [];
        foreach ( $faq_items as $item ) {
            if ( empty( $item['q'] ) || empty( $item['a'] ) ) {
                continue;
            }
            $main_entity[] = [
                '@type'          => 'Question',
                'name'           => $item['q'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => $item['a'],
                ],
            ];
        }

        if ( empty( $main_entity ) ) {
            return null;
        }

        return [
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => $main_entity,
        ];
    }

    /* ═══════════════════════════════════════════════════
     * Helpers
     * ═══════════════════════════════════════════════════ */

    private function get_seo_title() {
        global $post;

        if ( is_singular() && $post ) {
            $custom = get_post_meta( $post->ID, '_olo_seo_title', true );
            if ( $custom ) {
                return $custom;
            }
        }

        $titles = $this->opt( 'olo_seo_titles' );
        $sep    = $titles['separator'] ?? '-';

        if ( is_front_page() || is_home() ) {
            $tpl = $titles['homepage_title'] ?? '';
            if ( $tpl ) return $this->resolve_title_vars( $tpl, $sep );
            return get_bloginfo( 'name' ) . " {$sep} " . get_bloginfo( 'description' );
        }

        if ( is_singular() && $post ) {
            return get_the_title( $post ) . " {$sep} " . get_bloginfo( 'name' );
        }

        if ( is_category() ) {
            return single_cat_title( '', false ) . " {$sep} " . get_bloginfo( 'name' );
        }

        if ( is_search() ) {
            return 'Ricerca: ' . get_search_query() . " {$sep} " . get_bloginfo( 'name' );
        }

        return get_bloginfo( 'name' ) . " {$sep} " . get_bloginfo( 'description' );
    }

    private function get_seo_description() {
        global $post;

        if ( is_singular() && $post ) {
            $custom = get_post_meta( $post->ID, '_olo_seo_description', true );
            if ( $custom ) {
                return $custom;
            }
        }

        $titles = $this->opt( 'olo_seo_titles' );

        // Template-based descriptions
        if ( is_front_page() || is_home() ) {
            $tpl = $titles['homepage_desc'] ?? '';
            if ( $tpl ) return $tpl;
        } elseif ( is_singular( 'post' ) ) {
            $tpl = $titles['post_desc'] ?? '';
            if ( $tpl === '%excerpt%' && $post ) {
                $excerpt = get_the_excerpt( $post );
                if ( $excerpt ) return wp_trim_words( wp_strip_all_tags( $excerpt ), 30 );
            } elseif ( $tpl ) {
                return $this->resolve_title_vars( $tpl, '' );
            }
        } elseif ( is_category() ) {
            $tpl = $titles['category_desc'] ?? '';
            if ( $tpl === '%category_description%' ) {
                $desc = category_description();
                if ( $desc ) return wp_strip_all_tags( $desc );
            } elseif ( $tpl ) {
                return $this->resolve_title_vars( $tpl, '' );
            }
        }

        if ( is_singular() && $post ) {
            $excerpt = get_the_excerpt( $post );
            if ( $excerpt ) {
                return wp_trim_words( wp_strip_all_tags( $excerpt ), 30 );
            }
        }

        return get_bloginfo( 'description' );
    }

    private function get_og_title() {
        global $post;

        // Per-page OG title
        if ( is_singular() && $post ) {
            $custom = get_post_meta( $post->ID, '_olo_seo_og_title', true );
            if ( $custom ) {
                return $custom;
            }
        }

        return $this->get_seo_title();
    }

    private function get_og_description() {
        global $post;

        if ( is_singular() && $post ) {
            $custom = get_post_meta( $post->ID, '_olo_seo_og_description', true );
            if ( $custom ) {
                return $custom;
            }
        }

        return $this->get_seo_description();
    }

    private function get_canonical_url() {
        global $post;

        // Per-page canonical override
        if ( is_singular() && $post ) {
            $custom = get_post_meta( $post->ID, '_olo_seo_canonical', true );
            if ( $custom ) {
                return $custom;
            }
        }

        if ( is_singular() ) {
            return get_permalink();
        }
        if ( is_category() ) {
            return get_category_link( get_queried_object_id() );
        }
        if ( is_tag() ) {
            return get_tag_link( get_queried_object_id() );
        }
        if ( is_front_page() || is_home() ) {
            return home_url( '/' );
        }
        return '';
    }

    private function get_og_image() {
        global $post;

        // Per-page OG image
        if ( is_singular() && $post ) {
            $custom = get_post_meta( $post->ID, '_olo_seo_og_image', true );
            if ( $custom ) {
                return $custom;
            }

            $thumb = get_the_post_thumbnail_url( $post, 'large' );
            if ( $thumb ) {
                return $thumb;
            }
        }

        // Default OG image from settings
        $social = $this->opt( 'olo_seo_social' );
        if ( ! empty( $social['og_default_image'] ) ) {
            return $social['og_default_image'];
        }

        // Fallback: site logo
        $logo_id = get_theme_mod( 'custom_logo' );
        if ( $logo_id ) {
            return wp_get_attachment_image_url( $logo_id, 'full' );
        }

        return '';
    }

    /* ═══════════════════════════════════════════════════
     * Breadcrumb Shortcode
     * ═══════════════════════════════════════════════════ */

    public function shortcode_breadcrumb( $atts ) {
        $atts = shortcode_atts( [
            'separator' => '&raquo;',
            'class'     => 'olo-breadcrumb',
        ], $atts, 'olo_breadcrumb' );

        global $post;
        $sep   = $atts['separator'];
        $class = sanitize_html_class( $atts['class'] );
        $items = [];

        // Home
        $items[] = '<a href="' . esc_url( home_url( '/' ) ) . '">Home</a>';

        if ( is_singular() && $post ) {
            // Category for posts
            if ( is_singular( 'post' ) ) {
                $cats = get_the_category( $post->ID );
                if ( ! empty( $cats ) ) {
                    $cat = $cats[0];
                    $items[] = '<a href="' . esc_url( get_category_link( $cat->term_id ) ) . '">' . esc_html( $cat->name ) . '</a>';
                }
            }

            // Page ancestors
            if ( is_page() ) {
                $ancestors = array_reverse( get_post_ancestors( $post ) );
                foreach ( $ancestors as $anc_id ) {
                    $items[] = '<a href="' . esc_url( get_permalink( $anc_id ) ) . '">' . esc_html( get_the_title( $anc_id ) ) . '</a>';
                }
            }

            // Current
            $items[] = '<span class="olo-breadcrumb-current" aria-current="page">' . esc_html( get_the_title( $post ) ) . '</span>';

        } elseif ( is_category() ) {
            $items[] = '<span class="olo-breadcrumb-current" aria-current="page">' . esc_html( single_cat_title( '', false ) ) . '</span>';
        } elseif ( is_tag() ) {
            $items[] = '<span class="olo-breadcrumb-current" aria-current="page">' . esc_html( single_tag_title( '', false ) ) . '</span>';
        } elseif ( is_search() ) {
            $items[] = '<span class="olo-breadcrumb-current">Risultati per: ' . esc_html( get_search_query() ) . '</span>';
        } elseif ( is_404() ) {
            $items[] = '<span class="olo-breadcrumb-current">404</span>';
        }

        if ( count( $items ) < 2 ) {
            return '';
        }

        $html = '<nav class="' . esc_attr( $class ) . '" aria-label="Breadcrumb">';
        $html .= '<ol style="list-style:none;padding:0;margin:0;display:flex;flex-wrap:wrap;gap:4px;font-size:14px;">';
        foreach ( $items as $i => $item ) {
            $html .= '<li>';
            if ( $i > 0 ) {
                $html .= '<span class="olo-breadcrumb-sep" style="margin:0 6px;color:#999;">' . $sep . '</span>';
            }
            $html .= $item;
            $html .= '</li>';
        }
        $html .= '</ol></nav>';

        return $html;
    }

    /* ═══════════════════════════════════════════════════
     * Head Cleanup
     * ═══════════════════════════════════════════════════ */

    public function cleanup_head() {
        $adv = $this->opt( 'olo_seo_advanced' );

        // oEmbed discovery: quando LinkedIn trova il link json+oembed lo PREFERISCE
        // ai tag Open Graph → anteprima "Rich media" senza immagine ("No image found")
        // anche con og:image perfetta. Visto che gli OG li emettiamo noi, il discovery
        // va sempre rimosso. (Non tocca il rendering degli embed DENTRO il sito.)
        remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

        if ( ! empty( $adv['remove_shortlink'] ) ) {
            remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
            remove_action( 'template_redirect', 'wp_shortlink_header', 11 );
        }
        if ( ! empty( $adv['remove_rsd'] ) ) {
            remove_action( 'wp_head', 'rsd_link' );
        }
        if ( ! empty( $adv['remove_wlw'] ) ) {
            remove_action( 'wp_head', 'wlwmanifest_link' );
        }
        if ( ! empty( $adv['remove_generator'] ) ) {
            remove_action( 'wp_head', 'wp_generator' );
        }
        if ( ! empty( $adv['remove_feed_links'] ) ) {
            remove_action( 'wp_head', 'feed_links_extra', 3 );
        }
    }

    /* ═══════════════════════════════════════════════════
     * Robots.txt
     * ═══════════════════════════════════════════════════ */

    public function custom_robots_txt( $output ) {
        $adv = $this->opt( 'olo_seo_advanced' );
        $custom = $adv['robots_txt'] ?? '';

        if ( $custom ) {
            $output = $custom . "\n\n" . $output;
        }

        return $output;
    }

    /* ═══════════════════════════════════════════════════
     * REST API — Heading Structure Checker
     * ═══════════════════════════════════════════════════ */

    public function register_routes() {
        register_rest_route( 'olo/v1', '/heading-check/(?P<id>\d+)', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'check_heading_structure' ],
            'permission_callback' => function () {
                return current_user_can( 'edit_posts' );
            },
        ] );
    }

    public function check_heading_structure( $request ) {
        $template_id = intval( $request['id'] );
        $db          = new Olo_Database();
        $template    = $db->get_template( $template_id );

        if ( ! $template ) {
            return new WP_Error( 'not_found', 'Template non trovato', [ 'status' => 404 ] );
        }

        $data = json_decode( $template->data, true );
        if ( ! is_array( $data ) ) {
            return rest_ensure_response( [ 'issues' => [], 'headings' => [] ] );
        }

        $headings = [];
        $this->collect_headings( $data, $headings );

        $issues = $this->analyze_heading_hierarchy( $headings );

        return rest_ensure_response( [
            'headings' => $headings,
            'issues'   => $issues,
            'score'    => empty( $issues ) ? 100 : max( 0, 100 - count( $issues ) * 15 ),
        ] );
    }

    private function collect_headings( $nodes, &$headings ) {
        if ( ! is_array( $nodes ) ) return;

        foreach ( $nodes as $node ) {
            if ( ! is_array( $node ) ) continue;

            $type     = $node['type'] ?? '';
            $settings = $node['settings'] ?? [];

            if ( $type === 'headline' ) {
                $tag   = $settings['tag'] ?? 'h2';
                $text  = $settings['text'] ?? '';
                $level = $this->heading_level( $tag );
                if ( $level > 0 ) {
                    $headings[] = [
                        'tag'   => strtoupper( $tag ),
                        'level' => $level,
                        'text'  => wp_strip_all_tags( $text ) ?: '(vuoto)',
                        'id'    => $node['id'] ?? '',
                    ];
                }
            }

            if ( $type === 'content' ) {
                $content = $settings['content'] ?? '';
                if ( preg_match_all( '/<(h[1-6])[^>]*>(.*?)<\/\1>/is', $content, $m ) ) {
                    foreach ( $m[1] as $idx => $tag ) {
                        $headings[] = [
                            'tag'   => strtoupper( $tag ),
                            'level' => $this->heading_level( $tag ),
                            'text'  => wp_strip_all_tags( $m[2][ $idx ] ) ?: '(vuoto)',
                            'id'    => $node['id'] ?? '',
                        ];
                    }
                }
            }

            if ( $type === 'hero' ) {
                $tag  = $settings['title_tag'] ?? 'h1';
                $text = $settings['title'] ?? '';
                $level = $this->heading_level( $tag );
                if ( $level > 0 ) {
                    $headings[] = [
                        'tag'   => strtoupper( $tag ),
                        'level' => $level,
                        'text'  => wp_strip_all_tags( $text ) ?: '(vuoto)',
                        'id'    => $node['id'] ?? '',
                    ];
                }
            }

            if ( ! empty( $node['children'] ) ) {
                $this->collect_headings( $node['children'], $headings );
            }
        }
    }

    private function heading_level( $tag ) {
        $tag = strtolower( trim( $tag ) );
        if ( preg_match( '/^h([1-6])$/', $tag, $m ) ) {
            return intval( $m[1] );
        }
        return 0;
    }

    private function analyze_heading_hierarchy( $headings ) {
        $issues = [];

        if ( empty( $headings ) ) {
            $issues[] = [
                'type'    => 'warning',
                'message' => 'Nessun heading trovato nel template. Aggiungi almeno un H1.',
                'id'      => '',
            ];
            return $issues;
        }

        $h1_count = 0;
        foreach ( $headings as $h ) {
            if ( $h['level'] === 1 ) $h1_count++;
        }

        if ( $h1_count === 0 ) {
            $issues[] = [
                'type'    => 'error',
                'message' => 'Manca un tag H1. Ogni pagina dovrebbe avere esattamente un H1.',
                'id'      => '',
            ];
        } elseif ( $h1_count > 1 ) {
            $issues[] = [
                'type'    => 'warning',
                'message' => "Trovati {$h1_count} tag H1. Si consiglia un solo H1 per pagina.",
                'id'      => '',
            ];
        }

        $prev_level = 0;
        foreach ( $headings as $h ) {
            $level = $h['level'];
            if ( $prev_level > 0 ) {
                $gap = $level - $prev_level;
                if ( $gap > 1 ) {
                    $prev_tag = 'H' . $prev_level;
                    $curr_tag = 'H' . $level;
                    $missing  = 'H' . ( $prev_level + 1 );
                    $issues[] = [
                        'type'    => 'warning',
                        'message' => "Salto nella gerarchia: {$prev_tag} → {$curr_tag}. Manca {$missing}.",
                        'id'      => $h['id'],
                    ];
                }
            }
            $prev_level = $level;
        }

        foreach ( $headings as $h ) {
            if ( $h['text'] === '(vuoto)' ) {
                $issues[] = [
                    'type'    => 'warning',
                    'message' => "Heading {$h['tag']} vuoto. Aggiungi del testo.",
                    'id'      => $h['id'],
                ];
            }
        }

        if ( ! empty( $headings ) ) {
            $first = $headings[0];
            if ( $first['level'] !== 1 ) {
                $issues[] = [
                    'type'    => 'info',
                    'message' => "Il primo heading è {$first['tag']}. Si consiglia di iniziare con H1.",
                    'id'      => $first['id'],
                ];
            }
        }

        return $issues;
    }

    /* ═══════════════════════════════════════════════════
     * Sitemap XML
     * ═══════════════════════════════════════════════════ */

    public function register_sitemap() {
        $sitemap_opts = $this->opt( 'olo_seo_sitemap' );

        // Check if sitemap is enabled (default: yes)
        if ( isset( $sitemap_opts['enabled'] ) && ! $sitemap_opts['enabled'] ) {
            return;
        }

        add_action( 'template_redirect', [ $this, 'serve_olo_sitemap' ] );
    }

    public function serve_olo_sitemap() {
        if ( empty( $_GET['olo_sitemap'] ) ) {
            return;
        }

        $sitemap_opts = $this->opt( 'olo_seo_sitemap' );
        $max_urls     = intval( $sitemap_opts['max_urls'] ?? 1000 );
        $db           = new Olo_Database();
        $templates    = $db->get_templates();
        $urls         = [];

        foreach ( $templates as $tpl ) {
            if ( $tpl->type === 'header' || $tpl->type === 'footer' || $tpl->type === 'megapanel' ) {
                continue;
            }

            $tpl_id = intval( $tpl->id );
            $posts = get_posts( [
                'post_type'      => 'any',
                'post_status'    => 'publish',
                'posts_per_page' => $max_urls,
                'meta_query'     => [
                    [
                        'key'     => '_olo_template_id',
                        'value'   => $tpl_id,
                        'compare' => '=',
                    ],
                ],
                'fields' => 'ids',
            ] );

            foreach ( $posts as $pid ) {
                // Check noindex
                $noindex = get_post_meta( $pid, '_olo_seo_noindex', true );
                if ( $noindex ) continue;

                $url = get_permalink( $pid );
                if ( $url ) {
                    $mod = get_the_modified_date( 'c', $pid );
                    $urls[ $url ] = [ 'lastmod' => $mod, 'post_id' => $pid ];
                }
            }
        }

        // Pages with [olo_template] shortcode
        $sc_posts = get_posts( [
            'post_type'      => [ 'page', 'post' ],
            'post_status'    => 'publish',
            'posts_per_page' => $max_urls,
            's'              => '[olo_template',
            'fields'         => 'ids',
        ] );

        foreach ( $sc_posts as $pid ) {
            $noindex = get_post_meta( $pid, '_olo_seo_noindex', true );
            if ( $noindex ) continue;
            $url = get_permalink( $pid );
            if ( $url ) {
                $urls[ $url ] = [ 'lastmod' => get_the_modified_date( 'c', $pid ), 'post_id' => $pid ];
            }
        }

        // Single templates
        $public_pts = get_post_types( [ 'public' => true ], 'names' );
        foreach ( $public_pts as $pt ) {
            // Check if this post type is included in sitemap
            if ( isset( $sitemap_opts['pt'] ) && empty( $sitemap_opts['pt'][ $pt ] ) ) {
                continue;
            }

            $single_tpl = get_option( "olo_active_single_{$pt}", 0 );
            if ( $single_tpl ) {
                $pt_posts = get_posts( [
                    'post_type'      => $pt,
                    'post_status'    => 'publish',
                    'posts_per_page' => min( 100, $max_urls ),
                    'fields'         => 'ids',
                ] );
                foreach ( $pt_posts as $pid ) {
                    $noindex = get_post_meta( $pid, '_olo_seo_noindex', true );
                    if ( $noindex ) continue;
                    $url = get_permalink( $pid );
                    if ( $url ) {
                        $urls[ $url ] = [ 'lastmod' => get_the_modified_date( 'c', $pid ), 'post_id' => $pid ];
                    }
                }
            }
        }

        // Include images?
        $include_images = ! empty( $sitemap_opts['include_images'] );

        header( 'Content-Type: application/xml; charset=UTF-8' );
        header( 'X-Robots-Tag: noindex' );

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        if ( $include_images ) {
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
        } else {
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        }

        $count = 0;
        foreach ( $urls as $url => $info ) {
            if ( $count >= $max_urls ) break;
            echo "  <url>\n";
            echo '    <loc>' . esc_url( $url ) . "</loc>\n";
            if ( $info['lastmod'] ) {
                echo '    <lastmod>' . esc_html( $info['lastmod'] ) . "</lastmod>\n";
            }
            // Add featured image
            if ( $include_images && ! empty( $info['post_id'] ) ) {
                $thumb = get_the_post_thumbnail_url( $info['post_id'], 'large' );
                if ( $thumb ) {
                    echo "    <image:image>\n";
                    echo '      <image:loc>' . esc_url( $thumb ) . "</image:loc>\n";
                    echo "    </image:image>\n";
                }
            }
            echo "  </url>\n";
            $count++;
        }

        echo '</urlset>' . "\n";
        exit;
    }

    /**
     * Check if a major SEO plugin is active (to avoid duplicate tags).
     */
    private function seo_plugin_active() {
        if ( defined( 'WPSEO_VERSION' ) ) return true;       // Yoast SEO
        if ( class_exists( 'RankMath' ) ) return true;       // Rank Math
        if ( defined( 'AIOSEO_VERSION' ) ) return true;      // All in One SEO
        if ( defined( 'SEOPRESS_VERSION' ) ) return true;    // SEOPress
        return false;
    }
}
