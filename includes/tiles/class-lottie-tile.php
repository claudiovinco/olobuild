<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Olo_Lottie_Tile extends Olo_Tile_Base {
    protected $type     = 'lottie';
    protected $name     = 'Lottie Animation';
    protected $icon     = 'dashicons-format-video';
    protected $category = 'media';
    protected $defaults = [
        'json_url'     => '',
        'width'        => '300',
        'height'       => '300',
        'loop'         => true,
        'autoplay'     => true,
        'speed'        => '1',
        'trigger'      => 'autoplay',
        'hover_action' => 'none',
        'alignment'    => 'center',
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
        $uid = 'olo-lottie-' . wp_rand(10000, 99999);

        $url    = esc_url($s['json_url']);
        $width  = absint($s['width']) ?: 300;
        $height = absint($s['height']) ?: 300;
        $loop   = !empty($s['loop']) ? 'true' : 'false';
        $speed  = floatval($s['speed']) ?: 1;
        $trigger = $s['trigger'];
        $hover   = $s['hover_action'] ?? 'none';
        $align   = in_array($s['alignment'], ['left','center','right'], true) ? $s['alignment'] : 'center';
        $justify = $align === 'left' ? 'flex-start' : ($align === 'right' ? 'flex-end' : 'center');

        if (empty($url)) return '<div class="olo-lottie" style="text-align:center;padding:20px;color:var(--olo-color-text-muted, #9CA3AF);">Nessun file Lottie configurato</div>';

        wp_enqueue_script( 'lottie-web', OLO_URL . 'assets/vendor/lottie/lottie.min.js', [], '5.12.2', true );

        ob_start();
        ?>
        <div class="olo-lottie" style="display:flex;justify-content:<?php echo $justify; ?>;padding:16px;">
            <div id="<?php echo $uid; ?>" style="width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;"></div>
        </div>
        <script>
        (function(){
            function initLottie(){
                if(typeof lottie === 'undefined'){ setTimeout(initLottie, 100); return; }
                var container = document.getElementById('<?php echo $uid; ?>');
                if(!container) return;
                var autoplay = <?php echo ($trigger === 'autoplay') ? 'true' : 'false'; ?>;
                var anim = lottie.loadAnimation({
                    container: container,
                    renderer: 'svg',
                    loop: <?php echo $loop; ?>,
                    autoplay: autoplay,
                    path: '<?php echo $url; ?>'
                });
                <?php if ($speed != 1) : ?>
                anim.setSpeed(<?php echo $speed; ?>);
                <?php endif; ?>
                <?php if ($trigger === 'viewport') : ?>
                var obs = new IntersectionObserver(function(entries){
                    entries.forEach(function(e){
                        if(e.isIntersecting){ anim.play(); obs.unobserve(e.target); }
                    });
                }, {threshold: 0.3});
                obs.observe(container);
                <?php elseif ($trigger === 'hover') : ?>
                container.addEventListener('mouseenter', function(){ anim.play(); });
                container.addEventListener('mouseleave', function(){ anim.stop(); });
                <?php elseif ($trigger === 'click') : ?>
                container.style.cursor = 'pointer';
                var playing = false;
                container.addEventListener('click', function(){
                    if(playing){ anim.pause(); playing = false; }
                    else { anim.play(); playing = true; }
                });
                <?php endif; ?>
                <?php if ($hover === 'pause') : ?>
                container.addEventListener('mouseenter', function(){ anim.pause(); });
                container.addEventListener('mouseleave', function(){ anim.play(); });
                <?php elseif ($hover === 'reverse') : ?>
                container.addEventListener('mouseenter', function(){ anim.setDirection(-1); });
                container.addEventListener('mouseleave', function(){ anim.setDirection(1); });
                <?php elseif ($hover === 'speed-up') : ?>
                container.addEventListener('mouseenter', function(){ anim.setSpeed(<?php echo $speed * 2; ?>); });
                container.addEventListener('mouseleave', function(){ anim.setSpeed(<?php echo $speed; ?>); });
                <?php endif; ?>
            }
            initLottie();
        })();
        </script>
        <?php
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}";
            echo $border_hover_css . $border_effect_css . '</style>';
        }
        return ob_get_clean();
    }
}
