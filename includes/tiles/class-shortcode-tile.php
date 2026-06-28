<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Shortcode_Tile extends Olobuild_Tile_Base {

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
            [ 'key' => 'shortcode_text',   'type' => 'textarea', 'label' => olobuild_t( 'Shortcode' ) ],
            [ 'key' => 'parse_shortcodes', 'type' => 'toggle',   'label' => olobuild_t( 'Esegui shortcode' ) ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $shortcode_text = trim( $s['shortcode_text'] );

        if ( empty( $shortcode_text ) ) {
            return '<div class="olo-shortcode" style="text-align:center;padding:20px;color:var(--olo-color-text-muted, #9ca3af);">'
                 . olobuild_t( 'Inserisci uno shortcode nell\'inspector.' )
                 . '</div>';
        }

        ob_start();
        ?>
        <div class="olo-shortcode">
        <?php
            if ( ! empty( $s['parse_shortcodes'] ) ) {
                // Il testo viene sanificato con wp_kses_post al salvataggio per chi
                // non ha unfiltered_html (Olobuild_Rest_Api::sanitize_unfiltered_tile_fields).
                echo do_shortcode( $shortcode_text ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- do_shortcode() output cannot be escaped without breaking shortcode markup; the raw text is sanitized with wp_kses_post() on save for users without unfiltered_html (Olobuild_Rest_Api::sanitize_unfiltered_tile_fields).
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
