<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Demo temi — rail orizzontale di card con mini-preview PARAMETRICA di un tema:
 * il riquadro anteprima è disegnato (logo quadratino, headline nel font del tema,
 * bottone pill, badge zona blur) coi colori bg/ink/accent dell'item — niente
 * screenshot. I colori e il font degli item sono CONTENUTO (rappresentano il tema
 * mostrato), il guscio (card, footer, bordi) segue i token del sito. Scroll-snap
 * orizzontale, hover lift, nessun JS runtime. Render == Vue (ThemeDemosTile.vue).
 * Estratta dal blueprint "Clod — Evoluzione v2" (.rs__demos).
 */
class Olobuild_ThemeDemos_Tile extends Olobuild_Tile_Base {

    protected $type     = 'themedemos';
    protected $name     = 'Demo temi (mini anteprime)';
    protected $icon     = 'dashicons-welcome-view-site';
    protected $category = 'media';
    protected $defaults = [
        'items' => [
            [ 'name' => 'Forge', 'category' => 'Software & Tech', 'zone_label' => 'Contrast', 'bg' => '#121212', 'ink' => '#f4f4f4', 'accent' => '#ff6a2b', 'font_label' => 'Big Shoulders Display', 'light' => false, 'link' => '' ],
            [ 'name' => 'Prisma', 'category' => 'Creative', 'zone_label' => 'Palette', 'bg' => '#160a24', 'ink' => '#f1e9f7', 'accent' => '#c14bff', 'font_label' => 'Big Shoulders Display', 'light' => false, 'link' => '' ],
            [ 'name' => 'Saffron', 'category' => 'Food & Drink', 'zone_label' => 'Floor plan', 'bg' => '#f6efe2', 'ink' => '#241a16', 'accent' => '#c75d3a', 'font_label' => 'Big Shoulders Display', 'light' => true, 'link' => '' ],
            [ 'name' => 'Soundwave', 'category' => 'Artist', 'zone_label' => 'Sequencer', 'bg' => '#0c0c10', 'ink' => '#ffffff', 'accent' => '#27e0a3', 'font_label' => 'Big Shoulders Display', 'light' => false, 'link' => '' ],
        ],
        'accent'                  => '',
        'card_bg'                 => '',
        'card_border_color'       => '',
        'card_border_hover_color' => '',
        'preview_height'          => 168,
        'gap'                     => 16,

        // KIT standard OLObuild — additivi, no-op coi default (sfondo none, ombra none, bordo 0)
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
        $uid = 'otd-' . wp_rand( 10000, 99999 );

        $items = is_array( $s['items'] ) ? array_values( $s['items'] ) : [];
        if ( empty( $items ) ) { return ''; }

        $acc  = $this->safe_color_css( $s['accent'] ?? '' ) ?: 'var(--olo-color-primary, #C6F24E)';
        $cbg  = $this->safe_color_css( $s['card_bg'] ?? '' ) ?: 'var(--olo-color-muted, #101218)';
        $cbd  = $this->safe_color_css( $s['card_border_color'] ?? '' ) ?: 'var(--olo-color-border, rgba(236,234,227,.10))';
        $cbdh = $this->safe_color_css( $s['card_border_hover_color'] ?? '' ) ?: 'color-mix(in srgb, var(--olo-color-text, #ECEAE3) 20%, transparent)';

        $ph = intval( $s['preview_height'] );
        $ph = $ph > 0 ? max( 100, min( 320, $ph ) ) : 168;
        $gap = intval( $s['gap'] );
        if ( $gap <= 0 ) { $gap = 16; }

        $disp = "var(--olo-font-family-heading, 'Big Shoulders Display', sans-serif)";
        $sans = "var(--olo-font-family, 'Hanken Grotesk', sans-serif)";
        $mono = "var(--olo-font-family-mono, 'Space Mono', ui-monospace, monospace)";

        // ── KIT standard OLObuild: sfondo completo + ombra + bordo sul contenitore ──
        $bg_obj  = $s['bg'] ?? null;
        $bg_decl = '';
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olobuild_CSS_Builder' ) ) {
            $bg_decl = ( new Olobuild_CSS_Builder() )->get_bg_inline_css( $bg_obj );
        }
        $shadow_css        = $this->build_shadow_decl( $s );
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );

        $box_decl = '';
        if ( $bg_decl )    { $box_decl .= rtrim( trim( $bg_decl ), ';' ) . ';'; }
        if ( $border_css ) { $box_decl .= $border_css; }
        if ( $shadow_css ) { $box_decl .= 'box-shadow:' . $shadow_css . ';'; }
        if ( $box_decl || $border_effect_css ) { $box_decl .= 'position:relative;'; }

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: every colour via the safe_color_css() whitelist (or fixed var()/color-mix literals), sizes via intval() with min()/max() clamps, fixed font-stack literals, background/shadow/border via the Olobuild_CSS_Builder/Olobuild_Tile_Base shared helpers (sanitized internally); $uid is internally generated. ?>
        <style>
            .<?php echo $uid; ?>{font-family:<?php echo $sans; ?>;<?php echo $box_decl; ?>}
            .<?php echo $uid; ?> .otd-row{display:flex;gap:<?php echo $gap; ?>px;overflow-x:auto;scroll-snap-type:x mandatory;padding-bottom:14px;-webkit-overflow-scrolling:touch;scrollbar-width:thin;}
            .<?php echo $uid; ?> .otd-row::-webkit-scrollbar{height:6px;}
            .<?php echo $uid; ?> .otd-row::-webkit-scrollbar-thumb{background:<?php echo $cbdh; ?>;border-radius:3px;}
            .<?php echo $uid; ?> .otd-card{flex:0 0 clamp(250px,28vw,320px);scroll-snap-align:start;border:1px solid <?php echo $cbd; ?>;border-radius:12px;overflow:hidden;background:<?php echo $cbg; ?>;transition:transform .18s,border-color .18s;display:block;text-decoration:none;color:inherit;}
            .<?php echo $uid; ?> .otd-card:hover{transform:translateY(-4px);border-color:<?php echo $cbdh; ?>;}
            .<?php echo $uid; ?> .otd-card:focus-visible{outline:none;box-shadow:0 0 0 3px color-mix(in srgb, <?php echo $acc; ?> 30%, transparent);}
            .<?php echo $uid; ?> .otd-pv{position:relative;height:<?php echo $ph; ?>px;background:var(--c-bg);padding:15px 16px 0;display:flex;flex-direction:column;overflow:hidden;}
            .<?php echo $uid; ?> .otd-logo{width:13px;height:13px;border-radius:4px;background:var(--c-acc);flex:none;}
            .<?php echo $uid; ?> .otd-h{font-family:var(--c-font);font-weight:800;text-transform:uppercase;font-size:29px;line-height:.95;color:var(--c-ink);margin-top:auto;letter-spacing:-.01em;white-space:nowrap;}
            .<?php echo $uid; ?> .otd-btn{height:14px;width:52px;border-radius:7px;background:var(--c-acc);margin:11px 0 13px;flex:none;}
            .<?php echo $uid; ?> .otd-z{position:absolute;right:13px;bottom:12px;font-family:<?php echo $mono; ?>;font-size:9.5px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#fff;background:rgba(8,9,12,.5);-webkit-backdrop-filter:blur(5px);backdrop-filter:blur(5px);border:1px solid rgba(255,255,255,.18);border-radius:999px;padding:4px 9px;}
            .<?php echo $uid; ?> .otd-pv.light .otd-z{background:rgba(255,255,255,.6);border-color:rgba(0,0,0,.12);color:#1a1a1a;}
            .<?php echo $uid; ?> .otd-ft{display:flex;align-items:baseline;justify-content:space-between;gap:10px;padding:13px 16px;}
            .<?php echo $uid; ?> .otd-name{font-family:<?php echo $disp; ?>;font-weight:700;font-size:19px;text-transform:uppercase;color:var(--olo-color-text, #ECEAE3);}
            .<?php echo $uid; ?> .otd-cat{font-size:11.5px;color:var(--olo-color-text-soft, #a0a298);}
            <?php echo $border_hover_css; ?><?php echo $border_effect_css; ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <div class="olo-themedemos <?php echo esc_attr( $uid ); ?>">
            <div class="otd-row">
                <?php foreach ( $items as $it ) :
                    $name  = isset( $it['name'] ) ? trim( (string) $it['name'] ) : '';
                    $cat   = isset( $it['category'] ) ? trim( (string) $it['category'] ) : '';
                    $zone  = isset( $it['zone_label'] ) ? trim( (string) $it['zone_label'] ) : '';
                    $ibg   = $this->safe_color_css( $it['bg'] ?? '' ) ?: '#121212';
                    $iink  = $this->safe_color_css( $it['ink'] ?? '' ) ?: '#f4f4f4';
                    $iacc  = $this->safe_color_css( $it['accent'] ?? '' ) ?: $acc;
                    // Whitelist del nome font item (contenuto: rappresenta il TEMA, non i ruoli del sito).
                    $font  = trim( preg_replace( '/[^a-zA-Z0-9 \-]/', '', (string) ( $it['font_label'] ?? '' ) ) );
                    if ( $font === '' ) { $font = 'Big Shoulders Display'; }
                    $fontcss = "'" . $font . "',sans-serif";
                    $light = ! empty( $it['light'] );
                    $href  = isset( $it['link'] ) ? trim( (string) $it['link'] ) : '';
                    $vars  = '--c-bg:' . $ibg . ';--c-ink:' . $iink . ';--c-acc:' . $iacc . ';--c-font:' . $fontcss . ';';
                ?>
                    <?php if ( $href !== '' ) : ?>
                        <a class="otd-card" href="<?php echo esc_url( $href ); ?>" style="<?php echo esc_attr( $vars ); ?>" data-olo-cta>
                    <?php else : ?>
                        <div class="otd-card" style="<?php echo esc_attr( $vars ); ?>">
                    <?php endif; ?>
                        <span class="otd-pv<?php echo $light ? ' light' : ''; ?>" data-olo-tilt-child>
                            <span class="otd-logo" aria-hidden="true"></span>
                            <?php if ( $name !== '' ) : ?><span class="otd-h"><?php echo esc_html( $name ); ?></span><?php endif; ?>
                            <span class="otd-btn" aria-hidden="true"></span>
                            <?php if ( $zone !== '' ) : ?><span class="otd-z"><?php echo esc_html( $zone ); ?></span><?php endif; ?>
                        </span>
                        <span class="otd-ft">
                            <?php if ( $name !== '' ) : ?><b class="otd-name"><?php echo esc_html( $name ); ?></b><?php endif; ?>
                            <?php if ( $cat !== '' ) : ?><span class="otd-cat"><?php echo esc_html( $cat ); ?></span><?php endif; ?>
                        </span>
                    <?php if ( $href !== '' ) : ?>
                        </a>
                    <?php else : ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Restituisce la dichiarazione box-shadow (valore, senza "box-shadow:")
     * dal setting shadow (preset sm/md/lg/xl o custom). '' se none.
     * Copiato dal pattern standard OLObuild (cfr. Olobuild_CategoryRail_Tile).
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
