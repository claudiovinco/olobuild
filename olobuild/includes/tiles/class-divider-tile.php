<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Divider_Tile extends Olo_Tile_Base {

    protected $type     = 'divider';
    protected $name     = 'Divisore';
    protected $icon     = 'dashicons-minus';
    protected $category = 'essential';
    protected $defaults = [
        'style'      => 'solid',
        'width'      => '100',
        'thickness'  => '1',
        'color'      => '',
        'alignment'  => 'center',
        'text'       => '',
        'text_color' => '',
        'icon_emoji' => '',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'style',      'type' => 'select', 'label' => 'Style' ],
            [ 'key' => 'width',      'type' => 'range',  'label' => 'Width (%)' ],
            [ 'key' => 'thickness',  'type' => 'range',  'label' => 'Thickness' ],
            [ 'key' => 'color',      'type' => 'color',  'label' => 'Color' ],
            [ 'key' => 'alignment',  'type' => 'select', 'label' => 'Alignment' ],
            [ 'key' => 'text',       'type' => 'text',   'label' => 'Center Text' ],
            [ 'key' => 'text_color', 'type' => 'color',  'label' => 'Text Color' ],
            [ 'key' => 'icon_emoji', 'type' => 'text',   'label' => 'Center Emoji' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $w     = min( max( absint( $s['width'] ), 10 ), 100 );
        $thick = min( max( absint( $s['thickness'] ), 1 ), 10 );
        $align_map = [ 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' ];
        $justify   = $align_map[ $s['alignment'] ] ?? 'center';

        $line_clr = $this->safe_color_css( $s['color'] ) ?: 'var(--olo-color-text, #374151)';
        $txt_clr  = $this->safe_color_css( $s['text_color'] );
        $has_center = ! empty( $s['text'] ) || ! empty( $s['icon_emoji'] );

        // Use UIkit divider classes for simple cases, custom styling for complex ones
        if ( ! $has_center && $w === 100 && $s['style'] === 'solid' ) {
            ob_start();
            ?>
            <div class="olo-divider" style="padding:16px;">
                <hr class="uk-divider-small" style="border-top-color:<?php echo $line_clr; ?>;border-top-width:<?php echo $thick; ?>px;">
            </div>
            <?php
            return ob_get_clean();
        }

        // Complex cases: keep custom styling
        if ( $s['style'] === 'gradient' ) {
            $line_style = 'border:none;height:' . $thick . 'px;background:linear-gradient(90deg, transparent, ' . $line_clr . ', transparent);';
        } else {
            $line_style = 'border:none;border-top:' . $thick . 'px ' . esc_attr( $s['style'] ) . ' ' . $line_clr . ';';
        }

        ob_start();
        ?>
        <div class="olo-divider" style="display:flex;justify-content:<?php echo esc_attr( $justify ); ?>;padding:16px;">
            <?php if ( $has_center ) : ?>
                <div style="display:flex;align-items:center;gap:16px;width:<?php echo $w; ?>%;">
                    <hr style="flex:1;margin:0;<?php echo $line_style; ?>" />
                    <span style="<?php if ( $txt_clr ) echo 'color:' . $txt_clr . ';'; ?>font-size:0.875em;white-space:nowrap;">
                        <?php if ( ! empty( $s['icon_emoji'] ) ) :
                            if ( preg_match( '/^[a-z][a-z0-9-]*$/', $s['icon_emoji'] ) ) : ?>
                                <span uk-icon="icon: <?php echo esc_attr( $s['icon_emoji'] ); ?>"></span>
                            <?php else :
                                echo esc_html( $s['icon_emoji'] ) . ' ';
                            endif;
                        endif;
                        echo esc_html( $s['text'] ); ?>
                    </span>
                    <hr style="flex:1;margin:0;<?php echo $line_style; ?>" />
                </div>
            <?php else : ?>
                <hr style="width:<?php echo $w; ?>%;margin:0;<?php echo $line_style; ?>" />
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
