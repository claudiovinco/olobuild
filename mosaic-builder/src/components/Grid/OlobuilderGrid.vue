<template>
  <div class="olo-canvas" :data-canvas-theme="canvasTheme">
    <!-- Sections (top-level draggable) -->
    <draggable
      v-model="tilesStore.canvasTiles"
      item-key="id"
      class="olo-sections-list"
      ghost-class="olo-ghost"
      chosen-class="olo-chosen"
      animation="200"
      handle=".olo-section-grip"
      @change="onChange"
    >
      <template #item="{ element: section }">
        <div
          class="olo-section-block"
          :class="{ 'olo-section-block--selected': builderStore.selectedTileId === section.id }"
          :data-tile-id="section.id"
          :style="{ ...getSectionColorStyle(section), ...(getNodeBg(section).type === 'solid' ? getNodeBgStyle(section) : {}), ...getNodeSpacingStyle(section) }"
        >
          <!-- Background preview layers -->
          <div
            v-if="getNodeBg(section).type === 'image' || getNodeBg(section).type === 'gradient' || getNodeBg(section).type === 'video'"
            class="olo-bg-preview"
            :style="getNodeBgStyle(section)"
          ></div>
          <div
            v-if="getOverlayStyle(section)"
            class="olo-bg-overlay"
            :style="getOverlayStyle(section)"
          ></div>

          <!-- Section bar -->
          <div class="olo-section-bar" @click.stop="selectTile(section.id)">
            <span class="olo-section-grip" title="Trascina per riordinare la sezione">&#x2630;</span>
            <span class="olo-bar-type">Sezione</span>
            <span v-if="section.settings?.style && section.settings.style !== 'default'" class="olo-bar-badge">{{ section.settings.style }}</span>
            <span v-if="hasBgImage(section)" class="olo-bar-badge olo-bar-badge--bg">BG</span>
            <span v-if="hasVideo(section)" class="olo-bar-badge olo-bar-badge--bg">VID</span>
            <span v-if="hasParallax(section)" class="olo-bar-badge olo-bar-badge--parallax">&#x21C5;</span>
            <span class="olo-bar-spacer"></span>
            <button class="olo-bar-btn" title="Duplica" @click.stop="duplicateItem(section.id)">&#x2398;</button>
            <button class="olo-bar-btn olo-bar-btn--delete" title="Elimina" @click.stop="removeItem(section.id)">&#x2715;</button>
          </div>

          <!-- Section body: rows -->
          <div class="olo-section-body">
            <draggable
              :list="section.children"
              item-key="id"
              ghost-class="olo-ghost"
              animation="150"
              handle=".olo-row-grip"
              :group="{ name: 'rows' }"
              @change="onChange"
            >
              <template #item="{ element: row }">
                <div
                  class="olo-row-block"
                  :class="{ 'olo-row-block--selected': builderStore.selectedTileId === row.id }"
                  :data-tile-id="row.id"
                  :style="{ ...(getNodeBg(row).type === 'solid' ? getNodeBgStyle(row) : {}), ...getNodeSpacingStyle(row) }"
                >
                  <!-- Background preview layers -->
                  <div
                    v-if="getNodeBg(row).type === 'image' || getNodeBg(row).type === 'gradient' || getNodeBg(row).type === 'video'"
                    class="olo-bg-preview"
                    :style="getNodeBgStyle(row)"
                  ></div>
                  <div
                    v-if="getOverlayStyle(row)"
                    class="olo-bg-overlay"
                    :style="getOverlayStyle(row)"
                  ></div>

                  <!-- Row bar -->
                  <div class="olo-row-bar" @click.stop="selectTile(row.id)">
                    <span class="olo-row-grip" title="Trascina per riordinare la riga">&#x2630;</span>
                    <span class="olo-bar-type">Riga</span>
                    <span class="olo-bar-badge">{{ row.settings?.layout === 'custom' ? (row.settings?.custom_widths || '%') : (row.settings?.layout || '50-50') }}</span>
                    <span v-if="hasBgImage(row)" class="olo-bar-badge olo-bar-badge--bg">BG</span>
                    <span v-if="hasVideo(row)" class="olo-bar-badge olo-bar-badge--bg">VID</span>
                    <span v-if="hasParallax(row)" class="olo-bar-badge olo-bar-badge--parallax">&#x21C5;</span>
                    <span class="olo-bar-spacer"></span>
                    <button class="olo-bar-btn" title="Duplica" @click.stop="duplicateItem(row.id)">&#x2398;</button>
                    <button class="olo-bar-btn olo-bar-btn--delete" title="Elimina" @click.stop="removeItem(row.id)">&#x2715;</button>
                  </div>

                  <!-- Columns flex layout -->
                  <div
                    class="olo-row-columns"
                    :style="{
                      gap: (row.settings?.gap || 16) + 'px',
                      alignItems: alignMap[row.settings?.vertical_align] || 'stretch'
                    }"
                  >
                    <div
                      v-for="col in (row.children || [])"
                      :key="col.id"
                      class="olo-column-block"
                      :class="{
                        'olo-column-block--selected': builderStore.selectedTileId === col.id,
                        'olo-column-block--dragover': dragOverColId === col.id
                      }"
                      :data-tile-id="col.id"
                      :style="{ width: getColPercent(col) + '%', minWidth: 0, ...getNodeSpacingStyle(col) }"
                      @click.stop="selectTile(col.id)"
                      @dragover.prevent.stop="dragOverColId = col.id"
                      @dragleave="onColDragLeave($event, col.id)"
                      @drop.prevent.stop="onDropIntoColumn($event, col.id)"
                    >
                      <!-- Elements inside column -->
                      <draggable
                        :list="col.children"
                        item-key="id"
                        ghost-class="olo-ghost"
                        animation="150"
                        :group="{ name: 'elements' }"
                        class="olo-column-elements"
                        @change="onChange"
                      >
                        <template #item="{ element: tile }">
                          <GridCell :tile="tile" />
                        </template>
                      </draggable>

                      <!-- Empty column placeholder -->
                      <div v-if="!col.children || col.children.length === 0" class="olo-column-empty">
                        <span class="olo-column-plus">+</span>
                        <span>Rilascia qui</span>
                      </div>
                    </div>
                  </div>

                  <!-- Layout presets -->
                  <div class="olo-row-presets">
                    <button
                      v-for="preset in layoutPresets"
                      :key="preset.key"
                      @click.stop="changeRowLayout(row, preset.key)"
                      :class="['olo-preset-btn', { 'olo-preset-btn--active': (row.settings?.layout || '50-50') === preset.key }]"
                    >{{ preset.label }}</button>
                  </div>
                  <!-- Custom widths input -->
                  <div
                    v-if="row.settings?.layout === 'custom' || customEditingRowId === row.id"
                    class="olo-custom-input-row"
                  >
                    <input
                      v-model="customInputValue"
                      class="olo-custom-input"
                      type="text"
                      placeholder="es: 20,30,50"
                      @keydown.enter="confirmCustomInput(row)"
                      @blur="confirmCustomInput(row)"
                      @focus="customInputValue = customInputValue || (row.settings?.custom_widths || '')"
                      @click.stop
                    />
                  </div>
                </div>
              </template>
            </draggable>

            <!-- Add row zone -->
            <div
              class="olo-add-row"
              @dragover.prevent
              @drop.prevent="onDropIntoSection($event, section)"
            >
              <button @click="addRowToSection(section)">+ Aggiungi riga</button>
            </div>
          </div>
        </div>
      </template>
    </draggable>

    <!-- Empty canvas state -->
    <div
      v-if="tilesStore.canvasTiles.length === 0"
      class="olo-canvas-empty"
      @dragover.prevent
      @drop.prevent="onDropCanvas"
    >
      <div style="font-size: 32px; margin-bottom: 8px;">&#x1F4D0;</div>
      <div>Trascina una tile dalla barra laterale per iniziare</div>
    </div>

    <!-- Bottom drop zone (when canvas has content) -->
    <div
      v-else
      class="olo-canvas-bottom-drop"
      @dragover.prevent
      @drop.prevent="onDropCanvas"
    >
      <span>+ Rilascia qui per aggiungere una sezione</span>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import draggable from 'vuedraggable';
import { useTilesStore, createRow, createColumn } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';
import { useStylesStore } from '@/stores/styles';
import { useDragDrop } from '@/composables/useDragDrop';
import GridCell from './GridCell.vue';

const tilesStore = useTilesStore();
const builderStore = useBuilderStore();
const stylesStore = useStylesStore();
const { handleDropFromSidebar, handleDropIntoColumn, createTileFromType } = useDragDrop();

function getSectionColorStyle(section) {
  const sectionType = section.settings?.style || 'default';
  const colors = stylesStore.colors;
  const map = {
    primary:   { backgroundColor: colors.primary, color: colors.primary_contrast },
    secondary: { backgroundColor: colors.secondary, color: colors.secondary_contrast },
    muted:     { backgroundColor: colors.muted, color: colors.muted_contrast },
  };
  return map[sectionType] || {};
}

function getNodeBg(node) {
  const s = node.style || {};
  if (s.bg && s.bg.type && s.bg.type !== 'none') return s.bg;
  if (s.bg_color) return { type: 'solid', color: s.bg_color };
  return { type: 'none' };
}

function getNodeBgStyle(node) {
  const bg = getNodeBg(node);
  if (bg.type === 'solid') return { backgroundColor: bg.color };
  if (bg.type === 'gradient') return { background: `linear-gradient(${bg.gradient_angle || 180}deg, ${bg.gradient_from || '#fff'}, ${bg.gradient_to || '#000'})` };
  if (bg.type === 'image' && bg.image_url) return { backgroundImage: `url(${bg.image_url})`, backgroundSize: bg.image_size || 'cover', backgroundPosition: bg.image_position || 'center center', backgroundRepeat: 'no-repeat' };
  if (bg.type === 'video' && bg.video_poster) return { backgroundImage: `url(${bg.video_poster})`, backgroundSize: 'cover', backgroundPosition: bg.image_position || 'center center', backgroundRepeat: 'no-repeat' };
  if (bg.type === 'video') return { backgroundColor: '#1a1a2e' };
  return {};
}

function hasBgImage(node) {
  const bg = getNodeBg(node);
  return (bg.type === 'image' && !!bg.image_url) || (bg.type === 'video' && !!bg.video_url);
}

function hasVideo(node) {
  const bg = getNodeBg(node);
  return bg.type === 'video' && !!bg.video_url;
}

function hasParallax(node) {
  const bg = getNodeBg(node);
  // Background parallax: boolean true (legacy) or object (new multi-stop)
  const bgParallax = bg.type === 'image' && (bg.parallax === true || (typeof bg.parallax === 'object' && bg.parallax !== null));
  // Element parallax: new format in advanced.parallax object
  const adv = node.advanced || {};
  const elParallax = typeof adv.parallax === 'object' && adv.parallax !== null;
  return bgParallax || elParallax;
}

function getOverlayStyle(node) {
  const bg = getNodeBg(node);
  if (bg.type === 'none' || !bg.overlay_opacity || parseInt(bg.overlay_opacity) <= 0) return null;
  return { backgroundColor: bg.overlay_color || '#000000', opacity: parseInt(bg.overlay_opacity) / 100 };
}

function getNodeSpacingStyle(node) {
  const s = node.style || {};
  const st = {};
  if (s.margin_top)    st.marginTop    = `${s.margin_top}px`;
  if (s.margin_right)  st.marginRight  = `${s.margin_right}px`;
  if (s.margin_bottom) st.marginBottom = `${s.margin_bottom}px`;
  if (s.margin_left)   st.marginLeft   = `${s.margin_left}px`;
  if (s.padding_top)    st.paddingTop    = `${s.padding_top}px`;
  if (s.padding_right)  st.paddingRight  = `${s.padding_right}px`;
  if (s.padding_bottom) st.paddingBottom = `${s.padding_bottom}px`;
  if (s.padding_left)   st.paddingLeft   = `${s.padding_left}px`;
  return st;
}

const dragOverColId = ref(null);

// --- Custom layout editing state ---
const customEditingRowId = ref(null);
const customInputValue = ref('');

function startCustomEdit(row) {
  customEditingRowId.value = row.id;
  customInputValue.value = row.settings?.custom_widths || '';
}

function confirmCustomInput(row) {
  if (customInputValue.value.trim()) {
    const result = tilesStore.applyCustomWidths(row.id, customInputValue.value);
    if (result) customInputValue.value = result;
    builderStore.isDirty = true;
  }
  customEditingRowId.value = null;
}

function isLightColor(hex) {
  const c = hex.replace('#', '');
  const r = parseInt(c.substr(0, 2), 16);
  const g = parseInt(c.substr(2, 2), 16);
  const b = parseInt(c.substr(4, 2), 16);
  return (r * 299 + g * 587 + b * 114) / 1000 > 128;
}

const canvasTheme = computed(() => {
  const tc = window.oloData?.themeColors || {};
  const bg = tc.background || '#ffffff';
  return isLightColor(bg) ? 'light' : 'dark';
});

// --- Layout data ---

const layoutPresets = [
  { key: '100', label: '100' },
  { key: '50-50', label: '50/50' },
  { key: '33-33-33', label: '33/33/33' },
  { key: '25-50-25', label: '25/50/25' },
  { key: '25-25-25-25', label: '25x4' },
  { key: '66-33', label: '66/33' },
  { key: '33-66', label: '33/66' },
  { key: 'custom', label: '%' },
];

const fractionToPercent = {
  '1-1': 100, '1-2': 50, '1-3': 33.33, '2-3': 66.66,
  '1-4': 25, '3-4': 75, '1-5': 20, '2-5': 40,
  '3-5': 60, '4-5': 80, '1-6': 16.66, '5-6': 83.33,
};

const alignMap = {
  stretch: 'stretch',
  start: 'flex-start',
  center: 'center',
  end: 'flex-end',
};

const layoutColWidths = {
  '100': ['1-1'],
  '50-50': ['1-2', '1-2'],
  '33-33-33': ['1-3', '1-3', '1-3'],
  '25-50-25': ['1-4', '1-2', '1-4'],
  '25-25-25-25': ['1-4', '1-4', '1-4', '1-4'],
  '66-33': ['2-3', '1-3'],
  '33-66': ['1-3', '2-3'],
};

// --- Helpers ---

function getColPercent(col) {
  if (col.settings?.width_custom != null && col.settings.width_custom !== '') {
    return parseFloat(col.settings.width_custom) || 50;
  }
  const w = col.settings?.width_medium || '1-2';
  return fractionToPercent[w] || 50;
}

function selectTile(id) {
  builderStore.selectTile(id);
}

function duplicateItem(id) {
  tilesStore.duplicateTile(id);
  builderStore.isDirty = true;
}

function removeItem(id) {
  tilesStore.removeTile(id);
  if (builderStore.selectedTileId === id) builderStore.deselectTile();
  builderStore.isDirty = true;
}

function onChange() {
  builderStore.isDirty = true;
}

// --- Column drag from sidebar ---

function onColDragLeave(event, colId) {
  if (!event.currentTarget.contains(event.relatedTarget)) {
    if (dragOverColId.value === colId) dragOverColId.value = null;
  }
}

function onDropIntoColumn(event, colId) {
  dragOverColId.value = null;
  const tileType = event.dataTransfer.getData('tile-type');
  if (!tileType) return;
  handleDropIntoColumn(tileType, colId);
}

// --- Drop on canvas (creates new section) ---

function onDropCanvas(event) {
  const tileType = event.dataTransfer.getData('tile-type');
  if (!tileType) return;
  handleDropFromSidebar(tileType);
}

// --- Drop into section (adds row or element) ---

function onDropIntoSection(event, section) {
  const tileType = event.dataTransfer.getData('tile-type');
  if (!tileType || tileType === 'section') return;

  if (tileType === 'row') {
    addRowToSection(section);
  } else {
    const newTile = createTileFromType(tileType);
    if (!newTile) return;
    const col = createColumn('1-1', [newTile]);
    const row = createRow('100', [col]);
    if (!Array.isArray(section.children)) section.children = [];
    section.children.push(row);
    builderStore.isDirty = true;
    builderStore.selectTile(newTile.id);
  }
}

// --- Add empty row to section ---

function addRowToSection(section) {
  const col1 = createColumn('1-2', []);
  const col2 = createColumn('1-2', []);
  const row = createRow('50-50', [col1, col2]);
  if (!Array.isArray(section.children)) section.children = [];
  section.children.push(row);
  builderStore.isDirty = true;
  builderStore.selectTile(row.id);
}

// --- Change row layout ---

function changeRowLayout(row, layoutKey) {
  if (layoutKey === 'custom') {
    tilesStore.changeRowLayout(row.id, 'custom');
    startCustomEdit(row);
    builderStore.isDirty = true;
    return;
  }

  tilesStore.changeRowLayout(row.id, layoutKey);
  customEditingRowId.value = null;
  builderStore.isDirty = true;
}
</script>

<style scoped>
.olo-canvas {
  padding: 16px;
}

.olo-sections-list {
  min-height: 100px;
}

/* === Section block === */
.olo-section-block {
  margin-bottom: 12px;
  border: 1px solid #374151;
  background: rgba(17, 24, 39, 0.5);
  overflow: hidden;
  position: relative;
}

/* === Background preview layers === */
.olo-bg-preview {
  position: absolute;
  inset: 0;
  z-index: 0;
  pointer-events: none;
  border-radius: inherit;
  opacity: 0.6;
}
.olo-bg-overlay {
  position: absolute;
  inset: 0;
  z-index: 1;
  pointer-events: none;
  border-radius: inherit;
}
.olo-section-bar,
.olo-section-body {
  position: relative;
  z-index: 2;
}
.olo-row-bar,
.olo-row-columns,
.olo-row-presets {
  position: relative;
  z-index: 2;
}
.olo-section-block--selected {
  border-color: var(--olo-color-primary, #6366F1);
}

/* === Bars (section & row headers) === */
.olo-section-bar,
.olo-row-bar {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 4px 8px;
  font-size: 10px;
  color: #9CA3AF;
  cursor: pointer;
  user-select: none;
}
.olo-section-bar {
  background: rgb(var(--olo-primary-rgb, 99 102 241) / 0.06);
  border-bottom: 1px solid #374151;
}
.olo-section-bar:hover,
.olo-row-bar:hover {
  background: rgb(var(--olo-primary-rgb, 99 102 241) / 0.1);
}
.olo-section-block--selected > .olo-section-bar {
  background: rgb(var(--olo-primary-rgb, 99 102 241) / 0.15);
}

.olo-section-grip,
.olo-row-grip {
  cursor: grab;
  font-size: 12px;
  opacity: 0.4;
  transition: opacity 0.15s;
}
.olo-section-bar:hover .olo-section-grip,
.olo-row-bar:hover .olo-row-grip {
  opacity: 0.8;
}

.olo-bar-type {
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.olo-bar-badge {
  background: rgb(var(--olo-primary-rgb, 99 102 241) / 0.15);
  color: var(--olo-color-primary, #6366F1);
  padding: 1px 6px;
  border-radius: 3px;
  font-size: 9px;
}
.olo-bar-badge--bg {
  background: rgba(16, 185, 129, 0.15);
  color: #34D399;
}
.olo-bar-badge--parallax {
  background: rgba(245, 158, 11, 0.15);
  color: #FBBF24;
}
.olo-bar-spacer {
  flex: 1;
}
.olo-bar-btn {
  background: none;
  border: none;
  color: #6B7280;
  cursor: pointer;
  font-size: 12px;
  padding: 2px 4px;
  border-radius: 3px;
  opacity: 0;
  transition: opacity 0.15s, color 0.15s;
}
.olo-section-bar:hover .olo-bar-btn,
.olo-row-bar:hover .olo-bar-btn {
  opacity: 1;
}
.olo-bar-btn:hover {
  color: #D1D5DB;
  background: rgba(255, 255, 255, 0.08);
}
.olo-bar-btn--delete:hover {
  color: #EF4444;
}

/* === Section body === */
.olo-section-body {
  padding: 8px;
}

/* === Row block === */
.olo-row-block {
  margin-bottom: 8px;
  border: 1px solid #2D3748;
  background: rgba(31, 41, 55, 0.3);
  overflow: hidden;
  position: relative;
}
.olo-row-block--selected {
  border-color: var(--olo-color-primary, #6366F1);
}
.olo-row-bar {
  border-bottom: 1px solid #2D3748;
}

/* === Columns layout === */
.olo-row-columns {
  display: flex;
  padding: 8px;
  min-height: 80px;
}

.olo-column-block {
  border: 2px dashed #374151;
  min-height: 80px;
  transition: border-color 0.2s, background-color 0.2s;
  position: relative;
}
.olo-column-block--selected {
  border-color: var(--olo-color-primary, #6366F1);
}
.olo-column-block--dragover {
  border-color: var(--olo-color-primary, #6366F1);
  background: rgb(var(--olo-primary-rgb, 99 102 241) / 0.08);
}

.olo-column-elements {
  min-height: 40px;
  padding: 4px;
}

.olo-column-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 80px;
  color: #4B5563;
  font-size: 11px;
  gap: 2px;
  user-select: none;
}
.olo-column-plus {
  font-size: 18px;
  line-height: 1;
}

/* === Layout presets === */
.olo-row-presets {
  display: flex;
  justify-content: center;
  gap: 4px;
  padding: 4px 8px 8px;
}
.olo-preset-btn {
  padding: 2px 8px;
  font-size: 10px;
  border-radius: 4px;
  border: 1px solid #374151;
  background: none;
  color: #6B7280;
  cursor: pointer;
  transition: all 0.15s;
}
.olo-preset-btn:hover {
  border-color: #4B5563;
  color: #9CA3AF;
}
.olo-preset-btn--active {
  border-color: var(--olo-color-primary, #6366F1);
  color: var(--olo-color-primary, #6366F1);
  background: rgb(var(--olo-primary-rgb, 99 102 241) / 0.1);
}

/* === Add row === */
.olo-add-row {
  display: flex;
  justify-content: center;
  padding: 4px 0;
}
.olo-add-row button {
  background: none;
  border: 1px dashed #374151;
  color: #4B5563;
  padding: 4px 16px;
  border-radius: 4px;
  font-size: 11px;
  cursor: pointer;
  transition: all 0.15s;
}
.olo-add-row button:hover {
  border-color: var(--olo-color-primary, #6366F1);
  color: var(--olo-color-primary, #6366F1);
}

/* === Empty canvas === */
.olo-canvas-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 300px;
  color: #4B5563;
  font-size: 13px;
  border: 2px dashed #374151;
}

/* === Bottom drop zone === */
.olo-canvas-bottom-drop {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 12px;
  margin-top: 4px;
  border: 2px dashed #2D3748;
  color: #4B5563;
  font-size: 11px;
  transition: all 0.2s;
  cursor: default;
}
.olo-canvas-bottom-drop:hover {
  border-color: var(--olo-color-primary, #6366F1);
  color: var(--olo-color-primary, #6366F1);
}

/* === Drag states === */
.olo-ghost {
  opacity: 0.3;
}
.olo-chosen {
  opacity: 0.8;
}

/* ========================================
   Light canvas theme overrides
   ======================================== */
.olo-canvas[data-canvas-theme="light"] .olo-section-block {
  border-color: #e5e7eb;
  background: rgb(var(--olo-primary-rgb, 99 102 241) / 0.03);
}
.olo-canvas[data-canvas-theme="light"] .olo-section-bar {
  border-bottom-color: #e5e7eb;
  color: #6b7280;
}
.olo-canvas[data-canvas-theme="light"] .olo-row-block {
  border-color: #d1d5db;
  background: rgb(var(--olo-primary-rgb, 99 102 241) / 0.02);
}
.olo-canvas[data-canvas-theme="light"] .olo-row-bar {
  border-bottom-color: #d1d5db;
  color: #6b7280;
}
.olo-canvas[data-canvas-theme="light"] .olo-column-block {
  border-color: #d1d5db;
}
.olo-canvas[data-canvas-theme="light"] .olo-column-empty {
  color: #9ca3af;
}
.olo-canvas[data-canvas-theme="light"] .olo-preset-btn {
  border-color: #d1d5db;
  color: #9ca3af;
}
.olo-canvas[data-canvas-theme="light"] .olo-preset-btn:hover {
  border-color: #9ca3af;
  color: #6b7280;
}
.olo-canvas[data-canvas-theme="light"] .olo-add-row button {
  border-color: #d1d5db;
  color: #9ca3af;
}
.olo-canvas[data-canvas-theme="light"] .olo-canvas-empty {
  color: #9ca3af;
  border-color: #d1d5db;
}
.olo-canvas[data-canvas-theme="light"] .olo-canvas-bottom-drop {
  border-color: #d1d5db;
  color: #9ca3af;
}
.olo-canvas[data-canvas-theme="light"] .olo-bar-btn {
  color: #9ca3af;
}
.olo-canvas[data-canvas-theme="light"] .olo-bar-btn:hover {
  color: #374151;
  background: rgba(0, 0, 0, 0.05);
}

/* === Custom width input === */
.olo-custom-input-row {
  display: flex;
  justify-content: center;
  padding: 0 8px 8px;
}
.olo-custom-input {
  width: 200px;
  padding: 3px 8px;
  font-size: 11px;
  font-family: monospace;
  border: 1px solid #4B5563;
  border-radius: 4px;
  background: rgba(0, 0, 0, 0.3);
  color: #E5E7EB;
  text-align: center;
  outline: none;
  transition: border-color 0.15s;
}
.olo-custom-input:focus {
  border-color: var(--olo-color-primary, #6366F1);
}
.olo-custom-input::placeholder {
  color: #6B7280;
}
.olo-canvas[data-canvas-theme="light"] .olo-custom-input {
  background: rgba(0, 0, 0, 0.05);
  border-color: #d1d5db;
  color: #374151;
}
</style>
