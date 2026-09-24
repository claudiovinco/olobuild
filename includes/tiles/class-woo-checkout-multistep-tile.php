<?php
/**
 * WooCommerce Multi-step Checkout — split checkout into steps with progress bar.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Woo_Checkout_Multistep_Tile extends Olobuild_Tile_Base {

    protected $type     = 'woo_checkout_multistep';
    protected $name     = 'Checkout Multi-step WC';
    protected $icon     = 'dashicons-cart';
    protected $category = 'woocommerce';
    protected $defaults = [
        'step_labels'      => 'Dati,Spedizione,Pagamento',
        'step_style'       => 'progress',
        'accent_color'     => '',
        'step_bg'          => '',
        'active_color'     => '',
        'text_color'       => '',
        'card_radius'      => 12,
        'show_order_review' => true,
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
        if ( ! class_exists( 'WooCommerce' ) ) {
            return '<div style="padding:20px;text-align:center;color:var(--olo-color-warning, #b45309);background:color-mix(in srgb, var(--olo-color-warning, #b45309) 12%, #fff);border:1px solid var(--olo-color-warning, #b45309);border-radius:8px;">'
                 . esc_html( olobuild_t( 'WooCommerce non attivo. Installa e attiva WooCommerce per utilizzare questo elemento.' ) )
                 . '</div>';
        }

        if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
            return '<div style="padding:40px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);background:var(--olo-color-muted, #F3F4F6);border-radius:8px;">'
                 . esc_html( olobuild_t( 'Questo elemento funziona solo nella pagina Checkout.' ) )
                 . '</div>';
        }

        // «Ordine ricevuto» e «Paga per l'ordine» stanno sotto la pagina Checkout ma non hanno
        // il form da dividere: solo lo shortcode, senza barra dei passi, bottoni né script.
        if ( function_exists( 'is_wc_endpoint_url' ) && ( is_wc_endpoint_url( 'order-received' ) || is_wc_endpoint_url( 'order-pay' ) ) ) {
            return '<div class="olo-woo-multistep">' . do_shortcode( '[woocommerce_checkout]' ) . '</div>';
        }

        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-woo-ms-' . wp_rand( 10000, 99999 );

        $accent  = $this->safe_color_css( $s['accent_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $active  = $this->safe_color_css( $s['active_color'] ) ?: $accent;
        $text_c  = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text, #1f2937)';
        $step_bg = $this->safe_color_css( $s['step_bg'] ) ?: 'var(--olo-color-surface-alt, #f6f7f9)';
        $on_c    = 'var(--olo-color-on-primary, #ffffff)';
        $radius  = Olobuild_Tile_Utils::border_radius( $s['card_radius'] ?? 0 );
        $radius_hover_css = Olobuild_Tile_Utils::radius_force_css( $s['card_radius_hover'] ?? null );

        // Passi per RUOLO, non per posizione: 1ª etichetta = dati, 2ª = spedizione,
        // 3ª = riepilogo e pagamento (#order_review intero: #payment e «Effettua ordine»
        // stanno lì dentro); dalla 4ª in poi non si usano. Il passo Spedizione esiste solo
        // se il carrello chiede un indirizzo: è la condizione del template di WooCommerce,
        // che stampa comunque .woocommerce-shipping-fields, vuoto.
        $labels = array_map( 'trim', explode( ',', is_scalar( $s['step_labels'] ) ? (string) $s['step_labels'] : '' ) );
        $parts  = [
            'billing'  => ( isset( $labels[0] ) && '' !== $labels[0] ) ? $labels[0] : olobuild_t( 'Dati' ),
            'shipping' => ( isset( $labels[1] ) && '' !== $labels[1] ) ? $labels[1] : olobuild_t( 'Spedizione' ),
            'review'   => ( isset( $labels[2] ) && '' !== $labels[2] ) ? $labels[2] : olobuild_t( 'Pagamento' ),
        ];
        $has_ship = function_exists( 'WC' ) && WC()->cart && WC()->cart->needs_shipping_address();
        if ( ! $has_ship ) {
            unset( $parts['shipping'] );
        }
        $last = count( $parts ) - 1;

        ob_start();
        ?>
        <div id="<?php echo esc_attr( $uid ); ?>" class="olo-woo-multistep" style="color:<?php echo $text_c; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- safe_color_css() whitelisted color or fixed var() fallback ?>">

            <!-- Progress Steps -->
            <div class="olo-wms-progress" style="display:flex;justify-content:center;gap:0;margin-bottom:30px">
                <?php foreach ( array_keys( $parts ) as $i => $part ) : ?>
                <div class="olo-wms-step<?php echo $i === 0 ? ' olo-wms-active' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed literal class ternary; colors via safe_color_css() with fixed fallbacks; radius absint-built by Olobuild_Tile_Utils::border_radius() ?>" data-step="<?php echo (int) $i; ?>" data-part="<?php echo esc_attr( $part ); ?>" data-live="<?php echo esc_attr( sprintf( olobuild_t( 'Passo %1$d di %2$d: %3$s' ), $i + 1, $last + 1, $parts[ $part ] ) ); ?>" role="button" tabindex="0"<?php echo $i === 0 ? ' aria-current="step"' : ''; ?> style="flex:1;text-align:center;padding:12px 16px;position:relative;font-size:13px;font-weight:600;cursor:pointer;transition:all .2s;background:<?php echo $i === 0 ? $accent : $step_bg; ?>;color:<?php echo $i === 0 ? $on_c : $text_c; ?>;<?php if ( $i === 0 ) echo 'border-radius:' . $radius . ';border-top-right-radius:0;border-bottom-right-radius:0;'; elseif ( $i === $last ) echo 'border-radius:' . $radius . ';border-top-left-radius:0;border-bottom-left-radius:0;'; ?>">
                    <span class="olo-wms-num" style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:<?php echo $i === 0 ? 'color-mix(in srgb, var(--olo-color-on-primary, #ffffff) 30%, transparent)' : 'var(--olo-color-border, #E5E7EB)'; ?>;font-size:12px;margin-right:6px"><?php echo (int) ( $i + 1 ); ?></span>
                    <?php echo esc_html( $parts[ $part ] ); ?>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- WooCommerce Checkout wrapped in steps -->
            <div class="olo-wms-content">
                <?php echo do_shortcode( '[woocommerce_checkout]' ); ?>
            </div>

            <?php // Indietro/Continua fuori dal markup di WooCommerce, che l'AJAX sostituisce; nascosti finché lo script non li attiva (senza JS resta il checkout intero). ?>
            <div class="olo-wms-nav" hidden>
                <button type="button" class="olo-wms-prev"><?php echo esc_html( olobuild_t( 'Indietro' ) ); ?></button>
                <button type="button" class="olo-wms-next"><?php echo esc_html( olobuild_t( 'Continua' ) ); ?></button>
            </div>
            <?php // Annuncia il cambio di passo quando il focus resta dov'è (Continua/Indietro a un passo intermedio, clic sulla barra). ?>
            <span class="olo-wms-live" role="status" aria-live="polite" aria-atomic="true"></span>

        </div>

        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist for every colour, Olobuild_Tile_Utils radius helpers (absint-built) and the internally generated $uid. ?>
        <style>
            #<?php echo $uid; ?> .woocommerce-checkout .col2-set{display:block;float:none;width:auto}
            #<?php echo $uid; ?> .col2-set .col-1,#<?php echo $uid; ?> .col2-set .col-2,#<?php echo $uid; ?> #order_review_heading,#<?php echo $uid; ?> #order_review{float:none;width:auto}
            <?php if ( ! $has_ship ) : ?>#<?php echo $uid; ?> .woocommerce-shipping-fields{display:none}<?php endif; ?>
            #<?php echo $uid; ?> .woocommerce-billing-fields,
            #<?php echo $uid; ?> .woocommerce-shipping-fields,
            #<?php echo $uid; ?> #payment,
            #<?php echo $uid; ?> .woocommerce-checkout-review-order{background:<?php echo $step_bg; ?>;padding:24px;border-radius:<?php echo $radius; ?>;margin-bottom:20px;transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}
            <?php if ( $radius_hover_css !== '' ) : ?>#<?php echo $uid; ?> .woocommerce-billing-fields:hover,#<?php echo $uid; ?> .woocommerce-shipping-fields:hover,#<?php echo $uid; ?> #payment:hover,#<?php echo $uid; ?> .woocommerce-checkout-review-order:hover{border-radius:<?php echo $radius_hover_css; ?> !important}<?php endif; ?>
            #<?php echo $uid; ?> .olo-wms-step:hover{opacity:.85}
            #<?php echo $uid; ?> .olo-wms-step.olo-wms-done{background:<?php echo $accent; ?>;opacity:.6;color:<?php echo $on_c; ?>}
            #<?php echo $uid; ?> .olo-wms-nav{display:flex;justify-content:space-between;gap:12px;margin-top:20px}
            #<?php echo $uid; ?> .olo-wms-nav[hidden],#<?php echo $uid; ?> .olo-wms-nav button[hidden]{display:none}
            #<?php echo $uid; ?> .olo-wms-nav button{padding:10px 24px;border-radius:6px;cursor:pointer}
            #<?php echo $uid; ?> .olo-wms-prev{border:1px solid var(--olo-color-border, #E5E7EB);background:var(--olo-color-background, #FFFFFF);color:inherit;font-weight:500}
            #<?php echo $uid; ?> .olo-wms-next{border:none;background:<?php echo $accent; ?>;color:<?php echo $on_c; ?>;font-weight:600;margin-left:auto}
            #<?php echo $uid; ?> .olo-wms-step:focus-visible,#<?php echo $uid; ?> .olo-wms-nav button:focus-visible{outline:2px solid <?php echo $accent; ?>;outline-offset:2px}
            #<?php echo $uid; ?> .olo-wms-live{position:absolute;width:1px;height:1px;margin:-1px;padding:0;border:0;overflow:hidden;clip:rect(0 0 0 0);clip-path:inset(50%);white-space:nowrap}
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <script>
        (function(){
            var wrap=document.getElementById('<?php echo esc_js( $uid ); ?>');
            if(!wrap)return;
            var bar=wrap.querySelector('.olo-wms-progress');
            var nav=wrap.querySelector('.olo-wms-nav');
            var prev=wrap.querySelector('.olo-wms-prev');
            var next=wrap.querySelector('.olo-wms-next');
            var steps=wrap.querySelectorAll('.olo-wms-step');
            var live=wrap.querySelector('.olo-wms-live');
            if(!bar||!nav||!prev||!next||!steps.length)return;
            var ACC='<?php echo esc_js( $accent ); ?>',BG='<?php echo esc_js( $step_bg ); ?>',TXT='<?php echo esc_js( $text_c ); ?>',ON='<?php echo esc_js( $on_c ); ?>';
            var parts=[];
            steps.forEach(function(s){parts.push(s.getAttribute('data-part'))});
            var hasShip=parts.indexOf('shipping')!==-1;

            // Ogni passo è un GRUPPO di nodi che l'AJAX di WooCommerce non sostituisce
            // (update_checkout rimpiazza #payment e la tabella DENTRO #order_review): si
            // cercano di nuovo a ogni chiamata, nessun riferimento tenuto da una all'altra.
            var SEL={
                billing:'.woocommerce-billing-fields,.woocommerce-account-fields'+(hasShip?'':',.woocommerce-additional-fields'),
                shipping:'.woocommerce-shipping-fields,.woocommerce-additional-fields',
                review:'#order_review_heading,#order_review'
            };
            var current=0;

            function inStep(i,node){
                var list=wrap.querySelectorAll(SEL[parts[i]]);
                for(var k=0;k<list.length;k++){if(list[k].contains(node))return true}
                return false;
            }

            function showStep(idx){
                // Niente form (login obbligatorio, sessione scaduta): niente passi.
                if(!wrap.querySelector('form.checkout')){bar.style.display='none';nav.hidden=true;return}
                if(idx>parts.length-1)idx=parts.length-1;
                if(idx<0)idx=0;
                parts.forEach(function(p,i){
                    wrap.querySelectorAll(SEL[p]).forEach(function(n){n.style.display=i===idx?'':'none'});
                });
                steps.forEach(function(s,i){
                    s.classList.remove('olo-wms-active','olo-wms-done');
                    if(i===idx){s.classList.add('olo-wms-active');s.setAttribute('aria-current','step')}
                    else{s.removeAttribute('aria-current');if(i<idx){s.classList.add('olo-wms-done')}}
                    s.style.background=i<=idx?ACC:BG;
                    s.style.color=i<=idx?ON:TXT;
                });
                bar.style.display='flex';
                nav.hidden=false;
                prev.hidden=idx===0;
                next.hidden=idx===parts.length-1;
                current=idx;
            }

            function stepOf(node){
                for(var i=0;i<parts.length;i++){if(inStep(i,node))return i}
                return -1;
            }

            function go(idx){
                var was=document.activeElement;
                var at=stepOf(was);
                showStep(idx);
                // Il cambio di passo può nascondere proprio ciò che ha il focus (Continua
                // all'ultimo passo, Indietro al primo, il campo in cui si è premuto Invio): il
                // focus passa al passo corrente della barra, invece di cadere sul body.
                var lost=(was===prev||was===next)?was.hidden:(at>=0?at!==current:false);
                if(lost)steps[current].focus({preventScroll:true});
                // Se il focus resta dov'è il cambio non si sente: lo dice la regione live
                // («Passo 2 di 3: Spedizione»). Se il focus va sul passo lo annuncia già il
                // lettore di schermo: la regione si svuota, così il prossimo annuncio è sempre
                // un cambio di testo anche quando torna lo stesso passo.
                if(live)live.textContent=lost?'':(steps[current].getAttribute('data-live')||'');
                window.scrollTo({top:wrap.getBoundingClientRect().top+window.pageYOffset-60,behavior:'smooth'});
            }

            steps.forEach(function(s,i){
                s.addEventListener('click',function(){go(i)});
                s.addEventListener('keydown',function(e){
                    if(e.key==='Enter'||e.key===' '){e.preventDefault();go(i)}
                });
            });
            prev.addEventListener('click',function(){go(current-1)});
            next.addEventListener('click',function(){go(current+1)});

            // Invio in un campo prima dell'ultimo passo porta avanti e non invia l'ordine:
            // #place_order resta il pulsante predefinito del form anche quando è nascosto.
            // Un Invio già usato da altri (scelta di un suggerimento dell'autocompletamento
            // indirizzi, che chiama preventDefault) resta loro; i bottoni e i file lo usano per sé.
            wrap.addEventListener('keydown',function(e){
                var t=e.target;
                if(e.key!=='Enter'||e.defaultPrevented||e.isComposing||current>=parts.length-1)return;
                if(!t||t.tagName!=='INPUT'||!t.form||!t.form.classList.contains('checkout'))return;
                if(/^(button|submit|reset|image|file)$/i.test(t.type))return;
                e.preventDefault();
                go(current+1);
            });

            // WooCommerce fa di ogni voce dell'avviso d'errore un link al suo campo (#id): se il
            // campo sta in un passo nascosto l'ancora non porta a niente. Si apre prima quel
            // passo, poi il salto nativo all'ancora fa il resto (niente preventDefault).
            wrap.addEventListener('click',function(e){
                var a=e.target.closest?e.target.closest('.woocommerce-NoticeGroup-checkout a[href^="#"]'):null;
                if(!a)return;
                var id=a.getAttribute('href').slice(1);
                var f=id?document.getElementById(id):null;
                var i=f?stepOf(f):-1;
                if(i>=0)showStep(i);
            });

            // Eventi jQuery di WooCommerce: dopo ogni ricalcolo si riapplica il passo; dopo un
            // errore dell'ordine si torna al passo del primo campo segnalato (data-id).
            var bound=false;
            function bindWc(){
                var jq=window.jQuery;
                if(bound||!jq)return;
                bound=true;
                jq(document.body).on('updated_checkout',function(){showStep(current)});
                jq(document.body).on('checkout_error',function(){
                    var el=wrap.querySelector('.woocommerce-NoticeGroup-checkout [data-id]');
                    var f=el?document.getElementById(el.getAttribute('data-id')):null;
                    var i=f?stepOf(f):-1;
                    if(i>=0)showStep(i);
                });
            }

            showStep(0);
            if(window.jQuery){bindWc()}
            else if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',bindWc)}
            else{window.addEventListener('load',bindWc)}
        })();
        </script>
        <?php
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( "#{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( "#{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo "#{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }
}
