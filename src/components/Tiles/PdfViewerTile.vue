<template>
  <div :style="containerStyle" class="olo-pdfv-preview">
    <!-- Top toolbar (mockup) -->
    <div
      v-if="s.show_toolbar"
      class="olo-pdfv-preview-toolbar"
      :style="toolbarStyle"
    >
      <span v-if="s.show_page_nav !== false" class="olo-pdfv-preview-btn" :title="t('Pagina precedente')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
      </span>
      <span v-if="s.show_page_nav !== false" class="olo-pdfv-preview-page">1 / {{ estimatedPages }}</span>
      <span v-if="s.show_page_nav !== false" class="olo-pdfv-preview-btn" :title="t('Pagina successiva')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 6 15 12 9 18"/></svg>
      </span>
      <span v-if="s.show_page_nav !== false" class="olo-pdfv-preview-sep"></span>
      <span v-if="s.show_zoom !== false" class="olo-pdfv-preview-btn" :title="t('Riduci')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
      </span>
      <span v-if="s.show_zoom !== false" class="olo-pdfv-preview-page">100%</span>
      <span v-if="s.show_zoom !== false" class="olo-pdfv-preview-btn" :title="t('Ingrandisci')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
      </span>
      <span class="olo-pdfv-preview-spacer"></span>
      <span v-if="s.show_thumbnails !== false" class="olo-pdfv-preview-btn" :title="t('Miniature')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
      </span>
      <span v-if="s.show_download !== false" class="olo-pdfv-preview-btn" :title="t('Scarica')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      </span>
      <span v-if="s.show_print !== false" class="olo-pdfv-preview-btn" :title="t('Stampa')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
      </span>
      <span v-if="s.show_fullscreen !== false" class="olo-pdfv-preview-btn" :title="t('Schermo intero')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/></svg>
      </span>
    </div>

    <!-- Pagina mock al centro -->
    <div class="olo-pdfv-preview-body" :style="bodyStyle">
      <div class="olo-pdfv-preview-page-mock" :style="pageMockStyle">
        <svg class="olo-pdfv-preview-pdf-icon" viewBox="0 0 24 24" fill="currentColor">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z"/>
        </svg>
        <div v-if="fileName" class="olo-pdfv-preview-fname">{{ fileName }}</div>
        <div v-else class="olo-pdfv-preview-empty">{{ t('Seleziona un file PDF') }}</div>
        <div class="olo-pdfv-preview-badge">{{ modeLabel }}</div>
      </div>
    </div>

    <!-- Bottom bar (rispetta i toggle show_bottombar_*) -->
    <div v-if="showBottomBar" class="olo-pdfv-preview-bottombar">
      <span v-if="s.show_bottombar_pages !== false" class="olo-pdfv-preview-bb-info">1 / {{ estimatedPages }}</span>
      <div v-if="s.show_bottombar_pages !== false" class="olo-pdfv-preview-bb-range">
        <div class="olo-pdfv-preview-bb-track"></div>
        <div class="olo-pdfv-preview-bb-thumb"></div>
      </div>
      <span v-if="s.show_bottombar_zoom" class="olo-pdfv-preview-bb-btn" :title="t('Riduci')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
      </span>
      <span v-if="s.show_bottombar_zoom" class="olo-pdfv-preview-bb-btn" :title="t('Ingrandisci')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
      </span>
      <span v-if="s.show_bottombar_fullscreen" class="olo-pdfv-preview-bb-btn" :title="t('Schermo intero')">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/></svg>
      </span>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
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
  show_search: false,
  show_thumbnails: false,
  show_bottombar: true,
  show_bottombar_pages: true,
  show_bottombar_zoom: false,
  show_bottombar_fullscreen: false,
  border_width: '0',
  border_color: '#e5e7eb',
  border_radius: { tl: 8, tr: 8, br: 8, bl: 8 },
  ...props.settings,
}));

const isDark = computed(() => s.value.theme === 'dark');

const containerStyle = computed(() => {
  const bw = parseInt(s.value.border_width) || 0;
  const br = s.value.border_radius || {};
  return {
    height: s.value.viewer_height + 'px',
    background: s.value.bg_color || (isDark.value ? '#1a1a1a' : '#f5f5f5'),
    borderRadius: `${br.tl || 0}px ${br.tr || 0}px ${br.br || 0}px ${br.bl || 0}px`,
    overflow: 'hidden',
    border: bw > 0 ? `${bw}px solid ${s.value.border_color || 'var(--olo-color-border, #e5e7eb)'}` : '1px solid var(--olo-color-border, #e5e7eb)',
    display: 'flex',
    flexDirection: 'column',
    boxSizing: 'border-box',
  };
});

const toolbarStyle = computed(() => ({
  background: isDark.value ? '#1e1e1e' : '#ffffff',
  color: isDark.value ? '#e0e0e0' : '#374151',
  borderBottom: '1px solid ' + (isDark.value ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.08)'),
  flexShrink: 0,
}));

const bodyStyle = computed(() => ({
  flex: 1,
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  minHeight: 0,
  padding: '16px',
}));

const pageMockStyle = computed(() => ({
  background: '#fff',
  boxShadow: '0 2px 8px rgba(0,0,0,0.15)',
  width: 'min(320px, 80%)',
  height: '100%',
  maxHeight: '100%',
  display: 'flex',
  flexDirection: 'column',
  alignItems: 'center',
  justifyContent: 'center',
  gap: '8px',
  color: '#94a3b8',
  position: 'relative',
  borderRadius: '2px',
}));

const modeLabel = computed(() => {
  const map = { flipbook: 'Flipbook', single: 'Pagina singola', double: 'Doppia pagina', scroll: 'Scroll' };
  return map[s.value.mode] || 'Flipbook';
});

const fileName = computed(() => {
  const url = s.value.pdf_url;
  if (!url) return '';
  return url.split('/').pop().split('?')[0];
});

const estimatedPages = computed(() => s.value.pdf_url ? '…' : '0');

const showBottomBar = computed(() => {
  if (!s.value.show_bottombar) return false;
  return s.value.show_bottombar_pages || s.value.show_bottombar_zoom || s.value.show_bottombar_fullscreen;
});
</script>

<style scoped>
.olo-pdfv-preview-toolbar {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 6px 10px;
  font-size: 12px;
  user-select: none;
  height: 34px;
}
.olo-pdfv-preview-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 26px;
  height: 26px;
  border-radius: 4px;
  color: inherit;
  opacity: 0.75;
  cursor: default;
}
.olo-pdfv-preview-btn:hover { opacity: 1; background: rgba(128,128,128,0.1); }
.olo-pdfv-preview-sep {
  width: 1px;
  height: 18px;
  background: rgba(128,128,128,0.25);
  margin: 0 4px;
}
.olo-pdfv-preview-page {
  font-size: 11px;
  font-variant-numeric: tabular-nums;
  opacity: 0.8;
  min-width: 44px;
  text-align: center;
}
.olo-pdfv-preview-spacer { flex: 1; }
.olo-pdfv-preview-pdf-icon {
  width: 48px;
  height: 48px;
  opacity: 0.35;
}
.olo-pdfv-preview-fname {
  font-size: 13px;
  font-weight: 500;
  color: #475569;
  max-width: 90%;
  text-align: center;
  word-break: break-word;
}
.olo-pdfv-preview-empty {
  font-size: 12px;
  font-style: italic;
  color: #94a3b8;
}
.olo-pdfv-preview-badge {
  position: absolute;
  top: 8px;
  right: 8px;
  font-size: 10px;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 999px;
  background: color-mix(in srgb, var(--olo-color-primary, #e1474f) 12%, transparent);
  color: var(--olo-color-primary, #e1474f);
  letter-spacing: 0.02em;
}
.olo-pdfv-preview-bottombar {
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
.olo-pdfv-preview-bb-info {
  font-size: 12px;
  font-variant-numeric: tabular-nums;
  white-space: nowrap;
  min-width: 50px;
}
.olo-pdfv-preview-bb-range {
  flex: 1;
  height: 14px;
  position: relative;
}
.olo-pdfv-preview-bb-track {
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 3px;
  background: rgba(255,255,255,0.25);
  border-radius: 2px;
  transform: translateY(-50%);
}
.olo-pdfv-preview-bb-thumb {
  position: absolute;
  top: 50%;
  left: 15%;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: #fff;
  box-shadow: 0 1px 3px rgba(0,0,0,0.4);
  transform: translate(-50%, -50%);
}
.olo-pdfv-preview-bb-btn {
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
.olo-pdfv-preview-bb-btn:hover { opacity: 1; background: rgba(255,255,255,0.1); }
</style>
