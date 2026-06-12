<?php
/**
 * Olo_Uikit_Subset — serve un subset di uikit.min.css con i soli componenti
 * usati dal sito, appreso automaticamente dall'HTML renderizzato.
 *
 * Architettura (gemella di Olo_Page_CSS, ma auto-appresa):
 * - uikit.min.css viene tokenizzato in blocchi e ogni blocco è classificato
 *   per FAMIGLIA = primo segmento dopo `uk-` dei token di classe nel selettore
 *   (.uk-card-default → card). Classi di stato globali (uk-light, uk-open…)
 *   sono trasparenti per la classificazione; blocchi senza token → core.
 * - L'insieme delle famiglie usate dal sito è una UNION sitewide appresa:
 *   l'output buffer (Olo_Performance_Hints) scansiona ogni pagina servita
 *   e fa il merge dei token trovati nell'opzione `olo_uikit_families`.
 *   Union sitewide = stesso file su tutte le pagine = cache browser calda
 *   nella navigazione interna.
 * - Primo caricamento (store vuoto): si serve il full uikit (sicuro) e si
 *   semina lo store. Auto-guarigione: se una pagina usa famiglie non incluse
 *   nel subset servito, il buffer inietta il full uikit prima di </body> e
 *   aggiorna lo store (dalla vista successiva il subset è completo).
 * - Salvataggio template → reset dello store + file: si ri-apprende.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Uikit_Subset {

    const OPT = 'olo_uikit_families';

    /**
     * Classi di stato/contesto globali: trasparenti nella classificazione.
     * Include i CONTESTI inverse di UIkit (.uk-light, .uk-card-primary, .uk-tile-*…):
     * le regole "contesto × componente" si classificano così sul componente target.
     * Le regole composte SOLO da classi trasparenti finiscono nel core (sempre incluse).
     */
    const TRANSPARENT = [
        'uk-light', 'uk-dark', 'uk-open', 'uk-active', 'uk-disabled',
        'uk-hover', 'uk-touch', 'uk-notouch', 'uk-first-column', 'uk-last-column',
        'uk-preserve-color', 'uk-sticky-fixed', 'uk-sticky-below',
        'uk-card-primary', 'uk-card-secondary', 'uk-card-body',
        'uk-overlay-primary', 'uk-overlay-default',
        'uk-tile-primary', 'uk-tile-secondary', 'uk-tile-default', 'uk-tile-muted',
        'uk-section-primary', 'uk-section-secondary', 'uk-section-default', 'uk-section-muted',
        'uk-offcanvas-bar', 'uk-navbar-container', 'uk-navbar-transparent',
    ];

    /**
     * Dipendenze: componenti il cui JS genera markup di ALTRE famiglie
     * non presente nell'HTML server-side.
     */
    const DEPENDENCIES = [
        'lightbox' => [ 'slidenav', 'close', 'icon', 'position', 'overlay', 'transition', 'spinner', 'slideshow' ],
        'dropdown' => [ 'drop', 'dropbar' ],
        'navbar'   => [ 'drop', 'dropbar', 'nav', 'dropdown' ],
        'drop'     => [ 'dropbar' ],
        'modal'    => [ 'close', 'icon' ],
        'offcanvas'=> [ 'close', 'icon' ],
        'slider'   => [ 'slidenav', 'dotnav' ],
        'slideshow'=> [ 'slidenav', 'dotnav' ],
    ];

    /** Famiglie sempre incluse: markup generato SOLO da JS (UIkit.notification). */
    const ALWAYS = [ 'notification', 'totop' ];

    /** @var array|null Famiglie incluse nel subset servito (null = full uikit). */
    private static $served_families = null;

    /** @var int Wrapper @media duplicati emessi dal parser (per la validazione graffe). */
    private static $extra_wrappers = 0;

    public static function init() {
        add_action( 'olo_template_saved', [ __CLASS__, 'reset' ] );
    }

    /* ─────────────────────────────────────────────
     * Entry point: URL del subset
     * ───────────────────────────────────────────── */

    /**
     * URL del subset per il sito, o false per il full uikit
     * (store vuoto = fase di apprendimento, o build fallita).
     */
    public static function subset_url() {
        $src_path = OLO_PATH . 'assets/vendor/uikit/css/uikit.min.css';
        if ( ! is_readable( $src_path ) ) {
            return false;
        }

        $stored = get_option( self::OPT, [] );
        if ( ! is_array( $stored ) || empty( $stored ) ) {
            return false; // apprendimento: servi full, il buffer semina lo store
        }

        $families = self::expand_families( array_keys( $stored ) );

        $upload_dir = wp_upload_dir();
        $cache_dir  = $upload_dir['basedir'] . '/olobuild-cache/';
        $cache_url  = $upload_dir['baseurl'] . '/olobuild-cache/';
        sort( $families );
        $hash     = substr( md5( implode( ',', $families ) . '|' . (int) filemtime( $src_path ) . '|' . OLO_VERSION ), 0, 12 );
        $filename = "olo-uikit-{$hash}.css";
        $filepath = $cache_dir . $filename;

        if ( ! file_exists( $filepath ) ) {
            $css = self::build_css( $families );
            if ( false === $css ) {
                return false;
            }
            if ( ! is_dir( $cache_dir ) ) {
                wp_mkdir_p( $cache_dir );
            }
            // Una sola variante per sito: elimina i subset precedenti
            $old = glob( $cache_dir . 'olo-uikit-*.css' );
            if ( $old ) {
                foreach ( $old as $f ) {
                    @unlink( $f );
                }
            }
            if ( false === file_put_contents( $filepath, $css ) ) {
                return false;
            }
        }

        self::$served_families = $families;
        return $cache_url . $filename;
    }

    /* ─────────────────────────────────────────────
     * Apprendimento + auto-guarigione (dal buffer HTML)
     * ───────────────────────────────────────────── */

    /**
     * Chiamata dal buffer di output con l'HTML completo della pagina.
     * Apprende le famiglie usate; se il subset servito non le copre,
     * inietta il full uikit prima di </body> (auto-guarigione).
     *
     * @param string $html HTML della pagina.
     * @return string HTML (eventualmente con il fallback iniettato).
     */
    public static function learn_and_heal( $html ) {
        if ( ! is_string( $html ) || '' === $html ) {
            return $html;
        }

        $needed = self::scan_families( $html );
        if ( empty( $needed ) ) {
            return $html;
        }

        // Merge nello store (solo se ci sono novità)
        $stored = get_option( self::OPT, [] );
        if ( ! is_array( $stored ) ) {
            $stored = [];
        }
        $new = array_diff_key( array_fill_keys( $needed, 1 ), $stored );
        if ( ! empty( $new ) ) {
            update_option( self::OPT, array_merge( $stored, $new ), false );
        }

        // Auto-guarigione: subset servito ma famiglie scoperte non incluse
        if ( null !== self::$served_families && ! empty( $new ) ) {
            $missing = array_diff( self::expand_families( $needed ), self::$served_families );
            if ( ! empty( $missing ) ) {
                $tag = '<link rel="stylesheet" href="' . esc_url( OLO_URL . 'assets/vendor/uikit/css/uikit.min.css' ) . '?ver=' . rawurlencode( OLO_VERSION ) . '" media="all" />';
                $pos = strripos( $html, '</body>' );
                $html = ( false !== $pos ) ? substr_replace( $html, $tag, $pos, 0 ) : $html . $tag;
            }
        }

        return $html;
    }

    /** Estrae le famiglie uk-* presenti nell'HTML (classi, attributi, valori). */
    private static function scan_families( $html ) {
        $fams = [];
        if ( preg_match_all( '/(?<![a-z0-9_-])uk-([a-z][a-z0-9]*)/', $html, $m ) ) {
            $fams = array_unique( $m[1] );
        }
        // Stili base agganciati a selettori di elemento: rileva i tag stessi
        if ( preg_match( '/<(input|select|textarea|form|fieldset)\b/i', $html ) ) {
            $fams[] = 'form';
        }
        if ( false !== stripos( $html, '<table' ) ) {
            $fams[] = 'table';
        }
        return array_values( array_unique( $fams ) );
    }

    /** Espande con dipendenze + ALWAYS. */
    private static function expand_families( $families ) {
        $out = array_fill_keys( $families, true );
        foreach ( self::ALWAYS as $a ) {
            $out[ $a ] = true;
        }
        $queue = array_keys( $out );
        while ( $queue ) {
            $f = array_pop( $queue );
            foreach ( self::DEPENDENCIES[ $f ] ?? [] as $dep ) {
                if ( ! isset( $out[ $dep ] ) ) {
                    $out[ $dep ] = true;
                    $queue[]     = $dep;
                }
            }
        }
        return array_keys( $out );
    }

    /* ─────────────────────────────────────────────
     * Parser e build
     * ───────────────────────────────────────────── */

    /**
     * CSS subset: core + famiglie richieste. false se la validazione fallisce.
     */
    private static function build_css( $families ) {
        $css = file_get_contents( OLO_PATH . 'assets/vendor/uikit/css/uikit.min.css' );
        if ( false === $css ) {
            return false;
        }
        $stripped = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
        if ( null === $stripped ) {
            return false;
        }

        self::$extra_wrappers = 0;
        $buckets = self::parse_buckets( $stripped );
        if ( false === $buckets ) {
            return false;
        }

        // Validazione: nessuna regola persa. Un blocco @media con regole di N
        // famiglie viene emesso con N wrapper → N-1 coppie di graffe in più.
        $total = implode( '', $buckets );
        if ( substr_count( $total, '{' ) !== substr_count( $stripped, '{' ) + self::$extra_wrappers
            || substr_count( $total, '}' ) !== substr_count( $stripped, '}' ) + self::$extra_wrappers ) {
            return false;
        }

        $want = array_fill_keys( $families, true );
        $out  = $buckets['core'];
        unset( $buckets['core'] );
        foreach ( $buckets as $family => $chunk ) {
            if ( isset( $want[ $family ] ) ) {
                $out .= $chunk;
            }
        }
        return $out;
    }

    /**
     * Tokenizza CSS (senza commenti) in bucket per famiglia.
     * @media/@supports ricorsivi; il resto delle at-rule → core.
     */
    private static function parse_buckets( $css ) {
        $buckets = [ 'core' => '' ];
        $len     = strlen( $css );
        $i       = 0;
        while ( $i < $len ) {
            $brace = strpos( $css, '{', $i );
            if ( false === $brace ) {
                break;
            }
            $head  = trim( substr( $css, $i, $brace - $i ) );
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
                return false;
            }
            $body = substr( $css, $brace + 1, $j - $brace - 2 );

            if ( '' !== $head && '@' === $head[0] && preg_match( '/^@(media|supports)\b/i', $head ) ) {
                $inner = self::parse_buckets( $body );
                if ( false === $inner ) {
                    return false;
                }
                $emitted = 0;
                foreach ( $inner as $family => $chunk ) {
                    if ( '' !== $chunk ) {
                        if ( ! isset( $buckets[ $family ] ) ) {
                            $buckets[ $family ] = '';
                        }
                        $buckets[ $family ] .= $head . '{' . $chunk . '}';
                        $emitted++;
                    }
                }
                if ( $emitted > 1 ) {
                    self::$extra_wrappers += $emitted - 1;
                }
            } else {
                $family = self::classify( $head );
                if ( ! isset( $buckets[ $family ] ) ) {
                    $buckets[ $family ] = '';
                }
                $buckets[ $family ] .= $head . '{' . $body . '}';
            }

            $i = $j;
        }
        return $buckets;
    }

    /**
     * Classifica un gruppo di selettori in una famiglia uk-*.
     * Regola: i token (al netto di :not() e classi trasparenti) devono
     * appartenere tutti alla STESSA famiglia, altrimenti core.
     */
    private static function classify( $head ) {
        if ( '' === $head || '@' === $head[0] ) {
            return 'core';
        }
        // :not(...) non determina l'appartenenza
        $clean = preg_replace( '/:not\([^)]*\)/', '', $head );
        if ( ! preg_match_all( '/(?<![a-z0-9_-])uk-([a-z][a-z0-9-]*)/', $clean, $m ) ) {
            // Stili base via selettori di ELEMENTO (form.less / table.less di UIkit)
            if ( preg_match( '/(^|[\s,>+~(])(input|select|textarea|fieldset|legend|optgroup)\b/', $clean )
                || false !== strpos( $clean, '[type=' ) || false !== strpos( $clean, '::placeholder' ) ) {
                return 'form';
            }
            if ( preg_match( '/(^|[\s,>+~(])(table|caption|thead|tbody|tfoot)\b/', $clean ) ) {
                return 'table';
            }
            return 'core';
        }
        $families = [];
        foreach ( $m[0] as $idx => $token ) {
            if ( in_array( $token, self::TRANSPARENT, true ) ) {
                continue;
            }
            $root = explode( '-', $m[1][ $idx ], 2 )[0];
            $families[ $root ] = true;
        }
        if ( empty( $families ) ) {
            return 'core';
        }
        return ( 1 === count( $families ) ) ? array_key_first( $families ) : 'core';
    }

    /* ─────────────────────────────────────────────
     * Invalidazione
     * ───────────────────────────────────────────── */

    /** Reset: si ri-apprende dalla prossima vista (che serve il full uikit). */
    public static function reset() {
        delete_option( self::OPT );
        $upload_dir = wp_upload_dir();
        $files      = glob( $upload_dir['basedir'] . '/olobuild-cache/olo-uikit-*.css' );
        if ( $files ) {
            foreach ( $files as $file ) {
                @unlink( $file );
            }
        }
        self::$served_families = null;
    }
}
