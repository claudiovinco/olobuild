<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Section Header — eyebrow + headline editoriale multi-riga + tagline destra.
 * Allineato agli standard Olobuild (docs/TILE-PERFETTA.md):
 *   - select per ogni enum, color picker globe per i colori
 *   - sistema base si occupa di padding/margin/border-radius/sfondo
 *   - headline_align 4 opzioni (incluso justify)
 */
class Olobuild_SectionHeader_Tile extends Olobuild_Tile_Base {

    protected $type     = 'section-header';
    protected $name     = 'Section Header';
    protected $icon     = 'dashicons-heading';
    protected $category = 'layout';
    protected $defaults = [
        'eyebrow_show'      => true,
        'eyebrow_text'      => 'PROVALO SUBITO',
        'eyebrow_color'     => '#b3261e',
        'eyebrow_dot_color' => '#b3261e',
        'eyebrow_separator' => '— ',

        'headline_lines' => [
            [ 'text' => 'Nessun rischio,',  'color' => '#0f172a', 'italic' => false ],
            [ 'text' => 'solo prodotto.',   'color' => '#b3261e', 'italic' => true  ],
        ],
        'headline_font_family' => 'serif',
        'headline_font_size'   => 96,
        'headline_line_height' => 1.0,
        'headline_font_weight' => '700',
        'headline_align'       => 'left',
        'headline_inline'      => false,

        'tagline_show'          => true,
        'tagline_text'          => 'Try before you trust',
        'tagline_text_italic'   => true,
        'tagline_text_color'    => '#0f172a',
        'tagline_text_size'     => 22,
        'tagline_caption'       => 'TRE GARANZIE · CINQUE PROMESSE',
        'tagline_caption_color' => '',
        'tagline_caption_size'  => 11,

        'layout'         => 'split',
        'split_ratio'    => '1.6fr 1fr',
        'gap'            => 60,
        'vertical_align' => 'end',
    ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $serif = "var(--olo-font-family-heading, 'Playfair Display','Cormorant Garamond',Georgia,'Times New Roman',serif)";
        $sans  = "var(--olo-font-family, 'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
        $mono  = "ui-monospace,'SF Mono',Menlo,Consolas,monospace";
        // Valori legacy ('serif'/'sans-serif'/'mono') → stack storici della tile;
        // valori nuovi (type 'font-family') → CSS pronto via resolver condiviso.
        $legacy = [ 'serif' => $serif, 'sans-serif' => $sans, 'mono' => $mono ];
        $hfam   = $this->resolve_font_family( $s['headline_font_family'], $legacy ) ?: $serif;

        $layout = in_array( $s['layout'], [ 'split', 'stack', 'center' ], true ) ? $s['layout'] : 'split';
        $valign = in_array( $s['vertical_align'], [ 'start', 'center', 'end', 'baseline' ], true ) ? $s['vertical_align'] : 'end';
        $gap    = max( 0, min( 200, absint( $s['gap'] ?? 60 ) ) );
        $ratios = [ '1fr 1fr', '1.6fr 1fr', '2fr 1fr', '1fr 2fr' ];
        $split  = in_array( $s['split_ratio'], $ratios, true ) ? $s['split_ratio'] : '1.6fr 1fr';

        $grid_style = 'display:grid;gap:' . $gap . 'px;align-items:' . $valign . ';';
        if ( $layout === 'split' ) {
            $grid_style .= 'grid-template-columns:' . $split . ';';
        } elseif ( $layout === 'center' ) {
            $grid_style .= 'grid-template-columns:1fr;text-align:center;';
        } else {
            $grid_style .= 'grid-template-columns:1fr;';
        }

        $hsize   = absint( $s['headline_font_size'] ) ?: 96;
        $hlh     = floatval( $s['headline_line_height'] ) ?: 1.0;
        $hweight = preg_match( '/^\d+$/', (string) $s['headline_font_weight'] ) ? $s['headline_font_weight'] : '700';
        $halign  = in_array( $s['headline_align'] ?? 'left', [ 'left', 'center', 'right', 'justify' ], true ) ? ( $s['headline_align'] ?? 'left' ) : 'left';
        if ( $layout === 'center' ) $halign = 'center';
        $inline  = ! empty( $s['headline_inline'] );

        $eb_color = $this->safe_color_css( $s['eyebrow_color'] ) ?: '#b3261e';
        $eb_dot   = $this->safe_color_css( $s['eyebrow_dot_color'] ) ?: '#b3261e';
        $eb_sep   = $s['eyebrow_separator'] ?? '';
        $is_bullet = ( trim( $eb_sep ) === '·' );

        $show_tagline  = ! empty( $s['tagline_show'] ) && $layout === 'split';
        $show_subtitle = ! empty( $s['tagline_show'] ) && $layout !== 'split' && ! empty( $s['tagline_text'] );

        ob_start();
        ?>
        <div class="olo-sechead" style="<?php echo esc_attr( $grid_style ); ?>">
            <div class="olo-sechead__left">
                <?php if ( ! empty( $s['eyebrow_show'] ) && ! empty( $s['eyebrow_text'] ) ) : ?>
                    <div class="olo-sechead__eyebrow" style="display:inline-flex;align-items:center;gap:10px;font-family:<?php echo esc_attr( $mono ); ?>;font-size:12px;letter-spacing:0.1em;text-transform:uppercase;color:<?php echo esc_attr( $eb_color ); ?>;margin-bottom:24px">
                        <?php if ( $is_bullet ) : ?>
                            <span style="width:10px;height:10px;border-radius:50%;background:<?php echo esc_attr( $eb_dot ); ?>"></span>
                        <?php elseif ( $eb_sep ) : ?>
                            <span style="white-space:pre"><?php echo esc_html( $eb_sep ); ?></span>
                        <?php endif; ?>
                        <span data-olo-editable="eyebrow_text"><?php echo esc_html( $s['eyebrow_text'] ); ?></span>
                    </div>
                <?php endif; ?>

                <?php
                $headlines = is_array( $s['headline_lines'] ) ? $s['headline_lines'] : [];
                if ( $headlines ) :
                ?>
                    <h2 class="olo-sechead__headline" style="font-family:<?php echo esc_attr( $hfam ); ?>;font-size:<?php echo (int) $hsize; ?>px;line-height:<?php echo (float) $hlh; ?>;font-weight:<?php echo esc_attr( $hweight ); ?>;letter-spacing:-0.02em;text-align:<?php echo esc_attr( $halign ); ?>;margin:0">
                        <?php $first = true; foreach ( $headlines as $idx => $line ) :
                            $ltext = $line['text'] ?? '';
                            if ( $ltext === '' ) continue;
                            $lcolor  = $this->safe_color_css( $line['color'] ?? '' ) ?: '#0f172a';
                            $litalic = ! empty( $line['italic'] ) ? 'font-style:italic;' : '';
                            if ( $inline && ! $first ) echo ' ';
                            $first = false;
                        ?>
                            <span style="display:<?php echo $inline ? 'inline' : 'block'; ?>;color:<?php echo esc_attr( $lcolor ); ?>;<?php echo esc_attr( $litalic ); ?>" data-olo-editable="<?php echo 'headline_lines.' . intval( $idx ) . '.text'; ?>"><?php echo esc_html( $ltext ); ?></span>
                        <?php endforeach; ?>
                    </h2>
                <?php endif; ?>

                <?php if ( $show_subtitle ) :
                    $sub_clr    = $this->safe_color_css( $s['tagline_text_color'] ) ?: '#475569';
                    $sub_size   = absint( $s['tagline_text_size'] ) ?: 18;
                    $sub_italic = ! empty( $s['tagline_text_italic'] ) ? 'font-style:italic;' : '';
                    $sub_align  = ( $layout === 'center' ) ? 'center' : $halign;
                    $sub_mx     = ( $layout === 'center' ) ? 'margin-left:auto;margin-right:auto;' : '';
                    $sub_mt     = max( 8, min( 80, $gap ) );
                ?>
                    <p class="olo-sechead__sub" style="font-family:<?php echo esc_attr( $sans ); ?>;font-size:<?php echo (int) $sub_size; ?>px;line-height:1.6;color:<?php echo esc_attr( $sub_clr ); ?>;<?php echo esc_attr( $sub_italic ); ?>max-width:62ch;<?php echo esc_attr( $sub_mx ); ?>margin-top:<?php echo (int) $sub_mt; ?>px;text-align:<?php echo esc_attr( $sub_align ); ?>" data-olo-editable="tagline_text"><?php echo esc_html( $s['tagline_text'] ); ?></p>
                <?php endif; ?>
            </div>

            <?php if ( $show_tagline ) :
                $tag_clr     = $this->safe_color_css( $s['tagline_text_color'] ) ?: '#0f172a';
                $tag_size    = absint( $s['tagline_text_size'] ) ?: 22;
                $cap_clr     = $this->safe_color_css( $s['tagline_caption_color'] ) ?: 'var(--olo-color-text-faint, #9ca3af)';
                $cap_size    = absint( $s['tagline_caption_size'] ) ?: 11;
                $tag_italic  = ! empty( $s['tagline_text_italic'] ) ? 'font-style:italic;' : '';
            ?>
                <div class="olo-sechead__right" style="text-align:right">
                    <?php if ( ! empty( $s['tagline_text'] ) ) : ?>
                        <div class="olo-sechead__tag" style="font-family:<?php echo esc_attr( $hfam ); ?>;font-size:<?php echo (int) $tag_size; ?>px;color:<?php echo esc_attr( $tag_clr ); ?>;<?php echo esc_attr( $tag_italic ); ?>line-height:1.3;margin-bottom:10px" data-olo-editable="tagline_text"><?php echo esc_html( $s['tagline_text'] ); ?></div>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['tagline_caption'] ) ) : ?>
                        <div class="olo-sechead__cap" style="font-family:<?php echo esc_attr( $mono ); ?>;font-size:<?php echo (int) $cap_size; ?>px;letter-spacing:0.1em;text-transform:uppercase;color:<?php echo esc_attr( $cap_clr ); ?>" data-olo-editable="tagline_caption"><?php echo esc_html( $s['tagline_caption'] ); ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <style>
            @media (max-width:900px) {
                .olo-sechead { grid-template-columns: 1fr !important; gap: 24px !important; text-align: left !important; }
                .olo-sechead__right { text-align: left !important; }
                .olo-sechead__headline { font-size: clamp(40px, 10vw, 72px) !important; }
            }
        </style>
        <?php

        return ob_get_clean();
    }
}
