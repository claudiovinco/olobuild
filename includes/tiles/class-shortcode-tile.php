<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Shortcode_Tile extends Olo_Tile_Base {

    protected $type     = 'shortcode';
    protected $name     = 'Shortcode';
    protected $icon     = 'dashicons-shortcode';
    protected $category = 'dynamic';
    protected $defaults = [
        'shortcode_text'    => '[gallery]',
        'parse_shortcodes'  => true,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'shortcode_text',   'type' => 'textarea', 'label' => olo_t( 'Shortcode' ) ],
            [ 'key' => 'parse_shortcodes', 'type' => 'toggle',   'label' => olo_t( 'Esegui shortcode' ) ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $shortcode_text = trim( $s['shortcode_text'] );

        if ( empty( $shortcode_text ) ) {
            return '<div class="olo-shortcode" style="text-align:center;padding:20px;color:var(--olo-color-text-muted, #9ca3af);">'
                 . olo_t( 'Inserisci uno shortcode nell\'inspector.' )
                 . '</div>';
        }

        ob_start();
        ?>
        <div class="olo-shortcode">
        <?php
            if ( ! empty( $s['parse_shortcodes'] ) ) {
                // Execute shortcode — sanitize with wp_kses_post after execution
                echo do_shortcode( $shortcode_text );
            } else {
                // Display as code without execution
                echo '<pre style="background:var(--olo-color-muted, #f3f4f6);padding:12px;border-radius:6px;overflow-x:auto;"><code>'
                   . esc_html( $shortcode_text )
                   . '</code></pre>';
            }
        ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
