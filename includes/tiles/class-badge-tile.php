<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Tile Badge / Etichetta — pill compatta con testo + icona + opzione "Stato live".
 * Lo "Stato live" antepone un pallino con onda pulsante (.olo-live-dot); la CSS e il
 * @keyframes olo-pulse sono registrati UNA volta nel foglio frontend (frontend.css)
 * e nel CSS del canvas builder (iframe-builder.css), non per-tile.
 *
 * Chiavi additive: badge_live (bool), badge_live_color ('success'|'primary').
 */
class Olo_Badge_Tile extends Olo_Tile_Base {

    protected $type     = 'badge';
    protected $name     = 'Badge / Etichetta';
    protected $icon     = 'dashicons-tag';
    protected $category = 'text';
    protected $defaults = [
        'preset'          => 'custom',
        'text'            => 'Online',
        'icon'            => '',
        'icon_position'   => 'before',
        'badge_live'       => false,
        'badge_live_color' => 'success',
        'variant'         => 'soft',
        'bg_color'        => '',
        'text_color'      => '',
        'font_size'       => '13',
        'font_weight'     => '600',
        'text_transform'  => 'none',
        'letter_spacing'  => '0',
        'badge_radius'    => [ 'tl' => 999, 'tr' => 999, 'br' => 999, 'bl' => 999 ],
        'padding_y'       => 7,
        'padding_x'       => 13,
        'alignment'       => 'left',
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

    public function render( $settings, $style = [] ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $uid     = 'olo-badge-' . wp_rand( 10000, 99999 );
        $variant = in_array( $s['variant'], [ 'soft', 'solid', 'outline', 'light' ], true ) ? $s['variant'] : 'soft';

        // Colori da ruoli globali del cliente (no hex hardcoded): accent = bg_color o primario.
        $accent = $this->safe_color_css( $s['bg_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $txt    = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text, #1f2937)';

        // Box model
        $pad_y  = max( 0, intval( $s['padding_y'] ?? 7 ) );
        $pad_x  = max( 0, intval( $s['padding_x'] ?? 13 ) );
        $radius = $this->build_border_radius_css( $s['badge_radius'] ?? null ) ?: '999px';
        $fs     = max( 8, intval( $s['font_size'] ?? 13 ) );
        $fw     = esc_attr( $s['font_weight'] ?? '600' );
        $tt     = esc_attr( $s['text_transform'] ?? 'none' );
        $ls     = floatval( $s['letter_spacing'] ?? 0 );

        // Variant → background/border/color
        switch ( $variant ) {
            case 'solid':
                $bg     = $accent;
                $color  = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-on-primary, #ffffff)';
                $border = '1px solid transparent';
                break;
            case 'outline':
                $bg     = 'transparent';
                $color  = $this->safe_color_css( $s['text_color'] ) ?: $accent;
                $border = '1px solid ' . $accent;
                break;
            case 'light':
                $bg     = 'var(--olo-color-background, #ffffff)';
                $color  = $txt;
                $border = '1px solid var(--olo-color-border, #e6e8ec)';
                break;
            default: // soft
                $bg     = 'color-mix(in srgb, ' . $accent . ' 12%, transparent)';
                $color  = $txt;
                $border = '1px solid color-mix(in srgb, ' . $accent . ' 22%, transparent)';
        }

        $justify = $s['alignment'] === 'center' ? 'center' : ( $s['alignment'] === 'right' ? 'flex-end' : 'flex-start' );

        $badge_css = 'display:inline-flex;align-items:center;gap:8px;'
            . 'padding:' . $pad_y . 'px ' . $pad_x . 'px;'
            . 'border-radius:' . $radius . ';'
            . 'background:' . $bg . ';border:' . $border . ';color:' . $color . ';'
            . 'font-size:' . $fs . 'px;font-weight:' . $fw . ';text-transform:' . $tt . ';'
            . 'letter-spacing:' . $ls . 'px;line-height:1;';

        // Live dot
        $live_html = '';
        if ( ! empty( $s['badge_live'] ) ) {
            $brand = ( $s['badge_live_color'] ?? 'success' ) === 'primary' ? ' is-brand' : '';
            $live_html = '<span class="olo-live-dot' . $brand . '" aria-hidden="true"></span>';
        }

        // Icona (SVG dal set, mai emoji)
        $icon_html = ! empty( $s['icon'] ) ? $this->render_icon_html( $s['icon'], 0.8 ) : '';
        $icon_before = ( $s['icon_position'] ?? 'before' ) !== 'after' ? $icon_html : '';
        $icon_after  = ( $s['icon_position'] ?? 'before' ) === 'after'  ? $icon_html : '';

        $text = esc_html( wp_strip_all_tags( $s['text'] ?? '' ) );

        ob_start();
        ?>
        <div class="olo-badge-wrap <?php echo esc_attr( $uid ); ?>" style="display:flex;justify-content:<?php echo $justify; ?>;">
            <span class="olo-badge" style="<?php echo esc_attr( $badge_css ); ?>">
                <?php echo $live_html; ?>
                <?php echo $icon_before; ?>
                <span class="olo-badge-text" data-olo-editable="text"><?php echo $text; ?></span>
                <?php echo $icon_after; ?>
            </span>
        </div>
        <?php
        return ob_get_clean();
    }
}
