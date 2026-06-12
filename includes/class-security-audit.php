<?php
/**
 * Olo_Security_Audit — registro attività di OLOsecurity (chi / cosa / quando).
 *
 * Traccia gli eventi rilevanti per la sicurezza in una tabella dedicata:
 * login (riusciti e falliti), creazione utenti e promozioni di ruolo, attivazione
 * di plugin/temi, modifiche al codice personalizzato e alle opzioni critiche,
 * azioni di OLOsecurity (quarantena, ripristino).
 *
 * Quando il resto di OLOsecurity segnala "qualcosa è cambiato", il registro risponde
 * a "chi è stato e quando".
 *
 * Tabella: {prefix}olo_security_log
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Security_Audit {

    const TABLE = 'olo_security_log';
    const DB_VERSION = '1';

    /** Crea la tabella se manca (utile sui siti dove il plugin è già attivo e l'activation hook non rigira). */
    public static function maybe_install() {
        if ( get_option( 'olo_sec_db_version' ) !== self::DB_VERSION ) {
            self::create_table();
            update_option( 'olo_sec_db_version', self::DB_VERSION, false );
        }
    }

    public static function table_name() {
        global $wpdb;
        return $wpdb->prefix . self::TABLE;
    }

    public static function create_table() {
        global $wpdb;
        $table   = self::table_name();
        $charset = $wpdb->get_charset_collate();
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $sql = "CREATE TABLE $table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at DATETIME NOT NULL,
            event_type VARCHAR(40) NOT NULL DEFAULT '',
            severity VARCHAR(10) NOT NULL DEFAULT 'info',
            user_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
            user_login VARCHAR(80) NOT NULL DEFAULT '',
            ip VARCHAR(45) NOT NULL DEFAULT '',
            message TEXT NOT NULL,
            PRIMARY KEY  (id),
            KEY created_at (created_at),
            KEY event_type (event_type)
        ) $charset;";
        dbDelta( $sql );
    }

    public static function init() {
        // Autenticazione.
        add_action( 'wp_login',        [ __CLASS__, 'on_login' ], 10, 2 );
        add_action( 'wp_login_failed', [ __CLASS__, 'on_login_failed' ], 10, 1 );

        // Utenti e ruoli.
        add_action( 'user_register', [ __CLASS__, 'on_user_register' ], 10, 1 );
        add_action( 'set_user_role', [ __CLASS__, 'on_set_user_role' ], 10, 3 );

        // Plugin e temi.
        add_action( 'activated_plugin',   [ __CLASS__, 'on_plugin_activated' ], 10, 1 );
        add_action( 'deactivated_plugin', [ __CLASS__, 'on_plugin_deactivated' ], 10, 1 );
        add_action( 'switch_theme',       [ __CLASS__, 'on_switch_theme' ], 10, 1 );

        // Opzioni critiche (WordPress).
        foreach ( [ 'siteurl', 'home', 'admin_email', 'users_can_register', 'default_role', 'template', 'stylesheet' ] as $opt ) {
            add_action( 'update_option_' . $opt, [ __CLASS__, 'on_core_option' ], 10, 3 );
        }
        // Codice personalizzato Olobuild.
        foreach ( [ 'head', 'body', 'footer' ] as $slot ) {
            add_action( 'update_option_olo_custom_code_' . $slot, [ __CLASS__, 'on_custom_code' ], 10, 0 );
            add_action( 'add_option_olo_custom_code_' . $slot,    [ __CLASS__, 'on_custom_code' ], 10, 0 );
        }
        // Header/footer attivi.
        foreach ( [ 'olo_active_header', 'olo_active_footer' ] as $opt ) {
            add_action( 'update_option_' . $opt, [ __CLASS__, 'on_active_layout' ], 10, 3 );
        }
    }

    // ── Logger ───────────────────────────────────────────────────────────

    /**
     * Registra un evento.
     *
     * @param string $type     event_type breve (es. 'login', 'option_change').
     * @param string $message  Descrizione leggibile.
     * @param string $severity 'info' | 'warn' | 'high'.
     * @param array  $args     ['user_id'=>int,'user_login'=>string] override opzionali.
     */
    public static function log( $type, $message, $severity = 'info', $args = [] ) {
        global $wpdb;

        $user_id    = $args['user_id']    ?? get_current_user_id();
        $user_login = $args['user_login'] ?? '';
        if ( $user_login === '' && $user_id ) {
            $u = get_userdata( $user_id );
            $user_login = $u ? $u->user_login : '';
        }

        $wpdb->insert(
            self::table_name(),
            [
                'created_at' => current_time( 'mysql' ),
                'event_type' => substr( (string) $type, 0, 40 ),
                'severity'   => in_array( $severity, [ 'info', 'warn', 'high' ], true ) ? $severity : 'info',
                'user_id'    => (int) $user_id,
                'user_login' => substr( (string) $user_login, 0, 80 ),
                'ip'         => self::client_ip(),
                'message'    => (string) $message,
            ],
            [ '%s', '%s', '%s', '%d', '%s', '%s', '%s' ]
        );
    }

    // ── Event handlers ─────────────────────────────────────────────────────

    public static function on_login( $user_login, $user = null ) {
        $uid = ( $user instanceof WP_User ) ? $user->ID : 0;
        self::log( 'login', sprintf( __( 'Accesso riuscito: %s', 'olobuild' ), $user_login ), 'info', [ 'user_id' => $uid, 'user_login' => $user_login ] );
    }

    public static function on_login_failed( $username ) {
        self::log( 'login_failed', sprintf( __( 'Tentativo di accesso fallito per: %s', 'olobuild' ), $username ), 'warn', [ 'user_id' => 0, 'user_login' => (string) $username ] );
    }

    public static function on_user_register( $user_id ) {
        $u    = get_userdata( $user_id );
        $is_admin = $u && in_array( 'administrator', (array) $u->roles, true );
        self::log(
            'user_register',
            sprintf( __( 'Nuovo utente registrato: %1$s (ruoli: %2$s)', 'olobuild' ), $u ? $u->user_login : $user_id, $u ? implode( ',', $u->roles ) : '?' ),
            $is_admin ? 'high' : 'warn'
        );
    }

    public static function on_set_user_role( $user_id, $role, $old_roles ) {
        $privileged = in_array( $role, [ 'administrator', 'editor' ], true );
        $was_admin  = in_array( 'administrator', (array) $old_roles, true );
        if ( $privileged && ! in_array( $role, (array) $old_roles, true ) ) {
            $u = get_userdata( $user_id );
            self::log(
                'role_change',
                sprintf( __( 'Ruolo elevato a "%1$s" per: %2$s', 'olobuild' ), $role, $u ? $u->user_login : $user_id ),
                $role === 'administrator' && ! $was_admin ? 'high' : 'warn'
            );
        }
    }

    public static function on_plugin_activated( $plugin ) {
        self::log( 'plugin_activate', sprintf( __( 'Plugin attivato: %s', 'olobuild' ), $plugin ), 'warn' );
    }

    public static function on_plugin_deactivated( $plugin ) {
        self::log( 'plugin_deactivate', sprintf( __( 'Plugin disattivato: %s', 'olobuild' ), $plugin ), 'info' );
    }

    public static function on_switch_theme( $new_name ) {
        self::log( 'theme_switch', sprintf( __( 'Tema cambiato: %s', 'olobuild' ), $new_name ), 'warn' );
    }

    public static function on_core_option( $old, $new, $option ) {
        if ( $old === $new ) {
            return;
        }
        $high = in_array( $option, [ 'siteurl', 'home', 'admin_email', 'users_can_register', 'default_role' ], true );
        // default_role/users_can_register: alza solo se va verso uno stato rischioso.
        if ( $option === 'default_role' ) {
            $high = in_array( $new, [ 'administrator', 'editor' ], true );
        }
        if ( $option === 'users_can_register' ) {
            $high = ! empty( $new );
        }
        self::log(
            'option_change',
            sprintf( __( 'Opzione "%1$s" modificata: %2$s → %3$s', 'olobuild' ), $option, self::short( $old ), self::short( $new ) ),
            $high ? 'high' : 'warn'
        );
    }

    public static function on_custom_code() {
        self::log( 'custom_code', __( 'Codice personalizzato (head/body/footer) modificato', 'olobuild' ), 'warn' );
    }

    public static function on_active_layout( $old, $new, $option ) {
        if ( $old === $new ) {
            return;
        }
        self::log( 'layout_change', sprintf( __( '"%1$s" cambiato in #%2$s', 'olobuild' ), $option, self::short( $new ) ), 'info' );
    }

    // ── Query / manutenzione ────────────────────────────────────────────────

    /** @return array Righe del log (più recenti prima). */
    public static function get_events( $limit = 200, $type = '' ) {
        global $wpdb;
        $table = self::table_name();
        $limit = max( 1, min( 1000, (int) $limit ) );
        if ( $type !== '' ) {
            return $wpdb->get_results(
                $wpdb->prepare( "SELECT * FROM $table WHERE event_type = %s ORDER BY id DESC LIMIT %d", $type, $limit ),
                ARRAY_A
            ) ?: [];
        }
        return $wpdb->get_results(
            $wpdb->prepare( "SELECT * FROM $table ORDER BY id DESC LIMIT %d", $limit ),
            ARRAY_A
        ) ?: [];
    }

    /** Tipi di evento presenti (per il filtro). */
    public static function get_types() {
        global $wpdb;
        $table = self::table_name();
        return $wpdb->get_col( "SELECT DISTINCT event_type FROM $table ORDER BY event_type ASC" ) ?: [];
    }

    /** Elimina i log più vecchi di N giorni. Chiamato dal cron di OLOsecurity. */
    public static function cleanup( $days = 90 ) {
        global $wpdb;
        $table = self::table_name();
        $wpdb->query(
            $wpdb->prepare( "DELETE FROM $table WHERE created_at < %s", gmdate( 'Y-m-d H:i:s', time() - $days * DAY_IN_SECONDS ) )
        );
    }

    public static function export_csv() {
        $rows = self::get_events( 1000 );
        header( 'Content-Type: text/csv; charset=UTF-8' );
        header( 'Content-Disposition: attachment; filename="olosecurity-log.csv"' );
        $out = fopen( 'php://output', 'w' );
        fwrite( $out, "\xEF\xBB\xBF" );
        fputcsv( $out, [ 'ID', 'Data', 'Tipo', 'Gravità', 'User ID', 'Utente', 'IP', 'Messaggio' ], ';' );
        foreach ( $rows as $r ) {
            // olo_csv_safe: user_login/message contengono input esterno (es. username
            // dei login falliti) → anti CSV formula injection all'apertura in Excel.
            fputcsv( $out, array_map( 'olo_csv_safe', [ $r['id'], $r['created_at'], $r['event_type'], $r['severity'], $r['user_id'], $r['user_login'], $r['ip'], $r['message'] ] ), ';' );
        }
        fclose( $out );
        exit;
    }

    // ── Helper ───────────────────────────────────────────────────────────

    /** IP client, controllando con prudenza l'header proxy. Condiviso con gli altri moduli. */
    public static function client_ip() {
        if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ips = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
            $ip  = trim( $ips[0] );
            if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
                return $ip;
            }
        }
        $remote = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
        return filter_var( $remote, FILTER_VALIDATE_IP ) ? $remote : '0.0.0.0';
    }

    private static function short( $v ) {
        if ( is_array( $v ) || is_object( $v ) ) {
            $v = wp_json_encode( $v );
        }
        $v = (string) $v;
        return strlen( $v ) > 60 ? substr( $v, 0, 57 ) . '…' : $v;
    }

    public static function sev_color( $sev ) {
        $map = [ 'high' => '#dc3232', 'warn' => '#dba617', 'info' => '#3582c4' ];
        return $map[ $sev ] ?? '#777';
    }
}
