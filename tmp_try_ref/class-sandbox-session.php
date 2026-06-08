<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Gestione cookie SID + tabella sessioni.
 * Singolo entry-point: Olo_Sandbox_Session::current() restituisce array
 * { sid, template_id, created_at, last_seen_at } o null se nessuna sessione attiva.
 */
class Olo_Sandbox_Session {

    /** Cache per-request (la sessione si chiama N volte per request). */
    private static $cache_loaded = false;
    private static $cache_session = null;

    /**
     * Restituisce la sessione corrente o null.
     * Se il cookie c'è e la session è in tabella → array.
     * Se il cookie c'è ma session scaduta/cancellata → null (e il caller
     * dovrà rigenerare il clone).
     * Se il cookie manca → null (verrà creato dal bootstrap).
     */
    public static function current() {
        if ( self::$cache_loaded ) {
            return self::$cache_session;
        }
        self::$cache_loaded = true;

        $sid = self::sid_from_cookie();
        if ( ! $sid ) {
            return null;
        }

        $row = self::fetch( $sid );
        if ( ! $row ) {
            return null;
        }

        // TTL: se inattivo da troppo tempo, considera scaduta
        $ttl = Olo_Sandbox_Config::session_ttl();
        $last_seen = strtotime( $row['last_seen_at'] . ' UTC' );
        if ( ( time() - $last_seen ) > $ttl ) {
            // Marca per cleanup ma non bloccare: il caller rigenererà
            return null;
        }

        self::$cache_session = $row;
        return $row;
    }

    /**
     * Legge il SID dal cookie. Restituisce stringa o null.
     * Accetta solo UUID v4 well-formed (sanity check anti-tampering).
     */
    public static function sid_from_cookie() {
        if ( empty( $_COOKIE[ Olo_Sandbox_Config::COOKIE ] ) ) {
            return null;
        }
        $sid = (string) $_COOKIE[ Olo_Sandbox_Config::COOKIE ];
        if ( ! preg_match( '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $sid ) ) {
            return null;
        }
        return strtolower( $sid );
    }

    /**
     * Genera nuovo SID e setta il cookie. Restituisce il SID.
     * Va chiamato PRIMA che WP invii output (headers).
     */
    public static function generate_and_set_cookie() {
        $sid = wp_generate_uuid4();
        $expire = time() + ( Olo_Sandbox_Config::COOKIE_DAYS * DAY_IN_SECONDS );
        // Setcookie restituisce false se headers già inviati: in quel caso il SID
        // sarà comunque disponibile per la request corrente via $_COOKIE
        if ( ! headers_sent() ) {
            setcookie(
                Olo_Sandbox_Config::COOKIE,
                $sid,
                [
                    'expires'  => $expire,
                    'path'     => '/',
                    'secure'   => is_ssl(),
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]
            );
        }
        $_COOKIE[ Olo_Sandbox_Config::COOKIE ] = $sid;
        return $sid;
    }

    /**
     * Inserisce/aggiorna record sessione in tabella.
     */
    public static function save( $sid, $template_id ) {
        global $wpdb;
        $table = $wpdb->prefix . Olo_Sandbox_Config::SESSIONS_TABLE;
        $now = gmdate( 'Y-m-d H:i:s' );

        $existing = self::fetch( $sid );
        if ( $existing ) {
            $wpdb->update(
                $table,
                [ 'template_id' => $template_id, 'last_seen_at' => $now ],
                [ 'sid' => $sid ],
                [ '%d', '%s' ],
                [ '%s' ]
            );
        } else {
            $wpdb->insert(
                $table,
                [
                    'sid'           => $sid,
                    'template_id'   => $template_id,
                    'created_at'    => $now,
                    'last_seen_at'  => $now,
                ],
                [ '%s', '%d', '%s', '%s' ]
            );
        }
        // Invalida cache per-request
        self::$cache_loaded = false;
        self::$cache_session = null;
    }

    /** Aggiorna last_seen_at per il SID corrente. Throttle a 5 min per non spammare write. */
    public static function touch( $sid ) {
        global $wpdb;
        $table = $wpdb->prefix . Olo_Sandbox_Config::SESSIONS_TABLE;
        $now = gmdate( 'Y-m-d H:i:s' );
        $threshold = gmdate( 'Y-m-d H:i:s', time() - 5 * MINUTE_IN_SECONDS );

        $wpdb->query( $wpdb->prepare(
            "UPDATE $table SET last_seen_at = %s WHERE sid = %s AND last_seen_at < %s",
            $now, $sid, $threshold
        ) );
    }

    /** Recupera riga sessione per SID. */
    public static function fetch( $sid ) {
        global $wpdb;
        $table = $wpdb->prefix . Olo_Sandbox_Config::SESSIONS_TABLE;
        $row = $wpdb->get_row( $wpdb->prepare(
            "SELECT sid, template_id, created_at, last_seen_at FROM $table WHERE sid = %s",
            $sid
        ), ARRAY_A );
        return $row ?: null;
    }

    /** Cancella sessione e relativo template clone. */
    public static function destroy( $sid ) {
        global $wpdb;
        $row = self::fetch( $sid );
        if ( ! $row ) {
            return;
        }

        // Drop template clone (revisions cascade via Olo_Database)
        if ( class_exists( 'Olo_Database' ) && ! empty( $row['template_id'] ) ) {
            $db = Olo_Database::instance();
            $db->delete_template( (int) $row['template_id'] );
        }

        // Drop session row
        $table = $wpdb->prefix . Olo_Sandbox_Config::SESSIONS_TABLE;
        $wpdb->delete( $table, [ 'sid' => $sid ], [ '%s' ] );

        self::$cache_loaded = false;
        self::$cache_session = null;
    }

    /** Crea tabella sessioni (attivazione). */
    public static function create_table() {
        global $wpdb;
        $table = $wpdb->prefix . Olo_Sandbox_Config::SESSIONS_TABLE;
        $charset = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table (
            sid CHAR(36) NOT NULL,
            template_id BIGINT(20) UNSIGNED NOT NULL,
            created_at DATETIME NOT NULL,
            last_seen_at DATETIME NOT NULL,
            PRIMARY KEY (sid),
            KEY idx_last_seen (last_seen_at),
            KEY idx_template (template_id)
        ) $charset;";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    /** Drop tabella (disattivazione opzionale — non chiamata di default). */
    public static function drop_table() {
        global $wpdb;
        $table = $wpdb->prefix . Olo_Sandbox_Config::SESSIONS_TABLE;
        $wpdb->query( "DROP TABLE IF EXISTS $table" );
    }
}
