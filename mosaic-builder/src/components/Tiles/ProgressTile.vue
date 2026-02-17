<template>
  <div class="mb-p-4 mb-flex mb-flex-col mb-gap-4">
    <div v-for="(bar, i) in parsedBars" :key="i">
      <div class="mb-flex mb-justify-between mb-mb-1.5 mb-text-sm" :style="{ color: settings.text_color }">
        <span class="mb-font-semibold">{{ bar.label }}</span>
        <span v-if="settings.show_percentage !== false">{{ bar.value }}%</span>
      </div>
      <div :style="barBgStyle">
        <div :style="barFillStyle(bar.value)"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const parsedBars = computed(() => {
  const text = props.settings.bars || '';
  return text.split('\n').map(l => l.trim()).filter(Boolean).map(line => {
    const parts = line.split('|');
    if (parts.length >= 2) return { label: parts[0].trim(), value: Math.min(Math.max(parseInt(parts[1]) || 0, 0), 100) };
    return null;
  }).filter(Boolean);
});

const barRadius = computed(() => (parseInt(props.settings.border_radius) || 10) + 'px');
const barHeight = computed(() => (parseInt(props.settings.height) || 20) + 'px');

const barBgStyle = computed(() => ({
  background: props.settings.bar_bg || '#1F2937',
  borderRadius: barRadius.value,
  height: barHeight.value,
  overflow: 'hidden',
}));

function barFillStyle(value) {
  return {
    height: '100%',
    width: value + '%',
    background: props.settings.bar_color || '#6366F1',
    borderRadius: barRadius.value,
    transition: 'width 1s ease',
  };
}
</script>
