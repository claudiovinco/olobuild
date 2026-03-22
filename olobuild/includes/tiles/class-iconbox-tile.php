<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_IconBox_Tile extends Olo_Tile_Base {

    protected $type     = 'iconbox';
    protected $name     = 'Icon Box';
    protected $icon     = 'dashicons-star-filled';
    protected $category = 'marketing';
    protected $defaults = [
        'icon_emoji'        => 'star',
        'title'             => 'Feature Title',
        'description'       => 'A short description of this feature and why it matters.',
        'link_url'          => '',
        'link_text'         => 'Learn more',
        'alignment'         => 'center',
        'text_color'        => '',
        'title_color'       => '',
        'icon_size'         => '3',
        'icon_position'     => 'top',
        'icon_bg_color'     => '',
        'icon_bg_shape'     => 'circle',
        'icon_color'        => '',
        'title_font_size'   => '20',
        'title_font_weight' => '600',
        'link_color'        => '',
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

        $fg         = $this->safe_color_css( $s['text_color'] );
        $title_clr  = $this->safe_color_css( $s['title_color'] ?? '' );
        $link_clr   = $this->safe_color_css( $s['link_color'] ) ?: 'var(--olo-color-primary, #6366F1)';
        $icon_size  = floatval( $s['icon_size'] ) ?: 3;
        $title_fs   = absint( $s['title_font_size'] ) ?: 20;
        $title_fw   = absint( $s['title_font_weight'] ) ?: 600;
        $align_class = 'uk-text-' . esc_attr( $s['alignment'] );
        $icon_pos   = in_array( $s['icon_position'], [ 'top', 'left', 'right' ], true ) ? $s['icon_position'] : 'top';
        $is_horiz   = ( $icon_pos === 'left' || $icon_pos === 'right' );
        $icon_bg    = $this->safe_color_css( $s['icon_bg_color'] );
        $icon_clr   = $this->safe_color_css( $s['icon_color'] );
        $icon_shape = in_array( $s['icon_bg_shape'], [ 'circle', 'square', 'rounded' ], true ) ? $s['icon_bg_shape'] : 'circle';

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                <?php if ( $fg ) : ?>color: <?php echo $fg; ?>;<?php endif; ?>
            }
            <?php if ( $is_horiz ) : ?>
            .<?php echo $uid; ?> .mib-flex {
                display: flex;
                flex-direction: <?php echo $icon_pos === 'right' ? 'row-reverse' : 'row'; ?>;
                align-items: flex-start;
                gap: 16px;
                text-align: left;
            }
            .<?php echo $uid; ?> .mib-icon-col { flex-shrink: 0; }
            .<?php echo $uid; ?> .mib-content-col { flex: 1; }
            <?php endif; ?>
            .<?php echo $uid; ?> .mib-title {
                font-size: <?php echo $title_fs; ?>px;
                font-weight: <?php echo $title_fw; ?>;
                margin: 0 0 8px;
                <?php if ( $title_clr ) : ?>color: <?php echo $title_clr; ?>;<?php endif; ?>
            }
            .<?php echo $uid; ?> .mib-link {
                color: <?php echo $link_clr; ?> !important;
                text-decoration: none !important;
                font-weight: 500;
            }
            .<?php echo $uid; ?> .mib-link:hover {
                text-decoration: underline !important;
            }
            <?php if ( $icon_bg ) : ?>
            .<?php echo $uid; ?> .mib-icon-bg {
                background: <?php echo $icon_bg; ?>;
                padding: 16px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: <?php echo $icon_shape === 'circle' ? '50%' : ( $icon_shape === 'rounded' ? '12px' : '4px' ); ?>;
            }
            <?php endif; ?>
        </style>
        <div class="olo-iconbox uk-card uk-card-body <?php echo $align_class; ?> <?php echo esc_attr( $uid ); ?>">
          <?php if ( $is_horiz ) : ?><div class="mib-flex"><?php endif; ?>

            <?php if ( ! empty( $s['icon_emoji'] ) ) : ?>
                <div class="<?php echo $is_horiz ? 'mib-icon-col' : ''; ?>" style="<?php echo $is_horiz ? '' : 'margin-bottom:16px;'; ?>">
                    <?php
                    $icon_inline = 'font-size:' . esc_attr( $icon_size ) . 'em;line-height:1;';
                    if ( $icon_clr ) { $icon_inline .= 'color:' . $icon_clr . ';'; }
                    ?>
                    <div style="<?php echo $icon_inline; ?>" <?php echo $icon_bg ? 'class="mib-icon-bg"' : ''; ?>>
                        <?php if ( preg_match( '/^[a-z][a-z0-9-]*$/', $s['icon_emoji'] ) ) : ?>
                            <span style="color:inherit;" uk-icon="icon: <?php echo esc_attr( $s['icon_emoji'] ); ?>; ratio: <?php echo esc_attr( $icon_size ); ?>"></span>
                        <?php else : ?>
                            <?php echo esc_html( $s['icon_emoji'] ); ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ( $is_horiz ) : ?><div class="mib-content-col"><?php endif; ?>
                <h3 class="mib-title"><?php echo esc_html( wp_strip_all_tags( $s['title'] ) ); ?></h3>
                <div style="margin: 0 0 16px; opacity: 0.8; line-height: 1.6;"><?php echo nl2br( esc_html( wp_strip_all_tags( $s['description'] ) ) ); ?></div>
                <?php if ( ! empty( $s['link_url'] ) ) : ?>
                    <a href="<?php echo esc_url( $s['link_url'] ); ?>" class="mib-link" style="color:<?php echo $link_clr; ?>"><?php echo esc_html( wp_strip_all_tags( $s['link_text'] ) ); ?> &rarr;</a>
                <?php endif; ?>
            <?php if ( $is_horiz ) : ?></div><?php endif; ?>

          <?php if ( $is_horiz ) : ?></div><?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
