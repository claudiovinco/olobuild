<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_TextBlock_Tile extends Olo_Tile_Base {

    protected $type     = 'text-block';
    protected $name     = 'Testo';
    protected $icon     = 'dashicons-editor-paragraph';
    protected $category = 'essential';
    protected $defaults = [
        'content'     => '<p>Scrivi qui il tuo testo.</p>',
        'text_color'  => '',
        'font_size'   => '',
        'line_height' => '',
        'max_width'   => '',
        'columns'     => 1,
        'column_gap'  => '30',
        'padding'      => '16',
        'tile_padding' => [ 'top' => 16, 'right' => 16, 'bottom' => 16, 'left' => 16 ],
        'tile_margin'  => [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 ],
        'border_radius'           => [ 'tl' => 0, 'tr' => 0, 'br' => 0, 'bl' => 0 ],
        'hover_border_radius'     => [ 'tl' => 0, 'tr' => 0, 'br' => 0, 'bl' => 0 ],
        'hover_radius_duration'   => 400,
        // Text effects
        'text_effect'             => 'none',
        'text_effect_target'      => 'content',
        'text_effect_speed'       => '50',
        'text_effect_delay'       => '0',
        'text_effect_loop'        => false,
        'text_effect_cursor'      => true,
        'text_effect_cursor_char' => '|',
        'text_effect_color'       => '',
        'text_effect_color_to'    => '',
        'text_effect_phrases'     => '',
        'text_effect_pause'       => '1500',
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
            [ 'key' => 'content',     'type' => 'editor', 'label' => 'Contenuto' ],
            [ 'key' => 'text_color',  'type' => 'color',  'label' => 'Colore testo' ],
            [ 'key' => 'font_size',   'type' => 'range',  'label' => 'Dimensione' ],
            [ 'key' => 'line_height', 'type' => 'select', 'label' => 'Interlinea' ],
            [ 'key' => 'max_width',   'type' => 'range',  'label' => 'Larghezza max' ],
            [ 'key' => 'padding',     'type' => 'range',  'label' => 'Padding' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        // Build inline style — padding e margini con FieldSpacing (oggetto {top,right,bottom,left})
        $style = '';

        // Padding: usa tile_padding (standard) oppure tb_padding/padding (legacy)
        $p = $s['tile_padding'] ?? $s['tb_padding'] ?? null;
        if ( is_array( $p ) ) {
            $style .= 'padding:' . intval( $p['top'] ?? 0 ) . 'px ' . intval( $p['right'] ?? 0 ) . 'px ' . intval( $p['bottom'] ?? 0 ) . 'px ' . intval( $p['left'] ?? 0 ) . 'px;';
        } else {
            $pad = absint( $s['padding'] ?? 16 );
            $style .= 'padding:' . $pad . 'px;';
        }

        // Margini: usa tile_margin (standard) oppure tb_margin (legacy)
        $m = $s['tile_margin'] ?? $s['tb_margin'] ?? null;
        if ( is_array( $m ) ) {
            $mt = intval( $m['top'] ?? 0 );
            $mr = intval( $m['right'] ?? 0 );
            $mb = intval( $m['bottom'] ?? 0 );
            $ml = intval( $m['left'] ?? 0 );
            if ( $mt || $mr || $mb || $ml ) {
                $style .= "margin:{$mt}px {$mr}px {$mb}px {$ml}px;";
            }
        }

        // Apply global typography preset if set
        $tp = sanitize_text_field( $s['typography_preset'] ?? '' );
        if ( $tp ) {
            $style .= "font-family:var(--olo-font-{$tp}-family);";
            $style .= "font-weight:var(--olo-font-{$tp}-weight);";
            $style .= "text-transform:var(--olo-font-{$tp}-transform);";
            $style .= "line-height:var(--olo-font-{$tp}-line-height);";
            $style .= "letter-spacing:var(--olo-font-{$tp}-letter-spacing);";
        }

        $txt_clr = $this->safe_color_css( $s['text_color'] ?? '' );
        if ( $txt_clr ) {
            $style .= 'color:' . $txt_clr . ';';
        }

        $fs = absint( $s['font_size'] ?? 0 );
        if ( $fs > 0 ) {
            $style .= 'font-size:' . $fs . 'px;';
        }

        $lh = $s['line_height'] ?? '';
        if ( is_numeric( $lh ) ) {
            $lh_val = (float) $lh;
            if ( $lh_val >= 0.5 && $lh_val <= 5 ) {
                $style .= 'line-height:' . rtrim( rtrim( sprintf( '%.2f', $lh_val ), '0' ), '.' ) . ';';
            }
        }

        $mw = absint( $s['max_width'] ?? 0 );
        if ( $mw > 0 ) {
            $style .= 'max-width:' . $mw . 'px;';
        }

        // Allineamento testo
        $ta = $s['text_align'] ?? '';
        if ( in_array( $ta, [ 'left', 'center', 'right', 'justify' ], true ) ) {
            $style .= 'text-align:' . $ta . ';';
        }

        // Multi-colonne (CSS columns): 1=single, 2-4=multi colonne con gap
        $cols = max( 1, min( 4, absint( $s['columns'] ?? 1 ) ) );
        if ( $cols > 1 ) {
            $col_gap = max( 0, min( 80, absint( $s['column_gap'] ?? 30 ) ) );
            $style .= 'column-count:' . $cols . ';';
            $style .= 'column-gap:' . $col_gap . 'px;';
        }

        // Border radius (4 angoli indipendenti via FieldBorderRadius)
        $br_css = $this->build_border_radius_css( $s['border_radius'] ?? [] );
        if ( $br_css ) {
            $style .= 'border-radius:' . $br_css . ';';
        }

        // Hover border-radius: se settato (anche con valori 0) genera transition + rule :hover
        $hover_br_raw = $s['hover_border_radius'] ?? '';
        $has_hover_br = is_array( $hover_br_raw ) && (
            ( isset( $hover_br_raw['tl'] ) && intval( $hover_br_raw['tl'] ) !== intval( $s['border_radius']['tl'] ?? 0 ) ) ||
            ( isset( $hover_br_raw['tr'] ) && intval( $hover_br_raw['tr'] ) !== intval( $s['border_radius']['tr'] ?? 0 ) ) ||
            ( isset( $hover_br_raw['br'] ) && intval( $hover_br_raw['br'] ) !== intval( $s['border_radius']['br'] ?? 0 ) ) ||
            ( isset( $hover_br_raw['bl'] ) && intval( $hover_br_raw['bl'] ) !== intval( $s['border_radius']['bl'] ?? 0 ) )
        );
        $hover_br_css = $has_hover_br ? $this->build_border_radius_css( $hover_br_raw ) : '';
        $br_duration  = max( 50, intval( $s['hover_radius_duration'] ?? 400 ) );
        if ( $hover_br_css ) {
            $style .= 'transition:border-radius ' . $br_duration . 'ms ease;';
        }

        // Content: supports both HTML (from RichTextEditor) and plain text (legacy).
        // Rileva HTML in QUALSIASI posizione (non solo all'inizio). Prima la regex
        // matchava solo '^\s*<' → un paragrafo "Testo <strong>...</strong>" veniva
        // trattato come plain text e i tag finivano escapati come testo letterale.
        $content_raw = $s['content'] ?? '';
        if ( preg_match( '/<[a-z!\/][^>]*>/i', $content_raw ) ) {
            $content = $this->safe_richtext_content( $content_raw );
        } else {
            $content = nl2br( esc_html( $content_raw ) );
        }

        list( $tb_cls, $tb_data ) = $this->tfx_attrs( $s, 'content', wp_strip_all_tags( $content_raw ) );
        $tb_uid = 'olo-tb-' . wp_unique_id();

        ob_start();
        ?>
        <div class="olo-text-block <?php echo $tb_uid; ?><?php echo $tb_cls; ?>" style="<?php echo esc_attr( $style ); ?>"<?php echo $tb_data; ?>>
            <?php echo $content; ?>
        </div>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $tb_uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$tb_uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$tb_uid}", $s['border'] ?? [], $s );
        $hover_br_rule_css = $hover_br_css ? ".{$tb_uid}:hover{border-radius:{$hover_br_css};}" : '';
        if ( $border_css || $border_hover_css || $border_effect_css || $hover_br_rule_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$tb_uid}{{$border_css}}";
            echo $border_hover_css . $border_effect_css . $hover_br_rule_css . '</style>';
        }
        return ob_get_clean();
    }
}
