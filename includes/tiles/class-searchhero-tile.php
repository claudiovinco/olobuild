<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Hero — Search (Carrello) : hero e-commerce centrato con glow radiale superiore,
 * eyebrow coral, H1 gigante con parola-accento, sub, BARRA DI RICERCA (input + button)
 * come meccanica-firma, e riga di chip-categoria. Render == Vue (SearchHeroTile.vue).
 * Nessun JS. Estratta pixel-perfect dal blueprint OLOthemes "Carrello" (.ca-hero).
 */
class Olobuild_SearchHero_Tile extends Olobuild_Tile_Base {

    protected $type     = 'searchhero';
    protected $name     = 'Hero — Search (Carrello)';
    protected $icon     = 'dashicons-search';
    protected $category = 'marketing';
    protected $defaults = [
        'eyebrow_text'    => 'Marketplace for independent makers',
        'headline_text'   => 'Everything good,',
        'headline_line2'  => 'from',
        'accent_text'     => 'small shops.',
        'subhead'         => 'Thousands of independent sellers, one cart, one checkout. Find the thing — and support the person who made it.',
        'search_placeholder' => 'Search 90,000+ handmade things…',
        'search_button'   => 'Search',
        'search_url'      => '#',
        'chips'           => 'Ceramics, Prints, Jewellery, Homeware, Vintage, Gifts',
        'bg_color'        => '#1a1a22',
        'panel_color'     => '#26262f',
        'accent'          => '#ff5a5f',
        'accent_on'       => '#ffffff',
        'glow_color'      => 'rgba(255,90,95,0.22)',
        'text_color'      => '#ffffff',
        'sub_color'       => '#6c6c7c',
        'chip_color'      => '#a6a6b4',
        'border_color'    => 'rgba(255,255,255,0.09)',
        'search_border'   => 'rgba(255,90,95,0.4)',
        'min_height'      => 0,

        // Spaziatura (additivo, no-op): override GATED del padding responsive del contenitore.
        'pad_custom'      => false,
        'content_padding' => [ 'top' => 52, 'right' => 0, 'bottom' => 52, 'left' => 0 ],

        // Raggio (additivo, no-op): raggio della barra di ricerca (firma) — default 14px.
        'search_radius'   => [ 'tl' => 14, 'tr' => 14, 'br' => 14, 'bl' => 14 ],

        // KIT standard OLObuild (additivo, no-op coi default) — contenitore principale
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
        $uid = 'osh-' . wp_rand( 10000, 99999 );

        $bg      = $this->safe_color_css( $s['bg_color'] ) ?: '#1a1a22';
        $panel   = $this->safe_color_css( $s['panel_color'] ) ?: '#26262f';
        $accent  = $this->safe_color_css( $s['accent'] ) ?: 'var(--olo-color-primary, #ff5a5f)';
        $accOn   = $this->safe_color_css( $s['accent_on'] ?? '' ) ?: '#ffffff';
        $glow    = $this->safe_color_css( $s['glow_color'] ?? '' ) ?: 'rgba(255,90,95,0.22)';
        $txt     = $this->safe_color_css( $s['text_color'] ?? '' ) ?: '#ffffff';
        $sub     = $this->safe_color_css( $s['sub_color'] ?? '' ) ?: '#6c6c7c';
        $chipCol = $this->safe_color_css( $s['chip_color'] ?? '' ) ?: '#a6a6b4';
        $line    = $this->safe_color_css( $s['border_color'] ?? '' ) ?: 'rgba(255,255,255,0.09)';
        $sline   = $this->safe_color_css( $s['search_border'] ?? '' ) ?: 'rgba(255,90,95,0.4)';
        $mh      = max( 0, min( 100, intval( $s['min_height'] ) ) );

        // ── Spaziatura: override GATED del padding contenitore (no-op coi default) ──
        // pad_custom false → mantiene clamp(52px,7vw,92px) 0 (responsivo, invariato).
        $pad_decl = 'clamp(52px,7vw,92px) 0';
        if ( ! empty( $s['pad_custom'] ) && is_array( $s['content_padding'] ?? null ) ) {
            $cp = $s['content_padding'];
            $pt = max( 0, intval( $cp['top']    ?? 0 ) );
            $pr = max( 0, intval( $cp['right']  ?? 0 ) );
            $pb = max( 0, intval( $cp['bottom'] ?? 0 ) );
            $pl = max( 0, intval( $cp['left']   ?? 0 ) );
            $pad_decl = "{$pt}px {$pr}px {$pb}px {$pl}px";
        }
        // ── Raggio barra ricerca (default 14px → invariato) ──
        // Collassa 4 angoli uguali alla forma breve per parità byte-per-byte col render originale.
        $sr = $s['search_radius'] ?? [];
        if ( is_array( $sr ) ) {
            $rtl = intval( $sr['tl'] ?? 0 ); $rtr = intval( $sr['tr'] ?? 0 );
            $rbr = intval( $sr['br'] ?? 0 ); $rbl = intval( $sr['bl'] ?? 0 );
            if ( $rtl === $rtr && $rtr === $rbr && $rbr === $rbl ) {
                $search_radius = $rtl . 'px';
            } else {
                $search_radius = "{$rtl}px {$rtr}px {$rbr}px {$rbl}px";
            }
        } else {
            $search_radius = '14px';
        }

        $disp = "var(--olo-font-family-heading, 'Mona Sans',-apple-system,sans-serif)";
        $sans = "var(--olo-font-family, 'Mona Sans',-apple-system,sans-serif)";

        $chips = array_filter( array_map( 'trim', explode( ',', (string) $s['chips'] ) ), 'strlen' );
        $url   = $s['search_url'] ?: '#';

        // ── KIT standard OLObuild sul contenitore principale (.$uid) ──────────
        // Sfondo completo: override del bg colore SOLO se valorizzato → default invariato.
        $bg_obj  = $s['bg'] ?? null;
        $bg_decl = '';
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olobuild_CSS_Builder' ) ) {
            $bg_decl = ( new Olobuild_CSS_Builder() )->get_bg_inline_css( $bg_obj );
        }
        // Ombra (preset/custom): '' se none.
        $shadow_css = $this->build_shadow_decl( $s );

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: every colour via the safe_color_css() whitelist (or fixed literal fallback), padding/radius from intval()'d sides, fixed font-stack literals, min-height via intval() clamp, bg via Olobuild_CSS_Builder::get_bg_inline_css(), shadow via build_shadow_decl() (intval/safe_color_css/fixed map); $uid is internally generated. ?>
        <style>
            .<?php echo $uid; ?>{position:relative;overflow:hidden;padding:<?php echo $pad_decl; ?>;text-align:center;background:<?php echo $bg; ?>;<?php if ( $bg_decl ) { echo $bg_decl . ';'; } ?>color:<?php echo $txt; ?>;font-family:<?php echo $sans; ?>;<?php if ( $shadow_css ) { echo 'box-shadow:' . $shadow_css . ';'; } ?><?php if ( $mh > 0 ) : ?>min-height:<?php echo $mh; ?>vh;display:flex;align-items:center;<?php endif; ?>}
            .<?php echo $uid; ?> .sh-glow{position:absolute;top:-160px;left:50%;transform:translateX(-50%);width:720px;height:480px;border-radius:50%;filter:blur(120px);background:radial-gradient(circle,<?php echo $glow; ?>,transparent 70%);pointer-events:none;}
            .<?php echo $uid; ?> .sh-in{position:relative;z-index:2;max-width:760px;margin:0 auto;width:100%;padding:0 28px;}
            .<?php echo $uid; ?> .sh-eyebrow{display:block;margin-bottom:18px;font-family:<?php echo $sans; ?>;font-weight:700;font-size:12px;letter-spacing:.12em;text-transform:uppercase;color:<?php echo $accent; ?>;}
            .<?php echo $uid; ?> .sh-h{font-family:<?php echo $disp; ?>;font-weight:800;font-size:clamp(40px,6.6vw,80px);line-height:1.0;letter-spacing:-.02em;color:<?php echo $txt; ?>;margin:0;}
            .<?php echo $uid; ?> .sh-acc{color:<?php echo $accent; ?>;}
            .<?php echo $uid; ?> .sh-sub{font-size:18px;line-height:1.6;color:<?php echo $sub; ?>;max-width:460px;margin:20px auto 30px;}
            .<?php echo $uid; ?> .sh-search{display:flex;gap:8px;max-width:560px;margin:0 auto;background:<?php echo $panel; ?>;border:1px solid <?php echo $sline; ?>;border-radius:<?php echo $search_radius; ?>;padding:8px;}
            .<?php echo $uid; ?> .sh-search input{flex:1;background:transparent;border:0;padding:12px 14px;font-family:<?php echo $sans; ?>;font-size:15px;color:<?php echo $txt; ?>;min-width:0;}
            .<?php echo $uid; ?> .sh-search input::placeholder{color:<?php echo $sub; ?>;}
            .<?php echo $uid; ?> .sh-search input:focus{outline:none;}
            .<?php echo $uid; ?> .sh-search input:focus-visible{outline:2px solid <?php echo $accent; ?>;outline-offset:2px;border-radius:8px;}
            .<?php echo $uid; ?> .sh-btn{display:inline-flex;align-items:center;gap:8px;padding:13px 24px;border-radius:10px;font-family:<?php echo $sans; ?>;font-weight:700;font-size:14px;text-decoration:none;cursor:pointer;border:0;background:<?php echo $accent; ?>;color:<?php echo $accOn; ?>;box-shadow:0 10px 28px -10px <?php echo $glow; ?>;transition:transform .15s,filter .2s,box-shadow .2s;white-space:nowrap;}
            .<?php echo $uid; ?> .sh-btn:hover{transform:translateY(-2px);filter:brightness(1.06);}
            .<?php echo $uid; ?> .sh-btn:focus-visible{outline:2px solid <?php echo $accOn; ?>;outline-offset:2px;}
            .<?php echo $uid; ?> .sh-chips{display:flex;gap:8px;justify-content:center;flex-wrap:wrap;margin-top:18px;}
            .<?php echo $uid; ?> .sh-chip{font-family:<?php echo $sans; ?>;font-weight:600;font-size:13px;color:<?php echo $chipCol; ?>;border:1px solid <?php echo $line; ?>;border-radius:999px;padding:7px 15px;text-decoration:none;transition:border-color .15s,color .15s;}
            .<?php echo $uid; ?> .sh-chip:hover{border-color:<?php echo $accent; ?>;color:<?php echo $accent; ?>;}
            .<?php echo $uid; ?> .sh-chip:focus-visible{outline:2px solid <?php echo $accent; ?>;outline-offset:2px;}
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <section class="olo-searchhero <?php echo esc_attr( $uid ); ?>">
            <span class="sh-glow"></span>
            <div class="sh-in">
                <?php if ( ! empty( $s['eyebrow_text'] ) ) : ?><span class="sh-eyebrow"><?php echo esc_html( $s['eyebrow_text'] ); ?></span><?php endif; ?>
                <h1 class="sh-h"><?php echo esc_html( $s['headline_text'] ); ?><br/><?php
                    if ( ! empty( $s['headline_line2'] ) ) { echo esc_html( $s['headline_line2'] ) . ' '; }
                    if ( ! empty( $s['accent_text'] ) ) : ?><span class="sh-acc"><?php echo esc_html( $s['accent_text'] ); ?></span><?php endif; ?></h1>
                <?php if ( ! empty( $s['subhead'] ) ) : ?><p class="sh-sub"><?php echo esc_html( $s['subhead'] ); ?></p><?php endif; ?>
                <div class="sh-search">
                    <input type="search" placeholder="<?php echo esc_attr( $s['search_placeholder'] ); ?>" aria-label="<?php echo esc_attr( $s['search_placeholder'] ); ?>"/>
                    <?php if ( ! empty( $s['search_button'] ) ) : ?><a class="sh-btn" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $s['search_button'] ); ?></a><?php endif; ?>
                </div>
                <?php if ( ! empty( $chips ) ) : ?>
                <div class="sh-chips">
                    <?php foreach ( $chips as $chip ) : ?><a class="sh-chip" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $chip ); ?></a><?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </section>
        <?php
        // ── Sistema bordi standard (come particlefx) ─────────────────────────
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) {
                echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            }
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
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
