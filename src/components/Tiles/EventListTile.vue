<template>
  <div class="mb-font-sans">
    <div v-if="s.filter_bar" :style="{ display:'flex', gap:'8px', marginBottom: s.gap+'px', flexWrap:'wrap' }">
      <button v-for="cat in categories" :key="cat.label" type="button" class="olo-evl-filter"
            :style="filterStyle(cat.active)">
        {{ cat.label }}
      </button>
    </div>
    <div :style="gridStyle">
      <div v-for="(ev, i) in events" :key="i" :style="cardStyle">
        <div :style="{ position:'relative', height: imgHeight, background: placeholders[i % placeholders.length], borderRadius: s.layout === 'horizontal' ? s.card_border_radius+'px 0 0 '+s.card_border_radius+'px' : s.card_border_radius+'px '+s.card_border_radius+'px 0 0' }">
          <div v-if="s.show_date_badge" :style="dateBadgeStyle">
            <div :style="{ fontSize:'20px', fontWeight:'800', lineHeight:'1' }">{{ ev.day }}</div>
            <div :style="{ fontSize:'11px', fontWeight:'600', textTransform:'uppercase' }">{{ ev.month }}</div>
          </div>
        </div>
        <div :style="{ padding:'16px', flex:'1' }">
          <div :style="{ fontSize:'16px', fontWeight:'700', color: TOKENS.text, marginBottom:'4px' }">{{ ev.title }}</div>
          <div v-if="s.show_venue" :style="{ fontSize:'13px', color: TOKENS.textSoft, marginBottom:'10px' }">{{ ev.venue }}</div>
          <div :style="{ display:'flex', justifyContent:'space-between', alignItems:'center' }">
            <span v-if="s.show_price" :style="{ fontSize:'17px', fontWeight:'700', color: accent }">{{ ev.price }}</span>
            <button v-if="s.show_btn" type="button" class="olo-evl-btn" :style="btnStyle">{{ s.btn_text }}</button>
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
const defaults = { columns: 3, gap: 24, card_border_radius: 12, card_shadow: 'sm', show_date_badge: true, show_venue: true, show_price: true, show_btn: true, btn_text: 'Biglietti', btn_bg: '', btn_color: '', image_ratio: '56%', layout: 'vertical', filter_bar: true, accent_color: '' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const accent = computed(() => resolveColor(s.value.accent_color, TOKENS.primary));
const shadowMap = { none:'none', sm:'0 1px 3px rgba(0,0,0,0.1)', md:'0 4px 12px rgba(0,0,0,0.1)', lg:'0 8px 24px rgba(0,0,0,0.15)' };
const categories = [ { label:'Tutti', active:true }, { label:'Musica', active:false }, { label:'Workshop', active:false }, { label:'Food', active:false } ];
// placeholder media elegante: tinte soft brand su superficie (no pi\u00F9 gradienti off-brand)
const placeholders = [
  'linear-gradient(135deg, color-mix(in srgb, var(--olo-color-primary, #e1474f) 18%, #fff), color-mix(in srgb, var(--olo-color-primary, #e1474f) 38%, #fff))',
  'linear-gradient(135deg, color-mix(in srgb, var(--olo-color-accent, #f4a23b) 18%, #fff), color-mix(in srgb, var(--olo-color-accent, #f4a23b) 38%, #fff))',
  'linear-gradient(135deg, color-mix(in srgb, var(--olo-color-secondary, #16263d) 14%, #fff), color-mix(in srgb, var(--olo-color-secondary, #16263d) 30%, #fff))',
];
const events = [
  { title:'Festival Jazz', venue:'Piazza Duomo, Trento', price:'\u20AC 25', day:'14', month:'Giu' },
  { title:'Workshop Ceramica', venue:'Spazio Arte, Rovereto', price:'\u20AC 40', day:'22', month:'Giu' },
  { title:'Degustazione Vini', venue:'Cantina Sociale, Mezzocorona', price:'\u20AC 30', day:'05', month:'Lug' },
];
const imgHeight = computed(() => s.value.layout === 'horizontal' ? '100%' : s.value.image_ratio);
const gridStyle = computed(() => {
  if (s.value.layout === 'horizontal') return { display:'flex', flexDirection:'column', gap: s.value.gap+'px' };
  return { display:'grid', gridTemplateColumns:'repeat('+s.value.columns+',1fr)', gap: s.value.gap+'px' };
});
const cardStyle = computed(() => ({
  background: TOKENS.surface, borderRadius: s.value.card_border_radius+'px', border:'1px solid '+TOKENS.border,
  boxShadow: shadowMap[s.value.card_shadow]||shadowMap.sm, overflow:'hidden',
  display: s.value.layout === 'horizontal' ? 'flex' : 'block',
  minHeight: s.value.layout === 'horizontal' ? '140px' : 'auto',
}));
const dateBadgeStyle = computed(() => ({
  position:'absolute', top:'12px', left:'12px', background: TOKENS.surface, color: accent.value,
  padding:'6px 10px', borderRadius:'8px', textAlign:'center', lineHeight:'1.2',
  boxShadow:'0 2px 6px rgba(0,0,0,0.15)',
}));
const filterStyle = (active) => ({
  padding:'8px 18px', borderRadius:'20px', fontSize:'13px', fontWeight:'600', cursor:'pointer',
  border:'none', fontFamily:'inherit',
  background: active ? accent.value : TOKENS.surfaceAlt,
  color: active ? TOKENS.onPrimary : TOKENS.text,
});
const btnStyle = computed(() => ({ padding:'8px 16px', background: resolveColor(s.value.btn_bg, accent.value), color: resolveColor(s.value.btn_color, TOKENS.onPrimary), borderRadius:'8px', border:'none', fontFamily:'inherit', fontSize:'13px', fontWeight:'600', cursor:'pointer' }));
</script>

<style scoped>
.olo-evl-filter:focus-visible,
.olo-evl-btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
</style>
