<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Tagcloud_Tile extends Olo_Tile_Base {

    protected $type     = 'tagcloud';
    protected $name     = 'Tag Cloud';
    protected $icon     = 'dashicons-tag';
    protected $category = 'dynamic';
    protected $defaults = [
        'preset' => 'custom',
        'taxonomy'         => 'post_tag',
        'custom_taxonomy'  => '',
        'min_font'         => '12',
        'max_font'         => '28',
        'max_tags'         => '30',
        'orderby'          => 'name',
        'order'            => 'ASC',
        'show_count'       => false,
        'separator'        => ' ',
        'layout'           => 'cloud',
        'columns'          => '3',
        'text_color'       => '#374151',
        'hover_color'      => '#ffffff',
        'background_color' => '#f3f4f6',
        'hover_background' => '#6366f1',
        'border_radius'    => '16',
        'padding'          => [ 'top' => 6, 'right' => 14, 'bottom' => 6, 'left' => 14 ],
        'gap'              => '8',
        'font_weight'      => '500',
        'link_underline'   => false,
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

        // Determina tassonomia
        $taxonomy = $s['taxonomy'];
        if ( $taxonomy === 'custom' ) {
            $taxonomy = sanitize_key( $s['custom_taxonomy'] );
        }
        if ( empty( $taxonomy ) || ! taxonomy_exists( $taxonomy ) ) {
            $taxonomy = 'post_tag';
        }

        // Parametri query
        $max_tags = absint( $s['max_tags'] ) ?: 30;
        $orderby  = in_array( $s['orderby'], [ 'name', 'count' ], true ) ? $s['orderby'] : 'name';
        $order    = in_array( $s['order'], [ 'ASC', 'DESC' ], true ) ? $s['order'] : 'ASC';

        // Ottieni i termini
        $terms = get_terms( [
            'taxonomy'   => $taxonomy,
            'number'     => $max_tags,
            'orderby'    => $orderby,
            'order'      => $order,
            'hide_empty' => true,
        ] );

        if ( is_wp_error( $terms ) || empty( $terms ) ) {
            return '<div class="olo-tagcloud" style="text-align:center;padding:20px;color:var(--olo-color-text-muted, #9CA3AF);">'
                . esc_html( olo_t( 'Nessun tag trovato' ) )
                . '</div>';
        }

        // Calcola range conteggio
        $counts    = wp_list_pluck( $terms, 'count' );
        $min_count = min( $counts );
        $max_count = max( $counts );

        // Parametri stile
        $min_font    = absint( $s['min_font'] ) ?: 12;
        $max_font    = absint( $s['max_font'] ) ?: 28;
        $gap         = absint( $s['gap'] );
        $radius      = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );
        $font_weight = in_array( $s['font_weight'], [ '400', '500', '600', '700' ], true ) ? $s['font_weight'] : '500';
        $show_count  = ! empty( $s['show_count'] );
        $underline   = ! empty( $s['link_underline'] );
        $layout      = $s['layout'];
        $columns     = absint( $s['columns'] ) ?: 3;

        // Colori
        $text_color       = $this->safe_color_css( $s['text_color'] ) ?: '#374151';
        $hover_color      = $this->safe_color_css( $s['hover_color'] ) ?: '#ffffff';
        $bg_color         = $this->safe_color_css( $s['background_color'] ) ?: '#f3f4f6';
        $hover_bg         = $this->safe_color_css( $s['hover_background'] ) ?: '#6366f1';

        // Padding — supporta sia string legacy "6 14" che object spacing { top, right, bottom, left }
        $padding_raw = $s['padding'];
        if ( is_array( $padding_raw ) ) {
            $padding_css = sprintf( '%dpx %dpx %dpx %dpx',
                intval( $padding_raw['top'] ?? 0 ),
                intval( $padding_raw['right'] ?? 0 ),
                intval( $padding_raw['bottom'] ?? 0 ),
                intval( $padding_raw['left'] ?? 0 )
            );
        } else {
            $padding_parts = preg_split( '/\s+/', trim( (string) $padding_raw ) );
            $padding_css   = implode( 'px ', $padding_parts ) . 'px';
        }

        // Container style
        $container_style = '';
        if ( $layout === 'list' ) {
            $container_style = "display:flex;flex-direction:column;gap:{$gap}px;";
        } elseif ( $layout === 'grid' ) {
            $container_style = "display:grid;grid-template-columns:repeat({$columns},1fr);gap:{$gap}px;";
        } else {
            // cloud
            $container_style = "display:flex;flex-wrap:wrap;gap:{$gap}px;align-items:center;";
        }

        // ID univoco per stile hover
        $uid = 'olo-tc-' . wp_unique_id();

        ob_start();
        ?>
        <style>
            #<?php echo $uid; ?> .olo-tagcloud-tag {
                color: <?php echo $text_color; ?>;
                background: <?php echo $bg_color; ?>;
                border-radius: <?php echo $radius; ?>;
                padding: <?php echo $padding_css; ?>;
                font-weight: <?php echo $font_weight; ?>;
                text-decoration: <?php echo $underline ? 'underline' : 'none'; ?>;
                display: inline-flex;
                align-items: center;
                gap: 4px;
                transition: all .2s ease;
                line-height: 1.4;
            }
            <?php if ( $radius_hover_css !== '' ) : ?>#<?php echo $uid; ?> .olo-tagcloud-tag{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}#<?php echo $uid; ?> .olo-tagcloud-tag:hover{border-radius:<?php echo $radius_hover_css; ?> !important}<?php endif; ?>
            #<?php echo $uid; ?> .olo-tagcloud-tag:hover {
                color: <?php echo $hover_color; ?>;
                background: <?php echo $hover_bg; ?>;
            }
        </style>
        <div id="<?php echo esc_attr( $uid ); ?>" class="olo-tagcloud olo-tc-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" style="<?php echo esc_attr( $container_style ); ?>">
            <?php foreach ( $terms as $term ) :
                $count     = (int) $term->count;
                $range     = max( 1, $max_count - $min_count );
                $font_size = $min_font + ( ( $count - $min_count ) / $range ) * ( $max_font - $min_font );
                $font_size = round( $font_size, 1 );
                $link      = get_term_link( $term );
                if ( is_wp_error( $link ) ) {
                    $link = '#';
                }
            ?>
                <a href="<?php echo esc_url( $link ); ?>" class="olo-tagcloud-tag" style="font-size:<?php echo $font_size; ?>px;">
                    <?php echo esc_html( $term->name ); ?>
                    <?php if ( $show_count ) : ?>
                        <span class="olo-tagcloud-count" style="opacity:.6;">(<?php echo $count; ?>)</span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
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
