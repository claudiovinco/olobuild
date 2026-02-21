<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_ImgCompare_Tile extends Olo_Tile_Base {

    protected $type     = 'imgcompare';
    protected $name     = 'Confronto Immagini';
    protected $icon     = 'dashicons-image-flip-horizontal';
    protected $category = 'media';
    protected $defaults = [
        'before_image'   => '',
        'after_image'    => '',
        'before_label'   => 'Prima',
        'after_label'    => 'Dopo',
        'show_labels'    => true,
        'start_position' => '50',
        'orientation'    => 'horizontal',
        'handle_color'   => '#FFFFFF',
        'handle_size'    => '40',
        'handle_border'  => '3',
        'line_width'     => '3',
        'height'            => '400',
        'border_radius'     => '8',
        'object_fit'        => 'cover',
        'card_border_width' => '0',
        'card_border_color' => '#374151',
        'card_shadow'       => 'none',
        'autoplay'          => false,
        'autoplay_delay'    => '3',
        'autoplay_speed'    => '2',
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-ic-' . wp_rand( 10000, 99999 );

        $before_url  = esc_url( $s['before_image'] );
        $after_url   = esc_url( $s['after_image'] );
        $orientation = in_array( $s['orientation'], [ 'horizontal', 'vertical' ] ) ? $s['orientation'] : 'horizontal';
        $is_vert     = $orientation === 'vertical';
        $start       = max( 0, min( 100, intval( $s['start_position'] ) ) );
        $height      = intval( $s['height'] ) ?: 400;
        $radius      = intval( $s['border_radius'] );
        $fit         = in_array( $s['object_fit'], [ 'cover', 'contain' ] ) ? $s['object_fit'] : 'cover';
        $handle_c    = $this->safe_color( $s['handle_color'] ) ?: '#FFFFFF';
        $handle_sz   = intval( $s['handle_size'] ) ?: 40;
        $handle_bw   = intval( $s['handle_border'] ) ?: 3;
        $line_w      = intval( $s['line_width'] ) ?: 3;
        $show_labels = ! empty( $s['show_labels'] );
        $before_lbl  = esc_html( $s['before_label'] );
        $after_lbl   = esc_html( $s['after_label'] );
        $cbw         = intval( $s['card_border_width'] );
        $cbc         = $this->safe_color( $s['card_border_color'] ) ?: '#374151';
        $shadow      = $this->get_shadow_css( $s['card_shadow'] );
        $autoplay    = ! empty( $s['autoplay'] );
        $ap_delay    = max( 1, intval( $s['autoplay_delay'] ) );
        $ap_speed    = max( 1, intval( $s['autoplay_speed'] ) );

        // clip-path per "before" image (la parte visibile a sinistra/sopra)
        // Orizzontale: inset(0 {100-pos}% 0 0)  —  taglia da destra
        // Verticale:   inset(0 0 {100-pos}% 0)  —  taglia dal basso
        $clip_prop = $is_vert ? 'bottom' : 'right';

        // Slider direction attributes
        $range_style = $is_vert
            ? "writing-mode: vertical-lr; direction: rtl; height: 100%; width: {$handle_sz}px;"
            : "width: 100%; height: {$handle_sz}px;";

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                position: relative;
                overflow: hidden;
                height: <?php echo $height; ?>px;
                <?php if ( $radius > 0 ) : ?>border-radius: <?php echo $radius; ?>px;<?php endif; ?>
                <?php if ( $cbw > 0 ) : ?>border: <?php echo $cbw; ?>px solid <?php echo $cbc; ?>;<?php endif; ?>
                <?php if ( $shadow ) : ?>box-shadow: <?php echo $shadow; ?>;<?php endif; ?>
                cursor: <?php echo $is_vert ? 'row-resize' : 'col-resize'; ?>;
                user-select: none;
                -webkit-user-select: none;
                touch-action: none;
            }

            .<?php echo $uid; ?> img {
                display: block;
                width: 100%;
                height: 100%;
                object-fit: <?php echo $fit; ?>;
                pointer-events: none;
                -webkit-user-drag: none;
            }

            .<?php echo $uid; ?> .olo-ic-after {
                position: absolute;
                inset: 0;
            }

            .<?php echo $uid; ?> .olo-ic-before {
                position: absolute;
                inset: 0;
                <?php if ( $is_vert ) : ?>
                clip-path: inset(0 0 <?php echo 100 - $start; ?>% 0);
                <?php else : ?>
                clip-path: inset(0 <?php echo 100 - $start; ?>% 0 0);
                <?php endif; ?>
            }

            /* Divider line */
            .<?php echo $uid; ?> .olo-ic-line {
                position: absolute;
                z-index: 2;
                background: <?php echo $handle_c; ?>;
                pointer-events: none;
                <?php if ( $is_vert ) : ?>
                left: 0;
                right: 0;
                top: <?php echo $start; ?>%;
                height: <?php echo $line_w; ?>px;
                transform: translateY(-50%);
                <?php else : ?>
                top: 0;
                bottom: 0;
                left: <?php echo $start; ?>%;
                width: <?php echo $line_w; ?>px;
                transform: translateX(-50%);
                <?php endif; ?>
            }

            /* Handle circle */
            .<?php echo $uid; ?> .olo-ic-handle {
                position: absolute;
                z-index: 3;
                width: <?php echo $handle_sz; ?>px;
                height: <?php echo $handle_sz; ?>px;
                border-radius: 50%;
                border: <?php echo $handle_bw; ?>px solid <?php echo $handle_c; ?>;
                background: rgba(0,0,0,0.3);
                backdrop-filter: blur(4px);
                -webkit-backdrop-filter: blur(4px);
                display: flex;
                align-items: center;
                justify-content: center;
                pointer-events: none;
                <?php if ( $is_vert ) : ?>
                left: 50%;
                top: <?php echo $start; ?>%;
                transform: translate(-50%, -50%);
                <?php else : ?>
                top: 50%;
                left: <?php echo $start; ?>%;
                transform: translate(-50%, -50%);
                <?php endif; ?>
            }

            .<?php echo $uid; ?> .olo-ic-handle svg {
                width: <?php echo round( $handle_sz * 0.45 ); ?>px;
                height: <?php echo round( $handle_sz * 0.45 ); ?>px;
                fill: none;
                stroke: <?php echo $handle_c; ?>;
                stroke-width: 2.5;
                stroke-linecap: round;
                stroke-linejoin: round;
            }

            /* Labels */
            <?php if ( $show_labels ) : ?>
            .<?php echo $uid; ?> .olo-ic-label {
                position: absolute;
                z-index: 1;
                padding: 4px 12px;
                border-radius: 4px;
                background: rgba(0,0,0,0.55);
                color: #fff;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: 0.5px;
                text-transform: uppercase;
                pointer-events: none;
                backdrop-filter: blur(2px);
                -webkit-backdrop-filter: blur(2px);
            }
            .<?php echo $uid; ?> .olo-ic-label-before {
                <?php if ( $is_vert ) : ?>
                top: 12px; left: 12px;
                <?php else : ?>
                bottom: 12px; left: 12px;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-ic-label-after {
                <?php if ( $is_vert ) : ?>
                bottom: 12px; right: 12px;
                <?php else : ?>
                bottom: 12px; right: 12px;
                <?php endif; ?>
            }
            <?php endif; ?>

            /* Hidden range input (accessible, covers full area) */
            .<?php echo $uid; ?> .olo-ic-range {
                position: absolute;
                z-index: 4;
                margin: 0;
                padding: 0;
                opacity: 0;
                cursor: col-resize;
                <?php if ( $is_vert ) : ?>
                writing-mode: vertical-lr;
                direction: rtl;
                top: 0;
                left: 50%;
                transform: translateX(-50%);
                height: 100%;
                width: <?php echo $handle_sz; ?>px;
                cursor: row-resize;
                <?php else : ?>
                top: 50%;
                left: 0;
                transform: translateY(-50%);
                width: 100%;
                height: <?php echo $handle_sz; ?>px;
                <?php endif; ?>
            }
        </style>
        <div class="olo-ic <?php echo esc_attr( $uid ); ?>" data-orientation="<?php echo esc_attr( $orientation ); ?>">
            <div class="olo-ic-after">
                <?php if ( $after_url ) : ?>
                    <img src="<?php echo $after_url; ?>" alt="<?php echo esc_attr( $after_lbl ); ?>" draggable="false" />
                <?php else : ?>
                    <div style="width:100%;height:100%;background:#374151;display:flex;align-items:center;justify-content:center;color:#9CA3AF;font-size:14px;">Dopo</div>
                <?php endif; ?>
            </div>
            <div class="olo-ic-before">
                <?php if ( $before_url ) : ?>
                    <img src="<?php echo $before_url; ?>" alt="<?php echo esc_attr( $before_lbl ); ?>" draggable="false" />
                <?php else : ?>
                    <div style="width:100%;height:100%;background:#1F2937;display:flex;align-items:center;justify-content:center;color:#9CA3AF;font-size:14px;">Prima</div>
                <?php endif; ?>
            </div>
            <div class="olo-ic-line"></div>
            <div class="olo-ic-handle">
                <?php if ( $is_vert ) : ?>
                <svg viewBox="0 0 24 24"><polyline points="6 9 12 3 18 9"/><polyline points="6 15 12 21 18 15"/></svg>
                <?php else : ?>
                <svg viewBox="0 0 24 24"><polyline points="9 6 3 12 9 18"/><polyline points="15 6 21 12 15 18"/></svg>
                <?php endif; ?>
            </div>
            <?php if ( $show_labels && $before_lbl ) : ?>
                <span class="olo-ic-label olo-ic-label-before"><?php echo $before_lbl; ?></span>
            <?php endif; ?>
            <?php if ( $show_labels && $after_lbl ) : ?>
                <span class="olo-ic-label olo-ic-label-after"><?php echo $after_lbl; ?></span>
            <?php endif; ?>
            <input type="range" min="0" max="100" value="<?php echo $start; ?>" class="olo-ic-range" aria-label="Confronto immagini" />
        </div>
        <script>
        (function(){
            document.querySelectorAll('.<?php echo $uid; ?>').forEach(function(el){
                var before = el.querySelector('.olo-ic-before');
                var line = el.querySelector('.olo-ic-line');
                var handle = el.querySelector('.olo-ic-handle');
                var range = el.querySelector('.olo-ic-range');
                var vert = el.dataset.orientation === 'vertical';
                var currentPct = <?php echo $start; ?>;

                function update(pct) {
                    pct = Math.max(0, Math.min(100, pct));
                    currentPct = pct;
                    if (vert) {
                        before.style.clipPath = 'inset(0 0 ' + (100 - pct) + '% 0)';
                        line.style.top = pct + '%';
                        handle.style.top = pct + '%';
                    } else {
                        before.style.clipPath = 'inset(0 ' + (100 - pct) + '% 0 0)';
                        line.style.left = pct + '%';
                        handle.style.left = pct + '%';
                    }
                }

                // Range input (accessibility + keyboard)
                range.addEventListener('input', function() {
                    userInteracted();
                    update(parseFloat(this.value));
                });

                // Pointer drag (mouse + touch)
                var dragging = false;
                function startDrag(e) {
                    dragging = true;
                    userInteracted();
                    e.preventDefault();
                    moveDrag(e);
                }
                function moveDrag(e) {
                    if (!dragging) return;
                    var rect = el.getBoundingClientRect();
                    var clientX, clientY;
                    if (e.touches) {
                        clientX = e.touches[0].clientX;
                        clientY = e.touches[0].clientY;
                    } else {
                        clientX = e.clientX;
                        clientY = e.clientY;
                    }
                    var pct;
                    if (vert) {
                        pct = ((clientY - rect.top) / rect.height) * 100;
                    } else {
                        pct = ((clientX - rect.left) / rect.width) * 100;
                    }
                    pct = Math.max(0, Math.min(100, pct));
                    range.value = pct;
                    update(pct);
                }
                function endDrag() {
                    dragging = false;
                    resetAutoplayTimer();
                }

                el.addEventListener('mousedown', startDrag);
                el.addEventListener('touchstart', startDrag, {passive: false});
                document.addEventListener('mousemove', moveDrag);
                document.addEventListener('touchmove', moveDrag, {passive: false});
                document.addEventListener('mouseup', endDrag);
                document.addEventListener('touchend', endDrag);

                /* ── Autoplay ── */
                <?php if ( $autoplay ) : ?>
                var apDelay = <?php echo $ap_delay; ?> * 1000;
                var apSpeed = <?php echo $ap_speed; ?> * 1000;
                var apTimer = null;
                var apAnim = null;
                var apDirection = 1; // 1 = verso 95%, -1 = verso 5%

                function startAutoplay() {
                    if (apAnim) return;
                    var startPct = currentPct;
                    var targetPct = apDirection > 0 ? 95 : 5;
                    var startTime = null;
                    var dist = Math.abs(targetPct - startPct);
                    // Adatta la durata in proporzione alla distanza
                    var duration = (dist / 90) * apSpeed;

                    function step(ts) {
                        if (!startTime) startTime = ts;
                        var elapsed = ts - startTime;
                        var progress = Math.min(elapsed / duration, 1);
                        // Easing ease-in-out
                        var ease = progress < 0.5
                            ? 2 * progress * progress
                            : 1 - Math.pow(-2 * progress + 2, 2) / 2;
                        var pct = startPct + (targetPct - startPct) * ease;
                        update(pct);
                        range.value = Math.round(pct);
                        if (progress < 1) {
                            apAnim = requestAnimationFrame(step);
                        } else {
                            apAnim = null;
                            apDirection *= -1;
                            // Prossimo ciclo dopo breve pausa
                            apTimer = setTimeout(startAutoplay, 400);
                        }
                    }
                    apAnim = requestAnimationFrame(step);
                }

                function stopAutoplay() {
                    if (apAnim) { cancelAnimationFrame(apAnim); apAnim = null; }
                    if (apTimer) { clearTimeout(apTimer); apTimer = null; }
                }

                function userInteracted() {
                    stopAutoplay();
                }

                function resetAutoplayTimer() {
                    stopAutoplay();
                    apTimer = setTimeout(startAutoplay, apDelay);
                }

                // Avvia dopo il delay iniziale
                apTimer = setTimeout(startAutoplay, apDelay);
                <?php else : ?>
                function userInteracted() {}
                function resetAutoplayTimer() {}
                <?php endif; ?>
            });
        })();
        </script>
        <?php
        return ob_get_clean();
    }

    private function get_shadow_css( $size ) {
        $map = [
            'none' => '',
            'sm'   => '0 1px 2px rgba(0,0,0,0.05)',
            'md'   => '0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1)',
            'lg'   => '0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1)',
            'xl'   => '0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1)',
        ];
        return $map[ $size ] ?? '';
    }
}
