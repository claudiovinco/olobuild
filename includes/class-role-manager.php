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
        add_filter( 'olobuild_can_edit_builder', [ $this, 'check_permission' ], 10, 1 );
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
        // Se l'amministratore non ha MAI configurato i ruoli del builder, non restringere:
        // si mantiene il comportamento storico (chi possiede edit_pages puo' editare). Cosi'
        // l'attivazione di questo enforcement non blocca utenti che oggi hanno gia' accesso.
        $roles = get_option( 'olobuild_builder_roles', null );
        if ( ! is_array( $roles ) || empty( $roles ) ) {
            return $can_edit;
        }

        $user = wp_get_current_user();
        if ( ! $user->ID ) {
            return false;
        }

        // Gli amministratori non vengono mai esclusi.
        if ( user_can( $user, 'manage_options' ) ) {
            return $can_edit;
        }

        foreach ( (array) $user->roles as $role ) {
            if ( in_array( $role, (array) $roles, true ) ) {
                return $can_edit;
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

        $content_only_roles = get_option( 'olobuild_content_only_roles', [] );

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
        return get_option( 'olobuild_builder_roles', [ 'administrator' ] );
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

        return update_option( 'olobuild_builder_roles', $sanitized );
    }

    /**
     * Get the content-only roles option value.
     *
     * @return array List of role slugs restricted to content-only editing.
     */
    public function get_content_only_roles() {
        return get_option( 'olobuild_content_only_roles', [] );
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

        return update_option( 'olobuild_content_only_roles', $sanitized );
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
        $all = get_option( 'olobuild_role_restrictions', [] );
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

        $all_restrictions = get_option( 'olobuild_role_restrictions', [] );
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

        $design_only_roles = get_option( 'olobuild_design_only_roles', [] );
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
        // v1.0.30 — pagina migrata in ?page=olobuilder-settings&tab=permessi
        // Submenu rimosso: la scheda Configurazione → Permessi & Ruoli mostra lo stato
        // in sola lettura (GET /role-manager) e ne permette il ripristino (DELETE).
        // La classe resta attiva per il filtro olobuild_can_edit_builder (API del builder).
    }

    public function render_admin_page() {
        $roles           = $this->get_available_roles();
        $allowed         = $this->get_allowed_roles();
        $content_only    = $this->get_content_only_roles();
        $design_only     = get_option( 'olobuild_design_only_roles', [] );
        $allowed_count = is_array( $allowed ) ? count( $allowed ) : 0;
        $design_only_count = is_array( $design_only ) ? count( $design_only ) : 0;
        ?>
        <?php Olobuild_Builder::cockpit_shell_open( '<b>' . esc_html__( 'Permessi Utente', 'olobuild' ) . '</b>' ); ?>
        <main class="olo-cockpit-main olo-cockpit-legacy">
            <?php
            // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML generated by Olobuild_Builder::cockpit_page_head(), which escapes title via esc_html() and sub via wp_kses_post() internally; the sub markup only interpolates (int) casts.
            echo Olobuild_Builder::cockpit_page_head( [
                'title' => __( 'Permessi & Ruoli', 'olobuild' ),
                'sub'   => sprintf(
                    /* translators: 1: roles with edit access, 2: roles with design-only access */
                    __( '%1$s ruoli con accesso completo · %2$s ruoli design-only', 'olobuild' ),
                    '<b>' . (int) $allowed_count . '</b>',
                    '<b>' . (int) $design_only_count . '</b>'
                ),
            ] );
            // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
            ?>

            <div id="olo-roles-msg-box" style="margin-top:16px"></div>

            <div class="olo-card">
                <div class="olo-card-head">
                    <div class="olo-card-icon black">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    </div>
                    <div>
                        <h3><?php esc_html_e( 'Livelli di Accesso', 'olobuild' ); ?></h3>
                        <p><?php esc_html_e( 'Assegna un livello di accesso per ciascun ruolo WordPress', 'olobuild' ); ?></p>
                    </div>
                </div>
                <div class="olo-card-body" style="padding:0">
                    <table class="olo-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'Ruolo', 'olobuild' ); ?></th>
                                <th style="text-align:center"><?php esc_html_e( 'Accesso completo', 'olobuild' ); ?></th>
                                <th style="text-align:center"><?php esc_html_e( 'Solo design', 'olobuild' ); ?></th>
                                <th style="text-align:center"><?php esc_html_e( 'Solo contenuti', 'olobuild' ); ?></th>
                                <th style="text-align:center"><?php esc_html_e( 'Nessun accesso', 'olobuild' ); ?></th>
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
                                <td style="text-align:center"><input type="radio" name="olo_role_<?php echo esc_attr( $slug ); ?>" value="full" <?php checked( $level, 'full' ); ?><?php if ( $is_admin ) echo ' checked disabled'; ?> class="olo-role-radio" data-role="<?php echo esc_attr( $slug ); ?>" style="accent-color:#1a1a1a" /></td>
                                <td style="text-align:center"><input type="radio" name="olo_role_<?php echo esc_attr( $slug ); ?>" value="design" <?php checked( $level, 'design' ); ?><?php if ( $is_admin ) echo ' disabled'; ?> class="olo-role-radio" data-role="<?php echo esc_attr( $slug ); ?>" style="accent-color:#1a1a1a" /></td>
                                <td style="text-align:center"><input type="radio" name="olo_role_<?php echo esc_attr( $slug ); ?>" value="content" <?php checked( $level, 'content' ); ?><?php if ( $is_admin ) echo ' disabled'; ?> class="olo-role-radio" data-role="<?php echo esc_attr( $slug ); ?>" style="accent-color:#1a1a1a" /></td>
                                <td style="text-align:center"><input type="radio" name="olo_role_<?php echo esc_attr( $slug ); ?>" value="none" <?php checked( $level, 'none' ); ?><?php if ( $is_admin ) echo ' disabled'; ?> class="olo-role-radio" data-role="<?php echo esc_attr( $slug ); ?>" style="accent-color:#1a1a1a" /></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="olo-msg info" style="margin-bottom:20px">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                <div>
                    <strong><?php esc_html_e( 'Completo:', 'olobuild' ); ?></strong> <?php esc_html_e( 'struttura + design + contenuti', 'olobuild' ); ?> &nbsp;&bull;&nbsp;
                    <strong><?php esc_html_e( 'Design:', 'olobuild' ); ?></strong> <?php esc_html_e( 'stili e layout', 'olobuild' ); ?> &nbsp;&bull;&nbsp;
                    <strong><?php esc_html_e( 'Contenuti:', 'olobuild' ); ?></strong> <?php esc_html_e( 'solo testo e immagini', 'olobuild' ); ?>
                </div>
            </div>

            <div class="olo-actions" style="margin-bottom:30px">
                <button class="olo-btn-save" id="olo-save-roles">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    <?php esc_html_e( 'Salva Permessi', 'olobuild' ); ?>
                </button>
            </div>

            <?php
            $restriction_opts = self::get_restriction_options();
            $all_restrictions = get_option( 'olobuild_role_restrictions', [] );
            $non_admin_roles  = array_filter( $roles, function( $r ) { return $r['value'] !== 'administrator'; } );
            ?>

            <div class="olo-card">
                <div class="olo-card-head">
                    <div class="olo-card-icon orange">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <div>
                        <h3>Restrizioni Granulari per Pannello</h3>
                        <p>Seleziona quali pannelli e funzioni nascondere per ogni ruolo</p>
                    </div>
                </div>
                <div class="olo-card-body" style="padding:0">
                    <table class="olo-table">
                        <thead>
                            <tr>
                                <th>Restrizione</th>
                                <?php foreach ( $non_admin_roles as $role ) : ?>
                                    <th style="text-align:center;font-size:11px"><?php echo esc_html( $role['label'] ); ?></th>
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
                                        <input type="checkbox" class="olo-restriction-cb" data-role="<?php echo esc_attr( $slug ); ?>" data-restriction="<?php echo esc_attr( $key ); ?>" <?php checked( $is_checked ); ?> style="accent-color:#1a1a1a" />
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="olo-actions">
                <button class="olo-btn-reset" id="olo-save-restrictions">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Salva Restrizioni
                </button>
            </div>

            <script>
            (function(){
                function showMsg(text, ok) {
                    var box = document.getElementById('olo-roles-msg-box');
                    box.className = 'olo-msg ' + (ok ? 'success' : 'error');
                    box.innerHTML = (ok ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg> ' : '') + text;
                    setTimeout(function() { box.className = ''; box.innerHTML = ''; }, 3000);
                }

                document.getElementById('olo-save-roles').addEventListener('click', function() {
                    var btn = this;
                    btn.disabled = true;
                    var data = {};
                    document.querySelectorAll('.olo-role-radio:checked').forEach(function(r) {
                        data[r.dataset.role] = r.value;
                    });
                    data['administrator'] = 'full';

                    fetch('<?php echo esc_js( rest_url( 'olobuild/v1/role-manager' ) ); ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': '<?php echo esc_js( wp_create_nonce( 'wp_rest' ) ); ?>' },
                        body: JSON.stringify(data)
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(d) {
                        showMsg(d.success ? 'Permessi salvati' : 'Errore nel salvataggio', d.success);
                    })
                    .finally(function() { btn.disabled = false; });
                });

                document.getElementById('olo-save-restrictions').addEventListener('click', function() {
                    var btn = this;
                    btn.disabled = true;
                    var data = {};
                    document.querySelectorAll('.olo-restriction-cb').forEach(function(cb) {
                        var role = cb.dataset.role;
                        var key = cb.dataset.restriction;
                        if (!data[role]) { data[role] = []; }
                        if (cb.checked) { data[role].push(key); }
                    });

                    fetch('<?php echo esc_js( rest_url( 'olobuild/v1/role-restrictions' ) ); ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': '<?php echo esc_js( wp_create_nonce( 'wp_rest' ) ); ?>' },
                        body: JSON.stringify(data)
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(d) {
                        showMsg(d.success ? 'Restrizioni salvate' : 'Errore nel salvataggio', d.success);
                    })
                    .finally(function() { btn.disabled = false; });
                });
            })();
            </script>
        </main>
        <?php Olobuild_Builder::cockpit_shell_close(); ?>
        <?php
    }

    /* ─────────────────────────────────────────────
     * REST API
     * ───────────────────────────────────────────── */

    public function register_routes() {
        register_rest_route( 'olobuild/v1', '/role-manager', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_roles_api' ],
                'permission_callback' => [ $this, 'can_manage_roles' ],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'save_roles_api' ],
                'permission_callback' => [ $this, 'can_manage_roles' ],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [ $this, 'reset_roles_api' ],
                'permission_callback' => [ $this, 'can_manage_roles' ],
            ],
        ] );

        register_rest_route( 'olobuild/v1', '/role-restrictions', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'save_restrictions_api' ],
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            },
        ] );
    }

    /**
     * Permesso delle rotte /role-manager: solo chi gestisce le opzioni del sito.
     *
     * @return bool
     */
    public function can_manage_roles() {
        return current_user_can( 'manage_options' );
    }

    /**
     * Stato reale dell'accesso al builder, per la scheda Permessi (sola lettura).
     *
     * «configured» segue check_permission(): option assente o vuota = nessuna
     * restrizione (comportamento storico). NON si usa get_allowed_roles(): il suo
     * default ['administrator'] farebbe sembrare «solo amministratori» un sito mai
     * configurato.
     *
     * @return array
     */
    public function get_access_state() {
        $raw        = get_option( 'olobuild_builder_roles', null );
        $configured = is_array( $raw ) && ! empty( $raw );
        $allowed    = $configured ? array_values( array_filter( $raw, 'is_string' ) ) : [];
        $counts     = count_users();
        $avail      = ( isset( $counts['avail_roles'] ) && is_array( $counts['avail_roles'] ) ) ? $counts['avail_roles'] : [];

        $roles = [];
        foreach ( wp_roles()->roles as $slug => $role ) {
            $caps    = ( isset( $role['capabilities'] ) && is_array( $role['capabilities'] ) ) ? $role['capabilities'] : [];
            $roles[] = [
                'slug'           => (string) $slug,
                'name'           => translate_user_role( isset( $role['name'] ) ? $role['name'] : (string) $slug ),
                'users'          => isset( $avail[ $slug ] ) ? (int) $avail[ $slug ] : 0,
                'edit_pages'     => ! empty( $caps['edit_pages'] ),
                'manage_options' => ! empty( $caps['manage_options'] ),
            ];
        }

        return [
            'configured' => $configured,
            'allowed'    => $allowed,
            'roles'      => $roles,
        ];
    }

    public function get_roles_api() {
        return rest_ensure_response( $this->get_access_state() );
    }

    /**
     * «Ripristina comportamento predefinito»: senza olobuild_builder_roles
     * check_permission() non restringe più (decidono i permessi di WordPress).
     * Solo su clic esplicito: un ['administrator'] salvato può essere voluto.
     * Le altre option (content/design only, restrizioni) restano come sono.
     */
    public function reset_roles_api() {
        delete_option( 'olobuild_builder_roles' );
        return rest_ensure_response( $this->get_access_state() );
    }

    public function save_roles_api( $request ) {
        $data = $request->get_json_params();
        if ( ! is_array( $data ) ) {
            return new WP_Error( 'invalid', 'Dati non validi', [ 'status' => 400 ] );
        }

        // Formato atteso: { ruolo: 'full'|'design'|'content'|'none' } con ruoli WP esistenti.
        // Tutto il resto si rifiuta PRIMA di scrivere: il { roles, matrix, advanced } della
        // vecchia scheda Permessi (ancora in cache in qualche browser) non combaciava con
        // nessun caso e riduceva olobuild_builder_roles ai soli amministratori; lo switch
        // lasco trattava anche true (e 0 su PHP 7.4) come 'full'.
        $levels  = [ 'full', 'design', 'content', 'none' ];
        $known   = array_map( 'strval', array_keys( wp_roles()->roles ) );
        $invalid = [];
        foreach ( $data as $role => $level ) {
            if ( ! is_string( $level )
                || ! in_array( $level, $levels, true )
                || ! in_array( sanitize_key( (string) $role ), $known, true ) ) {
                $invalid[] = sanitize_text_field( (string) $role );
            }
        }
        if ( empty( $data ) || ! empty( $invalid ) ) {
            return new WP_Error(
                'olobuild_invalid_roles',
                __( 'Dati dei permessi non validi: nessuna modifica salvata.', 'olobuild' ),
                [
                    'status'  => 400,
                    'invalid' => $invalid,
                ]
            );
        }

        // Si parte dagli elenchi attuali e si cambiano SOLO i ruoli nominati: un ruolo
        // assente dal payload resta com'è. Prima gli assenti valevano 'none' e un payload
        // valido ma parziale ({ administrator: 'full' }) toglieva il builder a tutti gli
        // altri. Una revoca si chiede per nome, con 'none'.
        // Option mai configurata = nessuna restrizione (check_permission): per lasciare
        // invariati i ruoli non nominati si parte da tutti i ruoli WordPress.
        $saved        = get_option( 'olobuild_builder_roles', null );
        $full_access  = ( is_array( $saved ) && ! empty( $saved ) ) ? self::role_list( $saved ) : $known;
        $design_only  = self::role_list( get_option( 'olobuild_design_only_roles', [] ) );
        $content_only = self::role_list( get_option( 'olobuild_content_only_roles', [] ) );
        $before       = [ $full_access, $design_only, $content_only ];

        foreach ( $data as $role => $level ) {
            $role = sanitize_key( (string) $role );
            if ( $role === 'administrator' ) {
                continue; // Always full
            }

            // 'full' = solo in olobuild_builder_roles; 'design'/'content' = anche
            // nel proprio elenco; 'none' = in nessuno.
            $full_access  = self::with_role( $full_access, $role, 'none' !== $level );
            $design_only  = self::with_role( $design_only, $role, 'design' === $level );
            $content_only = self::with_role( $content_only, $role, 'content' === $level );
        }

        // Si scrive SOLO l'elenco che cambia. Riscriverli tutti e tre appena uno cambiava
        // salvava olobuild_builder_roles con i ruoli di oggi anche per un { editor: 'content' }:
        // un sito mai configurato diventava ristretto e i ruoli registrati dopo (shop_manager…)
        // perdevano il builder. Così olobuild_builder_roles nasce solo da un 'none' esplicito.
        if ( $full_access !== $before[0] ) {
            $this->set_allowed_roles( $full_access );
        }
        if ( $design_only !== $before[1] ) {
            update_option( 'olobuild_design_only_roles', $design_only );
        }
        if ( $content_only !== $before[2] ) {
            $this->set_content_only_roles( $content_only );
        }

        return rest_ensure_response( [ 'success' => true ] );
    }

    /**
     * Elenco di slug da una option di ruoli: solo stringhe non vuote, chiavi 0..n.
     *
     * @param mixed $value Valore salvato.
     * @return array
     */
    private static function role_list( $value ) {
        return array_values( array_filter( array_filter( (array) $value, 'is_string' ) ) );
    }

    /**
     * Mette o toglie un ruolo da un elenco senza toccare gli altri né l'ordine:
     * un ruolo già al suo posto lascia l'elenco identico.
     *
     * @param array  $list    Slug dei ruoli.
     * @param string $role    Slug del ruolo.
     * @param bool   $present true = deve esserci, false = non deve esserci.
     * @return array
     */
    private static function with_role( array $list, $role, $present ) {
        $has = in_array( $role, $list, true );
        if ( $present && ! $has ) {
            $list[] = $role;
        } elseif ( ! $present && $has ) {
            $list = array_values( array_diff( $list, [ $role ] ) );
        }
        return $list;
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

        update_option( 'olobuild_role_restrictions', $clean );

        return rest_ensure_response( [ 'success' => true ] );
    }
}
