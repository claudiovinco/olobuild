// ─────────────────────────────────────────────────────────────────────────────
// Generatore template Olobuild "Try Home v2" — restyle sandbox (tile-native).
// Fonte design: "OLObuild - Try sandbox restyle v2.html" (TryPageV2, wow-effects).
//
// Filosofia: contenuto + effetti = TILE NATIVE Olobuild.
//   • struttura: section / row / column
//   • testo ricco (h1/h2 + <em> rosso-corsivo, lead con <strong>): text-block
//     (rende HTML via wp_kses_post — la headline tile strippa i tag, quindi NO)
//   • azioni: button (atomiche)  · eyebrow + chip live: badge
//   • wow: goo (sfondo metaball hero), asciiviz (equalizer CTA),
//          text_effect (scramble/glitch/typewriter su text-block & iconbox),
//          border_effect neon-pulse, mouse_tilt + transform + custom_css (advanced)
//   • countdown evergreen 12h · counter (numeri CTA)
//   • card showcase / step / limit: iconbox (title/desc PLAIN → decori via custom_css)
// Micro-chrome (nav, footer, <style> font/scope, mockup builder hero): tile `html`.
//
// CTA builder: sentinel  #apri-builder  → sostituito dal mu-plugin con
//   admin.php?page=olobuilder-templates&template_id={clone_id} (per-visitatore).
//
// Output: try-home-tiles.json  (content del template wp_olo_templates)
// ─────────────────────────────────────────────────────────────────────────────

const { randomUUID } = require('crypto');
const fs = require('fs');
const path = require('path');
const uid = () => randomUUID();

// ── Palette ─────────────────────────────────────────────────────────────────────
const NAVY = '#0b0d12', NAVY_SOFT = '#14171f', NAVY_INK = '#1c2030';
const CREAM = '#f7f3ee', RED = '#e1474f', RED_DARK = '#c8323a';
const VIOLET = '#7c6cff', ORANGE = '#ff8a3d', GREEN = '#22c55e', WHITE = '#ffffff';
const DIM = 'rgba(255,255,255,.62)', FAINT = 'rgba(255,255,255,.42)', LINE = 'rgba(255,255,255,.10)';
const INK = '#0f172a', INK_SOFT = '#6b5b54';

const PILL = { tl: 999, tr: 999, br: 999, bl: 999 };
const R18 = { tl: 18, tr: 18, br: 18, bl: 18 };
const R16 = { tl: 16, tr: 16, br: 16, bl: 16 };
const BORDER_NONE = { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' };
const borderAll = (w, color, style = 'solid') => ({ top: w, right: w, bottom: w, left: w, linked: true, style, color });

// ── Helpers struttura ─────────────────────────────────────────────────────────
const section = (children, settings = {}, adv = {}) => ({
  id: uid(), type: 'section',
  settings: {
    style: 'default', width: 'default', padding: 'default', bg_scope: 'container',
    flex_direction: 'row', flex_justify: 'flex-start', flex_align: 'stretch',
    flex_wrap: 'nowrap', flex_gap: '0', layout_mode: 'flex', sticky_effect: 'none',
    scroll_snap: false, bg: { type: 'none' }, ...settings,
  }, style: [], advanced: adv, children,
});
const row = (children, settings = {}, adv = {}) => ({
  id: uid(), type: 'row',
  settings: {
    layout: '100', gap: 0, column_gap: 'default', vertical_align: 'top', stack_mobile: true,
    flex_direction: 'row', flex_justify: 'flex-start', flex_align: 'stretch',
    flex_wrap: 'nowrap', flex_gap: '0', ...settings,
  }, style: [], advanced: adv, children,
});
const column = (children, settings = {}, adv = {}) => ({
  id: uid(), type: 'column',
  settings: { width_default: '', width_medium: '1-1', ...settings },
  style: [], advanced: adv, children,
});
const tile = (type, settings = {}, adv = {}) => ({ id: uid(), type, settings, style: [], advanced: adv });
const htmlTile = (html, adv = {}) => tile('html', { html_content: html }, adv);

// ── Tile-factory ────────────────────────────────────────────────────────────────
function richText(html, opts = {}) {
  const { cls = '', align = 'left', color = '', fontSize = '', lineHeight = '', maxWidth = '0',
          textEffect = 'none', effectColor = '', effectPhrases = '', effectLoop = false,
          effectCursor = true, adv = {} } = opts;
  return tile('text-block', {
    preset: 'custom', bg: { type: 'none' }, content: html,
    text_color: color, font_size: fontSize, line_height: lineHeight, text_align: align, max_width: maxWidth,
    tile_padding: { top: 0, right: 0, bottom: 0, left: 0 }, tile_margin: { top: 0, right: 0, bottom: 0, left: 0 },
    text_effect: textEffect, text_effect_target: 'content', text_effect_loop: effectLoop,
    text_effect_cursor: effectCursor, text_effect_cursor_char: '|', text_effect_color: effectColor,
    text_effect_phrases: effectPhrases, text_effect_speed: '50', text_effect_delay: '0', text_effect_pause: '1500',
  }, cls ? { css_classes: cls, ...adv } : adv);
}

const eyebrow = (text, icon = 'sparkles', color = RED, bgc = 'rgba(225,71,79,.12)', bdc = 'rgba(225,71,79,.22)') => tile('badge', {
  bg: { type: 'none' }, preset: 'custom', variant: 'soft', text, icon, icon_position: 'before',
  badge_live: false, bg_color: bgc, text_color: color,
  font_size: '12', font_weight: '600', text_transform: 'uppercase', letter_spacing: '2',
  badge_radius: PILL, padding_y: 6, padding_x: 12, alignment: 'left', shadow: 'none', border: borderAll(1, bdc),
});

function button(text, url, opts = {}) {
  const { variant = 'primary', icon = '', iconPos = 'after', big = true, adv = {} } = opts;
  const base = {
    text, url, target: url.startsWith('http') ? '_blank' : '_self', preset: 'custom',
    alignment: 'left', full_width: false, bg: { type: 'none' }, icon, icon_position: iconPos, icon_spacing: '8',
    border_radius: PILL,
    tile_padding: big ? { top: 16, right: 28, bottom: 16, left: 28 } : { top: 12, right: 20, bottom: 12, left: 20 },
    font_size: big ? '15' : '14', font_weight: '600', letter_spacing: '0', text_transform: 'none',
    hover_effect: 'lift', border: BORDER_NONE,
  };
  if (variant === 'primary') Object.assign(base, {
    bg_color: RED, text_color: WHITE, hover_bg_color: RED_DARK, hover_text_color: WHITE,
    shadow: 'custom', shadow_h: 0, shadow_v: 8, shadow_blur: 24, shadow_spread: 0, shadow_color: 'rgba(225,71,79,.35)', shadow_inset: false,
  });
  else if (variant === 'ghost') Object.assign(base, {
    bg_color: 'rgba(255,255,255,.08)', text_color: WHITE, hover_bg_color: 'rgba(255,255,255,.16)', hover_text_color: WHITE,
    shadow: 'none', border: borderAll(1, 'rgba(255,255,255,.18)'),
  });
  return tile('button', base, adv);
}

// ── 0) STYLE/FONT (page-scoped) ──────────────────────────────────────────────────
function buildStyleTile() {
  const css = `
@import 'https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Work+Sans:wght@400;500;600;700;800&display=swap';
.olo-template{ color:rgba(255,255,255,.86); font-family:'Work Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; }

/* titoli display serif + emphasis rosso-corsivo (text-block .thd) */
.olo-template .thd h1, .olo-template .thd h2, .olo-template .thd h3{ font-family:'Instrument Serif','Playfair Display',Georgia,serif !important; font-weight:400 !important; letter-spacing:-.02em; margin:0; color:#fff !important; text-wrap:balance; }
.thd em{ font-style:italic; color:${RED} !important; }
.thd1 h1{ font-size:clamp(42px,5.2vw,66px) !important; line-height:1.0; }
.thd2 h2{ font-size:clamp(36px,4.6vw,52px) !important; line-height:1.02; }
.thd-cta h2{ font-size:clamp(44px,6.2vw,74px) !important; line-height:.97; }
.olo-template .thd-cream h1, .olo-template .thd-cream h2, .olo-template .thd-cream h3{ color:${INK} !important; }
.thd-cream em{ color:${RED_DARK}; }
.tlead strong{ color:#fff; font-weight:600; }
.tlead-cream strong{ color:${INK}; font-weight:600; }
.tryh-display .olo-cnt-number{ font-family:'Instrument Serif','Playfair Display',Georgia,serif !important; font-weight:400 !important; }

/* gruppi inline — la colonna olo è già flex-direction:column, serve forzare row */
.olo-inline-ctas{ display:flex !important; flex-direction:row !important; flex-wrap:wrap; gap:12px; align-items:center; }
.olo-inline-ctas.center{ justify-content:center; }

/* Card "Text FX dal vivo" (chrome su colonna via css_classes) */
.tryh-fxcard{ background:${NAVY_SOFT}; border:1px solid ${LINE}; border-radius:18px; padding:40px 24px; display:flex; flex-direction:column; align-items:center; gap:14px; text-align:center; }
.tryh-fxcard h3{ font-family:'Instrument Serif','Playfair Display',Georgia,serif !important; font-style:italic; color:${RED}; font-size:clamp(26px,3.4vw,38px); margin:0; }
.tryh-fxcard p{ margin:0; }

/* Decori step (numero) + limiti (pill) via css_classes + ::after */
.tstep{ position:relative; } .tlim{ position:relative; }
.tstep::after{ position:absolute; top:18px; right:22px; font-family:'Instrument Serif',serif; font-style:italic; font-size:40px; line-height:1; color:rgba(255,255,255,.10); pointer-events:none; }
.tstep-1::after{ content:"01"; } .tstep-2::after{ content:"02"; } .tstep-3::after{ content:"03"; } .tstep-4::after{ content:"04"; }
.tlim::after{ position:absolute; top:22px; right:18px; font:700 11px/1 'Work Sans',sans-serif; letter-spacing:.03em; padding:5px 9px; border-radius:99px; background:rgba(225,71,79,.08); color:${RED_DARK}; pointer-events:none; }
.tlim-1::after{ content:"12h"; } .tlim-2::after{ content:"Privata"; } .tlim-3::after{ content:"28 / 135"; } .tlim-4::after{ content:"Demo"; }

/* asciiviz CTA: container trasparente, niente box/bordo */
.tryh-ascii, .tryh-ascii *{ background:transparent !important; box-shadow:none !important; border:0 !important; outline:0 !important; }
.tryh-trust2{ font-size:13px; color:${FAINT}; line-height:1.9; }
.trustck{ color:${RED}; font-weight:700; }

/* NAV sticky */
.tryh-nav{ position:sticky; top:0; z-index:60; background:rgba(11,13,18,.72); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); border-bottom:1px solid ${LINE}; margin:0 calc(50% - 50vw); padding:0 max(28px,calc(50vw - 600px)); }
.tryh-nav-inner{ max-width:1200px; margin:0 auto; padding:13px 0; display:flex; align-items:center; gap:16px; }
.tryh-nav .logo{ height:30px; width:auto; }
.tryh-nav .demo-tag{ display:inline-flex; align-items:center; gap:7px; font:600 11px/1 'Work Sans',sans-serif; letter-spacing:.04em; color:${DIM}; padding:5px 11px; border-radius:99px; border:1px solid ${LINE}; background:rgba(255,255,255,.03); }
.tryh-nav .dot{ width:7px; height:7px; border-radius:50%; background:${GREEN}; position:relative; }
.tryh-nav .dot::after{ content:""; position:absolute; inset:-4px; border-radius:50%; border:1px solid ${GREEN}; opacity:.6; animation:tryhPulse 1.8s ease-out infinite; }
@keyframes tryhPulse{ 0%{transform:scale(.7);opacity:.7} 100%{transform:scale(1.9);opacity:0} }
.tryh-nav .spc{ flex:1; }
.tryh-nav a.lnk{ font-size:13px; color:${DIM}; text-decoration:none; font-weight:500; }
.tryh-nav a.lnk:hover{ color:#fff; }
.tryh-nav a.cta{ display:inline-flex; align-items:center; gap:7px; background:${RED}; color:#fff; font:600 13px/1 'Work Sans',sans-serif; padding:10px 18px; border-radius:99px; text-decoration:none; box-shadow:0 8px 24px rgba(225,71,79,.32); transition:transform .15s; }
.tryh-nav a.cta:hover{ transform:translateY(-1px); }
@media(max-width:680px){ .tryh-nav a.lnk{ display:none; } }

/* FOOTER */
.tryh-footer{ border-top:1px solid ${LINE}; margin:0 calc(50% - 50vw); padding:30px max(28px,calc(50vw - 600px)); display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; font-size:12.5px; color:${FAINT}; }
.tryh-footer a{ color:${FAINT}; text-decoration:none; } .tryh-footer a:hover{ color:#fff; }
.tryh-footer .links{ display:flex; gap:18px; }

/* HERO scrim leggibilità sopra il goo */
.tryh-hero-text{ position:relative; z-index:3; }
.tryh-hero-text::before{ content:""; position:absolute; inset:-40px -25% -40px -50%; z-index:-1; background:linear-gradient(100deg,rgba(11,13,18,.96) 0%,rgba(11,13,18,.84) 36%,rgba(11,13,18,.4) 56%,transparent 74%); pointer-events:none; }
.tryh-hero-text h1{ text-shadow:0 2px 24px rgba(11,13,18,.85),0 1px 3px rgba(11,13,18,.6); }

/* WOW showcase cards */
.wtitle h1, .wtitle h2, .wtitle h3{ font-family:'Instrument Serif','Playfair Display',Georgia,serif !important; font-weight:400 !important; font-size:27px !important; line-height:1.04; letter-spacing:-.01em; color:#fff !important; margin:0; }
.wtitle.neon h3{ color:#d9d2ff !important; text-shadow:0 0 8px #7c6cff,0 0 22px rgba(124,108,255,.7); letter-spacing:.02em; }
.wtitle.mono h3{ font-family:ui-monospace,Menlo,monospace !important; font-size:15px !important; color:#66e08a !important; text-shadow:0 0 6px rgba(34,197,94,.6); }
.wtitle.dark h3{ color:#1a1205 !important; }
.wdesc, .wdesc p{ font-size:13px; line-height:1.5; margin:0; }
.wdesc code{ font:600 11.5px/1 ui-monospace,Menlo,monospace; background:rgba(255,255,255,.08); padding:2px 5px; border-radius:4px; color:#fff; }
@keyframes wneon{ 0%,100%{opacity:.45} 50%{opacity:1} }
@keyframes worbA{ 0%,100%{transform:translate(0,0)} 50%{transform:translate(40px,30px)} }
@keyframes worbB{ 0%,100%{transform:translate(0,0)} 50%{transform:translate(-30px,-24px)} }
@media (prefers-reduced-motion: reduce){ .wtitle.neon h3, [class*=wcard]::after{ animation:none !important; } }

/* HERO h1 (split per lo scramble nativo) */
.tryh-hx, .tryh-hx *{ font-family:'Instrument Serif','Playfair Display',Georgia,serif !important; font-weight:400; font-size:clamp(42px,5.2vw,66px); line-height:1.0; letter-spacing:-.02em; color:#fff; margin:0; }
.tryh-hx h1{ display:inline; }
.tryh-h1l1 .olo-text-block{ padding:0 !important; }
.tryh-scr, .tryh-scr *{ font-style:italic !important; color:${RED} !important; }

/* HERO right visual (countdown pill + mockup + drag chip) */
.tryh-visual{ position:relative; }
.tryh-cd{ position:absolute; z-index:7; right:0; top:-26px; display:flex; align-items:center; gap:9px; padding:9px 16px; border-radius:99px; background:rgba(11,13,18,.85); border:1px solid ${LINE}; box-shadow:0 12px 30px rgba(0,0,0,.4); }
.tryh-cd .cdl{ display:inline-flex; align-items:center; gap:7px; font:600 13px/1 'Work Sans'; color:#fff; white-space:nowrap; }
.tryh-cd .cdl svg{ width:15px; height:15px; color:${RED}; }
.tryh-cd .olo-countdown{ font-variant-numeric:tabular-nums; }

/* BUILDER MOCKUP dettagliato */
.tryh-mockwrap{ perspective:2400px; width:840px; }
.tryh-mock{ width:840px; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 60px 120px -30px rgba(0,0,0,.7),0 30px 60px -40px rgba(225,71,79,.45); transform:rotateY(-13deg) rotateX(7deg) rotateZ(.5deg); transform-origin:left center; font-family:'Work Sans',sans-serif; }
.tryh-mock .bm-bar{ display:flex; align-items:center; gap:7px; padding:11px 14px; background:#fafbfc; border-bottom:1px solid #e9ecef; }
.tryh-mock .bm-bar .d{ width:10px; height:10px; border-radius:50%; } .bm-bar .d.r{background:#ff5f57}.bm-bar .d.y{background:#febc2e}.bm-bar .d.g{background:#28c840}
.tryh-mock .bm-url{ flex:1; margin-left:10px; background:#fff; border:1px solid #e9ecef; border-radius:6px; padding:5px 10px; font:11px/1 ui-monospace,monospace; color:#94a3b8; display:flex; align-items:center; gap:7px; } .tryh-mock .bm-url::before{ content:""; width:7px; height:7px; border-radius:50%; background:#22c55e; }
.tryh-mock .bm-body{ display:grid; grid-template-columns:300px 1fr 268px; height:430px; }
.tryh-mock .bm-side{ display:grid; grid-template-columns:62px 1fr; border-right:1px solid #e9ecef; background:#fff; }
.tryh-mock .bm-rail{ background:#f8f9fa; border-right:1px solid #eef0f3; padding:6px 0; }
.tryh-mock .bm-cat{ height:52px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:3px; position:relative; color:#64748b; }
.tryh-mock .bm-cat.on{ background:#fff; color:#1e293b; } .tryh-mock .bm-cat.on::before{ content:""; position:absolute; left:0; top:8px; bottom:8px; width:2px; background:${RED}; border-radius:0 2px 2px 0; }
.tryh-mock .bm-cat .ci{ width:24px; height:24px; border-radius:6px; display:grid; place-items:center; font-size:13px; font-weight:600; } .tryh-mock .bm-cat.on .ci{ background:rgba(225,71,79,.08); }
.tryh-mock .bm-cat .cl{ font-size:9px; font-weight:500; } .tryh-mock .bm-cat .cn{ position:absolute; top:5px; right:7px; font:700 8px/1 sans-serif; padding:1px 4px; border-radius:99px; background:#e9ecef; color:#64748b; } .tryh-mock .bm-cat.on .cn{ background:rgba(225,71,79,.15); color:#b8323a; }
.tryh-mock .bm-cards{ padding:13px; display:flex; flex-direction:column; gap:10px; }
.tryh-mock .bm-ch{ display:flex; align-items:center; gap:6px; } .tryh-mock .bm-ch .dt{ width:8px; height:8px; border-radius:50%; background:#ef4444; } .tryh-mock .bm-ch b{ font-size:13px; font-weight:700; color:#1e293b; } .tryh-mock .bm-ch .n{ margin-left:auto; font:600 10px/1 sans-serif; padding:1px 6px; border-radius:99px; background:#f1f5f9; color:#64748b; }
.tryh-mock .bm-search{ display:flex; align-items:center; gap:6px; padding:6px 9px; background:#f8f9fa; border:1px solid #eef0f3; border-radius:8px; font-size:11px; color:#94a3b8; }
.tryh-mock .bm-grid{ display:grid; grid-template-columns:1fr 1fr; gap:7px; }
.tryh-mock .bm-tc{ padding:10px; border:1px solid #e9ecef; border-radius:8px; background:#fff; display:flex; flex-direction:column; gap:6px; } .tryh-mock .bm-tc.sel{ border-color:${RED}; box-shadow:0 4px 12px rgba(225,71,79,.12); }
.tryh-mock .bm-tc .ti{ width:30px; height:30px; border-radius:6px; background:#f1f5f9; display:grid; place-items:center; font-size:13px; color:#475569; } .tryh-mock .bm-tc.sel .ti{ background:#fff; color:${RED}; }
.tryh-mock .bm-tc .tl{ font-size:11px; font-weight:500; color:#1e293b; }
.tryh-mock .bm-canvas{ background:#f3f4f6; padding:16px; }
.tryh-mock .bm-cv{ background:#fff; border-radius:8px; height:100%; border:1px solid #e9ecef; overflow:hidden; }
.tryh-mock .bm-hero{ position:relative; height:150px; background:linear-gradient(135deg,${RED},#7a1d23); color:#fff; padding:18px; }
.tryh-mock .bm-hero .eb{ font:600 9px/1 sans-serif; opacity:.7; text-transform:uppercase; letter-spacing:.1em; margin-bottom:8px; } .tryh-mock .bm-hero h4{ font:700 20px/1.15 'Work Sans'; margin:0 0 5px; max-width:62%; } .tryh-mock .bm-hero p{ font:400 11px/1.4 'Work Sans'; opacity:.85; max-width:52%; margin:0; }
.tryh-mock .bm-hero .sel{ position:absolute; inset:6px; border:1.5px dashed rgba(255,255,255,.6); border-radius:6px; } .tryh-mock .bm-hero .tag{ position:absolute; top:0; left:14px; background:${RED}; color:#fff; font:700 9px/1 sans-serif; padding:3px 7px; border-radius:0 0 4px 4px; }
.tryh-mock .bm-row3{ padding:16px; display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; }
.tryh-mock .bm-row3 span{ border:1px dashed #d1d5db; border-radius:8px; min-height:78px; padding:12px; display:flex; flex-direction:column; gap:6px; } .tryh-mock .bm-row3 span::before{ content:""; height:8px; width:55%; background:#f1f5f9; border-radius:3px; } .tryh-mock .bm-row3 span::after{ content:""; height:5px; background:#f1f5f9; border-radius:2px; }
.tryh-mock .bm-insp{ border-left:1px solid #e9ecef; background:#fff; padding:14px; display:flex; flex-direction:column; gap:12px; }
.tryh-mock .bm-bc{ font-size:10px; color:#64748b; display:flex; gap:5px; align-items:center; } .tryh-mock .bm-bc .bd{ background:#faf5ff; color:#7e22ce; padding:2px 6px; border-radius:4px; font:700 9px/1 sans-serif; }
.tryh-mock .bm-it{ font:700 14px/1 'Work Sans'; color:#1e293b; }
.tryh-mock .bm-tabs{ display:flex; gap:3px; padding:2px; border:1px solid #e9ecef; border-radius:8px; } .tryh-mock .bm-tabs span{ flex:1; text-align:center; font:600 11px/1 sans-serif; padding:6px 0; border-radius:6px; color:#64748b; } .tryh-mock .bm-tabs span.on{ background:${RED}; color:#fff; }
.tryh-mock .bm-field{ height:30px; border:1px solid #e9ecef; border-radius:6px; }
.tryh-mock .bm-dec{ background:#f8f9fa; border-radius:8px; padding:11px; display:flex; flex-direction:column; gap:8px; } .tryh-mock .bm-dec .dh{ font:700 9px/1 sans-serif; color:#64748b; text-transform:uppercase; letter-spacing:.06em; }
.tryh-mock .bm-sw{ display:flex; gap:4px; } .tryh-mock .bm-sw i{ width:18px; height:18px; border-radius:5px; } .tryh-mock .bm-sw i:nth-child(1){background:#ef4444;box-shadow:0 0 0 2px ${RED}}.tryh-mock .bm-sw i:nth-child(2){background:#1f2937}.tryh-mock .bm-sw i:nth-child(3){background:#fff;border:1px solid #e9ecef}.tryh-mock .bm-sw i:nth-child(4){background:#f59e0b}.tryh-mock .bm-sw i:nth-child(5){background:#22c55e}.tryh-mock .bm-sw i:nth-child(6){background:#0ea5e9}
.tryh-mock .bm-sl{ display:flex; align-items:center; gap:8px; } .tryh-mock .bm-sl .tr{ flex:1; height:5px; background:#e9ecef; border-radius:99px; position:relative; } .tryh-mock .bm-sl .tr b{ position:absolute; left:0; top:0; bottom:0; width:70%; background:linear-gradient(90deg,${RED}33,${RED}); border-radius:99px; } .tryh-mock .bm-sl .vv{ font:600 10px/1 sans-serif; color:#64748b; }
.tryh-dragchip{ position:absolute; z-index:6; left:26%; top:36%; width:96px; padding:11px; background:#fff; border-radius:11px; box-shadow:0 18px 40px rgba(0,0,0,.4),0 0 0 1px rgba(225,71,79,.4); transform:rotate(-4deg); animation:tryhFloat 4.5s ease-in-out infinite; }
.tryh-dragchip .ic{ width:30px; height:30px; border-radius:7px; background:rgba(225,71,79,.1); color:${RED}; display:grid; place-items:center; margin-bottom:6px; } .tryh-dragchip .lbl{ font:600 11px/1 'Work Sans'; color:#1e293b; } .tryh-dragchip .cur{ position:absolute; right:-12px; bottom:-15px; color:#0b0d12; filter:drop-shadow(0 2px 4px rgba(0,0,0,.3)); }
@keyframes tryhFloat{ 0%,100%{transform:rotate(-4deg) translateY(0)} 50%{transform:rotate(-4deg) translateY(-12px)} }
@media(max-width:940px){ .tryh-cd{ position:static; margin-bottom:14px; } }
`.trim();
  // Ritorna la STRINGA CSS globale: va messa nel custom_css di una section CON contenuto
  // (la nav) — una section vuota non viene renderizzata e il suo custom_css non emesso.
  return css;
}
const GLOBAL_CSS = buildStyleTile();

// ── 1) NAV (tile nativi) ─────────────────────────────────────────────────────────
function buildNav() {
  const logo = tile('image', {
    preset: 'custom', image_url: 'https://try.olotheme.com/wp-content/plugins/olobuild/assets/img/olobuild-1000-orizz-w.png',
    alt_text: 'OLObuild', image_width: '86px', height: '30px', max_width: '86px', aspect_ratio: 'auto',
    object_fit: 'contain', object_position: 'left center', image_alignment: 'left',
    border_radius: { tl: 0, tr: 0, br: 0, bl: 0 }, shadow: 'none', border: BORDER_NONE,
  });
  const liveBadge = tile('badge', {
    bg: { type: 'none' }, preset: 'custom', variant: 'soft', text: 'Sandbox dal vivo', icon: '',
    badge_live: true, badge_live_color: 'success', bg_color: 'rgba(255,255,255,.03)', text_color: DIM,
    font_size: '11', font_weight: '600', badge_radius: PILL, padding_y: 5, padding_x: 11, alignment: 'left',
    shadow: 'none', border: borderAll(1, LINE),
  });
  const links = richText('<a href="#effetti">Effetti wow</a> <a href="#come-funziona">Come funziona</a> <a href="https://olotheme.com/prodotti/olobuild/" target="_blank" rel="noopener">Installa</a>',
    { cls: 'tryh-navlinks', color: DIM, fontSize: '13' });
  const cta = button('Apri il builder', '#apri-builder', { variant: 'primary', icon: 'arrow-right', big: false });
  const navCol = column([logo, liveBadge, links, cta], { width_medium: '1-1',
    custom_css: "selector{display:flex;flex-direction:row;align-items:center;gap:14px;padding:13px 0;} selector > *:nth-child(1){flex:none;} selector > *:nth-child(1) img{width:auto !important;height:30px !important;max-width:none !important;object-fit:contain;} selector > *:nth-child(3){margin-left:auto;} selector .tryh-navlinks{display:flex;gap:18px;margin:0;} selector .tryh-navlinks a{font-size:13px;color:" + DIM + ";text-decoration:none;font-weight:500;} selector .tryh-navlinks a:hover{color:#fff;} @media(max-width:680px){ selector .tryh-navlinks{display:none;} }" });
  return section([row([navCol])], { padding: 'remove-vertical', bg_scope: 'section',
    custom_css: GLOBAL_CSS + "\nselector{position:sticky;top:0;z-index:60;background:rgba(11,13,18,.72);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border-bottom:1px solid " + LINE + ";}" });
}

// ── 2) HERO ───────────────────────────────────────────────────────────────────
function buildHero() {
  const goo = tile('goo', {
    scope: 'section', mode: 'goo', color_1: RED, color_2: VIOLET, color_3: ORANGE, color_4: '', color_5: '',
    blob_count: 5, blob_size_min: 180, blob_size_max: 400, drift_speed: 0.45,
    goo_strength: 19, follow_cursor: true, cursor_blob_size: 280, layer_opacity: 62,
    base_color: NAVY, content: '', min_height: 0, padding_y: 0, shadow: 'none', border: BORDER_NONE,
  });

  const chip = tile('badge', {
    bg: { type: 'none' }, preset: 'custom', variant: 'soft',
    text: 'Sandbox demo · 12h gratis · nessun account', icon: '',
    badge_live: true, badge_live_color: 'primary',
    bg_color: 'rgba(255,255,255,.06)', text_color: 'rgba(255,255,255,.85)',
    font_size: '12', font_weight: '600', badge_radius: PILL, padding_y: 6, padding_x: 13,
    alignment: 'left', shadow: 'none', border: borderAll(1, 'rgba(255,255,255,.12)'),
  });

  // h1 con parola scramble NATIVA (split: statico + scramble + resto)
  const h1a = richText('<h1>Prova OLObuild</h1>', { cls: 'tryh-hx', align: 'left' });
  const h1scr = richText('ORA.', { cls: 'tryh-hx tryh-scr', align: 'left', textEffect: 'scramble', effectLoop: true, effectColor: RED });
  const h1l1 = column([h1a, h1scr], { width_medium: '1-1', custom_css: 'selector{display:flex;flex-wrap:wrap;align-items:baseline;gap:0 .26em;}' }, { css_classes: 'tryh-h1l1' });
  const h1b = richText('Costruisci come<br>nel 2026.', { cls: 'tryh-hx', align: 'left' });

  const lead = richText(
    '<p>Un <strong>canvas vuoto</strong> e una copia personale del builder. Trascini i tile, attivi <strong>effetti wow</strong> con uno slider, salvi e guardi il risultato dal vivo — tutto nel browser.</p>',
    { cls: 'tlead', color: 'rgba(255,255,255,.72)', fontSize: '19', lineHeight: '1.55', maxWidth: '560' }
  );
  const ctaRow = column([
    button('Apri il builder · inizia ora', '#apri-builder', { variant: 'primary', icon: 'arrow-right' }),
    button('Vedi gli effetti', '#effetti', { variant: 'ghost' }),
  ], { width_medium: '1-1', custom_css: 'selector{display:flex;flex-direction:row;flex-wrap:wrap;gap:12px;align-items:center;}' });
  const trust = richText('<span class="trustck">✓</span> Niente account &nbsp;&nbsp;&nbsp; <span class="trustck">✓</span> Niente email &nbsp;&nbsp;&nbsp; <span class="trustck">✓</span> Reset dopo 12h',
    { cls: 'tryh-trust2', color: FAINT, fontSize: '13' });
  const leftCol = column([chip, h1l1, h1b, lead, ctaRow, trust], { width_medium: '1-2' }, { css_classes: 'tryh-hero-text' });

  // ── colonna destra: countdown PILL (tile countdown) + mockup dettagliato + drag chip ──
  const cdLabel = tile('badge', {
    bg: { type: 'none' }, preset: 'custom', variant: 'soft', text: 'Reset tra', icon: 'clock', icon_position: 'before',
    badge_live: false, bg_color: 'transparent', text_color: '#fff',
    font_size: '13', font_weight: '600', badge_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
    padding_y: 0, padding_x: 0, alignment: 'left', shadow: 'none', border: BORDER_NONE,
  });
  const cd = tile('countdown', {
    bg: { type: 'none' }, preset: 'custom', countdown_style: 'custom', countdown_type: 'evergreen',
    evergreen_hours: '12', evergreen_minutes: '0', evergreen_loop: true, display_mode: 'inline',
    show_days: false, show_hours: true, show_minutes: true, show_seconds: true,
    label_hours: '', label_minutes: '', label_seconds: '', separator: ':', expire_action: 'none',
    text_color: RED, accent_color: RED, separator_color: RED,
    number_font_size: '15', number_font_weight: '700', label_font_size: '8', label_font_weight: '500',
    separator_font_size: '14', item_min_width: '16',
    tile_padding: { top: 0, right: 0, bottom: 0, left: 0 }, item_bg_color: '', item_radius: '0', item_padding: '0',
    bg_color: 'transparent', border_radius: '0', shadow: 'none', border: BORDER_NONE,
  });
  const cdPill = column([cdLabel, cd], { width_medium: '1-1',
    custom_css: "selector{position:absolute;z-index:7;right:0;top:-26px;width:auto;display:flex;flex-direction:row;align-items:center;gap:8px;padding:9px 16px;border-radius:99px;background:rgba(11,13,18,.85);border:1px solid rgba(255,255,255,.10);box-shadow:0 12px 30px rgba(0,0,0,.4);white-space:nowrap;} selector .olo-countdown{font-variant-numeric:tabular-nums;}" });

  // Mockup builder = TILE NATIVO "buildermock" (mockup dettagliato + drag chip animato, wow)
  const mockup = tile('buildermock', {
    accent: RED, url_text: 'olobuild.it/editor',
    canvas_title: 'Benvenuto al Resort delle Ville',
    canvas_sub: 'Una struttura immersa nel verde, a 10 minuti dal mare.',
    cat_active: 'Essenziale', selected_tile: 'Titolo', drag_label: 'Titolo',
    animate_drag: true, tilt: 13, width: 840, shadow: 'xl', border: BORDER_NONE,
  });

  const rightCol = column([cdPill, mockup], { width_medium: '1-2', custom_css: 'selector{position:relative;}' });
  const contentRow = row([leftCol, rightCol], { layout: '1-2,1-2', gap: 56, column_gap: 'default', vertical_align: 'middle', stack_mobile: true });
  const gooRow = row([column([goo])], { layout: '100' });

  return section([gooRow, contentRow], { padding: 'large', bg_scope: 'section', width: 'default', bg: { type: 'solid', color: NAVY }, custom_css: 'selector{overflow:clip;}' });
}

// ── 3) WOW SHOWCASE ─────────────────────────────────────────────────────────────
// Card = colonna (chrome via custom_css) con TAG + titolo serif + descrizione in basso,
// EFFETTO VERO che riempie la card, ALTEZZE UGUALI (row stretch + card height:100%).
const CARD_BASE = "selector{position:relative;height:100%;min-height:236px;border-radius:18px;overflow:hidden;display:flex;flex-direction:column;justify-content:flex-end;gap:8px;padding:22px;border:1px solid " + LINE + ";} selector > .olo-frontend-tile{position:relative;z-index:3;}";

function wowCard({ width = '1-4', cardCss = '', tag, tagColor = RED, tagBg = 'rgba(225,71,79,.14)', tagBd = 'rgba(225,71,79,.25)',
                   title, titleCls = '', desc, descColor = DIM, titleEffect = 'none', effectTile = null, adv = {} }) {
  const tagBadge = tile('badge', {
    bg: { type: 'none' }, preset: 'custom', variant: 'soft', text: tag, icon: '', badge_live: false,
    bg_color: tagBg, text_color: tagColor, font_size: '10.5', font_weight: '600', text_transform: 'uppercase', letter_spacing: '1',
    badge_radius: PILL, padding_y: 5, padding_x: 9, alignment: 'left', shadow: 'none', border: borderAll(1, tagBd),
  });
  const titleT = richText('<h3>' + title + '</h3>', { cls: ('wtitle ' + titleCls).trim(), textEffect: titleEffect, effectLoop: true, effectColor: RED });
  const descT = richText('<p>' + desc + '</p>', { cls: 'wdesc', color: descColor });
  const kids = [];
  if (effectTile) kids.push(effectTile);
  kids.push(tagBadge, titleT, descT);
  return column(kids, { width_medium: width, custom_css: CARD_BASE + ' ' + cardCss }, adv);
}

function gooCardTile() {
  return tile('goo', {
    scope: 'column', mode: 'goo', color_1: RED, color_2: VIOLET, color_3: ORANGE, color_4: '', color_5: '',
    blob_count: 4, blob_size_min: 120, blob_size_max: 240, drift_speed: 0.5, goo_strength: 18,
    follow_cursor: true, cursor_blob_size: 200, layer_opacity: 95, base_color: '#0e1017',
    content: '', min_height: 236, padding_y: 0, shadow: 'none', border: BORDER_NONE,
  });
}

function buildShowcase() {
  const head = column([
    eyebrow('Novità · effetti wow', 'sparkles'),
    richText('<h2>Gli effetti che <em>non</em> ti aspetti<br>da un page builder.</h2>', { cls: 'thd thd2', align: 'left' }),
    richText("<p>Niente plugin, niente codice. Sono controlli nell'inspector: scegli, regoli gli slider, salvi. Ogni card qui sotto è un effetto vero che puoi attivare nella sandbox.</p>",
      { cls: 'tlead', color: DIM, fontSize: '17', lineHeight: '1.6', maxWidth: '620' }),
  ], { width_medium: '1-1' });

  // Goo: tile goo VERO (scope colonna) + scrim per leggibilità
  const cardGoo = wowCard({
    width: '1-2', tag: 'Sfondo Goo', title: 'Metaball che inseguono il mouse',
    desc: 'Gocce di colore che si fondono e seguono il cursore. Muovi il puntatore qui sopra.',
    effectTile: gooCardTile(),
    cardCss: "selector{background:#0e1017;} selector::after{content:'';position:absolute;inset:0;z-index:1;background:linear-gradient(to top,rgba(10,11,16,.9),transparent 52%);pointer-events:none;}",
  });
  // Vetro liquido: orb sfocati dietro + pannello glass (backdrop-blur) col contenuto
  const cardGlass = column([
    richText('<p>Vetro liquido</p>', { cls: 'wglass-h' }),
    richText('<p>Backdrop-blur + saturazione, bordo luce.</p>', { cls: 'wglass-p' }),
    tile('badge', { bg: { type: 'none' }, preset: 'custom', variant: 'soft', text: 'Liquid glass', icon: '',
      bg_color: 'rgba(255,255,255,.14)', text_color: '#fff', font_size: '10.5', font_weight: '600', text_transform: 'uppercase',
      letter_spacing: '1', badge_radius: PILL, padding_y: 5, padding_x: 9, alignment: 'left', shadow: 'none', border: borderAll(1, 'rgba(255,255,255,.3)') }),
  ], { width_medium: '1-4', custom_css:
    "selector{position:relative;height:100%;min-height:236px;border-radius:18px;overflow:hidden;border:1px solid " + LINE + ";background:#1a1426;display:flex;flex-direction:column;justify-content:center;align-items:flex-start;gap:8px;padding:18px;} " +
    "selector::before{content:'';position:absolute;width:180px;height:180px;left:-30px;top:-20px;border-radius:50%;filter:blur(10px);background:radial-gradient(circle,#ff5436,transparent 70%);animation:worbA 9s ease-in-out infinite;} " +
    "selector::after{content:'';position:absolute;width:200px;height:200px;right:-40px;bottom:-40px;border-radius:50%;filter:blur(10px);background:radial-gradient(circle,#7c6cff,transparent 70%);animation:worbB 11s ease-in-out infinite;} " +
    "selector > *{position:relative;z-index:3;background:rgba(255,255,255,.08);-webkit-backdrop-filter:blur(12px) saturate(160%);backdrop-filter:blur(12px) saturate(160%);border:1px solid rgba(255,255,255,.22);border-radius:10px;padding:6px 12px;box-shadow:inset 0 1px 0 rgba(255,255,255,.35);} " +
    "selector .wglass-h p{font-family:'Instrument Serif',Georgia,serif !important;font-size:24px !important;color:#fff !important;margin:0;} selector .wglass-p p{font-size:12.5px;color:rgba(255,255,255,.7);margin:0;}" });
  // Neon: bordo neon-pulse (CSS) + glow titolo
  const cardNeon = wowCard({
    tag: 'Neon · bordo pulsante', tagColor: '#b9b0ff', tagBg: 'rgba(124,108,255,.16)', tagBd: 'rgba(124,108,255,.4)',
    title: 'NEON CYBER', titleCls: 'neon', desc: 'Bordo <code>neon-pulse</code> + glow sul titolo.', descColor: DIM,
    cardCss: "selector{background:#0a0a14;border-color:rgba(124,108,255,.35);} selector::after{content:'';position:absolute;inset:0;z-index:1;border-radius:18px;border:1.5px solid #7c6cff;box-shadow:0 0 12px #7c6cff,inset 0 0 12px rgba(124,108,255,.5);pointer-events:none;animation:wneon 2.4s ease-in-out infinite;}",
  });
  // Glitch: text_effect glitch sul titolo
  const cardGlitch = wowCard({
    tag: 'Text FX · Glitch RGB', title: 'ERRORE?', titleEffect: 'glitch',
    desc: 'Scostamento canali rosso/ciano animato.', cardCss: 'selector{background:#120d12;}',
  });
  // Retro terminal: scanline CRT + titolo mono + cursore
  const cardTerm = wowCard({
    tag: 'Retro terminal', tagColor: '#66e08a', tagBg: 'rgba(34,197,94,.14)', tagBd: 'rgba(34,197,94,.35)',
    title: '> olobuild --demo', titleCls: 'mono', titleEffect: 'typewriter', desc: 'Scanlines CRT + prompt + cursore.', descColor: 'rgba(102,224,138,.65)',
    cardCss: "selector{background:#04140a;border-color:rgba(34,197,94,.3);} selector::after{content:'';position:absolute;inset:0;z-index:1;background:repeating-linear-gradient(to bottom,rgba(34,197,94,.08) 0 1px,transparent 1px 3px);pointer-events:none;}",
  });
  // Tilt 3D: mouse_tilt
  const cardTilt = wowCard({
    tag: 'Mouse · tilt 3D', tagColor: '#fff', tagBg: 'rgba(255,255,255,.14)', tagBd: 'rgba(255,255,255,.3)',
    title: 'Inclina al passaggio', desc: 'Passa il mouse: la card segue in prospettiva.',
    cardCss: 'selector{background:linear-gradient(135deg,#1c1f2b,#14161e);transition:transform .1s ease-out;}',
    adv: { mouse_tilt: true, mouse_tilt_intensity: 14 },
  });
  // Sticker: giallo, rotazione, bordo bianco
  const cardSticker = wowCard({
    tag: 'Sticker', tagColor: '#ffd23f', tagBg: '#1a1205', tagBd: '#1a1205',
    title: 'Adesivo', titleCls: 'dark', desc: 'Rotazione + bordo bianco + ombra.', descColor: 'rgba(26,18,5,.72)',
    cardCss: 'selector{background:#ffd23f;border:3px solid #fff;box-shadow:0 14px 30px rgba(0,0,0,.35);}',
    adv: { transform_rotate: -3 },
  });

  // Text FX dal vivo (scramble + typewriter) — card combo full width
  const fxTag = eyebrow('Text FX dal vivo', 'type');
  const fxScramble = richText('Prova OLObuild ORA', { cls: 'tryh-fxbig', align: 'center', textEffect: 'scramble', effectLoop: true, effectColor: RED });
  const fxType = richText('<p>Trascini il tile <strong></strong> sul canvas.</p>', {
    cls: 'tlead', align: 'center', color: DIM, fontSize: '16',
    textEffect: 'typewriter-loop', effectLoop: true, effectColor: RED, effectPhrases: 'headline\ngallery\nform\nhero\naccordion',
  });
  const fxCard = column([fxTag, fxScramble, fxType], { width_medium: '1-1',
    custom_css: `selector{background:${NAVY_SOFT};border:1px solid ${LINE};border-radius:18px;padding:44px 24px;display:flex;flex-direction:column;align-items:center;gap:14px;text-align:center;} selector .tryh-fxbig, selector .tryh-fxbig *{font-family:'Instrument Serif',serif !important;font-style:italic !important;color:${RED} !important;font-size:clamp(30px,4.2vw,46px) !important;line-height:1.05;}` });

  const grid1 = row([cardGoo, cardGlass, cardNeon], { layout: '1-2,1-4,1-4', gap: 16, column_gap: 'default', vertical_align: 'stretch', stack_mobile: true });
  const grid2 = row([cardGlitch, cardTerm, cardTilt, cardSticker], { layout: '1-4,1-4,1-4,1-4', gap: 16, column_gap: 'default', vertical_align: 'stretch', stack_mobile: true });
  const grid3 = row([fxCard], { layout: '100', gap: 16 });

  return section([row([head]), grid1, grid2, grid3], { padding: 'large', width: 'default' }, { css_id: 'effetti' });
}

// ── 4) STEPS ─────────────────────────────────────────────────────────────────────
function stepCard(ic, i, t, p) {
  return column([
    tile('iconbox', {
      bg: { type: 'none' }, preset: 'custom', icon_emoji: ic, title: t, description: p,
      link_url: '', link_text: '', alignment: 'left', icon_size: '1.6', icon_position: 'top',
      icon_color: RED, icon_bg_color: 'rgba(225,71,79,.12)', icon_bg_shape: 'rounded',
      title_font_size: '23', title_font_weight: '500', title_color: WHITE, text_color: DIM,
      icon_gap: '16', title_gap: '10', desc_gap: '0',
      tile_padding: { top: 28, right: 24, bottom: 26, left: 24 },
      border_radius: R18, bg_type: 'color', bg_color: NAVY_SOFT, shadow: 'none', border: borderAll(1, LINE),
    }, { css_classes: `tryh-display tstep tstep-${i}` }),
  ], { width_medium: '1-4', custom_css: 'selector{display:flex;} selector > *{flex:1 1 auto;}' });
}
function buildSteps() {
  const head = column([
    eyebrow('In 12 ore', 'clock'),
    richText('<h2>Quattro mosse, <em>zero attriti.</em></h2>', { cls: 'thd thd2', align: 'left' }),
    richText('<p>Nessun setup, nessun tutorial obbligatorio. Apri il builder e sei già dentro la tua sandbox personale.</p>',
      { cls: 'tlead', color: DIM, fontSize: '17', lineHeight: '1.6', maxWidth: '620' }),
  ], { width_medium: '1-1' });
  const grid = row([
    stepCard('layout-grid', 1, 'Costruisci da zero', 'Parti da un canvas vuoto. Trascini i tile demo dalla sidebar — headline, immagini, gallery, accordion, form, hero.'),
    stepCard('square-pen', 2, 'Modifica al volo', 'Doppio click su un testo e lo editi inline con la toolbar floating. Niente switch tra edit e preview.'),
    stepCard('sliders-horizontal', 3, 'Attiva gli effetti', 'Inspector: colori, spaziature, animazioni, Text FX, effetti wow, mouse-tilt. Tutto con slider e toggle.'),
    stepCard('save', 4, 'Salva e visualizza', 'Salva e vedi il risultato sul frontend. Modifica e ripeti, senza limiti durante la sessione.'),
  ], { layout: '1-4,1-4,1-4,1-4', gap: 18, column_gap: 'default', vertical_align: 'top', stack_mobile: true });
  return section([row([head]), grid], { padding: 'large', width: 'default' }, { css_id: 'come-funziona' });
}

// ── 5) LIMITS (cream) ─────────────────────────────────────────────────────────────
function limitCard(ic, i, t, p) {
  return column([
    tile('iconbox', {
      bg: { type: 'none' }, preset: 'custom', icon_emoji: ic, title: t, description: p,
      link_url: '', link_text: '', alignment: 'left', icon_size: '1.5', icon_position: 'top',
      icon_color: RED_DARK, icon_bg_color: 'rgba(225,71,79,.08)', icon_bg_shape: 'rounded',
      title_font_size: '17', title_font_weight: '600', title_color: INK, text_color: INK_SOFT,
      icon_gap: '12', title_gap: '8', desc_gap: '0',
      tile_padding: { top: 24, right: 22, bottom: 24, left: 22 },
      border_radius: R16, bg_type: 'color', bg_color: WHITE, shadow: 'sm', border: borderAll(1, '#e9e3da'),
    }, { css_classes: `tlim tlim-${i}` }),
  ], { width_medium: '1-4', custom_css: 'selector{display:flex;} selector > *{flex:1 1 auto;}' });
}
function buildLimits() {
  const head = column([
    eyebrow('In trasparenza', 'lock', RED_DARK),
    richText('<h2>I limiti della <em>sandbox.</em></h2>', { cls: 'thd thd2 thd-cream', align: 'left' }),
    richText('<p>È un assaggio onesto, non la versione completa. Ecco esattamente cosa aspettarti.</p>',
      { cls: 'tlead tlead-cream', color: INK_SOFT, fontSize: '17', lineHeight: '1.6', maxWidth: '620' }),
  ], { width_medium: '1-1' });
  const grid = row([
    limitCard('clock', 1, 'Inattività', 'Dopo 12 ore senza modifiche il template viene cancellato. Torna prima e riparti da dove eri.'),
    limitCard('user', 2, 'Sandbox personale', 'Ognuno ha il suo template. Quello che modifichi non lo vede nessun altro. Niente registrazione.'),
    limitCard('layout-grid', 3, 'Tile della demo', 'Selezione rappresentativa. Nella versione completa hai 135 tile: oltre 100 gratis, alcune speciali per gli abbonati.'),
    limitCard('file-text', 4, 'Solo template demo', 'Non crei pagine nuove o header/footer: modifichi il template che ti diamo.'),
  ], { layout: '1-4,1-4,1-4,1-4', gap: 16, column_gap: 'default', vertical_align: 'top', stack_mobile: true });
  return section([row([head]), grid], { padding: 'large', width: 'default', bg_scope: 'section', bg: { type: 'solid', color: CREAM } });
}

// ── 6) CTA FINALE ─────────────────────────────────────────────────────────────────
function buildCta() {
  const ascii = tile('asciiviz', {
    show_player: false, react_to: 'simulated', cols: 48, rows: 4, ramp: ' ·:-=+*o%#@', char_top: '█',
    idle_amplitude: 0.2, react_speed: 1.6, show_progress: false, show_listeners: false,
    color: RED, bg_color: '', glow: 10, font_size: 12, line_height: 1.0, letter_spacing: 1.5,
    radius: 0, padding: 0, shadow: 'none', border: BORDER_NONE,
  }, { css_classes: 'tryh-ascii' });
  const head = column([
    ascii,
    eyebrow('Gratis per sempre', 'zap'),
    richText('<h2>Convinto? Installa<br>OLObuild <em>gratis.</em></h2>', { cls: 'thd thd2 thd-cta', align: 'center' }),
    richText('<p>La sandbox è un assaggio. Sul tuo WordPress hai <strong>tutto</strong>: oltre 100 tile gratis (135 in tutto, alcune speciali per gli abbonati), gli effetti wow, i Text FX, gli sfondi Goo/Aurora animati e il form builder multi-step.</p>',
      { cls: 'tlead', color: DIM, align: 'center', fontSize: '18', lineHeight: '1.55', maxWidth: '620' }),
  ], { width_medium: '1-1' });
  const ctas = column([
    button('Installa OLObuild', 'https://olotheme.com/prodotti/olobuild/', { variant: 'primary', icon: 'arrow-right' }),
    button('Continua nella sandbox', '#apri-builder', { variant: 'ghost' }),
  ], { width_medium: '1-1', custom_css: 'selector{display:flex;flex-direction:row;flex-wrap:wrap;gap:12px;align-items:center;justify-content:center;}' });

  const figs = [['135', 'tile totali'], ['100+', 'gratis'], ['35', 'animazioni'], ['10', 'Text FX']];
  const figCols = figs.map(([v, c]) => column([
    tile('counter', {
      preset: 'custom', bg: { type: 'none' }, number: v.replace(/[^0-9]/g, ''), label: c,
      prefix: '', suffix: v.replace(/[0-9]/g, ''), icon_emoji: '', icon_size: '0',
      number_font_size: '54', number_font_weight: '400', text_color: WHITE, label_color: DIM,
      label_font_size: '12', label_font_weight: '500', bg_type: 'color', bg_color: '',
      tile_padding: { top: 24, right: 12, bottom: 24, left: 12 }, border_radius: '0', shadow: 'none',
    }, { css_classes: 'tryh-display' }),
  ], { width_medium: '1-4' }));
  const figRow = row(figCols, { layout: '1-4,1-4,1-4,1-4', gap: 0, column_gap: 'default', vertical_align: 'middle', stack_mobile: true });

  return section([row([head]), row([ctas]), figRow], {
    padding: 'large', width: 'default', bg_scope: 'section',
    bg: { type: 'glow', glow_base: NAVY, glow_color: RED, glow_color2: VIOLET, glow_preset: 'top',
          glow_intensity: 40, glow_size: 80, glow_grain: true, glow_anim: 'vivo', glow_anim_speed: 5, glow_anim_intensity: 50 },
  });
}

// ── 7) FOOTER ─────────────────────────────────────────────────────────────────
function buildFooter() {
  const copy = richText('© 2026 OLObuild · La sandbox è il tuo browser · Reset automatico dopo 12h',
    { cls: 'tryh-foot', color: FAINT, fontSize: '12.5' });
  const links = richText('<a href="https://olotheme.com/prodotti/olobuild/" target="_blank" rel="noopener">olotheme.com</a> <a href="#effetti">Effetti</a>',
    { cls: 'tryh-footlinks', color: FAINT, fontSize: '12.5' });
  const footCol = column([copy, links], { width_medium: '1-1',
    custom_css: "selector{display:flex;flex-direction:row;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;padding:30px 0;} selector .tryh-footlinks{display:flex;gap:18px;margin:0;} selector .tryh-footlinks a{color:" + FAINT + ";text-decoration:none;} selector .tryh-footlinks a:hover{color:#fff;} selector .tryh-foot{margin:0;}" });
  return section([row([footCol])], { padding: 'remove-vertical', bg_scope: 'section',
    custom_css: "selector{border-top:1px solid " + LINE + ";}" });
}

// ── ASSEMBLE ────────────────────────────────────────────────────────────────────
const sections = [
  buildNav(), buildHero(), buildShowcase(), buildSteps(), buildLimits(), buildCta(), buildFooter(),
];
const outPath = path.join(__dirname, 'try-home-tiles.json');
fs.writeFileSync(outPath, JSON.stringify(sections), 'utf8');
console.log(`Generato: ${outPath}`);
console.log(`Sezioni: ${sections.length}  ·  Bytes: ${fs.statSync(outPath).size}`);
