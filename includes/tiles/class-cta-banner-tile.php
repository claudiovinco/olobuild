<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * CTA Banner — banner CTA editoriale a 3 colonne.
 * Standard Olobuild: i18n, separazione contenuto/stile, withHover su CTA,
 * border-radius 4 angoli + hover, sfondo creativo unificato, inline editing.
 */
class Olobuild_CtaBanner_Tile extends Olobuild_Tile_Base {

    protected $type     = 'cta-banner';
    protected $name     = 'CTA Banner';
    protected $icon     = 'dashicons-megaphone';
    protected $category = 'layout';
    protected $defaults = [
        'headline'                => 'Il tuo primo sito OLObuild è online',
        'headline_accent'         => 'oggi pomeriggio.',
        'headline_accent_italic'  => true,
        'subtitle'                => 'Trial gratuita, niente carta. Tre passi, una sigaretta a testa di pausa.',
        'cta_text'                => 'Inizia ora →',
        'cta_url'                 => '#',
        'cta_target'              => '_self',

        'cta2_text'               => '',
        'cta2_url'                => '#',
        'cta2_target'             => '_self',
        'cta2_bg'                 => 'transparent',
        'cta2_color'              => '#ffffff',
        'cta2_border'             => 'rgba(255,255,255,.28)',

        'bg'                      => [ 'type' => 'solid', 'color' => '#0f172a' ],
        'text_color'              => '#ffffff',
        'accent_color'            => 'var(--olo-color-primary, #e1474f)',
        'subtitle_color'          => '#9ca3af',

        'cta_bg'                  => 'var(--olo-color-primary, #e1474f)',
        'cta_bg_hover'            => '',
        'cta_color'               => '#ffffff',
        'cta_color_hover'         => '#ffffff',
        'cta_radius'              => [ 'tl' => 999, 'tr' => 999, 'br' => 999, 'bl' => 999, 'linked' => true ],
        'cta_radius_hover'        => [ 'tl' => 999, 'tr' => 999, 'br' => 999, 'bl' => 999, 'linked' => true ],
        'cta_radius_hover_duration' => 300,
        'cta_size'                => 15,
        'cta_padding_y'           => 18,
        'cta_padding_x'           => 32,

        'headline_font_family'    => 'serif',
        'headline_size'           => 36,
        'headline_weight'         => '400',
        'subtitle_size'           => 14,

        'layout'                       => 'split-3',
        'ratio'                        => '1.4fr 1fr auto',
        'gap'                          => 40,
        'vertical_align'               => 'center',
        'banner_radius'                => [ 'tl' => 20, 'tr' => 20, 'br' => 20, 'bl' => 20, 'linked' => true ],
        'banner_radius_hover'          => [ 'tl' => 20, 'tr' => 20, 'br' => 20, 'bl' => 20, 'linked' => true ],
        'banner_radius_hover_duration' => 400,
        'banner_padding'               => 40,
    ];

    public function get_controls() { return []; }

    private function _radius_hover_diff( $base, $hover ) {
        if ( ! is_array( $base ) || ! is_array( $hover ) ) return '';
        foreach ( [ 'tl', 'tr', 'br', 'bl' ] as $c ) {
            if ( intval( $base[ $c ] ?? 0 ) !== intval( $hover[ $c ] ?? 0 ) ) {
                return $this->build_border_radius_css( $hover );
            }
        }
        return '';
    }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-ctab-' . wp_rand( 10000, 99999 );

        $serif = "var(--olo-font-family-heading, 'Playfair Display','Cormorant Garamond',Georgia,'Times New Roman',serif)";
        $sans  = "var(--olo-font-family, 'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
        $legacy = [ 'serif' => $serif, 'sans-serif' => $sans, 'mono' => "ui-monospace,'SF Mono',Menlo,Consolas,monospace" ];

        $h_family = $this->resolve_font_family( $s['headline_font_family'] ?? '', $legacy ) ?: $serif;
        $h_size   = max( 18, min( 80, absint( $s['headline_size'] ) ) );
        $h_weight = preg_match( '/^\d+$/', (string) $s['headline_weight'] ) ? $s['headline_weight'] : '400';
        $text_c   = $this->safe_color_css( $s['text_color'] ) ?: '#ffffff';
        $accent_c = $this->safe_color_css( $s['accent_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $sub_size = max( 11, min( 22, absint( $s['subtitle_size'] ) ) );
        $sub_c    = $this->safe_color_css( $s['subtitle_color'] ) ?: '#9ca3af';

        $cta_bg   = $this->safe_color_css( $s['cta_bg'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $cta_c    = $this->safe_color_css( $s['cta_color'] ) ?: '#ffffff';
        $cta_size = max( 12, min( 22, absint( $s['cta_size'] ) ) );
        $cta_py   = max( 10, min( 30, absint( $s['cta_padding_y'] ) ) );
        $cta_px   = max( 16, min( 60, absint( $s['cta_padding_x'] ) ) );
        $cta_tgt  = $s['cta_target'] === '_blank' ? ' target="_blank" rel="noopener"' : '';

        $cta2_bg   = $this->safe_color_css( $s['cta2_bg'] ?? '' ) ?: 'transparent';
        $cta2_c    = $this->safe_color_css( $s['cta2_color'] ?? '' ) ?: '#ffffff';
        $cta2_bord = $this->safe_color_css( $s['cta2_border'] ?? '' );
        $cta2_tgt  = ( ( $s['cta2_target'] ?? '' ) === '_blank' ) ? ' target="_blank" rel="noopener"' : '';

        $cta_radius   = $this->build_border_radius_css( $s['cta_radius'] ?? [] );
        $cta_radius_h = $this->_radius_hover_diff( $s['cta_radius'] ?? [], $s['cta_radius_hover'] ?? [] );
        $cta_rdur     = max( 50, intval( $s['cta_radius_hover_duration'] ?? 300 ) );

        $banner_radius   = $this->build_border_radius_css( $s['banner_radius'] ?? [] );
        $banner_radius_h = $this->_radius_hover_diff( $s['banner_radius'] ?? [], $s['banner_radius_hover'] ?? [] );
        $banner_rdur     = max( 50, intval( $s['banner_radius_hover_duration'] ?? 400 ) );

        $bg_css = '';
        $bg = $s['bg'] ?? [ 'type' => 'solid', 'color' => '#0f172a' ];
        if ( is_array( $bg ) && ( $bg['type'] ?? 'none' ) !== 'none' && class_exists( 'Olobuild_CSS_Builder' ) ) {
            $cssb = new Olobuild_CSS_Builder();
            $bg_css = $cssb->get_bg_inline_css( $bg );
        }
        if ( ! $bg_css ) $bg_css = 'background:#0f172a';

        $banner_padding = max( 16, min( 120, absint( $s['banner_padding'] ) ) );
        $layout         = in_array( $s['layout'] ?? 'split-3', [ 'split-3', 'split-2', 'stack' ], true ) ? ( $s['layout'] ?? 'split-3' ) : 'split-3';
        $valign         = in_array( $s['vertical_align'] ?? 'center', [ 'start', 'center', 'end' ], true ) ? ( $s['vertical_align'] ?? 'center' ) : 'center';
        $gap            = max( 0, min( 120, absint( $s['gap'] ) ) );
        $allowed_ratio  = [ '1.4fr 1fr auto', '1fr 1fr auto', '2fr 1fr auto', '1fr auto' ];
        $ratio          = in_array( $s['ratio'], $allowed_ratio, true ) ? $s['ratio'] : '1.4fr 1fr auto';

        if ( $layout === 'split-3' )      $grid_cols = $ratio;
        elseif ( $layout === 'split-2' )  $grid_cols = '1fr auto';
        else                              $grid_cols = '1fr';

        $sub_raw  = $s['subtitle'] ?? '';
        $sub_html = preg_match( '/<[a-z!\/][^>]*>/i', $sub_raw ) ? $this->safe_richtext_content( $sub_raw ) : nl2br( esc_html( $sub_raw ) );

        $h_accent_italic = ! empty( $s['headline_accent_italic'] );

        ob_start();
        ?>
        <div class="olo-ctab <?php echo esc_attr( $uid ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- unescaped fragments on this line are fixed-literal ternaries plus the absint()/intval() clamped padding, gap and duration; everything else is esc_attr()'d ?>" style="<?php echo esc_attr( $bg_css ); ?>;<?php if ( $banner_radius ) echo 'border-radius:' . esc_attr( $banner_radius ) . ';'; ?>padding:<?php echo (int) $banner_padding; ?>px;color:<?php echo esc_attr( $text_c ); ?>;display:grid;grid-template-columns:<?php echo esc_attr( $grid_cols ); ?>;gap:<?php echo (int) $gap; ?>px;align-items:<?php echo esc_attr( $valign === 'start' ? 'flex-start' : ( $valign === 'end' ? 'flex-end' : 'center' ) ); ?>;<?php echo ( $layout === 'stack' ) ? 'text-align:center;justify-items:center;' : ''; ?>transition:<?php echo $banner_radius_h ? 'border-radius ' . (int) $banner_rdur . 'ms ease' : 'none'; ?>">

            <!-- Headline -->
            <?php if ( ! empty( $s['headline'] ) || ! empty( $s['headline_accent'] ) ) : ?>
                <h2 style="font-family:<?php echo esc_attr( $h_family ); ?>;font-size:<?php echo (int) $h_size; ?>px;font-weight:<?php echo esc_attr( $h_weight ); ?>;line-height:1.15;letter-spacing:-0.01em;color:<?php echo esc_attr( $text_c ); ?>;margin:0">
                    <?php if ( ! empty( $s['headline'] ) ) : ?><span data-olo-editable="headline"><?php echo esc_html( $s['headline'] ); ?></span><?php endif; ?><?php if ( ! empty( $s['headline_accent'] ) ) : ?> <span style="color:<?php echo esc_attr( $accent_c ); ?>;<?php if ( $h_accent_italic ) echo 'font-style:italic;'; ?>" data-olo-editable="headline_accent"><?php echo esc_html( $s['headline_accent'] ); ?></span><?php endif; ?>
                </h2>
            <?php endif; ?>

            <!-- Subtitle (solo se split-3 o stack) -->
            <?php if ( $layout !== 'split-2' && ! empty( $sub_html ) ) : ?>
                <div style="font-family:<?php echo esc_attr( $sans ); ?>;font-size:<?php echo (int) $sub_size; ?>px;line-height:1.5;color:<?php echo esc_attr( $sub_c ); ?>" data-olo-editable="subtitle" data-olo-richtext><?php echo $sub_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitized above via safe_richtext_content() (wp_kses_post) for rich text, or esc_html()+nl2br() for plain text ?></div>
            <?php elseif ( $layout === 'split-2' && ! empty( $sub_html ) ) : ?>
                <!-- In split-2 il subtitle va dentro alla cella headline -->
                <div style="font-family:<?php echo esc_attr( $sans ); ?>;font-size:<?php echo (int) $sub_size; ?>px;line-height:1.5;color:<?php echo esc_attr( $sub_c ); ?>;grid-column:1;margin-top:8px" data-olo-editable="subtitle" data-olo-richtext><?php echo $sub_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitized above via safe_richtext_content() (wp_kses_post) for rich text, or esc_html()+nl2br() for plain text ?></div>
            <?php endif; ?>

            <!-- CTA(s) -->
            <?php if ( ! empty( $s['cta_text'] ) || ! empty( $s['cta2_text'] ) ) : ?>
                <div class="olo-ctab__ctas" style="display:inline-flex;gap:12px;flex-wrap:wrap;align-items:center;<?php echo ( $layout === 'stack' ) ? 'justify-content:center;' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- both ternary branches are fixed literals ?>">
                    <?php if ( ! empty( $s['cta_text'] ) ) : ?>
                        <a href="<?php echo esc_url( $s['cta_url'] ?: '#' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- unescaped fragments on this line are the fixed-literal $cta_tgt (' target="_blank" rel="noopener"' or '') and absint()/intval() clamped paddings/size/duration; everything else is esc_url()/esc_attr()/esc_html()'d ?>"<?php echo $cta_tgt; ?> class="olo-ctab__cta" data-olo-editable="cta_text" style="display:inline-flex;align-items:center;justify-content:center;padding:<?php echo (int) $cta_py; ?>px <?php echo (int) $cta_px; ?>px;background:<?php echo esc_attr( $cta_bg ); ?>;color:<?php echo esc_attr( $cta_c ); ?>;<?php if ( $cta_radius ) echo 'border-radius:' . esc_attr( $cta_radius ) . ';'; ?>font-family:<?php echo esc_attr( $sans ); ?>;font-size:<?php echo (int) $cta_size; ?>px;font-weight:600;text-decoration:none;white-space:nowrap;transition:transform .2s ease,background .2s,color .2s<?php if ( $cta_radius_h ) echo ',border-radius ' . (int) $cta_rdur . 'ms ease'; ?>"><?php echo esc_html( $s['cta_text'] ); ?></a>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['cta2_text'] ) ) : ?>
                        <a href="<?php echo esc_url( $s['cta2_url'] ?: '#' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- unescaped fragments on this line are the fixed-literal $cta2_tgt (' target="_blank" rel="noopener"' or '') and absint() clamped paddings/size; everything else is esc_url()/esc_attr()/esc_html()'d ?>"<?php echo $cta2_tgt; ?> class="olo-ctab__cta2" data-olo-editable="cta2_text" style="display:inline-flex;align-items:center;justify-content:center;padding:<?php echo (int) $cta_py; ?>px <?php echo (int) $cta_px; ?>px;background:<?php echo esc_attr( $cta2_bg ); ?>;color:<?php echo esc_attr( $cta2_c ); ?>;<?php if ( $cta2_bord ) echo 'border:1px solid ' . esc_attr( $cta2_bord ) . ';'; ?><?php if ( $cta_radius ) echo 'border-radius:' . esc_attr( $cta_radius ) . ';'; ?>font-family:<?php echo esc_attr( $sans ); ?>;font-size:<?php echo (int) $cta_size; ?>px;font-weight:600;text-decoration:none;white-space:nowrap;transition:transform .2s ease,background .2s,color .2s,border-color .2s"><?php echo esc_html( $s['cta2_text'] ); ?></a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from the internally generated $uid, safe_color_css()-validated hover colours and build_border_radius_css() (intval-based) radii. ?>
        <style>
            <?php
            $cta_bg_h  = $this->safe_color_css( $s['cta_bg_hover'] ?? '' );
            $cta_clr_h = $this->safe_color_css( $s['cta_color_hover'] ?? '' );
            ?>
            .<?php echo $uid; ?> .olo-ctab__cta:hover { transform: translateY(-1px); <?php if ( $cta_bg_h ) echo 'background:' . $cta_bg_h . ' !important;'; ?> <?php if ( $cta_clr_h ) echo 'color:' . $cta_clr_h . ' !important;'; ?> <?php if ( $cta_radius_h ) echo 'border-radius:' . $cta_radius_h . ' !important;'; ?> }
            .<?php echo $uid; ?> .olo-ctab__cta:focus-visible { outline: none; box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent); }
            .<?php echo $uid; ?> .olo-ctab__cta2:hover { transform: translateY(-1px); <?php if ( $cta2_bord ) echo 'border-color:' . $cta2_c . ' !important;'; ?> }
            .<?php echo $uid; ?> .olo-ctab__cta2:focus-visible { outline: none; box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent); }
            <?php if ( $banner_radius_h ) : ?>
            .<?php echo $uid; ?>:hover { border-radius: <?php echo $banner_radius_h; ?> !important; }
            <?php endif; ?>
            @media (max-width: 800px) {
                .<?php echo $uid; ?> { grid-template-columns: 1fr !important; text-align: center; justify-items: center; gap: 20px !important; }
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php

        return ob_get_clean();
    }
}
