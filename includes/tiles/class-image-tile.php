<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Image_Tile extends Olo_Tile_Base {

    protected $type     = 'image';
    protected $name     = 'Immagine';
    protected $icon     = 'dashicons-format-image';
    protected $category = 'essential';
    protected $defaults = [
        'image_url'   => '',
        'hover_image' => '',
        'hover_video' => '',
        'alt_text'    => '',
        'caption'     => '',
        'link_url'    => '',
        'link_target' => '_self',
        'object_fit'  => 'cover',
        'height'      => '300px',
        'filter_blur'       => '0',
        'filter_brightness' => '100',
        'filter_contrast'   => '100',
        'filter_saturate'   => '100',
        'filter_grayscale'  => '0',
        'filter_sepia'      => '0',
        'hover_filter_blur'       => '',
        'hover_filter_brightness' => '',
        'hover_filter_contrast'   => '',
        'hover_filter_saturate'   => '',
        'hover_filter_grayscale'  => '',
        'hover_filter_sepia'      => '',
        'hover_animation'  => 'none',
        'lightbox'         => false,
        'border_radius'    => '0',
        'hover_border_radius' => '',
        'hover_radius_duration' => '400',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'image_url',   'type' => 'image',  'label' => 'Image' ],
            [ 'key' => 'alt_text',    'type' => 'text',   'label' => 'Alt Text' ],
            [ 'key' => 'caption',     'type' => 'text',   'label' => 'Caption' ],
            [ 'key' => 'link_url',    'type' => 'text',   'label' => 'Link URL' ],
            [ 'key' => 'link_target', 'type' => 'select', 'label' => 'Link Target', 'options' => [ '_self' => 'Same Window', '_blank' => 'New Tab' ] ],
            [ 'key' => 'object_fit',  'type' => 'select', 'label' => 'Fit Mode', 'options' => [ 'cover' => 'Cover', 'contain' => 'Contain', 'fill' => 'Fill' ] ],
            [ 'key' => 'height',      'type' => 'text',   'label' => 'Height' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $uid = 'olo-img-' . wp_rand( 10000, 99999 );

        // Build CSS filter string
        $filters = [];
        $blur = absint( $s['filter_blur'] ?? 0 );
        $brightness = absint( $s['filter_brightness'] ?? 100 );
        $contrast = absint( $s['filter_contrast'] ?? 100 );
        $saturate = absint( $s['filter_saturate'] ?? 100 );
        $grayscale = absint( $s['filter_grayscale'] ?? 0 );
        $sepia = absint( $s['filter_sepia'] ?? 0 );
        if ( $blur > 0 ) $filters[] = "blur({$blur}px)";
        if ( $brightness !== 100 ) $filters[] = "brightness({$brightness}%)";
        if ( $contrast !== 100 ) $filters[] = "contrast({$contrast}%)";
        if ( $saturate !== 100 ) $filters[] = "saturate({$saturate}%)";
        if ( $grayscale > 0 ) $filters[] = "grayscale({$grayscale}%)";
        if ( $sepia > 0 ) $filters[] = "sepia({$sepia}%)";
        $filter_css = $filters ? implode( ' ', $filters ) : '';

        // Hover filters
        $hover_filters = [];
        $hblur = $s['hover_filter_blur'] ?? '';
        $hbright = $s['hover_filter_brightness'] ?? '';
        $hcontrast = $s['hover_filter_contrast'] ?? '';
        $hsat = $s['hover_filter_saturate'] ?? '';
        $hgray = $s['hover_filter_grayscale'] ?? '';
        $hsepia = $s['hover_filter_sepia'] ?? '';
        if ( $hblur !== '' ) $hover_filters[] = 'blur(' . absint($hblur) . 'px)';
        if ( $hbright !== '' ) $hover_filters[] = 'brightness(' . absint($hbright) . '%)';
        if ( $hcontrast !== '' ) $hover_filters[] = 'contrast(' . absint($hcontrast) . '%)';
        if ( $hsat !== '' ) $hover_filters[] = 'saturate(' . absint($hsat) . '%)';
        if ( $hgray !== '' ) $hover_filters[] = 'grayscale(' . absint($hgray) . '%)';
        if ( $hsepia !== '' ) $hover_filters[] = 'sepia(' . absint($hsepia) . '%)';
        $hover_filter_css = $hover_filters ? implode( ' ', $hover_filters ) : '';

        // Hover animation
        $anim = $s['hover_animation'] ?? 'none';
        $hover_transform = '';
        switch ( $anim ) {
            case 'zoom-in':    $hover_transform = 'scale(1.08)'; break;
            case 'zoom-out':   $hover_transform = 'scale(1)'; break;
            case 'slide-up':   $hover_transform = 'translateY(-5px)'; break;
            case 'rotate-cw':  $hover_transform = 'rotate(2deg) scale(1.02)'; break;
            case 'rotate-ccw': $hover_transform = 'rotate(-2deg) scale(1.02)'; break;
        }
        $init_transform = $anim === 'zoom-out' ? 'transform:scale(1.05);' : '';

        // Border radius (base + optional hover — image_tile uses legacy `hover_border_radius` key,
        // not the canonical `*_hover` convention used elsewhere)
        $br_css        = $this->build_border_radius_css( $s['border_radius'] ?? '0' );
        $hover_br_raw  = $s['hover_border_radius'] ?? '';
        // hover is "set" whenever the user touched the field — i.e. it's an object
        // (4 sides) or a non-empty scalar. All-zero values must still apply (override base).
        $has_hover_br  = is_array( $hover_br_raw ) || ( $hover_br_raw !== '' && $hover_br_raw !== null );
        $hover_br_css  = '';
        if ( $has_hover_br ) {
            if ( is_array( $hover_br_raw ) ) {
                $h_tl = intval( $hover_br_raw['tl'] ?? 0 );
                $h_tr = intval( $hover_br_raw['tr'] ?? 0 );
                $h_br = intval( $hover_br_raw['br'] ?? 0 );
                $h_bl = intval( $hover_br_raw['bl'] ?? 0 );
                $hover_br_css = "{$h_tl}px {$h_tr}px {$h_br}px {$h_bl}px";
            } else {
                $h_n = max( 0, intval( $hover_br_raw ) );
                $hover_br_css = "{$h_n}px";
            }
        }
        $br_duration   = max( 50, intval( $s['hover_radius_duration'] ?? 400 ) );

        ob_start();

        if ( $filter_css || $hover_filter_css || $hover_transform || $anim === 'blur-in' || $has_hover_br ) {
            echo '<style>';
            echo ".{$uid} img { transition: filter 0.4s ease, transform 0.4s ease;";
            if ( $filter_css ) echo "filter:{$filter_css};";
            if ( $init_transform ) echo $init_transform;
            echo '}';
            if ( $hover_filter_css || $hover_transform ) {
                echo ".{$uid}:hover img {";
                if ( $hover_filter_css ) echo "filter:{$hover_filter_css};";
                if ( $hover_transform ) echo "transform:{$hover_transform};";
                echo '}';
            }
            if ( $anim === 'blur-in' ) {
                echo ".{$uid} img { filter:" . ($filter_css ? $filter_css . ' ' : '') . "blur(3px); }";
                echo ".{$uid}:hover img { filter:" . ($filter_css ?: '') . "blur(0); }";
            }
            // Hover border-radius — applied to the figure wrapper (which has overflow:hidden + base radius)
            if ( $has_hover_br ) {
                echo ".{$uid}.olo-image{transition:border-radius {$br_duration}ms cubic-bezier(.4,0,.2,1);}";
                echo ".{$uid}.olo-image:hover{border-radius:{$hover_br_css}!important;}";
            }
            echo '</style>';
        }
        ?>
        <?php
        $figure_style = 'margin: 0;';
        if ( $br_css ) {
            $figure_style .= ' border-radius: ' . esc_attr( $br_css ) . '; overflow: hidden;';
        } elseif ( $has_hover_br ) {
            // Need overflow:hidden + explicit border-radius:0 for the hover transition to start from 0 and clip the image
            $figure_style .= ' border-radius: 0; overflow: hidden;';
        }
        ?>
        <figure class="olo-image <?php echo esc_attr( $uid ); ?>"<?php if ( ! empty( $s['lightbox'] ) && empty( $s['link_url'] ) ) echo ' data-uk-lightbox'; ?> style="<?php echo esc_attr( $figure_style ); ?>">
            <?php
            $att_id = absint( $s['image_url_id'] ?? 0 );
            // No border-radius on the <img>: figure has overflow:hidden + radius which clips correctly.
            // Applying radius on both could conflict when the figure changes radius on :hover (the
            // intersection of two different rounded shapes can leave residual sharp corners).
            $img_style = 'width: 100%; height: ' . esc_attr( $s['height'] ) . '; object-fit: ' . esc_attr( $s['object_fit'] ) . '; display: block;';
            $extra  = 'uk-img style="' . $img_style . '"';
            $img_opts = [];
            if ( ! empty( $s['_img_loading'] ) ) $img_opts['loading'] = $s['_img_loading'];
            if ( ! empty( $s['_fetch_priority'] ) ) $img_opts['fetchpriority'] = $s['_fetch_priority'];
            $img    = Olo_Tile_Utils::img_srcset( $att_id, $s['image_url'], $s['alt_text'], 'uk-border-rounded', 'full', $extra, $img_opts );

            $img = $this->render_hover_wrap( $img, $s['hover_image'] ?? '', $s['hover_video'] ?? '' );

            if ( ! empty( $s['link_url'] ) ) {
                $link_rel = ! empty( $s['_link_rel'] ) ? ' rel="' . esc_attr( $s['_link_rel'] ) . '"' : '';
                $link_title = ! empty( $s['_link_title'] ) ? ' title="' . esc_attr( $s['_link_title'] ) . '"' : '';
                printf(
                    '<a href="%s" target="%s"%s%s style="display: block;">%s</a>',
                    esc_url( $s['link_url'] ),
                    esc_attr( $s['link_target'] ),
                    $link_rel,
                    $link_title,
                    $img
                );
            } elseif ( ! empty( $s['lightbox'] ) ) {
                printf(
                    '<a href="%s" style="display: block;">%s</a>',
                    esc_url( $s['image_url'] ),
                    $img
                );
            } else {
                echo $img;
            }
            ?>
            <?php if ( ! empty( $s['caption'] ) ) : ?>
                <?php list( $ic_cls, $ic_data ) = $this->tfx_attrs( $s, 'caption', wp_strip_all_tags( $s['caption'] ) ); ?>
                <figcaption class="olo-img-caption<?php echo $ic_cls; ?>" style="padding: 8px 0; font-size: 0.875em; color: var(--olo-color-text-muted, #9CA3AF); text-align: center;"<?php echo $ic_data; ?>>
                    <?php echo esc_html( wp_strip_all_tags( $s['caption'] ) ); ?>
                </figcaption>
            <?php endif; ?>
        </figure>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();
        return ob_get_clean();
    }
}
