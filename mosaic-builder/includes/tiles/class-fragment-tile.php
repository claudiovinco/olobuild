<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Fragment_Tile extends Olo_Tile_Base {

    protected $type     = 'fragment';
    protected $name     = 'Frammento';
    protected $icon     = 'dashicons-screenoptions';
    protected $category = 'layout';
    protected $defaults = [
        'html_id'     => '',
        'css_classes' => '',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'html_id',     'type' => 'text', 'label' => 'HTML ID' ],
            [ 'key' => 'css_classes',  'type' => 'text', 'label' => 'CSS Classes' ],
        ];
    }

    /**
     * Render the fragment container.
     *
     * Fragment is an invisible wrapper that can contain children.
     * Children rendering is handled by the recursive renderer in class-frontend-renderer.php.
     * This render method outputs only the wrapping <div>.
     */
    public function render( $settings, $node = [] ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $attrs = [];

        // ID attribute
        if ( ! empty( $s['html_id'] ) ) {
            $attrs[] = 'id="' . esc_attr( $s['html_id'] ) . '"';
        }

        // CSS classes
        $classes = [ 'olo-fragment' ];
        if ( ! empty( $s['css_classes'] ) ) {
            $classes[] = esc_attr( $s['css_classes'] );
        }
        $attrs[] = 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';

        $html = '<div ' . implode( ' ', $attrs ) . '>';

        // Render children if present
        if ( ! empty( $node['children'] ) && is_array( $node['children'] ) ) {
            $manager = Olo_Tile_Manager::instance();
            foreach ( $node['children'] as $child ) {
                $child_type = $child['type'] ?? '';
                $tile_instance = $manager->get_tile( $child_type );
                if ( $tile_instance ) {
                    $child_settings = $child['settings'] ?? [];
                    $html .= $tile_instance->render( $child_settings, $child );
                }
            }
        }

        $html .= '</div>';

        return $html;
    }
}
