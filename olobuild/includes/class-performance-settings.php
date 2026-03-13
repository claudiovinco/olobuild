<?php
/**
 * Olo_Performance_Settings — Pagina admin per Critical CSS, Asset Optimizer, Performance Hints.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Performance_Settings {

    private static $instance = null;

    const OPT = 'olo_performance';

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init() {
        add_action( 'admin_menu', [ $this, 'add_menu' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );

        // AJAX handlers
        add_action( 'wp_ajax_olo_perf_regenerate_critical', [ $this, 'ajax_regenerate_critical' ] );
        add_action( 'wp_ajax_olo_perf_purge_critical', [ $this, 'ajax_purge_critical' ] );
        add_action( 'wp_ajax_olo_perf_flush_css_cache', [ $this, 'ajax_flush_css_cache' ] );
    }

    /**
     * Get option with defaults.
     */
    public static function get_option() {
        $defaults = [
            // Critical CSS
            'critical_css_enabled'  => false,
            'critical_css_ttl'      => 7,
            'critical_css_sections' => 2,
            // Asset Optimizer
            'defer_js'              => true,
            'css_cache_files'       => true,
            'minify_css'            => true,
            // Performance Hints
            'resource_hints'        => true,
            'font_preload'          => true,
            'video_facade'          => true,
            'fetchpriority'         => true,
            'lazy_images'           => true,
            // Head cleanup
            'remove_jquery_migrate' => false,
            'remove_emoji_scripts'  => false,
            'remove_block_css'      => false,
            'remove_classic_theme'  => false,
            'dns_prefetch_domains'  => '',
            'preconnect_domains'    => '',
        ];

        $saved = get_option( self::OPT, [] );
        if ( ! is_array( $saved ) ) {
            $saved = [];
        }

        return wp_parse_args( $saved, $defaults );
    }

    /* ═══════════════════════════════════════════════════
     * MENU & REGISTRAZIONE
     * ═══════════════════════════════════════════════════ */

    public function add_menu() {
        add_submenu_page(
            'olobuilder',
            __( 'Performance', 'olobuilder' ),
            __( 'Performance', 'olobuilder' ),
            'manage_options',
            'olo-performance',
            [ $this, 'render_page' ]
        );
    }

    public function register_settings() {
        register_setting( 'olo_performance_group', self::OPT, [
            'sanitize_callback' => [ $this, 'sanitize' ],
        ] );
    }

    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'olo-performance' ) === false ) {
            return;
        }
        wp_enqueue_style( 'olo-perf-admin', OLO_URL . 'assets/css/perf-admin.css', [], OLO_VERSION );
    }

    public function sanitize( $input ) {
        $clean = [];

        // Booleans
        $bools = [
            'critical_css_enabled', 'defer_js', 'css_cache_files', 'minify_css',
            'resource_hints', 'font_preload', 'video_facade', 'fetchpriority',
            'lazy_images', 'remove_jquery_migrate', 'remove_emoji_scripts',
            'remove_block_css', 'remove_classic_theme',
        ];
        foreach ( $bools as $key ) {
            $clean[ $key ] = ! empty( $input[ $key ] );
        }

        // Integers
        $clean['critical_css_ttl']      = max( 1, min( 30, intval( $input['critical_css_ttl'] ?? 7 ) ) );
        $clean['critical_css_sections'] = max( 1, min( 5, intval( $input['critical_css_sections'] ?? 2 ) ) );

        // Textarea (domini)
        $clean['dns_prefetch_domains'] = sanitize_textarea_field( $input['dns_prefetch_domains'] ?? '' );
        $clean['preconnect_domains']   = sanitize_textarea_field( $input['preconnect_domains'] ?? '' );

        // Sync legacy option for Critical CSS
        update_option( 'olo_critical_css_enabled', $clean['critical_css_enabled'] ? '1' : '' );

        return $clean;
    }

    /* ═══════════════════════════════════════════════════
     * RENDER PAGE
     * ═══════════════════════════════════════════════════ */

    public function render_page() {
        $opt  = self::get_option();
        $tab  = sanitize_key( $_GET['tab'] ?? 'critical-css' );
        $tabs = [
            'critical-css'    => __( 'Critical CSS', 'olobuilder' ),
            'assets'          => __( 'Asset Optimizer', 'olobuilder' ),
            'hints'           => __( 'Performance Hints', 'olobuilder' ),
            'cleanup'         => __( 'Head Cleanup', 'olobuilder' ),
        ];

        // Critical CSS status
        $ccss_status = null;
        if ( class_exists( 'Olo_Critical_CSS' ) ) {
            $ccss_status = Olo_Critical_CSS::get_status();
        }

        // CSS cache info
        $css_cache_info = $this->get_css_cache_info();

        ?>
        <?php Olo_Builder::page_shell_open( 'Performance', 'olo-perf-page' ); ?>

            <div class="olo-admin-tabs">
                <?php foreach ( $tabs as $slug => $label ) : ?>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=olo-performance&tab=' . $slug ) ); ?>"
                       class="olo-admin-tab <?php echo $tab === $slug ? 'active' : ''; ?>">
                        <?php echo esc_html( $label ); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <form method="post" action="options.php" class="olo-perf-form">
                <?php settings_fields( 'olo_performance_group' ); ?>

                <?php
                switch ( $tab ) {
                    case 'critical-css':
                        $this->render_tab_critical_css( $opt, $ccss_status );
                        break;
                    case 'assets':
                        $this->render_tab_assets( $opt, $css_cache_info );
                        break;
                    case 'hints':
                        $this->render_tab_hints( $opt );
                        break;
                    case 'cleanup':
                        $this->render_tab_cleanup( $opt );
                        break;
                }
                ?>

                <div class="olo-actions" style="margin-top: 20px;">
                    <button type="submit" class="olo-btn-save">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        <?php esc_html_e( 'Salva impostazioni', 'olobuilder' ); ?>
                    </button>
                </div>
            </form>
        <?php Olo_Builder::page_shell_close(); ?>
        <?php
    }

    /* ─────────────────────────────────────────────
     * Tab: Critical CSS
     * ───────────────────────────────────────────── */

    private function render_tab_critical_css( $opt, $ccss_status ) {
        $n = self::OPT;
        ?>
        <!-- Settings card -->
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                </div>
                <div>
                    <h3><?php esc_html_e( 'Critical CSS', 'olobuilder' ); ?></h3>
                    <p><?php esc_html_e( 'Genera automaticamente il CSS above-the-fold e lo inietta inline nel <head>. Il foglio di stile principale viene caricato in modo asincrono.', 'olobuilder' ); ?></p>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Abilita Critical CSS', 'olobuilder' ); ?></label>
                        <span class="olo-field-hint"><?php esc_html_e( 'Migliora FCP e LCP inlinando il CSS critico e deferendo il resto.', 'olobuilder' ); ?></span>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo $n; ?>[critical_css_enabled]" value="1" <?php checked( $opt['critical_css_enabled'] ); ?> />
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Durata cache', 'olobuilder' ); ?></label>
                        <span class="olo-field-hint"><?php esc_html_e( 'Il CSS critico viene rigenerato automaticamente alla scadenza. Invalidato quando salvi un template.', 'olobuilder' ); ?></span>
                    </div>
                    <div class="olo-perf-number-wrap">
                        <input type="number" name="<?php echo $n; ?>[critical_css_ttl]" value="<?php echo esc_attr( $opt['critical_css_ttl'] ); ?>"
                               min="1" max="30" class="olo-field-input olo-perf-number" />
                        <span class="olo-perf-number-unit"><?php esc_html_e( 'giorni', 'olobuilder' ); ?></span>
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Sezioni above-the-fold', 'olobuilder' ); ?></label>
                        <span class="olo-field-hint"><?php esc_html_e( 'Quante sezioni del template analizzare per il CSS critico. 2 è il valore consigliato.', 'olobuilder' ); ?></span>
                    </div>
                    <input type="number" name="<?php echo $n; ?>[critical_css_sections]" value="<?php echo esc_attr( $opt['critical_css_sections'] ); ?>"
                           min="1" max="5" class="olo-field-input olo-perf-number" />
                </div>
            </div>
        </div>

        <!-- Status card -->
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon orange">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                </div>
                <div>
                    <h3><?php esc_html_e( 'Stato cache', 'olobuilder' ); ?></h3>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-status-grid">
                    <div class="olo-status-card">
                        <div class="olo-status-card-icon" style="background: <?php echo $opt['critical_css_enabled'] ? '#ecfdf5' : '#fef2f2'; ?>; color: <?php echo $opt['critical_css_enabled'] ? '#059669' : '#dc2626'; ?>;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <?php if ( $opt['critical_css_enabled'] ) : ?>
                                    <polyline points="20 6 9 17 4 12"/>
                                <?php else : ?>
                                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                <?php endif; ?>
                            </svg>
                        </div>
                        <div class="olo-status-card-value"><?php echo $opt['critical_css_enabled'] ? esc_html__( 'Attivo', 'olobuilder' ) : esc_html__( 'Off', 'olobuilder' ); ?></div>
                        <div class="olo-status-card-label"><?php esc_html_e( 'Stato', 'olobuilder' ); ?></div>
                    </div>

                    <div class="olo-status-card">
                        <div class="olo-status-card-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                        </div>
                        <div class="olo-status-card-value" id="olo-ccss-count"><?php echo $ccss_status ? intval( $ccss_status['cached_count'] ) : 0; ?></div>
                        <div class="olo-status-card-label"><?php esc_html_e( 'Pagine in cache', 'olobuilder' ); ?></div>
                    </div>

                    <div class="olo-status-card">
                        <div class="olo-status-card-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div class="olo-status-card-value" id="olo-ccss-last">
                            <?php
                            if ( $ccss_status && ! empty( $ccss_status['last_generated'] ) ) {
                                echo esc_html( $ccss_status['last_generated'] );
                            } else {
                                esc_html_e( 'Mai', 'olobuilder' );
                            }
                            ?>
                        </div>
                        <div class="olo-status-card-label"><?php esc_html_e( 'Ultima generazione', 'olobuilder' ); ?></div>
                    </div>
                </div>

                <div class="olo-actions">
                    <button type="button" class="olo-btn-save" id="olo-ccss-regenerate">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
                        <?php esc_html_e( 'Rigenera tutto', 'olobuilder' ); ?>
                    </button>
                    <button type="button" class="olo-btn-danger" id="olo-ccss-purge">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                        <?php esc_html_e( 'Svuota cache', 'olobuilder' ); ?>
                    </button>
                    <span id="olo-ccss-msg" class="olo-perf-inline-msg"></span>
                </div>
            </div>
        </div>

        <script>
        (function(){
            var nonce = '<?php echo wp_create_nonce( 'olo_perf_action' ); ?>';
            var msg   = document.getElementById('olo-ccss-msg');

            document.getElementById('olo-ccss-regenerate').addEventListener('click', function(){
                this.disabled = true;
                msg.textContent = '<?php echo esc_js( __( 'Rigenerazione in corso...', 'olobuilder' ) ); ?>';
                msg.className = 'olo-perf-inline-msg';
                fetch(ajaxurl + '?action=olo_perf_regenerate_critical&_nonce=' + nonce)
                    .then(function(r){return r.json()})
                    .then(function(d){
                        if(d.success){
                            msg.textContent = d.data.message;
                            msg.className = 'olo-perf-inline-msg success';
                            var el = document.getElementById('olo-ccss-count');
                            if(el){el.textContent = d.data.generated}
                        } else {
                            msg.textContent = d.data || 'Errore';
                            msg.className = 'olo-perf-inline-msg error';
                        }
                        document.getElementById('olo-ccss-regenerate').disabled = false;
                    });
            });

            document.getElementById('olo-ccss-purge').addEventListener('click', function(){
                if(!confirm('<?php echo esc_js( __( 'Svuotare tutta la cache Critical CSS?', 'olobuilder' ) ); ?>'))return;
                this.disabled = true;
                fetch(ajaxurl + '?action=olo_perf_purge_critical&_nonce=' + nonce)
                    .then(function(r){return r.json()})
                    .then(function(d){
                        if(d.success){
                            msg.textContent = d.data.message;
                            msg.className = 'olo-perf-inline-msg success';
                            var el = document.getElementById('olo-ccss-count');
                            if(el){el.textContent = '0'}
                        }
                        document.getElementById('olo-ccss-purge').disabled = false;
                    });
            });
        })();
        </script>
        <?php
    }

    /* ─────────────────────────────────────────────
     * Tab: Asset Optimizer
     * ───────────────────────────────────────────── */

    private function render_tab_assets( $opt, $cache_info ) {
        $n = self::OPT;
        ?>
        <!-- Settings card -->
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>
                </div>
                <div>
                    <h3><?php esc_html_e( 'Ottimizzazione Asset', 'olobuilder' ); ?></h3>
                    <p><?php esc_html_e( 'Controlla come vengono caricati JavaScript e CSS nel frontend.', 'olobuilder' ); ?></p>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Defer JavaScript', 'olobuilder' ); ?></label>
                        <span class="olo-field-hint"><?php esc_html_e( 'Aggiunge l\'attributo defer agli script frontend di Olobuild. Non blocca il rendering della pagina.', 'olobuilder' ); ?></span>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo $n; ?>[defer_js]" value="1" <?php checked( $opt['defer_js'] ); ?> />
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Cache CSS su file', 'olobuilder' ); ?></label>
                        <span class="olo-field-hint"><?php esc_html_e( 'Salva il CSS generato dai template come file statici invece di iniettarlo inline. Migliora il caching del browser.', 'olobuilder' ); ?></span>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo $n; ?>[css_cache_files]" value="1" <?php checked( $opt['css_cache_files'] ); ?> />
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Minifica CSS', 'olobuilder' ); ?></label>
                        <span class="olo-field-hint"><?php esc_html_e( 'Rimuove commenti, spazi e newline dal CSS generato.', 'olobuilder' ); ?></span>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo $n; ?>[minify_css]" value="1" <?php checked( $opt['minify_css'] ); ?> />
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- CSS Cache status card -->
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon orange">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div>
                    <h3><?php esc_html_e( 'Cache CSS', 'olobuilder' ); ?></h3>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-status-grid">
                    <div class="olo-status-card">
                        <div class="olo-status-card-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        </div>
                        <div class="olo-status-card-value"><?php echo intval( $cache_info['count'] ); ?></div>
                        <div class="olo-status-card-label"><?php esc_html_e( 'File in cache', 'olobuilder' ); ?></div>
                    </div>
                    <div class="olo-status-card">
                        <div class="olo-status-card-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg>
                        </div>
                        <div class="olo-status-card-value"><?php echo esc_html( $cache_info['size_human'] ); ?></div>
                        <div class="olo-status-card-label"><?php esc_html_e( 'Dimensione totale', 'olobuilder' ); ?></div>
                    </div>
                </div>

                <div class="olo-actions">
                    <button type="button" class="olo-btn-danger" id="olo-flush-css-cache">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                        <?php esc_html_e( 'Svuota cache CSS', 'olobuilder' ); ?>
                    </button>
                    <span id="olo-css-cache-msg" class="olo-perf-inline-msg"></span>
                </div>
            </div>
        </div>

        <script>
        (function(){
            var nonce = '<?php echo wp_create_nonce( 'olo_perf_action' ); ?>';
            document.getElementById('olo-flush-css-cache').addEventListener('click', function(){
                if(!confirm('<?php echo esc_js( __( 'Svuotare la cache CSS? I file verranno rigenerati automaticamente.', 'olobuilder' ) ); ?>'))return;
                this.disabled = true;
                var msg = document.getElementById('olo-css-cache-msg');
                fetch(ajaxurl + '?action=olo_perf_flush_css_cache&_nonce=' + nonce)
                    .then(function(r){return r.json()})
                    .then(function(d){
                        if(d.success){
                            msg.textContent = d.data.message;
                            msg.className = 'olo-perf-inline-msg success';
                        }
                        document.getElementById('olo-flush-css-cache').disabled = false;
                    });
            });
        })();
        </script>
        <?php
    }

    /* ─────────────────────────────────────────────
     * Tab: Performance Hints
     * ───────────────────────────────────────────── */

    private function render_tab_hints( $opt ) {
        $n = self::OPT;
        ?>
        <!-- Resource Hints card -->
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div>
                    <h3><?php esc_html_e( 'Resource Hints', 'olobuilder' ); ?></h3>
                    <p><?php esc_html_e( 'Suggerimenti al browser per precaricare risorse e velocizzare il caricamento.', 'olobuilder' ); ?></p>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'DNS Prefetch & Preconnect', 'olobuilder' ); ?></label>
                        <span class="olo-field-hint"><?php esc_html_e( 'Aggiunge dns-prefetch e preconnect per Google Fonts, YouTube, Vimeo e altri domini esterni.', 'olobuilder' ); ?></span>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo $n; ?>[resource_hints]" value="1" <?php checked( $opt['resource_hints'] ); ?> />
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Preload font', 'olobuilder' ); ?></label>
                        <span class="olo-field-hint"><?php esc_html_e( 'Precarica i font custom usati come body/heading per evitare FOUT (Flash of Unstyled Text).', 'olobuilder' ); ?></span>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo $n; ?>[font_preload]" value="1" <?php checked( $opt['font_preload'] ); ?> />
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Video facade', 'olobuilder' ); ?></label>
                        <span class="olo-field-hint"><?php esc_html_e( 'Mostra un\'anteprima statica dei video YouTube/Vimeo. L\'iframe si carica solo al click. Risparmia ~500KB per video.', 'olobuilder' ); ?></span>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo $n; ?>[video_facade]" value="1" <?php checked( $opt['video_facade'] ); ?> />
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'fetchpriority hero image', 'olobuilder' ); ?></label>
                        <span class="olo-field-hint"><?php esc_html_e( 'Aggiunge fetchpriority="high" alla prima immagine della pagina e rimuove lazy loading dagli elementi above-fold.', 'olobuilder' ); ?></span>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo $n; ?>[fetchpriority]" value="1" <?php checked( $opt['fetchpriority'] ); ?> />
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Lazy loading immagini', 'olobuilder' ); ?></label>
                        <span class="olo-field-hint"><?php esc_html_e( 'Aggiunge loading="lazy" alle immagini below-the-fold. Riduce il peso iniziale della pagina.', 'olobuilder' ); ?></span>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo $n; ?>[lazy_images]" value="1" <?php checked( $opt['lazy_images'] ); ?> />
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Custom domains card -->
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon orange">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                </div>
                <div>
                    <h3><?php esc_html_e( 'Domini personalizzati', 'olobuilder' ); ?></h3>
                    <p><?php esc_html_e( 'Aggiungi domini per dns-prefetch e preconnect personalizzati (uno per riga, con https://).', 'olobuilder' ); ?></p>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-perf-textarea-row">
                    <label class="olo-perf-textarea-label"><?php esc_html_e( 'DNS Prefetch', 'olobuilder' ); ?></label>
                    <textarea name="<?php echo $n; ?>[dns_prefetch_domains]" rows="3" class="olo-field-input wide"
                              placeholder="//cdn.example.com&#10;//api.example.com"><?php echo esc_textarea( $opt['dns_prefetch_domains'] ); ?></textarea>
                    <span class="olo-field-hint"><?php esc_html_e( 'Risolve il DNS in anticipo. Utile per CDN e servizi esterni.', 'olobuilder' ); ?></span>
                </div>
                <div class="olo-perf-textarea-row">
                    <label class="olo-perf-textarea-label"><?php esc_html_e( 'Preconnect', 'olobuilder' ); ?></label>
                    <textarea name="<?php echo $n; ?>[preconnect_domains]" rows="3" class="olo-field-input wide"
                              placeholder="https://cdn.example.com&#10;https://api.example.com"><?php echo esc_textarea( $opt['preconnect_domains'] ); ?></textarea>
                    <span class="olo-field-hint"><?php esc_html_e( 'Apre connessione completa (DNS + TCP + TLS). Più aggressivo del dns-prefetch, usare solo per risorse critiche.', 'olobuilder' ); ?></span>
                </div>
            </div>
        </div>
        <?php
    }

    /* ─────────────────────────────────────────────
     * Tab: Head Cleanup
     * ───────────────────────────────────────────── */

    private function render_tab_cleanup( $opt ) {
        $n = self::OPT;
        ?>
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="9" x2="15" y2="15"/><line x1="15" y1="9" x2="9" y2="15"/></svg>
                </div>
                <div>
                    <h3><?php esc_html_e( 'Pulizia head', 'olobuilder' ); ?></h3>
                    <p><?php esc_html_e( 'Rimuovi risorse non necessarie dal <head> per ridurre richieste e peso della pagina.', 'olobuilder' ); ?></p>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Rimuovi jQuery Migrate', 'olobuilder' ); ?></label>
                        <span class="olo-field-hint"><?php esc_html_e( 'Rimuove jquery-migrate.js (~10KB). Necessario solo per compatibilità con plugin molto vecchi.', 'olobuilder' ); ?></span>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo $n; ?>[remove_jquery_migrate]" value="1" <?php checked( $opt['remove_jquery_migrate'] ); ?> />
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Rimuovi emoji scripts', 'olobuilder' ); ?></label>
                        <span class="olo-field-hint"><?php esc_html_e( 'Rimuove wp-emoji-release.min.js e relativi stili inline. I browser moderni supportano emoji nativamente.', 'olobuilder' ); ?></span>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo $n; ?>[remove_emoji_scripts]" value="1" <?php checked( $opt['remove_emoji_scripts'] ); ?> />
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Rimuovi Block CSS', 'olobuilder' ); ?></label>
                        <span class="olo-field-hint"><?php esc_html_e( 'Rimuove wp-block-library-css (~30KB). Attiva solo se non usi blocchi Gutenberg nel frontend.', 'olobuilder' ); ?></span>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo $n; ?>[remove_block_css]" value="1" <?php checked( $opt['remove_block_css'] ); ?> />
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Rimuovi Classic Theme CSS', 'olobuilder' ); ?></label>
                        <span class="olo-field-hint"><?php esc_html_e( 'Rimuove classic-theme-styles-css. Inutile se non usi un tema classico.', 'olobuilder' ); ?></span>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo $n; ?>[remove_classic_theme]" value="1" <?php checked( $opt['remove_classic_theme'] ); ?> />
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>
        <?php
    }

    /* ═══════════════════════════════════════════════════
     * AJAX HANDLERS
     * ═══════════════════════════════════════════════════ */

    public function ajax_regenerate_critical() {
        check_ajax_referer( 'olo_perf_action', '_nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Permesso negato' );
        }

        if ( ! class_exists( 'Olo_Critical_CSS' ) ) {
            wp_send_json_error( 'Critical CSS non disponibile' );
        }

        $result = Olo_Critical_CSS::regenerate_all();
        wp_send_json_success( [
            'message'   => sprintf(
                __( 'Rigenerato %d su %d pagine (%d errori)', 'olobuilder' ),
                $result['generated'], $result['total'], $result['failed']
            ),
            'generated' => $result['generated'],
            'total'     => $result['total'],
        ] );
    }

    public function ajax_purge_critical() {
        check_ajax_referer( 'olo_perf_action', '_nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Permesso negato' );
        }

        if ( ! class_exists( 'Olo_Critical_CSS' ) ) {
            wp_send_json_error( 'Critical CSS non disponibile' );
        }

        $purged = Olo_Critical_CSS::purge_all();
        wp_send_json_success( [
            'message' => sprintf( __( 'Svuotati %d transient Critical CSS', 'olobuilder' ), $purged ),
        ] );
    }

    public function ajax_flush_css_cache() {
        check_ajax_referer( 'olo_perf_action', '_nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Permesso negato' );
        }

        if ( class_exists( 'Olo_Asset_Optimizer' ) ) {
            Olo_Asset_Optimizer::flush_all_cache();
        }

        wp_send_json_success( [
            'message' => __( 'Cache CSS svuotata. I file verranno rigenerati al prossimo caricamento.', 'olobuilder' ),
        ] );
    }

    /* ═══════════════════════════════════════════════════
     * HELPERS
     * ═══════════════════════════════════════════════════ */

    private function get_css_cache_info() {
        $upload_dir = wp_upload_dir();
        $cache_dir  = $upload_dir['basedir'] . '/olobuild-cache/';

        $count = 0;
        $size  = 0;

        if ( is_dir( $cache_dir ) ) {
            $files = glob( $cache_dir . 'olo-*.css' );
            if ( $files ) {
                $count = count( $files );
                foreach ( $files as $f ) {
                    $size += filesize( $f );
                }
            }
        }

        return [
            'count'      => $count,
            'size'       => $size,
            'size_human' => size_format( $size, 1 ),
        ];
    }
}
