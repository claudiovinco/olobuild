<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
require_once __DIR__ . '/traits/trait-olobuild-builder-cockpit.php';
require_once __DIR__ . '/traits/trait-olobuild-builder-settings.php';
require_once __DIR__ . '/traits/trait-olobuild-builder-tiles.php';

class Olobuild_Builder {
    use Olobuild_Builder_Cockpit_Trait;
    use Olobuild_Builder_Settings_Trait;
    use Olobuild_Builder_Tiles_Trait;

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init();
    }

    private function init() {
        // Admin menu
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );
        add_action( 'admin_menu', [ $this, 'admin_menu_trim' ], 999 );

        // Settings page (API keys)
        add_action( 'admin_init', [ $this, 'register_settings' ] );

        // REST endpoints for Configurazione page
        add_action( 'rest_api_init', [ $this, 'register_settings_rest_routes' ] );

        // Enqueue scripts only on builder page
        add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );

        // Invalida cache mappa meta_keys quando si salva o elimina un post
        add_action( 'save_post',    function () { delete_transient( 'olo_meta_keys_map_v1' ); } );
        add_action( 'deleted_post', function () { delete_transient( 'olo_meta_keys_map_v1' ); } );

        // Submenu icons via CSS
        add_action( 'admin_head', [ $this, 'admin_submenu_icons' ] );

        // Admin body class for Olobuild shell layout
        add_filter( 'admin_body_class', [ $this, 'admin_body_class' ] );

        // Initialize REST API
        $rest_api = new Olobuild_Rest_Api();
        $rest_api->init();

        // Initialize frontend renderer (shortcode)
        $frontend = new Olobuild_Frontend_Renderer();
        $frontend->init();

        // Initialize page integration (Edit with Olobuild buttons)
        $page_integration = new Olobuild_Page_Integration();
        $page_integration->init();

        // Initialize header integration
        $header = new Olobuild_Header_Integration();
        $header->init();

        // Initialize footer integration
        $footer = new Olobuild_Footer_Integration();
        $footer->init();

        // Initialize single template integration
        $single = new Olobuild_Single_Integration();
        $single->init();

        // Initialize archive template integration
        Olobuild_Archive_Integration::instance();

        // Initialize 404 template integration
        Olobuild_404_Integration::instance();

        // Initialize search results template integration
        Olobuild_Search_Results_Integration::instance();

        // Builder iframe page (live preview)
        add_action( 'template_redirect', [ $this, 'serve_builder_iframe' ] );

        // Register core tiles
        $this->register_core_tiles();

        // LiveSearch REST endpoint
        add_action( 'rest_api_init', [ 'Olobuild_LiveSearch_Tile', 'register_rest_routes' ] );

        // Unsplash integration
        $unsplash = new Olobuild_Unsplash();
        $unsplash->init();

        // Pexels integration
        $pexels = new Olobuild_Pexels();
        $pexels->init();

        // Pixabay integration
        $pixabay = new Olobuild_Pixabay();
        $pixabay->init();

        // Openverse integration
        $openverse = new Olobuild_Openverse();
        $openverse->init();

        // Freesound integration
        $freesound = new Olobuild_Freesound();
        $freesound->init();
    }

    /**
     * Serve the builder iframe page for live preview.
     *
     * Comportamento context-aware:
     *
     *   1. Se siamo su un permalink reale (`is_singular()`), NON serviamo il
     *      template standalone: lasciamo WP renderizzare la pagina con il suo
     *      tema (header/footer/regole template Olobuild) e sostituiamo SOLO
     *      il content del post con un placeholder che il bridge JS aggiorna
     *      via postMessage. Così il preview mostra header/footer reali esattamente
     *      come sono in prod (incluso `_olo_header_id` per-page o regole).
     *
     *   2. Se Vue ha passato `olo_tpl=<id>` e siamo sulla home (caso default
     *      quando il template editato è una "page"), facciamo lookup automatico
     *      di un post che usa quel template e ridirezioniamo all'iframe contestuale.
     *
     *   3. Altrimenti (home senza match, archive, ecc.) serviamo il template
     *      standalone come fallback (status quo).
     */
    public function serve_builder_iframe() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per routing dell'iframe builder; nessuna modifica di stato; sola verifica di presenza del flag.
        if ( empty( $_GET['olo_builder_iframe'] ) ) return;
        if ( ! current_user_can( 'edit_pages' ) ) {
            // Diagnostica: distingue tra "non loggato" e "loggato ma senza permessi"
            $uid = get_current_user_id();
            if ( ! $uid ) {
                $msg = '<h2>Sessione scaduta</h2><p>Il browser non ha inviato il cookie di autenticazione all\'iframe del builder.</p>'
                     . '<p><strong>Cosa fare:</strong></p><ul>'
                     . '<li>Ricarica la pagina del builder con <kbd>Ctrl+Shift+R</kbd></li>'
                     . '<li>Se persiste, esegui logout/login da WordPress</li>'
                     . '<li>Verifica che i cookie di terze parti non siano bloccati nel browser</li>'
                     . '</ul>';
            } else {
                $user = wp_get_current_user();
                $roles = implode( ', ', (array) $user->roles );
                $msg = '<h2>Permessi insufficienti</h2><p>L\'utente <strong>' . esc_html( $user->user_login ) . '</strong> (ruoli: ' . esc_html( $roles ) . ') non ha la capability <code>edit_pages</code>.</p>'
                     . '<p>Aggiungi il ruolo Editor o Administrator all\'utente, oppure usa un plugin di gestione capability.</p>';
            }
            wp_die( $msg, 'Olobuild Builder', [ 'response' => 403 ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $msg is static diagnostic HTML built above; the only dynamic parts (user_login, roles) are esc_html()'d.
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per routing/paginazione iframe builder; nessuna modifica di stato; valore sanitizzato via absint().
        $tpl_id = isset( $_GET['olo_tpl'] ) ? absint( wp_unslash( $_GET['olo_tpl'] ) ) : 0;

        // Modalità inline (1): siamo già su un permalink reale.
        if ( is_singular() ) {
            $this->setup_inline_preview_mode();
            return;
        }

        // Modalità inline (2): redirect automatico al primo post associato al template.
        if ( $tpl_id ) {
            $associated = get_posts( [
                'post_type'      => 'any',
                'meta_key'       => '_olo_template_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- lookup del post associato al template Olobuild; meta query necessaria alla funzione, una sola riga (posts_per_page=1, fields=ids), volume limitato.
                'meta_value'     => $tpl_id, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- lookup del post associato al template Olobuild; meta query necessaria alla funzione, una sola riga, volume limitato.
                'posts_per_page' => 1,
                'fields'         => 'ids',
                'post_status'    => [ 'publish', 'private', 'draft' ],
            ] );
            if ( ! empty( $associated ) ) {
                $url = get_permalink( $associated[0] );
                if ( $url ) {
                    $url = add_query_arg( [
                        'olo_builder_iframe' => 1,
                        'olo_tpl'            => $tpl_id,
                    ], $url );
                    wp_safe_redirect( $url );
                    exit;
                }
            }
        }

        // Fallback: standalone iframe template (home root, no associated post).
        $this->enqueue_builder_iframe_assets();
        include OLOBUILD_PATH . 'templates/builder-iframe.php';
        exit;
    }

    /** Handles registrati per il documento standalone builder-iframe.php. */
    private $iframe_style_handles  = [];
    private $iframe_script_handles = [];
    private $iframe_module_handles = [];

    /**
     * Registra/enqueue TUTTI gli asset di templates/builder-iframe.php tramite
     * l'API wp_enqueue (niente più <link>/<script> raw nel template: il documento
     * standalone li emette con wp_print_styles()/wp_print_scripts()). L'ordine è
     * garantito dalle dipendenze: uikit→icons, leaflet→markercluster→map, bridge ultimo.
     */
    private function enqueue_builder_iframe_assets() {
        $v       = OLOBUILD_VERSION;
        $leaflet = OLOBUILD_URL . 'assets/vendor/leaflet/';

        // ── CSS (cascade: uikit → frontend → overrides) ──
        $styles = [];
        $st = function ( $handle, $url, $deps = [] ) use ( &$styles, $v ) {
            wp_enqueue_style( $handle, $url, $deps, $v );
            $styles[] = $handle;
        };
        $st( 'olo-ifr-uikit', OLOBUILD_URL . 'assets/vendor/uikit/css/uikit.min.css' );
        $st( 'olo-ifr-frontend', OLOBUILD_URL . 'assets/css/frontend.css', [ 'olo-ifr-uikit' ] );
        $st( 'olo-ifr-proslider', OLOBUILD_URL . 'assets/css/olo-proslider.css', [ 'olo-ifr-frontend' ] );
        $st( 'olo-ifr-svganimator', OLOBUILD_URL . 'assets/css/olo-svganimator.css', [ 'olo-ifr-frontend' ] );
        if ( file_exists( OLOBUILD_PATH . 'assets/css/olo-livesearch.css' ) ) {
            $st( 'olo-ifr-livesearch', OLOBUILD_URL . 'assets/css/olo-livesearch.css', [ 'olo-ifr-frontend' ] );
        }
        $st( 'olo-ifr-leaflet', $leaflet . 'leaflet.css' );
        $st( 'olo-ifr-markercluster', $leaflet . 'leaflet.markercluster.css', [ 'olo-ifr-leaflet' ] );
        $st( 'olo-ifr-markercluster-default', $leaflet . 'leaflet.markercluster-default.css', [ 'olo-ifr-leaflet' ] );
        $st( 'olo-ifr-builder', OLOBUILD_URL . 'assets/css/iframe-builder.css', [ 'olo-ifr-frontend' ] );
        if ( file_exists( OLOBUILD_PATH . 'assets/css/olo-pdfviewer.css' ) ) {
            $st( 'olo-ifr-pdfviewer-css', OLOBUILD_URL . 'assets/css/olo-pdfviewer.css', [ 'olo-ifr-frontend' ] );
        }
        $booking_path = WP_PLUGIN_DIR . '/olo-booking/';
        $booking_url  = plugins_url( 'olo-booking/' );
        if ( file_exists( $booking_path . 'assets/css/booking-front.css' ) ) {
            $st( 'olo-ifr-booking', $booking_url . 'assets/css/booking-front.css' );
        }
        $vtour_path = WP_PLUGIN_DIR . '/olotour/';
        $vtour_url  = plugins_url( 'olotour/' );
        if ( file_exists( $vtour_path . 'assets/vendor/psv/psv-bundle.css' ) ) {
            $st( 'olo-ifr-psv', $vtour_url . 'assets/vendor/psv/psv-bundle.css' );
            $st( 'olo-ifr-vtour', $vtour_url . 'assets/css/olotour-viewer.css' );
        }
        $this->iframe_style_handles = $styles;

        // ── JS (uikit/icons → runtimes → bridge ultimo) ──
        $scripts = [];
        $modules = [];
        $js = function ( $handle, $url, $deps = [], $module = false ) use ( &$scripts, &$modules, $v ) {
            wp_enqueue_script( $handle, $url, $deps, $v, true );
            $scripts[] = $handle;
            if ( $module ) {
                $modules[] = $handle;
            }
        };
        $js( 'olo-ifr-uikit-js', OLOBUILD_URL . 'assets/vendor/uikit/js/uikit.min.js' );
        $js( 'olo-ifr-uikit-icons-js', OLOBUILD_URL . 'assets/vendor/uikit/js/uikit-icons.min.js', [ 'olo-ifr-uikit-js' ] );
        if ( file_exists( OLOBUILD_PATH . 'assets/js/olo-proslider.js' ) ) {
            $js( 'olo-ifr-proslider-js', OLOBUILD_URL . 'assets/js/olo-proslider.js', [], true );
        }
        if ( file_exists( OLOBUILD_PATH . 'assets/js/olo-postgrid.js' ) ) {
            $js( 'olo-ifr-postgrid-js', OLOBUILD_URL . 'assets/js/olo-postgrid.js', [], true );
        }
        $js( 'olo-ifr-leaflet-js', $leaflet . 'leaflet.js' );
        $js( 'olo-ifr-markercluster-js', $leaflet . 'leaflet.markercluster.js', [ 'olo-ifr-leaflet-js' ] );
        if ( file_exists( OLOBUILD_PATH . 'assets/js/olo-map.js' ) ) {
            $js( 'olo-ifr-map-js', OLOBUILD_URL . 'assets/js/olo-map.js', [ 'olo-ifr-markercluster-js' ] );
        }
        if ( file_exists( OLOBUILD_PATH . 'assets/js/olo-svganimator.js' ) ) {
            $js( 'olo-ifr-svganimator-js', OLOBUILD_URL . 'assets/js/olo-svganimator.js', [], true );
        }
        if ( file_exists( OLOBUILD_PATH . 'assets/js/olo-viewer360.js' ) ) {
            $js( 'olo-ifr-viewer360-js', OLOBUILD_URL . 'assets/js/olo-viewer360.js' );
        }
        if ( file_exists( OLOBUILD_PATH . 'assets/vendor/pdfjs/pdf.min.js' ) ) {
            $js( 'olo-ifr-pdfjs', OLOBUILD_URL . 'assets/vendor/pdfjs/pdf.min.js' );
            $worker = esc_url( OLOBUILD_URL . 'assets/vendor/pdfjs/pdf.worker.min.js' );
            wp_add_inline_script( 'olo-ifr-pdfjs', "window.oloPdfViewerData={workerUrl:'{$worker}'};window.oloPdfProData={workerUrl:'{$worker}'};", 'after' );
        }
        if ( file_exists( OLOBUILD_PATH . 'assets/vendor/pageflip/page-flip.browser.js' ) ) {
            $js( 'olo-ifr-pageflip', OLOBUILD_URL . 'assets/vendor/pageflip/page-flip.browser.js' );
        }
        if ( file_exists( OLOBUILD_PATH . 'assets/js/olo-pdfviewer.js' ) ) {
            $js( 'olo-ifr-pdfviewer-js', OLOBUILD_URL . 'assets/js/olo-pdfviewer.js' );
        }
        if ( file_exists( OLOBUILD_PATH . 'assets/js/olo-pdfpro.js' ) ) {
            $js( 'olo-ifr-pdfpro-js', OLOBUILD_URL . 'assets/js/olo-pdfpro.js' );
        }
        if ( file_exists( OLOBUILD_PATH . 'assets/js/olo-utils.js' ) ) {
            $js( 'olo-ifr-utils-js', OLOBUILD_URL . 'assets/js/olo-utils.js', [], true );
        }
        if ( file_exists( $booking_path . 'assets/js/booking-front.js' ) ) {
            $js( 'olo-ifr-booking-js', $booking_url . 'assets/js/booking-front.js' );
        }
        if ( file_exists( $booking_path . 'assets/js/olo-restaurant-booking.js' ) ) {
            $js( 'olo-ifr-restaurant-booking-js', $booking_url . 'assets/js/olo-restaurant-booking.js' );
        }
        if ( file_exists( $vtour_path . 'assets/vendor/psv/psv-bundle.js' ) ) {
            $js( 'olo-ifr-psv-js', $vtour_url . 'assets/vendor/psv/psv-bundle.js' );
            $js( 'olo-ifr-vtour-js', $vtour_url . 'assets/js/olotour-viewer.js' );
        }
        // Bridge: deve caricarsi DOPO ogni runtime → dipende da tutti gli handle sopra.
        wp_enqueue_script( 'olo-ifr-bridge', OLOBUILD_URL . 'assets/js/iframe-bridge.js', $scripts, $v, true );
        $scripts[] = 'olo-ifr-bridge';

        $this->iframe_script_handles = $scripts;
        $this->iframe_module_handles = $modules;
        if ( $modules ) {
            add_filter( 'script_loader_tag', [ $this, 'iframe_module_script_tag' ], 10, 2 );
        }
    }

    /** @internal Aggiunge type="module" agli script ES module dell'iframe del builder. */
    public function iframe_module_script_tag( $tag, $handle ) {
        if ( in_array( $handle, $this->iframe_module_handles, true ) ) {
            $tag = preg_replace( '/<script /', '<script type="module" ', $tag, 1 );
        }
        return $tag;
    }

    /**
     * Inline mode: WP renderizza la pagina; sostituiamo content con il placeholder
     * del bridge e iniettiamo gli stessi asset del builder-iframe.php.
     */
    private function setup_inline_preview_mode() {
        // Sostituisce il content del post con il placeholder che bridge.js aggiornerà.
        add_filter( 'the_content', [ $this, 'inline_preview_replace_content' ], 999 );

        // Sostituisce eventuale block `core/post-content` (block theme).
        add_filter( 'render_block', [ $this, 'inline_preview_replace_post_content_block' ], 100, 2 );

        // CSS inline in head (mode-specific) + asset bridge in footer.
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_inline_preview_assets' ], 100 );
        add_action( 'wp_head', [ $this, 'print_inline_preview_styles' ], 99 );

        // Quando l'utente sta editando un template di tipo header o footer
        // nel builder iframe, evitiamo il DOPPIO render (header/footer dal tema
        // + template in editing renderizzato come body): definiamo una costante
        // che Olobuild_Header_Integration/Olobuild_Footer_Integration leggono per
        // skippare il rendering dell'header/footer attivo.
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per routing iframe builder (skip doppio render header/footer); nessuna modifica di stato; valore sanitizzato via absint().
        $tpl_id = isset( $_GET['olo_tpl'] ) ? absint( wp_unslash( $_GET['olo_tpl'] ) ) : 0;
        if ( $tpl_id && class_exists( 'Olobuild_Database' ) ) {
            $db = new Olobuild_Database();
            $tpl = $db->get_template( $tpl_id );
            $editing_type = is_array( $tpl ) ? ( $tpl['type'] ?? '' ) : '';
            if ( $editing_type === 'header' && ! defined( 'OLOBUILD_BUILDER_EDITING_HEADER' ) ) {
                define( 'OLOBUILD_BUILDER_EDITING_HEADER', true );
            }
            if ( $editing_type === 'footer' && ! defined( 'OLOBUILD_BUILDER_EDITING_FOOTER' ) ) {
                define( 'OLOBUILD_BUILDER_EDITING_FOOTER', true );
            }
        }

        // Disabilita admin bar e altri overlay
        show_admin_bar( false );
    }

    /**
     * Empty-state HTML mostrato nell'iframe del builder durante il boot iniziale,
     * quando il template è interamente vuoto, oppure quando solo il body è vuoto
     * ma header/footer sono presenti. Markup unico = UX coerente nei 3 casi.
     *
     * Uso: server-side (templates/builder-iframe.php, filtri inline_preview_*,
     * REST builder_render); il bridge JS replica lo stesso markup via oloData.
     */
    public static function get_iframe_empty_html() {
        $title       = esc_html__( 'Pagina vuota', 'olobuild' );
        $text        = esc_html__( 'Aggiungi un modulo o scegli un layout per iniziare', 'olobuild' );
        $add_module  = esc_html__( 'Aggiungi modulo', 'olobuild' );
        $add_row     = esc_html__( 'Scegli layout', 'olobuild' );
        return '<div class="olo-iframe-empty">'
             . '<div class="olo-iframe-empty-card">'
             . '<h3 class="olo-iframe-empty-title">' . $title . '</h3>'
             . '<p class="olo-iframe-empty-text">' . $text . '</p>'
             . '<div class="olo-iframe-empty-actions">'
             . '<button type="button" class="olo-iframe-empty-btn" data-olo-empty-action="add-module" aria-label="' . $add_module . '">'
             . '<span class="olo-iframe-empty-btn-icon">'
             . '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
             . '<rect x="3" y="3" width="18" height="18" rx="2" stroke-dasharray="3 3"/>'
             . '<path d="M12 8v8M8 12h8"/>'
             . '</svg>'
             . '</span>'
             . '<span class="olo-iframe-empty-btn-label">' . $add_module . '</span>'
             . '</button>'
             . '<button type="button" class="olo-iframe-empty-btn" data-olo-empty-action="add-row" aria-label="' . $add_row . '">'
             . '<span class="olo-iframe-empty-btn-icon">'
             . '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
             . '<rect x="3" y="3" width="7" height="7" rx="1"/>'
             . '<rect x="14" y="3" width="7" height="7" rx="1"/>'
             . '<rect x="3" y="14" width="7" height="7" rx="1"/>'
             . '<rect x="14" y="14" width="7" height="7" rx="1"/>'
             . '</svg>'
             . '</span>'
             . '<span class="olo-iframe-empty-btn-label">' . $add_row . '</span>'
             . '</button>'
             . '</div>'
             . '</div>'
             . '</div>';
    }

    /** @internal Filter callback: rimpiazza the_content con placeholder. */
    public function inline_preview_replace_content( $content ) {
        return '<div id="olo-iframe-root">' . self::get_iframe_empty_html() . '</div>';
    }

    /** @internal Filter callback: rimpiazza core/post-content block (block themes). */
    public function inline_preview_replace_post_content_block( $html, $block ) {
        if ( ( $block['blockName'] ?? '' ) === 'core/post-content' ) {
            return '<div id="olo-iframe-root">' . self::get_iframe_empty_html() . '</div>';
        }
        return $html;
    }

    /** @internal Enqueue degli stessi asset di templates/builder-iframe.php. */
    public function enqueue_inline_preview_assets() {
        // Core CSS (mirror del builder-iframe.php)
        wp_enqueue_style( 'olo-uikit-inline', OLOBUILD_URL . 'assets/vendor/uikit/css/uikit.min.css', [], OLOBUILD_VERSION );
        wp_enqueue_style( 'olo-frontend-inline', OLOBUILD_URL . 'assets/css/frontend.css', [], OLOBUILD_VERSION );
        wp_enqueue_style( 'olo-iframe-builder-inline', OLOBUILD_URL . 'assets/css/iframe-builder.css', [], OLOBUILD_VERSION );
        // Tile-specific CSS (mirror del builder-iframe.php)
        wp_enqueue_style( 'olo-proslider-css', OLOBUILD_URL . 'assets/css/olo-proslider.css', [], OLOBUILD_VERSION );
        // Core JS
        wp_enqueue_script( 'olo-uikit-inline', OLOBUILD_URL . 'assets/vendor/uikit/js/uikit.min.js', [], OLOBUILD_VERSION, true );
        wp_enqueue_script( 'olo-uikit-icons-inline', OLOBUILD_URL . 'assets/vendor/uikit/js/uikit-icons.min.js', [ 'olo-uikit-inline' ], OLOBUILD_VERSION, true );
        // Tile runtimes (proslider, postgrid, map, ecc.)
        if ( file_exists( OLOBUILD_PATH . 'assets/js/olo-proslider.js' ) ) {
            wp_enqueue_script( 'olo-proslider-js', OLOBUILD_URL . 'assets/js/olo-proslider.js', [], OLOBUILD_VERSION, true );
        }
        if ( file_exists( OLOBUILD_PATH . 'assets/js/olo-postgrid.js' ) ) {
            wp_enqueue_script( 'olo-postgrid-js', OLOBUILD_URL . 'assets/js/olo-postgrid.js', [], OLOBUILD_VERSION, true );
        }
        if ( file_exists( OLOBUILD_PATH . 'assets/js/olo-utils.js' ) ) {
            wp_enqueue_script( 'olo-utils', OLOBUILD_URL . 'assets/js/olo-utils.js', [], OLOBUILD_VERSION, true );
        }
        // Bridge: deve essere DOPO ogni runtime (postMessage receiver)
        wp_enqueue_script( 'olo-iframe-bridge', OLOBUILD_URL . 'assets/js/iframe-bridge.js', [], OLOBUILD_VERSION, true );
        // Mode flag letto dal bridge.js → segnala al parent (Vue useIframeBridge) che
        // questa è una pagina WP reale, header/footer NON vanno re-iniettati.
        wp_add_inline_script( 'olo-iframe-bridge', "window.OLO_IFRAME_MODE='inline';", 'before' );
    }

    /** @internal Stile inline per la modalità preview (mirror del builder-iframe.php). */
    public function print_inline_preview_styles() {
        ?>
        <style id="olo-inline-preview-styles">
        #olo-iframe-root { min-height: 60vh; }
        .olo-iframe-empty { display: flex; align-items: center; justify-content: center; min-height: 40vh; color: #9CA3AF; font-family: system-ui, sans-serif; font-size: 14px; }
        .olo-site-header.olo-header-sticky,
        .olo-site-header.olo-header-classic.olo-header-sticky,
        .olo-sticky-cover, .olo-sticky-reveal {
            position: relative !important; top: auto !important; z-index: auto !important;
        }
        .olo-template { width: 100% !important; position: static !important; left: auto !important; transform: none !important; }
        .olo-floatingpanel, .olo-fp-wrapper { scroll-margin-top: 80px; }
        /* Hide WP admin bar gap if any */
        html { margin-top: 0 !important; }
        </style>
        <?php
    }

    public function admin_menu() {
        // Use 'manage_options' as primary capability — admins always have it.
        // 'edit_posts' can be missing on sites with custom role plugins (Tutor LMS, etc.)
        add_menu_page(
            __( 'Olobuild', 'olobuild' ),
            __( 'Olobuild', 'olobuild' ),
            'manage_options',
            'olobuild',
            [ $this, 'render_dashboard_page' ],
            OLOBUILD_URL . 'assets/img/ob-menu-v2.png',
            30
        );

        // Override the auto-generated first submenu item
        add_submenu_page(
            'olobuild',
            __( 'Dashboard', 'olobuild' ),
            __( 'Dashboard', 'olobuild' ),
            'manage_options',
            'olobuild',
            [ $this, 'render_dashboard_page' ]
        );

        /*
         * Restyling Fase 2: il menu si accorcia a Dashboard + 4 aree
         * (Costruisci · Media · Raccolta · Sistema). Gli slug NON cambiano —
         * ogni area apre la sua pagina d'ingresso di sempre e i vecchi URL
         * continuano a funzionare — cambiano solo etichette e ordine.
         */
        add_submenu_page(
            'olobuild',
            __( 'Costruisci', 'olobuild' ),
            __( 'Costruisci', 'olobuild' ),
            'manage_options',
            'olobuilder-templates',
            [ $this, 'render_builder_page' ]
        );

        add_submenu_page(
            'olobuild',
            __( 'Media', 'olobuild' ),
            __( 'Media', 'olobuild' ),
            'upload_files',
            'olo-media-search',
            [ 'Olobuild_Media_Search', 'render_page' ]
        );

        add_submenu_page(
            'olobuild',
            __( 'Raccolta', 'olobuild' ),
            __( 'Raccolta', 'olobuild' ),
            'manage_options',
            'olo-form-submissions',
            [ 'Olobuild_Form_Submissions', 'render_page' ]
        );

        add_submenu_page(
            'olobuild',
            __( 'Sistema', 'olobuild' ),
            __( 'Sistema', 'olobuild' ),
            'manage_options',
            'olobuilder-settings',
            [ $this, 'render_configurazione_page' ]
        );

        // Registrata ma senza voce di menu (rimossa in admin_menu_trim):
        // vive nella sub-nav dell'area Raccolta, l'URL resta lo stesso.
        add_submenu_page(
            'olobuild',
            __( 'Newsletter', 'olobuild' ),
            __( 'Newsletter', 'olobuild' ),
            'manage_options',
            'olo-newsletter',
            [ 'Olobuild_Newsletter', 'render_page' ]
        );
    }

    /**
     * Toglie dal menu le voci traslocate nelle sub-nav delle aree (restyling
     * Fase 2). remove_submenu_page rimuove SOLO la voce: le pagine restano
     * registrate e i loro URL continuano a funzionare. Priorità 999 perché
     * Strumenti e Import/Export sono registrate da altre classi a priorità 10.
     */
    public function admin_menu_trim() {
        remove_submenu_page( 'olobuild', 'olo-newsletter' );    // → sub-nav Raccolta
        remove_submenu_page( 'olobuild', 'olo-tools' );         // → sub-nav Sistema
        remove_submenu_page( 'olobuild', 'olo-import-export' ); // → sub-nav Costruisci
    }

    /**
     * Inject CSS icons for each Olobuild submenu item using dashicons.
     */
    public function admin_submenu_icons() {
        $screen = get_current_screen();
        if ( ! $screen ) return;
        // Only on Olobuild pages
        $id = $screen->id;
        if ( ! str_contains( $id, 'olobuild' )
            && ! str_contains( $id, 'olo-' ) ) {
            return;
        }

        $icons = [
            'olobuild'          => '\f226', // dashicons-dashboard
            'olobuilder-templates'=> '\f538', // dashicons-layout
            'olobuilder-settings' => '\f107', // dashicons-admin-generic
            'olo-media-search'    => '\f128', // dashicons-format-image
            'olo-form-submissions'=> '\f466', // dashicons-email-alt
            'olo-newsletter'      => '\f465', // dashicons-email
            'olo-analytics'       => '\f185', // dashicons-chart-bar
            'olo-cookie-consent'  => '\f332', // dashicons-shield
            'olo-seo'             => '\f179', // dashicons-search
            'olo-redirects'       => '\f503', // dashicons-randomize
            'olo-performance'     => '\f228', // dashicons-performance
            'olo-woo-templates'   => '\f174', // dashicons-cart
            'olo-global-popups'   => '\f479', // dashicons-admin-page
            'olo-role-manager'    => '\f110', // dashicons-admin-users
            'olo-import-export'   => '\f316', // dashicons-download
            'olo-white-label'     => '\f323', // dashicons-tag
            'olo-tools'           => '\f533', // dashicons-admin-tools
        ];

        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- $slug/$code come from the hardcoded internal $icons map above (fixed page slugs + dashicon codepoints).
        echo '<style>';
        foreach ( $icons as $slug => $code ) {
            echo '#adminmenu .wp-submenu a[href*="page=' . $slug . '"]::before{';
            echo 'font-family:dashicons;content:"' . $code . '";margin-right:6px;font-size:16px;vertical-align:middle;opacity:.7;';
            echo '}';
        }
        echo '</style>';
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    private function detect_theme_colors() {
        $bg_color   = '';
        $text_color = '';

        // Strategy 1: Block theme with theme.json (WP 5.9+)
        if ( function_exists( 'wp_get_global_styles' ) ) {
            $colors = wp_get_global_styles(
                [ 'color' ],
                [ 'transforms' => [ 'resolve-variables' ] ]
            );
            if ( ! empty( $colors['background'] ) ) $bg_color   = $colors['background'];
            if ( ! empty( $colors['text'] ) )        $text_color = $colors['text'];
        }

        // Strategy 2: Palette slug "base"/"contrast"
        if ( empty( $bg_color ) && function_exists( 'wp_get_global_settings' ) ) {
            $palette = wp_get_global_settings( [ 'color', 'palette', 'theme' ] );
            if ( is_array( $palette ) ) {
                foreach ( $palette as $item ) {
                    if ( $item['slug'] === 'base' && empty( $bg_color ) )       $bg_color   = $item['color'];
                    if ( $item['slug'] === 'contrast' && empty( $text_color ) ) $text_color = $item['color'];
                }
            }
        }

        // Strategy 3: Classic theme - get_background_color()
        if ( empty( $bg_color ) ) {
            $classic_bg = get_background_color();
            if ( ! empty( $classic_bg ) ) $bg_color = '#' . ltrim( $classic_bg, '#' );
        }

        // Final fallback
        return [
            'background' => $bg_color ?: '#ffffff',
            'text'       => $text_color ?: '#333333',
        ];
    }

    public function admin_enqueue_scripts( $hook ) {
        // Shared admin CSS for ALL Olobuild pages
        //
        // Stesso riconoscimento di admin_body_class(), e per la stessa ragione:
        // 'olo-' come sottostringa faceva servire il foglio di stile di
        // Olobuild dentro le pagine di ogni altro prodotto della famiglia.
        if ( str_contains( $hook, 'olobuild' ) || in_array( $hook, self::extra_olo_screen_ids(), true ) ) {
            wp_enqueue_style(
                'olo-admin-css',
                OLOBUILD_URL . 'assets/css/olo-admin.css',
                [],
                OLOBUILD_VERSION
            );

            // Palette globale ⌘K su TUTTE le pagine Olobuild (prima viveva in
            // dashboard.js: sulle altre pagine il trigger in topbar era morto).
            // Voci menu/azioni nel localize; pagine+template via REST al primo
            // uso; campi Configurazione dal JSON statico generato a build time.
            wp_enqueue_script(
                'olo-palette-js',
                OLOBUILD_URL . 'assets/js/olo-palette.js',
                [],
                OLOBUILD_VERSION,
                true
            );
            $palette_menu = [];
            foreach ( self::dashboard_manage_tiles() as $t ) {
                $palette_menu[] = [ 'label' => $t['label'], 'hint' => $t['hint'], 'href' => $t['href'] ];
            }
            foreach ( self::dashboard_system_chips() as $t ) {
                $palette_menu[] = [ 'label' => $t['label'], 'hint' => '', 'href' => $t['href'] ];
            }
            wp_localize_script( 'olo-palette-js', 'oloPaletteConfig', [
                'restUrl'  => esc_url_raw( rest_url( 'olobuild/v1/' ) ),
                'nonce'    => wp_create_nonce( 'wp_rest' ),
                'adminUrl' => admin_url(),
                'indexUrl' => OLOBUILD_URL . 'assets/data/settings-search-index.json?ver=' . OLOBUILD_VERSION,
                'menu'     => $palette_menu,
                'i18n'     => [
                    'searchPh'   => __( 'Cerca pagine, template, impostazioni…', 'olobuild' ),
                    'noResults'  => __( 'Nessun risultato', 'olobuild' ),
                    'loading'    => __( 'Carico l\'indice…', 'olobuild' ),
                    'goto'       => __( 'Vai a', 'olobuild' ),
                    'settings'   => __( 'Impostazioni', 'olobuild' ),
                    'pages'      => __( 'Pagine', 'olobuild' ),
                    'templates'  => __( 'Template', 'olobuild' ),
                    'goField'    => __( 'Vai al campo', 'olobuild' ),
                    'openEditor' => __( 'Apri editor', 'olobuild' ),
                    'openTab'    => __( 'Configurazione', 'olobuild' ),
                    'nav'        => __( 'scorri', 'olobuild' ),
                    'open'       => __( 'apri', 'olobuild' ),
                    'scope'      => __( 'pagine · template · impostazioni · azioni', 'olobuild' ),
                ],
            ] );
        }

        // Cockpit CSS condiviso per tutte le pagine top-level che usano cockpit_shell_open()
        if ( in_array( $hook, self::cockpit_screen_ids(), true ) ) {
            wp_enqueue_style(
                'olo-cockpit-css',
                OLOBUILD_URL . 'assets/css/dashboard.css',
                [],
                OLOBUILD_VERSION
            );
        }

        // Submissions cockpit (Fase 3)
        if ( $hook === 'olobuild_page_olo-form-submissions' ) {
            wp_enqueue_script(
                'olo-submissions-js',
                OLOBUILD_URL . 'assets/js/olo-submissions.js',
                [],
                OLOBUILD_VERSION,
                true
            );
            wp_localize_script( 'olo-submissions-js', 'oloSubmissionsConfig', [
                'restUrl' => esc_url_raw( rest_url( 'olobuild/v1/' ) ),
                'nonce'   => wp_create_nonce( 'wp_rest' ),
                'i18n'    => [
                    'noResults'    => __( 'Nessun invio trovato.', 'olobuild' ),
                    'noResultsHint'=> __( 'Prova a cambiare filtro o aspetta nuovi messaggi.', 'olobuild' ),
                    'markRead'     => __( 'Segna come letto', 'olobuild' ),
                    'markUnread'   => __( 'Segna come non letto', 'olobuild' ),
                    'confirmDelete'=> __( "Eliminare definitivamente questo invio? L'azione non si può annullare.", 'olobuild' ),
                    'deleted'      => __( 'Invio eliminato', 'olobuild' ),
                    'deleteFailed' => __( 'Errore eliminazione', 'olobuild' ),
                    'loadFailed'   => __( 'Errore caricamento. Riprova.', 'olobuild' ),
                    'fields'       => __( 'Campi compilati', 'olobuild' ),
                    'metadata'     => __( 'Metadati', 'olobuild' ),
                    'ip'           => 'IP',
                    'userAgent'    => 'User Agent',
                    'submittedAt'  => __( 'Inviato il', 'olobuild' ),
                ],
            ] );
        }

        // Dashboard cockpit (toplevel_page_olobuild)
        if ( 'toplevel_page_olobuild' === $hook ) {
            wp_enqueue_style(
                'olo-dashboard-css',
                OLOBUILD_URL . 'assets/css/dashboard.css',
                [],
                OLOBUILD_VERSION
            );
            wp_enqueue_script(
                'olo-dashboard-js',
                OLOBUILD_URL . 'assets/js/dashboard.js',
                [],
                OLOBUILD_VERSION,
                true
            );

            // Boot data per evitare flash di skeleton
            $rest = new Olobuild_Rest_Api();
            $req_recent = new WP_REST_Request( 'GET' );
            $req_recent->set_query_params( [ 'limit' => 6 ] );
            $req_changelog = new WP_REST_Request( 'GET' );
            $req_changelog->set_query_params( [ 'limit' => 3 ] );

            $boot_kpis      = $rest->dashboard_kpis( null );
            $boot_recent    = $rest->dashboard_recent( $req_recent );
            $boot_changelog = $rest->dashboard_changelog( $req_changelog );

            $user_id = get_current_user_id();
            $user_prefs = get_user_meta( $user_id, 'olo_dashboard_prefs', true );
            if ( ! is_array( $user_prefs ) ) $user_prefs = [];
            $user_prefs = wp_parse_args( $user_prefs, [
                'pinned'      => [ 'tpl', 'cfg', 'media' ],
                'rail'        => 'expanded',
                /*
                 * ⚠️ SPENTO, e deve dire lo stesso che dice `admin_body_class()`.
                 * Questo valore di serie sta scritto in QUATTRO punti (qui, la
                 * rotta REST, `dashboard.js` e la classe sul body): se uno dice
                 * acceso e un altro spento, l'interruttore parte credendo il
                 * contrario di quello che si vede, e il primo clic non fa
                 * niente.
                 */
                'app_mode'    => false,
                'banners_off' => [],
            ] );

            wp_localize_script( 'olo-dashboard-js', 'oloDashboardData', [
                'restUrl'     => esc_url_raw( rest_url( 'olobuild/v1/' ) ),
                'adminUrl'    => admin_url(),
                'nonce'       => wp_create_nonce( 'wp_rest' ),
                'pluginUrl'   => OLOBUILD_URL,
                'version'     => OLOBUILD_VERSION,
                'prefs'       => $user_prefs,
                'boot' => [
                    'kpis'      => is_wp_error( $boot_kpis )      ? [] : $boot_kpis->get_data(),
                    'recent'    => is_wp_error( $boot_recent )    ? [] : $boot_recent->get_data(),
                    'changelog' => is_wp_error( $boot_changelog ) ? [] : $boot_changelog->get_data(),
                ],
                'i18n' => [
                    'live'         => __( 'live', 'olobuild' ),
                    'draft'        => __( 'bozza', 'olobuild' ),
                    'pin'          => __( 'Aggiungi ai preferiti', 'olobuild' ),
                    'unpin'        => __( 'Rimuovi dai preferiti', 'olobuild' ),
                    'expand'       => __( 'Espandi pannello', 'olobuild' ),
                    'collapse'     => __( 'Comprimi pannello', 'olobuild' ),
                    'searchPh'     => __( 'Cerca pagine, template, impostazioni…', 'olobuild' ),
                    'noResults'    => __( 'Nessun risultato', 'olobuild' ),
                    'emptyRecent'  => __( 'Nessuna attività recente. Crea o modifica una pagina per iniziare.', 'olobuild' ),
                    'creating'     => __( 'Creazione…', 'olobuild' ),
                    'untitled'     => __( 'Senza titolo', 'olobuild' ),
                    'newPageError' => __( 'Errore nella creazione della pagina. Riprova.', 'olobuild' ),
                ],
            ] );
            return;
        }

        // Configurazione page — admin Vue app
        if ( 'olobuild_page_olobuilder-settings' === $hook ) {
            // CSS is injected inline by Vite IIFE build + render_configurazione_page inline styles
            wp_enqueue_script(
                'olo-admin-settings-js',
                OLOBUILD_URL . 'assets/js/admin-settings.js',
                [],
                OLOBUILD_VERSION,
                true
            );

            $style_system = Olobuild_Style_System::instance();
            // Timestamp ultimo save (mostrato nella savebar). Ogni tab al suo POST aggiorna
            // l'option `olo_settings_last_saved`; questo è solo il bootstrap iniziale.
            $last_saved_ts = (int) get_option( 'olobuild_settings_last_saved', 0 );
            $last_saved_str = '';
            if ( $last_saved_ts > 0 ) {
                $diff = max( 0, time() - $last_saved_ts );
                if ( $diff < 60 ) {
                    $last_saved_str = __( 'pochi secondi fa', 'olobuild' );
                } elseif ( $diff < 3600 ) {
                    /* translators: %d: minutes elapsed */
                    $last_saved_str = sprintf( __( '%d minuti fa', 'olobuild' ), intval( $diff / 60 ) );
                } else {
                    $last_saved_str = wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $last_saved_ts );
                }
            }
            wp_localize_script( 'olo-admin-settings-js', 'oloData', [
                'restUrl'           => esc_url_raw( rest_url( 'olobuild/v1' ) . '/' ),
                'nonce'             => wp_create_nonce( 'wp_rest' ),
                'importsDisabled'   => olobuild_imports_disabled(),
                'perfNonce'         => wp_create_nonce( 'olo_perf_action' ),
                'ajaxUrl'           => admin_url( 'admin-ajax.php' ),
                'adminUrl'          => admin_url(),
                'siteUrl'           => home_url( '/' ),
                'pluginUrl'         => OLOBUILD_URL,
                'version'           => OLOBUILD_VERSION,
                'locale'            => olobuild_current_locale(),
                'translations'      => olobuild_get_translations_map(),
                'styles'            => $style_system->get_styles(),
                'presets'           => $style_system->get_presets(),
                'globalColors'      => $style_system->get_global_colors(),
                'globalTypography'  => $style_system->get_global_typography(),
                'settingsLastSaved' => $last_saved_str,
            ] );
            return;
        }

        if ( 'olobuild_page_olobuilder-templates' !== $hook ) {
            return;
        }

        // Load full WP media framework (needed for wp.media settings & templates)
        wp_enqueue_media();

        // Carica frontend.css anche nel builder admin: il canvas Vue usa .olo-template
        // e ha bisogno delle stesse regole, including @keyframes entrance animations.
        wp_enqueue_style(
            'olo-builder-frontend-styles',
            OLOBUILD_URL . 'assets/css/frontend.css',
            [],
            OLOBUILD_VERSION
        );

        // Cache-busting basato sul mtime reale dei file (oltre OLOBUILD_VERSION),
        // così ogni rebuild forza il reload del bundle anche se la versione
        // del plugin non è stata bumpata (utile in dev/staging).
        $css_path = OLOBUILD_PATH . 'assets/css/builder.css';
        $js_path  = OLOBUILD_PATH . 'assets/js/builder.js';
        $mtime    = file_exists( $js_path ) ? filemtime( $js_path ) : 0;
        $css_ver  = OLOBUILD_VERSION . '.' . ( file_exists( $css_path ) ? filemtime( $css_path ) : 0 );
        // OLOBUILD_VERSION + mtime sono sufficienti: ogni build aggiorna mtime,
        // ogni release aggiorna OLOBUILD_VERSION → il browser riscarica solo quando
        // serve. Aggiungere time() come prima rompeva la cache del browser
        // ad ogni F5 in admin (4 MB di builder.js riscaricati senza motivo).
        // Per forzare il reload in dev: aggiungere `?olo_no_cache=1`.
        $js_ver   = OLOBUILD_VERSION . '.' . $mtime;
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per cache-busting asset in admin; nessuna modifica di stato; sola verifica di presenza del flag.
        if ( isset( $_GET['olo_no_cache'] ) ) {
            $js_ver .= '.' . time();
        }

        // Build ESM con cssCodeSplit:false: Vite estrae il CSS in assets/css/builder.css.
        // Il file esiste sempre nelle build correnti; il check resta come guardia.
        if ( file_exists( $css_path ) ) {
            wp_enqueue_style(
                'olobuilder-css',
                OLOBUILD_URL . 'assets/css/builder.css',
                [],
                $css_ver
            );
        }

        wp_enqueue_script(
            'olobuilder-js',
            OLOBUILD_URL . 'assets/js/builder.js',
            [ 'media-views' ],
            $js_ver,
            true
        );

        // Il bundle del builder e' un ES module (code-splitting Vite: l'entry importa i
        // chunk vendor/tiptap/icons via import relativi → richiede type="module"). oloData
        // resta disponibile: wp_localize_script stampa uno <script> classico PRIMA, eseguito
        // durante il parse, mentre il modulo e' deferito. Filtro idempotente sul solo handle.
        add_filter( 'script_loader_tag', function ( $tag, $handle ) {
            if ( 'olobuilder-js' !== $handle ) {
                return $tag;
            }
            // WP concatena nel $tag anche gli inline before/after dell'handle: il
            // type="module" va messo SOLO sul tag con src= (il bundle vero), non sugli
            // inline (altrimenti builder.js resta classico e l'import ESM esplode).
            return preg_replace_callback(
                '#<script\b([^>]*\bsrc=[^>]*)>#i',
                function ( $m ) {
                    $attrs = $m[1];
                    if ( preg_match( '/\stype=([\'"]).*?\1/', $attrs ) ) {
                        $attrs = preg_replace( '/\stype=([\'"]).*?\1/', ' type="module"', $attrs, 1 );
                        return '<script' . $attrs . '>';
                    }
                    return '<script type="module"' . $attrs . '>';
                },
                $tag
            );
        }, 10, 2 );

        // Auto-thumbnail capture: listener su `olobuild:saved` → html2canvas → upload
        wp_enqueue_script(
            'olo-thumb-capture',
            OLOBUILD_URL . 'assets/js/olo-thumb-capture.js',
            [],
            OLOBUILD_VERSION,
            true
        );
        wp_localize_script( 'olo-thumb-capture', 'oloThumbConfig', [
            'restUrl'   => esc_url_raw( rest_url( 'olobuild/v1/' ) ),
            'nonce'     => wp_create_nonce( 'wp_rest' ),
            'vendorUrl' => OLOBUILD_URL . 'assets/vendor/html2canvas.min.js?v=' . OLOBUILD_VERSION,
            'debug'     => defined( 'WP_DEBUG' ) && WP_DEBUG,
        ] );

        $style_system = Olobuild_Style_System::instance();

        // Inject primary color as CSS custom properties so admin UI can use it
        $styles   = $style_system->get_styles();
        $primary  = $styles['colors']['primary'] ?? '#6366F1';
        $hex      = ltrim( $primary, '#' );
        $r        = hexdec( substr( $hex, 0, 2 ) );
        $g        = hexdec( substr( $hex, 2, 2 ) );
        $b        = hexdec( substr( $hex, 4, 2 ) );
        wp_add_inline_style( 'olobuilder-css',
            ':root { --olo-color-primary: ' . esc_attr( $primary ) . '; --olo-primary-rgb: ' . "$r $g $b" . '; }'
        );

        // Validate post_id — user must be able to edit that post
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per bootstrap del builder admin (preselezione post); nessuna modifica di stato; valore sanitizzato via absint + capability check edit_post sotto.
        $post_id = absint( wp_unslash( $_GET['post_id'] ?? 0 ) );
        if ( $post_id && ! current_user_can( 'edit_post', $post_id ) ) {
            $post_id = 0;
        }

        // v3.55.42 — quando l'utente apre il builder per un template (senza un post
        // specifico in URL), risolvi via PHP il permalink REALE del post collegato
        // nei settings del template. Senza questo, il pulsante "Reale" costruiva un
        // URL `?p=ID` lato JS che funziona solo per post_type='post' e dava 404
        // sulle pages (che usano `?page_id=ID`) o sui CPT.
        $linked_post_permalink = '';
        $linked_post_id = 0;
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per bootstrap del builder admin (risoluzione permalink template); nessuna modifica di stato; valore sanitizzato via absint.
        $template_id_for_link = absint( wp_unslash( $_GET['template_id'] ?? 0 ) );
        if ( $template_id_for_link && class_exists( 'Olobuild_Database' ) ) {
            $db_for_link = new Olobuild_Database();
            $tpl_for_link = $db_for_link->get_template( $template_id_for_link );
            if ( $tpl_for_link && ! empty( $tpl_for_link['settings']['post_id'] ) ) {
                $linked_post_id = absint( $tpl_for_link['settings']['post_id'] );
                if ( $linked_post_id ) {
                    $linked_post_permalink = (string) get_permalink( $linked_post_id );
                }
            }
        }

        wp_localize_script( 'olobuilder-js', 'oloData', [
            'restUrl'        => esc_url_raw( rest_url( 'olobuild/v1' ) ),
            'nonce'          => wp_create_nonce( 'wp_rest' ),
            'importsDisabled' => olobuild_imports_disabled(),
            'userId'         => get_current_user_id(),
            'userName'       => wp_get_current_user()->display_name,
            'version'        => OLOBUILD_VERSION,
            'pluginUrl'      => OLOBUILD_URL,
            'brandName'      => apply_filters( 'olobuild_brand_name', 'Olobuild' ),
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per bootstrap del builder admin (template in editing); nessuna modifica di stato; valore sanitizzato via absint.
            'templateId'     => absint( wp_unslash( $_GET['template_id'] ?? 0 ) ),
            'postId'         => $post_id,
            'postPermalink'  => $post_id ? get_permalink( $post_id ) : '',
            // Permalink REALE del post collegato al template (vedi commento sopra).
            // Usato dal pulsante "Anteprima reale" come priorità 2 se postPermalink è vuoto.
            'linkedPostId'        => $linked_post_id,
            'linkedPostPermalink' => $linked_post_permalink,
            'themeColors'    => $this->detect_theme_colors(),
            'styles'         => $style_system->get_styles(),
            'stylesCss'      => $style_system->generate_css(),
            'presets'        => $style_system->get_presets(),
            'globalColors'   => $style_system->get_global_colors(),
            'globalTypography' => $style_system->get_global_typography(),
            'wpMenus'        => $this->get_wp_menus(),
            'activeHeaderId' => (int) get_option( 'olobuild_active_header', 0 ),
            'activeFooterId' => (int) get_option( 'olobuild_active_footer', 0 ),
            'active404Id'    => (int) get_option( 'olobuild_active_404', 0 ),
            'activeSingles'  => $this->get_active_singles_map(),
            'stockmedia'     => wp_parse_args(
                get_option( 'olobuild_stockmedia_behavior', [] ) ?: [],
                [ 'preferred' => 'unsplash', 'download_local' => true, 'optimize_on_download' => false ]
            ),
            'templateList'       => $this->get_template_list(),
            'megapanelTemplates' => $this->get_megapanel_templates(),
            'widgetTemplates'    => $this->get_widget_templates(),
            'postTypes'      => $this->get_public_post_types(),
            'taxonomies'     => $this->get_public_taxonomies(),
            'metaPrefixes'   => $this->get_meta_prefixes(),
            'metaKeys'       => $this->get_meta_keys_map(),
            'serviceList'    => $this->get_service_list(),
            'wpPages'        => $this->get_wp_pages(),
            'singlePostItems' => $this->get_single_post_items(),
            'iframeEmptyHtml' => self::get_iframe_empty_html(),
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per debug bootstrap builder admin; nessuna modifica di stato; valore sanitizzato via absint.
            '_debug_tpl_id'   => absint( wp_unslash( $_GET['template_id'] ?? 0 ) ),
            'hasAiKey'       => ! empty( get_option( 'olobuild_ai_anthropic_key', '' ) ),
            'breakpointsEnabled' => wp_parse_args( get_option( 'olobuild_breakpoints_enabled', [] ), [
                'widescreen'       => true,
                'tablet_landscape' => false,
                'tablet'           => true,
                'mobile_landscape' => false,
                'mobile'           => true,
            ] ),
            'userRestrictions' => Olobuild_Role_Manager::instance()->get_current_user_restrictions(),
            'isContentOnly'    => Olobuild_Role_Manager::instance()->is_content_only(),
            'isDesignOnly'     => Olobuild_Role_Manager::instance()->is_design_only(),
            'locale'         => olobuild_current_locale(),
            'translations'   => olobuild_get_translations_map(),
            'siteInfo'       => [
                'name'     => get_bloginfo( 'name' ),
                'tagline'  => get_bloginfo( 'description' ),
                'logo_url' => $this->get_site_logo_url(),
                'home_url' => home_url( '/' ),
            ],
        ] );

        // Filtro per plugin esterni che vogliono aggiungere dati a oloData
        $olo_data = apply_filters( 'olobuild_builder_localize_data', [] );
        if ( ! empty( $olo_data ) ) {
            wp_localize_script( 'olobuilder-js', 'oloExternalData', $olo_data );
        }
    }

    public function render_builder_page() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per routing della pagina admin (editor vs lista template); nessuna modifica di stato; valore sanitizzato via absint.
        $template_id = absint( wp_unslash( $_GET['template_id'] ?? 0 ) );

        // Forza il browser a NON usare la cache locale per la pagina builder.
        // Senza questi header alcuni browser (Chrome con bfcache) riservano
        // l'HTML completo della pagina builder — inclusi i tag <script src="builder.js?ver=X">
        // — e ricaricarla con F5 normale serve la versione vecchia.
        if ( ! headers_sent() ) {
            header( 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0' );
            header( 'Pragma: no-cache' );
            header( 'Expires: 0' );
        }

        if ( $template_id > 0 ) {
            // Fullscreen editor mode — keep existing behaviour
            include OLOBUILD_PATH . 'templates/builder-page.php';
        } else {
            // Template list mode — usa il cockpit shell della dashboard
            // (topbar OLObuild + appback strip + WP sidebar collassata in app mode)
            self::cockpit_shell_open( '<a href="' . esc_url( admin_url( 'admin.php?page=olobuilder-templates' ) ) . '" style="color:inherit;text-decoration:none">' . esc_html__( 'Template', 'olobuild' ) . '</a> · <b>' . esc_html__( 'Salvati', 'olobuild' ) . '</b>' );
            echo '<main class="olo-cockpit-main"><div id="olobuilder-app"></div></main>';
            self::cockpit_shell_close();
        }
    }

    /**
     * Add body class on Olobuild admin pages for shell layout.
     */
    /**
     * Lista degli screen IDs che usano lo shell cockpit (topbar OLObuild + appback +
     * sidebar WP collassata in app mode + body full-width).
     *
     * Single source of truth: usata da admin_body_class() e admin_enqueue_scripts()
     * per applicare CSS + body class in modo consistente.
     */
    public static function cockpit_screen_ids() {
        return [
            'toplevel_page_olobuild',
            'olobuild_page_olobuilder-templates',
            'olobuild_page_olobuilder-settings',     // Configurazione (Vue app)
            'olobuild_page_olo-media-search',
            'olobuild_page_olo-form-submissions',
            'olobuild_page_olo-newsletter',
            'olobuild_page_olo-analytics',
            'olobuild_page_olo-cookie-consent',
            'olobuild_page_olo-role-manager',
            'olobuild_page_olo-seo',
            'olobuild_page_olo-redirects',
            'olobuild_page_olo-performance',
            'olobuild_page_olo-tools',
            'olobuild_page_olo-global-popups',
            'olobuild_page_olo-white-label',
            'olobuild_page_olo-import-export',
            'olobuild_page_olo-woo-templates',
        ];
    }

    /**
     * Le pagine Olobuild che NON hanno 'olobuild' nel proprio screen id.
     *
     * Sono quelle appese a un menu di WordPress invece che al nostro: la
     * diagnostica sta sotto Strumenti, il wizard sotto Impostazioni, e i loro
     * screen id diventano 'tools_page_olo-diagnostics' e
     * 'settings_page_olo-setup'. Tutto il resto passa da add_submenu_page con
     * parent 'olobuild' e si riconosce dal prefisso.
     */
    public static function extra_olo_screen_ids() {
        return apply_filters( 'olobuild_extra_screen_ids', [
            'tools_page_olo-diagnostics',
            'settings_page_olo-setup',
        ] );
    }

    public function admin_body_class( $classes ) {
        $screen = get_current_screen();

        /*
         * Il riconoscimento e' per PREFISSO, non per sottostringa 'olo-'.
         *
         * Cercare 'olo-' dentro lo screen id faceva passare per pagina Olobuild
         * qualunque schermata di qualunque altro prodotto della famiglia: gli
         * screen di OLOtutor si chiamano 'olo_course_page_olo-tutor-*', quindi
         * si prendevano addosso 'olo-admin-shell' e 'olobuild-app-mode' — cioe'
         * la sidebar di wp-admin stretta a 52px e il sottomenu nascosto, dentro
         * un plugin che con Olobuild non c'entra. Misurato il 9 agosto 2026: il
         * menu di wp-admin restava contratto sopra i contenuti di OLOtutor e
         * non si riapriva piu'.
         */
        $is_olo_page = $screen && (
            str_contains( $screen->id, 'olobuild' )
            || in_array( $screen->id, self::extra_olo_screen_ids(), true )
        );

        if ( $is_olo_page ) {
            $classes .= ' olo-admin-shell';
        }

        // Pagine top-level che usano lo shell cockpit (full-width, no padding WP)
        $is_cockpit = $screen && in_array( $screen->id, self::cockpit_screen_ids(), true );

        if ( $is_cockpit ) {
            $classes .= ' olobuild-cockpit';
            // Mantieni la vecchia classe per la dashboard per compatibilità
            if ( $screen->id === 'toplevel_page_olobuild' ) {
                $classes .= ' olobuild-dashboard';
            }
        }

        /*
         * App mode: la sidebar di wp-admin stretta a 52 pixel e il sotto-menu
         * Olobuild nascosto, su tutte le pagine del prodotto.
         *
         * ⚠️ SPENTO DI SERIE, e prima era acceso. Contrarre il menu di
         * WordPress e' una cosa che WordPress sa gia' fare, con il suo
         * «Riduci menu», e chi lo vuole se lo tiene ovunque. Deciderlo noi al
         * posto suo vuol dire che aprendo Olobuild il menu si stringe da solo,
         * e chi non l'ha chiesto non ha modo di capire perche'.
         *
         * ⚠️ E il modo per spegnerlo era il NUMERO DI VERSIONE nella barra in
         * cima (`data-olo-app-mode-toggle` su `v1.4.379`): un comando che
         * nessuno cerca li'. Peggio, funzionava solo nella dashboard, perche'
         * `dashboard.js` si carica solo su `toplevel_page_olobuild`: su tutte
         * le altre pagine quel pulsante c'era, aveva il suo suggerimento
         * «Cambia modalita'», e non faceva niente.
         *
         * Chi la vuole se la accende dalla dashboard, e da li' resta accesa.
         */
        if ( $is_olo_page ) {
            $user_id = get_current_user_id();
            $prefs = get_user_meta( $user_id, 'olo_dashboard_prefs', true );
            $app_mode = is_array( $prefs ) && array_key_exists( 'app_mode', $prefs )
                ? (bool) $prefs['app_mode']
                : false; // default SPENTO
            if ( $app_mode ) {
                $classes .= ' olobuild-app-mode';
            }
        }

        return $classes;
    }





}
