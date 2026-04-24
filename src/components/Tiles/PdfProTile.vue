<template>
  <div :style="containerStyle">
    <!-- Toolbar -->
    <div v-if="s.show_toolbar" class="olo-pdfpro-toolbar" :style="toolbarStyle">
      <div class="olo-pdfpro-toolbar__left">
        <span class="olo-pdfpro-mode-badge">{{ modeLabel }}</span>
        <span v-if="hotspotCount > 0" class="olo-pdfpro-hs-badge">
          {{ hotspotCount }} hotspot
        </span>
      </div>
      <div class="olo-pdfpro-toolbar__controls">
        <span v-if="s.show_page_nav !== false" class="olo-pdfpro-tool" :title="t('Navigazione pagine')">
          <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor"><path d="M4.5 2l-4 6 4 6h3L3.5 8l4-6z"/><path d="M11.5 2l4 6-4 6h-3l4-6-4-6z"/></svg>
        </span>
        <span v-if="s.show_zoom !== false" class="olo-pdfpro-tool" :title="t('Zoom')">
          <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="6.5" cy="6.5" r="4.5"/><line x1="10" y1="10" x2="14" y2="14"/></svg>
        </span>
        <span v-if="s.show_fullscreen !== false" class="olo-pdfpro-tool" :title="t('Schermo intero')">
          <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor"><path d="M2 2h5V0H0v7h2V2zM14 2h-5V0h7v7h-2V2zM2 14h5v2H0V9h2v5zM14 14h-5v2h7V9h-2v5z"/></svg>
        </span>
        <span v-if="s.show_search" class="olo-pdfpro-tool" :title="t('Cerca')">
          <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="6" cy="6" r="4"/><line x1="9" y1="9" x2="14" y2="14"/></svg>
        </span>
        <span v-if="s.show_thumbnails" class="olo-pdfpro-tool" :title="t('Miniature')">
          <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor"><rect x="1" y="1" width="6" height="6" rx="1"/><rect x="9" y="1" width="6" height="6" rx="1"/><rect x="1" y="9" width="6" height="6" rx="1"/><rect x="9" y="9" width="6" height="6" rx="1"/></svg>
        </span>
        <span v-if="s.show_download !== false" class="olo-pdfpro-tool" :title="t('Download')">
          <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor"><path d="M8 0v10M8 10l-3-3M8 10l3-3M2 13h12v2H2z"/></svg>
        </span>
        <span v-if="s.show_print !== false" class="olo-pdfpro-tool" :title="t('Stampa')">
          <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor"><path d="M4 0h8v4H4zM2 5h12a1 1 0 011 1v5H1V6a1 1 0 011-1zM4 12h8v4H4z"/></svg>
        </span>
      </div>
      <span class="olo-pdfpro-label">{{ t('PDF Pro') }}</span>
    </div>

    <!-- PDF viewer area -->
    <div class="olo-pdfpro-viewer" :style="viewerBgStyle">
      <!-- Loading -->
      <div v-if="loading" class="olo-pdfpro-status">
        <span class="olo-pdfpro-spinner"></span>
        <span>{{ t('Caricamento PDF...') }}</span>
      </div>

      <!-- Error -->
      <div v-else-if="error" class="olo-pdfpro-status">
        <span style="font-size:20px">&#9888;</span>
        <span>{{ error }}</span>
      </div>

      <!-- Real PDF pages -->
      <div v-else-if="pageImages.length > 0" class="olo-pdfpro-pages" :class="'olo-pdfpro-pages--' + (s.mode || 'flipbook')">
        <!-- Flipbook / Double -->
        <template v-if="s.mode === 'flipbook' || s.mode === 'double'">
          <div class="olo-pdfpro-page-wrap">
            <img v-if="pageImages[0]" :src="pageImages[0]" class="olo-pdfpro-page-img" />
            <!-- Hotspots page 1 -->
            <div
              v-for="(hs, hi) in page1Hotspots"
              :key="'hs1-' + hi"
              class="olo-pdfpro-hotspot"
              :class="{ 'olo-pdfpro-hotspot--pulse': s.hotspot_pulse !== false }"
              :style="hotspotStyle(hs)"
              :title="hs.title"
            ></div>
          </div>
          <div class="olo-pdfpro-fold" :style="foldStyle"></div>
          <div class="olo-pdfpro-page-wrap">
            <img v-if="pageImages[1]" :src="pageImages[1]" class="olo-pdfpro-page-img" />
            <div v-else class="olo-pdfpro-page-empty">{{ t('Ultima pagina') }}</div>
          </div>
        </template>

        <!-- Single -->
        <template v-else-if="s.mode === 'single'">
          <div class="olo-pdfpro-page-wrap olo-pdfpro-page-wrap--single">
            <img v-if="pageImages[0]" :src="pageImages[0]" class="olo-pdfpro-page-img" />
            <div
              v-for="(hs, hi) in page1Hotspots"
              :key="'hs-' + hi"
              class="olo-pdfpro-hotspot"
              :class="{ 'olo-pdfpro-hotspot--pulse': s.hotspot_pulse !== false }"
              :style="hotspotStyle(hs)"
              :title="hs.title"
            ></div>
          </div>
        </template>

        <!-- Scroll -->
        <template v-else-if="s.mode === 'scroll'">
          <div class="olo-pdfpro-scroll-stack">
            <div v-for="(img, pi) in pageImages" :key="'pg-' + pi" class="olo-pdfpro-page-wrap olo-pdfpro-page-wrap--scroll">
              <img :src="img" class="olo-pdfpro-page-img" />
              <template v-if="pi === 0">
                <div
                  v-for="(hs, hi) in page1Hotspots"
                  :key="'hss-' + hi"
                  class="olo-pdfpro-hotspot"
                  :class="{ 'olo-pdfpro-hotspot--pulse': s.hotspot_pulse !== false }"
                  :style="hotspotStyle(hs)"
                  :title="hs.title"
                ></div>
              </template>
            </div>
          </div>
        </template>
      </div>

      <!-- No PDF: placeholder -->
      <div v-else class="olo-pdfpro-status">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor" style="opacity:.25">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z"/>
        </svg>
        <span class="olo-pdfpro-nofile">Seleziona un file PDF</span>
      </div>

      <!-- File name -->
      <div v-if="fileName" class="olo-pdfpro-filename" :style="filenameStyle">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="opacity:.4">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z"/>
        </svg>
        {{ fileName }}
      </div>
    </div>

    <!-- Bottom bar (preview dei toggle show_bottombar_*) -->
    <div v-if="showBottomBar" class="olo-pdfpro-bottombar">
      <span v-if="s.show_bottombar_pages !== false" class="olo-pdfpro-bb-info">{{ currentPage }} / {{ totalPages }}</span>
      <div v-if="s.show_bottombar_pages !== false" class="olo-pdfpro-bb-range">
        <div class="olo-pdfpro-bb-track"></div>
        <div class="olo-pdfpro-bb-thumb" :style="{ left: thumbPercent + '%' }"></div>
      </div>
      <span v-if="s.show_bottombar_zoom" class="olo-pdfpro-bb-btn" :title="t('Riduci')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
      </span>
      <span v-if="s.show_bottombar_zoom" class="olo-pdfpro-bb-btn" :title="t('Ingrandisci')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
      </span>
      <span v-if="s.show_bottombar_fullscreen" class="olo-pdfpro-bb-btn" :title="t('Schermo intero')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/></svg>
      </span>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, computed, watch, onMounted } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({
  pdf_url: '',
  mode: 'flipbook',
  viewer_height: '600',
  start_page: '1',
  initial_zoom: 'fit-width',
  theme: 'light',
  bg_color: '#f5f5f5',
  show_toolbar: true,
  show_page_nav: true,
  show_zoom: true,
  show_fullscreen: true,
  show_download: true,
  show_print: true,
  show_search: false,
  show_thumbnails: false,
  show_bottombar: true,
  show_bottombar_pages: true,
  show_bottombar_zoom: false,
  show_bottombar_fullscreen: false,
  hotspots: [],
  hotspot_color: '#EF4444',
  hotspot_size: '14',
  hotspot_pulse: true,
  border_width: '0',
  border_color: '#e5e7eb',
  border_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
  ...props.settings,
}));

const isDark = computed(() => s.value.theme === 'dark');

// --- PDF.js rendering ---
const pageImages = ref([]);
const loading = ref(false);
const error = ref('');
let currentLoadId = 0;

const PDFJS_CDN = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174';
let pdfjsLoaded = null;

function loadPdfJs() {
  if (pdfjsLoaded) return pdfjsLoaded;
  if (window.pdfjsLib) {
    pdfjsLoaded = Promise.resolve(window.pdfjsLib);
    return pdfjsLoaded;
  }
  pdfjsLoaded = new Promise((resolve, reject) => {
    const script = document.createElement('script');
    script.src = PDFJS_CDN + '/pdf.min.js';
    script.onload = () => {
      const lib = window.pdfjsLib;
      lib.GlobalWorkerOptions.workerSrc = PDFJS_CDN + '/pdf.worker.min.js';
      resolve(lib);
    };
    script.onerror = () => reject(new Error('Errore caricamento PDF.js'));
    document.head.appendChild(script);
  });
  return pdfjsLoaded;
}

async function renderPages(url, startPage, mode) {
  const loadId = ++currentLoadId;
  if (!url) {
    pageImages.value = [];
    error.value = '';
    return;
  }

  loading.value = true;
  error.value = '';
  pageImages.value = [];

  try {
    const pdfjsLib = await loadPdfJs();
    const pdf = await pdfjsLib.getDocument(url).promise;

    if (loadId !== currentLoadId) return; // stale

    const start = Math.max(1, Math.min(pdf.numPages, parseInt(startPage) || 1));
    // How many pages to render based on mode
    let pagesToRender;
    if (mode === 'scroll') {
      pagesToRender = Math.min(3, pdf.numPages - start + 1);
    } else if (mode === 'flipbook' || mode === 'double') {
      pagesToRender = Math.min(2, pdf.numPages - start + 1);
    } else {
      pagesToRender = 1;
    }

    const images = [];
    for (let i = 0; i < pagesToRender; i++) {
      const pageNum = start + i;
      if (pageNum > pdf.numPages) break;
      const page = await pdf.getPage(pageNum);
      if (loadId !== currentLoadId) return;

      // Render at reasonable resolution for preview
      const viewport = page.getViewport({ scale: 1 });
      const maxH = 400;
      const scale = Math.min(maxH / viewport.height, 1.5);
      const scaledViewport = page.getViewport({ scale });

      const canvas = document.createElement('canvas');
      canvas.width = scaledViewport.width;
      canvas.height = scaledViewport.height;
      const ctx = canvas.getContext('2d');

      await page.render({ canvasContext: ctx, viewport: scaledViewport }).promise;
      if (loadId !== currentLoadId) return;

      images.push(canvas.toDataURL('image/jpeg', 0.85));
    }

    pageImages.value = images;
  } catch (e) {
    if (loadId !== currentLoadId) return;
    error.value = 'Impossibile caricare il PDF';
    console.warn('PDF Pro preview error:', e);
  } finally {
    if (loadId === currentLoadId) {
      loading.value = false;
    }
  }
}

// Watch for changes and re-render
watch(
  () => [s.value.pdf_url, s.value.start_page, s.value.mode],
  ([url, page, mode]) => renderPages(url, page, mode),
  { immediate: true }
);

// --- Styles ---
const containerStyle = computed(() => {
  const bw = parseInt(s.value.border_width) || 0;
  const br = s.value.border_radius || {};
  const style = {
    height: (parseInt(s.value.viewer_height) || 600) + 'px',
    borderRadius: `${br.tl || 0}px ${br.tr || 0}px ${br.br || 0}px ${br.bl || 0}px`,
    overflow: 'hidden',
    display: 'flex',
    flexDirection: 'column',
  };
  style.border = bw > 0
    ? bw + 'px solid ' + (s.value.border_color || '#e5e7eb')
    : '1px solid ' + (isDark.value ? '#374151' : 'var(--olo-color-border, #e5e7eb)');
  return style;
});

const toolbarStyle = computed(() => ({
  background: isDark.value ? '#1f2937' : 'var(--olo-color-background, #ffffff)',
  color: isDark.value ? '#e5e7eb' : 'var(--olo-color-text, #374151)',
  borderBottom: '1px solid ' + (isDark.value ? '#374151' : 'var(--olo-color-border, #e5e7eb)'),
}));

const viewerBgStyle = computed(() => ({
  background: s.value.bg_color || '#f5f5f5',
  flex: 1,
}));

const foldStyle = computed(() => ({
  background: isDark.value
    ? 'linear-gradient(90deg, rgba(0,0,0,.25), rgba(0,0,0,.05) 40%, rgba(0,0,0,.05) 60%, rgba(0,0,0,.25))'
    : 'linear-gradient(90deg, rgba(0,0,0,.1), rgba(0,0,0,.02) 40%, rgba(0,0,0,.02) 60%, rgba(0,0,0,.1))',
}));

const filenameStyle = computed(() => ({
  color: isDark.value ? '#d1d5db' : '#6b7280',
  background: isDark.value ? 'rgba(0,0,0,.5)' : 'rgba(255,255,255,.8)',
}));

const modeLabel = computed(() => {
  const map = { flipbook: 'Flipbook', single: 'Pagina singola', double: 'Doppia pagina', scroll: 'Scroll' };
  return map[s.value.mode] || 'Flipbook';
});

const hotspotCount = computed(() => {
  const hs = s.value.hotspots;
  return Array.isArray(hs) ? hs.length : 0;
});

const page1Hotspots = computed(() => {
  const hs = s.value.hotspots;
  if (!Array.isArray(hs)) return [];
  const startPage = parseInt(s.value.start_page) || 1;
  return hs.filter(h => (parseInt(h.page) || 1) === startPage);
});

function hotspotStyle(hs) {
  const size = parseInt(s.value.hotspot_size) || 14;
  const color = hs.color || s.value.hotspot_color || '#EF4444';
  return {
    width: size + 'px',
    height: size + 'px',
    background: color,
    left: (parseFloat(hs.x) || 50) + '%',
    top: (parseFloat(hs.y) || 50) + '%',
    boxShadow: '0 0 0 3px ' + color + '40',
  };
}

const fileName = computed(() => {
  const url = s.value.pdf_url;
  if (!url) return '';
  return url.split('/').pop().split('?')[0];
});

// Bottom bar preview state
const showBottomBar = computed(() => {
  if (!s.value.show_bottombar) return false;
  return s.value.show_bottombar_pages || s.value.show_bottombar_zoom || s.value.show_bottombar_fullscreen;
});
const currentPage = computed(() => {
  const p = parseInt(s.value.start_page) || 1;
  return p;
});
const totalPages = computed(() => pageImages.value.length || (s.value.pdf_url ? '…' : 0));
const thumbPercent = computed(() => {
  const cur = currentPage.value;
  const tot = pageImages.value.length;
  if (!tot || tot < 2) return 0;
  return Math.round(((cur - 1) / (tot - 1)) * 100);
});
</script>

<style scoped>
/* Toolbar */
.olo-pdfpro-toolbar {
  display: flex;
  align-items: center;
  padding: 6px 10px;
  gap: 8px;
  flex-shrink: 0;
}
.olo-pdfpro-toolbar__left {
  display: flex;
  align-items: center;
  gap: 6px;
}
.olo-pdfpro-toolbar__controls {
  display: flex;
  align-items: center;
  gap: 4px;
  margin-left: 8px;
}
.olo-pdfpro-mode-badge {
  font-size: 10px;
  font-weight: 600;
  padding: 1px 6px;
  border-radius: 4px;
  background: rgba(139, 92, 246, .15);
  color: #8b5cf6;
}
.olo-pdfpro-hs-badge {
  font-size: 10px;
  font-weight: 600;
  padding: 1px 6px;
  border-radius: 4px;
  background: rgba(239, 68, 68, .12);
  color: #ef4444;
}
.olo-pdfpro-tool {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 22px;
  height: 22px;
  border-radius: 3px;
  opacity: .5;
  background: rgba(0,0,0,.04);
}
.olo-pdfpro-label {
  font-size: 10px;
  opacity: .4;
  margin-left: auto;
  font-weight: 600;
}

/* Viewer area */
.olo-pdfpro-viewer {
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Status / loading / error / empty */
.olo-pdfpro-status {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  color: #9ca3af;
  font-size: 12px;
}

/* Spinner */
.olo-pdfpro-spinner {
  width: 24px;
  height: 24px;
  border: 3px solid rgba(139,92,246,.2);
  border-top-color: #8b5cf6;
  border-radius: 50%;
  animation: oloPdfSpin .8s linear infinite;
}
@keyframes oloPdfSpin {
  to { transform: rotate(360deg); }
}

.olo-pdfpro-nofile {
  font-style: italic;
  opacity: .6;
  font-size: 12px;
}

/* Pages container */
.olo-pdfpro-pages {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0;
  height: 100%;
  padding: 12px;
  z-index: 1;
}

/* Page wrap */
.olo-pdfpro-page-wrap {
  position: relative;
  height: 90%;
  max-width: 45%;
  border-radius: 2px;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(0,0,0,.15);
}
.olo-pdfpro-page-wrap--single {
  max-width: 60%;
}
.olo-pdfpro-page-wrap--scroll {
  max-width: 55%;
  height: auto;
  margin-bottom: 6px;
  flex-shrink: 0;
}

.olo-pdfpro-page-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  display: block;
}
.olo-pdfpro-page-wrap--scroll .olo-pdfpro-page-img {
  height: auto;
}

.olo-pdfpro-page-empty {
  width: 120px;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f3f4f6;
  color: #9ca3af;
  font-size: 10px;
}

/* Fold line for flipbook/double */
.olo-pdfpro-fold {
  width: 4px;
  align-self: stretch;
  flex-shrink: 0;
  margin: 12px 0;
}

/* Scroll stack */
.olo-pdfpro-scroll-stack {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 8px 0;
  max-height: 100%;
  overflow-y: auto;
  width: 100%;
}

/* Hotspots */
.olo-pdfpro-hotspot {
  position: absolute;
  border-radius: 50%;
  transform: translate(-50%, -50%);
  z-index: 3;
  cursor: pointer;
}
.olo-pdfpro-hotspot--pulse {
  animation: oloPdfPulse 2s ease-in-out infinite;
}
@keyframes oloPdfPulse {
  0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
  50% { transform: translate(-50%, -50%) scale(1.4); opacity: .6; }
}

/* Filename overlay */
.olo-pdfpro-filename {
  position: absolute;
  bottom: 8px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 10px;
  font-weight: 500;
  backdrop-filter: blur(4px);
  padding: 2px 8px;
  border-radius: 4px;
  z-index: 5;
  white-space: nowrap;
}

/* Bottom bar preview */
.olo-pdfpro-bottombar {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 12px;
  flex-shrink: 0;
  background: #1e1e1e;
  color: #f0f0f0;
  border-radius: 8px;
  margin: 6px 8px 8px;
  user-select: none;
  height: 36px;
  box-sizing: border-box;
}
.olo-pdfpro-bb-info {
  font-size: 12px;
  font-variant-numeric: tabular-nums;
  white-space: nowrap;
  min-width: 50px;
}
.olo-pdfpro-bb-range {
  flex: 1;
  height: 14px;
  position: relative;
}
.olo-pdfpro-bb-track {
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 3px;
  background: rgba(255,255,255,0.25);
  border-radius: 2px;
  transform: translateY(-50%);
}
.olo-pdfpro-bb-thumb {
  position: absolute;
  top: 50%;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: #fff;
  box-shadow: 0 1px 3px rgba(0,0,0,0.4);
  transform: translate(-50%, -50%);
}
.olo-pdfpro-bb-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  border-radius: 4px;
  color: inherit;
  opacity: 0.85;
  cursor: default;
}
.olo-pdfpro-bb-btn:hover { opacity: 1; background: rgba(255,255,255,0.1); }
</style>
