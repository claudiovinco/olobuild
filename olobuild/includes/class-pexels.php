<?php
/**
 * Olo_Pexels — Integrazione Pexels per Olobuild.
 *
 * Fornisce 2 endpoint REST:
 *   GET  olo/v1/pexels/search   — Cerca foto su Pexels
 *   POST olo/v1/pexels/download — Scarica foto nel WP Media Library
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Pexels {

    const API_KEY = '***REMOVED-API-KEY***';

    public function init() {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    public function register_routes() {
        register_rest_route( 'olo/v1', '/pexels/search', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'search' ],
            'permission_callback' => function () {
                return current_user_can( 'upload_files' );
            },
            'args' => [
                'query'    => [ 'required' => true, 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ],
                'page'     => [ 'default' => 1, 'type' => 'integer', 'sanitize_callback' => 'absint' ],
                'per_page' => [ 'default' => 30, 'type' => 'integer', 'sanitize_callback' => 'absint' ],
            ],
        ] );

        register_rest_route( 'olo/v1', '/pexels/download', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'download' ],
            'permission_callback' => function () {
                return current_user_can( 'upload_files' );
            },
        ] );
    }

    /**
     * Proxy ricerca Pexels.
     */
    public function search( $request ) {
        $query    = $request->get_param( 'query' );
        $page     = $request->get_param( 'page' ) ?: 1;
        $per_page = min( $request->get_param( 'per_page' ) ?: 30, 30 );

        $response = wp_remote_get( 'https://api.pexels.com/v1/search?' . http_build_query( [
            'query'    => $query,
            'page'     => $page,
            'per_page' => $per_page,
        ] ), [
            'headers' => [ 'Authorization' => self::API_KEY ],
            'timeout' => 15,
        ] );

        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'pexels_error', $response->get_error_message(), [ 'status' => 502 ] );
        }

        $code = wp_remote_retrieve_response_code( $response );
        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( $code !== 200 ) {
            $msg = $body['error'] ?? 'Pexels API error';
            return new WP_Error( 'pexels_api', $msg, [ 'status' => $code ] );
        }

        $results = [];
        foreach ( $body['photos'] ?? [] as $photo ) {
            $results[] = [
                'id'               => $photo['id'],
                'thumb'            => $photo['src']['medium'] ?? '',
                'regular'          => $photo['src']['large'] ?? '',
                'alt'              => $photo['alt'] ?? '',
                'photographer'     => $photo['photographer'] ?? '',
                'photographer_url' => $photo['photographer_url'] ?? '',
                'width'            => $photo['width'] ?? 0,
                'height'           => $photo['height'] ?? 0,
            ];
        }

        $total       = $body['total_results'] ?? 0;
        $total_pages = $per_page > 0 ? (int) ceil( $total / $per_page ) : 0;

        return rest_ensure_response( [
            'results'     => $results,
            'total'       => $total,
            'total_pages' => $total_pages,
        ] );
    }

    /**
     * Scarica una foto Pexels nel WP Media Library.
     */
    public function download( $request ) {
        $params = $request->get_json_params();

        $regular_url  = $params['regular_url'] ?? '';
        $alt          = sanitize_text_field( $params['alt'] ?? '' );
        $photographer = sanitize_text_field( $params['photographer'] ?? '' );
        $photo_id     = sanitize_text_field( $params['photo_id'] ?? '' );

        if ( empty( $regular_url ) ) {
            return new WP_Error( 'missing_url', 'URL immagine mancante', [ 'status' => 400 ] );
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $tmp_file = download_url( $regular_url, 30 );
        if ( is_wp_error( $tmp_file ) ) {
            return new WP_Error( 'download_failed', $tmp_file->get_error_message(), [ 'status' => 502 ] );
        }

        $filename  = 'pexels-' . $photo_id . '.jpg';
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

        $caption = $photographer ? 'Photo by ' . $photographer . ' on Pexels' : '';

        $attachment_data = [
            'post_title'     => $alt ?: ( 'Pexels ' . $photo_id ),
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
