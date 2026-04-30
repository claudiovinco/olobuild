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
        'padding'      => '16',
        'tile_padding' => [ 'top' => 16, 'right' => 16, 'bottom' => 16, 'left' => 16 ],
        'tile_margin'  => [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 ],
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
        $allowed_lh = [ '1.2', '1.4', '1.6', '1.8', '2.0' ];
        if ( in_array( $lh, $allowed_lh, true ) ) {
            $style .= 'line-height:' . $lh . ';';
        }

        $mw = absint( $s['max_width'] ?? 0 );
        if ( $mw > 0 ) {
            $style .= 'max-width:' . $mw . 'px;';
        }

        // Content: supports both HTML (from RichTextEditor) and plain text (legacy)
        $content_raw = $s['content'] ?? '';
        if ( preg_match( '/^\s*</', $content_raw ) ) {
            $content = wp_kses_post( $content_raw );
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
        return ob_get_clean();
    }
}
