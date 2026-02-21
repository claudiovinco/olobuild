<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Content_Tile extends Olo_Tile_Base {

    protected $type     = 'content';
    protected $name     = 'Contenuto';
    protected $icon     = 'dashicons-text-page';
    protected $category = 'content';
    protected $defaults = [
        'heading'            => 'Section Title',
        'text'               => 'Add your content here. This is a simple text block that you can customize.',
        'image'              => '',
        'image_position'     => 'top',
        'image_width'        => '40',
        'image_height'       => 'auto',
        'image_fit'          => 'cover',
        'image_radius'       => '0',
        'image_border_width' => '0',
        'image_border_color' => '#e5e7eb',
        'image_shadow'       => 'none',
        'image_gap'          => '16',
        'hover_effect'       => 'none',
        'hover_image'        => '',
        'hover_video'        => '',
        'link_url'           => '',
        'link_target'        => '_self',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'heading', 'type' => 'text',     'label' => 'Heading' ],
            [ 'key' => 'text',    'type' => 'textarea', 'label' => 'Content' ],
            [ 'key' => 'image',   'type' => 'image',    'label' => 'Image' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $uid = 'olo-ct-' . wp_rand( 10000, 99999 );

        $position     = in_array( $s['image_position'], [ 'top', 'bottom', 'left', 'right' ], true ) ? $s['image_position'] : 'top';
        $is_horizontal = in_array( $position, [ 'left', 'right' ], true );
        $image_width  = max( 20, min( 80, absint( $s['image_width'] ) ) );
        $image_height = $s['image_height'];
        $image_fit    = in_array( $s['image_fit'], [ 'cover', 'contain', 'fill' ], true ) ? $s['image_fit'] : 'cover';
        $image_radius = absint( $s['image_radius'] );
        $border_width = absint( $s['image_border_width'] );
        $border_color = $this->safe_color( $s['image_border_color'] ?? '#e5e7eb' );
        $image_gap    = absint( $s['image_gap'] );
        $hover_effect = $s['hover_effect'] ?? 'none';
        $link_url     = $s['link_url'] ?? '';
        $link_target  = $s['link_target'] === '_blank' ? '_blank' : '_self';

        // Shadow map
        $shadow_map = [
            'none' => 'none',
            'sm'   => '0 1px 2px rgba(0,0,0,.05)',
            'md'   => '0 4px 6px rgba(0,0,0,.1)',
            'lg'   => '0 10px 15px rgba(0,0,0,.1)',
            'xl'   => '0 20px 25px rgba(0,0,0,.1)',
        ];
        $shadow = $shadow_map[ $s['image_shadow'] ] ?? 'none';

        // Image CSS class
        $img_class = 'olo-ct-img';
        if ( $hover_effect !== 'none' ) {
            $img_class .= ' olo-ct-hover-' . esc_attr( $hover_effect );
        }

        // Height CSS
        $height_css = 'auto';
        if ( ! empty( $image_height ) && $image_height !== 'auto' ) {
            $height_css = is_numeric( $image_height ) ? $image_height . 'px' : esc_attr( $image_height );
        }

        // UIkit grid fractions for horizontal layout
        $uk_fractions = [
            20 => '1-5', 25 => '1-4', 30 => '3-10', 33 => '1-3', 35 => '7-20',
            40 => '2-5', 45 => '9-20', 50 => '1-2', 55 => '11-20', 60 => '3-5',
            65 => '13-20', 66 => '2-3', 70 => '7-10', 75 => '3-4', 80 => '4-5',
        ];
        // Find closest fraction
        $img_fraction  = $uk_fractions[ $image_width ] ?? null;
        $text_width_pct = 100 - $image_width;
        $text_fraction = $uk_fractions[ $text_width_pct ] ?? null;

        // Fallback: use custom width if no UIkit fraction matches
        $use_custom_widths = ( $img_fraction === null || $text_fraction === null );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> .olo-ct-img {
                transition: transform 0.5s ease, filter 0.5s ease;
                width: 100%;
                display: block;
                height: <?php echo $height_css; ?>;
                object-fit: <?php echo $image_fit; ?>;
                border-radius: <?php echo $image_radius; ?>px;
                <?php if ( $border_width > 0 ) : ?>
                border: <?php echo $border_width; ?>px solid <?php echo $border_color; ?>;
                <?php endif; ?>
                <?php if ( $shadow !== 'none' ) : ?>
                box-shadow: <?php echo $shadow; ?>;
                <?php endif; ?>
            }
            <?php if ( $hover_effect !== 'none' ) : ?>
            .<?php echo $uid; ?>:hover .olo-ct-hover-zoom { transform: scale(1.08); }
            .<?php echo $uid; ?>:hover .olo-ct-hover-zoom-rotate { transform: scale(1.08) rotate(2deg); }
            .<?php echo $uid; ?> .olo-ct-hover-brightness { filter: brightness(0.7); }
            .<?php echo $uid; ?>:hover .olo-ct-hover-brightness { filter: brightness(1); }
            .<?php echo $uid; ?> .olo-ct-hover-desaturate { filter: grayscale(100%); }
            .<?php echo $uid; ?>:hover .olo-ct-hover-desaturate { filter: grayscale(0%); }
            .<?php echo $uid; ?> .olo-ct-hover-blur-in { filter: blur(3px); }
            .<?php echo $uid; ?>:hover .olo-ct-hover-blur-in { filter: blur(0); }
            <?php endif; ?>
            <?php if ( $is_horizontal && $use_custom_widths ) : ?>
            .<?php echo $uid; ?> .olo-ct-img-col { width: <?php echo $image_width; ?>%; }
            .<?php echo $uid; ?> .olo-ct-text-col { width: <?php echo $text_width_pct; ?>%; }
            <?php endif; ?>
        </style>

        <div class="olo-content <?php echo $uid; ?> uk-panel" style="padding: 40px;">
        <?php if ( $is_horizontal ) : ?>
            <?php
            // Horizontal layout: uk-grid with two columns
            $img_col_cls  = $use_custom_widths ? 'olo-ct-img-col' : 'uk-width-' . $img_fraction . '@m';
            $text_col_cls = $use_custom_widths ? 'olo-ct-text-col' : 'uk-width-' . $text_fraction . '@m';
            $order_cls    = ( $position === 'right' ) ? ' uk-flex-last@m' : '';
            ?>
            <div class="uk-grid" uk-grid style="column-gap: <?php echo $image_gap; ?>px;">
                <div class="<?php echo esc_attr( $img_col_cls . $order_cls ); ?>">
                    <?php $this->render_image_block( $s, $img_class, $link_url, $link_target ); ?>
                </div>
                <div class="<?php echo esc_attr( $text_col_cls ); ?>">
                    <h2 class="uk-heading-small" style="margin-bottom: 0.5em;"><?php echo wp_kses_post( $s['heading'] ); ?></h2>
                    <div><?php echo wp_kses_post( $s['text'] ); ?></div>
                </div>
            </div>
        <?php else : ?>
            <?php
            // Vertical layout: top or bottom
            $is_bottom = ( $position === 'bottom' );
            ?>
            <?php if ( ! $is_bottom ) : ?>
                <?php if ( ! empty( $s['image'] ) ) : ?>
                    <div style="margin-bottom: <?php echo $image_gap; ?>px; overflow: hidden; border-radius: <?php echo $image_radius; ?>px;">
                        <?php $this->render_image_block( $s, $img_class, $link_url, $link_target ); ?>
                    </div>
                <?php endif; ?>
                <h2 class="uk-heading-small" style="margin-bottom: 0.5em;"><?php echo wp_kses_post( $s['heading'] ); ?></h2>
                <div><?php echo wp_kses_post( $s['text'] ); ?></div>
            <?php else : ?>
                <h2 class="uk-heading-small" style="margin-bottom: 0.5em;"><?php echo wp_kses_post( $s['heading'] ); ?></h2>
                <div><?php echo wp_kses_post( $s['text'] ); ?></div>
                <?php if ( ! empty( $s['image'] ) ) : ?>
                    <div style="margin-top: <?php echo $image_gap; ?>px; overflow: hidden; border-radius: <?php echo $image_radius; ?>px;">
                        <?php $this->render_image_block( $s, $img_class, $link_url, $link_target ); ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private function render_image_block( $s, $img_class, $link_url, $link_target ) {
        if ( empty( $s['image'] ) ) {
            return;
        }

        $img_html = '<img src="' . esc_url( $s['image'] ) . '" alt="" class="' . esc_attr( $img_class ) . '" loading="lazy">';
        $img_html = $this->render_hover_wrap( $img_html, $s['hover_image'] ?? '', $s['hover_video'] ?? '' );

        if ( ! empty( $link_url ) ) {
            $target_attr = $link_target === '_blank' ? ' target="_blank" rel="noopener noreferrer"' : '';
            echo '<a href="' . esc_url( $link_url ) . '"' . $target_attr . '>' . $img_html . '</a>';
        } else {
            echo $img_html;
        }
    }
}
