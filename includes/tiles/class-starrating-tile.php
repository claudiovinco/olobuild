<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Olo_Starrating_Tile extends Olo_Tile_Base {
    protected $type     = 'starrating';
    protected $name     = 'Valutazione';
    protected $icon     = 'dashicons-star-filled';
    protected $category = 'marketing';
    protected $defaults = [
        'rating'         => '4',
        'max_stars'      => '5',
        'star_size'      => '32',
        'star_color'     => '#FBBF24',
        'empty_color'    => '#4B5563',
        'style'          => 'filled',
        'title'          => '',
        'subtitle'       => '',
        'title_color'    => '#F3F4F6',
        'subtitle_color' => '#9CA3AF',
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
        $clr    = $this->safe_color_css( $s['star_color'] ) ?: '#FBBF24';
        $empty  = $this->safe_color_css( $s['empty_color'] ) ?: '#4B5563';
        $align  = in_array( $s['alignment'], ['left','center','right'], true ) ? $s['alignment'] : 'center';
        $is_outline = $s['style'] === 'outline';
        $star_d = 'M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z';

        ob_start();
        ?>
        <?php $sr_uid = 'olo-sr-' . wp_unique_id(); ?>
        <div class="olo-starrating <?php echo $sr_uid; ?>" style="text-align:<?php echo $align; ?>;padding:16px;">
            <?php
            list( $srt_cls, $srt_data ) = $this->tfx_attrs( $s, 'title', wp_strip_all_tags( $s['title'] ?? '' ) );
            list( $srs_cls, $srs_data ) = $this->tfx_attrs( $s, 'subtitle', wp_strip_all_tags( $s['subtitle'] ?? '' ) );
            ?>
            <?php if ( ! empty( $s['title'] ) ) : ?>
                <div class="olo-sr-title<?php echo $srt_cls; ?>" style="font-weight:600;margin-bottom:8px;color:<?php echo $this->safe_color_css($s['title_color']) ?: '#F3F4F6'; ?>;font-size:16px;"<?php echo $srt_data; ?>>
                    <?php echo esc_html( wp_strip_all_tags( $s['title'] ) ); ?>
                </div>
            <?php endif; ?>
            <div style="display:inline-flex;gap:4px;">
                <?php for ( $i = 1; $i <= $max; $i++ ) :
                    $fill = $i <= floor($rating) ? $clr : $empty;
                    $is_half = ($i === ceil($rating)) && (fmod($rating, 1) !== 0.0);
                    ?>
                    <svg width="<?php echo $size; ?>" height="<?php echo $size; ?>" viewBox="0 0 24 24">
                        <?php if ( $is_half ) : ?>
                            <defs><clipPath id="olo-half-<?php echo $i; ?>"><rect x="0" y="0" width="12" height="24"/></clipPath></defs>
                            <path d="<?php echo $star_d; ?>" fill="<?php echo $empty; ?>" <?php if ($is_outline) echo 'stroke="' . $empty . '" stroke-width="1.5" fill="none"'; ?>/>
                            <path d="<?php echo $star_d; ?>" fill="<?php echo $clr; ?>" clip-path="url(#olo-half-<?php echo $i; ?>)"/>
                        <?php else : ?>
                            <path d="<?php echo $star_d; ?>" fill="<?php echo $is_outline ? 'none' : $fill; ?>" <?php if ($is_outline) echo 'stroke="' . $fill . '" stroke-width="1.5"'; ?>/>
                        <?php endif; ?>
                    </svg>
                <?php endfor; ?>
            </div>
            <div style="margin-top:4px;font-size:13px;color:<?php echo $clr; ?>;font-weight:600;">
                <?php echo esc_html( $rating . ' / ' . $max ); ?>
            </div>
            <?php if ( ! empty( $s['subtitle'] ) ) : ?>
                <div class="olo-sr-subtitle<?php echo $srs_cls; ?>" style="margin-top:4px;font-size:13px;color:<?php echo $this->safe_color_css($s['subtitle_color']) ?: '#9CA3AF'; ?>;"<?php echo $srs_data; ?>>
                    <?php echo esc_html( wp_strip_all_tags( $s['subtitle'] ) ); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $sr_uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$sr_uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$sr_uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$sr_uid}{{$border_css}}";
            echo $border_hover_css . $border_effect_css . '</style>';
        }
        return ob_get_clean();
    }
}
