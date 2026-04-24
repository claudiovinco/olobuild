<template>
  <div class="mb-font-sans" :style="cardStyle">
    <div :style="imageStyle">
      <span :style="badgeStyle">{{ t('Vendita') }}</span>
    </div>
    <div :style="{ padding: s.padding+'px' }">
      <div :style="{ fontSize: s.title_size+'px', fontWeight:'700', color:'#1F2937', marginBottom:'4px' }">{{ t('Villa Panoramica') }}</div>
      <div :style="{ fontSize:'13px', color:'#6B7280', marginBottom:'12px' }">{{ t('Pergine Valsugana, Lago') }}</div>
      <div :style="{ display:'flex', gap:'16px', fontSize:'13px', color:'#6B7280', marginBottom:'14px' }">
        <span>{{ t('220 m&sup2;') }}</span><span>{{ t('8 locali') }}</span><span>{{ t('4 camere') }}</span><span>{{ t('3 bagni') }}</span>
      </div>
      <div :style="{ display:'flex', justifyContent:'space-between', alignItems:'center' }">
        <span :style="{ fontSize:'20px', fontWeight:'700', color: accent }">{{ t('&euro; 520.000') }}</span>
        <span :style="btnStyle">{{ t('Scopri') }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { max_width: 380, border_radius: 12, shadow: 'md', padding: 20, title_size: 18, accent_color: '', btn_radius: 8 };
const s = computed(() => ({ ...defaults, ...props.settings }));
const accent = computed(() => s.value.accent_color || 'var(--olo-color-primary, #6366F1)');
const shadowMap = { none:'none', sm:'0 1px 3px rgba(0,0,0,0.1)', md:'0 4px 12px rgba(0,0,0,0.1)', lg:'0 8px 24px rgba(0,0,0,0.15)' };
const cardStyle = computed(() => ({ maxWidth: s.value.max_width+'px', background:'#fff', borderRadius: s.value.border_radius+'px', boxShadow: shadowMap[s.value.shadow]||shadowMap.md, overflow:'hidden', border:'1px solid #E5E7EB' }));
const imageStyle = computed(() => ({ position:'relative', height:'200px', background:'linear-gradient(135deg,#F5920040,#F5920090)', borderRadius: s.value.border_radius+'px '+s.value.border_radius+'px 0 0' }));
const badgeStyle = { position:'absolute', top:'12px', left:'12px', background:'#6366F1', color:'#fff', padding:'4px 12px', borderRadius:'6px', fontSize:'11px', fontWeight:'700', textTransform:'uppercase' };
const btnStyle = computed(() => ({ padding:'8px 18px', background: accent.value, color:'#fff', borderRadius: s.value.btn_radius+'px', fontSize:'13px', fontWeight:'600', cursor:'pointer' }));
</script>
