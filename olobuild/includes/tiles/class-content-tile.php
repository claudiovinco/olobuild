<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Content_Tile extends Olo_Tile_Base {

    protected $type     = 'content';
    protected $name     = 'Contenuto';
    protected $icon     = 'dashicons-text-page';
    protected $category = 'essential';
    protected $defaults = [
        'heading'            => 'Titolo sezione',
        'heading_tag'        => 'h2',
        'heading_size'       => 'md',
        'heading_color'      => '',
        'text'               => 'Aggiungi il tuo contenuto qui.',
        'text_color'         => '',
        'image'              => '',
        'image_position'     => 'top',
        'image_width'        => '40',
        'image_height'       => 'auto',
        'image_fit'          => 'cover',
        'image_radius'       => '0',
        'image_border_width' => '0',
        'image_border_color' => '',
        'image_shadow'       => 'none',
        'heading_gap'        => '8',
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
            [ 'key' => 'text',    'type' => 'editor',   'label' => 'Content' ],
            [ 'key' => 'image',   'type' => 'image',    'label' => 'Image' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $uid = 'olo-ct-' . wp_rand( 10000, 99999 );

        // Heading tag/size/color
        $allowed_tags = [ 'h2', 'h3', 'h4', 'h5', 'p' ];
        $htag = in_array( $s['heading_tag'] ?? 'h2', $allowed_tags, true ) ? $s['heading_tag'] : 'h2';

        $size_px_map = [ 'sm' => '1.25rem', 'md' => '1.7rem', 'lg' => '2.3rem', 'xl' => '3rem' ];
        $base_size   = $s['heading_size'] ?? 'md';
        $font_size   = $size_px_map[ $base_size ] ?? '1.7rem';

        $hd_clr = $this->safe_color_css( $s['heading_color'] ?? '' );
        $hstyle = 'margin:0 0 ' . absint( $s['heading_gap'] ?? 8 ) . 'px 0;font-weight:bold;font-size:' . $font_size . ';';
        if ( $hd_clr ) { $hstyle .= 'color:' . $hd_clr . ';'; }

        // Text color
        $txt_clr = $this->safe_color_css( $s['text_color'] ?? '' );
        $txt_style = '';
        if ( $txt_clr ) { $txt_style = 'color:' . $txt_clr . ';'; }

        // Heading (plain text, no HTML)
        $heading_text = esc_html( wp_strip_all_tags( $s['heading'] ) );
        // Text content: supporta sia plain text (legacy) che HTML (da RichTextEditor)
        $text_raw = $s['text'] ?? '';
        if ( preg_match( '/^\s*</', $text_raw ) ) {
            $text_content = wp_kses_post( $text_raw );
        } else {
            $text_content = nl2br( esc_html( $text_raw ) );
        }

        $position     = $this->validate_position( $s['image_position'] ?? 'top' );
        $image_width  = max( 20, min( 80, absint( $s['image_width'] ) ) );
        $image_height = $s['image_height'];
        $image_fit    = in_array( $s['image_fit'], [ 'cover', 'contain', 'fill' ], true ) ? $s['image_fit'] : 'cover';
        $image_radius = Olo_Tile_Utils::border_radius( $s['image_radius'] ?? 0 );
        $border_width = absint( $s['image_border_width'] );
        $border_color = $this->safe_color_css( $s['image_border_color'] ) ?: 'var(--olo-color-border, #E5E7EB)';
        $image_gap    = absint( $s['image_gap'] );
        $hover_effect = $s['hover_effect'] ?? 'none';
        $link_url     = $s['link_url'] ?? '';
        $link_target  = $s['link_target'] === '_blank' ? '_blank' : '_self';

        // Shadow
        $shadow = Olo_Tile_Utils::shadow( $s['image_shadow'] ?? 'none' );

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

        // Flex direction map
        $dir_map = [ 'top' => 'column', 'bottom' => 'column-reverse', 'left' => 'row', 'right' => 'row-reverse' ];
        $is_hz   = in_array( $position, [ 'left', 'right' ], true );

        // Responsive breakpoint overrides for image_position
        $bp_map = [
            'tablet_landscape' => 1200,
            'tablet'           => 960,
            'mobile_landscape' => 640,
            'mobile'           => 480,
        ];

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> .olo-ct-layout {
                display: flex;
                flex-direction: <?php echo $dir_map[ $position ]; ?>;
                gap: <?php echo $image_gap; ?>px;
                <?php if ( $is_hz ) : ?>align-items: flex-start;<?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-ct-img-col {
                overflow: hidden;
                border-radius: <?php echo $image_radius; ?>;
                <?php if ( $is_hz ) : ?>
                width: <?php echo $image_width; ?>%;
                flex-shrink: 0;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-ct-text {
                <?php if ( $is_hz ) : ?>flex: 1; min-width: 0;<?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-ct-img {
                transition: transform 0.5s ease, filter 0.5s ease;
                width: 100%;
                display: block;
                height: <?php echo $height_css; ?>;
                object-fit: <?php echo $image_fit; ?>;
                border-radius: <?php echo $image_radius; ?>;
                <?php if ( $border_width > 0 ) : ?>
                border: <?php echo $border_width; ?>px solid <?php echo $border_color; ?>;
                <?php endif; ?>
            }
            <?php if ( $shadow !== 'none' ) : ?>
            .<?php echo $uid; ?> .olo-ct-img-col {
                box-shadow: <?php echo $shadow; ?>;
            }
            <?php endif; ?>
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
            <?php
            // Responsive heading_size overrides
            foreach ( $bp_map as $bp => $max_w ) :
                $sz_key = 'heading_size_' . $bp;
                if ( ! empty( $s[ $sz_key ] ) ) :
                    $bp_font = $size_px_map[ $s[ $sz_key ] ] ?? '';
                    if ( $bp_font ) :
            ?>
            @media (max-width: <?php echo $max_w; ?>px) {
                .<?php echo $uid; ?> .olo-ct-heading { font-size: <?php echo $bp_font; ?>; }
            }
            <?php
                    endif;
                endif;
            endforeach;

            // Responsive image_position overrides
            foreach ( $bp_map as $bp => $max_w ) :
                $pos_key = 'image_position_' . $bp;
                if ( ! empty( $s[ $pos_key ] ) ) :
                    $bp_pos = $this->validate_position( $s[ $pos_key ] );
                    $bp_hz  = in_array( $bp_pos, [ 'left', 'right' ], true );
            ?>
            @media (max-width: <?php echo $max_w; ?>px) {
                .<?php echo $uid; ?> .olo-ct-layout {
                    flex-direction: <?php echo $dir_map[ $bp_pos ]; ?>;
                    <?php if ( $bp_hz ) : ?>align-items: flex-start;<?php else : ?>align-items: stretch;<?php endif; ?>
                }
                .<?php echo $uid; ?> .olo-ct-img-col {
                    <?php if ( $bp_hz ) : ?>
                    width: <?php echo $image_width; ?>%;
                    flex-shrink: 0;
                    <?php else : ?>
                    width: auto;
                    flex-shrink: initial;
                    <?php endif; ?>
                }
                .<?php echo $uid; ?> .olo-ct-text {
                    <?php if ( $bp_hz ) : ?>flex: 1; min-width: 0;<?php else : ?>flex: initial; min-width: initial;<?php endif; ?>
                }
            }
            <?php
                endif;
            endforeach;
            ?>
        </style>

        <div class="olo-content <?php echo $uid; ?> uk-panel">
            <div class="olo-ct-layout">
                <?php if ( ! empty( $s['image'] ) ) : ?>
                <div class="olo-ct-img-col">
                    <?php $this->render_image_block( $s, $img_class, $link_url, $link_target ); ?>
                </div>
                <?php endif; ?>
                <div class="olo-ct-text">
                    <<?php echo $htag; ?> class="olo-ct-heading" style="<?php echo $hstyle; ?>"><?php echo $heading_text; ?></<?php echo $htag; ?>>
                    <div<?php if ( $txt_style ) echo ' style="' . $txt_style . '"'; ?>><?php echo $text_content; ?></div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function validate_position( $pos ) {
        return in_array( $pos, [ 'top', 'bottom', 'left', 'right' ], true ) ? $pos : 'top';
    }

    private function render_image_block( $s, $img_class, $link_url, $link_target ) {
        if ( empty( $s['image'] ) ) {
            return;
        }

        $att_id   = absint( $s['image_id'] ?? 0 );
        $img_html = Olo_Tile_Utils::img_srcset( $att_id, $s['image'], wp_strip_all_tags( $s['title'] ?? '' ), $img_class );
        $img_html = $this->render_hover_wrap( $img_html, $s['hover_image'] ?? '', $s['hover_video'] ?? '' );

        if ( ! empty( $link_url ) ) {
            $target_attr = $link_target === '_blank' ? ' target="_blank" rel="noopener noreferrer"' : '';
            echo '<a href="' . esc_url( $link_url ) . '"' . $target_attr . '>' . $img_html . '</a>';
        } else {
            echo $img_html;
        }
    }
}
