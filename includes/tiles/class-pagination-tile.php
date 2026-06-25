<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Pagination_Tile extends Olo_Tile_Base {

    protected $type     = 'pagination';
    protected $name     = 'Paginazione';
    protected $icon     = 'dashicons-ellipsis';
    protected $category = 'navigation';
    protected $defaults = [
        'preset' => 'custom',
        'style'              => 'both',
        'alignment'          => 'center',
        'show_first_last'    => false,
        'prev_text'          => "\xC2\xAB Precedente",
        'next_text'          => "Successivo \xC2\xBB",
        'gap'                => '8',
        'button_padding'     => '8 16',
        'text_color'         => '',
        'active_text_color'  => '',
        'background_color'   => '',
        'active_background'  => '',
        'border_radius'      => '4',
        'hover_background'   => '',
        'font_size'          => '14',
        'border_color'       => '#e5e7eb',
        'border_width'       => '1',
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
            [ 'key' => 'style',           'type' => 'select', 'label' => 'Stile' ],
            [ 'key' => 'alignment',        'type' => 'select', 'label' => 'Allineamento' ],
            [ 'key' => 'show_first_last',  'type' => 'toggle', 'label' => 'Primo/Ultimo' ],
            [ 'key' => 'prev_text',        'type' => 'text',   'label' => 'Testo Precedente' ],
            [ 'key' => 'next_text',        'type' => 'text',   'label' => 'Testo Successivo' ],
            [ 'key' => 'gap',              'type' => 'range',  'label' => 'Distanza' ],
            [ 'key' => 'button_padding',   'type' => 'text',   'label' => 'Padding' ],
            [ 'key' => 'font_size',        'type' => 'range',  'label' => 'Dim. testo' ],
            [ 'key' => 'border_radius',    'type' => 'range',  'label' => 'Raggio bordo' ],
            [ 'key' => 'border_width',     'type' => 'range',  'label' => 'Spessore bordo' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $uid = 'olo-pgn-' . wp_rand( 10000, 99999 );

        // Sanitize
        $style_mode  = in_array( $s['style'], [ 'numbered', 'prev-next', 'both' ], true ) ? $s['style'] : 'both';
        $alignment   = in_array( $s['alignment'], [ 'left', 'center', 'right' ], true ) ? $s['alignment'] : 'center';
        $show_fl     = ! empty( $s['show_first_last'] );
        $prev_text   = esc_html( $s['prev_text'] ?: olo_t( "\xC2\xAB Precedente" ) );
        $next_text   = esc_html( $s['next_text'] ?: olo_t( "Successivo \xC2\xBB" ) );
        $gap         = max( 0, absint( $s['gap'] ) );
        $font_size   = max( 10, min( 24, absint( $s['font_size'] ) ) );
        $radius      = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );
        $bw          = max( 0, min( 4, absint( $s['border_width'] ) ) );

        // Sanitize padding — dual-format: oggetto spacing {top,right,bottom,left}
        // (type 'spacing') oppure stringa legacy 'V H' (es. '8 16').
        if ( is_array( $s['button_padding'] ) ) {
            $padding_css = Olo_Tile_Utils::spacing_css( $s['button_padding'] );
        } else {
            $raw_padding = preg_replace( '/[^0-9\s]/', '', (string) $s['button_padding'] );
            $pad_parts   = preg_split( '/\s+/', trim( $raw_padding ) );
            $padding_css = '';
            if ( count( $pad_parts ) === 1 ) {
                $padding_css = absint( $pad_parts[0] ) . 'px';
            } elseif ( count( $pad_parts ) >= 2 ) {
                $padding_css = absint( $pad_parts[0] ) . 'px ' . absint( $pad_parts[1] ) . 'px';
            } else {
                $padding_css = '8px 16px';
            }
        }

        // Colors — TOKEN-FIRST: voce attiva = primario brand (era #e1474f blu off-brand)
        $text_color      = $this->safe_color_css( $s['text_color'] );
        $active_text     = $this->safe_color_css( $s['active_text_color'] ) ?: 'var(--olo-color-primary-contrast, #ffffff)';
        $bg_color        = $this->safe_color_css( $s['background_color'] );
        $active_bg       = $this->safe_color_css( $s['active_background'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $hover_bg        = $this->safe_color_css( $s['hover_background'] );
        $border_color    = $this->safe_color_css( $s['border_color'] ?: '#e5e7eb' );

        // Alignment map
        $align_map = [ 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' ];
        $justify   = $align_map[ $alignment ] ?? 'center';

        // Detect context: singular (multi-page post) vs archive
        $is_singular = is_singular();

        // For singular posts with <!--nextpage-->
        if ( $is_singular ) {
            global $page, $numpages, $multipage;
            if ( ! $multipage ) {
                return '';
            }

            ob_start();
            $this->render_styles( $uid, $justify, $gap, $font_size, $radius, $bw, $padding_css,
                $text_color, $bg_color, $border_color, $active_text, $active_bg, $hover_bg );
            ?>
            <nav class="olo-pagination <?php echo esc_attr( $uid ); ?> olo-pg-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" role="navigation" aria-label="<?php echo esc_attr( olo_t( 'Paginazione' ) ); ?>">
            <?php
            $show_numbers = ( $style_mode === 'numbered' || $style_mode === 'both' );
            $show_pn      = ( $style_mode === 'prev-next' || $style_mode === 'both' );

            // Prev
            if ( $show_pn ) {
                if ( $page > 1 ) {
                    echo '<a class="olo-pagination-link olo-pagination-prev" href="' . esc_url( $this->get_pagenum_link_singular( $page - 1 ) ) . '">' . $prev_text . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- URL escaped via esc_url(); label escaped via esc_html() at assignment above
                }
            }

            // First
            if ( $show_fl ) {
                if ( $show_numbers ) {
                    if ( $page > 2 ) {
                        echo '<a class="olo-pagination-link olo-pagination-first" href="' . esc_url( $this->get_pagenum_link_singular( 1 ) ) . '">&laquo;</a>';
                    }
                }
            }

            // Numbered pages
            if ( $show_numbers ) {
                for ( $i = 1; $i <= $numpages; $i++ ) {
                    if ( $i === $page ) {
                        echo '<span class="olo-pagination-current" aria-current="page">' . (int) $i . '</span>';
                    } else {
                        echo '<a class="olo-pagination-link" href="' . esc_url( $this->get_pagenum_link_singular( $i ) ) . '">' . (int) $i . '</a>';
                    }
                }
            }

            // Last
            if ( $show_fl ) {
                if ( $show_numbers ) {
                    if ( $page < ( $numpages - 1 ) ) {
                        echo '<a class="olo-pagination-link olo-pagination-last" href="' . esc_url( $this->get_pagenum_link_singular( $numpages ) ) . '">&raquo;</a>';
                    }
                }
            }

            // Next
            if ( $show_pn ) {
                if ( $page < $numpages ) {
                    echo '<a class="olo-pagination-link olo-pagination-next" href="' . esc_url( $this->get_pagenum_link_singular( $page + 1 ) ) . '">' . $next_text . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- URL escaped via esc_url(); label escaped via esc_html() at assignment above
                }
            }
            ?>
            </nav>
            <?php
            return ob_get_clean();
        }

        // Archive / search pagination
        global $wp_query;
        $total_pages = (int) ( $wp_query->max_num_pages ?? 1 );

        if ( $total_pages <= 1 ) {
            return '';
        }

        $current_page = max( 1, get_query_var( 'paged', 1 ) );

        ob_start();
        $this->render_styles( $uid, $justify, $gap, $font_size, $radius, $bw, $padding_css,
            $text_color, $bg_color, $border_color, $active_text, $active_bg, $hover_bg );
        ?>
        <nav class="olo-pagination <?php echo esc_attr( $uid ); ?>" role="navigation" aria-label="<?php echo esc_attr( olo_t( 'Paginazione' ) ); ?>">
        <?php
        $show_numbers = ( $style_mode === 'numbered' || $style_mode === 'both' );
        $show_pn      = ( $style_mode === 'prev-next' || $style_mode === 'both' );

        // Prev
        if ( $show_pn ) {
            if ( $current_page > 1 ) {
                echo '<a class="olo-pagination-link olo-pagination-prev" href="' . esc_url( get_pagenum_link( $current_page - 1 ) ) . '">' . $prev_text . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- URL escaped via esc_url(); label escaped via esc_html() at assignment above
            }
        }

        // First
        if ( $show_fl ) {
            if ( $show_numbers ) {
                if ( $current_page > 2 ) {
                    echo '<a class="olo-pagination-link olo-pagination-first" href="' . esc_url( get_pagenum_link( 1 ) ) . '">&laquo;</a>';
                }
            }
        }

        // Numbered
        if ( $show_numbers ) {
            $paginate = paginate_links( [
                'total'     => $total_pages,
                'current'   => $current_page,
                'type'      => 'array',
                'prev_next' => false,
                'end_size'  => 1,
                'mid_size'  => 2,
            ] );

            if ( $paginate ) {
                foreach ( $paginate as $link ) {
                    // paginate_links returns <a> or <span class="current"> elements
                    // Replace classes for our styling
                    $link = str_replace( 'page-numbers current', 'olo-pagination-current', $link );
                    $link = str_replace( 'page-numbers dots', 'olo-pagination-dots', $link );
                    $link = str_replace( 'page-numbers', 'olo-pagination-link', $link );
                    echo $link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML generated by WordPress core paginate_links(); only CSS class names swapped via fixed str_replace() above
                }
            }
        }

        // Last
        if ( $show_fl ) {
            if ( $show_numbers ) {
                if ( $current_page < ( $total_pages - 1 ) ) {
                    echo '<a class="olo-pagination-link olo-pagination-last" href="' . esc_url( get_pagenum_link( $total_pages ) ) . '">&raquo;</a>';
                }
            }
        }

        // Next
        if ( $show_pn ) {
            if ( $current_page < $total_pages ) {
                echo '<a class="olo-pagination-link olo-pagination-next" href="' . esc_url( get_pagenum_link( $current_page + 1 ) ) . '">' . $next_text . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- URL escaped via esc_url(); label escaped via esc_html() at assignment above
            }
        }
        ?>
        </nav>
        <?php
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_css() from sanitized border settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base border helpers from sanitized border settings
        }
        return ob_get_clean();
    }

    /**
     * Render shared CSS styles
     */
    private function render_styles( $uid, $justify, $gap, $font_size, $radius, $bw, $padding_css,
        $text_color, $bg_color, $border_color, $active_text, $active_bg, $hover_bg ) {
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized in render(): colors via the safe_color_css() whitelist (with token fallbacks), integers via absint() with min()/max() clamps, alignment from a fixed map, padding/radius via absint() parts and Olo_Tile_Utils helpers; $uid is internally generated. ?>
        <style>
            .<?php echo $uid; ?> {
                display: flex;
                flex-wrap: wrap;
                justify-content: <?php echo $justify; ?>;
                gap: <?php echo (int) $gap; ?>px;
                padding: 8px 0;
            }
            .<?php echo $uid; ?> .olo-pagination-link,
            .<?php echo $uid; ?> .olo-pagination-current,
            .<?php echo $uid; ?> .olo-pagination-dots {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: <?php echo $padding_css; ?>;
                font-size: <?php echo (int) $font_size; ?>px;
                line-height: 1;
                min-width: 32px;
                text-align: center;
                border-radius: <?php echo $radius; ?>;
                text-decoration: none;
                transition: background 0.2s ease, color 0.2s ease;
                box-sizing: border-box;
            }
            <?php if ( $radius_hover_css !== '' ) : ?>.<?php echo $uid; ?> .olo-pagination-dots{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?> .olo-pagination-dots:hover{border-radius:<?php echo $radius_hover_css; ?> !important}<?php endif; ?>
            .<?php echo $uid; ?> .olo-pagination-link {
                <?php if ( $text_color ) : ?>color: <?php echo $text_color; ?>;<?php endif; ?>
                <?php if ( $bg_color ) : ?>background: <?php echo $bg_color; ?>;<?php else : ?>background: transparent;<?php endif; ?>
                <?php if ( $bw > 0 ) : ?>border: <?php echo (int) $bw; ?>px solid <?php echo $border_color; ?>;<?php else : ?>border: none;<?php endif; ?>
                cursor: pointer;
            }
            <?php if ( $hover_bg ) : ?>
            .<?php echo $uid; ?> .olo-pagination-link:hover {
                background: <?php echo $hover_bg; ?>;
            }
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-pagination-link:focus-visible {
                outline: none;
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
            }
            .<?php echo $uid; ?> .olo-pagination-current {
                color: <?php echo $active_text; ?>;
                background: <?php echo $active_bg; ?>;
                border: <?php echo (int) $bw; ?>px solid <?php echo $active_bg; ?>;
                font-weight: 600;
                cursor: default;
            }
            .<?php echo $uid; ?> .olo-pagination-dots {
                <?php if ( $text_color ) : ?>color: <?php echo $text_color; ?>;<?php endif; ?>
                background: transparent;
                border: none;
                cursor: default;
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php
    }

    /**
     * Get page link for singular multi-page posts
     */
    private function get_pagenum_link_singular( $page_num ) {
        global $post;
        if ( $page_num <= 1 ) {
            return get_permalink( $post->ID );
        }
        if ( get_option( 'permalink_structure' ) ) {
            return trailingslashit( get_permalink( $post->ID ) ) . $page_num . '/';
        }
        return add_query_arg( 'page', $page_num, get_permalink( $post->ID ) );
    }
}
