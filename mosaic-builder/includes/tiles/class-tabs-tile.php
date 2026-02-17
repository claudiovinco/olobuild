<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Tabs_Tile extends Olo_Tile_Base {

    protected $type     = 'tabs';
    protected $name     = 'Schede';
    protected $icon     = 'dashicons-category';
    protected $category = 'content';
    protected $defaults = [
        'tabs_data'    => "Tab 1\nContent for the first tab goes here.\n---\nTab 2\nContent for the second tab goes here.\n---\nTab 3\nContent for the third tab goes here.",
        'accent_color' => '#6366F1',
        'text_color'   => '#F3F4F6',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'tabs_data',    'type' => 'textarea', 'label' => 'Tabs (title\\ncontent\\n--- separator)' ],
            [ 'key' => 'accent_color', 'type' => 'color',    'label' => 'Active Tab Color' ],
            [ 'key' => 'text_color',   'type' => 'color',    'label' => 'Text Color' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $tabs = $this->parse_tabs( $s['tabs_data'] );
        $uid = 'tabs-' . wp_rand( 1000, 9999 );

        ob_start();
        ?>
        <?php $tabs_fg = $this->safe_color( $s['text_color'] ); $tabs_acc = $this->safe_color( $s['accent_color'] ); ?>
        <div class="olo-tabs" style="<?php if ( $tabs_fg ) echo 'color:' . $tabs_fg . ';'; ?>">
            <ul uk-tab>
                <?php foreach ( $tabs as $i => $tab ) : ?>
                    <li class="<?php echo $i === 0 ? 'uk-active' : ''; ?>">
                        <a href="#" style="<?php echo $this->build_style( [ 'border-bottom-color' => $s['accent_color'], 'color' => $s['text_color'] ] ); ?>"><?php echo esc_html( $tab['title'] ); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <ul class="uk-switcher">
                <?php foreach ( $tabs as $tab ) : ?>
                    <li style="padding: 20px 4px; line-height: 1.6;"><?php echo esc_html( $tab['content'] ); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php if ( $tabs_acc ) : ?>
        <style>
            .olo-tabs .uk-active > a { border-bottom-color: <?php echo $tabs_acc; ?> !important; }
        </style>
        <?php endif; ?>
        <?php
        return ob_get_clean();
    }

    private function parse_tabs( $text ) {
        $tabs = [];
        $blocks = preg_split( '/^---$/m', $text );
        foreach ( $blocks as $block ) {
            $lines = array_filter( array_map( 'trim', explode( "\n", trim( $block ) ) ) );
            if ( count( $lines ) >= 2 ) {
                $title = array_shift( $lines );
                $tabs[] = [ 'title' => $title, 'content' => implode( "\n", $lines ) ];
            } elseif ( count( $lines ) === 1 ) {
                $tabs[] = [ 'title' => $lines[0], 'content' => '' ];
            }
        }
        return $tabs;
    }
}
