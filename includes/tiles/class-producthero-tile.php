<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Hero — Product (SaaS) : hero centrato SaaS/tech. Eyebrow PILL + headline
 * centrata con parola-accento (gradiente), subhead, 2 CTA, su un RADIAL GLOW + griglia
 * faint mascherata. Sotto, mockup PRODUCT in cornice browser/window (barra con 3 pallini
 * + opzionale URL): media placeholder (modo "media") OPPURE dashboard con KPI cards
 * (label/value/delta) + chart a barre inline (modo "dashboard"). Tutto puro CSS/SVG,
 * nessun JS, nessun dato reale.
 *
 * Parametrizzato per riprodurre 1:1 i blueprint OLOthemes Circuit (indigo glow + grid +
 * cornice browser) e DataFold (teal glow + KPI + bar chart). Default fedeli a Circuit.
 * Render == Vue (ProductHeroTile.vue). Nessun JS.
 */
class Olo_ProductHero_Tile extends Olo_Tile_Base {

    protected $type     = 'producthero';
    protected $name     = 'Hero — Product (SaaS)';
    protected $icon     = 'dashicons-desktop';
    protected $category = 'marketing';
    protected $defaults = [
        // Content
        'pill_pre'      => 'New',
        'pill_text'     => 'Circuit 3.0 — now with live workflows',
        'headline_text' => 'Ship reliable software,',
        'accent_text'   => 'without the busywork.',
        'subhead'       => 'Circuit connects the tools your team already uses, automates the hand-offs, and gives everyone one honest view of every release.',
        'cta1_text'     => 'Start free — no card',
        'cta1_url'      => '#',
        'cta2_text'     => 'See how it works',
        'cta2_url'      => '#',

        // Backdrop
        'glow_on'       => true,
        'glow_color'    => '#6c8cff',
        'grid_on'       => true,
        'grid_color'    => 'rgba(255,255,255,0.04)',
        'grid_size'     => 48,

        // Mockup
        'mock_mode'     => 'dashboard', // 'media' | 'dashboard'
        'mock_url'      => 'app.circuit.io / workspace / releases',
        'mock_label'    => 'product — workflow board &amp; live status dashboard',
        'kpis'          => [
            [ 'label' => 'Net revenue',     'value' => '$4.82M', 'delta' => '▲ 18.4% MoM', 'down' => '' ],
            [ 'label' => 'Active accounts', 'value' => '12,408', 'delta' => '▲ 6.1% MoM',  'down' => '' ],
            [ 'label' => 'Churn',           'value' => '1.9%',   'delta' => '▼ 0.3 pts',    'down' => '1' ],
        ],
        'chart_title'   => 'Revenue by week',
        'chart_meta'    => 'last 12 weeks · live',
        'bars'          => [
            [ 'h' => 38, 'label' => 'w1',  'alt' => '' ],
            [ 'h' => 46, 'label' => 'w2',  'alt' => '' ],
            [ 'h' => 41, 'label' => 'w3',  'alt' => '' ],
            [ 'h' => 54, 'label' => 'w4',  'alt' => '' ],
            [ 'h' => 60, 'label' => 'w5',  'alt' => '' ],
            [ 'h' => 52, 'label' => 'w6',  'alt' => '' ],
            [ 'h' => 68, 'label' => 'w7',  'alt' => '' ],
            [ 'h' => 74, 'label' => 'w8',  'alt' => '' ],
            [ 'h' => 66, 'label' => 'w9',  'alt' => '1' ],
            [ 'h' => 82, 'label' => 'w10', 'alt' => '' ],
            [ 'h' => 90, 'label' => 'w11', 'alt' => '' ],
            [ 'h' => 100,'label' => 'w12', 'alt' => '1' ],
        ],

        // Colors / theme
        'bg_color'      => '#0b0d18',
        'panel_color'   => '#141a2e',
        'panel2_color'  => '#1b2238',
        'cell_color'    => '#11142270',
        'accent'        => '#6c8cff',
        'accent2'       => '#b08bff',
        'accent_on'     => '#ffffff',
        'down_color'    => '', // vuoto = usa accent2 (per blueprint con colore neg. dedicato, es. DataFold amber)
        'text_color'    => '#ffffff',
        'sub_color'     => '#8a90a8',
        'pill_text_color' => '#c9cde0',
        'pill_bg'       => 'rgba(255,255,255,0.05)',
        'line_color'    => 'rgba(255,255,255,0.09)',
        'pill_mono'     => false,
        'mono_meta'     => true,

        // Spaziatura + Raggio (additivi, no-op coi default).
        // content_padding = padding dell'inner .oph-in (oggi '0 28px' fisso) → invariato.
        'content_padding' => [ 'top' => 0, 'right' => 28, 'bottom' => 0, 'left' => 28 ],
        // frame_radius = angoli cornice browser .oph-frame (oggi '16px 16px 0 0') → invariato.
        'frame_radius'    => [ 'tl' => 16, 'tr' => 16, 'br' => 0, 'bl' => 0 ],
        // kpi_radius = angoli card KPI .oph-kpi (oggi '11px') → invariato.
        'kpi_radius'      => [ 'tl' => 11, 'tr' => 11, 'br' => 11, 'bl' => 11 ],

        // KIT standard OLObuild — sfondo completo + ombra + bordo sul contenitore.
        // Default no-op: bg none / shadow none / border 0 → render invariato.
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
        $uid = 'oph-' . wp_rand( 10000, 99999 );

        $bg     = $this->safe_color_css( $s['bg_color'] ?? '' )      ?: '#0b0d18';
        $panel  = $this->safe_color_css( $s['panel_color'] ?? '' )   ?: '#141a2e';
        $panel2 = $this->safe_color_css( $s['panel2_color'] ?? '' )  ?: '#1b2238';
        $cell   = $this->safe_color_css( $s['cell_color'] ?? '' )    ?: '#11142270';
        $accent = $this->safe_color_css( $s['accent'] ?? '' )        ?: 'var(--olo-color-primary, #6c8cff)';
        $acc2   = $this->safe_color_css( $s['accent2'] ?? '' )       ?: '#b08bff';
        $accOn  = $this->safe_color_css( $s['accent_on'] ?? '' )     ?: '#ffffff';
        $downC  = $this->safe_color_css( $s['down_color'] ?? '' )    ?: $acc2;
        $pillBg = $this->safe_color_css( $s['pill_bg'] ?? '' )       ?: 'rgba(255,255,255,0.05)';
        $txt    = $this->safe_color_css( $s['text_color'] ?? '' )    ?: '#ffffff';
        $sub    = $this->safe_color_css( $s['sub_color'] ?? '' )     ?: '#8a90a8';
        $pillC  = $this->safe_color_css( $s['pill_text_color'] ?? '' ) ?: '#c9cde0';
        $line   = $this->safe_color_css( $s['line_color'] ?? '' )    ?: 'rgba(255,255,255,0.09)';
        $glowC  = $this->safe_color_css( $s['glow_color'] ?? '' )    ?: '#6c8cff';
        $gridC  = $this->safe_color_css( $s['grid_color'] ?? '' )    ?: 'rgba(255,255,255,0.04)';

        $glowOn = ! empty( $s['glow_on'] );
        $gridOn = ! empty( $s['grid_on'] );
        $gsize  = max( 16, min( 120, intval( $s['grid_size'] ) ) );
        $mode   = ( ( $s['mock_mode'] ?? 'dashboard' ) === 'media' ) ? 'media' : 'dashboard';
        $pillMono = ! empty( $s['pill_mono'] );
        $monoMeta = ! empty( $s['mono_meta'] );

        $glowRgb = $this->color_to_rgb( $glowC ) ?: '108,140,255';

        $disp = "var(--olo-font-family-heading, 'Space Grotesk',-apple-system,sans-serif)";
        $sans = "var(--olo-font-family, 'Work Sans',-apple-system,sans-serif)";
        $mono = "var(--olo-font-family-mono, ui-monospace,'SF Mono',Menlo,monospace)";

        $kpis = is_array( $s['kpis'] ) ? array_values( $s['kpis'] ) : [];
        $bars = is_array( $s['bars'] ) ? array_values( $s['bars'] ) : [];

        // ── KIT standard OLObuild — sfondo completo + ombra (sul contenitore) ──
        // Sfondo completo: override del bg di sezione SOLO se valorizzato (default none → invariato).
        $bg_obj  = $s['bg'] ?? null;
        $bg_decl = '';
        if ( is_array( $bg_obj ) && ! empty( $bg_obj['type'] ) && $bg_obj['type'] !== 'none' && class_exists( 'Olo_CSS_Builder' ) ) {
            $bg_decl = ( new Olo_CSS_Builder() )->get_bg_inline_css( $bg_obj );
        }
        $kit_bg_css = $bg_decl ? rtrim( $bg_decl, '; ' ) . ';' : '';
        // Ombra (preset/custom). '' = nessuna → invariato.
        $shadow_css = $this->build_shadow_decl( $s );
        $kit_shadow_css = $shadow_css ? "box-shadow:{$shadow_css};" : '';

        // ── Spaziatura + Raggio (additivi, no-op coi default) ──
        // Padding inner: default '0px 28px 0px 28px' (== '0 28px' attuale).
        $in_pad = Olo_Tile_Utils::spacing_css( $s['content_padding'] ?? [ 'top' => 0, 'right' => 28, 'bottom' => 0, 'left' => 28 ], 0 );
        // Raggio cornice browser: default '16px 16px 0px 0px' (== '16px 16px 0 0' attuale).
        $frame_radius = $this->build_border_radius_css( $s['frame_radius'] ?? [] );
        if ( '' === $frame_radius ) { $frame_radius = '0'; }
        // Raggio card KPI: default '11px 11px 11px 11px' (== '11px' attuale).
        $kpi_radius = $this->build_border_radius_css( $s['kpi_radius'] ?? [] );
        if ( '' === $kpi_radius ) { $kpi_radius = '0'; }

        ob_start();
        ?>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() for every colour, color_to_rgb() digit triplets, intval()-clamped grid size, Olo_Tile_Utils::spacing_css()/build_border_radius_css() int-built values, internal Olo_CSS_Builder::get_bg_inline_css()/build_shadow_decl() helpers, fixed font-stack literals, internal wp_rand() uid. ?>
        <style>
            .<?php echo $uid; ?>{position:relative;overflow:hidden;text-align:center;padding:clamp(56px,8vw,104px) 0 0;background:<?php echo $bg; ?>;color:<?php echo $txt; ?>;font-family:<?php echo $sans; ?>;<?php echo $kit_bg_css . $kit_shadow_css; ?>}
            .<?php echo $uid; ?> .oph-glow{position:absolute;top:-200px;left:50%;transform:translateX(-50%);width:760px;height:560px;border-radius:50%;filter:blur(90px);pointer-events:none;background:radial-gradient(circle, rgba(<?php echo $glowRgb; ?>,.34) 0%, rgba(<?php echo $glowRgb; ?>,0) 70%);}
            .<?php echo $uid; ?> .oph-grid{position:absolute;inset:0;pointer-events:none;opacity:.5;background-image:linear-gradient(<?php echo $gridC; ?> 1px, transparent 1px), linear-gradient(90deg, <?php echo $gridC; ?> 1px, transparent 1px);background-size:<?php echo (int) $gsize; ?>px <?php echo (int) $gsize; ?>px;-webkit-mask:radial-gradient(70% 60% at 50% 30%, #000, transparent 75%);mask:radial-gradient(70% 60% at 50% 30%, #000, transparent 75%);}
            .<?php echo $uid; ?> .oph-in{position:relative;z-index:2;max-width:820px;margin:0 auto;padding:<?php echo $in_pad; ?>;}
            .<?php echo $uid; ?> .oph-pill{display:inline-flex;align-items:center;gap:9px;padding:6px 14px;border-radius:999px;background:<?php echo $pillBg; ?>;border:1px solid <?php echo $line; ?>;font-size:13px;color:<?php echo $pillC; ?>;margin-bottom:26px;<?php echo $pillMono ? 'font-family:' . $mono . ';font-size:12px;' : ''; ?>}
            .<?php echo $uid; ?> .oph-pill b{color:<?php echo $accent; ?>;font-weight:600;}
            .<?php echo $uid; ?> .oph-h{font-family:<?php echo $disp; ?>;font-weight:600;color:<?php echo $txt; ?>;font-size:clamp(40px,6.4vw,78px);line-height:1.02;letter-spacing:-.02em;margin:0;}
            .<?php echo $uid; ?> .oph-grad{background:linear-gradient(120deg,<?php echo $accent; ?>,<?php echo $acc2; ?>);-webkit-background-clip:text;background-clip:text;color:transparent;}
            .<?php echo $uid; ?> .oph-sub{font-size:18px;line-height:1.6;color:<?php echo $sub; ?>;max-width:560px;margin:24px auto 30px;}
            .<?php echo $uid; ?> .oph-cta{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;}
            .<?php echo $uid; ?> .oph-btn{display:inline-flex;align-items:center;gap:8px;padding:15px 28px;border-radius:9px;font-family:<?php echo $sans; ?>;font-weight:600;font-size:15px;text-decoration:none;cursor:pointer;border:0;transition:transform .15s,background .2s,box-shadow .2s,filter .2s;}
            .<?php echo $uid; ?> .oph-btn svg{width:16px;height:16px;}
            .<?php echo $uid; ?> .oph-btn:hover{transform:translateY(-2px);}
            .<?php echo $uid; ?> .oph-btn--solid{background:<?php echo $accent; ?>;color:<?php echo $accOn; ?>;box-shadow:0 8px 24px -8px rgba(<?php echo $glowRgb; ?>,.6);}
            .<?php echo $uid; ?> .oph-btn--solid:hover{filter:brightness(1.06);}
            .<?php echo $uid; ?> .oph-btn--ghost{background:rgba(255,255,255,.06);color:#fff;border:1px solid rgba(255,255,255,.16);}
            .<?php echo $uid; ?> .oph-btn:focus-visible{outline:2px solid <?php echo $accent; ?>;outline-offset:3px;}
            .<?php echo $uid; ?> .oph-mockwrap{position:relative;z-index:2;max-width:1020px;margin:clamp(48px,7vw,84px) auto 0;padding:0 28px;}
            .<?php echo $uid; ?> .oph-frame{border:1px solid <?php echo $line; ?>;border-radius:<?php echo $frame_radius; ?>;background:<?php echo $panel; ?>;overflow:hidden;box-shadow:0 -10px 80px -20px rgba(<?php echo $glowRgb; ?>,.4);}
            .<?php echo $uid; ?> .oph-bar{display:flex;align-items:center;gap:7px;padding:13px 16px;border-bottom:1px solid <?php echo $line; ?>;background:<?php echo $panel2; ?>;}
            .<?php echo $uid; ?> .oph-bar i{width:11px;height:11px;border-radius:50%;background:rgba(255,255,255,.18);}
            .<?php echo $uid; ?> .oph-bar .oph-url{margin-left:14px;font-family:<?php echo $mono; ?>;font-size:11px;color:<?php echo $sub; ?>;}
            .<?php echo $uid; ?> .oph-media{position:relative;overflow:hidden;aspect-ratio:16/8.5;background:<?php echo $panel; ?>;background-image:repeating-linear-gradient(135deg, rgba(255,255,255,.035) 0 16px, transparent 16px 32px);}
            .<?php echo $uid; ?> .oph-medialabel{position:absolute;left:14px;bottom:12px;right:14px;font-family:<?php echo $mono; ?>;font-size:10.5px;letter-spacing:.03em;color:rgba(255,255,255,.42);text-transform:uppercase;text-align:left;}
            .<?php echo $uid; ?> .oph-body{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;padding:20px;text-align:left;}
            .<?php echo $uid; ?> .oph-kpi{background:<?php echo $cell; ?>;border:1px solid <?php echo $line; ?>;border-radius:<?php echo $kpi_radius; ?>;padding:16px;}
            .<?php echo $uid; ?> .oph-kpi .oph-k{font-family:<?php echo $mono; ?>;font-size:10.5px;letter-spacing:.06em;text-transform:uppercase;color:<?php echo $sub; ?>;}
            .<?php echo $uid; ?> .oph-kpi .oph-v{font-family:<?php echo $disp; ?>;font-weight:700;font-size:26px;color:<?php echo $txt; ?>;margin:7px 0 4px;}
            .<?php echo $uid; ?> .oph-kpi .oph-t{font-family:<?php echo $mono; ?>;font-size:11px;color:<?php echo $accent; ?>;}
            .<?php echo $uid; ?> .oph-kpi .oph-t.oph-dn{color:<?php echo $downC; ?>;}
            .<?php echo $uid; ?> .oph-chart{grid-column:1/-1;background:<?php echo $cell; ?>;border:1px solid <?php echo $line; ?>;border-radius:11px;padding:18px 18px 10px;}
            .<?php echo $uid; ?> .oph-chhead{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;}
            .<?php echo $uid; ?> .oph-chhead b{font-family:<?php echo $disp; ?>;font-size:14px;color:<?php echo $txt; ?>;}
            .<?php echo $uid; ?> .oph-chhead span{font-family:<?php echo $monoMeta ? $mono : $sans; ?>;font-size:11px;color:<?php echo $sub; ?>;}
            .<?php echo $uid; ?> .oph-bars{display:flex;align-items:flex-end;gap:7px;height:120px;}
            .<?php echo $uid; ?> .oph-col{flex:1;display:flex;flex-direction:column;justify-content:flex-end;gap:3px;height:100%;}
            .<?php echo $uid; ?> .oph-col i{display:block;width:100%;border-radius:3px 3px 0 0;background:linear-gradient(180deg,<?php echo $accent; ?>,rgba(<?php echo $glowRgb; ?>,.25));min-height:4px;}
            .<?php echo $uid; ?> .oph-col i.oph-b2{background:linear-gradient(180deg,<?php echo $acc2; ?>,rgba(<?php echo $this->color_to_rgb( $acc2 ) ?: '176,139,255'; ?>,.2));}
            .<?php echo $uid; ?> .oph-col span{font-family:<?php echo $mono; ?>;font-size:9.5px;color:<?php echo $sub; ?>;text-align:center;}
            @media(max-width:680px){.<?php echo $uid; ?> .oph-body{grid-template-columns:1fr 1fr;}}
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <section class="olo-producthero <?php echo esc_attr( $uid ); ?>">
            <?php if ( $glowOn ) : ?><span class="oph-glow"></span><?php endif; ?>
            <?php if ( $gridOn ) : ?><span class="oph-grid"></span><?php endif; ?>
            <div class="oph-in">
                <?php if ( ! empty( $s['pill_text'] ) || ! empty( $s['pill_pre'] ) ) : ?>
                <span class="oph-pill"><?php if ( ! empty( $s['pill_pre'] ) ) : ?><b><?php echo esc_html( $s['pill_pre'] ); ?></b> · <?php endif; ?><?php echo esc_html( $s['pill_text'] ); ?></span>
                <?php endif; ?>
                <h1 class="oph-h"><?php echo esc_html( $s['headline_text'] ); ?><?php if ( ! empty( $s['accent_text'] ) ) : ?><br><span class="oph-grad"><?php echo esc_html( $s['accent_text'] ); ?></span><?php endif; ?></h1>
                <?php if ( ! empty( $s['subhead'] ) ) : ?><p class="oph-sub"><?php echo esc_html( $s['subhead'] ); ?></p><?php endif; ?>
                <div class="oph-cta">
                    <?php if ( ! empty( $s['cta1_text'] ) ) : ?><a class="oph-btn oph-btn--solid" href="<?php echo esc_url( $s['cta1_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta1_text'] ); ?></a><?php endif; ?>
                    <?php if ( ! empty( $s['cta2_text'] ) ) : ?><a class="oph-btn oph-btn--ghost" href="<?php echo esc_url( $s['cta2_url'] ?: '#' ); ?>"><?php echo esc_html( $s['cta2_text'] ); ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a><?php endif; ?>
                </div>
            </div>
            <div class="oph-mockwrap">
                <div class="oph-frame">
                    <div class="oph-bar"><i></i><i></i><i></i><?php if ( $mode === 'dashboard' && ! empty( $s['mock_url'] ) ) : ?><span class="oph-url"><?php echo esc_html( $s['mock_url'] ); ?></span><?php endif; ?></div>
                    <?php if ( $mode === 'media' ) : ?>
                        <div class="oph-media"><span class="oph-medialabel"><?php echo esc_html( html_entity_decode( (string) $s['mock_label'], ENT_QUOTES ) ); ?></span></div>
                    <?php else : ?>
                        <div class="oph-body">
                            <?php foreach ( $kpis as $k ) :
                                $kl = esc_html( $k['label'] ?? '' );
                                $kv = esc_html( $k['value'] ?? '' );
                                $kt = esc_html( $k['delta'] ?? '' );
                                $kd = ! empty( $k['down'] ) ? ' oph-dn' : '';
                            ?>
                            <div class="oph-kpi"><div class="oph-k"><?php echo $kl; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $kl/$kv/$kt are esc_html()'d at assignment above; $kd is a fixed class literal. ?></div><div class="oph-v"><?php echo $kv; ?></div><div class="oph-t<?php echo $kd; ?>"><?php echo $kt; ?></div></div>
                            <?php endforeach; ?>
                            <div class="oph-chart">
                                <div class="oph-chhead"><b><?php echo esc_html( $s['chart_title'] ); ?></b><span><?php echo esc_html( $s['chart_meta'] ); ?></span></div>
                                <div class="oph-bars">
                                    <?php foreach ( $bars as $b ) :
                                        $bh  = max( 0, min( 100, intval( $b['h'] ?? 0 ) ) );
                                        $bl  = esc_html( $b['label'] ?? '' );
                                        $b2  = ! empty( $b['alt'] ) ? ' class="oph-b2"' : '';
                                    ?>
                                    <div class="oph-col"><i<?php echo $b2; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $b2 is a fixed class-attribute literal from the ternary above; $bl is esc_html()'d at assignment above. ?> style="height:<?php echo (int) $bh; ?>%"></i><span><?php echo $bl; ?></span></div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php
        // ── Sistema bordi standard (KIT OLObuild, come particlefx) ────────
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) {
                echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- border CSS from base-class build_border_css() (intval'd widths, safe_color_css()'d colours), internal wp_rand() uid.
            }
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- border CSS from base-class build_border_hover_css()/build_border_effect_css() helpers (intval'd values, safe_color_css()'d colours).
        }
        return ob_get_clean();
    }

    /**
     * Restituisce la dichiarazione box-shadow (valore, senza "box-shadow:")
     * dal setting shadow (preset sm/md/lg/xl o custom). '' se none.
     * Copiato 1:1 da Olo_Particlefx_Tile (KIT standard OLObuild).
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
