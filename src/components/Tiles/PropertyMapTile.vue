<template>
  <div class="mb-font-sans">
    <div :style="mapStyle">
      <div :style="{ position:'absolute', inset:0, display:'flex', flexDirection:'column', alignItems:'center', justifyContent:'center', gap:'12px' }">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--olo-color-text-faint, #94a3b8)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
        <span :style="{ background:'rgba(0,0,0,0.6)', color:'#fff', padding:'6px 16px', borderRadius:'20px', fontSize:'13px', fontWeight:'600' }">{{ t('Mappa interattiva') }}</span>
        <div :style="{ display:'flex', gap:'16px', marginTop:'8px' }">
          <div v-for="pin in pins" :key="pin.label" :style="pinStyle">
            <span :style="{ width:'8px', height:'8px', borderRadius:'50%', background: pin.color, display:'inline-block' }"></span>
            <span>{{ pin.label }}</span>
          </div>
        </div>
      </div>
      <div :style="{ position:'absolute', inset:0, opacity:0.08, backgroundImage:'linear-gradient(var(--olo-color-text, #374151) 1px, transparent 1px), linear-gradient(90deg, var(--olo-color-text, #374151) 1px, transparent 1px)', backgroundSize:'60px 60px' }"></div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { height: 400, border_radius: 12, bg_color: '' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const pins = [
  { label:'Vendita', color:'var(--olo-color-primary, #e1474f)' },
  { label:'Affitto', color:'var(--olo-color-success, #15803d)' },
  { label:'Nuovo', color:'var(--olo-color-warning, #b45309)' },
];
const mapStyle = computed(() => ({ position:'relative', height: s.value.height+'px', background: s.value.bg_color || 'var(--olo-color-surface-alt, #f6f7f9)', borderRadius: s.value.border_radius+'px', overflow:'hidden' }));
const pinStyle = { display:'flex', alignItems:'center', gap:'4px', fontSize:'12px', color:'var(--olo-color-text, #374151)', fontWeight:'500' };
</script>
