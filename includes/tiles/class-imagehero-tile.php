<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Hero — Image Overlay : hero full-bleed con IMMAGINE di sfondo, velo a gradiente
 * scuro (verticale top/bottom + opzionale laterale), blocco testo sovrapposto
 * (eyebrow + dot, titolo con parola-accento, sottotitolo, fino a 2 CTA, riga meta/data
 * opzionale, hint di scroll opzionale). Niente showcase/stat, niente split.
 *
 * Una SINGOLA proprietà `text_position` riproduce i diversi blueprint OLOthemes:
 *   - 'center'       → fiori (stack centrato, full-height)
 *   - 'bottom-left'  → loft (testo in basso a sinistra, velo top→bottom)
 *   - 'left'         → atelier/saffron/linea (sinistra, centrato in verticale)  ← DEFAULT
 *   - 'center-right' → atelier alt (destra, centrato in verticale)
 *
 * Default fedeli al blueprint Atelier (an-hero): immagine ~21/10, velo scuro laterale,
 * titolo serif con parola in corsivo/accento (oro), eyebrow oro, 2 CTA.
 * Render == Vue (ImageHeroTile.vue). Nessun JS.
 */
class Olo_ImageHero_Tile extends Olo_Tile_Base {

    protected $type     = 'imagehero';
    protected $name     = 'Hero — Image Overlay';
    protected $icon     = 'dashicons-format-image';
    protected $category = 'marketing';
    protected $defaults = [
        // contenuto
        'eyebrow_text'     => "Autumn / Winter '26",
        'eyebrow_dot'      => false,
        'headline_text'    => 'The',
        'accent_text'      => 'Nocturne',
        'headline_tail'    => 'Collection',
        'accent_italic'    => true,
        'stack_lines'      => false,
        'subhead'          => 'Tailoring for the hours after dark. Cut in wool crêpe and silk, finished by hand in our Paris atelier.',
        'cta1_text'        => 'Shop the collection',
        'cta1_url'         => '#',
        'cta2_text'        => 'View lookbook',
        'cta2_url'         => '#',
        'meta_text'        => '',
        'scroll_hint'      => '',
        // sfondo / media
        'bg_image'         => '',
        'media_bg'         => [ 'type' => 'none' ],
        'bg_color'         => '#0c0c0c',
        'media_label'      => 'campaign — figure in black tailoring, gold light, full bleed',
        // layout
        'text_position'    => 'left',
        'text_align'       => 'left',
        'content_width'    => 600,
        'aspect_ratio'     => '21/10',
        'min_height'       => 520,
        // tipografia
        'heading_font'     => 'serif',
        // colori velo
        'overlay_color'    => '#0c0c0c',
        'overlay_top'      => 0.2,
        'overlay_bottom'   => 0.75,
        'overlay_sides'    => true,
        // colori testo
        'accent'           => '',
        'accent_on'        => '#0c0c0c',
        'text_color'       => '#ffffff',
        'sub_color'        => '#efe9de',
        'eyebrow_color'    => '',
        // spaziatura / forma (additivi, default no-op)
        'pad_custom'       => false,
        'content_padding'  => [ 'top' => 60, 'right' => 32, 'bottom' => 60, 'left' => 32 ],
        'cta_radius'       => [ 'tl' => 2, 'tr' => 2, 'br' => 2, 'bl' => 2 ],
        'wrap_radius'      => [ 'tl' => 0, 'tr' => 0, 'br' => 0, 'bl' => 0 ],
        // kit standard OLObuild — sfondo completo + ombra + bordo (default no-op)
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

    /** "#rrggbb" → "r,g,b" (fallback su nero atelier se non valido). */
    private function hex_rgb( $hex, $fallback = '12,12,12' ) {
        $hex = ltrim( (string) $hex, '#' );
        if ( strlen( $hex ) === 3 ) { $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2]; }
        if ( ! preg_match( '/^[0-9a-fA-F]{6}$/', $hex ) ) { return $fallback; }
        return hexdec( substr($hex,0,2) ) . ',' . hexdec( substr($hex,2,2) ) . ',' . hexdec( substr($hex,4,2) );
    }

    private function clamp01( $v, $fb ) {
        $n = is_numeric( $v ) ? floatval( $v ) : $fb;
        return round( max( 0, min( 1, $n ) ), 3 );
    }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'oimh-' . wp_rand( 10000, 99999 );

        $bg      = $this->safe_color_css( $s['bg_color'] ) ?: '#0c0c0c';
        $accent  = $this->safe_color_css( $s['accent'] ) ?: 'var(--olo-color-primary, #c9a86a)';
        $accOn   = $this->safe_color_css( $s['accent_on'] ?? '' ) ?: '#0c0c0c';
        $txt     = $this->safe_color_css( $s['text_color'] ?? '' ) ?: '#ffffff';
        $sub     = $this->safe_color_css( $s['sub_color'] ?? '' ) ?: '#efe9de';
        $eyeCol  = $this->safe_color_css( $s['eyebrow_color'] ?? '' ) ?: $accent;

        $orgb    = $this->hex_rgb( $s['overlay_color'] ?? '#0c0c0c' );
        $oTop    = $this->clamp01( $s['overlay_top'] ?? 0.2, 0.2 );
        $oBot    = $this->clamp01( $s['overlay_bottom'] ?? 0.75, 0.75 );
        $oMid    = round( $oTop * 0.6, 3 );
        $sides   = ! empty( $s['overlay_sides'] );

        // posizione del blocco testo
        $pos = (string) ( $s['text_position'] ?? 'left' );
        $allowedPos = [ 'center', 'bottom-left', 'left', 'center-right' ];
        if ( ! in_array( $pos, $allowedPos, true ) ) { $pos = 'left'; }
        $align = in_array( ( $s['text_align'] ?? 'left' ), [ 'left', 'center', 'right' ], true ) ? $s['text_align'] : 'left';

        // mappa posizione → flex del contenitore
        if ( $pos === 'center' ) {
            $justifyV = 'center'; $alignH = 'center';
        } elseif ( $pos === 'bottom-left' ) {
            $justifyV = 'flex-end'; $alignH = 'flex-start';
        } elseif ( $pos === 'center-right' ) {
            $justifyV = 'center'; $alignH = 'flex-end';
        } else { // left
            $justifyV = 'center'; $alignH = 'flex-start';
        }
        $marginC = ( $align === 'center' ) ? '0 auto' : ( $align === 'right' ? '0 0 0 auto' : '0' );

        $cw      = max( 280, min( 1200, intval( $s['content_width'] ) ) );
        $ar      = preg_match( '#^\d+\s*/\s*\d+$#', (string) $s['aspect_ratio'] ) ? str_replace( ' ', '', $s['aspect_ratio'] ) : '21/10';
        $mh      = intval( $s['min_height'] );
        // min_height: numeri <=100 trattati come vh, altrimenti px (compat 520/540 px e 84/100 vh)
        $mhCss   = ( $mh > 0 && $mh <= 100 ) ? ( $mh . 'vh' ) : ( max( 200, $mh ) . 'px' );

        $disp    = ( ( $s['heading_font'] ?? 'serif' ) === 'sans' )
            ? "var(--olo-font-family, 'Work Sans',-apple-system,sans-serif)"
            : "var(--olo-font-family-heading, 'Marcellus','Cormorant Garamond',Georgia,serif)";
        $sans    = "var(--olo-font-family, 'Work Sans',-apple-system,sans-serif)";
        $italic  = ! empty( $s['accent_italic'] ) ? 'italic' : 'normal';

        $img     = trim( (string) ( $s['bg_image'] ?? '' ) );
        $imgCss  = $img !== ''
            ? 'url(' . esc_url( $img ) . ')'
            : 'repeating-linear-gradient(135deg, rgba(255,255,255,.05) 0 18px, rgba(255,255,255,0) 18px 36px)';
        // Media slot di OGNI tipo (stesso sistema bg dello Stile): override del placeholder.
        $mb = $this->bg_media_parts( $s['media_bg'] ?? null, $uid );
        $media_decl = ( $mb['has'] && $mb['css'] !== '' )
            ? $mb['css']
            : ( 'background-color:' . $bg . ';background-image:' . $imgCss . ';background-size:cover;background-position:center;' );

        // gradiente verticale (sempre) + laterale (opzionale, stile atelier)
        $gradV = 'linear-gradient(180deg, rgba(' . $orgb . ',' . $oTop . ') 0%, rgba(' . $orgb . ',' . $oMid . ') 38%, rgba(' . $orgb . ',' . $oBot . ') 100%)';
        $gradS = 'linear-gradient(90deg, rgba(' . $orgb . ',' . $oBot . ') 0%, rgba(' . $orgb . ',' . round( $oTop * 0.4, 3 ) . ') 52%, rgba(' . $orgb . ',' . round( $oBot * 0.7, 3 ) . ') 100%)';
        $grad  = $sides ? ( $gradS . ',' . $gradV ) : $gradV;

        $hasMeta   = ! empty( $s['meta_text'] );
        $hasScroll = ! empty( $s['scroll_hint'] );

        // ── Kit standard OLObuild: sfondo completo + ombra + bordo (sul contenitore) ──
        // Sfondo completo: override del background del contenitore SOLO se valorizzato
        // (type !== 'none') → con i default resta invariato.
        $bg_obj  = $s['bg'] ?? null;
        $bg_decl = '';
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olo_CSS_Builder' ) ) {
            $bg_decl = ( new Olo_CSS_Builder() )->get_bg_inline_css( $bg_obj );
        }
        // Ombra
        $shadow_css = $this->build_shadow_decl( $s );
        // Bordo (sistema standard)
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );

        // ── Spaziatura / Forma (additivi, default no-op) ──
        // Padding contenuto: responsivo (clamp/vh) di default; override SOLO se pad_custom on.
        $pad_css = 'clamp(40px,7vh,80px) 32px';
        if ( ! empty( $s['pad_custom'] ) ) {
            $cp = $s['content_padding'] ?? [];
            if ( is_array( $cp ) ) {
                $pt = absint( $cp['top'] ?? 0 ); $pr = absint( $cp['right'] ?? 0 );
                $pb = absint( $cp['bottom'] ?? 0 ); $pl = absint( $cp['left'] ?? 0 );
                $pad_css = "{$pt}px {$pr}px {$pb}px {$pl}px";
            }
        }
        // Raggio CTA (bottoni): default {2,2,2,2} = border-radius:2px attuale del .oih-btn.
        // Emessa SOLO se diversa dal default (≠ 2 su tutti gli angoli) → resa default invariata.
        $cr        = $s['cta_radius'] ?? [];
        $cr_def    = ( ! is_array( $cr ) ) || ( absint( $cr['tl'] ?? 0 ) === 2 && absint( $cr['tr'] ?? 0 ) === 2 && absint( $cr['br'] ?? 0 ) === 2 && absint( $cr['bl'] ?? 0 ) === 2 );
        $cta_radius_css = $cr_def ? '' : $this->build_border_radius_css( $cr );
        // Raggio contenitore: default {0,0,0,0} → build_border_radius_css() restituisce '' (no-op).
        $wrap_radius_css = $this->build_border_radius_css( $s['wrap_radius'] ?? [] );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?>{position:relative;overflow:hidden;background:<?php echo $bg; ?>;color:<?php echo $txt; ?>;font-family:<?php echo $sans; ?>;display:flex;flex-direction:column;justify-content:<?php echo $justifyV; ?>;min-height:<?php echo $mhCss; ?>;<?php echo $border_css; ?><?php if ( $wrap_radius_css ) { echo 'border-radius:' . $wrap_radius_css . ';'; } ?><?php if ( $shadow_css ) { echo 'box-shadow:' . $shadow_css . ';'; } ?>}
            <?php if ( $bg_decl ) : ?>.<?php echo $uid; ?>{<?php echo $bg_decl; ?>;}<?php endif; ?>
            .<?php echo $uid; ?> .oih-media{position:absolute;inset:0;z-index:0;<?php echo $media_decl; ?>aspect-ratio:<?php echo $ar; ?>;min-height:100%;}
            <?php if ( ! $mb['has'] && $img === '' && ! empty( $s['media_label'] ) ) : ?>
            .<?php echo $uid; ?> .oih-media::after{content:"<?php echo esc_attr( $s['media_label'] ); ?>";position:absolute;left:20px;bottom:16px;font-size:11px;letter-spacing:.04em;text-transform:uppercase;font-weight:600;color:rgba(255,255,255,.42);max-width:60%;}
            <?php endif; ?>
            .<?php echo $uid; ?> .oih-grad{position:absolute;inset:0;z-index:1;background:<?php echo $grad; ?>;}
            .<?php echo $uid; ?> .oih-in{position:relative;z-index:2;width:100%;max-width:1240px;margin:0 auto;padding:<?php echo $pad_css; ?>;display:flex;flex-direction:column;align-items:<?php echo $alignH; ?>;}
            .<?php echo $uid; ?> .oih-c{max-width:<?php echo $cw; ?>px;margin:<?php echo $marginC; ?>;text-align:<?php echo $align; ?>;display:flex;flex-direction:column;align-items:<?php echo ( $align === 'center' ? 'center' : ( $align === 'right' ? 'flex-end' : 'flex-start' ) ); ?>;}
            .<?php echo $uid; ?> .oih-eyebrow{display:inline-flex;align-items:center;gap:9px;font-family:<?php echo $sans; ?>;font-weight:600;font-size:11.5px;letter-spacing:.28em;text-transform:uppercase;color:<?php echo $eyeCol; ?>;margin:0 0 22px;}
            .<?php echo $uid; ?> .oih-dot{width:6px;height:6px;border-radius:50%;background:<?php echo $accent; ?>;box-shadow:0 0 8px <?php echo $accent; ?>;}
            .<?php echo $uid; ?> .oih-h{font-family:<?php echo $disp; ?>;font-weight:400;font-size:clamp(48px,8vw,104px);line-height:.98;letter-spacing:.005em;color:<?php echo $txt; ?>;margin:0;}
            .<?php echo $uid; ?> .oih-acc{color:<?php echo $accent; ?>;font-style:<?php echo $italic; ?>;}
            .<?php echo $uid; ?> .oih-sub{font-size:17px;line-height:1.6;color:<?php echo $sub; ?>;max-width:440px;margin:24px 0 0;}
            .<?php echo $uid; ?> .oih-meta{margin-top:22px;font-family:<?php echo $disp; ?>;font-size:clamp(13px,1.7vw,18px);letter-spacing:.16em;text-transform:uppercase;color:<?php echo $sub; ?>;display:flex;gap:14px;align-items:center;flex-wrap:wrap;<?php echo ( $align === 'center' ? 'justify-content:center;' : '' ); ?>}
            .<?php echo $uid; ?> .oih-cta{display:flex;gap:14px;flex-wrap:wrap;margin-top:32px;<?php echo ( $align === 'center' ? 'justify-content:center;' : '' ); ?>}
            .<?php echo $uid; ?> .oih-btn{display:inline-flex;align-items:center;gap:9px;padding:15px 28px;border-radius:2px;font-family:<?php echo $sans; ?>;font-weight:600;font-size:13px;letter-spacing:.04em;text-transform:uppercase;text-decoration:none;cursor:pointer;border:1px solid transparent;transition:transform .15s,background .2s,color .2s,filter .2s;}
            <?php if ( $cta_radius_css ) : ?>.<?php echo $uid; ?> .oih-btn{border-radius:<?php echo $cta_radius_css; ?>;}<?php endif; ?>
            .<?php echo $uid; ?> .oih-btn svg{width:16px;height:16px;}
            .<?php echo $uid; ?> .oih-btn:hover{transform:translateY(-2px);}
            .<?php echo $uid; ?> .oih-btn--solid{background:<?php echo $accent; ?>;color:<?php echo $accOn; ?>;}
            .<?php echo $uid; ?> .oih-btn--solid:hover{filter:brightness(1.08);}
            .<?php echo $uid; ?> .oih-btn--out{background:transparent;color:<?php echo $txt; ?>;border-color:rgba(255,255,255,.4);}
            .<?php echo $uid; ?> .oih-btn--out:hover{background:<?php echo $txt; ?>;color:<?php echo $bg; ?>;border-color:<?php echo $txt; ?>;}
            .<?php echo $uid; ?> .oih-btn:focus-visible{outline:2px solid <?php echo $accent; ?>;outline-offset:3px;}
            .<?php echo $uid; ?> .oih-scroll{position:absolute;bottom:28px;left:50%;transform:translateX(-50%);z-index:2;font-family:<?php echo $sans; ?>;font-size:11px;letter-spacing:.16em;text-transform:uppercase;color:rgba(255,255,255,.7);}
            @media(max-width:700px){.<?php echo $uid; ?> .oih-media{min-height:600px;}.<?php echo $uid; ?> .oih-cta{flex-direction:column;align-items:stretch;}}
        </style>
        <section class="olo-imagehero <?php echo esc_attr( $uid ); ?>">
            <div class="oih-media"><?php if ( $mb['has'] && $mb['markup'] !== '' ) { echo $mb['markup']; } ?></div>
            <div class="oih-grad"></div>
            <div class="oih-in">
                <div class="oih-c">
                    <?php if ( ! empty( $s['eyebrow_text'] ) ) : ?>
                        <span class="oih-eyebrow"><?php if ( ! empty( $s['eyebrow_dot'] ) ) : ?><span class="oih-dot"></span><?php endif; ?><?php echo esc_html( $s['eyebrow_text'] ); ?></span>
                    <?php endif; ?>
                    <h1 class="oih-h"><?php
                        $sep = ! empty( $s['stack_lines'] ) ? '<br>' : ' ';
                        echo esc_html( $s['headline_text'] );
                        if ( ! empty( $s['accent_text'] ) ) {
                            echo $sep . '<span class="oih-acc">' . esc_html( $s['accent_text'] ) . '</span>';
                        }
                        if ( ! empty( $s['headline_tail'] ) ) {
                            echo $sep . esc_html( $s['headline_tail'] );
                        }
                    ?></h1>
                    <?php if ( ! empty( $s['subhead'] ) ) : ?><p class="oih-sub"><?php echo esc_html( $s['subhead'] ); ?></p><?php endif; ?>
                    <?php if ( $hasMeta ) : ?><div class="oih-meta"><?php echo esc_html( $s['meta_text'] ); ?></div><?php endif; ?>
                    <?php if ( ! empty( $s['cta1_text'] ) || ! empty( $s['cta2_text'] ) ) : ?>
                    <div class="oih-cta">
                        <?php if ( ! empty( $s['cta1_text'] ) ) : ?><a class="oih-btn oih-btn--solid" href="<?php echo esc_url( $s['cta1_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta1_text'] ); ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a><?php endif; ?>
                        <?php if ( ! empty( $s['cta2_text'] ) ) : ?><a class="oih-btn oih-btn--out" href="<?php echo esc_url( $s['cta2_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta2_text'] ); ?></a><?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ( $hasScroll ) : ?><span class="oih-scroll"><?php echo esc_html( $s['scroll_hint'] ); ?></span><?php endif; ?>
        </section>
        <?php
        // ── Bordo: hover + effetti (neon/gradiente) sul contenitore ──
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
