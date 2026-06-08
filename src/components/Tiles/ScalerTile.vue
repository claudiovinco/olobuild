<template>
  <div class="olo-scaler" :style="rootStyle">
    <span v-if="s.eyebrow" :style="eyebrowStyle">{{ s.eyebrow }}</span>
    <h2 v-if="s.heading" :style="headingStyle">{{ s.heading }}</h2>
    <p v-if="s.intro" :style="introStyle">{{ s.intro }}</p>
    <div :style="panelStyle">
      <div style="display:flex;align-items:baseline;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:6px">
        <span :style="baseLabelStyle">{{ s.base_label }}</span>
        <span :style="baseValStyle">{{ cur }}<template v-if="s.base_suffix"> {{ s.base_suffix }}</template></span>
      </div>
      <input type="range" :style="rangeStyle" :min="s.base_min" :max="s.base_max" :step="s.base_step" v-model="cur" :aria-label="s.base_label" />
      <div v-for="(it, i) in items" :key="i" :style="rowStyle(i)">
        <span :style="rowNameStyle">{{ it.name }}</span>
        <span :style="rowValStyle">{{ fmt(compute(it)) }}<u v-if="it.unit" style="font-weight:400;opacity:.6;font-size:13px;margin-left:3px">{{ it.unit }}</u></span>
      </div>
      <div v-if="s.show_total" :style="totStyle">
        <span :style="baseLabelStyle">{{ s.total_label }}</span>
        <b :style="totBStyle">{{ fmt(Math.round(total)) }} {{ s.total_unit }}</b>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = {
  eyebrow: '', heading: 'Scala la ricetta', intro: '', mode: 'scale',
  base_label: 'Porzioni', base_value: 4, base_min: 1, base_max: 12, base_step: 1, base_suffix: '',
  items: [
    { name: 'Ingrediente A', amount: 200, unit: 'g' },
    { name: 'Ingrediente B', amount: 2, unit: '' },
    { name: 'Ingrediente C', amount: 50, unit: 'ml' },
  ],
  show_total: false, total_label: 'Totale', total_unit: 'g',
  zone_accent: '', card_bg: '', card_border: '', align: 'left',
};
const s = computed(() => ({ ...defaults, ...props.settings }));
const items = computed(() => Array.isArray(s.value.items) ? s.value.items : []);
const base = computed(() => parseFloat(s.value.base_value) || 1);
const cur = ref(base.value);
watch(base, v => { cur.value = v; });

function compute(it) {
  const a = parseFloat(it.amount) || 0;
  const c = parseFloat(cur.value) || 0;
  return s.value.mode === 'percent' ? (c * a / 100) : (a * (base.value === 0 ? 0 : c / base.value));
}
function fmt(n) { return new Intl.NumberFormat('en-US', { maximumFractionDigits: 1 }).format(Math.round(n * 10) / 10); }
const total = computed(() => items.value.reduce((t, it) => t + compute(it), 0));

const accent = computed(() => s.value.zone_accent || 'var(--olo-color-primary, #e1474f)');
const line = computed(() => s.value.card_border || 'var(--olo-color-border,#e5e7eb)');
const center = computed(() => s.value.align === 'center');
const SERIF = "var(--olo-font-family-heading, 'Playfair Display',Georgia,serif)";
const SANS = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";
const pct = computed(() => { const mn = parseFloat(s.value.base_min) || 0, mx = parseFloat(s.value.base_max) || 100; return (mx === mn ? 0 : ((parseFloat(cur.value) - mn) / (mx - mn) * 100)) + '%'; });

const rootStyle = computed(() => ({ fontFamily: SANS, textAlign: center.value ? 'center' : 'left' }));
const eyebrowStyle = computed(() => ({ fontSize: '12px', fontWeight: 700, letterSpacing: '.16em', textTransform: 'uppercase', color: accent.value, display: 'block', marginBottom: '10px' }));
const headingStyle = computed(() => ({ fontFamily: SERIF, fontSize: '38px', lineHeight: 1.12, margin: 0, color: 'var(--olo-color-text,#111827)' }));
const introStyle = computed(() => ({ fontSize: '15.5px', lineHeight: 1.6, opacity: .8, margin: center.value ? '14px auto 0' : '14px 0 0', maxWidth: '560px' }));
const panelStyle = computed(() => ({ marginTop: '26px', background: s.value.card_bg || 'var(--olo-color-surface-alt,#f6f7f9)', border: '1px solid ' + line.value, borderRadius: '16px', padding: '34px', textAlign: 'left', maxWidth: center.value ? '620px' : 'none', marginLeft: center.value ? 'auto' : '0', marginRight: center.value ? 'auto' : '0' }));
const baseLabelStyle = computed(() => ({ fontSize: '12px', fontWeight: 700, letterSpacing: '.06em', textTransform: 'uppercase', opacity: .7 }));
const baseValStyle = computed(() => ({ fontFamily: SERIF, fontSize: '26px', color: accent.value }));
const rangeStyle = computed(() => ({ width: '100%', height: '6px', borderRadius: '99px', cursor: 'pointer', margin: '8px 0 22px', accentColor: accent.value, background: `linear-gradient(to right, ${accent.value} ${pct.value}, ${line.value} ${pct.value})`, WebkitAppearance: 'none', appearance: 'none' }));
function rowStyle(i) { return { display: 'flex', alignItems: 'baseline', justifyContent: 'space-between', gap: '14px', padding: '12px 0', borderTop: '1px solid ' + line.value }; }
const rowNameStyle = computed(() => ({ fontSize: '15px', color: 'var(--olo-color-text,#111827)' }));
const rowValStyle = computed(() => ({ fontWeight: 700, color: 'var(--olo-color-text,#111827)', whiteSpace: 'nowrap' }));
const totStyle = computed(() => ({ display: 'flex', alignItems: 'baseline', justifyContent: 'space-between', gap: '14px', marginTop: '16px', paddingTop: '14px', borderTop: '2px solid ' + line.value }));
const totBStyle = computed(() => ({ fontFamily: SERIF, fontSize: '24px', color: accent.value }));
</script>
