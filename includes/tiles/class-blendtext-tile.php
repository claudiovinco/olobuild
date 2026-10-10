<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Blendtext_Tile extends Olobuild_Tile_Base {

    protected $type     = 'blendtext';
    protected $name     = 'Blend Text';
    protected $icon     = 'dashicons-editor-textcolor';
    protected $category = 'text';
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
        'text_color'      => 'var(--olo-color-light, #ffffff)',
        'blend_mode'      => 'difference',
        'mode'              => 'text',
        'spotlight_size'    => 300,
        'spotlight_softness'=> 40,
        'spotlight_blend'   => 'difference',
        'spotlight_color'   => 'var(--olo-color-light, #ffffff)',
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
        $fw         = $this->font_weight_css( $s['font_weight'] ) ?: '900';
        // Famiglia dal risolutore comune: esc_attr() trasformava gli apici di un font salvato
        // come "'Playfair Display', serif" in &#039;, la dichiarazione non era più CSS valido
        // e il testo tornava al font del tema.
        $ff         = $this->resolve_font_family( (string) $s['font_family'] ) ?: 'inherit';
        $tt         = in_array( $s['text_transform'], [ 'none', 'uppercase', 'lowercase', 'capitalize' ], true ) ? $s['text_transform'] : 'uppercase';
        $ls         = intval( $s['letter_spacing'] );
        $lh         = floatval( $s['line_height'] ) ?: 1;
        $allineam   = [ 'left', 'center', 'right' ];
        $ta         = in_array( $s['text_align'], $allineam, true ) ? $s['text_align'] : 'center';
        $color      = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-light, #ffffff)';
        $blend      = in_array( $s['blend_mode'], [ 'normal', 'multiply', 'screen', 'overlay', 'darken', 'lighten', 'color-dodge', 'color-burn', 'hard-light', 'soft-light', 'difference', 'exclusion', 'hue', 'saturation', 'color', 'luminosity' ], true ) ? $s['blend_mode'] : 'difference';
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
        // Allineamento per dispositivo: il campo lo offriva (tablet, telefono) ma i valori
        // non li leggeva nessuno.
        $css .= $this->css_per_dispositivo( $s, 'text_align', "#{$uid} .olo-bt-text", static function ( $a ) use ( $allineam ) {
            return in_array( $a, $allineam, true ) ? 'text-align:' . $a : '';
        } );

        // Modalità «Torcia» (dati di prima della 1.4.633). La torcia ora è un effetto del mouse: Avanzate → Effetti
        // mouse → Spotlight cursore, con l'Ambito; il builder converte la tile quando la apre. Finché non la si salva
        // convertita la disegna il motore comune della pagina, con ambito «Tutta la pagina» come prima: un disco nel
        // body che segue il puntatore ovunque. Prima la tile aveva un disco suo e nel builder non si vedeva.
        $spot_attr = '';
        if ( $mode === 'spotlight' ) {
            $spot_attr = Olobuild_Animation_Builder::spotlight_attr( [
                'scope'  => 'page',
                'size'   => $s['spotlight_size'] ?? 300,
                'soft'   => $s['spotlight_softness'] ?? 40,
                'blend'  => $s['spotlight_blend'] ?? 'difference',
                'color'  => $this->safe_color_css( $s['spotlight_color'] ?? '' ) ?: 'var(--olo-color-light, #ffffff)',
                'easing' => $s['spotlight_easing'] ?? 22,
            ] );
        }

        list( $bt_cls, $bt_data ) = $this->tfx_attrs( $s, 'text', wp_strip_all_tags( $s['text'] ) );

        ob_start();
        echo '<style>' . $css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS assembled above from intval()/floatval() clamped numerics, typography from resolve_font_family()/font_weight_css() and in_array() whitelists (transform, align, blend mode), safe_color_css() whitelisted colors, css_per_dispositivo() media queries from the same align whitelist and the internally generated uid
        ?>
        <div id="<?php echo esc_attr( $uid ); ?>"<?php echo $spot_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- data-olo-spotlight built by Olobuild_Animation_Builder::spotlight_attr() (esc_attr of a wp_json_encode of clamped/whitelisted values) ?>>
            <<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $tag is in_array() whitelisted; tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); $text is esc_html()'d above (nl2br only adds <br /> tags) ?> class="olo-bt-text<?php echo $bt_cls; ?>"<?php echo $bt_data; ?>><?php echo nl2br( $text ); ?></<?php echo $tag; ?>>
        </div>
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
        <?php
        $tfx_css = $this->tfx_css( $s, '#' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Text_Effects::css() from whitelisted effects, sanitized colors and integer timings
        $this->tfx_print_script();
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
