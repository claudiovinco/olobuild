<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Loginform_Tile extends Olo_Tile_Base {

    protected $type     = 'loginform';
    protected $name     = 'Login / Registrazione';
    protected $icon     = 'dashicons-lock';
    protected $category = 'marketing';
    protected $defaults = [
        'preset' => 'custom',
        'mode'                 => 'login',
        'redirect_url'         => '',
        'show_remember_me'     => true,
        'show_lost_password'   => true,
        'show_avatar'          => true,
        'logged_in_message'    => 'Bentornato!',
        'logged_out_redirect'  => '',

        // Titoli
        'login_title'          => 'Bentornato',
        'login_subtitle'       => 'Accedi al tuo account',
        'register_title'       => 'Crea un account',
        'register_subtitle'    => 'Registrati in pochi secondi',
        'login_button_text'    => 'Accedi',
        'register_button_text' => 'Registrati',

        // Visual
        'show_input_icons'      => true,
        'show_password_toggle'  => true,
        'show_password_strength' => true,
        'password_min_length'    => 8,
        'password_require_uppercase' => false,
        'password_require_number'    => false,
        'password_require_special'   => false,
        'password_min_strength'      => 0,
        'tab_style'             => 'underline',
        'form_padding'          => '32',

        // Terms
        'show_terms'            => false,
        'terms_text'            => 'Accetto i Termini e le Condizioni',
        'terms_url'             => '',

        // Social
        'show_social_divider'   => false,
        'social_divider_text'   => 'oppure',
        'social_google'         => false,
        'social_facebook'       => false,
        'social_apple'          => false,
        'social_google_url'     => '#',
        'social_facebook_url'   => '#',
        'social_apple_url'      => '#',

        // Register fields
        'register_fields'       => [],

        // Stile
        'form_bg'              => '',
        'text_color'           => '',
        'label_color'          => '',
        'input_bg'             => '',
        'input_color'          => '',
        'input_border_color'   => '',
        'input_focus_color'    => '',
        'input_padding'        => '11',
        'input_radius'         => '8',
        'submit_bg'            => '',
        'submit_color'         => '#FFFFFF',
        'submit_radius'        => '8',
        'submit_hover_bg'      => '',
        'link_color'           => '',
        'icon_color'           => '',
        'border_radius'        => '12',
        'border_width'         => '0',
        'border_color'         => '',
            'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-login-' . wp_unique_id();

        // Colors
        $form_bg          = $this->safe_color_css( $s['form_bg'] );
        $text_color       = $this->safe_color_css( $s['text_color'] );
        $label_color      = $this->safe_color_css( $s['label_color'] );
        $input_bg         = $this->safe_color_css( $s['input_bg'] );
        $input_color      = $this->safe_color_css( $s['input_color'] );
        $input_border     = $this->safe_color_css( $s['input_border_color'] );
        $input_focus      = $this->safe_color_css( $s['input_focus_color'] );
        $input_padding    = max( 4, intval( $s['input_padding'] ) ?: 11 );
        $input_radius     = Olo_Tile_Utils::border_radius( $s['input_radius'] ?? 0 );
        $input_radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['input_radius_hover'] ?? null );
        $submit_bg        = $this->safe_color_css( $s['submit_bg'] );
        $submit_color     = $this->safe_color_css( $s['submit_color'] );
        $submit_radius    = Olo_Tile_Utils::border_radius( $s['submit_radius'] ?? 0 );
        $submit_radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['submit_radius_hover'] ?? null );
        $submit_hover_bg  = $this->safe_color_css( $s['submit_hover_bg'] );
        $link_color_css   = $this->safe_color_css( $s['link_color'] );
        $icon_color_css   = $this->safe_color_css( $s['icon_color'] );
        $form_radius      = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $form_radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );
        $form_bw          = intval( $s['border_width'] );
        $form_bc          = $this->safe_color_css( $s['border_color'] );
        $form_pad = Olo_Tile_Utils::spacing_css( $s['tile_padding'] ?? $s['form_padding'] ?? 32, 32 );

        $submit_bg_val    = $submit_bg ?: 'var(--olo-color-primary, #e1474f)';
        $text_color_val   = $text_color ?: 'var(--olo-color-text, #374151)';
        $label_color_val  = $label_color ?: $text_color_val;
        $input_bg_val     = $input_bg ?: 'var(--olo-color-background, #FFFFFF)';
        $input_color_val  = $input_color ?: 'var(--olo-color-text, #374151)';
        $input_border_val = $input_border ?: 'var(--olo-color-border, #E5E7EB)';
        $input_focus_val  = $input_focus ?: $submit_bg_val;
        $link_color_val   = $link_color_css ?: $submit_bg_val;
        $icon_color_val   = $icon_color_css ?: 'var(--olo-color-text-muted, #9CA3AF)';
        $submit_hover_val = $submit_hover_bg ?: '';

        $mode             = in_array( $s['mode'], [ 'login', 'register', 'both' ], true ) ? $s['mode'] : 'login';
        $show_remember    = ! empty( $s['show_remember_me'] );
        $show_lost_pw     = ! empty( $s['show_lost_password'] );
        $show_avatar      = ! empty( $s['show_avatar'] );
        $show_icons       = ! empty( $s['show_input_icons'] );
        $show_pw_toggle   = ! empty( $s['show_password_toggle'] );
        $show_pw_strength = ! empty( $s['show_password_strength'] );
        $show_terms       = ! empty( $s['show_terms'] );
        $show_social      = ! empty( $s['show_social_divider'] );
        $tab_style        = in_array( $s['tab_style'], [ 'underline', 'pill', 'classic' ], true ) ? $s['tab_style'] : 'underline';

        $pw_min_length    = max( 1, intval( $s['password_min_length'] ) ?: 8 );
        $pw_req_upper     = ! empty( $s['password_require_uppercase'] );
        $pw_req_number    = ! empty( $s['password_require_number'] );
        $pw_req_special   = ! empty( $s['password_require_special'] );
        $pw_min_strength  = max( 0, min( 4, intval( $s['password_min_strength'] ) ) );

        $logged_in_msg    = esc_html( $s['logged_in_message'] ?: 'Bentornato!' );
        $login_title      = esc_html( $s['login_title'] ?: '' );
        $login_subtitle   = esc_html( $s['login_subtitle'] ?: '' );
        $register_title   = esc_html( $s['register_title'] ?: '' );
        $register_subtitle = esc_html( $s['register_subtitle'] ?: '' );
        $login_btn_text   = esc_html( $s['login_button_text'] ?: 'Accedi' );
        $register_btn_text = esc_html( $s['register_button_text'] ?: 'Registrati' );
        $terms_text       = esc_html( $s['terms_text'] ?: 'Accetto i Termini e le Condizioni' );
        $terms_url        = esc_url( $s['terms_url'] ?: '' );
        $social_div_text  = esc_html( $s['social_divider_text'] ?: 'oppure' );

        $redirect_url     = ! empty( $s['redirect_url'] ) ? esc_url( $s['redirect_url'] ) : '';
        $logout_redirect  = ! empty( $s['logged_out_redirect'] ) ? esc_url( $s['logged_out_redirect'] ) : '';

        $nonce            = wp_create_nonce( 'olo_loginform_' . $uid );

        // Register fields
        $register_fields = [];
        if ( ! empty( $s['register_fields'] ) ) {
            if ( is_array( $s['register_fields'] ) ) {
                $register_fields = $s['register_fields'];
            } elseif ( is_string( $s['register_fields'] ) ) {
                $decoded = json_decode( $s['register_fields'], true );
                if ( is_array( $decoded ) ) {
                    $register_fields = $decoded;
                }
            }
        }
        // Fallback: se vuoto, usa i campi built-in di default
        if ( empty( $register_fields ) ) {
            $register_fields = [
                [ 'label' => 'Nome utente', 'field_type' => 'username', 'placeholder' => 'Scegli un nome utente', 'required' => true, 'width' => '100' ],
                [ 'label' => 'Email', 'field_type' => 'user_email', 'placeholder' => 'nome@esempio.it', 'required' => true, 'width' => '100' ],
                [ 'label' => 'Password', 'field_type' => 'user_password', 'placeholder' => 'Min. 8 caratteri', 'required' => true, 'width' => '100' ],
            ];
        }

        // Social providers
        $has_google   = ! empty( $s['social_google'] );
        $has_facebook = ! empty( $s['social_facebook'] );
        $has_apple    = ! empty( $s['social_apple'] );
        $has_social   = $show_social && ( $has_google || $has_facebook || $has_apple );

        // SVG icons
        $icon_user = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
        $icon_lock = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>';
        $icon_mail = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>';
        $icon_eye  = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
        $icon_eye_off = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                padding: <?php echo $form_pad; ?>;
                border-radius: <?php echo $form_radius; ?>;
                <?php if ( $form_bg ) : ?>background-color: <?php echo $form_bg; ?>;<?php endif; ?>
                <?php if ( $form_bw > 0 ) : ?>border: <?php echo $form_bw; ?>px solid <?php echo $form_bc ?: $input_border_val; ?>;<?php endif; ?>
            }
            <?php if ( $form_radius_hover_css !== '' ) : ?>.<?php echo $uid; ?>{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?>:hover{border-radius:<?php echo $form_radius_hover_css; ?> !important}<?php endif; ?>
            /* Header */
            .<?php echo $uid; ?> .olo-lf-header {
                text-align: center; margin-bottom: 24px;
            }
            .<?php echo $uid; ?> .olo-lf-title {
                font-size: 22px; font-weight: 700; line-height: 1.3;
                color: <?php echo $text_color_val; ?>;
            }
            .<?php echo $uid; ?> .olo-lf-subtitle {
                font-size: 14px; margin-top: 6px; opacity: 0.6;
                color: <?php echo $text_color_val; ?>;
            }
            /* Labels */
            .<?php echo $uid; ?> .olo-lf-label {
                display: block; margin-bottom: 6px; font-size: 13px; font-weight: 600;
                color: <?php echo $label_color_val; ?>;
            }
            /* Input wrapper */
            .<?php echo $uid; ?> .olo-lf-input-wrap {
                display: flex; align-items: center;
                background-color: <?php echo $input_bg_val; ?>;
                border: 1px solid <?php echo $input_border_val; ?>;
                border-radius: <?php echo $input_radius; ?>;
                overflow: hidden; transition: border-color 0.2s;
            }
            <?php if ( $input_radius_hover_css !== '' ) : ?>.<?php echo $uid; ?> .olo-lf-input-wrap{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?> .olo-lf-input-wrap:hover{border-radius:<?php echo $input_radius_hover_css; ?> !important}<?php endif; ?>
            .<?php echo $uid; ?> .olo-lf-input-wrap:focus-within {
                border-color: <?php echo $input_focus_val; ?>;
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 25%, transparent);
            }
            .<?php echo $uid; ?> .olo-lf-icon {
                display: flex; align-items: center; padding: 0 0 0 14px;
                color: <?php echo $icon_color_val; ?>; flex-shrink: 0;
            }
            .<?php echo $uid; ?> .olo-lf-input {
                display: block; width: 100%; box-sizing: border-box;
                padding: <?php echo $input_padding; ?>px 14px <?php echo $show_icons ? $input_padding . 'px 0' : $input_padding . 'px 14px'; ?>;
                font-size: 14px; font-family: inherit;
                background: transparent;
                color: <?php echo $input_color_val; ?>;
                border: none; outline: none;
            }
            .<?php echo $uid; ?> .olo-lf-pw-toggle {
                display: flex; align-items: center; padding: 0 12px;
                background: none; border: none; cursor: pointer;
                color: <?php echo $icon_color_val; ?>; flex-shrink: 0;
            }
            .<?php echo $uid; ?> .olo-lf-pw-toggle .olo-eye-off { display: none; }
            .<?php echo $uid; ?> .olo-lf-pw-toggle.olo-showing .olo-eye-on { display: none; }
            .<?php echo $uid; ?> .olo-lf-pw-toggle.olo-showing .olo-eye-off { display: block; }
            /* Standalone inputs (select, textarea) */
            .<?php echo $uid; ?> select.olo-lf-input-standalone,
            .<?php echo $uid; ?> textarea.olo-lf-input-standalone {
                display: block; width: 100%; box-sizing: border-box;
                padding: <?php echo $input_padding; ?>px 14px; font-size: 14px; font-family: inherit;
                background-color: <?php echo $input_bg_val; ?>;
                color: <?php echo $input_color_val; ?>;
                border: 1px solid <?php echo $input_border_val; ?>;
                border-radius: <?php echo $input_radius; ?>;
                outline: none; transition: border-color 0.2s;
            }
            .<?php echo $uid; ?> select.olo-lf-input-standalone:focus,
            .<?php echo $uid; ?> textarea.olo-lf-input-standalone:focus {
                border-color: <?php echo $input_focus_val; ?>;
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 25%, transparent);
            }
            .<?php echo $uid; ?> textarea.olo-lf-input-standalone {
                min-height: 72px; resize: vertical;
            }
            /* Fields */
            .<?php echo $uid; ?> .olo-lf-field { margin-bottom: 16px; }
            /* Password strength */
            .<?php echo $uid; ?> .olo-lf-strength { margin: 8px 0 16px; }
            .<?php echo $uid; ?> .olo-lf-strength-bars { display: flex; gap: 4px; }
            .<?php echo $uid; ?> .olo-lf-strength-bar {
                flex: 1; height: 4px; border-radius: 2px;
                background: <?php echo $input_border_val; ?>; transition: background 0.3s;
            }
            .<?php echo $uid; ?> .olo-lf-strength-text {
                font-size: 11px; margin-top: 4px;
                color: <?php echo $text_color_val; ?>; opacity: 0.6;
            }
            .<?php echo $uid; ?>[data-pw-str="1"] .olo-lf-strength-bar:nth-child(1) { background: var(--olo-color-danger, #ef4444); }
            .<?php echo $uid; ?>[data-pw-str="2"] .olo-lf-strength-bar:nth-child(-n+2) { background: var(--olo-color-warning, #b45309); }
            .<?php echo $uid; ?>[data-pw-str="3"] .olo-lf-strength-bar:nth-child(-n+3) { background: var(--olo-color-success, #15803d); }
            .<?php echo $uid; ?>[data-pw-str="4"] .olo-lf-strength-bar { background: var(--olo-color-success, #15803d); }
            /* Submit */
            .<?php echo $uid; ?> .olo-lf-submit {
                display: block; width: 100%; padding: 12px 24px;
                font-size: 15px; font-weight: 600; font-family: inherit;
                background-color: <?php echo $submit_bg_val; ?>;
                color: <?php echo $submit_color ?: 'var(--olo-color-primary-contrast, #FFFFFF)'; ?>;
                border: none; border-radius: <?php echo $submit_radius; ?>;
                cursor: pointer; text-align: center;
                transition: background-color 0.2s, transform 0.15s; margin-top: 16px;
            }
            <?php if ( $submit_radius_hover_css !== '' ) : ?>.<?php echo $uid; ?> .olo-lf-submit{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?> .olo-lf-submit:hover{border-radius:<?php echo $submit_radius_hover_css; ?> !important}<?php endif; ?>
            .<?php echo $uid; ?> .olo-lf-submit:hover {
                <?php if ( $submit_hover_val ) : ?>background-color: <?php echo $submit_hover_val; ?>;
                <?php else : ?>opacity: 0.9;<?php endif; ?>
                transform: translateY(-1px);
            }
            .<?php echo $uid; ?> .olo-lf-submit:focus-visible { outline: none; box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent); }
            .<?php echo $uid; ?> .olo-lf-pw-toggle:focus-visible { outline: none; border-radius: 4px; box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent); }
            .<?php echo $uid; ?> .olo-lf-submit:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
            /* Links */
            .<?php echo $uid; ?> .olo-lf-link {
                color: <?php echo $link_color_val; ?>; text-decoration: none; font-size: 13px; font-weight: 500;
            }
            .<?php echo $uid; ?> .olo-lf-link:hover { text-decoration: underline; }
            .<?php echo $uid; ?> .olo-lf-link:focus-visible,
            .<?php echo $uid; ?> .olo-lf-switch a:focus-visible { outline: none; border-radius: 3px; box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent); }
            /* Remember + lost password row */
            .<?php echo $uid; ?> .olo-lf-row {
                display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;
            }
            .<?php echo $uid; ?> .olo-lf-remember {
                display: flex; align-items: center; gap: 8px; font-size: 13px;
                color: <?php echo $text_color_val; ?>; cursor: pointer;
            }
            .<?php echo $uid; ?> .olo-lf-remember input { accent-color: <?php echo $submit_bg_val; ?>; }
            /* Tabs */
            <?php if ( $mode === 'both' ) : ?>
            .<?php echo $uid; ?> .olo-lf-tabs {
                display: flex; margin-bottom: 24px;
                <?php if ( $tab_style === 'pill' ) : ?>
                background: <?php echo $input_bg ?: 'var(--olo-color-muted, #F3F4F6)'; ?>;
                border-radius: <?php echo $input_radius; ?>; padding: 4px; gap: 4px;
                <?php else : ?>
                gap: 0; border-bottom: 2px solid <?php echo $input_border_val; ?>;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-lf-tab {
                flex: 1; padding: 10px 16px; font-size: 14px; font-weight: 500;
                font-family: inherit; border: none; cursor: pointer; text-align: center;
                transition: all 0.2s; background: transparent;
                color: <?php echo $text_color_val; ?>;
                <?php if ( $tab_style === 'underline' ) : ?>
                border-bottom: 2px solid transparent; margin-bottom: -2px;
                <?php elseif ( $tab_style === 'pill' ) : ?>
                border-radius: calc(<?php echo $input_radius; ?> - 2px);
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-lf-tab:hover { opacity: 0.8; }
            .<?php echo $uid; ?> .olo-lf-tab:focus-visible { outline: none; box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent); }
            .<?php echo $uid; ?> .olo-lf-tab.active {
                font-weight: 600;
                <?php if ( $tab_style === 'underline' ) : ?>
                color: <?php echo $submit_bg_val; ?>; border-bottom-color: <?php echo $submit_bg_val; ?>;
                <?php elseif ( $tab_style === 'pill' ) : ?>
                background-color: <?php echo $submit_bg_val; ?>;
                color: <?php echo $submit_color ?: 'var(--olo-color-primary-contrast, #FFFFFF)'; ?>;
                <?php else : /* classic */ ?>
                background-color: <?php echo $submit_bg_val; ?>;
                color: <?php echo $submit_color ?: 'var(--olo-color-primary-contrast, #FFFFFF)'; ?>;
                border-radius: <?php echo $input_radius; ?> <?php echo $input_radius; ?> 0 0;
                <?php endif; ?>
            }
            <?php endif; ?>
            /* Logged in */
            .<?php echo $uid; ?> .olo-lf-loggedin { text-align: center; padding: 20px 0; }
            .<?php echo $uid; ?> .olo-lf-avatar {
                width: 64px; height: 64px; border-radius: 50%; overflow: hidden;
                margin: 0 auto 12px; display: block;
            }
            .<?php echo $uid; ?> .olo-lf-avatar img { width: 100%; height: 100%; object-fit: cover; display: block; border-radius: 50%; }
            .<?php echo $uid; ?> .olo-lf-user-name { font-weight: 700; font-size: 17px; color: <?php echo $text_color_val; ?>; }
            .<?php echo $uid; ?> .olo-lf-welcome { font-size: 14px; color: <?php echo $text_color_val; ?>; opacity: 0.6; margin-top: 4px; }
            /* Messages */
            .<?php echo $uid; ?> .olo-lf-msg {
                padding: 10px 14px; border-radius: <?php echo $input_radius; ?>;
                font-size: 14px; margin-bottom: 14px; display: none;
            }
            .<?php echo $uid; ?> .olo-lf-msg--error {
                background: color-mix(in srgb, var(--olo-color-danger, #EF4444) 15%, transparent);
                color: var(--olo-color-danger, #EF4444);
                border: 1px solid color-mix(in srgb, var(--olo-color-danger, #EF4444) 30%, transparent);
            }
            .<?php echo $uid; ?> .olo-lf-msg--success {
                background: color-mix(in srgb, var(--olo-color-success, #10B981) 15%, transparent);
                color: var(--olo-color-success, #10B981);
                border: 1px solid color-mix(in srgb, var(--olo-color-success, #10B981) 30%, transparent);
            }
            /* Panels */
            .<?php echo $uid; ?> .olo-lf-panel { display: none; }
            .<?php echo $uid; ?> .olo-lf-panel.active { display: block; }
            /* Social */
            .<?php echo $uid; ?> .olo-lf-social-btn {
                display: flex; align-items: center; justify-content: center; gap: 10px;
                width: 100%; padding: 10px 16px; font-size: 14px; font-weight: 500;
                font-family: inherit; border-radius: <?php echo $input_radius; ?>;
                cursor: pointer; transition: background-color 0.2s; text-decoration: none;
                box-sizing: border-box;
            }
            .<?php echo $uid; ?> .olo-lf-social-btn:hover { opacity: 0.85; }
            .<?php echo $uid; ?> .olo-lf-social-btn:focus-visible { outline: none; box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent); }
            .<?php echo $uid; ?> .olo-lf-divider {
                display: flex; align-items: center; gap: 12px; margin: 20px 0;
            }
            .<?php echo $uid; ?> .olo-lf-divider-line {
                flex: 1; height: 1px; background: <?php echo $input_border_val; ?>;
            }
            .<?php echo $uid; ?> .olo-lf-divider-text {
                font-size: 12px; white-space: nowrap; opacity: 0.5;
                color: <?php echo $text_color_val; ?>;
            }
            /* Switch link */
            .<?php echo $uid; ?> .olo-lf-switch {
                text-align: center; margin-top: 16px; font-size: 13px;
                color: <?php echo $text_color_val; ?>; opacity: 0.7;
            }
            .<?php echo $uid; ?> .olo-lf-switch a {
                color: <?php echo $link_color_val; ?>; text-decoration: none; font-weight: 600;
            }
            .<?php echo $uid; ?> .olo-lf-switch a:hover { text-decoration: underline; }
            /* Custom fields row */
            .<?php echo $uid; ?> .olo-lf-custom-row {
                display: flex; flex-wrap: wrap; gap: 16px 12px;
            }
            .<?php echo $uid; ?> .olo-lf-cf-full { width: 100%; }
            .<?php echo $uid; ?> .olo-lf-cf-half { width: calc(50% - 6px); }
            .<?php echo $uid; ?> .olo-lf-cf-third { width: calc(33.333% - 8px); }
            @media (max-width: 480px) {
                .<?php echo $uid; ?> .olo-lf-cf-half,
                .<?php echo $uid; ?> .olo-lf-cf-third { width: 100%; }
            }
            /* Terms */
            .<?php echo $uid; ?> .olo-lf-terms {
                display: flex; align-items: flex-start; gap: 8px;
                margin: 16px 0; font-size: 13px; cursor: pointer;
                color: <?php echo $text_color_val; ?>;
            }
            .<?php echo $uid; ?> .olo-lf-terms input { accent-color: <?php echo $submit_bg_val; ?>; margin-top: 2px; }
            .<?php echo $uid; ?> .olo-lf-terms a {
                color: <?php echo $link_color_val; ?>; text-decoration: underline;
            }
            /* Radio/checkbox labels */
            .<?php echo $uid; ?> .olo-lf-option {
                display: flex; align-items: center; gap: 8px; font-size: 14px;
                cursor: pointer; color: <?php echo $text_color_val; ?>; margin-bottom: 6px;
            }
            .<?php echo $uid; ?> .olo-lf-option input {
                accent-color: <?php echo $submit_bg_val; ?>;
            }
        </style>

        <div class="olo-loginform <?php echo esc_attr( $uid ); ?> olo-lf-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" data-pw-str="0">
        <?php
        if ( is_user_logged_in() ) :
            $current_user = wp_get_current_user();
            $logout_url   = $logout_redirect ? wp_logout_url( $logout_redirect ) : wp_logout_url( get_permalink() );
            ?>
            <div class="olo-lf-loggedin">
                <?php if ( $show_avatar ) : ?>
                    <div class="olo-lf-avatar">
                        <?php echo get_avatar( $current_user->ID, 64 ); ?>
                    </div>
                <?php endif; ?>
                <div class="olo-lf-user-name"><?php echo esc_html( $logged_in_msg ); ?></div>
                <div class="olo-lf-welcome"><?php echo esc_html( $current_user->display_name ); ?></div>
                <div style="margin-top:16px;">
                    <a href="<?php echo esc_url( $logout_url ); ?>" class="olo-lf-link"><?php echo esc_html( olo_t( 'Esci' ) ); ?></a>
                </div>
            </div>
        <?php else : ?>

            <?php if ( $mode === 'both' ) : ?>
                <div class="olo-lf-tabs">
                    <button type="button" class="olo-lf-tab active" data-olo-tab="login" data-olo-uid="<?php echo esc_attr( $uid ); ?>"><?php echo $login_btn_text; ?></button>
                    <button type="button" class="olo-lf-tab" data-olo-tab="register" data-olo-uid="<?php echo esc_attr( $uid ); ?>"><?php echo $register_btn_text; ?></button>
                </div>
            <?php endif; ?>

            <?php
            // ── Login panel ──
            $show_login = ( $mode === 'login' || $mode === 'both' );
            if ( $show_login ) :
                $login_active = ' active';
                $redirect_to  = $redirect_url ?: esc_url( get_permalink() );
            ?>
            <div class="olo-lf-panel<?php echo $login_active; ?>" data-olo-panel="login" data-olo-uid="<?php echo esc_attr( $uid ); ?>">
                <!-- Header -->
                <?php if ( $login_title ) : ?>
                <div class="olo-lf-header">
                    <div class="olo-lf-title"><?php echo $login_title; ?></div>
                    <?php if ( $login_subtitle ) : ?><div class="olo-lf-subtitle"><?php echo $login_subtitle; ?></div><?php endif; ?>
                </div>
                <?php endif; ?>

                <?php
                // Social buttons
                if ( $has_social ) :
                    $this->render_social_buttons( $s, $uid );
                endif;
                ?>

                <div class="olo-lf-msg" id="<?php echo esc_attr( $uid ); ?>-login-msg"></div>
                <form method="post" class="olo-lf-form" data-olo-loginform="login" data-olo-uid="<?php echo esc_attr( $uid ); ?>">
                    <input type="hidden" name="olo_login_nonce" value="<?php echo esc_attr( $nonce ); ?>" />
                    <input type="hidden" name="olo_uid" value="<?php echo esc_attr( $uid ); ?>" />
                    <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
                    <div class="olo-lf-field">
                        <label class="olo-lf-label" for="<?php echo esc_attr( $uid ); ?>-user"><?php echo esc_html( olo_t( 'Nome utente o email' ) ); ?></label>
                        <div class="olo-lf-input-wrap">
                            <?php if ( $show_icons ) : ?><span class="olo-lf-icon"><?php echo $icon_user; ?></span><?php endif; ?>
                            <input type="text" id="<?php echo esc_attr( $uid ); ?>-user" name="log" class="olo-lf-input" placeholder="<?php echo esc_attr( olo_t( 'nome@esempio.it' ) ); ?>" required autocomplete="username" />
                        </div>
                    </div>
                    <div class="olo-lf-field">
                        <label class="olo-lf-label" for="<?php echo esc_attr( $uid ); ?>-pass"><?php echo esc_html( olo_t( 'Password' ) ); ?></label>
                        <div class="olo-lf-input-wrap">
                            <?php if ( $show_icons ) : ?><span class="olo-lf-icon"><?php echo $icon_lock; ?></span><?php endif; ?>
                            <input type="password" id="<?php echo esc_attr( $uid ); ?>-pass" name="pwd" class="olo-lf-input" placeholder="<?php echo esc_attr( olo_t( 'La tua password' ) ); ?>" required autocomplete="current-password" />
                            <?php if ( $show_pw_toggle ) : ?>
                            <button type="button" class="olo-lf-pw-toggle" data-olo-pw-toggle>
                                <span class="olo-eye-on"><?php echo $icon_eye; ?></span>
                                <span class="olo-eye-off"><?php echo $icon_eye_off; ?></span>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="olo-lf-row">
                        <?php if ( $show_remember ) : ?>
                        <label class="olo-lf-remember">
                            <input type="checkbox" name="rememberme" value="forever" />
                            <span><?php echo esc_html( olo_t( 'Ricordami' ) ); ?></span>
                        </label>
                        <?php else : ?><span></span><?php endif; ?>
                        <?php if ( $show_lost_pw ) : ?>
                        <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="olo-lf-link"><?php echo esc_html( olo_t( 'Password dimenticata?' ) ); ?></a>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="olo-lf-submit"><?php echo $login_btn_text; ?></button>
                </form>
                <?php if ( $mode === 'both' ) : ?>
                <div class="olo-lf-switch">Non hai un account? <a href="#" data-olo-switch="register" data-olo-uid="<?php echo esc_attr( $uid ); ?>"><?php echo esc_html( olo_t( 'Registrati' ) ); ?></a></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php
            // ── Register panel ──
            $show_reg = ( $mode === 'register' || $mode === 'both' );
            if ( $show_reg ) :
                $reg_active = ( $mode === 'register' ) ? ' active' : '';
                $reg_redirect = $redirect_url ?: esc_url( get_permalink() );
            ?>
            <div class="olo-lf-panel<?php echo $reg_active; ?>" data-olo-panel="register" data-olo-uid="<?php echo esc_attr( $uid ); ?>">
                <!-- Header -->
                <?php if ( $register_title ) : ?>
                <div class="olo-lf-header">
                    <div class="olo-lf-title"><?php echo $register_title; ?></div>
                    <?php if ( $register_subtitle ) : ?><div class="olo-lf-subtitle"><?php echo $register_subtitle; ?></div><?php endif; ?>
                </div>
                <?php endif; ?>

                <?php
                if ( $has_social ) :
                    $this->render_social_buttons( $s, $uid );
                endif;
                ?>

                <div class="olo-lf-msg" id="<?php echo esc_attr( $uid ); ?>-reg-msg"></div>
                <?php if ( get_option( 'users_can_register' ) ) : ?>
                <?php
                // Encode password config server-side to prevent client-side bypass
                $pw_config = base64_encode( wp_json_encode( [
                    'pw_min_length'  => $pw_min_length,
                    'pw_req_upper'   => $pw_req_upper,
                    'pw_req_number'  => $pw_req_number,
                    'pw_req_special' => $pw_req_special,
                    'pw_min_strength' => $pw_min_strength,
                ] ) );
                ?>
                <form method="post" class="olo-lf-form" data-olo-loginform="register" data-olo-uid="<?php echo esc_attr( $uid ); ?>">
                    <input type="hidden" name="olo_register_nonce" value="<?php echo esc_attr( $nonce ); ?>" />
                    <input type="hidden" name="olo_uid" value="<?php echo esc_attr( $uid ); ?>" />
                    <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $reg_redirect ); ?>" />
                    <input type="hidden" name="_olo_form_config" value="<?php echo esc_attr( $pw_config ); ?>" />
                    <div class="olo-lf-custom-row">
                    <?php foreach ( $register_fields as $rf_idx => $rf ) :
                        $rf_type  = sanitize_key( $rf['field_type'] ?? 'text' );
                        $rf_label = esc_html( $rf['label'] ?? '' );
                        $rf_ph    = esc_attr( $rf['placeholder'] ?? '' );
                        $rf_req   = ! empty( $rf['required'] );
                        $rf_w_val = $rf['width'] ?? '100';
                        $rf_width = $rf_w_val === '50' ? 'olo-lf-cf-half' : ( $rf_w_val === '33' ? 'olo-lf-cf-third' : 'olo-lf-cf-full' );
                        $rf_meta  = sanitize_key( $rf['meta_key'] ?? '' );
                        $rf_opts  = array_filter( array_map( 'trim', explode( "\n", $rf['options'] ?? '' ) ) );
                        $req_attr = $rf_req ? ' required' : '';
                        $req_star = $rf_req ? ' *' : '';
                        $builtin  = in_array( $rf_type, [ 'username', 'user_email', 'user_password', 'confirm_password' ], true );
                    ?>

                    <?php if ( $rf_type === 'username' ) : ?>
                    <div class="<?php echo $rf_width; ?>">
                        <label class="olo-lf-label" for="<?php echo esc_attr( $uid ); ?>-reg-user"><?php echo $rf_label . $req_star; ?></label>
                        <div class="olo-lf-input-wrap">
                            <?php if ( $show_icons ) : ?><span class="olo-lf-icon"><?php echo $icon_user; ?></span><?php endif; ?>
                            <input type="text" id="<?php echo esc_attr( $uid ); ?>-reg-user" name="user_login" class="olo-lf-input" placeholder="<?php echo $rf_ph; ?>"<?php echo $req_attr; ?> autocomplete="username" />
                        </div>
                    </div>

                    <?php elseif ( $rf_type === 'user_email' ) : ?>
                    <div class="<?php echo $rf_width; ?>">
                        <label class="olo-lf-label" for="<?php echo esc_attr( $uid ); ?>-reg-email"><?php echo $rf_label . $req_star; ?></label>
                        <div class="olo-lf-input-wrap">
                            <?php if ( $show_icons ) : ?><span class="olo-lf-icon"><?php echo $icon_mail; ?></span><?php endif; ?>
                            <input type="email" id="<?php echo esc_attr( $uid ); ?>-reg-email" name="user_email" class="olo-lf-input" placeholder="<?php echo $rf_ph; ?>"<?php echo $req_attr; ?> autocomplete="email" />
                        </div>
                    </div>

                    <?php elseif ( $rf_type === 'user_password' ) : ?>
                    <div class="<?php echo $rf_width; ?>" style="margin-bottom:4px;">
                        <label class="olo-lf-label" for="<?php echo esc_attr( $uid ); ?>-reg-pass"><?php echo $rf_label . $req_star; ?></label>
                        <div class="olo-lf-input-wrap">
                            <?php if ( $show_icons ) : ?><span class="olo-lf-icon"><?php echo $icon_lock; ?></span><?php endif; ?>
                            <input type="password" id="<?php echo esc_attr( $uid ); ?>-reg-pass" name="user_pass" class="olo-lf-input olo-lf-pw-input" placeholder="<?php echo $rf_ph; ?>"<?php echo $req_attr; ?> autocomplete="new-password" minlength="<?php echo esc_attr( $pw_min_length ); ?>" />
                            <?php if ( $show_pw_toggle ) : ?>
                            <button type="button" class="olo-lf-pw-toggle" data-olo-pw-toggle>
                                <span class="olo-eye-on"><?php echo $icon_eye; ?></span>
                                <span class="olo-eye-off"><?php echo $icon_eye_off; ?></span>
                            </button>
                            <?php endif; ?>
                        </div>
                        <?php if ( $show_pw_strength ) : ?>
                        <div class="olo-lf-strength">
                            <div class="olo-lf-strength-bars">
                                <div class="olo-lf-strength-bar"></div>
                                <div class="olo-lf-strength-bar"></div>
                                <div class="olo-lf-strength-bar"></div>
                                <div class="olo-lf-strength-bar"></div>
                            </div>
                            <div class="olo-lf-strength-text"></div>
                        </div>
                        <?php endif; ?>
                        <?php
                        // Password requirements checklist
                        $pw_reqs = [];
                        if ( $pw_min_length > 0 ) { $pw_reqs[] = 'Almeno ' . $pw_min_length . ' caratteri'; }
                        if ( $pw_req_upper )       { $pw_reqs[] = 'Almeno una lettera maiuscola'; }
                        if ( $pw_req_number )      { $pw_reqs[] = 'Almeno un numero'; }
                        if ( $pw_req_special )     { $pw_reqs[] = 'Almeno un carattere speciale (!@#$...)'; }
                        $strength_labels = [ 1 => 'Debole', 2 => 'Media', 3 => 'Buona', 4 => 'Forte' ];
                        if ( $pw_min_strength > 0 ) { $pw_reqs[] = 'Forza minima: ' . ( $strength_labels[ $pw_min_strength ] ?? $pw_min_strength ); }
                        if ( ! empty( $pw_reqs ) ) : ?>
                        <div class="olo-lf-pw-reqs" style="margin-top:6px;">
                            <?php foreach ( $pw_reqs as $pr ) : ?>
                            <div class="olo-lf-pw-req" data-met="0" style="display:flex;align-items:center;gap:6px;font-size:11px;margin-bottom:3px;color:<?php echo $text_color_val; ?>;opacity:0.6;">
                                <svg class="olo-req-circle" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>
                                <svg class="olo-req-check" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.5" style="display:none;"><path d="M20 6L9 17l-5-5"/></svg>
                                <span><?php echo esc_html( $pr ); ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php elseif ( $rf_type === 'confirm_password' ) : ?>
                    <div class="<?php echo $rf_width; ?>">
                        <label class="olo-lf-label" for="<?php echo esc_attr( $uid ); ?>-reg-pass2"><?php echo $rf_label . $req_star; ?></label>
                        <div class="olo-lf-input-wrap">
                            <?php if ( $show_icons ) : ?><span class="olo-lf-icon"><?php echo $icon_lock; ?></span><?php endif; ?>
                            <input type="password" id="<?php echo esc_attr( $uid ); ?>-reg-pass2" name="user_pass_confirm" class="olo-lf-input" placeholder="<?php echo $rf_ph; ?>"<?php echo $req_attr; ?> autocomplete="new-password" />
                        </div>
                    </div>

                    <?php elseif ( in_array( $rf_type, [ 'text', 'email', 'tel', 'number', 'date', 'url' ], true ) ) : ?>
                    <?php $cf_name = $rf_meta ?: 'olo_cf_' . sanitize_key( $rf_label ); ?>
                    <div class="<?php echo $rf_width; ?>">
                        <label class="olo-lf-label"><?php echo $rf_label . $req_star; ?></label>
                        <div class="olo-lf-input-wrap">
                            <input type="<?php echo $rf_type; ?>" name="olo_custom[<?php echo esc_attr( $cf_name ); ?>]" class="olo-lf-input" placeholder="<?php echo $rf_ph; ?>"<?php echo $req_attr; ?> />
                        </div>
                    </div>

                    <?php elseif ( $rf_type === 'textarea' ) : ?>
                    <?php $cf_name = $rf_meta ?: 'olo_cf_' . sanitize_key( $rf_label ); ?>
                    <div class="<?php echo $rf_width; ?>">
                        <label class="olo-lf-label"><?php echo $rf_label . $req_star; ?></label>
                        <textarea name="olo_custom[<?php echo esc_attr( $cf_name ); ?>]" class="olo-lf-input-standalone" placeholder="<?php echo $rf_ph; ?>"<?php echo $req_attr; ?>></textarea>
                    </div>

                    <?php elseif ( $rf_type === 'select' ) : ?>
                    <?php $cf_name = $rf_meta ?: 'olo_cf_' . sanitize_key( $rf_label ); ?>
                    <div class="<?php echo $rf_width; ?>">
                        <label class="olo-lf-label"><?php echo $rf_label . $req_star; ?></label>
                        <select name="olo_custom[<?php echo esc_attr( $cf_name ); ?>]" class="olo-lf-input-standalone"<?php echo $req_attr; ?>>
                            <option value=""><?php echo $rf_ph ?: 'Seleziona...'; ?></option>
                            <?php foreach ( $rf_opts as $opt ) : ?>
                            <option value="<?php echo esc_attr( $opt ); ?>"><?php echo esc_html( $opt ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php elseif ( $rf_type === 'checkbox' ) : ?>
                    <?php $cf_name = $rf_meta ?: 'olo_cf_' . sanitize_key( $rf_label ); ?>
                    <div class="<?php echo $rf_width; ?>">
                        <label class="olo-lf-option">
                            <input type="checkbox" name="olo_custom[<?php echo esc_attr( $cf_name ); ?>]" value="1"<?php echo $req_attr; ?> />
                            <span><?php echo $rf_ph ?: $rf_label; ?></span>
                        </label>
                    </div>

                    <?php elseif ( $rf_type === 'radio' ) : ?>
                    <?php $cf_name = $rf_meta ?: 'olo_cf_' . sanitize_key( $rf_label ); ?>
                    <div class="<?php echo $rf_width; ?>">
                        <label class="olo-lf-label"><?php echo $rf_label . $req_star; ?></label>
                        <?php foreach ( $rf_opts as $opt ) : ?>
                        <label class="olo-lf-option">
                            <input type="radio" name="olo_custom[<?php echo esc_attr( $cf_name ); ?>]" value="<?php echo esc_attr( $opt ); ?>"<?php echo $req_attr; ?> />
                            <span><?php echo esc_html( $opt ); ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <?php endforeach; ?>
                    </div>

                    <?php if ( $show_terms ) : ?>
                    <label class="olo-lf-terms">
                        <input type="checkbox" name="olo_terms" value="1" required />
                        <span>
                            <?php if ( $terms_url ) : ?>
                            <a href="<?php echo $terms_url; ?>" target="_blank"><?php echo $terms_text; ?></a>
                            <?php else : ?>
                            <?php echo $terms_text; ?>
                            <?php endif; ?>
                        </span>
                    </label>
                    <?php endif; ?>

                    <button type="submit" class="olo-lf-submit"><?php echo $register_btn_text; ?></button>
                </form>
                <?php else : ?>
                <p style="color:<?php echo $text_color_val; ?>;font-size:14px;text-align:center;padding:20px 0;">
                    La registrazione non è attiva su questo sito.
                </p>
                <?php endif; ?>
                <?php if ( $mode === 'both' ) : ?>
                <div class="olo-lf-switch">Hai già un account? <a href="#" data-olo-switch="login" data-olo-uid="<?php echo esc_attr( $uid ); ?>"><?php echo esc_html( olo_t( 'Accedi' ) ); ?></a></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        <?php endif; ?>
        </div>

        <script>
        (function(){
            var uid = <?php echo wp_json_encode( $uid ); ?>;
            var wrap = document.querySelector('.' + uid);
            if (!wrap) { return; }

            /* ── Tab switching ── */
            var tabs = wrap.querySelectorAll('.olo-lf-tab[data-olo-uid="' + uid + '"]');
            if (tabs.length) {
                tabs.forEach(function(tab){
                    tab.addEventListener('click', function(){
                        var target = this.getAttribute('data-olo-tab');
                        tabs.forEach(function(t){ t.classList.remove('active'); });
                        this.classList.add('active');
                        var panels = wrap.querySelectorAll('.olo-lf-panel[data-olo-uid="' + uid + '"]');
                        panels.forEach(function(p){
                            if (p.getAttribute('data-olo-panel') === target) {
                                p.classList.add('active');
                            } else {
                                p.classList.remove('active');
                            }
                        });
                    });
                });
            }

            /* ── Switch links ── */
            var switchLinks = wrap.querySelectorAll('a[data-olo-switch][data-olo-uid="' + uid + '"]');
            switchLinks.forEach(function(link){
                link.addEventListener('click', function(e){
                    e.preventDefault();
                    var target = this.getAttribute('data-olo-switch');
                    tabs.forEach(function(t){
                        if (t.getAttribute('data-olo-tab') === target) { t.click(); }
                    });
                });
            });

            /* ── Password toggle ── */
            wrap.querySelectorAll('[data-olo-pw-toggle]').forEach(function(btn){
                btn.addEventListener('click', function(){
                    var inputWrap = this.closest('.olo-lf-input-wrap');
                    if (!inputWrap) { return; }
                    var inp = inputWrap.querySelector('input');
                    if (!inp) { return; }
                    if (inp.type === 'password') {
                        inp.type = 'text';
                        this.classList.add('olo-showing');
                    } else {
                        inp.type = 'password';
                        this.classList.remove('olo-showing');
                    }
                });
            });

            /* ── Password strength + requirements ── */
            var pwInput = wrap.querySelector('.olo-lf-pw-input');
            var strText = wrap.querySelector('.olo-lf-strength-text');
            var reqItems = wrap.querySelectorAll('.olo-lf-pw-req');
            var regFormEl = wrap.querySelector('form[data-olo-loginform="register"]');
            var pwMinLen = 8, pwReqUpper = false, pwReqNumber = false, pwReqSpecial = false, pwMinStr = 0;
            if (regFormEl) {
                var ml = regFormEl.querySelector('input[name="olo_pw_min_length"]');
                if (ml) { pwMinLen = parseInt(ml.value) || 8; }
                var ru = regFormEl.querySelector('input[name="olo_pw_req_upper"]');
                if (ru) { pwReqUpper = ru.value === '1'; }
                var rn = regFormEl.querySelector('input[name="olo_pw_req_number"]');
                if (rn) { pwReqNumber = rn.value === '1'; }
                var rs = regFormEl.querySelector('input[name="olo_pw_req_special"]');
                if (rs) { pwReqSpecial = rs.value === '1'; }
                var ms = regFormEl.querySelector('input[name="olo_pw_min_strength"]');
                if (ms) { pwMinStr = parseInt(ms.value) || 0; }
            }
            if (pwInput) {
                var labels = ['', 'Debole', 'Media', 'Buona', 'Forte'];
                function updateReqItem(idx, met) {
                    if (idx >= reqItems.length) { return; }
                    var el = reqItems[idx];
                    var circle = el.querySelector('.olo-req-circle');
                    var check = el.querySelector('.olo-req-check');
                    if (met) {
                        if (circle) { circle.style.display = 'none'; }
                        if (check) { check.style.display = 'block'; }
                        el.style.opacity = '1';
                    } else {
                        if (circle) { circle.style.display = 'block'; }
                        if (check) { check.style.display = 'none'; }
                        el.style.opacity = '0.6';
                    }
                }
                pwInput.addEventListener('input', function(){
                    var v = this.value;
                    var hasLen = v.length >= pwMinLen;
                    var hasUpper = /[A-Z]/.test(v);
                    var hasNum = /[0-9]/.test(v);
                    var hasSpec = /[^A-Za-z0-9]/.test(v);
                    var score = 0;
                    if (hasLen) { score++; }
                    if (hasUpper) { score++; }
                    if (hasNum) { score++; }
                    if (hasSpec) { score++; }
                    if (v.length === 0) { score = 0; }
                    wrap.setAttribute('data-pw-str', score);
                    if (strText) { strText.textContent = labels[score] || ''; }
                    var ri = 0;
                    if (pwMinLen > 0) { updateReqItem(ri, hasLen); ri++; }
                    if (pwReqUpper) { updateReqItem(ri, hasUpper); ri++; }
                    if (pwReqNumber) { updateReqItem(ri, hasNum); ri++; }
                    if (pwReqSpecial) { updateReqItem(ri, hasSpec); ri++; }
                    if (pwMinStr > 0) { updateReqItem(ri, score >= pwMinStr); ri++; }
                    var allMet = true;
                    if (!hasLen) { allMet = false; }
                    if (pwReqUpper) { if (!hasUpper) { allMet = false; } }
                    if (pwReqNumber) { if (!hasNum) { allMet = false; } }
                    if (pwReqSpecial) { if (!hasSpec) { allMet = false; } }
                    if (pwMinStr > 0) { if (score < pwMinStr) { allMet = false; } }
                    if (v.length === 0) { this.setCustomValidity(''); }
                    else if (!allMet) { this.setCustomValidity('La password non soddisfa i requisiti minimi.'); }
                    else { this.setCustomValidity(''); }
                });
            }

            /* ── Confirm password validation ── */
            var passField = wrap.querySelector('input[name="user_pass"]');
            var confirmField = wrap.querySelector('input[name="user_pass_confirm"]');
            if (passField) {
                if (confirmField) {
                    confirmField.addEventListener('input', function(){
                        if (this.value !== passField.value) {
                            this.setCustomValidity('Le password non corrispondono');
                        } else {
                            this.setCustomValidity('');
                        }
                    });
                }
            }

            /* ── AJAX login ── */
            var loginForm = wrap.querySelector('form[data-olo-loginform="login"][data-olo-uid="' + uid + '"]');
            if (loginForm) {
                loginForm.addEventListener('submit', function(e){
                    e.preventDefault();
                    var btn = loginForm.querySelector('.olo-lf-submit');
                    var msgEl = document.getElementById(uid + '-login-msg');
                    var origText = btn ? btn.textContent : '';
                    if (btn) { btn.disabled = true; btn.textContent = 'Accesso in corso...'; }
                    if (msgEl) { msgEl.style.display = 'none'; }

                    var fd = new FormData(loginForm);
                    fd.append('action', 'olo_ajax_login');

                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', (typeof oloData !== 'undefined' ? oloData.ajax_url : '/wp-admin/admin-ajax.php'), true);
                    xhr.onreadystatechange = function(){
                        if (xhr.readyState !== 4) { return; }
                        if (btn) { btn.disabled = false; btn.textContent = origText; }
                        try {
                            var resp = JSON.parse(xhr.responseText);
                            if (resp.success) {
                                if (msgEl) {
                                    msgEl.className = 'olo-lf-msg olo-lf-msg--success';
                                    msgEl.textContent = 'Accesso effettuato! Reindirizzamento...';
                                    msgEl.style.display = 'block';
                                }
                                var redir = fd.get('redirect_to');
                                if (redir) {
                                    window.location.href = redir;
                                } else {
                                    window.location.reload();
                                }
                            } else {
                                if (msgEl) {
                                    msgEl.className = 'olo-lf-msg olo-lf-msg--error';
                                    msgEl.textContent = resp.data || 'Credenziali non valide.';
                                    msgEl.style.display = 'block';
                                }
                            }
                        } catch(ex) {
                            if (msgEl) {
                                msgEl.className = 'olo-lf-msg olo-lf-msg--error';
                                msgEl.textContent = 'Errore di comunicazione. Riprova.';
                                msgEl.style.display = 'block';
                            }
                        }
                    };
                    xhr.send(fd);
                });
            }

            /* ── AJAX register ── */
            var regForm = wrap.querySelector('form[data-olo-loginform="register"][data-olo-uid="' + uid + '"]');
            if (regForm) {
                regForm.addEventListener('submit', function(e){
                    e.preventDefault();
                    var btn = regForm.querySelector('.olo-lf-submit');
                    var msgEl = document.getElementById(uid + '-reg-msg');
                    var origText = btn ? btn.textContent : '';
                    if (btn) { btn.disabled = true; btn.textContent = 'Registrazione in corso...'; }
                    if (msgEl) { msgEl.style.display = 'none'; }

                    var fd = new FormData(regForm);
                    fd.append('action', 'olo_ajax_register');

                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', (typeof oloData !== 'undefined' ? oloData.ajax_url : '/wp-admin/admin-ajax.php'), true);
                    xhr.onreadystatechange = function(){
                        if (xhr.readyState !== 4) { return; }
                        if (btn) { btn.disabled = false; btn.textContent = origText; }
                        try {
                            var resp = JSON.parse(xhr.responseText);
                            if (resp.success) {
                                if (msgEl) {
                                    msgEl.className = 'olo-lf-msg olo-lf-msg--success';
                                    msgEl.textContent = resp.data || 'Registrazione completata!';
                                    msgEl.style.display = 'block';
                                }
                                var redir = fd.get('redirect_to');
                                if (redir) {
                                    setTimeout(function(){ window.location.href = redir; }, 1500);
                                }
                            } else {
                                if (msgEl) {
                                    msgEl.className = 'olo-lf-msg olo-lf-msg--error';
                                    msgEl.textContent = resp.data || 'Errore nella registrazione.';
                                    msgEl.style.display = 'block';
                                }
                            }
                        } catch(ex) {
                            if (msgEl) {
                                msgEl.className = 'olo-lf-msg olo-lf-msg--error';
                                msgEl.textContent = 'Errore di comunicazione. Riprova.';
                                msgEl.style.display = 'block';
                            }
                        }
                    };
                    xhr.send(fd);
                });
            }
        })();
        </script>

        <?php
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}";
            echo $border_hover_css . $border_effect_css . '</style>';
        }
        return ob_get_clean();
    }

    /**
     * Render social login buttons.
     */
    private function render_social_buttons( $s, $uid ) {
        $input_bg    = $this->safe_color_css( $s['input_bg'] ) ?: 'var(--olo-color-background, #FFFFFF)';
        $text_color  = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text, #374151)';
        $border      = $this->safe_color_css( $s['input_border_color'] ) ?: 'var(--olo-color-border, #E5E7EB)';
        $divider_text = esc_html( $s['social_divider_text'] ?: 'oppure' );
        ?>
        <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:0;">
            <?php if ( ! empty( $s['social_google'] ) ) : ?>
            <a href="<?php echo esc_url( $s['social_google_url'] ?: '#' ); ?>" class="olo-lf-social-btn" style="background:<?php echo $input_bg; ?>;color:<?php echo $text_color; ?>;border:1px solid <?php echo $border; ?>;">
                <svg width="18" height="18" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18A10.96 10.96 0 0 0 1 12c0 1.77.42 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                <span><?php echo esc_html( olo_t( 'Continua con Google' ) ); ?></span>
            </a>
            <?php endif; ?>
            <?php if ( ! empty( $s['social_facebook'] ) ) : ?>
            <a href="<?php echo esc_url( $s['social_facebook_url'] ?: '#' ); ?>" class="olo-lf-social-btn" style="background:<?php echo $input_bg; ?>;color:<?php echo $text_color; ?>;border:1px solid <?php echo $border; ?>;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                <span><?php echo esc_html( olo_t( 'Continua con Facebook' ) ); ?></span>
            </a>
            <?php endif; ?>
            <?php if ( ! empty( $s['social_apple'] ) ) : ?>
            <a href="<?php echo esc_url( $s['social_apple_url'] ?: '#' ); ?>" class="olo-lf-social-btn" style="background:#000;color:#FFF;border:1px solid #000;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="#FFF"><path d="M17.05 20.28c-.98.95-2.05.88-3.08.4-1.09-.5-2.08-.48-3.24 0-1.44.62-2.2.44-3.06-.4C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/></svg>
                <span><?php echo esc_html( olo_t( 'Continua con Apple' ) ); ?></span>
            </a>
            <?php endif; ?>
        </div>
        <div class="olo-lf-divider">
            <div class="olo-lf-divider-line"></div>
            <span class="olo-lf-divider-text"><?php echo $divider_text; ?></span>
            <div class="olo-lf-divider-line"></div>
        </div>
        <?php
    }

    /**
     * Register AJAX handlers for login and register.
     */
    public static function register_ajax_handlers() {
        add_action( 'wp_ajax_nopriv_olo_ajax_login', [ __CLASS__, 'handle_ajax_login' ] );
        add_action( 'wp_ajax_olo_ajax_login', [ __CLASS__, 'handle_ajax_login' ] );
        add_action( 'wp_ajax_nopriv_olo_ajax_register', [ __CLASS__, 'handle_ajax_register' ] );

        // Colonne custom nella tabella utenti admin
        add_filter( 'manage_users_columns', [ __CLASS__, 'add_user_columns' ] );
        add_filter( 'manage_users_custom_column', [ __CLASS__, 'render_user_column' ], 10, 3 );
        add_filter( 'manage_users_sortable_columns', [ __CLASS__, 'sortable_user_columns' ] );
        add_action( 'pre_get_users', [ __CLASS__, 'sort_user_columns' ] );

        // Sezione nel profilo utente
        add_action( 'show_user_profile', [ __CLASS__, 'render_user_profile_fields' ] );
        add_action( 'edit_user_profile', [ __CLASS__, 'render_user_profile_fields' ] );
        add_action( 'personal_options_update', [ __CLASS__, 'save_user_profile_fields' ] );
        add_action( 'edit_user_profile_update', [ __CLASS__, 'save_user_profile_fields' ] );
    }

    /**
     * Recupera le chiavi meta custom salvate dal form di registrazione.
     */
    private static function get_olo_custom_meta_keys() {
        static $cache = null;
        if ( $cache !== null ) return $cache;

        global $wpdb;
        $keys = $wpdb->get_col(
            "SELECT DISTINCT meta_key FROM {$wpdb->usermeta} WHERE meta_key LIKE 'olo_cf_%' ORDER BY meta_key"
        );
        $cache = is_array( $keys ) ? $keys : [];
        return $cache;
    }

    /**
     * Aggiunge colonne alla tabella utenti.
     */
    public static function add_user_columns( $columns ) {
        $meta_keys = self::get_olo_custom_meta_keys();
        foreach ( $meta_keys as $key ) {
            $label = str_replace( 'olo_cf_', '', $key );
            $label = ucfirst( str_replace( '_', ' ', $label ) );
            $columns[ 'olo_' . $key ] = $label;
        }
        return $columns;
    }

    /**
     * Renderizza il valore della colonna.
     */
    public static function render_user_column( $value, $column_name, $user_id ) {
        if ( str_starts_with( $column_name, 'olo_olo_cf_' ) ) {
            $meta_key = substr( $column_name, 4 ); // rimuove 'olo_'
            return esc_html( get_user_meta( $user_id, $meta_key, true ) );
        }
        return $value;
    }

    /**
     * Rende le colonne ordinabili.
     */
    public static function sortable_user_columns( $columns ) {
        $meta_keys = self::get_olo_custom_meta_keys();
        foreach ( $meta_keys as $key ) {
            $columns[ 'olo_' . $key ] = $key;
        }
        return $columns;
    }

    /**
     * Gestisce l'ordinamento per meta key.
     */
    public static function sort_user_columns( $query ) {
        if ( ! is_admin() ) return;
        $orderby = $query->get( 'orderby' );
        if ( $orderby && str_starts_with( $orderby, 'olo_cf_' ) ) {
            $query->set( 'meta_key', $orderby );
            $query->set( 'orderby', 'meta_value' );
        }
    }

    /**
     * Mostra i campi custom nel profilo utente admin.
     */
    public static function render_user_profile_fields( $user ) {
        $meta_keys = self::get_olo_custom_meta_keys();
        if ( empty( $meta_keys ) ) return;
        ?>
        <h3><?php echo esc_html( olo_t( 'Dati registrazione (Olobuild)' ) ); ?></h3>
        <table class="form-table">
            <?php foreach ( $meta_keys as $key ) :
                $label = str_replace( 'olo_cf_', '', $key );
                $label = ucfirst( str_replace( '_', ' ', $label ) );
                $value = get_user_meta( $user->ID, $key, true );
            ?>
            <tr>
                <th><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
                <td><input type="text" name="olo_profile_meta[<?php echo esc_attr( $key ); ?>]" id="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>" class="regular-text" /></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php
    }

    /**
     * Salva i campi custom dal profilo utente admin.
     */
    public static function save_user_profile_fields( $user_id ) {
        if ( ! current_user_can( 'edit_user', $user_id ) ) return;
        if ( empty( $_POST['olo_profile_meta'] ) || ! is_array( $_POST['olo_profile_meta'] ) ) return;
        foreach ( $_POST['olo_profile_meta'] as $key => $value ) {
            $safe_key = sanitize_key( $key );
            if ( str_starts_with( $safe_key, 'olo_cf_' ) ) {
                update_user_meta( $user_id, $safe_key, sanitize_text_field( $value ) );
            }
        }
    }

    /**
     * AJAX login handler.
     */
    public static function handle_ajax_login() {
        // CSRF protection — nonce was generated as olo_loginform_{uid}
        $nonce_val = sanitize_text_field( $_POST['olo_login_nonce'] ?? '' );
        $uid       = sanitize_text_field( $_POST['olo_uid'] ?? '' );
        if ( ! $uid || ! wp_verify_nonce( $nonce_val, 'olo_loginform_' . $uid ) ) {
            wp_send_json_error( 'Sessione scaduta. Ricarica la pagina.' );
        }

        $user = sanitize_text_field( $_POST['log'] ?? '' );
        $pass = wp_unslash( $_POST['pwd'] ?? '' );
        $remember = ! empty( $_POST['rememberme'] );

        if ( empty( $user ) ) {
            wp_send_json_error( 'Compila tutti i campi.' );
        }
        if ( empty( $pass ) ) {
            wp_send_json_error( 'Compila tutti i campi.' );
        }

        $creds = [
            'user_login'    => $user,
            'user_password' => $pass,
            'remember'      => $remember,
        ];

        $result = wp_signon( $creds, is_ssl() );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( 'Nome utente o password non corretti.' );
        }

        wp_send_json_success( 'OK' );
    }

    /**
     * AJAX register handler.
     */
    public static function handle_ajax_register() {
        // CSRF protection — nonce was generated as olo_loginform_{uid}
        $nonce_val = sanitize_text_field( $_POST['olo_register_nonce'] ?? '' );
        $uid       = sanitize_text_field( $_POST['olo_uid'] ?? '' );
        if ( ! $uid || ! wp_verify_nonce( $nonce_val, 'olo_loginform_' . $uid ) ) {
            wp_send_json_error( 'Sessione scaduta. Ricarica la pagina.' );
        }

        if ( ! get_option( 'users_can_register' ) ) {
            wp_send_json_error( 'La registrazione non è attiva.' );
        }

        $username = sanitize_user( $_POST['user_login'] ?? '' );
        $email    = sanitize_email( $_POST['user_email'] ?? '' );
        $password = wp_unslash( $_POST['user_pass'] ?? '' );

        if ( empty( $username ) ) {
            wp_send_json_error( 'Compila tutti i campi.' );
        }
        if ( empty( $email ) ) {
            wp_send_json_error( 'Compila tutti i campi.' );
        }
        if ( empty( $password ) ) {
            wp_send_json_error( 'Compila tutti i campi.' );
        }

        // Confirm password check
        if ( isset( $_POST['user_pass_confirm'] ) ) {
            if ( wp_unslash( $_POST['user_pass_confirm'] ) !== $password ) {
                wp_send_json_error( 'Le password non corrispondono.' );
            }
        }

        // Password complexity validation — read from saved config (NEVER from $_POST to prevent client-side bypass)
        $form_config_b64 = sanitize_text_field( $_POST['_olo_form_config'] ?? '' );
        $form_config     = $form_config_b64 ? json_decode( base64_decode( $form_config_b64 ), true ) : [];
        $pw_min_len  = max( 1, intval( $form_config['pw_min_length'] ?? 8 ) );
        $pw_req_up   = ! empty( $form_config['pw_req_upper'] );
        $pw_req_num  = ! empty( $form_config['pw_req_number'] );
        $pw_req_spec = ! empty( $form_config['pw_req_special'] );
        $pw_min_str  = max( 0, min( 4, intval( $form_config['pw_min_strength'] ?? 0 ) ) );

        if ( strlen( $password ) < $pw_min_len ) {
            wp_send_json_error( 'La password deve avere almeno ' . $pw_min_len . ' caratteri.' );
        }
        if ( $pw_req_up && ! preg_match( '/[A-Z]/', $password ) ) {
            wp_send_json_error( 'La password deve contenere almeno una lettera maiuscola.' );
        }
        if ( $pw_req_num && ! preg_match( '/[0-9]/', $password ) ) {
            wp_send_json_error( 'La password deve contenere almeno un numero.' );
        }
        if ( $pw_req_spec && ! preg_match( '/[^A-Za-z0-9]/', $password ) ) {
            wp_send_json_error( 'La password deve contenere almeno un carattere speciale.' );
        }
        if ( $pw_min_str > 0 ) {
            $pw_score = 0;
            if ( strlen( $password ) >= $pw_min_len ) { $pw_score++; }
            if ( preg_match( '/[A-Z]/', $password ) ) { $pw_score++; }
            if ( preg_match( '/[0-9]/', $password ) ) { $pw_score++; }
            if ( preg_match( '/[^A-Za-z0-9]/', $password ) ) { $pw_score++; }
            if ( $pw_score < $pw_min_str ) {
                $labels = [ 1 => 'Debole', 2 => 'Media', 3 => 'Buona', 4 => 'Forte' ];
                wp_send_json_error( 'La password deve raggiungere almeno il livello: ' . ( $labels[ $pw_min_str ] ?? $pw_min_str ) . '.' );
            }
        }

        if ( ! is_email( $email ) ) {
            wp_send_json_error( 'Inserisci un indirizzo email valido.' );
        }

        if ( username_exists( $username ) ) {
            wp_send_json_error( 'Questo nome utente è già in uso.' );
        }

        if ( email_exists( $email ) ) {
            wp_send_json_error( 'Questo indirizzo email è già registrato.' );
        }

        $user_id = wp_create_user( $username, $password, $email );

        if ( is_wp_error( $user_id ) ) {
            wp_send_json_error( 'Errore nella registrazione. Riprova.' );
        }

        // Save custom fields as user meta — only allow olo_cf_ prefixed keys to prevent privilege escalation
        if ( ! empty( $_POST['olo_custom'] ) ) {
            if ( is_array( $_POST['olo_custom'] ) ) {
                foreach ( $_POST['olo_custom'] as $meta_key => $meta_value ) {
                    $safe_key   = sanitize_key( $meta_key );
                    $safe_value = sanitize_text_field( $meta_value );
                    // SECURITY: only allow olo_cf_ prefixed keys — block wp_capabilities, wp_user_level etc.
                    if ( $safe_key && str_starts_with( $safe_key, 'olo_cf_' ) ) {
                        update_user_meta( $user_id, $safe_key, $safe_value );
                    }
                }
            }
        }

        // Send new user notification
        wp_new_user_notification( $user_id, null, 'both' );

        wp_send_json_success( 'Registrazione completata! Ora puoi accedere.' );
    }
}
