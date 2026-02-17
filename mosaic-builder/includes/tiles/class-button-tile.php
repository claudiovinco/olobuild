<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Button_Tile extends Olo_Tile_Base {

    protected $type     = 'button';
    protected $name     = 'Pulsante';
    protected $icon     = 'dashicons-button';
    protected $category = 'content';
    protected $defaults = [
        'text'             => 'Click Here',
        'url'              => '#',
        'target'           => '_self',
        'alignment'        => 'center',
        'bg_color'         => '#6366F1',
        'text_color'       => '#FFFFFF',
        'border_radius'    => '6',
        'padding_x'        => '32',
        'padding_y'        => '14',
        'font_size'        => '16',
        'full_width'       => false,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'text',          'type' => 'text',   'label' => 'Button Text' ],
            [ 'key' => 'url',           'type' => 'text',   'label' => 'URL' ],
            [ 'key' => 'target',        'type' => 'select', 'label' => 'Open In', 'options' => [ '_self' => 'Same Window', '_blank' => 'New Tab' ] ],
            [ 'key' => 'alignment',     'type' => 'select', 'label' => 'Alignment', 'options' => [ 'left' => 'Left', 'center' => 'Center', 'right' => 'Right' ] ],
            [ 'key' => 'bg_color',      'type' => 'color',  'label' => 'Background Color' ],
            [ 'key' => 'text_color',    'type' => 'color',  'label' => 'Text Color' ],
            [ 'key' => 'border_radius', 'type' => 'range',  'label' => 'Border Radius (px)', 'min' => 0, 'max' => 50, 'step' => 2 ],
            [ 'key' => 'padding_x',     'type' => 'range',  'label' => 'Padding H (px)', 'min' => 8, 'max' => 80, 'step' => 4 ],
            [ 'key' => 'padding_y',     'type' => 'range',  'label' => 'Padding V (px)', 'min' => 4, 'max' => 40, 'step' => 2 ],
            [ 'key' => 'font_size',     'type' => 'range',  'label' => 'Font Size (px)', 'min' => 12, 'max' => 32, 'step' => 1 ],
            [ 'key' => 'full_width',    'type' => 'toggle', 'label' => 'Full Width' ],
        ];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'mbtn-' . wp_rand( 10000, 99999 );

        // Map alignment to UIkit text alignment classes
        $align_class = 'uk-text-' . $s['alignment'];
        $bg   = $this->safe_color( $s['bg_color'] );
        $fg   = $this->safe_color( $s['text_color'] );
        $rad_raw = $s['border_radius'];
        if ( is_array( $rad_raw ) ) {
            $rad_css = sprintf( '%dpx %dpx %dpx %dpx', absint( $rad_raw['tl'] ?? 0 ), absint( $rad_raw['tr'] ?? 0 ), absint( $rad_raw['br'] ?? 0 ), absint( $rad_raw['bl'] ?? 0 ) );
        } else {
            $rad_css = absint( $rad_raw ) . 'px';
        }
        $py   = absint( $s['padding_y'] );
        $px   = absint( $s['padding_x'] );
        $fs   = absint( $s['font_size'] );
        $fw   = $s['full_width'] ? 'width: 100%;' : '';

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> .olo-btn-link {
                display: inline-block;
                <?php echo $fw; ?>
                padding: <?php echo $py; ?>px <?php echo $px; ?>px;
                <?php if ( $bg ) : ?>background-color: <?php echo $bg; ?> !important;<?php endif; ?>
                <?php if ( $fg ) : ?>color: <?php echo $fg; ?> !important;<?php endif; ?>
                border-radius: <?php echo $rad_css; ?>;
                font-size: <?php echo $fs; ?>px;
                text-decoration: none !important;
                text-align: center;
                transition: filter .2s, transform .2s;
                cursor: pointer;
            }
            .<?php echo $uid; ?> .olo-btn-link:hover {
                filter: brightness(1.12);
                transform: translateY(-1px);
                <?php if ( $fg ) : ?>color: <?php echo $fg; ?> !important;<?php endif; ?>
                text-decoration: none !important;
            }
            .<?php echo $uid; ?> .olo-btn-link:active {
                transform: translateY(0);
            }
        </style>
        <div class="olo-button <?php echo esc_attr( $align_class ); ?> <?php echo esc_attr( $uid ); ?>" style="padding: 16px 0;">
            <a href="<?php echo esc_url( $s['url'] ); ?>"
               target="<?php echo esc_attr( $s['target'] ); ?>"
               class="olo-btn-link"
               style="<?php if ( $bg ) echo 'background-color:' . $bg . ';'; if ( $fg ) echo 'color:' . $fg . ';'; ?>">
                <?php echo esc_html( $s['text'] ); ?>
            </a>
        </div>
        <?php
        return ob_get_clean();
    }
}
