<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Viewscounter_Tile extends Olo_Tile_Base {

    protected $type     = 'viewscounter';
    protected $name     = 'Contatore Visite';
    protected $icon     = 'dashicons-visibility';
    protected $category = 'dynamic';
    protected $defaults = [
        'preset' => 'custom',
        'show_icon'     => true,
        'icon_position' => 'before',
        'label'         => 'visualizzazioni',
        'show_label'    => true,
        'text_color'    => '',
        'icon_color'    => '',
        'font_size'     => '14',
        'font_weight'   => '400',
        'layout'        => 'inline',
        'icon_size'     => '16',
        'number_format' => true,
            'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    /** Common bot user-agent fragments. */
    private static $bot_patterns = [
        'bot', 'crawl', 'spider', 'slurp', 'mediapartners',
        'facebookexternalhit', 'bingpreview', 'yandex', 'baidu',
        'duckduckbot', 'twitterbot', 'applebot', 'semrush',
        'ahrefsbot', 'mj12bot', 'dotbot', 'petalbot',
        'bytespider', 'gptbot', 'claudebot',
    ];

    public function get_controls() { return []; }

    /**
     * Detect if the current user-agent is a known bot.
     */
    private function is_bot() {
        if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
            return true; // No UA = treat as bot
        }
        $ua = strtolower( $_SERVER['HTTP_USER_AGENT'] );
        foreach ( self::$bot_patterns as $pattern ) {
            if ( str_contains( $ua, $pattern ) ) {
                return true;
            }
        }
        return false;
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-vc-' . wp_rand( 10000, 99999 );

        $post_id = get_the_ID();

        // ── Increment views (frontend only) ──
        if ( $post_id ) {
            // Only increment if NOT admin, NOT AJAX, NOT REST, NOT bot
            $should_increment = true;

            if ( is_admin() ) {
                $should_increment = false;
            }
            if ( $should_increment ) {
                if ( wp_doing_ajax() ) {
                    $should_increment = false;
                }
            }
            if ( $should_increment ) {
                if ( defined( 'REST_REQUEST' ) ) {
                    if ( REST_REQUEST ) {
                        $should_increment = false;
                    }
                }
            }
            if ( $should_increment ) {
                if ( $this->is_bot() ) {
                    $should_increment = false;
                }
            }

            if ( $should_increment ) {
                $current = (int) get_post_meta( $post_id, '_olo_post_views', true );
                update_post_meta( $post_id, '_olo_post_views', $current + 1 );
            }
        }

        // ── Read view count ──
        $views = 0;
        if ( $post_id ) {
            $views = (int) get_post_meta( $post_id, '_olo_post_views', true );
        }

        // ── Settings ──
        $show_icon   = filter_var( $s['show_icon'], FILTER_VALIDATE_BOOLEAN );
        $show_label  = filter_var( $s['show_label'], FILTER_VALIDATE_BOOLEAN );
        $num_format  = filter_var( $s['number_format'], FILTER_VALIDATE_BOOLEAN );
        $icon_pos    = $s['icon_position'] === 'after' ? 'after' : 'before';
        $layout      = $s['layout'] === 'block' ? 'block' : 'inline';
        $label       = esc_html( $s['label'] ?: olo_t( 'visualizzazioni' ) );
        $text_color  = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text-soft, #6b7280)';
        $icon_color  = $this->safe_color_css( $s['icon_color'] ) ?: $text_color;
        $font_size   = absint( $s['font_size'] ) ?: 14;
        $font_weight = absint( $s['font_weight'] ) ?: 400;
        $icon_size   = absint( $s['icon_size'] ) ?: 16;

        $formatted = $num_format ? number_format_i18n( $views ) : $views;

        // Eye SVG
        $eye_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $icon_size . '" height="' . $icon_size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';

        $direction = $layout === 'block' ? 'column' : 'row';

        ob_start();
        ?>
        <div class="olo-viewscounter <?php echo esc_attr( $uid ); ?> olo-vc-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" style="display:flex; flex-direction:<?php echo $direction; ?>; align-items:center; gap:6px; font-size:<?php echo $font_size; ?>px; font-weight:<?php echo $font_weight; ?>; color:<?php echo $text_color; ?>;">
            <?php if ( $show_icon ) : ?>
                <?php if ( $icon_pos === 'before' ) : ?>
                    <span class="olo-vc-icon" style="display:inline-flex; align-items:center; color:<?php echo $icon_color; ?>;"><?php echo $eye_svg; ?></span>
                <?php endif; ?>
            <?php endif; ?>

            <span class="olo-vc-text">
                <strong><?php echo esc_html( $formatted ); ?></strong>
                <?php if ( $show_label ) : ?>
                    <?php list( $vcl_cls, $vcl_data ) = $this->tfx_attrs( $s, 'label', wp_strip_all_tags( $label ) ); ?>
                    <span class="olo-vc-label<?php echo $vcl_cls; ?>" style="margin-left:4px;"<?php echo $vcl_data; ?>><?php echo $label; ?></span>
                <?php endif; ?>
            </span>

            <?php if ( $show_icon ) : ?>
                <?php if ( $icon_pos === 'after' ) : ?>
                    <span class="olo-vc-icon" style="display:inline-flex; align-items:center; color:<?php echo $icon_color; ?>;"><?php echo $eye_svg; ?></span>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php
        $tfx_css = $this->tfx_css( $s, '.olo-vc-text' );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();
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
