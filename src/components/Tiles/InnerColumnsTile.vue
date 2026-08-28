<template>
  <div class="olo-inner-columns-tile">
    <!-- Label bar -->
    <div class="olo-ic-label">
      <span>{{ t('Colonne interne') }}</span>
      <span class="olo-ic-layout-badge">{{ settings.layout || '50-50' }}</span>
    </div>

    <!-- Sub-columns flex container -->
    <div
      class="olo-ic-columns"
      :class="{ 'olo-ic-stack': shouldStack() }"
      :style="{
        gap: (settings.gap || 16) + 'px',
        alignItems: alignMap[settings.vertical_align] || 'stretch'
      }"
    >
      <div
        v-for="icol in innerColumns"
        :key="icol.id"
        class="olo-ic-column"
        :class="{ 'olo-ic-column--selected': builderStore.selectedTileId === icol.id }"
        :style="{ width: icol.settings?.width + '%', minWidth: 0 }"
        :data-tile-id="icol.id"
        v-olo-drop-target="innerColDrop(icol)"
        @click.stop="builderStore.selectTile(icol.id)"
      >
        <!-- Elements inside inner column — motore DnD custom (v1.4.387, ex
             vuedraggable): stesso pattern dei container annidati del canvas
             (floatingpanel in OlobuilderGrid); lo spostamento lo esegue il
             monitor globale (applyPragmaticDrop). Bonus: ora i tile si
             spostano anche tra colonna interna e canvas, non solo qui dentro. -->
        <div class="olo-ic-elements" v-olo-drop-target="listEndDrop(icol.id)">
          <template v-for="(tile, elIdx) in (icol.children || [])" :key="tile.id">
            <div v-olo-draggable="elementDraggable(tile, icol.id, elIdx)" v-olo-drop-target="elementDrop(tile, icol.id, elIdx)">
              <GridCell :tile="tile" />
            </div>
          </template>
        </div>

        <!-- Empty placeholder -->
        <div v-if="!icol.children || icol.children.length === 0" class="olo-ic-empty">
          <span class="olo-ic-plus">+</span>
        </div>
      </div>
    </div>

    <!-- Layout presets -->
    <div class="olo-ic-presets">
      <button
        v-for="preset in layoutPresets"
        :key="preset.key"
        :class="['olo-ic-preset-btn', { 'olo-ic-preset-btn--active': (settings.layout || '50-50') === preset.key }]"
        @click.stop="changeLayout(preset.key)"
      >{{ preset.label }}</button>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
import {
  vOloDraggable,
  vOloDropTarget,
  attachClosestEdge,
  makeNodePayload,
  isOloData,
} from '@/composables/useDnD';
import { useDnDStore } from '@/stores/dnd';
import { useTilesStore } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';

import GridCell from '../Grid/GridCell.vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

const tilesStore = useTilesStore();
const builderStore = useBuilderStore();
const dndStore = useDnDStore();

function shouldStack() {
  const m = builderStore.viewMode;
  const isMob = m === 'mobile' || m === 'mobile_landscape';
  const isTab = m === 'tablet' || m === 'tablet_landscape';
  if (isMob && props.settings.stack_mobile !== false) return true;
  if (isTab && props.settings.stack_tablet) return true;
  return false;
}

const alignMap = {
  stretch: 'stretch',
  start: 'flex-start',
  center: 'center',
  end: 'flex-end',
};

const layoutPresets = [
  { key: '50-50', label: '50/50' },
  { key: '33-33-33', label: '33/33/33' },
  { key: '25-75', label: '25/75' },
  { key: '75-25', label: '75/25' },
  { key: '25-50-25', label: '25/50/25' },
];

// Access the full node (with children) from the store
const innerColumns = computed(() => {
  const node = tilesStore.getTileById(props.tileId);
  return node?.children || [];
});

function changeLayout(layoutKey) {
  tilesStore.changeInnerLayout(props.tileId, layoutKey);
  builderStore.isDirty = true;
}

// ── DnD col motore custom (v1.4.387) ─────────────────────────────────
// Factory con lo stesso contratto payload del canvas (makeDraggableOpts /
// makeEdgeDrop / columnDrop / listEndDrop in OlobuilderGrid): il monitor
// globale (applyPragmaticDrop in useDragDrop) esegue lo spostamento via
// tilesStore.moveNodeTo / addChild, che risolvono qualsiasi parent per id.

// Vietato annidare tipi strutturali dentro le colonne interne.
const FORBIDDEN_TYPES = ['section', 'row', 'inner-columns'];

function canAcceptPayload(p) {
  if (p.kind === 'tile-type') return !FORBIDDEN_TYPES.includes(p.tileType);
  if (p.kind === 'global-widget') return true;
  if (p.kind === 'node' && p.nodeKind === 'element') {
    const node = tilesStore.getTileById(p.nodeId);
    return !node || !FORBIDDEN_TYPES.includes(node.type);
  }
  return false;
}

function elementDraggable(el, parentId, idx) {
  return {
    getInitialData: () => makeNodePayload(el.id, 'element', parentId, idx),
    onDragStart: () => dndStore.startDrag(makeNodePayload(el.id, 'element', parentId, idx)),
    onDrop: () => { if (!dndStore.isIdle) dndStore.endDrag(); },
  };
}

function elementDrop(el, parentId, idx) {
  return {
    canDrop: ({ source }) => {
      if (!isOloData(source.data)) return false;
      const p = source.data;
      if (p.kind === 'node') return p.nodeKind === 'element' && p.nodeId !== el.id && canAcceptPayload(p);
      return canAcceptPayload(p);
    },
    getData: ({ input, element }) => attachClosestEdge(
      { _olo: true, kind: 'node-edge', nodeKind: 'element', nodeId: el.id, parentId, index: idx },
      { element, input, allowedEdges: ['top', 'bottom'] }
    ),
    getIsSticky: () => true,
    onDragEnter: ({ self } = {}) => { if (self?.element) self.element.classList.add('olo-dnd-over'); },
    onDragLeave: ({ self } = {}) => { if (self?.element) self.element.classList.remove('olo-dnd-over'); },
    onDrop: ({ self } = {}) => { if (self?.element) self.element.classList.remove('olo-dnd-over'); },
  };
}

function innerColDrop(icol) {
  return {
    canDrop: ({ source }) => isOloData(source.data) && canAcceptPayload(source.data),
    getData: () => ({ _olo: true, kind: 'column-body', columnId: icol.id }),
    getIsSticky: () => true,
    onDragEnter: ({ self } = {}) => { if (self?.element) self.element.classList.add('olo-ic-column--dragover'); },
    onDragLeave: ({ self } = {}) => { if (self?.element) self.element.classList.remove('olo-ic-column--dragover'); },
    onDrop: ({ self } = {}) => { if (self?.element) self.element.classList.remove('olo-ic-column--dragover'); },
  };
}

function listEndDrop(parentId) {
  return {
    canDrop: ({ source }) => isOloData(source.data) && canAcceptPayload(source.data),
    getData: () => ({ _olo: true, kind: 'list-end', listKind: 'elements', parentId }),
    getIsSticky: () => false,
  };
}
</script>

<style scoped>
.olo-inner-columns-tile {
  min-height: 40px;
}

.olo-ic-label {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 4px 8px;
  font-size: 10px;
  color: var(--olo-color-text-soft, #6B7280);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.olo-ic-layout-badge {
  background: color-mix(in srgb, var(--olo-color-primary, #e1474f) 15%, transparent);
  color: var(--olo-color-primary, #e1474f);
  padding: 1px 6px;
  border-radius: 3px;
  font-size: 9px;
}

.olo-ic-columns {
  display: flex;
  padding: 6px;
  min-height: 60px;
}

.olo-ic-column {
  border: 2px dashed var(--olo-color-border, #E5E7EB);
  min-height: 60px;
  transition: border-color 0.2s, background-color 0.2s;
  position: relative;
}
.olo-ic-column--selected {
  border-color: var(--olo-color-primary, #e1474f);
}
.olo-ic-column--dragover {
  border-color: var(--olo-color-primary, #e1474f);
  background: color-mix(in srgb, var(--olo-color-primary, #e1474f) 8%, transparent);
}

.olo-ic-elements {
  min-height: 30px;
  padding: 4px;
}

.olo-ic-empty {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 60px;
  color: var(--olo-color-text-soft, #4B5563);
  font-size: 16px;
  user-select: none;
}
.olo-ic-plus {
  font-size: 18px;
  line-height: 1;
}

.olo-ic-presets {
  display: flex;
  justify-content: center;
  gap: 4px;
  padding: 4px 8px 6px;
}
.olo-ic-preset-btn {
  padding: 2px 8px;
  font-size: 10px;
  border-radius: 4px;
  border: 1px solid var(--olo-color-border, #E5E7EB);
  background: none;
  color: var(--olo-color-text-soft, #6B7280);
  cursor: pointer;
  transition: all 0.15s;
}
.olo-ic-preset-btn:hover {
  border-color: var(--olo-color-border, #E5E7EB);
  color: var(--olo-color-text-faint, #9CA3AF);
}
.olo-ic-preset-btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
.olo-ic-preset-btn--active {
  border-color: var(--olo-color-primary, #e1474f);
  color: var(--olo-color-primary, #e1474f);
  background: color-mix(in srgb, var(--olo-color-primary, #e1474f) 10%, transparent);
}
</style>
