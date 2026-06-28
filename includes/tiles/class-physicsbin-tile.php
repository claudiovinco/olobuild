<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Tile PhysicsBin — giocattoli trascinabili con gravità, rimbalzo e collisioni.
 *
 * Famiglia C / bucket C — rif. handoff-tile-speciali/temi/69-tema-toy-store.html
 * (blocco "tiny physics bin"). Il runtime è uno snippet portato 1:1 dal tema e reso
 * multi-istanza, idempotente, con IntersectionObserver, prefers-reduced-motion e
 * disattivazione del puntatore su (hover:none)/(pointer:coarse).
 *
 * Contratto §2:
 *  - Ogni numero/colore = campo editor con default (vedi $defaults).
 *  - UID per-istanza 'olo-physicsbin-'.wp_rand(): CSS e selettore runtime tutti prefissati.
 *  - SSR: il PHP dispone gli oggetti staticamente (decorativo, aria-hidden) → già visibile.
 *  - Runtime INLINE nel render (IIFE con guard su dataset → idempotente, N istanze ok).
 *  - DOM + transform (no canvas): adatto a ~10–20 corpi. Cap a max_items.
 */
class Olobuild_Physicsbin_Tile extends Olobuild_Tile_Base {

    protected $type     = 'physicsbin';
    protected $name     = 'Cesto Fisico';
    protected $icon     = 'dashicons-games';
    protected $category = 'interactive';
    protected $defaults = [
        'preset' => 'custom',
        'items'  => [
            [ 'shape' => 'circle', 'color' => '#E63E3E', 'radius' => '46', 'glyph' => '★', 'image' => '' ],
            [ 'shape' => 'square', 'color' => '#2E6BE6', 'radius' => '40', 'glyph' => 'A', 'image' => '' ],
            [ 'shape' => 'circle', 'color' => '#F4B400', 'radius' => '34', 'glyph' => '',  'image' => '' ],
            [ 'shape' => 'circle', 'color' => '#2BA65A', 'radius' => '50', 'glyph' => 'B', 'image' => '' ],
            [ 'shape' => 'square', 'color' => '#8B53D6', 'radius' => '36', 'glyph' => 'C', 'image' => '' ],
            [ 'shape' => 'circle', 'color' => '#E63E3E', 'radius' => '32', 'glyph' => '',  'image' => '' ],
            [ 'shape' => 'circle', 'color' => '#2E6BE6', 'radius' => '44', 'glyph' => '1', 'image' => '' ],
            [ 'shape' => 'star',   'color' => '#F4B400', 'radius' => '42', 'glyph' => '',  'image' => '' ],
            [ 'shape' => 'square', 'color' => '#2BA65A', 'radius' => '30', 'glyph' => '2', 'image' => '' ],
        ],
        'gravity'     => '0.55',
        'restitution' => '0.74',
        'friction'    => '0.992',
        'walls'       => true,
        'spawn'       => 'random',
        'max_items'   => '14',

        'bg_color'      => '',
        'height'        => '480',
        'border_radius' => [ 'tl' => 28, 'tr' => 28, 'br' => 28, 'bl' => 28 ],
        'shadow'        => 'lg',

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
        $uid = 'olo-physicsbin-' . wp_rand( 10000, 99999 );

        // ── Parametri fisici (clampati, dal campo editor) ──────────────────
        $gravity     = max( 0,   min( 2,    floatval( $s['gravity'] ) ) );
        $restitution = max( 0,   min( 1,    floatval( $s['restitution'] ) ) );
        $friction    = max( 0.9, min( 1,    floatval( $s['friction'] ) ) );
        $walls       = ! empty( $s['walls'] );
        $spawn       = $s['spawn'] === 'grid' ? 'grid' : 'random';
        $max_items   = max( 1,   min( 50,   intval( $s['max_items'] ) ) );

        // ── Aspetto cesto ──────────────────────────────────────────────────
        $height = max( 160, min( 1000, intval( $s['height'] ) ) );
        $radius = $this->build_border_radius_css( $s['border_radius'] ?? 0 );
        $bg     = $this->safe_color_css( $s['bg_color'] );

        // ── Oggetti (cap a max_items) ──────────────────────────────────────
        $items_raw = is_array( $s['items'] ) ? $s['items'] : [];
        $items = [];
        foreach ( $items_raw as $it ) {
            if ( ! is_array( $it ) ) continue;
            $shape = in_array( ( $it['shape'] ?? 'circle' ), [ 'circle', 'square', 'star' ], true ) ? $it['shape'] : 'circle';
            $r     = max( 14, min( 90, intval( $it['radius'] ?? 40 ) ) );
            $color = $this->safe_color_css( $it['color'] ?? '' ) ?: 'var(--olo-color-primary, #e1474f)';
            $glyph = mb_substr( wp_strip_all_tags( (string) ( $it['glyph'] ?? '' ) ), 0, 2 );
            $img   = esc_url( $it['image'] ?? '' );
            $items[] = compact( 'shape', 'r', 'color', 'glyph', 'img' );
            if ( count( $items ) >= $max_items ) break;
        }
        $count = count( $items );

        // Layout statico (SSR / no-JS / reduced-motion): disposizione deterministica
        // che riempie il cesto "appoggiando" gli oggetti dal basso. Niente Math.random
        // server-side (un valore stabile per evitare layout shift tra render/SSR).
        $cols = max( 1, (int) ceil( sqrt( max( 1, $count ) ) ) );
        $rows = max( 1, (int) ceil( $count / $cols ) );

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: $height via intval()+min()/max() clamps, $bg via the safe_color_css() whitelist, $radius via build_border_radius_css() (integer-forced); $uid is internally generated.
        ?>
        <style>
            .<?php echo $uid; ?> {
                position: relative;
                width: 100%;
                height: <?php echo $height; ?>px;
                overflow: hidden;
                touch-action: none;
                <?php if ( $bg ) : ?>
                background: <?php echo $bg; ?>;
                <?php else : ?>
                background: repeating-linear-gradient(135deg, var(--olo-color-surface-2, #F6E9CE) 0 22px, var(--olo-color-surface, #F1E2C2) 22px 44px);
                <?php endif; ?>
                <?php if ( $radius ) : ?>border-radius: <?php echo $radius; ?>;<?php endif; ?>
                box-shadow: inset 0 -10px 0 rgba(0,0,0,.06);
            }
            .<?php echo $uid; ?> .olo-pb-lab {
                position: absolute;
                top: 16px; left: 18px;
                z-index: 5;
                font-size: 10.5px;
                letter-spacing: .1em;
                text-transform: uppercase;
                color: var(--olo-color-text-muted, #6E6150);
                pointer-events: none;
                background: rgba(255,255,255,.65);
                padding: 5px 10px;
                border-radius: 100px;
            }
            .<?php echo $uid; ?> .olo-pb-toy {
                position: absolute;
                top: 0; left: 0;
                display: grid;
                place-items: center;
                border-radius: 50%;
                cursor: grab;
                will-change: transform;
                user-select: none;
                -webkit-user-select: none;
                overflow: hidden;
                font-weight: 700;
                color: #fff;
                box-shadow: inset -6px -8px 0 rgba(0,0,0,.14), 0 6px 14px -6px rgba(0,0,0,.4);
            }
            .<?php echo $uid; ?> .olo-pb-toy:active { cursor: grabbing; }
            .<?php echo $uid; ?> .olo-pb-toy:focus-visible {
                outline: 3px solid var(--olo-color-primary, #e1474f);
                outline-offset: 2px;
            }
            .<?php echo $uid; ?> .olo-pb-toy.sq   { border-radius: 22%; }
            .<?php echo $uid; ?> .olo-pb-toy.star {
                clip-path: polygon(50% 0,61% 35%,98% 35%,68% 57%,79% 91%,50% 70%,21% 91%,32% 57%,2% 35%,39% 35%);
                box-shadow: none;
            }
            .<?php echo $uid; ?> .olo-pb-toy img {
                width: 100%; height: 100%;
                object-fit: cover;
                pointer-events: none;
                -webkit-user-drag: none;
            }
            /* prefers-reduced-motion → niente loop fisico: oggetti fermi, cursore neutro */
            @media (prefers-reduced-motion: reduce) {
                .<?php echo $uid; ?> .olo-pb-toy { cursor: default; transition: none; }
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <div class="olo-physicsbin <?php echo esc_attr( $uid ); ?>" data-olo-pb
             role="img" aria-label="<?php echo esc_attr__( 'Cesto interattivo: trascina e lancia i giocattoli', 'olobuild' ); ?>">
            <span class="olo-pb-lab" aria-hidden="true">&#8623; <?php echo esc_html__( 'trascina & lancia', 'olobuild' ); ?></span>
            <?php
            foreach ( $items as $i => $it ) {
                $d   = $it['r'] * 2;
                $cls = 'olo-pb-toy';
                if ( $it['shape'] === 'square' ) $cls .= ' sq';
                if ( $it['shape'] === 'star' )   $cls .= ' star';

                // Posizione statica (percentuale) per SSR / no-JS — "scaffale" dal basso.
                $col = $i % $cols;
                $row = (int) floor( $i / $cols );
                $px  = $cols > 1 ? ( 12 + ( $col / max( 1, $cols - 1 ) ) * 76 ) : 50; // 12%..88%
                $py  = $rows > 1 ? ( 88 - ( $row / max( 1, $rows - 1 ) ) * 60 ) : 78; // basso → alto

                $style = 'width:' . $d . 'px;height:' . $d . 'px;font-size:' . round( $it['r'] * 0.8 ) . 'px;'
                       . 'left:calc(' . $px . '% - ' . $it['r'] . 'px);top:calc(' . $py . '% - ' . $it['r'] . 'px);';
                if ( ! $it['img'] ) {
                    $style .= 'background:' . $it['color'] . ';';
                }

                echo '<div class="' . esc_attr( $cls ) . '" tabindex="0" aria-hidden="true"'
                   . ' style="' . esc_attr( $style ) . '"'
                   . ' data-r="' . esc_attr( $it['r'] ) . '"'
                   . ' data-shape="' . esc_attr( $it['shape'] ) . '">';
                if ( $it['img'] ) {
                    echo '<img src="' . $it['img'] . '" alt="" loading="lazy" />'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $it['img'] escaped via esc_url() when building $items above
                } elseif ( $it['glyph'] !== '' ) {
                    echo esc_html( $it['glyph'] );
                }
                echo '</div>';
            }
            ?>
        </div>

        <?php if ( $count > 0 ) : ?>
        <script>
        /* PhysicsBin — runtime scoped per istanza (rif. 69-tema-toy-store.html "tiny physics bin").
           Drag/throw via Pointer Events, gravità, integrazione posizione, collisioni coppia-coppia
           con risposta impulsiva. Idempotente, multi-istanza, IO per spegnere il loop fuori viewport. */
        (function(){
            var bin = document.querySelector('.<?php echo esc_js( $uid ); ?>');
            if ( ! bin ) { return; }
            if ( bin.dataset.oloPbInit ) { return; }   // idempotente: una sola init per istanza
            bin.dataset.oloPbInit = '1';

            // prefers-reduced-motion → nessun loop: resta la disposizione statica SSR
            var rm = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)');
            if ( rm && rm.matches ) { return; }

            // Effetti puntatore disattivati su touch/coarse: niente drag, resta statico ma leggibile
            var coarse = window.matchMedia && (window.matchMedia('(hover:none)').matches || window.matchMedia('(pointer:coarse)').matches);

            var G    = <?php echo json_encode( $gravity ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- float clamped via floatval()+min()/max() above, JSON-encoded ?>;
            var REST = <?php echo json_encode( $restitution ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- float clamped via floatval()+min()/max() above, JSON-encoded ?>;
            var FR   = <?php echo json_encode( $friction ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- float clamped via floatval()+min()/max() above, JSON-encoded ?>;
            var WALLS = <?php echo json_encode( $walls ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- boolean from !empty(), JSON-encoded ?>;
            var SPAWN = <?php echo json_encode( $spawn ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed 'grid'/'random' literal from the ternary above, JSON-encoded ?>;

            var W = 0, H = 0, toys = [];
            function size(){ W = bin.clientWidth; H = bin.clientHeight; }
            size();
            window.addEventListener('resize', size, { passive: true });

            // Costruisce lo stato fisico dai .toy già renderizzati (SSR) — niente nodi nuovi.
            var els = bin.querySelectorAll('.olo-pb-toy');
            var n = els.length;
            var gcols = Math.max(1, Math.ceil(Math.sqrt(n)));
            for ( var i = 0; i < n; i++ ) {
                var el = els[i];
                var r  = parseFloat( el.getAttribute('data-r') ) || 30;
                var x, y;
                if ( SPAWN === 'grid' ) {
                    var col = i % gcols, rw = Math.floor( i / gcols );
                    x = ( W / (gcols + 1) ) * ( col + 1 );
                    y = 40 + rw * ( r * 2 + 10 );
                } else {
                    x = r + Math.random() * Math.max( 1, W - r * 2 );
                    y = 30 + Math.random() * 120;
                }
                toys.push({
                    el: el, r: r,
                    x: x, y: y,
                    vx: (Math.random() - 0.5) * 6, vy: 0,
                    drag: false
                });
            }

            // Drag & throw — la velocità di rilascio diventa impulso
            var dragT = null, px = 0, py = 0, lpx = 0, lpy = 0;
            function at(e){ var rb = bin.getBoundingClientRect(); return { x: e.clientX - rb.left, y: e.clientY - rb.top }; }

            if ( ! coarse ) {
                bin.addEventListener('pointerdown', function(e){
                    var p = at(e);
                    for ( var i = toys.length - 1; i >= 0; i-- ) {
                        var t = toys[i];
                        if ( Math.hypot( p.x - t.x, p.y - t.y ) < t.r ) {
                            dragT = t; t.drag = true;
                            px = lpx = p.x; py = lpy = p.y;
                            try { bin.setPointerCapture( e.pointerId ); } catch ( err ) {}
                            break;
                        }
                    }
                });
                bin.addEventListener('pointermove', function(e){
                    if ( ! dragT ) { return; }
                    var p = at(e);
                    lpx = px; lpy = py; px = p.x; py = p.y;
                    dragT.x = p.x; dragT.y = p.y;
                });
                var release = function(){
                    if ( dragT ) {
                        dragT.vx = (px - lpx) * 1.4;
                        dragT.vy = (py - lpy) * 1.4;
                        dragT.drag = false; dragT = null;
                    }
                };
                bin.addEventListener('pointerup', release);
                bin.addEventListener('pointercancel', release);
            }

            var running = false, rafId = null;
            function step(){
                if ( ! running ) { return; }
                for ( var i = 0; i < toys.length; i++ ) {
                    var t = toys[i];
                    if ( ! t.drag ) {
                        t.vy += G; t.x += t.vx; t.y += t.vy; t.vx *= FR;
                        if ( WALLS ) {
                            if ( t.x < t.r )       { t.x = t.r;       t.vx = -t.vx * REST; }
                            if ( t.x > W - t.r )   { t.x = W - t.r;   t.vx = -t.vx * REST; }
                            if ( t.y > H - t.r )   { t.y = H - t.r;   t.vy = -t.vy * REST; t.vx *= 0.96; }
                            if ( t.y < t.r )       { t.y = t.r;       t.vy = -t.vy * REST; }
                        } else {
                            // senza pareti: solo il fondo trattiene (altrimenti spariscono)
                            if ( t.y > H - t.r )   { t.y = H - t.r;   t.vy = -t.vy * REST; t.vx *= 0.96; }
                        }
                    }
                }
                /* collisioni coppia-coppia con risposta impulsiva */
                for ( var a = 0; a < toys.length; a++ ) {
                    for ( var b = a + 1; b < toys.length; b++ ) {
                        var A = toys[a], B = toys[b];
                        var dx = B.x - A.x, dy = B.y - A.y;
                        var d = Math.hypot( dx, dy ) || 0.01, min = A.r + B.r;
                        if ( d < min ) {
                            var nx = dx / d, ny = dy / d, overlap = ( min - d ) / 2;
                            if ( ! A.drag ) { A.x -= nx * overlap; A.y -= ny * overlap; }
                            if ( ! B.drag ) { B.x += nx * overlap; B.y += ny * overlap; }
                            var rvx = B.vx - A.vx, rvy = B.vy - A.vy, vn = rvx * nx + rvy * ny;
                            if ( vn < 0 ) {
                                var imp = -(1 + REST) * vn / 2;
                                if ( ! A.drag ) { A.vx -= imp * nx; A.vy -= imp * ny; }
                                if ( ! B.drag ) { B.vx += imp * nx; B.vy += imp * ny; }
                            }
                        }
                    }
                }
                for ( var k = 0; k < toys.length; k++ ) {
                    var tt = toys[k];
                    tt.el.style.transform = 'translate(' + ( tt.x - tt.r ) + 'px,' + ( tt.y - tt.r ) + 'px) rotate(' + ( tt.x * 0.7 ) + 'deg)';
                    // neutralizza il posizionamento statico SSR ora che il JS guida via transform
                    tt.el.style.left = '0'; tt.el.style.top = '0';
                }
                rafId = requestAnimationFrame( step );
            }
            function start(){ if ( ! running ) { running = true; rafId = requestAnimationFrame( step ); } }
            function stop(){ running = false; if ( rafId ) { cancelAnimationFrame( rafId ); rafId = null; } }

            // Performance: il loop gira solo quando il cesto è nel viewport
            if ( 'IntersectionObserver' in window ) {
                var io = new IntersectionObserver(function( entries ){
                    for ( var i = 0; i < entries.length; i++ ) {
                        if ( entries[i].isIntersecting ) { start(); } else { stop(); }
                    }
                }, { threshold: 0 });
                io.observe( bin );
            } else {
                start();
            }
        })();
        </script>
        <?php endif; ?>

        <?php
        // Border system (come marquee): base + hover + effetti, scoped sull'UID.
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
