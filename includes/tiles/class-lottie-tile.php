<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Olobuild_Lottie_Tile extends Olobuild_Tile_Base {
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

        // Accessibilità: nome accessibile opzionale per l'animazione (campo additivo, non altera le chiavi salvate esistenti)
        $alt_label = isset($s['alt']) ? trim( (string) $s['alt'] ) : '';
        if ( $alt_label === '' && isset($s['aria_label']) ) {
            $alt_label = trim( (string) $s['aria_label'] );
        }
        $is_clickable = ( $trigger === 'click' );

        // Attributi di semantica per il container: role=img+aria-label se informativo, aria-hidden se decorativo
        $a11y_attrs = '';
        if ( $alt_label !== '' ) {
            $a11y_attrs .= ' role="img" aria-label="' . esc_attr( $alt_label ) . '"';
        } elseif ( ! $is_clickable ) {
            $a11y_attrs .= ' aria-hidden="true"';
        }
        // trigger=click: il container è operabile da puntatore → renderlo operabile anche da tastiera
        if ( $is_clickable ) {
            $a11y_attrs .= ' tabindex="0" role="button"';
            if ( $alt_label !== '' ) {
                $a11y_attrs .= ' aria-pressed="false"';
            } else {
                $a11y_attrs .= ' aria-label="' . esc_attr( olobuild_t( 'Riproduci/Pausa animazione', 'olobuilder' ) ) . '" aria-pressed="false"';
            }
        }

        if (empty($url)) return '<div class="olo-lottie" style="text-align:center;padding:20px;color:var(--olo-color-text-muted, #9CA3AF);">Nessun file Lottie configurato</div>';

        wp_enqueue_script( 'lottie-web', OLOBUILD_URL . 'assets/vendor/lottie/lottie.min.js', [], '5.12.2', true );

        ob_start();
        ?>
        <div class="olo-lottie" style="display:flex;justify-content:<?php echo esc_attr( $justify ); ?>;padding:16px;">
            <div id="<?php echo esc_attr( $uid ); ?>"<?php echo $a11y_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributi ARIA costruiti sopra con esc_attr() sui valori dinamici ?> style="width:<?php echo (int) $width; ?>px;height:<?php echo (int) $height; ?>px;"></div>
        </div>
        <script>
        (function(){
            function initLottie(){
                if(typeof lottie === 'undefined'){ setTimeout(initLottie, 100); return; }
                var container = document.getElementById('<?php echo esc_js( $uid ); ?>');
                if(!container) return;
                var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                var autoplay = <?php echo ($trigger === 'autoplay') ? 'true' : 'false'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed-literal ternary ('true'/'false') ?>;
                // 2.2.2 / 2.3.3: rispetta prefers-reduced-motion, non avviare l'autoplay
                if (reduceMotion) { autoplay = false; }
                var anim = lottie.loadAnimation({
                    container: container,
                    renderer: 'svg',
                    loop: <?php echo $loop; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed-literal ternary ('true'/'false') assigned above ?>,
                    autoplay: autoplay,
                    path: '<?php echo $url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $url passed through esc_url() above ?>'
                });
                <?php if ($speed != 1) : ?>
                anim.setSpeed(<?php echo (float) $speed; ?>);
                <?php endif; ?>
                <?php if ($trigger === 'viewport') : ?>
                if (!reduceMotion) {
                    var obs = new IntersectionObserver(function(entries){
                        entries.forEach(function(e){
                            if(e.isIntersecting){ anim.play(); obs.unobserve(e.target); }
                        });
                    }, {threshold: 0.3});
                    obs.observe(container);
                }
                <?php elseif ($trigger === 'hover') : ?>
                container.addEventListener('mouseenter', function(){ anim.play(); });
                container.addEventListener('mouseleave', function(){ anim.stop(); });
                <?php elseif ($trigger === 'click') : ?>
                container.style.cursor = 'pointer';
                var playing = false;
                function toggleLottie(){
                    if(playing){ anim.pause(); playing = false; }
                    else { anim.play(); playing = true; }
                    container.setAttribute('aria-pressed', playing ? 'true' : 'false');
                }
                container.addEventListener('click', toggleLottie);
                // 2.1.1: operabilità da tastiera (Enter/Spazio) per il controllo play/pausa
                container.addEventListener('keydown', function(ev){
                    if(ev.key === 'Enter' || ev.key === ' ' || ev.key === 'Spacebar'){
                        ev.preventDefault();
                        toggleLottie();
                    }
                });
                <?php endif; ?>
                <?php if ($hover === 'pause') : ?>
                container.addEventListener('mouseenter', function(){ anim.pause(); });
                container.addEventListener('mouseleave', function(){ anim.play(); });
                <?php elseif ($hover === 'reverse') : ?>
                container.addEventListener('mouseenter', function(){ anim.setDirection(-1); });
                container.addEventListener('mouseleave', function(){ anim.setDirection(1); });
                <?php elseif ($hover === 'speed-up') : ?>
                container.addEventListener('mouseenter', function(){ anim.setSpeed(<?php echo (float) ( $speed * 2 ); ?>); });
                container.addEventListener('mouseleave', function(){ anim.setSpeed(<?php echo (float) $speed; ?>); });
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
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }
}
