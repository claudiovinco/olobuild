<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Manifesto — Scrub testo : dichiarazione editoriale in display (Big Shoulders)
 * con parole-accento via <em> (colore primario, niente corsivo) e paragrafo lead sans.
 * Meccanica firma: allo scroll le parole si "accendono" progressivamente — partono
 * attenuate (dim_opacity) e passano a piena opacità in base al progresso della sezione
 * nel viewport (p = clamp((vh*0.9 - rect.top) / (vh*0.6), 0, 1)). Runtime JS minimo,
 * scoped al proprio $uid (nessun `&&`/`||` inline), rispetta prefers-reduced-motion.
 * Render == Vue (ScrubTextTile.vue). Estratta dal blueprint "Clod — Evoluzione v2".
 */
class Olobuild_ScrubText_Tile extends Olobuild_Tile_Base {

    protected $type     = 'scrubtext';
    protected $name     = 'Manifesto — Scrub testo';
    protected $icon     = 'dashicons-editor-textcolor';
    protected $category = 'text';
    protected $defaults = [
        'text'              => 'Idee che si <em>vedono.</em><br/>Progetti che <em>funzionano.</em>',
        'show_lead'         => true,
        'lead'              => 'La mia consulenza parte da un\'analisi della situazione reale dell\'azienda — sfide e opportunità — per identificare le soluzioni più adatte. Poi le rendo visibili: strategia, web e media originali, in un unico filo conduttore.',
        'scroll_reveal'     => true,
        'dim_opacity'       => 13,
        'accent'            => '',
        'text_color'        => '',
        'lead_color'        => '',
        'size_min'          => 26,
        'size_max'          => 56,
        'max_width_ch'      => 20,
        'lead_size'         => 16.5,
        'lead_max_width_ch' => 52,

        // KIT standard OLObuild — additivi, no-op coi default (sfondo none, ombra none, bordo 0)
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
        $uid = 'ostx-' . wp_rand( 10000, 99999 );

        $acc  = $this->safe_color_css( $s['accent'] ?? '' ) ?: 'var(--olo-color-primary, #C6F24E)';
        $txt  = $this->safe_color_css( $s['text_color'] ?? '' ) ?: 'var(--olo-color-text, #ECEAE3)';
        $lcol = $this->safe_color_css( $s['lead_color'] ?? '' ) ?: 'var(--olo-color-text-soft, #a0a298)';

        $smin = floatval( $s['size_min'] );
        if ( $smin <= 0 ) { $smin = 26; }
        $smax = floatval( $s['size_max'] );
        if ( $smax <= 0 ) { $smax = 56; }
        $mch = floatval( $s['max_width_ch'] );
        if ( $mch <= 0 ) { $mch = 20; }
        $lsz = floatval( $s['lead_size'] );
        if ( $lsz <= 0 ) { $lsz = 16.5; }
        $lch = floatval( $s['lead_max_width_ch'] );
        if ( $lch <= 0 ) { $lch = 52; }
        $dim_raw = floatval( $s['dim_opacity'] );
        $dim     = max( 0, min( 100, is_finite( $dim_raw ) ? $dim_raw : 13 ) ) / 100;

        // Formattazione deterministica (PHP 7.4: l'echo di float rispetta LC_NUMERIC).
        $smin = $this->fnum( $smin );
        $smax = $this->fnum( $smax );
        $mch  = $this->fnum( $mch );
        $lsz  = $this->fnum( $lsz );
        $lch  = $this->fnum( $lch );
        $dim  = $this->fnum( $dim );

        $scrub     = ! empty( $s['scroll_reveal'] );
        $show_lead = ! empty( $s['show_lead'] );
        $lead      = trim( (string) $s['lead'] );

        $disp = "var(--olo-font-family-heading, 'Big Shoulders Display', sans-serif)";
        $sans = "var(--olo-font-family, 'Hanken Grotesk', sans-serif)";

        // ── KIT standard OLObuild: sfondo completo + ombra + bordo sul contenitore ──
        $bg_obj  = $s['bg'] ?? null;
        $bg_decl = '';
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olobuild_CSS_Builder' ) ) {
            $bg_decl = ( new Olobuild_CSS_Builder() )->get_bg_inline_css( $bg_obj );
        }
        $shadow_css        = $this->build_shadow_decl( $s );
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );

        $box_decl = '';
        if ( $bg_decl )    { $box_decl .= rtrim( trim( $bg_decl ), ';' ) . ';'; }
        if ( $border_css ) { $box_decl .= $border_css; }
        if ( $shadow_css ) { $box_decl .= 'box-shadow:' . $shadow_css . ';'; }
        if ( $box_decl || $border_effect_css ) { $box_decl .= 'position:relative;'; }

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: every colour via the safe_color_css() whitelist, sizes via floatval() with positive fallbacks, opacity clamped 0-1, fixed font-stack literals, background/shadow/border via the Olobuild_CSS_Builder/Olobuild_Tile_Base shared helpers (sanitized internally); $uid is internally generated. ?>
        <style>
            .<?php echo $uid; ?>{font-family:<?php echo $sans; ?>;<?php echo $box_decl; ?>}
            .<?php echo $uid; ?> .ost-p{font-family:<?php echo $disp; ?>;font-weight:600;font-size:clamp(<?php echo $smin; ?>px,4.2vw,<?php echo $smax; ?>px);line-height:1.04;letter-spacing:-.01em;text-transform:none;max-width:<?php echo $mch; ?>ch;margin:0;color:<?php echo $txt; ?>;}
            .<?php echo $uid; ?> .ost-p em{font-style:normal;color:<?php echo $acc; ?>;}
            .<?php echo $uid; ?> .ost-lead{font-family:<?php echo $sans; ?>;font-weight:400;font-size:<?php echo $lsz; ?>px;line-height:1.65;color:<?php echo $lcol; ?>;max-width:<?php echo $lch; ?>ch;margin:28px 0 0;}
            .<?php echo $uid; ?> .st-w{opacity:<?php echo $dim; ?>;transition:opacity .3s ease;}
            .<?php echo $uid; ?> .st-w.on{opacity:1;}
            @media(prefers-reduced-motion:reduce){.<?php echo $uid; ?> .st-w{opacity:1;}}
            <?php echo $border_hover_css; ?><?php echo $border_effect_css; ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-scrubtext <?php echo esc_attr( $uid ); ?>">
            <p class="ost-p" data-olo-wave<?php if ( $scrub ) : ?> data-st-scrub<?php endif; ?>><?php echo wp_kses_post( $s['text'] ); ?></p>
            <?php if ( $show_lead && $lead !== '' ) : ?>
                <p class="ost-lead"<?php if ( $scrub ) : ?> data-st-scrub<?php endif; ?>><?php echo wp_kses_post( $lead ); ?></p>
            <?php endif; ?>
        </div>
        <?php if ( $scrub ) : ?>
        <script>
        (function(){
            var root=document.currentScript.previousElementSibling;
            if(!root){return;}
            var reduce=window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if(reduce){return;}
            var targets=[].slice.call(root.querySelectorAll('[data-st-scrub]'));
            if(!targets.length){return;}
            function split(rootEl){
                var ws=[];
                (function walk(n){
                    [].slice.call(n.childNodes).forEach(function(ch){
                        if(ch.nodeType===3){
                            if(!ch.textContent.trim()){return;}
                            var parts=ch.textContent.split(/(\s+)/), fr=document.createDocumentFragment();
                            parts.forEach(function(p){
                                if(!p){return;}
                                if(/^\s+$/.test(p)){fr.appendChild(document.createTextNode(p));return;}
                                var sp=document.createElement('span');
                                sp.className='st-w';
                                sp.textContent=p;
                                fr.appendChild(sp);
                                ws.push(sp);
                            });
                            n.replaceChild(fr,ch);
                        }else if(ch.nodeType===1){
                            if(ch.tagName!=='BR'){walk(ch);}
                        }
                    });
                })(rootEl);
                return ws;
            }
            var sets=targets.map(function(t){ return {el:t,ws:split(t),lastN:-1}; });
            var tk=false;
            function upd(){
                tk=false;
                var vh=window.innerHeight;
                sets.forEach(function(st){
                    var r=st.el.getBoundingClientRect();
                    var p=Math.max(0,Math.min(1,(vh*0.9-r.top)/(vh*0.6)));
                    var n=Math.round(p*st.ws.length);
                    /* Skip se invariato: senza, decine di classList.toggle a ogni frame
                       (repaint inutili che fanno scattare lo scroll su mobile). */
                    if(n===st.lastN){return;}
                    st.lastN=n;
                    st.ws.forEach(function(w,i){ w.classList.toggle('on',i<n); });
                });
            }
            window.addEventListener('scroll',function(){ if(!tk){ tk=true; window.requestAnimationFrame(upd); } },{passive:true});
            window.addEventListener('resize',upd);
            upd();
        })();
        </script>
        <?php endif; ?>
        <?php
        return ob_get_clean();
    }

    /**
     * Formatta un float in stringa CSS deterministica (punto decimale, niente
     * zeri di coda): 26 → "26", 16.5 → "16.5", 0.13 → "0.13". Evita l'effetto
     * LC_NUMERIC dell'echo di float su PHP 7.4.
     */
    private function fnum( $v ) {
        $out = number_format( (float) $v, 4, '.', '' );
        $out = rtrim( rtrim( $out, '0' ), '.' );
        return $out === '' ? '0' : $out;
    }

    /**
     * Restituisce la dichiarazione box-shadow (valore, senza "box-shadow:")
     * dal setting shadow (preset sm/md/lg/xl o custom). '' se none.
     * Copiato dal pattern standard OLObuild (cfr. Olobuild_CategoryRail_Tile).
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
