<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Hero Split — layout editoriale bicolonna allineato agli standard Olobuild
 * (vedi docs/TILE-PERFETTA.md). Caratteristiche:
 *
 *   - headline serif multi-riga (ogni riga colorabile + italic)
 *   - eyebrow tag con dot
 *   - sottotitolo italic con max-width
 *   - 2 CTA pill con hover bilaterale
 *   - fascia stats configurabile
 *   - showcase 2x2 con sfondo creativo unificato per ogni card
 *     (solid/gradient/pattern/image/video/gallery via Olo_CSS_Builder)
 *   - 5 effetti hover card (radius-morph, lift, scale, tilt, none)
 *   - shadow preset per le card
 */
class Olo_HeroSplit_Tile extends Olo_Tile_Base {

    protected $type     = 'hero-split';
    protected $name     = 'Hero Split';
    protected $icon     = 'dashicons-columns';
    protected $category = 'layout';
    protected $defaults = [
        // Eyebrow
        'eyebrow_text'      => 'STACK WORDPRESS · PER AGENZIE E PMI',
        'eyebrow_dot_color' => '#10b981',
        'eyebrow_color'     => 'var(--olo-color-text, #1f2937)',

        // Headline
        'headline_lines' => [
            [ 'text' => 'Costruisci.', 'color' => '#0f172a', 'italic' => false ],
            [ 'text' => 'Traduci.',    'color' => '#b3261e', 'italic' => true  ],
            [ 'text' => 'Prenota.',    'color' => '#0f172a', 'italic' => false ],
        ],
        'headline_font_family' => 'serif',
        'headline_font_size'   => 96,
        'headline_line_height' => 1.0,
        'headline_font_weight' => '700',
        'headline_align'       => 'left',

        // Subhead
        'subhead'            => 'Un telaio, cinque prodotti, nessuna catena. Page builder gratis + prenotazioni + multilingua + virtual tour + e-learning, tutto in WordPress.',
        'subhead_color'      => 'var(--olo-color-text, #374151)',
        'subhead_size'       => 18,
        'subhead_italic'     => true,
        'subhead_max_width'  => 520,
        'subhead_align'      => 'left',

        // CTA primaria
        'cta1_text'        => 'Prenota demo →',
        'cta1_url'         => '#',
        'cta1_target'      => '_self',
        'cta1_bg'          => '#0f172a',
        'cta1_bg_hover'    => '',
        'cta1_color'       => '#ffffff',
        'cta1_color_hover' => '',
        'cta1_size'        => 14,
        'cta1_radius'              => [ 'tl' => 999, 'tr' => 999, 'br' => 999, 'bl' => 999, 'linked' => true ],
        'cta1_radius_hover'        => [ 'tl' => 999, 'tr' => 999, 'br' => 999, 'bl' => 999, 'linked' => true ],
        'cta1_radius_hover_duration' => 300,

        // CTA secondaria
        'cta2_text'        => 'Esplora i prodotti',
        'cta2_url'         => '#',
        'cta2_target'      => '_self',
        'cta2_bg'          => 'transparent',
        'cta2_bg_hover'    => '#0f172a',
        'cta2_color'       => '#0f172a',
        'cta2_color_hover' => '#ffffff',
        'cta2_border'      => '#0f172a',
        'cta2_size'        => 14,
        'cta2_radius'              => [ 'tl' => 999, 'tr' => 999, 'br' => 999, 'bl' => 999, 'linked' => true ],
        'cta2_radius_hover'        => [ 'tl' => 999, 'tr' => 999, 'br' => 999, 'bl' => 999, 'linked' => true ],
        'cta2_radius_hover_duration' => 300,

        // Stats
        'stats' => [
            [ 'value' => '5',      'value_color' => '#0f172a', 'label' => 'PRODOTTI MODULARI' ],
            [ 'value' => 'Gratis', 'value_color' => '#b3261e', 'label' => 'OLOBUILD, PER SEMPRE' ],
            [ 'value' => '0 %',    'value_color' => '#0f172a', 'label' => 'SAAS · LOCK-IN · COMMISSIONI' ],
        ],

        // Showcase
        'showcase_enabled'        => true,
        'showcase_bg'             => [ 'type' => 'solid', 'color' => '#f0e9dc' ],
        'showcase_padding'                => 28,
        'showcase_radius'                 => [ 'tl' => 24, 'tr' => 24, 'br' => 24, 'bl' => 24, 'linked' => true ],
        'showcase_radius_hover'           => [ 'tl' => 24, 'tr' => 24, 'br' => 24, 'bl' => 24, 'linked' => true ],
        'showcase_radius_hover_duration'  => 400,
        'showcase_badge_text'     => 'DEMO LIVE',
        'showcase_badge_dot'      => '#dc2626',
        'showcase_badge_bg'       => '#ffffff',
        'showcase_badge_color'    => '#0f172a',
        'showcase_items' => [
            [ 'number' => '01', 'text' => 'crea',     'italic' => true, 'text_color' => '#0f172a', 'bg' => [ 'type' => 'solid', 'color' => '#ffffff' ] ],
            [ 'number' => '02', 'text' => 'anima',    'italic' => true, 'text_color' => '#0f172a', 'bg' => [ 'type' => 'solid', 'color' => '#ffffff' ] ],
            [ 'number' => '03', 'text' => 'traduci',  'italic' => true, 'text_color' => '#0f172a', 'bg' => [ 'type' => 'solid', 'color' => '#ffffff' ] ],
            [ 'number' => '04', 'text' => 'pubblica', 'italic' => true, 'text_color' => '#0f172a', 'bg' => [ 'type' => 'solid', 'color' => '#ffffff' ] ],
        ],
        'showcase_card_radius'                 => [ 'tl' => 18, 'tr' => 18, 'br' => 18, 'bl' => 18, 'linked' => true ],
        'showcase_card_radius_hover'           => [ 'tl' => 18, 'tr' => 18, 'br' => 18, 'bl' => 18, 'linked' => true ],
        'showcase_card_radius_hover_duration'  => 400,
        'showcase_card_shadow'                 => 'sm',
        'showcase_caption_left'   => 'PASSA IL MOUSE SUI TILE',
        'showcase_caption_right'  => 'BORDER-RADIUS ANIMATO',
        'showcase_hover_effect'   => 'none',

        // Layout
        'split_ratio' => '1fr 1fr',
        'gap'         => 60,
        'min_height'  => 600,

        // Fallback interni (lo style.padding del wrapper esterno ha priorità
        // quando l'utente lo configura via la sezione Spaziatura base).
        'tile_padding' => [ 'top' => 80, 'right' => 80, 'bottom' => 60, 'left' => 80 ],
        'tile_margin'  => [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 ],
    ];

    public function get_controls() {
        return [];
    }

    /**
     * Confronta base e hover radius; ritorna il CSS hover solo se differisce,
     * altrimenti '' (così non emettiamo regole :hover ridondanti).
     */
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
        $uid = 'olo-hsplit-' . wp_rand( 10000, 99999 );

        // Font stack
        $serif_stack = "var(--olo-font-family-heading, 'Playfair Display','Cormorant Garamond',Georgia,'Times New Roman',serif)";
        $sans_stack  = "var(--olo-font-family, 'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
        $mono_stack  = "ui-monospace,'SF Mono',Menlo,Consolas,monospace";
        $family_map  = [ 'serif' => $serif_stack, 'sans-serif' => $sans_stack, 'mono' => $mono_stack ];
        $headline_family = $family_map[ $s['headline_font_family'] ] ?? $serif_stack;

        // Wrapper interno: padding fallback se non configurato dallo style.padding del wrapper esterno
        $wrap_style = '';
        if ( empty( $style['padding_top'] ) && empty( $style['padding_right'] ) && empty( $style['padding_bottom'] ) && empty( $style['padding_left'] ) ) {
            $p = $s['tile_padding'];
            $wrap_style .= 'padding:' . intval( $p['top'] ) . 'px ' . intval( $p['right'] ) . 'px ' . intval( $p['bottom'] ) . 'px ' . intval( $p['left'] ) . 'px;';
        }
        $min_h = absint( $s['min_height'] ?? 0 );
        if ( $min_h ) $wrap_style .= 'min-height:' . $min_h . 'px;';

        // Grid style
        $gap = absint( $s['gap'] ?? 60 );
        $allowed_ratios = [ '1fr 1fr', '1.2fr 1fr', '1fr 1.2fr', '1fr 0.8fr', '0.8fr 1fr' ];
        $split = in_array( $s['split_ratio'], $allowed_ratios, true ) ? $s['split_ratio'] : '1fr 1fr';
        $grid_style = 'display:grid;grid-template-columns:' . ( ! empty( $s['showcase_enabled'] ) ? $split : '1fr' ) . ';gap:' . $gap . 'px;align-items:center;';

        // Showcase bg (sfondo creativo unificato)
        $showcase_bg_css = '';
        $sc_bg_obj = $s['showcase_bg'] ?? [ 'type' => 'none' ];
        if ( is_array( $sc_bg_obj ) && ( $sc_bg_obj['type'] ?? 'none' ) !== 'none' && class_exists( 'Olo_CSS_Builder' ) ) {
            $cssb = new Olo_CSS_Builder();
            $showcase_bg_css = $cssb->get_bg_inline_css( $sc_bg_obj );
        } elseif ( ! is_array( $sc_bg_obj ) ) {
            // Backward-compat: legacy string color
            $sc_clr = $this->safe_color_css( $sc_bg_obj );
            if ( $sc_clr ) $showcase_bg_css = 'background:' . $sc_clr;
        }

        // Shadow map per card
        $shadow_map = [
            'none' => '',
            'sm'   => '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
            'md'   => '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
            'lg'   => '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
            'xl'   => '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
        ];
        $card_shadow = $shadow_map[ $s['showcase_card_shadow'] ?? 'sm' ] ?? '';

        // CTA / Showcase / Card: border-radius standard Olobuild (4 angoli + hover)
        $cta1_size      = max( 10, min( 30, absint( $s['cta1_size'] ?? 14 ) ) );
        $cta2_size      = max( 10, min( 30, absint( $s['cta2_size'] ?? 14 ) ) );
        $sc_padding     = max( 0, min( 80, absint( $s['showcase_padding'] ?? 28 ) ) );
        $cta1_radius    = $this->build_border_radius_css( $s['cta1_radius'] ?? [] );
        $cta2_radius    = $this->build_border_radius_css( $s['cta2_radius'] ?? [] );
        $sc_radius      = $this->build_border_radius_css( $s['showcase_radius'] ?? [] );
        $card_radius    = $this->build_border_radius_css( $s['showcase_card_radius'] ?? [] );
        // Hover radius (vuoto se uguale al base → niente CSS hover)
        $cta1_radius_h  = $this->_radius_hover_diff( $s['cta1_radius'] ?? [], $s['cta1_radius_hover'] ?? [] );
        $cta2_radius_h  = $this->_radius_hover_diff( $s['cta2_radius'] ?? [], $s['cta2_radius_hover'] ?? [] );
        $sc_radius_h    = $this->_radius_hover_diff( $s['showcase_radius'] ?? [], $s['showcase_radius_hover'] ?? [] );
        $card_radius_h  = $this->_radius_hover_diff( $s['showcase_card_radius'] ?? [], $s['showcase_card_radius_hover'] ?? [] );
        $cta1_rdur      = max( 50, intval( $s['cta1_radius_hover_duration'] ?? 300 ) );
        $cta2_rdur      = max( 50, intval( $s['cta2_radius_hover_duration'] ?? 300 ) );
        $sc_rdur        = max( 50, intval( $s['showcase_radius_hover_duration'] ?? 400 ) );
        $card_rdur      = max( 50, intval( $s['showcase_card_radius_hover_duration'] ?? 400 ) );

        $hover_effect = in_array( $s['showcase_hover_effect'] ?? 'none', [ 'none', 'lift', 'scale', 'tilt' ], true ) ? $s['showcase_hover_effect'] : 'none';

        ob_start();
        ?>
        <div class="olo-hsplit <?php echo esc_attr( $uid ); ?> olo-hsplit-hover-<?php echo esc_attr( $hover_effect ); ?>" style="<?php echo esc_attr( $wrap_style ); ?>">
            <div class="olo-hsplit__grid" style="<?php echo esc_attr( $grid_style ); ?>">

                <!-- LEFT COLUMN -->
                <div class="olo-hsplit__left">
                    <?php if ( ! empty( $s['eyebrow_text'] ) ) : ?>
                        <div class="olo-hsplit__eyebrow" style="display:inline-flex;align-items:center;gap:10px;font-family:<?php echo esc_attr( $mono_stack ); ?>;font-size:12px;letter-spacing:0.08em;text-transform:uppercase;color:<?php echo esc_attr( $this->safe_color_css( $s['eyebrow_color'] ) ?: 'var(--olo-color-text, #1f2937)' ); ?>;margin-bottom:32px">
                            <span style="width:10px;height:10px;border-radius:50%;background:<?php echo esc_attr( $this->safe_color_css( $s['eyebrow_dot_color'] ) ?: '#10b981' ); ?>"></span>
                            <span data-olo-editable="eyebrow_text"><?php echo esc_html( $s['eyebrow_text'] ); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php
                    $headlines = is_array( $s['headline_lines'] ) ? $s['headline_lines'] : [];
                    if ( $headlines ) :
                        $hsize   = absint( $s['headline_font_size'] ) ?: 96;
                        $hlh     = floatval( $s['headline_line_height'] ) ?: 1.0;
                        $hweight = preg_match( '/^\d+$/', (string) $s['headline_font_weight'] ) ? $s['headline_font_weight'] : '700';
                        $halign  = in_array( $s['headline_align'] ?? 'left', [ 'left', 'center', 'right', 'justify' ], true ) ? $s['headline_align'] : 'left';
                    ?>
                        <h1 class="olo-hsplit__headline" style="font-family:<?php echo esc_attr( $headline_family ); ?>;font-size:<?php echo $hsize; ?>px;line-height:<?php echo $hlh; ?>;font-weight:<?php echo esc_attr( $hweight ); ?>;letter-spacing:-0.02em;text-align:<?php echo esc_attr( $halign ); ?>;margin:0 0 28px">
                            <?php foreach ( $headlines as $idx => $line ) :
                                $line_text = $line['text'] ?? '';
                                if ( $line_text === '' ) continue;
                                $color  = $this->safe_color_css( $line['color'] ?? '' ) ?: '#0f172a';
                                $italic = ! empty( $line['italic'] ) ? 'font-style:italic;' : '';
                            ?>
                                <span style="display:block;color:<?php echo esc_attr( $color ); ?>;<?php echo $italic; ?>" data-olo-editable="<?php echo 'headline_lines.' . intval( $idx ) . '.text'; ?>"><?php echo esc_html( $line_text ); ?></span>
                            <?php endforeach; ?>
                        </h1>
                    <?php endif; ?>

                    <?php if ( ! empty( $s['subhead'] ) ) :
                        $sub_color  = $this->safe_color_css( $s['subhead_color'] ) ?: 'var(--olo-color-text, #374151)';
                        $sub_size   = absint( $s['subhead_size'] ) ?: 18;
                        $sub_italic = ! empty( $s['subhead_italic'] ) ? 'font-style:italic;' : '';
                        $sub_mw     = absint( $s['subhead_max_width'] ?? 0 );
                        $sub_mw_css = $sub_mw ? 'max-width:' . $sub_mw . 'px;' : '';
                        $sub_align  = in_array( $s['subhead_align'] ?? 'left', [ 'left', 'center', 'right', 'justify' ], true ) ? $s['subhead_align'] : 'left';
                    ?>
                        <?php
                        $subhead_raw  = $s['subhead'] ?? '';
                        $subhead_html = preg_match( '/<[a-z!\/][^>]*>/i', $subhead_raw ) ? $this->safe_richtext_content( $subhead_raw ) : nl2br( esc_html( $subhead_raw ) );
                        ?>
                        <p class="olo-hsplit__subhead" style="font-family:<?php echo esc_attr( $headline_family ); ?>;font-size:<?php echo $sub_size; ?>px;line-height:1.5;color:<?php echo esc_attr( $sub_color ); ?>;text-align:<?php echo esc_attr( $sub_align ); ?>;<?php echo $sub_italic . $sub_mw_css; ?>margin:0 0 40px" data-olo-editable="subhead" data-olo-richtext><?php echo $subhead_html; ?></p>
                    <?php endif; ?>

                    <?php if ( ! empty( $s['cta1_text'] ) || ! empty( $s['cta2_text'] ) ) : ?>
                        <div class="olo-hsplit__ctas" style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:60px">
                            <?php if ( ! empty( $s['cta1_text'] ) ) :
                                $c1_bg  = $this->safe_color_css( $s['cta1_bg'] ) ?: '#0f172a';
                                $c1_clr = $this->safe_color_css( $s['cta1_color'] ) ?: '#ffffff';
                                $c1_tgt = $s['cta1_target'] === '_blank' ? ' target="_blank" rel="noopener"' : '';
                            ?>
                                <a href="<?php echo esc_url( $s['cta1_url'] ?: '#' ); ?>"<?php echo $c1_tgt; ?> class="olo-hsplit__cta olo-hsplit__cta--primary" data-olo-editable="cta1_text" style="display:inline-flex;align-items:center;justify-content:center;padding:14px 28px;background:<?php echo esc_attr( $c1_bg ); ?>;color:<?php echo esc_attr( $c1_clr ); ?>;<?php if ( $cta1_radius ) echo 'border-radius:' . esc_attr( $cta1_radius ) . ';'; ?>font-family:<?php echo esc_attr( $sans_stack ); ?>;font-size:<?php echo $cta1_size; ?>px;font-weight:500;text-decoration:none;border:1px solid <?php echo esc_attr( $c1_bg ); ?>;transition:transform .2s ease,box-shadow .2s ease,background .2s,color .2s,border-color .2s<?php if ( $cta1_radius_h ) echo ',border-radius ' . $cta1_rdur . 'ms ease'; ?>"><?php echo esc_html( $s['cta1_text'] ); ?></a>
                            <?php endif; ?>
                            <?php if ( ! empty( $s['cta2_text'] ) ) :
                                $c2_bg    = $s['cta2_bg'] === 'transparent' ? 'transparent' : ( $this->safe_color_css( $s['cta2_bg'] ) ?: 'transparent' );
                                $c2_clr   = $this->safe_color_css( $s['cta2_color'] ) ?: '#0f172a';
                                $c2_bord  = $this->safe_color_css( $s['cta2_border'] ) ?: '#0f172a';
                                $c2_tgt   = $s['cta2_target'] === '_blank' ? ' target="_blank" rel="noopener"' : '';
                            ?>
                                <a href="<?php echo esc_url( $s['cta2_url'] ?: '#' ); ?>"<?php echo $c2_tgt; ?> class="olo-hsplit__cta olo-hsplit__cta--outline" data-olo-editable="cta2_text" style="display:inline-flex;align-items:center;justify-content:center;padding:14px 28px;background:<?php echo esc_attr( $c2_bg ); ?>;color:<?php echo esc_attr( $c2_clr ); ?>;border:1px solid <?php echo esc_attr( $c2_bord ); ?>;<?php if ( $cta2_radius ) echo 'border-radius:' . esc_attr( $cta2_radius ) . ';'; ?>font-family:<?php echo esc_attr( $sans_stack ); ?>;font-size:<?php echo $cta2_size; ?>px;font-weight:500;text-decoration:none;transition:transform .2s ease,box-shadow .2s ease,background .2s,color .2s,border-color .2s<?php if ( $cta2_radius_h ) echo ',border-radius ' . $cta2_rdur . 'ms ease'; ?>"><?php echo esc_html( $s['cta2_text'] ); ?></a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php
                    $stats = is_array( $s['stats'] ) ? array_slice( $s['stats'], 0, 4 ) : [];
                    if ( $stats ) :
                    ?>
                        <div class="olo-hsplit__stats" style="display:grid;grid-template-columns:repeat(<?php echo count( $stats ); ?>,1fr);gap:32px;padding-top:32px;border-top:1px solid rgba(15,23,42,0.12)">
                            <?php foreach ( $stats as $sidx => $st ) :
                                $val       = $st['value'] ?? '';
                                $val_color = $this->safe_color_css( $st['value_color'] ?? '' ) ?: '#0f172a';
                                $lbl       = $st['label'] ?? '';
                            ?>
                                <div class="olo-hsplit__stat">
                                    <div style="font-family:<?php echo esc_attr( $headline_family ); ?>;font-size:36px;line-height:1;font-weight:600;color:<?php echo esc_attr( $val_color ); ?>;margin-bottom:10px;<?php echo $val === 'Gratis' ? 'font-style:italic;' : ''; ?>" data-olo-editable="<?php echo 'stats.' . intval( $sidx ) . '.value'; ?>"><?php echo esc_html( $val ); ?></div>
                                    <div style="font-family:<?php echo esc_attr( $mono_stack ); ?>;font-size:11px;letter-spacing:0.08em;text-transform:uppercase;color:var(--olo-color-text-soft, #6b7280);line-height:1.4" data-olo-editable="<?php echo 'stats.' . intval( $sidx ) . '.label'; ?>"><?php echo esc_html( $lbl ); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- RIGHT COLUMN: Showcase -->
                <?php if ( ! empty( $s['showcase_enabled'] ) ) :
                    $sc_badge_dot = $this->safe_color_css( $s['showcase_badge_dot'] ) ?: '#dc2626';
                    $sc_badge_bg  = $this->safe_color_css( $s['showcase_badge_bg'] ) ?: '#ffffff';
                    $sc_badge_col = $this->safe_color_css( $s['showcase_badge_color'] ?? '' ) ?: '#0f172a';
                    $items        = is_array( $s['showcase_items'] ) ? array_slice( $s['showcase_items'], 0, 4 ) : [];
                ?>
                    <div class="olo-hsplit__right" style="<?php echo esc_attr( $showcase_bg_css ); ?>;<?php if ( $sc_radius ) echo 'border-radius:' . esc_attr( $sc_radius ) . ';'; ?>padding:<?php echo $sc_padding; ?>px;position:relative;min-height:480px;display:flex;flex-direction:column<?php if ( $sc_radius_h ) echo ';transition:border-radius ' . $sc_rdur . 'ms ease'; ?>">
                        <?php if ( ! empty( $s['showcase_badge_text'] ) ) : ?>
                            <div class="olo-hsplit__badge" style="display:inline-flex;align-items:center;gap:8px;background:<?php echo esc_attr( $sc_badge_bg ); ?>;padding:6px 14px;border-radius:999px;font-family:<?php echo esc_attr( $mono_stack ); ?>;font-size:11px;letter-spacing:0.08em;text-transform:uppercase;color:<?php echo esc_attr( $sc_badge_col ); ?>;align-self:flex-start;box-shadow:0 1px 3px rgba(0,0,0,0.06)">
                                <span style="width:8px;height:8px;border-radius:50%;background:<?php echo esc_attr( $sc_badge_dot ); ?>"></span>
                                <span data-olo-editable="showcase_badge_text"><?php echo esc_html( $s['showcase_badge_text'] ); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if ( $items ) : ?>
                            <div class="olo-hsplit__cards" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin:24px 0 18px;flex:1">
                                <?php foreach ( $items as $idx => $it ) :
                                    $num     = $it['number'] ?? '';
                                    $txt     = $it['text'] ?? '';
                                    $txt_clr = $this->safe_color_css( $it['text_color'] ?? '' ) ?: '#0f172a';
                                    $italic  = ! empty( $it['italic'] ) ? 'font-style:italic;' : '';

                                    // Card bg via Olo_CSS_Builder (background creativo unificato)
                                    $card_bg_css = '';
                                    $card_bg_obj = $it['bg'] ?? [ 'type' => 'solid', 'color' => '#ffffff' ];
                                    if ( is_array( $card_bg_obj ) && ( $card_bg_obj['type'] ?? 'none' ) !== 'none' && class_exists( 'Olo_CSS_Builder' ) ) {
                                        $cssb = new Olo_CSS_Builder();
                                        $card_bg_css = $cssb->get_bg_inline_css( $card_bg_obj );
                                    }
                                    $shadow_css = $card_shadow ? 'box-shadow:' . $card_shadow . ';' : '';
                                ?>
                                    <div class="olo-hsplit__card olo-hsplit__card--<?php echo $idx; ?>" style="<?php echo esc_attr( $card_bg_css ); ?>;<?php if ( $card_radius ) echo 'border-radius:' . esc_attr( $card_radius ) . ';'; ?>padding:24px;display:flex;flex-direction:column;justify-content:space-between;min-height:180px;<?php echo $shadow_css; ?>transition:border-radius <?php echo $card_rdur; ?>ms cubic-bezier(.4,0,.2,1),transform .3s ease,box-shadow .3s ease">
                                        <div style="font-family:<?php echo esc_attr( $mono_stack ); ?>;font-size:11px;color:var(--olo-color-text-faint, #9ca3af);letter-spacing:0.05em" data-olo-editable="<?php echo 'showcase_items.' . intval( $idx ) . '.number'; ?>"><?php echo esc_html( $num ); ?></div>
                                        <div style="font-family:<?php echo esc_attr( $headline_family ); ?>;font-size:36px;font-weight:500;color:<?php echo esc_attr( $txt_clr ); ?>;text-align:center;<?php echo $italic; ?>" data-olo-editable="<?php echo 'showcase_items.' . intval( $idx ) . '.text'; ?>"><?php echo esc_html( $txt ); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ( ! empty( $s['showcase_caption_left'] ) || ! empty( $s['showcase_caption_right'] ) ) : ?>
                            <div class="olo-hsplit__captions" style="display:flex;justify-content:space-between;align-items:center;margin-top:auto;font-family:<?php echo esc_attr( $mono_stack ); ?>;font-size:10px;letter-spacing:0.08em;text-transform:uppercase;color:var(--olo-color-text-faint, #9ca3af)">
                                <span data-olo-editable="showcase_caption_left"><?php echo esc_html( $s['showcase_caption_left'] ); ?></span>
                                <span data-olo-editable="showcase_caption_right"><?php echo esc_html( $s['showcase_caption_right'] ); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <style>
            <?php
            // CTA hover (bilaterale withHover)
            $c1_bg_hover  = $this->safe_color_css( $s['cta1_bg_hover'] ?? '' );
            $c1_clr_hover = $this->safe_color_css( $s['cta1_color_hover'] ?? '' );
            $c2_bg_hover  = $this->safe_color_css( $s['cta2_bg_hover'] ?? '' );
            $c2_clr_hover = $this->safe_color_css( $s['cta2_color_hover'] ?? '' );
            ?>
            .<?php echo $uid; ?> .olo-hsplit__cta--primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(15,23,42,0.18); <?php if ( $c1_bg_hover ) echo 'background:' . $c1_bg_hover . ' !important;border-color:' . $c1_bg_hover . ' !important;'; ?> <?php if ( $c1_clr_hover ) echo 'color:' . $c1_clr_hover . ' !important;'; ?> <?php if ( $cta1_radius_h ) echo 'border-radius:' . $cta1_radius_h . ' !important;'; ?> }
            .<?php echo $uid; ?> .olo-hsplit__cta--outline:hover { <?php echo $c2_bg_hover ? 'background:' . $c2_bg_hover . ' !important;' : 'background:#0f172a !important;'; ?> <?php echo $c2_clr_hover ? 'color:' . $c2_clr_hover . ' !important;' : 'color:#fff !important;'; ?> <?php if ( $cta2_radius_h ) echo 'border-radius:' . $cta2_radius_h . ' !important;'; ?> }
            <?php if ( $sc_radius_h ) : ?>.<?php echo $uid; ?> .olo-hsplit__right:hover { border-radius: <?php echo $sc_radius_h; ?> !important; }<?php endif; ?>
            <?php if ( $card_radius_h ) : ?>.<?php echo $uid; ?> .olo-hsplit__card:hover { border-radius: <?php echo $card_radius_h; ?> !important; }<?php endif; ?>

            <?php
            // Card hover effects — il border-radius hover è già gestito dal sistema standard
            // withHover (vedi sopra). Qui restano solo gli effetti che agiscono su transform/shadow.
            switch ( $hover_effect ) :
                case 'lift' : ?>
                    .<?php echo $uid; ?> .olo-hsplit__card:hover { transform: translateY(-8px); box-shadow: 0 18px 40px rgba(0,0,0,0.18); }
                    <?php break;
                case 'scale' : ?>
                    .<?php echo $uid; ?> .olo-hsplit__card:hover { transform: scale(1.04); z-index: 2; }
                    <?php break;
                case 'tilt' : ?>
                    .<?php echo $uid; ?> .olo-hsplit__card { transform-style: preserve-3d; }
                    .<?php echo $uid; ?> .olo-hsplit__card:hover { transform: perspective(800px) rotateX(4deg) rotateY(-4deg) scale(1.02); }
                    <?php break;
            endswitch; ?>

            @media (max-width: 900px) {
                .<?php echo $uid; ?> .olo-hsplit__grid { grid-template-columns: 1fr !important; gap: 40px !important; }
                .<?php echo $uid; ?> .olo-hsplit__headline { font-size: clamp(48px, 12vw, 80px) !important; }
            }
        </style>
        <?php

        return ob_get_clean();
    }
}
