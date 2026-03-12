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
            >{{ tile.name }}</button>
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
              >{{ gw.name }}</button>
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
import { createSection, createRow, createColumn } from '@/stores/tiles';
import StructureTree from './StructureTree.vue';

const tilesStore = useTilesStore();
const builderStore = useBuilderStore();
const { handleDropFromSidebar } = useDragDrop();

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

let lastDragStart = 0;

function onDragStart(event, tileType) {
  lastDragStart = Date.now();
  event.dataTransfer.setData('tile-type', tileType);
  event.dataTransfer.effectAllowed = 'copy';
}

function addTile(tileType) {
  if (Date.now() - lastDragStart < 1000) return;
  handleDropFromSidebar(tileType);
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
  border-bottom: 1px solid #374151;
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
  color: #9CA3AF;
  background: none;
  border: none;
  border-bottom: 2px solid transparent;
  cursor: pointer;
  transition: color 0.15s, border-color 0.15s;
}
.sidebar-tab:hover {
  color: #9CA3AF;
}
.sidebar-tab--active {
  color: #C8CCD0;
  border-bottom-color: var(--olo-color-primary, #6366F1);
}

/* Tile palette */
.tp-root {
  padding: 8px 6px;
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
  background: rgba(255, 255, 255, 0.04);
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
  color: #9CA3AF;
}
.tp-cat-count {
  font-size: 9px;
  color: #E5E7EB;
  background: rgba(255, 255, 255, 0.08);
  padding: 0 5px;
  border-radius: 8px;
  line-height: 15px;
}
.tp-chevron {
  margin-left: auto;
  color: #9CA3AF;
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
  gap: 3px;
  padding: 2px 4px 6px;
}
.tp-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 26px;
  padding: 0 4px;
  border-radius: 4px;
  cursor: pointer;
  border: 1px solid #2D3748;
  background: rgba(255, 255, 255, 0.02);
  font-size: 10px;
  color: #9CA3AF;
  font-weight: 500;
  font-family: inherit;
  line-height: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  user-select: none;
  transition: background-color 0.1s, border-color 0.1s, color 0.1s;
}
.tp-btn:hover {
  background: rgb(var(--olo-primary-rgb, 99 102 241) / 0.1);
  border-color: var(--cat-color, var(--olo-color-primary, #6366F1));
  color: #E5E7EB;
}
.tp-btn:active {
  opacity: 0.5;
}
.tp-empty {
  text-align: center;
  color: #9CA3AF;
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
  background: #7f1d1d;
  color: #fca5a5;
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
  background: #991b1b;
  color: #fff;
}
.tp-btn--global {
  border-color: #92400e;
  color: #D97706;
}
.tp-btn--global:hover {
  background: rgba(217, 119, 6, 0.1) !important;
  border-color: #D97706 !important;
  color: #FBBF24 !important;
}
.tp-hint {
  font-size: 10px;
  color: #9CA3AF;
  text-align: center;
  margin-top: 8px;
}
</style>
