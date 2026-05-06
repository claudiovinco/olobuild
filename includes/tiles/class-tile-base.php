<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

abstract class Olo_Tile_Base {

    protected $type     = '';
    protected $name     = '';
    protected $icon     = '';
    protected $category = 'general';
    protected $defaults = [];

    /** Tracks whether the delegated-events footer script has been enqueued. */
    private static $delegated_events_enqueued = false;

    public function get_type() {
        return $this->type;
    }

    public function get_name() {
        return $this->name;
    }

    public function get_icon() {
        return $this->icon;
    }

    public function get_category() {
        return $this->category;
    }

    public function get_defaults() {
        return $this->defaults;
    }

    /**
     * Returns control definitions for the tile settings panel.
     * Each control: [ 'key' => string, 'type' => string, 'label' => string, 'default' => mixed ]
     */
    abstract public function get_controls();

    /**
     * Renders the frontend HTML for this tile.
     */
    abstract public function render( $settings );

    /**
     * Build a CSS style string from key-value pairs, skipping empty values.
     * Prevents invalid CSS like "color: ;" when a value is empty.
     *
     * @param array $pairs [ 'property' => 'value', ... ]
     * @return string e.g. "color: #fff; background: #000"
     */
    protected function build_style( $pairs ) {
        $parts = [];
        foreach ( $pairs as $prop => $value ) {
            $v = trim( (string) $value );
            if ( $v !== '' ) {
                $parts[] = $prop . ': ' . esc_attr( $v );
            }
        }
        return implode( '; ', $parts );
    }

    /**
     * Return escaped color value or empty string.
     * Use in HTML attribute context (data-color="...").
     * For CSS inline context (style="color:...") use safe_color_css() instead.
     */
    protected function safe_color( $value ) {
        $v = trim( (string) $value );
        return $v !== '' ? esc_attr( $v ) : '';
    }

    /**
     * Validate and return a CSS-safe color value, or empty string.
     * Use inside style="" attributes and <style> blocks.
     * Prevents CSS injection by allowing only valid color formats.
     */
    protected function safe_color_css( $value ) {
        $v = trim( (string) $value );
        if ( $v === '' ) return '';
        // Allow: #hex, rgb(), rgba(), hsl(), hsla(), CSS variables, named colors, transparent/inherit/initial/currentColor
        if ( preg_match( '/^(#[0-9a-fA-F]{3,8}|rgba?\(\s*[\d\s,.%\/]+\)|hsla?\(\s*[\d\s,.%\/deg]+\)|var\(\s*--[\w-]+(?:\s*,\s*[^)]+)?\)|transparent|inherit|initial|currentColor|[a-zA-Z]{3,20})$/', $v ) ) {
            return $v;
        }
        return '';
    }

    /**
     * Sanitize rich text from TipTap editor.
     * Strips block-level tags (<p>), converts rgb() colors to hex
     * (WordPress safecss_filter_attr doesn't support rgb()), then sanitizes.
     */
    /**
     * Build a CSS border-radius string from the border_radius setting.
     * Handles both uniform (number/string) and per-corner (object) values.
     *
     * @param mixed $br  Border radius value — number, string, or array { tl, tr, br, bl }
     * @return string    CSS value like "8px" or "8px 0px 12px 4px", or empty string
     */
    /**
     * Normalizza un valore border (object JSON) in array PHP uniforme.
     * Accetta array associativi o scalari (legacy 0).
     * Restituisce array { top, right, bottom, left, style, color } oppure null se il bordo è vuoto.
     */
    protected function parse_border( $b ) {
        if ( ! is_array( $b ) ) return null;
        $color = trim( $b['color'] ?? '' );
        if ( $color === '' ) return null;
        $style = $b['style'] ?? 'solid';
        $top    = max( 0, intval( $b['top']    ?? 0 ) );
        $right  = max( 0, intval( $b['right']  ?? 0 ) );
        $bottom = max( 0, intval( $b['bottom'] ?? 0 ) );
        $left   = max( 0, intval( $b['left']   ?? 0 ) );
        if ( ! $top && ! $right && ! $bottom && ! $left ) return null;
        return compact( 'top', 'right', 'bottom', 'left', 'style', 'color' );
    }

    /**
     * Genera le proprietà CSS di bordo inline (senza selettore).
     * Restituisce stringa vuota se il bordo è inattivo.
     */
    protected function build_border_css( $b ) {
        $d = $this->parse_border( $b );
        if ( ! $d ) return '';
        ['top'=>$t,'right'=>$r,'bottom'=>$bo,'left'=>$l,'style'=>$s,'color'=>$c] = $d;
        if ( $t === $r && $r === $bo && $bo === $l ) {
            return "border:{$t}px {$s} {$c};";
        }
        $css = '';
        if ( $t  ) $css .= "border-top:{$t}px {$s} {$c};";
        if ( $r  ) $css .= "border-right:{$r}px {$s} {$c};";
        if ( $bo ) $css .= "border-bottom:{$bo}px {$s} {$c};";
        if ( $l  ) $css .= "border-left:{$l}px {$s} {$c};";
        return $css;
    }

    /**
     * Genera il blocco CSS hover + transizione per il bordo.
     * $uid   = selettore CSS univoco (es. ".olo-img-12345")
     * $base  = valore border base (array o null)
     * $hover = valore border hover (array)
     * $dur   = durata transizione in ms
     */
    protected function build_border_hover_css( $uid, $base, $hover, $dur = 300 ) {
        if ( ! is_array( $hover ) ) return '';

        $base_d  = $this->parse_border( $base );
        $h_color = trim( $hover['color'] ?? '' );
        $h_style = trim( $hover['style'] ?? '' );
        $h_top   = $hover['top']    !== '' ? max( 0, intval( $hover['top']    ?? 0 ) ) : null;
        $h_right = $hover['right']  !== '' ? max( 0, intval( $hover['right']  ?? 0 ) ) : null;
        $h_bot   = $hover['bottom'] !== '' ? max( 0, intval( $hover['bottom'] ?? 0 ) ) : null;
        $h_left  = $hover['left']   !== '' ? max( 0, intval( $hover['left']   ?? 0 ) ) : null;

        // Nulla da cambiare
        if ( $h_color === '' && $h_style === '' && $h_top === null && $h_right === null && $h_bot === null && $h_left === null ) {
            return '';
        }

        $dur_s  = max( 50, intval( $dur ) );
        $eff_c  = $h_color !== '' ? $h_color : ( $base_d['color'] ?? '' );
        $eff_s  = $h_style !== '' ? $h_style : ( $base_d['style'] ?? 'solid' );
        $eff_t  = $h_top   !== null ? $h_top   : ( $base_d['top']    ?? 0 );
        $eff_r  = $h_right !== null ? $h_right : ( $base_d['right']  ?? 0 );
        $eff_bo = $h_bot   !== null ? $h_bot   : ( $base_d['bottom'] ?? 0 );
        $eff_l  = $h_left  !== null ? $h_left  : ( $base_d['left']   ?? 0 );

        if ( ! $eff_c ) return '';

        $css = "{$uid}{transition:border {$dur_s}ms ease;}";
        if ( $eff_t === $eff_r && $eff_r === $eff_bo && $eff_bo === $eff_l ) {
            $css .= "{$uid}:hover{border:{$eff_t}px {$eff_s} {$eff_c};}";
        } else {
            $css .= "{$uid}:hover{";
            if ( $eff_t  ) $css .= "border-top:{$eff_t}px {$eff_s} {$eff_c};";
            if ( $eff_r  ) $css .= "border-right:{$eff_r}px {$eff_s} {$eff_c};";
            if ( $eff_bo ) $css .= "border-bottom:{$eff_bo}px {$eff_s} {$eff_c};";
            if ( $eff_l  ) $css .= "border-left:{$eff_l}px {$eff_s} {$eff_c};";
            $css .= '}';
        }
        return $css;
    }

    /**
     * Genera CSS per effetti bordo avanzati (neon, gradiente).
     * Restituisce stringa CSS (senza tag <style>) o ''.
     */
    protected function build_border_effect_css( $uid, $border, $settings ) {
        $effect = $settings['border_effect'] ?? 'none';
        if ( $effect === 'none' || $effect === '' ) return '';

        $d = $this->parse_border( $border );
        if ( ! $d && ! in_array( $effect, [ 'gradient', 'gradient-spin' ], true ) ) return '';

        $color1 = $d['color'] ?? '#6366f1';
        $color2 = trim( $settings['border_effect_color2'] ?? '' ) ?: '#ec4899';

        switch ( $effect ) {

            case 'neon':
                $levels = [
                    'subtle'  => [ 4, 8 ],
                    'medium'  => [ 6, 18 ],
                    'intense' => [ 10, 30 ],
                ];
                $intensity = $settings['border_effect_intensity'] ?? 'medium';
                [$a, $b] = $levels[ $intensity ] ?? $levels['medium'];
                $alpha = $this->hex_to_rgba( $color1, 0.45 );
                return "{$uid}{box-shadow:0 0 {$a}px {$color1},0 0 {$b}px {$color1},0 0 " . ($b*2) . "px {$alpha};}";

            case 'neon-pulse':
                $intensity = $settings['border_effect_intensity'] ?? 'medium';
                $anim_id   = 'olo-np-' . substr( md5( $uid . $color1 ), 0, 6 );
                $a1 = $this->hex_to_rgba( $color1, 0.5 );
                $a2 = $this->hex_to_rgba( $color1, 0.8 );
                switch ( $intensity ) {
                    case 'subtle':  [$s1,$s2,$s3,$s4] = [3,6,5,12]; break;
                    case 'intense': [$s1,$s2,$s3,$s4] = [8,20,14,40]; break;
                    default:        [$s1,$s2,$s3,$s4] = [5,12,8,24]; break;
                }
                return "@keyframes {$anim_id}{0%,100%{box-shadow:0 0 {$s1}px {$color1},0 0 {$s2}px {$a1};}50%{box-shadow:0 0 {$s3}px {$color1},0 0 {$s4}px {$a2},0 0 " . ($s4*2) . "px {$a1};}}" .
                       "{$uid}{animation:{$anim_id} 2s ease-in-out infinite;}";

            case 'gradient':
                $angle = intval( $settings['border_effect_angle'] ?? 135 );
                // border-image: no border-radius — nota documentata nel tooltip
                return "{$uid}{border-image:linear-gradient({$angle}deg,{$color1},{$color2}) 1;}";

            case 'gradient-spin':
                $speed  = max( 1, intval( $settings['border_effect_speed'] ?? 4 ) );
                $prop   = '--olo-ba-' . substr( md5( $uid ), 0, 6 );
                $anim_id = 'olo-bs-' . substr( md5( $uid ), 0, 6 );
                return "@property {$prop}{syntax:'<angle>';initial-value:0deg;inherits:false;}" .
                       "@keyframes {$anim_id}{to{{$prop}:360deg;}}" .
                       "{$uid}{border-image:conic-gradient(from var({$prop}),{$color1},{$color2},{$color1}) 1;" .
                       "animation:{$anim_id} {$speed}s linear infinite;}";
        }
        return '';
    }

    /** Converte un colore hex in rgba(r,g,b,alpha). */
    private function hex_to_rgba( $hex, $alpha ) {
        $hex = ltrim( $hex, '#' );
        if ( strlen( $hex ) === 3 ) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        if ( strlen( $hex ) !== 6 ) return "rgba(99,102,241,{$alpha})";
        $r = hexdec( substr( $hex, 0, 2 ) );
        $g = hexdec( substr( $hex, 2, 2 ) );
        $b = hexdec( substr( $hex, 4, 2 ) );
        return "rgba({$r},{$g},{$b},{$alpha})";
    }

    protected function build_border_radius_css( $br ) {
        if ( is_array( $br ) ) {
            $tl  = intval( $br['tl'] ?? 0 );
            $tr  = intval( $br['tr'] ?? 0 );
            $brr = intval( $br['br'] ?? 0 );
            $bl  = intval( $br['bl'] ?? 0 );
            if ( $tl || $tr || $brr || $bl ) {
                return "{$tl}px {$tr}px {$brr}px {$bl}px";
            }
            return '';
        }
        $n = intval( $br );
        return $n > 0 ? "{$n}px" : '';
    }

    /**
     * Text-effects helper: returns [$class_fragment, $data_attrs_string] for the given semantic target.
     * If the target receives the active effect, returns class like " olo-tfx olo-tfx--gradient-anim"
     * (with leading space) and data-attributes string ready to inline-echo on the element.
     * Otherwise returns ['', ''].
     *
     * Pass `$plain_text` (the inner text of the element) so glitch/scramble can mirror it via data-fx-text.
     *
     * Usage:
     *   list( $h_cls, $h_data ) = $this->tfx_attrs( $s, 'heading', $heading_text );
     *   echo "<h2 class=\"olo-heading{$h_cls}\"{$h_data}>{$heading_text}</h2>";
     *
     * The tile must also:
     *   - Emit CSS for CSS-driven effects via Olo_Text_Effects::css($s, $sel)  (or style_block)
     *   - Print the runtime script via Olo_Text_Effects::print_script() once per render
     *
     * `'all'` is a shorthand target that matches anything except an explicit other target — used by
     * tiles that expose multiple text fields and want one effect to apply to all of them at once.
     *
     * @param array  $s          Settings.
     * @param string $target     The element's semantic target (e.g. 'heading', 'text', 'title', 'subtitle').
     * @param string $plain_text Inner text content (for data-fx-text mirror).
     * @return array [$class_fragment_with_lead_space, $data_attrs_with_lead_space]
     */
    protected function tfx_attrs( $s, $target, $plain_text = '' ) {
        if ( ! class_exists( 'Olo_Text_Effects' ) ) return [ '', '' ];
        if ( ! Olo_Text_Effects::active( $s ) ) return [ '', '' ];
        $tgt = $s['text_effect_target'] ?? 'heading';
        $hits = ( $tgt === 'all' ) || ( $tgt === 'both' && in_array( $target, [ 'heading', 'text' ], true ) ) || ( $tgt === $target );
        if ( ! $hits ) return [ '', '' ];
        // Fake a settings array where target matches so the helper greenlights it
        $faux = $s;
        $faux['text_effect_target'] = $target;
        return [
            Olo_Text_Effects::classes( $faux, $target ),
            Olo_Text_Effects::data_attrs( $faux, $target, $plain_text ),
        ];
    }

    /**
     * Convenience: return the CSS block (without <style>) for the active text effect, scoped to $sel.
     */
    protected function tfx_css( $s, $sel ) {
        if ( ! class_exists( 'Olo_Text_Effects' ) ) return '';
        return Olo_Text_Effects::css( $s, $sel );
    }

    /**
     * Convenience: print the runtime script once per request.
     */
    protected function tfx_print_script() {
        if ( ! class_exists( 'Olo_Text_Effects' ) ) return;
        Olo_Text_Effects::print_script();
    }

    /**
     * Render an icon — supports both UIkit icons and custom SVG icons.
     * Custom icons are stored with prefix "custom:" and saved in olo_custom_icons option.
     *
     * @param string $icon_name  e.g. "star" or "custom:my-logo"
     * @param float  $ratio      UIkit icon ratio (default 1)
     * @param string $extra_attr Extra HTML attributes
     * @return string  HTML for the icon
     */
    protected function render_icon_html( $icon_name, $ratio = 1, $extra_attr = '' ) {
        if ( empty( $icon_name ) ) return '';
        if ( str_starts_with( $icon_name, 'custom:' ) ) {
            $name = substr( $icon_name, 7 );
            $icons = get_option( 'olo_custom_icons', [] );
            if ( isset( $icons[ $name ] ) ) {
                $size = round( 20 * $ratio );
                // Sanitize SVG output to prevent stored XSS
                $safe_svg = function_exists( 'olo_sanitize_svg' ) ? olo_sanitize_svg( $icons[ $name ] ) : wp_kses_post( $icons[ $name ] );
                return '<span class="olo-custom-icon" style="display:inline-flex;width:' . $size . 'px;height:' . $size . 'px;" ' . $extra_attr . '>' . $safe_svg . '</span>';
            }
            return '';
        }
        return '<span ' . $extra_attr . ' uk-icon="icon: ' . esc_attr( $icon_name ) . '; ratio: ' . esc_attr( $ratio ) . '"></span>';
    }

    /**
     * Wrap an image in a hover-media container for image swap / video on hover.
     *
     * @param string $img_html  The original <img> (or inner HTML).
     * @param string $hover_image  URL of the alternative hover image.
     * @param string $hover_video  URL of an mp4 video to play on hover.
     * @return string  Wrapped HTML, or original if no hover media.
     */
    protected function render_hover_wrap( $img_html, $hover_image = '', $hover_video = '' ) {
        if ( empty( $hover_image ) && empty( $hover_video ) ) {
            return $img_html;
        }

        $hover_el = '';
        if ( ! empty( $hover_video ) ) {
            $hover_el = '<video src="' . esc_url( $hover_video ) . '" muted loop playsinline preload="none"></video>';
        } elseif ( ! empty( $hover_image ) ) {
            $hover_el = '<img src="' . esc_url( $hover_image ) . '" alt="" loading="lazy" />';
        }

        $vid_attrs = '';
        if ( ! empty( $hover_video ) ) {
            $vid_attrs = ' data-olo-hover-video="1"';
            self::enqueue_delegated_events();
        }

        return '<div class="olo-hover-wrap"' . $vid_attrs . '>'
             . $img_html
             . '<div class="olo-hover-media">' . $hover_el . '</div>'
             . '</div>';
    }

    protected function sanitize_richtext( $html ) {
        // Strip block-level wrappers (TipTap may wrap in <p>)
        $html = preg_replace( '/<\/?p[^>]*>/', '', $html );
        // Convert rgb() to hex (WordPress CSS sanitizer only allows hex/named colors)
        $html = preg_replace_callback(
            '/rgb\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)/',
            function( $m ) { return sprintf( '#%02x%02x%02x', $m[1], $m[2], $m[3] ); },
            $html
        );
        return wp_kses_post( $html );
    }

    /**
     * Enqueue a single footer script that handles all delegated tile events.
     * Replaces inline onmouseenter/onmouseleave/onclick handlers with
     * data-attribute-based event delegation (CSP-friendly).
     */
    public static function enqueue_delegated_events() {
        if ( self::$delegated_events_enqueued ) {
            return;
        }
        self::$delegated_events_enqueued = true;
        add_action( 'wp_footer', [ __CLASS__, 'print_delegated_events_script' ], 99 );
    }

    /**
     * Print the delegated events script in the footer.
     */
    public static function print_delegated_events_script() {
        ?>
        <script>
        (function(){
            /* Hover video: play on mouseenter, pause+rewind on mouseleave */
            document.addEventListener('mouseenter',function(e){
                var el=e.target.closest('[data-olo-hover-video]');
                if(!el)return;
                var v=el.querySelector('.olo-hover-media video');
                if(v)v.play();
            },true);
            document.addEventListener('mouseleave',function(e){
                var el=e.target.closest('[data-olo-hover-video]');
                if(!el)return;
                var v=el.querySelector('.olo-hover-media video');
                if(v){v.pause();v.currentTime=0}
            },true);
            /* ServiceVideo play button */
            document.addEventListener('click',function(e){
                var btn=e.target.closest('[data-olo-svid-play]');
                if(!btn)return;
                var wrap=btn.parentElement;
                if(wrap){
                    wrap.classList.add('olo-svid-playing');
                    var v=wrap.querySelector('video');
                    if(v)v.play();
                }
            },false);
        })();
        </script>
        <?php
    }
}
