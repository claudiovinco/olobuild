<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Hero — Masthead (Dispatch) : testata di giornale stampato. Riga edizione/data a
 * sinistra · nameplate serif gigante centrato · azioni (sign-in + abbonati) a destra,
 * tutto tra file/righe sottili (hairline rules). Sotto, il blocco lead-story: kicker,
 * occhiello, titolo serif gigante (riga normale + riga corsiva), standfirst e firma.
 * Mecca­nismo firma = la testata stampata con righe sottili. Nessun JS.
 * Render == Vue (MastheadTile.vue). Estratta dal blueprint OLOthemes "Dispatch".
 */
class Olo_Masthead_Tile extends Olo_Tile_Base {

    protected $type     = 'masthead';
    protected $name     = 'Hero — Masthead (Dispatch)';
    protected $icon     = 'dashicons-text-page';
    protected $category = 'marketing';
    protected $defaults = [
        // Edition / nameplate
        'edition_text'   => 'Friday, 6 March 2026 · Milan',
        'nameplate_text' => 'The Dispatch',
        'action1_text'   => 'Sign in',
        'action1_url'    => '#',
        'action2_text'   => 'Subscribe',
        'action2_url'    => '#newsletter',
        // Lead story
        'kicker_text'    => 'Politics · Analysis',
        'headline_text'  => 'Inside the budget deal',
        'headline_italic_text' => 'that almost didn\'t happen',
        'subhead'        => 'For seventy-two hours the talks looked dead. Then a late-night compromise on housing rewrote the maths — and the coalition with it. We reconstruct the week.',
        'byline_text'    => 'By Elena Marchetti · 12 min read',
        // Colors / type
        'bg_color'       => '#f4f1ea',
        'ink_color'      => '#16161a',
        'ink_soft_color' => '#44444c',
        'ink_faint_color'=> '#86848c',
        'accent'         => '#cf2e2e',
        'rule_color'     => '#ddd8cc',
        'nameplate_size' => 52,
        'headline_size'  => 54,

        // Spaziatura (additivo, no-op coi default). Override GATED del padding lead (responsive clamp).
        'pad_custom'     => false,
        'lead_padding'   => [ 'top' => 52, 'right' => 0, 'bottom' => 60, 'left' => 0 ],

        // Forma — raggio bottone "Subscribe" (default 2px = invariato).
        'btn_radius'     => [ 'tl' => 2, 'tr' => 2, 'br' => 2, 'bl' => 2 ],

        // KIT standard OLObuild (additivo, no-op coi default)
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
        $uid = 'omst-' . wp_rand( 10000, 99999 );

        $bg     = $this->safe_color_css( $s['bg_color'] ) ?: '#f4f1ea';
        $ink    = $this->safe_color_css( $s['ink_color'] ?? '' ) ?: '#16161a';
        $inkS   = $this->safe_color_css( $s['ink_soft_color'] ?? '' ) ?: '#44444c';
        $inkF   = $this->safe_color_css( $s['ink_faint_color'] ?? '' ) ?: '#86848c';
        $accent = $this->safe_color_css( $s['accent'] ?? '' ) ?: 'var(--olo-color-primary, #cf2e2e)';
        $rule   = $this->safe_color_css( $s['rule_color'] ?? '' ) ?: '#ddd8cc';

        $npSize = max( 24, min( 96, intval( $s['nameplate_size'] ) ) );
        $hSize  = max( 24, min( 96, intval( $s['headline_size'] ) ) );

        $disp   = "var(--olo-font-family-heading, 'Newsreader',Georgia,serif)";
        $sans   = "var(--olo-font-family, 'Work Sans',-apple-system,sans-serif)";

        // ── KIT standard OLObuild ─────────────────────────────────────────
        // Sfondo completo: override del bg di base SOLO se valorizzato (default none → invariato).
        $bg_obj  = $s['bg'] ?? null;
        $bg_decl = '';
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olo_CSS_Builder' ) ) {
            $bg_decl = ( new Olo_CSS_Builder() )->get_bg_inline_css( $bg_obj );
        }
        // Ombra (preset/custom). '' coi default → nessuna ombra.
        $shadow_css = $this->build_shadow_decl( $s );

        // Stile extra del contenitore principale: sfondo completo + ombra (additivi).
        $root_extra = '';
        if ( $bg_decl !== '' ) {
            // get_bg_inline_css restituisce una o più dichiarazioni (es. "background:...").
            $root_extra .= rtrim( $bg_decl, ';' ) . ';';
        }
        if ( $shadow_css !== '' ) {
            $root_extra .= 'box-shadow:' . $shadow_css . ';';
        }

        // ── Spaziatura: override GATED del padding del blocco lead ─────────
        // Default (pad_custom false) → clamp() responsive invariato.
        $lead_pad = 'clamp(32px,4vw,52px) 0 clamp(36px,5vw,60px)';
        if ( ! empty( $s['pad_custom'] ) && is_array( $s['lead_padding'] ?? null ) ) {
            $lp    = $s['lead_padding'];
            $lpT   = intval( $lp['top'] ?? 0 );
            $lpR   = intval( $lp['right'] ?? 0 );
            $lpB   = intval( $lp['bottom'] ?? 0 );
            $lpL   = intval( $lp['left'] ?? 0 );
            $lead_pad = "{$lpT}px {$lpR}px {$lpB}px {$lpL}px";
        }

        // ── Forma: raggio bottone. Default {2,2,2,2} → "2px" esatto (byte-identico al render storico). ──
        $br_obj = is_array( $s['btn_radius'] ?? null ) ? $s['btn_radius'] : [];
        $br_tl  = intval( $br_obj['tl'] ?? 2 );
        $br_tr  = intval( $br_obj['tr'] ?? 2 );
        $br_brr = intval( $br_obj['br'] ?? 2 );
        $br_bl  = intval( $br_obj['bl'] ?? 2 );
        if ( $br_tl === $br_tr && $br_tr === $br_brr && $br_brr === $br_bl ) {
            $btn_radius = "{$br_tl}px";
        } else {
            $btn_radius = "{$br_tl}px {$br_tr}px {$br_brr}px {$br_bl}px";
        }

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: every colour via the safe_color_css() whitelist (or fixed literal fallback), sizes via intval() with min()/max() clamps and round(), padding/radius from intval()'d sides or fixed clamp() literals, fixed font-stack literals, $root_extra from Olo_CSS_Builder::get_bg_inline_css()/build_shadow_decl(), border via build_border_css(); $uid is internally generated. ?>
        <style>
            .<?php echo $uid; ?>{position:relative;background:<?php echo $bg; ?>;color:<?php echo $ink; ?>;font-family:<?php echo $sans; ?>;-webkit-font-smoothing:antialiased;<?php echo $root_extra; ?><?php echo $this->build_border_css( $s['border'] ?? [] ); ?>}
            .<?php echo $uid; ?> .mst-wrap{max-width:1200px;margin:0 auto;padding:0 28px;}
            .<?php echo $uid; ?> .mst-mast{border-bottom:1px solid <?php echo $rule; ?>;}
            .<?php echo $uid; ?> .mst-top{display:flex;align-items:center;justify-content:space-between;padding:18px 0 14px;gap:16px;}
            .<?php echo $uid; ?> .mst-date{font-size:12px;color:<?php echo $inkF; ?>;letter-spacing:.02em;flex:1;}
            .<?php echo $uid; ?> .mst-name{font-family:<?php echo $disp; ?>;font-weight:600;font-size:clamp(34px,<?php echo round( $npSize / 11.8, 2 ); ?>vw,<?php echo $npSize; ?>px);letter-spacing:-.02em;text-align:center;flex:1;color:<?php echo $ink; ?>;text-decoration:none;line-height:1.04;}
            .<?php echo $uid; ?> .mst-act{flex:1;display:flex;justify-content:flex-end;gap:12px;align-items:center;}
            .<?php echo $uid; ?> .mst-byline{font-family:<?php echo $sans; ?>;font-size:12px;color:<?php echo $inkF; ?>;letter-spacing:.02em;text-decoration:none;transition:color .15s;}
            .<?php echo $uid; ?> .mst-byline:hover{color:<?php echo $ink; ?>;}
            .<?php echo $uid; ?> .mst-btn{display:inline-flex;align-items:center;gap:8px;padding:12px 22px;border-radius:<?php echo $btn_radius; ?>;font-family:<?php echo $sans; ?>;font-weight:600;font-size:13px;letter-spacing:.03em;text-decoration:none;cursor:pointer;border:0;background:<?php echo $accent; ?>;color:#fff;transition:transform .15s,filter .2s;}
            .<?php echo $uid; ?> .mst-btn:hover{transform:translateY(-2px);filter:brightness(1.06);}
            .<?php echo $uid; ?> .mst-nav-rule{border-top:1px solid <?php echo $rule; ?>;border-bottom:1px solid <?php echo $rule; ?>;height:6px;}
            .<?php echo $uid; ?> .mst-lead{padding:<?php echo $lead_pad; ?>;max-width:820px;}
            .<?php echo $uid; ?> .mst-kicker{font-family:<?php echo $sans; ?>;font-weight:700;font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:<?php echo $accent; ?>;display:block;margin-bottom:12px;}
            .<?php echo $uid; ?> .mst-h{font-family:<?php echo $disp; ?>;font-weight:600;font-size:clamp(32px,<?php echo round( $hSize / 11.7, 2 ); ?>vw,<?php echo $hSize; ?>px);line-height:1.04;letter-spacing:-.01em;color:<?php echo $ink; ?>;margin:0;}
            .<?php echo $uid; ?> .mst-h em{font-style:italic;}
            .<?php echo $uid; ?> .mst-sub{color:<?php echo $inkS; ?>;font-size:17px;line-height:1.6;margin:14px 0 0;max-width:620px;}
            .<?php echo $uid; ?> .mst-lead-by{margin-top:14px;font-family:<?php echo $sans; ?>;font-size:12px;color:<?php echo $inkF; ?>;letter-spacing:.02em;}
            .<?php echo $uid; ?> a:focus-visible,.<?php echo $uid; ?> .mst-btn:focus-visible{outline:2px solid <?php echo $accent; ?>;outline-offset:3px;}
            @media(max-width:760px){.<?php echo $uid; ?> .mst-date,.<?php echo $uid; ?> .mst-act{display:none;}.<?php echo $uid; ?> .mst-name{text-align:left;}}
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <section class="olo-masthead <?php echo esc_attr( $uid ); ?>">
            <div class="mst-mast">
                <div class="mst-wrap mst-top">
                    <?php if ( ! empty( $s['edition_text'] ) ) : ?><span class="mst-date"><?php echo esc_html( $s['edition_text'] ); ?></span><?php endif; ?>
                    <a class="mst-name" href="<?php echo esc_url( '#' ); ?>"><?php echo esc_html( $s['nameplate_text'] ); ?></a>
                    <div class="mst-act">
                        <?php if ( ! empty( $s['action1_text'] ) ) : ?><a class="mst-byline" href="<?php echo esc_url( $s['action1_url'] ?: '#' ); ?>"><?php echo esc_html( $s['action1_text'] ); ?></a><?php endif; ?>
                        <?php if ( ! empty( $s['action2_text'] ) ) : ?><a class="mst-btn" href="<?php echo esc_url( $s['action2_url'] ?: '#' ); ?>"><?php echo esc_html( $s['action2_text'] ); ?></a><?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="mst-nav-rule"></div>
            <div class="mst-wrap">
                <div class="mst-lead">
                    <?php if ( ! empty( $s['kicker_text'] ) ) : ?><span class="mst-kicker"><?php echo esc_html( $s['kicker_text'] ); ?></span><?php endif; ?>
                    <h1 class="mst-h"><?php echo esc_html( $s['headline_text'] ); ?><?php if ( ! empty( $s['headline_italic_text'] ) ) : ?> <em><?php echo esc_html( $s['headline_italic_text'] ); ?></em><?php endif; ?></h1>
                    <?php if ( ! empty( $s['subhead'] ) ) : ?><p class="mst-sub"><?php echo esc_html( $s['subhead'] ); ?></p><?php endif; ?>
                    <?php if ( ! empty( $s['byline_text'] ) ) : ?><div class="mst-lead-by"><?php echo esc_html( $s['byline_text'] ); ?></div><?php endif; ?>
                </div>
            </div>
        </section>
        <?php
        // ── Sistema bordi standard (hover + effetti) ──────────────────────
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_hover_css || $border_effect_css ) {
            echo '<style>' . $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }

    /**
     * Restituisce la dichiarazione box-shadow (valore, senza "box-shadow:")
     * dal setting shadow (preset sm/md/lg/xl o custom). '' se none.
     */
    private function build_shadow_decl( $s ) {
        $preset = $s['shadow'] ?? 'none';
        if ( $preset === 'none' || $preset === '' ) {
            return '';
        }
        if ( $preset === 'custom' ) {
            $h     = intval( $s['shadow_h'] ?? 0 );
            $v     = intval( $s['shadow_v'] ?? 4 );
            $blur  = max( 0, intval( $s['shadow_blur'] ?? 10 ) );
            $spread = intval( $s['shadow_spread'] ?? 0 );
            $color = $this->safe_color_css( $s['shadow_color'] ?? '' ) ?: 'rgba(0,0,0,0.15)';
            $inset = ! empty( $s['shadow_inset'] ) ? 'inset ' : '';
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
