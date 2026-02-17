<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Totop_Tile extends Olo_Tile_Base {

    protected $type     = 'totop';
    protected $name     = 'Torna su';
    protected $icon     = 'dashicons-arrow-up-alt';
    protected $category = 'content';
    protected $defaults = [
        'alignment' => 'right',
        'style'     => 'default',
        'smooth'    => true,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'alignment', 'type' => 'select', 'label' => 'Alignment', 'options' => [
                'left'   => 'Left',
                'center' => 'Center',
                'right'  => 'Right',
            ]],
            [ 'key' => 'style', 'type' => 'select', 'label' => 'Style', 'options' => [
                'default' => 'Default',
                'primary' => 'Primary',
            ]],
            [ 'key' => 'smooth', 'type' => 'toggle', 'label' => 'Smooth Scroll' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $align_class = 'uk-text-' . esc_attr( $s['alignment'] );
        $scroll_attr = ! empty( $s['smooth'] ) ? ' uk-scroll' : '';

        ob_start();
        ?>
        <div class="olo-totop <?php echo esc_attr( $align_class ); ?>">
            <a href="#" uk-totop<?php echo $scroll_attr; ?>></a>
        </div>
        <?php
        return ob_get_clean();
    }
}
