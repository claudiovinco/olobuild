<template>
  <div class="mb-w-60 mb-bg-gray-800 mb-border-r mb-border-gray-700 mb-overflow-y-auto mb-shrink-0">
    <!-- Tab switcher -->
    <div class="sidebar-tabs">
      <button
        class="sidebar-tab"
        :class="{ 'sidebar-tab--active': activeTab === 'tiles' }"
        @click="activeTab = 'tiles'"
      >
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="3" width="7" height="7" rx="1"/>
          <rect x="14" y="3" width="7" height="7" rx="1"/>
          <rect x="3" y="14" width="7" height="7" rx="1"/>
          <rect x="14" y="14" width="7" height="7" rx="1"/>
        </svg>
        Elementi
      </button>
      <button
        class="sidebar-tab"
        :class="{ 'sidebar-tab--active': activeTab === 'structure' }"
        @click="activeTab = 'structure'"
      >
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M3 12h4l3-9 4 18 3-9h4"/>
        </svg>
        Struttura
      </button>
    </div>

    <!-- Insert-after banner -->
    <div v-if="builderStore.insertAfterTileId" class="tp-insert-banner">
      <span>⊕ Seleziona un elemento da inserire</span>
      <button class="tp-insert-cancel" @click="builderStore.insertAfterTileId = null">✕</button>
    </div>

    <!-- Tiles tab -->
    <div v-if="activeTab === 'tiles'" class="tp-root">
      <div v-for="(tiles, category) in tilesByCategory" :key="category" class="tp-group">
        <button class="tp-cat-head" @click="toggleCategory(category)">
          <span class="tp-cat-dot" :style="{ background: catColor(category) }"></span>
          <span class="tp-cat-label">{{ categoryLabel(category) }}</span>
          <span class="tp-cat-count">{{ tiles.length }}</span>
          <svg class="tp-chevron" :class="{ 'tp-chevron--open': isCategoryOpen(category) }" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="tp-drawer" :class="{ 'tp-drawer--open': isCategoryOpen(category) }">
          <div class="tp-grid">
            <button
              v-for="tile in tiles"
              :key="tile.type"
              class="tp-btn"
              :style="{ '--cat-color': catColor(category) }"
              draggable="true"
              @dragstart="onDragStart($event, tile.type)"
              @click="addTile(tile.type)"
              :title="tile.name"
            ><span class="tp-btn-icon" v-html="tileIcon(tile.type)"></span><span class="tp-btn-label">{{ tile.name }}</span></button>
          </div>
        </div>
      </div>

      <div v-if="Object.keys(tilesByCategory).length === 0" class="tp-empty">
        Caricamento tile...
      </div>

      <!-- Global Widgets section -->
      <div v-if="tilesStore.globalWidgets.length > 0" class="tp-group">
        <button class="tp-cat-head" @click="toggleCategory('_global')">
          <span class="tp-cat-dot" style="background: #D97706"></span>
          <span class="tp-cat-label">Widget Globali</span>
          <span class="tp-cat-count">{{ tilesStore.globalWidgets.length }}</span>
          <svg class="tp-chevron" :class="{ 'tp-chevron--open': isCategoryOpen('_global') }" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="tp-drawer" :class="{ 'tp-drawer--open': isCategoryOpen('_global') }">
          <div class="tp-grid">
            <div
              v-for="gw in tilesStore.globalWidgets"
              :key="'gw-' + gw.id"
              class="tp-gw-wrap"
            >
              <button
                class="tp-btn tp-btn--global"
                draggable="true"
                @dragstart="onGlobalDragStart($event, gw.id)"
                @click="addGlobalWidget(gw.id)"
                :title="gw.name"
              ><span class="tp-btn-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></span><span class="tp-btn-label">{{ gw.name }}</span></button>
              <button
                class="tp-gw-del"
                title="Elimina widget globale"
                @click.stop="deleteGlobal(gw.id, gw.name)"
              >&times;</button>
            </div>
          </div>
        </div>
      </div>

      <p class="tp-hint">Clicca o trascina per aggiungere</p>
    </div>

    <!-- Structure tab -->
    <StructureTree v-else @save-as-template="section => $emit('save-as-template', section)" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useTilesStore } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';
import { useDragDrop } from '@/composables/useDragDrop';
import { createSection, createRow, createColumn, generateId } from '@/stores/tiles';
import StructureTree from './StructureTree.vue';

const tilesStore = useTilesStore();
const builderStore = useBuilderStore();
const { handleDropFromSidebar } = useDragDrop();

import { onUnmounted } from 'vue';

function onOpenElementsPanel() {
  activeTab.value = 'tiles';
}
window.addEventListener('olo:open-elements-panel', onOpenElementsPanel);
onUnmounted(() => {
  window.removeEventListener('olo:open-elements-panel', onOpenElementsPanel);
});

onMounted(() => {
  tilesStore.fetchGlobalWidgets();
});

const activeTab = ref('tiles');
const tilesByCategory = computed(() => tilesStore.tilesByCategory);

// Collapsible categories — persisted in localStorage
const STORAGE_KEY = 'olo_sidebar_collapsed';
function loadCollapsed() {
  try {
    const saved = localStorage.getItem(STORAGE_KEY);
    return saved ? new Set(JSON.parse(saved)) : new Set();
  } catch { return new Set(); }
}
const collapsedCategories = ref(loadCollapsed());

function toggleCategory(category) {
  const s = new Set(collapsedCategories.value);
  if (s.has(category)) {
    s.delete(category);
  } else {
    s.add(category);
  }
  collapsedCategories.value = s;
  try { localStorage.setItem(STORAGE_KEY, JSON.stringify([...s])); } catch {}
}

function isCategoryOpen(category) {
  return !collapsedCategories.value.has(category);
}

const categoryColors = {
  essential: 'var(--olo-color-primary, #6366F1)',
  layout: '#3B82F6',
  text: '#22C55E',
  media: '#A855F7',
  marketing: '#F59E0B',
  interactive: '#06B6D4',
  navigation: '#F43F5E',
  dynamic: '#F97316',
  booking: '#EAB308',
  'olo-space': '#14B8A6',
};

// Ordine fisso delle categorie nella sidebar
const categoryOrder = [
  'essential', 'layout', 'text', 'media', 'marketing',
  'interactive', 'navigation', 'dynamic', 'booking', 'olo-space',
];

const categoryLabels = {
  essential: 'Essenziale',
  layout: 'Layout',
  text: 'Testo',
  media: 'Media',
  marketing: 'Marketing',
  interactive: 'Interattivo',
  navigation: 'Navigazione',
  dynamic: 'Dinamico',
  booking: 'Olo Booking',
  'olo-space': 'Olo Space',
};

function categoryLabel(category) {
  return categoryLabels[category] || category;
}

function catColor(category) {
  return categoryColors[category] || '#6B7280';
}

// ── SVG icon map per tile type ──
const tileIcons = {
  // Essential
  'headline':     '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 4v16M20 4v16M4 12h16"/></svg>',
  'text-block':   '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 6h16M4 10h16M4 14h10"/></svg>',
  'content':      '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 6h16M4 10h16M4 14h16M4 18h8"/></svg>',
  'image':        '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>',
  'video':        '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="4" w="20" h="16" rx="2"/><path d="M10 9l5 3-5 3z"/></svg>',
  'button':       '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="7" w="18" h="10" rx="5"/><path d="M8 12h8"/></svg>',
  'icon':         '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
  'spacer':       '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 6h16M4 18h16M12 9v6M9 10l3-1 3 1M9 14l3 1 3-1"/></svg>',
  'divider':      '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M3 12h18"/><circle cx="12" cy="12" r="1"/></svg>',
  // Layout
  'row':          '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="18" rx="2"/><path d="M9 3v18M15 3v18"/></svg>',
  'inner-columns':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="5" w="18" h="14" rx="2"/><path d="M12 5v14"/></svg>',
  'grid':         '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="7" h="7"/><rect x="14" y="3" w="7" h="7"/><rect x="3" y="14" w="7" h="7"/><rect x="14" y="14" w="7" h="7"/></svg>',
  'hero':         '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="3" w="20" h="18" rx="2"/><path d="M7 10h10M7 14h6"/></svg>',
  'section':      '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="4" w="20" h="16" rx="2"/><path d="M2 8h20"/></svg>',
  'fragment':     '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M8 3H5a2 2 0 00-2 2v3M16 3h3a2 2 0 012 2v3M8 21H5a2 2 0 01-2-2v-3M16 21h3a2 2 0 002-2v-3"/></svg>',
  'shapedivider': '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M2 17c4-6 6 2 10-2s6-6 10 0"/></svg>',
  'templateembed':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="18" rx="2"/><path d="M9 3v18M3 9h6M3 15h6"/></svg>',
  // Text
  'animatedheading':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 4v16M20 4v16M4 12h16"/><path d="M7 20l2-2 2 2" stroke-dasharray="2 2"/></svg>',
  'list':         '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M9 6h11M9 12h11M9 18h11"/><circle cx="4" cy="6" r="1" fill="currentColor"/><circle cx="4" cy="12" r="1" fill="currentColor"/><circle cx="4" cy="18" r="1" fill="currentColor"/></svg>',
  'iconlist':     '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M10 6h11M10 12h11M10 18h11"/><path d="M3 6l1.5 1.5L7 5M3 12l1.5 1.5L7 11M3 18l1.5 1.5L7 17"/></svg>',
  'desclist':     '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 5h16M4 9h10M4 15h16M4 19h10"/></svg>',
  'table':        '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="18" rx="2"/><path d="M3 9h18M3 15h18M9 3v18"/></svg>',
  'alert':        '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><path d="M12 9v4M12 17h.01"/></svg>',
  'quotation':    '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V21z"/><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3z"/></svg>',
  'code':         '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/></svg>',
  'html':         '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M16 18l6-6-6-6M8 6l-6 6 6 6M14 4l-4 16"/></svg>',
  'textpath':     '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M2 18c4-8 8 4 12-4s4 0 8-4"/><path d="M4 12h2"/></svg>',
  // Media
  'gallery':      '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="2" w="8" h="8" rx="1"/><rect x="14" y="2" w="8" h="8" rx="1"/><rect x="2" y="14" w="8" h="8" rx="1"/><rect x="14" y="14" w="8" h="8" rx="1"/></svg>',
  'carousel':     '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="4" y="4" w="16" h="16" rx="2"/><path d="M1 10v4M23 10v4"/></svg>',
  'slideshow':    '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="4" w="20" h="16" rx="2"/><path d="M7 15l5-5 5 5"/></svg>',
  'audio':        '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M3 18v-6a9 9 0 0118 0v6"/><path d="M21 19a2 2 0 01-2 2h-1a2 2 0 01-2-2v-3a2 2 0 012-2h3zM3 19a2 2 0 002 2h1a2 2 0 002-2v-3a2 2 0 00-2-2H3z"/></svg>',
  'lottie':       '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="12" cy="12" r="10"/><path d="M8 12c1-3 3 3 4 0s3 3 4 0"/></svg>',
  'overlay':      '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="18" rx="2"/><rect x="7" y="7" w="10" h="10" rx="1" stroke-dasharray="3 2"/></svg>',
  'lightbox':     '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/></svg>',
  'map':          '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>',
  'marquee':      '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M3 12h18M16 7l5 5-5 5"/></svg>',
  'imgcompare':   '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="18" rx="2"/><path d="M12 3v18M8 12H3M16 12h5"/></svg>',
  'soundcloud':   '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M3 18v-4M7 18V8M11 18v-8M15 18V6M19 18v-6"/></svg>',
  'pdfviewer':    '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M10 13h4"/></svg>',
  'pdfpro':       '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M10 13h4M10 17h4"/></svg>',
  'progallery':   '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="2" w="9" h="9" rx="1"/><rect x="13" y="2" w="9" h="5" rx="1"/><rect x="13" y="9" w="9" h="9" rx="1" style="fill:none"/><rect x="2" y="13" w="9" h="9" rx="1"/></svg>',
  'proslider':    '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="4" w="18" h="16" rx="2"/><path d="M8 20l4-4 4 4M1 12h2M21 12h2"/></svg>',
  'shatteredimage':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="18" rx="2"/><path d="M3 10l8 2 3-9M11 12l10-2M11 12l-4 9M11 12l10 9"/></svg>',
  'videoplaylist':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="3" w="14" h="10" rx="2"/><path d="M10 8l-3 2V6zM20 5h2M20 9h2M20 13h2M18 17h4M18 21h4"/></svg>',
  // Marketing
  'form':         '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="18" rx="2"/><path d="M7 7h10M7 12h10M7 17h5"/></svg>',
  'countdown':    '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>',
  'counter':      '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M5 19V5M9 19V9M13 19v-6M17 19V7M21 19v-4"/></svg>',
  'testimonial':  '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>',
  'pricing':      '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="18" rx="2"/><path d="M12 7v2M12 15v2M9 11h6M8 14h8"/></svg>',
  'team':         '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>',
  'social':       '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="M8.59 13.51l6.83 3.98M15.41 6.51l-6.82 3.98"/></svg>',
  'sharebuttons': '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="M8.59 13.51l6.83 3.98M15.41 6.51l-6.82 3.98"/></svg>',
  'progress':     '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="10" w="20" h="4" rx="2"/><rect x="2" y="10" w="12" h="4" rx="2" fill="currentColor" opacity="0.3"/></svg>',
  'starrating':   '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
  'flipcard':     '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="4" w="20" h="16" rx="2"/><path d="M12 4v16" stroke-dasharray="3 2"/><path d="M17 10l2 2-2 2"/></svg>',
  'iconbox':      '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="18" rx="2"/><circle cx="12" cy="9" r="3"/><path d="M8 17h8"/></svg>',
  'instagram':    '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="2" w="20" h="20" rx="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor"/></svg>',
  'facebookpage': '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>',
  'twitterfeed':  '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 4l11.7 16h4.3M4 20L20 4M8 4H4l5.3 7.3M20 20h-4l-5.3-7.3"/></svg>',
  'loginform':    '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="11" w="18" h="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/><circle cx="12" cy="16" r="1"/></svg>',
  'paymentbuttons':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="5" w="20" h="14" rx="2"/><path d="M2 10h20"/></svg>',
  'linkinbio':    '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="12" cy="7" r="4"/><path d="M6 14h12M7 18h10M8 22h8"/></svg>',
  // Interactive
  'accordion':    '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="5" rx="1"/><rect x="3" y="10" w="18" h="5" rx="1"/><rect x="3" y="17" w="18" h="5" rx="1"/></svg>',
  'switcher':     '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="18" rx="2"/><path d="M3 8h18M8 3v5M14 3v5"/></svg>',
  'switcherpanel':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="18" rx="2"/><path d="M3 8h18M9 8v13"/></svg>',
  'timeline':     '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M12 2v20"/><circle cx="12" cy="6" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="12" cy="18" r="2"/><path d="M14 6h5M5 12h7M14 18h5"/></svg>',
  'popup':        '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="4" y="4" w="16" h="16" rx="2"/><path d="M9 2h6M15 4V2M9 4V2M9 9l6 6M15 9l-6 6"/></svg>',
  'panel':        '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="18" rx="2"/><path d="M3 9h18"/></svg>',
  'panelslider':  '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="18" rx="2"/><path d="M3 9h18M1 12h2M21 12h2"/></svg>',
  'hotspot':      '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="12" cy="12" r="3"/><circle cx="12" cy="12" r="8" stroke-dasharray="4 3"/></svg>',
  'chart':        '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M3 20V8M8 20V4M13 20v-8M18 20V6M23 20v-4"/></svg>',
  'popover':      '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="4" y="2" w="16" h="12" rx="2"/><path d="M8 14l4 4 4-4"/></svg>',
  'darkmode':     '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>',
  'overlaygrid':  '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="2" w="9" h="9" rx="1"/><rect x="13" y="2" w="9" h="9" rx="1"/><rect x="2" y="13" w="9" h="9" rx="1"/><rect x="13" y="13" w="9" h="9" rx="1"/></svg>',
  'overlayslider':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="18" rx="2"/><path d="M1 12h3M20 12h3M10 14h4"/></svg>',
  'scrollprogress':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="2" w="20" h="3" rx="1.5"/><rect x="2" y="2" w="10" h="3" rx="1.5" fill="currentColor" opacity="0.3"/><path d="M4 10h16M4 15h16M4 20h10"/></svg>',
  'togglebtn':    '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="1" y="7" w="22" h="10" rx="5"/><circle cx="16" cy="12" r="3"/></svg>',
  // Navigation
  'nav':          '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M3 6h18M3 12h18M3 18h18"/></svg>',
  'navmenu':      '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M3 6h18M3 12h18M3 18h18"/></svg>',
  'megamenu':     '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M3 6h18M3 12h18"/><rect x="3" y="15" w="18" h="6" rx="1" stroke-dasharray="3 2"/></svg>',
  'offcanvas':    '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="3" w="20" h="18" rx="2"/><path d="M9 3v18M3 8h6M3 12h6M3 16h4"/></svg>',
  'breadcrumbs':  '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M3 12h4l2-2 2 2h4l2-2 2 2h2"/></svg>',
  'search':       '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>',
  'livesearch':   '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35M8 11h6"/></svg>',
  'pagination':   '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M5 12h14M15 8l4 4-4 4M9 8l-4 4 4 4"/></svg>',
  'sitelogo':     '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>',
  'subnav':       '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 6h16M8 12h12M8 18h12"/></svg>',
  'toc':          '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 6h16M8 10h12M8 14h12M4 18h16"/><circle cx="4" cy="10" r="1" fill="currentColor"/><circle cx="4" cy="14" r="1" fill="currentColor"/></svg>',
  'totop':        '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M12 19V5M5 12l7-7 7 7"/></svg>',
  'menuanchor':   '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 4h16M12 4v16M8 20h8"/></svg>',
  'postnavigation':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M5 12h14M8 8l-4 4 4 4M16 8l4 4-4 4"/></svg>',
  'langswitcher': '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10A15.3 15.3 0 0112 2z"/></svg>',
  'killnextprev': '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M18 6L6 18M6 6l12 12"/></svg>',
  // Dynamic
  'postgrid':     '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="2" w="9" h="9" rx="1"/><rect x="13" y="2" w="9" h="9" rx="1"/><rect x="2" y="13" w="9" h="9" rx="1"/><rect x="13" y="13" w="9" h="9" rx="1"/><path d="M5 7h3M16 7h3"/></svg>',
  'queryloop':    '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 4h16v5H4zM4 11h16v5H4zM4 18h16"/><circle cx="20" cy="15" r="3" stroke-dasharray="2 2"/></svg>',
  'shortcode':    '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M7 8l-4 4 4 4M17 8l4 4-4 4M14 4l-4 16"/></svg>',
  'portfolio':    '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="2" w="20" h="20" rx="2"/><path d="M2 8h20M8 2v6"/></svg>',
  'authorbox':    '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="8" cy="8" r="4"/><path d="M2 20c0-3.3 2.7-6 6-6s6 2.7 6 6M16 7h6M16 11h6M16 15h4"/></svg>',
  'relatedposts': '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="2" w="6" h="8" rx="1"/><rect x="10" y="2" w="6" h="8" rx="1"/><rect x="18" y="2" w="4" h="8" rx="1"/><path d="M2 14h20M2 18h14"/></svg>',
  'tagcloud':     '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><circle cx="7" cy="7" r="1.5" fill="currentColor"/></svg>',
  'sitemap':      '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="8" y="2" w="8" h="4" rx="1"/><rect x="2" y="18" w="6" h="4" rx="1"/><rect x="9" y="18" w="6" h="4" rx="1"/><rect x="16" y="18" w="6" h="4" rx="1"/><path d="M12 6v6M5 18v-4h14v4M12 12v6"/></svg>',
  'newsticker':   '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="8" w="20" h="8" rx="2"/><path d="M6 12h12M18 10l2 2-2 2"/></svg>',
  'readingtime':  '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l3 3"/></svg>',
  'postmeta':     '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>',
  'wpcomments':   '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>',
  'viewscounter': '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>',
  'osmmap':       '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>',
  'pricelist':    '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 6h10M18 6h2M4 12h6M14 12h8M4 18h12M20 18h2"/></svg>',
  'progresstracker':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="5" cy="12" r="3"/><circle cx="12" cy="12" r="3"/><circle cx="19" cy="12" r="3" stroke-dasharray="3 2"/><path d="M8 12h1M15 12h1"/></svg>',
  'countercircle':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6"/></svg>',
  'blendtext':    '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 4v16M20 4v16M4 12h16" opacity="0.5"/><path d="M6 8h12M6 16h12" stroke-dasharray="3 2"/></svg>',
  'textmask':     '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="4" w="20" h="16" rx="2"/><path d="M6 12h4M14 12h4M10 8v8"/></svg>',
  'pagetitlebar': '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="4" w="20" h="6" rx="2"/><path d="M6 7h8M6 14h16M6 18h10"/></svg>',
  'column':       '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="8" y="2" w="8" h="20" rx="2"/></svg>',
  // Aliases
  'heading':      '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 4v16M20 4v16M4 12h16"/></svg>',
  'textblock':    '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 6h16M4 10h16M4 14h10"/></svg>',
  'slider':       '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="4" w="18" h="16" rx="2"/><path d="M8 12l3 3 3-3"/></svg>',
  // Header / Marketing extras
  'floatingpanel':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="5" y="5" w="14" h="14" rx="2"/><path d="M5 9h14"/><circle cx="8" cy="7" r=".8" fill="currentColor"/><circle cx="10.5" cy="7" r=".8" fill="currentColor"/></svg>',
  'mobilebar':    '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="5" y="2" w="14" h="20" rx="2"/><path d="M8 18h8"/></svg>',
  // Booking / Service
  'booking':      '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="4" w="18" h="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/><path d="M8 14l3 3 5-5"/></svg>',
  'bookingpicker':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="4" w="18" h="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/><circle cx="12" cy="16" r="2" fill="currentColor"/></svg>',
  'calendar':     '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="4" w="18" h="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>',
  'hostcard':     '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="18" rx="2"/><circle cx="9" cy="10" r="3"/><path d="M14 8h5M14 12h5"/><path d="M6 18c0-2 1.5-3 3-3s3 1 3 3"/></svg>',
  'servicesearch':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="10" cy="10" r="7"/><path d="M21 21l-4.35-4.35" sw="2"/></svg>',
  'serviceresults':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="8" h="8" rx="1"/><rect x="13" y="3" w="8" h="8" rx="1"/><rect x="3" y="13" w="8" h="8" rx="1"/><rect x="13" y="13" w="8" h="8" rx="1"/></svg>',
  'servicelist':  '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="4" w="18" h="5" rx="1"/><rect x="3" y="11" w="18" h="5" rx="1"/><rect x="3" y="18" w="18" h="5" rx="1" stroke-dasharray="3 2"/></svg>',
  'servicestats': '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="14" w="4" h="7" rx="1"/><rect x="10" y="8" w="4" h="13" rx="1"/><rect x="17" y="3" w="4" h="18" rx="1"/></svg>',
  'servicehero':  '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="3" w="20" h="18" rx="2"/><path d="M6 14h12M6 18h8"/></svg>',
  'servicegallery':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="8" h="8" rx="1"/><rect x="13" y="3" w="8" h="8" rx="1"/><rect x="3" y="13" w="18" h="8" rx="1"/></svg>',
  'serviceinfo':  '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="12" cy="12" r="9"/><path d="M12 8h.01" sw="2.5"/><path d="M12 12v4"/></svg>',
  'serviceprices':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M12 2v20M9 5.5h4.5a2.5 2.5 0 010 5H9h5.5a2.5 2.5 0 010 5H9"/></svg>',
  'serviceaddress':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>',
  'serviceamenities':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M3.5 5l1.5 1.5 3-3M3.5 11l1.5 1.5 3-3M3.5 17l1.5 1.5 3-3"/><path d="M11 6h10M11 12h8M11 18h10"/></svg>',
  'servicedescription':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 5h16M4 9h12M4 13h16M4 17h8"/></svg>',
  'serviceexcerpt':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 5h16M4 9h12M4 13h8"/></svg>',
  'servicedirections':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M3 12h18M18 8l4 4-4 4"/><circle cx="5" cy="12" r="2"/></svg>',
  'servicerules': '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="5" y="3" w="14" h="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h4"/></svg>',
  'servicerelated':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="5" w="6" h="8" rx="1"/><rect x="9" y="5" w="6" h="8" rx="1"/><rect x="16" y="5" w="6" h="8" rx="1"/></svg>',
  'servicevideo': '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="4" w="20" h="16" rx="2"/><path d="M10 9l5 3-5 3z"/></svg>',
  'servicecheckin':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="4" w="18" h="18" rx="2"/><path d="M16 2v4M8 2v4"/><path d="M8 14l3 3 5-5"/></svg>',
  'servicecipat': '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="4" y="3" w="16" h="18" rx="2"/><path d="M8 8h8M8 12h6M8 16h4"/></svg>',
  'serviceclub':  '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
  'servicemushrooms':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M8 16c0-5 2-9 4-9s4 4 4 9"/><path d="M5 16h14" sw="2"/><path d="M10 16v4M14 16v4"/></svg>',
  // OLO Room
  'olo_room_availability':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="4" w="18" h="18" rx="2"/><path d="M8 2v4M16 2v4M3 10h18"/><path d="M8 14l3 3 5-5"/></svg>',
  'olo_room_calendar':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="4" w="18" h="18" rx="2"/><path d="M8 2v4M16 2v4M3 10h18"/></svg>',
  'olo_room_contacts':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="5" w="18" h="14" rx="2"/><path d="M3 7l9 5 9-5"/></svg>',
  'olo_room_description':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 5h16M4 9h12M4 13h16M4 17h8"/></svg>',
  'olo_room_gallery':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="8" h="8" rx="1"/><rect x="13" y="3" w="8" h="8" rx="1"/><rect x="3" y="13" w="18" h="8" rx="1"/></svg>',
  'olo_room_grid':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="8" h="8" rx="1"/><rect x="13" y="3" w="8" h="8" rx="1"/><rect x="3" y="13" w="8" h="8" rx="1"/><rect x="13" y="13" w="8" h="8" rx="1"/></svg>',
  'olo_room_hero':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="3" w="20" h="18" rx="2"/><path d="M6 14h12M6 18h8"/></svg>',
  'olo_room_info':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="12" cy="12" r="9"/><path d="M12 8h.01" sw="2.5"/><path d="M12 12v4"/></svg>',
  'olo_room_pricing':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M12 2v20M9 5.5h4.5a2.5 2.5 0 010 5H9h5.5a2.5 2.5 0 010 5H9"/></svg>',
  'olo_room_related':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="5" w="6" h="8" rx="1"/><rect x="9" y="5" w="6" h="8" rx="1"/><rect x="16" y="5" w="6" h="8" rx="1"/></svg>',
  // WooCommerce
  'woo_products': '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="8" h="8" rx="1"/><rect x="13" y="3" w="8" h="8" rx="1"/><rect x="3" y="13" w="8" h="8" rx="1"/><rect x="13" y="13" w="8" h="8" rx="1"/></svg>',
  'woo_cart':     '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>',
  'woo_minicart': '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4zM3 6h18"/><path d="M16 10a4 4 0 01-8 0"/></svg>',
  'woo_checkout': '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="18" rx="2"/><path d="M8 10l3 3 5-5"/></svg>',
  'woo_checkout_multistep':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="5" cy="6" r="2.5" fill="currentColor" opacity=".3"/><circle cx="12" cy="6" r="2.5"/><circle cx="19" cy="6" r="2.5"/><path d="M7.5 6h2M14.5 6h2"/><rect x="3" y="11" w="18" h="10" rx="2"/></svg>',
  'woo_myaccount':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="12" cy="8" r="4"/><path d="M5 20c0-4 3-6 7-6s7 2 7 6"/></svg>',
  'woo_price':    '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M12 2v20M9 5.5h4.5a2.5 2.5 0 010 5H9h5.5a2.5 2.5 0 010 5H9"/></svg>',
  'woo_product_image':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="18" rx="2"/><circle cx="8.5" cy="8.5" r="2"/><path d="M3 16l5-5 4 4 4-6 5 7"/></svg>',
  'woo_product_title':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 4v16M20 4v16M4 12h16"/></svg>',
  'woo_product_description':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 5h16M4 9h12M4 13h16M4 17h8"/></svg>',
  'woo_product_tabs':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="7" w="18" h="14" rx="2"/><path d="M3 11h18M7 7V4h4v3M13 7V4h4v3"/></svg>',
  'woo_product_meta':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 7h4M12 7h4M4 12h8M4 17h6"/><circle cx="10" cy="7" r="1.5"/></svg>',
  'woo_product_gallery_slider':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="12" rx="2"/><rect x="3" y="17" w="5" h="4" rx="1"/><rect x="10" y="17" w="5" h="4" rx="1"/><rect x="17" y="17" w="4" h="4" rx="1"/></svg>',
  'woo_rating':   '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
  'woo_addtocart':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/><path d="M12 9v4M10 11h4"/></svg>',
  'woo_breadcrumbs':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 12h3M11 12h3M18 12h3"/><path d="M8 9l3 3-3 3M15 9l3 3-3 3"/></svg>',
  'woo_categories':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="8" h="8" rx="2"/><rect x="13" y="3" w="8" h="8" rx="2"/><rect x="3" y="13" w="8" h="8" rx="2"/><rect x="13" y="13" w="8" h="8" rx="2"/></svg>',
  'woo_sale_badge':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="12" cy="12" r="9"/><text x="7" y="16" font-size="9" font-family="sans-serif" fill="currentColor" stroke="none" font-weight="700">%</text></svg>',
  'woo_notices':  '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="5" w="18" h="14" rx="2"/><path d="M12 9v3M12 15h.01" sw="2"/></svg>',
  'woo_wishlist': '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 000-7.78z"/></svg>',
  'woo_comparison':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="8" h="18" rx="2"/><rect x="13" y="3" w="8" h="18" rx="2"/><path d="M5 9h4M15 9h4M5 13h4M15 13h4"/></svg>',
  'woo_quickview':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>',
  'woo_recently_viewed':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/><path d="M3 12h2"/></svg>',
  'woo_related':  '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="5" w="6" h="8" rx="1"/><rect x="9" y="5" w="6" h="8" rx="1"/><rect x="16" y="5" w="6" h="8" rx="1"/></svg>',
  'woo_cross_sells':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="5" w="6" h="8" rx="1"/><rect x="9" y="5" w="6" h="8" rx="1"/><rect x="16" y="5" w="6" h="8" rx="1"/><path d="M5 16h14"/></svg>',
  'woo_upsells':  '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="5" w="6" h="8" rx="1"/><rect x="9" y="5" w="6" h="8" rx="1"/><rect x="16" y="5" w="6" h="8" rx="1"/><path d="M12 16v4M10 18l2-2 2 2"/></svg>',
  'woo_product_stock':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="6" w="18" h="15" rx="2"/><path d="M8 6V4M16 6V4"/><path d="M8 13l3 3 5-5"/></svg>',
  'woo_product_bundle':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="8" w="7" h="7" rx="1"/><rect x="14" y="8" w="7" h="7" rx="1"/><rect x="8" y="3" w="8" h="5" rx="1"/><path d="M12 15v6"/></svg>',
  'woo_product_filter':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 6h16M8 12h8M10 18h4"/></svg>',
  'woo_product_navigation':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M3 12h18M6 8l-4 4 4 4M18 8l4 4-4 4"/></svg>',
  'woo_order_tracking':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="5" w="18" h="14" rx="2"/><path d="M3 10h18"/><path d="M7 15h3M14 15h3"/></svg>',
};

function tileIcon(type) {
  const raw = tileIcons[type];
  if (!raw) return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="12" cy="12" r="2"/></svg>';
  // Expand shorthand attributes
  return raw
    .replace(/ w="/g, ' width="')
    .replace(/ h="/g, ' height="')
    .replace(/ vb="/g, ' viewBox="')
    .replace(/ f="/g, ' fill="')
    .replace(/ s="cC"/g, ' stroke="currentColor"')
    .replace(/ sw="/g, ' stroke-width="');
}

let lastDragStart = 0;

function onDragStart(event, tileType) {
  lastDragStart = Date.now();
  event.dataTransfer.setData('tile-type', tileType);
  event.dataTransfer.effectAllowed = 'copy';
}

function addTile(tileType) {
  if (Date.now() - lastDragStart < 1000) return;
  const afterId = builderStore.insertAfterTileId;
  if (afterId) {
    // Insert directly after the target tile (same column, no wrapping)
    const newTile = {
      id: generateId(),
      type: tileType,
      settings: JSON.parse(JSON.stringify(
        (tilesStore.registeredTiles.find(t => t.type === tileType) || {}).defaults || {}
      )),
      style: {},
      advanced: {},
    };
    const inserted = tilesStore.insertAfter(afterId, newTile);
    if (inserted) {
      builderStore.isDirty = true;
      builderStore.selectTile(newTile.id);
    }
    builderStore.insertAfterTileId = null;
  } else {
    handleDropFromSidebar(tileType);
  }
}

function onGlobalDragStart(event, globalId) {
  lastDragStart = Date.now();
  event.dataTransfer.setData('global-widget-id', String(globalId));
  event.dataTransfer.effectAllowed = 'copy';
}

function addGlobalWidget(globalId) {
  if (Date.now() - lastDragStart < 1000) return;
  const newTile = tilesStore.insertGlobalWidget(globalId);
  if (!newTile) return;
  // Wrap in Section > Row > Column
  const col = createColumn('1-1', [newTile]);
  const row = createRow('100', [col]);
  const section = createSection([row]);
  tilesStore.addTile(section);
  builderStore.isDirty = true;
}

async function deleteGlobal(globalId, name) {
  if (!confirm('Eliminare il widget globale "' + name + '"?\nLe istanze già inserite resteranno ma non saranno più sincronizzate.')) return;
  await tilesStore.deleteGlobalWidget(globalId);
}
</script>

<style scoped>
.sidebar-tabs {
  display: flex;
  border-bottom: 1px solid rgba(0, 0, 0, 0.08);
}
.sidebar-tab {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
  padding: 9px 0;
  font-size: 11px;
  font-weight: 500;
  letter-spacing: 0.3px;
  color: #888;
  background: none;
  border: none;
  border-bottom: 2px solid transparent;
  cursor: pointer;
  transition: color 0.15s, border-color 0.15s;
}
.sidebar-tab:hover {
  color: #555;
}
.sidebar-tab--active {
  color: #1a1a1a;
  border-bottom-color: var(--olo-color-primary, #e8622a);
}

/* Insert-after banner */
.tp-insert-banner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 12px;
  margin: 6px 6px 0;
  background: rgba(34, 197, 94, 0.15);
  border: 1px solid rgba(34, 197, 94, 0.4);
  border-radius: 6px;
  color: #22c55e;
  font-size: 12px;
  font-weight: 600;
  animation: tp-insert-pulse 1.5s ease-in-out infinite;
}
@keyframes tp-insert-pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.7; }
}
.tp-insert-cancel {
  background: none;
  border: none;
  color: #22c55e;
  cursor: pointer;
  font-size: 14px;
  padding: 0 4px;
  opacity: 0.6;
}
.tp-insert-cancel:hover {
  opacity: 1;
}

/* Tile palette — light glass */
.tp-root {
  padding: 8px 6px;
  container-type: inline-size;
}
.tp-group {
  margin-bottom: 2px;
}
.tp-cat-head {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 8px;
  width: 100%;
  background: none;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.15s;
  font-family: inherit;
}
.tp-cat-head:hover {
  background: rgba(0, 0, 0, 0.04);
}
.tp-cat-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  flex-shrink: 0;
}
.tp-cat-label {
  font-size: 10px;
  font-weight: 600;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  color: #888;
}
.tp-cat-count {
  font-size: 9px;
  color: #555;
  background: rgba(0, 0, 0, 0.06);
  padding: 0 5px;
  border-radius: 8px;
  line-height: 15px;
}
.tp-chevron {
  margin-left: auto;
  color: #aaa;
  transform: rotate(-90deg);
  transition: transform 0.2s ease;
  flex-shrink: 0;
}
.tp-chevron--open {
  transform: rotate(0deg);
}

/* Collapsible drawer */
.tp-drawer {
  display: grid;
  grid-template-rows: 0fr;
  transition: grid-template-rows 0.25s ease;
}
.tp-drawer--open {
  grid-template-rows: 1fr;
}
.tp-drawer > .tp-grid {
  overflow: hidden;
}

.tp-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4px;
  padding: 2px 4px 6px;
}
@container (max-width: 200px) {
  .tp-grid {
    grid-template-columns: 1fr;
  }
}
.tp-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  height: 38px;
  padding: 0 8px;
  border-radius: 10px;
  cursor: pointer;
  border: 1px solid rgba(0, 0, 0, 0.07);
  background: rgba(255, 255, 255, 0.7);
  font-size: 10px;
  color: #666;
  font-weight: 500;
  font-family: inherit;
  line-height: 1;
  user-select: none;
  transition: background-color 0.12s, border-color 0.12s, color 0.12s, box-shadow 0.12s, transform 0.12s;
}
.tp-btn:hover {
  background: #fff;
  border-color: var(--cat-color, #e8622a);
  color: #1a1a1a;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  transform: translateY(-1px);
}
.tp-btn:active {
  transform: translateY(0);
  box-shadow: none;
  opacity: 0.7;
}
.tp-btn-icon {
  flex-shrink: 0;
  width: 14px;
  height: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0.5;
}
.tp-btn:hover .tp-btn-icon {
  opacity: 0.85;
  color: var(--cat-color, #e8622a);
}
.tp-btn-label {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  min-width: 0;
}
.tp-empty {
  text-align: center;
  color: #aaa;
  font-size: 11px;
  padding: 32px 0;
}
.tp-gw-wrap {
  position: relative;
}
.tp-gw-wrap:hover .tp-gw-del {
  opacity: 1;
}
.tp-gw-del {
  position: absolute;
  top: -4px;
  right: -4px;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: #dc2626;
  color: #fff;
  border: none;
  font-size: 12px;
  line-height: 1;
  cursor: pointer;
  opacity: 0;
  transition: opacity 0.15s;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2;
}
.tp-gw-del:hover {
  background: #b91c1c;
  color: #fff;
}
.tp-btn--global {
  border-color: rgba(217, 119, 6, 0.3);
  color: #d97706;
  background: rgba(217, 119, 6, 0.06);
}
.tp-btn--global:hover {
  background: rgba(217, 119, 6, 0.12) !important;
  border-color: #D97706 !important;
  color: #b45309 !important;
}
.tp-hint {
  font-size: 10px;
  color: #aaa;
  text-align: center;
  margin-top: 8px;
}
</style>
