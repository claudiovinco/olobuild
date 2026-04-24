<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Headline_Tile extends Olo_Tile_Base {

    protected $type     = 'headline';
    protected $name     = 'Titolo';
    protected $icon     = 'dashicons-heading';
    protected $category = 'essential';
    protected $defaults = [
        'heading'           => 'Titolo sezione',
        'subtitle'          => '',
        'tag'               => 'h2',
        'alignment'         => 'center',
        'heading_size'      => 'lg',
        'heading_color'     => '',
        'heading_italic'    => false,
        'heading_uppercase' => false,
        'decoration'        => 'line',
        'decoration_color'  => '',
        'subtitle_color'    => '',
        'text_stroke'       => '0',
        'text_stroke_color' => '',
        'text_shadow'       => '',
        'gradient_text'     => false,
        'gradient_from'     => '',
        'gradient_to'       => '',
        'gradient_angle'    => '90',
        'blend_mode'        => 'normal',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'heading',      'type' => 'text',     'label' => 'Heading' ],
            [ 'key' => 'subtitle',     'type' => 'textarea', 'label' => 'Subtitle' ],
            [ 'key' => 'tag',          'type' => 'select',   'label' => 'Tag' ],
            [ 'key' => 'alignment',    'type' => 'select',   'label' => 'Alignment' ],
            [ 'key' => 'heading_size', 'type' => 'select',   'label' => 'Heading Size' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        // Size classes (UIkit)
        $size_map = [
            'sm' => 'uk-heading-small',
            'md' => 'uk-heading-medium',
            'lg' => 'uk-heading-large',
            'xl' => 'uk-heading-xlarge',
        ];
        $heading_class = $size_map[ $s['heading_size'] ] ?? 'uk-heading-large';

        // Alignment
        $align_class = 'uk-text-' . esc_attr( $s['alignment'] );

        // Tag
        $allowed_tags = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p' ];
        $tag = in_array( $s['tag'], $allowed_tags, true ) ? $s['tag'] : 'h2';

        // Colors
        $hd_clr  = $this->safe_color_css( $s['heading_color'] );
        $dec_clr = $this->safe_color_css( $s['decoration_color'] );
        $sub_clr = $this->safe_color_css( $s['subtitle_color'] );

        // Gradient
        $has_gradient = ! empty( $s['gradient_text'] );

        // Text effects
        $text_effects = '';
        $stroke = absint( $s['text_stroke'] ?? 0 );
        if ( $stroke > 0 ) {
            $stroke_clr = $this->safe_color_css( $s['text_stroke_color'] ) ?: '#000';
            $text_effects .= '-webkit-text-stroke:' . $stroke . 'px ' . $stroke_clr . ';';
        }
        if ( ! empty( $s['text_shadow'] ) ) {
            if ( $s['text_shadow'] === 'custom' ) {
                $ts_h     = intval( $s['text_shadow_h'] ?? 0 );
                $ts_v     = intval( $s['text_shadow_v'] ?? 0 );
                $ts_blur  = intval( $s['text_shadow_blur'] ?? 0 );
                $ts_color = esc_attr( $s['text_shadow_color'] ?? 'rgba(0,0,0,0.3)' );
                $text_effects .= "text-shadow:{$ts_h}px {$ts_v}px {$ts_blur}px {$ts_color};";
            } else {
                $text_effects .= 'text-shadow:' . esc_attr( $s['text_shadow'] ) . ';';
            }
        }
        if ( ! empty( $s['blend_mode'] ) && $s['blend_mode'] !== 'normal' ) {
            $text_effects .= 'mix-blend-mode:' . esc_attr( $s['blend_mode'] ) . ';';
        }

        // Heading inline style
        $heading_style = 'margin:0;font-weight:bold;';

        // Apply global typography preset if set
        $tp = sanitize_text_field( $s['typography_preset'] ?? '' );
        if ( $tp ) {
            $heading_style .= "font-family:var(--olo-font-{$tp}-family);";
            $heading_style .= "font-weight:var(--olo-font-{$tp}-weight);";
            $heading_style .= "text-transform:var(--olo-font-{$tp}-transform);";
            $heading_style .= "line-height:var(--olo-font-{$tp}-line-height);";
            $heading_style .= "letter-spacing:var(--olo-font-{$tp}-letter-spacing);";
        }

        if ( ! empty( $s['heading_italic'] ) ) {
            $heading_style .= 'font-style:italic;';
        }
        if ( ! empty( $s['heading_uppercase'] ) ) {
            $heading_style .= 'text-transform:uppercase;letter-spacing:0.05em;';
        }
        // Gradient wins over heading_color
        if ( ! $has_gradient && $hd_clr ) {
            $heading_style .= 'color:' . $hd_clr . ';';
        }
        $heading_style .= $text_effects;

        // Unique ID for scoped styles
        $uid = 'olo-hl-' . wp_rand( 10000, 99999 );

        // Heading text — support multiline via \n or <br>
        $raw = $s['heading'];
        // Convert <br> / <br/> / <br /> to \n before stripping
        $raw = preg_replace( '/<br\s*\/?>/i', "\n", $raw );
        $raw = trim( wp_strip_all_tags( $raw ) );
        $heading_text = str_contains( $raw, "\n" )
            ? nl2br( esc_html( $raw ) )
            : esc_html( $raw );
        $heading_extra = $has_gradient ? ' olo-hl-grad' : '';

        // Decoration color fallback
        $dec_css = $dec_clr ?: 'var(--olo-color-primary, #6366F1)';

        // Subtitle — strip <p> tags (legacy editor), keep inline formatting
        $subtitle_text = '';
        if ( ! empty( $s['subtitle'] ) ) {
            $subtitle_text = esc_html( wp_strip_all_tags( $s['subtitle'] ) );
        }

        ob_start();

        $needs_style = $has_gradient || $dec_clr;
        if ( $needs_style ) :
        ?>
        <style>
        <?php if ( $has_gradient ) :
            $gf = $this->safe_color_css( $s['gradient_from'] ) ?: 'var(--olo-color-primary, #6366F1)';
            $gt = $this->safe_color_css( $s['gradient_to'] ) ?: '#EC4899';
            $ga = absint( $s['gradient_angle'] ?? 90 );
        ?>
        .<?php echo $uid; ?> .olo-hl-grad { background: linear-gradient(<?php echo $ga; ?>deg, <?php echo $gf; ?>, <?php echo $gt; ?>); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        <?php endif; ?>
        <?php if ( $dec_clr ) : ?>
        .<?php echo $uid; ?> .uk-heading-line > span::before, .<?php echo $uid; ?> .uk-heading-line > span::after { border-color: <?php echo $dec_clr; ?>; }
        .<?php echo $uid; ?> .uk-heading-divider { border-color: <?php echo $dec_clr; ?>; }
        <?php endif; ?>
        </style>
        <?php endif; ?>
        <div class="olo-headline <?php echo $uid; ?> <?php echo $align_class; ?>" style="display:block;width:100%;">
            <?php if ( $s['decoration'] === 'line' ) : ?>
                <<?php echo $tag; ?> class="<?php echo esc_attr( $heading_class ); ?> uk-heading-line<?php echo $heading_extra; ?>" style="<?php echo $heading_style; ?>">
                    <span><?php echo $heading_text; ?></span>
                </<?php echo $tag; ?>>

            <?php elseif ( $s['decoration'] === 'divider' ) : ?>
                <<?php echo $tag; ?> class="<?php echo esc_attr( $heading_class ); ?> uk-heading-divider<?php echo $heading_extra; ?>" style="<?php echo $heading_style; ?>">
                    <?php echo $heading_text; ?>
                </<?php echo $tag; ?>>

            <?php else : ?>
                <?php
                $deco_count   = max( 1, min( 9, absint( $s['decoration_count'] ?? 3 ) ) );
                $deco_spacing = max( 0, min( 20, absint( $s['decoration_spacing'] ?? 6 ) ) );
                $jc_map       = [ 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' ];
                $jc           = $jc_map[ $s['alignment'] ] ?? 'center';
                ?>
                <?php if ( $s['decoration'] === 'dot' ) : ?>
                    <div style="display:flex;justify-content:<?php echo $jc; ?>;gap:<?php echo $deco_spacing; ?>px;margin-bottom:12px;">
                    <?php for ( $di = 0; $di < $deco_count; $di++ ) : ?>
                        <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:<?php echo $dec_css; ?>;"></span>
                    <?php endfor; ?>
                    </div>
                <?php elseif ( $s['decoration'] === 'star' ) : ?>
                    <div style="display:flex;justify-content:<?php echo $jc; ?>;gap:<?php echo $deco_spacing; ?>px;font-size:1.5em;margin-bottom:8px;<?php if ( $dec_clr ) echo 'color:' . $dec_clr . ';'; ?>">
                    <?php for ( $di = 0; $di < $deco_count; $di++ ) : ?>
                        <span><?php echo esc_html( olo_t( '&#x2605;' ) ); ?></span>
                    <?php endfor; ?>
                    </div>
                <?php endif; ?>
                <<?php echo $tag; ?> class="<?php echo esc_attr( $heading_class ); ?><?php echo $heading_extra; ?>" style="<?php echo $heading_style; ?>">
                    <?php echo $heading_text; ?>
                </<?php echo $tag; ?>>
            <?php endif; ?>

            <?php if ( ! empty( $subtitle_text ) ) : ?>
                <p style="margin:12px 0 0;font-size:1em;line-height:1.5;<?php if ( $sub_clr ) echo 'color:' . $sub_clr . ';'; ?>"><?php echo $subtitle_text; ?></p>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
