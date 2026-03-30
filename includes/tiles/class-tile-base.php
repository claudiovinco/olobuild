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
                return '<span class="olo-custom-icon" style="display:inline-flex;width:' . $size . 'px;height:' . $size . 'px;" ' . $extra_attr . '>' . $icons[ $name ] . '</span>';
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
