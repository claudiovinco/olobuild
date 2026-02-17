<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Counter_Tile extends Olo_Tile_Base {

    protected $type     = 'counter';
    protected $name     = 'Contatore';
    protected $icon     = 'dashicons-performance';
    protected $category = 'content';
    protected $defaults = [
        'number'             => '1250',
        'label'              => 'Happy Clients',
        'prefix'             => '',
        'suffix'             => '+',
        'icon_emoji'         => "\xF0\x9F\x8F\x86",
        'text_color'         => '#F3F4F6',
        'number_font_size'   => '48',
        'number_font_weight' => '700',
        'label_color'        => '',
        'label_font_size'    => '14',
        'label_font_weight'  => '400',
        'padding'            => '32',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'number',     'type' => 'text',  'label' => 'Number' ],
            [ 'key' => 'label',      'type' => 'text',  'label' => 'Label' ],
            [ 'key' => 'prefix',     'type' => 'text',  'label' => 'Prefix' ],
            [ 'key' => 'suffix',     'type' => 'text',  'label' => 'Suffix' ],
            [ 'key' => 'icon_emoji', 'type' => 'text',  'label' => 'Icon (emoji)' ],
            [ 'key' => 'text_color', 'type' => 'color', 'label' => 'Text Color' ],
        ];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'mcnt-' . wp_rand( 10000, 99999 );

        $fg       = $this->safe_color( $s['text_color'] );
        $lbl_clr  = $this->safe_color( $s['label_color'] ) ?: $fg;
        $num_fs   = absint( $s['number_font_size'] );
        $num_fw   = absint( $s['number_font_weight'] );
        $lbl_fs   = absint( $s['label_font_size'] );
        $lbl_fw   = absint( $s['label_font_weight'] );
        $pad      = absint( $s['padding'] );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                text-align: center;
                padding: <?php echo $pad; ?>px;
                <?php if ( $fg ) : ?>color: <?php echo $fg; ?>;<?php endif; ?>
            }
            .<?php echo $uid; ?> .mcnt-number {
                font-size: <?php echo $num_fs; ?>px;
                font-weight: <?php echo $num_fw; ?>;
                line-height: 1.1;
            }
            .<?php echo $uid; ?> .mcnt-label {
                font-size: <?php echo $lbl_fs; ?>px;
                font-weight: <?php echo $lbl_fw; ?>;
                margin-top: 8px;
                opacity: 0.7;
                <?php if ( $lbl_clr && $lbl_clr !== $fg ) : ?>color: <?php echo $lbl_clr; ?>; opacity: 1;<?php endif; ?>
            }
        </style>
        <div class="olo-counter <?php echo esc_attr( $uid ); ?>">
            <?php if ( ! empty( $s['icon_emoji'] ) ) : ?>
                <div style="font-size: 2.5em; margin-bottom: 8px;">
                    <?php if ( preg_match( '/^[a-z][a-z0-9-]*$/', $s['icon_emoji'] ) ) : ?>
                        <span style="color:inherit;" uk-icon="icon: <?php echo esc_attr( $s['icon_emoji'] ); ?>; ratio: 2.5"></span>
                    <?php else : ?>
                        <?php echo esc_html( $s['icon_emoji'] ); ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="mcnt-number">
                <?php echo esc_html( $s['prefix'] ); ?><?php echo esc_html( $s['number'] ); ?><?php echo esc_html( $s['suffix'] ); ?>
            </div>
            <?php if ( ! empty( $s['label'] ) ) : ?>
                <div class="mcnt-label"><?php echo wp_kses_post( $s['label'] ); ?></div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
