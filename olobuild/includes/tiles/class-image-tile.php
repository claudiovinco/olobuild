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

        ob_start();

        if ( $filter_css || $hover_filter_css || $hover_transform || $anim === 'blur-in' ) {
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
            echo '</style>';
        }
        ?>
        <figure class="olo-image <?php echo esc_attr( $uid ); ?>"<?php if ( ! empty( $s['lightbox'] ) && empty( $s['link_url'] ) ) echo ' data-uk-lightbox'; ?> style="margin: 0;">
            <?php
            $att_id = absint( $s['image_url_id'] ?? 0 );
            $extra  = 'uk-img style="width: 100%; height: ' . esc_attr( $s['height'] ) . '; object-fit: ' . esc_attr( $s['object_fit'] ) . '; display: block;"';
            $img    = Olo_Tile_Utils::img_srcset( $att_id, $s['image_url'], $s['alt_text'], 'uk-border-rounded', 'full', $extra );

            $img = $this->render_hover_wrap( $img, $s['hover_image'] ?? '', $s['hover_video'] ?? '' );

            if ( ! empty( $s['link_url'] ) ) {
                printf(
                    '<a href="%s" target="%s" style="display: block;">%s</a>',
                    esc_url( $s['link_url'] ),
                    esc_attr( $s['link_target'] ),
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
                <figcaption style="padding: 8px 0; font-size: 0.875em; color: var(--olo-color-text-muted, #9CA3AF); text-align: center;">
                    <?php echo esc_html( wp_strip_all_tags( $s['caption'] ) ); ?>
                </figcaption>
            <?php endif; ?>
        </figure>
        <?php
        return ob_get_clean();
    }
}
