<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Floatingpanel_Tile extends Olo_Tile_Base {

    protected $type     = 'floatingpanel';
    protected $name     = 'Pannello flottante';
    protected $icon     = 'dashicons-move';
    protected $category = 'interactive';
    protected $defaults = [
        'position'          => 'fixed',
        'placement'         => 'bottom-right',
        'offset_x'         => '20',
        'offset_y'         => '20',
        'custom_top'        => '',
        'custom_left'       => '',
        'custom_bottom'     => '',
        'custom_right'      => '',
        'width'             => '300',
        'height'            => '',
        'z_index'           => '9999',
        'bg_color'          => '#ffffff',
        'border_color'      => '',
        'border_width'      => '0',
        'border_radius'     => '12',
        'shadow'            => true,
        'shadow_color'      => 'rgba(0,0,0,0.15)',
        'shadow_blur'       => '20',
        'shadow_y'          => '4',
        'padding'           => '20',
        'trigger_mode'      => 'always',
        'trigger_icon'      => 'plus',
        'trigger_size'      => '48',
        'trigger_bg'        => '',
        'trigger_color'     => '#ffffff',
        'trigger_radius'    => '50',
        'trigger_shadow'    => true,
        'show_close'        => true,
        'close_color'       => '#666666',
        'close_size'        => '20',
        'close_outside'     => true,
        'animation'         => 'fade',
        'animation_duration'=> '300',
        'visible_desktop'   => true,
        'visible_tablet'    => true,
        'visible_mobile'    => true,
        'layout_direction'  => 'column',
        'layout_gap'        => '12',
        'layout_align'      => 'stretch',
    ];

    public function get_controls() {
        return [];
    }

    /**
     * Placement map — shared between panel and trigger positioning.
     */
    private function get_placement_map( $ox, $oy ) {
        return [
            'top-left'      => "top:{$oy}px;left:{$ox}px;",
            'top-center'    => "top:{$oy}px;left:50%;transform:translateX(-50%);",
            'top-right'     => "top:{$oy}px;right:{$ox}px;",
            'center-left'   => "top:50%;left:{$ox}px;transform:translateY(-50%);",
            'center'        => "top:50%;left:50%;transform:translate(-50%,-50%);",
            'center-right'  => "top:50%;right:{$ox}px;transform:translateY(-50%);",
            'bottom-left'   => "bottom:{$oy}px;left:{$ox}px;",
            'bottom-center' => "bottom:{$oy}px;left:50%;transform:translateX(-50%);",
            'bottom-right'  => "bottom:{$oy}px;right:{$ox}px;",
        ];
    }

    /**
     * Build custom position CSS from settings.
     */
    private function build_custom_pos( $s ) {
        $css = '';
        foreach ( [ 'top', 'left', 'bottom', 'right' ] as $dir ) {
            $val = $s[ 'custom_' . $dir ] ?? '';
            if ( $val !== '' ) {
                $css .= $dir . ':' . esc_attr( $val ) . ( is_numeric( $val ) ? 'px' : '' ) . ';';
            }
        }
        return $css;
    }

    /**
     * Render the floating panel opening wrapper.
     * The frontend renderer injects children, then calls render_closing().
     */
    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-fp-' . substr( md5( wp_json_encode( $s ) ), 0, 8 );

        // --- Position ---
        $pos       = in_array( $s['position'], [ 'fixed', 'absolute', 'sticky', 'relative', 'static' ], true ) ? $s['position'] : 'fixed';
        $placement = $s['placement'];
        $ox        = intval( $s['offset_x'] );
        $oy        = intval( $s['offset_y'] );
        $map       = $this->get_placement_map( $ox, $oy );

        $pos_css = "position:{$pos};z-index:" . intval( $s['z_index'] ) . ";";

        if ( $placement === 'custom' ) {
            $pos_css .= $this->build_custom_pos( $s );
        } else {
            $pos_css .= $map[ $placement ] ?? $map['bottom-right'];
        }

        // --- Dimensions ---
        $w = $s['width'];
        if ( $w !== '' ) {
            $pos_css .= 'width:' . ( is_numeric( $w ) ? $w . 'px' : esc_attr( $w ) ) . ';';
        }
        $h = $s['height'];
        if ( $h !== '' ) {
            $pos_css .= 'height:' . ( is_numeric( $h ) ? $h . 'px' : esc_attr( $h ) ) . ';';
        }

        // --- Visual ---
        $bg = $this->safe_color_css( $s['bg_color'] ) ?: '#ffffff';
        $pos_css .= "background:{$bg};";
        $pos_css .= 'border-radius:' . intval( $s['border_radius'] ) . 'px;';
        $pos_css .= 'padding:' . Olo_Tile_Utils::spacing_css( $s['tile_padding'] ?? $s['padding'] ?? 20, 20 ) . ';';
        $pos_css .= 'box-sizing:border-box;';

        if ( intval( $s['border_width'] ) > 0 ) {
            $bc = $this->safe_color_css( $s['border_color'] ) ?: '#e0e0e0';
            $pos_css .= 'border:' . intval( $s['border_width'] ) . 'px solid ' . $bc . ';';
        }

        if ( ! empty( $s['shadow'] ) && $s['shadow'] !== 'false' ) {
            $sc = $s['shadow_color'] ?: 'rgba(0,0,0,0.15)';
            $sb = intval( $s['shadow_blur'] );
            $sy = intval( $s['shadow_y'] );
            $pos_css .= "box-shadow:0 {$sy}px {$sb}px {$sc};";
        }

        // --- Layout for children ---
        $dir   = in_array( $s['layout_direction'], [ 'row', 'column' ], true ) ? $s['layout_direction'] : 'column';
        $gap   = intval( $s['layout_gap'] );
        $align = in_array( $s['layout_align'], [ 'stretch', 'flex-start', 'center', 'flex-end' ], true ) ? $s['layout_align'] : 'stretch';
        $pos_css .= "display:flex;flex-direction:{$dir};gap:{$gap}px;align-items:{$align};";

        // --- Responsive visibility classes ---
        $resp_class = '';
        if ( empty( $s['visible_desktop'] ) || $s['visible_desktop'] === 'false' ) $resp_class .= ' olo-fp-hide-desktop';
        if ( empty( $s['visible_tablet'] ) || $s['visible_tablet'] === 'false' )   $resp_class .= ' olo-fp-hide-tablet';
        if ( empty( $s['visible_mobile'] ) || $s['visible_mobile'] === 'false' )   $resp_class .= ' olo-fp-hide-mobile';

        // --- Trigger mode ---
        $is_trigger  = ( $s['trigger_mode'] === 'button' );
        $anim        = in_array( $s['animation'], [ 'fade', 'slide-up', 'slide-down', 'slide-left', 'slide-right', 'scale' ], true ) ? $s['animation'] : 'fade';
        $dur         = intval( $s['animation_duration'] );

        ob_start();

        // --- Trigger button ---
        $trigger_html = '';
        if ( $is_trigger ) :
            $t_size   = intval( $s['trigger_size'] );
            $t_bg     = $this->safe_color_css( $s['trigger_bg'] ) ?: 'var(--olo-color-primary, #3B82F6)';
            $t_color  = $this->safe_color_css( $s['trigger_color'] ) ?: '#ffffff';
            $t_radius = intval( $s['trigger_radius'] );
            $t_shadow = ( ! empty( $s['trigger_shadow'] ) && $s['trigger_shadow'] !== 'false' )
                        ? 'box-shadow:0 4px 12px rgba(0,0,0,0.2);' : '';

            $t_pos_css = "position:{$pos};z-index:" . ( intval( $s['z_index'] ) + 1 ) . ";";
            if ( $placement === 'custom' ) {
                $t_pos_css .= $this->build_custom_pos( $s );
            } else {
                $t_pos_css .= $map[ $placement ] ?? $map['bottom-right'];
            }

            $icon_svg = $this->get_trigger_icon( $s['trigger_icon'], $t_color );
            ob_start();
            ?>
            <button class="<?php echo $uid; ?>-trigger <?php echo trim( $resp_class ); ?>"
                    style="<?php echo $t_pos_css; ?>width:<?php echo $t_size; ?>px;height:<?php echo $t_size; ?>px;background:<?php echo $t_bg; ?>;color:<?php echo $t_color; ?>;border:none;border-radius:<?php echo $t_radius; ?>%;cursor:pointer;display:flex;align-items:center;justify-content:center;<?php echo $t_shadow; ?>"
                    data-olo-fp-trigger="<?php echo $uid; ?>"
                    aria-label="<?php echo esc_attr( olo_t( 'Apri pannello' ) ); ?>">
                <?php echo $icon_svg; ?>
            </button>
            <?php
            $trigger_html = ob_get_clean();
        endif;

        // Everything inside a single wrapper div that gets moved to body
        $wrapper_style = ! empty( $s['_builder_mode'] ) ? 'scroll-margin-top:140px;' : 'display:none;';
        ?>
        <div class="olo-fp-wrapper" data-olo-fp-wrapper="<?php echo $uid; ?>" style="<?php echo $wrapper_style; ?>">
        <style>
        .olo-fp-hide-desktop { display: none !important; }
        @media (max-width: 1024px) {
            .olo-fp-hide-desktop { display: flex !important; }
            .olo-fp-hide-tablet { display: none !important; }
        }
        @media (max-width: 640px) {
            .olo-fp-hide-tablet { display: flex !important; }
            .olo-fp-hide-mobile { display: none !important; }
        }
        .<?php echo $uid; ?>-panel {
            transition: opacity <?php echo $dur; ?>ms ease, transform <?php echo $dur; ?>ms ease;
        }
        .<?php echo $uid; ?>-panel.olo-fp-hidden {
            pointer-events: none;
            opacity: 0;
            <?php
            if ( $anim === 'slide-up' )    echo 'transform: translateY(20px);';
            elseif ( $anim === 'slide-down' ) echo 'transform: translateY(-20px);';
            elseif ( $anim === 'slide-left' ) echo 'transform: translateX(20px);';
            elseif ( $anim === 'slide-right' ) echo 'transform: translateX(-20px);';
            elseif ( $anim === 'scale' )       echo 'transform: scale(0.85);';
            ?>
        }
        </style>
        <?php echo $trigger_html; ?>
        <div class="olo-floatingpanel <?php echo $uid; ?>-panel <?php echo trim( $resp_class ); ?><?php if ( $is_trigger ) echo ' olo-fp-hidden'; ?>"
             style="<?php echo $pos_css; ?>"
             data-olo-fp-id="<?php echo $uid; ?>">

            <?php
            $show_close = ( ! empty( $s['show_close'] ) && $s['show_close'] !== 'false' );
            // Hide close button in builder mode (panel must stay visible for editing)
            if ( $show_close && empty( $s['_builder_mode'] ) ) :
                $cc = $this->safe_color_css( $s['close_color'] ) ?: '#666';
                $cs = intval( $s['close_size'] );
            ?>
            <button class="olo-fp-close"
                    style="position:absolute;top:8px;right:8px;background:none;border:none;cursor:pointer;padding:4px;line-height:0;color:<?php echo $cc; ?>;z-index:2;"
                    data-olo-fp-close="<?php echo $uid; ?>"
                    aria-label="<?php echo esc_attr( olo_t( 'Chiudi' ) ); ?>">
                <svg width="<?php echo $cs; ?>" height="<?php echo $cs; ?>" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            <?php endif; ?>

        <?php
        $html = ob_get_clean();

        // Store state for render_closing()
        $this->_uid           = $uid;
        $this->_is_trigger    = $is_trigger;
        $this->_close_outside = ( ! empty( $s['close_outside'] ) && $s['close_outside'] !== 'false' );

        return $html;
    }

    /**
     * Render closing wrapper + JS to move elements to body.
     */
    public function render_closing( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = $this->_uid ?? 'olo-fp-' . substr( md5( wp_json_encode( $s ) ), 0, 8 );
        $is_trigger    = $this->_is_trigger ?? ( $s['trigger_mode'] === 'button' );
        $close_outside = $this->_close_outside ?? ( ! empty( $s['close_outside'] ) && $s['close_outside'] !== 'false' );
        $dur           = intval( $s['animation_duration'] );

        // In builder mode: skip body-move JS (panel must stay in canvas iframe flow).
        if ( ! empty( $s['_builder_mode'] ) ) {
            return '</div><!-- .olo-floatingpanel --></div><!-- .olo-fp-wrapper -->';
        }

        ob_start();
        ?>
        </div><!-- .olo-floatingpanel -->

        <script>
        (function(){
            var wrapper = document.querySelector('[data-olo-fp-wrapper="<?php echo $uid; ?>"]');
            if (!wrapper) return;

            /* Move the entire wrapper (style + trigger + panel) to body and make visible */
            document.body.appendChild(wrapper);
            wrapper.style.display = "contents";

            var panel = wrapper.querySelector('[data-olo-fp-id="<?php echo $uid; ?>"]');
            var trigger = wrapper.querySelector('[data-olo-fp-trigger="<?php echo $uid; ?>"]');

            function showPanel() {
                panel.classList.remove("olo-fp-hidden");
                if (trigger) trigger.style.display = "none";
            }
            function hidePanel() {
                panel.classList.add("olo-fp-hidden");
                if (trigger) trigger.style.display = "flex";
            }

            <?php if ( $is_trigger ) : ?>
            if (trigger) {
                trigger.addEventListener("click", function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    showPanel();
                });
            }
            <?php if ( $close_outside ) : ?>
            document.addEventListener("click", function(e) {
                if (panel.classList.contains("olo-fp-hidden")) return;
                if (panel.contains(e.target)) return;
                if (trigger) {
                    if (trigger.contains(e.target)) return;
                }
                hidePanel();
            });
            <?php endif; ?>
            <?php endif; ?>

            // Close button (works in both 'always' and 'button' trigger modes)
            var closeBtn = panel.querySelector('[data-olo-fp-close="<?php echo $uid; ?>"]');
            if (closeBtn) {
                closeBtn.addEventListener("click", function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    hidePanel();
                });
            }
        })();
        </script>
        </div><!-- .olo-fp-wrapper -->
        <?php
        return ob_get_clean();
    }

    /**
     * Get SVG icon for the trigger button.
     */
    private function get_trigger_icon( $icon, $color ) {
        $icons = [
            'plus'     => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>',
            'chat'     => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
            'info'     => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
            'menu'     => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>',
            'arrow-up' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>',
            'star'     => '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
            'heart'    => '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>',
            'settings' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>',
        ];
        return $icons[ $icon ] ?? $icons['plus'];
    }
}
