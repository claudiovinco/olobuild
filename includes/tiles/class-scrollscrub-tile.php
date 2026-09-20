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
 *     Senza JS resta anche la BARRA del browser, e frecce e indicatori non si mostrano: è il
 *     runtime ad aggiungere `olo-scrub--js` alla radice, e solo quella classe nasconde la barra.
 *     Lo scambio è uno solo — i comandi veri al posto della barra — e o avviene tutto o niente.
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

        // Comandi di scorrimento.
        // La barra di scorrimento del browser era l'unico comando visibile del
        // nastro: sempre accesa, grigia e muta (non dice quanti elementi ci sono
        // né a che punto sei). Ora di fabbrica è spenta — chi la rivuole ha il
        // suo interruttore — e al suo posto ci sono frecce e indicatori.
        'arrows'                  => 'tonde',
        'arrows_pos'              => 'dentro',
        'arrows_size'             => 44,
        'arrows_bg'               => '',
        'arrows_color'            => '',
        'indicators'              => 'pallini',
        'indicators_color'        => '',
        'indicators_active_color' => '',
        'show_scrollbar'          => false,
        'edge_fade'               => true,
        'drag_scroll'             => true,
        'snap_strong'             => false,

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
     * Il segno dentro il pulsante freccia.
     *
     * Chevron dal set SVG della casa — lo stesso tracciato di progallery e
     * panelslider, così le frecce di OloBuild si somigliano tutte. Mai un'emoji
     * né un carattere tipografico («‹ ›» cambia disegno con ogni font e non si
     * può colorare per stato).
     *
     * Il tratto è `currentColor`: il colore lo decide il pulsante, l'icona lo
     * segue — hover e stato spento non devono ridisegnare l'SVG.
     */
    protected function scrub_arrow_svg( $dir ) {
        $points = ( 'prev' === $dir ) ? '15 18 9 12 15 6' : '9 18 15 12 9 6';
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"'
             . ' stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'
             . '<polyline points="' . $points . '"/></svg>';
    }

    /**
     * Un pulsante freccia, pronto da stampare.
     *
     * `<button type="button">`: è un comando, non un link — niente `href` finto
     * che sporca la cronologia, e la tastiera lo attiva con Invio e Spazio senza
     * che si debba scrivere una riga di JS.
     *
     * `data-olo-interactive` è l'opt-in previsto dal ponte dell'iframe: nel canvas
     * del builder quel ponte ascolta i clic in fase di CATTURA e ferma tutto ciò
     * che non riconosce, quindi senza questo attributo il clic sulla freccia non
     * arriverebbe mai al suo gestore — si sarebbe acceso il runtime nel canvas
     * apposta per farle rispondere, e avrebbero risposto solo da tastiera.
     * Sul sito pubblicato l'attributo non fa nulla.
     */
    protected function scrub_arrow_btn( $dir ) {
        $prev  = ( 'prev' === $dir );
        $label = $prev ? olobuild_t( 'Elemento precedente' ) : olobuild_t( 'Elemento successivo' );
        return '<button type="button" class="olo-scrub__arrow olo-scrub__arrow--' . ( $prev ? 'prev' : 'next' ) . '"'
             . ' data-olo-scrub-dir="' . ( $prev ? '-1' : '1' ) . '" data-olo-interactive'
             . ' aria-label="' . esc_attr( $label ) . '">'
             . $this->scrub_arrow_svg( $dir )
             . '</button>';
    }

    /**
     * Le due frecce, sciolte o dentro la pillola.
     */
    protected function scrub_arrows_html( $pill ) {
        $btns = $this->scrub_arrow_btn( 'prev' ) . $this->scrub_arrow_btn( 'next' );
        return $pill ? '<div class="olo-scrub__pill">' . $btns . '</div>' : $btns;
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
        $pad_sides = Olobuild_Tile_Utils::spacing_sides( $s['item_padding'] ?? 0, [], [ 0, 0, 0, 0 ] );
        $pad_css   = Olobuild_Tile_Utils::sides_css( $pad_sides );
        $pad       = array_sum( $pad_sides );
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

        // ── Comandi di scorrimento ──
        // Sta qui, dopo i colori, perché l'indicatore attivo eredita per default
        // il colore della barra di progresso ($prog_c): un solo accento per la
        // tile, non due che litigano.
        $arrows     = in_array( $s['arrows'] ?? '', [ 'none', 'tonde', 'vetro', 'minimale', 'quadrate', 'pillola' ], true ) ? $s['arrows'] : 'tonde';
        $arrows_pos = in_array( $s['arrows_pos'] ?? '', [ 'dentro', 'fuori', 'sotto' ], true ) ? $s['arrows_pos'] : 'dentro';
        $arr_size   = max( 28, min( 72, intval( $s['arrows_size'] ?? 44 ) ) );
        $arr_half   = (int) round( $arr_size / 2 );
        $arr_bg     = $this->safe_color_css( $s['arrows_bg'] ?? '' )    ?: 'var(--olo-color-surface, #ffffff)';
        $indics     = in_array( $s['indicators'] ?? '', [ 'none', 'pallini', 'trattini', 'numeri', 'linea' ], true ) ? $s['indicators'] : 'pallini';

        /* Chi sta SOPRA una card e chi sta sullo SFONDO DELLA SEZIONE non può
           avere lo stesso colore di riserva. `--olo-color-text` è il colore del
           testo DENTRO la card, il cui fondo è `--olo-color-surface`: su una
           sezione scura un indicatore grigio-scuro al 30% non si vede.
           I due gemelli che stanno lì da sempre lo sapevano già — la scrubbar usa
           un grigio neutro, il suggerimento «⇄ scorri» eredita e si attenua — e
           qui si fa la stessa cosa con `currentColor`: chiaro su scuro, scuro su
           chiaro, senza che nessuno debba impostare niente.
           Le frecce con uno sfondo proprio (tonde/vetro/quadrate/pillola) restano
           sul colore del testo: lì il fondo è la superficie, ed è giusto. Solo la
           «minimale», che di sfondo non ne ha, eredita dalla sezione. */
        $su_sezione = ( 'minimale' === $arrows );
        $arr_fg     = $this->safe_color_css( $s['arrows_color'] ?? '' ) ?: ( $su_sezione ? 'currentColor' : 'var(--olo-color-text, #1f2937)' );
        $ind_c      = $this->safe_color_css( $s['indicators_color'] ?? '' )        ?: 'currentColor';
        $ind_on     = $this->safe_color_css( $s['indicators_active_color'] ?? '' ) ?: $prog_c;
        $show_sbar  = ! empty( $s['show_scrollbar'] );
        $edge_fade  = ! empty( $s['edge_fade'] );
        $drag_on    = ! empty( $s['drag_scroll'] );
        $snap_hard  = ! empty( $s['snap_strong'] );

        // Con un solo elemento (o nessuno) non c'è niente da scorrere: frecce
        // sempre spente e un pallino solo sarebbero arredamento, non comandi.
        $has_nav     = $total > 1;
        $show_arrows = $has_nav && 'none' !== $arrows;
        $show_indics = $has_nav && 'none' !== $indics;
        $arrows_bar  = $show_arrows && 'sotto' === $arrows_pos;   // frecce dentro la barra comandi
        $arrows_side = $show_arrows && 'sotto' !== $arrows_pos;   // frecce ai lati del nastro
        $has_ctrl    = $show_indics || $arrows_bar;
        // La pillola è un blocchetto unico: le due frecce stanno dentro un
        // contenitore, non una per lato.
        $arr_pill    = 'pillola' === $arrows;

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: intval()/absint() clamped via max()/min() for every size, safe_color_css() whitelist for every colour, in_array() whitelists for align/easing/arrows/arrows_pos/indicators, build_border_radius_css() (integer-forced), preset shadow map or integer-built custom shadow, fixed ternary literals and the internally generated uid. ?>
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
                scroll-snap-type: x <?php echo $snap_hard ? 'mandatory' : 'proximity'; ?>;
                scrollbar-width: thin;
            }
            <?php if ( ! $show_sbar ) : ?>
            /* La barra del browser sotto il nastro spariva sotto le card e
               restava lì grigia anche a nastro fermo. Si nasconde la barra, NON
               lo scorrimento: dito, trackpad, rotella e tastiera continuano a
               funzionare, e i comandi veri (frecce, indicatori) dicono molto di
               più di quanto dicesse lei.
               MA solo dove i comandi veri esistono davvero. Senza JavaScript
               frecce e pallini sono disegnati e inerti, e togliere anche la barra
               lascerebbe la fila senza NESSUN appiglio: su Firefox la rotella
               verticale non scorre un contenitore orizzontale, e chi ha un mouse
               semplice resterebbe fermo sul primo schermo di card. Da qui la
               classe `olo-scrub--js`, che il runtime aggiunge alla radice prima
               del primo disegno (lo script è inline subito dopo il markup): la
               barra sparisce solo se c'è qualcosa che la sostituisce. */
            .<?php echo $uid; ?>.olo-scrub--js .olo-scrub__track {
                scrollbar-width: none;
                -ms-overflow-style: none;
            }
            .<?php echo $uid; ?>.olo-scrub--js .olo-scrub__track::-webkit-scrollbar { display: none; }
            <?php endif; ?>
            <?php if ( $show_arrows || $has_ctrl ) : ?>
            /* E per lo stesso motivo, al contrario: finché il runtime non c'è, i
               comandi non si mostrano. Un pulsante che non fa niente è peggio di
               un pulsante che manca. */
            .<?php echo $uid; ?>:not(.olo-scrub--js) .olo-scrub__arrow,
            .<?php echo $uid; ?>:not(.olo-scrub--js) .olo-scrub__pill,
            .<?php echo $uid; ?>:not(.olo-scrub--js) .olo-scrub__ctrl { display: none; }
            <?php endif; ?>
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
                /* Rete di sicurezza mobile: la card non supera mai lo schermo
                   (60px = padding traccia + spiraglio della card successiva);
                   i valori per-breakpoint dell'inspector restano sovrani sotto il cap. */
                width: min(<?php echo $item_w; ?>px, calc(100vw - 60px));
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
                padding: <?php echo esc_attr( $overlay ? '20px' : $pad_css ); ?>;
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

            /* ── PALCO e FINESTRA ────────────────────────────────────────────
               Due involucri invece di uno, e il motivo è la sfumatura ai bordi:
               la maschera che sfuma il nastro cancellerebbe anche le frecce se
               vivessero nello stesso elemento. Il PALCO ospita le frecce, la
               FINESTRA (dentro) è ciò che viene sfumato.
               Nessun overflow qui: in pin taglia già il blocco incollato, e
               mettere un ritaglio in più mangerebbe l'ombra delle card. */
            .<?php echo $uid; ?> .olo-scrub__stage,
            .<?php echo $uid; ?> .olo-scrub__viewport { position: relative; }

            <?php if ( $edge_fade ) : ?>
            /* Sfumatura ai bordi: le card svaniscono invece di essere tagliate
               di netto — si legge a colpo d'occhio che il nastro continua.
               mask-size 100% 300% centrata: la maschera deborda in verticale,
               così NON ritaglia le ombre delle card sopra e sotto. */
            .<?php echo $uid; ?> .olo-scrub__viewport {
                --olo-scrub-fade: clamp(24px, 6vw, 72px);
                -webkit-mask-image: linear-gradient(to right, transparent 0, black var(--olo-scrub-fade), black calc(100% - var(--olo-scrub-fade)), transparent 100%);
                mask-image: linear-gradient(to right, transparent 0, black var(--olo-scrub-fade), black calc(100% - var(--olo-scrub-fade)), transparent 100%);
                -webkit-mask-size: 100% 300%;
                mask-size: 100% 300%;
                -webkit-mask-position: center;
                mask-position: center;
                -webkit-mask-repeat: no-repeat;
                mask-repeat: no-repeat;
            }
            /* La maschera cancella tutto ciò che sta nel suo elemento, e l'anello
               di fuoco della traccia sta lì dentro: sporge di 5px a sinistra e a
               destra, cioè esattamente fuori dalla maschera, e le estremità dei
               tratti finiscono nella rampa trasparente. Di un anello intero
               restavano due righe interrotte — sull'elemento più importante per
               chi naviga da tastiera. Lo si disegna quindi sul PALCO, che la
               maschera non tocca. La regola sulla traccia resta per i browser
               senza `:has()`: è meglio un anello tagliato che nessun anello. */
            @supports selector(:has(*)) {
                .<?php echo $uid; ?> .olo-scrub__track:focus-visible { outline: none; }
                .<?php echo $uid; ?> .olo-scrub__stage:has(.olo-scrub__track:focus-visible) {
                    outline: 2px solid var(--olo-color-primary, #e1474f);
                    outline-offset: 3px;
                }
            }
            <?php endif; ?>

            <?php if ( $drag_on ) : ?>
            /* Trascina per scorrere: il cursore lo annuncia PRIMA del clic. */
            .<?php echo $uid; ?> .olo-scrub__track.is-grab { cursor: grab; }
            /* Il browser ha un suo trascinamento per le immagini, e nelle card in
               sovraimpressione l'immagine copre tutta la superficie: partendo da
               lì il nastro faceva un centinaio di pixel e poi si piantava
               (dragstart → pointercancel), con il fantasma della foto appeso al
               cursore. Qui si spegne alla radice; nel runtime c'è anche la
               cintura, per i browser che ignorano questa proprietà. */
            .<?php echo $uid; ?> .olo-scrub__track img { -webkit-user-drag: none; }
            .<?php echo $uid; ?> .olo-scrub__track.is-grabbing {
                cursor: grabbing;
                -webkit-user-select: none;
                user-select: none;
                scroll-snap-type: none;   /* l'aggancio combatte col trascinamento */
            }
            <?php endif; ?>

            <?php if ( $show_arrows ) : ?>
            /* ── FRECCE ───────────────────────────────────────────────────── */
            .<?php echo $uid; ?> .olo-scrub__arrow {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                box-sizing: border-box;
                width: <?php echo $arr_size; ?>px;
                height: <?php echo $arr_size; ?>px;
                padding: 0;
                margin: 0;
                border: 0;
                background: <?php echo $arr_bg; ?>;
                color: <?php echo $arr_fg; ?>;
                cursor: pointer;
                -webkit-appearance: none;
                appearance: none;
                transition: opacity 0.2s ease, transform 0.2s ease, background-color 0.2s ease;
            }
            .<?php echo $uid; ?> .olo-scrub__arrow svg {
                width: 56%;
                height: 56%;
                display: block;
                pointer-events: none;   /* il clic è del pulsante, non del disegno */
            }
            .<?php echo $uid; ?> .olo-scrub__arrow:focus-visible {
                outline: 2px solid var(--olo-color-primary, #e1474f);
                outline-offset: 3px;
            }
            /* Spenta: resta raggiungibile da tastiera (aria-disabled, non
               disabled) ma si vede che da quel lato non c'è più corsa. */
            .<?php echo $uid; ?> .olo-scrub__arrow[aria-disabled="true"] {
                opacity: 0.3;
                cursor: default;
            }

            <?php if ( 'tonde' === $arrows ) : ?>
            .<?php echo $uid; ?> .olo-scrub__arrow {
                border-radius: 50%;
                box-shadow: 0 6px 18px rgba(0,0,0,0.18);
            }
            <?php elseif ( 'vetro' === $arrows ) : ?>
            .<?php echo $uid; ?> .olo-scrub__arrow {
                border-radius: 50%;
                background: color-mix(in srgb, <?php echo $arr_bg; ?> 55%, transparent);
                border: 1px solid color-mix(in srgb, <?php echo $arr_fg; ?> 16%, transparent);
                -webkit-backdrop-filter: blur(10px) saturate(1.4);
                backdrop-filter: blur(10px) saturate(1.4);
                box-shadow: 0 4px 16px rgba(0,0,0,0.12);
            }
            <?php elseif ( 'minimale' === $arrows ) : ?>
            .<?php echo $uid; ?> .olo-scrub__arrow {
                background: none;
                box-shadow: none;
            }
            /* Senza sfondo il segno deve reggersi da solo: più grande, più sottile. */
            .<?php echo $uid; ?> .olo-scrub__arrow svg { width: 82%; height: 82%; stroke-width: 1.6; }
            /* La guardia non è pignoleria: questa regola ha la stessa specificità
               di quella che spegne la freccia a fine corsa e viene dopo, quindi
               senza `:not()` una freccia spenta si RIACCENDE sotto il cursore —
               si schiarisce proprio mentre il clic non fa niente. */
            .<?php echo $uid; ?> .olo-scrub__arrow:hover:not([aria-disabled="true"]) { opacity: 0.65; }
            <?php elseif ( 'quadrate' === $arrows ) : ?>
            /* Stesso raggio delle card: una tile, una sola curva. */
            .<?php echo $uid; ?> .olo-scrub__arrow {
                border-radius: <?php echo $round; ?>;
                box-shadow: 0 6px 18px rgba(0,0,0,0.18);
            }
            <?php elseif ( $arr_pill ) : ?>
            .<?php echo $uid; ?> .olo-scrub__pill {
                display: inline-flex;
                align-items: stretch;
                background: <?php echo $arr_bg; ?>;
                border-radius: <?php echo $arr_size; ?>px;
                box-shadow: 0 6px 18px rgba(0,0,0,0.18);
                overflow: hidden;
            }
            .<?php echo $uid; ?> .olo-scrub__pill .olo-scrub__arrow {
                background: none;
                box-shadow: none;
                border-radius: 0;
            }
            /* Un filo fra le due metà: si capisce che sono due comandi, non uno. */
            .<?php echo $uid; ?> .olo-scrub__pill .olo-scrub__arrow + .olo-scrub__arrow {
                border-left: 1px solid color-mix(in srgb, <?php echo $arr_fg; ?> 16%, transparent);
            }
            .<?php echo $uid; ?> .olo-scrub__pill .olo-scrub__arrow:focus-visible { outline-offset: -3px; }
            <?php endif; ?>

            <?php if ( $arrows_side ) : ?>
            /* Ai lati del nastro: ancorate al palco, centrate sull'altezza card. */
            .<?php echo $uid; ?> .olo-scrub__stage > .olo-scrub__arrow,
            .<?php echo $uid; ?> .olo-scrub__stage > .olo-scrub__pill {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                z-index: 4;
            }
            .<?php echo $uid; ?> .olo-scrub__stage > .olo-scrub__arrow:hover:not([aria-disabled="true"]) {
                transform: translateY(-50%) scale(1.07);
            }
            <?php if ( $arr_pill ) : ?>
            /* La pillola è un blocchetto solo: sta a destra, dove la mano
               cerca il «avanti» — è il verso naturale della lettura. */
            .<?php echo $uid; ?> .olo-scrub__stage > .olo-scrub__pill {
                <?php echo 'fuori' === $arrows_pos
                    ? 'right: 4px;'
                    : 'right: calc(clamp(20px, 8vw, 96px) + 8px);'; ?>
            }
            <?php else : ?>
            <?php if ( 'fuori' === $arrows_pos ) : ?>
            /* Fuori: centrate nel margine laterale, accanto al nastro. */
            .<?php echo $uid; ?> .olo-scrub__stage > .olo-scrub__arrow--prev { left: max(4px, calc((clamp(20px, 8vw, 96px) - <?php echo $arr_size; ?>px) / 2)); }
            .<?php echo $uid; ?> .olo-scrub__stage > .olo-scrub__arrow--next { right: max(4px, calc((clamp(20px, 8vw, 96px) - <?php echo $arr_size; ?>px) / 2)); }
            /* Sotto i 960px il margine si stringe a 20px: lì «fuori» non ci sta
               più e la freccia rientra a cavallo del bordo della card. */
            @media (max-width: 960px) {
                .<?php echo $uid; ?> .olo-scrub__stage > .olo-scrub__arrow--prev { left: max(4px, calc(clamp(20px, 8vw, 96px) - <?php echo $arr_half; ?>px)); }
                .<?php echo $uid; ?> .olo-scrub__stage > .olo-scrub__arrow--next { right: max(4px, calc(clamp(20px, 8vw, 96px) - <?php echo $arr_half; ?>px)); }
            }
            <?php else : ?>
            /* Dentro: a cavallo del bordo della card, copre poco contenuto.
               Il max(4px) è la rete per i piccoli schermi: lì il margine scende a
               20px e senza freno la freccia uscirebbe a sinistra dello schermo. */
            .<?php echo $uid; ?> .olo-scrub__stage > .olo-scrub__arrow--prev { left: max(4px, calc(clamp(20px, 8vw, 96px) - <?php echo $arr_half; ?>px)); }
            .<?php echo $uid; ?> .olo-scrub__stage > .olo-scrub__arrow--next { right: max(4px, calc(clamp(20px, 8vw, 96px) - <?php echo $arr_half; ?>px)); }
            <?php endif; ?>
            <?php endif; ?>
            <?php endif; ?>
            <?php endif; ?>

            <?php if ( $has_ctrl ) : ?>
            /* ── BARRA COMANDI (indicatori + eventuali frecce «sotto») ────── */
            .<?php echo $uid; ?> .olo-scrub__ctrl {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 16px;
                padding: 14px clamp(20px, 8vw, 96px) 2px;
            }
            /* In pin il blocco è alto tutto lo schermo e la fila è centrata: i
               comandi vanno ancorati in basso. A 56px stanno SOPRA la scrubbar
               (34px) e il suggerimento (18px), senza accavallarsi a nessuno. */
            .<?php echo $uid; ?> .olo-scrub__outer.is-pinned .olo-scrub__ctrl {
                position: absolute;
                left: clamp(20px, 8vw, 96px);
                right: clamp(20px, 8vw, 96px);
                bottom: 56px;
                padding: 0;
            }
            /* Una riga sola, sempre. Andando a capo, in pin, la barra è ancorata
               in basso e quindi cresce verso l'ALTO: due file di pallini si
               stendono sopra l'ultima riga di card. Con molti elementi la fila
               scorre invece di spezzarsi, e il runtime tiene in vista il pallino
               acceso (la barra del browser qui sarebbe rumore: si nasconde). */
            .<?php echo $uid; ?> .olo-scrub__dots {
                display: flex;
                align-items: center;
                /* `safe center`: una fila centrata che sborda mette metà
                   dell'eccedenza a SINISTRA, dove lo scorrimento non arriva — il
                   primo pallino resta tagliato e irraggiungibile (misurato a
                   375px con 14 elementi). Con `safe` il centraggio si arrende
                   quando non c'è spazio e la fila riparte dal bordo. La riga
                   sopra resta per i browser che non conoscono la parola. */
                justify-content: center;
                justify-content: safe center;
                gap: 4px;
                flex-wrap: nowrap;
                max-width: 100%;
                overflow-x: auto;
                scrollbar-width: none;
                -ms-overflow-style: none;
                scroll-behavior: smooth;
            }
            .<?php echo $uid; ?> .olo-scrub__dots::-webkit-scrollbar { display: none; }
            /* Il BERSAGLIO e il DISEGNO sono due cose diverse. Un pallino di 10px
               (e ancor più un trattino alto 3) è sotto il minimo di 24×24 che
               chiede la WCAG 2.2 (SC 2.5.8), e con 9px di spazio saltava anche
               l'eccezione sulla distanza fra i centri: su telefono si sbaglia
               quasi sempre. Il pulsante è quindi un quadrato di 24px invisibile,
               e il pallino lo disegna un `::after` centrato dentro. */
            .<?php echo $uid; ?> .olo-scrub__dot {
                display: flex;
                align-items: center;
                justify-content: center;
                flex: 0 0 auto;
                box-sizing: border-box;
                min-width: 24px;
                height: 24px;
                padding: 0;
                border: 0;
                background: none;
                /* Un <button> NON eredita il colore: il browser gli dà il suo
                   `buttontext`, cioè nero. Senza questa riga il `currentColor`
                   del pallino qui sotto resta nero su qualsiasi sezione — ed è
                   proprio quello che stiamo cercando di evitare. Misurato: la
                   linea e il contatore, che non sono pulsanti, seguivano già la
                   sezione; i pallini no. */
                color: inherit;
                cursor: pointer;
                -webkit-appearance: none;
                appearance: none;
            }
            /* L'attenuazione si fa con l'OPACITÀ, non mescolando l'alfa nel
               colore. `color-mix(in srgb, currentColor 30%, transparent)` sembra
               la strada giusta e non lo è: dentro color-mix il currentColor viene
               risolto al momento del calcolo, quando il colore ereditato non c'è
               ancora, e diventa NERO — misurato: su una sezione scura il pallino
               spento restava nero al 30%, cioè invisibile, che è esattamente il
               difetto da togliere. `background: currentColor` invece si risolve a
               valle e il colore della sezione arriva davvero. */
            .<?php echo $uid; ?> .olo-scrub__dot::after {
                content: '';
                display: block;
                background: <?php echo $ind_c; ?>;
                opacity: 0.32;
                transition: background-color 0.25s ease, opacity 0.25s ease, width 0.25s ease, transform 0.25s ease;
            }
            .<?php echo $uid; ?> .olo-scrub__dot:focus-visible {
                outline: 2px solid var(--olo-color-primary, #e1474f);
                outline-offset: 3px;
            }
            <?php if ( 'pallini' === $indics ) : ?>
            .<?php echo $uid; ?> .olo-scrub__dot::after { width: 10px; height: 10px; border-radius: 50%; }
            .<?php echo $uid; ?> .olo-scrub__dot[aria-current="true"]::after {
                background: <?php echo $ind_on; ?>;
                opacity: 1;
                transform: scale(1.35);
            }
            <?php elseif ( 'trattini' === $indics ) : ?>
            .<?php echo $uid; ?> .olo-scrub__dot { min-width: 26px; }
            .<?php echo $uid; ?> .olo-scrub__dot::after { width: 22px; height: 3px; border-radius: 3px; }
            .<?php echo $uid; ?> .olo-scrub__dot[aria-current="true"] { min-width: 44px; }
            .<?php echo $uid; ?> .olo-scrub__dot[aria-current="true"]::after {
                background: <?php echo $ind_on; ?>;
                opacity: 1;
                width: 40px;
            }
            <?php elseif ( 'numeri' === $indics ) : ?>
            /* Un contatore, non una scorciatoia: dice a che punto sei. Le
               tabular-nums evitano che la riga balli cambiando cifra. */
            .<?php echo $uid; ?> .olo-scrub__count {
                font-size: 13px;
                font-weight: 600;
                letter-spacing: 0.12em;
                font-variant-numeric: tabular-nums;
                color: <?php echo $ind_c; ?>;
            }
            /* L'attenuazione sta sul SOLO totale, in uno span suo: messa sul
               contenitore formerebbe un gruppo e spegnerebbe anche il numero
               corrente, che deve restare pieno. (E vale qui la stessa ragione dei
               pallini: niente color-mix con currentColor.) */
            .<?php echo $uid; ?> .olo-scrub__count span { opacity: 0.6; }
            .<?php echo $uid; ?> .olo-scrub__count b {
                color: <?php echo $ind_on; ?>;
                font-weight: 700;
            }
            <?php elseif ( 'linea' === $indics ) : ?>
            /* La rotaia attenuata è uno pseudo-elemento e non il fondo del
               contenitore: l'opacità sul contenitore spegnerebbe anche il
               riempimento, che è suo figlio. */
            .<?php echo $uid; ?> .olo-scrub__line {
                position: relative;
                flex: 0 1 220px;
                height: 3px;
                border-radius: 3px;
                overflow: hidden;
            }
            .<?php echo $uid; ?> .olo-scrub__line::before {
                content: '';
                position: absolute;
                inset: 0;
                border-radius: 3px;
                background: <?php echo $ind_c; ?>;
                opacity: 0.24;
            }
            .<?php echo $uid; ?> .olo-scrub__line i {
                position: relative;
                display: block;
                height: 100%;
                width: 0;
                border-radius: 3px;
                background: <?php echo $ind_on; ?>;
            }
            <?php endif; ?>
            <?php endif; ?>

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
                <?php if ( $has_ctrl ) : ?>
                /* I comandi NON sono decorazione: con «riduci movimento» il pin
                   sparisce ma frecce e indicatori restano, e tornano in fila
                   sotto il nastro come nello stato base. */
                .<?php echo $uid; ?> .olo-scrub__outer.is-pinned .olo-scrub__ctrl {
                    position: static;
                    padding: 14px clamp(20px, 8vw, 96px) 2px;
                }
                <?php endif; ?>
            }
            <?php endif; ?>

            <?php if ( $show_arrows || $has_ctrl ) : ?>
            /* Questo blocco è INCONDIZIONATO, e non è una svista: «Rispetta
               riduci movimento» è l'interruttore del PIN, e con quello spento
               restavano comunque il pallino che si ingrandisce, il trattino che
               si allunga e la freccia che scatta in avanti sotto il cursore.
               La preferenza di sistema parla di animazioni, non di ancoraggi: va
               onorata sempre. L'indicatore acceso si distingue col colore, che
               non si muove. */
            @media (prefers-reduced-motion: reduce) {
                .<?php echo $uid; ?> .olo-scrub__arrow,
                .<?php echo $uid; ?> .olo-scrub__dot,
                .<?php echo $uid; ?> .olo-scrub__dot::after { transition: none; }
                .<?php echo $uid; ?> .olo-scrub__dots { scroll-behavior: auto; }
                .<?php echo $uid; ?> .olo-scrub__stage > .olo-scrub__arrow:hover:not([aria-disabled="true"]) {
                    transform: translateY(-50%);
                }
                .<?php echo $uid; ?> .olo-scrub__dot[aria-current="true"]::after { transform: none; }
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
                if ( $sc_iw !== '' ) { $sc_decls .= 'width:min(' . max( 120, min( 900, absint( $sc_iw ) ) ) . 'px, calc(100vw - 60px));'; }
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

                    <div class="olo-scrub__stage">
                    <div class="olo-scrub__viewport">
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
                    </div><?php // /track ?>
                    </div><?php // /viewport — la sfumatura vive qui, le frecce fuori ?>
                    <?php if ( $arrows_side ) {
                        echo $this->scrub_arrows_html( $arr_pill ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup built from fixed literals, the SVG set and esc_attr()'d labels
                    } ?>
                    </div><?php // /stage ?>

                    <?php if ( $has_ctrl ) : ?>
                    <div class="olo-scrub__ctrl">
                        <?php
                        // Ordine: la pillola tiene già le due frecce insieme e va
                        // in coda; sciolte, abbracciano gli indicatori.
                        if ( $arrows_bar && ! $arr_pill ) {
                            echo $this->scrub_arrow_btn( 'prev' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- see scrub_arrow_btn()
                        }
                        ?>
                        <?php if ( $show_indics ) : ?>
                            <?php if ( 'pallini' === $indics || 'trattini' === $indics ) : ?>
                                <div class="olo-scrub__dots" role="group" aria-label="<?php echo esc_attr( olobuild_t( 'Vai a un elemento' ) ); ?>">
                                    <?php for ( $d = 0; $d < $total; $d++ ) : ?>
                                        <?php // data-olo-interactive: vedi scrub_arrow_btn() — senza, nel canvas del builder il clic non arriva. ?>
                                        <button type="button" class="olo-scrub__dot" data-olo-scrub-go="<?php echo intval( $d ); ?>" data-olo-interactive
                                            aria-label="<?php echo esc_attr( sprintf( olobuild_t( 'Elemento %d' ), $d + 1 ) ); ?>"
                                            <?php echo 0 === $d ? ' aria-current="true"' : ''; ?>></button>
                                    <?php endfor; ?>
                                </div>
                            <?php elseif ( 'numeri' === $indics ) : ?>
                                <?php // Contatore: aiuto VISIVO. aria-hidden perché cambia a ogni
                                      // pixel di scorrimento e un lettore di schermo lo annuncerebbe
                                      // in continuazione; il nastro resta leggibile dai suoi elementi. ?>
                                <div class="olo-scrub__count" aria-hidden="true"><b>01</b><span> / <?php echo esc_html( str_pad( (string) $total, 2, '0', STR_PAD_LEFT ) ); ?></span></div>
                            <?php elseif ( 'linea' === $indics ) : ?>
                                <div class="olo-scrub__line" aria-hidden="true"><i></i></div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php
                        if ( $arrows_bar && ! $arr_pill ) {
                            echo $this->scrub_arrow_btn( 'next' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- see scrub_arrow_btn()
                        } elseif ( $arrows_bar ) {
                            echo $this->scrub_arrows_html( true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- see scrub_arrows_html()
                        }
                        ?>
                    </div>
                    <?php endif; ?>

                    <?php if ( $show_prog ) : ?>
                    <div class="olo-scrub__bar" aria-hidden="true"><i></i></div>
                    <?php endif; ?>
                    <div class="olo-scrub__hint" aria-hidden="true">⇄ scorri</div>
                </div>
            </div>
        </div>

        <script>
        /* ScrollScrub — runtime INLINE, scoped per istanza, idempotente, multi-istanza.
           Rif. 62-tema-libreria-indie.html (pinned horizontal shelf) e 35-tema-immobiliare.html.

           STATO BASE (SSR): la traccia è già uno scroll orizzontale nativo, usabile senza JS,
           da tastiera, su mobile e con reduced-motion. Questo IIFE *promuove* l'esperienza al
           "pin verticale → translateX" solo dove ha senso:
             p = clamp((-rect.top)/(outer.h - vh), 0..1);  track.x = -p*(track.scrollW - vw).
           Ricalcola la corsa massima su resize. scroll listener passive. IntersectionObserver
           aggancia/sgancia lo scroll per non far girare nulla fuori viewport.

           NEL CANVAS DEL BUILDER lo script ORA gira lo stesso, con PIN_ALLOWED a false.
           Il pin no di sicuro (l'iframe è alto quanto la pagina, il suo "100vh" è enorme e
           la sezione diventerebbe un mostro), ma frecce e indicatori sì: chi li sta
           scegliendo dall'ispettore deve vederli rispondere, non solo vederli disegnati.
           Senza pin non serve nessun listener su window — e non ne agganciamo: il canvas
           ridisegna la tile a ogni modifica e un listener globale per render si accumulerebbe.
           Le misure si rifanno a ogni comando, che nel canvas è più che sufficiente. */
        (function(){
            var root = document.querySelector('.<?php echo esc_js( $uid ); ?>');
            if ( ! root ) { return; }
            if ( root.dataset.oloScrub ) { return; }   // una sola init per istanza
            root.dataset.oloScrub = '1';

            var outer = root.querySelector('.olo-scrub__outer');
            var pin   = root.querySelector('.olo-scrub__pin');
            var track = root.querySelector('.olo-scrub__track');
            if ( ! outer || ! pin || ! track ) { return; }

            // La finestra: il riquadro che si vede del nastro. È anche l'elemento
            // da cui gli item contano il loro offsetLeft (è lui il positioned più
            // vicino), quindi le due misure parlano già la stessa lingua.
            var vp = root.querySelector('.olo-scrub__viewport') || track;

            var bar = root.querySelector('.olo-scrub__bar > i');

            var RESPECT_RM  = <?php echo $respect_rm ? 'true' : 'false'; ?>;
            var BEHAVIOR    = '<?php echo esc_js( $behavior ); ?>';
            var PIN_ALLOWED = <?php echo $in_builder ? 'false' : 'true'; ?>;
            var DRAG_OK     = <?php echo ( $drag_on && ! $in_builder ) ? 'true' : 'false'; ?>;

            // I comandi: frecce (una per verso, o due dentro la pillola) e
            // indicatori. Se un comando non è stato richiesto, la lista è vuota
            // e tutto il codice che segue gira a vuoto senza doverlo sapere.
            var arrowEls = root.querySelectorAll('[data-olo-scrub-dir]');
            var dotEls   = root.querySelectorAll('[data-olo-scrub-go]');
            var dotsEl   = root.querySelector('.olo-scrub__dots');
            var countEl  = root.querySelector('.olo-scrub__count b');
            var lineEl   = root.querySelector('.olo-scrub__line > i');
            var itemEls  = track.querySelectorAll('.olo-scrub__item');

            /* Da qui in poi i comandi esistono davvero: il CSS può nascondere la
               barra del browser e mostrare frecce e pallini. Prima di questa riga
               erano disegnati e inerti, e la barra era l'unico appiglio rimasto a
               chi ha JavaScript spento. Siamo inline subito dopo il markup: la
               classe arriva prima del primo disegno, niente lampeggio. */
            root.classList.add( 'olo-scrub--js' );

            // Salto secco invece di scorrimento morbido quando il sistema dice
            // «meno movimento»: vale sempre, anche con il pin attivo — quella
            // preferenza parla di animazioni, non di ancoraggi.
            var SMOOTH_OK = false;
            try { SMOOTH_OK = ( 'scrollBehavior' in document.documentElement.style ); } catch ( e ) {}

            // Condizioni per cui restiamo allo scroll orizzontale NATIVO (no pin):
            //  - prefers-reduced-motion (se l'opzione lo rispetta)
            //  - sticky non supportato
            // NB: il pin vale ANCHE su mobile/touch (richiesta esplicita: stesso
            // comportamento del desktop — lo scroll di pagina guida la fila; è
            // scroll nativo + sticky + transform, nessuna intercettazione touch).
            var rm = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)');

            function stickyOK(){
                try {
                    return !!( window.CSS && CSS.supports && (
                        CSS.supports('position','sticky') || CSS.supports('position','-webkit-sticky')
                    ) );
                } catch ( e ) { return false; }
            }

            var pinned = false;     // pin attualmente attivo?
            var progress = 0;       // p dell'ultimo frame: 0 = inizio corsa, 1 = fine
            var maxX = 0;           // corsa orizzontale massima (track.scrollW - vw)
            var pinTop = 0;         // padding-top del pin = altezza dell'header fisso/sticky (0 se assente)
            var listening = false;  // scroll listener agganciato?
            var ticking = false;    // throttle via rAF

            function shouldPin(){
                if ( ! PIN_ALLOWED ) { return false; }           // canvas del builder
                if ( BEHAVIOR === 'inline' ) { return false; }   // scelta esplicita: mai pin
                if ( RESPECT_RM && rm && rm.matches ) { return false; }
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
                measure();
            }

            function update(){
                ticking = false;
                if ( ! pinned ) { return; }
                var total = outer.offsetHeight - window.innerHeight;
                if ( total <= 0 ) { return; }
                var top = outer.getBoundingClientRect().top;
                var p = ( -top ) / total;
                if ( p < 0 ) { p = 0; } else if ( p > 1 ) { p = 1; }
                progress = p;
                track.style.transform = 'translateX(' + ( -( p * maxX ) ) + 'px)';
                if ( bar ) { bar.style.width = ( p * 100 ) + '%'; }
                sync();
            }

            /* ═══ COMANDI DI SCORRIMENTO ═══════════════════════════════════════
               Le due modalità muovono il nastro in due modi opposti, e i comandi
               devono funzionare in entrambe senza saperlo. Il trucco è un'unica
               grandezza, la CORSA: quanti pixel di nastro sono già passati.
                 · nativo → corsa = track.scrollLeft
                 · pin    → corsa = progress * maxX   (progress lo calcola update())
               Ogni comando calcola la corsa che vuole; a tradurla nella modalità
               giusta ci pensa goToRun(), una volta sola. */

            var stops = [];   // per ogni elemento: la corsa che lo porta al centro

            function viewW(){ return pinned ? window.innerWidth : track.clientWidth; }
            function runMax(){ return pinned ? maxX : Math.max( 0, track.scrollWidth - track.clientWidth ); }
            function runNow(){ return pinned ? ( progress * maxX ) : track.scrollLeft; }
            function clampRun( x ){ var m = runMax(); return x < 0 ? 0 : ( x > m ? m : x ); }
            function behav(){ return ( rm && rm.matches ) ? 'auto' : 'smooth'; }

            /* Geometria del nastro, misurata di rado e messa da parte: rileggerla
               a ogni frame costringerebbe il browser a rifare il layout mentre
               scorre. offsetLeft e offsetWidth non cambiano con lo scorrimento.

               Centrare l'elemento i vuol dire portare la corsa a
               offsetLeft + metà elemento − metà finestra. Cambia solo da dove si
               conta: nello scroll nativo l'origine è la traccia stessa (scrollLeft
               parte da lì), in pin è lo SCHERMO — perché maxX si misura in
               innerWidth — e se la sezione non è a tutta larghezza il bordo
               sinistro della finestra non sta a zero e va aggiunto. */
            function measure(){
                stops = [];
                if ( ! itemEls.length ) { return; }
                var orig  = pinned ? vp.getBoundingClientRect().left : 0;
                var mezzo = viewW() / 2;
                for ( var i = 0; i < itemEls.length; i++ ) {
                    stops.push( orig + itemEls[i].offsetLeft + ( itemEls[i].offsetWidth / 2 ) - mezzo );
                }
            }

            function goToRun( x ){
                x = clampRun( x );
                if ( ! pinned ) {
                    if ( SMOOTH_OK ) { track.scrollTo({ left: x, behavior: behav() }); }
                    else { track.scrollLeft = x; }
                    return;
                }
                /* In pin la traccia non si scorre: la muove translateX in funzione
                   dello scroll di PAGINA. Per portarla alla corsa x bisogna quindi
                   muovere la PAGINA, ed è l'inversa esatta della riga di update():
                     update():  x = p * maxX,  p = -rect.top / (outer.h - vh)
                     qui:       p = x / maxX,  scrollY = (cima di outer) + p*(outer.h - vh)
                   Senza corsa (un solo schermo di card, o pagina più corta della
                   sezione) non c'è nessun posto dove andare: meglio non muoversi
                   che dividere per zero e saltare in cima alla pagina. */
                var run = outer.offsetHeight - window.innerHeight;
                if ( maxX <= 0 || run <= 0 ) { return; }
                var y = window.pageYOffset + outer.getBoundingClientRect().top + ( x / maxX ) * run;
                if ( SMOOTH_OK ) { window.scrollTo({ top: y, behavior: behav() }); }
                else { window.scrollTo( 0, y ); }
            }

            // Un clic = un elemento: si cerca il primo fermo OLTRE la posizione
            // attuale nel verso chiesto. Cercare il fermo invece di sommare
            // "larghezza + gap" regge anche quando le card non sono tutte uguali
            // (larghezze per breakpoint, ultima card più stretta…).
            function goRel( dir ){
                measure();
                var x = runNow(), i, t;
                for ( var k = 0; k < stops.length; k++ ) {
                    i = dir > 0 ? k : ( stops.length - 1 - k );
                    t = clampRun( stops[i] );
                    if ( dir > 0 ? ( t > x + 1 ) : ( t < x - 1 ) ) { goToRun( t ); return; }
                }
                // Nessun fermo più in là: resta la coda di corsa fino al bordo.
                goToRun( dir > 0 ? runMax() : 0 );
            }

            function goToIndex( i ){
                measure();
                if ( ! stops.length ) { return; }
                if ( i < 0 ) { i = 0; } else if ( i >= stops.length ) { i = stops.length - 1; }
                goToRun( stops[i] );
            }

            // Quale elemento è "quello di adesso". Ai due estremi la risposta è
            // secca — la corsa si ferma prima che l'ultima card arrivi al centro,
            // e senza questo l'ultimo pallino non si accenderebbe mai.
            function activeIndex(){
                var x = runNow(), m = runMax();
                if ( ! stops.length ) { return 0; }
                if ( x <= 0.5 ) { return 0; }
                if ( x >= m - 0.5 ) { return stops.length - 1; }
                var best = 0, bd = Infinity, i, d;
                for ( i = 0; i < stops.length; i++ ) {
                    d = Math.abs( stops[i] - x );
                    if ( d < bd ) { bd = d; best = i; }
                }
                return best;
            }

            /* Allinea lo STATO dei comandi alla posizione del nastro. In pin gira
               a OGNI frame di scroll, quindi non legge nulla dal layout (corsa e
               massimo sono già in memoria) e soprattutto non scrive niente se
               niente è cambiato: riscrivere gli stessi attributi sessanta volte
               al secondo farebbe ricalcolare lo stile dei pallini per nulla. */
            var vistoSpente = '', vistoAttivo = -1;

            /* Una freccia è spenta quando premerla non porterebbe da nessuna
               parte — e la domanda ha una sola risposta giusta: quella che darebbe
               goRel() se la premessimo. Misurare invece la distanza dal fondo
               della corsa sbagliava nello scroll nativo, dove l'ultimo fermo non
               coincide col massimo assoluto della traccia (l'aggancio e i valori
               frazionari di scrollLeft lasciano qualche pixel di coda): arrivati
               sull'ultima card la freccia «avanti» restava accesa e un clic in più
               spostava il nastro di due pixel. Nessuna lettura dal layout: `stops`
               è già in memoria, e qui si passa a ogni frame di scroll. */
            function fineCorsa( dir, x, m ){
                if ( m <= 0 ) { return true; }
                for ( var i = 0; i < stops.length; i++ ) {
                    // Clamp a mano invece di clampRun(): quello richiama runMax(),
                    // che fuori dal pin legge il layout — una volta per fermo, a
                    // ogni frame. Il massimo ce l'abbiamo già come argomento.
                    var t = stops[i] < 0 ? 0 : ( stops[i] > m ? m : stops[i] );
                    if ( dir > 0 ? ( t > x + 2 ) : ( t < x - 2 ) ) { return false; }
                }
                // Nessun fermo più in là: resta solo la coda fino al bordo.
                return dir > 0 ? ( x >= m - 2 ) : ( x <= 2 );
            }

            function sync(){
                var x = runNow(), m = runMax(), i, dir, spente = '';
                for ( i = 0; i < arrowEls.length; i++ ) {
                    dir = arrowEls[i].getAttribute( 'data-olo-scrub-dir' ) === '-1' ? -1 : 1;
                    spente += fineCorsa( dir, x, m ) ? '1' : '0';
                }
                if ( spente !== vistoSpente ) {
                    vistoSpente = spente;
                    for ( i = 0; i < arrowEls.length; i++ ) {
                        // aria-disabled e non disabled: il pulsante resta nel giro
                        // dei TAB, così chi naviga da tastiera non se lo vede
                        // sparire e ricomparire sotto le dita.
                        arrowEls[i].setAttribute( 'aria-disabled', spente.charAt( i ) === '1' ? 'true' : 'false' );
                    }
                }

                if ( lineEl ) { lineEl.style.width = ( m > 0 ? ( x / m ) * 100 : 0 ) + '%'; }
                if ( ! dotEls.length && ! countEl ) { return; }

                var a = activeIndex();
                if ( a === vistoAttivo ) { return; }
                vistoAttivo = a;
                for ( i = 0; i < dotEls.length; i++ ) {
                    if ( i === a ) { dotEls[i].setAttribute( 'aria-current', 'true' ); }
                    else { dotEls[i].removeAttribute( 'aria-current' ); }
                }
                // Con molti elementi la fila non va a capo, scorre: il pallino
                // acceso va tenuto in vista, o indica una posizione che non si
                // vede. Aritmetica sul suo scrollLeft, non scrollIntoView, che
                // trascinerebbe anche la PAGINA.
                if ( dotsEl && dotEls.length && dotEls[ a ] && dotsEl.scrollWidth > dotsEl.clientWidth + 1 ) {
                    dotsEl.scrollLeft = dotEls[ a ].offsetLeft + ( dotEls[ a ].offsetWidth / 2 ) - ( dotsEl.clientWidth / 2 );
                }
                if ( countEl ) { countEl.textContent = ( a + 1 < 10 ? '0' : '' ) + ( a + 1 ); }
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
                if ( ! pinned ) {
                    // Anche senza cambio di modalità i comandi vanno allineati:
                    // è il caso dello stato base, dove disablePin() è l'unica
                    // strada che porta qui alla prima apertura.
                    measure();
                    sync();
                    armCursor();
                    return;
                }
                pinned = false;
                progress = 0;
                outer.classList.remove( 'is-pinned' );
                pin.style.paddingTop = '';
                track.style.transform = '';
                if ( bar ) { bar.style.width = '0'; }
                measure();
                sync();
                armCursor();
            }

            // (Ri)applica la modalità in base alle media query / dimensioni attuali.
            function applyMode(){
                if ( shouldPin() ) { enablePin(); recalc(); update(); armCursor(); }
                else { disablePin(); }
            }

            // Progress anche senza pin (inline / stato base / mobile): la barra
            // segue lo scroll orizzontale NATIVO della traccia. Strozzato con un
            // rAF perché ora aggiorna anche pallini e contatore, e lo scroll
            // nativo spara eventi molto più in fretta di quanto si disegni.
            var tScroll = false;
            track.addEventListener( 'scroll', function(){
                if ( pinned || tScroll ) { return; }
                tScroll = true;
                window.requestAnimationFrame( function(){
                    tScroll = false;
                    if ( pinned ) { return; }
                    if ( bar ) {
                        var m = track.scrollWidth - track.clientWidth;
                        bar.style.width = ( m > 0 ? ( track.scrollLeft / m ) * 100 : 0 ) + '%';
                    }
                    sync();
                } );
            }, { passive: true } );

            /* ── Frecce e indicatori ─────────────────────────────────────────── */
            for ( var ai = 0; ai < arrowEls.length; ai++ ) {
                arrowEls[ ai ].addEventListener( 'click', function( e ){
                    e.preventDefault();
                    // Spenta: il pulsante resta a fuoco ma non fa niente — non c'è
                    // più corsa da quel lato e fingere un movimento sarebbe peggio.
                    if ( this.getAttribute( 'aria-disabled' ) === 'true' ) { return; }
                    goRel( this.getAttribute( 'data-olo-scrub-dir' ) === '-1' ? -1 : 1 );
                } );
            }
            for ( var di = 0; di < dotEls.length; di++ ) {
                dotEls[ di ].addEventListener( 'click', function( e ){
                    e.preventDefault();
                    goToIndex( parseInt( this.getAttribute( 'data-olo-scrub-go' ), 10 ) || 0 );
                } );
            }

            /* ── Tastiera ────────────────────────────────────────────────────
               La traccia è già focusabile (tabindex). Le frecce spostano di un
               elemento in ENTRAMBE le modalità: nello stato nativo il browser
               scorrerebbe di una manciata di pixel e in pin non farebbe nulla —
               così invece il tasto fa esattamente quello che fa il clic sulla
               freccia, e il comando da imparare resta uno solo. */
            track.addEventListener( 'keydown', function( e ){
                if ( e.altKey || e.ctrlKey || e.metaKey ) { return; }
                // Se il fuoco è su un testo che si sta scrivendo, le frecce sono
                // del cursore: nel canvas del builder i titoli delle card
                // diventano modificabili sul posto, e rubargli i tasti
                // renderebbe impossibile correggere una parola.
                var t = e.target;
                if ( t && t.closest && t.closest( 'input, textarea, select, [contenteditable="true"], [contenteditable=""]' ) ) { return; }
                var k = e.key;
                if ( k === 'ArrowRight' ) { e.preventDefault(); goRel( 1 ); }
                else if ( k === 'ArrowLeft' ) { e.preventDefault(); goRel( -1 ); }
                else if ( k === 'Home' ) { e.preventDefault(); goToRun( 0 ); }
                else if ( k === 'End' ) { e.preventDefault(); goToRun( runMax() ); }
            } );

            /* ── Trascina per scorrere ───────────────────────────────────────
               Solo col MOUSE e solo quando la fila si scorre da sé. Il dito ha
               già il suo scorrimento nativo, con l'inerzia che una riscrittura in
               JS perderebbe; e in pin è lo scroll di pagina a comandare, quindi
               il trascinamento sarebbe un secondo padrone dello stesso nastro. */
            var dragging = false, dragX = 0, dragL = 0, dragMoved = 0, dragId = null;

            function armCursor(){
                if ( ! DRAG_OK ) { return; }
                var ok = ( ! pinned && runMax() > 0 );
                if ( ok ) { track.classList.add( 'is-grab' ); }
                else { track.classList.remove( 'is-grab' ); }
            }

            if ( DRAG_OK && window.PointerEvent ) {
                track.addEventListener( 'pointerdown', function( e ){
                    if ( e.pointerType !== 'mouse' || e.button !== 0 ) { return; }
                    if ( pinned || runMax() <= 0 ) { return; }
                    // Un comando dentro la card resta un comando: non lo si trascina via.
                    if ( e.target && e.target.closest && e.target.closest( 'a, button, input, textarea, select, [contenteditable="true"]' ) ) { return; }
                    dragging = true;
                    dragMoved = 0;
                    dragId = e.pointerId;
                    dragX = e.clientX;
                    dragL = track.scrollLeft;
                    track.classList.add( 'is-grabbing' );
                    try { track.setPointerCapture( dragId ); } catch ( err ) {}
                } );
                track.addEventListener( 'pointermove', function( e ){
                    if ( ! dragging ) { return; }
                    var dx = e.clientX - dragX;
                    if ( Math.abs( dx ) > dragMoved ) { dragMoved = Math.abs( dx ); }
                    track.scrollLeft = dragL - dx;
                }, { passive: true } );
                track.addEventListener( 'pointerup', endDrag );
                track.addEventListener( 'pointercancel', endDrag );
                /* Il browser ha un suo trascinamento per le immagini e, se parte,
                   annulla il nostro puntatore: il nastro faceva un centinaio di
                   pixel e si piantava, con la foto fantasma appesa al cursore.
                   Nelle card in sovraimpressione l'immagine copre tutta la
                   superficie, quindi succedeva quasi sempre. Lo si ferma solo
                   mentre si sta trascinando: fuori dalla presa, trascinare
                   un'immagine resta una cosa che la gente fa. */
                track.addEventListener( 'dragstart', function( e ){
                    if ( dragging ) { e.preventDefault(); }
                } );
                // Dopo un trascinamento vero il click che segue NON è un clic: se
                // non lo si ferma, lasciare la presa sopra un link porta via dalla
                // pagina senza che nessuno l'abbia chiesto.
                track.addEventListener( 'click', function( e ){
                    if ( dragMoved > 6 ) { e.preventDefault(); e.stopPropagation(); dragMoved = 0; }
                }, true );
            }

            function endDrag(){
                if ( ! dragging ) { return; }
                dragging = false;
                track.classList.remove( 'is-grabbing' );
                try { if ( dragId !== null ) { track.releasePointerCapture( dragId ); } } catch ( err ) {}
                dragId = null;
            }

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

            /* Tutto ciò che si aggancia a window vale solo dove il pin può
               esistere. Nel canvas del builder la tile viene ridisegnata a ogni
               modifica dell'ispettore: un listener globale per render resterebbe
               appeso a un nodo che non c'è più. Lì le misure si rifanno a ogni
               comando (goRel/goToIndex chiamano measure()), che basta e avanza. */
            if ( PIN_ALLOWED ) {
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
            }

            applyMode();
        })();
        </script>

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
