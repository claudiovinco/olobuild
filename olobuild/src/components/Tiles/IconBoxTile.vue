<template>
  <div
    class="mb-py-8 mb-px-6"
    :style="{ textAlign: settings.alignment || 'center', color: settings.text_color || '#F3F4F6', minHeight: '80px' }"
  >
    <div v-if="settings.icon_emoji" class="mb-mb-4" :style="{ fontSize: (settings.icon_size || 3) + 'em', lineHeight: 1 }">
      <span
        v-if="iconSvg"
        class="iconbox-icon-preview"
        :style="{ width: '1em', height: '1em' }"
        v-html="iconSvg"
      ></span>
      <template v-else>{{ settings.icon_emoji }}</template>
    </div>
    <h3 class="mb-text-xl mb-font-semibold mb-mb-2" v-html="settings.title || 'Feature Title'"></h3>
    <div class="mb-opacity-80 mb-leading-relaxed mb-mb-4" v-html="settings.description || 'Description here.'"></div>
    <span v-if="settings.link_url" class="mb-text-primary-400 mb-font-medium">
      <span v-html="settings.link_text || 'Learn more'"></span> &rarr;
    </span>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const iconSvg = computed(() => iconsSvg[props.settings.icon_emoji] || '');
</script>

<style scoped>
.iconbox-icon-preview {
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.iconbox-icon-preview :deep(svg) {
  width: 100%;
  height: 100%;
  fill: currentColor;
  stroke: currentColor;
}
</style>
