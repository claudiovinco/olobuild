<template>
  <div class="mb-flex mb-flex-col mb-flex-1 mb-overflow-hidden">
    <!-- ═══ LIVE IFRAME PREVIEW ═══ -->
    <div v-if="builderStore.livePreviewMode" class="mb-flex-1 mb-relative mb-overflow-hidden" style="background: #f3f4f6"
      @dragover.prevent="onIframeDragOver"
      @dragleave="onIframeDragLeave"
      @drop.prevent="onIframeDrop"
    >
      <iframe
        ref="iframeRef"
        :src="iframeSrc"
        class="olo-live-iframe"
        :style="iframeStyle"
        allow="autoplay"
      ></iframe>
      <ContextMenu ref="iframeContextMenuRef" />
    </div>

    <!-- ═══ CLASSIC VUE CANVAS (fallback) ═══ -->
    <div v-else
      ref="canvasRef"
      :class="['mb-flex-1 mb-bg-white', deviceFrame ? 'mb-overflow-hidden' : 'mb-overflow-y-auto', builderStore.cleanMode && !builderStore.previewMode ? 'mb-p-0' : 'mb-p-6']"
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
          <!-- ═══ Unified: Header zone ═══ -->
          <div v-if="builderStore.unifiedMode && builderStore.headerTemplate && !builderStore.previewMode"
            class="olo-unified-zone olo-unified-zone--header"
            :class="{ 'olo-unified-zone--active': builderStore.activeZone === 'header', 'olo-unified-zone--clean': builderStore.cleanMode }"
            @click.self="onZoneClick('header')"
          >
            <div v-if="!builderStore.cleanMode" class="olo-zone-label">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="6" rx="2"/><line x1="3" y1="13" x2="21" y2="13" opacity="0.3"/><line x1="3" y1="17" x2="21" y2="17" opacity="0.3"/></svg>
              <span>Header</span>
            </div>
            <div :class="['olo-template', { 'clean-mode': builderStore.cleanMode }]">
              <OlobuilderGrid zone="header" />
            </div>
          </div>
          <!-- Preview header (non-unified or preview mode) -->
          <div v-else-if="builderStore.previewMode && builderStore.previewHeaderContent" class="olo-preview-header olo-template" v-html="builderStore.previewHeaderContent"></div>

          <!-- ═══ Body zone ═══ -->
          <div
            v-if="builderStore.unifiedMode && !builderStore.previewMode"
            class="olo-unified-zone olo-unified-zone--body"
            :class="{ 'olo-unified-zone--active': builderStore.activeZone === 'body', 'olo-unified-zone--clean': builderStore.cleanMode }"
            @click.self="onZoneClick('body')"
          >
            <div v-if="!builderStore.cleanMode" class="olo-zone-label">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/></svg>
              <span>Body</span>
            </div>
            <div
              :class="[canvasClasses, { 'clean-mode': builderStore.cleanMode, 'wireframe-mode': builderStore.wireframeMode }]"
              :style="canvasStyle"
              class="olo-template olo-device-screen mb-mx-auto mb-relative"
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
                <OlobuilderGrid zone="body" />
              </div>
            </div>
          </div>
          <!-- Non-unified body (original) -->
          <div v-else-if="!builderStore.unifiedMode || builderStore.previewMode"
            :class="[canvasClasses, { 'preview-mode': builderStore.previewMode, 'clean-mode': builderStore.cleanMode && !builderStore.previewMode, 'wireframe-mode': builderStore.wireframeMode }]"
            :style="canvasStyle"
            class="olo-template olo-device-screen mb-mx-auto mb-relative"
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

          <!-- ═══ Unified: Footer zone ═══ -->
          <div v-if="builderStore.unifiedMode && builderStore.footerTemplate && !builderStore.previewMode"
            class="olo-unified-zone olo-unified-zone--footer"
            :class="{ 'olo-unified-zone--active': builderStore.activeZone === 'footer', 'olo-unified-zone--clean': builderStore.cleanMode }"
            @click.self="onZoneClick('footer')"
          >
            <div v-if="!builderStore.cleanMode" class="olo-zone-label">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="6" rx="2" opacity="0.3"/><line x1="3" y1="13" x2="21" y2="13" opacity="0.3"/><rect x="3" y="17" width="18" height="4" rx="1"/></svg>
              <span>Footer</span>
            </div>
            <div :class="['olo-template', { 'clean-mode': builderStore.cleanMode }]">
              <OlobuilderGrid zone="footer" />
            </div>
          </div>
          <!-- Preview footer (non-unified or preview mode) -->
          <div v-else-if="builderStore.previewMode && builderStore.previewFooterContent" class="olo-preview-footer olo-template" v-html="builderStore.previewFooterContent"></div>
        </div>
        <!-- Device chrome bottom (home bar) -->
        <div class="olo-device-chrome-bottom" :class="'olo-device-chrome--' + deviceType">
          <div v-if="deviceType === 'phone'" class="olo-phone-home-bar"></div>
        </div>
      </div>

      <!-- Desktop / no frame -->
      <div v-else>
        <!-- ═══ Unified: Header zone (desktop) ═══ -->
        <div v-if="builderStore.unifiedMode && builderStore.headerTemplate && !builderStore.previewMode"
          class="olo-unified-zone olo-unified-zone--header mb-mx-auto"
          :class="{ 'olo-unified-zone--active': builderStore.activeZone === 'header', 'olo-unified-zone--clean': builderStore.cleanMode }"
          @click.self="onZoneClick('header')"
        >
          <div v-if="!builderStore.cleanMode" class="olo-zone-label">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="6" rx="2"/><line x1="3" y1="13" x2="21" y2="13" opacity="0.3"/><line x1="3" y1="17" x2="21" y2="17" opacity="0.3"/></svg>
            <span>Header</span>
          </div>
          <div :class="['olo-template', { 'clean-mode': builderStore.cleanMode }]">
            <OlobuilderGrid zone="header" />
          </div>
        </div>
        <!-- Preview header -->
        <div v-else-if="builderStore.previewMode && builderStore.previewHeaderContent" class="olo-preview-header olo-template mb-mx-auto" v-html="builderStore.previewHeaderContent"></div>

        <!-- ═══ Body zone (desktop) ═══ -->
        <div v-if="builderStore.unifiedMode && !builderStore.previewMode"
          class="olo-unified-zone olo-unified-zone--body mb-mx-auto"
          :class="{ 'olo-unified-zone--active': builderStore.activeZone === 'body', 'olo-unified-zone--clean': builderStore.cleanMode }"
          @click.self="onZoneClick('body')"
        >
          <div v-if="!builderStore.cleanMode" class="olo-zone-label">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/></svg>
            <span>Body</span>
          </div>
          <div
            :class="[canvasClasses, { 'clean-mode': builderStore.cleanMode, 'wireframe-mode': builderStore.wireframeMode }]"
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
              <OlobuilderGrid zone="body" />
            </div>
          </div>
        </div>
        <!-- Non-unified body (original) -->
        <div v-else-if="!builderStore.unifiedMode || builderStore.previewMode"
          :class="[canvasClasses, { 'preview-mode': builderStore.previewMode, 'clean-mode': builderStore.cleanMode && !builderStore.previewMode, 'wireframe-mode': builderStore.wireframeMode }]"
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

        <!-- ═══ Unified: Footer zone (desktop) ═══ -->
        <div v-if="builderStore.unifiedMode && builderStore.footerTemplate && !builderStore.previewMode"
          class="olo-unified-zone olo-unified-zone--footer mb-mx-auto"
          :class="{ 'olo-unified-zone--active': builderStore.activeZone === 'footer', 'olo-unified-zone--clean': builderStore.cleanMode }"
          @click.self="onZoneClick('footer')"
        >
          <div v-if="!builderStore.cleanMode" class="olo-zone-label">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="6" rx="2" opacity="0.3"/><line x1="3" y1="13" x2="21" y2="13" opacity="0.3"/><rect x="3" y="17" width="18" height="4" rx="1"/></svg>
            <span>Footer</span>
          </div>
          <div :class="['olo-template', { 'clean-mode': builderStore.cleanMode }]">
            <OlobuilderGrid zone="footer" />
          </div>
        </div>
        <!-- Preview footer -->
        <div v-else-if="builderStore.previewMode && builderStore.previewFooterContent" class="olo-preview-footer olo-template mb-mx-auto" v-html="builderStore.previewFooterContent"></div>
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
import { useIframeBridge } from '@/composables/useIframeBridge';
import OlobuilderGrid from '@/components/Grid/OlobuilderGrid.vue';
import ContextMenu from '@/components/Builder/ContextMenu.vue';

const canvasRef = ref(null);
const iframeRef = ref(null);
const iframeContextMenuRef = ref(null);
const emit = defineEmits(['zone-click']);
useInlineEdit(canvasRef);

const builderStore = useBuilderStore();
const tilesStore = useTilesStore();
const stylesStore = useStylesStore();
const { handleDropFromSidebar } = useDragDrop();

// ── Live iframe preview ──
const { iframeReady, iframeHeight, postToIframe } = useIframeBridge(iframeRef);

const iframeSrc = computed(() => {
  const base = window.oloData?.home_url || window.location.origin;
  return base + '?olo_builder_iframe=1';
});

const iframeStyle = computed(() => {
  const mode = builderStore.viewMode;
  const widths = { desktop: '100%', tablet: '768px', 'tablet-landscape': '1024px', mobile: '375px', 'mobile-landscape': '667px' };
  const w = widths[mode] || '100%';
  const zoom = builderStore.canvasZoom / 100;
  const style = {
    width: w,
    height: '100%',
    maxWidth: '100%',
    border: 'none',
    display: 'block',
    margin: w === '100%' ? '0' : '0 auto',
    transition: 'width 0.3s ease, transform 0.2s ease',
    background: '#fff',
  };
  if (zoom !== 1) {
    style.transform = `scale(${zoom})`;
    style.transformOrigin = 'top center';
    // Compensate width so scaled iframe fills/fits its container
    if (w === '100%') {
      style.width = (100 / zoom) + '%';
    }
    style.height = (100 / zoom) + '%';
  }
  return style;
});

function onIframeDragOver(e) {
  e.dataTransfer.dropEffect = 'copy';
  // Forward coordinates to iframe for drop indicator
  const iframe = iframeRef.value;
  if (iframe) {
    const rect = iframe.getBoundingClientRect();
    postToIframe('olo:drag-over', { y: e.clientY - rect.top });
  }
}

function onIframeDragLeave() {
  postToIframe('olo:drag-leave');
}

function onIframeDrop(e) {
  postToIframe('olo:drag-leave');
  const tileType = e.dataTransfer.getData('tile-type');
  if (tileType) {
    handleDropFromSidebar(tileType);
  }
}

// ── Context menu from iframe ──
watch(() => builderStore._iframeContextMenu, (ctx) => {
  if (ctx && ctx.tileId && iframeContextMenuRef.value) {
    iframeContextMenuRef.value.open({ clientX: ctx.x, clientY: ctx.y, preventDefault() {}, stopPropagation() {} }, ctx.tileId);
    builderStore._iframeContextMenu = null;
  }
});


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

function onZoneClick(zone) {
  builderStore.setActiveZone(zone);
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

/* === Unified Editing Zones === */
.olo-unified-zone {
  position: relative;
  transition: box-shadow 0.2s, border-color 0.2s;
}
.olo-unified-zone--header {
  border-bottom: 2px dashed rgba(59, 130, 246, 0.25);
}
.olo-unified-zone--body {
  min-height: 200px;
}
.olo-unified-zone--footer {
  border-top: 2px dashed rgba(59, 130, 246, 0.25);
}
.olo-unified-zone--active {
  box-shadow: inset 0 0 0 2px rgba(99, 102, 241, 0.35);
}
.olo-unified-zone--active.olo-unified-zone--header {
  border-bottom-color: rgba(99, 102, 241, 0.5);
}
.olo-unified-zone--active.olo-unified-zone--footer {
  border-top-color: rgba(99, 102, 241, 0.5);
}

/* Zone label */
.olo-zone-label {
  position: sticky;
  top: 0;
  z-index: 30;
  display: flex;
  align-items: center;
  gap: 5px;
  padding: 3px 10px;
  font-size: 10px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #9CA3AF;
  background: rgba(17, 24, 39, 0.85);
  backdrop-filter: blur(8px);
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
  cursor: pointer;
  user-select: none;
}
.olo-unified-zone--active .olo-zone-label {
  color: #A5B4FC;
  background: rgba(99, 102, 241, 0.12);
  border-bottom-color: rgba(99, 102, 241, 0.2);
}
.olo-zone-label:hover {
  color: #C8CCD0;
}

/* Clean mode: zones merge seamlessly — zero gap */
.olo-unified-zone--clean {
  box-shadow: none !important;
  margin: 0 !important;
  padding: 0 !important;
}
.olo-unified-zone--clean.olo-unified-zone--header {
  border-bottom: none;
}
.olo-unified-zone--clean.olo-unified-zone--footer {
  border-top: none;
}
/* In clean mode, subtle separator on hover between zones */
.olo-unified-zone--clean:hover {
  outline: 1px dashed rgba(99, 102, 241, 0.15);
  outline-offset: -1px;
}
.olo-unified-zone--clean.olo-unified-zone--active {
  outline: 1px dashed rgba(99, 102, 241, 0.3);
  outline-offset: -1px;
}
/* Reduce gap between zones in normal editing mode */
.olo-unified-zone--header + .olo-unified-zone--body {
  margin-top: 0;
}
.olo-unified-zone--body + .olo-unified-zone--footer {
  margin-top: 0;
}
/* === Live iframe preview === */
.olo-live-iframe {
  border: none;
  display: block;
  background: #fff;
}

/* === Wireframe / Gabbia mode === */
.wireframe-mode :deep(.olo-section-body) {
  outline: 2px dashed rgba(245, 158, 11, 0.35) !important;
  outline-offset: -2px;
  position: relative;
}
.wireframe-mode :deep(.olo-row-block) {
  outline: 1px dashed rgba(99, 102, 241, 0.35) !important;
  outline-offset: -1px;
}
.wireframe-mode :deep(.olo-grid-cell) {
  outline: 1px solid rgba(59, 130, 246, 0.2) !important;
  outline-offset: -1px;
}
.wireframe-mode :deep(.olo-grid-cell:hover) {
  outline-color: rgba(59, 130, 246, 0.5) !important;
}
.wireframe-mode :deep(.olo-section-bar)::after {
  content: 'SEZIONE';
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 1px;
  color: rgba(245, 158, 11, 0.6);
  margin-left: 8px;
}
.wireframe-mode :deep(.olo-row-bar)::after {
  content: 'RIGA';
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 1px;
  color: rgba(99, 102, 241, 0.5);
  margin-left: 8px;
}
</style>
