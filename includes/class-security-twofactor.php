<?php
/**
 * Olo_Security_TwoFactor — verifica in due passaggi nativa (parte di OLOsecurity).
 *
 * Metodi per utente (configurazione self-service dal profilo):
 *   - TOTP (RFC 6238): app authenticator, QR code + chiave manuale, finestra ±1,
 *     anti-replay sull'ultimo timeslot usato. Il secret è cifrato (AES-256-GCM
 *     con chiave derivata dai salt di WordPress) nell'user meta.
 *   - Email OTP: codice a 6 cifre via wp_mail, valido 10 minuti. Disponibile come
 *     metodo principale o come riserva per chi usa l'app.
 *   - Recovery codes: 8 codici monouso (hash in meta), mostrati una sola volta.
 *
 * Flusso di login (pattern interstitial): a password verificata (wp_login) il
 * cookie di autenticazione viene revocato e si mostra la pagina del secondo
 * fattore con un token monouso (10 min). I codici errati alimentano
 * wp_login_failed, quindi contano per il lockout IP e l'auto-blocklist.
 *
 * Porte laterali: per gli utenti con 2FA l'autenticazione XML-RPC con sola
 * password è rifiutata; toggle opzionale per disabilitare le Application
 * Passwords di WordPress su tutto il sito.
 *
 * Convivenza: se è attivo un altro plugin 2FA noto (Two Factor, Wordfence Login
 * Security, WP 2FA, miniOrange) questo modulo si disattiva da solo e lo segnala
 * nelle impostazioni — mai due interstitial in cascata.
 *
 * Impostazioni (option olo_sec_2fa):
 *   enabled, require_admins, email_fallback, remember_days, disable_app_passwords
 * User meta: olo_2fa (config), olo_2fa_login (token interstitial), olo_2fa_email (OTP email)
 *
 * Sblocco di emergenza via wp-cli: wp user meta delete <id> olo_2fa
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Security_TwoFactor {

    const OPT        = 'olo_sec_2fa';
    const META       = 'olo_2fa';        // configurazione per-utente
    const META_LOGIN = 'olo_2fa_login';  // token monouso dell'interstitial
    const META_EMAIL = 'olo_2fa_email';  // codice OTP email in corso

    const TOKEN_TTL     = 600;  // vita del token interstitial (s)
    const EMAIL_TTL     = 600;  // vita del codice email (s)
    const PENDING_TTL   = 1800; // vita di un setup non confermato (s)
    const MAX_CODE_FAIL = 5;    // codici errati prima di invalidare il token
    const RECOVERY_N    = 8;    // codici di recupero generati

    /** True mentre completiamo noi il login post-verifica: evita la rientranza su wp_login. */
    private static $completing = false;

    public static function defaults() {
        return [
            'enabled'               => 1,
            'require_admins'        => 0,
            'email_fallback'        => 1,
            'remember_days'         => 30,
            'disable_app_passwords' => 0,
        ];
    }

    public static function get_settings() {
        $s = get_option( self::OPT, [] );
        return wp_parse_args( is_array( $s ) ? $s : [], self::defaults() );
    }

    public static function init() {
        // Gli altri plugin 2FA possono caricarsi dopo OLObuild: la rilevazione
        // del conflitto va fatta a plugins_loaded, non adesso.
        add_action( 'plugins_loaded', [ __CLASS__, 'register_hooks' ], 20 );
    }

    public static function register_hooks() {
        $s = self::get_settings();

        if ( ! empty( $s['disable_app_passwords'] ) ) {
            add_filter( 'wp_is_application_passwords_available', '__return_false' );
        }

        if ( empty( $s['enabled'] ) || self::conflicting_plugin() ) {
            return;
        }

        // Flusso di login.
        add_action( 'wp_login', [ __CLASS__, 'on_wp_login' ], 1, 2 );
        add_action( 'login_form_olo2fa', [ __CLASS__, 'login_form_handler' ] );
        add_filter( 'login_message', [ __CLASS__, 'login_expired_message' ] );
        add_filter( 'authenticate', [ __CLASS__, 'block_xmlrpc' ], 99 );

        // Pagina di setup self-service + sezioni nel profilo.
        // ⚠️ Il setup vive in una pagina DEDICATA: la pagina profilo è un unico
        // <form>, e un form annidato viene scartato dal browser (il submit
        // finirebbe nel form del profilo con il nonce sbagliato → "link scaduto").
        add_action( 'admin_menu', [ __CLASS__, 'register_page' ] );
        add_action( 'show_user_profile', [ __CLASS__, 'render_profile' ] );
        add_action( 'edit_user_profile', [ __CLASS__, 'render_profile_admin' ] );
        add_action( 'admin_post_olo_2fa_setup',   [ __CLASS__, 'handle_setup' ] );
        add_action( 'admin_post_olo_2fa_confirm', [ __CLASS__, 'handle_confirm' ] );
        add_action( 'admin_post_olo_2fa_cancel',  [ __CLASS__, 'handle_cancel' ] );
        add_action( 'admin_post_olo_2fa_disable', [ __CLASS__, 'handle_disable' ] );
        add_action( 'admin_post_olo_2fa_regen',   [ __CLASS__, 'handle_regen' ] );
        add_action( 'admin_post_olo_2fa_reset',   [ __CLASS__, 'handle_reset' ] );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_profile_assets' ] );

        // Obbligo per gli amministratori (se attivo).
        add_action( 'admin_init', [ __CLASS__, 'maybe_require_setup' ] );
        add_action( 'admin_notices', [ __CLASS__, 'required_notice' ] );
    }

    /**
     * Nome del plugin 2FA DEDICATO attivo (se il sito ne usa già uno, ci facciamo
     * da parte), o null. Le suite generaliste con 2FA opt-in incorporata (Wordfence
     * completo) NON bloccano: vedi coexisting_plugin().
     */
    public static function conflicting_plugin() {
        if ( class_exists( 'Two_Factor_Core' ) ) {
            return 'Two Factor';
        }
        // La classe WordfenceLS esiste anche nel Wordfence completo (modulo bundled):
        // qui si intercetta solo il plugin standalone "Wordfence Login Security".
        if ( ( defined( 'WFLS_PLUGIN_VERSION' ) || class_exists( 'WordfenceLS\Controller_WordfenceLS' ) ) && ! defined( 'WORDFENCE_VERSION' ) ) {
            return 'Wordfence Login Security';
        }
        if ( class_exists( 'WP2FA\WP2FA' ) ) {
            return 'WP 2FA';
        }
        if ( defined( 'MO2F_VERSION' ) || class_exists( 'Miniorange_Authentication' ) ) {
            return 'miniOrange 2FA';
        }
        return null;
    }

    /** Suite di sicurezza che include una propria 2FA opt-in: si convive, ma va segnalato. */
    public static function coexisting_plugin() {
        if ( defined( 'WORDFENCE_VERSION' ) ) {
            return 'Wordfence';
        }
        return null;
    }

    // ── Stato per-utente ─────────────────────────────────────────────────────

    public static function user_conf( $user_id ) {
        $c = get_user_meta( $user_id, self::META, true );
        return is_array( $c ) ? $c : [];
    }

    public static function is_user_configured( $user_id ) {
        $c = self::user_conf( $user_id );
        if ( empty( $c['method'] ) ) {
            return false;
        }
        return $c['method'] === 'email' || ! empty( $c['secret'] );
    }

    // ── Flusso di login ──────────────────────────────────────────────────────

    /** Password verificata: se l'utente ha la 2FA, ferma qui e chiedi il secondo fattore. */
    public static function on_wp_login( $user_login, $user ) {
        if ( self::$completing || ! $user instanceof WP_User || ! self::is_user_configured( $user->ID ) ) {
            return;
        }
        if ( self::has_valid_device_cookie( $user->ID ) ) {
            return;
        }

        wp_clear_auth_cookie();

        if ( wp_doing_ajax() ) {
            // Login AJAX di tema/plugin: non possiamo mostrare l'interstitial.
            wp_send_json_error( [ 'message' => __( 'Questo account richiede la verifica in due passaggi: accedi da wp-login.php.', 'olobuild' ) ] );
        }

        $token = self::create_login_token( $user->ID );
        $conf  = self::user_conf( $user->ID );
        if ( ( $conf['method'] ?? '' ) === 'email' ) {
            self::send_email_code( $user );
        }
        self::render_interstitial( $user, $token );
    }

    private static function create_login_token( $user_id ) {
        $token    = bin2hex( random_bytes( 32 ) );
        $remember = ! empty( $_POST['rememberme'] );
        $redirect = (string) wp_unslash( $_REQUEST['redirect_to'] ?? '' );
        update_user_meta( $user_id, self::META_LOGIN, [
            'hash'     => hash_hmac( 'sha256', $token, wp_salt( 'auth' ) ),
            'exp'      => time() + self::TOKEN_TTL,
            'remember' => $remember ? 1 : 0,
            'redirect' => $redirect,
            'fails'    => 0,
        ] );
        return $token;
    }

    /** POST di wp-login.php?action=olo2fa: verifica del codice. */
    public static function login_form_handler() {
        if ( ( $_SERVER['REQUEST_METHOD'] ?? '' ) !== 'POST' ) {
            wp_safe_redirect( wp_login_url() );
            exit;
        }

        $uid   = (int) ( $_POST['olo2fa_uid'] ?? 0 );
        $token = (string) wp_unslash( $_POST['olo2fa_token'] ?? '' );
        $user  = $uid ? get_user_by( 'id', $uid ) : false;
        $meta  = $user ? get_user_meta( $uid, self::META_LOGIN, true ) : null;

        $valid = $user && is_array( $meta ) && ( $meta['exp'] ?? 0 ) > time()
            && hash_equals( (string) ( $meta['hash'] ?? '' ), hash_hmac( 'sha256', $token, wp_salt( 'auth' ) ) );
        if ( ! $valid ) {
            wp_safe_redirect( add_query_arg( 'olo2fa', 'expired', wp_login_url() ) );
            exit;
        }

        // Il lockout IP vale anche qui: martellare codici = martellare il login.
        if ( class_exists( 'Olo_Security_Login' ) ) {
            $locked = Olo_Security_Login::check_lockout( null );
            if ( is_wp_error( $locked ) ) {
                delete_user_meta( $uid, self::META_LOGIN );
                wp_die( esc_html( $locked->get_error_message() ), 403 );
            }
        }

        // Reinvio del codice email (bottone dedicato, niente verifica).
        if ( ! empty( $_POST['olo2fa_resend'] ) ) {
            $sent = self::send_email_code( $user );
            self::render_interstitial( $user, $token, [
                'notice' => $sent ? __( 'Codice inviato: controlla la tua email.', 'olobuild' ) : __( 'Attendi un minuto prima di richiedere un altro codice.', 'olobuild' ),
            ] );
        }

        $code = (string) wp_unslash( $_POST['olo2fa_code'] ?? '' );
        if ( self::verify_any_code( $user, $code ) ) {
            delete_user_meta( $uid, self::META_LOGIN );
            self::$completing = true;
            wp_set_auth_cookie( $uid, ! empty( $meta['remember'] ) );
            do_action( 'wp_login', $user->user_login, $user ); // registro attività + reset contatori lockout
            if ( ! empty( $_POST['olo2fa_td'] ) ) {
                self::issue_device_cookie( $uid );
            }
            $redirect = $meta['redirect'] ?: admin_url();
            wp_safe_redirect( wp_validate_redirect( $redirect, admin_url() ) );
            exit;
        }

        // Codice errato: conta come tentativo fallito (lockout IP + registro).
        do_action( 'wp_login_failed', $user->user_login, new WP_Error( 'olo_2fa_wrong', __( 'Codice 2FA errato.', 'olobuild' ) ) );

        $meta['fails'] = (int) ( $meta['fails'] ?? 0 ) + 1;
        if ( $meta['fails'] >= self::MAX_CODE_FAIL ) {
            delete_user_meta( $uid, self::META_LOGIN );
            self::audit( 'warn', sprintf( __( 'Verifica 2FA interrotta per %s: troppi codici errati.', 'olobuild' ), $user->user_login ), $user );
            wp_safe_redirect( add_query_arg( 'olo2fa', 'expired', wp_login_url() ) );
            exit;
        }
        update_user_meta( $uid, self::META_LOGIN, $meta );
        self::render_interstitial( $user, $token, [ 'error' => __( 'Codice non valido. Riprova.', 'olobuild' ) ] );
    }

    /** Prova nell'ordine: TOTP, codice email, codice di recupero. */
    private static function verify_any_code( $user, $code ) {
        $code = trim( $code );
        if ( $code === '' ) {
            return false;
        }
        $conf = self::user_conf( $user->ID );
        $s    = self::get_settings();

        $digits = preg_replace( '/\D+/', '', $code );
        if ( strlen( $digits ) === 6 ) {
            if ( ( $conf['method'] ?? '' ) === 'totp' && self::verify_totp( $user->ID, $digits ) ) {
                return true;
            }
            $email_ok = ( $conf['method'] ?? '' ) === 'email' || ! empty( $s['email_fallback'] );
            if ( $email_ok && self::verify_email_code( $user->ID, $digits ) ) {
                return true;
            }
        }
        return self::consume_recovery_code( $user, $code );
    }

    /** Messaggio su wp-login dopo un token scaduto/invalidato. */
    public static function login_expired_message( $message ) {
        if ( ( $_GET['olo2fa'] ?? '' ) === 'expired' ) {
            $message .= '<div id="login_error">' . esc_html__( 'Verifica in due passaggi scaduta o interrotta: accedi di nuovo.', 'olobuild' ) . '</div>';
        }
        return $message;
    }

    /** XML-RPC autentica con la sola password: per gli utenti 2FA è una porta laterale, va chiusa. */
    public static function block_xmlrpc( $user ) {
        if ( $user instanceof WP_User && defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST && self::is_user_configured( $user->ID ) ) {
            return new WP_Error( 'olo_2fa_xmlrpc', __( 'XML-RPC non disponibile per gli account con verifica in due passaggi.', 'olobuild' ) );
        }
        return $user;
    }

    // ── Interstitial ─────────────────────────────────────────────────────────

    private static function render_interstitial( $user, $token, $args = [] ) {
        $conf      = self::user_conf( $user->ID );
        $s         = self::get_settings();
        $is_email  = ( $conf['method'] ?? '' ) === 'email';
        $can_email = $is_email || ! empty( $s['email_fallback'] );
        $action    = add_query_arg( 'action', 'olo2fa', wp_login_url() );
        $remember  = (int) $s['remember_days'];

        nocache_headers();

        $intro = $is_email
            ? __( 'Ti abbiamo inviato un codice a 6 cifre via email.', 'olobuild' )
            : __( 'Inserisci il codice a 6 cifre dalla tua app di autenticazione.', 'olobuild' );

        ob_start();
        ?>
        <?php if ( ! empty( $args['error'] ) ) : ?>
            <div id="login_error"><?php echo esc_html( $args['error'] ); ?></div>
        <?php endif; ?>
        <?php if ( ! empty( $args['notice'] ) ) : ?>
            <p class="message"><?php echo esc_html( $args['notice'] ); ?></p>
        <?php endif; ?>
        <form name="olo2fa" method="post" action="<?php echo esc_url( $action ); ?>">
            <p style="margin-bottom:12px"><?php echo esc_html( $intro ); ?></p>
            <p>
                <label for="olo2fa_code"><?php esc_html_e( 'Codice di verifica', 'olobuild' ); ?></label>
                <input type="text" name="olo2fa_code" id="olo2fa_code" class="input" size="20"
                       autocomplete="one-time-code" inputmode="numeric" autofocus>
            </p>
            <?php if ( $remember > 0 ) : ?>
                <p style="margin:14px 0">
                    <label>
                        <input type="checkbox" name="olo2fa_td" value="1">
                        <?php echo esc_html( sprintf( __( 'Non chiedere più su questo browser per %d giorni', 'olobuild' ), $remember ) ); ?>
                    </label>
                </p>
            <?php endif; ?>
            <input type="hidden" name="olo2fa_uid" value="<?php echo esc_attr( $user->ID ); ?>">
            <input type="hidden" name="olo2fa_token" value="<?php echo esc_attr( $token ); ?>">
            <p class="submit">
                <input type="submit" class="button button-primary button-large" value="<?php esc_attr_e( 'Verifica', 'olobuild' ); ?>">
            </p>
        </form>
        <p style="margin-top:14px;font-size:13px">
            <?php esc_html_e( 'Hai perso l\'accesso? Inserisci qui sopra uno dei tuoi codici di recupero.', 'olobuild' ); ?>
        </p>
        <?php if ( $can_email ) : ?>
            <form method="post" action="<?php echo esc_url( $action ); ?>" style="margin-top:6px">
                <input type="hidden" name="olo2fa_uid" value="<?php echo esc_attr( $user->ID ); ?>">
                <input type="hidden" name="olo2fa_token" value="<?php echo esc_attr( $token ); ?>">
                <input type="hidden" name="olo2fa_resend" value="1">
                <button type="submit" class="button-link" style="text-decoration:underline;cursor:pointer;background:none;border:0;padding:0;color:#2271b1;font-size:13px">
                    <?php esc_html_e( 'Inviami un codice via email', 'olobuild' ); ?>
                </button>
            </form>
        <?php endif; ?>
        <?php
        $body = ob_get_clean();

        if ( function_exists( 'login_header' ) ) {
            login_header( __( 'Verifica in due passaggi', 'olobuild' ) );
            // Il markup del form di wp-login vive dentro #login: riusiamo i suoi stili.
            echo '<div style="margin-top:20px" class="olo2fa-box">' . $body . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
            login_footer( 'olo2fa_code' );
        } else {
            // Login partito fuori da wp-login.php (es. form di un tema): pagina minima autonoma.
            ?><!DOCTYPE html><html <?php language_attributes(); ?>><head>
            <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
            <title><?php esc_html_e( 'Verifica in due passaggi', 'olobuild' ); ?></title>
            <style>body{font-family:-apple-system,Segoe UI,Roboto,sans-serif;background:#f0f0f1;display:flex;justify-content:center;padding-top:8vh}
            .card{background:#fff;border:1px solid #c3c4c7;border-radius:4px;padding:26px;width:320px;box-shadow:0 1px 3px rgba(0,0,0,.04)}
            input[type=text]{width:100%;padding:6px 8px;font-size:18px;margin-top:4px}
            .button-primary{background:#2271b1;color:#fff;border:0;padding:8px 14px;border-radius:3px;cursor:pointer}
            #login_error{border-left:4px solid #d63638;background:#fff;padding:8px 12px;margin-bottom:14px;box-shadow:0 1px 1px rgba(0,0,0,.06)}
            .message{border-left:4px solid #72aee6;background:#fff;padding:8px 12px;margin-bottom:14px}</style>
            </head><body><div class="card"><h1 style="font-size:18px;margin-top:0"><?php esc_html_e( 'Verifica in due passaggi', 'olobuild' ); ?></h1>
            <?php echo $body; // phpcs:ignore WordPress.Security.EscapeOutput ?>
            </div></body></html><?php
        }
        exit;
    }

    // ── TOTP (RFC 6238) ──────────────────────────────────────────────────────

    private static function base32_encode( $bin ) {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $out      = '';
        $bits     = '';
        foreach ( str_split( $bin ) as $c ) {
            $bits .= str_pad( decbin( ord( $c ) ), 8, '0', STR_PAD_LEFT );
        }
        foreach ( str_split( $bits, 5 ) as $chunk ) {
            $out .= $alphabet[ bindec( str_pad( $chunk, 5, '0' ) ) ];
        }
        return $out;
    }

    private static function base32_decode( $b32 ) {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $b32      = strtoupper( preg_replace( '/[^A-Za-z2-7]/', '', $b32 ) );
        $bits     = '';
        for ( $i = 0; $i < strlen( $b32 ); $i++ ) {
            $pos = strpos( $alphabet, $b32[ $i ] );
            if ( $pos === false ) {
                return '';
            }
            $bits .= str_pad( decbin( $pos ), 5, '0', STR_PAD_LEFT );
        }
        $out = '';
        foreach ( str_split( $bits, 8 ) as $chunk ) {
            if ( strlen( $chunk ) === 8 ) {
                $out .= chr( bindec( $chunk ) );
            }
        }
        return $out;
    }

    /** Codice HOTP a 6 cifre per un dato contatore. */
    private static function hotp( $secret_bin, $counter ) {
        $hash = hash_hmac( 'sha1', pack( 'J', $counter ), $secret_bin, true );
        $off  = ord( substr( $hash, -1 ) ) & 0x0F;
        $int  = unpack( 'N', substr( $hash, $off, 4 ) )[1] & 0x7FFFFFFF;
        return str_pad( (string) ( $int % 1000000 ), 6, '0', STR_PAD_LEFT );
    }

    /**
     * Verifica un codice TOTP con finestra ±1 (90 s totali) e anti-replay:
     * un timeslot già speso non è riutilizzabile, anche se il codice è giusto.
     */
    private static function verify_totp( $user_id, $code, $secret_b32 = null ) {
        $conf = self::user_conf( $user_id );
        if ( $secret_b32 === null ) {
            $secret_b32 = self::decrypt( (string) ( $conf['secret'] ?? '' ) );
        }
        $secret_bin = self::base32_decode( $secret_b32 );
        if ( $secret_bin === '' ) {
            return false;
        }
        $slot = (int) floor( time() / 30 );
        $last = (int) ( $conf['last_slot'] ?? 0 );
        foreach ( [ 0, -1, 1 ] as $d ) {
            $candidate = $slot + $d;
            if ( $candidate <= $last ) {
                continue;
            }
            if ( hash_equals( self::hotp( $secret_bin, $candidate ), $code ) ) {
                $conf['last_slot'] = $candidate;
                update_user_meta( $user_id, self::META, $conf );
                return true;
            }
        }
        return false;
    }

    /** URI otpauth:// per QR code e inserimento manuale. */
    private static function otpauth_uri( $user, $secret_b32 ) {
        $issuer = wp_parse_url( home_url(), PHP_URL_HOST ) ?: 'WordPress';
        return 'otpauth://totp/' . rawurlencode( $issuer . ':' . $user->user_login )
            . '?secret=' . rawurlencode( $secret_b32 )
            . '&issuer=' . rawurlencode( $issuer )
            . '&algorithm=SHA1&digits=6&period=30';
    }

    // ── Cifratura del secret (chiave derivata dai salt WP) ───────────────────

    private static function crypt_key() {
        return hash( 'sha256', wp_salt( 'auth' ) . '|olo2fa', true );
    }

    private static function encrypt( $plain ) {
        if ( ! function_exists( 'openssl_encrypt' ) || ! in_array( 'aes-256-gcm', openssl_get_cipher_methods(), true ) ) {
            return 'raw:' . $plain;
        }
        $iv  = random_bytes( 12 );
        $tag = '';
        $ct  = openssl_encrypt( $plain, 'aes-256-gcm', self::crypt_key(), OPENSSL_RAW_DATA, $iv, $tag );
        return 'gcm:' . base64_encode( $iv . $tag . $ct );
    }

    private static function decrypt( $stored ) {
        if ( strpos( $stored, 'raw:' ) === 0 ) {
            return substr( $stored, 4 );
        }
        if ( strpos( $stored, 'gcm:' ) !== 0 ) {
            return '';
        }
        $raw = base64_decode( substr( $stored, 4 ), true );
        if ( $raw === false || strlen( $raw ) < 29 ) {
            return '';
        }
        $plain = openssl_decrypt( substr( $raw, 28 ), 'aes-256-gcm', self::crypt_key(), OPENSSL_RAW_DATA, substr( $raw, 0, 12 ), substr( $raw, 12, 16 ) );
        return $plain === false ? '' : $plain;
    }

    // ── Codice email ─────────────────────────────────────────────────────────

    /** Genera e invia un codice email; rate-limit 60 s tra invii. Ritorna true se inviato. */
    private static function send_email_code( $user ) {
        $meta = get_user_meta( $user->ID, self::META_EMAIL, true );
        if ( is_array( $meta ) && ( $meta['sent'] ?? 0 ) > time() - MINUTE_IN_SECONDS ) {
            return false;
        }
        $code = str_pad( (string) random_int( 0, 999999 ), 6, '0', STR_PAD_LEFT );
        update_user_meta( $user->ID, self::META_EMAIL, [
            'hash' => hash_hmac( 'sha256', $code, wp_salt() ),
            'exp'  => time() + self::EMAIL_TTL,
            'sent' => time(),
        ] );
        $site = wp_parse_url( home_url(), PHP_URL_HOST ) ?: get_bloginfo( 'name' );
        wp_mail(
            $user->user_email,
            '[' . $site . '] ' . __( 'Il tuo codice di accesso', 'olobuild' ),
            sprintf(
                /* translators: 1: code, 2: minutes */
                __( "Il tuo codice di verifica è: %1\$s\n\nVale %2\$d minuti. Se non hai tentato di accedere, ignora questa email e cambia la password.", 'olobuild' ),
                $code,
                (int) ( self::EMAIL_TTL / MINUTE_IN_SECONDS )
            )
        );
        return true;
    }

    private static function verify_email_code( $user_id, $code ) {
        $meta = get_user_meta( $user_id, self::META_EMAIL, true );
        if ( ! is_array( $meta ) || ( $meta['exp'] ?? 0 ) < time() ) {
            return false;
        }
        if ( ! hash_equals( (string) ( $meta['hash'] ?? '' ), hash_hmac( 'sha256', $code, wp_salt() ) ) ) {
            return false;
        }
        delete_user_meta( $user_id, self::META_EMAIL ); // monouso
        return true;
    }

    // ── Recovery codes ───────────────────────────────────────────────────────

    /** Genera N codici nuovi, salva gli hash nella config e ritorna i codici in chiaro. */
    private static function generate_recovery_codes( &$conf ) {
        $alphabet = '23456789ABCDEFGHJKMNPQRSTUVWXYZ'; // niente 0/O/1/I/L ambigui
        $codes    = [];
        $hashes   = [];
        for ( $i = 0; $i < self::RECOVERY_N; $i++ ) {
            $raw = '';
            for ( $j = 0; $j < 10; $j++ ) {
                $raw .= $alphabet[ random_int( 0, strlen( $alphabet ) - 1 ) ];
            }
            $codes[]  = substr( $raw, 0, 5 ) . '-' . substr( $raw, 5 );
            $hashes[] = hash_hmac( 'sha256', $raw, wp_salt() );
        }
        $conf['recovery'] = $hashes;
        return $codes;
    }

    private static function consume_recovery_code( $user, $code ) {
        $conf  = self::user_conf( $user->ID );
        $codes = isset( $conf['recovery'] ) && is_array( $conf['recovery'] ) ? $conf['recovery'] : [];
        if ( ! $codes ) {
            return false;
        }
        $norm = strtoupper( preg_replace( '/[^A-Za-z0-9]/', '', $code ) );
        $hash = hash_hmac( 'sha256', $norm, wp_salt() );
        foreach ( $codes as $i => $stored ) {
            if ( hash_equals( $stored, $hash ) ) {
                unset( $conf['recovery'][ $i ] );
                $conf['recovery'] = array_values( $conf['recovery'] );
                update_user_meta( $user->ID, self::META, $conf );
                self::audit( 'high', sprintf( __( 'Codice di recupero 2FA usato da %1$s (%2$d rimasti).', 'olobuild' ), $user->user_login, count( $conf['recovery'] ) ), $user );
                return true;
            }
        }
        return false;
    }

    // ── Cookie "dispositivo fidato" ──────────────────────────────────────────
    //
    // Firmato con i salt WP + un epoch per-utente: disattivare/azzerare la 2FA
    // cambia l'epoch e revoca tutti i browser ricordati in un colpo solo.

    private static function device_cookie_name() {
        return 'olo_2fa_td_' . COOKIEHASH;
    }

    private static function device_sig( $user_id, $exp, $epoch ) {
        return hash_hmac( 'sha256', $user_id . '|' . $exp . '|' . $epoch, wp_salt( 'auth' ) );
    }

    private static function issue_device_cookie( $user_id ) {
        $days = (int) self::get_settings()['remember_days'];
        if ( $days < 1 ) {
            return;
        }
        $conf  = self::user_conf( $user_id );
        $epoch = (int) ( $conf['epoch'] ?? 0 );
        $exp   = time() + $days * DAY_IN_SECONDS;
        setcookie(
            self::device_cookie_name(),
            $user_id . '|' . $exp . '|' . self::device_sig( $user_id, $exp, $epoch ),
            [ 'expires' => $exp, 'path' => COOKIEPATH ? COOKIEPATH : '/', 'secure' => is_ssl(), 'httponly' => true, 'samesite' => 'Lax' ]
        );
    }

    private static function has_valid_device_cookie( $user_id ) {
        if ( (int) self::get_settings()['remember_days'] < 1 ) {
            return false;
        }
        $raw = (string) ( $_COOKIE[ self::device_cookie_name() ] ?? '' );
        if ( $raw === '' ) {
            return false;
        }
        $parts = explode( '|', $raw );
        if ( count( $parts ) !== 3 ) {
            return false;
        }
        list( $uid, $exp, $sig ) = $parts;
        if ( (int) $uid !== (int) $user_id || (int) $exp < time() ) {
            return false;
        }
        $epoch = (int) ( self::user_conf( $user_id )['epoch'] ?? 0 );
        return hash_equals( self::device_sig( (int) $uid, (int) $exp, $epoch ), $sig );
    }

    // ── Obbligo per gli amministratori ───────────────────────────────────────

    public static function maybe_require_setup() {
        $s = self::get_settings();
        if ( empty( $s['require_admins'] ) || ! current_user_can( 'manage_options' ) ) {
            return;
        }
        if ( self::is_user_configured( get_current_user_id() ) || wp_doing_ajax() || wp_doing_cron() ) {
            return;
        }
        global $pagenow;
        if ( $pagenow === 'admin-post.php' || ( $_GET['page'] ?? '' ) === 'olo-2fa' ) {
            return;
        }
        wp_safe_redirect( add_query_arg( 'olo2fa_required', 1, self::page_url() ) );
        exit;
    }

    public static function required_notice() {
        if ( empty( $_GET['olo2fa_required'] ) ) {
            return;
        }
        echo '<div class="notice notice-warning"><p><strong>OLOsecurity:</strong> '
            . esc_html__( 'la verifica in due passaggi è obbligatoria per gli amministratori di questo sito. Configurala qui sotto per continuare.', 'olobuild' )
            . '</p></div>';
    }

    // ── Pagina di setup + sezioni profilo ────────────────────────────────────

    public static function register_page() {
        add_submenu_page(
            self::page_parent(),
            __( 'Verifica in due passaggi', 'olobuild' ),
            __( 'Verifica in due passaggi', 'olobuild' ),
            'read',
            'olo-2fa',
            [ __CLASS__, 'render_page' ]
        );
    }

    /** Per chi non gestisce gli utenti il menu Utenti non esiste: il genitore è il Profilo. */
    private static function page_parent() {
        return current_user_can( 'list_users' ) ? 'users.php' : 'profile.php';
    }

    public static function page_url() {
        return admin_url( self::page_parent() . '?page=olo-2fa' );
    }

    public static function enqueue_profile_assets( $hook ) {
        if ( ! in_array( $hook, [ 'users_page_olo-2fa', 'profile_page_olo-2fa' ], true ) ) {
            return;
        }
        $pending = self::user_conf( get_current_user_id() )['pending'] ?? null;
        if ( is_array( $pending ) && ( $pending['method'] ?? '' ) === 'totp' && ( $pending['exp'] ?? 0 ) > time() ) {
            wp_enqueue_script( 'olo-qrcode', OLOSEC_URL . 'assets/vendor/qrcode/qrcode.min.js', [], OLOSEC_VERSION, true );
            wp_add_inline_script( 'olo-qrcode', 'var el=document.getElementById("olo-2fa-qr");if(el&&window.QRCode){new QRCode(el,{text:el.getAttribute("data-uri"),width:180,height:180,correctLevel:QRCode.CorrectLevel.M});}' );
        }
    }

    /** Sezione nel profilo: solo stato + link alla pagina di setup (un form qui verrebbe annidato). */
    public static function render_profile( $user ) {
        if ( $user->ID !== get_current_user_id() ) {
            return;
        }
        $active = self::is_user_configured( $user->ID );
        $conf   = self::user_conf( $user->ID );
        ?>
        <h2 id="olo-2fa"><?php esc_html_e( 'Verifica in due passaggi (OLOsecurity)', 'olobuild' ); ?></h2>
        <table class="form-table" role="presentation"><tr>
            <th><?php esc_html_e( 'Stato', 'olobuild' ); ?></th>
            <td>
                <p style="margin-top:4px">
                <?php
                if ( $active ) {
                    echo '✔ <strong>' . esc_html__( 'Attiva', 'olobuild' ) . '</strong> — ';
                    echo ( ( $conf['method'] ?? '' ) === 'totp' )
                        ? esc_html__( 'app di autenticazione (TOTP)', 'olobuild' )
                        : esc_html__( 'codice via email', 'olobuild' );
                } else {
                    esc_html_e( 'Non attiva: la tua password da sola basta per entrare.', 'olobuild' );
                }
                ?>
                </p>
                <a class="button" href="<?php echo esc_url( self::page_url() ); ?>">
                    <?php $active ? esc_html_e( 'Gestisci', 'olobuild' ) : esc_html_e( 'Configura la verifica in due passaggi', 'olobuild' ); ?>
                </a>
            </td>
        </tr></table>
        <?php
    }

    /** Pagina dedicata Utenti → Verifica in due passaggi (setup self-service). */
    public static function render_page() {
        $user    = wp_get_current_user();
        $conf    = self::user_conf( $user->ID );
        $pending = isset( $conf['pending'] ) && is_array( $conf['pending'] ) && ( $conf['pending']['exp'] ?? 0 ) > time() ? $conf['pending'] : null;
        $active  = self::is_user_configured( $user->ID );
        $msg     = sanitize_key( $_GET['olo2fa_msg'] ?? '' );

        // Codici di recupero appena generati: si mostrano UNA volta sola.
        $fresh_codes = get_transient( 'olo_2fa_codes_' . $user->ID );
        if ( $fresh_codes ) {
            delete_transient( 'olo_2fa_codes_' . $user->ID );
        }
        ?>
        <div class="wrap">
        <h1><?php esc_html_e( 'Verifica in due passaggi (OLOsecurity)', 'olobuild' ); ?></h1>
        <?php if ( $msg === 'wrong' ) : ?>
            <div class="notice notice-error inline"><p><?php esc_html_e( 'Codice non valido: riprova.', 'olobuild' ); ?></p></div>
        <?php elseif ( $msg === 'disabled' ) : ?>
            <div class="notice notice-info inline"><p><?php esc_html_e( 'Verifica in due passaggi disattivata.', 'olobuild' ); ?></p></div>
        <?php elseif ( $msg === 'badpwd' ) : ?>
            <div class="notice notice-error inline"><p><?php esc_html_e( 'Password attuale errata: 2FA non disattivata.', 'olobuild' ); ?></p></div>
        <?php endif; ?>

        <?php if ( $fresh_codes && is_array( $fresh_codes ) ) : ?>
            <div class="notice notice-success inline" style="padding:12px 14px">
                <p style="margin-top:0"><strong><?php esc_html_e( 'Verifica in due passaggi attiva.', 'olobuild' ); ?></strong>
                <?php esc_html_e( 'Salva subito questi codici di recupero: non verranno mostrati mai più. Ognuno vale un solo accesso (telefono perso, email irraggiungibile…).', 'olobuild' ); ?></p>
                <pre style="background:#f6f7f7;padding:10px 12px;font-size:14px;line-height:1.8;user-select:all"><?php echo esc_html( implode( "\n", $fresh_codes ) ); ?></pre>
            </div>
        <?php endif; ?>

        <table class="form-table" role="presentation">
        <?php if ( $active ) : ?>
            <tr>
                <th><?php esc_html_e( 'Stato', 'olobuild' ); ?></th>
                <td>
                    <p style="margin-top:4px">✔ <strong><?php esc_html_e( 'Attiva', 'olobuild' ); ?></strong> —
                    <?php
                    echo ( $conf['method'] === 'totp' )
                        ? esc_html__( 'app di autenticazione (TOTP)', 'olobuild' )
                        : esc_html__( 'codice via email', 'olobuild' );
                    printf( ' · ' . esc_html__( '%d codici di recupero rimasti', 'olobuild' ), count( $conf['recovery'] ?? [] ) );
                    ?></p>
                    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline-block;margin-right:8px">
                        <?php wp_nonce_field( 'olo_2fa' ); ?>
                        <input type="hidden" name="action" value="olo_2fa_regen">
                        <button type="submit" class="button"><?php esc_html_e( 'Rigenera codici di recupero', 'olobuild' ); ?></button>
                    </form>
                    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline-block">
                        <?php wp_nonce_field( 'olo_2fa' ); ?>
                        <input type="hidden" name="action" value="olo_2fa_disable">
                        <input type="password" name="olo_2fa_pwd" placeholder="<?php esc_attr_e( 'Password attuale', 'olobuild' ); ?>" autocomplete="current-password" style="vertical-align:baseline">
                        <button type="submit" class="button"><?php esc_html_e( 'Disattiva 2FA', 'olobuild' ); ?></button>
                    </form>
                </td>
            </tr>
        <?php elseif ( $pending ) : ?>
            <tr>
                <th><?php esc_html_e( 'Completa l\'attivazione', 'olobuild' ); ?></th>
                <td>
                    <?php if ( $pending['method'] === 'totp' ) :
                        $secret = self::decrypt( $pending['secret'] );
                        ?>
                        <p style="margin-top:4px"><?php esc_html_e( '1. Inquadra il QR con la tua app (Google Authenticator, Microsoft Authenticator, Authy, Bitwarden…):', 'olobuild' ); ?></p>
                        <div id="olo-2fa-qr" data-uri="<?php echo esc_attr( self::otpauth_uri( $user, $secret ) ); ?>" style="margin:10px 0;background:#fff;display:inline-block;padding:10px;border:1px solid #c3c4c7"></div>
                        <p><?php esc_html_e( 'Oppure inserisci la chiave a mano:', 'olobuild' ); ?>
                            <code style="user-select:all"><?php echo esc_html( trim( chunk_split( $secret, 4, ' ' ) ) ); ?></code></p>
                        <p><?php esc_html_e( '2. Inserisci il codice a 6 cifre generato dall\'app:', 'olobuild' ); ?></p>
                    <?php else : ?>
                        <p style="margin-top:4px"><?php echo esc_html( sprintf( __( 'Ti abbiamo inviato un codice a 6 cifre a %s. Inseriscilo per confermare:', 'olobuild' ), $user->user_email ) ); ?></p>
                    <?php endif; ?>
                    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline-block;margin-right:8px">
                        <?php wp_nonce_field( 'olo_2fa' ); ?>
                        <input type="hidden" name="action" value="olo_2fa_confirm">
                        <input type="text" name="olo_2fa_code" inputmode="numeric" autocomplete="one-time-code" size="8" placeholder="123456">
                        <button type="submit" class="button button-primary"><?php esc_html_e( 'Conferma e attiva', 'olobuild' ); ?></button>
                    </form>
                    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline-block">
                        <?php wp_nonce_field( 'olo_2fa' ); ?>
                        <input type="hidden" name="action" value="olo_2fa_cancel">
                        <button type="submit" class="button-link" style="color:#b32d2e"><?php esc_html_e( 'Annulla', 'olobuild' ); ?></button>
                    </form>
                </td>
            </tr>
        <?php else : ?>
            <tr>
                <th><?php esc_html_e( 'Proteggi il tuo account', 'olobuild' ); ?></th>
                <td>
                    <p style="margin-top:4px;max-width:640px"><?php esc_html_e( 'Con la verifica in due passaggi, la password da sola non basta per entrare: serve anche un codice usa-e-getta. Scegli il metodo:', 'olobuild' ); ?></p>
                    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                        <?php wp_nonce_field( 'olo_2fa' ); ?>
                        <input type="hidden" name="action" value="olo_2fa_setup">
                        <label style="display:block;margin:6px 0">
                            <input type="radio" name="olo_2fa_method" value="totp" checked>
                            <strong><?php esc_html_e( 'App di autenticazione (consigliato)', 'olobuild' ); ?></strong> —
                            <?php esc_html_e( 'codici generati sul tuo telefono, funziona anche offline', 'olobuild' ); ?>
                        </label>
                        <label style="display:block;margin:6px 0 12px">
                            <input type="radio" name="olo_2fa_method" value="email">
                            <strong><?php esc_html_e( 'Codice via email', 'olobuild' ); ?></strong> —
                            <?php esc_html_e( 'nessuna app da installare, il codice arriva nella tua casella', 'olobuild' ); ?>
                        </label>
                        <button type="submit" class="button button-primary"><?php esc_html_e( 'Attiva la verifica in due passaggi', 'olobuild' ); ?></button>
                    </form>
                </td>
            </tr>
        <?php endif; ?>
        </table>
        </div>
        <?php
    }

    /** Vista per l'admin che modifica un ALTRO utente: stato + azzeramento di emergenza. */
    public static function render_profile_admin( $user ) {
        if ( ! current_user_can( 'manage_options' ) || $user->ID === get_current_user_id() ) {
            return;
        }
        $active = self::is_user_configured( $user->ID );
        ?>
        <h2><?php esc_html_e( 'Verifica in due passaggi (OLOsecurity)', 'olobuild' ); ?></h2>
        <table class="form-table" role="presentation"><tr>
            <th><?php esc_html_e( 'Stato', 'olobuild' ); ?></th>
            <td>
                <?php if ( $active ) :
                    // Link con nonce, NON un form: qui siamo dentro il form di modifica utente.
                    $reset_url = wp_nonce_url(
                        add_query_arg( [ 'action' => 'olo_2fa_reset', 'olo_2fa_user' => $user->ID ], admin_url( 'admin-post.php' ) ),
                        'olo_2fa'
                    );
                    ?>
                    <p style="margin-top:4px">✔ <?php esc_html_e( 'Attiva per questo utente.', 'olobuild' ); ?></p>
                    <a class="button" href="<?php echo esc_url( $reset_url ); ?>"><?php esc_html_e( 'Azzera la 2FA di questo utente (telefono perso)', 'olobuild' ); ?></a>
                <?php else : ?>
                    <p style="margin-top:4px"><?php esc_html_e( 'Non attiva. L\'utente può configurarla dal proprio profilo.', 'olobuild' ); ?></p>
                <?php endif; ?>
            </td>
        </tr></table>
        <?php
    }

    // ── Azioni admin-post ────────────────────────────────────────────────────

    private static function back_to_profile( $msg = '' ) {
        $url = self::page_url();
        if ( $msg ) {
            $url = add_query_arg( 'olo2fa_msg', $msg, $url );
        }
        wp_safe_redirect( $url );
        exit;
    }

    private static function check_action_prereqs() {
        if ( ! is_user_logged_in() ) {
            wp_die( esc_html__( 'Accesso richiesto.', 'olobuild' ), 403 );
        }
        check_admin_referer( 'olo_2fa' );
    }

    public static function handle_setup() {
        self::check_action_prereqs();
        $method = ( $_POST['olo_2fa_method'] ?? '' ) === 'email' ? 'email' : 'totp';
        $uid    = get_current_user_id();
        $conf   = self::user_conf( $uid );

        $conf['pending'] = [
            'method' => $method,
            'secret' => $method === 'totp' ? self::encrypt( self::base32_encode( random_bytes( 20 ) ) ) : '',
            'exp'    => time() + self::PENDING_TTL,
        ];
        update_user_meta( $uid, self::META, $conf );

        if ( $method === 'email' ) {
            self::send_email_code( wp_get_current_user() );
        }
        self::back_to_profile();
    }

    public static function handle_confirm() {
        self::check_action_prereqs();
        $uid     = get_current_user_id();
        $user    = wp_get_current_user();
        $conf    = self::user_conf( $uid );
        $pending = isset( $conf['pending'] ) && is_array( $conf['pending'] ) ? $conf['pending'] : null;
        if ( ! $pending || ( $pending['exp'] ?? 0 ) < time() ) {
            self::back_to_profile();
        }

        $code = preg_replace( '/\D+/', '', (string) wp_unslash( $_POST['olo_2fa_code'] ?? '' ) );
        $ok   = false;
        if ( $pending['method'] === 'totp' ) {
            $ok = self::verify_totp( $uid, $code, self::decrypt( $pending['secret'] ) );
        } else {
            $ok = self::verify_email_code( $uid, $code );
        }
        if ( ! $ok ) {
            self::back_to_profile( 'wrong' );
        }

        // Rileggi la config: verify_totp ha appena registrato il last_slot consumato.
        $fresh = self::user_conf( $uid );
        $new   = [
            'method'    => $pending['method'],
            'secret'    => $pending['method'] === 'totp' ? $pending['secret'] : '',
            'epoch'     => (int) ( $conf['epoch'] ?? 0 ) + 1,
            'last_slot' => (int) ( $fresh['last_slot'] ?? 0 ),
            'since'     => time(),
        ];
        $codes = self::generate_recovery_codes( $new );
        update_user_meta( $uid, self::META, $new );
        set_transient( 'olo_2fa_codes_' . $uid, $codes, 5 * MINUTE_IN_SECONDS );

        self::audit( 'info', sprintf( __( 'Verifica in due passaggi attivata da %1$s (metodo: %2$s).', 'olobuild' ), $user->user_login, $new['method'] ), $user );
        self::back_to_profile();
    }

    public static function handle_cancel() {
        self::check_action_prereqs();
        $uid  = get_current_user_id();
        $conf = self::user_conf( $uid );
        unset( $conf['pending'] );
        if ( self::is_user_configured( $uid ) || $conf ) {
            update_user_meta( $uid, self::META, $conf );
        } else {
            delete_user_meta( $uid, self::META );
        }
        self::back_to_profile();
    }

    public static function handle_disable() {
        self::check_action_prereqs();
        $user = wp_get_current_user();
        $pwd  = (string) wp_unslash( $_POST['olo_2fa_pwd'] ?? '' );
        if ( ! wp_check_password( $pwd, $user->user_pass, $user->ID ) ) {
            self::back_to_profile( 'badpwd' );
        }
        delete_user_meta( $user->ID, self::META );
        delete_user_meta( $user->ID, self::META_EMAIL );
        self::audit( 'warn', sprintf( __( 'Verifica in due passaggi disattivata da %s.', 'olobuild' ), $user->user_login ), $user );
        self::back_to_profile( 'disabled' );
    }

    public static function handle_regen() {
        self::check_action_prereqs();
        $uid  = get_current_user_id();
        $conf = self::user_conf( $uid );
        if ( ! self::is_user_configured( $uid ) ) {
            self::back_to_profile();
        }
        $codes = self::generate_recovery_codes( $conf );
        update_user_meta( $uid, self::META, $conf );
        set_transient( 'olo_2fa_codes_' . $uid, $codes, 5 * MINUTE_IN_SECONDS );
        self::audit( 'info', sprintf( __( 'Codici di recupero 2FA rigenerati da %s.', 'olobuild' ), wp_get_current_user()->user_login ), wp_get_current_user() );
        self::back_to_profile();
    }

    /** Azzeramento da parte di un admin per un altro utente (telefono perso). */
    public static function handle_reset() {
        self::check_action_prereqs();
        $target = (int) ( $_REQUEST['olo_2fa_user'] ?? 0 ); // arriva via link GET con nonce
        if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'edit_user', $target ) || $target === get_current_user_id() ) {
            wp_die( esc_html__( 'Permessi insufficienti.', 'olobuild' ), 403 );
        }
        delete_user_meta( $target, self::META );
        delete_user_meta( $target, self::META_EMAIL );
        delete_user_meta( $target, self::META_LOGIN );
        $t = get_userdata( $target );
        self::audit( 'warn', sprintf( __( '2FA azzerata dall\'amministratore %1$s per l\'utente %2$s.', 'olobuild' ), wp_get_current_user()->user_login, $t ? $t->user_login : ( '#' . $target ) ) );
        wp_safe_redirect( get_edit_user_link( $target ) );
        exit;
    }

    // ── Impostazioni (campi nella pagina OLOsecurity, come gli altri moduli) ─

    public static function save_settings( $post ) {
        $s = [
            'enabled'               => empty( $post['olo_2fa_enabled'] ) ? 0 : 1,
            'require_admins'        => empty( $post['olo_2fa_require_admins'] ) ? 0 : 1,
            'email_fallback'        => empty( $post['olo_2fa_email_fallback'] ) ? 0 : 1,
            'remember_days'         => max( 0, min( 365, (int) ( $post['olo_2fa_remember'] ?? 30 ) ) ),
            'disable_app_passwords' => empty( $post['olo_2fa_no_app_pwd'] ) ? 0 : 1,
        ];
        update_option( self::OPT, $s, false );
    }

    public static function render_settings_fields() {
        $s        = self::get_settings();
        $conflict = self::conflicting_plugin();

        if ( $conflict ) {
            echo '<p style="color:#996800">⚠ ' . esc_html( sprintf(
                __( 'È attivo un altro sistema di verifica in due passaggi (%s): la 2FA di OLOsecurity si è disattivata da sola per non interferire. Le impostazioni qui sotto torneranno effettive disattivando quel plugin.', 'olobuild' ),
                $conflict
            ) ) . '</p>';
        } elseif ( self::coexisting_plugin() ) {
            echo '<p style="color:#996800">⚠ ' . esc_html( sprintf(
                __( 'È attivo anche %s, che include una propria 2FA facoltativa: le due convivono, ma evita di attivarle entrambe per lo stesso utente (doppia richiesta di codice).', 'olobuild' ),
                self::coexisting_plugin()
            ) ) . '</p>';
        }

        // Copertura: quanti amministratori la usano davvero.
        $admins  = get_users( [ 'role' => 'administrator', 'fields' => [ 'ID', 'user_login' ] ] );
        $without = [];
        foreach ( $admins as $a ) {
            if ( ! self::is_user_configured( $a->ID ) ) {
                $without[] = $a->user_login;
            }
        }
        ?>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><?php esc_html_e( 'Verifica in due passaggi', 'olobuild' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="olo_2fa_enabled" value="1" <?php checked( ! empty( $s['enabled'] ) ); ?>>
                        <?php esc_html_e( 'Permetti agli utenti di proteggere il proprio account con la 2FA (si configura dal profilo utente)', 'olobuild' ); ?>
                    </label>
                    <p class="description">
                        <?php
                        echo esc_html( sprintf(
                            /* translators: 1: count with 2FA, 2: total admins */
                            __( 'Amministratori con 2FA attiva: %1$d su %2$d.', 'olobuild' ),
                            count( $admins ) - count( $without ),
                            count( $admins )
                        ) );
                        if ( $without ) {
                            echo ' ' . esc_html( sprintf( __( 'Senza 2FA: %s.', 'olobuild' ), implode( ', ', $without ) ) );
                        }
                        ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Obbligo amministratori', 'olobuild' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="olo_2fa_require_admins" value="1" <?php checked( ! empty( $s['require_admins'] ) ); ?>>
                        <?php esc_html_e( 'Obbligatoria per gli amministratori: chi non l\'ha ancora configurata viene portato al proprio profilo a ogni accesso in bacheca', 'olobuild' ); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Riserva via email', 'olobuild' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="olo_2fa_email_fallback" value="1" <?php checked( ! empty( $s['email_fallback'] ) ); ?>>
                        <?php esc_html_e( 'Chi usa l\'app può farsi inviare un codice via email come riserva (telefono scarico o perso)', 'olobuild' ); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Ricorda il browser', 'olobuild' ); ?></th>
                <td>
                    <input type="number" min="0" max="365" name="olo_2fa_remember" value="<?php echo esc_attr( $s['remember_days'] ); ?>" class="small-text">
                    <?php esc_html_e( 'giorni senza richiedere il codice sullo stesso browser (0 = chiedi sempre)', 'olobuild' ); ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Application Passwords', 'olobuild' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="olo_2fa_no_app_pwd" value="1" <?php checked( ! empty( $s['disable_app_passwords'] ) ); ?>>
                        <?php esc_html_e( 'Disabilita le Application Passwords di WordPress su tutto il sito (autenticano via REST con la sola password: aggirano la 2FA)', 'olobuild' ); ?>
                    </label>
                </td>
            </tr>
        </table>
        <?php
    }

    // ── Helper ───────────────────────────────────────────────────────────────

    private static function audit( $severity, $message, $user = null ) {
        if ( ! class_exists( 'Olo_Security_Audit' ) ) {
            return;
        }
        $args = [];
        if ( $user instanceof WP_User ) {
            $args = [ 'user_id' => $user->ID, 'user_login' => $user->user_login ];
        }
        Olo_Security_Audit::log( '2fa', $message, $severity, $args );
    }
}
