<template>
  <div class="fmp-wrap">
    <button
      v-for="opt in options"
      :key="opt.value"
      type="button"
      :class="[
        'fmp-pill',
        selected.includes(opt.value) ? 'fmp-pill--active' : ''
      ]"
      @click="toggle(opt.value)"
    >
      <svg v-if="opt.icon && iconPaths[opt.icon]" class="fmp-pill-icon" width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><path :d="iconPaths[opt.icon]" /></svg>
      {{ opt.label }}
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  modelValue: { type: String, default: '' },
  options: { type: Array, default: () => [] },
});

const emit = defineEmits(['update:modelValue']);

const selected = computed(() => {
  if (!props.modelValue) return [];
  return props.modelValue.split(',').map(s => s.trim()).filter(Boolean);
});

function toggle(val) {
  const arr = [...selected.value];
  const idx = arr.indexOf(val);
  if (idx === -1) {
    arr.push(val);
  } else {
    arr.splice(idx, 1);
  }
  emit('update:modelValue', arr.join(','));
}

// SVG icon paths (UIkit-style, 20x20 viewBox)
const iconPaths = {
  location: 'M10 1C6.13 1 3 4.13 3 8c0 5.25 7 11 7 11s7-5.75 7-11c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z',
  calendar: 'M6 2v2M14 2v2M3 7h14M4 4h12a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z',
  users: 'M7 10a3 3 0 100-6 3 3 0 000 6zm0 1c-3 0-5 1.5-5 3v1h10v-1c0-1.5-2-3-5-3zm8-1a2.5 2.5 0 100-5 2.5 2.5 0 000 5zm0 1c-1.5 0-3 .7-3.5 1.5.7.8 1 1.6 1 2.5h5v-1c0-1.2-1-3-2.5-3z',
  home: 'M3 10l7-7 7 7M5 9v7a1 1 0 001 1h3v-4h2v4h3a1 1 0 001-1V9',
  'arrow-up': 'M10 17V3m0 0l-5 5m5-5l5 5',
  grid: 'M3 3h5v5H3zm9 0h5v5h-5zM3 12h5v5H3zm9 0h5v5h-5z',
  star: 'M10 2l2.4 4.8 5.3.8-3.8 3.7.9 5.3L10 14l-4.8 2.6.9-5.3L2.3 7.6l5.3-.8z',
  pencil: 'M13.5 2.5l4 4L6 18H2v-4L13.5 2.5z',
  chart: 'M3 17h14M6 14V8m4 6V5m4 9V2',
  tag: 'M2 4a2 2 0 012-2h5.17a2 2 0 011.42.59l7.41 7.41a2 2 0 010 2.83l-5.17 5.17a2 2 0 01-2.83 0L2.59 10.59A2 2 0 012 9.17V4zm4 2a1 1 0 100-2 1 1 0 000 2z',
  bolt: 'M11 2L4 12h5l-1 6 7-10h-5l1-6z',
  check: 'M4 10l4 4 8-8',
  wifi: 'M10 16.5a.5.5 0 100-1 .5.5 0 000 1zM3.5 9.5a9 9 0 0113 0M6 12a5.5 5.5 0 018 0',
  fire: 'M10 18c-3.3 0-6-2.7-6-6 0-4 3-6 4-9 .5 2 2 3 3 3-1-2 0-5 2-7 1 2.5 3 4 3 6.5 1-.5 2-2 2-3 1.5 2 2 3.5 2 5.5 0 3.3-2.7 6-6 6z',
  car: 'M5 11h10M4 15h1m10 0h1M6 7l1.5-3h5L14 7M3 7h14a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1V8a1 1 0 011-1z',
  tree: 'M10 18v-5m0 0l-4-4h3l-3-4h3L6 1l4 4 4-4-3 4h3l-3 4h3l-4 4z',
  paw: 'M8 14c-1.5-1-3-3-3-5a2 2 0 014 0c0 2-1.5 4-3 5zm4 0c1.5-1 3-3 3-5a2 2 0 00-4 0c0 2 1.5 4 3 5zM6 6a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm8 0a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm-7 4a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm6 0a1.5 1.5 0 110-3 1.5 1.5 0 010 3z',
  snowflake: 'M10 2v16m0-16l-3 3m3-3l3 3m-3 13l-3-3m3 3l3-3M2 10h16M2 10l3-3m-3 3l3 3m10-6l3-3m-3 3V4m0 12l3 3m-3-3v3',
  droplet: 'M10 2s-6 7-6 11a6 6 0 0012 0c0-4-6-11-6-11z',
  bed: 'M3 12v5m14-5v5M3 12h14a2 2 0 012 2v1H1v-1a2 2 0 012-2zM3 12V7a1 1 0 011-1h4a1 1 0 011 1v2h8v3',
  cup: 'M5 5h8v7a3 3 0 01-3 3H8a3 3 0 01-3-3V5zm8 1h2a2 2 0 010 4h-2M4 18h10',
};
</script>

<style scoped>
.fmp-wrap {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
}

.fmp-pill {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 3px 9px;
  font-size: 11px;
  font-weight: 500;
  border: 1px solid #4b5563;
  border-radius: 999px;
  background: transparent;
  color: #9ca3af;
  cursor: pointer;
  transition: all 0.15s;
  line-height: 1.4;
}

.fmp-pill:hover {
  border-color: #6b7280;
  color: #d1d5db;
}

.fmp-pill--active {
  background: rgb(var(--olo-primary-rgb, 99 102 241) / 0.2);
  border-color: var(--olo-color-primary, #6366F1);
  color: #c7d2fe;
}

.fmp-pill--active:hover {
  background: rgb(var(--olo-primary-rgb, 99 102 241) / 0.3);
}

.fmp-pill-icon {
  flex-shrink: 0;
}
</style>
