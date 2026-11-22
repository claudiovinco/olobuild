<?php
/**
 * La licenza di OLObuild (1.4.1345). Senza una chiave valida e attivata su questo sito il builder, la configurazione
 * guidata e l'importazione dei temi non partono: al loro posto c'è la pagina «Licenza» con la schermata «Collega
 * OLObuild». Le pagine già pubblicate restano SEMPRE visibili: il disegno del sito non guarda mai la licenza.
 *
 * Le regole (utente, 22 nov 2026): una chiave per sito, anche la Free (gratuita, si chiede con l'email); una licenza
 * annuale scaduta non blocca mai il builder, ferma solo gli aggiornamenti; una chiave revocata blocca il builder, non il
 * sito. Valgono anche le chiavi di OLObooking (OLObooking porta con sé OLObuild Pro).
 *
 * Come: la chiave (OLO-<dati>.<firma>, Ed25519) si verifica qui senza internet con la chiave pubblica del server delle
 * licenze; al salvataggio il sito si attiva sul server (POST /activate: uno slot per sito) e una volta alla settimana
 * si riverifica (POST /verify-key). Se il server non risponde vale l'ultima risposta. Lo zip scaricato dal link
 * dell'email porta la chiave in licenza-olotheme.php: alla prima apertura dell'amministrazione si attiva da sola.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

final class Olobuild_Licenza {

    const OPT        = 'olobuild_licenza';
    const OPT_ZIP    = 'olobuild_licenza_zip';
    const SERVER     = 'https://license.clod.eu/wp-json/olo-license/v1';
    const NEGOZIO    = 'https://olotheme.com/wp-json/olo-store/v1';
    const PUBLIC_KEY = 'uo31ZjuXYI8fBROXSKutj3Ab1BqUXvnHyvW2Ptsk9yY=';
    const PRODOTTI   = [ 'olobuild', 'olo-booking' ];
    const PAGE       = 'olobuild-licenza';
    const CRON       = 'olobuild_licenza_verifica';
    // Le risposte del server che fermano il builder (scaduta no: ferma solo gli aggiornamenti).
    const BLOCCANTI  = [ 'revoked', 'suspended', 'pending_block', 'not_found', 'bad_signature', 'wrong_product', 'max_sites_reached' ];

    public static function init() {
        add_action( 'admin_init', [ __CLASS__, 'importa_dallo_zip' ], 5 );
        add_action( 'admin_init', [ __CLASS__, 'porta_alla_licenza' ], 6 );
        add_action( 'admin_menu', [ __CLASS__, 'menu' ], 30 );
        add_action( 'admin_post_olobuild_licenza', [ __CLASS__, 'azione' ] );
        add_action( self::CRON, [ __CLASS__, 'verifica' ] );
        add_action( 'admin_notices', [ __CLASS__, 'avviso' ] );
    }

    /* ─── Lo stato ────────────────────────────────────────────────────────── */

    private static function dati() {
        $d = get_option( self::OPT, [] );
        return array_merge( [ 'chiave' => '', 'sito' => '', 'stato' => '', 'prodotto' => '', 'piano' => '', 'moduli' => [], 'scade' => '', 'attivata' => 0, 'verificata' => 0 ], is_array( $d ) ? $d : [] );
    }

    private static function salva_dati( array $d ) {
        update_option( self::OPT, $d, true );
    }

    /** L'indirizzo di questo sito come lo scrive il server delle licenze (https, minuscolo, senza / finale). */
    public static function sito() {
        return strtolower( preg_replace( '#^http://#i', 'https://', untrailingslashit( home_url() ) ) );
    }

    /** I dati firmati di una chiave, verificati con la chiave pubblica; null se la firma non torna. */
    public static function leggi_chiave( $key ) {
        $key = trim( (string) $key );
        if ( ! preg_match( '/^OLO-([A-Za-z0-9_-]+)\.([A-Za-z0-9_-]+)$/', $key, $m ) || ! function_exists( 'sodium_crypto_sign_verify_detached' ) ) return null;
        $dec = function ( $s ) {
            $s   = strtr( $s, '-_', '+/' );
            $pad = strlen( $s ) % 4;
            return base64_decode( $pad ? $s . str_repeat( '=', 4 - $pad ) : $s, true );
        };
        $sig = $dec( $m[2] );
        $pub = base64_decode( self::PUBLIC_KEY, true );
        if ( ! is_string( $sig ) || strlen( $sig ) !== 64 || ! is_string( $pub ) ) return null;
        try {
            if ( ! sodium_crypto_sign_verify_detached( $sig, $m[1], $pub ) ) return null;
        } catch ( \Throwable $e ) {
            return null;
        }
        $data = json_decode( (string) $dec( $m[1] ), true );
        return is_array( $data ) ? $data : null;
    }

    /** La chiave di OLObooking su questo sito, se c'è (vale anche per OLObuild). */
    private static function chiave_booking() {
        if ( ! class_exists( 'Olo_License_Manager' ) || ! method_exists( 'Olo_License_Manager', 'get_license_key' ) ) return '';
        return (string) Olo_License_Manager::get_license_key();
    }

    /** La chiave in uso: quella di OLObuild, o quella di OLObooking. */
    public static function chiave() {
        $d = self::dati();
        return '' !== $d['chiave'] ? (string) $d['chiave'] : self::chiave_booking();
    }

    /** I moduli della licenza in uso (aggiornamenti, olosecurity, temi-pro, ololang, olosocial, whitelabel). */
    public static function moduli() {
        $d = self::dati();
        if ( '' !== $d['chiave'] ) return (array) $d['moduli'];
        $b = self::leggi_chiave( self::chiave_booking() );
        // OLObooking porta OLObuild Pro
        return $b ? [ 'aggiornamenti', 'olosecurity', 'temi-pro', 'ololang', 'olosocial' ] : [];
    }

    /** Il builder si può usare? Chiave di OLObuild valida e attivata su questo sito, o una chiave di OLObooking. */
    public static function builder_attivo() {
        static $si = null;
        if ( null !== $si ) return $si;
        $d = self::dati();
        if ( '' !== $d['chiave'] ) {
            $p  = self::leggi_chiave( $d['chiave'] );
            $si = $p && in_array( (string) ( $p['prd'] ?? '' ), self::PRODOTTI, true ) && self::sito() === (string) $d['sito']
                && ! in_array( (string) $d['stato'], self::BLOCCANTI, true );
            return $si;
        }
        $b  = self::leggi_chiave( self::chiave_booking() );
        $si = $b && 'olo-booking' === (string) ( $b['prd'] ?? '' );
        return $si;
    }

    /* ─── Il server delle licenze ─────────────────────────────────────────── */

    /** POST al server: [ codice, dati ] o WP_Error se non risponde. */
    private static function chiama( $url, array $body ) {
        $r = wp_remote_post( $url, [
            'timeout' => 12,
            'headers' => [ 'Content-Type' => 'application/json', 'Accept' => 'application/json' ],
            'body'    => wp_json_encode( $body ),
        ] );
        if ( is_wp_error( $r ) ) return $r;
        $d = json_decode( (string) wp_remote_retrieve_body( $r ), true );
        return [ (int) wp_remote_retrieve_response_code( $r ), is_array( $d ) ? $d : [] ];
    }

    /** Il messaggio per chi usa il sito, da un codice del server. */
    private static function messaggio( $codice ) {
        $m = [
            'max_sites_reached' => __( 'Questa chiave è già attiva su un altro sito: ogni chiave vale per un sito. Chiedine una nuova qui sotto, oppure disattivala sull\'altro sito.', 'olobuild' ),
            'revoked'           => __( 'Questa chiave è stata revocata. Scrivici a info@olotheme.com.', 'olobuild' ),
            'suspended'         => __( 'Questa chiave è sospesa. Scrivici a info@olotheme.com.', 'olobuild' ),
            'not_found'         => __( 'Questa chiave non risulta fra le nostre licenze.', 'olobuild' ),
            'bad_signature'     => __( 'La chiave non è valida: controlla di averla copiata tutta.', 'olobuild' ),
            'wrong_product'     => __( 'Questa chiave è di un altro software OLOtheme.', 'olobuild' ),
            'non_risponde'      => __( 'Il server delle licenze non risponde: riprova tra qualche minuto.', 'olobuild' ),
        ];
        return $m[ $codice ] ?? __( 'La chiave non è stata attivata: riprova, o scrivici a info@olotheme.com.', 'olobuild' );
    }

    /**
     * Salva una chiave e attiva questo sito sul server. Una chiave nuova al posto di un'altra (la Pro al posto della
     * Free) libera prima lo slot della vecchia. true, o WP_Error col messaggio per chi usa il sito.
     */
    public static function salva_chiave( $key ) {
        $key = trim( preg_replace( '/\s+/', '', (string) $key ) );
        $p   = self::leggi_chiave( $key );
        if ( ! $p ) return new WP_Error( 'bad_signature', self::messaggio( 'bad_signature' ) );
        if ( ! in_array( (string) ( $p['prd'] ?? '' ), self::PRODOTTI, true ) ) return new WP_Error( 'wrong_product', self::messaggio( 'wrong_product' ) );
        $r = self::chiama( self::SERVER . '/activate', [
            'license_key'    => $key,
            'site_url'       => home_url(),
            'wp_version'     => get_bloginfo( 'version' ),
            'plugin_version' => defined( 'OLOBUILD_VERSION' ) ? OLOBUILD_VERSION : '',
        ] );
        if ( is_wp_error( $r ) ) return new WP_Error( 'non_risponde', self::messaggio( 'non_risponde' ) );
        if ( 200 !== $r[0] || empty( $r[1]['ok'] ) ) {
            $code = sanitize_key( (string) ( $r[1]['code'] ?? '' ) );
            return new WP_Error( $code ?: 'rifiutata', self::messaggio( $code ) );
        }
        $vecchia = self::dati();
        if ( '' !== $vecchia['chiave'] && $vecchia['chiave'] !== $key ) self::libera( $vecchia['chiave'] );
        $a = $r[1];
        self::salva_dati( [
            'chiave'     => ! empty( $a['license_key'] ) ? (string) $a['license_key'] : $key,
            'sito'       => self::sito(),
            'stato'      => 'active',
            'prodotto'   => (string) ( $a['product'] ?? $p['prd'] ),
            'piano'      => (string) ( $a['plan'] ?? '' ),
            'moduli'     => array_values( array_map( 'strval', (array) ( $a['modules'] ?? [] ) ) ),
            'scade'      => (string) ( $a['expires_at'] ?? '' ),
            'attivata'   => time(),
            'verificata' => time(),
        ] );
        if ( ! wp_next_scheduled( self::CRON ) ) wp_schedule_event( time() + WEEK_IN_SECONDS, 'weekly', self::CRON );
        delete_site_transient( 'update_plugins' ); // gli aggiornamenti si richiedono con la chiave nuova
        return true;
    }

    /** Libera lo slot di una chiave su questo sito (best effort: se il server non risponde, lo slot resta). */
    private static function libera( $key ) {
        self::chiama( self::SERVER . '/deactivate', [ 'license_key' => (string) $key, 'site_url' => home_url() ] );
    }

    /** La verifica della settimana (e di «Verifica ora»): aggiorna stato, piano e scadenza; riattiva se serve. */
    public static function verifica() {
        $d = self::dati();
        if ( '' === $d['chiave'] ) return;
        $r = self::chiama( self::SERVER . '/verify-key', [ 'key' => $d['chiave'], 'site' => home_url() ] );
        if ( is_wp_error( $r ) || 200 !== $r[0] ) return; // vale l'ultima risposta
        $v = $r[1];
        if ( ! empty( $v['license_key'] ) && $v['license_key'] !== $d['chiave'] && self::leggi_chiave( $v['license_key'] ) ) $d['chiave'] = (string) $v['license_key'];
        if ( ! empty( $v['valid'] ) ) {
            $d['stato']   = 'active';
            $d['piano']   = (string) ( $v['plan'] ?? $d['piano'] );
            $d['moduli']  = array_values( array_map( 'strval', (array) ( $v['modules'] ?? $d['moduli'] ) ) );
            $d['scade']   = (string) ( $v['expires'] ?? '' );
        } else {
            $motivo = sanitize_key( (string) ( $v['reason'] ?? 'not_found' ) );
            if ( 'not_activated' === $motivo && self::sito() === (string) $d['sito'] ) {
                $ok = self::salva_chiave( $d['chiave'] );
                if ( true === $ok ) return;
                $motivo = is_wp_error( $ok ) ? $ok->get_error_code() : $motivo;
            }
            $d['stato'] = 'expired' === $motivo ? 'expired' : $motivo;
        }
        $d['verificata'] = time();
        self::salva_dati( $d );
    }

    /** Disattiva questo sito: libera lo slot sul server e toglie la chiave. */
    public static function disattiva() {
        $d = self::dati();
        if ( '' !== $d['chiave'] ) self::libera( $d['chiave'] );
        delete_option( self::OPT );
        wp_clear_scheduled_hook( self::CRON );
    }

    /** La chiave che viene nello zip scaricato dal link dell'email: si attiva da sola, una volta sola. */
    public static function importa_dallo_zip() {
        if ( ! current_user_can( 'manage_options' ) || '' !== self::dati()['chiave'] ) return;
        $file = OLOBUILD_PATH . 'licenza-olotheme.php';
        if ( ! is_readable( $file ) ) return;
        $a   = include $file;
        $key = is_array( $a ) ? (string) ( $a['key'] ?? '' ) : '';
        if ( '' === $key || get_option( self::OPT_ZIP ) === md5( $key ) ) return; // provata già: non a ogni pagina
        update_option( self::OPT_ZIP, md5( $key ), false );
        self::salva_chiave( $key );
    }

    /* ─── Il blocco ───────────────────────────────────────────────────────── */

    /** Senza licenza il builder e la configurazione guidata portano alla pagina «Licenza». */
    public static function porta_alla_licenza() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- solo instradamento
        $page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
        if ( ! in_array( $page, [ 'olobuilder-templates', 'olo-setup' ], true ) || self::builder_attivo() ) return;
        if ( wp_doing_ajax() ) return;
        wp_safe_redirect( add_query_arg( [ 'page' => self::PAGE, 'dopo' => $page ], admin_url( 'admin.php' ) ) );
        exit;
    }

    /** Per le rotte REST di importazione: true, o l'errore che dice di collegare OLObuild. */
    public static function permesso() {
        if ( self::builder_attivo() ) return true;
        return new WP_Error( 'olobuild_licenza', __( 'Per importare temi e blocchi serve la chiave di OLObuild: la trovi in Olobuild › Licenza.', 'olobuild' ), [ 'status' => 403 ] );
    }

    /** Sulle pagine di OLObuild, finché non c'è la chiave, una riga che porta alla pagina «Licenza». */
    public static function avviso() {
        if ( ! current_user_can( 'manage_options' ) || self::builder_attivo() ) return;
        $s = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
        if ( ! $s || ( false === strpos( (string) $s->id, 'olobuild' ) && 'plugins' !== $s->id ) || false !== strpos( (string) $s->id, self::PAGE ) ) return;
        echo '<div class="notice notice-warning"><p><strong>' . esc_html__( 'Collega OLObuild', 'olobuild' ) . '</strong> · '
            . esc_html__( 'Per usare il builder serve la chiave: è gratuita, una per sito.', 'olobuild' )
            . ' <a href="' . esc_url( admin_url( 'admin.php?page=' . self::PAGE ) ) . '">' . esc_html__( 'Collega ora', 'olobuild' ) . '</a></p></div>';
    }

    /* ─── La pagina «Licenza» ─────────────────────────────────────────────── */

    public static function menu() {
        add_submenu_page( 'olobuild', __( 'Licenza', 'olobuild' ), __( 'Licenza', 'olobuild' ), 'manage_options', self::PAGE, [ __CLASS__, 'pagina' ] );
    }

    /** Le azioni della pagina: attiva una chiave, chiedi la chiave gratuita, verifica, disattiva. */
    public static function azione() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( esc_html__( 'Non hai i permessi.', 'olobuild' ) );
        check_admin_referer( 'olobuild_licenza' );
        $cosa = isset( $_POST['cosa'] ) ? sanitize_key( wp_unslash( $_POST['cosa'] ) ) : '';
        $dopo = isset( $_POST['dopo'] ) ? sanitize_key( wp_unslash( $_POST['dopo'] ) ) : '';
        $esito = [];
        if ( 'attiva' === $cosa ) {
            $r     = self::salva_chiave( isset( $_POST['chiave'] ) ? sanitize_text_field( wp_unslash( $_POST['chiave'] ) ) : '' );
            $esito = true === $r ? [ 'ok' => 'attivata' ] : [ 'errore' => $r->get_error_code() ];
            if ( true === $r && in_array( $dopo, [ 'olobuilder-templates', 'olo-setup' ], true ) ) {
                wp_safe_redirect( admin_url( 'admin.php?page=' . $dopo ) );
                exit;
            }
        } elseif ( 'chiedi' === $cosa ) {
            $email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
            if ( ! is_email( $email ) ) {
                $esito = [ 'errore' => 'email' ];
            } elseif ( empty( $_POST['consenso'] ) ) {
                $esito = [ 'errore' => 'consenso' ];
            } else {
                $r = self::chiama( self::NEGOZIO . '/free-site', [
                    'email'   => $email,
                    'site'    => home_url(),
                    'admin'   => admin_url( 'admin.php?page=' . self::PAGE ),
                    'lang'    => substr( get_user_locale(), 0, 2 ),
                    'consent' => true,
                ] );
                $esito = ( ! is_wp_error( $r ) && 200 === $r[0] && ! empty( $r[1]['ok'] ) ) ? [ 'ok' => 'mandata', 'a' => $email ] : [ 'errore' => is_wp_error( $r ) ? 'non_risponde' : 'chiedi' ];
            }
        } elseif ( 'verifica' === $cosa ) {
            self::verifica();
            $esito = [ 'ok' => 'verificata' ];
        } elseif ( 'disattiva' === $cosa ) {
            self::disattiva();
            $esito = [ 'ok' => 'disattivata' ];
        }
        wp_safe_redirect( add_query_arg( $esito + ( '' !== $dopo ? [ 'dopo' => $dopo ] : [] ), admin_url( 'admin.php?page=' . self::PAGE ) ) );
        exit;
    }

    /** Il riquadro di un modulo della pagina, con nonce e azione. */
    private static function apri_form( $cosa, $dopo ) {
        echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
        wp_nonce_field( 'olobuild_licenza' );
        echo '<input type="hidden" name="action" value="olobuild_licenza"><input type="hidden" name="cosa" value="' . esc_attr( $cosa ) . '">';
        if ( '' !== $dopo ) echo '<input type="hidden" name="dopo" value="' . esc_attr( $dopo ) . '">';
    }

    public static function pagina() {
        if ( ! current_user_can( 'manage_options' ) ) return;
        // phpcs:disable WordPress.Security.NonceVerification.Recommended -- solo lettura per i messaggi e la chiave dal link dell'email
        $ok     = isset( $_GET['ok'] ) ? sanitize_key( wp_unslash( $_GET['ok'] ) ) : '';
        $errore = isset( $_GET['errore'] ) ? sanitize_key( wp_unslash( $_GET['errore'] ) ) : '';
        $a      = isset( $_GET['a'] ) ? sanitize_email( wp_unslash( $_GET['a'] ) ) : '';
        $dopo   = isset( $_GET['dopo'] ) ? sanitize_key( wp_unslash( $_GET['dopo'] ) ) : '';
        $dalink = isset( $_GET['olo_chiave'] ) ? sanitize_text_field( wp_unslash( $_GET['olo_chiave'] ) ) : '';
        // phpcs:enable
        $d      = self::dati();
        $attivo = self::builder_attivo();

        echo '<div class="wrap olb-lic"><style>
            .olb-lic{max-width:880px}.olb-lic h1{display:flex;align-items:center;gap:10px}
            .olb-lic .olb-card{background:#fff;border:1px solid #e2e4eb;border-radius:12px;padding:16px 18px;margin:14px 0}
            .olb-lic .olb-card h2{margin:0 0 4px;font-size:15px}.olb-lic .olb-card p{margin:4px 0 12px;color:#5a6074}
            .olb-lic .olb-riga{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
            .olb-lic .olb-riga input[type=text],.olb-lic .olb-riga input[type=email]{flex:1 1 280px;min-height:36px;border-radius:8px}
            .olb-lic .olb-consenso{display:flex;gap:8px;align-items:flex-start;margin:10px 0 0;color:#5a6074;font-size:13px}
            .olb-lic .olb-stato{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:8px 18px;margin:6px 0 12px}
            .olb-lic .olb-stato div{display:flex;flex-direction:column}.olb-lic .olb-stato span{font-size:12px;color:#8b90a3}
            .olb-lic .olb-pill{display:inline-block;padding:2px 10px;border-radius:99px;font-size:12px;font-weight:600}
            .olb-lic .olb-ok{background:#e7f5ed;color:#1f8a55}.olb-lic .olb-att{background:#fff3df;color:#a86400}.olb-lic .olb-no{background:#fdecee;color:#c62f37}
        </style>';
        echo '<h1>' . ( $attivo ? esc_html__( 'Licenza di OLObuild', 'olobuild' ) : esc_html__( 'Collega OLObuild', 'olobuild' ) ) . '</h1>';

        $msg = [
            'attivata'    => __( 'Fatto: OLObuild è collegato a questo sito.', 'olobuild' ),
            'verificata'  => __( 'Licenza verificata.', 'olobuild' ),
            'disattivata' => __( 'Chiave tolta da questo sito: ora puoi usarla su un altro.', 'olobuild' ),
            /* translators: %s: indirizzo email */
            'mandata'     => sprintf( __( 'Ti abbiamo mandato la chiave a %s: apri l\'email e premi «Attiva su questo sito».', 'olobuild' ), $a ),
        ];
        if ( isset( $msg[ $ok ] ) ) echo '<div class="notice notice-success"><p>' . esc_html( $msg[ $ok ] ) . '</p></div>';
        if ( '' !== $errore ) {
            $e = [
                'email'    => __( 'Scrivi un indirizzo email valido.', 'olobuild' ),
                'consenso' => __( 'Per mandarti la chiave serve il tuo consenso.', 'olobuild' ),
                'chiedi'   => __( 'Non siamo riusciti a mandarti la chiave: riprova tra qualche minuto.', 'olobuild' ),
            ];
            echo '<div class="notice notice-error"><p>' . esc_html( $e[ $errore ] ?? self::messaggio( $errore ) ) . '</p></div>';
        }

        if ( $attivo && '' !== $d['chiave'] ) {
            $stati = [ 'active' => [ __( 'Attiva', 'olobuild' ), 'olb-ok' ], 'expired' => [ __( 'Scaduta: il builder funziona, gli aggiornamenti no', 'olobuild' ), 'olb-att' ] ];
            $st    = $stati[ $d['stato'] ] ?? [ $d['stato'], 'olb-att' ];
            echo '<div class="olb-card"><h2>' . esc_html( '' !== $d['piano'] ? $d['piano'] : 'OLObuild' ) . ' <span class="olb-pill ' . esc_attr( $st[1] ) . '">' . esc_html( $st[0] ) . '</span></h2><div class="olb-stato">';
            echo '<div><span>' . esc_html__( 'Sito', 'olobuild' ) . '</span>' . esc_html( $d['sito'] ) . '</div>';
            echo '<div><span>' . esc_html__( 'Scadenza', 'olobuild' ) . '</span>' . esc_html( '' !== $d['scade'] ? wp_date( get_option( 'date_format' ), strtotime( $d['scade'] . ' UTC' ) ) : __( 'senza scadenza', 'olobuild' ) ) . '</div>';
            echo '<div><span>' . esc_html__( 'Ultima verifica', 'olobuild' ) . '</span>' . esc_html( $d['verificata'] ? wp_date( get_option( 'date_format' ) . ' H:i', (int) $d['verificata'] ) : '—' ) . '</div>';
            echo '</div><div class="olb-riga">';
            self::apri_form( 'verifica', '' );
            echo '<button class="button">' . esc_html__( 'Verifica ora', 'olobuild' ) . '</button></form>';
            self::apri_form( 'disattiva', '' );
            echo '<button class="button" onclick="return confirm(this.dataset.c)" data-c="' . esc_attr__( 'Togliere la chiave da questo sito? Il builder si ferma finché non ne metti una.', 'olobuild' ) . '">' . esc_html__( 'Disattiva su questo sito', 'olobuild' ) . '</button></form>';
            echo '</div></div>';
            echo '<div class="olb-card"><h2>' . esc_html__( 'Passi alla Pro?', 'olobuild' ) . '</h2><p>' . esc_html__( 'Incolla qui la chiave nuova: prende il posto di questa e libera il suo posto.', 'olobuild' ) . '</p>';
            self::apri_form( 'attiva', '' );
            echo '<div class="olb-riga"><input type="text" name="chiave" value="' . esc_attr( $dalink ) . '" placeholder="OLO-…" autocomplete="off" spellcheck="false"><button class="button button-primary">' . esc_html__( 'Attiva', 'olobuild' ) . '</button></div></form></div>';
        } elseif ( $attivo ) {
            echo '<div class="olb-card"><h2>' . esc_html__( 'OLObuild è collegato con la licenza di OLObooking', 'olobuild' ) . '</h2><p>' . esc_html__( 'La chiave si gestisce nel gestionale di OLObooking, in Impostazioni › Pacchetti moduli.', 'olobuild' ) . '</p></div>';
        } else {
            echo '<p style="font-size:14px;max-width:70ch">' . esc_html__( 'OLObuild è gratis, ma per usarlo serve una chiave: una per sito. Se l\'hai scaricato da olotheme.com la chiave è già nel file e si attiva da sola; altrimenti chiedila qui sotto, ti arriva per email.', 'olobuild' ) . '</p>';
            if ( '' !== $d['chiave'] && in_array( (string) $d['stato'], self::BLOCCANTI, true ) ) {
                echo '<div class="notice notice-error inline"><p>' . esc_html( self::messaggio( (string) $d['stato'] ) ) . '</p></div>';
            } elseif ( '' !== $d['chiave'] && self::sito() !== (string) $d['sito'] ) {
                echo '<div class="notice notice-warning inline"><p>' . esc_html__( 'L\'indirizzo del sito è cambiato: attiva di nuovo la chiave per questo indirizzo.', 'olobuild' ) . '</p></div>';
            }
            echo '<div class="olb-card"><h2>' . esc_html__( 'Chiedi la chiave gratuita', 'olobuild' ) . '</h2><p>' . esc_html__( 'Scrivi la tua email: ti mandiamo la chiave e un pulsante per attivarla su questo sito.', 'olobuild' ) . '</p>';
            self::apri_form( 'chiedi', $dopo );
            $utente = wp_get_current_user();
            echo '<div class="olb-riga"><input type="email" name="email" required value="' . esc_attr( $utente ? $utente->user_email : '' ) . '"><button class="button button-primary">' . esc_html__( 'Mandami la chiave', 'olobuild' ) . '</button></div>';
            echo '<label class="olb-consenso"><input type="checkbox" name="consenso" value="1" required> <span>' . wp_kses( sprintf(
                /* translators: %s: link all'informativa sulla privacy di olotheme.com */
                __( 'Accetto che OLOtheme usi la mia email per mandarmi la chiave e le notizie importanti su OLObuild, secondo la %s. Posso smettere quando voglio.', 'olobuild' ),
                '<a href="https://olotheme.com/privacy-policy/" target="_blank" rel="noopener">' . esc_html__( 'informativa sulla privacy', 'olobuild' ) . '</a>'
            ), [ 'a' => [ 'href' => true, 'target' => true, 'rel' => true ] ] ) . '</span></label></form></div>';
            echo '<div class="olb-card"><h2>' . esc_html__( 'Hai già una chiave?', 'olobuild' ) . '</h2><p>' . esc_html__( 'Incollala qui: la Free, la Pro o quella di OLObooking.', 'olobuild' ) . '</p>';
            self::apri_form( 'attiva', $dopo );
            echo '<div class="olb-riga"><input type="text" name="chiave" value="' . esc_attr( $dalink ) . '" placeholder="OLO-…" autocomplete="off" spellcheck="false"><button class="button button-primary">' . esc_html__( 'Attiva', 'olobuild' ) . '</button></div></form></div>';
        }
        echo '</div>';
    }
}
