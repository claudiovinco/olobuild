<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Overlay_Tile extends Olo_Tile_Base {

    protected $type     = 'overlay';
    protected $name     = 'Overlay';
    protected $icon     = 'dashicons-format-image';
    protected $category = 'media';
    protected $defaults = [
        'image_url'       => '',
        'title'           => 'Project Title',
        'description'     => 'A brief description of this project.',
        'link_url'        => '',
        'link_target'     => '_self',
        'overlay_color'   => '#000000',
        'text_color'      => '#FFFFFF',
        'hover_effect'    => 'fade',
        'overlay_opacity' => '70',
        'border_radius'   => '8',
        'height'          => '300',
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
        $effect = in_array( $s['hover_effect'], [ 'fade', 'slide-up', 'zoom' ], true ) ? $s['hover_effect'] : 'fade';

        $effect_map = [
            'fade'     => 'uk-transition-fade',
            'slide-up' => 'uk-transition-slide-bottom',
            'zoom'     => 'uk-transition-scale-up',
        ];
        $uk_effect = $effect_map[ $effect ] ?? 'uk-transition-fade';

        $tag      = ! empty( $s['link_url'] ) ? 'a' : 'div';
        $link_attr = '';
        if ( ! empty( $s['link_url'] ) ) {
            $link_attr = ' href="' . esc_url( $s['link_url'] ) . '" target="' . esc_attr( $s['link_target'] ) . '" rel="noopener noreferrer" style="text-decoration:none;color:inherit;"';
        }

        ob_start();
        ?>
        <<?php echo $tag; ?> id="<?php echo esc_attr( $id ); ?>"<?php echo $link_attr; ?> class="olo-overlay uk-inline uk-transition-toggle" style="display:block;border-radius:<?php echo $radius_css; ?>;height:<?php echo $h; ?>px;overflow:hidden;cursor:pointer;">
            <?php if ( ! empty( $s['image_url'] ) ) : ?>
                <?php echo Olo_Tile_Utils::img_srcset( absint( $s['image_url_id'] ?? 0 ), $s['image_url'], $s['title'] ?? '', '', 'full', 'uk-cover' ); ?>
            <?php else : ?>
                <div style="background:#374151;" uk-cover></div>
            <?php endif; ?>
            <?php $ov_bg = $this->safe_color( $s['overlay_color'] ); $ov_fg = $this->safe_color( $s['text_color'] ); ?>
            <div class="uk-overlay uk-overlay-primary uk-position-cover <?php echo esc_attr( $uk_effect ); ?>" style="<?php if ( $ov_bg ) echo 'background:' . $ov_bg . ';'; ?>opacity:<?php echo $opa; ?>;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:24px;text-align:center;">
                <div style="<?php if ( $ov_fg ) echo 'color:' . $ov_fg . ';'; ?>">
                    <?php if ( ! empty( $s['title'] ) ) : ?>
                        <div style="font-size:1.5em;font-weight:700;margin-bottom:8px;"><?php echo wp_kses_post( $s['title'] ); ?></div>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['description'] ) ) : ?>
                        <div style="font-size:0.9em;opacity:0.9;line-height:1.5;"><?php echo wp_kses_post( $s['description'] ); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </<?php echo $tag; ?>>
        <?php
        return ob_get_clean();
    }
}
