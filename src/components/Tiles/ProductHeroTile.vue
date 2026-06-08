<template>
  <section class="olo-producthero oph" :style="rootStyle">
    <span v-if="s.glow_on" class="oph-glow" :style="glowStyle"></span>
    <span v-if="s.grid_on" class="oph-grid" :style="gridStyle"></span>
    <div class="oph-in" :style="inStyle">
      <span v-if="s.pill_text || s.pill_pre" class="oph-pill" :style="pillStyle"><b v-if="s.pill_pre" :style="{ color: accent, fontWeight: 600 }">{{ s.pill_pre }}</b><template v-if="s.pill_pre"> · </template>{{ s.pill_text }}</span>
      <h1 class="oph-h" :style="hStyle">{{ s.headline_text }}<template v-if="s.accent_text"><br><span class="oph-grad" :style="gradStyle">{{ s.accent_text }}</span></template></h1>
      <p v-if="s.subhead" class="oph-sub" :style="subStyle">{{ s.subhead }}</p>
      <div class="oph-cta" :style="{ display: 'flex', gap: '12px', justifyContent: 'center', flexWrap: 'wrap' }">
        <a v-if="s.cta1_text" class="oph-btn oph-btn--solid" :style="solidStyle" :href="s.cta1_url || '#'">{{ s.cta1_text }}</a>
        <a v-if="s.cta2_text" class="oph-btn oph-btn--ghost" :style="ghostBtnStyle" :href="s.cta2_url || '#'">{{ s.cta2_text }}
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
      </div>
    </div>
    <div class="oph-mockwrap" :style="mockWrapStyle">
      <div class="oph-frame" :style="frameStyle">
        <div class="oph-bar" :style="barStyle"><i :style="dotStyle"></i><i :style="dotStyle"></i><i :style="dotStyle"></i><span v-if="mode === 'dashboard' && s.mock_url" class="oph-url" :style="urlStyle">{{ s.mock_url }}</span></div>
        <div v-if="mode === 'media'" class="oph-media" :style="mediaStyle"><span class="oph-medialabel" :style="mediaLabelStyle">{{ mediaLabel }}</span></div>
        <div v-else class="oph-body" :style="bodyStyle">
          <div v-for="(k, i) in kpis" :key="'k' + i" class="oph-kpi" :style="kpiStyle">
            <div class="oph-k" :style="kStyle">{{ k.label }}</div>
            <div class="oph-v" :style="vStyle">{{ k.value }}</div>
            <div class="oph-t" :class="{ 'oph-dn': k.down }" :style="k.down ? tDnStyle : tStyle">{{ k.delta }}</div>
          </div>
          <div class="oph-chart" :style="chartStyle">
            <div class="oph-chhead" :style="chHeadStyle"><b :style="chTitleStyle">{{ s.chart_title }}</b><span :style="chMetaStyle">{{ s.chart_meta }}</span></div>
            <div class="oph-bars" :style="{ display: 'flex', alignItems: 'flex-end', gap: '7px', height: '120px' }">
              <div v-for="(b, i) in bars" :key="'b' + i" class="oph-col" :style="colStyle">
                <i class="oph-bar-i" :class="{ 'oph-b2': b.alt }" :style="barFillStyle(b)"></i>
                <span :style="colLabelStyle">{{ b.label }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue';
import { buildBgStyle } from '@/composables/useBackgroundStyle';
import { borderDefault, borderHoverDefault, borderEffectDefaults } from '@/config/elements/_shared.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  pill_pre: 'New',
  pill_text: 'Circuit 3.0 — now with live workflows',
  headline_text: 'Ship reliable software,',
  accent_text: 'without the busywork.',
  subhead: 'Circuit connects the tools your team already uses, automates the hand-offs, and gives everyone one honest view of every release.',
  cta1_text: 'Start free — no card', cta1_url: '#',
  cta2_text: 'See how it works', cta2_url: '#',
  glow_on: true, glow_color: '#6c8cff',
  grid_on: true, grid_color: 'rgba(255,255,255,0.04)', grid_size: 48,
  mock_mode: 'dashboard',
  mock_url: 'app.circuit.io / workspace / releases',
  mock_label: 'product — workflow board &amp; live status dashboard',
  kpis: [
    { label: 'Net revenue', value: '$4.82M', delta: '▲ 18.4% MoM', down: '' },
    { label: 'Active accounts', value: '12,408', delta: '▲ 6.1% MoM', down: '' },
    { label: 'Churn', value: '1.9%', delta: '▼ 0.3 pts', down: '1' },
  ],
  chart_title: 'Revenue by week',
  chart_meta: 'last 12 weeks · live',
  bars: [
    { h: 38, label: 'w1', alt: '' }, { h: 46, label: 'w2', alt: '' }, { h: 41, label: 'w3', alt: '' },
    { h: 54, label: 'w4', alt: '' }, { h: 60, label: 'w5', alt: '' }, { h: 52, label: 'w6', alt: '' },
    { h: 68, label: 'w7', alt: '' }, { h: 74, label: 'w8', alt: '' }, { h: 66, label: 'w9', alt: '1' },
    { h: 82, label: 'w10', alt: '' }, { h: 90, label: 'w11', alt: '' }, { h: 100, label: 'w12', alt: '1' },
  ],
  bg_color: '#0b0d18', panel_color: '#141a2e', panel2_color: '#1b2238', cell_color: '#11142270',
  accent: '#6c8cff', accent2: '#b08bff', accent_on: '#ffffff', down_color: '',
  text_color: '#ffffff', sub_color: '#8a90a8', pill_text_color: '#c9cde0',
  pill_bg: 'rgba(255,255,255,0.05)',
  line_color: 'rgba(255,255,255,0.09)',
  pill_mono: false, mono_meta: true,

  // Spaziatura + Raggio (additivi, no-op coi default).
  content_padding: { top: 0, right: 28, bottom: 0, left: 28 },
  frame_radius: { tl: 16, tr: 16, br: 0, bl: 0 },
  kpi_radius: { tl: 11, tr: 11, br: 11, bl: 11 },

  // KIT standard OLObuild — sfondo completo + ombra + bordo sul contenitore.
  // Default no-op: bg none / shadow none / border 0 → render invariato.
  bg: { type: 'none' },
  shadow: 'none',
  border: { ...borderDefault },
  border_hover: { ...borderHoverDefault },
  border_hover_duration: 300,
  ...borderEffectDefaults,
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const DISP = "var(--olo-font-family-heading, 'Space Grotesk',-apple-system,sans-serif)";
const SANS = "var(--olo-font-family, 'Work Sans',-apple-system,sans-serif)";
const MONO = "var(--olo-font-family-mono, ui-monospace,'SF Mono',Menlo,monospace)";

const accent = computed(() => s.value.accent || 'var(--olo-color-primary, #6c8cff)');
const acc2 = computed(() => s.value.accent2 || '#b08bff');
const downC = computed(() => s.value.down_color || acc2.value);
const accOn = computed(() => s.value.accent_on || '#ffffff');
const txt = computed(() => s.value.text_color || '#ffffff');
const sub = computed(() => s.value.sub_color || '#8a90a8');
const line = computed(() => s.value.line_color || 'rgba(255,255,255,0.09)');
const panel = computed(() => s.value.panel_color || '#141a2e');
const panel2 = computed(() => s.value.panel2_color || '#1b2238');
const cell = computed(() => s.value.cell_color || '#11142270');

const mode = computed(() => (s.value.mock_mode === 'media' ? 'media' : 'dashboard'));
const kpis = computed(() => (Array.isArray(s.value.kpis) ? s.value.kpis : []));
const bars = computed(() => (Array.isArray(s.value.bars) ? s.value.bars : []));
const mediaLabel = computed(() => String(s.value.mock_label || '').replace(/&amp;/g, '&'));

function colorToRgb(color, fb) {
  let c = String(color || '').trim();
  let m = c.match(/^#([0-9a-f]{3})$/i);
  if (m) { const h = m[1]; return parseInt(h[0] + h[0], 16) + ',' + parseInt(h[1] + h[1], 16) + ',' + parseInt(h[2] + h[2], 16); }
  m = c.match(/^#([0-9a-f]{6})$/i);
  if (m) { const h = m[1]; return parseInt(h.slice(0, 2), 16) + ',' + parseInt(h.slice(2, 4), 16) + ',' + parseInt(h.slice(4, 6), 16); }
  m = c.match(/rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/i);
  if (m) return m[1] + ',' + m[2] + ',' + m[3];
  return fb;
}
const glowRgb = computed(() => colorToRgb(s.value.glow_color || '#6c8cff', '108,140,255'));
const acc2Rgb = computed(() => colorToRgb(acc2.value, '176,139,255'));

// ── KIT standard OLObuild — sfondo completo + ombra + bordo (parità col PHP) ──
// Sfondo completo: override del bg di sezione SOLO se valorizzato (default none → invariato).
const kitBgStyle = computed(() => {
  const bg = s.value.bg;
  if (!bg || !bg.type || bg.type === 'none') return {};
  return buildBgStyle(bg);
});
// Ombra: preset/custom, mirror 1:1 di build_shadow_decl PHP. '' = nessuna → invariato.
const kitShadow = computed(() => {
  const p = s.value.shadow || 'none';
  if (p === 'none' || p === '') return '';
  if (p === 'custom') {
    const h = parseInt(s.value.shadow_h ?? 0, 10) || 0;
    const v = parseInt(s.value.shadow_v ?? 4, 10) || 0;
    const blur = Math.max(0, parseInt(s.value.shadow_blur ?? 10, 10) || 0);
    const spread = parseInt(s.value.shadow_spread ?? 0, 10) || 0;
    const color = s.value.shadow_color || 'rgba(0,0,0,0.15)';
    const inset = s.value.shadow_inset ? 'inset ' : '';
    return `${inset}${h}px ${v}px ${blur}px ${spread}px ${color}`;
  }
  const map = {
    sm: '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
    md: '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
    lg: '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
    xl: '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
  };
  return map[p] || '';
});
// Bordo base: mirror 1:1 di parse_border + build_border_css PHP. Vuoto se inattivo.
const kitBorderStyle = computed(() => {
  const b = s.value.border;
  if (!b || typeof b !== 'object') return {};
  const color = String(b.color || '').trim();
  if (color === '') return {};
  const style = b.style || 'solid';
  const top = Math.max(0, parseInt(b.top ?? 0, 10) || 0);
  const right = Math.max(0, parseInt(b.right ?? 0, 10) || 0);
  const bottom = Math.max(0, parseInt(b.bottom ?? 0, 10) || 0);
  const left = Math.max(0, parseInt(b.left ?? 0, 10) || 0);
  if (!top && !right && !bottom && !left) return {};
  if (top === right && right === bottom && bottom === left) {
    return { border: `${top}px ${style} ${color}` };
  }
  const out = {};
  if (top) out.borderTop = `${top}px ${style} ${color}`;
  if (right) out.borderRight = `${right}px ${style} ${color}`;
  if (bottom) out.borderBottom = `${bottom}px ${style} ${color}`;
  if (left) out.borderLeft = `${left}px ${style} ${color}`;
  return out;
});

// ── Spaziatura + Raggio (additivi, parità col PHP, no-op coi default) ──
// Padding inner: mirror di Olo_Tile_Utils::spacing_css (oggetto {top,right,bottom,left}).
const inPad = computed(() => {
  const v = s.value.content_padding;
  if (v && typeof v === 'object') {
    const t = parseInt(v.top ?? 0, 10) || 0;
    const r = parseInt(v.right ?? 0, 10) || 0;
    const b = parseInt(v.bottom ?? 0, 10) || 0;
    const l = parseInt(v.left ?? 0, 10) || 0;
    return `${t}px ${r}px ${b}px ${l}px`;
  }
  const n = (v !== '' && v != null) ? (parseInt(v, 10) || 0) : 0;
  return `${n}px`;
});
// Raggio: mirror di build_border_radius_css (oggetto {tl,tr,br,bl}); '' (tutto 0) → '0'.
function radiusCss(v) {
  if (v && typeof v === 'object') {
    const tl = parseInt(v.tl ?? 0, 10) || 0;
    const tr = parseInt(v.tr ?? 0, 10) || 0;
    const br = parseInt(v.br ?? 0, 10) || 0;
    const bl = parseInt(v.bl ?? 0, 10) || 0;
    if (tl || tr || br || bl) return `${tl}px ${tr}px ${br}px ${bl}px`;
    return '0';
  }
  const n = parseInt(v, 10) || 0;
  return n > 0 ? `${n}px` : '0';
}
const frameRadius = computed(() => radiusCss(s.value.frame_radius));
const kpiRadius = computed(() => radiusCss(s.value.kpi_radius));

const rootStyle = computed(() => {
  const st = { position: 'relative', overflow: 'hidden', textAlign: 'center', padding: 'clamp(56px,8vw,104px) 0 0', background: s.value.bg_color || '#0b0d18', color: txt.value, fontFamily: SANS, '--oph-accent': accent.value };
  // KIT: sfondo completo (override), ombra, bordo — applicati DOPO il base, no-op coi default.
  Object.assign(st, kitBgStyle.value, kitBorderStyle.value);
  if (kitShadow.value) st.boxShadow = kitShadow.value;
  return st;
});
const glowStyle = computed(() => ({ position: 'absolute', top: '-200px', left: '50%', transform: 'translateX(-50%)', width: '760px', height: '560px', borderRadius: '50%', filter: 'blur(90px)', pointerEvents: 'none', background: `radial-gradient(circle, rgba(${glowRgb.value},.34) 0%, rgba(${glowRgb.value},0) 70%)` }));
const gridStyle = computed(() => {
  const g = s.value.grid_color || 'rgba(255,255,255,0.04)';
  const gs = Math.max(16, Math.min(120, Number(s.value.grid_size) || 48));
  return { position: 'absolute', inset: 0, pointerEvents: 'none', opacity: .5, backgroundImage: `linear-gradient(${g} 1px, transparent 1px), linear-gradient(90deg, ${g} 1px, transparent 1px)`, backgroundSize: `${gs}px ${gs}px`, WebkitMask: 'radial-gradient(70% 60% at 50% 30%, #000, transparent 75%)', mask: 'radial-gradient(70% 60% at 50% 30%, #000, transparent 75%)' };
});
const inStyle = computed(() => ({ position: 'relative', zIndex: 2, maxWidth: '820px', margin: '0 auto', padding: inPad.value }));
const pillStyle = computed(() => {
  const st = { display: 'inline-flex', alignItems: 'center', gap: '9px', padding: '6px 14px', borderRadius: '999px', background: s.value.pill_bg || 'rgba(255,255,255,0.05)', border: '1px solid ' + line.value, fontSize: '13px', color: s.value.pill_text_color || '#c9cde0', marginBottom: '26px' };
  if (s.value.pill_mono) { st.fontFamily = MONO; st.fontSize = '12px'; }
  return st;
});
const hStyle = computed(() => ({ fontFamily: DISP, fontWeight: 600, color: txt.value, fontSize: 'clamp(40px,6.4vw,78px)', lineHeight: 1.02, letterSpacing: '-.02em', margin: 0 }));
const gradStyle = computed(() => ({ background: `linear-gradient(120deg,${accent.value},${acc2.value})`, WebkitBackgroundClip: 'text', backgroundClip: 'text', color: 'transparent' }));
const subStyle = computed(() => ({ fontSize: '18px', lineHeight: 1.6, color: sub.value, maxWidth: '560px', margin: '24px auto 30px' }));
const solidStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '8px', padding: '15px 28px', borderRadius: '9px', fontFamily: SANS, fontWeight: 600, fontSize: '15px', textDecoration: 'none', background: accent.value, color: accOn.value, border: 0, boxShadow: `0 8px 24px -8px rgba(${glowRgb.value},.6)` }));
const ghostBtnStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '8px', padding: '15px 28px', borderRadius: '9px', fontFamily: SANS, fontWeight: 600, fontSize: '15px', textDecoration: 'none', background: 'rgba(255,255,255,.06)', color: '#fff', border: '1px solid rgba(255,255,255,.16)' }));

const mockWrapStyle = { position: 'relative', zIndex: 2, maxWidth: '1020px', margin: 'clamp(48px,7vw,84px) auto 0', padding: '0 28px' };
const frameStyle = computed(() => ({ border: '1px solid ' + line.value, borderRadius: frameRadius.value, background: panel.value, overflow: 'hidden', boxShadow: `0 -10px 80px -20px rgba(${glowRgb.value},.4)` }));
const barStyle = computed(() => ({ display: 'flex', alignItems: 'center', gap: '7px', padding: '13px 16px', borderBottom: '1px solid ' + line.value, background: panel2.value }));
const dotStyle = { width: '11px', height: '11px', borderRadius: '50%', background: 'rgba(255,255,255,.18)' };
const urlStyle = computed(() => ({ marginLeft: '14px', fontFamily: MONO, fontSize: '11px', color: sub.value }));
const mediaStyle = computed(() => ({ position: 'relative', overflow: 'hidden', aspectRatio: '16/8.5', background: panel.value, backgroundImage: 'repeating-linear-gradient(135deg, rgba(255,255,255,.035) 0 16px, transparent 16px 32px)' }));
const mediaLabelStyle = { position: 'absolute', left: '14px', bottom: '12px', right: '14px', fontFamily: MONO, fontSize: '10.5px', letterSpacing: '.03em', color: 'rgba(255,255,255,.42)', textTransform: 'uppercase', textAlign: 'left' };
const bodyStyle = { display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: '14px', padding: '20px', textAlign: 'left' };
const kpiStyle = computed(() => ({ background: cell.value, border: '1px solid ' + line.value, borderRadius: kpiRadius.value, padding: '16px' }));
const kStyle = computed(() => ({ fontFamily: MONO, fontSize: '10.5px', letterSpacing: '.06em', textTransform: 'uppercase', color: sub.value }));
const vStyle = computed(() => ({ fontFamily: DISP, fontWeight: 700, fontSize: '26px', color: txt.value, margin: '7px 0 4px' }));
const tStyle = computed(() => ({ fontFamily: MONO, fontSize: '11px', color: accent.value }));
const tDnStyle = computed(() => ({ fontFamily: MONO, fontSize: '11px', color: downC.value }));
const chartStyle = computed(() => ({ gridColumn: '1/-1', background: cell.value, border: '1px solid ' + line.value, borderRadius: '11px', padding: '18px 18px 10px' }));
const chHeadStyle = { display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '16px' };
const chTitleStyle = computed(() => ({ fontFamily: DISP, fontSize: '14px', color: txt.value }));
const chMetaStyle = computed(() => ({ fontFamily: s.value.mono_meta ? MONO : SANS, fontSize: '11px', color: sub.value }));
const colStyle = { flex: 1, display: 'flex', flexDirection: 'column', justifyContent: 'flex-end', gap: '3px', height: '100%' };
const colLabelStyle = computed(() => ({ fontFamily: MONO, fontSize: '9.5px', color: sub.value, textAlign: 'center' }));
function barFillStyle(b) {
  const h = Math.max(0, Math.min(100, Number(b.h) || 0));
  const grad = b.alt
    ? `linear-gradient(180deg,${acc2.value},rgba(${acc2Rgb.value},.2))`
    : `linear-gradient(180deg,${accent.value},rgba(${glowRgb.value},.25))`;
  return { display: 'block', width: '100%', borderRadius: '3px 3px 0 0', background: grad, minHeight: '4px', height: h + '%' };
}
</script>

<style scoped>
.oph-btn { transition: transform .15s, background .2s, box-shadow .2s, filter .2s; }
.oph-btn:hover { transform: translateY(-2px); }
.oph-btn--solid:hover { filter: brightness(1.06); }
.oph-btn:focus-visible { outline: 2px solid var(--oph-accent, #6c8cff); outline-offset: 3px; }
@media (max-width: 680px) {
  .oph-body { grid-template-columns: 1fr 1fr !important; }
}
</style>
