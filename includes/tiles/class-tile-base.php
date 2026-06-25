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
    /**
     * Render del tile.
     *
     * @param array $settings Settings del tile (tile.settings).
     *
     * Nota: dal v3.18+, il frontend renderer passa anche `$style` (tile.style)
     * come secondo argomento. I tile che vogliono usare `style.bg` come fallback
     * al loro `settings.bg_*` (es. iconbox, hero) devono dichiarare l'override:
     *   public function render( $settings, $style = [] ) { ... }
     * I tile che ignorano il secondo argomento (la maggioranza) funzionano
     * identici — PHP scarta silenziosamente i parametri extra.
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
     * Sanitize rich-text HTML for safe output preserving inline color styles.
     * WordPress `safecss_filter_attr` (used inside `wp_kses_post`) rejects
     * `color: rgb(r,g,b)` because its regex disallows commas in values —
     * Tiptap's parseHTML fallback can emit `style="color: rgb(...)"` when
     * the browser normalizes the inline color, so we convert any rgb/rgba
     * in CSS color properties to #hex (with alpha as 8-digit hex if needed)
     * BEFORE handing the string to wp_kses_post.
     */
    protected function safe_richtext_content( $html ) {
        if ( empty( $html ) || ! is_string( $html ) ) return '';
        $html = preg_replace_callback(
            '/(color|background-color|border-color|outline-color|text-decoration-color|caret-color|column-rule-color)\s*:\s*rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)(?:\s*,\s*([\d.]+))?\s*\)/i',
            function( $m ) {
                $r = max( 0, min( 255, intval( $m[2] ) ) );
                $g = max( 0, min( 255, intval( $m[3] ) ) );
                $b = max( 0, min( 255, intval( $m[4] ) ) );
                if ( isset( $m[5] ) && $m[5] !== '' ) {
                    $a = max( 0.0, min( 1.0, floatval( $m[5] ) ) );
                    if ( $a < 1.0 ) {
                        $a_int = (int) round( $a * 255 );
                        return $m[1] . ': ' . sprintf( '#%02x%02x%02x%02x', $r, $g, $b, $a_int );
                    }
                }
                return $m[1] . ': ' . sprintf( '#%02x%02x%02x', $r, $g, $b );
            },
            $html
        );
        return wp_kses_post( $html );
    }

    /**
     * Validate and return a CSS-safe color value, or empty string.
     * Use inside style="" attributes and <style> blocks.
     * Prevents CSS injection by allowing only valid color formats.
     */
    protected function safe_color_css( $value ) {
        $v = trim( (string) $value );
        if ( $v === '' ) return '';
        // Allow: #hex, rgb(), rgba(), hsl(), hsla(), CSS variables, color-mix(), named colors, transparent/inherit/initial/currentColor
        if ( preg_match( '/^(#[0-9a-fA-F]{3,8}|rgba?\(\s*[\d\s,.%\/]+\)|hsla?\(\s*[\d\s,.%\/deg]+\)|var\(\s*--[\w-]+(?:\s*,\s*[^)]+)?\)|color-mix\([^;{}<>]*\)|transparent|inherit|initial|currentColor|[a-zA-Z]{3,20})$/', $v ) ) {
            return $v;
        }
        return '';
    }

    /**
     * Risolve il valore di un campo "famiglia font" in CSS pronto.
     * Gemello PHP di resolveFontFamily (oloTileDefaults.js) — UNICA mappa dei
     * valori-ruolo legacy ('serif','sans','mono','heading','body') salvati
     * dalle vecchie select per-tile. Il formato nuovo (type 'font') salva CSS
     * pronto: var(--olo-font-family-…) per i ruoli, "'Poppins', sans-serif"
     * per font specifici, '' = eredita.
     *
     * @param string $value      Valore salvato.
     * @param array  $legacy_map Mappa per-tile opzionale che sovrascrive i ruoli
     *                           legacy con gli stack storici della tile.
     * @return string font-family CSS-safe, '' se da ereditare.
     */
    protected function resolve_font_family( $value, $legacy_map = [] ) {
        $v = trim( (string) $value );
        if ( $v === '' ) return '';
        if ( $v === 'inherit' ) return 'inherit';
        if ( ! empty( $legacy_map[ $v ] ) ) return $legacy_map[ $v ];
        static $roles = [
            'body'       => "var(--olo-font-family, 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif)",
            'sans'       => "var(--olo-font-family, 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif)",
            'sans-serif' => "var(--olo-font-family, 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif)",
            'heading'    => "var(--olo-font-family-heading, Georgia, 'Times New Roman', serif)",
            'serif'      => "var(--olo-font-family-heading, 'Playfair Display', Georgia, serif)",
            'mono'       => "var(--olo-font-family-mono, ui-monospace, 'SF Mono', Menlo, Consolas, monospace)",
        ];
        if ( isset( $roles[ $v ] ) ) return $roles[ $v ];
        // CSS pronto: consenti solo caratteri leciti per un font-family
        // (lettere, cifre, spazi, virgole, apici, trattini, parentesi per var()).
        if ( preg_match( '/^[\w\s,\'"()\-]+$/u', $v ) && strpos( $v, '--' ) === false ) {
            return $v;
        }
        if ( preg_match( '/^var\(\s*--[\w-]+(?:\s*,\s*[^;{}<>]+)?\)$/', $v ) ) {
            return $v;
        }
        return '';
    }

    /**
     * Convert a color (hex, rgb, rgba) to "r,g,b" triplet for use inside rgba().
     * V3.26.0 — shared helper used by audacious preset extra CSS.
     *
     * @param string $color
     * @return string e.g. "255,106,42"
     */
    protected function color_to_rgb( $color ) {
        $color = trim( (string) $color );
        if ( preg_match( '/^#([0-9a-f]{3})$/i', $color, $m ) ) {
            $h = $m[1];
            $r = hexdec( $h[0] . $h[0] );
            $g = hexdec( $h[1] . $h[1] );
            $b = hexdec( $h[2] . $h[2] );
            return "{$r},{$g},{$b}";
        }
        if ( preg_match( '/^#([0-9a-f]{6})$/i', $color, $m ) ) {
            $h = $m[1];
            $r = hexdec( substr( $h, 0, 2 ) );
            $g = hexdec( substr( $h, 2, 2 ) );
            $b = hexdec( substr( $h, 4, 2 ) );
            return "{$r},{$g},{$b}";
        }
        if ( preg_match( '/rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/i', $color, $m ) ) {
            return "{$m[1]},{$m[2]},{$m[3]}";
        }
        return '128,128,128';
    }

    /**
     * Mix a color with white (lighten) or black (darken). Amount: 0..1.
     */
    protected function color_mix( $color, $amount, $with_white ) {
        $rgb_str = $this->color_to_rgb( $color );
        list( $r, $g, $b ) = array_map( 'intval', explode( ',', $rgb_str ) );
        if ( $with_white ) {
            $r = round( $r + ( 255 - $r ) * $amount );
            $g = round( $g + ( 255 - $g ) * $amount );
            $b = round( $b + ( 255 - $b ) * $amount );
        } else {
            $r = round( $r * ( 1 - $amount ) );
            $g = round( $g * ( 1 - $amount ) );
            $b = round( $b * ( 1 - $amount ) );
        }
        return sprintf( '#%02x%02x%02x', max( 0, min( 255, $r ) ), max( 0, min( 255, $g ) ), max( 0, min( 255, $b ) ) );
    }

    protected function color_lighten( $c, $a ) { return $this->color_mix( $c, $a, true ); }
    protected function color_darken( $c, $a )  { return $this->color_mix( $c, $a, false ); }

    /**
     * Sanitize a CSS selector to make a valid CSS identifier suffix
     * (used for keyframes and animation names tied to a preset).
     */
    protected function preset_uid( $sel ) {
        return preg_replace( '/[^a-z0-9-]/i', '', (string) $sel );
    }

    /**
     * Build CSS for the 11 "wow effects" controls shared via wowEffectsFields()
     * helper (defined in src/config/elements/_shared.js). Each tile that uses the
     * audacious presets includes this helper in its style fields and calls this
     * method during render to emit the corresponding CSS — backdrop-filter,
     * border-style, font-family, transform rotate/perspective/tilt, glow-pulse
     * keyframe animation, title text-shadow glow, scanlines via background-image.
     *
     * Replaces hardcoded `!important` CSS that previously lived in each tile's
     * get_preset_extra_css() block — so every effect is now driven by an
     * inspector control and can be tweaked or disabled after picking a preset.
     *
     * The `wow_disable` toggle short-circuits the entire output: the user can
     * keep a wow preset's colors/typography but strip the special effects.
     *
     * @param array  $s        Tile settings.
     * @param string $sel      Base CSS selector for the wrapper (e.g. "#tile-123").
     * @param string $title    CSS selector (within $sel) for the title element. Pass '' to skip title glow.
     * @return string CSS (no <style> wrapper) — empty when wow_disable is on.
     */
    protected function build_wow_effects_css( $s, $sel, $title = '' ) {
        if ( ! empty( $s['wow_disable'] ) ) return '';

        $blur     = max( 0, min( 40, intval( $s['wow_backdrop_blur'] ?? 0 ) ) );
        $sat      = max( 100, min( 200, intval( $s['wow_backdrop_saturate'] ?? 100 ) ) );
        $bstyle_a = [ 'solid', 'dashed', 'dotted', 'double' ];
        $bstyle   = in_array( $s['wow_border_style'] ?? 'solid', $bstyle_a, true ) ? $s['wow_border_style'] : 'solid';
        $font_map = [
            'inherit'   => 'inherit',
            'monospace' => 'ui-monospace, SFMono-Regular, Menlo, monospace',
            'serif'     => 'Georgia, "Times New Roman", serif',
            'sans'      => '"Helvetica Neue", Helvetica, Arial, sans-serif',
        ];
        $font_key = $s['wow_font_family'] ?? 'inherit';
        $font     = $font_map[ $font_key ] ?? 'inherit';
        $rot      = max( -10, min( 10, floatval( $s['wow_rotation'] ?? 0 ) ) );
        $persp    = max( 0, min( 2000, intval( $s['wow_perspective'] ?? 0 ) ) );
        $tilt_x   = max( -10, min( 10, floatval( $s['wow_tilt_x'] ?? 0 ) ) );
        $glow     = ! empty( $s['wow_glow_pulse'] );
        $t_glow   = ! empty( $s['wow_title_glow'] );
        $scan     = ! empty( $s['wow_scanlines'] );
        $speed    = absint( $s['effect_speed'] ?? 0 );
        $color_in = $s['effect_color'] ?? '';
        $color    = $color_in ? $this->safe_color_css( $color_in ) : '';
        $uid      = $this->preset_uid( $sel );

        $css = '';

        $main_props = [];
        if ( $blur > 0 || $sat > 100 ) {
            $main_props[] = "backdrop-filter: blur({$blur}px) saturate({$sat}%)";
            $main_props[] = "-webkit-backdrop-filter: blur({$blur}px) saturate({$sat}%)";
        }
        if ( $bstyle !== 'solid' ) {
            $main_props[] = "border-style: {$bstyle}";
        }
        if ( $font !== 'inherit' ) {
            $main_props[] = "font-family: {$font}";
        }
        $tf = [];
        if ( $persp > 0 )            $tf[] = "perspective({$persp}px)";
        if ( abs( $tilt_x ) > 0.01 ) $tf[] = "rotateX({$tilt_x}deg)";
        if ( abs( $rot ) > 0.01 )    $tf[] = "rotate({$rot}deg)";
        if ( $tf ) $main_props[] = 'transform: ' . implode( ' ', $tf );

        if ( $main_props ) {
            $css .= $sel . '{' . implode( ';', $main_props ) . ';}';
        }

        if ( $scan ) {
            $sc_c   = $color ?: '#00ff8c';
            $sc_rgb = $this->color_to_rgb( $sc_c );
            $css   .= $sel . "{background-image:repeating-linear-gradient(0deg,transparent 0,transparent 2px,rgba({$sc_rgb},0.06) 2px,rgba({$sc_rgb},0.06) 3px);}";
        }

        if ( $glow ) {
            $g_c   = $color ?: '#ff6a2a';
            $g_rgb = $this->color_to_rgb( $g_c );
            $pulse = $speed > 0 ? $speed : 2200;
            $kf    = "olo-wow-glow-{$uid}";
            $css  .= "@keyframes {$kf}{0%,100%{box-shadow:0 0 12px rgba({$g_rgb},0.5),inset 0 0 12px rgba({$g_rgb},0.15)}50%{box-shadow:0 0 24px rgba({$g_rgb},0.85),inset 0 0 24px rgba({$g_rgb},0.30)}}";
            $css  .= $sel . "{animation:{$kf} {$pulse}ms ease-in-out infinite;}";
        }

        if ( $t_glow && $title !== '' ) {
            $tg_c   = $color ?: '';
            $tg_c   = $tg_c ?: '#ff6a2a';
            $tg_rgb = $this->color_to_rgb( $tg_c );
            $css   .= $sel . ' ' . $title . "{text-shadow:0 0 8px rgba({$tg_rgb},0.6);}";
        }

        if ( ! empty( $s['wow_terminal_prompt'] ) && $title !== '' ) {
            $blink_ms = $speed > 0 ? $speed : 1000;
            $kf2      = "olo-wow-cursor-{$uid}";
            $css     .= "@keyframes {$kf2}{0%,49%{opacity:1}50%,100%{opacity:0}}";
            $css     .= $sel . ' ' . $title . "::before{content:'> ';opacity:0.7;}";
            $css     .= $sel . ' ' . $title . "::after{content:' \\2588';margin-left:2px;animation:{$kf2} {$blink_ms}ms steps(1) infinite;}";
        }

        return $css;
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
     * Media slot con sfondo/media di OGNI tipo — STESSO sistema del bg della tab Stile.
     * Dato un oggetto bg (type:'background': solid/gradient/pattern/image/video/gallery),
     * restituisce:
     *   - has    (bool)   : true se il bg è valorizzato (≠ none) → il chiamante lo usa al
     *                       posto del placeholder a righe / immagine semplice.
     *   - css    (string) : dichiarazioni CSS (solid/gradient/pattern/immagine) da mettere
     *                       nello style del media element.
     *   - markup (string) : HTML (video / slideshow gallery) da iniettare DENTRO il wrapper
     *                       del media (che deve essere position:relative; overflow:hidden).
     * Per i tipi solo-CSS markup è ''. $scope = classe univoca della tile (per lo scoping gallery).
     */
    protected function bg_media_parts( $bg, $scope = '' ) {
        $out = [ 'has' => false, 'css' => '', 'markup' => '' ];
        if ( is_array( $bg ) && ! empty( $bg['type'] ) && $bg['type'] !== 'none' && class_exists( 'Olo_CSS_Builder' ) ) {
            $cssb = new Olo_CSS_Builder();
            $out['css']    = (string) $cssb->get_bg_inline_css( $bg );
            $out['markup'] = (string) $cssb->get_bg_html_markup( $bg, $scope );
            $out['has']    = ( $out['css'] !== '' || $out['markup'] !== '' );
        }
        return $out;
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

        // Early-out: hover NON configurato (icona occhio mai aperta) →
        // tutti i lati 0 (o assenti) + style e color vuoti. Comportamento atteso:
        // al hover il bordo resta IDENTICO al base (niente decl :hover).
        $size_top    = intval( $hover['top']    ?? 0 );
        $size_right  = intval( $hover['right']  ?? 0 );
        $size_bottom = intval( $hover['bottom'] ?? 0 );
        $size_left   = intval( $hover['left']   ?? 0 );
        if ( $h_color === '' && $h_style === '' && $size_top === 0 && $size_right === 0 && $size_bottom === 0 && $size_left === 0 ) {
            return '';
        }

        // Per la logica downstream: distingue "non impostato" (assente o '') da "esplicito 0"
        $h_top   = ( ! array_key_exists( 'top',    $hover ) || $hover['top']    === '' ) ? null : max( 0, $size_top );
        $h_right = ( ! array_key_exists( 'right',  $hover ) || $hover['right']  === '' ) ? null : max( 0, $size_right );
        $h_bot   = ( ! array_key_exists( 'bottom', $hover ) || $hover['bottom'] === '' ) ? null : max( 0, $size_bottom );
        $h_left  = ( ! array_key_exists( 'left',   $hover ) || $hover['left']   === '' ) ? null : max( 0, $size_left );

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
     * Versione "decoupled" di build_border_hover_css: restituisce decl e transition
     * separate, così il caller può integrare la transition nella sua lista invece
     * di creare una regola CSS separata che sovrascriverebbe la transition base.
     *
     * Da preferire in tutti i NUOVI tile / refactor. Il vecchio metodo
     * `build_border_hover_css()` rimane per backward compat sui ~30 tile esistenti.
     *
     * @param mixed $base   Valore border base (array o null)
     * @param mixed $hover  Valore border hover (array)
     * @param int   $dur    Durata transizione in ms
     * @return array{ decls: string, transition: string }
     */
    protected function build_border_hover_props( $base, $hover, $dur = 300 ) {
        $empty = [ 'decls' => '', 'transition' => '' ];
        if ( ! is_array( $hover ) ) return $empty;

        $h_color = trim( $hover['color'] ?? '' );
        $h_style = trim( $hover['style'] ?? '' );
        $size_top    = intval( $hover['top']    ?? 0 );
        $size_right  = intval( $hover['right']  ?? 0 );
        $size_bottom = intval( $hover['bottom'] ?? 0 );
        $size_left   = intval( $hover['left']   ?? 0 );

        // Hover non configurato → niente decls, niente transition
        if ( $h_color === '' && $h_style === '' && $size_top === 0 && $size_right === 0 && $size_bottom === 0 && $size_left === 0 ) {
            return $empty;
        }

        $base_d = $this->parse_border( $base );
        $h_top   = ( ! array_key_exists( 'top',    $hover ) || $hover['top']    === '' ) ? null : max( 0, $size_top );
        $h_right = ( ! array_key_exists( 'right',  $hover ) || $hover['right']  === '' ) ? null : max( 0, $size_right );
        $h_bot   = ( ! array_key_exists( 'bottom', $hover ) || $hover['bottom'] === '' ) ? null : max( 0, $size_bottom );
        $h_left  = ( ! array_key_exists( 'left',   $hover ) || $hover['left']   === '' ) ? null : max( 0, $size_left );

        $eff_c  = $h_color !== '' ? $h_color : ( $base_d['color'] ?? '' );
        $eff_s  = $h_style !== '' ? $h_style : ( $base_d['style'] ?? 'solid' );
        $eff_t  = $h_top   !== null ? $h_top   : ( $base_d['top']    ?? 0 );
        $eff_r  = $h_right !== null ? $h_right : ( $base_d['right']  ?? 0 );
        $eff_bo = $h_bot   !== null ? $h_bot   : ( $base_d['bottom'] ?? 0 );
        $eff_l  = $h_left  !== null ? $h_left  : ( $base_d['left']   ?? 0 );

        if ( ! $eff_c ) return $empty;

        $decls = '';
        if ( $eff_t === $eff_r && $eff_r === $eff_bo && $eff_bo === $eff_l ) {
            $decls = "border:{$eff_t}px {$eff_s} {$eff_c};";
        } else {
            if ( $eff_t  ) $decls .= "border-top:{$eff_t}px {$eff_s} {$eff_c};";
            if ( $eff_r  ) $decls .= "border-right:{$eff_r}px {$eff_s} {$eff_c};";
            if ( $eff_bo ) $decls .= "border-bottom:{$eff_bo}px {$eff_s} {$eff_c};";
            if ( $eff_l  ) $decls .= "border-left:{$eff_l}px {$eff_s} {$eff_c};";
        }

        $dur_s = max( 50, intval( $dur ) );
        return [ 'decls' => $decls, 'transition' => "border {$dur_s}ms ease" ];
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

        $color1 = $d['color'] ?? '#e1474f';
        $color2 = trim( $settings['border_effect_color2'] ?? '' ) ?: '#f4a23b';

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
        if ( strlen( $hex ) !== 6 ) return "rgba(225,71,79,{$alpha})";
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
     * Genera dichiarazioni CSS inline da un oggetto style.bg (formato unificato
     * di BackgroundControls.vue) per i tile che vogliono usare style.bg come
     * fallback al loro settings.bg_* legacy (es. iconbox, hero).
     *
     * Gestisce solid/gradient/pattern (resi inline). I tipi image/video/gallery
     * sono ignorati perché vengono già renderizzati a livello wrapper .olo-tile
     * dal frontend renderer (uk-position-cover etc.) — replicare qui creerebbe
     * duplicazioni visibili.
     *
     * @param array $bg Oggetto style.bg con almeno 'type'.
     * @return string CSS inline (es. "background-color: #fff;") o '' se nessun bg.
     */
    protected function build_bg_css_from_style_bg( $bg ) {
        if ( ! is_array( $bg ) || empty( $bg['type'] ) || $bg['type'] === 'none' ) {
            return '';
        }
        if ( ! class_exists( 'Olo_Css_Builder' ) ) {
            return '';
        }
        // Solo solid/gradient/pattern qui: image/video/gallery vanno al wrapper.
        $type = $bg['type'];
        if ( $type !== 'solid' && $type !== 'gradient' && $type !== 'pattern' ) {
            return '';
        }
        static $css_builder = null;
        if ( $css_builder === null ) {
            $css_builder = new Olo_Css_Builder();
        }
        $decl = $css_builder->get_bg_inline_css( $bg );
        return $decl ? rtrim( $decl, ';' ) . ';' : '';
    }

    /**
     * Genera CSS :hover + transitions da una mappa dichiarativa di chiavi
     * settings → CSS property. Duale del helper JS `withHover()` in _shared.js.
     *
     * Per ogni voce della mappa legge `{key}_hover` (o opts.hover_key custom)
     * e `{key}_hover_duration` dai settings, e produce:
     *   - 'hover_decls'  → stringa con le dichiarazioni CSS per il blocco :hover
     *   - 'transitions'  → array di "css-prop {dur}ms ease" da concatenare
     *
     * Form della mappa:
     *   [ 'bg_color' => 'background-color' ]                                  (forma corta)
     *   [ 'bg_color' => [
     *         'css'        => 'background-color',
     *         'hover_key'  => 'hover_bg_color',     // override chiave
     *         'dur_key'    => 'hover_bg_duration',  // override chiave durata
     *         'important'  => true,                  // aggiunge !important
     *         'unit'       => 'px',                  // unit per valori numerici
     *     ]
     *   ]
     *
     * Tipi di valore auto-detected:
     *   - oggetto {tl,tr,br,bl} → "Xpx Ypx Zpx Wpx"  (border-radius)
     *   - oggetto {top,right,bottom,left} → "Xpx Ypx Zpx Wpx"  (spacing)
     *   - stringa con prop "*color*"/"background"/"fill"/"stroke" → safe_color_css
     *   - numero → "<n><unit>"
     *   - stringa generica → as-is
     *
     * @param array $s   Settings del tile.
     * @param array $map Mappa property hoverable → CSS.
     * @return array{ hover_decls: string, transitions: array }
     */
    protected function build_hover_css( $s, $map ) {
        $decls       = [];
        $transitions = [];

        foreach ( $map as $key => $cfg ) {
            $opts = is_array( $cfg ) ? $cfg : [ 'css' => $cfg ];
            $css_prop  = $opts['css'] ?? '';
            $hover_key = $opts['hover_key'] ?? ( $key . '_hover' );
            $dur_key   = $opts['dur_key']   ?? ( $key . '_hover_duration' );
            $important = ! empty( $opts['important'] );

            if ( $css_prop === '' ) continue;
            if ( ! array_key_exists( $hover_key, $s ) ) continue;

            $css_value = $this->normalize_hover_value( $css_prop, $s[ $hover_key ], $opts );
            if ( $css_value === '' ) continue;

            $imp = $important ? ' !important' : '';
            $decls[] = "{$css_prop}: {$css_value}{$imp};";

            $dur = absint( $s[ $dur_key ] ?? 300 );
            if ( $dur < 1 ) $dur = 300;
            $transitions[] = "{$css_prop} {$dur}ms ease";
        }

        return [
            'hover_decls' => implode( "\n                ", $decls ),
            'transitions' => $transitions,
        ];
    }

    /**
     * Converte un valore hover in stringa CSS valida per la property data.
     * Vedi build_hover_css() per i tipi supportati.
     */
    protected function normalize_hover_value( $css_prop, $value, $opts = [] ) {
        if ( $value === null || $value === '' ) return '';

        // Oggetto con 4 angoli (border-radius)
        if ( is_array( $value ) && isset( $value['tl'] ) ) {
            return $this->build_border_radius_css( $value );
        }
        // Oggetto con 4 lati (spacing/padding/margin)
        if ( is_array( $value ) && isset( $value['top'] ) ) {
            $t = absint( $value['top']    ?? 0 );
            $r = absint( $value['right']  ?? 0 );
            $b = absint( $value['bottom'] ?? 0 );
            $l = absint( $value['left']   ?? 0 );
            if ( $t === 0 && $r === 0 && $b === 0 && $l === 0 ) return '';
            return "{$t}px {$r}px {$b}px {$l}px";
        }
        // Color (euristica sul nome della CSS property)
        if ( is_string( $value ) && preg_match( '/(color|background|fill|stroke)/i', $css_prop ) ) {
            return $this->safe_color_css( $value );
        }
        // Numero con unit auto (default px)
        if ( is_numeric( $value ) ) {
            $unit = $opts['unit'] ?? 'px';
            return $value . $unit;
        }
        // Stringa generica (select, ecc.)
        return is_string( $value ) ? $value : '';
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
        // UIkit precede in caso di nome duplicato (preserva look storico): se la
        // 131-set UIkit conosce il nome, deleghiamo a uk-icon JS (più leggero).
        // Lucide (~1700 icone, ISC) coprono il resto via SVG inline server-side.
        static $uikit_lib = null;
        if ( $uikit_lib === null ) {
            $uikit_path = OLO_PATH . 'assets/data/uikit-icons.json';
            if ( file_exists( $uikit_path ) ) {
                $raw = file_get_contents( $uikit_path );
                $uikit_lib = json_decode( $raw, true ) ?: [];
            } else {
                $uikit_lib = [];
            }
        }
        if ( isset( $uikit_lib[ $icon_name ] ) ) {
            return '<span ' . $extra_attr . ' uk-icon="icon: ' . esc_attr( $icon_name ) . '; ratio: ' . esc_attr( $ratio ) . '"></span>';
        }
        static $lucide_lib = null;
        if ( $lucide_lib === null ) {
            $lucide_path = OLO_PATH . 'assets/data/lucide-icons.json';
            if ( file_exists( $lucide_path ) ) {
                $raw = file_get_contents( $lucide_path );
                $lucide_lib = json_decode( $raw, true ) ?: [];
            } else {
                $lucide_lib = [];
            }
        }
        if ( isset( $lucide_lib[ $icon_name ] ) ) {
            $size = round( 20 * $ratio );
            $svg = $lucide_lib[ $icon_name ];
            $svg = preg_replace( '/width="\d+"/', 'width="' . $size . '"', $svg, 1 );
            $svg = preg_replace( '/height="\d+"/', 'height="' . $size . '"', $svg, 1 );
            return '<span class="olo-lucide-icon" style="display:inline-flex" ' . $extra_attr . '>' . $svg . '</span>';
        }
        // Fallback: il nome non è in nessuno dei due dict — lascia che UIkit JS provi
        // (potrebbe essere un'icona nuova di una versione UIkit più recente).
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

    /**
     * Render a "Widget" template (type=widget) inside a tile container's item.
     *
     * Used by tile container types (accordion, icontabs, panelslider, switcher,
     * slideshow, carousel, overlayslider, ecc.) per consentire all'utente di
     * inserire un template come contenuto della singola scheda/slide/pannello.
     *
     * Sicurezza:
     *  - Anti-loop: stack di ID già in render. Se l'ID è già presente, abort.
     *  - Max depth: 5 livelli di nesting widget→widget→widget...
     *  - Type check: il template deve essere effettivamente type='widget'
     *    (no `page` o altri type per evitare embedding casuali di pagine intere).
     *
     * @param int $widget_id  ID del template widget.
     * @return string  HTML del widget renderizzato, o '' se non valido.
     */
    protected function render_widget_template( $widget_id ) {
        $widget_id = absint( $widget_id );
        if ( ! $widget_id ) return '';

        static $stack = [];
        if ( in_array( $widget_id, $stack, true ) ) {
            return '<!-- olo: widget loop detected (id ' . esc_html( $widget_id ) . ') -->';
        }
        if ( count( $stack ) >= 5 ) {
            return '<!-- olo: widget max nesting depth reached -->';
        }

        $db  = new Olo_Database();
        $tpl = $db->get_template( $widget_id );
        if ( ! $tpl ) return '';
        if ( ( $tpl['type'] ?? '' ) !== 'widget' ) {
            return '<!-- olo: template ' . esc_html( $widget_id ) . ' is not a widget -->';
        }

        $stack[] = $widget_id;
        // Chiamiamo direttamente render_shortcode invece di do_shortcode per evitare
        // che WordPress applichi filtri the_content/wpautop/wptexturize all'HTML
        // del widget — quei filtri possono spostare elementi block-level (section,
        // div) fuori dal contesto annidato (es. fuori dai <li> di uno switcher).
        if ( class_exists( 'Olo_Frontend_Renderer' ) ) {
            $renderer = new Olo_Frontend_Renderer();
            $output = $renderer->render_shortcode( [ 'id' => $widget_id ] );
        } else {
            $output = do_shortcode( '[olo_template id="' . $widget_id . '"]' );
        }
        array_pop( $stack );

        // wpautop interferisce con HTML annidato dentro <li>/<td>/etc. trasformando
        // sequenze di newlines in <p>...</p> che possono rompere la chiusura di
        // elementi e far "estrarre" il content fuori dal contenitore. Rimuoviamo
        // i double-newlines (e collassiamo gli whitespace tra tag) per evitarlo.
        $output = preg_replace( '/\n\s*\n/', "\n", $output );
        $output = preg_replace( '/>\s+</', '><', $output );
        return $output;
    }
}
