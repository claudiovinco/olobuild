<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Form_Tile extends Olo_Tile_Base {

    protected $type     = 'form';
    protected $name     = 'Form Contatti';
    protected $icon     = 'dashicons-email-alt';
    protected $category = 'content';
    protected $defaults = [
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
        'label_color'       => '#D1D5DB',
        'label_size'        => '14',
        'label_weight'      => '500',
        'input_bg'          => '#1F2937',
        'input_color'       => '#F3F4F6',
        'input_border_color'=> '#374151',
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
        $radius      = absint( $s['input_radius'] );
        $label_color = $this->safe_color( $s['label_color'] );
        $label_size  = absint( $s['label_size'] ) ?: 14;
        $label_weight= $s['label_weight'] ?: '500';
        $input_bg    = $this->safe_color( $s['input_bg'] );
        $input_color = $this->safe_color( $s['input_color'] );
        $input_bc    = $this->safe_color( $s['input_border_color'] );
        $focus_bc    = $this->safe_color( $s['input_focus_border'] );
        $focus_shadow= ! empty( $s['input_focus_shadow'] );
        $ph_opacity  = floatval( $s['input_placeholder_opacity'] ) ?: 0.4;
        $btn_bg      = $this->safe_color( $s['submit_bg'] );
        $btn_color   = $this->safe_color( $s['submit_color'] );
        $btn_hover   = $this->safe_color( $s['submit_hover_bg'] );
        $btn_radius  = absint( $s['submit_radius'] );
        $btn_px      = absint( $s['submit_padding_x'] ) ?: 32;
        $btn_py      = absint( $s['submit_padding_y'] ) ?: 14;
        $btn_fs      = absint( $s['submit_font_size'] ) ?: 16;
        $btn_fw      = absint( $s['submit_font_weight'] ) ?: 600;
        $btn_full    = ! empty( $s['submit_full_width'] );
        $btn_bw      = absint( $s['submit_border_width'] );
        $btn_bc      = $this->safe_color( $s['submit_border_color'] );
        $btn_hbc     = $this->safe_color( $s['submit_hover_border_color'] );
        $btn_ls      = floatval( $s['submit_letter_spacing'] );
        $btn_tt      = in_array( $s['submit_text_transform'], [ 'none', 'uppercase', 'lowercase', 'capitalize' ], true ) ? $s['submit_text_transform'] : 'none';
        $btn_icon    = sanitize_text_field( $s['submit_icon'] ?? '' );
        $btn_icon_pos= $s['submit_icon_pos'] ?? 'left';
        $chk_accent  = $this->safe_color( $s['check_accent_color'] );
        $chk_bg      = $this->safe_color( $s['check_bg'] );
        $chk_bc      = $this->safe_color( $s['check_border_color'] );
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
        ] );

        // Container style
        $container_style = '';
        if ( $max_w > 0 ) {
            $container_style .= 'max-width:' . $max_w . 'px;';
            if ( $form_align === 'center' ) $container_style .= 'margin-left:auto;margin-right:auto;';
            elseif ( $form_align === 'right' ) $container_style .= 'margin-left:auto;';
        }

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> .olo-f-label{color:<?php echo $label_color; ?>;font-size:<?php echo $label_size; ?>px;font-weight:<?php echo $label_weight; ?>;margin-bottom:6px;display:block}
            .<?php echo $uid; ?> .olo-f-required{color:#EF4444;margin-left:2px}
            .<?php echo $uid; ?> .uk-input,
            .<?php echo $uid; ?> .uk-textarea,
            .<?php echo $uid; ?> .uk-select{background-color:<?php echo $input_bg; ?>;color:<?php echo $input_color; ?>;border:<?php echo $bw; ?>px solid <?php echo $input_bc; ?>;border-radius:<?php echo $radius; ?>px}
            .<?php echo $uid; ?> .uk-input:focus,
            .<?php echo $uid; ?> .uk-textarea:focus,
            .<?php echo $uid; ?> .uk-select:focus{border-color:<?php echo $focus_bc ?: 'var(--olo-color-primary, #6366F1)'; ?>;outline:none<?php if ( $focus_shadow ) : ?>;box-shadow:0 0 0 3px rgba(99,102,241,0.15)<?php endif; ?>}
            .<?php echo $uid; ?> .uk-input::placeholder,
            .<?php echo $uid; ?> .uk-textarea::placeholder{color:<?php echo $input_color; ?>;opacity:<?php echo $ph_opacity; ?>}
            .<?php echo $uid; ?> .uk-input:-webkit-autofill,
            .<?php echo $uid; ?> .uk-textarea:-webkit-autofill,
            .<?php echo $uid; ?> .uk-select:-webkit-autofill{-webkit-box-shadow:0 0 0 1000px <?php echo $input_bg; ?> inset !important;-webkit-text-fill-color:<?php echo $input_color; ?> !important;transition:background-color 5000s ease-in-out 0s}
            .<?php echo $uid; ?> .uk-form-icon{color:<?php echo $input_color; ?>;opacity:0.5}
            .<?php echo $uid; ?> .uk-form-icon:hover{opacity:0.8}
            .<?php echo $uid; ?> .olo-f-btn{background:<?php echo $btn_bg ?: 'var(--olo-color-primary, #6366F1)'; ?>;color:<?php echo $btn_color; ?>;<?php if ( $btn_bw > 0 ) : ?>border:<?php echo $btn_bw; ?>px solid <?php echo $btn_bc ?: 'var(--olo-color-primary, #6366F1)'; ?><?php else : ?>border:none<?php endif; ?>;border-radius:<?php echo $btn_radius; ?>px;padding:<?php echo $btn_py; ?>px <?php echo $btn_px; ?>px;font-size:<?php echo $btn_fs; ?>px;font-weight:<?php echo $btn_fw; ?>;cursor:pointer;transition:background 0.2s ease,border-color 0.2s ease,transform 0.15s ease;display:inline-flex;align-items:center;gap:8px<?php if ( $btn_ls > 0 ) : ?>;letter-spacing:<?php echo $btn_ls; ?>px<?php endif; ?><?php if ( $btn_tt !== 'none' ) : ?>;text-transform:<?php echo $btn_tt; ?><?php endif; ?><?php if ( $btn_full ) : ?>;width:100%;justify-content:center<?php endif; ?>}
            .<?php echo $uid; ?> .olo-f-btn:hover{background:<?php echo $btn_hover ?: 'var(--olo-color-primary-dark, #4F46E5)'; ?><?php if ( $btn_bw > 0 && $btn_hbc ) : ?>;border-color:<?php echo $btn_hbc; ?><?php endif; ?>}
            .<?php echo $uid; ?> .olo-f-btn:active{transform:translateY(1px)}
            .<?php echo $uid; ?> .olo-f-btn:disabled{opacity:0.6;cursor:not-allowed}
            .<?php echo $uid; ?> .olo-f-msg{margin-top:12px;padding:12px 16px;border-radius:<?php echo $radius; ?>px;font-size:14px;display:none}
            .<?php echo $uid; ?> .olo-f-msg--success{background:rgba(16,185,129,0.15);color:#10B981;border:1px solid rgba(16,185,129,0.3)}
            .<?php echo $uid; ?> .olo-f-msg--error{background:rgba(239,68,68,0.15);color:#EF4444;border:1px solid rgba(239,68,68,0.3)}
            .<?php echo $uid; ?> .olo-f-sending .olo-f-btn-text{display:none}
            .<?php echo $uid; ?> .olo-f-sending .olo-f-btn-loading{display:inline-flex;align-items:center;gap:8px}
            .<?php echo $uid; ?> .olo-f-btn-loading{display:none}
            .<?php echo $uid; ?> .uk-radio,
            .<?php echo $uid; ?> .uk-checkbox{width:<?php echo $chk_size; ?>px;height:<?php echo $chk_size; ?>px<?php if ( $chk_bc ) : ?>;border-color:<?php echo $chk_bc; ?><?php endif; ?>;flex-shrink:0}
            .<?php echo $uid; ?> .uk-checkbox:checked,
            .<?php echo $uid; ?> .uk-radio:checked{background-color:<?php echo $chk_accent ?: 'var(--olo-color-primary, #1e87f0)'; ?>}
            .<?php echo $uid; ?> .uk-checkbox:focus,
            .<?php echo $uid; ?> .uk-radio:focus{border-color:<?php echo $chk_accent ?: 'var(--olo-color-primary, #1e87f0)'; ?>}
            .<?php echo $uid; ?> .olo-f-option{display:flex;align-items:center;gap:<?php echo $chk_gap; ?>px;color:<?php echo $label_color; ?>;font-size:<?php echo $label_size; ?>px;cursor:pointer}
            .<?php echo $uid; ?> .olo-f-privacy{margin-top:<?php echo $gap; ?>px}
            .<?php echo $uid; ?> .olo-f-privacy a{color:var(--olo-color-primary, #818CF8);text-decoration:underline}
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
        </style>

        <div class="olo-form <?php echo esc_attr( $uid ); ?>"<?php if ( $container_style ) : ?> style="<?php echo esc_attr( $container_style ); ?>"<?php endif; ?>>
            <form class="uk-form-stacked" data-olo-form="<?php echo esc_attr( $uid ); ?>">
                <input type="hidden" name="_olo_form_config" value="<?php echo esc_attr( base64_encode( $form_config ) ); ?>" />

                <?php if ( ! empty( $s['honeypot'] ) ) : ?>
                <div style="position:absolute;left:-9999px;opacity:0" aria-hidden="true">
                    <input type="text" name="olo_website_url" tabindex="-1" autocomplete="off" />
                </div>
                <?php endif; ?>

                <div class="uk-grid-small" uk-grid style="row-gap:<?php echo $gap; ?>px">
                    <?php foreach ( $fields as $i => $field ) :
                        $ftype       = $field['field_type'] ?? 'text';
                        $flabel      = $field['label'] ?? '';
                        $fname       = sanitize_key( $field['name'] ?? 'field_' . $i );
                        $fplaceholder= $field['placeholder'] ?? '';
                        $frequired   = ! empty( $field['required'] );
                        $fwidth      = $field['width'] ?? '1-1';
                        $foptions    = $field['options'] ?? '';
                        $ficon       = sanitize_text_field( $field['icon'] ?? '' );
                        $req_attr    = $frequired ? ' required' : '';

                        if ( $ftype === 'hidden' ) : ?>
                            <input type="hidden" name="fields[<?php echo esc_attr( $fname ); ?>]" value="<?php echo esc_attr( $fplaceholder ); ?>" />
                        <?php continue; endif;

                        // Width class — support 1-2, 1-3, 2-3, 1-4, 3-4
                        $width_class = 'uk-width-1-1';
                        if ( in_array( $fwidth, [ '1-2', '1-3', '2-3', '1-4', '3-4' ], true ) ) {
                            $width_class = 'uk-width-' . $fwidth . '@s';
                        }
                    ?>
                    <div class="<?php echo esc_attr( $width_class ); ?>">
                        <?php if ( in_array( $ftype, [ 'text', 'email', 'tel', 'url', 'number', 'date', 'time' ], true ) ) : ?>
                            <?php if ( $is_floating ) : ?>
                                <div class="olo-f-float">
                                    <?php if ( $ficon ) : ?>
                                    <div class="uk-inline uk-width-1-1">
                                        <span class="uk-form-icon" uk-icon="icon: <?php echo esc_attr( $ficon ); ?>"></span>
                                        <input type="<?php echo esc_attr( $ftype ); ?>" name="fields[<?php echo esc_attr( $fname ); ?>]" class="uk-input<?php echo $size_class; ?>" placeholder=" "<?php echo $req_attr; ?> />
                                        <?php if ( $flabel ) : ?>
                                            <label class="olo-f-float-label"><?php echo esc_html( $flabel ); ?><?php if ( $frequired ) : ?><span class="olo-f-required">*</span><?php endif; ?></label>
                                        <?php endif; ?>
                                    </div>
                                    <?php else : ?>
                                    <input type="<?php echo esc_attr( $ftype ); ?>" name="fields[<?php echo esc_attr( $fname ); ?>]" class="uk-input<?php echo $size_class; ?>" placeholder=" "<?php echo $req_attr; ?> />
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
                                    <input type="<?php echo esc_attr( $ftype ); ?>" name="fields[<?php echo esc_attr( $fname ); ?>]" class="uk-input<?php echo $size_class; ?>" placeholder="<?php echo esc_attr( $fplaceholder ); ?>"<?php echo $req_attr; ?> />
                                </div>
                                <?php else : ?>
                                <input type="<?php echo esc_attr( $ftype ); ?>" name="fields[<?php echo esc_attr( $fname ); ?>]" class="uk-input<?php echo $size_class; ?>" placeholder="<?php echo esc_attr( $fplaceholder ); ?>"<?php echo $req_attr; ?> />
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
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if ( ! empty( $s['privacy_checkbox'] ) ) : ?>
                <div class="olo-f-privacy">
                    <label class="olo-f-option">
                        <input type="checkbox" class="uk-checkbox" name="_olo_privacy_consent" required />
                        <span><?php echo wp_kses_post( $s['privacy_text'] ); ?></span>
                    </label>
                </div>
                <?php endif; ?>

                <div class="uk-margin-top" style="text-align:<?php echo esc_attr( $s['submit_alignment'] ?: 'left' ); ?>">
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

        <script>
        (function(){
            var form = document.querySelector('[data-olo-form="<?php echo esc_js( $uid ); ?>"]');
            if (!form) return;
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                var btn = form.querySelector('.olo-f-btn');
                var msgOk = form.querySelector('.olo-f-msg--success');
                var msgErr = form.querySelector('.olo-f-msg--error');
                msgOk.style.display = 'none';
                msgOk.className = 'olo-f-msg olo-f-msg--success';
                msgErr.style.display = 'none';
                msgErr.className = 'olo-f-msg olo-f-msg--error';

                // Validate required checkbox groups
                var checkGroups = form.querySelectorAll('[data-olo-check-required]');
                for (var g = 0; g < checkGroups.length; g++) {
                    var checked = checkGroups[g].querySelectorAll('input[type="checkbox"]:checked');
                    if (checked.length === 0) {
                        showMsg(msgErr, 'Seleziona almeno un\'opzione per ogni campo obbligatorio.');
                        return;
                    }
                }

                // Clear validation states
                form.querySelectorAll('.uk-form-danger').forEach(function(el) { el.classList.remove('uk-form-danger'); });

                // Validate required fields
                var invalid = form.querySelectorAll(':invalid');
                if (invalid.length) {
                    invalid.forEach(function(el) { el.classList.add('uk-form-danger'); });
                    invalid[0].focus();
                    showMsg(msgErr, 'Compila tutti i campi obbligatori.');
                    return;
                }

                btn.disabled = true;
                form.classList.add('olo-f-sending');

                var fd = new FormData(form);
                fetch('<?php echo esc_url( rest_url( 'olo/v1/form/submit' ) ); ?>', {
                    method: 'POST',
                    body: fd,
                    credentials: 'same-origin',
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    btn.disabled = false;
                    form.classList.remove('olo-f-sending');
                    if (data.success) {
                        showMsg(msgOk, data.data.message || 'Inviato!');
                        form.reset();
                        form.querySelectorAll('.uk-form-danger').forEach(function(el) { el.classList.remove('uk-form-danger'); });
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
            });

            // Remove danger state on input
            form.addEventListener('input', function(e) {
                if (e.target.classList) e.target.classList.remove('uk-form-danger');
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
        })();
        </script>

        <?php
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
