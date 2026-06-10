<?php
/**
 * Olo_Tools — Pagina strumenti unificata.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Tools {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init() {
        add_action( 'admin_menu', [ $this, 'add_menu' ] );

        // AJAX handlers
        add_action( 'wp_ajax_olo_tools_flush_cache',       [ $this, 'ajax_flush_cache' ] );
        add_action( 'wp_ajax_olo_tools_toggle_option',     [ $this, 'ajax_toggle_option' ] );
        add_action( 'wp_ajax_olo_tools_url_replace',       [ $this, 'ajax_url_replace' ] );
        add_action( 'wp_ajax_olo_tools_rollback',          [ $this, 'ajax_rollback' ] );
        add_action( 'wp_ajax_olo_tools_save_maintenance',  [ $this, 'ajax_save_maintenance' ] );

        // Maintenance mode frontend hook — handled by Olo_Maintenance_Mode (class-maintenance-mode.php)
        // Removed duplicate hook to prevent race condition. Only class-maintenance-mode.php runs at priority 1.
    }

    /* ═══════════════════════════════════════════════════
     * MENU
     * ═══════════════════════════════════════════════════ */

    public function add_menu() {
        add_submenu_page(
            'olobuild',
            'Strumenti',
            'Strumenti',
            'manage_options',
            'olo-tools',
            [ $this, 'render_page' ]
        );
    }

    /* ═══════════════════════════════════════════════════
     * RENDER PAGE
     * ═══════════════════════════════════════════════════ */

    public function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $tab  = sanitize_key( $_GET['tab'] ?? 'generale' );
        $tabs = [
            'generale'         => __( 'Generale', 'olobuild' ),
            'url-replace'      => __( 'Sostituzione URL', 'olobuild' ),
            'versioni'         => __( 'Controllo Versione', 'olobuild' ),
            'manutenzione'     => __( 'Modalità di Manutenzione', 'olobuild' ),
            'template-website' => __( 'Template Website', 'olobuild' ),
        ];

        // Subnav items per cockpit_subnav()
        $subnav_items = [];
        foreach ( $tabs as $slug => $label ) {
            $subnav_items[] = [
                'slug'  => $slug,
                'label' => $label,
                'href'  => admin_url( 'admin.php?page=olo-tools&tab=' . $slug ),
            ];
        }

        ?>
        <?php Olo_Builder::cockpit_shell_open( '<b>' . esc_html__( 'Strumenti', 'olobuild' ) . '</b>' ); ?>
        <main class="olo-cockpit-main olo-cockpit-legacy olo-tools-page">

            <?php
            echo Olo_Builder::cockpit_page_head( [
                'title' => __( 'Strumenti', 'olobuild' ),
                'sub'   => __( 'Cache, manutenzione, sostituzione URL e rollback. Utilizzare con cura.', 'olobuild' ),
            ] );
            echo Olo_Builder::cockpit_subnav( $subnav_items, $tab );
            ?>

            <div id="olo-tools-msg" style="margin-top:16px"></div>

            <?php
            switch ( $tab ) {
                case 'generale':
                    $this->render_tab_generale();
                    break;
                case 'url-replace':
                    $this->render_tab_url_replace();
                    break;
                case 'versioni':
                    $this->render_tab_versioni();
                    break;
                case 'manutenzione':
                    $this->render_tab_manutenzione();
                    break;
                case 'template-website':
                    $this->render_tab_template_website();
                    break;
            }
            ?>

        </main>
        <?php Olo_Builder::cockpit_shell_close(); ?>

        <script>
        var oloToolsNonce = '<?php echo wp_create_nonce( 'olo_tools_nonce' ); ?>';

        function oloToolsMsg( text, type ) {
            var el = document.getElementById('olo-tools-msg');
            el.innerHTML = '<div class="olo-notice olo-notice-' + type + '">' + text + '</div>';
            setTimeout(function() { el.innerHTML = ''; }, 6000);
        }
        </script>

        <style>
            .olo-tools-page .olo-notice { padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; font-size: 13px; }
            .olo-tools-page .olo-notice-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
            .olo-tools-page .olo-notice-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
            .olo-tools-page .olo-field-row { display: flex; align-items: center; justify-content: space-between; gap: 20px; padding: 14px 0; border-bottom: 1px solid #f0f0f0; }
            .olo-tools-page .olo-field-row:last-child { border-bottom: none; }
            .olo-tools-page .olo-field-info { flex: 1; }
            .olo-tools-page .olo-field-info label { font-weight: 600; font-size: 13px; color: #1a1a1a; display: block; margin-bottom: 2px; }
            .olo-tools-page .olo-field-hint { font-size: 12px; color: #999; }
            .olo-tools-page .olo-btn-action { display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; font-family: inherit; cursor: pointer; color: #fff; background: #1a1a1a; transition: all .15s; }
            .olo-tools-page .olo-btn-action:hover { background: #333; box-shadow: 0 4px 12px rgba(0,0,0,.15); }
            .olo-tools-page .olo-btn-action:disabled { opacity: 0.5; cursor: not-allowed; box-shadow: none; }
            .olo-tools-page .olo-btn-action.red { background: #dc2626; }
            .olo-tools-page .olo-btn-action.red:hover { background: #b91c1c; }
            .olo-tools-page .olo-btn-small { padding: 5px 12px; font-size: 12px; font-weight: 600; font-family: inherit; border: 1.5px solid #e5e0d8; border-radius: 8px; background: #fff; color: #666; cursor: pointer; transition: all .15s; }
            .olo-tools-page .olo-btn-small:hover { background: #f5f0eb; color: #1a1a1a; border-color: #d8d0c4; }
            .olo-tools-page .olo-tpl-table { width: 100%; border-collapse: collapse; font-size: 13px; }
            .olo-tools-page .olo-tpl-table th { text-align: left; padding: 10px 14px; border-bottom: 2px solid #f0f0f0; font-weight: 700; font-size: 11px; color: #999; text-transform: uppercase; letter-spacing: .05em; }
            .olo-tools-page .olo-tpl-table td { padding: 12px 14px; border-bottom: 1px solid #f5f5f5; color: #1a1a1a; vertical-align: middle; }
            .olo-tools-page .olo-tpl-table tbody tr:hover td { background: #fafafa; }
            .olo-tools-page .olo-rev-row td, .olo-tools-page .olo-rev-row:hover td { background: #faf8f5; }
            .olo-tools-page .olo-rev-row td { padding: 10px 14px 10px 32px; font-size: 12px; }
            .olo-tools-page .olo-maint-warning { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-top: 12px; }
            .olo-tools-page .olo-field-group { margin-bottom: 16px; }
            .olo-tools-page .olo-field-group label { display: block; font-weight: 600; color: #1a1a1a; margin-bottom: 6px; font-size: 13px; }
        </style>
        <?php
    }

    /* ─────────────────────────────────────────────
     * Tab: Generale
     * ───────────────────────────────────────────── */

    private function render_tab_generale() {
        $safe_mode = get_option( 'olo_safe_mode', '' );
        $debug_bar = get_option( 'olo_debug_bar', '' );
        ?>

        <!-- Cache -->
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6l9-4 9 4"/><path d="M3 6v12l9 4 9-4V6"/><path d="M3 6l9 4 9-4"/><path d="M12 10v12"/></svg>
                </div>
                <div>
                    <h3>Cache di Olobuild</h3>
                    <p>Cancella i file CSS obsoleti e i dati memorizzati nella cache del database. Rigenereremo questi file la prossima volta che qualcuno visiter&agrave; una pagina del tuo sito web.</p>
                </div>
            </div>
            <div class="olo-card-body">
                <button type="button" class="olo-btn-action" id="olo-tools-flush-cache">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                    Cancella file e dati
                </button>
            </div>
        </div>

        <!-- Modalita sicura -->
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div>
                    <h3>Modalit&agrave; sicura</h3>
                    <p>La modalit&agrave; sicura ti consente di risolvere i problemi caricando solo l'editor, senza caricare il tema o altri plugin.</p>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Stato</label>
                    </div>
                    <select class="olo-select olo-w-md" id="olo-tools-safe-mode" data-key="olo_safe_mode">
                        <option value="" <?php selected( $safe_mode, '' ); ?>>Disabilita</option>
                        <option value="1" <?php selected( $safe_mode, '1' ); ?>>Abilita</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Barra Debug -->
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h.01"/><path d="M8 16V8a4 4 0 018 0v8"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="M6 8l-2-2"/><path d="M18 8l2-2"/></svg>
                </div>
                <div>
                    <h3>Barra Debug</h3>
                    <p>La Barra di Debug aggiunge un menu nella barra di amministrazione che elenca tutti i template utilizzati in una pagina che viene visualizzata.</p>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Stato</label>
                    </div>
                    <select class="olo-select olo-w-md" id="olo-tools-debug-bar" data-key="olo_debug_bar">
                        <option value="" <?php selected( $debug_bar, '' ); ?>>Disabilita</option>
                        <option value="1" <?php selected( $debug_bar, '1' ); ?>>Abilita</option>
                    </select>
                </div>
            </div>
        </div>

        <script>
        (function() {
            // Flush cache
            document.getElementById('olo-tools-flush-cache').addEventListener('click', function() {
                var btn = this;
                var origText = btn.innerHTML;
                btn.disabled = true;
                btn.textContent = 'Attendere...';
                fetch(ajaxurl, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'action=olo_tools_flush_cache&_wpnonce=' + oloToolsNonce
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        oloToolsMsg(data.data.message, 'success');
                    } else {
                        oloToolsMsg(data.data || 'Errore', 'error');
                    }
                })
                .catch(function() { oloToolsMsg('Errore di rete', 'error'); })
                .finally(function() { btn.disabled = false; btn.innerHTML = origText; });
            });

            // Toggle options (safe mode + debug bar)
            document.querySelectorAll('#olo-tools-safe-mode, #olo-tools-debug-bar').forEach(function(sel) {
                sel.addEventListener('change', function() {
                    var key = this.getAttribute('data-key');
                    var val = this.value;
                    fetch(ajaxurl, {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: 'action=olo_tools_toggle_option&_wpnonce=' + oloToolsNonce + '&key=' + encodeURIComponent(key) + '&value=' + encodeURIComponent(val)
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.success) {
                            oloToolsMsg(data.data.message, 'success');
                        } else {
                            oloToolsMsg(data.data || 'Errore', 'error');
                        }
                    })
                    .catch(function() { oloToolsMsg('Errore di rete', 'error'); });
                });
            });
        })();
        </script>
        <?php
    }

    /* ─────────────────────────────────────────────
     * Tab: Sostituzione URL
     * ───────────────────────────────────────────── */

    private function render_tab_url_replace() {
        ?>
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
                </div>
                <div>
                    <h3>Sostituzione dell'URL</h3>
                    <p>Sostituisci un URL vecchio con uno nuovo in tutti i template Olobuild. Utile dopo una migrazione del sito.</p>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-field-group">
                    <label for="olo-url-old">URL vecchio:</label>
                    <input type="text" id="olo-url-old" class="olo-input olo-w-lg" placeholder="https://vecchio-dominio.it" />
                </div>
                <div class="olo-field-group">
                    <label for="olo-url-new">URL nuovo:</label>
                    <input type="text" id="olo-url-new" class="olo-input olo-w-lg" placeholder="https://nuovo-dominio.it" />
                </div>
                <button type="button" class="olo-btn-action" id="olo-tools-url-replace">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 1l4 4-4 4"/><path d="M3 11V9a4 4 0 014-4h14"/><path d="M7 23l-4-4 4-4"/><path d="M21 13v2a4 4 0 01-4 4H3"/></svg>
                    Sostituisci URL
                </button>
            </div>
        </div>

        <script>
        (function() {
            document.getElementById('olo-tools-url-replace').addEventListener('click', function() {
                var btn = this;
                var oldUrl = document.getElementById('olo-url-old').value.trim();
                var newUrl = document.getElementById('olo-url-new').value.trim();
                if (!oldUrl || !newUrl) {
                    oloToolsMsg('Inserisci entrambi gli URL.', 'error');
                    return;
                }
                var origText = btn.innerHTML;
                btn.disabled = true;
                btn.textContent = 'Attendere...';
                fetch(ajaxurl, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'action=olo_tools_url_replace&_wpnonce=' + oloToolsNonce
                        + '&old_url=' + encodeURIComponent(oldUrl)
                        + '&new_url=' + encodeURIComponent(newUrl)
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        oloToolsMsg(data.data.message, 'success');
                    } else {
                        oloToolsMsg(data.data || 'Errore', 'error');
                    }
                })
                .catch(function() { oloToolsMsg('Errore di rete', 'error'); })
                .finally(function() { btn.disabled = false; btn.innerHTML = origText; });
            });
        })();
        </script>
        <?php
    }

    /* ─────────────────────────────────────────────
     * Tab: Controllo Versione
     * ───────────────────────────────────────────── */

    private function render_tab_versioni() {
        global $wpdb;
        $prefix = $wpdb->prefix;

        $templates = $wpdb->get_results(
            "SELECT t.id, t.title, t.type, t.updated_at,
                (SELECT COUNT(*) FROM {$prefix}olo_revisions r WHERE r.template_id = t.id) AS rev_count
             FROM {$prefix}olo_templates t ORDER BY t.title"
        );
        ?>
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="10"/></svg>
                </div>
                <div>
                    <h3>Controllo Versione</h3>
                    <p>Visualizza e ripristina versioni precedenti dei template.</p>
                </div>
            </div>
            <div class="olo-card-body" style="padding: 0;">
                <table class="olo-tpl-table">
                    <thead>
                        <tr>
                            <th>Template</th>
                            <th>Tipo</th>
                            <th>Revisioni</th>
                            <th>Ultima modifica</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ( empty( $templates ) ) : ?>
                            <tr><td colspan="5" style="text-align:center; padding:20px; color:#888;">Nessun template trovato.</td></tr>
                        <?php else : ?>
                            <?php foreach ( $templates as $tpl ) : ?>
                            <tr>
                                <td><strong><?php echo esc_html( $tpl->title ); ?></strong></td>
                                <td><?php echo esc_html( $tpl->type ); ?></td>
                                <td><?php echo intval( $tpl->rev_count ); ?></td>
                                <td><?php echo esc_html( $tpl->updated_at ); ?></td>
                                <td>
                                    <?php if ( $tpl->rev_count > 0 ) : ?>
                                    <button type="button" class="olo-btn-small olo-tools-show-revs" data-id="<?php echo intval( $tpl->id ); ?>">Vedi revisioni</button>
                                    <?php else : ?>
                                    <span style="color:#aaa; font-size:12px;">Nessuna revisione</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr class="olo-rev-row" id="olo-revs-<?php echo intval( $tpl->id ); ?>" style="display:none;">
                                <td colspan="5">
                                    <div class="olo-rev-list" data-id="<?php echo intval( $tpl->id ); ?>">Caricamento...</div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script>
        (function() {
            // Toggle revisions
            document.querySelectorAll('.olo-tools-show-revs').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var id = this.getAttribute('data-id');
                    var row = document.getElementById('olo-revs-' + id);
                    if (row.style.display === 'none') {
                        row.style.display = '';
                        this.textContent = 'Nascondi';
                        loadRevisions(id);
                    } else {
                        row.style.display = 'none';
                        this.textContent = 'Vedi revisioni';
                    }
                });
            });

            function loadRevisions(templateId) {
                var container = document.querySelector('.olo-rev-list[data-id="' + templateId + '"]');
                container.innerHTML = 'Caricamento...';
                fetch(wpApiSettings.root + 'olo/v1/templates/' + templateId + '/revisions', {
                    headers: { 'X-WP-Nonce': wpApiSettings.nonce }
                })
                .then(function(r) { return r.json(); })
                .then(function(revisions) {
                    if (!revisions || revisions.length === 0) {
                        container.innerHTML = '<em>Nessuna revisione disponibile.</em>';
                        return;
                    }
                    var html = '<table style="width:100%; font-size:12px;">';
                    html += '<tr><th style="text-align:left; padding:4px 8px;">Data</th><th style="text-align:left; padding:4px 8px;">Dimensione</th><th style="padding:4px 8px;">Azioni</th></tr>';
                    revisions.forEach(function(rev) {
                        var size = rev.content_size ? (Math.round(rev.content_size / 1024) + ' KB') : '—';
                        html += '<tr>';
                        html += '<td style="padding:4px 8px;">' + rev.created_at + '</td>';
                        html += '<td style="padding:4px 8px;">' + size + '</td>';
                        html += '<td style="padding:4px 8px;"><button type="button" class="olo-btn-small olo-tools-rollback" data-rev="' + rev.id + '">Ripristina</button></td>';
                        html += '</tr>';
                    });
                    html += '</table>';
                    container.innerHTML = html;

                    // Bind rollback buttons
                    container.querySelectorAll('.olo-tools-rollback').forEach(function(rbtn) {
                        rbtn.addEventListener('click', function() {
                            var revId = this.getAttribute('data-rev');
                            var rb = this;
                            rb.disabled = true;
                            rb.textContent = 'Attendere...';
                            fetch(ajaxurl, {
                                method: 'POST',
                                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                                body: 'action=olo_tools_rollback&_wpnonce=' + oloToolsNonce + '&revision_id=' + revId
                            })
                            .then(function(r) { return r.json(); })
                            .then(function(data) {
                                if (data.success) {
                                    oloToolsMsg(data.data.message, 'success');
                                    // Reload revisions
                                    loadRevisions(templateId);
                                } else {
                                    oloToolsMsg(data.data || 'Errore', 'error');
                                }
                            })
                            .catch(function() { oloToolsMsg('Errore di rete', 'error'); })
                            .finally(function() { rb.disabled = false; rb.textContent = 'Ripristina'; });
                        });
                    });
                })
                .catch(function() { container.innerHTML = '<em>Errore nel caricamento.</em>'; });
            }
        })();
        </script>
        <?php
    }

    /* ─────────────────────────────────────────────
     * Tab: Modalita di Manutenzione
     * ───────────────────────────────────────────── */

    private function render_tab_manutenzione() {
        global $wpdb;
        $prefix = $wpdb->prefix;

        $mode          = get_option( 'olo_maintenance_mode', 'off' );
        $bypass_roles  = get_option( 'olo_maintenance_bypass_roles', [ 'administrator' ] );
        if ( ! is_array( $bypass_roles ) ) {
            $bypass_roles = [ 'administrator' ];
        }
        $template_id            = get_option( 'olo_maintenance_template_id', '' );
        $coming_soon_template_id = get_option( 'olo_coming_soon_template_id', '' );
        $bypass_secret          = get_option( 'olo_maintenance_bypass_secret', '' );

        // Get all templates
        $templates = $wpdb->get_results( "SELECT id, title, type FROM {$prefix}olo_templates ORDER BY title" );

        // Get all WP roles
        $wp_roles = wp_roles()->get_names();
        ?>
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v2m0 4h.01"/><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                </div>
                <div>
                    <h3>Modalit&agrave; di manutenzione</h3>
                    <p>Impostare il tuo intero sito nella MODALIT&Agrave; DI MANUTENZIONE, significa che il sito sar&agrave; temporaneamente fuori servizio per manutenzione. Se imposti il sito in modalit&agrave; COMING SOON, gli utenti vedranno una pagina di anteprima indicando che il sito &egrave; in fase di preparazione.</p>
                </div>
            </div>
            <div class="olo-card-body">

                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Seleziona la modalit&agrave;</label>
                    </div>
                    <select class="olo-select olo-w-md" id="olo-maint-mode">
                        <option value="off" <?php selected( $mode, 'off' ); ?>>Disabilitato</option>
                        <option value="coming_soon" <?php selected( $mode, 'coming_soon' ); ?>>Coming Soon</option>
                        <option value="maintenance" <?php selected( $mode, 'maintenance' ); ?>>Manutenzione</option>
                    </select>
                </div>

                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Chi pu&ograve; accedere</label>
                        <span class="olo-field-hint">Seleziona i ruoli che possono bypassare la modalit&agrave; di manutenzione.</span>
                    </div>
                    <select class="olo-select olo-w-md" id="olo-maint-roles" multiple style="min-height: 80px;">
                        <?php foreach ( $wp_roles as $role_key => $role_name ) : ?>
                        <option value="<?php echo esc_attr( $role_key ); ?>" <?php echo in_array( $role_key, $bypass_roles, true ) ? 'selected' : ''; ?>>
                            <?php echo esc_html( translate_user_role( $role_name ) ); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Template Manutenzione</label>
                        <span class="olo-field-hint">Pagina mostrata quando il sito &egrave; in manutenzione (HTTP 503).</span>
                    </div>
                    <select class="olo-select olo-w-md" id="olo-maint-template">
                        <option value="">— Pagina predefinita —</option>
                        <?php foreach ( $templates as $tpl ) : ?>
                        <option value="<?php echo intval( $tpl->id ); ?>" <?php selected( $template_id, $tpl->id ); ?>>
                            <?php echo esc_html( $tpl->title ); ?> (<?php echo esc_html( $tpl->type ); ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>Template Coming Soon</label>
                        <span class="olo-field-hint">Pagina mostrata quando il sito &egrave; in modalit&agrave; Coming Soon (HTTP 200).</span>
                    </div>
                    <select class="olo-select olo-w-md" id="olo-maint-coming-soon-template">
                        <option value="">— Pagina predefinita —</option>
                        <?php foreach ( $templates as $tpl ) : ?>
                        <option value="<?php echo intval( $tpl->id ); ?>" <?php selected( $coming_soon_template_id, $tpl->id ); ?>>
                            <?php echo esc_html( $tpl->title ); ?> (<?php echo esc_html( $tpl->type ); ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label>URL segreta di bypass</label>
                        <span class="olo-field-hint">Aggiungi <code>?bypass=CHIAVE</code> all'URL per bypassare la manutenzione.</span>
                    </div>
                    <input type="text" class="olo-input olo-w-md" id="olo-maint-secret" value="<?php echo esc_attr( $bypass_secret ); ?>" placeholder="chiave-segreta" />
                </div>

                <div style="margin-top: 16px;">
                    <button type="button" class="olo-btn-action" id="olo-tools-save-maint">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Salva le modifiche
                    </button>
                </div>
            </div>
        </div>

        <script>
        (function() {
            document.getElementById('olo-tools-save-maint').addEventListener('click', function() {
                var btn = this;
                var origText = btn.innerHTML;
                btn.disabled = true;
                btn.textContent = 'Attendere...';

                var rolesSelect = document.getElementById('olo-maint-roles');
                var roles = [];
                for (var i = 0; i < rolesSelect.options.length; i++) {
                    if (rolesSelect.options[i].selected) {
                        roles.push(rolesSelect.options[i].value);
                    }
                }

                var body = 'action=olo_tools_save_maintenance&_wpnonce=' + oloToolsNonce
                    + '&mode=' + encodeURIComponent(document.getElementById('olo-maint-mode').value)
                    + '&template_id=' + encodeURIComponent(document.getElementById('olo-maint-template').value)
                    + '&coming_soon_template_id=' + encodeURIComponent(document.getElementById('olo-maint-coming-soon-template').value)
                    + '&bypass_secret=' + encodeURIComponent(document.getElementById('olo-maint-secret').value);

                roles.forEach(function(r) {
                    body += '&bypass_roles[]=' + encodeURIComponent(r);
                });

                fetch(ajaxurl, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: body
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        oloToolsMsg(data.data.message, 'success');
                    } else {
                        oloToolsMsg(data.data || 'Errore', 'error');
                    }
                })
                .catch(function() { oloToolsMsg('Errore di rete', 'error'); })
                .finally(function() { btn.disabled = false; btn.innerHTML = origText; });
            });
        })();
        </script>
        <?php
    }

    /* ─────────────────────────────────────────────
     * Tab: Template Website
     * ───────────────────────────────────────────── */

    private function render_tab_template_website() {
        // Delegate to Olo_Site_Import_Export if render method exists
        if ( class_exists( 'Olo_Site_Import_Export' ) && method_exists( Olo_Site_Import_Export::instance(), 'render_admin_page' ) ) {
            // Re-use the import/export cards inline (without shell, since we already have one)
            $this->render_template_website_inline();
            return;
        }

        // Fallback: link to the existing page
        ?>
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                </div>
                <div>
                    <h3>Import / Export</h3>
                    <p>Gestisci l'importazione e l'esportazione dei template del sito.</p>
                </div>
            </div>
            <div class="olo-card-body">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=olo-import-export' ) ); ?>" class="olo-btn-action">
                    Vai alla pagina Import/Export
                </a>
            </div>
        </div>
        <?php
    }

    /**
     * Inline version of template website import/export.
     */
    private function render_template_website_inline() {
        global $wpdb;
        $prefix    = $wpdb->prefix;
        $templates = $wpdb->get_results( "SELECT id, title, type FROM {$prefix}olo_templates ORDER BY title" );
        $rest_url  = esc_url( rest_url( 'olo/v1/' ) );
        $nonce     = wp_create_nonce( 'wp_rest' );
        ?>

        <!-- Esporta -->
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                </div>
                <div>
                    <h3>Esporta questo website</h3>
                    <p>Puoi scaricare questo sito come un file .zip, o caricarlo nella libreria.</p>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-field-group">
                    <label for="olo-tw-export-tpl">Template da esportare</label>
                    <select id="olo-tw-export-tpl" class="olo-select olo-w-lg">
                        <?php foreach ( $templates as $t ) : ?>
                        <option value="<?php echo intval( $t->id ); ?>"><?php echo esc_html( $t->title ); ?> (<?php echo esc_html( $t->type ); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="button" class="olo-btn-action" id="olo-tw-export-btn">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Esporta
                </button>
            </div>
        </div>

        <!-- Importa -->
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </div>
                <div>
                    <h3>Applica un template website</h3>
                    <p>Puoi importare il design e le impostazioni da un file .zip o sceglierli dalla libreria.</p>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-field-group">
                    <label for="olo-tw-import-file">Seleziona file JSON</label>
                    <input type="file" id="olo-tw-import-file" accept=".json" class="olo-file olo-w-lg" />
                </div>
                <button type="button" class="olo-btn-action" id="olo-tw-import-btn">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Importa
                </button>
            </div>
        </div>

        <script>
        (function() {
            var restUrl = '<?php echo $rest_url; ?>';
            var restNonce = '<?php echo $nonce; ?>';

            // Export
            document.getElementById('olo-tw-export-btn').addEventListener('click', function() {
                var tplId = document.getElementById('olo-tw-export-tpl').value;
                if (!tplId) return;
                var url = restUrl + 'export-template/' + tplId + '?include_media=1';
                window.open(url + '&_wpnonce=' + restNonce, '_blank');
            });

            // Import
            document.getElementById('olo-tw-import-btn').addEventListener('click', function() {
                var btn = this;
                var fileInput = document.getElementById('olo-tw-import-file');
                if (!fileInput.files.length) {
                    oloToolsMsg('Seleziona un file JSON da importare.', 'error');
                    return;
                }
                var origText = btn.innerHTML;
                btn.disabled = true;
                btn.textContent = 'Attendere...';

                var formData = new FormData();
                formData.append('file', fileInput.files[0]);

                fetch(restUrl + 'import-template', {
                    method: 'POST',
                    headers: { 'X-WP-Nonce': restNonce },
                    body: formData
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.id || data.success) {
                        oloToolsMsg('Template importato con successo!', 'success');
                    } else {
                        oloToolsMsg(data.message || 'Errore durante l\'importazione.', 'error');
                    }
                })
                .catch(function() { oloToolsMsg('Errore di rete', 'error'); })
                .finally(function() { btn.disabled = false; btn.innerHTML = origText; });
            });
        })();
        </script>
        <?php
    }

    /* ═══════════════════════════════════════════════════
     * AJAX HANDLERS
     * ═══════════════════════════════════════════════════ */

    /**
     * Flush cache: CSS files + Critical CSS + DB transients.
     */
    public function ajax_flush_cache() {
        check_ajax_referer( 'olo_tools_nonce', '_wpnonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Permesso negato.' );
        }

        $count = 0;

        // Asset optimizer cache
        if ( class_exists( 'Olo_Asset_Optimizer' ) ) {
            Olo_Asset_Optimizer::flush_all_cache();
            $count++;
        }

        // Critical CSS
        if ( class_exists( 'Olo_Critical_CSS' ) ) {
            $purged = Olo_Critical_CSS::purge_all();
            $count += (int) $purged;
        }

        // DB transients matching %olo_%
        global $wpdb;
        $deleted = $wpdb->query(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE '%\_transient\_%olo\_%'"
        );
        $count += max( 0, (int) $deleted );

        wp_send_json_success( [
            'message' => sprintf( 'Cache svuotata. %d elementi rimossi.', $count ),
        ] );
    }

    /**
     * Toggle a simple on/off option.
     */
    public function ajax_toggle_option() {
        check_ajax_referer( 'olo_tools_nonce', '_wpnonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Permesso negato.' );
        }

        $allowed_keys = [ 'olo_safe_mode', 'olo_debug_bar' ];
        $key   = sanitize_key( $_POST['key'] ?? '' );
        $value = sanitize_text_field( $_POST['value'] ?? '' );

        if ( ! in_array( $key, $allowed_keys, true ) ) {
            wp_send_json_error( 'Opzione non valida.' );
        }

        update_option( $key, $value );

        $label = $value ? 'abilitata' : 'disabilitata';
        wp_send_json_success( [
            'message' => sprintf( 'Opzione %s.', $label ),
        ] );
    }

    /**
     * URL replacement across all templates.
     */
    public function ajax_url_replace() {
        check_ajax_referer( 'olo_tools_nonce', '_wpnonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Permesso negato.' );
        }

        $old_url = esc_url_raw( trim( $_POST['old_url'] ?? '' ) );
        $new_url = esc_url_raw( trim( $_POST['new_url'] ?? '' ) );

        if ( empty( $old_url ) || empty( $new_url ) ) {
            wp_send_json_error( 'Inserisci entrambi gli URL.' );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'olo_templates';
        $rows  = $wpdb->get_results( "SELECT id, content FROM {$table}" );
        $db    = new Olo_Database();

        $modified = 0;

        foreach ( $rows as $row ) {
            $content_raw = $row->content;
            $replaced    = str_replace( $old_url, $new_url, $content_raw );

            if ( $replaced !== $content_raw ) {
                // Create revision of current content as backup
                $current_decoded = json_decode( $content_raw, true );
                if ( is_array( $current_decoded ) ) {
                    $db->create_revision( $row->id, $current_decoded );
                }

                // Update template with replaced content (raw JSON string)
                $wpdb->update(
                    $table,
                    [ 'content' => $replaced ],
                    [ 'id' => $row->id ],
                    [ '%s' ],
                    [ '%d' ]
                );
                $modified++;
            }
        }

        wp_send_json_success( [
            'message' => sprintf( 'Sostituzione completata. %d template modificati.', $modified ),
        ] );
    }

    /**
     * Rollback a template to a specific revision.
     */
    public function ajax_rollback() {
        check_ajax_referer( 'olo_tools_nonce', '_wpnonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Permesso negato.' );
        }

        $revision_id = intval( $_POST['revision_id'] ?? 0 );
        if ( ! $revision_id ) {
            wp_send_json_error( 'ID revisione non valido.' );
        }

        $db       = new Olo_Database();
        $revision = $db->get_revision( $revision_id );

        if ( ! $revision ) {
            wp_send_json_error( 'Revisione non trovata.' );
        }

        $template_id = intval( $revision['template_id'] );
        $template    = $db->get_template( $template_id );

        if ( ! $template ) {
            wp_send_json_error( 'Template non trovato.' );
        }

        // Backup current content as new revision
        if ( ! empty( $template['content'] ) ) {
            $db->create_revision( $template_id, $template['content'] );
        }

        // Restore revision content
        $db->update_template( $template_id, [
            'content' => $revision['content'],
        ] );

        // Flush cache
        if ( class_exists( 'Olo_Asset_Optimizer' ) ) {
            Olo_Asset_Optimizer::flush_all_cache();
        }
        if ( class_exists( 'Olo_Critical_CSS' ) ) {
            Olo_Critical_CSS::purge_all();
        }

        wp_send_json_success( [
            'message' => sprintf( 'Template ripristinato alla revisione #%d.', $revision_id ),
        ] );
    }

    /**
     * Save maintenance mode settings.
     */
    public function ajax_save_maintenance() {
        check_ajax_referer( 'olo_tools_nonce', '_wpnonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Permesso negato.' );
        }

        $mode                    = sanitize_key( $_POST['mode'] ?? 'off' );
        $template_id             = intval( $_POST['template_id'] ?? 0 );
        $coming_soon_template_id = intval( $_POST['coming_soon_template_id'] ?? 0 );
        $bypass_secret           = sanitize_text_field( $_POST['bypass_secret'] ?? '' );
        $bypass_roles            = array_map( 'sanitize_key', (array) ( $_POST['bypass_roles'] ?? [ 'administrator' ] ) );

        $valid_modes = [ 'off', 'coming_soon', 'maintenance' ];
        if ( ! in_array( $mode, $valid_modes, true ) ) {
            $mode = 'off';
        }

        update_option( 'olo_maintenance_mode', $mode );
        update_option( 'olo_maintenance_template_id', $template_id );
        update_option( 'olo_coming_soon_template_id', $coming_soon_template_id );
        update_option( 'olo_maintenance_bypass_secret', $bypass_secret );
        update_option( 'olo_maintenance_bypass_roles', $bypass_roles );

        wp_send_json_success( [
            'message' => 'Impostazioni di manutenzione salvate.',
        ] );
    }

    /* ═══════════════════════════════════════════════════
     * MAINTENANCE MODE — FRONTEND
     * ═══════════════════════════════════════════════════ */

    /**
     * Show maintenance/coming soon page to visitors.
     */
    public function maybe_show_maintenance() {
        $mode = get_option( 'olo_maintenance_mode', 'off' );
        if ( $mode === 'off' ) {
            return;
        }

        $template_id = get_option( 'olo_maintenance_template_id', 0 );
        if ( empty( $template_id ) ) {
            return;
        }

        // Bypass for allowed roles
        if ( is_user_logged_in() ) {
            $bypass_roles = get_option( 'olo_maintenance_bypass_roles', [ 'administrator' ] );
            if ( ! is_array( $bypass_roles ) ) {
                $bypass_roles = [ 'administrator' ];
            }
            $user = wp_get_current_user();
            foreach ( $bypass_roles as $role ) {
                if ( in_array( $role, (array) $user->roles, true ) ) {
                    return;
                }
            }
        }

        // Bypass via secret URL parameter
        $bypass_secret = get_option( 'olo_maintenance_bypass_secret', '' );
        if ( $bypass_secret && isset( $_GET['bypass'] ) ) {
            if ( sanitize_text_field( $_GET['bypass'] ) === $bypass_secret ) {
                return;
            }
        }

        // Don't block wp-admin, login, REST API, or AJAX
        if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
            return;
        }
        if ( strpos( $_SERVER['REQUEST_URI'], 'wp-login.php' ) !== false ) {
            return;
        }

        // Set appropriate HTTP status
        $status = ( $mode === 'maintenance' ) ? 503 : 200;
        status_header( $status );
        if ( $status === 503 ) {
            header( 'Retry-After: 3600' );
        }

        // Render the template
        if ( class_exists( 'Olo_Frontend_Renderer' ) ) {
            $renderer = new Olo_Frontend_Renderer();
            $db       = new Olo_Database();
            $template = $db->get_template( $template_id );

            if ( $template ) {
                echo '<!DOCTYPE html><html ' . get_language_attributes() . '><head>';
                echo '<meta charset="' . esc_attr( get_bloginfo( 'charset' ) ) . '">';
                echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
                wp_head();
                echo '</head><body class="olo-maintenance-page">';
                echo do_shortcode( '[olo_template id="' . intval( $template_id ) . '"]' );
                wp_footer();
                echo '</body></html>';
                exit;
            }
        }
    }
}
