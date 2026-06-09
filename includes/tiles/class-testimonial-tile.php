<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Testimonial_Tile extends Olo_Tile_Base {

    protected $type     = 'testimonial';
    protected $name     = 'Testimonianza';
    protected $icon     = 'dashicons-format-quote';
    protected $category = 'marketing';
    protected $defaults = [
        'preset' => 'custom',
        'quote'           => 'Un prodotto fantastico!',
        'author_name'     => 'Mario Rossi',
        'author_role'     => 'CEO',
        'avatar'          => '',
        'rating'          => '5',
        'bg_color'        => '',
        'text_color'      => '',
        'star_color'        => '',
        'author_color'      => '',
        'quote_accent_color' => '',
        'quote_font'        => 'inherit',
        'quote_size'        => 0,
        'author_uppercase'  => false,
        'layout'          => 'single',
        'autoplay'        => false,
        'autoplay_interval' => 5,
        'slides_to_show'  => 1,
        'show_dots'       => true,
        'show_arrows'     => true,
        'grid_columns'    => 2,
        'items'           => [],
        'show_line'       => true,
        'line_color'      => '',
        'author_position' => 'bottom-left',
        'avatar_size'     => '48',
        'avatar_shape'    => 'circle',
        'avatar_radius'      => '6',
        'avatar_shadow'      => 'none',
        'avatar_border_width' => '0',
        'avatar_border_color' => '',
        'avatar_filter'      => 'none',
        'border_radius'      => '12',
        'border_width'            => '0',
        'border_color'            => '',
        'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'card_border'                 => [],
        'card_border_hover'           => [],
        'card_border_hover_duration'  => 300,
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
        $s      = wp_parse_args( $settings, $this->defaults );
        $uid    = 'olo-test-' . wp_rand( 10000, 99999 );
        $layout = in_array( $s['layout'], [ 'single', 'editorial', 'carousel', 'grid' ], true ) ? $s['layout'] : 'single';

        // Preparazione stili comuni
        $bg          = $this->safe_color_css( $s['bg_color'] ) ?: 'var(--olo-color-surface-alt, #F3F4F6)';
        $fg          = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text, #374151)';
        $line_col    = $this->safe_color_css( $s['line_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $show_line   = filter_var( $s['show_line'], FILTER_VALIDATE_BOOLEAN );
        $valid_pos   = [ 'bottom-left', 'bottom-center', 'bottom-right', 'left', 'right' ];
        $position    = in_array( $s['author_position'], $valid_pos ) ? $s['author_position'] : 'bottom-left';
        $is_bottom   = str_starts_with( $position, 'bottom' );
        $av_size     = intval( $s['avatar_size'] ) ?: 48;
        $is_square   = $s['avatar_shape'] === 'square';
        $av_radius   = $is_square ? ( Olo_Tile_Utils::radius_int( $s['avatar_radius'] ) . 'px' ) : '50%';
        $tile_radius = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $tile_radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );

        $star_color = $this->safe_color_css( $s['star_color'] ?? '' ) ?: '#FBBF24';
        $star_svg = '<svg width="18" height="18" viewBox="0 0 20 20" fill="' . esc_attr( $star_color ) . '" style="vertical-align:-2px;display:inline-block"><polygon points="10,1.5 12.5,7 18.5,7.6 14,11.5 15.3,17.5 10,14.5 4.7,17.5 6,11.5 1.5,7.6 7.5,7"/></svg>';

        $bottom_jc = 'flex-start';
        if ( $position === 'bottom-center' ) $bottom_jc = 'center';
        if ( $position === 'bottom-right' )  $bottom_jc = 'flex-end';

        ob_start();

        // CSS comune
        $this->render_common_styles( $uid, $bg, $fg, $line_col, $show_line, $is_bottom, $position, $av_size, $av_radius, $tile_radius, $tile_radius_hover_css, $bottom_jc, $s );

        if ( $layout === 'editorial' ) {
            $this->render_editorial( $uid, $s, $star_color );
        } elseif ( $layout === 'single' ) {
            $this->render_single( $uid, $s, $star_svg, $is_bottom, $position );
        } elseif ( $layout === 'carousel' ) {
            $this->render_carousel( $uid, $s, $star_svg, $is_bottom );
        } elseif ( $layout === 'grid' ) {
            $this->render_grid( $uid, $s, $star_svg, $is_bottom );
        }

        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();

        // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        // V3.21: hover transitions for the card border (the base CSS is emitted inline above).
        $card_border_hover_css = $this->build_border_hover_css( ".{$uid} .olo-test-card", $s['card_border'] ?? [], $s['card_border_hover'] ?? [], intval( $s['card_border_hover_duration'] ?? 300 ) );
        if ( $border_css || $border_hover_css || $border_effect_css || $card_border_hover_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}";
            echo $border_hover_css . $border_effect_css . $card_border_hover_css . '</style>';
        }

        return ob_get_clean();
    }

    private function render_common_styles( $uid, $bg, $fg, $line_col, $show_line, $is_bottom, $position, $av_size, $av_radius, $tile_radius, $tile_radius_hover_css, $bottom_jc, $s ) {
        ?>
        <style>
            .<?php echo $uid; ?> .olo-test-card {
                background: <?php echo $bg; ?>;
                color: <?php echo $fg; ?>;
                border-radius: <?php echo $tile_radius; ?>;
                padding: 24px;
                <?php
                // V3.21: nuovo card_border (4 lati hoverable) ha priorità sul legacy border_width/color.
                $_card_border_css = $this->build_border_css( $s['card_border'] ?? [] );
                if ( $_card_border_css ) {
                    echo $_card_border_css;
                } else {
                    $bw = intval( $s['border_width'] );
                    if ( $bw > 0 ) {
                        $bc = $this->safe_color_css( $s['border_color'] ) ?: 'var(--olo-color-text, #374151)';
                        echo "border:{$bw}px solid {$bc};";
                    }
                }
                ?>
            }
            <?php if ( $tile_radius_hover_css !== '' ) : ?>.<?php echo $uid; ?> .olo-test-card{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?> .olo-test-card:hover{border-radius:<?php echo $tile_radius_hover_css; ?> !important}<?php endif; ?>
            .<?php echo $uid; ?> .olo-test-stars {
                margin-bottom: 12px;
                display: flex;
                gap: 2px;
            }
            .<?php echo $uid; ?> blockquote {
                font-size: 1.1em;
                font-style: italic;
                margin: 0;
                line-height: 1.6;
                color: <?php echo $fg; ?>;
                <?php if ( $show_line ) : ?>
                border-left: 3px solid <?php echo $line_col; ?>;
                padding-left: 16px;
                <?php else : ?>
                border-left: none;
                padding-left: 0;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-test-author img {
                width: <?php echo $av_size; ?>px;
                height: <?php echo $av_size; ?>px;
                border-radius: <?php echo $av_radius; ?>;
                object-fit: cover;
                <?php
                $av_shadow = Olo_Tile_Utils::shadow( $s['avatar_shadow'] ?? 'none', 'photo' );
                if ( $av_shadow !== 'none' ) :
                ?>
                box-shadow: <?php echo $av_shadow; ?>;
                <?php endif; ?>
                <?php
                $abw = intval( $s['avatar_border_width'] );
                if ( $abw > 0 ) :
                    $abc = $this->safe_color_css( $s['avatar_border_color'] ) ?: 'var(--olo-color-on-primary, #FFFFFF)';
                ?>
                border: <?php echo $abw; ?>px solid <?php echo $abc; ?>;
                <?php endif; ?>
                <?php
                $filter_map = [ 'grayscale' => 'grayscale(100%)', 'sepia' => 'sepia(80%)', 'brightness' => 'brightness(1.15)', 'contrast' => 'contrast(1.3)' ];
                if ( isset( $filter_map[ $s['avatar_filter'] ] ) ) :
                ?>
                filter: <?php echo $filter_map[ $s['avatar_filter'] ]; ?>;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-test-author-name {
                font-weight: 600;
                color: <?php echo $fg; ?>;
            }
            .<?php echo $uid; ?> .olo-test-author-role {
                font-size: 0.875em;
                opacity: 0.7;
                color: <?php echo $fg; ?>;
            }
            <?php if ( ! $is_bottom ) : ?>
            .<?php echo $uid; ?> .olo-test-layout {
                display: flex;
                gap: 20px;
                align-items: flex-start;
                <?php if ( $position === 'right' ) : ?>flex-direction: row-reverse;<?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-test-author {
                flex-shrink: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 8px;
            }
            .<?php echo $uid; ?> .olo-test-quote {
                flex: 1;
                min-width: 0;
            }
            <?php else : ?>
            .<?php echo $uid; ?> .olo-test-author-wrap {
                display: flex;
                justify-content: <?php echo $bottom_jc; ?>;
                margin-top: 20px;
            }
            .<?php echo $uid; ?> .olo-test-author {
                display: flex;
                align-items: center;
                gap: 12px;
            }
            <?php endif; ?>
        </style>
        <?php
    }

    /* Layout EDITORIALE: centrato — stelle · citazione serif (con <em> accento) · autore "Nome · Ruolo" */
    private function render_editorial( $uid, $s, $star_color ) {
        $fg      = $this->safe_color_css( $s['text_color'] ?? '' ) ?: 'var(--olo-color-text-emphasis, #f6e9ec)';
        $accent  = $this->safe_color_css( $s['quote_accent_color'] ?? '' ) ?: 'var(--olo-color-primary, #e7a0b4)';
        $authclr = $this->safe_color_css( $s['author_color'] ?? '' ) ?: $accent;
        $rating  = absint( $s['rating'] );
        $qfont   = $s['quote_font'] ?? 'inherit';
        $qfam    = ( $qfont === 'heading' ) ? 'var(--olo-font-family-heading, Georgia, serif)' : ( ( $qfont === 'body' ) ? 'var(--olo-font-family, -apple-system, sans-serif)' : 'inherit' );
        $qsize   = intval( $s['quote_size'] ?? 0 );
        $qsize_css = $qsize > 0 ? ( $qsize . 'px' ) : 'clamp(24px,3.4vw,40px)';
        $upper   = ! empty( $s['author_uppercase'] );
        $qupper  = ! empty( $s['quote_uppercase'] );
        $quote   = wp_kses( (string) ( $s['quote'] ?? '' ), [ 'em' => [], 'strong' => [], 'br' => [] ] );
        $name    = esc_html( wp_strip_all_tags( $s['author_name'] ?? '' ) );
        $role    = esc_html( wp_strip_all_tags( $s['author_role'] ?? '' ) );
        $stars   = str_repeat( '★', max( 0, $rating ) );
        ?>
        <div class="olo-testimonial <?php echo esc_attr( $uid ); ?> olo-test-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>">
            <div class="olo-test-ed" style="text-align:center;max-width:840px;margin:0 auto;">
                <?php if ( $rating > 0 ) : ?><div class="olo-test-ed__stars" style="color:<?php echo esc_attr( $star_color ); ?>;letter-spacing:.2em;font-size:18px;margin-bottom:18px;line-height:1;"><?php echo $stars; ?></div><?php endif; ?>
                <q class="olo-test-ed__q" style="display:block;font-family:<?php echo $qfam; ?>;font-size:<?php echo $qsize_css; ?>;line-height:1.28;color:<?php echo esc_attr( $fg ); ?>;quotes:none;margin:0;<?php echo $qupper ? 'text-transform:uppercase;' : ''; ?>"><?php echo $quote; ?></q>
                <?php if ( $name !== '' || $role !== '' ) : ?>
                <div class="olo-test-ed__by" style="margin-top:22px;font-weight:700;font-size:12px;letter-spacing:.1em;color:<?php echo esc_attr( $authclr ); ?>;<?php echo $upper ? 'text-transform:uppercase;' : ''; ?>"><?php echo $name; ?><?php if ( $name !== '' && $role !== '' ) echo ' · '; ?><?php echo $role; ?></div>
                <?php endif; ?>
            </div>
        </div>
        <style>.<?php echo $uid; ?> .olo-test-ed__q em{font-style:italic;color:<?php echo esc_attr( $accent ); ?>;}</style>
        <?php
    }

    private function render_single( $uid, $s, $star_svg, $is_bottom, $position ) {
        $rating      = absint( $s['rating'] );
        $quote       = nl2br( esc_html( wp_strip_all_tags( $s['quote'] ) ) );
        $author_name = esc_html( wp_strip_all_tags( $s['author_name'] ) );
        $author_role = esc_html( wp_strip_all_tags( $s['author_role'] ) );
        ?>
        <div class="olo-testimonial <?php echo esc_attr( $uid ); ?> olo-test-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>">
            <div class="olo-test-card">
                <?php echo $this->render_card_inner( $s, $rating, $star_svg, $quote, $author_name, $author_role, $is_bottom, $position ); ?>
            </div>
        </div>
        <?php
    }

    private function render_carousel( $uid, $s, $star_svg, $is_bottom ) {
        $items = $this->parse_items( $s['items'] ?? [] );
        if ( empty( $items ) ) {
            // Fallback: mostra singola card
            $this->render_single( $uid, $s, $star_svg, $is_bottom, $s['author_position'] ?? 'bottom-left' );
            return;
        }

        $autoplay     = filter_var( $s['autoplay'], FILTER_VALIDATE_BOOLEAN );
        $interval     = max( 1, min( 30, absint( $s['autoplay_interval'] ) ) ) * 1000;
        $slides       = max( 1, min( 4, absint( $s['slides_to_show'] ) ) );
        $show_dots    = filter_var( $s['show_dots'], FILTER_VALIDATE_BOOLEAN );
        $show_arrows  = filter_var( $s['show_arrows'], FILTER_VALIDATE_BOOLEAN );

        $autoplay_attr = $autoplay ? 'true' : 'false';
        ?>
        <style>
            .<?php echo $uid; ?> .uk-slider-items > li { padding: 0 8px; box-sizing: border-box; }
            .<?php echo $uid; ?> .olo-test-slider-nav { display: flex; justify-content: center; align-items: center; gap: 16px; margin-top: 16px; }
            .<?php echo $uid; ?> .olo-test-slider-nav a { color: inherit; }
            .<?php echo $uid; ?> .uk-dotnav > * > * { background: var(--olo-color-border, #E5E7EB); }
            .<?php echo $uid; ?> .uk-dotnav > .uk-active > * { background: var(--olo-color-primary, #e1474f); }
        </style>
        <div class="olo-testimonial <?php echo esc_attr( $uid ); ?> olo-test-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>"
             uk-slider="autoplay: <?php echo $autoplay_attr; ?>; autoplay-interval: <?php echo $interval; ?>">
            <ul class="uk-slider-items uk-child-width-1-<?php echo $slides; ?>@m uk-child-width-1-1">
                <?php foreach ( $items as $item ) : ?>
                <li>
                    <div class="olo-test-card">
                        <?php
                        $rating      = absint( $item['rating'] ?? 0 );
                        $quote       = nl2br( esc_html( wp_strip_all_tags( $item['quote'] ?? '' ) ) );
                        $author_name = esc_html( $item['author_name'] ?? '' );
                        $author_role = esc_html( $item['author_role'] ?? '' );
                        $item_s      = array_merge( $s, $item );
                        echo $this->render_card_inner( $item_s, $rating, $star_svg, $quote, $author_name, $author_role, $is_bottom, $s['author_position'] ?? 'bottom-left' );
                        ?>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>

            <?php if ( $show_arrows || $show_dots ) : ?>
            <div class="olo-test-slider-nav">
                <?php if ( $show_arrows ) : ?>
                <a href="#" uk-slidenav-previous uk-slider-item="previous"></a>
                <?php endif; ?>
                <?php if ( $show_dots ) : ?>
                <ul class="uk-dotnav uk-slider-nav"></ul>
                <?php endif; ?>
                <?php if ( $show_arrows ) : ?>
                <a href="#" uk-slidenav-next uk-slider-item="next"></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }

    private function render_grid( $uid, $s, $star_svg, $is_bottom ) {
        $items   = $this->parse_items( $s['items'] ?? [] );
        if ( empty( $items ) ) {
            $this->render_single( $uid, $s, $star_svg, $is_bottom, $s['author_position'] ?? 'bottom-left' );
            return;
        }

        $columns = max( 1, min( 4, absint( $s['grid_columns'] ) ) );
        ?>
        <style>
            .<?php echo $uid; ?> .olo-test-grid {
                display: grid;
                grid-template-columns: repeat(<?php echo $columns; ?>, 1fr);
                gap: 20px;
            }
            @media (max-width: 640px) {
                .<?php echo $uid; ?> .olo-test-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
        <div class="olo-testimonial <?php echo esc_attr( $uid ); ?> olo-test-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>">
            <div class="olo-test-grid">
                <?php foreach ( $items as $item ) : ?>
                <div class="olo-test-card">
                    <?php
                    $rating      = absint( $item['rating'] ?? 0 );
                    $quote       = nl2br( esc_html( wp_strip_all_tags( $item['quote'] ?? '' ) ) );
                    $author_name = esc_html( $item['author_name'] ?? '' );
                    $author_role = esc_html( $item['author_role'] ?? '' );
                    $item_s      = array_merge( $s, $item );
                    echo $this->render_card_inner( $item_s, $rating, $star_svg, $quote, $author_name, $author_role, $is_bottom, $s['author_position'] ?? 'bottom-left' );
                    ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    private function render_card_inner( $s, $rating, $star_svg, $quote, $author_name, $author_role, $is_bottom, $position ) {
        ob_start();
        if ( ! $is_bottom ) : ?>
            <div class="olo-test-layout">
                <div class="olo-test-author">
                    <?php echo $this->render_author( $s, $author_name, $author_role ); ?>
                </div>
                <div class="olo-test-quote">
                    <?php echo $this->render_quote( $rating, $star_svg, $quote, $s ); ?>
                </div>
            </div>
        <?php else : ?>
            <?php echo $this->render_quote( $rating, $star_svg, $quote, $s ); ?>
            <div class="olo-test-author-wrap">
                <div class="olo-test-author">
                    <?php echo $this->render_author( $s, $author_name, $author_role ); ?>
                </div>
            </div>
        <?php endif;
        return ob_get_clean();
    }

    private function render_quote( $rating, $star_svg, $quote, $s = [] ) {
        $out = '';
        if ( $rating > 0 ) {
            $out .= '<div class="olo-test-stars">' . str_repeat( $star_svg, $rating ) . '</div>';
        }
        list( $tq_cls, $tq_data ) = $this->tfx_attrs( $s, 'quote', wp_strip_all_tags( $quote ) );
        $out .= '<blockquote class="' . trim( $tq_cls ) . '"' . $tq_data . '>' . $quote . '</blockquote>';
        return $out;
    }

    private function render_author( $s, $author_name, $author_role ) {
        $out = '';
        if ( ! empty( $s['avatar'] ) ) {
            $out .= '<img src="' . esc_url( $s['avatar'] ) . '" alt="' . esc_attr( wp_strip_all_tags( $s['author_name'] ?? '' ) ) . '" loading="lazy" />';
        }
        $out .= '<div>';
        $out .= '<div class="olo-test-author-name">' . $author_name . '</div>';
        if ( ! empty( $author_role ) ) {
            $out .= '<div class="olo-test-author-role">' . $author_role . '</div>';
        }
        $out .= '</div>';
        return $out;
    }

    private function parse_items( $raw ) {
        if ( ! is_array( $raw ) ) {
            $decoded = json_decode( $raw, true );
            if ( is_array( $decoded ) ) {
                return $decoded;
            }
            return [];
        }
        return $raw;
    }
}
