<template>
  <Teleport to="body">
    <div
      v-if="visible"
      ref="menuRef"
      class="olo-ctx-menu"
      :style="{ left: x + 'px', top: y + 'px' }"
      @click.stop
      @contextmenu.prevent
    >
      <button @click="doCopy" class="olo-ctx-item">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
        Copia
      </button>
      <button @click="doPaste" :disabled="!tilesStore.clipboardTile" class="olo-ctx-item">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 2H9a1 1 0 0 0-1 1v2c0 .6.4 1 1 1h6c.6 0 1-.4 1-1V3c0-.6-.4-1-1-1Z"/><path d="M8 4H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-2"/></svg>
        Incolla
      </button>
      <div class="olo-ctx-sep"></div>
      <button @click="doCopyStyle" class="olo-ctx-item">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 3-1.9 12.1a1 1 0 0 1-1 .9H4.8a1 1 0 0 1-1-.8L2 3"/><path d="M6.2 15h11.7"/><path d="M13 3v12"/></svg>
        Copia stile
      </button>
      <button @click="doPasteStyle" :disabled="!tilesStore.clipboardStyle" class="olo-ctx-item">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 3-1.9 12.1a1 1 0 0 1-1 .9H4.8a1 1 0 0 1-1-.8L2 3"/><path d="M6.2 15h11.7"/><path d="M13 3v12"/></svg>
        Incolla stile
      </button>
      <div class="olo-ctx-sep"></div>
      <button @click="doDuplicate" class="olo-ctx-item">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="13" height="13" rx="2"/><rect x="9" y="9" width="13" height="13" rx="2"/></svg>
        Duplica
      </button>
      <button @click="doMoveUp" class="olo-ctx-item">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m18 15-6-6-6 6"/></svg>
        Sposta su
      </button>
      <button @click="doMoveDown" class="olo-ctx-item">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
        Sposta giù
      </button>
      <div class="olo-ctx-sep"></div>
      <button v-if="currentTileIsSection" @click="doSaveAsTemplate" class="olo-ctx-item">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        Salva sezione come template
      </button>
      <button v-if="currentTileIsSection" @click="doLoadTemplate" class="olo-ctx-item">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 15v4c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Carica template sezione
      </button>
      <button @click="doSaveGlobal" class="olo-ctx-item">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5Z"/><path d="m2 17 10 5 10-5"/><path d="m2 12 10 5 10-5"/></svg>
        Salva come globale
      </button>
      <button @click="doDetachGlobal" :disabled="!currentTileIsGlobal" class="olo-ctx-item">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
        Sgancia globale
      </button>
      <div class="olo-ctx-sep"></div>
      <button @click="doDelete" class="olo-ctx-item olo-ctx-item--danger">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
        Elimina
      </button>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { useTilesStore } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';

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

function open(event, id) {
  tileId.value = id;
  // Select the tile on right-click
  builderStore.selectTile(id);
  // Position clamped to viewport
  const mx = event.clientX;
  const my = event.clientY;
  x.value = Math.min(mx, window.innerWidth - 200);
  y.value = Math.min(my, window.innerHeight - 360);
  visible.value = true;
}

function close() {
  visible.value = false;
  tileId.value = null;
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

function doDelete() {
  if (tileId.value) {
    tilesStore.removeTile(tileId.value);
    builderStore.deselectTile();
    builderStore.isDirty = true;
  }
  close();
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
  min-width: 180px;
  background: #1f2937;
  border: 1px solid #374151;
  border-radius: 8px;
  padding: 4px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.4);
  font-family: inherit;
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
</style>
