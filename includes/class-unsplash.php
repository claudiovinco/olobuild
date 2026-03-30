<?php
/**
 * Olo_Unsplash — Integrazione Unsplash per Olobuild.
 *
 * Fornisce 2 endpoint REST:
 *   GET  olo/v1/unsplash/search   — Cerca foto su Unsplash
 *   POST olo/v1/unsplash/download — Scarica foto nel WP Media Library
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Unsplash {

    /**
     * Restituisce la Access Key Unsplash da wp_options (con fallback hardcoded).
     */
    private static function get_access_key() {
        return get_option( 'olo_unsplash_api_key', 'mAtcGSa97BuefUN55vaORLV6YvFH4SHjdcCFbq_gJ84' );
    }

    public function init() {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    public function register_routes() {
        register_rest_route( 'olo/v1', '/unsplash/search', [
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
            ],
        ] );

        register_rest_route( 'olo/v1', '/unsplash/download', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'download' ],
            'permission_callback' => function () {
                return current_user_can( 'upload_files' );
            },
        ] );
    }

    /**
     * Proxy ricerca Unsplash.
     */
    public function search( $request ) {
        $query       = $request->get_param( 'query' );
        $page        = $request->get_param( 'page' ) ?: 1;
        $per_page    = min( $request->get_param( 'per_page' ) ?: 30, 30 );
        $orientation = sanitize_text_field( $request->get_param( 'orientation' ) );

        $params = [
            'query'    => $query,
            'page'     => $page,
            'per_page' => $per_page,
        ];
        // Unsplash: landscape, portrait, squarish
        $ori_map = [ 'landscape' => 'landscape', 'portrait' => 'portrait', 'square' => 'squarish' ];
        if ( ! empty( $orientation ) && isset( $ori_map[ $orientation ] ) ) {
            $params['orientation'] = $ori_map[ $orientation ];
        }

        $response = wp_remote_get( 'https://api.unsplash.com/search/photos?' . http_build_query( $params ), [
            'headers' => [ 'Authorization' => 'Client-ID ' . self::get_access_key() ],
            'timeout' => 15,
        ] );

        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'unsplash_error', $response->get_error_message(), [ 'status' => 502 ] );
        }

        $code = wp_remote_retrieve_response_code( $response );
        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( $code !== 200 ) {
            $msg = $body['errors'][0] ?? 'Unsplash API error';
            return new WP_Error( 'unsplash_api', $msg, [ 'status' => $code ] );
        }

        // Mappa solo i campi necessari
        $results = [];
        foreach ( $body['results'] ?? [] as $photo ) {
            $results[] = [
                'id'                => $photo['id'],
                'thumb'             => $photo['urls']['small'] ?? '',
                'regular'           => $photo['urls']['regular'] ?? '',
                'alt'               => $photo['alt_description'] ?? '',
                'photographer'      => $photo['user']['name'] ?? '',
                'photographer_url'  => $photo['user']['links']['html'] ?? '',
                'download_location' => $photo['links']['download_location'] ?? '',
                'width'             => $photo['width'] ?? 0,
                'height'            => $photo['height'] ?? 0,
            ];
        }

        return rest_ensure_response( [
            'results'     => $results,
            'total'       => $body['total'] ?? 0,
            'total_pages' => $body['total_pages'] ?? 0,
        ] );
    }

    /**
     * Scarica una foto Unsplash nel WP Media Library.
     */
    public function download( $request ) {
        $params = $request->get_json_params();

        $download_location = $params['download_location'] ?? '';
        $regular_url       = $params['regular_url'] ?? '';
        $alt               = sanitize_text_field( $params['alt'] ?? '' );
        $photographer      = sanitize_text_field( $params['photographer'] ?? '' );
        $photo_id          = sanitize_text_field( $params['photo_id'] ?? '' );

        if ( empty( $regular_url ) ) {
            return new WP_Error( 'missing_url', 'URL immagine mancante', [ 'status' => 400 ] );
        }

        // 1. Tracking download (richiesto da Unsplash guidelines)
        if ( $download_location ) {
            wp_remote_get( $download_location, [
                'headers' => [ 'Authorization' => 'Client-ID ' . self::get_access_key() ],
                'timeout' => 5,
            ] );
        }

        // 2. Scarica il file temporaneo
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $tmp_file = download_url( $regular_url, 30 );
        if ( is_wp_error( $tmp_file ) ) {
            return new WP_Error( 'download_failed', $tmp_file->get_error_message(), [ 'status' => 502 ] );
        }

        // Validate MIME type
        $mime = wp_check_filetype( $tmp_file );
        if ( empty( $mime['type'] ) ) {
            $finfo_mime = function_exists('mime_content_type') ? mime_content_type( $tmp_file ) : '';
            $allowed = [ 'image/jpeg', 'image/png', 'image/webp', 'image/gif', 'video/mp4', 'video/webm' ];
            if ( $finfo_mime && ! in_array( $finfo_mime, $allowed, true ) ) {
                @unlink( $tmp_file );
                return new WP_Error( 'invalid_mime', 'Il file scaricato non è un tipo media valido.' );
            }
        }

        // 3. Crea attachment nel WP Media Library
        $filename  = 'unsplash-' . $photo_id . '.jpg';
        $file_data = [
            'name'     => $filename,
            'type'     => 'image/jpeg',
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

        $caption = $photographer ? 'Photo by ' . $photographer . ' on Unsplash' : '';

        $attachment_data = [
            'post_title'   => $alt ?: ( 'Unsplash ' . $photo_id ),
            'post_content' => '',
            'post_excerpt' => $caption,
            'post_status'  => 'inherit',
            'post_mime_type' => $upload['type'],
        ];

        $attach_id = wp_insert_attachment( $attachment_data, $upload['file'] );
        if ( is_wp_error( $attach_id ) ) {
            return new WP_Error( 'attach_failed', $attach_id->get_error_message(), [ 'status' => 500 ] );
        }

        // Alt text
        if ( $alt ) {
            update_post_meta( $attach_id, '_wp_attachment_image_alt', $alt );
        }

        // Genera metadata (thumbnails, sizes)
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
