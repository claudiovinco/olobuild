<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Olo_Lightbox_Tile extends Olo_Tile_Base {

    protected $type     = 'lightbox';
    protected $name     = 'Lightbox';
    protected $icon     = 'dashicons-format-gallery';
    protected $category = 'media';

    public function get_controls() { return []; }

    public function render( $settings ) {
        $items   = $settings['items'] ?? [];
        if ( empty( $items ) ) return '';

        $cols    = absint( $settings['columns'] ?? 3 );
        $gap     = absint( $settings['gap'] ?? 15 );
        $ratio   = $settings['thumb_ratio'] ?? '1:1';
        $radius  = $this->build_border_radius_css( $settings["thumb_radius"] ?? 8 );
        $overlay = $settings['overlay_style'] ?? 'dark';
        $caption = ! empty( $settings['show_caption'] );
        $anim    = $settings['animation'] ?? 'fade';

        $ratio_map = [ '1:1' => '100%', '4:3' => '75%', '16:9' => '56.25%', 'auto' => '' ];
        $padding = $ratio_map[ $ratio ] ?? '100%';

        $html = '<div class="olo-lightbox-grid" uk-lightbox="animation: ' . esc_attr( $anim ) . '"'
              . ' style="display:grid;grid-template-columns:repeat(' . $cols . ',1fr);gap:' . $gap . 'px">';

        foreach ( $items as $item ) {
            $type  = $item['type'] ?? 'image';
            $url   = esc_url( $item['url'] ?? '' );
            $thumb = esc_url( $item['thumb'] ?? '' );
            $title = esc_attr( $item['title'] ?? '' );
            $cap   = esc_html( $item['caption'] ?? '' );

            if ( empty( $url ) ) continue;

            $src = $thumb ?: $url;
            $data_cap = $caption && $cap ? ' data-caption="' . $cap . '"' : '';

            $html .= '<a href="' . $url . '" data-type="' . esc_attr( $type ) . '"' . $data_cap . ' class="olo-lb-item"'
                   . ' style="display:block;position:relative;overflow:hidden;border-radius:' . $radius . 'px">';

            if ( $padding ) {
                $html .= '<div style="padding-bottom:' . $padding . ';position:relative">';
                $html .= '<img src="' . $src . '" alt="' . $title . '" loading="lazy" decoding="async"'
                       . ' style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover" />';
                $html .= '</div>';
            } else {
                $html .= '<img src="' . $src . '" alt="' . $title . '" loading="lazy" decoding="async"'
                       . ' style="width:100%;display:block" />';
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
                $html .= '<div style="position:absolute;bottom:0;left:0;right:0;padding:6px 10px;background:rgba(0,0,0,0.6);color:#fff;font-size:12px">' . $cap . '</div>';
            }

            $html .= '</a>';
        }

        $html .= '</div>';
        $html .= '<style>.olo-lb-item:hover .olo-lb-overlay{opacity:1!important}</style>';

        return $html;
    }
}
