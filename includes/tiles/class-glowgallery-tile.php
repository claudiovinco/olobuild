<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Hero — Glow Gallery : hero EVENTI centrato, su fondo scuro, con
 * un GLOW radiale rosa sfocato dietro lo stack di testo (eyebrow, H1 serif gigante con
 * parola in corsivo accento, sub, fino a 2 CTA) e, SOTTO, una STRISCIA orizzontale di
 * tessere media verticali (3 di default, ratio 3/4) con angoli superiori molto arrotondati
 * e un OFFSET verticale sfalsato (la 2a tessera traslata in su ~-28px). Striscia = array
 * content-items ripetibile (immagine + didascalia opzionale). Nessun JS.
 * Meccanica firma = glow radiale + striscia gallery sfalsata.
 * Render == Vue (GlowGalleryTile.vue). Default fedeli al blueprint OLOthemes "Aurora".
 */
class Olo_GlowGallery_Tile extends Olo_Tile_Base {

    protected $type     = 'glowgallery';
    protected $name     = 'Hero — Glow Gallery';
    protected $icon     = 'dashicons-buddicons-activity';
    protected $category = 'marketing';
    protected $defaults = [
        // Testo centrato
        'eyebrow'        => 'Events studio · est. 2011',
        'headline_text'  => 'Celebrations worth',
        'accent_text'    => 'remembering.',
        'subhead'        => 'We design and produce weddings, galas and private events — the kind your guests talk about for years. From the first spark of an idea to the last dance.',
        'cta1_text'      => 'Start planning',
        'cta1_url'       => '#',
        'cta2_text'      => 'See our work',
        'cta2_url'       => '#',
        // Striscia gallery (3 tessere verticali sfalsate)
        'items'          => [
            [ 'image' => '', 'caption' => 'tablescape, candlelight' ],
            [ 'image' => '', 'caption' => 'ballroom, florals' ],
            [ 'image' => '', 'caption' => 'couple, golden hour' ],
        ],
        'strip_offset'   => 28,
        'strip_radius'   => 200,
        // Aspetto / glow
        'bg_color'       => '#241430',
        'accent'         => '',
        'accent_on'      => '#170c1f',
        'text_color'     => '#f3e9ef',
        'sub_color'      => '#c8b3c6',
        'eyebrow_color'  => '#e0afca',
        'media_bg'       => '#33203f',
        'glow_color'     => 'rgba(224,175,202,0.22)',
        'glow_w'         => 760,
        'glow_h'         => 520,
        'glow_blur'      => 120,
        'glow_y'         => -160,
        // Titolo
        'h_size_min'     => 48,
        'h_size_vw'      => 8,
        'h_size_max'     => 108,
        'max_width'      => 880,
        // Spaziatura & Raggio (additivi, no-op coi default)
        'content_padding'     => [ 'top' => 0, 'right' => 30, 'bottom' => 0, 'left' => 30 ],
        'btn_radius'          => [ 'tl' => 999, 'tr' => 999, 'br' => 999, 'bl' => 999 ],
        'media_radius_custom' => false,
        'media_radius'        => [ 'tl' => 200, 'tr' => 200, 'br' => 8, 'bl' => 8 ],
        // KIT standard OLObuild — sfondo completo + ombra + bordo (no-op coi default)
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
        $uid = 'oevh-' . wp_rand( 10000, 99999 );

        $bg     = $this->safe_color_css( $s['bg_color'] ) ?: '#241430';
        $accent = $this->safe_color_css( $s['accent'] ) ?: 'var(--olo-color-primary, #e0afca)';
        $accOn  = $this->safe_color_css( $s['accent_on'] ?? '' ) ?: '#170c1f';
        $txt    = $this->safe_color_css( $s['text_color'] ?? '' ) ?: '#f3e9ef';
        $sub    = $this->safe_color_css( $s['sub_color'] ?? '' ) ?: '#c8b3c6';
        $eyc    = $this->safe_color_css( $s['eyebrow_color'] ?? '' ) ?: '#e0afca';
        $mbg    = $this->safe_color_css( $s['media_bg'] ?? '' ) ?: '#33203f';
        $glow   = $this->safe_color_css( $s['glow_color'] ?? '' ) ?: 'rgba(224,175,202,0.22)';

        $gw     = max( 100, intval( $s['glow_w'] ) );
        $gh     = max( 100, intval( $s['glow_h'] ) );
        $gblur  = max( 0, intval( $s['glow_blur'] ) );
        $gy     = intval( $s['glow_y'] );

        $hmin   = max( 20, intval( $s['h_size_min'] ) );
        $hvw    = max( 1, floatval( $s['h_size_vw'] ) );
        $hmax   = max( $hmin, intval( $s['h_size_max'] ) );
        $mw     = max( 480, intval( $s['max_width'] ) );

        $soff   = max( 0, intval( $s['strip_offset'] ) );
        $srad   = max( 0, intval( $s['strip_radius'] ) );

        // ── Spaziatura & Raggio (additivi, no-op coi default) ────────────────
        // Padding contenuto: oggetto {top,right,bottom,left}px applicato a .evh-in.
        $cp     = $s['content_padding'] ?? null;
        if ( is_array( $cp ) ) {
            $cpt = intval( $cp['top'] ?? 0 );
            $cpr = intval( $cp['right'] ?? 30 );
            $cpb = intval( $cp['bottom'] ?? 0 );
            $cpl = intval( $cp['left'] ?? 30 );
        } else {
            $cpt = 0; $cpr = 30; $cpb = 0; $cpl = 30;
        }
        $in_pad = "{$cpt}px {$cpr}px {$cpb}px {$cpl}px";
        // Raggio CTA (pill di default): default {999,999,999,999} → invariato.
        $btn_rad = $this->build_border_radius_css( $s['btn_radius'] ?? [] );
        if ( $btn_rad === '' ) { $btn_rad = '999px'; }
        // Raggio tessere media: default = strip_radius (top) + 8px (bottom);
        // override SOLO se media_radius_custom è true → default invariato.
        $media_rad = "{$srad}px {$srad}px 8px 8px";
        if ( ! empty( $s['media_radius_custom'] ) ) {
            $mr = $this->build_border_radius_css( $s['media_radius'] ?? [] );
            if ( $mr !== '' ) { $media_rad = $mr; }
        }

        $disp   = "var(--olo-font-family-heading, 'Italiana', Didot, serif)";
        $sans   = "var(--olo-font-family, 'Tenor Sans', -apple-system, sans-serif)";

        $items  = is_array( $s['items'] ?? null ) ? $s['items'] : [];

        // ── KIT standard OLObuild — sfondo completo + ombra + bordo ──────────
        // Sfondo completo: override del bg di sezione SOLO se valorizzato → default invariato.
        $bg_obj  = $s['bg'] ?? null;
        $bg_decl = '';
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olo_CSS_Builder' ) ) {
            $bg_decl = ( new Olo_CSS_Builder() )->get_bg_inline_css( $bg_obj );
        }
        // Ombra (preset/custom): '' se none.
        $shadow_css = $this->build_shadow_decl( $s );
        // Bordo standard.
        $border_css = $this->build_border_css( $s['border'] ?? [] );

        // Coda CSS extra sul contenitore .$uid (background override + box-shadow + bordo).
        $kit_decl = '';
        if ( $bg_decl !== '' )    { $kit_decl .= $bg_decl . ';'; }
        if ( $shadow_css !== '' ) { $kit_decl .= 'box-shadow:' . $shadow_css . ';'; }
        if ( $border_css !== '' ) { $kit_decl .= $border_css; }

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist for every colour, intval()/floatval() with max() clamps for numbers, build_border_radius_css()/build_border_css()/build_shadow_decl()/Olo_CSS_Builder::get_bg_inline_css() helpers, fixed font-stack literals; $uid is internally generated. ?>
        <style>
            .<?php echo $uid; ?>{position:relative;overflow:hidden;text-align:center;background:<?php echo $bg; ?>;color:<?php echo $txt; ?>;font-family:<?php echo $sans; ?>;padding:clamp(64px,9vw,120px) 0;<?php echo $kit_decl; ?>}
            .<?php echo $uid; ?> .evh-glow{position:absolute;top:<?php echo $gy; ?>px;left:50%;transform:translateX(-50%);width:<?php echo $gw; ?>px;height:<?php echo $gh; ?>px;border-radius:50%;filter:blur(<?php echo $gblur; ?>px);pointer-events:none;background:radial-gradient(circle, <?php echo $glow; ?>, transparent 70%);z-index:0;}
            .<?php echo $uid; ?> .evh-in{position:relative;z-index:2;max-width:<?php echo $mw; ?>px;margin:0 auto;padding:<?php echo $in_pad; ?>;}
            .<?php echo $uid; ?> .evh-eyebrow{display:block;margin-bottom:22px;font-family:<?php echo $sans; ?>;font-size:12px;letter-spacing:.32em;text-transform:uppercase;color:<?php echo $eyc; ?>;}
            .<?php echo $uid; ?> .evh-h{font-family:<?php echo $disp; ?>;font-weight:400;font-size:clamp(<?php echo $hmin; ?>px,<?php echo $hvw; ?>vw,<?php echo $hmax; ?>px);line-height:1.02;letter-spacing:.01em;color:<?php echo $txt; ?>;margin:0;}
            .<?php echo $uid; ?> .evh-h .acc{font-style:italic;color:<?php echo $accent; ?>;}
            .<?php echo $uid; ?> .evh-sub{font-family:<?php echo $sans; ?>;font-size:18px;line-height:1.7;color:<?php echo $sub; ?>;max-width:520px;margin:26px auto 32px;}
            .<?php echo $uid; ?> .evh-cta{display:flex;gap:14px;justify-content:center;flex-wrap:wrap;}
            .<?php echo $uid; ?> .evh-btn{display:inline-flex;align-items:center;gap:10px;padding:14px 30px;border-radius:<?php echo $btn_rad; ?>;font-family:<?php echo $sans; ?>;font-size:13px;letter-spacing:.14em;text-transform:uppercase;text-decoration:none;cursor:pointer;border:0;transition:transform .25s,background .25s,color .25s,border-color .25s;}
            .<?php echo $uid; ?> .evh-btn svg{width:15px;height:15px;}
            .<?php echo $uid; ?> .evh-btn:hover{transform:translateY(-2px);}
            .<?php echo $uid; ?> .evh-btn--solid{background:<?php echo $accent; ?>;color:<?php echo $accOn; ?>;}
            .<?php echo $uid; ?> .evh-btn--solid:hover{filter:brightness(.92);}
            .<?php echo $uid; ?> .evh-btn--out{background:transparent;color:<?php echo $txt; ?>;border:1px solid rgba(224,175,202,.42);}
            .<?php echo $uid; ?> .evh-btn--out:hover{border-color:<?php echo $accent; ?>;color:<?php echo $accent; ?>;}
            .<?php echo $uid; ?> .evh-btn:focus-visible{outline:2px solid <?php echo $accent; ?>;outline-offset:3px;}
            .<?php echo $uid; ?> .evh-strip{position:relative;z-index:2;display:flex;gap:14px;justify-content:center;margin-top:clamp(40px,6vw,64px);flex-wrap:wrap;max-width:<?php echo max( $mw, 1180 ); ?>px;margin-left:auto;margin-right:auto;padding:0 30px;}
            .<?php echo $uid; ?> .evh-media{position:relative;overflow:hidden;width:clamp(150px,22vw,240px);aspect-ratio:3/4;border-radius:<?php echo $media_rad; ?>;background:<?php echo $mbg; ?>;background-image:repeating-linear-gradient(135deg, rgba(243,233,239,.05) 0 16px, transparent 16px 32px);background-size:cover;background-position:center;}
            .<?php echo $uid; ?> .evh-media:nth-child(2){margin-top:-<?php echo $soff; ?>px;}
            .<?php echo $uid; ?> .evh-media .evh-cap{position:absolute;left:14px;bottom:12px;right:14px;font-family:<?php echo $sans; ?>;font-size:10.5px;letter-spacing:.1em;color:rgba(243,233,239,.4);text-transform:uppercase;}
            @media(max-width:600px){.<?php echo $uid; ?> .evh-strip .evh-media:nth-child(3){display:none;}}
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <section class="olo-glowgallery <?php echo esc_attr( $uid ); ?>">
            <span class="evh-glow"></span>
            <div class="evh-in">
                <?php if ( ! empty( $s['eyebrow'] ) ) : ?><span class="evh-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></span><?php endif; ?>
                <h1 class="evh-h"><?php echo esc_html( $s['headline_text'] ); ?><?php if ( ! empty( $s['accent_text'] ) ) : ?> <span class="acc"><?php echo esc_html( $s['accent_text'] ); ?></span><?php endif; ?></h1>
                <?php if ( ! empty( $s['subhead'] ) ) : ?><p class="evh-sub"><?php echo esc_html( $s['subhead'] ); ?></p><?php endif; ?>
                <?php if ( ! empty( $s['cta1_text'] ) || ! empty( $s['cta2_text'] ) ) : ?>
                <div class="evh-cta">
                    <?php if ( ! empty( $s['cta1_text'] ) ) : ?><a class="evh-btn evh-btn--solid" href="<?php echo esc_url( $s['cta1_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta1_text'] ); ?></a><?php endif; ?>
                    <?php if ( ! empty( $s['cta2_text'] ) ) : ?><a class="evh-btn evh-btn--out" href="<?php echo esc_url( $s['cta2_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta2_text'] ); ?></a><?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php if ( ! empty( $items ) ) : ?>
            <div class="evh-strip">
                <?php foreach ( $items as $it ) :
                    $img = isset( $it['image'] ) ? trim( (string) $it['image'] ) : '';
                    $cap = isset( $it['caption'] ) ? (string) $it['caption'] : '';
                    $isty = $img !== '' ? ' style="background-image:url(' . esc_url( $img ) . ')"' : '';
                    ?>
                    <div class="evh-media"<?php echo $isty; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- style attribute built above with esc_url()'d image URL, or empty string ?>><?php if ( $cap !== '' ) : ?><span class="evh-cap"><?php echo esc_html( $cap ); ?></span><?php endif; ?></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>
        <?php
        // ── KIT standard OLObuild — bordo hover + effetti bordo ──────────────
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_hover_css || $border_effect_css ) {
            echo '<style>' . $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized border settings
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
