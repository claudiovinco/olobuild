<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Row_Tile extends Olo_Tile_Base {

    protected $type     = 'row';
    protected $name     = 'Riga / Colonne';
    protected $icon     = 'dashicons-columns';
    protected $category = 'layout';
    protected $defaults = [
        'layout'         => '50-50',
        'gap'            => '16',
        'column_gap'     => 'default',
        'vertical_align' => 'stretch',
        'stack_mobile'   => true,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'layout', 'type' => 'select', 'label' => 'Layout', 'options' => [
                '100'          => '100',
                '50-50'        => '50 / 50',
                '33-33-33'     => '33 / 33 / 33',
                '25-50-25'     => '25 / 50 / 25',
                '25-25-25-25'  => '25 / 25 / 25 / 25',
                '66-33'        => '66 / 33',
                '33-66'        => '33 / 66',
            ]],
            [ 'key' => 'gap',            'type' => 'range',  'label' => 'Gap (px)', 'min' => 0, 'max' => 48, 'step' => 4 ],
            [ 'key' => 'vertical_align', 'type' => 'select', 'label' => 'Vertical Align', 'options' => [
                'stretch' => 'Stretch',
                'start'   => 'Top',
                'center'  => 'Center',
                'end'     => 'Bottom',
            ]],
            [ 'key' => 'stack_mobile', 'type' => 'toggle', 'label' => 'Stack on Mobile' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $layout_map = [
            '100'         => [ 100 ],
            '50-50'       => [ 50, 50 ],
            '33-33-33'    => [ 33.33, 33.33, 33.33 ],
            '25-50-25'    => [ 25, 50, 25 ],
            '25-25-25-25' => [ 25, 25, 25, 25 ],
            '66-33'       => [ 66.66, 33.33 ],
            '33-66'       => [ 33.33, 66.66 ],
        ];

        $widths = $layout_map[ $s['layout'] ] ?? [ 50, 50 ];
        $gap    = absint( $s['gap'] );
        $valign = in_array( $s['vertical_align'], [ 'stretch', 'start', 'center', 'end' ] ) ? $s['vertical_align'] : 'stretch';
        $align_map = [ 'stretch' => 'stretch', 'start' => 'flex-start', 'center' => 'center', 'end' => 'flex-end' ];

        $columns_data = is_array( $s['columns_data'] ) ? $s['columns_data'] : [];
        $manager = Olo_Tile_Manager::instance();

        $stack_class = ! empty( $s['stack_mobile'] ) ? ' olo-row--stack' : '';

        ob_start();
        ?>
        <div class="olo-row<?php echo esc_attr( $stack_class ); ?>" style="display: flex; gap: <?php echo $gap; ?>px; align-items: <?php echo esc_attr( $align_map[ $valign ] ); ?>;">
            <?php foreach ( $widths as $i => $width ) :
                $col = $columns_data[ $i ] ?? [ 'id' => '', 'tiles' => [] ];
                $col_tiles = is_array( $col['tiles'] ?? null ) ? $col['tiles'] : [];
            ?>
            <div class="olo-row-col" style="width: <?php echo esc_attr( $width ); ?>%; min-width: 0;">
                <?php
                foreach ( $col_tiles as $child_tile ) {
                    $type = $child_tile['type'] ?? '';
                    $tile_instance = $manager->get_tile( $type );
                    if ( ! $tile_instance ) continue;

                    $child_settings = $child_tile['settings'] ?? [];
                    echo $tile_instance->render( $child_settings );
                }

                if ( empty( $col_tiles ) ) {
                    echo '<div style="padding: 20px; text-align: center; color: #6b7280; font-size: 0.875em;">Column ' . ( $i + 1 ) . '</div>';
                }
                ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
