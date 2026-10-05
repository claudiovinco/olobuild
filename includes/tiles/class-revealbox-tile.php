<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Revealbox_Tile extends Olobuild_Tile_Base {

    protected $type     = 'revealbox';
    protected $name     = 'Reveal Box';
    protected $icon     = 'dashicons-arrow-up-alt';
    protected $category = 'interactive';

    protected $defaults = [
        // Sfondi unificati sul pannello media universale, PER ZONA (chiavi legacy tenute sotto come fallback).
        'media'                  => [ 'type' => 'none' ],
        'top_media'              => [ 'type' => 'none' ],
        'bottom_media'           => [ 'type' => 'none' ],
        'visible_height'         => '300',
        'top_image_url'          => '',
        'top_image_position'     => 'center center',
        'top_image_size'         => 'cover',
        'top_video_url'          => '',
        'bottom_image_url'       => '',
        'bottom_image_position'  => 'center center',
        'bottom_image_size'      => 'cover',
        'bottom_video_url'       => '',
        'top_content'            => '<h3>Titolo</h3>',
        'bottom_content'         => '<p>Contenuto rivelato al passaggio del mouse</p>',
        'top_icon'               => '',
        'top_icon_size'          => '2',
        'top_icon_color'         => 'var(--olo-color-light, #ffffff)',
        'bottom_icon'            => '',
        'bottom_icon_size'       => '2',
        'bottom_icon_color'      => 'var(--olo-color-light, #ffffff)',
        'reveal_effect'          => 'slide-up',
        'reveal_amount'          => '',
        'transition_speed'       => '0.5',
        'transition_easing'      => 'ease',
        'top_text_color'         => 'var(--olo-color-light, #ffffff)',
        'top_font_size'          => '',
        'bottom_text_color'      => 'var(--olo-color-light, #ffffff)',
        'bottom_font_size'       => '',
        'overlay_color'          => 'var(--olo-color-dark, #000000)',
        'overlay_opacity'        => '0',
        'reveal_overlay_color'   => 'var(--olo-color-dark, #000000)',
        'reveal_overlay_opacity' => '60',
        'text_color'             => 'var(--olo-color-light, #ffffff)',
        'top_align'              => 'flex-end',
        'top_justify'            => 'flex-start',
        'bottom_align'           => 'flex-start',
        'bottom_justify'         => 'flex-start',
        'top_padding'            => '24',
        'bottom_padding'         => '24',
        'border_radius'          => '0',
        'perspective'            => '800',
        // backward compat
        'image_url'              => '',
        'image_position'         => 'center center',
        'image_size'             => 'cover',
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
        $s = wp_parse_args( $settings, $this->defaults );

        // image_url is now the global background (fixed behind both zones)
        // top_image_url / bottom_image_url are per-face backgrounds (scroll with face)

        $uid       = 'olo-rb-' . substr( md5( wp_json_encode( $s ) . wp_rand() ), 0, 8 );
        $effect    = sanitize_html_class( $s['reveal_effect'] );
        $h         = intval( $s['visible_height'] ) ?: 300;
        $reveal_h  = intval( $s['reveal_amount'] ) ?: $h;
        // Scorrimento scelto a mano (0/vuoto = automatico): negli scorrimenti ←/→ prima era ignorato.
        $reveal_px = max( 0, intval( $s['reveal_amount'] ) );
        $speed     = floatval( $s['transition_speed'] ) ?: 0.5;
        $easing_map = [
            'ease'                        => 'ease',
            'ease-in-out'                 => 'ease-in-out',
            'ease-out'                    => 'ease-out',
            'cubic-bezier(0.4,0,0.2,1)'  => 'cubic-bezier(0.4,0,0.2,1)',
            'linear'                      => 'linear',
        ];
        $easing    = $easing_map[ $s['transition_easing'] ] ?? 'ease';
        // Raggio a 4 angoli (prima si usava solo il più grande dei quattro).
        $radius_css = $this->build_border_radius_css( $s['border_radius'] ?? '0' );
        $persp     = intval( $s['perspective'] ) ?: 800;

        $top_ov_op    = intval( $s['overlay_opacity'] );
        $bot_ov_op    = intval( $s['reveal_overlay_opacity'] );
        $top_pad      = Olobuild_Tile_Utils::spacing_css( $s['top_padding'] ?? 24, 24 );
        $bot_pad      = Olobuild_Tile_Utils::spacing_css( $s['bottom_padding'] ?? 24, 24 );

        $safe_text_color    = $this->safe_color_css( $s['text_color'] );
        $safe_top_text_clr  = $this->safe_color_css( $s['top_text_color'] ?: $s['text_color'] );
        $safe_bot_text_clr  = $this->safe_color_css( $s['bottom_text_color'] ?: $s['text_color'] );
        $top_font_size      = intval( $s['top_font_size'] );
        $bot_font_size      = intval( $s['bottom_font_size'] );
        $safe_overlay       = $this->safe_color_css( $s['overlay_color'] );
        $safe_reveal_ov     = $this->safe_color_css( $s['reveal_overlay_color'] );

        $is_slide = str_starts_with( $effect, 'slide-' );
        $is_flip  = str_starts_with( $effect, 'flip-' );
        $is_stack = in_array( $effect, [ 'fade', 'zoom-in', 'zoom-out', 'rotate-in' ], true );
        $is_horiz = $effect === 'slide-left' || $effect === 'slide-right';

        // ── Per-face BG helpers ──
        // Sfondi unificati sul pannello media universale PER ZONA (top_media/bottom_media):
        // se presente un media object (type!=none) ha la precedenza (bg_media_parts:
        // css sul wrapper + markup video/gallery); altrimenti resa legacy image/video.
        $top_bg  = $this->render_face_bg( $s['top_image_url'], $s['top_image_position'], $s['top_image_size'], $s['top_video_url'], $s['top_media'] ?? null, $uid . '-top' );
        $bot_bg  = $this->render_face_bg( $s['bottom_image_url'], $s['bottom_image_position'], $s['bottom_image_size'], $s['bottom_video_url'], $s['bottom_media'] ?? null, $uid . '-bot' );

        // ── Overlay helpers ──
        $top_overlay = $top_ov_op > 0
            ? '<div style="position:absolute;inset:0;background:' . $safe_overlay . ';opacity:' . round( $top_ov_op / 100, 2 ) . ';z-index:1;pointer-events:none"></div>'
            : '';
        $bot_overlay = $bot_ov_op > 0
            ? '<div style="position:absolute;inset:0;background:' . $safe_reveal_ov . ';opacity:' . round( $bot_ov_op / 100, 2 ) . ';z-index:1;pointer-events:none"></div>'
            : '';

        // ── Icon helpers ──
        $top_icon_html = $this->render_icon( $s['top_icon'], $s['top_icon_size'], $s['top_icon_color'] );
        $bot_icon_html = $this->render_icon( $s['bottom_icon'], $s['bottom_icon_size'], $s['bottom_icon_color'] );

        // ── Face styles ──
        $face_base = "position:relative;display:flex;flex-direction:column;box-sizing:border-box";
        if ( $is_horiz ) {
            $face_dim = "width:50%;height:{$h}px;flex-shrink:0";
        } else {
            $face_dim = "width:100%;height:{$h}px";
        }

        $top_face_css = "{$face_base};{$face_dim};align-items:" . esc_attr( $s['top_justify'] ) . ";justify-content:" . esc_attr( $s['top_align'] );
        $bot_face_css = "{$face_base};{$face_dim};align-items:" . esc_attr( $s['bottom_justify'] ) . ";justify-content:" . esc_attr( $s['bottom_align'] );

        $top_content = $this->safe_richtext_content( $s['top_content'] );
        $bot_content = $this->safe_richtext_content( $s['bottom_content'] );

        $top_content_css = 'position:relative;z-index:2;padding:' . $top_pad . ';color:' . $safe_top_text_clr;
        if ( $top_font_size > 0 ) { $top_content_css .= ';font-size:' . $top_font_size . 'px'; }
        $bot_content_css = 'position:relative;z-index:2;padding:' . $bot_pad . ';color:' . $safe_bot_text_clr;
        if ( $bot_font_size > 0 ) { $bot_content_css .= ';font-size:' . $bot_font_size . 'px'; }

        $top_inner = $top_bg . $top_overlay . '<div style="' . $top_content_css . '">' . $top_icon_html . $top_content . '</div>';
        $bot_inner = $bot_bg . $bot_overlay . '<div style="' . $bot_content_css . '">' . $bot_icon_html . $bot_content . '</div>';

        // ── CSS delle transizioni (scoped all'UID) ──
        // $css   = stato di partenza; $rivela = [ selettore interno, dichiarazioni ] dello stato
        // «faccia nascosta in vista», emesso sotto per mouse, tastiera e tocco.
        $css    = '';
        $rivela = [];
        // overflow:clip dove c'è (fallback hidden): il focus su un link della faccia nascosta
        // non deve far scorrere il riquadro.
        $container_css = "height:{$h}px;overflow:hidden;overflow:clip;position:relative;box-sizing:content-box;color:{$safe_text_color}";
        if ( $radius_css !== '' ) {
            $container_css .= ";border-radius:{$radius_css}";
        }

        if ( $is_slide ) {
            $slider_css = 'position:relative;z-index:1;transition:transform ' . $speed . 's ' . $easing;
            if ( $is_horiz ) {
                $slider_css .= ';display:flex;width:200%';
            }

            // NOTE: initial transforms go in CSS (not inline) so the reveal states can override them
            if ( $effect === 'slide-up' ) {
                $css     .= "#{$uid} .olo-rb-slider{transform:translateY(0)}";
                $rivela[] = [ '.olo-rb-slider', "transform:translateY(-{$reveal_h}px)" ];
                $html_faces = '<div class="olo-rb-face" style="' . $top_face_css . '">' . $top_inner . '</div>'
                            . '<div class="olo-rb-face olo-rb-revealed-face" style="' . $bot_face_css . '">' . $bot_inner . '</div>';
            } elseif ( $effect === 'slide-down' ) {
                $css     .= "#{$uid} .olo-rb-slider{transform:translateY(-{$reveal_h}px)}";
                $rivela[] = [ '.olo-rb-slider', 'transform:translateY(0)' ];
                $html_faces = '<div class="olo-rb-face olo-rb-revealed-face" style="' . $bot_face_css . '">' . $bot_inner . '</div>'
                            . '<div class="olo-rb-face" style="' . $top_face_css . '">' . $top_inner . '</div>';
            } elseif ( $effect === 'slide-left' ) {
                // -50% della striscia (larga il doppio) = una faccia intera; con lo scorrimento
                // scelto si ferma prima, mai oltre la faccia.
                $css     .= "#{$uid} .olo-rb-slider{transform:translateX(0)}";
                $rivela[] = [ '.olo-rb-slider', $reveal_px > 0 ? "transform:translateX(max(-50%, -{$reveal_px}px))" : 'transform:translateX(-50%)' ];
                $html_faces = '<div class="olo-rb-face" style="' . $top_face_css . '">' . $top_inner . '</div>'
                            . '<div class="olo-rb-face olo-rb-revealed-face" style="' . $bot_face_css . '">' . $bot_inner . '</div>';
            } else { // slide-right
                $css     .= "#{$uid} .olo-rb-slider{transform:translateX(-50%)}";
                $rivela[] = [ '.olo-rb-slider', $reveal_px > 0 ? "transform:translateX(min(0px, calc(-50% + {$reveal_px}px)))" : 'transform:translateX(0)' ];
                $html_faces = '<div class="olo-rb-face olo-rb-revealed-face" style="' . $bot_face_css . '">' . $bot_inner . '</div>'
                            . '<div class="olo-rb-face" style="' . $top_face_css . '">' . $top_inner . '</div>';
            }

            $inner_html = '<div class="olo-rb-slider" style="' . $slider_css . '">' . $html_faces . '</div>';

        } elseif ( $is_stack ) {
            $top_stack_css = $top_face_css . ';position:absolute;inset:0;z-index:2;transition:opacity ' . $speed . 's ' . $easing . ',transform ' . $speed . 's ' . $easing;
            $bot_stack_css = $bot_face_css . ';position:absolute;inset:0;z-index:1';

            // La faccia sopra, ormai trasparente, non deve più prendere i clic (i link sotto sì).
            $decl = 'opacity:0;pointer-events:none';
            if ( $effect === 'zoom-in' ) {
                $decl .= ';transform:scale(1.2)';
            } elseif ( $effect === 'zoom-out' ) {
                $decl .= ';transform:scale(0.8)';
            } elseif ( $effect === 'rotate-in' ) {
                $decl .= ';transform:rotate(15deg) scale(1.1)';
            }
            $rivela[] = [ '.olo-rb-stack-top', $decl ];

            $inner_html = '<div class="olo-rb-stack-top" style="' . $top_stack_css . '">' . $top_inner . '</div>'
                        . '<div class="olo-rb-stack-bottom olo-rb-revealed-face" style="' . $bot_stack_css . '">' . $bot_inner . '</div>';

        } elseif ( $is_flip ) {
            $container_css .= ";perspective:{$persp}px";
            $flipper_css = 'position:relative;width:100%;height:100%;transition:transform ' . $speed . 's ' . $easing . ';transform-style:preserve-3d';
            $front_css = $top_face_css . ';position:absolute;inset:0;backface-visibility:hidden;z-index:2';
            $back_transform = $effect === 'flip-x' ? 'rotateY(180deg)' : 'rotateX(180deg)';
            $hover_transform = $effect === 'flip-x' ? 'rotateY(180deg)' : 'rotateX(180deg)';
            $back_css = $bot_face_css . ';position:absolute;inset:0;backface-visibility:hidden;transform:' . $back_transform;

            $rivela[] = [ '.olo-rb-flipper', "transform:{$hover_transform}" ];
            $rivela[] = [ '.olo-rb-front', 'pointer-events:none' ];

            $inner_html = '<div class="olo-rb-flipper" style="' . $flipper_css . '">'
                        . '<div class="olo-rb-face olo-rb-front" style="' . $front_css . '">' . $top_inner . '</div>'
                        . '<div class="olo-rb-face olo-rb-revealed-face" style="' . $back_css . '">' . $bot_inner . '</div>'
                        . '</div>';
        } else {
            $inner_html = '';
        }

        // Stato «rivelato»: col mouse (solo dove il passaggio esiste: sul touch :hover resterebbe
        // appiccicato e il tap non potrebbe più richiudere), col tocco (classe .is-revealed, dallo
        // script), con la tastiera (focus sul riquadro o dentro la faccia nascosta). La regola
        // con :has() sta da sola: un browser che non la conosce scarterebbe l'intero elenco.
        if ( $rivela ) {
            $hover = '';
            $stato = '';
            $has   = '';
            foreach ( $rivela as $r ) {
                $hover .= "#{$uid}:hover {$r[0]}{{$r[1]}}";
                $stato .= "#{$uid}.is-revealed {$r[0]},#{$uid}:focus-visible {$r[0]}{{$r[1]}}";
                $has   .= "#{$uid}:has(.olo-rb-revealed-face :focus-visible) {$r[0]}{{$r[1]}}";
            }
            $css .= '@media (hover:hover){' . $hover . '}' . $stato . $has;
        }
        $css .= "#{$uid}:focus-visible{outline:2px solid var(--olo-color-primary, #e1474f);outline-offset:2px}";

        ob_start();
        echo '<style>' . $css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- reveal/transition CSS assembled above from the internally generated $uid, intval()'d heights/amounts, floatval()'d speed and a fixed easing map
        // Global background (behind everything): media object `media` (precedenza) →
        // fallback legacy image_url. Stesso layer posizionato z-index:0 di render_face_bg.
        $global_bg = $this->render_face_bg( $s['image_url'], $s['image_position'] ?? 'center center', $s['image_size'] ?? 'cover', '', $s['media'] ?? null, $uid . '-glob' );

        // tabindex: da tastiera il riquadro si raggiunge e mostra la faccia nascosta (con i suoi link).
        echo '<div id="' . esc_attr( $uid ) . '" class="olo-revealbox ' . esc_attr( $uid ) . ' olo-reveal-' . $effect . '" tabindex="0" role="group" style="' . $container_css . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $effect is sanitize_html_class()'d above; $container_css is built from intval()'d height/perspective, build_border_radius_css() (integer px) and a colour passed through safe_color_css()
        echo $global_bg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup assembled above with esc_url()/esc_attr() only
        echo $inner_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- faces assembled above from wp_kses_post()'d rich text (safe_richtext_content), esc_attr()/esc_url()'d media and alignment values, render_icon_html() icons, intval()'d paddings and preg_replace() character-whitelisted colours
        echo '</div>';
        ?>
        <script>
        (function(){
            function init(){
                var box = document.getElementById('<?php echo esc_js( $uid ); ?>');
                if(!box){return}
                if(box.getAttribute('data-olo-rb-ready')){return}
                box.setAttribute('data-olo-rb-ready','1');
                /* Tocco: un tap mostra la faccia nascosta, un altro la richiude; link e pulsanti restano link e pulsanti. */
                box.addEventListener('pointerup', function(e){
                    if(e.pointerType === 'mouse'){return}
                    if(e.target.closest('a,button,input,select,textarea,label,[data-olo-interactive]')){return}
                    box.classList.toggle('is-revealed');
                });
                document.addEventListener('pointerdown', function(e){
                    if(!box.contains(e.target)){ box.classList.remove('is-revealed'); }
                });
                /* Browser senza overflow:clip: il focus su un link nascosto farebbe scorrere il riquadro. */
                box.addEventListener('scroll', function(){
                    if(box.scrollTop !== 0){ box.scrollTop = 0; }
                    if(box.scrollLeft !== 0){ box.scrollLeft = 0; }
                });
            }
            if(document.readyState === 'loading'){ document.addEventListener('DOMContentLoaded', init); } else { init(); }
        })();
        </script>
        <?php
        // Border system (sul riquadro: ora porta anche la classe $uid, prima solo l'id)
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        // Raggio in hover: dopo il bordo in hover, di cui riprende la transizione (stesso elemento).
        $radius_hover_css  = Olobuild_Tile_Utils::radius_hover_rules( ".{$uid}", $s, 'border_radius_hover', Olobuild_Tile_Utils::transizione_di( $border_hover_css ) );
        if ( $border_css || $border_hover_css || $border_effect_css || $radius_hover_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . $radius_hover_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings; radius rules from Olobuild_Tile_Utils::radius_hover_rules() (integer px)
        }
        return ob_get_clean();
    }

    /**
     * Render background image or video for a face zone.
     *
     * Sfondo unificato: se $media è un oggetto background (type!=none) ha la
     * PRECEDENZA (pannello media universale immagine/video/gradiente/colore/gallery…):
     * si usa bg_media_parts() — css sul wrapper posizionato + markup (video/gallery)
     * al suo interno. Altrimenti resa LEGACY dai campi image_url/video_url (fallback,
     * i template non ri-salvati continuano a rendere identici).
     *
     * @param string      $image_url      Immagine legacy.
     * @param string      $image_position Focal legacy.
     * @param string      $image_size     Fit legacy.
     * @param string      $video_url      Video legacy.
     * @param array|null  $media          Oggetto background universale (top_media/bottom_media/media).
     * @param string      $scope          Scope univoco per la gallery (es. "{$uid}-top").
     */
    private function render_face_bg( $image_url, $image_position, $image_size, $video_url, $media = null, $scope = '' ) {
        // Precedenza al pannello media universale.
        if ( is_array( $media ) && ! empty( $media['type'] ) && $media['type'] !== 'none' ) {
            $mb = $this->bg_media_parts( $media, $scope );
            if ( $mb['has'] ) {
                return '<div style="position:absolute;inset:0;z-index:0;' . esc_attr( $mb['css'] ) . '">' . $mb['markup'] . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $mb['markup'] generato da Olobuild_CSS_Builder::get_bg_html_markup() (auto-escapato); $mb['css'] passato in esc_attr()
            }
        }
        // ── Fallback legacy ──
        if ( ! empty( $image_url ) ) {
            $bg_size = esc_attr( $image_size );
            $bg_pos  = esc_attr( $image_position );
            return '<div style="position:absolute;inset:0;background:url(' . esc_url( $image_url ) . ') ' . $bg_pos . '/' . $bg_size . ' no-repeat;z-index:0"></div>';
        }
        if ( ! empty( $video_url ) ) {
            return '<video style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;pointer-events:none;z-index:0" autoplay muted loop playsinline><source src="' . esc_url( $video_url ) . '" type="video/mp4"></video>';
        }
        return '';
    }

    /**
     * Icona della zona, con dimensione e colore. Passa da render_icon_html(): UIkit come
     * sempre, più le icone Lucide e personalizzate che il picker offre (prima non uscivano).
     */
    private function render_icon( $icon_name, $icon_size, $icon_color ) {
        if ( empty( $icon_name ) ) {
            return '';
        }
        $size  = floatval( $icon_size ) ?: 2;
        $color = $this->safe_color_css( $icon_color );
        $icon  = $this->render_icon_html( (string) $icon_name, $size, 'aria-hidden="true"' );
        if ( $icon === '' ) {
            return '';
        }
        return '<div style="line-height:1;margin-bottom:8px;color:' . $color . '">' . $icon . '</div>';
    }
}
