<?php
/**
 * WooCommerce Theme Builder Integration.
 *
 * Permette di assegnare template Olobuild a pagine WooCommerce:
 * - Product Single
 * - Product Archive (Shop)
 * - Cart
 * - Checkout
 * - My Account
 *
 * Options:
 *   olo_woo_tpl_product_single  → template ID per singolo prodotto
 *   olo_woo_tpl_product_archive → template ID per archivio prodotti / shop
 *   olo_woo_tpl_cart            → template ID per pagina carrello
 *   olo_woo_tpl_checkout        → template ID per pagina checkout
 *   olo_woo_tpl_myaccount       → template ID per pagina My Account
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class Olo_Woo_Template_Integration {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * WooCommerce page types that can have Olobuild templates.
     */
    private $page_types = [
        'product_single'   => [ 'label' => 'Singolo Prodotto',   'condition' => 'is_product' ],
        'product_archive'  => [ 'label' => 'Archivio Prodotti',  'condition' => 'is_shop' ],
        'product_category' => [ 'label' => 'Categoria Prodotto', 'condition' => 'is_product_category' ],
        'cart'             => [ 'label' => 'Carrello',           'condition' => 'is_cart' ],
        'checkout'         => [ 'label' => 'Checkout',           'condition' => 'is_checkout' ],
        'myaccount'        => [ 'label' => 'My Account',         'condition' => 'is_account_page' ],
    ];

    public function init() {
        if ( ! class_exists( 'WooCommerce' ) ) return;

        // Override WooCommerce page content with Olobuild template
        add_filter( 'the_content', [ $this, 'maybe_override_content' ], 5 );

        // Override product single template
        add_filter( 'template_include', [ $this, 'maybe_override_template' ], 99 );

        // REST API endpoints for managing WooCommerce templates
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );

        // Admin page under Olobuild menu
        add_action( 'admin_menu', [ $this, 'add_admin_page' ], 30 );
    }

    /**
     * Override WooCommerce page content with Olobuild template.
     * For Cart, Checkout, My Account (non-archive pages).
     */
    public function maybe_override_content( $content ) {
        if ( is_admin() ) return $content;
        if ( ! is_main_query() ) return $content;

        // Cart page
        if ( function_exists( 'is_cart' ) && is_cart() ) {
            $tpl = (int) get_option( 'olo_woo_tpl_cart', 0 );
            if ( $tpl > 0 ) return do_shortcode( '[olo_template id="' . $tpl . '"]' );
        }

        // Checkout page
        if ( function_exists( 'is_checkout' ) && is_checkout() ) {
            if ( ! is_order_received_page() ) {
                $tpl = (int) get_option( 'olo_woo_tpl_checkout', 0 );
                if ( $tpl > 0 ) return do_shortcode( '[olo_template id="' . $tpl . '"]' );
            }
        }

        // My Account page
        if ( function_exists( 'is_account_page' ) && is_account_page() ) {
            $tpl = (int) get_option( 'olo_woo_tpl_myaccount', 0 );
            if ( $tpl > 0 ) return do_shortcode( '[olo_template id="' . $tpl . '"]' );
        }

        return $content;
    }

    /**
     * Override WooCommerce template file for product single and archive.
     */
    public function maybe_override_template( $template ) {
        // Product single
        if ( function_exists( 'is_product' ) && is_product() ) {
            $tpl = (int) get_option( 'olo_woo_tpl_product_single', 0 );
            if ( $tpl > 0 ) {
                // Use a minimal template that renders the Olobuild template
                return $this->get_olo_wrapper_template( $tpl );
            }
        }

        // Product archive / shop
        if ( function_exists( 'is_shop' ) && is_shop() ) {
            $tpl = (int) get_option( 'olo_woo_tpl_product_archive', 0 );
            if ( $tpl > 0 ) {
                return $this->get_olo_wrapper_template( $tpl );
            }
        }

        // Product category / tag archives — option dedicata, fallback storico sull'archivio
        if ( is_product_category() || is_product_tag() ) {
            $tpl = (int) get_option( 'olo_woo_tpl_product_category', 0 );
            if ( ! $tpl ) {
                $tpl = (int) get_option( 'olo_woo_tpl_product_archive', 0 );
            }
            if ( $tpl > 0 ) {
                return $this->get_olo_wrapper_template( $tpl );
            }
        }

        return $template;
    }

    /**
     * Generate a minimal PHP template file that renders an Olobuild template.
     */
    private function get_olo_wrapper_template( $template_id ) {
        $wrapper = OLO_PATH . 'templates/woo-olo-wrapper.php';
        if ( ! file_exists( $wrapper ) ) {
            // Create the wrapper template on first use
            $code = '<?php' . "\n"
                  . '// Olobuild WooCommerce wrapper template' . "\n"
                  . 'if ( ! defined( \'ABSPATH\' ) ) exit;' . "\n"
                  . 'get_header();' . "\n"
                  . '$tpl_id = get_query_var( \'olo_woo_tpl_id\', 0 );' . "\n"
                  . 'if ( $tpl_id ) {' . "\n"
                  . '    echo do_shortcode( \'[olo_template id="\' . intval( $tpl_id ) . \'"]\' );' . "\n"
                  . '}' . "\n"
                  . 'get_footer();' . "\n";
            @file_put_contents( $wrapper, $code );
        }

        // Pass template ID via query var
        set_query_var( 'olo_woo_tpl_id', $template_id );

        return $wrapper;
    }

    /**
     * REST API routes for WooCommerce template assignments.
     */
    public function register_routes() {
        register_rest_route( 'olo/v1', '/woo-templates', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_woo_templates' ],
            'permission_callback' => function () { return current_user_can( 'manage_options' ); },
        ] );

        register_rest_route( 'olo/v1', '/woo-templates', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'save_woo_templates' ],
            'permission_callback' => function () { return current_user_can( 'manage_options' ); },
        ] );
    }

    public function get_woo_templates() {
        $data = [];
        foreach ( $this->page_types as $key => $info ) {
            $data[ $key ] = [
                'label'       => $info['label'],
                'template_id' => (int) get_option( "olo_woo_tpl_{$key}", 0 ),
            ];
        }
        return rest_ensure_response( $data );
    }

    public function save_woo_templates( $request ) {
        $body = $request->get_json_params();
        foreach ( $this->page_types as $key => $info ) {
            if ( isset( $body[ $key ] ) ) {
                $tpl_id = absint( $body[ $key ] );
                if ( $tpl_id > 0 ) {
                    update_option( "olo_woo_tpl_{$key}", $tpl_id, false );
                } else {
                    delete_option( "olo_woo_tpl_{$key}" );
                }
            }
        }
        return rest_ensure_response( [ 'success' => true ] );
    }

    /**
     * Admin page for assigning WooCommerce templates.
     */
    public function add_admin_page() {
        // v1.0.31 — pagina migrata in ?page=olobuilder-settings&tab=wootemplates
        // La classe resta attiva per il hook woocommerce_locate_template che sostituisce i template Woo.
    }

    public function render_admin_page() {
        // Get all Olobuild templates for dropdown
        global $wpdb;
        $table = $wpdb->prefix . 'olo_templates';
        $templates = [];
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) === $table ) {
            $rows = $wpdb->get_results( "SELECT id, title FROM {$table} WHERE status = 'published' ORDER BY title ASC" );
            foreach ( $rows as $row ) {
                $templates[] = [ 'id' => (int) $row->id, 'name' => $row->title ];
            }
        }

        $icons = [
            'product_single'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
            'product_archive' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>',
            'cart'            => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>',
            'checkout'        => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>',
            'myaccount'       => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
        ];

        // Conta override attivi (template Olobuild assegnati alle pagine WC)
        $woo_overrides = 0;
        foreach ( $this->page_types as $key => $info ) {
            if ( (int) get_option( "olo_woo_tpl_{$key}", 0 ) > 0 ) $woo_overrides++;
        }
        ?>
        <?php Olo_Builder::cockpit_shell_open( '<b>' . esc_html__( 'WooCommerce', 'olobuild' ) . '</b>' ); ?>
        <main class="olo-cockpit-main olo-cockpit-legacy">
            <?php
            // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML generated by Olo_Builder::cockpit_page_head(), which escapes title/sub internally (esc_html/wp_kses_post); the dynamic counts injected here are (int)-cast.
            echo Olo_Builder::cockpit_page_head( [
                'title' => __( 'Template WooCommerce', 'olobuild' ),
                'sub'   => sprintf(
                    /* translators: 1: overrides count, 2: total page types */
                    __( 'Override Olobuild attivi su %1$s/%2$s pagine WooCommerce (prodotto, archivio, carrello, checkout, mio account).', 'olobuild' ),
                    '<b>' . (int) $woo_overrides . '</b>',
                    (int) count( $this->page_types )
                ),
            ] );
            // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
            ?>

            <div id="woo-msg-box" style="margin-top:16px"></div>

            <div class="olo-card">
                <div class="olo-card-head">
                    <div class="olo-card-icon orange">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                    </div>
                    <div>
                        <h3>Assegnazione Template</h3>
                        <p>Seleziona un template Olobuild per ciascuna pagina WooCommerce. Lascia "Predefinito" per usare il template nativo.</p>
                    </div>
                </div>
                <div class="olo-card-body">
                    <?php foreach ( $this->page_types as $key => $info ) :
                        $current = (int) get_option( "olo_woo_tpl_{$key}", 0 );
                        $icon_class = ( $key === 'cart' || $key === 'checkout' ) ? 'orange' : 'black';
                    ?>
                    <div class="olo-field-row">
                        <div class="olo-field-info" style="display:flex;align-items:center;gap:10px">
                            <span style="width:32px;height:32px;border-radius:8px;background:#f5f5f5;display:flex;align-items:center;justify-content:center;color:#666;flex-shrink:0"><?php echo $icons[ $key ] ?? ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup from the hardcoded $icons map above ?></span>
                            <label><?php echo esc_html( $info['label'] ); ?></label>
                        </div>
                        <div class="olo-field-input-wrap">
                            <select class="olo-field-input olo-woo-tpl-select" data-key="<?php echo esc_attr( $key ); ?>">
                                <option value="0">— Predefinito WooCommerce —</option>
                                <?php foreach ( $templates as $tpl ) : ?>
                                <option value="<?php echo (int) $tpl['id']; ?>"<?php selected( $current, $tpl['id'] ); ?>>
                                    <?php echo esc_html( $tpl['name'] ); ?> (ID: <?php echo (int) $tpl['id']; ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="olo-actions">
                <button type="button" id="olo-woo-tpl-save" class="olo-btn-save">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Salva assegnazioni
                </button>
            </div>
        </main>
        <?php Olo_Builder::cockpit_shell_close(); ?>

        <script>
        (function(){
            var btn = document.getElementById('olo-woo-tpl-save');
            if (!btn) return;
            btn.addEventListener('click', function(){
                btn.disabled = true;
                btn.innerHTML = '<span class="olo-spinner"></span> Salvataggio...';
                var data = {};
                document.querySelectorAll('.olo-woo-tpl-select').forEach(function(sel){
                    data[sel.getAttribute('data-key')] = parseInt(sel.value) || 0;
                });
                fetch('<?php echo esc_url( rest_url( 'olo/v1/woo-templates' ) ); ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': '<?php echo esc_js( wp_create_nonce( 'wp_rest' ) ); ?>' },
                    body: JSON.stringify(data)
                }).then(function(r){ return r.json(); }).then(function(){
                    var box = document.getElementById('woo-msg-box');
                    box.className = 'olo-msg success';
                    box.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg> Assegnazioni salvate';
                    setTimeout(function(){ box.className = ''; box.innerHTML = ''; }, 3000);
                }).finally(function(){
                    btn.disabled = false;
                    btn.innerHTML = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Salva assegnazioni';
                });
            });
        })();
        </script>
        <?php
    }
}
