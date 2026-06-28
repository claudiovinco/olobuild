<?php
/**
 * Olo_Page_CSS — CSS per-tile: serve alla pagina solo le porzioni di frontend.css
 * relative ai tile effettivamente presenti, più il core strutturale.
 *
 * Architettura:
 * - frontend.css resta l'unica fonte (nessun file splittato nel repo).
 * - A runtime il file viene tokenizzato in blocchi top-level; ogni blocco è
 *   classificato per FAMIGLIA tramite prefissi di classe (vedi FAMILIES).
 *   Tutto ciò che non è classificabile con certezza resta nel core (conservativo).
 * - All'enqueue si calcolano i tipi di tile della pagina (template body via
 *   _olo_template_id + shortcode nel contenuto, TUTTI gli header/footer
 *   pubblicati — copre anche gli header condizionali —, template attivi
 *   archive/search/404, popup globali) e si serve un file combinato cachato
 *   in olobuild-cache (hash = famiglie + mtime + versione).
 * - Safety net: se a render avvenuto compare un template con famiglie non
 *   incluse (hook olo_template_rendered), viene accodato il frontend.css
 *   completo nel footer (CSS idempotente, caso raro).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Page_CSS {

    /**
     * Famiglie di CSS separabili da frontend.css.
     * prefixes = prefissi di selettore (con punto) che identificano la famiglia.
     * types    = tipi di tile che richiedono la famiglia.
     * Un blocco con selettori misti (famiglia + altro) o non riconosciuti → core.
     */
    const FAMILIES = [
        'mapsuite' => [ 'prefixes' => [ '.olo-map-' ], 'types' => [ 'map', 'osmmap', 'popup', 'servicesearch', 'serviceresults' ] ],
        'services' => [ 'prefixes' => [ '.olo-svsearch', '.olo-svresults', '.olo-hostcard' ], 'types' => [ 'servicesearch', 'serviceresults' ] ],
        'switcher' => [ 'prefixes' => [ '.olo-sp-' ], 'types' => [ 'switcherpanel' ] ],
        'gallery'  => [ 'prefixes' => [ '.olo-gallery-preset-', '.olo-gal-' ], 'types' => [ 'gallery' ] ],
        'hero'     => [ 'prefixes' => [ '.olo-hero-' ], 'types' => [ 'hero' ] ],
        'postgrid' => [ 'prefixes' => [ '.olo-postgrid', '.olo-pg-' ], 'types' => [ 'postgrid' ] ],
        'counter'  => [ 'prefixes' => [ '.olo-cnt' ], 'types' => [ 'counter' ] ],
        'navmenu'  => [ 'prefixes' => [ '.olo-navmenu', '.olo-mega-' ], 'types' => [ 'navmenu' ] ],
        'gridflt'  => [ 'prefixes' => [ '.olo-filter-' ], 'types' => [ 'grid' ] ],
    ];

    /** @var array|null Famiglie incluse nel CSS servito a questa pagina (null = swap non attivo). */
    private static $included_families = null;

    /** @var array Template già contabilizzati (id => true). */
    private static $accounted_tpls = [];

    /** @var bool Fallback full CSS già accodato. */
    private static $fallback_done = false;

    /** @var int Wrapper @media duplicati emessi dal parser (per la validazione graffe). */
    private static $extra_wrappers = 0;

    public static function init() {
        // Safety net: template renderizzati a runtime (shortcode nel body, nested)
        add_action( 'olo_template_rendered', [ __CLASS__, 'ensure_template' ], 10, 1 );
        // Invalidazione file combinati al salvataggio di un template
        add_action( 'olo_template_saved', [ __CLASS__, 'flush_files' ] );
    }

    /* ─────────────────────────────────────────────
     * Entry point: URL del CSS di pagina
     * ───────────────────────────────────────────── */

    /**
     * Ritorna l'URL del CSS combinato per la pagina corrente,
     * o false per usare il fallback (frontend.css intero).
     */
    public static function page_css_url() {
        $src_path = OLO_PATH . 'assets/css/frontend.css';
        if ( ! is_readable( $src_path ) ) {
            return false;
        }

        $types = self::collect_page_types();
        if ( null === $types ) {
            return false; // raccolta fallita → fallback full
        }

        $families = self::families_for_types( $types );

        $upload_dir = wp_upload_dir();
        $cache_dir  = $upload_dir['basedir'] . '/olobuild-cache/';
        $cache_url  = $upload_dir['baseurl'] . '/olobuild-cache/';
        $hash       = substr( md5( implode( ',', $families ) . '|' . (int) filemtime( $src_path ) . '|' . OLO_VERSION ), 0, 12 );
        $filename   = "olo-pagecss-{$hash}.css";
        $filepath   = $cache_dir . $filename;

        if ( ! file_exists( $filepath ) ) {
            $css = self::build_css( $families );
            if ( false === $css ) {
                return false;
            }
            if ( ! is_dir( $cache_dir ) ) {
                wp_mkdir_p( $cache_dir );
            }
            if ( false === file_put_contents( $filepath, $css ) ) {
                return false;
            }
        }

        self::$included_families = $families;
        return $cache_url . $filename;
    }

    /* ─────────────────────────────────────────────
     * Raccolta tipi di tile della pagina
     * ───────────────────────────────────────────── */

    /**
     * Tipi di tile presenti nella pagina corrente (union di tutti i template
     * coinvolti). false-positive ammessi (aggiungono solo CSS), false-negative no.
     *
     * @return array|null Lista tipi, o null se la raccolta non è affidabile.
     */
    private static function collect_page_types() {
        if ( ! class_exists( 'Olo_Database' ) ) {
            return null;
        }

        $tpl_ids = [];

        // Body: meta della pagina + shortcode nel contenuto
        if ( is_singular() ) {
            $pid = get_queried_object_id();
            if ( $pid ) {
                $t = (int) get_post_meta( $pid, '_olo_template_id', true );
                if ( $t ) {
                    $tpl_ids[] = $t;
                }
                $post_type = get_post_type( $pid );
                if ( $post_type ) {
                    $t = (int) get_option( "olo_active_single_{$post_type}", 0 );
                    if ( $t ) {
                        $tpl_ids[] = $t;
                    }
                }
                $post = get_post( $pid );
                if ( $post && preg_match_all( '/\[(?:olo|mosaic)_template[^\]]*id=["\']?(\d+)/', (string) $post->post_content, $m ) ) {
                    foreach ( $m[1] as $sid ) {
                        $tpl_ids[] = (int) $sid;
                    }
                }
            }
        } else {
            // Archivi / search / 404: includi tutti gli attivi pertinenti (conservativo)
            foreach ( [ 'olo_active_archive', 'olo_active_search', 'olo_active_404' ] as $opt ) {
                $t = (int) get_option( $opt, 0 );
                if ( $t ) {
                    $tpl_ids[] = $t;
                }
            }
            $qo = get_queried_object();
            if ( $qo instanceof WP_Post_Type || ( is_object( $qo ) && ! empty( $qo->name ) ) ) {
                $t = (int) get_option( 'olo_active_archive_' . $qo->name, 0 );
                if ( $t ) {
                    $tpl_ids[] = $t;
                }
            }
        }

        $types = [];

        // Header/footer: TUTTI i pubblicati (copre Template Conditions per-pagina)
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- tabella custom del plugin ({prefix}olo_templates); nessun equivalente WP_Query; solo letterali e nome tabella interpolati (nessun valore utente); risultato non cacheabile (cache gestita dal file CSS combinato in olobuild-cache).
        $hf = $wpdb->get_col(
            "SELECT content FROM {$wpdb->prefix}olo_templates WHERE type IN ('header','footer') AND status = 'published'"
        );
        if ( is_array( $hf ) ) {
            foreach ( $hf as $raw ) {
                $content = json_decode( (string) $raw, true );
                if ( is_array( $content ) ) {
                    self::walk_types( $content, $types );
                }
            }
        }

        // Popup globali
        $popups = get_option( 'olo_global_popups', [] );
        if ( is_array( $popups ) ) {
            foreach ( $popups as $popup ) {
                if ( is_array( $popup ) && ! empty( $popup['template_id'] ) ) {
                    $tpl_ids[] = (int) $popup['template_id'];
                }
            }
        }

        // Template raccolti per ID
        $db = new Olo_Database();
        foreach ( array_unique( array_filter( $tpl_ids ) ) as $tid ) {
            $tpl = $db->get_template( $tid );
            if ( $tpl && ! empty( $tpl['content'] ) && is_array( $tpl['content'] ) ) {
                self::walk_types( $tpl['content'], $types );
                self::$accounted_tpls[ $tid ] = true;
            }
        }

        return array_keys( $types );
    }

    /**
     * Deep-walk di un albero template: registra ogni valore stringa con chiave
     * 'type'. I false-positive (es. settings.bg.type) aggiungono solo CSS.
     */
    private static function walk_types( $node, &$types ) {
        if ( ! is_array( $node ) ) {
            return;
        }
        foreach ( $node as $key => $value ) {
            if ( 'type' === $key && is_string( $value ) && $value !== '' ) {
                $types[ $value ] = true;
            } elseif ( is_array( $value ) ) {
                self::walk_types( $value, $types );
            }
        }
    }

    /** Famiglie richieste da una lista di tipi. */
    private static function families_for_types( $types ) {
        $families = [];
        foreach ( self::FAMILIES as $key => $def ) {
            if ( array_intersect( $def['types'], $types ) ) {
                $families[] = $key;
            }
        }
        return $families;
    }

    /* ─────────────────────────────────────────────
     * Parser e build
     * ───────────────────────────────────────────── */

    /**
     * Costruisce il CSS combinato (core + famiglie richieste), minificato.
     *
     * @param array $families Famiglie da includere.
     * @return string|false CSS, o false se la validazione fallisce.
     */
    private static function build_css( $families ) {
        $css = file_get_contents( OLO_PATH . 'assets/css/frontend.css' );
        if ( false === $css ) {
            return false;
        }

        // Commenti via prima del parsing (possono contenere graffe)
        $stripped = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
        if ( null === $stripped ) {
            return false;
        }

        self::$extra_wrappers = 0;
        $buckets = self::parse_buckets( $stripped );
        if ( false === $buckets ) {
            return false;
        }

        // Validazione: la somma dei bucket deve coprire tutte le regole originali.
        // Un @media con regole di N famiglie emette N wrapper → N-1 coppie extra.
        $total = implode( '', $buckets );
        if ( substr_count( $total, '{' ) !== substr_count( $stripped, '{' ) + self::$extra_wrappers
            || substr_count( $total, '}' ) !== substr_count( $stripped, '}' ) + self::$extra_wrappers ) {
            return false;
        }

        $out = $buckets['core'];
        foreach ( self::FAMILIES as $key => $def ) {
            if ( in_array( $key, $families, true ) && ! empty( $buckets[ $key ] ) ) {
                $out .= "\n" . $buckets[ $key ];
            }
        }

        if ( class_exists( 'Olo_Asset_Optimizer' ) ) {
            $min = Olo_Asset_Optimizer::minify_css( $out );
            if ( substr_count( $min, '{' ) === substr_count( $out, '{' ) ) {
                $out = $min;
            }
        }

        return $out;
    }

    /**
     * Tokenizza CSS (già senza commenti) in bucket per famiglia + core.
     * Gestisce @media/@supports con ricorsione; @keyframes/@font-face → core.
     *
     * @param string $css CSS senza commenti.
     * @return array|false [ 'core' => css, famiglia => css, ... ]
     */
    private static function parse_buckets( $css ) {
        $buckets = [ 'core' => '' ];
        foreach ( array_keys( self::FAMILIES ) as $key ) {
            $buckets[ $key ] = '';
        }

        $len = strlen( $css );
        $i   = 0;
        while ( $i < $len ) {
            $brace = strpos( $css, '{', $i );
            if ( false === $brace ) {
                break; // resto = solo whitespace/at-rule senza corpo
            }
            $head = trim( substr( $css, $i, $brace - $i ) );

            // Chiusura bilanciata
            $depth = 1;
            $j     = $brace + 1;
            while ( $j < $len && $depth > 0 ) {
                $c = $css[ $j ];
                if ( '{' === $c ) {
                    $depth++;
                } elseif ( '}' === $c ) {
                    $depth--;
                }
                $j++;
            }
            if ( 0 !== $depth ) {
                return false; // CSS malformato: abort → fallback
            }
            $body = substr( $css, $brace + 1, $j - $brace - 2 );

            if ( '' !== $head && '@' === $head[0] && preg_match( '/^@(media|supports)\b/i', $head ) ) {
                // Ricorsione: smista le regole interne per famiglia
                $inner = self::parse_buckets( $body );
                if ( false === $inner ) {
                    return false;
                }
                $emitted = 0;
                foreach ( $inner as $key => $chunk ) {
                    if ( '' !== trim( $chunk ) ) {
                        $buckets[ $key ] .= $head . '{' . $chunk . '}' . "\n";
                        $emitted++;
                    }
                }
                if ( $emitted > 1 ) {
                    self::$extra_wrappers += $emitted - 1;
                }
            } else {
                $bucket = self::classify( $head );
                $buckets[ $bucket ] .= $head . '{' . $body . '}' . "\n";
            }

            $i = $j;
        }

        return $buckets;
    }

    /**
     * Classifica un selettore (o gruppo) in una famiglia.
     * Regola: TUTTI i selettori del gruppo devono appartenere alla STESSA
     * famiglia, altrimenti core. Le at-rule atomiche (@keyframes ecc.) → core.
     */
    private static function classify( $head ) {
        if ( '' === $head || '@' === $head[0] ) {
            return 'core';
        }
        $found = [];
        foreach ( preg_split( '/\s*,\s*/', $head ) as $selector ) {
            $family = null;
            foreach ( self::FAMILIES as $key => $def ) {
                foreach ( $def['prefixes'] as $prefix ) {
                    if ( false !== strpos( $selector, $prefix ) ) {
                        $family = $key;
                        break 2;
                    }
                }
            }
            if ( null === $family ) {
                return 'core';
            }
            $found[ $family ] = true;
        }
        return ( 1 === count( $found ) ) ? array_key_first( $found ) : 'core';
    }

    /* ─────────────────────────────────────────────
     * Safety net + invalidazione
     * ───────────────────────────────────────────── */

    /**
     * olo_template_rendered: se un template non contabilizzato introduce
     * famiglie non incluse, accoda il frontend.css completo (footer).
     */
    public static function ensure_template( $template_id ) {
        if ( null === self::$included_families || self::$fallback_done ) {
            return;
        }
        $template_id = (int) $template_id;
        if ( isset( self::$accounted_tpls[ $template_id ] ) ) {
            return;
        }
        self::$accounted_tpls[ $template_id ] = true;

        $db  = new Olo_Database();
        $tpl = $db->get_template( $template_id );
        if ( ! $tpl || empty( $tpl['content'] ) || ! is_array( $tpl['content'] ) ) {
            return;
        }
        $types = [];
        self::walk_types( $tpl['content'], $types );
        $needed = self::families_for_types( array_keys( $types ) );
        if ( array_diff( $needed, self::$included_families ) ) {
            self::$fallback_done = true;
            wp_enqueue_style( 'olo-frontend-full', OLO_URL . 'assets/css/frontend.css', [], OLO_VERSION );
        }
    }

    /** Elimina i file combinati cachati (rigenerati alla prossima vista). */
    public static function flush_files() {
        $upload_dir = wp_upload_dir();
        $files      = glob( $upload_dir['basedir'] . '/olobuild-cache/olo-pagecss-*.css' );
        if ( $files ) {
            foreach ( $files as $file ) {
                wp_delete_file( $file );
            }
        }
    }
}
