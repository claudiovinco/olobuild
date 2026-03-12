<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Role_Manager {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    /**
     * Initialize the role manager hooks.
     */
    public function init() {
        add_filter( 'olo_can_edit_builder', [ $this, 'check_permission' ], 10, 1 );
        add_action( 'admin_menu', [ $this, 'add_admin_page' ] );
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    /**
     * Check if current user has permission to edit the builder.
     *
     * @param bool $can_edit Current permission state.
     * @return bool Whether the user can edit.
     */
    public function check_permission( $can_edit ) {
        $roles = get_option( 'olo_builder_roles', [ 'administrator' ] );
        $user  = wp_get_current_user();

        if ( ! $user->ID ) {
            return false;
        }

        foreach ( $roles as $role ) {
            if ( in_array( $role, (array) $user->roles, true ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all available WordPress roles for settings UI.
     *
     * @return array Array of [ 'value' => slug, 'label' => name ].
     */
    public function get_available_roles() {
        global $wp_roles;

        if ( ! isset( $wp_roles ) ) {
            $wp_roles = wp_roles();
        }

        $roles = [];
        foreach ( $wp_roles->roles as $key => $role ) {
            $roles[] = [
                'value' => $key,
                'label' => $role['name'],
            ];
        }

        return $roles;
    }

    /**
     * Check if a user is in content-only mode.
     * Content-only users can edit text and media but not the page structure
     * (no adding/removing tiles, no reordering, no section settings).
     *
     * @param int $user_id User ID. Defaults to current user.
     * @return bool Whether the user is restricted to content-only editing.
     */
    public function is_content_only( $user_id = 0 ) {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        $content_only_roles = get_option( 'olo_content_only_roles', [] );

        if ( empty( $content_only_roles ) ) {
            return false;
        }

        $user = get_userdata( $user_id );
        if ( ! $user ) {
            return true;
        }

        foreach ( (array) $user->roles as $role ) {
            if ( in_array( $role, $content_only_roles, true ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the allowed builder roles option value.
     *
     * @return array List of role slugs allowed to use the builder.
     */
    public function get_allowed_roles() {
        return get_option( 'olo_builder_roles', [ 'administrator' ] );
    }

    /**
     * Update the allowed builder roles.
     *
     * @param array $roles Array of role slugs.
     * @return bool Whether the option was updated.
     */
    public function set_allowed_roles( $roles ) {
        $sanitized = array_map( 'sanitize_key', (array) $roles );
        $sanitized = array_values( array_filter( $sanitized ) );

        // Ensure administrator is always included
        if ( ! in_array( 'administrator', $sanitized, true ) ) {
            array_unshift( $sanitized, 'administrator' );
        }

        return update_option( 'olo_builder_roles', $sanitized );
    }

    /**
     * Get the content-only roles option value.
     *
     * @return array List of role slugs restricted to content-only editing.
     */
    public function get_content_only_roles() {
        return get_option( 'olo_content_only_roles', [] );
    }

    /**
     * Update the content-only roles.
     *
     * @param array $roles Array of role slugs.
     * @return bool Whether the option was updated.
     */
    public function set_content_only_roles( $roles ) {
        $sanitized = array_map( 'sanitize_key', (array) $roles );
        $sanitized = array_values( array_filter( $sanitized ) );

        return update_option( 'olo_content_only_roles', $sanitized );
    }

    /* ─────────────────────────────────────────────
     * Granular Restrictions
     * ───────────────────────────────────────────── */

    /**
     * Available restriction options that can be applied per role.
     */
    public static function get_restriction_options() {
        return [
            'hide_style_panel'     => 'Nascondi pannello Stile',
            'hide_advanced_panel'  => 'Nascondi pannello Avanzate',
            'hide_animation_panel' => 'Nascondi pannello Animazioni',
            'block_add_elements'   => 'Blocca aggiunta elementi',
            'block_remove_elements' => 'Blocca rimozione elementi',
            'block_reorder'        => 'Blocca riordinamento',
            'block_section_settings' => 'Blocca impostazioni section/row',
            'hide_page_settings'   => 'Nascondi impostazioni pagina',
            'hide_global_styles'   => 'Nascondi stili globali',
            'hide_template_library' => 'Nascondi libreria template',
        ];
    }

    /**
     * Get restrictions for a specific role.
     */
    public function get_role_restrictions( $role ) {
        $all = get_option( 'olo_role_restrictions', [] );
        return $all[ $role ] ?? [];
    }

    /**
     * Get restrictions for the current user (union of all role restrictions).
     */
    public function get_current_user_restrictions() {
        $user = wp_get_current_user();
        if ( ! $user->ID ) {
            return [];
        }

        // Administrators have no restrictions
        if ( in_array( 'administrator', (array) $user->roles, true ) ) {
            return [];
        }

        $all_restrictions = get_option( 'olo_role_restrictions', [] );
        $merged = [];

        foreach ( (array) $user->roles as $role ) {
            if ( isset( $all_restrictions[ $role ] ) ) {
                $merged = array_merge( $merged, $all_restrictions[ $role ] );
            }
        }

        return array_unique( $merged );
    }

    /**
     * Filter oloData to include user restrictions for the builder.
     */
    public function filter_builder_data( $data ) {
        $data['userRestrictions'] = $this->get_current_user_restrictions();
        $data['isContentOnly']   = $this->is_content_only();
        $data['isDesignOnly']    = $this->is_design_only();
        return $data;
    }

    /**
     * Check if current user is in design-only mode.
     */
    public function is_design_only( $user_id = 0 ) {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        $design_only_roles = get_option( 'olo_design_only_roles', [] );
        if ( empty( $design_only_roles ) ) {
            return false;
        }

        $user = get_userdata( $user_id );
        if ( ! $user ) {
            return false;
        }

        foreach ( (array) $user->roles as $role ) {
            if ( in_array( $role, $design_only_roles, true ) ) {
                return true;
            }
        }

        return false;
    }

    /* ─────────────────────────────────────────────
     * Admin Page
     * ───────────────────────────────────────────── */

    public function add_admin_page() {
        add_submenu_page(
            'olobuild',
            'Permessi Utente',
            'Permessi',
            'manage_options',
            'olo-role-manager',
            [ $this, 'render_admin_page' ]
        );
    }

    public function render_admin_page() {
        $roles           = $this->get_available_roles();
        $allowed         = $this->get_allowed_roles();
        $content_only    = $this->get_content_only_roles();
        $design_only     = get_option( 'olo_design_only_roles', [] );
        ?>
        <div class="wrap">
            <h1>Permessi Utente — Olobuild</h1>
            <p>Gestisci chi puo accedere al builder e con quali permessi.</p>

            <table class="widefat" style="max-width:800px;margin-top:20px">
                <thead>
                    <tr>
                        <th>Ruolo</th>
                        <th style="text-align:center">Accesso completo</th>
                        <th style="text-align:center">Solo design</th>
                        <th style="text-align:center">Solo contenuti</th>
                        <th style="text-align:center">Nessun accesso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $roles as $role ) :
                        $slug = $role['value'];
                        $is_admin = ( $slug === 'administrator' );
                        $level = 'none';
                        if ( in_array( $slug, $allowed, true ) ) {
                            if ( in_array( $slug, $content_only, true ) ) {
                                $level = 'content';
                            } elseif ( in_array( $slug, $design_only, true ) ) {
                                $level = 'design';
                            } else {
                                $level = 'full';
                            }
                        }
                    ?>
                    <tr>
                        <td><strong><?php echo esc_html( $role['label'] ); ?></strong></td>
                        <td style="text-align:center"><input type="radio" name="olo_role_<?php echo esc_attr( $slug ); ?>" value="full" <?php checked( $level, 'full' ); ?><?php if ( $is_admin ) echo ' checked disabled'; ?> class="olo-role-radio" data-role="<?php echo esc_attr( $slug ); ?>" /></td>
                        <td style="text-align:center"><input type="radio" name="olo_role_<?php echo esc_attr( $slug ); ?>" value="design" <?php checked( $level, 'design' ); ?><?php if ( $is_admin ) echo ' disabled'; ?> class="olo-role-radio" data-role="<?php echo esc_attr( $slug ); ?>" /></td>
                        <td style="text-align:center"><input type="radio" name="olo_role_<?php echo esc_attr( $slug ); ?>" value="content" <?php checked( $level, 'content' ); ?><?php if ( $is_admin ) echo ' disabled'; ?> class="olo-role-radio" data-role="<?php echo esc_attr( $slug ); ?>" /></td>
                        <td style="text-align:center"><input type="radio" name="olo_role_<?php echo esc_attr( $slug ); ?>" value="none" <?php checked( $level, 'none' ); ?><?php if ( $is_admin ) echo ' disabled'; ?> class="olo-role-radio" data-role="<?php echo esc_attr( $slug ); ?>" /></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div style="margin-top:15px">
                <p><strong>Accesso completo:</strong> Puo modificare struttura, design e contenuti</p>
                <p><strong>Solo design:</strong> Puo modificare stili e layout, ma non aggiungere/rimuovere elementi</p>
                <p><strong>Solo contenuti:</strong> Puo modificare solo testo e immagini</p>
            </div>

            <p style="margin-top:20px">
                <button class="button button-primary" id="olo-save-roles">Salva Permessi</button>
                <span id="olo-roles-msg" style="margin-left:10px;color:#059669;display:none">Salvato!</span>
            </p>

            <?php
            // Granular restrictions section
            $restriction_opts = self::get_restriction_options();
            $all_restrictions = get_option( 'olo_role_restrictions', [] );
            $non_admin_roles  = array_filter( $roles, function( $r ) { return $r['value'] !== 'administrator'; } );
            ?>
            <h2 style="margin-top:40px">Restrizioni Granulari per Pannello</h2>
            <p>Seleziona quali pannelli e funzioni nascondere per ogni ruolo.</p>

            <table class="widefat" style="max-width:900px;margin-top:15px">
                <thead>
                    <tr>
                        <th>Restrizione</th>
                        <?php foreach ( $non_admin_roles as $role ) : ?>
                            <th style="text-align:center;font-size:12px"><?php echo esc_html( $role['label'] ); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $restriction_opts as $key => $label ) : ?>
                    <tr>
                        <td><?php echo esc_html( $label ); ?></td>
                        <?php foreach ( $non_admin_roles as $role ) :
                            $slug = $role['value'];
                            $role_res = $all_restrictions[ $slug ] ?? [];
                            $is_checked = in_array( $key, $role_res, true );
                        ?>
                            <td style="text-align:center">
                                <input type="checkbox" class="olo-restriction-cb" data-role="<?php echo esc_attr( $slug ); ?>" data-restriction="<?php echo esc_attr( $key ); ?>" <?php checked( $is_checked ); ?> />
                            </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p style="margin-top:15px">
                <button class="button" id="olo-save-restrictions">Salva Restrizioni</button>
                <span id="olo-restrictions-msg" style="margin-left:10px;color:#059669;display:none">Salvato!</span>
            </p>

            <script>
            document.getElementById('olo-save-roles').addEventListener('click', function() {
                var data = {};
                document.querySelectorAll('.olo-role-radio:checked').forEach(function(r) {
                    data[r.dataset.role] = r.value;
                });
                data['administrator'] = 'full';

                fetch('<?php echo esc_js( rest_url( 'olo/v1/role-manager' ) ); ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': '<?php echo esc_js( wp_create_nonce( 'wp_rest' ) ); ?>' },
                    body: JSON.stringify(data)
                })
                .then(function(r) { return r.json(); })
                .then(function(d) {
                    var msg = document.getElementById('olo-roles-msg');
                    msg.style.display = 'inline';
                    msg.textContent = d.success ? 'Salvato!' : 'Errore';
                    setTimeout(function() { msg.style.display = 'none'; }, 3000);
                });
            });

            document.getElementById('olo-save-restrictions').addEventListener('click', function() {
                var data = {};
                document.querySelectorAll('.olo-restriction-cb').forEach(function(cb) {
                    var role = cb.dataset.role;
                    var key = cb.dataset.restriction;
                    if (!data[role]) { data[role] = []; }
                    if (cb.checked) { data[role].push(key); }
                });

                fetch('<?php echo esc_js( rest_url( 'olo/v1/role-restrictions' ) ); ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': '<?php echo esc_js( wp_create_nonce( 'wp_rest' ) ); ?>' },
                    body: JSON.stringify(data)
                })
                .then(function(r) { return r.json(); })
                .then(function(d) {
                    var msg = document.getElementById('olo-restrictions-msg');
                    msg.style.display = 'inline';
                    msg.textContent = d.success ? 'Salvato!' : 'Errore';
                    setTimeout(function() { msg.style.display = 'none'; }, 3000);
                });
            });
            </script>
        </div>
        <?php
    }

    /* ─────────────────────────────────────────────
     * REST API
     * ───────────────────────────────────────────── */

    public function register_routes() {
        register_rest_route( 'olo/v1', '/role-manager', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'save_roles_api' ],
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            },
        ] );

        register_rest_route( 'olo/v1', '/role-restrictions', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'save_restrictions_api' ],
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            },
        ] );
    }

    public function save_roles_api( $request ) {
        $data = $request->get_json_params();
        if ( ! is_array( $data ) ) {
            return new WP_Error( 'invalid', 'Dati non validi', [ 'status' => 400 ] );
        }

        $full_access   = [ 'administrator' ];
        $design_only   = [];
        $content_only  = [];

        foreach ( $data as $role => $level ) {
            $role = sanitize_key( $role );
            if ( $role === 'administrator' ) {
                continue; // Always full
            }

            switch ( $level ) {
                case 'full':
                    $full_access[] = $role;
                    break;
                case 'design':
                    $full_access[] = $role;
                    $design_only[] = $role;
                    break;
                case 'content':
                    $full_access[] = $role;
                    $content_only[] = $role;
                    break;
                // 'none' = not in any list
            }
        }

        $this->set_allowed_roles( $full_access );
        $this->set_content_only_roles( $content_only );
        update_option( 'olo_design_only_roles', $design_only );

        return rest_ensure_response( [ 'success' => true ] );
    }

    /**
     * Save granular restrictions per role.
     */
    public function save_restrictions_api( $request ) {
        $data = $request->get_json_params();
        if ( ! is_array( $data ) ) {
            return new WP_Error( 'invalid', 'Dati non validi', [ 'status' => 400 ] );
        }

        $valid_keys = array_keys( self::get_restriction_options() );
        $clean = [];

        foreach ( $data as $role => $restrictions ) {
            $role = sanitize_key( $role );
            if ( $role === 'administrator' ) {
                continue; // No restrictions for admins
            }

            if ( ! is_array( $restrictions ) ) {
                continue;
            }

            $sanitized = [];
            foreach ( $restrictions as $key ) {
                $key = sanitize_key( $key );
                if ( in_array( $key, $valid_keys, true ) ) {
                    $sanitized[] = $key;
                }
            }

            if ( ! empty( $sanitized ) ) {
                $clean[ $role ] = $sanitized;
            }
        }

        update_option( 'olo_role_restrictions', $clean );

        return rest_ensure_response( [ 'success' => true ] );
    }
}
