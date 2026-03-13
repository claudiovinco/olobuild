<template>
  <div ref="canvasRef" class="olo-canvas" :data-canvas-theme="canvasTheme" style="position: relative">
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
          :class="{ 'olo-section-block--selected': builderStore.selectedTileId === section.id, 'olo-node-hidden-vp': isHiddenInViewport(section) }"
          :data-tile-id="section.id"
          :style="{ ...getSectionColorStyle(section), ...(getNodeBg(section).type === 'solid' ? getNodeBgStyle(section) : {}), ...getNodeSpacingStyle(section) }"
          @contextmenu.prevent="onTileContextMenu($event, section.id)"
        >
          <!-- Background preview layers -->
          <div
            v-if="getNodeBg(section).type === 'image' || getNodeBg(section).type === 'gradient'"
            class="olo-bg-preview"
            :style="getNodeBgStyle(section)"
          ></div>
          <video
            v-if="getNodeBg(section).type === 'video' && getNodeBg(section).video_url"
            class="olo-bg-video"
            :style="{ objectFit: getNodeBg(section).video_fit || 'cover', objectPosition: getNodeBg(section).image_position || 'center center' }"
            :src="getNodeBg(section).video_url"
            :poster="getNodeBg(section).video_poster || undefined"
            muted autoplay loop playsinline
          ></video>
          <div
            v-if="getOverlayStyle(section)"
            class="olo-bg-overlay"
            :style="getOverlayStyle(section)"
          ></div>

          <!-- Shape divider overlays (rendered at section level for proper overlap) -->
          <div
            v-for="sd in getShapeDividers(section)"
            :key="'sd-' + sd.id"
            class="olo-shapedivider-overlay"
            :class="{
              'olo-shapedivider-overlay--top': (sd.settings?.position || 'bottom') === 'top',
              'olo-shapedivider-overlay--bottom': (sd.settings?.position || 'bottom') !== 'top',
              'olo-grid-cell--selected': builderStore.selectedTileId === sd.id,
            }"
            :data-tile-id="sd.id"
            @click.stop="selectTile(sd.id)"
            @contextmenu.prevent="onTileContextMenu($event, sd.id)"
          >
            <ShapedividerTile :settings="sd.settings" :overlay-mode="true" />
          </div>

          <!-- Section bar -->
          <div class="olo-section-bar" @click.stop="selectTile(section.id)" @contextmenu.prevent="onTileContextMenu($event, section.id)">
            <span class="olo-section-grip" title="Trascina per riordinare la sezione">&#x2630;</span>
            <span class="olo-bar-type">Sezione</span>
            <span v-if="section.settings?.style && section.settings.style !== 'default'" class="olo-bar-badge">{{ section.settings.style }}</span>
            <span v-if="hasBgImage(section)" class="olo-bar-badge olo-bar-badge--bg">BG</span>
            <span v-if="hasVideo(section)" class="olo-bar-badge olo-bar-badge--bg">VID</span>
            <span v-if="hasParallax(section)" class="olo-bar-badge olo-bar-badge--parallax">&#x21C5;</span>
            <span v-if="section.settings?.sticky_effect && section.settings.sticky_effect !== 'none'" class="olo-bar-badge olo-bar-badge--sticky">{{ section.settings.sticky_effect.toUpperCase() }}</span>
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
                  :class="{ 'olo-row-block--selected': builderStore.selectedTileId === row.id, 'olo-node-hidden-vp': isHiddenInViewport(row) }"
                  :data-tile-id="row.id"
                  :style="{ ...(getNodeBg(row).type === 'solid' ? getNodeBgStyle(row) : {}), ...getNodeSpacingStyle(row) }"
                >
                  <!-- Background preview layers -->
                  <div
                    v-if="getNodeBg(row).type === 'image' || getNodeBg(row).type === 'gradient'"
                    class="olo-bg-preview"
                    :style="getNodeBgStyle(row)"
                  ></div>
                  <video
                    v-if="getNodeBg(row).type === 'video' && getNodeBg(row).video_url"
                    class="olo-bg-video"
                    :style="{ objectFit: getNodeBg(row).video_fit || 'cover', objectPosition: getNodeBg(row).image_position || 'center center' }"
                    :src="getNodeBg(row).video_url"
                    :poster="getNodeBg(row).video_poster || undefined"
                    muted autoplay loop playsinline
                  ></video>
                  <div
                    v-if="getOverlayStyle(row)"
                    class="olo-bg-overlay"
                    :style="getOverlayStyle(row)"
                  ></div>

                  <!-- Row bar -->
                  <div class="olo-row-bar" @click.stop="selectTile(row.id)" @contextmenu.prevent="onTileContextMenu($event, row.id)">
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
                    :class="{ 'olo-row-stack': shouldStack(row.settings) }"
                    :style="{
                      gap: rv(row.settings || {}, 'gap', 16, builderStore.viewMode) + 'px',
                      alignItems: alignMap[row.settings?.vertical_align] || 'stretch',
                      flexDirection: (shouldStack(row.settings)) ? 'column' : (row.settings?.flex_direction || undefined),
                      justifyContent: row.settings?.flex_justify || undefined,
                      flexWrap: (shouldStack(row.settings)) ? 'wrap' : (row.settings?.flex_wrap || undefined),
                    }"
                  >
                    <template v-for="(col, colIdx) in (row.children || [])" :key="col.id">
                      <!-- Resize handle between columns -->
                      <div
                        v-if="colIdx > 0"
                        class="olo-col-resize-handle"
                        @mousedown.stop.prevent="startColResize($event, row, colIdx)"
                        title="Trascina per ridimensionare"
                      >
                        <span class="olo-col-resize-grip"></span>
                      </div>
                      <div
                        class="olo-column-block"
                        :class="{
                          'olo-column-block--selected': builderStore.selectedTileId === col.id,
                          'olo-column-block--dragover': dragOverColId === col.id
                        }"
                        :data-tile-id="col.id"
                        :style="{ width: (shouldStack(row.settings)) ? '100%' : getColPercent(col) + '%', minWidth: 0, ...getNodeSpacingStyle(col) }"
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
                            <GridCell :tile="tile" @contextmenu="onTileContextMenu" />
                          </template>
                        </draggable>

                        <!-- Empty column placeholder -->
                        <div v-if="!col.children || col.children.length === 0" class="olo-column-empty" @click.stop="openFinder(col.id)" style="cursor:pointer">
                          <span class="olo-column-plus">+</span>
                          <span>Rilascia qui</span>
                        </div>
                      </div>
                    </template>
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
              @drop.prevent.stop="onDropIntoSection($event, section)"
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
      @drop.prevent.stop="onDropCanvas"
      @click.stop="openFinder()"
      style="cursor:pointer"
    >
      <div style="font-size: 32px; margin-bottom: 8px;">&#x1F4D0;</div>
      <div>Trascina o clicca per aggiungere una tile</div>
    </div>

    <!-- Bottom drop zone (when canvas has content) -->
    <div
      v-else
      class="olo-canvas-bottom-drop"
      @dragover.prevent
      @drop.prevent.stop="onDropCanvas"
      @click.stop="openFinder()"
      style="cursor:pointer"
    >
      <span>+ Rilascia qui o clicca per aggiungere</span>
    </div>

    <!-- Context menu -->
    <ContextMenu ref="contextMenuRef" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, inject } from 'vue';
import draggable from 'vuedraggable';
import { useTilesStore, createRow, createColumn } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';
import { useStylesStore } from '@/stores/styles';
import { useDragDrop } from '@/composables/useDragDrop';
import { resolveNodeBg, buildBgStyle, buildOverlayStyle } from '@/composables/useBackgroundStyle';
import { rv } from '@/composables/useResponsiveValue';
import GridCell from './GridCell.vue';
import ShapedividerTile from '@/components/Tiles/ShapedividerTile.vue';
import ContextMenu from '@/components/Builder/ContextMenu.vue';

const tilesStore = useTilesStore();
const builderStore = useBuilderStore();
const stylesStore = useStylesStore();

const isMobileView = computed(() => {
  const m = builderStore.viewMode;
  return m === 'mobile' || m === 'mobile_landscape' || m === 'tablet' || m === 'tablet_landscape';
});

function shouldStack(rowSettings) {
  const m = builderStore.viewMode;
  const isMob = m === 'mobile' || m === 'mobile_landscape';
  const isTab = m === 'tablet' || m === 'tablet_landscape';
  if (isMob && rowSettings?.stack_mobile !== false) return true;
  if (isTab && rowSettings?.stack_tablet) return true;
  return false;
}
const openFinder = inject('openFinder', () => {});
const { handleDropFromSidebar, handleDropIntoColumn, handleGlobalWidgetDrop, handleGlobalWidgetDropIntoColumn, createTileFromType } = useDragDrop();

/**
 * Extract shapedivider tiles from section tree for absolute overlay rendering.
 */
function getShapeDividers(section) {
  const result = [];
  function walk(node) {
    if (!node) return;
    if (node.type === 'shapedivider') result.push(node);
    if (Array.isArray(node.children)) node.children.forEach(walk);
  }
  if (Array.isArray(section.children)) section.children.forEach(walk);
  return result;
}

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

// Background helpers — delegano al composable condiviso
function getNodeBg(node) {
  return resolveNodeBg(node);
}

function getNodeBgStyle(node) {
  return buildBgStyle(getNodeBg(node));
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
  return buildOverlayStyle(getNodeBg(node));
}

function getNodeSpacingStyle(node) {
  const s = node.style || {};
  const mode = builderStore.viewMode;
  const st = {};
  const mt = rv(s, 'margin_top', undefined, mode);
  const mr = rv(s, 'margin_right', undefined, mode);
  const mb = rv(s, 'margin_bottom', undefined, mode);
  const ml = rv(s, 'margin_left', undefined, mode);
  if (mt) st.marginTop    = `${mt}px`;
  if (mr) st.marginRight  = `${mr}px`;
  if (mb) st.marginBottom = `${mb}px`;
  if (ml) st.marginLeft   = `${ml}px`;
  const pt = rv(s, 'padding_top', undefined, mode);
  const pr = rv(s, 'padding_right', undefined, mode);
  const pb = rv(s, 'padding_bottom', undefined, mode);
  const pl = rv(s, 'padding_left', undefined, mode);
  if (pt) st.paddingTop    = `${pt}px`;
  if (pr) st.paddingRight  = `${pr}px`;
  if (pb) st.paddingBottom = `${pb}px`;
  if (pl) st.paddingLeft   = `${pl}px`;
  return st;
}

// Responsive visibility helper
function isHiddenInViewport(node) {
  const adv = node.advanced || {};
  const mode = builderStore.viewMode;
  if (mode === 'desktop' && adv.visible_desktop === false) return true;
  if (mode === 'tablet_landscape' && adv.visible_tablet_landscape === false) return true;
  if (mode === 'tablet' && adv.visible_tablet === false) return true;
  if (mode === 'mobile_landscape' && adv.visible_mobile_landscape === false) return true;
  if (mode === 'mobile' && adv.visible_mobile === false) return true;
  return false;
}

const contextMenuRef = ref(null);
const canvasRef = ref(null);

function onTileContextMenu(event, tileId) {
  contextMenuRef.value?.open(event, tileId);
}

function onCanvasContextMenu(event) {
  event.preventDefault();
  event.stopPropagation();
  // Risali il DOM fino a trovare un [data-tile-id]
  let el = event.target;
  while (el && el !== canvasRef.value) {
    if (el.dataset && el.dataset.tileId) {
      contextMenuRef.value?.open(event, el.dataset.tileId);
      return;
    }
    el = el.parentElement;
  }
}

function onDocumentContextMenu(event) {
  // Solo se il click è dentro il canvas
  const canvas = canvasRef.value;
  if (!canvas || !canvas.contains(event.target)) return;
  event.preventDefault();
  event.stopImmediatePropagation();
  // Risali il DOM fino a trovare un [data-tile-id]
  let el = event.target;
  while (el && el !== canvas) {
    if (el.dataset && el.dataset.tileId) {
      contextMenuRef.value?.open(event, el.dataset.tileId);
      return;
    }
    el = el.parentElement;
  }
}

onMounted(() => {
  document.addEventListener('contextmenu', onDocumentContextMenu, true);
});

onBeforeUnmount(() => {
  document.removeEventListener('contextmenu', onDocumentContextMenu, true);
});

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

// ── Column drag-resize ──
function startColResize(event, row, colIdx) {
  const cols = row.children || [];
  if (colIdx < 1 || colIdx >= cols.length) return;
  const leftCol = cols[colIdx - 1];
  const rightCol = cols[colIdx];
  const startX = event.clientX;
  const rowEl = event.target.closest('.olo-row-columns');
  if (!rowEl) return;
  const rowWidth = rowEl.offsetWidth;
  const leftStart = getColPercent(leftCol);
  const rightStart = getColPercent(rightCol);

  function onMove(e) {
    const dx = e.clientX - startX;
    const dpct = (dx / rowWidth) * 100;
    let newLeft = Math.round((leftStart + dpct) * 10) / 10;
    let newRight = Math.round((rightStart - dpct) * 10) / 10;
    if (newLeft < 10) { newLeft = 10; newRight = leftStart + rightStart - 10; }
    if (newRight < 10) { newRight = 10; newLeft = leftStart + rightStart - 10; }
    leftCol.settings = { ...leftCol.settings, width_custom: String(newLeft) };
    rightCol.settings = { ...rightCol.settings, width_custom: String(newRight) };
    row.settings = { ...row.settings, layout: 'custom' };
  }
  function onUp() {
    document.removeEventListener('mousemove', onMove);
    document.removeEventListener('mouseup', onUp);
    document.body.style.cursor = '';
    document.body.style.userSelect = '';
    // Sync custom_widths CSV on the row so PHP frontend can render it
    const allWidths = cols.map(c => getColPercent(c));
    row.settings = { ...row.settings, custom_widths: allWidths.join(',') };
    builderStore.isDirty = true;
  }
  document.body.style.cursor = 'col-resize';
  document.body.style.userSelect = 'none';
  document.addEventListener('mousemove', onMove);
  document.addEventListener('mouseup', onUp);
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
  const globalId = event.dataTransfer.getData('global-widget-id');
  if (globalId) {
    handleGlobalWidgetDropIntoColumn(globalId, colId);
    return;
  }
  const tileType = event.dataTransfer.getData('tile-type');
  if (!tileType) return;
  handleDropIntoColumn(tileType, colId);
}

// --- Drop on canvas (creates new section) ---

function onDropCanvas(event) {
  const globalId = event.dataTransfer.getData('global-widget-id');
  if (globalId) {
    handleGlobalWidgetDrop(globalId);
    return;
  }
  const tileType = event.dataTransfer.getData('tile-type');
  if (!tileType) return;
  handleDropFromSidebar(tileType);
}

// --- Drop into section (adds row or element) ---

function onDropIntoSection(event, section) {
  const globalId = event.dataTransfer.getData('global-widget-id');
  if (globalId) {
    const newTile = tilesStore.insertGlobalWidget(globalId);
    if (!newTile) return;
    const col = createColumn('1-1', [newTile]);
    const row = createRow('100', [col]);
    if (!Array.isArray(section.children)) section.children = [];
    section.children.push(row);
    builderStore.isDirty = true;
    builderStore.selectTile(newTile.id);
    return;
  }
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
  border: 1px solid #e5e7eb;
  background: rgba(0, 0, 0, 0.015);
  overflow: visible;
  position: relative;
}

/* Shape divider overlays — positioned absolutely at section edges */
.olo-shapedivider-overlay {
  position: absolute;
  left: 0;
  width: 100%;
  z-index: 10;
  pointer-events: auto;
  cursor: pointer;
  transition: outline 0.15s;
}
.olo-shapedivider-overlay--top {
  top: 0;
  transform: translateY(-100%);
}
.olo-shapedivider-overlay--bottom {
  bottom: 0;
  transform: translateY(100%);
}
.olo-shapedivider-overlay:hover {
  outline: 2px dashed rgba(99, 102, 241, 0.4);
  outline-offset: -2px;
}
.olo-shapedivider-overlay.olo-grid-cell--selected {
  outline: 2px solid var(--olo-color-primary, #6366F1);
  outline-offset: -2px;
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
.olo-bg-video {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
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
  color: #6B7280;
  cursor: pointer;
  user-select: none;
}
.olo-section-bar {
  background: rgba(0, 0, 0, 0.025);
  border-bottom: 1px solid #e5e7eb;
}
.olo-section-bar:hover,
.olo-row-bar:hover {
  background: rgba(0, 0, 0, 0.05);
}
.olo-section-block--selected > .olo-section-bar {
  background: rgba(99, 102, 241, 0.06);
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
  background: rgba(0, 0, 0, 0.06);
  color: #6B7280;
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
.olo-bar-badge--sticky {
  background: rgba(168, 85, 247, 0.15);
  color: #C084FC;
  text-transform: uppercase;
  font-weight: 700;
  letter-spacing: 0.3px;
}
.olo-bar-spacer {
  flex: 1;
}
.olo-bar-btn {
  background: none;
  border: none;
  color: #9CA3AF;
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
  color: #374151;
  background: rgba(0, 0, 0, 0.05);
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
  border: 1px solid #d1d5db;
  background: rgba(0, 0, 0, 0.01);
  overflow: visible;
  position: relative;
}
.olo-row-block--selected {
  border-color: var(--olo-color-primary, #6366F1);
}
.olo-row-bar {
  border-bottom: 1px solid #d1d5db;
}

/* === Columns layout === */
.olo-row-columns {
  display: flex;
  padding: 8px;
  min-height: 80px;
}

/* Column resize handle */
.olo-col-resize-handle {
  width: 8px;
  flex-shrink: 0;
  cursor: col-resize;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  z-index: 5;
  margin: 0 -4px;
}
.olo-col-resize-handle:hover .olo-col-resize-grip,
.olo-col-resize-handle:active .olo-col-resize-grip {
  opacity: 1;
  background: var(--olo-color-primary, #6366F1);
}
.olo-col-resize-grip {
  width: 3px;
  height: 32px;
  border-radius: 2px;
  background: #9ca3af;
  opacity: 0;
  transition: opacity 0.15s, background 0.15s;
}
.olo-row-columns:hover .olo-col-resize-grip {
  opacity: 0.5;
}

.olo-column-block {
  border: 2px dashed #d1d5db;
  min-height: 80px;
  transition: border-color 0.2s, background-color 0.2s;
  position: relative;
}
.olo-column-block--selected {
  border-color: var(--olo-color-primary, #6366F1);
}
.olo-column-block--dragover {
  border-color: var(--olo-color-primary, #6366F1);
  background: rgba(107, 114, 128, 0.08);
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
  color: #9CA3AF;
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
  border: 1px solid #d1d5db;
  background: none;
  color: #9CA3AF;
  cursor: pointer;
  transition: all 0.15s;
}
.olo-preset-btn:hover {
  border-color: #9CA3AF;
  color: #6B7280;
}
.olo-preset-btn--active {
  border-color: var(--olo-color-primary, #6366F1);
  color: var(--olo-color-primary, #6366F1);
  background: rgba(107, 114, 128, 0.1);
}

/* === Add row === */
.olo-add-row {
  display: flex;
  justify-content: center;
  padding: 4px 0;
}
.olo-add-row button {
  background: none;
  border: 1px dashed #d1d5db;
  color: #9CA3AF;
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
  color: #9CA3AF;
  font-size: 13px;
  border: 2px dashed #d1d5db;
}

/* === Bottom drop zone === */
.olo-canvas-bottom-drop {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 12px;
  margin-top: 4px;
  border: 2px dashed #d1d5db;
  color: #9CA3AF;
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
  background: rgba(107, 114, 128, 0.03);
}
.olo-canvas[data-canvas-theme="light"] .olo-section-bar {
  border-bottom-color: #e5e7eb;
  color: #6b7280;
}
.olo-canvas[data-canvas-theme="light"] .olo-row-block {
  border-color: #d1d5db;
  background: rgba(107, 114, 128, 0.02);
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
  border: 1px solid #d1d5db;
  border-radius: 4px;
  background: rgba(0, 0, 0, 0.03);
  color: #374151;
  text-align: center;
  outline: none;
  transition: border-color 0.15s;
}
.olo-custom-input:focus {
  border-color: var(--olo-color-primary, #6366F1);
}
.olo-custom-input::placeholder {
  color: #9CA3AF;
}
.olo-canvas[data-canvas-theme="light"] .olo-custom-input {
  background: rgba(0, 0, 0, 0.05);
  border-color: #d1d5db;
  color: #374151;
}

/* Hidden in current viewport mode */
.olo-node-hidden-vp {
  opacity: 0.2;
  border-color: #f59e0b !important;
  border-style: dashed !important;
}
</style>
