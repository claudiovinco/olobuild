<?php
/**
 * Olo_Site_Import_Export — Export/import templates with embedded media.
 *
 * Exports: template JSON + referenced images as base64 or URL list.
 * Imports: uploads media to new site, remaps URLs in template data.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Site_Import_Export {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public function init() {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
        add_action( 'admin_menu', [ $this, 'add_admin_page' ] );
    }

    /* ─────────────────────────────────────────────
     * REST API
     * ───────────────────────────────────────────── */

    public function register_routes() {
        // Export template as JSON (with optional media)
        register_rest_route( 'olo/v1', '/export-template/(?P<id>\d+)', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'export_template' ],
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            },
            'args' => [
                'include_media' => [
                    'default'           => false,
                    'sanitize_callback' => 'rest_sanitize_boolean',
                ],
            ],
        ] );

        // Export full site (all templates + header + footer + global styles)
        register_rest_route( 'olo/v1', '/export-site', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'export_site' ],
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            },
            'args' => [
                'include_media' => [
                    'default'           => false,
                    'sanitize_callback' => 'rest_sanitize_boolean',
                ],
            ],
        ] );

        // Import template from JSON
        register_rest_route( 'olo/v1', '/import-template', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'import_template' ],
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            },
        ] );

        // Import full site
        register_rest_route( 'olo/v1', '/import-site', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'import_site' ],
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            },
        ] );
    }

    /* ─────────────────────────────────────────────
     * Export Template
     * ───────────────────────────────────────────── */

    public function export_template( $request ) {
        $id            = intval( $request['id'] );
        $include_media = ! empty( $request['include_media'] );

        $db       = new Olo_Database();
        $template = $db->get_template( $id );

        if ( ! $template ) {
            return new WP_Error( 'not_found', 'Template non trovato', [ 'status' => 404 ] );
        }

        // get_template() returns assoc array with 'content' already decoded
        $data = $template['content'] ?? null;
        if ( ! is_array( $data ) ) {
            return new WP_Error( 'invalid', 'Dati template non validi', [ 'status' => 400 ] );
        }

        $export = [
            'format'     => 'olobuild-template',
            'version'    => OLO_VERSION,
            'name'       => $template['title'] ?? ( 'Template #' . $id ),
            'type'       => $template['type'] ?? 'page',
            'data'       => $data,
            'styles'     => $template['settings'] ?? [],
            'exported_at' => gmdate( 'Y-m-d\TH:i:s\Z' ),
        ];

        if ( $include_media ) {
            $media_urls = $this->collect_media_urls( $data );
            $export['media'] = $this->package_media( $media_urls );
        }

        return rest_ensure_response( $export );
    }

    /* ─────────────────────────────────────────────
     * Export Site
     * ───────────────────────────────────────────── */

    public function export_site( $request ) {
        $include_media = ! empty( $request['include_media'] );

        $db        = new Olo_Database();
        $result    = $db->list_templates( [ 'per_page' => 999 ] );
        $templates = $result['items'] ?? $result;
        if ( ! is_array( $templates ) ) {
            $templates = [];
        }
        $exported  = [];

        foreach ( $templates as $tpl ) {
            $tpl_data = $tpl['content'] ?? null;
            if ( ! is_array( $tpl_data ) ) {
                continue;
            }
            $exported[] = [
                'id'     => intval( $tpl['id'] ?? 0 ),
                'name'   => $tpl['title'] ?? '',
                'type'   => $tpl['type'] ?? 'page',
                'data'   => $tpl_data,
                'styles' => $tpl['settings'] ?? [],
            ];
        }

        $site_export = [
            'format'        => 'olobuild-site',
            'version'       => OLO_VERSION,
            'site_url'      => home_url(),
            'exported_at'   => gmdate( 'Y-m-d\TH:i:s\Z' ),
            'templates'     => $exported,
            'global_styles' => get_option( 'olo_styles', [] ),
            'active_header' => get_option( 'olo_active_header', '' ),
            'active_footer' => get_option( 'olo_active_footer', '' ),
        ];

        // Collect media from all templates
        if ( $include_media ) {
            $all_urls = [];
            foreach ( $exported as $tpl ) {
                $all_urls = array_merge( $all_urls, $this->collect_media_urls( $tpl['data'] ) );
            }
            $all_urls = array_unique( $all_urls );
            $site_export['media'] = $this->package_media( $all_urls );
        }

        return rest_ensure_response( $site_export );
    }

    /* ─────────────────────────────────────────────
     * Import Template
     * ───────────────────────────────────────────── */

    public function import_template( $request ) {
        $json = $request->get_json_params();
        if ( empty( $json['format'] ) || $json['format'] !== 'olobuild-template' ) {
            return new WP_Error( 'invalid', 'Formato non valido. Richiesto olobuild-template.', [ 'status' => 400 ] );
        }

        $data = $json['data'] ?? [];
        if ( empty( $data ) ) {
            return new WP_Error( 'empty', 'Template vuoto', [ 'status' => 400 ] );
        }

        // Import media and remap URLs
        $url_map = [];
        if ( ! empty( $json['media'] ) ) {
            $url_map = $this->import_media( $json['media'] );
        }

        // Remap URLs in template data
        if ( ! empty( $url_map ) ) {
            $data = $this->remap_urls( $data, $url_map );
        }

        // Save template
        $db = new Olo_Database();
        $name = sanitize_text_field( $json['name'] ?? 'Template importato' );
        $type = sanitize_text_field( $json['type'] ?? 'page' );
        $styles = $json['styles'] ?? [];

        $new_id = $db->create_template( [
            'title'    => $name,
            'type'     => $type,
            'content'  => $data,
            'settings' => $styles,
            'status'   => 'published',
        ] );

        if ( ! $new_id ) {
            return new WP_Error( 'save_failed', 'Errore salvataggio template', [ 'status' => 500 ] );
        }

        return rest_ensure_response( [
            'success'     => true,
            'template_id' => $new_id,
            'name'        => $name,
            'media_imported' => count( $url_map ),
        ] );
    }

    /* ─────────────────────────────────────────────
     * Import Site
     * ───────────────────────────────────────────── */

    public function import_site( $request ) {
        $json = $request->get_json_params();
        if ( empty( $json['format'] ) || $json['format'] !== 'olobuild-site' ) {
            return new WP_Error( 'invalid', 'Formato non valido. Richiesto olobuild-site.', [ 'status' => 400 ] );
        }

        // Import media
        $url_map = [];
        if ( ! empty( $json['media'] ) ) {
            $url_map = $this->import_media( $json['media'] );
        }

        $db = new Olo_Database();
        $imported = [];
        $id_map = []; // old_id => new_id

        foreach ( $json['templates'] ?? [] as $tpl ) {
            $tpl_data = $tpl['data'] ?? [];
            if ( empty( $tpl_data ) ) {
                continue;
            }

            // Remap media URLs
            if ( ! empty( $url_map ) ) {
                $tpl_data = $this->remap_urls( $tpl_data, $url_map );
            }

            // Also remap source site URL to current site
            if ( ! empty( $json['site_url'] ) ) {
                $tpl_data = $this->remap_urls( $tpl_data, [ $json['site_url'] => home_url() ] );
            }

            $name    = sanitize_text_field( $tpl['name'] ?? 'Template importato' );
            $type    = sanitize_text_field( $tpl['type'] ?? 'page' );
            $styles  = $tpl['styles'] ?? [];

            $new_id = $db->create_template( [
                'title'    => $name,
                'type'     => $type,
                'content'  => $tpl_data,
                'settings' => $styles,
                'status'   => 'published',
            ] );

            if ( $new_id ) {
                $old_id = intval( $tpl['id'] ?? 0 );
                $id_map[ $old_id ] = $new_id;
                $imported[] = [
                    'old_id' => $old_id,
                    'new_id' => $new_id,
                    'name'   => $name,
                ];
            }
        }

        // Import global styles — SEMPRE attraverso sanitize_styles per evitare
        // stored XSS via bundle malevolo. `olo_styles` viene poi interpolato in
        // <style> da Olo_Style_System::generate_css() (es. var(--olo-color-X)),
        // quindi un valore tipo "</style><script>..." persisterebbe su ogni pagina.
        if ( ! empty( $json['global_styles'] ) && is_array( $json['global_styles'] ) ) {
            $existing = get_option( 'olo_styles', [] );
            if ( empty( $existing ) ) {
                $sanitized_styles = class_exists( 'Olo_Style_System' )
                    ? ( new Olo_Style_System() )->sanitize_styles( $json['global_styles'] )
                    : [];
                if ( ! empty( $sanitized_styles ) ) {
                    update_option( 'olo_styles', $sanitized_styles );
                }
            }
        }

        // Remap header/footer IDs
        if ( ! empty( $json['active_header'] ) ) {
            $old_h = intval( $json['active_header'] );
            if ( isset( $id_map[ $old_h ] ) ) {
                update_option( 'olo_active_header', $id_map[ $old_h ] );
            }
        }
        if ( ! empty( $json['active_footer'] ) ) {
            $old_f = intval( $json['active_footer'] );
            if ( isset( $id_map[ $old_f ] ) ) {
                update_option( 'olo_active_footer', $id_map[ $old_f ] );
            }
        }

        return rest_ensure_response( [
            'success'         => true,
            'templates'       => $imported,
            'media_imported'  => count( $url_map ),
        ] );
    }

    /* ─────────────────────────────────────────────
     * Media Collection
     * ───────────────────────────────────────────── */

    /**
     * Recursively find all media URLs in template data.
     */
    private function collect_media_urls( $data ) {
        $urls = [];
        $upload_dir = wp_get_upload_dir();
        $base_url = $upload_dir['baseurl'];

        // Use unescaped JSON (JSON_UNESCAPED_SLASHES) so URLs don't have \/
        $json_str = json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

        // Match URLs pointing to wp-content/uploads
        if ( preg_match_all( '#https?://[^"\'\\s]+/wp-content/uploads/[^"\'\\s]+\.(?:jpg|jpeg|png|gif|webp|svg|mp4|webm|pdf|mp3|ogg)#i', $json_str, $matches ) ) {
            $urls = array_unique( $matches[0] );
        }

        return $urls;
    }

    /**
     * Package media as URL list with metadata.
     * We store URLs + filenames (not base64) to keep export small.
     * For cross-site import, the import process downloads them.
     */
    private function package_media( $urls ) {
        $media = [];

        foreach ( $urls as $url ) {
            $attachment_id = attachment_url_to_postid( $url );
            $filename      = basename( $url );
            $item          = [
                'url'      => $url,
                'filename' => $filename,
            ];

            if ( $attachment_id ) {
                $meta = wp_get_attachment_metadata( $attachment_id );
                $item['alt']     = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
                $item['title']   = get_the_title( $attachment_id );
                $item['width']   = $meta['width'] ?? 0;
                $item['height']  = $meta['height'] ?? 0;
                $item['mime']    = get_post_mime_type( $attachment_id );
            }

            $media[] = $item;
        }

        return $media;
    }

    /* ─────────────────────────────────────────────
     * Media Import
     * ───────────────────────────────────────────── */

    /**
     * Download and import media files into current site.
     *
     * @param array $media_items Array of media items with url, filename, alt, title.
     * @return array Map of old_url => new_url.
     */
    private function import_media( $media_items ) {
        if ( ! function_exists( 'media_handle_sideload' ) ) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
        }

        $url_map = [];

        foreach ( $media_items as $item ) {
            $old_url  = $item['url'] ?? '';
            $filename = $item['filename'] ?? basename( $old_url );

            if ( empty( $old_url ) ) {
                continue;
            }

            // Validate URL: only allow http/https and block internal/private IPs (SSRF prevention)
            if ( ! wp_http_validate_url( $old_url ) ) {
                continue;
            }
            $scheme = wp_parse_url( $old_url, PHP_URL_SCHEME );
            if ( ! in_array( $scheme, [ 'http', 'https' ], true ) ) {
                continue;
            }

            // Check if this URL already exists on this site
            $existing = attachment_url_to_postid( $old_url );
            if ( $existing ) {
                // Already on this site, no remap needed
                continue;
            }

            // Download the file (30s timeout)
            $tmp_file = download_url( $old_url, 30 );
            if ( is_wp_error( $tmp_file ) ) {
                continue;
            }

            $file_array = [
                'name'     => sanitize_file_name( $filename ),
                'tmp_name' => $tmp_file,
            ];

            $attachment_id = media_handle_sideload( $file_array, 0 );

            if ( is_wp_error( $attachment_id ) ) {
                @unlink( $tmp_file );
                continue;
            }

            // Set alt text if available
            if ( ! empty( $item['alt'] ) ) {
                update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $item['alt'] ) );
            }

            // Set title
            if ( ! empty( $item['title'] ) ) {
                wp_update_post( [
                    'ID'         => $attachment_id,
                    'post_title' => sanitize_text_field( $item['title'] ),
                ] );
            }

            $new_url = wp_get_attachment_url( $attachment_id );
            if ( $new_url ) {
                $url_map[ $old_url ] = $new_url;
            }
        }

        return $url_map;
    }

    /* ─────────────────────────────────────────────
     * URL Remapping
     * ───────────────────────────────────────────── */

    /**
     * Recursively replace URLs in template data.
     * Uses targeted replacement only on valid URL strings to prevent data corruption.
     */
    private function remap_urls( $data, $url_map ) {
        if ( is_string( $data ) ) {
            // Only replace full URL values (not partial matches inside other strings)
            foreach ( $url_map as $old => $new ) {
                if ( $data === $old ) {
                    return $new;
                }
                // Replace URL occurrences in HTML content strings
                if ( str_contains( $data, $old ) ) {
                    $data = str_replace(
                        esc_url( $old ),
                        esc_url( $new ),
                        str_replace( $old, $new, $data )
                    );
                }
            }
            return $data;
        }

        if ( is_array( $data ) ) {
            foreach ( $data as $key => $value ) {
                $data[ $key ] = $this->remap_urls( $value, $url_map );
            }
        }

        return $data;
    }

    /* ─────────────────────────────────────────────
     * Admin Page
     * ───────────────────────────────────────────── */

    public function add_admin_page() {
        add_submenu_page(
            'olobuild',
            'Import/Export',
            'Import/Export',
            'manage_options',
            'olo-import-export',
            [ $this, 'render_admin_page' ]
        );
    }

    public function render_admin_page() {
        $db        = new Olo_Database();
        $templates = $db->list_templates();
        $tpl_count = isset( $templates['total'] ) ? (int) $templates['total'] : count( $templates['items'] ?? [] );
        ?>
        <?php Olo_Builder::cockpit_shell_open( '<b>' . esc_html__( 'Import / Export', 'olobuild' ) . '</b>' ); ?>
        <main class="olo-cockpit-main olo-cockpit-legacy">
            <?php
            echo Olo_Builder::cockpit_page_head( [
                'title' => __( 'Import / Export', 'olobuild' ),
                'sub'   => sprintf(
                    /* translators: %d: total templates */
                    __( 'Esporta singoli template, intero sito o backup completo. %d template disponibili.', 'olobuild' ),
                    (int) $tpl_count
                ),
            ] );
            ?>

            <div id="olo-ie-msg" style="margin-top:16px"></div>

            <div class="olo-grid-2">

                <!-- Export -->
                <div class="olo-card">
                    <div class="olo-card-head">
                        <div class="olo-card-icon black">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        </div>
                        <div>
                            <h3><?php esc_html_e( 'Esporta', 'olobuild' ); ?></h3>
                            <p><?php esc_html_e( 'Scarica template e media in formato JSON', 'olobuild' ); ?></p>
                        </div>
                    </div>
                    <div class="olo-card-body">
                        <div class="olo-field-row">
                            <div class="olo-field-info">
                                <label><?php esc_html_e( 'Singolo template', 'olobuild' ); ?></label>
                            </div>
                            <div class="olo-field-input-wrap">
                                <select id="olo-export-tpl" class="olo-field-input">
                                    <?php foreach ( $templates as $t ) : ?>
                                    <option value="<?php echo intval( $t->id ); ?>"><?php echo esc_html( $t->name ); ?> (<?php echo esc_html( $t->type ); ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="olo-check-row">
                            <input type="checkbox" id="olo-export-media" checked />
                            <label for="olo-export-media"><?php esc_html_e( 'Includi media (immagini, video, PDF)', 'olobuild' ); ?></label>
                        </div>
                        <div class="olo-actions" style="margin-top:12px">
                            <button class="olo-btn-save" id="olo-export-btn">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                <?php esc_html_e( 'Esporta Template', 'olobuild' ); ?>
                            </button>
                        </div>

                        <div style="border-top:1px solid #f0f0f0;margin:20px 0"></div>

                        <div class="olo-field-info" style="margin-bottom:12px">
                            <label><?php esc_html_e( 'Esporta tutto il sito', 'olobuild' ); ?></label>
                            <div class="olo-field-hint"><?php esc_html_e( 'Include tutti i template, header, footer e stili globali', 'olobuild' ); ?></div>
                        </div>
                        <button class="olo-btn-reset" id="olo-export-site-btn">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                            <?php esc_html_e( 'Esporta Sito Completo', 'olobuild' ); ?>
                        </button>
                    </div>
                </div>

                <!-- Import -->
                <div class="olo-card">
                    <div class="olo-card-head">
                        <div class="olo-card-icon orange">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        </div>
                        <div>
                            <h3><?php esc_html_e( 'Importa', 'olobuild' ); ?></h3>
                            <p><?php esc_html_e( 'Carica un file .json esportato da Olobuild', 'olobuild' ); ?></p>
                        </div>
                    </div>
                    <div class="olo-card-body">
                        <div style="margin-bottom:16px">
                            <input type="file" id="olo-import-file" accept=".json" class="olo-field-input" style="width:100%;padding:8px 12px" />
                        </div>
                        <div class="olo-check-row">
                            <input type="checkbox" id="olo-import-media" checked />
                            <label for="olo-import-media"><?php esc_html_e( 'Importa media (scarica e ricarica immagini)', 'olobuild' ); ?></label>
                        </div>
                        <div class="olo-actions" style="margin-top:12px">
                            <button class="olo-btn-orange" id="olo-import-btn" disabled>
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                <?php esc_html_e( 'Importa', 'olobuild' ); ?>
                            </button>
                        </div>

                        <div id="olo-import-log" class="olo-log-box" style="display:none"></div>
                    </div>
                </div>
            </div>

            <script>
            (function() {
                var restUrl = '<?php echo esc_js( rest_url( 'olo/v1' ) ); ?>';
                var nonce = '<?php echo esc_js( wp_create_nonce( 'wp_rest' ) ); ?>';
                var msg = document.getElementById('olo-ie-msg');
                var log = document.getElementById('olo-import-log');

                function showMsg(text, ok) {
                    msg.className = 'olo-msg ' + (ok ? 'success' : 'error');
                    msg.innerHTML = (ok ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg> ' : '') + text;
                    setTimeout(function() { msg.className = ''; msg.innerHTML = ''; }, 5000);
                }

                function downloadJSON(data, filename) {
                    var blob = new Blob([JSON.stringify(data, null, 2)], {type: 'application/json'});
                    var a = document.createElement('a');
                    a.href = URL.createObjectURL(blob);
                    a.download = filename;
                    a.click();
                    URL.revokeObjectURL(a.href);
                }

                // Export template
                document.getElementById('olo-export-btn').addEventListener('click', function() {
                    var id = document.getElementById('olo-export-tpl').value;
                    var media = document.getElementById('olo-export-media').checked;
                    this.disabled = true;
                    this.textContent = 'Esportazione...';
                    var btn = this;

                    fetch(restUrl + '/export-template/' + id + '?include_media=' + (media ? '1' : '0'), {
                        headers: { 'X-WP-Nonce': nonce }
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(d) {
                        if (d.data) {
                            var name = (d.name || 'template').replace(/[^a-z0-9]/gi, '-').toLowerCase();
                            downloadJSON(d, 'olobuild-' + name + '.json');
                            showMsg('Template esportato!', true);
                        } else {
                            showMsg('Errore: ' + (d.message || 'sconosciuto'), false);
                        }
                        btn.disabled = false;
                        btn.textContent = 'Esporta Template';
                    })
                    .catch(function(e) {
                        showMsg('Errore: ' + e.message, false);
                        btn.disabled = false;
                        btn.textContent = 'Esporta Template';
                    });
                });

                // Export site
                document.getElementById('olo-export-site-btn').addEventListener('click', function() {
                    var media = document.getElementById('olo-export-media').checked;
                    this.disabled = true;
                    this.textContent = 'Esportazione...';
                    var btn = this;

                    fetch(restUrl + '/export-site?include_media=' + (media ? '1' : '0'), {
                        headers: { 'X-WP-Nonce': nonce }
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(d) {
                        if (d.templates) {
                            downloadJSON(d, 'olobuild-site-export.json');
                            showMsg('Sito esportato! (' + d.templates.length + ' template)', true);
                        } else {
                            showMsg('Errore: ' + (d.message || 'sconosciuto'), false);
                        }
                        btn.disabled = false;
                        btn.textContent = 'Esporta Sito Completo';
                    })
                    .catch(function(e) {
                        showMsg('Errore: ' + e.message, false);
                        btn.disabled = false;
                        btn.textContent = 'Esporta Sito Completo';
                    });
                });

                // File input
                document.getElementById('olo-import-file').addEventListener('change', function() {
                    document.getElementById('olo-import-btn').disabled = !this.files.length;
                });

                // Import
                document.getElementById('olo-import-btn').addEventListener('click', function() {
                    var file = document.getElementById('olo-import-file').files[0];
                    if (!file) return;

                    var btn = this;
                    btn.disabled = true;
                    btn.textContent = 'Importazione...';
                    log.style.display = 'block';
                    log.innerHTML = '<div>Lettura file...</div>';

                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var data;
                        try {
                            data = JSON.parse(e.target.result);
                        } catch (err) {
                            showMsg('File JSON non valido', false);
                            btn.disabled = false;
                            btn.textContent = 'Importa';
                            return;
                        }

                        if (!data.format) {
                            showMsg('Formato non riconosciuto', false);
                            btn.disabled = false;
                            btn.textContent = 'Importa';
                            return;
                        }

                        var includeMedia = document.getElementById('olo-import-media').checked;
                        if (!includeMedia) {
                            delete data.media;
                        }

                        var endpoint = (data.format === 'olobuild-site') ? '/import-site' : '/import-template';
                        log.innerHTML += '<div>Invio dati al server...</div>';

                        if (data.media) {
                            log.innerHTML += '<div>Scaricamento ' + data.media.length + ' file media...</div>';
                        }

                        fetch(restUrl + endpoint, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                            body: JSON.stringify(data)
                        })
                        .then(function(r) {
                            if (!r.ok) {
                                return r.text().then(function(txt) {
                                    var detail = '';
                                    try { var j = JSON.parse(txt); detail = j.message || j.data?.status || ''; } catch(e) { detail = txt.substring(0, 200); }
                                    throw new Error('HTTP ' + r.status + (detail ? ': ' + detail : ''));
                                });
                            }
                            return r.json();
                        })
                        .then(function(d) {
                            if (d.success) {
                                var info = '';
                                if (d.templates) {
                                    info = d.templates.length + ' template importati';
                                } else if (d.template_id) {
                                    info = 'Template "' + d.name + '" importato (ID: ' + d.template_id + ')';
                                }
                                if (d.media_imported > 0) {
                                    info += ', ' + d.media_imported + ' media importati';
                                }
                                log.innerHTML += '<div style="color:#059669;font-weight:600">Completato! ' + info + '</div>';
                                showMsg('Importazione completata!', true);
                            } else {
                                log.innerHTML += '<div style="color:#dc2626">Errore: ' + (d.message || d.code || 'sconosciuto') + '</div>';
                                showMsg('Errore importazione', false);
                            }
                            btn.disabled = false;
                            btn.textContent = 'Importa';
                        })
                        .catch(function(err) {
                            log.innerHTML += '<div style="color:#dc2626">Errore: ' + err.message + '</div>';
                            showMsg('Errore importazione', false);
                            btn.disabled = false;
                            btn.textContent = 'Importa';
                        });
                    };
                    reader.readAsText(file);
                });
            })();
            </script>
        </main>
        <?php Olo_Builder::cockpit_shell_close(); ?>
        <?php
    }
}
