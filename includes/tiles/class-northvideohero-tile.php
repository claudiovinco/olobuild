<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Hero — North Video : hero SaaS scuro in stile "Cohere North".
 * Eyebrow MONO nudo (no pill) + crest ORB a wireframe (cerchio + ellissi incrociate)
 * + H1 display gigante + mockup PRODOTTO in cornice scura arrotondata che contiene un
 * VERO <video> nativo (poster + controlli scrubber/play/mute/fullscreen) OPPURE un
 * media placeholder. Dietro, layer IMMAGINE con background-attachment:fixed (parallasse
 * "sfondo fisso") mascherato in basso — riproduce l'erba aerea fissa del blueprint.
 *
 * Reveal del mockup all'ingresso (translateY+scale, prefers-reduced-motion safe, CSS only).
 * Render == Vue (NorthVideoHeroTile.vue). Colori via token, KIT bordi/ombra/sfondo standard.
 * Estratta dal blueprint cohere.com/north.
 */
class Olobuild_NorthVideoHero_Tile extends Olobuild_Tile_Base {

    protected $type     = 'northvideohero';
    protected $name     = 'Hero — North Video';
    protected $icon     = 'dashicons-video-alt2';
    protected $category = 'marketing';
    protected $defaults = [
        // contenuto
        'eyebrow_text'   => 'NORTH',
        'crest_on'       => true,
        'headline_text'  => 'AI for business that turns complexity into clarity',
        'accent_text'    => '',
        'subhead'        => '',
        'cta1_text'      => '',
        'cta1_url'       => '#',
        'cta2_text'      => '',
        'cta2_url'       => '#',

        // mockup
        'mock_mode'      => 'video', // 'video' | 'media' | 'none'
        'media_bg'       => [ 'type' => 'none' ], // sfondo hero unificato (precede su video_src/poster)
        'video_src'      => '',
        'video_poster'   => '',
        'show_controls'  => true,
        'autoplay'       => false,
        'muted'          => true,
        'loop'           => false,
        'media_label'    => 'product — North workspace',
        'mock_reveal'    => true,

        // sfondo fisso (erba/parallasse)
        'bg_fixed_image' => '',
        'bg_fixed_from'  => 42, // % da cui l'immagine fissa inizia a comparire (mask)

        // layout / forma
        'headline_max'   => 1100,
        'frame_radius'   => [ 'tl' => 20, 'tr' => 20, 'br' => 20, 'bl' => 20 ],
        'content_padding'=> [ 'top' => 160, 'right' => 40, 'bottom' => 96, 'left' => 40 ],

        // colori
        'bg_color'       => '#062C22',
        'text_color'     => '#ffffff',
        'eyebrow_color'  => 'rgba(255,255,255,0.78)',
        'sub_color'      => 'rgba(255,255,255,0.72)',
        'accent'         => '',
        'crest_color'    => 'rgba(255,255,255,0.5)',
        'frame_bg'       => '#0a201a',
        'frame_border'   => 'rgba(255,255,255,0.12)',

        // KIT standard OLObuild — default no-op
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

    private function video_mime( $url ) {
        $ext = strtolower( pathinfo( (string) wp_parse_url( $url, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
        $map = [ 'mp4' => 'video/mp4', 'webm' => 'video/webm', 'ogg' => 'video/ogg', 'm3u8' => 'application/x-mpegURL' ];
        return $map[ $ext ] ?? 'video/mp4';
    }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'nvh-' . wp_rand( 10000, 99999 );

        $bg       = $this->safe_color_css( $s['bg_color'] ?? '' )     ?: '#062C22';
        $txt      = $this->safe_color_css( $s['text_color'] ?? '' )   ?: '#ffffff';
        $eyeCol   = $this->safe_color_css( $s['eyebrow_color'] ?? '' ) ?: 'rgba(255,255,255,0.78)';
        $sub      = $this->safe_color_css( $s['sub_color'] ?? '' )    ?: 'rgba(255,255,255,0.72)';
        $accent   = $this->safe_color_css( $s['accent'] ?? '' )       ?: 'var(--olo-color-primary, #ff7759)';
        $crestC   = $this->safe_color_css( $s['crest_color'] ?? '' )  ?: 'rgba(255,255,255,0.5)';
        $frameBg  = $this->safe_color_css( $s['frame_bg'] ?? '' )     ?: '#0a201a';
        $frameBd  = $this->safe_color_css( $s['frame_border'] ?? '' ) ?: 'rgba(255,255,255,0.12)';

        $disp = "var(--olo-font-family-heading, 'Space Grotesk',-apple-system,sans-serif)";
        $sans = "var(--olo-font-family, 'Inter','Work Sans',-apple-system,sans-serif)";
        $mono = "var(--olo-font-family-mono, 'Space Mono',ui-monospace,'SF Mono',Menlo,monospace)";

        $mode    = in_array( ( $s['mock_mode'] ?? 'video' ), [ 'video', 'media', 'none' ], true ) ? ( $s['mock_mode'] ?? 'video' ) : 'video';
        $crestOn = ! empty( $s['crest_on'] );
        $reveal  = ! empty( $s['mock_reveal'] );
        $hmax    = max( 480, min( 1600, intval( $s['headline_max'] ) ) );

        $in_pad       = Olobuild_Tile_Utils::spacing_css( $s['content_padding'] ?? [ 'top' => 160, 'right' => 40, 'bottom' => 96, 'left' => 40 ], 0 );
        $frame_radius = $this->build_border_radius_css( $s['frame_radius'] ?? [] );
        if ( '' === $frame_radius ) { $frame_radius = '0'; }

        // Sfondo fisso (parallasse): layer con background-attachment:fixed, mascherato in basso.
        $grass     = trim( (string) ( $s['bg_fixed_image'] ?? '' ) );
        $grassFrom = max( 0, min( 100, intval( $s['bg_fixed_from'] ) ) );

        // Sfondo hero PRINCIPALE unificato: pannello media_bg (video/immagine/gradiente/…)
        // con precedenza sui campi legacy video_src/video_poster (tenuti come fallback).
        $mb          = $this->bg_media_parts( $s['media_bg'] ?? null, $uid );
        $has_mb      = $mb['has'];
        $mb_type     = ( is_array( $s['media_bg'] ?? null ) ) ? ( $s['media_bg']['type'] ?? 'none' ) : 'none';
        $mb_is_video = $has_mb && $mb_type === 'video';

        // Video
        $is_builder  = ! empty( $s['_builder_mode'] );
        $vsrc        = trim( (string) ( $s['video_src'] ?? '' ) );
        $vposter     = trim( (string) ( $s['video_poster'] ?? '' ) );
        $autoplay    = ! $is_builder && ! empty( $s['autoplay'] );
        $muted       = ! empty( $s['muted'] ) || $autoplay;
        $controls    = $is_builder || ! empty( $s['show_controls'] );
        $preload     = $is_builder ? 'metadata' : 'auto';

        // KIT standard: sfondo completo + ombra + bordo (sul contenitore)
        $bg_obj  = $s['bg'] ?? null;
        $bg_decl = '';
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olobuild_CSS_Builder' ) ) {
            $bg_decl = ( new Olobuild_CSS_Builder() )->get_bg_inline_css( $bg_obj );
        }
        $kit_bg_css     = $bg_decl ? rtrim( $bg_decl, '; ' ) . ';' : '';
        $shadow_css     = $this->build_shadow_decl( $s );
        $kit_shadow_css = $shadow_css ? "box-shadow:{$shadow_css};" : '';

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist for every colour, intval() clamps for sizes/percent, Olobuild_Tile_Utils::spacing_css()/build_border_radius_css() integer-built values, esc_url() for image URLs, internal Olobuild_CSS_Builder/build_shadow_decl() helpers, fixed font-stack literals, internal wp_rand() uid. ?>
        <style>
            .<?php echo $uid; ?>{position:relative;overflow:hidden;background:<?php echo $bg; ?>;color:<?php echo $txt; ?>;font-family:<?php echo $sans; ?>;<?php echo $kit_bg_css . $kit_shadow_css; ?>}
            <?php if ( $grass !== '' ) : ?>
            .<?php echo $uid; ?> .nvh-grass{position:absolute;inset:0;z-index:0;background-image:url('<?php echo esc_url( $grass ); ?>');background-attachment:fixed;background-size:cover;background-position:<?php echo esc_attr( Olobuild_Tile_Utils::focal_pos( $s, 'bg_fixed_image' ) ); ?>;-webkit-mask:linear-gradient(180deg,transparent 0%,transparent <?php echo $grassFrom; ?>%,#000 100%);mask:linear-gradient(180deg,transparent 0%,transparent <?php echo $grassFrom; ?>%,#000 100%);pointer-events:none;}
            @media(max-width:900px){.<?php echo $uid; ?> .nvh-grass{background-attachment:scroll;}}
            <?php endif; ?>
            .<?php echo $uid; ?> .nvh-in{position:relative;z-index:2;max-width:1280px;margin:0 auto;padding:<?php echo $in_pad; ?>;}
            .<?php echo $uid; ?> .nvh-head{display:flex;align-items:flex-start;gap:clamp(20px,4vw,72px);}
            .<?php echo $uid; ?> .nvh-crest{flex:0 0 auto;width:clamp(56px,7vw,92px);height:clamp(56px,7vw,92px);margin-top:6px;}
            .<?php echo $uid; ?> .nvh-crest svg{display:block;width:100%;height:100%;}
            .<?php echo $uid; ?> .nvh-text{flex:1 1 auto;min-width:0;}
            .<?php echo $uid; ?> .nvh-eyebrow{display:block;font-family:<?php echo $mono; ?>;font-size:14px;line-height:1.4;letter-spacing:.02em;text-transform:uppercase;color:<?php echo $eyeCol; ?>;margin:0 0 26px;}
            .<?php echo $uid; ?> .nvh-h{font-family:<?php echo $disp; ?>;font-weight:500;font-size:clamp(40px,6.6vw,72px);line-height:1.0;letter-spacing:-.02em;color:<?php echo $txt; ?>;margin:0;max-width:<?php echo (int) $hmax; ?>px;}
            .<?php echo $uid; ?> .nvh-acc{color:<?php echo $accent; ?>;}
            .<?php echo $uid; ?> .nvh-sub{font-size:18px;line-height:1.4;color:<?php echo $sub; ?>;max-width:560px;margin:26px 0 0;}
            .<?php echo $uid; ?> .nvh-cta{display:flex;gap:12px;flex-wrap:wrap;margin-top:32px;}
            .<?php echo $uid; ?> .nvh-btn{display:inline-flex;align-items:center;gap:8px;padding:14px 26px;border-radius:999px;font-family:<?php echo $sans; ?>;font-weight:500;font-size:16px;text-decoration:none;cursor:pointer;border:0;transition:transform .15s,filter .2s,background .2s;}
            .<?php echo $uid; ?> .nvh-btn--solid{background:#fff;color:<?php echo $bg; ?>;}
            .<?php echo $uid; ?> .nvh-btn--solid:hover{transform:translateY(-2px);filter:brightness(.96);}
            .<?php echo $uid; ?> .nvh-btn--ghost{background:rgba(255,255,255,.08);color:<?php echo $txt; ?>;border:1px solid rgba(255,255,255,.22);}
            .<?php echo $uid; ?> .nvh-btn--ghost:hover{transform:translateY(-2px);background:rgba(255,255,255,.14);}
            .<?php echo $uid; ?> .nvh-btn:focus-visible{outline:2px solid <?php echo $accent; ?>;outline-offset:3px;}
            .<?php echo $uid; ?> .nvh-mockwrap{position:relative;z-index:2;max-width:1180px;margin:clamp(40px,6vw,72px) auto 0;padding:0 40px;}
            .<?php echo $uid; ?> .nvh-frame{position:relative;border:1px solid <?php echo $frameBd; ?>;border-radius:<?php echo $frame_radius; ?>;background:<?php echo $frameBg; ?>;overflow:hidden;box-shadow:0 40px 80px -40px rgba(0,0,0,.6);}
            .<?php echo $uid; ?> .nvh-video{display:block;width:100%;height:auto;aspect-ratio:16/9.4;object-fit:cover;background:<?php echo $frameBg; ?>;}
            <?php if ( $has_mb && ! $mb_is_video && $mb['css'] !== '' ) : ?>
            .<?php echo $uid; ?> .nvh-mediabg{<?php echo $mb['css']; ?>}
            <?php endif; ?>
            .<?php echo $uid; ?> .nvh-media{position:relative;aspect-ratio:16/9.4;background:<?php echo $frameBg; ?>;background-image:repeating-linear-gradient(135deg, rgba(255,255,255,.035) 0 16px, transparent 16px 32px);}
            .<?php echo $uid; ?> .nvh-medialabel{position:absolute;left:18px;bottom:14px;font-family:<?php echo $mono; ?>;font-size:11px;letter-spacing:.03em;color:rgba(255,255,255,.42);text-transform:uppercase;}
            <?php if ( $reveal ) : ?>
            .<?php echo $uid; ?> .nvh-frame{transform-origin:center top;will-change:transform;transform:translateY(70px) scale(.9);}
            @media(prefers-reduced-motion:reduce){.<?php echo $uid; ?> .nvh-frame{transform:none !important;}}
            <?php endif; ?>
            @media(max-width:780px){
                .<?php echo $uid; ?> .nvh-head{flex-direction:column;gap:24px;}
                .<?php echo $uid; ?> .nvh-mockwrap{padding:0 20px;}
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <section class="olo-northvideohero <?php echo esc_attr( $uid ); ?>">
            <?php if ( $grass !== '' ) : ?><span class="nvh-grass" aria-hidden="true"></span><?php endif; ?>
            <div class="nvh-in">
                <div class="nvh-head">
                    <?php if ( $crestOn ) : ?>
                    <span class="nvh-crest" aria-hidden="true">
                        <svg viewBox="0 0 100 100" fill="none" stroke="<?php echo esc_attr( $crestC ); ?>" stroke-width="1">
                            <circle cx="50" cy="50" r="46"/>
                            <ellipse cx="50" cy="50" rx="46" ry="16" transform="rotate(35 50 50)"/>
                            <ellipse cx="50" cy="50" rx="16" ry="46" transform="rotate(35 50 50)"/>
                            <line x1="50" y1="4" x2="50" y2="96"/>
                        </svg>
                    </span>
                    <?php endif; ?>
                    <div class="nvh-text">
                        <?php if ( ! empty( $s['eyebrow_text'] ) ) : ?><span class="nvh-eyebrow"><?php echo esc_html( $s['eyebrow_text'] ); ?></span><?php endif; ?>
                        <h1 class="nvh-h"><?php echo esc_html( $s['headline_text'] ); ?><?php if ( ! empty( $s['accent_text'] ) ) : ?> <span class="nvh-acc"><?php echo esc_html( $s['accent_text'] ); ?></span><?php endif; ?></h1>
                        <?php if ( ! empty( $s['subhead'] ) ) : ?><p class="nvh-sub"><?php echo esc_html( $s['subhead'] ); ?></p><?php endif; ?>
                        <?php if ( ! empty( $s['cta1_text'] ) || ! empty( $s['cta2_text'] ) ) : ?>
                        <div class="nvh-cta">
                            <?php if ( ! empty( $s['cta1_text'] ) ) : ?><a class="nvh-btn nvh-btn--solid" href="<?php echo esc_url( $s['cta1_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta1_text'] ); ?></a><?php endif; ?>
                            <?php if ( ! empty( $s['cta2_text'] ) ) : ?><a class="nvh-btn nvh-btn--ghost" href="<?php echo esc_url( $s['cta2_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta2_text'] ); ?></a><?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php if ( $mode !== 'none' ) : ?>
            <div class="nvh-mockwrap">
                <div class="nvh-frame">
                    <?php if ( $mode === 'video' && $mb_is_video ) : ?>
                        <div class="nvh-video" style="position:relative;">
                            <?php echo $mb['markup']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup generato da Olobuild_CSS_Builder::get_bg_html_markup(), che escapa il proprio output ?>
                        </div>
                    <?php elseif ( $mode === 'video' && $has_mb ) : ?>
                        <div class="nvh-video nvh-mediabg"></div>
                    <?php elseif ( $mode === 'video' && $vsrc !== '' ) : ?>
                        <video class="nvh-video" preload="<?php echo esc_attr( $preload ); ?>"<?php echo $autoplay ? ' autoplay' : ''; ?><?php echo $muted ? ' muted' : ''; ?><?php echo ! empty( $s['loop'] ) ? ' loop' : ''; ?><?php echo $controls ? ' controls' : ''; ?><?php echo $vposter !== '' ? ' poster="' . esc_url( $vposter ) . '"' : ''; ?> playsinline>
                            <source src="<?php echo esc_url( $vsrc ); ?>" type="<?php echo esc_attr( $this->video_mime( $vsrc ) ); ?>">
                        </video>
                    <?php elseif ( $mode === 'video' && $vposter !== '' ) : ?>
                        <img class="nvh-video" src="<?php echo esc_url( $vposter ); ?>" alt="" loading="lazy" />
                    <?php else : ?>
                        <div class="nvh-media"><?php if ( ! empty( $s['media_label'] ) ) : ?><span class="nvh-medialabel"><?php echo esc_html( $s['media_label'] ); ?></span><?php endif; ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </section>
        <?php
        // ── Sistema bordi standard (KIT OLObuild) ──
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) {
                echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- border CSS from base-class build_border_css() (intval'd widths, safe_color_css()'d colours), internal wp_rand() uid.
            }
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- border CSS from base-class helpers (intval'd values, safe_color_css()'d colours).
        }
        // ── Parallasse del mockup: scivola e cresce sullo scroll (prato fisso sotto) ──
        if ( $reveal && $mode !== 'none' ) {
            ?>
            <script>
            (function(){
                var sec=document.querySelector('.<?php echo $uid; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- internal wp_rand() uid ?>');
                if(!sec) return;
                if(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                var frame=sec.querySelector('.nvh-frame'); if(!frame) return;
                var ticking=false;
                function update(){
                    ticking=false;
                    var r=sec.getBoundingClientRect();
                    var vh=window.innerHeight||800;
                    var p=Math.min(1, Math.max(0, (vh - r.top) / (vh*0.85)));
                    var scale=0.9 + 0.1*p;
                    var ty=(1-p)*70;
                    frame.style.transform='translateY('+ty.toFixed(1)+'px) scale('+scale.toFixed(4)+')';
                }
                function onScroll(){ if(!ticking){ ticking=true; requestAnimationFrame(update); } }
                window.addEventListener('scroll', onScroll, {passive:true});
                window.addEventListener('resize', onScroll, {passive:true});
                update();
            })();
            </script>
            <?php
        }
        return ob_get_clean();
    }

    /** Box-shadow declaration dal setting shadow (preset/custom). '' se none. */
    private function build_shadow_decl( $s ) {
        $preset = $s['shadow'] ?? 'none';
        if ( $preset === 'none' || $preset === '' ) { return ''; }
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
