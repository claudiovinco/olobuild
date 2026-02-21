<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_ServiceDescription_Tile extends Olo_Tile_Base {

    protected $type     = 'servicedescription';
    protected $name     = 'Descrizione Struttura';
    protected $icon     = 'dashicons-text-page';
    protected $category = 'booking';
    protected $defaults = [
        'show_title'     => true,
        'title_text'     => 'La struttura',
        'title_size'     => 20,
        'text_size'      => 15,
        'title_color'    => '#1F2937',
        'text_color'     => '#374151',
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

        $description = $post->post_content;
        if ( empty( $description ) ) {
            return '';
        }

        $text_size  = max( 12, min( 22, absint( $s['text_size'] ) ) );
        $title_size = max( 14, min( 32, absint( $s['title_size'] ) ) );
        $radius     = absint( $s['border_radius'] );
        $padding    = absint( $s['padding'] );
        $line_h     = floatval( $s['line_height'] ) ?: 1.7;
        $align      = in_array( $s['text_align'], [ 'left', 'center', 'right', 'justify' ] ) ? $s['text_align'] : 'left';

        $wrap = [];
        if ( $s['bg_color'] )     $wrap[] = 'background:' . esc_attr( $s['bg_color'] );
        if ( $s['border_color'] ) $wrap[] = 'border:1px solid ' . esc_attr( $s['border_color'] );
        if ( $radius )            $wrap[] = 'border-radius:' . $radius . 'px';
        if ( $padding )           $wrap[] = 'padding:' . $padding . 'px';
        if ( absint( $s['max_width'] ) ) $wrap[] = 'max-width:' . absint( $s['max_width'] ) . 'px';
        $wrap[] = 'text-align:' . $align;

        // Support HTML content (from WP editor) or plain text
        if ( strip_tags( $description ) === $description ) {
            $content = nl2br( esc_html( $description ) );
        } else {
            $content = wp_kses_post( wpautop( $description ) );
        }

        $uid = 'olo-sdesc-' . wp_rand( 10000, 99999 );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> p { margin:0 0 1em; }
            .<?php echo $uid; ?> p:last-child { margin-bottom:0; }
        </style>
        <div class="<?php echo esc_attr( $uid ); ?>" style="<?php echo implode( ';', $wrap ); ?>">
            <?php if ( ! empty( $s['show_title'] ) && ! empty( $s['title_text'] ) ) : ?>
            <h3 style="margin:0 0 12px;font-size:<?php echo $title_size; ?>px;font-weight:700;color:<?php echo esc_attr( $s['title_color'] ); ?>"><?php echo esc_html( $s['title_text'] ); ?></h3>
            <?php endif; ?>
            <div style="font-size:<?php echo $text_size; ?>px;color:<?php echo esc_attr( $s['text_color'] ); ?>;line-height:<?php echo $line_h; ?>"><?php echo $content; ?></div>
        </div>
        <?php
        return ob_get_clean();
    }
}
