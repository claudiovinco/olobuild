<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Alert_Tile extends Olo_Tile_Base {

    protected $type     = 'alert';
    protected $name     = 'Avviso';
    protected $icon     = 'dashicons-warning';
    protected $category = 'text';
    protected $defaults = [
        'alert_type'          => 'info',
        'title'               => 'Heads up!',
        'message'             => 'This is an informational alert message.',
        'show_icon'           => true,
        'custom_icon'         => '',
        'dismissible'         => false,
        'custom_bg_color'     => '',
        'custom_border_color' => '',
        'custom_text_color'   => '',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'alert_type', 'type' => 'select', 'label' => 'Type', 'options' => [
                'info'    => 'Info',
                'success' => 'Success',
                'warning' => 'Warning',
                'error'   => 'Error',
            ]],
            [ 'key' => 'title',     'type' => 'text',     'label' => 'Title' ],
            [ 'key' => 'message',   'type' => 'textarea', 'label' => 'Message' ],
            [ 'key' => 'show_icon', 'type' => 'toggle',   'label' => 'Show Icon' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        // Map alert types to UIkit classes
        $type_map = [
            'info'    => 'primary',
            'success' => 'success',
            'warning' => 'warning',
            'error'   => 'danger',
        ];
        $uk_type = $type_map[ $s['alert_type'] ] ?? 'primary';

        $icons = [
            'info'    => '&#x2139;&#xFE0F;',
            'success' => '&#x2705;',
            'warning' => '&#x26A0;&#xFE0F;',
            'error'   => '&#x274C;',
        ];
        $icon = $icons[ $s['alert_type'] ] ?? $icons['info'];

        // Build inline styles for custom colors
        $inline_styles = [];
        if ( ! empty( $s['custom_bg_color'] ) ) {
            $inline_styles[] = 'background-color:' . esc_attr( $s['custom_bg_color'] );
        }
        if ( ! empty( $s['custom_border_color'] ) ) {
            $inline_styles[] = 'border-left:4px solid ' . esc_attr( $s['custom_border_color'] );
        }
        if ( ! empty( $s['custom_text_color'] ) ) {
            $inline_styles[] = 'color:' . esc_attr( $s['custom_text_color'] );
        }
        $style_attr = ! empty( $inline_styles ) ? ' style="' . implode( ';', $inline_styles ) . '"' : '';

        ob_start();
        ?>
        <div class="olo-alert uk-alert uk-alert-<?php echo esc_attr( $uk_type ); ?>"<?php echo $style_attr; ?> uk-alert>
            <?php if ( ! empty( $s['dismissible'] ) ) : ?>
                <a class="uk-alert-close" uk-close></a>
            <?php endif; ?>
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                <?php if ( ! empty( $s['custom_icon'] ) ) : ?>
                    <span style="flex-shrink: 0;" uk-icon="icon: <?php echo esc_attr( $s['custom_icon'] ); ?>; ratio: 1.2"></span>
                <?php elseif ( $s['show_icon'] ) : ?>
                    <span style="font-size: 1.25em; flex-shrink: 0;"><?php echo $icon; ?></span>
                <?php endif; ?>
                <div style="flex: 1;">
                    <?php if ( ! empty( $s['title'] ) ) : ?>
                        <div style="font-weight: 600; margin-bottom: 4px;"><?php echo esc_html( wp_strip_all_tags( $s['title'] ) ); ?></div>
                    <?php endif; ?>
                    <div style="font-size: 0.9em; line-height: 1.5;"><?php echo nl2br( esc_html( wp_strip_all_tags( $s['message'] ) ) ); ?></div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
