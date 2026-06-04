<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Tile Goo / Aurora — sfondo "metaball" decorativo per Section.
 * Bucket C / famiglia A (effetti guidati dal cursore). Scheda §4 GooBackground
 * (tema 61) + variante Aurora (temi 45/48, tile-mancanti §2).
 *
 * Due modalità su un solo tile:
 *   - goo    → N <div.blob> dentro un layer con filter:url(#goo-<UID>)
 *              (feGaussianBlur + feColorMatrix) che li FONDE come metaball; un
 *              blob extra insegue il cursore con easing. Rif. 61-tema-profumeria
 *              (blocco "goo blobs"): drift sinusoidale + cursor-follow.
 *   - aurora → stessi blob ma SENZA filtro goo: solo blur + blend, aloni morbidi
 *              che derivano (no fusione metaball). Rif. 45/48.
 *
 * Contratto §2:
 *  - Nessun valore hardcoded: numeri/colori = setting con default.
 *  - UID scoped per istanza: classe wrapper, classi blob/stage, @keyframes E
 *    l'id del filtro SVG (#goo-<UID>) sono TUTTI prefissati → N istanze sulla
 *    stessa pagina non si calpestano (l'id del filtro è il punto critico).
 *  - SSR: i blob sono già nel DOM con posizione/colore inline (sfondo visibile
 *    anche senza JS); il contenuto è sopra, sempre presente. Il layer è
 *    decorativo (aria-hidden).
 *  - Runtime idempotente (guard su dataset); gira solo nel viewport (IO).
 *  - reduced-motion → blob fermi (gradiente statico), nessun rAF.
 *  - cursor-blob off su (hover:none)/(pointer:coarse); fallback no-JS = SSR.
 *  - Additivo: chiavi salvate invariate; include il sistema bordi standard.
 */
class Olo_Goo_Tile extends Olo_Tile_Base {

    protected $type     = 'goo';
    protected $name     = 'Sfondo Goo / Aurora';
    protected $icon     = 'dashicons-art';
    protected $category = 'atmosphere';
    protected $defaults = [
        'scope'            => 'section',
        'mode'             => 'goo',

        'color_1'          => 'var(--olo-color-primary)',
        'color_2'          => 'var(--olo-color-secondary)',
        'color_3'          => 'var(--olo-color-accent)',
        'color_4'          => '',
        'color_5'          => '',

        'blob_count'       => 4,
        'blob_size_min'    => 180,
        'blob_size_max'    => 340,
        'drift_speed'      => 0.5,
        'goo_strength'     => 18,
        'follow_cursor'    => true,
        'cursor_blob_size' => 260,
        'aurora_blur'      => 60,
        'blend_mode'       => 'normal',
        'layer_opacity'    => 90,

        'content'          => '',

        'min_height'        => 480,
        'align_v'           => 'center',
        'align_h'           => 'center',
        'text_align'        => 'left',
        'content_max_width' => 720,
        'padding_y'         => 100,
        'base_color'        => '',
        'full_width'        => false,

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

    /** Palette token-first di default (usata se l'utente non imposta colori). */
    private function default_palette() {
        return [
            'var(--olo-color-primary)',
            'var(--olo-color-secondary)',
            'var(--olo-color-accent)',
        ];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-goo-' . wp_rand( 10000, 99999 );

        // ── Parametri (clampati) ──────────────────────────────────────────
        $mode    = ( $s['mode'] === 'aurora' ) ? 'aurora' : 'goo';
        // 'section' (default) = sfondo della sezione ospite; 'column' = sfondo della
        // colonna ospite (utile per card/box con goo per-cella, es. showcase landing).
        $scope   = ( ( $s['scope'] ?? 'section' ) === 'column' ) ? 'column' : 'section';
        // Builder: l'effetto "tutta la pagina" è un overlay a runtime, non rappresentabile
        // nella cella del tile → si mostra un box-anteprima coi blob, con altezza propria.
        $preview = ! empty( $s['_builder_mode'] ) && $scope === 'page';
        $count   = max( 3, min( 8, intval( $s['blob_count'] ) ) );
        $smin    = max( 40, min( 700, intval( $s['blob_size_min'] ) ) );
        $smax    = max( 60, min( 800, intval( $s['blob_size_max'] ) ) );
        if ( $smax < $smin ) { $tmp = $smin; $smin = $smax; $smax = $tmp; }
        $drift   = max( 0, min( 1, floatval( $s['drift_speed'] ) ) );
        $goo_k   = max( 8, min( 28, intval( $s['goo_strength'] ) ) );
        $follow  = ( $mode === 'goo' ) && ! empty( $s['follow_cursor'] );
        $cb_size = max( 80, min( 600, intval( $s['cursor_blob_size'] ) ) );
        $blur    = max( 0, min( 200, intval( $s['aurora_blur'] ) ) );
        $opacity = max( 10, min( 100, intval( $s['layer_opacity'] ) ) ) / 100;

        $blends  = [ 'normal', 'screen', 'lighten', 'overlay', 'soft-light' ];
        $blend   = ( $mode === 'aurora' && in_array( $s['blend_mode'], $blends, true ) ) ? $s['blend_mode'] : 'normal';

        // ── Palette: color picker non-vuoti, altrimenti palette token ──────
        $palette = [];
        foreach ( [ 'color_1', 'color_2', 'color_3', 'color_4', 'color_5' ] as $ck ) {
            $c = $this->safe_color_css( $s[ $ck ] ?? '' );
            if ( $c !== '' ) {
                $palette[] = $c;
            }
        }
        if ( empty( $palette ) ) {
            $palette = $this->default_palette();
        }
        $pal_n = count( $palette );

        // ── Layout sezione ────────────────────────────────────────────────
        $min_h   = max( 80,  intval( $s['min_height'] ) );
        $pad_y   = max( 0,   intval( $s['padding_y'] ) );
        $max_w   = max( 200, intval( $s['content_max_width'] ) );
        $align_v = in_array( $s['align_v'], [ 'flex-start', 'center', 'flex-end' ], true ) ? $s['align_v'] : 'center';
        $align_h = in_array( $s['align_h'], [ 'flex-start', 'center', 'flex-end' ], true ) ? $s['align_h'] : 'center';
        $text_al = in_array( $s['text_align'], [ 'left', 'center', 'right' ], true ) ? $s['text_align'] : 'left';
        $full    = ! empty( $s['full_width'] );
        $base    = $this->safe_color_css( $s['base_color'] ?? '' );

        // Shadow (preset/custom)
        $shadow_css = $this->build_shadow_decl( $s );

        // Contenuto: HTML rich-text editabile inline (sanitizzato). Sempre nel DOM (SSR).
        $content_html = $this->safe_richtext_content( $s['content'] ?? '' );

        // ── Blob: posizioni/dimensioni/colore generati SSR (deterministici per
        //    render: wp_rand). Inline su ogni blob così sono visibili senza JS.
        //    Il runtime poi sovrascrive SOLO transform (drift), non size/pos. ──
        $blobs = [];
        for ( $i = 0; $i < $count; $i++ ) {
            $size = wp_rand( $smin, $smax );
            // posizione in % (lascia margine ai bordi); il blob è centrato sul suo punto
            $left = wp_rand( 4, 96 );
            $top  = wp_rand( 6, 94 );
            $col  = $palette[ $i % $pal_n ];
            // parametri di drift (ampiezza px + fase), passati al runtime
            $ax   = wp_rand( 14, 40 );
            $ay   = wp_rand( 12, 36 );
            $ph   = wp_rand( 0, 628 ) / 100; // 0..2π
            $sp   = wp_rand( 6, 14 ) / 10000; // velocità di fase per ms
            $blobs[] = [
                'size' => $size, 'left' => $left, 'top' => $top, 'col' => $col,
                'ax' => $ax, 'ay' => $ay, 'ph' => $ph, 'sp' => $sp,
            ];
        }

        // Config runtime (JSON), scoped all'istanza.
        $cfg = [
            'scope'     => $scope,
            'mode'      => $mode,
            'drift'     => $drift,
            'follow'    => $follow,
            // blend del layer in modalità "tutta la pagina": usa quello scelto (aurora) o
            // "screen" di default (illumina senza coprire il contenuto opaco sottostante).
            'pageBlend' => ( $blend !== 'normal' ? $blend : 'screen' ),
        ];

        ob_start();
        ?>
        <style>
            /* Tile a ZERO dimensioni: non occupa spazio nel flusso.
               In anteprima builder (scope=page) diventa un box con altezza propria
               così i blob restano contenuti e l'effetto è leggibile come anteprima. */
            <?php if ( $preview ) : ?>
            .<?php echo $uid; ?> { display: block; position: relative; height: 220px; overflow: hidden; border-radius: 10px; }
            <?php else : ?>
            .<?php echo $uid; ?> { display: block; height: 0; line-height: 0; }
            <?php endif; ?>
            /* Lo stage (blob + filtro) viene spostato dal runtime come sfondo del contenitore.
               Selettori scoped sullo stage (non sul wrapper) così funzionano una volta spostato. */
            .olo-goo-stage-<?php echo $uid; ?> {
                position: absolute;
                inset: 0;
                z-index: -1;          /* dietro al contenuto del contenitore, sopra il suo sfondo */
                pointer-events: none;
                opacity: <?php echo $opacity; ?>;
                overflow: hidden;
                <?php if ( $mode === 'goo' ) : ?>
                filter: url(#goo-<?php echo $uid; ?>);
                <?php else : ?>
                filter: blur(<?php echo $blur; ?>px);
                <?php endif; ?>
            }
            .olo-goo-stage-<?php echo $uid; ?> .olo-goo-blob {
                position: absolute;
                border-radius: 50%;
                will-change: transform;
                <?php if ( $mode === 'aurora' && $blend !== 'normal' ) : ?>
                mix-blend-mode: <?php echo $blend; ?>;
                <?php endif; ?>
            }
            <?php if ( $follow ) : ?>
            .olo-goo-stage-<?php echo $uid; ?> .olo-goo-cursor {
                width: <?php echo $cb_size; ?>px;
                height: <?php echo $cb_size; ?>px;
                margin: <?php echo - intval( $cb_size / 2 ); ?>px 0 0 <?php echo - intval( $cb_size / 2 ); ?>px;
                left: 0;
                top: 0;
            }
            /* Su touch / senza hover il blob-cursore è nascosto */
            @media (hover: none), (pointer: coarse) {
                .olo-goo-stage-<?php echo $uid; ?> .olo-goo-cursor { display: none; }
            }
            <?php endif; ?>
        </style>

        <div class="olo-goo <?php echo esc_attr( $uid ); ?>" aria-hidden="true">
            <?php if ( $mode === 'goo' ) : ?>
            <svg class="olo-goo-defs" width="0" height="0" aria-hidden="true" focusable="false" style="position:absolute"><defs>
                <filter id="goo-<?php echo esc_attr( $uid ); ?>">
                    <feGaussianBlur in="SourceGraphic" stdDeviation="20" result="b"/>
                    <feColorMatrix in="b" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 <?php echo $goo_k; ?> -<?php echo intval( $goo_k / 2 ); ?>" result="g"/>
                    <feBlend in="SourceGraphic" in2="g"/>
                </filter>
            </defs></svg>
            <?php endif; ?>

            <div class="olo-goo-stage olo-goo-stage-<?php echo esc_attr( $uid ); ?>" aria-hidden="true">
                <?php
                foreach ( $blobs as $b ) {
                    printf(
                        '<div class="olo-goo-blob" style="width:%1$dpx;height:%1$dpx;left:%2$d%%;top:%3$d%%;background:%4$s;margin-left:%5$dpx;margin-top:%5$dpx" data-ax="%6$d" data-ay="%7$d" data-ph="%8$s" data-sp="%9$s"></div>',
                        $b['size'],
                        $b['left'],
                        $b['top'],
                        esc_attr( $b['col'] ),
                        - intval( $b['size'] / 2 ),
                        $b['ax'],
                        $b['ay'],
                        esc_attr( (string) $b['ph'] ),
                        esc_attr( (string) $b['sp'] )
                    );
                }
                if ( $follow ) {
                    printf(
                        '<div class="olo-goo-blob olo-goo-cursor" style="background:%s"></div>',
                        esc_attr( $palette[1 % $pal_n] )
                    );
                }
                ?>
            </div>
            <?php if ( $preview ) : ?>
            <div style="position:absolute;top:10px;left:10px;z-index:5;background:rgba(15,23,42,.72);color:#fff;font:600 11px/1 system-ui,sans-serif;padding:6px 10px;border-radius:7px;pointer-events:none;letter-spacing:.02em">&#8596; Tutta la pagina</div>
            <?php endif; ?>
        </div>

        <script>
        /* Goo / Aurora — runtime scoped per istanza (rif. 61-tema-profumeria, blocco "goo blobs"). */
        (function(){
            var wrap = document.querySelector('.<?php echo esc_js( $uid ); ?>');
            if ( ! wrap ) { return; }
            var stage = wrap.querySelector('.olo-goo-stage');
            if ( ! stage ) { return; }
            if ( stage.dataset.oloGoo ) { return; }   // idempotente: una init per istanza
            stage.dataset.oloGoo = '1';

            var CFG = <?php echo wp_json_encode( $cfg ); ?>;
            var pageMode = ( CFG.scope === 'page' );
            var FH = 0, SCROLL = 0, mClientX = 0, mClientY = 0;

            // Posizionamento. "page" = sfondo animato su tutta la finestra (overlay fisso,
            // davanti). "section" = sfondo del contenitore in cui è droppato (zero dimensioni).
            var host;
            if ( pageMode ) {
                // Overlay fisso DAVANTI al contenuto con blend (default "screen") + opacità
                // dello stage → velo atmosferico visibile su tutta la pagina. Mai cliccabile.
                var layer = document.createElement('div');
                layer.className = 'olo-goo-page';
                layer.style.cssText = 'position:fixed;inset:0;pointer-events:none;z-index:99988;overflow:hidden;mix-blend-mode:' + ( CFG.pageBlend || 'screen' );
                document.body.appendChild( layer );
                layer.appendChild( stage );
                host = layer;
                // Movimento relativo allo scroll: lo stage copre l'INTERA pagina (FH) e viene
                // traslato di -scrollY, così i blob scorrono COL documento invece di restare
                // incollati allo schermo. Il blend/overflow restano sul layer fisso.
                FH = Math.max( document.documentElement.scrollHeight || 0, window.innerHeight * 3 );
                stage.style.top = '0'; stage.style.bottom = 'auto'; stage.style.height = FH + 'px';
                SCROLL = window.scrollY || window.pageYOffset || 0;
                stage.style.transform = 'translateY(' + ( -SCROLL ) + 'px)';
                window.addEventListener('scroll', function(){
                    SCROLL = window.scrollY || window.pageYOffset || 0;
                    stage.style.transform = 'translateY(' + ( -SCROLL ) + 'px)';
                }, { passive: true });
                if ( 'ResizeObserver' in window ) {
                    new ResizeObserver(function(){
                        var nh = Math.max( document.documentElement.scrollHeight || 0, window.innerHeight * 3 );
                        if ( nh > FH ) { FH = nh; stage.style.height = FH + 'px'; }   // cresce con il documento (lazy-load)
                    }).observe( document.documentElement );
                }
            } else
            if ( CFG.scope === 'column' && ( host = wrap.closest('.olo-column, [class*="uk-width"]') ) ) {
                // scope=column: il goo è lo sfondo della COLONNA ospite (card/box).
                var ccs = getComputedStyle( host );
                if ( ccs.position === 'static' )  { host.style.position = 'relative'; }
                if ( ccs.overflow === 'visible' ) { host.style.overflow = 'hidden'; }
                host.style.isolation = 'isolate';
                host.insertBefore( stage, host.firstChild );
                if ( host.clientHeight < 40 ) { host.style.minHeight = '<?php echo intval( $min_h ); ?>px'; }
            } else
            if ( ( host = wrap.closest('section') || wrap.closest('.olo-section') ) ) {
                var hcs = getComputedStyle( host );
                if ( hcs.position === 'static' )  { host.style.position = 'relative'; }
                if ( hcs.overflow === 'visible' ) { host.style.overflow = 'hidden'; }
                host.style.isolation = 'isolate';   // confina lo stacking: lo stage (z-index:-1) resta sopra lo sfondo del contenitore e sotto il contenuto
                host.insertBefore( stage, host.firstChild );
                // Decoratore "da solo": se il contenitore non ha contenuto proprio, dagli
                // un'altezza minima così i blob restano visibili. Con contenuto reale è no-op.
                if ( host.clientHeight < 40 ) { host.style.minHeight = '<?php echo intval( $min_h ); ?>px'; }
            } else {
                host = wrap.parentElement;          // fallback estremo (struttura inattesa)
            }
            var root = host || wrap;   // coordinate/pointermove/IO riferiti al contenitore

            // prefers-reduced-motion → blob fermi (gradiente statico), nessun rAF.
            var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var coarse = window.matchMedia && window.matchMedia('(hover: none), (pointer: coarse)').matches;
            var canFollow = CFG.follow && ! coarse;
            if ( CFG.drift <= 0 && ! canFollow ) { return; }   // niente da animare

            // Blob "drift": leggono ampiezza/fase/velocità dai data-attr (SSR).
            var blobs = [].slice.call( stage.querySelectorAll('.olo-goo-blob:not(.olo-goo-cursor)') ).map(function(el){
                return {
                    el: el,
                    ax: parseFloat( el.dataset.ax ) || 0,
                    ay: parseFloat( el.dataset.ay ) || 0,
                    ph: parseFloat( el.dataset.ph ) || 0,
                    sp: parseFloat( el.dataset.sp ) || 0
                };
            });

            // Blob cursore (solo goo + hover): easing verso il puntatore.
            var cursor = canFollow ? stage.querySelector('.olo-goo-cursor') : null;
            var tx = 0, ty = 0, cx = 0, cy = 0, haveTarget = false;

            if ( cursor ) {
                // In page mode il layer è pointer-events:none → il pointermove va ascoltato su
                // window (coordinate viewport) e compensato con lo scroll (lo stage è traslato).
                ( pageMode ? window : root ).addEventListener('pointermove', function(e){
                    if ( pageMode ) {
                        mClientX = e.clientX; mClientY = e.clientY;
                        tx = mClientX; ty = mClientY + SCROLL;
                    } else {
                        var r = root.getBoundingClientRect();
                        tx = e.clientX - r.left; ty = e.clientY - r.top;
                    }
                    if ( ! haveTarget ) { cx = tx; cy = ty; haveTarget = true; }
                }, { passive: true });
            }

            var rafId = null, running = false;
            var DRIFT = CFG.drift;

            function frame( t ) {
                if ( ! running ) { return; }
                if ( DRIFT > 0 ) {
                    for ( var i = 0; i < blobs.length; i++ ) {
                        var b = blobs[i];
                        var p = b.ph + t * b.sp * DRIFT;
                        var dx = Math.cos( p ) * b.ax * DRIFT;
                        var dy = Math.sin( p * 1.3 ) * b.ay * DRIFT;
                        b.el.style.transform = 'translate(' + dx.toFixed(2) + 'px,' + dy.toFixed(2) + 'px)';
                    }
                }
                if ( cursor ) {
                    if ( pageMode ) { tx = mClientX; ty = mClientY + SCROLL; }   // segue il mouse anche mentre scrolli
                    cx += ( tx - cx ) * 0.12;
                    cy += ( ty - cy ) * 0.12;
                    cursor.style.transform = 'translate(' + cx.toFixed(2) + 'px,' + cy.toFixed(2) + 'px)';
                }
                rafId = requestAnimationFrame( frame );
            }

            function start() {
                if ( running ) { return; }
                running = true;
                if ( reduce ) {
                    // reduced-motion: posiziona il blob-cursore al centro una volta, niente loop.
                    if ( cursor ) {
                        var r = root.getBoundingClientRect();
                        cursor.style.transform = 'translate(' + ( r.width / 2 ) + 'px,' + ( r.height / 2 ) + 'px)';
                    }
                    running = false;
                    return;
                }
                rafId = requestAnimationFrame( frame );
            }
            function stop() {
                running = false;
                if ( rafId ) { cancelAnimationFrame( rafId ); rafId = null; }
            }

            // Performance: anima solo quando visibile. In "tutta la pagina" il layer è fisso e
            // sempre a schermo → avvia subito: l'IntersectionObserver su un elemento position:fixed
            // può non emettere la callback iniziale, lasciando il loop spento (niente drift/cursore).
            if ( pageMode ) {
                start();
            } else if ( 'IntersectionObserver' in window ) {
                var io = new IntersectionObserver(function( entries ){
                    for ( var i = 0; i < entries.length; i++ ) {
                        if ( entries[i].isIntersecting ) { start(); } else { stop(); }
                    }
                }, { threshold: 0.01 });
                io.observe( root );
            } else {
                start();
            }
        })();
        </script>
        <?php
        // ── Sistema bordi standard (come marquee/particlefx) ──────────────
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) {
                echo ".{$uid}{{$border_css}}";
            }
            echo $border_hover_css . $border_effect_css . '</style>';
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
            'sm' => '0 1px 3px rgba(0,0,0,0.12)',
            'md' => '0 4px 12px rgba(0,0,0,0.15)',
            'lg' => '0 12px 28px rgba(0,0,0,0.18)',
            'xl' => '0 24px 48px rgba(0,0,0,0.22)',
        ];
        return $map[ $preset ] ?? '';
    }
}
