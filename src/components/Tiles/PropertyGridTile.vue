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
      <div v-for="(p, i) in properties" :key="i" :style="cardStyle">
        <div :style="{ position:'relative', height:'180px', background: p.gradient, borderRadius: s.card_radius+'px '+s.card_radius+'px 0 0' }">
          <span :style="badgeStyle(p.badge)">{{ p.badge }}</span>
        </div>
        <div :style="{ padding:'16px' }">
          <div :style="{ fontSize:'16px', fontWeight:'700', color:'#1F2937', marginBottom:'4px' }">{{ p.title }}</div>
          <div :style="{ fontSize:'13px', color:'#6B7280', marginBottom:'10px' }">{{ p.location }}</div>
          <div :style="{ display:'flex', gap:'12px', fontSize:'12px', color:'#6B7280', marginBottom:'12px' }">
            <span>{{ p.mq }} m&sup2;</span><span>{{ p.locali }} locali</span><span>{{ p.camere }} cam</span><span>{{ p.bagni }} bagni</span>
          </div>
          <div :style="{ display:'flex', justifyContent:'space-between', alignItems:'center' }">
            <span :style="{ fontSize:'18px', fontWeight:'700', color: accent }">{{ p.price }}</span>
            <span :style="btnStyle">{{ t('Dettagli') }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { columns: 3, gap: 24, card_radius: 12, card_shadow: 'sm', filter_bar: true, accent_color: '', btn_bg: '', btn_color: '#fff', btn_radius: 8 };
const s = computed(() => ({ ...defaults, ...props.settings }));
const accent = computed(() => s.value.accent_color || 'var(--olo-color-primary, #6366F1)');
const shadowMap = { none:'none', sm:'0 1px 3px rgba(0,0,0,0.1)', md:'0 4px 12px rgba(0,0,0,0.1)', lg:'0 8px 24px rgba(0,0,0,0.15)' };
const categories = [ { label:'Tutti', active:true }, { label:'Appartamenti', active:false }, { label:'Ville', active:false }, { label:'Commerciale', active:false } ];
const properties = [
  { title:'Trilocale Centro Storico', location:'Trento, Centro', mq:95, locali:4, camere:2, bagni:1, price:'\u20AC 325.000', badge:'Vendita', gradient:'linear-gradient(135deg,#6366F140,#6366F190)' },
  { title:'Bilocale con Terrazzo', location:'Rovereto, San Giorgio', mq:65, locali:3, camere:1, bagni:1, price:'\u20AC 850/mese', badge:'Affitto', gradient:'linear-gradient(135deg,#10B98140,#10B98190)' },
  { title:'Villa Panoramica', location:'Pergine, Lago', mq:220, locali:8, camere:4, bagni:3, price:'\u20AC 520.000', badge:'Vendita', gradient:'linear-gradient(135deg,#F5920040,#F5920090)' },
];
const gridStyle = computed(() => ({ display:'grid', gridTemplateColumns:'repeat('+s.value.columns+',1fr)', gap: s.value.gap+'px' }));
const cardStyle = computed(() => ({ background:'#fff', borderRadius: s.value.card_radius+'px', border:'1px solid #E5E7EB', boxShadow: shadowMap[s.value.card_shadow]||shadowMap.sm, overflow:'hidden' }));
function badgeStyle(label) {
  const bg = label === 'Affitto' ? '#10B981' : '#6366F1';
  return { position:'absolute', top:'12px', left:'12px', background: bg, color:'#fff', padding:'4px 12px', borderRadius:'6px', fontSize:'11px', fontWeight:'700', textTransform:'uppercase' };
}
const btnStyle = computed(() => ({ padding:'8px 16px', background: s.value.btn_bg || accent.value, color: s.value.btn_color, borderRadius: s.value.btn_radius+'px', fontSize:'13px', fontWeight:'600', cursor:'pointer' }));
</script>
