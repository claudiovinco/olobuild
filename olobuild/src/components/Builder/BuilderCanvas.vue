<template>
  <div class="mb-flex mb-flex-col mb-flex-1 mb-overflow-hidden">
    <!-- Canvas area -->
    <div
      ref="canvasRef"
      :class="['mb-flex-1 mb-p-6 mb-bg-white', deviceFrame ? 'mb-overflow-hidden' : 'mb-overflow-y-auto']"
      @dragover.prevent="onDragOver"
      @drop.prevent="onDrop"
      @click.self="onCanvasClick"
    >
      <!-- Device frame wrapper for responsive modes -->
      <div v-if="deviceFrame" class="olo-device-wrapper mb-mx-auto mb-transition-all mb-duration-300" :class="['olo-device--' + deviceType, isLandscape ? 'olo-device--landscape' : '']" :style="deviceWrapperStyle">
        <!-- Device chrome top (webcam) -->
        <div class="olo-device-chrome-top" :class="'olo-device-chrome--' + deviceType">
          <span class="olo-device-webcam"></span>
        </div>
        <!-- Screen area — altezza fissa, il contenuto scrolla dentro -->
        <div class="olo-device-screen-wrap">
          <!-- Preview header -->
          <div v-if="builderStore.previewMode && builderStore.previewHeaderContent" class="olo-preview-header olo-template" v-html="builderStore.previewHeaderContent"></div>
          <div
            :class="[canvasClasses, { 'preview-mode': builderStore.previewMode }]"
            :style="canvasStyle"
            class="olo-template olo-device-screen mb-relative"
          >
            <div
              v-if="pageBg.type !== 'none' && pageBg.overlay_opacity > 0"
              class="mb-absolute mb-inset-0 mb-pointer-events-none"
              :style="{ backgroundColor: pageBg.overlay_color || '#000000', opacity: (pageBg.overlay_opacity || 0) / 100, zIndex: 1 }"
            ></div>
            <div
              v-if="tilesStore.canvasTiles.length === 0"
              class="mb-flex mb-flex-col mb-items-center mb-justify-center mb-h-96 mb-text-gray-500 mb-relative"
              style="z-index: 2"
            >
              <svg class="mb-mb-4 mb-opacity-20" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
              </svg>
              <p class="mb-text-lg mb-font-medium">Trascina una tile qui per iniziare</p>
            </div>
            <div v-else class="mb-relative" style="z-index: 2">
              <OlobuilderGrid />
            </div>
          </div>
          <!-- Preview footer -->
          <div v-if="builderStore.previewMode && builderStore.previewFooterContent" class="olo-preview-footer olo-template" v-html="builderStore.previewFooterContent"></div>
        </div>
        <!-- Device chrome bottom (home bar) -->
        <div class="olo-device-chrome-bottom" :class="'olo-device-chrome--' + deviceType">
          <div v-if="deviceType === 'phone'" class="olo-phone-home-bar"></div>
        </div>
      </div>

      <!-- Desktop / no frame -->
      <div v-else>
        <!-- Preview header -->
        <div v-if="builderStore.previewMode && builderStore.previewHeaderContent" class="olo-preview-header olo-template mb-mx-auto" v-html="builderStore.previewHeaderContent"></div>
        <div
          :class="[canvasClasses, { 'preview-mode': builderStore.previewMode }]"
          :style="canvasStyle"
          class="olo-template mb-mx-auto mb-min-h-full mb-transition-all mb-duration-300 mb-relative mb-overflow-hidden"
        >
          <div
            v-if="pageBg.type !== 'none' && pageBg.overlay_opacity > 0"
            class="mb-absolute mb-inset-0 mb-pointer-events-none"
            :style="{ backgroundColor: pageBg.overlay_color || '#000000', opacity: (pageBg.overlay_opacity || 0) / 100, zIndex: 1 }"
          ></div>
          <div
            v-if="tilesStore.canvasTiles.length === 0"
            class="mb-flex mb-flex-col mb-items-center mb-justify-center mb-h-96 mb-text-gray-500 mb-relative"
            style="z-index: 2"
          >
            <svg class="mb-mb-4 mb-opacity-20" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
              <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            <p class="mb-text-lg mb-font-medium">Trascina una tile qui per iniziare</p>
            <p class="mb-text-sm mb-mt-1 mb-opacity-60">Trascina le tile dalla barra laterale a sinistra</p>
          </div>
          <div v-else class="mb-relative" style="z-index: 2">
            <OlobuilderGrid />
          </div>
        </div>
        <!-- Preview footer -->
        <div v-if="builderStore.previewMode && builderStore.previewFooterContent" class="olo-preview-footer olo-template mb-mx-auto" v-html="builderStore.previewFooterContent"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, watchEffect, onUnmounted } from 'vue';
import { useBuilderStore } from '@/stores/builder';
import { useTilesStore } from '@/stores/tiles';
import { useStylesStore } from '@/stores/styles';
import { useDragDrop } from '@/composables/useDragDrop';
import { useInlineEdit } from '@/composables/useInlineEdit';
import OlobuilderGrid from '@/components/Grid/OlobuilderGrid.vue';

const canvasRef = ref(null);
useInlineEdit(canvasRef);

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

// Load frontend CSS (UIkit, frontend.css, style system) when preview is active
const previewCssEls = [];
watch(() => builderStore.previewMode, (active) => {
  // Cleanup previous
  previewCssEls.forEach(el => el.remove());
  previewCssEls.length = 0;

  if (active) {
    // Inject external CSS URLs from server
    for (const url of builderStore.previewCssUrls) {
      const link = document.createElement('link');
      link.rel = 'stylesheet';
      link.className = 'olo-preview-css';
      link.href = url;
      document.head.appendChild(link);
      previewCssEls.push(link);
    }
    // Inject inline CSS (style system: fonts, custom properties, etc.)
    const inlineParts = [];
    if (builderStore.previewInlineCss) {
      inlineParts.push(builderStore.previewInlineCss);
    }
    // Hide interactive overlays/modals inside preview header/footer
    inlineParts.push(`
      .olo-preview-header .olo-ls-overlay,
      .olo-preview-footer .olo-ls-overlay,
      .olo-preview-header .olo-ls-dropdown,
      .olo-preview-footer .olo-ls-dropdown,
      .olo-preview-header .uk-modal,
      .olo-preview-footer .uk-modal,
      .olo-preview-header .uk-offcanvas,
      .olo-preview-footer .uk-offcanvas,
      .olo-preview-header .uk-drop,
      .olo-preview-footer .uk-drop,
      .olo-preview-header .uk-dropdown,
      .olo-preview-footer .uk-dropdown,
      .olo-preview-header .olo-ls-modal-hint,
      .olo-preview-footer .olo-ls-modal-hint { display: none !important; }
    `);
    if (inlineParts.length) {
      const style = document.createElement('style');
      style.className = 'olo-preview-css';
      style.textContent = inlineParts.join('\n');
      document.head.appendChild(style);
      previewCssEls.push(style);
    }
  }
});

onUnmounted(() => {
  styleEl.remove();
  previewCssEls.forEach(el => el.remove());
  previewCssEls.length = 0;
});

const pageBg = computed(() => builderStore.pageSettings.page_bg);

const canvasClasses = computed(() => {
  const maxW = builderStore.pageSettings.content_max_width || 1200;
  const mode = builderStore.viewMode;
  if (mode === 'desktop' || mode === 'widescreen') {
    if (maxW >= 9999) return 'mb-max-w-full';
    return '';
  }
  // Responsive modes use inline max-width via canvasStyle
  return '';
});

const canvasStyle = computed(() => {
  const style = {};
  const maxW = builderStore.pageSettings.content_max_width || 1200;
  const bg = pageBg.value;

  // Dynamic max-width
  const mode = builderStore.viewMode;
  const bp = builderStore.pageSettings.breakpoints || {};
  if (mode === 'desktop' && maxW < 9999) {
    style.maxWidth = `${maxW}px`;
  } else if (mode === 'tablet_landscape') {
    style.maxWidth = `${bp.tablet_landscape || 1200}px`;
  } else if (mode === 'tablet') {
    style.maxWidth = `${bp.tablet || 960}px`;
  } else if (mode === 'mobile_landscape') {
    style.maxWidth = `${bp.mobile_landscape || 640}px`;
  } else if (mode === 'mobile') {
    style.maxWidth = `${bp.mobile || 480}px`;
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
    // Use style system background color, fallback to light gray
    style.backgroundColor = stylesStore.colors.background || '#f8f9fa';
    style.color = stylesStore.colors.text || '#333333';
  }

  // Container queries for responsive preview
  style.containerType = 'inline-size';
  style.containerName = 'olo-canvas';

  return style;
});

const deviceFrame = computed(() => {
  const mode = builderStore.viewMode;
  return mode === 'mobile' || mode === 'mobile_landscape' || mode === 'tablet' || mode === 'tablet_landscape';
});

const deviceType = computed(() => {
  const mode = builderStore.viewMode;
  if (mode === 'mobile' || mode === 'mobile_landscape') return 'phone';
  return 'tablet';
});

const isLandscape = computed(() => {
  const mode = builderStore.viewMode;
  return mode === 'mobile_landscape' || mode === 'tablet_landscape';
});

// Proporzioni reali medie dei dispositivi (CSS pixels)
// Risoluzioni CSS reali: FHD phone 1920×1080 (CSS ~640×360 @3x), iPad 1024×768
const deviceWrapperStyle = computed(() => {
  const style = {};
  const mode = builderStore.viewMode;
  const bp = builderStore.pageSettings.breakpoints || {};
  const bezel = 16;
  let w = 480;
  if (mode === 'tablet_landscape') w = bp.tablet_landscape || 1024;
  else if (mode === 'tablet') w = bp.tablet || 768;
  else if (mode === 'mobile_landscape') w = bp.mobile_landscape || 640;
  else if (mode === 'mobile') w = bp.mobile || 390;
  style.maxWidth = `${w + bezel * 2}px`;
  style.padding = `0 ${bezel}px`;
  if (mode === 'tablet_landscape') {
    // iPad landscape 1024×768: ratio 4:3
    style.height = `min(${Math.round(w * 0.75)}px, calc(100vh - 120px))`;
  } else if (mode === 'mobile_landscape') {
    // Smartphone FHD landscape: 16:9
    style.height = `min(${Math.round(w * 0.5625)}px, calc(100vh - 120px))`;
  } else if (mode === 'tablet') {
    // iPad portrait 768×1024: ratio 3:4
    style.height = `min(${Math.round(w * 1.33)}px, calc(100vh - 120px))`;
  } else {
    // Smartphone portrait: 9:16
    style.height = `min(${Math.round(w * 1.78)}px, calc(100vh - 120px))`;
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

<style scoped>
/* === Device Frame === */
.olo-device-wrapper {
  display: flex;
  flex-direction: column;
  height: calc(100vh - 120px);
  border-radius: 28px;
  background: linear-gradient(160deg, #2a2a2a 0%, #111 40%, #1a1a1a 100%);
  box-shadow:
    0 30px 60px -15px rgba(0, 0, 0, 0.5),
    0 0 0 1px rgba(255, 255, 255, 0.08),
    inset 0 1px 0 rgba(255, 255, 255, 0.1),
    inset 0 -1px 0 rgba(255, 255, 255, 0.04);
}
.olo-device--phone {
  border-radius: 40px;
}
.olo-device--landscape {
  border-radius: 24px;
}
.olo-device--phone.olo-device--landscape {
  border-radius: 28px;
}

/* Screen wrap — occupa tutto lo spazio tra top e bottom chrome */
.olo-device-screen-wrap {
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
  min-height: 0;
}
.olo-device-screen {
  min-height: 100%;
}

/* === Chrome Top (webcam) === */
.olo-device-chrome-top {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.olo-device-chrome--phone.olo-device-chrome-top {
  height: 40px;
}
.olo-device-chrome--tablet.olo-device-chrome-top {
  height: 28px;
}
.olo-device-webcam {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: radial-gradient(circle at 30% 30%, #3a3a4a, #0a0a12);
  box-shadow: 0 0 0 2px #222, 0 0 6px rgba(59, 130, 246, 0.15);
}
.olo-device-chrome--phone .olo-device-webcam {
  width: 12px;
  height: 12px;
}

/* === Chrome Bottom === */
.olo-device-chrome-bottom {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.olo-device-chrome--phone.olo-device-chrome-bottom {
  height: 28px;
  padding-bottom: 6px;
}
.olo-device-chrome--tablet.olo-device-chrome-bottom {
  height: 20px;
}
.olo-phone-home-bar {
  width: 110px;
  height: 5px;
  border-radius: 3px;
  background: #444;
}

/* === Preview header/footer (rendered HTML from PHP) === */
.olo-preview-header,
.olo-preview-footer {
  pointer-events: none;
  position: relative;
}
.olo-preview-header {
  border-bottom: 2px dashed rgba(99, 102, 241, 0.3);
}
.olo-preview-footer {
  border-top: 2px dashed rgba(99, 102, 241, 0.3);
}
</style>
