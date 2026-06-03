<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Tile ScratchFX — "gratta e scopri" (famiglia C, bucket C).
 *
 * Riferimenti visivi: handoff-tile-speciali/temi/44-tema-tattoo.html (hint + foil texture,
 * brush destination-out) e 45-tema-gelateria.html (scaling coordinate dpr-corretto + reset).
 *
 * Contratto §2:
 *  - Parametrico: ogni colore/numero/testo è un campo (vedi src/config/elements/scratchfx.js).
 *  - Scoped per istanza: tutto il CSS è prefissato con $uid; il canvas ha la classe
 *    .olo-scratch-<id> e il runtime è un IIFE idempotente (guard su dataset). N istanze coesistono.
 *  - SSR: il contenuto sotto (immagine + testo premio) è già nel DOM e visibile.
 *  - reduced-motion / no-JS: nessuna copertura, contenuto direttamente visibile + pulsante "Scopri".
 *  - Pointer Events; off su (hover:none)/(pointer:coarse)? No: lo scratch funziona benissimo a
 *    dito (è il caso d'uso principale) → restiamo attivi su touch, ma usiamo touch-action:none e
 *    il pulsante "Scopri" copre la tastiera. Su reduced-motion la copertura non viene mai dipinta.
 *  - Performance: la copertura viene dipinta solo quando il canvas entra nel viewport (IO),
 *    devicePixelRatio cap a 2, will-change mirato.
 *  - A11y: il pulsante "Scopri" è focus-visible e rivela tutto da tastiera; il testo premio è
 *    reale (selezionabile) dietro al canvas; il canvas è aria-hidden.
 */
class Olo_Scratchfx_Tile extends Olo_Tile_Base {

    protected $type     = 'scratchfx';
    protected $name     = 'Gratta e Scopri';
    protected $icon     = 'dashicons-image-filter';
    protected $category = 'interactive';
    protected $defaults = [
        // Contenuto sotto la copertura
        'image'         => '',
        'prize_eyebrow' => 'Edizione limitata',
        'prize_title'   => 'Gusto a sorpresa',
        'prize_text'    => 'Gratta via la pellicola per scoprire la sorpresa.',
        'text_color'    => '',
        'accent_color'  => '',
        'under_bg'      => '',
        'hint'          => 'Gratta con il dito o il mouse per scoprire',
        'show_button'   => true,
        'reveal_label'  => 'Scopri',

        // Aspetto copertura
        'cover_type'       => 'gradient',
        'cover_color'      => '#C9C2CC',
        'cover_color2'     => '#9A93A0',
        'cover_angle'      => 135,
        'cover_image'      => '',
        'cover_text'       => '',
        'cover_text_color' => '',

        // Comportamento
        'brush_size'       => 32,
        'reveal_threshold' => 60,
        'reset_on_leave'   => false,

        // Layout
        'height_mode'   => 'aspect',
        'aspect'        => '16/10',
        'height'        => 320,
        'max_width'     => 520,
        'align'         => 'center',
        'border_radius' => [ 'tl' => 24, 'tr' => 24, 'br' => 24, 'bl' => 24 ],

        // Stile
        'shadow'                  => 'lg',
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
        $id  = wp_rand( 10000, 99999 );
        $uid = 'olo-scratchfx-' . $id;
        // Classe del canvas come da spec §4: .olo-scratch-<id> (scoped per istanza).
        $cv  = 'olo-scratch-' . $id;

        // ── Contenuto premio ──
        $image    = esc_url( $s['image'] );
        $eyebrow  = esc_html( wp_strip_all_tags( $s['prize_eyebrow'] ) );
        $title    = esc_html( wp_strip_all_tags( $s['prize_title'] ) );
        $text     = esc_html( wp_strip_all_tags( $s['prize_text'] ) );
        $hint     = esc_html( wp_strip_all_tags( $s['hint'] ) );
        $show_btn = ! empty( $s['show_button'] );
        $btn_lbl  = esc_html( wp_strip_all_tags( $s['reveal_label'] ) ) ?: esc_html__( 'Scopri', 'olobuilder' );

        $text_color   = $this->safe_color_css( $s['text_color'] )   ?: 'var(--olo-color-text, #2b2230)';
        $accent_color = $this->safe_color_css( $s['accent_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $under_bg     = $this->safe_color_css( $s['under_bg'] )     ?: 'var(--olo-color-surface, #fff6f0)';

        // ── Copertura (dipinta dal canvas via JS; qui solo i parametri) ──
        $cover_type  = in_array( $s['cover_type'], [ 'solid', 'gradient', 'image' ], true ) ? $s['cover_type'] : 'gradient';
        $cover_c1    = $this->safe_color_css( $s['cover_color'] )      ?: '#C9C2CC';
        $cover_c2    = $this->safe_color_css( $s['cover_color2'] )     ?: '#9A93A0';
        $cover_angle = max( 0, min( 360, intval( $s['cover_angle'] ) ) );
        $cover_img   = esc_url( $s['cover_image'] );
        $cover_text  = trim( (string) $s['cover_text'] );
        $cover_txt_c = $this->safe_color_css( $s['cover_text_color'] ) ?: 'rgba(0,0,0,0.28)';

        // ── Comportamento ──
        $brush     = max( 6, min( 100, intval( $s['brush_size'] ) ) );
        $threshold = max( 0, min( 100, intval( $s['reveal_threshold'] ) ) );
        $reset     = ! empty( $s['reset_on_leave'] );

        // ── Layout ──
        $height_mode = $s['height_mode'] === 'fixed' ? 'fixed' : 'aspect';
        $aspect_ok   = [ '16/10', '16/9', '4/3', '3/2', '1/1', '2/1' ];
        $aspect      = in_array( $s['aspect'], $aspect_ok, true ) ? $s['aspect'] : '16/10';
        $height      = max( 80, intval( $s['height'] ) );
        $max_width   = max( 160, intval( $s['max_width'] ) );
        $align       = in_array( $s['align'], [ 'left', 'center', 'right' ], true ) ? $s['align'] : 'center';
        $margin_x    = $align === 'left' ? '0 auto 0 0' : ( $align === 'right' ? '0 0 0 auto' : '0 auto' );
        $radius_css  = $this->build_border_radius_css( $s['border_radius'] ?? [] );

        // ── Shadow preset → box-shadow ──
        $shadow_map = [
            'sm' => '0 1px 3px 0 rgba(0,0,0,0.12),0 1px 2px -1px rgba(0,0,0,0.1)',
            'md' => '0 8px 18px -10px rgba(0,0,0,0.30)',
            'lg' => '0 30px 60px -28px rgba(0,0,0,0.40)',
            'xl' => '0 40px 80px -30px rgba(0,0,0,0.5)',
        ];
        $shadow_val = $s['shadow'] ?? 'none';
        $shadow_css = '';
        if ( isset( $shadow_map[ $shadow_val ] ) ) {
            $shadow_css = 'box-shadow:' . $shadow_map[ $shadow_val ] . ';';
        } elseif ( $shadow_val === 'custom' ) {
            $sh    = intval( $s['shadow_h'] ?? 0 );
            $sv    = intval( $s['shadow_v'] ?? 4 );
            $sblur = intval( $s['shadow_blur'] ?? 10 );
            $sspr  = intval( $s['shadow_spread'] ?? 0 );
            $scol  = $this->safe_color_css( $s['shadow_color'] ?? '' ) ?: 'rgba(0,0,0,0.15)';
            $sins  = ! empty( $s['shadow_inset'] ) ? 'inset ' : '';
            $shadow_css = "box-shadow:{$sins}{$sh}px {$sv}px {$sblur}px {$sspr}px {$scol};";
        }

        // Dimensione "box" (aspect-ratio o altezza fissa) applicata al wrapper grattabile
        $box_size_css = $height_mode === 'fixed'
            ? "height:{$height}px;"
            : "aspect-ratio:{$aspect};";

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                width: 100%;
                max-width: <?php echo $max_width; ?>px;
                margin: <?php echo $margin_x; ?>;
                text-align: <?php echo $align; ?>;
            }
            .<?php echo $uid; ?> .olo-scratch-stage {
                position: relative;
                width: 100%;
                <?php echo $box_size_css; ?>
                overflow: hidden;
                <?php if ( $radius_css ) : ?>border-radius: <?php echo $radius_css; ?>;<?php endif; ?>
                <?php echo $shadow_css; ?>
                background: <?php echo $under_bg; ?>;
                isolation: isolate;
            }
            /* Contenuto premio — SSR, sempre presente sotto */
            .<?php echo $uid; ?> .olo-scratch-under {
                position: absolute;
                inset: 0;
                z-index: 1;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
                padding: 30px;
                gap: 8px;
                color: <?php echo $text_color; ?>;
            }
            <?php if ( $image ) : ?>
            .<?php echo $uid; ?> .olo-scratch-img {
                position: absolute;
                inset: 0;
                z-index: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-scratch-eyebrow {
                font-size: 11px;
                letter-spacing: .16em;
                text-transform: uppercase;
                font-weight: 600;
                color: <?php echo $accent_color; ?>;
                margin: 0;
            }
            .<?php echo $uid; ?> .olo-scratch-title {
                font-size: clamp(28px, 5vw, 52px);
                line-height: 1.05;
                font-weight: 700;
                margin: 0;
            }
            .<?php echo $uid; ?> .olo-scratch-desc {
                font-size: 15px;
                line-height: 1.5;
                max-width: 32ch;
                margin: 0;
                opacity: .9;
            }
            /* Canvas copertura — sopra il contenuto */
            .<?php echo $uid; ?> .<?php echo $cv; ?> {
                position: absolute;
                inset: 0;
                z-index: 2;
                width: 100%;
                height: 100%;
                touch-action: none;
                cursor: grab;
                will-change: transform;
            }
            .<?php echo $uid; ?> .<?php echo $cv; ?>:active { cursor: grabbing; }
            /* Hint — scompare al primo tocco (via JS) */
            .<?php echo $uid; ?> .olo-scratch-hint {
                position: absolute;
                top: 14px;
                left: 50%;
                transform: translateX(-50%);
                z-index: 3;
                font-size: 11px;
                letter-spacing: .12em;
                text-transform: uppercase;
                color: #fff;
                background: rgba(0,0,0,0.45);
                padding: 8px 16px;
                border-radius: 100px;
                pointer-events: none;
                transition: opacity .5s ease;
                white-space: nowrap;
                max-width: calc(100% - 28px);
                overflow: hidden;
                text-overflow: ellipsis;
            }
            /* Pulsante "Scopri" — alternativa tastiera / fallback no-JS */
            .<?php echo $uid; ?> .olo-scratch-reveal {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                margin-top: 16px;
                background: transparent;
                border: 1px solid var(--olo-color-border, rgba(0,0,0,0.18));
                color: <?php echo $text_color; ?>;
                border-radius: 100px;
                padding: 9px 18px;
                font: inherit;
                font-size: 12px;
                letter-spacing: .06em;
                text-transform: uppercase;
                cursor: pointer;
                transition: border-color .2s ease, color .2s ease, background .2s ease;
            }
            .<?php echo $uid; ?> .olo-scratch-reveal:hover {
                border-color: <?php echo $accent_color; ?>;
                color: <?php echo $accent_color; ?>;
            }
            .<?php echo $uid; ?> .olo-scratch-reveal:focus-visible {
                outline: 2px solid <?php echo $accent_color; ?>;
                outline-offset: 2px;
            }
            /* Reduced motion: nessuna copertura — il contenuto è subito visibile */
            @media (prefers-reduced-motion: reduce) {
                .<?php echo $uid; ?> .<?php echo $cv; ?>,
                .<?php echo $uid; ?> .olo-scratch-hint { display: none !important; }
            }
        </style>

        <div class="<?php echo esc_attr( $uid ); ?>">
            <div class="olo-scratch-stage">
                <?php if ( $image ) : ?>
                    <img class="olo-scratch-img" src="<?php echo $image; ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" />
                <?php endif; ?>
                <div class="olo-scratch-under">
                    <?php if ( $eyebrow ) : ?><p class="olo-scratch-eyebrow"><?php echo $eyebrow; ?></p><?php endif; ?>
                    <?php if ( $title ) : ?><p class="olo-scratch-title"><?php echo $title; ?></p><?php endif; ?>
                    <?php if ( $text ) : ?><p class="olo-scratch-desc"><?php echo $text; ?></p><?php endif; ?>
                </div>
                <canvas class="<?php echo esc_attr( $cv ); ?>" aria-hidden="true"></canvas>
                <?php if ( $hint ) : ?>
                    <div class="olo-scratch-hint" data-olo-hint><?php echo $hint; ?></div>
                <?php endif; ?>
            </div>
            <?php if ( $show_btn ) : ?>
                <button type="button" class="olo-scratch-reveal" data-olo-reveal aria-label="<?php echo esc_attr( $btn_lbl ); ?>"><?php echo $btn_lbl; ?></button>
            <?php endif; ?>
        </div>

        <script>
        /* ScratchFX — runtime scoped per istanza (rif. 44/45-tema-*.html). IIFE idempotente, multi-istanza. */
        (function(){
            var root = document.querySelector('.<?php echo esc_js( $uid ); ?>');
            if ( ! root ) { return; }
            if ( root.dataset.oloScratch ) { return; }   // idempotente: una sola init per istanza
            root.dataset.oloScratch = '1';

            var canvas = root.querySelector('.<?php echo esc_js( $cv ); ?>');
            var hint   = root.querySelector('[data-olo-hint]');
            var revealBtn = root.querySelector('[data-olo-reveal]');

            // prefers-reduced-motion → nessuna copertura: il contenuto è già visibile (SSR).
            var rm = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)');
            if ( ! canvas || ( rm && rm.matches ) ) {
                if ( canvas ) { canvas.style.display = 'none'; }
                if ( hint ) { hint.style.display = 'none'; }
                if ( revealBtn ) { revealBtn.style.display = 'none'; } // niente da rivelare
                return;
            }

            var ctx = canvas.getContext('2d');
            if ( ! ctx ) { return; }

            var COVER_TYPE = <?php echo json_encode( $cover_type ); ?>;
            var COVER_C1   = <?php echo json_encode( $cover_c1 ); ?>;
            var COVER_C2   = <?php echo json_encode( $cover_c2 ); ?>;
            var COVER_ANG  = <?php echo json_encode( $cover_angle ); ?>;
            var COVER_IMG  = <?php echo json_encode( $cover_img ); ?>;
            var COVER_TEXT = <?php echo json_encode( $cover_text ); ?>;
            var COVER_TXTC = <?php echo json_encode( $cover_txt_c ); ?>;
            var BRUSH      = <?php echo json_encode( $brush ); ?>;
            var THRESHOLD  = <?php echo json_encode( $threshold ); ?>;   // 0 = no auto-reveal
            var RESET      = <?php echo $reset ? 'true' : 'false'; ?>;

            var dpr = Math.min( window.devicePixelRatio || 1, 2 ); // dpr cap
            var W = 0, H = 0;                 // dimensioni backing store (px * dpr)
            var painted = false, cleared = false, hintHidden = false, revealed = false;
            var coverImgEl = null;

            // Converte angolo (deg) in punti start/end per il gradiente lineare sul box WxH.
            function gradPoints( deg ) {
                var rad = ( deg - 90 ) * Math.PI / 180;
                var cx = W / 2, cy = H / 2;
                var ex = Math.cos( rad ), ey = Math.sin( rad );
                var half = Math.abs( W * ex ) / 2 + Math.abs( H * ey ) / 2;
                return [ cx - ex * half, cy - ey * half, cx + ex * half, cy + ey * half ];
            }

            function fillCover() {
                ctx.globalCompositeOperation = 'source-over';
                ctx.globalAlpha = 1;
                if ( COVER_TYPE === 'image' && coverImgEl ) {
                    ctx.drawImage( coverImgEl, 0, 0, W, H );
                } else if ( COVER_TYPE === 'gradient' ) {
                    var p = gradPoints( COVER_ANG );
                    var g = ctx.createLinearGradient( p[0], p[1], p[2], p[3] );
                    g.addColorStop( 0, COVER_C1 );
                    g.addColorStop( 1, COVER_C2 );
                    ctx.fillStyle = g;
                    ctx.fillRect( 0, 0, W, H );
                } else {
                    ctx.fillStyle = COVER_C1;
                    ctx.fillRect( 0, 0, W, H );
                }
                // Testo stampigliato ripetuto (es. "GRATTA")
                if ( COVER_TEXT ) {
                    ctx.save();
                    ctx.fillStyle = COVER_TXTC;
                    var fs = Math.round( 16 * dpr );
                    ctx.font = '600 ' + fs + 'px ui-monospace, SFMono-Regular, Menlo, monospace';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    var stepY = fs * 2.6, stepX = ctx.measureText( COVER_TEXT ).width + fs * 3;
                    var row = 0;
                    for ( var y = stepY; y < H + stepY; y += stepY ) {
                        var off = ( row % 2 ) ? stepX / 2 : 0;
                        for ( var x = -stepX; x < W + stepX; x += stepX ) {
                            ctx.save();
                            ctx.translate( x + off, y );
                            ctx.rotate( -0.18 );
                            ctx.fillText( COVER_TEXT, 0, 0 );
                            ctx.restore();
                        }
                        row++;
                    }
                    ctx.restore();
                }
                cleared = false;
                revealed = false;
                if ( hint ) { hint.style.opacity = '1'; hintHidden = false; }
            }

            function sizeAndPaint() {
                var rect = canvas.getBoundingClientRect();
                if ( rect.width < 1 || rect.height < 1 ) { return; }
                W = canvas.width  = Math.round( rect.width * dpr );
                H = canvas.height = Math.round( rect.height * dpr );
                fillCover();
                painted = true;
            }

            // Coordinate puntatore → spazio backing store (gestisce dpr + scaling CSS)
            function pos( e ) {
                var r = canvas.getBoundingClientRect();
                return [
                    ( e.clientX - r.left ) * ( canvas.width  / r.width ),
                    ( e.clientY - r.top  ) * ( canvas.height / r.height )
                ];
            }

            function scratch( x, y ) {
                ctx.globalCompositeOperation = 'destination-out';
                ctx.beginPath();
                ctx.arc( x, y, BRUSH * dpr, 0, Math.PI * 2 );
                ctx.fill();
                cleared = true;
                if ( hint && ! hintHidden ) { hint.style.opacity = '0'; hintHidden = true; }
            }

            // Stima % grattata campionando gli alpha su una griglia ridotta (economico).
            function scratchedPct() {
                try {
                    var step = 12, total = 0, gone = 0;
                    var data = ctx.getImageData( 0, 0, W, H ).data;
                    for ( var y = 0; y < H; y += step ) {
                        for ( var x = 0; x < W; x += step ) {
                            var a = data[ ( y * W + x ) * 4 + 3 ];
                            total++;
                            if ( a < 64 ) { gone++; }
                        }
                    }
                    return total ? ( gone / total ) * 100 : 0;
                } catch ( err ) {
                    return 0; // canvas "tainted" (immagine cross-origin) → niente auto-reveal
                }
            }

            // Reveal completo: cancella tutta la copertura (con piccola dissolvenza).
            function revealAll() {
                if ( revealed ) { return; }
                revealed = true;
                cleared = true;
                if ( hint && ! hintHidden ) { hint.style.opacity = '0'; hintHidden = true; }
                canvas.style.transition = 'opacity .4s ease';
                canvas.style.opacity = '0';
                setTimeout( function(){
                    ctx.globalCompositeOperation = 'destination-out';
                    ctx.fillRect( 0, 0, W, H );
                    canvas.style.pointerEvents = 'none';
                }, 400 );
            }

            function maybeAutoReveal() {
                // Campiona solo al rilascio del puntatore (frequenza bassa → costo trascurabile)
                if ( THRESHOLD <= 0 || revealed ) { return; }
                if ( scratchedPct() >= THRESHOLD ) { revealAll(); }
            }

            var drawing = false;
            function onDown( e ) {
                if ( ! painted || revealed ) { return; }
                drawing = true;
                if ( canvas.setPointerCapture && e.pointerId !== undefined ) {
                    try { canvas.setPointerCapture( e.pointerId ); } catch ( err ) {}
                }
                var p = pos( e );
                scratch( p[0], p[1] );
            }
            function onMove( e ) {
                if ( ! drawing || revealed ) { return; }
                e.preventDefault();
                var p = pos( e );
                scratch( p[0], p[1] );
            }
            function onUp() {
                if ( ! drawing ) { return; }
                drawing = false;
                maybeAutoReveal();
            }

            canvas.addEventListener( 'pointerdown', onDown );
            canvas.addEventListener( 'pointermove', onMove, { passive: false } );
            window.addEventListener( 'pointerup', onUp );
            window.addEventListener( 'pointercancel', onUp );

            // Ricopri all'uscita del puntatore (se non già completamente rivelato)
            if ( RESET ) {
                canvas.addEventListener( 'pointerleave', function(){
                    if ( drawing || revealed ) { return; }
                    if ( cleared ) { fillCover(); }
                });
            }

            // Pulsante "Scopri" — alternativa tastiera / fallback
            if ( revealBtn ) {
                revealBtn.addEventListener( 'click', function(){ revealAll(); } );
            }

            // ResizeObserver: ridipinge solo finché l'utente non ha iniziato a grattare
            if ( 'ResizeObserver' in window ) {
                var ro = new ResizeObserver( function(){
                    if ( ! cleared && ! revealed ) { sizeAndPaint(); }
                });
                ro.observe( canvas );
            }

            // Performance: dipingi la copertura solo quando il canvas entra nel viewport.
            function bootPaint() {
                if ( COVER_TYPE === 'image' && COVER_IMG ) {
                    coverImgEl = new Image();
                    coverImgEl.crossOrigin = 'anonymous';
                    coverImgEl.onload  = function(){ sizeAndPaint(); };
                    coverImgEl.onerror = function(){ COVER_TYPE = 'gradient'; sizeAndPaint(); };
                    coverImgEl.src = COVER_IMG;
                } else {
                    sizeAndPaint();
                }
            }
            if ( 'IntersectionObserver' in window ) {
                var io = new IntersectionObserver( function( entries ){
                    for ( var i = 0; i < entries.length; i++ ) {
                        if ( entries[i].isIntersecting && ! painted ) {
                            bootPaint();
                            io.disconnect();
                            break;
                        }
                    }
                }, { threshold: 0.05 });
                io.observe( canvas );
            } else {
                bootPaint();
            }
        })();
        </script>

        <?php
        // Border system — coerente con marquee/blendtext (chiavi salvate INVARIATE)
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid} .olo-scratch-stage", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid} .olo-scratch-stage", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid} .olo-scratch-stage{{$border_css}}";
            echo $border_hover_css . $border_effect_css . '</style>';
        }
        return ob_get_clean();
    }
}
