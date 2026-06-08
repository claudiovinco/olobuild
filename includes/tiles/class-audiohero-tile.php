<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Hero — Audio (Soundwave) : hero da musicista/band con SOUNDWAVE animato (equalizer
 * bars in puro CSS @keyframes) come meccanica firma. Layout a 2 colonne: a sinistra
 * tag (con barre eq) + H1 display gigante + sottotitolo + 2 CTA (play album / tour);
 * a destra cover art quadrata + mini-player inline (pulsante play, track meta, waveform).
 * Sfondo a doppio glow radiale (mint + pink). Nessun JS (animazioni solo CSS).
 * Estratta PIXEL-PERFECT dal blueprint OLOthemes "Soundwave" (.sw-hero). Render == Vue
 * (AudioHeroTile.vue). Archetipo dedicato: NON usare hero-split come surrogato.
 */
class Olo_AudioHero_Tile extends Olo_Tile_Base {

    protected $type     = 'audiohero';
    protected $name     = 'Hero — Audio (Soundwave)';
    protected $icon     = 'dashicons-format-audio';
    protected $category = 'marketing';
    protected $defaults = [
        'tag_text'        => 'New album · out now',
        'headline_text'   => 'Nightglass',
        'subhead'         => 'Eleven tracks recorded between Berlin and a cabin with bad wifi. Late-night electronics for headphones and dancefloors alike.',
        'cta1_text'       => 'Play album',
        'cta1_url'        => '#listen',
        'cta2_text'       => 'See tour dates',
        'cta2_url'        => '#tour',
        'cover_image'     => '',
        'cover_label'     => 'album cover — Nightglass, neon on black',
        'player_track'    => 'Glasshouse',
        'player_meta'     => 'Kova · Nightglass',
        'show_player'     => true,
        'bg_color'        => '#0c0c10',
        'panel_color'     => '#16161d',
        'accent'          => '#27e0a3',
        'accent_2'        => '#ff5d9e',
        'accent_on'       => '#060608',
        'text_color'      => '#ffffff',
        'sub_color'       => '#b6b6c2',
        'meta_color'      => '#74747f',
        'split_ratio'     => '1.1fr .9fr',

        // Spaziatura (override gated del padding interno responsivo) — default no-op.
        'pad_custom'      => false,
        'content_padding' => [ 'top' => 72, 'right' => 28, 'bottom' => 72, 'left' => 28 ],

        // Forma — raggi additivi (default = raggi attuali hardcoded → no-op).
        'cover_radius'    => [ 'tl' => 18, 'tr' => 18, 'br' => 18, 'bl' => 18 ],
        'player_radius'   => [ 'tl' => 14, 'tr' => 14, 'br' => 14, 'bl' => 14 ],

        // KIT standard OLObuild — sfondo completo + ombra + bordo (default no-op)
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
        $uid = 'oah-' . wp_rand( 10000, 99999 );

        $bg      = $this->safe_color_css( $s['bg_color'] ) ?: '#0c0c10';
        $panel   = $this->safe_color_css( $s['panel_color'] ?? '' ) ?: '#16161d';
        $accent  = $this->safe_color_css( $s['accent'] ) ?: 'var(--olo-color-primary, #27e0a3)';
        $accent2 = $this->safe_color_css( $s['accent_2'] ?? '' ) ?: '#ff5d9e';
        $accOn   = $this->safe_color_css( $s['accent_on'] ?? '' ) ?: '#060608';
        $txt     = $this->safe_color_css( $s['text_color'] ?? '' ) ?: '#ffffff';
        $sub     = $this->safe_color_css( $s['sub_color'] ?? '' ) ?: '#b6b6c2';
        $meta    = $this->safe_color_css( $s['meta_color'] ?? '' ) ?: '#74747f';

        $ratio   = trim( (string) ( $s['split_ratio'] ?? '1.1fr .9fr' ) ) ?: '1.1fr .9fr';
        $showpl  = ! empty( $s['show_player'] );
        $img     = trim( (string) ( $s['cover_image'] ?? '' ) );

        // ── Spaziatura: padding interno. Default (pad_custom=false) = clamp responsivo
        // invariato; se attivo, padding fisso dal field spacing {top,right,bottom,left}.
        $pad_in = 'clamp(48px,7vw,96px) 28px';
        if ( ! empty( $s['pad_custom'] ) ) {
            $cp   = is_array( $s['content_padding'] ?? null ) ? $s['content_padding'] : [];
            $cp_t = max( 0, intval( $cp['top']    ?? 72 ) );
            $cp_r = max( 0, intval( $cp['right']  ?? 28 ) );
            $cp_b = max( 0, intval( $cp['bottom'] ?? 72 ) );
            $cp_l = max( 0, intval( $cp['left']   ?? 28 ) );
            $pad_in = "{$cp_t}px {$cp_r}px {$cp_b}px {$cp_l}px";
        }

        // ── Forma: raggi (default = valori hardcoded attuali → no-op). ──
        $cover_radius  = $this->build_border_radius_css( $s['cover_radius']  ?? [] ) ?: '18px';
        $player_radius = $this->build_border_radius_css( $s['player_radius'] ?? [] ) ?: '14px';

        $disp    = "var(--olo-font-family-heading, 'Unbounded',-apple-system,sans-serif)";
        $sans    = "var(--olo-font-family, 'Figtree',-apple-system,sans-serif)";

        // Cover: immagine se presente, altrimenti placeholder a strisce diagonali.
        $cover_bg = $img !== ''
            ? 'background-image:url(' . esc_url( $img ) . ');background-size:cover;background-position:center;'
            : 'background-image:repeating-linear-gradient(135deg, rgba(255,255,255,.04) 0 16px, transparent 16px 32px);';

        // Waveform: 12 barre con altezze fisse (statiche, decorative).
        $wave = [ 30, 60, 90, 50, 75, 40, 85, 55, 95, 35, 70, 45 ];

        // ── KIT standard OLObuild — sfondo completo + ombra + bordo ───────────
        // Sfondo completo: override del background del contenitore SOLO se
        // valorizzato (default 'none' = invariato → nessun cambio di look).
        $bg_obj  = $s['bg'] ?? null;
        $bg_decl = '';
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olo_CSS_Builder' ) ) {
            $bg_decl = ( new Olo_CSS_Builder() )->get_bg_inline_css( $bg_obj );
        }
        // Ombra (preset sm/md/lg/xl o custom; '' se none).
        $shadow_css = $this->build_shadow_decl( $s );
        // Bordo (base + hover + effetti) — sistema standard.
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        // Coda di dichiarazioni extra per la regola .$uid del contenitore.
        $kit_decl = '';
        if ( $bg_decl !== '' ) {
            $kit_decl .= rtrim( $bg_decl, '; ' ) . ';';
        }
        if ( $shadow_css !== '' ) {
            $kit_decl .= 'box-shadow:' . $shadow_css . ';';
        }
        $kit_decl .= $border_css;

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?>{position:relative;overflow:hidden;background:<?php echo $bg; ?>;color:<?php echo $txt; ?>;font-family:<?php echo $sans; ?>;<?php echo $kit_decl; ?>}
            .<?php echo $uid; ?> .ah-bg{position:absolute;inset:0;z-index:0;background:radial-gradient(70% 70% at 80% 15%, <?php echo $this->rgba_from( $accent, 0.2 ); ?>, transparent 55%),radial-gradient(60% 60% at 15% 90%, <?php echo $this->rgba_from( $accent2, 0.14 ); ?>, transparent 55%);}
            .<?php echo $uid; ?> .ah-in{position:relative;z-index:2;width:100%;max-width:1180px;margin:0 auto;padding:<?php echo $pad_in; ?>;display:grid;grid-template-columns:<?php echo esc_attr( $ratio ); ?>;gap:48px;align-items:center;}
            .<?php echo $uid; ?> .ah-tag{display:inline-flex;align-items:center;gap:10px;font-family:<?php echo $sans; ?>;font-weight:700;font-size:12px;letter-spacing:.14em;text-transform:uppercase;color:<?php echo $accent; ?>;margin-bottom:22px;}
            .<?php echo $uid; ?> .ah-eq{display:inline-flex;align-items:flex-end;gap:3px;height:18px;}
            .<?php echo $uid; ?> .ah-eq i{width:3px;background:<?php echo $accent; ?>;border-radius:2px;animation:<?php echo $uid; ?>-eq 1s ease-in-out infinite;}
            .<?php echo $uid; ?> .ah-eq i:nth-child(1){height:40%;animation-delay:0s;}
            .<?php echo $uid; ?> .ah-eq i:nth-child(2){height:90%;animation-delay:.2s;}
            .<?php echo $uid; ?> .ah-eq i:nth-child(3){height:60%;animation-delay:.4s;}
            .<?php echo $uid; ?> .ah-eq i:nth-child(4){height:100%;animation-delay:.1s;}
            .<?php echo $uid; ?> .ah-eq i:nth-child(5){height:50%;animation-delay:.3s;}
            @keyframes <?php echo $uid; ?>-eq{0%,100%{transform:scaleY(.4);}50%{transform:scaleY(1);}}
            .<?php echo $uid; ?> .ah-h{font-family:<?php echo $disp; ?>;font-weight:700;font-size:clamp(48px,8vw,108px);line-height:.94;letter-spacing:-.01em;color:<?php echo $txt; ?>;margin:0;}
            .<?php echo $uid; ?> .ah-sub{font-size:18px;line-height:1.6;color:<?php echo $sub; ?>;max-width:420px;margin:24px 0 30px;}
            .<?php echo $uid; ?> .ah-cta{display:flex;gap:13px;flex-wrap:wrap;align-items:center;}
            .<?php echo $uid; ?> .ah-btn{display:inline-flex;align-items:center;gap:9px;padding:16px 30px;border-radius:999px;font-family:<?php echo $sans; ?>;font-weight:700;font-size:15px;text-decoration:none;cursor:pointer;border:0;transition:transform .15s,background .2s,box-shadow .2s;}
            .<?php echo $uid; ?> .ah-btn svg{width:17px;height:17px;}
            .<?php echo $uid; ?> .ah-btn:hover{transform:translateY(-2px);}
            .<?php echo $uid; ?> .ah-btn--solid{background:<?php echo $accent; ?>;color:<?php echo $accOn; ?>;box-shadow:0 10px 28px -10px <?php echo $this->rgba_from( $accent, 0.6 ); ?>;}
            .<?php echo $uid; ?> .ah-btn--solid:hover{filter:brightness(1.04);}
            .<?php echo $uid; ?> .ah-btn--ghost{background:rgba(255,255,255,.05);color:<?php echo $txt; ?>;border:1px solid rgba(255,255,255,.18);}
            .<?php echo $uid; ?> .ah-btn--ghost:hover{border-color:<?php echo $accent; ?>;color:<?php echo $accent; ?>;}
            .<?php echo $uid; ?> .ah-btn:focus-visible{outline:2px solid <?php echo $accent; ?>;outline-offset:3px;}
            .<?php echo $uid; ?> .ah-art{position:relative;}
            .<?php echo $uid; ?> .ah-cover{position:relative;aspect-ratio:1/1;border-radius:<?php echo $cover_radius; ?>;overflow:hidden;border:1px solid rgba(255,255,255,.09);box-shadow:0 30px 80px -30px <?php echo $this->rgba_from( $accent, 0.4 ); ?>;background:<?php echo $panel; ?>;<?php echo $cover_bg; ?>}
            .<?php echo $uid; ?> .ah-cover .ah-cover-lab{position:absolute;left:14px;bottom:12px;right:14px;font-family:<?php echo $sans; ?>;font-weight:600;font-size:10.5px;letter-spacing:.04em;color:rgba(255,255,255,.4);text-transform:uppercase;}
            .<?php echo $uid; ?> .ah-player{display:flex;align-items:center;gap:14px;margin-top:16px;background:<?php echo $panel; ?>;border:1px solid rgba(255,255,255,.09);border-radius:<?php echo $player_radius; ?>;padding:14px 16px;}
            .<?php echo $uid; ?> .ah-play{width:42px;height:42px;border-radius:50%;background:<?php echo $accent; ?>;display:grid;place-items:center;flex:none;}
            .<?php echo $uid; ?> .ah-play svg{width:16px;height:16px;color:<?php echo $accOn; ?>;}
            .<?php echo $uid; ?> .ah-pmeta{flex:1;min-width:0;}
            .<?php echo $uid; ?> .ah-pmeta b{color:<?php echo $txt; ?>;font-weight:700;font-size:14px;display:block;font-family:<?php echo $sans; ?>;}
            .<?php echo $uid; ?> .ah-pmeta span{font-size:12px;color:<?php echo $meta; ?>;}
            .<?php echo $uid; ?> .ah-wave{display:flex;align-items:center;gap:2px;height:26px;flex:none;}
            .<?php echo $uid; ?> .ah-wave i{width:2.5px;background:<?php echo $accent; ?>;border-radius:2px;opacity:.5;}
            @media (prefers-reduced-motion: reduce){.<?php echo $uid; ?> .ah-eq i{animation:none;}}
            @media(max-width:880px){.<?php echo $uid; ?> .ah-in{grid-template-columns:1fr;gap:40px;}}
        </style>
        <section class="olo-audiohero <?php echo esc_attr( $uid ); ?>">
            <span class="ah-bg" aria-hidden="true"></span>
            <div class="ah-in">
                <div class="ah-col">
                    <?php if ( ! empty( $s['tag_text'] ) ) : ?>
                    <span class="ah-tag"><span class="ah-eq" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i></span><?php echo esc_html( $s['tag_text'] ); ?></span>
                    <?php endif; ?>
                    <h1 class="ah-h"><?php echo esc_html( $s['headline_text'] ); ?></h1>
                    <?php if ( ! empty( $s['subhead'] ) ) : ?><p class="ah-sub"><?php echo esc_html( $s['subhead'] ); ?></p><?php endif; ?>
                    <div class="ah-cta">
                        <?php if ( ! empty( $s['cta1_text'] ) ) : ?><a class="ah-btn ah-btn--solid" href="<?php echo esc_url( $s['cta1_url'] ?: '#' ); ?>"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg><?php echo esc_html( $s['cta1_text'] ); ?></a><?php endif; ?>
                        <?php if ( ! empty( $s['cta2_text'] ) ) : ?><a class="ah-btn ah-btn--ghost" href="<?php echo esc_url( $s['cta2_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta2_text'] ); ?></a><?php endif; ?>
                    </div>
                </div>
                <div class="ah-art">
                    <div class="ah-cover">
                        <?php if ( $img === '' && ! empty( $s['cover_label'] ) ) : ?><span class="ah-cover-lab"><?php echo esc_html( $s['cover_label'] ); ?></span><?php endif; ?>
                    </div>
                    <?php if ( $showpl ) : ?>
                    <div class="ah-player">
                        <span class="ah-play" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg></span>
                        <div class="ah-pmeta"><b><?php echo esc_html( $s['player_track'] ); ?></b><span><?php echo esc_html( $s['player_meta'] ); ?></span></div>
                        <div class="ah-wave" aria-hidden="true"><?php foreach ( $wave as $h ) : ?><i style="height:<?php echo intval( $h ); ?>%"></i><?php endforeach; ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php
        // ── Sistema bordi standard — hover + effetti (come particlefx) ────────
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

    /** Estrae r,g,b da un colore #hex e ritorna "rgba(r,g,b,a)" (fallback se non hex). */
    private function rgba_from( $color, $alpha ) {
        $hex = ltrim( (string) $color, '#' );
        if ( strlen( $hex ) === 3 ) { $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2]; }
        if ( ! preg_match( '/^[0-9a-fA-F]{6}$/', $hex ) ) {
            return 'rgba(39,224,163,' . $alpha . ')';
        }
        $r = hexdec( substr( $hex, 0, 2 ) );
        $g = hexdec( substr( $hex, 2, 2 ) );
        $b = hexdec( substr( $hex, 4, 2 ) );
        return 'rgba(' . $r . ',' . $g . ',' . $b . ',' . $alpha . ')';
    }
}
