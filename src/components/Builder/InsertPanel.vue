<template>
  <Teleport to="body">
    <transition name="ip-fade">
      <div
        v-if="visible"
        class="mb-fixed mb-inset-0 mb-z-[99999] mb-flex mb-items-center mb-justify-center"
        @keydown.escape="close"
      >
        <!-- Backdrop -->
        <div class="mb-absolute mb-inset-0" style="background:rgba(0,0,0,0.35)" @click="close"></div>

        <!-- Panel -->
        <div
          ref="panelRef"
          class="ip-panel mb-relative"
          role="dialog"
          :aria-label="t('Inserisci modulo o riga')"
          tabindex="-1"
        >
          <!-- Header -->
          <div class="ip-header">
            <span class="ip-title">{{ t('Inserisci modulo o riga') }}</span>
            <button class="ip-close" @click="close" :aria-label="t('Chiudi')">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
          </div>

          <!-- Tabs -->
          <div class="ip-tabs">
            <button
              v-for="tab in tabs"
              :key="tab.key"
              :class="['ip-tab', { 'ip-tab--active': activeTab === tab.key }]"
              @click="activeTab = tab.key"
            >{{ t(tab.label) }}</button>
          </div>

          <!-- Search -->
          <div class="ip-search">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="ip-search-icon">
              <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
            </svg>
            <input
              ref="searchRef"
              v-model="searchQuery"
              type="text"
              :placeholder="searchPlaceholder"
              class="ip-search-input"
            />
          </div>

          <!-- Content -->
          <div class="ip-body">
            <!-- Tab: New Module — raggruppato per categoria (coerente con la Sidebar) -->
            <div v-if="activeTab === 'module'" class="ip-modules">
              <template v-for="cat in filteredModuleCategories" :key="cat.key">
                <div class="ip-cat-header">{{ cat.label }}</div>
                <div class="ip-grid">
                  <button
                    v-for="el in cat.modules"
                    :key="el.type"
                    class="ip-module-card"
                    @click="insertModule(el.type)"
                    :title="t(el.name)"
                  >
                    <span class="ip-module-icon" v-html="moduleIcon(el.type)"></span>
                    <span class="ip-module-name">{{ t(el.name) }}</span>
                  </button>
                </div>
              </template>
              <div v-if="filteredModules.length === 0" class="ip-empty">
                {{ t('Nessun modulo trovato') }}
              </div>
            </div>

            <!-- Tab: New Row -->
            <div v-if="activeTab === 'row'">
              <!-- Flex layouts -->
              <div class="ip-section-label">{{ t('Layout Flex') }}</div>
              <div class="ip-row-grid">
                <button
                  v-for="layout in rowLayouts"
                  :key="layout.key"
                  class="ip-row-card"
                  @click="insertRow(layout.key)"
                  :title="t(layout.label)"
                >
                  <div class="ip-row-preview">
                    <div
                      v-for="(w, i) in layout.cols"
                      :key="i"
                      class="ip-row-col"
                      :style="{ flex: w }"
                    ></div>
                  </div>
                  <span class="ip-row-label">{{ t(layout.label) }}</span>
                </button>
              </div>

              <!-- Grid template categories -->
              <template v-for="cat in filteredGridCategories" :key="cat.key">
                <div class="ip-section-label">{{ t(cat.label) }} <span class="ip-section-badge">{{ t('CSS Grid') }}</span></div>
                <div class="ip-row-grid">
                  <button
                    v-for="tpl in cat.templates"
                    :key="tpl.id"
                    class="ip-row-card"
                    @click="insertGridRow(tpl.id)"
                    :title="t(tpl.name)"
                  >
                    <div class="ip-row-preview ip-row-preview--grid" v-html="gridPreviewSvg(tpl)"></div>
                    <span class="ip-row-label">{{ t(tpl.name) }}</span>
                  </button>
                </div>
              </template>
            </div>

            <!-- Tab: Add From Library -->
            <div v-if="activeTab === 'library'" class="ip-library">
              <div class="ip-library-msg">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color:#9CA3AF">
                  <rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/>
                </svg>
                <p>{{ t('Apri la libreria Blocchi & Pagine per importare sezioni salvate') }}</p>
                <button class="ip-library-btn" @click="openLibrary">{{ t('Apri Libreria') }}</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue';
import { useTilesStore, createRow, createColumn, createSection } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';
import { useDragDrop } from '@/composables/useDragDrop';
import { columns as gridColumns, multirow, masonry, sidebar, TEMPLATES_MAP } from '@/config/gridTemplates';
import { t } from '@/i18n';

const tilesStore = useTilesStore();
const builderStore = useBuilderStore();
const { createTileFromType } = useDragDrop();

const emit = defineEmits(['open-library']);

const visible = ref(false);
const activeTab = ref('module');
const searchQuery = ref('');
const searchRef = ref(null);
const panelRef = ref(null);
const insertAtIndex = ref(null); // section index to insert at

const tabs = [
  { key: 'module', label: 'Nuovo modulo' },
  { key: 'row', label: 'Nuova riga' },
  { key: 'library', label: 'Da libreria' },
];

const searchPlaceholder = computed(() => {
  if (activeTab.value === 'module') return t('Cerca un modulo');
  if (activeTab.value === 'row') return t('Cerca layout riga');
  return t('Cerca in libreria');
});

// Structural types to exclude
const STRUCTURAL = new Set(['section', 'row', 'column', 'inner-columns']);

// Available modules
const availableModules = computed(() => {
  const all = [];
  for (const t of tilesStore.registeredTiles) {
    if (!STRUCTURAL.has(t.type)) {
      all.push(t);
    }
  }
  return all.sort((a, b) => (a.name || '').localeCompare(b.name || ''));
});

const filteredModules = computed(() => {
  const q = searchQuery.value.trim().toLowerCase();
  if (!q) return availableModules.value;
  return availableModules.value.filter(el => {
    const text = `${el.type} ${el.name} ${el.category || ''}`.toLowerCase();
    return text.includes(q);
  });
});

// Raggruppamento per categoria (stesso ordine/etichette della BuilderSidebar).
const moduleCategoryOrder = [
  'essential', 'layout', 'text', 'media', 'marketing',
  'interactive', 'atmosphere', 'navigation', 'dynamic', 'woocommerce', 'booking', 'olo-space',
];
const moduleCategoryLabels = {
  essential: 'Essenziale', layout: 'Layout', text: 'Testo', media: 'Media',
  marketing: 'Marketing', interactive: 'Interattivo', navigation: 'Navigazione',
  dynamic: 'Dinamico', booking: 'Olo Booking', 'olo-space': 'Olo Space',
  atmosphere: 'Atmosfera', woocommerce: 'WooCommerce',
};
const filteredModuleCategories = computed(() => {
  const groups = {};
  for (const el of filteredModules.value) {
    const cat = el.category || 'other';
    (groups[cat] = groups[cat] || []).push(el);
  }
  const ordered = [];
  for (const key of moduleCategoryOrder) {
    if (groups[key] && groups[key].length) {
      ordered.push({ key, label: t(moduleCategoryLabels[key] || key), modules: groups[key] });
      delete groups[key];
    }
  }
  // Eventuali categorie non previste, in coda (etichetta = chiave).
  for (const key of Object.keys(groups)) {
    ordered.push({ key, label: t(moduleCategoryLabels[key] || key), modules: groups[key] });
  }
  return ordered;
});

// Row layouts
const rowLayouts = [
  { key: '100', label: 'Full Width', cols: [1] },
  { key: '50-50', label: '1/2 + 1/2', cols: [1, 1] },
  { key: '33-33-33', label: '1/3 + 1/3 + 1/3', cols: [1, 1, 1] },
  { key: '25-25-25-25', label: '1/4 x 4', cols: [1, 1, 1, 1] },
  { key: '66-33', label: '2/3 + 1/3', cols: [2, 1] },
  { key: '33-66', label: '1/3 + 2/3', cols: [1, 2] },
  { key: '25-50-25', label: '1/4 + 1/2 + 1/4', cols: [1, 2, 1] },
  { key: '20-20-20-20-20', label: '1/5 x 5', cols: [1, 1, 1, 1, 1] },
  { key: '16-16-16-16-16-16', label: '1/6 x 6', cols: [1, 1, 1, 1, 1, 1] },
];

// Module icons (same SVGs from StructureTree + extras)
// ── Registry icone tile (1 di 3) ──────────────────────────────────────────
// Esistono TRE set di icone tile, specializzati per contesto e NON unificabili
// in un solo dizionario (gli SVG differiscono per dimensione/formato):
//   1. QUI moduleIcon()              → 24×24 espanso, card del pannello Inserisci
//   2. BuilderSidebar.tileIcons      → 24×24 shorthand (w=/h=/vb=…), card sidebar
//   3. StructureTree.nodeIcon()      → 14×14 path semplificati, albero struttura
// Invariante da mantenere: le CHIAVI (i tipi tile) devono restare allineate nei
// tre punti. Quando aggiungi un tile, registra la sua icona in tutti e tre.
var _iconCache = {};
function moduleIcon(type) {
  if (_iconCache[type]) return _iconCache[type];
  var S = ' stroke="currentColor" stroke-width="1.5"', V = ' viewBox="0 0 24 24" fill="none"';
  var icons = {
    // ── Essential ──
    heading: '<svg width="24" height="24"'+V+S+' stroke-width="2"><path d="M4 4v16M20 4v16M4 12h16"/></svg>',
    headline: '<svg width="24" height="24"'+V+S+' stroke-width="2"><path d="M4 4v16M20 4v16M4 12h16"/></svg>',
    textblock: '<svg width="24" height="24"'+V+S+'><path d="M4 5h16M4 9h12M4 13h16M4 17h8"/></svg>',
    'text-block': '<svg width="24" height="24"'+V+S+'><path d="M4 5h16M4 9h12M4 13h16M4 17h8"/></svg>',
    content: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M7 7h10M7 11h8M7 15h6"/></svg>',
    image: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="2"/><path d="M3 16l5-5 4 4 4-6 5 7"/></svg>',
    button: '<svg width="24" height="24"'+V+S+'><rect x="2" y="7" width="20" height="10" rx="3"/><line x1="7" y1="12" x2="17" y2="12"/></svg>',
    video: '<svg width="24" height="24"'+V+S+'><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M10 8v8l6-4z" fill="currentColor" stroke="none"/></svg>',
    icon: '<svg width="24" height="24"'+V+S+'><path d="M12 2l2.4 4.8 5.3 1-3.8 3.8.9 5.3L12 14.5l-4.8 2.4.9-5.3L4.3 7.8l5.3-1z"/></svg>',
    iconbox: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M12 7l1.5 3 3.3.6-2.4 2.4.6 3.3L12 14.5l-3 1.8.6-3.3-2.4-2.4 3.3-.6z" stroke-width="1.2"/></svg>',
    // ── Text ──
    list: '<svg width="24" height="24"'+V+S+'><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><circle cx="4" cy="6" r="1.5" fill="currentColor" stroke="none"/><circle cx="4" cy="12" r="1.5" fill="currentColor" stroke="none"/><circle cx="4" cy="18" r="1.5" fill="currentColor" stroke="none"/></svg>',
    iconlist: '<svg width="24" height="24"'+V+S+'><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><path d="M3.5 5l1 1 2-2M3.5 11l1 1 2-2M3.5 17l1 1 2-2"/></svg>',
    desclist: '<svg width="24" height="24"'+V+S+'><path d="M4 5h6M4 9h16M4 14h5M4 18h16"/></svg>',
    accordion: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="7" rx="1.5"/><rect x="3" y="13" width="18" height="7" rx="1.5"/><path d="M16 6.5l-2 1.5-2-1.5"/></svg>',
    tabs: '<svg width="24" height="24"'+V+S+'><rect x="3" y="7" width="18" height="14" rx="2"/><path d="M3 11h18M7 7V4h4v3M13 7V4h4v3"/></svg>',
    animatedheading: '<svg width="24" height="24"'+V+S+'><path d="M4 4v16M20 4v16M4 12h16" stroke-width="2"/><path d="M15 18l3-3-3-3" stroke-width="1.5"/></svg>',
    blendtext: '<svg width="24" height="24"'+V+S+' stroke-width="2"><text x="3" y="17" font-size="16" font-family="sans-serif" fill="currentColor" stroke="none" font-weight="800">Ab</text></svg>',
    marquee: '<svg width="24" height="24"'+V+S+'><path d="M3 12h18M17 8l4 4-4 4" stroke-width="1.5"/><path d="M7 8h3M12 8h3" stroke-width="1.2"/></svg>',
    textmask: '<svg width="24" height="24"'+V+S+'><rect x="3" y="5" width="18" height="14" rx="2"/><text x="5" y="17" font-size="14" font-family="sans-serif" fill="currentColor" stroke="none" font-weight="800">Aa</text></svg>',
    textpath: '<svg width="24" height="24"'+V+S+'><path d="M3 18c3-12 15-12 18 0" fill="none"/><text x="6" y="14" font-size="8" font-family="sans-serif" fill="currentColor" stroke="none">abc</text></svg>',
    quotation: '<svg width="24" height="24"'+V+S+'><path d="M6 10V6h4v4c0 3-2 5-4 6M14 10V6h4v4c0 3-2 5-4 6"/></svg>',
    toc: '<svg width="24" height="24"'+V+S+'><path d="M4 4h16M4 9h12M4 14h14M4 19h10"/><circle cx="20" cy="4" r="1" fill="currentColor" stroke="none"/><circle cx="18" cy="9" r="1" fill="currentColor" stroke="none"/><circle cx="20" cy="14" r="1" fill="currentColor" stroke="none"/></svg>',
    // ── Media ──
    gallery: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/><rect x="13" y="13" width="8" height="8" rx="1"/></svg>',
    progallery: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="8" height="11" rx="1"/><rect x="13" y="3" width="8" height="5" rx="1"/><rect x="13" y="10" width="8" height="11" rx="1"/><rect x="3" y="16" width="8" height="5" rx="1"/></svg>',
    slider: '<svg width="24" height="24"'+V+S+'><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M8 12l3 3 3-3"/></svg>',
    proslider: '<svg width="24" height="24"'+V+S+'><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 12h20" stroke-dasharray="4 2"/><circle cx="8" cy="12" r="2" fill="currentColor"/></svg>',
    carousel: '<svg width="24" height="24"'+V+S+'><rect x="1" y="6" width="6" height="12" rx="1" opacity=".4"/><rect x="9" y="4" width="6" height="16" rx="1"/><rect x="17" y="6" width="6" height="12" rx="1" opacity=".4"/></svg>',
    slideshow: '<svg width="24" height="24"'+V+S+'><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="7" cy="20" r="1" fill="currentColor" stroke="none"/><circle cx="10" cy="20" r="1" fill="currentColor" stroke="none"/><circle cx="13" cy="20" r="1" fill="currentColor" stroke="none"/></svg>',
    imgcompare: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="12" y1="3" x2="12" y2="21"/><path d="M9 12l-2-2v4zM15 12l2-2v4z" fill="currentColor" stroke="none"/></svg>',
    lightbox: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="12" cy="12" r="4"/><path d="M12 8v-2M12 18v-2M16 12h2M6 12h2" stroke-width="1.2"/></svg>',
    lottie: '<svg width="24" height="24"'+V+S+'><circle cx="12" cy="12" r="9"/><path d="M8 12c0-3 2-5 4-5s4 2 4 5-2 5-4 5-4-2-4-5z"/></svg>',
    shatteredimage: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 3l4 9-4 9M16 3l-4 9 4 9"/></svg>',
    videoplaylist: '<svg width="24" height="24"'+V+S+'><rect x="2" y="3" width="14" height="10" rx="2"/><path d="M8 6v4l3-2z" fill="currentColor" stroke="none"/><path d="M18 5h4M18 9h4M18 13h4M2 16h20M2 20h16"/></svg>',
    soundcloud: '<svg width="24" height="24"'+V+S+'><path d="M3 14v-2M6 16V8M9 15V9M12 16V7M15 15V9M18 14v-3M21 13v-1"/></svg>',
    audio: '<svg width="24" height="24"'+V+S+'><path d="M11 5L6 9H2v6h4l5 4V5z"/><path d="M19.07 4.93a10 10 0 010 14.14M15.54 8.46a5 5 0 010 7.07"/></svg>',
    // ── Layout ──
    map: '<svg width="24" height="24"'+V+S+'><path d="M12 21S5 14 5 8.5a7 7 0 0114 0C19 14 12 21 12 21z"/><circle cx="12" cy="8.5" r="2.5"/></svg>',
    osmmap: '<svg width="24" height="24"'+V+S+'><path d="M12 21s-6.5-5.8-6.5-11A6.5 6.5 0 0112 3.5 6.5 6.5 0 0118.5 10c0 5.2-6.5 11-6.5 11z"/><circle cx="12" cy="10" r="2.3"/></svg>',
    divider: '<svg width="24" height="24"'+V+S+'><line x1="3" y1="12" x2="21" y2="12"/></svg>',
    spacer: '<svg width="24" height="24"'+V+S+' stroke-dasharray="3 2"><path d="M5 4h14M5 20h14M12 7v10"/></svg>',
    grid: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="12" y1="3" x2="12" y2="21"/></svg>',
    panel: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 8h18"/></svg>',
    overlay: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="18" rx="2"/><rect x="6" y="12" width="12" height="7" rx="1" opacity=".5"/></svg>',
    overlaygrid: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/><rect x="13" y="13" width="8" height="8" rx="1"/><rect x="4" y="8" width="6" height="2" rx=".5" opacity=".5"/><rect x="14" y="8" width="6" height="2" rx=".5" opacity=".5"/></svg>',
    overlayslider: '<svg width="24" height="24"'+V+S+'><rect x="3" y="4" width="18" height="16" rx="2"/><rect x="6" y="13" width="12" height="5" rx="1" opacity=".5"/><path d="M1 12l2-2v4zM23 12l-2-2v4z" fill="currentColor" stroke="none"/></svg>',
    panelslider: '<svg width="24" height="24"'+V+S+'><rect x="1" y="4" width="8" height="16" rx="1.5"/><rect x="11" y="4" width="8" height="16" rx="1.5"/><path d="M21 8h2M21 12h2M21 16h2" stroke-width="1.2"/></svg>',
    flipcard: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M12 3v18" stroke-dasharray="3 2"/><path d="M15 10l2 2-2 2"/></svg>',
    fragment: '<svg width="24" height="24"'+V+S+'><path d="M5 3h14M5 21h14M3 7v10M21 7v10" stroke-dasharray="2 2"/></svg>',
    // ── Interactive ──
    form: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="7" y1="8" x2="17" y2="8"/><line x1="7" y1="12" x2="14" y2="12"/><rect x="7" y="15" width="5" height="3" rx="1"/></svg>',
    counter: '<svg width="24" height="24"'+V+S+'><text x="5" y="17" font-size="16" font-family="sans-serif" fill="currentColor" stroke="none" font-weight="700">42</text></svg>',
    countercircle: '<svg width="24" height="24"'+V+S+'><circle cx="12" cy="12" r="9" stroke-dasharray="40 20"/><text x="8" y="16" font-size="10" font-family="sans-serif" fill="currentColor" stroke="none" font-weight="700">75</text></svg>',
    countdown: '<svg width="24" height="24"'+V+S+'><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>',
    testimonial: '<svg width="24" height="24"'+V+S+'><path d="M3 3h18v12H9l-4 4V15H3z"/><path d="M7 8h3M7 11h6" stroke-width="1.2"/></svg>',
    pricing: '<svg width="24" height="24"'+V+S+'><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="10" x2="14" y2="10"/><line x1="8" y1="14" x2="12" y2="14"/><rect x="8" y="17" width="8" height="2.5" rx="1"/></svg>',
    pricelist: '<svg width="24" height="24"'+V+S+'><path d="M4 6h10M18 6h2M4 11h8M18 11h2M4 16h12M18 16h2" stroke-width="1.2"/><path d="M14 6h1M12 11h1M16 16h1" stroke-dasharray="1 2"/></svg>',
    social: '<svg width="24" height="24"'+V+S+'><circle cx="6" cy="12" r="3"/><circle cx="18" cy="6" r="3"/><circle cx="18" cy="18" r="3"/><line x1="8.5" y1="10.8" x2="15.5" y2="7.2"/><line x1="8.5" y1="13.2" x2="15.5" y2="16.8"/></svg>',
    progress: '<svg width="24" height="24"'+V+S+'><rect x="3" y="9" width="18" height="6" rx="3"/><rect x="3" y="9" width="12" height="6" rx="3" fill="currentColor" opacity=".3"/></svg>',
    progresstracker: '<svg width="24" height="24"'+V+S+'><circle cx="4" cy="12" r="2.5" fill="currentColor" opacity=".3"/><circle cx="12" cy="12" r="2.5" fill="currentColor" opacity=".3"/><circle cx="20" cy="12" r="2.5"/><line x1="6.5" y1="12" x2="9.5" y2="12"/><line x1="14.5" y1="12" x2="17.5" y2="12"/></svg>',
    chart: '<svg width="24" height="24"'+V+S+'><rect x="3" y="12" width="4" height="8" rx="1"/><rect x="10" y="6" width="4" height="14" rx="1"/><rect x="17" y="9" width="4" height="11" rx="1"/></svg>',
    timeline: '<svg width="24" height="24"'+V+S+'><line x1="12" y1="3" x2="12" y2="21"/><circle cx="12" cy="6" r="2" fill="currentColor"/><circle cx="12" cy="12" r="2"/><circle cx="12" cy="18" r="2"/><path d="M14 6h5M5 12h7M14 18h5" stroke-width="1.2"/></svg>',
    hotspot: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="10" cy="10" r="2.5"/><circle cx="16" cy="14" r="2.5"/><circle cx="10" cy="10" r="1" fill="currentColor" stroke="none"/><circle cx="16" cy="14" r="1" fill="currentColor" stroke="none"/></svg>',
    popover: '<svg width="24" height="24"'+V+S+'><rect x="4" y="3" width="16" height="10" rx="2"/><path d="M10 13l2 3 2-3"/></svg>',
    togglebtn: '<svg width="24" height="24"'+V+S+'><rect x="3" y="8" width="18" height="8" rx="4"/><circle cx="15" cy="12" r="3" fill="currentColor"/></svg>',
    starrating: '<svg width="24" height="24"'+V+S+'><polygon points="6.5 3.4 7.7 6 10.5 6.3 8.4 8.1 9 10.9 6.5 9.4 4 10.9 4.6 8.1 2.5 6.3 5.3 6"/><path d="M13 6.5h7M13 10h5"/></svg>',
    // ── Navigation ──
    navmenu: '<svg width="24" height="24"'+V+S+' stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>',
    megamenu: '<svg width="24" height="24"'+V+S+'><line x1="3" y1="4" x2="21" y2="4" stroke-width="2"/><rect x="2" y="8" width="20" height="12" rx="1.5"/><line x1="9" y1="8" x2="9" y2="20"/><line x1="16" y1="8" x2="16" y2="20"/></svg>',
    breadcrumbs: '<svg width="24" height="24"'+V+S+'><path d="M4 12h3M11 12h3M18 12h3"/><path d="M8 9l3 3-3 3M15 9l3 3-3 3" stroke-width="1.2"/></svg>',
    nav: '<svg width="24" height="24"'+V+S+'><path d="M4 6h16M4 12h12M4 18h14"/></svg>',
    subnav: '<svg width="24" height="24"'+V+S+'><path d="M3 12h4M10 12h4M17 12h4" stroke-width="2"/></svg>',
    pagination: '<svg width="24" height="24"'+V+S+'><path d="M3 10l3 3-3 3"/><rect x="8" y="10" width="3" height="6" rx="1"/><rect x="13" y="10" width="3" height="6" rx="1"/><rect x="18" y="10" width="3" height="6" rx="1"/></svg>',
    postnavigation: '<svg width="24" height="24"'+V+S+'><path d="M3 12h18M6 8l-4 4 4 4M18 8l4 4-4 4"/></svg>',
    menuanchor: '<svg width="24" height="24"'+V+S+'><path d="M12 3v18M8 7l4-4 4 4"/><line x1="4" y1="14" x2="20" y2="14" stroke-dasharray="3 2"/></svg>',
    totop: '<svg width="24" height="24"'+V+S+'><path d="M12 19V7M7 12l5-5 5 5"/><line x1="5" y1="4" x2="19" y2="4" stroke-width="2"/></svg>',
    scrollprogress: '<svg width="24" height="24"'+V+S+'><rect x="10" y="3" width="4" height="18" rx="2"/><rect x="10" y="3" width="4" height="10" rx="2" fill="currentColor" opacity=".3"/></svg>',
    // ── Content ──
    postgrid: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/><rect x="13" y="13" width="8" height="8" rx="1"/><line x1="5" y1="9" x2="9" y2="9" stroke-width="1"/><line x1="15" y1="9" x2="19" y2="9" stroke-width="1"/></svg>',
    queryloop: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/><path d="M17 17a3 3 0 11-3 3" stroke-width="1.5"/><path d="M16 22l-2-2 2-2" stroke-width="1.2"/></svg>',
    relatedposts: '<svg width="24" height="24"'+V+S+'><rect x="2" y="5" width="6" height="8" rx="1"/><rect x="9" y="5" width="6" height="8" rx="1"/><rect x="16" y="5" width="6" height="8" rx="1"/><path d="M2 15h6M9 15h6M16 15h6" stroke-width="1"/></svg>',
    portfolio: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="18" height="8" rx="1"/></svg>',
    team: '<svg width="24" height="24"'+V+S+'><circle cx="8" cy="8" r="3"/><circle cx="16" cy="8" r="3"/><path d="M4 19c0-3 2-4 4-4s4 1 4 4M12 19c0-3 2-4 4-4s4 1 4 4"/></svg>',
    authorbox: '<svg width="24" height="24"'+V+S+'><circle cx="8" cy="10" r="3"/><rect x="13" y="7" width="8" height="2" rx="1"/><rect x="13" y="11" width="6" height="2" rx="1"/><rect x="3" y="3" width="18" height="18" rx="2"/></svg>',
    postmeta: '<svg width="24" height="24"'+V+S+'><path d="M4 7h4M12 7h4M4 12h8M4 17h6"/><circle cx="10" cy="7" r="1.5"/><circle cx="18" cy="7" r="1.5"/></svg>',
    readingtime: '<svg width="24" height="24"'+V+S+'><circle cx="12" cy="12" r="9"/><path d="M12 7v5l2.5 2.5"/><rect x="3" y="18" width="6" height="2" rx="1"/></svg>',
    viewscounter: '<svg width="24" height="24"'+V+S+'><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>',
    sharebuttons: '<svg width="24" height="24"'+V+S+'><circle cx="6" cy="12" r="2.6"/><circle cx="17" cy="6" r="2.6"/><circle cx="17" cy="18" r="2.6"/><path d="M8.3 10.8l6.4-3.6M8.3 13.2l6.4 3.6"/><path d="M20 4l1.5-1.5M21.5 2.5h-2M21.5 2.5v2"/></svg>',
    wpcomments: '<svg width="24" height="24"'+V+S+'><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>',
    tagcloud: '<svg width="24" height="24"'+V+S+'><rect x="2" y="4" width="7" height="5" rx="2"/><rect x="11" y="4" width="5" height="5" rx="2"/><rect x="18" y="4" width="4" height="5" rx="2"/><rect x="2" y="11" width="5" height="5" rx="2"/><rect x="9" y="11" width="8" height="5" rx="2"/><rect x="2" y="18" width="9" height="4" rx="2"/></svg>',
    // ── Marketing ──
    popup: '<svg width="24" height="24"'+V+S+'><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 8h20"/><path d="M17 7V5M19 7V5"/></svg>',
    floatingpanel: '<svg width="24" height="24"'+V+S+'><rect x="5" y="5" width="14" height="14" rx="2"/><path d="M5 9h14"/><circle cx="8" cy="7" r=".8" fill="currentColor"/><circle cx="10.5" cy="7" r=".8" fill="currentColor"/></svg>',
    mobilebar: '<svg width="24" height="24"'+V+S+'><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="8" y1="18" x2="16" y2="18"/></svg>',
    newsticker: '<svg width="24" height="24"'+V+S+'><rect x="3" y="8" width="18" height="8" rx="2"/><path d="M7 12h10M19 10l2 2-2 2" stroke-width="1.2"/></svg>',
    pagetitlebar: '<svg width="24" height="24"'+V+S+'><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M6 10h12M6 14h8" stroke-width="1.2"/></svg>',
    alert: '<svg width="24" height="24"'+V+S+'><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M12 9v3M12 15h.01" stroke-width="2"/></svg>',
    // ── Code / Embed ──
    code: '<svg width="24" height="24"'+V+S+'><path d="M8 7l-5 5 5 5M16 7l5 5-5 5"/></svg>',
    html: '<svg width="24" height="24"'+V+S+'><path d="M8 7l-5 5 5 5M16 7l5 5-5 5M10 19l4-14"/></svg>',
    shortcode: '<svg width="24" height="24"'+V+S+'><path d="M7 7L3 12l4 5M17 7l4 5-4 5"/><line x1="9" y1="12" x2="15" y2="12" stroke-width="1.2"/></svg>',
    templateembed: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="18" rx="2"/><rect x="7" y="7" width="10" height="10" rx="1" stroke-dasharray="2 2"/></svg>',
    sitemap: '<svg width="24" height="24"'+V+S+'><rect x="9" y="2" width="6" height="4" rx="1"/><rect x="2" y="14" width="6" height="4" rx="1"/><rect x="9" y="14" width="6" height="4" rx="1"/><rect x="16" y="14" width="6" height="4" rx="1"/><path d="M12 6v4M5 14v-2h14v2M12 12v2"/></svg>',
    sitelogo: '<svg width="24" height="24"'+V+S+'><rect x="4" y="6" width="16" height="12" rx="2"/><circle cx="12" cy="12" r="3"/><path d="M12 9v0" stroke-width="3" stroke-linecap="round"/></svg>',
    // ── Misc ──
    shapedivider: '<svg width="24" height="24"'+V+S+'><path d="M2 16c4-4 6 2 10-2s6 2 10-2"/><line x1="2" y1="20" x2="22" y2="20"/></svg>',
    hero: '<svg width="24" height="24"'+V+S+'><rect x="2" y="3" width="20" height="18" rx="2"/><path d="M6 14h12M6 18h8" stroke-width="1.2"/><circle cx="12" cy="9" r="3"/></svg>',
    darkmode: '<svg width="24" height="24"'+V+S+'><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>',
    langswitcher: '<svg width="24" height="24"'+V+S+'><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c-3 3-3 15 0 18M12 3c3 3 3 15 0 18"/></svg>',
    loginform: '<svg width="24" height="24"'+V+S+'><rect x="5" y="3" width="14" height="18" rx="2"/><circle cx="12" cy="10" r="3"/><path d="M8 18c0-2 2-3 4-3s4 1 4 3"/></svg>',
    livesearch: '<svg width="24" height="24"'+V+S+'><circle cx="10" cy="10" r="7"/><line x1="15" y1="15" x2="21" y2="21" stroke-width="2"/><path d="M7 10h6M10 7v6" stroke-width="1.2"/></svg>',
    search: '<svg width="24" height="24"'+V+S+'><circle cx="10" cy="10" r="7"/><line x1="15" y1="15" x2="21" y2="21" stroke-width="2"/></svg>',
    instagram: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>',
    facebookpage: '<svg width="24" height="24"'+V+S+'><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M14 3v5h3l-1 3h-2v8h-3v-8H9v-3h2V6.5C11 4.5 12 3 14 3z" stroke-width="1.2"/></svg>',
    twitterfeed: '<svg width="24" height="24"'+V+S+'><path d="M23 3a10.9 10.9 0 01-3.14 1.53A4.48 4.48 0 0012 7.5v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c13 8 23 0 23-13.5" stroke-width="1.2"/></svg>',
    linkinbio: '<svg width="24" height="24"'+V+S+'><rect x="6" y="3" width="12" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h4" stroke-width="1.2"/></svg>',
    paymentbuttons: '<svg width="24" height="24"'+V+S+'><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/><path d="M6 15h4M14 15h4"/></svg>',
    pdfviewer: '<svg width="24" height="24"'+V+S+'><rect x="5" y="2" width="14" height="20" rx="2"/><text x="7" y="15" font-size="8" font-family="sans-serif" fill="currentColor" stroke="none" font-weight="700">PDF</text></svg>',
    pdfpro: '<svg width="24" height="24"'+V+S+'><rect x="5" y="2" width="14" height="20" rx="2"/><text x="7" y="15" font-size="8" font-family="sans-serif" fill="currentColor" stroke="none" font-weight="700">PDF</text><path d="M16 5l3-3" stroke-width="1.2"/></svg>',
    calendar: '<svg width="24" height="24"'+V+S+'><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
    table: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="10" y1="3" x2="10" y2="21"/></svg>',
    switcher: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="18" rx="1.5"/></svg>',
    switcherpanel: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18" stroke-width="1.2"/><path d="M3 6h5M10 6h5" stroke-width="1.5"/></svg>',
    killnextprev: '<svg width="24" height="24"'+V+S+'><path d="M18 6L6 18M6 6l12 12" stroke-width="2"/></svg>',
    // ── Booking / Service ──
    servicesearch: '<svg width="24" height="24"'+V+S+'><circle cx="10" cy="10" r="7"/><line x1="15" y1="15" x2="21" y2="21" stroke-width="2"/></svg>',
    booking: '<svg width="24" height="24"'+V+S+'><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M8 14l2 2 4-4" stroke-width="1.5"/></svg>',
    bookingpicker: '<svg width="24" height="24"'+V+S+'><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><circle cx="12" cy="16" r="2" fill="currentColor"/></svg>',
    hostcard: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="10" r="3"/><path d="M14 8h5M14 12h5"/><path d="M6 18c0-2 1.5-3 3-3s3 1 3 3"/></svg>',
    serviceresults: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/><rect x="13" y="13" width="8" height="8" rx="1"/></svg>',
    servicelist: '<svg width="24" height="24"'+V+S+'><rect x="3" y="4" width="18" height="5" rx="1"/><rect x="3" y="11" width="18" height="5" rx="1"/><rect x="3" y="18" width="18" height="5" rx="1" stroke-dasharray="3 2"/></svg>',
    servicestats: '<svg width="24" height="24"'+V+S+'><rect x="3" y="14" width="4" height="7" rx="1"/><rect x="10" y="8" width="4" height="13" rx="1"/><rect x="17" y="3" width="4" height="18" rx="1"/></svg>',
    servicehero: '<svg width="24" height="24"'+V+S+'><rect x="2" y="3" width="20" height="18" rx="2"/><path d="M6 14h12M6 18h8"/></svg>',
    servicegallery: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="18" height="8" rx="1"/></svg>',
    serviceinfo: '<svg width="24" height="24"'+V+S+'><circle cx="12" cy="12" r="9"/><path d="M12 8h.01" stroke-width="2.5"/><path d="M12 12v4"/></svg>',
    serviceprices: '<svg width="24" height="24"'+V+S+'><path d="M12 2v20M9 5.5h4.5a2.5 2.5 0 010 5H9h5.5a2.5 2.5 0 010 5H9"/></svg>',
    serviceaddress: '<svg width="24" height="24"'+V+S+'><path d="M12 21S5 14 5 8.5a7 7 0 0114 0C19 14 12 21 12 21z"/><circle cx="12" cy="8.5" r="2.5"/></svg>',
    serviceamenities: '<svg width="24" height="24"'+V+S+'><path d="M3.5 5l1.5 1.5 3-3M3.5 11l1.5 1.5 3-3M3.5 17l1.5 1.5 3-3"/><path d="M11 6h10M11 12h8M11 18h10"/></svg>',
    servicedescription: '<svg width="24" height="24"'+V+S+'><path d="M4 5h16M4 9h12M4 13h16M4 17h8"/></svg>',
    serviceexcerpt: '<svg width="24" height="24"'+V+S+'><path d="M4 5h16M4 9h12M4 13h8"/></svg>',
    servicedirections: '<svg width="24" height="24"'+V+S+'><path d="M3 12h18M18 8l4 4-4 4"/><circle cx="5" cy="12" r="2"/></svg>',
    servicerules: '<svg width="24" height="24"'+V+S+'><rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h4"/></svg>',
    servicerelated: '<svg width="24" height="24"'+V+S+'><rect x="2" y="5" width="6" height="8" rx="1"/><rect x="9" y="5" width="6" height="8" rx="1"/><rect x="16" y="5" width="6" height="8" rx="1"/><path d="M2 15h6M9 15h6M16 15h6" stroke-width="1"/></svg>',
    servicevideo: '<svg width="24" height="24"'+V+S+'><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M10 8v8l6-4z" fill="currentColor" stroke="none"/></svg>',
    servicecheckin: '<svg width="24" height="24"'+V+S+'><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><path d="M8 14l3 3 5-5"/></svg>',
    servicecipat: '<svg width="24" height="24"'+V+S+'><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h6M8 16h4"/></svg>',
    serviceclub: '<svg width="24" height="24"'+V+S+'><path d="M12 2l2.4 4.8 5.3 1-3.8 3.8.9 5.3L12 14.5l-4.8 2.4.9-5.3L4.3 7.8l5.3-1z"/></svg>',
    servicemushrooms: '<svg width="24" height="24"'+V+S+'><path d="M8 16c0-5 2-9 4-9s4 4 4 9"/><path d="M5 16h14" stroke-width="2"/><path d="M10 16v4M14 16v4"/></svg>',
    // ── OLO Room ──
    olo_room_availability: '<svg width="24" height="24"'+V+S+'><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M8 2v4M16 2v4M3 10h18"/><path d="M8 14l3 3 5-5"/></svg>',
    olo_room_calendar: '<svg width="24" height="24"'+V+S+'><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M8 2v4M16 2v4M3 10h18"/></svg>',
    olo_room_contacts: '<svg width="24" height="24"'+V+S+'><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 5 9-5"/></svg>',
    olo_room_description: '<svg width="24" height="24"'+V+S+'><path d="M4 5h16M4 9h12M4 13h16M4 17h8"/></svg>',
    olo_room_gallery: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="18" height="8" rx="1"/></svg>',
    olo_room_grid: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/><rect x="13" y="13" width="8" height="8" rx="1"/></svg>',
    olo_room_hero: '<svg width="24" height="24"'+V+S+'><rect x="2" y="3" width="20" height="18" rx="2"/><path d="M6 14h12M6 18h8"/></svg>',
    olo_room_info: '<svg width="24" height="24"'+V+S+'><circle cx="12" cy="12" r="9"/><path d="M12 8h.01" stroke-width="2.5"/><path d="M12 12v4"/></svg>',
    olo_room_pricing: '<svg width="24" height="24"'+V+S+'><path d="M12 2v20M9 5.5h4.5a2.5 2.5 0 010 5H9h5.5a2.5 2.5 0 010 5H9"/></svg>',
    olo_room_related: '<svg width="24" height="24"'+V+S+'><rect x="2" y="5" width="6" height="8" rx="1"/><rect x="9" y="5" width="6" height="8" rx="1"/><rect x="16" y="5" width="6" height="8" rx="1"/></svg>',
    // ── WooCommerce ──
    woo_products: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/><rect x="13" y="13" width="8" height="8" rx="1"/></svg>',
    woo_cart: '<svg width="24" height="24"'+V+S+'><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>',
    woo_minicart: '<svg width="24" height="24"'+V+S+'><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4zM3 6h18"/><path d="M16 10a4 4 0 01-8 0"/></svg>',
    woo_checkout: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 10l3 3 5-5"/></svg>',
    woo_checkout_multistep: '<svg width="24" height="24"'+V+S+'><circle cx="5" cy="6" r="2.5" fill="currentColor" opacity=".3"/><circle cx="12" cy="6" r="2.5"/><circle cx="19" cy="6" r="2.5"/><line x1="7.5" y1="6" x2="9.5" y2="6"/><line x1="14.5" y1="6" x2="16.5" y2="6"/><rect x="3" y="11" width="18" height="10" rx="2"/></svg>',
    woo_myaccount: '<svg width="24" height="24"'+V+S+'><circle cx="12" cy="8" r="4"/><path d="M5 20c0-4 3-6 7-6s7 2 7 6"/></svg>',
    woo_price: '<svg width="24" height="24"'+V+S+'><path d="M12 2v20M9 5.5h4.5a2.5 2.5 0 010 5H9h5.5a2.5 2.5 0 010 5H9"/></svg>',
    woo_product_image: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="2"/><path d="M3 16l5-5 4 4 4-6 5 7"/></svg>',
    woo_product_title: '<svg width="24" height="24"'+V+S+' stroke-width="2"><path d="M4 4v16M20 4v16M4 12h16"/></svg>',
    woo_product_description: '<svg width="24" height="24"'+V+S+'><path d="M4 5h16M4 9h12M4 13h16M4 17h8"/></svg>',
    woo_product_tabs: '<svg width="24" height="24"'+V+S+'><rect x="3" y="7" width="18" height="14" rx="2"/><path d="M3 11h18M7 7V4h4v3M13 7V4h4v3"/></svg>',
    woo_product_meta: '<svg width="24" height="24"'+V+S+'><path d="M4 7h4M12 7h4M4 12h8M4 17h6"/><circle cx="10" cy="7" r="1.5"/></svg>',
    woo_product_gallery_slider: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="12" rx="2"/><rect x="3" y="17" width="5" height="4" rx="1"/><rect x="10" y="17" width="5" height="4" rx="1"/><rect x="17" y="17" width="4" height="4" rx="1"/></svg>',
    woo_rating: '<svg width="24" height="24"'+V+S+'><path d="M12 2l2.4 4.8 5.3 1-3.8 3.8.9 5.3L12 14.5l-4.8 2.4.9-5.3L4.3 7.8l5.3-1z"/></svg>',
    woo_addtocart: '<svg width="24" height="24"'+V+S+'><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/><path d="M12 9v4M10 11h4" stroke-width="1.5"/></svg>',
    woo_breadcrumbs: '<svg width="24" height="24"'+V+S+'><path d="M4 12h3M11 12h3M18 12h3"/><path d="M8 9l3 3-3 3M15 9l3 3-3 3" stroke-width="1.2"/></svg>',
    woo_categories: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="3" width="8" height="8" rx="2"/><rect x="3" y="13" width="8" height="8" rx="2"/><rect x="13" y="13" width="8" height="8" rx="2"/></svg>',
    woo_sale_badge: '<svg width="24" height="24"'+V+S+'><circle cx="12" cy="12" r="9"/><text x="5" y="16" font-size="9" font-family="sans-serif" fill="currentColor" stroke="none" font-weight="700">%</text></svg>',
    woo_notices: '<svg width="24" height="24"'+V+S+'><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M12 9v3M12 15h.01" stroke-width="2"/></svg>',
    woo_wishlist: '<svg width="24" height="24"'+V+S+'><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 000-7.78z"/></svg>',
    woo_comparison: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="8" height="18" rx="2"/><rect x="13" y="3" width="8" height="18" rx="2"/><path d="M5 9h4M15 9h4M5 13h4M15 13h4" stroke-width="1"/></svg>',
    woo_quickview: '<svg width="24" height="24"'+V+S+'><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>',
    woo_recently_viewed: '<svg width="24" height="24"'+V+S+'><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/><path d="M3 12h2" stroke-width="1.2"/></svg>',
    woo_related: '<svg width="24" height="24"'+V+S+'><rect x="2" y="5" width="6" height="8" rx="1"/><rect x="9" y="5" width="6" height="8" rx="1"/><rect x="16" y="5" width="6" height="8" rx="1"/></svg>',
    woo_cross_sells: '<svg width="24" height="24"'+V+S+'><rect x="2" y="5" width="6" height="8" rx="1"/><rect x="9" y="5" width="6" height="8" rx="1"/><rect x="16" y="5" width="6" height="8" rx="1"/><path d="M5 16h14" stroke-width="1.2"/></svg>',
    woo_upsells: '<svg width="24" height="24"'+V+S+'><rect x="2" y="5" width="6" height="8" rx="1"/><rect x="9" y="5" width="6" height="8" rx="1"/><rect x="16" y="5" width="6" height="8" rx="1"/><path d="M12 16v4M10 18l2-2 2 2"/></svg>',
    woo_product_stock: '<svg width="24" height="24"'+V+S+'><rect x="3" y="6" width="18" height="15" rx="2"/><path d="M8 6V4M16 6V4"/><path d="M8 13l3 3 5-5"/></svg>',
    woo_product_bundle: '<svg width="24" height="24"'+V+S+'><rect x="3" y="8" width="7" height="7" rx="1"/><rect x="14" y="8" width="7" height="7" rx="1"/><rect x="8" y="3" width="8" height="5" rx="1"/><path d="M12 15v6"/></svg>',
    woo_product_filter: '<svg width="24" height="24"'+V+S+'><path d="M4 6h16M8 12h8M10 18h4"/></svg>',
    woo_product_navigation: '<svg width="24" height="24"'+V+S+'><path d="M3 12h18M6 8l-4 4 4 4M18 8l4 4-4 4"/></svg>',
    woo_order_tracking: '<svg width="24" height="24"'+V+S+'><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18"/><path d="M7 15h3M14 15h3"/></svg>',
    // ── Accommodation (olo-booking) ──
    'ac-hero':                  '<svg width="24" height="24"'+V+S+'><rect x="2" y="3" width="20" height="18" rx="2"/><path d="M2 16l5-6 4 4 4-5 7 8"/><circle cx="8" cy="8" r="1.8"/></svg>',
    'ac-card':                  '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 12h18"/><path d="M7 16h10M7 19h6"/></svg>',
    'ac-grid':                  '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/><rect x="13" y="13" width="8" height="8" rx="1"/></svg>',
    'ac-related':               '<svg width="24" height="24"'+V+S+'><rect x="2" y="5" width="6" height="8" rx="1"/><rect x="9" y="5" width="6" height="8" rx="1"/><rect x="16" y="5" width="6" height="8" rx="1"/><path d="M2 17h20"/></svg>',
    'ac-booking-form':          '<svg width="24" height="24"'+V+S+'><rect x="3" y="4" width="18" height="14" rx="2"/><path d="M3 9h18M8 4v3M16 4v3M7 13h10M7 16h6"/></svg>',
    'ac-availability-calendar': '<svg width="24" height="24"'+V+S+'><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M8 2v4M16 2v4M3 10h18"/><path d="M8 14l3 3 5-5"/></svg>',
    'ac-gallery':               '<svg width="24" height="24"'+V+S+'><rect x="2" y="2" width="9" height="9" rx="1"/><rect x="13" y="2" width="9" height="5" rx="1"/><rect x="13" y="9" width="9" height="5" rx="1"/><rect x="2" y="13" width="9" height="9" rx="1"/><rect x="13" y="16" width="9" height="6" rx="1"/></svg>',
    'ac-hero-video':            '<svg width="24" height="24"'+V+S+'><rect x="2" y="3" width="20" height="18" rx="2"/><circle cx="12" cy="12" r="4"/><polygon points="11 10 15 12 11 14" fill="currentColor"/></svg>',
    'ac-video':                 '<svg width="24" height="24"'+V+S+'><rect x="2" y="4" width="20" height="16" rx="2"/><polygon points="10 8 16 12 10 16" fill="currentColor"/></svg>',
    'ac-pricing-seasons':       '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 9v12M15 9v12"/><path d="M5 6h4M11 6h2M17 6h2"/></svg>',
    'ac-reviews':               '<svg width="24" height="24"'+V+S+'><polygon points="12 2 15 9 22 10 17 14 18 21 12 17 6 21 7 14 2 10 9 9"/></svg>',
    'ac-host-info':             '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="10" r="3"/><path d="M5 18c0-2 1.5-3 4-3s4 1 4 3"/><path d="M14 8h5M14 12h5"/></svg>',
    'ac-stats':                 '<svg width="24" height="24"'+V+S+'><rect x="3" y="14" width="4" height="7" rx="1"/><rect x="10" y="8" width="4" height="13" rx="1"/><rect x="17" y="3" width="4" height="18" rx="1"/></svg>',
    'ac-description':           '<svg width="24" height="24"'+V+S+'><path d="M4 5h16M4 9h12M4 13h16M4 17h8"/></svg>',
    'ac-amenities':             '<svg width="24" height="24"'+V+S+'><path d="M3.5 5l1.5 1.5 3-3M3.5 11l1.5 1.5 3-3M3.5 17l1.5 1.5 3-3"/><path d="M11 6h10M11 12h8M11 18h10"/></svg>',
    'ac-map':                   '<svg width="24" height="24"'+V+S+'><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><circle cx="12" cy="11" r="2"/></svg>',
    'ac-features':              '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><circle cx="6.5" cy="6.5" r="1"/><circle cx="17.5" cy="6.5" r="1"/><circle cx="6.5" cy="17.5" r="1"/><circle cx="17.5" cy="17.5" r="1"/></svg>',
    'ac-faq':                   '<svg width="24" height="24"'+V+S+'><rect x="3" y="4" width="18" height="4" rx="1"/><rect x="3" y="10" width="18" height="4" rx="1"/><rect x="3" y="16" width="18" height="4" rx="1"/><path d="M17 6l2-2M17 12l2-2M17 18l2-2" stroke-width="1.2"/></svg>',
    'ac-address':               '<svg width="24" height="24"'+V+S+'><path d="M12 22s7-7 7-13a7 7 0 1 0-14 0c0 6 7 13 7 13z"/><circle cx="12" cy="9" r="2.5"/></svg>',
    'ac-rules':                 '<svg width="24" height="24"'+V+S+'><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>',
    'ac-search':                '<svg width="24" height="24"'+V+S+'><circle cx="10" cy="10" r="7"/><line x1="15" y1="15" x2="21" y2="21" stroke-width="2"/></svg>',
    'ac-breadcrumb-hero':       '<svg width="24" height="24"'+V+S+'><rect x="2" y="3" width="20" height="18" rx="2"/><path d="M5 7h2M9 7h3M14 7h2"/><path d="M6 16h12"/></svg>',
    'ac-cta':                   '<svg width="24" height="24"'+V+S+'><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M5 10h6M5 13h4"/><rect x="14" y="10" width="6" height="4" rx="1.2"/></svg>',
    'ac-certifications':        '<svg width="24" height="24"'+V+S+'><circle cx="12" cy="9" r="6"/><path d="M9 14l-3 7 6-3 6 3-3-7"/><circle cx="12" cy="9" r="3" stroke-opacity=".4"/></svg>',
    'ac-contact-form':          '<svg width="24" height="24"'+V+S+'><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/><path d="M8 14h3M8 17h5"/></svg>',
    // ── Icone tile (design handoff): 62 tile che ricadevano sul placeholder ──
    announcementbar: '<svg width="24" height="24"'+V+S+'><rect x="2" y="4" width="20" height="6" rx="1.5"/><path d="M6 7h9"/><path d="M3 14h18M3 18h12"/></svg>',
    audiohero: '<svg width="24" height="24"'+V+S+'><rect x="2" y="3" width="20" height="18" rx="2"/><path d="M6 14v-3M9 15v-5M12 16.5v-9M15 15v-5M18 14v-3"/></svg>',
    buildermock: '<svg width="24" height="24"'+V+S+'><rect x="2" y="3" width="20" height="18" rx="2"/><path d="M2 7h20M8 7v14"/><rect x="3.8" y="9.4" width="2.6" height="2.2" rx=".4"/><path d="M10 11h8M10 14h6M10 17h7"/></svg>',
    chathero: '<svg width="24" height="24"'+V+S+'><rect x="2" y="3" width="20" height="18" rx="2"/><path d="M6 8h12M6 10.5h7"/><path d="M6 14h10v4l-3-2H6z"/></svg>',
    featuredstory: '<svg width="24" height="24"'+V+S+'><rect x="2" y="4" width="20" height="16" rx="2"/><rect x="4" y="7" width="7" height="10" rx="1"/><path d="M14 8h5M14 11h5M14 14h3"/></svg>',
    glowgallery: '<svg width="24" height="24"'+V+S+'><circle cx="12" cy="7" r="3.5" opacity=".4"/><path d="M7 5h10"/><rect x="3" y="13" width="5" height="8" rx="1"/><rect x="9.5" y="13" width="5" height="8" rx="1"/><rect x="16" y="13" width="5" height="8" rx="1"/></svg>',
    glowhero: '<svg width="24" height="24"'+V+S+'><circle cx="12" cy="9" r="6" opacity=".35"/><path d="M5 15h14M8 19h8"/></svg>',
    imagehero: '<svg width="24" height="24"'+V+S+'><rect x="2" y="3" width="20" height="18" rx="2"/><circle cx="7" cy="8" r="1.6"/><path d="M3 16l5-4 4 3 4-4 5 5"/><path d="M6 19h9"/></svg>',
    introsplit: '<svg width="24" height="24"'+V+S+'><path d="M3 5h8M3 9h8M3 13h5"/><path d="M3 18h3M9 18h2"/><rect x="14" y="5" width="7" height="9" rx="1"/><circle cx="18" cy="17.5" r="3"/></svg>',
    maskedvideohero: '<svg width="24" height="24"'+V+S+'><path d="M2 3h20v11c0 5-5 5-10 5S2 19 2 14z"/><polygon points="10 8 15 11 10 14" fill="currentColor" stroke-width="0"/></svg>',
    masthead: '<svg width="24" height="24"'+V+S+'><rect x="2" y="4" width="20" height="4" rx="1"/><path d="M2 11h20M2 14h13M2 17h20M2 20h9"/></svg>',
    mediacta: '<svg width="24" height="24"'+V+S+'><rect x="2" y="3" width="20" height="13" rx="2"/><polygon points="10 7 15 9.5 10 12" fill="currentColor" stroke-width="0"/><rect x="6" y="19" width="12" height="3" rx="1.5"/></svg>',
    newsletter: '<svg width="24" height="24"'+V+S+'><rect x="2" y="4" width="20" height="10" rx="2"/><path d="M2 6l10 5 10-5"/><rect x="4" y="17" width="10" height="3.5" rx="1.5"/><rect x="16" y="17" width="4" height="3.5" rx="1"/></svg>',
    photocover: '<svg width="24" height="24"'+V+S+'><rect x="2" y="2" width="20" height="20" rx="1.5"/><rect x="5" y="5" width="14" height="14"/><path d="M7 16h7"/></svg>',
    producthero: '<svg width="24" height="24"'+V+S+'><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 9h20"/><circle cx="5" cy="6.5" r=".7" fill="currentColor" stroke-width="0"/><circle cx="7.5" cy="6.5" r=".7" fill="currentColor" stroke-width="0"/><path d="M8 14h8M10 17h4"/></svg>',
    searchhero: '<svg width="24" height="24"'+V+S+'><path d="M4 5h16"/><rect x="3" y="9" width="13" height="4.5" rx="2.25"/><circle cx="19" cy="11.25" r="2.6"/><path d="M5 18h4M11 18h5M18 18h2"/></svg>',
    smearhero: '<svg width="24" height="24"'+V+S+'><rect x="2" y="3" width="20" height="18" rx="2"/><path d="M5 12c3-5 6 3 9-1s4-2 5-2"/><path d="M7 17h9"/></svg>',
    availability: '<svg width="24" height="24"'+V+S+'><rect x="4" y="4" width="4" height="4" rx="1"/><rect x="10" y="4" width="4" height="4" rx="1" fill="currentColor" opacity=".22" stroke-width="0"/><rect x="16" y="4" width="4" height="4" rx="1"/><rect x="4" y="10" width="4" height="4" rx="1" fill="currentColor" opacity=".22" stroke-width="0"/><rect x="10" y="10" width="4" height="4" rx="1"/><rect x="16" y="10" width="4" height="4" rx="1"/><path d="M5 19h14"/></svg>',
    builder: '<svg width="24" height="24"'+V+S+'><path d="M4 7h7M15 7h5M17.5 4.5v5"/><path d="M4 12h7M15 12h5"/><path d="M4 17h16"/></svg>',
    finder: '<svg width="24" height="24"'+V+S+'><rect x="3" y="4" width="5" height="3" rx="1.5"/><rect x="10" y="4" width="5" height="3" rx="1.5"/><rect x="17" y="4" width="4" height="3" rx="1.5"/><rect x="5" y="11" width="14" height="9" rx="2"/></svg>',
    hiddenpop: '<svg width="24" height="24"'+V+S+'><rect x="4" y="6" width="16" height="12" rx="2" stroke-dasharray="3 2.5"/><circle cx="12" cy="12" r="2.2"/></svg>',
    hotspots: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="9" r="1.4" fill="currentColor" stroke-width="0"/><circle cx="15" cy="14" r="1.4" fill="currentColor" stroke-width="0"/><circle cx="15" cy="14" r="4"/></svg>',
    icontabs: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><circle cx="7" cy="6" r="1.2"/><circle cx="12" cy="6" r="1.2"/><circle cx="17" cy="6" r="1.2"/></svg>',
    mixer: '<svg width="24" height="24"'+V+S+'><circle cx="9" cy="10" r="5"/><circle cx="15" cy="10" r="5"/><path d="M6 19h12"/></svg>',
    physicsbin: '<svg width="24" height="24"'+V+S+'><path d="M4 5v13a2 2 0 002 2h12a2 2 0 002-2V5"/><circle cx="9" cy="15" r="2.6"/><rect x="13" y="12.5" width="5" height="5" rx="1" transform="rotate(18 15.5 15)"/></svg>',
    projector: '<svg width="24" height="24"'+V+S+'><path d="M3 8h18"/><circle cx="9" cy="8" r="2.4"/><path d="M5 14h14M5 18h8"/></svg>',
    revealbox: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 13h18" stroke-dasharray="3 2"/><path d="M9 9l3-3 3 3"/></svg>',
    scaler: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="7" height="5" rx="1"/><path d="M14 5.5h7M5 13h16M5 17h12M5 21h8"/></svg>',
    scratchfx: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M6 13c2-3 4 2 6-1s3-2 4-3"/><path d="M14 6l4 4"/></svg>',
    timezone: '<svg width="24" height="24"'+V+S+'><circle cx="6" cy="7" r="3"/><path d="M6 5.5V7l1.2 .8"/><path d="M12 6h9M12 11h9M12 16h9"/><circle cx="9" cy="11" r="1.3" fill="currentColor" stroke-width="0"/><circle cx="16" cy="16" r="1.3" fill="currentColor" stroke-width="0"/></svg>',
    tripfinder: '<svg width="24" height="24"'+V+S+'><rect x="2" y="8" width="20" height="8" rx="2"/><path d="M8 8v8M14 8v8"/><path d="M16 12h4"/></svg>',
    presencegrid: '<svg width="24" height="24"'+V+S+'><rect x="3" y="4" width="7" height="7" rx="1.5"/><rect x="14" y="4" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="6" rx="1.5"/><rect x="14" y="14" width="7" height="6" rx="1.5"/><circle cx="9.5" cy="5.5" r="1.3" fill="currentColor" stroke-width="0"/></svg>',
    matchfixtures: '<svg width="24" height="24"'+V+S+'><circle cx="7" cy="12" r="3"/><circle cx="17" cy="12" r="3"/><path d="M11.5 12h1M12 10.5v3"/></svg>',
    asciiviz: '<svg width="24" height="24"'+V+S+'><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M6 9h2.5M11 9h3.5M17 9h1.5M6 13h4M12.5 13h2M6 17h6.5"/></svg>',
    beforeafter: '<svg width="24" height="24"'+V+S+'><rect x="3" y="5" width="8" height="11" rx="1"/><rect x="13" y="5" width="8" height="11" rx="1"/><path d="M4 19h6M14 19h6"/></svg>',
    categoryrail: '<svg width="24" height="24"'+V+S+'><rect x="2" y="5" width="7" height="13" rx="1.5"/><rect x="11" y="5" width="7" height="13" rx="1.5"/><rect x="20" y="5" width="3" height="13" rx="1.5"/><path d="M8 21h8"/></svg>',
    productgrid: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="8" height="11" rx="1"/><rect x="13" y="3" width="8" height="11" rx="1"/><path d="M3 16.5h6M13 16.5h6M3 19.5h4M13 19.5h4"/></svg>',
    showcasegrid: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="17" cy="7" r="2.6"/><path d="M16 8l2-2M16.4 6h1.6v1.6"/><path d="M6 17h8"/></svg>',
    svganimator: '<svg width="24" height="24"'+V+S+'><path d="M3 12h4l2.5-6 3 12 2.5-6H21"/><circle cx="3" cy="12" r="1.4" fill="currentColor" stroke-width="0"/><circle cx="21" cy="12" r="1.4" fill="currentColor" stroke-width="0"/></svg>',
    viewer360: '<svg width="24" height="24"'+V+S+'><circle cx="12" cy="12" r="9"/><path d="M3 12c0-2.2 4-4 9-4s9 1.8 9 4-4 4-9 4-9-1.8-9-4z"/><path d="M13 3.6l2.4 1.5-2.4 1.6"/></svg>',
    'cta-banner': '<svg width="24" height="24"'+V+S+'><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M5 10h6M5 13h4"/><rect x="14" y="10" width="5.5" height="4" rx="1"/></svg>',
    'hero-split': '<svg width="24" height="24"'+V+S+'><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M12 4v16"/><path d="M4 9h5M4 12h5"/></svg>',
    hoursstrip: '<svg width="24" height="24"'+V+S+'><circle cx="5" cy="12" r="2.6"/><path d="M5 10.5V12l1.1 .7"/><path d="M10 9h11M10 13h8M10 17h10"/></svg>',
    hoverlist: '<svg width="24" height="24"'+V+S+'><circle cx="5" cy="7" r="2" fill="currentColor" stroke-width="0"/><path d="M9 7h12"/><circle cx="5" cy="12.5" r="2"/><path d="M9 12.5h9"/><circle cx="5" cy="18" r="2"/><path d="M9 18h11"/></svg>',
    'info-cards': '<svg width="24" height="24"'+V+S+'><rect x="3" y="4" width="8" height="8" rx="1.5"/><rect x="13" y="4" width="8" height="8" rx="1.5"/><path d="M5 15h6M15 15h4M5 18h4M15 18h5"/></svg>',
    lookbookmixer: '<svg width="24" height="24"'+V+S+'><rect x="3" y="5" width="5" height="12" rx="1"/><rect x="10" y="5" width="5" height="12" rx="1"/><rect x="17" y="5" width="4" height="12" rx="1"/><path d="M5.5 3.6l-1 1.4h2zM6 20h12"/></svg>',
    'process-steps': '<svg width="24" height="24"'+V+S+'><circle cx="6" cy="9" r="3"/><circle cx="18" cy="9" r="3"/><path d="M9 9h6"/><path d="M3 16h6M15 16h6"/></svg>',
    'product-cards': '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="8" height="18" rx="1.5"/><rect x="13" y="3" width="8" height="18" rx="1.5"/><path d="M3 13h8M13 13h8"/><path d="M5 16h3M15 16h3"/></svg>',
    schedule: '<svg width="24" height="24"'+V+S+'><rect x="3" y="4" width="18" height="16" rx="1.5"/><path d="M3 8h18M9 4v16M15 4v16"/><rect x="9.5" y="8.6" width="5" height="3" rx=".5" fill="currentColor" opacity=".22" stroke-width="0"/></svg>',
    scrollscrub: '<svg width="24" height="24"'+V+S+'><rect x="2" y="6" width="5.5" height="9" rx="1"/><rect x="9.25" y="6" width="5.5" height="9" rx="1"/><rect x="16.5" y="6" width="5.5" height="9" rx="1"/><path d="M4 19h13M15 17l2 2-2 2"/></svg>',
    'section-header': '<svg width="24" height="24"'+V+S+'><path d="M3 5h6"/><path d="M3 9.5h15M3 13h10"/><path d="M16 19h5"/></svg>',
    stackscroll: '<svg width="24" height="24"'+V+S+'><rect x="8" y="3" width="13" height="6" rx="1.5"/><rect x="6" y="8.5" width="13" height="6" rx="1.5"/><rect x="4" y="14" width="13" height="6" rx="1.5"/></svg>',
    statstrip: '<svg width="24" height="24"'+V+S+'><path d="M3 8h6" stroke-width="2.2"/><path d="M3 12h4"/><path d="M11 4v16"/><path d="M15 8h6" stroke-width="2.2"/><path d="M15 12h4"/></svg>',
    'step-timeline': '<svg width="24" height="24"'+V+S+'><path d="M3 10h18" stroke-dasharray="3 2"/><circle cx="6" cy="10" r="2.2"/><circle cx="12" cy="10" r="2.2"/><circle cx="18" cy="10" r="2.2"/><path d="M4 16h4M10 16h4M16 16h4"/></svg>',
    'trust-strip': '<svg width="24" height="24"'+V+S+'><circle cx="6" cy="12" r="3"/><path d="M4.8 12l1 1 1.4-1.7"/><path d="M11 12h2"/><circle cx="18" cy="12" r="3"/><path d="M16.8 12l1 1 1.4-1.7"/></svg>',
    workgrid: '<svg width="24" height="24"'+V+S+'><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/><circle cx="16.5" cy="16.5" r="2.5"/><path d="M18.3 18.3l1.7 1.7"/></svg>',
    worklist: '<svg width="24" height="24"'+V+S+'><path d="M3 6h2M8 6h13M3 12h2M8 12h11M3 18h2M8 18h12"/></svg>',
    goo: '<svg width="24" height="24"'+V+S+'><circle cx="9" cy="10" r="4.5"/><circle cx="15" cy="13.5" r="3.5"/><circle cx="14" cy="8" r="2"/></svg>',
    particlefx: '<svg width="24" height="24"'+V+S+'><circle cx="6" cy="7" r="1.1" fill="currentColor" stroke-width="0"/><circle cx="12" cy="5" r="1.1" fill="currentColor" stroke-width="0"/><circle cx="18" cy="8" r="1.1" fill="currentColor" stroke-width="0"/><circle cx="8" cy="13" r="1.1" fill="currentColor" stroke-width="0"/><circle cx="15" cy="12" r="1.1" fill="currentColor" stroke-width="0"/><circle cx="11" cy="18" r="1.1" fill="currentColor" stroke-width="0"/><circle cx="19" cy="17" r="1.1" fill="currentColor" stroke-width="0"/></svg>',
    badge: '<svg width="24" height="24"'+V+S+'><rect x="3" y="8" width="13" height="8" rx="4"/><circle cx="19.5" cy="12" r="2.2"/></svg>',
    variablespecimen: '<svg width="24" height="24"'+V+S+'><path d="M4 19l5-13 5 13M6 14h6"/><path d="M16 11h5M18.5 9.5v3"/></svg>',
    leaderboard: '<svg width="24" height="24"'+V+S+'><rect x="3" y="6" width="13" height="3" rx="1.5"/><rect x="3" y="11" width="10" height="3" rx="1.5"/><rect x="3" y="16" width="7" height="3" rx="1.5"/><polygon points="20 5 20.7 6.5 22.3 6.7 21.1 7.8 21.4 9.4 20 8.6 18.6 9.4 18.9 7.8 17.7 6.7 19.3 6.5" fill="currentColor" stroke-width="0"/></svg>',
    // ── Tile fuori dal pacchetto handoff (North dormienti + clod-evoluzione) ──
    northvideohero: '<svg width="24" height="24"'+V+S+'><rect x="2" y="3" width="20" height="18" rx="2"/><polygon points="10 8 16 12 10 16" fill="currentColor" stroke-width="0"/><path d="M5 6l3 0-1 2H4z"/></svg>',
    northquoteslider: '<svg width="24" height="24"'+V+S+'><path d="M5 6h4v4c0 2-1.5 3-3.5 3.5"/><path d="M13 6h4v4c0 2-1.5 3-3.5 3.5"/><path d="M4 19h13M15 17l2 2-2 2"/></svg>',
    studiohero: '<svg width="24" height="24"'+V+S+'><rect x="2" y="3" width="20" height="18" rx="2"/><circle cx="12" cy="10" r="3.5"/><path d="M12 3v3M6 15h12"/></svg>',
    filmreel: '<svg width="24" height="24"'+V+S+'><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 9h18M3 15h18"/><path d="M7 5v14M17 5v14"/></svg>',
    scrubtext: '<svg width="24" height="24"'+V+S+'><path d="M5 6h14M12 6v9"/><path d="M4 20h16M15 18l2 2-2 2"/></svg>',
    themedemos: '<svg width="24" height="24"'+V+S+'><rect x="3" y="4" width="8" height="7" rx="1"/><rect x="13" y="4" width="8" height="7" rx="1"/><path d="M3 6.5h8M13 6.5h8"/><rect x="3" y="14" width="18" height="6" rx="1"/><path d="M3 16.5h18"/></svg>',
    evonotes: '<svg width="24" height="24"'+V+S+'><path d="M14 3H6a2 2 0 00-2 2v14a2 2 0 002 2h12a2 2 0 002-2V9z"/><path d="M14 3v6h6"/><path d="M8 13h7M8 17h5"/></svg>',
  };
  var svg = icons[type] || '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="4" y="4" width="16" height="16" rx="3"/><circle cx="12" cy="12" r="3"/></svg>';
  _iconCache[type] = svg;
  return svg;
}

function open(sectionIndex, initialTab) {
  insertAtIndex.value = sectionIndex != null ? sectionIndex : tilesStore.canvasTiles.length;
  activeTab.value = (initialTab === 'row' || initialTab === 'library') ? initialTab : 'module';
  searchQuery.value = '';
  visible.value = true;
  nextTick(() => {
    searchRef.value?.focus();
    panelRef.value?.focus();
  });
}

function close() {
  visible.value = false;
}

function insertModule(tileType) {
  const newTile = createTileFromType(tileType);
  if (!newTile) return;
  const col = createColumn('1-1', [newTile]);
  const row = createRow('100', [col]);
  const section = createSection([row]);
  const idx = insertAtIndex.value != null ? insertAtIndex.value : tilesStore.canvasTiles.length;
  tilesStore.canvasTiles.splice(idx, 0, section);
  builderStore.isDirty = true;
  builderStore.selectTile(newTile.id);
  close();
}

const layoutColWidths = {
  '100': ['1-1'],
  '50-50': ['1-2', '1-2'],
  '33-33-33': ['1-3', '1-3', '1-3'],
  '25-25-25-25': ['1-4', '1-4', '1-4', '1-4'],
  '66-33': ['2-3', '1-3'],
  '33-66': ['1-3', '2-3'],
  '25-50-25': ['1-4', '1-2', '1-4'],
  '20-20-20-20-20': ['1-5', '1-5', '1-5', '1-5', '1-5'],
  '16-16-16-16-16-16': ['1-6', '1-6', '1-6', '1-6', '1-6', '1-6'],
};

function insertRow(layoutKey) {
  const widths = layoutColWidths[layoutKey] || ['1-1'];
  const cols = widths.map(w => createColumn(w, []));
  const row = createRow(layoutKey, cols);
  const section = createSection([row]);
  const idx = insertAtIndex.value != null ? insertAtIndex.value : tilesStore.canvasTiles.length;
  tilesStore.canvasTiles.splice(idx, 0, section);
  builderStore.isDirty = true;
  builderStore.selectTile(row.id);
  close();
}

// Grid template categories
const gridCategories = [
  { key: 'columns', label: 'Colonne', templates: gridColumns },
  { key: 'multirow', label: 'Multi-Riga', templates: multirow },
  { key: 'masonry', label: 'Masonry', templates: masonry },
  { key: 'sidebar', label: 'Sidebar', templates: sidebar },
];

const filteredGridCategories = computed(() => {
  const q = searchQuery.value.trim().toLowerCase();
  if (!q) return gridCategories;
  return gridCategories.map(cat => ({
    ...cat,
    templates: cat.templates.filter(t => t.name.toLowerCase().includes(q) || t.id.toLowerCase().includes(q)),
  })).filter(cat => cat.templates.length > 0);
});

function insertGridRow(templateId) {
  const tpl = TEMPLATES_MAP[templateId];
  if (!tpl) return;
  const cols = tpl.cells.map(cell => {
    const col = createColumn('1-1', []);
    col.settings = { ...col.settings, grid_column: cell.gridColumn || '', grid_row: cell.gridRow || '' };
    return col;
  });
  const row = createRow('custom', cols);
  row.settings = {
    ...row.settings,
    layout_mode: 'grid',
    grid_template: templateId,
    grid_columns: tpl.gridTemplateColumns,
    grid_rows: tpl.gridTemplateRows,
  };
  const section = createSection([row]);
  const idx = insertAtIndex.value != null ? insertAtIndex.value : tilesStore.canvasTiles.length;
  tilesStore.canvasTiles.splice(idx, 0, section);
  builderStore.isDirty = true;
  builderStore.selectTile(row.id);
  close();
}

function gridPreviewSvg(tpl) {
  if (!tpl.preview) return '';
  const p = tpl.preview;
  const cw = 60, ch = 40;
  const cellW = cw / p.cols, cellH = ch / p.rows, gap = 2;
  let rects = '';
  for (const r of p.rects) {
    const x = r.x * cellW + gap/2, y = r.y * cellH + gap/2;
    const w = r.w * cellW - gap, h = r.h * cellH - gap;
    rects += '<rect x="'+x+'" y="'+y+'" width="'+w+'" height="'+h+'" rx="2" fill="#d1d5db"/>';
  }
  return '<svg width="'+cw+'" height="'+ch+'" viewBox="0 0 '+cw+' '+ch+'">'+rects+'</svg>';
}

function openLibrary() {
  close();
  emit('open-library');
}

defineExpose({ open, close });
</script>

<style scoped>
/* Panel container */
.ip-panel {
  width: 720px;
  max-width: 95vw;
  max-height: 80vh;
  display: flex;
  flex-direction: column;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 25px 60px rgba(0,0,0,0.25), 0 0 0 1px rgba(0,0,0,0.08);
  overflow: hidden;
  animation: ip-slide-up 0.2s ease-out;
}

@keyframes ip-slide-up {
  from { opacity: 0; transform: translateY(20px) scale(0.97); }
  to   { opacity: 1; transform: translateY(0) scale(1); }
}

/* Header */
.ip-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px 12px;
}
.ip-title {
  font-size: 15px;
  font-weight: 600;
  color: #1a1a1a;
}
.ip-close {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  border-radius: 6px;
  border: none;
  background: none;
  color: #9CA3AF;
  cursor: pointer;
  transition: all 0.15s;
}
.ip-close:hover {
  background: #f3f4f6;
  color: #374151;
}

/* Tabs */
.ip-tabs {
  display: flex;
  gap: 0;
  padding: 0 20px;
  border-bottom: 1px solid #e5e7eb;
}
.ip-tab {
  padding: 8px 16px;
  font-size: 13px;
  font-weight: 500;
  color: #6B7280;
  background: none;
  border: none;
  border-bottom: 2px solid transparent;
  cursor: pointer;
  transition: all 0.15s;
  margin-bottom: -1px;
}
.ip-tab:hover {
  color: #374151;
}
.ip-tab--active {
  color: var(--olo-ui-accent, #e8622a);
  border-bottom-color: var(--olo-ui-accent, #e8622a);
}

/* Search */
.ip-search {
  display: flex;
  align-items: center;
  gap: 8px;
  margin: 12px 20px;
  padding: 8px 12px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #f9fafb;
  transition: border-color 0.15s;
}
.ip-search:focus-within {
  border-color: var(--olo-ui-accent, #e8622a);
  background: #fff;
}
.ip-search-icon {
  color: #9CA3AF;
  flex-shrink: 0;
}
.ip-search-input {
  flex: 1;
  border: none;
  background: none;
  outline: none;
  font-size: 13px;
  color: #374151;
  font-family: inherit;
}
.ip-search-input::placeholder {
  color: #9CA3AF;
}

/* Body */
.ip-body {
  flex: 1;
  overflow-y: auto;
  padding: 4px 20px 20px;
}

/* Module list grouped by category */
.ip-modules {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.ip-cat-header {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .04em;
  color: #9ca3af;
  margin: 8px 0 -2px;
}
.ip-modules > .ip-cat-header:first-child {
  margin-top: 0;
}

/* Module grid */
.ip-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
  gap: 8px;
}
.ip-module-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  padding: 14px 6px 10px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #fff;
  cursor: pointer;
  transition: all 0.15s;
}
.ip-module-card:hover {
  border-color: var(--olo-ui-accent, #e8622a);
  background: rgba(232, 98, 42, 0.04);
  box-shadow: 0 2px 8px rgba(232, 98, 42, 0.12);
  transform: translateY(-1px);
}
.ip-module-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: #f3f4f6;
  color: #6B7280;
  transition: all 0.15s;
}
.ip-module-card:hover .ip-module-icon {
  background: rgba(232, 98, 42, 0.1);
  color: var(--olo-ui-accent, #e8622a);
}
.ip-module-name {
  font-size: 10px;
  font-weight: 500;
  color: #374151;
  text-align: center;
  line-height: 1.2;
  max-width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* Row layout grid */
.ip-row-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 10px;
}
.ip-row-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  padding: 16px 12px 12px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #fff;
  cursor: pointer;
  transition: all 0.15s;
}
.ip-row-card:hover {
  border-color: var(--olo-ui-accent, #e8622a);
  background: rgba(232, 98, 42, 0.04);
  box-shadow: 0 2px 8px rgba(232, 98, 42, 0.12);
}
.ip-row-preview {
  display: flex;
  gap: 3px;
  width: 100%;
  height: 28px;
}
.ip-row-col {
  background: #e5e7eb;
  border-radius: 3px;
  transition: background 0.15s;
}
.ip-row-card:hover .ip-row-col {
  background: rgba(232, 98, 42, 0.25);
}
.ip-row-label {
  font-size: 11px;
  font-weight: 500;
  color: #6B7280;
}
.ip-row-preview--grid {
  display: flex;
  align-items: center;
  justify-content: center;
}
.ip-row-preview--grid :deep(svg) {
  width: 100%;
  height: 100%;
}

/* Section labels */
.ip-section-label {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #9CA3AF;
  padding: 16px 0 8px;
  display: flex;
  align-items: center;
  gap: 6px;
}
.ip-section-label:first-child {
  padding-top: 0;
}
.ip-section-badge {
  font-size: 9px;
  font-weight: 600;
  padding: 1px 5px;
  border-radius: 3px;
  background: rgba(232, 98, 42, 0.1);
  color: var(--olo-ui-accent, #e8622a);
  text-transform: none;
  letter-spacing: 0;
}

/* Library tab */
.ip-library {
  padding: 20px 0;
}
.ip-library-msg {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  text-align: center;
  color: #6B7280;
  font-size: 13px;
}
.ip-library-msg p {
  margin: 0;
  max-width: 280px;
}
.ip-library-btn {
  padding: 8px 20px;
  border-radius: 8px;
  border: 1px solid var(--olo-ui-accent, #e8622a);
  background: var(--olo-ui-accent, #e8622a);
  color: #fff;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.15s;
}
.ip-library-btn:hover {
  opacity: 0.9;
  box-shadow: 0 2px 8px rgba(232, 98, 42, 0.3);
}

/* Empty state */
.ip-empty {
  grid-column: 1 / -1;
  text-align: center;
  color: #9CA3AF;
  font-size: 13px;
  padding: 32px 0;
}

/* Fade transition */
.ip-fade-enter-active { transition: opacity 0.15s ease; }
.ip-fade-leave-active { transition: opacity 0.1s ease; }
.ip-fade-enter-from,
.ip-fade-leave-to { opacity: 0; }
</style>
