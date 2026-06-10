<template>
  <div class="olo-statstrip" :style="bandStyle">
    <div class="olo-statstrip__grid" :style="gridStyle">
      <div
        v-for="(it, idx) in items"
        :key="idx"
        class="olo-statstrip__cell"
        :class="{ 'has-divider': s.show_dividers && idx > 0 }"
        :style="cellStyle"
      >
        <div class="olo-statstrip__value" :style="valueStyle">{{ it.value }}</div>
        <div class="olo-statstrip__label" :style="labelStyle">{{ it.label }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { resolveFontFamily } from '@/composables/oloTileDefaults';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  items: [
    { value: '500+', label: 'Progetti consegnati' },
    { value: '12', label: 'Anni di attività' },
    { value: '98%', label: 'Clienti soddisfatti' },
    { value: '40M', label: 'Utenti raggiunti' },
  ],
  columns: 4, band_padding_y: 40, show_dividers: true, divider_color: '#d7d1c2', band_border: true,
  value_font_family: 'heading', value_color: '#18181a', value_size: 48, value_weight: '600',
  label_color: '#8d8a82', label_size: 13, label_uppercase: false, align: 'left', mono_font_family: '',
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
const vfam = computed(() => resolveFontFamily(s.value.value_font_family, { heading: HEADING, body: BODY, mono: mono.value }) || HEADING);
const align = computed(() => s.value.align === 'center' ? 'center' : 'left');

const bandStyle = computed(() => {
  const st = { padding: (s.value.band_padding_y || 0) + 'px 0' };
  if (s.value.band_border) { st.borderTop = '1px solid ' + line.value; st.borderBottom = '1px solid ' + line.value; }
  return st;
});
const gridStyle = computed(() => ({ display: 'grid', gridTemplateColumns: `repeat(${Math.max(1, Math.min(6, s.value.columns || 4))},minmax(0,1fr))` }));
const cellStyle = computed(() => ({
  display: 'flex', flexDirection: 'column', gap: '8px', textAlign: align.value, padding: '4px 24px',
  alignItems: align.value === 'center' ? 'center' : 'stretch',
  '--olo-ss-line': line.value,
}));
const valueStyle = computed(() => ({
  fontFamily: vfam.value, fontWeight: s.value.value_weight || '600', fontSize: (s.value.value_size || 48) + 'px',
  lineHeight: 1, letterSpacing: '-0.02em', color: s.value.value_color || '#18181a',
}));
const labelStyle = computed(() => ({
  fontFamily: mono.value, fontSize: (s.value.label_size || 13) + 'px', color: s.value.label_color || '#8d8a82', lineHeight: 1.4,
  textTransform: s.value.label_uppercase ? 'uppercase' : 'none', letterSpacing: s.value.label_uppercase ? '0.06em' : 'normal',
}));
</script>

<style scoped>
.olo-statstrip__cell.has-divider { border-left: 1px solid var(--olo-ss-line); }
@media (max-width: 760px) {
  .olo-statstrip__grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; gap: 28px 0; }
}
</style>
