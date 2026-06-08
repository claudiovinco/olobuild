<template>
  <div class="olo-availability" :style="rootStyle">
    <span v-if="s.eyebrow" :style="eyebrowStyle">{{ s.eyebrow }}</span>
    <h2 v-if="s.heading" :style="headingStyle">{{ s.heading }}</h2>
    <p v-if="s.intro" :style="introStyle">{{ s.intro }}</p>
    <div :style="gridStyle">
      <div :style="hdStyle"></div>
      <div v-for="(d, i) in days" :key="'d'+i" :style="hdStyle">{{ d }}</div>
      <template v-for="(b, bi) in bands" :key="'b'+bi">
        <div :style="blStyle">{{ b }}</div>
        <button v-for="(d, di) in days" :key="'c'+bi+'-'+di" type="button"
          :style="cellStyle(bi*days.length+di)" @click="toggle(bi*days.length+di)"></button>
      </template>
    </div>
    <div :style="footStyle">
      <div>
        <span :style="countLabelStyle">{{ s.count_label }}</span>
        <b :style="countStyle">{{ count }}</b>
      </div>
      <div v-if="tier">
        <div :style="tierLabelStyle">{{ s.verdict_label }}</div>
        <div :style="tierNameStyle">{{ tier.label }}</div>
        <div v-if="tier.text" :style="tierTextStyle">{{ tier.text }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = {
  eyebrow: '', heading: 'Quando puoi?', intro: '',
  days: 'Mon, Tue, Wed, Thu, Fri, Sat, Sun', bands: 'Morning, Midday, Evening',
  count_label: 'Slot scelti', verdict_label: 'Consigliato',
  tiers: [
    { min: 0, label: 'Reset', text: 'Poco tempo: una base sostenibile.' },
    { min: 5, label: 'Build', text: 'Buona costanza: progressione vera.' },
    { min: 10, label: 'Peak', text: 'Massima disponibilità: spingi.' },
  ],
  zone_accent: '', zone_on: '#ffffff', cell_bg: '', card_border: '', align: 'left',
};
const s = computed(() => ({ ...defaults, ...props.settings }));
const days = computed(() => String(s.value.days || '').split(',').map(d => d.trim()).filter(Boolean));
const bands = computed(() => String(s.value.bands || '').split(',').map(d => d.trim()).filter(Boolean));
const tiers = computed(() => (Array.isArray(s.value.tiers) ? s.value.tiers.slice() : []).sort((a, b) => (parseInt(a.min) || 0) - (parseInt(b.min) || 0)));

const sel = ref({});
watch([days, bands], () => { sel.value = {}; });
function toggle(i) { sel.value = { ...sel.value, [i]: !sel.value[i] }; }
const count = computed(() => Object.values(sel.value).filter(Boolean).length);
const tier = computed(() => {
  let chosen = tiers.value[0] || null;
  tiers.value.forEach(t => { if (count.value >= (parseInt(t.min) || 0)) chosen = t; });
  return chosen;
});

const accent = computed(() => s.value.zone_accent || 'var(--olo-color-primary, #e1474f)');
const line = computed(() => s.value.card_border || 'var(--olo-color-border,#e5e7eb)');
const cellbg = computed(() => s.value.cell_bg || 'var(--olo-color-surface,#ffffff)');
const center = computed(() => s.value.align === 'center');
const SERIF = "var(--olo-font-family-heading, 'Playfair Display',Georgia,serif)";
const SANS = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";

const rootStyle = computed(() => ({ fontFamily: SANS, textAlign: center.value ? 'center' : 'left' }));
const eyebrowStyle = computed(() => ({ fontSize: '12px', fontWeight: 700, letterSpacing: '.16em', textTransform: 'uppercase', color: accent.value, display: 'block', marginBottom: '10px' }));
const headingStyle = computed(() => ({ fontFamily: SERIF, fontSize: '38px', lineHeight: 1.12, margin: 0, color: 'var(--olo-color-text,#111827)' }));
const introStyle = computed(() => ({ fontSize: '15.5px', lineHeight: 1.6, opacity: .8, margin: '14px 0 24px', maxWidth: '560px' }));
const gridStyle = computed(() => ({ display: 'grid', gridTemplateColumns: `minmax(64px,auto) repeat(${days.value.length},1fr)`, gap: '6px', textAlign: 'left' }));
const hdStyle = computed(() => ({ fontWeight: 700, fontSize: '11px', letterSpacing: '.05em', textTransform: 'uppercase', color: 'var(--olo-color-text-muted,#6b7280)', padding: '6px 4px', alignSelf: 'center' }));
const blStyle = computed(() => ({ fontWeight: 700, fontSize: '12px', color: 'var(--olo-color-text-muted,#6b7280)', alignSelf: 'center' }));
function cellStyle(i) { const on = !!sel.value[i]; return { background: on ? accent.value : cellbg.value, border: '1px solid ' + (on ? accent.value : line.value), borderRadius: '8px', minHeight: '34px', cursor: 'pointer', transition: 'all .12s', padding: 0 }; }
const footStyle = computed(() => ({ display: 'flex', alignItems: 'center', gap: '22px', flexWrap: 'wrap', marginTop: '24px', paddingTop: '20px', borderTop: '2px solid ' + line.value, justifyContent: center.value ? 'center' : 'flex-start' }));
const countLabelStyle = computed(() => ({ fontSize: '11px', fontWeight: 700, letterSpacing: '.06em', textTransform: 'uppercase', opacity: .6, display: 'block' }));
const countStyle = computed(() => ({ fontFamily: SERIF, fontSize: '30px', color: accent.value }));
const tierLabelStyle = computed(() => ({ fontSize: '11px', fontWeight: 700, letterSpacing: '.06em', textTransform: 'uppercase', opacity: .6 }));
const tierNameStyle = computed(() => ({ fontFamily: SERIF, fontSize: '22px', color: 'var(--olo-color-text,#111827)' }));
const tierTextStyle = computed(() => ({ fontSize: '13.5px', lineHeight: 1.5, opacity: .8, marginTop: '2px', maxWidth: '340px' }));
</script>
