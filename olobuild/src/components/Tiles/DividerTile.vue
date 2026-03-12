<template>
  <div class="mb-flex mb-py-4 mb-px-4" :style="{ justifyContent: alignJustify }">
    <!-- With center text/emoji -->
    <div v-if="hasCenter" class="mb-flex mb-items-center mb-gap-4" :style="{ width: s.width + '%' }">
      <hr class="mb-flex-1 mb-m-0" :style="lineStyle" />
      <span class="mb-text-sm mb-whitespace-nowrap" :style="{ color: s.text_color }" data-olo-editable="text">
        <template v-if="s.icon_emoji">{{ s.icon_emoji }} </template>{{ s.text }}
      </span>
      <hr class="mb-flex-1 mb-m-0" :style="lineStyle" />
    </div>

    <!-- Simple line -->
    <hr v-else class="mb-m-0" :style="[lineStyle, { width: s.width + '%' }]" />
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  style: 'solid',
  width: '100',
  thickness: '1',
  color: '#374151',
  alignment: 'center',
  text: '',
  text_color: '#9CA3AF',
  icon_emoji: '',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const hasCenter = computed(() => !!(s.value.text || s.value.icon_emoji));

const alignJustify = computed(() => {
  const m = { left: 'flex-start', center: 'center', right: 'flex-end' };
  return m[s.value.alignment] || 'center';
});

const lineStyle = computed(() => {
  const v = s.value;
  const thick = parseInt(v.thickness) || 1;
  if (v.style === 'gradient') {
    return {
      border: 'none',
      height: thick + 'px',
      background: `linear-gradient(90deg, transparent, ${v.color}, transparent)`,
    };
  }
  return {
    border: 'none',
    borderTop: `${thick}px ${v.style || 'solid'} ${v.color || '#374151'}`,
  };
});
</script>
