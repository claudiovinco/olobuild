<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Overlay_Tile extends Olobuild_Tile_Base {

    protected $type     = 'overlay';
    protected $name     = 'Overlay';
    protected $icon     = 'dashicons-format-image';
    protected $category = 'media';
    protected $defaults = [
        'image_url'       => '',
        'object_position' => 'center center',
        'title'           => 'Titolo del progetto',
        'description'     => 'Una breve descrizione del progetto.',
        'link_url'        => '',
        'link_target'     => '_self',
        'overlay_color'   => '#000000',
        'text_color'      => '#FFFFFF',
        'hover_effect'    => 'fade',
        'overlay_opacity' => '70',
        'border_radius'   => '8',
        'height'                  => '300',
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
        return [
            [ 'key' => 'image_url',       'type' => 'image',  'label' => 'Image' ],
            [ 'key' => 'title',           'type' => 'text',   'label' => 'Title' ],
            [ 'key' => 'description',     'type' => 'text',   'label' => 'Description' ],
            [ 'key' => 'link_url',        'type' => 'text',   'label' => 'Link URL' ],
            [ 'key' => 'link_target',     'type' => 'select', 'label' => 'Link Target' ],
            [ 'key' => 'overlay_color',   'type' => 'color',  'label' => 'Overlay Color' ],
            [ 'key' => 'text_color',      'type' => 'color',  'label' => 'Text Color' ],
            [ 'key' => 'hover_effect',    'type' => 'select', 'label' => 'Hover Effect' ],
            [ 'key' => 'overlay_opacity', 'type' => 'range',  'label' => 'Overlay Opacity' ],
            [ 'key' => 'border_radius',   'type' => 'range',  'label' => 'Border Radius' ],
            [ 'key' => 'height',          'type' => 'range',  'label' => 'Height' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $id     = 'olo-ov-' . wp_unique_id();
        $h      = absint( $s['height'] );
        $rad_raw = $s['border_radius'];
        if ( is_array( $rad_raw ) ) {
            $radius_css = sprintf( '%dpx %dpx %dpx %dpx', absint( $rad_raw['tl'] ?? 0 ), absint( $rad_raw['tr'] ?? 0 ), absint( $rad_raw['br'] ?? 0 ), absint( $rad_raw['bl'] ?? 0 ) );
        } else {
            $radius_css = absint( $rad_raw ) . 'px';
        }
        $opa    = absint( $s['overlay_opacity'] ) / 100;
        $obj_pos = trim( (string) ( $s['object_position'] ?? 'center center' ) );
        if ( $obj_pos === '' ) {
            $obj_pos = 'center center';
        }
        $effect = in_array( $s['hover_effect'], [ 'fade', 'slide-up', 'zoom' ], true ) ? $s['hover_effect'] : 'fade';

        $effect_map = [
            'fade'     => 'uk-transition-fade',
            'slide-up' => 'uk-transition-slide-bottom',
            'zoom'     => 'uk-transition-scale-up',
        ];
        $uk_effect = $effect_map[ $effect ] ?? 'uk-transition-fade';

        $has_link = ! empty( $s['link_url'] );
        $link_open = '';
        $link_close = '';
        if ( $has_link ) {
            $link_open  = '<a href="' . esc_url( $s['link_url'] ) . '" target="' . esc_attr( $s['link_target'] ) . '" rel="noopener noreferrer" style="display:block;width:100%;text-decoration:none;color:inherit;">';
            $link_close = '</a>';
        }

        ob_start();
        echo $link_open; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- anchor markup assembled above from fixed literals with esc_url()/esc_attr()'d values
        ?>
        <div id="<?php echo esc_attr( $id ); ?>" class="olo-overlay uk-inline uk-transition-toggle" style="display:block;width:100%;box-sizing:border-box;border-radius:<?php echo esc_attr( $radius_css ); ?>;height:<?php echo (int) $h; ?>px;overflow:hidden;cursor:pointer;">
            <?php if ( ! empty( $s['image_url'] ) ) : ?>
                <?php $img_extra = 'uk-cover style="object-position:' . esc_attr( $obj_pos ) . ';"'; ?>
                <?php echo Olobuild_Tile_Utils::img_srcset( absint( $s['image_url_id'] ?? 0 ), $s['image_url'], $s['title'] ?? '', '', 'full', $img_extra ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- <img> markup built by Olobuild_Tile_Utils::img_srcset() with esc_url()/esc_attr() internally; $img_extra contains uk-cover + esc_attr()'d object-position ?>
            <?php else : ?>
                <div style="background:#1F2937;" uk-cover></div>
            <?php endif; ?>
            <?php $ov_bg = $this->safe_color_css( $s['overlay_color'] ); $ov_fg = $this->safe_color_css( $s['text_color'] ); ?>
            <div class="uk-overlay uk-overlay-primary uk-position-cover <?php echo esc_attr( $uk_effect ); ?>" style="<?php if ( $ov_bg ) echo 'background:' . $ov_bg . ';'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- colour validated by safe_color_css() whitelist; opacity is absint()/100 ?>opacity:<?php echo (float) $opa; ?>;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:24px;text-align:center;">
                <div style="<?php if ( $ov_fg ) echo 'color:' . $ov_fg . ';'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- colour validated by safe_color_css() whitelist ?>">
                    <?php
                    list( $ovt_cls, $ovt_data ) = $this->tfx_attrs( $s, 'title', wp_strip_all_tags( $s['title'] ?? '' ) );
                    list( $ovd_cls, $ovd_data ) = $this->tfx_attrs( $s, 'description', wp_strip_all_tags( $s['description'] ?? '' ) );
                    ?>
                    <?php if ( ! empty( $s['title'] ) ) : ?>
                        <div class="olo-overlay-title<?php echo $ovt_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); title is esc_html()'d ?>" style="font-size:1.5em;font-weight:700;margin-bottom:8px;"<?php echo $ovt_data; ?>><?php echo esc_html( wp_strip_all_tags( $s['title'] ) ); ?></div>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['description'] ) ) : ?>
                        <div class="olo-overlay-desc<?php echo $ovd_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); description is esc_html()'d (nl2br only adds <br /> tags) ?>" style="font-size:0.9em;opacity:0.9;line-height:1.5;"<?php echo $ovd_data; ?>><?php echo nl2br( esc_html( wp_strip_all_tags( $s['description'] ) ) ); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
        echo $link_close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed literal '</a>' or empty string
        ?>
        <?php
        $tfx_css = $this->tfx_css( $s, '#' . $id );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Text_Effects::css() from whitelisted effects, sanitized colors and integer timings
        $this->tfx_print_script();

        // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( "#{$id}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( "#{$id}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo "#{$id}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $id is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }

        return ob_get_clean();
    }
}
