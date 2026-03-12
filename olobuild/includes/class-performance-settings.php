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
        <div class="wrap olo-perf-wrap">
            <h1>
                <span class="dashicons dashicons-performance" style="margin-right:8px;color:#2271b1"></span>
                <?php echo esc_html__( 'Performance', 'olobuilder' ); ?>
            </h1>

            <nav class="nav-tab-wrapper olo-perf-tabs">
                <?php foreach ( $tabs as $slug => $label ) : ?>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=olo-performance&tab=' . $slug ) ); ?>"
                       class="nav-tab <?php echo $tab === $slug ? 'nav-tab-active' : ''; ?>">
                        <?php echo esc_html( $label ); ?>
                    </a>
                <?php endforeach; ?>
            </nav>

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

                <?php submit_button( __( 'Salva impostazioni', 'olobuilder' ) ); ?>
            </form>
        </div>
        <?php
    }

    /* ─────────────────────────────────────────────
     * Tab: Critical CSS
     * ───────────────────────────────────────────── */

    private function render_tab_critical_css( $opt, $ccss_status ) {
        $n = self::OPT;
        ?>
        <div class="olo-perf-section">
            <h2><?php esc_html_e( 'Critical CSS', 'olobuilder' ); ?></h2>
            <p class="description">
                <?php esc_html_e( 'Genera automaticamente il CSS above-the-fold e lo inietta inline nel <head>. Il foglio di stile principale viene caricato in modo asincrono.', 'olobuilder' ); ?>
            </p>

            <table class="form-table olo-perf-table">
                <tr>
                    <th><?php esc_html_e( 'Abilita Critical CSS', 'olobuilder' ); ?></th>
                    <td>
                        <label class="olo-perf-toggle">
                            <input type="checkbox" name="<?php echo $n; ?>[critical_css_enabled]" value="1" <?php checked( $opt['critical_css_enabled'] ); ?> />
                            <span class="olo-perf-toggle-slider"></span>
                        </label>
                        <p class="description"><?php esc_html_e( 'Migliora FCP e LCP inlinando il CSS critico e deferendo il resto.', 'olobuilder' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Durata cache', 'olobuilder' ); ?></th>
                    <td>
                        <input type="number" name="<?php echo $n; ?>[critical_css_ttl]" value="<?php echo esc_attr( $opt['critical_css_ttl'] ); ?>"
                               min="1" max="30" class="small-text" />
                        <span><?php esc_html_e( 'giorni', 'olobuilder' ); ?></span>
                        <p class="description"><?php esc_html_e( 'Il CSS critico viene rigenerato automaticamente alla scadenza. Invalidato quando salvi un template.', 'olobuilder' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Sezioni above-the-fold', 'olobuilder' ); ?></th>
                    <td>
                        <input type="number" name="<?php echo $n; ?>[critical_css_sections]" value="<?php echo esc_attr( $opt['critical_css_sections'] ); ?>"
                               min="1" max="5" class="small-text" />
                        <p class="description"><?php esc_html_e( 'Quante sezioni del template analizzare per il CSS critico. 2 è il valore consigliato.', 'olobuilder' ); ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Status & Azioni -->
        <div class="olo-perf-section">
            <h2><?php esc_html_e( 'Stato cache', 'olobuilder' ); ?></h2>

            <div class="olo-perf-status-grid">
                <div class="olo-perf-status-card">
                    <span class="olo-perf-status-icon <?php echo $opt['critical_css_enabled'] ? 'active' : 'inactive'; ?>">
                        <span class="dashicons dashicons-<?php echo $opt['critical_css_enabled'] ? 'yes-alt' : 'dismiss'; ?>"></span>
                    </span>
                    <div class="olo-perf-status-info">
                        <strong><?php esc_html_e( 'Stato', 'olobuilder' ); ?></strong>
                        <span><?php echo $opt['critical_css_enabled'] ? esc_html__( 'Attivo', 'olobuilder' ) : esc_html__( 'Disattivato', 'olobuilder' ); ?></span>
                    </div>
                </div>

                <div class="olo-perf-status-card">
                    <span class="olo-perf-status-icon info">
                        <span class="dashicons dashicons-database"></span>
                    </span>
                    <div class="olo-perf-status-info">
                        <strong><?php esc_html_e( 'Pagine in cache', 'olobuilder' ); ?></strong>
                        <span id="olo-ccss-count"><?php echo $ccss_status ? intval( $ccss_status['cached_count'] ) : 0; ?></span>
                    </div>
                </div>

                <div class="olo-perf-status-card">
                    <span class="olo-perf-status-icon info">
                        <span class="dashicons dashicons-clock"></span>
                    </span>
                    <div class="olo-perf-status-info">
                        <strong><?php esc_html_e( 'Ultima generazione', 'olobuilder' ); ?></strong>
                        <span id="olo-ccss-last">
                            <?php
                            if ( $ccss_status && ! empty( $ccss_status['last_generated'] ) ) {
                                echo esc_html( $ccss_status['last_generated'] );
                            } else {
                                esc_html_e( 'Mai', 'olobuilder' );
                            }
                            ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="olo-perf-actions">
                <button type="button" class="button button-primary" id="olo-ccss-regenerate">
                    <span class="dashicons dashicons-update" style="margin-top:4px"></span>
                    <?php esc_html_e( 'Rigenera tutto', 'olobuilder' ); ?>
                </button>
                <button type="button" class="button" id="olo-ccss-purge">
                    <span class="dashicons dashicons-trash" style="margin-top:4px"></span>
                    <?php esc_html_e( 'Svuota cache', 'olobuilder' ); ?>
                </button>
                <span id="olo-ccss-msg" class="olo-perf-msg"></span>
            </div>
        </div>

        <script>
        (function(){
            var nonce = '<?php echo wp_create_nonce( 'olo_perf_action' ); ?>';
            var msg   = document.getElementById('olo-ccss-msg');

            document.getElementById('olo-ccss-regenerate').addEventListener('click', function(){
                this.disabled = true;
                msg.textContent = '<?php echo esc_js( __( 'Rigenerazione in corso...', 'olobuilder' ) ); ?>';
                msg.className = 'olo-perf-msg';
                fetch(ajaxurl + '?action=olo_perf_regenerate_critical&_nonce=' + nonce)
                    .then(function(r){return r.json()})
                    .then(function(d){
                        if(d.success){
                            msg.textContent = d.data.message;
                            msg.className = 'olo-perf-msg success';
                            var el = document.getElementById('olo-ccss-count');
                            if(el){el.textContent = d.data.generated}
                        } else {
                            msg.textContent = d.data || 'Errore';
                            msg.className = 'olo-perf-msg error';
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
                            msg.className = 'olo-perf-msg success';
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
        <div class="olo-perf-section">
            <h2><?php esc_html_e( 'Ottimizzazione Asset', 'olobuilder' ); ?></h2>
            <p class="description">
                <?php esc_html_e( 'Controlla come vengono caricati JavaScript e CSS nel frontend.', 'olobuilder' ); ?>
            </p>

            <table class="form-table olo-perf-table">
                <tr>
                    <th><?php esc_html_e( 'Defer JavaScript', 'olobuilder' ); ?></th>
                    <td>
                        <label class="olo-perf-toggle">
                            <input type="checkbox" name="<?php echo $n; ?>[defer_js]" value="1" <?php checked( $opt['defer_js'] ); ?> />
                            <span class="olo-perf-toggle-slider"></span>
                        </label>
                        <p class="description"><?php esc_html_e( 'Aggiunge l\'attributo defer agli script frontend di Olobuild. Non blocca il rendering della pagina.', 'olobuilder' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Cache CSS su file', 'olobuilder' ); ?></th>
                    <td>
                        <label class="olo-perf-toggle">
                            <input type="checkbox" name="<?php echo $n; ?>[css_cache_files]" value="1" <?php checked( $opt['css_cache_files'] ); ?> />
                            <span class="olo-perf-toggle-slider"></span>
                        </label>
                        <p class="description"><?php esc_html_e( 'Salva il CSS generato dai template come file statici invece di iniettarlo inline. Migliora il caching del browser.', 'olobuilder' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Minifica CSS', 'olobuilder' ); ?></th>
                    <td>
                        <label class="olo-perf-toggle">
                            <input type="checkbox" name="<?php echo $n; ?>[minify_css]" value="1" <?php checked( $opt['minify_css'] ); ?> />
                            <span class="olo-perf-toggle-slider"></span>
                        </label>
                        <p class="description"><?php esc_html_e( 'Rimuove commenti, spazi e newline dal CSS generato.', 'olobuilder' ); ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- CSS Cache status -->
        <div class="olo-perf-section">
            <h2><?php esc_html_e( 'Cache CSS', 'olobuilder' ); ?></h2>

            <div class="olo-perf-status-grid">
                <div class="olo-perf-status-card">
                    <span class="olo-perf-status-icon info">
                        <span class="dashicons dashicons-media-code"></span>
                    </span>
                    <div class="olo-perf-status-info">
                        <strong><?php esc_html_e( 'File in cache', 'olobuilder' ); ?></strong>
                        <span><?php echo intval( $cache_info['count'] ); ?></span>
                    </div>
                </div>
                <div class="olo-perf-status-card">
                    <span class="olo-perf-status-icon info">
                        <span class="dashicons dashicons-chart-bar"></span>
                    </span>
                    <div class="olo-perf-status-info">
                        <strong><?php esc_html_e( 'Dimensione totale', 'olobuilder' ); ?></strong>
                        <span><?php echo esc_html( $cache_info['size_human'] ); ?></span>
                    </div>
                </div>
            </div>

            <div class="olo-perf-actions">
                <button type="button" class="button" id="olo-flush-css-cache">
                    <span class="dashicons dashicons-trash" style="margin-top:4px"></span>
                    <?php esc_html_e( 'Svuota cache CSS', 'olobuilder' ); ?>
                </button>
                <span id="olo-css-cache-msg" class="olo-perf-msg"></span>
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
                            msg.className = 'olo-perf-msg success';
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
        <div class="olo-perf-section">
            <h2><?php esc_html_e( 'Resource Hints', 'olobuilder' ); ?></h2>
            <p class="description">
                <?php esc_html_e( 'Suggerimenti al browser per precaricare risorse e velocizzare il caricamento.', 'olobuilder' ); ?>
            </p>

            <table class="form-table olo-perf-table">
                <tr>
                    <th><?php esc_html_e( 'DNS Prefetch & Preconnect', 'olobuilder' ); ?></th>
                    <td>
                        <label class="olo-perf-toggle">
                            <input type="checkbox" name="<?php echo $n; ?>[resource_hints]" value="1" <?php checked( $opt['resource_hints'] ); ?> />
                            <span class="olo-perf-toggle-slider"></span>
                        </label>
                        <p class="description"><?php esc_html_e( 'Aggiunge dns-prefetch e preconnect per Google Fonts, YouTube, Vimeo e altri domini esterni.', 'olobuilder' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Preload font', 'olobuilder' ); ?></th>
                    <td>
                        <label class="olo-perf-toggle">
                            <input type="checkbox" name="<?php echo $n; ?>[font_preload]" value="1" <?php checked( $opt['font_preload'] ); ?> />
                            <span class="olo-perf-toggle-slider"></span>
                        </label>
                        <p class="description"><?php esc_html_e( 'Precarica i font custom usati come body/heading per evitare FOUT (Flash of Unstyled Text).', 'olobuilder' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Video facade', 'olobuilder' ); ?></th>
                    <td>
                        <label class="olo-perf-toggle">
                            <input type="checkbox" name="<?php echo $n; ?>[video_facade]" value="1" <?php checked( $opt['video_facade'] ); ?> />
                            <span class="olo-perf-toggle-slider"></span>
                        </label>
                        <p class="description"><?php esc_html_e( 'Mostra un\'anteprima statica dei video YouTube/Vimeo. L\'iframe si carica solo al click. Risparmia ~500KB per video.', 'olobuilder' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'fetchpriority hero image', 'olobuilder' ); ?></th>
                    <td>
                        <label class="olo-perf-toggle">
                            <input type="checkbox" name="<?php echo $n; ?>[fetchpriority]" value="1" <?php checked( $opt['fetchpriority'] ); ?> />
                            <span class="olo-perf-toggle-slider"></span>
                        </label>
                        <p class="description"><?php esc_html_e( 'Aggiunge fetchpriority="high" alla prima immagine della pagina e rimuove lazy loading dagli elementi above-fold.', 'olobuilder' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Lazy loading immagini', 'olobuilder' ); ?></th>
                    <td>
                        <label class="olo-perf-toggle">
                            <input type="checkbox" name="<?php echo $n; ?>[lazy_images]" value="1" <?php checked( $opt['lazy_images'] ); ?> />
                            <span class="olo-perf-toggle-slider"></span>
                        </label>
                        <p class="description"><?php esc_html_e( 'Aggiunge loading="lazy" alle immagini below-the-fold. Riduce il peso iniziale della pagina.', 'olobuilder' ); ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="olo-perf-section">
            <h2><?php esc_html_e( 'Domini personalizzati', 'olobuilder' ); ?></h2>
            <p class="description">
                <?php esc_html_e( 'Aggiungi domini per dns-prefetch e preconnect personalizzati (uno per riga, con https://).', 'olobuilder' ); ?>
            </p>

            <table class="form-table olo-perf-table">
                <tr>
                    <th><?php esc_html_e( 'DNS Prefetch', 'olobuilder' ); ?></th>
                    <td>
                        <textarea name="<?php echo $n; ?>[dns_prefetch_domains]" rows="3" class="large-text code"
                                  placeholder="//cdn.example.com&#10;//api.example.com"><?php echo esc_textarea( $opt['dns_prefetch_domains'] ); ?></textarea>
                        <p class="description"><?php esc_html_e( 'Risolve il DNS in anticipo. Utile per CDN e servizi esterni.', 'olobuilder' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Preconnect', 'olobuilder' ); ?></th>
                    <td>
                        <textarea name="<?php echo $n; ?>[preconnect_domains]" rows="3" class="large-text code"
                                  placeholder="https://cdn.example.com&#10;https://api.example.com"><?php echo esc_textarea( $opt['preconnect_domains'] ); ?></textarea>
                        <p class="description"><?php esc_html_e( 'Apre connessione completa (DNS + TCP + TLS). Più aggressivo del dns-prefetch, usare solo per risorse critiche.', 'olobuilder' ); ?></p>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }

    /* ─────────────────────────────────────────────
     * Tab: Head Cleanup
     * ───────────────────────────────────────────── */

    private function render_tab_cleanup( $opt ) {
        $n = self::OPT;
        ?>
        <div class="olo-perf-section">
            <h2><?php esc_html_e( 'Pulizia head', 'olobuilder' ); ?></h2>
            <p class="description">
                <?php esc_html_e( 'Rimuovi risorse non necessarie dal <head> per ridurre richieste e peso della pagina.', 'olobuilder' ); ?>
            </p>

            <table class="form-table olo-perf-table">
                <tr>
                    <th><?php esc_html_e( 'Rimuovi jQuery Migrate', 'olobuilder' ); ?></th>
                    <td>
                        <label class="olo-perf-toggle">
                            <input type="checkbox" name="<?php echo $n; ?>[remove_jquery_migrate]" value="1" <?php checked( $opt['remove_jquery_migrate'] ); ?> />
                            <span class="olo-perf-toggle-slider"></span>
                        </label>
                        <p class="description"><?php esc_html_e( 'Rimuove jquery-migrate.js (~10KB). Necessario solo per compatibilità con plugin molto vecchi.', 'olobuilder' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Rimuovi emoji scripts', 'olobuilder' ); ?></th>
                    <td>
                        <label class="olo-perf-toggle">
                            <input type="checkbox" name="<?php echo $n; ?>[remove_emoji_scripts]" value="1" <?php checked( $opt['remove_emoji_scripts'] ); ?> />
                            <span class="olo-perf-toggle-slider"></span>
                        </label>
                        <p class="description"><?php esc_html_e( 'Rimuove wp-emoji-release.min.js e relativi stili inline. I browser moderni supportano emoji nativamente.', 'olobuilder' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Rimuovi Block CSS', 'olobuilder' ); ?></th>
                    <td>
                        <label class="olo-perf-toggle">
                            <input type="checkbox" name="<?php echo $n; ?>[remove_block_css]" value="1" <?php checked( $opt['remove_block_css'] ); ?> />
                            <span class="olo-perf-toggle-slider"></span>
                        </label>
                        <p class="description"><?php esc_html_e( 'Rimuove wp-block-library-css (~30KB). Attiva solo se non usi blocchi Gutenberg nel frontend.', 'olobuilder' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Rimuovi Classic Theme CSS', 'olobuilder' ); ?></th>
                    <td>
                        <label class="olo-perf-toggle">
                            <input type="checkbox" name="<?php echo $n; ?>[remove_classic_theme]" value="1" <?php checked( $opt['remove_classic_theme'] ); ?> />
                            <span class="olo-perf-toggle-slider"></span>
                        </label>
                        <p class="description"><?php esc_html_e( 'Rimuove classic-theme-styles-css. Inutile se non usi un tema classico.', 'olobuilder' ); ?></p>
                    </td>
                </tr>
            </table>
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
