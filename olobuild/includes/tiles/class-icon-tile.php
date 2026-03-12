<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Icon_Tile extends Olo_Tile_Base {

    protected $type     = 'icon';
    protected $name     = 'Icona';
    protected $icon     = 'dashicons-star-filled';
    protected $category = 'essential';
    protected $defaults = [
        'icon'            => 'star',
        'size'            => 40,
        'color'           => '',
        'view'            => 'default',
        'bg_color'        => '',
        'bg_shape'        => 'circle',
        'padding'         => '20',
        'hover_animation' => 'none',
        'rotation'        => '0',
        'link_url'        => '',
        'link_target'     => '_self',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'icon',        'type' => 'text',   'label' => 'Icon Name (UIkit)' ],
            [ 'key' => 'size',        'type' => 'range',  'label' => 'Size (px)', 'min' => 16, 'max' => 120, 'step' => 4 ],
            [ 'key' => 'color',       'type' => 'color',  'label' => 'Color' ],
            [ 'key' => 'link_url',    'type' => 'text',   'label' => 'Link URL' ],
            [ 'key' => 'link_target', 'type' => 'select', 'label' => 'Link Target', 'options' => [
                '_self'  => 'Same Window',
                '_blank' => 'New Window',
            ]],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $icon_name = esc_attr( $s['icon'] );
        $size      = absint( $s['size'] );
        $ratio     = $size > 0 ? round( $size / 20, 2 ) : 2;
        $color     = ! empty( $s['color'] ) ? $this->safe_color_css( $s['color'] ) : '';
        $target    = $s['link_target'] === '_blank' ? ' target="_blank" rel="noopener"' : '';

        $icon_style = $color ? ' style="color:' . $color . ';"' : '';

        /* --- wrapper styling --- */
        $view   = $s['view'] ?? 'default';
        $bg_clr = $this->safe_color_css( $s['bg_color'] ) ?: 'var(--olo-color-primary, #6366F1)';
        $shape  = $s['bg_shape'] ?? 'circle';
        $pad    = absint( $s['padding'] ?? 20 );
        $rot    = intval( $s['rotation'] ?? 0 );
        $anim   = $s['hover_animation'] ?? 'none';

        $radius = $shape === 'circle' ? '50%' : ( $shape === 'rounded' ? '12px' : '0' );
        $wrapper_style = 'display:inline-flex;align-items:center;justify-content:center;';
        if ( $rot !== 0 ) {
            $wrapper_style .= "transform:rotate({$rot}deg);";
        }
        if ( $view === 'stacked' ) {
            $wrapper_style .= "background:{$bg_clr};padding:{$pad}px;border-radius:{$radius};";
        } elseif ( $view === 'framed' ) {
            $wrapper_style .= "border:2px solid {$bg_clr};padding:{$pad}px;border-radius:{$radius};";
        }
        $wrapper_style .= 'transition:transform 0.3s ease;';

        /* --- hover animation --- */
        $uid = 'olo-icon-' . wp_rand( 10000, 99999 );
        $anim_css = '';
        if ( $anim !== 'none' ) {
            $hover_transform = '';
            switch ( $anim ) {
                case 'grow':
                    $hover_transform = 'transform:scale(1.2);';
                    break;
                case 'shake':
                    $anim_css = "@keyframes olo-shake{0%,100%{transform:translateX(0)}25%{transform:translateX(-4px)}75%{transform:translateX(4px)}}.{$uid}:hover .olo-icon-wrap{animation:olo-shake 0.5s ease;}";
                    break;
                case 'bounce':
                    $anim_css = "@keyframes olo-bounce{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}.{$uid}:hover .olo-icon-wrap{animation:olo-bounce 0.6s ease;}";
                    break;
                case 'spin':
                    $anim_css = "@keyframes olo-spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}.{$uid}:hover .olo-icon-wrap{animation:olo-spin 0.8s ease;}";
                    break;
                case 'pulse':
                    $anim_css = "@keyframes olo-pulse{0%,100%{transform:scale(1)}50%{transform:scale(1.15)}}.{$uid}:hover .olo-icon-wrap{animation:olo-pulse 1s ease infinite;}";
                    break;
            }
            if ( $hover_transform ) {
                $anim_css = ".{$uid}:hover .olo-icon-wrap{{$hover_transform}}";
            }
        }

        ob_start();
        if ( $anim_css ) {
            echo '<style>' . $anim_css . '</style>';
        }
        ?>
        <div class="olo-icon uk-text-center <?php echo esc_attr( $uid ); ?>">
            <span class="olo-icon-wrap" style="<?php echo $wrapper_style; ?>">
            <?php if ( ! empty( $s['link_url'] ) ) : ?>
                <a href="<?php echo esc_url( $s['link_url'] ); ?>"<?php echo $target; ?>>
                    <span<?php echo $icon_style; ?> uk-icon="icon: <?php echo $icon_name; ?>; ratio: <?php echo $ratio; ?>"></span>
                </a>
            <?php else : ?>
                <span<?php echo $icon_style; ?> uk-icon="icon: <?php echo $icon_name; ?>; ratio: <?php echo $ratio; ?>"></span>
            <?php endif; ?>
            </span>
        </div>
        <?php
        return ob_get_clean();
    }
}
