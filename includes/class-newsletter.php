<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Olobuild_Newsletter — gestore dedicato delle iscrizioni newsletter.
 *
 * A differenza del form di contatto (Olobuild_Form_Handler), la newsletter ha
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
class Olobuild_Newsletter {

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
        // Solo il nome tabella custom ({prefix}olo_newsletter) è interpolato; il valore $email passa da prepare con placeholder %s. Tabella custom: nessun equivalente WP_Query; risultato non cacheabile.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $existing = $wpdb->get_row( $wpdb->prepare( "SELECT id, status FROM $table WHERE email = %s", $email ) );
        if ( $existing ) {
            if ( $existing->status !== 'subscribed' ) {
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- tabella custom del plugin ({prefix}olo_newsletter); $wpdb->update usa placeholder; nessun equivalente WP_Query; scrittura non cacheabile.
                $wpdb->update( $table, [ 'status' => 'subscribed' ], [ 'id' => $existing->id ], [ '%s' ], [ '%d' ] );
            }
            return self::ok( $custom_msg ?: __( 'Sei già iscritto. Grazie!', 'olobuild' ) );
        }

        // 5. Inserimento.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- tabella custom del plugin ({prefix}olo_newsletter); $wpdb->insert usa placeholder; nessun equivalente WP_Query; scrittura non cacheabile.
        $inserted = $wpdb->insert( $table, [
            'email'      => $email,
            'name'       => $name,
            'source'     => $source,
            'status'     => 'subscribed',
            'ip_address' => $ip,
            'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 0, 255 ) : '',
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
        if ( ! apply_filters( 'olobuild_newsletter_notify_admin', true ) ) {
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
            $candidates[] = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CF_CONNECTING_IP'] ) );
        }
        if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $parts = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
            $candidates[] = trim( $parts[0] );
        }
        if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
            $candidates[] = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
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
            // Solo il nome tabella custom è interpolato; il valore $status passa da prepare (%s). Tabella custom del plugin ({prefix}olo_newsletter): nessun equivalente WP_Query; conteggio non cacheabile.
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
            return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table WHERE status = %s", $status ) );
        }
        // Query senza valori utente: interpolato solo il nome tabella custom ({prefix}olo_newsletter). Nessun equivalente WP_Query; conteggio non cacheabile.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        return (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table" );
    }

    // =========================================================================
    // CSV export
    // =========================================================================

    public static function maybe_export_csv() {
        // Routing read-only della pagina admin: si decide solo SE intercettare l'export.
        // L'azione effettiva è protetta subito sotto da current_user_can + check_admin_referer( 'olo_export_newsletter' ).
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per routing pagina admin; nessuna modifica di stato qui; il nonce è verificato sotto con check_admin_referer prima di qualsiasi azione.
        if ( empty( $_GET['olo_nl_export'] ) || sanitize_key( wp_unslash( $_GET['page'] ?? '' ) ) !== 'olo-newsletter' ) {
            return;
        }
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Non autorizzato.' );
        }
        check_admin_referer( 'olo_export_newsletter' );

        global $wpdb;
        $table = self::table();
        // Export CSV: query senza valori utente, interpolato solo il nome tabella custom ({prefix}olo_newsletter). Nessun equivalente WP_Query; export una-tantum non cacheabile.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $rows  = $wpdb->get_results( "SELECT email, name, source, status, ip_address, created_at FROM $table ORDER BY created_at DESC", ARRAY_A );

        $filename = 'newsletter-' . gmdate( 'Y-m-d' ) . '.csv';
        header( 'Content-Type: text/csv; charset=UTF-8' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
        header( 'Pragma: no-cache' );
        header( 'Expires: 0' );

        $out = fopen( 'php://output', 'w' );
        // BOM per Excel
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- CSV streamed to php://output (download), not a filesystem file
        fwrite( $out, "\xEF\xBB\xBF" );
        fputcsv( $out, [ 'Email', 'Nome', 'Origine', 'Stato', 'IP', 'Data' ], ';' );
        foreach ( (array) $rows as $r ) {
            // olobuild_csv_safe: email/name/source arrivano dal form pubblico → anti CSV formula injection.
            fputcsv( $out, array_map( 'olobuild_csv_safe', [ $r['email'], $r['name'], $r['source'], $r['status'], $r['ip_address'], $r['created_at'] ] ), ';' );
        }
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- closing the php://output CSV stream
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
        // I read $_GET['action']/$_GET['id'] servono solo a decidere SE entrare nel ramo delete;
        // l'azione di scrittura è autorizzata da current_user_can( 'manage_options' ) (sopra) +
        // check_admin_referer( 'olo_nl_delete_' . $id ) eseguito qui prima del $wpdb->delete.
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- gate read-only; il nonce è verificato con check_admin_referer prima della scrittura.
        if ( ( sanitize_key( wp_unslash( $_GET['action'] ?? '' ) ) ) === 'delete' && ! empty( $_GET['id'] ) ) {
            $del_id = absint( wp_unslash( $_GET['id'] ) );
            check_admin_referer( 'olo_nl_delete_' . $del_id );
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- tabella custom del plugin ({prefix}olo_newsletter); $wpdb->delete usa placeholder (id=absint); nessun equivalente WP_Query; scrittura non cacheabile.
            $wpdb->delete( $table, [ 'id' => $del_id ], [ '%d' ] );
            echo '<div class="notice notice-success is-dismissible"><p>Iscritto eliminato.</p></div>';
        }

        $total = self::count();

        // Paginazione semplice.
        $per_page = 50;
        // Lettura read-only per la paginazione della lista admin; nessuna modifica di stato; valore convertito con absint.
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per paginazione; nessuna azione che cambia stato; valore sanitizzato con absint.
        $paged    = max( 1, absint( wp_unslash( $_GET['paged'] ?? 1 ) ) );
        $offset   = ( $paged - 1 ) * $per_page;
        $all      = self::count( '' );
        $pages    = max( 1, (int) ceil( $all / $per_page ) );

        // Interpolato solo il nome tabella custom ({prefix}olo_newsletter); i valori paginazione passano da prepare (%d/%d). Nessun equivalente WP_Query; lista admin non cacheabile.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $rows = $wpdb->get_results( $wpdb->prepare(
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- query su tabella custom del plugin: nome tabella da $wpdb->prefix / colonna whitelist; tutti i valori utente passano da $wpdb->prepare
            "SELECT * FROM $table ORDER BY created_at DESC LIMIT %d OFFSET %d",
            $per_page, $offset
        ), ARRAY_A );

        $export_url = wp_nonce_url(
            add_query_arg( [ 'page' => 'olo-newsletter', 'olo_nl_export' => '1' ], admin_url( 'admin.php' ) ),
            'olo_export_newsletter'
        );

        ?>
        <div class="wrap olo-nl-page">
            <div class="olo-nl-wrap">

                <div class="olo-admin-header">
                    <div class="olo-admin-header-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 7l-10 6L2 7"/></svg>
                    </div>
                    <div>
                        <h1><?php esc_html_e( 'Newsletter', 'olobuild' ); ?></h1>
                        <p>
                            <?php
                            printf(
                                /* translators: %s: numero iscritti */
                                esc_html__( '%s iscritti attivi raccolti dalle tile Newsletter del sito.', 'olobuild' ),
                                '<b>' . esc_html( number_format_i18n( $total ) ) . '</b>'
                            );
                            ?>
                        </p>
                    </div>
                    <a href="<?php echo esc_url( $export_url ); ?>" class="olo-btn-reset olo-btn-sm olo-nl-export">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        <?php esc_html_e( 'Esporta CSV', 'olobuild' ); ?>
                    </a>
                </div>

                <div class="olo-card">
                    <div class="olo-card-body olo-nl-card-body">
                        <table class="olo-table olo-nl-table">
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
                                    <tr><td colspan="7" class="olo-nl-empty"><?php esc_html_e( 'Nessun iscritto ancora.', 'olobuild' ); ?></td></tr>
                                <?php else : foreach ( $rows as $r ) :
                                    $del = wp_nonce_url(
                                        add_query_arg( [ 'page' => 'olo-newsletter', 'action' => 'delete', 'id' => $r['id'] ], admin_url( 'admin.php' ) ),
                                        'olo_nl_delete_' . $r['id']
                                    );
                                    ?>
                                    <tr>
                                        <td class="olo-nl-id"><?php echo esc_html( $r['id'] ); ?></td>
                                        <td><a class="olo-nl-email" href="mailto:<?php echo esc_attr( $r['email'] ); ?>"><?php echo esc_html( $r['email'] ); ?></a></td>
                                        <td><?php echo esc_html( $r['name'] ?: '—' ); ?></td>
                                        <td><span class="olo-nl-source"><?php echo esc_html( $r['source'] ?: '—' ); ?></span></td>
                                        <td>
                                            <?php if ( $r['status'] === 'subscribed' ) : ?>
                                                <span class="olo-badge green"><?php esc_html_e( 'Attivo', 'olobuild' ); ?></span>
                                            <?php else : ?>
                                                <span class="olo-badge gray"><?php echo esc_html( $r['status'] ); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="olo-nl-date"><?php echo esc_html( $r['created_at'] ); ?></td>
                                        <td><a href="<?php echo esc_url( $del ); ?>" class="submitdelete olo-nl-delete" onclick="return confirm('<?php echo esc_js( __( 'Eliminare questo iscritto?', 'olobuild' ) ); ?>')"><?php esc_html_e( 'Elimina', 'olobuild' ); ?></a></td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if ( $pages > 1 ) : ?>
                    <div class="tablenav olo-nl-pagination"><div class="tablenav-pages">
                        <?php
                        echo paginate_links( [ // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- paginate_links() returns core-generated pagination HTML with escaped URLs.
                            'base'    => add_query_arg( 'paged', '%#%' ),
                            'format'  => '',
                            'current' => $paged,
                            'total'   => $pages,
                        ] );
                        ?>
                    </div></div>
                <?php endif; ?>
            </div>
        </div>

        <style>
            /* ── Newsletter — card e tabella coerenti col design admin ── */
            .olo-nl-wrap {
                max-width: 1060px;
                margin: 28px auto 40px;
                padding: 0 24px;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            }
            .olo-nl-wrap * { box-sizing: border-box; }
            .olo-nl-wrap .olo-admin-header h1 {
                font-size: 20px;
                font-weight: 700;
                color: #1a1a1a;
                margin: 0;
                padding: 0;
                letter-spacing: -0.02em;
                line-height: 1.2;
            }
            .olo-nl-export { margin-left: auto; text-decoration: none; flex-shrink: 0; }
            .olo-nl-export:focus { color: #1a1a1a; box-shadow: none; outline: 2px solid rgba(232,98,42,.4); outline-offset: 2px; }
            .olo-nl-card-body { padding: 6px 10px; }
            .olo-nl-table { table-layout: fixed; }
            .olo-nl-id { color: #999; font-size: 12px; }
            .olo-nl-email { font-weight: 600; color: #1a1a1a; text-decoration: none; }
            .olo-nl-email:hover { color: #e8622a; }
            .olo-nl-source { color: #999; font-size: 12px; }
            .olo-nl-date { color: #666; font-size: 12px; white-space: nowrap; }
            .olo-nl-delete { color: #dc2626 !important; font-size: 12px; font-weight: 600; text-decoration: none; }
            .olo-nl-delete:hover { text-decoration: underline; }
            .olo-nl-empty { text-align: center; padding: 36px 20px !important; color: #999; }
            .olo-nl-pagination { margin-top: 16px; height: auto; }
            .olo-nl-pagination .tablenav-pages { float: none; display: flex; justify-content: flex-end; gap: 4px; }
            .olo-nl-pagination .page-numbers {
                display: inline-flex;
                align-items: center;
                padding: 6px 12px;
                border: 1.5px solid #e5e0d8;
                border-radius: 8px;
                background: #fff;
                color: #666;
                font-size: 13px;
                font-weight: 600;
                text-decoration: none;
                transition: all .15s;
            }
            .olo-nl-pagination a.page-numbers:hover { border-color: #1a1a1a; color: #1a1a1a; }
            .olo-nl-pagination .page-numbers.current { background: #1a1a1a; border-color: #1a1a1a; color: #fff; }
            .olo-nl-pagination .page-numbers.dots { border: none; background: none; }
        </style>
        <?php
    }
}
