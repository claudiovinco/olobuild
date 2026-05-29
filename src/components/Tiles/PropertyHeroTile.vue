<template>
  <div class="mb-font-sans">
    <div :style="heroStyle">
      <div :style="overlayStyle"></div>
      <div :style="{ position:'absolute', bottom:'0', left:'0', right:'0', padding:'32px', zIndex:2 }">
        <span :style="badgeStyle">{{ t('Vendita') }}</span>
        <div :style="{ fontSize:'28px', fontWeight:'800', color:'#fff', margin:'12px 0 6px' }">{{ t('&euro; 325.000') }}</div>
        <div :style="{ display:'inline-flex', alignItems:'center', gap:'6px', fontSize:'15px', color:'rgba(255,255,255,0.9)', marginBottom:'14px' }">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
          <span>{{ t('Trento, Centro Storico') }}</span>
        </div>
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
const accent = computed(() => s.value.accent_color || 'var(--olo-color-primary, #e1474f)');
const specs = ['95 m²', '4 locali', '2 camere', '1 bagno'];
const heroStyle = computed(() => ({
  position:'relative', height: s.value.height+'px',
  background:'var(--olo-color-dark, #16263d)',
  borderRadius: s.value.border_radius+'px', overflow:'hidden',
}));
const overlayStyle = computed(() => ({
  position:'absolute', inset:'0',
  background:'linear-gradient(to top, rgba(0,0,0,'+s.value.overlay_opacity+') 0%, rgba(0,0,0,0.05) 60%)',
}));
const badgeStyle = computed(() => ({ display:'inline-block', background: accent.value, color:'var(--olo-color-primary-contrast, #fff)', padding:'6px 16px', borderRadius:'6px', fontSize:'12px', fontWeight:'700', textTransform:'uppercase' }));
const specStyle = { fontSize:'13px', color:'rgba(255,255,255,0.85)', fontWeight:'500' };
</script>
