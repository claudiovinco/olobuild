<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Html_Tile extends Olo_Tile_Base {

    protected $type     = 'html';
    protected $name     = 'HTML / Codice';
    protected $icon     = 'dashicons-editor-code';
    protected $category = 'content';
    protected $defaults = [
        'html_content' => '<div style="padding:20px;text-align:center;color:#9CA3AF;">Custom HTML block</div>',
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
            ?>
            <div class="olo-html uk-panel">
                <?php
                // Raw HTML output — intentional for admin-authored content
                echo $s['html_content'];
                ?>
            </div>
            <?php
        }

        return ob_get_clean();
    }
}
