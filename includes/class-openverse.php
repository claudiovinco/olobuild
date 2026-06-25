<?php
/**
 * Olo_Openverse — Integrazione Openverse per Olobuild.
 *
 * Fornisce 2 endpoint REST:
 *   GET  olo/v1/openverse/search   — Cerca immagini CC su Openverse
 *   POST olo/v1/openverse/download — Scarica immagine nel WP Media Library
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Openverse {

    const API_BASE = 'https://api.openverse.org/v1';

    public function init() {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    public function register_routes() {
        register_rest_route( 'olo/v1', '/openverse/search', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'search' ],
            'permission_callback' => function () {
                return current_user_can( 'upload_files' );
            },
            'args' => [
                'query'       => [ 'required' => true, 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ],
                'page'        => [ 'default' => 1, 'type' => 'integer', 'sanitize_callback' => 'absint' ],
                'per_page'    => [ 'default' => 30, 'type' => 'integer', 'sanitize_callback' => 'absint' ],
                'orientation' => [ 'default' => '', 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ],
                'size'        => [ 'default' => '', 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ],
            ],
        ] );

        register_rest_route( 'olo/v1', '/openverse/download', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'download' ],
            'permission_callback' => function () {
                return current_user_can( 'upload_files' );
            },
        ] );
    }

    /**
     * Proxy ricerca Openverse.
     */
    public function search( $request ) {
        $query       = $request->get_param( 'query' );
        $page        = $request->get_param( 'page' ) ?: 1;
        $per_page    = min( $request->get_param( 'per_page' ) ?: 20, 20 );
        $orientation = sanitize_text_field( $request->get_param( 'orientation' ) );
        $size        = sanitize_text_field( $request->get_param( 'size' ) );

        $params = [
            'q'         => $query,
            'page'      => $page,
            'page_size' => $per_page,
            'mature'    => 'false',
        ];
        // Openverse: tall, wide, square
        $ori_map = [ 'landscape' => 'wide', 'portrait' => 'tall', 'square' => 'square' ];
        if ( ! empty( $orientation ) && isset( $ori_map[ $orientation ] ) ) {
            $params['aspect_ratio'] = $ori_map[ $orientation ];
        }
        // Openverse: small, medium, large
        if ( in_array( $size, [ 'small', 'medium', 'large' ], true ) ) {
            $params['size'] = $size;
        }

        $response = wp_remote_get( self::API_BASE . '/images/?' . http_build_query( $params ), [
            'timeout' => 15,
            'headers' => [
                'User-Agent' => 'Olobuild/1.0 (WordPress Plugin)',
            ],
        ] );

        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'openverse_error', $response->get_error_message(), [ 'status' => 502 ] );
        }

        $code = wp_remote_retrieve_response_code( $response );
        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( $code !== 200 ) {
            $msg = $body['detail'] ?? ( $body['message'] ?? 'Openverse API error' );
            return new WP_Error( 'openverse_api', $msg, [ 'status' => $code ] );
        }

        $results = [];
        foreach ( $body['results'] ?? [] as $photo ) {
            // Openverse thumbnail field is an API endpoint, not a direct image URL.
            // Build a usable thumbnail: for Flickr images, replace _b suffix with _n (320px).
            $full_url = $photo['url'] ?? '';
            $thumb_url = $full_url;
            if ( ! empty( $full_url ) && str_contains( $full_url, 'staticflickr.com' ) ) {
                $thumb_url = preg_replace( '/_[a-z](\.\w+)$/i', '_n$1', $full_url );
            }

            $results[] = [
                'id'               => $photo['id'] ?? '',
                'thumb'            => $thumb_url,
                'regular'          => $full_url,
                'alt'              => $photo['title'] ?? '',
                'photographer'     => $photo['creator'] ?? '',
                'photographer_url' => $photo['creator_url'] ?? '',
                'license'          => $photo['license'] ?? '',
                'license_url'      => $photo['license_url'] ?? '',
                'attribution'      => $photo['attribution'] ?? '',
                'source'           => $photo['source'] ?? '',
                'width'            => $photo['width'] ?? 0,
                'height'           => $photo['height'] ?? 0,
            ];
        }

        $total       = $body['result_count'] ?? 0;
        $total_pages = $body['page_count'] ?? 0;

        return rest_ensure_response( [
            'results'     => $results,
            'total'       => $total,
            'total_pages' => $total_pages,
        ] );
    }

    /**
     * Scarica un'immagine Openverse nel WP Media Library.
     */
    public function download( $request ) {
        $params = $request->get_json_params();

        $regular_url  = $params['regular_url'] ?? '';
        $alt          = sanitize_text_field( $params['alt'] ?? '' );
        $photographer = sanitize_text_field( $params['photographer'] ?? '' );
        $photo_id     = sanitize_text_field( $params['photo_id'] ?? '' );
        $attribution  = sanitize_text_field( $params['attribution'] ?? '' );
        $license      = sanitize_text_field( $params['license'] ?? '' );
        $source       = sanitize_text_field( $params['source'] ?? '' );

        if ( empty( $regular_url ) ) {
            return new WP_Error( 'missing_url', 'URL immagine mancante', [ 'status' => 400 ] );
        }
        // Anti-SSRF: Openverse aggrega CDN diversi → niente allowlist host,
        // ma schema http/https + blocco IP privati/loopback via wp_http_validate_url.
        if ( ! olo_validate_remote_media_url( $regular_url ) ) {
            return new WP_Error( 'invalid_url', 'URL non consentito.', [ 'status' => 400 ] );
        }

        // Hotlink mode: skip sideload
        $behavior = olo_stockmedia_behavior();
        if ( empty( $behavior['download_local'] ) ) {
            $caption_hotlink = $attribution ? wp_strip_all_tags( $attribution )
                : ( $photographer ? $photographer . ' / Openverse' : '' );
            return rest_ensure_response( [
                'id'      => 0,
                'url'     => $regular_url,
                'alt'     => $alt,
                'caption' => $caption_hotlink,
                'hotlink' => true,
            ] );
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        // Anti-SSRF rafforzato: Openverse aggrega CDN eterogenei (niente allowlist host),
        // quindi forziamo reject_unsafe_urls solo per questa richiesta. Cosi' WP_Http rivalida
        // OGNI hop di redirect contro wp_http_validate_url (blocca il DNS-rebinding verso IP
        // interni 127.0.0.1/169.254.169.254/10.x ecc.), cosa che download_url() di base non fa.
        $olo_safe_http = function ( $args ) {
            $args['reject_unsafe_urls'] = true;
            $args['redirection']        = 3;
            return $args;
        };
        add_filter( 'http_request_args', $olo_safe_http, 999 );
        $tmp_file = download_url( $regular_url, 30 );
        remove_filter( 'http_request_args', $olo_safe_http, 999 );
        if ( is_wp_error( $tmp_file ) ) {
            return new WP_Error( 'download_failed', $tmp_file->get_error_message(), [ 'status' => 502 ] );
        }

        // Optimize: WebP conversion se richiesto (solo jpg/png)
        if ( ! empty( $behavior['optimize_on_download'] ) ) {
            $webp = olo_convert_to_webp( $tmp_file, 82 );
            if ( $webp && $webp !== $tmp_file ) {
                @unlink( $tmp_file );
                $tmp_file = $webp;
            }
        }

        // Detect extension from URL or default to jpg
        $ext = pathinfo( wp_parse_url( $regular_url, PHP_URL_PATH ), PATHINFO_EXTENSION );
        $ext = preg_replace( '/[^a-z0-9]/i', '', $ext );
        if ( ! in_array( strtolower( $ext ), [ 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg' ], true ) ) {
            $ext = 'jpg';
        }
        if ( str_ends_with( $tmp_file, '.webp' ) ) {
            $ext = 'webp';
        }

        $filename  = 'openverse-' . sanitize_file_name( substr( $photo_id, 0, 36 ) ) . '.' . $ext;

        // Detect MIME type
        $mime = wp_check_filetype( $filename );
        $mime_type = $mime['type'] ?: 'image/jpeg';

        $file_data = [
            'name'     => $filename,
            'type'     => $mime_type,
            'tmp_name' => $tmp_file,
            'error'    => 0,
            'size'     => filesize( $tmp_file ),
        ];

        $overrides = [ 'test_form' => false ];
        $upload    = wp_handle_sideload( $file_data, $overrides );

        if ( ! empty( $upload['error'] ) ) {
            @unlink( $tmp_file );
            return new WP_Error( 'upload_failed', $upload['error'], [ 'status' => 500 ] );
        }

        // Build caption with attribution
        $caption = '';
        if ( $attribution ) {
            $caption = wp_strip_all_tags( $attribution );
        } elseif ( $photographer ) {
            $source_label = $source ? ucfirst( $source ) : 'Openverse';
            $caption = $photographer . ' / ' . $source_label;
            if ( $license ) {
                $caption .= ' (' . strtoupper( $license ) . ')';
            }
        }

        $attachment_data = [
            'post_title'     => $alt ?: ( 'Openverse ' . substr( $photo_id, 0, 8 ) ),
            'post_content'   => '',
            'post_excerpt'   => $caption,
            'post_status'    => 'inherit',
            'post_mime_type' => $upload['type'],
        ];

        $attach_id = wp_insert_attachment( $attachment_data, $upload['file'] );
        if ( is_wp_error( $attach_id ) ) {
            return new WP_Error( 'attach_failed', $attach_id->get_error_message(), [ 'status' => 500 ] );
        }

        if ( $alt ) {
            update_post_meta( $attach_id, '_wp_attachment_image_alt', $alt );
        }

        $metadata = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
        wp_update_attachment_metadata( $attach_id, $metadata );

        return rest_ensure_response( [
            'id'      => $attach_id,
            'url'     => $upload['url'],
            'alt'     => $alt,
            'caption' => $caption,
        ] );
    }
}
