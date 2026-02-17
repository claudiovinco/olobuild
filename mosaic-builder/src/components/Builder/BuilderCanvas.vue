<template>
  <div class="mb-flex mb-flex-col mb-flex-1 mb-overflow-hidden">
    <!-- Style system color bar (visible when style panel is open) -->
    <div
      v-if="builderStore.stylePanelOpen"
      class="mb-flex mb-items-center mb-gap-3 mb-px-4 mb-py-1.5 mb-bg-gray-800 mb-border-b mb-border-gray-700 mb-shrink-0"
    >
      <span class="mb-text-[10px] mb-text-gray-500">Anteprima:</span>
      <div
        v-for="(color, key) in previewColors"
        :key="key"
        class="mb-flex mb-items-center mb-gap-1"
        :title="key"
      >
        <span
          class="mb-w-4 mb-h-4 mb-rounded-sm mb-border mb-border-gray-600"
          :style="{ backgroundColor: color }"
        ></span>
        <span class="mb-text-[9px] mb-text-gray-500">{{ key }}</span>
      </div>
    </div>

    <!-- Canvas area -->
    <div
      class="mb-flex-1 mb-overflow-y-auto mb-p-6 mb-bg-gray-900"
      @dragover.prevent="onDragOver"
      @drop.prevent="onDrop"
      @click.self="onCanvasClick"
    >
      <div
        :class="[canvasClasses, { 'preview-mode': builderStore.previewMode }]"
        :style="canvasStyle"
        class="olo-template mb-mx-auto mb-min-h-full mb-transition-all mb-duration-300 mb-relative mb-overflow-hidden"
      >
        <!-- Page background overlay -->
        <div
          v-if="pageBg.type !== 'none' && pageBg.overlay_opacity > 0"
          class="mb-absolute mb-inset-0 mb-pointer-events-none"
          :style="{ backgroundColor: pageBg.overlay_color || '#000000', opacity: (pageBg.overlay_opacity || 0) / 100, zIndex: 1 }"
        ></div>

        <!-- Empty state -->
        <div
          v-if="tilesStore.canvasTiles.length === 0"
          class="mb-flex mb-flex-col mb-items-center mb-justify-center mb-h-96 mb-text-gray-500 mb-relative"
          style="z-index: 2"
        >
          <span class="mb-text-5xl mb-mb-4 mb-opacity-30">&#x1F9E9;</span>
          <p class="mb-text-lg mb-font-medium">Trascina una tile qui per iniziare</p>
          <p class="mb-text-sm mb-mt-1">Trascina le tile dalla barra laterale a sinistra</p>
        </div>

        <!-- Grid with tiles -->
        <div v-else class="mb-relative" style="z-index: 2">
          <OlobuilderGrid />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, watchEffect, onUnmounted } from 'vue';
import { useBuilderStore } from '@/stores/builder';
import { useTilesStore } from '@/stores/tiles';
import { useStylesStore } from '@/stores/styles';
import { useDragDrop } from '@/composables/useDragDrop';
import OlobuilderGrid from '@/components/Grid/OlobuilderGrid.vue';

const builderStore = useBuilderStore();
const tilesStore = useTilesStore();
const stylesStore = useStylesStore();
const { handleDropFromSidebar } = useDragDrop();

// Live style system: inject CSS into <head> reactively
const styleEl = document.createElement('style');
styleEl.id = 'olo-live-styles';
document.head.appendChild(styleEl);

watchEffect(() => {
  styleEl.textContent = stylesStore.cssVariables;
});

onUnmounted(() => {
  styleEl.remove();
});

const pageBg = computed(() => builderStore.pageSettings.page_bg);

const previewColors = computed(() => ({
  primary: stylesStore.colors.primary,
  secondary: stylesStore.colors.secondary,
  background: stylesStore.colors.background,
  text: stylesStore.colors.text,
  muted: stylesStore.colors.muted,
}));

const canvasClasses = computed(() => {
  const maxW = builderStore.pageSettings.content_max_width || 1200;
  switch (builderStore.viewMode) {
    case 'tablet':
      return 'mb-max-w-[768px]';
    case 'mobile':
      return 'mb-max-w-[375px]';
    default:
      if (maxW >= 9999) return 'mb-max-w-full';
      return '';
  }
});

const canvasStyle = computed(() => {
  const style = {};
  const maxW = builderStore.pageSettings.content_max_width || 1200;
  const bg = pageBg.value;

  // Dynamic max-width (only for desktop, non-viewport overrides)
  if (builderStore.viewMode === 'desktop' && maxW < 9999) {
    style.maxWidth = `${maxW}px`;
  }

  // Page background
  if (bg.type === 'solid') {
    style.backgroundColor = bg.color || '#ffffff';
  } else if (bg.type === 'gradient') {
    style.background = `linear-gradient(${bg.gradient_angle || 180}deg, ${bg.gradient_from || '#ffffff'}, ${bg.gradient_to || '#000000'})`;
  } else if (bg.type === 'image' && bg.image_url) {
    style.backgroundImage = `url(${bg.image_url})`;
    style.backgroundSize = bg.image_size || 'cover';
    style.backgroundPosition = bg.image_position || 'center center';
    style.backgroundRepeat = 'no-repeat';
  } else {
    // Use style system background color
    style.backgroundColor = stylesStore.colors.background || '#ffffff';
    style.color = stylesStore.colors.text || '#333333';
  }

  return style;
});

function onCanvasClick() {
  builderStore.togglePageSettings();
}

function onDragOver(event) {
  if (builderStore.previewMode) return;
  event.dataTransfer.dropEffect = 'copy';
}

function onDrop(event) {
  if (builderStore.previewMode) return;
  const tileType = event.dataTransfer.getData('tile-type');
  if (tileType) {
    handleDropFromSidebar(tileType);
  }
}
</script>
