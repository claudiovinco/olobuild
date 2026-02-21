<?php
/**
 * Olo Booking — Manager Frontend Page
 *
 * Registra l'URL /gestione/ e carica la SPA Vue.js per i gestori.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Manager_Page {

    public function init() {
        add_action( 'init', [ $this, 'add_rewrite_rules' ] );
        add_filter( 'query_vars', [ $this, 'add_query_vars' ] );
        add_action( 'template_redirect', [ $this, 'handle_template' ] );
    }

    public function add_rewrite_rules() {
        add_rewrite_rule( '^gestione/?$', 'index.php?olo_manager_page=1', 'top' );
        add_rewrite_rule( '^gestione/(.+)/?$', 'index.php?olo_manager_page=1', 'top' );
    }

    public function add_query_vars( $vars ) {
        $vars[] = 'olo_manager_page';
        return $vars;
    }

    public function handle_template() {
        if ( ! get_query_var( 'olo_manager_page' ) ) return;

        // Handle login POST
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['olo_manager_login'] ) ) {
            $this->process_login();
            return;
        }

        // Handle logout
        if ( isset( $_GET['olo_logout'] ) ) {
            wp_logout();
            wp_safe_redirect( home_url( '/gestione/' ) );
            exit;
        }

        // If not logged in, show login page
        if ( ! is_user_logged_in() ) {
            $this->render_login_page();
            exit;
        }

        // If logged in but no permissions
        $user = wp_get_current_user();
        $has_access = Olo_Role_Manager::can_access_panel();
        if ( ! $has_access ) {
            $this->render_error_page( 'Accesso non autorizzato', 'Il tuo account non ha i permessi per accedere al pannello di gestione strutture.' );
            exit;
        }

        // Render the SPA
        $this->render_manager_app();
        exit;
    }

    private function process_login() {
        if ( ! wp_verify_nonce( $_POST['_wpnonce'] ?? '', 'olo_manager_login' ) ) {
            $this->render_login_page( 'Errore di sicurezza. Riprova.' );
            exit;
        }

        $creds = [
            'user_login'    => sanitize_user( $_POST['log'] ?? '' ),
            'user_password' => $_POST['pwd'] ?? '',
            'remember'      => ! empty( $_POST['rememberme'] ),
        ];

        $user = wp_signon( $creds, is_ssl() );

        if ( is_wp_error( $user ) ) {
            $this->render_login_page( 'Credenziali non valide. Riprova.' );
            exit;
        }

        wp_set_current_user( $user->ID );

        $has_access = Olo_Role_Manager::can_access_panel( $user->ID );
        if ( ! $has_access ) {
            wp_logout();
            $this->render_login_page( 'Il tuo account non ha accesso al pannello di gestione.' );
            exit;
        }

        wp_safe_redirect( home_url( '/gestione/' ) );
        exit;
    }

    /* =========================================================================
     *  Render Pages
     * ======================================================================= */

    private function render_login_page( $error = '' ) {
        status_header( 200 );
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo( 'charset' ); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Gestione Strutture — <?php bloginfo( 'name' ); ?></title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .login-card {
                    background: #fff;
                    border-radius: 16px;
                    padding: 40px;
                    width: 100%;
                    max-width: 400px;
                    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
                }
                .login-logo {
                    text-align: center;
                    margin-bottom: 8px;
                }
                .login-logo img {
                    height: 36px;
                }
                .login-subtitle {
                    text-align: center;
                    color: #6B7280;
                    font-size: 14px;
                    margin-bottom: 28px;
                }
                .login-error {
                    background: #FEF2F2;
                    color: #DC2626;
                    padding: 10px 14px;
                    border-radius: 8px;
                    font-size: 13px;
                    margin-bottom: 16px;
                    border: 1px solid #FECACA;
                }
                .login-field {
                    margin-bottom: 16px;
                }
                .login-field label {
                    display: block;
                    font-size: 13px;
                    font-weight: 600;
                    color: #374151;
                    margin-bottom: 4px;
                }
                .login-field input[type="text"],
                .login-field input[type="password"] {
                    width: 100%;
                    padding: 10px 14px;
                    border: 1px solid #D1D5DB;
                    border-radius: 8px;
                    font-size: 15px;
                    transition: border-color 0.2s, box-shadow 0.2s;
                    outline: none;
                }
                .login-field input:focus {
                    border-color: #6366F1;
                    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
                }
                .login-remember {
                    display: flex;
                    align-items: center;
                    gap: 6px;
                    margin-bottom: 20px;
                    font-size: 13px;
                    color: #6B7280;
                }
                .login-remember input { accent-color: #6366F1; }
                .login-btn {
                    width: 100%;
                    padding: 12px;
                    background: #6366F1;
                    color: #fff;
                    border: none;
                    border-radius: 8px;
                    font-size: 15px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: background 0.2s;
                }
                .login-btn:hover { background: #4F46E5; }
                .login-footer {
                    text-align: center;
                    margin-top: 20px;
                    font-size: 12px;
                    color: #9CA3AF;
                }
                .login-footer a { color: #6366F1; text-decoration: none; }
            </style>
        </head>
        <body>
            <div class="login-card">
                <div class="login-logo">
                    <img src="<?php echo esc_url( plugins_url( '../assets/img/olobuild-logo-200.png', __FILE__ ) ); ?>" alt="Olobuild" onerror="this.outerHTML='<strong style=\'font-size:24px;color:#6366F1\'>Olo Booking</strong>'" />
                </div>
                <p class="login-subtitle">Pannello Gestione Strutture</p>
                <?php if ( $error ) : ?>
                    <div class="login-error"><?php echo esc_html( $error ); ?></div>
                <?php endif; ?>
                <form method="post" action="<?php echo esc_url( home_url( '/gestione/' ) ); ?>">
                    <?php wp_nonce_field( 'olo_manager_login' ); ?>
                    <input type="hidden" name="olo_manager_login" value="1" />
                    <div class="login-field">
                        <label for="log">Nome utente o email</label>
                        <input type="text" name="log" id="log" autocomplete="username" required />
                    </div>
                    <div class="login-field">
                        <label for="pwd">Password</label>
                        <input type="password" name="pwd" id="pwd" autocomplete="current-password" required />
                    </div>
                    <label class="login-remember">
                        <input type="checkbox" name="rememberme" value="forever" /> Ricordami
                    </label>
                    <button type="submit" class="login-btn">Accedi</button>
                </form>
                <div class="login-footer">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>">&larr; Torna al sito</a>
                </div>
            </div>
        </body>
        </html>
        <?php
    }

    private function render_error_page( $title, $message ) {
        status_header( 403 );
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo( 'charset' ); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title><?php echo esc_html( $title ); ?></title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    background: #F3F4F6;
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .error-card {
                    background: #fff;
                    border-radius: 16px;
                    padding: 40px;
                    max-width: 400px;
                    text-align: center;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
                }
                .error-icon { font-size: 48px; margin-bottom: 16px; }
                .error-card h2 { color: #DC2626; margin-bottom: 8px; }
                .error-card p { color: #6B7280; font-size: 14px; margin-bottom: 20px; }
                .error-card a { color: #6366F1; text-decoration: none; font-weight: 600; }
            </style>
        </head>
        <body>
            <div class="error-card">
                <div class="error-icon">🔒</div>
                <h2><?php echo esc_html( $title ); ?></h2>
                <p><?php echo esc_html( $message ); ?></p>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">&larr; Torna al sito</a>
            </div>
        </body>
        </html>
        <?php
    }

    private function render_manager_app() {
        $user = wp_get_current_user();

        // WordPress media library (for video picker)
        wp_enqueue_media();

        // Google Fonts
        wp_enqueue_style(
            'olo-google-fonts',
            'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap',
            [],
            null
        );

        // Enqueue the Vue SPA assets
        wp_enqueue_style(
            'olo-manager-app',
            OLO_BOOK_URL . 'assets/css/manager-app.css',
            [ 'olo-google-fonts' ],
            OLO_BOOK_VERSION
        );
        wp_enqueue_script(
            'olo-manager-app',
            OLO_BOOK_URL . 'assets/js/manager-app.js',
            [],
            OLO_BOOK_VERSION,
            true
        );

        // Pass config to JS
        $access_level = Olo_Role_Manager::get_access_level();
        wp_localize_script( 'olo-manager-app', 'oloManagerConfig', [
            'restUrl'    => esc_url_raw( rest_url( 'olo-booking/v2' ) ),
            'nonce'      => wp_create_nonce( 'wp_rest' ),
            'homeUrl'    => home_url( '/' ),
            'managerUrl' => home_url( '/gestione/' ),
            'logoutUrl'  => home_url( '/gestione/?olo_logout=1' ),
            'user'       => [
                'id'              => $user->ID,
                'name'            => $user->display_name,
                'email'           => $user->user_email,
                'is_admin'        => $access_level === 'admin',
                'access_level'    => $access_level,
                'can_filter_type' => Olo_Role_Manager::can( 'filter_service_type' ),
                'permissions'     => Olo_Role_Manager::get_user_permissions(),
            ],
            'mediaUploadEnabled' => current_user_can( 'upload_files' ),
        ] );

        status_header( 200 );
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo( 'charset' ); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Gestione Strutture — <?php bloginfo( 'name' ); ?></title>
            <?php wp_head(); ?>
        </head>
        <body class="olo-manager-body">
            <div id="olo-manager-app"></div>
            <?php wp_footer(); ?>
        </body>
        </html>
        <?php
    }
}
