<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * BottomBar — barra fissa in fondo alla viewport (credits, note, contatti
 * sempre visibili). Fixed solo sul frontend; nel canvas del builder il
 * wrapper resta in flusso per non coprire l'editor.
 */
class Olobuild_Bottombar_Tile extends Olobuild_Tile_Base {

    protected $type     = 'bottombar';
    protected $name     = 'Barra fissa in basso';
    protected $icon     = 'dashicons-minus';
    protected $category = 'content';
    protected $defaults = [
        'content_html'   => '',
        'align'          => 'center',
        'hide_mobile'    => false,
        'bg_color'       => '',
        'text_color'     => '',
        'link_color'     => '',
        'font_size'      => 11,
        'letter_spacing' => 2,
        'uppercase'      => true,
        'font_preset'    => '',
        'padding_y'      => 14,
        'border_top'     => false,
        'border_color'   => '',
        'z_index'        => 92,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'content_html',   'type' => 'textarea', 'label' => 'Contenuto (HTML)' ],
            [ 'key' => 'align',          'type' => 'select',   'label' => 'Allineamento', 'options' => [ 'left' => 'Sinistra', 'center' => 'Centro', 'right' => 'Destra' ] ],
            [ 'key' => 'hide_mobile',    'type' => 'toggle',   'label' => 'Nascondi su mobile' ],
            [ 'key' => 'bg_color',       'type' => 'color',    'label' => 'Sfondo' ],
            [ 'key' => 'text_color',     'type' => 'color',    'label' => 'Colore testo' ],
            [ 'key' => 'link_color',     'type' => 'color',    'label' => 'Colore link' ],
            [ 'key' => 'font_size',      'type' => 'range',    'label' => 'Dimensione testo (px)', 'min' => 9, 'max' => 16, 'step' => 1 ],
            [ 'key' => 'letter_spacing', 'type' => 'range',    'label' => 'Spaziatura lettere (px)', 'min' => 0, 'max' => 6, 'step' => 0.5 ],
            [ 'key' => 'uppercase',      'type' => 'toggle',   'label' => 'Maiuscolo' ],
            [ 'key' => 'padding_y',      'type' => 'range',    'label' => 'Padding verticale (px)', 'min' => 6, 'max' => 28, 'step' => 1 ],
            [ 'key' => 'border_top',     'type' => 'toggle',   'label' => 'Bordo superiore' ],
            [ 'key' => 'border_color',   'type' => 'color',    'label' => 'Colore bordo' ],
            [ 'key' => 'z_index',        'type' => 'range',    'label' => 'Z-index', 'min' => 10, 'max' => 9999, 'step' => 1 ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $uid     = 'olo-bottombar-' . wp_unique_id();
        $align   = in_array( $s['align'], [ 'left', 'center', 'right' ], true ) ? $s['align'] : 'center';
        $bg      = $this->safe_color_css( $s['bg_color'] ) ?: 'var(--olo-color-surface, rgba(12,14,19,.92))';
        $text    = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text-muted, #8B90A0)';
        $link    = $this->safe_color_css( $s['link_color'] ) ?: 'var(--olo-color-heading, #FAF7F2)';
        $fsize   = max( 9, min( 16, absint( $s['font_size'] ) ) );
        $lspace  = max( 0, min( 6, floatval( $s['letter_spacing'] ) ) );
        $upper   = ! empty( $s['uppercase'] );
        $pad     = max( 6, min( 28, absint( $s['padding_y'] ) ) );
        $zidx    = absint( $s['z_index'] ) ?: 92;
        $border  = '';
        if ( ! empty( $s['border_top'] ) ) {
            $bcol   = $this->safe_color_css( $s['border_color'] ) ?: 'var(--olo-color-border, rgba(250,247,242,.14))';
            $border = 'border-top:1px solid ' . $bcol . ';';
        }
        $font = '';
        if ( ! empty( $s['font_preset'] ) ) {
            $font = 'font-family:var(--olo-font-' . sanitize_html_class( $s['font_preset'] ) . '-family, var(--olo-font-family-mono, monospace));';
        } else {
            $font = 'font-family:var(--olo-font-family-mono, monospace);';
        }

        ob_start();
        ?>
        <div id="<?php echo esc_attr( $uid ); ?>" class="olo-bottombar<?php echo ! empty( $s['hide_mobile'] ) ? ' olo-bb-hide-mobile' : ''; ?>"
             style="position:fixed;bottom:0;left:0;right:0;z-index:<?php echo (int) $zidx; ?>;<?php echo esc_attr( $border ); ?>background:<?php echo esc_attr( $bg ); ?>;color:<?php echo esc_attr( $text ); ?>;
                    text-align:<?php echo esc_attr( $align ); ?>;padding:<?php echo (int) $pad; ?>px 24px;<?php echo esc_attr( $font ); ?>
                    font-size:<?php echo (int) $fsize; ?>px;letter-spacing:<?php echo esc_attr( $lspace ); ?>px;<?php echo $upper ? 'text-transform:uppercase;' : ''; ?>line-height:1.4;">
            <?php echo wp_kses_post( $s['content_html'] ); ?>
        </div>
        <style>
        #<?php echo esc_attr( $uid ); ?> a{color:<?php echo esc_attr( $link ); ?>;text-decoration:none;font-weight:600;}
        #<?php echo esc_attr( $uid ); ?> a:hover{text-decoration:underline;}
        #<?php echo esc_attr( $uid ); ?> a:focus-visible{outline:2px solid currentColor;outline-offset:2px;}
        @media(max-width:640px){ .olo-bb-hide-mobile{display:none;} }
        </style>
        <script>
        (function(){
            /* I wrapper del template (transform/container) creano un containing
               block che intrappola i position:fixed: la barra si sposta in
               document.body cosi' resta davvero ancorata alla viewport. */
            var el=document.getElementById('<?php echo esc_js( $uid ); ?>');
            if(el&&el.parentNode!==document.body){document.body.appendChild(el);}
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
