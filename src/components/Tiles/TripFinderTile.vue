<template>
  <form class="olo-tripfinder" :style="rootStyle" onsubmit="return false">
    <div class="otf-bar" :style="barStyle">
      <label v-for="(f, i) in fields" :key="'f'+i" class="otf-f" :style="fieldStyle(i)">
        <span class="otf-lab" :style="labStyle">{{ f.label }}</span>
        <select class="otf-sel" :style="selStyle" :value="f.value" :aria-label="f.label">
          <option v-for="(o, k) in optionsOf(f)" :key="k" :value="o">{{ o }}</option>
        </select>
      </label>
      <a class="otf-btn" :style="btnStyle" :href="s.button_url || '#'">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
        {{ s.button_text || 'Search' }}
      </a>
    </div>
  </form>
</template>

<script setup>
import { computed } from 'vue';
import { buildBgStyle } from '@/composables/useBackgroundStyle';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  fields: [
    { label: 'Destination', value: 'Anywhere', options: 'Anywhere\nNorway · Lofoten\nIceland\nGreenland' },
    { label: 'When', value: 'Any month', options: 'Any month\nMar — aurora\nJun — midnight sun\nSep — autumn light' },
    { label: 'Activity', value: 'Any', options: 'Any\nHiking & trekking\nSea kayaking\nWildlife' },
  ],
  button_text: 'Search', button_url: '#',
  accent: '', accent_on: '#ffffff', bar_bg: '', field_bg: '', field_border: '', label_color: '', value_color: '', radius: 14,

  // SPAZIATURA additiva — default = padding storico (barra 8px, campi 10/16).
  bar_padding: { top: 8, right: 8, bottom: 8, left: 8 },
  field_padding: { top: 10, right: 16, bottom: 10, left: 16 },

  // FORMA additiva — raggio per-angolo override. Tutto 0 → usa `radius` (no-op).
  radius_corners: { tl: 0, tr: 0, br: 0, bl: 0 },

  // KIT standard OLObuild — sfondo completo + ombra + bordo sul contenitore.
  // Default no-op: bg none / shadow none / border 0 → render invariato.
  bg: { type: 'none' },
  shadow: 'none',
  border: { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' },
  border_hover: { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: '', color: '' },
  border_hover_duration: 300,
  border_effect: 'none', border_effect_intensity: 'medium',
  border_effect_color2: '', border_effect_angle: 135, border_effect_speed: 4,
};

// Mappa ombre IDENTICA al PHP build_shadow_decl (parità render).
const SHADOW_MAP = {
  sm: '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
  md: '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
  lg: '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
  xl: '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
};

const s = computed(() => ({ ...defaults, ...props.settings }));
const fields = computed(() => Array.isArray(s.value.fields) ? s.value.fields : []);
function optionsOf(f) { return String(f.options || '').split('\n').map(o => o.trim()).filter(Boolean); }

const SANS = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";
const accent = computed(() => s.value.accent || 'var(--olo-color-primary, #e1474f)');
const on = computed(() => s.value.accent_on || '#ffffff');
const barBg = computed(() => s.value.bar_bg || 'var(--olo-color-surface, #ffffff)');
const fieldBg = computed(() => s.value.field_bg || 'transparent');
const fbd = computed(() => s.value.field_border || 'var(--olo-color-border, #e5e7eb)');
const labCol = computed(() => s.value.label_color || 'var(--olo-color-text-muted, #6b7280)');
const valCol = computed(() => s.value.value_color || 'var(--olo-color-text, #111827)');
const rad = computed(() => (parseInt(s.value.radius, 10) || 0) + 'px');

// FORMA: raggio per-angolo override (parità con build_border_radius_css PHP).
// Tutti 0 → '' → fallback al raggio uniforme `rad` storico (no-op coi default).
const radCorners = computed(() => {
  const c = s.value.radius_corners;
  if (!c || typeof c !== 'object') return '';
  const tl = parseInt(c.tl, 10) || 0;
  const tr = parseInt(c.tr, 10) || 0;
  const br = parseInt(c.br, 10) || 0;
  const bl = parseInt(c.bl, 10) || 0;
  if (!tl && !tr && !br && !bl) return '';
  return `${tl}px ${tr}px ${br}px ${bl}px`;
});
const radEff = computed(() => radCorners.value || rad.value);

// SPAZIATURA: padding barra/campi (parità con tf_pad_css PHP). Default = storico.
function padCss(pad, fb) {
  const top = pad && pad.top != null ? (parseInt(pad.top, 10) || 0) : fb[0];
  const right = pad && pad.right != null ? (parseInt(pad.right, 10) || 0) : fb[1];
  const bottom = pad && pad.bottom != null ? (parseInt(pad.bottom, 10) || 0) : fb[2];
  const left = pad && pad.left != null ? (parseInt(pad.left, 10) || 0) : fb[3];
  return `${top}px ${right}px ${bottom}px ${left}px`;
}
const barPad = computed(() => padCss(s.value.bar_padding, [8, 8, 8, 8]));
const fieldPad = computed(() => padCss(s.value.field_padding, [10, 16, 10, 16]));

// KIT standard: ombra (preset/custom) — IDENTICO al PHP build_shadow_decl.
const shadowDecl = computed(() => {
  const p = s.value.shadow || 'none';
  if (p === 'none' || p === '') return '';
  if (p === 'custom') {
    const h = parseInt(s.value.shadow_h, 10) || 0;
    const v = parseInt(s.value.shadow_v, 10) || 0;
    const blur = Math.max(0, parseInt(s.value.shadow_blur, 10) || 0);
    const spread = parseInt(s.value.shadow_spread, 10) || 0;
    const color = s.value.shadow_color || 'rgba(0,0,0,0.15)';
    const inset = s.value.shadow_inset ? 'inset ' : '';
    return `${inset}${h}px ${v}px ${blur}px ${spread}px ${color}`;
  }
  return SHADOW_MAP[p] || '';
});

// KIT standard: bordo base (parità con build_border_css PHP, no-op coi default).
const borderDecl = computed(() => {
  const b = s.value.border;
  if (!b || typeof b !== 'object') return null;
  const color = (b.color || '').trim();
  if (color === '') return null;
  const style = b.style || 'solid';
  const top = Math.max(0, parseInt(b.top, 10) || 0);
  const right = Math.max(0, parseInt(b.right, 10) || 0);
  const bottom = Math.max(0, parseInt(b.bottom, 10) || 0);
  const left = Math.max(0, parseInt(b.left, 10) || 0);
  if (!top && !right && !bottom && !left) return null;
  if (top === right && right === bottom && bottom === left) {
    return { border: `${top}px ${style} ${color}` };
  }
  const st = {};
  if (top) st.borderTop = `${top}px ${style} ${color}`;
  if (right) st.borderRight = `${right}px ${style} ${color}`;
  if (bottom) st.borderBottom = `${bottom}px ${style} ${color}`;
  if (left) st.borderLeft = `${left}px ${style} ${color}`;
  return st;
});

// KIT standard: sfondo completo opzionale (override SOLO se valorizzato).
const bgDecl = computed(() => {
  const bg = s.value.bg;
  if (!bg || !bg.type || bg.type === 'none') return {};
  return buildBgStyle(bg);
});

const rootStyle = computed(() => {
  const st = { fontFamily: SANS, '--otf-accent': accent.value, ...bgDecl.value };
  const bd = borderDecl.value;
  if (bd) Object.assign(st, bd);
  if (shadowDecl.value) st.boxShadow = shadowDecl.value;
  if (Object.keys(bgDecl.value).length || bd || shadowDecl.value) st.position = 'relative';
  return st;
});
const barStyle = computed(() => ({ display: 'flex', flexWrap: 'wrap', alignItems: 'stretch', background: barBg.value, border: '1px solid ' + fbd.value, borderRadius: radEff.value, padding: barPad.value, boxShadow: '0 18px 50px -28px rgba(0,0,0,.35)' }));
function fieldStyle(i) {
  return { flex: '1 1 160px', display: 'flex', flexDirection: 'column', gap: '4px', padding: fieldPad.value, background: fieldBg.value, borderLeft: i === 0 ? '0' : '1px solid ' + fbd.value, minWidth: 0 };
}
const labStyle = computed(() => ({ fontSize: '11px', fontWeight: 600, letterSpacing: '.12em', textTransform: 'uppercase', color: labCol.value }));
const CHEVRON = "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23999' stroke-width='2.4' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E\")";
const selStyle = computed(() => ({ fontFamily: SANS, fontSize: '15px', fontWeight: 600, color: valCol.value, background: 'transparent', border: 0, padding: '2px 18px 2px 0', cursor: 'pointer', width: '100%', appearance: 'none', WebkitAppearance: 'none', backgroundImage: CHEVRON, backgroundRepeat: 'no-repeat', backgroundPosition: 'right center' }));
const btnStyle = computed(() => ({ flex: '0 0 auto', display: 'inline-flex', alignItems: 'center', justifyContent: 'center', gap: '8px', marginLeft: '8px', padding: '0 26px', background: accent.value, color: on.value, fontWeight: 700, fontSize: '14px', letterSpacing: '.02em', border: 0, borderRadius: radEff.value, textDecoration: 'none', transition: 'transform .18s, filter .18s' }));
</script>

<style scoped>
.otf-btn:hover { transform: translateY(-1px); filter: brightness(1.05); }
.otf-sel:focus-visible { outline: 2px solid var(--otf-accent, #e1474f); outline-offset: 2px; }
.otf-btn:focus-visible { outline: 2px solid var(--otf-accent, #e1474f); outline-offset: 3px; }
@media (max-width: 680px) {
  .otf-bar { flex-direction: column; }
}
</style>
