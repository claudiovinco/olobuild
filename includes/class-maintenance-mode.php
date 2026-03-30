<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Maintenance_Mode {

    /**
     * Initialize the maintenance mode hooks.
     */
    public static function init() {
        $instance = new self();
        add_action( 'template_redirect', [ $instance, 'maybe_show_maintenance' ], 1 );
    }

    /**
     * Check if maintenance mode is active and show the appropriate page.
     */
    public function maybe_show_maintenance() {
        $mode = get_option( 'olo_maintenance_mode', 'off' );

        if ( $mode === 'off' ) {
            return;
        }

        // Skip for admin area, REST API, AJAX, cron
        if ( is_admin() ) {
            return;
        }
        if ( defined( 'DOING_AJAX' ) ) {
            if ( DOING_AJAX ) {
                return;
            }
        }
        if ( defined( 'DOING_CRON' ) ) {
            if ( DOING_CRON ) {
                return;
            }
        }
        if ( defined( 'REST_REQUEST' ) ) {
            if ( REST_REQUEST ) {
                return;
            }
        }

        // Allow WP login page
        if ( $this->is_login_page() ) {
            return;
        }

        // Check bypass by user role
        if ( is_user_logged_in() ) {
            $bypass_roles = get_option( 'olo_maintenance_bypass_roles', [ 'administrator' ] );
            if ( ! is_array( $bypass_roles ) ) {
                $bypass_roles = [ 'administrator' ];
            }
            $user = wp_get_current_user();
            foreach ( $bypass_roles as $role ) {
                if ( in_array( $role, (array) $user->roles, true ) ) {
                    return;
                }
            }
        }

        // Check bypass secret URL parameter
        $bypass_secret = get_option( 'olo_maintenance_bypass_secret', '' );
        if ( ! empty( $bypass_secret ) ) {
            // If the secret is in the URL, set a bypass cookie for 24 hours
            if ( isset( $_GET['bypass'] ) ) {
                if ( sanitize_text_field( wp_unslash( $_GET['bypass'] ) ) === $bypass_secret ) {
                    setcookie( 'olo_maintenance_bypass', md5( $bypass_secret ), time() + DAY_IN_SECONDS, '/' );
                    return;
                }
            }
            // If the bypass cookie exists and is valid
            if ( isset( $_COOKIE['olo_maintenance_bypass'] ) ) {
                if ( $_COOKIE['olo_maintenance_bypass'] === md5( $bypass_secret ) ) {
                    return;
                }
            }
        }

        // Set appropriate HTTP status
        if ( $mode === 'maintenance' ) {
            status_header( 503 );
            header( 'Retry-After: 3600' );
        } else {
            // coming_soon
            status_header( 200 );
        }

        // Try to render the Olobuild template
        $template_id = (int) get_option( 'olo_maintenance_template_id', 0 );

        if ( $template_id ) {
            $this->render_template( $template_id );
        } else {
            $this->render_default_page( $mode );
        }

        die();
    }

    /**
     * Render an Olobuild template for the maintenance/coming soon page.
     */
    private function render_template( $template_id ) {
        $db       = new Olo_Database();
        $template = $db->get_template( $template_id );

        if ( ! $template ) {
            $this->render_default_page( get_option( 'olo_maintenance_mode', 'maintenance' ) );
            return;
        }

        $renderer   = new Olo_Frontend_Renderer();
        $inner_html = $renderer->render_shortcode( [ 'id' => $template_id ] );
        ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?php
        if ( get_option( 'olo_maintenance_mode' ) === 'coming_soon' ) {
            echo esc_html( get_bloginfo( 'name' ) . ' — Presto online' );
        } else {
            echo esc_html( get_bloginfo( 'name' ) . ' — Manutenzione' );
        }
    ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class( 'olo-maintenance-page' ); ?>>
    <?php
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo $inner_html;
    ?>
    <?php wp_footer(); ?>
</body>
</html>
        <?php
    }

    /**
     * Render a default minimal maintenance/coming soon page.
     */
    private function render_default_page( $mode ) {
        $site_name = esc_html( get_bloginfo( 'name' ) );
        $logo_url  = '';

        // Try to get the custom logo from the customizer
        $custom_logo_id = get_theme_mod( 'custom_logo' );
        if ( $custom_logo_id ) {
            $logo_data = wp_get_attachment_image_src( $custom_logo_id, 'medium' );
            if ( $logo_data ) {
                $logo_url = esc_url( $logo_data[0] );
            }
        }

        if ( $mode === 'coming_soon' ) {
            $title    = 'Presto online';
            $subtitle = 'Stiamo lavorando per creare qualcosa di straordinario.';
        } else {
            $title    = 'Sito in manutenzione';
            $subtitle = 'Stiamo lavorando per migliorare la tua esperienza. Torna a trovarci presto!';
        }

        ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo esc_html( $site_name . ' — ' . $title ); ?></title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            height: 100%;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #e0e0e0;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            overflow: hidden;
        }
        .olo-maint-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 40px 20px;
            text-align: center;
        }
        .olo-maint-logo {
            max-width: 180px;
            max-height: 80px;
            margin-bottom: 40px;
            object-fit: contain;
        }
        .olo-maint-icon {
            width: 80px;
            height: 80px;
            margin-bottom: 32px;
            opacity: 0.7;
        }
        .olo-maint-title {
            font-size: clamp(28px, 5vw, 48px);
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 16px;
            background: linear-gradient(135deg, #ffffff 0%, #a0a0c0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .olo-maint-subtitle {
            font-size: clamp(16px, 2.5vw, 20px);
            font-weight: 400;
            color: #a0a0b8;
            max-width: 500px;
            line-height: 1.6;
            margin-bottom: 48px;
        }
        .olo-maint-dots {
            display: flex;
            gap: 8px;
        }
        .olo-maint-dots span {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            animation: olo-maint-pulse 1.4s ease-in-out infinite;
        }
        .olo-maint-dots span:nth-child(2) { animation-delay: 0.2s; }
        .olo-maint-dots span:nth-child(3) { animation-delay: 0.4s; }
        @keyframes olo-maint-pulse {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.3); }
        }
    </style>
</head>
<body>
    <div class="olo-maint-container">
        <?php if ( $logo_url ) : ?>
            <img class="olo-maint-logo" src="<?php echo $logo_url; ?>" alt="<?php echo $site_name; ?>" />
        <?php else : ?>
            <svg class="olo-maint-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <?php if ( $mode === 'coming_soon' ) : ?>
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                <?php else : ?>
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                <?php endif; ?>
            </svg>
        <?php endif; ?>

        <h1 class="olo-maint-title"><?php echo esc_html( $title ); ?></h1>
        <p class="olo-maint-subtitle"><?php echo esc_html( $subtitle ); ?></p>

        <div class="olo-maint-dots">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</body>
</html>
        <?php
    }

    /**
     * Check if the current request is for the WP login page.
     */
    private function is_login_page() {
        $script = isset( $_SERVER['SCRIPT_NAME'] ) ? basename( $_SERVER['SCRIPT_NAME'] ) : '';
        if ( $script === 'wp-login.php' ) {
            return true;
        }
        if ( $script === 'wp-register.php' ) {
            return true;
        }
        return false;
    }
}
