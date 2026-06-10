<template>
  <div class="olo-beforeafter" :style="rootStyle">
    <div v-for="(it, i) in items" :key="'c'+i" class="oba-card" :style="cardStyle">
      <div class="oba-pair" :style="pairStyle">
        <div class="oba-media" :style="mediaStyle(it.before_image)">
          <span v-if="it.before_label" :style="labStyle('b')">{{ it.before_label }}</span>
        </div>
        <div class="oba-media" :style="mediaStyle(it.after_image)">
          <span v-if="it.after_label" :style="labStyle('a')">{{ it.after_label }}</span>
        </div>
      </div>
      <div v-if="it.title || it.text" class="oba-cap" :style="{ padding: capPadCss || '16px 4px 4px' }">
        <h3 v-if="it.title" :style="titleStyle">{{ it.title }}</h3>
        <p v-if="it.text" :style="textStyle">{{ it.text }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { buildBgStyle } from '@/composables/useBackgroundStyle';
import { radiusToCss } from '@/composables/useRadius';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  items: [
    { before_image: '', after_image: '', before_label: 'Before', after_label: 'After', title: 'Marcus · 16 weeks', text: 'Down 11kg, first-ever pull-up, and a deadlift PB he never thought he’d hit.' },
    { before_image: '', after_image: '', before_label: 'Before', after_label: 'After', title: 'Priya · 6 months', text: 'Built real strength postpartum, pain-free and back to running.' },
    { before_image: '', after_image: '', before_label: 'Before', after_label: 'After', title: 'Sam · 1 year', text: 'From couch to first powerlifting meet — and stayed for the community.' },
  ],
  columns: 3, gap: 24, media_bg: '', media_aspect: '1/1', accent: '',
  before_label_color: '#ffffff', after_label_color: '#ffffff', title_color: '', text_color: '', card_bg: '', radius: 12,
  // Spaziatura / Forma — additivi e no-op coi default (parità PHP)
  cap_padding: { top: 16, right: 4, bottom: 4, left: 4 },
  card_radius: { tl: 12, tr: 12, br: 12, bl: 12 },
  label_radius: { tl: 999, tr: 999, br: 999, bl: 999 },
  // Kit standard OLObuild — sfondo completo + ombra + bordo (no-op coi default)
  bg: { type: 'none' },
  shadow: 'none',
  border: { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' },
  border_hover: { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: '', color: '' },
  border_hover_duration: 300,
  border_effect: 'none', border_effect_intensity: 'medium', border_effect_color2: '', border_effect_angle: 135, border_effect_speed: 4,
};

const s = computed(() => ({ ...defaults, ...props.settings }));
const items = computed(() => Array.isArray(s.value.items) ? s.value.items : []);

const SERIF = "var(--olo-font-family-heading, 'Playfair Display',Georgia,serif)";
const SANS = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";
const cols = computed(() => Math.max(1, Math.min(4, parseInt(s.value.columns, 10) || 3)));
const mbg = computed(() => s.value.media_bg || 'var(--olo-color-surface-alt, #eceff3)');
const asp = computed(() => String(s.value.media_aspect || '1/1').replace(/[^0-9/]/g, '') || '1/1');
const accent = computed(() => s.value.accent || 'var(--olo-color-primary, #e1474f)');
// Dual-format: numero legacy (12) E oggetto {tl,tr,br,bl} dal type 'border-radius'.
const rad = computed(() => radiusToCss(s.value.radius, { fallback: '0px' }));

// ── Override additivi gated (parità col PHP, no-op coi default) ──
// Padding didascalia: '' (usa '16px 4px 4px') se cap_padding == default.
const capPadCss = computed(() => {
  const cp = s.value.cap_padding;
  if (!cp || typeof cp !== 'object') return '';
  const tp = Math.max(0, parseInt(cp.top, 10) || 0);
  const rp = Math.max(0, parseInt(cp.right, 10) || 0);
  const bp = Math.max(0, parseInt(cp.bottom, 10) || 0);
  const lp = Math.max(0, parseInt(cp.left, 10) || 0);
  if (tp === 16 && rp === 4 && bp === 4 && lp === 4) return '';
  return `${tp}px ${rp}px ${bp}px ${lp}px`;
});
// Raggio card: '' (usa rad) se card_radius == default {12,12,12,12}.
const cardRadiusCss = computed(() => {
  const cr = s.value.card_radius;
  if (!cr || typeof cr !== 'object') return '';
  const tl = parseInt(cr.tl, 10) || 0;
  const tr = parseInt(cr.tr, 10) || 0;
  const br = parseInt(cr.br, 10) || 0;
  const bl = parseInt(cr.bl, 10) || 0;
  if (tl === 12 && tr === 12 && br === 12 && bl === 12) return '';
  if (!tl && !tr && !br && !bl) return '';
  return `${tl}px ${tr}px ${br}px ${bl}px`;
});
// Raggio etichette (pill): '' (usa '999px') se label_radius == default.
const labelRadiusCss = computed(() => {
  const lr = s.value.label_radius;
  if (!lr || typeof lr !== 'object') return '';
  const tl = parseInt(lr.tl, 10);
  const tr = parseInt(lr.tr, 10);
  const br = parseInt(lr.br, 10);
  const bl = parseInt(lr.bl, 10);
  if (tl === 999 && tr === 999 && br === 999 && bl === 999) return '';
  const v = (n) => (Number.isFinite(n) ? n : 0);
  return `${v(tl)}px ${v(tr)}px ${v(br)}px ${v(bl)}px`;
});

// ── Kit standard OLObuild — sfondo completo + ombra + bordo (parità col PHP) ──
const SHADOW_MAP = {
  sm: '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
  md: '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
  lg: '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
  xl: '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
};
const shadowDecl = computed(() => {
  const p = s.value.shadow || 'none';
  if (p === 'none' || p === '') return '';
  if (p === 'custom') {
    const h = parseInt(s.value.shadow_h, 10) || 0;
    const v = parseInt(s.value.shadow_v, 10) || 4;
    const blur = Math.max(0, parseInt(s.value.shadow_blur, 10) || 10);
    const spread = parseInt(s.value.shadow_spread, 10) || 0;
    const color = s.value.shadow_color || 'rgba(0,0,0,0.15)';
    const inset = s.value.shadow_inset ? 'inset ' : '';
    return `${inset}${h}px ${v}px ${blur}px ${spread}px ${color}`;
  }
  return SHADOW_MAP[p] || '';
});
const kitStyle = computed(() => {
  const st = {};
  // Sfondo completo: override SOLO se valorizzato (default type:'none' = invariato)
  const bg = s.value.bg;
  if (bg && bg.type && bg.type !== 'none') Object.assign(st, buildBgStyle(bg));
  // Ombra
  if (shadowDecl.value) st.boxShadow = shadowDecl.value;
  // Bordo (parse_border: serve colore non vuoto + almeno un lato > 0)
  const b = s.value.border || {};
  const col = (b.color || '').trim();
  const t = Math.max(0, parseInt(b.top, 10) || 0);
  const r = Math.max(0, parseInt(b.right, 10) || 0);
  const bo = Math.max(0, parseInt(b.bottom, 10) || 0);
  const l = Math.max(0, parseInt(b.left, 10) || 0);
  if (col && (t || r || bo || l)) {
    const sty = b.style || 'solid';
    if (t === r && r === bo && bo === l) {
      st.border = `${t}px ${sty} ${col}`;
    } else {
      if (t) st.borderTop = `${t}px ${sty} ${col}`;
      if (r) st.borderRight = `${r}px ${sty} ${col}`;
      if (bo) st.borderBottom = `${bo}px ${sty} ${col}`;
      if (l) st.borderLeft = `${l}px ${sty} ${col}`;
    }
    if (s.value.border_effect && s.value.border_effect !== 'none') st.position = 'relative';
  }
  return st;
});
const rootStyle = computed(() => ({ fontFamily: SANS, display: 'grid', gridTemplateColumns: 'repeat(' + cols.value + ', 1fr)', gap: (parseInt(s.value.gap, 10) || 24) + 'px', ...kitStyle.value }));
const cardStyle = computed(() => ({ background: s.value.card_bg || 'transparent', borderRadius: cardRadiusCss.value || rad.value, overflow: 'hidden' }));
const pairStyle = computed(() => ({ position: 'relative', display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '2px' }));
function mediaStyle(img) {
  const st = { position: 'relative', aspectRatio: asp.value, background: mbg.value, backgroundSize: 'cover', backgroundPosition: 'center' };
  if (img) st.backgroundImage = 'url(' + img + ')';
  return st;
}
function labStyle(side) {
  const base = { position: 'absolute', top: '10px', fontSize: '10.5px', fontWeight: 700, letterSpacing: '.08em', textTransform: 'uppercase', padding: '4px 10px', borderRadius: labelRadiusCss.value || '999px' };
  if (side === 'b') return { ...base, left: '10px', background: 'rgba(0,0,0,.55)', color: s.value.before_label_color || '#ffffff' };
  return { ...base, right: '10px', background: accent.value, color: s.value.after_label_color || '#ffffff' };
}
const titleStyle = computed(() => ({ fontFamily: SERIF, fontSize: '19px', lineHeight: 1.25, margin: 0, color: s.value.title_color || 'var(--olo-color-text, #111827)' }));
const textStyle = computed(() => ({ fontSize: '14px', lineHeight: 1.55, margin: '8px 0 0', color: s.value.text_color || 'var(--olo-color-text-muted, #6b7280)' }));
</script>
