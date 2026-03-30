<template>
  <div class="olo-inner-columns-tile">
    <!-- Label bar -->
    <div class="olo-ic-label">
      <span>Colonne interne</span>
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
        :class="{
          'olo-ic-column--selected': builderStore.selectedTileId === icol.id,
          'olo-ic-column--dragover': dragOverColId === icol.id
        }"
        :style="{ width: icol.settings?.width + '%', minWidth: 0 }"
        :data-tile-id="icol.id"
        @click.stop="builderStore.selectTile(icol.id)"
        @dragover.prevent.stop="dragOverColId = icol.id"
        @dragleave="onColDragLeave($event, icol.id)"
        @drop.prevent.stop="onDropIntoInnerCol($event, icol.id)"
      >
        <!-- Elements inside inner column -->
        <draggable
          :list="icol.children"
          item-key="id"
          ghost-class="olo-ghost"
          animation="150"
          :group="{ name: 'elements' }"
          class="olo-ic-elements"
          @change="onChange"
        >
          <template #item="{ element: tile }">
            <GridCell :tile="tile" />
          </template>
        </draggable>

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
import { computed, ref } from 'vue';
import draggable from 'vuedraggable';
import { useTilesStore } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';
import { useDragDrop } from '@/composables/useDragDrop';

import GridCell from '../Grid/GridCell.vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

const tilesStore = useTilesStore();
const builderStore = useBuilderStore();
const { handleDropIntoColumn } = useDragDrop();

function shouldStack() {
  const m = builderStore.viewMode;
  const isMob = m === 'mobile' || m === 'mobile_landscape';
  const isTab = m === 'tablet' || m === 'tablet_landscape';
  if (isMob && props.settings.stack_mobile !== false) return true;
  if (isTab && props.settings.stack_tablet) return true;
  return false;
}

const dragOverColId = ref(null);

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

function onColDragLeave(event, colId) {
  if (!event.currentTarget.contains(event.relatedTarget)) {
    if (dragOverColId.value === colId) dragOverColId.value = null;
  }
}

function onDropIntoInnerCol(event, colId) {
  dragOverColId.value = null;
  const tileType = event.dataTransfer.getData('tile-type');
  if (!tileType) return;
  // Prevent nesting structural types inside inner columns
  if (tileType === 'row' || tileType === 'section' || tileType === 'inner-columns') return;
  handleDropIntoColumn(tileType, colId);
}

function onChange() {
  builderStore.isDirty = true;
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
  color: #6B7280;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.olo-ic-layout-badge {
  background: rgba(99, 102, 241, 0.15);
  color: var(--olo-color-primary, #6366F1);
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
  border-color: var(--olo-color-primary, #6366F1);
}
.olo-ic-column--dragover {
  border-color: var(--olo-color-primary, #6366F1);
  background: rgb(var(--olo-primary-rgb, 99 102 241) / 0.08);
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
  color: #4B5563;
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
  color: #6B7280;
  cursor: pointer;
  transition: all 0.15s;
}
.olo-ic-preset-btn:hover {
  border-color: var(--olo-color-border, #E5E7EB);
  color: #9CA3AF;
}
.olo-ic-preset-btn--active {
  border-color: var(--olo-color-primary, #6366F1);
  color: var(--olo-color-primary, #6366F1);
  background: rgb(var(--olo-primary-rgb, 99 102 241) / 0.1);
}

.olo-ghost {
  opacity: 0.3;
}
</style>
