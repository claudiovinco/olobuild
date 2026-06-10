<template>
  <Teleport to="body">
    <div
      v-if="visible"
      ref="menuRef"
      class="olo-ctx-menu"
      role="menu"
      :aria-label="t('Menu contestuale')"
      :style="{ left: x + 'px', top: y + 'px' }"
      @click.stop
      @contextmenu.prevent
      @keydown.escape="close"
      @keydown.arrow-down.prevent="focusNextItem"
      @keydown.arrow-up.prevent="focusPrevItem"
    >
      <button @click="close" class="olo-ctx-close" :title="t('Chiudi')">
        <svg width="10" height="10" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 2l8 8M10 2l-8 8"/></svg>
      </button>
      <button @click="doCopy" class="olo-ctx-item" role="menuitem">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
        Copia
      </button>
      <button @click="doPaste" :disabled="!tilesStore.clipboardTile" class="olo-ctx-item" role="menuitem">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 2H9a1 1 0 0 0-1 1v2c0 .6.4 1 1 1h6c.6 0 1-.4 1-1V3c0-.6-.4-1-1-1Z"/><path d="M8 4H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-2"/></svg>
        Incolla
      </button>
      <div class="olo-ctx-sep"></div>
      <button @click="doCopyStyle" class="olo-ctx-item" role="menuitem">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 3-1.9 12.1a1 1 0 0 1-1 .9H4.8a1 1 0 0 1-1-.8L2 3"/><path d="M6.2 15h11.7"/><path d="M13 3v12"/></svg>
        Copia stile
      </button>
      <button @click="doPasteStyle" :disabled="!tilesStore.clipboardStyle" class="olo-ctx-item" role="menuitem">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 3-1.9 12.1a1 1 0 0 1-1 .9H4.8a1 1 0 0 1-1-.8L2 3"/><path d="M6.2 15h11.7"/><path d="M13 3v12"/></svg>
        Incolla stile
      </button>
      <div class="olo-ctx-sep"></div>
      <button @click="doDuplicate" class="olo-ctx-item" role="menuitem">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="13" height="13" rx="2"/><rect x="9" y="9" width="13" height="13" rx="2"/></svg>
        Duplica
      </button>
      <button @click="doMoveUp" class="olo-ctx-item" role="menuitem">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m18 15-6-6-6 6"/></svg>
        Sposta su
      </button>
      <button @click="doMoveDown" class="olo-ctx-item" role="menuitem">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
        Sposta giù
      </button>
      <div class="olo-ctx-sep"></div>
      <button v-if="currentTileIsSection" @click="doSaveAsTemplate" class="olo-ctx-item" role="menuitem">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        Salva sezione come template
      </button>
      <button v-if="currentTileIsSection" @click="doLoadTemplate" class="olo-ctx-item" role="menuitem">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 15v4c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Carica template sezione
      </button>
      <button @click="doSaveGlobal" class="olo-ctx-item" role="menuitem">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5Z"/><path d="m2 17 10 5 10-5"/><path d="m2 12 10 5 10-5"/></svg>
        Salva come globale
      </button>
      <button @click="doDetachGlobal" :disabled="!currentTileIsGlobal" class="olo-ctx-item" role="menuitem">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
        Sgancia globale
      </button>
      <!-- Row layout options -->
      <template v-if="hasParentRow">
        <div class="olo-ctx-sep"></div>
        <div class="olo-ctx-label">{{ t('Layout colonne') }}</div>
        <div class="olo-ctx-layouts">
          <button v-for="l in rowLayouts" :key="l.key" class="olo-ctx-layout"
            :class="{ 'olo-ctx-layout--active': !isParentGrid && currentRowLayout === l.key }"
            :title="l.label" @click="doChangeLayout(l.key)">
            <div class="olo-ctx-layout-preview">
              <div v-for="(w, i) in l.cols" :key="i" class="olo-ctx-layout-col" :style="{ flex: w }"></div>
            </div>
          </button>
        </div>
        <template v-for="(cat, catIdx) in gridCategories" :key="catIdx">
          <button class="olo-ctx-cat-toggle" :class="{ 'olo-ctx-cat-toggle--open': openCategory === catIdx }"
            @click="toggleCategory(catIdx)" type="button">
            <span class="olo-ctx-cat-label">{{ cat.label }}</span>
            <svg class="olo-ctx-cat-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          <div v-show="openCategory === catIdx" class="olo-ctx-layouts">
            <button v-for="g in cat.items" :key="g.id" class="olo-ctx-layout"
              :class="{ 'olo-ctx-layout--active': isParentGrid && currentGridTemplate === g.id }"
              :title="g.name" @click="doChangeGrid(g.id)">
              <svg class="olo-ctx-grid-svg" viewBox="0 0 36 22">
                <rect v-for="(r, ri) in g.preview.rects" :key="ri"
                  :x="r.x * (36/g.preview.cols)" :y="r.y * (22/g.preview.rows)"
                  :width="r.w * (36/g.preview.cols) - 1" :height="r.h * (22/g.preview.rows) - 1"
                  rx="1" fill="currentColor" opacity="0.6"
                />
              </svg>
            </button>
          </div>
        </template>
      </template>
      <div class="olo-ctx-sep"></div>
      <button @click="doDelete" class="olo-ctx-item olo-ctx-item--danger">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
        Elimina
      </button>
    </div>
  </Teleport>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, computed, nextTick, onMounted, onBeforeUnmount } from 'vue';
import { useTilesStore } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';
import { columns as gridColumns, multirow as gridMultirow, masonry as gridMasonry, sidebar as gridSidebar } from '@/config/gridTemplates.js';

const tilesStore = useTilesStore();
const builderStore = useBuilderStore();

const visible = ref(false);
const x = ref(0);
const y = ref(0);
const tileId = ref(null);
const menuRef = ref(null);

const currentTileIsGlobal = computed(() => {
  if (!tileId.value) return false;
  const tile = tilesStore.getTileById(tileId.value);
  return tile ? !!tile.global_id : false;
});

const currentTileIsSection = computed(() => {
  if (!tileId.value) return false;
  const tile = tilesStore.getTileById(tileId.value);
  return tile ? tile.type === 'section' : false;
});

const currentTileIsRow = computed(() => {
  if (!tileId.value) return false;
  const tile = tilesStore.getTileById(tileId.value);
  return tile ? tile.type === 'row' : false;
});

// Find the nearest parent row for the selected tile
const parentRow = computed(() => {
  if (!tileId.value) return null;
  const tile = tilesStore.getTileById(tileId.value);
  if (!tile) return null;
  if (tile.type === 'row') return tile;
  // Walk up to find the row ancestor
  return findAncestorOfType(tileId.value, 'row');
});

const hasParentRow = computed(() => !!parentRow.value);

const currentRowLayout = computed(() => {
  if (!parentRow.value) return '';
  return parentRow.value.settings?.layout || '100';
});

const rowLayouts = [
  { key: '100', label: '1 colonna', cols: [1] },
  { key: '50-50', label: '1/2 + 1/2', cols: [1, 1] },
  { key: '33-33-33', label: '1/3 x 3', cols: [1, 1, 1] },
  { key: '25-25-25-25', label: '1/4 x 4', cols: [1, 1, 1, 1] },
  { key: '20-20-20-20-20', label: '1/5 x 5', cols: [1, 1, 1, 1, 1] },
  { key: '16-16-16-16-16-16', label: '1/6 x 6', cols: [1, 1, 1, 1, 1, 1] },
  { key: '66-33', label: '2/3 + 1/3', cols: [2, 1] },
  { key: '33-66', label: '1/3 + 2/3', cols: [1, 2] },
  { key: '25-50-25', label: '1/4 + 1/2 + 1/4', cols: [1, 2, 1] },
  { key: '20-60-20', label: '1/5 + 3/5 + 1/5', cols: [1, 3, 1] },
];

// All grid templates organized by category
const gridCategories = [
  { label: 'Colonne', items: gridColumns },
  { label: 'Multi-riga', items: gridMultirow },
  { label: 'Masonry', items: gridMasonry },
  { label: 'Sidebar', items: gridSidebar },
];

// Accordion: solo una categoria aperta alla volta. -1 = tutte chiuse.
// Default: nessuna aperta — il menu resta compatto, l'utente sceglie.
const openCategory = ref(-1);
function toggleCategory(idx) {
  openCategory.value = openCategory.value === idx ? -1 : idx;
}

const isParentGrid = computed(() => {
  return parentRow.value?.settings?.layout_mode === 'grid';
});

const currentGridTemplate = computed(() => {
  return parentRow.value?.settings?.grid_template || '';
});

function open(event, id) {
  tileId.value = id;
  builderStore.selectTile(id);
  const mx = event.clientX;
  const my = event.clientY;
  x.value = Math.min(mx, window.innerWidth - 220);
  y.value = my;
  visible.value = true;
  // After render, clamp to viewport if menu overflows bottom
  nextTick(() => {
    const el = menuRef.value;
    if (!el) return;
    const rect = el.getBoundingClientRect();
    if (rect.bottom > window.innerHeight - 8) {
      y.value = Math.max(8, window.innerHeight - rect.height - 8);
    }
    if (rect.right > window.innerWidth - 8) {
      x.value = Math.max(8, window.innerWidth - rect.width - 8);
    }
  });
}

function close() {
  visible.value = false;
  tileId.value = null;
}

// A11y: arrow key navigation for menu items
function focusNextItem() {
  if (!menuRef.value) return;
  const items = menuRef.value.querySelectorAll('[role="menuitem"]:not(:disabled)');
  const current = document.activeElement;
  let idx = Array.from(items).indexOf(current);
  idx = (idx + 1) % items.length;
  items[idx]?.focus();
}
function focusPrevItem() {
  if (!menuRef.value) return;
  const items = menuRef.value.querySelectorAll('[role="menuitem"]:not(:disabled)');
  const current = document.activeElement;
  let idx = Array.from(items).indexOf(current);
  idx = (idx - 1 + items.length) % items.length;
  items[idx]?.focus();
}

function doCopy() {
  if (tileId.value) tilesStore.copyTile(tileId.value);
  close();
}

function doPaste() {
  if (!tilesStore.clipboardTile || !tileId.value) { close(); return; }
  // Find parent of current tile and paste after it
  const tile = tilesStore.getTileById(tileId.value);
  if (!tile) { close(); return; }
  // Paste as sibling after current tile
  const parent = findParentOfTile(tileId.value);
  if (parent) {
    const idx = parent.children.findIndex(c => c.id === tileId.value);
    tilesStore.pasteTile(parent.id, idx + 1);
  } else {
    tilesStore.pasteTile(null);
  }
  builderStore.isDirty = true;
  close();
}

function doCopyStyle() {
  if (tileId.value) tilesStore.copyStyle(tileId.value);
  close();
}

function doPasteStyle() {
  if (tileId.value) {
    tilesStore.pasteStyle(tileId.value);
    builderStore.isDirty = true;
  }
  close();
}

function doDuplicate() {
  if (tileId.value) {
    tilesStore.duplicateTile(tileId.value);
    builderStore.isDirty = true;
  }
  close();
}

function doMoveUp() {
  if (tileId.value) {
    tilesStore.moveUp(tileId.value);
    builderStore.isDirty = true;
  }
  close();
}

function doMoveDown() {
  if (tileId.value) {
    tilesStore.moveDown(tileId.value);
    builderStore.isDirty = true;
  }
  close();
}

function doSaveAsTemplate() {
  if (!tileId.value) { close(); return; }
  const tile = tilesStore.getTileById(tileId.value);
  if (!tile || tile.type !== 'section') { close(); return; }
  document.dispatchEvent(new CustomEvent('olo:save-section', { detail: { section: tile } }));
  close();
}

function doLoadTemplate() {
  document.dispatchEvent(new CustomEvent('olo:load-template'));
  close();
}

async function doSaveGlobal() {
  if (tileId.value) {
    await tilesStore.saveAsGlobalWidget(tileId.value);
    builderStore.isDirty = true;
  }
  close();
}

function doDetachGlobal() {
  if (tileId.value) {
    tilesStore.detachGlobalWidget(tileId.value);
    builderStore.isDirty = true;
  }
  close();
}

function doChangeLayout(layoutKey) {
  const row = parentRow.value;
  if (!row) { close(); return; }

  // Update DEVE finire sulla ROW, non sul tile cliccato col destro (che può
  // essere una colonna figlia). updateTile aspetta un oggetto da mergiare
  // in tile.settings, non un path-string come 'settings.layout' (quello faceva
  // silently fail e il layout/layout_mode non venivano mai aggiornati).
  tilesStore.updateTile(row.id, {
    layout: layoutKey,
    layout_mode: 'flex',
    grid_template: '',
    grid_columns: '',
    grid_rows: '',
  });

  // Ensure correct number of columns
  const colCounts = {
    '100': 1, '50-50': 2, '33-33-33': 3, '25-25-25-25': 4,
    '20-20-20-20-20': 5, '16-16-16-16-16-16': 6,
    '66-33': 2, '33-66': 2, '25-50-25': 3, '20-60-20': 3,
  };
  const widthMaps = {
    '100': ['1-1'], '50-50': ['1-2', '1-2'], '33-33-33': ['1-3', '1-3', '1-3'],
    '25-25-25-25': ['1-4', '1-4', '1-4', '1-4'],
    '20-20-20-20-20': ['1-5', '1-5', '1-5', '1-5', '1-5'],
    '16-16-16-16-16-16': ['1-6', '1-6', '1-6', '1-6', '1-6', '1-6'],
    '66-33': ['2-3', '1-3'], '33-66': ['1-3', '2-3'],
    '25-50-25': ['1-4', '1-2', '1-4'], '20-60-20': ['1-5', '3-5', '1-5'],
  };
  const needed = colCounts[layoutKey] || 1;
  const widths = widthMaps[layoutKey] || ['1-1'];

  if (!row.children) row.children = [];

  // Add columns if needed
  while (row.children.length < needed) {
    row.children.push({ id: 'col-' + Math.random().toString(36).substr(2, 8), type: 'column', settings: {}, style: {}, advanced: {}, children: [] });
  }

  // If reducing columns, merge excess children into the last kept column
  if (row.children.length > needed) {
    const lastCol = row.children[needed - 1];
    if (!lastCol.children) lastCol.children = [];
    for (let i = needed; i < row.children.length; i++) {
      if (Array.isArray(row.children[i].children)) {
        lastCol.children = [...lastCol.children, ...row.children[i].children];
      }
    }
    row.children.splice(needed);
  }

  // Update widths and clean grid settings from columns
  for (let i = 0; i < needed; i++) {
    if (row.children[i]) {
      row.children[i].settings = row.children[i].settings || {};
      row.children[i].settings.width_medium = widths[i] || '1-1';
      delete row.children[i].settings.grid_column;
      delete row.children[i].settings.grid_row;
    }
  }

  builderStore.isDirty = true;
  close();
}

function doChangeGrid(templateId) {
  const row = parentRow.value;
  if (!row) { close(); return; }
  tilesStore.changeRowToGrid(row.id, templateId);
  builderStore.isDirty = true;
  close();
}

function doDelete() {
  if (tileId.value) {
    tilesStore.removeTile(tileId.value);
    builderStore.deselectTile();
    builderStore.isDirty = true;
  }
  close();
}

// Utility: find ancestor of a specific type
function findAncestorOfType(id, type, nodes) {
  nodes = nodes || tilesStore.canvasTiles.concat(tilesStore.headerTiles || [], tilesStore.footerTiles || []);
  for (const node of nodes) {
    if (Array.isArray(node.children)) {
      // Check if any child (at any depth) matches the id
      const found = findNodeInTree(node.children, id);
      if (found) {
        // Is this node the type we want?
        if (node.type === type) return node;
        // Or search children
        const deeper = findAncestorOfType(id, type, node.children);
        if (deeper) return deeper;
      }
    }
  }
  return null;
}

function findNodeInTree(nodes, id) {
  for (const n of nodes) {
    if (n.id === id) return n;
    if (n.children) {
      const f = findNodeInTree(n.children, id);
      if (f) return f;
    }
  }
  return null;
}

// Utility: find parent node of a tile
function findParentOfTile(id, nodes) {
  nodes = nodes || tilesStore.canvasTiles;
  for (const node of nodes) {
    if (Array.isArray(node.children)) {
      if (node.children.some(c => c.id === id)) return node;
      const found = findParentOfTile(id, node.children);
      if (found) return found;
    }
  }
  return null;
}

function onClickOutside() {
  if (visible.value) close();
}

function onKeydown(e) {
  if (e.key === 'Escape' && visible.value) close();
}

onMounted(() => {
  document.addEventListener('click', onClickOutside);
  document.addEventListener('contextmenu', onClickOutside);
  document.addEventListener('keydown', onKeydown);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', onClickOutside);
  document.removeEventListener('contextmenu', onClickOutside);
  document.removeEventListener('keydown', onKeydown);
});

defineExpose({ open, close });
</script>

<style scoped>
.olo-ctx-menu {
  position: fixed;
  z-index: 99999;
  min-width: 200px;
  max-height: calc(100vh - 32px);
  overflow-y: auto;
  background: #1f2937;
  border: 1px solid #374151;
  border-radius: 8px;
  padding: 4px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.4);
  font-family: inherit;
}
.olo-ctx-close {
  position: absolute;
  top: 4px;
  right: 4px;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  background: transparent;
  color: #6b7280;
  cursor: pointer;
  border-radius: 4px;
}
.olo-ctx-close:hover {
  background: #374151;
  color: #fff;
}
.olo-ctx-item {
  display: flex;
  align-items: center;
  gap: 8px;
  width: 100%;
  padding: 6px 10px;
  border: none;
  background: none;
  color: #d1d5db;
  font-size: 12px;
  cursor: pointer;
  border-radius: 4px;
  text-align: left;
}
.olo-ctx-item:hover:not(:disabled) {
  background: #374151;
  color: #fff;
}
.olo-ctx-item:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
.olo-ctx-item--danger:hover:not(:disabled) {
  background: #7f1d1d;
  color: #fca5a5;
}
.olo-ctx-sep {
  height: 1px;
  background: #374151;
  margin: 3px 6px;
}
.olo-ctx-label {
  padding: 4px 10px 2px;
  font-size: 10px;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-weight: 600;
}
.olo-ctx-cat-toggle {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  padding: 6px 10px;
  background: transparent;
  border: 0;
  color: #9ca3af;
  cursor: pointer;
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-weight: 600;
  font-family: inherit;
  transition: background 0.12s;
}
.olo-ctx-cat-toggle:hover {
  background: rgba(255, 255, 255, 0.05);
  color: #d1d5db;
}
.olo-ctx-cat-toggle--open {
  color: #f3f4f6;
}
.olo-ctx-cat-chevron {
  transition: transform 0.15s ease;
  opacity: 0.6;
}
.olo-ctx-cat-toggle--open .olo-ctx-cat-chevron {
  transform: rotate(180deg);
  opacity: 1;
}
.olo-ctx-layouts {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 3px;
  padding: 2px 6px 4px;
}
.olo-ctx-layout {
  display: flex;
  align-items: center;
  width: auto;
  height: 22px;
  padding: 2px;
  border: 1px solid #4b5563;
  border-radius: 3px;
  background: transparent;
  cursor: pointer;
  transition: border-color 0.15s, background 0.15s;
}
.olo-ctx-layout:hover {
  border-color: #f0833f;
  background: rgba(232, 98, 42, 0.1);
}
.olo-ctx-layout--active {
  border-color: var(--olo-ui-accent, #e8622a);
  background: rgba(232, 98, 42, 0.2);
}
.olo-ctx-layout-preview {
  display: flex;
  gap: 1px;
  width: 100%;
  height: 100%;
}
.olo-ctx-layout-col {
  background: #6b7280;
  border-radius: 1px;
  min-width: 3px;
}
.olo-ctx-layout--active .olo-ctx-layout-col {
  background: #f0833f;
}
.olo-ctx-grid-svg {
  width: 100%;
  height: 100%;
  color: #6b7280;
}
.olo-ctx-layout--active .olo-ctx-grid-svg {
  color: #f0833f;
}
</style>
