<?php
/**
 * WooCommerce Multi-step Checkout — split checkout into steps with progress bar.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Woo_Checkout_Multistep_Tile extends Olo_Tile_Base {

    protected $type     = 'woo_checkout_multistep';
    protected $name     = 'Checkout Multi-step WC';
    protected $icon     = 'dashicons-cart';
    protected $category = 'woocommerce';
    protected $defaults = [
        'step_labels'      => 'Dati,Spedizione,Pagamento,Conferma',
        'step_style'       => 'progress',
        'accent_color'     => '',
        'step_bg'          => '#F9FAFB',
        'active_color'     => '',
        'text_color'       => '#374151',
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
            return '<div style="padding:20px;text-align:center;color:#92400E;background:#FEF3C7;border-radius:8px;">WooCommerce non attivo.</div>';
        }

        if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
            return '<div style="padding:40px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);background:var(--olo-color-muted, #F3F4F6);border-radius:8px;">Questo elemento funziona solo nella pagina Checkout.</div>';
        }

        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-woo-ms-' . wp_rand( 10000, 99999 );

        $accent  = $this->safe_color_css( $s['accent_color'] ) ?: 'var(--olo-color-primary, #6366F1)';
        $active  = $this->safe_color_css( $s['active_color'] ) ?: $accent;
        $text_c  = $this->safe_color_css( $s['text_color'] ) ?: '#374151';
        $step_bg = $this->safe_color_css( $s['step_bg'] ) ?: '#F9FAFB';
        $radius  = Olo_Tile_Utils::border_radius( $s['card_radius'] ?? 0 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['card_radius_hover'] ?? null );
        $labels  = array_map( 'trim', explode( ',', $s['step_labels'] ) );

        ob_start();
        ?>
        <div id="<?php echo esc_attr( $uid ); ?>" class="olo-woo-multistep" style="color:<?php echo $text_c; ?>">

            <!-- Progress Steps -->
            <div class="olo-wms-progress" style="display:flex;justify-content:center;gap:0;margin-bottom:30px">
                <?php foreach ( $labels as $i => $label ) : ?>
                <div class="olo-wms-step<?php echo $i === 0 ? ' olo-wms-active' : ''; ?>" data-step="<?php echo $i; ?>" style="flex:1;text-align:center;padding:12px 16px;position:relative;font-size:13px;font-weight:600;cursor:pointer;transition:all .2s;background:<?php echo $i === 0 ? $accent : $step_bg; ?>;color:<?php echo $i === 0 ? '#fff' : $text_c; ?>;<?php if ( $i === 0 ) echo 'border-radius:' . $radius . ';border-top-right-radius:0;border-bottom-right-radius:0;'; elseif ( $i === count( $labels ) - 1 ) echo 'border-radius:' . $radius . ';border-top-left-radius:0;border-bottom-left-radius:0;'; ?>">
                    <span class="olo-wms-num" style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:<?php echo $i === 0 ? 'rgba(255,255,255,.3)' : 'var(--olo-color-border, #E5E7EB)'; ?>;font-size:12px;margin-right:6px"><?php echo $i + 1; ?></span>
                    <?php echo esc_html( $label ); ?>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- WooCommerce Checkout wrapped in steps -->
            <div class="olo-wms-content">
                <?php echo do_shortcode( '[woocommerce_checkout]' ); ?>
            </div>

        </div>

        <style>
            #<?php echo $uid; ?> .woocommerce-checkout .col2-set{display:block}
            #<?php echo $uid; ?> .woocommerce-billing-fields,
            #<?php echo $uid; ?> .woocommerce-shipping-fields,
            #<?php echo $uid; ?> #payment,
            #<?php echo $uid; ?> .woocommerce-checkout-review-order{background:<?php echo $step_bg; ?>;padding:24px;border-radius:<?php echo $radius; ?>;margin-bottom:20px;transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}
            <?php if ( $radius_hover_css !== '' ) : ?>#<?php echo $uid; ?> .woocommerce-billing-fields:hover,#<?php echo $uid; ?> .woocommerce-shipping-fields:hover,#<?php echo $uid; ?> #payment:hover,#<?php echo $uid; ?> .woocommerce-checkout-review-order:hover{border-radius:<?php echo $radius_hover_css; ?> !important}<?php endif; ?>
            #<?php echo $uid; ?> .olo-wms-step:hover{opacity:.85}
            #<?php echo $uid; ?> .olo-wms-step.olo-wms-done{background:<?php echo $accent; ?>;opacity:.6;color:#fff}
        </style>

        <script>
        (function(){
            var wrap=document.getElementById('<?php echo esc_js( $uid ); ?>');
            if(!wrap)return;

            // Identify checkout sections
            var sections=[
                wrap.querySelector('.woocommerce-billing-fields'),
                wrap.querySelector('.woocommerce-shipping-fields'),
                wrap.querySelector('#payment'),
                wrap.querySelector('.woocommerce-checkout-review-order')
            ].filter(function(s){return s});

            var steps=wrap.querySelectorAll('.olo-wms-step');
            var current=0;

            function showStep(idx){
                sections.forEach(function(s,i){
                    s.style.display=i===idx?'block':'none';
                });
                steps.forEach(function(s,i){
                    s.classList.remove('olo-wms-active','olo-wms-done');
                    if(i===idx){s.classList.add('olo-wms-active');s.style.background='<?php echo esc_js( $accent ); ?>';s.style.color='#fff'}
                    else if(i<idx){s.classList.add('olo-wms-done');s.style.background='<?php echo esc_js( $accent ); ?>';s.style.color='#fff'}
                    else{s.style.background='<?php echo esc_js( $step_bg ); ?>';s.style.color='<?php echo esc_js( $text_c ); ?>'}
                });
                current=idx;
            }

            steps.forEach(function(s,i){
                s.addEventListener('click',function(){showStep(i)});
            });

            // Initial: show only first step
            if(sections.length>1){showStep(0)}

            // Add next/prev buttons to each section
            sections.forEach(function(sec,i){
                if(i>=sections.length-1)return;
                var nav=document.createElement('div');
                nav.style.cssText='display:flex;justify-content:space-between;margin-top:20px;gap:12px';
                if(i>0){
                    var prev=document.createElement('button');
                    prev.type='button';prev.textContent='Indietro';
                    prev.style.cssText='padding:10px 24px;border:1px solid var(--olo-color-border, #E5E7EB);background:var(--olo-color-background, #FFFFFF);border-radius:6px;cursor:pointer;font-weight:500';
                    prev.addEventListener('click',function(){showStep(i-1);window.scrollTo({top:wrap.offsetTop-60,behavior:"smooth"})});
                    nav.appendChild(prev);
                }
                var next=document.createElement('button');
                next.type='button';next.textContent='Continua';
                next.style.cssText='padding:10px 24px;border:none;background:<?php echo esc_js( $accent ); ?>;color:#fff;border-radius:6px;cursor:pointer;font-weight:600;margin-left:auto';
                next.addEventListener('click',function(){showStep(i+1);window.scrollTo({top:wrap.offsetTop-60,behavior:"smooth"})});
                nav.appendChild(next);
                sec.appendChild(nav);
            });
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
}
