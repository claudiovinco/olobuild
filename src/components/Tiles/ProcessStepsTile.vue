<template>
  <div class="olo-psteps" :style="gridStyle">
    <div v-for="(it, i) in items" :key="i" class="olo-psteps__item" :style="itemStyle">
      <span v-if="isCircle" :style="circleStyle">{{ numFor(it, i) }}</span>
      <span v-else :style="plainNumStyle">{{ numFor(it, i) }}</span>
      <h3 v-if="it.title" :style="titleStyle">{{ it.title }}</h3>
      <p v-if="it.description" :style="descStyle">{{ it.description }}</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { resolveFontFamily } from '@/composables/oloTileDefaults';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  items: [
    { number: '01', title: 'Listen', description: 'We start with your life, not your balance sheet.' },
    { number: '02', title: 'Plan', description: 'A clear strategy, modelled and stress-tested.' },
    { number: '03', title: 'Invest', description: 'Patient, diversified, low-cost where it counts.' },
    { number: '04', title: 'Review', description: 'We meet regularly and adjust as life changes.' },
  ],
  columns: 4, gap: 16, auto_number: false,
  number_style: 'plain', number_color: 'var(--olo-color-primary, #e1474f)', number_bg: '',
  number_size: 40, number_font: 'serif', number_weight: '500',
  title_color: '', title_size: 21, title_weight: '600', title_font: 'serif',
  desc_color: '', desc_size: 14, align: 'left', item_gap: 8,
  card_bg: '', card_border: '', card_radius: { tl: 0, tr: 0, br: 0, bl: 0, linked: true }, card_padding: 0,
};

const s = computed(() => ({ ...defaults, ...props.settings }));
const items = computed(() => Array.isArray(s.value.items) ? s.value.items : []);

const SERIF = "var(--olo-font-family-heading, 'Playfair Display',Georgia,serif)";
const SANS  = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";
const MONO  = "ui-monospace,Menlo,Consolas,monospace";
// Stack storici della tile per i valori legacy ancora salvati nei template.
const FONT_LEGACY = { serif: SERIF, 'sans-serif': SANS, mono: MONO };

const isCircle = computed(() => s.value.number_style === 'circle' || s.value.number_style === 'outline');

function numFor(it, i) {
  if (s.value.auto_number) return String(i + 1).padStart(2, '0');
  return it.number != null && it.number !== '' ? it.number : String(i + 1).padStart(2, '0');
}
function radiusToCss(r) {
  if (!r) return '0';
  if (typeof r === 'number') return r + 'px';
  return `${r.tl ?? 0}px ${r.tr ?? 0}px ${r.br ?? 0}px ${r.bl ?? 0}px`;
}

const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${Math.max(1, Math.min(6, parseInt(s.value.columns) || 4))}, 1fr)`,
  gap: (parseInt(s.value.gap) || 16) + 'px',
}));

const itemStyle = computed(() => {
  const align = ['left', 'center', 'right'].includes(s.value.align) ? s.value.align : 'left';
  const st = {
    display: 'flex', flexDirection: 'column', gap: (parseInt(s.value.item_gap) || 8) + 'px',
    textAlign: align, alignItems: align === 'center' ? 'center' : (align === 'right' ? 'flex-end' : 'flex-start'),
    padding: s.value.card_padding ? (parseInt(s.value.card_padding) + 'px') : '0 12px',
  };
  if (s.value.card_bg) st.background = s.value.card_bg;
  if (s.value.card_border) st.border = `1px solid ${s.value.card_border}`;
  if (s.value.card_radius) st.borderRadius = radiusToCss(s.value.card_radius);
  return st;
});

const nfont = computed(() => resolveFontFamily(s.value.number_font, FONT_LEGACY) || SERIF);
const ncolor = computed(() => s.value.number_color || 'var(--olo-color-primary, #e1474f)');
const nsize = computed(() => Math.max(12, Math.min(96, parseInt(s.value.number_size) || 40)));

const plainNumStyle = computed(() => ({
  fontFamily: nfont.value, fontWeight: s.value.number_weight || '500',
  fontSize: nsize.value + 'px', lineHeight: 1, color: ncolor.value, display: 'block',
}));

const circleStyle = computed(() => {
  const d = nsize.value + 24;
  const base = {
    width: d + 'px', height: d + 'px', borderRadius: '50%', display: 'inline-flex',
    alignItems: 'center', justifyContent: 'center', fontFamily: nfont.value,
    fontWeight: s.value.number_weight || '500', fontSize: Math.round(nsize.value * 0.6) + 'px',
    lineHeight: 1, color: ncolor.value,
  };
  if (s.value.number_style === 'circle') base.background = s.value.number_bg || 'rgba(127,127,127,.12)';
  else base.border = `1px solid ${s.value.number_bg || ncolor.value}`;
  return base;
});

const titleStyle = computed(() => ({
  fontFamily: resolveFontFamily(s.value.title_font, FONT_LEGACY) || SERIF, fontWeight: s.value.title_weight || '600',
  fontSize: (parseInt(s.value.title_size) || 21) + 'px', lineHeight: 1.2,
  color: s.value.title_color || 'var(--olo-color-text, #111827)', margin: 0,
}));

const descStyle = computed(() => ({
  fontFamily: SANS, fontSize: (parseInt(s.value.desc_size) || 14) + 'px', lineHeight: 1.6,
  color: s.value.desc_color || 'var(--olo-color-text-muted, #6b7280)', margin: 0,
}));
</script>
