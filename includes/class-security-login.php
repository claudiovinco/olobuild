<?php
/**
 * Olo_Security_Login — prevenzione attiva sull'accesso.
 *
 *  - Rate-limit dei tentativi di login per IP con lockout temporaneo (anti brute-force).
 *  - Stop alla user-enumeration: blocca ?author=N agli anonimi e l'endpoint REST
 *    /wp/v2/users a chi non può elencare gli utenti.
 *
 * I tentativi falliti e i lockout finiscono nel registro attività (Olo_Security_Audit).
 *
 * Impostazioni (option olo_sec_login):
 *   enabled, max_attempts, lockout_min, block_user_enum
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Security_Login {

    const OPT   = 'olo_sec_login';
    const STATE = 'olo_sec_login_state'; // contatori e lockout per IP (gestito via API option)

    public static function defaults() {
        return [
            'enabled'         => 1,
            'max_attempts'    => 5,
            'lockout_min'     => 15,
            'block_user_enum' => 1,
        ];
    }

    public static function get_settings() {
        $s = get_option( self::OPT, [] );
        return wp_parse_args( is_array( $s ) ? $s : [], self::defaults() );
    }

    public static function init() {
        $s = self::get_settings();

        if ( ! empty( $s['enabled'] ) ) {
            add_filter( 'authenticate',    [ __CLASS__, 'check_lockout' ], 30 );
            add_action( 'wp_login_failed', [ __CLASS__, 'on_failed' ], 5, 1 );
            add_action( 'wp_login',        [ __CLASS__, 'on_success' ], 5, 2 );
        }

        if ( ! empty( $s['block_user_enum'] ) ) {
            add_action( 'template_redirect', [ __CLASS__, 'block_author_enum' ] );
            add_filter( 'rest_endpoints',    [ __CLASS__, 'filter_rest_user_endpoints' ] );
        }
    }

    // ── Brute-force ────────────────────────────────────────────────────────
    //
    // Stato in un'unica option (olo_sec_login_state) gestita via API WordPress:
    //   [ 'fails' => [ iphash => ['count'=>n,'exp'=>ts] ], 'locks' => [ iphash => exp_ts ] ]
    // Più robusto dei transient per-IP: get/update/delete_option invalidano la cache
    // correttamente (anche con object cache esterni) ed è enumerabile per lo sblocco.

    private static function get_state() {
        $s = get_option( self::STATE, [] );
        return is_array( $s ) ? $s : [];
    }

    /** Rimuove voci scadute per tenere piccola l'option. */
    private static function prune( &$state ) {
        $now = time();
        if ( ! empty( $state['fails'] ) ) {
            foreach ( $state['fails'] as $k => $v ) {
                if ( ( $v['exp'] ?? 0 ) < $now ) {
                    unset( $state['fails'][ $k ] );
                }
            }
        }
        if ( ! empty( $state['locks'] ) ) {
            foreach ( $state['locks'] as $k => $exp ) {
                if ( $exp < $now ) {
                    unset( $state['locks'][ $k ] );
                }
            }
        }
    }

    private static function iphash() {
        return md5( Olo_Security_Audit::client_ip() );
    }

    /** Filtro authenticate: se l'IP è in lockout, blocca prima di validare la password. */
    public static function check_lockout( $user ) {
        $state = self::get_state();
        self::prune( $state );
        $ip = self::iphash();
        if ( ! empty( $state['locks'][ $ip ] ) && $state['locks'][ $ip ] > time() ) {
            $s = self::get_settings();
            return new WP_Error(
                'olo_locked',
                sprintf(
                    /* translators: %d: minutes */
                    __( 'Troppi tentativi di accesso falliti. Riprova tra circa %d minuti.', 'olobuild' ),
                    (int) $s['lockout_min']
                )
            );
        }
        return $user;
    }

    public static function on_failed( $username ) {
        $s      = self::get_settings();
        $max    = max( 1, (int) $s['max_attempts'] );
        $window = max( 1, (int) $s['lockout_min'] ) * MINUTE_IN_SECONDS;
        $now    = time();

        $state = self::get_state();
        self::prune( $state );
        $ip    = self::iphash();
        $count = (int) ( $state['fails'][ $ip ]['count'] ?? 0 ) + 1;
        $state['fails'][ $ip ] = [ 'count' => $count, 'exp' => $now + $window ];

        if ( $count >= $max ) {
            $state['locks'][ $ip ] = $now + $window;
            if ( class_exists( 'Olo_Security_Audit' ) ) {
                Olo_Security_Audit::log(
                    'lockout',
                    sprintf( __( 'IP bloccato dopo %1$d tentativi falliti (ultimo utente: %2$s)', 'olobuild' ), $count, $username ),
                    'high',
                    [ 'user_id' => 0, 'user_login' => (string) $username ]
                );
            }
        }
        update_option( self::STATE, $state, false );
    }

    public static function on_success( $user_login, $user = null ) {
        $state = self::get_state();
        $ip    = self::iphash();
        unset( $state['fails'][ $ip ], $state['locks'][ $ip ] );
        update_option( self::STATE, $state, false );
    }

    /** Rimuove tutti i lockout/contatori attivi (pulsante "sblocca" nella UI). */
    public static function clear_lockouts() {
        delete_option( self::STATE );
    }

    // ── User enumeration ─────────────────────────────────────────────────────

    /** Blocca ?author=N (redirect di enumerazione) per i visitatori anonimi. */
    public static function block_author_enum() {
        if ( is_admin() || current_user_can( 'list_users' ) ) {
            return;
        }
        if ( isset( $_GET['author'] ) && preg_match( '/^\d+$/', (string) wp_unslash( $_GET['author'] ) ) ) {
            wp_safe_redirect( home_url( '/' ), 302 );
            exit;
        }
    }

    /** Nasconde la collection REST degli utenti a chi non può elencarli. */
    public static function filter_rest_user_endpoints( $endpoints ) {
        if ( current_user_can( 'list_users' ) ) {
            return $endpoints;
        }
        foreach ( [ '/wp/v2/users', '/wp/v2/users/(?P<id>[\d]+)' ] as $route ) {
            if ( isset( $endpoints[ $route ] ) ) {
                unset( $endpoints[ $route ] );
            }
        }
        return $endpoints;
    }

    // ── Settings UI (i campi; il <form> è fornito dalla pagina OLOsecurity) ──

    public static function save_settings( $post ) {
        $s = [
            'enabled'         => empty( $post['olo_login_enabled'] ) ? 0 : 1,
            'max_attempts'    => max( 1, min( 50, (int) ( $post['olo_login_max'] ?? 5 ) ) ),
            'lockout_min'     => max( 1, min( 1440, (int) ( $post['olo_login_lockout'] ?? 15 ) ) ),
            'block_user_enum' => empty( $post['olo_login_enum'] ) ? 0 : 1,
        ];
        update_option( self::OPT, $s, false );
    }

    public static function render_settings_fields() {
        $s = self::get_settings();
        ?>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><?php esc_html_e( 'Anti brute-force', 'olobuild' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="olo_login_enabled" value="1" <?php checked( ! empty( $s['enabled'] ) ); ?>>
                        <?php esc_html_e( 'Blocca temporaneamente un IP dopo troppi tentativi di accesso falliti', 'olobuild' ); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Tentativi massimi', 'olobuild' ); ?></th>
                <td><input type="number" min="1" max="50" name="olo_login_max" value="<?php echo esc_attr( $s['max_attempts'] ); ?>" class="small-text"></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Durata blocco (minuti)', 'olobuild' ); ?></th>
                <td><input type="number" min="1" max="1440" name="olo_login_lockout" value="<?php echo esc_attr( $s['lockout_min'] ); ?>" class="small-text"></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Stop user-enumeration', 'olobuild' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="olo_login_enum" value="1" <?php checked( ! empty( $s['block_user_enum'] ) ); ?>>
                        <?php esc_html_e( 'Blocca ?author=N e l\'elenco utenti via REST per i non autorizzati', 'olobuild' ); ?>
                    </label>
                </td>
            </tr>
        </table>
        <?php
    }
}
