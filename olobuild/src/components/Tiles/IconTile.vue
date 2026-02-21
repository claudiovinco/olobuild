<template>
  <div
    class="mb-flex mb-items-center mb-justify-center mb-py-6"
    :style="{ minHeight: '60px' }"
  >
    <span
      v-if="iconSvg"
      class="olo-icon-preview"
      :style="{
        width: (settings.size || 40) + 'px',
        height: (settings.size || 40) + 'px',
        color: settings.color || '#9CA3AF',
      }"
      v-html="iconSvg"
    ></span>
    <span
      v-else
      :style="{
        fontSize: (settings.size || 40) + 'px',
        color: settings.color || '#9CA3AF',
        lineHeight: 1,
        display: 'inline-flex',
        alignItems: 'center',
        justifyContent: 'center',
      }"
    >&#9733;</span>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const iconSvg = computed(() => iconsSvg[props.settings.icon] || '');
</script>

<style scoped>
.olo-icon-preview {
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.olo-icon-preview :deep(svg) {
  width: 100%;
  height: 100%;
  fill: currentColor;
  stroke: currentColor;
}
</style>
