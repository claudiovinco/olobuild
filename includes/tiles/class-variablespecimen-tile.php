<?php
/**
 * Tile Variable Specimen — playground per font variabili.
 *
 * Render server-side = stato base GIA' visibile (lettera + readout + slider statici).
 * Il runtime JS inline (IIFE, idempotente, multi-istanza) arricchisce:
 *   - drag sulla lettera: X → primo asse, Y → secondo asse → font-variation-settings
 *   - slider nativi per ogni asse, con aria-valuenow + readout live
 *   - auto-loop sinusoidale a riposo (off se prefers-reduced-motion)
 *   - fallback: se gli assi non sono supportati → peso statico predefinito
 *
 * Tutto è scoped sull'UID dell'istanza: CSS, @keyframes (n/a qui), id slider/readout.
 * Riferimento: handoff-tile-speciali/temi/66-tema-type-foundry.html (snippet in fondo).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Variablespecimen_Tile extends Olo_Tile_Base {

    protected $type     = 'variablespecimen';
    protected $name     = 'Specimen Variabile';
    protected $icon     = 'dashicons-editor-textcolor';
    protected $category = 'text';
    protected $defaults = [
        'font_family'          => '',
        'sample_text'          => 'Ga',
        'interaction'          => 'both',
        'auto_animate'         => true,
        'auto_speed'           => '6',
        'drag_hint'            => '↔ trascina · X = primo asse · Y = secondo asse',
        'show_readout'         => true,

        'axes'                 => [
            [ 'tag' => 'wght', 'label' => 'Peso',         'min' => 300, 'max' => 1000, 'default_val' => 650 ],
            [ 'tag' => 'slnt', 'label' => 'Inclinazione', 'min' => -15, 'max' => 0,    'default_val' => 0 ],
        ],

        'text_color'           => '',
        'accent_color'         => '',
        'bg_color'             => '',
        'font_size'            => '220',
        'font_weight_fallback' => '700',
        'text_align'           => 'left',
        'padding_y'            => '48',

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

    /**
     * Normalizza la lista assi: tag 4-lettere safe, numeri, label.
     * Garantisce almeno 1 asse (fallback wght) così il render non è mai vuoto.
     */
    private function normalize_axes( $raw ) {
        $out = [];
        if ( is_array( $raw ) ) {
            foreach ( $raw as $a ) {
                if ( ! is_array( $a ) ) {
                    continue;
                }
                // tag: solo lettere/cifre, 1-4 char (i tag registrati sono lowercase, i custom MAIUSCOLI)
                $tag = isset( $a['tag'] ) ? preg_replace( '/[^A-Za-z0-9]/', '', (string) $a['tag'] ) : '';
                $tag = substr( $tag, 0, 4 );
                if ( $tag === '' ) {
                    continue;
                }
                $min = isset( $a['min'] ) ? floatval( $a['min'] ) : 0;
                $max = isset( $a['max'] ) ? floatval( $a['max'] ) : 100;
                if ( $max < $min ) {
                    $t = $min; $min = $max; $max = $t;
                }
                $def = isset( $a['default_val'] ) ? floatval( $a['default_val'] ) : $min;
                $def = max( $min, min( $max, $def ) );
                $lbl = isset( $a['label'] ) && $a['label'] !== '' ? (string) $a['label'] : $tag;
                // step sensato: assi interi (wght, ...) step 1; assi con range piccolo (es. CASL 0..1) step fine
                $span = $max - $min;
                $step = ( $span > 0 && $span <= 2 ) ? 0.01 : 1;
                $out[] = [
                    'tag'   => $tag,
                    'label' => $lbl,
                    'min'   => $min,
                    'max'   => $max,
                    'def'   => $def,
                    'step'  => $step,
                ];
            }
        }
        if ( empty( $out ) ) {
            $out[] = [ 'tag' => 'wght', 'label' => 'Peso', 'min' => 300, 'max' => 1000, 'def' => 650, 'step' => 1 ];
        }
        return $out;
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-vf-' . wp_rand( 10000, 99999 );

        // ── Contenuto ──────────────────────────────────────────────
        $sample      = $s['sample_text'] !== '' ? $s['sample_text'] : 'Ga';
        $sample_html = nl2br( esc_html( wp_strip_all_tags( $sample ) ) );
        $interaction = in_array( $s['interaction'], [ 'drag', 'sliders', 'both' ], true ) ? $s['interaction'] : 'both';
        $auto        = ! empty( $s['auto_animate'] );
        $auto_speed  = max( 2, min( 16, intval( $s['auto_speed'] ) ) );
        $drag_hint   = esc_html( $s['drag_hint'] );
        $show_read   = ! empty( $s['show_readout'] );
        $has_drag    = ( $interaction === 'drag' || $interaction === 'both' );
        $has_slider  = ( $interaction === 'sliders' || $interaction === 'both' );

        $axes = $this->normalize_axes( $s['axes'] ?? [] );

        // ── Stile ──────────────────────────────────────────────────
        $font_family = trim( (string) $s['font_family'] );
        // family CSS safe: lettere, cifre, spazi, virgole, trattini, apici/doppi apici
        $font_family = preg_replace( '/[^A-Za-z0-9 ,"\'\-]/', '', $font_family );
        $font_css    = $font_family !== '' ? $font_family . ', inherit' : 'inherit';

        $text_color  = $this->safe_color_css( $s['text_color'] )  ?: 'var(--olo-color-text, #111827)';
        $accent      = $this->safe_color_css( $s['accent_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $bg_color    = $this->safe_color_css( $s['bg_color'] );
        $bg_decl     = $bg_color ? "background:{$bg_color};" : '';

        $font_size   = max( 24, min( 800, intval( $s['font_size'] ) ) );
        $fw_fallback = in_array( $s['font_weight_fallback'], [ '300','400','500','600','700','900' ], true ) ? $s['font_weight_fallback'] : '700';
        $text_align  = in_array( $s['text_align'], [ 'left','center','right' ], true ) ? $s['text_align'] : 'left';
        $pad_y       = max( 0, min( 200, intval( $s['padding_y'] ) ) );

        // font-variation-settings iniziale (stato SSR — già "bello" senza JS)
        $fvs_parts = [];
        foreach ( $axes as $a ) {
            $fvs_parts[] = '"' . $a['tag'] . '" ' . rtrim( rtrim( number_format( $a['def'], 2, '.', '' ), '0' ), '.' );
        }
        $fvs_init = implode( ',', $fvs_parts );

        // payload assi per il runtime (id slider scoped per istanza)
        $axes_js = [];
        foreach ( $axes as $i => $a ) {
            $axes_js[] = [
                'tag'  => $a['tag'],
                'min'  => $a['min'],
                'max'  => $a['max'],
                'def'  => $a['def'],
                'step' => $a['step'],
            ];
        }

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist colours, intval()-clamped sizes, in_array() whitelisted enums, a charset-filtered font stack and the internally generated $uid. ?>
        <style>
            .<?php echo $uid; ?> {
                <?php echo $bg_decl; ?>
                padding: <?php echo (int) $pad_y; ?>px 0;
                text-align: <?php echo $text_align; ?>;
            }
            .<?php echo $uid; ?> .olo-vf-stage {
                font-family: <?php echo $font_css; ?>;
                font-size: <?php echo (int) $font_size; ?>px;
                line-height: .82;
                letter-spacing: -.03em;
                color: <?php echo $text_color; ?>;
                margin: 0;
                /* stato base via SSR; il JS prende il controllo se i varfont sono supportati */
                font-variation-settings: <?php echo $fvs_init; ?>;
                font-weight: <?php echo $fw_fallback; ?>;
                <?php if ( $has_drag ) : ?>
                cursor: ew-resize;
                touch-action: none;
                <?php endif; ?>
                will-change: font-variation-settings;
                display: inline-block;
                max-width: 100%;
                word-break: break-word;
                outline: none;
            }
            /* fallback no-varfont: la famiglia gestisce comunque il peso statico */
            .<?php echo $uid; ?>.olo-vf-nofvs .olo-vf-stage {
                font-variation-settings: normal;
            }
            .<?php echo $uid; ?> .olo-vf-stage:focus-visible {
                box-shadow: 0 0 0 3px <?php echo $accent; ?>;
                border-radius: 6px;
            }
            <?php if ( $show_read ) : ?>
            .<?php echo $uid; ?> .olo-vf-readout {
                display: flex;
                flex-wrap: wrap;
                gap: 18px;
                margin-top: 18px;
                font-size: 13px;
                font-feature-settings: "tnum" 1;
                color: <?php echo $text_color; ?>;
                opacity: .8;
                justify-content: <?php echo $text_align === 'center' ? 'center' : ( $text_align === 'right' ? 'flex-end' : 'flex-start' ); ?>;
            }
            .<?php echo $uid; ?> .olo-vf-readout b {
                color: <?php echo $accent; ?>;
                font-weight: 700;
            }
            <?php endif; ?>
            <?php if ( $has_drag ) : ?>
            .<?php echo $uid; ?> .olo-vf-hint {
                margin-top: 14px;
                font-size: 11px;
                letter-spacing: .14em;
                text-transform: uppercase;
                opacity: .55;
                color: <?php echo $text_color; ?>;
            }
            <?php endif; ?>
            <?php if ( $has_slider ) : ?>
            .<?php echo $uid; ?> .olo-vf-controls {
                display: flex;
                flex-direction: column;
                gap: 16px;
                margin-top: 28px;
                max-width: 420px;
                <?php echo $text_align === 'center' ? 'margin-left:auto;margin-right:auto;' : ''; ?>
                <?php echo $text_align === 'right'  ? 'margin-left:auto;' : ''; ?>
            }
            .<?php echo $uid; ?> .olo-vf-ctrl label {
                display: flex;
                justify-content: space-between;
                font-size: 11px;
                letter-spacing: .06em;
                text-transform: uppercase;
                opacity: .8;
                margin-bottom: 7px;
                color: <?php echo $text_color; ?>;
            }
            .<?php echo $uid; ?> .olo-vf-ctrl label b { color: <?php echo $accent; ?>; }
            .<?php echo $uid; ?> .olo-vf-range {
                width: 100%;
                -webkit-appearance: none;
                appearance: none;
                height: 3px;
                background: color-mix(in srgb, <?php echo $text_color; ?> 22%, transparent);
                border-radius: 3px;
                outline: none;
            }
            .<?php echo $uid; ?> .olo-vf-range::-webkit-slider-thumb {
                -webkit-appearance: none;
                appearance: none;
                width: 18px; height: 18px;
                border-radius: 50%;
                background: <?php echo $accent; ?>;
                cursor: pointer;
                border: none;
            }
            .<?php echo $uid; ?> .olo-vf-range::-moz-range-thumb {
                width: 18px; height: 18px;
                border: none; border-radius: 50%;
                background: <?php echo $accent; ?>;
                cursor: pointer;
            }
            .<?php echo $uid; ?> .olo-vf-range:focus-visible {
                box-shadow: 0 0 0 3px color-mix(in srgb, <?php echo $accent; ?> 45%, transparent);
            }
            <?php endif; ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <div class="olo-vf <?php echo esc_attr( $uid ); ?>" data-interaction="<?php echo esc_attr( $interaction ); ?>">
            <div
                class="olo-vf-stage"
                id="<?php echo esc_attr( $uid ); ?>-stage"
                <?php if ( $has_drag ) : ?>role="slider" aria-label="<?php echo esc_attr__( 'Trascina per modificare gli assi del font', 'olobuild' ); ?>" tabindex="0"<?php endif; ?>
            ><?php echo $sample_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped via esc_html( wp_strip_all_tags() ) then nl2br() at assignment above; esc_html() here would re-encode the <br /> tags ?></div>

            <?php if ( $show_read ) : ?>
            <div class="olo-vf-readout" id="<?php echo esc_attr( $uid ); ?>-readout" aria-hidden="true">
                <?php foreach ( $axes as $i => $a ) : ?>
                    <span><?php echo esc_html( $a['tag'] ); ?> <b data-ro="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( rtrim( rtrim( number_format( $a['def'], 2, '.', '' ), '0' ), '.' ) ); ?></b></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if ( $has_drag ) : ?>
            <div class="olo-vf-hint"><?php echo $drag_hint; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped via esc_html() at assignment above ?></div>
            <?php endif; ?>

            <?php if ( $has_slider ) : ?>
            <div class="olo-vf-controls">
                <?php foreach ( $axes as $i => $a ) :
                    $val_disp = rtrim( rtrim( number_format( $a['def'], 2, '.', '' ), '0' ), '.' );
                    ?>
                    <div class="olo-vf-ctrl">
                        <label for="<?php echo esc_attr( $uid . '-ax-' . $i ); ?>">
                            <span><?php echo esc_html( $a['label'] ); ?> · <?php echo esc_html( $a['tag'] ); ?></span>
                            <b data-val="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $val_disp ); ?></b>
                        </label>
                        <input
                            type="range"
                            class="olo-vf-range"
                            id="<?php echo esc_attr( $uid . '-ax-' . $i ); ?>"
                            data-axis="<?php echo esc_attr( $i ); ?>"
                            min="<?php echo esc_attr( $a['min'] ); ?>"
                            max="<?php echo esc_attr( $a['max'] ); ?>"
                            step="<?php echo esc_attr( $a['step'] ); ?>"
                            value="<?php echo esc_attr( $a['def'] ); ?>"
                            aria-label="<?php echo esc_attr( $a['label'] . ' (' . $a['tag'] . ')' ); ?>"
                            aria-valuenow="<?php echo esc_attr( $a['def'] ); ?>"
                            aria-valuemin="<?php echo esc_attr( $a['min'] ); ?>"
                            aria-valuemax="<?php echo esc_attr( $a['max'] ); ?>"
                        />
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <script>
        /* Variable Specimen — runtime scoped per istanza (rif. 66-tema-type-foundry.html) */
        (function(){
            var root = document.querySelector('.<?php echo esc_js( $uid ); ?>');
            if ( ! root ) { return; }
            if ( root.dataset.oloVf ) { return; }   // idempotente: una sola init per istanza
            root.dataset.oloVf = '1';

            var stage = document.getElementById('<?php echo esc_js( $uid ); ?>-stage');
            if ( ! stage ) { return; }

            var AXES = <?php echo wp_json_encode( $axes_js ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline JSON in <script> generated by wp_json_encode() ?>;
            if ( ! AXES.length ) { return; }

            var HAS_DRAG   = <?php echo $has_drag ? 'true' : 'false'; ?>;
            var HAS_SLIDER = <?php echo $has_slider ? 'true' : 'false'; ?>;
            var AUTO       = <?php echo $auto ? 'true' : 'false'; ?>;
            var AUTO_SPEED = <?php echo wp_json_encode( $auto_speed ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline JSON in <script> generated by wp_json_encode() from an intval()-clamped number ?>;

            // Fallback: se font-variation-settings non è supportato → pesi statici, niente JS dinamico.
            var FVS_OK = ( 'CSS' in window ) && window.CSS.supports && window.CSS.supports( 'font-variation-settings', '"wght" 400' );
            if ( ! FVS_OK ) {
                root.classList.add( 'olo-vf-nofvs' );
                return;   // gli slider restano nel DOM ma non muovono nulla; il peso statico CSS regge la resa
            }

            var rm = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)');
            var REDUCED = !!( rm && rm.matches );

            // stato corrente = default di ogni asse
            var vals = AXES.map(function(a){ return a.def; });

            var ros   = root.querySelectorAll('[data-ro]');     // readout (può essere assente)
            var rovals = root.querySelectorAll('[data-val]');   // valori accanto agli slider
            var ranges = root.querySelectorAll('.olo-vf-range');

            function fmt(n){
                var r = Math.round(n * 100) / 100;
                return ( Math.abs(r - Math.round(r)) < 0.001 ) ? String(Math.round(r)) : r.toFixed(2);
            }
            function apply(){
                var parts = [];
                for ( var i = 0; i < AXES.length; i++ ) {
                    parts.push('"' + AXES[i].tag + '" ' + fmt(vals[i]));
                }
                stage.style.fontVariationSettings = parts.join(',');
                // readout sotto la lettera
                for ( var r = 0; r < ros.length; r++ ) {
                    var ri = parseInt(ros[r].getAttribute('data-ro'), 10);
                    if ( !isNaN(ri) && AXES[ri] ) { ros[r].textContent = fmt(vals[ri]); }
                }
            }
            function syncSliders(){
                for ( var k = 0; k < ranges.length; k++ ) {
                    var ai = parseInt(ranges[k].getAttribute('data-axis'), 10);
                    if ( isNaN(ai) || !AXES[ai] ) { continue; }
                    ranges[k].value = vals[ai];
                    ranges[k].setAttribute('aria-valuenow', fmt(vals[ai]));
                }
                for ( var v = 0; v < rovals.length; v++ ) {
                    var vi = parseInt(rovals[v].getAttribute('data-val'), 10);
                    if ( !isNaN(vi) && AXES[vi] ) { rovals[v].textContent = fmt(vals[vi]); }
                }
            }

            var auto = AUTO && !REDUCED;   // loop solo se richiesto e non reduced-motion
            var t = 0, rafId = null, running = false;

            // ── Drag: X → asse 0, Y → asse 1 (se esiste) ──
            if ( HAS_DRAG ) {
                var dragging = false;
                function fromEvent(e){
                    var rect = stage.getBoundingClientRect();
                    if ( rect.width > 0 && AXES[0] ) {
                        var px = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
                        vals[0] = AXES[0].min + px * (AXES[0].max - AXES[0].min);
                    }
                    if ( rect.height > 0 && AXES[1] ) {
                        var py = Math.max(0, Math.min(1, (e.clientY - rect.top) / rect.height));
                        vals[1] = AXES[1].min + py * (AXES[1].max - AXES[1].min);
                    }
                    apply();
                    if ( HAS_SLIDER ) { syncSliders(); }
                }
                // mouse fine (hover:fine) → morph al passaggio; touch/pen → solo durante il drag attivo
                var FINE = !( window.matchMedia && window.matchMedia('(hover: none), (pointer: coarse)').matches );
                stage.addEventListener('pointermove', function(e){
                    if ( ! ( ( e.pointerType === 'mouse' && FINE ) || dragging ) ) { return; }
                    auto = false;   // l'utente prende il controllo
                    fromEvent(e);
                });
                stage.addEventListener('pointerdown', function(e){
                    dragging = true; auto = false;
                    try { stage.setPointerCapture(e.pointerId); } catch(err){}
                    fromEvent(e);
                });
                stage.addEventListener('pointerup', function(e){ dragging = false; try { stage.releasePointerCapture(e.pointerId); } catch(err){} });
                stage.addEventListener('pointerleave', function(){ if ( AUTO && !REDUCED ) { auto = true; } });
                // tastiera: frecce muovono asse 0 (e Shift = asse 1 se presente)
                stage.addEventListener('keydown', function(e){
                    var axIdx = e.shiftKey && AXES[1] ? 1 : 0;
                    var ax = AXES[axIdx];
                    var stepK = ax.step || ((ax.max - ax.min) / 50) || 1;
                    if ( e.key === 'ArrowRight' || e.key === 'ArrowUp' ) { auto = false; vals[axIdx] = Math.min(ax.max, vals[axIdx] + stepK); apply(); if(HAS_SLIDER){syncSliders();} e.preventDefault(); }
                    else if ( e.key === 'ArrowLeft' || e.key === 'ArrowDown' ) { auto = false; vals[axIdx] = Math.max(ax.min, vals[axIdx] - stepK); apply(); if(HAS_SLIDER){syncSliders();} e.preventDefault(); }
                });
            }

            // ── Slider nativi ──
            if ( HAS_SLIDER ) {
                for ( var s = 0; s < ranges.length; s++ ) {
                    (function(input){
                        input.addEventListener('input', function(){
                            auto = false;
                            var ai = parseInt(input.getAttribute('data-axis'), 10);
                            if ( isNaN(ai) || !AXES[ai] ) { return; }
                            vals[ai] = parseFloat(input.value);
                            input.setAttribute('aria-valuenow', fmt(vals[ai]));
                            apply();
                            // aggiorna il valore mostrato accanto a QUESTO slider
                            for ( var v = 0; v < rovals.length; v++ ) {
                                if ( parseInt(rovals[v].getAttribute('data-val'), 10) === ai ) { rovals[v].textContent = fmt(vals[ai]); }
                            }
                        });
                    })(ranges[s]);
                }
            }

            // ── Loop a riposo (sinusoidale) ──
            function frame(){
                if ( ! running ) { return; }
                if ( auto ) {
                    t += 0.02 * (6 / AUTO_SPEED);
                    for ( var i = 0; i < AXES.length; i++ ) {
                        var a = AXES[i];
                        var mid = (a.min + a.max) / 2;
                        var half = (a.max - a.min) / 2;
                        // fasi leggermente diverse per asse → movimento organico
                        vals[i] = mid + Math.sin(t * (0.7 + i * 0.25) + i) * half;
                    }
                    apply();
                    if ( HAS_SLIDER ) { syncSliders(); }
                }
                rafId = requestAnimationFrame(frame);
            }
            function start(){ if ( ! running ) { running = true; rafId = requestAnimationFrame(frame); } }
            function stop(){ running = false; if ( rafId ) { cancelAnimationFrame(rafId); rafId = null; } }

            apply();   // assicura coerenza JS↔SSR all'avvio

            // Performance: gira solo quando in viewport (se non c'è loop, non avviamo rAF affatto)
            if ( auto ) {
                if ( 'IntersectionObserver' in window ) {
                    var io = new IntersectionObserver(function( entries ){
                        for ( var i = 0; i < entries.length; i++ ) {
                            if ( entries[i].isIntersecting ) { start(); } else { stop(); }
                        }
                    }, { threshold: 0 });
                    io.observe( root );
                } else {
                    start();
                }
            }
        })();
        </script>

        <?php
        // Border system (scoped sull'UID, come gli altri tile)
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) {
                echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_css() (integer-forced widths) for the internally generated uid
            }
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_hover_css()/build_border_effect_css() shared helpers
        }
        return ob_get_clean();
    }
}
