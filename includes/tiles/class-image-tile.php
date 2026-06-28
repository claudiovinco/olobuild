<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Image_Tile extends Olo_Tile_Base {

    protected $type     = 'image';
    protected $name     = 'Immagine';
    protected $icon     = 'dashicons-format-image';
    protected $category = 'essential';
    protected $defaults = [
        'image_url'   => '',
        'media_bg'    => [ 'type' => 'none' ],
        'media_label' => '',
        'hover_image' => '',
        'hover_video' => '',
        'alt_text'    => '',
        'caption'     => '',
        'link_url'    => '',
        'link_target' => '_self',
        // ── Dimensioni / fit ──
        'image_width'         => '100%',
        'height'              => '300px',
        'max_width'           => '',
        'aspect_ratio'        => 'auto',
        'aspect_ratio_custom' => '16/9',
        'object_fit'          => 'cover',
        'object_position'     => 'center center',
        'image_alignment'     => 'center',
        'align_in_column'     => '',
        'filter_blur'       => '0',
        'filter_brightness' => '100',
        'filter_contrast'   => '100',
        'filter_saturate'   => '100',
        'filter_grayscale'  => '0',
        'filter_sepia'      => '0',
        'hover_filter_blur'       => '',
        'hover_filter_brightness' => '',
        'hover_filter_contrast'   => '',
        'hover_filter_saturate'   => '',
        'hover_filter_grayscale'  => '',
        'hover_filter_sepia'      => '',
        'hover_animation'  => 'none',
        'lightbox'         => false,
        // ── SpotlightFX (alone di luce sul cursore — tema 43) ──
        'spotlight_enabled'   => false,
        'spotlight_mode'      => 'mask',
        'spotlight_radius'    => 260,
        'spotlight_intensity' => 80,
        'spotlight_falloff'   => 55,
        'spotlight_tint'      => '',
        // ── WaterDisplacement (filtro acqua SVG feTurbulence+feDisplacementMap — tema 68) ──
        'water_enabled'        => false,
        'water_target'         => 'image',
        'water_base_freq_x'    => 0.012,
        'water_base_freq_y'    => 0.02,
        'water_octaves'        => 2,
        'water_displace_scale' => 12,
        'water_ripple_scale'   => 34,
        'water_anim_speed'     => 22,
        'border_radius'    => '0',
        'hover_border_radius' => '',
        'hover_radius_duration' => '400',
        'border'              => [],
        'border_hover'        => [],
        'border_hover_duration' => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'image_url',   'type' => 'image',  'label' => 'Image' ],
            [ 'key' => 'alt_text',    'type' => 'text',   'label' => 'Alt Text' ],
            [ 'key' => 'caption',     'type' => 'text',   'label' => 'Caption' ],
            [ 'key' => 'link_url',    'type' => 'text',   'label' => 'Link URL' ],
            [ 'key' => 'link_target', 'type' => 'select', 'label' => 'Link Target', 'options' => [ '_self' => 'Same Window', '_blank' => 'New Tab' ] ],
            [ 'key' => 'object_fit',  'type' => 'select', 'label' => 'Fit Mode', 'options' => [ 'cover' => 'Cover', 'contain' => 'Contain', 'fill' => 'Fill' ] ],
            [ 'key' => 'height',      'type' => 'text',   'label' => 'Height' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $uid = 'olo-img-' . wp_rand( 10000, 99999 );

        // Build CSS filter string
        $filters = [];
        $blur = absint( $s['filter_blur'] ?? 0 );
        $brightness = absint( $s['filter_brightness'] ?? 100 );
        $contrast = absint( $s['filter_contrast'] ?? 100 );
        $saturate = absint( $s['filter_saturate'] ?? 100 );
        $grayscale = absint( $s['filter_grayscale'] ?? 0 );
        $sepia = absint( $s['filter_sepia'] ?? 0 );
        if ( $blur > 0 ) $filters[] = "blur({$blur}px)";
        if ( $brightness !== 100 ) $filters[] = "brightness({$brightness}%)";
        if ( $contrast !== 100 ) $filters[] = "contrast({$contrast}%)";
        if ( $saturate !== 100 ) $filters[] = "saturate({$saturate}%)";
        if ( $grayscale > 0 ) $filters[] = "grayscale({$grayscale}%)";
        if ( $sepia > 0 ) $filters[] = "sepia({$sepia}%)";
        $filter_css = $filters ? implode( ' ', $filters ) : '';

        // Hover filters
        $hover_filters = [];
        $hblur = $s['hover_filter_blur'] ?? '';
        $hbright = $s['hover_filter_brightness'] ?? '';
        $hcontrast = $s['hover_filter_contrast'] ?? '';
        $hsat = $s['hover_filter_saturate'] ?? '';
        $hgray = $s['hover_filter_grayscale'] ?? '';
        $hsepia = $s['hover_filter_sepia'] ?? '';
        if ( $hblur !== '' ) $hover_filters[] = 'blur(' . absint($hblur) . 'px)';
        if ( $hbright !== '' ) $hover_filters[] = 'brightness(' . absint($hbright) . '%)';
        if ( $hcontrast !== '' ) $hover_filters[] = 'contrast(' . absint($hcontrast) . '%)';
        if ( $hsat !== '' ) $hover_filters[] = 'saturate(' . absint($hsat) . '%)';
        if ( $hgray !== '' ) $hover_filters[] = 'grayscale(' . absint($hgray) . '%)';
        if ( $hsepia !== '' ) $hover_filters[] = 'sepia(' . absint($hsepia) . '%)';
        $hover_filter_css = $hover_filters ? implode( ' ', $hover_filters ) : '';

        // Hover animation
        $anim = $s['hover_animation'] ?? 'none';
        $hover_transform = '';
        switch ( $anim ) {
            case 'zoom-in':    $hover_transform = 'scale(1.08)'; break;
            case 'zoom-out':   $hover_transform = 'scale(1)'; break;
            case 'slide-up':   $hover_transform = 'translateY(-5px)'; break;
            case 'rotate-cw':  $hover_transform = 'rotate(2deg) scale(1.02)'; break;
            case 'rotate-ccw': $hover_transform = 'rotate(-2deg) scale(1.02)'; break;
        }
        $init_transform = $anim === 'zoom-out' ? 'transform:scale(1.05);' : '';

        // Border radius (base + optional hover — image_tile uses legacy `hover_border_radius` key,
        // not the canonical `*_hover` convention used elsewhere)
        $br_css        = $this->build_border_radius_css( $s['border_radius'] ?? '0' );
        $hover_br_raw  = $s['hover_border_radius'] ?? '';
        // hover is "set" whenever the user touched the field — i.e. it's an object
        // (4 sides) or a non-empty scalar. All-zero values must still apply (override base).
        $has_hover_br  = is_array( $hover_br_raw ) || ( $hover_br_raw !== '' && $hover_br_raw !== null );
        $hover_br_css  = '';
        if ( $has_hover_br ) {
            if ( is_array( $hover_br_raw ) ) {
                $h_tl = intval( $hover_br_raw['tl'] ?? 0 );
                $h_tr = intval( $hover_br_raw['tr'] ?? 0 );
                $h_br = intval( $hover_br_raw['br'] ?? 0 );
                $h_bl = intval( $hover_br_raw['bl'] ?? 0 );
                $hover_br_css = "{$h_tl}px {$h_tr}px {$h_br}px {$h_bl}px";
            } else {
                $h_n = max( 0, intval( $hover_br_raw ) );
                $hover_br_css = "{$h_n}px";
            }
        }
        $br_duration   = max( 50, intval( $s['hover_radius_duration'] ?? 400 ) );

        // Border system
        $border_d       = $this->parse_border( $s['border'] ?? [] );
        $border_css     = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css = $this->build_border_hover_css(
            ".{$uid}.olo-image",
            $s['border'] ?? [],
            $s['border_hover'] ?? [],
            intval( $s['border_hover_duration'] ?? 300 )
        );
        $border_effect_css = $this->build_border_effect_css(
            ".{$uid}.olo-image",
            $s['border'] ?? [],
            $s
        );

        // ── SpotlightFX (alone di luce sul cursore — rif. 43-tema-gioielleria.html #spot) ──
        // Stato base SSR: gradiente centrato già visibile. Il runtime aggiorna --mx/--my.
        // Scoped sull'UID dell'istanza (classe .olo-spot-<uid>); N istanze non si calpestano.
        $spot_on = ! empty( $s['spotlight_enabled'] );
        $spot_css   = '';
        $spot_html  = '';
        $spot_js    = '';
        if ( $spot_on ) {
            $spot_mode   = ( $s['spotlight_mode'] ?? 'mask' ) === 'lighten' ? 'lighten' : 'mask';
            $spot_radius = max( 40, min( 1200, intval( $s['spotlight_radius'] ?? 260 ) ) );
            // intensità 0..100 → alpha 0..1
            $spot_int    = max( 0, min( 100, intval( $s['spotlight_intensity'] ?? 80 ) ) );
            $spot_a      = round( $spot_int / 100, 3 );
            // falloff 0..100 → posizione (%) in cui parte la dissolvenza dell'alone
            $spot_fall   = max( 0, min( 100, intval( $s['spotlight_falloff'] ?? 55 ) ) );
            // soft = punto da cui inizia la sfumatura (più alto = bordo più netto).
            $spot_soft   = max( 0, min( 90, 100 - $spot_fall ) );
            $spot_cls    = 'olo-spot-' . $uid;

            if ( $spot_mode === 'lighten' ) {
                // Luce additiva: tinta al centro che svanisce verso i bordi.
                $tint  = $this->safe_color_css( $s['spotlight_tint'] ?? '' );
                $blend = 'screen';
                $a_mid = round( $spot_a * 0.55, 3 );
                if ( $tint !== '' && strpos( $tint, 'var(' ) === 0 ) {
                    // Token cliente: mantieni il token, modula l'alpha via color-mix (token-first).
                    $p_core = round( $spot_a * 100 );
                    $p_mid  = round( $a_mid  * 100 );
                    $c_core = 'color-mix(in srgb,' . $tint . ' ' . $p_core . '%,transparent)';
                    $c_mid  = 'color-mix(in srgb,' . $tint . ' ' . $p_mid  . '%,transparent)';
                    $c_edge = 'color-mix(in srgb,' . $tint . ' 0%,transparent)';
                } else {
                    // Hex/rgb o default bianco caldo → tripletta rgb con alpha.
                    $tint_rgb = $this->color_to_rgb( $tint !== '' ? $tint : '#FFF4DE' );
                    $c_core   = 'rgba(' . $tint_rgb . ',' . $spot_a . ')';
                    $c_mid    = 'rgba(' . $tint_rgb . ',' . $a_mid . ')';
                    $c_edge   = 'rgba(' . $tint_rgb . ',0)';
                }
                $grad = 'radial-gradient(circle ' . $spot_radius . 'px at var(--mx,50%) var(--my,42%),'
                      . $c_core . ' 0%,'
                      . $c_mid . ' ' . $spot_soft . '%,'
                      . $c_edge . ' 100%)';
            } else {
                // Maschera: trasparente sotto il cursore, scuro verso i bordi (come #spot del demo).
                $blend    = 'normal';
                $grad     = 'radial-gradient(circle ' . $spot_radius . 'px at var(--mx,50%) var(--my,42%),'
                          . 'rgba(8,7,5,0) 0%,'
                          . 'rgba(8,7,5,' . round( $spot_a * 0.6, 3 ) . ') ' . $spot_soft . '%,'
                          . 'rgba(8,7,5,' . $spot_a . ') 100%)';
            }

            // Stile scoped: figure relative, overlay assoluto sopra l'immagine, didascalia
            // sopra l'overlay; riduzione movimento toglie la transizione; touch/no-hover spegne.
            $spot_css = '<style>'
                . '.' . $uid . '.olo-image{position:relative;}'
                . '.' . $uid . ' .' . $spot_cls . '{position:absolute;inset:0;z-index:1;pointer-events:none;'
                . 'mix-blend-mode:' . $blend . ';'
                . 'background:' . $grad . ';'
                . 'transition:background .12s ease;'
                . 'will-change:background;}'
                . '.' . $uid . ' .olo-img-caption{position:relative;z-index:2;}'
                . '@media (prefers-reduced-motion: reduce){.' . $uid . ' .' . $spot_cls . '{transition:none;}}'
                . '@media (hover:none),(pointer:coarse){.' . $uid . ' .' . $spot_cls . '{display:none;}}'
                . '</style>';

            $spot_html = '<div class="' . esc_attr( $spot_cls ) . '" aria-hidden="true"></div>';

            // Runtime inline, idempotente (guard su dataset), multi-istanza. Niente "&&"/"||"
            // nel JS (WordPress li converte in entità HTML): si usano if annidati + helper mq().
            // Touch/no-hover e reduced-motion → nessun listener (alone resta al default centrato).
            $spot_js = '<script>'
                . '(function(){'
                . 'var fig=document.querySelector(".' . esc_js( $uid ) . '.olo-image");'
                . 'if(!fig){return;}'
                . 'var spot=fig.querySelector(".' . esc_js( $spot_cls ) . '");'
                . 'if(!spot){return;}'
                . 'if(spot.dataset.oloSpot){return;}spot.dataset.oloSpot="1";'
                . 'function mq(q){if(!window.matchMedia){return false;}return window.matchMedia(q).matches;}'
                . 'if(mq("(hover:none)")){return;}'
                . 'if(mq("(pointer:coarse)")){return;}'
                . 'if(mq("(prefers-reduced-motion: reduce)")){return;}'
                . 'function move(e){'
                . 'var r=fig.getBoundingClientRect();if(!r.width){return;}if(!r.height){return;}'
                . 'var x=((e.clientX-r.left)/r.width*100);'
                . 'var y=((e.clientY-r.top)/r.height*100);'
                . 'if(x<0){x=0;}if(x>100){x=100;}if(y<0){y=0;}if(y>100){y=100;}'
                . 'spot.style.setProperty("--mx",x+"%");'
                . 'spot.style.setProperty("--my",y+"%");'
                . '}'
                . 'function reset(){spot.style.setProperty("--mx","50%");spot.style.setProperty("--my","42%");}'
                . 'fig.addEventListener("pointermove",move);'
                . 'fig.addEventListener("pointerleave",reset);'
                . '})();'
                . '</script>';
        }

        // ── WaterDisplacement (filtro acqua — rif. 68-tema-terme-spa.html) ──
        // SSR: il filtro SVG con <animate> sul baseFrequency rende il moto base GIA' visibile,
        // anche senza JS. Il runtime fa solo l'easing dello "scale" del feDisplacementMap verso
        // rippleScale al passaggio del cursore, e il ritorno a displaceScale. Tutto scoped sull'UID
        // (id filtro/turbolenza/displacement) così N istanze non si calpestano. reduced-motion →
        // niente <animate>, scale statico e leggero, nessun listener.
        $water_on   = ! empty( $s['water_enabled'] );
        $water_svg  = '';   // <svg><defs><filter>… (UID-scoped)
        $water_css  = '';   // wrapper .olo-water-<uid>{filter:url(#water-<uid>)}
        $water_js   = '';   // easing scale su pointermove (image) / applica filtro alla section (section-bg)
        $water_cls  = '';   // classe wrapper per target=image
        $water_tgt  = 'image';
        if ( $water_on ) {
            $water_tgt = ( $s['water_target'] ?? 'image' ) === 'section-bg' ? 'section-bg' : 'image';
            // Frequenze: clamp in un range sicuro (turbolenza troppo alta = "rumore", non acqua).
            $wfx   = max( 0.001, min( 0.08, floatval( $s['water_base_freq_x'] ?? 0.012 ) ) );
            $wfy   = max( 0.001, min( 0.08, floatval( $s['water_base_freq_y'] ?? 0.02 ) ) );
            $woct  = max( 1, min( 4, intval( $s['water_octaves'] ?? 2 ) ) );
            $wrest = max( 0, min( 80, intval( $s['water_displace_scale'] ?? 12 ) ) );
            $wrip  = max( 0, min( 160, intval( $s['water_ripple_scale'] ?? 34 ) ) );
            if ( $wrip < $wrest ) { $wrip = $wrest; }   // ripple non può essere meno del riposo
            $wspeed = max( 4, min( 90, intval( $s['water_anim_speed'] ?? 22 ) ) );
            // Wobble del baseFrequency attorno al valore impostato (moto "respirante" dell'acqua).
            $fx_lo = round( $wfx * 0.82, 4 ); $fx_hi = round( $wfx * 1.32, 4 );
            $fy_lo = round( $wfy * 0.82, 4 ); $fy_hi = round( $wfy * 1.32, 4 );
            $bf_lo = $fx_lo . ' ' . $fy_lo;
            $bf_md = $fx_hi . ' ' . $fy_hi;
            $bf_vals = $bf_lo . ';' . $bf_md . ';' . $bf_lo;

            $filter_id = 'water-' . $uid;
            $turb_id   = 'wturb-' . $uid;
            $disp_id   = 'wdisp-' . $uid;
            $seed      = wp_rand( 1, 99 );

            // SVG nascosto (0x0): definisce SOLO il filtro. id scoped per istanza.
            // x/y/width/height al -20%/140% per evitare il clip dei bordi sotto displacement.
            $water_svg  = '<svg class="olo-water-defs" width="0" height="0" aria-hidden="true" focusable="false" style="position:absolute;width:0;height:0;overflow:hidden;">';
            $water_svg .= '<defs>';
            $water_svg .= '<filter id="' . esc_attr( $filter_id ) . '" x="-20%" y="-20%" width="140%" height="140%" color-interpolation-filters="sRGB">';
            $water_svg .= '<feTurbulence id="' . esc_attr( $turb_id ) . '" type="fractalNoise" baseFrequency="' . esc_attr( $bf_lo ) . '" numOctaves="' . esc_attr( $woct ) . '" seed="' . esc_attr( $seed ) . '" result="noise">';
            // <animate> = moto base SSR (no-JS). Rimosso nel ramo reduced-motion via <style>.
            $water_svg .= '<animate class="olo-water-anim" attributeName="baseFrequency" dur="' . esc_attr( $wspeed ) . 's" values="' . esc_attr( $bf_vals ) . '" repeatCount="indefinite"/>';
            $water_svg .= '</feTurbulence>';
            $water_svg .= '<feDisplacementMap id="' . esc_attr( $disp_id ) . '" in="SourceGraphic" in2="noise" scale="' . esc_attr( $wrest ) . '" xChannelSelector="R" yChannelSelector="G"/>';
            $water_svg .= '</filter>';
            $water_svg .= '</defs></svg>';

            $water_cls = 'olo-water-' . $uid;
            // Il wrapper porta il filtro. Per target=image avvolge l'immagine; per section-bg
            // la classe viene applicata dal runtime alla sezione contenitore.
            $water_css  = '<style>';
            $water_css .= '.' . $water_cls . '{filter:url(#' . $filter_id . ');-webkit-filter:url(#' . $filter_id . ');will-change:filter;}';
            // reduced-motion: ferma l'<animate> SVG (l'acqua resta nitida/statica) e niente easing JS.
            $water_css .= '@media (prefers-reduced-motion: reduce){.olo-water-defs .olo-water-anim{display:none;}}';
            $water_css .= '</style>';

            // Runtime inline, idempotente, multi-istanza. Niente "&&"/"||" (WordPress li trasforma
            // in entità): if annidati + helper mq(). reduced-motion / touch → nessun easing.
            $sel_fig  = '.' . $uid . '.olo-image';
            $water_js  = '<script>';
            $water_js .= '(function(){';
            $water_js .= 'var fig=document.querySelector("' . esc_js( $sel_fig ) . '");';
            $water_js .= 'if(!fig){return;}';
            $water_js .= 'var disp=document.getElementById("' . esc_js( $disp_id ) . '");';
            $water_js .= 'if(!disp){return;}';
            $water_js .= 'if(fig.dataset.oloWater){return;}fig.dataset.oloWater="1";';
            // Target dell'hover: la sezione contenitore (section-bg) o il figure stesso (image).
            $water_js .= 'var tgt="' . esc_js( $water_tgt ) . '";';
            $water_js .= 'var host=fig;';
            $water_js .= 'if(tgt==="section-bg"){';
            $water_js .= 'var sec=fig.closest(".olo-section,section,.uk-section");';
            $water_js .= 'if(sec){sec.classList.add("' . esc_js( $water_cls ) . '");host=sec;}';
            $water_js .= '}';
            $water_js .= 'var REST=' . json_encode( $wrest ) . ',RIP=' . json_encode( $wrip ) . ';';
            $water_js .= 'function mq(q){if(!window.matchMedia){return false;}return window.matchMedia(q).matches;}';
            // Senza hover / con riduzione movimento: scale fisso al riposo, nessun rAF.
            $water_js .= 'var noFx=false;';
            $water_js .= 'if(mq("(hover:none)")){noFx=true;}';
            $water_js .= 'if(mq("(pointer:coarse)")){noFx=true;}';
            $water_js .= 'if(mq("(prefers-reduced-motion: reduce)")){noFx=true;}';
            $water_js .= 'if(noFx){disp.setAttribute("scale",REST);return;}';
            $water_js .= 'var cur=REST,target=REST,running=false,rafId=null;';
            $water_js .= 'function loop(){';
            $water_js .= 'if(!running){return;}';
            $water_js .= 'cur+=(target-cur)*0.08;';        // easing verso il target
            $water_js .= 'target+=(REST-target)*0.04;';    // il target si rilassa da solo verso il riposo (ripple)
            $water_js .= 'disp.setAttribute("scale",cur.toFixed(1));';
            $water_js .= 'rafId=requestAnimationFrame(loop);';
            $water_js .= '}';
            $water_js .= 'function start(){if(!running){running=true;rafId=requestAnimationFrame(loop);}}';
            $water_js .= 'function stop(){running=false;if(rafId){cancelAnimationFrame(rafId);rafId=null;}}';
            $water_js .= 'host.addEventListener("pointermove",function(){target=RIP;start();});';
            $water_js .= 'host.addEventListener("pointerleave",function(){target=REST;});';
            // Performance: spegne il rAF quando l'istanza è fuori viewport.
            $water_js .= 'if("IntersectionObserver" in window){';
            $water_js .= 'var io=new IntersectionObserver(function(es){for(var i=0;i<es.length;i++){if(es[i].isIntersecting){if(Math.abs(cur-REST)>0.2){start();}}else{stop();}}},{threshold:0});';
            $water_js .= 'io.observe(fig);';
            $water_js .= '}';
            $water_js .= '})();';
            $water_js .= '</script>';
        }

        ob_start();

        if ( $filter_css || $hover_filter_css || $hover_transform || $anim === 'blur-in' || $has_hover_br ) {
            // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS built exclusively from values sanitized above (absint filters, fixed transform map, intval radii/duration); $uid is an internal generated class name.
            echo '<style>';
            echo ".{$uid} img { transition: filter 0.4s ease, transform 0.4s ease;";
            if ( $filter_css ) echo "filter:{$filter_css};";
            if ( $init_transform ) echo $init_transform;
            echo '}';
            if ( $hover_filter_css || $hover_transform ) {
                echo ".{$uid}:hover img {";
                if ( $hover_filter_css ) echo "filter:{$hover_filter_css};";
                if ( $hover_transform ) echo "transform:{$hover_transform};";
                echo '}';
            }
            if ( $anim === 'blur-in' ) {
                echo ".{$uid} img { filter:" . ($filter_css ? $filter_css . ' ' : '') . "blur(3px); }";
                echo ".{$uid}:hover img { filter:" . ($filter_css ?: '') . "blur(0); }";
            }
            if ( $has_hover_br ) {
                echo ".{$uid}.olo-image{transition:border-radius {$br_duration}ms cubic-bezier(.4,0,.2,1);}";
                echo ".{$uid}.olo-image:hover{border-radius:{$hover_br_css}!important;}";
            }
            echo '</style>';
            // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        // CSS bordo, hover bordo, effetti bordo
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- border CSS generated by Olo_Tile_Base::build_border_*() helpers (intval sizes, fixed templates); $uid is an internal generated class name.
            echo '<style>';
            if ( $border_css ) echo ".{$uid}.olo-image{{$border_css}}";
            if ( $border_hover_css ) echo $border_hover_css;
            if ( $border_effect_css ) echo $border_effect_css;
            echo '</style>';
            // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        // CSS SpotlightFX (scoped per istanza)
        if ( $spot_css ) echo $spot_css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS built above from intval/round values, safe_color_css() tint and internal class names.

        // WaterDisplacement: filtro SVG (UID-scoped) + CSS wrapper. Stampati una volta per istanza.
        if ( $water_svg ) echo $water_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG filter markup built above with esc_attr() on every attribute value.
        if ( $water_css ) echo $water_css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS built above from internal generated class/filter ids only.
        ?>
        <?php
        // ── Dimensioni & fit (controlli professionali) ──
        $figure_style = 'margin: 0;';
        $img_width   = trim( (string) ( $s['image_width'] ?? '100%' ) );
        $img_height  = trim( (string) ( $s['height'] ?? '300px' ) );
        $img_maxw    = trim( (string) ( $s['max_width'] ?? '' ) );
        $aspect      = $s['aspect_ratio'] ?? 'auto';
        $aspect_css  = '';
        if ( $aspect && $aspect !== 'auto' ) {
            $aspect_css = $aspect === 'custom'
                ? trim( (string) ( $s['aspect_ratio_custom'] ?? '' ) )
                : $aspect;
        }
        $valid_fit = [ 'cover', 'contain', 'fill', 'none', 'scale-down' ];
        $obj_fit   = in_array( $s['object_fit'] ?? 'cover', $valid_fit, true ) ? $s['object_fit'] : 'cover';
        $obj_pos   = trim( (string) ( $s['object_position'] ?? 'center center' ) );
        $align     = $s['image_alignment'] ?? 'center';
        $valid_align = [ 'left', 'center', 'right' ];
        if ( ! in_array( $align, $valid_align, true ) ) $align = 'center';

        // Applica width/max-width al figure (così il container può essere ristretto e centrato)
        if ( $img_width !== '' && $img_width !== '100%' ) {
            $figure_style .= ' width: ' . esc_attr( $img_width ) . ';';
        }
        if ( $img_maxw !== '' && $img_maxw !== 'none' ) {
            $figure_style .= ' max-width: ' . esc_attr( $img_maxw ) . ';';
        }
        // Aspect ratio sul figure (se settato, l'altezza segue il rapporto)
        if ( $aspect_css !== '' ) {
            $figure_style .= ' aspect-ratio: ' . esc_attr( $aspect_css ) . ';';
        }
        // Allineamento: margin auto per left/center/right
        if ( $align === 'center' ) {
            $figure_style .= ' margin-left: auto; margin-right: auto;';
        } elseif ( $align === 'right' ) {
            $figure_style .= ' margin-left: auto; margin-right: 0;';
        } else {
            $figure_style .= ' margin-left: 0; margin-right: auto;';
        }

        if ( $br_css ) {
            $figure_style .= ' border-radius: ' . esc_attr( $br_css ) . '; overflow: hidden;';
        } elseif ( $has_hover_br ) {
            $figure_style .= ' border-radius: 0; overflow: hidden;';
        }

        // Shadow: applicata al figure. Quando c'è border-radius + overflow:hidden,
        // box-shadow funziona comunque (CSS standard: shadow vive fuori dal box).
        // Per shadow custom (preset 'custom'), si possono usare i sub-field shadow_h/v/blur/spread/color/inset.
        $shadow_value = '';
        $shadow_pref  = $s['shadow'] ?? 'none';
        if ( $shadow_pref === 'custom' ) {
            $sh_h = intval( $s['shadow_h']      ?? 0 );
            $sh_v = intval( $s['shadow_v']      ?? 4 );
            $sh_b = max( 0, intval( $s['shadow_blur']   ?? 10 ) );
            $sh_s = intval( $s['shadow_spread']    ?? 0 );
            $sh_c = $this->safe_color_css( $s['shadow_color'] ?? 'rgba(0,0,0,0.15)' ) ?: 'rgba(0,0,0,0.15)';
            $sh_inset = ! empty( $s['shadow_inset'] ) ? 'inset ' : '';
            $shadow_value = $sh_inset . "{$sh_h}px {$sh_v}px {$sh_b}px {$sh_s}px {$sh_c}";
        } elseif ( $shadow_pref && $shadow_pref !== 'none' ) {
            $shadow_value = Olo_Tile_Utils::shadow( $shadow_pref );
            if ( $shadow_value === 'none' ) $shadow_value = '';
        }
        if ( $shadow_value ) {
            $figure_style .= ' box-shadow: ' . esc_attr( $shadow_value ) . ';';
        }

        // Posizione verticale nella colonna: usa :has() per rendere flex la column
        // parent e applica margin-auto al .olo-frontend-tile wrapper (parent diretto
        // del figure) per ancorarla in alto/centro/basso.
        // Struttura HTML:  .uk-width-X-Y (column) > .olo-frontend-tile (tile wrapper) > figure.olo-image
        // Quindi il flex va sulla column, il margin auto sul tile wrapper.
        $align_in_col = $s['align_in_column'] ?? '';
        $align_data_attr = '';
        $align_css_block = '';
        if ( in_array( $align_in_col, [ 'top', 'center', 'bottom' ], true ) ) {
            $align_data_attr = ' data-olo-align-col="' . esc_attr( $align_in_col ) . '"';
            $tile_margin = '';
            if ( $align_in_col === 'top' )    $tile_margin = 'margin-bottom:auto;';
            if ( $align_in_col === 'center' ) $tile_margin = 'margin-top:auto;margin-bottom:auto;';
            if ( $align_in_col === 'bottom' ) $tile_margin = 'margin-top:auto;';
            // Cerca la column UIkit (class che contiene "uk-width-") che ha il figure come descendant
            $align_css_block = '<style>'
                . '[class*="uk-width-"]:has(figure.olo-image.' . $uid . '[data-olo-align-col]){display:flex;flex-direction:column;}'
                . '.olo-frontend-tile:has(> figure.olo-image.' . $uid . '[data-olo-align-col]){' . $tile_margin . '}'
                . '</style>';
        }
        echo $align_css_block; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS built above from internal $uid class and fixed margin declarations.
        ?>
        <figure class="olo-image <?php echo esc_attr( $uid ); ?>"<?php echo $align_data_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attribute string built above with esc_attr() on a whitelisted value ?><?php if ( ! empty( $s['lightbox'] ) && empty( $s['link_url'] ) ) echo ' data-uk-lightbox'; ?> style="<?php echo esc_attr( $figure_style ); ?>">
            <?php
            if ( ! empty( $s['image_url'] ) ) :
            $att_id = absint( $s['image_url_id'] ?? 0 );
            // No border-radius on the <img>: figure has overflow:hidden + radius which clips correctly.
            // Applying radius on both could conflict when the figure changes radius on :hover.
            // Quando aspect-ratio è settato sul figure, l'img usa height:100% per riempirlo.
            $img_h = $aspect_css !== '' ? '100%' : $img_height;
            $img_style = 'width: 100%; height: ' . esc_attr( $img_h ) . '; object-fit: ' . esc_attr( $obj_fit ) . '; object-position: ' . esc_attr( $obj_pos ) . '; display: block;';
            $extra  = 'uk-img style="' . $img_style . '"';
            $img_opts = [];
            if ( ! empty( $s['_img_loading'] ) ) $img_opts['loading'] = $s['_img_loading'];
            if ( ! empty( $s['_fetch_priority'] ) ) $img_opts['fetchpriority'] = $s['_fetch_priority'];
            $img    = Olo_Tile_Utils::img_srcset( $att_id, $s['image_url'], $s['alt_text'], 'uk-border-rounded', 'full', $extra, $img_opts );

            $img = $this->render_hover_wrap( $img, $s['hover_image'] ?? '', $s['hover_video'] ?? '' );

            // WaterDisplacement (target=image): avvolge l'immagine nel wrapper filtrato.
            // Per target=section-bg il filtro è applicato dal runtime alla sezione, non qui.
            if ( $water_on && $water_tgt === 'image' && $water_cls ) {
                $img = '<div class="' . esc_attr( $water_cls ) . '">' . $img . '</div>';
            }

            if ( ! empty( $s['link_url'] ) ) {
                $link_rel = ! empty( $s['_link_rel'] ) ? ' rel="' . esc_attr( $s['_link_rel'] ) . '"' : '';
                $link_title = ! empty( $s['_link_title'] ) ? ' title="' . esc_attr( $s['_link_title'] ) . '"' : '';
                // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- placeholders use esc_url/esc_attr; $link_rel/$link_title built with esc_attr() above; $img is markup generated by Olo_Tile_Utils::img_srcset()/render_hover_wrap() which escape internally.
                printf(
                    '<a href="%s" target="%s"%s%s style="display: block;">%s</a>',
                    esc_url( $s['link_url'] ),
                    esc_attr( $s['link_target'] ),
                    $link_rel,
                    $link_title,
                    $img
                );
                // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
            } elseif ( ! empty( $s['lightbox'] ) ) {
                // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- href uses esc_url(); $img is markup generated by Olo_Tile_Utils::img_srcset()/render_hover_wrap() which escape internally.
                printf(
                    '<a href="%s" style="display: block;">%s</a>',
                    esc_url( $s['image_url'] ),
                    $img
                );
                // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
            } else {
                echo $img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup generated by Olo_Tile_Utils::img_srcset()/render_hover_wrap() which escape internally.
            }
            else :
                // Media slot universale: media_bg (ogni tipo) o placeholder a righe + etichetta
                // Senza <img> il box deve avere un'altezza PROPRIA (come il render Vue, che mette
                // height sul div): "height:100%" erediterebbe da un <figure> privo di altezza →
                // collasso a 0 e lo sfondo/placeholder non si vedrebbe. Con aspect-ratio sul figure
                // il 100% è corretto; altrimenti si usa l'Altezza impostata (default 300px).
                $media_h_css = 'height:' . esc_attr( $aspect_css !== '' ? '100%' : ( ( $img_height !== '' && $img_height !== 'auto' ) ? $img_height : '300px' ) ) . ';';
                $parts = $this->bg_media_parts( $s['media_bg'] ?? [], '.' . $uid );
                if ( $parts['has'] ) {
                    echo '<div class="olo-img-media" style="position:relative;width:100%;' . $media_h_css . 'overflow:hidden;' . $parts['css'] . '">' . $parts['markup'] . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS and markup generated by Olo_Tile_Base::bg_media_parts()/Olo_CSS_Builder which sanitize internally.
                } else {
                    $lbl = trim( (string) ( $s['media_label'] ?? '' ) );
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $media_h_css is esc_attr()'d at build; $lbl is esc_html()'d below; the rest is static markup.
                    echo '<div class="olo-img-ph" style="position:relative;width:100%;' . $media_h_css . 'overflow:hidden;background:var(--olo-color-muted,#2b2b2b);background-image:repeating-linear-gradient(135deg,rgba(255,255,255,.05) 0 16px,transparent 16px 32px);">'
                       . ( $lbl !== '' ? '<span style="position:absolute;left:14px;bottom:12px;right:14px;font-size:10.5px;letter-spacing:.1em;color:rgba(255,255,255,.4);text-transform:uppercase;">' . esc_html( $lbl ) . '</span>' : '' )
                       . '</div>';
                }
            endif;
            // SpotlightFX overlay (decorativo, sopra l'immagine, sotto la didascalia)
            echo $spot_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup built above with esc_attr() on the class name.
            ?>
            <?php if ( ! empty( $s['caption'] ) ) : ?>
                <?php list( $ic_cls, $ic_data ) = $this->tfx_attrs( $s, 'caption', wp_strip_all_tags( $s['caption'] ) ); ?>
                <figcaption class="olo-img-caption<?php echo $ic_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- class/data attrs generated by Olo_Text_Effects helpers which escape internally ?>" style="padding: 8px 0; font-size: 0.875em; color: var(--olo-color-text-muted, #9CA3AF); text-align: center;"<?php echo $ic_data; ?>>
                    <?php echo esc_html( wp_strip_all_tags( $s['caption'] ) ); ?>
                </figcaption>
            <?php endif; ?>
        </figure>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated internally by Olo_Text_Effects::css() from sanitized settings.
        $this->tfx_print_script();

        // SpotlightFX runtime (inline, idempotente, multi-istanza)
        if ( $spot_js ) echo $spot_js; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline JS built above from fixed code with esc_js()'d internal ids only.

        // WaterDisplacement runtime (inline, idempotente, multi-istanza)
        if ( $water_js ) echo $water_js; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline JS built above from fixed code with esc_js()'d internal ids and json_encode()'d integers.

        return ob_get_clean();
    }
}
