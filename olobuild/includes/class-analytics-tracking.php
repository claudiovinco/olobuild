<?php
/**
 * Olo_Analytics_Tracking — GA4, Facebook Pixel, GTM, Microsoft Clarity, Hotjar.
 *
 * Pagina admin + output script con integrazione Cookie Consent.
 * Gli script di tracking vengono bloccati se il Cookie Consent è attivo
 * e l'utente non ha accettato la categoria corrispondente.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Analytics_Tracking {

    const OPT = 'olo_analytics';

    public static function get_options() {
        $defaults = [
            // Provider IDs
            'ga_id'              => get_option( 'olo_ga_measurement_id', '' ),
            'fb_pixel_id'        => get_option( 'olo_fb_pixel_id', '' ),
            'gtm_id'             => get_option( 'olo_gtm_container_id', '' ),
            'clarity_id'         => '',
            'hotjar_id'          => '',
            // Features
            'track_buttons'      => true,
            'track_forms'        => true,
            'track_video'        => true,
            'track_scroll'       => true,
            'track_pricing'      => true,
            'track_downloads'    => true,
            'track_outbound'     => true,
            // Settings
            'anonymize_ip'       => true,
            'respect_dnt'        => false,
            'exclude_admins'     => true,
            'scroll_milestones'  => '25,50,75,100',
            'download_extensions'=> 'pdf,zip,doc,docx,xls,xlsx,ppt,pptx,csv',
            // Cookie consent integration
            'consent_required'   => true,
            // Custom scripts
            'head_scripts'       => '',
            'body_scripts'       => '',
        ];

        $saved = get_option( self::OPT, [] );
        if ( ! is_array( $saved ) ) {
            $saved = [];
        }

        return wp_parse_args( $saved, $defaults );
    }

    public static function init() {
        // Admin page
        add_action( 'admin_menu', [ __CLASS__, 'add_menu' ] );
        add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_admin' ] );

        // Frontend
        add_action( 'wp_head', [ __CLASS__, 'output_tracking_scripts' ], 1 );
        add_action( 'wp_footer', [ __CLASS__, 'output_event_listeners' ], 99 );

        // GTM body tag
        add_action( 'wp_body_open', [ __CLASS__, 'output_gtm_noscript' ], 1 );
    }

    /* ═══════════════════════════════════════════════════
     * ADMIN
     * ═══════════════════════════════════════════════════ */

    public static function add_menu() {
        add_submenu_page(
            'olobuilder',
            __( 'Analytics', 'olobuilder' ),
            __( 'Analytics', 'olobuilder' ),
            'manage_options',
            'olo-analytics',
            [ __CLASS__, 'render_page' ]
        );
    }

    public static function register_settings() {
        register_setting( 'olo_analytics_group', self::OPT, [
            'sanitize_callback' => [ __CLASS__, 'sanitize' ],
        ] );
    }

    public static function enqueue_admin( $hook ) {
        if ( strpos( $hook, 'olo-analytics' ) === false ) {
            return;
        }
        wp_enqueue_style( 'olo-analytics-admin', OLO_URL . 'assets/css/analytics-admin.css', [], OLO_VERSION );
    }

    public static function sanitize( $input ) {
        $clean = [];

        // IDs (strip HTML, trim)
        $ids = [ 'ga_id', 'fb_pixel_id', 'gtm_id', 'clarity_id', 'hotjar_id' ];
        foreach ( $ids as $k ) {
            $clean[ $k ] = sanitize_text_field( trim( $input[ $k ] ?? '' ) );
        }

        // Booleans
        $bools = [
            'track_buttons', 'track_forms', 'track_video', 'track_scroll',
            'track_pricing', 'track_downloads', 'track_outbound',
            'anonymize_ip', 'respect_dnt', 'exclude_admins', 'consent_required',
        ];
        foreach ( $bools as $k ) {
            $clean[ $k ] = ! empty( $input[ $k ] );
        }

        // Strings
        $clean['scroll_milestones']   = sanitize_text_field( $input['scroll_milestones'] ?? '25,50,75,100' );
        $clean['download_extensions'] = sanitize_text_field( $input['download_extensions'] ?? '' );

        // Custom scripts (allow HTML/JS)
        $clean['head_scripts'] = wp_unslash( $input['head_scripts'] ?? '' );
        $clean['body_scripts'] = wp_unslash( $input['body_scripts'] ?? '' );

        // Sync legacy options for REST API compatibility
        update_option( 'olo_ga_measurement_id', $clean['ga_id'], false );
        update_option( 'olo_fb_pixel_id', $clean['fb_pixel_id'], false );
        update_option( 'olo_gtm_container_id', $clean['gtm_id'], false );

        return $clean;
    }

    /* ─── Render Page ─── */

    public static function render_page() {
        $opts = self::get_options();
        $tab  = sanitize_key( $_GET['tab'] ?? 'providers' );
        $tabs = [
            'providers' => __( 'Provider', 'olobuilder' ),
            'events'    => __( 'Eventi', 'olobuilder' ),
            'settings'  => __( 'Impostazioni', 'olobuilder' ),
            'scripts'   => __( 'Script personalizzati', 'olobuilder' ),
        ];
        $n = self::OPT;
        ?>
        <div class="wrap olo-an-wrap">
            <h1>
                <span class="dashicons dashicons-chart-bar" style="margin-right:8px;color:#2271b1"></span>
                <?php esc_html_e( 'Analytics & Tracking', 'olobuilder' ); ?>
            </h1>

            <nav class="nav-tab-wrapper olo-an-tabs">
                <?php foreach ( $tabs as $slug => $label ) : ?>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=olo-analytics&tab=' . $slug ) ); ?>"
                       class="nav-tab <?php echo $tab === $slug ? 'nav-tab-active' : ''; ?>">
                        <?php echo esc_html( $label ); ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <form method="post" action="options.php" class="olo-an-form">
                <?php settings_fields( 'olo_analytics_group' ); ?>
                <?php self::render_hidden_fields( $opts, $tab ); ?>

                <?php
                switch ( $tab ) {
                    case 'providers': self::render_tab_providers( $opts ); break;
                    case 'events':    self::render_tab_events( $opts ); break;
                    case 'settings':  self::render_tab_settings( $opts ); break;
                    case 'scripts':   self::render_tab_scripts( $opts ); break;
                }
                ?>

                <?php submit_button( __( 'Salva impostazioni', 'olobuilder' ) ); ?>
            </form>
        </div>
        <?php
    }

    private static function render_hidden_fields( $opts, $current_tab ) {
        $n = self::OPT;
        $tab_fields = [
            'providers' => [ 'ga_id', 'fb_pixel_id', 'gtm_id', 'clarity_id', 'hotjar_id' ],
            'events'    => [ 'track_buttons', 'track_forms', 'track_video', 'track_scroll', 'track_pricing', 'track_downloads', 'track_outbound', 'scroll_milestones', 'download_extensions' ],
            'settings'  => [ 'anonymize_ip', 'respect_dnt', 'exclude_admins', 'consent_required' ],
            'scripts'   => [ 'head_scripts', 'body_scripts' ],
        ];

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
    }

    /* ─── Tab: Providers ─── */

    private static function render_tab_providers( $opts ) {
        $n = self::OPT;
        $providers = [
            [
                'key'   => 'ga_id',
                'label' => 'Google Analytics 4',
                'icon'  => 'chart-area',
                'placeholder' => 'G-XXXXXXXXXX',
                'desc'  => __( 'Measurement ID di GA4. Lo trovi in Amministrazione → Flussi di dati.', 'olobuilder' ),
            ],
            [
                'key'   => 'gtm_id',
                'label' => 'Google Tag Manager',
                'icon'  => 'tag',
                'placeholder' => 'GTM-XXXXXXX',
                'desc'  => __( 'Container ID di GTM. Se usi GTM per gestire GA4, non inserire anche il Measurement ID sopra.', 'olobuilder' ),
            ],
            [
                'key'   => 'fb_pixel_id',
                'label' => 'Facebook Pixel',
                'icon'  => 'share',
                'placeholder' => '123456789012345',
                'desc'  => __( 'Pixel ID di Meta/Facebook. Lo trovi in Events Manager → Impostazioni.', 'olobuilder' ),
            ],
            [
                'key'   => 'clarity_id',
                'label' => 'Microsoft Clarity',
                'icon'  => 'visibility',
                'placeholder' => 'xxxxxxxxxx',
                'desc'  => __( 'Project ID di Clarity. Heatmap e session recording gratuiti.', 'olobuilder' ),
            ],
            [
                'key'   => 'hotjar_id',
                'label' => 'Hotjar',
                'icon'  => 'admin-generic',
                'placeholder' => '1234567',
                'desc'  => __( 'Site ID di Hotjar. Lo trovi in Settings → Sites & Organizations.', 'olobuilder' ),
            ],
        ];
        ?>
        <div class="olo-an-section">
            <h2><?php esc_html_e( 'Provider di tracking', 'olobuilder' ); ?></h2>
            <p class="description"><?php esc_html_e( 'Inserisci gli ID dei servizi di analytics. Lascia vuoto per disattivare.', 'olobuilder' ); ?></p>

            <?php foreach ( $providers as $p ) : ?>
            <div class="olo-an-provider-card">
                <div class="olo-an-provider-header">
                    <span class="dashicons dashicons-<?php echo esc_attr( $p['icon'] ); ?>"></span>
                    <strong><?php echo esc_html( $p['label'] ); ?></strong>
                    <?php if ( ! empty( $opts[ $p['key'] ] ) ) : ?>
                        <span class="olo-an-badge-active"><?php esc_html_e( 'Attivo', 'olobuilder' ); ?></span>
                    <?php endif; ?>
                </div>
                <input type="text" name="<?php echo $n; ?>[<?php echo esc_attr( $p['key'] ); ?>]"
                       value="<?php echo esc_attr( $opts[ $p['key'] ] ); ?>"
                       placeholder="<?php echo esc_attr( $p['placeholder'] ); ?>"
                       class="regular-text" style="margin-top:8px" />
                <p class="description"><?php echo $p['desc']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

    /* ─── Tab: Events ─── */

    private static function render_tab_events( $opts ) {
        $n = self::OPT;
        $events = [
            [ 'key' => 'track_buttons',   'label' => __( 'Click sui pulsanti', 'olobuilder' ),           'desc' => 'olo_button_click — elementi con data-olo-track="button"' ],
            [ 'key' => 'track_forms',     'label' => __( 'Invio form', 'olobuilder' ),                   'desc' => 'olo_form_submit — form con data-olo-track="form"' ],
            [ 'key' => 'track_video',     'label' => __( 'Riproduzione video', 'olobuilder' ),           'desc' => 'olo_video_play — evento "play" su tag <video>' ],
            [ 'key' => 'track_scroll',    'label' => __( 'Profondità di scroll', 'olobuilder' ),         'desc' => 'olo_scroll_depth — milestone configurabili' ],
            [ 'key' => 'track_pricing',   'label' => __( 'Click su pricing CTA', 'olobuilder' ),        'desc' => 'olo_pricing_click — elementi con data-olo-track="pricing"' ],
            [ 'key' => 'track_downloads', 'label' => __( 'Download file', 'olobuilder' ),               'desc' => 'olo_file_download — click su link con estensioni configurate' ],
            [ 'key' => 'track_outbound',  'label' => __( 'Link esterni', 'olobuilder' ),                'desc' => 'olo_outbound_click — click su link verso domini diversi' ],
        ];
        ?>
        <div class="olo-an-section">
            <h2><?php esc_html_e( 'Auto-tracking eventi', 'olobuilder' ); ?></h2>
            <p class="description"><?php esc_html_e( 'Scegli quali eventi tracciare automaticamente. Vengono inviati a tutti i provider configurati.', 'olobuilder' ); ?></p>

            <table class="form-table olo-an-table">
                <?php foreach ( $events as $ev ) : ?>
                <tr>
                    <th><?php echo esc_html( $ev['label'] ); ?></th>
                    <td>
                        <label class="olo-an-toggle">
                            <input type="checkbox" name="<?php echo $n; ?>[<?php echo esc_attr( $ev['key'] ); ?>]" value="1" <?php checked( $opts[ $ev['key'] ] ); ?> />
                            <span class="olo-an-toggle-slider"></span>
                        </label>
                        <p class="description"><code><?php echo esc_html( $ev['desc'] ); ?></code></p>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <th><?php esc_html_e( 'Milestone scroll (%)', 'olobuilder' ); ?></th>
                    <td>
                        <input type="text" name="<?php echo $n; ?>[scroll_milestones]" value="<?php echo esc_attr( $opts['scroll_milestones'] ); ?>" class="regular-text" placeholder="25,50,75,100" />
                        <p class="description"><?php esc_html_e( 'Percentuali di scroll a cui inviare un evento (separate da virgola).', 'olobuilder' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Estensioni download', 'olobuilder' ); ?></th>
                    <td>
                        <input type="text" name="<?php echo $n; ?>[download_extensions]" value="<?php echo esc_attr( $opts['download_extensions'] ); ?>" class="regular-text" placeholder="pdf,zip,doc" />
                        <p class="description"><?php esc_html_e( 'Estensioni file da tracciare come download (separate da virgola).', 'olobuilder' ); ?></p>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }

    /* ─── Tab: Settings ─── */

    private static function render_tab_settings( $opts ) {
        $n = self::OPT;
        ?>
        <div class="olo-an-section">
            <h2><?php esc_html_e( 'Privacy e conformità', 'olobuilder' ); ?></h2>
            <table class="form-table olo-an-table">
                <tr>
                    <th><?php esc_html_e( 'Richiedi consenso cookie', 'olobuilder' ); ?></th>
                    <td>
                        <label class="olo-an-toggle">
                            <input type="checkbox" name="<?php echo $n; ?>[consent_required]" value="1" <?php checked( $opts['consent_required'] ); ?> />
                            <span class="olo-an-toggle-slider"></span>
                        </label>
                        <p class="description"><?php esc_html_e( 'Blocca gli script di tracking finché l\'utente non accetta i cookie analitici/marketing dal banner Cookie Consent. Richiesto per GDPR.', 'olobuilder' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Anonimizza IP', 'olobuilder' ); ?></th>
                    <td>
                        <label class="olo-an-toggle">
                            <input type="checkbox" name="<?php echo $n; ?>[anonymize_ip]" value="1" <?php checked( $opts['anonymize_ip'] ); ?> />
                            <span class="olo-an-toggle-slider"></span>
                        </label>
                        <p class="description"><?php esc_html_e( 'GA4 anonimizza l\'IP di default, ma questa opzione aggiunge il parametro esplicito per conformità.', 'olobuilder' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Rispetta Do Not Track', 'olobuilder' ); ?></th>
                    <td>
                        <label class="olo-an-toggle">
                            <input type="checkbox" name="<?php echo $n; ?>[respect_dnt]" value="1" <?php checked( $opts['respect_dnt'] ); ?> />
                            <span class="olo-an-toggle-slider"></span>
                        </label>
                        <p class="description"><?php esc_html_e( 'Non caricare gli script se il browser invia l\'header Do Not Track.', 'olobuilder' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Escludi amministratori', 'olobuilder' ); ?></th>
                    <td>
                        <label class="olo-an-toggle">
                            <input type="checkbox" name="<?php echo $n; ?>[exclude_admins]" value="1" <?php checked( $opts['exclude_admins'] ); ?> />
                            <span class="olo-an-toggle-slider"></span>
                        </label>
                        <p class="description"><?php esc_html_e( 'Non tracciare le visite degli utenti con ruolo Amministratore.', 'olobuilder' ); ?></p>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }

    /* ─── Tab: Custom Scripts ─── */

    private static function render_tab_scripts( $opts ) {
        $n = self::OPT;
        ?>
        <div class="olo-an-section">
            <h2><?php esc_html_e( 'Script personalizzati', 'olobuilder' ); ?></h2>
            <p class="description"><?php esc_html_e( 'Inserisci script di tracking aggiuntivi. Verranno bloccati dal consenso cookie se abilitato.', 'olobuilder' ); ?></p>

            <table class="form-table olo-an-table">
                <tr>
                    <th><?php esc_html_e( 'Script nel <head>', 'olobuilder' ); ?></th>
                    <td>
                        <textarea name="<?php echo $n; ?>[head_scripts]" rows="6" class="large-text code" placeholder="&lt;script&gt;...&lt;/script&gt;"><?php echo esc_textarea( $opts['head_scripts'] ); ?></textarea>
                        <p class="description"><?php esc_html_e( 'Inseriti prima della chiusura di </head>. Utile per snippet di tracking non supportati nativamente.', 'olobuilder' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Script nel <body>', 'olobuilder' ); ?></th>
                    <td>
                        <textarea name="<?php echo $n; ?>[body_scripts]" rows="6" class="large-text code" placeholder="&lt;script&gt;...&lt;/script&gt;"><?php echo esc_textarea( $opts['body_scripts'] ); ?></textarea>
                        <p class="description"><?php esc_html_e( 'Inseriti prima della chiusura di </body>.', 'olobuilder' ); ?></p>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }

    /* ═══════════════════════════════════════════════════
     * FRONTEND OUTPUT
     * ═══════════════════════════════════════════════════ */

    /**
     * Should we skip tracking for this request?
     */
    private static function should_skip() {
        if ( is_admin() ) {
            return true;
        }

        $opts = self::get_options();

        // Exclude admins
        if ( ! empty( $opts['exclude_admins'] ) ) {
            if ( current_user_can( 'manage_options' ) ) {
                return true;
            }
        }

        // Respect DNT
        if ( ! empty( $opts['respect_dnt'] ) ) {
            if ( isset( $_SERVER['HTTP_DNT'] ) ) {
                if ( $_SERVER['HTTP_DNT'] === '1' ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if consent is required and if the user has given it.
     * Returns the script type to use: 'text/javascript' or 'text/plain'.
     */
    private static function get_script_type( $category = 'analytics' ) {
        $opts = self::get_options();

        if ( empty( $opts['consent_required'] ) ) {
            return 'text/javascript';
        }

        // Check if cookie consent system is active
        if ( ! class_exists( 'Olo_Cookie_Consent' ) ) {
            return 'text/javascript';
        }

        $cc_opts = Olo_Cookie_Consent::get_options();
        if ( empty( $cc_opts['enabled'] ) ) {
            return 'text/javascript';
        }

        // Check the consent cookie
        if ( isset( $_COOKIE['olo_cc'] ) ) {
            $consent = json_decode( urldecode( $_COOKIE['olo_cc'] ), true );
            if ( is_array( $consent ) ) {
                if ( ! empty( $consent[ $category ] ) ) {
                    return 'text/javascript';
                }
            }
        }

        // No consent: block scripts
        return 'text/plain';
    }

    /**
     * Output tracking provider scripts in <head>.
     * NOTA: NO && negli script inline — solo if annidati.
     */
    public static function output_tracking_scripts() {
        if ( self::should_skip() ) {
            return;
        }

        $opts = self::get_options();

        $ga_id  = $opts['ga_id'];
        $fb_id  = $opts['fb_pixel_id'];
        $gtm_id = $opts['gtm_id'];
        $clar   = $opts['clarity_id'];
        $hj_id  = $opts['hotjar_id'];

        $analytics_type = self::get_script_type( 'analytics' );
        $marketing_type = self::get_script_type( 'marketing' );

        // GA4 attribute for cookie consent auto-blocking
        $ga_cat  = $analytics_type === 'text/plain' ? ' data-cookiecategory="analytics"' : '';
        $mkt_cat = $marketing_type === 'text/plain' ? ' data-cookiecategory="marketing"' : '';

        // Google Analytics 4
        if ( ! empty( $ga_id ) ) {
            $ga_esc = esc_attr( $ga_id );
            echo '<script type="' . $analytics_type . '"' . $ga_cat . ' async src="https://www.googletagmanager.com/gtag/js?id=' . $ga_esc . '"></script>' . "\n";
            echo '<script type="' . $analytics_type . '"' . $ga_cat . '>' . "\n";
            echo 'window.dataLayer=window.dataLayer||[];' . "\n";
            echo 'function gtag(){dataLayer.push(arguments);}' . "\n";
            echo 'gtag("js",new Date());' . "\n";
            $config_params = '';
            if ( ! empty( $opts['anonymize_ip'] ) ) {
                $config_params = ',{anonymize_ip:true}';
            }
            echo 'gtag("config","' . $ga_esc . '"' . $config_params . ');' . "\n";
            echo '</script>' . "\n";
        }

        // Google Tag Manager
        if ( ! empty( $gtm_id ) ) {
            $gtm_esc = esc_attr( $gtm_id );
            echo '<script type="' . $analytics_type . '"' . $ga_cat . '>' . "\n";
            echo "(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':" . "\n";
            echo "new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0]," . "\n";
            echo "j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=" . "\n";
            echo "'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);" . "\n";
            echo "})(window,document,'script','dataLayer','" . $gtm_esc . "');" . "\n";
            echo '</script>' . "\n";
        }

        // Facebook Pixel
        if ( ! empty( $fb_id ) ) {
            $fb_esc = esc_attr( $fb_id );
            echo '<script type="' . $marketing_type . '"' . $mkt_cat . '>' . "\n";
            echo '!function(f,b,e,v,n,t,s){' . "\n";
            echo 'if(f.fbq)return;n=f.fbq=function(){n.callMethod?' . "\n";
            echo 'n.callMethod.apply(n,arguments):n.queue.push(arguments)};' . "\n";
            echo 'if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version="2.0";' . "\n";
            echo 'n.queue=[];t=b.createElement(e);t.async=!0;' . "\n";
            echo 't.src=v;s=b.getElementsByTagName(e)[0];' . "\n";
            echo 's.parentNode.insertBefore(t,s)}(window,document,"script",' . "\n";
            echo '"https://connect.facebook.net/en_US/fbevents.js");' . "\n";
            echo 'fbq("init","' . $fb_esc . '");' . "\n";
            echo 'fbq("track","PageView");' . "\n";
            echo '</script>' . "\n";
            if ( $marketing_type === 'text/javascript' ) {
                echo '<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=' . $fb_esc . '&ev=PageView&noscript=1"/></noscript>' . "\n";
            }
        }

        // Microsoft Clarity
        if ( ! empty( $clar ) ) {
            $clar_esc = esc_js( $clar );
            echo '<script type="' . $analytics_type . '"' . $ga_cat . '>' . "\n";
            echo "(function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};" . "\n";
            echo "t=l.createElement(r);t.async=1;t.src='https://www.clarity.ms/tag/'+i;" . "\n";
            echo "y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);" . "\n";
            echo "})(window,document,'clarity','script','" . $clar_esc . "');" . "\n";
            echo '</script>' . "\n";
        }

        // Hotjar
        if ( ! empty( $hj_id ) ) {
            $hj_esc = intval( $hj_id );
            echo '<script type="' . $analytics_type . '"' . $ga_cat . '>' . "\n";
            echo "(function(h,o,t,j,a,r){h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};" . "\n";
            echo "h._hjSettings={hjid:" . $hj_esc . ",hjsv:6};a=o.getElementsByTagName('head')[0];" . "\n";
            echo "r=o.createElement('script');r.async=1;r.src=t+h._hjSettings.hjid+j;" . "\n";
            echo "a.appendChild(r)})(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=6');" . "\n";
            echo '</script>' . "\n";
        }

        // Custom head scripts
        if ( ! empty( $opts['head_scripts'] ) ) {
            echo $opts['head_scripts'] . "\n";
        }
    }

    /**
     * GTM noscript fallback in <body>.
     */
    public static function output_gtm_noscript() {
        if ( self::should_skip() ) {
            return;
        }
        $opts   = self::get_options();
        $gtm_id = $opts['gtm_id'];
        if ( empty( $gtm_id ) ) {
            return;
        }
        echo '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . esc_attr( $gtm_id ) . '" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>' . "\n";
    }

    /**
     * Output event listener script in footer.
     * NOTA: NO && — solo if annidati.
     */
    public static function output_event_listeners() {
        if ( self::should_skip() ) {
            return;
        }

        $opts = self::get_options();

        // Check if at least one provider is configured
        $has_provider = ! empty( $opts['ga_id'] ) || ! empty( $opts['fb_pixel_id'] ) || ! empty( $opts['gtm_id'] ) || ! empty( $opts['clarity_id'] ) || ! empty( $opts['hotjar_id'] );
        if ( ! $has_provider ) {
            // Custom body scripts only
            if ( ! empty( $opts['body_scripts'] ) ) {
                echo $opts['body_scripts'] . "\n";
            }
            return;
        }

        // Check if any tracking events are enabled
        $has_events = $opts['track_buttons'] || $opts['track_forms'] || $opts['track_video'] || $opts['track_scroll'] || $opts['track_pricing'] || $opts['track_downloads'] || $opts['track_outbound'];

        if ( ! $has_events ) {
            if ( ! empty( $opts['body_scripts'] ) ) {
                echo $opts['body_scripts'] . "\n";
            }
            return;
        }

        $milestones = array_map( 'intval', array_filter( explode( ',', $opts['scroll_milestones'] ) ) );
        $extensions = array_map( 'trim', array_filter( explode( ',', $opts['download_extensions'] ) ) );

        $script_type = self::get_script_type( 'analytics' );
        $sc_attr     = $script_type === 'text/plain' ? ' data-cookiecategory="analytics"' : '';

        ?>
<script type="<?php echo $script_type; ?>"<?php echo $sc_attr; ?>>
(function(){
    "use strict";

    function oloTrack(eventName, params) {
        params = params || {};
        if (typeof gtag === "function") {
            gtag("event", eventName, params);
        }
        if (typeof fbq === "function") {
            fbq("trackCustom", eventName, params);
        }
        if (window.dataLayer) {
            var obj = {event: eventName};
            for (var k in params) {
                if (params.hasOwnProperty(k)) {
                    obj[k] = params[k];
                }
            }
            window.dataLayer.push(obj);
        }
    }

<?php if ( $opts['track_buttons'] ) : ?>
    document.addEventListener("click", function(e) {
        var btn = e.target.closest("[data-olo-track='button']");
        if (btn) {
            var label = btn.getAttribute("data-olo-track-label") || btn.textContent.trim().substring(0, 50);
            oloTrack("olo_button_click", {button_label: label});
        }
    });
<?php endif; ?>

<?php if ( $opts['track_forms'] ) : ?>
    document.addEventListener("submit", function(e) {
        var form = e.target.closest("[data-olo-track='form']");
        if (form) {
            var formName = form.getAttribute("data-olo-track-label") || form.getAttribute("name") || "unknown";
            oloTrack("olo_form_submit", {form_name: formName});
        }
    });
<?php endif; ?>

<?php if ( $opts['track_video'] ) : ?>
    document.addEventListener("play", function(e) {
        if (e.target.tagName === "VIDEO") {
            var videoSrc = e.target.currentSrc || "";
            oloTrack("olo_video_play", {video_src: videoSrc.substring(0, 200)});
        }
    }, true);
<?php endif; ?>

<?php if ( $opts['track_scroll'] ) : ?>
    var scrollMilestones = {};
    var milestoneValues = <?php echo wp_json_encode( $milestones ); ?>;

    function checkScrollDepth() {
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        var docHeight = Math.max(document.body.scrollHeight, document.documentElement.scrollHeight) - window.innerHeight;
        if (docHeight <= 0) return;
        var percent = Math.round((scrollTop / docHeight) * 100);
        for (var i = 0; i < milestoneValues.length; i++) {
            var ms = milestoneValues[i];
            if (percent >= ms) {
                if (!scrollMilestones[ms]) {
                    scrollMilestones[ms] = true;
                    oloTrack("olo_scroll_depth", {depth: ms});
                }
            }
        }
    }

    var scrollTimer = null;
    window.addEventListener("scroll", function() {
        if (scrollTimer) return;
        scrollTimer = setTimeout(function() {
            scrollTimer = null;
            checkScrollDepth();
        }, 250);
    }, {passive: true});
<?php endif; ?>

<?php if ( $opts['track_pricing'] ) : ?>
    document.addEventListener("click", function(e) {
        var cta = e.target.closest("[data-olo-track='pricing']");
        if (cta) {
            var label = cta.getAttribute("data-olo-track-label") || cta.textContent.trim().substring(0, 50);
            oloTrack("olo_pricing_click", {pricing_label: label});
        }
    });
<?php endif; ?>

<?php if ( $opts['track_downloads'] ) : ?>
    var dlExts = <?php echo wp_json_encode( $extensions ); ?>;
    document.addEventListener("click", function(e) {
        var a = e.target.closest("a[href]");
        if (!a) return;
        var href = a.getAttribute("href") || "";
        for (var i = 0; i < dlExts.length; i++) {
            if (href.toLowerCase().indexOf("." + dlExts[i]) !== -1) {
                oloTrack("olo_file_download", {file_url: href.substring(0, 200), file_type: dlExts[i]});
                break;
            }
        }
    });
<?php endif; ?>

<?php if ( $opts['track_outbound'] ) : ?>
    document.addEventListener("click", function(e) {
        var a = e.target.closest("a[href]");
        if (!a) return;
        var href = a.getAttribute("href") || "";
        if (href.indexOf("http") === 0) {
            if (href.indexOf(location.hostname) === -1) {
                oloTrack("olo_outbound_click", {outbound_url: href.substring(0, 200)});
            }
        }
    });
<?php endif; ?>

    /* Generic tracked elements */
    document.addEventListener("click", function(e) {
        var el = e.target.closest("[data-olo-track]");
        if (el) {
            var trackType = el.getAttribute("data-olo-track");
            if (trackType === "button") return;
            if (trackType === "form") return;
            if (trackType === "pricing") return;
            var label = el.getAttribute("data-olo-track-label") || el.textContent.trim().substring(0, 50);
            oloTrack("olo_track_" + trackType, {label: label});
        }
    });

})();
</script>
        <?php

        // Custom body scripts
        if ( ! empty( $opts['body_scripts'] ) ) {
            echo $opts['body_scripts'] . "\n";
        }
    }
}
