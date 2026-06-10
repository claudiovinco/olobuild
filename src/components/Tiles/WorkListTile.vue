<template>
  <div class="olo-worklist" :style="{ borderTop: '1px solid ' + line }">
    <component
      :is="it.link_url ? 'a' : 'div'"
      v-for="(it, idx) in items"
      :key="idx"
      :href="it.link_url || undefined"
      class="olo-worklist__row"
      :style="rowStyle"
    >
      <span class="olo-worklist__n" :style="{ fontFamily: mono, fontSize: numberSize + 'px', color: numberColor }">{{ it.number }}</span>
      <span class="olo-worklist__t" :style="titleStyle">{{ it.title }}</span>
      <span v-if="s.show_category" class="olo-worklist__cat" :style="{ fontFamily: mono, fontSize: catSize + 'px', textTransform: 'uppercase', letterSpacing: '0.02em', color: catColor }">{{ it.category }}</span>
      <span v-if="s.show_year" class="olo-worklist__yr" :style="{ fontFamily: mono, fontSize: yearSize + 'px', color: yearColor }">{{ it.year }}</span>
      <svg v-if="s.show_arrow" class="olo-worklist__arrow" viewBox="0 0 24 24" fill="none" :stroke="arrowColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17 17 7M9 7h8v8" /></svg>
    </component>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { resolveFontFamily } from '@/composables/oloTileDefaults';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  items: [
    { number: '01', title: 'Marisol', category: 'Brand identity', year: '2026', link_url: '' },
    { number: '02', title: 'Atlas Press', category: 'Editorial · web', year: '2025', link_url: '' },
    { number: '03', title: 'Field Museum', category: 'Wayfinding', year: '2025', link_url: '' },
    { number: '04', title: 'Cobalt', category: 'Identity · product', year: '2024', link_url: '' },
  ],
  divider_color: '#d7d1c2', row_padding_y: 26, row_hover_bg: '#e8e3d7', hover_indent: 24,
  number_color: '#8d8a82', number_size: 13,
  title_font_family: 'heading', title_color: '#18181a', title_size: 40, title_weight: '500',
  show_category: true, category_color: '#8d8a82', category_size: 12,
  show_year: true, year_color: '#18181a', year_size: 13,
  show_arrow: true, arrow_color: '#18181a', mono_font_family: '',
};

const HEADING = "var(--olo-font-family-heading, 'DM Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
const BODY = "var(--olo-font-family, 'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
const MONO_FB = "ui-monospace,'SF Mono',Menlo,Consolas,monospace";

const s = computed(() => ({ ...defaults, ...props.settings }));
const items = computed(() => Array.isArray(s.value.items) ? s.value.items : []);
const line = computed(() => s.value.divider_color || '#d7d1c2');
const mono = computed(() => {
  const fam = resolveFontFamily(s.value.mono_font_family);
  if (!fam) return MONO_FB;
  // Nome font puro (legacy campo text) → wrap con lo stack mono di fallback storico.
  return /^[A-Za-z0-9 \-]+$/.test(fam) ? `'${fam}',${MONO_FB}` : fam;
});
const tfam = computed(() => resolveFontFamily(s.value.title_font_family, { heading: HEADING, body: BODY, mono: mono.value }) || HEADING);

const numberColor = computed(() => s.value.number_color);
const numberSize = computed(() => s.value.number_size);
const catColor = computed(() => s.value.category_color);
const catSize = computed(() => s.value.category_size);
const yearColor = computed(() => s.value.year_color);
const yearSize = computed(() => s.value.year_size);
const arrowColor = computed(() => s.value.arrow_color);

const gridCols = computed(() => {
  let c = '48px 1fr';
  if (s.value.show_category) c += ' auto';
  if (s.value.show_year) c += ' auto';
  if (s.value.show_arrow) c += ' auto';
  return c;
});
const rowStyle = computed(() => ({
  display: 'grid', gridTemplateColumns: gridCols.value, gap: '24px', alignItems: 'center',
  padding: (s.value.row_padding_y || 26) + 'px 8px', borderBottom: '1px solid ' + line.value,
  '--olo-wl-indent': (s.value.hover_indent || 0) + 'px', '--olo-wl-hover-bg': s.value.row_hover_bg || 'transparent',
}));
const titleStyle = computed(() => ({
  fontFamily: tfam.value, fontWeight: s.value.title_weight || '500', fontSize: (s.value.title_size || 40) + 'px',
  lineHeight: 1, letterSpacing: '-0.03em', color: s.value.title_color || '#18181a',
}));
</script>

<style scoped>
.olo-worklist__row { cursor: pointer; transition: padding .25s ease, background .25s ease; color: inherit; text-decoration: none; }
.olo-worklist__row:hover { padding-left: var(--olo-wl-indent) !important; padding-right: var(--olo-wl-indent) !important; background: var(--olo-wl-hover-bg); }
.olo-worklist__t { transition: transform .25s ease; }
.olo-worklist__row:hover .olo-worklist__t { transform: translateX(6px); }
.olo-worklist__arrow { width: 20px; height: 20px; opacity: 0; transform: translateX(-6px); transition: opacity .25s ease, transform .25s ease; justify-self: end; }
.olo-worklist__row:hover .olo-worklist__arrow { opacity: 1; transform: none; }
@media (max-width: 640px) {
  .olo-worklist__row { grid-template-columns: 32px 1fr auto !important; gap: 14px !important; }
  .olo-worklist__cat { display: none; }
}
</style>
