<template>
  <div class="mb-py-4 mb-px-4">
    <div class="mb-text-xs mb-text-gray-400 mb-mb-2">
      Anteprima barra scroll (posizione: {{ s.position === 'bottom' ? 'basso' : 'alto' }})
    </div>
    <div
      :style="{
        display: 'flex',
        alignItems: 'center',
        height: barHeight + 'px',
        backgroundColor: s.bar_bg || '#e5e7eb',
        borderRadius: '2px',
        overflow: 'hidden',
        position: 'relative',
      }"
    >
      <div
        :style="{
          width: '42%',
          height: '100%',
          backgroundColor: s.bar_color || '#6366F1',
          transition: 'width 0.3s ease',
          borderRadius: '2px',
        }"
      ></div>
      <span
        v-if="s.show_percentage"
        :style="{
          position: 'absolute',
          right: '6px',
          top: '50%',
          transform: 'translateY(-50%)',
          fontSize: '10px',
          fontWeight: '600',
          color: s.percentage_color || '#ffffff',
          lineHeight: '1',
        }"
      >42%</span>
    </div>
    <div class="mb-text-xs mb-text-gray-500 mb-mt-1">
      z-index: {{ s.z_index }} &middot; altezza: {{ barHeight }}px
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const defaults = {
  position: 'top',
  bar_color: '#6366F1',
  bar_bg: '#e5e7eb',
  bar_height: '4',
  show_percentage: false,
  percentage_color: '#ffffff',
  z_index: '9999',
};

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({ ...defaults, ...props.settings }));

const barHeight = computed(() => Math.max(2, Math.min(12, parseInt(s.value.bar_height) || 4)));
</script>
