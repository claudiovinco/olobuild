<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Popup_Tile extends Olo_Tile_Base {

    protected $type     = 'popup';
    protected $name     = 'Popup';
    protected $icon     = 'dashicons-external';
    protected $category = 'content';
    protected $defaults = [
        'mode'               => 'simple',
        'button_text'        => 'Apri',
        'button_style'       => 'default',
        'button_size'        => '',
        'button_icon'        => '',
        'button_fullwidth'   => false,
        'modal_size'         => '',
        'modal_close_button' => true,
        'modal_title'        => '',
        'content'            => '<p>Contenuto del popup...</p>',
        'image'              => '',
        'image_position'     => 'top',
        'template_id'        => 0,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'mode',               'type' => 'select', 'label' => 'Mode' ],
            [ 'key' => 'button_text',         'type' => 'text',   'label' => 'Button Text' ],
            [ 'key' => 'button_style',        'type' => 'select', 'label' => 'Button Style' ],
            [ 'key' => 'button_size',         'type' => 'select', 'label' => 'Button Size' ],
            [ 'key' => 'button_icon',         'type' => 'icon',   'label' => 'Button Icon' ],
            [ 'key' => 'button_fullwidth',    'type' => 'toggle', 'label' => 'Full Width' ],
            [ 'key' => 'modal_size',          'type' => 'select', 'label' => 'Modal Size' ],
            [ 'key' => 'modal_close_button',  'type' => 'toggle', 'label' => 'Close Button' ],
            [ 'key' => 'modal_title',         'type' => 'text',   'label' => 'Modal Title' ],
            [ 'key' => 'content',             'type' => 'editor', 'label' => 'Content' ],
            [ 'key' => 'image',               'type' => 'image',  'label' => 'Image' ],
            [ 'key' => 'image_position',      'type' => 'select', 'label' => 'Image Position' ],
            [ 'key' => 'template_id',         'type' => 'select', 'label' => 'Template' ],
        ];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'mpopup-' . wp_rand( 10000, 99999 );

        // Button classes
        $btn_classes = [ 'uk-button' ];
        $btn_classes[] = 'uk-button-' . sanitize_html_class( $s['button_style'] ?: 'default' );
        if ( ! empty( $s['button_size'] ) ) {
            $btn_classes[] = 'uk-button-' . sanitize_html_class( $s['button_size'] );
        }
        if ( ! empty( $s['button_fullwidth'] ) ) {
            $btn_classes[] = 'uk-width-1-1';
        }
        $btn_class = implode( ' ', $btn_classes );

        // Modal dialog classes
        $dialog_classes = [ 'uk-modal-dialog' ];
        $is_container = $s['modal_size'] === 'container'
                     || ( $s['mode'] === 'template' && $s['modal_size'] === '' );
        if ( $is_container ) {
            $dialog_classes[] = 'uk-modal-dialog-large';
        }
        $dialog_class = implode( ' ', $dialog_classes );

        // Modal attribute
        $modal_attr = 'uk-modal';
        if ( $is_container ) {
            $modal_attr = 'uk-modal=container: true';
        }

        // Button icon
        $icon_html = '';
        if ( ! empty( $s['button_icon'] ) && preg_match( '/^[a-z][a-z0-9-]*$/', $s['button_icon'] ) ) {
            $icon_html = '<span uk-icon="icon: ' . esc_attr( $s['button_icon'] ) . '"></span> ';
        }

        // Button text
        $btn_text = esc_html( $s['button_text'] ?: 'Apri' );

        ob_start();
        ?>
        <style>
            #<?php echo esc_attr( $uid ); ?> .uk-modal-body { overflow-x: hidden; }
            #<?php echo esc_attr( $uid ); ?> .olo-template,
            #<?php echo esc_attr( $uid ); ?> .olo-frontend-grid,
            #<?php echo esc_attr( $uid ); ?> .olo-section,
            #<?php echo esc_attr( $uid ); ?> .olo-row,
            #<?php echo esc_attr( $uid ); ?> .olo-col { max-width: 100%; box-sizing: border-box; overflow-x: hidden; }
            #<?php echo esc_attr( $uid ); ?> .olo-map-canvas,
            #<?php echo esc_attr( $uid ); ?> .olo-map iframe { width: 100% !important; }
            #<?php echo esc_attr( $uid ); ?> .olo-frontend-grid { --olo-container-max-width: none; }
            #<?php echo esc_attr( $uid ); ?> .wp-block-post-title,
            #<?php echo esc_attr( $uid ); ?> .entry-title { display: none; }
        </style>
        <?php

        // Full-screen modal uses special structure
        if ( $s['modal_size'] === 'full' ) :
        ?>
        <div class="olo-popup">
            <button class="<?php echo esc_attr( $btn_class ); ?>" type="button" uk-toggle="target: #<?php echo esc_attr( $uid ); ?>">
                <?php echo $icon_html; ?><?php echo $btn_text; ?>
            </button>

            <div id="<?php echo esc_attr( $uid ); ?>" class="uk-modal-full" <?php echo $modal_attr; ?>>
                <div class="uk-modal-dialog uk-flex uk-flex-center uk-flex-middle" uk-height-viewport>
                    <?php if ( ! empty( $s['modal_close_button'] ) ) : ?>
                        <button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>
                    <?php endif; ?>
                    <div class="uk-modal-body uk-width-xlarge">
                        <?php echo $this->render_modal_content( $s ); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php else : ?>
        <div class="olo-popup">
            <button class="<?php echo esc_attr( $btn_class ); ?>" type="button" uk-toggle="target: #<?php echo esc_attr( $uid ); ?>">
                <?php echo $icon_html; ?><?php echo $btn_text; ?>
            </button>

            <div id="<?php echo esc_attr( $uid ); ?>" <?php echo $modal_attr; ?>>
                <div class="<?php echo esc_attr( $dialog_class ); ?>">
                    <?php if ( ! empty( $s['modal_close_button'] ) ) : ?>
                        <button class="uk-modal-close-default" type="button" uk-close></button>
                    <?php endif; ?>

                    <?php if ( ! empty( $s['modal_title'] ) ) : ?>
                    <div class="uk-modal-header">
                        <h2 class="uk-modal-title"><?php echo esc_html( $s['modal_title'] ); ?></h2>
                    </div>
                    <?php endif; ?>

                    <div class="uk-modal-body" uk-overflow-auto>
                        <?php echo $this->render_modal_content( $s ); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <script>
        (function(){
            var el = document.getElementById('<?php echo esc_js( $uid ); ?>');
            if (!el) return;
            el.addEventListener('shown', function() {
                // Leaflet maps: invalidate size after modal is visible
                var maps = el.querySelectorAll('.olo-map-canvas');
                maps.forEach(function(c) { if (c._oloMap) c._oloMap.invalidateSize(); });
                // OSM iframes: reload src to force proper rendering
                var iframes = el.querySelectorAll('.olo-map iframe');
                iframes.forEach(function(f) { f.src = f.src; });
            });
        })();
        </script>
        <?php

        return ob_get_clean();
    }

    /**
     * Render the modal inner content based on mode (simple or template).
     */
    private function render_modal_content( $s ) {
        if ( $s['mode'] === 'template' && ! empty( $s['template_id'] ) ) {
            return $this->render_template_content( (int) $s['template_id'] );
        }

        return $this->render_simple_content( $s );
    }

    /**
     * Render simple mode: image + rich text content.
     */
    private function render_simple_content( $s ) {
        $html      = '';
        $image     = trim( $s['image'] ?? '' );
        $content   = $s['content'] ?? '';
        $position  = $s['image_position'] ?? 'top';
        $has_image = ! empty( $image );

        // Image HTML
        $img_html = '';
        if ( $has_image ) {
            $img_html = '<div class="olo-popup-image"><img src="' . esc_url( $image ) . '" alt="" loading="lazy" style="width:100%;height:auto;" /></div>';
        }

        // Content HTML
        $content_html = '';
        if ( ! empty( $content ) ) {
            $content_html = '<div class="olo-popup-content">' . wp_kses_post( $content ) . '</div>';
        }

        if ( ! $has_image ) {
            return $content_html;
        }

        // Layout based on image position
        if ( $position === 'left' || $position === 'right' ) {
            $left  = $position === 'left' ? $img_html : $content_html;
            $right = $position === 'left' ? $content_html : $img_html;
            $html  = '<div uk-grid class="uk-child-width-1-2@s uk-grid-small">';
            $html .= '<div>' . $left . '</div>';
            $html .= '<div>' . $right . '</div>';
            $html .= '</div>';
        } elseif ( $position === 'bottom' ) {
            $html = $content_html . $img_html;
        } else {
            // top (default)
            $html = $img_html . $content_html;
        }

        return $html;
    }

    /**
     * Render template mode: embed a Olobuilder template inside the modal.
     */
    private function render_template_content( $template_id ) {
        if ( $template_id <= 0 ) {
            return '<!-- Olobuilder Popup: No template selected -->';
        }

        // Use the frontend renderer's shortcode method
        $renderer = new Olo_Frontend_Renderer();
        $output   = $renderer->render_shortcode( [ 'id' => $template_id ] );

        if ( empty( $output ) || strpos( $output, '<!-- Olobuilder' ) === 0 ) {
            return '<p><em>Template non disponibile.</em></p>';
        }

        return $output;
    }
}
