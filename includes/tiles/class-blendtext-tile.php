<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Blendtext_Tile extends Olo_Tile_Base {

    protected $type     = 'blendtext';
    protected $name     = 'Blend Text';
    protected $icon     = 'dashicons-editor-textcolor';
    protected $category = 'creative';
    protected $defaults = [
        'text'            => 'BLEND',
        'tag'             => 'div',
        'font_size'       => '120',
        'font_size_tablet'=> '80',
        'font_size_mobile'=> '50',
        'font_weight'     => '900',
        'font_family'     => '',
        'text_transform'  => 'uppercase',
        'letter_spacing'  => '5',
        'line_height'     => '1',
        'text_align'      => 'center',
        'text_color'      => '#ffffff',
        'blend_mode'      => 'difference',
        'padding_top'     => '40',
        'padding_bottom'  => '40',
        'padding_left'    => '20',
        'padding_right'   => '20',
    ];

    public function get_controls() { return []; }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $uid = 'olo-bt-' . wp_unique_id();

        $raw_text   = esc_html( wp_strip_all_tags( $s['text'] ) ) ?: 'BLEND';
        $text       = preg_replace( '/<\/?p[^>]*>/', '', $raw_text );
        $tag        = in_array( $s['tag'], [ 'h1','h2','h3','h4','h5','h6','p','div','span' ], true ) ? $s['tag'] : 'div';
        $fs         = intval( $s['font_size'] ) ?: 120;
        $fs_tablet  = intval( $s['font_size_tablet'] ) ?: 80;
        $fs_mobile  = intval( $s['font_size_mobile'] ) ?: 50;
        $fw         = esc_attr( $s['font_weight'] ) ?: '900';
        $ff         = $s['font_family'] ? esc_attr( $s['font_family'] ) : 'inherit';
        $tt         = esc_attr( $s['text_transform'] ) ?: 'uppercase';
        $ls         = intval( $s['letter_spacing'] );
        $lh         = floatval( $s['line_height'] ) ?: 1;
        $ta         = esc_attr( $s['text_align'] ) ?: 'center';
        $color      = $s['text_color'] ?: '#ffffff';
        $blend      = esc_attr( $s['blend_mode'] ) ?: 'difference';
        // Padding: nuovo formato spacing oggetto o legacy singoli
        $pad_obj    = $s['bt_padding'] ?? null;
        if ( is_array( $pad_obj ) ) {
            $pt = intval( $pad_obj['top'] ?? 0 );
            $pr = intval( $pad_obj['right'] ?? 0 );
            $pb = intval( $pad_obj['bottom'] ?? 0 );
            $pl = intval( $pad_obj['left'] ?? 0 );
        } else {
            $pt = intval( $s['padding_top'] ?? 40 );
            $pb = intval( $s['padding_bottom'] ?? 40 );
            $pl = intval( $s['padding_left'] ?? 20 );
            $pr = intval( $s['padding_right'] ?? 20 );
        }

        // NO z-index, NO position:relative on wrapper — so blend composites with parent stacking context
        $css  = "#{$uid}{mix-blend-mode:{$blend};padding:{$pt}px {$pr}px {$pb}px {$pl}px}";
        $css .= "#{$uid} .olo-bt-text{font-size:{$fs}px;font-weight:{$fw};font-family:{$ff};text-transform:{$tt};letter-spacing:{$ls}px;line-height:{$lh};text-align:{$ta};color:{$color};margin:0}";
        $css .= "@media(max-width:960px){#{$uid} .olo-bt-text{font-size:{$fs_tablet}px !important}}";
        $css .= "@media(max-width:640px){#{$uid} .olo-bt-text{font-size:{$fs_mobile}px !important}}";

        ob_start();
        echo '<style>' . $css . '</style>';
        ?>
        <div id="<?php echo esc_attr( $uid ); ?>">
            <<?php echo $tag; ?> class="olo-bt-text"><?php echo nl2br( $text ); ?></<?php echo $tag; ?>>
        </div>
        <script>
        (function(){
            var el = document.getElementById('<?php echo esc_js( $uid ); ?>');
            if(!el) return;
            var p = el.parentElement;
            while(p){
                if(p.tagName === 'SECTION') break;
                var st = p.style;
                if(st.zIndex){ st.zIndex = ''; }
                var cs = getComputedStyle(p);
                if(cs.isolation === 'isolate'){ st.isolation = 'auto'; }
                p = p.parentElement;
            }
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
