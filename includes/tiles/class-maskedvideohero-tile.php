<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Hero — Masked Video : pannello full-bleed mascherato (arco inferiore), media/video
 * di sfondo (slot, strisce se vuoto), watermark ghost gigante, pill+dot, H1 gigante
 * uppercase con parola-accento, riga sub+2 CTA. Render == Vue (MaskedVideoHeroTile.vue).
 * Nessun JS. Estratta dal blueprint OLOthemes "MaskedVideoHero".
 */
class Olo_MaskedVideoHero_Tile extends Olo_Tile_Base {

    protected $type     = 'maskedvideohero';
    protected $name     = 'Hero — Masked Video';
    protected $icon     = 'dashicons-format-video';
    protected $category = 'marketing';
    protected $defaults = [
        'tag_text'       => 'Next home game · Sat 14 Mar · 15:00',
        'tag_dot_color'  => '',
        'headline_text'  => 'Forged on the',
        'accent_text'    => 'pitch.',
        'uppercase'      => true,
        'subhead'        => 'Eight teams, one badge. Verdano FC has played, fought and grown in this city for fifty years — and we’re only getting started.',
        'cta1_text'      => 'View fixtures',
        'cta1_url'       => '#',
        'cta2_text'      => 'Become a member',
        'cta2_url'       => '#',
        'bg_color'       => '#0a2a1e',
        'bg_image'       => '',
        'media_label'    => 'home hero — match footage · background video',
        'overlay_color'  => '#0a2a1e',
        'overlay_strength' => 0.55,
        'watermark_text' => 'VFC',
        'watermark_color'=> 'rgba(255,255,255,0.055)',
        'accent'         => '',
        'accent_on'      => '#0a2a1e',
        'text_color'     => '#ffffff',
        'sub_color'      => 'rgba(255,255,255,0.72)',
        'arch'           => true,
        'transparent_bg' => false,
        'min_height'     => 84,
    ];

    public function get_controls() { return []; }

    /** "#rrggbb" → "r,g,b" (fallback su pitch se non valido). */
    private function hex_rgb( $hex, $fallback = '10,42,30' ) {
        $hex = ltrim( (string) $hex, '#' );
        if ( strlen( $hex ) === 3 ) { $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2]; }
        if ( ! preg_match( '/^[0-9a-fA-F]{6}$/', $hex ) ) { return $fallback; }
        return hexdec( substr($hex,0,2) ) . ',' . hexdec( substr($hex,2,2) ) . ',' . hexdec( substr($hex,4,2) );
    }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'omvh-' . wp_rand( 10000, 99999 );

        $bg     = $this->safe_color_css( $s['bg_color'] ) ?: '#0a2a1e';
        $accent = $this->safe_color_css( $s['accent'] ) ?: 'var(--olo-color-primary, #c8ff3c)';
        $accOn  = $this->safe_color_css( $s['accent_on'] ?? '' ) ?: '#0a2a1e';
        $txt    = $this->safe_color_css( $s['text_color'] ?? '' ) ?: '#ffffff';
        $sub    = $this->safe_color_css( $s['sub_color'] ?? '' ) ?: 'rgba(255,255,255,0.72)';
        $dot    = $this->safe_color_css( $s['tag_dot_color'] ?? '' ) ?: $accent;
        $wm     = $this->safe_color_css( $s['watermark_color'] ?? '' ) ?: 'rgba(255,255,255,0.055)';
        $orgb   = $this->hex_rgb( $s['overlay_color'] ?? '#0a2a1e' );
        $st     = is_numeric( $s['overlay_strength'] ) ? floatval( $s['overlay_strength'] ) : 0.55;
        $a_top  = round( min( 0.96, $st * 0.9 ), 3 );
        $a_mid  = round( min( 0.96, $st * 0.27 ), 3 );
        $a_bot  = round( min( 0.97, $st * 1.7 ), 3 );
        $mh     = max( 50, min( 100, intval( $s['min_height'] ) ) );
        $arch   = ! empty( $s['arch'] );
        $up     = ! empty( $s['uppercase'] ) ? 'uppercase' : 'none';
        $disp   = "var(--olo-font-family-heading, 'Archivo',-apple-system,sans-serif)";
        $sans   = "var(--olo-font-family, 'Work Sans',-apple-system,sans-serif)";

        $img    = trim( (string) ( $s['bg_image'] ?? '' ) );
        $transp = ! empty( $s['transparent_bg'] );
        $bgc    = $transp ? 'transparent' : $bg;
        $mask   = $arch ? 'radial-gradient(150% 125% at 50% 0%, #000 87%, transparent 87.5%)' : 'none';

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?>{position:relative;overflow:hidden;min-height:<?php echo $mh; ?>vh;display:flex;align-items:center;color:<?php echo $txt; ?>;font-family:<?php echo $sans; ?>;}
            .<?php echo $uid; ?> .mvh-bg{position:absolute;inset:0;z-index:0;overflow:hidden;background:<?php echo $bgc; ?>;-webkit-mask:<?php echo $mask; ?>;mask:<?php echo $mask; ?>;}
            .<?php echo $uid; ?> .mvh-media{position:absolute;inset:0;background:<?php echo $bgc; ?>;background-image:<?php echo $img !== '' ? 'url(' . esc_url( $img ) . ')' : ( $transp ? 'none' : 'repeating-linear-gradient(135deg, rgba(255,255,255,.05) 0 18px, rgba(255,255,255,0) 18px 36px)' ); ?>;background-size:cover;background-position:center;}
            <?php if ( $img === '' && ! $transp && ! empty( $s['media_label'] ) ) : ?>
            .<?php echo $uid; ?> .mvh-media::after{content:"<?php echo esc_attr( $s['media_label'] ); ?>";position:absolute;left:18px;bottom:14px;font-size:11px;letter-spacing:.04em;text-transform:uppercase;font-weight:600;color:rgba(255,255,255,.4);}
            <?php endif; ?>
            .<?php echo $uid; ?> .mvh-grad{position:absolute;inset:0;z-index:1;background:<?php echo $transp ? 'none' : 'linear-gradient(180deg, rgba(' . $orgb . ',' . $a_top . ') 0%, rgba(' . $orgb . ',' . $a_mid . ') 38%, rgba(' . $orgb . ',' . $a_bot . ') 100%)'; ?>;}
            .<?php echo $uid; ?> .mvh-ghost{position:absolute;top:6%;left:50%;transform:translateX(-50%);z-index:1;font-family:<?php echo $disp; ?>;font-weight:900;font-size:min(34vw,420px);line-height:1;color:<?php echo $wm; ?>;letter-spacing:-.02em;pointer-events:none;white-space:nowrap;}
            .<?php echo $uid; ?> .mvh-in{position:relative;z-index:2;width:100%;max-width:1240px;margin:0 auto;padding:clamp(36px,7vh,80px) 28px clamp(70px,12vh,130px);}
            .<?php echo $uid; ?> .mvh-tag{display:inline-flex;align-items:center;gap:10px;margin-bottom:22px;padding:8px 16px;border-radius:999px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.16);font-size:12.5px;font-weight:700;letter-spacing:.06em;}
            .<?php echo $uid; ?> .mvh-dot{width:8px;height:8px;border-radius:50%;background:<?php echo $dot; ?>;box-shadow:0 0 10px <?php echo $dot; ?>;}
            .<?php echo $uid; ?> .mvh-h{font-family:<?php echo $disp; ?>;font-weight:900;font-size:clamp(48px,8.4vw,128px);line-height:.86;letter-spacing:-.01em;text-transform:<?php echo $up; ?>;color:<?php echo $txt; ?>;margin:0;}
            .<?php echo $uid; ?> .mvh-acc{color:<?php echo $accent; ?>;}
            .<?php echo $uid; ?> .mvh-row{display:flex;align-items:flex-end;justify-content:space-between;gap:30px;flex-wrap:wrap;margin-top:30px;}
            .<?php echo $uid; ?> .mvh-sub{max-width:440px;font-size:16.5px;line-height:1.6;color:<?php echo $sub; ?>;margin:0;}
            .<?php echo $uid; ?> .mvh-cta{display:flex;gap:12px;flex-wrap:wrap;}
            .<?php echo $uid; ?> .mvh-btn{display:inline-flex;align-items:center;gap:9px;padding:17px 30px;border-radius:999px;font-family:<?php echo $sans; ?>;font-weight:700;font-size:15px;text-decoration:none;cursor:pointer;border:0;transition:transform .15s,background .2s,filter .2s;}
            .<?php echo $uid; ?> .mvh-btn svg{width:17px;height:17px;}
            .<?php echo $uid; ?> .mvh-btn:hover{transform:translateY(-2px);}
            .<?php echo $uid; ?> .mvh-btn--solid{background:<?php echo $accent; ?>;color:<?php echo $accOn; ?>;}
            .<?php echo $uid; ?> .mvh-btn--solid:hover{filter:brightness(1.06);}
            .<?php echo $uid; ?> .mvh-btn--ghost{background:rgba(255,255,255,.06);color:<?php echo $txt; ?>;border:1.5px solid rgba(255,255,255,.26);}
            .<?php echo $uid; ?> .mvh-btn:focus-visible{outline:2px solid <?php echo $accent; ?>;outline-offset:3px;}
            @media(max-width:680px){.<?php echo $uid; ?> .mvh-row{flex-direction:column;align-items:flex-start;}}
        </style>
        <section class="olo-maskedvideohero <?php echo esc_attr( $uid ); ?>">
            <div class="mvh-bg">
                <div class="mvh-media"></div>
                <div class="mvh-grad"></div>
                <?php if ( ! empty( $s['watermark_text'] ) ) : ?><div class="mvh-ghost"><?php echo esc_html( $s['watermark_text'] ); ?></div><?php endif; ?>
            </div>
            <div class="mvh-in">
                <?php if ( ! empty( $s['tag_text'] ) ) : ?><span class="mvh-tag"><span class="mvh-dot"></span><?php echo esc_html( $s['tag_text'] ); ?></span><?php endif; ?>
                <h1 class="mvh-h"><?php echo esc_html( $s['headline_text'] ); ?><?php if ( ! empty( $s['accent_text'] ) ) : ?> <span class="mvh-acc"><?php echo esc_html( $s['accent_text'] ); ?></span><?php endif; ?></h1>
                <div class="mvh-row">
                    <?php if ( ! empty( $s['subhead'] ) ) : ?><p class="mvh-sub"><?php echo esc_html( $s['subhead'] ); ?></p><?php endif; ?>
                    <div class="mvh-cta">
                        <?php if ( ! empty( $s['cta1_text'] ) ) : ?><a class="mvh-btn mvh-btn--solid" href="<?php echo esc_url( $s['cta1_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta1_text'] ); ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a><?php endif; ?>
                        <?php if ( ! empty( $s['cta2_text'] ) ) : ?><a class="mvh-btn mvh-btn--ghost" href="<?php echo esc_url( $s['cta2_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta2_text'] ); ?></a><?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }
}
