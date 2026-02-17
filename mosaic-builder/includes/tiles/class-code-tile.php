<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Code_Tile extends Olo_Tile_Base {

    protected $type     = 'code';
    protected $name     = 'Codice';
    protected $icon     = 'dashicons-editor-code';
    protected $category = 'content';
    protected $defaults = [
        'code'              => 'console.log("Hello World");',
        'language'          => 'javascript',
        'show_line_numbers' => false,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'code',              'type' => 'textarea', 'label' => 'Code' ],
            [ 'key' => 'language',           'type' => 'text',     'label' => 'Language' ],
            [ 'key' => 'show_line_numbers',  'type' => 'toggle',   'label' => 'Show Line Numbers' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $language   = esc_attr( $s['language'] );
        $lang_class = ! empty( $language ) ? "language-{$language}" : '';
        $code       = esc_html( $s['code'] );

        // Add line numbers if enabled
        if ( ! empty( $s['show_line_numbers'] ) ) {
            $lines     = explode( "\n", $s['code'] );
            $pad       = strlen( (string) count( $lines ) );
            $numbered  = [];
            foreach ( $lines as $i => $line ) {
                $num        = str_pad( $i + 1, $pad, ' ', STR_PAD_LEFT );
                $numbered[] = esc_html( $num ) . ' | ' . esc_html( $line );
            }
            $code = implode( "\n", $numbered );
        }

        ob_start();
        ?>
        <div class="olo-code">
            <pre class="<?php echo esc_attr( $lang_class ); ?>" style="margin: 0; padding: 1em; overflow-x: auto;"><code class="<?php echo esc_attr( $lang_class ); ?>"><?php echo $code; ?></code></pre>
        </div>
        <?php
        return ob_get_clean();
    }
}
