<?php
/**
 * Olobuild Diagnostics — admin-only debug page.
 * Accessible to administrators at: /wp-admin/tools.php?page=olo-diagnostics
 * Used only for troubleshooting access issues.
 *
 * NOTA sicurezza: in passato era aperta a qualsiasi utente loggato (capability 'read'),
 * ma espone versioni software, lista plugin attivi e path — info utili a un attaccante
 * per profilare il sito. Ora richiede 'manage_options'.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Olobuild_Diagnostics {

    public static function init() {
        add_action( 'admin_menu', [ __CLASS__, 'register_page' ], 5 );
    }

    public static function register_page() {
        // Solo amministratori: la pagina espone informazioni di sistema (info-disclosure).
        add_submenu_page(
            'tools.php',
            __( 'Olobuild Diagnostics', 'olobuild' ),
            __( 'Olobuild Diagnostics', 'olobuild' ),
            'manage_options',
            'olo-diagnostics',
            [ __CLASS__, 'render_page' ]
        );
    }

    public static function render_page() {
        $user     = wp_get_current_user();
        $all_caps = $user->allcaps ?? [];
        $roles    = $user->roles ?? [];

        $caps_to_check = [
            'read', 'edit_posts', 'edit_pages', 'publish_posts',
            'manage_options', 'upload_files', 'switch_themes',
            'unfiltered_html', 'install_plugins', 'activate_plugins',
            'olo_edit_builder',
        ];

        $active_plugins = get_option( 'active_plugins', [] );
        $setup_complete = get_option( 'olo_setup_complete' );
        $activating     = get_transient( 'olo_activating' );

        $yes = __( 'Sì', 'olobuild' );
        $no  = __( 'No', 'olobuild' );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Olobuild Diagnostics', 'olobuild' ); ?></h1>
            <p style="color:#666"><?php esc_html_e( "Questa pagina serve solo per debug. Se stai vedendo questo contenuto, l'accesso funziona. Riporta queste informazioni per risolvere problemi di permessi.", 'olobuild' ); ?></p>

            <h2><?php esc_html_e( 'Utente corrente', 'olobuild' ); ?></h2>
            <table class="widefat striped" style="max-width:800px">
                <tr><td><strong><?php esc_html_e( 'User ID', 'olobuild' ); ?></strong></td><td><?php echo (int) $user->ID; ?></td></tr>
                <tr><td><strong><?php esc_html_e( 'Username', 'olobuild' ); ?></strong></td><td><?php echo esc_html( $user->user_login ); ?></td></tr>
                <tr><td><strong><?php esc_html_e( 'Email', 'olobuild' ); ?></strong></td><td><?php echo esc_html( $user->user_email ); ?></td></tr>
                <tr><td><strong><?php esc_html_e( 'Ruoli', 'olobuild' ); ?></strong></td><td><?php echo esc_html( implode( ', ', $roles ) ); ?></td></tr>
                <tr><td><strong><?php esc_html_e( 'Super admin?', 'olobuild' ); ?></strong></td><td><?php echo is_super_admin() ? esc_html( $yes ) : esc_html( $no ); ?></td></tr>
                <tr><td><strong><?php esc_html_e( 'Multisite?', 'olobuild' ); ?></strong></td><td><?php echo is_multisite() ? esc_html( $yes ) : esc_html( $no ); ?></td></tr>
            </table>

            <h2><?php esc_html_e( 'Verifica capability', 'olobuild' ); ?></h2>
            <table class="widefat striped" style="max-width:800px">
                <thead><tr><th><?php esc_html_e( 'Capability', 'olobuild' ); ?></th><th>current_user_can()</th></tr></thead>
                <tbody>
                <?php foreach ( $caps_to_check as $cap ): ?>
                    <tr>
                        <td><code><?php echo esc_html( $cap ); ?></code></td>
                        <td><?php echo current_user_can( $cap ) ? '<span style="color:#46b450">✓ ' . esc_html( $yes ) . '</span>' : '<span style="color:#dc3232">✗ ' . esc_html( $no ) . '</span>'; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <h2><?php esc_html_e( 'Stato Olobuild', 'olobuild' ); ?></h2>
            <table class="widefat striped" style="max-width:800px">
                <tr><td><strong>OLOBUILD_VERSION</strong></td><td><?php echo esc_html( defined( 'OLOBUILD_VERSION' ) ? OLOBUILD_VERSION : __( 'NON DEFINITO', 'olobuild' ) ); ?></td></tr>
                <tr><td><strong>OLOBUILD_PATH</strong></td><td><?php echo esc_html( defined( 'OLOBUILD_PATH' ) ? OLOBUILD_PATH : __( 'NON DEFINITO', 'olobuild' ) ); ?></td></tr>
                <tr><td><strong>olo_setup_complete</strong></td><td><?php echo $setup_complete ? esc_html( $yes ) . ' (' . esc_html( $setup_complete ) . ')' : esc_html( $no ); ?></td></tr>
                <tr><td><strong>olo_activating transient</strong></td><td><?php echo $activating ? esc_html( $yes ) : esc_html( $no ); ?></td></tr>
                <tr><td><strong>olo_builder_roles</strong></td><td><code><?php echo esc_html( wp_json_encode( get_option( 'olo_builder_roles' ) ) ); ?></code></td></tr>
            </table>

            <h2><?php esc_html_e( 'Plugin attivi', 'olobuild' ); ?></h2>
            <table class="widefat striped" style="max-width:800px">
                <?php foreach ( $active_plugins as $p ): ?>
                    <tr><td><code><?php echo esc_html( $p ); ?></code></td></tr>
                <?php endforeach; ?>
            </table>

            <h2><?php esc_html_e( 'Info WordPress', 'olobuild' ); ?></h2>
            <table class="widefat striped" style="max-width:800px">
                <tr><td><strong><?php esc_html_e( 'Versione WP', 'olobuild' ); ?></strong></td><td><?php echo esc_html( get_bloginfo( 'version' ) ); ?></td></tr>
                <tr><td><strong><?php esc_html_e( 'Versione PHP', 'olobuild' ); ?></strong></td><td><?php echo esc_html( PHP_VERSION ); ?></td></tr>
                <tr><td><strong><?php esc_html_e( 'Tema', 'olobuild' ); ?></strong></td><td><?php echo esc_html( get_stylesheet() ); ?></td></tr>
                <tr><td><strong><?php esc_html_e( 'URL sito', 'olobuild' ); ?></strong></td><td><?php echo esc_html( site_url() ); ?></td></tr>
                <tr><td><strong><?php esc_html_e( 'URL admin', 'olobuild' ); ?></strong></td><td><?php echo esc_html( admin_url() ); ?></td></tr>
            </table>

            <h2><?php esc_html_e( 'Test accesso pagine', 'olobuild' ); ?></h2>
            <p><?php esc_html_e( 'Clicca questi link per capire dove fallisce:', 'olobuild' ); ?></p>
            <ul>
                <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=olobuild' ) ); ?>">admin.php?page=olobuild (<?php esc_html_e( 'Dashboard', 'olobuild' ); ?>)</a></li>
                <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=olobuilder-templates' ) ); ?>">admin.php?page=olobuilder-templates (<?php esc_html_e( 'Gestione Template', 'olobuild' ); ?>)</a></li>
                <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=olobuilder-settings' ) ); ?>">admin.php?page=olobuilder-settings (<?php esc_html_e( 'Configurazione', 'olobuild' ); ?>)</a></li>
                <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=olo-setup' ) ); ?>">admin.php?page=olo-setup (<?php esc_html_e( 'Setup Wizard', 'olobuild' ); ?>)</a></li>
            </ul>
        </div>
        <?php
    }
}
