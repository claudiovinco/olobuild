<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_PanelSlider_Tile extends Olo_Tile_Base {

    protected $type     = 'panelslider';
    protected $name     = 'Panel Slider';
    protected $icon     = 'dashicons-slides';
    protected $category = 'interactive';
    protected $defaults = [
        'panels' => [
            [ 'id' => 'ps-1', 'title' => 'Card 1', 'content' => 'Content...', 'image' => '' ],
            [ 'id' => 'ps-2', 'title' => 'Card 2', 'content' => 'Content...', 'image' => '' ],
            [ 'id' => 'ps-3', 'title' => 'Card 3', 'content' => 'Content...', 'image' => '' ],
        ],
        'columns'           => '3',
        'gap'               => 'default',
        'card_style'        => 'default',
        'card_radius'       => '8',
        'card_padding'      => [ 'top' => 16, 'right' => 16, 'bottom' => 16, 'left' => 16 ],
        'equal_height'      => true,
        'image_ratio'       => '4/3',
        'image_height'      => '',
        'image_fit'         => 'cover',
        'image_zoom'        => false,
        'show_arrows'       => true,
        'arrow_style'       => 'circle',
        'arrow_size'        => '40',
        'arrow_color'       => '',
        'arrow_bg'          => '',
        'show_dots'         => false,
        'autoplay'          => false,
        'autoplay_interval' => '5000',
        'shadow'            => 'none',
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
            [ 'key' => 'panels',            'type' => 'panels', 'label' => 'Panels' ],
            [ 'key' => 'columns',           'type' => 'select', 'label' => 'Columns' ],
            [ 'key' => 'gap',               'type' => 'select', 'label' => 'Gap' ],
            [ 'key' => 'card_style',        'type' => 'select', 'label' => 'Card Style' ],
            [ 'key' => 'show_arrows',       'type' => 'toggle', 'label' => 'Show Arrows' ],
            [ 'key' => 'autoplay',          'type' => 'toggle', 'label' => 'Autoplay' ],
            [ 'key' => 'autoplay_interval', 'type' => 'range',  'label' => 'Autoplay Interval (ms)' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $panels = is_array( $s['panels'] ) ? $s['panels'] : [];
        if ( empty( $panels ) ) {
            return '<div class="olo-panelslider" style="padding:40px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);">No panels added</div>';
        }

        $uid      = 'ops-' . wp_unique_id();
        $columns  = absint( $s['columns'] ) ?: 3;
        $gap      = in_array( $s['gap'], [ 'collapse', 'small', 'default', 'medium', 'large' ], true ) ? $s['gap'] : 'default';
        $style    = in_array( $s['card_style'], [ 'default', 'primary', 'secondary', 'hover' ], true ) ? $s['card_style'] : 'default';
        $autoplay = ! empty( $s['autoplay'] ) ? 'true' : 'false';
        $interval = absint( $s['autoplay_interval'] ) ?: 5000;

        $gap_class    = $gap === 'collapse' ? 'uk-grid-collapse' : 'uk-grid-' . $gap;
        $equal_class  = ! empty( $s['equal_height'] ) ? ' uk-grid-match' : '';
        $arrow_style  = $s['arrow_style'] ?? 'circle';
        $show_arrows  = ! empty( $s['show_arrows'] ) && count( $panels ) > $columns;
        $show_dots    = ! empty( $s['show_dots'] );

        // Build scoped CSS
        $css = $this->build_scoped_css( $uid, $s );

        ob_start();
        ?>
        <style><?php echo $css; ?></style>
        <div class="olo-panelslider <?php echo esc_attr( $uid ); ?> olo-ps-arrows-<?php echo esc_attr( $arrow_style ); ?>" uk-slider="autoplay: <?php echo $autoplay; ?>; autoplay-interval: <?php echo $interval; ?>; finite: <?php echo $autoplay === 'true' ? 'false' : 'true'; ?>">
            <div class="uk-position-relative">
                <div class="uk-slider-container uk-slider-container-offset">
                    <ul class="uk-slider-items uk-child-width-1-<?php echo $columns; ?>@m uk-grid <?php echo esc_attr( $gap_class . $equal_class ); ?>">
                        <?php foreach ( $panels as $i => $panel ) :
                            $has_link   = ! empty( $panel['link'] );
                            $link_open  = '';
                            $link_close = '';
                            if ( $has_link ) {
                                $tgt = ! empty( $panel['link_target'] ) ? ' target="_blank" rel="noopener noreferrer"' : '';
                                $link_open  = '<a href="' . esc_url( $panel['link'] ) . '"' . $tgt . ' class="olo-ps-link" style="text-decoration:none;color:inherit;display:block;height:100%;">';
                                $link_close = '</a>';
                            }
                        ?>
                            <li>
                                <?php echo $link_open; ?>
                                <div class="olo-ps-card uk-card uk-card-<?php echo esc_attr( $style ); ?>">
                                    <?php if ( ! empty( $panel['image'] ) ) : ?>
                                        <div class="olo-ps-media">
                                            <?php
                                            $ps_img = '<img class="olo-ps-img" src="' . esc_url( $panel['image'] ) . '" alt="' . esc_attr( $panel['title'] ?? '' ) . '" loading="lazy">';
                                            echo $this->render_hover_wrap( $ps_img, $panel['hover_image'] ?? '', $panel['hover_video'] ?? '' );
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php
                                    list( $pst_cls, $pst_data ) = $this->tfx_attrs( $s, 'title', $panel['title'] ?? '' );
                                    list( $psc_cls, $psc_data ) = $this->tfx_attrs( $s, 'content', wp_strip_all_tags( $panel['content'] ?? '' ) );
                                    ?>
                                    <div class="olo-ps-body">
                                        <?php if ( ! empty( $panel['title'] ) ) : ?>
                                        <h3 class="olo-ps-title uk-card-title<?php echo $pst_cls; ?>"<?php echo $pst_data; ?>><?php echo esc_html( $panel['title'] ); ?></h3>
                                        <?php endif; ?>
                                        <?php if ( ! empty( $panel['content'] ) ) : ?>
                                        <div class="olo-ps-text<?php echo $psc_cls; ?>"<?php echo $psc_data; ?>><?php echo wp_kses_post( $panel['content'] ); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php echo $link_close; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <?php if ( $show_arrows ) :
                    echo $this->render_arrows( $arrow_style );
                endif; ?>
            </div>

            <?php if ( $show_dots ) : ?>
            <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>
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
     * Build scoped CSS for shadow, image ratio, equal height, arrows.
     */
    private function build_scoped_css( $uid, $s ) {
        $sel = '.' . $uid;
        $css = '';

        // Card radius + padding
        $radius  = $this->build_border_radius_css( $s['card_radius'] ?? 8 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['card_radius_hover'] ?? null );
        $padding = Olo_Tile_Utils::spacing_css( $s['card_padding'] ?? 16, 16 );
        $radius_css = $radius ? 'border-radius:' . $radius . ';' : '';

        // Shadow (preset or custom)
        $shadow_val = Olo_Tile_Utils::shadow_value( $s, 'shadow' );
        $shadow_css = ( $shadow_val && $shadow_val !== 'none' ) ? 'box-shadow:' . $shadow_val . ';' : '';

        // Allow shadow to be visible: slider items must not clip
        if ( $shadow_css ) {
            $css .= $sel . ' .uk-slider-items > li{overflow:visible;}';
        }

        // Card base
        $css .= $sel . ' .olo-ps-card{' . $radius_css . $shadow_css . 'overflow:hidden;background:#fff;display:flex;flex-direction:column;height:100%;transition:transform 0.35s cubic-bezier(.4,0,.2,1),box-shadow 0.35s ease,border-radius 400ms cubic-bezier(.4,0,.2,1);}';
        if ( $radius_hover_css !== '' ) {
            $css .= $sel . ' .olo-ps-card:hover{border-radius:' . $radius_hover_css . ' !important;}';
        }

        // Equal-height: stretch li and inner content
        if ( ! empty( $s['equal_height'] ) ) {
            $css .= $sel . ' .uk-slider-items{align-items:stretch;}';
            $css .= $sel . ' .uk-slider-items > li{display:flex;}';
            $css .= $sel . ' .uk-slider-items > li > .olo-ps-link{width:100%;}';
            $css .= $sel . ' .uk-slider-items > li .olo-ps-card{flex:1 1 auto;}';
            $css .= $sel . ' .olo-ps-body{flex:1 1 auto;display:flex;flex-direction:column;}';
        }

        // Image ratio / height / fit
        $img_ratio  = $s['image_ratio'] ?? '4/3';
        $img_height = absint( $s['image_height'] ?? 0 );
        $img_fit    = in_array( $s['image_fit'] ?? 'cover', [ 'cover', 'contain', 'fill' ], true ) ? $s['image_fit'] : 'cover';

        $css .= $sel . ' .olo-ps-media{position:relative;overflow:hidden;width:100%;flex:0 0 auto;}';
        if ( $img_ratio && $img_ratio !== 'auto' ) {
            $css .= $sel . ' .olo-ps-media{aspect-ratio:' . esc_attr( $img_ratio ) . ';}';
        } elseif ( $img_height > 0 ) {
            $css .= $sel . ' .olo-ps-media{height:' . $img_height . 'px;}';
        }
        $css .= $sel . ' .olo-ps-img{width:100%;height:100%;object-fit:' . esc_attr( $img_fit ) . ';display:block;transition:transform 0.5s cubic-bezier(.4,0,.2,1);}';

        if ( ! empty( $s['image_zoom'] ) ) {
            $css .= $sel . ' .olo-ps-card:hover .olo-ps-img{transform:scale(1.06);}';
        }

        // Body padding
        $css .= $sel . ' .olo-ps-body{padding:' . $padding . ';}';

        // Title
        $title_size = absint( $s['title_size'] ?? 0 );
        $title_col  = $s['title_color'] ?? '';
        $title_styles = '';
        if ( $title_size > 0 ) { $title_styles .= 'font-size:' . $title_size . 'px;'; }
        if ( $title_col )      { $title_styles .= 'color:' . esc_attr( $title_col ) . ';'; }
        if ( $title_styles )   { $css .= $sel . ' .olo-ps-title{' . $title_styles . '}'; }

        // Content
        $content_size = absint( $s['content_size'] ?? 0 );
        $content_col  = $s['content_color'] ?? '';
        $content_styles = '';
        if ( $content_size > 0 ) { $content_styles .= 'font-size:' . $content_size . 'px;'; }
        if ( $content_col )      { $content_styles .= 'color:' . esc_attr( $content_col ) . ';'; }
        if ( $content_styles )   { $css .= $sel . ' .olo-ps-text{' . $content_styles . '}'; }

        // Arrows styling
        $arrow_size  = absint( $s['arrow_size'] ?? 40 ) ?: 40;
        $arrow_color = $s['arrow_color'] ?? '';
        $arrow_bg    = $s['arrow_bg'] ?? '';
        $arrow_style = $s['arrow_style'] ?? 'circle';

        $css .= $this->build_arrow_css( $sel, $arrow_style, $arrow_size, $arrow_color, $arrow_bg );

        return $css;
    }

    /**
     * Generate CSS for arrow variants.
     */
    private function build_arrow_css( $sel, $style, $size, $color, $bg ) {
        $css = '';
        $color = $color ? esc_attr( $color ) : '#1f2937';
        $bg    = $bg ? esc_attr( $bg ) : '#ffffff';
        $half  = intval( $size / 2 );

        // Common positioning + interaction
        $css .= $sel . ' .olo-ps-arrow{position:absolute;top:50%;transform:translateY(-50%);z-index:5;display:flex;align-items:center;justify-content:center;cursor:pointer;border:none;outline:none;transition:all 0.25s ease;text-decoration:none;}';
        $css .= $sel . ' .olo-ps-arrow.olo-ps-prev{left:-' . ( $half + 4 ) . 'px;}';
        $css .= $sel . ' .olo-ps-arrow.olo-ps-next{right:-' . ( $half + 4 ) . 'px;}';
        $css .= '@media (max-width:960px){' . $sel . ' .olo-ps-arrow.olo-ps-prev{left:8px;}' . $sel . ' .olo-ps-arrow.olo-ps-next{right:8px;}}';
        $css .= $sel . ' .olo-ps-arrow svg{width:50%;height:50%;display:block;pointer-events:none;}';
        $css .= $sel . ' .olo-ps-arrow:hover{transform:translateY(-50%) scale(1.08);}';

        switch ( $style ) {
            case 'circle':
                $css .= $sel . ' .olo-ps-arrow{width:' . $size . 'px;height:' . $size . 'px;border-radius:50%;background:' . $bg . ';color:' . $color . ';box-shadow:0 4px 12px rgba(0,0,0,0.15);}';
                $css .= $sel . ' .olo-ps-arrow:hover{box-shadow:0 6px 18px rgba(0,0,0,0.22);}';
                break;
            case 'circle-outline':
                $css .= $sel . ' .olo-ps-arrow{width:' . $size . 'px;height:' . $size . 'px;border-radius:50%;background:transparent;color:' . $color . ';border:2px solid ' . $color . ';}';
                $css .= $sel . ' .olo-ps-arrow:hover{background:' . $color . ';color:' . $bg . ';}';
                break;
            case 'square':
                $css .= $sel . ' .olo-ps-arrow{width:' . $size . 'px;height:' . $size . 'px;border-radius:6px;background:' . $bg . ';color:' . $color . ';box-shadow:0 4px 12px rgba(0,0,0,0.15);}';
                break;
            case 'minimal':
                $css .= $sel . ' .olo-ps-arrow{width:' . $size . 'px;height:' . $size . 'px;background:transparent;color:' . $color . ';}';
                $css .= $sel . ' .olo-ps-arrow svg{width:80%;height:80%;}';
                $css .= $sel . ' .olo-ps-arrow:hover{opacity:0.7;}';
                break;
            case 'chevron-bold':
                $css .= $sel . ' .olo-ps-arrow{width:' . $size . 'px;height:' . $size . 'px;background:transparent;color:' . $color . ';}';
                $css .= $sel . ' .olo-ps-arrow svg{width:90%;height:90%;stroke-width:3;}';
                break;
            case 'arrow-long':
                $css .= $sel . ' .olo-ps-arrow{width:' . ( $size + 12 ) . 'px;height:' . $size . 'px;background:' . $bg . ';color:' . $color . ';border-radius:' . intval( $size / 2 ) . 'px;box-shadow:0 4px 12px rgba(0,0,0,0.15);}';
                $css .= $sel . ' .olo-ps-arrow svg{width:55%;height:55%;}';
                break;
            case 'fancy':
                $css .= $sel . ' .olo-ps-arrow{width:' . $size . 'px;height:' . $size . 'px;border-radius:50%;background:linear-gradient(135deg,var(--olo-color-primary,#6366F1),#8b5cf6);color:#fff;box-shadow:0 6px 20px rgba(99,102,241,0.35);}';
                $css .= $sel . ' .olo-ps-arrow:hover{box-shadow:0 10px 28px rgba(99,102,241,0.5);}';
                break;
            case 'uikit':
            default:
                // Uses native uk-slidenav, only positioning
                $css .= $sel . ' .olo-ps-arrow{width:' . $size . 'px;height:' . $size . 'px;color:' . $color . ';}';
                break;
        }

        // Dots styling
        $css .= $sel . ' .uk-dotnav > * > *{width:10px;height:10px;border:2px solid ' . $color . ';background:transparent;}';
        $css .= $sel . ' .uk-dotnav > .uk-active > *{background:' . $color . ';border-color:' . $color . ';}';

        return $css;
    }

    /**
     * Render arrow markup with correct SVG icons per style.
     */
    private function render_arrows( $style ) {
        $prev_label = esc_attr__( 'Precedente', 'olobuild' );
        $next_label = esc_attr__( 'Successivo', 'olobuild' );

        if ( $style === 'uikit' ) {
            return '<a class="olo-ps-arrow olo-ps-prev" href uk-slidenav-previous uk-slider-item="previous" aria-label="' . $prev_label . '"></a>'
                 . '<a class="olo-ps-arrow olo-ps-next" href uk-slidenav-next uk-slider-item="next" aria-label="' . $next_label . '"></a>';
        }

        $svg_prev = $this->arrow_svg( $style, 'prev' );
        $svg_next = $this->arrow_svg( $style, 'next' );

        return '<button type="button" class="olo-ps-arrow olo-ps-prev" uk-slider-item="previous" aria-label="' . $prev_label . '">' . $svg_prev . '</button>'
             . '<button type="button" class="olo-ps-arrow olo-ps-next" uk-slider-item="next" aria-label="' . $next_label . '">' . $svg_next . '</button>';
    }

    /**
     * Return SVG markup for the arrow style and direction.
     */
    private function arrow_svg( $style, $dir ) {
        $rotate = $dir === 'prev' ? ' style="transform:rotate(180deg);"' : '';

        switch ( $style ) {
            case 'arrow-long':
                return '<svg' . $rotate . ' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg"><line x1="3" y1="12" x2="20" y2="12"/><polyline points="14 6 20 12 14 18"/></svg>';
            case 'chevron-bold':
                return '<svg' . $rotate . ' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg"><polyline points="9 6 15 12 9 18"/></svg>';
            default:
                return '<svg' . $rotate . ' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg"><polyline points="9 6 15 12 9 18"/></svg>';
        }
    }
}
