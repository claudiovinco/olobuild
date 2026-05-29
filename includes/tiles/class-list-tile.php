<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_List_Tile extends Olo_Tile_Base {

    protected $type     = 'list';
    protected $name     = 'Lista';
    protected $icon     = 'dashicons-editor-ul';
    protected $category = 'text';
    protected $defaults = [
        'preset' => 'custom',
        'items'        => [
            [ 'text' => 'Prima voce della lista', 'icon' => 'check' ],
            [ 'text' => 'Seconda voce della lista', 'icon' => 'check' ],
            [ 'text' => 'Terza voce della lista', 'icon' => 'check' ],
        ],
        'icon_default' => 'check',
        'icon_color'   => '#22C55E',
        'text_color'   => '',
        'spacing'      => '12',
        'icon_size'    => '18',
        'icon_gap'     => '10',
        'padding'      => '16',
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
            [ 'key' => 'items',        'type' => 'custom',   'label' => 'Items' ],
            [ 'key' => 'icon_default', 'type' => 'select',   'label' => 'Default Icon' ],
            [ 'key' => 'icon_color',   'type' => 'color',    'label' => 'Icon Color' ],
            [ 'key' => 'text_color',   'type' => 'color',    'label' => 'Text Color' ],
            [ 'key' => 'spacing',      'type' => 'range',    'label' => 'Spacing' ],
            [ 'key' => 'icon_size',    'type' => 'range',    'label' => 'Icon Size' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $items   = $this->parse_items( $s['items'], $s['icon_default'] );
        $spacing = absint( $s['spacing'] );
        $isize   = absint( $s['icon_size'] );
        $igap    = absint( $s['icon_gap'] ?? 10 );
        $pad = Olo_Tile_Utils::spacing_css( $s['tile_padding'] ?? $s['padding'] ?? 16, 16 );
        $default_icon = $s['icon_default'] ?: 'check';

        // Shadow
        $shadow_css = '';
        $shadow_val = $s['shadow'] ?? 'none';
        $shadow_map = [
            'sm' => '0 1px 3px 0 rgba(0,0,0,0.12),0 1px 2px -1px rgba(0,0,0,0.1)',
            'md' => '0 4px 6px -1px rgba(0,0,0,0.15),0 2px 4px -2px rgba(0,0,0,0.12)',
            'lg' => '0 10px 15px -3px rgba(0,0,0,0.18),0 4px 6px -4px rgba(0,0,0,0.12)',
            'xl' => '0 20px 25px -5px rgba(0,0,0,0.2),0 8px 10px -6px rgba(0,0,0,0.15)',
        ];
        if ( isset( $shadow_map[ $shadow_val ] ) ) {
            $shadow_css = 'box-shadow:' . $shadow_map[ $shadow_val ] . ';';
        } elseif ( $shadow_val === 'custom' ) {
            $sh = intval( $s['shadow_h'] ?? 0 );
            $sv = intval( $s['shadow_v'] ?? 4 );
            $sb = intval( $s['shadow_blur'] ?? 10 );
            $ss = intval( $s['shadow_spread'] ?? 0 );
            $sc = $s['shadow_color'] ?? 'rgba(0,0,0,0.15)';
            $si = ! empty( $s['shadow_inset'] ) ? 'inset ' : '';
            $shadow_css = 'box-shadow:' . $si . $sh . 'px ' . $sv . 'px ' . $sb . 'px ' . $ss . 'px ' . esc_attr( $sc ) . ';';
        }

                $uid = 'olo-list-' . wp_rand( 10000, 99999 );
ob_start();
        ?>
        <?php
        $list_text_clr = $this->safe_color_css( $s['text_color'] );
        $list_ta_val  = $s['text_align'] ?? '';
        $list_ta_css  = in_array( $list_ta_val, [ 'left', 'center', 'right', 'justify' ], true ) ? 'text-align:' . $list_ta_val . ';flex:1;' : '';
        ?>
        <ul class="olo-list <?php echo esc_attr( $uid ); ?> uk-list olo-ls-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" style="padding: <?php echo $pad; ?>;<?php echo $shadow_css; ?><?php if ( $list_text_clr ) echo 'color:' . $list_text_clr . ';'; ?>"><?php // kept on same line to avoid whitespace ?>
            <?php foreach ( $items as $i => $item ) :
                $icon = ! empty( $item['icon'] ) ? $item['icon'] : $default_icon;
            ?>
                <?php
                    $has_link = ! empty( $item['link'] );
                    $li_tag = $has_link
                        ? '<a href="' . esc_url( $item['link'] ) . '" style="display:flex;align-items:flex-start;gap:' . $igap . 'px;text-decoration:none;color:inherit;' . ( $i > 0 ? 'margin-top:' . $spacing . 'px;' : '' ) . '">'
                        : '<li style="display:flex;align-items:flex-start;gap:' . $igap . 'px;' . ( $i > 0 ? 'margin-top:' . $spacing . 'px;' : '' ) . '">';
                    $li_close = $has_link ? '</a>' : '</li>';
                ?>
                <?php echo $li_tag; ?>
                    <?php if ( $icon === 'number' ) : ?>
                        <span style="flex-shrink:0;font-weight:700;line-height:normal;font-size:<?php echo $isize; ?>px;min-width:<?php echo $isize; ?>px;text-align:center;color:<?php echo esc_attr( $this->safe_color_css( $s['icon_color'] ) ?: 'currentColor' ); ?>;"><?php echo ( $i + 1 ); ?>.</span>
                    <?php else : ?>
                        <span style="flex-shrink:0;display:flex;align-items:center;line-height:1;"><?php echo $this->get_icon_svg( $icon, $s['icon_color'], $isize ); ?></span>
                    <?php endif; ?>
                    <?php list( $li_cls, $li_data ) = $this->tfx_attrs( $s, 'text', $item['text'] ); ?>
                    <span class="olo-list-text<?php echo $li_cls; ?>" style="line-height:1.5;<?php echo $list_ta_css; ?>"<?php echo $li_data; ?>><?php echo esc_html( $item['text'] ); ?></span>
                <?php echo $li_close; ?>
            <?php endforeach; ?>
        </ul>
        <?php
        $tfx_css = $this->tfx_css( $s, '.olo-list' );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}";
            echo $border_hover_css . $border_effect_css . '</style>';
        }
        return ob_get_clean();
    }

    /**
     * Parse items — supports both new array format and legacy textarea string.
     */
    private function parse_items( $raw, $default_icon ) {
        // New format: array of objects
        if ( is_array( $raw ) && ! empty( $raw ) ) {
            $items = [];
            foreach ( $raw as $item ) {
                if ( is_array( $item ) && ! empty( $item['text'] ) ) {
                    $items[] = [
                        'icon' => $item['icon'] ?? $default_icon,
                        'text' => $item['text'],
                        'link' => $item['link'] ?? '',
                    ];
                }
            }
            if ( ! empty( $items ) ) {
                return $items;
            }
        }

        // Legacy format: "icon|text\nicon|text"
        $items = [];
        $text  = is_array( $raw ) ? implode( "\n", $raw ) : (string) $raw;
        $lines = array_filter( array_map( 'trim', explode( "\n", $text ) ) );
        foreach ( $lines as $line ) {
            $parts = explode( '|', $line, 2 );
            if ( count( $parts ) === 2 ) {
                $items[] = [ 'icon' => trim( $parts[0] ), 'text' => trim( $parts[1] ) ];
            } else {
                $items[] = [ 'icon' => $default_icon, 'text' => trim( $parts[0] ) ];
            }
        }
        return $items;
    }

    private function get_icon_svg( $icon, $color, $size ) {
        $c = $this->safe_color_css( $color ) ?: 'currentColor';
        $s = intval( $size );
        switch ( $icon ) {
            case 'check':
                return '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="' . $c . '" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            case 'arrow':
                return '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="none"><path d="M5 12h14m-6-6l6 6-6 6" stroke="' . $c . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            case 'star':
                return '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="' . $c . '"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.27 5.82 22 7 14.14l-5-4.87 6.91-1.01z"/></svg>';
            case 'dot':
                return '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5" fill="' . $c . '"/></svg>';
            case 'x':
                return '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="none"><path d="M18 6L6 18M6 6l12 12" stroke="' . $c . '" stroke-width="2.5" stroke-linecap="round"/></svg>';
            case 'heart':
                return '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="' . $c . '"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>';
            case 'bolt':
                return '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="' . $c . '"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>';
            case 'info':
                return '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="' . $c . '" stroke-width="2"/><path d="M12 16v-4m0-4h.01" stroke="' . $c . '" stroke-width="2" stroke-linecap="round"/></svg>';
            case 'warning':
                return '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="none"><path d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" stroke="' . $c . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            default:
                return '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="' . $c . '" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        }
    }
}
