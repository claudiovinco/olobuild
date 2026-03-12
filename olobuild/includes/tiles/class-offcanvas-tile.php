<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Offcanvas_Tile extends Olo_Tile_Base {

    protected $type     = 'offcanvas';
    protected $name     = 'Off-Canvas';
    protected $icon     = 'dashicons-slides';
    protected $category = 'interactive';
    protected $defaults = [
        'mode'                => 'right',
        'panel_width'         => '320',
        'panel_width_unit'    => 'px',
        'bg_color'            => '#1F2937',
        'text_color'          => '#FFFFFF',
        'backdrop_blur'       => false,
        'backdrop_blur_amount'=> '8',
        'overlay'             => true,
        'overlay_color'       => '#000000',
        'overlay_opacity'     => '50',
        'content_align'       => 'top',

        'template_id'         => 0,

        'show_logo'           => false,
        'logo_url'            => '',
        'logo_height'         => '36',

        'close_button'        => true,
        'close_style'         => 'x',
        'close_color'         => '#FFFFFF',
        'close_bg'            => '',
        'close_size'          => '28',
        'close_position'      => 'top-right',
        'close_offset_x'     => '16',
        'close_offset_y'     => '16',
        'close_border_radius' => '50',
        'close_outside'       => true,

        'show_trigger'        => true,
        'trigger_style'       => 'bars',
        'trigger_color'       => '',
        'trigger_size'        => '24',
        'trigger_bg'          => '',
        'trigger_position'    => 'inline',
        'trigger_offset_x'   => '16',
        'trigger_offset_y'   => '16',
        'trigger_border_radius' => '6',
        'trigger_selector'    => '',

        'transition'          => 'slide',
        'transition_duration' => '350',
        'stagger_items'       => false,
        'stagger_delay'       => '60',
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-oc-' . substr( md5( wp_json_encode( $s ) ), 0, 8 );

        $mode            = in_array( $s['mode'], [ 'left', 'right', 'fullscreen' ], true ) ? $s['mode'] : 'right';
        $is_fullscreen   = $mode === 'fullscreen';
        $transition      = in_array( $s['transition'], [ 'slide', 'push', 'fade' ], true ) ? $s['transition'] : 'slide';
        $duration        = max( 100, intval( $s['transition_duration'] ) );
        $dur_s           = round( $duration / 1000, 2 );

        // Colors
        $bg_color        = $this->safe_color_css( $s['bg_color'] ) ?: '#1F2937';
        $text_color      = $this->safe_color_css( $s['text_color'] ) ?: '#FFFFFF';
        $close_color     = $this->safe_color_css( $s['close_color'] ) ?: '#FFFFFF';
        $close_size      = max( 16, intval( $s['close_size'] ) );
        $trigger_color   = $this->safe_color_css( $s['trigger_color'] ) ?: 'currentColor';
        $trigger_size    = max( 16, intval( $s['trigger_size'] ) );

        // Overlay
        $has_overlay     = ! empty( $s['overlay'] );
        $ov_color        = $this->safe_color_css( $s['overlay_color'] ) ?: '#000000';
        $ov_opacity      = max( 0, min( 100, intval( $s['overlay_opacity'] ) ) ) / 100;

        // Backdrop blur
        $has_blur        = ! empty( $s['backdrop_blur'] );
        $blur_amount     = max( 2, intval( $s['backdrop_blur_amount'] ) );

        // Panel size
        if ( $is_fullscreen ) {
            $panel_css_size = 'width:100%;height:100%;';
        } else {
            $w    = max( 200, intval( $s['panel_width'] ) );
            $unit = in_array( $s['panel_width_unit'], [ 'px', '%', 'vw' ], true ) ? $s['panel_width_unit'] : 'px';
            $panel_css_size = "width:{$w}{$unit};max-width:100vw;height:100%;";
        }

        // Panel position
        $panel_pos = $mode === 'left' ? 'top:0;left:0;' : 'top:0;right:0;';
        if ( $is_fullscreen ) {
            $panel_pos = 'top:0;left:0;';
        }

        // Transform (hidden state)
        if ( $transition === 'fade' || $is_fullscreen ) {
            $hide_transform = $is_fullscreen ? 'translateY(-20px)' : 'none';
            $hide_opacity   = '0';
            $show_opacity   = '1';
        } else {
            $hide_opacity   = '1';
            $show_opacity   = '1';
            if ( $mode === 'left' ) {
                $hide_transform = 'translateX(-100%)';
            } else {
                $hide_transform = 'translateX(100%)';
            }
        }

        // Content align
        $align_map = [
            'top'     => 'flex-start',
            'center'  => 'center',
            'bottom'  => 'flex-end',
            'between' => 'space-between',
        ];
        $justify = $align_map[ $s['content_align'] ] ?? 'flex-start';

        // Logo
        $show_logo  = ! empty( $s['show_logo'] ) && ! empty( $s['logo_url'] );
        $logo_h     = max( 20, intval( $s['logo_height'] ) );

        // Stagger
        $stagger    = ! empty( $s['stagger_items'] );
        $stagger_d  = max( 20, intval( $s['stagger_delay'] ) );

        // Template content
        $content_html = '';
        $template_id  = intval( $s['template_id'] );
        if ( $template_id > 0 ) {
            $renderer     = new Olo_Frontend_Renderer();
            $content_html = $renderer->render_shortcode( [ 'id' => $template_id ] );
            if ( empty( $content_html ) || strpos( $content_html, '<!-- Olobuilder' ) === 0 ) {
                $content_html = '';
            }
        }

        // Trigger icon SVG
        $trigger_svg = $this->get_trigger_svg( $s['trigger_style'], $trigger_size, $trigger_color );

        // Push mode size for body transform
        $push_size = '';
        if ( $transition === 'push' && ! $is_fullscreen ) {
            $w    = max( 200, intval( $s['panel_width'] ) );
            $unit = in_array( $s['panel_width_unit'], [ 'px', '%', 'vw' ], true ) ? $s['panel_width_unit'] : 'px';
            $dir  = $mode === 'left' ? '' : '-';
            $push_size = "translateX({$dir}{$w}{$unit})";
        }

        ob_start();
        ?>
        <style>
        .<?php echo $uid; ?>-overlay {
            position:fixed;inset:0;z-index:10099;
            background:<?php echo esc_attr( $ov_color ); ?>;
            opacity:0;pointer-events:none;
            transition:opacity <?php echo $dur_s; ?>s ease;
            <?php if ( $has_blur ) : ?>
            backdrop-filter:blur(0px);-webkit-backdrop-filter:blur(0px);
            <?php endif; ?>
        }
        .<?php echo $uid; ?>-overlay.olo-oc-vis {
            opacity:<?php echo $ov_opacity; ?>;pointer-events:auto;
            <?php if ( $has_blur ) : ?>
            backdrop-filter:blur(<?php echo $blur_amount; ?>px);-webkit-backdrop-filter:blur(<?php echo $blur_amount; ?>px);
            <?php endif; ?>
        }
        .<?php echo $uid; ?>-panel {
            position:fixed;<?php echo $panel_pos; ?>
            <?php echo $panel_css_size; ?>
            background:<?php echo esc_attr( $bg_color ); ?>;
            color:<?php echo esc_attr( $text_color ); ?>;
            z-index:10100;
            display:flex;flex-direction:column;
            justify-content:<?php echo $justify; ?>;
            overflow-y:auto;-webkit-overflow-scrolling:touch;
            box-sizing:border-box;
            <?php if ( $transition === 'fade' || $is_fullscreen ) : ?>
            opacity:0;pointer-events:none;
            transform:<?php echo $hide_transform; ?>;
            transition:opacity <?php echo $dur_s; ?>s ease, transform <?php echo $dur_s; ?>s ease;
            <?php else : ?>
            transform:<?php echo $hide_transform; ?>;
            transition:transform <?php echo $dur_s; ?>s cubic-bezier(.4,0,.2,1);
            <?php endif; ?>
        }
        .<?php echo $uid; ?>-panel.olo-oc-vis {
            <?php if ( $transition === 'fade' || $is_fullscreen ) : ?>
            opacity:1;pointer-events:auto;transform:translate(0,0);
            <?php else : ?>
            transform:translate(0,0);
            <?php endif; ?>
        }
        <?php if ( $transition === 'push' && $push_size ) : ?>
        body.<?php echo $uid; ?>-push {
            transition:transform <?php echo $dur_s; ?>s cubic-bezier(.4,0,.2,1);
            transform:<?php echo $push_size; ?>;overflow-x:hidden;
        }
        <?php endif; ?>
        .<?php echo $uid; ?>-close {
            position:absolute;z-index:2;
            border:none;cursor:pointer;line-height:1;
            display:inline-flex;align-items:center;justify-content:center;gap:4px;
            color:<?php echo esc_attr( $close_color ); ?>;
            transition:opacity .2s, transform .2s;
            <?php
            $cl_bg     = $this->safe_color_css( $s['close_bg'] ?? '' );
            $cl_ox     = max( 4, intval( $s['close_offset_x'] ) );
            $cl_oy     = max( 4, intval( $s['close_offset_y'] ) );
            $cl_pos    = $s['close_position'] ?? 'top-right';
            $cl_radius = max( 0, intval( $s['close_border_radius'] ) );
            // Position
            if ( strpos( $cl_pos, 'top' ) !== false )    echo "top:{$cl_oy}px;";
            if ( strpos( $cl_pos, 'bottom' ) !== false ) echo "bottom:{$cl_oy}px;";
            if ( strpos( $cl_pos, 'right' ) !== false )  echo "right:{$cl_ox}px;";
            if ( strpos( $cl_pos, 'left' ) !== false )   echo "left:{$cl_ox}px;";
            if ( $cl_pos === 'top-center' ) echo "left:50%;transform:translateX(-50%);right:auto;";
            // Background
            if ( $cl_bg ) {
                echo "background:{$cl_bg};padding:8px;border-radius:{$cl_radius}%;";
            } else {
                echo 'background:none;padding:4px;';
            }
            ?>
        }
        .<?php echo $uid; ?>-close:hover { opacity:.7;transform:rotate(90deg); }
        <?php if ( $cl_pos === 'top-center' ) : ?>
        .<?php echo $uid; ?>-close:hover { opacity:.7;transform:translateX(-50%) rotate(90deg); }
        <?php endif; ?>
        .<?php echo $uid; ?>-trigger {
            display:inline-flex;align-items:center;justify-content:center;
            border:none;cursor:pointer;padding:<?php echo ! empty( $s['trigger_bg'] ) ? '10px' : '6px'; ?>;
            color:<?php echo esc_attr( $trigger_color ); ?>;
            line-height:1;
            <?php
            $t_bg = $this->safe_color_css( $s['trigger_bg'] ?? '' );
            $t_pos = $s['trigger_position'] ?? 'inline';
            $t_radius = max( 0, intval( $s['trigger_border_radius'] ) );
            if ( $t_bg ) {
                echo "background:{$t_bg};border-radius:{$t_radius}px;";
            } else {
                echo 'background:none;';
            }
            if ( $t_pos !== 'inline' ) {
                $t_ox = max( 0, intval( $s['trigger_offset_x'] ) );
                $t_oy = max( 0, intval( $s['trigger_offset_y'] ) );
                echo 'position:fixed;z-index:10101;';
                if ( strpos( $t_pos, 'top' ) !== false )    echo "top:{$t_oy}px;";
                if ( strpos( $t_pos, 'bottom' ) !== false ) echo "bottom:{$t_oy}px;";
                if ( strpos( $t_pos, 'left' ) !== false )   echo "left:{$t_ox}px;";
                if ( strpos( $t_pos, 'right' ) !== false )  echo "right:{$t_ox}px;";
            }
            ?>
        }
        .<?php echo $uid; ?>-trigger:hover { opacity:.7; }
        .<?php echo $uid; ?>-trigger { transition:opacity .25s ease; }
        <?php if ( $show_logo ) : ?>
        .<?php echo $uid; ?>-logo {
            padding:20px 24px 0;
        }
        .<?php echo $uid; ?>-logo img {
            height:<?php echo $logo_h; ?>px;display:block;object-fit:contain;
        }
        <?php endif; ?>
        .<?php echo $uid; ?>-content {
            flex:1;display:flex;flex-direction:column;
            justify-content:<?php echo $justify; ?>;
        }
        <?php if ( $stagger ) : ?>
        .<?php echo $uid; ?>-panel.olo-oc-vis .<?php echo $uid; ?>-content > .olo-frontend-tile,
        .<?php echo $uid; ?>-panel.olo-oc-vis .<?php echo $uid; ?>-content > section {
            animation: oloOcStagger <?php echo max( 200, $duration ); ?>ms ease both;
        }
        <?php for ( $i = 1; $i <= 20; $i++ ) : ?>
        .<?php echo $uid; ?>-panel.olo-oc-vis .<?php echo $uid; ?>-content > :nth-child(<?php echo $i; ?>) {
            animation-delay: <?php echo $i * $stagger_d; ?>ms;
        }
        <?php endfor; ?>
        @keyframes oloOcStagger {
            from { opacity:0; transform:translateY(12px); }
            to   { opacity:1; transform:translateY(0); }
        }
        <?php endif; ?>
        </style>

        <?php if ( ! empty( $s['show_trigger'] ) ) : ?>
        <button type="button" class="<?php echo $uid; ?>-trigger" data-olo-oc-open="<?php echo $uid; ?>" aria-label="Apri menu">
            <?php echo $trigger_svg; ?>
        </button>
        <?php endif; ?>

        <?php if ( $has_overlay || $has_blur ) : ?>
        <div class="<?php echo $uid; ?>-overlay" data-olo-oc-overlay="<?php echo $uid; ?>"></div>
        <?php endif; ?>

        <div class="<?php echo $uid; ?>-panel" data-olo-oc-panel="<?php echo $uid; ?>" aria-hidden="true">
            <?php if ( ! empty( $s['close_button'] ) ) : ?>
            <button type="button" class="<?php echo $uid; ?>-close" data-olo-oc-close="<?php echo $uid; ?>" aria-label="Chiudi menu">
                <?php echo $this->get_close_svg( $s['close_style'] ?? 'x', $close_size, $close_color ); ?>
            </button>
            <?php endif; ?>

            <?php if ( $show_logo ) : ?>
            <div class="<?php echo $uid; ?>-logo">
                <img src="<?php echo esc_url( $s['logo_url'] ); ?>" alt="Logo" loading="lazy" />
            </div>
            <?php endif; ?>

            <div class="<?php echo $uid; ?>-content">
                <?php echo $content_html; ?>
            </div>
        </div>

        <script>
        (function(){
            var uid='<?php echo esc_js( $uid ); ?>';
            var panel=document.querySelector('[data-olo-oc-panel="'+uid+'"]');
            var overlay=document.querySelector('[data-olo-oc-overlay="'+uid+'"]');
            var trigger=document.querySelector('[data-olo-oc-open="'+uid+'"]');
            var pushCls='<?php echo esc_js( $uid ); ?>-push';
            var push=<?php echo ( $transition === 'push' && $push_size ) ? 'true' : 'false'; ?>;
            var closeOut=<?php echo ! empty( $s['close_outside'] ) ? 'true' : 'false'; ?>;
            if(!panel)return;

            function open(){
                panel.classList.add('olo-oc-vis');
                panel.setAttribute('aria-hidden','false');
                if(overlay)overlay.classList.add('olo-oc-vis');
                if(trigger)trigger.style.opacity='0';
                if(trigger)trigger.style.pointerEvents='none';
                if(push)document.body.classList.add(pushCls);
                document.body.style.overflow='hidden';
            }
            function close(){
                panel.classList.remove('olo-oc-vis');
                panel.setAttribute('aria-hidden','true');
                if(overlay)overlay.classList.remove('olo-oc-vis');
                if(trigger)trigger.style.opacity='';
                if(trigger)trigger.style.pointerEvents='';
                if(push)document.body.classList.remove(pushCls);
                document.body.style.overflow='';
            }

            document.addEventListener('click',function(e){
                if(e.target.closest('[data-olo-oc-open="'+uid+'"]')){open();return}
                if(e.target.closest('[data-olo-oc-close="'+uid+'"]')){close();return}
            });

            if(closeOut){
                if(overlay){overlay.addEventListener('click',close)}
            }

            document.addEventListener('keydown',function(e){
                if(e.key==='Escape'){
                    if(panel.classList.contains('olo-oc-vis'))close()
                }
            });

            <?php
            $ext = trim( $s['trigger_selector'] );
            if ( $ext !== '' ) :
            ?>
            document.querySelectorAll('<?php echo esc_js( $ext ); ?>').forEach(function(el){
                el.addEventListener('click',function(e){e.preventDefault();open()})
            });
            <?php endif; ?>
        })();
        </script>
        <?php

        return ob_get_clean();
    }

    /**
     * Generate trigger icon SVG.
     */
    private function get_trigger_svg( $style, $size, $color ) {
        $s = intval( $size );
        $c = esc_attr( $color );
        switch ( $style ) {
            case 'dots':
                return '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="' . $c . '"><circle cx="12" cy="5" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="12" cy="19" r="2"/></svg>';
            case 'grid':
                return '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="' . $c . '">'
                    . '<rect x="3" y="3" width="4" height="4" rx="1"/><rect x="10" y="3" width="4" height="4" rx="1"/><rect x="17" y="3" width="4" height="4" rx="1"/>'
                    . '<rect x="3" y="10" width="4" height="4" rx="1"/><rect x="10" y="10" width="4" height="4" rx="1"/><rect x="17" y="10" width="4" height="4" rx="1"/>'
                    . '<rect x="3" y="17" width="4" height="4" rx="1"/><rect x="10" y="17" width="4" height="4" rx="1"/><rect x="17" y="17" width="4" height="4" rx="1"/></svg>';
            case 'arrow':
                return '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="none" stroke="' . $c . '" stroke-width="2" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>';
            default: // bars
                return '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="none" stroke="' . $c . '" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>';
        }
    }

    /**
     * Generate close button icon.
     */
    private function get_close_svg( $style, $size, $color ) {
        $s = intval( $size );
        $c = esc_attr( $color );
        switch ( $style ) {
            case 'x-circle':
                return '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="none" stroke="' . $c . '" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="8" y1="8" x2="16" y2="16"/><line x1="16" y1="8" x2="8" y2="16"/></svg>';
            case 'arrow-left':
                return '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="none" stroke="' . $c . '" stroke-width="2" stroke-linecap="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>';
            case 'arrow-right':
                return '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="none" stroke="' . $c . '" stroke-width="2" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>';
            case 'chevron-left':
                return '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="none" stroke="' . $c . '" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>';
            case 'chevron-right':
                return '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="none" stroke="' . $c . '" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 6 15 12 9 18"/></svg>';
            case 'text':
                return '<span style="font-size:' . max( 10, $s - 14 ) . 'px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase">Chiudi</span>';
            default: // x
                return '<svg width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="none" stroke="' . $c . '" stroke-width="2" stroke-linecap="round"><line x1="6" y1="6" x2="18" y2="18"/><line x1="18" y1="6" x2="6" y2="18"/></svg>';
        }
    }
}
