<?php
/**
 * Olobuild Diagnostics — permission-free debug page.
 * Accessible to any logged-in user at: /wp-admin/admin.php?page=olo-diagnostics
 * Used only for troubleshooting access issues.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Olo_Diagnostics {

    public static function init() {
        add_action( 'admin_menu', [ __CLASS__, 'register_page' ], 5 );
    }

    public static function register_page() {
        // Registered with the lowest capability 'read' so it's always accessible to logged-in users.
        add_submenu_page(
            'tools.php',
            'Olobuild Diagnostics',
            'Olobuild Diagnostics',
            'read',
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

        ?>
        <div class="wrap">
            <h1>Olobuild Diagnostics</h1>
            <p style="color:#666">Questa pagina serve solo per debug. Se stai vedendo questo contenuto, l'accesso funziona. Riporta queste informazioni per risolvere problemi di permessi.</p>

            <h2>Utente corrente</h2>
            <table class="widefat striped" style="max-width:800px">
                <tr><td><strong>User ID</strong></td><td><?php echo (int) $user->ID; ?></td></tr>
                <tr><td><strong>Username</strong></td><td><?php echo esc_html( $user->user_login ); ?></td></tr>
                <tr><td><strong>Email</strong></td><td><?php echo esc_html( $user->user_email ); ?></td></tr>
                <tr><td><strong>Roles</strong></td><td><?php echo esc_html( implode( ', ', $roles ) ); ?></td></tr>
                <tr><td><strong>Is super admin?</strong></td><td><?php echo is_super_admin() ? 'Yes' : 'No'; ?></td></tr>
                <tr><td><strong>Is multisite?</strong></td><td><?php echo is_multisite() ? 'Yes' : 'No'; ?></td></tr>
            </table>

            <h2>Capability check</h2>
            <table class="widefat striped" style="max-width:800px">
                <thead><tr><th>Capability</th><th>current_user_can()</th></tr></thead>
                <tbody>
                <?php foreach ( $caps_to_check as $cap ): ?>
                    <tr>
                        <td><code><?php echo esc_html( $cap ); ?></code></td>
                        <td><?php echo current_user_can( $cap ) ? '<span style="color:#46b450">✓ Yes</span>' : '<span style="color:#dc3232">✗ No</span>'; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <h2>Olobuild state</h2>
            <table class="widefat striped" style="max-width:800px">
                <tr><td><strong>OLO_VERSION</strong></td><td><?php echo esc_html( defined( 'OLO_VERSION' ) ? OLO_VERSION : 'NOT DEFINED' ); ?></td></tr>
                <tr><td><strong>OLO_PATH</strong></td><td><?php echo esc_html( defined( 'OLO_PATH' ) ? OLO_PATH : 'NOT DEFINED' ); ?></td></tr>
                <tr><td><strong>olo_setup_complete</strong></td><td><?php echo $setup_complete ? 'Yes (' . esc_html( $setup_complete ) . ')' : 'No'; ?></td></tr>
                <tr><td><strong>olo_activating transient</strong></td><td><?php echo $activating ? 'Yes' : 'No'; ?></td></tr>
                <tr><td><strong>olo_builder_roles</strong></td><td><code><?php echo esc_html( wp_json_encode( get_option( 'olo_builder_roles' ) ) ); ?></code></td></tr>
            </table>

            <h2>Active plugins</h2>
            <table class="widefat striped" style="max-width:800px">
                <?php foreach ( $active_plugins as $p ): ?>
                    <tr><td><code><?php echo esc_html( $p ); ?></code></td></tr>
                <?php endforeach; ?>
            </table>

            <h2>WordPress info</h2>
            <table class="widefat striped" style="max-width:800px">
                <tr><td><strong>WP version</strong></td><td><?php echo esc_html( get_bloginfo( 'version' ) ); ?></td></tr>
                <tr><td><strong>PHP version</strong></td><td><?php echo esc_html( PHP_VERSION ); ?></td></tr>
                <tr><td><strong>Theme</strong></td><td><?php echo esc_html( get_stylesheet() ); ?></td></tr>
                <tr><td><strong>Site URL</strong></td><td><?php echo esc_html( site_url() ); ?></td></tr>
                <tr><td><strong>Admin URL</strong></td><td><?php echo esc_html( admin_url() ); ?></td></tr>
            </table>

            <h2>Test page access</h2>
            <p>Clicca questi link per capire dove fallisce:</p>
            <ul>
                <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=olobuilder' ) ); ?>">admin.php?page=olobuilder (Avvio rapido)</a></li>
                <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=olobuilder-templates' ) ); ?>">admin.php?page=olobuilder-templates (Gestione Template)</a></li>
                <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=olobuilder-settings' ) ); ?>">admin.php?page=olobuilder-settings (Configurazione)</a></li>
                <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=olo-setup' ) ); ?>">admin.php?page=olo-setup (Setup Wizard)</a></li>
            </ul>
        </div>
        <?php
    }
}
