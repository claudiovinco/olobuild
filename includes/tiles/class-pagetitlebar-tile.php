<?php
/**
 * Page Title Bar tile — configurable hero-style page header.
 *
 * Shows dynamic page/post title, breadcrumbs, and optional background.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Pagetitlebar_Tile extends Olo_Tile_Base {

    protected $type     = 'pagetitlebar';
    protected $name     = 'Page Title Bar';
    protected $icon     = 'dashicons-format-aside';
    protected $category = 'structure';
    protected $defaults = [
        'title_tag'         => 'h1',
        'title_color'       => '',
        'title_size'        => '36',
        'title_weight'      => '700',
        'title_align'       => 'center',
        'subtitle'          => '',
        'subtitle_color'    => '',
        'subtitle_size'     => '16',
        'show_breadcrumbs'  => true,
        'breadcrumb_color'  => '',
        'breadcrumb_separator' => '/',
        'bg_color'          => '',
        'bg_image'          => '',
        'bg_overlay'        => '60',
        'bg_overlay_color'  => '#000000',
        'bg_size'           => 'cover',
        'bg_position'       => 'center center',
        'bg_parallax'       => false,
        'min_height'        => '200',
        'padding_y'         => '60',
        'content_width'     => '1200',
        'border_bottom'     => false,
        'border_color'      => '',
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
        return [];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-ptb-' . wp_rand( 10000, 99999 );

        // Dynamic title
        $title = $this->get_dynamic_title();
        $tag   = in_array( $s['title_tag'], [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span' ], true ) ? $s['title_tag'] : 'h1';

        // TOKEN-FIRST: barra titolo scura del brand, testo bianco in contrasto, neutri → token
        $title_c    = $this->safe_color_css( $s['title_color'] ) ?: 'var(--olo-color-primary-contrast, #FFFFFF)';
        $title_size = max( 14, intval( $s['title_size'] ) );
        $title_w    = sanitize_text_field( $s['title_weight'] ) ?: '700';
        $align      = in_array( $s['title_align'], [ 'left', 'center', 'right' ], true ) ? $s['title_align'] : 'center';

        $sub_c    = $this->safe_color_css( $s['subtitle_color'] ) ?: 'var(--olo-color-text-soft, #D1D5DB)';
        $sub_size = max( 12, intval( $s['subtitle_size'] ) );

        $bg_c       = $this->safe_color_css( $s['bg_color'] ) ?: 'var(--olo-color-dark, #1F2937)';
        $bg_img     = esc_url( $s['bg_image'] ?? '' );
        $overlay    = max( 0, min( 100, intval( $s['bg_overlay'] ) ) );
        $overlay_c  = $this->safe_color_css( $s['bg_overlay_color'] ) ?: '#000000';
        $min_h      = max( 0, intval( $s['min_height'] ) );
        $_tp = $s['tile_padding'] ?? null;
        $pad_y = is_array( $_tp ) ? max( 0, intval( $_tp['top'] ?? 40 ) ) : max( 0, intval( $s['padding_y'] ?? 40 ) );
        $max_w      = max( 0, intval( $s['content_width'] ) );

        $bc_color = $this->safe_color_css( $s['breadcrumb_color'] ) ?: 'var(--olo-color-text-faint, #9CA3AF)';
        $bc_sep   = esc_html( $s['breadcrumb_separator'] ?: '/' );

        $border_b = ! empty( $s['border_bottom'] );
        $border_c = $this->safe_color_css( $s['border_color'] ) ?: 'var(--olo-color-border, #374151)';

        $bg_style = "background-color:{$bg_c};";
        if ( $bg_img ) {
            $bg_size = esc_attr( $s['bg_size'] ?? 'cover' );
            $bg_pos  = esc_attr( $s['bg_position'] ?? 'center center' );
            $bg_style .= "background-image:url({$bg_img});background-size:{$bg_size};background-position:{$bg_pos};background-repeat:no-repeat;";
        }

        ob_start();
        ?>
        <div id="<?php echo esc_attr( $uid ); ?>" class="olo-page-title-bar" style="<?php echo $bg_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $bg_style assembled above from safe_color_css() colours, esc_url() image and esc_attr() size/position; $border_c via safe_color_css() or fixed var() fallback ?>position:relative;min-height:<?php echo (int) $min_h; ?>px;display:flex;align-items:center;text-align:<?php echo esc_attr( $align ); ?>;<?php if ( $border_b ) echo "border-bottom:1px solid {$border_c};"; ?>"<?php if ( ! empty( $s['bg_parallax'] ) && $bg_img ) echo ' uk-parallax="bgy: -100"'; ?>>

            <?php if ( $bg_img && $overlay > 0 ) : ?>
            <div style="position:absolute;inset:0;background:<?php echo $overlay_c; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- colour validated by safe_color_css() or fixed '#000000' fallback; opacity is round() of an intval()-clamped 0-100 value ?>;opacity:<?php echo (float) round( $overlay / 100, 2 ); ?>;pointer-events:none" aria-hidden="true"></div>
            <?php endif; ?>

            <div style="position:relative;z-index:1;width:100%;max-width:<?php echo (int) $max_w; ?>px;margin:0 auto;padding:<?php echo (int) $pad_y; ?>px <?php echo (int) ( is_array( $_tp ) ? intval( $_tp['right'] ?? 20 ) : 20 ); ?>px <?php echo (int) ( is_array( $_tp ) ? intval( $_tp['bottom'] ?? $pad_y ) : $pad_y ); ?>px <?php echo (int) ( is_array( $_tp ) ? intval( $_tp['left'] ?? 20 ) : 20 ); ?>px">
                <<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tag whitelisted via in_array() above; colour via safe_color_css() or fixed var() fallback; size intval()-clamped; weight sanitize_text_field()'d or fixed '700' ?> style="color:<?php echo $title_c; ?>;font-size:<?php echo (int) $title_size; ?>px;font-weight:<?php echo $title_w; ?>;margin:0;line-height:1.2">
                    <?php echo esc_html( $title ); ?>
                </<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tag whitelisted via in_array() above ?>>

                <?php if ( ! empty( $s['subtitle'] ) ) : ?>
                <?php list( $pts_cls, $pts_data ) = $this->tfx_attrs( $s, 'subtitle', $s['subtitle'] ); ?>
                <p class="olo-ptb-sub<?php echo $pts_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); colour via safe_color_css() or fixed var() fallback; size intval()-clamped ?>" style="color:<?php echo $sub_c; ?>;font-size:<?php echo (int) $sub_size; ?>px;margin:10px 0 0;opacity:.85"<?php echo $pts_data; ?>>
                    <?php echo esc_html( $s['subtitle'] ); ?>
                </p>
                <?php endif; ?>

                <?php if ( ! empty( $s['show_breadcrumbs'] ) ) : ?>
                <nav class="olo-ptb-breadcrumbs" aria-label="<?php echo esc_attr( olo_t( 'Breadcrumb' ) ); ?>" style="margin-top:16px;font-size:13px;color:<?php echo $bc_color; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- colour validated by safe_color_css() or fixed var() fallback ?>">
                    <?php echo $this->render_breadcrumbs( $bc_sep, $bc_color ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- breadcrumb HTML built internally with esc_url()/esc_html(); separator esc_html()'d and colour safe_color_css()'d above ?>
                </nav>
                <?php endif; ?>
            </div>
        </div>
        <style>
            /* a11y tastiera: anello di focus visibile sui link breadcrumb della barra titolo */
            #<?php echo esc_attr( $uid ); ?> .olo-ptb-breadcrumbs a:focus-visible {
                outline: none;
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
                border-radius: 3px;
            }
        </style>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Text_Effects::css() from whitelisted effects, sanitized colors and integer timings
        $this->tfx_print_script();
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }

    private function get_dynamic_title() {
        if ( is_singular() ) {
            return get_the_title();
        }
        if ( is_category() ) {
            return single_cat_title( '', false );
        }
        if ( is_tag() ) {
            return single_tag_title( '', false );
        }
        if ( is_tax() ) {
            return single_term_title( '', false );
        }
        if ( is_post_type_archive() ) {
            return post_type_archive_title( '', false );
        }
        if ( is_author() ) {
            return get_the_author();
        }
        if ( is_search() ) {
            return 'Risultati ricerca: ' . get_search_query();
        }
        if ( is_404() ) {
            return 'Pagina non trovata';
        }
        if ( is_home() ) {
            return get_the_title( get_option( 'page_for_posts' ) ) ?: 'Blog';
        }
        if ( function_exists( 'is_shop' ) && is_shop() ) {
            return wc_get_page_id( 'shop' ) ? get_the_title( wc_get_page_id( 'shop' ) ) : 'Shop';
        }
        return get_bloginfo( 'name' );
    }

    private function render_breadcrumbs( $separator, $color ) {
        $items = [];
        $items[] = '<a href="' . esc_url( home_url( '/' ) ) . '" style="color:' . $color . ';text-decoration:none;opacity:.7">Home</a>';

        if ( is_singular() ) {
            global $post;
            if ( is_singular( 'post' ) ) {
                $cats = get_the_category();
                if ( ! empty( $cats ) ) {
                    $items[] = '<a href="' . esc_url( get_category_link( $cats[0]->term_id ) ) . '" style="color:' . $color . ';text-decoration:none;opacity:.7">' . esc_html( $cats[0]->name ) . '</a>';
                }
            }
            if ( $post ) {
                $ancestors = array_reverse( get_post_ancestors( $post ) );
                foreach ( $ancestors as $anc ) {
                    $items[] = '<a href="' . esc_url( get_permalink( $anc ) ) . '" style="color:' . $color . ';text-decoration:none;opacity:.7">' . esc_html( get_the_title( $anc ) ) . '</a>';
                }
                $items[] = '<span>' . esc_html( get_the_title() ) . '</span>';
            }
        } elseif ( is_category() ) {
            $items[] = '<span>' . esc_html( single_cat_title( '', false ) ) . '</span>';
        } elseif ( is_search() ) {
            $items[] = '<span>Ricerca</span>';
        } elseif ( is_404() ) {
            $items[] = '<span>404</span>';
        }

        return implode( ' <span style="margin:0 6px;opacity:.5">' . $separator . '</span> ', $items );
    }
}
