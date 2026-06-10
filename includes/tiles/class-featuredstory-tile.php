<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Hero — Featured Story : hero editoriale "lead" a 2 colonne — grande immagine di
 * copertina su un lato + colonna editoriale sull'altro (kicker + headline serif + standfirst/
 * deck + byline/meta, CTA opzionali). Parametrizzata per riprodurre i blueprint OLOthemes
 * "gazette" (cream/serif Caslon/claret, media 4/3 a sinistra — DEFAULT) e "voyage"
 * (panel navy/serif Vollkorn/coral, standfirst italic, media radius 8). Render == Vue
 * (FeaturedStoryTile.vue). Nessun JS.
 */
class Olo_FeaturedStory_Tile extends Olo_Tile_Base {

    protected $type     = 'featuredstory';
    protected $name     = 'Hero — Featured Story';
    protected $icon     = 'dashicons-media-document';
    protected $category = 'marketing';
    protected $defaults = [
        // Content
        'kicker_text'    => 'The Essay · Cities',
        'headline_text'  => 'The slow return of the city night market',
        'headline_url'   => '#',
        'standfirst'     => 'For a decade they were left for dead. Now, under the same lanterns, a new generation is rebuilding the night market — one stall, one recipe, one argument at a time.',
        'byline_pre'     => 'By',
        'byline_name'    => 'Elena Russo',
        'byline_meta'    => '18 min read',
        'cover_image'    => '',
        'cover_url'      => '#',
        'cover_label'    => 'cover — empty night market, lanterns, long exposure',
        // CTAs (optional)
        'cta1_text'      => '',
        'cta1_url'       => '#',
        'cta2_text'      => '',
        'cta2_url'       => '#',
        // Layout
        'media_side'     => 'left',     // left | right
        'col_ratio'      => '1.15fr .85fr',
        'cover_aspect'   => '4 / 3',
        'media_radius'   => 0,
        'standfirst_italic' => false,
        'placeholder_dark'  => true,   // true = dark ink lines/label (cream theme); false = light (navy theme)
        // Spaziatura (override gated) — root padding verticale è responsivo (clamp);
        // pad_custom=false (default) → clamp invariato.
        'pad_custom'     => false,
        'content_padding' => [ 'top' => 45, 'right' => 0, 'bottom' => 45, 'left' => 0 ],
        // Raggi per-angolo (additivi, no-op ai default).
        'cover_radius'   => [ 'tl' => 0, 'tr' => 0, 'br' => 0, 'bl' => 0 ],
        'cta_radius'     => [ 'tl' => 2, 'tr' => 2, 'br' => 2, 'bl' => 2 ],
        // Colors
        'bg_color'       => '#f3f0e9',
        'kicker_color'   => '#9a2b22',
        'headline_color' => '#16161a',
        'accent_color'   => '',
        'standfirst_color' => '#2c2c30',
        'byline_color'   => '#76746e',
        'byline_name_color' => '#16161a',
        'media_bg'       => '#e9e4d8',
        'cta_solid_bg'   => '#16161a',
        'cta_solid_text' => '#f3f0e9',
        // Fonts
        'heading_font'   => "var(--olo-font-family-heading, 'Libre Caslon Display', Georgia, serif)",
        'serif_font'     => "var(--olo-font-family-heading, 'Libre Caslon Text', Georgia, serif)",
        'sans_font'      => "var(--olo-font-family, 'Mulish', -apple-system, sans-serif)",

        // KIT standard OLObuild — sfondo completo + ombra + bordo sul contenitore.
        // Default no-op: bg 'none', shadow 'none', bordo 0 → render invariato.
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
        $uid = 'ofs-' . wp_rand( 10000, 99999 );

        $bg      = $this->safe_color_css( $s['bg_color'] ) ?: 'var(--olo-color-surface, #f3f0e9)';
        $kicker  = $this->safe_color_css( $s['kicker_color'] ) ?: 'var(--olo-color-primary, #9a2b22)';
        $hcol    = $this->safe_color_css( $s['headline_color'] ) ?: 'var(--olo-color-heading, #16161a)';
        $accent  = $this->safe_color_css( $s['accent_color'] ) ?: 'var(--olo-color-primary, #9a2b22)';
        $stand   = $this->safe_color_css( $s['standfirst_color'] ) ?: 'var(--olo-color-text, #2c2c30)';
        $byline  = $this->safe_color_css( $s['byline_color'] ) ?: 'var(--olo-color-muted, #76746e)';
        $bname   = $this->safe_color_css( $s['byline_name_color'] ) ?: $hcol;
        $mediabg = $this->safe_color_css( $s['media_bg'] ) ?: 'var(--olo-color-surface-2, #e9e4d8)';
        $csolid  = $this->safe_color_css( $s['cta_solid_bg'] ) ?: $hcol;
        $csoltxt = $this->safe_color_css( $s['cta_solid_text'] ) ?: $bg;

        $disp    = $this->resolve_font_family( (string) $s['heading_font'] ) ?: $this->defaults['heading_font'];
        $serif   = $this->resolve_font_family( (string) $s['serif_font'] ) ?: $this->defaults['serif_font'];
        $sans    = $this->resolve_font_family( (string) $s['sans_font'] ) ?: $this->defaults['sans_font'];

        $ratio   = preg_match( '/^[\d.\sfr]+$/', (string) $s['col_ratio'] ) ? (string) $s['col_ratio'] : '1.15fr .85fr';
        $aspect  = preg_match( '/^[\d.\s\/]+$/', (string) $s['cover_aspect'] ) ? (string) $s['cover_aspect'] : '4 / 3';
        $radius  = max( 0, intval( $s['media_radius'] ) );

        // ── Spaziatura (override gated): se pad_custom è true usa content_padding,
        //    altrimenti mantieni il clamp responsivo originale → default invariato. ──
        $root_pad = 'clamp(34px,5vw,56px) 0';
        if ( ! empty( $s['pad_custom'] ) ) {
            $cp = $s['content_padding'] ?? [];
            $pt = intval( $cp['top'] ?? 0 );
            $pr = intval( $cp['right'] ?? 0 );
            $pb = intval( $cp['bottom'] ?? 0 );
            $pl = intval( $cp['left'] ?? 0 );
            $root_pad = "{$pt}px {$pr}px {$pb}px {$pl}px";
        }

        // ── Raggio copertina (per-angolo): se tutti gli angoli sono 0 ricade su media_radius
        //    (default {0,0,0,0} → stringa "{$radius}px" originale, byte-per-byte invariata). ──
        $cover_br = $this->build_border_radius_css( $s['cover_radius'] ?? [] );
        $cover_radius_css = $cover_br !== '' ? $cover_br : ( $radius . 'px' );
        // ── Raggio pulsanti CTA: default {2,2,2,2} → ricade su '2px' originale (no-op). ──
        $cta_radius_css = $this->fs_uniform_radius( $s['cta_radius'] ?? [], 2 );
        $right   = ( (string) $s['media_side'] === 'right' );
        $standIt = ! empty( $s['standfirst_italic'] );
        $ph_dark = ! empty( $s['placeholder_dark'] );
        $ph_rgb  = $ph_dark ? '22,22,26' : '238,242,247';
        $ph_line = 'rgba(' . $ph_rgb . ',.05)';
        $ph_lbl  = 'rgba(' . $ph_rgb . ',' . ( $ph_dark ? '.4' : '.42' ) . ')';

        // media-first / editorial column order (media_side controls visual placement)
        $media_order = $right ? 2 : 1;
        $col_order   = $right ? 1 : 2;
        // when media on right, the grid ratio mirrors so the editorial keeps the wider/narrower slot intent
        $grid_ratio  = $right ? $this->mirror_ratio( $ratio ) : $ratio;

        $img   = trim( (string) ( $s['cover_image'] ?? '' ) );
        $cover_href = $this->safe_url( $s['cover_url'] );
        $head_href  = $this->safe_url( $s['headline_url'] );

        // ── KIT standard: sfondo completo (override del bg sezione SOLO se valorizzato) ──
        $bg_obj  = $s['bg'] ?? null;
        $bg_decl = '';
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olo_CSS_Builder' ) ) {
            $bg_decl = ( new Olo_CSS_Builder() )->get_bg_inline_css( $bg_obj );
        }
        // background del contenitore: il KIT vince se impostato, altrimenti il bg_color storico.
        $root_bg_css = $bg_decl !== '' ? rtrim( $bg_decl, ';' ) : ( 'background:' . $bg );

        // ── KIT standard: ombra ──
        $shadow_css = $this->build_shadow_decl( $s );

        // ── KIT standard: bordo (base/hover/effetti) sul contenitore .$uid ──
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?>{padding:<?php echo $root_pad; ?>;<?php echo $root_bg_css; ?>;font-family:<?php echo $sans; ?>;position:relative;<?php echo $border_css; ?><?php echo $shadow_css !== '' ? 'box-shadow:' . $shadow_css . ';' : ''; ?>}
            .<?php echo $uid; ?> .fs-in{max-width:1200px;margin:0 auto;padding:0 30px;display:grid;grid-template-columns:<?php echo esc_attr( $grid_ratio ); ?>;gap:48px;align-items:center;}
            .<?php echo $uid; ?> .fs-media{order:<?php echo $media_order; ?>;}
            .<?php echo $uid; ?> .fs-col{order:<?php echo $col_order; ?>;}
            .<?php echo $uid; ?> .fs-media a{display:block;}
            .<?php echo $uid; ?> .fs-frame{position:relative;display:block;overflow:hidden;aspect-ratio:<?php echo esc_attr( $aspect ); ?>;background:<?php echo $mediabg; ?>;border-radius:<?php echo $cover_radius_css; ?>;background-size:cover;background-position:center;<?php echo $img === '' ? 'background-image:repeating-linear-gradient(135deg, ' . $ph_line . ' 0 15px, transparent 15px 30px);' : 'background-image:url(' . esc_url( $img ) . ');'; ?>}
            <?php if ( $img === '' && ! empty( $s['cover_label'] ) ) : ?>
            .<?php echo $uid; ?> .fs-frame::after{content:"<?php echo esc_attr( $s['cover_label'] ); ?>";position:absolute;left:13px;right:13px;bottom:11px;font-family:<?php echo $sans; ?>;font-weight:600;font-size:10.5px;letter-spacing:.05em;text-transform:uppercase;color:<?php echo $ph_lbl; ?>;}
            <?php endif; ?>
            .<?php echo $uid; ?> .fs-kicker{display:block;margin-bottom:14px;font-family:<?php echo $sans; ?>;font-weight:700;font-size:11.5px;letter-spacing:.16em;text-transform:uppercase;color:<?php echo $kicker; ?>;}
            .<?php echo $uid; ?> .fs-h{font-family:<?php echo $disp; ?>;font-weight:400;font-size:clamp(38px,5.6vw,72px);line-height:1.02;letter-spacing:.002em;color:<?php echo $hcol; ?>;margin:0;}
            .<?php echo $uid; ?> .fs-h a{color:inherit;text-decoration:none;transition:color .2s;}
            .<?php echo $uid; ?> .fs-h a:hover{color:<?php echo $accent; ?>;}
            .<?php echo $uid; ?> .fs-stand{font-family:<?php echo $serif; ?>;font-style:<?php echo $standIt ? 'italic' : 'normal'; ?>;font-size:19px;line-height:1.55;color:<?php echo $stand; ?>;margin:20px 0 22px;}
            .<?php echo $uid; ?> .fs-byline{font-family:<?php echo $sans; ?>;font-size:12.5px;letter-spacing:.04em;text-transform:uppercase;color:<?php echo $byline; ?>;}
            .<?php echo $uid; ?> .fs-byline b{color:<?php echo $bname; ?>;font-weight:700;}
            .<?php echo $uid; ?> .fs-cta{display:flex;gap:12px;flex-wrap:wrap;margin-top:22px;}
            .<?php echo $uid; ?> .fs-btn{display:inline-flex;align-items:center;gap:8px;padding:13px 24px;border-radius:<?php echo $cta_radius_css; ?>;font-family:<?php echo $sans; ?>;font-weight:700;font-size:12.5px;letter-spacing:.08em;text-transform:uppercase;text-decoration:none;cursor:pointer;border:0;transition:transform .15s,background .2s,filter .2s;}
            .<?php echo $uid; ?> .fs-btn svg{width:15px;height:15px;}
            .<?php echo $uid; ?> .fs-btn:hover{transform:translateY(-2px);}
            .<?php echo $uid; ?> .fs-btn--solid{background:<?php echo $csolid; ?>;color:<?php echo $csoltxt; ?>;}
            .<?php echo $uid; ?> .fs-btn--solid:hover{filter:brightness(1.12);}
            .<?php echo $uid; ?> .fs-btn--ghost{background:transparent;color:<?php echo $hcol; ?>;border:1.5px solid <?php echo $byline; ?>;}
            .<?php echo $uid; ?> .fs-btn--ghost:hover{border-color:<?php echo $hcol; ?>;}
            .<?php echo $uid; ?> .fs-btn:focus-visible,.<?php echo $uid; ?> .fs-h a:focus-visible,.<?php echo $uid; ?> .fs-media a:focus-visible{outline:2px solid <?php echo $accent; ?>;outline-offset:3px;}
            @media(max-width:880px){.<?php echo $uid; ?> .fs-in{grid-template-columns:1fr;gap:28px;}.<?php echo $uid; ?> .fs-media{order:1;}.<?php echo $uid; ?> .fs-col{order:2;}}
        </style>
        <section class="olo-featuredstory <?php echo esc_attr( $uid ); ?>">
            <div class="fs-in">
                <div class="fs-media">
                    <?php if ( $cover_href !== '' ) : ?><a href="<?php echo esc_url( $cover_href ); ?>"><span class="fs-frame"></span></a><?php else : ?><span class="fs-frame"></span><?php endif; ?>
                </div>
                <div class="fs-col">
                    <?php if ( ! empty( $s['kicker_text'] ) ) : ?><span class="fs-kicker"><?php echo esc_html( $s['kicker_text'] ); ?></span><?php endif; ?>
                    <h1 class="fs-h"><?php if ( $head_href !== '' ) : ?><a href="<?php echo esc_url( $head_href ); ?>"><?php echo esc_html( $s['headline_text'] ); ?></a><?php else : ?><?php echo esc_html( $s['headline_text'] ); ?><?php endif; ?></h1>
                    <?php if ( ! empty( $s['standfirst'] ) ) : ?><p class="fs-stand"><?php echo esc_html( $s['standfirst'] ); ?></p><?php endif; ?>
                    <?php if ( ! empty( $s['byline_name'] ) || ! empty( $s['byline_meta'] ) ) : ?>
                    <div class="fs-byline"><?php
                        if ( ! empty( $s['byline_name'] ) ) {
                            if ( ! empty( $s['byline_pre'] ) ) { echo esc_html( $s['byline_pre'] ) . ' '; }
                            echo '<b>' . esc_html( $s['byline_name'] ) . '</b>';
                        }
                        if ( ! empty( $s['byline_name'] ) && ! empty( $s['byline_meta'] ) ) { echo ' · '; }
                        if ( ! empty( $s['byline_meta'] ) ) { echo esc_html( $s['byline_meta'] ); }
                    ?></div>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['cta1_text'] ) || ! empty( $s['cta2_text'] ) ) : ?>
                    <div class="fs-cta">
                        <?php if ( ! empty( $s['cta1_text'] ) ) : ?><a class="fs-btn fs-btn--solid" href="<?php echo esc_url( $s['cta1_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta1_text'] ); ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a><?php endif; ?>
                        <?php if ( ! empty( $s['cta2_text'] ) ) : ?><a class="fs-btn fs-btn--ghost" href="<?php echo esc_url( $s['cta2_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta2_text'] ); ?></a><?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php
        // ── KIT standard: bordo hover + effetti bordo (neon/gradiente…) ──
        if ( $border_hover_css || $border_effect_css ) {
            echo '<style>' . $border_hover_css . $border_effect_css . '</style>';
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

    /**
     * Border-radius da oggetto {tl,tr,br,bl}: se i 4 angoli sono uguali ritorna la
     * forma compatta "{n}px" (preserva byte-per-byte lo stile originale a 1 valore),
     * altrimenti la forma a 4 valori. $fallback usato se l'oggetto manca/è vuoto.
     */
    private function fs_uniform_radius( $br, $fallback ) {
        if ( ! is_array( $br ) ) { return intval( $fallback ) . 'px'; }
        $tl  = intval( $br['tl'] ?? $fallback );
        $tr  = intval( $br['tr'] ?? $fallback );
        $brr = intval( $br['br'] ?? $fallback );
        $bl  = intval( $br['bl'] ?? $fallback );
        if ( $tl === $tr && $tr === $brr && $brr === $bl ) {
            return "{$tl}px";
        }
        return "{$tl}px {$tr}px {$brr}px {$bl}px";
    }

    /** Swap the two track sizes of a "A B" grid-template-columns ratio. */
    private function mirror_ratio( $ratio ) {
        $parts = preg_split( '/\s+/', trim( (string) $ratio ) );
        if ( count( $parts ) === 2 ) { return $parts[1] . ' ' . $parts[0]; }
        return $ratio;
    }

    /** Allow '#' and ordinary URLs, return '' for empty/false. */
    private function safe_url( $url ) {
        $u = trim( (string) $url );
        if ( $u === '' ) { return ''; }
        if ( $u === '#' ) { return '#'; }
        return $u;
    }
}
