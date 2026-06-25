<template>
  <div class="olo-sb-root mb-bg-white mb-border-r mb-border-gray-200 mb-shrink-0">
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
        {{ t('Elementi') }}
      </button>
      <button
        class="sidebar-tab"
        :class="{ 'sidebar-tab--active': activeTab === 'structure' }"
        @click="activeTab = 'structure'"
      >
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M3 12h4l3-9 4 18 3-9h4"/>
        </svg>
        {{ t('Struttura') }}
      </button>
    </div>

    <!-- Insert-after banner -->
    <div v-if="builderStore.insertAfterTileId" class="tp-insert-banner">
      <span>⊕ {{ t('Seleziona un elemento da inserire') }}</span>
      <button class="tp-insert-cancel" @click="builderStore.insertAfterTileId = null">✕</button>
    </div>

    <!-- Tiles tab — V2 layout: 56px rail + 1fr panel -->
    <div v-if="activeTab === 'tiles'" class="olo-sb-body">
      <!-- Rail: vertical category buttons -->
      <div class="olo-sb-rail olo-sb-rail--cats" role="tablist" aria-label="Categorie elementi">
        <button
          v-for="cat in railCategories"
          :key="cat.id"
          class="olo-sb-rail-btn"
          :class="{ 'on': activeCategory === cat.id && !tileSearch.trim() }"
          role="tab"
          :aria-selected="activeCategory === cat.id"
          :title="cat.label"
          @click="onSelectCategory(cat.id)"
        >
          <span class="bar"></span>
          <span class="ic" v-html="catIcon(cat.id)"></span>
          <span class="lbl">{{ cat.label }}</span>
        </button>
      </div>

      <!-- Panel: header + search + grid -->
      <div class="olo-sb-panel">
        <div class="olo-sb-panel-head olo-sb-panel-head--cats">
          <h3 class="olo-sb-ph-title">
            <span class="t">{{ tileSearch.trim() ? t('Risultati') : activeCategoryLabel }}</span>
            <span class="olo-sb-ph-count">· {{ panelCount }}</span>
          </h3>
          <span class="olo-sb-ph-hint">{{ t('trascina nel canvas') }}</span>
        </div>

        <div class="olo-sb-search">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
          <input v-model="tileSearch" type="text" :placeholder="tileSearch ? '' : t('Cerca in') + ' ' + activeCategoryLabel + '…'" />
          <button v-if="tileSearch" class="ic-x" :title="t('Pulisci')" @click="tileSearch = ''">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
          </button>
        </div>

        <div class="olo-sb-grid">
          <!-- Global widgets cards (only when category is _global) -->
          <template v-if="activeCategory === '_global' && !tileSearch.trim()">
            <div
              v-for="gw in tilesStore.globalWidgets"
              :key="'gw-' + gw.id"
              class="olo-sb-card olo-sb-card--global"
              role="button"
              tabindex="0"
              v-olo-draggable="draggableGlobalWidgetOpts(gw.id)"
              @click="addGlobalWidget(gw.id)"
              @keydown.enter.prevent="addGlobalWidget(gw.id)"
              @keydown.space.prevent="addGlobalWidget(gw.id)"
              :title="gw.name"
            >
              <div class="ic" v-html="globeIcon"></div>
              <div class="lbl">{{ gw.name }}</div>
              <button class="del" :title="t('Elimina widget globale')" @click.stop="deleteGlobal(gw.id, gw.name)">×</button>
            </div>
            <div v-if="!tilesStore.globalWidgets.length" class="olo-sb-empty">
              {{ t('Nessun widget globale') }}
            </div>
          </template>

          <!-- Standard tile cards -->
          <template v-else>
            <div
              v-for="tile in displayedTiles"
              :key="(tile.cat || activeCategory) + '-' + tile.type"
              class="olo-sb-card"
              role="button"
              tabindex="0"
              v-olo-draggable="draggableTileOpts(tile.type)"
              @click="addTile(tile.type)"
              @keydown.enter.prevent="addTile(tile.type)"
              @keydown.space.prevent="addTile(tile.type)"
              :title="t(tile.name)"
            >
              <div class="ic" v-html="tileIcon(tile.type)"></div>
              <div class="lbl">{{ t(tile.name) }}</div>
              <div
                v-if="tileSearch.trim() && tile.cat"
                class="bdg"
                :style="{ background: catColor(tile.cat) + '22', color: catColor(tile.cat) }"
              >{{ categoryLabel(tile.cat) }}</div>
              <span
                v-if="!tileSearch.trim() && activeCategory !== '_recent' && activeCategory !== '_favorites'"
                class="fav"
                :class="{ 'on': isFavorite(tile.type) }"
                :title="isFavorite(tile.type) ? t('Rimuovi dai preferiti') : t('Aggiungi ai preferiti')"
                @click.stop="toggleFavorite(tile.type)"
              >★</span>
              <span class="grip" aria-hidden="true">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/><circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/><circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/></svg>
              </span>
            </div>
            <div v-if="!displayedTiles.length" class="olo-sb-empty">
              <template v-if="tileSearch.trim()">{{ t('Nessun elemento per') }} "<b>{{ tileSearch }}</b>"</template>
              <template v-else>{{ t('Nessun elemento in questa categoria') }}</template>
            </div>
          </template>
        </div>
      </div>
    </div>

    <!-- Structure tab — V2 layout: 56px rail + 1fr panel -->
    <div v-else class="olo-sb-body olo-sb-body--struct">
      <!-- Rail: zone filters -->
      <div class="olo-sb-rail" role="tablist" aria-label="Filtri vista struttura">
        <button
          v-for="f in structFilters"
          :key="f.id"
          class="olo-sb-rail-btn"
          :class="['tone-' + f.tone, { 'on': structFilter === f.id }]"
          role="tab"
          :aria-selected="structFilter === f.id"
          :title="f.label"
          @click="structFilter = f.id"
        >
          <span class="bar"></span>
          <span class="ic" v-html="f.icon"></span>
          <span class="lbl">{{ f.label }}</span>
          <span v-if="f.count !== null" class="cnt">{{ f.count }}</span>
        </button>
      </div>

      <!-- Panel: header + search + toolbar + tree + breadcrumb -->
      <div class="olo-sb-panel olo-sb-panel--struct">
        <div class="olo-sb-panel-head">
          <span class="dot" style="background: var(--olo-ui-accent)"></span>
          <h3>{{ t('Struttura pagina') }}</h3>
          <span class="cnt">{{ structureCount }}</span>
        </div>

        <div class="olo-sb-search">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
          <input v-model="structSearch" type="text" :placeholder="t('Cerca un blocco…')" />
          <button v-if="structSearch" class="ic-x" :title="t('Pulisci')" @click="structSearch = ''">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
          </button>
        </div>

        <div class="olo-sb-tools">
          <button type="button" :title="t('Comprimi tutto')" @click="structureRef?.collapseAll()">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="transform:rotate(-90deg)"><polyline points="6 9 12 15 18 9"/></svg>
            {{ t('Comprimi tutto') }}
          </button>
          <button type="button" :title="t('Espandi tutto')" @click="structureRef?.expandAll()">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            {{ t('Espandi tutto') }}
          </button>
          <span class="spc"></span>
          <button
            type="button"
            class="iso"
            :class="{ 'on': onlySelected }"
            :title="t('Mostra solo selezione')"
            @click="onlySelected = !onlySelected"
          >
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>

        <StructureTree
          ref="structureRef"
          :filter="structFilter"
          :search-query="structSearch"
          :only-selected="onlySelected"
          @save-as-template="section => $emit('save-as-template', section)"
        />

        <div v-if="selectedBreadcrumb.length" class="olo-sb-breadcrumb">
          <span class="lbl">{{ t('Selezionato') }}</span>
          <div class="path">
            <template v-for="(item, i) in selectedBreadcrumb" :key="i">
              <span v-if="i < selectedBreadcrumb.length - 1" class="seg" :title="item.label">{{ item.label }}</span>
              <span v-else class="cur">
                <span v-if="item.icon" class="ic" v-html="item.icon"></span>
                {{ item.label }}
              </span>
              <svg
                v-if="i < selectedBreadcrumb.length - 1"
                class="chev"
                width="9"
                height="9"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              ><polyline points="9 6 15 12 9 18"/></svg>
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useTilesStore } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';
import { useDnDStore } from '@/stores/dnd';
import { useDragDrop } from '@/composables/useDragDrop';
import { vOloDraggable, makeSidebarPayload, makeGlobalWidgetPayload, setCustomNativeDragPreview } from '@/composables/useDnD';
import { createSection, createRow, createColumn, generateId } from '@/stores/tiles';
import StructureTree from './StructureTree.vue';
import { t } from '@/i18n';

const tilesStore = useTilesStore();
const builderStore = useBuilderStore();
const dndStore = useDnDStore();
const { handleDropFromSidebar } = useDragDrop();

const tileSearch = ref('');
const searchResults = computed(() => {
  const q = tileSearch.value.trim().toLowerCase();
  if (!q) return [];
  const all = tilesStore.registeredTiles || [];
  return all
    .filter(t => (t.name || '').toLowerCase().includes(q) || (t.type || '').toLowerCase().includes(q))
    .map(t => ({ ...t, cat: t.category }));
});

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

// ── Favorites ──
const FAV_KEY = 'olo_favorite_tiles';
const favorites = ref(new Set(JSON.parse(localStorage.getItem(FAV_KEY) || '[]')));

function isFavorite(type) { return favorites.value.has(type); }

function toggleFavorite(type) {
  const s = new Set(favorites.value);
  if (s.has(type)) { s.delete(type); } else { s.add(type); }
  favorites.value = s;
  try { localStorage.setItem(FAV_KEY, JSON.stringify([...s])); } catch {}
}

const favoriteTilesList = computed(() => {
  const all = tilesStore.registeredTiles || [];
  return all.filter(t => favorites.value.has(t.type));
});

// ── Recent tiles ──
const RECENT_KEY = 'olo_recent_tiles';
const MAX_RECENT = 8;
const recentTypes = ref(JSON.parse(localStorage.getItem(RECENT_KEY) || '[]'));

function trackRecent(type) {
  const arr = recentTypes.value.filter(t => t !== type);
  arr.unshift(type);
  if (arr.length > MAX_RECENT) arr.length = MAX_RECENT;
  recentTypes.value = arr;
  try { localStorage.setItem(RECENT_KEY, JSON.stringify(arr)); } catch {}
}

const recentTilesList = computed(() => {
  const all = tilesStore.registeredTiles || [];
  const map = {};
  all.forEach(t => { map[t.type] = t; });
  return recentTypes.value.map(type => map[type]).filter(Boolean);
});

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
  essential: 'var(--olo-ui-accent, #e8622a)',
  layout: '#3B82F6',
  text: '#22C55E',
  media: '#A855F7',
  marketing: '#F59E0B',
  interactive: '#06B6D4',
  navigation: '#F43F5E',
  dynamic: '#F97316',
  booking: '#EAB308',
  'olo-space': '#14B8A6',
  atmosphere: '#38BDF8',
  woocommerce: '#7F54B3',
};

// Ordine fisso delle categorie nella sidebar
const categoryOrder = [
  'essential', 'layout', 'text', 'media', 'marketing',
  'interactive', 'atmosphere', 'navigation', 'dynamic', 'woocommerce', 'booking', 'olo-space',
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
  atmosphere: 'Atmosfera',
  woocommerce: 'WooCommerce',
};

function categoryLabel(category) {
  if (category === '_recent') return t('Recenti');
  if (category === '_favorites') return t('Preferiti');
  if (category === '_global') return t('Globali');
  return t(categoryLabels[category] || category);
}

function catColor(category) {
  if (category === '_recent') return '#8B5CF6';
  if (category === '_favorites') return '#EAB308';
  if (category === '_global') return '#D97706';
  return categoryColors[category] || '#6B7280';
}

// ── V2 Rail icons (per category) ──
const catIcons = {
  '_recent':    '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>',
  '_favorites': '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
  '_global':    '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
  'essential':  '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>',
  'layout':     '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>',
  'text':       '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7V4h16v3M9 20h6M12 4v16"/></svg>',
  'media':      '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>',
  'marketing':  '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l18-8v18L3 13z"/><path d="M11.6 16.8a3 3 0 11-5.8-1.6"/></svg>',
  'interactive':'<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.9 5.8H20l-4.9 3.6 1.9 5.8L12 14.6 7 18.2l1.9-5.8L4 8.8h6.1z"/></svg>',
  'navigation': '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h18M3 6h18M3 18h18"/></svg>',
  'dynamic':    '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/></svg>',
  'booking':    '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18M8 14l3 3 5-5"/></svg>',
  'olo-space':  '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/></svg>',
  'atmosphere': '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.4 4.1L17.5 8.5 13.4 9.9 12 14l-1.4-4.1L6.5 8.5l4.1-1.4z"/><path d="M5 14l.8 2.2L8 17l-2.2.8L5 20l-.8-2.2L2 17l2.2-.8z"/><path d="M18.5 13l.6 1.6 1.6.6-1.6.6-.6 1.6-.6-1.6-1.6-.6 1.6-.6z"/></svg>',
  'woocommerce': '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>',
};

function catIcon(category) {
  return catIcons[category] || catIcons['essential'];
}

const globeIcon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>';

// ── V2 active category state ──
const ACTIVE_CAT_KEY = 'olo_sb_active_cat';
const activeCategory = ref(localStorage.getItem(ACTIVE_CAT_KEY) || 'essential');

function onSelectCategory(catId) {
  activeCategory.value = catId;
  tileSearch.value = '';
  try { localStorage.setItem(ACTIVE_CAT_KEY, catId); } catch {}
}

// Rail categories: built dynamically from current tile inventory
const railCategories = computed(() => {
  const out = [];
  if (recentTilesList.value.length) {
    out.push({ id: '_recent', label: t('Recenti'), count: recentTilesList.value.length });
  }
  if (favoriteTilesList.value.length) {
    out.push({ id: '_favorites', label: t('Preferiti'), count: favoriteTilesList.value.length });
  }
  for (const cid of categoryOrder) {
    const tiles = (tilesByCategory.value || {})[cid] || [];
    if (!tiles.length) continue;
    out.push({ id: cid, label: t(categoryLabels[cid] || cid), count: tiles.length });
  }
  if ((tilesStore.globalWidgets || []).length) {
    out.push({ id: '_global', label: t('Globali'), count: tilesStore.globalWidgets.length });
  }
  return out;
});

// Tiles to display in panel grid (category-scoped or search-scoped)
const displayedTiles = computed(() => {
  if (tileSearch.value.trim()) return searchResults.value;
  if (activeCategory.value === '_recent') return recentTilesList.value;
  if (activeCategory.value === '_favorites') return favoriteTilesList.value;
  if (activeCategory.value === '_global') return [];
  return (tilesByCategory.value || {})[activeCategory.value] || [];
});

const activeCategoryLabel = computed(() => categoryLabel(activeCategory.value));

const panelCount = computed(() => {
  if (tileSearch.value.trim()) return searchResults.value.length;
  if (activeCategory.value === '_global') return (tilesStore.globalWidgets || []).length;
  return displayedTiles.value.length;
});

// ── Structure tab: zone filters ──
const STRUCT_FILTER_KEY = 'olo_sb_struct_filter';
const structFilter = ref(localStorage.getItem(STRUCT_FILTER_KEY) || 'all');
import { watch } from 'vue';
watch(structFilter, v => { try { localStorage.setItem(STRUCT_FILTER_KEY, v); } catch {} });

// V2: live search across nodes + isolation toggle ("show only selected path")
const structSearch = ref('');
const onlySelected = ref(false);
const structureRef = ref(null);

// Breadcrumb path of the currently selected tile
function _findNodePath(nodes, targetId, chain = []) {
  for (const node of (nodes || [])) {
    const newChain = [...chain, node];
    if (node.id === targetId) return newChain;
    if (node.children?.length) {
      const r = _findNodePath(node.children, targetId, newChain);
      if (r) return r;
    }
  }
  return null;
}

const _typeLabels = {
  section: 'Sezione', row: 'Row', column: 'Column', 'inner-columns': 'Colonne', 'inner-column': 'Colonna',
};

const selectedBreadcrumb = computed(() => {
  const id = builderStore.selectedTileId;
  if (!id) return [];
  let path = _findNodePath(tilesStore.headerTiles || [], id);
  let zone = t('Header');
  if (!path) { path = _findNodePath(tilesStore.canvasTiles || [], id); zone = t('Body'); }
  if (!path) { path = _findNodePath(tilesStore.footerTiles || [], id); zone = t('Footer'); }
  if (!path) return [];
  const out = [{ label: zone, icon: '' }];
  for (const node of path) {
    const lbl = node.settings?._label || _typeLabels[node.type] || node.type;
    const ic = (node === path[path.length - 1]) ? (tileIcons[node.type] || '') : '';
    out.push({ label: lbl, icon: ic });
  }
  return out;
});

// Recursive count of leaf elements in a tree
function _countTreeLeaves(nodes) {
  if (!Array.isArray(nodes)) return 0;
  let n = 0;
  for (const node of nodes) {
    const kids = node.children || [];
    if (kids.length === 0) { n += 1; }
    else { n += _countTreeLeaves(kids); }
  }
  return n;
}

const headerCount = computed(() => _countTreeLeaves(tilesStore.headerTiles || []));
const bodyCount   = computed(() => _countTreeLeaves(tilesStore.canvasTiles  || []));
const footerCount = computed(() => _countTreeLeaves(tilesStore.footerTiles  || []));

const structureCount = computed(() => {
  const total = headerCount.value + bodyCount.value + footerCount.value;
  return total === 1 ? `1 ${t('elemento')}` : `${total} ${t('elementi')}`;
});

const structFilters = computed(() => [
  { id: 'all',    icon: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l9 4.5v11L12 22l-9-4.5v-11z"/><path d="M3 6.5L12 11l9-4.5M12 11v11"/></svg>',
    label: t('Tutto'), tone: 'neutral', count: headerCount.value + bodyCount.value + footerCount.value },
  { id: 'header', icon: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="6" rx="2"/><line x1="3" y1="13" x2="21" y2="13" opacity=".4"/><line x1="3" y1="17" x2="21" y2="17" opacity=".4"/></svg>',
    label: t('Header'), tone: 'info', count: headerCount.value },
  { id: 'body',   icon: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/></svg>',
    label: t('Body'), tone: 'brand', count: bodyCount.value },
  { id: 'footer', icon: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="7" x2="21" y2="7" opacity=".4"/><line x1="3" y1="11" x2="21" y2="11" opacity=".4"/><rect x="3" y="15" width="18" height="6" rx="2"/></svg>',
    label: t('Footer'), tone: 'success', count: footerCount.value },
]);

// ── SVG icon map per tile type ──
// ── Registry icone tile (2 di 3) ──────────────────────────────────────────
// Set 24×24 in formato shorthand (w=/h=/vb=/f=/s=/sw=) espanso da tileIcon().
// Gli altri due set (InsertPanel.moduleIcon 24×24 espanso, StructureTree.nodeIcon
// 14×14) divergono per formato/dimensione: NON unificare. Allinea le CHIAVI.
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
  'sharebuttons': '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="6" cy="12" r="2.6"/><circle cx="17" cy="6" r="2.6"/><circle cx="17" cy="18" r="2.6"/><path d="M8.3 10.8l6.4-3.6M8.3 13.2l6.4 3.6"/><path d="M20 4l1.5-1.5M21.5 2.5h-2M21.5 2.5v2"/></svg>',
  'progress':     '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="10" w="20" h="4" rx="2"/><rect x="2" y="10" w="12" h="4" rx="2" fill="currentColor" opacity="0.3"/></svg>',
  'starrating':   '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><polygon points="6.5 3.4 7.7 6 10.5 6.3 8.4 8.1 9 10.9 6.5 9.4 4 10.9 4.6 8.1 2.5 6.3 5.3 6"/><path d="M13 6.5h7M13 10h5"/></svg>',
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
  'osmmap':       '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M12 21s-6.5-5.8-6.5-11A6.5 6.5 0 0112 3.5 6.5 6.5 0 0118.5 10c0 5.2-6.5 11-6.5 11z"/><circle cx="12" cy="10" r="2.3"/></svg>',
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

  // ── Real Estate tiles ──
  'propertygrid':       '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="8" h="8" rx="1"/><rect x="13" y="3" w="8" h="8" rx="1"/><path d="M4.5 7l2.5-2 2.5 2M14.5 7l2.5-2 2.5 2"/><rect x="3" y="14" w="18" h="6" rx="1"/><path d="M6 17h4M13 17h5"/></svg>',
  'propertysearch':     '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="10" cy="10" r="7"/><path d="M21 21l-4.35-4.35"/><path d="M7 8l3 2 3-4"/></svg>',
  'propertymap':        '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><circle cx="12" cy="11" r="2"/></svg>',
  'propertymapsearch':  '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="1" y="3" w="10" h="18" rx="1"/><path d="M14 6h7M14 10h7M14 14h5"/><circle cx="6" cy="10" r="2"/></svg>',
  'propertycard':       '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="18" rx="2"/><path d="M3 12h18"/><path d="M8 16h4"/><path d="M8 19h8"/></svg>',
  'propertyfeatured':   '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 16.8l-6.2 4.5 2.4-7.4L2 9.4h7.6z"/></svg>',
  'propertystats':      '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="4" y="14" w="4" h="8" rx="1"/><rect x="10" y="8" w="4" h="14" rx="1"/><rect x="16" y="4" w="4" h="18" rx="1"/></svg>',
  'propertycta':        '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a8.38 8.38 0 0 1 13 0"/><rect x="14" y="17" w="8" h="4" rx="2"/></svg>',
  'propertyhero':       '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="4" w="20" h="16" rx="2"/><path d="M2 14l5-5 4 4 3-3 8 8"/><circle cx="8" cy="9" r="1.5"/></svg>',
  'propertyheroscroll': '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="1" y="6" w="8" h="12" rx="1"/><rect x="11" y="6" w="8" h="12" rx="1"/><path d="M21 12h2M1 12H0"/><path d="M19 9l3 3-3 3"/></svg>',
  'propertyinfo':       '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="M9 13l2 2 4-4"/><path d="M9 18h6"/></svg>',
  'propertygallery':    '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="2" w="9" h="9" rx="1"/><rect x="13" y="2" w="9" h="5" rx="1"/><rect x="13" y="9" w="9" h="5" rx="1"/><rect x="2" y="13" w="9" h="9" rx="1"/><rect x="13" y="16" w="9" h="6" rx="1"/></svg>',
  'propertyprice':      '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
  'propertyspecs':      '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="7" h="7" rx="1.5"/><rect x="14" y="3" w="7" h="7" rx="1.5"/><rect x="3" y="14" w="7" h="7" rx="1.5"/><rect x="14" y="14" w="7" h="7" rx="1.5"/><circle cx="6.5" cy="6.5" r="1"/><circle cx="17.5" cy="6.5" r="1"/><circle cx="6.5" cy="17.5" r="1"/><circle cx="17.5" cy="17.5" r="1"/></svg>',
  'propertydescription':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 6h16M4 10h16M4 14h12M4 18h8"/></svg>',
  'propertyaddress':    '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>',
  'propertyfeatures':   '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><polyline points="9 6 9 6"/><path d="M4 6h2m4 0h10"/><polyline points="9 12 9 12"/><path d="M4 12h2m4 0h10"/><polyline points="9 18 9 18"/><path d="M4 18h2m4 0h6"/><circle cx="9" cy="6" r="1.5" f="currentColor"/><circle cx="9" cy="12" r="1.5" f="currentColor"/><circle cx="9" cy="18" r="1.5" f="currentColor"/></svg>',
  'propertyvideo':      '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="4" w="20" h="16" rx="2"/><polygon points="10 8 16 12 10 16 10 8" f="currentColor" sw="0"/></svg>',
  'propertyexcerpt':    '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 6h16M4 10h16M4 14h10"/><path d="M3 20l3-3 3 3"/></svg>',
  'propertyrules':      '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>',
  'propertycontactform':'<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="4" w="20" h="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/><path d="M8 14h3M8 17h5"/></svg>',
  // ── Accommodation (olo-booking) ──
  'ac-hero':                  '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="3" w="20" h="18" rx="2"/><path d="M2 16l5-6 4 4 4-5 7 8"/><circle cx="8" cy="8" r="1.8"/></svg>',
  'ac-card':                  '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="18" rx="2"/><path d="M3 12h18"/><path d="M7 16h10M7 19h6"/></svg>',
  'ac-grid':                  '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="8" h="8" rx="1"/><rect x="13" y="3" w="8" h="8" rx="1"/><rect x="3" y="13" w="8" h="8" rx="1"/><rect x="13" y="13" w="8" h="8" rx="1"/></svg>',
  'ac-related':               '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="5" w="6" h="8" rx="1"/><rect x="9" y="5" w="6" h="8" rx="1"/><rect x="16" y="5" w="6" h="8" rx="1"/><path d="M2 17h20"/></svg>',
  'ac-booking-form':          '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="4" w="18" h="14" rx="2"/><path d="M3 9h18M8 4v3M16 4v3M7 13h10M7 16h6"/></svg>',
  'ac-availability-calendar': '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="4" w="18" h="18" rx="2"/><path d="M8 2v4M16 2v4M3 10h18"/><path d="M8 14l3 3 5-5"/></svg>',
  'ac-gallery':               '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="2" w="9" h="9" rx="1"/><rect x="13" y="2" w="9" h="5" rx="1"/><rect x="13" y="9" w="9" h="5" rx="1"/><rect x="2" y="13" w="9" h="9" rx="1"/><rect x="13" y="16" w="9" h="6" rx="1"/></svg>',
  'ac-hero-video':            '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="3" w="20" h="18" rx="2"/><circle cx="12" cy="12" r="4"/><polygon points="11 10 15 12 11 14" f="currentColor"/></svg>',
  'ac-video':                 '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="4" w="20" h="16" rx="2"/><polygon points="10 8 16 12 10 16" f="currentColor"/></svg>',
  'ac-pricing-seasons':       '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="18" rx="2"/><path d="M3 9h18M9 9v12M15 9v12"/><path d="M5 6h4M11 6h2M17 6h2"/></svg>',
  'ac-reviews':               '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><polygon points="12 2 15 9 22 10 17 14 18 21 12 17 6 21 7 14 2 10 9 9"/></svg>',
  'ac-host-info':             '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="18" rx="2"/><circle cx="9" cy="10" r="3"/><path d="M5 18c0-2 1.5-3 4-3s4 1 4 3"/><path d="M14 8h5M14 12h5"/></svg>',
  'ac-stats':                 '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="14" w="4" h="7" rx="1"/><rect x="10" y="8" w="4" h="13" rx="1"/><rect x="17" y="3" w="4" h="18" rx="1"/></svg>',
  'ac-description':           '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 5h16M4 9h12M4 13h16M4 17h8"/></svg>',
  'ac-amenities':             '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M3.5 5l1.5 1.5 3-3M3.5 11l1.5 1.5 3-3M3.5 17l1.5 1.5 3-3"/><path d="M11 6h10M11 12h8M11 18h10"/></svg>',
  'ac-map':                   '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><circle cx="12" cy="11" r="2"/></svg>',
  'ac-features':              '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="7" h="7" rx="1.5"/><rect x="14" y="3" w="7" h="7" rx="1.5"/><rect x="3" y="14" w="7" h="7" rx="1.5"/><rect x="14" y="14" w="7" h="7" rx="1.5"/><circle cx="6.5" cy="6.5" r="1"/><circle cx="17.5" cy="6.5" r="1"/><circle cx="6.5" cy="17.5" r="1"/><circle cx="17.5" cy="17.5" r="1"/></svg>',
  'ac-faq':                   '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="4" w="18" h="4" rx="1"/><rect x="3" y="10" w="18" h="4" rx="1"/><rect x="3" y="16" w="18" h="4" rx="1"/><path d="M17 6l2-2M17 12l2-2M17 18l2-2" sw="1.2"/></svg>',
  'ac-address':               '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M12 22s7-7 7-13a7 7 0 1 0-14 0c0 6 7 13 7 13z"/><circle cx="12" cy="9" r="2.5"/></svg>',
  'ac-rules':                 '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>',
  'ac-search':                '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="10" cy="10" r="7"/><line x1="15" y1="15" x2="21" y2="21" sw="2"/></svg>',
  'ac-breadcrumb-hero':       '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="3" w="20" h="18" rx="2"/><path d="M5 7h2M9 7h3M14 7h2"/><path d="M6 16h12"/></svg>',
  'ac-cta':                   '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="6" w="20" h="12" rx="2"/><path d="M5 10h6M5 13h4"/><rect x="14" y="10" w="6" h="4" rx="1.2"/></svg>',
  'ac-certifications':        '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="12" cy="9" r="6"/><path d="M9 14l-3 7 6-3 6 3-3-7"/><circle cx="12" cy="9" r="3" stroke-opacity=".4"/></svg>',
  'ac-contact-form':          '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="4" w="20" h="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/><path d="M8 14h3M8 17h5"/></svg>',

  // ── Icone tile (design handoff): 62 tile che ricadevano sul placeholder ──
  'announcementbar':     '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="4" w="20" h="6" rx="1.5"/><path d="M6 7h9"/><path d="M3 14h18M3 18h12"/></svg>',
  'audiohero':           '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="3" w="20" h="18" rx="2"/><path d="M6 14v-3M9 15v-5M12 16.5v-9M15 15v-5M18 14v-3"/></svg>',
  'buildermock':         '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="3" w="20" h="18" rx="2"/><path d="M2 7h20M8 7v14"/><rect x="3.8" y="9.4" w="2.6" h="2.2" rx=".4"/><path d="M10 11h8M10 14h6M10 17h7"/></svg>',
  'chathero':            '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="3" w="20" h="18" rx="2"/><path d="M6 8h12M6 10.5h7"/><path d="M6 14h10v4l-3-2H6z"/></svg>',
  'featuredstory':       '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="4" w="20" h="16" rx="2"/><rect x="4" y="7" w="7" h="10" rx="1"/><path d="M14 8h5M14 11h5M14 14h3"/></svg>',
  'glowgallery':         '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="12" cy="7" r="3.5" opacity=".4"/><path d="M7 5h10"/><rect x="3" y="13" w="5" h="8" rx="1"/><rect x="9.5" y="13" w="5" h="8" rx="1"/><rect x="16" y="13" w="5" h="8" rx="1"/></svg>',
  'glowhero':            '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="12" cy="9" r="6" opacity=".35"/><path d="M5 15h14M8 19h8"/></svg>',
  'imagehero':           '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="3" w="20" h="18" rx="2"/><circle cx="7" cy="8" r="1.6"/><path d="M3 16l5-4 4 3 4-4 5 5"/><path d="M6 19h9"/></svg>',
  'introsplit':          '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M3 5h8M3 9h8M3 13h5"/><path d="M3 18h3M9 18h2"/><rect x="14" y="5" w="7" h="9" rx="1"/><circle cx="18" cy="17.5" r="3"/></svg>',
  'maskedvideohero':     '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M2 3h20v11c0 5-5 5-10 5S2 19 2 14z"/><polygon points="10 8 15 11 10 14" f="currentColor" sw="0"/></svg>',
  'masthead':            '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="4" w="20" h="4" rx="1"/><path d="M2 11h20M2 14h13M2 17h20M2 20h9"/></svg>',
  'mediacta':            '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="3" w="20" h="13" rx="2"/><polygon points="10 7 15 9.5 10 12" f="currentColor" sw="0"/><rect x="6" y="19" w="12" h="3" rx="1.5"/></svg>',
  'newsletter':          '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="4" w="20" h="10" rx="2"/><path d="M2 6l10 5 10-5"/><rect x="4" y="17" w="10" h="3.5" rx="1.5"/><rect x="16" y="17" w="4" h="3.5" rx="1"/></svg>',
  'photocover':          '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="2" w="20" h="20" rx="1.5"/><rect x="5" y="5" w="14" h="14"/><path d="M7 16h7"/></svg>',
  'producthero':         '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="4" w="20" h="16" rx="2"/><path d="M2 9h20"/><circle cx="5" cy="6.5" r=".7" f="currentColor" sw="0"/><circle cx="7.5" cy="6.5" r=".7" f="currentColor" sw="0"/><path d="M8 14h8M10 17h4"/></svg>',
  'searchhero':          '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 5h16"/><rect x="3" y="9" w="13" h="4.5" rx="2.25"/><circle cx="19" cy="11.25" r="2.6"/><path d="M5 18h4M11 18h5M18 18h2"/></svg>',
  'smearhero':           '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="3" w="20" h="18" rx="2"/><path d="M5 12c3-5 6 3 9-1s4-2 5-2"/><path d="M7 17h9"/></svg>',
  'availability':        '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="4" y="4" w="4" h="4" rx="1"/><rect x="10" y="4" w="4" h="4" rx="1" f="currentColor" opacity=".22" sw="0"/><rect x="16" y="4" w="4" h="4" rx="1"/><rect x="4" y="10" w="4" h="4" rx="1" f="currentColor" opacity=".22" sw="0"/><rect x="10" y="10" w="4" h="4" rx="1"/><rect x="16" y="10" w="4" h="4" rx="1"/><path d="M5 19h14"/></svg>',
  'builder':             '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 7h7M15 7h5M17.5 4.5v5"/><path d="M4 12h7M15 12h5"/><path d="M4 17h16"/></svg>',
  'finder':              '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="4" w="5" h="3" rx="1.5"/><rect x="10" y="4" w="5" h="3" rx="1.5"/><rect x="17" y="4" w="4" h="3" rx="1.5"/><rect x="5" y="11" w="14" h="9" rx="2"/></svg>',
  'hiddenpop':           '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="4" y="6" w="16" h="12" rx="2" stroke-dasharray="3 2.5"/><circle cx="12" cy="12" r="2.2"/></svg>',
  'hotspots':            '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="18" rx="2"/><circle cx="8.5" cy="9" r="1.4" f="currentColor" sw="0"/><circle cx="15" cy="14" r="1.4" f="currentColor" sw="0"/><circle cx="15" cy="14" r="4"/></svg>',
  'icontabs':            '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="18" rx="2"/><path d="M3 9h18"/><circle cx="7" cy="6" r="1.2"/><circle cx="12" cy="6" r="1.2"/><circle cx="17" cy="6" r="1.2"/></svg>',
  'mixer':               '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="9" cy="10" r="5"/><circle cx="15" cy="10" r="5"/><path d="M6 19h12"/></svg>',
  'physicsbin':          '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 5v13a2 2 0 002 2h12a2 2 0 002-2V5"/><circle cx="9" cy="15" r="2.6"/><rect x="13" y="12.5" w="5" h="5" rx="1" transform="rotate(18 15.5 15)"/></svg>',
  'projector':           '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M3 8h18"/><circle cx="9" cy="8" r="2.4"/><path d="M5 14h14M5 18h8"/></svg>',
  'revealbox':           '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="18" rx="2"/><path d="M3 13h18" stroke-dasharray="3 2"/><path d="M9 9l3-3 3 3"/></svg>',
  'scaler':              '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="7" h="5" rx="1"/><path d="M14 5.5h7M5 13h16M5 17h12M5 21h8"/></svg>',
  'scratchfx':           '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="18" rx="2"/><path d="M6 13c2-3 4 2 6-1s3-2 4-3"/><path d="M14 6l4 4"/></svg>',
  'timezone':            '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="6" cy="7" r="3"/><path d="M6 5.5V7l1.2 .8"/><path d="M12 6h9M12 11h9M12 16h9"/><circle cx="9" cy="11" r="1.3" f="currentColor" sw="0"/><circle cx="16" cy="16" r="1.3" f="currentColor" sw="0"/></svg>',
  'tripfinder':          '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="8" w="20" h="8" rx="2"/><path d="M8 8v8M14 8v8"/><path d="M16 12h4"/></svg>',
  'presencegrid':        '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="4" w="7" h="7" rx="1.5"/><rect x="14" y="4" w="7" h="7" rx="1.5"/><rect x="3" y="14" w="7" h="6" rx="1.5"/><rect x="14" y="14" w="7" h="6" rx="1.5"/><circle cx="9.5" cy="5.5" r="1.3" f="currentColor" sw="0"/></svg>',
  'matchfixtures':       '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="7" cy="12" r="3"/><circle cx="17" cy="12" r="3"/><path d="M11.5 12h1M12 10.5v3"/></svg>',
  'asciiviz':            '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="4" w="18" h="16" rx="2"/><path d="M6 9h2.5M11 9h3.5M17 9h1.5M6 13h4M12.5 13h2M6 17h6.5"/></svg>',
  'beforeafter':         '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="5" w="8" h="11" rx="1"/><rect x="13" y="5" w="8" h="11" rx="1"/><path d="M4 19h6M14 19h6"/></svg>',
  'categoryrail':        '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="5" w="7" h="13" rx="1.5"/><rect x="11" y="5" w="7" h="13" rx="1.5"/><rect x="20" y="5" w="3" h="13" rx="1.5"/><path d="M8 21h8"/></svg>',
  'productgrid':         '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="8" h="11" rx="1"/><rect x="13" y="3" w="8" h="11" rx="1"/><path d="M3 16.5h6M13 16.5h6M3 19.5h4M13 19.5h4"/></svg>',
  'showcasegrid':        '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="18" h="18" rx="2"/><circle cx="17" cy="7" r="2.6"/><path d="M16 8l2-2M16.4 6h1.6v1.6"/><path d="M6 17h8"/></svg>',
  'svganimator':         '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M3 12h4l2.5-6 3 12 2.5-6H21"/><circle cx="3" cy="12" r="1.4" f="currentColor" sw="0"/><circle cx="21" cy="12" r="1.4" f="currentColor" sw="0"/></svg>',
  'viewer360':           '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="12" cy="12" r="9"/><path d="M3 12c0-2.2 4-4 9-4s9 1.8 9 4-4 4-9 4-9-1.8-9-4z"/><path d="M13 3.6l2.4 1.5-2.4 1.6"/></svg>',
  'cta-banner':          '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="6" w="20" h="12" rx="2"/><path d="M5 10h6M5 13h4"/><rect x="14" y="10" w="5.5" h="4" rx="1"/></svg>',
  'hero-split':          '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="4" w="20" h="16" rx="2"/><path d="M12 4v16"/><path d="M4 9h5M4 12h5"/></svg>',
  'hoursstrip':          '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="5" cy="12" r="2.6"/><path d="M5 10.5V12l1.1 .7"/><path d="M10 9h11M10 13h8M10 17h10"/></svg>',
  'hoverlist':           '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="5" cy="7" r="2" f="currentColor" sw="0"/><path d="M9 7h12"/><circle cx="5" cy="12.5" r="2"/><path d="M9 12.5h9"/><circle cx="5" cy="18" r="2"/><path d="M9 18h11"/></svg>',
  'info-cards':          '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="4" w="8" h="8" rx="1.5"/><rect x="13" y="4" w="8" h="8" rx="1.5"/><path d="M5 15h6M15 15h4M5 18h4M15 18h5"/></svg>',
  'lookbookmixer':       '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="5" w="5" h="12" rx="1"/><rect x="10" y="5" w="5" h="12" rx="1"/><rect x="17" y="5" w="4" h="12" rx="1"/><path d="M5.5 3.6l-1 1.4h2zM6 20h12"/></svg>',
  'process-steps':       '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="6" cy="9" r="3"/><circle cx="18" cy="9" r="3"/><path d="M9 9h6"/><path d="M3 16h6M15 16h6"/></svg>',
  'product-cards':       '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="8" h="18" rx="1.5"/><rect x="13" y="3" w="8" h="18" rx="1.5"/><path d="M3 13h8M13 13h8"/><path d="M5 16h3M15 16h3"/></svg>',
  'schedule':            '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="4" w="18" h="16" rx="1.5"/><path d="M3 8h18M9 4v16M15 4v16"/><rect x="9.5" y="8.6" w="5" h="3" rx=".5" f="currentColor" opacity=".22" sw="0"/></svg>',
  'scrollscrub':         '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="6" w="5.5" h="9" rx="1"/><rect x="9.25" y="6" w="5.5" h="9" rx="1"/><rect x="16.5" y="6" w="5.5" h="9" rx="1"/><path d="M4 19h13M15 17l2 2-2 2"/></svg>',
  'section-header':      '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M3 5h6"/><path d="M3 9.5h15M3 13h10"/><path d="M16 19h5"/></svg>',
  'stackscroll':         '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="8" y="3" w="13" h="6" rx="1.5"/><rect x="6" y="8.5" w="13" h="6" rx="1.5"/><rect x="4" y="14" w="13" h="6" rx="1.5"/></svg>',
  'statstrip':           '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M3 8h6" sw="2.2"/><path d="M3 12h4"/><path d="M11 4v16"/><path d="M15 8h6" sw="2.2"/><path d="M15 12h4"/></svg>',
  'step-timeline':       '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M3 10h18" stroke-dasharray="3 2"/><circle cx="6" cy="10" r="2.2"/><circle cx="12" cy="10" r="2.2"/><circle cx="18" cy="10" r="2.2"/><path d="M4 16h4M10 16h4M16 16h4"/></svg>',
  'trust-strip':         '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="6" cy="12" r="3"/><path d="M4.8 12l1 1 1.4-1.7"/><path d="M11 12h2"/><circle cx="18" cy="12" r="3"/><path d="M16.8 12l1 1 1.4-1.7"/></svg>',
  'workgrid':            '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="3" w="8" h="8" rx="1"/><rect x="13" y="3" w="8" h="8" rx="1"/><rect x="3" y="13" w="8" h="8" rx="1"/><circle cx="16.5" cy="16.5" r="2.5"/><path d="M18.3 18.3l1.7 1.7"/></svg>',
  'worklist':            '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M3 6h2M8 6h13M3 12h2M8 12h11M3 18h2M8 18h12"/></svg>',
  'goo':                 '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="9" cy="10" r="4.5"/><circle cx="15" cy="13.5" r="3.5"/><circle cx="14" cy="8" r="2"/></svg>',
  'particlefx':          '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><circle cx="6" cy="7" r="1.1" f="currentColor" sw="0"/><circle cx="12" cy="5" r="1.1" f="currentColor" sw="0"/><circle cx="18" cy="8" r="1.1" f="currentColor" sw="0"/><circle cx="8" cy="13" r="1.1" f="currentColor" sw="0"/><circle cx="15" cy="12" r="1.1" f="currentColor" sw="0"/><circle cx="11" cy="18" r="1.1" f="currentColor" sw="0"/><circle cx="19" cy="17" r="1.1" f="currentColor" sw="0"/></svg>',
  'badge':               '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="8" w="13" h="8" rx="4"/><circle cx="19.5" cy="12" r="2.2"/></svg>',
  'variablespecimen':    '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M4 19l5-13 5 13M6 14h6"/><path d="M16 11h5M18.5 9.5v3"/></svg>',
  'leaderboard':         '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="6" w="13" h="3" rx="1.5"/><rect x="3" y="11" w="10" h="3" rx="1.5"/><rect x="3" y="16" w="7" h="3" rx="1.5"/><polygon points="20 5 20.7 6.5 22.3 6.7 21.1 7.8 21.4 9.4 20 8.6 18.6 9.4 18.9 7.8 17.7 6.7 19.3 6.5" f="currentColor" sw="0"/></svg>',

  // ── Tile fuori dal pacchetto handoff (North dormienti + clod-evoluzione) ──
  'northvideohero':      '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="3" w="20" h="18" rx="2"/><polygon points="10 8 16 12 10 16" f="currentColor" sw="0"/><path d="M5 6l3 0-1 2H4z"/></svg>',
  'northquoteslider':    '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M5 6h4v4c0 2-1.5 3-3.5 3.5"/><path d="M13 6h4v4c0 2-1.5 3-3.5 3.5"/><path d="M4 19h13M15 17l2 2-2 2"/></svg>',
  'studiohero':          '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="2" y="3" w="20" h="18" rx="2"/><circle cx="12" cy="10" r="3.5"/><path d="M12 3v3M6 15h12"/></svg>',
  'filmreel':            '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="5" w="18" h="14" rx="2"/><path d="M3 9h18M3 15h18"/><path d="M7 5v14M17 5v14"/></svg>',
  'scrubtext':           '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M5 6h14M12 6v9"/><path d="M4 20h16M15 18l2 2-2 2"/></svg>',
  'themedemos':          '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><rect x="3" y="4" w="8" h="7" rx="1"/><rect x="13" y="4" w="8" h="7" rx="1"/><path d="M3 6.5h8M13 6.5h8"/><rect x="3" y="14" w="18" h="6" rx="1"/><path d="M3 16.5h18"/></svg>',
  'evonotes':            '<svg w="14" h="14" vb="0 0 24 24" f="none" s="cC" sw="1.5"><path d="M14 3H6a2 2 0 00-2 2v14a2 2 0 002 2h12a2 2 0 002-2V9z"/><path d="M14 3v6h6"/><path d="M8 13h7M8 17h5"/></svg>',
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

/**
 * Factory per le opzioni v-olo-draggable di un tile-type.
 * Richiede un layout snapshot aggiornato dall'iframe prima dell'hit-test.
 */
/**
 * Crea un elemento ghost custom per il drag — card con icona + label del tile,
 * stile coerente con la sidebar ma più spiccato (ombra, rotazione).
 */
function makeTileGhost(iconHtml, labelText, catColor) {
  const ghost = document.createElement('div');
  ghost.className = 'olo-dnd-tile-preview';
  ghost.style.cssText = [
    'display:inline-flex',
    'align-items:center',
    'gap:8px',
    'padding:8px 14px',
    'border-radius:10px',
    'background:#fff',
    'border:2px solid ' + (catColor || '#e8622a'),
    'box-shadow:0 10px 24px rgba(0,0,0,0.18),0 2px 6px rgba(0,0,0,0.1)',
    'font-family:inherit',
    'font-size:11px',
    'font-weight:600',
    'color:#1F2937',
    'transform:rotate(-2deg)',
    'white-space:nowrap',
    'pointer-events:none',
  ].join(';');
  ghost.innerHTML = `
    <span style="display:inline-flex;align-items:center;color:${catColor || '#e8622a'}">${iconHtml}</span>
    <span>${labelText}</span>
  `;
  return ghost;
}

function draggableTileOpts(tileType) {
  return {
    getInitialData: () => makeSidebarPayload(tileType),
    onGenerateDragPreview: ({ nativeSetDragImage, source }) => {
      setCustomNativeDragPreview({
        getOffset: () => ({ x: 16, y: 16 }),
        render: ({ container }) => {
          const srcEl = source.element;
          const iconEl = srcEl.querySelector('.tp-btn-icon');
          const labelEl = srcEl.querySelector('.tp-btn-label');
          const catColor = getComputedStyle(srcEl).getPropertyValue('--cat-color').trim() || '#e8622a';
          const ghost = makeTileGhost(
            iconEl?.innerHTML || '',
            labelEl?.textContent || tileType,
            catColor
          );
          container.appendChild(ghost);
          return () => { try { container.removeChild(ghost); } catch (e) {} };
        },
        nativeSetDragImage,
      });
    },
    onDragStart: () => {
      dndStore.startDrag(makeSidebarPayload(tileType));
      // Richiedi layout snapshot fresh dall'iframe
      const iframe = document.querySelector('.olo-live-iframe');
      if (iframe && iframe.contentWindow) {
        iframe.contentWindow.postMessage({ type: 'olo:request-layout' }, '*');
      }
    },
    onDrop: () => {
      if (!dndStore.isIdle) dndStore.endDrag();
    },
  };
}

/**
 * Factory per le opzioni v-olo-draggable di un global widget.
 */
function draggableGlobalWidgetOpts(globalId) {
  return {
    getInitialData: () => makeGlobalWidgetPayload(globalId),
    onGenerateDragPreview: ({ nativeSetDragImage, source }) => {
      setCustomNativeDragPreview({
        getOffset: () => ({ x: 16, y: 16 }),
        render: ({ container }) => {
          const srcEl = source.element;
          const labelEl = srcEl.querySelector('.tp-btn-label');
          const ghost = makeTileGhost(
            '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
            labelEl?.textContent || 'Widget',
            '#D97706'
          );
          container.appendChild(ghost);
          return () => { try { container.removeChild(ghost); } catch (e) {} };
        },
        nativeSetDragImage,
      });
    },
    onDragStart: () => {
      dndStore.startDrag(makeGlobalWidgetPayload(globalId));
      const iframe = document.querySelector('.olo-live-iframe');
      if (iframe && iframe.contentWindow) {
        iframe.contentWindow.postMessage({ type: 'olo:request-layout' }, '*');
      }
    },
    onDrop: () => {
      if (!dndStore.isIdle) dndStore.endDrag();
    },
  };
}

function addTile(tileType) {
  trackRecent(tileType);
  // Pragmatic sopprime il click dopo un drag: nessun flag temporale necessario.
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

function addGlobalWidget(globalId) {
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
/* ── Root container ──────────────────────────────────────── */
.olo-sb-root {
  /* Accento CHROME del builder = arancio fisso #e8622a (identità prodotto). Definito qui in
     locale, indipendente da --olo-color-primary (che AIAssistant rimappa col colore del cliente):
     stesso pattern di InspectorField / StyleBoxStack / StyleEffectsStack. */
  --olo-ui-accent: #e8622a;
  height: 100%;
  display: flex;
  flex-direction: column;
  min-width: 0;
  overflow: hidden;
  container-type: inline-size;
}

/* ── Top tabs (Elementi / Struttura) ─────────────────────── */
.sidebar-tabs {
  display: flex;
  height: 38px;
  border-bottom: 1px solid rgba(0, 0, 0, 0.08);
  background: #fff;
  flex-shrink: 0;
}
.sidebar-tab {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 0;
  font-size: 12px;
  font-weight: 500;
  color: #64748b;
  background: none;
  border: none;
  border-bottom: 2px solid transparent;
  cursor: pointer;
  transition: color 0.15s, border-color 0.15s;
  font-family: inherit;
}
.sidebar-tab:hover { color: #1e293b; }
.sidebar-tab--active {
  color: #1e293b;
  border-bottom-color: var(--olo-ui-accent);
}

/* ── Insert-after banner ─────────────────────────────────── */
.tp-insert-banner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 12px;
  margin: 6px 6px 0;
  background: rgba(232, 98, 42, 0.10);
  border: 1px solid rgba(232, 98, 42, 0.35);
  border-radius: 6px;
  color: #b04217;
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
  color: #b04217;
  cursor: pointer;
  font-size: 14px;
  padding: 0 4px;
  opacity: 0.6;
}
.tp-insert-cancel:hover { opacity: 1; }

/* ── V2 body: rail (56px) + panel (1fr) ──────────────────── */
.olo-sb-body {
  flex: 1;
  display: grid;
  grid-template-columns: 78px 1fr;
  min-height: 0;
  overflow: hidden;
  background: #fff;
}
/* Tab Struttura: rail stretto invariato (le sue label brevi non servono più larghe) */
.olo-sb-body--struct { grid-template-columns: 56px 1fr; }

/* Rail — vertical category buttons */
.olo-sb-rail {
  background: #f9fafb;
  border-right: 1px solid #f1f5f9;
  display: flex;
  flex-direction: column;
  padding: 4px 0;
  overflow-y: auto;
  scrollbar-width: none;
}
.olo-sb-rail::-webkit-scrollbar { display: none; }

.olo-sb-rail-btn {
  position: relative;
  width: 100%;
  height: 56px;
  border: 0;
  background: transparent;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 3px;
  padding: 0 4px;
  font-family: inherit;
  color: #64748b;
  flex-shrink: 0;
  transition: background-color 0.15s, color 0.15s;
}
.olo-sb-rail-btn .bar {
  position: absolute;
  left: 0;
  top: 8px;
  bottom: 8px;
  width: 2px;
  background: transparent;
  border-radius: 0 2px 2px 0;
  transition: background-color 0.15s;
}
.olo-sb-rail-btn .ic {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 18px;
  height: 18px;
}
.olo-sb-rail-btn .ic :deep(svg) { width: 18px; height: 18px; }
.olo-sb-rail-btn .lbl {
  font-size: 10px;
  font-weight: 500;
  line-height: 1.1;
  text-align: center;
  letter-spacing: 0.1px;
  max-width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.olo-sb-rail-btn .cnt {
  position: absolute;
  top: 5px;
  right: 6px;
  font-size: 9px;
  font-weight: 600;
  background: #f1f5f9;
  color: #64748b;
  padding: 1px 5px;
  border-radius: 99px;
  min-width: 14px;
  text-align: center;
  line-height: 1.4;
  font-variant-numeric: tabular-nums;
}
.olo-sb-rail-btn:not(.on):hover {
  color: #1e293b;
  background: rgba(0, 0, 0, 0.02);
}
.olo-sb-rail-btn.on {
  color: #1e293b;
  background: #fff;
}
.olo-sb-rail-btn.on .cnt {
  background: rgba(232, 98, 42, 0.12);
  color: #b04217;
}

/* ── Inserter Elementi: rail coerente ──────────────────────
   Label intere su 2 righe (no troncamento), accento arancio UNICO sulla categoria
   attiva (barretta 3px + icona), niente conteggi sull'icona. Scope --cats: il tab
   Struttura (toni per zona + conteggi) resta invariato. */
.olo-sb-rail--cats .olo-sb-rail-btn {
  height: auto;
  min-height: 60px;
  padding: 8px 4px;
  gap: 5px;
}
.olo-sb-rail--cats .olo-sb-rail-btn .bar { width: 3px; }
.olo-sb-rail--cats .olo-sb-rail-btn .lbl {
  white-space: normal;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow-wrap: anywhere;
  line-height: 1.15;
}
.olo-sb-rail--cats .olo-sb-rail-btn.on {
  box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--olo-ui-accent) 22%, #fff);
}
.olo-sb-rail--cats .olo-sb-rail-btn.on .bar { background: var(--olo-ui-accent); }
.olo-sb-rail--cats .olo-sb-rail-btn.on .ic { color: var(--olo-ui-accent); }

/* Panel — header + search + grid */
.olo-sb-panel {
  display: flex;
  flex-direction: column;
  min-height: 0;
  min-width: 0;
  background: #fff;
}
.olo-sb-panel-head {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 12px 8px;
  flex-shrink: 0;
}
.olo-sb-panel-head .dot {
  width: 8px;
  height: 8px;
  border-radius: 99px;
  flex-shrink: 0;
}
.olo-sb-panel-head h3 {
  margin: 0;
  font-size: 13px;
  font-weight: 700;
  color: #1e293b;
  flex: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.olo-sb-panel-head .cnt {
  background: #f1f5f9;
  color: #64748b;
  font-size: 10px;
  font-weight: 600;
  padding: 1px 7px;
  border-radius: 99px;
  font-variant-numeric: tabular-nums;
  margin-left: auto;
}

/* ── Inserter Elementi: header parlante "Categoria · N" + hint, senza pallino ── */
.olo-sb-panel-head--cats { gap: 0; }
.olo-sb-panel-head--cats .olo-sb-ph-title {
  margin: 0;
  display: flex;
  align-items: baseline;
  gap: 5px;
  min-width: 0;
  flex: 0 1 auto;
  font-size: 13px;
  font-weight: 700;
  color: #1e293b;
}
.olo-sb-panel-head--cats .olo-sb-ph-title .t {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.olo-sb-panel-head--cats .olo-sb-ph-count {
  color: var(--olo-ui-accent);
  font-weight: 700;
  font-variant-numeric: tabular-nums;
  flex-shrink: 0;
}
.olo-sb-panel-head--cats .olo-sb-ph-hint {
  margin-left: auto;
  padding-left: 10px;
  font-size: 11px;
  color: #94a3b8;
  white-space: nowrap;
  flex-shrink: 0;
}

.olo-sb-search {
  margin: 0 12px 10px;
  padding: 6px 10px;
  display: flex;
  align-items: center;
  gap: 8px;
  background: #f9fafb;
  border: 1px solid #f1f5f9;
  border-radius: 8px;
  flex-shrink: 0;
  transition: border-color 0.15s, background 0.15s;
}
.olo-sb-search:focus-within {
  border-color: var(--olo-ui-accent);
  background: #fff;
}
.olo-sb-search > svg {
  color: #94a3b8;
  flex-shrink: 0;
}
.olo-sb-search input {
  flex: 1;
  border: 0;
  background: transparent;
  outline: 0;
  font: inherit;
  font-size: 12px;
  color: #1e293b;
  padding: 2px 0;
  min-width: 0;
}
.olo-sb-search input::placeholder { color: #94a3b8; }
.olo-sb-search .ic-x {
  background: #f1f5f9;
  border: 0;
  width: 18px;
  height: 18px;
  border-radius: 4px;
  display: grid;
  place-items: center;
  cursor: pointer;
  color: #64748b;
  flex-shrink: 0;
  padding: 0;
}
.olo-sb-search .ic-x:hover { background: #e2e8f0; color: #1e293b; }

/* Grid of cards */
.olo-sb-grid {
  flex: 1;
  overflow-y: auto;
  /* padding-top: spazio per il sollevamento (translateY) + ombra della prima riga di card,
     altrimenti l'overflow le taglia in cima al passaggio del mouse. */
  padding: 8px 12px 14px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
  align-content: start;
}

.olo-sb-card {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 9px;
  padding: 14px 8px;
  min-height: 104px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  background: #fff;
  cursor: grab;
  text-align: center;
  font: inherit;
  color: #1e293b;
  font-family: inherit;
  min-width: 0;
  transition: background-color 0.15s, border-color 0.15s, box-shadow 0.15s, transform 0.15s;
  -webkit-user-drag: element;
  user-select: none;
  -webkit-user-select: none;
}
.olo-sb-card:active { cursor: grabbing; }
.olo-sb-card:hover {
  border-color: var(--olo-ui-accent);
  background: rgba(232, 98, 42, 0.04);
  box-shadow: 0 4px 14px -4px color-mix(in srgb, var(--olo-ui-accent) 30%, transparent);
  transform: translateY(-1px);
}
/* Affordance "in trascinamento" (solo CSS, nessuna logica DnD toccata): bordo+ombra arancio,
   translateY e icona piena — l'highlight che il mockup mostra sulla card "Galleria". */
.olo-sb-card:active {
  border-color: var(--olo-ui-accent);
  background: #fff;
  box-shadow: 0 8px 20px -5px color-mix(in srgb, var(--olo-ui-accent) 42%, transparent);
  transform: translateY(-2px);
}
.olo-sb-card:active .ic {
  background: var(--olo-ui-accent);
  color: #fff;
}
.olo-sb-card .ic {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  background: #f1f5f9;
  color: #475569;
  display: grid;
  place-items: center;
  transition: background-color 0.15s, color 0.15s, box-shadow 0.15s;
}
.olo-sb-card .ic :deep(svg) { width: 24px; height: 24px; }
.olo-sb-card:hover .ic {
  background: #fff;
  color: var(--olo-ui-accent);
  box-shadow: 0 1px 2px rgba(16, 24, 40, 0.05);
}
.olo-sb-card .lbl {
  font-size: 11px;
  font-weight: 500;
  line-height: 1.2;
  color: #1e293b;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  max-width: 100%;
}
.olo-sb-card .bdg {
  font-size: 9px;
  font-weight: 600;
  padding: 1px 6px;
  border-radius: 99px;
  align-self: center;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  line-height: 1.4;
}
.olo-sb-card .fav {
  position: absolute;
  top: 6px;
  right: 6px;
  font-size: 12px;
  line-height: 1;
  color: transparent;
  cursor: pointer;
  transition: color 0.15s, opacity 0.15s;
}
.olo-sb-card:hover .fav { color: #cbd5e1; }
.olo-sb-card .fav.on { color: #f59e0b; }
.olo-sb-card .fav:hover { color: #f59e0b; }

.olo-sb-card .grip {
  position: absolute;
  right: 6px;
  bottom: 6px;
  color: #cbd5e1;
  opacity: 0;
  transition: opacity 0.15s;
  pointer-events: none;
  display: flex;
}
.olo-sb-card:hover .grip { opacity: 1; }

/* Global widget card variant */
.olo-sb-card--global {
  border-color: rgba(217, 119, 6, 0.3);
  background: rgba(217, 119, 6, 0.05);
}
.olo-sb-card--global:hover {
  border-color: #D97706;
  background: rgba(217, 119, 6, 0.10);
}
.olo-sb-card--global .ic {
  background: rgba(217, 119, 6, 0.10);
  color: #b45309;
}
.olo-sb-card--global:hover .ic { background: #fff; color: #d97706; }
.olo-sb-card--global .del {
  position: absolute;
  top: -5px;
  right: -5px;
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
  padding: 0;
  z-index: 2;
}
.olo-sb-card--global:hover .del { opacity: 1; }
.olo-sb-card--global .del:hover { background: #b91c1c; }

.olo-sb-empty {
  grid-column: 1 / -1;
  padding: 28px 16px;
  text-align: center;
  color: #94a3b8;
  font-size: 12px;
  line-height: 1.5;
}
.olo-sb-empty b { color: #1e293b; font-weight: 600; }

/* Narrow sidebar fallback (< 280px) — single column */
@container (max-width: 280px) {
  .olo-sb-grid { grid-template-columns: 1fr; }
}

/* ── V2 Structure tab ────────────────────────────────────── */
.olo-sb-rail-btn.tone-info.on    { background: #eff6ff; color: #1d4ed8; }
.olo-sb-rail-btn.tone-info.on .bar    { background: #3b82f6; }
.olo-sb-rail-btn.tone-info.on .cnt    { background: #dbeafe; color: #1d4ed8; }

.olo-sb-rail-btn.tone-brand.on   { background: #faf5ff; color: #7e22ce; }
.olo-sb-rail-btn.tone-brand.on .bar   { background: #a855f7; }
.olo-sb-rail-btn.tone-brand.on .cnt   { background: #f3e8ff; color: #7e22ce; }

.olo-sb-rail-btn.tone-success.on { background: #f0fdf4; color: #15803d; }
.olo-sb-rail-btn.tone-success.on .bar { background: #22c55e; }
.olo-sb-rail-btn.tone-success.on .cnt { background: #dcfce7; color: #15803d; }

.olo-sb-rail-btn.tone-neutral.on { background: #fff; color: #1e293b; }
.olo-sb-rail-btn.tone-neutral.on .bar { background: var(--olo-ui-accent); }

.olo-sb-panel--struct {
  display: flex;
  flex-direction: column;
  min-height: 0;
}
.olo-sb-panel--struct :deep(.st-root) {
  flex: 1;
  overflow-y: auto;
  padding: 4px 8px 8px;
  min-height: 0;
}

/* Toolbar (Comprimi/Espandi/Solo selezione) */
.olo-sb-tools {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 0 12px 8px;
  border-bottom: 1px solid #f1f5f9;
  margin-bottom: 4px;
  flex-shrink: 0;
}
.olo-sb-tools button {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  border: 0;
  background: transparent;
  cursor: pointer;
  font: inherit;
  font-family: inherit;
  font-size: 11px;
  color: #64748b;
  padding: 4px 6px;
  border-radius: 4px;
  transition: background-color 0.12s, color 0.12s;
}
.olo-sb-tools button:hover {
  background: #f1f5f9;
  color: #1e293b;
}
.olo-sb-tools .spc { flex: 1; }
.olo-sb-tools button.iso {
  width: 24px;
  height: 24px;
  padding: 0;
  display: grid;
  place-items: center;
}
.olo-sb-tools button.iso.on {
  background: rgba(232, 98, 42, 0.12);
  color: var(--olo-ui-accent);
}

/* Breadcrumb sticky bottom */
.olo-sb-breadcrumb {
  border-top: 1px solid #f1f5f9;
  background: #f9fafb;
  padding: 6px 12px 8px;
  display: flex;
  flex-direction: column;
  gap: 3px;
  flex-shrink: 0;
}
.olo-sb-breadcrumb .lbl {
  font-size: 9px;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: #94a3b8;
  font-weight: 600;
}
.olo-sb-breadcrumb .path {
  display: flex;
  align-items: center;
  gap: 3px;
  font-size: 11px;
  color: #64748b;
  flex-wrap: wrap;
}
.olo-sb-breadcrumb .path .seg {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 80px;
}
.olo-sb-breadcrumb .path .chev {
  color: #cbd5e1;
  flex-shrink: 0;
}
.olo-sb-breadcrumb .path .cur {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  background: rgba(232, 98, 42, 0.12);
  color: var(--olo-ui-accent);
  font-weight: 600;
  padding: 1px 7px;
  border-radius: 99px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 140px;
}
.olo-sb-breadcrumb .path .cur .ic {
  display: inline-flex;
  align-items: center;
  flex-shrink: 0;
}
.olo-sb-breadcrumb .path .cur .ic :deep(svg) { width: 11px; height: 11px; }
</style>
