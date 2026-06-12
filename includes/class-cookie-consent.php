<?php
/**
 * Olo_Cookie_Consent — Sistema completo GDPR/ePrivacy cookie consent.
 *
 * Features:
 * - Banner + modal preferenze con categorie (necessary, analytics, marketing, preferences)
 * - Script blocking per categoria (type="text/plain" data-cookiecategory)
 * - Google Consent Mode v2
 * - Log consensi per audit GDPR
 * - Pagina admin con configurazione completa
 * - Shortcode [olo_cookie_settings] per riaprire il pannello preferenze
 * - Auto-blocco script noti (GA, GTM, Facebook Pixel, Hotjar, ecc.)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Cookie_Consent {

    private static $instance = null;

    const OPT = 'olo_cookie_settings';

    /** Default cookie categories */
    const CATEGORIES = [ 'necessary', 'analytics', 'marketing', 'preferences' ];

    /** Consent log table (without prefix) */
    const LOG_TABLE = 'olo_consent_log';

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public function init() {
        // Admin
        add_action( 'admin_menu', [ $this, 'add_menu' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );

        // AJAX
        add_action( 'wp_ajax_olo_cookie_log_consent', [ $this, 'ajax_log_consent' ] );
        add_action( 'wp_ajax_nopriv_olo_cookie_log_consent', [ $this, 'ajax_log_consent' ] );
        add_action( 'wp_ajax_olo_cookie_clear_log', [ $this, 'ajax_clear_log' ] );
        add_action( 'wp_ajax_olo_cookie_export_log', [ $this, 'ajax_export_log' ] );

        // Frontend
        $opts = self::get_options();
        if ( ! empty( $opts['enabled'] ) ) {
            add_action( 'wp_footer', [ $this, 'render_frontend' ], 5 );
            add_action( 'wp_head', [ $this, 'output_gcm_default' ], 1 );

            // Script blocking
            if ( ! empty( $opts['auto_block'] ) ) {
                add_filter( 'script_loader_tag', [ $this, 'auto_block_scripts' ], 999, 2 );
            }
        }

        // Shortcode
        add_shortcode( 'olo_cookie_settings', [ $this, 'shortcode_open_preferences' ] );

        // Create table
        add_action( 'admin_init', [ $this, 'maybe_create_table' ] );
    }

    /* ═══════════════════════════════════════════════════
     * OPTIONS
     * ═══════════════════════════════════════════════════ */

    public static function get_options() {
        $defaults = [
            'enabled'             => false,
            'layout'              => 'bar',       // bar | box | fullwidth
            'position'            => 'bottom',    // bottom | top
            'show_reject_all'     => true,
            'show_preferences'    => true,
            'auto_block'          => true,
            'gcm_enabled'         => false,        // Google Consent Mode v2
            'consent_duration'    => 365,
            'reshow_days'         => 0,            // 0 = never re-ask
            'banner_version'      => 1,              // Increment when policy changes
            'block_iframes'       => true,           // Block YouTube/Maps/etc. iframes
            // Texts
            'banner_title'        => 'Cookie',
            'banner_message'      => 'Utilizziamo cookie per migliorare la tua esperienza di navigazione, mostrare contenuti personalizzati e analizzare il traffico del sito.',
            'accept_all_text'     => 'Accetta tutti',
            'reject_all_text'     => 'Rifiuta tutti',
            'preferences_text'    => 'Personalizza',
            'save_text'           => 'Salva preferenze',
            'privacy_url'         => '',
            'privacy_text'        => 'Informativa Privacy',
            'cookie_policy_url'   => '',
            'cookie_policy_text'  => 'Cookie Policy',
            // Category descriptions
            'cat_necessary_desc'  => 'Cookie essenziali per il funzionamento del sito. Non possono essere disattivati.',
            'cat_analytics_desc'  => 'Cookie utilizzati per raccogliere statistiche anonime sull\'uso del sito.',
            'cat_marketing_desc'  => 'Cookie utilizzati per mostrare pubblicità pertinente ai tuoi interessi.',
            'cat_preferences_desc'=> 'Cookie che memorizzano le tue preferenze di navigazione (lingua, tema, ecc.).',
            // Category labels
            'cat_necessary_label' => 'Necessari',
            'cat_analytics_label' => 'Analitici',
            'cat_marketing_label' => 'Marketing',
            'cat_preferences_label'=> 'Preferenze',
            // Appearance
            'bg_color'            => '#ffffff',
            'text_color'          => '#1f2937',
            'btn_primary_bg'      => '#2563eb',
            'btn_primary_text'    => '#ffffff',
            'btn_secondary_bg'    => '#e5e7eb',
            'btn_secondary_text'  => '#374151',
            'overlay'             => true,
            'border_radius'       => 12,
            // Auto-block patterns
            'block_patterns'      => "google-analytics.com\ngoogletagmanager.com\nfacebook.net/en_US/fbevents.js\nhotjar.com\nclarity.ms\nlinkedin.com/insight\npinterest.com/ct.js\ntiktok.com/i18n/pixel\nsnapchat.com/scevent.min.js",
            // Cookie declaration
            'cookie_table'        => [],
        ];

        $saved = get_option( self::OPT, [] );
        if ( ! is_array( $saved ) ) {
            $saved = [];
        }

        return wp_parse_args( $saved, $defaults );
    }

    /* ═══════════════════════════════════════════════════
     * ADMIN MENU & PAGE
     * ═══════════════════════════════════════════════════ */

    public function add_menu() {
        // v1.0.30 — pagina migrata in ?page=olobuilder-settings&tab=cookie
        // Submenu rimosso: i campi vivono ora in Configurazione → Cookie Consent & GDPR.
        // La classe resta attiva per il banner frontend e il blocco script pre-consenso.
    }

    public function register_settings() {
        register_setting( 'olo_cookie_group', self::OPT, [
            'sanitize_callback' => [ $this, 'sanitize_options' ],
        ] );
    }

    public function enqueue_admin_assets( $hook ) {
        if ( ! str_contains( $hook, 'olo-cookie-consent' ) ) {
            return;
        }
        wp_enqueue_style( 'olo-cookie-admin', OLO_URL . 'assets/css/cookie-admin.css', [], OLO_VERSION );
    }

    public function sanitize_options( $input ) {
        $clean = [];

        // Booleans
        $bools = [ 'enabled', 'show_reject_all', 'show_preferences', 'auto_block', 'gcm_enabled', 'overlay', 'block_iframes' ];
        foreach ( $bools as $k ) {
            $clean[ $k ] = ! empty( $input[ $k ] );
        }

        // Strings
        $strings = [
            'layout', 'position', 'banner_title', 'banner_message',
            'accept_all_text', 'reject_all_text', 'preferences_text', 'save_text',
            'privacy_text', 'cookie_policy_text',
            'cat_necessary_desc', 'cat_analytics_desc', 'cat_marketing_desc', 'cat_preferences_desc',
            'cat_necessary_label', 'cat_analytics_label', 'cat_marketing_label', 'cat_preferences_label',
            'bg_color', 'text_color', 'btn_primary_bg', 'btn_primary_text',
            'btn_secondary_bg', 'btn_secondary_text',
        ];
        foreach ( $strings as $k ) {
            $clean[ $k ] = sanitize_text_field( $input[ $k ] ?? '' );
        }

        // URLs
        $clean['privacy_url']       = esc_url_raw( $input['privacy_url'] ?? '' );
        $clean['cookie_policy_url'] = esc_url_raw( $input['cookie_policy_url'] ?? '' );

        // Integers
        $clean['consent_duration'] = max( 1, min( 730, intval( $input['consent_duration'] ?? 365 ) ) );
        $clean['reshow_days']      = max( 0, min( 365, intval( $input['reshow_days'] ?? 0 ) ) );
        $clean['border_radius']    = max( 0, min( 30, intval( $input['border_radius'] ?? 12 ) ) );
        $clean['banner_version']   = max( 1, intval( $input['banner_version'] ?? 1 ) );

        // Layout validation
        if ( ! in_array( $clean['layout'], [ 'bar', 'box', 'fullwidth' ], true ) ) {
            $clean['layout'] = 'bar';
        }
        if ( ! in_array( $clean['position'], [ 'top', 'bottom' ], true ) ) {
            $clean['position'] = 'bottom';
        }

        // Block patterns
        $clean['block_patterns'] = sanitize_textarea_field( $input['block_patterns'] ?? '' );

        // Cookie table (array of arrays)
        $clean['cookie_table'] = [];
        if ( ! empty( $input['cookie_table'] ) ) {
            if ( is_array( $input['cookie_table'] ) ) {
                foreach ( $input['cookie_table'] as $row ) {
                    if ( ! empty( $row['name'] ) ) {
                        $clean['cookie_table'][] = [
                            'name'     => sanitize_text_field( $row['name'] ),
                            'provider' => sanitize_text_field( $row['provider'] ?? '' ),
                            'purpose'  => sanitize_text_field( $row['purpose'] ?? '' ),
                            'expiry'   => sanitize_text_field( $row['expiry'] ?? '' ),
                            'category' => in_array( $row['category'] ?? '', self::CATEGORIES, true ) ? $row['category'] : 'necessary',
                        ];
                    }
                }
            }
        }

        // Sync legacy option
        update_option( 'olo_cookie_consent_enabled', $clean['enabled'] ? '1' : '' );

        return $clean;
    }

    /* ─────────────────────────────────────────────
     * Admin Page Render
     * ───────────────────────────────────────────── */

    public function render_admin_page() {
        $opts = self::get_options();
        $tab  = sanitize_key( $_GET['tab'] ?? 'general' );
        $tabs = [
            'general'     => __( 'Generale', 'olobuild' ),
            'texts'       => __( 'Testi', 'olobuild' ),
            'appearance'  => __( 'Aspetto', 'olobuild' ),
            'categories'  => __( 'Categorie', 'olobuild' ),
            'blocking'    => __( 'Blocco Script', 'olobuild' ),
            'declaration' => __( 'Dichiarazione Cookie', 'olobuild' ),
            'consent_log' => __( 'Log Consensi', 'olobuild' ),
        ];
        $subnav = [];
        foreach ( $tabs as $slug => $label ) {
            $subnav[] = [ 'slug' => $slug, 'label' => $label, 'href' => admin_url( 'admin.php?page=olo-cookie-consent&tab=' . $slug ) ];
        }
        $banner_active = ! empty( $opts['enabled'] );
        ?>
        <?php Olo_Builder::cockpit_shell_open( '<b>' . esc_html__( 'Cookie Consent', 'olobuild' ) . '</b>' ); ?>
        <main class="olo-cockpit-main olo-cockpit-legacy olo-ck-page">
            <?php
            // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML built by Olo_Builder::cockpit_page_head(), which escapes via esc_html()/wp_kses_post() internally.
            echo Olo_Builder::cockpit_page_head( [
                'title' => __( 'Cookie Consent', 'olobuild' ),
                'sub'   => $banner_active
                    ? __( 'Banner attivo · GDPR-compliant · log consensi tracciato.', 'olobuild' )
                    : __( 'Banner disattivato. Configura testi, aspetto e categorie cookie per attivarlo.', 'olobuild' ),
            ] );
            // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
            echo Olo_Builder::cockpit_subnav( $subnav, $tab ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML built by Olo_Builder::cockpit_subnav(), which escapes via esc_url()/esc_html() internally.
            ?>

            <?php if ( $tab !== 'consent_log' ) : ?>
            <form method="post" action="options.php" class="olo-ck-form" style="margin-top:16px">
                <?php settings_fields( 'olo_cookie_group' ); ?>
                <?php $this->render_hidden_fields( $opts, $tab ); ?>

                <?php
                switch ( $tab ) {
                    case 'general':     $this->render_tab_general( $opts ); break;
                    case 'texts':       $this->render_tab_texts( $opts ); break;
                    case 'appearance':  $this->render_tab_appearance( $opts ); break;
                    case 'categories':  $this->render_tab_categories( $opts ); break;
                    case 'blocking':    $this->render_tab_blocking( $opts ); break;
                    case 'declaration': $this->render_tab_declaration( $opts ); break;
                }
                ?>

                <div class="olo-actions" style="margin-top:24px">
                    <?php
                    // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML built by Olo_Builder::cockpit_button(), which escapes all parts internally.
                    echo Olo_Builder::cockpit_button( [
                        'label'   => __( 'Salva impostazioni', 'olobuild' ),
                        'variant' => 'pri',
                        'type'    => 'submit',
                        'icon'    => '<path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>',
                    ] );
                    // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
                    ?>
                </div>
            </form>
            <?php else : ?>
                <div style="margin-top:16px"><?php $this->render_tab_consent_log(); ?></div>
            <?php endif; ?>
        </main>
        <?php Olo_Builder::cockpit_shell_close(); ?>
        <?php
    }

    /**
     * Render hidden fields to preserve settings from other tabs.
     */
    private function render_hidden_fields( $opts, $current_tab ) {
        $n = self::OPT;

        // All fields grouped by tab
        $tab_fields = [
            'general' => [ 'enabled', 'layout', 'position', 'show_reject_all', 'show_preferences', 'consent_duration', 'reshow_days', 'gcm_enabled', 'banner_version', 'block_iframes' ],
            'texts' => [ 'banner_title', 'banner_message', 'accept_all_text', 'reject_all_text', 'preferences_text', 'save_text', 'privacy_url', 'privacy_text', 'cookie_policy_url', 'cookie_policy_text' ],
            'appearance' => [ 'bg_color', 'text_color', 'btn_primary_bg', 'btn_primary_text', 'btn_secondary_bg', 'btn_secondary_text', 'overlay', 'border_radius' ],
            'categories' => [ 'cat_necessary_label', 'cat_analytics_label', 'cat_marketing_label', 'cat_preferences_label', 'cat_necessary_desc', 'cat_analytics_desc', 'cat_marketing_desc', 'cat_preferences_desc' ],
            'blocking' => [ 'auto_block', 'block_patterns' ],
            'declaration' => [],
        ];

        // For each tab that is NOT the current one, output hidden fields
        foreach ( $tab_fields as $tab_name => $fields ) {
            if ( $tab_name === $current_tab ) {
                continue;
            }
            foreach ( $fields as $field ) {
                $val = $opts[ $field ] ?? '';
                if ( is_bool( $val ) ) {
                    if ( $val ) {
                        echo '<input type="hidden" name="' . esc_attr( $n ) . '[' . esc_attr( $field ) . ']" value="1" />';
                    }
                } else {
                    echo '<input type="hidden" name="' . esc_attr( $n ) . '[' . esc_attr( $field ) . ']" value="' . esc_attr( $val ) . '" />';
                }
            }
        }

        // Cookie table (always preserve if not on declaration tab)
        if ( $current_tab !== 'declaration' ) {
            if ( ! empty( $opts['cookie_table'] ) ) {
                foreach ( $opts['cookie_table'] as $i => $row ) {
                    foreach ( $row as $k => $v ) {
                        echo '<input type="hidden" name="' . esc_attr( $n ) . '[cookie_table][' . (int) $i . '][' . esc_attr( $k ) . ']" value="' . esc_attr( $v ) . '" />';
                    }
                }
            }
        }
    }

    /* ─── Tab: General ─── */

    private function render_tab_general( $opts ) {
        $n = self::OPT;
        ?>
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                </div>
                <div>
                    <h3><?php esc_html_e( 'Impostazioni generali', 'olobuild' ); ?></h3>
                    <p><?php esc_html_e( 'Configura il comportamento del cookie banner', 'olobuild' ); ?></p>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Abilita Cookie Banner', 'olobuild' ); ?></label>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo esc_attr( $n ); ?>[enabled]" value="1" <?php checked( $opts['enabled'] ); ?> />
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Layout', 'olobuild' ); ?></label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <select name="<?php echo esc_attr( $n ); ?>[layout]" class="olo-field-input">
                            <option value="bar" <?php selected( $opts['layout'], 'bar' ); ?>><?php esc_html_e( 'Barra', 'olobuild' ); ?></option>
                            <option value="box" <?php selected( $opts['layout'], 'box' ); ?>><?php esc_html_e( 'Box laterale', 'olobuild' ); ?></option>
                            <option value="fullwidth" <?php selected( $opts['layout'], 'fullwidth' ); ?>><?php esc_html_e( 'Full-width', 'olobuild' ); ?></option>
                        </select>
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Posizione', 'olobuild' ); ?></label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <select name="<?php echo esc_attr( $n ); ?>[position]" class="olo-field-input">
                            <option value="bottom" <?php selected( $opts['position'], 'bottom' ); ?>><?php esc_html_e( 'In basso', 'olobuild' ); ?></option>
                            <option value="top" <?php selected( $opts['position'], 'top' ); ?>><?php esc_html_e( 'In alto', 'olobuild' ); ?></option>
                        </select>
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Mostra "Rifiuta tutti"', 'olobuild' ); ?></label>
                        <div class="olo-field-hint"><?php esc_html_e( 'Richiesto dalle linee guida CNIL/Garante Privacy italiano.', 'olobuild' ); ?></div>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo esc_attr( $n ); ?>[show_reject_all]" value="1" <?php checked( $opts['show_reject_all'] ); ?> />
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Mostra "Personalizza"', 'olobuild' ); ?></label>
                        <div class="olo-field-hint"><?php esc_html_e( 'Pulsante per aprire il pannello preferenze con le categorie.', 'olobuild' ); ?></div>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo esc_attr( $n ); ?>[show_preferences]" value="1" <?php checked( $opts['show_preferences'] ); ?> />
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Durata consenso', 'olobuild' ); ?></label>
                        <div class="olo-field-hint"><?php esc_html_e( 'GDPR raccomanda max 6 mesi (180 giorni). Molti usano 365.', 'olobuild' ); ?></div>
                    </div>
                    <div class="olo-field-input-wrap olo-field-input-short">
                        <input type="number" name="<?php echo esc_attr( $n ); ?>[consent_duration]" value="<?php echo esc_attr( $opts['consent_duration'] ); ?>" min="1" max="730" class="olo-field-input" />
                        <span class="olo-field-suffix"><?php esc_html_e( 'giorni', 'olobuild' ); ?></span>
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Richiedere nuovamente dopo', 'olobuild' ); ?></label>
                        <div class="olo-field-hint"><?php esc_html_e( 'Se la cookie policy cambia, puoi forzare la ripresentazione del banner.', 'olobuild' ); ?></div>
                    </div>
                    <div class="olo-field-input-wrap olo-field-input-short">
                        <input type="number" name="<?php echo esc_attr( $n ); ?>[reshow_days]" value="<?php echo esc_attr( $opts['reshow_days'] ); ?>" min="0" max="365" class="olo-field-input" />
                        <span class="olo-field-suffix"><?php esc_html_e( 'giorni (0 = mai)', 'olobuild' ); ?></span>
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Google Consent Mode v2', 'olobuild' ); ?></label>
                        <div class="olo-field-hint"><?php esc_html_e( 'Invia segnali gtag("consent") a Google. Richiesto da Google Ads dal marzo 2024 per EEA/UK.', 'olobuild' ); ?></div>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo esc_attr( $n ); ?>[gcm_enabled]" value="1" <?php checked( $opts['gcm_enabled'] ); ?> />
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Blocco iframe', 'olobuild' ); ?></label>
                        <div class="olo-field-hint"><?php esc_html_e( 'Blocca iframe di YouTube, Vimeo, Google Maps fino al consenso. Mostra un placeholder con pulsante "Carica contenuto".', 'olobuild' ); ?></div>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo esc_attr( $n ); ?>[block_iframes]" value="1" <?php checked( $opts['block_iframes'] ); ?> />
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Versione banner', 'olobuild' ); ?></label>
                        <div class="olo-field-hint"><?php esc_html_e( 'Incrementa quando cambi la cookie policy. I visitatori con consenso a una versione precedente vedranno di nuovo il banner.', 'olobuild' ); ?></div>
                    </div>
                    <div class="olo-field-input-wrap olo-field-input-short">
                        <input type="number" name="<?php echo esc_attr( $n ); ?>[banner_version]" value="<?php echo esc_attr( $opts['banner_version'] ); ?>" min="1" class="olo-field-input" />
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /* ─── Tab: Texts ─── */

    private function render_tab_texts( $opts ) {
        $n = self::OPT;
        ?>
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </div>
                <div>
                    <h3><?php esc_html_e( 'Testi del banner', 'olobuild' ); ?></h3>
                    <p><?php esc_html_e( 'Personalizza i testi mostrati nel cookie banner', 'olobuild' ); ?></p>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Titolo', 'olobuild' ); ?></label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="text" name="<?php echo esc_attr( $n ); ?>[banner_title]" value="<?php echo esc_attr( $opts['banner_title'] ); ?>" class="olo-field-input" />
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Messaggio', 'olobuild' ); ?></label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <textarea name="<?php echo esc_attr( $n ); ?>[banner_message]" rows="3" class="olo-field-input"><?php echo esc_textarea( $opts['banner_message'] ); ?></textarea>
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Pulsante "Accetta tutti"', 'olobuild' ); ?></label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="text" name="<?php echo esc_attr( $n ); ?>[accept_all_text]" value="<?php echo esc_attr( $opts['accept_all_text'] ); ?>" class="olo-field-input" />
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Pulsante "Rifiuta tutti"', 'olobuild' ); ?></label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="text" name="<?php echo esc_attr( $n ); ?>[reject_all_text]" value="<?php echo esc_attr( $opts['reject_all_text'] ); ?>" class="olo-field-input" />
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Pulsante "Personalizza"', 'olobuild' ); ?></label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="text" name="<?php echo esc_attr( $n ); ?>[preferences_text]" value="<?php echo esc_attr( $opts['preferences_text'] ); ?>" class="olo-field-input" />
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Pulsante "Salva preferenze"', 'olobuild' ); ?></label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="text" name="<?php echo esc_attr( $n ); ?>[save_text]" value="<?php echo esc_attr( $opts['save_text'] ); ?>" class="olo-field-input" />
                    </div>
                </div>
            </div>
        </div>

        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon orange">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
                </div>
                <div>
                    <h3><?php esc_html_e( 'Link policy', 'olobuild' ); ?></h3>
                    <p><?php esc_html_e( 'URL e testi dei link alle pagine di policy', 'olobuild' ); ?></p>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'URL Privacy Policy', 'olobuild' ); ?></label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="url" name="<?php echo esc_attr( $n ); ?>[privacy_url]" value="<?php echo esc_url( $opts['privacy_url'] ); ?>" class="olo-field-input" placeholder="https://..." />
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Testo link Privacy', 'olobuild' ); ?></label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="text" name="<?php echo esc_attr( $n ); ?>[privacy_text]" value="<?php echo esc_attr( $opts['privacy_text'] ); ?>" class="olo-field-input" />
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'URL Cookie Policy', 'olobuild' ); ?></label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="url" name="<?php echo esc_attr( $n ); ?>[cookie_policy_url]" value="<?php echo esc_url( $opts['cookie_policy_url'] ); ?>" class="olo-field-input" placeholder="https://..." />
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Testo link Cookie Policy', 'olobuild' ); ?></label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="text" name="<?php echo esc_attr( $n ); ?>[cookie_policy_text]" value="<?php echo esc_attr( $opts['cookie_policy_text'] ); ?>" class="olo-field-input" />
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /* ─── Tab: Appearance ─── */

    private function render_tab_appearance( $opts ) {
        $n = self::OPT;
        ?>
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2.69l5.66 5.66a8 8 0 11-11.31 0z"/></svg>
                </div>
                <div>
                    <h3><?php esc_html_e( 'Colori banner', 'olobuild' ); ?></h3>
                    <p><?php esc_html_e( 'Sfondo e testo del cookie banner', 'olobuild' ); ?></p>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Sfondo banner', 'olobuild' ); ?></label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="color" name="<?php echo esc_attr( $n ); ?>[bg_color]" value="<?php echo esc_attr( $opts['bg_color'] ); ?>" class="olo-field-color" />
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Colore testo', 'olobuild' ); ?></label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="color" name="<?php echo esc_attr( $n ); ?>[text_color]" value="<?php echo esc_attr( $opts['text_color'] ); ?>" class="olo-field-color" />
                    </div>
                </div>
            </div>
        </div>

        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon orange">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M12 8v8"/><path d="M8 12h8"/></svg>
                </div>
                <div>
                    <h3><?php esc_html_e( 'Colori pulsanti', 'olobuild' ); ?></h3>
                    <p><?php esc_html_e( 'Personalizza i colori dei pulsanti "Accetta" e "Rifiuta"', 'olobuild' ); ?></p>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Pulsante primario — sfondo', 'olobuild' ); ?></label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="color" name="<?php echo esc_attr( $n ); ?>[btn_primary_bg]" value="<?php echo esc_attr( $opts['btn_primary_bg'] ); ?>" class="olo-field-color" />
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Pulsante primario — testo', 'olobuild' ); ?></label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="color" name="<?php echo esc_attr( $n ); ?>[btn_primary_text]" value="<?php echo esc_attr( $opts['btn_primary_text'] ); ?>" class="olo-field-color" />
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Pulsante secondario — sfondo', 'olobuild' ); ?></label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="color" name="<?php echo esc_attr( $n ); ?>[btn_secondary_bg]" value="<?php echo esc_attr( $opts['btn_secondary_bg'] ); ?>" class="olo-field-color" />
                    </div>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Pulsante secondario — testo', 'olobuild' ); ?></label>
                    </div>
                    <div class="olo-field-input-wrap">
                        <input type="color" name="<?php echo esc_attr( $n ); ?>[btn_secondary_text]" value="<?php echo esc_attr( $opts['btn_secondary_text'] ); ?>" class="olo-field-color" />
                    </div>
                </div>
            </div>
        </div>

        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                </div>
                <div>
                    <h3><?php esc_html_e( 'Stile', 'olobuild' ); ?></h3>
                    <p><?php esc_html_e( 'Overlay e border radius del banner', 'olobuild' ); ?></p>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Overlay scuro', 'olobuild' ); ?></label>
                        <div class="olo-field-hint"><?php esc_html_e( 'Oscura la pagina dietro il banner per attirare l\'attenzione.', 'olobuild' ); ?></div>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo esc_attr( $n ); ?>[overlay]" value="1" <?php checked( $opts['overlay'] ); ?> />
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Border radius', 'olobuild' ); ?></label>
                    </div>
                    <div class="olo-field-input-wrap olo-field-input-short">
                        <input type="number" name="<?php echo esc_attr( $n ); ?>[border_radius]" value="<?php echo esc_attr( $opts['border_radius'] ); ?>" min="0" max="30" class="olo-field-input" />
                        <span class="olo-field-suffix">px</span>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /* ─── Tab: Categories ─── */

    private function render_tab_categories( $opts ) {
        $n = self::OPT;
        $cats = [
            'necessary'   => [ 'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>', 'locked' => true ],
            'analytics'   => [ 'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>', 'locked' => false ],
            'marketing'   => [ 'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>', 'locked' => false ],
            'preferences' => [ 'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/></svg>', 'locked' => false ],
        ];
        ?>
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                </div>
                <div>
                    <h3><?php esc_html_e( 'Categorie cookie', 'olobuild' ); ?></h3>
                    <p><?php esc_html_e( 'Personalizza nome e descrizione di ogni categoria mostrata nel pannello preferenze.', 'olobuild' ); ?></p>
                </div>
            </div>
            <div class="olo-card-body">
                <?php foreach ( $cats as $cat => $meta ) : ?>
                <div class="olo-card" style="margin:0 0 16px;box-shadow:none;border:1px solid #e5e0db">
                    <div class="olo-card-head" style="padding:14px 18px">
                        <div class="olo-card-icon <?php echo $meta['locked'] ? 'orange' : 'black'; ?>">
                            <?php echo $meta['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup from the hardcoded $cats map above. ?>
                        </div>
                        <div style="display:flex;align-items:center;gap:10px">
                            <strong><?php echo esc_html( ucfirst( $cat ) ); ?></strong>
                            <?php if ( $meta['locked'] ) : ?>
                                <span class="olo-badge green"><?php esc_html_e( 'Sempre attivo', 'olobuild' ); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="olo-card-body" style="padding:12px 18px">
                        <div class="olo-field-row">
                            <div class="olo-field-info">
                                <label><?php esc_html_e( 'Etichetta', 'olobuild' ); ?></label>
                            </div>
                            <div class="olo-field-input-wrap">
                                <input type="text" name="<?php echo esc_attr( $n ); ?>[cat_<?php echo esc_attr( $cat ); ?>_label]" value="<?php echo esc_attr( $opts[ "cat_{$cat}_label" ] ); ?>" class="olo-field-input" />
                            </div>
                        </div>
                        <div class="olo-field-row">
                            <div class="olo-field-info">
                                <label><?php esc_html_e( 'Descrizione', 'olobuild' ); ?></label>
                            </div>
                            <div class="olo-field-input-wrap">
                                <textarea name="<?php echo esc_attr( $n ); ?>[cat_<?php echo esc_attr( $cat ); ?>_desc]" rows="2" class="olo-field-input"><?php echo esc_textarea( $opts[ "cat_{$cat}_desc" ] ); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /* ─── Tab: Blocking ─── */

    private function render_tab_blocking( $opts ) {
        $n = self::OPT;
        ?>
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                </div>
                <div>
                    <h3><?php esc_html_e( 'Blocco automatico script', 'olobuild' ); ?></h3>
                    <p><?php esc_html_e( 'Blocca automaticamente script di terze parti finché l\'utente non accetta la categoria corrispondente.', 'olobuild' ); ?></p>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-field-row">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Abilita auto-blocco', 'olobuild' ); ?></label>
                        <div class="olo-field-hint"><?php esc_html_e( 'Converte gli script corrispondenti in type="text/plain" finché il consenso non è dato.', 'olobuild' ); ?></div>
                    </div>
                    <label class="olo-toggle">
                        <input type="checkbox" name="<?php echo esc_attr( $n ); ?>[auto_block]" value="1" <?php checked( $opts['auto_block'] ); ?> />
                        <span class="olo-toggle-slider"></span>
                    </label>
                </div>
                <div class="olo-field-row olo-field-row-stack">
                    <div class="olo-field-info">
                        <label><?php esc_html_e( 'Pattern da bloccare', 'olobuild' ); ?></label>
                        <div class="olo-field-hint"><?php esc_html_e( 'Un dominio/pattern per riga. Gli script il cui src contiene uno di questi pattern vengono bloccati.', 'olobuild' ); ?></div>
                    </div>
                    <div class="olo-field-input-wrap" style="flex:1 1 100%">
                        <textarea name="<?php echo esc_attr( $n ); ?>[block_patterns]" rows="10" class="olo-field-input olo-field-code"><?php echo esc_textarea( $opts['block_patterns'] ); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon orange">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                </div>
                <div>
                    <h3><?php esc_html_e( 'Blocco manuale', 'olobuild' ); ?></h3>
                    <p><?php esc_html_e( 'Puoi anche bloccare script manualmente nel tuo HTML aggiungendo:', 'olobuild' ); ?></p>
                </div>
            </div>
            <div class="olo-card-body">
                <pre class="olo-ck-code">&lt;script type="text/plain" data-cookiecategory="analytics" src="..."&gt;&lt;/script&gt;
&lt;script type="text/plain" data-cookiecategory="marketing"&gt;
  // inline script bloccato finché l'utente non accetta "marketing"
&lt;/script&gt;</pre>
            </div>
        </div>
        <?php
    }

    /* ─── Tab: Declaration ─── */

    private function render_tab_declaration( $opts ) {
        $n    = self::OPT;
        $rows = $opts['cookie_table'];
        ?>
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </div>
                <div>
                    <h3><?php esc_html_e( 'Dichiarazione cookie', 'olobuild' ); ?></h3>
                    <p><?php esc_html_e( 'Elenca i cookie utilizzati dal sito. Questi vengono mostrati nel pannello preferenze.', 'olobuild' ); ?></p>
                </div>
            </div>
            <div class="olo-card-body">
                <table class="olo-table" id="olo-ck-decl">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Nome cookie', 'olobuild' ); ?></th>
                            <th><?php esc_html_e( 'Provider', 'olobuild' ); ?></th>
                            <th><?php esc_html_e( 'Scopo', 'olobuild' ); ?></th>
                            <th><?php esc_html_e( 'Scadenza', 'olobuild' ); ?></th>
                            <th><?php esc_html_e( 'Categoria', 'olobuild' ); ?></th>
                            <th style="width:40px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ( ! empty( $rows ) ) : ?>
                            <?php foreach ( $rows as $i => $row ) : ?>
                                <tr>
                                    <td><input type="text" name="<?php echo esc_attr( $n ); ?>[cookie_table][<?php echo (int) $i; ?>][name]" value="<?php echo esc_attr( $row['name'] ); ?>" class="olo-field-input" /></td>
                                    <td><input type="text" name="<?php echo esc_attr( $n ); ?>[cookie_table][<?php echo (int) $i; ?>][provider]" value="<?php echo esc_attr( $row['provider'] ); ?>" class="olo-field-input" /></td>
                                    <td><input type="text" name="<?php echo esc_attr( $n ); ?>[cookie_table][<?php echo (int) $i; ?>][purpose]" value="<?php echo esc_attr( $row['purpose'] ); ?>" class="olo-field-input" /></td>
                                    <td><input type="text" name="<?php echo esc_attr( $n ); ?>[cookie_table][<?php echo (int) $i; ?>][expiry]" value="<?php echo esc_attr( $row['expiry'] ); ?>" class="olo-field-input" style="width:100px" /></td>
                                    <td>
                                        <select name="<?php echo esc_attr( $n ); ?>[cookie_table][<?php echo (int) $i; ?>][category]" class="olo-field-input">
                                            <?php foreach ( self::CATEGORIES as $c ) : ?>
                                                <option value="<?php echo esc_attr( $c ); ?>" <?php selected( $row['category'], $c ); ?>><?php echo esc_html( $opts[ "cat_{$c}_label" ] ); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><button type="button" class="olo-btn-danger olo-btn-sm olo-ck-remove-row" title="<?php esc_attr_e( 'Rimuovi', 'olobuild' ); ?>">&times;</button></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <button type="button" class="olo-btn-orange olo-btn-sm" id="olo-ck-add-row" style="margin-top:12px">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    <?php esc_html_e( 'Aggiungi cookie', 'olobuild' ); ?>
                </button>
            </div>
        </div>

        <script>
        (function(){
            var idx = <?php echo (int) count( $rows ); ?>;
            var n   = '<?php echo esc_js( $n ); ?>';
            var cats = <?php echo wp_json_encode( array_map( function($c) use ($opts) { return [ 'value' => $c, 'label' => $opts["cat_{$c}_label"] ]; }, self::CATEGORIES ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline JSON in <script> built by wp_json_encode(). ?>;

            document.getElementById('olo-ck-add-row').addEventListener('click', function(){
                var tbody = document.querySelector('#olo-ck-decl tbody');
                var tr = document.createElement('tr');
                var catOpts = '';
                cats.forEach(function(c){ catOpts += '<option value="'+c.value+'">'+c.label+'</option>'; });
                tr.innerHTML = '<td><input type="text" name="'+n+'[cookie_table]['+idx+'][name]" class="olo-field-input" /></td>'
                    + '<td><input type="text" name="'+n+'[cookie_table]['+idx+'][provider]" class="olo-field-input" /></td>'
                    + '<td><input type="text" name="'+n+'[cookie_table]['+idx+'][purpose]" class="olo-field-input" /></td>'
                    + '<td><input type="text" name="'+n+'[cookie_table]['+idx+'][expiry]" class="olo-field-input" style="width:100px" /></td>'
                    + '<td><select name="'+n+'[cookie_table]['+idx+'][category]" class="olo-field-input">'+catOpts+'</select></td>'
                    + '<td><button type="button" class="olo-btn-danger olo-btn-sm olo-ck-remove-row">&times;</button></td>';
                tbody.appendChild(tr);
                idx++;
            });

            document.getElementById('olo-ck-decl').addEventListener('click', function(e){
                if(e.target.classList.contains('olo-ck-remove-row')){
                    e.target.closest('tr').remove();
                }
            });
        })();
        </script>
        <?php
    }

    /* ─── Tab: Consent Log ─── */

    private function render_tab_consent_log() {
        global $wpdb;
        $table = $wpdb->prefix . self::LOG_TABLE;

        $total = 0;
        if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table ) ) === $table ) {
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $table is built from $wpdb->prefix constant
            $total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM `$table`" );
        }

        $page     = max( 1, intval( $_GET['paged'] ?? 1 ) );
        $per_page = 30;
        $offset   = ( $page - 1 ) * $per_page;
        $rows     = [];

        if ( $total > 0 ) {
            $rows = $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM $table ORDER BY created_at DESC LIMIT %d OFFSET %d",
                $per_page, $offset
            ), ARRAY_A );
        }

        ?>
        <div class="olo-card">
            <div class="olo-card-head">
                <div class="olo-card-icon black">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <div>
                    <h3><?php esc_html_e( 'Log dei consensi', 'olobuild' ); ?></h3>
                    <p>
                        <?php
                        printf(
                            esc_html__( '%d consensi registrati. Il GDPR richiede di poter dimostrare il consenso raccolto.', 'olobuild' ),
                            (int) $total
                        );
                        ?>
                    </p>
                </div>
            </div>
            <div class="olo-card-body">
                <div class="olo-ck-log-actions">
                    <button type="button" class="olo-btn-reset" id="olo-ck-export-log">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        <?php esc_html_e( 'Esporta CSV', 'olobuild' ); ?>
                    </button>
                    <button type="button" class="olo-btn-reset" id="olo-ck-clear-log">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                        <?php esc_html_e( 'Svuota log', 'olobuild' ); ?>
                    </button>
                    <span id="olo-ck-log-msg" class="olo-msg"></span>
                </div>

                <?php if ( ! empty( $rows ) ) : ?>
                <div class="olo-ck-log-table-wrap">
                    <table class="olo-ck-log-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'ID Consenso', 'olobuild' ); ?></th>
                                <th><?php esc_html_e( 'Data', 'olobuild' ); ?></th>
                                <th><?php esc_html_e( 'Azione', 'olobuild' ); ?></th>
                                <th><?php esc_html_e( 'Ver.', 'olobuild' ); ?></th>
                                <th><?php esc_html_e( 'IP (hash)', 'olobuild' ); ?></th>
                                <th><?php esc_html_e( 'Categorie accettate', 'olobuild' ); ?></th>
                                <th><?php esc_html_e( 'User Agent', 'olobuild' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $rows as $row ) : ?>
                            <tr>
                                <td><code><?php echo esc_html( substr( $row['consent_id'], 0, 12 ) ); ?>...</code></td>
                                <td><?php echo esc_html( $row['created_at'] ); ?></td>
                                <td>
                                    <?php
                                    $at = $row['action_type'] ?? 'initial';
                                    $at_labels = [ 'initial' => 'Primo consenso', 'update' => 'Modifica', 'revoke' => 'Revoca' ];
                                    $at_classes = [ 'initial' => 'olo-ck-action-initial', 'update' => 'olo-ck-action-update', 'revoke' => 'olo-ck-action-revoke' ];
                                    echo '<span class="' . esc_attr( $at_classes[ $at ] ?? '' ) . '">' . esc_html( $at_labels[ $at ] ?? $at ) . '</span>';
                                    ?>
                                </td>
                                <td><?php echo intval( $row['banner_version'] ?? 1 ); ?></td>
                                <td><code><?php echo esc_html( substr( $row['ip_hash'], 0, 16 ) ); ?>...</code></td>
                                <td>
                                    <?php
                                    $cats = json_decode( $row['categories'], true );
                                    if ( is_array( $cats ) ) {
                                        foreach ( $cats as $c => $v ) {
                                            if ( $v ) {
                                                echo '<span class="olo-ck-cat-pill">' . esc_html( $c ) . '</span> ';
                                            }
                                        }
                                    }
                                    ?>
                                </td>
                                <td class="olo-ck-ua-cell" title="<?php echo esc_attr( $row['user_agent'] ); ?>"><?php echo esc_html( $row['user_agent'] ); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php
                // Pagination
                $total_pages = ceil( $total / $per_page );
                if ( $total_pages > 1 ) {
                    echo '<div class="tablenav"><div class="tablenav-pages">';
                    echo paginate_links( [ // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- paginate_links() returns core-generated pagination HTML with escaped URLs.
                        'base'    => add_query_arg( 'paged', '%#%' ),
                        'format'  => '',
                        'current' => $page,
                        'total'   => $total_pages,
                    ] );
                    echo '</div></div>';
                }
                ?>
                <?php else : ?>
                    <p class="olo-ck-empty"><?php esc_html_e( 'Nessun consenso registrato.', 'olobuild' ); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <script>
        (function(){
            var nonce = '<?php echo esc_js( wp_create_nonce( 'olo_cookie_log' ) ); ?>';
            var msg   = document.getElementById('olo-ck-log-msg');

            document.getElementById('olo-ck-clear-log').addEventListener('click', function(){
                if(!confirm('<?php echo esc_js( __( 'Svuotare tutti i log dei consensi?', 'olobuild' ) ); ?>'))return;
                this.disabled = true;
                var self = this;
                fetch(ajaxurl + '?action=olo_cookie_clear_log&_nonce=' + nonce)
                    .then(function(r){return r.json()})
                    .then(function(d){
                        if(d.success){ location.reload(); }
                        self.disabled = false;
                    });
            });

            document.getElementById('olo-ck-export-log').addEventListener('click', function(){
                window.location = ajaxurl + '?action=olo_cookie_export_log&_nonce=' + nonce;
            });
        })();
        </script>
        <?php
    }

    /* ═══════════════════════════════════════════════════
     * DATABASE
     * ═══════════════════════════════════════════════════ */

    public function maybe_create_table() {
        global $wpdb;
        $table   = $wpdb->prefix . self::LOG_TABLE;
        $charset = $wpdb->get_charset_collate();

        // dbDelta handles both CREATE and ALTER (adding new columns)
        // NOTE: dbDelta requires PRIMARY KEY on its own line (not inline) to avoid
        // "Multiple primary key defined" error on subsequent runs.
        $sql = "CREATE TABLE $table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            consent_id VARCHAR(64) NOT NULL,
            ip_hash VARCHAR(64) NOT NULL DEFAULT '',
            categories TEXT NOT NULL,
            action_type VARCHAR(20) NOT NULL DEFAULT 'initial',
            banner_version INT UNSIGNED NOT NULL DEFAULT 1,
            user_agent VARCHAR(512) NOT NULL DEFAULT '',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            INDEX idx_consent_id (consent_id),
            INDEX idx_created (created_at)
        ) $charset;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    /* ═══════════════════════════════════════════════════
     * AJAX HANDLERS
     * ═══════════════════════════════════════════════════ */

    public function ajax_log_consent() {
        // Rate limiting for anonymous consent logging (5 per IP per hour to prevent flooding)
        $ip_raw = sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0' );
        $rl_key = 'olo_consent_rl_' . md5( $ip_raw );
        $rl_count = intval( get_transient( $rl_key ) );
        if ( $rl_count >= 5 ) {
            wp_send_json_error( 'Rate limited' );
        }
        set_transient( $rl_key, $rl_count + 1, HOUR_IN_SECONDS );

        global $wpdb;

        $consent_id   = sanitize_text_field( $_POST['consent_id'] ?? '' );
        $categories   = sanitize_text_field( $_POST['categories'] ?? '' );
        $action_type  = sanitize_text_field( $_POST['action_type'] ?? 'initial' );
        $bv           = intval( $_POST['banner_version'] ?? 1 );

        if ( empty( $consent_id ) ) {
            wp_send_json_error( 'Missing consent_id' );
        }

        // Validate action type
        if ( ! in_array( $action_type, [ 'initial', 'update', 'revoke' ], true ) ) {
            $action_type = 'initial';
        }

        // Hash IP for privacy (GDPR: no plain IP storage)
        $ip_hash = hash( 'sha256', $_SERVER['REMOTE_ADDR'] . wp_salt( 'auth' ) );
        $ua      = sanitize_text_field( substr( $_SERVER['HTTP_USER_AGENT'] ?? '', 0, 512 ) );

        // Validate categories JSON
        $cats = json_decode( stripslashes( $categories ), true );
        if ( ! is_array( $cats ) ) {
            $cats = [];
        }

        $table = $wpdb->prefix . self::LOG_TABLE;
        $wpdb->insert( $table, [
            'consent_id'     => $consent_id,
            'ip_hash'        => $ip_hash,
            'categories'     => wp_json_encode( $cats ),
            'action_type'    => $action_type,
            'banner_version' => $bv,
            'user_agent'     => $ua,
        ] );

        // Auto-prune: keep max 10000 entries
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $table is built from $wpdb->prefix constant
        $count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM `$table`" );
        if ( $count > 10000 ) {
            $wpdb->query( $wpdb->prepare( "DELETE FROM `$table` ORDER BY created_at ASC LIMIT %d", $count - 10000 ) );
        }

        wp_send_json_success();
    }

    public function ajax_clear_log() {
        check_ajax_referer( 'olo_cookie_log', '_nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Forbidden' );
        }

        global $wpdb;
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- table name from constant
        $wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . self::LOG_TABLE . "`" );
        wp_send_json_success();
    }

    public function ajax_export_log() {
        check_ajax_referer( 'olo_cookie_log', '_nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Forbidden' );
        }

        global $wpdb;
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- table name from constant
        $rows = $wpdb->get_results( "SELECT * FROM `" . $wpdb->prefix . self::LOG_TABLE . "` ORDER BY created_at DESC", ARRAY_A );

        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename=consent-log-' . date( 'Y-m-d' ) . '.csv' );

        $out = fopen( 'php://output', 'w' );
        fputcsv( $out, [ 'ID', 'Consent ID', 'IP Hash', 'Categories', 'Action Type', 'Banner Version', 'User Agent', 'Date' ] );

        foreach ( $rows as $row ) {
            // olo_csv_safe: user_agent/categories arrivano dal client → anti CSV formula injection.
            fputcsv( $out, array_map( 'olo_csv_safe', [
                $row['id'],
                $row['consent_id'],
                $row['ip_hash'],
                $row['categories'],
                $row['action_type'] ?? 'initial',
                $row['banner_version'] ?? 1,
                $row['user_agent'],
                $row['created_at'],
            ] ) );
        }

        fclose( $out );
        exit;
    }

    /* ═══════════════════════════════════════════════════
     * FRONTEND
     * ═══════════════════════════════════════════════════ */

    /**
     * Output Google Consent Mode v2 default state (deny all non-necessary).
     * MUST be first script in <head>, before GTM/GA.
     * NOTA: No && in inline scripts — WordPress converts them to HTML entities.
     */
    public function output_gcm_default() {
        $opts = self::get_options();
        if ( empty( $opts['gcm_enabled'] ) ) {
            return;
        }
        ?>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('consent', 'default', {
            'ad_storage': 'denied',
            'ad_user_data': 'denied',
            'ad_personalization': 'denied',
            'analytics_storage': 'denied',
            'functionality_storage': 'denied',
            'personalization_storage': 'denied',
            'security_storage': 'granted'
        });
        </script>
        <?php
    }

    /**
     * Render the complete frontend: banner + preferences modal + JS.
     */
    public function render_frontend() {
        if ( is_admin() ) {
            return;
        }

        $opts = self::get_options();

        $bg      = sanitize_hex_color( $opts['bg_color'] ) ?: '#ffffff';
        $text    = sanitize_hex_color( $opts['text_color'] ) ?: '#1f2937';
        $btnPBg  = sanitize_hex_color( $opts['btn_primary_bg'] ) ?: '#2563eb';
        $btnPTx  = sanitize_hex_color( $opts['btn_primary_text'] ) ?: '#ffffff';
        $btnSBg  = sanitize_hex_color( $opts['btn_secondary_bg'] ) ?: '#e5e7eb';
        $btnSTx  = sanitize_hex_color( $opts['btn_secondary_text'] ) ?: '#374151';
        $radius  = intval( $opts['border_radius'] );
        $layout  = $opts['layout'];
        $pos     = $opts['position'] === 'top' ? 'top' : 'bottom';
        $dur     = intval( $opts['consent_duration'] );

        // Build policy links HTML
        $links = '';
        if ( ! empty( $opts['privacy_url'] ) ) {
            $links .= ' <a href="' . esc_url( $opts['privacy_url'] ) . '" target="_blank" rel="noopener" style="color:' . $btnPBg . '">' . esc_html( $opts['privacy_text'] ) . '</a>';
        }
        if ( ! empty( $opts['cookie_policy_url'] ) ) {
            if ( $links ) { $links .= ' | '; }
            $links .= '<a href="' . esc_url( $opts['cookie_policy_url'] ) . '" target="_blank" rel="noopener" style="color:' . $btnPBg . '">' . esc_html( $opts['cookie_policy_text'] ) . '</a>';
        }

        // Categories for modal
        $categories = [];
        foreach ( self::CATEGORIES as $cat ) {
            $categories[] = [
                'id'       => $cat,
                'label'    => $opts[ "cat_{$cat}_label" ],
                'desc'     => $opts[ "cat_{$cat}_desc" ],
                'required' => $cat === 'necessary',
            ];
        }

        // Cookie table for modal
        $cookie_table = $opts['cookie_table'];

        // Position CSS
        $bar_pos = $pos === 'top' ? 'top:0' : 'bottom:0';
        $box_pos = $pos === 'top' ? 'top:20px' : 'bottom:20px';

        // Layout-specific styles
        $banner_style = '';
        if ( $layout === 'bar' ) {
            $banner_style = "position:fixed;{$bar_pos};left:0;right:0;";
        } elseif ( $layout === 'box' ) {
            $banner_style = "position:fixed;{$box_pos};left:20px;max-width:420px;";
        } else {
            $banner_style = "position:fixed;{$bar_pos};left:0;right:0;";
        }

        ?>
        <!-- Olobuild Cookie Consent -->
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above (sanitize_hex_color with fallbacks, intval, fixed position/layout literals). ?>
        <style>
        .olo-cc-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999998;display:none}
        .olo-cc-banner{<?php echo $banner_style; ?>z-index:999999;background:<?php echo $bg; ?>;color:<?php echo $text; ?>;padding:20px 24px;box-shadow:0 -4px 20px rgba(0,0,0,0.12);font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;font-size:14px;line-height:1.6;display:none;border-radius:<?php echo ($layout==='box') ? $radius.'px' : '0'; ?>}
        .olo-cc-banner-inner{max-width:1200px;margin:0 auto}
        .olo-cc-title{font-size:18px;font-weight:700;margin-bottom:8px}
        .olo-cc-message{margin-bottom:16px;opacity:0.85}
        .olo-cc-links{margin-bottom:12px;font-size:13px}
        .olo-cc-btns{display:flex;gap:8px;flex-wrap:wrap}
        .olo-cc-btn{padding:10px 22px;border:none;border-radius:<?php echo (int) $radius; ?>px;cursor:pointer;font-size:14px;font-weight:600;transition:opacity 0.2s;outline:none}
        .olo-cc-btn:hover{opacity:0.88}
        .olo-cc-btn-primary{background:<?php echo $btnPBg; ?>;color:<?php echo $btnPTx; ?>}
        .olo-cc-btn-secondary{background:<?php echo $btnSBg; ?>;color:<?php echo $btnSTx; ?>}
        .olo-cc-btn-link{background:none;color:<?php echo $text; ?>;text-decoration:underline;padding:10px 12px;font-weight:500}

        /* Modal */
        .olo-cc-modal{position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);z-index:999999;background:<?php echo $bg; ?>;color:<?php echo $text; ?>;width:90%;max-width:560px;max-height:85vh;overflow-y:auto;border-radius:<?php echo (int) $radius; ?>px;box-shadow:0 20px 60px rgba(0,0,0,0.2);padding:28px;display:none;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;font-size:14px;line-height:1.6}
        .olo-cc-modal-title{font-size:20px;font-weight:700;margin-bottom:8px}
        .olo-cc-modal-close{position:absolute;top:12px;right:16px;background:none;border:none;font-size:22px;cursor:pointer;color:<?php echo $text; ?>;opacity:0.5;padding:4px}
        .olo-cc-modal-close:hover{opacity:1}
        .olo-cc-cat{border:1px solid <?php echo $btnSBg; ?>;border-radius:<?php echo (int) max(6,$radius-4); ?>px;padding:16px;margin-bottom:12px}
        .olo-cc-cat-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:6px}
        .olo-cc-cat-label{font-weight:600;font-size:15px}
        .olo-cc-cat-desc{font-size:13px;opacity:0.7}
        .olo-cc-cat-required{font-size:11px;background:<?php echo $btnSBg; ?>;color:<?php echo $btnSTx; ?>;padding:2px 8px;border-radius:10px;font-weight:600}

        /* Toggle */
        .olo-cc-toggle{position:relative;width:44px;height:24px;display:inline-block}
        .olo-cc-toggle input{opacity:0;width:0;height:0}
        .olo-cc-toggle-track{position:absolute;inset:0;background:#ccc;border-radius:24px;transition:0.25s;cursor:pointer}
        .olo-cc-toggle-track::before{content:'';position:absolute;width:18px;height:18px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:0.25s}
        .olo-cc-toggle input:checked+.olo-cc-toggle-track{background:<?php echo $btnPBg; ?>}
        .olo-cc-toggle input:checked+.olo-cc-toggle-track::before{transform:translateX(20px)}

        /* Cookie declaration table */
        .olo-cc-decl{margin-top:16px;border-collapse:collapse;width:100%;font-size:12px}
        .olo-cc-decl th,.olo-cc-decl td{text-align:left;padding:6px 8px;border-bottom:1px solid <?php echo $btnSBg; ?>}
        .olo-cc-decl th{font-weight:600;font-size:11px;text-transform:uppercase;letter-spacing:0.5px;opacity:0.6}

        /* Re-open cookie settings button (shortcode) */
        .olo-cc-reopen{cursor:pointer;text-decoration:underline;background:none;border:none;font:inherit;color:inherit}

        @media(max-width:480px){
            .olo-cc-banner{padding:16px}
            .olo-cc-btns{flex-direction:column}
            .olo-cc-btn{width:100%;text-align:center}
            .olo-cc-modal{width:95%;padding:20px}
        }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <?php if ( ! empty( $opts['overlay'] ) ) : ?>
        <div class="olo-cc-overlay" id="olo-cc-overlay"></div>
        <?php endif; ?>

        <!-- Banner -->
        <div class="olo-cc-banner" id="olo-cc-banner" role="dialog" aria-label="<?php echo esc_attr( $opts['banner_title'] ); ?>">
            <div class="olo-cc-banner-inner">
                <?php if ( ! empty( $opts['banner_title'] ) ) : ?>
                    <div class="olo-cc-title"><?php echo esc_html( $opts['banner_title'] ); ?></div>
                <?php endif; ?>
                <div class="olo-cc-message"><?php echo esc_html( $opts['banner_message'] ); ?></div>
                <?php if ( $links ) : ?>
                    <div class="olo-cc-links"><?php echo $links; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- links HTML built above with esc_url()/esc_html() and sanitize_hex_color()'d link color. ?></div>
                <?php endif; ?>
                <div class="olo-cc-btns">
                    <button class="olo-cc-btn olo-cc-btn-primary" id="olo-cc-accept-all"><?php echo esc_html( $opts['accept_all_text'] ); ?></button>
                    <?php if ( $opts['show_reject_all'] ) : ?>
                        <button class="olo-cc-btn olo-cc-btn-secondary" id="olo-cc-reject-all"><?php echo esc_html( $opts['reject_all_text'] ); ?></button>
                    <?php endif; ?>
                    <?php if ( $opts['show_preferences'] ) : ?>
                        <button class="olo-cc-btn olo-cc-btn-link" id="olo-cc-open-prefs"><?php echo esc_html( $opts['preferences_text'] ); ?></button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Preferences Modal -->
        <div class="olo-cc-modal" id="olo-cc-modal" role="dialog" aria-label="<?php esc_attr_e( 'Preferenze cookie', 'olobuild' ); ?>">
            <button class="olo-cc-modal-close" id="olo-cc-modal-close" aria-label="<?php esc_attr_e( 'Chiudi', 'olobuild' ); ?>">&times;</button>
            <div class="olo-cc-modal-title"><?php echo esc_html( $opts['preferences_text'] ); ?></div>
            <p style="opacity:0.7;margin-bottom:16px"><?php esc_html_e( 'Scegli quali categorie di cookie accettare. Puoi modificare le tue preferenze in qualsiasi momento.', 'olobuild' ); ?></p>

            <?php foreach ( $categories as $cat ) : ?>
            <div class="olo-cc-cat">
                <div class="olo-cc-cat-head">
                    <span class="olo-cc-cat-label"><?php echo esc_html( $cat['label'] ); ?></span>
                    <?php if ( $cat['required'] ) : ?>
                        <span class="olo-cc-cat-required"><?php esc_html_e( 'Sempre attivo', 'olobuild' ); ?></span>
                    <?php else : ?>
                        <label class="olo-cc-toggle">
                            <input type="checkbox" data-cc-cat="<?php echo esc_attr( $cat['id'] ); ?>" />
                            <span class="olo-cc-toggle-track"></span>
                        </label>
                    <?php endif; ?>
                </div>
                <div class="olo-cc-cat-desc"><?php echo esc_html( $cat['desc'] ); ?></div>
            </div>
            <?php endforeach; ?>

            <?php if ( ! empty( $cookie_table ) ) : ?>
            <details style="margin-top:12px">
                <summary style="cursor:pointer;font-weight:600;font-size:13px"><?php esc_html_e( 'Dettaglio cookie utilizzati', 'olobuild' ); ?></summary>
                <table class="olo-cc-decl">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Cookie', 'olobuild' ); ?></th>
                            <th><?php esc_html_e( 'Provider', 'olobuild' ); ?></th>
                            <th><?php esc_html_e( 'Scopo', 'olobuild' ); ?></th>
                            <th><?php esc_html_e( 'Scadenza', 'olobuild' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $cookie_table as $row ) : ?>
                        <tr>
                            <td><code><?php echo esc_html( $row['name'] ); ?></code></td>
                            <td><?php echo esc_html( $row['provider'] ); ?></td>
                            <td><?php echo esc_html( $row['purpose'] ); ?></td>
                            <td><?php echo esc_html( $row['expiry'] ); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </details>
            <?php endif; ?>

            <div class="olo-cc-btns" style="margin-top:20px">
                <button class="olo-cc-btn olo-cc-btn-primary" id="olo-cc-save-prefs"><?php echo esc_html( $opts['save_text'] ); ?></button>
                <button class="olo-cc-btn olo-cc-btn-secondary" id="olo-cc-modal-accept-all"><?php echo esc_html( $opts['accept_all_text'] ); ?></button>
            </div>
        </div>

        <script>
        (function(){
            var CC = {
                cookieName: 'olo_cc',
                duration: <?php echo (int) $dur; ?>,
                bannerVersion: <?php echo intval( $opts['banner_version'] ); ?>,
                gcm: <?php echo $opts['gcm_enabled'] ? 'true' : 'false'; ?>,
                blockIframes: <?php echo ! empty( $opts['block_iframes'] ) ? 'true' : 'false'; ?>,
                ajaxUrl: '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>',
                cats: ['necessary','analytics','marketing','preferences'],
                consentId: null,

                init: function(){
                    // Generate or retrieve consent ID
                    var sid = localStorage.getItem('olo_cc_id');
                    if(!sid){
                        sid = 'cc_' + Date.now() + '_' + Math.random().toString(36).substr(2,9);
                        localStorage.setItem('olo_cc_id', sid);
                    }
                    this.consentId = sid;

                    var stored = this.getConsent();

                    // Check banner version — re-ask if policy changed
                    if(stored){
                        var sv = stored._v || 1;
                        if(sv < this.bannerVersion){
                            // Policy changed: clear old consent, re-ask
                            this.clearCookie();
                            stored = null;
                        }
                    }

                    if(!stored){
                        this.showBanner();
                        // Block iframes immediately
                        if(this.blockIframes){ this.blockThirdPartyIframes(); }
                    } else {
                        this.applyConsent(stored, false);
                    }
                    this.bindEvents();
                },

                getConsent: function(){
                    var match = document.cookie.match(new RegExp('(^| )' + this.cookieName + '=([^;]+)'));
                    if(match){
                        try { return JSON.parse(decodeURIComponent(match[2])); } catch(e){}
                    }
                    return null;
                },

                clearCookie: function(){
                    document.cookie = this.cookieName + '=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;SameSite=Lax';
                },

                setConsent: function(cats, actionType){
                    var prev = this.getConsent();
                    if(!actionType){
                        actionType = prev ? 'update' : 'initial';
                    }

                    // Stamp version
                    cats._v = this.bannerVersion;
                    cats._t = new Date().toISOString();

                    var val = encodeURIComponent(JSON.stringify(cats));
                    var d = new Date();
                    d.setTime(d.getTime() + (this.duration * 86400000));
                    document.cookie = this.cookieName + '=' + val + ';expires=' + d.toUTCString() + ';path=/;SameSite=Lax';

                    this.applyConsent(cats, true);
                    this.hideBanner();
                    this.hideModal();
                    this.logConsent(cats, actionType);
                },

                applyConsent: function(cats, isNew){
                    // Activate blocked scripts
                    var scripts = document.querySelectorAll('script[type="text/plain"][data-cookiecategory]');
                    for(var i=0;i<scripts.length;i++){
                        var cat = scripts[i].getAttribute('data-cookiecategory');
                        if(cats[cat]){
                            var ns = document.createElement('script');
                            if(scripts[i].src){ ns.src = scripts[i].src; }
                            else if(scripts[i].getAttribute('data-src')){ ns.src = scripts[i].getAttribute('data-src'); }
                            else { ns.textContent = scripts[i].textContent; }
                            ns.type = 'text/javascript';
                            scripts[i].parentNode.replaceChild(ns, scripts[i]);
                        }
                    }

                    // Unblock iframes
                    if(this.blockIframes){
                        this.unblockIframes(cats);
                    }

                    // Google Consent Mode v2 update
                    if(this.gcm){
                        if(typeof gtag === 'function'){
                            gtag('consent', 'update', {
                                'ad_storage': cats.marketing ? 'granted' : 'denied',
                                'ad_user_data': cats.marketing ? 'granted' : 'denied',
                                'ad_personalization': cats.marketing ? 'granted' : 'denied',
                                'analytics_storage': cats.analytics ? 'granted' : 'denied',
                                'functionality_storage': cats.preferences ? 'granted' : 'denied',
                                'personalization_storage': cats.preferences ? 'granted' : 'denied'
                            });
                        }
                    }

                    // Dispatch custom event
                    window.dispatchEvent(new CustomEvent('oloCookieConsent', { detail: cats }));
                },

                /* ── Iframe blocking ── */

                blockThirdPartyIframes: function(){
                    var iframes = document.querySelectorAll('iframe[src]');
                    var blocked = ['youtube.com','youtube-nocookie.com','youtu.be','vimeo.com','google.com/maps','maps.google','facebook.com','instagram.com'];
                    for(var i=0;i<iframes.length;i++){
                        var src = iframes[i].src || '';
                        var cat = this.getIframeCategory(src, blocked);
                        if(cat){
                            var wrap = document.createElement('div');
                            wrap.className = 'olo-cc-iframe-placeholder';
                            wrap.setAttribute('data-cc-iframe-cat', cat);
                            wrap.setAttribute('data-cc-iframe-src', src);
                            wrap.style.cssText = 'background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px;padding:32px;text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:200px;' + (iframes[i].width ? 'width:'+iframes[i].width+'px;' : 'width:100%;') + (iframes[i].height ? 'height:'+iframes[i].height+'px;' : '');
                            wrap.innerHTML = '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>'
                                + '<p style="margin:12px 0 8px;color:#6b7280;font-size:14px"><?php echo esc_js( __( 'Questo contenuto richiede il consenso ai cookie', 'olobuild' ) ); ?></p>'
                                + '<button class="olo-cc-iframe-load" style="background:#2563eb;color:#fff;border:none;padding:8px 20px;border-radius:6px;cursor:pointer;font-size:13px;font-weight:600"><?php echo esc_js( __( 'Accetta e carica', 'olobuild' ) ); ?></button>';
                            iframes[i].parentNode.replaceChild(wrap, iframes[i]);
                        }
                    }

                    // Single iframe load button
                    document.addEventListener('click', function(e){
                        var btn = e.target.closest('.olo-cc-iframe-load');
                        if(!btn) return;
                        var ph = btn.closest('.olo-cc-iframe-placeholder');
                        if(!ph) return;
                        var cat = ph.getAttribute('data-cc-iframe-cat');
                        var src = ph.getAttribute('data-cc-iframe-src');
                        // Accept this category
                        var current = CC.getConsent() || {necessary:true,analytics:false,marketing:false,preferences:false};
                        current[cat] = true;
                        CC.setConsent(current, 'update');
                    });
                },

                getIframeCategory: function(src, patterns){
                    var marketingDomains = ['facebook.com','instagram.com'];
                    for(var j=0;j<patterns.length;j++){
                        if(src.indexOf(patterns[j]) !== -1){
                            for(var k=0;k<marketingDomains.length;k++){
                                if(src.indexOf(marketingDomains[k]) !== -1) return 'marketing';
                            }
                            return 'analytics';
                        }
                    }
                    return null;
                },

                unblockIframes: function(cats){
                    var phs = document.querySelectorAll('.olo-cc-iframe-placeholder');
                    for(var i=0;i<phs.length;i++){
                        var cat = phs[i].getAttribute('data-cc-iframe-cat');
                        if(cats[cat]){
                            var src = phs[i].getAttribute('data-cc-iframe-src');
                            var iframe = document.createElement('iframe');
                            iframe.src = src;
                            iframe.style.cssText = 'width:' + (phs[i].style.width||'100%') + ';height:' + (phs[i].style.height||'400px') + ';border:0';
                            iframe.allow = 'accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture';
                            iframe.allowFullscreen = true;
                            phs[i].parentNode.replaceChild(iframe, phs[i]);
                        }
                    }
                },

                /* ── Logging ── */

                logConsent: function(cats, actionType){
                    var fd = new FormData();
                    fd.append('action', 'olo_cookie_log_consent');
                    fd.append('consent_id', this.consentId);
                    fd.append('categories', JSON.stringify(cats));
                    fd.append('action_type', actionType || 'initial');
                    fd.append('banner_version', this.bannerVersion);
                    fetch(this.ajaxUrl, { method:'POST', body: fd, credentials:'same-origin' });
                },

                /* ── UI ── */

                showBanner: function(){
                    var b = document.getElementById('olo-cc-banner');
                    var o = document.getElementById('olo-cc-overlay');
                    if(b) b.style.display = 'block';
                    if(o) o.style.display = 'block';
                },

                hideBanner: function(){
                    var b = document.getElementById('olo-cc-banner');
                    var o = document.getElementById('olo-cc-overlay');
                    if(b) b.style.display = 'none';
                    if(o) o.style.display = 'none';
                },

                showModal: function(){
                    var m = document.getElementById('olo-cc-modal');
                    var o = document.getElementById('olo-cc-overlay');
                    if(m) m.style.display = 'block';
                    if(o) o.style.display = 'block';
                    this.hideBanner();

                    // Restore current choices
                    var stored = this.getConsent();
                    if(stored){
                        var toggles = m.querySelectorAll('[data-cc-cat]');
                        for(var i=0;i<toggles.length;i++){
                            var cat = toggles[i].getAttribute('data-cc-cat');
                            toggles[i].checked = !!stored[cat];
                        }
                    }
                },

                hideModal: function(){
                    var m = document.getElementById('olo-cc-modal');
                    var o = document.getElementById('olo-cc-overlay');
                    if(m) m.style.display = 'none';
                    if(o) o.style.display = 'none';
                },

                acceptAll: function(){
                    var cats = { necessary: true, analytics: true, marketing: true, preferences: true };
                    this.setConsent(cats);
                },

                rejectAll: function(){
                    var cats = { necessary: true, analytics: false, marketing: false, preferences: false };
                    this.setConsent(cats, 'revoke');
                },

                savePrefs: function(){
                    var m = document.getElementById('olo-cc-modal');
                    var cats = { necessary: true };
                    var toggles = m.querySelectorAll('[data-cc-cat]');
                    for(var i=0;i<toggles.length;i++){
                        cats[toggles[i].getAttribute('data-cc-cat')] = toggles[i].checked;
                    }
                    this.setConsent(cats);
                },

                bindEvents: function(){
                    var self = this;
                    var on = function(id, fn){
                        var el = document.getElementById(id);
                        if(el){ el.addEventListener('click', function(){ fn.call(self); }); }
                    };

                    on('olo-cc-accept-all', self.acceptAll);
                    on('olo-cc-reject-all', self.rejectAll);
                    on('olo-cc-open-prefs', self.showModal);
                    on('olo-cc-modal-close', self.hideModal);
                    on('olo-cc-save-prefs', self.savePrefs);
                    on('olo-cc-modal-accept-all', self.acceptAll);

                    // Re-open buttons (shortcode)
                    document.addEventListener('click', function(e){
                        if(e.target.closest('.olo-cc-reopen')){
                            e.preventDefault();
                            self.showModal();
                        }
                    });

                    // Close modal on overlay click
                    var overlay = document.getElementById('olo-cc-overlay');
                    if(overlay){
                        overlay.addEventListener('click', function(){
                            self.hideModal();
                            // If no consent yet, re-show banner
                            if(!self.getConsent()){
                                self.showBanner();
                            }
                        });
                    }

                    // ESC key
                    document.addEventListener('keydown', function(e){
                        if(e.key === 'Escape'){
                            var m = document.getElementById('olo-cc-modal');
                            if(m){ if(m.style.display !== 'none'){ self.hideModal(); if(!self.getConsent()){self.showBanner();} } }
                        }
                    });
                }
            };

            if(document.readyState === 'loading'){
                document.addEventListener('DOMContentLoaded', function(){ CC.init(); });
            } else {
                CC.init();
            }

            // Global API
            window.oloCookieConsent = CC;
        })();
        </script>
        <?php
    }

    /* ═══════════════════════════════════════════════════
     * SCRIPT AUTO-BLOCKING
     * ═══════════════════════════════════════════════════ */

    /**
     * Auto-block known third-party scripts by changing type to text/plain.
     * Scripts are activated when user grants consent for the matching category.
     */
    public function auto_block_scripts( $tag, $handle ) {
        // Don't block in admin
        if ( is_admin() ) {
            return $tag;
        }

        // Don't block if consent already given
        if ( isset( $_COOKIE['olo_cc'] ) ) {
            $consent = json_decode( wp_unslash( $_COOKIE['olo_cc'] ), true );
            if ( json_last_error() !== JSON_ERROR_NONE ) { $consent = []; }
            if ( is_array( $consent ) ) {
                // If all accepted, don't block anything
                $all = true;
                foreach ( [ 'analytics', 'marketing', 'preferences' ] as $c ) {
                    if ( empty( $consent[ $c ] ) ) {
                        $all = false;
                        break;
                    }
                }
                if ( $all ) {
                    return $tag;
                }
            }
        }

        $opts     = self::get_options();
        $patterns = array_filter( array_map( 'trim', explode( "\n", $opts['block_patterns'] ) ) );

        if ( empty( $patterns ) ) {
            return $tag;
        }

        // Marketing patterns
        $marketing_patterns = [
            'facebook.net', 'fbevents', 'linkedin.com/insight', 'pinterest.com/ct',
            'tiktok.com', 'snapchat.com', 'doubleclick.net', 'googlesyndication',
            'googleadservices', 'adsbygoogle',
        ];

        foreach ( $patterns as $pattern ) {
            if ( stripos( $tag, $pattern ) !== false ) {
                // Determine category
                $category = 'analytics'; // default
                foreach ( $marketing_patterns as $mp ) {
                    if ( stripos( $pattern, $mp ) !== false ) {
                        $category = 'marketing';
                        break;
                    }
                }

                // Check if this specific category is consented
                if ( isset( $_COOKIE['olo_cc'] ) ) {
                    $consent = json_decode( wp_unslash( $_COOKIE['olo_cc'] ), true );
                    if ( json_last_error() !== JSON_ERROR_NONE ) { $consent = []; }
                    if ( is_array( $consent ) ) {
                        if ( ! empty( $consent[ $category ] ) ) {
                            return $tag; // Already consented for this category
                        }
                    }
                }

                // Block: change type to text/plain
                $tag = str_replace( "type='text/javascript'", "type='text/plain'", $tag );
                $tag = str_replace( 'type="text/javascript"', 'type="text/plain"', $tag );
                // If no type attribute, add one
                if ( ! str_contains( $tag, 'type=' ) ) {
                    $tag = str_replace( '<script ', '<script type="text/plain" ', $tag );
                }
                // Add category attribute
                $tag = str_replace( '<script ', '<script data-cookiecategory="' . esc_attr( $category ) . '" ', $tag );
                break;
            }
        }

        return $tag;
    }

    /* ═══════════════════════════════════════════════════
     * SHORTCODE
     * ═══════════════════════════════════════════════════ */

    /**
     * [olo_cookie_settings] — renders a link/button to re-open the cookie preferences modal.
     *
     * @param array $atts Attributes: text, class, tag (a|button).
     */
    public function shortcode_open_preferences( $atts ) {
        $atts = shortcode_atts( [
            'text'  => __( 'Gestisci cookie', 'olobuild' ),
            'class' => '',
            'tag'   => 'button',
        ], $atts );

        $tag   = in_array( $atts['tag'], [ 'a', 'button', 'span' ], true ) ? $atts['tag'] : 'button';
        $class = 'olo-cc-reopen' . ( $atts['class'] ? ' ' . esc_attr( $atts['class'] ) : '' );

        return '<' . $tag . ' class="' . $class . '">' . esc_html( $atts['text'] ) . '</' . $tag . '>';
    }
}
