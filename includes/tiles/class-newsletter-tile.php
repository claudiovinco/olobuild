<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Newsletter_Tile extends Olo_Tile_Base {

    protected $type     = 'newsletter';
    protected $name     = 'Newsletter';
    protected $icon     = 'dashicons-email-alt';
    protected $category = 'marketing';
    protected $defaults = [
        'preset' => 'custom',
        'layout'            => 'horizontal',
        'title'             => 'Iscriviti alla newsletter',
        'subtitle'          => 'Ricevi aggiornamenti e contenuti esclusivi direttamente nella tua casella email.',
        'icon_type'         => 'none',
        'icon_name'         => '📧',
        'icon_image'        => '',
        'show_name'         => false,
        'name_placeholder'  => 'Il tuo nome',
        'email_placeholder' => 'La tua email',
        'button_text'       => 'Iscriviti',
        'button_icon'       => true,
        'privacy_text'      => '',
        'privacy_required'  => false,
        'success_message'   => 'Iscrizione completata! Controlla la tua email.',
        'success_animation' => 'fade',
        'redirect_url'      => '',
        'content_lock'      => false,
        'lock_message'      => 'Iscriviti alla newsletter per sbloccare questo contenuto',
        'lock_blur'         => 8,
        'lock_height'       => 200,
        'integration'       => 'none',
        'mailchimp_api'     => '',
        'mailchimp_list'    => '',
        'brevo_api'         => '',
        'brevo_list'        => '',
        'activecampaign_url' => '',
        'activecampaign_api' => '',
        'activecampaign_list' => '',
        'convertkit_api'    => '',
        'convertkit_form'   => '',
        'hubspot_portal'    => '',
        'hubspot_form'      => '',
        'webhook_url'       => '',
        'webhook_method'    => 'POST',
        'honeypot'          => true,
        'recaptcha'         => false,
        'max_width'         => '600',
        'alignment'         => 'center',
        'bg_color'          => '',
        'border_radius'     => 12,
        'padding'           => '32',
        'title_size'        => '24',
        'title_weight'      => '700',
        'title_color'       => '',
        'subtitle_size'     => '14',
        'subtitle_color'    => '',
        'icon_size'         => '48',
        'icon_color'        => '',
        'input_bg'          => '#ffffff',
        'input_color'       => '#1F2937',
        'input_border'      => '#D1D5DB',
        'input_focus_border' => '',
        'input_radius'      => 8,
        'input_height'      => '44',
        'btn_bg'            => '',
        'btn_color'         => '#ffffff',
        'btn_hover_bg'      => '',
        'btn_radius'        => 8,
        'btn_font_size'     => '14',
        'btn_font_weight'   => '600',
        'shadow'            => 'none',
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
        $s = wp_parse_args( $settings, $this->defaults );

        $uid = 'olo-nl-' . wp_rand( 10000, 99999 );

        // Build form config for handler (reuses form handler)
        $form_config = [
            'form_name'    => 'Newsletter',
            'integration'  => $s['integration'],
            'success_msg'  => $s['success_message'],
            'redirect_url' => $s['redirect_url'],
            'honeypot'     => ! empty( $s['honeypot'] ),
            'recaptcha'    => ! empty( $s['recaptcha'] ),
        ];

        // Add integration credentials
        $int = $s['integration'];
        if ( $int === 'mailchimp' ) {
            $form_config['mailchimp_api']  = $s['mailchimp_api'];
            $form_config['mailchimp_list'] = $s['mailchimp_list'];
        } elseif ( $int === 'brevo' ) {
            $form_config['brevo_api']  = $s['brevo_api'];
            $form_config['brevo_list'] = $s['brevo_list'];
        } elseif ( $int === 'activecampaign' ) {
            $form_config['activecampaign_url']  = $s['activecampaign_url'];
            $form_config['activecampaign_api']  = $s['activecampaign_api'];
            $form_config['activecampaign_list'] = $s['activecampaign_list'];
        } elseif ( $int === 'convertkit' ) {
            $form_config['convertkit_api']  = $s['convertkit_api'];
            $form_config['convertkit_form'] = $s['convertkit_form'];
        } elseif ( $int === 'hubspot' ) {
            $form_config['hubspot_portal'] = $s['hubspot_portal'];
            $form_config['hubspot_form']   = $s['hubspot_form'];
        } elseif ( $int === 'webhook' ) {
            $form_config['webhook_url']    = $s['webhook_url'];
            $form_config['webhook_method'] = $s['webhook_method'];
        }

        $config_b64 = base64_encode( wp_json_encode( $form_config ) );
        // Token v2: legato al config — impedisce manomissione di email_to / api_keys
        // / webhook_url che permetterebbe l'uso del sito come relay.
        $token      = class_exists( 'Olo_Form_Handler' )
            ? Olo_Form_Handler::generate_token( $config_b64 )
            : ''; // fallback no-op: senza handler il form non è funzionante

        // Styles
        $primary     = 'var(--olo-color-primary, #3B82F6)';
        $bg          = $s['bg_color'] ?: 'transparent';
        $radius      = Olo_Tile_Utils::radius_int( $s['border_radius'] );
        $pad = Olo_Tile_Utils::spacing_css( $s['tile_padding'] ?? $s['padding'] ?? 32, 32 );
        $align_map   = [ 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' ];
        $align_css   = $align_map[ $s['alignment'] ] ?? 'center';
        $btn_bg      = $s['btn_bg'] ?: $primary;
        $btn_hover   = $s['btn_hover_bg'] ?: $btn_bg;
        $focus_b     = $s['input_focus_border'] ?: $primary;
        $ih          = absint( $s['input_height'] ) ?: 44;
        $ir          = Olo_Tile_Utils::radius_int( $s['input_radius'] );
        $br          = Olo_Tile_Utils::radius_int( $s['btn_radius'] );
        $is_h        = $s['layout'] === 'horizontal';
        $is_minimal  = $s['layout'] === 'minimal';

        ob_start();
        ?>
        <style>
        .<?php echo $uid; ?>{display:flex;justify-content:<?php echo $align_css; ?>}
        .<?php echo $uid; ?> .olo-nl-box{max-width:<?php echo absint($s['max_width']) ?: 600; ?>px;width:100%;background:<?php echo esc_attr($bg); ?>;border-radius:<?php echo $radius; ?>px;padding: <?php echo $pad; ?>;text-align:center}
        .<?php echo $uid; ?> .olo-nl-title{font-size:<?php echo absint($s['title_size']); ?>px;font-weight:<?php echo esc_attr($s['title_weight']); ?>;color:<?php echo $s['title_color'] ? esc_attr($s['title_color']) : 'inherit'; ?>;margin:0 0 8px;line-height:1.3}
        .<?php echo $uid; ?> .olo-nl-sub{font-size:<?php echo absint($s['subtitle_size']); ?>px;color:<?php echo $s['subtitle_color'] ? esc_attr($s['subtitle_color']) : 'var(--olo-color-text-muted, #6B7280)'; ?>;margin:0 0 20px;line-height:1.5}
        .<?php echo $uid; ?> .olo-nl-icon{font-size:<?php echo absint($s['icon_size']); ?>px;margin-bottom:12px;<?php echo $s['icon_color'] ? 'color:' . esc_attr($s['icon_color']) . ';' : ''; ?>line-height:1}
        .<?php echo $uid; ?> .olo-nl-icon img{width:<?php echo absint($s['icon_size']); ?>px;height:auto;display:inline-block}
        .<?php echo $uid; ?> .olo-nl-form{display:flex;<?php echo $is_h ? 'flex-direction:row;gap:8px;align-items:stretch' : 'flex-direction:column;gap:10px'; ?>}
        .<?php echo $uid; ?> .olo-nl-form input[type="text"],
        .<?php echo $uid; ?> .olo-nl-form input[type="email"]{height:<?php echo $ih; ?>px;padding:0 14px;background:<?php echo esc_attr($s['input_bg']); ?>;color:<?php echo esc_attr($s['input_color']); ?>;border:1px solid <?php echo esc_attr($s['input_border']); ?>;border-radius:<?php echo $ir; ?>px;font-size:14px;outline:none;transition:border-color 0.2s;flex:1;min-width:0}
        .<?php echo $uid; ?> .olo-nl-form input:focus{border-color:<?php echo esc_attr($focus_b); ?>}
        .<?php echo $uid; ?> .olo-nl-btn{height:<?php echo $ih; ?>px;padding:0 <?php echo $is_minimal ? '16' : '24'; ?>px;background:<?php echo esc_attr($btn_bg); ?>;color:<?php echo esc_attr($s['btn_color']); ?>;border:none;border-radius:<?php echo $br; ?>px;font-size:<?php echo absint($s['btn_font_size']); ?>px;font-weight:<?php echo esc_attr($s['btn_font_weight']); ?>;cursor:pointer;transition:background 0.2s,transform 0.15s;display:inline-flex;align-items:center;gap:6px;justify-content:center;white-space:nowrap;<?php echo $is_h ? '' : 'width:100%'; ?>}
        .<?php echo $uid; ?> .olo-nl-btn:hover{background:<?php echo esc_attr($btn_hover); ?>;transform:translateY(-1px)}
        .<?php echo $uid; ?> .olo-nl-privacy{font-size:11px;color:var(--olo-color-text-muted,#9CA3AF);margin-top:10px;display:flex;align-items:flex-start;gap:6px;justify-content:center;text-align:left}
        .<?php echo $uid; ?> .olo-nl-privacy a{color:inherit;text-decoration:underline}
        .<?php echo $uid; ?> .olo-nl-msg{padding:16px;border-radius:8px;font-size:14px;text-align:center;display:none}
        .<?php echo $uid; ?> .olo-nl-msg.olo-nl-ok{background:#ECFDF5;color:#065F46}
        .<?php echo $uid; ?> .olo-nl-msg.olo-nl-err{background:#FEF2F2;color:#991B1B}
        .<?php echo $uid; ?> .olo-nl-loading{opacity:0.6;pointer-events:none}
        <?php if ( ! empty( $s['content_lock'] ) ) : ?>
        .<?php echo $uid; ?>-lock{position:relative;overflow:hidden;max-height:<?php echo absint($s['lock_height']); ?>px}
        .<?php echo $uid; ?>-lock>.olo-nl-lock-content{filter:blur(<?php echo absint($s['lock_blur']); ?>px);pointer-events:none;user-select:none}
        .<?php echo $uid; ?>-lock>.olo-nl-lock-overlay{position:absolute;bottom:0;left:0;right:0;height:100%;background:linear-gradient(transparent 0%,rgba(255,255,255,0.95) 60%);display:flex;align-items:flex-end;justify-content:center;padding:20px}
        .<?php echo $uid; ?>-unlocked{max-height:none!important}
        .<?php echo $uid; ?>-unlocked>.olo-nl-lock-content{filter:none!important;pointer-events:auto!important;user-select:auto!important}
        .<?php echo $uid; ?>-unlocked>.olo-nl-lock-overlay{display:none!important}
        <?php endif; ?>
        @media(max-width:640px){.<?php echo $uid; ?> .olo-nl-form{flex-direction:column}.<?php echo $uid; ?> .olo-nl-btn{width:100%}}
        </style>

        <div class="<?php echo esc_attr( $uid ); ?> olo-nl-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>">
          <div class="olo-nl-box">
            <?php if ( $s['icon_type'] === 'emoji' ) : ?>
              <div class="olo-nl-icon"><?php echo esc_html( $s['icon_name'] ); ?></div>
            <?php elseif ( $s['icon_type'] === 'image' ) : ?>
              <div class="olo-nl-icon"><img src="<?php echo esc_url( $s['icon_image'] ); ?>" alt="" loading="lazy" /></div>
            <?php endif; ?>

            <?php
            list( $nt_cls, $nt_data ) = $this->tfx_attrs( $s, 'title', $s['title'] ?? '' );
            list( $ns_cls, $ns_data ) = $this->tfx_attrs( $s, 'subtitle', $s['subtitle'] ?? '' );
            ?>
            <?php if ( ! empty( $s['title'] ) ) : ?>
              <h3 class="olo-nl-title<?php echo $nt_cls; ?>"<?php echo $nt_data; ?>><?php echo esc_html( $s['title'] ); ?></h3>
            <?php endif; ?>
            <?php if ( ! empty( $s['subtitle'] ) ) : ?>
              <p class="olo-nl-sub<?php echo $ns_cls; ?>"<?php echo $ns_data; ?>><?php echo esc_html( $s['subtitle'] ); ?></p>
            <?php endif; ?>

            <div class="olo-nl-msg olo-nl-ok" id="<?php echo $uid; ?>-ok"></div>
            <div class="olo-nl-msg olo-nl-err" id="<?php echo $uid; ?>-err"></div>

            <form class="olo-nl-form" id="<?php echo $uid; ?>-form" novalidate>
              <input type="hidden" name="_olo_form_token" value="<?php echo esc_attr( $token ); ?>" />
              <input type="hidden" name="_olo_form_config" value="<?php echo esc_attr( $config_b64 ); ?>" />
              <?php if ( ! empty( $s['honeypot'] ) ) : ?>
                <div style="position:absolute;left:-9999px;top:-9999px" aria-hidden="true">
                  <input type="text" name="olo_website_url" tabindex="-1" autocomplete="off" />
                  <input type="text" name="olo_hp_field" tabindex="-1" autocomplete="off" />
                </div>
              <?php endif; ?>

              <?php if ( ! empty( $s['show_name'] ) ) : ?>
                <input type="text" name="name" placeholder="<?php echo esc_attr( $s['name_placeholder'] ); ?>" autocomplete="name" />
              <?php endif; ?>
              <input type="email" name="email" placeholder="<?php echo esc_attr( $s['email_placeholder'] ); ?>" required autocomplete="email" />
              <button type="submit" class="olo-nl-btn">
                <?php echo esc_html( $s['button_text'] ); ?>
                <?php if ( ! empty( $s['button_icon'] ) ) : ?>
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                <?php endif; ?>
              </button>
            </form>

            <?php if ( ! empty( $s['privacy_text'] ) ) : ?>
              <div class="olo-nl-privacy">
                <?php if ( ! empty( $s['privacy_required'] ) ) : ?>
                  <input type="checkbox" id="<?php echo $uid; ?>-priv" required style="margin-top:2px;flex-shrink:0" />
                <?php endif; ?>
                <label <?php if ( ! empty( $s['privacy_required'] ) ) echo 'for="' . $uid . '-priv"'; ?>>
                  <?php echo wp_kses_post( $s['privacy_text'] ); ?>
                </label>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <script>
        (function(){
          var uid='<?php echo $uid; ?>';
          var form=document.getElementById(uid+'-form');
          if(!form)return;
          var okEl=document.getElementById(uid+'-ok');
          var errEl=document.getElementById(uid+'-err');
          var lockId='<?php echo $uid; ?>-lock';
          var contentLock=<?php echo ! empty( $s['content_lock'] ) ? 'true' : 'false'; ?>;
          var lockKey='olo_nl_unlocked_'+uid.replace('olo-nl-','');

          // Check if already unlocked (localStorage)
          if(contentLock){
            if(localStorage.getItem(lockKey)){
              var lockWrap=document.querySelector('.'+uid+'-lock');
              if(lockWrap){lockWrap.classList.add(uid+'-unlocked')}
            }
          }

          form.addEventListener('submit',function(e){
            e.preventDefault();
            var email=form.querySelector('input[type="email"]').value;
            if(!email){errEl.textContent='Inserisci un indirizzo email';errEl.style.display='block';return}

            // Privacy check
            var privCb=form.querySelector('input[type="checkbox"][required]');
            if(privCb){
              if(!privCb.checked){errEl.textContent='Accetta la privacy policy';errEl.style.display='block';return}
            }

            form.classList.add('olo-nl-loading');
            errEl.style.display='none';

            var fd=new FormData(form);
            var payload={};
            fd.forEach(function(v,k){payload[k]=v});

            fetch('<?php echo esc_url( rest_url( 'olo/v1/form/submit' ) ); ?>',{
              method:'POST',
              headers:{'Content-Type':'application/json'},
              body:JSON.stringify(payload)
            })
            .then(function(r){return r.json()})
            .then(function(data){
              form.classList.remove('olo-nl-loading');
              if(data.success){
                form.style.display='none';
                var priv=form.parentElement.querySelector('.olo-nl-privacy');
                if(priv)priv.style.display='none';
                okEl.textContent=data.data.message||'<?php echo esc_js( $s['success_message'] ); ?>';
                okEl.style.display='block';

                // Unlock content
                if(contentLock){
                  localStorage.setItem(lockKey,'1');
                  var lockWrap=document.querySelector('.'+uid+'-lock');
                  if(lockWrap){lockWrap.classList.add(uid+'-unlocked')}
                }

                // Redirect
                if(data.data.redirect){
                  setTimeout(function(){window.location.href=data.data.redirect},1500);
                }
              }else{
                errEl.textContent=data.message||'Errore durante l\'iscrizione';
                errEl.style.display='block';
              }
            })
            .catch(function(){
              form.classList.remove('olo-nl-loading');
              errEl.textContent='Errore di connessione';
              errEl.style.display='block';
            });
          });
        })();
        </script>
        <?php

        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();
        $html = ob_get_clean();

        // Content Lock: wrap the NEXT sibling content
        if ( ! empty( $s['content_lock'] ) ) {
            $html .= '<div class="' . esc_attr( $uid ) . '-lock"><div class="olo-nl-lock-content">';
            // The locked content will be whatever comes after this tile in the template
            // We close the lock wrapper after rendering (handled by frontend renderer hook)
            // For now, we add a placeholder — actual implementation needs renderer support
            $html .= '</div><div class="olo-nl-lock-overlay"><p style="font-size:14px;color:#374151;font-weight:500">' . esc_html( $s['lock_message'] ) . '</p></div></div>';
        }

        // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}";
            echo $border_hover_css . $border_effect_css . '</style>';
        }
        return $html;
    }
}
