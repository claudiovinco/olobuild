<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Tile ParticleFX — sistema di particelle a tema (preset) su <canvas> a tutta
 * sezione, dietro al contenuto. Bucket C / famiglia C (canvas / generativo).
 *
 * Preset: petals (36), snow (42), bubbles (50), stars/costellazioni (49),
 * confetti (one-shot, rif. Konami tema 60). Gli snippet runtime dei temi sono
 * portati nell'IIFE inline del render() — multi-istanza, idempotente, con
 * IntersectionObserver, dpr cap e ramo prefers-reduced-motion.
 *
 * Contratto §2:
 *  - Nessun valore hardcoded: numeri/colori = setting con default.
 *  - UID scoped per istanza: classe wrapper, classe canvas e CSS prefissati.
 *  - SSR: il contenuto è già nel DOM e visibile; il canvas è decorativo (aria-hidden).
 *  - Runtime idempotente con guard su dataset; gira solo nel viewport (IO).
 *  - reduced-motion → canvas disegnato fermo (1 frame) o non avviato.
 *  - Pointer off su (hover:none)/(pointer:coarse).
 *  - Additivo: chiavi salvate invariate; include il sistema bordi standard.
 */
class Olobuild_Particlefx_Tile extends Olobuild_Tile_Base {

    protected $type     = 'particlefx';
    protected $name     = 'Particelle (ParticleFX)';
    protected $icon     = 'dashicons-art';
    protected $category = 'atmosphere';
    protected $defaults = [
        'scope'             => 'section',
        'preset'            => 'petals',
        'count'             => 40,
        'speed'             => 1,
        'size'              => 6,
        'wind'              => 0.5,
        'gravity'           => 1,
        'connect_lines'     => false,
        'connect_distance'  => 90,
        'interact_on_hover' => false,

        'palette_1'         => '',
        'palette_2'         => '',
        'palette_3'         => '',
        'palette_4'         => '',
        'palette_5'         => '',
        'particle_opacity'  => 80,

        'content'           => '',

        'min_height'        => 420,
        'align_v'           => 'center',
        'align_h'           => 'center',
        'text_align'        => 'center',
        'content_max_width' => 720,
        'padding_y'         => 80,
        'bg_color'          => '',
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

    /** Palette di default per preset (usata se l'utente non imposta colori). */
    private function preset_palette( $preset ) {
        $map = [
            'petals'   => [ '#E8C4B8', '#D9A593', '#F0D9CE', '#C98B7A', '#EBD3B0' ],
            'snow'     => [ '#FFFFFF', '#EAF2FB', '#D7E6F5' ],
            'bubbles'  => [ '#BDEBEE', '#A7D8E8', '#E6FBFF' ],
            'stars'    => [ '#EDEFFB', '#F5C24B', '#9B6BFF', '#5BD6E0' ],
            'confetti' => [ '#e1474f', '#F5C24B', '#5BD6E0', '#9B6BFF', '#7BD88F' ],
            'soccer'   => [ '#FFFFFF', '#15241D' ],
        ];
        return $map[ $preset ] ?? $map['petals'];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-particlefx-' . wp_rand( 10000, 99999 );

        // ── Parametri sistema (clampati) ──────────────────────────────────
        $presets = [ 'petals', 'snow', 'bubbles', 'stars', 'confetti', 'soccer' ];
        $preset  = in_array( $s['preset'], $presets, true ) ? $s['preset'] : 'petals';
        // 'section' (default) = sfondo della sezione ospite; 'page' = overlay fisso
        // su tutto il documento (il runtime ha già il ramo dedicato: canvas fixed,
        // campo alto quanto la pagina, pointer-events none).
        $scope   = ( ( $s['scope'] ?? 'section' ) === 'page' ) ? 'page' : 'section';

        $count    = max( 1,   min( 400,  intval( $s['count'] ) ) );
        $speed    = max( 0.1, min( 6,     floatval( $s['speed'] ) ) );
        $size     = max( 1,   min( 40,    floatval( $s['size'] ) ) );
        $wind     = max( 0,   min( 6,     floatval( $s['wind'] ) ) );
        $gravity  = max( 0,   min( 6,     floatval( $s['gravity'] ) ) );
        $connect  = ! empty( $s['connect_lines'] );
        $conn_d   = max( 20,  min( 300,   intval( $s['connect_distance'] ) ) );
        $hover    = ! empty( $s['interact_on_hover'] );
        $opacity  = max( 5,   min( 100,   intval( $s['particle_opacity'] ) ) ) / 100;

        // ── Palette: color picker non-vuoti, altrimenti palette del preset ──
        $palette = [];
        foreach ( [ 'palette_1', 'palette_2', 'palette_3', 'palette_4', 'palette_5' ] as $pk ) {
            $c = $this->safe_color_css( $s[ $pk ] ?? '' );
            if ( $c !== '' ) {
                $palette[] = $c;
            }
        }
        if ( empty( $palette ) ) {
            $palette = $this->preset_palette( $preset );
        }

        // ── Layout sezione ────────────────────────────────────────────────
        $min_h     = max( 80,  intval( $s['min_height'] ) );
        $pad_y     = max( 0,   intval( $s['padding_y'] ) );
        $max_w     = max( 200, intval( $s['content_max_width'] ) );
        $align_v   = in_array( $s['align_v'], [ 'flex-start', 'center', 'flex-end' ], true ) ? $s['align_v'] : 'center';
        $align_h   = in_array( $s['align_h'], [ 'flex-start', 'center', 'flex-end' ], true ) ? $s['align_h'] : 'center';
        $text_al   = in_array( $s['text_align'], [ 'left', 'center', 'right' ], true ) ? $s['text_align'] : 'center';
        $full      = ! empty( $s['full_width'] );

        // Bg: oggetto "bg" (Sfondo creativo) o bg_color semplice; default trasparente.
        $bg_obj  = $s['bg'] ?? null;
        $bg_decl = '';
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olobuild_CSS_Builder' ) ) {
            $bg_decl = ( new Olobuild_CSS_Builder() )->get_bg_inline_css( $bg_obj );
        }
        if ( ! $bg_decl ) {
            $color = $this->safe_color_css( $s['bg_color'] ?? '' );
            if ( $color !== '' ) {
                $bg_decl = 'background: ' . $color;
            }
        }

        // Shadow (preset/custom)
        $shadow_css = $this->build_shadow_decl( $s );

        // Contenuto: HTML rich-text editabile inline (sanitizzato). Sempre nel DOM (SSR).
        $content_html = $this->safe_richtext_content( $s['content'] ?? '' );
        if ( trim( wp_strip_all_tags( $content_html ) ) === '' && ! $content_html ) {
            $content_html = '';
        }

        // Dati runtime (JSON), tutti scoped all'istanza.
        $cfg = [
            'scope'    => $scope,
            'preset'   => $preset,
            'count'    => $count,
            'speed'    => $speed,
            'size'     => $size,
            'wind'     => $wind,
            'gravity'  => $gravity,
            'connect'  => $connect,
            'connDist' => $conn_d,
            'hover'    => $hover,
            'opacity'  => $opacity,
            'colors'   => array_values( $palette ),
        ];

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from the internally generated $uid and a fixed-literal ternary on the boolean $hover. ?>
        <style>
            /* Tile a ZERO dimensioni: non occupa spazio nel flusso. */
            .<?php echo $uid; ?> { display: block; height: 0; line-height: 0; }
            /* Il canvas viene spostato dal runtime come sfondo del contenitore (section/colonna). */
            .olo-particles-<?php echo $uid; ?> {
                position: absolute;
                inset: 0;
                width: 100%;
                height: 100%;
                z-index: -1;          /* dietro al contenuto del contenitore, sopra il suo sfondo */
                display: block;
                pointer-events: <?php echo $hover ? 'auto' : 'none'; ?>;
            }
            /* Su touch / senza hover il puntatore non interagisce comunque */
            @media (hover: none), (pointer: coarse) {
                .olo-particles-<?php echo $uid; ?> { pointer-events: none; }
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <div class="olo-particlefx <?php echo esc_attr( $uid ); ?>" aria-hidden="true">
            <canvas class="olo-particles olo-particles-<?php echo esc_attr( $uid ); ?>" aria-hidden="true"></canvas>
        </div>

        <script>
        /* ParticleFX — runtime scoped per istanza (rif. temi 36/42/49/50; confetti one-shot 60). */
        (function(){
            var canvas = document.querySelector('.olo-particles-<?php echo esc_js( $uid ); ?>');
            if ( ! canvas ) { return; }
            if ( canvas.dataset.oloPfx ) { return; }   // idempotente: una init per istanza
            canvas.dataset.oloPfx = '1';

            var CFG = <?php echo wp_json_encode( $cfg ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline JSON in <script> built by wp_json_encode() from clamped numerics, whitelisted enums and safe_color_css()'d colors. ?>;

            // Posizionamento. "page" = overlay fisso su tutta la finestra, SOPRA il contenuto
            // (effetti stagionali: neve, coriandoli). "section" = sfondo del contenitore.
            var host;
            if ( CFG.scope === 'page' ) {
                var layer = document.createElement('div');
                layer.className = 'olo-particles-page';
                layer.style.cssText = 'position:fixed;inset:0;pointer-events:none;z-index:99990;overflow:hidden';
                document.body.appendChild( layer );
                layer.appendChild( canvas );
                canvas.style.pointerEvents = 'none';   // su tutta la pagina non blocca mai i click
                host = layer;
            } else {
                // Tile a ZERO dimensioni: sposta il canvas come SFONDO del contenitore (section)
                // in cui è droppato — dietro il contenuto, senza occupare spazio nel flusso.
                host = canvas.closest('section') || canvas.closest('.olo-section');
                if ( host ) {
                    var hcs = getComputedStyle( host );
                    if ( hcs.position === 'static' )  { host.style.position = 'relative'; }
                    if ( hcs.overflow === 'visible' ) { host.style.overflow = 'hidden'; }
                    host.style.isolation = 'isolate';   // confina lo stacking nella sezione
                    // Se la sezione ha layer di sfondo DOM (video/gallery/immagine), il suo
                    // contenitore-contenuto sta a z-index>=1 e i layer bg a z-index:0. In tal
                    // caso il canvas va messo SOPRA i layer bg (z-index:0, inserito DOPO di essi
                    // cioè subito prima del contenitore) ma SOTTO il contenuto. Altrimenti
                    // (sfondo solo CSS o assente) resta dietro al contenuto in flusso (z-index:-1).
                    var contentEl = host.querySelector(':scope > .uk-container, :scope > .olo-section-fullbleed');
                    var raise = false;
                    if ( contentEl ) {
                        var cz = parseInt( getComputedStyle( contentEl ).zIndex, 10 );
                        if ( cz >= 1 ) { raise = true; }
                    }
                    if ( raise ) {
                        canvas.style.zIndex = '0';
                        host.insertBefore( canvas, contentEl );   // sopra video/gallery, sotto il contenuto
                    } else {
                        canvas.style.zIndex = '-1';
                        host.insertBefore( canvas, host.firstChild );
                    }
                    // Decoratore "da solo": se il contenitore non ha contenuto che gli dia
                    // altezza, dagli un'altezza minima così le particelle restano visibili.
                    if ( host.clientHeight < 40 ) { host.style.minHeight = '<?php echo intval( $min_h ); ?>px'; }
                } else {
                    host = canvas.parentElement;        // fallback estremo (struttura inattesa)
                }
            }

            var ctx = canvas.getContext('2d');
            if ( ! ctx ) { return; }

            /* I color picker token-first salvano var(--olo-color-*): valide nel CSS
               ma NON sul canvas 2D (fillStyle le ignora → particelle col colore di
               fallback). Risolviamo i token via computed style dell'host. */
            function resolveVarColor( c ) {
                if ( typeof c === 'string' && c.indexOf( 'var(' ) !== -1 ) {
                    var m = c.match( /var\(\s*(--[A-Za-z0-9_-]+)/ );
                    if ( m ) {
                        var v = getComputedStyle( host ).getPropertyValue( m[1] ).trim();
                        if ( v ) { return v; }
                    }
                }
                return c;
            }
            if ( CFG.colors && CFG.colors.map ) { CFG.colors = CFG.colors.map( resolveVarColor ); }

            var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var coarse = window.matchMedia && window.matchMedia('(hover: none), (pointer: coarse)').matches;
            var canHover = CFG.hover && ! coarse;

            // dpr cap: evita canvas enormi su display HiDPI
            var DPR = Math.min( window.devicePixelRatio || 1, 2 );
            var W = 0, H = 0;
            var parts = [];
            var mouse = { x: -9999, y: -9999, on: false };
            var rafId = null, running = false, started = false;
            var TAU = Math.PI * 2;
            var SOCCER_DARK = '#15241d';
            if ( CFG.colors ) { if ( CFG.colors[1] ) { SOCCER_DARK = CFG.colors[1]; } }

            // Modalità "tutta la pagina": le particelle vivono nello spazio dell'intero
            // documento (altezza FH) e si disegnano con offset = scorrimento, così scorrono
            // CON la pagina invece di restare incollate allo schermo (movimento relativo).
            var pageMode = ( CFG.scope === 'page' );
            var FH = 0, SCROLL = 0;
            function updateField() { FH = pageMode ? Math.max( document.documentElement.scrollHeight || H, H ) : H; }
            if ( pageMode ) {
                SCROLL = window.scrollY || window.pageYOffset || 0;
                window.addEventListener( 'scroll', function() { SCROLL = window.scrollY || window.pageYOffset || 0; }, { passive: true } );
            }

            function rnd( a, b ) { return a + Math.random() * ( b - a ); }
            function pick( arr ) { return arr[ ( Math.random() * arr.length ) | 0 ]; }

            // Su mobile riduce il numero di particelle (perf).
            function targetCount() {
                var n = CFG.count;
                if ( window.innerWidth < 640 ) { n = Math.round( n * 0.5 ); }
                else if ( window.innerWidth < 1024 ) { n = Math.round( n * 0.75 ); }
                // In "tutta la pagina" il campo è più alto del viewport: più particelle per
                // mantenere la densità (cap 4× per le prestazioni).
                if ( pageMode && FH > H ) { n = Math.round( n * Math.min( FH / H, 4 ) ); }
                return Math.max( 1, n );
            }

            function resize() {
                W = canvas.offsetWidth;
                H = canvas.offsetHeight;
                canvas.width  = Math.max( 1, Math.round( W * DPR ) );
                canvas.height = Math.max( 1, Math.round( H * DPR ) );
                ctx.setTransform( DPR, 0, 0, DPR, 0, 0 );
                updateField();
            }

            // ── Factory particella per preset ──────────────────────────────
            function makePetal()  { return { x: rnd(0,W), y: rnd(-H,0),  r: CFG.size*rnd(.7,1.3), s: rnd(.5,1.6)*CFG.speed, a: rnd(0,TAU), sw: rnd(.6,2)*CFG.wind, col: pick(CFG.colors), rot: rnd(0,TAU) }; }
            function makeSnow()   { return { x: rnd(0,W), y: rnd(-H,0),  r: Math.max(1,CFG.size*rnd(.25,.7)), s: rnd(.4,1.4)*CFG.speed, a: rnd(0,TAU), sw: rnd(.3,1)*CFG.wind, col: pick(CFG.colors) }; }
            function makeBubble() { return { x: rnd(0,W), y: H+rnd(0,H), r: Math.max(1,CFG.size*rnd(.4,1.1)), s: rnd(.5,1.6)*CFG.speed, a: rnd(0,TAU), sw: rnd(.3,1)*CFG.wind, col: pick(CFG.colors) }; }
            function makeStar()   { return { x: rnd(0,W), y: rnd(0,H),   r: Math.max(.4,CFG.size*rnd(.06,.28)), tw: rnd(0,TAU), sp: rnd(.3,.8), vx: rnd(-.15,.15)*CFG.wind, vy: rnd(.02,.18)*CFG.gravity*CFG.speed, col: pick(CFG.colors) }; }
            function makeConfetti(){ var ang=rnd(0,TAU), spd=rnd(3,9)*CFG.speed; return { x: rnd(W*.35,W*.65), y: rnd(-20, H*.25), r: CFG.size*rnd(.5,1.1), vx: Math.cos(ang)*spd*.5, vy: Math.sin(ang)*spd - rnd(2,6), a: rnd(0,TAU), va: rnd(-.2,.2), col: pick(CFG.colors), life: 1 }; }
            function makeSoccer(){ return { x: rnd(0,W), y: rnd(-H,0), r: Math.max(3, CFG.size*rnd(.9,1.6)), s: rnd(.5,1.4)*CFG.speed, a: rnd(0,TAU), ph: rnd(0,TAU), sw: rnd(.4,1.2)*CFG.wind, spin: rnd(-.05,.05), col: CFG.colors[0] }; }

            function makeOne() {
                switch ( CFG.preset ) {
                    case 'snow':     return makeSnow();
                    case 'bubbles':  return makeBubble();
                    case 'stars':    return makeStar();
                    case 'confetti': return makeConfetti();
                    case 'soccer':   return makeSoccer();
                    default:         return makePetal();
                }
            }

            // page mode: posiziona la Y "fuori dal campo" così la scena parte VUOTA e si popola
            // per movimento — caduta (snow/petals) → sopra [-FH,0]; salita (bubbles) → sotto
            // [FH,2·FH]; stelle sospese → distribuite. Il riciclo (wrap su FH) tiene poi pieno il campo.
            function placeY( p ) {
                if ( ! pageMode || CFG.preset === 'confetti' ) { return; }
                if ( CFG.preset === 'bubbles' )    { p.y = rnd( FH, 2 * FH ); }
                else if ( CFG.preset === 'stars' ) { p.y = rnd( 0, FH ); }
                else                               { p.y = rnd( -FH, 0 ); }
            }

            function build() {
                parts = [];
                var n = targetCount();
                for ( var i = 0; i < n; i++ ) { var p = makeOne(); placeY( p ); parts.push( p ); }
            }

            // ── Disegno singola particella ────────────────────────────────
            function drawPetal( p ) {
                ctx.save(); ctx.translate( p.x, p.y ); ctx.rotate( p.a );
                ctx.globalAlpha = CFG.opacity; ctx.fillStyle = p.col;
                ctx.beginPath(); ctx.ellipse( 0, 0, p.r, p.r * .55, 0, 0, TAU ); ctx.fill();
                ctx.restore();
            }
            function drawDot( p ) {
                ctx.globalAlpha = CFG.opacity; ctx.fillStyle = p.col;
                ctx.beginPath(); ctx.arc( p.x, p.y, p.r, 0, TAU ); ctx.fill();
            }
            function drawBubble( p ) {
                ctx.globalAlpha = CFG.opacity; ctx.lineWidth = 1; ctx.strokeStyle = p.col;
                ctx.beginPath(); ctx.arc( p.x, p.y, p.r, 0, TAU ); ctx.stroke();
                ctx.globalAlpha = CFG.opacity * .18; ctx.fillStyle = p.col; ctx.fill();
            }
            function drawStar( p ) {
                var a = ( .5 + Math.sin( p.tw ) * .4 ) * CFG.opacity;
                ctx.globalAlpha = a; ctx.fillStyle = p.col;
                ctx.beginPath(); ctx.arc( p.x, p.y, p.r, 0, TAU ); ctx.fill();
            }
            function drawConfetti( p ) {
                ctx.save(); ctx.translate( p.x, p.y ); ctx.rotate( p.a );
                ctx.globalAlpha = Math.max( 0, Math.min( 1, p.life ) ) * CFG.opacity; ctx.fillStyle = p.col;
                ctx.fillRect( -p.r * .5, -p.r * .35, p.r, p.r * .7 );
                ctx.restore();
            }
            // Pallone da calcio stilizzato: corpo chiaro + contorno, pentagono centrale e
            // cuciture verso il bordo (in SOCCER_DARK). Ruota su se stesso (p.a/p.spin).
            function drawSoccer( p ) {
                var pr = p.r * .42, i, ang, px, py, a2;
                ctx.save(); ctx.translate( p.x, p.y ); ctx.rotate( p.a );
                ctx.globalAlpha = CFG.opacity;
                ctx.fillStyle = p.col;
                ctx.beginPath(); ctx.arc( 0, 0, p.r, 0, TAU ); ctx.fill();
                ctx.lineWidth = Math.max( 1, p.r * .08 ); ctx.strokeStyle = SOCCER_DARK;
                ctx.beginPath(); ctx.arc( 0, 0, p.r, 0, TAU ); ctx.stroke();
                ctx.fillStyle = SOCCER_DARK;
                ctx.beginPath();
                for ( i = 0; i < 5; i++ ) {
                    ang = -Math.PI / 2 + i * TAU / 5; px = Math.cos( ang ) * pr; py = Math.sin( ang ) * pr;
                    if ( i === 0 ) { ctx.moveTo( px, py ); } else { ctx.lineTo( px, py ); }
                }
                ctx.closePath(); ctx.fill();
                ctx.lineWidth = Math.max( 1, p.r * .09 );
                for ( i = 0; i < 5; i++ ) {
                    a2 = -Math.PI / 2 + i * TAU / 5;
                    ctx.beginPath();
                    ctx.moveTo( Math.cos( a2 ) * pr, Math.sin( a2 ) * pr );
                    ctx.lineTo( Math.cos( a2 ) * p.r, Math.sin( a2 ) * p.r );
                    ctx.stroke();
                }
                ctx.restore(); ctx.globalAlpha = 1;
            }

            // ── Linee di connessione (costellazioni) ──────────────────────
            function drawLines() {
                var d2 = CFG.connDist * CFG.connDist, lc = CFG.colors[1] || CFG.colors[0] || '#ffffff';
                ctx.lineWidth = .7;
                // particella ↔ particella
                for ( var i = 0; i < parts.length; i++ ) {
                    for ( var j = i + 1; j < parts.length; j++ ) {
                        var a = parts[i], b = parts[j];
                        var dx = a.x - b.x, dy = a.y - b.y, dd = dx*dx + dy*dy;
                        if ( dd < d2 ) {
                            ctx.globalAlpha = ( 1 - Math.sqrt(dd) / CFG.connDist ) * .5 * CFG.opacity;
                            ctx.strokeStyle = lc;
                            ctx.beginPath(); ctx.moveTo( a.x, a.y ); ctx.lineTo( b.x, b.y ); ctx.stroke();
                        }
                    }
                }
                // particella ↔ cursore
                if ( canHover && mouse.on ) {
                    var md2 = ( CFG.connDist * 1.8 ); md2 = md2 * md2;
                    for ( var k = 0; k < parts.length; k++ ) {
                        var s = parts[k], mx = s.x - mouse.x, my = s.y - mouse.y, mdd = mx*mx + my*my;
                        if ( mdd < md2 ) {
                            ctx.globalAlpha = ( 1 - Math.sqrt(mdd) / ( CFG.connDist * 1.8 ) ) * CFG.opacity;
                            ctx.strokeStyle = CFG.colors[1] || lc;
                            ctx.beginPath(); ctx.moveTo( mouse.x, mouse.y ); ctx.lineTo( s.x, s.y ); ctx.stroke();
                        }
                    }
                }
                ctx.globalAlpha = 1;
            }

            // Scostamento leggero dal cursore (preset non-costellazione, hover on)
            function repel( p ) {
                var dx = p.x - mouse.x, dy = p.y - mouse.y, dd = dx*dx + dy*dy;
                if ( dd < 9000 && dd > 0.01 ) {
                    var f = ( 1 - dd / 9000 ) * 1.2, dist = Math.sqrt( dd );
                    p.x += ( dx / dist ) * f; p.y += ( dy / dist ) * f;
                }
            }

            // ── Step fisico per preset ────────────────────────────────────
            function step() {
                for ( var i = 0; i < parts.length; i++ ) {
                    var p = parts[i];
                    if ( CFG.preset === 'bubbles' ) {
                        p.y -= p.s * CFG.gravity; p.a += .03; p.x += Math.sin( p.a ) * .4 * CFG.wind;
                        if ( canHover && mouse.on ) { repel( p ); }
                        if ( pageMode ) { if ( p.y < 0 ) { p.y += FH; } } else if ( p.y < -10 ) { p.y = H + 10; p.x = rnd( 0, W ); }
                    } else if ( CFG.preset === 'stars' ) {
                        p.tw += .03; p.x += p.vx; p.y += p.vy;
                        var sh = pageMode ? FH : H;
                        if ( p.y > sh ) { p.y -= sh; } if ( p.y < 0 ) { p.y += sh; }
                        if ( p.x > W ) { p.x = 0; } if ( p.x < 0 ) { p.x = W; }
                    } else if ( CFG.preset === 'confetti' ) {
                        p.vy += .12 * CFG.gravity; p.x += p.vx; p.y += p.vy; p.a += p.va;
                        p.vx *= .992; p.life -= .006;
                        // one-shot: NON ricicla; quando esce/sfuma resta fuori scena
                    } else { // petals / snow / soccer
                        p.y += p.s * CFG.gravity; p.a += ( p.spin != null ? p.spin : .02 );
                        var phase = ( p.ph != null ? p.ph : p.a ); p.x += Math.sin( phase ) * p.sw;
                        if ( p.ph != null ) { p.ph += .02; }
                        if ( canHover && mouse.on ) { repel( p ); }
                        if ( pageMode ) { if ( p.y > FH ) { p.y -= FH; } } else if ( p.y > H + 20 ) { p.y = -20; p.x = rnd( 0, W ); }
                        if ( p.x < -30 ) { p.x = W + 20; } if ( p.x > W + 30 ) { p.x = -20; }
                    }
                }
            }

            function drawAll() {
                ctx.clearRect( 0, 0, W, H );
                // page mode: trasla il disegno di -SCROLL — le particelle (in coordinate
                // documento) appaiono nella posizione giusta e scorrono con la pagina.
                if ( pageMode ) { ctx.save(); ctx.translate( 0, -SCROLL ); }
                if ( CFG.connect ) { drawLines(); }
                for ( var i = 0; i < parts.length; i++ ) {
                    var p = parts[i];
                    if ( pageMode ) { var sy = p.y - SCROLL; if ( sy < -60 || sy > H + 60 ) { continue; } }
                    switch ( CFG.preset ) {
                        case 'snow':     drawDot( p ); break;
                        case 'bubbles':  drawBubble( p ); break;
                        case 'stars':    drawStar( p ); break;
                        case 'confetti': drawConfetti( p ); break;
                        case 'soccer':   drawSoccer( p ); break;
                        default:         drawPetal( p );
                    }
                }
                if ( pageMode ) { ctx.restore(); }
                ctx.globalAlpha = 1;
            }

            function confettiDone() {
                // tutte fuori dallo schermo o sfumate
                for ( var i = 0; i < parts.length; i++ ) {
                    if ( parts[i].life > 0 && parts[i].y < H + 40 ) { return false; }
                }
                return true;
            }

            function frame() {
                if ( ! running ) { return; }
                step();
                drawAll();
                if ( CFG.preset === 'confetti' && confettiDone() ) {
                    drawAll(); running = false; rafId = null; return; // burst concluso
                }
                rafId = requestAnimationFrame( frame );
            }

            function start() {
                if ( running ) { return; }
                if ( ! started ) { started = true; if ( ! parts.length ) { build(); } }
                running = true;
                if ( reduce ) {
                    // reduced-motion: un solo frame statico, niente loop
                    drawAll(); running = false; return;
                }
                rafId = requestAnimationFrame( frame );
            }
            function stop() {
                running = false;
                if ( rafId ) { cancelAnimationFrame( rafId ); rafId = null; }
            }

            // Ricalcolo su cambio dimensioni, SENZA azzerare le particelle quando non serve.
            // Distingue viewport (W/H) da altezza-documento (FH): se cambia solo il documento
            // — tipico durante lo scroll quando i tile lazy si caricano — aggiorna il campo e
            // adatta il numero di particelle, ma quelle già in scena restano (niente reset).
            function reflow() {
                var prevW = W, prevH = H;
                resize();
                if ( CFG.preset === 'confetti' ) { return; }   // burst one-shot: non rigenerare
                if ( ! started || ! parts.length ) { return; }
                if ( W !== prevW || Math.abs( H - prevH ) > 120 ) {
                    build();   // viewport cambiato davvero (no micro-variazioni barra URL mobile)
                } else if ( pageMode ) {
                    var want = targetCount();
                    while ( parts.length < want ) { var np = makeOne(); placeY( np ); parts.push( np ); }
                    if ( parts.length > want ) { parts.length = want; }
                }
                if ( reduce ) { drawAll(); }
            }

            // Pointer Events (solo se hover abilitato e device non coarse)
            if ( canHover ) {
                canvas.addEventListener( 'pointermove', function( e ) {
                    var r = canvas.getBoundingClientRect();
                    mouse.x = e.clientX - r.left; mouse.y = e.clientY - r.top; mouse.on = true;
                }, { passive: true } );
                canvas.addEventListener( 'pointerleave', function() { mouse.on = false; mouse.x = mouse.y = -9999; } );
            }

            var rT = null;
            window.addEventListener( 'resize', function() {
                if ( rT ) { clearTimeout( rT ); }
                rT = setTimeout( reflow, 150 );
            }, { passive: true } );

            resize();

            // Il contenitore può cambiare altezza dopo il primo paint (contenuto che
            // arriva dopo nel parse, immagini che caricano): riadatta canvas + particelle.
            if ( 'ResizeObserver' in window && host ) {
                var roT = null;
                var ro = new ResizeObserver( function() {
                    if ( roT ) { clearTimeout( roT ); }
                    roT = setTimeout( reflow, 150 );
                } );
                // page mode: segui l'altezza del documento (il campo copre tutta la pagina);
                // section mode: segui il contenitore.
                ro.observe( pageMode ? document.body : host );
            }

            // Performance: avvia/spegni col viewport. Confetti = parte una volta sola.
            if ( 'IntersectionObserver' in window ) {
                var io = new IntersectionObserver( function( entries ) {
                    for ( var i = 0; i < entries.length; i++ ) {
                        if ( entries[i].isIntersecting ) {
                            start();
                            if ( CFG.preset === 'confetti' ) { io.disconnect(); }
                        } else if ( CFG.preset !== 'confetti' ) {
                            stop();
                        }
                    }
                }, { threshold: 0.01 } );
                io.observe( canvas );
            } else {
                start();
            }
        })();
        </script>
        <?php
        // ── Sistema bordi standard (come marquee) ─────────────────────────
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) {
                echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            }
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
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
            $h     = intval( $s['shadow_h'] ?? 0 );
            $v     = intval( $s['shadow_v'] ?? 4 );
            $blur  = max( 0, intval( $s['shadow_blur'] ?? 10 ) );
            $spread = intval( $s['shadow_spread'] ?? 0 );
            $color = $this->safe_color_css( $s['shadow_color'] ?? '' ) ?: 'rgba(0,0,0,0.15)';
            $inset = ! empty( $s['shadow_inset'] ) ? 'inset ' : '';
            return "{$inset}{$h}px {$v}px {$blur}px {$spread}px {$color}";
        }
        $map = [
            'sm' => '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
            'md' => '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
            'lg' => '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
            'xl' => '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
        ];
        return $map[ $preset ] ?? '';
    }
}
