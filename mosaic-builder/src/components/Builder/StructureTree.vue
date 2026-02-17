<template>
  <div class="st-root">
    <!-- Empty state -->
    <div v-if="tilesStore.canvasTiles.length === 0" class="st-empty">
      <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#4B5563" stroke-width="1.5">
        <rect x="3" y="3" width="7" height="7" rx="1"/>
        <rect x="14" y="3" width="7" height="7" rx="1"/>
        <rect x="3" y="14" width="7" height="7" rx="1"/>
        <rect x="14" y="14" width="7" height="7" rx="1"/>
      </svg>
      <span>Nessun elemento</span>
    </div>

    <template v-else>
      <!-- Counter -->
      <div class="st-count">{{ tilesStore.totalElementCount }} element{{ tilesStore.totalElementCount !== 1 ? 'i' : 'o' }}</div>

      <!-- Sections -->
      <draggable
        v-model="tilesStore.canvasTiles"
        item-key="id"
        ghost-class="st-ghost"
        animation="150"
        handle=".st-grip"
        @change="onChange"
      >
        <template #item="{ element: section }">
          <div class="st-item">
            <!-- Section node -->
            <div
              class="st-row st-row--section"
              :class="{ 'st-row--active': builderStore.selectedTileId === section.id }"
              @click="selectTile(section.id)"
            >
              <span class="st-grip" title="Trascina per riordinare">
                <svg width="6" height="10" viewBox="0 0 6 10" fill="currentColor">
                  <circle cx="1" cy="1" r="1"/><circle cx="5" cy="1" r="1"/>
                  <circle cx="1" cy="5" r="1"/><circle cx="5" cy="5" r="1"/>
                  <circle cx="1" cy="9" r="1"/><circle cx="5" cy="9" r="1"/>
                </svg>
              </span>
              <button class="st-toggle" :class="{ 'st-toggle--open': isExpanded(section.id) }" @click.stop="toggle(section.id)">
                <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><path d="M2 1l4 3-4 3z"/></svg>
              </button>
              <span class="st-dot" style="background: var(--olo-color-primary, #6366F1)"></span>
              <span class="st-name">Sezione</span>
              <span class="st-badge" v-if="section.children?.length">{{ section.children.length }}r</span>
              <span class="st-actions">
                <button title="Duplica" @click.stop="duplicate(section.id)">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="9" y="9" width="13" height="13" rx="2"/>
                    <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
                  </svg>
                </button>
                <button title="Elimina" @click.stop="remove(section.id)">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6L6 18M6 6l12 12"/>
                  </svg>
                </button>
              </span>
            </div>

            <!-- Section children: Rows -->
            <div v-if="isExpanded(section.id)" class="st-sub">
              <draggable
                :list="section.children"
                item-key="id"
                ghost-class="st-ghost"
                :group="{ name: 'st-rows' }"
                animation="150"
                handle=".st-grip"
                @change="onChange"
              >
                <template #item="{ element: row }">
                  <div class="st-item">
                    <!-- Row node -->
                    <div
                      class="st-row st-row--row"
                      :class="{ 'st-row--active': builderStore.selectedTileId === row.id }"
                      @click.stop="selectTile(row.id)"
                    >
                      <span class="st-grip" title="Trascina per riordinare">
                        <svg width="6" height="10" viewBox="0 0 6 10" fill="currentColor">
                          <circle cx="1" cy="1" r="1"/><circle cx="5" cy="1" r="1"/>
                          <circle cx="1" cy="5" r="1"/><circle cx="5" cy="5" r="1"/>
                          <circle cx="1" cy="9" r="1"/><circle cx="5" cy="9" r="1"/>
                        </svg>
                      </span>
                      <button class="st-toggle" :class="{ 'st-toggle--open': isExpanded(row.id) }" @click.stop="toggle(row.id)">
                        <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><path d="M2 1l4 3-4 3z"/></svg>
                      </button>
                      <span class="st-dot" style="background: var(--olo-color-primary, #6366F1)"></span>
                      <span class="st-name">Riga <span class="st-meta">{{ row.settings?.layout === 'custom' ? (row.settings?.custom_widths || '%') : (row.settings?.layout || '50-50') }}</span></span>
                      <span class="st-actions">
                        <button title="Duplica" @click.stop="duplicate(row.id)">
                          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="9" y="9" width="13" height="13" rx="2"/>
                            <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
                          </svg>
                        </button>
                        <button title="Elimina" @click.stop="remove(row.id)">
                          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 6L6 18M6 6l12 12"/>
                          </svg>
                        </button>
                      </span>
                    </div>

                    <!-- Row children: Columns -->
                    <div v-if="isExpanded(row.id)" class="st-sub">
                      <div v-for="(col, ci) in (row.children || [])" :key="col.id" class="st-item">
                        <!-- Column node -->
                        <div
                          class="st-row st-row--column"
                          :class="{ 'st-row--active': builderStore.selectedTileId === col.id }"
                          @click.stop="selectTile(col.id)"
                        >
                          <span class="st-grip-ph"></span>
                          <button
                            v-if="col.children && col.children.length > 0"
                            class="st-toggle"
                            :class="{ 'st-toggle--open': isExpanded(col.id) }"
                            @click.stop="toggle(col.id)"
                          >
                            <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><path d="M2 1l4 3-4 3z"/></svg>
                          </button>
                          <span v-else class="st-toggle-ph"></span>
                          <span class="st-dot" style="background: #3B82F6"></span>
                          <span class="st-name">Col {{ ci + 1 }} <span class="st-meta">{{ col.settings?.width_medium || '' }}</span></span>
                          <span v-if="col.children?.length" class="st-badge">{{ col.children.length }}</span>
                        </div>

                        <!-- Column children: Elements -->
                        <div v-if="isExpanded(col.id) && col.children?.length" class="st-sub">
                          <draggable
                            :list="col.children"
                            item-key="id"
                            ghost-class="st-ghost"
                            :group="{ name: 'st-elements' }"
                            animation="150"
                            handle=".st-grip"
                            class="st-dropzone"
                            @change="onChange"
                          >
                            <template #item="{ element: tile }">
                              <div
                                class="st-row st-row--element"
                                :class="{ 'st-row--active': builderStore.selectedTileId === tile.id }"
                                @click.stop="selectTile(tile.id)"
                              >
                                <span class="st-grip" title="Trascina per riordinare">
                                  <svg width="6" height="10" viewBox="0 0 6 10" fill="currentColor">
                                    <circle cx="1" cy="1" r="1"/><circle cx="5" cy="1" r="1"/>
                                    <circle cx="1" cy="5" r="1"/><circle cx="5" cy="5" r="1"/>
                                    <circle cx="1" cy="9" r="1"/><circle cx="5" cy="9" r="1"/>
                                  </svg>
                                </span>
                                <span class="st-toggle-ph"></span>
                                <span class="st-dot" :style="{ background: dotColor(tile.type) }"></span>
                                <span class="st-name">{{ tileLabel(tile) }}</span>
                                <span class="st-actions">
                                  <button title="Duplica" @click.stop="duplicate(tile.id)">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                      <rect x="9" y="9" width="13" height="13" rx="2"/>
                                      <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
                                    </svg>
                                  </button>
                                  <button title="Elimina" @click.stop="remove(tile.id)">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                      <path d="M18 6L6 18M6 6l12 12"/>
                                    </svg>
                                  </button>
                                </span>
                              </div>
                            </template>
                          </draggable>
                        </div>
                      </div>
                    </div>
                  </div>
                </template>
              </draggable>
            </div>
          </div>
        </template>
      </draggable>
    </template>
  </div>
</template>

<script setup>
import { reactive, computed } from 'vue';
import draggable from 'vuedraggable';
import { useTilesStore } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';

const tilesStore = useTilesStore();
const builderStore = useBuilderStore();

// Track collapsed state (everything expanded by default)
const collapsed = reactive({});

function isExpanded(id) {
  return collapsed[id] !== true;
}

function toggle(id) {
  collapsed[id] = !collapsed[id];
}

// Category color mapping
const categoryColors = {
  layout: 'var(--olo-color-primary, #6366F1)',
  content: '#22C55E',
  media: '#A855F7',
  structure: '#3B82F6',
};

const typeCategoryMap = computed(() => {
  const map = {};
  for (const t of tilesStore.registeredTiles) {
    map[t.type] = t.category || 'general';
  }
  return map;
});

function dotColor(type) {
  const cat = typeCategoryMap.value[type] || 'general';
  return categoryColors[cat] || '#6B7280';
}

const tileNameMap = computed(() => {
  const map = {};
  for (const t of tilesStore.registeredTiles) {
    map[t.type] = t.name;
  }
  return map;
});

function tileLabel(tile) {
  const s = tile.settings || {};
  const custom = s.title || s.heading || s.plan_name || s.name || s.quote || s.text || '';
  if (custom) {
    const clean = custom.replace(/<[^>]*>/g, '').trim();
    if (clean) return clean.length > 30 ? clean.substring(0, 30) + '\u2026' : clean;
  }
  return tileNameMap.value[tile.type] || tile.type;
}

function selectTile(id) {
  builderStore.selectTile(id);
}

function duplicate(id) {
  tilesStore.duplicateTile(id);
  builderStore.isDirty = true;
}

function remove(id) {
  tilesStore.removeTile(id);
  if (builderStore.selectedTileId === id) builderStore.deselectTile();
  builderStore.isDirty = true;
}

function onChange() {
  builderStore.isDirty = true;
}
</script>

<style scoped>
.st-root {
  padding: 8px 6px;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Empty state */
.st-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  padding: 32px 0;
  color: #9CA3AF;
  font-size: 11px;
}

/* Counter */
.st-count {
  font-size: 10px;
  color: #9CA3AF;
  padding: 0 4px 6px;
  letter-spacing: 0.3px;
}

/* Node row */
.st-row {
  display: flex;
  align-items: center;
  gap: 5px;
  height: 26px;
  padding: 0 4px;
  border-radius: 3px;
  cursor: pointer;
  position: relative;
  border-left: 2px solid transparent;
  transition: background-color 0.1s;
}
.st-row:hover {
  background: rgba(255, 255, 255, 0.04);
}
.st-row--active {
  background: rgb(var(--olo-primary-rgb, 99 102 241) / 0.1);
  border-left-color: var(--olo-color-primary, #6366F1);
}
.st-row--active:hover {
  background: rgb(var(--olo-primary-rgb, 99 102 241) / 0.14);
}

/* Section row styling */
.st-row--section {
  font-weight: 500;
}

/* Drag grip */
.st-grip {
  flex-shrink: 0;
  width: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: grab;
  color: transparent;
  transition: color 0.15s;
}
.st-row:hover .st-grip {
  color: #4B5563;
}
.st-grip:hover {
  color: #9CA3AF !important;
}
.st-grip-ph {
  flex-shrink: 0;
  width: 12px;
}

/* Expand toggle */
.st-toggle {
  flex-shrink: 0;
  width: 14px;
  height: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: none;
  border: none;
  color: #4B5563;
  cursor: pointer;
  padding: 0;
  border-radius: 2px;
  transition: color 0.15s, transform 0.15s;
}
.st-toggle:hover {
  color: #9CA3AF;
}
.st-toggle--open {
  transform: rotate(90deg);
}
.st-toggle-ph {
  flex-shrink: 0;
  width: 14px;
}

/* Category dot */
.st-dot {
  flex-shrink: 0;
  width: 5px;
  height: 5px;
  border-radius: 50%;
  opacity: 0.8;
}

/* Label */
.st-name {
  flex: 1;
  min-width: 0;
  font-size: 11px;
  color: #C8CCD0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  line-height: 1;
  font-weight: 400;
}
.st-row--active .st-name {
  color: #E5E7EB;
}
.st-meta {
  color: #6B7280;
  font-size: 10px;
  font-weight: 400;
}

/* Badge (count) */
.st-badge {
  font-size: 9px;
  color: #6B7280;
  background: rgba(255, 255, 255, 0.04);
  padding: 0 4px;
  border-radius: 8px;
  line-height: 14px;
  flex-shrink: 0;
}

/* Hover actions */
.st-actions {
  display: flex;
  gap: 1px;
  opacity: 0;
  transition: opacity 0.1s;
  flex-shrink: 0;
}
.st-row:hover .st-actions {
  opacity: 1;
}
.st-actions button {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 18px;
  height: 18px;
  background: none;
  border: none;
  color: #4B5563;
  cursor: pointer;
  border-radius: 2px;
  padding: 0;
  transition: color 0.1s, background-color 0.1s;
}
.st-actions button:hover {
  color: #D1D5DB;
  background: rgba(255, 255, 255, 0.08);
}

/* Subtree (children) */
.st-sub {
  margin-left: 18px;
  padding-left: 8px;
  border-left: 1px solid #262a30;
}

/* Dropzone for elements */
.st-dropzone {
  min-height: 4px;
}

/* Drag ghost */
.st-ghost {
  opacity: 0.3;
}
</style>
