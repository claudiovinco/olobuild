<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Table_Tile extends Olobuild_Tile_Base {

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
            return '<div class="olo-table" style="padding:20px;text-align:center;color:var(--olo-color-text-muted,#9CA3AF);">' . esc_html( olobuild_t( 'Nessun dato nella tabella' ) ) . '</div>';
        }

        $has_header    = ! empty( $s['has_header'] );
        $header        = $has_header ? array_shift( $rows ) : null;
        $col_aligns    = is_array( $s['col_alignments'] ) ? $s['col_alignments'] : [];
        $border_color  = $this->safe_color_css( $s['border_color'] ) ?: 'var(--olo-color-border,#e5e7eb)';
        $text_color    = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text,#374151)';
        $header_bg     = $this->safe_color_css( $s['header_bg'] ) ?: 'var(--olo-color-secondary,#16263d)';
        // Senza un colore scelto, il testo dell'intestazione è il contrasto dello sfondo che
        // c'è davvero: prima ricadeva sempre su on-primary (il contrasto del PRIMARIO), anche
        // sul secondario di serie o su uno sfondo chiaro scelto a mano.
        $header_tc     = $this->safe_color_css( $s['header_text_color'] ) ?: $this->testo_su_intestazione( $this->safe_color_css( $s['header_bg'] ), $text_color );
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
            // La tinta del passaggio segue il primario del tema (prima: rosso del brand fisso).
            $css .= ".{$uid} tbody tr:hover{background:color-mix(in srgb, var(--olo-color-primary, #e1474f) 6%, transparent)}";
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
        echo $css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- scoped CSS assembled above exclusively from safe_color_css() whitelisted colours, fixed padding/size literals and the internally generated md5-based $uid
        ?>
        <div class="olo-table olo-tb-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" style="<?php echo $responsive === 'scroll' ? 'overflow-x:auto' : ''; ?>">
            <table class="<?php echo esc_attr( $uid ); ?>">
                <?php if ( $header ) : ?>
                <thead>
                    <tr style="background:<?php echo esc_attr( $header_bg ); ?>;color:<?php echo esc_attr( $header_tc ); ?>">
                        <?php foreach ( $header as $ci => $cell ) :
                            $align = isset( $col_aligns[ $ci ] ) ? $col_aligns[ $ci ] : 'left';
                        ?>
                            <th scope="col" style="text-align:<?php echo esc_attr( $align ); ?>;padding:<?php echo esc_attr( $pad ); ?>"><?php echo esc_html( $cell ); ?></th>
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
                                $is_row_header = ( $first_bold && $ci === 0 );
                                $cell_tag      = $is_row_header ? 'th' : 'td';
                                $scope_attr    = $is_row_header ? ' scope="row"' : '';
                            ?>
                                <<?php echo $cell_tag; ?><?php echo $scope_attr . $bold . $data_label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $cell_tag/$scope_attr fixed literals; remaining attribute strings assembled above from fixed literals with esc_attr()'d values ?>><?php echo esc_html( $cell ); ?></<?php echo $cell_tag; ?>>
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
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }

    /**
     * Colore del testo leggibile sullo sfondo dell'intestazione, quando non è scelto.
     * '' = il secondario di serie → il suo contrasto. Un token della Palette che ha il
     * contrasto (primario, secondario, tenue) → quel contrasto, che segue palette e modalità
     * scura. Sfondo, superficie e bordo cambiano in modalità scura insieme al testo → il testo
     * delle celle; testo e testo tenue come sfondo → lo sfondo della pagina (la coppia
     * rovesciata). Quasi trasparente (alfa sotto 0,5) → il testo delle celle, perché sotto si
     * vede la pagina. Un altro colore → chiaro o scuro secondo la sua luminanza (WCAG); uno
     * che il server non sa leggere (hsl(), nomi CSS, token fuori Palette) → on-primary, come
     * prima: il testo delle celle su un hsl() scuro o su «black» non si leggeva.
     *
     * @param string $bg         Sfondo già passato da safe_color_css() ('' = non scelto).
     * @param string $text_color Colore del testo delle celle (già validato).
     * @return string Colore CSS.
     */
    private function testo_su_intestazione( $bg, $text_color ) {
        if ( '' === $bg ) {
            return 'var(--olo-color-secondary-contrast, #ffffff)';
        }
        if ( preg_match( '/^var\(\s*--olo-color-(primary|secondary|muted|surface-alt)\s*[,)]/', $bg, $m ) ) {
            $ruolo = 'surface-alt' === $m[1] ? 'muted' : $m[1];
            return 'var(--olo-color-' . $ruolo . '-contrast, ' . ( 'muted' === $ruolo ? '#374151' : '#ffffff' ) . ')';
        }
        // Token che la modalità scura ridefinisce: una luminanza calcolata qui vale solo per
        // la modalità chiara (var(--olo-color-dark) su background diventava scuro su scuro).
        if ( preg_match( '/^var\(\s*--olo-color-(background|surface|border)\s*[,)]/', $bg ) ) {
            return $text_color;
        }
        if ( preg_match( '/^var\(\s*--olo-color-(text|text-muted|text-soft|text-faint)\s*[,)]/', $bg ) ) {
            return 'var(--olo-color-background, #ffffff)';
        }
        // Trasparenza: colore_hex() scarta l'alfa, e un velo come rgba(0,0,0,0.04) passava
        // per nero (testo chiaro su un'intestazione quasi bianca).
        $alfa = null;
        if ( preg_match( '/^(?:rgb|hsl)a?\((?:[^,\/()]*,){3}\s*([\d.]+)(%?)\s*\)$/i', $bg, $a ) || preg_match( '/^(?:rgb|hsl)a?\([^,\/()]*\/\s*([\d.]+)(%?)\s*\)$/i', $bg, $a ) ) {
            $alfa = (float) $a[1] / ( '%' === $a[2] ? 100 : 1 );
        } elseif ( preg_match( '/^#[0-9a-f]{6}([0-9a-f]{2})$/i', $bg, $a ) ) {
            $alfa = hexdec( $a[1] ) / 255;
        } elseif ( preg_match( '/^color-mix\(\s*in\s+srgb\s*,\s*(.+?)\s+([\d.]+)%\s*,\s*transparent\s*\)$/is', $bg, $a ) ) {
            // con_alfa() e i preset scrivono così un colore velato: la percentuale è l'alfa.
            // Abbastanza coprente → si giudica il colore pieno (anche se è un token).
            if ( (float) $a[2] >= 50 ) {
                return $this->testo_su_intestazione( trim( $a[1] ), $text_color );
            }
            return $text_color;
        } elseif ( false !== stripos( $bg, 'transparent' ) ) {
            $alfa = 0;
        }
        if ( null !== $alfa ) {
            if ( $alfa < 0.5 ) {
                return $text_color;
            }
        }
        $hex = Olobuild_Tile_Utils::colore_hex( $bg );
        if ( '' === $hex ) {
            return 'var(--olo-color-on-primary,#ffffff)';
        }
        $lum = 0;
        foreach ( [ [ 0.2126, 1 ], [ 0.7152, 3 ], [ 0.0722, 5 ] ] as $canale ) {
            $c    = hexdec( substr( $hex, $canale[1], 2 ) ) / 255;
            $lum += $canale[0] * ( $c <= 0.03928 ? $c / 12.92 : pow( ( $c + 0.055 ) / 1.055, 2.4 ) );
        }
        return $lum < 0.179 ? 'var(--olo-color-light, #fdfcfa)' : 'var(--olo-color-dark, #14161c)';
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
