<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Hero — Glow Statement : hero centrato a tutto schermo su fondo scuro con
 * GLOW radiale sfocato dietro un titolo editoriale gigante multi-riga. Ogni riga/parola
 * puo' essere colorata-accento, resa OUTLINE (text-stroke, fill trasparente) o con
 * accento a gradiente. Eyebrow con dot tra le parole, sub, fino a 2 CTA, scroll hint.
 * Glow color/size/blur/position configurabili. Titolo via clamp(). Nessun JS.
 * Render == Vue (GlowHeroTile.vue). Default fedeli al blueprint OLOthemes "Vela".
 */
class Olobuild_GlowHero_Tile extends Olobuild_Tile_Base {

    protected $type     = 'glowhero';
    protected $name     = 'Hero — Glow Statement';
    protected $icon     = 'dashicons-superhero';
    protected $category = 'marketing';
    protected $defaults = [
        // Eyebrow (parole separate da " | "): rese con dot tra le parole
        'eyebrow'        => 'Independent studio | Est. 2015 | Milan / everywhere',
        // Righe titolo: array di { text, mode } — mode: '' (normale) | 'accent' | 'outline' | 'gradient'
        'lines'          => [
            [ 'text' => 'Design with', 'mode' => '' ],
            [ 'text' => 'a point',     'mode' => 'accent' ],
            [ 'text' => 'of view.',    'mode' => 'outline' ],
        ],
        'uppercase'      => true,
        'eyebrow_dots'   => true,
        'subhead'        => "We build brands, identities and digital products for people who'd rather stand out than blend in.",
        'cta1_text'      => '',
        'cta1_url'       => '#',
        'cta2_text'      => '',
        'cta2_url'       => '#',
        'scroll_text'    => 'Scroll to see the work',
        'show_scroll'    => true,
        // Aspetto / glow
        'bg_color'       => '#0a0a0c',
        'accent'         => '',
        'accent2'        => '',
        'accent_on'      => '#0a0a0c',
        'text_color'     => '#ece9e3',
        'sub_color'      => '#9b988f',
        'eyebrow_color'  => '#9b988f',
        'glow_color'     => 'rgba(244,162,59,0.18)',
        'glow_w'         => 760,
        'glow_h'         => 560,
        'glow_blur'      => 100,
        'glow_x'         => 50,
        'glow_y'         => 20,
        // Titolo
        'h_size_min'     => 54,
        'h_size_vw'      => 12,
        'h_size_max'     => 180,
        'h_line_height'  => 0.86,
        'stroke_width'   => 2,
        // Layout
        'align'          => 'left',
        'max_width'      => 1240,
        'min_height'     => 100,
        'bottom_split'   => true,

        // Spaziatura — override GATED del padding responsive del contenitore (clamp). No-op coi default.
        'pad_custom'      => false,
        'content_padding' => [ 'top' => 96, 'right' => 0, 'bottom' => 96, 'left' => 0 ],
        // Forma — raggio dei pulsanti CTA (pill). No-op coi default (999 = pill attuale).
        'btn_radius'      => [ 'tl' => 999, 'tr' => 999, 'br' => 999, 'bl' => 999 ],

        // KIT standard OLObuild — sfondo completo / ombra / bordo (no-op coi default)
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
        $uid = 'oglw-' . wp_rand( 10000, 99999 );

        $bg     = $this->safe_color_css( $s['bg_color'] ) ?: '#0a0a0c';
        $accent = $this->safe_color_css( $s['accent'] ) ?: 'var(--olo-color-primary, #f4a23b)';
        $acc2   = $this->safe_color_css( $s['accent2'] ?? '' ) ?: 'var(--olo-color-secondary, #4be0ff)';
        $accOn  = $this->safe_color_css( $s['accent_on'] ?? '' ) ?: '#0a0a0c';
        $txt    = $this->safe_color_css( $s['text_color'] ?? '' ) ?: '#ece9e3';
        $sub    = $this->safe_color_css( $s['sub_color'] ?? '' ) ?: '#9b988f';
        $eyc    = $this->safe_color_css( $s['eyebrow_color'] ?? '' ) ?: '#9b988f';
        $glow   = $this->safe_color_css( $s['glow_color'] ?? '' ) ?: 'rgba(244,162,59,0.18)';

        $gw     = max( 100, intval( $s['glow_w'] ) );
        $gh     = max( 100, intval( $s['glow_h'] ) );
        $gblur  = max( 0, intval( $s['glow_blur'] ) );
        $gx     = max( 0, min( 100, intval( $s['glow_x'] ) ) );
        $gy     = max( -50, min( 100, intval( $s['glow_y'] ) ) );

        $hmin   = max( 20, intval( $s['h_size_min'] ) );
        $hvw    = max( 1, floatval( $s['h_size_vw'] ) );
        $hmax   = max( $hmin, intval( $s['h_size_max'] ) );
        $hlh    = is_numeric( $s['h_line_height'] ) ? floatval( $s['h_line_height'] ) : 0.86;
        $strk   = max( 0, floatval( $s['stroke_width'] ) );

        $align  = in_array( $s['align'] ?? 'left', [ 'left', 'center' ], true ) ? ( $s['align'] ?? 'left' ) : 'left';
        $alignI = $align === 'center' ? 'center' : 'flex-start';
        $txtAl  = $align === 'center' ? 'center' : 'left';
        $mw     = max( 600, intval( $s['max_width'] ) );
        $mh     = max( 40, min( 100, intval( $s['min_height'] ) ) );
        $up     = ! empty( $s['uppercase'] ) ? 'uppercase' : 'none';
        $split  = ! empty( $s['bottom_split'] );
        $dots   = ! empty( $s['eyebrow_dots'] );

        // ── Spaziatura: override GATED del padding responsive (clamp) del contenitore ──
        $pad_decl = 'clamp(96px,14vh,140px) 0';
        if ( ! empty( $s['pad_custom'] ) && is_array( $s['content_padding'] ?? null ) ) {
            $cp = $s['content_padding'];
            $pt = intval( $cp['top'] ?? 0 );
            $pr = intval( $cp['right'] ?? 0 );
            $pb = intval( $cp['bottom'] ?? 0 );
            $pl = intval( $cp['left'] ?? 0 );
            $pad_decl = "{$pt}px {$pr}px {$pb}px {$pl}px";
        }

        // ── Forma: raggio pulsanti CTA (pill). build_border_radius_css ritorna '' se tutti 0 ──
        $btn_radius_val = $this->build_border_radius_css( $s['btn_radius'] ?? [] );
        $btn_radius_css = $btn_radius_val !== '' ? $btn_radius_val : '999px';

        $disp   = "var(--olo-font-family-heading, 'Syne',-apple-system,sans-serif)";
        $sans   = "var(--olo-font-family, 'Work Sans',-apple-system,sans-serif)";

        $eyebrow = trim( (string) ( $s['eyebrow'] ?? '' ) );
        $words   = array_filter( array_map( 'trim', explode( '|', $eyebrow ) ), 'strlen' );

        $lines = is_array( $s['lines'] ?? null ) ? $s['lines'] : [];

        // ── KIT standard: sfondo completo (override del bg attuale SOLO se valorizzato) ──
        $bg_obj  = $s['bg'] ?? null;
        $bg_decl = '';
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olobuild_CSS_Builder' ) ) {
            $bg_decl = ( new Olobuild_CSS_Builder() )->get_bg_inline_css( $bg_obj );
        }
        // ── Ombra (preset/custom) ──
        $shadow_css = $this->build_shadow_decl( $s );
        // Coda dichiarazioni extra per la regola del contenitore .$uid
        $kit_extra = '';
        if ( $bg_decl !== '' )    { $kit_extra .= $bg_decl . ';'; }
        if ( $shadow_css !== '' ) { $kit_extra .= 'box-shadow:' . $shadow_css . ';'; }

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above (safe_color_css/intval/floatval/whitelist ternaries/fixed font stacks); escaping would corrupt valid CSS quotes. ?>
        <style>
            .<?php echo $uid; ?>{position:relative;overflow:hidden;min-height:<?php echo (int) $mh; ?>vh;display:flex;flex-direction:column;justify-content:center;background:<?php echo $bg; ?>;color:<?php echo $txt; ?>;font-family:<?php echo $sans; ?>;padding:<?php echo $pad_decl; ?>;<?php echo $kit_extra; ?>}
            .<?php echo $uid; ?> .glw-glow{position:absolute;top:<?php echo (int) $gy; ?>%;left:<?php echo (int) $gx; ?>%;transform:translate(-50%,-30%);width:<?php echo (int) $gw; ?>px;height:<?php echo (int) $gh; ?>px;border-radius:50%;filter:blur(<?php echo (int) $gblur; ?>px);pointer-events:none;background:radial-gradient(circle, <?php echo $glow; ?>, transparent 70%);z-index:0;}
            .<?php echo $uid; ?> .glw-in{position:relative;z-index:2;width:100%;max-width:<?php echo (int) $mw; ?>px;margin:0 auto;padding:0 28px;display:flex;flex-direction:column;align-items:<?php echo $alignI; ?>;text-align:<?php echo $txtAl; ?>;}
            .<?php echo $uid; ?> .glw-eyebrow{display:flex;gap:26px;flex-wrap:wrap;justify-content:<?php echo $alignI; ?>;margin-bottom:34px;color:<?php echo $eyc; ?>;font-size:13px;letter-spacing:.04em;}
            .<?php echo $uid; ?> .glw-eyebrow span{display:inline-flex;align-items:center;gap:<?php echo $dots ? '9px' : '0'; ?>;}
            <?php if ( $dots ) : ?>.<?php echo $uid; ?> .glw-eyebrow span::before{content:"";width:6px;height:6px;border-radius:50%;background:<?php echo $accent; ?>;}<?php endif; ?>
            .<?php echo $uid; ?> .glw-h{font-family:<?php echo $disp; ?>;font-weight:800;font-size:clamp(<?php echo (int) $hmin; ?>px,<?php echo (float) $hvw; ?>vw,<?php echo (int) $hmax; ?>px);line-height:<?php echo (float) $hlh; ?>;letter-spacing:-.02em;text-transform:<?php echo $up; ?>;color:<?php echo $txt; ?>;margin:0;}
            .<?php echo $uid; ?> .glw-h .ln{display:block;}
            .<?php echo $uid; ?> .glw-h .acc{color:<?php echo $accent; ?>;}
            .<?php echo $uid; ?> .glw-h .out{-webkit-text-stroke:<?php echo (float) $strk; ?>px <?php echo $txt; ?>;color:transparent;}
            .<?php echo $uid; ?> .glw-h .grd{background:linear-gradient(110deg, <?php echo $accent; ?>, <?php echo $acc2; ?>);-webkit-background-clip:text;background-clip:text;color:transparent;}
            .<?php echo $uid; ?> .glw-bottom{display:flex;<?php echo $split ? 'justify-content:space-between;' : 'flex-direction:column;gap:24px;'; ?>align-items:<?php echo $split ? 'flex-end' : $alignI; ?>;gap:30px;margin-top:42px;flex-wrap:wrap;width:100%;}
            .<?php echo $uid; ?> .glw-sub{max-width:440px;font-size:17px;line-height:1.6;color:<?php echo $sub; ?>;margin:0;}
            .<?php echo $uid; ?> .glw-side{display:flex;flex-direction:column;gap:18px;align-items:<?php echo $align === 'center' ? 'center' : 'flex-start'; ?>;}
            .<?php echo $uid; ?> .glw-cta{display:flex;gap:13px;flex-wrap:wrap;}
            .<?php echo $uid; ?> .glw-btn{display:inline-flex;align-items:center;gap:9px;padding:16px 30px;border-radius:<?php echo $btn_radius_css; ?>;font-family:<?php echo $sans; ?>;font-weight:700;font-size:15px;text-decoration:none;cursor:pointer;border:0;transition:transform .15s,background .2s,filter .2s;}
            .<?php echo $uid; ?> .glw-btn svg{width:17px;height:17px;}
            .<?php echo $uid; ?> .glw-btn:hover{transform:translateY(-2px);}
            .<?php echo $uid; ?> .glw-btn--solid{background:<?php echo $accent; ?>;color:<?php echo $accOn; ?>;}
            .<?php echo $uid; ?> .glw-btn--solid:hover{filter:brightness(1.06);}
            .<?php echo $uid; ?> .glw-btn--ghost{background:transparent;color:<?php echo $txt; ?>;border:1.5px solid rgba(255,255,255,.22);}
            .<?php echo $uid; ?> .glw-btn:focus-visible{outline:2px solid <?php echo $accent; ?>;outline-offset:3px;}
            .<?php echo $uid; ?> .glw-scroll{display:inline-flex;align-items:center;gap:10px;font-size:12px;letter-spacing:.1em;text-transform:uppercase;color:<?php echo $sub; ?>;}
            .<?php echo $uid; ?> .glw-scroll .ln{width:46px;height:1px;background:<?php echo $sub; ?>;}
            @media(max-width:680px){.<?php echo $uid; ?> .glw-bottom{flex-direction:column;align-items:<?php echo $alignI; ?>;}}
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <section class="olo-glowhero <?php echo esc_attr( $uid ); ?>">
            <span class="glw-glow"></span>
            <div class="glw-in">
                <?php if ( ! empty( $words ) ) : ?>
                <div class="glw-eyebrow"><?php foreach ( $words as $w ) : ?><span><?php echo esc_html( $w ); ?></span><?php endforeach; ?></div>
                <?php endif; ?>
                <h1 class="glw-h"><?php
                    foreach ( $lines as $ln ) {
                        $t = isset( $ln['text'] ) ? (string) $ln['text'] : '';
                        if ( $t === '' ) { continue; }
                        $m = isset( $ln['mode'] ) ? (string) $ln['mode'] : '';
                        $cls = 'ln';
                        if ( $m === 'accent' )  { $cls .= ' acc'; }
                        if ( $m === 'outline' ) { $cls .= ' out'; }
                        if ( $m === 'gradient' ){ $cls .= ' grd'; }
                        echo '<span class="' . esc_attr( $cls ) . '">' . esc_html( $t ) . '</span>';
                    }
                ?></h1>
                <div class="glw-bottom">
                    <?php if ( ! empty( $s['subhead'] ) ) : ?><p class="glw-sub"><?php echo esc_html( $s['subhead'] ); ?></p><?php endif; ?>
                    <div class="glw-side">
                        <?php if ( ! empty( $s['cta1_text'] ) || ! empty( $s['cta2_text'] ) ) : ?>
                        <div class="glw-cta">
                            <?php if ( ! empty( $s['cta1_text'] ) ) : ?><a class="glw-btn glw-btn--solid" href="<?php echo esc_url( $s['cta1_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta1_text'] ); ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a><?php endif; ?>
                            <?php if ( ! empty( $s['cta2_text'] ) ) : ?><a class="glw-btn glw-btn--ghost" href="<?php echo esc_url( $s['cta2_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta2_text'] ); ?></a><?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php if ( ! empty( $s['show_scroll'] ) && ! empty( $s['scroll_text'] ) ) : ?><span class="glw-scroll"><span class="ln"></span><?php echo esc_html( $s['scroll_text'] ); ?></span><?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
        <?php
        // ── KIT standard: sistema bordi (base + hover + effetti) sul contenitore ──
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- border CSS generated by Olobuild_Tile_Base::build_border_*() helpers (intval sizes, fixed templates); $uid is an internal generated class name.
            echo '<style>';
            if ( $border_css ) {
                echo ".{$uid}{{$border_css}}";
            }
            echo $border_hover_css . $border_effect_css . '</style>';
            // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
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
