<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Readingtime_Tile extends Olo_Tile_Base {

    protected $type     = 'readingtime';
    protected $name     = 'Tempo di Lettura';
    protected $icon     = 'dashicons-clock';
    protected $category = 'dynamic';
    protected $defaults = [
        'words_per_minute' => 200,
        'format'           => 'full',
        'prefix'           => 'Tempo di lettura:',
        'suffix'           => 'min',
        'icon'             => 'clock',
        'show_icon'        => true,
        'text_color'       => '',
        'icon_color'       => '',
        'font_size'        => '',
        'font_weight'      => '',
        'text_align'       => 'left',
        'border_width'     => '0',
        'border_color'     => '',
        'border_radius'    => '0',
        'box_shadow'       => '',
            'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-rt-' . wp_rand( 10000, 99999 );

        $wpm    = max( 50, min( 500, absint( $s['words_per_minute'] ) ) ) ?: 200;
        $format = in_array( $s['format'], [ 'full', 'short', 'minutes_only' ], true ) ? $s['format'] : 'full';
        $prefix = esc_html( $s['prefix'] );
        $suffix = esc_html( $s['suffix'] );
        $align  = in_array( $s['text_align'], [ 'left', 'center', 'right' ], true ) ? $s['text_align'] : 'left';

        $show_icon  = filter_var( $s['show_icon'], FILTER_VALIDATE_BOOLEAN );
        $text_color = $this->safe_color_css( $s['text_color'] );
        $icon_color = $this->safe_color_css( $s['icon_color'] );
        $font_size  = esc_attr( $s['font_size'] );
        $font_weight = esc_attr( $s['font_weight'] );

        // Calcolo tempo lettura
        $minutes = 0;
        $post = get_post();
        if ( $post ) {
            $content    = get_the_content( null, false, $post );
            $word_count = str_word_count( wp_strip_all_tags( $content ) );
            $minutes    = (int) ceil( $word_count / $wpm );
            if ( $minutes < 1 ) {
                $minutes = 1;
            }
        }

        // Formatta testo
        if ( ! $post ) {
            $display_text = '&mdash;';
        } else {
            switch ( $format ) {
                case 'minutes_only':
                    $display_text = (string) $minutes;
                    break;
                case 'short':
                    $display_text = $minutes . ' ' . $suffix . ' ' . olo_t( 'di lettura' );
                    break;
                case 'full':
                default:
                    $display_text = $prefix . ' ' . $minutes . ' ' . $suffix;
                    break;
            }
        }

        // Stili
        $bw = intval( $s['border_width'] );
        $bc = $this->safe_color_css( $s['border_color'] ) ?: 'var(--olo-color-border, #e5e7eb)';
        $br = esc_attr( $s['border_radius'] );

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: the internally generated $uid, in_array() whitelisted align (and fixed-literal ternary), safe_color_css() colours, esc_attr()'d size/weight/radius and the intval()'d border width.
        ?>
        <style>
            .<?php echo $uid; ?> {
                display: flex;
                align-items: center;
                gap: 8px;
                text-align: <?php echo $align; ?>;
                justify-content: <?php echo $align === 'center' ? 'center' : ( $align === 'right' ? 'flex-end' : 'flex-start' ); ?>;
                padding: 8px 12px;
                <?php if ( $text_color ) : ?>color: <?php echo $text_color; ?>;<?php endif; ?>
                <?php if ( $font_size ) : ?>font-size: <?php echo $font_size; ?>;<?php endif; ?>
                <?php if ( $font_weight ) : ?>font-weight: <?php echo $font_weight; ?>;<?php endif; ?>
                <?php if ( $bw > 0 ) : ?>border: <?php echo (int) $bw; ?>px solid <?php echo $bc; ?>;<?php endif; ?>
                <?php if ( $br ) : ?>border-radius: <?php echo $br; ?>px;<?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-rt-icon svg {
                width: 1em;
                height: 1em;
                vertical-align: -0.125em;
                <?php if ( $icon_color ) : ?>color: <?php echo $icon_color; ?>;<?php endif; ?>
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-readingtime <?php echo esc_attr( $uid ); ?>">
            <?php if ( $show_icon ) : ?>
                <span class="olo-rt-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </span>
            <?php endif; ?>
            <span class="olo-rt-text"><?php echo $display_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- assembled above from esc_html()'d prefix/suffix, an integer minute count, a translated fixed literal or the '&mdash;' entity ?></span>
        </div>
        <?php
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }
}
