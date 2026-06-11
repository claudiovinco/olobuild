<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_ToggleBtn_Tile extends Olo_Tile_Base {

    protected $type     = 'togglebtn';
    protected $name     = 'Pulsante Toggle';
    protected $icon     = 'dashicons-hidden';
    protected $category = 'interactive';
    protected $defaults = [
        'text_show'         => 'Mostra di più',
        'text_hide'         => 'Mostra di meno',
        'icon_show'         => 'chevron-down',
        'icon_hide'         => 'chevron-up',
        'icon_position'     => 'right',

        'target_id'         => '',
        'initial_state'     => 'hidden',
        'animation'         => 'collapse',
        'duration'          => '400',

        'btn_bg'            => 'transparent',
        'btn_color'         => '',
        'btn_hover_bg'      => '',
        'btn_border_width'  => '2',
        'btn_border_color'  => '',
        'btn_border_radius' => '8',
        'btn_padding_x'     => '24',
        'btn_padding_y'     => '12',
        'btn_font_size'     => '15',
        'btn_font_weight'   => '600',
        'btn_align'         => 'center',
        'btn_full_width'          => false,
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
        return [];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-tb-' . wp_rand( 10000, 99999 );

        $target_id   = sanitize_html_class( $s['target_id'] );
        if ( empty( $target_id ) ) {
            return '<p style="color:var(--olo-color-danger, #EF4444);font-size:13px;text-align:center;">⚠ Toggle Button: imposta l\'ID della sezione target nell\'inspector.</p>';
        }

        $text_show   = esc_html( $s['text_show'] );
        $text_hide   = esc_html( $s['text_hide'] );
        $icon_show   = $this->get_svg_icon( $s['icon_show'] );
        $icon_hide   = $this->get_svg_icon( $s['icon_hide'] );
        $icon_pos    = $s['icon_position'] === 'left' ? 'left' : 'right';
        $initial     = $s['initial_state'] === 'visible' ? 'visible' : 'hidden';
        $is_open     = $initial === 'visible';
        $animation   = in_array( $s['animation'], [ 'collapse', 'fade', 'slide' ] ) ? $s['animation'] : 'collapse';
        $duration    = max( 100, intval( $s['duration'] ) );

        // Button styles
        $bg          = $this->safe_color_css( $s['btn_bg'] ) ?: 'transparent';
        $color       = $this->safe_color_css( $s['btn_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $hover_bg    = $this->safe_color_css( $s['btn_hover_bg'] ) ?: 'color-mix(in srgb, var(--olo-color-primary, #e1474f) 10%, transparent)';
        $bw          = max( 0, intval( $s['btn_border_width'] ) );
        $bc          = $this->safe_color_css( $s['btn_border_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $radius      = Olo_Tile_Utils::border_radius( $s['btn_border_radius'] ?? 0 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['btn_border_radius_hover'] ?? null );
        $px          = intval( $s['btn_padding_x'] );
        $py          = intval( $s['btn_padding_y'] );
        $fsize       = max( 12, intval( $s['btn_font_size'] ) );
        $fweight     = in_array( $s['btn_font_weight'], [ '400', '500', '600', '700' ] ) ? $s['btn_font_weight'] : '600';
        $align       = in_array( $s['btn_align'], [ 'left', 'center', 'right' ] ) ? $s['btn_align'] : 'center';
        $full_width  = ! empty( $s['btn_full_width'] );

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: colors via the safe_color_css() whitelist (with token fallbacks), integers via intval() with max() clamps, enums via in_array() whitelists and fixed ternaries, radius via Olo_Tile_Utils helpers, target id via sanitize_html_class() + esc_attr(); $uid is internally generated. ?>
        <style>
            .<?php echo $uid; ?>-wrap {
                text-align: <?php echo $align; ?>;
            }

            .<?php echo $uid; ?> {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: <?php echo $bg; ?>;
                color: <?php echo $color; ?>;
                font-size: <?php echo (int) $fsize; ?>px;
                font-weight: <?php echo $fweight; ?>;
                line-height: 1.2;
                padding: <?php echo (int) $py; ?>px <?php echo (int) $px; ?>px;
                <?php if ( $bw > 0 ) : ?>border: <?php echo (int) $bw; ?>px solid <?php echo $bc; ?>;<?php endif; ?>
                <?php if ( $radius && $radius !== '0px' ) : ?>border-radius: <?php echo $radius; ?>;<?php endif; ?>
                cursor: pointer;
                transition: background 0.2s, transform 0.15s;
                user-select: none;
                -webkit-user-select: none;
                <?php if ( $full_width ) : ?>width: 100%; justify-content: center;<?php endif; ?>
            }
            <?php if ( $radius_hover_css !== '' ) : ?>.<?php echo $uid; ?>{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?>:hover{border-radius:<?php echo $radius_hover_css; ?> !important}<?php endif; ?>

            .<?php echo $uid; ?>:hover {
                background: <?php echo $hover_bg; ?>;
            }
            .<?php echo $uid; ?>:active {
                transform: scale(0.97);
            }
            .<?php echo $uid; ?>:focus-visible {
                outline: none;
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
            }

            .<?php echo $uid; ?> svg {
                width: <?php echo round( $fsize * 1.1 ); ?>px;
                height: <?php echo round( $fsize * 1.1 ); ?>px;
                fill: none;
                stroke: currentColor;
                stroke-width: 2;
                stroke-linecap: round;
                stroke-linejoin: round;
                flex-shrink: 0;
                transition: transform 0.3s;
            }

            /* Hide target before JS takes over (no flash) */
            <?php if ( ! $is_open ) : ?>
            #<?php echo esc_attr( $target_id ); ?>:not(.olo-tb-ready) {
                max-height: 0 !important;
                padding-top: 0 !important;
                padding-bottom: 0 !important;
                margin-top: 0 !important;
                margin-bottom: 0 !important;
                border-width: 0 !important;
                overflow: hidden;
                <?php if ( $animation === 'fade' || $animation === 'slide' ) : ?>opacity: 0;<?php endif; ?>
                <?php if ( $animation === 'slide' ) : ?>transform: translateY(-20px);<?php endif; ?>
            }
            <?php endif; ?>

            /* Target section transitions (after JS adds .olo-tb-ready) */
            #<?php echo esc_attr( $target_id ); ?>.olo-tb-ready {
                transition: max-height <?php echo (int) $duration; ?>ms ease, opacity <?php echo (int) $duration; ?>ms ease, transform <?php echo (int) $duration; ?>ms ease, padding <?php echo (int) $duration; ?>ms ease, margin <?php echo (int) $duration; ?>ms ease;
                overflow: hidden;
            }
            #<?php echo esc_attr( $target_id ); ?>.olo-tb-ready.olo-tb-hidden {
                max-height: 0;
                padding-top: 0 !important;
                padding-bottom: 0 !important;
                margin-top: 0 !important;
                margin-bottom: 0 !important;
                border-width: 0 !important;
                overflow: hidden;
                <?php if ( $animation === 'fade' || $animation === 'slide' ) : ?>opacity: 0; pointer-events: none;<?php endif; ?>
                <?php if ( $animation === 'slide' ) : ?>transform: translateY(-20px);<?php endif; ?>
            }
            #<?php echo esc_attr( $target_id ); ?>.olo-tb-ready.olo-tb-visible {
                opacity: 1;
                transform: translateY(0);
                pointer-events: auto;
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="<?php echo esc_attr( $uid ); ?>-wrap">
            <button
                type="button"
                class="<?php echo esc_attr( $uid ); ?>"
                data-target="<?php echo esc_attr( $target_id ); ?>"
                data-text-show="<?php echo esc_attr( $text_show ); ?>"
                data-text-hide="<?php echo esc_attr( $text_hide ); ?>"
                data-animation="<?php echo esc_attr( $animation ); ?>"
                data-duration="<?php echo esc_attr( $duration ); ?>"
                data-open="<?php echo $is_open ? '1' : '0'; ?>"
            >
                <?php if ( $icon_pos === 'left' ) : ?>
                    <span class="olo-tb-icon olo-tb-icon-show" style="<?php echo $is_open ? 'display:none' : ''; ?>"><?php echo $icon_show; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG from the hardcoded get_svg_icon() map ?></span>
                    <span class="olo-tb-icon olo-tb-icon-hide" style="<?php echo $is_open ? '' : 'display:none'; ?>"><?php echo $icon_hide; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG from the hardcoded get_svg_icon() map ?></span>
                <?php endif; ?>
                <span class="olo-tb-label"><?php echo $is_open ? $text_hide : $text_show; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- both branches escaped via esc_html() at assignment above ?></span>
                <?php if ( $icon_pos === 'right' ) : ?>
                    <span class="olo-tb-icon olo-tb-icon-show" style="<?php echo $is_open ? 'display:none' : ''; ?>"><?php echo $icon_show; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG from the hardcoded get_svg_icon() map ?></span>
                    <span class="olo-tb-icon olo-tb-icon-hide" style="<?php echo $is_open ? '' : 'display:none'; ?>"><?php echo $icon_hide; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG from the hardcoded get_svg_icon() map ?></span>
                <?php endif; ?>
            </button>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function(){
          document.querySelectorAll('.<?php echo $uid; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- internal 'olo-tb-' . wp_rand() identifier ?>').forEach(function(btn){
            var target = document.getElementById(btn.dataset.target);
            if (!target) return;
            var textShow = btn.dataset.textShow;
            var textHide = btn.dataset.textHide;
            var duration = parseInt(btn.dataset.duration) || 400;
            var isOpen = btn.dataset.open === '1';
            var label = btn.querySelector('.olo-tb-label');
            var iconShow = btn.querySelector('.olo-tb-icon-show');
            var iconHide = btn.querySelector('.olo-tb-icon-hide');

            // JS takes over: add .olo-tb-ready removes the :not(.olo-tb-ready) CSS rule
            if (!isOpen) {
              target.style.maxHeight = '0';
              target.classList.add('olo-tb-hidden');
            } else {
              target.classList.add('olo-tb-visible');
              target.style.maxHeight = 'none';
            }
            target.classList.add('olo-tb-ready');

            function updateUI() {
              if (label) label.textContent = isOpen ? textHide : textShow;
              if (iconShow) iconShow.style.display = isOpen ? 'none' : '';
              if (iconHide) iconHide.style.display = isOpen ? '' : 'none';
            }

            btn.addEventListener('click', function() {
              isOpen = !isOpen;
              if (isOpen) {
                target.classList.remove('olo-tb-hidden');
                target.classList.add('olo-tb-visible');
                target.style.maxHeight = target.scrollHeight + 'px';
                setTimeout(function(){
                  if (target.classList.contains('olo-tb-visible')) target.style.maxHeight = 'none';
                }, duration + 50);
              } else {
                target.style.maxHeight = target.scrollHeight + 'px';
                target.offsetHeight;
                target.classList.remove('olo-tb-visible');
                target.classList.add('olo-tb-hidden');
                target.style.maxHeight = '0';
              }
              updateUI();
            });
          });
        });
        </script>
        <?php
        // Border system — applicato al <button class="{uid}"> (pulsante visibile),
        // NON al wrapper esterno {uid}-wrap.
        $btn_sel           = ".{$uid}";
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( $btn_sel, $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( $btn_sel, $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo "{$btn_sel}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_css() from sanitized border settings; selector from internal uid
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base border helpers from sanitized border settings
        }
        return ob_get_clean();
    }

    private function get_svg_icon( $name ) {
        $icons = [
            'chevron-down' => '<svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>',
            'chevron-up'   => '<svg viewBox="0 0 24 24"><polyline points="6 15 12 9 18 15"/></svg>',
            'plus'         => '<svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>',
            'minus'        => '<svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/></svg>',
            'arrow-down'   => '<svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>',
            'arrow-up'     => '<svg viewBox="0 0 24 24"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>',
            'eye'          => '<svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>',
            'eye-off'      => '<svg viewBox="0 0 24 24"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>',
        ];
        return $icons[ $name ] ?? '';
    }
}
