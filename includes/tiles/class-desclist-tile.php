<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_DescList_Tile extends Olo_Tile_Base {

    protected $type     = 'desclist';
    protected $name     = 'Lista descrittiva';
    protected $icon     = 'dashicons-editor-justify';
    protected $category = 'text';
    protected $defaults = [
        'preset' => 'custom',
        'items'                => [
            [ 'term' => 'Framework', 'definition' => 'Vue.js 3 con Composition API', 'icon' => 'code' ],
            [ 'term' => 'Linguaggio', 'definition' => 'PHP 7.4+ con WordPress', 'icon' => 'server' ],
            [ 'term' => 'Build Tool', 'definition' => 'Vite 5', 'icon' => 'bolt' ],
        ],
        'layout'               => 'stacked',
        'show_icon'            => true,
        'icon_color'           => '',
        'icon_size'            => '20',
        'term_color'           => '',
        'term_font_size'       => '15',
        'term_font_weight'     => '600',
        'definition_color'     => '',
        'definition_font_size' => '14',
        'separator'            => true,
        'border_color'         => '',
        'spacing'              => '16',
        'striped'              => false,
        'striped_color'        => 'rgba(255,255,255,0.03)',
        // Text effects
        'text_effect'             => 'none',
        'text_effect_target'      => 'definition',
        'text_effect_speed'       => '50',
        'text_effect_delay'       => '0',
        'text_effect_loop'        => false,
        'text_effect_cursor'      => true,
        'text_effect_cursor_char' => '|',
        'text_effect_color'       => '',
        'text_effect_color_to'    => '',
        'text_effect_phrases'     => '',
        'text_effect_pause'       => '1500',
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
            return '<div class="olo-desclist" style="padding:20px;text-align:center;color:var(--olo-color-text-faint, #9CA3AF);">Nessun elemento definito</div>';
        }

        $uid    = 'mdl-' . wp_rand( 10000, 99999 );
        $layout = in_array( $s['layout'], [ 'stacked', 'inline', 'grid' ], true ) ? $s['layout'] : 'stacked';

        $term_clr    = $this->safe_color_css( $s['term_color'] );
        $def_clr     = $this->safe_color_css( $s['definition_color'] );
        $brd_clr     = $this->safe_color_css( $s['border_color'] ) ?: 'var(--olo-color-border, #E5E7EB)';
        $icon_clr    = $this->safe_color_css( $s['icon_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
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
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: colors via the safe_color_css() whitelist (with var() token fallbacks), striped color esc_attr()'d inline, integers via absint(); $uid is internally generated. ?>
        <style>
            .<?php echo $uid; ?> {
                margin: 0;
                padding: 0;
                list-style: none;
            }
            .<?php echo $uid; ?> .mdl-item {
                padding: <?php echo (int) $spacing; ?>px 16px;
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
                font-size: <?php echo (int) $term_fs; ?>px;
                font-weight: <?php echo (int) $term_fw; ?>;
                <?php if ( $term_clr ) : ?>color: <?php echo $term_clr; ?>;<?php endif; ?>
                line-height: 1.4;
                margin: 0;
            }
            .<?php echo $uid; ?> .mdl-def {
                font-size: <?php echo (int) $def_fs; ?>px;
                <?php if ( $def_clr ) : ?>color: <?php echo $def_clr; ?>;<?php endif; ?>
                line-height: 1.6;
                margin: 0;
                <?php if ( $layout === 'stacked' ) : ?>margin-top: 4px;<?php endif; ?>
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <?php
        $dl_ta = $s['text_align'] ?? '';
        $dl_ta_css = in_array( $dl_ta, [ 'left', 'center', 'right', 'justify' ], true ) ? 'text-align:' . $dl_ta . ';' : '';
        ?>
        <dl class="olo-desclist <?php echo esc_attr( $uid ); ?><?php echo $show_icon ? ' mdl-has-icon' : ''; ?> olo-dl-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" style="<?php echo $dl_ta_css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed text-align declaration from the in_array() whitelist above ?>">
            <?php foreach ( $items as $item ) :
                $icon = $item['icon'] ?? '';
                $has_icon = $show_icon && ! empty( $icon );
                $has_link = ! empty( $item['link'] );
                $item_tag  = $has_link ? '<a href="' . esc_url( $item['link'] ) . '" class="mdl-item" role="group" style="text-decoration:none;color:inherit;display:block;">' : '<div class="mdl-item" role="group">';
                $item_close = $has_link ? '</a>' : '</div>';
            ?>
            <?php
                $term_plain = wp_strip_all_tags( $item['term'] );
                $def_plain  = wp_strip_all_tags( $item['definition'] );
                list( $term_cls, $term_data ) = $this->tfx_attrs( $s, 'term', $term_plain );
                list( $def_cls,  $def_data  ) = $this->tfx_attrs( $s, 'definition', $def_plain );
            ?>
            <?php echo $item_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- opening markup built above from fixed literals with the link escaped via esc_url() ?>
                <?php if ( $layout === 'stacked' ) : ?>
                    <div class="mdl-row">
                        <?php if ( $has_icon ) : ?>
                            <span class="mdl-icon-wrap">
                                <?php echo $this->render_icon( $icon, $icon_size ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- icon markup built by render_icon() with esc_attr()/esc_html()/absint() internally ?>
                            </span>
                        <?php endif; ?>
                        <div class="mdl-text">
                            <dt class="mdl-term<?php echo $term_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); term escaped inline ?>"<?php echo $term_data; ?>><?php echo esc_html( $term_plain ); ?></dt>
                            <dd class="mdl-def<?php echo $def_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); definition esc_html()'d (nl2br only adds <br /> tags) ?>"<?php echo $def_data; ?>><?php echo nl2br( esc_html( $def_plain ) ); ?></dd>
                        </div>
                    </div>
                <?php elseif ( $layout === 'inline' ) : ?>
                    <div class="mdl-row">
                        <?php if ( $has_icon ) : ?>
                            <span class="mdl-icon-wrap">
                                <?php echo $this->render_icon( $icon, $icon_size ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- icon markup built by render_icon() with esc_attr()/esc_html()/absint() internally ?>
                            </span>
                        <?php endif; ?>
                        <dt class="mdl-term<?php echo $term_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); term escaped inline ?>"<?php echo $term_data; ?>><?php echo esc_html( $term_plain ); ?></dt>
                        <dd class="mdl-def<?php echo $def_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); definition esc_html()'d (nl2br only adds <br /> tags) ?>"<?php echo $def_data; ?>><?php echo nl2br( esc_html( $def_plain ) ); ?></dd>
                    </div>
                <?php elseif ( $layout === 'grid' ) : ?>
                    <div class="mdl-row">
                        <?php if ( $has_icon ) : ?>
                            <span class="mdl-icon-wrap">
                                <?php echo $this->render_icon( $icon, $icon_size ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- icon markup built by render_icon() with esc_attr()/esc_html()/absint() internally ?>
                            </span>
                        <?php endif; ?>
                        <dt class="mdl-term<?php echo $term_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); term escaped inline ?>"<?php echo $term_data; ?>><?php echo esc_html( $term_plain ); ?></dt>
                        <dd class="mdl-def<?php echo $def_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); definition esc_html()'d (nl2br only adds <br /> tags) ?>"<?php echo $def_data; ?>><?php echo nl2br( esc_html( $def_plain ) ); ?></dd>
                    </div>
                <?php endif; ?>
            <?php echo $item_close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed '</a>'/'</div>' literal from the ternary above ?>
            <?php endforeach; ?>
        </dl>
        <?php
        // Text effects: CSS scoped + runtime script (una sola volta per request)
        $tfx_css = $this->tfx_css( $s, '.olo-desclist' );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Text_Effects::css() from whitelisted effects, sanitized colors and integer timings
        $this->tfx_print_script();
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_css() from sanitized border settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base border helpers from sanitized border settings
        }
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
                        'link'       => $item['link'] ?? '',
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
