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
        <div class="tp-cat-head">
          <span class="tp-cat-label">{{ categoryLabel(category) }}</span>
          <span class="tp-cat-count">{{ tiles.length }}</span>
        </div>
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

      <div v-if="Object.keys(tilesByCategory).length === 0" class="tp-empty">
        Caricamento tile...
      </div>

      <p class="tp-hint">Clicca o trascina per aggiungere</p>
    </div>

    <!-- Structure tab -->
    <StructureTree v-else />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useTilesStore } from '@/stores/tiles';
import { useDragDrop } from '@/composables/useDragDrop';
import StructureTree from './StructureTree.vue';

const tilesStore = useTilesStore();
const { handleDropFromSidebar } = useDragDrop();

const activeTab = ref('tiles');
const tilesByCategory = computed(() => tilesStore.tilesByCategory);

const categoryColors = {
  layout: 'var(--olo-color-primary, #6366F1)',
  content: '#22C55E',
  media: '#A855F7',
};

const categoryLabels = {
  layout: 'Layout',
  content: 'Contenuto',
  media: 'Media',
  structure: 'Struttura',
  header: 'Header',
  dynamic: 'Dinamico',
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
  // Prevent click from firing after a drag operation
  if (Date.now() - lastDragStart < 1000) return;
  handleDropFromSidebar(tileType);
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
  padding: 8px 0;
  font-size: 11px;
  font-weight: 500;
  letter-spacing: 0.3px;
  color: #6B7280;
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
  margin-bottom: 6px;
}
.tp-cat-head {
  display: flex;
  align-items: center;
  gap: 5px;
  padding: 0 4px 4px;
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
  color: #6B7280;
  background: rgba(255, 255, 255, 0.04);
  padding: 0 4px;
  border-radius: 8px;
  line-height: 14px;
}
.tp-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3px;
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
.tp-hint {
  font-size: 10px;
  color: #6B7280;
  text-align: center;
  margin-top: 8px;
}
</style>
