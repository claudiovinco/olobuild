<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Postnavigation_Tile extends Olo_Tile_Base {

    protected $type     = 'postnavigation';
    protected $name     = 'Navigazione articolo';
    protected $icon     = 'dashicons-arrow-left-alt';
    protected $category = 'navigation';
    protected $defaults = [
        'show_thumbnail'  => true,
        'show_label'      => true,
        'prev_label'      => 'Precedente',
        'next_label'      => 'Successivo',
        'show_title'      => true,
        'title_length'    => '30',
        'layout'          => 'side-by-side',
        'gap'             => '20',
        'thumbnail_size'  => '60',
        'text_color'      => '#F3F4F6',
        'link_color'      => '#93C5FD',
        'hover_color'     => '#60A5FA',
        'background_color' => '',
        'border_radius'   => '8',
        'padding'         => '16',
        'same_taxonomy'   => false,
        'taxonomy'        => 'category',
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
            [ 'key' => 'show_thumbnail',  'type' => 'toggle', 'label' => olo_t( 'Mostra miniatura' ) ],
            [ 'key' => 'show_label',      'type' => 'toggle', 'label' => olo_t( 'Mostra etichetta' ) ],
            [ 'key' => 'prev_label',      'type' => 'text',   'label' => olo_t( 'Etichetta precedente' ) ],
            [ 'key' => 'next_label',      'type' => 'text',   'label' => olo_t( 'Etichetta successivo' ) ],
            [ 'key' => 'show_title',      'type' => 'toggle', 'label' => olo_t( 'Mostra titolo' ) ],
            [ 'key' => 'title_length',    'type' => 'range',  'label' => olo_t( 'Lunghezza titolo' ) ],
            [ 'key' => 'layout',          'type' => 'select', 'label' => olo_t( 'Layout' ) ],
            [ 'key' => 'gap',             'type' => 'range',  'label' => olo_t( 'Gap' ) ],
            [ 'key' => 'thumbnail_size',  'type' => 'range',  'label' => olo_t( 'Dimensione miniatura' ) ],
            [ 'key' => 'text_color',      'type' => 'color',  'label' => olo_t( 'Colore testo' ) ],
            [ 'key' => 'link_color',      'type' => 'color',  'label' => olo_t( 'Colore link' ) ],
            [ 'key' => 'hover_color',     'type' => 'color',  'label' => olo_t( 'Colore hover' ) ],
            [ 'key' => 'background_color','type' => 'color',  'label' => olo_t( 'Sfondo card' ) ],
            [ 'key' => 'border_radius',   'type' => 'range',  'label' => olo_t( 'Arrotondamento' ) ],
            [ 'key' => 'padding',         'type' => 'range',  'label' => olo_t( 'Padding' ) ],
            [ 'key' => 'same_taxonomy',   'type' => 'toggle', 'label' => olo_t( 'Stesso termine tassonomia' ) ],
            [ 'key' => 'taxonomy',        'type' => 'text',   'label' => olo_t( 'Tassonomia' ) ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $same_tax = filter_var( $s['same_taxonomy'], FILTER_VALIDATE_BOOLEAN );
        $taxonomy = sanitize_text_field( $s['taxonomy'] ?: 'category' );

        $prev_post = get_previous_post( $same_tax, '', $taxonomy );
        $next_post = get_next_post( $same_tax, '', $taxonomy );

        // Nulla da mostrare
        if ( ! $prev_post && ! $next_post ) {
            return '';
        }

        $uid = 'olo-pnav-' . wp_rand( 10000, 99999 );

        $show_thumb = filter_var( $s['show_thumbnail'], FILTER_VALIDATE_BOOLEAN );
        $show_label = filter_var( $s['show_label'], FILTER_VALIDATE_BOOLEAN );
        $show_title = filter_var( $s['show_title'], FILTER_VALIDATE_BOOLEAN );
        $title_len  = max( 10, intval( $s['title_length'] ) );
        $layout     = $s['layout'] === 'stacked' ? 'column' : 'row';
        $gap        = intval( $s['gap'] ) ?: 20;
        $thumb_size = intval( $s['thumbnail_size'] ) ?: 60;
        $padding = Olo_Tile_Utils::spacing_css( $s['tile_padding'] ?? $s['padding'] ?? 16, 16 );
        $radius     = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );

        $text_clr   = $this->safe_color_css( $s['text_color'] ) ?: '#F3F4F6';
        $link_clr   = $this->safe_color_css( $s['link_color'] ) ?: '#93C5FD';
        $hover_clr  = $this->safe_color_css( $s['hover_color'] ) ?: '#60A5FA';
        $bg_clr     = $this->safe_color_css( $s['background_color'] ) ?: 'var(--olo-color-muted, #F3F4F6)';

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                display: flex;
                flex-direction: <?php echo $layout; ?>;
                gap: <?php echo $gap; ?>px;
            }
            .<?php echo $uid; ?> .olo-pnav-card {
                display: flex;
                align-items: center;
                gap: 12px;
                flex: 1;
                min-width: 0;
                padding: <?php echo $padding; ?>;
                border-radius: <?php echo $radius; ?>;
                background: <?php echo $bg_clr; ?>;
                text-decoration: none !important;
                transition: box-shadow 0.3s, transform 0.3s;
            }
            <?php if ( $radius_hover_css !== '' ) : ?>.<?php echo $uid; ?> .olo-pnav-card{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?> .olo-pnav-card:hover{border-radius:<?php echo $radius_hover_css; ?> !important}<?php endif; ?>
            .<?php echo $uid; ?> .olo-pnav-card:hover {
                box-shadow: 0 4px 16px rgba(0,0,0,.25);
                transform: translateY(-2px);
            }
            .<?php echo $uid; ?> .olo-pnav-label {
                font-size: 11px;
                color: <?php echo $text_clr; ?>;
                opacity: 0.7;
                margin-bottom: 2px;
            }
            .<?php echo $uid; ?> .olo-pnav-title {
                font-size: 14px;
                font-weight: 600;
                color: <?php echo $link_clr; ?>;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                line-height: 1.3;
            }
            .<?php echo $uid; ?> .olo-pnav-card:hover .olo-pnav-title {
                color: <?php echo $hover_clr; ?>;
            }
            .<?php echo $uid; ?> .olo-pnav-thumb {
                width: <?php echo $thumb_size; ?>px;
                height: <?php echo $thumb_size; ?>px;
                border-radius: <?php echo $radius; ?>;
                overflow: hidden;
                flex-shrink: 0;
                background: var(--olo-color-secondary, #1F2937);
            }
            .<?php echo $uid; ?> .olo-pnav-thumb img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }
            .<?php echo $uid; ?> .olo-pnav-text {
                flex: 1;
                min-width: 0;
            }
            .<?php echo $uid; ?> .olo-pnav-card--next .olo-pnav-text {
                text-align: right;
            }
            @media (max-width: 640px) {
                .<?php echo $uid; ?> {
                    flex-direction: column;
                }
            }
        </style>
        <nav class="olo-postnavigation <?php echo esc_attr( $uid ); ?>" aria-label="<?php echo esc_attr( olo_t( 'Navigazione articoli' ) ); ?>">
            <?php if ( $prev_post ) :
                $prev_url   = get_permalink( $prev_post );
                $prev_title = mb_strimwidth( get_the_title( $prev_post ), 0, $title_len, '...' );
                $prev_thumb = $show_thumb ? get_the_post_thumbnail_url( $prev_post, 'thumbnail' ) : '';
            ?>
            <a href="<?php echo esc_url( $prev_url ); ?>" class="olo-pnav-card olo-pnav-card--prev">
                <?php if ( $show_thumb ) : ?>
                <div class="olo-pnav-thumb">
                    <?php if ( $prev_thumb ) : ?>
                        <img src="<?php echo esc_url( $prev_thumb ); ?>" alt="<?php echo esc_attr( $prev_title ); ?>" loading="lazy" />
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <div class="olo-pnav-text">
                    <?php if ( $show_label ) : ?>
                    <div class="olo-pnav-label">&larr; <?php echo esc_html( $s['prev_label'] ?: olo_t( 'Precedente' ) ); ?></div>
                    <?php endif; ?>
                    <?php if ( $show_title ) : ?>
                    <div class="olo-pnav-title"><?php echo esc_html( $prev_title ); ?></div>
                    <?php endif; ?>
                </div>
            </a>
            <?php else : ?>
            <div class="olo-pnav-card" style="visibility:hidden"></div>
            <?php endif; ?>

            <?php if ( $next_post ) :
                $next_url   = get_permalink( $next_post );
                $next_title = mb_strimwidth( get_the_title( $next_post ), 0, $title_len, '...' );
                $next_thumb = $show_thumb ? get_the_post_thumbnail_url( $next_post, 'thumbnail' ) : '';
            ?>
            <a href="<?php echo esc_url( $next_url ); ?>" class="olo-pnav-card olo-pnav-card--next">
                <div class="olo-pnav-text">
                    <?php if ( $show_label ) : ?>
                    <div class="olo-pnav-label"><?php echo esc_html( $s['next_label'] ?: olo_t( 'Successivo' ) ); ?> &rarr;</div>
                    <?php endif; ?>
                    <?php if ( $show_title ) : ?>
                    <div class="olo-pnav-title"><?php echo esc_html( $next_title ); ?></div>
                    <?php endif; ?>
                </div>
                <?php if ( $show_thumb ) : ?>
                <div class="olo-pnav-thumb">
                    <?php if ( $next_thumb ) : ?>
                        <img src="<?php echo esc_url( $next_thumb ); ?>" alt="<?php echo esc_attr( $next_title ); ?>" loading="lazy" />
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </a>
            <?php else : ?>
            <div class="olo-pnav-card" style="visibility:hidden"></div>
            <?php endif; ?>
        </nav>
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
