<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Olobuild_Lightbox_Tile extends Olobuild_Tile_Base {

    protected $type     = 'lightbox';
    protected $name     = 'Lightbox';
    protected $icon     = 'dashicons-format-gallery';
    protected $category = 'media';

    public function get_controls() { return []; }

    public function render( $settings ) {
        $items   = $settings['items'] ?? [];
        $uid = 'olo-lb-' . wp_rand( 10000, 99999 );
        $builder_mode = ! empty( $settings['_builder_mode'] );
        // In builder mode mostriamo sempre un'anteprima (placeholder vuoti inclusi)
        // così l'utente vede dove cliccare per aggiungere immagini.
        if ( empty( $items ) ) {
            if ( ! $builder_mode ) return '';
            $items = [ [ 'id' => 'placeholder', 'type' => 'image', 'url' => '', 'thumb' => '', 'title' => '', 'caption' => '' ] ];
        }

        $cols    = absint( $settings['columns'] ?? 3 );
        $gap     = absint( $settings['gap'] ?? 15 );
        $ratio   = $settings['thumb_ratio'] ?? '1:1';
        $radius  = $this->build_border_radius_css( $settings["thumb_radius"] ?? 8 );
        $overlay = $settings['overlay_style'] ?? 'dark';
        $caption = ! empty( $settings['show_caption'] );
        $anim    = $settings['animation'] ?? 'fade';

        $obj_pos = trim( (string) ( $settings['object_position'] ?? 'center center' ) );
        if ( $obj_pos === '' ) { $obj_pos = 'center center'; }

        // La miniatura si ritaglia col padding-bottom percentuale (tecnica storica di
        // questa tile: NON aspect-ratio, cambiarla sposterebbe le gallerie pubblicate).
        // La tabella e' la traduzione del rapporto in percentuale ed e' l'UNICO posto
        // che decide quali proporzioni la tile sa davvero rendere: va tenuta allineata
        // all'elenco del select (ratioOptions sep:':'), altrimenti una voce nuova
        // cadrebbe nel fallback '100%' e ritaglierebbe quadrato senza dirlo.
        $ratio_map = [
            '1:1'  => '100%',
            '4:3'  => '75%',
            '3:2'  => '66.67%',
            '16:9' => '56.25%',
            '21:9' => '42.86%',
            '3:4'  => '133.33%',
            '4:5'  => '125%',
            '9:16' => '177.78%',
            '2:3'  => '150%',
            'auto' => '',
        ];
        $padding = $ratio_map[ $ratio ] ?? '100%';

        // Adattamento: campo nuovo, default 'cover' = l'hardcode di prima → no-op.
        // Si emette in TUTTI E DUE i rami, anche in 'auto' dove finora non c'era
        // alcun object-fit. Non e' una svista: nel ramo 'auto' l'<img> e'
        // `width:100%` senza altezza, quindi il riquadro ha esattamente la forma
        // della foto e 'cover' disegna gli stessi pixel dell'iniziale 'fill' →
        // le gallerie pubblicate non si muovono. In compenso 'none' e 'scale-down'
        // li' fanno un lavoro vero (foto a dimensione naturale, ritagliata dal
        // riquadro): senza questa riga sarebbero due voci del menu senza effetto.
        // Col 'fill' l'immagine e' deformata per riempire e il punto focale non
        // sposterebbe nulla: non lo si scrive, come fa image_frame().
        $fit = (string) ( $settings['thumb_fit'] ?? 'cover' );
        if ( ! in_array( $fit, [ 'cover', 'contain', 'fill', 'none', 'scale-down' ], true ) ) { $fit = 'cover'; }
        $fit_css = 'object-fit:' . $fit . ';';
        if ( $fit !== 'fill' ) { $fit_css .= 'object-position:' . esc_attr( $obj_pos ) . ';'; }

        $preset_id = isset( $settings['preset'] ) ? sanitize_key( $settings['preset'] ) : 'custom';
        $html = '<div class="olo-lightbox-grid ' . esc_attr( $uid ) . ' olo-lb-preset-' . esc_attr( $preset_id ) . '" uk-lightbox="animation: ' . esc_attr( $anim ) . '"'
              . ' style="display:grid;grid-template-columns:repeat(' . $cols . ',1fr);gap:' . $gap . 'px">';

        foreach ( $items as $item ) {
            $type  = $item['type'] ?? 'image';
            $url   = esc_url( $item['url'] ?? '' );
            $thumb = esc_url( $item['thumb'] ?? '' );
            $title = esc_attr( $item['title'] ?? '' );
            $cap   = esc_html( $item['caption'] ?? '' );

            // Frontend: skippa items vuoti. Builder: render placeholder visivo.
            if ( empty( $url ) && ! $builder_mode ) continue;

            $src = $thumb ?: $url;
            $data_cap = $caption && $cap ? ' data-caption="' . $cap . '"' : '';

            if ( $url ) {
                $html .= '<a href="' . $url . '" data-type="' . esc_attr( $type ) . '"' . $data_cap . ' class="olo-lb-item"'
                       . ' style="display:block;position:relative;overflow:hidden;border-radius:' . $radius . '">';
            } else {
                $html .= '<div class="olo-lb-item olo-lb-item--empty"'
                       . ' style="display:block;position:relative;overflow:hidden;border-radius:' . $radius . ';background:#F3F4F6">';
            }

            if ( $padding ) {
                $html .= '<div style="padding-bottom:' . $padding . ';position:relative">';
                if ( $url || $src ) {
                    $html .= '<img src="' . $src . '" alt="' . $title . '" loading="lazy" decoding="async"'
                           . ' style="position:absolute;inset:0;width:100%;height:100%;' . $fit_css . '" />';
                } else {
                    // Placeholder SVG inline (icona immagine grigia su sfondo neutro)
                    $html .= '<div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#9CA3AF">'
                           . '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>'
                           . '</div>';
                }
                $html .= '</div>';
            } else {
                if ( $url || $src ) {
                    // Ramo 'auto': niente riquadro percentuale, l'immagine tiene la
                    // sua altezza. $fit_css porta object-fit (+ object-position, che
                    // qui c'era già) — vedi la nota sopra sul perché non muove nulla.
                    $html .= '<img src="' . $src . '" alt="' . $title . '" loading="lazy" decoding="async"'
                           . ' style="width:100%;display:block;' . $fit_css . '" />';
                } else {
                    $html .= '<div style="aspect-ratio:1/1;display:flex;align-items:center;justify-content:center;color:#9CA3AF">'
                           . '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>'
                           . '</div>';
                }
            }

            // Hover overlay
            if ( $overlay !== 'none' ) {
                $bg = $overlay === 'light' ? 'rgba(255,255,255,0.4)' : 'rgba(0,0,0,0.35)';
                $color = $overlay === 'light' ? '#000' : '#fff';
                $html .= '<div class="olo-lb-overlay" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:' . $bg . ';opacity:0;transition:opacity 0.2s">';
                $html .= '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="' . $color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>';
                $html .= '</div>';
            }

            // Caption
            if ( $caption && $cap ) {
                list( $lc_cls, $lc_data ) = $this->tfx_attrs( $settings, 'caption', $cap );
                $html .= '<div class="olo-lb-cap' . $lc_cls . '" style="position:absolute;bottom:0;left:0;right:0;padding:6px 10px;background:rgba(0,0,0,0.6);color:#fff;font-size:12px"' . $lc_data . '>' . $cap . '</div>';
            }

            $html .= $url ? '</a>' : '</div>';
        }

        $html .= '</div>';
        $html .= '<style>.olo-lb-item:hover .olo-lb-overlay{opacity:1!important}</style>';

        $tfx_css = $this->tfx_css( $settings, '.olo-lightbox-grid' );
        if ( $tfx_css ) $html .= '<style>' . $tfx_css . '</style>';
        ob_start(); $this->tfx_print_script(); $html .= ob_get_clean();

        $border_css        = $this->build_border_css( [] );
        $border_hover_css  = $this->build_border_hover_css( '.$uid', [], [], 300 );
        $border_effect_css = $this->build_border_effect_css( '.$uid', [], $settings );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            $html .= '<style>';
            if ( $border_css ) $html .= '.' . $uid . '{' . $border_css . '}';
            $html .= $border_hover_css . $border_effect_css . '</style>';
        }
        return $html;
    }
}
