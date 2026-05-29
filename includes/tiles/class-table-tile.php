<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Table_Tile extends Olo_Tile_Base {

    protected $type     = 'table';
    protected $name     = 'Tabella';
    protected $icon     = 'dashicons-editor-table';
    protected $category = 'text';
    protected $defaults = [
        'preset' => 'custom',
        'table_data'        => "Funzionalità|Base|Pro|Enterprise\nSpazio|5 GB|50 GB|Illimitato\nUtenti|1|10|Illimitato\nSupporto|Email|Prioritario|Dedicato",
        'has_header'        => true,
        'striped'           => true,
        'bordered'          => true,
        'hover_effect'      => true,
        'compact'           => false,
        'first_col_bold'    => false,
        'col_alignments'    => [],
        'responsive_mode'   => 'scroll',
        'header_bg'         => '',
        'header_text_color' => '',
        'text_color'        => '',
        'border_color'      => '',
        'even_row_bg'       => '',
            'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'has_header',        'type' => 'toggle',  'label' => 'Header Row' ],
            [ 'key' => 'striped',           'type' => 'toggle',  'label' => 'Striped Rows' ],
            [ 'key' => 'bordered',          'type' => 'toggle',  'label' => 'Bordered' ],
            [ 'key' => 'hover_effect',      'type' => 'toggle',  'label' => 'Hover Effect' ],
            [ 'key' => 'compact',           'type' => 'toggle',  'label' => 'Compact' ],
            [ 'key' => 'first_col_bold',    'type' => 'toggle',  'label' => 'Bold First Column' ],
            [ 'key' => 'responsive_mode',   'type' => 'select',  'label' => 'Responsive Mode' ],
            [ 'key' => 'header_bg',         'type' => 'color',   'label' => 'Header Background' ],
            [ 'key' => 'header_text_color', 'type' => 'color',   'label' => 'Header Text Color' ],
            [ 'key' => 'text_color',        'type' => 'color',   'label' => 'Text Color' ],
            [ 'key' => 'border_color',      'type' => 'color',   'label' => 'Border Color' ],
            [ 'key' => 'even_row_bg',       'type' => 'color',   'label' => 'Even Row Background' ],
        ];
    }

    public function render( $settings ) {
        $s    = wp_parse_args( $settings, $this->defaults );
        $rows = $this->parse_table( $s['table_data'] );

        if ( empty( $rows ) ) {
            return '<div class="olo-table" style="padding:20px;text-align:center;color:var(--olo-color-text-muted,#9CA3AF);">No table data</div>';
        }

        $has_header    = ! empty( $s['has_header'] );
        $header        = $has_header ? array_shift( $rows ) : null;
        $col_aligns    = is_array( $s['col_alignments'] ) ? $s['col_alignments'] : [];
        $border_color  = $this->safe_color_css( $s['border_color'] ) ?: '#e5e7eb';
        $text_color    = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text,#374151)';
        $header_bg     = $this->safe_color_css( $s['header_bg'] ) ?: 'var(--olo-color-secondary,#1F2937)';
        $header_tc     = $this->safe_color_css( $s['header_text_color'] ) ?: '#fff';
        $even_bg       = $this->safe_color_css( $s['even_row_bg'] ) ?: 'rgba(0,0,0,0.025)';
        $compact       = ! empty( $s['compact'] );
        $bordered      = ! empty( $s['bordered'] );
        $striped       = ! empty( $s['striped'] );
        $hover         = ! empty( $s['hover_effect'] );
        $first_bold    = ! empty( $s['first_col_bold'] );
        $responsive    = ( $s['responsive_mode'] ?? 'scroll' );
        $pad           = $compact ? '6px 10px' : '10px 16px';
        $uid           = 'olo-tbl-' . substr( md5( wp_json_encode( $s ) ), 0, 6 );

        // Scoped CSS
        $css = '<style>';
        $css .= ".{$uid}{width:100%;border-collapse:collapse;color:{$text_color};font-size:" . ( $compact ? '13px' : '15px' ) . '}';
        $css .= ".{$uid} th,.{$uid} td{padding:{$pad};text-align:left}";
        if ( $bordered ) {
            $css .= ".{$uid} th,.{$uid} td{border-bottom:1px solid {$border_color}}";
        }
        if ( $hover ) {
            $css .= ".{$uid} tbody tr:hover{background:rgba(99,102,241,0.06)}";
        }
        if ( $responsive === 'stack' ) {
            $css .= "@media(max-width:767px){";
            $css .= ".{$uid} thead{display:none}";
            $css .= ".{$uid} tbody tr{display:block;margin-bottom:12px;border:1px solid {$border_color};border-radius:6px;overflow:hidden}";
            $css .= ".{$uid} tbody td{display:flex;justify-content:space-between;align-items:center;text-align:right}";
            $css .= ".{$uid} tbody td::before{content:attr(data-label);font-weight:600;text-align:left;margin-right:12px}";
            $css .= '}';
        }
        $css .= '</style>';

        ob_start();
        echo $css;
        ?>
        <div class="olo-table olo-tb-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" style="<?php echo $responsive === 'scroll' ? 'overflow-x:auto' : ''; ?>">
            <table class="<?php echo esc_attr( $uid ); ?>">
                <?php if ( $header ) : ?>
                <thead>
                    <tr style="background:<?php echo esc_attr( $header_bg ); ?>;color:<?php echo esc_attr( $header_tc ); ?>">
                        <?php foreach ( $header as $ci => $cell ) :
                            $align = isset( $col_aligns[ $ci ] ) ? $col_aligns[ $ci ] : 'left';
                        ?>
                            <th style="text-align:<?php echo esc_attr( $align ); ?>;padding:<?php echo esc_attr( $pad ); ?>"><?php echo esc_html( $cell ); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <?php endif; ?>
                <tbody>
                    <?php foreach ( $rows as $ri => $row ) :
                        $row_style = '';
                        if ( $striped ) {
                            if ( $ri % 2 === 1 ) {
                                $row_style = "background:{$even_bg}";
                            }
                        }
                    ?>
                        <tr<?php echo $row_style ? ' style="' . esc_attr( $row_style ) . '"' : ''; ?>>
                            <?php foreach ( $row as $ci => $cell ) :
                                $align = isset( $col_aligns[ $ci ] ) ? $col_aligns[ $ci ] : 'left';
                                $td_style = "text-align:{$align};padding:{$pad}";
                                $bold = ( $first_bold && $ci === 0 ) ? ' style="font-weight:600;' . esc_attr( $td_style ) . '"' : ' style="' . esc_attr( $td_style ) . '"';
                                $data_label = '';
                                if ( $responsive === 'stack' && $header && isset( $header[ $ci ] ) ) {
                                    $data_label = ' data-label="' . esc_attr( $header[ $ci ] ) . '"';
                                }
                            ?>
                                <td<?php echo $bold . $data_label; ?>><?php echo esc_html( $cell ); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}";
            echo $border_hover_css . $border_effect_css . '</style>';
        }
        return ob_get_clean();
    }

    /**
     * Parse table data — supports both array (new) and pipe-separated string (legacy).
     */
    private function parse_table( $data ) {
        // New format: 2D array
        if ( is_array( $data ) ) {
            // Check if it's already a 2D array
            if ( ! empty( $data ) && is_array( $data[0] ) ) {
                return array_map( function( $row ) {
                    return array_map( 'strval', $row );
                }, $data );
            }
            // 1D array — treat each element as a pipe-separated line
            $data = implode( "\n", $data );
        }

        // Legacy string format
        $rows  = [];
        $text  = (string) $data;
        $lines = array_filter( array_map( 'trim', explode( "\n", $text ) ) );
        foreach ( $lines as $line ) {
            $cells = array_map( 'trim', explode( '|', $line ) );
            $rows[] = $cells;
        }
        return $rows;
    }
}
