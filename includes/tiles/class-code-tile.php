<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Code_Tile extends Olo_Tile_Base {

    protected $type     = 'code';
    protected $name     = 'Codice';
    protected $icon     = 'dashicons-editor-code';
    protected $category = 'text';
    protected $defaults = [
        'code'              => 'console.log("Hello World");',
        'language'          => 'javascript',
        'show_line_numbers' => false,
        'theme'             => 'github-dark',
        'show_copy_button'  => true,
        'font_size'         => '14',
        'max_height'        => '',
        'wrap_lines'        => false,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'code',              'type' => 'textarea', 'label' => 'Code' ],
            [ 'key' => 'language',           'type' => 'text',     'label' => 'Language' ],
            [ 'key' => 'show_line_numbers',  'type' => 'toggle',   'label' => 'Show Line Numbers' ],
            [ 'key' => 'theme',              'type' => 'select',   'label' => 'Theme' ],
            [ 'key' => 'show_copy_button',   'type' => 'toggle',   'label' => 'Copy Button' ],
            [ 'key' => 'font_size',          'type' => 'range',    'label' => 'Font Size' ],
            [ 'key' => 'max_height',         'type' => 'text',     'label' => 'Max Height' ],
            [ 'key' => 'wrap_lines',         'type' => 'toggle',   'label' => 'Wrap Lines' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        // Theme colors
        $themes = [
            'github-dark'    => [ 'bg' => '#0d1117', 'text' => '#c9d1d9', 'line' => '#484f58', 'header' => '#161b22' ],
            'monokai'        => [ 'bg' => '#272822', 'text' => '#f8f8f2', 'line' => '#75715e', 'header' => '#1e1f1c' ],
            'dracula'        => [ 'bg' => '#282a36', 'text' => '#f8f8f2', 'line' => '#6272a4', 'header' => '#21222c' ],
            'one-dark'       => [ 'bg' => '#282c34', 'text' => '#abb2bf', 'line' => '#636d83', 'header' => '#21252b' ],
            'solarized-dark' => [ 'bg' => '#002b36', 'text' => '#839496', 'line' => '#586e75', 'header' => '#073642' ],
            'light'          => [ 'bg' => '#ffffff', 'text' => '#24292e', 'line' => '#babbbd', 'header' => '#f6f8fa' ],
        ];

        $theme_key = isset( $themes[ $s['theme'] ] ) ? $s['theme'] : 'github-dark';
        $t         = $themes[ $theme_key ];

        $language   = esc_attr( $s['language'] );
        $lang_class = ! empty( $language ) ? "language-{$language}" : '';
        $code       = esc_html( $s['code'] );
        $font_size  = intval( $s['font_size'] );
        if ( $font_size < 10 ) { $font_size = 14; }

        $white_space = ! empty( $s['wrap_lines'] ) ? 'pre-wrap' : 'pre';

        // Add line numbers if enabled
        if ( ! empty( $s['show_line_numbers'] ) ) {
            $code_str  = is_array( $s['code'] ) ? implode( "\n", $s['code'] ) : (string) $s['code'];
            $lines     = explode( "\n", $code_str );
            $pad       = strlen( (string) count( $lines ) );
            $numbered  = [];
            foreach ( $lines as $i => $line ) {
                $num        = str_pad( $i + 1, $pad, ' ', STR_PAD_LEFT );
                $numbered[] = '<span style="color:' . esc_attr( $t['line'] ) . ';user-select:none;">' . esc_html( $num ) . ' | </span>' . esc_html( $line );
            }
            $code = implode( "\n", $numbered );
        }

        // Max height style
        $max_height_style = '';
        if ( ! empty( $s['max_height'] ) ) {
            $max_height_style = 'max-height:' . intval( $s['max_height'] ) . 'px;overflow-y:auto;';
        }

        // Unique ID for copy functionality
        $uid = 'olo-code-' . wp_unique_id();

        ob_start();
        ?>
        <div class="olo-code" id="<?php echo esc_attr( $uid ); ?>" style="border-radius:8px;overflow:hidden;background:<?php echo esc_attr( $t['bg'] ); ?>;border:1px solid <?php echo esc_attr( $t['line'] ); ?>;">
            <?php if ( ! empty( $s['language'] ) || ! empty( $s['show_copy_button'] ) ) : ?>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 16px;background:<?php echo esc_attr( $t['header'] ); ?>;border-bottom:1px solid <?php echo esc_attr( $t['line'] ); ?>;">
                <span style="font-size:12px;font-family:monospace;text-transform:uppercase;color:<?php echo esc_attr( $t['line'] ); ?>;"><?php echo esc_html( $s['language'] ); ?></span>
                <?php if ( ! empty( $s['show_copy_button'] ) ) : ?>
                <button type="button" onclick="(function(btn){var el=btn.closest('.olo-code');if(el){var c=el.querySelector('code');if(c){var t=c.textContent;if(navigator.clipboard){navigator.clipboard.writeText(t)}btn.textContent='Copied!';setTimeout(function(){btn.textContent='Copy'},2000)}}})(this)" style="font-size:12px;font-family:monospace;color:<?php echo esc_attr( $t['text'] ); ?>;opacity:0.7;background:transparent;border:1px solid <?php echo esc_attr( $t['line'] ); ?>;border-radius:4px;padding:2px 8px;cursor:pointer;">Copy</button>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <pre class="<?php echo esc_attr( $lang_class ); ?>" style="margin:0;padding:1em;overflow-x:auto;background:transparent;<?php echo $max_height_style; ?>"><code class="<?php echo esc_attr( $lang_class ); ?>" style="font-family:monospace;font-size:<?php echo $font_size; ?>px;color:<?php echo esc_attr( $t['text'] ); ?>;line-height:1.6;white-space:<?php echo $white_space; ?>;"><?php echo $code; ?></code></pre>
        </div>
        <?php
        return ob_get_clean();
    }
}
