<template>
  <div class="mb-font-sans">
    <div :style="gridStyle">
      <div v-for="(svc, i) in services" :key="i" :style="cardStyle">
        <div v-if="s.show_image" :style="{ height:'140px', background: placeholderBg, borderRadius: s.layout === 'horizontal' ? s.card_border_radius+'px 0 0 '+s.card_border_radius+'px' : s.card_border_radius+'px '+s.card_border_radius+'px 0 0' }"></div>
        <div :style="{ padding:'16px', flex:'1' }">
          <div v-if="s.show_title" :style="{ fontSize: s.title_size+'px', fontWeight:'700', color: TOKENS.text, marginBottom:'6px' }">{{ svc.title }}</div>
          <div v-if="s.show_duration" :style="durationPillStyle">{{ svc.duration }}</div>
          <div :style="{ display:'flex', justifyContent:'space-between', alignItems:'center', marginTop:'12px' }">
            <span v-if="s.show_price" :style="{ fontSize:'18px', fontWeight:'700', color: accent }">{{ svc.price }}</span>
            <button v-if="s.show_btn" type="button" class="olo-apg-btn" :style="btnStyle">{{ s.btn_text }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { resolveColor, TOKENS, SHADOW } from '@/composables/oloTileDefaults';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { columns: 3, gap: 24, card_border_radius: 12, card_shadow: 'sm', show_image: true, show_title: true, title_size: 16, show_duration: true, show_price: true, show_btn: true, btn_text: 'Prenota', btn_bg: '', btn_color: '', layout: 'vertical', accent_color: '' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const accent = computed(() => resolveColor(s.value.accent_color, TOKENS.primary));
const shadowMap = SHADOW;
// placeholder media elegante: tinta soft brand su superficie (no pi\u00F9 gradienti off-brand)
const placeholderBg = 'linear-gradient(135deg, color-mix(in srgb, var(--olo-color-primary, #e1474f) 14%, #fff), color-mix(in srgb, var(--olo-color-primary, #e1474f) 28%, #fff))';
const services = [
  { title:'Taglio Uomo', duration:'30 min', price:'\u20AC 18' },
  { title:'Colore e Piega', duration:'90 min', price:'\u20AC 65' },
  { title:'Massaggio Rilassante', duration:'60 min', price:'\u20AC 50' },
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
const durationPillStyle = { display:'inline-block', padding:'3px 10px', borderRadius:'12px', fontSize:'12px', fontWeight:'500', background: TOKENS.surfaceAlt, color: TOKENS.textSoft };
const btnStyle = computed(() => ({ padding:'8px 16px', background: resolveColor(s.value.btn_bg, accent.value), color: resolveColor(s.value.btn_color, TOKENS.onPrimary), borderRadius:'8px', border:'none', fontFamily:'inherit', fontSize:'13px', fontWeight:'600', cursor:'pointer' }));
</script>

<style scoped>
.olo-apg-btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
</style>
