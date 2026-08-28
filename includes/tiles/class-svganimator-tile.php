<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Svganimator_Tile extends Olobuild_Tile_Base {

    protected $type     = 'svganimator';
    protected $name     = 'SVG Animator';
    protected $icon     = 'dashicons-art';
    protected $category = 'media';
    protected $defaults = [
        'source_type'         => 'upload',
        'svg_url'             => '',
        'svg_code'            => '',
        'anim_type'           => 'draw',
        'anim_sequence'       => 'delayed',
        'trigger'             => 'viewport',
        'duration'            => 1500,
        'delay'               => 0,
        'easing'              => 'ease',
        'easing_custom'       => '0.42, 0, 0.58, 1',
        'stagger_delay'       => 100,
        'stroke_width'        => '',
        'stroke_color'        => '',
        'stroke_linecap'      => '',
        'stroke_linejoin'     => '',
        'show_fill'           => true,
        'fill_color'          => '',
        'fill_delay'          => 300,
        'fill_duration'       => 500,
        'reverse'             => false,
        'erase_on_leave'      => false,
        'loop'                => false,
        'loop_pause'          => 500,
        'replay_button'       => false,
        'replay_button_label' => 'Replay',
        'max_width'           => '',
        'alignment'           => 'center',
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
        $s = wp_parse_args( $settings, $this->defaults );

        $uid = 'olo-svga-' . wp_rand( 10000, 99999 );

        // Get SVG content
        $svg_content = '';
        if ( $s['source_type'] === 'code' ) {
            $svg_content = $s['svg_code'];
        } elseif ( ! empty( $s['svg_url'] ) ) {
            // Try to get from attachment
            $att_id = absint( $s['svg_url_id'] ?? 0 );
            if ( $att_id > 0 ) {
                $file = get_attached_file( $att_id );
                if ( $file ) {
                    $svg_content = @file_get_contents( $file );
                }
            }
            if ( empty( $svg_content ) ) {
                // Use wp_remote_get instead of file_get_contents to prevent SSRF
                $url = esc_url_raw( $s['svg_url'] );
                if ( $url && wp_http_validate_url( $url ) ) {
                    $response = wp_remote_get( $url, [ 'timeout' => 10 ] );
                    if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
                        $svg_content = wp_remote_retrieve_body( $response );
                    }
                }
            }
        }

        if ( empty( $svg_content ) ) {
            return '<div style="padding:24px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);background:var(--olo-color-muted, #F3F4F6);border-radius:8px">'
                 . '<p style="font-size:1em;margin:0">' . esc_html( olobuild_t( 'Inserisci un file SVG per attivare l\'animazione.' ) ) . '</p>'
                 . '</div>';
        }

        // Sanitize SVG
        $svg_content = $this->sanitize_svg( $svg_content );

        if ( empty( $svg_content ) ) {
            return '<div style="padding:24px;text-align:center;color:#EF4444;background:#FEF2F2;border-radius:8px">'
                 . '<p style="font-size:1em;margin:0">SVG non valido o contenente codice non sicuro.</p>'
                 . '</div>';
        }

        // Build config JSON
        $easing = $s['easing'];
        if ( $easing === 'custom' ) {
            $easing = 'cubic-bezier(' . esc_attr( $s['easing_custom'] ) . ')';
        }

        $config = [
            'type'          => $s['anim_type'],
            'sequence'      => $s['anim_sequence'],
            'trigger'       => $s['trigger'],
            'duration'      => absint( $s['duration'] ),
            'delay'         => absint( $s['delay'] ),
            'easing'        => $easing,
            'stagger'       => absint( $s['stagger_delay'] ),
            'strokeWidth'   => $s['stroke_width'] !== '' ? floatval( $s['stroke_width'] ) : null,
            'strokeColor'   => $s['stroke_color'] ?: null,
            'strokeLinecap' => $s['stroke_linecap'] ?: null,
            'strokeLinejoin' => $s['stroke_linejoin'] ?: null,
            'showFill'      => ! empty( $s['show_fill'] ),
            'fillColor'     => $s['fill_color'] ?: null,
            'fillDelay'     => absint( $s['fill_delay'] ),
            'fillDuration'  => absint( $s['fill_duration'] ),
            'reverse'       => ! empty( $s['reverse'] ),
            'erase'         => ! empty( $s['erase_on_leave'] ),
            'loop'          => ! empty( $s['loop'] ),
            'loopPause'     => absint( $s['loop_pause'] ),
        ];

        // Remove null values
        $config = array_filter( $config, function( $v ) { return $v !== null; } );

        // Alignment
        $align_map = [ 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' ];
        $align_css = $align_map[ $s['alignment'] ] ?? 'center';
        $wrap_style = 'display:flex;justify-content:' . $align_css;

        $inner_style = 'display:inline-block;width:100%';
        if ( ! empty( $s['max_width'] ) ) {
            $mw = $s['max_width'];
            if ( is_numeric( $mw ) ) $mw .= 'px';
            $inner_style .= ';max-width:' . esc_attr( $mw );
        }

        ob_start();
        ?>
        <div class="olo-svga <?php echo esc_attr( $uid ); ?>" style="<?php echo esc_attr( $wrap_style ); ?>">
            <div class="olo-svga-wrap" style="<?php echo $inner_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed layout literals; the max-width value is esc_attr()'d at assignment above ?>" data-olo-svga='<?php echo esc_attr( wp_json_encode( $config ) ); ?>'>
                <?php echo $svg_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG markup sanitized via sanitize_svg() above (scripts, on* handlers, javascript: URIs, foreignObject and external use refs stripped) ?>
            </div>
            <?php if ( ! empty( $s['replay_button'] ) ) : ?>
                <button class="olo-svga-replay" onclick="this.parentElement.querySelector('[data-olo-svga]').__oloSvgaReplay()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                    <?php echo esc_html( $s['replay_button_label'] ?: 'Replay' ); ?>
                </button>
            <?php endif; ?>
        </div>
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

    /**
     * Sanitize SVG content — remove scripts, event handlers, dangerous elements
     */
    private function sanitize_svg( $svg ) {
        if ( empty( $svg ) ) return '';

        // Remove XML declaration and DOCTYPE
        $svg = preg_replace( '/<\?xml[^?]*\?>/i', '', $svg );
        $svg = preg_replace( '/<!DOCTYPE[^>]*>/i', '', $svg );

        // Remove script tags
        $svg = preg_replace( '/<script[\s\S]*?<\/script>/i', '', $svg );

        // Remove on* event attributes
        $svg = preg_replace( '/\s+on\w+\s*=\s*"[^"]*"/i', '', $svg );
        $svg = preg_replace( '/\s+on\w+\s*=\s*\'[^\']*\'/i', '', $svg );

        // Remove javascript: URIs
        $svg = preg_replace( '/href\s*=\s*"javascript:[^"]*"/i', '', $svg );
        $svg = preg_replace( '/href\s*=\s*\'javascript:[^\']*\'/i', '', $svg );

        // Remove foreignObject (can contain HTML)
        $svg = preg_replace( '/<foreignObject[\s\S]*?<\/foreignObject>/i', '', $svg );

        // Remove use with external references (potential SSRF)
        $svg = preg_replace( '/<use[^>]*xlink:href\s*=\s*"https?:[^"]*"[^>]*\/?>/i', '', $svg );

        // Ensure it contains an SVG tag
        if ( stripos( $svg, '<svg' ) === false ) return '';

        return trim( $svg );
    }
}
