<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Revealbox_Tile extends Olo_Tile_Base {

    protected $type     = 'revealbox';
    protected $name     = 'Reveal Box';
    protected $icon     = 'dashicons-arrow-up-alt';
    protected $category = 'general';

    protected $defaults = [
        'visible_height'         => '300',
        'top_image_url'          => '',
        'top_image_position'     => 'center center',
        'top_image_size'         => 'cover',
        'top_video_url'          => '',
        'bottom_image_url'       => '',
        'bottom_image_position'  => 'center center',
        'bottom_image_size'      => 'cover',
        'bottom_video_url'       => '',
        'top_content'            => '<h3>Titolo</h3>',
        'bottom_content'         => '<p>Contenuto rivelato al passaggio del mouse</p>',
        'top_icon'               => '',
        'top_icon_size'          => '2',
        'top_icon_color'         => '#ffffff',
        'bottom_icon'            => '',
        'bottom_icon_size'       => '2',
        'bottom_icon_color'      => '#ffffff',
        'reveal_effect'          => 'slide-up',
        'reveal_amount'          => '',
        'transition_speed'       => '0.5',
        'transition_easing'      => 'ease',
        'top_text_color'         => '#ffffff',
        'top_font_size'          => '',
        'bottom_text_color'      => '#ffffff',
        'bottom_font_size'       => '',
        'overlay_color'          => '#000000',
        'overlay_opacity'        => '0',
        'reveal_overlay_color'   => '#000000',
        'reveal_overlay_opacity' => '60',
        'text_color'             => '#ffffff',
        'top_align'              => 'flex-end',
        'top_justify'            => 'flex-start',
        'bottom_align'           => 'flex-start',
        'bottom_justify'         => 'flex-start',
        'top_padding'            => '24',
        'bottom_padding'         => '24',
        'border_radius'          => '0',
        'perspective'            => '800',
        // backward compat
        'image_url'              => '',
        'image_position'         => 'center center',
        'image_size'             => 'cover',
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        // image_url is now the global background (fixed behind both zones)
        // top_image_url / bottom_image_url are per-face backgrounds (scroll with face)

        $uid       = 'olo-rb-' . substr( md5( wp_json_encode( $s ) . wp_rand() ), 0, 8 );
        $effect    = sanitize_html_class( $s['reveal_effect'] );
        $h         = intval( $s['visible_height'] ) ?: 300;
        $reveal_h  = intval( $s['reveal_amount'] ) ?: $h;
        $speed     = floatval( $s['transition_speed'] ) ?: 0.5;
        $easing_map = [
            'ease'                        => 'ease',
            'ease-in-out'                 => 'ease-in-out',
            'ease-out'                    => 'ease-out',
            'cubic-bezier(0.4,0,0.2,1)'  => 'cubic-bezier(0.4,0,0.2,1)',
            'linear'                      => 'linear',
        ];
        $easing    = $easing_map[ $s['transition_easing'] ] ?? 'ease';
        $radius    = intval( $s['border_radius'] );
        $persp     = intval( $s['perspective'] ) ?: 800;

        $top_ov_op    = intval( $s['overlay_opacity'] );
        $bot_ov_op    = intval( $s['reveal_overlay_opacity'] );
        $top_pad      = intval( $s['top_padding'] );
        $bot_pad      = intval( $s['bottom_padding'] );

        $safe_text_color    = preg_replace( '/[^a-zA-Z0-9#\(\)\,\.\s\%]/', '', $s['text_color'] );
        $safe_top_text_clr  = preg_replace( '/[^a-zA-Z0-9#\(\)\,\.\s\%]/', '', $s['top_text_color'] ?: $s['text_color'] );
        $safe_bot_text_clr  = preg_replace( '/[^a-zA-Z0-9#\(\)\,\.\s\%]/', '', $s['bottom_text_color'] ?: $s['text_color'] );
        $top_font_size      = intval( $s['top_font_size'] );
        $bot_font_size      = intval( $s['bottom_font_size'] );
        $safe_overlay       = preg_replace( '/[^a-zA-Z0-9#\(\)\,\.\s\%]/', '', $s['overlay_color'] );
        $safe_reveal_ov     = preg_replace( '/[^a-zA-Z0-9#\(\)\,\.\s\%]/', '', $s['reveal_overlay_color'] );

        $is_slide = strpos( $effect, 'slide-' ) === 0;
        $is_flip  = strpos( $effect, 'flip-' ) === 0;
        $is_stack = in_array( $effect, [ 'fade', 'zoom-in', 'zoom-out', 'rotate-in' ], true );
        $is_horiz = $effect === 'slide-left' || $effect === 'slide-right';

        // ── Per-face BG helpers ──
        $top_bg  = $this->render_face_bg( $s['top_image_url'], $s['top_image_position'], $s['top_image_size'], $s['top_video_url'] );
        $bot_bg  = $this->render_face_bg( $s['bottom_image_url'], $s['bottom_image_position'], $s['bottom_image_size'], $s['bottom_video_url'] );

        // ── Overlay helpers ──
        $top_overlay = $top_ov_op > 0
            ? '<div style="position:absolute;inset:0;background:' . $safe_overlay . ';opacity:' . round( $top_ov_op / 100, 2 ) . ';z-index:1;pointer-events:none"></div>'
            : '';
        $bot_overlay = $bot_ov_op > 0
            ? '<div style="position:absolute;inset:0;background:' . $safe_reveal_ov . ';opacity:' . round( $bot_ov_op / 100, 2 ) . ';z-index:1;pointer-events:none"></div>'
            : '';

        // ── Icon helpers ──
        $top_icon_html = $this->render_icon( $s['top_icon'], $s['top_icon_size'], $s['top_icon_color'] );
        $bot_icon_html = $this->render_icon( $s['bottom_icon'], $s['bottom_icon_size'], $s['bottom_icon_color'] );

        // ── Face styles ──
        $face_base = "position:relative;display:flex;flex-direction:column;box-sizing:border-box";
        if ( $is_horiz ) {
            $face_dim = "width:50%;height:{$h}px;flex-shrink:0";
        } else {
            $face_dim = "width:100%;height:{$h}px";
        }

        $top_face_css = "{$face_base};{$face_dim};align-items:" . esc_attr( $s['top_justify'] ) . ";justify-content:" . esc_attr( $s['top_align'] );
        $bot_face_css = "{$face_base};{$face_dim};align-items:" . esc_attr( $s['bottom_justify'] ) . ";justify-content:" . esc_attr( $s['bottom_align'] );

        $top_content = wp_kses_post( $s['top_content'] );
        $bot_content = wp_kses_post( $s['bottom_content'] );

        $top_content_css = 'position:relative;z-index:2;padding:' . $top_pad . 'px;color:' . $safe_top_text_clr;
        if ( $top_font_size > 0 ) { $top_content_css .= ';font-size:' . $top_font_size . 'px'; }
        $bot_content_css = 'position:relative;z-index:2;padding:' . $bot_pad . 'px;color:' . $safe_bot_text_clr;
        if ( $bot_font_size > 0 ) { $bot_content_css .= ';font-size:' . $bot_font_size . 'px'; }

        $top_inner = $top_bg . $top_overlay . '<div style="' . $top_content_css . '">' . $top_icon_html . $top_content . '</div>';
        $bot_inner = $bot_bg . $bot_overlay . '<div style="' . $bot_content_css . '">' . $bot_icon_html . $bot_content . '</div>';

        // ── Build CSS for hover transitions (scoped to UID) ──
        $css = '';
        $container_css = "height:{$h}px;overflow:hidden;position:relative;color:{$safe_text_color}";
        if ( $radius > 0 ) {
            $container_css .= ";border-radius:{$radius}px";
        }

        if ( $is_slide ) {
            $slider_css = 'position:relative;z-index:1;transition:transform ' . $speed . 's ' . $easing;
            if ( $is_horiz ) {
                $slider_css .= ';display:flex;width:200%';
            }

            // NOTE: initial transforms go in CSS (not inline) so :hover can override them
            if ( $effect === 'slide-up' ) {
                $css .= "#{$uid} .olo-rb-slider{transform:translateY(0)}";
                $css .= "#{$uid}:hover .olo-rb-slider{transform:translateY(-{$reveal_h}px)}";
                $html_faces = '<div class="olo-rb-face" style="' . $top_face_css . '">' . $top_inner . '</div>'
                            . '<div class="olo-rb-face" style="' . $bot_face_css . '">' . $bot_inner . '</div>';
            } elseif ( $effect === 'slide-down' ) {
                $css .= "#{$uid} .olo-rb-slider{transform:translateY(-{$reveal_h}px)}";
                $css .= "#{$uid}:hover .olo-rb-slider{transform:translateY(0)}";
                $html_faces = '<div class="olo-rb-face" style="' . $bot_face_css . '">' . $bot_inner . '</div>'
                            . '<div class="olo-rb-face" style="' . $top_face_css . '">' . $top_inner . '</div>';
            } elseif ( $effect === 'slide-left' ) {
                $css .= "#{$uid} .olo-rb-slider{transform:translateX(0)}";
                $css .= "#{$uid}:hover .olo-rb-slider{transform:translateX(-50%)}";
                $html_faces = '<div class="olo-rb-face" style="' . $top_face_css . '">' . $top_inner . '</div>'
                            . '<div class="olo-rb-face" style="' . $bot_face_css . '">' . $bot_inner . '</div>';
            } else { // slide-right
                $css .= "#{$uid} .olo-rb-slider{transform:translateX(-50%)}";
                $css .= "#{$uid}:hover .olo-rb-slider{transform:translateX(0)}";
                $html_faces = '<div class="olo-rb-face" style="' . $bot_face_css . '">' . $bot_inner . '</div>'
                            . '<div class="olo-rb-face" style="' . $top_face_css . '">' . $top_inner . '</div>';
            }

            $inner_html = '<div class="olo-rb-slider" style="' . $slider_css . '">' . $html_faces . '</div>';

        } elseif ( $is_stack ) {
            $top_stack_css = $top_face_css . ';position:absolute;inset:0;z-index:2;transition:opacity ' . $speed . 's ' . $easing . ',transform ' . $speed . 's ' . $easing;
            $bot_stack_css = $bot_face_css . ';position:absolute;inset:0;z-index:1';

            $css .= "#{$uid}:hover .olo-rb-stack-top{opacity:0}";

            if ( $effect === 'zoom-in' ) {
                $css .= "#{$uid}:hover .olo-rb-stack-top{transform:scale(1.2)}";
            } elseif ( $effect === 'zoom-out' ) {
                $css .= "#{$uid}:hover .olo-rb-stack-top{transform:scale(0.8)}";
            } elseif ( $effect === 'rotate-in' ) {
                $css .= "#{$uid}:hover .olo-rb-stack-top{transform:rotate(15deg) scale(1.1)}";
            }

            $inner_html = '<div class="olo-rb-stack-top" style="' . $top_stack_css . '">' . $top_inner . '</div>'
                        . '<div class="olo-rb-stack-bottom" style="' . $bot_stack_css . '">' . $bot_inner . '</div>';

        } elseif ( $is_flip ) {
            $container_css .= ";perspective:{$persp}px";
            $flipper_css = 'position:relative;width:100%;height:100%;transition:transform ' . $speed . 's ' . $easing . ';transform-style:preserve-3d';
            $front_css = $top_face_css . ';position:absolute;inset:0;backface-visibility:hidden;z-index:2';
            $back_transform = $effect === 'flip-x' ? 'rotateY(180deg)' : 'rotateX(180deg)';
            $hover_transform = $effect === 'flip-x' ? 'rotateY(180deg)' : 'rotateX(180deg)';
            $back_css = $bot_face_css . ';position:absolute;inset:0;backface-visibility:hidden;transform:' . $back_transform;

            $css .= "#{$uid}:hover .olo-rb-flipper{transform:{$hover_transform}}";

            $inner_html = '<div class="olo-rb-flipper" style="' . $flipper_css . '">'
                        . '<div class="olo-rb-face" style="' . $front_css . '">' . $top_inner . '</div>'
                        . '<div class="olo-rb-face" style="' . $back_css . '">' . $bot_inner . '</div>'
                        . '</div>';
        } else {
            $inner_html = '';
        }

        ob_start();
        if ( $css ) {
            echo '<style>' . $css . '</style>';
        }
        // Global background image (behind everything)
        $global_bg = '';
        if ( ! empty( $s['image_url'] ) ) {
            $g_size = esc_attr( $s['image_size'] ?? 'cover' );
            $g_pos  = esc_attr( $s['image_position'] ?? 'center center' );
            $global_bg = '<div style="position:absolute;inset:0;background:url(' . esc_url( $s['image_url'] ) . ') ' . $g_pos . '/' . $g_size . ' no-repeat;z-index:0"></div>';
        }

        echo '<div id="' . esc_attr( $uid ) . '" class="olo-revealbox olo-reveal-' . $effect . '" style="' . $container_css . '">';
        echo $global_bg;
        echo $inner_html;
        echo '</div>';
        return ob_get_clean();
    }

    /**
     * Render background image or video for a face zone.
     */
    private function render_face_bg( $image_url, $image_position, $image_size, $video_url ) {
        if ( ! empty( $image_url ) ) {
            $bg_size = esc_attr( $image_size );
            $bg_pos  = esc_attr( $image_position );
            return '<div style="position:absolute;inset:0;background:url(' . esc_url( $image_url ) . ') ' . $bg_pos . '/' . $bg_size . ' no-repeat;z-index:0"></div>';
        }
        if ( ! empty( $video_url ) ) {
            return '<video style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;pointer-events:none;z-index:0" autoplay muted loop playsinline><source src="' . esc_url( $video_url ) . '" type="video/mp4"></video>';
        }
        return '';
    }

    /**
     * Render UIkit icon with size and color.
     */
    private function render_icon( $icon_name, $icon_size, $icon_color ) {
        if ( empty( $icon_name ) ) {
            return '';
        }
        $icon_name = sanitize_html_class( $icon_name );
        $size      = floatval( $icon_size ) ?: 2;
        $color     = preg_replace( '/[^a-zA-Z0-9#\(\)\,\.\s\%]/', '', $icon_color );
        return '<div style="line-height:1;margin-bottom:8px;color:' . $color . '"><span uk-icon="icon: ' . $icon_name . '; ratio: ' . $size . '"></span></div>';
    }
}
