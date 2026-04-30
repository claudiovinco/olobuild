<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Search_Tile extends Olo_Tile_Base {

    protected $type     = 'search';
    protected $name     = 'Ricerca';
    protected $icon     = 'dashicons-search';
    protected $category = 'navigation';
    protected $defaults = [
        'placeholder'        => 'Cerca...',
        'style'              => 'default',
        'size'               => 'medium',
        'show_icon'          => true,
        'icon_position'      => 'left',
        'show_button'        => false,
        'button_text'        => 'Cerca',
        'button_style'       => 'filled',
        'full_width'         => true,
        'max_width'          => '',
        'alignment'          => 'center',
        'bg_color'           => '#FFFFFF',
        'text_color'         => '#374151',
        'placeholder_color'  => '#9CA3AF',
        'icon_color'         => '#6B7280',
        'border_color'       => '#E5E7EB',
        'border_width'       => '1',
        'border_radius'      => '8',
        'focus_border_color' => '#6366F1',
        'button_bg'          => '#6366F1',
        'button_color'       => '#FFFFFF',
        'button_radius'      => '8',
        'input_shadow'       => false,
        'focus_shadow'       => true,
        'animated_placeholder' => false,
        'placeholder_words'  => '',
    ];

    public function get_controls() { return []; }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-srch-' . wp_rand( 10000, 99999 );

        $style       = in_array( $s['style'], [ 'default', 'minimal', 'pill', 'underline', 'hero', 'floating' ], true ) ? $s['style'] : 'default';
        $size        = in_array( $s['size'], [ 'small', 'medium', 'large' ], true ) ? $s['size'] : 'medium';
        $placeholder = esc_attr( $s['placeholder'] ?: 'Cerca...' );
        $show_icon   = ! empty( $s['show_icon'] );
        $icon_pos    = $s['icon_position'] === 'right' ? 'right' : 'left';
        $show_btn    = ! empty( $s['show_button'] );
        $btn_text    = esc_html( $s['button_text'] ?: 'Cerca' );
        $btn_style   = in_array( $s['button_style'], [ 'filled', 'outline', 'icon-only' ], true ) ? $s['button_style'] : 'filled';
        $full_width  = ! empty( $s['full_width'] );
        $max_width   = absint( $s['max_width'] );
        $alignment   = in_array( $s['alignment'], [ 'left', 'center', 'right' ], true ) ? $s['alignment'] : 'center';

        $bg_c          = $this->safe_color_css( $s['bg_color'] ) ?: '#FFFFFF';
        $text_c        = $this->safe_color_css( $s['text_color'] ) ?: '#374151';
        $ph_c          = $this->safe_color_css( $s['placeholder_color'] ) ?: '#9CA3AF';
        $icon_c        = $this->safe_color_css( $s['icon_color'] ) ?: '#6B7280';
        $border_c      = $this->safe_color_css( $s['border_color'] ) ?: '#E5E7EB';
        $border_w      = absint( $s['border_width'] ) . 'px';
        $focus_c       = $this->safe_color_css( $s['focus_border_color'] ) ?: '#6366F1';
        $btn_bg        = $this->safe_color_css( $s['button_bg'] ) ?: '#6366F1';
        $btn_color     = $this->safe_color_css( $s['button_color'] ) ?: '#FFFFFF';
        $btn_radius    = $this->build_border_radius_css( $s["button_radius"] );
        $btn_radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['button_radius_hover'] ?? null );
        $input_shadow  = ! empty( $s['input_shadow'] );
        $focus_shadow  = ! empty( $s['focus_shadow'] );

        // Radius
        $radius = Olo_Tile_Utils::radius_int( $s['border_radius'] ) . 'px';
        if ( $style === 'pill' ) $radius = '50px';
        if ( $style === 'hero' ) $radius = '16px';

        // Size map
        $sizes = [
            'small'  => [ 'fs' => '13px', 'pad' => '8px 12px' ],
            'medium' => [ 'fs' => '15px', 'pad' => '12px 16px' ],
            'large'  => [ 'fs' => '18px', 'pad' => '16px 20px' ],
        ];
        $sz = $sizes[ $size ] ?? $sizes['medium'];
        if ( $style === 'hero' ) {
            $sz = [ 'fs' => '20px', 'pad' => '18px 20px' ];
        }

        // Animated placeholder
        $anim_ph = ! empty( $s['animated_placeholder'] );
        $ph_words = [];
        if ( $anim_ph ) {
            $raw = is_array( $s['placeholder_words'] ) ? implode( "\n", $s['placeholder_words'] ) : (string) $s['placeholder_words'];
            $ph_words = array_filter( array_map( 'trim', explode( "\n", $raw ) ) );
        }

        // Build form/wrapper styles
        $form_css = "display:flex;align-items:center;border-radius:{$radius};overflow:hidden;transition:all 0.2s ease;";
        if ( $style === 'underline' ) {
            $form_css = "display:flex;align-items:center;border-bottom:2px solid {$border_c};transition:all 0.2s ease;";
        } elseif ( $style === 'minimal' ) {
            $form_css .= "background:transparent;border:{$border_w} solid {$border_c};";
        } elseif ( $style === 'floating' ) {
            $form_css .= "background:{$bg_c};border:none;box-shadow:0 4px 20px rgba(0,0,0,0.08),0 1px 4px rgba(0,0,0,0.04);";
        } elseif ( $style === 'hero' ) {
            $form_css .= "background:{$bg_c};border:{$border_w} solid {$border_c};box-shadow:0 8px 32px rgba(0,0,0,0.1),0 2px 8px rgba(0,0,0,0.05);";
        } else {
            $form_css .= "background:{$bg_c};border:{$border_w} solid {$border_c};";
        }
        if ( $input_shadow && ! in_array( $style, [ 'floating', 'hero', 'underline' ], true ) ) {
            $form_css .= 'box-shadow:0 1px 3px rgba(0,0,0,0.06),0 1px 2px rgba(0,0,0,0.04);';
        }

        $wrapper_css = '';
        if ( ! $full_width && $max_width ) {
            $wrapper_css .= "max-width:{$max_width}px;";
        }
        if ( ! $full_width ) {
            $flex_map = [ 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' ];
            $wrapper_css = "display:flex;justify-content:" . ( $flex_map[ $alignment ] ?? 'center' ) . ";";
        }

        $icon_svg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="' . esc_attr( $icon_c ) . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>';

        ob_start();
        ?>
        <style>
        .<?php echo $uid; ?> input::placeholder { color: <?php echo $ph_c; ?>; }
        .<?php echo $uid; ?> input:focus { outline: none; }
        <?php if ( $focus_shadow ) : ?>
        .<?php echo $uid; ?> .olo-srch-form:focus-within {
            <?php if ( $style !== 'underline' ) : ?>
            border-color: <?php echo $focus_c; ?>;
            box-shadow: 0 0 0 3px <?php echo $focus_c; ?>20;
            <?php else : ?>
            border-bottom-color: <?php echo $focus_c; ?>;
            <?php endif; ?>
        }
        <?php endif; ?>
        <?php if ( $btn_radius_hover_css !== '' ) : ?>.<?php echo $uid; ?> button[type=submit]{transition:border-radius 400ms cubic-bezier(.4,0,.2,1) !important}.<?php echo $uid; ?> button[type=submit]:hover{border-radius:<?php echo $btn_radius_hover_css; ?> !important}<?php endif; ?>
        </style>
        <div class="olo-search <?php echo $uid; ?>"<?php if ( $wrapper_css ) echo ' style="' . esc_attr( $wrapper_css ) . '"'; ?>>
            <form class="olo-srch-form" style="<?php echo esc_attr( $form_css ); ?>" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search">
                <?php if ( $show_icon && $icon_pos === 'left' ) : ?>
                <span style="display:flex;align-items:center;flex-shrink:0;padding-left:<?php echo $style === 'hero' ? '20px' : '14px'; ?>"><?php echo $icon_svg; ?></span>
                <?php endif; ?>

                <input type="search" name="s" placeholder="<?php echo $placeholder; ?>" aria-label="<?php echo $placeholder; ?>" style="flex:1;min-width:0;background:transparent;border:none;outline:none;color:<?php echo $text_c; ?>;font-size:<?php echo $sz['fs']; ?>;padding:<?php echo $sz['pad']; ?>;width:100%;"<?php if ( $anim_ph && ! empty( $ph_words ) ) echo ' data-olo-anim-ph="' . esc_attr( wp_json_encode( array_values( $ph_words ) ) ) . '"'; ?> />

                <?php if ( $show_icon && $icon_pos === 'right' && ! $show_btn ) : ?>
                <span style="display:flex;align-items:center;flex-shrink:0;padding-right:<?php echo $style === 'hero' ? '20px' : '14px'; ?>"><?php echo $icon_svg; ?></span>
                <?php endif; ?>

                <?php if ( $show_btn ) :
                    $bp = $style === 'hero' ? '18px 32px' : ( $btn_style === 'icon-only' ? $sz['pad'] : explode( ' ', $sz['pad'] )[0] . ' 24px' );
                    $br = $style === 'pill' ? '50px' : $btn_radius;
                    $btn_css = "display:inline-flex;align-items:center;justify-content:center;gap:6px;cursor:pointer;font-weight:600;font-size:{$sz['fs']};padding:{$bp};border:none;border-radius:{$br};margin:4px;transition:all 0.2s;";
                    if ( $btn_style === 'outline' ) {
                        $btn_css .= "background:transparent;color:{$btn_bg};border:2px solid {$btn_bg};";
                    } else {
                        $btn_css .= "background:{$btn_bg};color:{$btn_color};";
                    }
                ?>
                <button type="submit" style="<?php echo esc_attr( $btn_css ); ?>">
                    <?php if ( $btn_style === 'icon-only' ) : ?>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <?php else : ?>
                    <?php echo $btn_text; ?>
                    <?php endif; ?>
                </button>
                <?php endif; ?>
            </form>
        </div>
        <?php if ( $anim_ph && ! empty( $ph_words ) ) : ?>
        <script>
        (function(){
            var inp = document.querySelector('.<?php echo $uid; ?> input[data-olo-anim-ph]');
            if(!inp) return;
            var words = JSON.parse(inp.getAttribute('data-olo-anim-ph'));
            if(!words.length) return;
            var i = 0;
            setInterval(function(){
                i = (i + 1) % words.length;
                inp.setAttribute('placeholder', words[i]);
            }, 2500);
        })();
        </script>
        <?php endif; ?>
        <?php
        return ob_get_clean();
    }
}
