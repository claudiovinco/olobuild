<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Pricelist_Tile extends Olo_Tile_Base {

    protected $type     = 'pricelist';
    protected $name     = 'Lista prezzi';
    protected $icon     = 'dashicons-list-view';
    protected $category = 'content';
    protected $defaults = [
        'items' => [
            [ 'title' => 'Bruschetta', 'description' => 'Pomodoro fresco, basilico e olio EVO', 'price' => '€8', 'image_url' => '', 'highlighted' => false, 'badge' => '' ],
            [ 'title' => 'Risotto ai funghi porcini', 'description' => 'Riso Carnaroli mantecato con porcini freschi', 'price' => '€14', 'image_url' => '', 'highlighted' => false, 'badge' => '' ],
            [ 'title' => 'Tiramisù', 'description' => 'Mascarpone, savoiardi e caffè espresso', 'price' => '€7', 'image_url' => '', 'highlighted' => false, 'badge' => 'Consigliato' ],
        ],
        'separator_style'      => 'dotted',
        'separator_color'      => '',
        'title_color'          => '',
        'price_color'          => '',
        'description_color'    => '',
        'image_size'           => '60',
        'image_border_radius'  => '8',
        'show_image'           => true,
        'price_position'       => 'right',
        'highlighted_bg'       => '',
        'badge_bg'             => '',
        'badge_color'          => '',
        'badge_border_color'   => '',
        'badge_border_width'   => '0',
        'badge_border_style'   => 'solid',
        'badge_border_radius'  => '6',
        'gap'                  => '12',
        'padding'              => '14',
        'card_bg'              => '',
        'card_border_radius'   => '12',
        'card_border_color'    => '',
        'hover_lift'           => true,
            'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() { return []; }

    public function render( $settings ) {
        $s     = wp_parse_args( $settings, $this->defaults );
        $items = $this->parse_items( $s['items'] );

        if ( empty( $items ) ) {
            return '';
        }

        $uid = 'olo-pl-' . wp_rand( 10000, 99999 );

        $sep_style   = in_array( $s['separator_style'], [ 'dotted', 'dashed', 'solid', 'none' ] ) ? $s['separator_style'] : 'dotted';
        $sep_color   = $this->safe_color_css( $s['separator_color'] ) ?: 'rgba(0, 0, 0, 0.06)';
        $title_clr   = $this->safe_color_css( $s['title_color'] ) ?: 'var(--olo-color-text, #1a1a1a)';
        $price_clr   = $this->safe_color_css( $s['price_color'] ) ?: 'var(--olo-color-primary, #e8622a)';
        $desc_clr    = $this->safe_color_css( $s['description_color'] ) ?: 'var(--olo-color-text-muted, #888)';
        $img_size    = intval( $s['image_size'] ) ?: 60;
        $img_radius  = Olo_Tile_Utils::border_radius( $s['image_border_radius'] ?? 0 );
        $img_radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['image_border_radius_hover'] ?? null );
        $show_image  = filter_var( $s['show_image'], FILTER_VALIDATE_BOOLEAN );
        $price_pos   = $s['price_position'] ?: 'right';
        $hl_bg       = $this->safe_color_css( $s['highlighted_bg'] ) ?: 'rgba(232, 98, 42, 0.06)';
        $badge_bg    = $this->safe_color_css( $s['badge_bg'] ) ?: 'var(--olo-color-primary, #e8622a)';
        $badge_clr   = $this->safe_color_css( $s['badge_color'] ) ?: '#fff';
        $badge_bw    = intval( $s['badge_border_width'] );
        $badge_bs    = in_array( $s['badge_border_style'], [ 'solid', 'dashed', 'dotted' ] ) ? $s['badge_border_style'] : 'solid';
        $badge_bc    = $this->safe_color_css( $s['badge_border_color'] ) ?: 'var(--olo-color-primary, #e8622a)';
        $badge_br    = Olo_Tile_Utils::radius_int( $s['badge_border_radius'] ?? 6 );
        $gap         = intval( $s['gap'] ) ?: 12;
        $padding = Olo_Tile_Utils::spacing_css( $s['tile_padding'] ?? $s['padding'] ?? 14, 14 );
        $card_bg     = $this->safe_color_css( $s['card_bg'] ?? '' ) ?: 'rgba(255, 255, 255, 0.8)';
        $card_radius = Olo_Tile_Utils::radius_int( $s['card_border_radius'] ?? 12 );
        $card_border = $this->safe_color_css( $s['card_border_color'] ?? '' ) ?: 'rgba(0, 0, 0, 0.06)';
        $hover_lift  = filter_var( $s['hover_lift'] ?? true, FILTER_VALIDATE_BOOLEAN );
        $hl_border   = $this->safe_color_css( $s['highlighted_bg'] ) ? $this->safe_color_css( $s['highlighted_bg'] ) : 'rgba(232, 98, 42, 0.2)';

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                display: flex;
                flex-direction: column;
                gap: <?php echo $gap; ?>px;
            }
            .<?php echo $uid; ?> .olo-pl-card {
                display: flex;
                align-items: center;
                gap: 14px;
                padding: <?php echo $padding; ?>;
                border-radius: <?php echo $card_radius; ?>px;
                background: <?php echo $card_bg; ?>;
                border: 1px solid <?php echo $card_border; ?>;
                transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
                position: relative;
                overflow: hidden;
            }
            <?php if ( $hover_lift ) : ?>
            .<?php echo $uid; ?> .olo-pl-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
                border-color: rgba(0, 0, 0, 0.12);
            }
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-pl-card--hl {
                background: <?php echo $hl_bg; ?>;
                border-color: <?php echo $hl_border; ?>;
            }
            <?php if ( $hover_lift ) : ?>
            .<?php echo $uid; ?> .olo-pl-card--hl:hover {
                border-color: rgba(232, 98, 42, 0.35);
            }
            <?php endif; ?>
            <?php if ( $show_image ) : ?>
            .<?php echo $uid; ?> .olo-pl-img {
                width: <?php echo $img_size; ?>px;
                height: <?php echo $img_size; ?>px;
                border-radius: <?php echo $img_radius; ?>;
                overflow: hidden;
                flex-shrink: 0;
                background: rgba(0, 0, 0, 0.03);
            }
            <?php if ( $img_radius_hover_css !== '' ) : ?>.<?php echo $uid; ?> .olo-pl-img{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?> .olo-pl-img:hover{border-radius:<?php echo $img_radius_hover_css; ?> !important}<?php endif; ?>
            .<?php echo $uid; ?> .olo-pl-img img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-pl-body {
                flex: 1;
                min-width: 0;
            }
            .<?php echo $uid; ?> .olo-pl-top {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 12px;
            }
            .<?php echo $uid; ?> .olo-pl-title {
                font-weight: 600;
                font-size: 15px;
                color: <?php echo $title_clr; ?>;
                display: inline;
                letter-spacing: -0.01em;
            }
            .<?php echo $uid; ?> .olo-pl-desc {
                font-size: 13px;
                color: <?php echo $desc_clr; ?>;
                margin-top: 4px;
                line-height: 1.5;
            }
            .<?php echo $uid; ?> .olo-pl-badge {
                display: inline-flex;
                align-items: center;
                background: <?php echo $badge_bg; ?>;
                color: <?php echo $badge_clr; ?>;
                font-size: 9px;
                font-weight: 700;
                padding: 3px 7px;
                border-radius: <?php echo $badge_br; ?>px;
                text-transform: uppercase;
                margin-left: 8px;
                vertical-align: middle;
                line-height: 1;
                letter-spacing: 0.04em;
                <?php if ( $badge_bw > 0 ) : ?>
                border: <?php echo $badge_bw; ?>px <?php echo $badge_bs; ?> <?php echo $badge_bc; ?>;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-pl-price {
                color: <?php echo $price_clr; ?>;
                font-weight: 700;
                font-size: 17px;
                white-space: nowrap;
                flex-shrink: 0;
                letter-spacing: -0.02em;
            }
            .<?php echo $uid; ?> .olo-pl-price--below {
                font-size: 15px;
                margin-top: 6px;
            }
            <?php if ( $sep_style !== 'none' ) : ?>
            .<?php echo $uid; ?> .olo-pl-sep {
                position: absolute;
                bottom: 0;
                left: <?php echo $padding; ?>px;
                right: <?php echo $padding; ?>px;
                border-bottom: 1px <?php echo $sep_style; ?> <?php echo $sep_color; ?>;
                pointer-events: none;
            }
            .<?php echo $uid; ?> .olo-pl-card:last-child .olo-pl-sep {
                display: none;
            }
            <?php endif; ?>
            @media (max-width: 480px) {
                .<?php echo $uid; ?> .olo-pl-top {
                    flex-direction: column;
                    gap: 4px;
                }
                .<?php echo $uid; ?> .olo-pl-card {
                    flex-wrap: wrap;
                }
            }
        </style>
        <div class="olo-pricelist <?php echo esc_attr( $uid ); ?>">
            <?php foreach ( $items as $item ) :
                $highlighted = filter_var( $item['highlighted'], FILTER_VALIDATE_BOOLEAN );
                $hl_class    = $highlighted ? ' olo-pl-card--hl' : '';
            ?>
            <div class="olo-pl-card<?php echo $hl_class; ?>">
                <?php if ( $show_image ) : ?>
                <div class="olo-pl-img">
                    <?php if ( ! empty( $item['image_url'] ) ) : ?>
                        <img src="<?php echo esc_url( $item['image_url'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" loading="lazy" />
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="olo-pl-body">
                    <div class="olo-pl-top">
                        <div class="olo-pl-info">
                            <?php list( $plt_cls, $plt_data ) = $this->tfx_attrs( $s, "title", $item["title"] ); ?><span class="olo-pl-title<?php echo $plt_cls; ?>"<?php echo $plt_data; ?>><?php echo esc_html( $item["title"] ); ?></span>
                            <?php if ( ! empty( $item['badge'] ) ) : ?>
                                <span class="olo-pl-badge"><?php echo esc_html( $item['badge'] ); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if ( $price_pos === 'right' ) : ?>
                            <div class="olo-pl-price"><?php echo esc_html( $item['price'] ); ?></div>
                        <?php endif; ?>
                    </div>
                    <?php if ( ! empty( $item['description'] ) ) : ?>
                    <?php list( $pld_cls, $pld_data ) = $this->tfx_attrs( $s, "description", $item["description"] ); ?><div class="olo-pl-desc<?php echo $pld_cls; ?>"<?php echo $pld_data; ?>><?php echo esc_html( $item["description"] ); ?></div>
                    <?php endif; ?>
                    <?php if ( $price_pos === 'below' ) : ?>
                    <div class="olo-pl-price olo-pl-price--below"><?php echo esc_html( $item['price'] ); ?></div>
                    <?php endif; ?>
                </div>

                <?php if ( $sep_style !== 'none' ) : ?>
                <div class="olo-pl-sep"></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
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

    private function parse_items( $raw ) {
        if ( ! is_array( $raw ) ) {
            return [];
        }
        $items = [];
        foreach ( $raw as $item ) {
            if ( is_array( $item ) ) {
                $items[] = [
                    'title'       => $item['title'] ?? '',
                    'description' => $item['description'] ?? '',
                    'price'       => $item['price'] ?? '',
                    'image_url'   => $item['image_url'] ?? '',
                    'highlighted' => $item['highlighted'] ?? false,
                    'badge'       => $item['badge'] ?? '',
                ];
            }
        }
        return $items;
    }
}
