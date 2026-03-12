<template>
  <div class="mb-flex mb-flex-col mb-items-center mb-justify-center mb-py-6 mb-gap-2">
    <!-- Title above -->
    <div v-if="s.title_position === 'above'" class="mb-text-sm mb-font-semibold" :style="{ color: s.title_color || '#666' }" data-olo-editable="title">
      {{ s.title }}
    </div>

    <!-- Circle SVG -->
    <svg :width="size" :height="size" :viewBox="'0 0 ' + size + ' ' + size">
      <!-- Track circle -->
      <circle
        :cx="center" :cy="center" :r="radius"
        fill="none"
        :stroke="s.track_color || '#e5e5e5'"
        :stroke-width="strokeW"
      />
      <!-- Progress circle -->
      <circle
        :cx="center" :cy="center" :r="radius"
        fill="none"
        :stroke="s.stroke_color || '#1e87f0'"
        :stroke-width="strokeW"
        :stroke-dasharray="circumference"
        :stroke-dashoffset="dashOffset"
        stroke-linecap="round"
        :transform="'rotate(-90 ' + center + ' ' + center + ')'"
        :style="{ transition: 'stroke-dashoffset ' + ((parseInt(s.duration) || 1500) / 1000) + 's ease' }"
      />
      <!-- Value text -->
      <text
        :x="center"
        :y="s.title_position === 'inside' ? center - fontSize * 0.35 : center"
        text-anchor="middle"
        dominant-baseline="central"
        :fill="s.text_color || '#333'"
        :font-size="fontSize + 'px'"
        font-weight="700"
      >{{ s.prefix }}{{ displayValue }}{{ s.suffix }}</text>
      <!-- Title inside -->
      <text
        v-if="s.title_position === 'inside'"
        :x="center"
        :y="center + fontSize * 0.65"
        text-anchor="middle"
        dominant-baseline="central"
        :fill="s.title_color || '#666'"
        :font-size="titleFontSize + 'px'"
        font-weight="500"
      >{{ s.title }}</text>
    </svg>

    <!-- Title below -->
    <div v-if="s.title_position === 'below'" class="mb-text-sm mb-font-semibold" :style="{ color: s.title_color || '#666' }" data-olo-editable="title">
      {{ s.title }}
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  value: '75',
  max_value: '100',
  suffix: '%',
  prefix: '',
  title: 'Progresso',
  size: '160',
  stroke_width: '10',
  stroke_color: '#1e87f0',
  track_color: '#e5e5e5',
  text_color: '#333333',
  title_color: '#666666',
  duration: '1500',
  title_position: 'below',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const size = computed(() => parseInt(s.value.size) || 160);
const strokeW = computed(() => parseInt(s.value.stroke_width) || 10);
const center = computed(() => size.value / 2);
const radius = computed(() => (size.value - strokeW.value) / 2);
const circumference = computed(() => 2 * Math.PI * radius.value);

const val = computed(() => parseFloat(s.value.value) || 0);
const maxVal = computed(() => parseFloat(s.value.max_value) || 100);
const ratio = computed(() => Math.min(Math.max(val.value / maxVal.value, 0), 1));
const dashOffset = computed(() => circumference.value * (1 - ratio.value));
const displayValue = computed(() => Math.round(val.value));
const fontSize = computed(() => Math.round(size.value * 0.18));
const titleFontSize = computed(() => Math.round(size.value * 0.09));
</script>
