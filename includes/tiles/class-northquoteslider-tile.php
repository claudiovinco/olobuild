<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Quote Slider — North : slider quote stile "Cohere North".
 * A sinistra logo cliente + quote display + autore/ruolo + frecce e dots; a destra una
 * card scura la cui FORMA morfa da rettangolo verticale a parallelogramma (clip-path)
 * a ogni cambio slide, con linee punteggiate "topografiche" animate che scivolano.
 * Render == Vue (NorthQuoteSliderTile.vue). Colori via token/safe_color_css, KIT standard.
 * Estratta dal blueprint cohere.com/north (§ "Why enterprises and innovators choose Cohere").
 */
class Olo_NorthQuoteSlider_Tile extends Olo_Tile_Base {

    protected $type     = 'northquoteslider';
    protected $name     = 'Quote Slider — North';
    protected $icon     = 'dashicons-format-quote';
    protected $category = 'marketing';
    protected $defaults = [
        'heading'        => 'Why enterprises and innovators choose Cohere',
        'items'          => [],
        'slant'          => true,
        'autoplay'       => false,
        'autoplay_speed' => 6,

        'bg_color'           => '#ffffff',
        'heading_color'      => '#212121',
        'quote_color'        => '#212121',
        'author_color'       => '#212121',
        'role_color'         => '#6B7280',
        'logo_color'         => '#062C22',
        'arrow_color'        => '#212121',
        'graphic_color'      => '#0A2E22',
        'graphic_line_color' => '#9DF5D6',
        'quote_size'         => 26,

        // KIT standard OLObuild
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

    /** Linee punteggiate "topografiche" generate via sinusoidi (SVG paths). */
    private function topo_paths( $vbW = 400, $vbH = 640, $n = 11 ) {
        $out = '';
        for ( $i = 0; $i < $n; $i++ ) {
            $baseY = 26 + $i * ( ( $vbH - 52 ) / max( 1, $n - 1 ) );
            $amp   = 14 + 9 * abs( sin( $i * 1.1 ) );
            $waves = 1.3 + 0.25 * ( $i % 3 );
            $phase = $i * 0.6;
            $d     = 'M';
            for ( $x = 0; $x <= $vbW; $x += 16 ) {
                $y  = $baseY + $amp * sin( ( $x / $vbW ) * M_PI * 2 * $waves + $phase );
                $d .= ( 0 === $x ? '' : ' L' ) . $x . ' ' . round( $y, 1 );
            }
            $out .= '<path d="' . esc_attr( $d ) . '"/>';
        }
        return $out;
    }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'nqs-' . wp_rand( 10000, 99999 );

        $items = is_array( $s['items'] ?? null ) ? array_values( $s['items'] ) : [];
        if ( empty( $items ) ) { $items = $this->defaults['items']; }
        if ( empty( $items ) ) {
            $items = [ [ 'quote' => '', 'author_name' => '', 'author_role' => '', 'logo_text' => '' ] ];
        }

        $bgCol    = $this->safe_color_css( $s['bg_color'] ?? '' )           ?: '#ffffff';
        $headCol  = $this->safe_color_css( $s['heading_color'] ?? '' )      ?: '#212121';
        $quoteCol = $this->safe_color_css( $s['quote_color'] ?? '' )        ?: '#212121';
        $authCol  = $this->safe_color_css( $s['author_color'] ?? '' )       ?: '#212121';
        $roleCol  = $this->safe_color_css( $s['role_color'] ?? '' )         ?: '#6B7280';
        $logoCol  = $this->safe_color_css( $s['logo_color'] ?? '' )         ?: '#062C22';
        $arrCol   = $this->safe_color_css( $s['arrow_color'] ?? '' )        ?: '#212121';
        $grCol    = $this->safe_color_css( $s['graphic_color'] ?? '' )      ?: '#0A2E22';
        $lineCol  = $this->safe_color_css( $s['graphic_line_color'] ?? '' ) ?: '#9DF5D6';
        $qSize    = max( 16, min( 56, intval( $s['quote_size'] ?? 26 ) ) );

        $disp = "var(--olo-font-family-heading, 'Space Grotesk',-apple-system,sans-serif)";
        $sans = "var(--olo-font-family, 'Inter','Work Sans',-apple-system,sans-serif)";
        $mono = "var(--olo-font-family-mono, 'Space Mono',ui-monospace,'SF Mono',Menlo,monospace)";

        $slant    = ! empty( $s['slant'] );
        $autoplay = ! empty( $s['autoplay'] ) ? 1 : 0;
        $speed    = max( 2, min( 30, intval( $s['autoplay_speed'] ?? 6 ) ) ) * 1000;
        $multi    = count( $items ) > 1;

        // KIT bg/shadow/border
        $bg_obj  = $s['bg'] ?? null;
        $bg_decl = '';
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && 'none' !== $bg_obj['type'] && class_exists( 'Olo_CSS_Builder' ) ) {
            $bg_decl = ( new Olo_CSS_Builder() )->get_bg_inline_css( $bg_obj );
        }
        $kit_bg_css     = $bg_decl ? rtrim( $bg_decl, '; ' ) . ';' : '';
        $shadow_css     = $this->build_shadow_decl( $s );
        $kit_shadow_css = $shadow_css ? "box-shadow:{$shadow_css};" : '';

        $topo = $this->topo_paths();

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS built only from values sanitized above: safe_color_css() whitelist for every colour, intval() clamps for sizes, esc_attr() for SVG path data, fixed font-stack literals, internal wp_rand() uid. ?>
        <style>
            .<?php echo $uid; ?>{position:relative;background:<?php echo $bgCol; ?>;font-family:<?php echo $sans; ?>;<?php echo $kit_bg_css . $kit_shadow_css; ?>}
            .<?php echo $uid; ?> .nqs-in{max-width:1280px;margin:0 auto;padding:0 40px;}
            .<?php echo $uid; ?> .nqs-head{display:flex;align-items:flex-end;justify-content:space-between;gap:24px;margin:0 0 42px;}
            .<?php echo $uid; ?> .nqs-title{font-family:<?php echo $disp; ?>;font-weight:500;font-size:clamp(28px,3.4vw,44px);line-height:1.05;letter-spacing:-.02em;color:<?php echo $headCol; ?>;margin:0;max-width:760px;}
            .<?php echo $uid; ?> .nqs-nav{display:flex;gap:10px;flex:0 0 auto;}
            .<?php echo $uid; ?> .nqs-arrow{width:48px;height:48px;border-radius:999px;border:1px solid rgba(0,0,0,.16);background:transparent;color:<?php echo $arrCol; ?>;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:background .2s,transform .15s;}
            .<?php echo $uid; ?> .nqs-arrow:hover{background:rgba(0,0,0,.05);transform:translateY(-1px);}
            .<?php echo $uid; ?> .nqs-arrow:focus-visible{outline:2px solid <?php echo $arrCol; ?>;outline-offset:3px;}
            .<?php echo $uid; ?> .nqs-arrow svg{width:20px;height:20px;display:block;}
            .<?php echo $uid; ?> .nqs-grid{display:grid;grid-template-columns:1.45fr .9fr;gap:clamp(28px,4vw,56px);align-items:stretch;}
            .<?php echo $uid; ?> .nqs-left{position:relative;min-height:380px;display:flex;}
            .<?php echo $uid; ?> .nqs-slide{position:absolute;inset:0;opacity:0;visibility:hidden;transform:translateY(14px);transition:opacity .5s ease,transform .5s ease;display:flex;flex-direction:column;}
            .<?php echo $uid; ?> .nqs-slide.is-active{position:relative;opacity:1;visibility:visible;transform:none;}
            .<?php echo $uid; ?> .nqs-logo{font-family:<?php echo $disp; ?>;font-weight:700;font-size:22px;letter-spacing:.02em;color:<?php echo $logoCol; ?>;margin:0 0 24px;min-height:1px;}
            .<?php echo $uid; ?> .nqs-quote{font-family:<?php echo $disp; ?>;font-weight:500;font-size:<?php echo $qSize; ?>px;line-height:1.34;letter-spacing:-.01em;color:<?php echo $quoteCol; ?>;margin:0 0 28px;}
            .<?php echo $uid; ?> .nqs-author{font-weight:600;font-size:16px;color:<?php echo $authCol; ?>;margin:auto 0 2px;}
            .<?php echo $uid; ?> .nqs-role{font-size:15px;color:<?php echo $roleCol; ?>;margin:0;}
            .<?php echo $uid; ?> .nqs-dots{display:flex;gap:8px;margin-top:30px;}
            .<?php echo $uid; ?> .nqs-dot{height:8px;width:8px;border-radius:999px;border:0;background:rgba(0,0,0,.18);cursor:pointer;padding:0;transition:width .3s,background .3s;}
            .<?php echo $uid; ?> .nqs-dot.is-active{background:<?php echo $arrCol; ?>;width:24px;}
            .<?php echo $uid; ?> .nqs-right{position:relative;min-height:500px;}
            .<?php echo $uid; ?> .nqs-graphic{position:absolute;inset:0;background:<?php echo $grCol; ?>;border-radius:22px;overflow:hidden;clip-path:polygon(0 0,100% 0,100% 100%,0 100%);transition:clip-path .85s cubic-bezier(.66,0,.34,1);will-change:clip-path;}
            <?php if ( $slant ) : ?>
            .<?php echo $uid; ?> .nqs-graphic.is-slant{clip-path:polygon(20% 0,100% 0,80% 100%,0 100%);}
            <?php endif; ?>
            .<?php echo $uid; ?> .nqs-graphic svg{position:absolute;inset:0;width:100%;height:100%;}
            .<?php echo $uid; ?> .nqs-lines{transition:transform .85s cubic-bezier(.66,0,.34,1);}
            <?php if ( $slant ) : ?>
            .<?php echo $uid; ?> .nqs-graphic.is-slant .nqs-lines{transform:skewX(-10deg) translateX(5%);}
            <?php endif; ?>
            .<?php echo $uid; ?> .nqs-lines path{fill:none;stroke:<?php echo $lineCol; ?>;stroke-width:2.2;stroke-linecap:round;stroke-dasharray:.1 13;opacity:.85;animation:<?php echo $uid; ?>-flow 16s linear infinite;}
            @keyframes <?php echo $uid; ?>-flow{to{stroke-dashoffset:-260;}}
            .<?php echo $uid; ?> .nqs-glabel{position:absolute;left:24px;bottom:22px;font-family:<?php echo $mono; ?>;font-size:11px;letter-spacing:.05em;text-transform:uppercase;color:rgba(255,255,255,.52);z-index:2;}
            @media(prefers-reduced-motion:reduce){.<?php echo $uid; ?> .nqs-graphic,.<?php echo $uid; ?> .nqs-lines{transition:none;}.<?php echo $uid; ?> .nqs-lines path{animation:none;}}
            @media(max-width:860px){.<?php echo $uid; ?> .nqs-grid{grid-template-columns:1fr;}.<?php echo $uid; ?> .nqs-left{min-height:0;}.<?php echo $uid; ?> .nqs-right{min-height:340px;}.<?php echo $uid; ?> .nqs-in{padding:0 20px;}}
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <section class="olo-northquoteslider <?php echo esc_attr( $uid ); ?>" data-autoplay="<?php echo esc_attr( $autoplay ); ?>" data-speed="<?php echo esc_attr( $speed ); ?>" data-slant="<?php echo $slant ? '1' : '0'; ?>">
            <div class="nqs-in">
                <div class="nqs-head">
                    <?php if ( ! empty( $s['heading'] ) ) : ?><h2 class="nqs-title"><?php echo esc_html( $s['heading'] ); ?></h2><?php else : ?><span></span><?php endif; ?>
                    <?php if ( $multi ) : ?>
                    <div class="nqs-nav">
                        <button type="button" class="nqs-arrow" data-dir="-1" aria-label="<?php esc_attr_e( 'Precedente', 'olobuild' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg></button>
                        <button type="button" class="nqs-arrow" data-dir="1" aria-label="<?php esc_attr_e( 'Successivo', 'olobuild' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg></button>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="nqs-grid">
                    <div class="nqs-left">
                        <?php foreach ( $items as $i => $it ) : ?>
                        <div class="nqs-slide<?php echo 0 === $i ? ' is-active' : ''; ?>" data-idx="<?php echo (int) $i; ?>">
                            <?php if ( ! empty( $it['logo_text'] ) ) : ?><p class="nqs-logo"><?php echo esc_html( $it['logo_text'] ); ?></p><?php endif; ?>
                            <blockquote class="nqs-quote"><?php echo esc_html( $it['quote'] ?? '' ); ?></blockquote>
                            <?php if ( ! empty( $it['author_name'] ) ) : ?><p class="nqs-author"><?php echo esc_html( $it['author_name'] ); ?></p><?php endif; ?>
                            <?php if ( ! empty( $it['author_role'] ) ) : ?><p class="nqs-role"><?php echo esc_html( $it['author_role'] ); ?></p><?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                        <?php if ( $multi ) : ?>
                        <div class="nqs-dots">
                            <?php foreach ( $items as $i => $it ) : ?>
                            <button type="button" class="nqs-dot<?php echo 0 === $i ? ' is-active' : ''; ?>" data-idx="<?php echo (int) $i; ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %d slide number */ __( 'Slide %d', 'olobuild' ), $i + 1 ) ); ?>"></button>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="nqs-right">
                        <div class="nqs-graphic" aria-hidden="true">
                            <svg viewBox="0 0 400 640" preserveAspectRatio="xMidYMid slice"><g class="nqs-lines"><?php echo $topo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $topo is SVG <path> markup generated internally by topo_paths() (numeric coordinates only) ?></g></svg>
                            <span class="nqs-glabel">North · enterprise AI</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php
        // ── Sistema bordi standard (KIT) ──
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) {
                echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- base-class build_border_css() (intval'd widths, safe_color_css()'d colours), internal wp_rand() uid.
            }
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- base-class helpers (intval'd values, safe_color_css()'d colours).
        }
        if ( $multi ) {
            ?>
            <script>
            (function(){
                var root=document.querySelector('.<?php echo $uid; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- internal wp_rand() uid ?>');
                if(!root||root.__nqs) return; root.__nqs=1;
                var slides=[].slice.call(root.querySelectorAll('.nqs-slide'));
                var dots=[].slice.call(root.querySelectorAll('.nqs-dot'));
                var graphic=root.querySelector('.nqs-graphic');
                var slant=root.getAttribute('data-slant')==='1';
                var n=slides.length, cur=0, timer=null;
                function show(i){
                    i=(i%n+n)%n; cur=i;
                    slides.forEach(function(s,k){ s.classList.toggle('is-active',k===i); });
                    dots.forEach(function(d,k){ d.classList.toggle('is-active',k===i); });
                    if(graphic && slant){ graphic.classList.toggle('is-slant', i%2===1); }
                }
                function go(d){ show(cur+d); restart(); }
                function restart(){ if(timer){clearInterval(timer);timer=null;} arm(); }
                function arm(){
                    if(root.getAttribute('data-autoplay')!=='1') return;
                    if(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                    var ms=parseInt(root.getAttribute('data-speed'),10)||6000;
                    timer=setInterval(function(){ show(cur+1); }, ms);
                }
                root.querySelectorAll('.nqs-arrow').forEach(function(b){ b.addEventListener('click',function(){ go(parseInt(b.getAttribute('data-dir'),10)||1); }); });
                dots.forEach(function(d){ d.addEventListener('click',function(){ show(parseInt(d.getAttribute('data-idx'),10)||0); restart(); }); });
                arm();
            })();
            </script>
            <?php
        }
        return ob_get_clean();
    }

    /** Box-shadow declaration dal setting shadow (preset/custom). '' se none. */
    private function build_shadow_decl( $s ) {
        $preset = $s['shadow'] ?? 'none';
        if ( 'none' === $preset || '' === $preset ) { return ''; }
        if ( 'custom' === $preset ) {
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
