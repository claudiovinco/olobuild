<?php
/**
 * Olo_Security_Hardening — protezioni opzionali a livello di sito (parte di OLOsecurity).
 *
 * Tutto è OPT-IN e disattivato di default: attivare nulla cambia il comportamento del
 * sito finché l'amministratore non lo sceglie da OLOsecurity → Impostazioni.
 *
 *  - Security header (X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy)
 *  - Nascondi la versione di WordPress (meta generator / query string)
 *  - Disabilita XML-RPC
 *  - CSP in modalità REPORT-ONLY: non blocca nulla, ma raccoglie le violazioni via un
 *    endpoint REST dedicato, così l'admin scopre quali risorse esterne carica il sito
 *    prima di valutare una policy in enforce.
 *
 * Impostazioni: option olo_sec_hardening. Report CSP: option olo_sec_csp_reports.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Security_Hardening {

    const OPT     = 'olo_sec_hardening';
    const OPT_CSP = 'olo_sec_csp_reports';
    const CSP_MAX = 100; // numero massimo di violazioni distinte memorizzate

    /**
     * Domini essenziali per la CSP. Lista CORTA di proposito: un header CSP troppo grande
     * (>4 KB con gli altri header) viene rifiutato dai reverse proxy con proxy_buffer_size
     * di default (nginx/openresty 4 KB) → 502 Bad Gateway. I domini specifici del sito si
     * aggiungono via "domini extra" (csp_extra), non allungando questa lista.
     */
    private static $csp_hosts = [
        'googletagmanager.com', 'google-analytics.com', 'googleapis.com', 'gstatic.com',
        'doubleclick.net', 'cloudflare.com', 'jsdelivr.net', 'cdnjs.cloudflare.com',
        'facebook.net', 'stripe.com', 'paypal.com', 'hotjar.com', 'clarity.ms',
    ];

    public static function defaults() {
        return [
            'headers'         => 0,
            'xfo'             => 1,
            'nosniff'         => 1,
            'referrer'        => 1,
            'permissions'     => 1,
            'hide_wp_version' => 0,
            'disable_xmlrpc'  => 0,
            'csp'             => 0,
            'csp_extra'       => '',
        ];
    }

    public static function get_settings() {
        $s = get_option( self::OPT, [] );
        return wp_parse_args( is_array( $s ) ? $s : [], self::defaults() );
    }

    public static function init() {
        $s = self::get_settings();

        if ( ! empty( $s['headers'] ) || ! empty( $s['csp'] ) ) {
            add_action( 'send_headers', [ __CLASS__, 'emit_headers' ] );
        }
        if ( ! empty( $s['csp'] ) ) {
            add_action( 'rest_api_init', [ __CLASS__, 'register_csp_route' ] );
        }
        if ( ! empty( $s['hide_wp_version'] ) ) {
            remove_action( 'wp_head', 'wp_generator' );
            add_filter( 'the_generator', '__return_empty_string' );
            add_filter( 'style_loader_src', [ __CLASS__, 'strip_version_qs' ], 9999 );
            add_filter( 'script_loader_src', [ __CLASS__, 'strip_version_qs' ], 9999 );
        }
        if ( ! empty( $s['disable_xmlrpc'] ) ) {
            add_filter( 'xmlrpc_enabled', '__return_false' );
            add_filter( 'xmlrpc_methods', '__return_empty_array' );
        }
    }

    /** Header di sicurezza calcolati dai toggle. */
    public static function build_headers() {
        $s   = self::get_settings();
        $out = [];
        if ( empty( $s['headers'] ) ) {
            return $out;
        }
        if ( ! empty( $s['xfo'] ) ) {
            $out['X-Frame-Options'] = 'SAMEORIGIN';
        }
        if ( ! empty( $s['nosniff'] ) ) {
            $out['X-Content-Type-Options'] = 'nosniff';
        }
        if ( ! empty( $s['referrer'] ) ) {
            $out['Referrer-Policy'] = 'strict-origin-when-cross-origin';
        }
        if ( ! empty( $s['permissions'] ) ) {
            $out['Permissions-Policy'] = 'geolocation=(), microphone=(), camera=()';
        }
        return $out;
    }

    public static function emit_headers() {
        if ( is_admin() ) {
            return;
        }
        foreach ( self::build_headers() as $name => $value ) {
            header( $name . ': ' . $value );
        }
        $s = self::get_settings();
        if ( ! empty( $s['csp'] ) ) {
            // SEMPRE report-only: non blocca, raccoglie soltanto.
            header( 'Content-Security-Policy-Report-Only: ' . self::build_csp() );
        }
    }

    /**
     * Costruisce la policy CSP report-only. Permissiva sugli inline (unsafe-inline/eval)
     * per non sommergere di rumore: l'obiettivo è scoprire i DOMINI esterni inattesi,
     * non i singoli script inline. I domini fidati riusano la whitelist del Sentinel.
     */
    public static function build_csp() {
        $src = [];
        foreach ( self::$csp_hosts as $h ) {
            $src[] = 'https://*.' . $h;
        }
        // Host extra dall'admin (qui si aggiungono i domini specifici del sito).
        $s     = self::get_settings();
        $extra = array_filter( array_map( 'trim', preg_split( '/[\s,]+/', (string) ( $s['csp_extra'] ?? '' ) ) ) );
        foreach ( $extra as $e ) {
            if ( preg_match( '/^[a-z0-9.\-\*]+$/i', $e ) ) {
                $src[] = 'https://' . $e;
            }
        }
        $trusted = implode( ' ', array_unique( $src ) );
        $report  = esc_url_raw( rest_url( 'olo/v1/csp-report' ) );

        // Header compatto (~1 KB) per restare sotto il proxy_buffer_size dei reverse proxy.
        $policy = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' " . $trusted,
            "connect-src 'self' " . $trusted,
            "style-src 'self' 'unsafe-inline' https://*.googleapis.com",
            "img-src 'self' data: blob: https:",
            "font-src 'self' data: https://*.gstatic.com",
            "frame-src 'self' https://*.youtube.com https://*.vimeo.com https://*.google.com https://*.facebook.com",
            "object-src 'none'",
            "base-uri 'self'",
            'report-uri ' . $report,
        ];
        return implode( '; ', $policy );
    }

    /** Endpoint pubblico che riceve i report CSP dei browser. */
    public static function register_csp_route() {
        register_rest_route( 'olo/v1', '/csp-report', [
            'methods'             => 'POST',
            'callback'            => [ __CLASS__, 'receive_csp_report' ],
            'permission_callback' => '__return_true',
        ] );
    }

    public static function receive_csp_report( $request ) {
        $body = (string) $request->get_body();
        if ( strlen( $body ) > 16384 ) {
            $body = substr( $body, 0, 16384 );
        }
        $data = json_decode( $body, true );
        $r    = null;
        if ( is_array( $data ) ) {
            if ( isset( $data['csp-report'] ) && is_array( $data['csp-report'] ) ) {
                $r = $data['csp-report'];
            } elseif ( isset( $data[0]['body'] ) && is_array( $data[0]['body'] ) ) {
                $r = $data[0]['body']; // formato Reporting API
            }
        }
        if ( ! is_array( $r ) ) {
            return new WP_REST_Response( null, 204 );
        }

        $directive = sanitize_text_field( substr( (string) ( $r['effective-directive'] ?? $r['violated-directive'] ?? '' ), 0, 40 ) );
        $blocked   = (string) ( $r['blocked-uri'] ?? '' );
        $host      = wp_parse_url( $blocked, PHP_URL_HOST );
        if ( ! $host ) {
            $host = $blocked !== '' ? $blocked : 'inline';
        }
        $host = sanitize_text_field( substr( $host, 0, 120 ) );
        if ( $directive === '' ) {
            return new WP_REST_Response( null, 204 );
        }

        $key     = $directive . '|' . $host;
        $reports = get_option( self::OPT_CSP, [] );
        if ( ! is_array( $reports ) ) {
            $reports = [];
        }
        if ( isset( $reports[ $key ] ) ) {
            $reports[ $key ]['count'] = min( 1000000, (int) ( $reports[ $key ]['count'] ?? 0 ) + 1 );
            $reports[ $key ]['last']  = time();
        } elseif ( count( $reports ) < self::CSP_MAX ) {
            $reports[ $key ] = [ 'directive' => $directive, 'host' => $host, 'count' => 1, 'last' => time() ];
        }
        update_option( self::OPT_CSP, $reports, false );

        return new WP_REST_Response( null, 204 );
    }

    public static function get_reports() {
        $r = get_option( self::OPT_CSP, [] );
        return is_array( $r ) ? $r : [];
    }

    public static function clear_reports() {
        delete_option( self::OPT_CSP );
    }

    /** Rimuove ?ver= dagli asset. */
    public static function strip_version_qs( $src ) {
        if ( $src && strpos( $src, 'ver=' ) !== false ) {
            $src = remove_query_arg( 'ver', $src );
        }
        return $src;
    }

    public static function save_settings( $post ) {
        $s = [
            'headers'         => empty( $post['olo_hard_headers'] ) ? 0 : 1,
            'xfo'             => empty( $post['olo_hard_xfo'] ) ? 0 : 1,
            'nosniff'         => empty( $post['olo_hard_nosniff'] ) ? 0 : 1,
            'referrer'        => empty( $post['olo_hard_referrer'] ) ? 0 : 1,
            'permissions'     => empty( $post['olo_hard_permissions'] ) ? 0 : 1,
            'hide_wp_version' => empty( $post['olo_hard_hidever'] ) ? 0 : 1,
            'disable_xmlrpc'  => empty( $post['olo_hard_xmlrpc'] ) ? 0 : 1,
            'csp'             => empty( $post['olo_hard_csp'] ) ? 0 : 1,
            'csp_extra'       => sanitize_text_field( wp_unslash( $post['olo_hard_csp_extra'] ?? '' ) ),
        ];
        update_option( self::OPT, $s, false );
    }

    public static function render_settings_fields() {
        $s = self::get_settings();
        ?>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><?php esc_html_e( 'Security header HTTP', 'olobuild' ); ?></th>
                <td>
                    <label style="display:block;margin-bottom:6px">
                        <input type="checkbox" name="olo_hard_headers" value="1" <?php checked( ! empty( $s['headers'] ) ); ?>>
                        <strong><?php esc_html_e( 'Attiva gli header di sicurezza sul frontend', 'olobuild' ); ?></strong>
                    </label>
                    <label style="display:block;margin-left:22px"><input type="checkbox" name="olo_hard_xfo" value="1" <?php checked( ! empty( $s['xfo'] ) ); ?>> X-Frame-Options: SAMEORIGIN <span style="color:#888"><?php esc_html_e( '(anti-clickjacking)', 'olobuild' ); ?></span></label>
                    <label style="display:block;margin-left:22px"><input type="checkbox" name="olo_hard_nosniff" value="1" <?php checked( ! empty( $s['nosniff'] ) ); ?>> X-Content-Type-Options: nosniff</label>
                    <label style="display:block;margin-left:22px"><input type="checkbox" name="olo_hard_referrer" value="1" <?php checked( ! empty( $s['referrer'] ) ); ?>> Referrer-Policy: strict-origin-when-cross-origin</label>
                    <label style="display:block;margin-left:22px"><input type="checkbox" name="olo_hard_permissions" value="1" <?php checked( ! empty( $s['permissions'] ) ); ?>> Permissions-Policy <span style="color:#888"><?php esc_html_e( '(blocca geolocalizzazione/microfono/camera)', 'olobuild' ); ?></span></label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Riduci impronta', 'olobuild' ); ?></th>
                <td>
                    <label style="display:block;margin-bottom:6px"><input type="checkbox" name="olo_hard_hidever" value="1" <?php checked( ! empty( $s['hide_wp_version'] ) ); ?>> <?php esc_html_e( 'Nascondi la versione di WordPress (meta generator e ?ver= negli asset)', 'olobuild' ); ?></label>
                    <label style="display:block"><input type="checkbox" name="olo_hard_xmlrpc" value="1" <?php checked( ! empty( $s['disable_xmlrpc'] ) ); ?>> <?php esc_html_e( 'Disabilita XML-RPC (brute-force e pingback DDoS)', 'olobuild' ); ?></label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'CSP (report-only)', 'olobuild' ); ?></th>
                <td>
                    <label style="display:block;margin-bottom:6px">
                        <input type="checkbox" name="olo_hard_csp" value="1" <?php checked( ! empty( $s['csp'] ) ); ?>>
                        <strong><?php esc_html_e( 'Attiva la Content-Security-Policy in sola segnalazione', 'olobuild' ); ?></strong>
                    </label>
                    <p style="color:#888;margin:4px 0 8px"><?php esc_html_e( 'Non blocca nulla: registra solo le risorse che verrebbero bloccate, così puoi vedere (qui sotto) cosa servirebbe consentire prima di un eventuale blocco reale.', 'olobuild' ); ?></p>
                    <input type="text" name="olo_hard_csp_extra" value="<?php echo esc_attr( $s['csp_extra'] ?? '' ); ?>" class="regular-text" placeholder="cdn.miosito.com, widget.altro.com">
                    <p style="color:#888;margin:4px 0 0"><?php esc_html_e( 'Domini extra da considerare attendibili (separati da virgola).', 'olobuild' ); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }

    /** Sezione "Report CSP" (fuori dal form delle impostazioni). */
    public static function render_csp_reports() {
        $reports = self::get_reports();
        // Ordina per conteggio decrescente.
        uasort( $reports, function ( $a, $b ) {
            return (int) ( $b['count'] ?? 0 ) <=> (int) ( $a['count'] ?? 0 );
        } );
        ?>
        <hr style="margin:22px 0">
        <h3><?php esc_html_e( 'Report CSP raccolti', 'olobuild' ); ?></h3>
        <?php if ( empty( $reports ) ) : ?>
            <p style="color:#666"><?php esc_html_e( 'Nessuna violazione registrata. Attiva la CSP report-only, naviga il sito per qualche giorno, poi torna qui: vedrai i domini/risorse che una CSP in blocco fermerebbe.', 'olobuild' ); ?></p>
        <?php else : ?>
            <table class="widefat striped" style="max-width:900px">
                <thead><tr>
                    <th><?php esc_html_e( 'Direttiva', 'olobuild' ); ?></th>
                    <th><?php esc_html_e( 'Risorsa bloccata (host)', 'olobuild' ); ?></th>
                    <th><?php esc_html_e( 'Occorrenze', 'olobuild' ); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ( $reports as $r ) : ?>
                    <tr>
                        <td><code style="font-size:11px"><?php echo esc_html( $r['directive'] ?? '' ); ?></code></td>
                        <td><code style="font-size:11px;word-break:break-all"><?php echo esc_html( $r['host'] ?? '' ); ?></code></td>
                        <td><?php echo (int) ( $r['count'] ?? 0 ); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <p style="color:#888;font-size:12px"><?php esc_html_e( 'Suggerimento: gli host che riconosci come legittimi vanno aggiunti ai "domini extra" qui sopra; quelli che non riconosci meritano un controllo.', 'olobuild' ); ?></p>
            <form method="post" style="margin-top:8px">
                <?php wp_nonce_field( 'olo_sentinel' ); ?>
                <input type="hidden" name="olo_sentinel_action" value="clear_csp">
                <button type="submit" class="button"><?php esc_html_e( 'Svuota report CSP', 'olobuild' ); ?></button>
            </form>
        <?php endif; ?>
        <?php
    }
}
