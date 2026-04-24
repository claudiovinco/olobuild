<template>
  <div class="mb-font-sans" :style="wrapStyle">
    <div :style="formStyle">
      <div :style="fieldStyle" v-for="field in fields" :key="field.label">
        <label :style="{ fontSize:'11px', fontWeight:'600', color:'#6B7280', textTransform:'uppercase', letterSpacing:'0.5px', marginBottom:'4px', display:'block' }">{{ field.label }}</label>
        <div :style="selectStyle">
          <span>{{ field.value }}</span>
          <span style="color:#9CA3AF">&#9662;</span>
        </div>
      </div>
      <div :style="{ display:'flex', alignItems: s.layout==='vertical' ? 'stretch' : 'flex-end' }">
        <div :style="btnStyle">{{ t('&#128269; Cerca') }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { layout:'horizontal', bg:'#FFFFFF', border_radius:12, shadow:'md', padding:24, accent_color:'', btn_radius:8 };
const s = computed(() => ({ ...defaults, ...props.settings }));
const accent = computed(() => s.value.accent_color || 'var(--olo-color-primary, #6366F1)');
const fields = [
  { label:'Tipo', value:'Vendita' }, { label:'Tipologia', value:'Appartamento' },
  { label:'Citt\u00E0', value:'Trento' }, { label:'Prezzo max', value:'\u20AC 500.000' },
];
const shadowMap = { none:'none', sm:'0 1px 3px rgba(0,0,0,0.1)', md:'0 4px 12px rgba(0,0,0,0.1)', lg:'0 8px 24px rgba(0,0,0,0.15)' };
const wrapStyle = computed(() => ({ background: s.value.bg, borderRadius: s.value.border_radius+'px', boxShadow: shadowMap[s.value.shadow]||shadowMap.md, padding: s.value.padding+'px' }));
const formStyle = computed(() => {
  if (s.value.layout === 'vertical') return { display:'flex', flexDirection:'column', gap:'16px' };
  return { display:'flex', gap:'16px', alignItems:'flex-end' };
});
const fieldStyle = computed(() => ({ flex:'1' }));
const selectStyle = computed(() => ({ display:'flex', justifyContent:'space-between', alignItems:'center', padding:'10px 14px', border:'1px solid #E5E7EB', borderRadius:'8px', fontSize:'14px', color:'#374151', background:'#F9FAFB', cursor:'pointer' }));
const btnStyle = computed(() => ({ padding:'10px 28px', background: accent.value, color:'#fff', borderRadius: s.value.btn_radius+'px', fontSize:'14px', fontWeight:'700', cursor:'pointer', whiteSpace:'nowrap', textAlign:'center' }));
</script>
