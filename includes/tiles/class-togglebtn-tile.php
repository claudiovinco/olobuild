<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_ToggleBtn_Tile extends Olobuild_Tile_Base {

    protected $type     = 'togglebtn';
    protected $name     = 'Pulsante Toggle';
    protected $icon     = 'dashicons-hidden';
    protected $category = 'interactive';
    protected $defaults = [
        'text_show'         => 'Mostra di più',
        'text_hide'         => 'Mostra di meno',
        'icon_show'         => 'chevron-down',
        'icon_hide'         => 'chevron-up',
        'icon_position'     => 'right',

        'target_id'         => '',
        'initial_state'     => 'hidden',
        'animation'         => 'collapse',
        'duration'          => '400',

        'btn_bg'            => 'transparent',
        'btn_color'         => '',
        'btn_hover_bg'      => '',
        'btn_border_width'  => '2',
        'btn_border_color'  => '',
        'btn_border_radius' => '8',
        'btn_padding_x'     => '24',
        'btn_padding_y'     => '12',
        'btn_font_size'     => '15',
        'btn_font_weight'   => '600',
        'btn_align'         => 'center',
        'btn_full_width'          => false,
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

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-tb-' . wp_rand( 10000, 99999 );
        // Canvas del builder: la sezione comandata resta aperta e modificabile (niente regole
        // che la comprimono, niente script) e il clic sul pulsante seleziona la tile. Vale anche
        // per un pulsante dell'header o del footer reso nell'iframe dell'anteprima.
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- sola lettura del flag di routing dell'iframe del builder; nessuna modifica di stato.
        $in_canvas = ! empty( $s['_builder_mode'] ) || ! empty( $_GET['olo_builder_iframe'] );
        // Sfondo del tab Stile disegnato sul pulsante (il contenitore resta trasparente).
        $sfondo = $this->sfondo_elemento( $style, $uid );

        $target_id   = sanitize_html_class( $s['target_id'] );
        if ( empty( $target_id ) ) {
            // Senza sezione da comandare il pulsante non farebbe niente: ai visitatori non si
            // mostra; all'autore, solo nel canvas, si dice cosa manca.
            if ( ! $in_canvas ) {
                return '';
            }
            return '<p class="olo-tb-avviso" style="margin:0;padding:.6em .9em;border:1px dashed var(--olo-color-danger, #EF4444);border-radius:.6em;color:var(--olo-color-danger, #EF4444);font-size:13px;line-height:1.4;text-align:center;">'
                . esc_html( olobuild_t( 'Pulsante Toggle: scrivi nel Contenuto l\'ID della sezione da mostrare e nascondere (lo stesso del campo ID nelle Avanzate della sezione).' ) )
                . '</p>';
        }

        // Bordo del pulsante: UN solo controllo nell'inspector, «Bordo» (btn_border, con hover ed
        // effetti); prima ce n'erano due sullo stesso pulsante. Da qui in poi $s['btn_border'] è il
        // bordo effettivo, già normalizzato: lo leggono bordo, hover ed effetti.
        $s['btn_border'] = $this->bordo_pulsante( $s );

        $text_show   = esc_html( $s['text_show'] );
        $text_hide   = esc_html( $s['text_hide'] );
        $icon_show   = $this->get_svg_icon( $s['icon_show'] );
        $icon_hide   = $this->get_svg_icon( $s['icon_hide'] );
        $icon_pos    = $s['icon_position'] === 'left' ? 'left' : 'right';
        $initial     = $s['initial_state'] === 'visible' ? 'visible' : 'hidden';
        $is_open     = $initial === 'visible';
        $animation   = in_array( $s['animation'], [ 'collapse', 'fade', 'slide' ] ) ? $s['animation'] : 'collapse';
        $duration    = max( 100, intval( $s['duration'] ) );

        // Button styles
        $bg          = $this->safe_color_css( $s['btn_bg'] ) ?: 'transparent';
        $color       = $this->safe_color_css( $s['btn_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $hover_bg    = $this->safe_color_css( $s['btn_hover_bg'] ) ?: 'color-mix(in srgb, var(--olo-color-primary, #e1474f) 10%, transparent)';
        $radius      = Olobuild_Tile_Utils::border_radius( $s['btn_border_radius'] ?? 0 );
        // Bordo in hover del sistema bordi (sul pulsante): dichiarazioni e transizione separate,
        // così la sua transizione si accoda a quella del pulsante invece di finire in una regola
        // `.uid{transition:border …}` stampata dopo, che spegneva sfondo, pressione e raggio.
        $border_hover_props = $this->build_border_hover_props( $s['btn_border'], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        // Transizione del pulsante con la «Durata» dello Sfondo in hover (senza chiave 0.2s come
        // sempre) più quella del Bordo in hover. La regola del Raggio in hover la ripete: in CSS
        // vince UNA sola `transition`.
        $btn_tr = 'background ' . Olobuild_Tile_Utils::durata_hover( $s, 'btn_bg_hover_duration', '0.2s' ) . ', transform 0.15s';
        if ( $border_hover_props['transition'] !== '' ) {
            $btn_tr .= ', ' . $border_hover_props['transition'];
        }
        // Raggio in hover: legge anche la sua «Durata» (btn_border_radius_hover_duration, 300 ms
        // come l'inspector); prima erano 400 ms fissi e il campo non agiva.
        $radius_hover_rules = Olobuild_Tile_Utils::radius_hover_rules( '.' . $uid, $s, 'btn_border_radius_hover', $btn_tr );
        $border_css         = $this->build_border_css( $s['btn_border'] );
        // Padding pulsante: controllo unico a 4 lati (tile_padding), ripiego legacy x/y.
        $btn_pad = Olobuild_Tile_Utils::spacing_sides(
            $s['tile_padding'] ?? null,
            [ 'y' => $s['btn_padding_y'] ?? null, 'x' => $s['btn_padding_x'] ?? null ],
            [ 12, 24, 12, 24 ]
        );
        $px          = intval( $btn_pad['left'] );
        $py          = intval( $btn_pad['top'] );
        $fsize       = max( 12, intval( $s['btn_font_size'] ) );
        $fweight     = in_array( $s['btn_font_weight'], [ '400', '500', '600', '700' ] ) ? $s['btn_font_weight'] : '600';
        $align       = in_array( $s['btn_align'], [ 'left', 'center', 'right' ] ) ? $s['btn_align'] : 'center';
        $full_width  = ! empty( $s['btn_full_width'] );

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: colors via the safe_color_css() whitelist (with token fallbacks), integers via intval() with max() clamps, enums via in_array() whitelists and fixed ternaries, radius via Olobuild_Tile_Utils helpers, target id via sanitize_html_class() + esc_attr(); $uid is internally generated. ?>
        <style>
            .<?php echo $uid; ?>-wrap {
                text-align: <?php echo $align; ?>;
            }

            .<?php echo $uid; ?> {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: <?php echo $bg; ?>;
                <?php echo $sfondo['css_con_livelli']; ?>
                color: <?php echo $color; ?>;
                font-size: <?php echo (int) $fsize; ?>px;
                font-weight: <?php echo $fweight; ?>;
                line-height: 1.2;
                padding: <?php echo esc_attr( Olobuild_Tile_Utils::sides_css( $btn_pad ) ); ?>;
                <?php echo $border_css; ?>
                <?php if ( $radius && $radius !== '0px' ) : ?>border-radius: <?php echo $radius; ?>;<?php endif; ?>
                cursor: pointer;
                transition: <?php echo $btn_tr; ?>;
                user-select: none;
                -webkit-user-select: none;
                <?php if ( $full_width ) : ?>width: 100%; justify-content: center;<?php endif; ?>
            }
            <?php echo $radius_hover_rules; ?>

            .<?php echo $uid; ?>:hover {
                background: <?php echo $hover_bg; ?>;
            }
            .<?php echo $uid; ?>:active {
                transform: scale(0.97);
            }
            .<?php echo $uid; ?>:focus-visible {
                outline: none;
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
            }

            .<?php echo $uid; ?> svg {
                width: <?php echo round( $fsize * 1.1 ); ?>px;
                height: <?php echo round( $fsize * 1.1 ); ?>px;
                fill: none;
                stroke: currentColor;
                stroke-width: 2;
                stroke-linecap: round;
                stroke-linejoin: round;
                flex-shrink: 0;
                transition: transform 0.3s;
            }

            <?php if ( ! $in_canvas ) : ?>
            /* Hide target before JS takes over (no flash) */
            <?php if ( ! $is_open ) : ?>
            #<?php echo esc_attr( $target_id ); ?>:not(.olo-tb-ready) {
                max-height: 0 !important;
                padding-top: 0 !important;
                padding-bottom: 0 !important;
                margin-top: 0 !important;
                margin-bottom: 0 !important;
                border-width: 0 !important;
                overflow: hidden;
                <?php if ( $animation === 'fade' || $animation === 'slide' ) : ?>opacity: 0;<?php endif; ?>
                <?php if ( $animation === 'slide' ) : ?>transform: translateY(-20px);<?php endif; ?>
            }
            <?php endif; ?>

            /* Target section transitions (after JS adds .olo-tb-ready) */
            #<?php echo esc_attr( $target_id ); ?>.olo-tb-ready {
                transition: max-height <?php echo (int) $duration; ?>ms ease, opacity <?php echo (int) $duration; ?>ms ease, transform <?php echo (int) $duration; ?>ms ease, padding <?php echo (int) $duration; ?>ms ease, margin <?php echo (int) $duration; ?>ms ease;
                overflow: hidden;
            }
            #<?php echo esc_attr( $target_id ); ?>.olo-tb-ready.olo-tb-hidden {
                max-height: 0;
                padding-top: 0 !important;
                padding-bottom: 0 !important;
                margin-top: 0 !important;
                margin-bottom: 0 !important;
                border-width: 0 !important;
                overflow: hidden;
                <?php if ( $animation === 'fade' || $animation === 'slide' ) : ?>opacity: 0; pointer-events: none;<?php endif; ?>
                <?php if ( $animation === 'slide' ) : ?>transform: translateY(-20px);<?php endif; ?>
            }
            #<?php echo esc_attr( $target_id ); ?>.olo-tb-ready.olo-tb-visible {
                opacity: 1;
                transform: translateY(0);
                pointer-events: auto;
            }
            <?php endif; ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="<?php echo esc_attr( $uid ); ?>-wrap">
            <button
                type="button"
                class="<?php echo esc_attr( $uid ); ?>"
                data-target="<?php echo esc_attr( $target_id ); ?>"
                data-text-show="<?php echo esc_attr( $text_show ); ?>"
                data-text-hide="<?php echo esc_attr( $text_hide ); ?>"
                data-animation="<?php echo esc_attr( $animation ); ?>"
                data-duration="<?php echo esc_attr( $duration ); ?>"
                data-open="<?php echo $is_open ? '1' : '0'; ?>"
                aria-controls="<?php echo esc_attr( $target_id ); ?>"
                aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>"
            >
                <?php echo $sfondo['markup']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- background layers built by sfondo_elemento() from Olobuild_CSS_Builder::get_bg_html_markup() (esc_url/esc_attr inside) and a safe_color_css()-whitelisted overlay ?>
                <?php if ( $icon_pos === 'left' ) : ?>
                    <span class="olo-tb-icon olo-tb-icon-show" aria-hidden="true" style="<?php echo $is_open ? 'display:none' : ''; ?>"><?php echo $icon_show; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG from the hardcoded get_svg_icon() map ?></span>
                    <span class="olo-tb-icon olo-tb-icon-hide" aria-hidden="true" style="<?php echo $is_open ? '' : 'display:none'; ?>"><?php echo $icon_hide; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG from the hardcoded get_svg_icon() map ?></span>
                <?php endif; ?>
                <span class="olo-tb-label"><?php echo $is_open ? $text_hide : $text_show; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- both branches escaped via esc_html() at assignment above ?></span>
                <?php if ( $icon_pos === 'right' ) : ?>
                    <span class="olo-tb-icon olo-tb-icon-show" aria-hidden="true" style="<?php echo $is_open ? 'display:none' : ''; ?>"><?php echo $icon_show; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG from the hardcoded get_svg_icon() map ?></span>
                    <span class="olo-tb-icon olo-tb-icon-hide" aria-hidden="true" style="<?php echo $is_open ? '' : 'display:none'; ?>"><?php echo $icon_hide; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG from the hardcoded get_svg_icon() map ?></span>
                <?php endif; ?>
            </button>
        </div>
        <?php if ( ! $in_canvas ) : ?>
        <script>
        (function(){
          function avvia() {
          document.querySelectorAll('.<?php echo $uid; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- internal 'olo-tb-' . wp_rand() identifier ?>').forEach(function(btn){
            if (btn.getAttribute('data-olo-tb-pronto') === '1') return;
            btn.setAttribute('data-olo-tb-pronto', '1');
            var target = document.getElementById(btn.dataset.target);
            if (!target) {
              // La sezione non è in questa pagina: un pulsante che non fa niente non si mostra.
              if (btn.parentNode) btn.parentNode.style.display = 'none';
              return;
            }
            var textShow = btn.dataset.textShow;
            var textHide = btn.dataset.textHide;
            var duration = parseInt(btn.dataset.duration) || 400;
            var isOpen = btn.dataset.open === '1';
            var label = btn.querySelector('.olo-tb-label');
            var iconShow = btn.querySelector('.olo-tb-icon-show');
            var iconHide = btn.querySelector('.olo-tb-icon-hide');

            // JS takes over: add .olo-tb-ready removes the :not(.olo-tb-ready) CSS rule
            if (!isOpen) {
              target.style.maxHeight = '0';
              target.classList.add('olo-tb-hidden');
            } else {
              target.classList.add('olo-tb-visible');
              target.style.maxHeight = 'none';
            }
            target.classList.add('olo-tb-ready');

            function updateUI() {
              if (label) label.textContent = isOpen ? textHide : textShow;
              if (iconShow) iconShow.style.display = isOpen ? 'none' : '';
              if (iconHide) iconHide.style.display = isOpen ? '' : 'none';
              btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            }

            btn.addEventListener('click', function() {
              isOpen = !isOpen;
              if (isOpen) {
                target.classList.remove('olo-tb-hidden');
                target.classList.add('olo-tb-visible');
                target.style.maxHeight = target.scrollHeight + 'px';
                setTimeout(function(){
                  if (target.classList.contains('olo-tb-visible')) target.style.maxHeight = 'none';
                }, duration + 50);
              } else {
                target.style.maxHeight = target.scrollHeight + 'px';
                target.offsetHeight;
                target.classList.remove('olo-tb-visible');
                target.classList.add('olo-tb-hidden');
                target.style.maxHeight = '0';
              }
              updateUI();
            });
          });
          }
          // Nel sito lo script si legge mentre la pagina si carica; dove il DOM è già pronto
          // (script rieseguiti dopo il caricamento) DOMContentLoaded non arriverebbe più.
          if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', avvia);
          } else {
            avvia();
          }
        })();
        </script>
        <?php endif; ?>
        <?php
        // Bordo in hover ed effetti — sul <button class="{uid}"> (pulsante visibile), NON sul
        // wrapper esterno {uid}-wrap. Il bordo base è già nella regola del pulsante (sopra).
        $btn_sel           = ".{$uid}";
        // Bordo in hover: la sua transizione è già in $btn_tr (sopra), qui solo le dichiarazioni.
        $border_hover_css  = $border_hover_props['decls'] !== '' ? "{$btn_sel}:hover{{$border_hover_props['decls']}}" : '';
        $border_effect_css = $this->build_border_effect_css( $btn_sel, $s['btn_border'], $s );
        if ( $border_hover_css || $border_effect_css ) {
            echo '<style>' . $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base border helpers from the border normalized by bordo_pulsante() (safe_color_css() colour, whitelisted style, integer sides); selector from internal uid
        }
        return ob_get_clean();
    }

    /**
     * Il bordo del pulsante, normalizzato per parse_border()/build_border_css(): lati interi,
     * stile ammesso, colore passato da safe_color_css() (vuoto = primario, come sempre).
     * Fonti, dalla più recente:
     *  1. `btn_border`, salvato dal controllo «Bordo» (lati, stile, colore);
     *  2. `border`, il secondo controllo «Bordo» che il pulsante aveva fino al 5 ott 2026, se
     *     disegna (prima veniva stampato dopo e vinceva sul bordo storico);
     *  3. le chiavi storiche btn_border_width / btn_border_color (2px primario di serie), che il
     *     ponte legacy del controllo tiene in sincronia con `btn_border`.
     *
     * @param array $s Settings del tile.
     * @return array{top:int,right:int,bottom:int,left:int,style:string,color:string}
     */
    private function bordo_pulsante( $s ) {
        if ( is_array( $s['btn_border'] ?? null ) ) {
            $b = $s['btn_border'];
        } elseif ( is_array( $s['border'] ?? null ) && $this->parse_border( $s['border'] ) ) {
            $b = $s['border'];
        } else {
            $w = max( 0, intval( $s['btn_border_width'] ?? 0 ) );
            $b = [ 'top' => $w, 'right' => $w, 'bottom' => $w, 'left' => $w, 'style' => 'solid', 'color' => $s['btn_border_color'] ?? '' ];
        }
        $stile = (string) ( $b['style'] ?? 'solid' );
        if ( ! in_array( $stile, [ 'solid', 'dashed', 'dotted', 'double', 'groove', 'ridge', 'inset', 'outset' ], true ) ) {
            $stile = 'solid';
        }
        return [
            'top'    => max( 0, intval( $b['top'] ?? 0 ) ),
            'right'  => max( 0, intval( $b['right'] ?? 0 ) ),
            'bottom' => max( 0, intval( $b['bottom'] ?? 0 ) ),
            'left'   => max( 0, intval( $b['left'] ?? 0 ) ),
            'style'  => $stile,
            'color'  => $this->safe_color_css( $b['color'] ?? '' ) ?: 'var(--olo-color-primary, #e1474f)',
        ];
    }

    private function get_svg_icon( $name ) {
        $icons = [
            'chevron-down' => '<svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>',
            'chevron-up'   => '<svg viewBox="0 0 24 24"><polyline points="6 15 12 9 18 15"/></svg>',
            'plus'         => '<svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>',
            'minus'        => '<svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/></svg>',
            'arrow-down'   => '<svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>',
            'arrow-up'     => '<svg viewBox="0 0 24 24"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>',
            'eye'          => '<svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>',
            'eye-off'      => '<svg viewBox="0 0 24 24"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>',
        ];
        return $icons[ $name ] ?? '';
    }
}
