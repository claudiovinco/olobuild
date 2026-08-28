<?php
/**
 * Olobuild_Builder_Settings_Trait — pagina Configurazione: settings WP + rotte REST settings.
 *
 * Estratto verbatim da class-olo-builder.php (dieta monoliti v1.4.390):
 * stessi metodi, stessa visibilita', zero cambi alle chiamate ($this/self
 * risolvono nella classe che usa il trait).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

trait Olobuild_Builder_Settings_Trait {
    /**
     * Registra le impostazioni API keys nella WP Settings API.
     */
    public function register_settings() {
        // Sezione API Keys
        add_settings_section(
            'olo_api_keys_section',
            __( 'API Keys — Stock Media', 'olobuild' ),
            function () {
                echo '<p>' . esc_html__( 'Inserisci le chiavi API per i servizi di immagini/video stock. Se lasci un campo vuoto, verrà usata la chiave predefinita.', 'olobuild' ) . '</p>';
            },
            'olobuilder-settings'
        );

        // Pexels
        register_setting( 'olobuild_settings_group', 'olobuild_pexels_api_key', [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        ] );
        add_settings_field(
            'olobuild_pexels_api_key',
            __( 'Pexels API Key', 'olobuild' ),
            function () {
                $val = get_option( 'olobuild_pexels_api_key', '' );
                $via_const = defined( 'OLO_PEXELS_API_KEY' ) && OLO_PEXELS_API_KEY;
                echo '<input type="text" name="olobuild_pexels_api_key" value="' . esc_attr( $val ) . '" class="regular-text"' . ( $via_const ? ' placeholder="' . esc_attr__( 'Definita in wp-config.php (OLO_PEXELS_API_KEY)', 'olobuild' ) . '" disabled' : '' ) . ' />';
                echo '<p class="description">' . sprintf(
                    /* translators: %s = URL */
                    esc_html__( 'Ottieni una chiave su %s', 'olobuild' ),
                    '<a href="https://www.pexels.com/api/" target="_blank" rel="noopener">pexels.com/api</a>'
                ) . '</p>';
            },
            'olobuilder-settings',
            'olo_api_keys_section'
        );

        // Pixabay
        register_setting( 'olobuild_settings_group', 'olobuild_pixabay_api_key', [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        ] );
        add_settings_field(
            'olobuild_pixabay_api_key',
            __( 'Pixabay API Key', 'olobuild' ),
            function () {
                $val = get_option( 'olobuild_pixabay_api_key', '' );
                $via_const = defined( 'OLO_PIXABAY_API_KEY' ) && OLO_PIXABAY_API_KEY;
                echo '<input type="text" name="olobuild_pixabay_api_key" value="' . esc_attr( $val ) . '" class="regular-text"' . ( $via_const ? ' placeholder="' . esc_attr__( 'Definita in wp-config.php (OLO_PIXABAY_API_KEY)', 'olobuild' ) . '" disabled' : '' ) . ' />';
                echo '<p class="description">' . sprintf(
                    /* translators: %s: link to the Pixabay API documentation */
                    esc_html__( 'Ottieni una chiave su %s', 'olobuild' ),
                    '<a href="https://pixabay.com/api/docs/" target="_blank" rel="noopener">pixabay.com/api/docs</a>'
                ) . '</p>';
            },
            'olobuilder-settings',
            'olo_api_keys_section'
        );

        // Unsplash
        register_setting( 'olobuild_settings_group', 'olobuild_unsplash_api_key', [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        ] );
        add_settings_field(
            'olobuild_unsplash_api_key',
            __( 'Unsplash Access Key', 'olobuild' ),
            function () {
                $val = get_option( 'olobuild_unsplash_api_key', '' );
                $via_const = defined( 'OLO_UNSPLASH_API_KEY' ) && OLO_UNSPLASH_API_KEY;
                echo '<input type="text" name="olobuild_unsplash_api_key" value="' . esc_attr( $val ) . '" class="regular-text"' . ( $via_const ? ' placeholder="' . esc_attr__( 'Definita in wp-config.php (OLO_UNSPLASH_API_KEY)', 'olobuild' ) . '" disabled' : '' ) . ' />';
                echo '<p class="description">' . sprintf(
                    /* translators: %s: link to the Unsplash developers page */
                    esc_html__( 'Ottieni una chiave su %s', 'olobuild' ),
                    '<a href="https://unsplash.com/developers" target="_blank" rel="noopener">unsplash.com/developers</a>'
                ) . '</p>';
            },
            'olobuilder-settings',
            'olo_api_keys_section'
        );

        // Freesound
        register_setting( 'olobuild_settings_group', 'olobuild_freesound_api_key', [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        ] );
        add_settings_field(
            'olobuild_freesound_api_key',
            __( 'Freesound API Key', 'olobuild' ),
            function () {
                $val = get_option( 'olobuild_freesound_api_key', '' );
                echo '<input type="text" name="olobuild_freesound_api_key" value="' . esc_attr( $val ) . '" class="regular-text" placeholder="xbKm7Gp3..." />';
                echo '<p class="description">' . sprintf(
                    /* translators: %s: link to the Freesound API page */
                    esc_html__( 'Ottieni una chiave su %s', 'olobuild' ),
                    '<a href="https://freesound.org/apiv2/apply" target="_blank" rel="noopener">freesound.org/apiv2/apply</a>'
                ) . '</p>';
            },
            'olobuilder-settings',
            'olo_api_keys_section'
        );

        // ── Sezione reCAPTCHA v3 ──
        add_settings_section(
            'olo_recaptcha_section',
            __( 'reCAPTCHA v3', 'olobuild' ),
            function () {
                echo '<p>' . esc_html__( 'Google reCAPTCHA v3 per la protezione dei form. Ottieni le chiavi su google.com/recaptcha', 'olobuild' ) . '</p>';
            },
            'olobuilder-settings'
        );

        register_setting( 'olobuild_settings_group', 'olobuild_recaptcha_site_key', [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        ] );
        add_settings_field(
            'olobuild_recaptcha_site_key',
            __( 'Site Key', 'olobuild' ),
            function () {
                $val = get_option( 'olobuild_recaptcha_site_key', '' );
                echo '<input type="text" name="olobuild_recaptcha_site_key" value="' . esc_attr( $val ) . '" class="regular-text" />';
            },
            'olobuilder-settings',
            'olo_recaptcha_section'
        );

        register_setting( 'olobuild_settings_group', 'olobuild_recaptcha_secret_key', [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        ] );
        add_settings_field(
            'olobuild_recaptcha_secret_key',
            __( 'Secret Key', 'olobuild' ),
            function () {
                $val = get_option( 'olobuild_recaptcha_secret_key', '' );
                echo '<input type="password" name="olobuild_recaptcha_secret_key" value="' . esc_attr( $val ) . '" class="regular-text" />';
            },
            'olobuilder-settings',
            'olo_recaptcha_section'
        );

        // ── Sezione Mailchimp ──
        add_settings_section(
            'olo_mailchimp_section',
            __( 'Mailchimp', 'olobuild' ),
            function () {
                echo '<p>' . esc_html__( 'API key Mailchimp per le integrazioni dei form contatti.', 'olobuild' ) . '</p>';
            },
            'olobuilder-settings'
        );

        register_setting( 'olobuild_settings_group', 'olobuild_mailchimp_api_key', [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        ] );
        add_settings_field(
            'olobuild_mailchimp_api_key',
            __( 'Mailchimp API Key', 'olobuild' ),
            function () {
                $val = get_option( 'olobuild_mailchimp_api_key', '' );
                echo '<input type="text" name="olobuild_mailchimp_api_key" value="' . esc_attr( $val ) . '" class="regular-text" placeholder="xxxxxxxxxxxxxxxx-us1" />';
                echo '<p class="description">' . sprintf(
                    /* translators: %s: link to the Mailchimp help page */
                    esc_html__( 'Ottieni una chiave su %s', 'olobuild' ),
                    '<a href="https://mailchimp.com/help/about-api-keys/" target="_blank" rel="noopener">mailchimp.com</a>'
                ) . '</p>';
            },
            'olobuilder-settings',
            'olo_mailchimp_section'
        );
    }

    /**
     * Renderizza la pagina Configurazione Olobuild (Vue app con tab).
     */
    public function render_configurazione_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        // v1.0.30 — redesign: la pagina ora occupa l'intera area #wpbody con shell propria
        // (topbar + sidebar console navy + savebar). Niente più cockpit chrome — Vue gestisce tutto.
        // .olo-cfg-wrap toglie il margin/padding di default di .wrap WP.
        ?>
        <div class="wrap olo-cfg-wrap">
            <h1 class="screen-reader-text"><?php esc_html_e( 'Configurazione Olobuild', 'olobuild' ); ?></h1>
            <div id="olo-admin-settings"></div>
        </div>
        <?php
    }

    /**
     * REST endpoints per la pagina Configurazione.
     */
    public function register_settings_rest_routes() {
        $ns = 'olobuild/v1';

        // API Keys
        register_rest_route( $ns, '/settings/api-keys', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'rest_get_api_keys' ],
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'rest_put_api_keys' ],
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // Breakpoints
        register_rest_route( $ns, '/settings/breakpoints', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'rest_get_breakpoints' ],
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [ $this, 'rest_put_breakpoints' ],
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // Palette colori (ColorsTab in Configurazione): brand palette + scala neutri + dark
        register_rest_route( $ns, '/settings/global-colors', [
            [
                'methods'             => 'GET',
                'callback'            => function () {
                    return rest_ensure_response( [
                        'palette'      => get_option( 'olobuild_palette', [] ),
                        'neutrals'     => get_option( 'olobuild_neutrals', [] ),
                        'neutral_mode' => get_option( 'olobuild_neutral_mode', 'auto' ),
                        'neutral_tint' => get_option( 'olobuild_neutral_tint', 'zinc' ),
                        'dark'         => get_option( 'olobuild_dark_settings', [ 'enabled' => true, 'strategy' => 'auto' ] ),
                    ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'PUT',
                'callback'            => function ( $req ) {
                    $b = $req->get_json_params();
                    if ( ! is_array( $b ) ) $b = [];

                    if ( isset( $b['palette'] ) && is_array( $b['palette'] ) ) {
                        $clean = [];
                        foreach ( $b['palette'] as $p ) {
                            if ( ! is_array( $p ) ) continue;
                            $hex = sanitize_hex_color( $p['hex'] ?? '' );
                            $clean[] = [
                                'id'   => sanitize_key( $p['id'] ?? '' ),
                                'name' => sanitize_text_field( $p['name'] ?? '' ),
                                'role' => sanitize_text_field( $p['role'] ?? '' ),
                                'hex'  => $hex ?: '#000000',
                            ];
                        }
                        update_option( 'olobuild_palette', $clean );
                    }

                    if ( isset( $b['neutrals'] ) && is_array( $b['neutrals'] ) ) {
                        $clean = array_values( array_filter( array_map( 'sanitize_hex_color', $b['neutrals'] ) ) );
                        update_option( 'olobuild_neutrals', $clean );
                    }

                    if ( isset( $b['neutral_mode'] ) ) {
                        $m = $b['neutral_mode'] === 'manual' ? 'manual' : 'auto';
                        update_option( 'olobuild_neutral_mode', $m );
                    }

                    if ( isset( $b['neutral_tint'] ) ) {
                        $allowed = [ 'slate', 'gray', 'zinc', 'neutral', 'stone' ];
                        $tint = in_array( $b['neutral_tint'], $allowed, true ) ? $b['neutral_tint'] : 'zinc';
                        update_option( 'olobuild_neutral_tint', $tint );
                    }

                    if ( isset( $b['dark'] ) && is_array( $b['dark'] ) ) {
                        $strategy = in_array( $b['dark']['strategy'] ?? '', [ 'auto', 'manual', 'luminance' ], true ) ? ( $b['dark']['strategy'] ?? '' ) : 'auto';
                        update_option( 'olobuild_dark_settings', [
                            'enabled'  => ! empty( $b['dark']['enabled'] ),
                            'strategy' => $strategy,
                        ] );
                    }

                    update_option( 'olobuild_settings_last_saved', time() );
                    return rest_ensure_response( [ 'ok' => true ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // ─── Endpoint generici per i 5 tab migrati in Configurazione (v1.0.30) ───
        // Cookie Consent — option array unica `olo_cookie_settings`
        register_rest_route( $ns, '/cookie-consent', [
            [
                'methods'             => 'GET',
                'callback'            => function () {
                    return rest_ensure_response( get_option( 'olobuild_cookie_settings', [] ) );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'POST',
                'callback'            => function ( $req ) {
                    $existing = get_option( 'olobuild_cookie_settings', [] );
                    $payload  = $req->get_json_params();
                    if ( ! is_array( $payload ) ) $payload = [];
                    update_option( 'olobuild_cookie_settings', array_merge( $existing, $payload ) );
                    update_option( 'olobuild_settings_last_saved', time() );
                    return rest_ensure_response( [ 'ok' => true ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // Performance — option array unica `olo_performance`
        register_rest_route( $ns, '/performance', [
            [
                'methods'             => 'GET',
                'callback'            => function () {
                    return rest_ensure_response( get_option( 'olobuild_performance', [] ) );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'POST',
                'callback'            => function ( $req ) {
                    $existing = get_option( 'olobuild_performance', [] );
                    if ( ! is_array( $existing ) ) $existing = [];
                    $payload  = $req->get_json_params();
                    if ( ! is_array( $payload ) ) $payload = [];
                    $merged = array_merge( $existing, $payload );
                    update_option( 'olobuild_performance', $merged );
                    // Olobuild_Critical_CSS::init() si attiva sulla legacy option: va tenuta in sync
                    // (la vecchia pagina lo faceva nel sanitize di register_setting).
                    update_option( 'olobuild_critical_css_enabled', ! empty( $merged['critical_css_enabled'] ) ? '1' : '' );
                    update_option( 'olobuild_settings_last_saved', time() );
                    return rest_ensure_response( [ 'ok' => true ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // Performance stats — read-only, dati reali da Critical CSS + CSS cache
        register_rest_route( $ns, '/performance/stats', [
            [
                'methods'             => 'GET',
                'callback'            => function () {
                    // get_option() della classe = defaults-aware (i flag attivi di default contano)
                    $opt = class_exists( 'Olobuild_Performance_Settings' )
                        ? Olobuild_Performance_Settings::get_option()
                        : get_option( 'olobuild_performance', [] );
                    if ( ! is_array( $opt ) ) $opt = [];

                    // Critical CSS pages cached
                    $ccss_count = 0;
                    $ccss_last  = '';
                    if ( class_exists( 'Olobuild_Critical_CSS' ) && method_exists( 'Olobuild_Critical_CSS', 'get_status' ) ) {
                        $st = Olobuild_Critical_CSS::get_status();
                        if ( is_array( $st ) ) {
                            $ccss_count = (int) ( $st['cached_count'] ?? 0 );
                            $ccss_last  = (string) ( $st['last_generated'] ?? '' );
                        }
                    }

                    // CSS cache files dir
                    $upload_dir = wp_upload_dir();
                    $cache_dir  = trailingslashit( $upload_dir['basedir'] ) . 'olobuild-cache/';
                    $cache_count = 0;
                    $cache_size  = 0;
                    if ( is_dir( $cache_dir ) ) {
                        $files = glob( $cache_dir . 'olo-*.css' ) ?: [];
                        $cache_count = count( $files );
                        foreach ( $files as $f ) $cache_size += filesize( $f ) ?: 0;
                    }

                    // Templates totali (proxy per "pages_total")
                    global $wpdb;
                    $t_templates = Olobuild_Database::table( 'templates' );
                    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Tabella custom del plugin ({prefix}olobuild_templates); nessun equivalente WP_Query. Interpolato solo il nome tabella da $wpdb->prefix; il valore 'published' passa da $wpdb->prepare con %s; conteggio non cacheabile.
                    $pages_total = (int) $wpdb->get_var( $wpdb->prepare(
                        "SELECT COUNT(*) FROM {$t_templates} WHERE status = %s",
                        'published'
                    ) );

                    // Score derivato dai flag performance attivi
                    $flag_keys = [
                        'critical_css_enabled', 'defer_js', 'minify_css', 'css_cache_files',
                        'css_per_tile', 'uikit_subset', 'resource_hints', 'font_preload', 'video_facade',
                        'fetchpriority', 'lazy_images', 'lazy_videos', 'browser_cache_headers',
                        'remove_jquery_migrate', 'remove_emoji_scripts',
                    ];
                    $on = 0;
                    foreach ( $flag_keys as $k ) if ( ! empty( $opt[ $k ] ) ) $on++;
                    $score = (int) round( 60 + ( $on / count( $flag_keys ) ) * 40 ); // 60-100

                    return rest_ensure_response( [
                        'score'           => $score,
                        'pages_cached'    => $ccss_count + $cache_count,
                        'pages_total'     => max( $pages_total, $ccss_count + $cache_count ),
                        'hit_rate'        => '—', // no log layer yet
                        'size'            => size_format( $cache_size, 1 ),
                        'size_max'        => '500 MB',
                        'bandwidth_saved' => '—',
                        'last_purge'      => $ccss_last ?: '—',
                    ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // Stockmedia behavior — preferenze importazione stock provider
        register_rest_route( $ns, '/settings/stockmedia-behavior', [
            [
                'methods'             => 'GET',
                'callback'            => function () {
                    $defaults = [ 'preferred' => 'unsplash', 'download_local' => true, 'optimize_on_download' => false ];
                    $saved = get_option( 'olobuild_stockmedia_behavior', [] );
                    if ( ! is_array( $saved ) ) $saved = [];
                    return rest_ensure_response( wp_parse_args( $saved, $defaults ) );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'POST',
                'callback'            => function ( $req ) {
                    $b = $req->get_json_params();
                    if ( ! is_array( $b ) ) $b = [];
                    $allowed_providers = [ 'unsplash', 'pexels', 'pixabay', 'freesound', 'openverse' ];
                    $clean = [
                        'preferred'            => in_array( $b['preferred'] ?? '', $allowed_providers, true ) ? ( $b['preferred'] ?? '' ) : 'unsplash',
                        'download_local'       => ! empty( $b['download_local'] ),
                        'optimize_on_download' => ! empty( $b['optimize_on_download'] ),
                    ];
                    update_option( 'olobuild_stockmedia_behavior', $clean );
                    update_option( 'olobuild_settings_last_saved', time() );
                    return rest_ensure_response( [ 'ok' => true ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // AI usage — rolling stats degli ultimi 30 giorni
        register_rest_route( $ns, '/ai/usage', [
            [
                'methods'             => 'GET',
                'callback'            => function () {
                    $log = get_option( 'olobuild_ai_usage', [] );
                    if ( ! is_array( $log ) ) $log = [];

                    // Filter ultimi 30 giorni
                    $cutoff = time() - 30 * DAY_IN_SECONDS;
                    $recent = array_filter( $log, function ( $e ) use ( $cutoff ) {
                        return is_array( $e ) && ( $e['ts'] ?? 0 ) >= $cutoff;
                    } );

                    if ( empty( $recent ) ) {
                        return rest_ensure_response( [
                            'calls'         => 0,
                            'tokens'        => '0',
                            'cost_estimate' => '0.00',
                            'latency_avg'   => '0',
                        ] );
                    }

                    $calls = count( $recent );
                    $tokens = 0; $cost = 0.0; $latency_sum = 0;
                    foreach ( $recent as $e ) {
                        $tokens      += (int) ( $e['tokens']  ?? 0 );
                        $cost        += (float) ( $e['cost']  ?? 0 );
                        $latency_sum += (int) ( $e['ms']      ?? 0 );
                    }
                    $latency_avg = $calls > 0 ? round( ( $latency_sum / $calls ) / 1000, 1 ) : 0;

                    return rest_ensure_response( [
                        'calls'         => $calls,
                        'tokens'        => $tokens >= 1000 ? round( $tokens / 1000, 1 ) . 'k' : (string) $tokens,
                        'cost_estimate' => number_format_i18n( $cost, 2 ),
                        'latency_avg'   => (string) $latency_avg,
                    ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // SEO — 6 sub-endpoint, ognuno mappa a una option WP separata
        $seo_subkeys = [
            'titles'         => 'olo_seo_titles',
            'social'         => 'olo_seo_social',
            'local_business' => 'olo_seo_local_business',
            'webmaster'      => 'olo_seo_webmaster',
            'sitemap'        => 'olo_seo_sitemap',
            'advanced'       => 'olobuild_seo_advanced',
        ];
        foreach ( $seo_subkeys as $route => $option_key ) {
            register_rest_route( $ns, '/seo/' . $route, [
                [
                    'methods'             => 'GET',
                    'callback'            => function () use ( $option_key ) {
                        return rest_ensure_response( get_option( $option_key, [] ) );
                    },
                    'permission_callback' => function () { return current_user_can( 'manage_options' ); },
                ],
                [
                    'methods'             => 'POST',
                    'callback'            => function ( $req ) use ( $option_key ) {
                        $existing = get_option( $option_key, [] );
                        $payload  = $req->get_json_params();
                        if ( ! is_array( $payload ) ) $payload = [];
                        update_option( $option_key, array_merge( $existing, $payload ) );
                        update_option( 'olobuild_settings_last_saved', time() );
                        return rest_ensure_response( [ 'ok' => true ] );
                    },
                    'permission_callback' => function () { return current_user_can( 'manage_options' ); },
                ],
            ] );
        }

        // ─── Endpoint generici v1.0.31 (sprint configurazione fase 2) ───
        // Analytics / Tracking — option array `olo_analytics`
        register_rest_route( $ns, '/analytics', [
            [
                'methods'             => 'GET',
                'callback'            => function () {
                    return rest_ensure_response( get_option( 'olobuild_analytics', [] ) );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'POST',
                'callback'            => function ( $req ) {
                    $existing = get_option( 'olobuild_analytics', [] );
                    $payload  = $req->get_json_params();
                    if ( ! is_array( $payload ) ) $payload = [];
                    update_option( 'olobuild_analytics', array_merge( $existing, $payload ) );
                    // Mantieni anche le 3 option singole legacy per compat con codice frontend più vecchio.
                    foreach ( [ 'ga_id', 'fb_pixel_id', 'gtm_id' ] as $legacy_k ) {
                        if ( isset( $payload[ $legacy_k ] ) ) {
                            update_option( 'olo_' . ( $legacy_k === 'ga_id' ? 'ga_measurement_id' : $legacy_k ), sanitize_text_field( $payload[ $legacy_k ] ) );
                        }
                    }
                    update_option( 'olobuild_settings_last_saved', time() );
                    return rest_ensure_response( [ 'ok' => true ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // Maintenance & Coming Soon — 5 option singole
        register_rest_route( $ns, '/maintenance', [
            [
                'methods'             => 'GET',
                'callback'            => function () {
                    return rest_ensure_response( [
                        'mode'                    => get_option( 'olobuild_maintenance_mode', 'off' ),
                        'template_id'             => (int) get_option( 'olobuild_maintenance_template_id', 0 ),
                        'coming_soon_template_id' => (int) get_option( 'olobuild_coming_soon_template_id', 0 ),
                        'bypass_roles'            => (array) get_option( 'olobuild_maintenance_bypass_roles', [ 'administrator' ] ),
                        'bypass_secret'           => get_option( 'olobuild_maintenance_bypass_secret', '' ),
                    ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'POST',
                'callback'            => function ( $req ) {
                    $p = $req->get_json_params();
                    if ( isset( $p['mode'] ) ) {
                        $mode = in_array( $p['mode'], [ 'off', 'coming_soon', 'maintenance' ], true ) ? $p['mode'] : 'off';
                        update_option( 'olobuild_maintenance_mode', $mode );
                    }
                    if ( isset( $p['template_id'] ) )             update_option( 'olobuild_maintenance_template_id', (int) $p['template_id'] );
                    if ( isset( $p['coming_soon_template_id'] ) ) update_option( 'olobuild_coming_soon_template_id', (int) $p['coming_soon_template_id'] );
                    if ( isset( $p['bypass_roles'] ) && is_array( $p['bypass_roles'] ) ) {
                        update_option( 'olobuild_maintenance_bypass_roles', array_map( 'sanitize_key', $p['bypass_roles'] ) );
                    }
                    if ( isset( $p['bypass_secret'] ) )            update_option( 'olobuild_maintenance_bypass_secret', sanitize_text_field( $p['bypass_secret'] ) );
                    update_option( 'olobuild_settings_last_saved', time() );
                    return rest_ensure_response( [ 'ok' => true ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // Maintenance — genera un template "Coming Soon" precaricato e lo seleziona automaticamente
        register_rest_route( $ns, '/maintenance/generate-template', [
            'methods'             => 'POST',
            'callback'            => function ( $req ) {
                $params = $req->get_json_params();
                $kind   = ( $params['kind'] ?? 'coming_soon' ) === 'maintenance' ? 'maintenance' : 'coming_soon';
                if ( ! class_exists( 'Olobuild_Database' ) ) {
                    return new WP_Error( 'no_db', 'Olobuild_Database non disponibile', [ 'status' => 500 ] );
                }
                $launch_at = strtotime( '+30 days' );
                $launch_iso = wp_date( 'Y-m-d\TH:i:s', $launch_at );
                $title_text = $kind === 'coming_soon' ? __( 'Stiamo per arrivare', 'olobuild' ) : __( 'Sito in manutenzione', 'olobuild' );
                $sub_text   = $kind === 'coming_soon'
                    ? __( 'Un\'esperienza unica sta per cominciare. Lascia la tua email per essere tra i primi a scoprirla.', 'olobuild' )
                    : __( 'Stiamo aggiornando il sito per offrirti una nuova esperienza. Torniamo presto.', 'olobuild' );
                $bg_color   = $kind === 'coming_soon' ? '#0f172a' : '#1c1917';
                $accent     = '#e1474f';

                $headline_id  = wp_generate_uuid4();
                $sub_id       = wp_generate_uuid4();
                $countdown_id = wp_generate_uuid4();
                $spacer_id    = wp_generate_uuid4();
                $btn_id       = wp_generate_uuid4();
                $col_id       = wp_generate_uuid4();
                $row_id       = wp_generate_uuid4();
                $section_id   = wp_generate_uuid4();

                // Template content: 1 section centered → 1 row → 1 column → headline + text + countdown + button.
                // I tile usano default config; settings essenziali sotto.
                $content = [
                    [
                        'id'   => $section_id,
                        'type' => 'section',
                        'settings' => [
                            'width'         => 'default',
                            'bg_scope'      => 'container',
                            'padding'       => 'large',
                            'sticky_effect' => 'none',
                            'flex_direction'=> 'column',
                            'flex_justify'  => 'center',
                            'flex_align'    => 'center',
                        ],
                        'style' => [
                            'bg' => [
                                'type'  => 'solid',
                                'color' => $bg_color,
                                'gradient_angle' => 135, 'gradient_from' => $bg_color, 'gradient_to' => '#1e1b4b',
                                'image_url' => '', 'image_size' => 'cover', 'image_position' => 'center center',
                                'video_url' => '', 'video_poster' => '', 'video_fit' => 'cover',
                                'overlay_color' => '#000000', 'overlay_opacity' => 0, 'color_opacity' => 100,
                            ],
                            'tile_min_height' => '100vh',
                            'padding_top' => 80, 'padding_right' => 24, 'padding_bottom' => 80, 'padding_left' => 24,
                        ],
                        'advanced' => [],
                        'children' => [
                            [
                                'id'   => $row_id,
                                'type' => 'row',
                                'settings' => [ 'layout' => '100', 'gap' => '24', 'vertical_align' => 'stretch' ],
                                'style'    => [],
                                'advanced' => [],
                                'children' => [
                                    [
                                        'id'   => $col_id,
                                        'type' => 'column',
                                        'settings' => [],
                                        'style'    => [ 'tile_max_width' => '720px' ],
                                        'advanced' => [],
                                        'children' => [
                                            [
                                                'id'   => $headline_id,
                                                'type' => 'headline',
                                                'settings' => [
                                                    'preset'             => 'custom',
                                                    'heading'            => $title_text,
                                                    'subtitle'           => '',
                                                    'tag'                => 'h1',
                                                    'alignment'          => 'center',
                                                    'heading_size'       => 'xl',
                                                    'heading_font_size'  => '64',
                                                    'heading_color'      => '#ffffff',
                                                    'heading_italic'     => false,
                                                    'heading_uppercase'  => false,
                                                    'decoration'         => 'none',
                                                ],
                                                'style' => [ 'margin_bottom' => 16 ],
                                                'advanced' => [],
                                                'children' => [],
                                            ],
                                            [
                                                'id'   => $sub_id,
                                                'type' => 'text-block',
                                                'settings' => [
                                                    'content' => '<p style="font-size:18px;line-height:1.6;color:rgba(255,255,255,.75);text-align:center;max-width:560px;margin:0 auto;">' . esc_html( $sub_text ) . '</p>',
                                                ],
                                                'style' => [ 'margin_bottom' => 40 ],
                                                'advanced' => [],
                                                'children' => [],
                                            ],
                                            $kind === 'coming_soon' ? [
                                                'id'   => $countdown_id,
                                                'type' => 'countdown',
                                                'settings' => [
                                                    'date'             => $launch_iso,
                                                    'show_days'        => true,
                                                    'show_hours'       => true,
                                                    'show_minutes'     => true,
                                                    'show_seconds'     => true,
                                                    'expired_message'  => __( 'È arrivato il momento!', 'olobuild' ),
                                                    'digit_color'      => '#ffffff',
                                                    'label_color'      => 'rgba(255,255,255,0.6)',
                                                    'alignment'        => 'center',
                                                    'digit_font_size'  => '56',
                                                    'label_font_size'  => '13',
                                                    'days_label'       => __( 'Giorni', 'olobuild' ),
                                                    'hours_label'      => __( 'Ore', 'olobuild' ),
                                                    'minutes_label'    => __( 'Minuti', 'olobuild' ),
                                                    'seconds_label'    => __( 'Secondi', 'olobuild' ),
                                                ],
                                                'style' => [ 'margin_bottom' => 32 ],
                                                'advanced' => [],
                                                'children' => [],
                                            ] : [
                                                'id'   => $spacer_id,
                                                'type' => 'spacer',
                                                'settings' => [ 'height' => '40' ],
                                                'style'    => [],
                                                'advanced' => [],
                                                'children' => [],
                                            ],
                                            [
                                                'id'   => $btn_id,
                                                'type' => 'button',
                                                'settings' => [
                                                    'preset'           => 'modern-clean',
                                                    'text'             => $kind === 'coming_soon' ? __( 'Avvisami al lancio', 'olobuild' ) : __( 'Contattaci', 'olobuild' ),
                                                    'url'              => 'mailto:' . get_bloginfo( 'admin_email' ),
                                                    'target'           => '_self',
                                                    'alignment'        => 'center',
                                                    'full_width'       => false,
                                                    'bg'               => [ 'type' => 'none' ],
                                                    'bg_color'         => $accent,
                                                    'text_color'       => '#ffffff',
                                                    'border_radius'    => '999',
                                                    'font_size'        => '16',
                                                    'font_weight'      => '600',
                                                    'letter_spacing'   => '0',
                                                    'text_transform'   => 'none',
                                                    'tile_padding'     => [ 'top' => 16, 'right' => 36, 'bottom' => 16, 'left' => 36 ],
                                                    'icon_position'    => 'before',
                                                    'icon_spacing'     => '8',
                                                    'shadow'           => 'sm',
                                                    'hover_bg_color'   => '#c8323a',
                                                    'hover_text_color' => '#ffffff',
                                                    'hover_effect'     => 'lift',
                                                ],
                                                // Forza il wrapper a NON ereditare bg_color dai settings.
                                                // Senza questo, il renderer element copia settings.bg_color
                                                // (rosso del button) anche allo style del wrapper esterno,
                                                // creando il doppio sfondo "scatola rossa" attorno al button.
                                                'style' => [ 'bg_color' => 'transparent', 'bg' => [ 'type' => 'none' ] ],
                                                'advanced' => [],
                                                'children' => [],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];

                $db = new Olobuild_Database();
                $title = $kind === 'coming_soon' ? __( 'Coming Soon — Default', 'olobuild' ) : __( 'Manutenzione — Default', 'olobuild' );
                $new_id = $db->create_template( [
                    'title'   => $title,
                    'type'    => 'page',
                    'content' => $content,
                    'status'  => 'published',
                    'settings'=> new stdClass(),
                ] );

                if ( ! $new_id ) {
                    return new WP_Error( 'create_failed', 'Errore creazione template', [ 'status' => 500 ] );
                }

                $option_key = $kind === 'coming_soon' ? 'olobuild_coming_soon_template_id' : 'olobuild_maintenance_template_id';
                update_option( $option_key, (int) $new_id );
                update_option( 'olobuild_settings_last_saved', time() );

                return rest_ensure_response( [
                    'ok'          => true,
                    'template_id' => (int) $new_id,
                    'title'       => $title,
                    'edit_url'    => admin_url( 'admin.php?page=olobuilder-templates&template_id=' . (int) $new_id ),
                ] );
            },
            'permission_callback' => function () { return current_user_can( 'manage_options' ); },
        ] );

        // WooCommerce templates — 6 option singole
        register_rest_route( $ns, '/woo-templates', [
            [
                'methods'             => 'GET',
                'callback'            => function () {
                    return rest_ensure_response( [
                        'olobuild_woo_tpl_product_single'   => (int) get_option( 'olobuild_woo_tpl_product_single', 0 ),
                        'olobuild_woo_tpl_product_archive'  => (int) get_option( 'olobuild_woo_tpl_product_archive', 0 ),
                        'olobuild_woo_tpl_product_category' => (int) get_option( 'olobuild_woo_tpl_product_category', 0 ),
                        'olobuild_woo_tpl_cart'             => (int) get_option( 'olobuild_woo_tpl_cart', 0 ),
                        'olobuild_woo_tpl_checkout'         => (int) get_option( 'olobuild_woo_tpl_checkout', 0 ),
                        'olobuild_woo_tpl_myaccount'        => (int) get_option( 'olobuild_woo_tpl_myaccount', 0 ),
                        'woo_active'                   => class_exists( 'WooCommerce' ),
                    ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'POST',
                'callback'            => function ( $req ) {
                    $p = $req->get_json_params();
                    $allowed = [ 'olobuild_woo_tpl_product_single', 'olobuild_woo_tpl_product_archive', 'olobuild_woo_tpl_product_category', 'olobuild_woo_tpl_cart', 'olobuild_woo_tpl_checkout', 'olobuild_woo_tpl_myaccount' ];
                    foreach ( $allowed as $k ) {
                        if ( isset( $p[ $k ] ) ) update_option( $k, (int) $p[ $k ] );
                    }
                    update_option( 'olobuild_settings_last_saved', time() );
                    return rest_ensure_response( [ 'ok' => true ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // Design Presets — behavior (overwrite_manual, snapshot_before, preview_mode)
        // + snapshots stub (per ora list/restore/delete: noop, gli stili reali sono in olo_styles).
        register_rest_route( $ns, '/design-presets/behavior', [
            [
                'methods'             => 'GET',
                'callback'            => function () {
                    return rest_ensure_response( get_option( 'olobuild_design_preset_behavior', [
                        'overwrite_manual' => true,
                        'snapshot_before'  => true,
                        'preview_mode'     => 'side_by_side',
                    ] ) );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'POST',
                'callback'            => function ( $req ) {
                    $payload = $req->get_json_params();
                    if ( ! is_array( $payload ) ) $payload = [];
                    update_option( 'olobuild_design_preset_behavior', $payload );
                    update_option( 'olobuild_settings_last_saved', time() );
                    return rest_ensure_response( [ 'ok' => true ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // Design Presets — apply (stub graceful, l'apply reale richiede integrazione stylesStore).
        register_rest_route( $ns, '/design-presets/apply', [
            'methods'             => 'POST',
            'callback'            => function ( $req ) {
                $p = $req->get_json_params();
                $preset_id = sanitize_key( $p['preset_id'] ?? '' );
                update_option( 'olobuild_active_preset_id', $preset_id );
                update_option( 'olobuild_settings_last_saved', time() );
                // TODO: applicare effettivamente i colori/font/tokens del preset a `olo_styles`,
                // `olo_global_colors`, `olo_global_typography`. Per ora salva solo il flag attivo.
                return rest_ensure_response( [ 'ok' => true, 'preset_id' => $preset_id ] );
            },
            'permission_callback' => function () { return current_user_can( 'manage_options' ); },
        ] );

        // Design Presets — snapshots (stub: list ritorna [], action: noop)
        register_rest_route( $ns, '/design-presets/snapshots', [
            [
                'methods'             => 'GET',
                'callback'            => function () {
                    return rest_ensure_response( get_option( 'olobuild_design_preset_snapshots', [] ) );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'POST',
                'callback'            => function () {
                    // Stub — TODO: implementare restore/delete reale che ripristina olo_styles ecc.
                    return rest_ensure_response( [ 'ok' => true ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );

        // Redirects — CRUD su tabella custom + log 404
        register_rest_route( $ns, '/redirects', [
            [
                'methods'             => 'GET',
                'callback'            => function () {
                    global $wpdb;
                    $redirects = [];
                    $log404    = [];
                    $rt = $wpdb->prefix . 'olobuild_redirects';
                    $lt = $wpdb->prefix . 'olo_404_log';
                    // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Tabelle custom del plugin ($wpdb->prefix . 'olobuild_redirects'/'olo_404_log'); nessun equivalente WP_Query. Interpolati solo i nomi tabella derivati da $wpdb->prefix (nessun valore utente); liste redirect/404 non cacheabili (lette al volo nell'admin).
                    if ( $wpdb->get_var( "SHOW TABLES LIKE '$rt'" ) === $rt ) {
                        $redirects = $wpdb->get_results( "SELECT id, from_url, to_url, type, hits FROM $rt ORDER BY id DESC LIMIT 500", ARRAY_A );
                    }
                    if ( $wpdb->get_var( "SHOW TABLES LIKE '$lt'" ) === $lt ) {
                        $log404 = $wpdb->get_results( "SELECT id, url, hits, last_hit FROM $lt ORDER BY hits DESC LIMIT 200", ARRAY_A );
                    }
                    // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
                    $advanced = (array) get_option( 'olobuild_seo_advanced', [] );
                    return rest_ensure_response( [
                        'redirects'    => $redirects ?: [],
                        'log404'       => $log404 ?: [],
                        'indexnow_key' => $advanced['indexnow_key'] ?? '',
                    ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
            [
                'methods'             => 'POST',
                'callback'            => function ( $req ) {
                    global $wpdb;
                    $p = $req->get_json_params();
                    $action = $p['action'] ?? '';
                    $rt = $wpdb->prefix . 'olobuild_redirects';
                    $lt = $wpdb->prefix . 'olo_404_log';
                    // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Tabelle custom del plugin ($wpdb->prefix . 'olobuild_redirects'/'olo_404_log'); nessun equivalente WP_Query. insert/delete usano l'API $wpdb con valori parametrizzati; il TRUNCATE interpola solo il nome tabella da $wpdb->prefix (nessun valore utente); operazioni di scrittura non cacheabili.
                    if ( $action === 'add' ) {
                        if ( empty( $p['from_url'] ) || empty( $p['to_url'] ) ) {
                            return new WP_Error( 'missing', 'from_url e to_url obbligatori', [ 'status' => 400 ] );
                        }
                        $wpdb->insert( $rt, [
                            'from_url'   => sanitize_text_field( $p['from_url'] ),
                            'to_url'     => esc_url_raw( $p['to_url'] ),
                            'type'       => (int) ( $p['type'] ?? 301 ),
                            'hits'       => 0,
                            'created_at' => current_time( 'mysql' ),
                        ] );
                        return rest_ensure_response( [ 'ok' => true, 'id' => $wpdb->insert_id ] );
                    }
                    if ( $action === 'delete' && ! empty( $p['id'] ) ) {
                        $wpdb->delete( $rt, [ 'id' => (int) $p['id'] ], [ '%d' ] );
                        return rest_ensure_response( [ 'ok' => true ] );
                    }
                    if ( $action === 'clear_404' ) {
                        $wpdb->query( "TRUNCATE TABLE $lt" );
                        return rest_ensure_response( [ 'ok' => true ] );
                    }
                    // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
                    if ( $action === 'save_indexnow' ) {
                        $adv = (array) get_option( 'olobuild_seo_advanced', [] );
                        $adv['indexnow_key'] = sanitize_text_field( $p['indexnow_key'] ?? '' );
                        update_option( 'olobuild_seo_advanced', $adv );
                        update_option( 'olobuild_settings_last_saved', time() );
                        return rest_ensure_response( [ 'ok' => true ] );
                    }
                    return new WP_Error( 'invalid_action', 'Azione non riconosciuta', [ 'status' => 400 ] );
                },
                'permission_callback' => function () { return current_user_can( 'manage_options' ); },
            ],
        ] );
    }

    public function rest_get_api_keys() {
        $keys = [
            'olobuild_pexels_api_key', 'olobuild_pixabay_api_key', 'olobuild_unsplash_api_key',
            'olobuild_freesound_api_key', 'olobuild_recaptcha_site_key', 'olobuild_recaptcha_secret_key',
            'olobuild_mailchimp_api_key',
        ];
        // La recaptcha_site_key e' pubblica (renderizzata nell'HTML): resta in chiaro.
        // Tutto il resto e' segreto e viene mascherato: solo gli ultimi 4 caratteri
        // lasciano il server, coerente con Olobuild_AI_Assistant::get_settings().
        $public = [ 'olobuild_recaptcha_site_key' ];
        $data   = [];
        foreach ( $keys as $k ) {
            $val = (string) get_option( $k, '' );
            if ( $val !== '' && ! in_array( $k, $public, true ) ) {
                // Mostra gli ultimi 4 char solo se la chiave e' abbastanza lunga, altrimenti maschera tutto.
                $val = strlen( $val ) > 4
                    ? str_repeat( '*', strlen( $val ) - 4 ) . substr( $val, -4 )
                    : str_repeat( '*', strlen( $val ) );
            }
            $data[ $k ] = $val;
        }
        return rest_ensure_response( $data );
    }

    public function rest_put_api_keys( $request ) {
        $allowed = [
            'olobuild_pexels_api_key', 'olobuild_pixabay_api_key', 'olobuild_unsplash_api_key',
            'olobuild_freesound_api_key', 'olobuild_recaptcha_site_key', 'olobuild_recaptcha_secret_key',
            'olobuild_mailchimp_api_key',
        ];
        $body = $request->get_json_params();
        foreach ( $allowed as $k ) {
            if ( isset( $body[ $k ] ) ) {
                $val = (string) $body[ $k ];
                // Placeholder mascherato (contiene '*') = valore non modificato: non sovrascrivere il segreto reale.
                if ( strpos( $val, '*' ) !== false ) {
                    continue;
                }
                update_option( $k, sanitize_text_field( $val ) );
            }
        }
        return rest_ensure_response( [ 'success' => true ] );
    }

    public function rest_get_breakpoints() {
        $defaults_bps = [
            [ 'id' => 'desktop_xl', 'name' => 'Desktop XL', 'min' => '1440', 'max' => '∞',    'icon' => '🖥️', 'is_default' => false ],
            [ 'id' => 'desktop',    'name' => 'Desktop',    'min' => '1200', 'max' => '1439', 'icon' => '🖥️', 'is_default' => true  ],
            [ 'id' => 'laptop',     'name' => 'Laptop',     'min' => '992',  'max' => '1199', 'icon' => '💻', 'is_default' => false ],
            [ 'id' => 'tablet',     'name' => 'Tablet',     'min' => '768',  'max' => '991',  'icon' => '📱', 'is_default' => false ],
            [ 'id' => 'mobile_l',   'name' => 'Mobile L',   'min' => '576',  'max' => '767',  'icon' => '📱', 'is_default' => false ],
            [ 'id' => 'mobile',     'name' => 'Mobile',     'min' => '0',    'max' => '575',  'icon' => '📱', 'is_default' => false ],
        ];
        $defaults_adv = [ 'strategy' => 'mobile' ];

        $bps = get_option( 'olobuild_breakpoints_v2', $defaults_bps );
        $adv = get_option( 'olobuild_breakpoints_advanced', $defaults_adv );

        if ( ! is_array( $bps ) || empty( $bps ) ) $bps = $defaults_bps;
        if ( ! is_array( $adv ) ) $adv = $defaults_adv;

        return rest_ensure_response( [
            'breakpoints' => array_values( $bps ),
            'advanced'    => wp_parse_args( $adv, $defaults_adv ),
        ] );
    }

    public function rest_put_breakpoints( $request ) {
        $body = $request->get_json_params();

        if ( isset( $body['breakpoints'] ) && is_array( $body['breakpoints'] ) ) {
            $clean = [];
            foreach ( $body['breakpoints'] as $b ) {
                if ( ! is_array( $b ) ) continue;
                $clean[] = [
                    'id'         => sanitize_key( $b['id']   ?? 'bp_' . wp_generate_password( 6, false ) ),
                    'name'       => sanitize_text_field( $b['name'] ?? '' ),
                    'min'        => sanitize_text_field( (string) ( $b['min'] ?? '0' ) ),
                    'max'        => sanitize_text_field( (string) ( $b['max'] ?? '∞' ) ),
                    'icon'       => sanitize_text_field( $b['icon'] ?? '📱' ),
                    'is_default' => ! empty( $b['is_default'] ),
                ];
            }
            update_option( 'olobuild_breakpoints_v2', $clean );
        }

        if ( isset( $body['advanced'] ) && is_array( $body['advanced'] ) ) {
            $strategy = in_array( $body['advanced']['strategy'] ?? '', [ 'mobile', 'desktop' ], true ) ? ( $body['advanced']['strategy'] ?? '' ) : 'mobile';
            update_option( 'olobuild_breakpoints_advanced', [ 'strategy' => $strategy ] );
        }

        update_option( 'olobuild_settings_last_saved', time() );
        return rest_ensure_response( [ 'success' => true ] );
    }
}
