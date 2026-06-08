<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Bootstrap: imposta cookie SID + virtual login dell'utente guest per ogni visitatore.
 *
 * Uso `determine_current_user` (hook molto basso nel ciclo WP) per "loggare"
 * automaticamente come guest user chi ha cookie sandbox e non è già un admin.
 * Il guest user è un singolo utente WP reale (role `olo_sandbox`) condiviso da
 * tutti i visitatori — la distinzione fra visitatori avviene tramite il SID
 * in cookie + clone template dedicato.
 *
 * NOTA: non chiamiamo wp_set_auth_cookie. Il "login" è solo per la request
 * corrente, non persistente. L'admin reale può continuare a loggarsi via
 * wp-login.php normalmente.
 */
class Olo_Sandbox_Bootstrap {

    public function __construct() {
        // Cookie + virtual login (priorità 99 = dopo eventuali altri filter)
        add_filter( 'determine_current_user', [ $this, 'maybe_virtual_login' ], 99 );

        // Auto-clone e touch al primo init utile
        add_action( 'init', [ $this, 'ensure_clone' ], 5 );

        // Garantisci che il cookie venga mandato anche su richieste senza output (REST)
        add_action( 'rest_api_init', [ $this, 'maybe_set_cookie_on_rest' ], 1 );

        // Set auth cookie WP per guest così wp-admin non redirige al login
        add_action( 'init', [ $this, 'maybe_set_auth_cookie_for_guest' ], 1 );

        // Bypass nonce check sul guest (logout link, ecc.) — non strettamente
        // necessario ma evita rumore in dashboard
        add_filter( 'auth_cookie_expiration', [ $this, 'auth_cookie_session_only' ], 10, 3 );
    }

    /**
     * Bootstrap: crea utente guest, role e tabella sessioni.
     * Chiamato da register_activation_hook.
     */
    public static function on_activate() {
        // 1. Tabella sessioni
        Olo_Sandbox_Session::create_table();

        // 1b. Tabella feedback (idempotente via dbDelta — chiamabile anche su upgrade)
        if ( class_exists( 'Olo_Sandbox_Feedback' ) ) {
            Olo_Sandbox_Feedback::create_table();
        }

        // 2. Role custom (no capability — la concessione è dinamica via filter user_has_cap)
        if ( ! get_role( Olo_Sandbox_Config::GUEST_ROLE ) ) {
            add_role( Olo_Sandbox_Config::GUEST_ROLE, 'Sandbox Guest', [ 'read' => true ] );
        }

        // 3. Utente guest
        if ( ! get_user_by( 'login', Olo_Sandbox_Config::GUEST_LOGIN ) ) {
            $user_id = wp_insert_user( [
                'user_login'   => Olo_Sandbox_Config::GUEST_LOGIN,
                'user_pass'    => wp_generate_password( 32, true, true ),
                'user_email'   => 'sandbox@try.olotheme.com',
                'display_name' => 'Sandbox Guest',
                'role'         => Olo_Sandbox_Config::GUEST_ROLE,
            ] );
            // Forzo show_admin_bar_front a false per il guest
            if ( ! is_wp_error( $user_id ) ) {
                update_user_meta( $user_id, 'show_admin_bar_front', 'false' );
            }
        }

        // 4. Cron 1h per cleanup
        if ( ! wp_next_scheduled( 'olo_sandbox_cleanup' ) ) {
            wp_schedule_event( time() + 60, 'hourly', 'olo_sandbox_cleanup' );
        }
    }

    public static function on_deactivate() {
        wp_clear_scheduled_hook( 'olo_sandbox_cleanup' );
        // Lasciamo intenzionalmente tabella + utente guest (uninstall.php può pulirli)
    }

    /**
     * Restituisce ID utente guest, cache.
     */
    public static function guest_user_id() {
        static $id = null;
        if ( $id !== null ) return $id;
        $user = get_user_by( 'login', Olo_Sandbox_Config::GUEST_LOGIN );
        $id = $user ? (int) $user->ID : 0;
        return $id;
    }

    /**
     * Filter `determine_current_user`: se non già autenticato come admin
     * e visitatore ha cookie sandbox (o glielo creiamo ora) → restituiamo guest ID.
     */
    public function maybe_virtual_login( $user_id ) {
        // Se già loggato come admin reale, non interferire
        if ( $user_id ) {
            return $user_id;
        }

        // Esclusi: wp-login.php, wp-cron, install
        if ( $this->is_excluded_request() ) {
            return $user_id;
        }

        // Skip se la request porta un cookie WP auth valido di un admin reale.
        // Caso: admin loggato fa una chiamata fetch dal browser — non vogliamo
        // creargli un SID/clone "fantasma".
        if ( self::has_real_admin_auth_cookie() ) {
            return $user_id;
        }

        // Cookie esistente?
        $sid = Olo_Sandbox_Session::sid_from_cookie();

        if ( ! $sid ) {
            if ( ! headers_sent() ) {
                $sid = Olo_Sandbox_Session::generate_and_set_cookie();
            } else {
                return $user_id;
            }
        }

        // Virtual login come guest
        $guest_id = self::guest_user_id();
        if ( ! $guest_id ) {
            return $user_id;
        }

        return $guest_id;
    }

    /**
     * Su REST, il filtro determine_current_user gira PRIMA dei response headers.
     * Forziamo il cookie set se manca, MA solo per visitatori non-admin.
     */
    public function maybe_set_cookie_on_rest() {
        // Se admin reale, niente cookie sandbox: non vogliamo inquinare il
        // browser dell'admin con un SID che non userà mai.
        if ( self::has_real_admin_auth_cookie() ) {
            return;
        }
        $sid = Olo_Sandbox_Session::sid_from_cookie();
        if ( ! $sid && ! headers_sent() ) {
            Olo_Sandbox_Session::generate_and_set_cookie();
        }
    }

    /**
     * Verifica se la richiesta corrente porta un cookie WP `wordpress_logged_in_*`
     * valido per un utente NON-guest (cioè un admin reale).
     * Usato per skippare l'intera logica sandbox quando un admin sta usando il sito.
     */
    public static function has_real_admin_auth_cookie() {
        $user_id = wp_validate_auth_cookie( '', 'logged_in' );
        if ( ! $user_id ) return false;
        $user = get_userdata( $user_id );
        if ( ! $user ) return false;
        return $user->user_login !== Olo_Sandbox_Config::GUEST_LOGIN;
    }

    /**
     * Setta cookie `wordpress_logged_in_*` per il guest user, così WordPress
     * passa il check `auth_redirect()` quando il guest visita /wp-admin/.
     * Solo se cookie sandbox attivo e nessun cookie WP esistente.
     */
    public function maybe_set_auth_cookie_for_guest() {
        if ( $this->is_excluded_request() ) {
            return;
        }
        if ( is_user_logged_in() ) {
            $user = wp_get_current_user();
            // Se già loggato e NON è guest (admin reale), skip
            if ( $user && $user->user_login !== Olo_Sandbox_Config::GUEST_LOGIN ) {
                return;
            }
        }

        $sid = Olo_Sandbox_Session::sid_from_cookie();
        if ( ! $sid ) {
            return;
        }

        $guest_id = self::guest_user_id();
        if ( ! $guest_id ) {
            return;
        }

        // Cookie auth già presente e valido per il guest?
        $current_user_id_via_cookie = wp_validate_auth_cookie( '', 'logged_in' );
        if ( $current_user_id_via_cookie === $guest_id ) {
            return;
        }

        if ( headers_sent() ) {
            return;
        }

        // Set auth cookie session-only (durata gestita da auth_cookie_expiration filter)
        wp_set_auth_cookie( $guest_id, false, is_ssl() );
    }

    /**
     * Limita la scadenza del cookie auth del guest a 12h (TTL sandbox).
     * Per gli admin reali lascia il valore di default.
     */
    public function auth_cookie_session_only( $expiration, $user_id, $remember ) {
        $user = get_userdata( $user_id );
        if ( $user && $user->user_login === Olo_Sandbox_Config::GUEST_LOGIN ) {
            return Olo_Sandbox_Config::session_ttl();
        }
        return $expiration;
    }

    /**
     * Hook init: garantisce clone del template per il SID corrente e tocca last_seen.
     */
    public function ensure_clone() {
        $sid = Olo_Sandbox_Session::sid_from_cookie();
        if ( ! $sid ) {
            return;
        }

        // Se utente è admin reale (non guest) — skip, non clonare per lui
        $current = wp_get_current_user();
        if ( $current && $current->ID && $current->user_login !== Olo_Sandbox_Config::GUEST_LOGIN ) {
            return;
        }

        // Garantisci clone
        Olo_Sandbox_Clone::ensure_for_sid( $sid );

        // Aggiorna last_seen (throttled internamente a 5 min)
        Olo_Sandbox_Session::touch( $sid );
    }

    /**
     * Request da escludere dal virtual login.
     */
    private function is_excluded_request() {
        // CLI: WP-CLI, scheduled tasks tramite cron unix, ecc.
        if ( ( defined( 'WP_CLI' ) && WP_CLI ) || php_sapi_name() === 'cli' ) {
            return true;
        }
        // wp-cron interno
        if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
            return true;
        }
        // wp-login.php (admin reale deve poter loggarsi)
        $script = isset( $_SERVER['SCRIPT_NAME'] ) ? basename( (string) $_SERVER['SCRIPT_NAME'] ) : '';
        if ( in_array( $script, [ 'wp-login.php', 'wp-cron.php', 'wp-signup.php', 'wp-activate.php' ], true ) ) {
            return true;
        }
        // XML-RPC e altre interfacce non interessano la sandbox
        if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
            return true;
        }
        return false;
    }
}
