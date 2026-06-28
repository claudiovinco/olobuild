<?php
/**
 * Olo_Pexels — Integrazione Pexels per Olobuild.
 *
 * Fornisce 4 endpoint REST:
 *   GET  olo/v1/pexels/search         — Cerca foto su Pexels
 *   POST olo/v1/pexels/download       — Scarica foto nel WP Media Library
 *   GET  olo/v1/pexels/videos         — Cerca video su Pexels
 *   POST olo/v1/pexels/video-download — Scarica video nel WP Media Library
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Pexels {

    /**
     * Restituisce la API key Pexels.
     *
     * Ordine di risoluzione: costante OLO_PEXELS_API_KEY (definibile in wp-config.php)
     * → opzione olo_pexels_api_key. Nessuna chiave hardcoded: l'utente deve
     * configurare la propria chiave (requisito wordpress.org).
     */
    public static function get_api_key() {
        if ( defined( 'OLO_PEXELS_API_KEY' ) && OLO_PEXELS_API_KEY ) {
            return OLO_PEXELS_API_KEY;
        }
        return (string) get_option( 'olo_pexels_api_key', '' );
    }

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
                'query'       => [ 'required' => true, 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ],
                'page'        => [ 'default' => 1, 'type' => 'integer', 'sanitize_callback' => 'absint' ],
                'per_page'    => [ 'default' => 30, 'type' => 'integer', 'sanitize_callback' => 'absint' ],
                'orientation' => [ 'default' => '', 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ],
                'size'        => [ 'default' => '', 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ],
            ],
        ] );

        register_rest_route( 'olo/v1', '/pexels/download', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'download' ],
            'permission_callback' => function () {
                return current_user_can( 'upload_files' );
            },
        ] );

        register_rest_route( 'olo/v1', '/pexels/videos', [
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

        register_rest_route( 'olo/v1', '/pexels/video-download', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'video_download' ],
            'permission_callback' => function () {
                return current_user_can( 'upload_files' );
            },
        ] );
    }

    /**
     * Proxy ricerca Pexels.
     */
    public function search( $request ) {
        $query       = $request->get_param( 'query' );
        $page        = $request->get_param( 'page' ) ?: 1;
        $per_page    = min( $request->get_param( 'per_page' ) ?: 30, 30 );
        $orientation = sanitize_text_field( $request->get_param( 'orientation' ) );
        $size        = sanitize_text_field( $request->get_param( 'size' ) );

        $params = [
            'query'    => $query,
            'page'     => $page,
            'per_page' => $per_page,
        ];
        // Pexels: landscape, portrait, square
        $ori_map = [ 'landscape' => 'landscape', 'portrait' => 'portrait', 'square' => 'square' ];
        if ( ! empty( $orientation ) && isset( $ori_map[ $orientation ] ) ) {
            $params['orientation'] = $ori_map[ $orientation ];
        }
        // Pexels: small, medium, large
        if ( in_array( $size, [ 'small', 'medium', 'large' ], true ) ) {
            $params['size'] = $size;
        }

        $response = wp_remote_get( 'https://api.pexels.com/v1/search?' . http_build_query( $params ), [
            'headers' => [ 'Authorization' => self::get_api_key() ],
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

        // Scegli quale URL servire come "regular" in base al filtro size.
        // Pexels restituisce sempre tutte le varianti; qui scegliamo quella
        // corrispondente alla dimensione richiesta dall'utente.
        // Pexels src variants:
        //   small  ~130px h  |  medium ~350px h  |  large ~940px w
        //   large2x ~1880px w  |  original = full resolution
        $size_map = [
            'small'  => 'medium',   // ~350px — ragionevole come "piccola"
            'medium' => 'large',    // ~940px — standard web
            'large'  => 'original', // full resolution
        ];
        $src_key = isset( $size_map[ $size ] ) ? $size_map[ $size ] : 'large';

        $results = [];
        foreach ( $body['photos'] ?? [] as $photo ) {
            $src = $photo['src'] ?? [];
            $regular_url = $src[ $src_key ] ?? $src['large'] ?? '';
            $results[] = [
                'id'               => $photo['id'],
                'thumb'            => $src['medium'] ?? '',
                'regular'          => $regular_url,
                'small'            => $src['small'] ?? '',
                'medium'           => $src['medium'] ?? '',
                'large'            => $src['large'] ?? '',
                'large2x'          => $src['large2x'] ?? '',
                'original'         => $src['original'] ?? '',
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
        // Anti-SSRF: l'URL arriva dal client — accetta solo host Pexels.
        if ( ! olo_validate_remote_media_url( $regular_url, [ 'pexels.com' ] ) ) {
            return new WP_Error( 'invalid_url', 'URL non consentito.', [ 'status' => 400 ] );
        }

        // Hotlink mode: skip sideload
        $behavior = olo_stockmedia_behavior();
        if ( empty( $behavior['download_local'] ) ) {
            return rest_ensure_response( [
                'id'      => 0,
                'url'     => $regular_url,
                'alt'     => $alt,
                'caption' => $photographer ? 'Photo by ' . $photographer . ' on Pexels' : '',
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
                wp_delete_file( $tmp_file );
                return new WP_Error( 'invalid_mime', 'Il file scaricato non è un tipo media valido.' );
            }
        }

        // Optimize: WebP conversion se richiesto
        if ( ! empty( $behavior['optimize_on_download'] ) ) {
            $webp = olo_convert_to_webp( $tmp_file, 82 );
            if ( $webp && $webp !== $tmp_file ) {
                wp_delete_file( $tmp_file );
                $tmp_file = $webp;
            }
        }

        $is_webp   = str_ends_with( $tmp_file, '.webp' );
        $filename  = 'pexels-' . $photo_id . ( $is_webp ? '.webp' : '.jpg' );
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
            wp_delete_file( $tmp_file );
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

    /**
     * Proxy ricerca video Pexels.
     */
    public function video_search( $request ) {
        $query        = $request->get_param( 'query' );
        $page         = $request->get_param( 'page' ) ?: 1;
        $per_page     = min( $request->get_param( 'per_page' ) ?: 15, 30 );
        $min_duration = intval( $request->get_param( 'min_duration' ) );
        $max_duration = intval( $request->get_param( 'max_duration' ) );

        $params = [
            'query'    => $query,
            'page'     => $page,
            'per_page' => $per_page,
        ];
        if ( $min_duration > 0 ) {
            $params['min_duration'] = $min_duration;
        }
        if ( $max_duration > 0 ) {
            $params['max_duration'] = $max_duration;
        }

        $response = wp_remote_get( 'https://api.pexels.com/videos/search?' . http_build_query( $params ), [
            'headers' => [ 'Authorization' => self::get_api_key() ],
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
        foreach ( $body['videos'] ?? [] as $video ) {
            // Find best MP4 file around HD (720-1080p)
            $hd_file  = null;
            $any_file = null;
            foreach ( $video['video_files'] ?? [] as $vf ) {
                if ( ( $vf['file_type'] ?? '' ) !== 'video/mp4' ) continue;
                $w = $vf['width'] ?? 0;
                if ( ! $any_file || $w > ( $any_file['width'] ?? 0 ) ) {
                    $any_file = $vf;
                }
                if ( $w >= 720 && $w <= 1920 ) {
                    if ( ! $hd_file || $w < ( $hd_file['width'] ?? 9999 ) ) {
                        $hd_file = $vf;
                    }
                }
            }
            $best = $hd_file ?: $any_file;
            if ( ! $best ) continue;

            $results[] = [
                'id'           => $video['id'],
                'thumb'        => $video['image'] ?? '',
                'regular'      => $best['link'] ?? '',
                'alt'          => '',
                'photographer' => $video['user']['name'] ?? '',
                'duration'     => $video['duration'] ?? 0,
                'width'        => $best['width'] ?? 0,
                'height'       => $best['height'] ?? 0,
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
     * Scarica un video Pexels nel WP Media Library.
     */
    public function video_download( $request ) {
        $params = $request->get_json_params();

        $video_url    = $params['regular_url'] ?? $params['video_url'] ?? '';
        $alt          = sanitize_text_field( $params['alt'] ?? '' );
        $photographer = sanitize_text_field( $params['photographer'] ?? '' );
        $video_id     = sanitize_text_field( $params['photo_id'] ?? $params['video_id'] ?? '' );
        $thumb_url    = esc_url_raw( $params['thumb_url'] ?? '' );

        if ( empty( $video_url ) ) {
            return new WP_Error( 'missing_url', 'URL video mancante', [ 'status' => 400 ] );
        }
        // Anti-SSRF: solo host Pexels (i file video legacy stanno su player.vimeo.com).
        if ( ! olo_validate_remote_media_url( $video_url, [ 'pexels.com', 'vimeo.com' ] ) ) {
            return new WP_Error( 'invalid_url', 'URL non consentito.', [ 'status' => 400 ] );
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
                wp_delete_file( $tmp_file );
                return new WP_Error( 'invalid_mime', 'Il file scaricato non è un tipo media valido.' );
            }
        }

        $filename  = 'pexels-video-' . $video_id . '.mp4';
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
            wp_delete_file( $tmp_file );
            return new WP_Error( 'upload_failed', $upload['error'], [ 'status' => 500 ] );
        }

        $caption = $photographer ? 'Video by ' . $photographer . ' on Pexels' : '';

        $attachment_data = [
            'post_title'     => $alt ?: ( 'Pexels Video ' . $video_id ),
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
