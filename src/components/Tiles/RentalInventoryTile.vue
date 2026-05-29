<template>
  <div class="mb-font-sans">
    <div v-if="s.filter_bar" :style="{ display:'flex', gap:'8px', marginBottom: s.gap+'px', flexWrap:'wrap' }">
      <button v-for="cat in categories" :key="cat.label" type="button" class="olo-rin-filter"
            :style="filterStyle(cat.active)">
        {{ cat.label }}
      </button>
    </div>
    <div :style="gridStyle">
      <div v-for="(v, i) in vehicles" :key="i" :style="cardStyle">
        <div v-if="s.show_image" :style="{ position:'relative', height: s.layout === 'horizontal' ? '100%' : '160px', minHeight:'160px', background: placeholders[i % placeholders.length], borderRadius: s.layout === 'horizontal' ? s.card_border_radius+'px 0 0 '+s.card_border_radius+'px' : s.card_border_radius+'px '+s.card_border_radius+'px 0 0' }">
          <span :style="rateBadgeStyle">{{ v.rate }}</span>
        </div>
        <div :style="{ padding:'16px', flex:'1' }">
          <div v-if="s.show_title" :style="{ fontSize: s.title_size+'px', fontWeight: s.title_weight, color: TOKENS.text, marginBottom:'6px' }">{{ v.title }}</div>
          <div v-if="s.show_specs" :style="{ display:'flex', gap:'6px', flexWrap:'wrap', marginBottom:'12px' }">
            <span v-for="spec in v.specs" :key="spec" :style="specPillStyle">{{ spec }}</span>
          </div>
          <div v-if="s.show_price || s.show_btn" :style="{ display:'flex', justifyContent:'space-between', alignItems:'center' }">
            <span v-if="s.show_price" :style="{ fontSize:'18px', fontWeight:'700', color: priceColor }">{{ v.price }}</span>
            <button v-if="s.show_btn" type="button" class="olo-rin-btn" :style="btnStyle">{{ s.btn_text }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { columns: 3, gap: 24, card_border_radius: 12, card_shadow: 'sm', hover_effect: 'none', show_image: true, show_title: true, title_size: 16, title_weight: '700', show_specs: true, show_price: true, show_btn: true, btn_text: 'Prenota', btn_bg: '', btn_color: '', btn_radius: 8, layout: 'vertical', filter_bar: true, accent_color: '', price_color: '' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const accent = computed(() => resolveColor(s.value.accent_color, TOKENS.primary));
const priceColor = computed(() => resolveColor(s.value.price_color, accent.value));
const shadowMap = { none:'none', sm:'0 1px 3px rgba(0,0,0,0.1)', md:'0 4px 12px rgba(0,0,0,0.1)', lg:'0 8px 24px rgba(0,0,0,0.15)' };
const categories = [ { label:'Tutti', active:true }, { label:'SUV', active:false }, { label:'Berlina', active:false }, { label:'City', active:false } ];
// placeholder media elegante: tinte soft brand su superficie (no pi\u00F9 gradienti off-brand)
const placeholders = [
  'linear-gradient(135deg, color-mix(in srgb, var(--olo-color-primary, #e1474f) 18%, #fff), color-mix(in srgb, var(--olo-color-primary, #e1474f) 38%, #fff))',
  'linear-gradient(135deg, color-mix(in srgb, var(--olo-color-accent, #f4a23b) 18%, #fff), color-mix(in srgb, var(--olo-color-accent, #f4a23b) 38%, #fff))',
  'linear-gradient(135deg, color-mix(in srgb, var(--olo-color-secondary, #16263d) 14%, #fff), color-mix(in srgb, var(--olo-color-secondary, #16263d) 30%, #fff))',
];
const vehicles = [
  { title:'BMW X1 sDrive18d', specs:['2023','GN 432 HT','42.000 km'], price:'\u20AC 55/giorno', rate:'\u20AC 55/g' },
  { title:'Fiat 500 Hybrid', specs:['2024','FT 101 AB','8.500 km'], price:'\u20AC 35/giorno', rate:'\u20AC 35/g' },
  { title:'Jeep Renegade 4xe', specs:['2022','TN 888 KL','61.000 km'], price:'\u20AC 65/giorno', rate:'\u20AC 65/g' },
];
const gridStyle = computed(() => {
  if (s.value.layout === 'horizontal') return { display:'flex', flexDirection:'column', gap: s.value.gap+'px' };
  return { display:'grid', gridTemplateColumns:'repeat('+s.value.columns+',1fr)', gap: s.value.gap+'px' };
});
const cardStyle = computed(() => ({
  background: TOKENS.surface, borderRadius: s.value.card_border_radius+'px', border:'1px solid '+TOKENS.border,
  boxShadow: shadowMap[s.value.card_shadow]||shadowMap.sm, overflow:'hidden',
  display: s.value.layout === 'horizontal' ? 'flex' : 'block',
}));
const rateBadgeStyle = computed(() => ({
  position:'absolute', top:'12px', right:'12px', background: accent.value, color: TOKENS.onPrimary,
  padding:'4px 10px', borderRadius:'6px', fontSize:'11px', fontWeight:'700',
}));
const specPillStyle = { padding:'3px 10px', borderRadius:'12px', fontSize:'11px', fontWeight:'500', background: TOKENS.surfaceAlt, color: TOKENS.textSoft };
const filterStyle = (active) => ({
  padding:'8px 18px', borderRadius:'20px', fontSize:'13px', fontWeight:'600', cursor:'pointer',
  border:'none', fontFamily:'inherit',
  background: active ? accent.value : TOKENS.surfaceAlt,
  color: active ? TOKENS.onPrimary : TOKENS.text,
});
const btnStyle = computed(() => ({ padding:'8px 16px', background: resolveColor(s.value.btn_bg, accent.value), color: resolveColor(s.value.btn_color, TOKENS.onPrimary), borderRadius: s.value.btn_radius+'px', border:'none', fontFamily:'inherit', fontSize:'13px', fontWeight:'600', cursor:'pointer' }));
</script>

<style scoped>
.olo-rin-filter:focus-visible,
.olo-rin-btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
</style>
