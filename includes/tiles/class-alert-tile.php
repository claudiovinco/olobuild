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
        'preset' => 'custom',
        'alert_type'          => 'info',
        'title'               => 'Attenzione!',
        'message'             => 'Questo è un avviso informativo.',
        'show_icon'           => true,
        'custom_icon'         => '',
        'dismissible'         => false,
        'custom_bg_color'     => '',
        'custom_border_color' => '',
        'custom_text_color'   => '',
            'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
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

        // Icone SVG dal set (UIkit), MAI emoji: rese coerenti col builder
        // (AlertTile.vue usa gli stessi nomi: info/check/warning/ban).
        $icons = [
            'info'    => 'info',
            'success' => 'check',
            'warning' => 'warning',
            'error'   => 'ban',
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
        $alert_ta = $s['text_align'] ?? '';
        if ( in_array( $alert_ta, [ 'left', 'center', 'right', 'justify' ], true ) ) {
            $inline_styles[] = 'text-align:' . $alert_ta;
        }
        $style_attr = ! empty( $inline_styles ) ? ' style="' . implode( ';', $inline_styles ) . '"' : '';

        $alert_uid = 'olo-alert-' . wp_unique_id();
        $title_plain = wp_strip_all_tags( $s['title'] ?? '' );
        $msg_plain   = wp_strip_all_tags( $s['message'] ?? '' );
        list( $t_tfx_cls, $t_tfx_data ) = $this->tfx_attrs( $s, 'title', $title_plain );
        list( $m_tfx_cls, $m_tfx_data ) = $this->tfx_attrs( $s, 'message', $msg_plain );

        ob_start();
        ?>
        <div class="olo-alert uk-alert uk-alert-<?php echo esc_attr( $uk_type ); ?> <?php echo $alert_uid; ?> olo-alert-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>"<?php echo $style_attr; ?> uk-alert>
            <?php if ( ! empty( $s['dismissible'] ) ) : ?>
                <a class="uk-alert-close" uk-close></a>
            <?php endif; ?>
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                <?php if ( ! empty( $s['custom_icon'] ) ) : ?>
                    <span style="flex-shrink: 0;" uk-icon="icon: <?php echo esc_attr( $s['custom_icon'] ); ?>; ratio: 1.2"></span>
                <?php elseif ( $s['show_icon'] ) : ?>
                    <span style="flex-shrink: 0;" uk-icon="icon: <?php echo esc_attr( $icon ); ?>; ratio: 1.2"></span>
                <?php endif; ?>
                <div style="flex: 1;">
                    <?php if ( ! empty( $s['title'] ) ) : ?>
                        <div class="olo-alert-title<?php echo $t_tfx_cls; ?>" style="font-weight: 600; margin-bottom: 4px;"<?php echo $t_tfx_data; ?>><?php echo esc_html( $title_plain ); ?></div>
                    <?php endif; ?>
                    <div class="olo-alert-msg<?php echo $m_tfx_cls; ?>" style="font-size: 0.9em; line-height: 1.5;"<?php echo $m_tfx_data; ?>><?php echo nl2br( esc_html( $msg_plain ) ); ?></div>
                </div>
            </div>
        </div>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $alert_uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$alert_uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$alert_uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$alert_uid}{{$border_css}}";
            echo $border_hover_css . $border_effect_css . '</style>';
        }
        return ob_get_clean();
    }
}
