<template>
  <div :style="wrapStyle">
    <div :style="imgStyle"></div>
    <div :style="overlayStyle"></div>
    <div style="position:relative;z-index:2;text-align:center;color:#fff;padding:30px 20px">
      <span v-if="s.show_badge" :style="badgeStyle">Sala Conferenze</span>
      <div :style="{fontSize:'24px',fontWeight:'700',margin:'8px 0 4px',textShadow:'0 2px 8px rgba(0,0,0,.4)'}">Nome Sala</div>
      <div v-if="s.show_address" style="font-size:13px;opacity:.85;margin-bottom:14px">Via Roma 1, 38068 Rovereto (TN)</div>
      <div v-if="s.cta_text" :style="ctaStyle">{{ s.cta_text }}</div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { height: 420, overlay: true, overlay_opacity: 45, show_badge: true, show_address: true, cta_text: 'Prenota questa sala' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const wrapStyle = computed(() => ({ position: 'relative', height: Math.min(parseInt(s.value.height) || 420, 250) + 'px', borderRadius: '12px', overflow: 'hidden', display: 'flex', alignItems: 'center', justifyContent: 'center' }));
const imgStyle = computed(() => ({ position: 'absolute', inset: '0', background: 'linear-gradient(135deg, #1e3a5f 0%, #0e7490 100%)' }));
const overlayStyle = computed(() => ({ position: 'absolute', inset: '0', background: s.value.overlay ? `rgba(0,0,0,${(parseInt(s.value.overlay_opacity)||45)/100})` : 'transparent', zIndex: '1' }));
const badgeStyle = computed(() => ({ background: '#1e87f0', color: '#fff', padding: '3px 12px', borderRadius: '4px', fontSize: '11px', fontWeight: '600', display: 'inline-block', marginBottom: '6px' }));
const ctaStyle = computed(() => ({ display: 'inline-block', background: '#1e87f0', color: '#fff', padding: '8px 20px', borderRadius: '8px', fontSize: '13px', fontWeight: '600' }));
</script>
