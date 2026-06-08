<template>
  <div class="olo-builder" :style="rootStyle">
    <!-- SPLIT layout (header + tally + grid card) -->
    <template v-if="s.layout === 'split'">
      <div :style="splitHeadStyle">
        <div>
          <span v-if="s.eyebrow" :style="splitEyebrowStyle">{{ s.eyebrow }}</span>
          <h2 v-if="s.heading" :style="splitHStyle">{{ s.heading }}<span v-if="s.heading_accent" :style="{ color: accent }"> {{ s.heading_accent }}</span></h2>
          <p v-if="s.intro" :style="splitIntroStyle">{{ s.intro }}</p>
        </div>
        <div :style="tallyStyle">
          <div><b :style="tallyNumStyle">{{ totalCount }}</b> <span :style="tallyLblStyle">{{ s.count_label }}</span></div>
          <div :style="tallyTotStyle">{{ cur }}{{ totalFmt }}</div>
        </div>
      </div>
      <div :style="splitGridStyle">
        <div v-for="(it, i) in rows" :key="i" :style="splitItemStyle(i)">
          <div><h3 :style="splitNameStyle">{{ it.name }}</h3><span v-if="it.note" :style="splitNoteStyle">{{ it.note }}</span></div>
          <div style="display:flex;align-items:center;gap:14px;flex:0 0 auto">
            <span :style="splitPriceStyle">{{ cur }}{{ it.price }}</span>
            <div :style="splitStepWrap"><button type="button" :style="splitStepBtn" @click="dec(i)">−</button><span :style="splitCountStyle">{{ counts[i] }}</span><button type="button" :style="splitStepBtn" @click="inc(i)">+</button></div>
          </div>
        </div>
      </div>
    </template>

    <!-- PANEL layout (default) -->
    <template v-else>
      <span v-if="s.eyebrow" :style="eyebrowStyle">{{ s.eyebrow }}</span>
      <h2 v-if="s.heading" :style="headingStyle">{{ s.heading }}</h2>
      <p v-if="s.intro" :style="introStyle">{{ s.intro }}</p>
      <div :style="panelStyle">
        <div v-for="(it, i) in rows" :key="i" :style="rowStyle(i)">
          <div style="flex:1;min-width:0">
            <div :style="nameStyle">{{ it.name }}</div>
            <div v-if="it.note" :style="noteStyle">{{ it.note }}</div>
          </div>
          <div :style="priceStyle">{{ cur }}{{ it.price }}</div>
          <div style="display:inline-flex;align-items:center;gap:12px">
            <button type="button" :style="stepBtn" @click="dec(i)">−</button>
            <span :style="countStyle">{{ counts[i] }}</span>
            <button type="button" :style="stepBtn" @click="inc(i)">+</button>
          </div>
        </div>
        <div :style="footStyle">
          <div :style="{ fontFamily: SERIF }">
            <span :style="totLabelStyle">{{ s.total_label }} · {{ totalCount }} {{ s.count_label }}</span>
            <b :style="totalStyle">{{ cur }}{{ totalFmt }}</b>
          </div>
          <a v-if="s.cta_text" :href="s.cta_url || '#'" :style="ctaStyle">{{ s.cta_text }}</a>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = {
  eyebrow: 'Componi', heading: 'Crea la tua selezione', intro: '', currency: '€', cap: 0,
  items: [
    { name: 'Articolo A', price: '12', note: '', start: 0 },
    { name: 'Articolo B', price: '8', note: '', start: 0 },
    { name: 'Articolo C', price: '15', note: '', start: 0 },
  ],
  total_label: 'Totale', count_label: 'articoli', cta_text: 'Aggiungi al carrello', cta_url: '#',
  zone_accent: '', zone_on: '#ffffff', card_bg: '', card_border: '', align: 'left',
  layout: 'panel', heading_accent: '', heading_color: '', tally_bg: '', item_name_color: '', item_price_color: '',
};
const s = computed(() => ({ ...defaults, ...props.settings }));
const rows = computed(() => Array.isArray(s.value.items) ? s.value.items : []);
const cur = computed(() => s.value.currency || '');
const cap = computed(() => parseInt(s.value.cap) || 0);

const counts = ref([]);
function seed() { counts.value = rows.value.map(it => Math.max(0, parseInt(it.start) || 0)); }
seed();
watch(rows, seed);

const totalCount = computed(() => counts.value.reduce((a, b) => a + b, 0));
const totalFmt = computed(() => {
  let t = 0;
  rows.value.forEach((it, i) => { t += (counts.value[i] || 0) * (parseFloat(it.price) || 0); });
  return new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 }).format(t);
});
function inc(i) {
  if (cap.value !== 0 && totalCount.value >= cap.value) return;
  counts.value[i] = (counts.value[i] || 0) + 1;
}
function dec(i) { counts.value[i] = Math.max(0, (counts.value[i] || 0) - 1); }

const SERIF = "var(--olo-font-family-heading, 'Playfair Display',Georgia,serif)";
const SANS = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";
const DISP = "var(--olo-font-family-heading, 'Archivo',-apple-system,sans-serif)";
const accent = computed(() => s.value.zone_accent || 'var(--olo-color-primary, #e1474f)');
const on = computed(() => s.value.zone_on || '#ffffff');
const center = computed(() => s.value.align === 'center');
const cardbd = computed(() => s.value.card_border || 'var(--olo-color-border,#e5e7eb)');
const cardbg = computed(() => s.value.card_bg || 'var(--olo-color-surface-alt,#f6f7f9)');
const hcol = computed(() => s.value.heading_color || 'var(--olo-color-text,#111827)');
const tally = computed(() => s.value.tally_bg || 'var(--olo-color-text,#111827)');
const inm = computed(() => s.value.item_name_color || 'var(--olo-color-text,#111827)');
const ipr = computed(() => s.value.item_price_color || accent.value);

const rootStyle = computed(() => ({ fontFamily: SANS, textAlign: (s.value.layout !== 'split' && center.value) ? 'center' : 'left' }));

/* panel */
const eyebrowStyle = computed(() => ({ fontSize: '12px', fontWeight: 700, letterSpacing: '.16em', textTransform: 'uppercase', color: accent.value, display: 'block', marginBottom: '10px' }));
const headingStyle = computed(() => ({ fontFamily: SERIF, fontSize: '38px', lineHeight: 1.12, margin: 0, color: 'var(--olo-color-text,#111827)' }));
const introStyle = computed(() => ({ fontSize: '15.5px', lineHeight: 1.6, opacity: .8, margin: center.value ? '14px auto 0' : '14px 0 0', maxWidth: '560px' }));
const panelStyle = computed(() => ({ marginTop: '26px', background: cardbg.value, border: '1px solid ' + cardbd.value, borderRadius: '16px', padding: '32px', textAlign: 'left', maxWidth: center.value ? '640px' : 'none', marginLeft: center.value ? 'auto' : '0', marginRight: center.value ? 'auto' : '0' }));
function rowStyle(i) { return { display: 'flex', alignItems: 'center', gap: '16px', padding: '16px 0', borderTop: i === 0 ? '0' : '1px solid ' + cardbd.value }; }
const nameStyle = computed(() => ({ fontWeight: 600, fontSize: '15.5px', color: 'var(--olo-color-text,#111827)' }));
const noteStyle = computed(() => ({ fontSize: '13px', opacity: .6, marginTop: '2px' }));
const priceStyle = computed(() => ({ fontWeight: 600, fontSize: '14.5px', color: accent.value, whiteSpace: 'nowrap' }));
const stepBtn = computed(() => ({ width: '32px', height: '32px', borderRadius: '50%', border: '1px solid ' + cardbd.value, background: 'transparent', color: 'var(--olo-color-text,#111827)', fontSize: '18px', lineHeight: 1, cursor: 'pointer' }));
const countStyle = computed(() => ({ minWidth: '20px', textAlign: 'center', fontWeight: 700 }));
const footStyle = computed(() => ({ display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: '16px', flexWrap: 'wrap', marginTop: '22px', paddingTop: '20px', borderTop: '2px solid ' + cardbd.value }));
const totLabelStyle = computed(() => ({ fontSize: '12px', fontWeight: 700, letterSpacing: '.08em', textTransform: 'uppercase', opacity: .6, display: 'block', fontFamily: SANS }));
const totalStyle = computed(() => ({ fontSize: '34px', color: 'var(--olo-color-text,#111827)' }));
const ctaStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '8px', fontWeight: 600, fontSize: '14.5px', color: on.value, background: accent.value, padding: '13px 26px', borderRadius: '999px', textDecoration: 'none' }));

/* split */
const splitHeadStyle = { display: 'flex', justifyContent: 'space-between', alignItems: 'flex-end', gap: '28px', flexWrap: 'wrap', marginBottom: '26px' };
const splitEyebrowStyle = computed(() => ({ fontSize: '12px', fontWeight: 700, letterSpacing: '.16em', textTransform: 'uppercase', color: accent.value, display: 'block', marginBottom: '10px' }));
const splitHStyle = computed(() => ({ fontFamily: DISP, fontWeight: 800, fontSize: 'clamp(32px,4.6vw,56px)', lineHeight: 1.02, letterSpacing: '-.01em', textTransform: 'uppercase', margin: 0, color: hcol.value }));
const splitIntroStyle = computed(() => ({ color: 'var(--olo-color-text-muted,#6b7280)', fontSize: '15.5px', lineHeight: 1.6, margin: '10px 0 0', maxWidth: '440px' }));
const tallyStyle = computed(() => ({ display: 'flex', alignItems: 'center', gap: '22px', background: tally.value, color: accent.value, borderRadius: '10px', padding: '14px 24px', whiteSpace: 'nowrap' }));
const tallyNumStyle = { fontFamily: DISP, fontWeight: 800, fontSize: '28px', lineHeight: 1, color: '#fff' };
const tallyLblStyle = { fontWeight: 600, fontSize: '13px', opacity: .75 };
const tallyTotStyle = computed(() => ({ fontFamily: DISP, fontWeight: 800, fontSize: '28px', lineHeight: 1, borderLeft: '1px solid color-mix(in srgb, ' + accent.value + ' 30%, transparent)', paddingLeft: '22px' }));
const splitGridStyle = { display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '14px' };
function splitItemStyle(i) {
  const on = (counts.value[i] || 0) > 0;
  return { display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: '16px', background: cardbg.value, border: '1px solid ' + (on ? accent.value : cardbd.value), borderRadius: '10px', padding: '16px 20px', boxShadow: on ? '0 12px 28px -18px rgba(0,0,0,.4)' : 'none' };
}
const splitNameStyle = computed(() => ({ fontFamily: DISP, fontWeight: 700, fontSize: '18px', textTransform: 'uppercase', letterSpacing: '.01em', color: inm.value, margin: 0 }));
const splitNoteStyle = { fontSize: '12px', color: 'var(--olo-color-text-muted,#8a948d)', marginTop: '2px' };
const splitPriceStyle = computed(() => ({ fontFamily: DISP, fontWeight: 700, fontSize: '18px', color: ipr.value }));
const splitStepWrap = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '8px', border: '1.5px solid ' + cardbd.value, borderRadius: '8px', padding: '4px 6px' }));
const splitStepBtn = computed(() => ({ width: '28px', height: '28px', border: 0, background: 'transparent', color: inm.value, fontSize: '18px', lineHeight: 1, cursor: 'pointer', borderRadius: '6px' }));
const splitCountStyle = computed(() => ({ minWidth: '18px', textAlign: 'center', fontWeight: 700, color: inm.value }));
</script>
