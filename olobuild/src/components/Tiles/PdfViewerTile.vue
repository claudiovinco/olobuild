<template>
  <div :style="containerStyle">
    <!-- Toolbar mockup -->
    <div v-if="s.show_toolbar"
         class="mb-flex mb-items-center mb-gap-2 mb-px-3 mb-py-2"
         :style="toolbarStyle">
      <span class="mb-text-xs mb-font-medium mb-px-2 mb-py-0.5 mb-rounded mb-bg-indigo-100 mb-text-indigo-700">
        {{ modeLabel }}
      </span>
      <!-- Individual toolbar toggles -->
      <div class="mb-flex mb-items-center mb-gap-1.5 mb-ml-2">
        <span v-if="s.show_page_nav !== false" class="olo-pdf-tool" title="Navigazione pagine">&#9664; &#9654;</span>
        <span v-if="s.show_zoom !== false" class="olo-pdf-tool" title="Zoom">&#128269;</span>
        <span v-if="s.show_fullscreen !== false" class="olo-pdf-tool" title="Schermo intero">&#9974;</span>
        <span v-if="s.show_search !== false" class="olo-pdf-tool" title="Cerca">&#128270;</span>
        <span v-if="s.show_thumbnails !== false" class="olo-pdf-tool" title="Miniature">&#9638;</span>
        <span v-if="s.show_download !== false" class="olo-pdf-tool" title="Download">&#11015;</span>
        <span v-if="s.show_print !== false" class="olo-pdf-tool" title="Stampa">&#128424;</span>
      </div>
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
  show_page_nav: true,
  show_zoom: true,
  show_fullscreen: true,
  show_download: true,
  show_print: true,
  show_search: true,
  show_thumbnails: true,
  border_width: '0',
  border_color: '#e5e7eb',
  border_radius: { tl: 8, tr: 8, br: 8, bl: 8 },
  ...props.settings,
}));

const containerStyle = computed(() => {
  const bw = parseInt(s.value.border_width) || 0;
  const br = s.value.border_radius || {};
  const style = {
    height: s.value.viewer_height + 'px',
    backgroundColor: s.value.bg_color,
    borderRadius: `${br.tl || 0}px ${br.tr || 0}px ${br.br || 0}px ${br.bl || 0}px`,
    overflow: 'hidden',
  };
  if (bw > 0) {
    style.border = bw + 'px solid ' + (s.value.border_color || '#e5e7eb');
  } else {
    style.border = '1px solid #e5e7eb';
  }
  return style;
});

const toolbarStyle = computed(() => ({
  background: s.value.theme === 'dark' ? '#1f2937' : 'var(--olo-color-background, #ffffff)',
  color: s.value.theme === 'dark' ? '#ffffff' : 'var(--olo-color-text, #374151)',
  borderBottom: '1px solid ' + (s.value.theme === 'dark' ? '#374151' : 'var(--olo-color-border, #e5e7eb)'),
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

<style scoped>
.olo-pdf-tool {
  font-size: 11px;
  opacity: 0.6;
  display: inline-flex;
  align-items: center;
  padding: 1px 3px;
  border-radius: 2px;
  background: rgba(0,0,0,0.06);
}
</style>
