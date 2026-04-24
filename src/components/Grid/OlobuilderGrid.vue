<template>
  <div ref="canvasRef" class="olo-canvas" :data-canvas-theme="canvasTheme" style="position: relative">
    <!-- Sections (top-level) -->
    <div class="olo-sections-list" v-olo-drop-target="listEndDrop('sections', null)">
      <template v-for="(section, sectionIdx) in zoneTiles" :key="section.id">
        <div class="olo-section-wrap">
          <!-- Quick insert "+" before first section -->
          <div v-if="sectionIdx === 0" class="olo-quick-insert" @click.stop="onCleanInsert(0)">
            <div class="olo-quick-insert__line"></div>
            <button class="olo-quick-insert__btn" title="Inserisci sezione o modulo">+</button>
            <div class="olo-quick-insert__line"></div>
          </div>
          <div
            class="olo-section-block"
          :class="{ 'olo-section-block--selected': builderStore.selectedTileId === section.id, 'olo-node-hidden-vp': isHiddenInViewport(section), 'olo-section-block--fullbleed': section.settings?.width === 'fullbleed' }"
          :data-tile-id="section.id"
          :style="getSectionBlockStyle(section)"
          v-olo-draggable="sectionDraggable(section, sectionIdx)"
          v-olo-drop-target="sectionDrop(section, sectionIdx)"
          @contextmenu.prevent="onTileContextMenu($event, section.id)"
        >
          <!-- Background preview layers -->
          <div
            v-if="getNodeBg(section).type === 'image' || getNodeBg(section).type === 'gradient' || getNodeBg(section).type === 'gallery'"
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
          <div class="olo-section-body" :style="getSectionBodyStyle(section)">
            <div class="olo-rows-list" v-olo-drop-target="listEndDrop('rows', section.id)">
              <template v-for="(row, rowIdx) in (section.children || [])" :key="row.id">
                <div
                  class="olo-row-block"
                  :class="{ 'olo-row-block--selected': builderStore.selectedTileId === row.id, 'olo-node-hidden-vp': isHiddenInViewport(row) }"
                  :data-tile-id="row.id"
                  :style="{ ...(getNodeBg(row).type === 'solid' ? getNodeBgStyle(row) : {}), ...getNodeSpacingStyle(row), ...(hasBgImage(row) ? { overflow: 'clip' } : {}) }"
                  v-olo-draggable="rowDraggable(row, section.id, rowIdx)"
                  v-olo-drop-target="rowDrop(row, section.id, rowIdx)"
                >
                  <!-- Background preview layers -->
                  <div
                    v-if="getNodeBg(row).type === 'image' || getNodeBg(row).type === 'gradient' || getNodeBg(row).type === 'gallery'"
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
                    <span v-if="row.settings?.loop_enabled" class="olo-bar-badge olo-bar-badge--loop" title="Loop attivo">&#x21BB; Loop</span>
                    <span class="olo-bar-spacer"></span>
                    <button class="olo-bar-btn" title="Duplica" @click.stop="duplicateItem(row.id)">&#x2398;</button>
                    <button class="olo-bar-btn olo-bar-btn--delete" title="Elimina" @click.stop="removeItem(row.id)">&#x2715;</button>
                  </div>

                  <!-- CSS Grid layout mode -->
                  <div
                    v-if="isGridRow(row)"
                    class="olo-row-columns olo-row-columns--grid"
                    :style="getGridStyle(row)"
                  >
                    <div
                      v-for="(col, colIdx) in (row.children || [])"
                      :key="col.id"
                      class="olo-column-block"
                      :class="{
                        'olo-column-block--selected': builderStore.selectedTileId === col.id
                      }"
                      :data-tile-id="col.id"
                      :style="{ ...getCellGridStyle(col), ...getNodeSpacingStyle(col) }"
                      v-olo-drop-target="columnDrop(col)"
                      @click.stop="selectTile(col.id)"
                    >
                      <div class="olo-column-elements" v-olo-drop-target="listEndDrop('elements', col.id)">
                        <template v-for="(tile, elIdx) in (col.children || [])" :key="tile.id">
                          <div v-olo-draggable="elementDraggable(tile, col.id, elIdx)" v-olo-drop-target="elementDrop(tile, col.id, elIdx)">
                            <GridCell :tile="tile" @contextmenu="onTileContextMenu">
                              <template v-if="tile.type === 'floatingpanel'" #after>
                                <div class="olo-fp-children-zone">
                                  <div class="olo-fp-children-list" v-olo-drop-target="listEndDrop('elements', tile.id)">
                                    <template v-for="(child, chIdx) in (tile.children || [])" :key="child.id">
                                      <div v-olo-draggable="elementDraggable(child, tile.id, chIdx)" v-olo-drop-target="elementDrop(child, tile.id, chIdx)">
                                        <GridCell :tile="child" @contextmenu="onTileContextMenu" />
                                      </div>
                                    </template>
                                  </div>
                                  <div v-if="!tile.children || tile.children.length === 0" class="olo-fp-empty" @click.stop="openFinder(tile.id)">
                                    <span class="olo-column-plus">+</span>
                                    <span>Trascina tile qui</span>
                                  </div>
                                </div>
                              </template>
                            </GridCell>
                          </div>
                        </template>
                      </div>
                      <div v-if="!col.children || col.children.length === 0" class="olo-column-empty" @click.stop="openFinder(col.id)" style="cursor:pointer">
                        <span class="olo-column-plus">+</span>
                        <span>Rilascia qui</span>
                      </div>
                    </div>
                  </div>

                  <!-- Flex layout mode (classic) -->
                  <div
                    v-else
                    class="olo-row-columns"
                    :class="{ 'olo-row-stack': shouldStack(row.settings) }"
                    :style="getRowFlexStyle(row)"
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
                          'olo-column-block--selected': builderStore.selectedTileId === col.id
                        }"
                        :data-tile-id="col.id"
                        :style="{ width: (shouldStack(row.settings)) ? '100%' : getColPercent(col) + '%', minWidth: 0, ...getNodeSpacingStyle(col) }"
                        v-olo-drop-target="columnDrop(col)"
                        @click.stop="selectTile(col.id)"
                      >
                        <!-- Elements inside column -->
                        <div class="olo-column-elements" v-olo-drop-target="listEndDrop('elements', col.id)">
                          <template v-for="(tile, elIdx) in (col.children || [])" :key="tile.id">
                            <div v-olo-draggable="elementDraggable(tile, col.id, elIdx)" v-olo-drop-target="elementDrop(tile, col.id, elIdx)">
                              <GridCell :tile="tile" @contextmenu="onTileContextMenu">
                                <!-- Floating panel children drop zone -->
                                <template v-if="tile.type === 'floatingpanel'" #after>
                                  <div class="olo-fp-children-zone">
                                    <div class="olo-fp-children-list" v-olo-drop-target="listEndDrop('elements', tile.id)">
                                      <template v-for="(child, chIdx) in (tile.children || [])" :key="child.id">
                                        <div v-olo-draggable="elementDraggable(child, tile.id, chIdx)" v-olo-drop-target="elementDrop(child, tile.id, chIdx)">
                                          <GridCell :tile="child" @contextmenu="onTileContextMenu" />
                                        </div>
                                      </template>
                                    </div>
                                    <div v-if="!tile.children || tile.children.length === 0" class="olo-fp-empty" @click.stop="openFinder(tile.id)">
                                      <span class="olo-column-plus">+</span>
                                      <span>Trascina tile qui</span>
                                    </div>
                                  </div>
                                </template>
                              </GridCell>
                            </div>
                          </template>
                        </div>

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
                      :class="['olo-preset-btn', { 'olo-preset-btn--active': !isGridRow(row) && (row.settings?.layout || '50-50') === preset.key }]"
                    >{{ preset.label }}</button>
                    <button
                      @click.stop="openGridPicker(row.id)"
                      :class="['olo-preset-btn olo-preset-btn--grid', { 'olo-preset-btn--active': isGridRow(row) }]"
                      title="CSS Grid Layout"
                    >
                      <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="1" width="6" height="6" rx="1"/><rect x="9" y="1" width="6" height="6" rx="1"/><rect x="1" y="9" width="6" height="6" rx="1"/><rect x="9" y="9" width="6" height="6" rx="1"/></svg>
                      Grid
                    </button>
                    <span class="olo-preset-sep"></span>
                    <button
                      @click.stop="addColumnAfter(row, (row.children || []).length - 1)"
                      class="olo-preset-btn olo-preset-btn--add"
                      title="Aggiungi colonna"
                    >
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                      Col
                    </button>
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
            </div>

            <!-- Quick insert "+" for new row at bottom of section -->
            <div
              class="olo-quick-insert olo-quick-insert--row"
              v-olo-drop-target="listEndDrop('rows', section.id)"
              @click.stop="addRowToSection(section)"
            >
              <div class="olo-quick-insert__line"></div>
              <button class="olo-quick-insert__btn olo-quick-insert__btn--row" title="Aggiungi riga">+</button>
              <div class="olo-quick-insert__line"></div>
            </div>
          </div>
          </div>
          <!-- Quick insert "+" after section -->
          <div class="olo-quick-insert" @click.stop="onCleanInsert(sectionIdx + 1)">
            <div class="olo-quick-insert__line"></div>
            <button class="olo-quick-insert__btn" title="Inserisci sezione o modulo">+</button>
            <div class="olo-quick-insert__line"></div>
          </div>
        </div>
      </template>
    </div>

    <!-- Empty canvas state -->
    <div
      v-if="zoneTiles.length === 0"
      class="olo-canvas-empty"
      v-olo-drop-target="listEndDrop('sections', null)"
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
      v-olo-drop-target="listEndDrop('sections', null)"
      @click.stop="openFinder()"
      style="cursor:pointer"
    >
      <span>+ Rilascia qui o clicca per aggiungere</span>
    </div>

    <!-- Context menu -->
    <ContextMenu ref="contextMenuRef" />

    <!-- Grid Layout Picker -->
    <GridLayoutPicker
      v-if="gridPickerRowId"
      :currentLayout="tilesStore.getTileById(gridPickerRowId)?.settings?.grid_template || tilesStore.getTileById(gridPickerRowId)?.settings?.layout || '50-50'"
      :currentMode="tilesStore.getTileById(gridPickerRowId)?.settings?.layout_mode || 'flex'"
      @close="closeGridPicker"
      @select-grid="onSelectGrid"
      @select-flex="onSelectFlex"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, inject } from 'vue';
import { useTilesStore, createSection, createRow, createColumn } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';
import { useStylesStore } from '@/stores/styles';
import { useDragDrop } from '@/composables/useDragDrop';
import {
  vOloDraggable,
  vOloDropTarget,
  attachClosestEdge,
  makeNodePayload,
  isOloData,
} from '@/composables/useDnD';
import { resolveNodeBg, buildBgStyle, buildOverlayStyle } from '@/composables/useBackgroundStyle';
import { rv } from '@/composables/useResponsiveValue';
import GridCell from './GridCell.vue';
import ShapedividerTile from '@/components/Tiles/ShapedividerTile.vue';
import ContextMenu from '@/components/Builder/ContextMenu.vue';
import GridLayoutPicker from '@/components/Builder/GridLayoutPicker.vue';
import { TEMPLATES_MAP } from '@/config/gridTemplates.js';

const props = defineProps({
  zone: { type: String, default: '' }, // 'header' | 'body' | 'footer' | '' (legacy)
});

const tilesStore = useTilesStore();
const builderStore = useBuilderStore();
const stylesStore = useStylesStore();

/**
 * Computed ref to the correct tiles array based on zone prop.
 * Returns the reactive array directly so v-model on draggable works.
 */
const zoneTiles = computed({
  get() {
    if (props.zone === 'header') return tilesStore.headerTiles;
    if (props.zone === 'footer') return tilesStore.footerTiles;
    return tilesStore.canvasTiles;
  },
  set(val) {
    if (props.zone === 'header') tilesStore.headerTiles = val;
    else if (props.zone === 'footer') tilesStore.footerTiles = val;
    else tilesStore.canvasTiles = val;
  },
});

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
const openInsertPanel = inject('openInsertPanel', () => {});
const { handleDropFromSidebar, handleDropIntoColumn, handleGlobalWidgetDrop, handleGlobalWidgetDropIntoColumn, createTileFromType } = useDragDrop();

// ═════════════════════════════════════════════════════════════════
// DnD Pragmatic — drop targets & draggables
// ═════════════════════════════════════════════════════════════════

/**
 * Helper: produce le opzioni per un draggable generico (sezione/riga/elemento).
 * Il payload rappresenta il nodo nella sua posizione corrente (per reorder).
 */
function makeDraggableOpts(nodeKind, node, parentId, index, handleSelector) {
  return {
    dragHandle: handleSelector || undefined,
    getInitialData: () => makeNodePayload(node.id, nodeKind, parentId, index),
    onDragStart: () => dndStore.startDrag(makeNodePayload(node.id, nodeKind, parentId, index)),
    onDrop: () => { if (!dndStore.isIdle) dndStore.endDrag(); },
  };
}

function sectionDraggable(section, idx) {
  return makeDraggableOpts('section', section, null, idx, '.olo-section-grip');
}

function rowDraggable(row, sectionId, idx) {
  return makeDraggableOpts('row', row, sectionId, idx, '.olo-row-grip');
}

function elementDraggable(el, parentId, idx) {
  return makeDraggableOpts('element', el, parentId, idx, undefined);
}

/**
 * Drop target su un nodo (sezione/riga/elemento): permette reorder relativo
 * usando attachClosestEdge per sapere se il pointer è sopra/sotto.
 */
function makeEdgeDrop(nodeKind, node, parentId, index, allowedEdges) {
  return {
    canDrop: ({ source }) => {
      if (!isOloData(source.data)) return false;
      const p = source.data;
      // Reorder-only: stesso kind, stesso parent (sezioni top-level: parent=null)
      if (p.kind === 'node') {
        return p.nodeKind === nodeKind && p.fromParentId === parentId && p.nodeId !== node.id;
      }
      // Sidebar drop dentro elemento: solo se non è section/row e nodeKind è element
      if (p.kind === 'tile-type' && nodeKind === 'element') {
        return p.tileType !== 'section' && p.tileType !== 'row';
      }
      if (p.kind === 'tile-type' && nodeKind === 'section') return true;
      if (p.kind === 'tile-type' && nodeKind === 'row') return p.tileType !== 'section';
      if (p.kind === 'global-widget' && nodeKind === 'element') return true;
      return false;
    },
    getData: ({ input, element }) => attachClosestEdge(
      { _olo: true, kind: 'node-edge', nodeKind, nodeId: node.id, parentId, index },
      { element, input, allowedEdges: allowedEdges || ['top', 'bottom'] }
    ),
    getIsSticky: () => true,
    onDragEnter: ({ self }) => { self.element.classList.add('olo-dnd-over'); },
    onDragLeave: ({ self }) => { self.element.classList.remove('olo-dnd-over'); },
    onDrop: ({ self }) => { self.element.classList.remove('olo-dnd-over'); },
  };
}

function sectionDrop(section, idx) {
  return makeEdgeDrop('section', section, null, idx, ['top', 'bottom']);
}

function rowDrop(row, sectionId, idx) {
  return makeEdgeDrop('row', row, sectionId, idx, ['top', 'bottom']);
}

function elementDrop(el, colId, idx) {
  return makeEdgeDrop('element', el, colId, idx, ['top', 'bottom']);
}

/**
 * Drop target su una COLONNA (accoglie tile dalla sidebar, spostamento
 * elementi da altra colonna, e drop di nuovi elementi).
 */
const columnDrop = (col) => ({
  canDrop: ({ source }) => {
    if (!isOloData(source.data)) return false;
    const p = source.data;
    if (p.kind === 'tile-type') return p.tileType !== 'section' && p.tileType !== 'row';
    if (p.kind === 'global-widget') return true;
    if (p.kind === 'node' && p.nodeKind === 'element') return true;
    return false;
  },
  getData: () => ({ _olo: true, kind: 'column-body', columnId: col.id }),
  getIsSticky: () => true,
  onDragEnter: ({ self }) => { self.element.classList.add('olo-column-block--dragover'); },
  onDragLeave: ({ self }) => { self.element.classList.remove('olo-column-block--dragover'); },
  onDrop: ({ self }) => { self.element.classList.remove('olo-column-block--dragover'); },
});

/**
 * Drop target su container-list (sections-list, rows-list, elements-list):
 * accoglie drop a fine lista quando il pointer non è su nessun item.
 */
const listEndDrop = (listKind, parentId) => ({
  canDrop: ({ source }) => {
    if (!isOloData(source.data)) return false;
    const p = source.data;
    if (listKind === 'sections') {
      if (p.kind === 'tile-type') return true;
      if (p.kind === 'global-widget') return true;
      if (p.kind === 'node' && p.nodeKind === 'section') return true;
    }
    if (listKind === 'rows') {
      if (p.kind === 'tile-type' && p.tileType !== 'section') return true;
      if (p.kind === 'node' && p.nodeKind === 'row' && p.fromParentId === parentId) return true;
    }
    if (listKind === 'elements') {
      if (p.kind === 'tile-type' && p.tileType !== 'section' && p.tileType !== 'row') return true;
      if (p.kind === 'global-widget') return true;
      if (p.kind === 'node' && p.nodeKind === 'element') return true;
    }
    return false;
  },
  getData: () => ({ _olo: true, kind: 'list-end', listKind, parentId }),
  getIsSticky: () => false,
});

// Il monitor Pragmatic è registrato centralmente in BuilderCanvas.vue (sempre montato).
// Qui definiamo solo le factory di draggable/drop-target; la logica applicata al drop
// è in useDragDrop.applyPragmaticDrop.

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

/**
 * Apply container width constraints to section body to match frontend rendering.
 * fullbleed = edge-to-edge (no container), expand = full width, default/small/large/xlarge = max-width centered.
 */
function getSectionBodyStyle(section) {
  const w = section.settings?.width || 'default';
  if (w === 'fullbleed' || w === 'expand') return {};
  const baseMax = parseInt(window.oloData?.containerMaxWidth) || 1200;
  const widthMap = { small: 0.75, default: 1, large: 1.167, xlarge: 1.333 };
  const factor = widthMap[w] || 1;
  const maxW = Math.round(baseMax * factor);
  return { maxWidth: maxW + 'px', marginLeft: 'auto', marginRight: 'auto' };
}

/**
 * For fullbleed sections, use negative margins to break out of canvas padding.
 */
function getSectionBlockStyle(section) {
  const bgType = getNodeBg(section).type;
  const applyInline = bgType === 'solid' || (bgType === 'gallery' && !(getNodeBg(section).gallery_images?.length));
  const base = { ...getSectionColorStyle(section), ...(applyInline ? getNodeBgStyle(section) : {}), ...getNodeSpacingStyle(section) };
  const w = section.settings?.width || 'default';
  if (w === 'fullbleed') {
    // Negative margins to cancel canvas padding (16px in normal mode, 0 in clean mode)
    if (!builderStore.cleanMode) {
      base.marginLeft = '-16px';
      base.marginRight = '-16px';
      base.marginBottom = '12px';
    }
  }
  return base;
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
  return (bg.type === 'image' && !!bg.image_url) || (bg.type === 'video' && !!bg.video_url) || (bg.type === 'gallery' && bg.gallery_images?.length > 0);
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

// --- Grid Layout Picker ---
const gridPickerRowId = ref(null);
function openGridPicker(rowId) { gridPickerRowId.value = rowId; }
function closeGridPicker() { gridPickerRowId.value = null; }
function onSelectGrid(templateId) {
  if (gridPickerRowId.value) {
    tilesStore.changeRowToGrid(gridPickerRowId.value, templateId);
    builderStore.isDirty = true;
  }
  closeGridPicker();
}
function onSelectFlex(layoutKey) {
  if (gridPickerRowId.value) {
    const row = tilesStore.getTileById(gridPickerRowId.value);
    if (row && row.settings?.layout_mode === 'grid') {
      tilesStore.changeRowToFlex(gridPickerRowId.value, layoutKey);
    } else {
      tilesStore.changeRowLayout(gridPickerRowId.value, layoutKey);
    }
    builderStore.isDirty = true;
  }
  closeGridPicker();
}

function isGridRow(row) {
  return row.settings?.layout_mode === 'grid';
}

function getRowFlexStyle(row) {
  const s = row.settings || {};
  const style = {};
  const mode = builderStore.viewMode;
  // Flex gap — prefer separate column/row gap, fallback to legacy flex_gap, then base gap
  const fcg = parseInt(s.flex_column_gap || 0);
  const frg = parseInt(s.flex_row_gap || 0);
  const fgLegacy = parseInt(s.flex_gap || 0);
  if (fcg > 0 || frg > 0) {
    if (fcg > 0) style.columnGap = fcg + 'px';
    if (frg > 0) style.rowGap = frg + 'px';
  } else if (fgLegacy > 0) {
    style.gap = fgLegacy + 'px';
  } else {
    style.gap = rv(s, 'gap', 16, mode) + 'px';
  }
  style.alignItems = alignMap[s.vertical_align] || 'stretch';
  if (shouldStack(s)) {
    style.flexDirection = 'column';
    style.flexWrap = 'wrap';
  } else {
    if (s.flex_direction) style.flexDirection = s.flex_direction;
    if (s.flex_wrap) style.flexWrap = s.flex_wrap;
  }
  if (s.flex_justify) style.justifyContent = s.flex_justify;
  return style;
}

function getGridStyle(row) {
  const s = row.settings || {};
  const style = {
    display: 'grid',
    gridTemplateColumns: s.grid_columns || '1fr 1fr',
    gridTemplateRows: s.grid_rows || 'auto',
  };
  // Separate column/row gaps or unified gap
  const colGap = s.grid_column_gap;
  const rowGap = s.grid_row_gap;
  if (colGap !== '' && colGap != null && rowGap !== '' && rowGap != null) {
    style.columnGap = colGap + 'px';
    style.rowGap = rowGap + 'px';
  } else if (colGap !== '' && colGap != null) {
    style.columnGap = colGap + 'px';
    style.rowGap = rv(s, 'gap', 16, builderStore.viewMode) + 'px';
  } else if (rowGap !== '' && rowGap != null) {
    style.columnGap = rv(s, 'gap', 16, builderStore.viewMode) + 'px';
    style.rowGap = rowGap + 'px';
  } else {
    style.gap = rv(s, 'gap', 16, builderStore.viewMode) + 'px';
  }
  // Grid auto-flow (direction + density)
  let autoFlow = s.grid_auto_flow || 'row';
  if (s.grid_auto_flow_dense) autoFlow += ' dense';
  if (autoFlow !== 'row') style.gridAutoFlow = autoFlow;
  // Justify content
  if (s.grid_justify_content && s.grid_justify_content !== 'stretch') {
    style.justifyContent = s.grid_justify_content;
  }
  // Align items
  const ai = s.grid_align_items || s.vertical_align || 'stretch';
  if (ai && ai !== 'stretch') style.alignItems = ai;
  // Align content
  if (s.grid_align_content && s.grid_align_content !== 'stretch') {
    style.alignContent = s.grid_align_content;
  }
  return style;
}

function getCellGridStyle(col) {
  const s = col.settings || {};
  const style = {};
  if (s.grid_column) style.gridColumn = s.grid_column;
  if (s.grid_row) style.gridRow = s.grid_row;
  style.minWidth = 0;
  style.minHeight = '40px';
  return style;
}

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
  // In unified mode, auto-switch active zone based on tile location
  if (props.zone) {
    builderStore.setActiveZone(props.zone);
  }
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

// ─── DnD Pragmatic: le operazioni di drop sono centralizzate nel monitor (sopra). ───

// --- Add empty row to section ---

function onCleanInsert(sectionIndex) {
  openInsertPanel(sectionIndex);
}

function addRowToSection(section) {
  const col1 = createColumn('1-2', []);
  const col2 = createColumn('1-2', []);
  const row = createRow('50-50', [col1, col2]);
  if (!Array.isArray(section.children)) section.children = [];
  section.children.push(row);
  builderStore.isDirty = true;
  builderStore.selectTile(row.id);
}

// --- Add column to row ---
function addColumnAfter(row, afterColIdx) {
  tilesStore.addColumnToRow(row.id, afterColIdx);
  builderStore.isDirty = true;
}

// --- Insert section at specific index ---
function insertSectionAt(index) {
  const col1 = createColumn('1-2', []);
  const col2 = createColumn('1-2', []);
  const row = createRow('50-50', [col1, col2]);
  const section = createSection([row]);
  zoneTiles.value.splice(index, 0, section);
  markDirty();
  builderStore.selectTile(section.id);
}

// --- Insert row at specific index within section ---
function insertRowAt(section, index) {
  const col1 = createColumn('1-2', []);
  const col2 = createColumn('1-2', []);
  const row = createRow('50-50', [col1, col2]);
  if (!Array.isArray(section.children)) section.children = [];
  section.children.splice(index, 0, row);
  markDirty();
  builderStore.selectTile(row.id);
}

function markDirty() {
  if (props.zone === 'header') builderStore.headerDirty = true;
  else if (props.zone === 'footer') builderStore.footerDirty = true;
  else builderStore.isDirty = true;
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
.olo-bar-badge--loop {
  background: rgba(34, 197, 94, 0.15);
  color: #4ADE80;
  font-weight: 600;
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
  padding: 0;
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
  padding: 0;
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
  border: 2px dashed transparent;
  min-height: 80px;
  transition: border-color 0.2s, background-color 0.2s;
  position: relative;
}
.olo-row-block:hover .olo-column-block {
  border-color: #d1d5db;
}
.olo-column-block--selected,
.olo-row-block:hover .olo-column-block--selected {
  border-color: var(--olo-color-primary, #6366F1);
}
.olo-column-block--dragover {
  border-color: var(--olo-color-primary, #6366F1) !important;
  background: rgba(107, 114, 128, 0.08);
}

.olo-column-elements {
  min-height: 40px;
  padding: 0;
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

/* === Floating Panel children zone === */
.olo-fp-children-zone {
  margin-top: 4px;
  border: 2px dashed #818cf8;
  border-radius: 8px;
  padding: 6px;
  min-height: 40px;
  background: rgba(129, 140, 248, 0.04);
}
.olo-fp-children-list {
  min-height: 30px;
}
.olo-fp-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 50px;
  color: #818cf8;
  font-size: 11px;
  gap: 2px;
  user-select: none;
  cursor: pointer;
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
.olo-preset-btn--grid {
  display: inline-flex;
  align-items: center;
  gap: 3px;
  font-weight: 600;
  border-color: #e8910c;
  color: #d97706;
}
.olo-preset-sep {
  width: 1px;
  height: 16px;
  background: #e5e7eb;
  margin: 0 2px;
  display: inline-block;
  vertical-align: middle;
}
.olo-preset-btn--add {
  display: inline-flex;
  align-items: center;
  gap: 3px;
  font-weight: 600;
  border-color: #10B981;
  color: #10B981;
}
.olo-preset-btn--add:hover {
  border-color: #059669;
  color: #fff;
  background: #10B981;
}
.olo-preset-btn--grid:hover {
  border-color: #d97706;
  color: #b45309;
}
.olo-preset-btn--grid.olo-preset-btn--active {
  border-color: #d97706;
  color: #d97706;
  background: rgba(217, 119, 6, 0.1);
}

/* Grid mode columns */
.olo-row-columns--grid {
  display: grid !important;
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
  border: 2px dashed var(--olo-color-primary, #6366F1) !important;
  border-radius: 4px;
  background: rgba(99, 102, 241, 0.05) !important;
}
.olo-chosen {
  opacity: 0.8;
  box-shadow: 0 0 0 2px var(--olo-color-primary, #6366F1);
  border-radius: 4px;
}

/* Drop zone highlights — shown when dragging over valid targets */
.sortable-drag ~ .olo-column-block,
.sortable-drag ~ .olo-section-block {
  transition: border-color 0.15s ease, background-color 0.15s ease;
}
.olo-column-block.sortable-ghost-adjacent,
.olo-column-empty:not(:has(.olo-tile-wrapper)) {
  border: 2px dashed rgba(99, 102, 241, 0.4);
  background: rgba(99, 102, 241, 0.04);
  border-radius: 4px;
}

/* Drag-over active state on columns */
.olo-column-block:has(.sortable-ghost) {
  border-color: var(--olo-color-primary, #6366F1) !important;
  background: rgba(99, 102, 241, 0.06);
}

/* Visual cue for empty columns during drag */
.olo-column-empty {
  min-height: 60px;
  transition: border-color 0.15s ease, background 0.15s ease;
}

/* === Sidebar drag active: highlight all drop targets === */
.olo-drag-active .olo-column-empty {
  border: 2px dashed rgba(99, 102, 241, 0.35) !important;
  background: rgba(99, 102, 241, 0.04);
  min-height: 80px;
}
.olo-drag-active .olo-column-block {
  border-color: rgba(99, 102, 241, 0.25) !important;
  transition: border-color 0.15s ease;
}
.olo-drag-active .olo-canvas-bottom-drop {
  border: 2px dashed rgba(99, 102, 241, 0.4);
  background: rgba(99, 102, 241, 0.04);
  padding: 24px;
}
/* Pulse animation on the empty canvas drop hint */
.olo-drag-active .olo-canvas-bottom-drop::after {
  content: '';
  position: absolute;
  inset: 0;
  border: 2px solid transparent;
  border-radius: inherit;
  animation: olo-drop-pulse 1.5s ease-in-out infinite;
}
@keyframes olo-drop-pulse {
  0%, 100% { border-color: transparent; }
  50% { border-color: rgba(99, 102, 241, 0.3); }
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
