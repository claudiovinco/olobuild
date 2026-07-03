<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Intro Split — feature split: testo (eyebrow + titolo gigante con accento + lead +
 * N stat inline + CTA) accanto a media con badge d'angolo. Render == Vue. Nessun JS.
 * Valori 1:1 dal blueprint OLOthemes (.vd-intro).
 */
class Olobuild_IntroSplit_Tile extends Olobuild_Tile_Base {

    protected $type     = 'introsplit';
    protected $name     = 'Intro Split';
    protected $icon     = 'dashicons-align-pull-left';
    protected $category = 'marketing';
    protected $defaults = [
        'eyebrow' => 'One unit · since 1974', 'eyebrow_color' => '',
        'headline' => 'A regional club with a', 'accent' => 'rich history', 'headline_tail' => '', 'uppercase' => true,
        'headline_color' => '', 'accent_color' => '',
        'lead' => "From a handful of friends on a muddy field to eight competitive teams across men's, women's and youth football — Verdano FC is built on the people who keep showing up.",
        'lead_color' => '',
        'stats' => [
            [ 'number' => '50', 'label' => 'Years of football' ],
            [ 'number' => '8', 'label' => 'Competitive teams' ],
            [ 'number' => '600+', 'label' => 'Active members' ],
        ],
        'stat_number_color' => '', 'stat_label_color' => '',
        'cta_text' => 'About the club', 'cta_url' => '#', 'cta_bg' => '', 'cta_color' => '#ffffff',
        'cta2_text' => '', 'cta2_url' => '#', 'cta2_style' => 'outline',
        'media_image' => '', 'media_bg' => [ 'type' => 'none' ], 'media_label' => 'club portrait — squad on the pitch', 'media_light' => true,
        'media_aspect' => '4/4.4', 'media_radius' => 20, 'media_radius_top' => 0, 'media_blob' => false, 'media_blob_color' => '',
        // Spaziatura/forma additive — default no-op (padding gated OFF, raggi = valori attuali).
        'pad_custom'      => false,
        'content_padding' => [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 ],
        'badge_radius'    => [ 'tl' => 16, 'tr' => 16, 'br' => 16, 'bl' => 16 ],
        'cta_radius'      => [ 'tl' => 999, 'tr' => 999, 'br' => 999, 'bl' => 999 ],
        'badge_number' => '1974', 'badge_label' => 'Established', 'badge_bg' => '', 'badge_color' => '',
        'media_position' => 'right',
        'flush' => false, 'content_bg' => '', 'signature' => '', 'cta_style' => 'button',
        'accent_italic' => false, 'headline_weight' => '900', 'headline_size' => '',

        // KIT standard OLObuild — sfondo completo + ombra + bordo sul contenitore.
        // Default no-op (bg none, shadow none, border 0) → render invariato.
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
        $uid = 'ois-' . wp_rand( 10000, 99999 );

        $eyec  = $this->safe_color_css( $s['eyebrow_color'] ?? '' ) ?: 'var(--olo-color-secondary, #d33a55)';
        $accc  = $this->safe_color_css( $s['accent_color'] ?? '' ) ?: 'var(--olo-color-secondary, #d33a55)';
        $hcol  = $this->safe_color_css( $s['headline_color'] ?? '' ) ?: 'var(--olo-color-text, #142019)';
        $lcol  = $this->safe_color_css( $s['lead_color'] ?? '' ) ?: 'var(--olo-color-text-muted, #4f5b54)';
        $snc   = $this->safe_color_css( $s['stat_number_color'] ?? '' ) ?: 'var(--olo-color-text, #142019)';
        $slc   = $this->safe_color_css( $s['stat_label_color'] ?? '' ) ?: 'var(--olo-color-text-muted, #8a948d)';
        $ctabg = $this->safe_color_css( $s['cta_bg'] ?? '' ) ?: 'var(--olo-color-text, #0a2a1e)';
        $ctac  = $this->safe_color_css( $s['cta_color'] ?? '' ) ?: '#ffffff';
        $bbg   = $this->safe_color_css( $s['badge_bg'] ?? '' ) ?: 'var(--olo-color-primary, #c8ff3c)';
        $bcol  = $this->safe_color_css( $s['badge_color'] ?? '' ) ?: 'var(--olo-color-primary-contrast, #0a2a1e)';
        $asp   = preg_replace( '/[^0-9.\/]/', '', $s['media_aspect'] ?: '4/4.4' ) ?: '4/4.4';
        // media_radius dual-format: numero legacy O oggetto {tl,tr,br,bl} (type border-radius).
        $mr_raw = $s['media_radius'] ?? 20;
        $mr_t   = intval( $s['media_radius_top'] ?? 0 );
        if ( $mr_t > 0 ) {
            // Arco: angoli superiori da media_radius_top, inferiori da media_radius (scalare o br/bl dell'oggetto).
            $mr_br = is_array( $mr_raw ) ? intval( $mr_raw['br'] ?? 0 ) : intval( $mr_raw );
            $mr_bl = is_array( $mr_raw ) ? intval( $mr_raw['bl'] ?? 0 ) : intval( $mr_raw );
            $mrad  = "{$mr_t}px {$mr_t}px {$mr_br}px {$mr_bl}px";
        } else {
            $mrad = $this->build_border_radius_css( $mr_raw ) ?: '0px';
        }
        $blob_on    = ! empty( $s['media_blob'] );
        $blob_color = $this->safe_color_css( $s['media_blob_color'] ?? '' ) ?: 'var(--olo-color-primary, #e7a0b4)';

        // ── Spaziatura/forma additive (no-op ai default) ──
        // Padding gated: applicato al contenitore principale SOLO se pad_custom è true.
        $pad_decl = '';
        if ( ! empty( $s['pad_custom'] ) ) {
            $cp = is_array( $s['content_padding'] ?? null ) ? $s['content_padding'] : [];
            $pt = absint( $cp['top'] ?? 0 ); $pr = absint( $cp['right'] ?? 0 );
            $pb = absint( $cp['bottom'] ?? 0 ); $pl = absint( $cp['left'] ?? 0 );
            $pad_decl = "padding:{$pt}px {$pr}px {$pb}px {$pl}px;";
        }
        // Raggi badge + CTA via build_border_radius_css (default = valori attuali → invariato).
        // Collassa "Npx Npx Npx Npx" → "Npx" così il render coi default resta byte-identico.
        $brad_css = $this->radius_css_collapsed( $s['badge_radius'] ?? [], '16px' );
        $crad_css = $this->radius_css_collapsed( $s['cta_radius'] ?? [], '999px' );
        $up    = ! empty( $s['uppercase'] ) ? 'uppercase' : 'none';
        $light = ! empty( $s['media_light'] );
        $left  = ( ( $s['media_position'] ?? 'right' ) === 'left' );
        $disp  = "var(--olo-font-family-heading, 'Archivo',-apple-system,sans-serif)";
        $sans  = "var(--olo-font-family, 'Work Sans',-apple-system,sans-serif)";
        $ratio = $left ? '.95fr 1.05fr' : '1.05fr .95fr';
        // Flush 50/50 + tipografia titolo + firma + CTA underline (additivi, default no-op).
        $flush  = ! empty( $s['flush'] );
        $acc_it = ! empty( $s['accent_italic'] ) ? 'font-style:italic;' : '';
        $h_wt   = in_array( (string) ( $s['headline_weight'] ?? '900' ), [ '400', '500', '600', '700', '900' ], true ) ? (string) $s['headline_weight'] : '900';
        $hsz    = intval( $s['headline_size'] ?? 0 );
        $h_fs   = $hsz > 0 ? "clamp(30px,4.2vw,{$hsz}px)" : 'clamp(34px,5.2vw,68px)';
        $content_bg    = $this->safe_color_css( $s['content_bg'] ?? '' );
        $cta_underline = ( ( $s['cta_style'] ?? 'button' ) === 'underline' );
        $cta_outline   = ( ( $s['cta_style'] ?? 'button' ) === 'outline' );
        if ( $flush ) { $ratio = '1fr 1fr'; }
        $grid_gap   = $flush ? '0' : '54px';
        $grid_align = $flush ? 'stretch' : 'center';
        $content_decl = '';
        if ( $flush ) { $content_decl = 'display:flex;flex-direction:column;justify-content:center;padding:clamp(40px,6vw,80px);'; }
        if ( $content_bg !== '' ) { $content_decl .= 'background:' . $content_bg . ';'; }
        $media_size = $flush ? 'height:100%;min-height:440px;' : 'aspect-ratio:' . $asp . ';';
        $sigcol = $hcol;

        $img   = trim( (string) ( $s['media_image'] ?? '' ) );
        $media_bg = $light ? '#d8d2c2' : 'var(--olo-color-surface-alt, #0f3a2a)';
        $stripe   = $light ? 'rgba(20,32,25,.06)' : 'rgba(255,255,255,.05)';
        $lblcol   = $light ? 'rgba(20,32,25,.45)' : 'rgba(255,255,255,.4)';

        $stats = is_array( $s['stats'] ) ? array_values( $s['stats'] ) : [];

        // ── KIT: sfondo completo opzionale (override SOLO se valorizzato → default invariato) ──
        $bg_obj  = $s['bg'] ?? null;
        $bg_decl = '';
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olobuild_CSS_Builder' ) ) {
            $bg_decl = ( new Olobuild_CSS_Builder() )->get_bg_inline_css( $bg_obj );
        }
        // ── KIT: ombra ──
        $shadow_css = $this->build_shadow_decl( $s );

        // media block (riusato)
        $mb = $this->bg_media_parts( $s['media_bg'] ?? null, $uid );
        $media_sty = $mb['has'] ? ( $mb['css'] !== '' ? ' style="' . esc_attr( $mb['css'] ) . '"' : '' ) : ( $img !== '' ? ' style="background-image:url(' . esc_url( $img ) . ')"' : '' );
        ob_start();
        ?>
        <div class="ois-media"<?php echo $media_sty; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- style attribute built above from esc_attr()'d bg CSS or an esc_url()'d image URL only ?>>
            <?php if ( $mb['has'] && $mb['markup'] !== '' ) { echo $mb['markup']; } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- media markup generated by Olobuild_CSS_Builder::get_bg_html_markup(), which escapes its own output ?>
            <?php if ( ! $mb['has'] && $img === '' && ! empty( $s['media_label'] ) ) : ?><span class="ois-media__lbl"><?php echo esc_html( $s['media_label'] ); ?></span><?php endif; ?>
            <?php if ( ! empty( $s['badge_number'] ) || ! empty( $s['badge_label'] ) ) : ?>
            <div class="ois-badge"><?php if ( ! empty( $s['badge_number'] ) ) : ?><b><?php echo esc_html( $s['badge_number'] ); ?></b><?php endif; ?><?php if ( ! empty( $s['badge_label'] ) ) : ?><span><?php echo esc_html( $s['badge_label'] ); ?></span><?php endif; ?></div>
            <?php endif; ?>
        </div>
        <?php if ( $blob_on ) : ?><span class="ois-blob" aria-hidden="true"></span><?php endif; ?>
        <?php
        $media_html = ob_get_clean();

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: every color via the safe_color_css() whitelist (with var() fallbacks), integers via intval()/absint() clamps, aspect ratio via a [0-9./] preg_replace, weight/ratio/gap/align/transform from in_array() whitelists or fixed ternary literals, radii via build_border_radius_css() (integer-forced), font stacks are fixed internal literals; bg/shadow fragments esc_attr()'d inline; $uid is internally generated.
        ?>
        <style>
            .<?php echo $uid; ?>{position:relative;display:grid;grid-template-columns:<?php echo $ratio; ?>;gap:<?php echo $grid_gap; ?>;align-items:<?php echo $grid_align; ?>;font-family:<?php echo $sans; ?>;<?php echo $pad_decl; ?><?php echo $bg_decl ? esc_attr( $bg_decl ) . ';' : ''; ?><?php echo $shadow_css ? 'box-shadow:' . esc_attr( $shadow_css ) . ';' : ''; ?>}
            <?php if ( $content_decl !== '' ) : ?>.<?php echo $uid; ?> .ois-content{<?php echo $content_decl; ?>}<?php endif; ?>
            .<?php echo $uid; ?> .ois-eyebrow{font-weight:700;font-size:12px;letter-spacing:.18em;text-transform:uppercase;color:<?php echo $eyec; ?>;display:block;margin-bottom:16px;}
            .<?php echo $uid; ?> .ois-h{font-family:<?php echo $disp; ?>;font-weight:<?php echo $h_wt; ?>;font-size:<?php echo $h_fs; ?>;line-height:<?php echo $flush ? '1.05' : '.9'; ?>;letter-spacing:-.01em;text-transform:<?php echo $up; ?>;margin:0;color:<?php echo $hcol; ?>;}
            .<?php echo $uid; ?> .ois-h .acc{color:<?php echo $accc; ?>;<?php echo $acc_it; ?>}
            .<?php echo $uid; ?> .ois-sig{font-family:<?php echo $disp; ?>;font-style:italic;font-size:24px;color:<?php echo $sigcol; ?>;margin:18px 0 0;}
            .<?php echo $uid; ?> .ois-link{display:inline-block;margin-top:24px;font-weight:500;font-size:12px;letter-spacing:.18em;text-transform:uppercase;color:<?php echo $ctac; ?>;border-bottom:1px solid <?php echo $accc; ?>;padding-bottom:3px;text-decoration:none;transition:opacity .2s;}
            .<?php echo $uid; ?> .ois-link:hover{opacity:.7;}
            .<?php echo $uid; ?> .ois-link:focus-visible{outline:2px solid <?php echo $accc; ?>;outline-offset:3px;}
            .<?php echo $uid; ?> .ois-lead{margin:24px 0;font-size:16.5px;line-height:1.65;color:<?php echo $lcol; ?>;max-width:440px;}
            .<?php echo $uid; ?> .ois-stats{display:flex;gap:34px;margin:26px 0 30px;flex-wrap:wrap;}
            .<?php echo $uid; ?> .ois-stats b{font-family:<?php echo $disp; ?>;font-weight:900;font-size:46px;color:<?php echo $snc; ?>;display:block;line-height:1;}
            .<?php echo $uid; ?> .ois-stats span{font-size:12.5px;color:<?php echo $slc; ?>;text-transform:uppercase;letter-spacing:.06em;margin-top:5px;display:block;}
            .<?php echo $uid; ?> .ois-cta{display:inline-flex;align-items:center;gap:8px;background:<?php echo $ctabg; ?>;color:<?php echo $ctac; ?>;font-weight:700;font-size:14px;padding:14px 24px;border-radius:<?php echo $crad_css; ?>;text-decoration:none;transition:transform .15s,filter .2s;}
            .<?php echo $uid; ?> .ois-cta:hover{transform:translateY(-2px);filter:brightness(1.08);}
            .<?php echo $uid; ?> .ois-cta:focus-visible{outline:2px solid <?php echo $bbg; ?>;outline-offset:3px;}
            .<?php echo $uid; ?> .ois-cta--outline{background:transparent;color:<?php echo $ctac; ?>;border:1px solid <?php echo $accc; ?>;}
            .<?php echo $uid; ?> .ois-cta--outline:hover{background:<?php echo $ctac; ?>;color:#0c0c0c;filter:none;}
            .<?php echo $uid; ?> .ois-mediawrap{position:relative;}
            .<?php echo $uid; ?> .ois-media{position:relative;z-index:1;<?php echo $media_size; ?>border-radius:<?php echo $mrad; ?>;overflow:hidden;background:<?php echo $media_bg; ?>;background-size:cover;background-position:<?php echo esc_attr( Olobuild_Tile_Utils::focal_pos( $s, 'media_image' ) ); ?>;background-image:repeating-linear-gradient(135deg, <?php echo $stripe; ?> 0 18px, transparent 18px 36px);}
            <?php if ( $blob_on ) : ?>.<?php echo $uid; ?> .ois-blob{position:absolute;left:-24px;bottom:-24px;width:130px;height:130px;border-radius:50%;background:<?php echo $blob_color; ?>;mix-blend-mode:screen;opacity:.5;filter:blur(6px);pointer-events:none;z-index:0;}<?php endif; ?>
            .<?php echo $uid; ?> .ois-media__lbl{position:absolute;left:16px;bottom:14px;font-size:11px;letter-spacing:.04em;text-transform:uppercase;font-weight:600;color:<?php echo $lblcol; ?>;}
            .<?php echo $uid; ?> .ois-badge{position:absolute;left:-18px;bottom:24px;background:<?php echo $bbg; ?>;color:<?php echo $bcol; ?>;border-radius:<?php echo $brad_css; ?>;padding:18px 22px;box-shadow:0 18px 40px -16px rgba(10,42,30,.5);}
            .<?php echo $uid; ?> .ois-badge b{font-family:<?php echo $disp; ?>;font-weight:900;font-size:34px;display:block;line-height:1;}
            .<?php echo $uid; ?> .ois-badge span{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;}
            @media(max-width:880px){.<?php echo $uid; ?>{grid-template-columns:1fr;gap:34px;}}
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-introsplit <?php echo esc_attr( $uid ); ?>">
            <?php if ( $left ) : ?><div class="ois-mediawrap"><?php echo $media_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fragment buffered above with every dynamic value escaped at build time (esc_html/esc_url/esc_attr, Olobuild_CSS_Builder markup) ?></div><?php endif; ?>
            <div class="ois-content">
                <?php if ( ! empty( $s['eyebrow'] ) ) : ?><span class="ois-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></span><?php endif; ?>
                <?php if ( ! empty( $s['headline'] ) ) : ?><h2 class="ois-h"><?php echo esc_html( $s['headline'] ); ?><?php if ( ! empty( $s['accent'] ) ) : ?> <span class="acc"><?php echo esc_html( $s['accent'] ); ?></span><?php endif; ?><?php if ( ! empty( $s['headline_tail'] ) ) { echo esc_html( $s['headline_tail'] ); } ?></h2><?php endif; ?>
                <?php if ( ! empty( $s['lead'] ) ) : ?><p class="ois-lead"><?php echo esc_html( $s['lead'] ); ?></p><?php endif; ?>
                <?php if ( ! empty( $s['signature'] ) ) : ?><p class="ois-sig"><?php echo esc_html( $s['signature'] ); ?></p><?php endif; ?>
                <?php if ( ! empty( $stats ) ) : ?>
                <div class="ois-stats">
                    <?php foreach ( $stats as $st ) : ?><div><b><?php echo esc_html( $st['number'] ?? '' ); ?></b><span><?php echo esc_html( $st['label'] ?? '' ); ?></span></div><?php endforeach; ?>
                </div>
                <?php endif; ?>
                <?php
                $cta2_text  = trim( (string) ( $s['cta2_text'] ?? '' ) );
                $cta2_style = $s['cta2_style'] ?? 'outline';
                if ( ! empty( $s['cta_text'] ) || $cta2_text !== '' ) : ?>
                <div class="ois-ctas" style="display:flex;gap:13px;flex-wrap:wrap;align-items:center;margin-top:30px">
                    <?php if ( ! empty( $s['cta_text'] ) ) : ?><?php if ( $cta_underline ) : ?><a class="ois-link" href="<?php echo esc_url( $s['cta_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta_text'] ); ?></a><?php else : ?><a class="ois-cta<?php echo $cta_outline ? ' ois-cta--outline' : ''; ?>" href="<?php echo esc_url( $s['cta_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta_text'] ); ?></a><?php endif; ?><?php endif; ?>
                    <?php if ( $cta2_text !== '' ) : $c2u = ( $cta2_style === 'underline' ); $c2o = ( $cta2_style === 'outline' ); ?><?php if ( $c2u ) : ?><a class="ois-link" href="<?php echo esc_url( $s['cta2_url'] ?: '#' ); ?>"><?php echo esc_html( $cta2_text ); ?></a><?php else : ?><a class="ois-cta<?php echo $c2o ? ' ois-cta--outline' : ''; ?>" href="<?php echo esc_url( $s['cta2_url'] ?: '#' ); ?>"><?php echo esc_html( $cta2_text ); ?></a><?php endif; ?><?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php if ( ! $left ) : ?><div class="ois-mediawrap"><?php echo $media_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fragment buffered above with every dynamic value escaped at build time (esc_html/esc_url/esc_attr, Olobuild_CSS_Builder markup) ?></div><?php endif; ?>
        </div>
        <?php
        // ── KIT: sistema bordi standard (come particlefx) ─────────────────
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) {
                echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized border settings; $uid is internally generated
            }
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base border helpers from sanitized border settings
        }
        return ob_get_clean();
    }

    /**
     * Valore border-radius dal field type:'border-radius' (oggetto {tl,tr,br,bl}).
     * Usa build_border_radius_css e collassa i 4 angoli uguali in un solo valore
     * (es. "16px 16px 16px 16px" → "16px") per restare byte-identico al render storico.
     * Se il field è vuoto/zero restituisce il fallback fornito.
     */
    private function radius_css_collapsed( $br, $fallback ) {
        $css = $this->build_border_radius_css( is_array( $br ) ? $br : [] );
        if ( $css === '' ) {
            return $fallback;
        }
        $tl  = intval( $br['tl'] ?? 0 );
        $tr  = intval( $br['tr'] ?? 0 );
        $brr = intval( $br['br'] ?? 0 );
        $bl  = intval( $br['bl'] ?? 0 );
        if ( $tl === $tr && $tr === $brr && $brr === $bl ) {
            return $tl . 'px';
        }
        return $css;
    }

    /**
     * Restituisce la dichiarazione box-shadow (valore, senza "box-shadow:")
     * dal setting shadow (preset sm/md/lg/xl o custom). '' se none. (copia da particlefx)
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
