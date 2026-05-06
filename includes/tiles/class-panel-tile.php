<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Panel_Tile extends Olo_Tile_Base {

    protected $type     = 'panel';
    protected $name     = 'Pannello';
    protected $icon     = 'dashicons-id-alt';
    protected $category = 'interactive';
    protected $defaults = [
        'style'          => 'default',
        'title'          => 'Panel Title',
        'meta'           => 'Written by Author',
        'content'        => 'Panel content goes here. Add your text, images, or any other content.',
        'media_type'     => 'image',
        'image'          => '',
        'image_ratio'    => 'auto',
        'image_height'   => '',
        'image_fit'      => 'cover',
        'image_zoom'     => false,
        'media_padding'  => [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 ],
        'text_align'     => 'left',
        'title_size'     => '',
        'title_weight'   => '',
        'title_color'    => '',
        'meta_size'      => '',
        'meta_color'     => '',
        'content_size'   => '',
        'content_color'  => '',
        'link_label'     => '',
        'link_color'     => '',
        'hover_image'    => '',
        'hover_video'    => '',
        'video'          => '',
        'video_autoplay' => true,
        'video_loop'     => true,
        'video_muted'    => true,
        'video_controls' => false,
        'video_poster'   => '',
        'link_url'       => '',
        'link_target'    => '_self',
        'title_element'  => 'h3',
        'card_padding'   => [ 'top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20 ],
        'shadow'         => 'none',
        'border_radius'  => '0',
        'card_radius'             => '0',
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
            [ 'key' => 'style', 'type' => 'select', 'label' => 'Style', 'options' => [
                'default'   => 'Default',
                'primary'   => 'Primary',
                'secondary' => 'Secondary',
                'hover'     => 'Hover',
            ]],
            [ 'key' => 'image',         'type' => 'image',  'label' => 'Image' ],
            [ 'key' => 'title',         'type' => 'text',   'label' => 'Title' ],
            [ 'key' => 'meta',          'type' => 'text',   'label' => 'Meta' ],
            [ 'key' => 'content',       'type' => 'textarea', 'label' => 'Content' ],
            [ 'key' => 'link_url',      'type' => 'text',   'label' => 'Link URL' ],
            [ 'key' => 'link_target',   'type' => 'select', 'label' => 'Link Target', 'options' => [
                '_self'  => 'Same Window',
                '_blank' => 'New Window',
            ]],
            [ 'key' => 'title_element', 'type' => 'select', 'label' => 'Title Element', 'options' => [
                'h2'  => 'H2',
                'h3'  => 'H3',
                'h4'  => 'H4',
                'div' => 'DIV',
            ]],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $uid        = 'opn-' . wp_unique_id();
        $style      = in_array( $s['style'], [ 'default', 'primary', 'secondary', 'hover' ], true ) ? $s['style'] : 'default';
        $card_class = "uk-card uk-card-{$style} uk-card-body";
        $tag        = in_array( $s['title_element'], [ 'h2', 'h3', 'h4', 'div' ], true ) ? $s['title_element'] : 'h3';
        $target     = $s['link_target'] === '_blank' ? ' target="_blank" rel="noopener"' : '';
        $media_type = in_array( $s['media_type'] ?? 'image', [ 'none', 'image', 'video' ], true ) ? $s['media_type'] : 'image';

        $css = $this->build_scoped_css( $uid, $s, $media_type );

        ob_start();
        ?>
        <style><?php echo $css; ?></style>
        <div class="olo-panel <?php echo esc_attr( $uid ); ?> <?php echo esc_attr( $card_class ); ?>">
            <?php if ( $media_type === 'image' && ! empty( $s['image'] ) ) : ?>
                <div class="olo-panel-media uk-card-media-top">
                    <?php
                    $att_id    = absint( $s['image_id'] ?? 0 );
                    $img_extra = 'class="olo-panel-img"';
                    $panel_img = Olo_Tile_Utils::img_srcset( $att_id, $s['image'], $s['title'] ?? '', '', 'full', $img_extra );
                    echo $this->render_hover_wrap( $panel_img, $s['hover_image'] ?? '', $s['hover_video'] ?? '' );
                    ?>
                </div>
            <?php elseif ( $media_type === 'video' && ! empty( $s['video'] ) ) :
                $vattrs = '';
                if ( ! empty( $s['video_autoplay'] ) ) { $vattrs .= ' autoplay'; }
                if ( ! empty( $s['video_loop'] ) )     { $vattrs .= ' loop'; }
                if ( ! empty( $s['video_muted'] ) )    { $vattrs .= ' muted'; }
                if ( ! empty( $s['video_controls'] ) ) { $vattrs .= ' controls'; }
                $vattrs .= ' playsinline preload="metadata"';
                $poster = ! empty( $s['video_poster'] ) ? ' poster="' . esc_url( $s['video_poster'] ) . '"' : '';
            ?>
                <div class="olo-panel-media uk-card-media-top">
                    <video class="olo-panel-video" src="<?php echo esc_url( $s['video'] ); ?>"<?php echo $poster . $vattrs; ?>></video>
                </div>
            <?php endif; ?>

            <?php
            $title_plain = wp_strip_all_tags( $s['title'] ?? '' );
            $content_plain = wp_strip_all_tags( $s['content'] ?? '' );
            list( $t_tfx_cls, $t_tfx_data ) = $this->tfx_attrs( $s, 'title', $title_plain );
            list( $c_tfx_cls, $c_tfx_data ) = $this->tfx_attrs( $s, 'content', $content_plain );
            ?>
            <?php if ( ! empty( $s['title'] ) ) : ?>
                <<?php echo $tag; ?> class="uk-card-title olo-panel-title<?php echo $t_tfx_cls; ?>"<?php echo $t_tfx_data; ?>><?php
                    if ( ! empty( $s['link_url'] ) ) {
                        echo '<a href="' . esc_url( $s['link_url'] ) . '"' . $target . ' class="olo-panel-titlelink">';
                    }
                    echo esc_html( $title_plain );
                    if ( ! empty( $s['link_url'] ) ) {
                        echo '</a>';
                    }
                ?></<?php echo $tag; ?>>
            <?php endif; ?>

            <?php if ( ! empty( $s['meta'] ) ) : ?>
                <p class="uk-text-meta olo-panel-meta"><?php echo esc_html( $s['meta'] ); ?></p>
            <?php endif; ?>

            <?php if ( ! empty( $s['content'] ) ) : ?>
                <div class="olo-panel-content<?php echo $c_tfx_cls; ?>"<?php echo $c_tfx_data; ?>><?php echo nl2br( esc_html( $content_plain ) ); ?></div>
            <?php endif; ?>

            <?php if ( ! empty( $s['link_url'] ) && ! empty( $s['link_label'] ) ) : ?>
                <a class="olo-panel-readmore" href="<?php echo esc_url( $s['link_url'] ); ?>"<?php echo $target; ?>><?php echo esc_html( $s['link_label'] ); ?> &rarr;</a>
            <?php endif; ?>
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

    /**
     * Build scoped CSS.
     * Note: uk-card-body provides default padding (30px). uk-card-media-top expands media to card edges
     * via negative margin. We override padding+media negative margin together to keep them in sync.
     */
    private function build_scoped_css( $uid, $s, $media_type ) {
        $sel = '.' . $uid;
        $css = '';

        // Outer card radius
        $card_radius = Olo_Tile_Utils::radius_int( $s['card_radius'] ?? 0 );
        if ( $card_radius > 0 ) {
            $css .= $sel . '.olo-panel{border-radius:' . $card_radius . 'px;overflow:hidden;}';
        }

        // Shadow
        $shadow_val = Olo_Tile_Utils::shadow_value( $s, 'shadow' );
        if ( $shadow_val && $shadow_val !== 'none' ) {
            $css .= $sel . '.olo-panel{box-shadow:' . $shadow_val . ';}';
        }
        $css .= $sel . '.olo-panel{transition:transform 0.35s cubic-bezier(.4,0,.2,1),box-shadow 0.35s ease;}';

        // Determine media radius / padding before deciding margin behavior
        $media_radius = '';
        $has_media_padding = false;
        if ( $media_type !== 'none' ) {
            $media_radius = $this->build_border_radius_css( $s['border_radius'] ?? '0' );
            $media_radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );
            $media_padding_arr = $s['media_padding'] ?? null;
            $has_media_padding = is_array( $media_padding_arr ) && (
                intval( $media_padding_arr['top'] ?? 0 ) > 0 ||
                intval( $media_padding_arr['right'] ?? 0 ) > 0 ||
                intval( $media_padding_arr['bottom'] ?? 0 ) > 0 ||
                intval( $media_padding_arr['left'] ?? 0 ) > 0
            );
        }
        // Media should be "inset" (no full-bleed) when user wants visible radius or padding around it
        $media_inset = ( $media_radius !== '' || $has_media_padding );

        // Custom padding override (uk-card-body default = 30px)
        $padding_arr = $s['card_padding'] ?? null;
        $has_custom_padding = is_array( $padding_arr ) || ( is_numeric( $padding_arr ) && $padding_arr !== '' );
        if ( $has_custom_padding ) {
            $padding = Olo_Tile_Utils::spacing_css( $padding_arr, 20 );
            $css .= $sel . '.olo-panel{padding:' . $padding . ';}';

            if ( $media_type !== 'none' && ! $media_inset ) {
                // Full-bleed: media spans card edges via negative margin matching the padding
                $t = is_array( $padding_arr ) ? intval( $padding_arr['top'] ?? 20 ) : intval( $padding_arr );
                $r = is_array( $padding_arr ) ? intval( $padding_arr['right'] ?? 20 ) : intval( $padding_arr );
                $l = is_array( $padding_arr ) ? intval( $padding_arr['left'] ?? 20 ) : intval( $padding_arr );
                $css .= $sel . ' > .olo-panel-media.uk-card-media-top{margin:-' . $t . 'px -' . $r . 'px 20px -' . $l . 'px;}';
            }
        }

        // When media is inset (has radius or padding), kill UIkit's negative margin
        if ( $media_type !== 'none' && $media_inset ) {
            $css .= $sel . ' > .olo-panel-media.uk-card-media-top{margin:0 0 16px 0!important;}';
        }

        // Media: ratio / height / fit / radius / zoom
        if ( $media_type !== 'none' ) {
            $img_ratio  = $s['image_ratio'] ?? 'auto';
            $img_height = absint( $s['image_height'] ?? 0 );
            $img_fit    = in_array( $s['image_fit'] ?? 'cover', [ 'cover', 'contain', 'fill' ], true ) ? $s['image_fit'] : 'cover';

            $css .= $sel . ' .olo-panel-media{position:relative;overflow:hidden;display:block;box-sizing:border-box;}';

            if ( $media_radius ) {
                // Override UIkit's uk-card-media-top inheriting card radius
                $css .= $sel . ' .olo-panel-media{border-radius:' . $media_radius . '!important;}';
                if ( $media_radius_hover_css !== '' ) $css .= $sel . ' .olo-panel-media:hover{border-radius:' . $media_radius_hover_css . ' !important;}';
            }

            if ( $has_media_padding ) {
                $mp = Olo_Tile_Utils::spacing_css( $media_padding_arr, 0 );
                $css .= $sel . ' .olo-panel-media{padding:' . $mp . ';}';
            }

            if ( $img_ratio && $img_ratio !== 'auto' ) {
                $css .= $sel . ' .olo-panel-media{aspect-ratio:' . esc_attr( $img_ratio ) . ';}';
            } elseif ( $img_height > 0 ) {
                $css .= $sel . ' .olo-panel-media{height:' . $img_height . 'px;}';
            }

            // Image / video sizing
            $css .= $sel . ' .olo-panel-img,' . $sel . ' .olo-panel-video{width:100%;height:100%;object-fit:' . esc_attr( $img_fit ) . ';display:block;transition:transform 0.5s cubic-bezier(.4,0,.2,1);}';

            // Hover wrap inside media: ensure full-fill
            $css .= $sel . ' .olo-panel-media .olo-hover-wrap{width:100%;height:100%;display:block;}';
            $css .= $sel . ' .olo-panel-media .olo-hover-wrap img,' . $sel . ' .olo-panel-media .olo-hover-wrap video{width:100%;height:100%;object-fit:' . esc_attr( $img_fit ) . ';display:block;}';

            // Image zoom on hover
            if ( ! empty( $s['image_zoom'] ) ) {
                $css .= $sel . ':hover .olo-panel-img,' . $sel . ':hover .olo-panel-video,' . $sel . ':hover .olo-panel-media .olo-hover-wrap img{transform:scale(1.06);}';
            }
        }

        // Text alignment
        $align = in_array( $s['text_align'] ?? 'left', [ 'left', 'center', 'right' ], true ) ? $s['text_align'] : 'left';
        if ( $align !== 'left' ) {
            $css .= $sel . '.olo-panel{text-align:' . $align . ';}';
        }

        // Title typography
        $title_size   = absint( $s['title_size'] ?? 0 );
        $title_weight = $s['title_weight'] ?? '';
        $title_color  = $s['title_color'] ?? '';
        $title_styles = '';
        if ( $title_size > 0 )       { $title_styles .= 'font-size:' . $title_size . 'px;'; }
        if ( $title_weight !== '' )  { $title_styles .= 'font-weight:' . esc_attr( $title_weight ) . ';'; }
        if ( $title_color )          { $title_styles .= 'color:' . esc_attr( $title_color ) . ';'; }
        if ( $title_styles ) {
            $css .= $sel . ' .olo-panel-title{' . $title_styles . '}';
            if ( $title_color ) {
                $css .= $sel . ' .olo-panel-title .olo-panel-titlelink{color:inherit;}';
            }
        }

        // Meta typography
        $meta_size  = absint( $s['meta_size'] ?? 0 );
        $meta_color = $s['meta_color'] ?? '';
        $meta_styles = '';
        if ( $meta_size > 0 ) { $meta_styles .= 'font-size:' . $meta_size . 'px;'; }
        if ( $meta_color )    { $meta_styles .= 'color:' . esc_attr( $meta_color ) . ';'; }
        if ( $meta_styles ) {
            $css .= $sel . ' .olo-panel-meta{' . $meta_styles . '}';
        }

        // Content typography
        $content_size  = absint( $s['content_size'] ?? 0 );
        $content_color = $s['content_color'] ?? '';
        $content_styles = '';
        if ( $content_size > 0 ) { $content_styles .= 'font-size:' . $content_size . 'px;'; }
        if ( $content_color )    { $content_styles .= 'color:' . esc_attr( $content_color ) . ';'; }
        if ( $content_styles ) {
            $css .= $sel . ' .olo-panel-content{' . $content_styles . '}';
        }

        // Read-more link
        $link_color = $s['link_color'] ?? '';
        $css .= $sel . ' .olo-panel-readmore{display:inline-block;margin-top:12px;font-weight:600;text-decoration:none;';
        $css .= $link_color ? 'color:' . esc_attr( $link_color ) . ';' : 'color:var(--olo-color-primary,#6366F1);';
        $css .= '}';
        $css .= $sel . ' .olo-panel-readmore:hover{opacity:0.8;}';

        return $css;
    }
}
