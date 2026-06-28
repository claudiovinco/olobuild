<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Hiddenpop_Tile extends Olobuild_Tile_Base {

    protected $type     = 'hiddenpop';
    protected $name     = 'Hidden Pop';
    protected $icon     = 'dashicons-flag';
    protected $category = 'interactive';
    protected $defaults = [
        'mode'                => 'simple',
        'title'               => 'Titolo popup',
        'subtitle'            => '',
        'image'               => '',
        'image_position'      => 'top',
        'object_position'     => 'center center',
        'cta_text'            => '',
        'cta_url'             => '#',
        'cta_style'           => 'primary',
        'cta_target'          => '_self',
        'template_id'         => 0,
        'modal_max_width'     => 560,
        'modal_bg_color'      => '#ffffff',
        'modal_shadow'        => 'lg',
        'modal_radius'        => '16',
        'modal_border_width'  => '0',
        'modal_border_color'  => '',
        'modal_overlay'       => '60',
        'modal_close_button'  => true,
        'popup_close_overlay' => true,
        'popup_overlay_blur'  => 0,
        'popup_animation'     => 'slide-up',
        'title_color'         => '#111827',
        'title_size'          => '24',
        'text_color'          => '#4b5563',
        'trigger_threshold'   => 50,
        'trigger_direction'   => 'down',
        'exit_intent'         => false,
        'retrigger'           => false,
        'key_sequence'                 => false,
        'key_sequence_keys'            => '↑↑↓↓←→←→ba',
        'key_sequence_confetti'        => false,
        'key_sequence_confetti_colors' => '',
        'confetti_color_1'             => '',
        'confetti_color_2'             => '',
        'confetti_color_3'             => '',
        'popup_frequency'     => 'always',
        'show_max_times'      => 0,
        'display_device'      => '',
        'display_logged'      => '',
        'display_date_from'   => '',
        'display_date_to'     => '',
        'display_referrer'        => '',
        'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'mode',             'type' => 'select', 'label' => 'Mode' ],
            [ 'key' => 'title',            'type' => 'text',   'label' => 'Title' ],
            [ 'key' => 'subtitle',         'type' => 'textarea','label' => 'Subtitle' ],
            [ 'key' => 'image',            'type' => 'image',  'label' => 'Image' ],
            [ 'key' => 'cta_text',         'type' => 'text',   'label' => 'CTA Text' ],
            [ 'key' => 'cta_url',          'type' => 'text',   'label' => 'CTA URL' ],
            [ 'key' => 'cta_style',        'type' => 'select', 'label' => 'CTA Style' ],
            [ 'key' => 'trigger_threshold','type' => 'range',  'label' => 'Trigger %' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        // ── Server-side display rules ──
        $display_logged = $s['display_logged'] ?? '';
        if ( $display_logged === 'logged_in'  && ! is_user_logged_in() ) return '';
        if ( $display_logged === 'logged_out' && is_user_logged_in() )   return '';

        $date_from = $s['display_date_from'] ?? '';
        $date_to   = $s['display_date_to'] ?? '';
        $now       = current_time( 'Y-m-d' );
        if ( $date_from && $now < $date_from ) return '';
        if ( $date_to   && $now > $date_to )   return '';

        $uid = 'hp-' . wp_rand( 10000, 99999 );

        // ── Data attributes for JS ──
        $threshold   = max( 10, min( 90, intval( $s['trigger_threshold'] ) ) );
        $direction   = sanitize_text_field( $s['trigger_direction'] ?: 'down' );
        $retrigger   = ! empty( $s['retrigger'] );
        $freq        = sanitize_text_field( $s['popup_frequency'] ?: 'always' );
        $max_times   = max( 0, intval( $s['show_max_times'] ) );
        $animation   = sanitize_html_class( $s['popup_animation'] ?: 'slide-up' );
        $close_overlay = ! empty( $s['popup_close_overlay'] );

        $exit_intent = ! empty( $s['exit_intent'] );
        $display_device   = sanitize_text_field( $s['display_device'] ?? '' );
        $display_referrer = sanitize_text_field( $s['display_referrer'] ?? '' );

        // ── Trigger sequenza-tasti (Konami) — additivo ──
        $key_seq_on       = ! empty( $s['key_sequence'] );
        $key_seq_confetti = ! empty( $s['key_sequence_confetti'] );
        // Normalizza la sequenza scritta dall'utente in token KeyboardEvent.key.
        // ↑↓←→ → Arrow*, lettere/cifre → minuscolo. Spazi e separatori ignorati.
        $key_seq_arr = [];
        if ( $key_seq_on ) {
            $raw_seq = (string) ( $s['key_sequence_keys'] ?? '' );
            if ( trim( $raw_seq ) === '' ) {
                $raw_seq = '↑↑↓↓←→←→ba';
            }
            // Itera per carattere unicode (mb_str_split: PHP 7.4+).
            $chars = function_exists( 'mb_str_split' )
                ? mb_str_split( $raw_seq )
                : preg_split( '//u', $raw_seq, -1, PREG_SPLIT_NO_EMPTY );
            $arrow_map = [
                '↑' => 'ArrowUp',   '⬆' => 'ArrowUp',
                '↓' => 'ArrowDown', '⬇' => 'ArrowDown',
                '←' => 'ArrowLeft', '⬅' => 'ArrowLeft',
                '→' => 'ArrowRight','➡' => 'ArrowRight',
            ];
            foreach ( (array) $chars as $ch ) {
                if ( $ch === null || $ch === '' ) { continue; }
                if ( isset( $arrow_map[ $ch ] ) ) {
                    $key_seq_arr[] = $arrow_map[ $ch ];
                } elseif ( trim( $ch ) === '' || $ch === ',' || $ch === '-' || $ch === '+' ) {
                    // separatori "decorativi" ignorati
                    continue;
                } else {
                    $key_seq_arr[] = function_exists( 'mb_strtolower' ) ? mb_strtolower( $ch, 'UTF-8' ) : strtolower( $ch );
                }
            }
        }
        // Fallback di sicurezza: se dopo il parsing la coda è vuota, usa il Konami canonico.
        if ( $key_seq_on && empty( $key_seq_arr ) ) {
            $key_seq_arr = [ 'ArrowUp','ArrowUp','ArrowDown','ArrowDown','ArrowLeft','ArrowRight','ArrowLeft','ArrowRight','b','a' ];
        }

        // Colori coriandoli — dual-format:
        //   1) slot color picker confetti_color_1..3 (formato nuovo),
        //   2) fallback CSV legacy key_sequence_confetti_colors (template esistenti),
        //   3) fallback palette brand (token).
        $confetti_colors = [];
        if ( $key_seq_confetti ) {
            foreach ( [ 'confetti_color_1', 'confetti_color_2', 'confetti_color_3' ] as $slot ) {
                $col = $this->safe_color_css( trim( (string) ( $s[ $slot ] ?? '' ) ) );
                if ( $col !== '' ) { $confetti_colors[] = $col; }
            }
            if ( empty( $confetti_colors ) ) {
                $raw_cols = (string) ( $s['key_sequence_confetti_colors'] ?? '' );
                foreach ( explode( ',', $raw_cols ) as $col ) {
                    $col = $this->safe_color_css( trim( $col ) );
                    if ( $col !== '' ) { $confetti_colors[] = $col; }
                }
            }
            if ( empty( $confetti_colors ) ) {
                $confetti_colors = [
                    'var(--olo-color-primary, #e1474f)',
                    'var(--olo-color-secondary, #1f2937)',
                    'var(--olo-color-accent, #f59e0b)',
                ];
            }
        }

        // ── Modal styling ──
        $max_w       = max( 300, min( 900, intval( $s['modal_max_width'] ) ) );
        $bg_color    = $this->safe_color_css( $s['modal_bg_color'] ?? '' ) ?: '#ffffff';
        $shadow      = Olobuild_Tile_Utils::shadow( $s['modal_shadow'] ?? 'lg' );
        $radius      = Olobuild_Tile_Utils::border_radius( $s['modal_radius'] ?? 16 );
        $radius_hover_css = Olobuild_Tile_Utils::radius_force_css( $s['modal_radius_hover'] ?? null );
        $border_w    = max( 0, intval( $s['modal_border_width'] ?? 0 ) );
        $border_c    = $this->safe_color_css( $s['modal_border_color'] ?? '' ) ?: '#e5e7eb';
        $overlay_pct = max( 0, min( 100, intval( $s['modal_overlay'] ?? 60 ) ) );
        $overlay_a   = round( $overlay_pct / 100, 2 );
        $blur        = max( 0, min( 20, intval( $s['popup_overlay_blur'] ?? 0 ) ) );

        $title_color = $this->safe_color_css( $s['title_color'] ?? '' ) ?: '#111827';
        $title_size  = max( 14, min( 48, intval( $s['title_size'] ?? 24 ) ) );
        $text_color  = $this->safe_color_css( $s['text_color'] ?? '' ) ?: '#4b5563';

        // ── Modal opts ──
        $modal_opts = [];
        if ( ! $close_overlay ) {
            $modal_opts[] = 'bg-close: false';
        }
        $modal_attr = 'uk-modal' . ( ! empty( $modal_opts ) ? '="' . implode( '; ', $modal_opts ) . '"' : '' );

        // ── CTA button ──
        $cta_text   = trim( $s['cta_text'] ?? '' );
        $cta_url    = esc_url( $s['cta_url'] ?? '#' );
        $cta_target = $s['cta_target'] === '_blank' ? '_blank' : '_self';
        $cta_cls    = 'uk-button uk-button-' . sanitize_html_class( $s['cta_style'] ?: 'primary' );

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist colours, intval()-clamped sizes, Olobuild_Tile_Utils border_radius()/radius_force_css()/shadow() helpers (integer px / fixed map) and the esc_attr()'d generated $uid. ?>
        <style>
            #<?php echo esc_attr( $uid ); ?>-modal {
                z-index: 999999 !important;
                background: rgba(0,0,0,<?php echo (float) $overlay_a; ?>) !important;
                <?php if ( $blur > 0 ) : ?>backdrop-filter: blur(<?php echo (int) $blur; ?>px); -webkit-backdrop-filter: blur(<?php echo (int) $blur; ?>px);<?php endif; ?>
            }
            #<?php echo esc_attr( $uid ); ?>-modal .uk-modal-dialog {
                max-width: <?php echo (int) $max_w; ?>px;
                background: <?php echo $bg_color; ?>;
                <?php if ( $radius && $radius !== '0px' ) : ?>border-radius: <?php echo $radius; ?>; overflow: hidden;<?php endif; ?>
                <?php if ( $shadow !== 'none' ) : ?>box-shadow: <?php echo $shadow; ?>;<?php endif; ?>
                <?php if ( $border_w > 0 ) : ?>border: <?php echo (int) $border_w; ?>px solid <?php echo $border_c; ?>;<?php endif; ?>
            }
            <?php if ( $radius_hover_css !== '' ) : ?>#<?php echo esc_attr( $uid ); ?>-modal .uk-modal-dialog{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}#<?php echo esc_attr( $uid ); ?>-modal .uk-modal-dialog:hover{border-radius:<?php echo $radius_hover_css; ?> !important}<?php endif; ?>
            <?php if ( $animation === 'slide-up' ) : ?>
            #<?php echo esc_attr( $uid ); ?>-modal .uk-modal-dialog { animation: oloHpSlideUp 0.35s ease-out; }
            @keyframes oloHpSlideUp { from { opacity:0; transform: translateY(40px); } to { opacity:1; transform: translateY(0); } }
            <?php elseif ( $animation === 'slide-down' ) : ?>
            #<?php echo esc_attr( $uid ); ?>-modal .uk-modal-dialog { animation: oloHpSlideDown 0.35s ease-out; }
            @keyframes oloHpSlideDown { from { opacity:0; transform: translateY(-40px); } to { opacity:1; transform: translateY(0); } }
            <?php elseif ( $animation === 'zoom' ) : ?>
            #<?php echo esc_attr( $uid ); ?>-modal .uk-modal-dialog { animation: oloHpZoom 0.3s ease-out; }
            @keyframes oloHpZoom { from { opacity:0; transform: scale(0.7); } to { opacity:1; transform: scale(1); } }
            <?php elseif ( $animation === 'flip' ) : ?>
            #<?php echo esc_attr( $uid ); ?>-modal .uk-modal-dialog { animation: oloHpFlip 0.4s ease-out; }
            @keyframes oloHpFlip { from { opacity:0; transform: perspective(600px) rotateX(-60deg); } to { opacity:1; transform: perspective(600px) rotateX(0); } }
            <?php else : ?>
            #<?php echo esc_attr( $uid ); ?>-modal .uk-modal-dialog { animation: oloHpFade 0.25s ease-out; }
            @keyframes oloHpFade { from { opacity:0; } to { opacity:1; } }
            <?php endif; ?>
            #<?php echo esc_attr( $uid ); ?>-modal .olo-hp-title {
                font-size: <?php echo (int) $title_size; ?>px;
                font-weight: 700;
                color: <?php echo $title_color; ?>;
                margin: 0 0 8px;
                line-height: 1.3;
            }
            #<?php echo esc_attr( $uid ); ?>-modal .olo-hp-text {
                color: <?php echo $text_color; ?>;
                font-size: 15px;
                line-height: 1.6;
                margin: 0 0 16px;
            }
            #<?php echo esc_attr( $uid ); ?>-modal .olo-hp-cta {
                margin-top: 8px;
            }
            /* a11y: anello di focus visibile da tastiera su CTA + chiudi */
            #<?php echo esc_attr( $uid ); ?>-modal .olo-hp-cta a:focus-visible,
            #<?php echo esc_attr( $uid ); ?>-modal .uk-modal-close-default:focus-visible {
                outline: none;
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
            }
            <?php if ( $key_seq_confetti ) : ?>
            /* Coriandoli al match della sequenza — layer + pezzi scoped per istanza */
            #<?php echo esc_attr( $uid ); ?>-confetti {
                position: fixed; inset: 0; z-index: 1000000;
                pointer-events: none; overflow: hidden;
            }
            #<?php echo esc_attr( $uid ); ?>-confetti .olo-hp-conf {
                position: absolute; top: -16px; width: 10px; height: 14px;
                border-radius: 2px; will-change: transform, opacity;
                animation: <?php echo esc_attr( $uid ); ?>-conf-fall linear forwards;
            }
            @keyframes <?php echo esc_attr( $uid ); ?>-conf-fall {
                0%   { transform: translateY(0) rotate(0deg);   opacity: 1; }
                100% { transform: translateY(105vh) rotate(720deg); opacity: 0; }
            }
            @media (prefers-reduced-motion: reduce) {
                /* reduced-motion: nessun coriandolo animato */
                #<?php echo esc_attr( $uid ); ?>-confetti { display: none !important; }
            }
            <?php endif; ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <div class="olo-hiddenpop" id="<?php echo esc_attr( $uid ); ?>">
            <!-- Invisible scroll marker (must be visible to IntersectionObserver) -->
            <div class="olo-hiddenpop-marker" id="<?php echo esc_attr( $uid ); ?>-marker" style="height:1px;width:100%;pointer-events:none;"></div>
        </div>

        <!-- Modal -->
        <div id="<?php echo esc_attr( $uid ); ?>-modal" <?php echo $modal_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attribute composed only of the fixed internal literals 'uk-modal' and 'bg-close: false' ?>>
            <div class="uk-modal-dialog uk-margin-auto-vertical">
                <?php if ( ! empty( $s['modal_close_button'] ) ) : ?>
                    <button class="uk-modal-close-default" type="button" uk-close></button>
                <?php endif; ?>

                <?php if ( $s['mode'] === 'template' && ! empty( $s['template_id'] ) ) : ?>
                    <div class="uk-modal-body" uk-overflow-auto>
                        <?php echo $this->render_template_content( (int) $s['template_id'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- template HTML rendered by Olobuild_Frontend_Renderer; each tile escapes its own output at build time ?>
                    </div>
                <?php else : ?>
                    <?php
                    $image    = trim( $s['image'] ?? '' );
                    $position = $s['image_position'] ?? 'top';
                    $has_img  = ! empty( $image );
                    $is_side  = $has_img && ( $position === 'left' || $position === 'right' );
                    // Punto focale (object-position): default invariato = comportamento attuale.
                    // Applicato SOLO alle immagini laterali (object-fit:cover); con top/bottom
                    // l'immagine è height:auto e il valore resta ininfluente.
                    $obj_pos = trim( (string) ( $s['object_position'] ?? 'center center' ) );
                    if ( $obj_pos === '' ) { $obj_pos = 'center center'; }
                    ?>

                    <?php if ( $has_img && $position === 'top' ) : ?>
                    <div><img src="<?php echo esc_url( $image ); ?>" alt="" loading="lazy" style="width:100%;height:auto;display:block;" /></div>
                    <?php endif; ?>

                    <?php if ( $is_side ) : ?>
                    <div uk-grid class="uk-child-width-1-2@s uk-grid-collapse">
                        <?php if ( $position === 'left' ) : ?>
                        <div><img src="<?php echo esc_url( $image ); ?>" alt="" loading="lazy" style="width:100%;height:100%;object-fit:cover;object-position:<?php echo esc_attr( $obj_pos ); ?>;display:block;" /></div>
                        <?php endif; ?>
                        <div class="uk-padding">
                    <?php else : ?>
                    <div class="uk-modal-body">
                    <?php endif; ?>

                        <?php if ( ! empty( $s['title'] ) ) : ?>
                            <h2 class="olo-hp-title"><?php echo esc_html( $s['title'] ); ?></h2>
                        <?php endif; ?>

                        <?php if ( ! empty( $s['subtitle'] ) ) : ?>
                            <p class="olo-hp-text"><?php echo nl2br( esc_html( $s['subtitle'] ) ); ?></p>
                        <?php endif; ?>

                        <?php if ( $cta_text ) : ?>
                            <div class="olo-hp-cta">
                                <a href="<?php echo $cta_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped via esc_url() at assignment above ?>" target="<?php echo esc_attr( $cta_target ); ?>"
                                   class="<?php echo esc_attr( $cta_cls ); ?>"
                                   <?php if ( $cta_target === '_blank' ) echo 'rel="noopener"'; ?>
                                ><?php echo esc_html( $cta_text ); ?></a>
                            </div>
                        <?php endif; ?>

                    <?php if ( $is_side ) : ?>
                        </div>
                        <?php if ( $position === 'right' ) : ?>
                        <div><img src="<?php echo esc_url( $image ); ?>" alt="" loading="lazy" style="width:100%;height:100%;object-fit:cover;object-position:<?php echo esc_attr( $obj_pos ); ?>;display:block;" /></div>
                        <?php endif; ?>
                    </div>
                    <?php else : ?>
                    </div>
                    <?php endif; ?>

                    <?php if ( $has_img && $position === 'bottom' ) : ?>
                    <div><img src="<?php echo esc_url( $image ); ?>" alt="" loading="lazy" style="width:100%;height:auto;display:block;" /></div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <script>
        (function(){
            /* Skip in builder iframe */
            if (window.location.search.indexOf('olo_builder_iframe') !== -1) return;

            var marker = document.getElementById('<?php echo esc_js( $uid ); ?>-marker');
            var modalEl = document.getElementById('<?php echo esc_js( $uid ); ?>-modal');
            if (!marker) return;
            if (!modalEl) return;

            var threshold = <?php echo intval( $threshold ); ?>;
            var direction = '<?php echo esc_js( $direction ); ?>';
            var retrigger = <?php echo $retrigger ? 'true' : 'false'; ?>;
            var popupKey  = 'olo_hp_<?php echo esc_js( $uid ); ?>';
            var freq      = '<?php echo esc_js( $freq ); ?>';
            var maxTimes  = <?php echo intval( $max_times ); ?>;
            var exitIntent = <?php echo $exit_intent ? 'true' : 'false'; ?>;
            var device    = '<?php echo esc_js( $display_device ); ?>';
            var referrer  = '<?php echo esc_js( $display_referrer ); ?>';
            var fired     = false;
            var lastY     = window.scrollY;
            var modalOpen = false;

            if (device === 'desktop') { if (window.innerWidth < 1024) return; }
            if (device === 'mobile')  { if (window.innerWidth >= 1024) return; }
            if (referrer) { if (document.referrer.indexOf(referrer) === -1) return; }

            function getCookie(n) {
                var m = document.cookie.match('(^|; )' + n + '=([^;]*)');
                return m ? decodeURIComponent(m[2]) : null;
            }
            function setCookie(n, v, d) {
                var dt = new Date();
                dt.setTime(dt.getTime() + (d * 86400000));
                document.cookie = n + '=' + encodeURIComponent(v) + ';expires=' + dt.toUTCString() + ';path=/;SameSite=Lax';
            }
            function canShow() {
                if (freq === 'once_session') {
                    try { if (sessionStorage.getItem(popupKey)) return false; } catch(e){}
                }
                if (freq === 'once_day')  { if (getCookie(popupKey + '_d')) return false; }
                if (freq === 'once_week') { if (getCookie(popupKey + '_w')) return false; }
                if (freq === 'once_ever') {
                    try { if (localStorage.getItem(popupKey)) return false; } catch(e){}
                }
                if (maxTimes > 0) {
                    try {
                        var c = parseInt(localStorage.getItem(popupKey + '_cnt')) || 0;
                        if (c >= maxTimes) return false;
                    } catch(e){}
                }
                return true;
            }
            function markShown() {
                if (freq === 'once_session') {
                    try { sessionStorage.setItem(popupKey, '1'); } catch(e){}
                }
                if (freq === 'once_day')  { setCookie(popupKey + '_d', '1', 1); }
                if (freq === 'once_week') { setCookie(popupKey + '_w', '1', 7); }
                if (freq === 'once_ever') {
                    try { localStorage.setItem(popupKey, '1'); } catch(e){}
                }
                if (maxTimes > 0) {
                    try {
                        var c = parseInt(localStorage.getItem(popupKey + '_cnt')) || 0;
                        localStorage.setItem(popupKey + '_cnt', String(c + 1));
                    } catch(e){}
                }
            }

            if (!canShow()) return;

            function isModalVisible() {
                /* UIkit adds uk-open class when modal is visible */
                var el = document.getElementById('<?php echo esc_js( $uid ); ?>-modal');
                if (!el) return false;
                return el.classList.contains('uk-open');
            }

            function openModal() {
                fired = true;
                markShown();
                if (typeof UIkit !== 'undefined') {
                    UIkit.modal(modalEl).show();
                }
            }

            /* ── Exit Intent mode ── */
            if (exitIntent) {
              document.addEventListener('mouseout', function(evt) {
                if (evt.clientY < 10) {
                  if (!fired) {
                    if (!isModalVisible()) {
                      if (canShow()) {
                        openModal();
                      }
                    }
                  }
                }
              });
            } else {
              /* ── Scroll trigger mode ── */

              /* Track scroll direction */
              window.addEventListener('scroll', function() { lastY = window.scrollY; }, { passive: true });

              function directionOk() {
                  if (direction === 'both') return true;
                  var curY = window.scrollY;
                  if (direction === 'down') return curY >= lastY;
                  if (direction === 'up')   return curY <= lastY;
                  return true;
              }

              function isMarkerAtThreshold() {
                  var rect = marker.getBoundingClientRect();
                  var triggerLine = window.innerHeight * (threshold / 100);
                  if (rect.top > triggerLine) return false;
                  return rect.top >= triggerLine - 100;
              }

              function tryOpen() {
                  if (fired) return;
                  if (isModalVisible()) return;
                  if (!directionOk()) return;
                  if (!canShow()) return;
                  if (!isMarkerAtThreshold()) return;
                  openModal();
              }

              var scrollTimer = null;
              window.addEventListener('scroll', function() {
                  if (scrollTimer) return;
                  scrollTimer = setTimeout(function() {
                      scrollTimer = null;
                      if (!fired) {
                          tryOpen();
                      } else if (retrigger) { if (!isModalVisible()) { if (!isMarkerAtThreshold()) {
                          fired = false;
                      } } }
                  }, 80);
              }, { passive: true });

              /* Check on load in case marker is already at threshold */
              setTimeout(tryOpen, 500);
            }

            <?php if ( $key_seq_on ) : ?>
            /* ── Trigger sequenza-tasti (Konami) — additivo agli altri trigger ── */
            /* Rif. 60-tema-community-gamer.html. Idempotente: guard su dataset del modal. */
            (function(){
                if (modalEl.dataset.oloHpKeySeq) { return; }   // una sola init per istanza
                modalEl.dataset.oloHpKeySeq = '1';

                var SEQ = <?php echo wp_json_encode( $key_seq_arr ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline JSON in <script> generated by wp_json_encode() ?>;
                if (!SEQ || !SEQ.length) { return; }
                var pos = 0;

                function keyShow() {
                    /* Riusa il meccanismo show già presente + regole di frequenza/visibilità */
                    if (isModalVisible()) { return; }
                    if (!canShow()) { return; }
                    openModal();
                    <?php if ( $key_seq_confetti ) : ?>
                    party();
                    <?php endif; ?>
                }

                document.addEventListener('keydown', function(e){
                    /* Non interferire con la digitazione in campi/editor */
                    var tgt = e.target;
                    if (tgt && (tgt.tagName === 'INPUT' || tgt.tagName === 'TEXTAREA' || tgt.tagName === 'SELECT' || tgt.isContentEditable)) {
                        return;
                    }
                    var k = (e.key && e.key.length === 1) ? e.key.toLowerCase() : e.key;
                    if (k === SEQ[pos]) {
                        pos++;
                        if (pos === SEQ.length) {
                            pos = 0;
                            keyShow();
                        }
                    } else {
                        /* ricomincia; se il tasto coincide col primo, conta come passo 1 */
                        pos = (k === SEQ[0]) ? 1 : 0;
                    }
                });

                <?php if ( $key_seq_confetti ) : ?>
                /* Coriandoli one-shot, scoped per istanza. Off in reduced-motion. */
                function party(){
                    var rm = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)');
                    if (rm && rm.matches) { return; }
                    var COLORS = <?php echo wp_json_encode( $confetti_colors ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline JSON in <script> generated by wp_json_encode() from safe_color_css()'d colours ?>;
                    if (!COLORS || !COLORS.length) { return; }
                    var layer = document.getElementById('<?php echo esc_js( $uid ); ?>-confetti');
                    if (!layer) {
                        layer = document.createElement('div');
                        layer.id = '<?php echo esc_js( $uid ); ?>-confetti';
                        document.body.appendChild(layer);
                    }
                    var N = 90;
                    for (var n = 0; n < N; n++) {
                        var c = document.createElement('span');
                        c.className = 'olo-hp-conf';
                        c.style.background = COLORS[n % COLORS.length];
                        c.style.left = (Math.random() * 100) + 'vw';
                        c.style.animationDuration = (1.6 + Math.random() * 1.4) + 's';
                        c.style.animationDelay = (Math.random() * 0.4) + 's';
                        c.style.transform = 'translateY(0) rotate(' + (Math.random() * 360) + 'deg)';
                        (function(el){
                            el.addEventListener('animationend', function(){ if (el.parentNode) { el.parentNode.removeChild(el); } });
                        })(c);
                        layer.appendChild(c);
                    }
                    /* safety cleanup nel caso animationend non scatti */
                    setTimeout(function(){ if (layer) { layer.innerHTML = ''; } }, 4000);
                }
                <?php endif; ?>
            })();
            <?php endif; ?>
        })();
        </script>
        <?php
        // Border system (su elemento trigger)
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".hp-trigger-{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".hp-trigger-{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".hp-trigger-{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() (integer-forced widths) for the internally generated uid
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() shared helpers
        }

        return ob_get_clean();
    }

    /**
     * Render template mode content.
     */
    private function render_template_content( $template_id ) {
        if ( $template_id <= 0 ) {
            return '<p><em>Template non disponibile.</em></p>';
        }
        $renderer = new Olobuild_Frontend_Renderer();
        $output   = $renderer->render_shortcode( [ 'id' => $template_id ] );
        if ( empty( $output ) || str_starts_with( $output, '<!-- Olobuilder' ) ) {
            return '<p><em>Template non disponibile.</em></p>';
        }
        return $output;
    }
}
