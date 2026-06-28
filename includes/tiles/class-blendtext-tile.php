<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Blendtext_Tile extends Olobuild_Tile_Base {

    protected $type     = 'blendtext';
    protected $name     = 'Blend Text';
    protected $icon     = 'dashicons-editor-textcolor';
    protected $category = 'creative';
    protected $defaults = [
        'text'            => 'BLEND',
        'tag'             => 'div',
        'font_size'       => '120',
        'font_size_tablet'=> '80',
        'font_size_mobile'=> '50',
        'font_weight'     => '900',
        'font_family'     => '',
        'text_transform'  => 'uppercase',
        'letter_spacing'  => '5',
        'line_height'     => '1',
        'text_align'      => 'center',
        'text_color'      => '#ffffff',
        'blend_mode'      => 'difference',
        'mode'              => 'text',
        'spotlight_size'    => 300,
        'spotlight_softness'=> 40,
        'spotlight_blend'   => 'difference',
        'spotlight_color'   => '#ffffff',
        'spotlight_easing'  => 22,
        'padding_top'     => '40',
        'padding_bottom'  => '40',
        'padding_left'    => '20',
        'padding_right'   => '20',
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

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $uid = 'olo-bt-' . wp_unique_id();

        $raw_text   = esc_html( wp_strip_all_tags( $s['text'] ) ) ?: 'BLEND';
        $text       = preg_replace( '/<\/?p[^>]*>/', '', $raw_text );
        $tag        = in_array( $s['tag'], [ 'h1','h2','h3','h4','h5','h6','p','div','span' ], true ) ? $s['tag'] : 'div';
        $fs         = intval( $s['font_size'] ) ?: 120;
        $fs_tablet  = intval( $s['font_size_tablet'] ) ?: 80;
        $fs_mobile  = intval( $s['font_size_mobile'] ) ?: 50;
        $fw         = esc_attr( $s['font_weight'] ) ?: '900';
        $ff         = $s['font_family'] ? esc_attr( $s['font_family'] ) : 'inherit';
        $tt         = esc_attr( $s['text_transform'] ) ?: 'uppercase';
        $ls         = intval( $s['letter_spacing'] );
        $lh         = floatval( $s['line_height'] ) ?: 1;
        $ta         = esc_attr( $s['text_align'] ) ?: 'center';
        $color      = $this->safe_color_css( $s['text_color'] ) ?: '#ffffff';
        $blend      = esc_attr( $s['blend_mode'] ) ?: 'difference';
        $mode       = ( ( $s['mode'] ?? 'text' ) === 'spotlight' ) ? 'spotlight' : 'text';
        // Padding: tile_padding (standard) oppure bt_padding/legacy
        $pad_obj    = $s['tile_padding'] ?? $s['bt_padding'] ?? null;
        if ( is_array( $pad_obj ) ) {
            $pt = intval( $pad_obj['top'] ?? 0 );
            $pr = intval( $pad_obj['right'] ?? 0 );
            $pb = intval( $pad_obj['bottom'] ?? 0 );
            $pl = intval( $pad_obj['left'] ?? 0 );
        } else {
            $pt = intval( $s['padding_top'] ?? 40 );
            $pb = intval( $s['padding_bottom'] ?? 40 );
            $pl = intval( $s['padding_left'] ?? 20 );
            $pr = intval( $s['padding_right'] ?? 20 );
        }

        // Apply mix-blend-mode to the OUTER .olo-frontend-tile wrapper using :has() selector
        // (CSS-native, no JS timing issues with parallax). The parent .olo-frontend-tile is
        // also the parallax target — putting mix-blend-mode on the SAME element as the
        // transform/z-index avoids the descendant-isolation pitfall: the blend composites
        // with the parent stacking context's backdrop (which contains the section bg image).
        // In modalità "spotlight" il blend NON è statico sul testo: lo porta il disco-torcia.
        $css  = '';
        if ( $mode === 'text' ) {
            $css .= ".olo-frontend-tile:has(> #{$uid}){mix-blend-mode:{$blend};}";
        }
        $css .= "#{$uid}{padding:{$pt}px {$pr}px {$pb}px {$pl}px}";
        $css .= "#{$uid} .olo-bt-text{font-size:{$fs}px;font-weight:{$fw};font-family:{$ff};text-transform:{$tt};letter-spacing:{$ls}px;line-height:{$lh};text-align:{$ta};color:{$color};margin:0}";
        $css .= "@media(max-width:960px){#{$uid} .olo-bt-text{font-size:{$fs_tablet}px !important}}";
        $css .= "@media(max-width:640px){#{$uid} .olo-bt-text{font-size:{$fs_mobile}px !important}}";

        // ── BlendText · Spotlight: disco-torcia che segue il cursore (rif. 63-tema-risograph.html) ──
        // Anatomia: <div#uid-flash> position:fixed, border-radius:50%, mix-blend-mode, pointer-events:none.
        // SSR: il testo resta leggibile senza JS. Runtime: portale su body + rAF easing. Scoped per UID.
        // a11y/touch: nascosto su (hover:none); reduced-motion → off. In builder: solo testo (no disco).
        $flash_css = $flash_html = $flash_js = '';
        $in_builder = ! empty( $s['_builder_mode'] );
        if ( $mode === 'spotlight' && ! $in_builder ) {
            $sp_size  = max( 40, min( 1000, intval( $s['spotlight_size'] ?? 300 ) ) );
            $sp_half  = intval( round( $sp_size / 2 ) );
            $sp_soft  = max( 0, min( 100, intval( $s['spotlight_softness'] ?? 40 ) ) );
            $sp_inner = max( 0, min( 100, 100 - $sp_soft ) );  // softness alto → inner basso → bordo più sfumato
            $sp_blend = in_array( $s['spotlight_blend'] ?? 'difference', [ 'difference', 'exclusion', 'screen' ], true ) ? $s['spotlight_blend'] : 'difference';
            $sp_color = $this->safe_color_css( $s['spotlight_color'] ?? '' ) ?: '#ffffff';
            $sp_ease  = max( 5, min( 90, intval( $s['spotlight_easing'] ?? 22 ) ) ) / 100;
            $flash_id = $uid . '-flash';

            $flash_css  = "#{$flash_id}{position:fixed;top:0;left:0;width:{$sp_size}px;height:{$sp_size}px;margin:-{$sp_half}px 0 0 -{$sp_half}px;border-radius:50%;pointer-events:none;z-index:99990;display:none;will-change:transform;background:radial-gradient(circle, {$sp_color} 0%, {$sp_color} {$sp_inner}%, transparent 100%);mix-blend-mode:{$sp_blend};}";
            $flash_css .= "@media(hover:none){#{$flash_id}{display:none !important;}}";

            $flash_html = '<div id="' . esc_attr( $flash_id ) . '" class="olo-bt-flash" aria-hidden="true"></div>';

            ob_start();
            ?>
            <script>
            (function(){
                var flash = document.getElementById('<?php echo esc_js( $flash_id ); ?>');
                if(!flash) return;
                if(flash.dataset.oloFlash) return;
                flash.dataset.oloFlash = '1';
                if(window.matchMedia && window.matchMedia('(hover:none)').matches) return;
                if(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                document.body.appendChild(flash);
                flash.style.display = 'block';
                var EASE = <?php echo (float) $sp_ease; ?>;
                var x = window.innerWidth / 2, y = window.innerHeight / 2, cx = x, cy = y;
                var running = false;
                function loop(){
                    cx += (x - cx) * EASE;
                    cy += (y - cy) * EASE;
                    if ( Math.abs(x - cx) < 0.5 && Math.abs(y - cy) < 0.5 ) {
                        flash.style.transform = 'translate(' + x + 'px,' + y + 'px)';
                        running = false; return;
                    }
                    flash.style.transform = 'translate(' + cx + 'px,' + cy + 'px)';
                    requestAnimationFrame( loop );
                }
                function start(){ if ( ! running ) { running = true; requestAnimationFrame( loop ); } }
                window.addEventListener('pointermove', function( e ){ x = e.clientX; y = e.clientY; start(); }, { passive: true });
            })();
            </script>
            <?php
            $flash_js = ob_get_clean();
        }

        list( $bt_cls, $bt_data ) = $this->tfx_attrs( $s, 'text', wp_strip_all_tags( $s['text'] ) );

        ob_start();
        echo '<style>' . $css . $flash_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS assembled above from intval()/floatval() clamped numerics, esc_attr()'d typography values, safe_color_css() whitelisted colors, in_array() whitelisted blend mode and the internally generated uid
        ?>
        <div id="<?php echo esc_attr( $uid ); ?>">
            <<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $tag is in_array() whitelisted; tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); $text is esc_html()'d above (nl2br only adds <br /> tags) ?> class="olo-bt-text<?php echo $bt_cls; ?>"<?php echo $bt_data; ?>><?php echo nl2br( $text ); ?></<?php echo $tag; ?>>
        </div>
        <?php echo $flash_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup built above with esc_attr()'d id ?>
        <?php if ( $mode === 'text' ) : // l'auto-fix stacking-context serve solo al blend statico ?>
        <script>
        (function(){
            var el = document.getElementById('<?php echo esc_js( $uid ); ?>');
            if(!el) return;
            // Strip stacking-context creators (z-index, isolation) from ancestors up to the section,
            // so the blend on .olo-frontend-tile reaches the section background image.
            var target = el.parentElement;
            while (target && target.tagName !== 'SECTION') {
                if (target.classList && target.classList.contains('olo-frontend-tile')) break;
                target = target.parentElement;
            }
            var chain = [];
            var p = (target && target.tagName !== 'SECTION') ? target.parentElement : el.parentElement;
            while (p) {
                if (p.tagName === 'SECTION') break;
                chain.push(p);
                p = p.parentElement;
            }
            function clean(){
                for (var i = 0; i < chain.length; i++) {
                    var st = chain[i].style;
                    if (st.zIndex) st.zIndex = '';
                    var cs = getComputedStyle(chain[i]);
                    if (cs.isolation === 'isolate') st.isolation = 'auto';
                }
            }
            clean();
            requestAnimationFrame(clean);
            setTimeout(clean, 100);
            setTimeout(clean, 500);
            try {
                var mo = new MutationObserver(clean);
                for (var i = 0; i < chain.length; i++) {
                    mo.observe(chain[i], { attributes: true, attributeFilter: ['style'] });
                }
            } catch(e){}
        })();
        </script>
        <?php endif; ?>
        <?php echo $flash_js; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline <script> assembled above with esc_js()'d id and float-cast easing only ?>
        <?php
        $tfx_css = $this->tfx_css( $s, '#' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Text_Effects::css() from whitelisted effects, sanitized colors and integer timings
        $this->tfx_print_script();
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }
}
