<template>
  <div class="mb-flex mb-flex-col mb-items-center mb-justify-center mb-py-6 mb-gap-2">
    <!-- Title above -->
    <div v-if="s.title_position === 'above'" class="mb-text-sm mb-font-semibold" :style="{ color: titleColor }" data-olo-editable="title">
      {{ s.title }}
    </div>

    <!-- Circle SVG -->
    <svg :width="size" :height="size" :viewBox="'0 0 ' + size + ' ' + size">
      <!-- Track circle -->
      <circle
        :cx="center" :cy="center" :r="radius"
        fill="none"
        :stroke="trackColor"
        :stroke-width="strokeW"
      />
      <!-- Progress circle -->
      <circle
        :cx="center" :cy="center" :r="radius"
        fill="none"
        :stroke="strokeColor"
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
        :fill="textColor"
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
        :fill="titleColor"
        :font-size="titleFontSize + 'px'"
        font-weight="500"
      >{{ s.title }}</text>
    </svg>

    <!-- Title below -->
    <div v-if="s.title_position === 'below'" class="mb-text-sm mb-font-semibold" :style="{ color: titleColor }" data-olo-editable="title">
      {{ s.title }}
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

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
  stroke_color: '',
  track_color: '',
  text_color: '',
  title_color: '',
  duration: '1500',
  title_position: 'below',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

// Colori token-first: progresso = primario brand (era #e1474f blu UIkit),
// traccia = border neutro, valore = text, titolo = text-soft.
const strokeColor = computed(() => resolveColor(s.value.stroke_color, TOKENS.primary));
const trackColor = computed(() => resolveColor(s.value.track_color, TOKENS.border));
const textColor = computed(() => resolveColor(s.value.text_color, TOKENS.text));
const titleColor = computed(() => resolveColor(s.value.title_color, TOKENS.textSoft));

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
