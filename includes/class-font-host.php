<?php
/**
 * Olo_Font_Host — self-hosting dei Google Fonts.
 *
 * Scarica i file woff2 da Google una sola volta, li salva in
 * /uploads/olo-fonts/ e genera regole @font-face con URL locali.
 * Nessuna richiesta del visitatore a fonts.googleapis.com / fonts.gstatic.com
 * (conformità privacy/GDPR e linee guida wordpress.org).
 *
 * Se il download fallisce si ricade sui font di sistema: non viene MAI
 * lasciato un URL Google nel CSS servito al frontend.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Font_Host {

    const DIR = 'olo-fonts';
    const UA  = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    /**
     * Regole CSS @font-face self-hosted per le famiglie indicate.
     * Stringa vuota se non ci sono famiglie o se il download fallisce.
     *
     * @param array  $families Nomi famiglia (es. ['Inter','Poppins']).
     * @param string $weights  Pesi in formato css2 (default '300;400;500;600;700').
     * @return string
     */
    public static function get_font_face_css( $families, $weights = '300;400;500;600;700' ) {
        $families = array_values( array_unique( array_filter( array_map( 'trim', (array) $families ) ) ) );
        if ( empty( $families ) ) {
            return '';
        }

        $key    = 'olo_fonthost_' . md5( implode( '|', $families ) . '|' . $weights );
        $cached = get_transient( $key );
        if ( is_string( $cached ) ) {
            return $cached;
        }

        $css = self::build( $families, $weights );
        // L'esito vuoto (Google irraggiungibile) viene cachato solo per poco,
        // così i retry riprendono presto; l'esito valido dura un mese.
        set_transient( $key, $css, '' === $css ? 5 * MINUTE_IN_SECONDS : MONTH_IN_SECONDS );
        return $css;
    }

    /**
     * Scarica il CSS da Google, salva localmente i woff2 e riscrive gli URL.
     *
     * @return string CSS con URL locali, o '' in caso di errore.
     */
    private static function build( $families, $weights ) {
        $req = [];
        foreach ( $families as $f ) {
            $req[] = 'family=' . rawurlencode( $f ) . ':wght@' . $weights;
        }
        $url = 'https://fonts.googleapis.com/css2?' . implode( '&', $req ) . '&display=swap';

        // User-agent moderno per ottenere la variante woff2.
        $resp = wp_remote_get( $url, [ 'timeout' => 15, 'user-agent' => self::UA ] );
        if ( is_wp_error( $resp ) || 200 !== (int) wp_remote_retrieve_response_code( $resp ) ) {
            return '';
        }
        $remote_css = wp_remote_retrieve_body( $resp );
        if ( ! $remote_css ) {
            return '';
        }

        $upload = wp_upload_dir();
        if ( ! empty( $upload['error'] ) ) {
            return '';
        }
        $base_dir = trailingslashit( $upload['basedir'] ) . self::DIR;
        $base_url = trailingslashit( $upload['baseurl'] ) . self::DIR;
        if ( ! wp_mkdir_p( $base_dir ) ) {
            return '';
        }

        $failed = false;
        $css = preg_replace_callback(
            '#https://fonts\.gstatic\.com/[^)\'"\s]+#',
            function ( $m ) use ( $base_dir, $base_url, &$failed ) {
                $remote = $m[0];
                $ext    = pathinfo( (string) wp_parse_url( $remote, PHP_URL_PATH ), PATHINFO_EXTENSION );
                $name   = md5( $remote ) . ( $ext ? '.' . $ext : '.woff2' );
                $local  = $base_dir . '/' . $name;

                if ( ! file_exists( $local ) ) {
                    $r = wp_remote_get( $remote, [ 'timeout' => 15 ] );
                    if ( is_wp_error( $r ) || 200 !== (int) wp_remote_retrieve_response_code( $r ) ) {
                        $failed = true;
                        return $remote;
                    }
                    $bytes = wp_remote_retrieve_body( $r );
                    if ( ! $bytes || ! self::write( $local, $bytes ) ) {
                        $failed = true;
                        return $remote;
                    }
                }
                return $base_url . '/' . $name;
            },
            $remote_css
        );

        // Se anche un solo file non è stato salvato in locale, niente URL Google
        // residui: fallback completo ai font di sistema.
        if ( $failed || ! is_string( $css ) ) {
            return '';
        }
        return $css;
    }

    /**
     * Scrive un file binario in /uploads usando WP_Filesystem.
     */
    private static function write( $path, $bytes ) {
        global $wp_filesystem;
        if ( ! $wp_filesystem ) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            WP_Filesystem();
        }
        if ( $wp_filesystem ) {
            return (bool) $wp_filesystem->put_contents( $path, $bytes, FS_CHMOD_FILE );
        }
        return false;
    }

    /**
     * Svuota la cache dei CSS generati (i woff2 su disco restano e si riusano).
     */
    public static function flush() {
        global $wpdb;
        $wpdb->query(
            "DELETE FROM {$wpdb->options}
             WHERE option_name LIKE '\_transient\_olo\_fonthost\_%'
                OR option_name LIKE '\_transient\_timeout\_olo\_fonthost\_%'"
        );
    }
}
