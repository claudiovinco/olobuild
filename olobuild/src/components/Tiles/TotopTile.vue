<template>
  <div
    class="mb-py-4"
    :style="{ textAlign: rv(settings, 'alignment', s.alignment, builderStore.viewMode) }"
  >
    <span
      class="mb-inline-flex mb-items-center mb-justify-center mb-rounded-full"
      :style="{
        width: '36px',
        height: '36px',
        border: s.style === 'primary' ? 'none' : '1px solid #6b7280',
        background: s.style === 'primary' ? '#6366F1' : 'transparent',
        color: s.style === 'primary' ? '#fff' : '#9CA3AF',
        fontSize: '16px',
        cursor: 'pointer',
      }"
    >&#9650;</span>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useBuilderStore } from '@/stores/builder';
import { rv } from '@/composables/useResponsiveValue';

const builderStore = useBuilderStore();

const defaults = {
  alignment: 'right',
  style: 'default',
  smooth: true,
  shadow: 'none',
  border_width: '0',
  border_color: '#e5e7eb',
  border_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
};

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({ ...defaults, ...props.settings }));
</script>
