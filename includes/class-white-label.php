<?php
/**
 * Olo_White_Label — Rename plugin name/icon, hide credits,
 * custom welcome screen, hide menu for non-admins.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_White_Label {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init() {
        // REST API + admin page: always registered (even if white label is disabled)
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
        add_action( 'admin_menu', [ $this, 'add_admin_page' ] );

        $settings = $this->get_settings();
        if ( empty( $settings['enabled'] ) ) {
            return;
        }

        // Rename plugin in admin
        add_filter( 'all_plugins', [ $this, 'rename_plugin' ] );

        // Change admin menu
        add_action( 'admin_menu', [ $this, 'modify_admin_menu' ], 999 );

        // Hide for non-admins
        if ( ! empty( $settings['hide_for_non_admins'] ) ) {
            add_action( 'admin_menu', [ $this, 'hide_menu_for_non_admins' ], 1000 );
        }

        // Filter builder brand name
        add_filter( 'olo_brand_name', [ $this, 'get_brand_name' ] );

        // Filter builder/wizard brand logo
        add_filter( 'olo_brand_logo_url', [ $this, 'get_logo_url' ] );
    }

    /* ─────────────────────────────────────────────
     * Filters
     * ───────────────────────────────────────────── */

    public function rename_plugin( $plugins ) {
        $settings = $this->get_settings();
        $key = 'olobuild/olobuild.php';

        if ( isset( $plugins[ $key ] ) && ! empty( $settings['plugin_name'] ) ) {
            $plugins[ $key ]['Name']   = $settings['plugin_name'];
            $plugins[ $key ]['Title']  = $settings['plugin_name'];

            if ( ! empty( $settings['plugin_description'] ) ) {
                $plugins[ $key ]['Description'] = $settings['plugin_description'];
            }
            if ( ! empty( $settings['author_name'] ) ) {
                $plugins[ $key ]['Author']     = $settings['author_name'];
                $plugins[ $key ]['AuthorName'] = $settings['author_name'];
            }
            if ( ! empty( $settings['author_url'] ) ) {
                $plugins[ $key ]['AuthorURI'] = $settings['author_url'];
            }
        }

        return $plugins;
    }

    public function modify_admin_menu() {
        global $menu, $submenu;
        $settings = $this->get_settings();

        if ( empty( $settings['plugin_name'] ) ) {
            return;
        }

        // Rename main menu item
        foreach ( $menu as &$item ) {
            if ( isset( $item[2] ) && $item[2] === 'olobuild' ) {
                $item[0] = esc_html( $settings['plugin_name'] );
                break;
            }
        }
    }

    public function hide_menu_for_non_admins() {
        // Non-admins: hide from WP menu via CSS (don't remove — breaks page registration)
        if ( ! current_user_can( 'manage_options' ) ) {
            add_action( 'admin_head', function () {
                echo '<style>#adminmenu a[href*="olo-white-label"] { display: none !important; }</style>';
            } );
        }
    }

    public function get_brand_name( $default ) {
        $settings = $this->get_settings();
        return ! empty( $settings['plugin_name'] ) ? $settings['plugin_name'] : $default;
    }

    public function get_logo_url( $default ) {
        $settings = $this->get_settings();
        return ! empty( $settings['plugin_logo_url'] ) ? $settings['plugin_logo_url'] : $default;
    }

    /* ─────────────────────────────────────────────
     * Settings
     * ───────────────────────────────────────────── */

    public function get_settings() {
        $defaults = [
            'enabled'            => false,
            'plugin_name'        => '',
            'plugin_description' => '',
            'plugin_logo_url'    => '',
            'author_name'        => '',
            'author_url'         => '',
            'hide_for_non_admins' => false,
            'hide_credits'       => false,
        ];
        // Merge coi default: opzioni salvate prima di nuove chiavi (es. plugin_logo_url)
        // non devono generare undefined-key notice.
        $saved = get_option( 'olo_white_label', [] );
        return wp_parse_args( is_array( $saved ) ? $saved : [], $defaults );
    }

    /* ─────────────────────────────────────────────
     * REST API
     * ───────────────────────────────────────────── */

    public function register_routes() {
        register_rest_route( 'olo/v1', '/white-label', [
            [
                'methods'             => 'GET',
                'callback'            => function() {
                    return rest_ensure_response( $this->get_settings() );
                },
                'permission_callback' => function() {
                    return current_user_can( 'manage_options' );
                },
            ],
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'save_settings' ],
                'permission_callback' => function() {
                    return current_user_can( 'manage_options' );
                },
            ],
        ] );
    }

    public function save_settings( $request ) {
        $data = $request->get_json_params();
        if ( ! is_array( $data ) ) {
            return new WP_Error( 'invalid', 'Dati non validi', [ 'status' => 400 ] );
        }

        $clean = [
            'enabled'             => ! empty( $data['enabled'] ),
            'plugin_name'         => sanitize_text_field( $data['plugin_name'] ?? '' ),
            'plugin_description'  => sanitize_text_field( $data['plugin_description'] ?? '' ),
            'plugin_logo_url'     => esc_url_raw( $data['plugin_logo_url'] ?? '' ),
            'author_name'         => sanitize_text_field( $data['author_name'] ?? '' ),
            'author_url'          => esc_url_raw( $data['author_url'] ?? '' ),
            'hide_for_non_admins' => ! empty( $data['hide_for_non_admins'] ),
            'hide_credits'        => ! empty( $data['hide_credits'] ),
        ];

        update_option( 'olo_white_label', $clean );
        return rest_ensure_response( [ 'success' => true ] );
    }

    /* ─────────────────────────────────────────────
     * Admin Page
     * ───────────────────────────────────────────── */

    public function add_admin_page() {
        // v1.0.30 — pagina migrata in ?page=olobuilder-settings&tab=whitelabel
        // Submenu rimosso: i campi vivono ora in Configurazione → White Label.
        // La classe resta attiva per i filter su plugin name/author/description nel listing WP.
    }

    public function render_admin_page() {
        $s = $this->get_settings();
        $custom_brand = ! empty( $s['plugin_name'] ) || ! empty( $s['plugin_logo_url'] );
        ?>
        <?php Olo_Builder::cockpit_shell_open( '<b>' . esc_html__( 'White Label', 'olobuild' ) . '</b>' ); ?>
        <main class="olo-cockpit-main olo-cockpit-legacy">
            <?php
            // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML built by Olo_Builder::cockpit_page_head(), which escapes via esc_html()/wp_kses_post() internally.
            echo Olo_Builder::cockpit_page_head( [
                'title' => __( 'White Label', 'olobuild' ),
                'sub'   => $custom_brand
                    ? __( 'Branding personalizzato attivo. Nasconde il marchio Olobuild dai tuoi clienti.', 'olobuild' )
                    : __( 'Personalizza nome plugin, logo, autore e link per nascondere il brand Olobuild.', 'olobuild' ),
            ] );
            // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
            ?>

            <div id="wl-msg-box" style="margin-top:16px"></div>

            <div class="olo-card">
                <div class="olo-card-head">
                    <div class="olo-card-icon black">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                    </div>
                    <div>
                        <h3><?php esc_html_e( 'Impostazioni White Label', 'olobuild' ); ?></h3>
                        <p><?php esc_html_e( 'Rinomina plugin, nascondi credits e impostazioni per i non-admin', 'olobuild' ); ?></p>
                    </div>
                </div>
                <div class="olo-card-body">
                    <div class="olo-field-row">
                        <div class="olo-field-info">
                            <label><?php esc_html_e( 'Attiva White Label', 'olobuild' ); ?></label>
                            <div class="olo-field-hint"><?php esc_html_e( 'Abilita la personalizzazione del brand', 'olobuild' ); ?></div>
                        </div>
                        <label class="olo-toggle">
                            <input type="checkbox" id="wl_enabled" <?php checked( $s['enabled'] ); ?> />
                            <span class="olo-toggle-slider"></span>
                        </label>
                    </div>
                    <div class="olo-field-row">
                        <div class="olo-field-info">
                            <label><?php esc_html_e( 'Nome plugin', 'olobuild' ); ?></label>
                        </div>
                        <div class="olo-field-input-wrap">
                            <input type="text" id="wl_name" value="<?php echo esc_attr( $s['plugin_name'] ); ?>" class="olo-field-input" placeholder="Olobuild" />
                        </div>
                    </div>
                    <div class="olo-field-row">
                        <div class="olo-field-info">
                            <label><?php esc_html_e( 'Descrizione', 'olobuild' ); ?></label>
                        </div>
                        <div class="olo-field-input-wrap">
                            <input type="text" id="wl_desc" value="<?php echo esc_attr( $s['plugin_description'] ); ?>" class="olo-field-input" placeholder="<?php echo esc_attr__( 'Page builder professionale', 'olobuild' ); ?>" />
                        </div>
                    </div>
                    <div class="olo-field-row">
                        <div class="olo-field-info">
                            <label><?php esc_html_e( 'URL logo', 'olobuild' ); ?></label>
                            <div class="olo-field-hint"><?php esc_html_e( 'Logo personalizzato per la barra del builder e il wizard (lascia vuoto per usare il default).', 'olobuild' ); ?></div>
                        </div>
                        <div class="olo-field-input-wrap">
                            <input type="url" id="wl_logo" value="<?php echo esc_attr( $s['plugin_logo_url'] ); ?>" class="olo-field-input" placeholder="https://…/logo.png" />
                        </div>
                    </div>
                    <div class="olo-field-row">
                        <div class="olo-field-info">
                            <label><?php esc_html_e( 'Nome autore', 'olobuild' ); ?></label>
                        </div>
                        <div class="olo-field-input-wrap">
                            <input type="text" id="wl_author" value="<?php echo esc_attr( $s['author_name'] ); ?>" class="olo-field-input" />
                        </div>
                    </div>
                    <div class="olo-field-row">
                        <div class="olo-field-info">
                            <label><?php esc_html_e( 'URL autore', 'olobuild' ); ?></label>
                        </div>
                        <div class="olo-field-input-wrap">
                            <input type="url" id="wl_url" value="<?php echo esc_attr( $s['author_url'] ); ?>" class="olo-field-input" />
                        </div>
                    </div>
                    <div class="olo-field-row">
                        <div class="olo-field-info">
                            <label><?php esc_html_e( 'Nascondi per non-admin', 'olobuild' ); ?></label>
                            <div class="olo-field-hint"><?php esc_html_e( 'Nasconde le impostazioni White Label per utenti non amministratori', 'olobuild' ); ?></div>
                        </div>
                        <label class="olo-toggle">
                            <input type="checkbox" id="wl_hide" <?php checked( $s['hide_for_non_admins'] ); ?> />
                            <span class="olo-toggle-slider"></span>
                        </label>
                    </div>
                    <div class="olo-field-row">
                        <div class="olo-field-info">
                            <label><?php esc_html_e( 'Nascondi credits', 'olobuild' ); ?></label>
                            <div class="olo-field-hint"><?php esc_html_e( 'Rimuove la scritta "Powered by Olobuild"', 'olobuild' ); ?></div>
                        </div>
                        <label class="olo-toggle">
                            <input type="checkbox" id="wl_credits" <?php checked( $s['hide_credits'] ); ?> />
                            <span class="olo-toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="olo-actions">
                <button class="olo-btn-save" id="wl-save">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    <?php esc_html_e( 'Salva impostazioni', 'olobuild' ); ?>
                </button>
            </div>

            <script>
            var wlI18n = {
                saving:       <?php echo wp_json_encode( __( 'Salvataggio...', 'olobuild' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline JSON in <script> built by wp_json_encode(). ?>,
                saved:        <?php echo wp_json_encode( __( 'Impostazioni salvate con successo', 'olobuild' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline JSON in <script> built by wp_json_encode(). ?>,
                saveError:    <?php echo wp_json_encode( __( 'Errore nel salvataggio', 'olobuild' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline JSON in <script> built by wp_json_encode(). ?>,
                saveSettings: <?php echo wp_json_encode( __( 'Salva impostazioni', 'olobuild' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline JSON in <script> built by wp_json_encode(). ?>
            };
            document.getElementById('wl-save').addEventListener('click', function() {
                var btn = this;
                btn.disabled = true;
                btn.innerHTML = '<span class="olo-spinner"></span> ' + wlI18n.saving;
                var data = {
                    enabled: document.getElementById('wl_enabled').checked,
                    plugin_name: document.getElementById('wl_name').value,
                    plugin_description: document.getElementById('wl_desc').value,
                    plugin_logo_url: document.getElementById('wl_logo').value,
                    author_name: document.getElementById('wl_author').value,
                    author_url: document.getElementById('wl_url').value,
                    hide_for_non_admins: document.getElementById('wl_hide').checked,
                    hide_credits: document.getElementById('wl_credits').checked
                };
                fetch('<?php echo esc_js( rest_url( 'olo/v1/white-label' ) ); ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': '<?php echo esc_js( wp_create_nonce( 'wp_rest' ) ); ?>' },
                    body: JSON.stringify(data)
                })
                .then(function(r) { return r.json(); })
                .then(function(d) {
                    var box = document.getElementById('wl-msg-box');
                    if (d.success) {
                        box.className = 'olo-msg success';
                        box.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg> ' + wlI18n.saved;
                    } else {
                        box.className = 'olo-msg error';
                        box.textContent = wlI18n.saveError;
                    }
                    setTimeout(function() { box.className = ''; box.innerHTML = ''; }, 3000);
                })
                .finally(function() {
                    btn.disabled = false;
                    btn.innerHTML = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> ' + wlI18n.saveSettings;
                });
            });
            </script>
        </main>
        <?php Olo_Builder::cockpit_shell_close(); ?>
        <?php
    }
}
