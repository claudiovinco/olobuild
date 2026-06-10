<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Work Grid — griglia di lavori con immagine (placeholder a strisce se vuota),
 * titolo + meta e descrizione, zoom dell'immagine al hover.
 */
class Olo_WorkGrid_Tile extends Olo_Tile_Base {

    protected $type     = 'workgrid';
    protected $name     = 'Work Grid';
    protected $icon     = 'dashicons-screenoptions';
    protected $category = 'layout';
    protected $defaults = [
        'items' => [
            [ 'image' => '', 'media_label' => 'Marisol — identity system', 'title' => 'Marisol',      'meta' => "'26 — Brand",      'description' => 'A coastal hotel group, rebuilt around one mark and a lot of restraint.', 'link_url' => '', 'tall' => false ],
            [ 'image' => '', 'media_label' => 'Atlas Press — book covers', 'title' => 'Atlas Press',   'meta' => "'25 — Editorial",  'description' => "An independent publisher's new look, from spine to site.", 'link_url' => '', 'tall' => true ],
            [ 'image' => '', 'media_label' => 'Field Museum — wayfinding', 'title' => 'Field Museum',  'meta' => "'25 — Wayfinding", 'description' => 'A signage and type system that quietly tells you where you are.', 'link_url' => '', 'tall' => true ],
            [ 'image' => '', 'media_label' => 'Cobalt — product UI',       'title' => 'Cobalt',        'meta' => "'24 — Product",    'description' => 'Brand and interface for a developer tool that hates noise.', 'link_url' => '', 'tall' => false ],
        ],
        'columns'           => 2,
        'items_gap'         => 32,
        'media_aspect'      => '4/3',
        'media_tall_aspect' => '4/5',
        'media_bg'          => '#ebe7dc',
        'media_label_color' => '#18181a',
        'hover_zoom'        => true,
        'title_font_family' => 'heading',
        'title_color'       => '#18181a',
        'title_size'        => 22,
        'title_weight'      => '500',
        'meta_color'        => '#8d8a82',
        'meta_size'         => 12,
        'show_desc'         => true,
        'desc_color'        => '#8d8a82',
        'desc_size'         => 15,
        'mono_font_family'  => '',
    ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-workgrid-' . wp_rand( 10000, 99999 );

        $heading = "var(--olo-font-family-heading, 'DM Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
        $body    = "var(--olo-font-family, 'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
        $mono_fb = "ui-monospace,'SF Mono',Menlo,Consolas,monospace";
        $mono_fam = $this->resolve_font_family( $s['mono_font_family'] ?? '' );
        // Nome font puro (legacy campo text) → wrap con lo stack mono di fallback storico.
        if ( $mono_fam !== '' && preg_match( '/^[A-Za-z0-9 \-]+$/', $mono_fam ) ) {
            $mono_fam = "'" . $mono_fam . "'," . $mono_fb;
        }
        $mono    = $mono_fam !== '' ? $mono_fam : $mono_fb;
        $tfam    = $this->resolve_font_family( $s['title_font_family'] ?? '', [ 'heading' => $heading, 'body' => $body, 'mono' => $mono ] ) ?: $heading;

        $cols       = max( 1, min( 4, absint( $s['columns'] ) ) );
        $gap        = max( 8, min( 60, absint( $s['items_gap'] ) ) );
        $title_size = max( 14, min( 48, absint( $s['title_size'] ) ) );
        $title_wt   = preg_match( '/^\d+$/', (string) $s['title_weight'] ) ? $s['title_weight'] : '500';
        $meta_size  = max( 10, min( 18, absint( $s['meta_size'] ) ) );
        $desc_size  = max( 12, min( 20, absint( $s['desc_size'] ) ) );

        $aspect_allow = [ '16/9', '16/10', '4/3', '3/2', '1/1' ];
        $aspect       = in_array( $s['media_aspect'] ?? '4/3', $aspect_allow, true ) ? $s['media_aspect'] : '4/3';
        $tall_allow   = [ '4/5', '3/4', '2/3' ];
        $tall_aspect  = in_array( $s['media_tall_aspect'] ?? '4/5', $tall_allow, true ) ? $s['media_tall_aspect'] : '4/5';

        $media_bg   = $this->safe_color_css( $s['media_bg'] ) ?: '#ebe7dc';
        $label_c    = $this->safe_color_css( $s['media_label_color'] ) ?: '#18181a';
        $title_c    = $this->safe_color_css( $s['title_color'] ) ?: '#18181a';
        $meta_c     = $this->safe_color_css( $s['meta_color'] ) ?: '#8d8a82';
        $desc_c     = $this->safe_color_css( $s['desc_color'] ) ?: '#8d8a82';
        $stripe     = $this->_hex_alpha( $label_c, '0d' );
        $label_dim  = $this->_hex_alpha( $label_c, '66' );
        $hover_zoom = ! empty( $s['hover_zoom'] );
        $show_desc  = ! empty( $s['show_desc'] );

        $items = is_array( $s['items'] ) ? $s['items'] : [];
        $grid_style = 'display:grid;grid-template-columns:repeat(' . $cols . ',minmax(0,1fr));gap:' . $gap . 'px;';

        ob_start();
        ?>
        <div class="olo-workgrid <?php echo esc_attr( $uid ); ?>" style="<?php echo esc_attr( $grid_style ); ?>">
            <?php foreach ( $items as $idx => $it ) :
                $image   = $it['image'] ?? '';
                $m_label = $it['media_label'] ?? '';
                $title   = $it['title'] ?? '';
                $meta    = $it['meta'] ?? '';
                $tall    = ! empty( $it['tall'] );
                $ar      = $tall ? $tall_aspect : $aspect;
                $link    = $it['link_url'] ?? '';
                $tag     = $link ? 'a' : 'div';
                $attrs   = $link ? ' href="' . esc_url( $link ) . '"' : '';
                $desc_raw = $it['description'] ?? '';
                $desc     = preg_match( '/<[a-z!\/][^>]*>/i', $desc_raw ) ? $this->safe_richtext_content( $desc_raw ) : nl2br( esc_html( $desc_raw ) );
            ?>
                <<?php echo $tag . $attrs; ?> class="olo-workgrid__item">
                    <div class="olo-workgrid__media" style="aspect-ratio:<?php echo esc_attr( $ar ); ?>;overflow:hidden;margin-bottom:16px;background:<?php echo esc_attr( $media_bg ); ?>;">
                        <?php if ( $image ) : ?>
                            <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;" />
                        <?php else : ?>
                            <div class="olo-workgrid__ph" style="width:100%;height:100%;position:relative;background-image:repeating-linear-gradient(135deg,<?php echo esc_attr( $stripe ); ?> 0 15px,transparent 15px 30px);">
                                <?php if ( $m_label ) : ?>
                                    <span style="position:absolute;left:13px;right:13px;bottom:11px;font-family:<?php echo esc_attr( $mono ); ?>;font-size:10.5px;letter-spacing:.02em;text-transform:uppercase;color:<?php echo esc_attr( $label_dim ); ?>;" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.media_label'; ?>"><?php echo esc_html( $m_label ); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="olo-workgrid__b" style="display:flex;align-items:baseline;justify-content:space-between;gap:16px;">
                        <?php if ( $title !== '' ) : ?>
                            <h3 style="font-family:<?php echo esc_attr( $tfam ); ?>;font-weight:<?php echo esc_attr( $title_wt ); ?>;font-size:<?php echo $title_size; ?>px;letter-spacing:-0.02em;color:<?php echo esc_attr( $title_c ); ?>;margin:0;" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.title'; ?>"><?php echo esc_html( $title ); ?></h3>
                        <?php endif; ?>
                        <?php if ( $meta !== '' ) : ?>
                            <span style="flex:none;font-family:<?php echo esc_attr( $mono ); ?>;font-size:<?php echo $meta_size; ?>px;letter-spacing:.02em;text-transform:uppercase;color:<?php echo esc_attr( $meta_c ); ?>;" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.meta'; ?>"><?php echo esc_html( $meta ); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if ( $show_desc && $desc ) : ?>
                        <p style="font-size:<?php echo $desc_size; ?>px;line-height:1.5;color:<?php echo esc_attr( $desc_c ); ?>;margin:6px 0 0;" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.description'; ?>" data-olo-richtext><?php echo $desc; ?></p>
                    <?php endif; ?>
                </<?php echo $tag; ?>>
            <?php endforeach; ?>
        </div>
        <style>
            .<?php echo $uid; ?> .olo-workgrid__item { display: block; color: inherit; text-decoration: none; }
            .<?php echo $uid; ?> .olo-workgrid__media img, .<?php echo $uid; ?> .olo-workgrid__media .olo-workgrid__ph { transition: transform .6s cubic-bezier(.2,.7,.3,1); }
            <?php if ( $hover_zoom ) : ?>
            .<?php echo $uid; ?> .olo-workgrid__item:hover .olo-workgrid__media img, .<?php echo $uid; ?> .olo-workgrid__item:hover .olo-workgrid__media .olo-workgrid__ph { transform: scale(1.04); }
            <?php endif; ?>
            .<?php echo $uid; ?> a.olo-workgrid__item:focus-visible { outline: 2px solid <?php echo $title_c; ?>; outline-offset: 4px; }
            @media (max-width: 680px) {
                .<?php echo $uid; ?> { grid-template-columns: 1fr !important; }
            }
        </style>
        <?php
        return ob_get_clean();
    }

    /** Aggiunge un alpha hex (es. '0d', '66') a un colore #rrggbb; passa attraverso non-hex. */
    private function _hex_alpha( $hex, $alpha ) {
        $h = ltrim( (string) $hex, '#' );
        if ( strlen( $h ) === 6 && ctype_xdigit( $h ) ) {
            return '#' . $h . $alpha;
        }
        return $hex; // rgba/var/ecc.: lascia invariato
    }
}
