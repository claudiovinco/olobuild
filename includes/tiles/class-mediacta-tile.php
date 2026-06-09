<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Media CTA — CTA full-bleed con media/video di sfondo + velo gradiente + contenuto
 * centrato (eyebrow + titolo gigante con accento + sub + CTA). Render == Vue. Nessun JS.
 * Valori 1:1 dal blueprint OLOthemes (.vd-cta).
 */
class Olo_MediaCTA_Tile extends Olo_Tile_Base {

    protected $type     = 'mediacta';
    protected $name     = 'Media CTA';
    protected $icon     = 'dashicons-megaphone';
    protected $category = 'marketing';
    protected $defaults = [
        'eyebrow' => 'Membership', 'eyebrow_color' => '',
        'headline' => 'Become a member of our', 'accent_text' => 'club', 'accent' => '', 'uppercase' => true, 'headline_color' => '#ffffff',
        'subhead' => '', 'subhead_color' => 'rgba(255,255,255,0.78)',
        'cta1_text' => 'Go to membership', 'cta1_url' => '#', 'cta2_text' => '', 'cta2_url' => '',
        'bg_image' => '', 'media_bg' => [ 'type' => 'none' ], 'media_label' => 'membership — supporters in the stands · background video',
        'overlay_color' => '#0a2a1e', 'overlay_top' => 0.78, 'overlay_bottom' => 0.9, 'overlay_type' => 'linear',
        'accent_on' => '#0a2a1e', 'accent_italic' => false, 'btn_bg' => '', 'btn_color' => '', 'headline_size' => '',
        'text_color' => '#ffffff', 'align' => 'center', 'pad_y' => 160,

        // SPAZIATURA (additivo, no-op coi default)
        'content_padding'         => [ 'top' => 0, 'right' => 28, 'bottom' => 0, 'left' => 28 ],
        'pad_custom'              => false,
        'root_padding'            => [ 'top' => 64, 'right' => 0, 'bottom' => 64, 'left' => 0 ],

        // FORMA — raggio pill dei pulsanti (default 999 = invariato)
        'btn_radius'              => [ 'tl' => 999, 'tr' => 999, 'br' => 999, 'bl' => 999 ],

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

    private function hex_rgb( $hex, $fallback = '10,42,30' ) {
        $hex = ltrim( (string) $hex, '#' );
        if ( strlen( $hex ) === 3 ) { $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2]; }
        if ( ! preg_match( '/^[0-9a-fA-F]{6}$/', $hex ) ) { return $fallback; }
        return hexdec( substr($hex,0,2) ) . ',' . hexdec( substr($hex,2,2) ) . ',' . hexdec( substr($hex,4,2) );
    }

    /**
     * Spacing object { top,right,bottom,left } → 'Tpx Rpx Bpx Lpx'.
     * $fb fornisce i valori di default (per il no-op se chiave assente).
     */
    private function pad_css( $v, $fb = [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 ] ) {
        if ( ! is_array( $v ) ) { $v = []; }
        $t = intval( $v['top']    ?? $fb['top'] );
        $r = intval( $v['right']  ?? $fb['right'] );
        $b = intval( $v['bottom'] ?? $fb['bottom'] );
        $l = intval( $v['left']   ?? $fb['left'] );
        return "{$t}px {$r}px {$b}px {$l}px";
    }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'omc-' . wp_rand( 10000, 99999 );

        $accent = $this->safe_color_css( $s['accent'] ?? '' ) ?: 'var(--olo-color-primary, #c8ff3c)';
        $accOn  = $this->safe_color_css( $s['accent_on'] ?? '' ) ?: '#0a2a1e';
        $txt    = $this->safe_color_css( $s['text_color'] ?? '' ) ?: '#ffffff';
        $eyec   = $this->safe_color_css( $s['eyebrow_color'] ?? '' ) ?: $accent;
        $subc   = $this->safe_color_css( $s['subhead_color'] ?? '' ) ?: 'rgba(255,255,255,0.78)';
        $orgb   = $this->hex_rgb( $s['overlay_color'] ?? '#0a2a1e' );
        $otop   = is_numeric( $s['overlay_top'] ) ? max( 0, min( 1, floatval( $s['overlay_top'] ) ) ) : 0.78;
        $obot   = is_numeric( $s['overlay_bottom'] ) ? max( 0, min( 1, floatval( $s['overlay_bottom'] ) ) ) : 0.9;
        $up     = ! empty( $s['uppercase'] ) ? 'uppercase' : 'none';
        $center = ( ( $s['align'] ?? 'center' ) === 'center' );
        $pad    = max( 60, min( 240, intval( $s['pad_y'] ) ) );
        $btnbg  = $this->safe_color_css( $s['btn_bg'] ?? '' ) ?: $accent;
        $btncol = $this->safe_color_css( $s['btn_color'] ?? '' ) ?: $accOn;
        $acc_it = ! empty( $s['accent_italic'] ) ? 'font-style:italic;' : '';
        $hsize  = intval( $s['headline_size'] ?? 0 );
        $h_fs   = $hsize > 0 ? "clamp(34px,6vw,{$hsize}px)" : 'clamp(40px,7.2vw,104px)';
        $otype  = ( ( $s['overlay_type'] ?? 'linear' ) === 'radial' ) ? 'radial' : 'linear';

        // ── SPAZIATURA (additivo, no-op coi default) ──
        // Padding verticale del root: responsive di default, override fisso se pad_custom.
        if ( ! empty( $s['pad_custom'] ) ) {
            $root_pad = 'padding:' . $this->pad_css( $s['root_padding'] ?? [], [ 'top' => 64, 'right' => 0, 'bottom' => 64, 'left' => 0 ] ) . ';';
        } else {
            $root_pad = 'padding:clamp(64px,12vw,' . $pad . 'px) 0;';
        }
        // Padding del contenuto interno (.omc-in): fisso '0 28px' di default.
        $in_pad = $this->pad_css( $s['content_padding'] ?? [], [ 'top' => 0, 'right' => 28, 'bottom' => 0, 'left' => 28 ] );
        // ── FORMA: raggio pill dei pulsanti (default 999 = invariato) ──
        $btn_radius = $this->build_border_radius_css( $s['btn_radius'] ?? [] ) ?: '999px';
        $disp   = "var(--olo-font-family-heading, 'Archivo',-apple-system,sans-serif)";
        $sans   = "var(--olo-font-family, 'Work Sans',-apple-system,sans-serif)";
        $img    = trim( (string) ( $s['bg_image'] ?? '' ) );
        $mediabg = $img !== '' ? 'url(' . esc_url( $img ) . ')' : 'repeating-linear-gradient(135deg, rgba(255,255,255,.05) 0 18px, rgba(255,255,255,0) 18px 36px)';
        $mb = $this->bg_media_parts( $s['media_bg'] ?? null, $uid );

        // ── KIT standard OLObuild: sfondo completo + ombra + bordo sul contenitore ──
        // Sfondo completo (override sul contenitore .$uid SOLO se valorizzato → default invariato).
        $bg_obj  = $s['bg'] ?? null;
        $bg_decl = '';
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olo_CSS_Builder' ) ) {
            $bg_decl = ( new Olo_CSS_Builder() )->get_bg_inline_css( $bg_obj );
        }
        // Ombra (preset/custom).
        $shadow_css = $this->build_shadow_decl( $s );
        // Bordo (base + hover + effetto), scoped al contenitore .$uid.
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        // Dichiarazioni extra da iniettare nella regola .$uid del contenitore.
        $kit_decl = '';
        if ( $bg_decl !== '' )    { $kit_decl .= $bg_decl . ';'; }
        if ( $shadow_css !== '' ) { $kit_decl .= 'box-shadow:' . $shadow_css . ';'; }
        if ( $border_css !== '' ) { $kit_decl .= $border_css; }

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?>{position:relative;overflow:hidden;color:<?php echo $txt; ?>;font-family:<?php echo $sans; ?>;text-align:<?php echo $center ? 'center' : 'left'; ?>;<?php echo $root_pad; ?><?php echo $kit_decl; ?>}
            .<?php echo $uid; ?> .omc-media{position:absolute;inset:0;z-index:0;<?php echo ( $mb['has'] && $mb['css'] !== '' ) ? $mb['css'] : ( 'background:' . ( $this->safe_color_css( $s['overlay_color'] ?? '' ) ?: '#0a2a1e' ) . ';background-image:' . $mediabg . ';background-size:cover;background-position:center;' ); ?>}
            .<?php echo $uid; ?> .omc-medialabel{position:absolute;left:18px;bottom:14px;z-index:1;font-size:11px;letter-spacing:.04em;text-transform:uppercase;font-weight:600;color:rgba(255,255,255,.4);}
            .<?php echo $uid; ?> .omc-grad{position:absolute;inset:0;z-index:1;background:<?php echo $otype === 'radial' ? 'radial-gradient(120% 100% at 50% 100%, rgba(' . $orgb . ',' . $obot . '), rgba(' . $orgb . ',' . $otop . '))' : 'linear-gradient(180deg, rgba(' . $orgb . ',' . $otop . '), rgba(' . $orgb . ',' . $obot . '))'; ?>;}
            .<?php echo $uid; ?> .omc-in{position:relative;z-index:2;max-width:1240px;margin:0 auto;padding:<?php echo $in_pad; ?>;}
            .<?php echo $uid; ?> .omc-eyebrow{font-weight:700;font-size:12px;letter-spacing:.18em;text-transform:uppercase;color:<?php echo $eyec; ?>;display:block;margin-bottom:18px;}
            .<?php echo $uid; ?> .omc-h{font-family:<?php echo $disp; ?>;font-weight:900;font-size:<?php echo $h_fs; ?>;line-height:.88;letter-spacing:-.01em;text-transform:<?php echo $up; ?>;margin:0;color:<?php echo $txt; ?>;}
            .<?php echo $uid; ?> .omc-h .acc{color:<?php echo $accent; ?>;<?php echo $acc_it; ?>}
            .<?php echo $uid; ?> .omc-sub{font-size:17px;line-height:1.6;color:<?php echo $subc; ?>;margin:18px auto 0;max-width:560px;<?php echo $center ? '' : 'margin-left:0;'; ?>}
            .<?php echo $uid; ?> .omc-cta{display:flex;gap:12px;flex-wrap:wrap;margin-top:32px;<?php echo $center ? 'justify-content:center;' : ''; ?>}
            .<?php echo $uid; ?> .omc-btn{display:inline-flex;align-items:center;gap:9px;padding:17px 30px;border-radius:<?php echo $btn_radius; ?>;font-weight:700;font-size:15px;text-decoration:none;border:0;transition:transform .15s,filter .2s;}
            .<?php echo $uid; ?> .omc-btn svg{width:17px;height:17px;}
            .<?php echo $uid; ?> .omc-btn:hover{transform:translateY(-2px);}
            .<?php echo $uid; ?> .omc-btn--solid{background:<?php echo $btnbg; ?>;color:<?php echo $btncol; ?>;}
            .<?php echo $uid; ?> .omc-btn--solid:hover{filter:brightness(1.06);}
            .<?php echo $uid; ?> .omc-btn--ghost{background:rgba(255,255,255,.08);color:<?php echo $txt; ?>;border:1.5px solid rgba(255,255,255,.26);}
            .<?php echo $uid; ?> .omc-btn:focus-visible{outline:2px solid <?php echo $accent; ?>;outline-offset:3px;}
        </style>
        <section class="olo-mediacta <?php echo esc_attr( $uid ); ?>">
            <div class="omc-media"><?php if ( $mb['has'] && $mb['markup'] !== '' ) { echo $mb['markup']; } ?><?php if ( ! $mb['has'] && $img === '' && ! empty( $s['media_label'] ) ) : ?><span class="omc-medialabel"><?php echo esc_html( $s['media_label'] ); ?></span><?php endif; ?></div>
            <div class="omc-grad"></div>
            <div class="omc-in">
                <?php if ( ! empty( $s['eyebrow'] ) ) : ?><span class="omc-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></span><?php endif; ?>
                <?php if ( ! empty( $s['headline'] ) ) : ?><h2 class="omc-h"><?php echo esc_html( $s['headline'] ); ?><?php if ( ! empty( $s['accent_text'] ) ) : ?> <span class="acc"><?php echo esc_html( $s['accent_text'] ); ?></span><?php endif; ?></h2><?php endif; ?>
                <?php if ( ! empty( $s['subhead'] ) ) : ?><p class="omc-sub"><?php echo esc_html( $s['subhead'] ); ?></p><?php endif; ?>
                <?php if ( ! empty( $s['cta1_text'] ) || ! empty( $s['cta2_text'] ) ) : ?>
                <div class="omc-cta">
                    <?php if ( ! empty( $s['cta1_text'] ) ) : ?><a class="omc-btn omc-btn--solid" href="<?php echo esc_url( $s['cta1_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta1_text'] ); ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a><?php endif; ?>
                    <?php if ( ! empty( $s['cta2_text'] ) ) : ?><a class="omc-btn omc-btn--ghost" href="<?php echo esc_url( $s['cta2_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta2_text'] ); ?></a><?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </section>
        <?php
        // ── KIT: bordo hover + effetto bordo (scoped .$uid) ──
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
}
