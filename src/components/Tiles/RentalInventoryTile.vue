<template>
  <div class="mb-font-sans">
    <div v-if="s.filter_bar" :style="{ display:'flex', gap:'8px', marginBottom: s.gap+'px', flexWrap:'wrap' }">
      <span v-for="cat in categories" :key="cat.label"
            :style="{ padding:'8px 18px', borderRadius:'20px', fontSize:'13px', fontWeight:'600', cursor:'pointer',
                       background: cat.active ? accent : '#F3F4F6', color: cat.active ? '#fff' : '#374151' }">
        {{ cat.label }}
      </span>
    </div>
    <div :style="gridStyle">
      <div v-for="(v, i) in vehicles" :key="i" :style="cardStyle">
        <div v-if="s.show_image" :style="{ position:'relative', height: s.layout === 'horizontal' ? '100%' : '160px', minHeight:'160px', background: v.gradient, borderRadius: s.layout === 'horizontal' ? s.card_border_radius+'px 0 0 '+s.card_border_radius+'px' : s.card_border_radius+'px '+s.card_border_radius+'px 0 0' }">
          <span :style="rateBadgeStyle">{{ v.rate }}</span>
        </div>
        <div :style="{ padding:'16px', flex:'1' }">
          <div v-if="s.show_title" :style="{ fontSize: s.title_size+'px', fontWeight: s.title_weight, color:'#1F2937', marginBottom:'6px' }">{{ v.title }}</div>
          <div v-if="s.show_specs" :style="{ display:'flex', gap:'6px', flexWrap:'wrap', marginBottom:'12px' }">
            <span v-for="spec in v.specs" :key="spec" :style="specPillStyle">{{ spec }}</span>
          </div>
          <div v-if="s.show_price || s.show_btn" :style="{ display:'flex', justifyContent:'space-between', alignItems:'center' }">
            <span v-if="s.show_price" :style="{ fontSize:'18px', fontWeight:'700', color: priceColor }">{{ v.price }}</span>
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
const defaults = { columns: 3, gap: 24, card_border_radius: 12, card_shadow: 'sm', hover_effect: 'none', show_image: true, show_title: true, title_size: 16, title_weight: '700', show_specs: true, show_price: true, show_btn: true, btn_text: 'Prenota', btn_bg: '', btn_color: '#fff', btn_radius: 8, layout: 'vertical', filter_bar: true, accent_color: '', price_color: '' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const accent = computed(() => s.value.accent_color || 'var(--olo-color-primary, #6366F1)');
const priceColor = computed(() => s.value.price_color || accent.value);
const shadowMap = { none:'none', sm:'0 1px 3px rgba(0,0,0,0.1)', md:'0 4px 12px rgba(0,0,0,0.1)', lg:'0 8px 24px rgba(0,0,0,0.15)' };
const categories = [ { label:'Tutti', active:true }, { label:'SUV', active:false }, { label:'Berlina', active:false }, { label:'City', active:false } ];
const vehicles = [
  { title:'BMW X1 sDrive18d', specs:['2023','GN 432 HT','42.000 km'], price:'\u20AC 55/giorno', rate:'\u20AC 55/g', gradient:'linear-gradient(135deg,#3B82F640,#3B82F690)' },
  { title:'Fiat 500 Hybrid', specs:['2024','FT 101 AB','8.500 km'], price:'\u20AC 35/giorno', rate:'\u20AC 35/g', gradient:'linear-gradient(135deg,#EF444440,#EF444490)' },
  { title:'Jeep Renegade 4xe', specs:['2022','TN 888 KL','61.000 km'], price:'\u20AC 65/giorno', rate:'\u20AC 65/g', gradient:'linear-gradient(135deg,#10B98140,#10B98190)' },
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
const rateBadgeStyle = computed(() => ({
  position:'absolute', top:'12px', right:'12px', background: accent.value, color:'#fff',
  padding:'4px 10px', borderRadius:'6px', fontSize:'11px', fontWeight:'700',
}));
const specPillStyle = { padding:'3px 10px', borderRadius:'12px', fontSize:'11px', fontWeight:'500', background:'#F3F4F6', color:'#6B7280' };
const btnStyle = computed(() => ({ padding:'8px 16px', background: s.value.btn_bg || accent.value, color: s.value.btn_color, borderRadius: s.value.btn_radius+'px', fontSize:'13px', fontWeight:'600', cursor:'pointer' }));
</script>
