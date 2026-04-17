<template>
  <div class="mb-font-sans" :style="{ position:'relative' }">
    <div :style="scrollStyle">
      <div v-for="(p, i) in properties" :key="i" :style="cardStyle">
        <div :style="{ position:'relative', height:'170px', background: p.gradient, borderRadius: s.card_radius+'px '+s.card_radius+'px 0 0' }">
          <span :style="badgeStyle">{{ p.badge }}</span>
        </div>
        <div :style="{ padding:'16px' }">
          <div :style="{ fontSize:'15px', fontWeight:'700', color:'#1F2937', marginBottom:'4px' }">{{ p.title }}</div>
          <div :style="{ fontSize:'12px', color:'#6B7280', marginBottom:'8px' }">{{ p.location }}</div>
          <div :style="{ fontSize:'17px', fontWeight:'700', color: accent }">{{ p.price }}</div>
        </div>
      </div>
    </div>
    <div :style="arrowStyle('-16px', null)">&#8249;</div>
    <div :style="arrowStyle(null, '-16px')">&#8250;</div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { card_radius: 12, card_width: 300, gap: 20, accent_color: '' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const accent = computed(() => s.value.accent_color || 'var(--olo-color-primary, #6366F1)');
const properties = [
  { title:'Attico Luminoso', location:'Trento, Centro', price:'\u20AC 410.000', badge:'Vendita', gradient:'linear-gradient(135deg,#818CF840,#818CF890)' },
  { title:'Bilocale Arredato', location:'Rovereto, Borgo', price:'\u20AC 750/mese', badge:'Affitto', gradient:'linear-gradient(135deg,#34D39940,#34D39990)' },
  { title:'Villetta a Schiera', location:'Lavis, Residenziale', price:'\u20AC 290.000', badge:'Vendita', gradient:'linear-gradient(135deg,#FB923C40,#FB923C90)' },
];
const scrollStyle = computed(() => ({ display:'flex', gap: s.value.gap+'px', overflow:'hidden' }));
const cardStyle = computed(() => ({ minWidth: s.value.card_width+'px', flex:'0 0 '+s.value.card_width+'px', background:'#fff', borderRadius: s.value.card_radius+'px', border:'1px solid #E5E7EB', boxShadow:'0 2px 8px rgba(0,0,0,0.08)', overflow:'hidden' }));
const badgeStyle = { position:'absolute', top:'10px', left:'10px', background:'rgba(30,41,59,0.75)', color:'#fff', padding:'4px 10px', borderRadius:'6px', fontSize:'11px', fontWeight:'600' };
function arrowStyle(left, right) {
  const pos = {};
  if (left) pos.left = left;
  if (right) pos.right = right;
  return { ...pos, position:'absolute', top:'50%', transform:'translateY(-50%)', width:'36px', height:'36px', borderRadius:'50%', background:'#fff', boxShadow:'0 2px 8px rgba(0,0,0,0.15)', display:'flex', alignItems:'center', justifyContent:'center', fontSize:'20px', color:'#374151', cursor:'pointer', zIndex:2 };
}
</script>
