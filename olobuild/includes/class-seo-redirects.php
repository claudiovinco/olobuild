<?php
/**
 * Olo_Seo_Redirects — Redirect 301/302, Monitor 404, IndexNow.
 *
 * DB tables: wp_olo_redirects, wp_olo_404_log
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Seo_Redirects {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init() {
        // Create tables on activation
        $this->maybe_create_tables();

        // Redirect matching — early priority
        add_action( 'template_redirect', [ $this, 'check_redirect' ], 1 );

        // 404 logging
        add_action( 'template_redirect', [ $this, 'log_404' ], 99 );

        // Admin page
        add_action( 'admin_menu', [ $this, 'add_menu' ] );

        // Admin AJAX handlers
        add_action( 'wp_ajax_olo_seo_save_redirect', [ $this, 'ajax_save_redirect' ] );
        add_action( 'wp_ajax_olo_seo_delete_redirect', [ $this, 'ajax_delete_redirect' ] );
        add_action( 'wp_ajax_olo_seo_delete_404', [ $this, 'ajax_delete_404' ] );
        add_action( 'wp_ajax_olo_seo_clear_404_log', [ $this, 'ajax_clear_404_log' ] );
        add_action( 'wp_ajax_olo_seo_404_to_redirect', [ $this, 'ajax_404_to_redirect' ] );

        // IndexNow on publish/update
        add_action( 'publish_post', [ $this, 'indexnow_ping' ], 20 );
        add_action( 'publish_page', [ $this, 'indexnow_ping' ], 20 );

        // Admin styles
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
    }

    /* ═══════════════════════════════════════════════════
     * DATABASE
     * ═══════════════════════════════════════════════════ */

    private function table_redirects() {
        global $wpdb;
        return $wpdb->prefix . 'olo_redirects';
    }

    private function table_404_log() {
        global $wpdb;
        return $wpdb->prefix . 'olo_404_log';
    }

    private function maybe_create_tables() {
        $db_version = get_option( 'olo_seo_redirects_db', '0' );
        if ( $db_version === '1.0' ) {
            return;
        }

        global $wpdb;
        $charset = $wpdb->get_charset_collate();

        $sql1 = "CREATE TABLE IF NOT EXISTS {$this->table_redirects()} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            from_url VARCHAR(500) NOT NULL,
            to_url VARCHAR(500) NOT NULL,
            type SMALLINT NOT NULL DEFAULT 301,
            hits BIGINT UNSIGNED NOT NULL DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_from_url (from_url(191))
        ) {$charset};";

        $sql2 = "CREATE TABLE IF NOT EXISTS {$this->table_404_log()} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            url VARCHAR(500) NOT NULL,
            hits BIGINT UNSIGNED NOT NULL DEFAULT 1,
            last_hit DATETIME DEFAULT CURRENT_TIMESTAMP,
            referer VARCHAR(500) DEFAULT '',
            user_agent VARCHAR(500) DEFAULT '',
            PRIMARY KEY (id),
            UNIQUE KEY idx_url (url(191))
        ) {$charset};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql1 );
        dbDelta( $sql2 );

        update_option( 'olo_seo_redirects_db', '1.0' );
    }

    /* ═══════════════════════════════════════════════════
     * REDIRECT MATCHING
     * ═══════════════════════════════════════════════════ */

    public function check_redirect() {
        if ( is_admin() ) {
            return;
        }

        $request_path = $this->get_request_path();
        if ( ! $request_path ) {
            return;
        }

        global $wpdb;

        // Exact match
        $redirect = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$this->table_redirects()} WHERE from_url = %s LIMIT 1",
            $request_path
        ) );

        // Try with/without trailing slash
        if ( ! $redirect ) {
            $alt = rtrim( $request_path, '/' );
            if ( $alt === $request_path ) {
                $alt = $request_path . '/';
            }
            $redirect = $wpdb->get_row( $wpdb->prepare(
                "SELECT * FROM {$this->table_redirects()} WHERE from_url = %s LIMIT 1",
                $alt
            ) );
        }

        // Regex match (from_url starts with ~)
        if ( ! $redirect ) {
            $regex_redirects = $wpdb->get_results(
                "SELECT * FROM {$this->table_redirects()} WHERE from_url LIKE '~%' LIMIT 50"
            );
            foreach ( $regex_redirects as $r ) {
                $pattern = substr( $r->from_url, 1 );
                if ( @preg_match( '#' . $pattern . '#i', $request_path, $matches ) ) {
                    $redirect = $r;
                    // Replace $1, $2 etc in target
                    if ( ! empty( $matches ) ) {
                        foreach ( $matches as $i => $m ) {
                            if ( $i === 0 ) continue;
                            $redirect->to_url = str_replace( '$' . $i, $m, $redirect->to_url );
                        }
                    }
                    break;
                }
            }
        }

        if ( ! $redirect ) {
            return;
        }

        // Update hit counter
        $wpdb->query( $wpdb->prepare(
            "UPDATE {$this->table_redirects()} SET hits = hits + 1 WHERE id = %d",
            $redirect->id
        ) );

        $to_url = $redirect->to_url;
        // Absolute URL
        if ( strpos( $to_url, 'http' ) !== 0 ) {
            $to_url = home_url( $to_url );
        }

        $status = intval( $redirect->type );
        if ( ! in_array( $status, [ 301, 302, 307, 410 ], true ) ) {
            $status = 301;
        }

        if ( $status === 410 ) {
            status_header( 410 );
            echo '<h1>410 Gone</h1><p>Questa risorsa non è più disponibile.</p>';
            exit;
        }

        wp_redirect( $to_url, $status );
        exit;
    }

    private function get_request_path() {
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        $parsed      = wp_parse_url( $request_uri );
        $path        = $parsed['path'] ?? '/';

        // Remove WordPress subdirectory if any
        $home_path = wp_parse_url( home_url(), PHP_URL_PATH ) ?: '';
        if ( $home_path && strpos( $path, $home_path ) === 0 ) {
            $path = substr( $path, strlen( $home_path ) );
        }

        if ( ! $path ) {
            $path = '/';
        }

        return $path;
    }

    /* ═══════════════════════════════════════════════════
     * 404 LOGGING
     * ═══════════════════════════════════════════════════ */

    public function log_404() {
        if ( ! is_404() ) {
            return;
        }

        // Skip bots/crawlers for common asset extensions
        $path = $this->get_request_path();
        $skip_ext = [ '.css', '.js', '.map', '.ico', '.png', '.jpg', '.jpeg', '.gif', '.svg', '.webp', '.woff', '.woff2', '.ttf', '.eot' ];
        foreach ( $skip_ext as $ext ) {
            if ( substr( $path, -strlen( $ext ) ) === $ext ) {
                return;
            }
        }

        global $wpdb;
        $url        = substr( $path, 0, 500 );
        $referer    = isset( $_SERVER['HTTP_REFERER'] ) ? substr( sanitize_text_field( $_SERVER['HTTP_REFERER'] ), 0, 500 ) : '';
        $user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? substr( sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ), 0, 500 ) : '';

        // Upsert: increment hits if exists, otherwise insert
        $existing = $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM {$this->table_404_log()} WHERE url = %s LIMIT 1",
            $url
        ) );

        if ( $existing ) {
            $wpdb->query( $wpdb->prepare(
                "UPDATE {$this->table_404_log()} SET hits = hits + 1, last_hit = NOW(), referer = %s, user_agent = %s WHERE id = %d",
                $referer,
                $user_agent,
                $existing
            ) );
        } else {
            $wpdb->insert( $this->table_404_log(), [
                'url'        => $url,
                'hits'       => 1,
                'last_hit'   => current_time( 'mysql' ),
                'referer'    => $referer,
                'user_agent' => $user_agent,
            ], [ '%s', '%d', '%s', '%s', '%s' ] );
        }

        // Prune old entries (keep max 500)
        $count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$this->table_404_log()}" );
        if ( $count > 500 ) {
            $wpdb->query( "DELETE FROM {$this->table_404_log()} ORDER BY last_hit ASC LIMIT " . ( $count - 500 ) );
        }
    }

    /* ═══════════════════════════════════════════════════
     * IndexNow
     * ═══════════════════════════════════════════════════ */

    public function indexnow_ping( $post_id ) {
        $general = get_option( 'olo_seo_advanced', [] );
        if ( empty( $general['indexnow_key'] ) ) {
            return;
        }

        $key = sanitize_text_field( $general['indexnow_key'] );
        $url = get_permalink( $post_id );
        if ( ! $url ) {
            return;
        }

        $host = wp_parse_url( home_url(), PHP_URL_HOST );

        // Fire and forget — non-blocking
        wp_remote_post( 'https://api.indexnow.org/IndexNow', [
            'timeout'  => 5,
            'blocking' => false,
            'body'     => wp_json_encode( [
                'host'    => $host,
                'key'     => $key,
                'urlList' => [ $url ],
            ] ),
            'headers'  => [
                'Content-Type' => 'application/json',
            ],
        ] );
    }

    /* ═══════════════════════════════════════════════════
     * ADMIN PAGE
     * ═══════════════════════════════════════════════════ */

    public function add_menu() {
        add_submenu_page(
            'olobuilder',
            __( 'Redirect & 404', 'olobuilder' ),
            __( 'Redirect & 404', 'olobuilder' ),
            'manage_options',
            'olo-redirects',
            [ $this, 'render_page' ]
        );
    }

    public function enqueue_admin_assets( $hook ) {
        if ( $hook !== 'olobuild_page_olo-redirects' ) {
            return;
        }
        wp_enqueue_style( 'olo-seo-admin', OLO_URL . 'assets/css/seo-admin.css', [], OLO_VERSION );
    }

    public function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $active_tab = sanitize_text_field( $_GET['tab'] ?? 'redirects' );
        global $wpdb;

        ?>
        <div class="wrap olo-seo-wrap">
            <h1>
                <img src="<?php echo esc_url( OLO_URL . 'assets/img/ob-menu.png' ); ?>" style="width:24px;height:24px;vertical-align:middle;margin-right:8px;" alt="">
                Redirect &amp; Monitor 404
            </h1>

            <nav class="nav-tab-wrapper olo-seo-tabs">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=olo-redirects&tab=redirects' ) ); ?>" class="nav-tab <?php echo $active_tab === 'redirects' ? 'nav-tab-active' : ''; ?>">
                    <span class="dashicons dashicons-randomize" style="font-size:16px;line-height:1.8;margin-right:4px;"></span>
                    Redirect (<?php echo intval( $wpdb->get_var( "SELECT COUNT(*) FROM {$this->table_redirects()}" ) ); ?>)
                </a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=olo-redirects&tab=monitor' ) ); ?>" class="nav-tab <?php echo $active_tab === 'monitor' ? 'nav-tab-active' : ''; ?>">
                    <span class="dashicons dashicons-warning" style="font-size:16px;line-height:1.8;margin-right:4px;"></span>
                    Monitor 404 (<?php echo intval( $wpdb->get_var( "SELECT COUNT(*) FROM {$this->table_404_log()}" ) ); ?>)
                </a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=olo-redirects&tab=indexnow' ) ); ?>" class="nav-tab <?php echo $active_tab === 'indexnow' ? 'nav-tab-active' : ''; ?>">
                    <span class="dashicons dashicons-megaphone" style="font-size:16px;line-height:1.8;margin-right:4px;"></span>
                    IndexNow
                </a>
            </nav>

            <div class="olo-seo-form" style="padding:20px;">
                <?php
                if ( $active_tab === 'redirects' ) {
                    $this->render_tab_redirects();
                } elseif ( $active_tab === 'monitor' ) {
                    $this->render_tab_monitor();
                } elseif ( $active_tab === 'indexnow' ) {
                    $this->render_tab_indexnow();
                }
                ?>
            </div>
        </div>
        <?php
    }

    /* ─── Tab: Redirect ─── */

    private function render_tab_redirects() {
        global $wpdb;
        $redirects = $wpdb->get_results( "SELECT * FROM {$this->table_redirects()} ORDER BY created_at DESC LIMIT 200" );
        ?>
        <div class="olo-seo-section">
            <h2>Aggiungi Redirect</h2>
            <table class="form-table" id="olo-new-redirect-form">
                <tr>
                    <th style="width:80px;">Da URL</th>
                    <td><input type="text" id="olo-redir-from" class="regular-text" placeholder="/vecchio-percorso/" style="width:100%;"></td>
                </tr>
                <tr>
                    <th>A URL</th>
                    <td><input type="text" id="olo-redir-to" class="regular-text" placeholder="/nuovo-percorso/ oppure https://..." style="width:100%;"></td>
                </tr>
                <tr>
                    <th>Tipo</th>
                    <td>
                        <select id="olo-redir-type">
                            <option value="301">301 — Permanente (consigliato)</option>
                            <option value="302">302 — Temporaneo</option>
                            <option value="307">307 — Temporaneo (strict)</option>
                            <option value="410">410 — Risorsa rimossa (Gone)</option>
                        </select>
                        &nbsp;
                        <button type="button" class="button button-primary" onclick="oloSaveRedirect()">Aggiungi</button>
                    </td>
                </tr>
            </table>
            <p class="description">Prefisso <code>~</code> per regex. Es: <code>~/vecchio/(.*)</code> → <code>/nuovo/$1</code></p>
        </div>

        <div class="olo-seo-section">
            <h2>Redirect attivi (<?php echo count( $redirects ); ?>)</h2>
            <?php if ( empty( $redirects ) ) : ?>
                <p style="color:#999;">Nessun redirect configurato.</p>
            <?php else : ?>
                <table class="wp-list-table widefat striped" style="margin-top:8px;">
                    <thead>
                        <tr>
                            <th>Da</th>
                            <th>A</th>
                            <th style="width:60px;">Tipo</th>
                            <th style="width:60px;">Hit</th>
                            <th style="width:80px;">Data</th>
                            <th style="width:60px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $redirects as $r ) : ?>
                            <tr id="olo-redir-row-<?php echo intval( $r->id ); ?>">
                                <td><code style="font-size:12px;"><?php echo esc_html( $r->from_url ); ?></code></td>
                                <td><code style="font-size:12px;"><?php echo esc_html( $r->to_url ); ?></code></td>
                                <td>
                                    <span class="<?php echo $r->type == 301 ? 'dashicons dashicons-migrate' : ( $r->type == 410 ? 'dashicons dashicons-no' : 'dashicons dashicons-randomize' ); ?>" style="font-size:14px;color:<?php echo $r->type == 301 ? '#00a32a' : ( $r->type == 410 ? '#d63638' : '#dba617' ); ?>;"></span>
                                    <?php echo intval( $r->type ); ?>
                                </td>
                                <td><?php echo intval( $r->hits ); ?></td>
                                <td style="font-size:12px;color:#666;"><?php echo esc_html( wp_date( 'd/m/Y', strtotime( $r->created_at ) ) ); ?></td>
                                <td><button type="button" class="button button-link-delete" onclick="oloDeleteRedirect(<?php echo intval( $r->id ); ?>)">Elimina</button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <script>
        function oloSaveRedirect() {
            var from = document.getElementById('olo-redir-from').value.trim();
            var to   = document.getElementById('olo-redir-to').value.trim();
            var type = document.getElementById('olo-redir-type').value;
            if (!from) { alert('Inserisci URL di origine.'); return; }
            if (!to && type !== '410') { alert('Inserisci URL di destinazione.'); return; }

            var fd = new FormData();
            fd.append('action', 'olo_seo_save_redirect');
            fd.append('_wpnonce', '<?php echo wp_create_nonce( 'olo_seo_redirect' ); ?>');
            fd.append('from_url', from);
            fd.append('to_url', to);
            fd.append('type', type);

            fetch(ajaxurl, { method: 'POST', body: fd })
                .then(function(r){ return r.json(); })
                .then(function(d){
                    if (d.success) { location.reload(); }
                    else { alert(d.data || 'Errore'); }
                });
        }

        function oloDeleteRedirect(id) {
            if (!confirm('Eliminare questo redirect?')) return;
            var fd = new FormData();
            fd.append('action', 'olo_seo_delete_redirect');
            fd.append('_wpnonce', '<?php echo wp_create_nonce( 'olo_seo_redirect' ); ?>');
            fd.append('id', id);

            fetch(ajaxurl, { method: 'POST', body: fd })
                .then(function(r){ return r.json(); })
                .then(function(d){
                    if (d.success) {
                        var row = document.getElementById('olo-redir-row-' + id);
                        if (row) row.remove();
                    }
                });
        }
        </script>
        <?php
    }

    /* ─── Tab: Monitor 404 ─── */

    private function render_tab_monitor() {
        global $wpdb;
        $entries = $wpdb->get_results( "SELECT * FROM {$this->table_404_log()} ORDER BY hits DESC, last_hit DESC LIMIT 100" );
        ?>
        <div class="olo-seo-section">
            <h2>
                URL che generano errore 404
                <?php if ( ! empty( $entries ) ) : ?>
                    <button type="button" class="button" onclick="oloClear404Log()" style="margin-left:12px;font-size:12px;">Svuota log</button>
                <?php endif; ?>
            </h2>
            <p class="description">Monitora gli URL visitati che non esistono. Puoi convertirli in redirect con un click.</p>

            <?php if ( empty( $entries ) ) : ?>
                <p style="color:#999;margin-top:16px;">Nessun errore 404 registrato. Le visite a URL inesistenti appariranno qui.</p>
            <?php else : ?>
                <table class="wp-list-table widefat striped" style="margin-top:12px;">
                    <thead>
                        <tr>
                            <th>URL</th>
                            <th style="width:60px;">Hit</th>
                            <th style="width:130px;">Ultimo accesso</th>
                            <th>Referrer</th>
                            <th style="width:160px;">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $entries as $e ) : ?>
                            <tr id="olo-404-row-<?php echo intval( $e->id ); ?>">
                                <td><code style="font-size:12px;"><?php echo esc_html( $e->url ); ?></code></td>
                                <td>
                                    <span style="background:<?php echo $e->hits > 10 ? '#d63638' : ( $e->hits > 3 ? '#dba617' : '#999' ); ?>;color:#fff;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600;">
                                        <?php echo intval( $e->hits ); ?>
                                    </span>
                                </td>
                                <td style="font-size:12px;color:#666;"><?php echo esc_html( wp_date( 'd/m/Y H:i', strtotime( $e->last_hit ) ) ); ?></td>
                                <td style="font-size:11px;color:#888;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo esc_attr( $e->referer ); ?>"><?php echo esc_html( $e->referer ?: '—' ); ?></td>
                                <td>
                                    <button type="button" class="button button-small" onclick="olo404ToRedirect(<?php echo intval( $e->id ); ?>, '<?php echo esc_js( $e->url ); ?>')">
                                        &rarr; Redirect
                                    </button>
                                    <button type="button" class="button button-small button-link-delete" onclick="oloDelete404(<?php echo intval( $e->id ); ?>)">
                                        &times;
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Modal per creare redirect da 404 -->
        <div id="olo-404-redirect-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:100000;display:none;align-items:center;justify-content:center;">
            <div style="background:#fff;border-radius:8px;padding:24px;width:480px;max-width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.3);">
                <h3 style="margin-top:0;">Crea redirect da 404</h3>
                <p>Da: <code id="olo-404-modal-from"></code></p>
                <label style="display:block;margin:12px 0 4px;font-weight:600;">Destinazione:</label>
                <input type="text" id="olo-404-modal-to" style="width:100%;" placeholder="/pagina-corretta/">
                <input type="hidden" id="olo-404-modal-id">
                <div style="margin-top:16px;text-align:right;">
                    <button type="button" class="button" onclick="olo404ModalClose()">Annulla</button>
                    <button type="button" class="button button-primary" onclick="olo404ModalSave()">Crea Redirect 301</button>
                </div>
            </div>
        </div>

        <script>
        function olo404ToRedirect(id, url) {
            document.getElementById('olo-404-modal-from').textContent = url;
            document.getElementById('olo-404-modal-to').value = '';
            document.getElementById('olo-404-modal-id').value = id;
            var modal = document.getElementById('olo-404-redirect-modal');
            modal.style.display = 'flex';
        }
        function olo404ModalClose() {
            document.getElementById('olo-404-redirect-modal').style.display = 'none';
        }
        function olo404ModalSave() {
            var id = document.getElementById('olo-404-modal-id').value;
            var to = document.getElementById('olo-404-modal-to').value.trim();
            if (!to) { alert('Inserisci URL di destinazione.'); return; }

            var fd = new FormData();
            fd.append('action', 'olo_seo_404_to_redirect');
            fd.append('_wpnonce', '<?php echo wp_create_nonce( 'olo_seo_redirect' ); ?>');
            fd.append('id', id);
            fd.append('to_url', to);

            fetch(ajaxurl, { method: 'POST', body: fd })
                .then(function(r){ return r.json(); })
                .then(function(d){
                    if (d.success) {
                        olo404ModalClose();
                        var row = document.getElementById('olo-404-row-' + id);
                        if (row) row.remove();
                    } else {
                        alert(d.data || 'Errore');
                    }
                });
        }
        function oloDelete404(id) {
            var fd = new FormData();
            fd.append('action', 'olo_seo_delete_404');
            fd.append('_wpnonce', '<?php echo wp_create_nonce( 'olo_seo_redirect' ); ?>');
            fd.append('id', id);

            fetch(ajaxurl, { method: 'POST', body: fd })
                .then(function(r){ return r.json(); })
                .then(function(d){
                    if (d.success) {
                        var row = document.getElementById('olo-404-row-' + id);
                        if (row) row.remove();
                    }
                });
        }
        function oloClear404Log() {
            if (!confirm('Svuotare tutto il log 404?')) return;
            var fd = new FormData();
            fd.append('action', 'olo_seo_clear_404_log');
            fd.append('_wpnonce', '<?php echo wp_create_nonce( 'olo_seo_redirect' ); ?>');

            fetch(ajaxurl, { method: 'POST', body: fd })
                .then(function(r){ return r.json(); })
                .then(function(d){
                    if (d.success) { location.reload(); }
                });
        }
        </script>
        <?php
    }

    /* ─── Tab: IndexNow ─── */

    private function render_tab_indexnow() {
        $adv = get_option( 'olo_seo_advanced', [] );
        $key = $adv['indexnow_key'] ?? '';
        ?>
        <div class="olo-seo-section">
            <h2>IndexNow</h2>
            <p class="description">IndexNow notifica istantaneamente i motori di ricerca (Bing, Yandex, Naver, Seznam) quando pubblichi o aggiorni un contenuto. Google non supporta ancora IndexNow direttamente ma riceve i dati tramite Bing.</p>

            <form method="post" action="options.php">
                <?php settings_fields( 'olo_seo_group' ); ?>
                <table class="form-table olo-seo-table">
                    <tr>
                        <th>API Key</th>
                        <td>
                            <input type="text" name="olo_seo_advanced[indexnow_key]" value="<?php echo esc_attr( $key ); ?>" class="regular-text" placeholder="Genera una chiave alfanumerica">
                            <p class="description">
                                Genera una chiave casuale (es. <code><?php echo esc_html( substr( md5( home_url() . AUTH_KEY ), 0, 32 ) ); ?></code>) e inseriscila qui.<br>
                                Crea anche un file <code><?php echo esc_html( home_url( '/' ) ); ?><strong>{chiave}.txt</strong></code> nella root del sito con la chiave come contenuto.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th>Stato</th>
                        <td>
                            <?php if ( $key ) : ?>
                                <span style="color:#00a32a;font-weight:600;">Attivo</span> — ogni pubblicazione/aggiornamento invia un ping IndexNow.
                                <br><br>
                                <strong>File di verifica:</strong> <code><?php echo esc_html( home_url( '/' . $key . '.txt' ) ); ?></code>
                                <br><small>Deve contenere solo: <code><?php echo esc_html( $key ); ?></code></small>
                            <?php else : ?>
                                <span style="color:#999;">Non configurato</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
                <?php submit_button( 'Salva' ); ?>
            </form>
        </div>
        <?php
    }

    /* ═══════════════════════════════════════════════════
     * AJAX HANDLERS
     * ═══════════════════════════════════════════════════ */

    public function ajax_save_redirect() {
        check_ajax_referer( 'olo_seo_redirect' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Permesso negato.' );
        }

        $from = sanitize_text_field( $_POST['from_url'] ?? '' );
        $to   = sanitize_text_field( $_POST['to_url'] ?? '' );
        $type = intval( $_POST['type'] ?? 301 );

        if ( ! $from ) {
            wp_send_json_error( 'URL di origine obbligatorio.' );
        }

        global $wpdb;

        // Check duplicates
        $exists = $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM {$this->table_redirects()} WHERE from_url = %s",
            $from
        ) );
        if ( $exists ) {
            wp_send_json_error( 'Redirect per questo URL esiste già.' );
        }

        $wpdb->insert( $this->table_redirects(), [
            'from_url'   => $from,
            'to_url'     => $to,
            'type'       => $type,
            'hits'       => 0,
            'created_at' => current_time( 'mysql' ),
        ], [ '%s', '%s', '%d', '%d', '%s' ] );

        wp_send_json_success( [ 'id' => $wpdb->insert_id ] );
    }

    public function ajax_delete_redirect() {
        check_ajax_referer( 'olo_seo_redirect' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Permesso negato.' );
        }

        $id = intval( $_POST['id'] ?? 0 );
        if ( ! $id ) {
            wp_send_json_error( 'ID obbligatorio.' );
        }

        global $wpdb;
        $wpdb->delete( $this->table_redirects(), [ 'id' => $id ], [ '%d' ] );

        wp_send_json_success();
    }

    public function ajax_delete_404() {
        check_ajax_referer( 'olo_seo_redirect' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Permesso negato.' );
        }

        $id = intval( $_POST['id'] ?? 0 );
        if ( ! $id ) {
            wp_send_json_error();
        }

        global $wpdb;
        $wpdb->delete( $this->table_404_log(), [ 'id' => $id ], [ '%d' ] );

        wp_send_json_success();
    }

    public function ajax_clear_404_log() {
        check_ajax_referer( 'olo_seo_redirect' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Permesso negato.' );
        }

        global $wpdb;
        $wpdb->query( "TRUNCATE TABLE {$this->table_404_log()}" );

        wp_send_json_success();
    }

    public function ajax_404_to_redirect() {
        check_ajax_referer( 'olo_seo_redirect' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Permesso negato.' );
        }

        $id    = intval( $_POST['id'] ?? 0 );
        $to    = sanitize_text_field( $_POST['to_url'] ?? '' );

        if ( ! $id || ! $to ) {
            wp_send_json_error( 'Dati mancanti.' );
        }

        global $wpdb;
        $entry = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$this->table_404_log()} WHERE id = %d",
            $id
        ) );

        if ( ! $entry ) {
            wp_send_json_error( 'Voce non trovata.' );
        }

        // Create redirect
        $wpdb->insert( $this->table_redirects(), [
            'from_url'   => $entry->url,
            'to_url'     => $to,
            'type'       => 301,
            'hits'       => 0,
            'created_at' => current_time( 'mysql' ),
        ], [ '%s', '%s', '%d', '%d', '%s' ] );

        // Remove from 404 log
        $wpdb->delete( $this->table_404_log(), [ 'id' => $id ], [ '%d' ] );

        wp_send_json_success();
    }
}
