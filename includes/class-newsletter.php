<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Olo_Newsletter — gestore dedicato delle iscrizioni newsletter.
 *
 * A differenza del form di contatto (Olo_Form_Handler), la newsletter ha
 * bisogno di una vera LISTA di iscritti persistente, non di una semplice
 * email all'admin. Questa classe fornisce:
 *
 *   - tabella dedicata `{prefix}olo_newsletter` (email unica + stato)
 *   - endpoint REST pubblico `olo/v1/newsletter/subscribe`
 *   - honeypot + rate-limit anti-bot
 *   - pagina admin (Olobuild → Newsletter) con conteggio, lista ed export CSV
 *
 * La tile `newsletter` invia qui il submit (vedi class-newsletter-tile.php).
 */
class Olo_Newsletter {

    /** Versione schema DB — bump per forzare dbDelta. */
    const DB_VERSION = '1.0.0';

    /** Nome tabella (senza prefix). */
    private static function table() {
        global $wpdb;
        return $wpdb->prefix . 'olo_newsletter';
    }

    // =========================================================================
    // Bootstrap
    // =========================================================================

    public static function init() {
        // Crea/aggiorna la tabella senza richiedere la riattivazione del plugin
        // (il deploy aggiorna solo i file: l'activation hook non scatta).
        add_action( 'init', [ __CLASS__, 'maybe_install' ] );

        // Endpoint REST pubblico.
        add_action( 'rest_api_init', [ __CLASS__, 'register_routes' ] );

        // Export CSV (intercettato prima dell'output HTML della pagina admin).
        add_action( 'admin_init', [ __CLASS__, 'maybe_export_csv' ] );
    }

    // =========================================================================
    // Database
    // =========================================================================

    /** Crea la tabella se manca o se la versione schema è cambiata. */
    public static function maybe_install() {
        if ( get_option( 'olo_newsletter_db_version' ) === self::DB_VERSION ) {
            return;
        }
        self::create_table();
        update_option( 'olo_newsletter_db_version', self::DB_VERSION );
    }

    /** dbDelta della tabella iscritti. Idempotente. */
    public static function create_table() {
        global $wpdb;
        $table           = self::table();
        $charset_collate = $wpdb->get_charset_collate();

        // email varchar(190) → UNIQUE compatibile con utf8mb4 (190*4 < 767 byte).
        $sql = "CREATE TABLE $table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            email varchar(190) NOT NULL DEFAULT '',
            name varchar(190) NOT NULL DEFAULT '',
            source varchar(255) NOT NULL DEFAULT '',
            status varchar(20) NOT NULL DEFAULT 'subscribed',
            ip_address varchar(45) NOT NULL DEFAULT '',
            user_agent varchar(255) NOT NULL DEFAULT '',
            created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            PRIMARY KEY (id),
            UNIQUE KEY email (email),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    // =========================================================================
    // REST
    // =========================================================================

    public static function register_routes() {
        register_rest_route( 'olo/v1', '/newsletter/subscribe', [
            'methods'             => 'POST',
            'callback'            => [ __CLASS__, 'subscribe' ],
            'permission_callback' => '__return_true', // endpoint pubblico
        ] );
    }

    /** Risposta standard di successo. */
    private static function ok( $message, $redirect = '' ) {
        return new WP_REST_Response( [
            'success' => true,
            'data'    => [ 'message' => $message, 'redirect' => $redirect ],
        ], 200 );
    }

    /** Risposta standard di errore. */
    private static function err( $message, $status = 400 ) {
        return new WP_REST_Response( [
            'success' => false,
            'data'    => [ 'message' => $message ],
        ], $status );
    }

    /**
     * Handler iscrizione.
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public static function subscribe( $request ) {
        // 1. Honeypot — se valorizzato è un bot: fingiamo successo per non avvisarlo.
        if ( $request->get_param( 'olo_website_url' ) || $request->get_param( 'olo_hp_field' ) ) {
            return self::ok( __( 'Iscrizione completata!', 'olobuild' ) );
        }

        // 2. Rate-limit per IP (max 15 tentativi / 10 minuti).
        $ip  = self::client_ip();
        $key = 'olo_nl_rl_' . md5( $ip );
        $hits = (int) get_transient( $key );
        if ( $hits >= 15 ) {
            return self::err( __( 'Troppe richieste. Riprova tra qualche minuto.', 'olobuild' ), 429 );
        }
        set_transient( $key, $hits + 1, 10 * MINUTE_IN_SECONDS );

        // 3. Email valida.
        $email = sanitize_email( (string) $request->get_param( 'email' ) );
        if ( ! is_email( $email ) ) {
            return self::err( __( 'Inserisci un indirizzo email valido.', 'olobuild' ), 400 );
        }

        $name   = sanitize_text_field( (string) ( $request->get_param( 'name' ) ?? '' ) );
        $source = sanitize_text_field( (string) ( $request->get_param( 'source' ) ?? '' ) );
        $custom_msg = sanitize_text_field( (string) ( $request->get_param( 'success_message' ) ?? '' ) );
        $success_msg = $custom_msg ?: __( 'Iscrizione completata! Ti avviseremo al lancio.', 'olobuild' );

        global $wpdb;
        $table = self::table();

        // 4. Dedup — email già in lista = idempotente (non riveliamo nulla di più).
        $existing = $wpdb->get_row( $wpdb->prepare( "SELECT id, status FROM $table WHERE email = %s", $email ) );
        if ( $existing ) {
            if ( $existing->status !== 'subscribed' ) {
                $wpdb->update( $table, [ 'status' => 'subscribed' ], [ 'id' => $existing->id ], [ '%s' ], [ '%d' ] );
            }
            return self::ok( $custom_msg ?: __( 'Sei già iscritto. Grazie!', 'olobuild' ) );
        }

        // 5. Inserimento.
        $inserted = $wpdb->insert( $table, [
            'email'      => $email,
            'name'       => $name,
            'source'     => $source,
            'status'     => 'subscribed',
            'ip_address' => $ip,
            'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( substr( $_SERVER['HTTP_USER_AGENT'], 0, 255 ) ) : '',
            'created_at' => current_time( 'mysql' ),
        ], [ '%s', '%s', '%s', '%s', '%s', '%s', '%s' ] );

        if ( ! $inserted ) {
            return self::err( __( 'Errore durante l\'iscrizione. Riprova.', 'olobuild' ), 500 );
        }

        // 6. Notifica admin (best-effort: un fallimento mail NON deve rompere l'iscrizione).
        self::notify_admin( $email, $name, $source );

        return self::ok( $success_msg );
    }

    /** Notifica email all'admin per ogni nuovo iscritto (disattivabile). */
    private static function notify_admin( $email, $name, $source ) {
        if ( ! apply_filters( 'olo_newsletter_notify_admin', true ) ) {
            return;
        }
        $to        = get_option( 'admin_email' );
        $site_name = get_bloginfo( 'name' );
        $subject   = sprintf( '[%s] Nuova iscrizione newsletter', $site_name );
        $lines   = [];
        $lines[] = 'Nuova iscrizione alla newsletter:';
        $lines[] = '';
        $lines[] = 'Email:  ' . $email;
        if ( $name )   { $lines[] = 'Nome:   ' . $name; }
        if ( $source ) { $lines[] = 'Da:     ' . $source; }
        $lines[] = 'Totale iscritti: ' . self::count();
        $lines[] = 'Data:   ' . wp_date( 'd/m/Y H:i' );
        @wp_mail( $to, $subject, implode( "\n", $lines ) );
    }

    /** IP client (gestisce X-Forwarded-For dietro proxy/CDN). */
    private static function client_ip() {
        $candidates = [];
        if ( ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
            $candidates[] = $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $parts = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
            $candidates[] = trim( $parts[0] );
        }
        if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
            $candidates[] = $_SERVER['REMOTE_ADDR'];
        }
        foreach ( $candidates as $ip ) {
            $ip = filter_var( $ip, FILTER_VALIDATE_IP );
            if ( $ip ) {
                return $ip;
            }
        }
        return '';
    }

    // =========================================================================
    // Query helpers
    // =========================================================================

    /** Numero iscritti attivi. */
    public static function count( $status = 'subscribed' ) {
        global $wpdb;
        $table = self::table();
        if ( $status ) {
            return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table WHERE status = %s", $status ) );
        }
        return (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table" );
    }

    // =========================================================================
    // CSV export
    // =========================================================================

    public static function maybe_export_csv() {
        if ( empty( $_GET['olo_nl_export'] ) || ( $_GET['page'] ?? '' ) !== 'olo-newsletter' ) {
            return;
        }
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Non autorizzato.' );
        }
        check_admin_referer( 'olo_export_newsletter' );

        global $wpdb;
        $table = self::table();
        $rows  = $wpdb->get_results( "SELECT email, name, source, status, ip_address, created_at FROM $table ORDER BY created_at DESC", ARRAY_A );

        $filename = 'newsletter-' . date( 'Y-m-d' ) . '.csv';
        header( 'Content-Type: text/csv; charset=UTF-8' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
        header( 'Pragma: no-cache' );
        header( 'Expires: 0' );

        $out = fopen( 'php://output', 'w' );
        fwrite( $out, "\xEF\xBB\xBF" ); // BOM per Excel
        fputcsv( $out, [ 'Email', 'Nome', 'Origine', 'Stato', 'IP', 'Data' ], ';' );
        foreach ( (array) $rows as $r ) {
            fputcsv( $out, [ $r['email'], $r['name'], $r['source'], $r['status'], $r['ip_address'], $r['created_at'] ], ';' );
        }
        fclose( $out );
        exit;
    }

    // =========================================================================
    // Admin page
    // =========================================================================

    public static function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Non autorizzato.' );
        }

        global $wpdb;
        $table = self::table();

        // Eliminazione singola.
        if ( ( $_GET['action'] ?? '' ) === 'delete' && ! empty( $_GET['id'] ) ) {
            check_admin_referer( 'olo_nl_delete_' . $_GET['id'] );
            $wpdb->delete( $table, [ 'id' => absint( $_GET['id'] ) ], [ '%d' ] );
            echo '<div class="notice notice-success is-dismissible"><p>Iscritto eliminato.</p></div>';
        }

        $total = self::count();

        // Paginazione semplice.
        $per_page = 50;
        $paged    = max( 1, absint( $_GET['paged'] ?? 1 ) );
        $offset   = ( $paged - 1 ) * $per_page;
        $all      = self::count( '' );
        $pages    = max( 1, (int) ceil( $all / $per_page ) );

        $rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM $table ORDER BY created_at DESC LIMIT %d OFFSET %d",
            $per_page, $offset
        ), ARRAY_A );

        $export_url = wp_nonce_url(
            add_query_arg( [ 'page' => 'olo-newsletter', 'olo_nl_export' => '1' ], admin_url( 'admin.php' ) ),
            'olo_export_newsletter'
        );

        ?>
        <div class="wrap">
            <h1 style="display:flex;align-items:center;gap:12px">
                <?php esc_html_e( 'Newsletter', 'olobuild' ); ?>
                <span class="title-count theme-count"><?php echo esc_html( number_format_i18n( $total ) ); ?></span>
                <a href="<?php echo esc_url( $export_url ); ?>" class="page-title-action"><?php esc_html_e( 'Esporta CSV', 'olobuild' ); ?></a>
            </h1>
            <p class="description">
                <?php
                printf(
                    /* translators: %s: numero iscritti */
                    esc_html__( '%s iscritti attivi raccolti dalle tile Newsletter del sito.', 'olobuild' ),
                    '<b>' . esc_html( number_format_i18n( $total ) ) . '</b>'
                );
                ?>
            </p>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width:60px">ID</th>
                        <th><?php esc_html_e( 'Email', 'olobuild' ); ?></th>
                        <th><?php esc_html_e( 'Nome', 'olobuild' ); ?></th>
                        <th><?php esc_html_e( 'Origine', 'olobuild' ); ?></th>
                        <th style="width:90px"><?php esc_html_e( 'Stato', 'olobuild' ); ?></th>
                        <th style="width:150px"><?php esc_html_e( 'Data', 'olobuild' ); ?></th>
                        <th style="width:80px"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( empty( $rows ) ) : ?>
                        <tr><td colspan="7"><em><?php esc_html_e( 'Nessun iscritto ancora.', 'olobuild' ); ?></em></td></tr>
                    <?php else : foreach ( $rows as $r ) :
                        $del = wp_nonce_url(
                            add_query_arg( [ 'page' => 'olo-newsletter', 'action' => 'delete', 'id' => $r['id'] ], admin_url( 'admin.php' ) ),
                            'olo_nl_delete_' . $r['id']
                        );
                        ?>
                        <tr>
                            <td><?php echo esc_html( $r['id'] ); ?></td>
                            <td><strong><a href="mailto:<?php echo esc_attr( $r['email'] ); ?>"><?php echo esc_html( $r['email'] ); ?></a></strong></td>
                            <td><?php echo esc_html( $r['name'] ?: '—' ); ?></td>
                            <td><span style="color:#666;font-size:12px"><?php echo esc_html( $r['source'] ?: '—' ); ?></span></td>
                            <td>
                                <?php if ( $r['status'] === 'subscribed' ) : ?>
                                    <span style="color:#46b450">&#10003; <?php esc_html_e( 'Attivo', 'olobuild' ); ?></span>
                                <?php else : ?>
                                    <span style="color:#999"><?php echo esc_html( $r['status'] ); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo esc_html( $r['created_at'] ); ?></td>
                            <td><a href="<?php echo esc_url( $del ); ?>" class="submitdelete" onclick="return confirm('<?php echo esc_js( __( 'Eliminare questo iscritto?', 'olobuild' ) ); ?>')"><?php esc_html_e( 'Elimina', 'olobuild' ); ?></a></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>

            <?php if ( $pages > 1 ) : ?>
                <div class="tablenav"><div class="tablenav-pages">
                    <?php
                    echo paginate_links( [
                        'base'    => add_query_arg( 'paged', '%#%' ),
                        'format'  => '',
                        'current' => $paged,
                        'total'   => $pages,
                    ] );
                    ?>
                </div></div>
            <?php endif; ?>
        </div>
        <?php
    }
}
