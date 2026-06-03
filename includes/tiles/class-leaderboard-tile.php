<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Leaderboard — classifica con barre XP animate (famiglia E · bucket C).
 *
 * Reference runtime: handoff-tile-speciali/temi/60-tema-community-gamer.html (#board, blocco
 * "leaderboard with animated XP"). Portato qui inline nel render(), scoped per istanza,
 * idempotente, multi-istanza, con IntersectionObserver + ramo reduced-motion.
 *
 * Contratto §2:
 *  - Parametrico: ogni numero/colore/testo è un campo editor con default; nessun hardcode.
 *  - Scoped per istanza: classe/keyframes/CSS prefissati con UID 'olo-lb-<rand>'.
 *  - SSR: le righe e le barre sono renderizzate GIA' piene (width:PCT%) → no-JS/stampa OK.
 *  - reduced-motion: nessuna animazione, barre già al valore finale.
 *  - A11y: ogni barra role=progressbar + aria-valuenow/min/max; valori sempre come testo.
 *  - Performance: IO attiva l'animazione una sola volta quando la classifica entra nel viewport.
 */
class Olo_Leaderboard_Tile extends Olo_Tile_Base {

    protected $type     = 'leaderboard';
    protected $name     = 'Classifica (barre XP)';
    protected $icon     = 'dashicons-awards';
    protected $category = 'marketing';
    protected $defaults = [
        'bg'                => [ 'type' => 'none' ],
        'typography_preset' => '',

        // Dati
        'source' => 'manual',
        'rows'   => [
            [ 'name' => 'KiraByte',  'role' => 'Capoclan', 'value' => 24810, 'max' => 25000 ],
            [ 'name' => 'nott2late', 'role' => 'Veterano', 'value' => 20140, 'max' => 25000 ],
            [ 'name' => 'pixelmom',  'role' => 'Veterano', 'value' => 18305, 'max' => 25000 ],
            [ 'name' => 'vex_',      'role' => 'Membro',   'value' => 15022, 'max' => 25000 ],
            [ 'name' => 't0fu',      'role' => 'Membro',   'value' => 11870, 'max' => 25000 ],
            [ 'name' => 'mochi',     'role' => 'Recluta',  'value' => 8140,  'max' => 25000 ],
        ],
        'name_prefix'   => '@',
        'value_suffix'  => 'xp',
        'show_position' => true,
        'show_value'    => true,
        'show_role'     => true,

        // Comportamento
        'animate_on_view'    => true,
        'animation_duration' => 1300,
        'highlight_top'      => 3,

        // Aspetto
        'row_bg'             => '',
        'row_padding'        => [ 'top' => 16, 'right' => 20, 'bottom' => 16, 'left' => 20 ],
        'row_gap'            => 12,
        'text_color'         => '',
        'role_color'         => '',
        'position_color'     => '',
        'badge_bg'           => '',
        'badge_color'        => '',
        'bar_track_color'    => '',
        'bar_gradient_from'  => '',
        'bar_gradient_to'    => '',
        'bar_gradient_angle' => 90,
        'bar_height'         => 8,
        'bar_radius'         => 6,
        'highlight_color'    => '',
        'name_size'          => 16,
        'name_weight'        => '700',
        'role_size'          => 11,
        'position_size'      => 24,

        'border_radius'           => [ 'tl' => 14, 'tr' => 14, 'br' => 14, 'bl' => 14 ],
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
        $uid = 'olo-lb-' . wp_rand( 10000, 99999 );

        // ── Righe ──
        $rows = is_array( $s['rows'] ?? null ) ? $s['rows'] : [];

        // ── Etichette / toggle ──
        $name_prefix = (string) ( $s['name_prefix'] ?? '' );
        $val_suffix  = (string) ( $s['value_suffix'] ?? '' );
        $show_pos    = ! empty( $s['show_position'] );
        $show_val    = ! empty( $s['show_value'] );
        $show_role   = ! empty( $s['show_role'] );

        // ── Comportamento ──
        $animate   = ! empty( $s['animate_on_view'] );
        $anim_dur  = max( 300, min( 3000, intval( $s['animation_duration'] ?? 1300 ) ) );
        $hl_top    = max( 0, min( 3, intval( $s['highlight_top'] ?? 3 ) ) );

        // ── Colori (token-first: fallback su var(--olo-color-*) coerenti) ──
        $row_bg      = $this->safe_color_css( $s['row_bg'] ?? '' ) ?: 'var(--olo-color-surface-2, #1A1233)';
        $text_color  = $this->safe_color_css( $s['text_color'] ?? '' ) ?: 'var(--olo-color-text, #EDEAFB)';
        $role_color  = $this->safe_color_css( $s['role_color'] ?? '' ) ?: 'var(--olo-color-text-muted, #948CC4)';
        $pos_color   = $this->safe_color_css( $s['position_color'] ?? '' ) ?: 'var(--olo-color-text-faint, #5E568C)';
        $badge_bg    = $this->safe_color_css( $s['badge_bg'] ?? '' ) ?: 'var(--olo-color-primary-soft, rgba(99,102,241,0.2))';
        $badge_color = $this->safe_color_css( $s['badge_color'] ?? '' ) ?: 'var(--olo-color-primary, #6366F1)';
        $track_color = $this->safe_color_css( $s['bar_track_color'] ?? '' ) ?: 'var(--olo-color-border, rgba(237,234,251,0.08))';
        $hl_color    = $this->safe_color_css( $s['highlight_color'] ?? '' ) ?: 'var(--olo-color-primary, #6366F1)';

        $grad_from   = $this->safe_color_css( $s['bar_gradient_from'] ?? '' ) ?: 'var(--olo-color-primary, #6366F1)';
        $grad_to     = $this->safe_color_css( $s['bar_gradient_to'] ?? '' ) ?: 'var(--olo-color-secondary, #22D3EE)';
        $grad_angle  = max( 0, min( 360, intval( $s['bar_gradient_angle'] ?? 90 ) ) );
        $bar_grad    = "linear-gradient({$grad_angle}deg,{$grad_from},{$grad_to})";

        // ── Dimensioni ──
        $bar_h       = max( 4, min( 28, intval( $s['bar_height'] ?? 8 ) ) );
        $bar_r       = max( 0, min( 16, intval( $s['bar_radius'] ?? 6 ) ) );
        $row_gap     = max( 0, min( 40, intval( $s['row_gap'] ?? 12 ) ) );
        $name_size   = max( 12, min( 32, intval( $s['name_size'] ?? 16 ) ) );
        $name_weight = in_array( (string) ( $s['name_weight'] ?? '700' ), [ '400', '500', '600', '700', '800', '900' ], true ) ? (string) $s['name_weight'] : '700';
        $role_size   = max( 8, min( 18, intval( $s['role_size'] ?? 11 ) ) );
        $pos_size    = max( 14, min( 40, intval( $s['position_size'] ?? 24 ) ) );

        // ── Padding riga (oggetto spacing {top,right,bottom,left}) ──
        $rp = is_array( $s['row_padding'] ?? null ) ? $s['row_padding'] : [];
        $rp_t = max( 0, intval( $rp['top']    ?? 16 ) );
        $rp_r = max( 0, intval( $rp['right']  ?? 20 ) );
        $rp_b = max( 0, intval( $rp['bottom'] ?? 16 ) );
        $rp_l = max( 0, intval( $rp['left']   ?? 20 ) );
        $row_pad = "{$rp_t}px {$rp_r}px {$rp_b}px {$rp_l}px";

        // ── Raggio riga ──
        $row_radius = $this->build_border_radius_css( $s['border_radius'] ?? [] ) ?: '14px';

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                display: grid;
                gap: <?php echo $row_gap; ?>px;
                width: 100%;
            }
            .<?php echo $uid; ?> .olo-lb-row {
                display: grid;
                grid-template-columns: <?php echo $show_pos ? 'auto ' : ''; ?>1fr<?php echo $show_val ? ' auto' : ''; ?>;
                align-items: center;
                gap: 18px;
                background: <?php echo $row_bg; ?>;
                border-radius: <?php echo $row_radius; ?>;
                padding: <?php echo $row_pad; ?>;
                box-sizing: border-box;
            }
            <?php if ( $show_pos ) : ?>
            .<?php echo $uid; ?> .olo-lb-pos {
                font-size: <?php echo $pos_size; ?>px;
                font-weight: 700;
                line-height: 1;
                color: <?php echo $pos_color; ?>;
                font-variant-numeric: tabular-nums;
                min-width: 1.4em;
                text-align: center;
            }
            .<?php echo $uid; ?> .olo-lb-row.is-top .olo-lb-pos {
                color: <?php echo $hl_color; ?>;
            }
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-lb-who {
                display: flex;
                flex-direction: column;
                gap: 9px;
                min-width: 0;
            }
            .<?php echo $uid; ?> .olo-lb-name {
                display: flex;
                align-items: center;
                gap: 9px;
                flex-wrap: wrap;
                font-size: <?php echo $name_size; ?>px;
                font-weight: <?php echo $name_weight; ?>;
                line-height: 1.2;
                color: <?php echo $text_color; ?>;
            }
            <?php if ( $show_role ) : ?>
            .<?php echo $uid; ?> .olo-lb-badge {
                font-size: <?php echo $role_size; ?>px;
                font-weight: 600;
                letter-spacing: .04em;
                text-transform: uppercase;
                padding: 3px 8px;
                border-radius: 6px;
                background: <?php echo $badge_bg; ?>;
                color: <?php echo $badge_color; ?>;
                line-height: 1.2;
                white-space: nowrap;
            }
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-lb-track {
                height: <?php echo $bar_h; ?>px;
                border-radius: <?php echo $bar_r; ?>px;
                background: <?php echo $track_color; ?>;
                overflow: hidden;
                width: 100%;
            }
            .<?php echo $uid; ?> .olo-lb-fill {
                display: block;
                height: 100%;
                border-radius: <?php echo $bar_r; ?>px;
                background: <?php echo $bar_grad; ?>;
                <?php if ( $animate ) : ?>
                transition: width <?php echo $anim_dur; ?>ms cubic-bezier(.2,.7,.2,1);
                will-change: width;
                <?php endif; ?>
            }
            <?php if ( $show_val ) : ?>
            .<?php echo $uid; ?> .olo-lb-pts {
                font-weight: 700;
                text-align: right;
                line-height: 1.2;
                color: <?php echo $text_color; ?>;
                font-variant-numeric: tabular-nums;
                white-space: nowrap;
            }
            .<?php echo $uid; ?> .olo-lb-pts b { color: <?php echo $grad_to; ?>; }
            .<?php echo $uid; ?> .olo-lb-pts .olo-lb-unit { color: <?php echo $role_color; ?>; font-weight: 500; }
            <?php endif; ?>
            /* reduced-motion → barre statiche al valore finale, niente transizione */
            @media (prefers-reduced-motion: reduce) {
                .<?php echo $uid; ?> .olo-lb-fill { transition: none !important; }
            }
            @media (max-width: 560px) {
                .<?php echo $uid; ?> .olo-lb-row { gap: 12px; }
            }
        </style>

        <div class="<?php echo esc_attr( $uid ); ?> olo-lb"<?php echo $animate ? ' data-olo-lb-animate="1"' : ''; ?> role="list" aria-label="<?php esc_attr_e( 'Classifica', 'olobuilder' ); ?>">
            <?php foreach ( $rows as $i => $r ) :
                $name  = (string) ( $r['name'] ?? '' );
                $role  = (string) ( $r['role'] ?? '' );
                $value = max( 0, intval( $r['value'] ?? 0 ) );
                $rmax  = max( 1, intval( $r['max'] ?? 1 ) );
                if ( $name === '' && $value === 0 ) { continue; }
                $pct      = max( 0, min( 100, round( ( $value / $rmax ) * 100, 2 ) ) );
                $is_top   = ( $hl_top > 0 && $i < $hl_top );
                $pos_num  = $i + 1;
                $disp_name = $name_prefix . $name;
                // SSR: la barra è già piena (width:PCT%). Il JS, se attivo e non reduced-motion,
                // la riazzera e la rianima all'ingresso. data-olo-lb-w preserva il valore target.
                $aria_lbl = trim( $disp_name . ' — ' . $value . ' ' . $val_suffix );
            ?>
                <div class="olo-lb-row<?php echo $is_top ? ' is-top' : ''; ?> olo-lb-row--<?php echo $pos_num; ?>" role="listitem">
                    <?php if ( $show_pos ) : ?>
                        <div class="olo-lb-pos" aria-hidden="true"><?php echo esc_html( $pos_num ); ?></div>
                    <?php endif; ?>
                    <div class="olo-lb-who">
                        <div class="olo-lb-name">
                            <span class="olo-lb-nm"><?php echo esc_html( $disp_name ); ?></span>
                            <?php if ( $show_role && $role !== '' ) : ?>
                                <span class="olo-lb-badge"><?php echo esc_html( $role ); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="olo-lb-track"
                             role="progressbar"
                             aria-label="<?php echo esc_attr( $aria_lbl ); ?>"
                             aria-valuenow="<?php echo esc_attr( $value ); ?>"
                             aria-valuemin="0"
                             aria-valuemax="<?php echo esc_attr( $rmax ); ?>">
                            <i class="olo-lb-fill" style="width:<?php echo esc_attr( $pct ); ?>%" data-olo-lb-w="<?php echo esc_attr( $pct ); ?>"></i>
                        </div>
                    </div>
                    <?php if ( $show_val ) : ?>
                        <div class="olo-lb-pts"><b><?php echo esc_html( number_format_i18n( $value ) ); ?></b><?php
                            if ( $val_suffix !== '' ) {
                                echo ' <span class="olo-lb-unit">' . esc_html( $val_suffix ) . '</span>';
                            }
                        ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <?php if ( empty( $rows ) ) : ?>
                <div class="olo-lb-row"><div class="olo-lb-who"><div class="olo-lb-name" style="opacity:.6"><?php esc_html_e( 'Aggiungi righe alla classifica…', 'olobuilder' ); ?></div></div></div>
            <?php endif; ?>
        </div>

        <?php if ( $animate ) : ?>
        <script>
        /* Leaderboard · barre XP animate — runtime scoped per istanza
           (rif. 60-tema-community-gamer.html, blocco "leaderboard with animated XP").
           Idempotente, multi-istanza, IntersectionObserver one-shot, reduced-motion safe. */
        (function(){
            var root = document.querySelector('.<?php echo esc_js( $uid ); ?>');
            if ( ! root ) { return; }
            if ( root.dataset.oloLbInit ) { return; }   // una sola init per istanza
            root.dataset.oloLbInit = '1';

            var bars = root.querySelectorAll('.olo-lb-fill');
            if ( ! bars.length ) { return; }

            // prefers-reduced-motion → lascia le barre già piene (SSR), nessuna animazione.
            var rm = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)');
            if ( rm && rm.matches ) { return; }

            // Riazzera le barre (erano piene per il fallback no-JS), poi anima all'ingresso.
            var i;
            for ( i = 0; i < bars.length; i++ ) { bars[i].style.width = '0%'; }

            function fill(){
                for ( var k = 0; k < bars.length; k++ ) {
                    var target = bars[k].getAttribute('data-olo-lb-w') || '0';
                    bars[k].style.width = target + '%';
                }
            }

            if ( 'IntersectionObserver' in window ) {
                var io = new IntersectionObserver(function( entries ){
                    for ( var j = 0; j < entries.length; j++ ) {
                        if ( entries[j].isIntersecting ) {
                            // doppio rAF: garantisce che il reflow registri width:0 prima della transizione
                            requestAnimationFrame(function(){ requestAnimationFrame( fill ); });
                            io.disconnect();
                            break;
                        }
                    }
                }, { threshold: 0.25 });
                io.observe( root );
            } else {
                fill();
            }
        })();
        </script>
        <?php endif; ?>

        <?php
        // Sistema bordi (come marquee): base + hover + effetti, scoped sul wrapper.
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );

        // Ombra + raggio sul wrapper (il raggio riga è già applicato alle righe;
        // shadow/border si applicano al contenitore della classifica).
        $shadow_css = $this->collect_shadow_css( $s );

        $wrap_decls = $border_css . $shadow_css;
        if ( $wrap_decls || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $wrap_decls ) { echo ".{$uid}{{$wrap_decls}}"; }
            echo $border_hover_css . $border_effect_css . '</style>';
        }
        return ob_get_clean();
    }

    /**
     * Ombra dal field condiviso shadowField (preset sm/md/lg/xl + custom).
     * Restituisce dichiarazioni CSS inline (senza selettore) o ''.
     */
    private function collect_shadow_css( $s ) {
        $shadow = $s['shadow'] ?? 'none';
        if ( $shadow === 'none' || $shadow === '' ) { return ''; }
        $presets = [
            'sm' => '0 1px 3px rgba(0,0,0,0.12)',
            'md' => '0 4px 12px rgba(0,0,0,0.18)',
            'lg' => '0 12px 28px rgba(0,0,0,0.22)',
            'xl' => '0 24px 48px rgba(0,0,0,0.28)',
        ];
        if ( $shadow === 'custom' ) {
            $h     = intval( $s['shadow_h'] ?? 0 );
            $v     = intval( $s['shadow_v'] ?? 4 );
            $blur  = max( 0, intval( $s['shadow_blur'] ?? 10 ) );
            $sprd  = intval( $s['shadow_spread'] ?? 0 );
            $color = $this->safe_color_css( $s['shadow_color'] ?? '' ) ?: 'rgba(0,0,0,0.15)';
            $inset = ! empty( $s['shadow_inset'] ) ? 'inset ' : '';
            return "box-shadow:{$inset}{$h}px {$v}px {$blur}px {$sprd}px {$color};";
        }
        $val = $presets[ $shadow ] ?? '';
        return $val ? "box-shadow:{$val};" : '';
    }
}
