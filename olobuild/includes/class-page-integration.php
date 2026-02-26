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
    }

    /**
     * Get the builder URL for a post.
     * If a template is already linked, edit it. Otherwise, create one.
     */
    private function get_builder_url( $post_id ) {
        $template_id = get_post_meta( $post_id, '_olo_template_id', true );

        if ( $template_id ) {
            return admin_url( 'admin.php?page=olobuilder&template_id=' . $template_id . '&post_id=' . $post_id );
        }

        // Check for CPT single template (olo_active_single_{post_type})
        $post_type = get_post_type( $post_id );
        if ( $post_type && $post_type !== 'page' && $post_type !== 'post' ) {
            $single_tpl = (int) get_option( "olo_active_single_{$post_type}", 0 );
            if ( $single_tpl ) {
                return admin_url( 'admin.php?page=olobuilder&template_id=' . $single_tpl . '&post_id=' . $post_id );
            }
        }

        // No template linked yet → URL to create one
        return wp_nonce_url(
            admin_url( 'admin.php?page=olobuilder&action=olo_create&post_id=' . $post_id ),
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

        $url = $this->get_builder_url( $post->ID );
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
     */
    public function add_admin_bar_button( $wp_admin_bar ) {
        if ( ! current_user_can( 'edit_posts' ) ) {
            return;
        }

        // On single post/page view (frontend)
        if ( is_singular() && ! is_admin() ) {
            $post_id = get_queried_object_id();
            $url = $this->get_builder_url( $post_id );
            $template_id = get_post_meta( $post_id, '_olo_template_id', true );

            // Also check CPT single template
            if ( ! $template_id ) {
                $pt = get_post_type( $post_id );
                if ( $pt && $pt !== 'page' && $pt !== 'post' ) {
                    $template_id = (int) get_option( "olo_active_single_{$pt}", 0 );
                }
            }

            $wp_admin_bar->add_node( [
                'id'    => 'olobuilder-edit',
                'title' => '<span style="color: #a5b4fc;">&#9638;</span> ' . ( $template_id ? 'Modifica con Olobuild' : 'Crea con Olobuild' ),
                'href'  => $url,
                'meta'  => [ 'title' => 'Apri in Olobuild' ],
            ] );
        }

        // On post edit screen (admin)
        global $pagenow, $post;
        if ( is_admin() && $pagenow === 'post.php' && $post ) {
            $url = $this->get_builder_url( $post->ID );
            $template_id = get_post_meta( $post->ID, '_olo_template_id', true );

            $wp_admin_bar->add_node( [
                'id'    => 'olobuilder-edit',
                'title' => '<span style="color: #a5b4fc;">&#9638;</span> ' . ( $template_id ? 'Modifica con Olobuild' : 'Crea con Olobuild' ),
                'href'  => $url,
                'meta'  => [ 'title' => 'Apri in Olobuild' ],
            ] );
        }
    }

    /**
     * Add meta box in page/post editor.
     */
    public function add_meta_box() {
        $post_types = [ 'page', 'post' ];
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
        $url = $this->get_builder_url( $post->ID );

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
                    <?php echo $tile_count; ?> elementi &middot;
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
        if ( ! wp_verify_nonce( $_POST['olo_metabox_nonce'], 'olo_metabox_' . $post_id ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $template_id = isset( $_POST['olo_template_id'] ) ? absint( $_POST['olo_template_id'] ) : 0;

        if ( $template_id ) {
            update_post_meta( $post_id, '_olo_template_id', $template_id );
        } else {
            delete_post_meta( $post_id, '_olo_template_id' );
        }

        // Header per-page override
        $header_id = isset( $_POST['olo_header_id'] ) ? absint( $_POST['olo_header_id'] ) : 0;
        if ( $header_id ) {
            update_post_meta( $post_id, '_olo_header_id', $header_id );
        } else {
            delete_post_meta( $post_id, '_olo_header_id' );
        }

        // Footer per-page override
        $footer_id = isset( $_POST['olo_footer_id'] ) ? absint( $_POST['olo_footer_id'] ) : 0;
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
        if ( empty( $_GET['action'] ) || $_GET['action'] !== 'olo_create' ) {
            return;
        }

        $post_id = absint( $_GET['post_id'] ?? 0 );
        if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( ! wp_verify_nonce( $_GET['_wpnonce'] ?? '', 'olo_create_' . $post_id ) ) {
            wp_die( 'Controllo di sicurezza fallito.' );
        }

        // Check if already linked
        $existing = get_post_meta( $post_id, '_olo_template_id', true );
        if ( $existing ) {
            wp_safe_redirect( admin_url( 'admin.php?page=olobuilder&template_id=' . $existing . '&post_id=' . $post_id ) );
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

        wp_safe_redirect( admin_url( 'admin.php?page=olobuilder&template_id=' . $template_id . '&post_id=' . $post_id ) );
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
