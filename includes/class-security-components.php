<?php
/**
 * Olo_Security_Components — integrità di TUTTI i plugin e temi (parte di OLOsecurity).
 *
 * Per i componenti di terzi NON si tiene un hash file-per-file (sarebbe un'option enorme
 * e genererebbe rumore a ogni aggiornamento). Si usa invece un'**impronta aggregata per
 * componente, legata alla sua versione**:
 *
 *   - versione cambiata  → aggiornamento legittimo → si riallinea la baseline, nessun allarme
 *   - versione invariata ma impronta diversa → **i file sono cambiati senza un update**:
 *     è la firma tipica di un'iniezione (webshell/skimmer in un plugin legittimo) → ALLARME
 *   - componente nuovo   → segnalato una volta (potrebbe essere un plugin malevolo droppato)
 *
 * OLObuild e il tema attivo sono esclusi: già coperti file-per-file dal Sentinel.
 *
 * Baseline trust-on-first-use in option olo_sec_components; "Conferma stato attuale"
 * (rebuild) per riallineare dopo una verifica.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Security_Components {

    const OPT        = 'olo_sec_components';
    const FILE_CAP   = 4000;   // file massimi per singolo componente
    const GLOBAL_CAP = 60000;  // file massimi processati per scansione (backstop)

    private static $ext  = [ 'php', 'js' ];
    private static $skip = [ 'node_modules', '.git', 'vendor', 'tests', 'test', 'languages' ];

    public static function get_baseline() {
        $b = get_option( self::OPT, [] );
        return is_array( $b ) ? $b : [];
    }

    public static function build_baseline() {
        update_option( self::OPT, [
            'created'  => time(),
            'snapshot' => self::snapshot(),
        ], false );
    }

    /** Fotografia di plugin e temi: per ciascuno versione + impronta aggregata. */
    public static function snapshot() {
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $processed     = 0;
        $global_capped = false;
        $olo_dir       = defined( 'OLO_PATH' ) ? basename( untrailingslashit( OLO_PATH ) ) : 'olobuild';

        $plugins = [];
        foreach ( get_plugins() as $file => $data ) {
            if ( $processed >= self::GLOBAL_CAP ) { $global_capped = true; break; }
            $d = dirname( $file );
            if ( $d === $olo_dir ) {
                continue; // OLObuild: già coperto file-per-file
            }
            if ( $d === '.' ) {
                // Plugin a file singolo.
                $path = wp_normalize_path( WP_PLUGIN_DIR . '/' . $file );
                $h    = @hash_file( 'sha256', $path );
                $plugins[ $file ] = [ 'ver' => $data['Version'] ?? '', 'agg' => $h ?: '', 'n' => 1 ];
                $processed++;
            } else {
                list( $agg, $n ) = self::aggregate( WP_PLUGIN_DIR . '/' . $d, $processed );
                $plugins[ $d ] = [ 'ver' => $data['Version'] ?? '', 'agg' => $agg, 'n' => $n ];
            }
        }

        $themes = [];
        $active = get_stylesheet();
        foreach ( wp_get_themes() as $slug => $theme ) {
            if ( $slug === $active ) {
                continue; // tema attivo: già coperto file-per-file
            }
            if ( $processed >= self::GLOBAL_CAP ) { $global_capped = true; break; }
            list( $agg, $n ) = self::aggregate( $theme->get_stylesheet_directory(), $processed );
            $themes[ $slug ] = [ 'ver' => (string) $theme->get( 'Version' ), 'agg' => $agg, 'n' => $n ];
        }

        return [ 'plugins' => $plugins, 'themes' => $themes, 'global_capped' => $global_capped ];
    }

    /** Impronta aggregata (sha256 dell'elenco ordinato "relpath:hash") dei .php/.js sotto $dir. */
    private static function aggregate( $dir, &$processed ) {
        $dir = trailingslashit( wp_normalize_path( $dir ) );
        if ( ! is_dir( $dir ) ) {
            return [ '', 0 ];
        }
        $parts = [];
        $count = 0;
        try {
            $it = new RecursiveIteratorIterator(
                new RecursiveCallbackFilterIterator(
                    new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ),
                    function ( $c ) {
                        $n = $c->getFilename();
                        if ( $c->isDir() ) {
                            return ! in_array( $n, self::$skip, true );
                        }
                        if ( substr( $n, -4 ) === '.map' ) {
                            return false;
                        }
                        return in_array( strtolower( $c->getExtension() ), self::$ext, true );
                    }
                ),
                RecursiveIteratorIterator::LEAVES_ONLY,
                RecursiveIteratorIterator::CATCH_GET_CHILD
            );
            foreach ( $it as $f ) {
                if ( $count >= self::FILE_CAP || $processed >= self::GLOBAL_CAP ) {
                    break;
                }
                $p = wp_normalize_path( $f->getPathname() );
                $h = @hash_file( 'sha256', $p );
                if ( $h ) {
                    $parts[] = ltrim( str_replace( $dir, '', $p ), '/' ) . ':' . $h;
                    $count++;
                    $processed++;
                }
            }
        } catch ( Exception $e ) {
            // Filesystem inaccessibile: non blocchiamo.
        }
        sort( $parts );
        return [ hash( 'sha256', implode( "\n", $parts ) ), $count ];
    }

    /**
     * Confronta lo stato attuale con la baseline. Auto-riallinea gli aggiornamenti
     * legittimi (versione cambiata), i nuovi e i rimossi; lascia come finding solo le
     * modifiche "senza update".
     *
     * @return array Finding: ['severity','label','path','detail'].
     */
    public static function scan() {
        $baseline = self::get_baseline();
        if ( empty( $baseline['snapshot'] ) ) {
            self::build_baseline();
            return [];
        }

        $base    = $baseline['snapshot'];
        $cur     = self::snapshot();
        $out     = [];
        $newbase = $base;
        $dirty   = false;

        $kinds = [ 'plugins' => __( 'Plugin', 'olobuild' ), 'themes' => __( 'Tema', 'olobuild' ) ];
        foreach ( $kinds as $kind => $label ) {
            $curset  = $cur[ $kind ] ?? [];
            $baseset = $base[ $kind ] ?? [];

            foreach ( $curset as $slug => $c ) {
                if ( ! isset( $baseset[ $slug ] ) ) {
                    /* translators: 1: Plugin/Tema, 2: slug */
                    $out[] = self::f( 'medium', sprintf( __( '%1$s nuovo non presente nella baseline', 'olobuild' ), $label ), $kind . ': ' . $slug, 'v' . ( $c['ver'] ?? '?' ) );
                    $newbase[ $kind ][ $slug ] = $c;
                    $dirty = true;
                } else {
                    $b = $baseset[ $slug ];
                    if ( (string) ( $c['ver'] ?? '' ) !== (string) ( $b['ver'] ?? '' ) ) {
                        // Aggiornamento legittimo → riallinea senza allarmare.
                        $newbase[ $kind ][ $slug ] = $c;
                        $dirty = true;
                    } elseif ( $c['agg'] !== '' && ! empty( $b['agg'] ) && ! hash_equals( (string) $b['agg'], (string) $c['agg'] ) ) {
                        /* translators: 1: Plugin/Tema, 2: slug */
                        $out[] = self::f( 'high', sprintf( __( '%1$s modificato senza aggiornamento di versione (possibile iniezione)', 'olobuild' ), $label ), $kind . ': ' . $slug, 'v' . ( $c['ver'] ?? '?' ) );
                    }
                }
            }

            // Rimossi: solo cleanup baseline (la disinstallazione non è un vettore d'attacco).
            if ( empty( $cur['global_capped'] ) ) {
                foreach ( $baseset as $slug => $b ) {
                    if ( ! isset( $curset[ $slug ] ) ) {
                        unset( $newbase[ $kind ][ $slug ] );
                        $dirty = true;
                    }
                }
            }
        }

        if ( ! empty( $cur['global_capped'] ) ) {
            $out[] = self::f( 'medium', __( 'Integrità plugin/temi parziale: troppi file, scansione interrotta al limite di sicurezza.', 'olobuild' ), 'components', '' );
        }

        if ( $dirty ) {
            update_option( self::OPT, [
                'created'  => $baseline['created'] ?? time(),
                'snapshot' => $newbase,
            ], false );
        }

        return $out;
    }

    private static function f( $severity, $label, $path, $detail ) {
        return [ 'severity' => $severity, 'label' => $label, 'path' => $path, 'detail' => $detail ];
    }
}
