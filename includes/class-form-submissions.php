<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Form Submissions Dashboard — WP_List_Table implementation.
 *
 * Manages the wp_olo_form_submissions table and provides an admin UI
 * under the Olobuild menu to view, search, mark as read, delete, and export
 * form submissions.
 */
class Olo_Form_Submissions {

    /** @var string DB table name (set in init) */
    private static $table = '';

    /**
     * Hook into WordPress.
     */
    public static function init() {
        global $wpdb;
        self::$table = $wpdb->prefix . 'olo_form_submissions';

        // Screen options (per-page) only on our page
        add_action( 'load-olobuilder_page_olo-form-submissions', [ __CLASS__, 'screen_options' ] );
        add_filter( 'set-screen-option', [ __CLASS__, 'set_screen_option' ], 10, 3 );
        // Also newer hook name (WP 5.4.2+)
        add_filter( 'set_screen_option_olo_submissions_per_page', [ __CLASS__, 'set_screen_option' ], 10, 3 );
    }

    // =========================================================================
    // Database
    // =========================================================================

    /**
     * Create / update the form_submissions table via dbDelta.
     * Called on plugin activation.
     */
    public static function create_table() {
        global $wpdb;
        $table           = $wpdb->prefix . 'olo_form_submissions';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            form_name varchar(255) NOT NULL DEFAULT '',
            fields_data longtext NOT NULL,
            submitted_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            ip_address varchar(45) DEFAULT '',
            user_agent text,
            read_status tinyint(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            KEY form_name (form_name),
            KEY submitted_at (submitted_at),
            KEY read_status (read_status)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    /**
     * Save a form submission.
     *
     * @param string $form_name  Identifier / name of the form.
     * @param array  $fields_data Associative array of submitted field values.
     * @param string $ip          Client IP address.
     * @return int|false Inserted row ID or false on failure.
     */
    public static function save_submission( $form_name, $fields_data, $ip = '' ) {
        global $wpdb;
        $table = $wpdb->prefix . 'olo_form_submissions';

        $result = $wpdb->insert( $table, [
            'form_name'   => sanitize_text_field( $form_name ),
            'fields_data' => wp_json_encode( $fields_data ),
            'submitted_at' => current_time( 'mysql' ),
            'ip_address'  => sanitize_text_field( $ip ),
            'user_agent'  => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( substr( $_SERVER['HTTP_USER_AGENT'], 0, 500 ) ) : '',
            'read_status' => 0,
        ], [ '%s', '%s', '%s', '%s', '%s', '%d' ] );

        return $result ? $wpdb->insert_id : false;
    }

    /**
     * Mark a submission as read.
     */
    public static function mark_read( $id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'olo_form_submissions';
        return $wpdb->update( $table, [ 'read_status' => 1 ], [ 'id' => absint( $id ) ], [ '%d' ], [ '%d' ] );
    }

    /**
     * Mark a submission as unread.
     */
    public static function mark_unread( $id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'olo_form_submissions';
        return $wpdb->update( $table, [ 'read_status' => 0 ], [ 'id' => absint( $id ) ], [ '%d' ], [ '%d' ] );
    }

    /**
     * Delete a submission.
     */
    public static function delete_submission( $id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'olo_form_submissions';
        return $wpdb->delete( $table, [ 'id' => absint( $id ) ], [ '%d' ] );
    }

    // =========================================================================
    // CSV Export
    // =========================================================================

    /**
     * Export submissions as CSV. Called from the admin page.
     */
    public static function export_csv() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Non autorizzato.' );
        }

        check_admin_referer( 'olo_export_submissions' );

        global $wpdb;
        $table     = $wpdb->prefix . 'olo_form_submissions';
        $form_name = sanitize_text_field( $_GET['form_name'] ?? '' );

        if ( $form_name ) {
            $rows = $wpdb->get_results(
                $wpdb->prepare( "SELECT * FROM $table WHERE form_name = %s ORDER BY submitted_at DESC", $form_name ),
                ARRAY_A
            );
        } else {
            $rows = $wpdb->get_results( "SELECT * FROM $table ORDER BY submitted_at DESC", ARRAY_A );
        }

        if ( empty( $rows ) ) {
            wp_die( 'Nessun invio trovato.' );
        }

        // Collect all field keys across submissions
        $all_keys    = [];
        $decoded_rows = [];
        foreach ( $rows as $row ) {
            $fields = json_decode( $row['fields_data'], true );
            if ( ! is_array( $fields ) ) {
                $fields = [];
            }
            $decoded_rows[] = [
                'id'          => $row['id'],
                'form_name'   => $row['form_name'],
                'fields'      => $fields,
                'ip_address'  => $row['ip_address'],
                'submitted_at' => $row['submitted_at'],
                'read_status' => $row['read_status'],
            ];
            foreach ( array_keys( $fields ) as $k ) {
                if ( ! in_array( $k, $all_keys, true ) ) {
                    $all_keys[] = $k;
                }
            }
        }

        // Send CSV headers
        $filename = 'form-submissions-' . ( $form_name ? sanitize_file_name( $form_name ) : 'all' ) . '-' . date( 'Y-m-d' ) . '.csv';
        header( 'Content-Type: text/csv; charset=UTF-8' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
        header( 'Pragma: no-cache' );
        header( 'Expires: 0' );

        $out = fopen( 'php://output', 'w' );
        // BOM for Excel UTF-8
        fwrite( $out, "\xEF\xBB\xBF" );

        // Header row
        $header = array_merge( [ 'ID', 'Form' ], array_map( 'ucfirst', $all_keys ), [ 'IP', 'Data', 'Stato' ] );
        fputcsv( $out, $header, ';' );

        // Data rows
        foreach ( $decoded_rows as $dr ) {
            $row_data = [ $dr['id'], $dr['form_name'] ];
            foreach ( $all_keys as $k ) {
                $val = $dr['fields'][ $k ] ?? '';
                if ( is_array( $val ) ) {
                    $val = implode( ', ', $val );
                }
                $row_data[] = $val;
            }
            $row_data[] = $dr['ip_address'];
            $row_data[] = $dr['submitted_at'];
            $row_data[] = $dr['read_status'] ? 'Letto' : 'Non letto';
            fputcsv( $out, $row_data, ';' );
        }

        fclose( $out );
        exit;
    }

    // =========================================================================
    // Screen options
    // =========================================================================

    public static function screen_options() {
        add_screen_option( 'per_page', [
            'label'   => 'Invii per pagina',
            'default' => 20,
            'option'  => 'olo_submissions_per_page',
        ] );
    }

    public static function set_screen_option( $status, $option, $value ) {
        if ( $option === 'olo_submissions_per_page' ) {
            return absint( $value );
        }
        return $status;
    }

    // =========================================================================
    // Admin page
    // =========================================================================

    /**
     * Render the admin page (cockpit redesign v3.40+).
     */
    public static function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Non autorizzato.' );
        }

        // Handle CSV export (before any HTML output)
        if ( ! empty( $_GET['olo_export_csv'] ) ) {
            self::export_csv();
            return;
        }

        // Boot data (preload prima riga di KPI + items per evitare skeleton flash)
        $rest = new Olo_Rest_Api();
        $boot_stats = $rest->submissions_stats( null );
        $stats = is_wp_error( $boot_stats ) ? [] : $boot_stats->get_data();

        $export_url = add_query_arg( [
            'page'           => 'olo-form-submissions',
            'olo_export_csv' => '1',
            '_wpnonce'       => wp_create_nonce( 'olo_export_submissions' ),
        ], admin_url( 'admin.php' ) );

        // Chip filter di stato
        $chips = [
            [ 'id' => 'all',    'label' => __( 'Tutti', 'olobuild' ),     'count' => $stats['total']  ?? 0 ],
            [ 'id' => 'unread', 'label' => __( 'Non letti', 'olobuild' ), 'count' => $stats['unread'] ?? 0, 'dot_color' => '#ef4444' ],
            [ 'id' => 'read',   'label' => __( 'Letti', 'olobuild' ),    'count' => $stats['read']   ?? 0, 'dot_color' => '#22c55e' ],
        ];
        // Aggiungi chip per i form più frequenti
        foreach ( ( $stats['forms'] ?? [] ) as $f ) {
            $chips[] = [
                'id'    => 'form:' . $f['name'],
                'label' => $f['name'],
                'count' => $f['count'],
            ];
        }

        // Sub: contatore inline
        $sub = sprintf(
            /* translators: 1: total, 2: unread, 3: last 7 days */
            __( '%1$s invii totali · %2$s da leggere · %3$s ultimi 7gg', 'olobuild' ),
            '<b>' . number_format_i18n( $stats['total']   ?? 0 ) . '</b>',
            '<b>' . number_format_i18n( $stats['unread']  ?? 0 ) . '</b>',
            '<b>' . number_format_i18n( $stats['last_7d'] ?? 0 ) . '</b>'
        );

        // Actions: bottone Esporta CSV
        $actions = Olo_Builder::cockpit_button( [
            'label'   => __( 'Esporta CSV', 'olobuild' ),
            'variant' => 'sec',
            'href'    => $export_url,
            'icon'    => '<path d="M12 4v12M7 9l5-5 5 5M5 20h14"/>',
        ] );

        ?>
        <?php Olo_Builder::cockpit_shell_open( '<b>' . esc_html__( 'Invii Form', 'olobuild' ) . '</b>' ); ?>
        <main class="olo-cockpit-main olo-submissions-page">
            <?php
            echo Olo_Builder::cockpit_page_head( [
                'title'   => __( 'Invii Form', 'olobuild' ),
                'sub'     => $sub,
                'actions' => $actions,
            ] );
            echo Olo_Builder::cockpit_toolbar( [
                'chips'              => $chips,
                'active_chip'        => 'all',
                'search'             => true,
                'search_id'          => 'olo-sub-search',
                'search_placeholder' => __( 'Cerca per nome, email, contenuto…', 'olobuild' ),
            ] );
            ?>

            <div class="olo-sub-list" data-olo-submissions>
                <div class="olo-empty-state" data-olo-loading>
                    <div class="loader-spinner"></div>
                    <div><?php esc_html_e( 'Caricamento invii…', 'olobuild' ); ?></div>
                </div>
            </div>
        </main>

        <!-- Drawer dettaglio (overlay laterale destra) -->
        <div class="olo-sub-drawer-back" data-olo-drawer hidden>
            <aside class="olo-sub-drawer">
                <header class="olo-sub-drawer-head">
                    <div>
                        <div class="olo-sub-drawer-title" data-olo-drawer-title>—</div>
                        <div class="olo-sub-drawer-meta" data-olo-drawer-meta></div>
                    </div>
                    <button type="button" class="olo-btn olo-btn-ghost olo-btn-icon" data-olo-drawer-close title="<?php esc_attr_e( 'Chiudi', 'olobuild' ); ?>">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
                    </button>
                </header>
                <div class="olo-sub-drawer-body" data-olo-drawer-body>
                    <!-- popolato via JS -->
                </div>
                <footer class="olo-sub-drawer-foot">
                    <button type="button" class="olo-btn olo-btn-sec" data-olo-drawer-toggle-read>
                        <?php esc_html_e( 'Segna come non letto', 'olobuild' ); ?>
                    </button>
                    <span style="flex:1"></span>
                    <button type="button" class="olo-btn olo-btn-danger" data-olo-drawer-delete>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2M6 6l1 14a2 2 0 002 2h6a2 2 0 002-2l1-14"/></svg>
                        <?php esc_html_e( 'Elimina', 'olobuild' ); ?>
                    </button>
                </footer>
            </aside>
        </div>

        <?php Olo_Builder::cockpit_shell_close(); ?>
        <?php
    }

    /**
     * Render detail view for a single submission.
     */
    private static function render_detail_view( $id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'olo_form_submissions';
        $row   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $id ), ARRAY_A );

        if ( ! $row ) {
            echo '<div class="olo-admin-wrap"><div class="olo-admin-header"><div class="olo-admin-header-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg></div><div><h1>Invio non trovato</h1></div></div><p><a href="' . esc_url( admin_url( 'admin.php?page=olo-form-submissions' ) ) . '">&larr; Torna alla lista</a></p></div>';
            return;
        }

        // Auto-mark as read
        if ( ! $row['read_status'] ) {
            self::mark_read( $id );
        }

        $fields = json_decode( $row['fields_data'], true );
        if ( ! is_array( $fields ) ) {
            $fields = [];
        }

        $back_url = admin_url( 'admin.php?page=olo-form-submissions' );

        ?>
        <div class="olo-admin-wrap">
            <div class="olo-admin-header">
                <div class="olo-admin-header-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/><path d="M8 10h8"/><path d="M8 14h4"/></svg>
                </div>
                <div>
                    <h1>Invio #<?php echo esc_html( $row['id'] ); ?></h1>
                    <p>Dettaglio del messaggio ricevuto</p>
                </div>
                <div style="margin-left:auto">
                    <a href="<?php echo esc_url( $back_url ); ?>" class="olo-btn-reset" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                        Torna alla lista
                    </a>
                </div>
            </div>

            <div class="olo-card">
                <div class="olo-card-head">
                    <div class="olo-card-icon black">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    </div>
                    <div>
                        <h3>Informazioni invio</h3>
                        <p>Metadati del messaggio</p>
                    </div>
                </div>
                <div class="olo-card-body" style="padding:0">
                    <table class="olo-table">
                        <tbody>
                            <tr>
                                <th style="width:160px">ID</th>
                                <td><?php echo esc_html( $row['id'] ); ?></td>
                            </tr>
                            <tr>
                                <th>Form</th>
                                <td><code><?php echo esc_html( $row['form_name'] ?: '(senza nome)' ); ?></code></td>
                            </tr>
                            <tr>
                                <th>Data invio</th>
                                <td><?php echo esc_html( $row['submitted_at'] ); ?></td>
                            </tr>
                            <tr>
                                <th>IP</th>
                                <td><code style="font-size:12px"><?php echo esc_html( $row['ip_address'] ); ?></code></td>
                            </tr>
                            <tr>
                                <th>User Agent</th>
                                <td style="word-break:break-all"><?php echo esc_html( $row['user_agent'] ); ?></td>
                            </tr>
                            <tr>
                                <th>Stato</th>
                                <td><?php echo $row['read_status'] ? '<span class="olo-badge green">Letto</span>' : '<span class="olo-badge orange">Non letto</span>'; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="olo-card">
                <div class="olo-card-head">
                    <div class="olo-card-icon orange">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </div>
                    <div>
                        <h3>Campi compilati</h3>
                        <p>Dati inseriti dall'utente nel form</p>
                    </div>
                </div>
                <div class="olo-card-body" style="padding:0">
                    <table class="olo-table">
                        <thead>
                            <tr>
                                <th style="width:200px">Campo</th>
                                <th>Valore</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $fields as $key => $value ) : ?>
                                <tr>
                                    <td><strong><?php echo esc_html( ucfirst( str_replace( [ '_', '-' ], ' ', $key ) ) ); ?></strong></td>
                                    <td><?php
                                        if ( is_array( $value ) ) {
                                            echo esc_html( implode( ', ', $value ) );
                                        } else {
                                            echo nl2br( esc_html( $value ) );
                                        }
                                    ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if ( empty( $fields ) ) : ?>
                                <tr><td colspan="2"><em>Nessun campo.</em></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Process single and bulk actions.
     */
    private static function process_actions() {
        global $wpdb;
        $table = $wpdb->prefix . 'olo_form_submissions';

        // Single delete
        if ( ! empty( $_GET['action'] ) && $_GET['action'] === 'delete' && ! empty( $_GET['submission_id'] ) ) {
            check_admin_referer( 'olo_delete_submission_' . $_GET['submission_id'] );
            self::delete_submission( absint( $_GET['submission_id'] ) );
            echo '<div class="notice notice-success is-dismissible"><p>Invio eliminato.</p></div>';
        }

        // Single mark read
        if ( ! empty( $_GET['action'] ) && $_GET['action'] === 'mark_read' && ! empty( $_GET['submission_id'] ) ) {
            check_admin_referer( 'olo_read_submission_' . $_GET['submission_id'] );
            self::mark_read( absint( $_GET['submission_id'] ) );
            echo '<div class="notice notice-success is-dismissible"><p>Segnato come letto.</p></div>';
        }

        // Bulk actions
        if ( ! empty( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'bulk-submissions' ) ) {
            $action = $_POST['action'] ?? '';
            if ( $action === '-1' ) {
                $action = $_POST['action2'] ?? '';
            }
            $ids = array_map( 'absint', $_POST['submission_ids'] ?? [] );

            if ( ! empty( $ids ) ) {
                if ( $action === 'bulk_delete' ) {
                    $placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
                    $wpdb->query( $wpdb->prepare( "DELETE FROM $table WHERE id IN ($placeholders)", ...$ids ) );
                    echo '<div class="notice notice-success is-dismissible"><p>' . count( $ids ) . ' invii eliminati.</p></div>';
                } elseif ( $action === 'mark_read' ) {
                    $placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
                    $wpdb->query( $wpdb->prepare( "UPDATE $table SET read_status = 1 WHERE id IN ($placeholders)", ...$ids ) );
                    echo '<div class="notice notice-success is-dismissible"><p>' . count( $ids ) . ' invii segnati come letti.</p></div>';
                } elseif ( $action === 'mark_unread' ) {
                    $placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
                    $wpdb->query( $wpdb->prepare( "UPDATE $table SET read_status = 0 WHERE id IN ($placeholders)", ...$ids ) );
                    echo '<div class="notice notice-success is-dismissible"><p>' . count( $ids ) . ' invii segnati come non letti.</p></div>';
                }
            }
        }
    }
}


// =============================================================================
// WP_List_Table subclass — loaded only when needed
// =============================================================================

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Olo_Form_Submissions_List_Table extends WP_List_Table {

    public function __construct() {
        parent::__construct( [
            'singular' => 'submission',
            'plural'   => 'submissions',
            'ajax'     => false,
        ] );
    }

    /**
     * Column definitions.
     */
    public function get_columns() {
        return [
            'cb'           => '<input type="checkbox" />',
            'id'           => 'ID',
            'form_name'    => 'Form',
            'fields_data'  => 'Dati',
            'submitted_at' => 'Data invio',
            'ip_address'   => 'IP',
            'read_status'  => 'Stato',
        ];
    }

    /**
     * Sortable columns.
     */
    public function get_sortable_columns() {
        return [
            'id'           => [ 'id', true ],  // default sort desc
            'form_name'    => [ 'form_name', false ],
            'submitted_at' => [ 'submitted_at', false ],
            'read_status'  => [ 'read_status', false ],
        ];
    }

    /**
     * Bulk actions.
     */
    public function get_bulk_actions() {
        return [
            'mark_read'   => 'Segna come letto',
            'mark_unread' => 'Segna come non letto',
            'bulk_delete'  => 'Elimina',
        ];
    }

    /**
     * Checkbox column.
     */
    public function column_cb( $item ) {
        return '<input type="checkbox" name="submission_ids[]" value="' . absint( $item['id'] ) . '" />';
    }

    /**
     * ID column — link to detail view.
     */
    public function column_id( $item ) {
        $view_url = add_query_arg( [
            'page'    => 'olo-form-submissions',
            'view_id' => $item['id'],
        ], admin_url( 'admin.php' ) );

        $delete_url = wp_nonce_url(
            add_query_arg( [
                'page'          => 'olo-form-submissions',
                'action'        => 'delete',
                'submission_id' => $item['id'],
            ], admin_url( 'admin.php' ) ),
            'olo_delete_submission_' . $item['id']
        );

        $mark_read_url = wp_nonce_url(
            add_query_arg( [
                'page'          => 'olo-form-submissions',
                'action'        => 'mark_read',
                'submission_id' => $item['id'],
            ], admin_url( 'admin.php' ) ),
            'olo_read_submission_' . $item['id']
        );

        $actions = [
            'view'   => '<a href="' . esc_url( $view_url ) . '">Visualizza</a>',
            'delete' => '<a href="' . esc_url( $delete_url ) . '" class="submitdelete" onclick="return confirm(\'Eliminare questo invio?\')">Elimina</a>',
        ];

        if ( ! $item['read_status'] ) {
            $actions['mark_read'] = '<a href="' . esc_url( $mark_read_url ) . '">Segna letto</a>';
        }

        $style = $item['read_status'] ? '' : ' style="font-weight:700"';
        return '<strong' . $style . '><a href="' . esc_url( $view_url ) . '">#' . absint( $item['id'] ) . '</a></strong>' . $this->row_actions( $actions );
    }

    /**
     * Form name column.
     */
    public function column_form_name( $item ) {
        $name = $item['form_name'] ?: '(senza nome)';
        $filter_url = add_query_arg( [
            'page'      => 'olo-form-submissions',
            'form_name' => $item['form_name'],
        ], admin_url( 'admin.php' ) );
        return '<a href="' . esc_url( $filter_url ) . '"><code>' . esc_html( $name ) . '</code></a>';
    }

    /**
     * Fields data column — show truncated preview.
     */
    public function column_fields_data( $item ) {
        $fields = json_decode( $item['fields_data'], true );
        if ( ! is_array( $fields ) || empty( $fields ) ) {
            return '<em>—</em>';
        }

        $parts = [];
        $count = 0;
        foreach ( $fields as $k => $v ) {
            if ( $count >= 3 ) {
                $remaining = count( $fields ) - 3;
                $parts[] = '<span style="color:#888">+' . $remaining . ' campi</span>';
                break;
            }
            $label = esc_html( ucfirst( str_replace( [ '_', '-' ], ' ', $k ) ) );
            $val   = is_array( $v ) ? implode( ', ', $v ) : mb_strimwidth( (string) $v, 0, 80, '...' );
            $parts[] = '<strong>' . $label . ':</strong> ' . esc_html( $val );
            $count++;
        }

        return implode( '<br>', $parts );
    }

    /**
     * Date column.
     */
    public function column_submitted_at( $item ) {
        $timestamp = strtotime( $item['submitted_at'] );
        if ( ! $timestamp ) {
            return esc_html( $item['submitted_at'] );
        }
        $date_str = wp_date( 'd/m/Y', $timestamp );
        $time_str = wp_date( 'H:i', $timestamp );
        return esc_html( $date_str ) . '<br><span style="color:#888">' . esc_html( $time_str ) . '</span>';
    }

    /**
     * IP column.
     */
    public function column_ip_address( $item ) {
        return '<code style="font-size:12px">' . esc_html( $item['ip_address'] ) . '</code>';
    }

    /**
     * Read status column.
     */
    public function column_read_status( $item ) {
        if ( $item['read_status'] ) {
            return '<span style="color:#46b450" title="Letto">&#10003; Letto</span>';
        }
        return '<strong style="color:#d63638" title="Non letto">&#9679; Nuovo</strong>';
    }

    /**
     * Default fallback column.
     */
    public function column_default( $item, $column_name ) {
        return esc_html( $item[ $column_name ] ?? '' );
    }

    /**
     * Extra table navigation — form name filter dropdown.
     */
    protected function extra_tablenav( $which ) {
        if ( $which !== 'top' ) {
            return;
        }

        global $wpdb;
        $table     = $wpdb->prefix . 'olo_form_submissions';
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $table is built from $wpdb->prefix constant
        $form_names = $wpdb->get_col( "SELECT DISTINCT form_name FROM `$table` WHERE form_name != '' ORDER BY form_name" );

        if ( empty( $form_names ) ) {
            return;
        }

        $current = sanitize_text_field( $_REQUEST['form_name'] ?? '' );
        ?>
        <div class="alignleft actions">
            <select name="form_name">
                <option value="">Tutti i form</option>
                <?php foreach ( $form_names as $fn ) : ?>
                    <option value="<?php echo esc_attr( $fn ); ?>" <?php selected( $current, $fn ); ?>>
                        <?php echo esc_html( $fn ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php submit_button( 'Filtra', '', 'filter_action', false ); ?>
        </div>
        <?php
    }

    /**
     * Prepare items — query with sorting, search, filter, pagination.
     */
    public function prepare_items() {
        global $wpdb;
        $table = $wpdb->prefix . 'olo_form_submissions';

        // Columns
        $this->_column_headers = [
            $this->get_columns(),
            [],  // hidden
            $this->get_sortable_columns(),
        ];

        // Per page
        $per_page = $this->get_items_per_page( 'olo_submissions_per_page', 20 );
        $page_num = $this->get_pagenum();
        $offset   = ( $page_num - 1 ) * $per_page;

        // Build WHERE clause
        $where_parts  = [];
        $where_values = [];

        // Filter by form name
        $form_name = sanitize_text_field( $_REQUEST['form_name'] ?? '' );
        if ( $form_name !== '' ) {
            $where_parts[]  = 'form_name = %s';
            $where_values[] = $form_name;
        }

        // Search
        $search = sanitize_text_field( $_REQUEST['s'] ?? '' );
        if ( $search !== '' ) {
            $like = '%' . $wpdb->esc_like( $search ) . '%';
            $where_parts[]  = '(form_name LIKE %s OR fields_data LIKE %s OR ip_address LIKE %s)';
            $where_values[] = $like;
            $where_values[] = $like;
            $where_values[] = $like;
        }

        $where_sql = '';
        if ( ! empty( $where_parts ) ) {
            $where_sql = 'WHERE ' . implode( ' AND ', $where_parts );
        }

        // Sorting
        $allowed_orderby = [ 'id', 'form_name', 'submitted_at', 'read_status' ];
        $orderby = sanitize_sql_orderby( $_REQUEST['orderby'] ?? 'id' );
        if ( ! in_array( $orderby, $allowed_orderby, true ) ) {
            $orderby = 'id';
        }
        $order = strtoupper( $_REQUEST['order'] ?? 'DESC' );
        if ( ! in_array( $order, [ 'ASC', 'DESC' ], true ) ) {
            $order = 'DESC';
        }

        // Count total
        if ( ! empty( $where_values ) ) {
            $total = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table $where_sql", ...$where_values ) );
        } else {
            $total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table" );
        }

        // Fetch rows
        $query = "SELECT * FROM $table $where_sql ORDER BY $orderby $order LIMIT %d OFFSET %d";
        $query_values = array_merge( $where_values, [ $per_page, $offset ] );
        $this->items = $wpdb->get_results( $wpdb->prepare( $query, ...$query_values ), ARRAY_A );

        // Pagination
        $this->set_pagination_args( [
            'total_items' => $total,
            'per_page'    => $per_page,
            'total_pages' => ceil( $total / $per_page ),
        ] );
    }

    /**
     * Message when no items are found.
     */
    public function no_items() {
        echo 'Nessun invio trovato.';
    }
}
