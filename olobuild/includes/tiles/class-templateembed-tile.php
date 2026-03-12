<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_TemplateEmbed_Tile extends Olo_Tile_Base {

    protected $type     = 'templateembed';
    protected $name     = 'Includi template';
    protected $icon     = 'dashicons-layout';
    protected $category = 'layout';
    protected $defaults = [
        'template_id'    => 0,
        'template_label' => '',
    ];

    /** Static depth counter to prevent infinite recursion. */
    private static $render_depth = 0;

    /** Maximum nesting depth for embedded templates. */
    const MAX_DEPTH = 3;

    public function get_controls() {
        return [
            [ 'key' => 'template_id',    'type' => 'number', 'label' => olo_t( 'ID Template' ) ],
            [ 'key' => 'template_label', 'type' => 'text',   'label' => olo_t( 'Etichetta (anteprima)' ) ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $template_id = absint( $s['template_id'] );

        if ( ! $template_id ) {
            return '<!-- Olobuild TemplateEmbed: nessun ID template -->';
        }

        // Guard against infinite recursion
        if ( self::$render_depth >= self::MAX_DEPTH ) {
            return '<!-- Olobuild TemplateEmbed: profondita\' massima raggiunta (' . self::MAX_DEPTH . ' livelli) -->';
        }

        // Load template from database
        if ( ! class_exists( 'Olo_Database' ) ) {
            return '<!-- Olobuild TemplateEmbed: classe database non disponibile -->';
        }

        $db       = new Olo_Database();
        $template = $db->get_template( $template_id );

        if ( ! $template ) {
            return '<!-- Olobuild TemplateEmbed: template #' . $template_id . ' non trovato -->';
        }

        $tiles = $template['content'];
        if ( empty( $tiles ) || ! is_array( $tiles ) ) {
            return '<!-- Olobuild TemplateEmbed: template #' . $template_id . ' vuoto -->';
        }

        // Render using the frontend renderer
        if ( ! class_exists( 'Olo_Frontend_Renderer' ) ) {
            return '<!-- Olobuild TemplateEmbed: renderer non disponibile -->';
        }

        self::$render_depth++;

        $renderer = new Olo_Frontend_Renderer();
        $output   = $renderer->render_shortcode( [ 'id' => $template_id ] );

        self::$render_depth--;

        if ( empty( $output ) ) {
            return '<!-- Olobuild TemplateEmbed: output vuoto per template #' . $template_id . ' -->';
        }

        return '<div class="olo-templateembed" data-template-id="' . esc_attr( $template_id ) . '">'
             . $output
             . '</div>';
    }
}
