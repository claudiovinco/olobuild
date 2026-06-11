<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Hero_Tile extends Olo_Tile_Base {

    protected $type     = 'hero';
    protected $name     = 'Hero';
    protected $icon     = 'dashicons-cover-image';
    protected $category = 'layout';
    protected $defaults = [
        // Sfondo: gestito dal wrapper esterno via tab Stile → Sfondo (style.bg).
        // Bordo, ombra, larghezza piena: gestiti anch'essi dal tab Stile.
        // La hero gestisce internamente solo testo, layout interno, CTA.

        'typography_preset' => '',
        'preset'            => 'custom',

        // Contenuto
        'title'      => 'Benvenuto nel nostro sito',
        'subtitle'   => 'Scopri qualcosa di straordinario',
        'text_color' => '',

        // Titolo tipografia
        'title_tag'            => 'h1',
        'title_font_family'    => '',
        'title_font_size'      => '',
        'title_font_weight'    => '700',
        'title_letter_spacing' => '0',
        'title_line_height'    => '1.2',
        'title_text_transform' => 'none',
        'title_color'          => '',
        'title_text_shadow'    => '',

        // Sottotitolo tipografia
        'subtitle_font_size'      => '',
        'subtitle_font_weight'    => '400',
        'subtitle_letter_spacing' => '0',
        'subtitle_color'          => '',
        'subtitle_max_width'      => '',

        // Layout
        'min_height'        => '500px',
        'content_max_width' => '700',
        'vertical_align'    => 'center',
        'horizontal_align'  => 'center',
        'text_align'        => 'center',
        'tile_padding'      => [ 'top' => 60, 'right' => 20, 'bottom' => 60, 'left' => 20 ],

        // CTA Primario
        'cta_text'       => 'Inizia ora',
        'cta_url'        => '#',
        'cta_target'     => '_self',
        'cta_bg_color'   => '',
        'cta_text_color' => '',
        'cta_radius'     => [ 'tl' => 6, 'tr' => 6, 'br' => 6, 'bl' => 6 ],
        'cta_size'       => '15',
        'cta_style'      => 'filled',

        // CTA Secondario
        'cta2_text'       => '',
        'cta2_url'        => '#',
        'cta2_target'     => '_self',
        'cta2_bg_color'   => '',
        'cta2_text_color' => '',
        'cta2_style'      => 'outline',
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-hero-' . wp_rand( 10000, 99999 );

        $fg = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-on-primary, #FFFFFF)';

        // Sanitize rich text — permetti inline tag sicuri (in particolare <br> per a-capo).
        // L'utente può scrivere "Riga 1<br>Riga 2" nel titolo/sottotitolo dell'inspector.
        $allowed_inline = [
            'br'     => [],
            'strong' => [],
            'em'     => [],
            'b'      => [],
            'i'      => [],
            'u'      => [],
            'span'   => [
                'class' => true,
                'style' => true,
                // Consenti il pilotaggio inline del Text-FX su una singola parola del
                // titolo (es. ultima riga "scramble" che cicla data-fx-phrases) senza
                // applicare l'effetto all'intero titolo. Attributi letti da class-text-effects.js.
                'data-olo-text-fx'    => true,
                'data-fx-phrases'     => true,
                'data-fx-loop'        => true,
                'data-fx-speed'       => true,
                'data-fx-delay'       => true,
                'data-fx-pause'       => true,
            ],
            'sup'    => [],
            'sub'    => [],
            'mark'   => [],
            'small'  => [],
        ];
        $title    = wp_kses( (string) $s['title'],    $allowed_inline );
        $subtitle = wp_kses( (string) $s['subtitle'], $allowed_inline );
        list( $h_tfx_cls, $h_tfx_data ) = $this->tfx_attrs( $s, 'title', $title );
        list( $s_tfx_cls, $s_tfx_data ) = $this->tfx_attrs( $s, 'subtitle', $subtitle );

        // Layout values
        $min_height    = esc_attr( $s['min_height'] ?: '500px' );
        $max_w         = intval( $s['content_max_width'] ) ?: 700;
        $v_align       = $this->map_align( $s['vertical_align'], 'v' );
        $h_align       = $this->map_align( $s['horizontal_align'], 'h' );
        $text_align    = in_array( $s['text_align'], [ 'left', 'center', 'right' ], true ) ? $s['text_align'] : 'center';

        $_tp = $s['tile_padding'] ?? null;
        if ( is_array( $_tp ) ) {
            $pad_t = intval( $_tp['top']    ?? 60 );
            $pad_r = intval( $_tp['right']  ?? 20 );
            $pad_b = intval( $_tp['bottom'] ?? 60 );
            $pad_l = intval( $_tp['left']   ?? 20 );
        } else {
            $pad_t = $pad_b = 60;
            $pad_r = $pad_l = 20;
        }

        // CTA sizing (range value = font-size in px, padding proportional)
        $cta_fs       = intval( $s['cta_size'] ) ?: 15;
        $cta_pad_y    = round( $cta_fs * 0.8 );
        $cta_pad_x    = round( $cta_fs * 2.1 );
        $cta_size_css = "padding:{$cta_pad_y}px {$cta_pad_x}px;font-size:{$cta_fs}px;";

        // CTA radius — sistema border-radius unificato (oggetto {tl,tr,br,bl})
        $cta_radius_val       = $this->build_border_radius_css( $s['cta_radius'] ?? null );
        $cta_radius_css       = $cta_radius_val ? "border-radius:{$cta_radius_val};" : '';
        $cta_radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['cta_radius_hover'] ?? null );

        // CTA Primary colors — outline/ghost fallback to hero text color (visible)
        $cta_bg          = $this->safe_color_css( $s['cta_bg_color'] ) ?: 'var(--olo-color-on-primary, #FFFFFF)';
        $cta_fg_explicit = $this->safe_color_css( $s['cta_text_color'] );
        if ( $cta_fg_explicit ) {
            $cta_fg = $cta_fg_explicit;
        } elseif ( $s['cta_style'] === 'filled' ) {
            $cta_fg = 'var(--olo-color-primary, #e1474f)';
        } else {
            $cta_fg = $fg;
        }

        // CTA Secondary colors
        $cta2_bg = $this->safe_color_css( $s['cta2_bg_color'] ) ?: 'transparent';
        $cta2_fg = $this->safe_color_css( $s['cta2_text_color'] ) ?: 'var(--olo-color-on-primary, #FFFFFF)';

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: colors via the safe_color_css() whitelist (with token fallbacks), min-height esc_attr()'d at assignment, integers via intval()/round(), alignments from fixed maps/in_array() whitelists, CTA declarations via build_cta_css()/build_border_radius_css()/Olo_Tile_Utils radius helpers; $uid is internally generated. ?>
        <style>
            .<?php echo $uid; ?> {
                position: relative;
                min-height: <?php echo $min_height; ?>;
                display: flex;
                color: <?php echo $fg; ?>;
            }

            .<?php echo $uid; ?> .olo-hero-content {
                position: relative;
                z-index: 2;
                display: flex;
                flex: 1;
                width: 100%;
                align-items: <?php echo $v_align; ?>;
                justify-content: <?php echo $h_align; ?>;
                padding: <?php echo (int) $pad_t; ?>px <?php echo (int) $pad_r; ?>px <?php echo (int) $pad_b; ?>px <?php echo (int) $pad_l; ?>px;
            }

            .<?php echo $uid; ?> .olo-hero-inner {
                max-width: <?php echo (int) $max_w; ?>px;
                width: 100%;
                text-align: <?php echo $text_align; ?>;
            }

            .<?php echo $uid; ?> .olo-hero-inner h1 {
                margin: 0 0 0.4em;
                color: <?php echo $fg; ?>;
            }

            .<?php echo $uid; ?> .olo-hero-inner .olo-hero-sub {
                font-size: 1.25em;
                margin-bottom: 1.5em;
                opacity: 0.9;
                color: <?php echo $fg; ?>;
            }

            .<?php echo $uid; ?> .olo-hero-cta-wrap {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                <?php
                $jc = 'center';
                if ( $text_align === 'left' ) $jc = 'flex-start';
                if ( $text_align === 'right' ) $jc = 'flex-end';
                ?>
                justify-content: <?php echo $jc; ?>;
            }

            /* CTA Primary */
            .<?php echo $uid; ?> .olo-hero-cta1 {
                display: inline-block;
                font-weight: 600;
                text-decoration: none !important;
                <?php echo $cta_radius_css; ?>
                <?php echo $cta_size_css; ?>
                transition: opacity .2s, transform .2s;
                <?php echo $this->build_cta_css( $s['cta_style'], $cta_bg, $cta_fg ); ?>
            }
            <?php if ( $cta_radius_hover_css !== '' ) : ?>.<?php echo $uid; ?> .olo-hero-cta1{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?> .olo-hero-cta1:hover{border-radius:<?php echo $cta_radius_hover_css; ?> !important}<?php endif; ?>
            .<?php echo $uid; ?> .olo-hero-cta1:hover {
                opacity: .85;
                transform: translateY(-1px);
                color: <?php echo $cta_fg; ?> !important;
                text-decoration: none !important;
            }

            /* CTA Secondary */
            <?php if ( ! empty( $s['cta2_text'] ) ) : ?>
            .<?php echo $uid; ?> .olo-hero-cta2 {
                display: inline-block;
                font-weight: 600;
                text-decoration: none !important;
                <?php echo $cta_radius_css; ?>
                <?php echo $cta_size_css; ?>
                transition: opacity .2s, transform .2s;
                <?php echo $this->build_cta_css( $s['cta2_style'], $cta2_bg, $cta2_fg ); ?>
            }
            .<?php echo $uid; ?> .olo-hero-cta2:hover {
                opacity: .85;
                transform: translateY(-1px);
                color: <?php echo $cta2_fg; ?> !important;
                text-decoration: none !important;
            }
            <?php endif; ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-hero <?php echo esc_attr( $uid ); ?> olo-hero-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>">
            <div class="olo-hero-content">
                <div class="olo-hero-inner">
                    <?php
                    // Title inline style
                    $title_css = '';
                    if ( ! empty( $s['title_font_family'] ) ) {
                        $title_css .= 'font-family:' . esc_attr( $s['title_font_family'] ) . ';';
                    }
                    if ( ! empty( $s['title_font_size'] ) ) {
                        $title_css .= 'font-size:' . intval( $s['title_font_size'] ) . 'px;';
                    }
                    $title_css .= 'font-weight:' . esc_attr( $s['title_font_weight'] ?: '700' ) . ';';
                    $title_css .= 'line-height:' . esc_attr( $s['title_line_height'] ?: '1.2' ) . ';';
                    if ( ! empty( $s['title_letter_spacing'] ) && floatval( $s['title_letter_spacing'] ) != 0 ) {
                        $title_css .= 'letter-spacing:' . floatval( $s['title_letter_spacing'] ) . 'px;';
                    }
                    if ( ! empty( $s['title_text_transform'] ) && $s['title_text_transform'] !== 'none' ) {
                        $title_css .= 'text-transform:' . esc_attr( $s['title_text_transform'] ) . ';';
                    }
                    if ( ! empty( $s['title_color'] ) ) {
                        $title_css .= 'color:' . $this->safe_color_css( $s['title_color'] ) . ';';
                    }
                    // Title text-shadow (preset rapidi). Per ombre custom: tab Stile → Effetti → Ombra testo.
                    if ( ! empty( $s['title_text_shadow'] ) ) {
                        $title_css .= 'text-shadow:' . esc_attr( $s['title_text_shadow'] ) . ';';
                    }
                    $title_css .= 'margin:0 0 12px 0;';
                    $title_tag = in_array( $s['title_tag'], [ 'h1', 'h2', 'h3', 'p', 'span' ], true ) ? $s['title_tag'] : 'h1';
                    ?>
                    <<?php echo $title_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tag from the in_array() whitelist above; title wp_kses()'d with a fixed inline-tag set; style attr built from esc_attr()/intval()/floatval()/safe_color_css() parts; fx class/data from Olo_Text_Effects helpers ?> class="olo-hero-title<?php echo $h_tfx_cls; ?>" style="<?php echo $title_css; ?>"<?php echo $h_tfx_data; ?>><?php echo $title; ?></<?php echo $title_tag; ?>>
                    <?php
                    // Subtitle inline style
                    $sub_css = 'opacity:0.9;margin:0 0 24px 0;';
                    if ( ! empty( $s['subtitle_font_size'] ) ) {
                        $sub_css .= 'font-size:' . intval( $s['subtitle_font_size'] ) . 'px;';
                    }
                    if ( ! empty( $s['subtitle_font_weight'] ) ) {
                        $sub_css .= 'font-weight:' . esc_attr( $s['subtitle_font_weight'] ) . ';';
                    }
                    if ( ! empty( $s['subtitle_letter_spacing'] ) && floatval( $s['subtitle_letter_spacing'] ) != 0 ) {
                        $sub_css .= 'letter-spacing:' . floatval( $s['subtitle_letter_spacing'] ) . 'px;';
                    }
                    if ( ! empty( $s['subtitle_color'] ) ) {
                        $sub_css .= 'color:' . $this->safe_color_css( $s['subtitle_color'] ) . ';opacity:1;';
                    }
                    if ( ! empty( $s['subtitle_max_width'] ) ) {
                        $sub_css .= 'max-width:' . intval( $s['subtitle_max_width'] ) . 'px;';
                    }
                    ?>
                    <div class="olo-hero-sub<?php echo $s_tfx_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- subtitle wp_kses()'d with a fixed inline-tag set; style attr built from esc_attr()/intval()/floatval()/safe_color_css() parts; fx class/data from Olo_Text_Effects helpers ?>" style="<?php echo $sub_css; ?>"<?php echo $s_tfx_data; ?>><?php echo $subtitle; ?></div>

                    <?php if ( ! empty( $s['cta_text'] ) || ! empty( $s['cta2_text'] ) ) : ?>
                        <div class="olo-hero-cta-wrap">
                            <?php if ( ! empty( $s['cta_text'] ) ) : ?>
                                <a href="<?php echo esc_url( $s['cta_url'] ); ?>"
                                   class="olo-hero-cta1"
                                   <?php if ( $s['cta_target'] === '_blank' ) echo 'target="_blank" rel="noopener"'; ?>>
                                    <?php echo esc_html( wp_strip_all_tags( $s['cta_text'] ) ); ?>
                                </a>
                            <?php endif; ?>
                            <?php if ( ! empty( $s['cta2_text'] ) ) : ?>
                                <a href="<?php echo esc_url( $s['cta2_url'] ); ?>"
                                   class="olo-hero-cta2"
                                   <?php if ( $s['cta2_target'] === '_blank' ) echo 'target="_blank" rel="noopener"'; ?>>
                                    <?php echo esc_html( wp_strip_all_tags( $s['cta2_text'] ) ); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Text_Effects::css() from a fixed effect map, scoped to the internal uid selector
        $this->tfx_print_script();
        return ob_get_clean();
    }

    /**
     * Map alignment values to CSS flex properties.
     */
    private function map_align( $value, $axis ) {
        $map = [
            'top'    => 'flex-start',
            'center' => 'center',
            'bottom' => 'flex-end',
            'left'   => 'flex-start',
            'right'  => 'flex-end',
        ];
        return $map[ $value ] ?? 'center';
    }

    /**
     * Build CTA style CSS for filled/outline/ghost variants.
     */
    private function build_cta_css( $style, $bg, $fg ) {
        switch ( $style ) {
            case 'outline':
                return "background: transparent !important; color: {$fg} !important; border: 2px solid {$fg};";
            case 'ghost':
                return "background: transparent !important; color: {$fg} !important; border: none;";
            default: // filled
                return "background: {$bg} !important; color: {$fg} !important; border: none;";
        }
    }
}
