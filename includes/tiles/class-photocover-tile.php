<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Hero — Photo Cover (Frame) : full-bleed editorial photo-cover hero. Large framed
 * cover photograph (slot, diagonal-stripe placeholder if empty) with an editorial
 * typographic overlay anchored to the bottom-left: mono kicker, giant uppercase headline
 * and a row of mono meta/byline lines. Signature: the precise photographic frame +
 * top→bottom darkening gradient that lets the image lead while the caption stays legible.
 * Render == Vue (PhotoCoverTile.vue). No JS. Extracted pixel-perfect from the OLOthemes
 * "Frame" (Media & News) blueprint hero (.fr-cover).
 */
class Olo_PhotoCover_Tile extends Olo_Tile_Base {

    protected $type     = 'photocover';
    protected $name     = 'Hero — Photo Cover';
    protected $icon     = 'dashicons-format-image';
    protected $category = 'marketing';
    protected $defaults = [
        'kicker_text'      => 'Photo Essay · Issue 41',
        'headline_text'    => 'The City After Rain',
        'uppercase'        => true,
        'meta_items'       => [
            [ 'text' => 'Photographs · Yuki Mori' ],
            [ 'text' => '28 frames' ],
            [ 'text' => '12 min' ],
        ],
        'bg_image'         => '',
        'media_label'      => 'cover photograph — rain-soaked city street, single figure',
        'aspect_ratio'     => '16/9',
        'min_height'       => 560,
        'overlay_top'      => 0.3,
        'overlay_bottom'   => 0.85,
        'frame_padding'    => 28,
        'media_bg'         => '#1a1a1a',
        'kicker_color'     => '',
        'headline_color'   => '#ffffff',
        'meta_color'       => '#e8e8e8',

        // SPAZIATURA — override gated del padding del contenuto (.pc-in).
        // Default no-op: con pad_custom=false resta il clamp responsivo originale.
        'pad_custom'       => false,
        'content_padding'  => [ 'top' => 28, 'right' => 28, 'bottom' => 28, 'left' => 28 ],

        // FORMA — raggio della foto di copertina (.pc-media). Default 0 = no-op.
        'media_radius'     => [ 'tl' => 0, 'tr' => 0, 'br' => 0, 'bl' => 0 ],

        // KIT standard OLObuild (sfondo + ombra + bordo) sul contenitore principale.
        // Default no-op: con questi valori il render resta identico a prima.
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
        $uid = 'opc-' . wp_rand( 10000, 99999 );

        $mediaBg  = $this->safe_color_css( $s['media_bg'] ?? '' ) ?: '#1a1a1a';
        $kicker   = $this->safe_color_css( $s['kicker_color'] ?? '' ) ?: 'var(--olo-color-primary, #ff4438)';
        $headline = $this->safe_color_css( $s['headline_color'] ?? '' ) ?: '#ffffff';
        $meta     = $this->safe_color_css( $s['meta_color'] ?? '' ) ?: '#e8e8e8';

        $aTop = is_numeric( $s['overlay_top'] ) ? max( 0, min( 1, floatval( $s['overlay_top'] ) ) ) : 0.3;
        $aBot = is_numeric( $s['overlay_bottom'] ) ? max( 0, min( 1, floatval( $s['overlay_bottom'] ) ) ) : 0.85;
        $aTop = round( $aTop, 3 );
        $aBot = round( $aBot, 3 );

        $ar   = preg_match( '#^\d+\s*/\s*\d+$#', (string) $s['aspect_ratio'] ) ? $s['aspect_ratio'] : '16/9';
        $mh   = max( 0, min( 1400, intval( $s['min_height'] ) ) );
        $fp   = max( 0, min( 200, intval( $s['frame_padding'] ) ) );
        $up   = ! empty( $s['uppercase'] ) ? 'uppercase' : 'none';

        $disp = "var(--olo-font-family-heading, 'Archivo',-apple-system,sans-serif)";
        $sans = "var(--olo-font-family, 'Archivo',-apple-system,sans-serif)";
        $mono = "var(--olo-font-family-mono, 'Archivo Narrow','Archivo',sans-serif)";

        $img    = trim( (string) ( $s['bg_image'] ?? '' ) );
        $mediaImg = $img !== ''
            ? 'url(' . esc_url( $img ) . ')'
            : 'repeating-linear-gradient(135deg, rgba(255,255,255,.04) 0 16px, transparent 16px 32px)';

        // clamp padding faithful to blueprint clamp(28px,5vw,56px) at default; driven by frame_padding (lo=fp, hi=fp*2).
        $pad = 'clamp(' . max( 8, $fp ) . 'px,5vw,' . max( 16, $fp * 2 ) . 'px)';

        // SPAZIATURA (gated): se pad_custom è true il padding del contenuto usa il field
        // content_padding (top/right/bottom/left in px); altrimenti resta il clamp responsivo.
        if ( ! empty( $s['pad_custom'] ) && is_array( $s['content_padding'] ?? null ) ) {
            $cp  = $s['content_padding'];
            $pt  = max( 0, intval( $cp['top'] ?? 0 ) );
            $pr  = max( 0, intval( $cp['right'] ?? 0 ) );
            $pb  = max( 0, intval( $cp['bottom'] ?? 0 ) );
            $pl  = max( 0, intval( $cp['left'] ?? 0 ) );
            $pad = "{$pt}px {$pr}px {$pb}px {$pl}px";
        }

        // FORMA: raggio della foto di copertina (.pc-media). '' (default 0) = no-op.
        $media_radius = $this->build_border_radius_css( $s['media_radius'] ?? [] );

        $metaItems = is_array( $s['meta_items'] ?? null ) ? $s['meta_items'] : [];

        // ── KIT standard: sfondo completo opzionale (override SOLO se valorizzato) ──
        $bg_obj  = $s['bg'] ?? null;
        $bg_decl = '';
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olo_CSS_Builder' ) ) {
            $bg_decl = ( new Olo_CSS_Builder() )->get_bg_inline_css( $bg_obj );
        }
        // ── KIT standard: ombra (preset/custom) ──
        $shadow_css = $this->build_shadow_decl( $s );
        // ── KIT standard: bordo ──
        $border_css = $this->build_border_css( $s['border'] ?? [] );

        // Decl extra applicate alla regola .$uid del contenitore principale.
        $root_extra = '';
        if ( $bg_decl !== '' )    { $root_extra .= rtrim( $bg_decl, ';' ) . ';'; }
        if ( $border_css !== '' ) { $root_extra .= $border_css; }
        if ( $shadow_css !== '' ) { $root_extra .= "box-shadow:{$shadow_css};"; }

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: colours via the safe_color_css() whitelist, overlay alphas via floatval() clamps, sizes/padding via intval() clamps (or fixed clamp() literal), aspect ratio via preg_match() whitelist, image via esc_url() (or fixed gradient literal), media label esc_attr()'d inline, fixed font-stack literals, root decorations via the Olo_CSS_Builder/Olo_Tile_Base shared helpers (sanitized internally); $uid is internally generated. ?>
        <style>
            .<?php echo $uid; ?>{position:relative;overflow:hidden;font-family:<?php echo $sans; ?>;<?php echo $root_extra; ?>}
            .<?php echo $uid; ?> .pc-media{position:relative;overflow:hidden;aspect-ratio:<?php echo $ar; ?>;<?php if ( $mh > 0 ) : ?>min-height:<?php echo $mh; ?>px;<?php endif; ?>background:<?php echo $mediaBg; ?>;background-image:<?php echo $mediaImg; ?>;background-size:cover;background-position:center;<?php if ( $media_radius !== '' ) : ?>border-radius:<?php echo $media_radius; ?>;<?php endif; ?>}
            <?php if ( $img === '' && ! empty( $s['media_label'] ) ) : ?>
            .<?php echo $uid; ?> .pc-media::after{content:"<?php echo esc_attr( $s['media_label'] ); ?>";position:absolute;left:14px;bottom:12px;right:14px;font-family:<?php echo $mono; ?>;font-size:11px;letter-spacing:.04em;text-transform:uppercase;color:rgba(255,255,255,.4);}
            <?php endif; ?>
            .<?php echo $uid; ?>::after{content:"";position:absolute;inset:0;z-index:1;background:linear-gradient(180deg,rgba(0,0,0,<?php echo $aTop; ?>),transparent 35%,rgba(0,0,0,<?php echo $aBot; ?>));pointer-events:none;}
            .<?php echo $uid; ?> .pc-in{position:absolute;inset:0;z-index:2;display:flex;flex-direction:column;justify-content:flex-end;padding:<?php echo $pad; ?>;}
            .<?php echo $uid; ?> .pc-kicker{display:block;margin-bottom:14px;font-family:<?php echo $mono; ?>;font-weight:600;font-size:12px;letter-spacing:.16em;text-transform:uppercase;color:<?php echo $kicker; ?>;}
            .<?php echo $uid; ?> .pc-h{font-family:<?php echo $disp; ?>;font-weight:800;font-size:clamp(40px,8vw,120px);line-height:.9;letter-spacing:-.02em;text-transform:<?php echo $up; ?>;color:<?php echo $headline; ?>;max-width:14ch;margin:0;}
            .<?php echo $uid; ?> .pc-meta{display:flex;gap:18px;margin-top:18px;font-family:<?php echo $mono; ?>;font-size:13px;letter-spacing:.06em;text-transform:uppercase;color:<?php echo $meta; ?>;flex-wrap:wrap;}
            .<?php echo $uid; ?> .pc-meta a{color:inherit;text-decoration:none;}
            .<?php echo $uid; ?> a:focus-visible,.<?php echo $uid; ?> .pc-in:focus-visible{outline:2px solid <?php echo $kicker; ?>;outline-offset:3px;}
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <section class="olo-photocover <?php echo esc_attr( $uid ); ?>">
            <div class="pc-media"></div>
            <div class="pc-in">
                <?php if ( ! empty( $s['kicker_text'] ) ) : ?><span class="pc-kicker"><?php echo esc_html( $s['kicker_text'] ); ?></span><?php endif; ?>
                <?php if ( ! empty( $s['headline_text'] ) ) : ?><h1 class="pc-h"><?php echo esc_html( $s['headline_text'] ); ?></h1><?php endif; ?>
                <?php if ( ! empty( $metaItems ) ) : ?>
                <div class="pc-meta">
                    <?php foreach ( $metaItems as $m ) :
                        $txt = isset( $m['text'] ) ? trim( (string) $m['text'] ) : '';
                        if ( $txt === '' ) { continue; }
                        ?><span><?php echo esc_html( $txt ); ?></span><?php
                    endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </section>
        <?php
        // ── KIT standard: bordo hover + effetti bordo sul contenitore (.$uid) ──
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
