<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Tile CRTOverlay — decoratore di pagina (famiglia E / bucket C).
 *
 * Riferimento: handoff-tile-speciali/temi/60-tema-community-gamer.html
 *   .crt      → repeating-linear-gradient (scanline) + mix-blend-mode:overlay
 *   .vignette → radial-gradient (vignettatura)
 *
 * È un tile renderizzato nel flusso ma che NON occupa spazio: emette due layer
 * `position:fixed; inset:0; pointer-events:none; z-index alto` che decorano
 * l'intera pagina con un look CRT (scanline + vignetta).
 *
 * Contratto §2:
 *   - Parametrico: ogni numero/colore è un campo editor (vedi $defaults / config JS).
 *   - Scoped per istanza: classi + @keyframes prefissati con UID `olo-crtoverlay-<rand>`.
 *     N istanze sulla stessa pagina non si calpestano.
 *   - SSR: l'overlay è già pienamente visibile dal markup/CSS (zero JS necessario).
 *   - prefers-reduced-motion: blocco @media che disattiva il flicker animato →
 *     overlay statico ma leggibile (lo stato finale resta).
 *   - pointer-events:none + aria-hidden: puramente decorativo, non intercetta input
 *     né entra nell'albero di accessibilità; il contenuto dietro resta intatto.
 *   - Additivo: chiavi salvate stabili.
 */
class Olobuild_Crtoverlay_Tile extends Olobuild_Tile_Base {

    protected $type     = 'crtoverlay';
    protected $name     = 'Overlay CRT';
    protected $icon     = 'dashicons-visibility';
    protected $category = 'interactive';
    protected $defaults = [
        // Scanline
        'scanline_opacity'   => 50,
        'scanline_gap'       => 3,
        'scanline_thickness' => 1,
        'scanline_color'     => '',
        // Vignetta
        'vignette'           => 55,
        'vignette_color'     => '',
        'vignette_spread'    => 120,
        // Composizione
        'blend_mode'         => 'overlay',
        // Flicker animato
        'flicker'            => false,
        'flicker_speed'      => 8,
        // Stack
        'z_index'            => 200,
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-crtoverlay-' . wp_rand( 10000, 99999 );

        // ── Scanline ───────────────────────────────────────────────
        // opacità 0–100 → decimale 0..1
        $scan_op_pct = max( 0, min( 100, intval( $s['scanline_opacity'] ) ) );
        $scan_op     = round( $scan_op_pct / 100, 3 );
        $gap         = max( 2, min( 12, intval( $s['scanline_gap'] ) ) );           // passo totale (riga+spazio)
        $thick       = max( 1, min( $gap, intval( $s['scanline_thickness'] ) ) );   // riga chiara (mai > gap)
        // Colore riga: bianco di default (le scanline sono righe chiare in blend overlay)
        $scan_color  = $this->safe_color_css( $s['scanline_color'] ) ?: 'rgba(255,255,255,0.55)';

        // ── Vignetta ───────────────────────────────────────────────
        $vig_pct     = max( 0, min( 100, intval( $s['vignette'] ) ) );
        $vig_alpha   = round( $vig_pct / 100, 3 );
        $vig_spread  = max( 80, min( 160, intval( $s['vignette_spread'] ) ) );
        // Punto di partenza trasparenza vignetta: più alta l'intensità, prima inizia a scurire.
        // mappa 0..100 → stop% in [70..40] (alta intensità = bordo scuro più ampio).
        $vig_stop    = 70 - intval( round( ( $vig_pct / 100 ) * 30 ) );
        // Colore vignetta: quasi nero di default; rispetta token se il colore è scelto come tale.
        $vig_color_base = $this->safe_color_css( $s['vignette_color'] );
        if ( $vig_color_base !== '' ) {
            // L'utente ha scelto un colore esplicito: ne ricavo r,g,b e applico l'alpha della vignetta.
            $vig_rgb   = $this->color_to_rgb( $vig_color_base );
            $vig_color = "rgba({$vig_rgb},{$vig_alpha})";
        } else {
            $vig_color = "rgba(5,3,12,{$vig_alpha})";
        }

        // ── Composizione ───────────────────────────────────────────
        $blend_allowed = [ 'normal', 'multiply', 'overlay', 'screen', 'soft-light' ];
        $blend         = in_array( $s['blend_mode'], $blend_allowed, true ) ? $s['blend_mode'] : 'overlay';

        // ── Flicker ────────────────────────────────────────────────
        $flicker     = ! empty( $s['flicker'] );
        $flick_speed = max( 2, min( 20, intval( $s['flicker_speed'] ) ) );

        // ── Stack ──────────────────────────────────────────────────
        $zidx     = max( 1, min( 9999, intval( $s['z_index'] ) ) );
        $zidx_vig = max( 1, $zidx - 1 ); // la vignetta sta appena sotto le scanline

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: opacities/sizes/z-index via intval() with min()/max() clamps and round(), scanline color via the safe_color_css() whitelist, vignette color rebuilt as rgba() from color_to_rgb() digits and a clamped alpha, blend mode from an in_array() whitelist; $uid is internally generated.
        ?>
        <style>
            /* Scanline retro — repeating-linear-gradient scoped sull'istanza <?php echo esc_html( $uid ); ?> */
            .<?php echo $uid; ?>-scan {
                position: fixed;
                inset: 0;
                z-index: <?php echo $zidx; ?>;
                pointer-events: none;
                opacity: <?php echo $scan_op; ?>;
                mix-blend-mode: <?php echo $blend; ?>;
                background: repeating-linear-gradient(
                    0deg,
                    <?php echo $scan_color; ?> 0,
                    <?php echo $scan_color; ?> <?php echo $thick; ?>px,
                    transparent <?php echo $thick; ?>px,
                    transparent <?php echo $gap; ?>px
                );
            }
            /* Vignettatura — radial-gradient scoped sull'istanza */
            .<?php echo $uid; ?>-vig {
                position: fixed;
                inset: 0;
                z-index: <?php echo $zidx_vig; ?>;
                pointer-events: none;
                background: radial-gradient(
                    <?php echo $vig_spread; ?>% <?php echo $vig_spread; ?>% at 50% 40%,
                    transparent <?php echo $vig_stop; ?>%,
                    <?php echo $vig_color; ?>
                );
            }
            <?php if ( $flicker ) : ?>
            @keyframes <?php echo $uid; ?>-flicker {
                0%, 100% { opacity: <?php echo $scan_op; ?>; }
                48%      { opacity: <?php echo max( 0, round( $scan_op * 0.78, 3 ) ); ?>; }
                50%      { opacity: <?php echo min( 1, round( $scan_op * 1.12, 3 ) ); ?>; }
                52%      { opacity: <?php echo max( 0, round( $scan_op * 0.86, 3 ) ); ?>; }
            }
            .<?php echo $uid; ?>-scan {
                animation: <?php echo $uid; ?>-flicker <?php echo $flick_speed; ?>s steps(60) infinite;
            }
            /* Ramo ridotto: niente sfarfallio, l'overlay resta statico e leggibile */
            @media (prefers-reduced-motion: reduce) {
                .<?php echo $uid; ?>-scan { animation: none; opacity: <?php echo $scan_op; ?>; }
            }
            <?php endif; ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-crtoverlay <?php echo esc_attr( $uid ); ?>-vig" aria-hidden="true" role="presentation"></div>
        <div class="olo-crtoverlay <?php echo esc_attr( $uid ); ?>-scan" aria-hidden="true" role="presentation"></div>
        <?php
        return ob_get_clean();
    }

    /**
     * Grana pellicola — secondo effetto di pagina della famiglia (blueprint
     * "Clod — Evoluzione v2", evo-fx.css [1] .fx-grain): layer fixed full-viewport
     * con noise SVG fractalNoise in mix-blend-mode overlay, animato a scatti
     * (steps) come una pellicola. Statico con prefers-reduced-motion.
     *
     * Statico e parametrico come render(): chiamato dal renderer per i page
     * settings `page_grain_*`, non entra nel flusso tile.
     *
     * @param array $settings { opacity (0-100), size (px), z_index, animate (bool) }
     * @return string HTML
     */
    public static function render_grain( $settings = [] ) {
        $s = wp_parse_args( $settings, [
            'opacity' => 7,
            'size'    => 240,
            'z_index' => 95,
            'animate' => true,
            // Su touch/coarse la grana è OFF di default: layer fixed full-viewport con
            // mix-blend-mode (+ animazione) = composite costoso → scroll a scatti su mobile.
            'mobile'  => false,
        ] );

        $op      = round( max( 0, min( 100, intval( $s['opacity'] ) ) ) / 100, 3 );
        $size    = max( 60, min( 800, intval( $s['size'] ) ) );
        $zidx    = max( 1, min( 9999, intval( $s['z_index'] ) ) );
        $animate = ! empty( $s['animate'] );
        $mobile  = ! empty( $s['mobile'] );
        $uid     = 'olo-grain-' . wp_rand( 10000, 99999 );

        // Noise come data-URI SVG (fractalNoise): nessun asset esterno, nessuna richiesta.
        $noise = "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='240' height='240'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E\")";

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS: tutti i valori dinamici sono intval() clampati / round(); il data-URI è una costante interna; $uid interno.
        ?>
        <style>
            .<?php echo $uid; ?> {
                position: fixed;
                inset: -60px;
                z-index: <?php echo $zidx; ?>;
                pointer-events: none;
                opacity: <?php echo $op; ?>;
                mix-blend-mode: overlay;
                background-image: <?php echo $noise; ?>;
                background-size: <?php echo $size; ?>px <?php echo $size; ?>px;
            }
            <?php if ( $animate ) : ?>
            @media (prefers-reduced-motion: no-preference) {
                .<?php echo $uid; ?> { animation: <?php echo $uid; ?>-anim .5s steps(3) infinite; }
            }
            @keyframes <?php echo $uid; ?>-anim {
                0%, 100% { transform: translate(0, 0); }
                33%      { transform: translate(-28px, 20px); }
                66%      { transform: translate(24px, -24px); }
            }
            <?php endif; ?>
            <?php if ( ! $mobile ) : ?>
            @media (hover: none), (pointer: coarse) {
                .<?php echo $uid; ?> { display: none; }
            }
            <?php endif; ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-pagegrain <?php echo esc_attr( $uid ); ?>" aria-hidden="true" role="presentation"></div>
        <?php
        return ob_get_clean();
    }
}
