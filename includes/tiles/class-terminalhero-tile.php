<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Hero — Terminale (typewriter) : hero centrato su fondo chiaro "tech" con label
 * mono maiuscola (parole tra [parentesi] rese come chip accento), titolo gigante, riga
 * TYPEWRITER con frasi a rotazione (prefisso configurabile, cursore a blocco), sub,
 * form email inline (GET verso una pagina) o 2 CTA, riga small con link freccia,
 * immagini decorative laterali (nascoste sotto i 1000px), crosshair "+" agli angoli e
 * hairline superiore. Estratta dal blueprint konghq.com (modalità umana).
 * Render == Vue (TerminalHeroTile.vue). Runtime: micro-IIFE typewriter che rispetta
 * prefers-reduced-motion (con una sola frase o reduced motion resta statico, zero JS attivo).
 */
class Olobuild_TerminalHero_Tile extends Olobuild_Tile_Base {

    protected $type     = 'terminalhero';
    protected $name     = 'Hero — Terminale (typewriter)';
    protected $icon     = 'dashicons-editor-code';
    protected $category = 'marketing';
    protected $defaults = [
        'show_label'       => true,
        'label'            => 'UN SOLO [ECOSISTEMA] WORDPRESS',
        'heading'          => 'Costruisci. Traduci. Prenota.',
        'type_phrases'     => [
            [ 'text' => 'per chi costruisce siti' ],
            [ 'text' => 'per chi affitta camere' ],
            [ 'text' => 'per chi parla al mondo' ],
        ],
        'type_prefix'      => '— ',
        'type_speed'       => 55,
        'type_pause'       => 1600,
        'subhead'          => 'La suite modulare per WordPress: page builder, prenotazioni, traduzioni, tour virtuali e corsi. Un solo fornitore, un solo standard.',
        'show_form'        => true,
        'form_placeholder' => 'La tua email',
        'form_button'      => 'Richiedi una demo',
        'form_action'      => '#',
        'small_text'       => 'Prova i prodotti su siti demo reali.',
        'small_link_text'  => 'Apri la demo',
        'small_link_url'   => '#',
        'cta1_text'        => '',
        'cta1_url'         => '#',
        'cta2_text'        => '',
        'cta2_url'         => '#',
        'img_left'         => '',
        'img_right'        => '',
        'side_width'       => 520,
        'side_opacity'     => 100,
        'show_crosshairs'  => true,
        'show_topline'     => true,
        'bg_color'         => 'var(--olo-color-light, #f8f9fa)',
        'text_color'       => 'var(--olo-color-dark, #1a1a2e)',
        'sub_color'        => '',
        'accent'           => '',
        'accent_on'        => '',
        'h_size_min'       => 44,
        'h_size_vw'        => 6.5,
        'h_size_max'       => 92,
        'h_line_height'    => 1.02,
        'type_size_min'    => 20,
        'type_size_vw'     => 2.4,
        'type_size_max'    => 34,
        'align'            => 'center',
        'max_width'        => 1200,
        'min_height'       => 76,

        // Spaziatura — override GATED del padding responsive del contenitore (clamp). No-op coi default.
        'pad_custom'       => false,
        'content_padding'  => [ 'top' => 96, 'right' => 0, 'bottom' => 96, 'left' => 0 ],
        // Forma — raggio pill di input/bottoni. No-op coi default (999 = pill).
        'btn_radius'       => [ 'tl' => 999, 'tr' => 999, 'br' => 999, 'bl' => 999 ],

        // KIT standard OLObuild — sfondo completo / ombra / bordo (no-op coi default)
        'bg'                      => [ 'type' => 'none' ],
        'shadow'                  => 'none',
        'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'otph-' . wp_rand( 10000, 99999 );

        $bg     = $this->safe_color_css( $s['bg_color'] ) ?: 'var(--olo-color-light, #f8f9fa)';
        $txt    = $this->safe_color_css( $s['text_color'] ?? '' ) ?: 'var(--olo-color-dark, #1a1a2e)';
        $accent = $this->safe_color_css( $s['accent'] ?? '' ) ?: 'var(--olo-color-primary, #e1474f)';
        $accOn  = $this->safe_color_css( $s['accent_on'] ?? '' ) ?: 'var(--olo-color-on-primary, #ffffff)';
        $sub    = $this->safe_color_css( $s['sub_color'] ?? '' ) ?: "color-mix(in srgb, {$txt} 72%, transparent)";
        $hair   = "color-mix(in srgb, {$txt} 26%, transparent)";

        $hmin = max( 20, intval( $s['h_size_min'] ) );
        $hvw  = max( 1, floatval( $s['h_size_vw'] ) );
        $hmax = max( $hmin, intval( $s['h_size_max'] ) );
        $hlh  = is_numeric( $s['h_line_height'] ) ? floatval( $s['h_line_height'] ) : 1.02;
        $tmin = max( 12, intval( $s['type_size_min'] ) );
        $tvw  = max( 0.5, floatval( $s['type_size_vw'] ) );
        $tmax = max( $tmin, intval( $s['type_size_max'] ) );

        $align  = ( $s['align'] ?? 'center' ) === 'left' ? 'left' : 'center';
        $alignI = $align === 'center' ? 'center' : 'flex-start';
        $mw     = max( 600, intval( $s['max_width'] ) );
        $mh     = max( 0, min( 100, intval( $s['min_height'] ) ) );
        $sidew  = max( 100, intval( $s['side_width'] ) );
        $sideo  = max( 0.1, min( 1, intval( $s['side_opacity'] ) / 100 ) );

        $tspeed = max( 20, intval( $s['type_speed'] ) );
        $tpause = max( 300, intval( $s['type_pause'] ) );

        // ── Spaziatura: override GATED del padding responsive (clamp) del contenitore ──
        $pad_decl = 'clamp(80px,12vh,128px) 0';
        if ( ! empty( $s['pad_custom'] ) && is_array( $s['content_padding'] ?? null ) ) {
            $cp = $s['content_padding'];
            $pt = intval( $cp['top'] ?? 0 );
            $pr = intval( $cp['right'] ?? 0 );
            $pb = intval( $cp['bottom'] ?? 0 );
            $pl = intval( $cp['left'] ?? 0 );
            $pad_decl = "{$pt}px {$pr}px {$pb}px {$pl}px";
        }

        // ── Forma: raggio input/bottoni (pill). build_border_radius_css ritorna '' se tutti 0 ──
        $btn_radius_val = $this->build_border_radius_css( $s['btn_radius'] ?? [] );
        $btn_radius_css = $btn_radius_val !== '' ? $btn_radius_val : '999px';

        $disp = "var(--olo-font-family-heading, 'Inter',-apple-system,sans-serif)";
        $sans = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";
        $mono = 'var(--olo-font-family-mono, ui-monospace, SFMono-Regular, Menlo, monospace)';

        // Label → parti fisse e chip [tra parentesi]
        $label_parts = [];
        if ( ! empty( $s['show_label'] ) ) {
            $raw = (string) ( $s['label'] ?? '' );
            if ( $raw !== '' ) {
                $split = preg_split( '/(\[[^\]]*\])/', $raw, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );
                foreach ( (array) $split as $part ) {
                    $is_chip = strlen( $part ) > 1 && $part[0] === '[' && substr( $part, -1 ) === ']';
                    $label_parts[] = [
                        'chip' => $is_chip,
                        'text' => $is_chip ? substr( $part, 1, -1 ) : $part,
                    ];
                }
            }
        }

        // Frasi typewriter → lista pulita di stringhe
        $phrases = [];
        foreach ( (array) ( $s['type_phrases'] ?? [] ) as $p ) {
            $t = trim( (string) ( is_array( $p ) ? ( $p['text'] ?? '' ) : $p ) );
            if ( $t !== '' ) { $phrases[] = $t; }
        }

        // ── KIT standard: sfondo completo (override del bg SOLO se valorizzato) ──
        $bg_obj  = $s['bg'] ?? null;
        $bg_decl = '';
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olobuild_CSS_Builder' ) ) {
            $bg_decl = ( new Olobuild_CSS_Builder() )->get_bg_inline_css( $bg_obj );
        }
        $shadow_css = $this->build_shadow_decl( $s );
        $kit_extra  = '';
        if ( $bg_decl !== '' )    { $kit_extra .= $bg_decl . ';'; }
        if ( $shadow_css !== '' ) { $kit_extra .= 'box-shadow:' . $shadow_css . ';'; }
        if ( ! empty( $s['show_topline'] ) ) { $kit_extra .= "border-top:1px solid {$hair};"; }

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above (safe_color_css/intval/floatval/whitelist ternaries/fixed font stacks); escaping would corrupt valid CSS quotes. ?>
        <style>
            .<?php echo $uid; ?>{position:relative;overflow:hidden;box-sizing:border-box;min-height:<?php echo (int) $mh; ?>vh;display:flex;flex-direction:column;justify-content:center;background:<?php echo $bg; ?>;color:<?php echo $txt; ?>;font-family:<?php echo $sans; ?>;padding:<?php echo $pad_decl; ?>;<?php echo $kit_extra; ?>}
            .<?php echo $uid; ?> *,.<?php echo $uid; ?> *::before,.<?php echo $uid; ?> *::after{box-sizing:border-box;}
            .<?php echo $uid; ?> .tph-cross{position:absolute;width:11px;height:11px;pointer-events:none;z-index:3;}
            .<?php echo $uid; ?> .tph-cross::before{content:"";position:absolute;left:50%;top:0;bottom:0;width:1px;margin-left:-.5px;background:<?php echo $hair; ?>;}
            .<?php echo $uid; ?> .tph-cross::after{content:"";position:absolute;top:50%;left:0;right:0;height:1px;margin-top:-.5px;background:<?php echo $hair; ?>;}
            .<?php echo $uid; ?> .tph-cross--tl{top:-5.5px;left:22px;}
            .<?php echo $uid; ?> .tph-cross--tr{top:-5.5px;right:22px;}
            .<?php echo $uid; ?> .tph-cross--bl{bottom:-5.5px;left:22px;}
            .<?php echo $uid; ?> .tph-cross--br{bottom:-5.5px;right:22px;}
            .<?php echo $uid; ?> .tph-side{position:absolute;bottom:0;z-index:1;pointer-events:none;max-width:38vw;height:auto;width:<?php echo (int) $sidew; ?>px;opacity:<?php echo $sideo; ?>;}
            .<?php echo $uid; ?> .tph-side--left{left:0;}
            .<?php echo $uid; ?> .tph-side--right{right:0;}
            @media(max-width:999px){.<?php echo $uid; ?> .tph-side{display:none;}}
            .<?php echo $uid; ?> .tph-in{position:relative;z-index:2;width:100%;max-width:<?php echo (int) $mw; ?>px;margin:0 auto;padding:0 28px;display:flex;flex-direction:column;align-items:<?php echo $alignI; ?>;text-align:<?php echo $align; ?>;}
            .<?php echo $uid; ?> .tph-label{display:inline-flex;align-items:center;flex-wrap:wrap;gap:2px;font-family:<?php echo $mono; ?>;font-size:13px;letter-spacing:.08em;text-transform:uppercase;color:<?php echo $txt; ?>;margin-bottom:28px;}
            .<?php echo $uid; ?> .tph-chip{background:<?php echo $accent; ?>;color:<?php echo $accOn; ?>;padding:0 .3em .04em;margin:0 .15em;}
            .<?php echo $uid; ?> .tph-h{font-family:<?php echo $disp; ?>;font-weight:700;font-size:clamp(<?php echo (int) $hmin; ?>px,<?php echo (float) $hvw; ?>vw,<?php echo (int) $hmax; ?>px);line-height:<?php echo (float) $hlh; ?>;letter-spacing:-.02em;color:<?php echo $txt; ?>;margin:0;text-wrap:balance;}
            .<?php echo $uid; ?> .tph-tw{display:flex;align-items:baseline;justify-content:<?php echo $alignI; ?>;font-family:<?php echo $disp; ?>;font-weight:600;font-size:clamp(<?php echo (int) $tmin; ?>px,<?php echo (float) $tvw; ?>vw,<?php echo (int) $tmax; ?>px);color:<?php echo $txt; ?>;margin-top:16px;min-height:1.4em;letter-spacing:-.01em;}
            .<?php echo $uid; ?> .tph-cursor{display:inline-block;width:.55em;height:1.05em;margin-left:3px;transform:translateY(.12em);background:<?php echo $accent; ?>;animation:<?php echo $uid; ?>-blink 1.1s steps(2,start) infinite;}
            @keyframes <?php echo $uid; ?>-blink{to{visibility:hidden;}}
            @media(prefers-reduced-motion:reduce){.<?php echo $uid; ?> .tph-cursor{animation:none;}}
            .<?php echo $uid; ?> .tph-sub{max-width:560px;font-size:18px;line-height:1.6;color:<?php echo $sub; ?>;margin:24px 0 0;text-wrap:pretty;}
            .<?php echo $uid; ?> .tph-form{display:flex;gap:10px;flex-wrap:wrap;margin-top:32px;justify-content:center;}
            .<?php echo $uid; ?> .tph-cta{display:flex;gap:12px;flex-wrap:wrap;margin-top:32px;}
            .<?php echo $uid; ?> .tph-input{font-family:<?php echo $sans; ?>;font-size:15px;color:<?php echo $txt; ?>;background:color-mix(in srgb, currentColor 6%, transparent);border:1px solid <?php echo $hair; ?>;border-radius:<?php echo $btn_radius_css; ?>;padding:0 20px;height:46px;min-width:240px;outline:none;}
            .<?php echo $uid; ?> .tph-input:focus{border-color:<?php echo $accent; ?>;}
            .<?php echo $uid; ?> .tph-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;font-family:<?php echo $sans; ?>;font-weight:600;font-size:15px;text-decoration:none;background:<?php echo $accent; ?>;color:<?php echo $accOn; ?>;border:1px solid <?php echo $accent; ?>;border-radius:<?php echo $btn_radius_css; ?>;padding:0 24px;height:46px;cursor:pointer;transition:transform .15s,filter .2s,background .2s;}
            .<?php echo $uid; ?> .tph-btn--ghost{background:transparent;color:<?php echo $txt; ?>;border:1px solid <?php echo $hair; ?>;}
            .<?php echo $uid; ?> .tph-btn:hover{transform:translateY(-1px);filter:brightness(1.05);}
            .<?php echo $uid; ?> .tph-btn:focus-visible,.<?php echo $uid; ?> .tph-input:focus-visible,.<?php echo $uid; ?> .tph-small a:focus-visible{outline:2px solid <?php echo $accent; ?>;outline-offset:3px;}
            .<?php echo $uid; ?> .tph-small{display:inline-flex;align-items:center;gap:10px;margin-top:16px;font-family:<?php echo $sans; ?>;font-size:13px;color:<?php echo $sub; ?>;}
            .<?php echo $uid; ?> .tph-small a{display:inline-flex;align-items:center;gap:4px;color:<?php echo $txt; ?>;text-decoration:underline;text-underline-offset:3px;}
            .<?php echo $uid; ?> .tph-small svg{width:14px;height:14px;}
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <section class="olo-terminalhero <?php echo esc_attr( $uid ); ?>">
            <?php if ( ! empty( $s['show_crosshairs'] ) ) : ?>
            <span class="tph-cross tph-cross--tl" aria-hidden="true"></span><span class="tph-cross tph-cross--tr" aria-hidden="true"></span><span class="tph-cross tph-cross--bl" aria-hidden="true"></span><span class="tph-cross tph-cross--br" aria-hidden="true"></span>
            <?php endif; ?>
            <?php if ( ! empty( $s['img_left'] ) ) : ?><img class="tph-side tph-side--left" src="<?php echo esc_url( $s['img_left'] ); ?>" alt="" aria-hidden="true" loading="lazy" /><?php endif; ?>
            <?php if ( ! empty( $s['img_right'] ) ) : ?><img class="tph-side tph-side--right" src="<?php echo esc_url( $s['img_right'] ); ?>" alt="" aria-hidden="true" loading="lazy" /><?php endif; ?>
            <div class="tph-in">
                <?php if ( ! empty( $label_parts ) ) : ?>
                <div class="tph-label"><?php
                    foreach ( $label_parts as $part ) {
                        if ( $part['chip'] ) {
                            echo '<span class="tph-chip">' . esc_html( $part['text'] ) . '</span>';
                        } else {
                            echo esc_html( $part['text'] );
                        }
                    }
                ?></div>
                <?php endif; ?>
                <?php if ( ! empty( $s['heading'] ) ) : ?><h1 class="tph-h"><?php echo esc_html( $s['heading'] ); ?></h1><?php endif; ?>
                <?php if ( ! empty( $phrases ) ) : ?>
                <div class="tph-tw"><span class="tph-pre"><?php echo esc_html( $s['type_prefix'] ); ?></span><span class="tph-type" data-phrases="<?php echo esc_attr( wp_json_encode( $phrases ) ); ?>"><?php echo esc_html( $phrases[0] ); ?></span><span class="tph-cursor" aria-hidden="true"></span></div>
                <?php endif; ?>
                <?php if ( ! empty( $s['subhead'] ) ) : ?><p class="tph-sub"><?php echo esc_html( $s['subhead'] ); ?></p><?php endif; ?>
                <?php if ( ! empty( $s['show_form'] ) ) : ?>
                <form class="tph-form" action="<?php echo esc_url( $s['form_action'] ?: '#' ); ?>" method="get">
                    <input class="tph-input" type="email" name="email" placeholder="<?php echo esc_attr( $s['form_placeholder'] ); ?>" aria-label="<?php echo esc_attr( $s['form_placeholder'] ?: 'Email' ); ?>" />
                    <button class="tph-btn" type="submit"><?php echo esc_html( $s['form_button'] ); ?></button>
                </form>
                <?php elseif ( ! empty( $s['cta1_text'] ) || ! empty( $s['cta2_text'] ) ) : ?>
                <div class="tph-cta">
                    <?php if ( ! empty( $s['cta1_text'] ) ) : ?><a class="tph-btn" href="<?php echo esc_url( $s['cta1_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta1_text'] ); ?></a><?php endif; ?>
                    <?php if ( ! empty( $s['cta2_text'] ) ) : ?><a class="tph-btn tph-btn--ghost" href="<?php echo esc_url( $s['cta2_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta2_text'] ); ?></a><?php endif; ?>
                </div>
                <?php endif; ?>
                <?php if ( ! empty( $s['small_text'] ) || ! empty( $s['small_link_text'] ) ) : ?>
                <small class="tph-small">
                    <?php if ( ! empty( $s['small_text'] ) ) : ?><span><?php echo esc_html( $s['small_text'] ); ?></span><?php endif; ?>
                    <?php if ( ! empty( $s['small_link_text'] ) ) : ?><a href="<?php echo esc_url( $s['small_link_url'] ?: '#' ); ?>"><?php echo esc_html( $s['small_link_text'] ); ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a><?php endif; ?>
                </small>
                <?php endif; ?>
            </div>
        </section>
        <?php if ( count( $phrases ) > 1 ) : ?>
        <script>
        (function(){
            var el=document.querySelector('.<?php echo esc_js( $uid ); ?> .tph-type');
            if(!el){return;}
            var raw=el.getAttribute('data-phrases');
            var list=[];
            try{list=JSON.parse(raw);}catch(e){list=[];}
            if(!list.length){return;}
            var reduced=false;
            if(window.matchMedia){reduced=window.matchMedia('(prefers-reduced-motion: reduce)').matches;}
            if(reduced){el.textContent=list[0];return;}
            if(list.length<2){el.textContent=list[0];return;}
            var speed=<?php echo (int) $tspeed; ?>;
            var pause=<?php echo (int) $tpause; ?>;
            var i=0,pos=list[0].length,del=true;
            function tick(){
                var w=list[i];
                if(!del){
                    pos++;el.textContent=w.slice(0,pos);
                    if(pos===w.length){del=true;setTimeout(tick,pause);return;}
                }else{
                    pos--;el.textContent=w.slice(0,pos);
                    if(pos===0){del=false;i=(i+1)%list.length;}
                }
                var d=speed;
                if(del){d=Math.max(18,Math.round(speed/2));}
                setTimeout(tick,d);
            }
            setTimeout(tick,<?php echo (int) $tpause; ?>);
        })();
        </script>
        <?php endif; ?>
        <?php
        // ── KIT standard: sistema bordi (base + hover + effetti) sul contenitore ──
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- border CSS generated by Olobuild_Tile_Base::build_border_*() helpers (intval sizes, fixed templates); $uid is an internal generated class name.
            echo '<style>';
            if ( $border_css ) {
                echo ".{$uid}{{$border_css}}";
            }
            echo $border_hover_css . $border_effect_css . '</style>';
            // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
        }
        return ob_get_clean();
    }

    /**
     * Restituisce la dichiarazione box-shadow (valore, senza "box-shadow:")
     * dal setting shadow (preset sm/md/lg/xl o custom). '' se none.
     */
    private function build_shadow_decl( $s ) {
        $preset = $s['shadow'] ?? 'none';
        if ( $preset === 'none' || $preset === '' ) {
            return '';
        }
        if ( $preset === 'custom' ) {
            $h      = intval( $s['shadow_h'] ?? 0 );
            $v      = intval( $s['shadow_v'] ?? 4 );
            $blur   = max( 0, intval( $s['shadow_blur'] ?? 10 ) );
            $spread = intval( $s['shadow_spread'] ?? 0 );
            $color  = $this->safe_color_css( $s['shadow_color'] ?? '' ) ?: 'rgba(0,0,0,0.15)';
            $inset  = ! empty( $s['shadow_inset'] ) ? 'inset ' : '';
            return "{$inset}{$h}px {$v}px {$blur}px {$spread}px {$color}";
        }
        $map = [
            'sm' => '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
            'md' => '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
            'lg' => '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
            'xl' => '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
        ];
        return $map[ $preset ] ?? '';
    }
}
