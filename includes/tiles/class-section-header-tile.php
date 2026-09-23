<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Section Header — occhiello + titolo editoriale multi-riga + sottotitolo.
 * Allineato agli standard Olobuild (docs/TILE-PERFETTA.md):
 *   - select per ogni enum, color picker globe per i colori
 *   - sistema base si occupa di padding/margin/border-radius/sfondo
 *   - headline_align 4 opzioni (incluso justify)
 *
 * Ogni controllo dell'inspector corrisponde a una proprietà che questo renderer
 * scrive davvero; quelli che in una certa disposizione non avrebbero effetto
 * (la didascalia fuori dalla disposizione affiancata, il colore del pallino
 * senza pallino…) l'inspector non li mostra affatto.
 */
class Olobuild_SectionHeader_Tile extends Olobuild_Tile_Base {

    protected $type     = 'section-header';
    protected $name     = 'Section Header';
    protected $icon     = 'dashicons-heading';
    protected $category = 'layout';
    protected $defaults = [
        'typography_preset' => '',

        'eyebrow_show'        => true,
        'eyebrow_text'        => 'PROVALO SUBITO',
        'eyebrow_color'       => 'var(--olo-color-primary, #b3261e)',
        'eyebrow_dot_color'   => 'var(--olo-color-primary, #b3261e)',
        'eyebrow_separator'   => '— ',
        'eyebrow_font_family' => '',
        'eyebrow_font_size'   => '',
        'eyebrow_font_weight' => '',

        'headline_lines' => [
            [ 'text' => 'Nessun rischio,',  'color' => 'var(--olo-color-dark, #0f172a)', 'italic' => false ],
            [ 'text' => 'solo prodotto.',   'color' => 'var(--olo-color-dark, #b3261e)', 'italic' => true  ],
        ],
        'headline_font_family' => 'serif',
        'headline_font_size'   => 96,
        'headline_line_height' => 1.0,
        'headline_font_weight' => '700',
        'headline_align'       => 'left',
        'headline_inline'      => false,

        'tagline_show'                => true,
        'tagline_text'                => 'Try before you trust',
        'tagline_text_italic'         => true,
        'tagline_text_color'          => 'var(--olo-color-dark, #0f172a)',
        'tagline_text_size'           => 22,
        'tagline_font_family'         => '',
        'tagline_font_weight'         => '',
        'tagline_caption'             => 'TRE GARANZIE · CINQUE PROMESSE',
        'tagline_caption_color'       => '',
        'tagline_caption_size'        => 11,
        'tagline_caption_font_family' => '',
        'tagline_caption_font_weight' => '',

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

        // Dimensione del titolo. Vale quella scelta; tablet e telefono possono
        // averne una propria. Senza, sotto i 900px il titolo si riduce da solo —
        // ma senza mai superare la misura scelta: prima una regola !important lo
        // portava fra 40 e 72px qualunque cosa ci fosse scritto (12px diventavano
        // 72) e il controllo sembrava non fare niente. Le misure viaggiano in
        // variabili sull'h2: la regola responsive sotto le legge senza !important.
        $hsize_t = absint( $s['headline_font_size_tablet'] ?? 0 );
        $hsize_m = absint( $s['headline_font_size_mobile'] ?? 0 );
        $h_size_css = 'font-size:var(--olo-sh-fs-now,' . $hsize . 'px);--olo-sh-fs:' . $hsize . 'px;';
        if ( $hsize_t ) $h_size_css .= '--olo-sh-fs-t:' . $hsize_t . 'px;';
        if ( $hsize_m ) $h_size_css .= '--olo-sh-fs-m:' . $hsize_m . 'px;';

        // Stile tipografico: qui vale per il TITOLO, non per tutta la tile.
        // Famiglia, peso, interlinea, spaziatura e maiuscole vengono dal set (per
        // questo l'inspector, a set collegato, quelle righe non le mostra); i
        // valori della tile restano come riserva se il set viene cancellato.
        $tp        = sanitize_key( (string) ( $s['typography_preset'] ?? '' ) );
        $h_fam_css = $hfam;
        $h_fw_css  = (string) $hweight;
        $h_lh_css  = (string) (float) $hlh;
        $h_ls_css  = '-0.02em';
        $h_tt_css  = '';
        if ( $tp !== '' ) {
            $h_fam_css = "var(--olo-font-{$tp}-family, {$hfam})";
            $h_fw_css  = "var(--olo-font-{$tp}-weight, {$hweight})";
            $h_lh_css  = "var(--olo-font-{$tp}-line-height, {$h_lh_css})";
            $h_ls_css  = "var(--olo-font-{$tp}-letter-spacing, -0.02em)";
            $h_tt_css  = "text-transform:var(--olo-font-{$tp}-transform, none);";
        }
        $h_style = 'font-family:' . $h_fam_css . ';' . $h_size_css
            . 'line-height:' . $h_lh_css . ';font-weight:' . $h_fw_css . ';letter-spacing:' . $h_ls_css . ';'
            . $h_tt_css . 'text-align:' . $halign . ';margin:0';

        $eb_color = $this->safe_color_css( $s['eyebrow_color'] ) ?: '#b3261e';
        $eb_dot   = $this->safe_color_css( $s['eyebrow_dot_color'] ) ?: '#b3261e';
        $eb_sep   = $s['eyebrow_separator'] ?? '';
        $is_bullet = ( trim( $eb_sep ) === '·' );
        // Occhiello: mono 12px finché non si sceglie altro.
        $eb_fam  = $this->resolve_font_family( (string) $s['eyebrow_font_family'], $legacy ) ?: $mono;
        $eb_size = absint( $s['eyebrow_font_size'] ) ?: 12;
        $eb_fw   = $this->font_weight_css( $s['eyebrow_font_weight'] );
        $eb_style = 'display:inline-flex;align-items:center;gap:10px;font-family:' . $eb_fam . ';font-size:' . $eb_size . 'px;'
            . ( $eb_fw !== '' ? 'font-weight:' . $eb_fw . ';' : '' )
            . 'letter-spacing:0.1em;text-transform:uppercase;color:' . $eb_color . ';margin-bottom:24px';

        $show_tagline  = ! empty( $s['tagline_show'] ) && $layout === 'split';
        $show_subtitle = ! empty( $s['tagline_show'] ) && $layout !== 'split' && ! empty( $s['tagline_text'] );
        $tag_fw = $this->font_weight_css( $s['tagline_font_weight'] );

        ob_start();
        ?>
        <div class="olo-sechead" style="<?php echo esc_attr( $grid_style ); ?>">
            <?php // L'allineamento vale per l'intera colonna: occhiello, titolo e sottotitolo si muovono insieme. ?>
            <div class="olo-sechead__left" style="text-align:<?php echo esc_attr( $halign ); ?>">
                <?php if ( ! empty( $s['eyebrow_show'] ) && ! empty( $s['eyebrow_text'] ) ) : ?>
                    <div class="olo-sechead__eyebrow" style="<?php echo esc_attr( $eb_style ); ?>">
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
                    <h2 class="olo-sechead__headline" style="<?php echo esc_attr( $h_style ); ?>">
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
                    $sub_fam    = $this->resolve_font_family( (string) $s['tagline_font_family'], $legacy ) ?: $sans;
                    $sub_italic = ! empty( $s['tagline_text_italic'] ) ? 'font-style:italic;' : '';
                    $sub_fw     = $tag_fw !== '' ? 'font-weight:' . $tag_fw . ';' : '';
                    $sub_align  = ( $layout === 'center' ) ? 'center' : $halign;
                    $sub_mx     = ( $layout === 'center' ) ? 'margin-left:auto;margin-right:auto;' : '';
                    $sub_mt     = max( 8, min( 80, $gap ) );
                ?>
                    <p class="olo-sechead__sub" style="font-family:<?php echo esc_attr( $sub_fam ); ?>;font-size:<?php echo (int) $sub_size; ?>px;<?php echo esc_attr( $sub_fw ); ?>line-height:1.6;color:<?php echo esc_attr( $sub_clr ); ?>;<?php echo esc_attr( $sub_italic ); ?>max-width:62ch;<?php echo esc_attr( $sub_mx ); ?>margin-top:<?php echo (int) $sub_mt; ?>px;text-align:<?php echo esc_attr( $sub_align ); ?>" data-olo-editable="tagline_text"><?php echo esc_html( $s['tagline_text'] ); ?></p>
                <?php endif; ?>
            </div>

            <?php if ( $show_tagline ) :
                $tag_clr     = $this->safe_color_css( $s['tagline_text_color'] ) ?: '#0f172a';
                $tag_size    = absint( $s['tagline_text_size'] ) ?: 22;
                // Il sottotitolo a destra usa, di suo, la famiglia del titolo (quella
                // della tile, non lo stile tipografico: quello è del solo titolo).
                $tag_fam     = $this->resolve_font_family( (string) $s['tagline_font_family'], $legacy ) ?: $hfam;
                $tag_fw_css  = $tag_fw !== '' ? 'font-weight:' . $tag_fw . ';' : '';
                $cap_clr     = $this->safe_color_css( $s['tagline_caption_color'] ) ?: 'var(--olo-color-text-faint, #9ca3af)';
                $cap_size    = absint( $s['tagline_caption_size'] ) ?: 11;
                $cap_fam     = $this->resolve_font_family( (string) $s['tagline_caption_font_family'], $legacy ) ?: $mono;
                $cap_fw      = $this->font_weight_css( $s['tagline_caption_font_weight'] );
                $cap_fw_css  = $cap_fw !== '' ? 'font-weight:' . $cap_fw . ';' : '';
                $tag_italic  = ! empty( $s['tagline_text_italic'] ) ? 'font-style:italic;' : '';
            ?>
                <div class="olo-sechead__right" style="text-align:right">
                    <?php if ( ! empty( $s['tagline_text'] ) ) : ?>
                        <div class="olo-sechead__tag" style="font-family:<?php echo esc_attr( $tag_fam ); ?>;font-size:<?php echo (int) $tag_size; ?>px;<?php echo esc_attr( $tag_fw_css ); ?>color:<?php echo esc_attr( $tag_clr ); ?>;<?php echo esc_attr( $tag_italic ); ?>line-height:1.3;margin-bottom:10px" data-olo-editable="tagline_text"><?php echo esc_html( $s['tagline_text'] ); ?></div>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['tagline_caption'] ) ) : ?>
                        <div class="olo-sechead__cap" style="font-family:<?php echo esc_attr( $cap_fam ); ?>;font-size:<?php echo (int) $cap_size; ?>px;<?php echo esc_attr( $cap_fw_css ); ?>letter-spacing:0.1em;text-transform:uppercase;color:<?php echo esc_attr( $cap_clr ); ?>" data-olo-editable="tagline_caption"><?php echo esc_html( $s['tagline_caption'] ); ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <style>
            @media (max-width:960px) {
                .olo-sechead__headline { --olo-sh-fs-now: var(--olo-sh-fs-t, var(--olo-sh-fs)); }
            }
            @media (max-width:900px) {
                .olo-sechead { grid-template-columns: 1fr !important; gap: 24px !important; text-align: left !important; }
                .olo-sechead__right { text-align: left !important; }
                .olo-sechead__headline { --olo-sh-fs-now: var(--olo-sh-fs-t, clamp(min(40px, var(--olo-sh-fs)), 10vw, min(72px, var(--olo-sh-fs)))); }
            }
            @media (max-width:480px) {
                .olo-sechead__headline { --olo-sh-fs-now: var(--olo-sh-fs-m, var(--olo-sh-fs-t, clamp(min(40px, var(--olo-sh-fs)), 10vw, min(72px, var(--olo-sh-fs))))); }
            }
        </style>
        <?php

        return ob_get_clean();
    }
}
