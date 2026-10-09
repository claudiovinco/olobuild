<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Newsletter_Tile extends Olobuild_Tile_Base {

    protected $type     = 'newsletter';
    protected $name     = 'Newsletter';
    protected $icon     = 'dashicons-email-alt';
    protected $category = 'marketing';
    protected $defaults = [
        'preset' => 'custom',
        'eyebrow'            => '',
        'eyebrow_color'      => '',
        'title_accent_color' => '',
        'layout'            => 'horizontal',
        'title'             => 'Iscriviti alla newsletter',
        'subtitle'          => 'Ricevi aggiornamenti e contenuti esclusivi direttamente nella tua casella email.',
        'icon_type'         => 'none',
        'icon'              => 'mail',
        'icon_name'         => '',
        'icon_image'        => '',
        'show_name'         => false,
        'name_placeholder'  => 'Il tuo nome',
        'email_placeholder' => 'La tua email',
        'button_text'       => 'Iscriviti',
        'button_icon'       => true,
        'button_icon_name'  => '',
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
        'input_bg'          => 'var(--olo-color-light, #ffffff)',
        'input_color'       => 'var(--olo-color-text, #1F2937)',
        'input_placeholder_color' => '',
        'input_border'      => 'var(--olo-color-border, #D1D5DB)',
        'input_focus_border' => '',
        'input_radius'      => 8,
        'input_height'      => '44',
        'btn_bg'            => '',
        'btn_color'         => 'var(--olo-color-light, #ffffff)',
        'btn_color_hover'   => '',
        'box_border'        => '',
        'btn_hover_bg'      => '',
        'btn_radius'        => 8,
        'btn_font_size'     => '14',
        'btn_font_weight'   => '600',
        'privacy_color'     => '',
        'success_bg'        => '',
        'success_color'     => '',
        'error_bg'          => '',
        'error_color'       => '',
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
        // Chiave stabile prima di rendere altro (un template annidato cambierebbe il nodo in resa).
        $chiave = $this->chiave_stabile( $settings );

        // Provider (Mailchimp, Brevo…), chiavi API e reCAPTCHA NON sono collegati: l'iscrizione
        // va a newsletter/subscribe (Olobuild_Newsletter), che salva l'iscritto nella lista di
        // Olobuild e avvisa l'amministratore senza leggerli. Qui si preparava un config firmato
        // che nessuno inviava; i controlli sono nascosti nell'inspector finché l'endpoint non li
        // userà, e le chiavi salvate restano dove sono.
        // Il reindirizzamento invece è della pagina: lo fa lo script qui sotto dopo l'iscrizione.
        $redirect = esc_url_raw( (string) ( $s['redirect_url'] ?? '' ) );
        // Content Lock: sfoca il primo blocco che segue la tile finché il visitatore non si
        // iscrive. Non nel canvas del builder, dove quel blocco si deve poter modificare.
        $lock_attivo = ! empty( $s['content_lock'] ) && empty( $s['_builder_mode'] );

        // Styles — TOKEN-FIRST: primary brand (era fallback #3B82F6 off-brand)
        $primary     = 'var(--olo-color-primary, #e1474f)';
        $bg          = $s['bg_color'] ?: 'transparent';
        $radius      = Olobuild_Tile_Utils::radius_int( $s['border_radius'] );
        $pad = Olobuild_Tile_Utils::spacing_css( $s['tile_padding'] ?? $s['padding'] ?? 32, 32 );
        $align_map   = [ 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' ];
        $align_css   = $align_map[ $s['alignment'] ] ?? 'center';
        $btn_bg      = $s['btn_bg'] ?: $primary;
        $btn_hover   = $s['btn_hover_bg'] ?: $btn_bg;
        $btn_bg_dur  = Olobuild_Tile_Utils::durata_hover( $s, 'btn_bg_hover_duration', '0.2s' );
        $focus_b     = $s['input_focus_border'] ?: $primary;
        $ih          = absint( $s['input_height'] ) ?: 44;
        $ir          = Olobuild_Tile_Utils::radius_int( $s['input_radius'] );
        $br          = Olobuild_Tile_Utils::radius_int( $s['btn_radius'] );
        $ir_h        = Olobuild_Tile_Utils::radius_hover( $s, 'input_radius_hover' );
        $br_h        = Olobuild_Tile_Utils::radius_hover( $s, 'btn_radius_hover' );
        $is_h        = $s['layout'] === 'horizontal';
        $is_minimal  = $s['layout'] === 'minimal';
        // Verticale e minimale impilano i campi in colonna: lì il «flex:1» dei campi (base 0) prende
        // il posto dell'altezza scritta e li schiacciava a 18 px. In colonna i campi non si allungano.
        $eyebrow_col = $this->safe_color_css( $s['eyebrow_color'] ?? '' ) ?: 'var(--olo-color-primary, #e1474f)';
        $accent_col  = $this->safe_color_css( $s['title_accent_color'] ?? '' ) ?: 'var(--olo-color-primary, #e1474f)';

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from sanitized values: colors via safe_color_css()/inline esc_attr(), integers via absint()/Olobuild_Tile_Utils::radius_int(), paddings via Olobuild_Tile_Utils::spacing_css(), enums via fixed literal maps/ternaries; $uid is internally generated.
        ?>
        <style>
        .<?php echo $uid; ?>{display:flex;justify-content:<?php echo $align_css; ?>}
        .<?php echo $uid; ?> .olo-nl-box{max-width:<?php echo absint($s['max_width']) ?: 600; ?>px;width:100%;background:<?php echo esc_attr($bg); ?>;border-radius:<?php echo $radius; ?>px;padding: <?php echo $pad; ?>;<?php echo ! empty( $s['box_border'] ) ? 'border:1px solid ' . esc_attr( $s['box_border'] ) . ';' : ''; ?>text-align:center}<?php echo Olobuild_Tile_Utils::radius_hover_rules( ".{$uid} .olo-nl-box", $s, 'border_radius_hover' ); ?>
        .<?php echo $uid; ?> .olo-nl-eyebrow{display:block;font-size:11px;font-weight:600;letter-spacing:.32em;text-transform:uppercase;color:<?php echo $eyebrow_col; ?>;margin:0 0 14px}
        .<?php echo $uid; ?> .olo-nl-title{font-size:<?php echo absint($s['title_size']); ?>px;font-weight:<?php echo esc_attr($s['title_weight']); ?>;color:<?php echo $s['title_color'] ? esc_attr($s['title_color']) : 'inherit'; ?>;margin:0 0 8px;line-height:1.3}
        .<?php echo $uid; ?> .olo-nl-title em{font-style:italic;color:<?php echo $accent_col; ?>}
        .<?php echo $uid; ?> .olo-nl-sub{font-size:<?php echo absint($s['subtitle_size']); ?>px;color:<?php echo $s['subtitle_color'] ? esc_attr($s['subtitle_color']) : 'var(--olo-color-text-muted, #6B7280)'; ?>;margin:0 0 20px;line-height:1.5}
        .<?php echo $uid; ?> .olo-nl-icon{font-size:<?php echo absint($s['icon_size']); ?>px;margin-bottom:12px;<?php echo $s['icon_color'] ? 'color:' . esc_attr($s['icon_color']) . ';' : ''; ?>line-height:1}
        .<?php echo $uid; ?> .olo-nl-icon img{width:<?php echo absint($s['icon_size']); ?>px;height:auto;display:inline-block}
        .<?php echo $uid; ?> .olo-nl-form{display:flex;<?php echo $is_h ? 'flex-direction:row;gap:8px;align-items:stretch' : 'flex-direction:column;gap:10px'; ?>}
        .<?php echo $uid; ?> .olo-nl-form input[type="text"],
        .<?php echo $uid; ?> .olo-nl-form input[type="email"]{height:<?php echo $ih; ?>px;padding:0 14px;background:<?php echo esc_attr($s['input_bg']); ?>;color:<?php echo esc_attr($s['input_color']); ?>;border:1px solid <?php echo esc_attr($s['input_border']); ?>;border-radius:<?php echo $ir; ?>px;font-size:14px;outline:none;transition:border-color 0.2s<?php if ( $ir_h ) echo ', ' . $ir_h['transition']; ?>;<?php echo $is_h ? 'flex:1;min-width:0' : 'flex:none;width:100%;box-sizing:border-box'; ?>}<?php if ( $ir_h ) : ?>.<?php echo $uid; ?> .olo-nl-form input[type="email"]:hover{border-radius:<?php echo $ir_h['css']; ?> !important}<?php endif; ?>
        .<?php echo $uid; ?> .olo-nl-form input:focus{border-color:<?php echo esc_attr($focus_b); ?>;box-shadow:0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent)}
        <?php if ( ! empty( $s['input_placeholder_color'] ) ) : ?>.<?php echo $uid; ?> .olo-nl-form input::placeholder{color:<?php echo esc_attr($s['input_placeholder_color']); ?>;opacity:1}<?php endif; ?>
        .<?php echo $uid; ?> .olo-nl-btn{height:<?php echo $ih; ?>px;padding:0 <?php echo $is_minimal ? '16' : '24'; ?>px;background:<?php echo esc_attr($btn_bg); ?>;color:<?php echo esc_attr($s['btn_color']); ?>;border:none;border-radius:<?php echo $br; ?>px;font-size:<?php echo absint($s['btn_font_size']); ?>px;font-weight:<?php echo esc_attr($s['btn_font_weight']); ?>;cursor:pointer;transition:background <?php echo esc_attr( $btn_bg_dur ); ?>,transform 0.15s<?php if ( $br_h ) echo ', ' . $br_h['transition']; ?>;display:inline-flex;align-items:center;gap:6px;justify-content:center;white-space:nowrap;<?php echo $is_h ? '' : 'width:100%'; ?>}
        .<?php echo $uid; ?> .olo-nl-btn:hover{background:<?php echo esc_attr($btn_hover); ?>;<?php if ( ! empty( $s['btn_color_hover'] ) ) echo 'color:' . esc_attr( $s['btn_color_hover'] ) . ';'; ?>transform:translateY(-1px)<?php if ( $br_h ) echo ';border-radius:' . $br_h['css'] . ' !important'; ?>}
        .<?php echo $uid; ?> .olo-nl-btn:focus-visible{outline:none;box-shadow:0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent)}
        .<?php echo $uid; ?> .olo-nl-privacy{font-size:11px;color:<?php echo ! empty( $s['privacy_color'] ) ? esc_attr( $s['privacy_color'] ) : 'var(--olo-color-text-muted,#9CA3AF)'; ?>;margin-top:10px;display:flex;align-items:flex-start;gap:6px;justify-content:center;text-align:left}
        .<?php echo $uid; ?> .olo-nl-privacy a{color:inherit;text-decoration:underline}
        .<?php echo $uid; ?> .olo-nl-msg{padding:16px;border-radius:8px;font-size:14px;text-align:center;display:none}
        .<?php echo $uid; ?> .olo-nl-msg.olo-nl-ok{background:<?php echo ! empty( $s['success_bg'] ) ? esc_attr( $s['success_bg'] ) : '#ECFDF5'; ?>;color:<?php echo ! empty( $s['success_color'] ) ? esc_attr( $s['success_color'] ) : '#065F46'; ?>}
        .<?php echo $uid; ?> .olo-nl-msg.olo-nl-err{background:<?php echo ! empty( $s['error_bg'] ) ? esc_attr( $s['error_bg'] ) : '#FEF2F2'; ?>;color:<?php echo ! empty( $s['error_color'] ) ? esc_attr( $s['error_color'] ) : '#991B1B'; ?>}
        .<?php echo $uid; ?> .olo-nl-loading{opacity:0.6;pointer-events:none}
        <?php if ( $lock_attivo ) : ?>
        .<?php echo $uid; ?>-lock{position:relative;overflow:hidden;max-height:<?php echo absint($s['lock_height']); ?>px}
        .<?php echo $uid; ?>-lock>:not(.olo-nl-lock-overlay){filter:blur(<?php echo absint($s['lock_blur']); ?>px);pointer-events:none;user-select:none}
        .<?php echo $uid; ?>-lock>.olo-nl-lock-overlay{position:absolute;inset:0;z-index:5;background:linear-gradient(transparent 0%,color-mix(in srgb, var(--olo-color-background, #ffffff) 95%, transparent) 60%);display:flex;align-items:flex-end;justify-content:center;padding:1.25em}
        .<?php echo $uid; ?>-lock>.olo-nl-lock-overlay p{margin:0;font-size:14px;font-weight:500;text-align:center;color:var(--olo-color-text, #374151)}
        <?php endif; ?>
        @media(max-width:768px){.<?php echo $uid; ?> .olo-nl-form{flex-direction:column}.<?php echo $uid; ?> .olo-nl-form input[type="text"],.<?php echo $uid; ?> .olo-nl-form input[type="email"]{flex:none;width:100%}.<?php echo $uid; ?> .olo-nl-btn{width:100%}}
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <div class="<?php echo esc_attr( $uid ); ?> olo-nl-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>">
          <div class="olo-nl-box">
            <?php if ( $s['icon_type'] === 'emoji' ) : ?>
              <div class="olo-nl-icon"><?php echo esc_html( $s['icon_name'] ); ?></div>
            <?php elseif ( $s['icon_type'] === 'icon' && '' !== (string) ( $s['icon'] ?? '' ) ) : ?>
              <div class="olo-nl-icon"><?php echo $this->render_icon_html( (string) $s['icon'], 1.6 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- icona del set (nome e SVG dalla libreria) ?></div>
            <?php elseif ( $s['icon_type'] === 'image' ) : ?>
              <div class="olo-nl-icon"><img src="<?php echo esc_url( $s['icon_image'] ); ?>" alt="" loading="lazy" /></div>
            <?php endif; ?>

            <?php
            list( $nt_cls, $nt_data ) = $this->tfx_attrs( $s, 'title', $s['title'] ?? '' );
            list( $ns_cls, $ns_data ) = $this->tfx_attrs( $s, 'subtitle', $s['subtitle'] ?? '' );
            ?>
            <?php if ( ! empty( $s['eyebrow'] ) ) : ?>
              <span class="olo-nl-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></span>
            <?php endif; ?>
            <?php if ( ! empty( $s['title'] ) ) : ?>
              <h3 class="olo-nl-title<?php echo $nt_cls; ?>"<?php echo $nt_data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); title filtered via wp_kses() inline ?>><?php echo wp_kses( $s['title'], [ 'em' => [], 'strong' => [], 'br' => [], 'span' => [] ] ); ?></h3>
            <?php endif; ?>
            <?php if ( ! empty( $s['subtitle'] ) ) : ?>
              <p class="olo-nl-sub<?php echo $ns_cls; ?>"<?php echo $ns_data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); subtitle escaped inline ?>><?php echo esc_html( $s['subtitle'] ); ?></p>
            <?php endif; ?>

            <div class="olo-nl-msg olo-nl-ok" id="<?php echo esc_attr( $uid ); ?>-ok"></div>
            <div class="olo-nl-msg olo-nl-err" id="<?php echo esc_attr( $uid ); ?>-err"></div>

            <form class="olo-nl-form" id="<?php echo esc_attr( $uid ); ?>-form" novalidate>
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
                  <?php if ( '' !== trim( (string) ( $s['button_icon_name'] ?? '' ) ) ) : echo $this->render_icon_html( $s['button_icon_name'], 0.8 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup from Olobuild_Tile_Base::render_icon_html(): esc_attr()'d icon name, sanitized custom SVG or the bundled Lucide SVG
                  else : ?>
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                  <?php endif; ?>
                <?php endif; ?>
              </button>
            </form>

            <?php if ( ! empty( $s['privacy_text'] ) ) : ?>
              <div class="olo-nl-privacy">
                <?php if ( ! empty( $s['privacy_required'] ) ) : ?>
                  <input type="checkbox" id="<?php echo esc_attr( $uid ); ?>-priv" required style="margin-top:2px;flex-shrink:0" />
                <?php endif; ?>
                <label <?php if ( ! empty( $s['privacy_required'] ) ) echo 'for="' . esc_attr( $uid ) . '-priv"'; ?>>
                  <?php echo wp_kses_post( $s['privacy_text'] ); ?>
                </label>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <script>
        (function(){
          var uid='<?php echo esc_js( $uid ); ?>';
          var form=document.getElementById(uid+'-form');
          if(!form)return;
          var okEl=document.getElementById(uid+'-ok');
          var errEl=document.getElementById(uid+'-err');
          var redirectUrl=<?php echo wp_json_encode( $redirect ); ?>;
          var contentLock=<?php echo $lock_attivo ? 'true' : 'false'; ?>;
          <?php
          // Content Lock. Prima la tile disegnava un riquadro sfocato VUOTO sotto di sé: il blocco
          // da nascondere non ci finiva mai dentro. Ora si sfoca il primo blocco che segue la tile
          // nella pagina (la tile sotto nella stessa colonna, se no la colonna o la sezione dopo),
          // da DOMContentLoaded perché quando lo script gira quel blocco non è ancora nel DOM. La
          // chiave è stabile: con l'id casuale della tile lo sblocco valeva per un caricamento solo.
          ?>
          var lockKey=<?php echo wp_json_encode( 'olo_nl_unlocked_' . $chiave ); ?>;
          var lockEl=null,lockOv=null;
          function sbloccato(){try{return !!localStorage.getItem(lockKey)}catch(e){return false}}
          function successivo(){
            var cur=form.closest('.olo-frontend-tile')||form.closest('.'+uid),n;
            while(cur){
              if(cur===document.body){return null}
              if(cur.classList.contains('olo-template')){return null}
              n=cur.nextElementSibling;
              while(n){
                if(!/^(SCRIPT|STYLE|LINK|TEMPLATE|NOSCRIPT)$/.test(n.tagName)){return n}
                n=n.nextElementSibling;
              }
              cur=cur.parentElement;
            }
            return null;
          }
          function blocca(){
            if(sbloccato()){return}
            lockEl=successivo();
            if(!lockEl){return}
            lockEl.classList.add(uid+'-lock');
            lockEl.setAttribute('inert','');
            lockOv=document.createElement('div');
            lockOv.className='olo-nl-lock-overlay';
            var p=document.createElement('p');
            p.textContent=<?php echo wp_json_encode( (string) $s['lock_message'] ); ?>;
            lockOv.appendChild(p);
            lockEl.appendChild(lockOv);
          }
          function sblocca(){
            try{localStorage.setItem(lockKey,'1')}catch(e){}
            if(!lockEl){return}
            lockEl.classList.remove(uid+'-lock');
            lockEl.removeAttribute('inert');
            if(lockOv){if(lockOv.parentNode){lockOv.parentNode.removeChild(lockOv)}}
            lockEl=null;lockOv=null;
          }
          if(contentLock){
            if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',blocca)}else{blocca()}
          }

          form.addEventListener('submit',function(e){
            e.preventDefault();
            var emailField=form.querySelector('input[type="email"]');
            var email=emailField?emailField.value.trim():'';
            if(!email){errEl.textContent=<?php echo wp_json_encode( olobuild_t( 'Inserisci un indirizzo email' ) ); ?>;errEl.style.display='block';return}

            // Privacy check
            var privCb=form.querySelector('input[type="checkbox"][required]');
            if(privCb){
              if(!privCb.checked){errEl.textContent='Accetta la privacy policy';errEl.style.display='block';return}
            }

            form.classList.add('olo-nl-loading');
            errEl.style.display='none';

            // Payload mirato per il gestore newsletter dedicato.
            var nameField=form.querySelector('input[name="name"]');
            var payload={
              email:email,
              name:nameField?nameField.value:'',
              source:(location.pathname||'')+(document.title?(' — '+document.title):''),
              success_message:'<?php echo esc_js( $s['success_message'] ); ?>'
            };
            var hp1=form.querySelector('input[name="olo_website_url"]'); if(hp1&&hp1.value){payload.olo_website_url=hp1.value}
            var hp2=form.querySelector('input[name="olo_hp_field"]'); if(hp2&&hp2.value){payload.olo_hp_field=hp2.value}

            fetch('<?php echo esc_url( rest_url( 'olobuild/v1/newsletter/subscribe' ) ); ?>',{
              method:'POST',
              headers:{'Content-Type':'application/json'},
              body:JSON.stringify(payload)
            })
            .then(function(r){return r.json()})
            .then(function(data){
              form.classList.remove('olo-nl-loading');
              var ok=!!(data&&data.success);
              var msg=(data&&data.data&&data.data.message)?data.data.message:((data&&data.message)?data.message:'');
              if(ok){
                form.style.display='none';
                var priv=form.parentElement.querySelector('.olo-nl-privacy');
                if(priv)priv.style.display='none';
                okEl.textContent=msg||'<?php echo esc_js( $s['success_message'] ); ?>';
                okEl.style.display='block';

                if(contentLock){sblocca()}

                // Redirect: quello della tile (prima non si leggeva), se no quello della risposta.
                var vai=redirectUrl;
                if(!vai){if(data.data){vai=data.data.redirect||''}}
                if(vai){setTimeout(function(){window.location.href=vai},1500)}
              }else{
                errEl.textContent=msg||'Errore durante l\'iscrizione';
                errEl.style.display='block';
              }
            })
            .catch(function(){
              form.classList.remove('olo-nl-loading');
              errEl.textContent=<?php echo wp_json_encode( olobuild_t( 'Errore di connessione' ) ); ?>;
              errEl.style.display='block';
            });
          });
        })();
        </script>
        <?php

        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Text_Effects::css() from fixed effect definitions
        $this->tfx_print_script();
        $html = ob_get_clean();

        // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized border settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base border helpers from sanitized border settings
        }
        return $html;
    }
}
