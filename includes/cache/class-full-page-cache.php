<?php
/**
 * Olo_FullPage_Cache — full-page cache di OLObuild.
 *
 * Orchestra il drop-in advanced-cache.php (che fa il lavoro vero, prima del bootstrap
 * di WordPress): lo installa/rimuove, gestisce il define WP_CACHE in wp-config.php,
 * scrive la configurazione che il drop-in legge (cache/olobuild/fpc-config.php) e
 * invalida la cache quando un contenuto cambia.
 *
 * Sicurezza: non tocca mai un drop-in di un ALTRO cache plugin (riconosce il proprio
 * dalla firma) e non duplica/altera un WP_CACHE già definito da altri (host/plugin):
 * inserisce e rimuove solo la propria riga marcata.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_FullPage_Cache {

    /** Opzione condivisa col pannello Performance. */
    const OPT = 'olo_performance';

    /** Opzione che custodisce il salt della cache key. */
    const SALT_OPT = 'olo_fpc_salt';

    /** Firma per riconoscere il NOSTRO advanced-cache.php. */
    const DROPIN_SIG = 'Olobuild Full-Page Cache';

    /** Marcatore della riga WP_CACHE che inseriamo in wp-config.php. */
    const WPCONFIG_MARKER = 'OLOBUILD_FPC';

    public static function init() {
        // Reagisce al cambio delle impostazioni Performance (toggle/ttl/esclusioni).
        add_action( 'update_option_' . self::OPT, [ __CLASS__, 'on_settings_change' ], 10, 2 );
        add_action( 'add_option_' . self::OPT, [ __CLASS__, 'on_settings_add' ], 10, 2 );

        // Invalidazione: ogni modifica di contenuto/struttura svuota la cache.
        add_action( 'olo_template_saved', [ __CLASS__, 'purge_all' ] );
        add_action( 'save_post', [ __CLASS__, 'on_save_post' ], 20, 2 );
        add_action( 'wp_trash_post', [ __CLASS__, 'purge_all' ] );
        add_action( 'wp_update_nav_menu', [ __CLASS__, 'purge_all' ] );
        add_action( 'switch_theme', [ __CLASS__, 'purge_all' ] );
        add_action( 'customize_save_after', [ __CLASS__, 'purge_all' ] );
        foreach ( [ 'olo_active_header', 'olo_active_footer', 'olo_styles' ] as $opt ) {
            add_action( 'update_option_' . $opt, [ __CLASS__, 'purge_all' ] );
        }
    }

    /* ── stato ─────────────────────────────────────────────── */

    public static function is_enabled() {
        $o = get_option( self::OPT, [] );
        return is_array( $o ) && ! empty( $o['full_page_cache'] );
    }

    private static function cache_dir()     { return WP_CONTENT_DIR . '/cache/olobuild/'; }
    private static function dropin_path()   { return WP_CONTENT_DIR . '/advanced-cache.php'; }
    private static function dropin_source() { return OLO_PATH . 'includes/cache/advanced-cache.php'; }
    private static function config_path()   { return self::cache_dir() . 'fpc-config.php'; }

    /* ── reazione al cambio impostazioni ───────────────────── */

    public static function on_settings_change( $old, $new ) {
        self::sync();
    }

    public static function on_settings_add( $option, $value ) {
        self::sync();
    }

    /**
     * Allinea drop-in + WP_CACHE + config allo stato del toggle. Idempotente.
     *
     * @return array{ok:bool,reason?:string}
     */
    public static function sync() {
        if ( self::is_enabled() ) {
            $res = self::install_dropin();
            self::ensure_wp_cache( true );
            self::write_config( true );
            self::purge_all();
            return $res;
        }

        // Disattivato: il drop-in resta ma diventa inerte (config enabled=false →
        // passthrough), così un toggle frequente non riscrive wp-config ogni volta.
        // La rimozione vera avviene alla disattivazione del plugin.
        self::write_config( false );
        self::purge_all();
        return [ 'ok' => true ];
    }

    /* ── drop-in ───────────────────────────────────────────── */

    /**
     * Copia il drop-in in wp-content/advanced-cache.php. NON sovrascrive il drop-in
     * di un altro cache plugin (lo riconosce dalla mancanza della nostra firma).
     *
     * @return array{ok:bool,reason?:string}
     */
    public static function install_dropin() {
        $dest = self::dropin_path();
        if ( file_exists( $dest ) ) {
            $head = (string) @file_get_contents( $dest, false, null, 0, 600 );
            if ( strpos( $head, self::DROPIN_SIG ) === false ) {
                return [ 'ok' => false, 'reason' => 'foreign_dropin' ];
            }
        }
        $src = self::dropin_source();
        if ( ! is_readable( $src ) ) {
            return [ 'ok' => false, 'reason' => 'source_missing' ];
        }

        return [ 'ok' => (bool) @copy( $src, $dest ) ];
    }

    /** Rimuove il drop-in SOLO se è il nostro. */
    public static function uninstall_dropin() {
        $dest = self::dropin_path();
        if ( file_exists( $dest ) ) {
            $head = (string) @file_get_contents( $dest, false, null, 0, 600 );
            if ( strpos( $head, self::DROPIN_SIG ) !== false ) {
                @unlink( $dest );
            }
        }
    }

    /* ── wp-config: define WP_CACHE ────────────────────────── */

    /**
     * Inserisce o rimuove la NOSTRA riga `define('WP_CACHE', true)` in wp-config.php.
     * Non duplica né rimuove un WP_CACHE definito da altri. Fa un backup .olo-bak.
     */
    public static function ensure_wp_cache( $on ) {
        $path = self::wp_config_path();
        if ( $path === '' || ! is_writable( $path ) ) {
            return false;
        }
        $src     = (string) file_get_contents( $path );
        $defined = (bool) preg_match( '/define\(\s*[\'"]WP_CACHE[\'"]\s*,/', $src );
        $marker  = preg_quote( self::WPCONFIG_MARKER, '/' );

        if ( $on ) {
            if ( $defined ) {
                return true; // già presente (nostro o dell'host): non si tocca.
            }
            $line = "/* " . self::WPCONFIG_MARKER . " */ define( 'WP_CACHE', true ); /* /" . self::WPCONFIG_MARKER . " */\n";
            $new  = preg_replace( '/^(<\?php\s*\n)/', '$1' . $line, $src, 1 );
            if ( $new === null || $new === $src ) {
                return false;
            }
            @copy( $path, $path . '.olo-bak' );
            return (bool) @file_put_contents( $path, $new, LOCK_EX );
        }

        // off: rimuovi SOLO la riga marcata da noi.
        $new = preg_replace( '/\/\* ' . $marker . ' \*\/.*?\/\* \/' . $marker . ' \*\/\n?/s', '', $src );
        if ( $new !== null && $new !== $src ) {
            @copy( $path, $path . '.olo-bak' );
            return (bool) @file_put_contents( $path, $new, LOCK_EX );
        }
        return true;
    }

    /** Trova wp-config.php (in ABSPATH o un livello sopra, come fa WordPress). */
    private static function wp_config_path() {
        if ( file_exists( ABSPATH . 'wp-config.php' ) ) {
            return ABSPATH . 'wp-config.php';
        }
        $parent = dirname( ABSPATH ) . '/wp-config.php';
        if ( file_exists( $parent ) && ! file_exists( dirname( ABSPATH ) . '/wp-settings.php' ) ) {
            return $parent;
        }
        return '';
    }

    /* ── configurazione letta dal drop-in ──────────────────── */

    public static function write_config( $enabled ) {
        $dir = self::cache_dir();
        if ( ! is_dir( $dir ) && ! wp_mkdir_p( $dir ) ) {
            return false;
        }

        $o         = get_option( self::OPT, [] );
        $ttl_hours = isset( $o['full_page_ttl'] ) ? max( 1, (int) $o['full_page_ttl'] ) : 8;

        $exclude = [];
        if ( ! empty( $o['full_page_exclude'] ) ) {
            foreach ( preg_split( '/[\r\n]+/', (string) $o['full_page_exclude'] ) as $line ) {
                $line = trim( $line );
                if ( $line !== '' ) {
                    $exclude[] = $line;
                }
            }
        }

        $salt = get_option( self::SALT_OPT );
        if ( ! $salt ) {
            $salt = wp_generate_password( 24, false );
            update_option( self::SALT_OPT, $salt, false );
        }

        $cfg = [
            'enabled'     => (bool) $enabled,
            'ttl'         => $ttl_hours * HOUR_IN_SECONDS,
            'vary_device' => true,
            'exclude_uri' => $exclude,
            'salt'        => (string) $salt,
        ];

        if ( ! file_exists( $dir . 'index.html' ) ) {
            @file_put_contents( $dir . 'index.html', '' );
        }

        $php = "<?php\n// Generato da Olo_FullPage_Cache: non modificare a mano.\nreturn " . var_export( $cfg, true ) . ";\n";

        return (bool) @file_put_contents( self::config_path(), $php, LOCK_EX );
    }

    /* ── invalidazione ─────────────────────────────────────── */

    public static function purge_all() {
        $dir = self::cache_dir();
        if ( ! is_dir( $dir ) ) {
            return 0;
        }
        $n = 0;
        foreach ( (array) glob( $dir . '*.html' ) as $f ) {
            if ( @unlink( $f ) ) {
                $n++;
            }
        }
        return $n;
    }

    public static function on_save_post( $post_id, $post ) {
        if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
            return;
        }
        if ( is_object( $post ) && isset( $post->post_status ) && $post->post_status === 'auto-draft' ) {
            return;
        }
        self::purge_all();
    }

    /* ── ciclo di vita del plugin ──────────────────────────── */

    public static function on_plugin_activate() {
        if ( self::is_enabled() ) {
            self::sync();
        }
    }

    public static function on_plugin_deactivate() {
        self::uninstall_dropin();
        self::ensure_wp_cache( false );
        self::write_config( false );
        self::purge_all();
    }
}
