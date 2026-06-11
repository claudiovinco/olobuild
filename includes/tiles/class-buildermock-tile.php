<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Tile Mockup Builder — illustrazione animata dell'editor OLObuild.
 * Browser bar + rail categorie + griglia tile + canvas + inspector, con una
 * "tile fantasma" che viene trascinata sul canvas in loop (effetto wow, solo CSS).
 *
 * SSR puro: nessun JS. CSS scoped per istanza (UID) → N mockup nella stessa pagina
 * non si calpestano. L'animazione del drag è @keyframes; prefers-reduced-motion la
 * ferma. Colori dai ruoli globali del cliente (var(--olo-color-primary)) di default.
 * Additivo: include il sistema bordi/ombra standard.
 */
class Olo_BuilderMock_Tile extends Olo_Tile_Base {

    protected $type     = 'buildermock';
    protected $name     = 'Mockup Builder';
    protected $icon     = 'dashicons-laptop';
    protected $category = 'marketing';
    protected $defaults = [
        'accent'        => 'var(--olo-color-primary)',
        'url_text'      => 'olobuild.it/editor',
        'cat_active'    => 'Essenziale',
        'canvas_title'  => 'Benvenuto al Resort delle Ville',
        'canvas_sub'    => 'Una struttura immersa nel verde, a 10 minuti dal mare.',
        'selected_tile' => 'Titolo',
        'drag_label'    => 'Titolo',
        'animate_drag'  => true,
        'tilt'          => 13,
        'width'         => 840,

        'shadow'                  => 'xl',
        'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-bm-' . wp_rand( 10000, 99999 );

        $accent = $this->safe_color_css( $s['accent'] ?? '' ) ?: 'var(--olo-color-primary, #e1474f)';
        $tilt   = max( 0, min( 22, intval( $s['tilt'] ) ) );
        $width  = max( 480, min( 1100, intval( $s['width'] ) ) );
        $anim   = ! empty( $s['animate_drag'] );

        $url    = esc_html( $s['url_text'] ?? '' );
        $catA   = esc_html( $s['cat_active'] ?? 'Essenziale' );
        $cTitle = esc_html( $s['canvas_title'] ?? '' );
        $cSub   = esc_html( $s['canvas_sub'] ?? '' );
        $selT   = esc_html( $s['selected_tile'] ?? 'Titolo' );
        $drag   = esc_html( $s['drag_label'] ?? 'Titolo' );

        // Rail categorie (glifo, etichetta, conteggio, attiva?)
        $cats = [
            [ '▦', 'Tutti', '97' ], [ '★', 'Preferiti', '8' ], [ '■', 'Essenziale', '9' ],
            [ '▤', 'Layout', '7' ], [ 'T', 'Testo', '10' ], [ '◑', 'Media', '20' ], [ '≡', 'Form', '8' ],
        ];
        // Card tile (glifo, etichetta)
        $tiles = [
            [ '▤', 'Contenuto' ], [ '◖', 'Immagine' ], [ '◉', 'Pulsante' ],
            [ 'T', 'Titolo' ], [ '▶', 'Video' ], [ '—', 'Divisore' ],
        ];

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: $accent via the safe_color_css() whitelist, $width/$tilt via intval() with min()/max() clamps; $uid is internally generated.
        ?>
        <style>
        .<?php echo $uid; ?>{ --bm-accent:<?php echo $accent; ?>; display:block; perspective:2400px; width:100%; }
        .<?php echo $uid; ?> .bm-stage{ position:relative; width:<?php echo $width; ?>px; max-width:none; transform:rotateY(-<?php echo $tilt; ?>deg) rotateX(7deg) rotateZ(.5deg); transform-origin:left center; }
        .<?php echo $uid; ?> .bm-frame{ width:100%; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 60px 120px -30px rgba(0,0,0,.7),0 30px 60px -40px color-mix(in srgb, var(--bm-accent) 45%, transparent); font-family:'Work Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; }
        .<?php echo $uid; ?> .bm-bar{ display:flex; align-items:center; gap:7px; padding:11px 14px; background:#fafbfc; border-bottom:1px solid #e9ecef; }
        .<?php echo $uid; ?> .bm-bar .d{ width:10px; height:10px; border-radius:50%; } .<?php echo $uid; ?> .bm-bar .d.r{background:#ff5f57}.<?php echo $uid; ?> .bm-bar .d.y{background:#febc2e}.<?php echo $uid; ?> .bm-bar .d.g{background:#28c840}
        .<?php echo $uid; ?> .bm-url{ flex:1; margin-left:10px; background:#fff; border:1px solid #e9ecef; border-radius:6px; padding:5px 10px; font:11px/1 ui-monospace,Menlo,monospace; color:#94a3b8; display:flex; align-items:center; gap:7px; } .<?php echo $uid; ?> .bm-url::before{ content:""; width:7px; height:7px; border-radius:50%; background:#22c55e; }
        .<?php echo $uid; ?> .bm-body{ display:grid; grid-template-columns:300px 1fr 268px; height:430px; }
        .<?php echo $uid; ?> .bm-side{ display:grid; grid-template-columns:62px 1fr; border-right:1px solid #e9ecef; background:#fff; }
        .<?php echo $uid; ?> .bm-rail{ background:#f8f9fa; border-right:1px solid #eef0f3; padding:6px 0; }
        .<?php echo $uid; ?> .bm-cat{ height:52px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:3px; position:relative; color:#64748b; }
        .<?php echo $uid; ?> .bm-cat.on{ background:#fff; color:#1e293b; } .<?php echo $uid; ?> .bm-cat.on::before{ content:""; position:absolute; left:0; top:8px; bottom:8px; width:2px; background:var(--bm-accent); border-radius:0 2px 2px 0; }
        .<?php echo $uid; ?> .bm-cat .ci{ width:24px; height:24px; border-radius:6px; display:grid; place-items:center; font-size:13px; font-weight:600; } .<?php echo $uid; ?> .bm-cat.on .ci{ background:color-mix(in srgb, var(--bm-accent) 10%, transparent); }
        .<?php echo $uid; ?> .bm-cat .cl{ font-size:9px; font-weight:500; } .<?php echo $uid; ?> .bm-cat .cn{ position:absolute; top:5px; right:7px; font:700 8px/1 sans-serif; padding:1px 4px; border-radius:99px; background:#e9ecef; color:#64748b; } .<?php echo $uid; ?> .bm-cat.on .cn{ background:color-mix(in srgb, var(--bm-accent) 16%, transparent); color:#b8323a; }
        .<?php echo $uid; ?> .bm-cards{ padding:13px; display:flex; flex-direction:column; gap:10px; }
        .<?php echo $uid; ?> .bm-ch{ display:flex; align-items:center; gap:6px; } .<?php echo $uid; ?> .bm-ch .dt{ width:8px; height:8px; border-radius:50%; background:var(--bm-accent); } .<?php echo $uid; ?> .bm-ch b{ font-size:13px; font-weight:700; color:#1e293b; } .<?php echo $uid; ?> .bm-ch .n{ margin-left:auto; font:600 10px/1 sans-serif; padding:1px 6px; border-radius:99px; background:#f1f5f9; color:#64748b; }
        .<?php echo $uid; ?> .bm-search{ display:flex; align-items:center; gap:6px; padding:6px 9px; background:#f8f9fa; border:1px solid #eef0f3; border-radius:8px; font-size:11px; color:#94a3b8; }
        .<?php echo $uid; ?> .bm-grid{ display:grid; grid-template-columns:1fr 1fr; gap:7px; }
        .<?php echo $uid; ?> .bm-tc{ padding:10px; border:1px solid #e9ecef; border-radius:8px; background:#fff; display:flex; flex-direction:column; gap:6px; } .<?php echo $uid; ?> .bm-tc.sel{ border-color:var(--bm-accent); box-shadow:0 4px 12px color-mix(in srgb, var(--bm-accent) 14%, transparent); }
        .<?php echo $uid; ?> .bm-tc .ti{ width:30px; height:30px; border-radius:6px; background:#f1f5f9; display:grid; place-items:center; font-size:13px; color:#475569; } .<?php echo $uid; ?> .bm-tc.sel .ti{ background:#fff; color:var(--bm-accent); }
        .<?php echo $uid; ?> .bm-tc .tl{ font-size:11px; font-weight:500; color:#1e293b; }
        .<?php echo $uid; ?> .bm-canvas{ background:#f3f4f6; padding:16px; }
        .<?php echo $uid; ?> .bm-cv{ background:#fff; border-radius:8px; height:100%; border:1px solid #e9ecef; overflow:hidden; }
        .<?php echo $uid; ?> .bm-hero{ position:relative; height:150px; background:linear-gradient(135deg, var(--bm-accent), color-mix(in srgb, var(--bm-accent) 45%, #000)); color:#fff; padding:18px; }
        .<?php echo $uid; ?> .bm-hero .eb{ font:600 9px/1 sans-serif; opacity:.7; text-transform:uppercase; letter-spacing:.1em; margin-bottom:8px; } .<?php echo $uid; ?> .bm-hero h4{ font:700 20px/1.15 'Work Sans',sans-serif; margin:0 0 5px; max-width:62%; } .<?php echo $uid; ?> .bm-hero p{ font:400 11px/1.4 'Work Sans',sans-serif; opacity:.85; max-width:52%; margin:0; }
        .<?php echo $uid; ?> .bm-hero .sel{ position:absolute; inset:6px; border:1.5px dashed rgba(255,255,255,.6); border-radius:6px; } .<?php echo $uid; ?> .bm-hero .tag{ position:absolute; top:0; left:14px; background:var(--bm-accent); color:#fff; font:700 9px/1 sans-serif; padding:3px 7px; border-radius:0 0 4px 4px; }
        .<?php echo $uid; ?> .bm-row3{ padding:16px; display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; }
        .<?php echo $uid; ?> .bm-row3 span{ border:1px dashed #d1d5db; border-radius:8px; min-height:78px; padding:12px; display:flex; flex-direction:column; gap:6px; } .<?php echo $uid; ?> .bm-row3 span::before{ content:""; height:8px; width:55%; background:#f1f5f9; border-radius:3px; } .<?php echo $uid; ?> .bm-row3 span::after{ content:""; height:5px; background:#f1f5f9; border-radius:2px; }
        .<?php echo $uid; ?> .bm-insp{ border-left:1px solid #e9ecef; background:#fff; padding:14px; display:flex; flex-direction:column; gap:12px; }
        .<?php echo $uid; ?> .bm-bc{ font-size:10px; color:#64748b; display:flex; gap:5px; align-items:center; } .<?php echo $uid; ?> .bm-bc .bd{ background:#faf5ff; color:#7e22ce; padding:2px 6px; border-radius:4px; font:700 9px/1 sans-serif; }
        .<?php echo $uid; ?> .bm-it{ font:700 14px/1 'Work Sans',sans-serif; color:#1e293b; }
        .<?php echo $uid; ?> .bm-tabs{ display:flex; gap:3px; padding:2px; border:1px solid #e9ecef; border-radius:8px; } .<?php echo $uid; ?> .bm-tabs span{ flex:1; text-align:center; font:600 11px/1 sans-serif; padding:6px 0; border-radius:6px; color:#64748b; } .<?php echo $uid; ?> .bm-tabs span.on{ background:var(--bm-accent); color:#fff; }
        .<?php echo $uid; ?> .bm-field{ height:30px; border:1px solid #e9ecef; border-radius:6px; }
        .<?php echo $uid; ?> .bm-dec{ background:#f8f9fa; border-radius:8px; padding:11px; display:flex; flex-direction:column; gap:8px; } .<?php echo $uid; ?> .bm-dec .dh{ font:700 9px/1 sans-serif; color:#64748b; text-transform:uppercase; letter-spacing:.06em; }
        .<?php echo $uid; ?> .bm-sw{ display:flex; gap:4px; } .<?php echo $uid; ?> .bm-sw i{ width:18px; height:18px; border-radius:5px; } .<?php echo $uid; ?> .bm-sw i:nth-child(1){background:var(--bm-accent);box-shadow:0 0 0 2px var(--bm-accent)}.<?php echo $uid; ?> .bm-sw i:nth-child(2){background:#1f2937}.<?php echo $uid; ?> .bm-sw i:nth-child(3){background:#fff;border:1px solid #e9ecef}.<?php echo $uid; ?> .bm-sw i:nth-child(4){background:#f59e0b}.<?php echo $uid; ?> .bm-sw i:nth-child(5){background:#22c55e}.<?php echo $uid; ?> .bm-sw i:nth-child(6){background:#0ea5e9}
        .<?php echo $uid; ?> .bm-sl{ display:flex; align-items:center; gap:8px; } .<?php echo $uid; ?> .bm-sl .tr{ flex:1; height:5px; background:#e9ecef; border-radius:99px; position:relative; } .<?php echo $uid; ?> .bm-sl .tr b{ position:absolute; left:0; top:0; bottom:0; width:70%; background:linear-gradient(90deg, color-mix(in srgb, var(--bm-accent) 25%, transparent), var(--bm-accent)); border-radius:99px; } .<?php echo $uid; ?> .bm-sl .vv{ font:600 10px/1 sans-serif; color:#64748b; }
        /* Drag chip + cursore */
        .<?php echo $uid; ?> .bm-chip{ position:absolute; z-index:6; left:120px; top:300px; width:96px; padding:11px; background:#fff; border-radius:11px; box-shadow:0 18px 40px rgba(0,0,0,.4),0 0 0 1px color-mix(in srgb, var(--bm-accent) 40%, transparent); }
        .<?php echo $uid; ?> .bm-chip .ic{ width:30px; height:30px; border-radius:7px; background:color-mix(in srgb, var(--bm-accent) 10%, transparent); color:var(--bm-accent); display:grid; place-items:center; margin-bottom:6px; }
        .<?php echo $uid; ?> .bm-chip .lbl{ font:600 11px/1 'Work Sans',sans-serif; color:#1e293b; }
        .<?php echo $uid; ?> .bm-chip .cur{ position:absolute; right:-12px; bottom:-15px; }
        <?php if ( $anim ) : ?>
        .<?php echo $uid; ?> .bm-chip{ animation:bm-drag-<?php echo $uid; ?> 5.5s cubic-bezier(.5,0,.2,1) infinite; }
        @keyframes bm-drag-<?php echo $uid; ?>{
            0%{ transform:translate(0,0) rotate(-5deg); opacity:0; }
            7%{ opacity:1; transform:translate(0,-6px) rotate(-5deg); }
            42%{ transform:translate(250px,-150px) rotate(-2deg); opacity:1; }
            56%{ transform:translate(250px,-110px) rotate(0deg) scale(.97); opacity:1; }
            66%{ transform:translate(250px,-110px) rotate(0deg) scale(1); opacity:0; }
            100%{ transform:translate(0,0) rotate(-5deg); opacity:0; }
        }
        @media (prefers-reduced-motion: reduce){ .<?php echo $uid; ?> .bm-chip{ animation:none; opacity:1; } }
        <?php endif; ?>
        @media (max-width:940px){ .<?php echo $uid; ?> .bm-stage{ width:100%; transform:none; } .<?php echo $uid; ?> .bm-chip{ display:none; } }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <div class="<?php echo esc_attr( $uid ); ?> olo-buildermock">
          <div class="bm-stage">
            <div class="bm-frame">
              <div class="bm-bar"><span class="d r"></span><span class="d y"></span><span class="d g"></span><div class="bm-url"><?php echo $url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped via esc_html() at assignment above ?></div></div>
              <div class="bm-body">
                <div class="bm-side">
                  <div class="bm-rail">
                    <?php foreach ( $cats as $c ) :
                        $on = ( $c[1] === ( $s['cat_active'] ?? 'Essenziale' ) ) ? ' on' : '';
                    ?>
                    <div class="bm-cat<?php echo $on; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed ' on'/'' literal from the ternary above ?>"><span class="ci"><?php echo esc_html( $c[0] ); ?></span><span class="cl"><?php echo esc_html( $c[1] ); ?></span><span class="cn"><?php echo esc_html( $c[2] ); ?></span></div>
                    <?php endforeach; ?>
                  </div>
                  <div class="bm-cards">
                    <div class="bm-ch"><span class="dt"></span><b><?php echo $catA; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped via esc_html() at assignment above ?></b><span class="n">9</span></div>
                    <div class="bm-search">&#9906; Cerca&hellip;</div>
                    <div class="bm-grid">
                      <?php foreach ( $tiles as $tcard ) :
                          $sel = ( $tcard[1] === ( $s['selected_tile'] ?? 'Titolo' ) ) ? ' sel' : '';
                      ?>
                      <div class="bm-tc<?php echo $sel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed ' sel'/'' literal from the ternary above ?>"><span class="ti"><?php echo esc_html( $tcard[0] ); ?></span><span class="tl"><?php echo esc_html( $tcard[1] ); ?></span></div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>
                <div class="bm-canvas"><div class="bm-cv">
                  <div class="bm-hero"><span class="tag">HERO</span><div class="eb">Hero section</div><h4><?php echo $cTitle; ?></h4><p><?php echo $cSub; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $cTitle and $cSub escaped via esc_html() at assignment above ?></p><div class="sel"></div></div>
                  <div class="bm-row3"><span></span><span></span><span></span></div>
                </div></div>
                <div class="bm-insp">
                  <div class="bm-bc"><span class="bd">BODY</span> Sezione &rsaquo; Row &rsaquo; <b><?php echo $selT; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped via esc_html() at assignment above ?></b></div>
                  <div class="bm-it">Impostazioni <?php echo $selT; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped via esc_html() at assignment above ?></div>
                  <div class="bm-tabs"><span class="on">Contenuto</span><span>Stile</span><span>Avanzate</span></div>
                  <div class="bm-field"></div>
                  <div class="bm-dec"><div class="dh">Decorazione</div><div class="bm-sw"><i></i><i></i><i></i><i></i><i></i><i></i></div><div class="bm-sl"><div class="tr"><b></b></div><span class="vv">70%</span></div></div>
                </div>
              </div>
            </div>
            <div class="bm-chip">
              <div class="ic"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="4 7 4 4 20 4 20 7"/><line x1="9" y1="20" x2="15" y2="20"/><line x1="12" y1="4" x2="12" y2="20"/></svg></div>
              <div class="lbl"><?php echo $drag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped via esc_html() at assignment above ?></div>
              <div class="cur"><svg viewBox="0 0 24 24" width="22" height="22" fill="#0b0d12" stroke="#fff" stroke-width="1"><path d="M5 3l4 18 3-7 7-3z"/></svg></div>
            </div>
          </div>
        </div>
        <?php
        // Sistema bordi/ombra standard (come goo/particlefx).
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid} .bm-frame", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid} .bm-frame", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) {
                echo ".{$uid} .bm-frame{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_css() from sanitized border settings; $uid is internally generated
            }
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base border helpers from sanitized border settings
        }
        return ob_get_clean();
    }
}
