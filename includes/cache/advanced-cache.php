<?php
/**
 * Olobuild Full-Page Cache — drop-in advanced-cache.php
 *
 * Questo file viene copiato in wp-content/advanced-cache.php da Olo_FullPage_Cache.
 * WordPress lo include MOLTO presto (in wp-settings.php, se define('WP_CACHE', true)),
 * PRIMA di caricare plugin, tema e query: qui possiamo servire l'HTML già cachato e
 * uscire, azzerando il costo di rigenerazione della pagina (TTFB).
 *
 * NON usa API di WordPress (non è ancora caricato): solo $_SERVER, $_COOKIE e PHP core.
 * La configurazione (enabled, ttl, esclusioni) è letta da cache/olobuild/fpc-config.php,
 * scritto da Olo_FullPage_Cache. Se manca o enabled=false, il drop-in è inerte.
 *
 * Sicurezza-prima: in caso di dubbio NON si serve e NON si salva (passthrough a WP).
 */

if ( ! defined( 'ABSPATH' ) ) {
    return;
}

if ( ! defined( 'WP_CONTENT_DIR' ) ) {
    return; // contesto inatteso: non interferire.
}

/**
 * Esegue il tentativo di cache. Tutta la logica è incapsulata per non sporcare
 * lo scope globale; ritorna senza fare nulla quando la richiesta non è cachabile.
 */
( static function () {

    $dir = WP_CONTENT_DIR . '/cache/olobuild/';

    // --- Config (scritta da OLObuild). Assente o disattivata => inerte. ---
    $cfg = [
        'enabled'      => false,
        'ttl'          => 28800,          // 8 ore
        'exclude_uri'  => [],             // sottostringhe di REQUEST_URI da non cachare
        'vary_device'  => true,           // variante mobile/desktop separata
        'salt'         => defined( 'WP_CACHE_KEY_SALT' ) ? WP_CACHE_KEY_SALT : '',
    ];
    $cfg_file = $dir . 'fpc-config.php';
    if ( is_file( $cfg_file ) ) {
        $loaded = include $cfg_file;
        if ( is_array( $loaded ) ) {
            $cfg = array_merge( $cfg, $loaded );
        }
    }
    if ( empty( $cfg['enabled'] ) ) {
        return;
    }

    // --- Solo GET/HEAD: niente POST/PUT/DELETE (form, checkout, REST mutanti). ---
    $method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) ) : 'GET';
    if ( $method !== 'GET' && $method !== 'HEAD' ) {
        return;
    }

    $uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';

    // --- Path mai cachabili (admin, login, cron, API, feed, e pagine WooCommerce note). ---
    $blocked = [
        '/wp-admin', '/wp-login.php', '/wp-cron.php', '/xmlrpc.php', '/wp-json',
        '/feed', '/sitemap', '.xml', '.txt',
        '/cart', '/checkout', '/my-account',          // WooCommerce (slug EN)
        '/carrello', '/pagamento', '/account', '/mio-account', // slug IT comuni
    ];
    foreach ( (array) $cfg['exclude_uri'] as $extra ) {
        if ( $extra !== '' ) {
            $blocked[] = $extra;
        }
    }
    $path_only = (string) strtok( $uri, '?' );
    foreach ( $blocked as $needle ) {
        if ( $needle !== '' && stripos( $path_only, $needle ) !== false ) {
            return;
        }
    }

    // --- Query string: si cacha solo l'URL "pulito". I parametri di tracking
    //     vengono ignorati (stessa pagina); qualsiasi altro parametro => no cache. ---
    $qs = isset( $_SERVER['QUERY_STRING'] ) ? sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ) ) : '';
    if ( $qs !== '' ) {
        parse_str( $qs, $params );
        $tracking = [ 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'fbclid', 'gad_source', 'mc_cid', 'mc_eid', '_ga' ];
        foreach ( $tracking as $t ) {
            unset( $params[ $t ] );
        }
        if ( ! empty( $params ) ) {
            return; // parametri significativi: contenuto potenzialmente diverso.
        }
    }

    // --- Cookie che indicano una sessione personale: mai servire/salvare. ---
    // drop-in pre-bootstrap (WP_CACHE): si leggono solo i NOMI dei cookie per
    // decidere il passthrough; nessuna API nonce disponibile e nessuna elaborazione
    // di form. I nomi vengono sanificati prima del confronto.
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- drop-in pre-bootstrap (WP_CACHE): nessuna API nonce disponibile, non è elaborazione di form; si iterano solo i NOMI dei cookie per il passthrough e ogni nome è sanificato sotto.
    foreach ( array_keys( $_COOKIE ) as $ck ) {
        $ck = sanitize_text_field( wp_unslash( $ck ) );
        if (
            strpos( $ck, 'wordpress_logged_in' ) === 0 ||
            strpos( $ck, 'comment_author' ) === 0 ||
            strpos( $ck, 'wp-postpass' ) === 0 ||
            strpos( $ck, 'woocommerce_items_in_cart' ) === 0 ||
            strpos( $ck, 'woocommerce_cart_hash' ) === 0 ||
            strpos( $ck, 'wp_woocommerce_session' ) === 0
        ) {
            return;
        }
    }

    // --- Chiave di cache: salt + schema + host + path + variante device. ---
    $https_val = isset( $_SERVER['HTTPS'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTPS'] ) ) : '';
    $xfp_val   = isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) ) : '';
    $https     = ( $https_val !== '' && $https_val !== 'off' ) || ( $xfp_val === 'https' );
    $scheme    = $https ? 'https' : 'http';
    $host      = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';

    $device = '';
    if ( ! empty( $cfg['vary_device'] ) ) {
        $ua     = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
        $device = preg_match( '/Mobile|Android|iPhone|iPod|Windows Phone|BlackBerry|Opera Mini|IEMobile/i', $ua ) ? 'm' : 'd';
    }

    // Variante per lingua: OLObuild può servire contenuti diversi in base al
    // cookie olo_lang. Includerlo nella chiave evita di servire la lingua sbagliata
    // (su sito monolingua resta un'unica variante).
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- drop-in pre-bootstrap (WP_CACHE): nessuna API nonce disponibile, non è elaborazione di form; lettura read-only del cookie lingua solo per la variante di chiave cache; valore sanificato e validato (whitelist [a-z]{2}).
    $olo_lang = isset( $_COOKIE['olo_lang'] ) ? sanitize_key( wp_unslash( $_COOKIE['olo_lang'] ) ) : '';
    $lang     = preg_match( '/^[a-z]{2}$/', $olo_lang ) ? $olo_lang : '';

    $key  = md5( $cfg['salt'] . '|' . $scheme . '://' . $host . $path_only . '|' . $device . '|' . $lang );
    $file = $dir . $key . '.html';

    // --- HIT: file presente e non scaduto => servi ed esci, niente WordPress. ---
    if ( is_file( $file ) ) {
        $age = time() - (int) @filemtime( $file );
        if ( $age >= 0 && $age < (int) $cfg['ttl'] ) {
            header( 'Content-Type: text/html; charset=UTF-8' );
            header( 'X-Olo-Cache: HIT' );
            header( 'X-Olo-Cache-Age: ' . $age );
            if ( $method === 'HEAD' ) {
                exit;
            }
            // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile -- full-page cache drop-in: runs before WordPress is loaded (WP_CACHE fast path), WP_Filesystem unavailable; direct I/O required for TTFB
            readfile( $file );
            echo "\n<!-- Olobuild full-page cache: HIT (age " . (int) $age . "s) -->";
            exit;
        }
    }

    // --- MISS: bufferizza l'output e, a fine richiesta, salvalo se cachabile. ---
    if ( $method === 'HEAD' ) {
        return; // non generiamo cache da una HEAD.
    }

    $GLOBALS['olo_fpc_ctx'] = [ 'dir' => $dir, 'file' => $file ];

    ob_start( static function ( $html ) {
        $ctx = isset( $GLOBALS['olo_fpc_ctx'] ) ? $GLOBALS['olo_fpc_ctx'] : null;
        if ( ! $ctx || $html === '' ) {
            return $html;
        }

        // Non cachare se: status non 200, un plugin/Woo ha chiesto di non cachare,
        // la risposta ha settato cookie di sessione, o non è una pagina HTML completa.
        $code = function_exists( 'http_response_code' ) ? (int) http_response_code() : 200;
        if ( $code !== 200 ) {
            return $html;
        }
        if ( defined( 'DONOTCACHEPAGE' ) && DONOTCACHEPAGE ) {
            return $html; // WooCommerce la imposta su cart/checkout/account, e altri plugin.
        }

        $is_html = false;
        foreach ( headers_list() as $h ) {
            $hl = strtolower( $h );
            if ( strpos( $hl, 'content-type:' ) === 0 ) {
                $is_html = ( strpos( $hl, 'text/html' ) !== false );
            }
            if ( strpos( $hl, 'set-cookie:' ) === 0 && preg_match( '/(wordpress_logged_in|woocommerce_|wp_woocommerce_session|comment_author|wp-postpass)/i', $h ) ) {
                return $html; // la richiesta ha aperto una sessione: non è cachabile.
            }
        }
        if ( ! $is_html ) {
            return $html;
        }
        if ( stripos( $html, '</html>' ) === false ) {
            return $html; // risposta parziale / non una pagina completa.
        }

        // Scrittura atomica (file temporaneo + rename) per non servire mai un file mezzo scritto.
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_mkdir -- drop-in pre-bootstrap (WP_CACHE), WP_Filesystem/wp_mkdir_p unavailable here
        if ( is_dir( $ctx['dir'] ) || @mkdir( $ctx['dir'], 0755, true ) || is_dir( $ctx['dir'] ) ) {
            $tmp = $ctx['file'] . '.' . getmypid() . '.tmp';
            if ( false !== @file_put_contents( $tmp, $html, LOCK_EX ) ) {
                // phpcs:ignore WordPress.WP.AlternativeFunctions.rename_rename -- atomic cache write in the drop-in (pre-bootstrap); WP_Filesystem unavailable
                @rename( $tmp, $ctx['file'] );
            }
        }

        return $html . "\n<!-- Olobuild full-page cache: MISS (salvato) -->";
    } );
} )();
