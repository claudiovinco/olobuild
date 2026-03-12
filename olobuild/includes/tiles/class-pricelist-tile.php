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
        'badge_border_radius'  => '4',
        'gap'                  => '16',
        'padding'              => '12',
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
        $sep_color   = $this->safe_color_css( $s['separator_color'] ) ?: 'var(--olo-color-border, #E5E7EB)';
        $title_clr   = $this->safe_color_css( $s['title_color'] ) ?: 'var(--olo-color-text, #374151)';
        $price_clr   = $this->safe_color_css( $s['price_color'] ) ?: 'var(--olo-color-primary, #6366F1)';
        $desc_clr    = $this->safe_color_css( $s['description_color'] ) ?: 'var(--olo-color-text-muted, #9CA3AF)';
        $img_size    = intval( $s['image_size'] ) ?: 60;
        $img_radius  = Olo_Tile_Utils::border_radius( $s['image_border_radius'] ?? 0 );
        $show_image  = filter_var( $s['show_image'], FILTER_VALIDATE_BOOLEAN );
        $price_pos   = $s['price_position'] ?: 'right';
        $hl_bg       = $this->safe_color_css( $s['highlighted_bg'] ) ?: 'var(--olo-color-muted, #F3F4F6)';
        $badge_bg    = $this->safe_color_css( $s['badge_bg'] ) ?: 'var(--olo-color-primary, #6366F1)';
        $badge_clr   = $this->safe_color_css( $s['badge_color'] ) ?: 'var(--olo-color-primary-contrast, #FFFFFF)';
        $badge_bw    = intval( $s['badge_border_width'] );
        $badge_bs    = in_array( $s['badge_border_style'], [ 'solid', 'dashed', 'dotted' ] ) ? $s['badge_border_style'] : 'solid';
        $badge_bc    = $this->safe_color_css( $s['badge_border_color'] ) ?: 'var(--olo-color-primary, #6366F1)';
        $badge_br    = intval( $s['badge_border_radius'] ?? 4 );
        $gap         = intval( $s['gap'] ) ?: 16;
        $padding     = intval( $s['padding'] ) ?: 12;

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                display: flex;
                flex-direction: column;
                gap: <?php echo $gap; ?>px;
            }
            .<?php echo $uid; ?> .olo-pl-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: <?php echo $padding; ?>px;
                border-radius: 6px;
                transition: background 0.2s;
            }
            .<?php echo $uid; ?> .olo-pl-item--hl {
                background: <?php echo $hl_bg; ?>;
            }
            <?php if ( $show_image ) : ?>
            .<?php echo $uid; ?> .olo-pl-img {
                width: <?php echo $img_size; ?>px;
                height: <?php echo $img_size; ?>px;
                border-radius: <?php echo $img_radius; ?>;
                overflow: hidden;
                flex-shrink: 0;
            }
            .<?php echo $uid; ?> .olo-pl-img img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-pl-content {
                flex: 1;
                min-width: 0;
            }
            .<?php echo $uid; ?> .olo-pl-title {
                font-weight: 600;
                font-size: 15px;
                color: <?php echo $title_clr; ?>;
                display: inline;
            }
            .<?php echo $uid; ?> .olo-pl-desc {
                font-size: 13px;
                color: <?php echo $desc_clr; ?>;
                margin-top: 2px;
                line-height: 1.4;
            }
            .<?php echo $uid; ?> .olo-pl-badge {
                display: inline-block;
                background: <?php echo $badge_bg; ?>;
                color: <?php echo $badge_clr; ?>;
                font-size: 9px;
                font-weight: 700;
                padding: 2px 6px;
                border-radius: <?php echo $badge_br; ?>px;
                text-transform: uppercase;
                margin-left: 6px;
                vertical-align: middle;
                line-height: 1;
                <?php if ( $badge_bw > 0 ) : ?>
                border: <?php echo $badge_bw; ?>px <?php echo $badge_bs; ?> <?php echo $badge_bc; ?>;
                <?php endif; ?>
            }
            <?php if ( $price_pos === 'right' ) : ?>
            .<?php echo $uid; ?> .olo-pl-sep {
                flex: 1;
                min-width: 20px;
                align-self: center;
                <?php if ( $sep_style !== 'none' ) : ?>
                border-bottom: 1px <?php echo $sep_style; ?> <?php echo $sep_color; ?>;
                <?php endif; ?>
            }
            <?php endif; ?>
            .<?php echo $uid; ?> .olo-pl-price {
                color: <?php echo $price_clr; ?>;
                font-weight: 700;
                font-size: 16px;
                white-space: nowrap;
                flex-shrink: 0;
            }
            .<?php echo $uid; ?> .olo-pl-price--below {
                color: <?php echo $price_clr; ?>;
                font-weight: 700;
                font-size: 15px;
                margin-top: 4px;
            }
            @media (max-width: 480px) {
                .<?php echo $uid; ?> .olo-pl-sep {
                    display: none;
                }
                .<?php echo $uid; ?> .olo-pl-item {
                    flex-wrap: wrap;
                }
            }
        </style>
        <div class="olo-pricelist <?php echo esc_attr( $uid ); ?>">
            <?php foreach ( $items as $item ) :
                $highlighted = filter_var( $item['highlighted'], FILTER_VALIDATE_BOOLEAN );
                $hl_class    = $highlighted ? ' olo-pl-item--hl' : '';
            ?>
            <div class="olo-pl-item<?php echo $hl_class; ?>">
                <?php if ( $show_image ) : ?>
                <div class="olo-pl-img">
                    <?php if ( ! empty( $item['image_url'] ) ) : ?>
                        <img src="<?php echo esc_url( $item['image_url'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" loading="lazy" />
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="olo-pl-content">
                    <div>
                        <span class="olo-pl-title"><?php echo esc_html( $item['title'] ); ?></span>
                        <?php if ( ! empty( $item['badge'] ) ) : ?>
                            <span class="olo-pl-badge"><?php echo esc_html( $item['badge'] ); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if ( ! empty( $item['description'] ) ) : ?>
                    <div class="olo-pl-desc"><?php echo esc_html( $item['description'] ); ?></div>
                    <?php endif; ?>
                    <?php if ( $price_pos === 'below' ) : ?>
                    <div class="olo-pl-price--below"><?php echo esc_html( $item['price'] ); ?></div>
                    <?php endif; ?>
                </div>

                <?php if ( $price_pos === 'right' ) : ?>
                    <?php if ( $sep_style !== 'none' ) : ?>
                    <div class="olo-pl-sep"></div>
                    <?php endif; ?>
                    <div class="olo-pl-price"><?php echo esc_html( $item['price'] ); ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
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
