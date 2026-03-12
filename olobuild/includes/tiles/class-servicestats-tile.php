<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_ServiceStats_Tile extends Olo_Tile_Base {

    protected $type     = 'servicestats';
    protected $name     = 'Stats Servizio';
    protected $icon     = 'dashicons-performance';
    protected $category = 'booking';
    protected $defaults = [
        'meta_prefix'        => '_olo_service_',
        'layout'             => 'grid',
        'columns'            => 4,
        'gap'                => 16,
        'align'              => 'center',
        'icon_size'          => 24,
        'number_size'        => 22,
        'label_size'         => 11,
        'icon_color'         => '#6366F1',
        'number_color'       => '',
        'label_color'        => '#9CA3AF',
        'show_dividers'      => false,
        'divider_color'      => '#E5E7EB',
        'item_bg'            => '',
        'item_border_radius' => 8,
        'item_padding'       => 0,
        'item_border_color'  => '',
        'bg_color'           => '',
        'border_radius'      => 0,
        'padding'            => 0,
    ];

    private function svg_icon( $name, $size, $color ) {
        $s = absint( $size );
        $c = $this->safe_color_css( $color ) ?: 'currentColor';
        $icons = [
            'bed' => '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 18V12C3 10.3431 4.34315 9 6 9H18C19.6569 9 21 10.3431 21 12V18" stroke="' . $c . '" stroke-width="1.8" stroke-linecap="round"/><path d="M3 18V20M21 18V20" stroke="' . $c . '" stroke-width="1.8" stroke-linecap="round"/><path d="M7 9V7C7 5.89543 7.89543 5 9 5H15C16.1046 5 17 5.89543 17 7V9" stroke="' . $c . '" stroke-width="1.8" stroke-linecap="round"/><circle cx="8.5" cy="7.5" r="1.5" fill="' . $c . '" opacity="0.5"/></svg>',

            'bath' => '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 12H20V15C20 17.2091 18.2091 19 16 19H8C5.79086 19 4 17.2091 4 15V12Z" stroke="' . $c . '" stroke-width="1.8"/><path d="M4 12V5C4 4.44772 4.44772 4 5 4H7C7.55228 4 8 4.44772 8 5V12" stroke="' . $c . '" stroke-width="1.8" stroke-linecap="round"/><path d="M7 19V21M17 19V21" stroke="' . $c . '" stroke-width="1.8" stroke-linecap="round"/></svg>',

            'guests' => '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="9" cy="7" r="3" stroke="' . $c . '" stroke-width="1.8"/><path d="M3 20C3 16.6863 5.68629 14 9 14C12.3137 14 15 16.6863 15 20" stroke="' . $c . '" stroke-width="1.8" stroke-linecap="round"/><circle cx="17" cy="8" r="2" stroke="' . $c . '" stroke-width="1.8" opacity="0.6"/><path d="M17 14C19.2091 14 21 15.7909 21 18" stroke="' . $c . '" stroke-width="1.8" stroke-linecap="round" opacity="0.6"/></svg>',

            'area' => '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="4" y="4" width="16" height="16" rx="2" stroke="' . $c . '" stroke-width="1.8"/><path d="M4 9H8M4 14H8" stroke="' . $c . '" stroke-width="1.2" stroke-linecap="round" opacity="0.35"/><path d="M9 4V8M14 4V8" stroke="' . $c . '" stroke-width="1.2" stroke-linecap="round" opacity="0.35"/><path d="M10 12L14 12M12 10V14" stroke="' . $c . '" stroke-width="1.5" stroke-linecap="round" opacity="0.5"/></svg>',
        ];
        return $icons[ $name ] ?? '';
    }

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        global $post;
        if ( ! $post || ! is_singular() ) {
            return '<div style="padding:24px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);background:var(--olo-color-muted, #F3F4F6);border-radius:8px">'
                 . '<p style="margin:0">Inserisci in un template single.</p></div>';
        }

        $pid = $post->ID;
        $pfx = rtrim( $s['meta_prefix'], '_' ) . '_';

        $bedrooms  = get_post_meta( $pid, $pfx . 'bedrooms', true );
        $beds      = get_post_meta( $pid, $pfx . 'beds', true );
        $bathrooms = get_post_meta( $pid, $pfx . 'bathrooms', true );
        $capacity  = get_post_meta( $pid, $pfx . 'capacity', true );
        $sqm       = get_post_meta( $pid, $pfx . 'sqm', true );

        $stats = [];
        if ( $bedrooms !== '' && $bedrooms !== false && $bedrooms ) $stats[] = [ 'bed',    $bedrooms,  'Camere' ];
        if ( $beds )                                                $stats[] = [ 'bed',    $beds,      'Posti letto' ];
        if ( $bathrooms )                                           $stats[] = [ 'bath',   $bathrooms, 'Bagni' ];
        if ( $capacity )                                            $stats[] = [ 'guests', $capacity,  'Ospiti' ];
        if ( $sqm )                                                 $stats[] = [ 'area',   $sqm . ' m²', 'Superficie' ];

        if ( empty( $stats ) ) {
            return '';
        }

        $uid   = 'olo-sstats-' . wp_rand( 10000, 99999 );
        $cols  = absint( $s['columns'] ) ?: 4;
        $gap   = absint( $s['gap'] );
        $layout = $s['layout'];
        $align  = $s['align'];

        // Outer wrapper
        $wrap_css = '';
        if ( $s['bg_color'] )      $wrap_css .= "background:{$this->safe_color_css($s['bg_color'])};";
        if ( $s['border_radius'] ) $wrap_css .= "border-radius:{$s['border_radius']}px;";
        if ( $s['padding'] )       $wrap_css .= "padding:{$s['padding']}px;";

        // Item style
        $item_css = '';
        if ( $s['item_bg'] )            $item_css .= "background:{$this->safe_color_css($s['item_bg'])};";
        if ( $s['item_border_radius'] )  $item_css .= "border-radius:{$s['item_border_radius']}px;";
        if ( $s['item_padding'] )        $item_css .= "padding:{$s['item_padding']}px;";
        if ( $s['item_border_color'] )   $item_css .= "border:1px solid {$this->safe_color_css($s['item_border_color'])};";

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?>-grid { display:grid; grid-template-columns:repeat(<?php echo $cols; ?>,1fr); gap:<?php echo $gap; ?>px; text-align:<?php echo esc_attr( $align ); ?>; <?php echo $wrap_css; ?> }
            .<?php echo $uid; ?>-inline { display:flex; flex-wrap:wrap; gap:<?php echo $gap; ?>px; align-items:center; justify-content:<?php echo $align === 'center' ? 'center' : ( $align === 'right' ? 'flex-end' : 'flex-start' ); ?>; <?php echo $wrap_css; ?> }
            .<?php echo $uid; ?>-item { <?php echo $item_css; ?> }
            .<?php echo $uid; ?>-inline .<?php echo $uid; ?>-item { display:flex; align-items:center; gap:8px; }
            <?php if ( ! empty( $s['show_dividers'] ) && $layout === 'grid' ) : ?>
            .<?php echo $uid; ?>-grid .<?php echo $uid; ?>-item + .<?php echo $uid; ?>-item { border-left:1px solid <?php echo $this->safe_color_css( $s['divider_color'] ); ?>; }
            <?php endif; ?>
            <?php if ( ! empty( $s['show_dividers'] ) && $layout === 'inline' ) : ?>
            .<?php echo $uid; ?>-inline .<?php echo $uid; ?>-item + .<?php echo $uid; ?>-item { padding-left:<?php echo $gap; ?>px; border-left:1px solid <?php echo $this->safe_color_css( $s['divider_color'] ); ?>; }
            <?php endif; ?>
        </style>
        <div class="<?php echo esc_attr( $uid . '-' . $layout ); ?>">
            <?php foreach ( $stats as $stat ) : ?>
            <div class="<?php echo esc_attr( $uid ); ?>-item">
                <?php if ( $layout === 'inline' ) : ?>
                    <span style="display:flex;align-items:center"><?php echo $this->svg_icon( $stat[0], $s['icon_size'], $s['icon_color'] ); ?></span>
                    <span style="font-size:<?php echo absint( $s['number_size'] ); ?>px;font-weight:700;color:<?php echo $this->safe_color_css( $s['number_color'] ); ?>"><?php echo esc_html( $stat[1] ); ?></span>
                    <span style="font-size:<?php echo absint( $s['label_size'] ); ?>px;text-transform:uppercase;color:<?php echo $this->safe_color_css( $s['label_color'] ); ?>;letter-spacing:0.5px;font-weight:600"><?php echo esc_html( $stat[2] ); ?></span>
                <?php else : ?>
                    <div style="margin-bottom:6px;display:flex;justify-content:<?php echo $align === 'center' ? 'center' : ( $align === 'right' ? 'flex-end' : 'flex-start' ); ?>"><?php echo $this->svg_icon( $stat[0], $s['icon_size'], $s['icon_color'] ); ?></div>
                    <div style="font-size:<?php echo absint( $s['number_size'] ); ?>px;font-weight:700;color:<?php echo $this->safe_color_css( $s['number_color'] ); ?>;line-height:1.2"><?php echo esc_html( $stat[1] ); ?></div>
                    <div style="font-size:<?php echo absint( $s['label_size'] ); ?>px;text-transform:uppercase;color:<?php echo $this->safe_color_css( $s['label_color'] ); ?>;letter-spacing:0.5px;font-weight:600;margin-top:2px"><?php echo esc_html( $stat[2] ); ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
