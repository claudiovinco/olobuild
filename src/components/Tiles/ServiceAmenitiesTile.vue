<template>
  <div class="mb-font-sans">
    <h3 v-if="s.show_title" :style="{ fontSize: s.title_size+'px', fontWeight:'700', color: s.title_color || 'var(--olo-color-text, #374151)', margin:'0 0 12px', fontStyle:'italic' }">
      {{ s.title_text }}
    </h3>
    <div :style="gridStyle">
      <div v-for="amenity in amenities" :key="amenity"
           :style="{ display:'flex', alignItems:'center', gap:'6px', fontSize: s.text_size+'px', color: s.text_color || 'var(--olo-color-text, #374151)', padding:'3px 0' }">
        <span class="olo-svam-check" :style="{ color: accent, flexShrink:0 }" v-html="checkSvg"></span>
        <span>{{ amenity }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const checkSvg = iconsSvg['check'] || '';

const defaults = {
  columns: '4', gap_x: '24', gap_y: '10', show_title: true, title_text: 'Servizi e comfort',
  title_size: '18', title_color: '', check_color: '', text_color: '', text_size: '14',
};
const s = computed(() => ({ ...defaults, ...props.settings }));
const accent = computed(() => s.value.check_color || 'var(--olo-color-primary, #e1474f)');

const amenities = ['Camino', 'Parcheggio', 'Cucina attrezzata', 'Riscaldamento', 'Biancheria inclusa', 'Asciugamani inclusi', 'Vista montagna'];

const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${parseInt(s.value.columns) || 4}, 1fr)`,
  gap: `${s.value.gap_y}px ${s.value.gap_x}px`,
}));
</script>

<style scoped>
.olo-svam-check { display: inline-flex; }
.olo-svam-check :deep(svg) { width: 15px; height: 15px; stroke: currentColor; fill: none; }
</style>
