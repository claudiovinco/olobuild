<template>
  <div class="mb-flex mb-py-4 mb-px-4" :style="{ justifyContent: alignJustify }">
    <!-- With center text/emoji -->
    <div v-if="hasCenter" class="mb-flex mb-items-center mb-gap-4" :style="{ width: settings.width + '%' }">
      <hr class="mb-flex-1 mb-m-0" :style="lineStyle" />
      <span class="mb-text-sm mb-whitespace-nowrap" :style="{ color: settings.text_color }">
        <template v-if="settings.icon_emoji">{{ settings.icon_emoji }} </template>{{ settings.text }}
      </span>
      <hr class="mb-flex-1 mb-m-0" :style="lineStyle" />
    </div>

    <!-- Simple line -->
    <hr v-else class="mb-m-0" :style="[lineStyle, { width: settings.width + '%' }]" />
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const hasCenter = computed(() => !!(props.settings.text || props.settings.icon_emoji));

const alignJustify = computed(() => {
  const m = { left: 'flex-start', center: 'center', right: 'flex-end' };
  return m[props.settings.alignment] || 'center';
});

const lineStyle = computed(() => {
  const s = props.settings;
  const thick = parseInt(s.thickness) || 1;
  if (s.style === 'gradient') {
    return {
      border: 'none',
      height: thick + 'px',
      background: `linear-gradient(90deg, transparent, ${s.color}, transparent)`,
    };
  }
  return {
    border: 'none',
    borderTop: `${thick}px ${s.style || 'solid'} ${s.color || '#374151'}`,
  };
});
</script>
