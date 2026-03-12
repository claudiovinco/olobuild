<template>
  <div class="mb-p-4 mb-flex mb-flex-col mb-gap-4">
    <!-- Circle layout -->
    <div v-if="s.layout === 'circle'" style="display:flex;flex-wrap:wrap;gap:16px;justify-content:center;padding:16px;">
      <div v-for="(item, i) in parsedBars" :key="i" style="text-align:center;">
        <svg :width="circleSize" :height="circleSize" :viewBox="'0 0 ' + circleSize + ' ' + circleSize">
          <!-- Background circle -->
          <circle :cx="circleSize/2" :cy="circleSize/2" :r="radius" fill="none" :stroke="s.bar_bg || 'var(--olo-color-border, #E5E7EB)'" :stroke-width="strokeWidth" />
          <!-- Progress circle -->
          <circle :cx="circleSize/2" :cy="circleSize/2" :r="radius" fill="none" :stroke="s.bar_color || 'var(--olo-color-primary, #6366F1)'" :stroke-width="strokeWidth"
            :stroke-dasharray="circumference" :stroke-dashoffset="circumference - (circumference * item.value / 100)"
            stroke-linecap="round" :transform="'rotate(-90 ' + circleSize/2 + ' ' + circleSize/2 + ')'"
            style="transition: stroke-dashoffset 1s ease;" />
          <!-- Center text -->
          <text :x="circleSize/2" :y="circleSize/2" text-anchor="middle" dominant-baseline="central"
            :fill="s.text_color || 'var(--olo-color-text, #374151)'" :font-size="circleSize * 0.2 + 'px'" font-weight="600">
            {{ s.inner_text || item.value + '%' }}
          </text>
        </svg>
        <div style="margin-top:4px;font-size:11px;" :style="{ color: s.text_color || 'var(--olo-color-text, #374151)' }" :data-olo-editable="'bars.' + i + '.label'">{{ item.label }}</div>
      </div>
    </div>

    <!-- Bar layout -->
    <template v-else>
      <div v-for="(bar, i) in parsedBars" :key="i">
        <div class="mb-flex mb-justify-between mb-mb-1.5 mb-text-sm" :style="{ color: s.text_color }">
          <span class="mb-font-semibold" :data-olo-editable="'bars.' + i + '.label'">{{ bar.label }}</span>
          <span v-if="s.show_percentage !== false">{{ bar.value }}%</span>
        </div>
        <div :style="barBgStyle" style="position:relative;">
          <div :style="barFillStyle(bar.value)"></div>
          <span v-if="s.inner_text || s.show_percentage !== false"
            style="position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);font-size:10px;font-weight:600;color:inherit;"
            :style="{ color: s.text_color || 'var(--olo-color-text, #374151)' }">
            {{ s.inner_text || bar.value + '%' }}
          </span>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const defaults = {
  bars: 'HTML|90\nJavaScript|80\nVue.js|75',
  bar_color: 'var(--olo-color-primary, #6366F1)',
  bar_bg: 'var(--olo-color-muted, #F3F4F6)',
  text_color: 'var(--olo-color-text, #374151)',
  height: '20',
  show_percentage: true,
  animated: true,
  layout: 'bar',
  circle_size: '120',
  circle_width: '8',
  inner_text: '',
  animate_counter: true,
  animation_duration: '1500',
  shadow: 'none',
  border_width: '0',
  border_color: '#e5e7eb',
  border_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
};

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({ ...defaults, ...props.settings }));

const parsedBars = computed(() => {
  const text = s.value.bars || '';
  return text.split('\n').map(l => l.trim()).filter(Boolean).map(line => {
    const parts = line.split('|');
    if (parts.length >= 2) return { label: parts[0].trim(), value: Math.min(Math.max(parseInt(parts[1]) || 0, 0), 100) };
    return null;
  }).filter(Boolean);
});

const barRadius = computed(() => ((v => isNaN(v) ? 10 : v)(parseInt(s.value.border_radius))) + 'px');
const barHeight = computed(() => (parseInt(s.value.height) || 20) + 'px');

const barBgStyle = computed(() => ({
  background: s.value.bar_bg,
  borderRadius: barRadius.value,
  height: barHeight.value,
  overflow: 'hidden',
}));

function barFillStyle(value) {
  return {
    height: '100%',
    width: value + '%',
    background: s.value.bar_color,
    borderRadius: barRadius.value,
    transition: 'width 1s ease',
  };
}

/* Circle computed */
const circleSize = computed(() => parseInt(s.value.circle_size) || 120);
const strokeWidth = computed(() => parseInt(s.value.circle_width) || 8);
const radius = computed(() => (circleSize.value - strokeWidth.value) / 2);
const circumference = computed(() => 2 * Math.PI * radius.value);
</script>
