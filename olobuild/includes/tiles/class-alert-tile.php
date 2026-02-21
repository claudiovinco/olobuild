<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Alert_Tile extends Olo_Tile_Base {

    protected $type     = 'alert';
    protected $name     = 'Avviso';
    protected $icon     = 'dashicons-warning';
    protected $category = 'content';
    protected $defaults = [
        'alert_type' => 'info',
        'title'      => 'Heads up!',
        'message'    => 'This is an informational alert message.',
        'show_icon'  => true,
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

        ob_start();
        ?>
        <div class="olo-alert uk-alert uk-alert-<?php echo esc_attr( $uk_type ); ?>" uk-alert>
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                <?php if ( $s['show_icon'] ) : ?>
                    <span style="font-size: 1.25em; flex-shrink: 0;"><?php echo $icon; ?></span>
                <?php endif; ?>
                <div style="flex: 1;">
                    <?php if ( ! empty( $s['title'] ) ) : ?>
                        <div style="font-weight: 600; margin-bottom: 4px;"><?php echo wp_kses_post( $s['title'] ); ?></div>
                    <?php endif; ?>
                    <div style="font-size: 0.9em; line-height: 1.5;"><?php echo wp_kses_post( $s['message'] ); ?></div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
