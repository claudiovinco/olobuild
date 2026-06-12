<?php
/**
 * Olo_Security_Login — prevenzione attiva sull'accesso.
 *
 *  - Rate-limit dei tentativi di login per IP con lockout temporaneo (anti brute-force).
 *  - Blocklist IP permanente: gli IP/CIDR elencati ricevono 403 su tutto il sito.
 *  - Escalation automatica: un IP recidivo (più lockout nella finestra di 24 ore)
 *    viene promosso da solo nella blocklist permanente.
 *  - Stop alla user-enumeration: blocca ?author=N agli anonimi e l'endpoint REST
 *    /wp/v2/users a chi non può elencare gli utenti.
 *
 * I tentativi falliti e i lockout finiscono nel registro attività (Olo_Security_Audit).
 *
 * Impostazioni (option olo_sec_login):
 *   enabled, max_attempts, lockout_min, block_user_enum, auto_block, auto_block_strikes
 * Blocklist (option olo_sec_blocklist, autoload on): array di IP o reti CIDR.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Security_Login {

    const OPT       = 'olo_sec_login';
    const STATE     = 'olo_sec_login_state'; // contatori e lockout per IP (gestito via API option)
    const BLOCKLIST = 'olo_sec_blocklist';   // IP/CIDR bloccati in modo permanente (403)

    public static function defaults() {
        return [
            'enabled'            => 1,
            'max_attempts'       => 5,
            'lockout_min'        => 15,
            'block_user_enum'    => 1,
            'auto_block'         => 1,
            'auto_block_strikes' => 2,
        ];
    }

    public static function get_settings() {
        $s = get_option( self::OPT, [] );
        return wp_parse_args( is_array( $s ) ? $s : [], self::defaults() );
    }

    public static function init() {
        self::enforce_blocklist();

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

    // ── Blocklist IP permanente ──────────────────────────────────────────────
    //
    // A differenza del lockout (temporaneo, scade), la blocklist è permanente e
    // vale su TUTTO il sito: 403 prima di qualunque elaborazione. Il confronto
    // considera tutti gli IP dichiarati dalla richiesta (REMOTE_ADDR + intera
    // catena X-Forwarded-For + CF-Connecting-IP + X-Real-IP): dietro Cloudflare
    // l'IP reale arriva via header, e un client non può evadere il blocco
    // falsificandone uno — qualunque candidato in lista fa scattare il 403.

    public static function get_blocklist() {
        $l = get_option( self::BLOCKLIST, [] );
        if ( ! is_array( $l ) ) {
            return [];
        }
        return array_values( array_filter( array_map( 'trim', array_map( 'strval', $l ) ) ) );
    }

    /** 403 immediato se la richiesta proviene da un IP in blocklist. */
    public static function enforce_blocklist() {
        if ( PHP_SAPI === 'cli' || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
            return; // wp-cli resta sempre la via di sblocco di emergenza
        }
        $list = self::get_blocklist();
        if ( ! $list ) {
            return;
        }
        foreach ( self::request_ips() as $ip ) {
            foreach ( $list as $entry ) {
                if ( self::ip_matches( $ip, $entry ) ) {
                    if ( function_exists( 'status_header' ) ) {
                        status_header( 403 );
                        nocache_headers();
                    } else {
                        header( 'HTTP/1.1 403 Forbidden' );
                    }
                    exit( 'Forbidden' );
                }
            }
        }
    }

    /** Tutti gli IP validi dichiarati dalla richiesta (connessione + header proxy). */
    private static function request_ips() {
        $raw = [];
        foreach ( [ 'REMOTE_ADDR', 'HTTP_CF_CONNECTING_IP', 'HTTP_X_REAL_IP' ] as $k ) {
            if ( ! empty( $_SERVER[ $k ] ) ) {
                $raw[] = (string) wp_unslash( $_SERVER[ $k ] );
            }
        }
        if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            foreach ( explode( ',', (string) wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) as $p ) {
                $raw[] = $p;
            }
        }
        $ips = [];
        foreach ( $raw as $ip ) {
            $ip = trim( $ip );
            if ( $ip !== '' && filter_var( $ip, FILTER_VALIDATE_IP ) ) {
                $ips[ $ip ] = true;
            }
        }
        return array_keys( $ips );
    }

    /** Confronta un IP con una voce di blocklist: IP esatto oppure rete CIDR (IPv4/IPv6). */
    private static function ip_matches( $ip, $entry ) {
        $ipb = @inet_pton( $ip );
        if ( $ipb === false ) {
            return false;
        }
        if ( strpos( $entry, '/' ) === false ) {
            $eb = @inet_pton( $entry );
            return $eb !== false && $eb === $ipb;
        }
        list( $net, $bits ) = array_pad( explode( '/', $entry, 2 ), 2, '' );
        $netb = @inet_pton( trim( $net ) );
        $bits = (int) $bits;
        if ( $netb === false || strlen( $netb ) !== strlen( $ipb ) || $bits < 1 || $bits > strlen( $netb ) * 8 ) {
            return false;
        }
        $bytes = intdiv( $bits, 8 );
        $rem   = $bits % 8;
        if ( $bytes > 0 && substr( $ipb, 0, $bytes ) !== substr( $netb, 0, $bytes ) ) {
            return false;
        }
        if ( $rem > 0 ) {
            $mask = ( 0xFF << ( 8 - $rem ) ) & 0xFF;
            if ( ( ord( $ipb[ $bytes ] ) & $mask ) !== ( ord( $netb[ $bytes ] ) & $mask ) ) {
                return false;
            }
        }
        return true;
    }

    /** Voce valida: IP (v4/v6) oppure rete CIDR con prefisso coerente. */
    private static function valid_blocklist_entry( $entry ) {
        if ( strpos( $entry, '/' ) === false ) {
            return filter_var( $entry, FILTER_VALIDATE_IP ) !== false;
        }
        list( $net, $bits ) = array_pad( explode( '/', $entry, 2 ), 2, '' );
        if ( ! ctype_digit( $bits ) ) {
            return false;
        }
        $netb = @inet_pton( trim( $net ) );
        return $netb !== false && (int) $bits >= 1 && (int) $bits <= strlen( $netb ) * 8;
    }

    // ── Brute-force ────────────────────────────────────────────────────────
    //
    // Stato in un'unica option (olo_sec_login_state) gestita via API WordPress:
    //   [ 'fails'   => [ iphash => ['count'=>n,'exp'=>ts] ],
    //     'locks'   => [ iphash => exp_ts ],
    //     'strikes' => [ iphash => ['count'=>n,'exp'=>ts] ] ]  ← lockout distinti (recidiva)
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
        if ( ! empty( $state['strikes'] ) ) {
            foreach ( $state['strikes'] as $k => $v ) {
                if ( ( $v['exp'] ?? 0 ) < $now ) {
                    unset( $state['strikes'][ $k ] );
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
            // Durante un lockout i tentativi continuano ad arrivare qui (il filtro
            // authenticate li respinge ma wp_login_failed scatta lo stesso): il lock
            // si estende, ma conta come recidiva solo un lockout NUOVO.
            $is_new_lockout        = empty( $state['locks'][ $ip ] ) || $state['locks'][ $ip ] <= $now;
            $state['locks'][ $ip ] = $now + $window;
            if ( class_exists( 'Olo_Security_Audit' ) ) {
                Olo_Security_Audit::log(
                    'lockout',
                    sprintf( __( 'IP bloccato dopo %1$d tentativi falliti (ultimo utente: %2$s)', 'olobuild' ), $count, $username ),
                    'high',
                    [ 'user_id' => 0, 'user_login' => (string) $username ]
                );
            }
            if ( $is_new_lockout ) {
                self::register_strike( $state, $ip, $s );
            }
        }
        update_option( self::STATE, $state, false );
    }

    /**
     * Registra un lockout come "recidiva" per l'IP corrente e, raggiunta la soglia,
     * lo promuove nella blocklist permanente. Le recidive scadono 24 ore dopo
     * l'ultimo lockout (finestra scorrevole). Niente anti-autoblocco qui: chi fa
     * scattare più lockout È l'IP da bloccare; lo sblocco resta possibile dalla
     * textarea Blocklist IP (da altro IP) o via wp-cli.
     */
    private static function register_strike( &$state, $iphash, $settings ) {
        if ( empty( $settings['auto_block'] ) ) {
            return;
        }
        $count = (int) ( $state['strikes'][ $iphash ]['count'] ?? 0 ) + 1;
        $state['strikes'][ $iphash ] = [ 'count' => $count, 'exp' => time() + DAY_IN_SECONDS ];

        if ( $count < max( 1, (int) $settings['auto_block_strikes'] ) ) {
            return;
        }

        // Lo stato è indicizzato per hash, ma l'escalation avviene durante una
        // richiesta dell'IP colpevole: l'IP in chiaro è quello della richiesta.
        $real_ip = Olo_Security_Audit::client_ip();
        if ( ! filter_var( $real_ip, FILTER_VALIDATE_IP ) || $real_ip === '0.0.0.0' || md5( $real_ip ) !== $iphash ) {
            return;
        }

        unset( $state['strikes'][ $iphash ], $state['fails'][ $iphash ], $state['locks'][ $iphash ] );

        $list = self::get_blocklist();
        foreach ( $list as $entry ) {
            if ( self::ip_matches( $real_ip, $entry ) ) {
                return; // già coperto da una voce esistente
            }
        }
        $list[] = $real_ip;
        update_option( self::BLOCKLIST, $list, true );

        Olo_Security_Audit::log(
            'blocklist',
            sprintf(
                /* translators: 1: IP address, 2: number of lockouts */
                __( 'IP %1$s aggiunto automaticamente alla blocklist permanente dopo %2$d lockout in 24 ore.', 'olobuild' ),
                $real_ip,
                $count
            ),
            'high',
            [ 'user_id' => 0 ]
        );
    }

    public static function on_success( $user_login, $user = null ) {
        $state = self::get_state();
        $ip    = self::iphash();
        unset( $state['fails'][ $ip ], $state['locks'][ $ip ], $state['strikes'][ $ip ] );
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
            'enabled'            => empty( $post['olo_login_enabled'] ) ? 0 : 1,
            'max_attempts'       => max( 1, min( 50, (int) ( $post['olo_login_max'] ?? 5 ) ) ),
            'lockout_min'        => max( 1, min( 1440, (int) ( $post['olo_login_lockout'] ?? 15 ) ) ),
            'block_user_enum'    => empty( $post['olo_login_enum'] ) ? 0 : 1,
            'auto_block'         => empty( $post['olo_login_autoblock'] ) ? 0 : 1,
            'auto_block_strikes' => max( 1, min( 20, (int) ( $post['olo_login_autoblock_strikes'] ?? 2 ) ) ),
        ];
        update_option( self::OPT, $s, false );

        return self::save_blocklist( (string) wp_unslash( $post['olo_blocklist'] ?? '' ) );
    }

    /**
     * Salva la blocklist dalla textarea (una voce per riga). Scarta le voci non
     * valide e quelle che bloccherebbero la richiesta corrente (anti-autoblocco).
     * Ritorna un'eventuale stringa di avviso da mostrare accanto al "salvato".
     */
    private static function save_blocklist( $raw ) {
        $old     = self::get_blocklist();
        $valid   = [];
        $invalid = [];
        $own     = [];
        $my_ips  = self::request_ips();

        foreach ( preg_split( '/[\r\n,]+/', $raw ) as $line ) {
            $entry = trim( sanitize_text_field( $line ) );
            if ( $entry === '' || in_array( $entry, $valid, true ) ) {
                continue;
            }
            if ( ! self::valid_blocklist_entry( $entry ) ) {
                $invalid[] = $entry;
                continue;
            }
            $is_own = false;
            foreach ( $my_ips as $ip ) {
                if ( self::ip_matches( $ip, $entry ) ) {
                    $is_own = true;
                    break;
                }
            }
            if ( $is_own ) {
                $own[] = $entry;
                continue;
            }
            $valid[] = $entry;
        }

        // Autoload on: la lista è letta a ogni richiesta dal blocco anticipato.
        update_option( self::BLOCKLIST, $valid, true );

        $added   = array_diff( $valid, $old );
        $removed = array_diff( $old, $valid );
        if ( ( $added || $removed ) && class_exists( 'Olo_Security_Audit' ) ) {
            Olo_Security_Audit::log(
                'blocklist',
                sprintf(
                    /* translators: 1: added entries, 2: removed entries */
                    __( 'Blocklist IP aggiornata. Aggiunti: %1$s — Rimossi: %2$s', 'olobuild' ),
                    $added ? implode( ', ', $added ) : '—',
                    $removed ? implode( ', ', $removed ) : '—'
                ),
                'info'
            );
        }

        $warn = [];
        if ( $own ) {
            $warn[] = sprintf( __( 'Voci scartate perché bloccherebbero il tuo IP attuale: %s.', 'olobuild' ), implode( ', ', $own ) );
        }
        if ( $invalid ) {
            $warn[] = sprintf( __( 'Voci non valide ignorate: %s.', 'olobuild' ), implode( ', ', $invalid ) );
        }
        return implode( ' ', $warn );
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
                <th scope="row"><?php esc_html_e( 'Blocklist automatica', 'olobuild' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="olo_login_autoblock" value="1" <?php checked( ! empty( $s['auto_block'] ) ); ?>>
                        <?php esc_html_e( 'Sposta nella Blocklist IP permanente gli IP recidivi', 'olobuild' ); ?>
                    </label>
                    <p>
                        <label>
                            <?php esc_html_e( 'Dopo quanti lockout (nell\'arco di 24 ore):', 'olobuild' ); ?>
                            <input type="number" min="1" max="20" name="olo_login_autoblock_strikes" value="<?php echo esc_attr( $s['auto_block_strikes'] ); ?>" class="small-text">
                        </label>
                    </p>
                    <p class="description"><?php esc_html_e( 'Un IP che fa scattare più volte il blocco temporaneo viene aggiunto da solo alla Blocklist IP qui sotto (403 permanente su tutto il sito) e registrato nel registro attività. Le voci aggiunte automaticamente compaiono nella textarea e si rimuovono come le altre.', 'olobuild' ); ?></p>
                </td>
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
            <tr>
                <th scope="row"><?php esc_html_e( 'Blocklist IP', 'olobuild' ); ?></th>
                <td>
                    <textarea name="olo_blocklist" rows="4" class="large-text code" placeholder="203.0.113.7&#10;198.51.100.0/24"><?php echo esc_textarea( implode( "\n", self::get_blocklist() ) ); ?></textarea>
                    <p class="description"><?php esc_html_e( 'Blocco permanente: questi IP ricevono 403 su tutto il sito. Una voce per riga: IP singolo o rete CIDR (es. 198.51.100.0/24). Il tuo IP attuale non può essere aggiunto. Sblocco di emergenza via wp-cli: wp option delete olo_sec_blocklist', 'olobuild' ); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
}
