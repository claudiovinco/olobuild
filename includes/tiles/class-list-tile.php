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
        'icon_color'   => '',
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
            'sm' => '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
            'md' => '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
            'lg' => '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
            'xl' => '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
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
        // Semantically ordered list: when every visible item uses the numbered marker,
        // expose the order programmatically with <ol> instead of <ul>.
        $is_ordered = ! empty( $items );
        foreach ( $items as $item ) {
            $item_icon = ! empty( $item['icon'] ) ? $item['icon'] : $default_icon;
            if ( $item_icon !== 'number' ) {
                $is_ordered = false;
                break;
            }
        }
        $list_el = $is_ordered ? 'ol' : 'ul';
        ?>
        <<?php echo $list_el; ?> class="olo-list <?php echo esc_attr( $uid ); ?> uk-list olo-ls-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" style="padding: <?php echo $pad; ?>;<?php echo $shadow_css; ?><?php if ( $list_text_clr ) echo 'color:' . $list_text_clr . ';'; ?>"><?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $list_el is a fixed 'ol'/'ul' literal, $pad is integer px values from Olo_Tile_Utils::spacing_css(), $shadow_css is built from a fixed map or intval()'d offsets with esc_attr()'d color, $list_text_clr passed the safe_color_css() whitelist; kept on same line to avoid whitespace ?>
            <?php foreach ( $items as $i => $item ) :
                $icon = ! empty( $item['icon'] ) ? $item['icon'] : $default_icon;
            ?>
                <?php
                    $has_link = ! empty( $item['link'] );
                    // Always keep a real <li> container; when the item links, the <a>
                    // lives INSIDE the <li> so the list markup stays valid for AT.
                    $li_margin = $i > 0 ? 'margin-top:' . $spacing . 'px;' : '';
                    $inner_open = $has_link
                        ? '<a href="' . esc_url( $item['link'] ) . '" style="display:flex;align-items:flex-start;gap:' . $igap . 'px;text-decoration:none;color:inherit;">'
                        : '';
                    $inner_close = $has_link ? '</a>' : '';
                    // Item-level CSS: linked items put flex/gap on the inner <a>, so the
                    // <li> only carries the vertical rhythm; non-linked items keep flex on <li>.
                    $li_style = $has_link
                        ? $li_margin
                        : 'display:flex;align-items:flex-start;gap:' . $igap . 'px;' . $li_margin;
                ?>
                <li style="<?php echo $li_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- assembled from absint()'d gap/spacing and fixed CSS literals ?>"><?php echo $inner_open; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- opening <a> assembled above from fixed markup, esc_url()'d link and absint()'d gap ?>
                    <?php if ( $icon === 'number' ) : ?>
                        <?php if ( ! $is_ordered ) : ?>
                        <span aria-hidden="true" style="flex-shrink:0;font-weight:700;line-height:normal;font-size:<?php echo (int) $isize; ?>px;min-width:<?php echo (int) $isize; ?>px;text-align:center;color:<?php echo esc_attr( $this->safe_color_css( $s['icon_color'] ) ?: 'var(--olo-color-success, #15803d)' ); ?>;"><?php echo (int) ( $i + 1 ); ?>.</span>
                        <?php endif; ?>
                    <?php else : ?>
                        <span aria-hidden="true" style="flex-shrink:0;display:flex;align-items:center;line-height:1;"><?php echo $this->get_icon_svg( $icon, $s['icon_color'], $isize ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup from the hardcoded get_icon_svg() map with safe_color_css()'d color and intval()'d size ?></span>
                        <?php if ( $icon === 'x' ) : ?>
                        <span class="olo-list-sr-only" style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;"><?php echo esc_html( olo_t( 'Non incluso' ) ); ?></span>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php list( $li_cls, $li_data ) = $this->tfx_attrs( $s, 'text', $item['text'] ); ?>
                    <span class="olo-list-text<?php echo $li_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally (sanitize_html_class/esc_attr); $list_ta_css from an in_array() whitelist ?>" style="line-height:1.5;<?php echo $list_ta_css; ?>"<?php echo $li_data; ?>><?php echo esc_html( $item['text'] ); ?></span>
                <?php echo $inner_close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed '</a>' literal from the ternary above ?></li>
            <?php endforeach; ?>
        </<?php echo $list_el; ?>>
        <?php
        $tfx_css = $this->tfx_css( $s, '.olo-list' );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Text_Effects::css() from whitelisted effects, sanitized colors and integer timings
        $this->tfx_print_script();
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
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
        $c = $this->safe_color_css( $color ) ?: 'var(--olo-color-success, #15803d)';
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
