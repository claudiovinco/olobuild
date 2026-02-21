<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_SwitcherPanel_Tile extends Olo_Tile_Base {

    protected $type     = 'switcherpanel';
    protected $name     = 'Switcher Panel';
    protected $icon     = 'dashicons-images-alt';
    protected $category = 'content';
    protected $defaults = [
        'items' => [
            [ 'id' => 'sp-1', 'nav_label' => 'About Us', 'title' => 'About Us', 'text' => 'Lorem ipsum dolor sit amet.', 'button_text' => 'READ MORE', 'button_url' => '#', 'image' => '' ],
            [ 'id' => 'sp-2', 'nav_label' => 'Bar & Cocktails', 'title' => 'Bar & Cocktails', 'text' => 'Ut enim ad minim veniam.', 'button_text' => 'READ MORE', 'button_url' => '#', 'image' => '' ],
        ],
        'hero_image'      => '',
        'hero_height'     => '400',
        'image_position'  => 'right',
        'nav_style'       => 'minimal',
        'animation'       => 'fade',
        'content_padding' => '40',
        'title_tag'       => 'h3',
        'button_style'    => 'default',
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s     = wp_parse_args( $settings, $this->defaults );
        $items = $this->parse_items( $s['items'] );
        $count = count( $items );

        if ( $count === 0 ) {
            return '';
        }

        $uid          = 'olo-sp-' . wp_rand( 10000, 99999 );
        $hero_image   = $s['hero_image'] ?? '';
        $hero_height  = absint( $s['hero_height'] ) ?: 400;
        $img_position = $s['image_position'] === 'left' ? 'left' : 'right';
        $nav_style    = $s['nav_style'] ?? 'minimal';
        $animation    = $s['animation'] ?? 'fade';
        $padding      = absint( $s['content_padding'] ?? 40 );
        $title_tag    = in_array( $s['title_tag'], [ 'h2', 'h3', 'h4' ], true ) ? $s['title_tag'] : 'h3';
        $btn_style    = $s['button_style'] ?? 'default';

        // UIkit button class
        $btn_class_map = [
            'default'   => 'uk-button uk-button-default',
            'primary'   => 'uk-button uk-button-primary',
            'secondary' => 'uk-button uk-button-secondary',
            'text'      => 'uk-button uk-button-text',
        ];
        $btn_class = $btn_class_map[ $btn_style ] ?? 'uk-button uk-button-default';

        // Switcher attribute
        $switcher_attr = 'connect: #' . $uid . '-content';
        if ( ! empty( $animation ) ) {
            $switcher_attr .= '; animation: uk-animation-' . esc_attr( $animation );
        }

        // Nav classes
        $nav_cls = 'olo-sp-nav';
        if ( $nav_style === 'minimal' ) {
            $nav_cls .= ' olo-sp-nav--minimal';
        } elseif ( $nav_style === 'pills' ) {
            $nav_cls .= ' uk-subnav uk-subnav-pill';
        } else {
            $nav_cls .= ' uk-subnav';
        }

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> .olo-sp-hero { height: <?php echo $hero_height; ?>px; }
            .<?php echo $uid; ?> .olo-sp-panel-body { padding: <?php echo $padding; ?>px; }
        </style>
        <div class="olo-switcherpanel <?php echo $uid; ?>">
            <!-- Hero image with nav overlay -->
            <div class="olo-sp-hero">
                <?php if ( ! empty( $hero_image ) ) : ?>
                    <img src="<?php echo esc_url( $hero_image ); ?>" alt="" class="olo-sp-hero__img" loading="lazy">
                <?php else : ?>
                    <div class="olo-sp-hero__placeholder"></div>
                <?php endif; ?>

                <!-- Navigation -->
                <ul class="<?php echo esc_attr( $nav_cls ); ?>" uk-switcher="<?php echo esc_attr( $switcher_attr ); ?>">
                    <?php foreach ( $items as $i => $item ) : ?>
                    <li<?php echo $i === 0 ? ' class="uk-active"' : ''; ?>>
                        <a href="#"><?php echo esc_html( $item['nav_label'] ); ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Switcher content panels -->
            <ul id="<?php echo esc_attr( $uid . '-content' ); ?>" class="uk-switcher olo-sp-panels">
                <?php foreach ( $items as $item ) : ?>
                <li>
                    <div class="olo-sp-panel uk-grid-medium <?php echo $img_position === 'left' ? 'uk-flex-row-reverse' : ''; ?>" uk-grid>
                        <div class="uk-width-expand">
                            <div class="olo-sp-panel-body">
                                <<?php echo $title_tag; ?> class="olo-sp-panel__title"><?php echo wp_kses_post( $item['title'] ); ?></<?php echo $title_tag; ?>>
                                <div class="olo-sp-panel__text"><?php echo wp_kses_post( $item['text'] ); ?></div>
                                <?php if ( ! empty( $item['button_text'] ) ) : ?>
                                    <a href="<?php echo esc_url( $item['button_url'] ); ?>" class="<?php echo esc_attr( $btn_class ); ?>">
                                        <?php echo esc_html( $item['button_text'] ); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if ( ! empty( $item['image'] ) ) : ?>
                        <div class="uk-width-2-5@m">
                            <?php
                            $sp_img = '<img src="' . esc_url( $item['image'] ) . '" alt="' . esc_attr( wp_strip_all_tags( $item['title'] ) ) . '" class="olo-sp-panel__img" loading="lazy">';
                            echo $this->render_hover_wrap( $sp_img, $item['hover_image'] ?? '', $item['hover_video'] ?? '' );
                            ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
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
            if ( is_array( $item ) && ! empty( $item['nav_label'] ) ) {
                $items[] = [
                    'nav_label'   => $item['nav_label'] ?? '',
                    'title'       => $item['title'] ?? '',
                    'text'        => $item['text'] ?? '',
                    'button_text' => $item['button_text'] ?? '',
                    'button_url'  => $item['button_url'] ?? '#',
                    'image'       => $item['image'] ?? '',
                    'hover_image' => $item['hover_image'] ?? '',
                    'hover_video' => $item['hover_video'] ?? '',
                ];
            }
        }
        return $items;
    }
}
