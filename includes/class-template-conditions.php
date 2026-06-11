<?php
/**
 * Olo_Template_Conditions — Advanced display conditions for templates.
 *
 * Allows multiple conditions with AND/OR logic per template assignment.
 * Extends the simple "one template per post type" system.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Template_Conditions {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init() {
        // REST API for managing template conditions
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );

        // Override single template selection with conditions
        add_filter( 'olo_resolve_template_id', [ $this, 'resolve_by_conditions' ], 10, 2 );

        // Admin UI: pagina dedicata sotto Olobuild
        add_action( 'admin_menu', [ $this, 'register_admin_page' ], 30 );
        add_action( 'admin_post_olo_save_template_conditions', [ $this, 'handle_admin_save' ] );
    }

    /* ─────────────────────────────────────────────
     * Template Resolution with Conditions
     * ───────────────────────────────────────────── */

    /**
     * Resolve template ID based on advanced conditions.
     * Falls back to simple post_type option if no conditions match.
     *
     * @param int    $template_id Current template ID (from simple system)
     * @param string $context     'single', 'archive', 'header', 'footer'
     * @return int Template ID
     */
    public function resolve_by_conditions( $template_id, $context = 'single' ) {
        $assignments = get_option( 'olo_template_conditions', [] );
        if ( empty( $assignments ) || ! is_array( $assignments ) ) {
            return $template_id;
        }

        // Filter assignments by context type
        $relevant = array_filter( $assignments, function( $a ) use ( $context ) {
            return ( $a['context'] ?? '' ) === $context && ! empty( $a['enabled'] );
        } );

        // Sort by priority (lower = higher priority)
        usort( $relevant, function( $a, $b ) {
            return ( $a['priority'] ?? 100 ) - ( $b['priority'] ?? 100 );
        } );

        foreach ( $relevant as $assignment ) {
            $tpl_id = intval( $assignment['template_id'] ?? 0 );
            if ( ! $tpl_id ) {
                continue;
            }

            $conditions = $assignment['conditions'] ?? [];
            $logic      = $assignment['conditions_logic'] ?? 'AND';

            if ( $this->evaluate_conditions( $conditions, $logic ) ) {
                return $tpl_id;
            }
        }

        return $template_id;
    }

    /**
     * Evaluate a set of conditions with AND/OR logic.
     */
    private function evaluate_conditions( $conditions, $logic = 'AND' ) {
        if ( empty( $conditions ) ) {
            return true;
        }

        $results = [];
        foreach ( $conditions as $cond ) {
            $results[] = $this->evaluate_single( $cond );
        }

        if ( $logic === 'OR' ) {
            return in_array( true, $results, true );
        }

        // AND: all must be true
        return ! in_array( false, $results, true );
    }

    private function evaluate_single( $cond ) {
        $type   = $cond['type'] ?? '';
        $value  = $cond['value'] ?? '';
        $negate = ! empty( $cond['negate'] );

        $result = false;

        switch ( $type ) {
            case 'entire_site':
                $result = true;
                break;

            case 'front_page':
                $result = is_front_page();
                break;

            case 'singular':
                $result = is_singular();
                break;

            case 'page':
                $result = empty( $value ) || $value === 'all' ? is_page() : is_page( intval( $value ) );
                break;

            case 'post':
                $result = empty( $value ) || $value === 'all'
                    ? is_singular( 'post' )
                    : ( is_singular( 'post' ) && get_the_ID() === intval( $value ) );
                break;

            case 'post_type':
                $result = is_singular( sanitize_text_field( $value ) );
                break;

            case 'archive':
                $result = empty( $value ) || $value === 'all'
                    ? is_archive()
                    : is_post_type_archive( sanitize_text_field( $value ) );
                break;

            case 'category':
                if ( is_singular() ) {
                    $result = has_category( $value ? intval( $value ) : null );
                } else {
                    $result = is_category( $value ? intval( $value ) : null );
                }
                break;

            case 'tag':
                if ( is_singular() ) {
                    $result = has_tag( sanitize_text_field( $value ) );
                } else {
                    $result = is_tag( sanitize_text_field( $value ) );
                }
                break;

            case 'taxonomy':
                $parts = explode( ':', $value );
                if ( count( $parts ) >= 2 ) {
                    $result = has_term( $parts[1], $parts[0] );
                } elseif ( count( $parts ) === 1 ) {
                    $result = is_tax( $parts[0] );
                }
                break;

            case 'author':
                $result = is_singular() && (int) get_the_author_meta( 'ID' ) === (int) $value;
                break;

            case 'user_logged_in':
                $result = is_user_logged_in();
                break;

            case 'user_logged_out':
                $result = ! is_user_logged_in();
                break;

            case 'user_role':
                if ( is_user_logged_in() ) {
                    $user   = wp_get_current_user();
                    $result = in_array( sanitize_text_field( $value ), $user->roles, true );
                }
                break;

            case 'has_template':
                // Post has a specific Olobuild template assigned
                $result = is_singular() && get_post_meta( get_the_ID(), '_olo_template_id', true ) == intval( $value );
                break;

            case 'post_format':
                $result = is_singular() && has_post_format( sanitize_text_field( $value ) );
                break;

            case '404':
                $result = is_404();
                break;

            case 'search':
                $result = is_search();
                break;

            case 'date_before':
                $result = time() < strtotime( $value );
                break;

            case 'date_after':
                $result = time() > strtotime( $value );
                break;

            // WooCommerce
            case 'woo_shop':
                $result = function_exists( 'is_shop' ) && is_shop();
                break;
            case 'woo_product':
                $result = function_exists( 'is_product' ) && is_product();
                break;
            case 'woo_product_cat':
                if ( function_exists( 'is_product' ) && is_product() ) {
                    $result = has_term( sanitize_text_field( $value ), 'product_cat' );
                }
                break;
            case 'woo_cart':
                $result = function_exists( 'is_cart' ) && is_cart();
                break;
            case 'woo_checkout':
                $result = function_exists( 'is_checkout' ) && is_checkout();
                break;
            case 'woo_account':
                $result = function_exists( 'is_account_page' ) && is_account_page();
                break;
        }

        return $negate ? ! $result : $result;
    }

    /* ─────────────────────────────────────────────
     * REST API
     * ───────────────────────────────────────────── */

    public function register_routes() {
        register_rest_route( 'olo/v1', '/template-conditions', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_conditions' ],
                'permission_callback' => function () {
                    return current_user_can( 'edit_posts' );
                },
            ],
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'save_conditions' ],
                'permission_callback' => function () {
                    return current_user_can( 'edit_posts' );
                },
            ],
        ] );
    }

    public function get_conditions( $request ) {
        return rest_ensure_response( get_option( 'olo_template_conditions', [] ) );
    }

    public function save_conditions( $request ) {
        $data = $request->get_json_params();
        if ( ! is_array( $data ) ) {
            return new WP_Error( 'invalid', 'Dati non validi', [ 'status' => 400 ] );
        }

        $clean = [];
        foreach ( $data as $item ) {
            $clean[] = [
                'enabled'          => ! empty( $item['enabled'] ),
                'name'             => sanitize_text_field( $item['name'] ?? '' ),
                'template_id'      => intval( $item['template_id'] ?? 0 ),
                'context'          => sanitize_text_field( $item['context'] ?? 'single' ),
                'priority'         => max( 1, intval( $item['priority'] ?? 10 ) ),
                'conditions'       => $this->sanitize_conditions_arr( $item['conditions'] ?? [] ),
                'conditions_logic' => in_array( $item['conditions_logic'] ?? 'AND', [ 'AND', 'OR' ], true ) ? $item['conditions_logic'] : 'AND',
            ];
        }

        update_option( 'olo_template_conditions', $clean, false );
        return rest_ensure_response( [ 'success' => true ] );
    }

    private function sanitize_conditions_arr( $conditions ) {
        if ( ! is_array( $conditions ) ) {
            return [];
        }
        $clean = [];
        foreach ( $conditions as $c ) {
            $clean[] = [
                'type'   => sanitize_text_field( $c['type'] ?? '' ),
                'value'  => sanitize_text_field( $c['value'] ?? '' ),
                'negate' => ! empty( $c['negate'] ),
            ];
        }
        return $clean;
    }

    /* ─────────────────────────────────────────────
     * Admin UI
     * ───────────────────────────────────────────── */

    public function register_admin_page() {
        add_submenu_page(
            'admin.php?page=olobuild',
            __( 'Regole di visualizzazione', 'olobuild' ),
            __( 'Regole di visualizzazione', 'olobuild' ),
            'edit_others_posts',
            'olobuilder-template-rules',
            [ $this, 'render_admin_page' ]
        );
    }

    public function render_admin_page() {
        if ( ! current_user_can( 'edit_others_posts' ) ) {
            wp_die( esc_html__( 'Accesso negato.', 'olobuild' ) );
        }

        $assignments = get_option( 'olo_template_conditions', [] );
        if ( ! is_array( $assignments ) ) $assignments = [];

        // Carica template per dropdown
        $db = new Olo_Database();
        $all = $db->list_templates( [ 'per_page' => 200, 'orderby' => 'title', 'order' => 'ASC' ] )['items'] ?? [];
        $headers = array_values( array_filter( $all, function ( $t ) { return ( $t['type'] ?? '' ) === 'header' && $t['status'] === 'published'; } ) );
        $footers = array_values( array_filter( $all, function ( $t ) { return ( $t['type'] ?? '' ) === 'footer' && $t['status'] === 'published'; } ) );

        $public_cpts = get_post_types( [ 'public' => true ], 'objects' );

        $saved   = isset( $_GET['olo_saved'] );
        ?>
        <div class="wrap olo-tpl-rules">
            <h1><?php esc_html_e( 'Regole di visualizzazione template', 'olobuild' ); ?></h1>
            <p class="description">
                <?php esc_html_e( 'Definisci dove ogni template Header/Footer si applica. Le regole hanno priorità sull\'header/footer globale e cedono il passo a un\'eventuale assegnazione per-pagina dal metabox Olobuild.', 'olobuild' ); ?>
            </p>

            <?php if ( $saved ) : ?>
                <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Regole salvate.', 'olobuild' ); ?></p></div>
            <?php endif; ?>

            <?php if ( empty( $headers ) && empty( $footers ) ) : ?>
                <p><em><?php esc_html_e( 'Nessun template Header/Footer pubblicato. Crea prima un template e impostalo come "Pubblicato".', 'olobuild' ); ?></em></p>
                <?php return; ?>
            <?php endif; ?>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="olo-tpl-rules-form">
                <input type="hidden" name="action" value="olo_save_template_conditions" />
                <?php wp_nonce_field( 'olo_save_template_conditions' ); ?>

                <table class="widefat striped" id="olo-tpl-rules-table">
                    <thead>
                        <tr>
                            <th style="width:36px;"></th>
                            <th style="width:18%;"><?php esc_html_e( 'Nome', 'olobuild' ); ?></th>
                            <th style="width:22%;"><?php esc_html_e( 'Template', 'olobuild' ); ?></th>
                            <th style="width:15%;"><?php esc_html_e( 'Area', 'olobuild' ); ?></th>
                            <th style="width:10%;"><?php esc_html_e( 'Priorità', 'olobuild' ); ?></th>
                            <th><?php esc_html_e( 'Condizione', 'olobuild' ); ?></th>
                            <th style="width:48px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $rows = empty( $assignments ) ? [ [] ] : $assignments;
                        foreach ( $rows as $i => $a ) :
                            $name        = $a['name']        ?? '';
                            $template_id = (int) ( $a['template_id'] ?? 0 );
                            $context     = $a['context']     ?? 'header';
                            $priority    = (int) ( $a['priority'] ?? 10 );
                            $enabled     = isset( $a['enabled'] ) ? ! empty( $a['enabled'] ) : true;
                            $cond_first  = $a['conditions'][0] ?? [ 'type' => '', 'value' => '', 'negate' => false ];
                            $ct          = $cond_first['type']  ?? '';
                            $cv          = $cond_first['value'] ?? '';
                            ?>
                            <tr class="olo-tpl-rule-row">
                                <td>
                                    <input type="checkbox" name="rules[<?php echo (int) $i; ?>][enabled]" value="1" <?php checked( $enabled ); ?> title="<?php esc_attr_e( 'Abilita', 'olobuild' ); ?>" />
                                </td>
                                <td>
                                    <input type="text" name="rules[<?php echo (int) $i; ?>][name]" value="<?php echo esc_attr( $name ); ?>" placeholder="<?php esc_attr_e( 'es. Header strutture', 'olobuild' ); ?>" style="width:100%;" />
                                </td>
                                <td>
                                    <select name="rules[<?php echo (int) $i; ?>][template_id]" class="olo-tpl-select" data-context="<?php echo esc_attr( $context ); ?>" style="width:100%;">
                                        <option value="0">— <?php esc_html_e( 'Seleziona template', 'olobuild' ); ?> —</option>
                                        <optgroup label="<?php esc_attr_e( 'Header', 'olobuild' ); ?>">
                                            <?php foreach ( $headers as $t ) : ?>
                                                <option value="<?php echo (int) $t['id']; ?>" data-type="header" <?php selected( $template_id, (int) $t['id'] ); ?>>#<?php echo (int) $t['id']; ?> — <?php echo esc_html( $t['title'] ); ?></option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                        <optgroup label="<?php esc_attr_e( 'Footer', 'olobuild' ); ?>">
                                            <?php foreach ( $footers as $t ) : ?>
                                                <option value="<?php echo (int) $t['id']; ?>" data-type="footer" <?php selected( $template_id, (int) $t['id'] ); ?>>#<?php echo (int) $t['id']; ?> — <?php echo esc_html( $t['title'] ); ?></option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    </select>
                                </td>
                                <td>
                                    <select name="rules[<?php echo (int) $i; ?>][context]" style="width:100%;">
                                        <option value="header" <?php selected( $context, 'header' ); ?>><?php esc_html_e( 'Header', 'olobuild' ); ?></option>
                                        <option value="footer" <?php selected( $context, 'footer' ); ?>><?php esc_html_e( 'Footer', 'olobuild' ); ?></option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="rules[<?php echo (int) $i; ?>][priority]" value="<?php echo (int) $priority; ?>" min="1" max="999" style="width:80px;" title="<?php esc_attr_e( 'Più basso = più importante', 'olobuild' ); ?>" />
                                </td>
                                <td class="olo-cond-cell">
                                    <select name="rules[<?php echo (int) $i; ?>][conditions][0][type]" class="olo-cond-type" style="min-width:170px;">
                                        <option value=""><?php esc_html_e( '— Seleziona —', 'olobuild' ); ?></option>
                                        <option value="entire_site" <?php selected( $ct, 'entire_site' ); ?>><?php esc_html_e( 'Tutto il sito', 'olobuild' ); ?></option>
                                        <option value="front_page" <?php selected( $ct, 'front_page' ); ?>><?php esc_html_e( 'Front page', 'olobuild' ); ?></option>
                                        <option value="post_type" <?php selected( $ct, 'post_type' ); ?>><?php esc_html_e( 'Singoli di un tipo (CPT)', 'olobuild' ); ?></option>
                                        <option value="archive" <?php selected( $ct, 'archive' ); ?>><?php esc_html_e( 'Archivio di un tipo (CPT)', 'olobuild' ); ?></option>
                                        <option value="page" <?php selected( $ct, 'page' ); ?>><?php esc_html_e( 'Una pagina specifica (ID)', 'olobuild' ); ?></option>
                                        <option value="post" <?php selected( $ct, 'post' ); ?>><?php esc_html_e( 'Un articolo specifico (ID)', 'olobuild' ); ?></option>
                                        <option value="search" <?php selected( $ct, 'search' ); ?>><?php esc_html_e( 'Risultati ricerca', 'olobuild' ); ?></option>
                                        <option value="404" <?php selected( $ct, '404' ); ?>><?php esc_html_e( 'Pagina 404', 'olobuild' ); ?></option>
                                        <option value="user_logged_in" <?php selected( $ct, 'user_logged_in' ); ?>><?php esc_html_e( 'Utenti loggati', 'olobuild' ); ?></option>
                                        <option value="user_logged_out" <?php selected( $ct, 'user_logged_out' ); ?>><?php esc_html_e( 'Utenti non loggati', 'olobuild' ); ?></option>
                                    </select>
                                    <?php $show_cpt = in_array( $ct, [ 'post_type', 'archive' ], true ); ?>
                                    <select name="rules[<?php echo (int) $i; ?>][conditions][0][value]" class="olo-cond-value-cpt" style="min-width:200px;<?php echo $show_cpt ? '' : 'display:none;'; ?>" <?php disabled( ! $show_cpt ); ?>>
                                        <option value=""><?php esc_html_e( '— Tipo —', 'olobuild' ); ?></option>
                                        <?php foreach ( $public_cpts as $slug => $obj ) : ?>
                                            <option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $cv, $slug ); ?>><?php echo esc_html( $obj->labels->singular_name . ' (' . $slug . ')' ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php $show_id = in_array( $ct, [ 'page', 'post' ], true ); ?>
                                    <input type="text" name="rules[<?php echo (int) $i; ?>][conditions][0][value]" class="olo-cond-value-id" value="<?php echo $show_id ? esc_attr( $cv ) : ''; ?>" placeholder="<?php esc_attr_e( 'ID', 'olobuild' ); ?>" style="width:80px;<?php echo $show_id ? '' : 'display:none;'; ?>" <?php disabled( ! $show_id ); ?> />
                                </td>
                                <td>
                                    <button type="button" class="button olo-rule-remove" title="<?php esc_attr_e( 'Rimuovi', 'olobuild' ); ?>">×</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <p style="margin-top:14px;">
                    <button type="button" class="button" id="olo-rule-add">+ <?php esc_html_e( 'Aggiungi regola', 'olobuild' ); ?></button>
                    <button type="submit" class="button button-primary" style="margin-left:8px;"><?php esc_html_e( 'Salva regole', 'olobuild' ); ?></button>
                </p>

                <h2 style="margin-top:30px;"><?php esc_html_e( 'Come funziona', 'olobuild' ); ?></h2>
                <ul style="list-style:disc;margin-left:18px;">
                    <li><strong><?php esc_html_e( 'Priorità più bassa = vince per prima', 'olobuild' ); ?></strong>. <?php esc_html_e( 'A parità, l\'ordine in tabella decide.', 'olobuild' ); ?></li>
                    <li><?php esc_html_e( 'Se un singolo post ha un header/footer assegnato dal metabox Olobuild, quello vince comunque sulle regole qui.', 'olobuild' ); ?></li>
                    <li><?php esc_html_e( 'Se nessuna regola matcha, viene usato l\'header/footer globale (Gestione Template → Attiva).', 'olobuild' ); ?></li>
                </ul>
            </form>
        </div>
        <script>
        (function () {
            function syncCondValue(row) {
                var sel = row.querySelector('.olo-cond-type');
                var type = sel ? sel.value : '';
                var cpt  = row.querySelector('.olo-cond-value-cpt');
                var idIn = row.querySelector('.olo-cond-value-id');
                var showCpt = (type === 'post_type' || type === 'archive');
                var showId  = (type === 'page' || type === 'post');
                // I due input hanno lo stesso `name` per condividere il valore.
                // Disabilitiamo quello non in uso così PHP riceve un solo
                // valore corrispondente al tipo selezionato (HTML disabled
                // exclude il campo dal form submission).
                if (cpt)  { cpt.style.display  = showCpt ? '' : 'none'; cpt.disabled  = !showCpt; }
                if (idIn) { idIn.style.display = showId  ? '' : 'none'; idIn.disabled = !showId; }
            }
            document.addEventListener('change', function (e) {
                if (e.target.classList.contains('olo-cond-type')) {
                    syncCondValue(e.target.closest('tr'));
                }
            });
            document.addEventListener('click', function (e) {
                if (e.target.id === 'olo-rule-add') {
                    var tbody = document.querySelector('#olo-tpl-rules-table tbody');
                    var row = tbody.querySelector('tr');
                    if (!row) return;
                    var clone = row.cloneNode(true);
                    var newIdx = tbody.children.length;
                    clone.querySelectorAll('input,select').forEach(function (el) {
                        el.name = el.name.replace(/rules\[\d+\]/, 'rules[' + newIdx + ']');
                        if (el.type === 'checkbox') el.checked = true;
                        else if (el.tagName === 'SELECT') el.selectedIndex = 0;
                        else el.value = '';
                    });
                    tbody.appendChild(clone);
                    syncCondValue(clone);
                }
                if (e.target.classList.contains('olo-rule-remove')) {
                    var tbody = e.target.closest('tbody');
                    var tr = e.target.closest('tr');
                    if (tbody && tbody.children.length > 1) {
                        tr.remove();
                    } else {
                        tr.querySelectorAll('input,select').forEach(function (el) {
                            if (el.type === 'checkbox') el.checked = false;
                            else if (el.tagName === 'SELECT') el.selectedIndex = 0;
                            else el.value = '';
                        });
                        syncCondValue(tr);
                    }
                }
            });
            // initial sync per ogni row
            document.querySelectorAll('#olo-tpl-rules-table tr.olo-tpl-rule-row').forEach(syncCondValue);
        })();
        </script>
        <?php
    }

    public function handle_admin_save() {
        if ( ! current_user_can( 'edit_others_posts' ) ) {
            wp_die( 'Forbidden' );
        }
        check_admin_referer( 'olo_save_template_conditions' );

        $raw   = $_POST['rules'] ?? [];
        if ( ! is_array( $raw ) ) $raw = [];

        $clean = [];
        $idx   = 0;
        foreach ( $raw as $r ) {
            if ( ! is_array( $r ) ) continue;
            $tid = (int) ( $r['template_id'] ?? 0 );
            if ( ! $tid ) continue; // skip incomplete

            $cond_in = $r['conditions'][0] ?? [];
            $ct = isset( $cond_in['type'] ) ? sanitize_text_field( $cond_in['type'] ) : '';
            $cv = isset( $cond_in['value'] ) ? sanitize_text_field( $cond_in['value'] ) : '';
            if ( $ct === '' ) continue; // condizione vuota → ignora

            $clean[] = [
                'enabled'          => ! empty( $r['enabled'] ),
                'name'             => sanitize_text_field( $r['name'] ?? '' ),
                'template_id'      => $tid,
                'context'          => in_array( $r['context'] ?? '', [ 'header', 'footer' ], true ) ? $r['context'] : 'header',
                'priority'         => max( 1, (int) ( $r['priority'] ?? 10 ) ),
                'conditions'       => [ [ 'type' => $ct, 'value' => $cv, 'negate' => false ] ],
                'conditions_logic' => 'AND',
            ];
            $idx++;
        }

        update_option( 'olo_template_conditions', $clean, false );

        wp_safe_redirect( add_query_arg( 'olo_saved', '1', admin_url( 'admin.php?page=olobuilder-template-rules' ) ) );
        exit;
    }
}
