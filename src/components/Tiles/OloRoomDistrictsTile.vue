<template>
  <div>
    <h3 :style="{ fontSize:'18px', fontWeight:'700', color: TOKENS.text, margin:'0 0 12px' }">{{ t('Zone') }}</h3>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
      <div v-for="zone in zones" :key="zone.name" :style="{ border:'1px solid ' + TOKENS.border, borderRadius:'10px', padding:'14px', display:'flex', alignItems:'center', gap:'10px' }">
        <div :style="iconWrapStyle">
          <span class="olo-dist-ic" :style="{ width:'18px', height:'18px', color: TOKENS.primary, display:'inline-flex' }" v-html="pinIcon"></span>
        </div>
        <div>
          <div :style="{ fontSize:'14px', fontWeight:'600', color: TOKENS.text }">{{ zone.name }}</div>
          <div :style="{ fontSize:'12px', color: TOKENS.textSoft }">{{ zone.count }} sale</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
import { TOKENS } from '@/composables/oloTileDefaults';
defineProps({ settings: { type: Object, default: () => ({}) } });
const pinIcon = computed(() => iconsSvg['map-pin'] || '');
const zones = [
  { name: 'Centro storico', count: 4 },
  { name: 'Brione', count: 2 },
  { name: 'Fucine', count: 1 },
];
const iconWrapStyle = {
  width: '36px', height: '36px', borderRadius: '50%',
  background: 'color-mix(in srgb, var(--olo-color-primary, #e1474f) 12%, #fff)',
  display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: '0',
};
</script>

<style scoped>
.olo-dist-ic :deep(svg) { width: 100%; height: 100%; fill: currentColor; stroke: currentColor; }
</style>
