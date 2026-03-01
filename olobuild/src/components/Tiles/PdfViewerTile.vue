<template>
  <div class="mb-rounded-lg mb-border mb-border-gray-200 mb-overflow-hidden"
       :style="{ height: s.viewer_height + 'px', backgroundColor: s.bg_color }">
    <!-- Toolbar mockup -->
    <div v-if="s.show_toolbar"
         class="mb-flex mb-items-center mb-gap-2 mb-px-3 mb-py-2 mb-border-b mb-border-gray-200"
         :class="s.theme === 'dark' ? 'mb-bg-gray-800 mb-text-white' : 'mb-bg-white mb-text-gray-700'">
      <span class="mb-text-xs mb-font-medium mb-px-2 mb-py-0.5 mb-rounded mb-bg-indigo-100 mb-text-indigo-700">
        {{ modeLabel }}
      </span>
      <span class="mb-text-xs mb-text-gray-400 mb-ml-auto">PDF Viewer</span>
    </div>
    <!-- Content area -->
    <div class="mb-flex mb-flex-col mb-items-center mb-justify-center mb-h-full mb-text-gray-400">
      <svg class="mb-w-16 mb-h-16 mb-mb-3 mb-opacity-30" viewBox="0 0 24 24" fill="currentColor">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z"/>
      </svg>
      <span v-if="fileName" class="mb-text-sm mb-font-medium mb-text-gray-500">{{ fileName }}</span>
      <span v-else class="mb-text-sm mb-italic">Seleziona un file PDF</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({
  pdf_url: '',
  mode: 'flipbook',
  viewer_height: '600',
  show_toolbar: true,
  theme: 'light',
  bg_color: '#f5f5f5',
  ...props.settings,
}));

const modeLabel = computed(() => {
  const map = {
    flipbook: 'Flipbook',
    single: 'Pagina singola',
    double: 'Doppia pagina',
    scroll: 'Scroll',
  };
  return map[s.value.mode] || 'Flipbook';
});

const fileName = computed(() => {
  const url = s.value.pdf_url;
  if (!url) return '';
  return url.split('/').pop().split('?')[0];
});
</script>
