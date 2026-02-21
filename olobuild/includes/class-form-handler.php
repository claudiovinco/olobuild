<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Form_Handler {

    public function init() {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    public function register_routes() {
        register_rest_route( 'olo/v1', '/form/submit', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'handle_submit' ],
            'permission_callback' => '__return_true', // Public endpoint (contact form)
        ] );
    }

    /**
     * Handle form submission.
     */
    public function handle_submit( $request ) {
        // 1. Decode form config
        $config_raw = $request->get_param( '_olo_form_config' );
        $config     = json_decode( base64_decode( $config_raw ), true );
        if ( ! is_array( $config ) ) {
            return new WP_REST_Response( [
                'success' => false,
                'data'    => [ 'message' => 'Configurazione form non valida.' ],
            ], 400 );
        }

        // 3. Honeypot check
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

        // 4. Rate limiting
        if ( ! empty( $config['rate_limit'] ) ) {
            $ip       = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            $key      = 'olo_form_rl_' . md5( $ip );
            $max      = absint( $config['rate_limit_max'] ?? 5 );
            $window   = absint( $config['rate_limit_window'] ?? 60 ) * MINUTE_IN_SECONDS;
            $count    = (int) get_transient( $key );

            if ( $count >= $max ) {
                return new WP_REST_Response( [
                    'success' => false,
                    'data'    => [ 'message' => 'Troppe richieste. Riprova tra qualche minuto.' ],
                ], 429 );
            }

            set_transient( $key, $count + 1, $window );
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
        $to = sanitize_email( $config['email_to'] ?? '' );
        if ( empty( $to ) ) {
            $to = get_option( 'admin_email' );
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
        $body_lines[] = 'IP: ' . ( $_SERVER['REMOTE_ADDR'] ?? 'N/A' );
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

        // CC
        $cc = sanitize_email( $config['email_cc'] ?? '' );
        if ( $cc ) {
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
}
