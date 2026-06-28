<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Olobuild_Iconlist_Tile extends Olobuild_Tile_Base {
    protected $type     = 'iconlist';
    protected $name     = 'Lista icone';
    protected $icon     = 'dashicons-list-view';
    protected $category = 'text';
    protected $defaults = [
        'preset' => 'custom',
        'items'          => [],
        'icon_color'     => '',
        'icon_size'      => '20',
        'text_color'     => '',
        'text_size'      => '16',
        'gap'            => '12',
        'icon_shape'     => 'none',
        'icon_bg_color'  => '',
        'divider'        => false,
        'divider_color'  => '',
        'layout'         => 'vertical',
        // Text effects defaults (l'unica opzione di target è 'text')
        'text_effect'           => 'none',
        'text_effect_target'    => 'text',
        'text_effect_speed'     => '50',
        'text_effect_delay'     => '0',
        'text_effect_loop'      => false,
        'text_effect_cursor'    => true,
        'text_effect_cursor_char' => '|',
        'text_effect_color'     => '',
        'text_effect_color_to'  => '',
        'text_effect_phrases'   => '',
        'text_effect_pause'     => '1500',
            'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() { return []; }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-il-' . wp_rand( 10000, 99999 );
        $items = is_array($s['items']) ? $s['items'] : [];
        if (empty($items)) {
            $items = [
                ['icon' => 'check', 'text' => 'Prima voce', 'color' => ''],
                ['icon' => 'check', 'text' => 'Seconda voce', 'color' => ''],
                ['icon' => 'check', 'text' => 'Terza voce', 'color' => ''],
            ];
        }

        $icon_clr   = $this->safe_color_css($s['icon_color']) ?: 'var(--olo-color-success, #10B981)';
        $text_clr   = $this->safe_color_css($s['text_color']) ?: 'var(--olo-color-text, #374151)';
        $icon_size  = absint($s['icon_size']) ?: 20;
        $text_size  = absint($s['text_size']) ?: 16;
        $gap        = absint($s['gap']) ?: 12;
        $shape      = $s['icon_shape'] ?? 'none';
        $bg_clr     = $this->safe_color_css($s['icon_bg_color']) ?: 'color-mix(in srgb, var(--olo-color-success, #10B981) 15%, transparent)';
        $is_horiz   = $s['layout'] === 'horizontal';
        $divider    = !empty($s['divider']);
        $div_clr    = $this->safe_color_css($s['divider_color']) ?: 'var(--olo-color-border, #E5E7EB)';
        $radius     = $shape === 'circle' ? '50%' : ($shape === 'rounded' ? '8px' : '4px');

        $list_style = 'display:flex;gap:' . $gap . 'px;';
        if ($is_horiz) {
            $list_style .= 'flex-wrap:wrap;flex-direction:row;';
        } else {
            $list_style .= 'flex-direction:column;';
        }

        ob_start();
        ?>
        <style>
            .<?php echo esc_attr( $uid ); ?> a:focus-visible { outline: none; border-radius: 4px; box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent); }
        </style>
        <div class="olo-iconlist <?php echo esc_attr( $uid ); ?> olo-il-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" style="padding:16px;<?php echo esc_attr( $list_style ); ?>">
            <?php foreach ($items as $item) :
                $item_icon = $item['icon'] ?? 'check';
                $item_text = $item['text'] ?? '';
                $item_clr  = $this->safe_color_css($item['color'] ?? '') ?: $icon_clr;
                $has_link  = ! empty( $item['link'] );
                $icon_style = "color:{$item_clr};flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;";
                if ($shape !== 'none') {
                    $w = $icon_size + 16;
                    $icon_style .= "background:{$bg_clr};width:{$w}px;height:{$w}px;border-radius:{$radius};";
                }
                $item_style = 'display:flex;align-items:center;gap:10px;';
                if ($divider && !$is_horiz) {
                    $item_style .= "padding-bottom:{$gap}px;border-bottom:1px solid {$div_clr};";
                }
                if ($has_link) {
                    $item_style .= 'text-decoration:none;color:inherit;';
                }
                $ratio = $icon_size > 0 ? round($icon_size / 20, 2) : 1;
                $tag_open  = $has_link ? '<a href="' . esc_url( $item['link'] ) . '" style="' . $item_style . '">' : '<div style="' . $item_style . '">';
                $tag_close = $has_link ? '</a>' : '</div>';
                ?>
                <?php echo $tag_open; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- anchor/div opener built above with esc_url() and a style string assembled from absint() sizes and safe_color_css() colours ?>
                    <span style="<?php echo $icon_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- style assembled above from safe_color_css() colours, absint() sizes and fixed-literal radius ?>">
                        <?php if (preg_match('/^[a-z][a-z0-9-]*$/', $item_icon)) : ?>
                            <span uk-icon="icon: <?php echo esc_attr($item_icon); ?>; ratio: <?php echo (float) $ratio; ?>"></span>
                        <?php else : ?>
                            <?php echo esc_html($item_icon); ?>
                        <?php endif; ?>
                    </span>
                    <?php
                    list( $il_cls, $il_data ) = $this->tfx_attrs( $s, 'text', wp_strip_all_tags( $item_text ) );
                    $il_ta = $s['text_align'] ?? '';
                    $il_ta_css = in_array( $il_ta, [ 'left', 'center', 'right', 'justify' ], true ) ? 'text-align:' . $il_ta . ';flex:1;' : '';
                    ?>
                    <span class="olo-il-text<?php echo $il_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); colour via safe_color_css(); size absint()'d; align built from an in_array() whitelist ?>" style="color:<?php echo $text_clr; ?>;font-size:<?php echo (int) $text_size; ?>px;line-height:1.4;<?php echo $il_ta_css; ?>"<?php echo $il_data; ?>>
                        <?php echo wp_kses_post($item_text); ?>
                    </span>
                <?php echo $tag_close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed '</a>'/'</div>' literal from the ternary above ?>
            <?php endforeach; ?>
        </div>
        <?php
        $tfx_css = $this->tfx_css( $s, '.olo-iconlist' );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Text_Effects::css() from whitelisted effects, sanitized colors and integer timings
        $this->tfx_print_script();
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }
}
