<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Textmask_Tile extends Olobuild_Tile_Base {

    protected $type     = 'textmask';
    protected $name     = 'Text Mask Video';
    protected $icon     = 'dashicons-editor-textcolor';
    protected $category = 'creative';
    protected $defaults = [
        'text'               => "WELCOME\nTO THE WORLD",
        'multiline'          => true,
        'font_size'          => '120',
        'font_size_tablet'   => '80',
        'font_size_mobile'   => '50',
        'font_weight'        => '900',
        'font_family'        => '',
        'text_transform'     => 'uppercase',
        'letter_spacing'     => '5',
        'line_height'        => '1',
        'text_align'         => 'center',
        'video_url'          => '',
        'video_poster'       => '',
        'video_opacity'      => '100',
        'min_height'         => '100vh',
        'padding_y'          => '0',
        'padding_x'          => '0',
        'vertical_align'     => 'center',
        'bg_color'           => '#000000',
        'mask_mode'          => 'text_reveals_video',
        'blend_mode'         => 'normal',
        'text_fill'          => '#ffffff',
        'scroll_animate'      => true,
        'scroll_start'        => '0',
        'scroll_end'          => '100',
        'scroll_scale'        => true,
        'scroll_scale_from'   => '100',
        'scroll_scale_to'     => '300',
        'scroll_opacity'      => true,
        'scroll_opacity_from' => '100',
        'scroll_opacity_to'   => '0',
        'scroll_blur'         => false,
        'scroll_blur_from'    => '0',
        'scroll_blur_to'      => '10',
        'overlay_color'      => '',
        'overlay_opacity'    => '0',
            'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() { return []; }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $uid = 'olo-tm-' . wp_unique_id();

        // Values
        $multiline  = ! empty( $s['multiline'] );
        $raw_text   = $s['text'] ?: 'WELCOME';
        $text       = $multiline ? nl2br( esc_html( $raw_text ) ) : esc_html( $raw_text );
        $fs         = intval( $s['font_size'] ) ?: 120;
        $fs_tablet  = intval( $s['font_size_tablet'] ) ?: 80;
        $fs_mobile  = intval( $s['font_size_mobile'] ) ?: 50;
        $fw         = esc_attr( $s['font_weight'] ) ?: '900';
        $ff         = $s['font_family'] ? esc_attr( $s['font_family'] ) : 'inherit';
        $tt         = esc_attr( $s['text_transform'] ) ?: 'uppercase';
        $ls         = intval( $s['letter_spacing'] );
        $lh         = floatval( $s['line_height'] ) ?: 1;
        $ta         = esc_attr( $s['text_align'] ) ?: 'center';
        $min_h      = esc_attr( $s['min_height'] ) ?: '100vh';
        $_tp = $s['tile_padding'] ?? null;
        if ( is_array( $_tp ) ) {
            $py = intval( $_tp['top'] ?? 0 );
            $px = intval( $_tp['right'] ?? 0 );
        } else {
            $py = intval( $s['padding_y'] ?? 0 );
            $px = intval( $s['padding_x'] ?? 0 );
        }
        $bg_color   = $this->safe_color_css( $s['bg_color'] ) ?: '#000000';
        $va         = $s['vertical_align'] ?: 'center';
        $mode       = $s['mask_mode'] ?: 'text_reveals_video';
        $blend      = esc_attr( $s['blend_mode'] ) ?: 'normal';
        $text_fill  = $this->safe_color_css( $s['text_fill'] ) ?: '#ffffff';
        $vid_url    = esc_url( $s['video_url'] );
        $vid_poster = $s['video_poster'] ? esc_url( $s['video_poster'] ) : '';
        $vid_opacity = intval( $s['video_opacity'] ) ?: 100;
        $scroll_on   = ! empty( $s['scroll_animate'] );
        $scroll_start = intval( $s['scroll_start'] );
        $scroll_end   = intval( $s['scroll_end'] ) ?: 100;
        $do_scale    = ! empty( $s['scroll_scale'] );
        $sc_from     = intval( $s['scroll_scale_from'] ) ?: 100;
        $sc_to       = intval( $s['scroll_scale_to'] ) ?: 300;
        $do_opacity  = ! empty( $s['scroll_opacity'] );
        $op_from     = intval( $s['scroll_opacity_from'] );
        $op_to       = intval( $s['scroll_opacity_to'] );
        $do_blur     = ! empty( $s['scroll_blur'] );
        $bl_from     = intval( $s['scroll_blur_from'] );
        $bl_to       = intval( $s['scroll_blur_to'] );
        $ov_color   = $this->safe_color_css( $s['overlay_color'] );
        $ov_opacity = intval( $s['overlay_opacity'] );

        // Vertical align mapping
        $va_map = [ 'top' => 'flex-start', 'center' => 'center', 'bottom' => 'flex-end' ];
        $va_css = $va_map[ $va ] ?? 'center';

        // Text CSS shared
        $ws = $multiline ? 'normal' : 'nowrap';
        $text_css = "font-size:{$fs}px;font-weight:{$fw};font-family:{$ff};text-transform:{$tt};letter-spacing:{$ls}px;line-height:{$lh};text-align:{$ta};width:100%;white-space:{$ws};will-change:transform,opacity;transform-origin:center center";

        // Base container CSS
        $css  = "#{$uid}{position:relative;overflow:hidden;min-height:{$min_h};background:{$bg_color}}";
        $css .= "#{$uid} .olo-tm-vid{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:" . ( $vid_opacity / 100 ) . "}";

        // Responsive
        $css .= "@media(max-width:960px){#{$uid} .olo-tm-text{font-size:{$fs_tablet}px !important}}";
        $css .= "@media(max-width:640px){#{$uid} .olo-tm-text{font-size:{$fs_mobile}px !important;white-space:normal !important;word-break:break-word}}";

        // Mode-specific CSS
        if ( $mode === 'text_reveals_video' ) {
            // Detect if bg is dark or light to pick correct blend modes
            $hex = ltrim( $bg_color, '#' );
            if ( strlen( $hex ) === 3 ) {
                $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
            }
            $r = hexdec( substr( $hex, 0, 2 ) );
            $g = hexdec( substr( $hex, 2, 2 ) );
            $b = hexdec( substr( $hex, 4, 2 ) );
            $lum = 0.299 * $r + 0.587 * $g + 0.114 * $b;
            // Dark bg: mask=multiply, text=screen(white)
            // Light bg: mask=screen, text=multiply(black)
            $is_dark     = $lum < 128;
            $mask_blend  = $is_dark ? 'multiply' : 'screen';
            $text_color  = $is_dark ? '#fff' : '#000';
            $text_blend  = $is_dark ? 'screen' : 'multiply';

            $css .= "#{$uid} .olo-tm-mask{position:absolute;inset:0;z-index:2;background:{$bg_color};display:flex;align-items:{$va_css};justify-content:center;padding:{$py}px {$px}px;isolation:isolate;mix-blend-mode:{$mask_blend}}";
            $css .= "#{$uid} .olo-tm-text{color:{$text_color};mix-blend-mode:{$text_blend};{$text_css};position:relative}";
        } elseif ( $mode === 'video_behind_text' ) {
            // background-clip: text approach — video plays, text clips it
            $css .= "#{$uid} .olo-tm-mask{position:absolute;inset:0;z-index:2;display:flex;align-items:{$va_css};justify-content:center;padding:{$py}px {$px}px}";
            $css .= "#{$uid} .olo-tm-text{color:transparent;-webkit-background-clip:text;background-clip:text;{$text_css};position:relative}";
        } else {
            // Simple blend mode — blend on mask layer so it composites with sibling video
            $css .= "#{$uid} .olo-tm-mask{position:absolute;inset:0;display:flex;align-items:{$va_css};justify-content:center;padding:{$py}px {$px}px;mix-blend-mode:{$blend}}";
            $css .= "#{$uid} .olo-tm-text{color:{$text_fill};{$text_css};position:relative}";
        }

        // Optional overlay (below the mask)
        if ( $ov_color && $ov_opacity > 0 ) {
            $ov_op = $ov_opacity / 100;
            $css .= "#{$uid} .olo-tm-overlay{position:absolute;inset:0;z-index:1;background:{$ov_color};opacity:{$ov_op}}";
        }

        ob_start();
        echo '<style>' . $css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- scoped CSS built above from intval()/floatval() numerics, esc_attr()'d typography values, safe_color_css() whitelisted colors, fixed literal maps and the internally generated $uid
        ?>
        <div id="<?php echo esc_attr( $uid ); ?>">
            <?php if ( $vid_url ) : ?>
            <video class="olo-tm-vid"<?php echo $vid_poster ? ' poster="' . $vid_poster . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attribute built with esc_url()'d poster above, or empty string ?> muted autoplay loop playsinline><?php
                $ext = pathinfo( wp_parse_url( $vid_url, PHP_URL_PATH ) ?: '', PATHINFO_EXTENSION );
                $mime = $ext === 'webm' ? 'video/webm' : 'video/mp4';
                ?><source src="<?php echo $vid_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_url()'d above ?>" type="<?php echo esc_attr( $mime ); ?>"></video>
            <?php endif; ?>

            <?php if ( $ov_color && $ov_opacity > 0 ) : ?>
            <div class="olo-tm-overlay"></div>
            <?php endif; ?>

            <?php list( $tm_cls, $tm_data ) = $this->tfx_attrs( $s, 'text', wp_strip_all_tags( $s['text'] ?? '' ) ); ?>
            <div class="olo-tm-mask">
                <div class="olo-tm-text<?php echo $tm_cls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tfx_attrs() fragments are escaped internally; text esc_html()'d above (nl2br only adds <br /> tags) ?>"<?php echo $tm_data; ?>><?php echo $text; ?></div>
            </div>
        </div>
        <?php

        // Scroll animation script
        if ( $scroll_on ) :
        ?>
        <script>
        (function(){
            var el = document.getElementById('<?php echo esc_js( $uid ); ?>');
            if(!el) return;
            var mask = el.querySelector('.olo-tm-mask');
            var txt = el.querySelector('.olo-tm-text');
            if(!txt) return;

            var rangeStart = <?php echo (int) $scroll_start; ?> / 100;
            var rangeEnd   = <?php echo (int) $scroll_end; ?> / 100;
            if(rangeEnd <= rangeStart){ rangeEnd = rangeStart + 0.01; }

            var doScale   = <?php echo $do_scale ? 'true' : 'false'; ?>;
            var sFrom     = <?php echo (int) $sc_from; ?> / 100;
            var sTo       = <?php echo (int) $sc_to; ?> / 100;

            var doOpacity = <?php echo $do_opacity ? 'true' : 'false'; ?>;
            var oFrom     = <?php echo (int) $op_from; ?> / 100;
            var oTo       = <?php echo (int) $op_to; ?> / 100;

            var doBlur    = <?php echo $do_blur ? 'true' : 'false'; ?>;
            var bFrom     = <?php echo (int) $bl_from; ?>;
            var bTo       = <?php echo (int) $bl_to; ?>;

            function lerp(a,b,t){ return a + (b - a) * t; }
            function clamp(v){ if(v < 0) return 0; if(v > 1) return 1; return v; }

            function update(){
                var rect = el.getBoundingClientRect();
                var vh = window.innerHeight;
                var elH = rect.height;
                /* raw progress: 0 = bottom edge enters viewport, 1 = top edge exits viewport */
                var scrolled = vh - rect.top;
                var total = vh + elH;
                var raw = clamp(scrolled / total);
                /* map raw into the user-defined range */
                var progress = clamp((raw - rangeStart) / (rangeEnd - rangeStart));

                var tf = '';
                if(doScale){
                    tf = 'scale(' + lerp(sFrom, sTo, progress) + ')';
                }
                if(tf){ txt.style.transform = tf; }

                if(doOpacity){
                    var op = lerp(oFrom, oTo, progress);
                    if(mask){ mask.style.opacity = op; }
                }

                if(doBlur){
                    var bl = lerp(bFrom, bTo, progress);
                    txt.style.filter = 'blur(' + bl + 'px)';
                }
            }

            txt.style.willChange = 'transform, opacity, filter';
            txt.style.transformOrigin = 'center center';
            var ticking = false;
            window.addEventListener('scroll', function(){
                if(!ticking){ requestAnimationFrame(function(){ update(); ticking = false; }); ticking = true; }
            }, {passive:true});
            update();
        })();
        </script>
        <?php
        endif;

        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Text_Effects::css() from whitelisted effects, sanitized colors and integer timings
        $this->tfx_print_script();
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }
}
