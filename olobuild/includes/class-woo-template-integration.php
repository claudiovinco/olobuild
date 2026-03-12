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
        'product_single'  => [ 'label' => 'Singolo Prodotto',   'condition' => 'is_product' ],
        'product_archive' => [ 'label' => 'Archivio Prodotti',  'condition' => 'is_shop' ],
        'cart'            => [ 'label' => 'Carrello',           'condition' => 'is_cart' ],
        'checkout'        => [ 'label' => 'Checkout',           'condition' => 'is_checkout' ],
        'myaccount'       => [ 'label' => 'My Account',         'condition' => 'is_account_page' ],
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

        // Product category / tag archives
        if ( is_product_category() || is_product_tag() ) {
            $tpl = (int) get_option( 'olo_woo_tpl_product_archive', 0 );
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
        add_submenu_page(
            'olobuilder',
            'WooCommerce Templates',
            'WooCommerce',
            'manage_options',
            'olo-woo-templates',
            [ $this, 'render_admin_page' ]
        );
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

        ?>
        <div class="wrap">
            <h1>Olobuild — WooCommerce Templates</h1>
            <p>Assegna un template Olobuild a ciascuna pagina WooCommerce. Imposta a 0 o vuoto per usare il template predefinito di WooCommerce.</p>

            <table class="form-table" id="olo-woo-tpl-table">
                <tbody>
                    <?php foreach ( $this->page_types as $key => $info ) :
                        $current = (int) get_option( "olo_woo_tpl_{$key}", 0 );
                    ?>
                    <tr>
                        <th scope="row"><?php echo esc_html( $info['label'] ); ?></th>
                        <td>
                            <select name="olo_woo_tpl_<?php echo esc_attr( $key ); ?>" class="olo-woo-tpl-select" data-key="<?php echo esc_attr( $key ); ?>">
                                <option value="0">— Predefinito WooCommerce —</option>
                                <?php foreach ( $templates as $tpl ) : ?>
                                <option value="<?php echo $tpl['id']; ?>"<?php selected( $current, $tpl['id'] ); ?>>
                                    <?php echo esc_html( $tpl['name'] ); ?> (ID: <?php echo $tpl['id']; ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p>
                <button type="button" id="olo-woo-tpl-save" class="button button-primary">Salva assegnazioni</button>
                <span id="olo-woo-tpl-msg" style="margin-left:10px;color:#059669;display:none">Salvato!</span>
            </p>
        </div>

        <script>
        (function(){
            var btn = document.getElementById('olo-woo-tpl-save');
            var msg = document.getElementById('olo-woo-tpl-msg');
            if (!btn) return;
            btn.addEventListener('click', function(){
                var data = {};
                var selects = document.querySelectorAll('.olo-woo-tpl-select');
                selects.forEach(function(sel){
                    data[sel.getAttribute('data-key')] = parseInt(sel.value) || 0;
                });
                fetch('<?php echo esc_url( rest_url( 'olo/v1/woo-templates' ) ); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': '<?php echo wp_create_nonce( 'wp_rest' ); ?>'
                    },
                    body: JSON.stringify(data)
                }).then(function(r){ return r.json(); }).then(function(){
                    msg.style.display = 'inline';
                    setTimeout(function(){ msg.style.display = 'none'; }, 2000);
                });
            });
        })();
        </script>
        <?php
    }
}
