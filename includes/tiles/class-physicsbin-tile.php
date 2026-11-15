<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Tile PhysicsBin — giocattoli trascinabili con gravità, rimbalzo e collisioni.
 *
 * Famiglia C / bucket C — rif. handoff-tile-speciali/temi/69-tema-toy-store.html
 * (blocco "tiny physics bin"). Il runtime è uno snippet portato dal tema e reso
 * multi-istanza, idempotente, con IntersectionObserver e prefers-reduced-motion.
 *
 * Sul telefono (1.4.993, Ti Consiglia: «il gioco dello stronzio sul cellulare non funziona»): prima il trascinamento
 * era spento sui dispositivi touch e il cesto, con touch-action:none, bloccava anche lo scorrimento della pagina. Ora
 * gli oggetti si trascinano col dito (touch-action:none solo su di loro) e sul fondo del cesto la pagina scorre.
 * «Scuoti» (pulsante, e sul telefono il telefono scosso) lancia in aria gli oggetti; con «Gli elementi si legano» gli
 * oggetti col simbolo di un elemento si legano secondo la valenza e sopra ogni molecola compare la formula.
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
            [ 'shape' => 'circle', 'color' => 'var(--olo-color-primary, #E63E3E)', 'radius' => '46', 'glyph' => '★', 'image' => '' ],
            [ 'shape' => 'square', 'color' => 'var(--olo-color-primary, #2E6BE6)', 'radius' => '40', 'glyph' => 'A', 'image' => '' ],
            [ 'shape' => 'circle', 'color' => 'var(--olo-color-primary, #F4B400)', 'radius' => '34', 'glyph' => '',  'image' => '' ],
            [ 'shape' => 'circle', 'color' => 'var(--olo-color-primary, #2BA65A)', 'radius' => '50', 'glyph' => 'B', 'image' => '' ],
            [ 'shape' => 'square', 'color' => 'var(--olo-color-primary, #8B53D6)', 'radius' => '36', 'glyph' => 'C', 'image' => '' ],
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
        // La scritta in alto a sinistra: vuota = «↯ trascina & lancia» (com'era), scritta = quel testo e basta.
        'hint_text'   => '',
        // Il pulsante «Scuoti» (e il telefono scosso) e i legami fra gli elementi (1.4.993): spenti = com'era.
        'shake'       => false,
        'shake_label' => '',
        'chemistry'   => false,
        // I nomi delle molecole: [ formula, nome ]. Una molecola senza nome mostra solo la formula.
        'molecules'   => [],

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
        $shake       = ! empty( $s['shake'] );
        $chemistry   = ! empty( $s['chemistry'] );

        // ── Nomi delle molecole (solo testo) ───────────────────────────────
        $molecules = [];
        if ( $chemistry && is_array( $s['molecules'] ) ) {
            foreach ( $s['molecules'] as $m ) {
                if ( ! is_array( $m ) ) continue;
                $f = trim( mb_substr( wp_strip_all_tags( (string) ( $m['formula'] ?? '' ) ), 0, 24 ) );
                $n = trim( mb_substr( wp_strip_all_tags( (string) ( $m['name'] ?? '' ) ), 0, 60 ) );
                if ( '' !== $f ) {
                    $molecules[] = [ 'f' => $f, 'n' => $n ];
                }
            }
        }

        // ── Aspetto cesto ──────────────────────────────────────────────────
        $height = max( 160, min( 1000, intval( $s['height'] ) ) );
        $radius = $this->build_border_radius_css( $s['border_radius'] ?? 0 );
        $bg     = $this->safe_color_css( $s['bg_color'] );

        // ── Oggetti (cap a max_items) ──────────────────────────────────────
        $items_raw = is_array( $s['items'] ) ? $s['items'] : [];
        $items = [];
        foreach ( $items_raw as $it ) {
            if ( ! is_array( $it ) ) continue;
            $shape = in_array( ( $it['shape'] ?? 'circle' ), [ 'circle', 'square', 'star' ], true ) ? ( $it['shape'] ?? 'circle' ) : 'circle';
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
                /* sul fondo del cesto il dito scorre la pagina; sugli oggetti li trascina (touch-action:none lì) */
                touch-action: pan-y;
                <?php if ( $bg ) : ?>
                background: <?php echo $bg; ?>;
                <?php else : ?>
                background: repeating-linear-gradient(135deg, var(--olo-color-surface-alt, #F6E9CE) 0 22px, var(--olo-color-surface, #F1E2C2) 22px 44px);
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
                /* Pillola nel fondo del sito e scritta nel suo testo (1.4.991): col bianco fisso e il grigio tenue del
                   tema, su un tema scuro la scritta (chiara su bianco) non si leggeva. */
                color: var(--olo-color-text, #3A3128);
                pointer-events: none;
                background: color-mix(in srgb, var(--olo-color-background, #FFFFFF) 78%, transparent);
                padding: 5px 10px;
                border-radius: 100px;
            }
            .<?php echo $uid; ?> .olo-pb-toy {
                position: absolute;
                top: 0; left: 0;
                z-index: 2;
                display: grid;
                place-items: center;
                border-radius: 50%;
                cursor: grab;
                touch-action: none;
                will-change: transform;
                user-select: none;
                -webkit-user-select: none;
                -webkit-tap-highlight-color: transparent;
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
            <?php if ( $shake ) : ?>
            .<?php echo $uid; ?> .olo-pb-shake {
                position: absolute;
                top: 11px; right: 12px;
                z-index: 6;
                display: inline-flex;
                align-items: center;
                gap: .55em;
                min-height: 34px;
                padding: .62em 1.25em .62em 1em;
                font: inherit;
                font-size: 11px;
                font-weight: 600;
                letter-spacing: .1em;
                text-transform: uppercase;
                color: var(--olo-color-text, #3A3128);
                background: color-mix(in srgb, var(--olo-color-background, #FFFFFF) 84%, transparent);
                border: 1px solid color-mix(in srgb, var(--olo-color-text, #3A3128) 22%, transparent);
                border-radius: 99em;
                cursor: pointer;
                -webkit-tap-highlight-color: transparent;
                transition: background-color .2s ease, border-color .2s ease;
            }
            .<?php echo $uid; ?> .olo-pb-shake:hover {
                background: color-mix(in srgb, var(--olo-color-background, #FFFFFF) 96%, transparent);
                border-color: color-mix(in srgb, var(--olo-color-text, #3A3128) 45%, transparent);
            }
            .<?php echo $uid; ?> .olo-pb-shake:focus-visible {
                outline: 3px solid var(--olo-color-primary, #e1474f);
                outline-offset: 2px;
            }
            .<?php echo $uid; ?> .olo-pb-shake svg { width: 15px; height: 15px; flex: none; }
            .<?php echo $uid; ?>.olo-pb-scosso { animation: <?php echo $uid; ?>-scuoti .5s cubic-bezier(.36,.07,.19,.97); }
            @keyframes <?php echo $uid; ?>-scuoti {
                0%, 100% { transform: translate(0, 0) rotate(0); }
                15% { transform: translate(-9px, 2px) rotate(-1.2deg); }
                30% { transform: translate(8px, -3px) rotate(1deg); }
                45% { transform: translate(-6px, 2px) rotate(-.7deg); }
                60% { transform: translate(5px, -1px) rotate(.5deg); }
                80% { transform: translate(-2px, 1px) rotate(-.2deg); }
            }
            <?php endif; ?>
            <?php if ( $chemistry ) : ?>
            /* i legami: un bastoncino fra i centri, sotto gli atomi (si vede nello spazio fra i due); doppio e triplo */
            .<?php echo $uid; ?> .olo-pb-bond {
                --olo-pb-filo: color-mix(in srgb, var(--olo-color-text, #3A3128) 58%, transparent);
                position: absolute;
                left: 0;
                z-index: 1;
                width: 100px;
                height: 3px;
                top: -1.5px;
                background: var(--olo-pb-filo);
                border-radius: 2px;
                transform-origin: 0 50%;
                pointer-events: none;
            }
            .<?php echo $uid; ?> .olo-pb-bond.o2 {
                height: 9px; top: -4.5px; background: none;
                border-top: 2px solid var(--olo-pb-filo); border-bottom: 2px solid var(--olo-pb-filo); border-radius: 0;
            }
            .<?php echo $uid; ?> .olo-pb-bond.o3 {
                height: 11px; top: -5.5px; border-radius: 0;
                background: linear-gradient(var(--olo-pb-filo), var(--olo-pb-filo)) center / 100% 2px no-repeat;
                border-top: 2px solid var(--olo-pb-filo); border-bottom: 2px solid var(--olo-pb-filo);
            }
            /* la formula sopra ogni molecola */
            .<?php echo $uid; ?> .olo-pb-mol {
                position: absolute;
                top: 0; left: 0;
                z-index: 4;
                padding: .24em .8em;
                font-size: 12.5px;
                font-weight: 600;
                line-height: 1.35;
                white-space: nowrap;
                color: var(--olo-color-text, #3A3128);
                background: color-mix(in srgb, var(--olo-color-background, #FFFFFF) 86%, transparent);
                border-radius: 99em;
                pointer-events: none;
                transition: opacity .25s ease;
            }
            .<?php echo $uid; ?> .olo-pb-mol sub { font-size: .72em; line-height: 0; }
            .<?php echo $uid; ?> .olo-pb-mol span { font-weight: 400; opacity: .82; }
            <?php endif; ?>
            /* prefers-reduced-motion → niente loop fisico: oggetti fermi, cursore neutro, niente «Scuoti» */
            @media (prefers-reduced-motion: reduce) {
                .<?php echo $uid; ?> .olo-pb-toy { cursor: default; transition: none; }
                .<?php echo $uid; ?> .olo-pb-shake { display: none; }
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <div class="olo-physicsbin <?php echo esc_attr( $uid ); ?>" data-olo-pb
             role="group" aria-label="<?php echo esc_attr__( 'Cesto interattivo: trascina e lancia i giocattoli', 'olobuild' ); ?>">
            <?php $hint = trim( wp_strip_all_tags( (string) ( $s['hint_text'] ?? '' ) ) ); ?>
            <span class="olo-pb-lab" aria-hidden="true"><?php
                if ( '' === $hint ) {
                    echo '&#8623; ' . esc_html__( 'trascina & lancia', 'olobuild' );
                } else {
                    echo esc_html( $hint );
                }
            ?></span>
            <?php if ( $shake && $count > 0 ) :
                $shake_label = trim( wp_strip_all_tags( (string) ( $s['shake_label'] ?? '' ) ) );
                if ( '' === $shake_label ) {
                    $shake_label = __( 'Scuoti', 'olobuild' );
                } ?>
            <button type="button" class="olo-pb-shake"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="8" y="3" width="8" height="18" rx="2"/><path d="M4.5 8.5c-1 1-1 6 0 7M19.5 8.5c1 1 1 6 0 7M2 6.5c-1.4 2-1.4 9 0 11M22 6.5c1.4 2 1.4 9 0 11"/></svg><?php echo esc_html( $shake_label ); ?></button>
            <?php endif; ?>
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
           Drag/throw via Pointer Events (mouse e dito), gravità, collisioni coppia-coppia con risposta impulsiva,
           «Scuoti» e legami chimici. Idempotente, multi-istanza, IO per spegnere il loop fuori viewport. */
        (function(){
            var bin = document.querySelector('.<?php echo esc_js( $uid ); ?>');
            if ( ! bin ) { return; }
            if ( bin.dataset.oloPbInit ) { return; }   // idempotente: una sola init per istanza
            bin.dataset.oloPbInit = '1';

            // prefers-reduced-motion → nessun loop: resta la disposizione statica SSR
            var rm = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)');
            if ( rm && rm.matches ) { return; }

            var coarse = window.matchMedia && (window.matchMedia('(hover:none)').matches || window.matchMedia('(pointer:coarse)').matches);

            var G    = <?php echo json_encode( $gravity ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- float clamped via floatval()+min()/max() above, JSON-encoded ?>;
            var REST = <?php echo json_encode( $restitution ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- float clamped via floatval()+min()/max() above, JSON-encoded ?>;
            var FR   = <?php echo json_encode( $friction ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- float clamped via floatval()+min()/max() above, JSON-encoded ?>;
            var WALLS = <?php echo json_encode( $walls ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- boolean from !empty(), JSON-encoded ?>;
            var SPAWN = <?php echo json_encode( $spawn ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed 'grid'/'random' literal from the ternary above, JSON-encoded ?>;
            var SHAKE = <?php echo json_encode( $shake ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- boolean from !empty(), JSON-encoded ?>;
            var CHEM  = <?php echo json_encode( $chemistry ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- boolean from !empty(), JSON-encoded ?>;
            var MOLS  = <?php echo wp_json_encode( $molecules, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plain strings stripped of tags above, JSON-encoded with HEX flags ?>;

            // ── Gli elementi: valenza (quanti legami fa), elettronegatività (l'ordine nella formula), metalli (fra loro
            //    non si legano). Un simbolo che non è qui (o un glifo qualsiasi) resta un oggetto libero.
            var VAL = { H:1, He:0, Li:1, Be:2, B:3, C:4, N:3, O:2, F:1, Ne:0, Na:1, Mg:2, Al:3, Si:4, P:3, S:2, Cl:1, Ar:0,
                        K:1, Ca:2, Fe:2, Cu:2, Zn:2, Br:1, Kr:0, Ag:1, Sr:2, I:1, Xe:0, Cs:1, Ba:2, Au:1, Hg:2, Pb:2, Rn:0, U:4 };
            var EN  = { H:2.2, Li:0.98, Be:1.57, B:2.04, C:2.55, N:3.04, O:3.44, F:3.98, Na:0.93, Mg:1.31, Al:1.61, Si:1.9,
                        P:2.19, S:2.58, Cl:3.16, K:0.82, Ca:1, Fe:1.83, Cu:1.9, Zn:1.65, Br:2.96, Ag:1.93, Sr:0.95, I:2.66,
                        Cs:0.79, Ba:0.89, Au:2.54, Hg:2, Pb:2.33, U:1.38 };
            var METALLI = { Li:1, Be:1, Na:1, Mg:1, Al:1, K:1, Ca:1, Fe:1, Cu:1, Zn:1, Ag:1, Sr:1, Cs:1, Ba:1, Au:1, Hg:1, Pb:1, U:1 };
            // un metallo si lega solo con ossigeno, zolfo, alogeni e idrogeno (ossidi, sali, idruri): niente «CNa»
            var CON_METALLI = { O:1, S:1, F:1, Cl:1, Br:1, I:1, H:1 };

            var W = 0, H = 0, K = 0, toys = [], bonds = [];
            var els = bin.querySelectorAll('.olo-pb-toy');
            // Sui cesti stretti (il telefono) gli oggetti si rimpiccioliscono in proporzione, fino al 65%: a 390 px
            // quattordici oggetti grandi riempivano il cesto.
            function misura( t ) {
                t.r = t.r0 * K;
                t.el.style.width = t.el.style.height = ( t.r * 2 ) + 'px';
                t.el.style.fontSize = Math.round( t.r * 0.8 ) + 'px';
            }
            function size() {
                W = bin.clientWidth; H = bin.clientHeight;
                var k = Math.max( 0.65, Math.min( 1, W / 640 ) );
                if ( Math.abs( k - K ) > 0.01 ) { K = k; toys.forEach( misura ); }
            }
            size();
            window.addEventListener('resize', size, { passive: true });

            // Il simbolo si legge su ogni colore (1.4.993): bianco sugli oggetti scuri, sui chiari il colore stesso quasi
            // nero. Prima era sempre bianco, e su un oro o un giallo chiaro «H» e «O» non si leggevano.
            function luce( c ) {
                var m = /rgba?\(\s*([\d.]+)[,\s]+([\d.]+)[,\s]+([\d.]+)/.exec( c || '' );
                if ( ! m ) { return 0; }
                var f = function ( v ) { v /= 255; return v <= 0.03928 ? v / 12.92 : Math.pow( ( v + 0.055 ) / 1.055, 2.4 ); };
                return 0.2126 * f( +m[1] ) + 0.7152 * f( +m[2] ) + 0.0722 * f( +m[3] );
            }

            // Costruisce lo stato fisico dai .toy già renderizzati (SSR) — niente nodi nuovi.
            var n = els.length;
            var gcols = Math.max(1, Math.ceil(Math.sqrt(n)));
            for ( var i = 0; i < n; i++ ) {
                var el = els[i];
                var r0 = parseFloat( el.getAttribute('data-r') ) || 30;
                var r  = r0 * K;
                var x, y;
                if ( SPAWN === 'grid' ) {
                    var col = i % gcols, rw = Math.floor( i / gcols );
                    x = ( W / (gcols + 1) ) * ( col + 1 );
                    y = 40 + rw * ( r * 2 + 10 );
                } else {
                    x = r + Math.random() * Math.max( 1, W - r * 2 );
                    y = 30 + Math.random() * 120;
                }
                var sym = ( el.textContent || '' ).trim();
                var val = CHEM && /^[A-Z][a-z]?$/.test( sym ) && VAL[ sym ] != null ? VAL[ sym ] : 0;
                var t = { el: el, r0: r0, r: r, x: x, y: y, vx: (Math.random() - 0.5) * 6, vy: 0, drag: false,
                          sym: val > 0 ? sym : '', val: val, liberi: val, con: [] };
                misura( t );
                if ( ! el.querySelector('img') ) {
                    var fondo = getComputedStyle( el ).backgroundColor;
                    if ( luce( fondo ) > 0.2 ) { el.style.color = 'color-mix(in srgb, ' + fondo + ' 22%, black)'; }
                }
                // il JS guida via transform: via il posizionamento statico SSR
                el.style.left = '0'; el.style.top = '0';
                toys.push( t );
            }

            // ── Legami ──────────────────────────────────────────────────────
            // Due atomi che si toccano si legano se a entrambi resta valenza libera (mai due metalli): il legame
            // prende tutta la valenza che possono dare insieme, fino a triplo (O=O, O=C=O, N≡N). Scuotendo i legami si
            // rompono e per un attimo non se ne formano: gli atomi si sparpagliano e si ricombinano in un altro modo.
            var quieteFino = 0, cambiato = true;
            function gap() { return 12 * K; }
            function lega( A, B ) {
                var o = Math.min( A.liberi, B.liberi, 3 );
                A.liberi -= o; B.liberi -= o;
                A.con.push( B ); B.con.push( A );
                var e = document.createElement('span');
                e.className = 'olo-pb-bond o' + o;
                e.setAttribute('aria-hidden', 'true');
                bin.appendChild( e );
                bonds.push({ a: A, b: B, o: o, el: e });
                cambiato = true;
            }
            function rompiTutti() {
                for ( var i = 0; i < bonds.length; i++ ) { bonds[i].el.remove(); }
                bonds = [];
                for ( var j = 0; j < toys.length; j++ ) { toys[j].liberi = toys[j].val; toys[j].con = []; }
                cambiato = true;
            }
            function puoLegarsi( A, B ) {
                if ( ! CHEM || A.liberi < 1 || B.liberi < 1 ) { return false; }
                if ( METALLI[ A.sym ] ) { return !! CON_METALLI[ B.sym ]; }
                if ( METALLI[ B.sym ] ) { return !! CON_METALLI[ A.sym ]; }
                return true;
            }

            // ── Le formule: dai nomi del pannello (anche Sr(OH)2, H₂O) la composizione; senza nome la formula si
            //    scrive da sé: col carbonio nell'ordine di Hill (C, H, poi alfabetico), senza per elettronegatività
            //    (NaCl, H2O, SrCl2), con l'idrogeno in fondo accanto ad azoto e fosforo (NH3).
            function conta( f ) {
                f = String( f ).replace( /[₀-₉]/g, function ( c ) { return String( c.charCodeAt(0) - 8320 ); } );
                var pila = [ {} ], i = 0, m;
                while ( i < f.length ) {
                    var c = f.charAt( i );
                    if ( c === '(' ) { pila.push( {} ); i++; continue; }
                    if ( c === ')' ) {
                        i++;
                        m = /^\d+/.exec( f.slice( i ) );
                        var k = m ? parseInt( m[0], 10 ) : 1;
                        if ( m ) { i += m[0].length; }
                        var su = pila.length > 1 ? pila.pop() : {};
                        for ( var s in su ) { pila[ pila.length - 1 ][ s ] = ( pila[ pila.length - 1 ][ s ] || 0 ) + su[ s ] * k; }
                        continue;
                    }
                    m = /^([A-Z][a-z]?)(\d*)/.exec( f.slice( i ) );
                    if ( ! m ) { i++; continue; }
                    var top = pila[ pila.length - 1 ];
                    top[ m[1] ] = ( top[ m[1] ] || 0 ) + ( m[2] ? parseInt( m[2], 10 ) : 1 );
                    i += m[0].length;
                }
                return pila[0];
            }
            function chiave( conti ) {
                return Object.keys( conti ).sort().map( function ( s ) { return s + conti[ s ]; } ).join( '' );
            }
            var NOMI = {};
            for ( var q = 0; q < MOLS.length; q++ ) {
                var cq = chiave( conta( MOLS[q].f ) );
                if ( cq && ! NOMI[ cq ] ) { NOMI[ cq ] = MOLS[q]; }
            }
            function formulaDa( conti ) {
                var simboli = Object.keys( conti ), hInFondo = ! conti.C && ( conti.N || conti.P );
                simboli.sort( function ( a, b ) {
                    if ( conti.C ) {
                        var pa = a === 'C' ? 0 : ( a === 'H' ? 1 : 2 ), pb = b === 'C' ? 0 : ( b === 'H' ? 1 : 2 );
                        return pa - pb || ( a < b ? -1 : a > b ? 1 : 0 );
                    }
                    var ea = a === 'H' && hInFondo ? 99 : ( EN[ a ] || 2 ), eb = b === 'H' && hInFondo ? 99 : ( EN[ b ] || 2 );
                    return ea - eb || ( a < b ? -1 : a > b ? 1 : 0 );
                });
                return simboli.map( function ( s ) { return s + ( conti[ s ] > 1 ? conti[ s ] : '' ); } ).join( '' );
            }
            // la formula con i numeri in pedice e il nome accanto (solo testo, mai HTML dal pannello)
            function scriviEtichetta( e, formula, nome ) {
                while ( e.firstChild ) { e.removeChild( e.firstChild ); }
                var parti = formula.split( /(\d+)/ );
                for ( var i = 0; i < parti.length; i++ ) {
                    if ( ! parti[i] ) { continue; }
                    if ( /^\d+$/.test( parti[i] ) ) {
                        var sb = document.createElement('sub'); sb.textContent = parti[i]; e.appendChild( sb );
                    } else {
                        e.appendChild( document.createTextNode( parti[i] ) );
                    }
                }
                if ( nome ) {
                    var sp = document.createElement('span'); sp.textContent = ' · ' + nome; e.appendChild( sp );
                }
            }
            var gruppi = [], etichette = [];
            function ricalcolaMolecole() {
                cambiato = false;
                var visti = [], g = [];
                for ( var i = 0; i < toys.length; i++ ) {
                    if ( visti.indexOf( toys[i] ) >= 0 || ! toys[i].con.length ) { continue; }
                    var coda = [ toys[i] ], gruppo = [];
                    visti.push( toys[i] );
                    while ( coda.length ) {
                        var t = coda.shift(); gruppo.push( t );
                        for ( var j = 0; j < t.con.length; j++ ) {
                            if ( visti.indexOf( t.con[j] ) < 0 ) { visti.push( t.con[j] ); coda.push( t.con[j] ); }
                        }
                    }
                    // la formula solo sulle molecole complete (nessun atomo con legami liberi): un frammento come
                    // «CH» o «HO» aspetta ancora i suoi atomi
                    if ( gruppo.some( function ( a ) { return a.liberi > 0; } ) ) { continue; }
                    var conti = {};
                    gruppo.forEach( function ( a ) { conti[ a.sym ] = ( conti[ a.sym ] || 0 ) + 1; } );
                    var noto = NOMI[ chiave( conti ) ];
                    g.push({ atomi: gruppo, testo: noto ? noto.f : formulaDa( conti ), nome: noto ? noto.n : '' });
                }
                gruppi = g;
                while ( etichette.length < gruppi.length ) {
                    var e = document.createElement('span');
                    e.className = 'olo-pb-mol';
                    e.setAttribute('aria-hidden', 'true');
                    bin.appendChild( e );
                    etichette.push({ el: e, testo: null });
                }
                for ( var k = 0; k < etichette.length; k++ ) {
                    var et = etichette[k], gr = gruppi[k];
                    if ( ! gr ) { et.el.style.display = 'none'; et.testo = null; continue; }
                    et.el.style.display = '';
                    var tx = gr.testo + '|' + gr.nome;
                    if ( et.testo !== tx ) { scriviEtichetta( et.el, gr.testo, gr.nome ); et.testo = tx; et.w = et.el.offsetWidth; }
                }
            }

            // ── Drag & throw (mouse e dito) — la velocità di rilascio diventa impulso ──
            var dragT = null, px = 0, py = 0, lpx = 0, lpy = 0;
            function at(e){ var rb = bin.getBoundingClientRect(); return { x: e.clientX - rb.left, y: e.clientY - rb.top }; }
            bin.addEventListener('pointerdown', function(e){
                if ( e.target.closest && e.target.closest('.olo-pb-shake') ) { return; }
                var p = at(e);
                for ( var i = toys.length - 1; i >= 0; i-- ) {
                    var t = toys[i];
                    if ( Math.hypot( p.x - t.x, p.y - t.y ) < t.r + ( e.pointerType === 'touch' ? 8 : 0 ) ) {
                        dragT = t; t.drag = true;
                        px = lpx = p.x; py = lpy = p.y;
                        try { bin.setPointerCapture( e.pointerId ); } catch ( err ) {}
                        if ( e.pointerType === 'touch' ) { e.preventDefault(); }
                        start();
                        break;
                    }
                }
            });
            bin.addEventListener('pointermove', function(e){
                if ( ! dragT ) { return; }
                var p = at(e);
                lpx = px; lpy = py; px = p.x; py = p.y;
                dragT.x = Math.max( dragT.r, Math.min( W - dragT.r, p.x ) );
                dragT.y = Math.max( dragT.r, Math.min( H - dragT.r, p.y ) );
            });
            var release = function(){
                if ( dragT ) {
                    dragT.vx = Math.max( -40, Math.min( 40, (px - lpx) * 1.4 ) );
                    dragT.vy = Math.max( -40, Math.min( 40, (py - lpy) * 1.4 ) );
                    dragT.drag = false; dragT = null;
                }
            };
            bin.addEventListener('pointerup', release);
            bin.addEventListener('pointercancel', release);

            // ── Scuoti: il pulsante, e sul telefono il telefono scosso ──
            function scuoti( forza ) {
                if ( ! running ) { return; }
                forza = forza || 1;
                if ( CHEM ) { rompiTutti(); quieteFino = Date.now() + 900; }
                for ( var i = 0; i < toys.length; i++ ) {
                    var t = toys[i];
                    if ( t.drag ) { continue; }
                    t.vx += ( Math.random() - 0.5 ) * 24 * forza;
                    t.vy -= ( 7 + Math.random() * 12 ) * forza;
                }
                bin.classList.remove('olo-pb-scosso');
                void bin.offsetWidth;
                bin.classList.add('olo-pb-scosso');
            }
            bin.addEventListener('animationend', function () { bin.classList.remove('olo-pb-scosso'); });
            var motoAttivo = false, ultimoScossone = 0, prec = null;
            function suMoto( e ) {
                if ( ! running ) { return; }
                var a = e.acceleration, m = 0;
                if ( a && a.x != null ) {
                    m = Math.sqrt( a.x * a.x + a.y * a.y + a.z * a.z );
                } else if ( e.accelerationIncludingGravity && e.accelerationIncludingGravity.x != null ) {
                    var g = e.accelerationIncludingGravity;
                    if ( prec ) { m = Math.sqrt( Math.pow( g.x - prec.x, 2 ) + Math.pow( g.y - prec.y, 2 ) + Math.pow( g.z - prec.z, 2 ) ); }
                    prec = { x: g.x, y: g.y, z: g.z };
                }
                var ora = Date.now();
                if ( m > 13 && ora - ultimoScossone > 700 ) { ultimoScossone = ora; scuoti( Math.min( 1.6, m / 16 ) ); }
            }
            function ascoltaMoto() {
                if ( motoAttivo ) { return; }
                motoAttivo = true;
                window.addEventListener('devicemotion', suMoto);
            }
            // Sul telefono si ascolta subito (Android manda il movimento senza chiedere); su iPhone il movimento arriva
            // solo dopo il permesso, che si chiede col primo tocco sul pulsante. Anche Chrome ha requestPermission.
            var permessoChiesto = false;
            function chiediMoto() {
                if ( permessoChiesto || ! coarse || typeof window.DeviceMotionEvent === 'undefined' ) { return; }
                permessoChiesto = true;
                if ( typeof window.DeviceMotionEvent.requestPermission === 'function' ) {
                    try { window.DeviceMotionEvent.requestPermission().catch(function () {}); } catch ( err ) {}
                }
            }
            if ( SHAKE ) {
                var btn = bin.querySelector('.olo-pb-shake');
                if ( btn ) { btn.addEventListener('click', function () { chiediMoto(); scuoti( 1 ); }); }
                if ( coarse && typeof window.DeviceMotionEvent !== 'undefined' ) { ascoltaMoto(); }
            }

            // ── Il passo della fisica ──
            function pareti( t, rimbalza ) {
                if ( WALLS ) {
                    if ( t.x < t.r )     { t.x = t.r;     if ( rimbalza ) { t.vx = -t.vx * REST; } }
                    if ( t.x > W - t.r ) { t.x = W - t.r; if ( rimbalza ) { t.vx = -t.vx * REST; } }
                    if ( t.y < t.r )     { t.y = t.r;     if ( rimbalza ) { t.vy = -t.vy * REST; } }
                }
                if ( t.y > H - t.r ) { t.y = H - t.r; if ( rimbalza ) { t.vy = -t.vy * REST; t.vx *= 0.96; } }
            }
            var running = false, rafId = null;
            function step(){
                if ( ! running ) { return; }
                var i, a, b, A, B, dx, dy, d;
                for ( i = 0; i < toys.length; i++ ) {
                    var t = toys[i];
                    if ( ! t.drag ) {
                        t.vy += G; t.x += t.vx; t.y += t.vy; t.vx *= FR;
                        pareti( t, true );
                    }
                }
                var ora = Date.now(), giri = bonds.length ? 4 : 1;
                // Gli atomi con legami liberi si attirano un poco quando sono vicini: i frammenti fermi sul fondo
                // cercano i loro atomi e le molecole si completano (non nell'attimo dopo uno scossone).
                if ( CHEM && ora > quieteFino ) {
                    for ( a = 0; a < toys.length; a++ ) {
                        A = toys[a];
                        if ( A.liberi < 1 ) { continue; }
                        for ( b = a + 1; b < toys.length; b++ ) {
                            B = toys[b];
                            if ( B.liberi < 1 || ! puoLegarsi( A, B ) ) { continue; }
                            dx = B.x - A.x; dy = B.y - A.y; d = Math.hypot( dx, dy ) || 0.01;
                            if ( d > ( A.r + B.r ) * 2.6 ) { continue; }
                            var f = 0.22 / d;
                            if ( ! A.drag ) { A.vx += dx * f; A.vy += dy * f; }
                            if ( ! B.drag ) { B.vx -= dx * f; B.vy -= dy * f; }
                        }
                    }
                }
                for ( var it = 0; it < giri; it++ ) {
                    // i legati stanno alla loro distanza (i raggi più lo spazio del bastoncino)
                    for ( i = 0; i < bonds.length; i++ ) {
                        A = bonds[i].a; B = bonds[i].b;
                        dx = B.x - A.x; dy = B.y - A.y; d = Math.hypot( dx, dy ) || 0.01;
                        var diff = ( d - ( A.r + B.r + gap() ) ) / d;
                        var wa = A.drag ? 0 : ( B.drag ? 1 : 0.5 ), wb = B.drag ? 0 : ( A.drag ? 1 : 0.5 );
                        A.x += dx * diff * wa * 0.8; A.y += dy * diff * wa * 0.8;
                        B.x -= dx * diff * wb * 0.8; B.y -= dy * diff * wb * 0.8;
                    }
                    /* collisioni coppia-coppia con risposta impulsiva (al primo giro), fra atomi non legati */
                    for ( a = 0; a < toys.length; a++ ) {
                        for ( b = a + 1; b < toys.length; b++ ) {
                            A = toys[a]; B = toys[b];
                            if ( A.con.length && A.con.indexOf( B ) >= 0 ) { continue; }
                            dx = B.x - A.x; dy = B.y - A.y;
                            d = Math.hypot( dx, dy ) || 0.01;
                            var min = A.r + B.r;
                            if ( d < min ) {
                                var nx = dx / d, ny = dy / d, overlap = ( min - d ) / 2;
                                if ( ! A.drag ) { A.x -= nx * overlap; A.y -= ny * overlap; }
                                if ( ! B.drag ) { B.x += nx * overlap; B.y += ny * overlap; }
                                if ( it === 0 ) {
                                    var rvx = B.vx - A.vx, rvy = B.vy - A.vy, vn = rvx * nx + rvy * ny;
                                    if ( vn < 0 ) {
                                        var imp = -(1 + REST) * vn / 2;
                                        if ( ! A.drag ) { A.vx -= imp * nx; A.vy -= imp * ny; }
                                        if ( ! B.drag ) { B.vx += imp * nx; B.vy += imp * ny; }
                                    }
                                    if ( ora > quieteFino && puoLegarsi( A, B ) ) { lega( A, B ); }
                                }
                            }
                        }
                    }
                    for ( i = 0; i < toys.length; i++ ) { if ( ! toys[i].drag ) { pareti( toys[i], false ); } }
                }
                // i legati si muovono insieme: lungo il legame la velocità relativa si annulla
                for ( i = 0; i < bonds.length; i++ ) {
                    A = bonds[i].a; B = bonds[i].b;
                    dx = B.x - A.x; dy = B.y - A.y; d = Math.hypot( dx, dy ) || 0.01;
                    var ux = dx / d, uy = dy / d, rv = ( B.vx - A.vx ) * ux + ( B.vy - A.vy ) * uy;
                    if ( A.drag ) { B.vx -= rv * ux; B.vy -= rv * uy; }
                    else if ( B.drag ) { A.vx += rv * ux; A.vy += rv * uy; }
                    else { A.vx += rv * ux / 2; A.vy += rv * uy / 2; B.vx -= rv * ux / 2; B.vy -= rv * uy / 2; }
                }
                for ( var k = 0; k < toys.length; k++ ) {
                    var tt = toys[k];
                    tt.el.style.transform = 'translate(' + ( tt.x - tt.r ) + 'px,' + ( tt.y - tt.r ) + 'px) rotate(' + ( tt.x * 0.7 ) + 'deg)';
                }
                if ( CHEM ) {
                    for ( i = 0; i < bonds.length; i++ ) {
                        A = bonds[i].a; B = bonds[i].b;
                        dx = B.x - A.x; dy = B.y - A.y;
                        bonds[i].el.style.transform = 'translate(' + A.x + 'px,' + A.y + 'px) rotate(' + Math.atan2( dy, dx ) + 'rad) scaleX(' + ( Math.hypot( dx, dy ) / 100 ) + ')';
                    }
                    if ( cambiato ) { ricalcolaMolecole(); }
                    // la formula sopra l'atomo più alto della molecola, centrata sulla molecola
                    for ( i = 0; i < gruppi.length; i++ ) {
                        var gr = gruppi[i], sx = 0, top = Infinity;
                        for ( var j = 0; j < gr.atomi.length; j++ ) { sx += gr.atomi[j].x; top = Math.min( top, gr.atomi[j].y - gr.atomi[j].r ); }
                        // tutta dentro il cesto: «NaH · idruro di sodio» vicino al bordo usciva a metà
                        var mezza = ( etichette[i].w || 80 ) / 2 + 6;
                        var cx = Math.max( mezza, Math.min( W - mezza, sx / gr.atomi.length ) );
                        etichette[i].el.style.transform = 'translate(' + cx + 'px,' + Math.max( 26, top - 6 ) + 'px) translate(-50%,-100%)';
                    }
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
