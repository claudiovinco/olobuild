<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Page_Integration {

    public function init() {
        // Row action in pages list
        add_filter( 'page_row_actions', [ $this, 'add_row_action' ], 10, 2 );
        add_filter( 'post_row_actions', [ $this, 'add_row_action' ], 10, 2 );

        // Admin bar button
        add_action( 'admin_bar_menu', [ $this, 'add_admin_bar_button' ], 100 );

        // Meta box in page/post editor
        add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );

        // Save meta box selection
        add_action( 'save_post', [ $this, 'save_meta_box' ] );

        // Handle "create and edit" action
        add_action( 'admin_init', [ $this, 'handle_create_template' ] );

        // Auto-render Olobuild template on frontend if linked
        add_filter( 'the_content', [ $this, 'auto_render_template' ], 20 );

        // Sulle pagine con template Olobuild: niente wptexturize + ripara <script> (vedi metodo).
        // Priorità bassa: l'ob_start dev'essere il più esterno per catturare l'output finale.
        add_action( 'template_redirect', [ $this, 'maybe_disable_texturize' ], 0 );
    }

    /**
     * Protegge gli <script> runtime dei tile dalla texturizzazione di WordPress, sulle
     * pagine renderizzate da un template Olobuild.
     *
     * Il contenuto Olobuild è markup strutturato (HTML + <script>), non prosa. Gli <script>
     * dei tile "wow" sono grandi e densi di operatori JS (`<`, `>`, `&&`): su input grande
     * wptexturize/wp_html_split va in backtrack PCRE, perde i confini dello <script> e ne
     * processa il contenuto come testo, convertendo `&` → `&#038;` (quindi `&&` → SyntaxError).
     * Il runtime del tile non parte (canvas/particelle inerti nel frontend, pur funzionando
     * nel builder). In un block-theme (FSE) la texturizzazione avviene in più punti, non solo
     * su `the_content`: per questo, oltre a togliere il filtro principale, ripariamo le entità
     * `&#038;` rimaste SOLO dentro i blocchi <script> dell'output finale.
     */
    public function maybe_disable_texturize() {
        $id = get_queried_object_id();
        if ( ! $id || ! get_post_meta( $id, '_olo_template_id', true ) ) {
            return;
        }
        // 1) Niente wptexturize sul markup del template.
        remove_filter( 'the_content', 'wptexturize' );
        // 2) Rete di sicurezza: ripara `&#038;` (→ `&`) solo dentro gli <script> finali.
        ob_start( [ $this, 'repair_script_entities' ] );
    }

    /**
     * Callback ob_start: nell'HTML finale, ripristina `&` corrotto in `&#038;` ma SOLO
     * all'interno dei blocchi <script> (dove `&` è sempre letterale, mai un'entità). Il
     * testo/markup fuori dagli script resta invariato. Guardia anti-backtrack PCRE: se il
     * match fallisce, restituisce l'HTML intatto.
     */
    public function repair_script_entities( $html ) {
        if ( ! is_string( $html ) || strpos( $html, '&#038;' ) === false ) {
            return $html;
        }
        $out = preg_replace_callback(
            '#<script\b[^>]*>.*?</script>#is',
            function ( $m ) {
                return strpos( $m[0], '&#038;' ) !== false ? str_replace( '&#038;', '&', $m[0] ) : $m[0];
            },
            $html
        );
        return ( null === $out ) ? $html : $out;
    }

    /**
     * Get the builder URL for a post.
     * If a template is already linked, edit it. Otherwise, create one.
     *
     * Public static: usato anche dalla dashboard cockpit per i CTA.
     */
    public static function get_builder_url( $post_id ) {
        $template_id = get_post_meta( $post_id, '_olo_template_id', true );

        if ( $template_id ) {
            return admin_url( 'admin.php?page=olobuilder-templates&template_id=' . $template_id . '&post_id=' . $post_id );
        }

        // Check for CPT single template (olo_active_single_{post_type})
        $post_type = get_post_type( $post_id );
        if ( $post_type && $post_type !== 'page' && $post_type !== 'post' ) {
            $single_tpl = (int) get_option( "olo_active_single_{$post_type}", 0 );
            if ( $single_tpl ) {
                return admin_url( 'admin.php?page=olobuilder-templates&template_id=' . $single_tpl . '&post_id=' . $post_id );
            }
        }

        // No template linked yet → URL to create one
        return wp_nonce_url(
            admin_url( 'admin.php?page=olobuilder-templates&action=olo_create&post_id=' . $post_id ),
            'olo_create_' . $post_id
        );
    }

    /**
     * Add "Olobuild" link in the pages/posts list row actions.
     */
    public function add_row_action( $actions, $post ) {
        if ( ! current_user_can( 'edit_post', $post->ID ) ) {
            return $actions;
        }

        $url = self::get_builder_url( $post->ID );
        $template_id = get_post_meta( $post->ID, '_olo_template_id', true );
        $label = $template_id ? 'Olobuild' : 'Crea con Olobuild';

        $actions['olo_builder'] = sprintf(
            '<a href="%s" style="color: #6366f1; font-weight: 600;">%s</a>',
            esc_url( $url ),
            esc_html( $label )
        );

        return $actions;
    }

    /**
     * Add "Edit with Olobuild" button in the admin bar.
     *
     * Strategie di contesto:
     *  - frontend singular  → edit/crea template della pagina/post
     *  - frontend home/front → edit page_on_front (se static) altrimenti dashboard
     *  - frontend archive   → dashboard (TODO: link diretto al template archive del post_type)
     *  - frontend 404/search → edit template 404 / search results se attivi
     *  - admin post.php     → edit template della pagina/post in editing
     */
    public function add_admin_bar_button( $wp_admin_bar ) {
        if ( ! current_user_can( 'edit_posts' ) ) {
            return;
        }

        $node = null;

        if ( ! is_admin() ) {
            // ── Frontend ──
            if ( is_singular() ) {
                $post_id = get_queried_object_id();
                $template_id = get_post_meta( $post_id, '_olo_template_id', true );
                if ( ! $template_id ) {
                    $pt = get_post_type( $post_id );
                    if ( $pt && $pt !== 'page' && $pt !== 'post' ) {
                        $template_id = (int) get_option( "olo_active_single_{$pt}", 0 );
                    }
                }
                $node = [
                    'href'  => self::get_builder_url( $post_id ),
                    'label' => $template_id ? 'Modifica con Olobuild' : 'Crea con Olobuild',
                ];
            } elseif ( is_front_page() || is_home() ) {
                // Home: se è static page edita quella, altrimenti dashboard
                $front_id = (int) get_option( 'page_on_front', 0 );
                if ( $front_id && get_option( 'show_on_front' ) === 'page' ) {
                    $tpl = get_post_meta( $front_id, '_olo_template_id', true );
                    $node = [
                        'href'  => self::get_builder_url( $front_id ),
                        'label' => $tpl ? 'Modifica home con Olobuild' : 'Crea home con Olobuild',
                    ];
                } else {
                    $node = [
                        'href'  => admin_url( 'admin.php?page=olobuild' ),
                        'label' => 'Apri Olobuild',
                    ];
                }
            } elseif ( is_404() ) {
                $tpl_404 = (int) get_option( 'olo_active_404', 0 );
                $node = [
                    'href'  => $tpl_404
                        ? admin_url( 'admin.php?page=olobuilder-templates&template_id=' . $tpl_404 )
                        : admin_url( 'admin.php?page=olobuild' ),
                    'label' => $tpl_404 ? 'Modifica 404 con Olobuild' : 'Apri Olobuild',
                ];
            } elseif ( is_search() ) {
                $tpl_search = (int) get_option( 'olo_active_search', 0 );
                $node = [
                    'href'  => $tpl_search
                        ? admin_url( 'admin.php?page=olobuilder-templates&template_id=' . $tpl_search )
                        : admin_url( 'admin.php?page=olobuild' ),
                    'label' => $tpl_search ? 'Modifica ricerca con Olobuild' : 'Apri Olobuild',
                ];
            } elseif ( is_archive() ) {
                $node = [
                    'href'  => admin_url( 'admin.php?page=olobuild' ),
                    'label' => 'Apri Olobuild',
                ];
            }
        } else {
            // ── Admin: editor post.php ──
            global $pagenow, $post;
            if ( $pagenow === 'post.php' && $post ) {
                $template_id = get_post_meta( $post->ID, '_olo_template_id', true );
                $node = [
                    'href'  => self::get_builder_url( $post->ID ),
                    'label' => $template_id ? 'Modifica con Olobuild' : 'Crea con Olobuild',
                ];
            }
        }

        if ( $node ) {
            $wp_admin_bar->add_node( [
                'id'    => 'olobuilder-edit',
                'title' => '<span style="color: #a5b4fc;">&#9638;</span> ' . esc_html( $node['label'] ),
                'href'  => $node['href'],
                'meta'  => [ 'title' => 'Apri in Olobuild' ],
            ] );
        }
    }

    /**
     * Add meta box in page/post editor.
     *
     * Esposto su page, post e tutti i Custom Post Type pubblici (es. olo_service)
     * così che ogni singolo dynamic post possa avere header/footer/template
     * Olobuild personalizzati. Filtrabile via `olo_metabox_post_types` per togliere
     * eventuali CPT che non vuoi tracciare.
     */
    public function add_meta_box() {
        $public_cpts = get_post_types( [ 'public' => true, '_builtin' => false ], 'names' );
        $post_types  = array_unique( array_merge( [ 'page', 'post' ], array_values( $public_cpts ) ) );
        $post_types  = apply_filters( 'olo_metabox_post_types', $post_types );

        foreach ( $post_types as $post_type ) {
            add_meta_box(
                'olo_builder_metabox',
                'Olobuild',
                [ $this, 'render_meta_box' ],
                $post_type,
                'side',
                'high'
            );
        }
    }

    public function render_meta_box( $post ) {
        $template_id = (int) get_post_meta( $post->ID, '_olo_template_id', true );
        $header_id   = (int) get_post_meta( $post->ID, '_olo_header_id', true );
        $footer_id   = (int) get_post_meta( $post->ID, '_olo_footer_id', true );
        $url = self::get_builder_url( $post->ID );

        // Fetch all templates for the select
        $db = new Olo_Database();
        $result = $db->list_templates( [ 'per_page' => 100, 'orderby' => 'title', 'order' => 'ASC' ] );
        $all_templates = $result['items'] ?? [];

        $header_templates = array_filter( $all_templates, function( $t ) { return ( $t['type'] ?? '' ) === 'header' && $t['status'] === 'published'; } );
        $footer_templates = array_filter( $all_templates, function( $t ) { return ( $t['type'] ?? '' ) === 'footer' && $t['status'] === 'published'; } );

        wp_nonce_field( 'olo_metabox_' . $post->ID, 'olo_metabox_nonce' );
        ?>
        <div style="padding: 4px 0;">
            <label for="olo_template_select" style="display: block; margin-bottom: 4px; font-size: 12px; color: #50575e;">
                Template associato
            </label>
            <select id="olo_template_select" name="olo_template_id" style="width: 100%; margin-bottom: 8px;">
                <option value="">— Nessun template —</option>
                <?php foreach ( $all_templates as $tpl ) : ?>
                    <option value="<?php echo esc_attr( $tpl['id'] ); ?>" <?php selected( $template_id, (int) $tpl['id'] ); ?>>
                        #<?php echo esc_html( $tpl['id'] ); ?> — <?php echo esc_html( $tpl['title'] ); ?>
                        (<?php echo esc_html( $tpl['status'] ); ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if ( ! empty( $header_templates ) ) : ?>
                <label for="olo_header_select" style="display: block; margin-bottom: 4px; font-size: 12px; color: #50575e;">
                    Header
                </label>
                <select id="olo_header_select" name="olo_header_id" style="width: 100%; margin-bottom: 8px;">
                    <option value="0">— Predefinito (globale) —</option>
                    <?php foreach ( $header_templates as $tpl ) : ?>
                        <option value="<?php echo esc_attr( $tpl['id'] ); ?>" <?php selected( $header_id, (int) $tpl['id'] ); ?>>
                            #<?php echo esc_html( $tpl['id'] ); ?> — <?php echo esc_html( $tpl['title'] ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <?php if ( ! empty( $footer_templates ) ) : ?>
                <label for="olo_footer_select" style="display: block; margin-bottom: 4px; font-size: 12px; color: #50575e;">
                    Footer
                </label>
                <select id="olo_footer_select" name="olo_footer_id" style="width: 100%; margin-bottom: 8px;">
                    <option value="0">— Predefinito (globale) —</option>
                    <?php foreach ( $footer_templates as $tpl ) : ?>
                        <option value="<?php echo esc_attr( $tpl['id'] ); ?>" <?php selected( $footer_id, (int) $tpl['id'] ); ?>>
                            #<?php echo esc_html( $tpl['id'] ); ?> — <?php echo esc_html( $tpl['title'] ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <?php if ( $template_id ) :
                $template = $db->get_template( $template_id );
                $tile_count = $this->count_elements( $template['content'] ?? [] );
            ?>
                <p style="margin: 0 0 8px; color: #50575e; font-size: 11px; text-align: center;">
                    <?php echo (int) $tile_count; ?> elementi &middot;
                    <code style="font-size: 10px;">[olo_template id="<?php echo esc_attr( $template_id ); ?>"]</code>
                </p>
                <a href="<?php echo esc_url( $url ); ?>"
                   class="button button-primary"
                   style="background: #6366f1; border-color: #6366f1; width: 100%; text-align: center;">
                    Modifica con Olobuild
                </a>
            <?php else : ?>
                <a href="<?php echo esc_url( $url ); ?>"
                   class="button button-primary"
                   style="background: #6366f1; border-color: #6366f1; width: 100%; text-align: center;">
                    Crea nuovo template
                </a>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Save meta box template selection.
     */
    public function save_meta_box( $post_id ) {
        if ( ! isset( $_POST['olo_metabox_nonce'] ) ) {
            return;
        }
        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['olo_metabox_nonce'] ) ), 'olo_metabox_' . $post_id ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $template_id = isset( $_POST['olo_template_id'] ) ? absint( wp_unslash( $_POST['olo_template_id'] ) ) : 0;

        if ( $template_id ) {
            update_post_meta( $post_id, '_olo_template_id', $template_id );
        } else {
            delete_post_meta( $post_id, '_olo_template_id' );
        }

        // Header per-page override
        $header_id = isset( $_POST['olo_header_id'] ) ? absint( wp_unslash( $_POST['olo_header_id'] ) ) : 0;
        if ( $header_id ) {
            update_post_meta( $post_id, '_olo_header_id', $header_id );
        } else {
            delete_post_meta( $post_id, '_olo_header_id' );
        }

        // Footer per-page override
        $footer_id = isset( $_POST['olo_footer_id'] ) ? absint( wp_unslash( $_POST['olo_footer_id'] ) ) : 0;
        if ( $footer_id ) {
            update_post_meta( $post_id, '_olo_footer_id', $footer_id );
        } else {
            delete_post_meta( $post_id, '_olo_footer_id' );
        }
    }

    /**
     * Handle creating a new template linked to a post.
     */
    public function handle_create_template() {
        // Routing read-only: si determina solo QUALE azione admin_init eseguire; nessuna
        // modifica di stato qui. Il nonce vero è verificato sotto (wp_verify_nonce olo_create_*)
        // prima di qualsiasi scrittura.
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if ( empty( $_GET['action'] ) || sanitize_key( wp_unslash( $_GET['action'] ) ) !== 'olo_create' ) {
            return;
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- post_id letto per il routing; nonce verificato subito sotto prima di scrivere.
        $post_id = isset( $_GET['post_id'] ) ? absint( wp_unslash( $_GET['post_id'] ) ) : 0;
        if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 'olo_create_' . $post_id ) ) {
            wp_die( 'Controllo di sicurezza fallito.' );
        }

        // Check if already linked
        $existing = get_post_meta( $post_id, '_olo_template_id', true );
        if ( $existing ) {
            wp_safe_redirect( admin_url( 'admin.php?page=olobuilder-templates&template_id=' . $existing . '&post_id=' . $post_id ) );
            exit;
        }

        // Create new template
        $post = get_post( $post_id );
        $db = new Olo_Database();
        $template_id = $db->create_template( [
            'title'    => $post->post_title ?: 'Senza titolo',
            'type'     => $post->post_type,
            'content'  => [],
            'settings' => [ 'post_id' => $post_id ],
            'status'   => 'draft',
        ] );

        if ( $template_id ) {
            update_post_meta( $post_id, '_olo_template_id', $template_id );
        }

        wp_safe_redirect( admin_url( 'admin.php?page=olobuilder-templates&template_id=' . $template_id . '&post_id=' . $post_id ) );
        exit;
    }

    /**
     * Recursively count all nodes in a tree.
     */
    private function count_elements( $nodes ) {
        if ( ! is_array( $nodes ) ) return 0;
        $count = 0;
        foreach ( $nodes as $node ) {
            $count++;
            if ( ! empty( $node['children'] ) && is_array( $node['children'] ) ) {
                $count += $this->count_elements( $node['children'] );
            }
        }
        return $count;
    }

    /**
     * Auto-render Olobuild template in page content if linked.
     */
    public function auto_render_template( $content ) {
        // Recursion guard: prevent re-entry when dynamic content resolves post_content
        static $rendering = false;
        if ( $rendering ) {
            return $content;
        }

        if ( ! is_singular() || ! in_the_loop() || ! is_main_query() ) {
            return $content;
        }

        $post_id = get_the_ID();
        $template_id = get_post_meta( $post_id, '_olo_template_id', true );

        // Fallback: un template "page" può essere collegato alla pagina tramite il suo
        // settings.post_id anche se il meta _olo_template_id è andato perso (tipico delle
        // pagine gestite/tradotte da OLOlang, che possono azzerare i meta in sync). Cerco
        // a ritroso il template e ri-scrivo il meta (self-healing) così i load successivi
        // tornano sulla via veloce.
        if ( ! $template_id ) {
            global $wpdb;
            // Tabella custom del plugin ({prefix}olo_templates); nessun equivalente WP_Query.
            // Nome tabella interpolato da $wpdb->prefix (sicuro); il valore utente ($post_id) è
            // intval e passato come argomento a $wpdb->prepare con placeholder %s (no injection).
            // Risultato non cacheabile (self-healing lookup occasionale, volume limitato).
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $rows = $wpdb->get_results( $wpdb->prepare(
                "SELECT id, settings FROM {$wpdb->prefix}olo_templates WHERE type = 'page' AND status = 'published' AND settings LIKE %s ORDER BY id DESC",
                '%"post_id":' . intval( $post_id ) . '%'
            ), ARRAY_A );
            if ( $rows ) {
                foreach ( $rows as $r ) {
                    $st = json_decode( $r['settings'], true );
                    if ( is_array( $st ) && isset( $st['post_id'] ) && intval( $st['post_id'] ) === intval( $post_id ) ) {
                        $template_id = $r['id'];
                        update_post_meta( $post_id, '_olo_template_id', $template_id );
                        break;
                    }
                }
            }
        }

        if ( ! $template_id ) {
            return $content;
        }

        $rendering = true;
        $renderer = new Olo_Frontend_Renderer();
        $olo_content = $renderer->render_shortcode( [ 'id' => $template_id ] );
        $rendering = false;

        // Prepend Olobuild content before the regular content
        return $olo_content . $content;
    }
}
