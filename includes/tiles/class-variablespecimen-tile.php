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

class Olobuild_Variablespecimen_Tile extends Olobuild_Tile_Base {

    protected $type     = 'variablespecimen';
    protected $name     = 'Variazione testo';
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
        'font_size_tablet'     => '',
        'font_size_mobile'     => '',
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
        $interaction = in_array( $s['interaction'], [ 'drag', 'sliders', 'both', 'pressione' ], true ) ? $s['interaction'] : 'both';
        $auto        = ! empty( $s['auto_animate'] );
        $auto_speed  = max( 2, min( 16, intval( $s['auto_speed'] ) ) );
        $drag_hint   = esc_html( $s['drag_hint'] );
        // «Pressione» (15 nov 2026): ogni lettera si tende vicino al puntatore o al dito e torna con una molla;
        // a riposo la attraversa un'onda lenta. Niente lettura degli assi né suggerimento: la parola parla da sé.
        $pressione   = ( $interaction === 'pressione' );
        $show_read   = ! $pressione && ! empty( $s['show_readout'] );
        $has_drag    = ( $interaction === 'drag' || $interaction === 'both' );
        $has_slider  = ( $interaction === 'sliders' || $interaction === 'both' );
        if ( $pressione ) {
            $chars = function_exists( 'mb_str_split' ) ? mb_str_split( wp_strip_all_tags( $sample ) ) : str_split( wp_strip_all_tags( $sample ) );
            $sample_html = '';
            foreach ( $chars as $ch ) {
                if ( "\n" === $ch ) {
                    $sample_html .= '<br>';
                } elseif ( ' ' === $ch || "\r" === $ch ) {
                    $sample_html .= ' ';
                } else {
                    $sample_html .= '<span class="olo-vf-l" aria-hidden="true">' . esc_html( $ch ) . '</span>';
                }
            }
            $sample_html .= '<span class="olo-vf-sr">' . esc_html( wp_strip_all_tags( $sample ) ) . '</span>';
        }

        $axes = $this->normalize_axes( $s['axes'] ?? [] );

        // ── Stile ──────────────────────────────────────────────────
        // Il filtro di base accetta i token del campo font (var(--olo-font-…)) e gli
        // elenchi di famiglie. Prima il filtro locale toglieva le parentesi ai
        // var() e aggiungeva «, inherit» in coda: inherit e' una parola chiave che
        // non puo' stare in un elenco, il browser scartava la dichiarazione intera
        // e il font scelto non si applicava mai.
        $font_css = $this->resolve_font_family( $s['font_family'] ) ?: 'inherit';

        // Assi oltre il peso (larghezza, inclinazione, assi propri): il file servito di solito ha solo il peso, fermo
        // sul resto. Se il campo nomina una famiglia vera (non un ruolo o una variabile), si scarica la versione
        // variabile con gli intervalli degli assi della tile, sotto un nome proprio messo in testa all'elenco.
        $vf_face = '';
        $tag_extra = array_filter( $axes ?? [], function ( $a ) { return 'wght' !== $a['tag']; } );
        if ( $tag_extra && class_exists( 'Olobuild_Font_Host' ) ) {
            $prima = trim( explode( ',', (string) $s['font_family'] )[0], " '\"" );
            if ( '' !== $prima && false === strpos( $prima, 'var(' ) && ! in_array( strtolower( $prima ), [ 'body', 'sans', 'sans-serif', 'heading', 'serif', 'mono', 'inherit' ], true ) ) {
                $assi_req = [];
                foreach ( $axes as $a ) {
                    $assi_req[ $a['tag'] ] = [ $a['min'], $a['max'] ];
                }
                $alias   = $prima . ' VF';
                $vf_face = Olobuild_Font_Host::get_variable_css( $prima, $assi_req, $alias );
                if ( '' !== $vf_face ) {
                    $font_css = "'" . preg_replace( '/[^A-Za-z0-9 \-]/', '', $alias ) . "', " . $font_css;
                }
            }
        }

        // Il valore che si legge sotto la lettera e accanto ai cursori (il gemello JS è mostra()).
        $mostra = static function ( $a, $v ) {
            if ( ( $a['max'] - $a['min'] ) >= 20 ) {
                return (string) round( $v );
            }
            return rtrim( rtrim( number_format( $v, 1, '.', '' ), '0' ), '.' );
        };
        $text_color  = $this->safe_color_css( $s['text_color'] )  ?: 'var(--olo-color-text, #111827)';
        $accent      = $this->safe_color_css( $s['accent_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $bg_color    = $this->safe_color_css( $s['bg_color'] );
        $bg_decl     = $bg_color ? "background:{$bg_color};" : '';

        $font_size   = max( 24, min( 800, intval( $s['font_size'] ) ) );
        // La parola sta nella larghezza della TILE (container query): a 220 px «Calma» sul telefono si spezzava a metà
        // («Cal/ma»). Il tetto in cqi viene dalla riga più lunga (una lettera ≈ 0,62 em); le misure scelte per tablet e
        // telefono, più sotto, vincono comunque. 17 nov 2026.
        $righe_c     = preg_split( '/\R/u', (string) ( '' !== $s['sample_text'] ? $s['sample_text'] : 'Ga' ) );
        $lung_c      = max( 1, max( array_map( function ( $r ) { return function_exists( 'mb_strlen' ) ? mb_strlen( trim( $r ), 'UTF-8' ) : strlen( trim( $r ) ); }, $righe_c ) ) );
        $cqi_c       = round( 92 / ( $lung_c * 0.62 ), 1 );
        $fw_fallback = in_array( $s['font_weight_fallback'], [ '300','400','500','600','700','900' ], true ) ? $s['font_weight_fallback'] : '700';
        $text_align  = in_array( $s['text_align'], [ 'left','center','right' ], true ) ? $s['text_align'] : 'left';
        $pad_css     = Olobuild_Tile_Utils::sides_css( Olobuild_Tile_Utils::spacing_sides(
            $s['padding'] ?? null,
            [ 'y' => $s['padding_y'] ?? null ],
            [ 0, 0, 0, 0 ]
        ) );

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
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist colours, intval()-clamped sizes, in_array() whitelisted enums, a font stack validated by Olobuild_Tile_Base::resolve_font_family() and the internally generated $uid. ?>
        <style>
            <?php echo $vf_face; // @font-face self-hosted generate da Olobuild_Font_Host (solo URL locali) ?>
            .<?php echo $uid; ?> {
                container-type: inline-size;
                <?php echo $bg_decl; ?>
                padding: <?php echo esc_attr( $pad_css ); ?>;
                text-align: <?php echo $text_align; ?>;
            }
            .<?php echo $uid; ?> .olo-vf-stage {
                font-family: <?php echo $font_css; ?>;
                font-size: <?php echo (int) $font_size; ?>px;
                font-size: min(<?php echo (int) $font_size; ?>px, <?php echo esc_attr( $cqi_c ); ?>cqi);
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
            <?php if ( $pressione ) : ?>
            .<?php echo $uid; ?> .olo-vf-stage { touch-action: pan-y; cursor: default; letter-spacing: 0; white-space: nowrap; }
            .<?php echo $uid; ?> .olo-vf-l { display: inline-block; font-variation-settings: inherit; will-change: font-variation-settings; }
            .<?php echo $uid; ?> .olo-vf-sr { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; }
            <?php endif; ?>
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
                opacity: .9;
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
                opacity: .8; /* era .55: a 11px stava a 4:1 sui fondi chiari */
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
            <?php
            // Misura per tablet e telefono (selettore del dispositivo del campo Tipografia):
            // senza, la parola restava di 220 px anche sul telefono e si spezzava a metà.
            // Vuota = la misura del computer, com'era.
            echo $this->css_per_dispositivo( $s, 'font_size', '.' . $uid . ' .olo-vf-stage', function ( $v ) {
                return 'font-size:' . max( 24, min( 800, intval( $v ) ) ) . 'px';
            } );
            ?>
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
                    <span><?php echo esc_html( '' !== $a['label'] ? $a['label'] : $a['tag'] ); ?> <b data-ro="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $mostra( $a, $a['def'] ) ); ?></b></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if ( $has_drag ) : ?>
            <div class="olo-vf-hint"><?php echo $drag_hint; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped via esc_html() at assignment above ?></div>
            <?php endif; ?>

            <?php if ( $has_slider ) : ?>
            <div class="olo-vf-controls">
                <?php foreach ( $axes as $i => $a ) :
                    $val_disp = $mostra( $a, $a['def'] );
                    ?>
                    <div class="olo-vf-ctrl">
                        <label for="<?php echo esc_attr( $uid . '-ax-' . $i ); ?>">
                            <span><?php echo esc_html( '' !== $a['label'] ? $a['label'] : $a['tag'] ); ?></span>
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

            // ── Pressione: ogni lettera si tende verso il massimo degli assi vicino al puntatore, con una molla ──
            if ( <?php echo $pressione ? 'true' : 'false'; ?> ) {
                var L = Array.prototype.slice.call( stage.querySelectorAll('.olo-vf-l') );
                if ( ! L.length ) { return; }
                var st = L.map(function(){ return { v: 0, w: 0 }; });
                var px = null, py = null, last = 0, tw = 0, pr = null;
                function fmtP(n){ return String( Math.round( n * 10 ) / 10 ); }
                function fvs(v){
                    var parts = [];
                    for ( var a = 0; a < AXES.length; a++ ) {
                        var ax = AXES[a];
                        var x = ax.def + ( ax.max - ax.def ) * v;
                        parts.push('"' + ax.tag + '" ' + fmtP( Math.max( ax.min, Math.min( ax.max, x ) ) ) );
                    }
                    return parts.join(',');
                }
                function onMove(e){ px = e.clientX; py = e.clientY; last = performance.now(); }
                stage.addEventListener('pointermove', onMove, { passive: true });
                stage.addEventListener('pointerdown', onMove, { passive: true });
                stage.addEventListener('pointerleave', function(){ px = null; });
                stage.addEventListener('pointercancel', function(){ px = null; });
                // il dito che si stacca lascia andare la parola (col mouse resta tesa finché il puntatore è sopra)
                stage.addEventListener('pointerup', function(e){ if ( e.pointerType !== 'mouse' ) { px = null; } });
                var vivo = false, raf = null;
                function passo(){
                    if ( ! vivo ) { return; }
                    var r = stage.getBoundingClientRect();
                    var fs = parseFloat( getComputedStyle( stage ).fontSize ) || 100;
                    var R = Math.max( 60, fs * 1.15 );
                    var x = px, y = py;
                    if ( x === null && ! REDUCED && AUTO ) {
                        // a riposo un'onda attraversa la parola da un capo all'altro
                        tw += 0.006 * ( 6 / AUTO_SPEED );
                        x = r.left - R + ( ( Math.sin( tw * 2.2 ) + 1 ) / 2 ) * ( r.width + 2 * R );
                        y = r.top + r.height / 2;
                    } else if ( x !== null && ( y < r.top - R * 0.6 || y > r.bottom + R * 0.6 ) ) {
                        x = null;
                    }
                    var fermo = true;
                    for ( var i = 0; i < L.length; i++ ) {
                        var q = L[i].getBoundingClientRect();
                        var t = 0;
                        if ( x !== null ) {
                            var d = Math.abs( q.left + q.width / 2 - x );
                            t = d < R ? ( 1 + Math.cos( Math.PI * d / R ) ) / 2 : 0;
                        }
                        var s2 = st[i];
                        s2.w = ( s2.w + ( t - s2.v ) * 0.16 ) * 0.74;   // molla: un piccolo rimbalzo e torna
                        s2.v += s2.w;
                        if ( Math.abs( s2.w ) > 0.0005 || Math.abs( t - s2.v ) > 0.001 ) { fermo = false; }
                        L[i].style.fontVariationSettings = fvs( s2.v );
                    }
                    raf = requestAnimationFrame( passo );
                }
                function via(){ if ( ! vivo ) { vivo = true; raf = requestAnimationFrame( passo ); } }
                function ferma(){ vivo = false; if ( raf ) { cancelAnimationFrame( raf ); raf = null; } }
                if ( REDUCED ) {
                    // movimento ridotto: la parola resta ferma alla sua forma di partenza
                    L.forEach(function(el){ el.style.fontVariationSettings = fvs( 0 ); });
                    return;
                }
                if ( 'IntersectionObserver' in window ) {
                    new IntersectionObserver(function(en){ en.forEach(function(e){ if ( e.isIntersecting ) { via(); } else { ferma(); } }); }, { threshold: 0 }).observe( root );
                } else { via(); }
                return;
            }

            // stato corrente = default di ogni asse
            var vals = AXES.map(function(a){ return a.def; });

            var ros   = root.querySelectorAll('[data-ro]');     // readout (può essere assente)
            var rovals = root.querySelectorAll('[data-val]');   // valori accanto agli slider
            var ranges = root.querySelectorAll('.olo-vf-range');

            function fmt(n){
                var r = Math.round(n * 100) / 100;
                return ( Math.abs(r - Math.round(r)) < 0.001 ) ? String(Math.round(r)) : r.toFixed(2);
            }
            // Il valore che si LEGGE: intero sugli assi ampi (peso 300–1000), un decimale sui piccoli (inclinazione -15–0).
            // «904.65» diceva una precisione che nessuno vede.
            function mostra(i, n){
                var a = AXES[i];
                if ( ( a.max - a.min ) >= 20 ) { return String( Math.round(n) ); }
                return String( Math.round( n * 10 ) / 10 );
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
                    if ( !isNaN(ri) && AXES[ri] ) { ros[r].textContent = mostra(ri, vals[ri]); }
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
                    if ( !isNaN(vi) && AXES[vi] ) { rovals[v].textContent = mostra(vi, vals[vi]); }
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
                    // Mentre si scrive la parola nel canvas del builder il clic resta del testo.
                    if ( stage.isContentEditable ) { return; }
                    // Trascinando col mouse si selezionava il testo (un riquadro azzurro sulla parola): niente selezione.
                    e.preventDefault();
                    stage.style.userSelect = 'none';
                    if ( window.getSelection ) { window.getSelection().removeAllRanges(); }
                    dragging = true; auto = false;
                    try { stage.setPointerCapture(e.pointerId); } catch(err){}
                    fromEvent(e);
                });
                stage.addEventListener('pointerup', function(e){ dragging = false; stage.style.userSelect = ''; try { stage.releasePointerCapture(e.pointerId); } catch(err){} });
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
                                if ( parseInt(rovals[v].getAttribute('data-val'), 10) === ai ) { rovals[v].textContent = mostra(ai, vals[ai]); }
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
                echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() (integer-forced widths) for the internally generated uid
            }
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() shared helpers
        }
        return ob_get_clean();
    }
}
