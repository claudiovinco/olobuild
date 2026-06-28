<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Form_Handler {

    /**
     * Secret key used to sign form tokens (HMAC).
     * Derived from WordPress AUTH_SALT for uniqueness per-site.
     *
     * NOTA: non includiamo user_id nel secret perché questo è un form pubblico
     * (contact form) — gli utenti non loggati avrebbero sempre user_id = 0,
     * rendendo inutile il binding per-session. Il token è protetto da:
     * - HMAC con salt unico per sito (wp_salt)
     * - Scadenza temporale (12h)
     * - Binding al config del form (impedisce mass-assignment del payload server-side)
     * - Rate limiting per IP (configurabile)
     */
    private static function get_token_secret() {
        return wp_salt( 'auth' ) . '|olo_form_token';
    }

    /**
     * Generate a time-limited token bound to a specific form config payload.
     * Token format: v2:{timestamp}:{hmac(timestamp + sha256(config_b64), secret)}
     * Il binding al config impedisce a un attaccante di prendere un token valido
     * (es. da una pagina contact pubblica) e riusarlo modificando email_to/subject/
     * auto_reply_message per trasformare il sito in relay phishing.
     *
     * Valid for 12 hours.
     *
     * @param string $config_b64 Il payload base64-encoded del form config (lo stesso
     *                            che finisce in `_olo_form_config`). Stringa vuota per
     *                            casi senza config (sconsigliato).
     */
    public static function generate_token( $config_b64 = '' ) {
        $timestamp     = time();
        $config_digest = hash( 'sha256', (string) $config_b64 );
        $hmac          = hash_hmac( 'sha256', $timestamp . ':' . $config_digest, self::get_token_secret() );
        return 'v2:' . $timestamp . ':' . $hmac;
    }

    /**
     * Validate a form token. Returns true if valid, not expired, and bound to the
     * supplied config payload.
     *
     * I token v1 legacy (senza binding al config) vengono rifiutati: dopo l'aggiornamento
     * gli utenti con form aperti vedranno "Ricarica la pagina" — accettabile per il fix
     * di sicurezza (relay email arbitrarie). TTL form: 12h → la finestra di disagio
     * è limitata.
     */
    private function validate_token( $token, $config_b64 = '' ) {
        if ( empty( $token ) || ! is_string( $token ) ) {
            return false;
        }

        $parts = explode( ':', $token, 3 );
        if ( count( $parts ) !== 3 || $parts[0] !== 'v2' ) {
            return false;
        }

        $timestamp = (int) $parts[1];
        $hmac      = $parts[2];

        // Check expiration (12 hours)
        if ( abs( time() - $timestamp ) > 12 * HOUR_IN_SECONDS ) {
            return false;
        }

        // Verify HMAC binds to this specific config (impedisce manomissione email_to)
        $config_digest = hash( 'sha256', (string) $config_b64 );
        $expected      = hash_hmac( 'sha256', $timestamp . ':' . $config_digest, self::get_token_secret() );
        return hash_equals( $expected, $hmac );
    }

    /**
     * Whitelist server-side per i destinatari email dei form.
     *
     * Anche con token v2 (config bound), trattiamo l'`email_to`/`email_cc` come dati
     * untrusted: validiamo contro un set sicuro.
     *
     *  - Sempre permesso: admin_email del sito
     *  - Sempre permesso: qualsiasi email con dominio uguale all'admin_email
     *    (l'utente sta inviando a sé stesso/al team dello stesso dominio)
     *  - Permesse anche le email in `olo_allowed_form_recipients` (CSV in option)
     *    — utile per chi vuole un destinatario su dominio diverso.
     *
     * Tutto il resto → fallback su admin_email (vedi chiamante).
     */
    private function is_recipient_allowed( $email, $admin_email ) {
        if ( ! is_email( $email ) ) return false;
        if ( strcasecmp( $email, $admin_email ) === 0 ) return true;

        $email_domain = strtolower( substr( strrchr( $email, '@' ), 1 ) );
        $admin_domain = strtolower( substr( strrchr( $admin_email, '@' ), 1 ) );
        if ( $email_domain && $email_domain === $admin_domain ) return true;

        $extra = (string) get_option( 'olo_allowed_form_recipients', '' );
        if ( $extra !== '' ) {
            $list = array_filter( array_map( 'trim', explode( ',', strtolower( $extra ) ) ) );
            if ( in_array( strtolower( $email ), $list, true ) ) return true;
        }
        return false;
    }

    /**
     * Get the client IP, checking proxy headers (sanitized) first.
     */
    private function get_client_ip() {
        // Check X-Forwarded-For (first IP in the chain is the client)
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only di $_SERVER per derivare l'IP client (rate-limit/log); nessuna modifica di stato; il modello anti-abuso del form è HMAC token v2 + honeypot + rate-limit, non un nonce (pagine cache); valore sanitizzato sotto.
        if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only header proxy; vedi nota sopra.
            $ips = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
            $ip  = trim( $ips[0] );
            if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
                return sanitize_text_field( $ip );
            }
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only di $_SERVER['REMOTE_ADDR'] per l'IP client; nessuna modifica di stato; modello anti-abuso HMAC token + honeypot + rate-limit; valore sanitizzato.
        return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0.0.0.0';
    }

    public function init() {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );

        // Schedule cleanup of old uploads (every day)
        add_action( 'olo_cleanup_form_uploads', [ __CLASS__, 'cleanup_old_uploads' ] );
        if ( ! wp_next_scheduled( 'olo_cleanup_form_uploads' ) ) {
            wp_schedule_event( time(), 'daily', 'olo_cleanup_form_uploads' );
        }
    }

    public function register_routes() {
        register_rest_route( 'olo/v1', '/form/submit', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'handle_submit' ],
            'permission_callback' => '__return_true', // Public endpoint (contact form)
        ] );

        register_rest_route( 'olo/v1', '/submissions/export', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'export_csv' ],
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            },
            'args' => [
                'form_id' => [
                    'required' => false,
                    'type'     => 'string',
                ],
            ],
        ] );
    }

    /**
     * Handle form submission.
     */
    public function handle_submit( $request ) {
        // 1. Decode form config — validate base64 before json_decode.
        //    Lo facciamo PRIMA del token check perché il token v2 è legato al config:
        //    se il config arriva corrotto/mancante, il token non potrà mai validare.
        $config_raw    = (string) $request->get_param( '_olo_form_config' );
        $config_decoded = base64_decode( $config_raw, true ); // strict mode
        if ( $config_decoded === false ) {
            return new WP_REST_Response( [
                'success' => false,
                'data'    => [ 'message' => 'Configurazione form non valida.' ],
            ], 400 );
        }
        $config = json_decode( $config_decoded, true );
        if ( ! is_array( $config ) ) {
            return new WP_REST_Response( [
                'success' => false,
                'data'    => [ 'message' => 'Configurazione form non valida.' ],
            ], 400 );
        }

        // 2. Validate token (anti-CSRF / anti-replay / anti-tampering).
        //    Il token è legato all'EXACT config payload: se l'attaccante modifica
        //    email_to (o qualsiasi altra chiave) il digest cambia e l'HMAC non torna.
        $token = $request->get_param( '_olo_form_token' );
        if ( ! $this->validate_token( $token, $config_raw ) ) {
            return new WP_REST_Response( [
                'success' => false,
                'data'    => [ 'message' => 'Token non valido o scaduto. Ricarica la pagina e riprova.' ],
            ], 403 );
        }

        // 3. Honeypot checks
        if ( ! empty( $config['honeypot'] ) ) {
            $honeypot_value = $request->get_param( 'olo_website_url' );
            if ( ! empty( $honeypot_value ) ) {
                // Silently pretend success to not tip off bots
                return new WP_REST_Response( [
                    'success' => true,
                    'data'    => [ 'message' => $config['success_message'] ?? 'Inviato!' ],
                ], 200 );
            }
        }

        // Secondary honeypot field (hidden field named olo_hp_field)
        $hp_field_value = $request->get_param( 'olo_hp_field' );
        if ( ! empty( $hp_field_value ) ) {
            // Bot filled in the hidden field — silently reject
            return new WP_REST_Response( [
                'success' => true,
                'data'    => [ 'message' => $config['success_message'] ?? 'Inviato!' ],
            ], 200 );
        }

        // 4. Rate limiting
        if ( ! empty( $config['rate_limit'] ) ) {
            $ip     = $this->get_client_ip();
            $max    = absint( $config['rate_limit_max'] ?? 5 );
            $window = absint( $config['rate_limit_window'] ?? 60 ) * MINUTE_IN_SECONDS;

            // Doppia chiave: l'IP dichiarato (X-Forwarded-For) è spoofabile — da solo
            // bastava ruotare l'header per azzerare il contatore. REMOTE_ADDR non lo è:
            // backstop più largo (10x) che non penalizza utenti dietro proxy/CDN condivisi.
            $limits = [ 'olo_form_rl_' . md5( $ip ) => $max ];
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only di $_SERVER['REMOTE_ADDR'] come backstop rate-limit; nessuna modifica di stato; modello anti-abuso HMAC token + honeypot + rate-limit; valore sanitizzato.
            $remote = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
            if ( $remote && $remote !== $ip ) {
                $limits[ 'olo_form_rl_r_' . md5( $remote ) ] = $max * 10;
            }

            foreach ( $limits as $key => $limit ) {
                if ( (int) get_transient( $key ) >= $limit ) {
                    return new WP_REST_Response( [
                        'success' => false,
                        'data'    => [ 'message' => 'Troppe richieste. Riprova tra qualche minuto.' ],
                    ], 429 );
                }
            }
            foreach ( $limits as $key => $limit ) {
                set_transient( $key, (int) get_transient( $key ) + 1, $window );
            }
        }

        // 4b. reCAPTCHA v3 verification
        if ( ! empty( $config['recaptcha_enabled'] ) ) {
            $recaptcha_secret = get_option( 'olo_recaptcha_secret_key', '' );
            $recaptcha_token  = sanitize_text_field( $request->get_param( '_olo_recaptcha_token' ) ?? '' );
            if ( $recaptcha_secret ) {
                if ( empty( $recaptcha_token ) ) {
                    return new WP_REST_Response( [
                        'success' => false,
                        'data'    => [ 'message' => 'Verifica reCAPTCHA mancante. Ricarica la pagina.' ],
                    ], 403 );
                }
                $verify = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', [
                    'body' => [
                        'secret'   => $recaptcha_secret,
                        'response' => $recaptcha_token,
                        'remoteip' => $this->get_client_ip(),
                    ],
                ] );
                if ( ! is_wp_error( $verify ) ) {
                    $body = json_decode( wp_remote_retrieve_body( $verify ), true );
                    if ( empty( $body['success'] ) || ( isset( $body['score'] ) && $body['score'] < 0.5 ) ) {
                        return new WP_REST_Response( [
                            'success' => false,
                            'data'    => [ 'message' => 'Verifica anti-spam non superata. Riprova.' ],
                        ], 403 );
                    }
                }
            }
        }

        // 5. Get fields
        $fields = $request->get_param( 'fields' );
        if ( ! is_array( $fields ) || empty( $fields ) ) {
            return new WP_REST_Response( [
                'success' => false,
                'data'    => [ 'message' => 'Nessun dato ricevuto.' ],
            ], 400 );
        }

        // 6. Sanitize all fields
        $sanitized = [];
        foreach ( $fields as $key => $value ) {
            $clean_key = sanitize_key( $key );
            if ( is_array( $value ) ) {
                // Checkbox arrays
                $sanitized[ $clean_key ] = array_map( 'sanitize_text_field', $value );
            } else {
                $sanitized[ $clean_key ] = sanitize_textarea_field( $value );
            }
        }

        // 6b. Handle file uploads
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- callback REST pubblica (form contatto): il modello anti-abuso è HMAC token v2 validato sopra (validate_token) + honeypot + rate-limit, non un nonce (form spesso serviti da pagine cache); upload validati con is_uploaded_file/wp_check_filetype/finfo + allowlist estensioni.
        if ( ! empty( $_FILES ) ) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';

            // Ensure upload directory exists
            $upload_dir = wp_upload_dir();
            $olo_upload_path = $upload_dir['basedir'] . '/olobuild-uploads';
            wp_mkdir_p( $olo_upload_path );

            // Protect upload directory from direct execution
            // .htaccess for Apache
            $htaccess_path = $olo_upload_path . '/.htaccess';
            if ( ! file_exists( $htaccess_path ) ) {
                file_put_contents( $htaccess_path, "Options -ExecCGI\nAddHandler cgi-script .php .php3 .php4 .php5 .phtml .pl .py .jsp .asp .htm .shtml .sh .cgi\n<Files *.php>\ndeny from all\n</Files>" );
            }

            // index.php for directory listing protection (works on both Apache and Nginx)
            $index_path = $olo_upload_path . '/index.php';
            if ( ! file_exists( $index_path ) ) {
                file_put_contents( $index_path, '<?php // Silence is golden.' );
            }

            // Nginx: write a note file reminding server admins to block PHP execution
            // Nginx ignores .htaccess — admins must add this to their server block:
            // location ~* /wp-content/uploads/olobuild-uploads/.*\.php$ { deny all; }
            $nginx_note = $olo_upload_path . '/NGINX-SECURITY.txt';
            if ( ! file_exists( $nginx_note ) ) {
                file_put_contents( $nginx_note, "# Nginx does not use .htaccess — add this rule to your server block:\n# location ~* /wp-content/uploads/olobuild-uploads/.*\\.php$ { deny all; }\n" );
            }

            // Global fallback settings
            $global_max_size = absint( $config['file_max_size'] ?? 5 ) * 1024 * 1024;
            $global_allowed  = array_map( 'trim', explode( ',', $config['file_types'] ?? '.pdf,.jpg,.png' ) );

            // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- callback REST pubblica protetta da HMAC token v2 + honeypot + rate-limit; i campi $_FILES vengono validati a valle (is_uploaded_file su tmp_name, sanitize_file_name + wp_check_filetype + finfo sul nome, allowlist/blocklist estensioni).
            foreach ( $_FILES as $file_key => $file_data ) {
                // Handle both single and multiple file uploads
                $is_multi = is_array( $file_data['name'] );
                $files_to_process = [];

                if ( $is_multi ) {
                    for ( $fi = 0; $fi < count( $file_data['name'] ); $fi++ ) {
                        if ( $file_data['error'][ $fi ] !== UPLOAD_ERR_OK ) {
                            continue;
                        }
                        $files_to_process[] = [
                            'name'     => $file_data['name'][ $fi ],
                            'type'     => $file_data['type'][ $fi ],
                            'tmp_name' => $file_data['tmp_name'][ $fi ],
                            'error'    => $file_data['error'][ $fi ],
                            'size'     => $file_data['size'][ $fi ],
                        ];
                    }
                } else {
                    if ( $file_data['error'] === UPLOAD_ERR_OK ) {
                        $files_to_process[] = $file_data;
                    }
                }

                $uploaded_urls = [];
                foreach ( $files_to_process as $single_file ) {
                    // Validate file size
                    if ( $single_file['size'] > $global_max_size ) {
                        continue;
                    }

                    // Validate file extension
                    $ext = '.' . strtolower( pathinfo( $single_file['name'], PATHINFO_EXTENSION ) );
                    if ( ! in_array( $ext, $global_allowed, true ) ) {
                        continue;
                    }

                    // Hard-block di estensioni eseguibili o renderizzabili nel browser
                    // (SVG/HTML/XML/JS -> stored XSS) ANCHE se inserite nell'allowlist admin.
                    $blocked_ext = [ '.svg', '.svgz', '.html', '.htm', '.xhtml', '.xml', '.js', '.mjs', '.php', '.php3', '.php4', '.php5', '.php7', '.phtml', '.phar', '.htaccess' ];
                    if ( in_array( $ext, $blocked_ext, true ) ) {
                        continue;
                    }

                    // Validate MIME type matches extension
                    $mime_check = wp_check_filetype( $single_file['name'] );
                    if ( ! $mime_check['type'] ) {
                        continue;
                    }
                    // Block dangerous file types regardless of extension
                    $dangerous_mimes = [ 'application/x-httpd-php', 'application/x-php', 'text/x-php', 'application/x-executable', 'application/x-msdownload', 'image/svg+xml', 'text/html', 'application/xhtml+xml', 'application/xml', 'text/xml' ];
                    $real_mime = '';
                    if ( function_exists( 'finfo_open' ) ) {
                        $finfo     = finfo_open( FILEINFO_MIME_TYPE );
                        $real_mime = finfo_file( $finfo, $single_file['tmp_name'] );
                        finfo_close( $finfo );
                    } elseif ( function_exists( 'mime_content_type' ) ) {
                        $real_mime = mime_content_type( $single_file['tmp_name'] );
                    }
                    if ( $real_mime && in_array( $real_mime, $dangerous_mimes, true ) ) {
                        continue;
                    }

                    // Sanitize filename
                    $safe_name = wp_unique_filename( $olo_upload_path, sanitize_file_name( $single_file['name'] ) );
                    $dest_path = $olo_upload_path . '/' . $safe_name;

                    // Move uploaded file — is_uploaded_file() valida l'upload HTTP,
                    // copy() sostituisce move_uploaded_file() (vietato da wp.org).
                    if ( is_uploaded_file( $single_file['tmp_name'] ) && copy( $single_file['tmp_name'], $dest_path ) ) {
                        $file_url = $upload_dir['baseurl'] . '/olobuild-uploads/' . $safe_name;
                        $uploaded_urls[] = esc_url_raw( $file_url );
                    }
                }

                // Store in sanitized data
                $clean_key = sanitize_key( str_replace( '[]', '', $file_key ) );
                if ( count( $uploaded_urls ) === 1 ) {
                    $sanitized[ $clean_key ] = $uploaded_urls[0];
                } elseif ( count( $uploaded_urls ) > 1 ) {
                    $sanitized[ $clean_key ] = $uploaded_urls;
                }
            }
        }

        // 7. Basic validation: at least one non-empty field
        $has_content = false;
        foreach ( $sanitized as $v ) {
            if ( is_array( $v ) ) {
                if ( ! empty( array_filter( $v ) ) ) $has_content = true;
            } elseif ( trim( $v ) !== '' ) {
                $has_content = true;
            }
        }
        if ( ! $has_content ) {
            return new WP_REST_Response( [
                'success' => false,
                'data'    => [ 'message' => 'Compila almeno un campo.' ],
            ], 400 );
        }

        // 8. Build email
        // Defense-in-depth: anche se il token v2 lega il config (impedendo il tamper
        // del destinatario), validiamo email_to contro una whitelist server-side.
        // Se non passa, fallback su admin_email — il form non spegne la submission,
        // ma la spedizione va sempre verso un destinatario di fiducia.
        $admin_email = get_option( 'admin_email' );
        $to          = sanitize_email( $config['email_to'] ?? '' );
        if ( empty( $to ) || ! $this->is_recipient_allowed( $to, $admin_email ) ) {
            $to = $admin_email;
        }

        $subject   = sanitize_text_field( $config['email_subject'] ?? 'Nuovo messaggio dal sito' );
        $site_name = get_bloginfo( 'name' );
        $from_name = sanitize_text_field( $config['email_from_name'] ?? '' ) ?: $site_name;

        // Build readable email body
        $body_lines = [];
        $body_lines[] = "Nuovo messaggio da {$site_name}";
        $body_lines[] = str_repeat( '─', 40 );
        $body_lines[] = '';

        foreach ( $sanitized as $key => $value ) {
            $label = ucfirst( str_replace( [ '_', '-' ], ' ', $key ) );
            if ( is_array( $value ) ) {
                $body_lines[] = "{$label}: " . implode( ', ', $value );
            } else {
                $body_lines[] = "{$label}: {$value}";
            }
        }

        $body_lines[] = '';
        $body_lines[] = str_repeat( '─', 40 );
        $body_lines[] = 'IP: ' . $this->get_client_ip();
        $body_lines[] = 'Data: ' . wp_date( 'd/m/Y H:i:s' );
        $body_lines[] = 'Pagina: ' . wp_get_referer();

        $body = implode( "\n", $body_lines );

        // Headers
        $headers = [
            'Content-Type: text/plain; charset=UTF-8',
            'From: ' . $from_name . ' <' . get_option( 'admin_email' ) . '>',
        ];

        // Reply-To: use email field if present
        $reply_email = '';
        foreach ( $sanitized as $key => $value ) {
            if ( ! is_array( $value ) && is_email( $value ) ) {
                $reply_email = $value;
                break;
            }
        }
        if ( $reply_email ) {
            $reply_name = '';
            foreach ( $sanitized as $key => $value ) {
                if ( ! is_array( $value ) && ! is_email( $value ) && strlen( $value ) > 1 && strlen( $value ) < 100 ) {
                    $reply_name = $value;
                    break;
                }
            }
            $headers[] = 'Reply-To: ' . ( $reply_name ? "{$reply_name} <{$reply_email}>" : $reply_email );
        }

        // CC — stessa whitelist del destinatario principale per evitare relay
        $cc = sanitize_email( $config['email_cc'] ?? '' );
        if ( $cc && $this->is_recipient_allowed( $cc, $admin_email ) ) {
            $headers[] = 'Cc: ' . $cc;
        }

        // 9. Send email
        $sent = wp_mail( $to, "[{$site_name}] {$subject}", $body, $headers );

        // 10. Auto-reply
        if ( $sent && ! empty( $config['auto_reply'] ) && $reply_email ) {
            $ar_subject = sanitize_text_field( $config['auto_reply_subject'] ?? 'Grazie per averci contattato' );
            $ar_message = sanitize_textarea_field( $config['auto_reply_message'] ?? '' );

            if ( $ar_message ) {
                $ar_body  = $ar_message . "\n\n";
                $ar_body .= str_repeat( '─', 40 ) . "\n";
                $ar_body .= "Questo è un messaggio automatico da {$site_name}.\n";
                $ar_body .= "Non rispondere a questa email.\n";

                $ar_headers = [
                    'Content-Type: text/plain; charset=UTF-8',
                    'From: ' . $from_name . ' <' . get_option( 'admin_email' ) . '>',
                ];

                wp_mail( $reply_email, "[{$site_name}] {$ar_subject}", $ar_body, $ar_headers );
            }
        }

        // 11. Mailchimp integration
        if ( ! empty( $config['mailchimp_enabled'] ) ) {
            $this->send_to_mailchimp( $sanitized, $config );
        }

        // 12. Webhook integration
        if ( ! empty( $config['webhook_enabled'] ) ) {
            $this->send_webhook( $sanitized, $config );
        }

        // 12b. HubSpot integration
        $hubspot_enabled = ! empty( $config['hubspot_enabled'] );
        $hubspot_portal  = $config['hubspot_portal_id'] ?? '';
        $hubspot_form    = $config['hubspot_form_guid'] ?? '';
        if ( $hubspot_enabled ) {
            if ( $hubspot_portal !== '' ) {
                if ( $hubspot_form !== '' ) {
                    $hubspot_fields = [];
                    foreach ( $sanitized as $key => $value ) {
                        if ( is_string( $value ) ) {
                            $hubspot_fields[] = [
                                'name'  => $key,
                                'value' => $value,
                            ];
                        }
                    }
                    $hubspot_url = 'https://api.hsforms.com/submissions/v3/integration/submit/'
                        . sanitize_text_field( $hubspot_portal ) . '/' . sanitize_text_field( $hubspot_form );
                    $hubspot_body = [
                        'fields'  => $hubspot_fields,
                        'context' => [
                            'pageUri'   => wp_get_referer(),
                            'pageName'  => get_the_title(),
                            'ipAddress' => $this->get_client_ip(),
                        ],
                    ];
                    wp_remote_post( $hubspot_url, [
                        'headers' => [ 'Content-Type' => 'application/json' ],
                        'body'    => wp_json_encode( $hubspot_body ),
                        'timeout' => 10,
                    ] );
                }
            }
        }

        // 12c. ActiveCampaign integration
        if ( ! empty( $config['activecampaign_enabled'] ) ) {
            $this->send_to_activecampaign( $sanitized, $config );
        }

        // 12d. ConvertKit integration
        if ( ! empty( $config['convertkit_enabled'] ) ) {
            $this->send_to_convertkit( $sanitized, $config );
        }

        // 12e. Brevo (Sendinblue) integration
        if ( ! empty( $config['brevo_enabled'] ) ) {
            $this->send_to_brevo( $sanitized, $config );
        }

        // 13. Store submission in database if enabled
        $form_id = sanitize_text_field( $request->get_param( '_olo_form_id' ) ?? '' );
        $this->store_submission( $form_id, $sanitized, $config );

        // 14. Store in form submissions dashboard table
        if ( class_exists( 'Olobuild_Form_Submissions' ) ) {
            $form_name = $form_id ?: sanitize_text_field( $config['email_subject'] ?? 'Form' );
            Olobuild_Form_Submissions::save_submission( $form_name, $sanitized, $this->get_client_ip() );
        }

        if ( $sent ) {
            return new WP_REST_Response( [
                'success' => true,
                'data'    => [
                    'message'  => $config['success_message'] ?? 'Messaggio inviato con successo!',
                    'redirect' => ! empty( $config['redirect_url'] ) ? esc_url( $config['redirect_url'] ) : '',
                ],
            ], 200 );
        }

        return new WP_REST_Response( [
            'success' => false,
            'data'    => [ 'message' => $config['error_message'] ?? 'Errore durante l\'invio. Riprova.' ],
        ], 500 );
    }

    /**
     * Store form submission in the database if enabled.
     */
    private function store_submission( $form_id, $data, $form_settings ) {
        if ( empty( $form_settings['store_submissions'] ) ) {
            return;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'olo_submissions';

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- tabella custom del plugin (olo_submissions); nessun equivalente WP_Query; insert (scrittura) non cacheabile.
        $wpdb->insert( $table, [
            'form_id'    => sanitize_text_field( $form_id ),
            'data'       => wp_json_encode( $data ),
            'ip_address' => $this->get_client_ip(),
            'created_at' => current_time( 'mysql' ),
        ], [ '%s', '%s', '%s', '%s' ] );
    }

    /**
     * Send subscriber to Mailchimp via API v3.
     */
    private function send_to_mailchimp( $form_data, $config ) {
        $api_key = get_option( 'olo_mailchimp_api_key', '' );
        $list_id = sanitize_text_field( $config['mailchimp_list_id'] ?? '' );
        if ( empty( $api_key ) || empty( $list_id ) ) {
            return;
        }

        // Extract data center from API key (last part after dash)
        $dc = '';
        if ( str_contains( $api_key, '-' ) ) {
            $dc = substr( $api_key, strpos( $api_key, '-' ) + 1 );
        }
        if ( empty( $dc ) ) {
            return;
        }

        // Get email from form data
        $email_field = sanitize_key( $config['mailchimp_email_field'] ?? 'email' );
        $email       = sanitize_email( $form_data[ $email_field ] ?? '' );
        if ( empty( $email ) ) {
            return;
        }

        // Parse merge fields mapping (format: field_name=MERGE_TAG per line)
        $merge_fields = [];
        $mapping_raw  = $config['mailchimp_merge_fields'] ?? '';
        if ( $mapping_raw ) {
            $lines = array_filter( array_map( 'trim', explode( "\n", $mapping_raw ) ) );
            foreach ( $lines as $line ) {
                $parts = explode( '=', $line, 2 );
                if ( count( $parts ) === 2 ) {
                    $field_name = sanitize_key( trim( $parts[0] ) );
                    $merge_tag  = strtoupper( trim( $parts[1] ) );
                    if ( isset( $form_data[ $field_name ] ) ) {
                        $val = $form_data[ $field_name ];
                        $merge_fields[ $merge_tag ] = is_array( $val ) ? implode( ', ', $val ) : $val;
                    }
                }
            }
        }

        $body = [
            'email_address' => $email,
            'status_if_new' => 'subscribed',
            'status'        => 'subscribed',
        ];
        if ( ! empty( $merge_fields ) ) {
            $body['merge_fields'] = $merge_fields;
        }

        $url = "https://{$dc}.api.mailchimp.com/3.0/lists/{$list_id}/members/" . md5( strtolower( $email ) );

        wp_remote_request( $url, [
            'method'  => 'PUT',
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode( 'anystring:' . $api_key ),
                'Content-Type'  => 'application/json',
            ],
            'body'    => wp_json_encode( $body ),
            'timeout' => 10,
        ] );
    }

    /**
     * Send form data to external webhook.
     */
    private function send_webhook( $form_data, $config ) {
        $url    = esc_url_raw( $config['webhook_url'] ?? '' );
        $method = in_array( $config['webhook_method'] ?? 'POST', [ 'POST', 'PUT' ], true ) ? $config['webhook_method'] : 'POST';

        if ( empty( $url ) ) {
            return;
        }

        wp_remote_request( $url, [
            'method'  => $method,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body'    => wp_json_encode( [
                'form_data'  => $form_data,
                'submitted'  => current_time( 'c' ),
                'site'       => get_bloginfo( 'name' ),
                'page'       => wp_get_referer(),
                'ip'         => $this->get_client_ip(),
            ] ),
            'timeout' => 10,
        ] );
    }

    /**
     * Send contact to ActiveCampaign via API v3.
     */
    private function send_to_activecampaign( $form_data, $config ) {
        $api_url = rtrim( get_option( 'olo_activecampaign_url', '' ), '/' );
        $api_key = get_option( 'olo_activecampaign_key', '' );
        $list_id = sanitize_text_field( $config['activecampaign_list_id'] ?? '' );

        if ( empty( $api_url ) || empty( $api_key ) ) return;

        $email_field = sanitize_key( $config['activecampaign_email_field'] ?? 'email' );
        $email = sanitize_email( $form_data[ $email_field ] ?? '' );
        if ( empty( $email ) ) return;

        $contact = [ 'email' => $email ];
        if ( ! empty( $form_data['first_name'] ) ) $contact['firstName'] = $form_data['first_name'];
        if ( ! empty( $form_data['last_name'] ) )  $contact['lastName']  = $form_data['last_name'];
        if ( ! empty( $form_data['name'] ) ) {
            $parts = explode( ' ', $form_data['name'], 2 );
            $contact['firstName'] = $parts[0];
            if ( isset( $parts[1] ) ) $contact['lastName'] = $parts[1];
        }
        if ( ! empty( $form_data['phone'] ) ) $contact['phone'] = $form_data['phone'];

        // Create/update contact
        $response = wp_remote_post( $api_url . '/api/3/contact/sync', [
            'headers' => [
                'Api-Token'    => $api_key,
                'Content-Type' => 'application/json',
            ],
            'body'    => wp_json_encode( [ 'contact' => $contact ] ),
            'timeout' => 10,
        ] );

        // Add to list if list_id provided
        if ( ! is_wp_error( $response ) && ! empty( $list_id ) ) {
            $body = json_decode( wp_remote_retrieve_body( $response ), true );
            $contact_id = $body['contact']['id'] ?? null;
            if ( $contact_id ) {
                wp_remote_post( $api_url . '/api/3/contactLists', [
                    'headers' => [
                        'Api-Token'    => $api_key,
                        'Content-Type' => 'application/json',
                    ],
                    'body'    => wp_json_encode( [ 'contactList' => [ 'list' => (int) $list_id, 'contact' => (int) $contact_id, 'status' => 1 ] ] ),
                    'timeout' => 10,
                ] );
            }
        }
    }

    /**
     * Send subscriber to ConvertKit via API v3.
     */
    private function send_to_convertkit( $form_data, $config ) {
        $api_key = get_option( 'olo_convertkit_key', '' );
        $form_id = sanitize_text_field( $config['convertkit_form_id'] ?? '' );

        if ( empty( $api_key ) || empty( $form_id ) ) return;

        $email_field = sanitize_key( $config['convertkit_email_field'] ?? 'email' );
        $email = sanitize_email( $form_data[ $email_field ] ?? '' );
        if ( empty( $email ) ) return;

        $body = [
            'api_key'    => $api_key,
            'email'      => $email,
        ];
        if ( ! empty( $form_data['first_name'] ) ) $body['first_name'] = $form_data['first_name'];
        if ( ! empty( $form_data['name'] ) ) {
            $body['first_name'] = explode( ' ', $form_data['name'] )[0];
        }

        wp_remote_post( "https://api.convertkit.com/v3/forms/{$form_id}/subscribe", [
            'headers' => [ 'Content-Type' => 'application/json' ],
            'body'    => wp_json_encode( $body ),
            'timeout' => 10,
        ] );
    }

    /**
     * Send contact to Brevo (Sendinblue) via API v3.
     */
    private function send_to_brevo( $form_data, $config ) {
        $api_key = get_option( 'olo_brevo_key', '' );
        $list_id = (int) ( $config['brevo_list_id'] ?? 0 );

        if ( empty( $api_key ) ) return;

        $email_field = sanitize_key( $config['brevo_email_field'] ?? 'email' );
        $email = sanitize_email( $form_data[ $email_field ] ?? '' );
        if ( empty( $email ) ) return;

        $body = [ 'email' => $email, 'updateEnabled' => true ];
        $attrs = [];
        if ( ! empty( $form_data['first_name'] ) ) $attrs['FIRSTNAME'] = $form_data['first_name'];
        if ( ! empty( $form_data['last_name'] ) )  $attrs['LASTNAME']  = $form_data['last_name'];
        if ( ! empty( $form_data['name'] ) ) {
            $parts = explode( ' ', $form_data['name'], 2 );
            $attrs['FIRSTNAME'] = $parts[0];
            if ( isset( $parts[1] ) ) $attrs['LASTNAME'] = $parts[1];
        }
        if ( ! empty( $attrs ) ) $body['attributes'] = $attrs;
        if ( $list_id > 0 ) $body['listIds'] = [ $list_id ];

        wp_remote_post( 'https://api.brevo.com/v3/contacts', [
            'headers' => [
                'api-key'      => $api_key,
                'Content-Type' => 'application/json',
            ],
            'body'    => wp_json_encode( $body ),
            'timeout' => 10,
        ] );
    }

    /**
     * Clean up uploaded files older than 30 days.
     * Runs via WP-Cron daily.
     */
    public static function cleanup_old_uploads() {
        $upload_dir = wp_upload_dir();
        $olo_dir    = $upload_dir['basedir'] . '/olobuild-uploads';

        if ( ! is_dir( $olo_dir ) ) {
            return;
        }

        $max_age = 30 * DAY_IN_SECONDS;
        $now     = time();
        $files   = glob( $olo_dir . '/*' );

        if ( ! is_array( $files ) ) {
            return;
        }

        foreach ( $files as $file ) {
            if ( is_file( $file ) ) {
                $basename = basename( $file );
                // Skip .htaccess and index.php
                if ( $basename === '.htaccess' ) { continue; }
                if ( $basename === 'index.php' ) { continue; }

                if ( ( $now - filemtime( $file ) ) > $max_age ) {
                    wp_delete_file( $file );
                }
            }
        }
    }

    /**
     * Export submissions as CSV (REST endpoint).
     */
    public function export_csv( $request ) {
        global $wpdb;
        $table   = $wpdb->prefix . 'olo_submissions';
        $form_id = sanitize_text_field( $request->get_param( 'form_id' ) ?? '' );

        if ( $form_id ) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- tabella custom del plugin (olo_submissions); $table da $wpdb->prefix; il valore utente $form_id passa da $wpdb->prepare (%s); export on-demand non cacheabile.
            $rows = $wpdb->get_results(
                // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- query su tabella custom del plugin: nome tabella da $wpdb->prefix / colonna whitelist; tutti i valori utente passano da $wpdb->prepare
                $wpdb->prepare( "SELECT * FROM $table WHERE form_id = %s ORDER BY created_at DESC", $form_id ),
                ARRAY_A
            );
        } else {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- tabella custom del plugin (olo_submissions); $table da $wpdb->prefix; nessun valore utente nella query; export on-demand non cacheabile.
            $rows = $wpdb->get_results( "SELECT * FROM $table ORDER BY created_at DESC", ARRAY_A );
        }

        if ( empty( $rows ) ) {
            return new WP_REST_Response( [
                'success' => false,
                'data'    => [ 'message' => 'Nessuna submission trovata.' ],
            ], 404 );
        }

        // Collect all field keys across all submissions
        $all_keys = [];
        $decoded_rows = [];
        foreach ( $rows as $row ) {
            $fields = json_decode( $row['data'], true );
            if ( ! is_array( $fields ) ) {
                $fields = [];
            }
            $decoded_rows[] = [
                'id'         => $row['id'],
                'form_id'    => $row['form_id'],
                'fields'     => $fields,
                'ip_address' => $row['ip_address'],
                'created_at' => $row['created_at'],
            ];
            foreach ( array_keys( $fields ) as $k ) {
                if ( ! in_array( $k, $all_keys, true ) ) {
                    $all_keys[] = $k;
                }
            }
        }

        // Build CSV
        header( 'Content-Type: text/csv; charset=UTF-8' );
        header( 'Content-Disposition: attachment; filename="submissions-' . ( $form_id ?: 'all' ) . '.csv"' );

        $out = fopen( 'php://output', 'w' );
        // BOM for Excel UTF-8
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- CSV streamed to php://output (download), not a filesystem file
        fwrite( $out, "\xEF\xBB\xBF" );

        // Header row
        $header = array_merge( [ 'ID', 'Form ID' ], array_map( 'ucfirst', $all_keys ), [ 'IP', 'Data' ] );
        fputcsv( $out, $header, ';' );

        // Data rows
        foreach ( $decoded_rows as $dr ) {
            $row_data = [ $dr['id'], $dr['form_id'] ];
            foreach ( $all_keys as $k ) {
                $val = $dr['fields'][ $k ] ?? '';
                if ( is_array( $val ) ) {
                    $val = implode( ', ', $val );
                }
                $row_data[] = $val;
            }
            $row_data[] = $dr['ip_address'];
            $row_data[] = $dr['created_at'];
            // olobuild_csv_safe: i campi arrivano dal form pubblico → anti CSV formula injection.
            fputcsv( $out, array_map( 'olobuild_csv_safe', $row_data ), ';' );
        }

        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- closing the php://output CSV stream
        fclose( $out );
        exit;
    }
}
