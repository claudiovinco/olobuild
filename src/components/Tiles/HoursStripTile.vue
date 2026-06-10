<template>
  <div class="olo-hoursstrip" :style="bandStyle">
    <div class="olo-hoursstrip__grid" :style="gridStyle">
      <div
        v-for="(it, idx) in items"
        :key="idx"
        class="olo-hoursstrip__cell"
        :class="{ 'has-divider': s.show_dividers && idx > 0 }"
        :style="cellStyle"
      >
        <div v-if="it.day" class="olo-hoursstrip__day" :style="dayStyle">{{ it.day }}</div>
        <div class="olo-hoursstrip__time" :style="timeStyle">{{ it.time }}</div>
        <div v-if="it.note" class="olo-hoursstrip__note" :style="noteStyle">{{ it.note }}</div>
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
    { day: 'Lun — Gio', time: '12 — 23', note: 'Cucina fino alle 22' },
    { day: 'Ven — Sab', time: '12 — 24', note: 'Aperitivo dalle 18' },
    { day: 'Domenica', time: '12 — 16', note: 'Solo pranzo' },
    { day: 'Martedì', time: 'Chiuso', note: 'Riposo settimanale' },
  ],
  columns: 4, band_padding_y: 36, show_dividers: true, divider_color: '#d7d1c2', band_border: true,
  day_color: '#8d8a82', day_size: 12,
  time_font_family: 'heading', time_color: '#18181a', time_size: 30, time_weight: '500',
  note_color: '#8d8a82', note_size: 13, mono_font_family: '',
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
const tfam = computed(() => resolveFontFamily(s.value.time_font_family, { heading: HEADING, body: BODY, mono: mono.value }) || HEADING);

const bandStyle = computed(() => {
  const st = { padding: (s.value.band_padding_y || 0) + 'px 0' };
  if (s.value.band_border) { st.borderTop = '1px solid ' + line.value; st.borderBottom = '1px solid ' + line.value; }
  return st;
});
const gridStyle = computed(() => ({ display: 'grid', gridTemplateColumns: `repeat(${Math.max(1, Math.min(6, s.value.columns || 4))},minmax(0,1fr))` }));
const cellStyle = computed(() => ({ display: 'flex', flexDirection: 'column', gap: '7px', padding: '4px 24px', '--olo-hs-line': line.value }));
const dayStyle = computed(() => ({ fontFamily: mono.value, fontSize: (s.value.day_size || 12) + 'px', textTransform: 'uppercase', letterSpacing: '0.06em', color: s.value.day_color || '#8d8a82' }));
const timeStyle = computed(() => ({ fontFamily: tfam.value, fontWeight: s.value.time_weight || '500', fontSize: (s.value.time_size || 30) + 'px', lineHeight: 1.05, letterSpacing: '-0.01em', color: s.value.time_color || '#18181a' }));
const noteStyle = computed(() => ({ fontSize: (s.value.note_size || 13) + 'px', lineHeight: 1.4, color: s.value.note_color || '#8d8a82' }));
</script>

<style scoped>
.olo-hoursstrip__cell.has-divider { border-left: 1px solid var(--olo-hs-line); }
@media (max-width: 760px) {
  .olo-hoursstrip__grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; gap: 26px 0; }
}
</style>
