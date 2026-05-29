<template>
  <div :style="wrapStyle">
    <h3 :style="{fontSize:'16px',fontWeight:'700',color: TOKENS.text, margin:'0 0 10px'}" data-olo-editable="title">{{ s.title || 'Contatti' }}</h3>
    <div :style="{ display:'flex', flexDirection:'column', gap:'6px', fontSize:'13px', color: TOKENS.text }">
      <div style="display:flex;align-items:center;gap:8px">
        <span class="olo-ct-ic" :style="iconStyle" v-html="phoneIcon"></span>
        <span>+39 0464 452 111</span>
      </div>
      <div style="display:flex;align-items:center;gap:8px">
        <span class="olo-ct-ic" :style="iconStyle" v-html="mailIcon"></span>
        <span>{{ t('info@comune.rovereto.tn.it') }}</span>
      </div>
      <div style="display:flex;align-items:center;gap:8px">
        <span class="olo-ct-ic" :style="iconStyle" v-html="faxIcon"></span>
        <span>+39 0464 452 199</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
import { TOKENS } from '@/composables/oloTileDefaults';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { style: 'card', title: 'Contatti' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const phoneIcon = computed(() => iconsSvg['phone'] || '');
const mailIcon = computed(() => iconsSvg['mail'] || '');
const faxIcon = computed(() => iconsSvg['printer'] || iconsSvg['phone'] || '');
const iconStyle = { width: '15px', height: '15px', color: TOKENS.primary, flexShrink: 0, display: 'inline-flex' };
const wrapStyle = computed(() => s.value.style === 'card' ? { background: TOKENS.surfaceAlt, border: '1px solid ' + TOKENS.border, borderRadius: '10px', padding: '16px' } : {});
</script>

<style scoped>
.olo-ct-ic :deep(svg) { width: 100%; height: 100%; fill: currentColor; stroke: currentColor; }
</style>
