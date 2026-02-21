<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_IconBox_Tile extends Olo_Tile_Base {

    protected $type     = 'iconbox';
    protected $name     = 'Icon Box';
    protected $icon     = 'dashicons-star-filled';
    protected $category = 'content';
    protected $defaults = [
        'icon_emoji'        => "\xF0\x9F\x9A\x80",
        'title'             => 'Feature Title',
        'description'       => 'A short description of this feature and why it matters.',
        'link_url'          => '',
        'link_text'         => 'Learn more',
        'alignment'         => 'center',
        'text_color'        => '#F3F4F6',
        'icon_size'         => '3',
        'title_font_size'   => '20',
        'title_font_weight' => '600',
        'link_color'        => '#6366F1',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'icon_emoji',  'type' => 'text',     'label' => 'Icon (emoji)' ],
            [ 'key' => 'title',       'type' => 'text',     'label' => 'Title' ],
            [ 'key' => 'description', 'type' => 'textarea', 'label' => 'Description' ],
            [ 'key' => 'link_url',    'type' => 'text',     'label' => 'Link URL' ],
            [ 'key' => 'link_text',   'type' => 'text',     'label' => 'Link Text' ],
            [ 'key' => 'alignment',   'type' => 'select',   'label' => 'Alignment', 'options' => [ 'left' => 'Left', 'center' => 'Center', 'right' => 'Right' ] ],
            [ 'key' => 'text_color',  'type' => 'color',    'label' => 'Text Color' ],
        ];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'mib-' . wp_rand( 10000, 99999 );

        $fg         = $this->safe_color( $s['text_color'] );
        $link_clr   = $this->safe_color( $s['link_color'] ) ?: '#6366F1';
        $icon_size  = floatval( $s['icon_size'] ) ?: 3;
        $title_fs   = absint( $s['title_font_size'] ) ?: 20;
        $title_fw   = absint( $s['title_font_weight'] ) ?: 600;
        $align_class = 'uk-text-' . esc_attr( $s['alignment'] );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                <?php if ( $fg ) : ?>color: <?php echo $fg; ?>;<?php endif; ?>
            }
            .<?php echo $uid; ?> .mib-title {
                font-size: <?php echo $title_fs; ?>px;
                font-weight: <?php echo $title_fw; ?>;
                margin: 0 0 8px;
            }
            .<?php echo $uid; ?> .mib-link {
                color: <?php echo $link_clr; ?> !important;
                text-decoration: none !important;
                font-weight: 500;
            }
            .<?php echo $uid; ?> .mib-link:hover {
                text-decoration: underline !important;
            }
        </style>
        <div class="olo-iconbox uk-card uk-card-body <?php echo $align_class; ?> <?php echo esc_attr( $uid ); ?>">
            <?php if ( ! empty( $s['icon_emoji'] ) ) : ?>
                <div style="font-size: <?php echo esc_attr( $icon_size ); ?>em; margin-bottom: 16px;">
                    <?php if ( preg_match( '/^[a-z][a-z0-9-]*$/', $s['icon_emoji'] ) ) : ?>
                        <span style="color:inherit;" uk-icon="icon: <?php echo esc_attr( $s['icon_emoji'] ); ?>; ratio: <?php echo esc_attr( $icon_size ); ?>"></span>
                    <?php else : ?>
                        <?php echo esc_html( $s['icon_emoji'] ); ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <h3 class="mib-title"><?php echo $this->sanitize_richtext( $s['title'] ); ?></h3>
            <div style="margin: 0 0 16px; opacity: 0.8; line-height: 1.6;"><?php echo wp_kses_post( $s['description'] ); ?></div>
            <?php if ( ! empty( $s['link_url'] ) ) : ?>
                <a href="<?php echo esc_url( $s['link_url'] ); ?>" class="mib-link" style="color:<?php echo $link_clr; ?>"><?php echo wp_kses_post( $s['link_text'] ); ?> &rarr;</a>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
