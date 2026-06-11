<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Html_Tile extends Olo_Tile_Base {

    protected $type     = 'html';
    protected $name     = 'HTML / Codice';
    protected $icon     = 'dashicons-editor-code';
    protected $category = 'text';
    protected $defaults = [
        'html_content' => '<div style="padding:20px;text-align:center;color:var(--olo-color-text-faint, #9ca3af);">Custom HTML block</div>',
        'sandbox'      => false,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'html_content', 'type' => 'textarea', 'label' => 'HTML Content' ],
            [ 'key' => 'sandbox',      'type' => 'toggle',   'label' => 'Sandbox (iframe)' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        ob_start();

        if ( $s['sandbox'] ) {
            ?>
            <div class="olo-html uk-panel">
                <iframe
                    sandbox="allow-scripts"
                    srcdoc="<?php echo esc_attr( $s['html_content'] ); ?>"
                    style="width:100%;min-height:100px;border:none;"
                    loading="lazy"
                ></iframe>
            </div>
            <?php
        } else {
            // Il tile "HTML / Codice" esiste apposta per inserire HTML grezzo
            // (script, style, form, iframe inclusi). Il contenuto viene scritto
            // SOLO da chi ha capability di editare i template Olobuild → trust.
            // Per casi che vogliono ri-sanitizzare opt-in c'è il filter
            // `olo_html_tile_output`.
            $html = apply_filters( 'olo_html_tile_output', $s['html_content'], $s );
            ?>
            <div class="olo-html uk-panel">
                <?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- raw HTML by design: html_content is stored unfiltered only for users with the unfiltered_html capability, everyone else is forced through wp_kses_post() on save (Olo_Rest_Api::sanitize_unfiltered_tile_fields); opt-in re-sanitization via the olo_html_tile_output filter. ?>
            </div>
            <?php
        }

        return ob_get_clean();
    }
}
