<template>
  <div class="olo-lbmix" :style="panelStyle">
    <div class="olo-lbmix__slots">
      <div v-for="(grp, gi) in groups" :key="gi" class="olo-lbmix__slot" :style="slotStyle">
        <span class="olo-lbmix__sw" :style="swStyle(grp.options[idx[gi] % grp.options.length])"></span>
        <div class="olo-lbmix__meta">
          <span class="olo-lbmix__step" :style="stepStyle">{{ grp.step }}</span>
          <div class="olo-lbmix__nm">
            <span :style="nameStyle">{{ cur(grp, gi).name }}</span>
            <span :style="priceStyle">{{ currency }}{{ cur(grp, gi).price }}</span>
          </div>
        </div>
        <div class="olo-lbmix__nav">
          <button type="button" :style="navBtn" @click="step(gi, -1)">‹</button>
          <button type="button" :style="navBtn" @click="step(gi, 1)">›</button>
        </div>
      </div>
    </div>
    <div class="olo-lbmix__card" :style="cardStyle">
      <span :style="capStyle">{{ s.card_title }} · {{ groups.length }} {{ s.card_steps_label }}</span>
      <div :style="totalStyle">{{ currency }}{{ total }}</div>
      <span :style="subStyle">{{ s.card_sub }}</span>
      <a :href="s.cta_url || '#'" :style="ctaStyle">{{ s.cta_text }}</a>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  items: [
    { step: 'Cleanse', name: 'Rosewater Gel', price: '24', color: '#f4c9d4' },
    { step: 'Cleanse', name: 'Clay Melt Balm', price: '29', color: '#e3b778' },
    { step: 'Treat', name: 'Vitamin C Drops', price: '38', color: '#e3b778' },
    { step: 'Treat', name: 'Niacinamide 10%', price: '32', color: '#e7a0b4' },
    { step: 'Hydrate', name: 'Ceramide Cream', price: '34', color: '#f4c9d4' },
    { step: 'Protect', name: 'Sheer SPF 50', price: '30', color: '#f6e9ec' },
  ],
  currency: '€', card_title: 'Your routine', card_steps_label: 'steps',
  card_sub: 'Built in four taps. Swap any step until it’s yours.', cta_text: 'Add routine to bag', cta_url: '#',
  panel_bg: '#4d2f40', slot_bg: '#432838', accent: '#e7a0b4', accent_ink: '#23131d',
  name_color: '#f6e9ec', price_color: '#9c7e8c', line_color: 'rgba(246,233,236,.13)',
  name_font_family: 'heading', mono_font_family: '',
};

const HEADING = "var(--olo-font-family-heading, 'DM Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
const BODY = "var(--olo-font-family, 'Inter',-apple-system,sans-serif)";
const MONO_FB = "ui-monospace,'SF Mono',Menlo,Consolas,monospace";

const s = computed(() => ({ ...defaults, ...props.settings }));
const currency = computed(() => s.value.currency || '');
const mono = computed(() => {
  const n = String(s.value.mono_font_family || '').replace(/[^A-Za-z0-9 \-]/g, '').trim();
  return n ? `'${n}',${MONO_FB}` : MONO_FB;
});
const nfam = computed(() => s.value.name_font_family === 'body' ? BODY : HEADING);

const groups = computed(() => {
  const map = {}, order = [];
  (Array.isArray(s.value.items) ? s.value.items : []).forEach((it) => {
    const st = it.step || '';
    if (!map[st]) { map[st] = { step: st, options: [] }; order.push(st); }
    map[st].options.push(it);
  });
  return order.map((st) => map[st]);
});

const idx = ref([]);
watch(groups, (g) => { idx.value = g.map(() => 0); }, { immediate: true });

function step(gi, dir) {
  const n = groups.value[gi].options.length;
  idx.value[gi] = ((idx.value[gi] + dir) % n + n) % n;
}
function cur(grp, gi) { return grp.options[(idx.value[gi] || 0) % grp.options.length] || { name: '', price: '0' }; }
const total = computed(() => groups.value.reduce((sum, grp, gi) => sum + parseFloat(cur(grp, gi).price || 0), 0));

const panelStyle = computed(() => ({ display: 'grid', gridTemplateColumns: '1.12fr .88fr', gap: '40px', alignItems: 'center', border: '1px solid ' + s.value.line_color, borderRadius: '24px', background: s.value.panel_bg, padding: '36px' }));
const slotStyle = computed(() => ({ display: 'flex', alignItems: 'center', gap: '16px', background: s.value.slot_bg, border: '1px solid ' + s.value.line_color, borderRadius: '14px', padding: '13px 16px', marginBottom: '12px' }));
function swStyle(o) { return { width: '46px', height: '46px', borderRadius: '50%', flex: 'none', background: (o && o.color) || '#999', boxShadow: 'inset 0 0 0 1.5px rgba(246,233,236,.3)' }; }
const stepStyle = computed(() => ({ fontFamily: mono.value, fontWeight: 700, fontSize: '10.5px', letterSpacing: '.14em', textTransform: 'uppercase', color: s.value.accent }));
const nameStyle = computed(() => ({ fontFamily: nfam.value, fontSize: '21px', color: s.value.name_color, lineHeight: 1.08 }));
const priceStyle = computed(() => ({ fontFamily: mono.value, fontWeight: 700, fontSize: '14px', color: s.value.price_color }));
const navBtn = computed(() => ({ width: '40px', height: '40px', borderRadius: '50%', border: '1px solid ' + s.value.accent, background: 'transparent', color: s.value.name_color, cursor: 'pointer', fontSize: '20px', lineHeight: 1 }));
const cardStyle = computed(() => ({ textAlign: 'center', border: '1px solid ' + s.value.accent, borderRadius: '20px', padding: '40px', background: 'linear-gradient(150deg,' + s.value.accent + '28,' + s.value.accent + '0a)' }));
const capStyle = computed(() => ({ fontFamily: mono.value, fontWeight: 700, fontSize: '11px', letterSpacing: '.12em', textTransform: 'uppercase', color: s.value.accent }));
const totalStyle = computed(() => ({ fontFamily: nfam.value, fontSize: '60px', color: s.value.name_color, lineHeight: 1, margin: '12px 0 10px' }));
const subStyle = computed(() => ({ display: 'block', fontSize: '14px', color: s.value.price_color, marginBottom: '22px', lineHeight: 1.55 }));
const ctaStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', justifyContent: 'center', padding: '14px 26px', borderRadius: '999px', background: s.value.accent, color: s.value.accent_ink, fontFamily: BODY, fontWeight: 600, fontSize: '14px', textDecoration: 'none' }));
</script>

<style scoped>
.olo-lbmix__meta { flex: 1; min-width: 0; }
.olo-lbmix__nm { display: flex; align-items: baseline; justify-content: space-between; gap: 12px; margin-top: 2px; }
.olo-lbmix__nav { display: flex; gap: 6px; flex: none; }
.olo-lbmix__slots { display: flex; flex-direction: column; }
@media (max-width: 860px) { .olo-lbmix { grid-template-columns: 1fr !important; gap: 28px !important; } }
</style>
