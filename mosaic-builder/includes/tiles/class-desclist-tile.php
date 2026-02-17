<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_DescList_Tile extends Olo_Tile_Base {

    protected $type     = 'desclist';
    protected $name     = 'Lista descrittiva';
    protected $icon     = 'dashicons-editor-justify';
    protected $category = 'content';
    protected $defaults = [
        'items'                => [],
        'layout'               => 'stacked',
        'show_icon'            => true,
        'icon_color'           => '#6366F1',
        'icon_size'            => '20',
        'term_color'           => '#F3F4F6',
        'term_font_size'       => '15',
        'term_font_weight'     => '600',
        'definition_color'     => '#9CA3AF',
        'definition_font_size' => '14',
        'separator'            => true,
        'border_color'         => '#374151',
        'spacing'              => '16',
        'striped'              => false,
        'striped_color'        => 'rgba(255,255,255,0.03)',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'items',            'type' => 'custom',   'label' => 'Items' ],
            [ 'key' => 'layout',           'type' => 'select',   'label' => 'Layout' ],
            [ 'key' => 'show_icon',        'type' => 'toggle',   'label' => 'Show Icon' ],
            [ 'key' => 'icon_color',       'type' => 'color',    'label' => 'Icon Color' ],
            [ 'key' => 'icon_size',        'type' => 'range',    'label' => 'Icon Size' ],
            [ 'key' => 'term_color',       'type' => 'color',    'label' => 'Term Color' ],
            [ 'key' => 'definition_color', 'type' => 'color',    'label' => 'Definition Color' ],
            [ 'key' => 'separator',        'type' => 'toggle',   'label' => 'Show Separator' ],
            [ 'key' => 'border_color',     'type' => 'color',    'label' => 'Border Color' ],
        ];
    }

    public function render( $settings ) {
        $s     = wp_parse_args( $settings, $this->defaults );
        $items = $this->parse_items( $s['items'] );

        if ( empty( $items ) ) {
            return '<div class="olo-desclist" style="padding:20px;text-align:center;color:#6b7280;">Nessun elemento definito</div>';
        }

        $uid    = 'mdl-' . wp_rand( 10000, 99999 );
        $layout = in_array( $s['layout'], [ 'stacked', 'inline', 'grid' ], true ) ? $s['layout'] : 'stacked';

        $term_clr    = $this->safe_color( $s['term_color'] );
        $def_clr     = $this->safe_color( $s['definition_color'] );
        $brd_clr     = $this->safe_color( $s['border_color'] ) ?: '#374151';
        $icon_clr    = $this->safe_color( $s['icon_color'] ) ?: '#6366F1';
        $icon_size   = absint( $s['icon_size'] );
        $show_icon   = ! empty( $s['show_icon'] );
        $show_sep    = ! empty( $s['separator'] );
        $spacing     = absint( $s['spacing'] );
        $term_fs     = absint( $s['term_font_size'] );
        $term_fw     = absint( $s['term_font_weight'] );
        $def_fs      = absint( $s['definition_font_size'] );
        $striped     = ! empty( $s['striped'] );
        $striped_clr = $s['striped_color'] ?? 'rgba(255,255,255,0.03)';

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                margin: 0;
                padding: 0;
                list-style: none;
            }
            .<?php echo $uid; ?> .mdl-item {
                padding: <?php echo $spacing; ?>px 16px;
                <?php if ( $show_sep ) : ?>
                border-bottom: 1px solid <?php echo $brd_clr; ?>;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .mdl-item:last-child {
                border-bottom: none;
            }
            <?php if ( $striped ) : ?>
            .<?php echo $uid; ?> .mdl-item:nth-child(even) {
                background: <?php echo esc_attr( $striped_clr ); ?>;
            }
            <?php endif; ?>
            <?php if ( $layout === 'stacked' ) : ?>
            .<?php echo $uid; ?> .mdl-row {
                display: flex;
                align-items: flex-start;
                gap: 12px;
            }
            .<?php echo $uid; ?> .mdl-text {
                flex: 1;
                min-width: 0;
            }
            <?php elseif ( $layout === 'inline' ) : ?>
            .<?php echo $uid; ?> .mdl-row {
                display: flex;
                align-items: baseline;
                gap: 12px;
            }
            .<?php echo $uid; ?> .mdl-term {
                white-space: nowrap;
            }
            .<?php echo $uid; ?> .mdl-def {
                flex: 1;
                min-width: 0;
            }
            <?php elseif ( $layout === 'grid' ) : ?>
            .<?php echo $uid; ?> .mdl-row {
                display: grid;
                grid-template-columns: auto 1fr;
                gap: 4px 24px;
                align-items: baseline;
            }
            .<?php echo $uid; ?> .mdl-icon-wrap {
                grid-row: span 2;
                align-self: start;
                padding-top: 2px;
            }
            .<?php echo $uid; ?>.mdl-has-icon .mdl-row {
                grid-template-columns: auto auto 1fr;
            }
            <?php endif; ?>
            .<?php echo $uid; ?> .mdl-icon-wrap {
                flex-shrink: 0;
                display: inline-flex;
                align-items: center;
                color: <?php echo $icon_clr; ?>;
            }
            .<?php echo $uid; ?> .mdl-term {
                font-size: <?php echo $term_fs; ?>px;
                font-weight: <?php echo $term_fw; ?>;
                <?php if ( $term_clr ) : ?>color: <?php echo $term_clr; ?>;<?php endif; ?>
                line-height: 1.4;
                margin: 0;
            }
            .<?php echo $uid; ?> .mdl-def {
                font-size: <?php echo $def_fs; ?>px;
                <?php if ( $def_clr ) : ?>color: <?php echo $def_clr; ?>;<?php endif; ?>
                line-height: 1.6;
                margin: 0;
                <?php if ( $layout === 'stacked' ) : ?>margin-top: 4px;<?php endif; ?>
            }
        </style>

        <div class="olo-desclist <?php echo esc_attr( $uid ); ?><?php echo $show_icon ? ' mdl-has-icon' : ''; ?>">
            <?php foreach ( $items as $item ) :
                $icon = $item['icon'] ?? '';
                $has_icon = $show_icon && ! empty( $icon );
            ?>
            <div class="mdl-item">
                <?php if ( $layout === 'stacked' ) : ?>
                    <div class="mdl-row">
                        <?php if ( $has_icon ) : ?>
                            <span class="mdl-icon-wrap">
                                <?php echo $this->render_icon( $icon, $icon_size ); ?>
                            </span>
                        <?php endif; ?>
                        <div class="mdl-text">
                            <dt class="mdl-term"><?php echo wp_kses_post( $item['term'] ); ?></dt>
                            <dd class="mdl-def"><?php echo wp_kses_post( $item['definition'] ); ?></dd>
                        </div>
                    </div>
                <?php elseif ( $layout === 'inline' ) : ?>
                    <div class="mdl-row">
                        <?php if ( $has_icon ) : ?>
                            <span class="mdl-icon-wrap">
                                <?php echo $this->render_icon( $icon, $icon_size ); ?>
                            </span>
                        <?php endif; ?>
                        <dt class="mdl-term"><?php echo wp_kses_post( $item['term'] ); ?></dt>
                        <dd class="mdl-def"><?php echo wp_kses_post( $item['definition'] ); ?></dd>
                    </div>
                <?php elseif ( $layout === 'grid' ) : ?>
                    <div class="mdl-row">
                        <?php if ( $has_icon ) : ?>
                            <span class="mdl-icon-wrap">
                                <?php echo $this->render_icon( $icon, $icon_size ); ?>
                            </span>
                        <?php endif; ?>
                        <dt class="mdl-term"><?php echo wp_kses_post( $item['term'] ); ?></dt>
                        <dd class="mdl-def"><?php echo wp_kses_post( $item['definition'] ); ?></dd>
                    </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render icon: UIkit icon name or emoji.
     */
    private function render_icon( $icon, $size = 20 ) {
        if ( preg_match( '/^[a-z][a-z0-9-]*$/', $icon ) ) {
            $ratio = round( $size / 20, 2 );
            return '<span uk-icon="icon: ' . esc_attr( $icon ) . '; ratio: ' . $ratio . '"></span>';
        }
        return '<span style="font-size:' . absint( $size ) . 'px;line-height:1;">' . esc_html( $icon ) . '</span>';
    }

    /**
     * Parse items: supports new array format and legacy string format.
     */
    private function parse_items( $raw ) {
        // New format: array of objects
        if ( is_array( $raw ) ) {
            $items = [];
            foreach ( $raw as $item ) {
                if ( is_array( $item ) && ! empty( $item['term'] ) ) {
                    $items[] = [
                        'term'       => $item['term'],
                        'definition' => $item['definition'] ?? '',
                        'icon'       => $item['icon'] ?? '',
                    ];
                }
            }
            return $items;
        }

        // Legacy format: string "term|definition" per line
        if ( is_string( $raw ) && ! empty( $raw ) ) {
            $items = [];
            $lines = array_filter( array_map( 'trim', explode( "\n", $raw ) ) );
            foreach ( $lines as $line ) {
                $parts = explode( '|', $line, 2 );
                if ( count( $parts ) === 2 ) {
                    $items[] = [ 'term' => trim( $parts[0] ), 'definition' => trim( $parts[1] ), 'icon' => '' ];
                }
            }
            return $items;
        }

        return [];
    }
}
