<?php
/**
 * Olobuild_Template_Conditions — Advanced display conditions for templates.
 *
 * Allows multiple conditions with AND/OR logic per template assignment.
 * Extends the simple "one template per post type" system.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Template_Conditions {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init() {
        // REST API for managing template conditions
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );

        // Override single template selection with conditions
        // (terzo argomento facoltativo: la pagina su cui valutarle, vedi resolve_zone()).
        add_filter( 'olobuild_resolve_template_id', [ $this, 'resolve_by_conditions' ], 10, 3 );

        // Admin UI: la pagina storica è ritirata (vedi register_admin_page()); il suo
        // salvataggio non è più agganciato: riscriveva l'option con una sola condizione,
        // senza esclusioni e in AND, e bastava edit_others_posts.
        add_action( 'admin_menu', [ $this, 'register_admin_page' ], 30 );
    }

    /* ─────────────────────────────────────────────
     * Template Resolution with Conditions
     * ───────────────────────────────────────────── */

    /**
     * Header o footer EFFETTIVO, con la sua provenienza. Unica catena, per il sito
     * e per il builder:
     *   1) assegnazione della pagina (meta `_olo_header_id` / `_olo_footer_id`;
     *      -1 = «nessun header/footer su questa pagina», e vince anche lui)
     *   2) regole di visualizzazione (filtro `olobuild_resolve_template_id`)
     *   3) header/footer globale (option `olobuild_active_header` / `_footer`)
     *
     * $post_id null: la richiesta corrente, come il sito ha sempre fatto.
     * $post_id > 0: quella pagina, anche da un'altra richiesta (il builder, in
     * admin): le regole leggono il post invece dei conditional tag.
     * $post_id <= 0: solo il globale, senza regole (in admin «Tutto il sito» o
     * «Utenti loggati» scatterebbero sempre).
     *
     * @param string   $zone    'header' | 'footer'
     * @param int|null $post_id
     * @return array { id: int, source: 'page'|'rule'|'global' }
     */
    public static function resolve_zone( $zone, $post_id = null ) {
        $zone = ( 'footer' === $zone ) ? 'footer' : 'header';

        if ( null === $post_id ) {
            $post = is_singular() ? (int) get_queried_object_id() : 0;
        } else {
            $post = max( 0, (int) $post_id );
        }

        if ( $post > 0 ) {
            $own = (int) get_post_meta( $post, '_olo_' . $zone . '_id', true );
            if ( $own ) {
                return [ 'id' => $own, 'source' => 'page' ];
            }
        }

        if ( null === $post_id ) {
            $by_rules = (int) apply_filters( 'olobuild_resolve_template_id', 0, $zone );
        } elseif ( $post > 0 ) {
            $by_rules = (int) apply_filters( 'olobuild_resolve_template_id', 0, $zone, $post );
        } else {
            $by_rules = 0;
        }
        if ( $by_rules ) {
            return [ 'id' => $by_rules, 'source' => 'rule' ];
        }

        return [ 'id' => (int) get_option( 'olobuild_active_' . $zone, 0 ), 'source' => 'global' ];
    }

    /**
     * Resolve template ID based on advanced conditions.
     * Falls back to simple post_type option if no conditions match.
     *
     * @param int    $template_id Current template ID (from simple system)
     * @param string $context     'single', 'archive', 'header', 'footer'
     * @param int    $post_id     Pagina su cui valutare le condizioni (0 = richiesta corrente)
     * @return int Template ID
     */
    public function resolve_by_conditions( $template_id, $context = 'single', $post_id = 0 ) {
        $post_id = (int) $post_id;
        $assignments = get_option( 'olobuild_template_conditions', [] );
        if ( empty( $assignments ) || ! is_array( $assignments ) ) {
            return $template_id;
        }

        // Filter assignments by context type
        $relevant = array_filter( $assignments, function( $a ) use ( $context ) {
            return ( $a['context'] ?? '' ) === $context && ! empty( $a['enabled'] );
        } );

        // Sort by priority (lower = higher priority)
        usort( $relevant, function( $a, $b ) {
            return ( $a['priority'] ?? 100 ) - ( $b['priority'] ?? 100 );
        } );

        foreach ( $relevant as $assignment ) {
            $tpl_id = intval( $assignment['template_id'] ?? 0 );
            if ( ! $tpl_id ) {
                continue;
            }

            $conditions = $assignment['conditions'] ?? [];
            $logic      = $assignment['conditions_logic'] ?? 'AND';

            if ( $this->evaluate_conditions( $conditions, $logic, $post_id ) ) {
                return $tpl_id;
            }
        }

        return $template_id;
    }

    /**
     * Evaluate a set of conditions with AND/OR logic.
     */
    private function evaluate_conditions( $conditions, $logic = 'AND', $post_id = 0 ) {
        if ( empty( $conditions ) ) {
            return true;
        }

        $results = [];
        foreach ( $conditions as $cond ) {
            $results[] = $this->evaluate_single( $cond, $post_id );
        }

        if ( $logic === 'OR' ) {
            return in_array( true, $results, true );
        }

        // AND: all must be true
        return ! in_array( false, $results, true );
    }

    private function evaluate_single( $cond, $post_id = 0 ) {
        $type   = $cond['type'] ?? '';
        $value  = $cond['value'] ?? '';
        $negate = ! empty( $cond['negate'] );

        // Su una pagina data (il builder): ciò che dipende dalla pagina legge il
        // post; utente e data restano sulla richiesta corrente, come sul sito.
        if ( $post_id > 0 ) {
            $result = $this->evaluate_for_post( $type, $value, (int) $post_id );
            if ( null !== $result ) {
                return $negate ? ! $result : $result;
            }
        }

        $result = false;

        switch ( $type ) {
            case 'entire_site':
                $result = true;
                break;

            case 'front_page':
                $result = is_front_page();
                break;

            case 'singular':
                $result = is_singular();
                break;

            case 'page':
                if ( empty( $value ) || $value === 'all' ) {
                    $result = is_page();
                } elseif ( is_numeric( $value ) ) {
                    $result = is_page( intval( $value ) );
                } else {
                    $result = is_page( self::candidati( $value ) );
                }
                break;

            case 'post':
                if ( empty( $value ) || $value === 'all' ) {
                    $result = is_singular( 'post' );
                } elseif ( is_numeric( $value ) ) {
                    $result = is_singular( 'post' ) && get_the_ID() === intval( $value );
                } else {
                    // is_single() vale per ogni tipo che non sia pagina o allegato: la
                    // regola «post» resta sugli articoli, come con l'ID.
                    $result = is_singular( 'post' ) && is_single( self::candidati( $value ) );
                }
                break;

            case 'post_type':
                $result = is_singular( sanitize_text_field( $value ) );
                break;

            case 'archive':
                $result = empty( $value ) || $value === 'all'
                    ? is_archive()
                    : is_post_type_archive( sanitize_text_field( $value ) );
                break;

            case 'category':
                if ( is_singular() ) {
                    $result = has_category( self::rif_categoria( $value ) );
                } else {
                    $result = is_category( self::rif_categoria( $value ) );
                }
                break;

            case 'tag':
                if ( is_singular() ) {
                    $result = has_tag( sanitize_text_field( $value ) );
                } else {
                    $result = is_tag( sanitize_text_field( $value ) );
                }
                break;

            case 'taxonomy':
                $parts = explode( ':', $value );
                if ( count( $parts ) >= 2 ) {
                    $result = has_term( $parts[1], $parts[0] );
                } elseif ( count( $parts ) === 1 ) {
                    $result = is_tax( $parts[0] );
                }
                break;

            case 'author':
                $result = is_singular() && (int) get_the_author_meta( 'ID' ) === (int) $value;
                break;

            case 'user_logged_in':
                $result = is_user_logged_in();
                break;

            case 'user_logged_out':
                $result = ! is_user_logged_in();
                break;

            case 'user_role':
                if ( is_user_logged_in() ) {
                    $user   = wp_get_current_user();
                    $result = in_array( sanitize_text_field( $value ), $user->roles, true );
                }
                break;

            case 'has_template':
                // Post has a specific Olobuild template assigned
                $result = is_singular() && get_post_meta( get_the_ID(), '_olo_template_id', true ) == intval( $value );
                break;

            case 'post_format':
                $result = is_singular() && has_post_format( sanitize_text_field( $value ) );
                break;

            case '404':
                $result = is_404();
                break;

            case 'search':
                $result = is_search();
                break;

            case 'date_before':
                $result = time() < strtotime( $value );
                break;

            case 'date_after':
                $result = time() > strtotime( $value );
                break;

            // WooCommerce
            case 'woo_shop':
                $result = function_exists( 'is_shop' ) && is_shop();
                break;
            case 'woo_product':
                $result = function_exists( 'is_product' ) && is_product();
                break;
            case 'woo_product_cat':
                if ( function_exists( 'is_product' ) && is_product() ) {
                    $result = has_term( sanitize_text_field( $value ), 'product_cat' );
                }
                break;
            case 'woo_cart':
                $result = function_exists( 'is_cart' ) && is_cart();
                break;
            case 'woo_checkout':
                $result = function_exists( 'is_checkout' ) && is_checkout();
                break;
            case 'woo_account':
                $result = function_exists( 'is_account_page' ) && is_account_page();
                break;
        }

        return $negate ? ! $result : $result;
    }

    /**
     * Le condizioni che dipendono dalla pagina, valutate su un post dato invece
     * che sulla richiesta corrente: lo stesso esito che il sito avrebbe aprendo
     * quel post (singolare). null = condizione che non dipende dalla pagina
     * (utente, data) o sconosciuta: la valuta evaluate_single() come sul sito.
     *
     * @param string $type
     * @param mixed  $value
     * @param int    $post_id
     * @return bool|null
     */
    private function evaluate_for_post( $type, $value, $post_id ) {
        $post_type = get_post_type( $post_id );

        switch ( $type ) {
            case 'entire_site':
            case 'singular':
                return true;

            case 'front_page':
                return 'page' === get_option( 'show_on_front' )
                    && (int) get_option( 'page_on_front' ) === $post_id;

            case 'page':
                if ( 'page' !== $post_type ) {
                    return false;
                }
                if ( empty( $value ) || 'all' === $value ) {
                    return true;
                }
                if ( is_numeric( $value ) ) {
                    // Come is_page( 0 ) sul sito: un numero che vale 0 è «qualunque pagina».
                    $page_id = intval( $value );
                    return 0 === $page_id || $page_id === $post_id;
                }
                return self::post_corrisponde( $post_id, self::candidati( $value ) );

            case 'post':
                if ( 'post' !== $post_type ) {
                    return false;
                }
                if ( empty( $value ) || 'all' === $value ) {
                    return true;
                }
                if ( is_numeric( $value ) ) {
                    return intval( $value ) === $post_id;
                }
                return self::post_corrisponde( $post_id, self::candidati( $value ) );

            case 'post_type':
                // Come is_singular( '0' ) sul sito: un valore vuoto per empty() è «qualsiasi tipo».
                $wanted = sanitize_text_field( $value );
                return empty( $wanted ) || $post_type === $wanted;

            case 'archive':
            case '404':
            case 'search':
                return false;

            case 'category':
                return (bool) has_category( self::rif_categoria( $value ), $post_id );

            case 'tag':
                return (bool) has_tag( sanitize_text_field( $value ), $post_id );

            case 'taxonomy':
                $parts = explode( ':', (string) $value );
                if ( count( $parts ) >= 2 ) {
                    return (bool) has_term( $parts[1], $parts[0], $post_id );
                }
                return false;

            case 'author':
                return (int) get_post_field( 'post_author', $post_id ) === (int) $value;

            case 'has_template':
                return (int) get_post_meta( $post_id, '_olo_template_id', true ) === intval( $value );

            case 'post_format':
                return (bool) has_post_format( sanitize_text_field( $value ), $post_id );

            // WooCommerce
            case 'woo_shop':
                return function_exists( 'wc_get_page_id' ) && (int) wc_get_page_id( 'shop' ) === $post_id;
            case 'woo_product':
                return function_exists( 'is_product' ) && 'product' === $post_type;
            case 'woo_product_cat':
                return function_exists( 'is_product' ) && 'product' === $post_type
                    && (bool) has_term( sanitize_text_field( $value ), 'product_cat', $post_id );
            case 'woo_cart':
                return function_exists( 'wc_get_page_id' ) && (int) wc_get_page_id( 'cart' ) === $post_id;
            case 'woo_checkout':
                return function_exists( 'wc_get_page_id' ) && (int) wc_get_page_id( 'checkout' ) === $post_id;
            case 'woo_account':
                return function_exists( 'wc_get_page_id' ) && (int) wc_get_page_id( 'myaccount' ) === $post_id;
        }

        return null;
    }

    /**
     * Il bersaglio di una condizione page / post / category scritta come testo:
     * il testo così com'è (slug, titolo o percorso «genitore/figlio», che
     * is_page(), is_single(), has_category() e is_category() del core confrontano
     * da sé) più il numero con cui comincia, se ce n'è uno.
     *
     * Prima il testo passava da intval(): uno slug valeva 0, che per il core è
     * «qualunque» (page: tutte le pagine; category: ogni articolo con una
     * categoria e ogni archivio di categoria) o «nessuno» (post). Il numero in
     * testa ('12-chi-siamo' → 12) tiene le corrispondenze che intval() dava già.
     * I valori numerici non passano di qui: restano ID, come sempre.
     *
     * @param mixed $value
     * @return array
     */
    private static function candidati( $value ) {
        $testo = is_scalar( $value ) ? trim( (string) $value ) : '';
        $voci  = [ $testo ];
        $id    = intval( $testo );
        if ( $id > 0 ) {
            $voci[] = $id;
        }
        return $voci;
    }

    /**
     * Argomento di has_category() / is_category() per una condizione category:
     * null = qualsiasi categoria (valore vuoto, '0' o 'all', come prima), un numero
     * = ID come prima, un testo = slug o nome (vedi candidati()).
     *
     * @param mixed $value
     * @return int|array|null
     */
    private static function rif_categoria( $value ) {
        if ( empty( $value ) || 'all' === $value ) {
            return null;
        }
        if ( is_numeric( $value ) ) {
            return intval( $value );
        }
        return self::candidati( $value );
    }

    /**
     * Gemello di is_page() / is_single() del core per il builder, su un post dato
     * invece che sulla richiesta corrente: corrisponde se un candidato è il suo ID,
     * il suo titolo, il suo slug o il suo percorso («genitore/figlio»).
     *
     * @param int   $post_id
     * @param array $candidati
     * @return bool
     */
    private static function post_corrisponde( $post_id, $candidati ) {
        $post = get_post( $post_id );
        if ( ! $post ) {
            return false;
        }
        $voci = array_map( 'strval', (array) $candidati );
        if ( in_array( (string) $post->ID, $voci, true )
            || in_array( (string) $post->post_title, $voci, true )
            || in_array( (string) $post->post_name, $voci, true ) ) {
            return true;
        }
        foreach ( $voci as $percorso ) {
            // Come il core: un percorso ha una «/» dopo il primo carattere.
            if ( ! strpos( $percorso, '/' ) ) {
                continue;
            }
            $trovato = get_page_by_path( $percorso, OBJECT, $post->post_type );
            if ( $trovato && (int) $trovato->ID === (int) $post->ID ) {
                return true;
            }
        }
        return false;
    }

    /* ─────────────────────────────────────────────
     * REST API
     * ───────────────────────────────────────────── */

    public function register_routes() {
        register_rest_route( 'olobuild/v1', '/template-conditions', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_conditions' ],
                // Impostazione SITE-WIDE (override globale di header/footer/template): solo admin.
                'permission_callback' => function () {
                    return current_user_can( 'manage_options' );
                },
            ],
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'save_conditions' ],
                // Scrittura globale: manage_options + nonce wp_rest (anti-CSRF), coerente con check_permission().
                'permission_callback' => function ( $request ) {
                    if ( ! current_user_can( 'manage_options' ) ) {
                        return false;
                    }
                    $nonce = $request->get_header( 'x-wp-nonce' );
                    if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
                        return new WP_Error( 'rest_forbidden', 'Nonce non valido.', [ 'status' => 403 ] );
                    }
                    return true;
                },
            ],
        ] );

        // Voci dei menu del «Valore» in Assegnazione template (TemplateConditionsTab):
        // gli stessi valori che il valutatore legge (ID per page, post e category;
        // slug per tag, o l'ID se lo slug ha ottetti %xx; slug per tipi e ruoli).
        // Sulla radice REST del plugin: /wp/v2 non elenca i ruoli, né i tipi senza
        // show_in_rest, né le bozze.
        register_rest_route( 'olobuild/v1', '/template-conditions/choices', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_choices' ],
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            },
            'args'                => [
                'kind'    => [ 'type' => 'string', 'required' => true ],
                'search'  => [ 'type' => 'string', 'default' => '' ],
                'include' => [ 'type' => 'string', 'default' => '' ],
            ],
        ] );
    }

    /** Voci restituite per una ricerca (pagine, articoli, categorie, tag). */
    const LIMITE_SCELTE = 50;

    /**
     * GET template-conditions/choices?kind=page|post|category|tag|post_type|archive|role
     * &search=<testo> — le voci [{ value, label }] per il menu del valore.
     * &include=<valore salvato> — una sola voce, con value uguale al valore salvato
     * e per label ciò che quel valore indica sul sito, trovato col confronto esatto
     * del valutatore (vedi scelta_post / scelta_termine; più voci separate da « / »);
     * nessuna voce se non indica niente. Il menu mostra il titolo senza riscrivere il
     * valore.
     */
    public function get_choices( $request ) {
        $kind    = sanitize_key( (string) $request->get_param( 'kind' ) );
        $search  = sanitize_text_field( (string) $request->get_param( 'search' ) );
        $include = sanitize_text_field( (string) $request->get_param( 'include' ) );

        switch ( $kind ) {
            case 'page':
            case 'post':
                $items = '' !== $include ? self::scelta_post( $kind, $include ) : self::scelte_post( $kind, $search );
                break;
            case 'category':
            case 'tag':
                $tax   = 'category' === $kind ? 'category' : 'post_tag';
                $items = '' !== $include ? self::scelta_termine( $tax, $include ) : self::scelte_termini( $tax, $search );
                break;
            case 'post_type':
            case 'archive':
                $items = self::scelte_tipi( 'archive' === $kind );
                break;
            case 'role':
                $items = self::scelte_ruoli();
                break;
            default:
                return new WP_Error( 'rest_invalid_param', __( 'Tipo di scelta non valido.', 'olobuild' ), [ 'status' => 400 ] );
        }

        return rest_ensure_response( [ 'items' => $items ] );
    }

    /** Stati in cui una pagina o un articolo si possono scegliere (una regola può precederne la pubblicazione). */
    private static function stati_sceglibili() {
        return [ 'publish', 'future', 'draft', 'pending', 'private' ];
    }

    private static function testo_semplice( $s ) {
        return trim( html_entity_decode( wp_strip_all_tags( (string) $s ), ENT_QUOTES, 'UTF-8' ) );
    }

    private static function voce_post( $p ) {
        $title = self::testo_semplice( $p->post_title );
        if ( '' === $title ) {
            /* translators: %d: ID della pagina o dell'articolo */
            $title = sprintf( __( 'Senza titolo #%d', 'olobuild' ), (int) $p->ID );
        }
        if ( 'publish' !== $p->post_status ) {
            $stato = get_post_status_object( $p->post_status );
            if ( $stato && ! empty( $stato->label ) ) {
                $title .= ' · ' . $stato->label;
            }
        }
        return [ 'value' => (string) $p->ID, 'label' => $title ];
    }

    private static function scelte_post( $post_type, $search ) {
        $args = [
            'post_type'           => $post_type,
            'post_status'         => self::stati_sceglibili(),
            'posts_per_page'      => self::LIMITE_SCELTE,
            'no_found_rows'       => true,
            'ignore_sticky_posts' => true,
        ];
        if ( '' !== $search ) {
            $args['s']       = $search;
            $args['orderby'] = 'relevance';
        } elseif ( 'page' === $post_type ) {
            $args['orderby'] = 'title';
            $args['order']   = 'ASC';
        } else {
            $args['orderby'] = 'date';
            $args['order']   = 'DESC';
        }

        $items = [];
        // Un numero cercato è anche un ID.
        if ( '' !== $search && is_numeric( $search ) && intval( $search ) > 0 ) {
            $by_id = get_post( intval( $search ) );
            if ( $by_id && $post_type === $by_id->post_type && in_array( $by_id->post_status, self::stati_sceglibili(), true ) ) {
                $items[ (int) $by_id->ID ] = self::voce_post( $by_id );
            }
        }
        foreach ( get_posts( $args ) as $p ) {
            if ( ! isset( $items[ (int) $p->ID ] ) ) {
                $items[ (int) $p->ID ] = self::voce_post( $p );
            }
        }
        return array_values( $items );
    }

    /** Titoli nominati al massimo nell'etichetta di un valore che indica più voci. */
    const LIMITE_CORRISPONDENZE = 5;

    /**
     * La voce di un valore salvato: value = il valore così com'è (il menu non lo
     * riscrive), label = ciò che indica; più voci separate da « / ».
     *
     * @param mixed $value
     * @param array $etichette
     * @return array
     */
    private static function voce_valore( $value, $etichette ) {
        $mostrate = array_slice( $etichette, 0, self::LIMITE_CORRISPONDENZE );
        $label    = implode( ' / ', $mostrate );
        $altre    = count( $etichette ) - count( $mostrate );
        if ( $altre > 0 ) {
            /* translators: %d: quante altre voci indica il valore */
            $label .= ' ' . sprintf( _n( '+ %d altra', '+ altre %d', $altre, 'olobuild' ), $altre );
        }
        return [ 'value' => (string) $value, 'label' => $label ];
    }

    /**
     * Ciò che il valore salvato indica sul sito, con lo STESSO confronto del
     * valutatore: is_page() / is_single() del core, di cui post_corrisponde() è il
     * gemello (ID, titolo e slug uguali alla lettera; percorso solo con una «/» dopo
     * il primo carattere). Le query qui sotto sono più larghe (lo slug passa da
     * sanitize_title_for_query, il titolo dalla collation, che ignora maiuscole e
     * accenti): raccolgono i candidati, e resta solo ciò che il confronto esatto
     * accetta. Così 'chi siamo', 'Chi-Siamo' o '/chi-siamo/' risultano «non trovato»,
     * come sul sito, e 'team' trova anche la pagina figlia con quello slug. Un testo
     * può indicare più voci (l'ID in testa e uno slug, due pagine figlie con lo
     * stesso slug): la regola vale per tutte e l'etichetta ne nomina fino a
     * LIMITE_CORRISPONDENZE, poi «+ N altre» (voce_valore).
     */
    private static function scelta_post( $post_type, $value ) {
        $trovati = [];
        if ( is_numeric( $value ) ) {
            $p = intval( $value ) > 0 ? get_post( intval( $value ) ) : null;
            if ( $p && $post_type === $p->post_type ) {
                $trovati[] = $p;
            }
        } else {
            $voci     = self::candidati( $value );
            $testo    = $voci[0];
            $raccolti = [];
            // Il numero in testa ('12-chi-siamo' → 12): l'ID che intval() dava già.
            if ( isset( $voci[1] ) ) {
                $p = get_post( $voci[1] );
                if ( $p ) {
                    $raccolti[ (int) $p->ID ] = $p;
                }
            }
            // Slug con qualsiasi genitore, poi titolo.
            foreach ( [ 'post_name__in' => [ $testo ], 'title' => $testo ] as $campo => $cerca ) {
                $args = [
                    'post_type'      => $post_type,
                    'post_status'    => self::stati_sceglibili(),
                    'posts_per_page' => 20,
                    'no_found_rows'  => true,
                    'orderby'        => 'ID',
                    'order'          => 'ASC',
                ];
                $args[ $campo ] = $cerca;
                foreach ( get_posts( $args ) as $p ) {
                    $raccolti[ (int) $p->ID ] = $p;
                }
            }
            if ( strpos( $testo, '/' ) ) {
                $p = get_page_by_path( $testo, OBJECT, $post_type );
                if ( $p ) {
                    $raccolti[ (int) $p->ID ] = $p;
                }
            }
            foreach ( $raccolti as $p ) {
                if ( $post_type === $p->post_type && self::post_corrisponde( (int) $p->ID, $voci ) ) {
                    $trovati[] = $p;
                }
            }
        }
        if ( ! $trovati ) {
            return [];
        }
        $etichette = [];
        foreach ( $trovati as $p ) {
            $voce        = self::voce_post( $p );
            $etichette[] = $voce['label'];
        }
        return [ self::voce_valore( $value, $etichette ) ];
    }

    /**
     * Il valore che il menu salva: l'ID per le categorie; per i tag lo slug, salvo
     * gli slug con ottetti %xx (tag in cirillico, greco, CJK, emoji: remove_accents
     * non li traslittera). Il salvataggio e il valutatore passano il valore da
     * sanitize_text_field(), che toglie ogni %xx: uno slug tutto codificato diventava
     * '', cioè «qualsiasi tag». Per quei tag si salva l'ID come stringa: has_tag() e
     * is_tag() confrontano una stringa numerica con term_id.
     */
    private static function voce_termine( $term, $taxonomy ) {
        $slug = (string) $term->slug;
        if ( 'category' === $taxonomy || false !== strpos( $slug, '%' ) ) {
            $value = (string) $term->term_id;
        } else {
            $value = $slug;
        }
        return [
            'value' => $value,
            'label' => self::testo_semplice( $term->name ),
        ];
    }

    private static function scelte_termini( $taxonomy, $search ) {
        $args = [
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
            'number'     => self::LIMITE_SCELTE,
            'orderby'    => 'name',
            'order'      => 'ASC',
        ];
        if ( '' !== $search ) {
            $args['search'] = $search;
        }
        $terms = get_terms( $args );
        if ( is_wp_error( $terms ) || ! is_array( $terms ) ) {
            return [];
        }
        $items = [];
        foreach ( $terms as $term ) {
            if ( is_object( $term ) ) {
                $items[] = self::voce_termine( $term, $taxonomy );
            }
        }
        return $items;
    }

    /**
     * La categoria o il tag che il valore salvato indica sul sito, con lo STESSO
     * confronto del valutatore: category passa da rif_categoria() (un numero è un
     * ID; un testo è testo + numero in testa), tag va a has_tag() / is_tag() così
     * com'è. get_terms() per slug normalizza e per nome ignora maiuscole e accenti
     * (collation): raccoglie i candidati, e resta solo ciò che termine_corrisponde()
     * accetta. Più voci: l'etichetta ne nomina fino a LIMITE_CORRISPONDENZE, poi
     * «+ N altre» (voce_valore); nessuna: [].
     */
    private static function scelta_termine( $taxonomy, $value ) {
        if ( 'category' === $taxonomy ) {
            $voci = is_numeric( $value ) ? [ intval( $value ) ] : self::candidati( $value );
        } else {
            $voci = [ trim( (string) $value ) ];
        }
        $raccolti = [];
        foreach ( $voci as $voce ) {
            if ( ( is_int( $voce ) || is_numeric( $voce ) ) && intval( $voce ) > 0 ) {
                $term = get_term( intval( $voce ), $taxonomy );
                if ( $term && ! is_wp_error( $term ) ) {
                    $raccolti[ (int) $term->term_id ] = $term;
                }
            }
            if ( is_int( $voce ) || '' === $voce ) {
                continue;
            }
            foreach ( [ 'slug', 'name' ] as $campo ) {
                $terms = get_terms( [
                    'taxonomy'               => $taxonomy,
                    'hide_empty'             => false,
                    'number'                 => 20,
                    'update_term_meta_cache' => false,
                    $campo                   => $voce,
                ] );
                if ( ! is_array( $terms ) ) {
                    continue;
                }
                foreach ( $terms as $term ) {
                    if ( is_object( $term ) ) {
                        $raccolti[ (int) $term->term_id ] = $term;
                    }
                }
            }
        }
        $etichette = [];
        foreach ( $raccolti as $term ) {
            if ( self::termine_corrisponde( $term, $voci ) ) {
                $etichette[] = self::testo_semplice( $term->name );
            }
        }
        return $etichette ? [ self::voce_valore( $value, $etichette ) ] : [];
    }

    /**
     * Gemello di is_object_in_term() (has_category / has_tag) del core: un intero
     * è un ID; una stringa numerica vale come ID, e come ogni stringa è confrontata
     * alla lettera con nome e slug. is_category() (archivi) fa lo stesso, salvo un
     * caso limite: rende stringa anche l'intero, e is_category( 12 ) accetta pure
     * una categoria che abbia nome o slug «12».
     *
     * @param WP_Term $term
     * @param array   $voci
     * @return bool
     */
    private static function termine_corrisponde( $term, $voci ) {
        foreach ( $voci as $voce ) {
            if ( ( is_int( $voce ) || is_numeric( $voce ) ) && (int) $term->term_id === intval( $voce ) ) {
                return true;
            }
            if ( is_int( $voce ) ) {
                continue;
            }
            $s = (string) $voce;
            if ( (string) $term->name === $s || (string) $term->slug === $s ) {
                return true;
            }
        }
        return false;
    }

    /** Tipi di contenuto pubblici (per gli archivi: solo quelli che ne hanno uno). */
    private static function scelte_tipi( $solo_con_archivio ) {
        $items = [];
        foreach ( get_post_types( [ 'public' => true ], 'objects' ) as $slug => $obj ) {
            if ( $solo_con_archivio && empty( $obj->has_archive ) ) {
                continue;
            }
            $nome    = $solo_con_archivio ? $obj->labels->name : $obj->labels->singular_name;
            $items[] = [
                'value' => (string) $slug,
                'label' => self::testo_semplice( $nome ) . ' (' . $slug . ')',
            ];
        }
        return $items;
    }

    private static function scelte_ruoli() {
        $items = [];
        foreach ( wp_roles()->get_names() as $slug => $name ) {
            $items[] = [ 'value' => (string) $slug, 'label' => translate_user_role( $name ) ];
        }
        return $items;
    }

    public function get_conditions( $request ) {
        return rest_ensure_response( get_option( 'olobuild_template_conditions', [] ) );
    }

    public function save_conditions( $request ) {
        $data = $request->get_json_params();
        if ( ! is_array( $data ) ) {
            return new WP_Error( 'invalid', 'Dati non validi', [ 'status' => 400 ] );
        }
        // Il client (TemplateConditionsTab.vue) invia { rules: [...] }; accetta anche
        // l'array nudo per retrocompatibilita'. Senza questo unwrap il salvataggio UI non persisteva.
        if ( isset( $data['rules'] ) && is_array( $data['rules'] ) ) {
            $data = $data['rules'];
        }

        $clean = [];
        foreach ( $data as $item ) {
            $clean[] = [
                'enabled'          => ! empty( $item['enabled'] ),
                'name'             => sanitize_text_field( $item['name'] ?? '' ),
                'template_id'      => intval( $item['template_id'] ?? 0 ),
                'context'          => sanitize_text_field( $item['context'] ?? 'single' ),
                'priority'         => max( 1, intval( $item['priority'] ?? 10 ) ),
                'conditions'       => $this->sanitize_conditions_arr( $item['conditions'] ?? [] ),
                'conditions_logic' => in_array( $item['conditions_logic'] ?? 'AND', [ 'AND', 'OR' ], true ) ? ( $item['conditions_logic'] ?? 'AND' ) : 'AND',
            ];
        }

        update_option( 'olobuild_template_conditions', $clean, false );
        return rest_ensure_response( [ 'success' => true ] );
    }

    private function sanitize_conditions_arr( $conditions ) {
        if ( ! is_array( $conditions ) ) {
            return [];
        }
        $clean = [];
        foreach ( $conditions as $c ) {
            $clean[] = [
                'type'   => sanitize_text_field( $c['type'] ?? '' ),
                'value'  => sanitize_text_field( $c['value'] ?? '' ),
                'negate' => ! empty( $c['negate'] ),
            ];
        }
        return $clean;
    }

    /* ─────────────────────────────────────────────
     * Admin UI
     * ───────────────────────────────────────────── */

    public function register_admin_page() {
        // Pagina migrata in ?page=olobuilder-settings&tab=tplconditions (Assegnazione
        // template). La vecchia «Regole di visualizzazione» (olobuilder-template-rules)
        // salvava una sola condizione per regola, senza esclusioni e in AND:
        // salvandola si perdevano le condizioni impostate dalla Configurazione.
        // render_admin_page() e handle_admin_save() restano, non più agganciati.
    }

    public function render_admin_page() {
        if ( ! current_user_can( 'edit_others_posts' ) ) {
            wp_die( esc_html__( 'Accesso negato.', 'olobuild' ) );
        }

        $assignments = get_option( 'olobuild_template_conditions', [] );
        if ( ! is_array( $assignments ) ) $assignments = [];

        // Carica template per dropdown
        $db = new Olobuild_Database();
        $all = $db->list_templates( [ 'per_page' => 200, 'orderby' => 'title', 'order' => 'ASC' ] )['items'] ?? [];
        $headers = array_values( array_filter( $all, function ( $t ) { return ( $t['type'] ?? '' ) === 'header' && $t['status'] === 'published'; } ) );
        $footers = array_values( array_filter( $all, function ( $t ) { return ( $t['type'] ?? '' ) === 'footer' && $t['status'] === 'published'; } ) );

        $public_cpts = get_post_types( [ 'public' => true ], 'objects' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per mostrare la notice "salvato" dopo il redirect; nessuna modifica di stato; isset() non usa il valore.
        $saved   = isset( $_GET['olo_saved'] );
        ?>
        <div class="wrap olo-tpl-rules">
            <h1><?php esc_html_e( 'Regole di visualizzazione template', 'olobuild' ); ?></h1>
            <p class="description">
                <?php esc_html_e( 'Definisci dove ogni template Header/Footer si applica. Le regole hanno priorità sull\'header/footer globale e cedono il passo a un\'eventuale assegnazione per-pagina dal metabox Olobuild.', 'olobuild' ); ?>
            </p>

            <?php if ( $saved ) : ?>
                <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Regole salvate.', 'olobuild' ); ?></p></div>
            <?php endif; ?>

            <?php if ( empty( $headers ) && empty( $footers ) ) : ?>
                <p><em><?php esc_html_e( 'Nessun template Header/Footer pubblicato. Crea prima un template e impostalo come "Pubblicato".', 'olobuild' ); ?></em></p>
                <?php return; ?>
            <?php endif; ?>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="olo-tpl-rules-form">
                <input type="hidden" name="action" value="olo_save_template_conditions" />
                <?php wp_nonce_field( 'olo_save_template_conditions' ); ?>

                <table class="widefat striped" id="olo-tpl-rules-table">
                    <thead>
                        <tr>
                            <th style="width:36px;"></th>
                            <th style="width:18%;"><?php esc_html_e( 'Nome', 'olobuild' ); ?></th>
                            <th style="width:22%;"><?php esc_html_e( 'Template', 'olobuild' ); ?></th>
                            <th style="width:15%;"><?php esc_html_e( 'Area', 'olobuild' ); ?></th>
                            <th style="width:10%;"><?php esc_html_e( 'Priorità', 'olobuild' ); ?></th>
                            <th><?php esc_html_e( 'Condizione', 'olobuild' ); ?></th>
                            <th style="width:48px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $rows = empty( $assignments ) ? [ [] ] : $assignments;
                        foreach ( $rows as $i => $a ) :
                            $name        = $a['name']        ?? '';
                            $template_id = (int) ( $a['template_id'] ?? 0 );
                            $context     = $a['context']     ?? 'header';
                            $priority    = (int) ( $a['priority'] ?? 10 );
                            $enabled     = isset( $a['enabled'] ) ? ! empty( $a['enabled'] ) : true;
                            $cond_first  = $a['conditions'][0] ?? [ 'type' => '', 'value' => '', 'negate' => false ];
                            $ct          = $cond_first['type']  ?? '';
                            $cv          = $cond_first['value'] ?? '';
                            ?>
                            <tr class="olo-tpl-rule-row">
                                <td>
                                    <input type="checkbox" name="rules[<?php echo (int) $i; ?>][enabled]" value="1" <?php checked( $enabled ); ?> title="<?php esc_attr_e( 'Abilita', 'olobuild' ); ?>" />
                                </td>
                                <td>
                                    <input type="text" name="rules[<?php echo (int) $i; ?>][name]" value="<?php echo esc_attr( $name ); ?>" placeholder="<?php esc_attr_e( 'es. Header strutture', 'olobuild' ); ?>" style="width:100%;" />
                                </td>
                                <td>
                                    <select name="rules[<?php echo (int) $i; ?>][template_id]" class="olo-tpl-select" data-context="<?php echo esc_attr( $context ); ?>" style="width:100%;">
                                        <option value="0">— <?php esc_html_e( 'Seleziona template', 'olobuild' ); ?> —</option>
                                        <optgroup label="<?php esc_attr_e( 'Header', 'olobuild' ); ?>">
                                            <?php foreach ( $headers as $t ) : ?>
                                                <option value="<?php echo (int) $t['id']; ?>" data-type="header" <?php selected( $template_id, (int) $t['id'] ); ?>>#<?php echo (int) $t['id']; ?> — <?php echo esc_html( $t['title'] ); ?></option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                        <optgroup label="<?php esc_attr_e( 'Footer', 'olobuild' ); ?>">
                                            <?php foreach ( $footers as $t ) : ?>
                                                <option value="<?php echo (int) $t['id']; ?>" data-type="footer" <?php selected( $template_id, (int) $t['id'] ); ?>>#<?php echo (int) $t['id']; ?> — <?php echo esc_html( $t['title'] ); ?></option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    </select>
                                </td>
                                <td>
                                    <select name="rules[<?php echo (int) $i; ?>][context]" style="width:100%;">
                                        <option value="header" <?php selected( $context, 'header' ); ?>><?php esc_html_e( 'Header', 'olobuild' ); ?></option>
                                        <option value="footer" <?php selected( $context, 'footer' ); ?>><?php esc_html_e( 'Footer', 'olobuild' ); ?></option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="rules[<?php echo (int) $i; ?>][priority]" value="<?php echo (int) $priority; ?>" min="1" max="999" style="width:80px;" title="<?php esc_attr_e( 'Più basso = più importante', 'olobuild' ); ?>" />
                                </td>
                                <td class="olo-cond-cell">
                                    <select name="rules[<?php echo (int) $i; ?>][conditions][0][type]" class="olo-cond-type" style="min-width:170px;">
                                        <option value=""><?php esc_html_e( '— Seleziona —', 'olobuild' ); ?></option>
                                        <option value="entire_site" <?php selected( $ct, 'entire_site' ); ?>><?php esc_html_e( 'Tutto il sito', 'olobuild' ); ?></option>
                                        <option value="front_page" <?php selected( $ct, 'front_page' ); ?>><?php esc_html_e( 'Front page', 'olobuild' ); ?></option>
                                        <option value="post_type" <?php selected( $ct, 'post_type' ); ?>><?php esc_html_e( 'Singoli di un tipo (CPT)', 'olobuild' ); ?></option>
                                        <option value="archive" <?php selected( $ct, 'archive' ); ?>><?php esc_html_e( 'Archivio di un tipo (CPT)', 'olobuild' ); ?></option>
                                        <option value="page" <?php selected( $ct, 'page' ); ?>><?php esc_html_e( 'Una pagina specifica (ID)', 'olobuild' ); ?></option>
                                        <option value="post" <?php selected( $ct, 'post' ); ?>><?php esc_html_e( 'Un articolo specifico (ID)', 'olobuild' ); ?></option>
                                        <option value="search" <?php selected( $ct, 'search' ); ?>><?php esc_html_e( 'Risultati ricerca', 'olobuild' ); ?></option>
                                        <option value="404" <?php selected( $ct, '404' ); ?>><?php esc_html_e( 'Pagina 404', 'olobuild' ); ?></option>
                                        <option value="user_logged_in" <?php selected( $ct, 'user_logged_in' ); ?>><?php esc_html_e( 'Utenti loggati', 'olobuild' ); ?></option>
                                        <option value="user_logged_out" <?php selected( $ct, 'user_logged_out' ); ?>><?php esc_html_e( 'Utenti non loggati', 'olobuild' ); ?></option>
                                    </select>
                                    <?php $show_cpt = in_array( $ct, [ 'post_type', 'archive' ], true ); ?>
                                    <select name="rules[<?php echo (int) $i; ?>][conditions][0][value]" class="olo-cond-value-cpt" style="min-width:200px;<?php echo $show_cpt ? '' : 'display:none;'; ?>" <?php disabled( ! $show_cpt ); ?>>
                                        <option value=""><?php esc_html_e( '— Tipo —', 'olobuild' ); ?></option>
                                        <?php foreach ( $public_cpts as $slug => $obj ) : ?>
                                            <option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $cv, $slug ); ?>><?php echo esc_html( $obj->labels->singular_name . ' (' . $slug . ')' ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php $show_id = in_array( $ct, [ 'page', 'post' ], true ); ?>
                                    <input type="text" name="rules[<?php echo (int) $i; ?>][conditions][0][value]" class="olo-cond-value-id" value="<?php echo $show_id ? esc_attr( $cv ) : ''; ?>" placeholder="<?php esc_attr_e( 'ID', 'olobuild' ); ?>" style="width:80px;<?php echo $show_id ? '' : 'display:none;'; ?>" <?php disabled( ! $show_id ); ?> />
                                </td>
                                <td>
                                    <button type="button" class="button olo-rule-remove" title="<?php esc_attr_e( 'Rimuovi', 'olobuild' ); ?>">×</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <p style="margin-top:14px;">
                    <button type="button" class="button" id="olo-rule-add">+ <?php esc_html_e( 'Aggiungi regola', 'olobuild' ); ?></button>
                    <button type="submit" class="button button-primary" style="margin-left:8px;"><?php esc_html_e( 'Salva regole', 'olobuild' ); ?></button>
                </p>

                <h2 style="margin-top:30px;"><?php esc_html_e( 'Come funziona', 'olobuild' ); ?></h2>
                <ul style="list-style:disc;margin-left:18px;">
                    <li><strong><?php esc_html_e( 'Priorità più bassa = vince per prima', 'olobuild' ); ?></strong>. <?php esc_html_e( 'A parità, l\'ordine in tabella decide.', 'olobuild' ); ?></li>
                    <li><?php esc_html_e( 'Se un singolo post ha un header/footer assegnato dal metabox Olobuild, quello vince comunque sulle regole qui.', 'olobuild' ); ?></li>
                    <li><?php esc_html_e( 'Se nessuna regola matcha, viene usato l\'header/footer globale (Gestione Template → Attiva).', 'olobuild' ); ?></li>
                </ul>
            </form>
        </div>
        <script>
        (function () {
            function syncCondValue(row) {
                var sel = row.querySelector('.olo-cond-type');
                var type = sel ? sel.value : '';
                var cpt  = row.querySelector('.olo-cond-value-cpt');
                var idIn = row.querySelector('.olo-cond-value-id');
                var showCpt = (type === 'post_type' || type === 'archive');
                var showId  = (type === 'page' || type === 'post');
                // I due input hanno lo stesso `name` per condividere il valore.
                // Disabilitiamo quello non in uso così PHP riceve un solo
                // valore corrispondente al tipo selezionato (HTML disabled
                // exclude il campo dal form submission).
                if (cpt)  { cpt.style.display  = showCpt ? '' : 'none'; cpt.disabled  = !showCpt; }
                if (idIn) { idIn.style.display = showId  ? '' : 'none'; idIn.disabled = !showId; }
            }
            document.addEventListener('change', function (e) {
                if (e.target.classList.contains('olo-cond-type')) {
                    syncCondValue(e.target.closest('tr'));
                }
            });
            document.addEventListener('click', function (e) {
                if (e.target.id === 'olo-rule-add') {
                    var tbody = document.querySelector('#olo-tpl-rules-table tbody');
                    var row = tbody.querySelector('tr');
                    if (!row) return;
                    var clone = row.cloneNode(true);
                    var newIdx = tbody.children.length;
                    clone.querySelectorAll('input,select').forEach(function (el) {
                        el.name = el.name.replace(/rules\[\d+\]/, 'rules[' + newIdx + ']');
                        if (el.type === 'checkbox') el.checked = true;
                        else if (el.tagName === 'SELECT') el.selectedIndex = 0;
                        else el.value = '';
                    });
                    tbody.appendChild(clone);
                    syncCondValue(clone);
                }
                if (e.target.classList.contains('olo-rule-remove')) {
                    var tbody = e.target.closest('tbody');
                    var tr = e.target.closest('tr');
                    if (tbody && tbody.children.length > 1) {
                        tr.remove();
                    } else {
                        tr.querySelectorAll('input,select').forEach(function (el) {
                            if (el.type === 'checkbox') el.checked = false;
                            else if (el.tagName === 'SELECT') el.selectedIndex = 0;
                            else el.value = '';
                        });
                        syncCondValue(tr);
                    }
                }
            });
            // initial sync per ogni row
            document.querySelectorAll('#olo-tpl-rules-table tr.olo-tpl-rule-row').forEach(syncCondValue);
        })();
        </script>
        <?php
    }

    public function handle_admin_save() {
        if ( ! current_user_can( 'edit_others_posts' ) ) {
            wp_die( 'Forbidden' );
        }
        check_admin_referer( 'olo_save_template_conditions' );

        // Nonce verificato sopra con check_admin_referer(). wp_unslash() rimuove gli
        // slash WP sull'intero array; ogni scalare viene poi sanitizzato singolarmente
        // sotto (sanitize_text_field / cast int / whitelist).
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- array sanitizzato per-campo nel loop sottostante.
        $raw   = isset( $_POST['rules'] ) ? wp_unslash( $_POST['rules'] ) : [];
        if ( ! is_array( $raw ) ) $raw = [];

        $clean = [];
        $idx   = 0;
        foreach ( $raw as $r ) {
            if ( ! is_array( $r ) ) continue;
            $tid = (int) ( $r['template_id'] ?? 0 );
            if ( ! $tid ) continue; // skip incomplete

            $cond_in = $r['conditions'][0] ?? [];
            $ct = isset( $cond_in['type'] ) ? sanitize_text_field( $cond_in['type'] ) : '';
            $cv = isset( $cond_in['value'] ) ? sanitize_text_field( $cond_in['value'] ) : '';
            if ( $ct === '' ) continue; // condizione vuota → ignora

            $clean[] = [
                'enabled'          => ! empty( $r['enabled'] ),
                'name'             => sanitize_text_field( $r['name'] ?? '' ),
                'template_id'      => $tid,
                'context'          => in_array( $r['context'] ?? '', [ 'header', 'footer' ], true ) ? ( $r['context'] ?? '' ) : 'header',
                'priority'         => max( 1, (int) ( $r['priority'] ?? 10 ) ),
                'conditions'       => [ [ 'type' => $ct, 'value' => $cv, 'negate' => false ] ],
                'conditions_logic' => 'AND',
            ];
            $idx++;
        }

        update_option( 'olobuild_template_conditions', $clean, false );

        wp_safe_redirect( add_query_arg( 'olo_saved', '1', admin_url( 'admin.php?page=olobuilder-template-rules' ) ) );
        exit;
    }
}
