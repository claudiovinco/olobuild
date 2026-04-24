<template>
  <div class="mb-font-sans">
    <div :style="heroStyle">
      <div :style="overlayStyle"></div>
      <div :style="{ position:'absolute', bottom:'0', left:'0', right:'0', padding:'32px', zIndex:2 }">
        <span :style="badgeStyle">{{ t('Vendita') }}</span>
        <div :style="{ fontSize:'28px', fontWeight:'800', color:'#fff', margin:'12px 0 6px' }">{{ t('&euro; 325.000') }}</div>
        <div :style="{ fontSize:'15px', color:'rgba(255,255,255,0.9)', marginBottom:'14px' }">{{ t('&#128205; Trento, Centro Storico') }}</div>
        <div :style="{ display:'flex', gap:'20px' }">
          <span v-for="spec in specs" :key="spec" :style="specStyle">{{ spec }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { height: 450, border_radius: 16, overlay_opacity: 0.4, accent_color: '' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const specs = ['95 m\u00B2', '4 locali', '2 camere', '1 bagno'];
const heroStyle = computed(() => ({
  position:'relative', height: s.value.height+'px',
  background:'linear-gradient(135deg,#0891B240,#0891B2A0)',
  borderRadius: s.value.border_radius+'px', overflow:'hidden',
}));
const overlayStyle = computed(() => ({
  position:'absolute', inset:'0',
  background:'linear-gradient(to top, rgba(0,0,0,'+s.value.overlay_opacity+') 0%, rgba(0,0,0,0.05) 60%)',
}));
const badgeStyle = { display:'inline-block', background:'#6366F1', color:'#fff', padding:'6px 16px', borderRadius:'6px', fontSize:'12px', fontWeight:'700', textTransform:'uppercase' };
const specStyle = { fontSize:'13px', color:'rgba(255,255,255,0.85)', fontWeight:'500' };
</script>
