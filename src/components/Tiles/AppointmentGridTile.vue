<template>
  <div class="mb-font-sans">
    <div :style="gridStyle">
      <div v-for="(svc, i) in services" :key="i" :style="cardStyle">
        <div v-if="s.show_image" :style="{ height:'140px', background: svc.gradient, borderRadius: s.layout === 'horizontal' ? s.card_border_radius+'px 0 0 '+s.card_border_radius+'px' : s.card_border_radius+'px '+s.card_border_radius+'px 0 0' }"></div>
        <div :style="{ padding:'16px', flex:'1' }">
          <div v-if="s.show_title" :style="{ fontSize: s.title_size+'px', fontWeight:'700', color:'#1F2937', marginBottom:'6px' }">{{ svc.title }}</div>
          <div v-if="s.show_duration" :style="durationPillStyle">{{ svc.duration }}</div>
          <div :style="{ display:'flex', justifyContent:'space-between', alignItems:'center', marginTop:'12px' }">
            <span v-if="s.show_price" :style="{ fontSize:'18px', fontWeight:'700', color: accent }">{{ svc.price }}</span>
            <span v-if="s.show_btn" :style="btnStyle">{{ s.btn_text }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { columns: 3, gap: 24, card_border_radius: 12, card_shadow: 'sm', show_image: true, show_title: true, title_size: 16, show_duration: true, show_price: true, show_btn: true, btn_text: 'Prenota', btn_bg: '', btn_color: '#fff', layout: 'vertical', accent_color: '' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const accent = computed(() => s.value.accent_color || 'var(--olo-color-primary, #6366F1)');
const shadowMap = { none:'none', sm:'0 1px 3px rgba(0,0,0,0.1)', md:'0 4px 12px rgba(0,0,0,0.1)', lg:'0 8px 24px rgba(0,0,0,0.15)' };
const services = [
  { title:'Taglio Uomo', duration:'30 min', price:'\u20AC 18', gradient:'linear-gradient(135deg,#3B82F640,#3B82F690)' },
  { title:'Colore e Piega', duration:'90 min', price:'\u20AC 65', gradient:'linear-gradient(135deg,#EC489940,#EC489990)' },
  { title:'Massaggio Rilassante', duration:'60 min', price:'\u20AC 50', gradient:'linear-gradient(135deg,#10B98140,#10B98190)' },
];
const gridStyle = computed(() => {
  if (s.value.layout === 'horizontal') return { display:'flex', flexDirection:'column', gap: s.value.gap+'px' };
  return { display:'grid', gridTemplateColumns:'repeat('+s.value.columns+',1fr)', gap: s.value.gap+'px' };
});
const cardStyle = computed(() => ({
  background:'#fff', borderRadius: s.value.card_border_radius+'px', border:'1px solid #E5E7EB',
  boxShadow: shadowMap[s.value.card_shadow]||shadowMap.sm, overflow:'hidden',
  display: s.value.layout === 'horizontal' ? 'flex' : 'block',
}));
const durationPillStyle = { display:'inline-block', padding:'3px 10px', borderRadius:'12px', fontSize:'12px', fontWeight:'500', background:'#F3F4F6', color:'#6B7280' };
const btnStyle = computed(() => ({ padding:'8px 16px', background: s.value.btn_bg || accent.value, color: s.value.btn_color, borderRadius:'8px', fontSize:'13px', fontWeight:'600', cursor:'pointer' }));
</script>
