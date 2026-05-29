<template>
  <div>
    <h3 :style="{ fontSize:'18px', fontWeight:'700', color: TOKENS.text, margin:'0 0 12px' }">{{ t('Sale nella stessa zona') }}</h3>
    <div :style="gridStyle">
      <div v-for="room in rooms" :key="room.name" :style="{ border:'1px solid ' + TOKENS.border, borderRadius:'10px', overflow:'hidden' }">
        <div :style="imgStyle">
          <span class="olo-rel-ph" :style="{ width:'28px', height:'28px', color: TOKENS.textFaint }" v-html="imgIcon"></span>
        </div>
        <div style="padding:10px">
          <div :style="{ fontSize:'14px', fontWeight:'600', color: TOKENS.text }">{{ room.name }}</div>
          <div :style="{ fontSize:'12px', color: TOKENS.textSoft, marginTop:'2px' }">{{ room.info }}</div>
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
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { count: 3, source: 'zone' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const imgIcon = computed(() => iconsSvg['image'] || '');
const rooms = [
  { name: 'Sala Tartarotti', info: '40 posti · Palazzo Annona' },
  { name: 'Auditorium Melotti', info: '200 posti · Via Laurenti' },
  { name: 'Sala Rosmini', info: '30 posti · Biblioteca Civica' },
];
const imgStyle = { height: '100px', background: TOKENS.surfaceAlt, display: 'flex', alignItems: 'center', justifyContent: 'center' };
const gridStyle = computed(() => ({ display: 'grid', gridTemplateColumns: `repeat(${parseInt(s.value.count)||3}, 1fr)`, gap: '16px' }));
</script>

<style scoped>
.olo-rel-ph :deep(svg) { width: 100%; height: 100%; fill: currentColor; stroke: currentColor; }
</style>
