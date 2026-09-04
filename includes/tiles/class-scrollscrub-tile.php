<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Tile ScrollScrub — pin verticale → scorrimento orizzontale.
 *
 * Effetto "Section · ScrollScrub" (rif. handoff-tile-speciali/temi/62-tema-libreria-indie.html
 * blocco "pinned horizontal shelf"; anche 35-tema-immobiliare.html "HorizontalScroll · portfolio").
 * Bucket C / famiglia B.
 *
 * Una sezione alta N×100vh resta "incollata" (sticky) mentre lo scroll verticale viene
 * rimappato a translateX di una traccia orizzontale. Con scrubbar opzionale.
 *
 * Anatomia:
 *   .outer { height: scroll_length*100vh } → .pin { position:sticky; top:0; height:100vh; overflow:hidden }
 *   (il pin copre lo schermo per nascondere lo scroll verticale; il contenuto NON è stirato)
 *   → .track { display:flex; will-change:transform }. Progress bar opzionale.
 *
 * Contratto §2:
 *   - Parametrico: ogni numero/colore = setting con default; nessun hardcode.
 *   - UID scoped: ogni regola CSS è prefissata con .olo-scrub-<id>; N istanze non si calpestano.
 *   - SSR: lo STATO BASE è la traccia in scroll orizzontale nativo (overflow-x:auto) — visibile
 *     e usabile senza JS, da tastiera, con reduced-motion e su mobile. Nessuna sezione vuota.
 *   - Runtime INLINE (IIFE idempotente, scoped, multi-istanza): su scroll
 *     p = clamp((-rect.top)/(outer.h - vh), 0..1); track.x = -p*(track.scrollW - vw);
 *     ricalcola max su resize; passive:true; IntersectionObserver spegne i listener fuori viewport.
 *   - prefers-reduced-motion (se pause_on_reduced_motion) / no-JS / mobile → resta lo scroll nativo.
 *   - Additivo: chiavi salvate invariate; riusa build_border_*_css come il marquee.
 */
class Olobuild_Scrollscrub_Tile extends Olobuild_Tile_Base {

    protected $type     = 'scrollscrub';
    protected $name     = 'Scorrimento orizzontale (ScrollScrub)';
    protected $icon     = 'dashicons-leftright';
    protected $category = 'layout';
    protected $defaults = [
        // Comportamento pin/scroll
        // behavior: 'pin' = la pagina si ferma (100vh) e lo scroll guida la fila;
        //           'inline' = niente pin, la sezione è alta quanto il contenuto e la
        //           fila scorre nativamente (touch/trackpad/tastiera, snap + progress).
        'behavior'                => 'pin',
        'scroll_length'           => 3,
        'align'                   => 'center',
        'gap'                     => 24,
        'easing'                  => 'linear',
        'show_progress'           => true,
        'pause_on_reduced_motion' => true,

        // Testata
        'heading' => 'Scorri in orizzontale',
        'kicker'  => 'scroll → orizzontale',
        // Stile testata (vuoto = comportamento storico: eredita dalla sezione)
        'heading_color' => '',
        'kicker_color'  => '',
        'heading_size'  => 44,
        'heading_font'  => '',

        // Aspetto item
        'item_width'         => 360,
        'item_min_height'    => 460,
        'round'              => 14,
        'item_padding'       => 0,
        'object_position'    => 'center center',
        'item_bg_default'    => '',
        'text_color_default' => '',
        'progress_color'     => '',
        'show_number'        => true,

        // Sovraimpressione (solo item_padding=0, testo sopra la foto): la sfumatura
        // è un layer sull'ITEM che copre la parte bassa della foto — non più il solo
        // background del box di testo, che su card piene lasciava il titolo sul vivo
        // dell'immagine.
        'overlay_scrim_color'   => '#000000',
        'overlay_scrim_opacity' => 78,   // 0 = nessuna sfumatura
        'overlay_scrim_height'  => 62,   // % dell'altezza card coperta (sfuma a trasparente)

        // Ombra (preset shadowField)
        'shadow'        => 'custom',
        'shadow_h'      => '0',
        'shadow_v'      => '14',
        'shadow_blur'   => '34',
        'shadow_spread' => '-16',
        'shadow_color'  => 'rgba(0,0,0,0.32)',
        'shadow_inset'  => false,

        // Bordo (sistema condiviso)
        'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,

        'items' => [],
    ];

    public function get_controls() {
        return [];
    }

    /**
     * box-shadow dai campi shadow_* (preset shadowField). Stesso schema di stackscroll/grid.
     */
    protected function scrub_shadow_css( $s ) {
        $val = $s['shadow'] ?? 'none';
        $map = [
            'sm' => '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
            'md' => '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
            'lg' => '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
            'xl' => '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
        ];
        if ( isset( $map[ $val ] ) ) {
            return $map[ $val ];
        }
        if ( $val === 'custom' ) {
            $sh = intval( $s['shadow_h'] ?? 0 );
            $sv = intval( $s['shadow_v'] ?? 4 );
            $sb = intval( $s['shadow_blur'] ?? 10 );
            $ss = intval( $s['shadow_spread'] ?? 0 );
            $sc = $this->safe_color_css( $s['shadow_color'] ?? '' ) ?: 'rgba(0,0,0,0.15)';
            $si = ! empty( $s['shadow_inset'] ) ? 'inset ' : '';
            return $si . $sh . 'px ' . $sv . 'px ' . $sb . 'px ' . $ss . 'px ' . $sc;
        }
        return '';
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-scrub-' . wp_rand( 10000, 99999 );

        // ── Parametri pin/scroll (clampati) ──
        $behavior   = ( ( $s['behavior'] ?? 'pin' ) === 'inline' ) ? 'inline' : 'pin';
        // Canvas del builder: l'iframe è alto quanto la pagina, il suo "100vh" è
        // enorme e il pin diventerebbe un mostro. Niente runtime: resta lo stato
        // base compatto (track scorrevole), l'altezza elemento si vede com'è.
        $in_builder = ! empty( $s['_builder_mode'] );
        $scroll_len = max( 2.0, min( 6.0, floatval( $s['scroll_length'] ) ) );
        // x100 per la height in vh: 3 → 300vh
        $outer_vh   = (int) round( $scroll_len * 100 );
        $align      = in_array( $s['align'], [ 'start', 'center' ], true ) ? $s['align'] : 'center';
        $align_css  = $align === 'start' ? 'flex-start' : 'center';
        $gap        = max( 0, min( 80, intval( $s['gap'] ) ) );
        $easing     = in_array( $s['easing'], [ 'linear', 'ease', 'ease-out' ], true ) ? $s['easing'] : 'linear';
        $show_prog  = ! empty( $s['show_progress'] );
        $respect_rm = ! empty( $s['pause_on_reduced_motion'] );

        // ── Testata ──
        $heading = isset( $s['heading'] ) ? (string) $s['heading'] : '';
        $kicker  = isset( $s['kicker'] )  ? (string) $s['kicker']  : '';

        // Stile testata: colori via token/safe_color_css, dimensione = max del clamp,
        // font via resolve_font_family (ruoli heading/serif/… o famiglia esplicita).
        $head_c    = $this->safe_color_css( $s['heading_color'] ?? '' );
        $kick_c    = $this->safe_color_css( $s['kicker_color'] ?? '' );
        $head_size = intval( $s['heading_size'] ?? 44 );
        $head_size = $head_size > 0 ? max( 18, min( 120, $head_size ) ) : 44;
        $head_font = $this->resolve_font_family( $s['heading_font'] ?? '' );

        // ── Aspetto item ──
        $item_w   = max( 120, min( 900, intval( $s['item_width'] ) ) );
        $item_mh  = max( 160, min( 900, intval( $s['item_min_height'] ) ) );
        // Dual-format: numero legacy (range) E oggetto {tl,tr,br,bl}; vuoto/zero → 0px (default storico).
        $round    = $this->build_border_radius_css( $s['round'] ?? 0 ) ?: '0px';
        $pad      = max( 0,   min( 80,  intval( $s['item_padding'] ) ) );
        $overlay  = $pad === 0; // 0 = immagine a tutto bordo, testo sovrapposto in basso

        // Punto focale GLOBALE (object-position) applicato a ogni <img> del nastro.
        // Default 'center center' = comportamento storico identico.
        $obj_pos = trim( (string) ( $s['object_position'] ?? 'center center' ) );
        if ( $obj_pos === '' ) { $obj_pos = 'center center'; }

        $bg_def   = $this->safe_color_css( $s['item_bg_default'] )    ?: 'var(--olo-color-surface, #ffffff)';
        $txt_def  = $this->safe_color_css( $s['text_color_default'] ) ?: 'var(--olo-color-text, #1f2937)';
        $prog_c   = $this->safe_color_css( $s['progress_color'] )     ?: 'var(--olo-color-primary, #e1474f)';
        $show_num = ! empty( $s['show_number'] );

        // In overlay il colore testo di default è bianco (leggibile sulla sfumatura),
        // ma text_color_default impostato lo sovrascrive; il colore per-item (inline
        // sull'item) vince su entrambi perché il body ora EREDITA, non forza #fff.
        $item_txt_css = $overlay
            ? ( $this->safe_color_css( $s['text_color_default'] ) ?: '#fff' )
            : $txt_def;

        // Scrim overlay: colore + intensità + altezza, tutti dall'inspector.
        $scrim_c = $this->safe_color_css( $s['overlay_scrim_color'] ?? '' ) ?: '#000000';
        $scrim_o = max( 0, min( 100, intval( $s['overlay_scrim_opacity'] ?? 78 ) ) );
        $scrim_h = max( 20, min( 100, intval( $s['overlay_scrim_height'] ?? 62 ) ) );

        $shadow_css = $this->scrub_shadow_css( $s );

        // ── Item ──
        $items = is_array( $s['items'] ) ? array_values( $s['items'] ) : [];
        $total = count( $items );

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: intval()/absint() clamped via max()/min() for every size, safe_color_css() whitelist for every colour, in_array() whitelists for align/easing, build_border_radius_css() (integer-forced), preset shadow map or integer-built custom shadow, fixed ternary literals and the internally generated uid. ?>
        <style>
            .<?php echo $uid; ?> { position: relative; }

            /* OUTER: in modalità pin è alta scroll_length×100vh. STATO BASE (no-JS/mobile):
               altezza automatica, il pin diventa un blocco normale e la traccia scrolla in
               orizzontale nativamente (vedi .pin / .track sotto). La classe .is-pinned viene
               aggiunta dal runtime solo quando il pin è attivo. */
            .<?php echo $uid; ?> .olo-scrub__outer {
                position: relative;
            }
            .<?php echo $uid; ?> .olo-scrub__outer.is-pinned {
                height: <?php echo $outer_vh; ?>vh;
            }

            .<?php echo $uid; ?> .olo-scrub__pin {
                position: relative;
                display: flex;
                flex-direction: column;
                justify-content: <?php echo $align_css; ?>;
            }
            /* In pin il blocco torna a coprire l'INTERO schermo (100vh): mentre le
               card scorrono in orizzontale non si vede la pagina verticale muoversi
               sotto la fila. Il CONTENUTO però NON viene stirato: «Altezza elemento»
               comanda sempre, e con align=start la fila si aggancia in alto subito
               sotto l'eventuale header sticky (padding-top misurato dal runtime). */
            .<?php echo $uid; ?> .olo-scrub__outer.is-pinned .olo-scrub__pin {
                position: -webkit-sticky;
                position: sticky;
                top: 0;
                height: 100vh;
                box-sizing: border-box;
                overflow: hidden;
            }

            .<?php echo $uid; ?> .olo-scrub__head {
                display: flex;
                justify-content: space-between;
                align-items: baseline;
                gap: 16px;
                flex-wrap: wrap;
                padding: 24px clamp(20px, 8vw, 96px) 4px;
            }
            .<?php echo $uid; ?> .olo-scrub__kick {
                font-size: 12px;
                letter-spacing: 0.16em;
                text-transform: uppercase;
                font-weight: 600;
                <?php if ( $kick_c ) : ?>
                color: <?php echo $kick_c; ?>;
                opacity: 1;
                <?php else : ?>
                opacity: 0.7;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-scrub__title {
                font-size: clamp(24px, 3.2vw, <?php echo $head_size; ?>px);
                line-height: 1.05;
                font-weight: 700;
                letter-spacing: -0.01em;
                margin: 0;
                <?php if ( $head_font ) : ?>font-family: <?php echo $head_font; ?>;<?php endif; ?>
                /* inherit: UIkit stila h1..h6 con color:#333 — il titolo deve
                   seguire il colore scelto o quello della sezione, mai il grigio vendor. */
                color: <?php echo $head_c ?: 'inherit'; ?>;
            }

            /* TRACK — STATO BASE = scroll orizzontale nativo (overflow-x:auto).
               Focusabile/scrollabile da tastiera. Quando .is-pinned è attivo, il runtime
               disattiva lo scroll nativo e guida translateX. */
            .<?php echo $uid; ?> .olo-scrub__track {
                display: flex;
                align-items: stretch;
                gap: <?php echo $gap; ?>px;
                padding: 18px clamp(20px, 8vw, 96px);
                overflow-x: auto;
                overflow-y: hidden;
                -webkit-overflow-scrolling: touch;
                scroll-snap-type: x proximity;
                scrollbar-width: thin;
            }
            .<?php echo $uid; ?> .olo-scrub__outer.is-pinned .olo-scrub__track {
                overflow: visible;
                scroll-snap-type: none;
                will-change: transform;
                transition: transform 0.06s <?php echo $easing; ?>;
            }
            <?php // NB: niente stiramento delle card in pin — «Altezza elemento» comanda
                  // sempre (v1.4.413 stirava a tutto viewport e il campo diventava morto). ?>
            .<?php echo $uid; ?> .olo-scrub__track:focus-visible {
                outline: 2px solid var(--olo-color-primary, #e1474f);
                outline-offset: 3px;
            }

            .<?php echo $uid; ?> .olo-scrub__item {
                position: relative;
                flex: 0 0 auto;
                width: <?php echo $item_w; ?>px;
                min-height: <?php echo $item_mh; ?>px;
                border-radius: <?php echo $round; ?>;
                overflow: hidden;
                background: <?php echo $bg_def; ?>;
                color: <?php echo $item_txt_css; ?>;
                <?php if ( $shadow_css ) : ?>box-shadow: <?php echo $shadow_css; ?>;<?php endif; ?>
                scroll-snap-align: center;
                display: flex;
                flex-direction: column;
                <?php if ( $overlay ) : ?>justify-content: flex-end;<?php endif; ?>
            }

            .<?php echo $uid; ?> .olo-scrub__media {
                position: <?php echo $overlay ? 'absolute' : 'relative'; ?>;
                <?php if ( $overlay ) : ?>inset: 0; z-index: 0;<?php else : ?>width: 100%; aspect-ratio: 4 / 3;<?php endif; ?>
                background: rgba(0,0,0,0.06);
            }
            <?php if ( $overlay && $scrim_o > 0 ) : ?>
            /* Scrim SOPRA la foto (z-index fra media e body): parte dal fondo e
               sfuma a trasparente all'altezza scelta. L'item ha overflow:hidden,
               quindi gli angoli restano puliti. */
            .<?php echo $uid; ?> .olo-scrub__item::after {
                content: '';
                position: absolute;
                left: 0; right: 0; bottom: 0;
                height: <?php echo $scrim_h; ?>%;
                background: linear-gradient(to top,
                    color-mix(in srgb, <?php echo $scrim_c; ?> <?php echo $scrim_o; ?>%, transparent) 0%,
                    color-mix(in srgb, <?php echo $scrim_c; ?> <?php echo (int) round( $scrim_o * 0.45 ); ?>%, transparent) 48%,
                    transparent 100%);
                z-index: 1;
                pointer-events: none;
            }
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-scrub__media img {
                position: absolute;
                inset: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
                object-position: <?php echo esc_attr( $obj_pos ); ?>;
                display: block;
            }
            .<?php echo $uid; ?> .olo-scrub__media .ph {
                position: absolute;
                inset: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                letter-spacing: 0.12em;
                text-transform: uppercase;
                opacity: 0.45;
            }

            .<?php echo $uid; ?> .olo-scrub__body {
                position: relative;
                padding: <?php echo $overlay ? '20px' : ( $pad . 'px' ); ?>;
                /* Il colore si EREDITA dall'item (default overlay = bianco): così i
                   campi "Colore testo" globale e per-elemento funzionano davvero.
                   La sfumatura non vive più qui: è lo scrim sull'item (vedi ::after). */
                z-index: 2;
            }
            .<?php echo $uid; ?> .olo-scrub__num {
                font-size: 12px;
                letter-spacing: 0.16em;
                text-transform: uppercase;
                font-weight: 700;
                opacity: 0.85;
                margin-bottom: 10px;
                <?php if ( ! $overlay ) : ?>color: <?php echo $prog_c; ?>;<?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-scrub__itemtitle {
                font-size: clamp(20px, 2.2vw, 28px);
                line-height: 1.08;
                font-weight: 700;
                margin: 0 0 6px;
                letter-spacing: -0.01em;
                /* inherit: batte il color:#333 che UIkit mette su ogni h1..h6 —
                   il titolo card segue il colore dell'item (bianco in overlay,
                   text_color_default, o il colore per-elemento). */
                color: inherit;
            }
            .<?php echo $uid; ?> .olo-scrub__sub {
                font-size: 12.5px;
                letter-spacing: 0.04em;
                text-transform: uppercase;
                opacity: 0.78;
                margin: 0 0 10px;
            }
            .<?php echo $uid; ?> .olo-scrub__text {
                font-size: 14.5px;
                line-height: 1.6;
                margin: 0;
                max-width: 42ch;
                opacity: 0.95;
            }
            .<?php echo $uid; ?> .olo-scrub__text p { margin: 0 0 0.6em; }
            .<?php echo $uid; ?> .olo-scrub__text p:last-child { margin-bottom: 0; }

            /* Scrubbar (progress) — visibile solo in modalità pin */
            .<?php echo $uid; ?> .olo-scrub__bar {
                position: absolute;
                left: clamp(20px, 8vw, 96px);
                right: clamp(20px, 8vw, 96px);
                bottom: 34px;
                height: 3px;
                border-radius: 3px;
                background: rgba(128,128,128,0.22);
                display: none;
            }
            .<?php echo $uid; ?> .olo-scrub__outer.is-pinned .olo-scrub__bar {
                display: block;
            }
            .<?php echo $uid; ?> .olo-scrub__bar i {
                display: block;
                height: 100%;
                width: 0;
                border-radius: 3px;
                background: <?php echo $prog_c; ?>;
            }
            .<?php echo $uid; ?> .olo-scrub__hint {
                position: absolute;
                bottom: 18px;
                left: clamp(20px, 8vw, 96px);
                font-size: 11px;
                letter-spacing: 0.12em;
                text-transform: uppercase;
                opacity: 0.5;
                display: none;
            }
            .<?php echo $uid; ?> .olo-scrub__outer.is-pinned .olo-scrub__hint {
                display: block;
            }

            <?php if ( $respect_rm ) : ?>
            /* Reduced-motion: forza lo stato base (scroll nativo), niente pin/height. */
            @media (prefers-reduced-motion: reduce) {
                .<?php echo $uid; ?> .olo-scrub__outer.is-pinned { height: auto; }
                .<?php echo $uid; ?> .olo-scrub__outer.is-pinned .olo-scrub__pin {
                    position: static; height: auto; overflow: visible;
                }
                .<?php echo $uid; ?> .olo-scrub__outer.is-pinned .olo-scrub__track {
                    overflow-x: auto; transform: none !important; will-change: auto;
                }
                .<?php echo $uid; ?> .olo-scrub__outer.is-pinned .olo-scrub__bar,
                .<?php echo $uid; ?> .olo-scrub__outer.is-pinned .olo-scrub__hint { display: none; }
            }
            <?php endif; ?>

            <?php
            // Override PER-DEVICE di larghezza e altezza item (campi responsive: true).
            // Ogni breakpoint senza valore eredita il desktop via cascade (come stackscroll).
            $sc_bps = [ 'tablet_landscape' => 1200, 'tablet' => 960, 'mobile_landscape' => 640, 'mobile' => 480 ];
            foreach ( $sc_bps as $sc_bp => $sc_w ) :
                $sc_iw = $s[ 'item_width_' . $sc_bp ]      ?? '';
                $sc_mh = $s[ 'item_min_height_' . $sc_bp ] ?? '';
                if ( $sc_iw === '' && $sc_mh === '' ) { continue; }
                $sc_decls = '';
                if ( $sc_iw !== '' ) { $sc_decls .= 'width:' . max( 120, min( 900, absint( $sc_iw ) ) ) . 'px;'; }
                if ( $sc_mh !== '' ) { $sc_decls .= 'min-height:' . max( 160, min( 900, absint( $sc_mh ) ) ) . 'px;'; }
                if ( $sc_decls === '' ) { continue; }
                ?>
            @media (max-width: <?php echo intval( $sc_w ); ?>px) {
                .<?php echo $uid; ?> .olo-scrub__item { <?php echo $sc_decls; ?> }
            }
            <?php endforeach; ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <div class="olo-scrub <?php echo esc_attr( $uid ); ?>">
            <div class="olo-scrub__outer">
                <div class="olo-scrub__pin">
                    <?php if ( $heading !== '' || $kicker !== '' ) : ?>
                    <div class="olo-scrub__head">
                        <div>
                            <?php if ( $kicker !== '' ) : ?><span class="olo-scrub__kick" data-olo-editable="kicker"><?php echo esc_html( $kicker ); ?></span><?php endif; ?>
                            <?php if ( $heading !== '' ) : ?><h2 class="olo-scrub__title" data-olo-editable="heading"><?php echo esc_html( $heading ); ?></h2><?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="olo-scrub__track" tabindex="0" role="region" aria-label="<?php echo esc_attr( $heading !== '' ? $heading : olobuild_t( 'Galleria a scorrimento orizzontale' ) ); ?>">
                        <?php if ( $total === 0 ) : ?>
                            <div class="olo-scrub__item">
                                <div class="olo-scrub__media"><span class="ph"><?php echo esc_html( olobuild_t( 'Immagine' ) ); ?></span></div>
                                <div class="olo-scrub__body">
                                    <?php if ( $show_num ) : ?><div class="olo-scrub__num">01</div><?php endif; ?>
                                    <h3 class="olo-scrub__itemtitle"><?php echo esc_html( olobuild_t( 'Aggiungi un elemento' ) ); ?></h3>
                                    <p class="olo-scrub__text"><?php echo esc_html( olobuild_t( 'Usa il pannello a destra per aggiungere gli elementi del nastro.' ) ); ?></p>
                                </div>
                            </div>
                        <?php else : ?>
                            <?php foreach ( $items as $i => $item ) :
                                $title     = isset( $item['title'] ) ? (string) $item['title'] : '';
                                $subtitle  = isset( $item['subtitle'] ) ? (string) $item['subtitle'] : '';
                                $text_raw  = isset( $item['text'] ) ? (string) $item['text'] : '';
                                $media     = isset( $item['media'] ) ? (string) $item['media'] : '';
                                $media_lbl = isset( $item['media_label'] ) ? (string) $item['media_label'] : '';
                                $item_bg   = $this->safe_color_css( $item['color'] ?? '' );
                                $item_txt  = $this->safe_color_css( $item['text_color'] ?? '' );

                                // Testo: HTML pulito (Tiptap) o testo semplice
                                $text_html = ( $text_raw !== '' && preg_match( '/<[a-z!\/][^>]*>/i', $text_raw ) )
                                    ? $this->safe_richtext_content( $text_raw )
                                    : ( $text_raw !== '' ? '<p>' . nl2br( esc_html( $text_raw ) ) . '</p>' : '' );

                                $item_style = '';
                                if ( $item_bg )  { $item_style .= 'background:' . $item_bg . ';'; }
                                if ( $item_txt ) { $item_style .= 'color:' . $item_txt . ';'; }

                                $num = str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT );
                            ?>
                                <div class="olo-scrub__item olo-scrub__item--<?php echo intval( $i ); ?>"<?php echo $item_style ? ' style="' . esc_attr( $item_style ) . '"' : ''; ?>>
                                    <div class="olo-scrub__media">
                                        <?php if ( $media !== '' ) : ?>
                                            <img src="<?php echo esc_url( $media ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" />
                                        <?php elseif ( $media_lbl !== '' ) : ?>
                                            <span class="ph" data-olo-editable="items.<?php echo intval( $i ); ?>.media_label"><?php echo esc_html( $media_lbl ); ?></span>
                                        <?php else : ?>
                                            <span class="ph"><?php echo esc_html( olobuild_t( 'Immagine' ) ); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="olo-scrub__body">
                                        <?php if ( $show_num ) : ?>
                                            <div class="olo-scrub__num"><?php echo esc_html( $num ); ?></div>
                                        <?php endif; ?>
                                        <?php if ( $title !== '' ) : ?>
                                            <h3 class="olo-scrub__itemtitle" data-olo-editable="items.<?php echo intval( $i ); ?>.title"><?php echo esc_html( $title ); ?></h3>
                                        <?php endif; ?>
                                        <?php if ( $subtitle !== '' ) : ?>
                                            <div class="olo-scrub__sub" data-olo-editable="items.<?php echo intval( $i ); ?>.subtitle"><?php echo esc_html( $subtitle ); ?></div>
                                        <?php endif; ?>
                                        <?php if ( $text_html !== '' ) : ?>
                                            <div class="olo-scrub__text" data-olo-editable="items.<?php echo intval( $i ); ?>.text" data-olo-richtext><?php echo $text_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitized above via safe_richtext_content() (wp_kses_post) or built from esc_html() + nl2br() ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <?php if ( $show_prog ) : ?>
                    <div class="olo-scrub__bar" aria-hidden="true"><i></i></div>
                    <?php endif; ?>
                    <div class="olo-scrub__hint" aria-hidden="true">⇄ scorri</div>
                </div>
            </div>
        </div>

        <?php if ( ! $in_builder ) : // canvas builder: niente pin, stato base compatto ?>
        <script>
        /* ScrollScrub — runtime INLINE, scoped per istanza, idempotente, multi-istanza.
           Rif. 62-tema-libreria-indie.html (pinned horizontal shelf) e 35-tema-immobiliare.html.

           STATO BASE (SSR): la traccia è già uno scroll orizzontale nativo, usabile senza JS,
           da tastiera, su mobile e con reduced-motion. Questo IIFE *promuove* l'esperienza al
           "pin verticale → translateX" solo dove ha senso:
             p = clamp((-rect.top)/(outer.h - vh), 0..1);  track.x = -p*(track.scrollW - vw).
           Ricalcola la corsa massima su resize. scroll listener passive. IntersectionObserver
           aggancia/sgancia lo scroll per non far girare nulla fuori viewport. */
        (function(){
            var root = document.querySelector('.<?php echo esc_js( $uid ); ?>');
            if ( ! root ) { return; }
            if ( root.dataset.oloScrub ) { return; }   // una sola init per istanza
            root.dataset.oloScrub = '1';

            var outer = root.querySelector('.olo-scrub__outer');
            var pin   = root.querySelector('.olo-scrub__pin');
            var track = root.querySelector('.olo-scrub__track');
            if ( ! outer || ! pin || ! track ) { return; }

            var bar = root.querySelector('.olo-scrub__bar > i');

            var RESPECT_RM = <?php echo $respect_rm ? 'true' : 'false'; ?>;
            var BEHAVIOR   = '<?php echo esc_js( $behavior ); ?>';

            // Condizioni per cui restiamo allo scroll orizzontale NATIVO (no pin):
            //  - prefers-reduced-motion (se l'opzione lo rispetta)
            //  - viewport stretto / touch primario (mobile): il pin ruba lo scroll verticale
            //  - sticky non supportato
            var rm = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)');
            var small = window.matchMedia && window.matchMedia('(max-width: 880px)');
            var coarse = window.matchMedia && window.matchMedia('(hover: none) and (pointer: coarse)');

            function stickyOK(){
                try {
                    return !!( window.CSS && CSS.supports && (
                        CSS.supports('position','sticky') || CSS.supports('position','-webkit-sticky')
                    ) );
                } catch ( e ) { return false; }
            }

            var pinned = false;     // pin attualmente attivo?
            var maxX = 0;           // corsa orizzontale massima (track.scrollW - vw)
            var pinTop = 0;         // padding-top del pin = altezza dell'header fisso/sticky (0 se assente)
            var listening = false;  // scroll listener agganciato?
            var ticking = false;    // throttle via rAF

            function shouldPin(){
                if ( BEHAVIOR === 'inline' ) { return false; }   // scelta esplicita: mai pin
                if ( RESPECT_RM && rm && rm.matches ) { return false; }
                if ( small && small.matches ) { return false; }
                if ( coarse && coarse.matches ) { return false; }
                if ( ! stickyOK() ) { return false; }
                // Inutile pinnare se la traccia non eccede la viewport
                return ( track.scrollWidth - window.innerWidth ) > 4;
            }

            // Altezza dell'header sticky/fixed in cima alla pagina (megamenu, nav
            // del tema…): con align=start il contenuto parte subito SOTTO l'header.
            function hdrOffset(){
                var cands = document.querySelectorAll( 'header, nav, .olo-megamenu' );
                for ( var i = 0; i < cands.length; i++ ) {
                    var cs = getComputedStyle( cands[i] );
                    if ( 'sticky' === cs.position || 'fixed' === cs.position ) {
                        var r = cands[i].getBoundingClientRect();
                        if ( r.top <= 1 && r.height > 0 && r.height < 220 ) { return Math.round( r.height ); }
                    }
                }
                return 0;
            }

            function recalc(){
                maxX = Math.max( 0, track.scrollWidth - window.innerWidth );
                pinTop = hdrOffset();
                if ( pinned ) { pin.style.paddingTop = pinTop + 'px'; }
            }

            function update(){
                ticking = false;
                if ( ! pinned ) { return; }
                var total = outer.offsetHeight - window.innerHeight;
                if ( total <= 0 ) { return; }
                var top = outer.getBoundingClientRect().top;
                var p = ( -top ) / total;
                if ( p < 0 ) { p = 0; } else if ( p > 1 ) { p = 1; }
                track.style.transform = 'translateX(' + ( -( p * maxX ) ) + 'px)';
                if ( bar ) { bar.style.width = ( p * 100 ) + '%'; }
            }

            function onScroll(){
                if ( ! ticking ) {
                    ticking = true;
                    window.requestAnimationFrame( update );
                }
            }

            function enablePin(){
                if ( pinned ) { return; }
                pinned = true;
                outer.classList.add( 'is-pinned' );
                recalc();
                update();
            }
            function disablePin(){
                if ( ! pinned ) { return; }
                pinned = false;
                outer.classList.remove( 'is-pinned' );
                pin.style.paddingTop = '';
                track.style.transform = '';
                if ( bar ) { bar.style.width = '0'; }
            }

            // (Ri)applica la modalità in base alle media query / dimensioni attuali.
            function applyMode(){
                if ( shouldPin() ) { enablePin(); recalc(); update(); }
                else { disablePin(); }
            }

            // Progress anche senza pin (inline / stato base / mobile): la barra
            // segue lo scroll orizzontale NATIVO della traccia.
            track.addEventListener( 'scroll', function(){
                if ( pinned || ! bar ) { return; }
                var m = track.scrollWidth - track.clientWidth;
                bar.style.width = ( m > 0 ? ( track.scrollLeft / m ) * 100 : 0 ) + '%';
            }, { passive: true } );

            function addScroll(){
                if ( listening ) { return; }
                listening = true;
                window.addEventListener( 'scroll', onScroll, { passive: true } );
            }
            function removeScroll(){
                if ( ! listening ) { return; }
                listening = false;
                window.removeEventListener( 'scroll', onScroll, { passive: true } );
            }

            // Performance: aggancia lo scroll solo quando la sezione è nel viewport.
            if ( 'IntersectionObserver' in window ) {
                var io = new IntersectionObserver( function( entries ){
                    for ( var i = 0; i < entries.length; i++ ) {
                        if ( entries[i].isIntersecting ) { addScroll(); onScroll(); }
                        else { removeScroll(); }
                    }
                }, { threshold: 0 } );
                io.observe( outer );
            } else {
                addScroll();
            }

            // Ricalcolo su resize (debounce) + ri-valutazione modalità.
            var rt = null;
            window.addEventListener( 'resize', function(){
                if ( rt ) { clearTimeout( rt ); }
                rt = setTimeout( function(){ applyMode(); }, 120 );
            }, { passive: true } );

            // Reagisci ai cambi di reduced-motion a runtime (DevTools/OS).
            if ( rm && rm.addEventListener ) {
                rm.addEventListener( 'change', applyMode );
            }

            applyMode();
        })();
        </script>
        <?php endif; ?>

        <?php
        // ── Sistema bordi (come il marquee) — applicato al singolo .olo-scrub__item ──
        $item_sel          = ".{$uid} .olo-scrub__item";
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( $item_sel, $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( $item_sel, $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) { echo "{$item_sel}{{$border_css}}"; } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() (integer-forced widths) for the internally generated selector
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() shared helpers
        }

        return ob_get_clean();
    }
}
