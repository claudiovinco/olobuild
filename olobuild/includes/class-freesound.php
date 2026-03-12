<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Freesound.org API integration for free sound/audio search.
 * API docs: https://freesound.org/docs/api/
 */
class Olo_Freesound {

    private $api_base = 'https://freesound.org/apiv2';

    public function init() {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    public function register_routes() {
        register_rest_route( 'olo/v1', '/freesound/search', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'search' ],
            'permission_callback' => function () {
                return current_user_can( 'upload_files' );
            },
            'args' => [
                'query'    => [ 'required' => true, 'type' => 'string' ],
                'page'     => [ 'default' => 1, 'type' => 'integer', 'sanitize_callback' => 'absint' ],
                'per_page' => [ 'default' => 30, 'type' => 'integer', 'sanitize_callback' => 'absint' ],
                'filter'   => [ 'default' => '', 'type' => 'string' ],
            ],
        ] );

        register_rest_route( 'olo/v1', '/freesound/download', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'download' ],
            'permission_callback' => function () {
                return current_user_can( 'upload_files' );
            },
            'args' => [
                'sound_id'    => [ 'required' => true, 'type' => 'integer', 'sanitize_callback' => 'absint' ],
                'name'        => [ 'default' => '', 'type' => 'string' ],
                'username'    => [ 'default' => '', 'type' => 'string' ],
                'preview_url' => [ 'default' => '', 'type' => 'string' ],
            ],
        ] );
    }

    private function get_api_key() {
        return get_option( 'olo_freesound_api_key', '' );
    }

    public function search( $request ) {
        $api_key = $this->get_api_key();
        if ( empty( $api_key ) ) {
            return new WP_Error( 'no_api_key', 'Freesound API key non configurata. Vai in Olobuild → Impostazioni.', [ 'status' => 400 ] );
        }

        $query    = sanitize_text_field( $request['query'] );
        $page     = max( 1, intval( $request['page'] ) );
        $per_page = min( 50, max( 1, intval( $request['per_page'] ) ) );
        $filter   = sanitize_text_field( $request['filter'] ?? '' );

        $url = add_query_arg( [
            'query'    => $query,
            'page'     => $page,
            'page_size' => $per_page,
            'fields'   => 'id,name,tags,description,duration,previews,images,username,license,avg_rating,num_downloads,filesize,type,channels,samplerate,bitrate',
            'token'    => $api_key,
        ], $this->api_base . '/search/text/' );

        if ( ! empty( $filter ) ) {
            $url = add_query_arg( 'filter', $filter, $url );
        }

        $response = wp_remote_get( $url, [
            'timeout' => 15,
            'headers' => [ 'User-Agent' => 'Olobuild/1.0' ],
        ] );

        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'api_error', $response->get_error_message(), [ 'status' => 500 ] );
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( empty( $body ) || isset( $body['detail'] ) ) {
            return new WP_Error( 'api_error', $body['detail'] ?? 'Errore API Freesound', [ 'status' => 500 ] );
        }

        $results = [];
        foreach ( $body['results'] ?? [] as $sound ) {
            $results[] = [
                'id'           => $sound['id'],
                'name'         => $sound['name'] ?? '',
                'description'  => wp_trim_words( wp_strip_all_tags( $sound['description'] ?? '' ), 20 ),
                'duration'     => round( $sound['duration'] ?? 0, 1 ),
                'username'     => $sound['username'] ?? '',
                'license'      => $sound['license'] ?? '',
                'preview_mp3'  => $sound['previews']['preview-hq-mp3'] ?? $sound['previews']['preview-lq-mp3'] ?? '',
                'preview_ogg'  => $sound['previews']['preview-hq-ogg'] ?? $sound['previews']['preview-lq-ogg'] ?? '',
                'waveform'     => $sound['images']['waveform_l'] ?? $sound['images']['waveform_m'] ?? '',
                'spectral'     => $sound['images']['spectral_l'] ?? $sound['images']['spectral_m'] ?? '',
                'tags'         => array_slice( $sound['tags'] ?? [], 0, 5 ),
                'rating'       => $sound['avg_rating'] ?? 0,
                'downloads'    => $sound['num_downloads'] ?? 0,
                'filesize'     => $sound['filesize'] ?? 0,
                'type'         => $sound['type'] ?? '',
                'channels'     => $sound['channels'] ?? 0,
                'samplerate'   => $sound['samplerate'] ?? 0,
            ];
        }

        return rest_ensure_response( [
            'results'     => $results,
            'total'       => $body['count'] ?? 0,
            'total_pages' => ceil( ( $body['count'] ?? 0 ) / $per_page ),
        ] );
    }

    public function download( $request ) {
        $sound_id    = absint( $request['sound_id'] );
        $name        = sanitize_text_field( $request['name'] );
        $username    = sanitize_text_field( $request['username'] );
        $preview_url = esc_url_raw( $request['preview_url'] );

        if ( empty( $preview_url ) ) {
            return new WP_Error( 'no_url', 'URL preview non fornito.', [ 'status' => 400 ] );
        }

        // Validate URL is from Freesound CDN
        if ( strpos( $preview_url, 'freesound.org' ) === false ) {
            return new WP_Error( 'invalid_url', 'URL non valido.', [ 'status' => 400 ] );
        }

        // Ensure required WP admin functions are available
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        // Increase timeout for audio files
        add_filter( 'http_request_timeout', function () { return 120; } );

        $tmp_file = download_url( $preview_url );
        if ( is_wp_error( $tmp_file ) ) {
            return new WP_Error( 'download_failed', $tmp_file->get_error_message(), [ 'status' => 500 ] );
        }

        // Determine extension from URL
        $ext = '.mp3';
        if ( strpos( $preview_url, '.ogg' ) !== false ) {
            $ext = '.ogg';
        }

        $filename = sanitize_file_name( ( $name ?: 'freesound-' . $sound_id ) . $ext );

        $file_array = [
            'name'     => $filename,
            'tmp_name' => $tmp_file,
        ];

        $attachment_id = media_handle_sideload( $file_array, 0 );
        if ( is_wp_error( $attachment_id ) ) {
            @unlink( $tmp_file );
            return new WP_Error( 'sideload_failed', $attachment_id->get_error_message(), [ 'status' => 500 ] );
        }

        // Add caption with attribution
        $caption = '';
        if ( $username ) {
            $caption = 'Audio: "' . $name . '" by ' . $username . ' — Freesound.org';
        }
        if ( $caption ) {
            wp_update_post( [ 'ID' => $attachment_id, 'post_excerpt' => $caption ] );
        }

        return rest_ensure_response( [
            'id'      => $attachment_id,
            'url'     => wp_get_attachment_url( $attachment_id ),
            'caption' => $caption,
        ] );
    }
}
