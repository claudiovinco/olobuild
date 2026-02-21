<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_ServiceExcerpt_Tile extends Olo_Tile_Base {

    protected $type     = 'serviceexcerpt';
    protected $name     = 'Descrizione Breve';
    protected $icon     = 'dashicons-editor-paragraph';
    protected $category = 'booking';
    protected $defaults = [
        'show_title'     => false,
        'title_text'     => '',
        'title_size'     => 16,
        'text_size'      => 15,
        'title_color'    => '#1F2937',
        'text_color'     => '#6B7280',
        'font_style'     => 'italic',
        'line_height'    => '1.7',
        'max_width'      => 0,
        'text_align'     => 'left',
        'bg_color'       => '',
        'border_color'   => '',
        'border_radius'  => 0,
        'padding'        => 0,
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        global $post;
        if ( ! $post || ! is_singular() ) {
            return '<div style="padding:24px;text-align:center;color:#9ca3af;background:#f9fafb;border-radius:8px">'
                 . '<p style="margin:0">Inserisci in un template single.</p></div>';
        }

        $excerpt = $post->post_excerpt;
        if ( empty( $excerpt ) ) {
            return '';
        }

        $text_size  = max( 12, min( 22, absint( $s['text_size'] ) ) );
        $title_size = max( 14, min( 28, absint( $s['title_size'] ) ) );
        $radius     = absint( $s['border_radius'] );
        $padding    = absint( $s['padding'] );
        $line_h     = floatval( $s['line_height'] ) ?: 1.7;
        $align      = in_array( $s['text_align'], [ 'left', 'center', 'right', 'justify' ] ) ? $s['text_align'] : 'left';
        $font_style = $s['font_style'] === 'italic' ? 'italic' : 'normal';

        $wrap = [];
        if ( $s['bg_color'] )     $wrap[] = 'background:' . esc_attr( $s['bg_color'] );
        if ( $s['border_color'] ) $wrap[] = 'border:1px solid ' . esc_attr( $s['border_color'] );
        if ( $radius )            $wrap[] = 'border-radius:' . $radius . 'px';
        if ( $padding )           $wrap[] = 'padding:' . $padding . 'px';
        if ( absint( $s['max_width'] ) ) $wrap[] = 'max-width:' . absint( $s['max_width'] ) . 'px';
        $wrap[] = 'text-align:' . $align;

        $content = nl2br( esc_html( $excerpt ) );

        ob_start();
        ?>
        <div style="<?php echo implode( ';', $wrap ); ?>">
            <?php if ( ! empty( $s['show_title'] ) && ! empty( $s['title_text'] ) ) : ?>
            <h3 style="margin:0 0 8px;font-size:<?php echo $title_size; ?>px;font-weight:700;color:<?php echo esc_attr( $s['title_color'] ); ?>"><?php echo esc_html( $s['title_text'] ); ?></h3>
            <?php endif; ?>
            <p style="margin:0;font-size:<?php echo $text_size; ?>px;color:<?php echo esc_attr( $s['text_color'] ); ?>;line-height:<?php echo $line_h; ?>;font-style:<?php echo $font_style; ?>"><?php echo $content; ?></p>
        </div>
        <?php
        return ob_get_clean();
    }
}
