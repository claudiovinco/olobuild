<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Olo_Animatedheading_Tile extends Olo_Tile_Base {
    protected $type     = 'animatedheading';
    protected $name     = 'Titolo animato';
    protected $icon     = 'dashicons-editor-textcolor';
    protected $category = 'text';
    protected $defaults = [
        'before_text'     => 'Noi siamo',
        'animated_words'  => "creativi\ninnovativi\nappassionati",
        'after_text'      => '',
        'animation'       => 'typing',
        'tag'             => 'h2',
        'alignment'       => 'center',
        'text_color'      => '',
        'animated_color'  => '',
        'font_size'       => '36',
        'font_weight'     => '700',
        'typing_speed'    => '100',
        'pause_time'      => '2000',
        'highlight_style' => 'underline',
        'highlight_color' => '',
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
        $uid = 'olo-ah-' . wp_rand(10000, 99999);

        $aw_raw = is_array( $s['animated_words'] ) ? implode( "\n", $s['animated_words'] ) : (string) $s['animated_words'];
        $words = array_filter(array_map('trim', explode("\n", $aw_raw)));
        if (empty($words)) $words = ['animato'];

        $tag   = in_array($s['tag'], ['h1','h2','h3','h4','h5','h6','p'], true) ? $s['tag'] : 'h2';
        $align = in_array($s['alignment'], ['left','center','right'], true) ? $s['alignment'] : 'center';
        $clr   = $this->safe_color_css($s['text_color']) ?: 'var(--olo-color-text, #374151)';
        $aclr  = $this->safe_color_css($s['animated_color']) ?: 'var(--olo-color-primary, #e1474f)';
        $fs    = absint($s['font_size']) ?: 36;
        $fw    = absint($s['font_weight']) ?: 700;
        $anim  = $s['animation'];
        $speed = absint($s['typing_speed']) ?: 100;
        $pause = absint($s['pause_time']) ?: 2000;

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist for every colour, absint() for sizes, in_array() whitelists for enums, fixed literals and the internally generated $uid. ?>
        <style>
        .<?php echo $uid; ?> { text-align: <?php echo $align; ?>; padding: 24px 16px; }
        .<?php echo $uid; ?> .olo-ah-heading { color: <?php echo $clr; ?>; font-size: <?php echo $fs; ?>px; font-weight: <?php echo $fw; ?>; margin: 0; line-height: 1.2; }
        .<?php echo $uid; ?> .olo-ah-word { color: <?php echo $aclr; ?>; }
        <?php if ($anim === 'typing') : ?>
        .<?php echo $uid; ?> .olo-ah-cursor { animation: olo-blink 0.7s infinite; }
        @keyframes olo-blink { 0%,50%{opacity:1}51%,100%{opacity:0} }
        <?php elseif ($anim === 'rotating') : ?>
        .<?php echo $uid; ?> .olo-ah-word { display: inline-block; overflow: hidden; vertical-align: bottom; }
        <?php elseif ($anim === 'fade') : ?>
        .<?php echo $uid; ?> .olo-ah-word { transition: opacity 0.5s ease; }
        <?php elseif ($anim === 'highlight') : ?>
        <?php
            $hclr = $this->safe_color_css($s['highlight_color']) ?: 'var(--olo-color-primary, #e1474f)';
            $hstyle = $s['highlight_style'];
            if ($hstyle === 'underline') {
                echo ".{$uid} .olo-ah-word { border-bottom: 3px solid {$hclr}; }";
            } elseif ($hstyle === 'background') {
                echo ".{$uid} .olo-ah-word { background: {$hclr}30; padding: 0 8px; border-radius: 4px; }";
            } elseif ($hstyle === 'strikethrough') {
                echo ".{$uid} .olo-ah-word { text-decoration: line-through {$hclr}; }";
            }
        ?>
        <?php endif; ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-animatedheading <?php echo esc_attr( $uid ); ?>">
            <<?php echo tag_escape( $tag ); ?> class="olo-ah-heading">
                <?php if (!empty($s['before_text'])) echo esc_html($s['before_text']) . ' '; ?>
                <span class="olo-ah-word" data-words="<?php echo esc_attr(wp_json_encode($words)); ?>"><?php echo esc_html($words[0]); ?></span>
                <?php if ($anim === 'typing') echo '<span class="olo-ah-cursor">|</span>'; ?>
                <?php if (!empty($s['after_text'])) echo ' ' . esc_html($s['after_text']); ?>
            </<?php echo tag_escape( $tag ); ?>>
        </div>
        <script>
        (function(){
            var el = document.querySelector('.<?php echo esc_js( $uid ); ?> .olo-ah-word');
            if(!el) return;
            var words = JSON.parse(el.getAttribute('data-words'));
            if(!words.length) return;
            var idx = 0;
            var anim = '<?php echo esc_js($anim); ?>';
            var speed = <?php echo (int) $speed; ?>;
            var pauseTime = <?php echo (int) $pause; ?>;

            if(anim === 'typing'){
                var currentText = words[0];
                var isDeleting = false;
                var charIdx = currentText.length;
                function typeStep(){
                    if(isDeleting){
                        charIdx--;
                        el.textContent = currentText.substring(0, charIdx);
                        if(charIdx === 0){
                            isDeleting = false;
                            idx = (idx + 1) % words.length;
                            currentText = words[idx];
                            setTimeout(typeStep, speed);
                            return;
                        }
                        setTimeout(typeStep, speed / 2);
                    } else {
                        charIdx++;
                        el.textContent = currentText.substring(0, charIdx);
                        if(charIdx === currentText.length){
                            isDeleting = true;
                            setTimeout(typeStep, pauseTime);
                            return;
                        }
                        setTimeout(typeStep, speed);
                    }
                }
                setTimeout(function(){ isDeleting = true; typeStep(); }, pauseTime);
            } else if(anim === 'fade'){
                el.style.transition = 'opacity 0.5s ease';
                setInterval(function(){
                    el.style.opacity = '0';
                    setTimeout(function(){
                        idx = (idx + 1) % words.length;
                        el.textContent = words[idx];
                        el.style.opacity = '1';
                    }, 500);
                }, pauseTime);
            } else if(anim === 'rotating'){
                var h = el.offsetHeight || parseInt(getComputedStyle(el).fontSize);
                el.style.display = 'inline-block';
                el.style.overflow = 'hidden';
                el.style.verticalAlign = 'bottom';
                el.style.height = h + 'px';
                el.style.lineHeight = h + 'px';
                el.style.transition = 'none';
                var inner = document.createElement('span');
                inner.style.display = 'block';
                inner.style.transition = 'transform 0.5s cubic-bezier(.4,0,.2,1)';
                inner.textContent = words[0];
                el.textContent = '';
                el.appendChild(inner);
                setInterval(function(){
                    inner.style.transform = 'translateY(-100%)';
                    inner.style.opacity = '0';
                    setTimeout(function(){
                        idx = (idx + 1) % words.length;
                        inner.style.transition = 'none';
                        inner.style.transform = 'translateY(100%)';
                        inner.textContent = words[idx];
                        setTimeout(function(){
                            inner.style.transition = 'transform 0.5s cubic-bezier(.4,0,.2,1), opacity 0.5s ease';
                            inner.style.transform = 'translateY(0)';
                            inner.style.opacity = '1';
                        }, 30);
                    }, 500);
                }, pauseTime);
            } else if(anim === 'slide'){
                el.style.display = 'inline-block';
                el.style.overflow = 'hidden';
                el.style.verticalAlign = 'bottom';
                el.style.transition = 'none';
                var inner2 = document.createElement('span');
                inner2.style.display = 'inline-block';
                inner2.style.transition = 'transform 0.5s ease, opacity 0.5s ease';
                inner2.textContent = words[0];
                el.textContent = '';
                el.appendChild(inner2);
                setInterval(function(){
                    inner2.style.transform = 'translateX(-110%)';
                    inner2.style.opacity = '0';
                    setTimeout(function(){
                        idx = (idx + 1) % words.length;
                        inner2.style.transition = 'none';
                        inner2.style.transform = 'translateX(110%)';
                        inner2.textContent = words[idx];
                        setTimeout(function(){
                            inner2.style.transition = 'transform 0.5s ease, opacity 0.5s ease';
                            inner2.style.transform = 'translateX(0)';
                            inner2.style.opacity = '1';
                        }, 30);
                    }, 500);
                }, pauseTime);
            } else if(anim === 'clip'){
                el.style.display = 'inline-block';
                el.style.overflow = 'hidden';
                el.style.borderRight = '2px solid currentColor';
                el.style.whiteSpace = 'nowrap';
                el.style.transition = 'width 0.8s cubic-bezier(.4,0,.2,1)';
                el.style.width = 'auto';
                var fullW = el.offsetWidth;
                el.style.width = fullW + 'px';
                setInterval(function(){
                    el.style.width = '0px';
                    setTimeout(function(){
                        idx = (idx + 1) % words.length;
                        el.textContent = words[idx];
                        el.style.transition = 'none';
                        el.style.width = '0px';
                        var nw = el.scrollWidth;
                        setTimeout(function(){
                            el.style.transition = 'width 0.8s cubic-bezier(.4,0,.2,1)';
                            el.style.width = nw + 'px';
                        }, 30);
                    }, 800);
                }, pauseTime);
            } else if(anim === 'highlight'){
                el.style.transition = 'opacity 0.4s ease';
                setInterval(function(){
                    el.style.opacity = '0';
                    setTimeout(function(){
                        idx = (idx + 1) % words.length;
                        el.textContent = words[idx];
                        el.style.opacity = '1';
                    }, 400);
                }, pauseTime);
            }
        })();
        </script>
        <?php
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
}
