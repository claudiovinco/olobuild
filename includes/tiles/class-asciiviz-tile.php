<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Tile ASCIIViz — visualizer audio in caratteri ASCII (famiglia C, bucket C).
 * Riferimento: handoff-tile-speciali/temi/67-tema-radio-notturna.html.
 *
 * SSR: stampa subito una griglia ASCII statica (onda a riposo) dentro un <pre>
 * aggiornato via textContent dal runtime (NO canvas → testo selezionabile/a11y).
 * Il <pre> è aria-hidden; titolo brano e stato sono testo reale.
 *
 * Runtime (IIFE inline, idempotente, multi-istanza):
 *   - simulated  → somma di sinusoidi + rumore (snippet del tema)
 *   - real-audio → AnalyserNode.getByteFrequencyData; fallback a simulated se
 *     Web Audio non disponibile o il file non parte.
 * prefers-reduced-motion → onda statica/lenta (nessun "ballo", niente drift).
 * IntersectionObserver spegne il rAF fuori viewport. Tutti gli id/keyframe/CSS
 * sono scoped sull'UID per-istanza.
 */
class Olobuild_Asciiviz_Tile extends Olobuild_Tile_Base {

    protected $type     = 'asciiviz';
    protected $name     = 'Visualizer ASCII';
    protected $icon     = 'dashicons-format-audio';
    protected $category = 'media';
    protected $defaults = [
        'show_player'     => true,
        'track_label'     => 'Ora in onda',
        'track_name'      => 'Velluto Blu — Måni',
        'audio_url'       => '',
        'autoplay'        => false,
        'show_progress'   => true,
        'show_listeners'  => true,
        'listeners_label' => 'in ascolto',
        'listeners_count' => 1204,
        'listeners_drift' => true,

        'react_to'        => 'simulated',
        'cols'            => 64,
        'rows'            => 12,
        'ramp'            => ' ·:-=+*o%#@',
        'char_top'        => '█',
        'idle_amplitude'  => 0.06,
        'react_speed'     => 1.6,

        'color'           => '',
        'bg_color'        => '',
        'glow'            => 8,
        'font_size'       => 13,
        'line_height'     => 1.02,
        'letter_spacing'  => 1,
        'radius'          => 18,
        'padding'         => 24,

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

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-asciiviz-' . wp_rand( 10000, 99999 );

        // ── Visualizer numerics (clamped) ──
        $cols   = max( 8,  min( 240, intval( $s['cols'] ) ) );
        $rows   = max( 2,  min( 64,  intval( $s['rows'] ) ) );
        $idle   = max( 0.0, min( 0.5, floatval( $s['idle_amplitude'] ) ) );
        $rspeed = max( 0.3, min( 4.0, floatval( $s['react_speed'] ) ) );
        $react  = ( $s['react_to'] === 'real-audio' ) ? 'real-audio' : 'simulated';

        // Ramp: stringa "dal vuoto al pieno". Sempre almeno 2 char (spazio + denso).
        $ramp = is_string( $s['ramp'] ) && $s['ramp'] !== '' ? $s['ramp'] : ' ·:-=+*o%#@';
        if ( function_exists( 'mb_strlen' ) && mb_strlen( $ramp ) < 2 ) {
            $ramp = ' ' . $ramp;
        }
        $char_top = is_string( $s['char_top'] ) && $s['char_top'] !== '' ? $s['char_top'] : '█';

        // ── Aspetto ──
        $color   = $this->safe_color_css( $s['color'] )    ?: 'var(--olo-color-primary, #FF9B3D)';
        $bg      = $this->safe_color_css( $s['bg_color'] ) ?: 'linear-gradient(180deg, var(--olo-color-bg-soft, #13100C), var(--olo-color-bg, #0B0907))';
        $glow    = max( 0, min( 30, intval( $s['glow'] ) ) );
        $fsize   = max( 6, min( 24, intval( $s['font_size'] ) ) );
        $lh      = max( 0.8, min( 1.6, floatval( $s['line_height'] ) ) );
        $ls      = max( 0.0, min( 6.0, floatval( $s['letter_spacing'] ) ) );
        // Dual-format: numero legacy ("18") E oggetto {tl,tr,br,bl} dal type 'border-radius'.
        $radius  = $this->build_border_radius_css( $s['radius'] ) ?: '0px';
        $pad     = max( 0, min( 80, intval( $s['padding'] ) ) );

        // ── Player ──
        $show_player    = ! empty( $s['show_player'] );
        $show_progress  = ! empty( $s['show_progress'] );
        $show_listeners = ! empty( $s['show_listeners'] );
        $list_drift     = ! empty( $s['listeners_drift'] );
        $list_base      = max( 0, intval( $s['listeners_count'] ) );
        $audio_url      = esc_url( $s['audio_url'] ?? '' );
        $autoplay       = ! empty( $s['autoplay'] ) && $audio_url !== '';

        // Texts
        $track_label = esc_html( (string) $s['track_label'] );
        $track_name  = esc_html( (string) $s['track_name'] );
        $list_label  = esc_html( (string) $s['listeners_label'] );

        // ── SSR: griglia ASCII statica (onda a riposo) già visibile ──
        // Ricostruisce lo stesso mapping del runtime per il primo paint (no-JS friendly).
        $ramp_chars = $this->mb_chars( $ramp );
        $rc_len     = max( 2, count( $ramp_chars ) );
        $static     = '';
        for ( $r = 0; $r < $rows; $r++ ) {
            $line = '';
            for ( $c = 0; $c < $cols; $c++ ) {
                $a    = $idle + sin( 0.5 + $c * 0.3 ) * 0.03;
                $a    = max( 0.0, $a );
                $h    = $a * $rows;
                $from = $rows - $r;
                if ( $h >= $from ) {
                    $line .= $char_top;
                } elseif ( $h > $from - 1 ) {
                    $f    = $h - ( $from - 1 );
                    $idx  = max( 1, (int) floor( $f * ( $rc_len - 1 ) ) );
                    $idx  = min( $rc_len - 1, $idx );
                    $line .= $ramp_chars[ $idx ];
                } else {
                    $line .= ' ';
                }
            }
            $static .= $line . "\n";
        }

        // Config JS (tutti gli id sono scoped sull'UID nel markup)
        $cfg = [
            'cols'     => $cols,
            'rows'     => $rows,
            'ramp'     => $ramp,
            'charTop'  => $char_top,
            'idle'     => $idle,
            'speed'    => $rspeed,
            'react'    => $react,
            'progress' => $show_progress,
            'drift'    => $list_drift,
            'listBase' => $list_base,
            'autoplay' => $autoplay,
        ];

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: colors via the safe_color_css() whitelist, integers/floats via intval()/floatval() with min()/max() clamps, radius via build_border_radius_css(); $uid is internally generated.
        ?>
        <style>
            .<?php echo $uid; ?>-wrap {
                border: 1px solid var(--olo-color-border, rgba(241,232,216,.12));
                border-radius: <?php echo $radius; ?>;
                background: <?php echo $bg; ?>;
                overflow: hidden;
            }
            .<?php echo $uid; ?>-viz {
                font-family: 'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
                font-size: <?php echo $fsize; ?>px;
                line-height: <?php echo $lh; ?>;
                letter-spacing: <?php echo $ls; ?>px;
                color: <?php echo $color; ?>;
                white-space: pre;
                margin: 0;
                padding: <?php echo $pad; ?>px <?php echo $pad; ?>px 8px;
                overflow: hidden;
                <?php if ( $glow > 0 ) : ?>
                text-shadow: 0 0 <?php echo $glow; ?>px <?php echo $color; ?>;
                <?php endif; ?>
            }
            <?php if ( $show_player ) : ?>
            .<?php echo $uid; ?>-bar {
                display: flex;
                align-items: center;
                gap: 20px;
                padding: 18px <?php echo max( 16, $pad ); ?>px;
                border-top: 1px solid var(--olo-color-border, rgba(241,232,216,.12));
                flex-wrap: wrap;
            }
            .<?php echo $uid; ?>-play {
                width: 58px; height: 58px;
                border-radius: 50%;
                background: <?php echo $color; ?>;
                border: none; cursor: pointer;
                color: var(--olo-color-bg, #0B0907);
                display: flex; align-items: center; justify-content: center;
                flex-shrink: 0;
                transition: transform .15s ease;
            }
            .<?php echo $uid; ?>-play:hover { transform: scale(1.06); }
            .<?php echo $uid; ?>-play:focus-visible { outline: 2px solid <?php echo $color; ?>; outline-offset: 3px; }
            .<?php echo $uid; ?>-play svg { width: 22px; height: 22px; }
            .<?php echo $uid; ?>-np { flex: 1; min-width: 180px; }
            .<?php echo $uid; ?>-np .lab {
                font-family: ui-monospace, monospace; font-size: 10px;
                letter-spacing: .16em; text-transform: uppercase;
                color: var(--olo-color-text-muted, #6A5E4C);
            }
            .<?php echo $uid; ?>-np .tr {
                font-weight: 800; font-size: 24px; line-height: 1.05; margin-top: 2px;
                color: var(--olo-color-text, #F1E8D8);
            }
            .<?php echo $uid; ?>-np .state {
                font-size: 13px; color: var(--olo-color-text-muted, #A99A82); margin-top: 2px;
            }
            <?php if ( $show_progress ) : ?>
            .<?php echo $uid; ?>-prog {
                height: 4px; background: rgba(127,127,127,.18);
                border-radius: 3px; margin-top: 10px; overflow: hidden;
            }
            .<?php echo $uid; ?>-prog i {
                display: block; height: 100%; width: 0;
                background: <?php echo $color; ?>;
            }
            <?php endif; ?>
            <?php if ( $show_listeners ) : ?>
            .<?php echo $uid; ?>-list {
                display: flex; align-items: center; gap: 9px;
                font-family: ui-monospace, monospace; font-size: 12px;
                color: var(--olo-color-text, #F1E8D8); white-space: nowrap;
            }
            .<?php echo $uid; ?>-list .dot {
                width: 9px; height: 9px; border-radius: 50%;
                background: <?php echo $color; ?>;
            }
            .<?php echo $uid; ?>-list b { color: <?php echo $color; ?>; }
            <?php endif; ?>
            <?php endif; ?>
            @media (prefers-reduced-motion: reduce) {
                .<?php echo $uid; ?>-play { transition: none; }
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <div class="<?php echo esc_attr( $uid ); ?>-wrap olo-asciiviz"
             data-olo-asciiviz='<?php echo esc_attr( wp_json_encode( $cfg ) ); ?>'>
            <pre class="<?php echo esc_attr( $uid ); ?>-viz" id="<?php echo esc_attr( $uid ); ?>-viz" aria-hidden="true"><?php echo esc_html( $static ); ?></pre>
            <?php if ( $audio_url !== '' ) : ?>
            <audio id="<?php echo esc_attr( $uid ); ?>-audio" src="<?php echo $audio_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped via esc_url() at assignment above; autoplay is a fixed literal from the ternary ?>" preload="none" crossorigin="anonymous" loop<?php echo $autoplay ? ' autoplay' : ''; ?>></audio>
            <?php endif; ?>
            <?php if ( $show_player ) : ?>
            <div class="<?php echo esc_attr( $uid ); ?>-bar">
                <button type="button" class="<?php echo esc_attr( $uid ); ?>-play" id="<?php echo esc_attr( $uid ); ?>-play"
                        aria-label="<?php echo esc_attr( olobuild_t( 'Riproduci / Pausa' ) ); ?>" aria-pressed="false">
                    <svg id="<?php echo esc_attr( $uid ); ?>-ic" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                </button>
                <div class="<?php echo esc_attr( $uid ); ?>-np">
                    <?php if ( $track_label !== '' ) : ?><div class="lab"><?php echo $track_label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped via esc_html() at assignment above ?></div><?php endif; ?>
                    <div class="tr"><?php echo $track_name; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped via esc_html() at assignment above ?></div>
                    <div class="state" id="<?php echo esc_attr( $uid ); ?>-state" role="status" aria-live="polite"><?php echo esc_html( olobuild_t( 'in pausa · premi play' ) ); ?></div>
                    <?php if ( $show_progress ) : ?>
                    <div class="<?php echo esc_attr( $uid ); ?>-prog" aria-hidden="true"><i id="<?php echo esc_attr( $uid ); ?>-prog"></i></div>
                    <?php endif; ?>
                </div>
                <?php if ( $show_listeners ) : ?>
                <div class="<?php echo esc_attr( $uid ); ?>-list">
                    <span class="dot" aria-hidden="true"></span>
                    <b id="<?php echo esc_attr( $uid ); ?>-lcount"><?php echo esc_html( number_format_i18n( $list_base ) ); ?></b>&nbsp;<?php echo $list_label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped via esc_html() at assignment above ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <script>
        /* ASCIIViz — runtime scoped per istanza (rif. 67-tema-radio-notturna.html) */
        (function(){
            var root = document.querySelector('.<?php echo esc_js( $uid ); ?>-wrap');
            if ( ! root ) { return; }
            if ( root.dataset.oloAsciivizInit ) { return; }   // idempotente: una init per istanza
            root.dataset.oloAsciivizInit = '1';

            var cfg;
            try { cfg = JSON.parse( root.getAttribute('data-olo-asciiviz') || '{}' ); }
            catch ( e ) { cfg = {}; }

            var viz   = document.getElementById('<?php echo esc_js( $uid ); ?>-viz');
            if ( ! viz ) { return; }
            var btn   = document.getElementById('<?php echo esc_js( $uid ); ?>-play');
            var ic    = document.getElementById('<?php echo esc_js( $uid ); ?>-ic');
            var state = document.getElementById('<?php echo esc_js( $uid ); ?>-state');
            var prog  = document.getElementById('<?php echo esc_js( $uid ); ?>-prog');
            var lc    = document.getElementById('<?php echo esc_js( $uid ); ?>-lcount');
            var audio = document.getElementById('<?php echo esc_js( $uid ); ?>-audio');

            var COLS    = cfg.cols    || 64;
            var ROWS    = cfg.rows    || 12;
            var RAMP    = (typeof cfg.ramp === 'string' && cfg.ramp.length >= 2) ? cfg.ramp : ' ·:-=+*o%#@';
            // Split unicode-safe (ramp può contenere caratteri multibyte)
            var RAMPCH  = Array.from(RAMP);
            var RCLEN   = Math.max(2, RAMPCH.length);
            var TOPCH   = (typeof cfg.charTop === 'string' && cfg.charTop) ? Array.from(cfg.charTop)[0] : '█';
            var IDLE    = (typeof cfg.idle === 'number') ? cfg.idle : 0.06;
            var SPEED   = (typeof cfg.speed === 'number') ? cfg.speed : 1.6;
            var REACT   = cfg.react === 'real-audio' ? 'real-audio' : 'simulated';
            var SHOWPRG = !!cfg.progress;
            var DRIFT   = !!cfg.drift;
            var LISTBAS = cfg.listBase || 0;

            var rm = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)');
            var REDUCED = !!( rm && rm.matches );

            var playing = false, t = 0, pct = 0;
            var amp = new Array(COLS); for ( var i = 0; i < COLS; i++ ) { amp[i] = 0; }

            // ── Web Audio (solo modalità real-audio) ──
            var actx = null, analyser = null, freq = null, srcNode = null, audioReady = false;
            function setupAudio(){
                if ( audioReady || REACT !== 'real-audio' || ! audio ) { return; }
                var AC = window.AudioContext || window.webkitAudioContext;
                if ( ! AC ) { return; }   // no Web Audio → resta sul ramo simulato
                try {
                    actx     = new AC();
                    analyser = actx.createAnalyser();
                    analyser.fftSize = 128;
                    freq     = new Uint8Array(analyser.frequencyBinCount);
                    srcNode  = actx.createMediaElementSource(audio);
                    srcNode.connect(analyser);
                    analyser.connect(actx.destination);
                    audioReady = true;
                } catch ( e ) { audioReady = false; analyser = null; }
            }

            function targetFor(c){
                // Ramo ridotto: onda lentissima e bassa, mai "ballo"
                if ( REDUCED ) { return IDLE + Math.sin(c * 0.3) * 0.03; }
                if ( playing ) {
                    if ( REACT === 'real-audio' && audioReady && analyser ) {
                        // mappa la colonna su un bin di frequenza
                        var bins = freq.length;
                        var bi   = Math.min(bins - 1, Math.floor(c / COLS * bins));
                        return freq[bi] / 255;
                    }
                    // simulato: somma di sinusoidi + rumore (snippet del tema)
                    var w  = Math.sin(t * SPEED + c * 0.28) * 0.5 + 0.5;
                    var w2 = Math.sin(t * (SPEED * 0.44) + c * 0.11) * 0.5 + 0.5;
                    return (w * 0.6 + w2 * 0.4) * (0.55 + Math.random() * 0.45);
                }
                // a riposo
                return IDLE + Math.sin(t * 0.5 + c * 0.3) * 0.03;
            }

            function render(){
                t += REDUCED ? 0.015 : 0.06;
                if ( REACT === 'real-audio' && audioReady && analyser && playing ) {
                    analyser.getByteFrequencyData(freq);
                }
                for ( var c = 0; c < COLS; c++ ) {
                    var target = targetFor(c);
                    if ( target < 0 ) { target = 0; }
                    amp[c] += (target - amp[c]) * 0.25;
                }
                var out = '';
                for ( var r = 0; r < ROWS; r++ ) {
                    var line = '';
                    for ( var c2 = 0; c2 < COLS; c2++ ) {
                        var h = amp[c2] * ROWS;
                        var fromBottom = ROWS - r;
                        if ( h >= fromBottom ) { line += TOPCH; }
                        else if ( h > fromBottom - 1 ) {
                            var f = h - (fromBottom - 1);
                            var idx = Math.max(1, Math.floor(f * (RCLEN - 1)));
                            if ( idx > RCLEN - 1 ) { idx = RCLEN - 1; }
                            line += RAMPCH[idx];
                        } else { line += ' '; }
                    }
                    out += line + '\n';
                }
                viz.textContent = out;
                if ( SHOWPRG && prog && playing && ! REDUCED ) {
                    pct = (pct + 0.04) % 100; prog.style.width = pct + '%';
                }
            }

            // ── rAF loop con gate IntersectionObserver ──
            var running = false, rafId = null, inView = true;
            function frame(){ if ( ! running ) { return; } render(); rafId = requestAnimationFrame(frame); }
            function start(){ if ( ! running && inView ) { running = true; rafId = requestAnimationFrame(frame); } }
            function stop(){ running = false; if ( rafId ) { cancelAnimationFrame(rafId); rafId = null; } }

            // Primo paint immediato (anche se l'IO non scatta subito / no-JS già coperto da SSR)
            render();

            if ( 'IntersectionObserver' in window ) {
                var io = new IntersectionObserver(function(entries){
                    for ( var k = 0; k < entries.length; k++ ) {
                        inView = entries[k].isIntersecting;
                        if ( inView ) { start(); } else { stop(); }
                    }
                }, { threshold: 0 });
                io.observe(root);
            } else {
                start();
            }

            // ── Play / pausa ──
            function setPlay(on){
                playing = on;
                if ( ic ) { ic.innerHTML = on ? '<path d="M6 5h4v14H6zM14 5h4v14h-4z"/>' : '<path d="M8 5v14l11-7z"/>'; }
                if ( btn ) { btn.setAttribute('aria-pressed', on ? 'true' : 'false'); }
                if ( state ) { state.textContent = on ? '▶ in diretta · streaming…' : 'in pausa · premi play'; }
            }
            if ( btn ) {
                btn.addEventListener('click', function(){
                    var next = ! playing;
                    if ( REACT === 'real-audio' && audio ) {
                        setupAudio();
                        if ( actx && actx.state === 'suspended' ) { actx.resume(); }
                        if ( next ) { var p = audio.play(); if ( p && p.catch ) { p.catch(function(){}); } }
                        else { audio.pause(); }
                    }
                    setPlay(next);
                });
            }
            // Sincronizza lo stato se l'audio reale parte/ferma da solo (es. autoplay riuscito)
            if ( REACT === 'real-audio' && audio ) {
                audio.addEventListener('play',  function(){ setupAudio(); if ( actx && actx.state === 'suspended' ) { actx.resume(); } setPlay(true); });
                audio.addEventListener('pause', function(){ setPlay(false); });
                if ( cfg.autoplay ) { setupAudio(); }
            }

            // ── Contatore ascoltatori (demo live) ──
            if ( DRIFT && lc && ! REDUCED ) {
                setInterval(function(){
                    var n = LISTBAS + Math.round((Math.random() - 0.4) * 60);
                    if ( n < 0 ) { n = 0; }
                    try { lc.textContent = n.toLocaleString('it-IT'); } catch ( e ) { lc.textContent = String(n); }
                }, 2800);
            }
        })();
        </script>
        <?php
        // ── Sistema bordi (condiviso) — scoped sull'UID del wrapper ──
        $sel               = ".{$uid}-wrap";
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( $sel, $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( $sel, $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo "{$sel}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized border settings; $sel comes from the internally generated uid
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base border helpers from sanitized border settings
        }
        return ob_get_clean();
    }

    /**
     * Split unicode-safe di una stringa in array di caratteri.
     * (preg_split con /u gestisce i char multibyte della rampa, es. ·.)
     */
    private function mb_chars( $str ) {
        $arr = preg_split( '//u', (string) $str, -1, PREG_SPLIT_NO_EMPTY );
        return is_array( $arr ) ? $arr : [];
    }
}
