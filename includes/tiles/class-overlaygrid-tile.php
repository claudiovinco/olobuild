<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_OverlayGrid_Tile extends Olo_Tile_Base {

    protected $type     = 'overlaygrid';
    protected $name     = 'Overlay Grid';
    protected $icon     = 'dashicons-grid-view';
    protected $category = 'interactive';
    protected $defaults = [
        'items' => [
            [ 'id' => 'og-1', 'image' => '', 'title' => 'Elemento 1', 'subtitle' => '', 'link' => '' ],
            [ 'id' => 'og-2', 'image' => '', 'title' => 'Elemento 2', 'subtitle' => '', 'link' => '' ],
            [ 'id' => 'og-3', 'image' => '', 'title' => 'Elemento 3', 'subtitle' => '', 'link' => '' ],
        ],
        'columns'             => '3',
        'columns_mobile'      => '1',
        'gap'                 => 'medium',
        'height'              => '300',
        'match_height'        => true,
        'overlay_position'    => 'bottom',
        'overlay_horizontal'  => 'left',
        'overlay_style'       => 'overlay-primary',
        'overlay_padding'     => 'medium',
        'title_size'          => 'h3',
        'hover_effect'        => 'none',
        'hover_overlay'       => 'always',
        'ribbon_position'     => 'top-right',
        'ribbon_bg'           => '#e11d48',
        'ribbon_color'        => '#ffffff',
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
            [ 'key' => 'items',     'type' => 'slides', 'label' => 'Items' ],
            [ 'key' => 'columns',   'type' => 'select', 'label' => 'Columns' ],
            [ 'key' => 'height',    'type' => 'range',  'label' => 'Height (px)' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $items = is_array( $s['items'] ) ? $s['items'] : [];
        if ( empty( $items ) ) {
            return '<div class="olo-overlaygrid" style="padding:40px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);">No items added</div>';
        }

        $columns   = absint( $s['columns'] ) ?: 3;
        $cols_mob  = absint( $s['columns_mobile'] ) ?: 1;
        $height    = absint( $s['height'] ) ?: 300;
        $position  = esc_attr( $s['overlay_position'] ?: 'bottom' );
        $style     = in_array( $s['overlay_style'], [ 'overlay-primary', 'overlay-default' ], true ) ? $s['overlay_style'] : 'overlay-primary';

        // Gap
        $gap = $s['gap'] ?? 'medium';
        $gap_class = 'uk-grid-' . ( $gap === 'collapse' ? 'collapse' : esc_attr( $gap ) );

        // Match height
        $grid_attr = 'uk-grid';
        if ( ! empty( $s['match_height'] ) ) {
            $grid_attr .= ' uk-height-match';
        }

        // Text alignment
        $text_align = $s['overlay_horizontal'] ?? 'left';
        $text_class = $text_align !== 'left' ? ' uk-text-' . esc_attr( $text_align ) : '';

        // Padding
        $pad = $s['overlay_padding'] ?? 'medium';
        $pad_class = '';
        if ( $pad === 'small' )  $pad_class = ' uk-padding-small';
        if ( $pad === 'large' )  $pad_class = ' uk-padding';

        // Title tag
        $title_tag = in_array( $s['title_size'] ?? 'h3', [ 'h1', 'h2', 'h3', 'h4' ], true ) ? $s['title_size'] : 'h3';

        // Hover effects
        $hover_effect  = $s['hover_effect'] ?? 'none';
        $hover_overlay = $s['hover_overlay'] ?? 'always';

        $img_class = 'mos-og-img';
        if ( $hover_effect !== 'none' ) {
            $img_class .= ' mos-og-hover-' . esc_attr( $hover_effect );
        }

        $overlay_class = '';
        $needs_toggle  = false;
        if ( $hover_overlay !== 'always' ) {
            $needs_toggle  = true;
            $overlay_class = ' uk-transition-' . esc_attr( $hover_overlay );
        }

        // Ribbon
        $ribbon_position = esc_attr( $s['ribbon_position'] ?? 'top-right' );
        $ribbon_bg       = $this->safe_color_css( $s['ribbon_bg'] ?? '#e11d48' );
        $ribbon_color    = $this->safe_color_css( $s['ribbon_color'] ?? '#ffffff' );

        $uid = 'mos-og-' . wp_rand( 10000, 99999 );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> .mos-og-img { transition: transform 0.5s ease, filter 0.5s ease; width: 100%; height: <?php echo $height; ?>px; object-fit: cover; }
            .<?php echo $uid; ?> .mos-og-hover-zoom:hover,
            .<?php echo $uid; ?> .uk-transition-toggle:hover .mos-og-hover-zoom { transform: scale(1.08); }
            .<?php echo $uid; ?> .mos-og-hover-zoom-rotate:hover,
            .<?php echo $uid; ?> .uk-transition-toggle:hover .mos-og-hover-zoom-rotate { transform: scale(1.08) rotate(2deg); }
            .<?php echo $uid; ?> .mos-og-hover-brightness { filter: brightness(0.7); }
            .<?php echo $uid; ?> .mos-og-hover-brightness:hover,
            .<?php echo $uid; ?> .uk-transition-toggle:hover .mos-og-hover-brightness { filter: brightness(1); }
            .<?php echo $uid; ?> .mos-og-hover-desaturate { filter: grayscale(100%); }
            .<?php echo $uid; ?> .mos-og-hover-desaturate:hover,
            .<?php echo $uid; ?> .uk-transition-toggle:hover .mos-og-hover-desaturate { filter: grayscale(0%); }
            .<?php echo $uid; ?> .mos-og-hover-blur-in { filter: blur(3px); }
            .<?php echo $uid; ?> .mos-og-hover-blur-in:hover,
            .<?php echo $uid; ?> .uk-transition-toggle:hover .mos-og-hover-blur-in { filter: blur(0); }
            .<?php echo $uid; ?> .mos-og-ribbon { position: absolute; z-index: 2; font-size: 11px; font-weight: 700; padding: 4px 12px; text-transform: uppercase; letter-spacing: 0.5px; background: <?php echo $ribbon_bg; ?>; color: <?php echo $ribbon_color; ?>; }
            .<?php echo $uid; ?> .mos-og-ribbon--top-right { top: 0; right: 14px; border-radius: 0 0 4px 4px; }
            .<?php echo $uid; ?> .mos-og-ribbon--top-left { top: 0; left: 14px; border-radius: 0 0 4px 4px; }
        </style>
        <div class="olo-overlaygrid <?php echo $uid; ?>">
            <div class="uk-child-width-1-<?php echo $cols_mob; ?> uk-child-width-1-<?php echo $columns; ?>@m <?php echo $gap_class; ?>" <?php echo $grid_attr; ?>>
                <?php foreach ( $items as $item ) :
                    $has_link    = ! empty( $item['link'] );
                    $link_url    = $has_link ? esc_url( $item['link'] ) : '';
                    $toggle_cls  = $needs_toggle ? ' uk-transition-toggle' : '';
                    $wrapper_tag = $has_link ? 'a' : 'div';
                    $wrapper_attr = $has_link
                        ? 'href="' . $link_url . '" class="uk-link-reset uk-display-block' . $toggle_cls . '" style="overflow:hidden;position:relative;" tabindex="0"'
                        : 'class="uk-panel' . $toggle_cls . '" style="overflow:hidden;position:relative;" tabindex="0"';
                ?>
                    <div>
                        <<?php echo $wrapper_tag; ?> <?php echo $wrapper_attr; ?>>
                            <?php if ( ! empty( $item['image'] ) ) : ?>
                                <?php
                                $og_img = Olo_Tile_Utils::img_srcset( absint( $item['image_id'] ?? 0 ), $item['image'], $item['title'] ?? '', $img_class );
                                echo $this->render_hover_wrap( $og_img, $item['hover_image'] ?? '', $item['hover_video'] ?? '' );
                                ?>
                            <?php else : ?>
                                <div style="height:<?php echo $height; ?>px;background:var(--olo-color-secondary, #1F2937);width:100%;"></div>
                            <?php endif; ?>
                            <?php if ( ! empty( $item['ribbon'] ) ) : ?>
                                <span class="mos-og-ribbon mos-og-ribbon--<?php echo $ribbon_position; ?>"><?php echo esc_html( $item['ribbon'] ); ?></span>
                            <?php endif; ?>
                            <?php
                            list( $ogt_cls, $ogt_data ) = $this->tfx_attrs( $s, 'title', $item['title'] ?? '' );
                            list( $ogs_cls, $ogs_data ) = $this->tfx_attrs( $s, 'subtitle', $item['subtitle'] ?? '' );
                            ?>
                            <div class="uk-<?php echo esc_attr( $style ); ?> uk-position-<?php echo $position; ?> uk-panel<?php echo $text_class . $pad_class . $overlay_class; ?>">
                                <<?php echo $title_tag; ?> class="uk-margin-remove<?php echo $ogt_cls; ?>"<?php echo $ogt_data; ?>><?php echo esc_html( $item['title'] ?? '' ); ?></<?php echo $title_tag; ?>>
                                <?php if ( ! empty( $item['subtitle'] ) ) : ?>
                                    <p class="uk-margin-small-top<?php echo $ogs_cls; ?>"<?php echo $ogs_data; ?>><?php echo esc_html( $item['subtitle'] ); ?></p>
                                <?php endif; ?>
                            </div>
                        </<?php echo $wrapper_tag; ?>>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}";
            echo $border_hover_css . $border_effect_css . '</style>';
        }
        return ob_get_clean();
    }
}
