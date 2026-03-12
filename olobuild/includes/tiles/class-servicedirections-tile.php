<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_ServiceDirections_Tile extends Olo_Tile_Base {

    protected $type     = 'servicedirections';
    protected $name     = 'Come Arrivare';
    protected $icon     = 'dashicons-car';
    protected $category = 'booking';
    protected $defaults = [
        'meta_prefix'    => '_olo_service_',
        'show_icon'      => true,
        'show_title'     => true,
        'title_text'     => 'Come arrivare',
        // Stile
        'title_size'     => 18,
        'text_size'      => 14,
        'title_color'    => '',
        'text_color'     => '#374151',
        'icon_color'     => '#6366F1',
        // Container
        'bg_color'       => '',
        'border_color'   => '',
        'border_radius'  => 8,
        'padding'        => 16,
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
        $directions = get_post_meta( $pid, $pfx . 'directions', true );

        if ( empty( $directions ) ) {
            return '';
        }

        $title_size = max( 14, min( 28, absint( $s['title_size'] ) ) );
        $text_size  = max( 12, min( 20, absint( $s['text_size'] ) ) );
        $radius     = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $padding    = absint( $s['padding'] );

        $wrap_styles = [];
        if ( $s['bg_color'] )     $wrap_styles[] = 'background:' . $this->safe_color_css( $s['bg_color'] );
        if ( $s['border_color'] ) $wrap_styles[] = 'border:1px solid ' . $this->safe_color_css( $s['border_color'] );
        if ( $radius && $radius !== '0px' ) $wrap_styles[] = 'border-radius:' . $radius;
        if ( $padding )           $wrap_styles[] = 'padding:' . $padding . 'px';

        $icon_svg = '<svg width="' . ( $title_size + 2 ) . '" height="' . ( $title_size + 2 ) . '" viewBox="0 0 24 24" fill="none">'
            . '<path d="M12 2L4.5 20.3l.7.7L12 18l6.8 3 .7-.7L12 2z" stroke="' . $this->safe_color_css( $s['icon_color'] ) . '" stroke-width="1.8" stroke-linejoin="round" fill="' . $this->safe_color_css( $s['icon_color'] ) . '" fill-opacity="0.12"/>'
            . '</svg>';

        // Convert newlines to <br> if no HTML tags present
        $content = $directions;
        if ( strip_tags( $content ) === $content ) {
            $content = nl2br( esc_html( $content ) );
        } else {
            $content = wp_kses_post( $content );
        }

        ob_start();
        ?>
        <div style="<?php echo implode( ';', $wrap_styles ); ?>">
            <?php if ( ! empty( $s['show_title'] ) ) : ?>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px">
                <?php if ( ! empty( $s['show_icon'] ) ) : ?>
                    <span style="display:flex"><?php echo $icon_svg; ?></span>
                <?php endif; ?>
                <h3 style="margin:0;font-size:<?php echo $title_size; ?>px;font-weight:700;color:<?php echo $this->safe_color_css( $s['title_color'] ); ?>"><?php echo esc_html( $s['title_text'] ); ?></h3>
            </div>
            <?php endif; ?>
            <div style="font-size:<?php echo $text_size; ?>px;color:<?php echo $this->safe_color_css( $s['text_color'] ); ?>;line-height:1.6"><?php echo $content; ?></div>
        </div>
        <?php
        return ob_get_clean();
    }
}
