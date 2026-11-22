<?php
/**
 * I temi del Pro (1.4.1347). OLObuild Free ha cento temi, scelti dall'utente il 22 nov 2026 e scritti in
 * assets/data/temi-free.json; ogni altro tema di assets/data/themes/ è del Pro. I temi portati dai plugin fratelli
 * (olobuild_external_theme_dirs) seguono il loro plugin e non sono né Free né Pro.
 *
 * Nello zip della Free (build_zip.py) di un tema del Pro resta solo theme.json, la scheda che il selettore mostra,
 * con «solo_scheda» e l'impronta delle pagine («pacchetto»). Le pagine si scaricano da olotheme.com (OLO Store,
 * POST /tema) quando si importa il tema, solo con una licenza che ha il modulo temi-pro (OLObooking lo porta): vanno
 * in wp-content/uploads/olobuild-temi/<tema>/ e restano lì finché l'impronta della scheda non cambia. I siti col
 * plugin intero (il treno, la copia di lavoro) hanno già tutte le pagine: per loro conta solo la licenza.
 *
 * Senza licenza col modulo il tema del Pro si vede nel selettore col lucchetto e «Passa al Pro»; chi chiama la
 * REST a mano riceve 403 olobuild_tema_pro.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

final class Olobuild_Temi_Pro {

    const MODULO   = 'temi-pro';
    const CARTELLA = 'olobuild-temi';
    const PREZZI   = 'https://olotheme.com/olobuild/prezzi/';
    const MAX_FILE = 4194304; // 4 MB per file: le pagine più grandi dei temi ne pesano poche decine

    /** @var array<string,int>|null */
    private static $free = null;

    /** Gli id dei temi della Free, come chiavi. Vuoto se l'elenco manca: allora nessun tema è del Pro. */
    public static function free() {
        if ( null === self::$free ) {
            $file = OLOBUILD_PATH . 'assets/data/temi-free.json';
            $d    = is_readable( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : null; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- file del plugin
            self::$free = is_array( $d ) && is_array( $d['free'] ?? null ) ? array_flip( array_map( 'strval', $d['free'] ) ) : [];
        }
        return self::$free;
    }

    /** La cartella è uno dei temi del plugin (assets/data/themes/)? */
    public static function del_plugin( $dir ) {
        $base = wp_normalize_path( OLOBUILD_PATH . 'assets/data/themes/' );
        return 0 === strpos( wp_normalize_path( (string) $dir ) . '/', $base );
    }

    /** L'edizione di un tema del plugin: 'free' o 'pro'; '' per i temi dei plugin fratelli o senza elenco. */
    public static function edizione( $id, $dir ) {
        $free = self::free();
        if ( ! $free || ! self::del_plugin( $dir ) ) return '';
        return isset( $free[ (string) $id ] ) ? 'free' : 'pro';
    }

    /** Questo sito può importare i temi del Pro? Builder attivo e licenza col modulo temi-pro. */
    public static function permesso() {
        if ( ! class_exists( 'Olobuild_Licenza' ) ) return false;
        return Olobuild_Licenza::builder_attivo() && in_array( self::MODULO, array_map( 'strval', (array) Olobuild_Licenza::moduli() ), true );
    }

    public static function errore_pro() {
        return new WP_Error( 'olobuild_tema_pro', __( 'Questo tema fa parte di OLObuild Pro: si importa con la licenza Pro.', 'olobuild' ), [ 'status' => 403, 'pro_url' => self::PREZZI ] );
    }

    /* ─── Le pagine scaricate ─────────────────────────────────────────────── */

    public static function base() {
        $u = wp_upload_dir( null, false );
        return trailingslashit( wp_normalize_path( (string) $u['basedir'] ) ) . self::CARTELLA;
    }

    public static function cartella( $id ) {
        return self::base() . '/' . sanitize_key( (string) $id );
    }

    /**
     * La cartella con le pagine già scaricate di un tema del Pro, se sono quelle della scheda che il plugin ha
     * adesso (stessa impronta); '' se vanno scaricate.
     */
    public static function scaricata( $id, array $scheda ) {
        $dir = self::cartella( $id );
        if ( ! is_readable( $dir . '/theme.json' ) ) return '';
        $vuole = (string) ( $scheda['pacchetto'] ?? '' );
        $ha    = is_readable( $dir . '/.impronta' ) ? trim( (string) file_get_contents( $dir . '/.impronta' ) ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- file locale
        return ( '' === $vuole || $ha === $vuole ) ? $dir : '';
    }

    /**
     * Scarica da olotheme.com le pagine di un tema del Pro e le mette nella sua cartella; torna la cartella o un
     * WP_Error. Lo zip deve contenere solo <tema>/<nome>.json: qualunque altro nome lo fa rifiutare tutto.
     */
    public static function scarica( $id, array $scheda ) {
        $id = sanitize_key( (string) $id );
        if ( ! self::permesso() ) return self::errore_pro();
        if ( ! class_exists( 'ZipArchive' ) ) {
            return new WP_Error( 'olobuild_tema_pro_zip', __( 'Per scaricare i temi del Pro il server deve avere l’estensione PHP zip.', 'olobuild' ), [ 'status' => 500 ] );
        }
        $r    = wp_remote_post( Olobuild_Licenza::NEGOZIO . '/tema', [
            'timeout' => 60,
            'body'    => [ 'id' => $id, 'license_key' => Olobuild_Licenza::chiave(), 'site_url' => home_url(), 'version' => OLOBUILD_VERSION ],
        ] );
        $code = is_wp_error( $r ) ? 0 : (int) wp_remote_retrieve_response_code( $r );
        if ( 403 === $code ) return self::errore_pro();
        $body = is_wp_error( $r ) ? '' : (string) wp_remote_retrieve_body( $r );
        $rete = new WP_Error( 'olobuild_tema_pro_rete', __( 'Non è stato possibile scaricare il tema da olotheme.com: riprova tra poco.', 'olobuild' ), [ 'status' => 502 ] );
        if ( 200 !== $code || 0 !== strpos( $body, 'PK' ) ) return $rete;

        if ( ! function_exists( 'wp_tempnam' ) ) require_once ABSPATH . 'wp-admin/includes/file.php';
        $tmp = wp_tempnam( 'olobuild-tema-' . $id );
        file_put_contents( $tmp, $body ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- file temporaneo
        $file = [];
        $zip  = new ZipArchive();
        $ok   = true === $zip->open( $tmp );
        if ( $ok ) {
            for ( $i = 0; $i < $zip->numFiles; $i++ ) {
                $nome = (string) $zip->getNameIndex( $i );
                if ( '/' === substr( $nome, -1 ) ) continue;
                $st = $zip->statIndex( $i );
                if ( ! preg_match( '#^' . preg_quote( $id, '#' ) . '/([a-z0-9][a-z0-9._-]{0,90}\.json)$#', $nome, $m ) || ! $st || (int) $st['size'] > self::MAX_FILE ) {
                    $ok = false;
                    break;
                }
                $file[ $m[1] ] = (string) $zip->getFromIndex( $i );
            }
            $zip->close();
        }
        wp_delete_file( $tmp );
        if ( ! $ok || empty( $file['theme.json'] ) || ! is_array( json_decode( $file['theme.json'], true ) ) ) return $rete;

        $base = self::base();
        if ( ! wp_mkdir_p( $base ) ) return new WP_Error( 'olobuild_tema_pro_cartella', __( 'Non è stato possibile scrivere nella cartella dei caricamenti.', 'olobuild' ), [ 'status' => 500 ] );
        // Le pagine dei temi del Pro non si leggono dal browser.
        if ( ! file_exists( $base . '/index.php' ) ) file_put_contents( $base . '/index.php', "<?php\n// Silence is golden.\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
        if ( ! file_exists( $base . '/.htaccess' ) ) file_put_contents( $base . '/.htaccess', "Require all denied\nDeny from all\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents

        $dest  = self::cartella( $id );
        $nuova = $dest . '.nuova-' . wp_generate_password( 6, false, false );
        if ( ! wp_mkdir_p( $nuova ) ) return new WP_Error( 'olobuild_tema_pro_cartella', __( 'Non è stato possibile scrivere nella cartella dei caricamenti.', 'olobuild' ), [ 'status' => 500 ] );
        // I permessi di WordPress (0644/0755), qualunque sia la umask di chi importa (anche wp-cli da terminale).
        $pf = defined( 'FS_CHMOD_FILE' ) ? FS_CHMOD_FILE : 0644;
        $file['.impronta'] = (string) ( $scheda['pacchetto'] ?? '' );
        foreach ( $file as $n => $c ) {
            file_put_contents( $nuova . '/' . $n, $c ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
            @chmod( $nuova . '/' . $n, $pf ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_operations_chmod
        }
        @chmod( $nuova, defined( 'FS_CHMOD_DIR' ) ? FS_CHMOD_DIR : 0755 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_operations_chmod
        if ( is_dir( $dest ) ) self::rimuovi( $dest );
        if ( ! @rename( $nuova, $dest ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.rename_rename
            self::rimuovi( $nuova );
            return new WP_Error( 'olobuild_tema_pro_cartella', __( 'Non è stato possibile scrivere nella cartella dei caricamenti.', 'olobuild' ), [ 'status' => 500 ] );
        }
        return $dest;
    }

    /** Toglie una cartella di tema scaricata (piatta: solo file). */
    private static function rimuovi( $dir ) {
        foreach ( (array) @scandir( $dir ) as $n ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
            if ( '.' !== $n && '..' !== $n && is_file( $dir . '/' . $n ) ) wp_delete_file( $dir . '/' . $n );
        }
        @rmdir( $dir ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_operations_rmdir
    }
}
