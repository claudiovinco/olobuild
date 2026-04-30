<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Button_Tile extends Olo_Tile_Base {

    protected $type     = 'button';
    protected $name     = 'Pulsante';
    protected $icon     = 'dashicons-button';
    protected $category = 'essential';
    protected $defaults = [
        'text'               => 'Click Here',
        'url'                => '#',
        'target'             => '_self',
        'alignment'          => 'center',
        'full_width'         => false,
        'bg_color'           => '',
        'text_color'         => '',
        'border_radius'      => '6',
        'tile_padding'       => [ 'top' => 14, 'right' => 32, 'bottom' => 14, 'left' => 32 ],
        'padding_x'          => '32',
        'padding_y'          => '14',
        'font_size'          => '16',
        'font_weight'        => '600',
        'letter_spacing'     => '0',
        'text_transform'     => 'none',
        'icon'               => '',
        'icon_position'      => 'before',
        'icon_spacing'       => '8',
        'border_width'       => '0',
        'border_color'       => '',
        'shadow'             => 'none',
        'hover_bg_color'     => '',
        'hover_text_color'   => '',
        'hover_border_color' => '',
        'hover_shadow'       => '',
        'hover_effect'       => 'lift',
        'hover_image'        => '',
        'hover_video'        => '',
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-btn-' . wp_rand( 10000, 99999 );

        // Alignment
        $align_class = 'uk-text-' . ( in_array( $s['alignment'], [ 'left', 'center', 'right' ], true ) ? $s['alignment'] : 'center' );

        // Colors
        $bg = $this->safe_color_css( $s['bg_color'] ) ?: 'var(--olo-color-primary, #6366F1)';
        $fg = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-primary-contrast, #FFFFFF)';

        // Border radius
        $rad_raw = $s['border_radius'];
        if ( is_array( $rad_raw ) ) {
            $rad_css = sprintf( '%dpx %dpx %dpx %dpx', absint( $rad_raw['tl'] ?? 0 ), absint( $rad_raw['tr'] ?? 0 ), absint( $rad_raw['br'] ?? 0 ), absint( $rad_raw['bl'] ?? 0 ) );
        } else {
            $rad_css = absint( $rad_raw ) . 'px';
        }

        // Typography preset (global style)
        $tp = sanitize_text_field( $s['typography_preset'] ?? '' );
        $tp_css = '';
        if ( $tp ) {
            $tp_css .= "font-family: var(--olo-font-{$tp}-family);\n";
            $tp_css .= "font-weight: var(--olo-font-{$tp}-weight);\n";
            $tp_css .= "text-transform: var(--olo-font-{$tp}-transform);\n";
            $tp_css .= "line-height: var(--olo-font-{$tp}-line-height);\n";
            $tp_css .= "letter-spacing: var(--olo-font-{$tp}-letter-spacing);\n";
        }

        // Padding: tile_padding (standard spacing object) with backward compat for padding_x/padding_y
        $tp = $s['tile_padding'] ?? null;
        if ( is_array( $tp ) ) {
            $pt = absint( $tp['top'] ?? 14 );
            $pr = absint( $tp['right'] ?? 32 );
            $pb = absint( $tp['bottom'] ?? 14 );
            $pl = absint( $tp['left'] ?? 32 );
        } else {
            $py_val = absint( $s['padding_y'] ?? 14 );
            $px_val = absint( $s['padding_x'] ?? 32 );
            $pt = $pb = $py_val;
            $pr = $pl = $px_val;
        }
        $fs = absint( $s['font_size'] );
        $fw_width = ! empty( $s['full_width'] ) ? 'width: 100%;' : '';
        $font_weight = absint( $s['font_weight'] ) ?: 600;
        $letter_spacing = floatval( $s['letter_spacing'] );
        $text_transform = in_array( $s['text_transform'], [ 'none', 'uppercase', 'lowercase', 'capitalize' ], true ) ? $s['text_transform'] : 'none';

        // Border
        $border_width = absint( $s['border_width'] );
        $border_color = $this->safe_color_css( $s['border_color'] ) ?: 'var(--olo-color-primary, #6366F1)';

        // Shadow
        $shadow = Olo_Tile_Utils::shadow( $s['shadow'] ?? 'none' );

        // Hover colors
        $hover_bg     = $this->safe_color_css( $s['hover_bg_color'] );
        $hover_fg     = $this->safe_color_css( $s['hover_text_color'] );
        $hover_bc     = $this->safe_color_css( $s['hover_border_color'] );
        $hover_shadow = ( $s['hover_shadow'] !== '' ) ? Olo_Tile_Utils::shadow( $s['hover_shadow'] ) : '';
        $hover_effect = $s['hover_effect'] ?? 'lift';

        // Hover image/video
        $has_hover_media = ! empty( $s['hover_image'] ) || ! empty( $s['hover_video'] );

        // Transition properties
        $transitions = [ 'background-color 0.25s ease', 'color 0.25s ease', 'border-color 0.25s ease', 'box-shadow 0.25s ease', 'transform 0.25s ease' ];

        // Determine the hover-transform target:
        // with hover media → .olo-hover-wrap  (so button + media move together)
        // without           → .olo-btn-link
        $transform_sel = $has_hover_media
            ? '.olo-hover-wrap'
            : '.olo-btn-link';

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> { overflow: visible; }
            .<?php echo $uid; ?> .olo-btn-link {
                display: inline-block;
                <?php echo $fw_width; ?>
                padding: <?php echo $pt; ?>px <?php echo $pr; ?>px <?php echo $pb; ?>px <?php echo $pl; ?>px;
                background-color: <?php echo $bg; ?> !important;
                color: <?php echo $fg; ?> !important;
                border-radius: <?php echo $rad_css; ?>;
                font-size: <?php echo $fs; ?>px;
                font-weight: <?php echo $font_weight; ?>;
                <?php if ( $tp_css ) : echo $tp_css; endif; ?>
                text-decoration: none !important;
                text-align: center;
                cursor: pointer;
                position: relative;
                <?php if ( $letter_spacing > 0 ) : ?>letter-spacing: <?php echo $letter_spacing; ?>px;<?php endif; ?>
                <?php if ( $text_transform !== 'none' ) : ?>text-transform: <?php echo $text_transform; ?>;<?php endif; ?>
                <?php if ( $border_width > 0 ) : ?>border: <?php echo $border_width; ?>px solid <?php echo $border_color; ?>;<?php endif; ?>
                <?php if ( $shadow !== 'none' ) : ?>box-shadow: <?php echo $shadow; ?>;<?php endif; ?>
                transition: <?php echo implode( ', ', $transitions ); ?>;
            }
            .<?php echo $uid; ?> .olo-btn-link:hover {
                text-decoration: none !important;
                <?php if ( $hover_bg ) : ?>background-color: <?php echo $hover_bg; ?> !important;<?php endif; ?>
                <?php if ( $hover_fg ) : ?>color: <?php echo $hover_fg; ?> !important;<?php endif; ?>
                <?php if ( $hover_bc ) : ?>border-color: <?php echo $hover_bc; ?>;<?php endif; ?>
                <?php if ( $hover_shadow !== '' ) : ?>box-shadow: <?php echo $hover_shadow; ?>;<?php endif; ?>
            }
            /* Transform effects — applied to <?php echo $transform_sel; ?> so media follows */
            .<?php echo $uid; ?> <?php echo $transform_sel; ?> {
                transition: transform 0.25s ease;
            }
            <?php
            $transform_hover_css = '';
            switch ( $hover_effect ) {
                case 'lift':
                    $transform_hover_css = 'transform: translateY(-2px);';
                    break;
                case 'grow':
                    $transform_hover_css = 'transform: scale(1.05);';
                    break;
                case 'shrink':
                    $transform_hover_css = 'transform: scale(0.95);';
                    break;
                case 'glow':
                    $glow_color = $hover_bg ?: $bg;
                    $transform_hover_css = 'box-shadow: 0 0 20px ' . $glow_color . '80;';
                    break;
            }
            if ( $transform_hover_css ) : ?>
            .<?php echo $uid; ?> <?php echo $transform_sel; ?>:hover {
                <?php echo $transform_hover_css; ?>
            }
            <?php endif; ?>
            .<?php echo $uid; ?> <?php echo $transform_sel; ?>:active {
                transform: translateY(0) scale(1);
            }
            <?php if ( $hover_effect === 'pulse' ) : ?>
            @keyframes olo-btn-pulse-<?php echo $uid; ?> {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.04); }
            }
            .<?php echo $uid; ?> <?php echo $transform_sel; ?>:hover {
                animation: olo-btn-pulse-<?php echo $uid; ?> 1s ease-in-out infinite;
            }
            <?php endif; ?>
            <?php if ( $has_hover_media ) : ?>
            .<?php echo $uid; ?> .olo-hover-wrap {
                display: inline-block;
                <?php echo $fw_width; ?>
                position: relative;
                border-radius: <?php echo $rad_css; ?>;
                overflow: hidden;
            }
            .<?php echo $uid; ?> .olo-hover-media {
                position: absolute;
                inset: 0;
                z-index: 1;
                pointer-events: none;
            }
            .<?php echo $uid; ?> .olo-hover-media img,
            .<?php echo $uid; ?> .olo-hover-media video {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            .<?php echo $uid; ?> .olo-btn-link {
                z-index: 2;
            }
            .<?php echo $uid; ?> .olo-btn-text {
                position: relative;
                z-index: 2;
            }
            <?php endif; ?>
        </style>

        <div class="olo-button <?php echo esc_attr( $align_class ); ?> <?php echo esc_attr( $uid ); ?>" style="padding: 16px 0; overflow: visible;">
            <?php
            $target_attr = $s['target'] === '_blank' ? ' target="_blank" rel="noopener noreferrer"' : ' target="_self"';

            $icon_html = '';
            if ( ! empty( $s['icon'] ) ) {
                $icon_spacing = absint( $s['icon_spacing'] ?? 8 );
                $icon_pos = $s['icon_position'] === 'after' ? 'after' : 'before';
                $icon_html = '<span uk-icon="icon: ' . esc_attr( $s['icon'] ) . '; ratio: 1" style="vertical-align: middle;"></span>';
            }

            $text_html = esc_html( $s['text'] );
            $icon_spacing_px = absint( $s['icon_spacing'] ?? 8 );

            list( $tfx_cls, $tfx_data ) = $this->tfx_attrs( $s, 'text', $s['text'] );
            $text_span_cls = 'olo-btn-text' . $tfx_cls;

            if ( $icon_html && $s['icon_position'] === 'after' ) {
                $inner = '<span class="' . $text_span_cls . '" style="display:inline-flex;align-items:center;gap:' . $icon_spacing_px . 'px;"' . $tfx_data . '>' . $text_html . $icon_html . '</span>';
            } elseif ( $icon_html ) {
                $inner = '<span class="' . $text_span_cls . '" style="display:inline-flex;align-items:center;gap:' . $icon_spacing_px . 'px;"' . $tfx_data . '>' . $icon_html . $text_html . '</span>';
            } else {
                $inner = '<span class="' . $text_span_cls . '"' . $tfx_data . '>' . $text_html . '</span>';
            }

            $btn_html = '<a href="' . esc_url( $s['url'] ) . '"' . $target_attr . ' class="olo-btn-link" role="button">' . $inner . '</a>';

            if ( $has_hover_media ) {
                echo $this->render_hover_wrap( $btn_html, $s['hover_image'] ?? '', $s['hover_video'] ?? '' );
            } else {
                echo $btn_html;
            }
            ?>
        </div>
        <?php
        // Text-effects scoped CSS + runtime script (idempotent)
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();
        return ob_get_clean();
    }
}
