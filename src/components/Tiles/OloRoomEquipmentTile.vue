<template>
  <div>
    <h3 :style="{ fontSize:'16px', fontWeight:'700', color: TOKENS.text, margin:'0 0 10px' }">{{ t('Dotazioni') }}</h3>
    <div :style="gridStyle">
      <div v-for="item in equipment" :key="item" :style="itemStyle">
        <span class="olo-eq-ic" :style="{ width:'14px', height:'14px', color: TOKENS.primary, flexShrink:0, display:'inline-flex' }" v-html="checkIcon"></span>
        <span>{{ item }}</span>
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
const defaults = { style: 'card', columns: 3 };
const s = computed(() => ({ ...defaults, ...props.settings }));
const checkIcon = computed(() => iconsSvg['check'] || '');
const equipment = ['Videoproiettore', 'Microfono', 'Wi-Fi', 'Lavagna', 'Condizionatore', 'Prese elettriche'];
const gridStyle = computed(() => ({ display: 'grid', gridTemplateColumns: `repeat(${parseInt(s.value.columns)||3}, 1fr)`, gap: '8px 16px' }));
const itemStyle = computed(() => ({ display: 'flex', alignItems: 'center', gap: '6px', fontSize: '13px', color: TOKENS.text, padding: s.value.style === 'card' ? '6px 10px' : '3px 0', background: s.value.style === 'card' ? TOKENS.surfaceAlt : 'transparent', borderRadius: '6px' }));
</script>

<style scoped>
.olo-eq-ic :deep(svg) { width: 100%; height: 100%; fill: currentColor; stroke: currentColor; }
</style>
