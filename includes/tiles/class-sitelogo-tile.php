<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_SiteLogo_Tile extends Olo_Tile_Base {

    protected $type     = 'sitelogo';
    protected $name     = 'Logo sito';
    protected $icon     = 'dashicons-admin-home';
    protected $category = 'navigation';
    protected $defaults = [
        'source'              => 'custom_image',
        'custom_image'        => '',
        'dark_image'          => '',
        'dark_mode'           => 'none',
        'svg_logo'            => '',
        'max_height'          => 50,
        'max_height_sticky'   => '',
        'max_width'           => '',
        'link_home'           => true,
        'link_url'            => '',
        'show_tagline'        => false,
        'tagline_color'       => '',
        'tagline_size'        => '14',
        'alignment'           => 'left',
        'retina_image'        => '',
        'hover_opacity'       => '',
        'transition_duration' => '0.3',
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
        $s = wp_parse_args( $settings, $this->defaults );

        $uid        = 'olo-logo-' . wp_rand( 10000, 99999 );
        $max_height = absint( $s['max_height'] );
        $link_home  = ! empty( $s['link_home'] );
        $link_url   = ! empty( $s['link_url'] ) ? esc_url( $s['link_url'] ) : esc_url( home_url( '/' ) );
        $alt        = esc_attr( get_bloginfo( 'name' ) );
        $dark_mode  = $s['dark_mode'];
        $has_dark   = $dark_mode !== 'none' && ! empty( $s['dark_image'] );
        $trans_dur  = floatval( $s['transition_duration'] ) ?: 0.3;

        // Alignment
        $align_map = [ 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' ];
        $align_css = $align_map[ $s['alignment'] ] ?? 'flex-start';

        // Build main logo image style
        $img_style = 'max-height:' . $max_height . 'px;width:auto;display:block;transition:opacity ' . $trans_dur . 's ease,max-height ' . $trans_dur . 's ease';
        if ( ! empty( $s['max_width'] ) ) {
            $img_style .= ';max-width:' . absint( $s['max_width'] ) . 'px';
        }

        // Resolve main logo
        $main_logo_url = '';
        $is_svg        = false;
        $svg_content   = '';

        if ( $s['source'] === 'svg' && ! empty( $s['svg_logo'] ) ) {
            $is_svg = true;
            $att_id = absint( $s['svg_logo_id'] ?? 0 );
            if ( $att_id > 0 ) {
                $file = get_attached_file( $att_id );
                if ( $file ) {
                    $svg_content = @file_get_contents( $file );
                }
            }
            if ( empty( $svg_content ) ) {
                $main_logo_url = $s['svg_logo'];
            }
        } elseif ( $s['source'] === 'custom_image' && ! empty( $s['custom_image'] ) ) {
            $main_logo_url = $s['custom_image'];
        } else {
            // Auto: WP custom logo or site title
            $custom_logo_id = get_theme_mod( 'custom_logo' );
            if ( $custom_logo_id ) {
                $main_logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
            }
        }

        ob_start();
        ?>
        <style>
        .<?php echo $uid; ?>{display:flex;justify-content:<?php echo $align_css; ?>}
        .<?php echo $uid; ?> .olo-sitelogo-link{display:inline-block;line-height:0}
        .<?php echo $uid; ?> img,.<?php echo $uid; ?> svg{max-height:<?php echo $max_height; ?>px;width:auto;display:block;transition:opacity <?php echo $trans_dur; ?>s ease,max-height <?php echo $trans_dur; ?>s ease}
        <?php if ( ! empty( $s['max_width'] ) ) : ?>
        .<?php echo $uid; ?> img,.<?php echo $uid; ?> svg{max-width:<?php echo absint( $s['max_width'] ); ?>px}
        <?php endif; ?>
        <?php if ( ! empty( $s['hover_opacity'] ) ) : ?>
        .<?php echo $uid; ?> .olo-sitelogo-link:hover img,.<?php echo $uid; ?> .olo-sitelogo-link:hover svg{opacity:<?php echo absint( $s['hover_opacity'] ) / 100; ?>}
        <?php endif; ?>
        <?php if ( ! empty( $s['max_height_sticky'] ) ) : ?>
        .olo-sticky-on .<?php echo $uid; ?> img,.olo-sticky-on .<?php echo $uid; ?> svg{max-height:<?php echo absint( $s['max_height_sticky'] ); ?>px}
        <?php endif; ?>
        <?php if ( $has_dark ) : ?>
        .<?php echo $uid; ?> .olo-logo-dark{display:none}
        .<?php echo $uid; ?> .olo-logo-main{display:block}
        <?php if ( $dark_mode === 'auto' ) : ?>
        @media(prefers-color-scheme:dark){
          .<?php echo $uid; ?> .olo-logo-dark{display:block}
          .<?php echo $uid; ?> .olo-logo-main{display:none}
        }
        <?php elseif ( $dark_mode === 'class' ) : ?>
        .olo-dark .<?php echo $uid; ?> .olo-logo-dark{display:block}
        .olo-dark .<?php echo $uid; ?> .olo-logo-main{display:none}
        <?php elseif ( $dark_mode === 'sticky' ) : ?>
        .olo-sticky-on .<?php echo $uid; ?> .olo-logo-dark{display:block}
        .olo-sticky-on .<?php echo $uid; ?> .olo-logo-main{display:none}
        <?php endif; ?>
        <?php endif; ?>
        </style>

        <div class="olo-sitelogo <?php echo esc_attr( $uid ); ?>">
        <?php
        // Determine what to output
        $main_html = '';
        $dark_html = '';

        if ( $is_svg && $svg_content ) {
            // Inline SVG
            $main_html = '<span class="olo-logo-main">' . $svg_content . '</span>';
        } elseif ( $main_logo_url ) {
            $srcset = '';
            if ( ! empty( $s['retina_image'] ) ) {
                $srcset = ' srcset="' . esc_url( $main_logo_url ) . ' 1x, ' . esc_url( $s['retina_image'] ) . ' 2x"';
            }
            $main_html = '<img src="' . esc_url( $main_logo_url ) . '"' . $srcset . ' alt="' . $alt . '" class="olo-logo-main" />';
        } elseif ( $is_svg && $main_logo_url ) {
            $main_html = '<img src="' . esc_url( $main_logo_url ) . '" alt="' . $alt . '" class="olo-logo-main" />';
        } else {
            // Text fallback
            $main_html = '<span class="olo-sitelogo-text uk-h3 uk-margin-remove olo-logo-main">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
        }

        // Dark variant
        if ( $has_dark ) {
            $dark_html = '<img src="' . esc_url( $s['dark_image'] ) . '" alt="' . $alt . '" class="olo-logo-dark" />';
        }

        // Wrap in link
        if ( $link_home ) {
            echo '<a href="' . $link_url . '" class="olo-sitelogo-link" aria-label="' . $alt . '">';
            echo $main_html;
            echo $dark_html;
            echo '</a>';
        } else {
            echo $main_html;
            echo $dark_html;
        }

        // Tagline
        if ( ! empty( $s['show_tagline'] ) ) {
            $tg_style = 'font-size:' . absint( $s['tagline_size'] ) . 'px;margin:4px 0 0;';
            if ( ! empty( $s['tagline_color'] ) ) {
                $tg_style .= 'color:' . esc_attr( $s['tagline_color'] ) . ';';
            } else {
                $tg_style .= 'color:var(--olo-color-text-muted, #9CA3AF);';
            }
            echo '<p class="olo-sitelogo-tagline" style="' . $tg_style . '">' . esc_html( get_bloginfo( 'description' ) ) . '</p>';
        }
        ?>
        </div>
        <?php
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
