<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Spacer_Tile extends Olo_Tile_Base {

    protected $type     = 'spacer';
    protected $name     = 'Spaziatore';
    protected $icon     = 'dashicons-arrows-alt';
    protected $category = 'layout';
    protected $defaults = [
        'height'           => '60',
        'show_divider'     => false,
        'divider_color'    => '#374151',
        'divider_width'    => '100',
        'divider_thickness' => '1',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'height',            'type' => 'range',  'label' => 'Height (px)', 'min' => 10, 'max' => 300, 'step' => 5 ],
            [ 'key' => 'show_divider',      'type' => 'toggle', 'label' => 'Show Divider' ],
            [ 'key' => 'divider_color',     'type' => 'color',  'label' => 'Divider Color' ],
            [ 'key' => 'divider_width',     'type' => 'range',  'label' => 'Divider Width (%)', 'min' => 10, 'max' => 100, 'step' => 5 ],
            [ 'key' => 'divider_thickness', 'type' => 'range',  'label' => 'Divider Thickness (px)', 'min' => 1, 'max' => 10, 'step' => 1 ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );
        ob_start();
        ?>
        <div class="olo-spacer uk-flex uk-flex-middle uk-flex-center" style="height: <?php echo absint( $s['height'] ); ?>px;">
            <?php if ( $s['show_divider'] ) : ?>
                <hr class="uk-divider-small" style="
                    width: <?php echo absint( $s['divider_width'] ); ?>%;
                    border-top: <?php echo absint( $s['divider_thickness'] ); ?>px solid <?php echo $this->safe_color( $s['divider_color'] ) ?: '#374151'; ?>;
                " />
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
