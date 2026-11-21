<?php
/**
 * Olobuild_Seo_Head — JSON-LD schema markup, Open Graph, Twitter Cards, canonical URL, robots meta.
 *
 * Reads settings from Olobuild_Seo_Settings options. Outputs structured data
 * and social meta tags in wp_head for all pages.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Seo_Head {

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
        // L'analisi della pagina, per il builder e per il riquadro di WordPress (olobuild/v1/seo/analisi).
        require_once __DIR__ . '/class-seo-analisi.php';
        Olobuild_Seo_Analisi::init();
        // Sitemap: quella di WordPress (wp-sitemap.xml), che comprende tutto il sito.
        // La vecchia /?olo_sitemap=1 elencava solo le pagine fatte con Olobuild.
        add_filter( 'wp_sitemaps_enabled', [ $this, 'sitemap_attiva' ] );
        add_filter( 'wp_sitemaps_posts_query_args', [ $this, 'sitemap_senza_noindex' ], 10, 2 );
        add_filter( 'wp_sitemaps_post_types', [ $this, 'sitemap_tipi' ] );
        // Gli archivi esclusi dai motori escono anche dalla sitemap (autori, categorie, tag: 1.4.1265)
        add_filter( 'wp_sitemaps_add_provider', [ $this, 'sitemap_senza_autori' ], 10, 2 );
        add_filter( 'wp_sitemaps_taxonomies', [ $this, 'sitemap_tassonomie' ] );
        add_action( 'template_redirect', [ $this, 'vecchia_sitemap' ], 0 );
        // llms.txt: il riassunto del sito per le intelligenze artificiali (Impostazioni › SEO, 1.4.1265)
        add_action( 'parse_request', [ $this, 'servi_llms_txt' ], 0 );
        // Separatore del titolo, robots e canonical passano dai filtri di WordPress:
        // prima il separatore finiva in coda al titolo («Pagina – Sito – –») e
        // canonical e meta robots uscivano due volte.
        add_filter( 'document_title_separator', [ $this, 'filter_title_separator' ], 20 );
        add_filter( 'wp_robots', [ $this, 'filter_wp_robots' ], 20 );
        add_filter( 'get_canonical_url', [ $this, 'filter_canonical' ], 10, 2 );
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
        if ( class_exists( 'Olobuild_Seo_Settings' ) ) {
            return Olobuild_Seo_Settings::get( $option_key, $field, $default );
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
        $sep    = $this->filter_title_separator( '-' );

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
                return $parts;
            }
        }

        // Schema globale della scheda SEO: {page} è il titolo che WordPress ha già
        // calcolato per questa vista. Senza {page} non si applica (ogni pagina
        // avrebbe lo stesso titolo). Il valore di serie dà il titolo di WordPress.
        $pattern = trim( (string) ( $titles['pattern'] ?? '' ) );
        if ( ! is_front_page() && false !== strpos( $pattern, '{page}' ) && ! empty( $parts['title'] ) ) {
            $parts['title'] = trim( strtr( $pattern, [
                '{page}' => $parts['title'],
                '{sep}'  => $sep,
                '{site}' => get_bloginfo( 'name', 'display' ),
            ] ) );
            unset( $parts['site'], $parts['tagline'] );
        }

        return $parts;
    }

    public function filter_title_separator( $sep ) {
        if ( $this->seo_plugin_active() ) {
            return $sep;
        }
        $t = $this->opt( 'olo_seo_titles' );
        return ( isset( $t['separator'] ) && '' !== trim( (string) $t['separator'] ) ) ? $t['separator'] : $sep;
    }

    public function filter_wp_robots( $robots ) {
        if ( $this->seo_plugin_active() ) {
            return $robots;
        }
        foreach ( $this->get_robots_directives() as $d ) {
            if ( 'follow' === $d && ! empty( $robots['nofollow'] ) ) {
                continue;
            }
            $robots[ $d ] = true;
        }
        return $robots;
    }

    public function filter_canonical( $url, $post ) {
        if ( $this->seo_plugin_active() || ! $post ) {
            return $url;
        }
        $c = get_post_meta( $post->ID, '_olo_seo_canonical', true );
        return $c ? esc_url_raw( $c ) : $url;
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

        // Robots: nel meta del core (filter_wp_robots), non in un secondo meta.
    }

    private function get_robots_directives() {
        global $post;
        $directives = [];
        $adv = $this->opt( 'olobuild_seo_advanced' );

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
        // Gli archivi per data sono esclusi di serie (1.4.1265): ripetono gli articoli e non li cerca nessuno.
        if ( is_date() && ( ! is_array( $adv ) || ! array_key_exists( 'noindex_date', $adv ) || ! empty( $adv['noindex_date'] ) ) ) {
            $directives[] = 'noindex';
        }
        if ( is_search() && ! empty( $adv['noindex_search'] ) ) {
            $directives[] = 'noindex';
        }

        // Post type noindex: le schede e l'archivio del tipo
        if ( is_singular() && $post ) {
            $pt = get_post_type( $post );
            if ( ! empty( $adv['noindex_pt'][ $pt ] ) ) {
                $directives[] = 'noindex';
            }
        } elseif ( is_post_type_archive() ) {
            $pt = get_query_var( 'post_type' );
            $pt = is_array( $pt ) ? reset( $pt ) : $pt;
            if ( $pt && ! empty( $adv['noindex_pt'][ $pt ] ) ) {
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

        // Canonical: sulle pagine singole lo scrive WordPress (rel_canonical, con il
        // valore della pagina via filter_canonical); qui archivi e home.
        if ( $url && ! is_singular() ) {
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
        $tw_card_type = $social['twitter_card_type'] ?? '';
        if ( '' === $tw_card_type || ( 'summary_large_image' === $tw_card_type && ! $image ) ) {
            $tw_card_type = $image ? 'summary_large_image' : 'summary';
        }
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

        /**
         * Il grafo dei dati strutturati della pagina, prima di uscire: un altro plugin di OLOtheme ci aggiunge i
         * suoi nodi (un hotel, una sala), collegandoli per @id a quelli di qui.
         *
         * @param array $grafo I nodi, ognuno col suo @id.
         */
        $grafo = apply_filters( 'olobuild_seo_grafo', $this->grafo_schema() );
        if ( is_array( $grafo ) && $grafo ) {
            $grafo = $this->grafo_nella_lingua( $grafo );
            echo '<script type="application/ld+json">' . wp_json_encode( [ '@context' => 'https://schema.org', '@graph' => array_values( array_filter( $grafo ) ) ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-LD payload safely encoded via wp_json_encode() inside a fixed <script type="application/ld+json"> wrapper
        }

        // JSON-LD personalizzato della pagina (textarea utente, validato come JSON parsabile): un blocco a sé.
        if ( is_singular() && $post ) {
            $extra = get_post_meta( $post->ID, '_olo_seo_extra_jsonld', true );
            if ( is_string( $extra ) && trim( $extra ) !== '' ) {
                $clean = trim( $extra );
                // L'utente può aver incluso il wrapping <script> per copia/incolla — lo rimuoviamo.
                $clean = preg_replace( '#</?script[^>]*>#i', '', $clean );
                $decoded = json_decode( $clean, true );
                if ( is_array( $decoded ) ) {
                    echo '<script type="application/ld+json">' . wp_json_encode( $decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- user JSON-LD is json_decode()-validated then re-encoded via wp_json_encode() (script tags stripped above), inside a fixed <script type="application/ld+json"> wrapper
                }
            }
        }
    }

    /**
     * I dati strutturati come UN grafo, coi nodi collegati per @id (novembre 2026). Prima uscivano quattro
     * blocchi separati, senza legami: chi pubblica, il sito, la pagina e l'articolo non risultavano la stessa
     * cosa. Ora la pagina fa parte del sito, l'articolo è il contenuto principale della pagina, il sito e
     * l'articolo sono pubblicati da chi pubblica il sito: è così che motori e IA riconoscono le entità.
     */
    /**
     * I testi del grafo nella lingua di chi guarda, con OLOlang (21 nov 2026): nomi, titoli e descrizioni prendono la
     * traduzione salvata di quel testo, fra le stringhe globali (titoli e descrizioni delle pagine, descrizione del sito)
     * e le traduzioni del template della pagina (le FAQ di un Accordion). Prima i dati strutturati restavano in italiano
     * in ogni lingua: OLOlang traduce la pagina finita, ma salta gli script, e il JSON-LD è uno script. Senza OLOlang, o
     * nella lingua principale, non cambia niente.
     */
    private function grafo_nella_lingua( $grafo ) {
        if ( ! class_exists( 'Olo_Lang_Language' ) || ! class_exists( 'Olo_Lang_Database' )
            || Olo_Lang_Language::detect_current_lang() === Olo_Lang_Language::get_default_lang() ) {
            return $grafo;
        }
        $chiavi = array_flip( [ 'name', 'headline', 'alternativeHeadline', 'description', 'alternateName', 'slogan', 'text', 'caption', 'disambiguatingDescription' ] );
        $giu    = function ( $nodo ) use ( &$giu, $chiavi ) {
            foreach ( $nodo as $k => $v ) {
                if ( is_string( $v ) && is_string( $k ) && isset( $chiavi[ $k ] ) && '' !== trim( $v ) ) {
                    $nodo[ $k ] = self::testo_nella_lingua( $v );
                } elseif ( is_array( $v ) ) {
                    $nodo[ $k ] = $giu( $v );
                }
            }
            return $nodo;
        };
        return $giu( $grafo );
    }

    /**
     * Un testo del grafo tradotto: intero, poi senza entità e con l'apostrofo dritto (i titoli escono da wptexturize(),
     * le traduzioni sono salvate col testo scritto), poi pezzo per pezzo fra « – », « | », « · », come OLOlang fa coi
     * titoli delle pagine. Se non c'è una traduzione resta com'è.
     */
    private static function testo_nella_lingua( $s ) {
        $mappa = self::mappa_della_lingua();
        $norma = function ( $x ) {
            return trim( preg_replace( '/\s+/u', ' ', $x ) );
        };
        $piano = str_replace( [ '’', '‘' ], "'", html_entity_decode( $s, ENT_QUOTES, 'UTF-8' ) );
        foreach ( array_unique( [ $s, html_entity_decode( $s, ENT_QUOTES, 'UTF-8' ), $piano ] ) as $prova ) {
            if ( isset( $mappa[ $norma( $prova ) ] ) ) {
                return $mappa[ $norma( $prova ) ];
            }
        }
        $pezzi = preg_split( '/(\s[–|·]\s)/u', $piano, -1, PREG_SPLIT_DELIM_CAPTURE );
        if ( is_array( $pezzi ) && count( $pezzi ) > 1 ) {
            $cambiato = false;
            foreach ( $pezzi as $i => $p ) {
                if ( 0 === $i % 2 && isset( $mappa[ $norma( $p ) ] ) ) {
                    $pezzi[ $i ] = $mappa[ $norma( $p ) ];
                    $cambiato    = true;
                }
            }
            if ( $cambiato ) {
                return implode( '', $pezzi );
            }
        }
        return $s;
    }

    /**
     * Testo italiano → traduzione nella lingua di chi guarda: le stringhe globali di OLOlang e le traduzioni del
     * template della pagina aperta. Due letture per richiesta (per indice), poi tutto in memoria: il filtro
     * `olo_lang_translate` cerca per testo su tutta la tabella e costava ~36 ms a testo.
     */
    private static function mappa_della_lingua() {
        static $mappa = null;
        if ( null !== $mappa ) {
            return $mappa;
        }
        $mappa = [];
        $lang  = Olo_Lang_Language::detect_current_lang();
        $db    = Olo_Lang_Database::instance();
        $righe = (array) $db->get_template_translations( 0, $lang );
        $tpl   = is_singular() ? (int) get_post_meta( (int) get_queried_object_id(), '_olo_template_id', true ) : 0;
        if ( $tpl ) {
            $righe = array_merge( $righe, (array) $db->get_template_translations( $tpl, $lang ) );
        }
        foreach ( $righe as $r ) {
            $o = trim( preg_replace( '/\s+/u', ' ', (string) ( $r['original'] ?? '' ) ) );
            $t = trim( (string) ( $r['translation'] ?? '' ) );
            // Come la pagina di OLOlang: vale ogni traduzione che non sia una bozza.
            if ( '' === $o || '' === $t || $o === $t || 'bozza' === ( $r['status'] ?? '' ) || isset( $mappa[ $o ] ) ) {
                continue;
            }
            $mappa[ $o ] = $t;
        }
        return $mappa;
    }

    private function grafo_schema() {
        global $post;

        $kg    = $this->schema_knowledge_graph();
        $grafo = [ $kg, $this->schema_website( $kg['@id'] ) ];

        $singolo = is_singular() && $post;
        $url     = $singolo ? $this->url_schema( $post ) : (string) $this->get_canonical_url();

        $breadcrumb = is_front_page() ? null : $this->schema_breadcrumb( $url );
        if ( $breadcrumb ) {
            $grafo[] = $breadcrumb;
        }

        if ( $singolo ) {
            $tipo = (string) get_post_meta( $post->ID, '_olo_seo_schema_type', true );
            $faq  = $this->domande_faq( $post->ID );

            if ( 'none' === $tipo ) {
                // Nessuno schema per la pagina: restano solo le domande, se ci sono.
                if ( $faq ) {
                    $grafo[] = [ '@type' => 'FAQPage', '@id' => $url . '#faq', 'url' => $url, 'mainEntity' => $faq ];
                }
            } else {
                $pagina = $this->schema_webpage( $url, $breadcrumb ? $breadcrumb['@id'] : '', $kg['@id'] );
                if ( 'FAQPage' === $tipo || $faq ) {
                    $pagina['@type'] = 'FAQPage';
                }
                if ( $faq ) {
                    $pagina['mainEntity'] = $faq;
                }
                $grafo[] = $pagina;

                $principale = $this->schema_principale( $tipo, $url, $pagina['@id'], $kg['@id'] );
                if ( $principale ) {
                    $grafo[] = $principale;
                }
            }
        }

        $lb = $this->schema_local_business();
        if ( $lb ) {
            $grafo[] = $lb;
        }

        return $grafo;
    }

    /**
     * L'indirizzo che fa da base agli @id della pagina: il suo, anche quando il canonical rimanda altrove (il
     * grafo descrive QUESTA pagina). Le lingue di OLOlang hanno ognuna il suo (?lang=en).
     */
    private function url_schema( $post ) {
        if ( get_post_meta( $post->ID, '_olo_seo_canonical', true ) ) {
            return (string) get_permalink( $post );
        }
        $url = wp_get_canonical_url( $post );
        return $url ? (string) $url : (string) get_permalink( $post );
    }

    /** Il contenuto principale della pagina, secondo il tipo scelto (vuoto = automatico). */
    private function schema_principale( $tipo, $url, $pagina_id, $kg_id ) {
        global $post;

        if ( '' === $tipo ) {
            if ( function_exists( 'is_product' ) && is_product() ) {
                return $this->schema_product( $url, $pagina_id );
            }
            return is_singular( 'post' ) ? $this->schema_article( 'Article', $url, $pagina_id, $kg_id ) : null;
        }
        if ( in_array( $tipo, [ 'Article', 'BlogPosting', 'NewsArticle' ], true ) ) {
            return $this->schema_article( $tipo, $url, $pagina_id, $kg_id );
        }
        if ( in_array( $tipo, [ 'WebPage', 'FAQPage' ], true ) ) {
            return null; // il tipo sta già sul nodo della pagina
        }
        if ( 'SoftwareApplication' === $tipo ) {
            return $this->schema_software( $url, $pagina_id, $kg_id );
        }
        if ( 'Event' === $tipo ) {
            return $this->schema_evento( $url, $pagina_id, $kg_id );
        }
        if ( 'Product' === $tipo && function_exists( 'wc_get_product' ) && $post && wc_get_product( $post->ID ) ) {
            return $this->schema_product( $url, $pagina_id );
        }
        return $this->schema_custom_type( $tipo, $url, $pagina_id );
    }

    /* ─── Schema generators ─── */

    private function schema_website( $kg_id ) {
        $casa = home_url( '/' );
        return [
            '@type'           => 'WebSite',
            '@id'             => $casa . '#website',
            'url'             => $casa,
            'name'            => get_bloginfo( 'name' ),
            'description'     => get_bloginfo( 'description' ),
            'publisher'       => [ '@id' => $kg_id ],
            'inLanguage'      => get_bloginfo( 'language' ),
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => home_url( '/?s={search_term_string}' ),
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * Chi pubblica il sito: UNA entità (1.4.1265). Il tipo scelto in Impostazioni › SEO › «Chi pubblica il sito», col
     * nome, il logo, i profili, e per chi ha una sede l'indirizzo, gli orari, le coordinate e la fascia di prezzo. Prima
     * l'attività locale usciva come un secondo nodo staccato (#localbusiness), con lo stesso nome ma senza legami.
     */
    private function schema_knowledge_graph() {
        $titles = $this->opt( 'olo_seo_titles' );
        $type   = $titles['kg_type'] ?? 'Organization';
        $name   = $titles['kg_name'] ?? get_bloginfo( 'name' );
        $logo   = $titles['kg_logo'] ?? '';
        $info   = class_exists( 'Olobuild_Seo_Settings' ) ? Olobuild_Seo_Settings::tipo_chi( $type ) : null;
        $sede   = $info && $info['sede'];
        $tipi   = ( $info && $info['luogo'] ) ? [ 'Organization', $type ] : $type;

        $schema = [
            '@type' => $tipi,
            '@id'   => home_url( '/' ) . ( 'Person' === $type ? '#person' : '#organization' ),
            'name'  => $name ?: get_bloginfo( 'name' ),
            'url'   => home_url( '/' ),
        ];
        if ( ! empty( $titles['description'] ) ) {
            $schema['description'] = $titles['description'];
        }

        // Contatti e indirizzo valgono per ogni tipo; orari, coordinate e prezzi solo per chi ha una sede.
        $biz = $this->opt( 'olo_seo_local_business' );
        $biz = is_array( $biz ) ? $biz : [];
        $ind = $this->indirizzo_schema( $biz );
        if ( $ind ) {
            $schema['address'] = $ind;
        }
        if ( ! empty( $biz['phone'] ) ) {
            $schema['telephone'] = $biz['phone'];
        }
        if ( ! empty( $biz['email'] ) ) {
            $schema['email'] = $biz['email'];
        }
        if ( $sede ) {
            if ( ! empty( $biz['price_range'] ) ) {
                $schema['priceRange'] = $biz['price_range'];
            }
            if ( '' !== (string) ( $biz['geo_lat'] ?? '' ) && '' !== (string) ( $biz['geo_lng'] ?? '' ) ) {
                $schema['geo'] = [ '@type' => 'GeoCoordinates', 'latitude' => (float) $biz['geo_lat'], 'longitude' => (float) $biz['geo_lng'] ];
            }
            $orari = $this->orari_schema( $biz );
            if ( $orari ) {
                $schema['openingHoursSpecification'] = $orari;
            }
        }

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
                $schema['logo']  = $logo;
                // Un'attività con una sede deve avere anche un'immagine (Google la chiede per LocalBusiness)
                if ( $sede ) {
                    $schema['image'] = ! empty( $biz['image'] ) ? $biz['image'] : $logo;
                }
            }
        }

        $same_as = $this->profili_social();
        if ( $same_as ) {
            $schema['sameAs'] = $same_as;
        }

        return $schema;
    }

    /**
     * Il titolo di un contenuto come testo semplice, per i dati strutturati: get_the_title() lo dà già trasformato per
     * l'HTML (l'apostrofo di «Los geht's» o «L'inspector» diventa &#8217;), e nel JSON-LD l'entità restava scritta così.
     */
    private static function titolo_semplice( $id ) {
        return trim( html_entity_decode( wp_strip_all_tags( get_the_title( $id ) ), ENT_QUOTES, 'UTF-8' ) );
    }

    private function schema_breadcrumb( $url ) {
        global $post;
        $items = [];
        $pos   = 1;

        $items[] = [
            '@type'    => 'ListItem',
            'position' => $pos++,
            'name'     => __( 'Home', 'olobuild' ),
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

            // Tutti i livelli in mezzo, come la tile Briciole: per le Pagine e per ogni tipo con argomenti e
            // sottoargomenti (il manuale: Home › OLObuild › Si parte › Le colonne). Prima solo per le Pagine, e un
            // capitolo del manuale usciva come Home › capitolo (21 nov 2026). Una traduzione sta sotto le traduzioni
            // dei suoi genitori, quindi nomi e indirizzi escono nella sua lingua.
            if ( is_page() || is_post_type_hierarchical( $post->post_type ) ) {
                $ancestors = array_reverse( get_post_ancestors( $post ) );
                foreach ( $ancestors as $anc_id ) {
                    $items[] = [
                        '@type'    => 'ListItem',
                        'position' => $pos++,
                        'name'     => self::titolo_semplice( $anc_id ),
                        'item'     => get_permalink( $anc_id ),
                    ];
                }
            }

            $items[] = [
                '@type'    => 'ListItem',
                'position' => $pos,
                'name'     => self::titolo_semplice( $post->ID ),
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
                'name'     => __( 'Risultati della ricerca', 'olobuild' ),
            ];
        }

        if ( count( $items ) < 2 ) {
            return null;
        }

        return [
            '@type'           => 'BreadcrumbList',
            '@id'             => ( $url ?: home_url( '/' ) ) . '#breadcrumb',
            'itemListElement' => $items,
        ];
    }

    private function schema_webpage( $url, $breadcrumb_id, $kg_id ) {
        global $post;

        $schema = [
            '@type'         => 'WebPage',
            '@id'           => $url . '#webpage',
            'url'           => $url,
            'name'          => get_the_title( $post ),
            'description'   => $this->get_seo_description(),
            'isPartOf'      => [ '@id' => home_url( '/' ) . '#website' ],
            'inLanguage'    => get_bloginfo( 'language' ),
            'datePublished' => get_the_date( 'c', $post ),
            'dateModified'  => get_the_modified_date( 'c', $post ),
        ];
        if ( $breadcrumb_id ) {
            $schema['breadcrumb'] = [ '@id' => $breadcrumb_id ];
        }
        if ( is_front_page() ) {
            $schema['about'] = [ '@id' => $kg_id ];
        }
        $image = get_the_post_thumbnail_url( $post, 'large' );
        if ( $image ) {
            $schema['primaryImageOfPage'] = [ '@type' => 'ImageObject', 'url' => $image ];
        }
        return $schema;
    }

    private function schema_article( $tipo, $url, $pagina_id, $kg_id ) {
        global $post;
        if ( ! $post ) return null;

        $schema = [
            '@type'            => $tipo,
            '@id'              => $url . '#article',
            'isPartOf'         => [ '@id' => $pagina_id ],
            'mainEntityOfPage' => [ '@id' => $pagina_id ],
            'headline'         => get_the_title( $post ),
            'url'              => $url,
            'datePublished'    => get_the_date( 'c', $post ),
            'dateModified'     => get_the_modified_date( 'c', $post ),
            'author'           => [
                '@type' => 'Person',
                'name'  => get_the_author_meta( 'display_name', $post->post_author ),
            ],
            'publisher'        => [ '@id' => $kg_id ],
            'inLanguage'       => get_bloginfo( 'language' ),
        ];

        $image = get_the_post_thumbnail_url( $post, 'large' );
        if ( $image ) {
            $schema['image'] = $image;
        }

        $excerpt = get_the_excerpt( $post );
        if ( $excerpt ) {
            $schema['description'] = wp_strip_all_tags( $excerpt );
        }

        return $schema;
    }

    /**
     * Un tipo scelto a mano senza campi suoi (HowTo, Recipe, LocalBusiness, Product senza WooCommerce): nome,
     * descrizione e immagine. Il resto va nel JSON-LD personalizzato.
     */
    private function schema_custom_type( $type, $url, $pagina_id ) {
        global $post;
        if ( ! $post ) return null;

        $schema = [
            '@type'            => $type,
            '@id'              => $url . '#main',
            'name'             => get_the_title( $post ),
            'url'              => $url,
            'description'      => $this->get_seo_description(),
            'mainEntityOfPage' => [ '@id' => $pagina_id ],
        ];
        // Le date sono delle opere (CreativeWork): su un'attività o un prodotto sarebbero proprietà sconosciute.
        if ( in_array( $type, [ 'HowTo', 'Recipe' ], true ) ) {
            $schema['datePublished'] = get_the_date( 'c', $post );
            $schema['dateModified']  = get_the_modified_date( 'c', $post );
        }

        $image = get_the_post_thumbnail_url( $post, 'large' );
        if ( $image ) {
            $schema['image'] = $image;
        }

        return $schema;
    }

    /** Il software della pagina, coi campi della scheda Schema (novembre 2026). */
    private function schema_software( $url, $pagina_id, $kg_id ) {
        global $post;
        $d = $this->dati_schema_pagina( $post->ID );

        $schema = [
            '@type'            => 'SoftwareApplication',
            '@id'              => $url . '#software',
            'name'             => get_the_title( $post ),
            'url'              => $url,
            'description'      => $this->get_seo_description(),
            'mainEntityOfPage' => [ '@id' => $pagina_id ],
            'publisher'        => [ '@id' => $kg_id ],
        ];
        $image = get_the_post_thumbnail_url( $post, 'large' );
        if ( $image ) {
            $schema['image'] = $image;
        }
        $mappa = [ 'app_category' => 'applicationCategory', 'app_os' => 'operatingSystem', 'app_version' => 'softwareVersion' ];
        foreach ( $mappa as $chiave => $proprieta ) {
            if ( isset( $d[ $chiave ] ) && '' !== $d[ $chiave ] ) {
                $schema[ $proprieta ] = $d[ $chiave ];
            }
        }
        if ( isset( $d['app_price'] ) && '' !== $d['app_price'] ) {
            $schema['offers'] = [
                '@type'         => 'Offer',
                'price'         => $d['app_price'],
                'priceCurrency' => $d['app_currency'] ?? 'EUR',
                'url'           => $url,
            ];
        }
        return $schema;
    }

    /** L'evento della pagina, coi campi della scheda Schema (novembre 2026). */
    private function schema_evento( $url, $pagina_id, $kg_id ) {
        global $post;
        $d = $this->dati_schema_pagina( $post->ID );

        $schema = [
            '@type'            => 'Event',
            '@id'              => $url . '#event',
            'name'             => get_the_title( $post ),
            'url'              => $url,
            'description'      => $this->get_seo_description(),
            'mainEntityOfPage' => [ '@id' => $pagina_id ],
        ];
        $image = get_the_post_thumbnail_url( $post, 'large' );
        if ( $image ) {
            $schema['image'] = $image;
        }

        $inizio = self::data_iso( $d['ev_start'] ?? '' );
        $fine   = self::data_iso( $d['ev_end'] ?? '' );
        if ( $inizio ) {
            $schema['startDate'] = $inizio;
        }
        if ( $fine ) {
            $schema['endDate'] = $fine;
        }

        $modo  = $d['ev_mode'] ?? 'offline';
        $modi  = [ 'offline' => 'OfflineEventAttendanceMode', 'online' => 'OnlineEventAttendanceMode', 'mixed' => 'MixedEventAttendanceMode' ];
        $schema['eventAttendanceMode'] = 'https://schema.org/' . ( $modi[ $modo ] ?? $modi['offline'] );

        $stati = [
            'scheduled'   => 'EventScheduled',
            'cancelled'   => 'EventCancelled',
            'postponed'   => 'EventPostponed',
            'rescheduled' => 'EventRescheduled',
            'movedonline' => 'EventMovedOnline',
        ];
        $schema['eventStatus'] = 'https://schema.org/' . ( $stati[ $d['ev_status'] ?? 'scheduled' ] ?? $stati['scheduled'] );

        $luoghi = [];
        if ( 'online' !== $modo && ( ! empty( $d['ev_place'] ) || ! empty( $d['ev_address'] ) ) ) {
            $luogo = [ '@type' => 'Place', 'name' => ! empty( $d['ev_place'] ) ? $d['ev_place'] : $d['ev_address'] ];
            if ( ! empty( $d['ev_address'] ) ) {
                $luogo['address'] = $d['ev_address'];
            }
            $luoghi[] = $luogo;
        }
        if ( 'offline' !== $modo && ! empty( $d['ev_online_url'] ) ) {
            $luoghi[] = [ '@type' => 'VirtualLocation', 'url' => $d['ev_online_url'] ];
        }
        if ( $luoghi ) {
            $schema['location'] = 1 === count( $luoghi ) ? $luoghi[0] : $luoghi;
        }

        $schema['organizer'] = ! empty( $d['ev_organizer'] )
            ? [ '@type' => 'Organization', 'name' => $d['ev_organizer'] ]
            : [ '@id' => $kg_id ];

        $prezzo = isset( $d['ev_price'] ) && '' !== $d['ev_price'];
        if ( $prezzo || ! empty( $d['ev_tickets_url'] ) ) {
            $offerta = [
                '@type'        => 'Offer',
                'url'          => ! empty( $d['ev_tickets_url'] ) ? $d['ev_tickets_url'] : $url,
                'availability' => 'https://schema.org/InStock',
            ];
            if ( $prezzo ) {
                $offerta['price']         = $d['ev_price'];
                $offerta['priceCurrency'] = $d['ev_currency'] ?? 'EUR';
            }
            $schema['offers'] = $offerta;
        }

        return $schema;
    }

    /** «2026-12-05T21:00» del campo, nel fuso del sito → ISO 8601 con lo scarto (2026-12-05T21:00:00+01:00). */
    private static function data_iso( $valore ) {
        if ( ! is_string( $valore ) || '' === $valore ) {
            return '';
        }
        try {
            $data = new DateTime( $valore, wp_timezone() );
            return $data->format( 'c' );
        } catch ( Exception $e ) {
            return '';
        }
    }

    private function dati_schema_pagina( $post_id ) {
        $d = get_post_meta( $post_id, '_olo_seo_schema_data', true );
        return is_array( $d ) ? $d : [];
    }

    private function schema_product( $url, $pagina_id ) {
        global $post;
        if ( ! $post || ! function_exists( 'wc_get_product' ) ) {
            return null;
        }

        $product = wc_get_product( $post->ID );
        if ( ! $product ) {
            return null;
        }

        $schema = [
            '@type'            => 'Product',
            '@id'              => $url . '#product',
            'name'             => $product->get_name(),
            'url'              => $url,
            'description'      => wp_strip_all_tags( $product->get_short_description() ),
            'mainEntityOfPage' => [ '@id' => $pagina_id ],
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
            'url'           => $url,
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

    /** L'indirizzo della sede come PostalAddress, o null se mancano via e città. */
    private function indirizzo_schema( $biz ) {
        $a = isset( $biz['address'] ) && is_array( $biz['address'] ) ? $biz['address'] : [];
        if ( empty( $a['street'] ) && empty( $a['city'] ) ) {
            return null;
        }
        $addr = [ '@type' => 'PostalAddress' ];
        foreach ( [ 'street' => 'streetAddress', 'city' => 'addressLocality', 'state' => 'addressRegion', 'zip' => 'postalCode' ] as $k => $p ) {
            if ( ! empty( $a[ $k ] ) ) {
                $addr[ $p ] = $a[ $k ];
            }
        }
        $addr['addressCountry'] = ! empty( $a['country'] ) ? $a['country'] : 'IT';
        return $addr;
    }

    /** Gli orari della sede, un OpeningHoursSpecification per giorno con apertura e chiusura. */
    private function orari_schema( $biz ) {
        $giorni = [ 'monday' => 'Monday', 'tuesday' => 'Tuesday', 'wednesday' => 'Wednesday', 'thursday' => 'Thursday', 'friday' => 'Friday', 'saturday' => 'Saturday', 'sunday' => 'Sunday' ];
        $out    = [];
        foreach ( (array) ( $biz['hours'] ?? [] ) as $g => $o ) {
            if ( isset( $giorni[ $g ] ) && ! empty( $o['open'] ) && ! empty( $o['close'] ) ) {
                $out[] = [ '@type' => 'OpeningHoursSpecification', 'dayOfWeek' => $giorni[ $g ], 'opens' => $o['open'], 'closes' => $o['close'] ];
            }
        }
        return $out;
    }

    /**
     * Il vecchio nodo #localbusiness, solo per i siti che l'avevano compilato con un nome suo quando chi pubblica il
     * sito non è un'attività con una sede (per esempio un'azienda con un negozio). Negli altri casi l'indirizzo e
     * gli orari stanno già nell'entità di chi pubblica (schema_knowledge_graph).
     */
    private function schema_local_business() {
        $biz = $this->opt( 'olo_seo_local_business' );
        if ( empty( $biz ) || empty( $biz['name'] ) ) {
            return null;
        }
        $titles = $this->opt( 'olo_seo_titles' );
        $info   = class_exists( 'Olobuild_Seo_Settings' ) ? Olobuild_Seo_Settings::tipo_chi( $titles['kg_type'] ?? 'Organization' ) : null;
        if ( ( $info && $info['sede'] ) || $biz['name'] === ( $titles['kg_name'] ?? get_bloginfo( 'name' ) ) ) {
            return null;
        }

        $schema = [
            '@type'       => ! empty( $biz['type'] ) ? $biz['type'] : 'LocalBusiness',
            '@id'         => home_url( '/' ) . '#localbusiness',
            'name'        => $biz['name'],
            'url'         => home_url( '/' ),
        ];

        if ( ! empty( $biz['description'] ) ) {
            $schema['description'] = $biz['description'];
        }

        $addr = $this->indirizzo_schema( $biz );
        if ( $addr ) {
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

    /** I profili social della scheda Social, per il sameAs di chi pubblica il sito (qualunque tipo abbia). */
    private function profili_social() {
        $social = $this->opt( 'olo_seo_social' );
        $urls   = [];

        $keys = [ 'facebook_url', 'instagram_url', 'linkedin_url', 'youtube_url', 'pinterest_url', 'tiktok_url', 'threads_url' ];
        foreach ( $keys as $key ) {
            if ( ! empty( $social[ $key ] ) ) {
                $urls[] = $social[ $key ];
            }
        }
        if ( ! empty( $social['twitter_user'] ) ) {
            $tw = ltrim( $social['twitter_user'], '@' );
            $urls[] = 'https://x.com/' . $tw;
        }
        // Gli altri profili (Wikipedia, Wikidata, un albo, una guida…): uno per riga. Sono i legami che fanno
        // riconoscere l'entità ai motori e alle IA.
        foreach ( preg_split( '/[\r\n]+/', (string) ( $social['altri_profili'] ?? '' ) ) as $r ) {
            $r = trim( $r );
            if ( '' !== $r ) {
                $urls[] = $r;
            }
        }

        return array_values( array_unique( $urls ) );
    }

    /** Le domande della scheda FAQ della pagina, come Question con la loro Answer. */
    private function domande_faq( $post_id ) {
        $faq_items = get_post_meta( $post_id, '_olo_seo_faq', true );
        if ( ! is_array( $faq_items ) || empty( $faq_items ) ) {
            return [];
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

        return $main_entity;
    }

    /* ═══════════════════════════════════════════════════
     * Campi dei dati strutturati per tipo (novembre 2026)
     * ═══════════════════════════════════════════════════
     *
     * Fonte UNICA dei campi della scheda Schema: il builder li riceve con la SEO della pagina (page-seo,
     * `_schema_campi`) e il riquadro dell'editor di WordPress li disegna da qui. Si salvano tutti in un solo meta,
     * `_olo_seo_schema_data`; le chiavi di prima non cambiano.
     */

    public static function campi_schema() {
        $valute = [ 'EUR' => 'EUR €', 'USD' => 'USD $', 'GBP' => 'GBP £', 'CHF' => 'CHF' ];
        $in_presenza = [ 'ev_mode' => [ '', 'offline', 'mixed' ] ];
        return [
            'SoftwareApplication' => [
                [ 'key' => 'app_category', 'label' => __( 'Categoria', 'olobuild' ), 'type' => 'select', 'default' => '', 'options' => [
                    ''                       => __( '— Scegli —', 'olobuild' ),
                    'BusinessApplication'    => __( 'Lavoro e gestione', 'olobuild' ),
                    'DesignApplication'      => __( 'Grafica e siti', 'olobuild' ),
                    'DeveloperApplication'   => __( 'Sviluppo', 'olobuild' ),
                    'EducationalApplication' => __( 'Istruzione', 'olobuild' ),
                    'MultimediaApplication'  => __( 'Foto, audio e video', 'olobuild' ),
                    'UtilitiesApplication'   => __( 'Utilità', 'olobuild' ),
                    'GameApplication'        => __( 'Giochi', 'olobuild' ),
                    'LifestyleApplication'   => __( 'Tempo libero', 'olobuild' ),
                ] ],
                [ 'key' => 'app_os', 'label' => __( 'Funziona su', 'olobuild' ), 'type' => 'text', 'placeholder' => 'WordPress' ],
                [ 'key' => 'app_version', 'label' => __( 'Versione', 'olobuild' ), 'type' => 'text', 'placeholder' => '1.0' ],
                [ 'key' => 'app_price', 'label' => __( 'Prezzo', 'olobuild' ), 'type' => 'number', 'placeholder' => '0', 'aiuto' => __( 'Scrivi 0 se è gratis.', 'olobuild' ) ],
                [ 'key' => 'app_currency', 'label' => __( 'Valuta', 'olobuild' ), 'type' => 'select', 'default' => 'EUR', 'options' => $valute ],
            ],
            'Event' => [
                [ 'key' => 'ev_start', 'label' => __( 'Inizio', 'olobuild' ), 'type' => 'datetime' ],
                [ 'key' => 'ev_end', 'label' => __( 'Fine', 'olobuild' ), 'type' => 'datetime', 'dopo' => 'ev_start' ],
                [ 'key' => 'ev_mode', 'label' => __( 'Si segue', 'olobuild' ), 'type' => 'select', 'default' => 'offline', 'options' => [
                    'offline' => __( 'In presenza', 'olobuild' ),
                    'online'  => __( 'Online', 'olobuild' ),
                    'mixed'   => __( 'In presenza e online', 'olobuild' ),
                ] ],
                [ 'key' => 'ev_place', 'label' => __( 'Luogo', 'olobuild' ), 'type' => 'text', 'placeholder' => __( 'Teatro Comunale', 'olobuild' ), 'mostra_se' => $in_presenza ],
                [ 'key' => 'ev_address', 'label' => __( 'Indirizzo', 'olobuild' ), 'type' => 'text', 'placeholder' => __( 'Via Roma 1, Trento', 'olobuild' ), 'mostra_se' => $in_presenza ],
                [ 'key' => 'ev_online_url', 'label' => __( 'Indirizzo online', 'olobuild' ), 'type' => 'url', 'placeholder' => 'https://', 'mostra_se' => [ 'ev_mode' => [ 'online', 'mixed' ] ] ],
                [ 'key' => 'ev_status', 'label' => __( 'Stato', 'olobuild' ), 'type' => 'select', 'default' => 'scheduled', 'options' => [
                    'scheduled'   => __( 'In programma', 'olobuild' ),
                    'rescheduled' => __( 'Spostato a un’altra data', 'olobuild' ),
                    'postponed'   => __( 'Rinviato, data da definire', 'olobuild' ),
                    'movedonline' => __( 'Spostato online', 'olobuild' ),
                    'cancelled'   => __( 'Annullato', 'olobuild' ),
                ] ],
                [ 'key' => 'ev_organizer', 'label' => __( 'Organizzatore', 'olobuild' ), 'type' => 'text', 'placeholder' => get_bloginfo( 'name' ) ],
                [ 'key' => 'ev_price', 'label' => __( 'Prezzo del biglietto', 'olobuild' ), 'type' => 'number', 'placeholder' => '0', 'aiuto' => __( 'Scrivi 0 se l’ingresso è libero.', 'olobuild' ) ],
                [ 'key' => 'ev_currency', 'label' => __( 'Valuta', 'olobuild' ), 'type' => 'select', 'default' => 'EUR', 'options' => $valute ],
                [ 'key' => 'ev_tickets_url', 'label' => __( 'Dove si prenota', 'olobuild' ), 'type' => 'url', 'placeholder' => 'https://' ],
            ],
        ];
    }

    /**
     * Pulisce i dati della scheda Schema: solo le chiavi note, ognuna col suo formato. La fine di un evento prima
     * dell'inizio si scarta (e si dice).
     *
     * @return array { dati: array, errori: string[] }
     */
    public static function sanitizza_dati_schema( $raw ) {
        $dati   = [];
        $errori = [];
        if ( ! is_array( $raw ) ) {
            return [ 'dati' => $dati, 'errori' => $errori ];
        }
        foreach ( self::campi_schema() as $campi ) {
            foreach ( $campi as $c ) {
                $k = $c['key'];
                if ( ! isset( $raw[ $k ] ) || is_array( $raw[ $k ] ) ) {
                    continue;
                }
                $v = trim( (string) $raw[ $k ] );
                if ( '' === $v ) {
                    continue;
                }
                switch ( $c['type'] ) {
                    case 'select':
                        if ( array_key_exists( $v, $c['options'] ) ) {
                            $dati[ $k ] = $v;
                        }
                        break;
                    case 'url':
                        $u = esc_url_raw( $v );
                        if ( $u ) {
                            $dati[ $k ] = $u;
                        }
                        break;
                    case 'number':
                        $n = str_replace( ',', '.', $v );
                        if ( is_numeric( $n ) && (float) $n >= 0 ) {
                            $dati[ $k ] = (string) ( 0 + $n );
                        } else {
                            /* translators: %s: nome del campo */
                            $errori[] = sprintf( __( '%s: serve un numero', 'olobuild' ), $c['label'] );
                        }
                        break;
                    case 'datetime':
                        if ( preg_match( '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $v ) ) {
                            $dati[ $k ] = $v;
                        }
                        break;
                    default:
                        $dati[ $k ] = sanitize_text_field( $v );
                }
            }
        }
        if ( ! empty( $dati['ev_start'] ) && ! empty( $dati['ev_end'] ) && $dati['ev_end'] < $dati['ev_start'] ) {
            unset( $dati['ev_end'] );
            $errori[] = __( 'La fine dell’evento non può venire prima dell’inizio', 'olobuild' );
        }
        return [ 'dati' => $dati, 'errori' => $errori ];
    }

    /**
     * I controlli dei dati strutturati per l'analisi della pagina (Olobuild_Seo_Analisi).
     *
     * @return array[] { id, esito, testo, aiuto }
     */
    public static function controlla_dati_schema( $tipo, $dati ) {
        $dati = is_array( $dati ) ? $dati : [];
        $out  = [];
        if ( 'Event' === $tipo ) {
            $modo = $dati['ev_mode'] ?? 'offline';
            if ( empty( $dati['ev_start'] ) ) {
                $out[] = [ 'id' => 'schema-evento-data', 'esito' => 'errore', 'testo' => __( 'Evento senza data d’inizio', 'olobuild' ),
                    'aiuto' => __( 'Senza la data Google non mostra la pagina fra gli eventi. Si scrive nella scheda Schema.', 'olobuild' ) ];
            }
            if ( 'online' !== $modo && empty( $dati['ev_place'] ) && empty( $dati['ev_address'] ) ) {
                $out[] = [ 'id' => 'schema-evento-luogo', 'esito' => 'attenzione', 'testo' => __( 'Evento senza luogo', 'olobuild' ),
                    'aiuto' => __( 'Scrivi il nome del luogo e l’indirizzo nella scheda Schema: senza, Google non sa dove si tiene.', 'olobuild' ) ];
            }
            if ( 'offline' !== $modo && empty( $dati['ev_online_url'] ) ) {
                $out[] = [ 'id' => 'schema-evento-online', 'esito' => 'attenzione', 'testo' => __( 'Evento online senza indirizzo', 'olobuild' ) ];
            }
            if ( ! $out ) {
                $out[] = [ 'id' => 'schema-evento', 'esito' => 'ok', 'testo' => __( 'Dati dell’evento completi', 'olobuild' ) ];
            }
        } elseif ( 'SoftwareApplication' === $tipo ) {
            if ( ! isset( $dati['app_price'] ) || '' === $dati['app_price'] ) {
                $out[] = [ 'id' => 'schema-software-prezzo', 'esito' => 'attenzione', 'testo' => __( 'Software senza prezzo', 'olobuild' ),
                    'aiuto' => __( 'Scrivi il prezzo nella scheda Schema, 0 se è gratis: è il dato che motori e IA cercano per primo.', 'olobuild' ) ];
            }
            if ( empty( $dati['app_category'] ) ) {
                $out[] = [ 'id' => 'schema-software-categoria', 'esito' => 'attenzione', 'testo' => __( 'Software senza categoria', 'olobuild' ) ];
            }
            if ( ! $out ) {
                $out[] = [ 'id' => 'schema-software', 'esito' => 'ok', 'testo' => __( 'Dati del software completi', 'olobuild' ) ];
            }
        } elseif ( in_array( $tipo, [ 'HowTo', 'Recipe', 'Product', 'LocalBusiness' ], true ) ) {
            $out[] = [ 'id' => 'schema-generico', 'esito' => 'info',
                /* translators: %s: tipo di dati strutturati (Recipe, HowTo…) */
                'testo' => sprintf( __( 'Dati strutturati %s con solo nome e descrizione', 'olobuild' ), $tipo ),
                'aiuto' => __( 'Per questo tipo Olobuild scrive nome, descrizione e immagine. Gli altri dati (ingredienti, passi, prezzo) vanno nel JSON-LD personalizzato della scheda Schema.', 'olobuild' ) ];
        }
        return $out;
    }

    /* ═══════════════════════════════════════════════════
     * Helpers
     * ═══════════════════════════════════════════════════ */

    /**
     * Il titolo per Open Graph e X: lo stesso di <title>, con modelli, schema e
     * separatore. Prima lo ricalcolava a parte, con un altro separatore.
     */
    private function get_seo_title() {
        return html_entity_decode( wp_get_document_title(), ENT_QUOTES, get_bloginfo( 'charset' ) );
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

        // «Meta description default» della scheda SEO globale, prima dello slogan.
        if ( ! empty( $titles['description'] ) ) {
            return $titles['description'];
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

            // L'immagine disegnata (1.4.1218): titolo, nome del sito e foto in evidenza nei colori del sito.
            if ( class_exists( 'Olobuild_Og_Disegnata' ) && Olobuild_Og_Disegnata::attiva() ) {
                $disegnata = Olobuild_Og_Disegnata::url_per( $post );
                if ( $disegnata ) {
                    return $disegnata;
                }
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
        $adv = $this->opt( 'olobuild_seo_advanced' );

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

    /** I robot che leggono i siti per ADDESTRARE i modelli delle IA (non portano visite). */
    const BOT_ADDESTRAMENTO = [ 'GPTBot', 'ClaudeBot', 'Google-Extended', 'Applebot-Extended', 'CCBot', 'meta-externalagent', 'Bytespider' ];

    /** I robot con cui le IA cercano e aprono le pagine per RISPONDERE a chi chiede (portano visite e citazioni). */
    const BOT_RISPOSTE = [ 'OAI-SearchBot', 'ChatGPT-User', 'Claude-SearchBot', 'Claude-User', 'PerplexityBot', 'Perplexity-User', 'MistralAI-User' ];

    /** I robot delle IA che la scelta di Impostazioni › SEO tiene fuori (vuoto = tutti dentro). */
    public static function bot_ia_esclusi( $scelta ) {
        if ( 'nessuno' === $scelta ) {
            return array_merge( self::BOT_ADDESTRAMENTO, self::BOT_RISPOSTE );
        }
        return 'senza-addestramento' === $scelta ? self::BOT_ADDESTRAMENTO : [];
    }

    public function custom_robots_txt( $output ) {
        $adv = $this->opt( 'olobuild_seo_advanced' );
        $custom = $adv['robots_txt'] ?? '';

        if ( $custom ) {
            $output = $custom . "\n\n" . $output;
        }

        // I robot delle IA (1.4.1265): un gruppo per robot, che non può leggere niente.
        $fuori = self::bot_ia_esclusi( $adv['ia_bot'] ?? 'tutti' );
        if ( $fuori ) {
            $output = rtrim( $output ) . "\n\n# " . __( 'Intelligenze artificiali tenute fuori (Impostazioni › SEO)', 'olobuild' ) . "\n";
            foreach ( $fuori as $bot ) {
                $output .= 'User-agent: ' . $bot . "\nDisallow: /\n\n";
            }
        }

        return $output;
    }

    /* ═══════════════════════════════════════════════════
     * llms.txt (1.4.1265)
     * ═══════════════════════════════════════════════════ */

    /** Acceso in Impostazioni › SEO › «Le intelligenze artificiali». */
    public static function llms_attivo() {
        $adv = get_option( 'olobuild_seo_advanced', [] );
        return is_array( $adv ) && ! empty( $adv['llms'] );
    }

    /** L'indirizzo del file, nella cartella del sito (anche se il sito sta in una sottocartella). */
    public static function url_llms() {
        return home_url( '/llms.txt' );
    }

    public function servi_llms_txt() {
        if ( ! self::llms_attivo() || $this->seo_plugin_active() ) {
            return;
        }
        $path = (string) wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) ), PHP_URL_PATH );
        if ( $path !== (string) wp_parse_url( self::url_llms(), PHP_URL_PATH ) ) {
            return;
        }
        status_header( 200 );
        header( 'Content-Type: text/plain; charset=utf-8' );
        header( 'X-Robots-Tag: noindex' );
        echo self::llms_testo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- testo semplice (Markdown), Content-Type text/plain
        exit;
    }

    /**
     * Il riassunto del sito in Markdown (llmstxt.org): chi pubblica, cosa fa, come contattarlo, e le pagine che i
     * motori possono vedere, con la loro descrizione. Le pagine escluse dai motori non ci sono.
     */
    public static function llms_testo() {
        $titles = Olobuild_Seo_Settings::get( 'olo_seo_titles' );
        $biz    = Olobuild_Seo_Settings::get( 'olo_seo_local_business' );
        $adv    = Olobuild_Seo_Settings::get( 'olobuild_seo_advanced' );
        $nome   = ! empty( $titles['kg_name'] ) ? $titles['kg_name'] : get_bloginfo( 'name' );
        $desc   = ! empty( $titles['description'] ) ? $titles['description'] : get_bloginfo( 'description' );
        $pulisci = function ( $s ) {
            return trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( html_entity_decode( (string) $s, ENT_QUOTES, 'UTF-8' ) ) ) );
        };

        $out = '# ' . $pulisci( $nome ) . "\n\n";
        if ( $desc ) {
            $out .= '> ' . $pulisci( $desc ) . "\n\n";
        }
        $info = Olobuild_Seo_Settings::tipo_chi( $titles['kg_type'] ?? 'Organization' );
        $righe = [];
        if ( $info ) {
            $righe[] = '- ' . __( 'Chi è', 'olobuild' ) . ': ' . $info['label'];
        }
        $a = isset( $biz['address'] ) && is_array( $biz['address'] ) ? $biz['address'] : [];
        $ind = implode( ', ', array_filter( [ $a['street'] ?? '', trim( ( $a['zip'] ?? '' ) . ' ' . ( $a['city'] ?? '' ) ), $a['state'] ?? '' ] ) );
        if ( $ind ) {
            $righe[] = '- ' . __( 'Indirizzo', 'olobuild' ) . ': ' . $ind;
        }
        if ( ! empty( $biz['phone'] ) ) {
            $righe[] = '- ' . __( 'Telefono', 'olobuild' ) . ': ' . $biz['phone'];
        }
        if ( ! empty( $biz['email'] ) ) {
            $righe[] = '- Email: ' . $biz['email'];
        }
        $righe[] = '- ' . __( 'Sito', 'olobuild' ) . ': ' . home_url( '/' );
        $out .= implode( "\n", $righe ) . "\n";

        foreach ( Olobuild_Seo_Centro::tipi_contenuto() as $pt => $etichetta ) {
            if ( ! empty( $adv['noindex_pt'][ $pt ] ) ) {
                continue;
            }
            $q = get_posts( [
                'post_type'      => $pt,
                'post_status'    => 'publish',
                'posts_per_page' => 100,
                'orderby'        => 'page' === $pt ? 'menu_order title' : 'date',
                'order'          => 'page' === $pt ? 'ASC' : 'DESC',
                'no_found_rows'  => true,
                'meta_query'     => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- file letto di rado, al massimo 100 righe per tipo
                    'relation' => 'OR',
                    [ 'key' => '_olo_seo_noindex', 'compare' => 'NOT EXISTS' ],
                    [ 'key' => '_olo_seo_noindex', 'value' => '1', 'compare' => '!=' ],
                ],
            ] );
            if ( ! $q ) {
                continue;
            }
            $out .= "\n## " . $pulisci( $etichetta ) . "\n\n";
            foreach ( $q as $p ) {
                $d = self::descrizione_per( $p );
                // La home senza una descrizione sua esce su Google con quella del sito (get_seo_description): qui uguale
                if ( '' === $d && (int) get_option( 'page_on_front' ) === (int) $p->ID ) {
                    $d = (string) $desc;
                }
                $out .= '- [' . $pulisci( get_the_title( $p ) ) . '](' . get_permalink( $p ) . ')' . ( $d ? ': ' . $pulisci( $d ) : '' ) . "\n";
            }
        }
        return $out;
    }

    /* ═══════════════════════════════════════════════════
     * Titolo e descrizione di un contenuto qualsiasi (1.4.1265)
     * ═══════════════════════════════════════════════════
     *
     * Per l'elenco delle pagine di Impostazioni › SEO e per llms.txt: gli stessi passi di filter_title_parts() e
     * get_seo_description(), senza bisogno che la pagina sia quella aperta.
     */

    /** [ titolo che esce su Google, true se scritto da te ]. */
    public static function titolo_per( $post ) {
        $suo = trim( (string) get_post_meta( $post->ID, '_olo_seo_title', true ) );
        if ( '' !== $suo ) {
            return [ $suo, true ];
        }
        $titles = Olobuild_Seo_Settings::get( 'olo_seo_titles' );
        $sep    = ( isset( $titles['separator'] ) && '' !== trim( (string) $titles['separator'] ) ) ? $titles['separator'] : '–';
        $sito   = html_entity_decode( get_bloginfo( 'name' ), ENT_QUOTES, 'UTF-8' );
        $titolo = html_entity_decode( get_the_title( $post ), ENT_QUOTES, 'UTF-8' );
        if ( (int) get_option( 'page_on_front' ) === (int) $post->ID ) {
            $motto = html_entity_decode( get_bloginfo( 'description' ), ENT_QUOTES, 'UTF-8' );
            return [ $motto ? $sito . ' ' . $sep . ' ' . $motto : $sito, false ];
        }
        $pattern = trim( (string) ( $titles['pattern'] ?? '' ) );
        if ( false !== strpos( $pattern, '{page}' ) ) {
            return [ trim( strtr( $pattern, [ '{page}' => $titolo, '{sep}' => $sep, '{site}' => $sito ] ) ), false ];
        }
        return [ $titolo . ' ' . $sep . ' ' . $sito, false ];
    }

    /** La descrizione che esce su Google: la tua, o quella presa dal riassunto o dal testo, o quella del sito. */
    public static function descrizione_per( $post, &$sua = null ) {
        $d = trim( (string) get_post_meta( $post->ID, '_olo_seo_description', true ) );
        $sua = '' !== $d;
        if ( $sua ) {
            return $d;
        }
        return self::descrizione_automatica( $post );
    }

    /** Dal riassunto, se c'è; se no dalle prime parole del testo (senza far girare il contenuto della pagina). */
    public static function descrizione_automatica( $post ) {
        if ( '' !== trim( (string) $post->post_excerpt ) ) {
            $c = wp_trim_words( wp_strip_all_tags( get_the_excerpt( $post ) ), 30 );
        } else {
            $c = (string) $post->post_content;
            if ( function_exists( 'excerpt_remove_blocks' ) ) {
                $c = excerpt_remove_blocks( $c );
            }
            $c = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( strip_shortcodes( $c ) ) ) );
            $c = '' === $c ? '' : wp_trim_words( $c, 30 );
        }
        // Testo semplice: «&hellip;» di wp_trim_words diventa «…»
        return html_entity_decode( $c, ENT_QUOTES, 'UTF-8' );
    }

    /* ═══════════════════════════════════════════════════
     * Sitemap XML
     * ═══════════════════════════════════════════════════ */

    public function sitemap_attiva( $on ) {
        if ( $this->seo_plugin_active() ) {
            return $on;
        }
        $sm = $this->opt( 'olo_seo_sitemap' );
        return ( isset( $sm['enabled'] ) && ! $sm['enabled'] ) ? false : $on;
    }

    /** Le pagine in noindex escono dalla sitemap. */
    public function sitemap_senza_noindex( $args, $post_type ) {
        $args['meta_query']   = (array) ( $args['meta_query'] ?? [] ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- la sitemap del core interroga comunque per tipo, una pagina alla volta
        $args['meta_query'][] = [
            'relation' => 'OR',
            [ 'key' => '_olo_seo_noindex', 'compare' => 'NOT EXISTS' ],
            [ 'key' => '_olo_seo_noindex', 'value' => '1', 'compare' => '!=' ],
        ];
        return $args;
    }

    /** I tipi di contenuto messi in noindex escono dalla sitemap. */
    public function sitemap_tipi( $tipi ) {
        $a = $this->opt( 'olobuild_seo_advanced' );
        foreach ( array_keys( (array) $tipi ) as $pt ) {
            if ( ! empty( $a['noindex_pt'][ $pt ] ) ) {
                unset( $tipi[ $pt ] );
            }
        }
        return $tipi;
    }

    /** Gli archivi degli autori esclusi dai motori: niente sitemap degli utenti. */
    public function sitemap_senza_autori( $provider, $nome ) {
        if ( 'users' === $nome && ! $this->seo_plugin_active() ) {
            $a = $this->opt( 'olobuild_seo_advanced' );
            if ( ! empty( $a['noindex_author'] ) ) {
                return false;
            }
        }
        return $provider;
    }

    /** Categorie e tag esclusi dai motori escono dalla sitemap. */
    public function sitemap_tassonomie( $tax ) {
        $a = $this->opt( 'olobuild_seo_advanced' );
        if ( ! empty( $a['noindex_categories'] ) ) {
            unset( $tax['category'] );
        }
        if ( ! empty( $a['noindex_tags'] ) ) {
            unset( $tax['post_tag'] );
        }
        return $tax;
    }

    /** Chi aveva inviato la vecchia /?olo_sitemap=1 arriva alla sitemap di WordPress. */
    public function vecchia_sitemap() {
        if ( empty( $_GET['olo_sitemap'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- sola lettura: reindirizza un indirizzo pubblico
            return;
        }
        $dest = function_exists( 'get_sitemap_url' ) ? get_sitemap_url( 'index' ) : '';
        wp_safe_redirect( $dest ? $dest : home_url( '/' ), 301 );
        exit;
    }

    private function seo_plugin_active() {
        if ( defined( 'WPSEO_VERSION' ) ) return true;       // Yoast SEO
        if ( class_exists( 'RankMath' ) ) return true;       // Rank Math
        if ( defined( 'AIOSEO_VERSION' ) ) return true;      // All in One SEO
        if ( defined( 'SEOPRESS_VERSION' ) ) return true;    // SEOPress
        return false;
    }
}
