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
        border: s.style === 'primary' ? 'none' : '1px solid var(--olo-color-border, #e5e7eb)',
        background: s.style === 'primary' ? 'var(--olo-color-primary, #e1474f)' : 'transparent',
        color: s.style === 'primary' ? 'var(--olo-color-primary-contrast, #fff)' : 'var(--olo-color-text-muted, #9ca3af)',
        cursor: 'pointer',
      }"
    >
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
    </span>
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
