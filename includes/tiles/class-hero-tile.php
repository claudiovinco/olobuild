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
        // Contenuto
        'title'            => 'Benvenuto nel nostro sito',
        'subtitle'         => 'Scopri qualcosa di straordinario',
        'text_color'       => '#FFFFFF',

        // Layout
        'min_height'         => '500px',
        'content_max_width'  => '700',
        'vertical_align'     => 'center',
        'horizontal_align'   => 'center',
        'text_align'         => 'center',
        'padding_y'          => '80',
        'padding_x'          => '30',

        // Sfondo
        'bg_type'            => 'color',
        'bg_color'           => '',
        'bg_gradient_from'   => '',
        'bg_gradient_to'     => '',
        'bg_gradient_angle'  => '135',
        'bg_image'           => '',
        'bg_video'           => '',
        'bg_position'        => 'center',
        'bg_size'            => 'cover',
        'bg_fixed'           => false,

        // Overlay
        'overlay'                => false,
        'overlay_color'          => '#000000',
        'overlay_opacity'        => '50',
        'overlay_gradient'       => false,
        'overlay_gradient_to'    => 'transparent',
        'overlay_gradient_angle' => '180',

        // CTA Primario
        'cta_text'       => 'Inizia ora',
        'cta_url'        => '#',
        'cta_target'     => '_self',
        'cta_bg_color'   => '#FFFFFF',
        'cta_text_color' => '',
        'cta_radius'     => '6',
        'cta_size'       => '15',
        'cta_style'      => 'filled',

        // CTA Secondario
        'cta2_text'       => '',
        'cta2_url'        => '#',
        'cta2_target'     => '_self',
        'cta2_bg_color'   => 'transparent',
        'cta2_text_color' => '#FFFFFF',
        'cta2_style'      => 'outline',

        // Titolo tipografia
        'title_tag'              => 'h1',
        'title_font_family'      => '',
        'title_font_size'        => '',
        'title_font_weight'      => '700',
        'title_letter_spacing'   => '0',
        'title_line_height'      => '1.2',
        'title_text_transform'   => 'none',
        'title_color'            => '',
        'title_text_shadow'      => '',
        'title_text_shadow_h'    => '0',
        'title_text_shadow_v'    => '0',
        'title_text_shadow_blur' => '0',
        'title_text_shadow_color'=> 'rgba(0,0,0,0.3)',

        // Sottotitolo tipografia
        'subtitle_font_size'     => '',
        'subtitle_font_weight'   => '400',
        'subtitle_letter_spacing'=> '0',
        'subtitle_color'         => '',
        'subtitle_max_width'     => '',

        // Avanzato
        'full_bleed'     => false,
        'shadow'         => 'none',
        'border_width'   => '0',
        'border_color'   => '',
        'border_radius'  => '0',
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-hero-' . wp_rand( 10000, 99999 );

        $fg = $this->safe_color_css( $s['text_color'] ) ?: '#FFFFFF';

        // Sanitize rich text
        $title    = esc_html( wp_strip_all_tags( $s['title'] ) );
        $subtitle = esc_html( wp_strip_all_tags( $s['subtitle'] ) );

        // Layout values
        $min_height    = esc_attr( $s['min_height'] ?: '500px' );
        $max_w         = intval( $s['content_max_width'] ) ?: 700;
        $v_align       = $this->map_align( $s['vertical_align'], 'v' );
        $h_align       = $this->map_align( $s['horizontal_align'], 'h' );
        $text_align    = in_array( $s['text_align'], [ 'left', 'center', 'right' ] ) ? $s['text_align'] : 'center';
        $_tp = $s['tile_padding'] ?? null;
        if ( is_array( $_tp ) ) {
            $pad_y = intval( $_tp['top'] ?? 0 );
            $pad_x = intval( $_tp['right'] ?? 0 );
        } else {
            $pad_y = intval( $s['padding_y'] ?? 80 );
            $pad_x = intval( $s['padding_x'] ?? 30 );
        }
        $border_radius = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );

        // Background
        $bg_css = $this->build_background_css( $s );

        // Overlay
        $overlay_css = '';
        if ( ! empty( $s['overlay'] ) ) {
            $overlay_css = $this->build_overlay_css( $s );
        }

        // CTA sizing (range value = font-size in px, padding proportional)
        $cta_fs     = intval( $s['cta_size'] ) ?: 15;
        $cta_pad_y  = round( $cta_fs * 0.8 );
        $cta_pad_x  = round( $cta_fs * 2.1 );
        $cta_size_css = "padding:{$cta_pad_y}px {$cta_pad_x}px;font-size:{$cta_fs}px;";
        $cta_radius   = Olo_Tile_Utils::border_radius( $s['cta_radius'] ?? 0 );

        // CTA Primary colors — outline/ghost fallback to hero text color (visible)
        $cta_bg  = $this->safe_color_css( $s['cta_bg_color'] ) ?: '#FFFFFF';
        $cta_fg_explicit = $this->safe_color_css( $s['cta_text_color'] );
        if ( $cta_fg_explicit ) {
            $cta_fg = $cta_fg_explicit;
        } elseif ( $s['cta_style'] === 'filled' ) {
            $cta_fg = $this->safe_color_css( $s['bg_color'] ) ?: 'var(--olo-color-primary, #6366F1)';
        } else {
            $cta_fg = $fg; // hero text_color (white)
        }

        // CTA Secondary colors
        $cta2_bg = $this->safe_color_css( $s['cta2_bg_color'] ) ?: 'transparent';
        $cta2_fg = $this->safe_color_css( $s['cta2_text_color'] ) ?: '#FFFFFF';

        // Full bleed
        $full_bleed = ! empty( $s['full_bleed'] );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                position: relative;
                overflow: hidden;
                min-height: <?php echo $min_height; ?>;
                display: flex;
                <?php echo $bg_css; ?>
                color: <?php echo $fg; ?>;
                <?php if ( $border_radius && $border_radius !== '0px' ) : ?>border-radius: <?php echo $border_radius; ?>;<?php endif; ?>
                <?php if ( $full_bleed ) : ?>
                width: 100vw;
                position: relative;
                left: 50%;
                margin-left: -50vw;
                <?php endif; ?>
            }

            <?php if ( ! empty( $s['bg_type'] ) && $s['bg_type'] === 'video' && ! empty( $s['bg_video'] ) ) : ?>
            .<?php echo $uid; ?> .olo-hero-video {
                position: absolute;
                inset: 0;
                width: 100%;
                height: 100%;
                object-fit: <?php echo esc_attr( $s['bg_size'] ?: 'cover' ); ?>;
                z-index: 0;
            }
            <?php endif; ?>

            <?php if ( ! empty( $s['overlay'] ) ) : ?>
            .<?php echo $uid; ?> .olo-hero-overlay {
                position: absolute;
                inset: 0;
                z-index: 1;
                <?php echo $overlay_css; ?>
            }
            <?php endif; ?>

            .<?php echo $uid; ?> .olo-hero-content {
                position: relative;
                z-index: 2;
                display: flex;
                flex: 1;
                width: 100%;
                align-items: <?php echo $v_align; ?>;
                justify-content: <?php echo $h_align; ?>;
                padding: <?php echo $pad_y; ?>px <?php echo $pad_x; ?>px;
            }

            .<?php echo $uid; ?> .olo-hero-inner {
                max-width: <?php echo $max_w; ?>px;
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
                border-radius: <?php echo $cta_radius; ?>;
                <?php echo $cta_size_css; ?>
                transition: opacity .2s, transform .2s;
                <?php echo $this->build_cta_css( $s['cta_style'], $cta_bg, $cta_fg ); ?>
            }
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
                border-radius: <?php echo $cta_radius; ?>;
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
        <div class="olo-hero <?php echo esc_attr( $uid ); ?>">
            <?php if ( $s['bg_type'] === 'video' && ! empty( $s['bg_video'] ) ) : ?>
                <video class="olo-hero-video" src="<?php echo esc_url( $s['bg_video'] ); ?>" autoplay muted loop playsinline></video>
            <?php endif; ?>

            <?php if ( ! empty( $s['overlay'] ) ) : ?>
                <div class="olo-hero-overlay"></div>
            <?php endif; ?>

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
                    // Title text-shadow
                    if ( ! empty( $s['title_text_shadow'] ) ) {
                        if ( $s['title_text_shadow'] === 'custom' ) {
                            $ts_h = intval( $s['title_text_shadow_h'] ?? 0 );
                            $ts_v = intval( $s['title_text_shadow_v'] ?? 0 );
                            $ts_b = intval( $s['title_text_shadow_blur'] ?? 0 );
                            $ts_c = esc_attr( $s['title_text_shadow_color'] ?? 'rgba(0,0,0,0.3)' );
                            $title_css .= "text-shadow:{$ts_h}px {$ts_v}px {$ts_b}px {$ts_c};";
                        } else {
                            $title_css .= 'text-shadow:' . esc_attr( $s['title_text_shadow'] ) . ';';
                        }
                    }
                    $title_css .= 'margin:0 0 12px 0;';
                    $title_tag = in_array( $s['title_tag'], [ 'h1', 'h2', 'h3', 'p', 'span' ], true ) ? $s['title_tag'] : 'h1';
                    ?>
                    <<?php echo $title_tag; ?> style="<?php echo $title_css; ?>"><?php echo $title; ?></<?php echo $title_tag; ?>>
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
                    <div class="olo-hero-sub" style="<?php echo $sub_css; ?>"><?php echo $subtitle; ?></div>

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
     * Build background CSS rules string.
     */
    private function build_background_css( $s ) {
        $type = $s['bg_type'] ?? 'color';

        switch ( $type ) {
            case 'gradient':
                $from  = $this->safe_color_css( $s['bg_gradient_from'] ) ?: 'var(--olo-color-primary, #6366F1)';
                $to    = $this->safe_color_css( $s['bg_gradient_to'] ) ?: '#8B5CF6';
                $angle = intval( $s['bg_gradient_angle'] ) ?: 135;
                return "background: linear-gradient({$angle}deg, {$from}, {$to});";

            case 'image':
                $url   = esc_url( $s['bg_image'] );
                $pos   = esc_attr( $s['bg_position'] ?: 'center' );
                $size  = esc_attr( $s['bg_size'] ?: 'cover' );
                $fixed = ! empty( $s['bg_fixed'] ) ? 'fixed' : 'scroll';
                if ( $url ) {
                    return "background: url('{$url}') {$pos} / {$size} no-repeat {$fixed};";
                }
                return 'background-color: var(--olo-color-secondary, #1F2937);';

            case 'video':
                // Video element is positioned absolute, give a dark fallback
                return 'background-color: var(--olo-color-secondary, #1F2937);';

            default: // color
                $color = $this->safe_color_css( $s['bg_color'] ) ?: 'var(--olo-color-primary, #6366F1)';
                return "background-color: {$color};";
        }
    }

    /**
     * Build overlay CSS rules string.
     */
    private function build_overlay_css( $s ) {
        $color   = $this->safe_color_css( $s['overlay_color'] ) ?: '#000000';
        $opacity = ( intval( $s['overlay_opacity'] ) ?: 50 ) / 100;

        if ( ! empty( $s['overlay_gradient'] ) ) {
            $to    = $this->safe_color_css( $s['overlay_gradient_to'] ) ?: 'transparent';
            $angle = intval( $s['overlay_gradient_angle'] ) ?: 180;
            return "background: linear-gradient({$angle}deg, {$color}, {$to}); opacity: {$opacity};";
        }

        return "background-color: {$color}; opacity: {$opacity};";
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
