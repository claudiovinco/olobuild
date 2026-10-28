<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Archive_Integration {

    private static $instance = null;

    /** @var int|null Il template d'archivio di questa richiesta, scelto una volta che la query principale è fatta. */
    private static $scelto = null;

    /** Le tile che possono seguire l'archivio aperto, col loro numero per pagina predefinito. */
    const TILE_CHE_SEGUONO = [ 'queryloop' => 6, 'postgrid' => 12 ];

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_filter( 'template_include', [ $this, 'maybe_override_archive' ], 99 );
        add_action( 'wp_enqueue_scripts', [ $this, 'maybe_enqueue_global_assets' ], 5 );
        add_action( 'pre_get_posts', [ __CLASS__, 'per_pagina_della_query' ] );
    }

    /**
     * Il tipo di contenuto dell'archivio aperto. Negli archivi di una tassonomia la query non porta il tipo: è quello a
     * cui la tassonomia appartiene, così le categorie di un tipo personalizzato non prendono il template degli Articoli.
     */
    public static function tipo_dell_archivio() {
        $tipo = get_query_var( 'post_type' );
        if ( is_array( $tipo ) ) {
            $tipo = reset( $tipo );
        }
        if ( ! $tipo && ( is_tax() || is_category() || is_tag() ) ) {
            $termine = get_queried_object();
            $tax     = $termine instanceof WP_Term ? get_taxonomy( $termine->taxonomy ) : null;
            if ( $tax && ! empty( $tax->object_type ) ) {
                $tipo = reset( $tax->object_type );
            }
        }
        return $tipo ? sanitize_key( (string) $tipo ) : 'post';
    }

    /**
     * Il template d'archivio della richiesta, scelto in un posto solo (pagina, asset, CSS, numero per pagina):
     * 1) una Regola di visualizzazione col contesto Archivio (una categoria con le sue sottocategorie, un tag, un
     * tipo…); 2) quello attivato per il tipo dell'archivio; 3) il generico. 0 = nessuno, resta il tema.
     */
    public static function template_id() {
        if ( ! is_archive() && ! is_home() ) {
            return 0;
        }
        if ( null !== self::$scelto ) {
            return self::$scelto;
        }
        $id = (int) get_option( 'olobuild_active_archive_' . self::tipo_dell_archivio(), 0 );
        if ( ! $id ) {
            $id = (int) get_option( 'olobuild_active_archive', 0 );
        }
        if ( class_exists( 'Olobuild_Template_Conditions' ) ) {
            $id = (int) Olobuild_Template_Conditions::instance()->resolve_by_conditions( $id, 'archive' );
        }
        if ( $id ) {
            $tpl = ( new Olobuild_Database() )->get_template( $id );
            if ( ! $tpl || ! in_array( $tpl['status'] ?? '', [ 'published', 'draft' ], true ) ) {
                $id = 0;
            }
        }
        // In pre_get_posts la query principale non è ancora fatta: si ricorda solo la scelta definitiva.
        if ( did_action( 'wp' ) ) {
            self::$scelto = $id;
        }
        return $id;
    }

    /**
     * Le tile che seguono l'archivio aperto (Query Loop, Post Grid, Sorgente dinamica) ne sfogliano le pagine: la query
     * principale prende il loro numero per pagina. Con un numero diverso da Impostazioni › Lettura WordPress rispondeva
     * 404 sulle ultime pagine della tile, o ne lasciava fuori.
     *
     * @param WP_Query $q
     */
    public static function per_pagina_della_query( $q ) {
        if ( is_admin() || ! $q->is_main_query() || $q->is_feed() ) {
            return;
        }
        if ( $q->is_search() ) {
            $tpl = (int) get_option( 'olobuild_active_search', 0 );
        } elseif ( $q->is_archive() || $q->is_home() ) {
            $tpl = self::template_id();
        } else {
            return;
        }
        $n = self::per_pagina( $tpl );
        if ( $n > 0 ) {
            $q->set( 'posts_per_page', $n );
        }
    }

    /**
     * Il numero per pagina della prima tile del template che segue l'archivio aperto (0 = nessuna).
     *
     * @param int $tpl_id
     * @return int
     */
    public static function per_pagina( $tpl_id ) {
        static $memo = [];
        $tpl_id = (int) $tpl_id;
        if ( $tpl_id <= 0 ) {
            return 0;
        }
        if ( ! isset( $memo[ $tpl_id ] ) ) {
            $tpl             = ( new Olobuild_Database() )->get_template( $tpl_id );
            $memo[ $tpl_id ] = ( $tpl && is_array( $tpl['content'] ?? null ) ) ? self::cerca_per_pagina( $tpl['content'] ) : 0;
        }
        return $memo[ $tpl_id ];
    }

    private static function cerca_per_pagina( $nodo ) {
        if ( ! is_array( $nodo ) ) {
            return 0;
        }
        $tipo = is_string( $nodo['type'] ?? null ) ? $nodo['type'] : '';
        $s    = is_array( $nodo['settings'] ?? null ) ? $nodo['settings'] : [];
        if ( isset( self::TILE_CHE_SEGUONO[ $tipo ] ) && ! empty( $s['segui_archivio'] ) ) {
            $n = absint( $s['posts_per_page'] ?? self::TILE_CHE_SEGUONO[ $tipo ] );
            return max( 1, min( 100, $n ? $n : self::TILE_CHE_SEGUONO[ $tipo ] ) );
        }
        $q = $nodo['dynamic']['_query'] ?? null;
        if ( is_array( $q ) && ! empty( $q['enabled'] ) && ! empty( $q['segui_archivio'] ) && 'posts' === ( $q['source'] ?? 'posts' ) ) {
            return max( 1, min( 100, absint( $q['posts_per_page'] ?? 6 ) ) );
        }
        foreach ( $nodo as $v ) {
            if ( is_array( $v ) ) {
                $n = self::cerca_per_pagina( $v );
                if ( $n ) {
                    return $n;
                }
            }
        }
        return 0;
    }

    /**
     * Gli argomenti di WP_Query dell'archivio aperto (categoria, tag, tassonomia, autore, data, ricerca, tipo: le
     * variabili della richiesta, come le ha lette WordPress), per le tile con «Segui l'archivio aperto». null fuori da
     * un archivio (il builder, una pagina qualunque): lì la tile mostra gli ultimi contenuti del suo tipo.
     *
     * @return array|null
     */
    public static function argomenti_dell_archivio() {
        global $wp_the_query;
        if ( ! ( $wp_the_query instanceof WP_Query ) || ! ( $wp_the_query->is_archive() || $wp_the_query->is_home() || $wp_the_query->is_search() ) ) {
            return null;
        }
        return self::variabili_pulite( (array) $wp_the_query->query );
    }

    /**
     * Le sole variabili pubbliche che descrivono un elenco (niente singolo contenuto, anteprima, pagina o feed), anche
     * quando arrivano dal browser («Carica altro»): un archivio si può sempre aprire dall'indirizzo, niente di più.
     *
     * @param array $vars
     * @return array
     */
    public static function variabili_pulite( $vars ) {
        global $wp;
        $pubbliche = ( $wp instanceof WP ) ? (array) $wp->public_query_vars : [];
        $fuori     = [ 'p', 'page_id', 'name', 'pagename', 'attachment', 'attachment_id', 'subpost', 'subpost_id', 'preview', 'page', 'paged', 'feed', 'embed', 'static', 'error', 'withcomments', 'withoutcomments', 'robots', 'favicon', 'sitemap', 'sitemap-subtype', 'sitemap-stylesheet', 'rest_route' ];
        $pulite    = [];
        foreach ( (array) $vars as $k => $v ) {
            $k = (string) $k;
            if ( ! in_array( $k, $pubbliche, true ) || in_array( $k, $fuori, true ) ) {
                continue;
            }
            if ( is_scalar( $v ) ) {
                $pulite[ $k ] = sanitize_text_field( (string) $v );
            } elseif ( 'post_type' === $k && is_array( $v ) ) {
                $pulite[ $k ] = array_map( 'sanitize_key', array_filter( $v, 'is_scalar' ) );
            }
        }
        if ( isset( $pulite['post_type'] ) ) {
            $tipi = array_filter( (array) $pulite['post_type'], 'is_post_type_viewable' );
            if ( ! $tipi ) {
                unset( $pulite['post_type'] );
            } else {
                $pulite['post_type'] = is_array( $pulite['post_type'] ) ? array_values( $tipi ) : reset( $tipi );
            }
        }
        return $pulite;
    }

    /**
     * Override the archive template when an Olobuild archive template is active.
     */
    public function maybe_override_archive( $template ) {
        $tpl_id = self::template_id();
        if ( ! $tpl_id ) {
            return $template;
        }

        // Store template ID for later use
        $GLOBALS['olobuild_archive_template_id'] = $tpl_id;

        // Return our custom template file
        return OLOBUILD_PATH . 'templates/archive-template.php';
    }

    /**
     * Enqueue UIkit + frontend CSS globally when an Olobuild archive template is active.
     */
    public function maybe_enqueue_global_assets() {
        if ( ! self::template_id() ) {
            return;
        }

        // UIkit CSS
        if ( ! wp_style_is( 'uikit-css', 'enqueued' ) ) {
            wp_enqueue_style(
                'uikit-css',
                OLOBUILD_URL . 'assets/vendor/uikit/css/uikit.min.css',
                [],
                '3.21.16'
            );
        }

        // UIkit JS
        if ( ! wp_script_is( 'uikit-js', 'enqueued' ) ) {
            wp_enqueue_script(
                'uikit-js',
                OLOBUILD_URL . 'assets/vendor/uikit/js/uikit.min.js',
                [],
                '3.21.16',
                true
            );
        }

        // UIkit Icons
        if ( ! wp_script_is( 'uikit-icons-js', 'enqueued' ) ) {
            wp_enqueue_script(
                'uikit-icons-js',
                OLOBUILD_URL . 'assets/vendor/uikit/js/uikit-icons.min.js',
                [ 'uikit-js' ],
                '3.21.16',
                true
            );
        }

        // Olobuild frontend CSS
        if ( ! wp_style_is( 'olo-frontend-css', 'enqueued' ) ) {
            wp_enqueue_style(
                'olo-frontend-css',
                OLOBUILD_URL . 'assets/css/frontend.css',
                [ 'uikit-css' ],
                OLOBUILD_VERSION
            );
        }

        // Style System CSS
        $style_css = Olobuild_Style_System::instance()->generate_css();
        if ( ! empty( $style_css ) ) {
            wp_add_inline_style( 'olo-frontend-css', $style_css );
        }
    }
}
