<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Form_Tile extends Olo_Tile_Base {

    protected $type     = 'form';
    protected $name     = 'Form Contatti';
    protected $icon     = 'dashicons-email-alt';
    protected $category = 'marketing';
    protected $defaults = [
        'preset' => 'custom',
        'fields' => [
            [ 'field_type' => 'text', 'label' => 'Nome', 'placeholder' => 'Il tuo nome', 'name' => 'nome', 'required' => true, 'width' => '1-2', 'options' => '', 'icon' => 'user' ],
            [ 'field_type' => 'email', 'label' => 'Email', 'placeholder' => 'La tua email', 'name' => 'email', 'required' => true, 'width' => '1-2', 'options' => '', 'icon' => 'mail' ],
            [ 'field_type' => 'text', 'label' => 'Oggetto', 'placeholder' => 'Oggetto del messaggio', 'name' => 'oggetto', 'required' => false, 'width' => '1-1', 'options' => '', 'icon' => '' ],
            [ 'field_type' => 'textarea', 'label' => 'Messaggio', 'placeholder' => 'Scrivi il tuo messaggio...', 'name' => 'messaggio', 'required' => true, 'width' => '1-1', 'options' => '', 'icon' => '' ],
        ],
        'email_to'          => '',
        'email_cc'          => '',
        'email_from_name'   => '',
        'email_subject'     => 'Nuovo messaggio dal sito',
        'success_message'   => 'Messaggio inviato con successo! Ti risponderemo al più presto.',
        'error_message'     => 'Si è verificato un errore. Riprova più tardi.',
        'redirect_url'      => '',
        'success_animation' => 'slide-top-small',
        'error_animation'   => 'shake',
        'auto_reply'        => false,
        'auto_reply_subject'=> 'Grazie per averci contattato',
        'auto_reply_message'=> 'Abbiamo ricevuto il tuo messaggio e ti risponderemo al più presto.',
        'submit_text'       => 'Invia messaggio',
        'submit_icon'       => '',
        'submit_icon_pos'   => 'left',
        'submit_alignment'  => 'left',
        'submit_full_width' => false,
        'form_max_width'    => '0',
        'form_align'        => 'left',
        'form_layout'       => 'stacked',
        'label_color'       => '',
        'label_size'        => '14',
        'label_weight'      => '500',
        'input_bg'          => '',
        'input_color'       => '',
        'input_border_color'=> '',
        'input_border_width'=> '1',
        'input_radius'      => '6',
        'input_size'        => 'default',
        'input_focus_border'=> '',
        'input_focus_shadow'=> true,
        'input_placeholder_opacity' => '0.4',
        'gap'               => '16',
        'submit_bg'         => '',
        'submit_color'      => '#FFFFFF',
        'submit_radius'     => '6',
        'submit_padding_x'  => '32',
        'submit_padding_y'  => '14',
        'submit_font_size'  => '16',
        'submit_font_weight'=> '600',
        'submit_hover_bg'   => '',
        'submit_border_width' => '0',
        'submit_border_color' => '',
        'submit_hover_border_color' => '',
        'submit_letter_spacing' => '0.3',
        'submit_text_transform' => 'none',
        'check_accent_color' => '',
        'check_bg'           => '',
        'check_border_color' => '',
        'check_size'         => '18',
        'check_label_gap'    => '8',
        'honeypot'          => true,
        'rate_limit'        => true,
        'rate_limit_max'    => '5',
        'rate_limit_window' => '60',
        'privacy_checkbox'  => false,
        'privacy_text'      => 'Accetto il trattamento dei dati personali secondo la <a href="/privacy-policy">Privacy Policy</a>',
        'enable_multistep'  => false,
        'step_style'        => 'progress',
        'step_labels'       => '',
        'enable_conditions' => false,
        'recaptcha_enabled' => false,
        'mailchimp_enabled' => false,
        'mailchimp_list_id' => '',
        'mailchimp_email_field' => 'email',
        'mailchimp_merge_fields' => '',
        'webhook_enabled'   => false,
        'webhook_url'       => '',
        'webhook_method'    => 'POST',
        'file_max_size'     => '5',
        'file_types'        => '.pdf,.doc,.docx,.jpg,.png',
        'store_submissions' => false,
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
        $uid = 'olo-form-' . wp_rand( 10000, 99999 );

        $fields = is_array( $s['fields'] ) ? $s['fields'] : [];
        if ( empty( $fields ) ) {
            return '';
        }

        // ── Settings ──
        $gap         = absint( $s['gap'] ) ?: 16;
        $bw          = absint( $s['input_border_width'] );
        $radius      = $this->build_border_radius_css( $s["input_radius"] );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['input_radius_hover'] ?? null );
        $label_color = $this->safe_color_css( $s['label_color'] ) ?: 'var(--olo-color-text, #374151)';
        $label_size  = absint( $s['label_size'] ) ?: 14;
        $label_weight= $s['label_weight'] ?: '500';
        $input_bg    = $this->safe_color_css( $s['input_bg'] ) ?: 'var(--olo-color-background, #FFFFFF)';
        $input_color = $this->safe_color_css( $s['input_color'] ) ?: 'var(--olo-color-text, #374151)';
        $input_bc    = $this->safe_color_css( $s['input_border_color'] ) ?: 'var(--olo-color-border, #E5E7EB)';
        $focus_bc    = $this->safe_color_css( $s['input_focus_border'] );
        $focus_shadow= ! empty( $s['input_focus_shadow'] );
        $ph_opacity  = floatval( $s['input_placeholder_opacity'] ) ?: 0.4;
        $btn_bg      = $this->safe_color_css( $s['submit_bg'] );
        $btn_color   = $this->safe_color_css( $s['submit_color'] );
        $btn_hover   = $this->safe_color_css( $s['submit_hover_bg'] );
        $btn_radius  = $this->build_border_radius_css( $s["submit_radius"] );
        $btn_radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['submit_radius_hover'] ?? null );
        $btn_px      = absint( $s['submit_padding_x'] ) ?: 32;
        $btn_py      = absint( $s['submit_padding_y'] ) ?: 14;
        $btn_fs      = absint( $s['submit_font_size'] ) ?: 16;
        $btn_fw      = absint( $s['submit_font_weight'] ) ?: 600;
        $btn_full    = ! empty( $s['submit_full_width'] );
        $btn_bw      = absint( $s['submit_border_width'] );
        $btn_bc      = $this->safe_color_css( $s['submit_border_color'] );
        $btn_hbc     = $this->safe_color_css( $s['submit_hover_border_color'] );
        $btn_ls      = floatval( $s['submit_letter_spacing'] );
        $btn_tt      = in_array( $s['submit_text_transform'], [ 'none', 'uppercase', 'lowercase', 'capitalize' ], true ) ? $s['submit_text_transform'] : 'none';
        $btn_icon    = sanitize_text_field( $s['submit_icon'] ?? '' );
        $btn_icon_pos= $s['submit_icon_pos'] ?? 'left';
        $chk_accent  = $this->safe_color_css( $s['check_accent_color'] );
        $chk_bg      = $this->safe_color_css( $s['check_bg'] );
        $chk_bc      = $this->safe_color_css( $s['check_border_color'] );
        $chk_size    = absint( $s['check_size'] ) ?: 18;
        $chk_gap     = absint( $s['check_label_gap'] ) ?: 8;
        $is_floating = ( $s['form_layout'] === 'floating' );
        $max_w       = absint( $s['form_max_width'] );
        $form_align  = $s['form_align'] ?? 'left';
        $succ_anim   = sanitize_text_field( $s['success_animation'] ?? 'slide-top-small' );
        $err_anim    = sanitize_text_field( $s['error_animation'] ?? 'shake' );

        $size_class = '';
        if ( $s['input_size'] === 'small' )  $size_class = ' uk-form-small';
        if ( $s['input_size'] === 'large' )  $size_class = ' uk-form-large';

        // Multi-step setup
        $is_multistep = ! empty( $s['enable_multistep'] );
        $step_style   = $s['step_style'] ?? 'progress';
        $has_conditions = ! empty( $s['enable_conditions'] );
        $has_recaptcha  = ! empty( $s['recaptcha_enabled'] );
        $recaptcha_site_key = '';
        if ( $has_recaptcha ) {
            $recaptcha_site_key = get_option( 'olo_recaptcha_site_key', '' );
            if ( ! $recaptcha_site_key ) {
                $has_recaptcha = false;
            }
        }

        // Count steps (fields before first 'step' separator = step 1, each separator starts a new step)
        $step_count = 1;
        if ( $is_multistep ) {
            foreach ( $fields as $f ) {
                if ( ( $f['field_type'] ?? '' ) === 'step' ) {
                    $step_count++;
                }
            }
            if ( $step_count < 2 ) {
                $is_multistep = false; // no step separators found, fallback to single form
            }
        }

        // Parse step labels — one per line (textarea), fallback to comma
        $step_labels_arr = [];
        if ( $is_multistep ) {
            $raw_labels = $s['step_labels'] ?? '';
            if ( str_contains( $raw_labels, "\n" ) ) {
                $step_labels_arr = array_map( 'trim', explode( "\n", $raw_labels ) );
            } else {
                $step_labels_arr = array_map( 'trim', explode( ',', $raw_labels ) );
            }
            $step_labels_arr = array_values( array_filter( $step_labels_arr, function( $v ) { return $v !== ''; } ) );
        }

        // Form config as JSON for the AJAX handler
        $form_config = wp_json_encode( [
            'email_to'          => $s['email_to'],
            'email_cc'          => $s['email_cc'],
            'email_from_name'   => $s['email_from_name'],
            'email_subject'     => $s['email_subject'],
            'success_message'   => $s['success_message'],
            'error_message'     => $s['error_message'],
            'redirect_url'      => $s['redirect_url'],
            'honeypot'          => ! empty( $s['honeypot'] ),
            'rate_limit'        => ! empty( $s['rate_limit'] ),
            'rate_limit_max'    => absint( $s['rate_limit_max'] ),
            'rate_limit_window' => absint( $s['rate_limit_window'] ),
            'auto_reply'        => ! empty( $s['auto_reply'] ),
            'auto_reply_subject'=> $s['auto_reply_subject'],
            'auto_reply_message'=> $s['auto_reply_message'],
            'file_max_size'     => absint( $s['file_max_size'] ),
            'file_types'        => $s['file_types'],
            'store_submissions' => ! empty( $s['store_submissions'] ),
            'recaptcha_enabled' => $has_recaptcha,
            'mailchimp_enabled' => ! empty( $s['mailchimp_enabled'] ),
            'mailchimp_list_id' => sanitize_text_field( $s['mailchimp_list_id'] ?? '' ),
            'mailchimp_email_field' => sanitize_key( $s['mailchimp_email_field'] ?? 'email' ),
            'mailchimp_merge_fields' => sanitize_textarea_field( $s['mailchimp_merge_fields'] ?? '' ),
            'webhook_enabled'   => ! empty( $s['webhook_enabled'] ),
            'webhook_url'       => esc_url_raw( $s['webhook_url'] ?? '' ),
            'webhook_method'    => in_array( $s['webhook_method'] ?? 'POST', [ 'POST', 'PUT' ], true ) ? $s['webhook_method'] : 'POST',
        ] );

        // Container style
        $container_style = '';
        if ( $max_w > 0 ) {
            $container_style .= 'max-width:' . $max_w . 'px;';
            if ( $form_align === 'center' ) $container_style .= 'margin-left:auto;margin-right:auto;';
            elseif ( $form_align === 'right' ) $container_style .= 'margin-left:auto;';
        }

        // ── Split fields into steps ──
        $steps = [ [] ]; // array of arrays of fields
        if ( $is_multistep ) {
            $current_step_idx = 0;
            foreach ( $fields as $i => $field ) {
                $ftype = $field['field_type'] ?? 'text';
                if ( $ftype === 'step' ) {
                    $current_step_idx++;
                    $steps[ $current_step_idx ] = [];
                } else {
                    $steps[ $current_step_idx ][] = $field;
                }
            }
        } else {
            // Single step: all fields (excluding step separators)
            foreach ( $fields as $field ) {
                if ( ( $field['field_type'] ?? 'text' ) !== 'step' ) {
                    $steps[0][] = $field;
                }
            }
        }

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> .olo-f-label{color:<?php echo $label_color; ?>;font-size:<?php echo $label_size; ?>px;font-weight:<?php echo $label_weight; ?>;margin-bottom:6px;display:block}
            .<?php echo $uid; ?> .olo-f-required{color:currentColor;opacity:.65;margin-left:2px;font-weight:700}
            .<?php echo $uid; ?> .uk-input,
            .<?php echo $uid; ?> .uk-textarea,
            .<?php echo $uid; ?> .uk-select{background-color:<?php echo $input_bg; ?>;color:<?php echo $input_color; ?>;border:<?php echo $bw; ?>px solid <?php echo $input_bc; ?>;border-radius:<?php echo $radius; ?>;transition:border-radius 400ms cubic-bezier(.4,0,.2,1),border-color 0.15s ease}
            <?php if ( $radius_hover_css !== '' ) : ?>.<?php echo $uid; ?> .uk-input:hover,.<?php echo $uid; ?> .uk-textarea:hover,.<?php echo $uid; ?> .uk-select:hover{border-radius:<?php echo $radius_hover_css; ?> !important}<?php endif; ?>
            .<?php echo $uid; ?> .uk-input:focus,
            .<?php echo $uid; ?> .uk-textarea:focus,
            .<?php echo $uid; ?> .uk-select:focus{border-color:<?php echo $focus_bc ?: 'var(--olo-color-primary, #e1474f)'; ?>;outline:none<?php if ( $focus_shadow ) : ?>;box-shadow:0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 15%, transparent)<?php endif; ?>}
            .<?php echo $uid; ?> .uk-input::placeholder,
            .<?php echo $uid; ?> .uk-textarea::placeholder{color:<?php echo $input_color; ?>;opacity:<?php echo $ph_opacity; ?>}
            .<?php echo $uid; ?> .uk-input:-webkit-autofill,
            .<?php echo $uid; ?> .uk-textarea:-webkit-autofill,
            .<?php echo $uid; ?> .uk-select:-webkit-autofill{-webkit-box-shadow:0 0 0 1000px <?php echo $input_bg; ?> inset !important;-webkit-text-fill-color:<?php echo $input_color; ?> !important;transition:background-color 5000s ease-in-out 0s}
            .<?php echo $uid; ?> .uk-form-icon{color:<?php echo $input_color; ?>;opacity:0.5}
            .<?php echo $uid; ?> .uk-form-icon:hover{opacity:0.8}
            .<?php echo $uid; ?> .olo-f-btn{background:<?php echo $btn_bg ?: 'var(--olo-color-primary, #e1474f)'; ?>;color:<?php echo $btn_color; ?>;<?php if ( $btn_bw > 0 ) : ?>border:<?php echo $btn_bw; ?>px solid <?php echo $btn_bc ?: 'var(--olo-color-primary, #e1474f)'; ?><?php else : ?>border:none<?php endif; ?>;border-radius:<?php echo $btn_radius; ?>;padding:<?php echo $btn_py; ?>px <?php echo $btn_px; ?>px;font-size:<?php echo $btn_fs; ?>px;font-weight:<?php echo $btn_fw; ?>;cursor:pointer;transition:background 0.2s ease,border-color 0.2s ease,transform 0.15s ease;display:inline-flex;align-items:center;gap:8px<?php if ( $btn_ls > 0 ) : ?>;letter-spacing:<?php echo $btn_ls; ?>px<?php endif; ?><?php if ( $btn_tt !== 'none' ) : ?>;text-transform:<?php echo $btn_tt; ?><?php endif; ?><?php if ( $btn_full ) : ?>;width:100%;justify-content:center<?php endif; ?>}
            .<?php echo $uid; ?> .olo-f-btn:hover{background:<?php echo $btn_hover ?: 'color-mix(in srgb, var(--olo-color-primary, #e1474f) 85%, #000)'; ?><?php if ( $btn_bw > 0 && $btn_hbc ) : ?>;border-color:<?php echo $btn_hbc; ?><?php endif; ?><?php if ( $btn_radius_hover_css !== '' ) : ?>;border-radius:<?php echo $btn_radius_hover_css; ?> !important<?php endif; ?>}
            .<?php echo $uid; ?> .olo-f-btn:focus-visible{outline:none;box-shadow:0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent)}
            .<?php echo $uid; ?> .olo-f-btn:active{transform:translateY(1px)}
            .<?php echo $uid; ?> .olo-f-btn:disabled{opacity:0.6;cursor:not-allowed}
            .<?php echo $uid; ?> .olo-f-msg{margin-top:12px;padding:12px 16px;border-radius:<?php echo $radius; ?>;font-size:14px;display:none}
            .<?php echo $uid; ?> .olo-f-msg--success{background:color-mix(in srgb, var(--olo-color-success, #10B981) 15%, transparent);color:var(--olo-color-success, #10B981);border:1px solid color-mix(in srgb, var(--olo-color-success, #10B981) 30%, transparent)}
            .<?php echo $uid; ?> .olo-f-msg--error{background:color-mix(in srgb, var(--olo-color-danger, #EF4444) 15%, transparent);color:var(--olo-color-danger, #EF4444);border:1px solid color-mix(in srgb, var(--olo-color-danger, #EF4444) 30%, transparent)}
            .<?php echo $uid; ?> .olo-f-sending .olo-f-btn-text{display:none}
            .<?php echo $uid; ?> .olo-f-sending .olo-f-btn-loading{display:inline-flex;align-items:center;gap:8px}
            .<?php echo $uid; ?> .olo-f-btn-loading{display:none}
            .<?php echo $uid; ?> .uk-radio,
            .<?php echo $uid; ?> .uk-checkbox{width:<?php echo $chk_size; ?>px;height:<?php echo $chk_size; ?>px<?php if ( $chk_bc ) : ?>;border-color:<?php echo $chk_bc; ?><?php endif; ?>;flex-shrink:0}
            .<?php echo $uid; ?> .uk-checkbox:checked,
            .<?php echo $uid; ?> .uk-radio:checked{background-color:<?php echo $chk_accent ?: 'var(--olo-color-primary, #e1474f)'; ?>}
            .<?php echo $uid; ?> .uk-checkbox:focus,
            .<?php echo $uid; ?> .uk-radio:focus{border-color:<?php echo $chk_accent ?: 'var(--olo-color-primary, #e1474f)'; ?>}
            .<?php echo $uid; ?> .olo-f-option{display:flex;align-items:center;gap:<?php echo $chk_gap; ?>px;color:<?php echo $label_color; ?>;font-size:<?php echo $label_size; ?>px;cursor:pointer}
            .<?php echo $uid; ?> .olo-f-privacy{margin-top:<?php echo $gap; ?>px}
            .<?php echo $uid; ?> .olo-f-privacy a{color:var(--olo-color-primary, #e1474f);text-decoration:underline}
            <?php if ( $is_floating ) : ?>
            .<?php echo $uid; ?> .olo-f-float{position:relative}
            .<?php echo $uid; ?> .olo-f-float-label{position:absolute;top:50%;left:14px;transform:translateY(-50%);color:<?php echo $label_color; ?>;font-size:<?php echo $label_size; ?>px;pointer-events:none;transition:all 0.2s ease;opacity:0.6}
            .<?php echo $uid; ?> .olo-f-float textarea ~ .olo-f-float-label{top:14px;transform:none}
            .<?php echo $uid; ?> .olo-f-float input:focus ~ .olo-f-float-label,
            .<?php echo $uid; ?> .olo-f-float input:not(:placeholder-shown) ~ .olo-f-float-label,
            .<?php echo $uid; ?> .olo-f-float textarea:focus ~ .olo-f-float-label,
            .<?php echo $uid; ?> .olo-f-float textarea:not(:placeholder-shown) ~ .olo-f-float-label{top:4px;left:12px;font-size:11px;opacity:1;transform:none}
            .<?php echo $uid; ?> .olo-f-float .uk-form-icon ~ .olo-f-float-label{left:38px}
            .<?php echo $uid; ?> .olo-f-float input:focus ~ .olo-f-float-label,
            .<?php echo $uid; ?> .olo-f-float input:not(:placeholder-shown) ~ .olo-f-float-label{left:12px}
            <?php endif; ?>
            @keyframes olo-shake{0%,100%{transform:translateX(0)}10%,30%,50%,70%,90%{transform:translateX(-4px)}20%,40%,60%,80%{transform:translateX(4px)}}
            .<?php echo $uid; ?> .olo-f-anim-shake{animation:olo-shake 0.5s ease}
            <?php if ( $is_multistep ) : ?>
            .<?php echo $uid; ?> .olo-f-step-page{display:none}
            .<?php echo $uid; ?> .olo-f-step-page.active{display:block}
            .<?php echo $uid; ?> .olo-f-step-nav{display:flex;gap:12px;margin-top:20px}
            .<?php echo $uid; ?> .olo-f-step-nav .olo-f-btn{flex:1}
            .<?php echo $uid; ?> .olo-f-progress-bar{height:4px;background:<?php echo $input_bc; ?>;border-radius:2px;margin-bottom:24px;overflow:hidden}
            .<?php echo $uid; ?> .olo-f-progress-fill{height:100%;background:<?php echo $btn_bg ?: 'var(--olo-color-primary, #e1474f)'; ?>;transition:width 0.3s ease;border-radius:2px}
            .<?php echo $uid; ?> .olo-f-steps-indicator{display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:24px;flex-wrap:wrap}
            .<?php echo $uid; ?> .olo-f-step-item{display:flex;flex-direction:column;align-items:center}
            .<?php echo $uid; ?> .olo-f-step-dot{width:12px;height:12px;border-radius:50%;background:<?php echo $input_bc; ?>;transition:background 0.3s ease,transform 0.3s ease}
            .<?php echo $uid; ?> .olo-f-step-dot.active{background:<?php echo $btn_bg ?: 'var(--olo-color-primary, #e1474f)'; ?>;transform:scale(1.3)}
            .<?php echo $uid; ?> .olo-f-step-dot.completed{background:<?php echo $btn_bg ?: 'var(--olo-color-primary, #e1474f)'; ?>;opacity:0.6}
            .<?php echo $uid; ?> .olo-f-step-line{width:32px;height:2px;background:<?php echo $input_bc; ?>}
            .<?php echo $uid; ?> .olo-f-step-line.completed{background:<?php echo $btn_bg ?: 'var(--olo-color-primary, #e1474f)'; ?>}
            .<?php echo $uid; ?> .olo-f-step-label{display:block;font-size:11px;color:<?php echo $label_color; ?>;text-align:center;margin-top:4px;opacity:0.6}
            .<?php echo $uid; ?> .olo-f-step-item.active .olo-f-step-label{opacity:1}
            .<?php echo $uid; ?> .olo-f-step-num{width:32px;height:32px;border-radius:50%;background:<?php echo $input_bc; ?>;color:<?php echo $label_color; ?>;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:600;transition:background 0.3s ease,color 0.3s ease}
            .<?php echo $uid; ?> .olo-f-step-num.active{background:<?php echo $btn_bg ?: 'var(--olo-color-primary, #e1474f)'; ?>;color:<?php echo $btn_color; ?>}
            .<?php echo $uid; ?> .olo-f-step-num.completed{background:<?php echo $btn_bg ?: 'var(--olo-color-primary, #e1474f)'; ?>;color:<?php echo $btn_color; ?>;opacity:0.7}
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-f-cond-hidden{display:none !important}
            .<?php echo $uid; ?> .olo-f-file-btn:hover{border-color:<?php echo $focus_bc ?: 'var(--olo-color-primary, #e1474f)'; ?>}
            .<?php echo $uid; ?> .olo-f-file-list-item{display:flex;align-items:center;gap:6px;padding:4px 0}
            .<?php echo $uid; ?> .olo-f-file-remove{color:var(--olo-color-danger, #EF4444);cursor:pointer;font-size:18px;line-height:1;border:none;background:none;padding:0 4px}
            .<?php echo $uid; ?> .olo-f-file-remove:hover{opacity:0.7}
        </style>

        <div class="olo-form <?php echo esc_attr( $uid ); ?> olo-fm-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>"<?php if ( $container_style ) : ?> style="<?php echo esc_attr( $container_style ); ?>"<?php endif; ?>>
            <?php
            // Token bound to the config: any tampering on email_to / email_subject /
            // auto_reply_message will fail HMAC verification server-side.
            $config_b64 = base64_encode( $form_config );
            $form_token = Olo_Form_Handler::generate_token( $config_b64 );
            ?>
            <form class="uk-form-stacked" data-olo-form="<?php echo esc_attr( $uid ); ?>" enctype="multipart/form-data">
                <input type="hidden" name="_olo_form_config" value="<?php echo esc_attr( $config_b64 ); ?>" />
                <input type="hidden" name="_olo_form_token" value="<?php echo esc_attr( $form_token ); ?>" />
                <input type="hidden" name="_olo_form_id" value="<?php echo esc_attr( $uid ); ?>" />

                <?php if ( ! empty( $s['honeypot'] ) ) : ?>
                <div style="position:absolute;left:-9999px;opacity:0" aria-hidden="true">
                    <input type="text" name="olo_website_url" tabindex="-1" autocomplete="off" />
                </div>
                <?php endif; ?>

                <?php
                // ── Multi-step progress indicator ──
                if ( $is_multistep ) :
                    if ( $step_style === 'progress' ) : ?>
                        <div class="olo-f-progress-bar" data-olo-steps="<?php echo $step_count; ?>">
                            <div class="olo-f-progress-fill" style="width:<?php echo round( 100 / $step_count, 2 ); ?>%"></div>
                        </div>
                    <?php elseif ( $step_style === 'numbers' ) : ?>
                        <div class="olo-f-steps-indicator" data-olo-steps="<?php echo $step_count; ?>">
                            <?php for ( $si = 0; $si < $step_count; $si++ ) : ?>
                                <?php if ( $si > 0 ) : ?><div class="olo-f-step-line"></div><?php endif; ?>
                                <div class="olo-f-step-item<?php echo $si === 0 ? ' active' : ''; ?>">
                                    <div class="olo-f-step-num<?php echo $si === 0 ? ' active' : ''; ?>"><?php echo ( $si + 1 ); ?></div>
                                    <?php if ( ! empty( $step_labels_arr[ $si ] ) ) : ?>
                                        <span class="olo-f-step-label"><?php echo esc_html( $step_labels_arr[ $si ] ); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endfor; ?>
                        </div>
                    <?php else : /* dots */ ?>
                        <div class="olo-f-steps-indicator" data-olo-steps="<?php echo $step_count; ?>">
                            <?php for ( $si = 0; $si < $step_count; $si++ ) : ?>
                                <?php if ( $si > 0 ) : ?><div class="olo-f-step-line"></div><?php endif; ?>
                                <div class="olo-f-step-item<?php echo $si === 0 ? ' active' : ''; ?>">
                                    <div class="olo-f-step-dot<?php echo $si === 0 ? ' active' : ''; ?>"></div>
                                    <?php if ( ! empty( $step_labels_arr[ $si ] ) ) : ?>
                                        <span class="olo-f-step-label"><?php echo esc_html( $step_labels_arr[ $si ] ); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endfor; ?>
                        </div>
                    <?php endif;
                endif; // is_multistep indicator
                ?>

                <?php
                // ── Render steps ──
                foreach ( $steps as $step_idx => $step_fields ) :
                    if ( $is_multistep ) : ?>
                        <div class="olo-f-step-page<?php echo $step_idx === 0 ? ' active' : ''; ?>" data-step="<?php echo $step_idx; ?>">
                    <?php endif; ?>

                    <div class="uk-grid-small" uk-grid style="row-gap:<?php echo $gap; ?>px">
                        <?php foreach ( $step_fields as $i => $field ) :
                            $ftype       = $field['field_type'] ?? 'text';
                            $flabel      = $field['label'] ?? '';
                            $fname       = sanitize_key( $field['name'] ?? 'field_' . $step_idx . '_' . $i );
                            $fplaceholder= $field['placeholder'] ?? '';
                            $frequired   = ! empty( $field['required'] );
                            $fwidth      = $field['width'] ?? '1-1';
                            $foptions    = $field['options'] ?? '';
                            $ficon       = sanitize_text_field( $field['icon'] ?? '' );
                            $req_attr    = $frequired ? ' required' : '';

                            // Conditional logic data attributes
                            $cond_attrs  = '';
                            $cond_hidden = false;
                            if ( $has_conditions ) {
                                $cond_field = $field['condition_field'] ?? '';
                                $cond_op    = $field['condition_operator'] ?? '';
                                $cond_value = $field['condition_value'] ?? '';
                                if ( $cond_field !== '' ) {
                                    $cond_attrs = ' data-cond-field="' . esc_attr( $cond_field ) . '"'
                                        . ' data-cond-op="' . esc_attr( $cond_op ) . '"'
                                        . ' data-cond-value="' . esc_attr( $cond_value ) . '"';
                                    $cond_hidden = true; // initially hidden, JS will evaluate
                                }
                            }

                            if ( $ftype === 'hidden' ) : ?>
                                <input type="hidden" name="fields[<?php echo esc_attr( $fname ); ?>]" value="<?php echo esc_attr( $fplaceholder ); ?>" />
                            <?php continue; endif;

                            // Width class
                            $width_class = 'uk-width-1-1';
                            if ( in_array( $fwidth, [ '1-2', '1-3', '2-3', '1-4', '3-4' ], true ) ) {
                                $width_class = 'uk-width-' . $fwidth . '@s';
                            }

                            $cond_class = $cond_hidden ? ' olo-f-cond-hidden' : '';
                        ?>
                        <div class="<?php echo esc_attr( $width_class . $cond_class ); ?>"<?php echo $cond_attrs; ?> data-field-name="<?php echo esc_attr( $fname ); ?>">
                            <?php if ( in_array( $ftype, [ 'text', 'email', 'tel', 'url', 'number', 'date', 'time', 'datetime' ], true ) ) :
                                $html_type = ( $ftype === 'datetime' ) ? 'datetime-local' : $ftype;
                                $extra_attrs = '';
                                if ( $ftype === 'tel' ) {
                                    $extra_attrs .= ' pattern="[+]?[0-9\\s\\-().]{6,20}" title="Inserisci un numero di telefono valido"';
                                }
                                if ( $ftype === 'url' ) {
                                    $extra_attrs .= ' title="Inserisci un URL valido (es. https://esempio.com)"';
                                }
                            ?>
                                <?php if ( $is_floating ) : ?>
                                    <div class="olo-f-float">
                                        <?php if ( $ficon ) : ?>
                                        <div class="uk-inline uk-width-1-1">
                                            <span class="uk-form-icon" uk-icon="icon: <?php echo esc_attr( $ficon ); ?>"></span>
                                            <input type="<?php echo esc_attr( $html_type ); ?>" name="fields[<?php echo esc_attr( $fname ); ?>]" class="uk-input<?php echo $size_class; ?>" placeholder=" "<?php echo $req_attr; ?><?php echo $extra_attrs; ?> />
                                            <?php if ( $flabel ) : ?>
                                                <label class="olo-f-float-label"><?php echo esc_html( $flabel ); ?><?php if ( $frequired ) : ?><span class="olo-f-required">*</span><?php endif; ?></label>
                                            <?php endif; ?>
                                        </div>
                                        <?php else : ?>
                                        <input type="<?php echo esc_attr( $html_type ); ?>" name="fields[<?php echo esc_attr( $fname ); ?>]" class="uk-input<?php echo $size_class; ?>" placeholder=" "<?php echo $req_attr; ?><?php echo $extra_attrs; ?> />
                                        <?php if ( $flabel ) : ?>
                                            <label class="olo-f-float-label"><?php echo esc_html( $flabel ); ?><?php if ( $frequired ) : ?><span class="olo-f-required">*</span><?php endif; ?></label>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                <?php else : ?>
                                    <?php if ( $flabel ) : ?>
                                        <label class="olo-f-label"><?php echo esc_html( $flabel ); ?><?php if ( $frequired ) : ?><span class="olo-f-required">*</span><?php endif; ?></label>
                                    <?php endif; ?>
                                    <?php if ( $ficon ) : ?>
                                    <div class="uk-inline uk-width-1-1">
                                        <span class="uk-form-icon" uk-icon="icon: <?php echo esc_attr( $ficon ); ?>"></span>
                                        <input type="<?php echo esc_attr( $html_type ); ?>" name="fields[<?php echo esc_attr( $fname ); ?>]" class="uk-input<?php echo $size_class; ?>" placeholder="<?php echo esc_attr( $fplaceholder ); ?>"<?php echo $req_attr; ?><?php echo $extra_attrs; ?> />
                                    </div>
                                    <?php else : ?>
                                    <input type="<?php echo esc_attr( $html_type ); ?>" name="fields[<?php echo esc_attr( $fname ); ?>]" class="uk-input<?php echo $size_class; ?>" placeholder="<?php echo esc_attr( $fplaceholder ); ?>"<?php echo $req_attr; ?><?php echo $extra_attrs; ?> />
                                    <?php endif; ?>
                                <?php endif; ?>

                            <?php elseif ( $ftype === 'textarea' ) : ?>
                                <?php if ( $is_floating ) : ?>
                                    <div class="olo-f-float">
                                        <textarea name="fields[<?php echo esc_attr( $fname ); ?>]" class="uk-textarea<?php echo $size_class; ?>" rows="4" placeholder=" "<?php echo $req_attr; ?>></textarea>
                                        <?php if ( $flabel ) : ?>
                                            <label class="olo-f-float-label"><?php echo esc_html( $flabel ); ?><?php if ( $frequired ) : ?><span class="olo-f-required">*</span><?php endif; ?></label>
                                        <?php endif; ?>
                                    </div>
                                <?php else : ?>
                                    <?php if ( $flabel ) : ?>
                                        <label class="olo-f-label"><?php echo esc_html( $flabel ); ?><?php if ( $frequired ) : ?><span class="olo-f-required">*</span><?php endif; ?></label>
                                    <?php endif; ?>
                                    <textarea name="fields[<?php echo esc_attr( $fname ); ?>]" class="uk-textarea<?php echo $size_class; ?>" rows="4" placeholder="<?php echo esc_attr( $fplaceholder ); ?>"<?php echo $req_attr; ?>></textarea>
                                <?php endif; ?>

                            <?php elseif ( $ftype === 'select' ) : ?>
                                <?php if ( $flabel ) : ?>
                                    <label class="olo-f-label"><?php echo esc_html( $flabel ); ?><?php if ( $frequired ) : ?><span class="olo-f-required">*</span><?php endif; ?></label>
                                <?php endif; ?>
                                <?php if ( $ficon ) : ?>
                                <div class="uk-inline uk-width-1-1">
                                    <span class="uk-form-icon" uk-icon="icon: <?php echo esc_attr( $ficon ); ?>"></span>
                                    <select name="fields[<?php echo esc_attr( $fname ); ?>]" class="uk-select<?php echo $size_class; ?>"<?php echo $req_attr; ?>>
                                        <option value=""><?php echo esc_html( $fplaceholder ?: 'Seleziona...' ); ?></option>
                                        <?php foreach ( $this->parse_options( $foptions ) as $opt ) : ?>
                                            <option value="<?php echo esc_attr( $opt ); ?>"><?php echo esc_html( $opt ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php else : ?>
                                <select name="fields[<?php echo esc_attr( $fname ); ?>]" class="uk-select<?php echo $size_class; ?>"<?php echo $req_attr; ?>>
                                    <option value=""><?php echo esc_html( $fplaceholder ?: 'Seleziona...' ); ?></option>
                                    <?php foreach ( $this->parse_options( $foptions ) as $opt ) : ?>
                                        <option value="<?php echo esc_attr( $opt ); ?>"><?php echo esc_html( $opt ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php endif; ?>

                            <?php elseif ( $ftype === 'radio' ) : ?>
                                <?php if ( $flabel ) : ?>
                                    <label class="olo-f-label"><?php echo esc_html( $flabel ); ?><?php if ( $frequired ) : ?><span class="olo-f-required">*</span><?php endif; ?></label>
                                <?php endif; ?>
                                <div class="uk-margin-small-top">
                                    <?php foreach ( $this->parse_options( $foptions ) as $opt ) : ?>
                                        <label class="olo-f-option" style="margin-bottom:6px">
                                            <input type="radio" class="uk-radio" name="fields[<?php echo esc_attr( $fname ); ?>]" value="<?php echo esc_attr( $opt ); ?>"<?php echo $req_attr; ?> />
                                            <?php echo esc_html( $opt ); ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>

                            <?php elseif ( $ftype === 'checkbox' ) : ?>
                                <?php if ( $flabel ) : ?>
                                    <label class="olo-f-label"><?php echo esc_html( $flabel ); ?><?php if ( $frequired ) : ?><span class="olo-f-required">*</span><?php endif; ?></label>
                                <?php endif; ?>
                                <div class="uk-margin-small-top"<?php if ( $frequired ) : ?> data-olo-check-required<?php endif; ?>>
                                    <?php foreach ( $this->parse_options( $foptions ) as $opt ) : ?>
                                        <label class="olo-f-option" style="margin-bottom:6px">
                                            <input type="checkbox" class="uk-checkbox" name="fields[<?php echo esc_attr( $fname ); ?>][]" value="<?php echo esc_attr( $opt ); ?>" />
                                            <?php echo esc_html( $opt ); ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>

                            <?php elseif ( $ftype === 'file' ) : ?>
                                <?php if ( $flabel ) : ?>
                                    <label class="olo-f-label"><?php echo esc_html( $flabel ); ?><?php if ( $frequired ) : ?><span class="olo-f-required">*</span><?php endif; ?></label>
                                <?php endif; ?>
                                <?php
                                    $f_allowed  = esc_attr( $field['file_allowed_types'] ?? $s['file_types'] ?? '.pdf,.doc,.docx,.jpg,.png' );
                                    $f_max_size = absint( $field['file_max_size'] ?? $s['file_max_size'] ?? 5 );
                                    $f_max_files= absint( $field['file_max_files'] ?? 1 );
                                    $f_btn_text = esc_html( $field['file_button_text'] ?? 'Scegli file' );
                                    $f_multiple = ( $f_max_files > 1 ) ? ' multiple' : '';
                                    $f_input_id = 'olo-file-' . esc_attr( $fname ) . '-' . wp_rand( 1000, 9999 );
                                    $f_input_name = ( $f_max_files > 1 ) ? esc_attr( $fname ) . '[]' : esc_attr( $fname );
                                ?>
                                <div class="olo-f-file-wrap" data-max-size="<?php echo $f_max_size; ?>" data-max-files="<?php echo $f_max_files; ?>" data-allowed="<?php echo $f_allowed; ?>">
                                    <input type="file" id="<?php echo $f_input_id; ?>" name="<?php echo $f_input_name; ?>" class="olo-f-file-input" accept="<?php echo $f_allowed; ?>"<?php echo $f_multiple; ?><?php echo $req_attr; ?> style="position:absolute;left:-9999px;opacity:0;" />
                                    <label for="<?php echo $f_input_id; ?>" class="olo-f-file-btn" style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:<?php echo $input_bg; ?>;color:<?php echo $input_color; ?>;border:<?php echo $bw; ?>px solid <?php echo $input_bc; ?>;border-radius:<?php echo $radius; ?>;cursor:pointer;font-size:14px;transition:border-color 0.2s ease;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                        <?php echo $f_btn_text; ?>
                                    </label>
                                    <span class="olo-f-file-info" style="display:inline-block;margin-left:12px;font-size:13px;color:<?php echo $label_color; ?>;opacity:0.7;"><?php echo esc_html( olo_t( 'Nessun file selezionato' ) ); ?></span>
                                    <div class="olo-f-file-list" style="margin-top:8px;font-size:13px;color:<?php echo $label_color; ?>;"></div>
                                    <div class="olo-f-file-error" style="color:var(--olo-color-danger, #EF4444);font-size:12px;margin-top:4px;display:none;"></div>
                                    <input type="hidden" name="fields[<?php echo esc_attr( $fname ); ?>]" value="" class="olo-f-file-meta" />
                                </div>

                            <?php elseif ( $ftype === 'range' ) : ?>
                                <?php if ( $flabel ) : ?>
                                    <label class="olo-f-label"><?php echo esc_html( $flabel ); ?><?php if ( $frequired ) : ?><span class="olo-f-required">*</span><?php endif; ?></label>
                                <?php endif; ?>
                                <?php
                                    $range_min  = intval( $field['range_min'] ?? 0 );
                                    $range_max  = intval( $field['range_max'] ?? 100 );
                                    $range_step = intval( $field['range_step'] ?? 1 );
                                    $range_def  = intval( $field['range_default'] ?? 50 );
                                    $range_id   = 'olo-range-' . esc_attr( $fname ) . '-' . wp_rand( 1000, 9999 );
                                ?>
                                <div class="olo-f-range-wrap" style="display:flex;align-items:center;gap:12px">
                                    <input type="range" id="<?php echo $range_id; ?>" name="fields[<?php echo esc_attr( $fname ); ?>]" class="uk-range" min="<?php echo $range_min; ?>" max="<?php echo $range_max; ?>" step="<?php echo $range_step; ?>" value="<?php echo $range_def; ?>" style="flex:1" oninput="this.nextElementSibling.textContent=this.value" />
                                    <span style="min-width:40px;text-align:center;font-size:14px;color:<?php echo $label_color; ?>"><?php echo $range_def; ?></span>
                                </div>

                            <?php elseif ( $ftype === 'star_rating' ) : ?>
                                <?php if ( $flabel ) : ?>
                                    <label class="olo-f-label"><?php echo esc_html( $flabel ); ?><?php if ( $frequired ) : ?><span class="olo-f-required">*</span><?php endif; ?></label>
                                <?php endif; ?>
                                <?php
                                    $max_stars = intval( $field['star_count'] ?? 5 );
                                    $star_id = 'olo-star-' . esc_attr( $fname ) . '-' . wp_rand( 1000, 9999 );
                                ?>
                                <div class="olo-f-star-wrap" id="<?php echo $star_id; ?>" style="display:flex;gap:4px;cursor:pointer">
                                    <?php for ( $si = 1; $si <= $max_stars; $si++ ) : ?>
                                        <span class="olo-f-star" data-val="<?php echo $si; ?>" style="font-size:28px;color:#9CA3AF;transition:color .15s" role="button" tabindex="0" aria-label="<?php echo $si; ?> stelle">&#9733;</span>
                                    <?php endfor; ?>
                                    <input type="hidden" name="fields[<?php echo esc_attr( $fname ); ?>]" value=""<?php echo $req_attr; ?> />
                                </div>
                                <script>
                                (function(){
                                    var w=document.getElementById('<?php echo esc_js( $star_id ); ?>');
                                    if(!w)return;
                                    var stars=w.querySelectorAll('.olo-f-star');
                                    var inp=w.querySelector('input[type="hidden"]');
                                    var accent='<?php echo esc_js( $this->safe_color_css( $s['check_accent_color'] ?? '' ) ?: '#F59E0B' ); ?>';
                                    function set(v){
                                        inp.value=v;
                                        stars.forEach(function(s){
                                            s.style.color=parseInt(s.dataset.val)<=v?accent:'#9CA3AF';
                                        });
                                    }
                                    stars.forEach(function(s){
                                        s.addEventListener('click',function(){set(parseInt(s.dataset.val))});
                                        s.addEventListener('keydown',function(e){if(e.key==='Enter'){set(parseInt(s.dataset.val))}});
                                        s.addEventListener('mouseenter',function(){
                                            var val=parseInt(s.dataset.val);
                                            stars.forEach(function(st){st.style.color=parseInt(st.dataset.val)<=val?accent:'#9CA3AF'});
                                        });
                                    });
                                    w.addEventListener('mouseleave',function(){
                                        var cur=parseInt(inp.value)||0;
                                        stars.forEach(function(s){s.style.color=parseInt(s.dataset.val)<=cur?accent:'#9CA3AF'});
                                    });
                                })();
                                </script>

                            <?php elseif ( $ftype === 'password' ) : ?>
                                <?php if ( $flabel ) : ?>
                                    <label class="olo-f-label"><?php echo esc_html( $flabel ); ?><?php if ( $frequired ) : ?><span class="olo-f-required">*</span><?php endif; ?></label>
                                <?php endif; ?>
                                <div class="uk-inline uk-width-1-1" style="position:relative">
                                    <?php if ( $ficon ) : ?>
                                        <span class="uk-form-icon" uk-icon="icon: <?php echo esc_attr( $ficon ); ?>"></span>
                                    <?php endif; ?>
                                    <input type="password" name="fields[<?php echo esc_attr( $fname ); ?>]" class="uk-input<?php echo $size_class; ?>" placeholder="<?php echo esc_attr( $fplaceholder ); ?>"<?php echo $req_attr; ?> autocomplete="new-password" />
                                    <a class="uk-form-icon uk-form-icon-flip" style="cursor:pointer" onclick="var i=this.previousElementSibling;i.type=i.type==='password'?'text':'password'" uk-icon="icon: eye"></a>
                                </div>

                            <?php elseif ( $ftype === 'password_confirm' ) : ?>
                                <?php if ( $flabel ) : ?>
                                    <label class="olo-f-label"><?php echo esc_html( $flabel ); ?><?php if ( $frequired ) : ?><span class="olo-f-required">*</span><?php endif; ?></label>
                                <?php endif; ?>
                                <?php $pc_id = 'olo-pwc-' . esc_attr( $fname ) . '-' . wp_rand( 1000, 9999 ); ?>
                                <div style="display:flex;flex-direction:column;gap:8px">
                                    <div class="uk-inline uk-width-1-1">
                                        <input type="password" name="fields[<?php echo esc_attr( $fname ); ?>]" class="uk-input<?php echo $size_class; ?>" placeholder="<?php echo esc_attr( $fplaceholder ?: 'Password' ); ?>"<?php echo $req_attr; ?> autocomplete="new-password" data-pwc-main="<?php echo $pc_id; ?>" />
                                        <a class="uk-form-icon uk-form-icon-flip" style="cursor:pointer" onclick="var i=this.previousElementSibling;i.type=i.type==='password'?'text':'password'" uk-icon="icon: eye"></a>
                                    </div>
                                    <div class="uk-inline uk-width-1-1">
                                        <input type="password" class="uk-input<?php echo $size_class; ?>" placeholder="<?php echo esc_attr( olo_t( 'Conferma password' ) ); ?>" autocomplete="new-password" data-pwc-confirm="<?php echo $pc_id; ?>" />
                                        <a class="uk-form-icon uk-form-icon-flip" style="cursor:pointer" onclick="var i=this.previousElementSibling;i.type=i.type==='password'?'text':'password'" uk-icon="icon: eye"></a>
                                    </div>
                                    <div class="olo-f-pwc-error" data-pwc-error="<?php echo $pc_id; ?>" style="color:var(--olo-color-danger, #EF4444);font-size:12px;display:none">Le password non corrispondono</div>
                                </div>

                            <?php elseif ( $ftype === 'color' ) : ?>
                                <?php if ( $flabel ) : ?>
                                    <label class="olo-f-label"><?php echo esc_html( $flabel ); ?><?php if ( $frequired ) : ?><span class="olo-f-required">*</span><?php endif; ?></label>
                                <?php endif; ?>
                                <div style="display:flex;align-items:center;gap:10px">
                                    <input type="color" name="fields[<?php echo esc_attr( $fname ); ?>]" value="<?php echo esc_attr( $fplaceholder ?: '#e1474f' ); ?>" style="width:48px;height:40px;padding:2px;border:<?php echo $bw; ?>px solid <?php echo $input_bc; ?>;border-radius:<?php echo $radius; ?>;background:<?php echo $input_bg; ?>;cursor:pointer" oninput="this.nextElementSibling.textContent=this.value" />
                                    <span style="font-size:14px;color:<?php echo $label_color; ?>;font-family:monospace"><?php echo esc_html( $fplaceholder ?: '#e1474f' ); ?></span>
                                </div>

                            <?php elseif ( $ftype === 'calculation' ) :
                                $calc_formula  = $field['calc_formula'] ?? '';
                                $calc_prefix   = esc_html( $field['calc_prefix'] ?? '' );
                                $calc_suffix   = esc_html( $field['calc_suffix'] ?? '' );
                                $calc_decimals = max( 0, intval( $field['calc_decimals'] ?? 2 ) );
                            ?>
                                <?php if ( $flabel ) : ?>
                                    <label class="olo-f-label"><?php echo esc_html( $flabel ); ?></label>
                                <?php endif; ?>
                                <div class="olo-f-calc-display" data-calc-formula="<?php echo esc_attr( $calc_formula ); ?>" data-calc-decimals="<?php echo $calc_decimals; ?>" data-calc-prefix="<?php echo esc_attr( $calc_prefix ); ?>" data-calc-suffix="<?php echo esc_attr( $calc_suffix ); ?>" style="font-size:20px;font-weight:600;padding:10px 16px;background:<?php echo $input_bg; ?>;color:<?php echo $input_c; ?>;border:<?php echo $border_w; ?>px solid <?php echo $border_c; ?>;border-radius:<?php echo $radius; ?>">
                                    <?php echo $calc_prefix; ?><span class="olo-f-calc-value">0</span><?php echo $calc_suffix; ?>
                                </div>
                                <input type="hidden" name="fields[<?php echo esc_attr( $fname ); ?>]" class="olo-f-calc-hidden" value="0" />

                            <?php elseif ( $ftype === 'hidden' ) : ?>
                                <input type="hidden" name="fields[<?php echo esc_attr( $fname ); ?>]" value="<?php echo esc_attr( $field['default_value'] ?? '' ); ?>" />

                            <?php elseif ( $ftype === 'step' ) : ?>
                                <!-- step separator handled by multi-step logic -->

                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ( $is_multistep ) : ?>
                        <div class="olo-f-step-nav">
                            <?php if ( $step_idx > 0 ) : ?>
                                <button type="button" class="olo-f-btn olo-f-step-prev"><?php echo esc_html( olo_t( 'Indietro' ) ); ?></button>
                            <?php endif; ?>
                            <?php if ( $step_idx < $step_count - 1 ) : ?>
                                <button type="button" class="olo-f-btn olo-f-step-next"><?php echo esc_html( olo_t( 'Avanti' ) ); ?></button>
                            <?php endif; ?>
                        </div>
                        </div><?php // close .olo-f-step-page ?>
                    <?php endif; ?>

                <?php endforeach; // steps loop ?>

                <?php if ( ! empty( $s['privacy_checkbox'] ) ) : ?>
                <div class="olo-f-privacy"<?php if ( $is_multistep ) : ?> data-olo-privacy<?php endif; ?>>
                    <label class="olo-f-option">
                        <input type="checkbox" class="uk-checkbox" name="_olo_privacy_consent" required />
                        <span><?php echo wp_kses_post( $s['privacy_text'] ); ?></span>
                    </label>
                </div>
                <?php endif; ?>

                <div class="uk-margin-top olo-f-submit-row" data-olo-submit style="text-align:<?php echo esc_attr( $s['submit_alignment'] ?: 'left' ); ?><?php if ( $is_multistep ) : ?>;display:none<?php endif; ?>">
                    <button type="submit" class="olo-f-btn">
                        <span class="olo-f-btn-text">
                            <?php if ( $btn_icon && $btn_icon_pos === 'left' ) : ?><span uk-icon="icon: <?php echo esc_attr( $btn_icon ); ?>"></span><?php endif; ?>
                            <?php echo esc_html( $s['submit_text'] ?: 'Invia' ); ?>
                            <?php if ( $btn_icon && $btn_icon_pos === 'right' ) : ?><span uk-icon="icon: <?php echo esc_attr( $btn_icon ); ?>"></span><?php endif; ?>
                        </span>
                        <span class="olo-f-btn-loading"><span uk-spinner="ratio: 0.6"></span> Invio in corso…</span>
                    </button>
                </div>

                <div class="olo-f-msg olo-f-msg--success" data-anim="<?php echo esc_attr( $succ_anim ); ?>"></div>
                <div class="olo-f-msg olo-f-msg--error" data-anim="<?php echo esc_attr( $err_anim ); ?>"></div>
            </form>
        </div>

        <?php if ( $has_recaptcha ) : ?>
        <script src="https://www.google.com/recaptcha/api.js?render=<?php echo esc_attr( $recaptcha_site_key ); ?>"></script>
        <?php endif; ?>

        <script>
        (function(){
            var uid = '<?php echo esc_js( $uid ); ?>';
            var wrapper = document.querySelector('.' + uid);
            if (!wrapper) return;
            var form = wrapper.querySelector('[data-olo-form="' + uid + '"]');
            if (!form) return;

            var isMultistep = <?php echo $is_multistep ? 'true' : 'false'; ?>;
            var hasConditions = <?php echo $has_conditions ? 'true' : 'false'; ?>;
            var hasRecaptcha = <?php echo $has_recaptcha ? 'true' : 'false'; ?>;
            var recaptchaSiteKey = '<?php echo $has_recaptcha ? esc_js( $recaptcha_site_key ) : ''; ?>';
            var totalSteps = <?php echo (int) $step_count; ?>;

            // ─── Multi-step state ───
            var currentStep = 0;
            var pages = wrapper.querySelectorAll('.olo-f-step-page');
            var progressFill = wrapper.querySelector('.olo-f-progress-fill');
            var dots = wrapper.querySelectorAll('.olo-f-step-dot');
            var nums = wrapper.querySelectorAll('.olo-f-step-num');
            var lines = wrapper.querySelectorAll('.olo-f-step-line');
            var stepItems = wrapper.querySelectorAll('.olo-f-step-item');
            var submitRow = wrapper.querySelector('[data-olo-submit]');

            function updateStepIndicator() {
                if (!isMultistep) return;
                var pct = ((currentStep + 1) / totalSteps) * 100;
                if (progressFill) { progressFill.style.width = pct + '%'; }
                var di;
                for (di = 0; di < dots.length; di++) {
                    dots[di].classList.remove('active', 'completed');
                    if (di === currentStep) { dots[di].classList.add('active'); }
                    if (di < currentStep) { dots[di].classList.add('completed'); }
                }
                for (di = 0; di < nums.length; di++) {
                    nums[di].classList.remove('active', 'completed');
                    if (di === currentStep) { nums[di].classList.add('active'); }
                    if (di < currentStep) { nums[di].classList.add('completed'); }
                }
                for (di = 0; di < lines.length; di++) {
                    if (di < currentStep) { lines[di].classList.add('completed'); }
                    else { lines[di].classList.remove('completed'); }
                }
                for (di = 0; di < stepItems.length; di++) {
                    stepItems[di].classList.remove('active');
                    if (di === currentStep) { stepItems[di].classList.add('active'); }
                }
            }

            function showStep(idx) {
                if (!isMultistep) return;
                for (var p = 0; p < pages.length; p++) {
                    pages[p].classList.remove('active');
                }
                pages[idx].classList.add('active');
                currentStep = idx;
                updateStepIndicator();

                // Show submit only on last step
                if (submitRow) {
                    if (currentStep === totalSteps - 1) {
                        submitRow.style.display = '';
                    } else {
                        submitRow.style.display = 'none';
                    }
                }

                // Scroll to top of form
                wrapper.scrollIntoView({behavior: 'smooth', block: 'nearest'});
            }

            // Validate required fields in a container (step page or entire form)
            function validateContainer(container) {
                // Clear previous validation states in this container
                var prevDanger = container.querySelectorAll('.uk-form-danger');
                for (var d = 0; d < prevDanger.length; d++) {
                    prevDanger[d].classList.remove('uk-form-danger');
                }
                var prevErr = container.querySelectorAll('.olo-f-field-error');
                for (var e = 0; e < prevErr.length; e++) {
                    prevErr[e].remove();
                }

                var valid = true;
                var firstInvalid = null;

                // Validate required checkbox groups
                var checkGroups = container.querySelectorAll('[data-olo-check-required]');
                for (var g = 0; g < checkGroups.length; g++) {
                    var groupEl = checkGroups[g];
                    // Skip if inside a hidden conditional wrapper
                    var condParent = groupEl.closest('.olo-f-cond-hidden');
                    if (condParent) { continue; }
                    var checked = groupEl.querySelectorAll('input[type="checkbox"]:checked');
                    if (checked.length === 0) {
                        valid = false;
                        if (!firstInvalid) { firstInvalid = groupEl; }
                    }
                }

                // Validate all required inputs/textareas/selects
                var allInputs = container.querySelectorAll('input, textarea, select');
                for (var i = 0; i < allInputs.length; i++) {
                    var el = allInputs[i];
                    // Skip hidden inputs and honeypot
                    if (el.type === 'hidden') { continue; }
                    // Skip fields inside conditionally hidden wrappers
                    var hiddenParent = el.closest('.olo-f-cond-hidden');
                    if (hiddenParent) { continue; }
                    if (!el.checkValidity()) {
                        el.classList.add('uk-form-danger');
                        var errMsg = '';
                        if (el.validity.valueMissing) {
                            errMsg = 'Questo campo \u00e8 obbligatorio';
                        } else if (el.validity.typeMismatch) {
                            if (el.type === 'email') { errMsg = 'Inserisci un indirizzo email valido'; }
                            else if (el.type === 'url') { errMsg = 'Inserisci un URL valido'; }
                            else { errMsg = 'Formato non valido'; }
                        } else if (el.validity.patternMismatch) {
                            if (el.type === 'tel') { errMsg = 'Inserisci un numero di telefono valido'; }
                            else { errMsg = el.title || 'Formato non valido'; }
                        }
                        if (errMsg) {
                            var span = document.createElement('span');
                            span.className = 'olo-f-field-error';
                            span.textContent = errMsg;
                            span.style.cssText = 'color:var(--olo-color-danger, #EF4444);font-size:12px;display:block;margin-top:4px';
                            el.parentNode.appendChild(span);
                        }
                        if (!firstInvalid) { firstInvalid = el; }
                        valid = false;
                    }
                }

                // Validate password_confirm fields
                var pwcMains = container.querySelectorAll('[data-pwc-main]');
                for (var p = 0; p < pwcMains.length; p++) {
                    var pid = pwcMains[p].getAttribute('data-pwc-main');
                    var confirmEl = container.querySelector('[data-pwc-confirm="' + pid + '"]');
                    var errEl = container.querySelector('[data-pwc-error="' + pid + '"]');
                    if (confirmEl) {
                        if (pwcMains[p].value !== confirmEl.value) {
                            valid = false;
                            confirmEl.classList.add('uk-form-danger');
                            if (errEl) { errEl.style.display = 'block'; }
                            if (!firstInvalid) { firstInvalid = confirmEl; }
                        } else {
                            if (errEl) { errEl.style.display = 'none'; }
                        }
                    }
                }

                if (firstInvalid) {
                    if (firstInvalid.focus) { firstInvalid.focus(); }
                }

                return valid;
            }

            // ─── Multi-step: navigation event delegation ───
            if (isMultistep) {
                // Initial state: show submit only if on last step (shouldn't be, but safe)
                if (submitRow) {
                    if (currentStep === totalSteps - 1) {
                        submitRow.style.display = '';
                    } else {
                        submitRow.style.display = 'none';
                    }
                }

                wrapper.addEventListener('click', function(ev) {
                    var prevBtn = ev.target.closest('.olo-f-step-prev');
                    if (prevBtn) {
                        if (currentStep > 0) { showStep(currentStep - 1); }
                        return;
                    }
                    var nextBtn = ev.target.closest('.olo-f-step-next');
                    if (nextBtn) {
                        // Validate current step before proceeding
                        if (validateContainer(pages[currentStep])) {
                            if (currentStep < totalSteps - 1) {
                                showStep(currentStep + 1);
                            }
                        }
                        return;
                    }
                });
            }

            // ─── Conditional logic ───
            if (hasConditions) {
                var condFields = wrapper.querySelectorAll('[data-cond-field]');

                function getFieldValue(name) {
                    // Try standard input/textarea/select
                    var el = wrapper.querySelector('[name="fields[' + name + ']"]');
                    if (el) {
                        if (el.tagName === 'SELECT') { return el.value; }
                        if (el.tagName === 'TEXTAREA') { return el.value; }
                        if (el.tagName === 'INPUT') { return el.value; }
                    }
                    // Check radio buttons
                    var radios = wrapper.querySelectorAll('[name="fields[' + name + ']"]');
                    for (var r = 0; r < radios.length; r++) {
                        if (radios[r].type === 'radio') {
                            if (radios[r].checked) { return radios[r].value; }
                        }
                    }
                    // Check checkboxes (multi-value)
                    var checks = wrapper.querySelectorAll('[name="fields[' + name + '][]"]');
                    var vals = [];
                    for (var c = 0; c < checks.length; c++) {
                        if (checks[c].checked) { vals.push(checks[c].value); }
                    }
                    if (vals.length) { return vals.join(','); }
                    return '';
                }

                function evalCondition(field, op, value) {
                    var current = getFieldValue(field);
                    if (op === 'equals') { return current === value; }
                    if (op === 'not_equals') { return current !== value; }
                    if (op === 'contains') { return current.indexOf(value) !== -1; }
                    if (op === 'not_empty') { return current !== ''; }
                    if (op === 'empty') { return current === ''; }
                    return true;
                }

                function checkConditions() {
                    for (var i = 0; i < condFields.length; i++) {
                        var el = condFields[i];
                        var cf = el.getAttribute('data-cond-field');
                        var co = el.getAttribute('data-cond-op');
                        var cv = el.getAttribute('data-cond-value');
                        var visible = evalCondition(cf, co, cv);

                        if (visible) {
                            el.classList.remove('olo-f-cond-hidden');
                            // Re-enable required attributes that were disabled
                            var reqInputs = el.querySelectorAll('[data-was-required]');
                            for (var ri = 0; ri < reqInputs.length; ri++) {
                                reqInputs[ri].setAttribute('required', '');
                            }
                        } else {
                            el.classList.add('olo-f-cond-hidden');
                            // Disable required on hidden fields so they don't block validation
                            var allReq = el.querySelectorAll('[required]');
                            for (var ai = 0; ai < allReq.length; ai++) {
                                allReq[ai].setAttribute('data-was-required', '1');
                                allReq[ai].removeAttribute('required');
                            }
                            // Clear values of hidden fields so they are not submitted
                            var hiddenInputs = el.querySelectorAll('input, textarea, select');
                            for (var hi = 0; hi < hiddenInputs.length; hi++) {
                                var inp = hiddenInputs[hi];
                                if (inp.type === 'checkbox' || inp.type === 'radio') {
                                    inp.checked = false;
                                } else {
                                    inp.value = '';
                                }
                                // Remove danger state
                                inp.classList.remove('uk-form-danger');
                            }
                        }
                    }
                }

                // Listen to all input changes via event delegation
                wrapper.addEventListener('input', checkConditions);
                wrapper.addEventListener('change', checkConditions);
                // Initial evaluation
                checkConditions();
            }

            // ─── Calculated fields ───
            var calcFields = wrapper.querySelectorAll('.olo-f-calc-display');
            if (calcFields.length > 0) {
                function evalCalc() {
                    for (var ci = 0; ci < calcFields.length; ci++) {
                        var cf = calcFields[ci];
                        var formula = cf.getAttribute('data-calc-formula') || '';
                        var decimals = parseInt(cf.getAttribute('data-calc-decimals') || '2');
                        var prefix = cf.getAttribute('data-calc-prefix') || '';
                        var suffix = cf.getAttribute('data-calc-suffix') || '';

                        // Replace {field_name} with actual values
                        var expr = formula.replace(/\{([^}]+)\}/g, function(m, name) {
                            return parseFloat(getFieldValue(name)) || 0;
                        });

                        // Safe evaluation: only allow numbers and math operators
                        var result = 0;
                        try {
                            expr = expr.replace(/[^0-9+\-*/.() ]/g, '');
                            if (expr) { result = Function('"use strict"; return (' + expr + ')')(); }
                        } catch(e) { result = 0; }

                        if (isNaN(result) || !isFinite(result)) { result = 0; }
                        result = parseFloat(result.toFixed(decimals));

                        var display = cf.querySelector('.olo-f-calc-value');
                        if (display) { display.textContent = result; }
                        var hidden = cf.parentElement.querySelector('.olo-f-calc-hidden');
                        if (hidden) { hidden.value = result; }
                    }
                }
                wrapper.addEventListener('input', evalCalc);
                wrapper.addEventListener('change', evalCalc);
                evalCalc();
            }

            // ─── Form submission ───
            function doSubmit() {
                var btn = form.querySelector('[type="submit"]');
                var msgOk = form.querySelector('.olo-f-msg--success');
                var msgErr = form.querySelector('.olo-f-msg--error');
                msgOk.style.display = 'none';
                msgOk.className = 'olo-f-msg olo-f-msg--success';
                msgErr.style.display = 'none';
                msgErr.className = 'olo-f-msg olo-f-msg--error';

                // For multi-step forms, validate all visible pages
                if (isMultistep) {
                    // Validate the last step (current step) which should be the final one
                    if (!validateContainer(pages[currentStep])) {
                        showMsg(msgErr, 'Correggi i campi evidenziati.');
                        return;
                    }
                } else {
                    if (!validateContainer(form)) {
                        showMsg(msgErr, 'Correggi i campi evidenziati.');
                        return;
                    }
                }

                // Validate privacy checkbox if present
                var privacyCheck = form.querySelector('[name="_olo_privacy_consent"]');
                if (privacyCheck) {
                    if (!privacyCheck.checked) {
                        showMsg(msgErr, 'Devi accettare la Privacy Policy per procedere.');
                        privacyCheck.focus();
                        return;
                    }
                }

                btn.disabled = true;
                form.classList.add('olo-f-sending');

                // Build FormData, excluding conditionally hidden fields
                var fd = new FormData();
                // Add hidden control fields
                var hiddenControls = form.querySelectorAll('input[type="hidden"]');
                for (var h = 0; h < hiddenControls.length; h++) {
                    fd.append(hiddenControls[h].name, hiddenControls[h].value);
                }
                // Add honeypot
                var honeypotEl = form.querySelector('[name="olo_website_url"]');
                if (honeypotEl) { fd.append('olo_website_url', honeypotEl.value); }
                // Add privacy consent
                if (privacyCheck) {
                    if (privacyCheck.checked) { fd.append('_olo_privacy_consent', '1'); }
                }
                // Add visible form fields only (skip hidden conditional fields)
                var allFieldWrappers = form.querySelectorAll('[data-field-name]');
                for (var w = 0; w < allFieldWrappers.length; w++) {
                    var fw = allFieldWrappers[w];
                    // Skip conditionally hidden
                    if (fw.classList.contains('olo-f-cond-hidden')) { continue; }
                    var wInputs = fw.querySelectorAll('input, textarea, select');
                    for (var wi = 0; wi < wInputs.length; wi++) {
                        var inp = wInputs[wi];
                        if (inp.type === 'file') {
                            for (var fi = 0; fi < inp.files.length; fi++) {
                                fd.append(inp.name, inp.files[fi]);
                            }
                        } else if (inp.type === 'checkbox') {
                            if (inp.checked) { fd.append(inp.name, inp.value); }
                        } else if (inp.type === 'radio') {
                            if (inp.checked) { fd.append(inp.name, inp.value); }
                        } else {
                            fd.append(inp.name, inp.value);
                        }
                    }
                }
                // Also add standalone hidden fields (not wrapped in data-field-name)
                var standaloneHidden = form.querySelectorAll('input[type="hidden"][name^="fields["]');
                for (var sh = 0; sh < standaloneHidden.length; sh++) {
                    fd.append(standaloneHidden[sh].name, standaloneHidden[sh].value);
                }

                fetch('<?php echo esc_url( rest_url( 'olo/v1/form/submit' ) ); ?>', {
                    method: 'POST',
                    body: fd,
                    credentials: 'same-origin'
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    btn.disabled = false;
                    form.classList.remove('olo-f-sending');
                    if (data.success) {
                        showMsg(msgOk, data.data.message || 'Inviato!');
                        form.reset();
                        form.querySelectorAll('.uk-form-danger').forEach(function(el) { el.classList.remove('uk-form-danger'); });
                        form.querySelectorAll('.olo-f-field-error').forEach(function(el) { el.remove(); });
                        // Reset multi-step to first step
                        if (isMultistep) { showStep(0); }
                        // Re-evaluate conditions after reset
                        if (hasConditions) {
                            var evt = new Event('change', {bubbles: true});
                            wrapper.dispatchEvent(evt);
                        }
                        var redirect = data.data.redirect;
                        if (redirect) {
                            setTimeout(function() { window.location.href = redirect; }, 1500);
                        }
                    } else {
                        showMsg(msgErr, data.data.message || 'Errore.');
                    }
                })
                .catch(function() {
                    btn.disabled = false;
                    form.classList.remove('olo-f-sending');
                    showMsg(msgErr, '<?php echo esc_js( $s['error_message'] ); ?>');
                });
            }

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                if (hasRecaptcha) {
                    // Check if token already exists (avoid re-requesting)
                    var existingToken = form.querySelector('[name="_olo_recaptcha_token"]');
                    if (existingToken) {
                        existingToken.remove();
                    }
                    if (typeof grecaptcha !== 'undefined') {
                        grecaptcha.ready(function(){
                            grecaptcha.execute(recaptchaSiteKey, {action: 'olo_form_submit'}).then(function(token){
                                var input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = '_olo_recaptcha_token';
                                input.value = token;
                                form.appendChild(input);
                                doSubmit();
                            });
                        });
                    } else {
                        // reCAPTCHA script failed to load, submit anyway
                        doSubmit();
                    }
                } else {
                    doSubmit();
                }
            });

            // Remove danger state and inline error on input
            form.addEventListener('input', function(e) {
                if (e.target.classList) {
                    e.target.classList.remove('uk-form-danger');
                    var fieldErr = e.target.parentNode.querySelector('.olo-f-field-error');
                    if (fieldErr) { fieldErr.remove(); }
                }
            });

            function showMsg(el, text) {
                el.textContent = text;
                el.style.display = 'block';
                var anim = el.dataset.anim;
                if (anim === 'shake') {
                    el.classList.add('olo-f-anim-shake');
                    setTimeout(function() { el.classList.remove('olo-f-anim-shake'); }, 600);
                } else if (anim) {
                    el.classList.add('uk-animation-' + anim);
                }
            }

            // ─── File upload client-side validation ───
            var fileWraps = wrapper.querySelectorAll('.olo-f-file-wrap');
            for (var fw = 0; fw < fileWraps.length; fw++) {
                (function(wrap) {
                    var input    = wrap.querySelector('.olo-f-file-input');
                    var info     = wrap.querySelector('.olo-f-file-info');
                    var listEl   = wrap.querySelector('.olo-f-file-list');
                    var errEl    = wrap.querySelector('.olo-f-file-error');
                    var maxSize  = parseInt(wrap.dataset.maxSize, 10) || 5;
                    var maxFiles = parseInt(wrap.dataset.maxFiles, 10) || 1;
                    var allowed  = (wrap.dataset.allowed || '').toLowerCase().split(',').map(function(s){return s.trim()});

                    input.addEventListener('change', function() {
                        errEl.style.display = 'none';
                        errEl.textContent = '';
                        listEl.innerHTML = '';

                        var files = input.files;
                        if (!files.length) {
                            info.textContent = 'Nessun file selezionato';
                            return;
                        }

                        // Validate count
                        if (files.length > maxFiles) {
                            errEl.textContent = 'Massimo ' + maxFiles + ' file consentiti.';
                            errEl.style.display = 'block';
                            input.value = '';
                            info.textContent = 'Nessun file selezionato';
                            return;
                        }

                        var errors = [];
                        var names = [];
                        for (var i = 0; i < files.length; i++) {
                            var file = files[i];
                            var ext = '.' + file.name.split('.').pop().toLowerCase();

                            // Validate type
                            if (allowed.length > 0) {
                                if (allowed[0] !== '') {
                                    var typeOk = false;
                                    for (var a = 0; a < allowed.length; a++) {
                                        if (ext === allowed[a]) { typeOk = true; break; }
                                    }
                                    if (!typeOk) {
                                        errors.push(file.name + ': tipo non consentito');
                                        continue;
                                    }
                                }
                            }

                            // Validate size
                            if (file.size > maxSize * 1024 * 1024) {
                                errors.push(file.name + ': supera ' + maxSize + ' MB');
                                continue;
                            }

                            names.push(file.name);

                            // Add to list with remove button
                            var item = document.createElement('div');
                            item.className = 'olo-f-file-list-item';
                            item.innerHTML = '<span>' + file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)</span>';
                            listEl.appendChild(item);
                        }

                        if (errors.length) {
                            errEl.textContent = errors.join('; ');
                            errEl.style.display = 'block';
                            input.value = '';
                            info.textContent = 'Nessun file selezionato';
                            listEl.innerHTML = '';
                            return;
                        }

                        if (names.length === 1) {
                            info.textContent = names[0];
                        } else {
                            info.textContent = names.length + ' file selezionati';
                        }
                    });
                })(fileWraps[fw]);
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
     * Parse options string (newline or comma separated) into array.
     */
    private function parse_options( $raw ) {
        if ( empty( $raw ) ) {
            return [ 'Opzione 1', 'Opzione 2' ];
        }
        $opts = array_filter( array_map( 'trim', preg_split( '/[\n,]+/', $raw ) ) );
        return ! empty( $opts ) ? $opts : [ 'Opzione 1', 'Opzione 2' ];
    }
}
