<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Countdown_Tile extends Olo_Tile_Base {

    protected $type     = 'countdown';
    protected $name     = 'Conto alla rovescia';
    protected $icon     = 'dashicons-clock';
    protected $category = 'marketing';
    protected $defaults = [
        'preset' => 'custom',
        'countdown_style'     => 'custom',
        'countdown_type'      => 'date',
        'evergreen_hours'     => '0',
        'evergreen_minutes'   => '30',
        'evergreen_loop'      => false,
        'expire_action'       => 'none',
        'expire_redirect_url' => '',
        'expire_message'      => 'Tempo scaduto!',
        'display_mode'        => 'block',
        'target_date'         => '2026-12-31T23:59',
        'show_days'           => true,
        'show_hours'          => true,
        'show_minutes'        => true,
        'show_seconds'        => true,
        'expired_message'     => 'L\'evento è iniziato!',
        'label_days'          => 'Giorni',
        'label_hours'         => 'Ore',
        'label_minutes'       => 'Minuti',
        'label_seconds'       => 'Secondi',
        'separator'           => ':',
        'bg_color'            => '',
        'text_color'          => '',
        'accent_color'        => '',
        'separator_color'     => '',
        'number_font_size'    => '48',
        'number_font_weight'  => '700',
        'label_font_size'     => '12',
        'label_font_weight'   => '500',
        'separator_font_size' => '32',
        'item_min_width'      => '70',
        'item_bg_color'       => '',
        'item_radius'         => '0',
        'item_padding'        => '0',
        'tile_padding'        => [ 'top' => 32, 'right' => 32, 'bottom' => 32, 'left' => 32 ],
        'padding'             => '32', // legacy, kept for backward compat
        'shadow'              => 'none',
        'border_width'        => '0',
        'border_color'        => '',
        'border_radius'       => '0',
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
        return [
            [ 'key' => 'target_date',     'type' => 'text',   'label' => 'Target Date (YYYY-MM-DDTHH:MM)' ],
            [ 'key' => 'show_days',       'type' => 'toggle', 'label' => 'Show Days' ],
            [ 'key' => 'show_hours',      'type' => 'toggle', 'label' => 'Show Hours' ],
            [ 'key' => 'show_minutes',    'type' => 'toggle', 'label' => 'Show Minutes' ],
            [ 'key' => 'show_seconds',    'type' => 'toggle', 'label' => 'Show Seconds' ],
            [ 'key' => 'expired_message', 'type' => 'text',   'label' => 'Expired Message' ],
            [ 'key' => 'label_days',      'type' => 'text',   'label' => 'Label Days' ],
            [ 'key' => 'label_hours',     'type' => 'text',   'label' => 'Label Hours' ],
            [ 'key' => 'label_minutes',   'type' => 'text',   'label' => 'Label Minutes' ],
            [ 'key' => 'label_seconds',   'type' => 'text',   'label' => 'Label Seconds' ],
            [ 'key' => 'separator',       'type' => 'text',   'label' => 'Separator' ],
            [ 'key' => 'bg_color',        'type' => 'color',  'label' => 'Background' ],
            [ 'key' => 'text_color',      'type' => 'color',  'label' => 'Text Color' ],
            [ 'key' => 'accent_color',    'type' => 'color',  'label' => 'Accent Color' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );
        if ( ( $s['countdown_style'] ?? 'custom' ) === 'uikit' ) {
            return $this->render_uikit( $s );
        }
        return $this->render_custom( $s );
    }

    private function render_custom( $s ) {
        $uid = 'mcd-' . wp_rand( 10000, 99999 );

        $num_fs  = absint( $s['number_font_size'] );
        $num_fw  = absint( $s['number_font_weight'] );
        $lbl_fs  = absint( $s['label_font_size'] );
        $lbl_fw  = absint( $s['label_font_weight'] );
        $sep_fs  = absint( $s['separator_font_size'] );
        $min_w   = absint( $s['item_min_width'] );
        $pad = Olo_Tile_Utils::spacing_css( $s['tile_padding'] ?? $s['padding'] ?? 32, 32 );
        // bg_color è un controllo legacy ridondante con `bg` (sfondo creativo universale).
        // Lo applichiamo solo se settato esplicitamente, NON come default vuoto — altrimenti
        // produrremmo `background: ;` (invalido) e nasconderemmo il bg universale applicato
        // dal wrapper esterno .olo-frontend-tile dal frontend renderer.
        $bg      = $this->safe_color_css( $s['bg_color'] ?? '' );
        $fg      = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text, #374151)';
        $accent  = $this->safe_color_css( $s['accent_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $is_inline = ( $s['display_mode'] === 'inline' );
        // Card per singola unità (giorni/ore/min/sec) — opzionali, default 0 = no card.
        $item_bg     = $this->safe_color_css( $s['item_bg_color'] ?? '' );
        $item_radius = absint( $s['item_radius'] ?? 0 );
        $item_pad    = absint( $s['item_padding'] ?? 0 );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                display: flex;
                justify-content: center;
                align-items: <?php echo $is_inline ? 'baseline' : 'center'; ?>;
                flex-wrap: <?php echo $is_inline ? 'nowrap' : 'wrap'; ?>;
                gap: <?php echo $is_inline ? '4px' : '8px'; ?>;
                padding: <?php echo $is_inline ? max(8, round((is_numeric($pad) ? (float)$pad : 32) / 2)) : $pad; ?>px;
                <?php if ( $bg ) : ?>background: <?php echo $bg; ?>;<?php endif; ?>
                color: <?php echo $fg; ?>;
            }
            .<?php echo $uid; ?> .mcd-item {
                text-align: center;
                <?php if ( $item_bg ) : ?>background: <?php echo $item_bg; ?>;<?php endif; ?>
                <?php if ( $item_radius > 0 ) : ?>border-radius: <?php echo $item_radius; ?>px;<?php endif; ?>
                <?php if ( $item_pad > 0 ) : ?>padding: <?php echo $item_pad; ?>px;<?php endif; ?>
                <?php if ( ! $is_inline ) : ?>
                min-width: <?php echo $min_w; ?>px;
                <?php else : ?>
                display: inline-flex;
                align-items: baseline;
                gap: 2px;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .mcd-num {
                font-size: <?php echo $is_inline ? max(16, round($num_fs * 0.55)) : $num_fs; ?>px;
                font-weight: <?php echo $num_fw; ?>;
                line-height: 1.15;
                color: <?php echo $accent; ?>;
            }
            .<?php echo $uid; ?> .mcd-label {
                font-size: <?php echo $lbl_fs; ?>px;
                font-weight: <?php echo $lbl_fw; ?>;
                opacity: 0.7;
                <?php if ( ! $is_inline ) : ?>
                margin-top: 4px;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                <?php else : ?>
                text-transform: lowercase;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .mcd-sep {
                font-size: <?php echo $is_inline ? max(12, round($sep_fs * 0.55)) : $sep_fs; ?>px;
                font-weight: 700;
                opacity: 0.45;
                line-height: 1;
                <?php if ( ! $is_inline ) : ?>
                align-self: flex-start;
                padding-top: <?php echo max( 0, round( $num_fs * 0.15 ) ); ?>px;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .mcd-expire-msg {
                text-align: center;
                font-size: 1.5em;
                padding: 20px;
            }
        </style>
        <div id="<?php echo esc_attr( $uid ); ?>"
            class="olo-countdown <?php echo esc_attr( $uid ); ?> olo-cd-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>"
            data-target="<?php echo esc_attr( $s['target_date'] ); ?>"
            data-expired="<?php echo esc_attr( $s['expired_message'] ); ?>"
            data-olo-countdown-type="<?php echo esc_attr( $s['countdown_type'] ); ?>"
            data-olo-evergreen-hours="<?php echo absint( $s['evergreen_hours'] ); ?>"
            data-olo-evergreen-minutes="<?php echo absint( $s['evergreen_minutes'] ); ?>"
            data-olo-evergreen-loop="<?php echo $s['evergreen_loop'] ? '1' : '0'; ?>"
            data-olo-expire-action="<?php echo esc_attr( $s['expire_action'] ); ?>"
            data-olo-expire-redirect="<?php echo esc_url( $s['expire_redirect_url'] ); ?>"
            data-olo-expire-message="<?php echo esc_attr( $s['expire_message'] ); ?>">
            <?php
            $units = [];
            if ( $s['show_days'] )    $units[] = [ 'key' => 'days',    'label' => $s['label_days'],    'short' => 'd' ];
            if ( $s['show_hours'] )   $units[] = [ 'key' => 'hours',   'label' => $s['label_hours'],   'short' => 'h' ];
            if ( $s['show_minutes'] ) $units[] = [ 'key' => 'minutes', 'label' => $s['label_minutes'], 'short' => 'm' ];
            if ( $s['show_seconds'] ) $units[] = [ 'key' => 'seconds', 'label' => $s['label_seconds'], 'short' => 's' ];

            foreach ( $units as $i => $unit ) :
                if ( $i > 0 && ! empty( $s['separator'] ) ) : ?>
                    <div class="mcd-sep"><?php echo esc_html( $s['separator'] ); ?></div>
                <?php endif; ?>
                <div class="mcd-item">
                    <div class="mcd-num" data-unit="<?php echo esc_attr( $unit['key'] ); ?>">00</div>
                    <div class="mcd-label"><?php echo esc_html( $is_inline ? $unit['short'] : $unit['label'] ); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <script>
        (function(){
            var el=document.getElementById('<?php echo esc_js( $uid ); ?>');
            if(!el)return;
            var ctype=el.getAttribute('data-olo-countdown-type');
            var expireAction=el.getAttribute('data-olo-expire-action');
            var expireRedirect=el.getAttribute('data-olo-expire-redirect');
            var expireMessage=el.getAttribute('data-olo-expire-message');
            var egLoop=el.getAttribute('data-olo-evergreen-loop')==='1';
            var target;

            if(ctype==='evergreen'){
                var egH=parseInt(el.getAttribute('data-olo-evergreen-hours'))||0;
                var egM=parseInt(el.getAttribute('data-olo-evergreen-minutes'))||0;
                var egDuration=(egH*3600+egM*60)*1000;
                var storageKey='olo_eg_'+location.pathname+'_<?php echo esc_js( $uid ); ?>';
                var stored=localStorage.getItem(storageKey);
                if(stored){
                    target=parseInt(stored);
                    var remaining=target-Date.now();
                    if(remaining<=0){
                        if(egLoop){
                            target=Date.now()+egDuration;
                            localStorage.setItem(storageKey,String(target));
                        }
                    }
                }
                if(!target){
                    target=Date.now()+egDuration;
                    localStorage.setItem(storageKey,String(target));
                }
            }else{
                target=new Date(el.dataset.target).getTime();
            }

            var vals=el.querySelectorAll('.mcd-num');

            function handleExpired(){
                if(expireAction==='hide'){
                    el.style.display='none';
                    return;
                }
                if(expireAction==='redirect'){
                    if(expireRedirect){
                        window.location.href=expireRedirect;
                    }
                    return;
                }
                if(expireAction==='message'){
                    el.innerHTML='<div class="mcd-expire-msg">'+expireMessage+'</div>';
                    return;
                }
                el.innerHTML='<div class="mcd-expire-msg">'+el.dataset.expired+'</div>';
            }

            function tick(){
                var now=Date.now(),diff=target-now;
                if(diff<=0){
                    if(ctype==='evergreen'){
                        if(egLoop){
                            var egH2=parseInt(el.getAttribute('data-olo-evergreen-hours'))||0;
                            var egM2=parseInt(el.getAttribute('data-olo-evergreen-minutes'))||0;
                            var egDur2=(egH2*3600+egM2*60)*1000;
                            target=Date.now()+egDur2;
                            var sKey='olo_eg_'+location.pathname+'_<?php echo esc_js( $uid ); ?>';
                            localStorage.setItem(sKey,String(target));
                            return;
                        }
                    }
                    handleExpired();
                    return;
                }
                var d=Math.floor(diff/86400000),h=Math.floor((diff%86400000)/3600000),m=Math.floor((diff%3600000)/60000),sc=Math.floor((diff%60000)/1000);
                var map={days:d,hours:h,minutes:m,seconds:sc};
                vals.forEach(function(v){var u=v.dataset.unit;if(map[u]!==undefined){v.textContent=String(map[u]).padStart(2,'0');}});
            }
            tick();setInterval(tick,1000);
        })();
        </script>
        <?php
        return ob_get_clean();
    }

    private function render_uikit( $s ) {
        $uid = 'mcd-uk-' . wp_rand( 10000, 99999 );

        // Complete ISO date: "2026-12-31T23:59" → "2026-12-31T23:59:00+00:00"
        $date = $s['target_date'];
        if ( strlen( $date ) === 16 ) {
            $date .= ':00+00:00';
        } elseif ( strlen( $date ) === 19 && ! str_contains( $date, '+' ) && ! str_contains( $date, 'Z' ) ) {
            $date .= '+00:00';
        }

        $num_fs  = absint( $s['number_font_size'] );
        $num_fw  = absint( $s['number_font_weight'] );
        $lbl_fs  = absint( $s['label_font_size'] );
        $lbl_fw  = absint( $s['label_font_weight'] );
        $sep_fs  = absint( $s['separator_font_size'] );
        // Bug pre-3.57.18: usava $s['padding'] che era stato sostituito da $s['tile_padding']
        // (oggetto spacing). Risultato: padding sempre 0 in uikit-mode. Allineiamo a render_custom.
        $pad = Olo_Tile_Utils::spacing_css( $s['tile_padding'] ?? $s['padding'] ?? 32, 32 );
        $bg      = $this->safe_color_css( $s['bg_color'] ?? '' );
        $fg      = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text, #374151)';
        $accent  = $this->safe_color_css( $s['accent_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $item_bg     = $this->safe_color_css( $s['item_bg_color'] ?? '' );
        $item_radius = absint( $s['item_radius'] ?? 0 );
        $item_pad    = absint( $s['item_padding'] ?? 0 );

        $units = [];
        if ( $s['show_days'] )    $units[] = [ 'cls' => 'uk-countdown-days',    'label' => $s['label_days'] ];
        if ( $s['show_hours'] )   $units[] = [ 'cls' => 'uk-countdown-hours',   'label' => $s['label_hours'] ];
        if ( $s['show_minutes'] ) $units[] = [ 'cls' => 'uk-countdown-minutes', 'label' => $s['label_minutes'] ];
        if ( $s['show_seconds'] ) $units[] = [ 'cls' => 'uk-countdown-seconds', 'label' => $s['label_seconds'] ];

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                padding: <?php echo $pad; ?>px;
                <?php if ( $bg ) : ?>background: <?php echo $bg; ?>;<?php endif; ?>
                color: <?php echo $fg; ?>;
            }
            .<?php echo $uid; ?> .uk-countdown > div > div:not(.uk-countdown-separator) {
                <?php if ( $item_bg ) : ?>background: <?php echo $item_bg; ?>;<?php endif; ?>
                <?php if ( $item_radius > 0 ) : ?>border-radius: <?php echo $item_radius; ?>px;<?php endif; ?>
                <?php if ( $item_pad > 0 ) : ?>padding: <?php echo $item_pad; ?>px;<?php endif; ?>
            }
            .<?php echo $uid; ?> .uk-countdown-number {
                font-size: <?php echo $num_fs; ?>px;
                font-weight: <?php echo $num_fw; ?>;
                line-height: 1.15;
                color: <?php echo $accent; ?>;
            }
            .<?php echo $uid; ?> .olo-cd-label {
                font-size: <?php echo $lbl_fs; ?>px;
                font-weight: <?php echo $lbl_fw; ?>;
                opacity: 0.7;
                margin-top: 4px;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                text-align: center;
            }
            .<?php echo $uid; ?> .uk-countdown-separator {
                font-size: <?php echo $sep_fs; ?>px;
                font-weight: 700;
                opacity: 0.45;
                <?php $sep_c = $this->safe_color_css( $s['separator_color'] ?? '' ); if ( $sep_c ) : ?>
                color: <?php echo $sep_c; ?>;
                <?php endif; ?>
            }
        </style>
        <div class="olo-countdown <?php echo esc_attr( $uid ); ?> olo-cd-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" uk-countdown="date: <?php echo esc_attr( $date ); ?>">
            <div class="uk-grid-small uk-child-width-auto uk-flex-center" uk-grid>
                <?php foreach ( $units as $i => $unit ) :
                    if ( $i > 0 && ! empty( $s['separator'] ) ) : ?>
                        <div><div class="uk-countdown-separator uk-countdown-number"><?php echo esc_html( $s['separator'] ); ?></div></div>
                    <?php endif; ?>
                    <div>
                        <div class="uk-countdown-number <?php echo esc_attr( $unit['cls'] ); ?>"></div>
                        <div class="olo-cd-label"><?php echo esc_html( $unit['label'] ); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
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
