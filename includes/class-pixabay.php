<?php
/**
 * Olo_Pixabay — Integrazione Pixabay per Olobuild.
 *
 * Fornisce 4 endpoint REST:
 *   GET  olo/v1/pixabay/search         — Cerca foto su Pixabay
 *   POST olo/v1/pixabay/download       — Scarica foto nel WP Media Library
 *   GET  olo/v1/pixabay/videos         — Cerca video su Pixabay
 *   POST olo/v1/pixabay/video-download — Scarica video nel WP Media Library
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Pixabay {

    /**
     * Restituisce la API key Pixabay.
     *
     * Ordine di risoluzione: costante OLO_PIXABAY_API_KEY (definibile in wp-config.php)
     * → opzione olo_pixabay_api_key. Nessuna chiave hardcoded: l'utente deve
     * configurare la propria chiave (requisito wordpress.org).
     */
    public static function get_api_key() {
        if ( defined( 'OLO_PIXABAY_API_KEY' ) && OLO_PIXABAY_API_KEY ) {
            return OLO_PIXABAY_API_KEY;
        }
        return (string) get_option( 'olo_pixabay_api_key', '' );
    }

    public function init() {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    public function register_routes() {
        register_rest_route( 'olo/v1', '/pixabay/search', [
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
                'min_width'   => [ 'default' => 0, 'type' => 'integer', 'sanitize_callback' => 'absint' ],
                'min_height'  => [ 'default' => 0, 'type' => 'integer', 'sanitize_callback' => 'absint' ],
            ],
        ] );

        register_rest_route( 'olo/v1', '/pixabay/download', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'download' ],
            'permission_callback' => function () {
                return current_user_can( 'upload_files' );
            },
        ] );

        register_rest_route( 'olo/v1', '/pixabay/videos', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'video_search' ],
            'permission_callback' => function () {
                return current_user_can( 'upload_files' );
            },
            'args' => [
                'query'        => [ 'required' => true, 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ],
                'page'         => [ 'default' => 1, 'type' => 'integer', 'sanitize_callback' => 'absint' ],
                'per_page'     => [ 'default' => 15, 'type' => 'integer', 'sanitize_callback' => 'absint' ],
                'min_duration' => [ 'default' => 0, 'type' => 'integer', 'sanitize_callback' => 'absint' ],
                'max_duration' => [ 'default' => 0, 'type' => 'integer', 'sanitize_callback' => 'absint' ],
            ],
        ] );

        register_rest_route( 'olo/v1', '/pixabay/video-download', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'video_download' ],
            'permission_callback' => function () {
                return current_user_can( 'upload_files' );
            },
        ] );
    }

    public function search( $request ) {
        $query       = $request->get_param( 'query' );
        $page        = $request->get_param( 'page' ) ?: 1;
        $per_page    = min( $request->get_param( 'per_page' ) ?: 30, 200 );
        $orientation = sanitize_text_field( $request->get_param( 'orientation' ) );
        $min_width   = intval( $request->get_param( 'min_width' ) );
        $min_height  = intval( $request->get_param( 'min_height' ) );

        $params = [
            'key'        => self::get_api_key(),
            'q'          => $query,
            'page'       => $page,
            'per_page'   => $per_page,
            'image_type' => 'photo',
        ];
        // Pixabay: horizontal, vertical (no square)
        $ori_map = [ 'landscape' => 'horizontal', 'portrait' => 'vertical' ];
        if ( ! empty( $orientation ) && isset( $ori_map[ $orientation ] ) ) {
            $params['orientation'] = $ori_map[ $orientation ];
        }
        if ( $min_width > 0 ) {
            $params['min_width'] = $min_width;
        }
        if ( $min_height > 0 ) {
            $params['min_height'] = $min_height;
        }

        $response = wp_remote_get( 'https://pixabay.com/api/?' . http_build_query( $params ), [
            'timeout' => 15,
        ] );

        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'pixabay_error', $response->get_error_message(), [ 'status' => 502 ] );
        }

        $code = wp_remote_retrieve_response_code( $response );
        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( $code !== 200 ) {
            $msg = $body['message'] ?? 'Pixabay API error';
            return new WP_Error( 'pixabay_api', $msg, [ 'status' => $code ] );
        }

        $results = [];
        foreach ( $body['hits'] ?? [] as $photo ) {
            $results[] = [
                'id'               => $photo['id'],
                'thumb'            => $photo['webformatURL'] ?? '',
                'regular'          => $photo['largeImageURL'] ?? '',
                'alt'              => $photo['tags'] ?? '',
                'photographer'     => $photo['user'] ?? '',
                'photographer_url' => 'https://pixabay.com/users/' . ( $photo['user'] ?? '' ) . '-' . ( $photo['user_id'] ?? '' ) . '/',
                'width'            => $photo['imageWidth'] ?? 0,
                'height'           => $photo['imageHeight'] ?? 0,
            ];
        }

        $total       = $body['totalHits'] ?? 0;
        $total_pages = $per_page > 0 ? (int) ceil( $total / $per_page ) : 0;

        return rest_ensure_response( [
            'results'     => $results,
            'total'       => $total,
            'total_pages' => $total_pages,
        ] );
    }

    public function download( $request ) {
        $params = $request->get_json_params();

        $regular_url  = $params['regular_url'] ?? '';
        $alt          = sanitize_text_field( $params['alt'] ?? '' );
        $photographer = sanitize_text_field( $params['photographer'] ?? '' );
        $photo_id     = sanitize_text_field( $params['photo_id'] ?? '' );

        if ( empty( $regular_url ) ) {
            return new WP_Error( 'missing_url', 'URL immagine mancante', [ 'status' => 400 ] );
        }

        // Hotlink mode: skip sideload
        $behavior = olo_stockmedia_behavior();
        if ( empty( $behavior['download_local'] ) ) {
            return rest_ensure_response( [
                'id'      => 0,
                'url'     => $regular_url,
                'alt'     => $alt,
                'caption' => $photographer ? 'Photo by ' . $photographer . ' on Pixabay' : '',
                'hotlink' => true,
            ] );
        }

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
            $finfo_mime = function_exists( 'finfo_open' ) ? finfo_file( finfo_open( FILEINFO_MIME_TYPE ), $tmp_file ) : ( function_exists( 'mime_content_type' ) ? mime_content_type( $tmp_file ) : '' );
            $allowed = [ 'image/jpeg', 'image/png', 'image/webp', 'image/gif', 'video/mp4', 'video/webm' ];
            if ( $finfo_mime && ! in_array( $finfo_mime, $allowed, true ) ) {
                @unlink( $tmp_file );
                return new WP_Error( 'invalid_mime', 'Il file scaricato non è un tipo media valido.' );
            }
        }

        // Optimize: WebP conversion se richiesto
        if ( ! empty( $behavior['optimize_on_download'] ) ) {
            $webp = olo_convert_to_webp( $tmp_file, 82 );
            if ( $webp && $webp !== $tmp_file ) {
                @unlink( $tmp_file );
                $tmp_file = $webp;
            }
        }

        $is_webp   = str_ends_with( $tmp_file, '.webp' );
        $filename  = 'pixabay-' . $photo_id . ( $is_webp ? '.webp' : '.jpg' );
        $file_data = [
            'name'     => $filename,
            'type'     => $is_webp ? 'image/webp' : 'image/jpeg',
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

        $caption = $photographer ? 'Photo by ' . $photographer . ' on Pixabay' : '';

        $attachment_data = [
            'post_title'     => $alt ?: ( 'Pixabay ' . $photo_id ),
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

    /**
     * Proxy ricerca video Pixabay.
     */
    public function video_search( $request ) {
        $query        = $request->get_param( 'query' );
        $page         = $request->get_param( 'page' ) ?: 1;
        $per_page     = min( $request->get_param( 'per_page' ) ?: 15, 200 );
        $min_duration = intval( $request->get_param( 'min_duration' ) );
        $max_duration = intval( $request->get_param( 'max_duration' ) );

        $response = wp_remote_get( 'https://pixabay.com/api/videos/?' . http_build_query( [
            'key'      => self::get_api_key(),
            'q'        => $query,
            'page'     => $page,
            'per_page' => $per_page,
        ] ), [
            'timeout' => 15,
        ] );

        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'pixabay_error', $response->get_error_message(), [ 'status' => 502 ] );
        }

        $code = wp_remote_retrieve_response_code( $response );
        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( $code !== 200 ) {
            $msg = $body['message'] ?? 'Pixabay API error';
            return new WP_Error( 'pixabay_api', $msg, [ 'status' => $code ] );
        }

        $results = [];
        foreach ( $body['hits'] ?? [] as $video ) {
            $dur = $video['duration'] ?? 0;
            if ( $min_duration > 0 && $dur < $min_duration ) continue;
            if ( $max_duration > 0 && $dur > $max_duration ) continue;
            $medium = $video['videos']['medium'] ?? [];
            $small  = $video['videos']['small'] ?? [];
            $large  = $video['videos']['large'] ?? [];
            $best   = $medium ?: $small ?: $large;
            if ( empty( $best['url'] ) ) continue;

            // Thumbnail: Pixabay videos hosted on their CDN, no direct thumb.
            // Use tiny video URL for poster via <video preload="metadata">.
            $tiny_url = $video['videos']['tiny']['url'] ?? '';

            $results[] = [
                'id'           => $video['id'],
                'thumb'        => $tiny_url,
                'regular'      => $best['url'] ?? '',
                'alt'          => $video['tags'] ?? '',
                'photographer' => $video['user'] ?? '',
                'duration'     => $video['duration'] ?? 0,
                'width'        => $best['width'] ?? 0,
                'height'       => $best['height'] ?? 0,
                'is_video_thumb' => true,
            ];
        }

        $total       = $body['totalHits'] ?? 0;
        $total_pages = $per_page > 0 ? (int) ceil( $total / $per_page ) : 0;

        return rest_ensure_response( [
            'results'     => $results,
            'total'       => $total,
            'total_pages' => $total_pages,
        ] );
    }

    /**
     * Scarica un video Pixabay nel WP Media Library.
     */
    public function video_download( $request ) {
        $params = $request->get_json_params();

        $video_url    = $params['regular_url'] ?? $params['video_url'] ?? '';
        $alt          = sanitize_text_field( $params['alt'] ?? '' );
        $photographer = sanitize_text_field( $params['photographer'] ?? '' );
        $video_id     = sanitize_text_field( $params['photo_id'] ?? $params['video_id'] ?? '' );
        $thumb_url    = $params['thumb_url'] ?? '';

        if ( empty( $video_url ) ) {
            return new WP_Error( 'missing_url', 'URL video mancante', [ 'status' => 400 ] );
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $tmp_file = download_url( $video_url, 120 );
        if ( is_wp_error( $tmp_file ) ) {
            return new WP_Error( 'download_failed', $tmp_file->get_error_message(), [ 'status' => 502 ] );
        }

        // Validate MIME type
        $mime = wp_check_filetype( $tmp_file );
        if ( empty( $mime['type'] ) ) {
            $finfo_mime = function_exists( 'finfo_open' ) ? finfo_file( finfo_open( FILEINFO_MIME_TYPE ), $tmp_file ) : ( function_exists( 'mime_content_type' ) ? mime_content_type( $tmp_file ) : '' );
            $allowed = [ 'image/jpeg', 'image/png', 'image/webp', 'image/gif', 'video/mp4', 'video/webm' ];
            if ( $finfo_mime && ! in_array( $finfo_mime, $allowed, true ) ) {
                @unlink( $tmp_file );
                return new WP_Error( 'invalid_mime', 'Il file scaricato non è un tipo media valido.' );
            }
        }

        $filename  = 'pixabay-video-' . $video_id . '.mp4';
        $file_data = [
            'name'     => $filename,
            'type'     => 'video/mp4',
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

        $caption = $photographer ? 'Video by ' . $photographer . ' on Pixabay' : '';

        $attachment_data = [
            'post_title'     => $alt ?: ( 'Pixabay Video ' . $video_id ),
            'post_content'   => '',
            'post_excerpt'   => $caption,
            'post_status'    => 'inherit',
            'post_mime_type' => 'video/mp4',
        ];

        $attach_id = wp_insert_attachment( $attachment_data, $upload['file'] );
        if ( is_wp_error( $attach_id ) ) {
            return new WP_Error( 'attach_failed', $attach_id->get_error_message(), [ 'status' => 500 ] );
        }

        wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $upload['file'] ) );

        return rest_ensure_response( [
            'id'      => $attach_id,
            'url'     => $upload['url'],
            'alt'     => $alt,
            'caption' => $caption,
            'type'    => 'video',
            'poster'  => $thumb_url,
        ] );
    }
}
