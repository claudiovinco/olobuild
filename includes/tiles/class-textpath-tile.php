<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Textpath_Tile extends Olobuild_Tile_Base {

    protected $type     = 'textpath';
    protected $name     = 'Testo su Tracciato';
    protected $icon     = 'dashicons-editor-textcolor';
    protected $category = 'text';
    protected $defaults = [
        'text'            => 'Testo che segue un tracciato curvo',
        'path_preset'     => 'arc',
        'custom_path'     => '',
        'font_size'       => '24',
        'text_color'      => '',
        'letter_spacing'  => '2',
        'animation'       => 'none',
        'animation_speed' => '10',
        // Spin: rotazione continua dell'intero gruppo (additivo, default OFF). Reduced-motion → fermo.
        'spin'            => false,
        'spin_speed'      => '14',
        'spin_direction'  => 'cw',
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
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-textpath-' . wp_unique_id();

        // Settings
        $text    = esc_html( $s['text'] );
        $fsize   = max( 12, min( 72, intval( $s['font_size'] ) ) );
        $color   = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text, #374151)';
        $spacing = max( 0, min( 20, intval( $s['letter_spacing'] ) ) );
        $anim    = in_array( $s['animation'], [ 'none', 'scroll', 'continuous' ], true ) ? $s['animation'] : 'none';
        $speed   = max( 1, min( 20, intval( $s['animation_speed'] ) ) );

        // Spin (rotazione continua del gruppo) — scoped per istanza, reduced-motion aware
        $spin       = ! empty( $s['spin'] );
        $spin_speed = max( 3, min( 40, intval( $s['spin_speed'] ) ) );
        $spin_dir   = ( $s['spin_direction'] === 'ccw' ) ? 'reverse' : 'normal';

        // Path presets
        $presets = [
            'arc'    => 'M 10 80 Q 150 10 290 80',
            'wave'   => 'M 0 50 Q 75 0 150 50 Q 225 100 300 50',
            'circle' => 'M 150,10 A 140,140 0 1,1 149.99,10',
            'spiral' => 'M 150,75 C 150,30 200,10 220,50 C 240,90 200,120 160,100 C 120,80 110,40 150,20 C 190,0 250,30 260,75 C 270,120 220,160 160,140',
        ];

        $preset = $s['path_preset'];
        if ( $preset === 'custom' ) {
            // Sanitize custom path: allow only SVG path commands
            $path_d = preg_replace( '/[^A-Za-z0-9.,\s\-]/', '', $s['custom_path'] );
            if ( empty( $path_d ) ) {
                $path_d = $presets['arc'];
            }
        } else {
            $path_d = isset( $presets[ $preset ] ) ? $presets[ $preset ] : $presets['arc'];
        }

        // ViewBox based on preset
        $viewbox = ( $preset === 'circle' ) ? '0 0 300 300' : '0 0 300 100';
        if ( $preset === 'spiral' ) {
            // La spirale occupa x 124,5-261,3 e y 13,7-145,3: nel riquadro 300x100 usciva
            // sotto di quasi meta' altezza e stava spostata a destra. Stessa larghezza
            // (la scala resta quella degli altri tracciati), centrata, alta quanto serve
            // piu' lo spazio per le lettere, che stanno a cavallo del tracciato.
            $m       = (int) ceil( $fsize * 0.6 ) + 4;
            $viewbox = '43 ' . ( 13.7 - $m ) . ' 300 ' . ( 131.6 + 2 * $m );
        }
        $chiuso = ( $preset === 'circle' ) || (bool) preg_match( '/z\s*$/i', $path_d );

        $path_id = $uid . '-path';

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: $spin_speed via intval()+min()/max() clamps, $spin_dir from a fixed 'reverse'/'normal' ternary; $uid is internally generated.
        ?>
        <style>
            .<?php echo $uid; ?> {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .<?php echo $uid; ?> svg {
                width: 100%;
                height: auto;
                overflow: visible;
            }

            <?php if ( $spin ) : ?>
            @keyframes <?php echo $uid; ?>-spin {
                to { transform: rotate(360deg); }
            }
            .<?php echo $uid; ?> svg {
                transform-box: view-box;
                transform-origin: center;
                animation: <?php echo $uid; ?>-spin <?php echo $spin_speed; ?>s linear infinite;
                animation-direction: <?php echo $spin_dir; ?>;
                will-change: transform;
            }
            @media (prefers-reduced-motion: reduce) {
                .<?php echo $uid; ?> svg { animation: none; }
            }
            <?php endif; ?>

        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <div class="olo-textpath <?php echo esc_attr( $uid ); ?>" id="<?php echo esc_attr( $uid ); ?>">
            <svg viewBox="<?php echo esc_attr( $viewbox ); ?>" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <path id="<?php echo esc_attr( $path_id ); ?>" d="<?php echo esc_attr( $path_d ); ?>" fill="none" />
                </defs>
                <text
                    fill="<?php echo $color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- validated via the safe_color_css() whitelist above ?>"
                    font-size="<?php echo (int) $fsize; ?>"
                    letter-spacing="<?php echo (int) $spacing; ?>"
                    font-family="inherit"
                >
                    <?php list( $tp_cls, $tp_data ) = $this->tfx_attrs( $s, 'text', wp_strip_all_tags( $s['text'] ?? '' ) ); ?>
                    <textPath
                        href="#<?php echo esc_attr( $path_id ); ?>"
                        startOffset="0%"
                        dominant-baseline="middle"
                        id="<?php echo esc_attr( $uid ); ?>-tp"
                        class="<?php echo trim( $tp_cls ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() class fragments are escaped internally (sanitize_html_class) ?>"
                        <?php echo $tp_data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() data attributes are escaped internally (esc_attr) ?>
                    ><?php echo $text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped via esc_html() at assignment above ?></textPath>
                </text>
            </svg>
        </div>

        <script>
        (function(){
            /*
             * Un testo piu' lungo del tracciato si rimpicciolisce finche' ci sta (prima
             * veniva troncato senza avviso). «Scorrimento una volta» entra dalla fine del
             * tracciato e si ferma dov'e' il testo fermo, quando la tile si vede (prima
             * arrivava al 100% e spariva). «Scorrimento continuo» e' un nastro: la frase
             * si ripete e scorre senza fine (prima usciva e rientrava); sul cerchio le
             * ripetizioni si distribuiscono in modo che la giuntura non si veda.
             * Con prefers-reduced-motion il testo resta fermo.
             */
            var root = document.getElementById('<?php echo esc_js( $uid ); ?>');
            var tp = document.getElementById('<?php echo esc_js( $uid ); ?>-tp');
            if (!root || !tp) { return; }
            var svg = root.querySelector('svg');
            var path = root.querySelector('path');
            var text = root.querySelector('text');
            var modo = '<?php echo esc_js( $anim ); ?>';
            var durata = <?php echo (int) $speed; ?> * 1000;
            var chiuso = <?php echo $chiuso ? 'true' : 'false'; ?>;
            var corpo = <?php echo (int) $fsize; ?>;
            var fermo = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)').matches : false;
            var frase = tp.textContent;

            function misura(str) {
                var m = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                m.setAttribute('font-size', text.getAttribute('font-size'));
                m.setAttribute('letter-spacing', text.getAttribute('letter-spacing'));
                m.setAttribute('visibility', 'hidden');
                m.textContent = str;
                svg.appendChild(m);
                var l = m.getComputedTextLength();
                svg.removeChild(m);
                return l;
            }

            function adatta() {
                text.setAttribute('font-size', corpo);
                var giro = path.getTotalLength();
                var lungo = misura(frase);
                if (lungo > giro) { text.setAttribute('font-size', (corpo * giro / lungo * 0.97).toFixed(2)); }
            }

            function entra() {
                if (fermo) { return; }
                var t0 = null;
                function passo(t) {
                    if (t0 === null) { t0 = t; }
                    var p = Math.min(1, (t - t0) / durata);
                    var e = 1 - Math.pow(1 - p, 3);
                    tp.setAttribute('startOffset', ((1 - e) * 100).toFixed(2) + '%');
                    if (p < 1) { requestAnimationFrame(passo); }
                }
                requestAnimationFrame(passo);
            }

            function nastro() {
                var giro = path.getTotalLength();
                var sep = '\u00a0\u00b7\u00a0';
                var unita = misura(frase + sep);
                if (!(unita > 0)) { return; }
                var k = Math.max(1, Math.round(giro / unita));
                var n = chiuso ? k + 1 : Math.ceil(giro / unita) + 1;
                var periodo = chiuso ? giro / k : unita;
                var tutto = '';
                for (var i = 0; i < n; i++) { tutto += frase + sep; }
                tp.textContent = tutto;
                if (chiuso) {
                    /* sul textPath: sul <text> Chrome lo ignora */
                    tp.setAttribute('textLength', (periodo * n).toFixed(2));
                    tp.setAttribute('lengthAdjust', 'spacing');
                }
                if (fermo) { return; }
                var velocita = giro / durata;
                var t0 = null;
                function passo(t) {
                    if (t0 === null) { t0 = t; }
                    tp.setAttribute('startOffset', (-((t - t0) * velocita % periodo)).toFixed(2));
                    requestAnimationFrame(passo);
                }
                requestAnimationFrame(passo);
            }

            function avvia() {
                if (modo === 'continuous') { nastro(); return; }
                adatta();
                if (modo !== 'scroll') { return; }
                if (!('IntersectionObserver' in window)) { entra(); return; }
                var io = new IntersectionObserver(function (voci) {
                    for (var i = 0; i < voci.length; i++) {
                        if (voci[i].isIntersecting) { io.disconnect(); entra(); return; }
                    }
                }, { threshold: 0.3 });
                io.observe(root);
            }

            if (modo === 'scroll') {
                if (!fermo) { tp.setAttribute('startOffset', '100%'); }
            }
            if (document.fonts) { document.fonts.ready.then(avvia); } else { avvia(); }
        })();
        </script>

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
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized border settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() shared helpers
        }
        return ob_get_clean();
    }
}
