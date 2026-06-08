<template>
  <div class="olo-schedule" :style="rootStyle">
    <span v-if="s.eyebrow" :style="eyebrowStyle">{{ s.eyebrow }}</span>
    <h2 v-if="s.heading" :style="headingStyle">{{ s.heading }}</h2>
    <div :style="gridStyle">
      <div :style="headCellStyle">{{ s.corner_label }}</div>
      <div v-for="(d, i) in days" :key="'d'+i" :style="headCellStyle">{{ d }}</div>
      <template v-for="(r, ri) in rows" :key="'r'+ri">
        <div :style="timeCellStyle">{{ r.time }}</div>
        <div v-for="(c, ci) in cellsFor(r)" :key="'c'+ri+'-'+ci" :style="cellStyle(c)">{{ c.text === '' ? '·' : c.text }}</div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = {
  eyebrow: '', heading: '', days: 'Mon, Tue, Wed, Thu, Fri', corner_label: '',
  rows: [
    { time: '07:00', cells: '!Reformer | | Mat | | !Reformer' },
    { time: '12:30', cells: 'Mat | Reformer | | Reformer | Mat' },
    { time: '18:30', cells: '!Reformer | Breath | !Reformer | Mat | Open' },
  ],
  zone_accent: '', zone_on: '#ffffff', cell_bg: '', card_border: '', head_color: '', align: 'left',
};
const s = computed(() => ({ ...defaults, ...props.settings }));
const days = computed(() => String(s.value.days || '').split(',').map(d => d.trim()).filter(Boolean));
const rows = computed(() => Array.isArray(s.value.rows) ? s.value.rows : []);

function normCells(cells) {
  const out = [];
  if (Array.isArray(cells)) {
    cells.forEach(c => {
      if (c && typeof c === 'object') out.push({ text: String(c.text || ''), on: !!c.on });
      else { const t = String(c || '').trim(); const on = t.charAt(0) === '!'; out.push({ text: on ? t.slice(1).trim() : t, on }); }
    });
  } else {
    String(cells || '').split('|').forEach(c => { const t = c.trim(); const on = t.charAt(0) === '!'; out.push({ text: on ? t.slice(1).trim() : t, on }); });
  }
  return out;
}
function cellsFor(r) {
  const c = normCells(r.cells || []);
  const out = [];
  for (let i = 0; i < days.value.length; i++) out.push(c[i] || { text: '', on: false });
  return out;
}

const accent = computed(() => s.value.zone_accent || 'var(--olo-color-primary, #e1474f)');
const on = computed(() => s.value.zone_on || '#ffffff');
const cellbg = computed(() => s.value.cell_bg || 'var(--olo-color-surface,#ffffff)');
const line = computed(() => s.value.card_border || 'var(--olo-color-border,#e5e7eb)');
const headc = computed(() => s.value.head_color || 'var(--olo-color-text-muted,#6b7280)');
const center = computed(() => s.value.align === 'center');
const SERIF = "var(--olo-font-family-heading, 'Playfair Display',Georgia,serif)";
const SANS = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";

const rootStyle = computed(() => ({ fontFamily: SANS, textAlign: center.value ? 'center' : 'left' }));
const eyebrowStyle = computed(() => ({ fontSize: '12px', fontWeight: 700, letterSpacing: '.16em', textTransform: 'uppercase', color: accent.value, display: 'block', marginBottom: '10px' }));
const headingStyle = computed(() => ({ fontFamily: SERIF, fontSize: '38px', lineHeight: 1.12, margin: '0 0 22px', color: 'var(--olo-color-text,#111827)' }));
const gridStyle = computed(() => ({ display: 'grid', gridTemplateColumns: `minmax(56px,auto) repeat(${days.value.length},1fr)`, border: '1px solid ' + line.value, borderRadius: '14px', overflow: 'hidden', background: line.value, gap: '1px', textAlign: 'left' }));
const headCellStyle = computed(() => ({ background: cellbg.value, padding: '14px 12px', fontWeight: 700, fontSize: '11.5px', letterSpacing: '.06em', textTransform: 'uppercase', color: headc.value, display: 'flex', alignItems: 'center' }));
const timeCellStyle = computed(() => ({ background: cellbg.value, padding: '14px 12px', fontWeight: 700, fontSize: '12.5px', color: headc.value, display: 'flex', alignItems: 'center' }));
function cellStyle(c) {
  return { background: c.on ? accent.value : cellbg.value, color: c.on ? on.value : 'inherit', fontWeight: c.on ? 600 : 400, opacity: c.text === '' ? .4 : 1, padding: '14px 12px', fontSize: '13.5px', minHeight: '30px', display: 'flex', alignItems: 'center' };
}
</script>
