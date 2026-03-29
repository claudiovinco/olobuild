<?php
/**
 * Admin page: Tools > OloBuild Converter
 * Handles menu registration, AJAX endpoints, and page rendering.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Admin_Page {

    /** @var Olo_Converter_Plugin */
    private $plugin;

    public function __construct( Olo_Converter_Plugin $plugin ) {
        $this->plugin = $plugin;

        add_action( 'admin_menu', [ $this, 'register_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );

        // AJAX handlers.
        add_action( 'wp_ajax_olo_converter_list_pages', [ $this, 'ajax_list_pages' ] );
        add_action( 'wp_ajax_olo_converter_convert', [ $this, 'ajax_convert' ] );
        add_action( 'wp_ajax_olo_converter_import', [ $this, 'ajax_import' ] );
    }

    public function register_menu() {
        add_management_page(
            'OloBuild Converter',
            'OloBuild Converter',
            'edit_posts',
            'olobuild-converter',
            [ $this, 'render_page' ]
        );
    }

    public function enqueue_assets( $hook ) {
        if ( $hook !== 'tools_page_olobuild-converter' ) {
            return;
        }

        wp_enqueue_style(
            'olo-converter-admin',
            OLO_CONVERTER_URL . 'assets/css/admin.css',
            [],
            OLO_CONVERTER_VERSION
        );

        wp_enqueue_script(
            'olo-converter-admin',
            OLO_CONVERTER_URL . 'assets/js/admin.js',
            [],
            OLO_CONVERTER_VERSION,
            true
        );

        wp_localize_script( 'olo-converter-admin', 'oloConverter', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'olo_converter' ),
            'restUrl' => rest_url( 'olo/v1/' ),
            'restNonce' => wp_create_nonce( 'wp_rest' ),
        ] );
    }

    public function render_page() {
        $converters = $this->plugin->get_converters();
        include OLO_CONVERTER_DIR . 'templates/admin-page.php';
    }

    // ─── AJAX: List available pages for a builder ───

    public function ajax_list_pages() {
        check_ajax_referer( 'olo_converter', 'nonce' );

        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( 'Permessi insufficienti.' );
        }

        $builder   = sanitize_text_field( $_POST['builder'] ?? '' );
        $converter = $this->plugin->get_converter( $builder );

        if ( ! $converter ) {
            wp_send_json_error( 'Builder non valido.' );
        }

        if ( ! $converter->is_source_installed() ) {
            wp_send_json_error( 'Builder non installato.' );
        }

        wp_send_json_success( $converter->get_available_pages() );
    }

    // ─── AJAX: Run conversion ───

    public function ajax_convert() {
        check_ajax_referer( 'olo_converter', 'nonce' );

        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( 'Permessi insufficienti.' );
        }

        $builder = sanitize_text_field( $_POST['builder'] ?? '' );
        $mode    = sanitize_text_field( $_POST['mode'] ?? '' );

        $converter = $this->plugin->get_converter( $builder );
        if ( ! $converter ) {
            wp_send_json_error( 'Builder non valido.' );
        }

        try {
            if ( $mode === 'db' ) {
                $post_id = (int) ( $_POST['post_id'] ?? 0 );
                if ( ! $post_id ) {
                    wp_send_json_error( 'Seleziona una pagina.' );
                }
                $result = $converter->convert_from_db( $post_id );
            } elseif ( $mode === 'file' ) {
                $file_content = $_POST['file_content'] ?? '';
                if ( empty( $file_content ) ) {
                    wp_send_json_error( 'Nessun file caricato.' );
                }
                $file_data = json_decode( wp_unslash( $file_content ), true );
                if ( json_last_error() !== JSON_ERROR_NONE ) {
                    wp_send_json_error( 'JSON non valido: ' . json_last_error_msg() );
                }
                $result = $converter->convert_from_file( $file_data );
            } else {
                wp_send_json_error( 'Modalità non valida.' );
                return;
            }

            wp_send_json_success( [
                'template' => $result['template'],
                'report'   => $result['report']->to_array(),
            ] );
        } catch ( \Exception $e ) {
            wp_send_json_error( 'Errore conversione: ' . $e->getMessage() );
        }
    }

    // ─── AJAX: Import to OloBuild ───

    public function ajax_import() {
        check_ajax_referer( 'olo_converter', 'nonce' );

        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( 'Permessi insufficienti.' );
        }

        $template_json = $_POST['template'] ?? '';
        if ( empty( $template_json ) ) {
            wp_send_json_error( 'Nessun template da importare.' );
        }

        $template = json_decode( wp_unslash( $template_json ), true );
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            wp_send_json_error( 'JSON template non valido.' );
        }

        // Use OloBuild's database class directly if available.
        if ( class_exists( 'Olo_Database' ) ) {
            $db = new Olo_Database();
            $id = $db->create_template( [
                'title'    => sanitize_text_field( $template['title'] ?? 'Importato' ),
                'type'     => sanitize_text_field( $template['type'] ?? 'page' ),
                'content'  => $template['content'] ?? [],
                'settings' => $template['settings'] ?? [],
                'status'   => 'draft',
            ] );

            if ( $id ) {
                wp_send_json_success( [
                    'id'       => $id,
                    'edit_url' => admin_url( "admin.php?page=olobuild&template_id={$id}" ),
                    'message'  => "Template importato con successo (ID: {$id})",
                ] );
            } else {
                wp_send_json_error( 'Errore durante il salvataggio in OloBuild.' );
            }
        } else {
            wp_send_json_error( 'OloBuild non disponibile. Scarica il JSON e importalo manualmente.' );
        }
    }
}
