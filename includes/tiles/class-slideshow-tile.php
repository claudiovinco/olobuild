<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Slideshow_Tile extends Olobuild_Tile_Base {

    protected $type     = 'slideshow';
    protected $name     = 'Slideshow';
    protected $icon     = 'dashicons-slides';
    protected $category = 'media';
    protected $defaults = [
        'preset' => 'custom',
        'slides' => [
            [ 'id' => 's-1', 'image' => '', 'title' => 'Prima slide', 'subtitle' => 'Prima slide', 'link' => '' ],
        ],
        'autoplay'       => true,
        'autoplay_speed'  => '5000',
        'show_arrows'    => true,
        'show_dots'      => true,
        'slide_height'   => '400',
        // Cornice tile-level: 'auto' = nessun rapporto, comanda slide_height (resa
        // storica); 'cover' = quel che uk-cover applica già da sé.
        'aspect_ratio'   => 'auto',
        'object_fit'     => 'cover',
        'object_position' => 'center center',
        'overlay_color'  => 'var(--olo-color-dark, #000000)',
        'overlay_opacity' => '45',
        'text_color'     => '#FFFFFF',
        'transition'     => 'slide',
        'shadow'         => 'none',
        'border_width'   => '0',
        'border_color'   => '',
        'border_radius'  => '0',
            'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
        'effect_color'            => '',
        'effect_intensity'        => 'medium',
        'effect_speed'            => 0,
        'wow_disable'             => false,
        'wow_backdrop_blur'       => 0,
        'wow_backdrop_saturate'   => 100,
        'wow_border_style'        => 'solid',
        'wow_font_family'         => 'inherit',
        'wow_rotation'            => 0,
        'wow_perspective'         => 0,
        'wow_tilt_x'              => 0,
        'wow_glow_pulse'          => false,
        'wow_title_glow'          => false,
        'wow_scanlines'           => false,

        'wow_terminal_prompt' => false,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'autoplay',       'type' => 'toggle', 'label' => 'Autoplay' ],
            [ 'key' => 'autoplay_speed', 'type' => 'range',  'label' => 'Speed (ms)' ],
            [ 'key' => 'show_arrows',    'type' => 'toggle', 'label' => 'Show Arrows' ],
            [ 'key' => 'show_dots',      'type' => 'toggle', 'label' => 'Show Dots' ],
            [ 'key' => 'slide_height',   'type' => 'range',  'label' => 'Height (px)' ],
            [ 'key' => 'overlay_color',  'type' => 'color',  'label' => 'Overlay Color' ],
            [ 'key' => 'text_color',     'type' => 'color',  'label' => 'Text Color' ],
            [ 'key' => 'transition',     'type' => 'select', 'label' => 'Transition' ],
        ];
    }

    private function get_preset_extra_css( $preset_id, $id, $s = [] ) {
        // @deprecated v1.0.73 — refactor profondo: i preset audaci ora settano direttamente
        // i field standard tramite TILE_PRESETS in BuilderInspector.vue, e i field wow_* via
        // build_wow_effects_css(). Nessun !important, ogni proprietà personalizzabile.
        return '';
    }

    public function render( $settings ) {
        $s  = wp_parse_args( $settings, $this->defaults );
        $id = 'olo-ss-' . wp_unique_id();

        $slides = is_array( $s['slides'] ) ? $s['slides'] : [];
        if ( empty( $slides ) ) return '<div class="olo-slideshow" style="padding:40px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);">' . esc_html( olobuild_t( 'Nessuna slide aggiunta' ) ) . '</div>';

        $h     = absint( $s['slide_height'] );
        $count = count( $slides );
        $speed = absint( $s['autoplay_speed'] );
        $transition = in_array( $s['transition'], [ 'slide', 'fade', 'scale' ], true ) ? $s['transition'] : 'slide';

        // Punto focale globale applicato a OGNI slide (default 'center center' = comportamento storico).
        $obj_pos = trim( (string) ( $s['object_position'] ?? 'center center' ) );
        if ( $obj_pos === '' ) {
            $obj_pos = 'center center';
        }

        // Proporzioni tile-level. 'auto' (default) = si tiene l'altezza fissa in px,
        // cioè la resa di sempre. Con un rapporto vero l'altezza in px va TOLTA: un'altezza
        // definita vince sull'aspect-ratio e il rapporto non si vedrebbe.
        // Il rapporto si passa a UIkit nella sua sintassi 'W:H' (i DUE PUNTI, non la barra
        // del valore salvato) perché è lui a riscriverlo sulla lista a init
        // (UIkit 3.21, Slideshow.watch.list → aspect-ratio + width:100% inline).
        //
        // Ma il rapporto lo scriviamo ANCHE noi, in CSS, subito nell'HTML. Le slide sono in
        // position:absolute per CSS UIkit (.uk-slideshow-items>*{top:0;left:0;right:0;bottom:0}):
        // senza un'altezza propria la lista è alta ZERO finché UIkit non è partito — salto di
        // layout a ogni caricamento, e slideshow invisibile per sempre se uikit.js non arriva
        // (FPC che serve l'HTML col nome vecchio dell'asset, subset 404: già successo su questi
        // siti). UIkit poi riscrive lo STESSO identico valore inline, quindi non c'è conflitto.
        $ratio    = trim( (string) ( $s['aspect_ratio'] ?? 'auto' ) );
        $has_ratio = ( 'auto' !== $ratio && '' !== $ratio && preg_match( '#^\d+(?:\.\d+)?\s*/\s*\d+(?:\.\d+)?$#', $ratio ) );
        $ratio_css = $has_ratio ? str_replace( ' ', '', $ratio ) : '';
        $uk_ratio  = $has_ratio ? str_replace( '/', ':', $ratio_css ) : '';
        // Il rapporto va SOLO sulla lista, non sulla radice: la radice si prende l'altezza
        // dalla lista e può così ospitare sotto i pallini di navigazione (.uk-slideshow-nav
        // è un dotnav in flusso normale, non in posizione assoluta). Forzandole un
        // aspect-ratio i pallini finirebbero a cavallo del contenuto successivo.
        $h_style    = $has_ratio ? '' : 'height:' . (int) $h . 'px;';
        // Lista: rapporto + width:100%, cioè lettera per lettera quel che UIkit scriverà qui
        // all'init. Coincidendo i valori, all'init non si muove niente.
        $list_style = $has_ratio ? 'aspect-ratio:' . $ratio_css . ';width:100%;' : 'height:' . (int) $h . 'px;';
        // La radice era position:static: frecce (uk-position-center-*) e pulsante Pausa
        // (uk-position-bottom-right) sono assoluti e si appoggiavano al primo antenato
        // posizionato, cioè la sezione — finivano ai bordi della pagina, fuori dalle foto.
        // Ora stanno con la lista in un palco posizionato (.olo-ss-stage, lo schema di UIkit)
        // e i pallini restano fuori, sotto: frecce centrate sulle foto e Pausa nel loro
        // angolo anche quando la radice comprende la riga dei pallini (con le proporzioni).
        $h_style   .= 'position:relative;';

        // Velo: opacità scelta dall'utente (0-100). Prima era fissa a 0,45; 45 resta la
        // riserva, così le slideshow già salvate (senza la chiave) rendono come prima.
        $ov_raw     = $s['overlay_opacity'] ?? '';
        $ov_opacity = ( is_numeric( $ov_raw ) ) ? max( 0, min( 100, (float) $ov_raw ) ) / 100 : 0.45;

        // Adattamento: inline sull'<img>, così scavalca l'object-fit:cover che UIkit
        // mette su [uk-cover] (regola in :where(), specificità zero).
        $fit = (string) ( $s['object_fit'] ?? 'cover' );
        if ( ! in_array( $fit, [ 'cover', 'contain', 'fill', 'none', 'scale-down' ], true ) ) {
            $fit = 'cover';
        }
        $img_pos_attr = 'uk-cover style="object-fit:' . esc_attr( $fit ) . ';object-position:' . esc_attr( $obj_pos ) . ';"';

        ob_start();
        ?>
        <div id="<?php echo esc_attr( $id ); ?>" class="olo-slideshow olo-ss-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" uk-slideshow="autoplay: <?php echo esc_attr( $s['autoplay'] ? 'true' : 'false' ); ?>; autoplay-interval: <?php echo (int) $speed; ?>; animation: <?php echo esc_attr( $transition ); ?><?php echo $uk_ratio ? '; ratio: ' . esc_attr( $uk_ratio ) : ''; ?>" style="<?php echo esc_attr( $h_style ); ?>">
            <div class="olo-ss-stage" style="position:relative;">
            <div class="uk-slideshow-items" style="<?php echo esc_attr( $list_style ); ?>">
                <?php foreach ( $slides as $slide ) : ?>
                    <div>
                        <?php if ( ! empty( $slide['image'] ) ) : ?>
                            <?php echo Olobuild_Tile_Utils::img_srcset( absint( $slide['image_id'] ?? 0 ), $slide['image'], $slide['title'] ?? '', '', 'full', $img_pos_attr ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- <img> markup built by Olobuild_Tile_Utils::img_srcset() with esc_url()/esc_attr() internally; $img_pos_attr style value is esc_attr()'d above ?>
                        <?php else : ?>
                            <div style="position:absolute;inset:0;background:#1F2937;" uk-cover></div>
                        <?php endif; ?>
                        <?php $sl_bg = $this->safe_color_css( $s['overlay_color'] ); $sl_fg = $this->safe_color_css( $s['text_color'] ); ?>
                        <div class="uk-position-cover" style="<?php if ( $sl_bg ) echo 'background:' . $sl_bg . ';'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- colour validated by safe_color_css() whitelist ?>opacity:<?php echo esc_attr( (string) $ov_opacity ); ?>;"></div>
                        <?php
                        list( $sst_cls, $sst_data ) = $this->tfx_attrs( $s, 'title', $slide['title'] ?? '' );
                        list( $sss_cls, $sss_data ) = $this->tfx_attrs( $s, 'subtitle', $slide['subtitle'] ?? '' );
                        ?>
                        <div class="uk-position-center uk-text-center" style="<?php if ( $sl_fg ) echo 'color:' . $sl_fg . ';'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- colour validated by safe_color_css() whitelist ?>z-index:1;padding:24px;">
                            <?php $widget_html = $this->render_widget_template( $slide['widget_template_id'] ?? 0 ); ?>
                            <?php if ( $widget_html ) : ?>
                                <div class="olo-item-widget"><?php echo $widget_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML produced by render_widget_template() (internal template renderer, escapes its own output) ?></div>
                            <?php endif; ?>
                            <?php if ( ! empty( $slide['title'] ) ) : ?>
                                <div class="olo-ss-title<?php echo $sst_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); title is esc_html()'d ?>" style="font-size:2em;font-weight:700;margin-bottom:8px;"<?php echo $sst_data; ?>><?php echo esc_html( $slide['title'] ); ?></div>
                            <?php endif; ?>
                            <?php if ( ! empty( $slide['subtitle'] ) ) : ?>
                                <div class="olo-ss-sub<?php echo $sss_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); subtitle is esc_html()'d ?>" style="font-size:1.1em;opacity:0.85;"<?php echo $sss_data; ?>><?php echo esc_html( $slide['subtitle'] ); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ( $s['show_arrows'] && $count > 1 ) : ?>
                <a class="uk-slidenav-large uk-position-center-left uk-position-small" href role="button" aria-label="<?php echo esc_attr__( 'Slide precedente', 'olobuild' ); ?>" uk-slidenav-previous uk-slideshow-item="previous"></a>
                <a class="uk-slidenav-large uk-position-center-right uk-position-small" href role="button" aria-label="<?php echo esc_attr__( 'Slide successiva', 'olobuild' ); ?>" uk-slideshow-item="next" uk-slidenav-next></a>
            <?php endif; ?>
            </div>

            <?php if ( $s['show_dots'] && $count > 1 ) : ?>
                <ul class="uk-slideshow-nav uk-dotnav uk-flex-center uk-margin" role="tablist" aria-label="<?php echo esc_attr__( 'Naviga tra le slide', 'olobuild' ); ?>"></ul>
            <?php endif; ?>
        </div>
        <?php
        $tfx_css = $this->tfx_css( $s, '#' . $id );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Text_Effects::css() from whitelisted effects, sanitized colors and integer timings
        $this->tfx_print_script();

        // v1.0.73 — refactor profondo: get_preset_extra_css svuotato, ora i preset audaci
        // settano i field standard tramite TILE_PRESETS.slideshow + helper wow_*.
        // Il titolo nel markup ha la classe .olo-ss-title: con '.olo-slide-title' (che non
        // esiste) «Glow sul titolo» non accendeva niente.
        $preset_css = $this->build_wow_effects_css( $s, '#' . $id, '.olo-ss-title' );
        if ( $preset_css ) echo '<style>' . $preset_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by the Olobuild_Tile_Base::build_wow_effects_css() shared helper (sanitized internally)
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( "#{$id}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( "#{$id}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo "#{$id}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $id is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }

        // A11y (WCAG 4.1.2 / 2.2.2): nomi accessibili + stato sui dot generati a runtime da UIkit,
        // e pulsante Pausa/Riproduci per l'autoplay (rispetta anche prefers-reduced-motion).
        $a11y_autoplay = ! empty( $s['autoplay'] ) && $count > 1;
        $lbl_dot   = __( 'Vai alla slide', 'olobuild' );
        $lbl_pause = __( 'Metti in pausa la presentazione', 'olobuild' );
        $lbl_play  = __( 'Riproduci la presentazione', 'olobuild' );
        // Comandi sulla foto (frecce e Pausa) coi colori della slideshow stessa: fondo = colore
        // del velo, segno = colore del testo, che è scelto per leggersi sul velo (riserve:
        // scuro e chiaro della palette). Le frecce, ora dentro la foto, prendevano il colore
        // dei link del tema, che sulla foto poteva sparire.
        $pp_bg  = $this->safe_color_css( $s['overlay_color'] ) ?: 'var(--olo-color-dark, #16263d)';
        $pp_fg  = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-light, #ffffff)';
        $cmd_css = '';
        if ( $s['show_arrows'] && $count > 1 ) {
            $cmd_css .= '#' . $id . ' .uk-slidenav{color:' . $pp_fg . ';opacity:.85;}#' . $id . ' .uk-slidenav:hover,#' . $id . ' .uk-slidenav:focus-visible{color:' . $pp_fg . ';opacity:1;}';
        }
        if ( $a11y_autoplay ) {
            // Pulsante Pausa/Riproduci: prima i glifi «⏸»/«▶» (resi come emoji su alcuni
            // sistemi) su un nero fisso. Ora icone SVG, anello di fuoco visibile da tastiera.
            // Il fondo prende il colore del velo a opacità .72 anche se il velo è già
            // trasparente (rgb(from …)); dove la sintassi non c'è resta il color-mix.
            $pp       = '#' . $id . ' .olo-ss-playpause';
            $cmd_css .= $pp . '{position:absolute;z-index:2;display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;padding:0;border:0;border-radius:50%;cursor:pointer;line-height:1;'
                . 'background:color-mix(in srgb, ' . $pp_bg . ' 72%, transparent);background:rgb(from ' . $pp_bg . ' r g b / .72);color:' . $pp_fg . ';transition:background .2s ease;}'
                . $pp . ':hover{background:color-mix(in srgb, ' . $pp_bg . ' 92%, transparent);background:rgb(from ' . $pp_bg . ' r g b / .92);}'
                . $pp . ':focus-visible{outline:2px solid ' . $pp_fg . ';outline-offset:2px;}'
                . $pp . ' svg{width:14px;height:14px;display:block;}';
        }
        if ( $cmd_css ) {
            echo '<style>' . $cmd_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- colours validated by the safe_color_css() whitelist (or fixed var() literals); $id is internally generated
        }
        $svg_pause = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><rect x="6" y="4" width="4" height="16" rx="1"/><rect x="14" y="4" width="4" height="16" rx="1"/></svg>';
        $svg_play  = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M8 5.14v13.72a1 1 0 0 0 1.52.85l10.8-6.86a1 1 0 0 0 0-1.7L9.52 4.29A1 1 0 0 0 8 5.14z"/></svg>';
        ?>
        <script>
        (function(){
            var root = document.getElementById(<?php echo wp_json_encode( $id ); ?>);
            if ( ! root || root.dataset.oloSsA11y ) return;
            root.dataset.oloSsA11y = '1';
            function labelDots(){
                var dots = root.querySelectorAll('.uk-slideshow-nav > li');
                dots.forEach(function(li, i){
                    var a = li.querySelector('a') || li;
                    a.setAttribute('aria-label', <?php echo wp_json_encode( $lbl_dot ); ?> + ' ' + (i + 1));
                    var active = li.classList.contains('uk-active');
                    a.setAttribute('aria-current', active ? 'true' : 'false');
                });
            }
            function sync(){ labelDots(); }
            labelDots();
            try {
                if ( window.UIkit && UIkit.util ) {
                    UIkit.util.on(root, 'itemshown', sync);
                    UIkit.util.on(root, 'beforeitemshow', sync);
                }
            } catch(e){}
            <?php if ( $a11y_autoplay ) : ?>
            try {
                var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                var ss = window.UIkit ? UIkit.slideshow(root) : null;
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'olo-ss-playpause uk-position-bottom-right uk-position-small';
                var paused = false;
                function setState(p){
                    paused = p;
                    btn.setAttribute('aria-pressed', p ? 'true' : 'false');
                    btn.setAttribute('aria-label', p ? <?php echo wp_json_encode( $lbl_play ); ?> : <?php echo wp_json_encode( $lbl_pause ); ?>);
                    btn.innerHTML = p ? <?php echo wp_json_encode( $svg_play ); ?> : <?php echo wp_json_encode( $svg_pause ); ?>;
                    try { if ( ss ) { if ( p ) ss.stopAutoplay(); else ss.startAutoplay(); } } catch(e){}
                }
                btn.addEventListener('click', function(){ setState(!paused); });
                ( root.querySelector('.olo-ss-stage') || root ).appendChild(btn);
                setState( !!reduce );
            } catch(e){}
            <?php endif; ?>
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
