<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Olobuild_Starrating_Tile extends Olobuild_Tile_Base {
    protected $type     = 'starrating';
    protected $name     = 'Valutazione';
    protected $icon     = 'dashicons-star-filled';
    protected $category = 'marketing';
    protected $defaults = [
        'preset' => 'custom',
        'rating'         => '4',
        'max_stars'      => '5',
        'star_size'      => '32',
        'star_color'     => '',
        'empty_color'    => '',
        'style'          => 'filled',
        'title'          => '',
        'subtitle'       => '',
        'title_color'    => '',
        'subtitle_color' => '',
        'alignment'      => 'center',
            'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() { return []; }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $rating = floatval( $s['rating'] );
        $max    = absint( $s['max_stars'] ) ?: 5;
        $size   = absint( $s['star_size'] ) ?: 32;
        $clr    = $this->safe_color_css( $s['star_color'] ) ?: 'var(--olo-color-accent, #f4a23b)';
        $empty  = $this->safe_color_css( $s['empty_color'] ) ?: 'var(--olo-color-border, #E5E7EB)';
        $align  = in_array( $s['alignment'], ['left','center','right'], true ) ? $s['alignment'] : 'center';
        $is_outline = $s['style'] === 'outline';
        $star_d = 'M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z';

        ob_start();
        ?>
        <?php $sr_uid = 'olo-sr-' . wp_unique_id(); ?>
        <div class="olo-starrating <?php echo esc_attr( $sr_uid ); ?> olo-sr-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" style="text-align:<?php echo esc_attr( $align ); ?>;padding:16px;">
            <?php
            list( $srt_cls, $srt_data ) = $this->tfx_attrs( $s, 'title', wp_strip_all_tags( $s['title'] ?? '' ) );
            list( $srs_cls, $srs_data ) = $this->tfx_attrs( $s, 'subtitle', wp_strip_all_tags( $s['subtitle'] ?? '' ) );
            ?>
            <?php if ( ! empty( $s['title'] ) ) : ?>
                <div class="olo-sr-title<?php echo $srt_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); colour validated by safe_color_css() whitelist or fixed var() fallback ?>" style="font-weight:600;margin-bottom:8px;color:<?php echo $this->safe_color_css($s['title_color']) ?: 'var(--olo-color-text, #374151)'; ?>;font-size:16px;"<?php echo $srt_data; ?>>
                    <?php echo esc_html( wp_strip_all_tags( $s['title'] ) ); ?>
                </div>
            <?php endif; ?>
            <div style="display:inline-flex;gap:4px;">
                <?php
                // Mezza stella: ceil() restituisce un float e il confronto stretto con
                // l'intero $i non era mai vero, così con 4.5 la quinta stella restava vuota.
                $half_at  = (int) ceil( $rating );
                $has_half = fmod( $rating, 1 ) > 0;
                for ( $i = 1; $i <= $max; $i++ ) :
                    $fill = $i <= floor($rating) ? $clr : $empty;
                    $is_half = $has_half && $i === $half_at;
                    ?>
                    <svg width="<?php echo (int) $size; ?>" height="<?php echo (int) $size; ?>" viewBox="0 0 24 24">
                        <?php if ( $is_half ) :
                            // id legato alla tile: con due valutazioni nella pagina gli id
                            // «olo-half-5» si ripetevano. Col Contorno il primo path aveva due
                            // attributi fill e vinceva il primo: la stella si riempiva.
                            $half_id    = $sr_uid . '-half-' . $i;
                            $half_base  = $is_outline ? 'fill="none" stroke="' . esc_attr( $empty ) . '" stroke-width="1.5"' : 'fill="' . esc_attr( $empty ) . '"';
                            $half_piena = $is_outline ? 'fill="none" stroke="' . esc_attr( $clr ) . '" stroke-width="1.5"' : 'fill="' . esc_attr( $clr ) . '"';
                            ?>
                            <defs><clipPath id="<?php echo esc_attr( $half_id ); ?>"><rect x="0" y="0" width="12" height="24"/></clipPath></defs>
                            <path d="<?php echo $star_d; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $star_d is a hardcoded SVG path literal; $half_base built from esc_attr() colours ?>" <?php echo $half_base; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributi costruiti qui sopra con esc_attr() ?>/>
                            <path d="<?php echo $star_d; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $star_d is a hardcoded SVG path literal ?>" <?php echo $half_piena; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributi costruiti qui sopra con esc_attr() ?> clip-path="url(#<?php echo esc_attr( $half_id ); ?>)"/>
                        <?php else : ?>
                            <path d="<?php echo $star_d; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $star_d is a hardcoded SVG path literal; $fill validated by safe_color_css() whitelist or fixed var() fallbacks ?>" fill="<?php echo $is_outline ? 'none' : $fill; ?>" <?php if ($is_outline) echo 'stroke="' . $fill . '" stroke-width="1.5"'; ?>/>
                        <?php endif; ?>
                    </svg>
                <?php endfor; ?>
            </div>
            <div style="margin-top:4px;font-size:13px;color:<?php echo $clr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- colour validated by safe_color_css() whitelist or fixed var() fallback ?>;font-weight:600;">
                <?php echo esc_html( $rating . ' / ' . $max ); ?>
            </div>
            <?php if ( ! empty( $s['subtitle'] ) ) : ?>
                <div class="olo-sr-subtitle<?php echo $srs_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); colour validated by safe_color_css() whitelist or fixed var() fallback ?>" style="margin-top:4px;font-size:13px;color:<?php echo $this->safe_color_css($s['subtitle_color']) ?: 'var(--olo-color-text-faint, #94a3b8)'; ?>;"<?php echo $srs_data; ?>>
                    <?php echo esc_html( wp_strip_all_tags( $s['subtitle'] ) ); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $sr_uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Text_Effects::css() from whitelisted effects, sanitized colors and integer timings
        $this->tfx_print_script();
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$sr_uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$sr_uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$sr_uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $sr_uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }
}
