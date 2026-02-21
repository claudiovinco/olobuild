<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_List_Tile extends Olo_Tile_Base {

    protected $type     = 'list';
    protected $name     = 'Lista';
    protected $icon     = 'dashicons-editor-ul';
    protected $category = 'content';
    protected $defaults = [
        'items'        => "check|Feature one included\ncheck|Feature two included\ncheck|Feature three included\ncheck|Feature four included",
        'icon_default' => 'check',
        'icon_color'   => '#22C55E',
        'text_color'   => '#F3F4F6',
        'spacing'      => '12',
        'icon_size'    => '18',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'items',        'type' => 'textarea', 'label' => 'Items (icon|text per line)' ],
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

        ob_start();
        ?>
        <?php $list_text_clr = $this->safe_color( $s['text_color'] ); ?>
        <ul class="olo-list uk-list" style="padding:16px;<?php if ( $list_text_clr ) echo 'color:' . $list_text_clr . ';'; ?>">
            <?php foreach ( $items as $i => $item ) :
                $svg = $this->get_icon_svg( $item['icon'], $s['icon_color'], $isize );
            ?>
                <li style="display:flex;align-items:flex-start;gap:10px;<?php echo $i > 0 ? 'margin-top:' . $spacing . 'px;' : ''; ?>">
                    <span style="flex-shrink:0;display:flex;align-items:center;line-height:1;"><?php echo $svg; ?></span>
                    <span style="line-height:1.5;"><?php echo esc_html( $item['text'] ); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php
        return ob_get_clean();
    }

    private function parse_items( $text, $default_icon ) {
        $items = [];
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
        $c = esc_attr( $color );
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
            case 'number':
                return '';
            default:
                return '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="' . $c . '" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        }
    }
}
