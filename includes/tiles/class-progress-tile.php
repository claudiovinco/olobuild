<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Progress_Tile extends Olobuild_Tile_Base {

    protected $type     = 'progress';
    protected $name     = 'Barra progresso';
    protected $icon     = 'dashicons-chart-bar';
    protected $category = 'marketing';
    protected $defaults = [
        'preset' => 'custom',
        'bars'               => "HTML / CSS|90\nJavaScript|80\nVue.js|75\nPHP / WordPress|85",
        'bar_color'          => '',
        'bar_bg'             => '',
        'text_color'         => '',
        'height'             => '20',
        'show_percentage'    => true,
        'animated'           => true,
        'border_radius'      => '10',
        'layout'             => 'bar',
        'circle_size'        => '120',
        'circle_width'       => '8',
        'inner_text'         => '',
        'animate_counter'    => true,
        'animation_duration' => '1500',
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
            [ 'key' => 'layout',            'type' => 'select',   'label' => 'Layout' ],
            [ 'key' => 'bars',              'type' => 'textarea', 'label' => 'Bars (label|value per line, 0-100)' ],
            [ 'key' => 'bar_color',         'type' => 'color',    'label' => 'Bar Color' ],
            [ 'key' => 'bar_bg',            'type' => 'color',    'label' => 'Bar Background' ],
            [ 'key' => 'text_color',        'type' => 'color',    'label' => 'Text Color' ],
            [ 'key' => 'height',            'type' => 'range',    'label' => 'Bar Height' ],
            [ 'key' => 'show_percentage',   'type' => 'toggle',   'label' => 'Show Percentage' ],
            [ 'key' => 'animated',          'type' => 'toggle',   'label' => 'Animated' ],
            [ 'key' => 'border_radius',     'type' => 'range',    'label' => 'Border Radius' ],
            [ 'key' => 'circle_size',       'type' => 'range',    'label' => 'Circle Size' ],
            [ 'key' => 'circle_width',      'type' => 'range',    'label' => 'Circle Width' ],
            [ 'key' => 'inner_text',        'type' => 'text',     'label' => 'Inner Text' ],
            [ 'key' => 'animate_counter',   'type' => 'toggle',   'label' => 'Animate Counter' ],
            [ 'key' => 'animation_duration','type' => 'range',    'label' => 'Animation Duration (ms)' ],
        ];
    }

    public function render( $settings ) {
        $s    = wp_parse_args( $settings, $this->defaults );
        $bars = $this->parse_bars( $s['bars'] );

        $layout = isset( $s['layout'] ) ? $s['layout'] : 'bar';

        ob_start();

        $prog_fg  = $this->safe_color_css( $s['text_color'] );
        $prog_bar = $this->safe_color_css( $s['bar_color'] );
        $prog_bg  = $this->safe_color_css( $s['bar_bg'] );

        $uid = 'olo-prog-' . wp_unique_id();

        if ( $layout === 'circle' ) {
            $this->render_circle( $s, $bars, $prog_fg, $prog_bar, $prog_bg, $uid );
        } else {
            $this->render_bar( $s, $bars, $prog_fg, $prog_bar, $prog_bg, $uid );
        }

        /*
         * Animazioni d'ingresso: «Animata» riempie barre e anelli da zero, «Anima
         * contatore» fa salire le percentuali. «Animata» prima non lo leggeva nessuno.
         * L'HTML porta già i valori finali: senza JS, senza IntersectionObserver o con
         * «riduci movimento» la tile resta piena e coi numeri veri.
         */
        $animate_counter = ! empty( $s['animate_counter'] );
        $animate_fill    = ! empty( $s['animated'] );
        $duration        = max( 100, intval( $s['animation_duration'] ) );
        if ( $animate_counter || $animate_fill ) :
        ?>
        <script>
        (function(){
            var wrap = document.getElementById('<?php echo esc_js( $uid ); ?>');
            if(!wrap) return;
            if(!('IntersectionObserver' in window)) return;
            if(window.matchMedia){
                if(window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            }
            var fills = wrap.querySelectorAll('[data-olo-fill]');
            var rings = wrap.querySelectorAll('[data-olo-ring]');
            var counters = wrap.querySelectorAll('[data-olo-counter]');
            if(fills.length + rings.length + counters.length === 0) return;
            var dur = <?php echo (int) $duration; ?>;
            var curva = 'cubic-bezier(.22,1,.36,1)';
            [].forEach.call(fills, function(el){ el.style.transition = 'none'; el.style.width = '0%'; });
            [].forEach.call(rings, function(el){ el.style.transition = 'none'; el.style.strokeDashoffset = el.getAttribute('data-olo-circ'); });
            [].forEach.call(counters, function(el){ el.textContent = '0%'; });
            function parti(){
                void wrap.offsetWidth;
                [].forEach.call(fills, function(el){
                    el.style.transition = 'width ' + dur + 'ms ' + curva;
                    el.style.width = el.getAttribute('data-olo-fill') + '%';
                });
                [].forEach.call(rings, function(el){
                    el.style.transition = 'stroke-dashoffset ' + dur + 'ms ' + curva;
                    el.style.strokeDashoffset = el.getAttribute('data-olo-ring');
                });
                [].forEach.call(counters, function(el){
                    var target = parseInt(el.getAttribute('data-olo-counter'), 10) || 0;
                    var t0 = null;
                    function step(ts){
                        if(t0 === null) t0 = ts;
                        var p = Math.min((ts - t0) / dur, 1);
                        el.textContent = Math.round((1 - Math.pow(1 - p, 5)) * target) + '%';
                        if(p < 1) requestAnimationFrame(step);
                    }
                    requestAnimationFrame(step);
                });
            }
            var obs = new IntersectionObserver(function(entries){
                entries.forEach(function(entry){
                    if(entry.isIntersecting){
                        obs.disconnect();
                        parti();
                    }
                });
            }, {threshold: 0.2});
            obs.observe(wrap);
        })();
        </script>
        <?php
        endif;

                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( "#{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( "#{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo "#{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized border settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized border settings
        }
        return ob_get_clean();
    }

    /**
     * Render circle layout
     */
    private function render_circle( $s, $bars, $prog_fg, $prog_bar, $prog_bg, $uid ) {
        $circle_size  = max( 40, intval( $s['circle_size'] ) );
        $circle_width = max( 1, intval( $s['circle_width'] ) );
        $radius       = ( $circle_size - $circle_width ) / 2;
        $circumference = 2 * 3.14159265 * $radius;
        $cx            = $circle_size / 2;
        $inner_text    = isset( $s['inner_text'] ) ? trim( $s['inner_text'] ) : '';
        $animate       = ! empty( $s['animate_counter'] );
        $animate_fill  = ! empty( $s['animated'] );
        ?>
        <div id="<?php echo esc_attr( $uid ); ?>" class="olo-progress olo-pr-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?> olo-progress-circle" style="display:flex;flex-wrap:wrap;gap:16px;justify-content:center;padding:16px;">
            <?php foreach ( $bars as $bar ) :
                $val    = min( max( intval( $bar['value'] ), 0 ), 100 );
                $offset = $circumference - ( $circumference * $val / 100 );
                $font_size = round( $circle_size * 0.2 );
            ?>
            <div style="text-align:center;">
                <svg role="img" aria-label="<?php echo esc_attr( trim( $bar['label'] . ': ' . (int) $val . '%' ) ); ?>" width="<?php echo (int) $circle_size; ?>" height="<?php echo (int) $circle_size; ?>" viewBox="0 0 <?php echo (int) $circle_size; ?> <?php echo (int) $circle_size; ?>">
                    <circle aria-hidden="true" cx="<?php echo (float) $cx; ?>" cy="<?php echo (float) $cx; ?>" r="<?php echo (float) $radius; ?>" fill="none"
                        stroke="<?php echo esc_attr( $prog_bg ? $prog_bg : 'var(--olo-color-secondary, #1F2937)' ); ?>" stroke-width="<?php echo (int) $circle_width; ?>" />
                    <circle aria-hidden="true" cx="<?php echo (float) $cx; ?>" cy="<?php echo (float) $cx; ?>" r="<?php echo (float) $radius; ?>" fill="none"
                        stroke="<?php echo esc_attr( $prog_bar ? $prog_bar : 'var(--olo-color-primary, #e1474f)' ); ?>" stroke-width="<?php echo (int) $circle_width; ?>"
                        stroke-dasharray="<?php echo (float) $circumference; ?>" stroke-dashoffset="<?php echo (float) $offset; ?>"
                        stroke-linecap="round" transform="rotate(-90 <?php echo (float) $cx; ?> <?php echo (float) $cx; ?>)"
                        style="transition:stroke-dashoffset 1s ease;"<?php if ( $animate_fill ) : ?> data-olo-ring="<?php echo (float) $offset; ?>" data-olo-circ="<?php echo (float) $circumference; ?>"<?php endif; ?> />
                    <text x="<?php echo (float) $cx; ?>" y="<?php echo (float) $cx; ?>" text-anchor="middle" dominant-baseline="central"
                        fill="<?php echo esc_attr( $prog_fg ? $prog_fg : 'var(--olo-color-text, #1F2937)' ); /* era il colore dei bordi: numero grigio chiarissimo, quasi invisibile */ ?>" font-size="<?php echo (int) $font_size; ?>px" font-weight="600"
                        <?php if ( $animate && $inner_text === '' ) : ?>data-olo-counter="<?php echo (int) $val; ?>"<?php endif; ?>><?php
                        echo $inner_text ? esc_html( $inner_text ) : (int) $val . '%';
                    ?></text>
                </svg>
                <div style="margin-top:4px;font-size:11px;<?php if ( $prog_fg ) echo 'color:' . $prog_fg . ';'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- colour validated by safe_color_css() whitelist above ?>"><?php echo esc_html( $bar['label'] ); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

    /**
     * Render bar layout
     */
    private function render_bar( $s, $bars, $prog_fg, $prog_bar, $prog_bg, $uid ) {
        $inner_text   = isset( $s['inner_text'] ) ? trim( $s['inner_text'] ) : '';
        $animate      = ! empty( $s['animate_counter'] );
        $animate_fill = ! empty( $s['animated'] );
        // I preset «Gradient» salvano un gradiente, che safe_color_css() scarta: la
        // barra lo accetta, è un background (prima restava vuota).
        $fill_bg  = $prog_bar ? $prog_bar : $this->gradiente_barra( $s['bar_color'] );
        $track_bg = $prog_bg;
        if ( $fill_bg === '' ) {
            // Nessun colore scelto: traccia e barra non avevano sfondo e restavano solo
            // le etichette. Ripiego sui token, come la partenza (barra primaria su una
            // traccia della stessa tinta). Chi ha scelto il colore della barra e non
            // quello della traccia la tiene trasparente, come prima.
            $fill_bg = 'var(--olo-color-primary, #e1474f)';
            if ( $track_bg === '' ) {
                $track_bg = 'color-mix(in srgb, var(--olo-color-primary, #e1474f) 14%, transparent)';
            }
        }
        // La percentuale compariva due volte, in testa e dentro la barra: ora una sola.
        // Di norma in testa; Thick Bold, Gradient Bar e Gradient Aurora però hanno il
        // testo bianco pensato per stare SULLA barra (in testa, su pagina chiara, non si
        // vede): con quei preset e un testo chiaro va dentro la barra.
        $pct_dentro = false;
        if ( ! empty( $s['show_percentage'] ) && $inner_text === '' ) {
            if ( in_array( $s['preset'] ?? '', [ 'thick-bold', 'gradient-bar', 'gradient-aurora' ], true ) ) {
                $pct_dentro = $this->testo_chiaro( $prog_fg );
            }
        }
        // Con la percentuale dentro la barra il colore chiaro e' di quella: le etichette in
        // testa, su pagina chiara, restavano bianche e invisibili. Prendono il colore del testo.
        $head_fg = $pct_dentro ? '' : $prog_fg;
        ?>
        <div id="<?php echo esc_attr( $uid ); ?>" class="olo-progress olo-pr-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" style="padding:16px;display:flex;flex-direction:column;gap:16px;">
            <?php foreach ( $bars as $bar ) :
                $val = min( max( intval( $bar['value'] ), 0 ), 100 );
            ?>
                <div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px;<?php if ( $head_fg ) echo 'color:' . $head_fg . ';'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- colour validated by safe_color_css() whitelist above ?>font-size:0.875em;">
                        <span style="font-weight:600;"><?php echo esc_html( $bar['label'] ); ?></span>
                        <?php if ( $s['show_percentage'] && ! $pct_dentro ) : ?>
                            <span<?php if ( $animate ) : ?> data-olo-counter="<?php echo (int) $val; ?>"<?php endif; ?>><?php echo (int) $val; ?>%</span>
                        <?php endif; ?>
                    </div>
                    <?php
                        $rad_raw = $s['border_radius'];
                        if ( is_array( $rad_raw ) ) {
                            $radius_css = sprintf( '%dpx %dpx %dpx %dpx', absint( $rad_raw['tl'] ?? 0 ), absint( $rad_raw['tr'] ?? 0 ), absint( $rad_raw['br'] ?? 0 ), absint( $rad_raw['bl'] ?? 0 ) );
                            $has_radius = true;
                        } else {
                            $radius_css = intval( $rad_raw ) . 'px';
                            $has_radius = intval( $rad_raw ) > 0;
                        }
                        $bar_height = max( 10, intval( $s['height'] ) );
                    ?>
                    <div role="progressbar" aria-valuenow="<?php echo (int) $val; ?>" aria-valuemin="0" aria-valuemax="100" aria-label="<?php echo esc_attr( $bar['label'] ); ?>" style="position:relative;<?php if ( $track_bg !== '' ) echo 'background:' . esc_attr( $track_bg ) . ';'; ?><?php if ( $has_radius ) echo 'border-radius:' . $radius_css . ';'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- absint/intval-built radius and (int) height only ?>height:<?php echo (int) $bar_height; ?>px;overflow:hidden;">
                        <div style="height:100%;width:<?php echo (int) $val; ?>%;background:<?php echo esc_attr( $fill_bg ); ?>;<?php if ( $has_radius ) echo 'border-radius:' . $radius_css . ';'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- absint/intval-built radius only ?>transition:width 1s ease;"<?php if ( $animate_fill ) : ?> data-olo-fill="<?php echo (int) $val; ?>"<?php endif; ?>></div>
                        <?php // Dentro la barra: il testo interno, oppure la percentuale coi tre preset di $pct_dentro. ?>
                        <?php if ( $pct_dentro ) :
                            // La percentuale sta sul riempimento, allineata alla sua fine: centrata
                            // sulla traccia, sotto il 50% cadeva sulla parte chiara (bianco su grigio
                            // chiarissimo). Con poco riempimento non ci sta: va subito dopo, nel
                            // colore del testo della pagina.
                            $pct_fuori = $val < 15;
                            $pct_pos   = $pct_fuori ? 'left:calc(' . (int) $val . '% + 6px);transform:translateY(-50%);' : 'left:calc(' . (int) $val . '% - 8px);transform:translate(-100%,-50%);';
                        ?>
                            <span style="position:absolute;top:50%;<?php echo $pct_pos; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from (int) $val and fixed literals ?>white-space:nowrap;font-size:10px;font-weight:600;<?php if ( ! $pct_fuori ) { if ( $prog_fg ) echo 'color:' . $prog_fg . ';'; } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- colour validated by safe_color_css() whitelist above ?>"<?php if ( $animate ) : ?> data-olo-counter="<?php echo (int) $val; ?>"<?php endif; ?>><?php echo (int) $val; ?>%</span>
                        <?php elseif ( $inner_text !== '' ) : ?>
                            <span style="position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);font-size:10px;font-weight:600;<?php if ( $prog_fg ) echo 'color:' . $prog_fg . ';'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- colour validated by safe_color_css() whitelist above ?>"><?php echo esc_html( $inner_text ); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
            $rad_raw2 = $s['border_radius'];
            if ( is_array( $rad_raw2 ) ) {
                $radius_css2 = sprintf( '%dpx %dpx %dpx %dpx', absint( $rad_raw2['tl'] ?? 0 ), absint( $rad_raw2['tr'] ?? 0 ), absint( $rad_raw2['br'] ?? 0 ), absint( $rad_raw2['bl'] ?? 0 ) );
                $has_radius2 = true;
            } else {
                $radius_css2 = intval( $rad_raw2 ) . 'px';
                $has_radius2 = intval( $rad_raw2 ) > 0;
            }
            if ( $prog_bar || $has_radius2 ) :
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above (safe_color_css() whitelist colour, absint/intval-built radius). ?>
        <style>
            .olo-progress .uk-progress::-webkit-progress-value { <?php if ( $prog_bar ) echo 'background-color:' . $prog_bar . ';'; ?><?php if ( $has_radius2 ) echo 'border-radius:' . $radius_css2 . ';'; ?> }
            .olo-progress .uk-progress::-moz-progress-bar { <?php if ( $prog_bar ) echo 'background-color:' . $prog_bar . ';'; ?><?php if ( $has_radius2 ) echo 'border-radius:' . $radius_css2 . ';'; ?> }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php endif; ?>
        <?php
    }

    /**
     * Il gradiente dei preset «Gradient Bar» / «Gradient Aurora», o '' se il valore
     * non è un gradiente CSS. Niente ; { } < > né apici: finisce in un attributo style.
     */
    private function gradiente_barra( $value ) {
        $v = trim( (string) $value );
        if ( preg_match( '/^(?:repeating-)?(?:linear|radial|conic)-gradient\([^;{}<>"\']+\)$/', $v ) ) {
            return $v;
        }
        return '';
    }

    /**
     * true se il colore del testo è chiaro (bianco o un esadecimale luminoso): serve a
     * $pct_dentro, per non mettere un testo scuro sulla barra scura di Thick Bold.
     * Token e altri formati non si possono valutare qui: false (percentuale in testa).
     */
    private function testo_chiaro( $color ) {
        $c = strtolower( trim( (string) $color ) );
        if ( $c === 'white' ) {
            return true;
        }
        if ( ! preg_match( '/^#([0-9a-f]{3}|[0-9a-f]{6})$/', $c, $m ) ) {
            return false;
        }
        $h = $m[1];
        if ( strlen( $h ) === 3 ) {
            $h = $h[0] . $h[0] . $h[1] . $h[1] . $h[2] . $h[2];
        }
        $luma = 0.299 * hexdec( substr( $h, 0, 2 ) ) + 0.587 * hexdec( substr( $h, 2, 2 ) ) + 0.114 * hexdec( substr( $h, 4, 2 ) );
        return $luma > 160;
    }

    private function parse_bars( $text ) {
        $bars  = [];
        $text  = is_array( $text ) ? implode( "\n", $text ) : (string) $text;
        $lines = array_filter( array_map( 'trim', explode( "\n", $text ) ) );
        foreach ( $lines as $line ) {
            $parts = explode( '|', $line, 2 );
            if ( count( $parts ) === 2 ) {
                $bars[] = [ 'label' => trim( $parts[0] ), 'value' => trim( $parts[1] ) ];
            }
        }
        return $bars;
    }
}
