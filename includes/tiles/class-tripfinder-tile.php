<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Trip Finder — barra di ricerca/prenotazione: N campi (label + select) + bottone.
 * Estratta dai blueprint OLOthemes (TripFinder/BookingBar). Token-first (`accent`).
 * Render == Vue (TripFinderTile.vue). Select nativi (nessun JS richiesto).
 */
class Olobuild_TripFinder_Tile extends Olobuild_Tile_Base {

    protected $type     = 'tripfinder';
    protected $name     = 'Trip Finder';
    protected $icon     = 'dashicons-search';
    protected $category = 'interactive';
    protected $defaults = [
        'fields' => [
            [ 'label' => 'Destination', 'value' => 'Anywhere', 'options' => "Anywhere\nNorway · Lofoten\nIceland\nGreenland" ],
            [ 'label' => 'When', 'value' => 'Any month', 'options' => "Any month\nMar — aurora\nJun — midnight sun\nSep — autumn light" ],
            [ 'label' => 'Activity', 'value' => 'Any', 'options' => "Any\nHiking & trekking\nSea kayaking\nWildlife" ],
        ],
        'button_text'  => 'Search',
        'button_url'   => '#',
        'accent'       => '',
        'accent_on'    => '#ffffff',
        'bar_bg'       => '',
        'field_bg'     => '',
        'field_border' => '',
        'label_color'  => '',
        'value_color'  => '',
        'radius'       => 14,

        // SPAZIATURA additiva — default = padding storico (barra 8px, campi 10/16).
        'bar_padding'   => [ 'top' => 8, 'right' => 8, 'bottom' => 8, 'left' => 8 ],
        'field_padding' => [ 'top' => 10, 'right' => 16, 'bottom' => 10, 'left' => 16 ],

        // FORMA additiva — raggio per-angolo override. Tutto 0 → usa `radius` (no-op).
        'radius_corners' => [ 'tl' => 0, 'tr' => 0, 'br' => 0, 'bl' => 0 ],

        // KIT standard OLObuild — sfondo completo + ombra + bordo sul contenitore.
        // Default no-op: bg none / shadow none / border 0 → render invariato.
        'bg'                      => [ 'type' => 'none' ],
        'shadow'                  => 'none',
        'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'otf-' . wp_rand( 10000, 99999 );

        $accent = $this->safe_color_css( $s['accent'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $on     = $this->safe_color_css( $s['accent_on'] ?? '' ) ?: '#ffffff';
        $barbg  = $this->safe_color_css( $s['bar_bg'] ?? '' ) ?: 'var(--olo-color-surface, #ffffff)';
        $fbg    = $this->safe_color_css( $s['field_bg'] ?? '' ) ?: 'transparent';
        $fbd    = $this->safe_color_css( $s['field_border'] ?? '' ) ?: 'var(--olo-color-border, #e5e7eb)';
        $lab    = $this->safe_color_css( $s['label_color'] ?? '' ) ?: 'var(--olo-color-text-muted, #6b7280)';
        $val    = $this->safe_color_css( $s['value_color'] ?? '' ) ?: 'var(--olo-color-text, #111827)';
        $rad    = intval( $s['radius'] ) . 'px';
        $sans   = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";

        // FORMA: raggio per-angolo override. '' (tutti 0) → usa $rad uniforme storico.
        $rad_corners = $this->build_border_radius_css( $s['radius_corners'] ?? [] );
        $rad_eff     = ( $rad_corners !== '' ) ? $rad_corners : $rad;

        // SPAZIATURA: padding barra/campi. Default = valori storici → render invariato.
        $bar_pad   = $this->tf_pad_css( $s['bar_padding'] ?? [], [ 8, 8, 8, 8 ] );
        $field_pad = $this->tf_pad_css( $s['field_padding'] ?? [], [ 10, 16, 10, 16 ] );

        $fields = is_array( $s['fields'] ) ? array_values( $s['fields'] ) : [];
        if ( empty( $fields ) ) return '';

        // ── KIT standard OLObuild: sfondo completo + ombra + bordo sul contenitore ──
        // Sfondo completo (override SOLO se valorizzato → default invariato).
        $bg_obj  = $s['bg'] ?? null;
        $bg_decl = '';
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olobuild_CSS_Builder' ) ) {
            $bg_decl = ( new Olobuild_CSS_Builder() )->get_bg_inline_css( $bg_obj );
        }
        // Ombra (preset/custom). '' se none.
        $shadow_css = $this->build_shadow_decl( $s );
        // Bordo (come la coda di particlefx render).
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );

        // Decorazioni inline per la regola del contenitore .$uid (no-op coi default).
        $box_decl = '';
        if ( $bg_decl )    { $box_decl .= $bg_decl . ';'; }
        if ( $border_css ) { $box_decl .= $border_css; }
        if ( $shadow_css ) { $box_decl .= 'box-shadow:' . $shadow_css . ';'; }
        // position:relative serve agli effetti bordo (come in particlefx).
        if ( $box_decl || $border_effect_css ) { $box_decl .= 'position:relative;'; }

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: every colour via the safe_color_css() whitelist (with fixed var() fallbacks), radius/padding via intval() helpers, box decorations via the Olobuild_CSS_Builder/Olobuild_Tile_Base shared helpers (sanitized internally), fixed font-stack literal; $uid is internally generated. ?>
        <style>
            .<?php echo $uid; ?>{font-family:<?php echo $sans; ?>;<?php echo $box_decl; ?>}
            .<?php echo $uid; ?> .otf-bar{display:flex;flex-wrap:wrap;align-items:stretch;gap:0;background:<?php echo $barbg; ?>;border:1px solid <?php echo $fbd; ?>;border-radius:<?php echo $rad_eff; ?>;padding:<?php echo $bar_pad; ?>;box-shadow:0 18px 50px -28px rgba(0,0,0,.35);}
            .<?php echo $uid; ?> .otf-f{flex:1 1 160px;display:flex;flex-direction:column;gap:4px;padding:<?php echo $field_pad; ?>;background:<?php echo $fbg; ?>;border-left:1px solid <?php echo $fbd; ?>;min-width:0;}
            .<?php echo $uid; ?> .otf-f:first-child{border-left:0;}
            .<?php echo $uid; ?> .otf-lab{font-size:11px;font-weight:600;letter-spacing:.12em;text-transform:uppercase;color:<?php echo $lab; ?>;}
            .<?php echo $uid; ?> .otf-sel{font-family:<?php echo $sans; ?>;font-size:15px;font-weight:600;color:<?php echo $val; ?>;background:transparent;border:0;padding:2px 0;cursor:pointer;width:100%;appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23999' stroke-width='2.4' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right center;padding-right:18px;}
            .<?php echo $uid; ?> .otf-sel:focus-visible{outline:2px solid <?php echo $accent; ?>;outline-offset:2px;}
            .<?php echo $uid; ?> .otf-btn{flex:0 0 auto;display:inline-flex;align-items:center;justify-content:center;gap:8px;margin-left:8px;padding:0 26px;background:<?php echo $accent; ?>;color:<?php echo $on; ?>;font-weight:700;font-size:14px;letter-spacing:.02em;border:0;border-radius:<?php echo $rad_eff; ?>;text-decoration:none;cursor:pointer;transition:transform .18s,filter .18s;}
            .<?php echo $uid; ?> .otf-btn:hover{transform:translateY(-1px);filter:brightness(1.05);}
            .<?php echo $uid; ?> .otf-btn:focus-visible{outline:2px solid <?php echo $accent; ?>;outline-offset:3px;}
            .<?php echo $uid; ?> .otf-btn svg{width:16px;height:16px;}
            @media (max-width:680px){.<?php echo $uid; ?> .otf-f{flex:1 1 100%;border-left:0;border-top:1px solid <?php echo $fbd; ?>;}.<?php echo $uid; ?> .otf-f:first-child{border-top:0;}.<?php echo $uid; ?> .otf-btn{flex:1 1 100%;margin:8px 0 0;padding:14px 26px;}}
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <form class="olo-tripfinder <?php echo esc_attr( $uid ); ?>" onsubmit="return false">
            <div class="otf-bar">
                <?php foreach ( $fields as $f ) :
                    $flabel = isset( $f['label'] ) ? $f['label'] : '';
                    $fval   = isset( $f['value'] ) ? $f['value'] : '';
                    $opts   = preg_split( '/\r\n|\r|\n/', isset( $f['options'] ) ? $f['options'] : '' );
                ?>
                    <label class="otf-f">
                        <span class="otf-lab"><?php echo esc_html( $flabel ); ?></span>
                        <select class="otf-sel" aria-label="<?php echo esc_attr( $flabel ); ?>">
                            <?php foreach ( $opts as $opt ) : $opt = trim( $opt ); if ( $opt === '' ) continue; ?>
                                <option<?php echo ( $opt === trim( (string) $fval ) ) ? ' selected' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed ' selected'/'' literal from the ternary ?>><?php echo esc_html( $opt ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                <?php endforeach; ?>
                <a class="otf-btn" href="<?php echo esc_url( $s['button_url'] ?: '#' ); ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                    <?php echo esc_html( $s['button_text'] ?: 'Search' ); ?>
                </a>
            </div>
        </form>
        <?php
        // ── Sistema bordi standard: hover + effetto (come particlefx) ──────
        if ( $border_hover_css || $border_effect_css ) {
            echo '<style>' . $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }

    /**
     * Spaziatura → stringa CSS "Tpx Rpx Bpx Lpx". Il setting è un oggetto
     * { top, right, bottom, left }. Se assente/non valido usa il fallback storico
     * (così il render coi default resta invariato). Additivo e no-op sui default.
     *
     * @param mixed $pad      Oggetto spacing { top, right, bottom, left }.
     * @param array $fallback [top, right, bottom, left] storici.
     * @return string CSS shorthand del padding.
     */
    private function tf_pad_css( $pad, $fallback ) {
        $top    = isset( $pad['top'] )    ? intval( $pad['top'] )    : $fallback[0];
        $right  = isset( $pad['right'] )  ? intval( $pad['right'] )  : $fallback[1];
        $bottom = isset( $pad['bottom'] ) ? intval( $pad['bottom'] ) : $fallback[2];
        $left   = isset( $pad['left'] )   ? intval( $pad['left'] )   : $fallback[3];
        return "{$top}px {$right}px {$bottom}px {$left}px";
    }

    /**
     * Restituisce la dichiarazione box-shadow (valore, senza "box-shadow:")
     * dal setting shadow (preset sm/md/lg/xl o custom). '' se none.
     * Copiato dal pattern standard OLObuild (cfr. Olobuild_Particlefx_Tile).
     */
    private function build_shadow_decl( $s ) {
        $preset = $s['shadow'] ?? 'none';
        if ( $preset === 'none' || $preset === '' ) {
            return '';
        }
        if ( $preset === 'custom' ) {
            $h      = intval( $s['shadow_h'] ?? 0 );
            $v      = intval( $s['shadow_v'] ?? 4 );
            $blur   = max( 0, intval( $s['shadow_blur'] ?? 10 ) );
            $spread = intval( $s['shadow_spread'] ?? 0 );
            $color  = $this->safe_color_css( $s['shadow_color'] ?? '' ) ?: 'rgba(0,0,0,0.15)';
            $inset  = ! empty( $s['shadow_inset'] ) ? 'inset ' : '';
            return "{$inset}{$h}px {$v}px {$blur}px {$spread}px {$color}";
        }
        $map = [
            'sm' => '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
            'md' => '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
            'lg' => '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
            'xl' => '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
        ];
        return $map[ $preset ] ?? '';
    }
}
