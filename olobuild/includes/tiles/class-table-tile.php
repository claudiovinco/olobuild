<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Table_Tile extends Olo_Tile_Base {

    protected $type     = 'table';
    protected $name     = 'Tabella';
    protected $icon     = 'dashicons-editor-table';
    protected $category = 'content';
    protected $defaults = [
        'table_data'       => "Feature|Basic|Pro|Enterprise\nStorage|5 GB|50 GB|Unlimited\nUsers|1|10|Unlimited\nSupport|Email|Priority|Dedicated",
        'striped'          => true,
        'bordered'         => true,
        'hover_effect'     => true,
        'header_bg'        => '#1F2937',
        'header_text_color'=> '#F3F4F6',
        'text_color'       => '#D1D5DB',
        'border_color'     => '#374151',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'table_data',        'type' => 'textarea', 'label' => 'Table Data (header row first, | separator)' ],
            [ 'key' => 'striped',           'type' => 'toggle',   'label' => 'Striped Rows' ],
            [ 'key' => 'bordered',          'type' => 'toggle',   'label' => 'Bordered' ],
            [ 'key' => 'hover_effect',      'type' => 'toggle',   'label' => 'Hover Effect' ],
            [ 'key' => 'header_bg',         'type' => 'color',    'label' => 'Header Background' ],
            [ 'key' => 'header_text_color', 'type' => 'color',    'label' => 'Header Text Color' ],
            [ 'key' => 'text_color',        'type' => 'color',    'label' => 'Text Color' ],
            [ 'key' => 'border_color',      'type' => 'color',    'label' => 'Border Color' ],
        ];
    }

    public function render( $settings ) {
        $s    = wp_parse_args( $settings, $this->defaults );
        $rows = $this->parse_table( $s['table_data'] );

        if ( empty( $rows ) ) {
            return '<div class="olo-table" style="padding:20px;text-align:center;color:#6b7280;">No table data</div>';
        }

        $header = array_shift( $rows );

        // Build UIkit table classes
        $table_classes = [ 'uk-table' ];
        if ( $s['striped'] ) {
            $table_classes[] = 'uk-table-striped';
        }
        if ( $s['hover_effect'] ) {
            $table_classes[] = 'uk-table-hover';
        }
        if ( $s['bordered'] ) {
            $table_classes[] = 'uk-table-divider';
        }

        ob_start();
        ?>
        <div class="olo-table uk-overflow-auto" style="padding:8px;">
            <table class="<?php echo esc_attr( implode( ' ', $table_classes ) ); ?>" style="<?php echo $this->build_style( [ 'color' => $s['text_color'] ] ); ?>">
                <thead>
                    <tr style="<?php echo $this->build_style( [ 'background' => $s['header_bg'], 'color' => $s['header_text_color'] ] ); ?>">
                        <?php foreach ( $header as $cell ) : ?>
                            <th style="padding:12px 16px;"><?php echo esc_html( $cell ); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $rows as $i => $row ) : ?>
                        <tr>
                            <?php foreach ( $row as $cell ) : ?>
                                <td style="padding:10px 16px;"><?php echo esc_html( $cell ); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
        return ob_get_clean();
    }

    private function parse_table( $text ) {
        $rows  = [];
        $lines = array_filter( array_map( 'trim', explode( "\n", $text ) ) );
        foreach ( $lines as $line ) {
            $cells = array_map( 'trim', explode( '|', $line ) );
            $rows[] = $cells;
        }
        return $rows;
    }
}
