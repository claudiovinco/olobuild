<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Olo_Iconlist_Tile extends Olo_Tile_Base {
    protected $type     = 'iconlist';
    protected $name     = 'Lista icone';
    protected $icon     = 'dashicons-list-view';
    protected $category = 'text';
    protected $defaults = [
        'items'          => [],
        'icon_color'     => '#22C55E',
        'icon_size'      => '20',
        'text_color'     => '#E5E7EB',
        'text_size'      => '16',
        'gap'            => '12',
        'icon_shape'     => 'none',
        'icon_bg_color'  => '',
        'divider'        => false,
        'divider_color'  => '#374151',
        'layout'         => 'vertical',
    ];

    public function get_controls() { return []; }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $items = is_array($s['items']) ? $s['items'] : [];
        if (empty($items)) {
            $items = [
                ['icon' => 'check', 'text' => 'Prima voce', 'color' => ''],
                ['icon' => 'check', 'text' => 'Seconda voce', 'color' => ''],
                ['icon' => 'check', 'text' => 'Terza voce', 'color' => ''],
            ];
        }

        $icon_clr   = $this->safe_color_css($s['icon_color']) ?: 'var(--olo-color-success, #10B981)';
        $text_clr   = $this->safe_color_css($s['text_color']) ?: 'var(--olo-color-border, #E5E7EB)';
        $icon_size  = absint($s['icon_size']) ?: 20;
        $text_size  = absint($s['text_size']) ?: 16;
        $gap        = absint($s['gap']) ?: 12;
        $shape      = $s['icon_shape'] ?? 'none';
        $bg_clr     = $this->safe_color_css($s['icon_bg_color']) ?: 'rgba(34,197,94,0.15)';
        $is_horiz   = $s['layout'] === 'horizontal';
        $divider    = !empty($s['divider']);
        $div_clr    = $this->safe_color_css($s['divider_color']) ?: 'var(--olo-color-text, #374151)';
        $radius     = $shape === 'circle' ? '50%' : ($shape === 'rounded' ? '8px' : '4px');

        $list_style = 'display:flex;gap:' . $gap . 'px;';
        if ($is_horiz) {
            $list_style .= 'flex-wrap:wrap;flex-direction:row;';
        } else {
            $list_style .= 'flex-direction:column;';
        }

        ob_start();
        ?>
        <div class="olo-iconlist" style="padding:16px;<?php echo $list_style; ?>">
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
                <?php echo $tag_open; ?>
                    <span style="<?php echo $icon_style; ?>">
                        <?php if (preg_match('/^[a-z][a-z0-9-]*$/', $item_icon)) : ?>
                            <span uk-icon="icon: <?php echo esc_attr($item_icon); ?>; ratio: <?php echo $ratio; ?>"></span>
                        <?php else : ?>
                            <?php echo esc_html($item_icon); ?>
                        <?php endif; ?>
                    </span>
                    <span style="color:<?php echo $text_clr; ?>;font-size:<?php echo $text_size; ?>px;line-height:1.4;">
                        <?php echo wp_kses_post($item_text); ?>
                    </span>
                <?php echo $tag_close; ?>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
