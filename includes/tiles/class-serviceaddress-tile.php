<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_ServiceAddress_Tile extends Olo_Tile_Base {

    protected $type     = 'serviceaddress';
    protected $name     = 'Indirizzo Servizio';
    protected $icon     = 'dashicons-location';
    protected $category = 'booking';
    protected $defaults = [
        'meta_prefix'    => '_olo_service_',
        'show_icon'      => true,
        'show_label'     => true,
        'label_text'     => 'Indirizzo',
        'show_locality'  => true,
        'show_map_link'  => true,
        'map_link_text'  => 'Apri in Google Maps',
        // Stile
        'font_size'      => 15,
        'label_color'    => '#6B7280',
        'text_color'     => '',
        'icon_color'     => '#EF4444',
        'link_color'     => '#2563EB',
        // Container
        'bg_color'       => '',
        'border_color'   => '',
        'border_radius'  => 8,
        'tile_padding' => ['top' => 16, 'right' => 16, 'bottom' => 16, 'left' => 16],
    ];

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

        $address  = get_post_meta( $pid, $pfx . 'address', true );
        $locality = get_post_meta( $pid, $pfx . 'locality', true );
        $lat      = get_post_meta( $pid, $pfx . 'latitude', true );
        $lng      = get_post_meta( $pid, $pfx . 'longitude', true );

        if ( empty( $address ) && empty( $locality ) ) {
            return '';
        }

        $font_size = max( 12, min( 22, absint( $s['font_size'] ) ) );
        $radius    = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $padding   = absint( $s['padding'] );

        $wrap_styles = [];
        if ( $s['bg_color'] )     $wrap_styles[] = 'background:' . $this->safe_color_css( $s['bg_color'] );
        if ( $s['border_color'] ) $wrap_styles[] = 'border:1px solid ' . $this->safe_color_css( $s['border_color'] );
        if ( $radius && $radius !== '0px' ) $wrap_styles[] = 'border-radius:' . $radius;
        if ( $padding )           $wrap_styles[] = 'padding:' . $padding . 'px';

        // Map link
        $map_url = '';
        if ( $lat && $lng ) {
            $map_url = 'https://www.google.com/maps?q=' . urlencode( $lat . ',' . $lng );
        } elseif ( $address ) {
            $map_url = 'https://www.google.com/maps?q=' . urlencode( $address );
        }

        $icon_svg = '<svg width="' . ( $font_size + 4 ) . '" height="' . ( $font_size + 4 ) . '" viewBox="0 0 24 24" fill="none">'
            . '<path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" stroke="' . $this->safe_color_css( $s['icon_color'] ) . '" stroke-width="1.8" fill="' . $this->safe_color_css( $s['icon_color'] ) . '" fill-opacity="0.15"/>'
            . '<circle cx="12" cy="9" r="2.5" stroke="' . $this->safe_color_css( $s['icon_color'] ) . '" stroke-width="1.8" fill="' . $this->safe_color_css( $s['icon_color'] ) . '" fill-opacity="0.3"/>'
            . '</svg>';

        ob_start();
        ?>
        <div style="display:flex;align-items:flex-start;gap:10px;<?php echo implode( ';', $wrap_styles ); ?>">
            <?php if ( ! empty( $s['show_icon'] ) ) : ?>
                <span style="display:flex;flex-shrink:0;margin-top:2px"><?php echo $icon_svg; ?></span>
            <?php endif; ?>
            <div style="display:flex;flex-direction:column;gap:3px">
                <?php if ( ! empty( $s['show_label'] ) ) : ?>
                    <span style="font-size:<?php echo $font_size - 3; ?>px;color:<?php echo $this->safe_color_css( $s['label_color'] ); ?>;font-weight:600;text-transform:uppercase;letter-spacing:0.5px"><?php echo esc_html( $s['label_text'] ); ?></span>
                <?php endif; ?>
                <?php if ( $address ) : ?>
                    <span style="font-size:<?php echo $font_size; ?>px;color:<?php echo $this->safe_color_css( $s['text_color'] ); ?>;line-height:1.4"><?php echo esc_html( $address ); ?></span>
                <?php endif; ?>
                <?php if ( ! empty( $s['show_locality'] ) && $locality ) : ?>
                    <span style="font-size:<?php echo $font_size - 1; ?>px;color:<?php echo $this->safe_color_css( $s['label_color'] ); ?>"><?php echo esc_html( $locality ); ?></span>
                <?php endif; ?>
                <?php if ( ! empty( $s['show_map_link'] ) && $map_url ) : ?>
                    <a href="<?php echo esc_url( $map_url ); ?>" target="_blank" rel="noopener" style="font-size:<?php echo $font_size - 2; ?>px;color:<?php echo $this->safe_color_css( $s['link_color'] ); ?>;text-decoration:none;display:inline-flex;align-items:center;gap:4px;margin-top:4px">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6M15 3h6v6M10 14L21 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <?php echo esc_html( $s['map_link_text'] ); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
