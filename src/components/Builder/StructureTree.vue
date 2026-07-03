<template>
  <div ref="stRoot" class="st-root" role="tree" :aria-label="t('Struttura della pagina')" @keydown="onTreeKeydown">
    <!-- Annunci screen-reader per la navigazione da tastiera dell'albero -->
    <div class="st-sr-only" aria-live="polite" role="status">{{ liveMsg }}</div>
    <!-- Empty state -->
    <div v-if="tilesStore.canvasTiles.length === 0 && !builderStore.unifiedMode" style="display:flex;flex-direction:column;align-items:center;gap:8px;padding:32px 0;color:#9CA3AF;font-size:11px">
      <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="1.5">
        <rect x="3" y="3" width="7" height="7" rx="1"/>
        <rect x="14" y="3" width="7" height="7" rx="1"/>
        <rect x="3" y="14" width="7" height="7" rx="1"/>
        <rect x="14" y="14" width="7" height="7" rx="1"/>
      </svg>
      <span>{{ t('Nessun elemento') }}</span>
    </div>

    <template v-else>
      <!-- Toolbar moved to BuilderSidebar V2 panel header (Comprimi/Espandi/Solo selezione). -->


      <!-- ═══ Unified: Header root node ═══ -->
      <div v-if="builderStore.unifiedMode && builderStore.headerTemplate" v-show="showHeader" class="st-zone-root st-zone-root--header">
        <div
          class="st-row st-row--zone"
          :class="{ 'st-row--zone-active': builderStore.activeZone === 'header' }"
          @click="onZoneClick('header')"
        >
          <button class="st-toggle" :class="{ 'st-toggle--open': isExpanded('__zone_header') }" @click.stop="toggle('__zone_header')">
            <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><path d="M2 1l4 3-4 3z"/></svg>
          </button>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="flex-shrink:0;color:#60A5FA"><rect x="3" y="3" width="18" height="6" rx="2"/><line x1="3" y1="13" x2="21" y2="13" opacity="0.3"/><line x1="3" y1="17" x2="21" y2="17" opacity="0.3"/></svg>
          <span class="st-name" style="font-weight:600;color:#60A5FA">{{ t('Header') }}</span>
          <span v-if="builderStore.headerDirty" style="width:6px;height:6px;border-radius:50%;background:#FBBF24;flex-shrink:0" :title="t('Modifiche non salvate')"></span>
        </div>
        <div v-if="isExpanded('__zone_header')" class="st-sub" style="margin-left:10px;padding-left:5px">
          <div class="st-list" v-olo-drop-target="listEndDrop('sections', null)">
            <template v-for="(section, sIdx) in tilesStore.headerTiles" :key="section.id">
              <div v-show="isVisible(section.id)" class="st-item" v-olo-draggable="sectionDraggable(section, sIdx)" v-olo-drop-target="sectionDrop(section, sIdx)">
                <div class="st-row st-row--section" v-strow="section.id" :class="{ 'st-row--active': builderStore.selectedTileId === section.id }" @click="selectTile(section.id, 'header')">
                  <span class="st-grip" :title="t('Trascina')"><svg width="6" height="10" viewBox="0 0 6 10" fill="currentColor"><circle cx="1" cy="1" r="1"/><circle cx="5" cy="1" r="1"/><circle cx="1" cy="5" r="1"/><circle cx="5" cy="5" r="1"/><circle cx="1" cy="9" r="1"/><circle cx="5" cy="9" r="1"/></svg></span>
                  <button class="st-toggle" :class="{ 'st-toggle--open': isExpanded(section.id) }" @click.stop="toggle(section.id)"><svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><path d="M2 1l4 3-4 3z"/></svg></button>
                  <span class="st-icon" v-html="nodeIcon('section')"></span>
                  <span class="st-name" :title="section.settings?._label || t('Sezione')">{{ section.settings?._label || t('Sezione') }}</span>
                </div>
                <div v-if="isExpanded(section.id)" class="st-sub" style="margin-left:10px;padding-left:5px">
                  <div class="st-list" v-olo-drop-target="listEndDrop('rows', section.id)">
                    <template v-for="(row, rIdx) in (section.children || [])" :key="row.id">
                      <div v-show="isVisible(row.id)" class="st-item" v-olo-draggable="rowDraggable(row, section.id, rIdx)" v-olo-drop-target="rowDrop(row, section.id, rIdx)">
                        <div class="st-row st-row--row" v-strow="row.id" :class="{ 'st-row--active': builderStore.selectedTileId === row.id }" @click.stop="selectTile(row.id, 'header')">
                          <span class="st-grip" :title="t('Trascina')"><svg width="6" height="10" viewBox="0 0 6 10" fill="currentColor"><circle cx="1" cy="1" r="1"/><circle cx="5" cy="1" r="1"/><circle cx="1" cy="5" r="1"/><circle cx="5" cy="5" r="1"/><circle cx="1" cy="9" r="1"/><circle cx="5" cy="9" r="1"/></svg></span>
                          <button class="st-toggle" :class="{ 'st-toggle--open': isExpanded(row.id) }" @click.stop="toggle(row.id)"><svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><path d="M2 1l4 3-4 3z"/></svg></button>
                          <span class="st-icon" v-html="nodeIcon('row')"></span>
                          <span class="st-name">{{ row.settings?._label || 'Row' }}</span>
                        </div>
                        <div v-if="isExpanded(row.id)" class="st-sub" style="margin-left:10px;padding-left:5px">
                          <div v-for="col in (row.children || [])" v-show="isVisible(col.id)" :key="col.id" class="st-item">
                            <div class="st-row st-row--column" v-strow="col.id" :class="{ 'st-row--active': builderStore.selectedTileId === col.id }" @click.stop="selectTile(col.id, 'header')">
                              <span class="st-grip-ph"></span>
                              <button v-if="col.children?.length" class="st-toggle" :class="{ 'st-toggle--open': isExpanded(col.id) }" @click.stop="toggle(col.id)"><svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><path d="M2 1l4 3-4 3z"/></svg></button>
                              <span v-else class="st-toggle-ph"></span>
                              <span class="st-icon" v-html="nodeIcon('column')"></span>
                              <span class="st-name">{{ col.settings?._label || 'Column' }}</span>
                            </div>
                            <div v-if="isExpanded(col.id) && col.children?.length" class="st-sub" style="margin-left:10px;padding-left:5px">
                              <div class="st-list" v-olo-drop-target="listEndDrop('elements', col.id)">
                                <template v-for="(tile, eIdx) in (col.children || [])" :key="tile.id">
                                  <div v-show="isVisible(tile.id)" class="st-item" v-olo-draggable="elementDraggable(tile, col.id, eIdx)" v-olo-drop-target="elementDrop(tile, col.id, eIdx)">
                                    <div class="st-row st-row--element" v-strow="tile.id" :class="{ 'st-row--active': builderStore.selectedTileId === tile.id }" @click.stop="selectTile(tile.id, 'header')">
                                      <span class="st-grip" :title="t('Trascina')"><svg width="6" height="10" viewBox="0 0 6 10" fill="currentColor"><circle cx="1" cy="1" r="1"/><circle cx="5" cy="1" r="1"/><circle cx="1" cy="5" r="1"/><circle cx="5" cy="5" r="1"/><circle cx="1" cy="9" r="1"/><circle cx="5" cy="9" r="1"/></svg></span>
                                      <span class="st-toggle-ph"></span>
                                      <span class="st-icon" v-html="nodeIcon(tile.type)"></span>
                                      <span class="st-name" :title="tileLabelFull(tile)">{{ tileLabelFull(tile) }}</span>
                                    </div>
                                  </div>
                                </template>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </template>
                  </div>
                </div>
              </div>
            </template>
          </div>
        </div>
      </div>

      <!-- ═══ Body root node (unified mode wrapper) ═══ -->
      <!-- In unifiedMode: wrapper colorato + header collassabile. Senza unifiedMode: wrapper trasparente, sezioni dirette. -->
      <div :class="builderStore.unifiedMode ? 'st-zone-root st-zone-root--body' : ''" v-show="!builderStore.unifiedMode || showBody">
        <div
          v-if="builderStore.unifiedMode"
          class="st-row st-row--zone"
          :class="{ 'st-row--zone-active': builderStore.activeZone === 'body' }"
          @click="onZoneClick('body')"
        >
          <button class="st-toggle" :class="{ 'st-toggle--open': isExpanded('__zone_body') }" @click.stop="toggle('__zone_body')">
            <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><path d="M2 1l4 3-4 3z"/></svg>
          </button>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="flex-shrink:0;color:#A78BFA"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/></svg>
          <span class="st-name" style="font-weight:600;color:#A78BFA">Body</span>
          <span v-if="builderStore.isDirty" style="width:6px;height:6px;border-radius:50%;background:#FBBF24;flex-shrink:0" :title="t('Modifiche non salvate')"></span>
        </div>
        <div :class="{ 'st-sub': builderStore.unifiedMode }" :style="builderStore.unifiedMode ? 'margin-left:10px;padding-left:5px' : ''" v-show="!builderStore.unifiedMode || isExpanded('__zone_body')">

      <!-- Sections -->
      <div class="st-list" v-olo-drop-target="listEndDrop('sections', null)">
        <template v-for="(section, sIdx) in tilesStore.canvasTiles" :key="section.id">
          <div v-show="isVisible(section.id)" class="st-item" v-olo-draggable="sectionDraggable(section, sIdx)" v-olo-drop-target="sectionDrop(section, sIdx)">
            <!-- Section node -->
            <div
              class="st-row st-row--section"
              v-strow="section.id"
              :class="{ 'st-row--active': builderStore.selectedTileId === section.id }"
              @click="selectTile(section.id)"
            >
              <span class="st-grip" :title="t('Trascina per riordinare')">
                <svg width="6" height="10" viewBox="0 0 6 10" fill="currentColor">
                  <circle cx="1" cy="1" r="1"/><circle cx="5" cy="1" r="1"/>
                  <circle cx="1" cy="5" r="1"/><circle cx="5" cy="5" r="1"/>
                  <circle cx="1" cy="9" r="1"/><circle cx="5" cy="9" r="1"/>
                </svg>
              </span>
              <button class="st-toggle" :class="{ 'st-toggle--open': isExpanded(section.id) }" :aria-label="t('Espandi/comprimi sezione')" @click.stop="toggle(section.id)">
                <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><path d="M2 1l4 3-4 3z"/></svg>
              </button>
              <span class="st-icon" v-html="nodeIcon('section')"></span>
              <input v-if="renamingId === section.id" class="st-rename-input" :style="renameInputStyle" :value="renameValue" @input="renameValue = $event.target.value" @keydown.enter.prevent="confirmRename(section)" @keydown.escape="cancelRename()" @blur="confirmRename(section)" @click.stop />
              <span v-else class="st-name" @click.stop="onNameClick(section)" :title="section.settings?._label || t('Sezione')">{{ section.settings?._label || t('Sezione') }}</span>
              <span class="st-actions">
                <button :title="t('Duplica')" :aria-label="t('Duplica')" @click.stop="duplicate(section.id)">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="9" y="9" width="13" height="13" rx="2"/>
                    <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
                  </svg>
                </button>
                <button :title="t('Salva come template')" :aria-label="t('Salva come template')" @click.stop="emit('save-as-template', section)">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                  </svg>
                </button>
                <button :title="t('Elimina')" :aria-label="t('Elimina')" @click.stop="remove(section.id)">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6L6 18M6 6l12 12"/>
                  </svg>
                </button>
              </span>
            </div>

            <!-- Section children: Rows -->
            <div v-if="isExpanded(section.id)" class="st-sub" style="margin-left:10px;padding-left:5px">
              <div class="st-list" v-olo-drop-target="listEndDrop('rows', section.id)">
                <template v-for="(row, rIdx) in (section.children || [])" :key="row.id">
                  <div v-show="isVisible(row.id)" class="st-item" v-olo-draggable="rowDraggable(row, section.id, rIdx)" v-olo-drop-target="rowDrop(row, section.id, rIdx)">
                    <!-- Row node -->
                    <div
                      class="st-row st-row--row"
                      v-strow="row.id"
                      :class="{ 'st-row--active': builderStore.selectedTileId === row.id }"
                      @click.stop="selectTile(row.id)"
                    >
                      <span class="st-grip" :title="t('Trascina per riordinare')">
                        <svg width="6" height="10" viewBox="0 0 6 10" fill="currentColor">
                          <circle cx="1" cy="1" r="1"/><circle cx="5" cy="1" r="1"/>
                          <circle cx="1" cy="5" r="1"/><circle cx="5" cy="5" r="1"/>
                          <circle cx="1" cy="9" r="1"/><circle cx="5" cy="9" r="1"/>
                        </svg>
                      </span>
                      <button class="st-toggle" :class="{ 'st-toggle--open': isExpanded(row.id) }" :aria-label="t('Espandi/comprimi riga')" @click.stop="toggle(row.id)">
                        <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><path d="M2 1l4 3-4 3z"/></svg>
                      </button>
                      <span class="st-icon" v-html="nodeIcon('row')"></span>
                      <input v-if="renamingId === row.id" class="st-rename-input" :style="renameInputStyle" :value="renameValue" @input="renameValue = $event.target.value" @keydown.enter.prevent="confirmRename(row)" @keydown.escape="cancelRename()" @blur="confirmRename(row)" @click.stop />
                      <span v-else class="st-name" @click.stop="onNameClick(row)" :title="row.settings?._label || 'Row'">{{ row.settings?._label || 'Row' }}</span>
                      <span class="st-actions">
                        <button :title="t('Duplica')" :aria-label="t('Duplica')" @click.stop="duplicate(row.id)">
                          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="9" y="9" width="13" height="13" rx="2"/>
                            <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
                          </svg>
                        </button>
                        <button :title="t('Elimina')" :aria-label="t('Elimina')" @click.stop="remove(row.id)">
                          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 6L6 18M6 6l12 12"/>
                          </svg>
                        </button>
                      </span>
                    </div>

                    <!-- Row children: Columns -->
                    <div v-if="isExpanded(row.id)" class="st-sub" style="margin-left:10px;padding-left:5px">
                      <div v-for="(col, ci) in (row.children || [])" v-show="isVisible(col.id)" :key="col.id" class="st-item">
                        <!-- Column node -->
                        <div
                          class="st-row st-row--column"
                          v-strow="col.id"
                          :class="{ 'st-row--active': builderStore.selectedTileId === col.id }"
                          @click.stop="selectTile(col.id)"
                        >
                          <span class="st-grip-ph"></span>
                          <button
                            class="st-toggle"
                            :class="{ 'st-toggle--open': isExpanded(col.id) }"
                            :aria-label="t('Espandi/comprimi colonna')"
                            @click.stop="toggle(col.id)"
                          >
                            <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><path d="M2 1l4 3-4 3z"/></svg>
                          </button>
                          <span class="st-icon" v-html="nodeIcon('column')"></span>
                          <input v-if="renamingId === col.id" class="st-rename-input" :style="renameInputStyle" :value="renameValue" @input="renameValue = $event.target.value" @keydown.enter.prevent="confirmRename(col)" @keydown.escape="cancelRename()" @blur="confirmRename(col)" @click.stop />
                          <span v-else class="st-name" @click.stop="onNameClick(col)" :title="col.settings?._label || 'Column'">{{ col.settings?._label || 'Column' }}</span>
                        </div>

                        <!-- Column children: Elements -->
                        <div v-if="isExpanded(col.id) || !(col.children && col.children.length)" class="st-sub" style="margin-left:10px;padding-left:5px">
                          <div class="st-list st-dropzone" v-olo-drop-target="listEndDrop('elements', col.id)">
                            <template v-for="(tile, eIdx) in (col.children || (col.children = []))" :key="tile.id">
                              <div v-show="isVisible(tile.id)" class="st-item" v-olo-draggable="elementDraggable(tile, col.id, eIdx)" v-olo-drop-target="elementDrop(tile, col.id, eIdx)">
                                <div
                                  class="st-row st-row--element"
                                  v-strow="tile.id"
                                  :class="{ 'st-row--active': builderStore.selectedTileId === tile.id }"
                                  @click.stop="selectTile(tile.id)"
                                >
                                  <span class="st-grip" :title="t('Trascina per riordinare')">
                                    <svg width="6" height="10" viewBox="0 0 6 10" fill="currentColor">
                                      <circle cx="1" cy="1" r="1"/><circle cx="5" cy="1" r="1"/>
                                      <circle cx="1" cy="5" r="1"/><circle cx="5" cy="5" r="1"/>
                                      <circle cx="1" cy="9" r="1"/><circle cx="5" cy="9" r="1"/>
                                    </svg>
                                  </span>
                                  <button
                                    v-if="tile.type === 'inner-columns' && tile.children?.length"
                                    class="st-toggle"
                                    :class="{ 'st-toggle--open': isExpanded(tile.id) }"
                                    :aria-label="t('Espandi/comprimi elemento')"
                                    @click.stop="toggle(tile.id)"
                                  >
                                    <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><path d="M2 1l4 3-4 3z"/></svg>
                                  </button>
                                  <span v-else class="st-toggle-ph"></span>
                                  <span class="st-icon" v-html="nodeIcon(tile.type)"></span>
                                  <input v-if="renamingId === tile.id" class="st-rename-input" :style="renameInputStyle" :value="renameValue" @input="renameValue = $event.target.value" @keydown.enter.prevent="confirmRename(tile)" @keydown.escape="cancelRename()" @blur="confirmRename(tile)" @click.stop />
                                  <span v-else class="st-name" @click.stop="onNameClick(tile)" :title="tileLabelFull(tile)">{{ tileLabel(tile) }}</span>
                                  <span class="st-actions">
                                    <button :title="t('Duplica')" :aria-label="t('Duplica')" @click.stop="duplicate(tile.id)">
                                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="9" y="9" width="13" height="13" rx="2"/>
                                        <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
                                      </svg>
                                    </button>
                                    <button :title="t('Elimina')" :aria-label="t('Elimina')" @click.stop="remove(tile.id)">
                                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M18 6L6 18M6 6l12 12"/>
                                      </svg>
                                    </button>
                                  </span>
                                </div>

                                <!-- Inner columns children -->
                                <div v-if="tile.type === 'inner-columns' && isExpanded(tile.id) && tile.children?.length" class="st-sub" style="margin-left:10px;padding-left:5px">
                                  <div v-for="(icol, ici) in tile.children" v-show="isVisible(icol.id)" :key="icol.id" class="st-item">
                                    <div
                                      class="st-row st-row--column"
                                      v-strow="icol.id"
                                      :class="{ 'st-row--active': builderStore.selectedTileId === icol.id }"
                                      @click.stop="selectTile(icol.id)"
                                    >
                                      <span class="st-grip-ph"></span>
                                      <button
                                        v-if="icol.children && icol.children.length > 0"
                                        class="st-toggle"
                                        :class="{ 'st-toggle--open': isExpanded(icol.id) }"
                                        :aria-label="t('Espandi/comprimi colonna interna')"
                                        @click.stop="toggle(icol.id)"
                                      >
                                        <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><path d="M2 1l4 3-4 3z"/></svg>
                                      </button>
                                      <span v-else class="st-toggle-ph"></span>
                                      <span class="st-icon" v-html="nodeIcon('column')"></span>
                                      <input v-if="renamingId === icol.id" class="st-rename-input" :style="renameInputStyle" :value="renameValue" @input="renameValue = $event.target.value" @keydown.enter.prevent="confirmRename(icol)" @keydown.escape="cancelRename()" @blur="confirmRename(icol)" @click.stop />
                                      <span v-else class="st-name" @click.stop="onNameClick(icol)" :title="icol.settings?._label || ('ICol ' + (ici + 1))">{{ icol.settings?._label || ('ICol ' + (ici + 1)) }}</span>
                                    </div>

                                    <!-- Inner column children: elements -->
                                    <div v-if="isExpanded(icol.id) && icol.children?.length" class="st-sub" style="margin-left:10px;padding-left:5px">
                                      <div class="st-list st-dropzone" v-olo-drop-target="listEndDrop('elements', icol.id)">
                                        <template v-for="(innerTile, iIdx) in (icol.children || [])" :key="innerTile.id">
                                          <div
                                            class="st-row st-row--element"
                                            v-strow="innerTile.id"
                                            :class="{ 'st-row--active': builderStore.selectedTileId === innerTile.id }"
                                            @click.stop="selectTile(innerTile.id)"
                                            v-olo-draggable="elementDraggable(innerTile, icol.id, iIdx)"
                                            v-olo-drop-target="elementDrop(innerTile, icol.id, iIdx)"
                                          >
                                            <span class="st-grip" :title="t('Trascina per riordinare')">
                                              <svg width="6" height="10" viewBox="0 0 6 10" fill="currentColor">
                                                <circle cx="1" cy="1" r="1"/><circle cx="5" cy="1" r="1"/>
                                                <circle cx="1" cy="5" r="1"/><circle cx="5" cy="5" r="1"/>
                                                <circle cx="1" cy="9" r="1"/><circle cx="5" cy="9" r="1"/>
                                              </svg>
                                            </span>
                                            <span class="st-toggle-ph"></span>
                                            <span class="st-icon" v-html="nodeIcon(innerTile.type)"></span>
                                            <input v-if="renamingId === innerTile.id" class="st-rename-input" :style="renameInputStyle" :value="renameValue" @input="renameValue = $event.target.value" @keydown.enter.prevent="confirmRename(innerTile)" @keydown.escape="cancelRename()" @blur="confirmRename(innerTile)" @click.stop />
                                            <span v-else class="st-name" @click.stop="onNameClick(innerTile)" :title="tileLabelFull(innerTile)">{{ tileLabel(innerTile) }}</span>
                                            <span class="st-actions">
                                              <button :title="t('Duplica')" :aria-label="t('Duplica')" @click.stop="duplicate(innerTile.id)">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                  <rect x="9" y="9" width="13" height="13" rx="2"/>
                                                  <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
                                                </svg>
                                              </button>
                                              <button :title="t('Elimina')" :aria-label="t('Elimina')" @click.stop="remove(innerTile.id)">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                  <path d="M18 6L6 18M6 6l12 12"/>
                                                </svg>
                                              </button>
                                            </span>
                                          </div>
                                        </template>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </template>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </template>
              </div>
            </div>
          </div>
        </template>
      </div>

        </div><!-- /st-sub or pass-through -->
      </div><!-- /st-zone-root--body or pass-through -->

      <!-- ═══ Unified: Footer root node ═══ -->
      <div v-if="builderStore.unifiedMode && builderStore.footerTemplate" v-show="showFooter" class="st-zone-root st-zone-root--footer">
        <div
          class="st-row st-row--zone"
          :class="{ 'st-row--zone-active': builderStore.activeZone === 'footer' }"
          @click="onZoneClick('footer')"
        >
          <button class="st-toggle" :class="{ 'st-toggle--open': isExpanded('__zone_footer') }" @click.stop="toggle('__zone_footer')">
            <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><path d="M2 1l4 3-4 3z"/></svg>
          </button>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="flex-shrink:0;color:#34D399"><rect x="3" y="3" width="18" height="6" rx="2" opacity="0.3"/><line x1="3" y1="13" x2="21" y2="13" opacity="0.3"/><rect x="3" y="17" width="18" height="4" rx="1"/></svg>
          <span class="st-name" style="font-weight:600;color:#34D399">Footer</span>
          <span v-if="builderStore.footerDirty" style="width:6px;height:6px;border-radius:50%;background:#FBBF24;flex-shrink:0" :title="t('Modifiche non salvate')"></span>
        </div>
        <div v-if="isExpanded('__zone_footer')" class="st-sub" style="margin-left:10px;padding-left:5px">
          <div class="st-list" v-olo-drop-target="listEndDrop('sections', null)">
            <template v-for="(section, sIdx) in tilesStore.footerTiles" :key="section.id">
              <div v-show="isVisible(section.id)" class="st-item" v-olo-draggable="sectionDraggable(section, sIdx)" v-olo-drop-target="sectionDrop(section, sIdx)">
                <div class="st-row st-row--section" v-strow="section.id" :class="{ 'st-row--active': builderStore.selectedTileId === section.id }" @click="selectTile(section.id, 'footer')">
                  <span class="st-grip" :title="t('Trascina')"><svg width="6" height="10" viewBox="0 0 6 10" fill="currentColor"><circle cx="1" cy="1" r="1"/><circle cx="5" cy="1" r="1"/><circle cx="1" cy="5" r="1"/><circle cx="5" cy="5" r="1"/><circle cx="1" cy="9" r="1"/><circle cx="5" cy="9" r="1"/></svg></span>
                  <button class="st-toggle" :class="{ 'st-toggle--open': isExpanded(section.id) }" @click.stop="toggle(section.id)"><svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><path d="M2 1l4 3-4 3z"/></svg></button>
                  <span class="st-icon" v-html="nodeIcon('section')"></span>
                  <span class="st-name" :title="section.settings?._label || t('Sezione')">{{ section.settings?._label || t('Sezione') }}</span>
                </div>
                <div v-if="isExpanded(section.id)" class="st-sub" style="margin-left:10px;padding-left:5px">
                  <div class="st-list" v-olo-drop-target="listEndDrop('rows', section.id)">
                    <template v-for="(row, rIdx) in (section.children || [])" :key="row.id">
                      <div v-show="isVisible(row.id)" class="st-item" v-olo-draggable="rowDraggable(row, section.id, rIdx)" v-olo-drop-target="rowDrop(row, section.id, rIdx)">
                        <div class="st-row st-row--row" v-strow="row.id" :class="{ 'st-row--active': builderStore.selectedTileId === row.id }" @click.stop="selectTile(row.id, 'footer')">
                          <span class="st-grip" :title="t('Trascina')"><svg width="6" height="10" viewBox="0 0 6 10" fill="currentColor"><circle cx="1" cy="1" r="1"/><circle cx="5" cy="1" r="1"/><circle cx="1" cy="5" r="1"/><circle cx="5" cy="5" r="1"/><circle cx="1" cy="9" r="1"/><circle cx="5" cy="9" r="1"/></svg></span>
                          <button class="st-toggle" :class="{ 'st-toggle--open': isExpanded(row.id) }" @click.stop="toggle(row.id)"><svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><path d="M2 1l4 3-4 3z"/></svg></button>
                          <span class="st-icon" v-html="nodeIcon('row')"></span>
                          <span class="st-name">{{ row.settings?._label || 'Row' }}</span>
                        </div>
                        <div v-if="isExpanded(row.id)" class="st-sub" style="margin-left:10px;padding-left:5px">
                          <div v-for="col in (row.children || [])" v-show="isVisible(col.id)" :key="col.id" class="st-item">
                            <div class="st-row st-row--column" v-strow="col.id" :class="{ 'st-row--active': builderStore.selectedTileId === col.id }" @click.stop="selectTile(col.id, 'footer')">
                              <span class="st-grip-ph"></span>
                              <button v-if="col.children?.length" class="st-toggle" :class="{ 'st-toggle--open': isExpanded(col.id) }" @click.stop="toggle(col.id)"><svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><path d="M2 1l4 3-4 3z"/></svg></button>
                              <span v-else class="st-toggle-ph"></span>
                              <span class="st-icon" v-html="nodeIcon('column')"></span>
                              <span class="st-name">{{ col.settings?._label || 'Column' }}</span>
                            </div>
                            <div v-if="isExpanded(col.id) && col.children?.length" class="st-sub" style="margin-left:10px;padding-left:5px">
                              <div class="st-list" v-olo-drop-target="listEndDrop('elements', col.id)">
                                <template v-for="(tile, eIdx) in (col.children || [])" :key="tile.id">
                                  <div v-show="isVisible(tile.id)" class="st-item" v-olo-draggable="elementDraggable(tile, col.id, eIdx)" v-olo-drop-target="elementDrop(tile, col.id, eIdx)">
                                    <div class="st-row st-row--element" v-strow="tile.id" :class="{ 'st-row--active': builderStore.selectedTileId === tile.id }" @click.stop="selectTile(tile.id, 'footer')">
                                      <span class="st-grip" :title="t('Trascina')"><svg width="6" height="10" viewBox="0 0 6 10" fill="currentColor"><circle cx="1" cy="1" r="1"/><circle cx="5" cy="1" r="1"/><circle cx="1" cy="5" r="1"/><circle cx="5" cy="5" r="1"/><circle cx="1" cy="9" r="1"/><circle cx="5" cy="9" r="1"/></svg></span>
                                      <span class="st-toggle-ph"></span>
                                      <span class="st-icon" v-html="nodeIcon(tile.type)"></span>
                                      <span class="st-name" :title="tileLabelFull(tile)">{{ tileLabelFull(tile) }}</span>
                                    </div>
                                  </div>
                                </template>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </template>
                  </div>
                </div>
              </div>
            </template>
          </div>
        </div>
      </div>

    </template>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, reactive, computed, nextTick, watch } from 'vue';
import { useTilesStore } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';
import { useHistory } from '@/composables/useHistory';
import { useTileActions } from '@/composables/useTileActions';
import { requestScrollToTile } from '@/utils/scrollToTileChannel';
import {
  vOloDraggable,
  vOloDropTarget,
  attachClosestEdge,
  extractClosestEdge,
  makeNodePayload,
  isOloData,
  useAutoScroll,
} from '@/composables/useDnD';

const emit = defineEmits(['save-as-template']);

const props = defineProps({
  // V2: filter zone visibility from outside (rail filters in BuilderSidebar)
  filter: { type: String, default: 'all' }, // 'all' | 'header' | 'body' | 'footer'
  // V2: live search over node labels — empty string = no filter
  searchQuery: { type: String, default: '' },
  // V2: when true, show only the path of the currently selected node
  onlySelected: { type: Boolean, default: false },
});

const showHeader = computed(() => props.filter === 'all' || props.filter === 'header');
const showBody   = computed(() => props.filter === 'all' || props.filter === 'body');
const showFooter = computed(() => props.filter === 'all' || props.filter === 'footer');

// Compute visible node IDs based on searchQuery and onlySelected.
// Returns null when no filter is active (= show everything).
function _nodeLabelText(node) {
  return ((node.settings?._label || node.type || '') + '').toLowerCase();
}
function _collectMatching(nodes, q, parentChain, out) {
  for (const node of (nodes || [])) {
    const childChain = [...parentChain, node.id];
    let matchedHere = _nodeLabelText(node).includes(q);
    if (matchedHere) {
      out.add(node.id);
      for (const a of parentChain) out.add(a);
    }
    if (node.children?.length) {
      const before = out.size;
      _collectMatching(node.children, q, childChain, out);
      if (out.size > before && !out.has(node.id)) {
        out.add(node.id);
      }
    }
  }
}
function _findPath(nodes, targetId, chain = []) {
  for (const node of (nodes || [])) {
    const newChain = [...chain, node.id];
    if (node.id === targetId) return newChain;
    if (node.children?.length) {
      const r = _findPath(node.children, targetId, newChain);
      if (r) return r;
    }
  }
  return null;
}
const visibleNodeIds = computed(() => {
  const q = (props.searchQuery || '').trim().toLowerCase();
  const isolate = !!props.onlySelected;
  if (!q && !isolate) return null;
  const out = new Set();
  if (q) {
    _collectMatching(tilesStore.headerTiles || [], q, [], out);
    _collectMatching(tilesStore.canvasTiles || [], q, [], out);
    _collectMatching(tilesStore.footerTiles || [], q, [], out);
  }
  if (isolate) {
    const sid = builderStore.selectedTileId;
    if (sid) {
      const path = _findPath(tilesStore.headerTiles || [], sid)
                || _findPath(tilesStore.canvasTiles || [], sid)
                || _findPath(tilesStore.footerTiles || [], sid);
      if (path) {
        for (const id of path) out.add(id);
      }
    }
    if (q && out.size === 0) {
      // search active but no match in selected path — keep search-only set
    }
  }
  return out;
});

function isVisible(id) {
  if (!visibleNodeIds.value) return true;
  return visibleNodeIds.value.has(id);
}

const tilesStore = useTilesStore();
const builderStore = useBuilderStore();
const { removeTiles } = useTileActions();
const stRoot = ref(null);

// Auto-scroll del pannello durante un drag: senza, trascinare un nodo oltre
// la porzione visibile dell'albero è impossibile. Il container scrollabile è
// un ancestor del tree (stessa risoluzione usata dallo scroll-to-selected).
useAutoScroll(
  () => stRoot.value?.closest('.mb-overflow-y-auto, [data-olo-scroll]') || stRoot.value?.parentElement || null,
  { getAllowedAxis: () => 'vertical' }
);

// Inline rename
const renamingId = ref(null);
const renameValue = ref('');
const renameInputStyle = 'flex:1;min-width:0;font-size:11px;color:#E5E7EB;background:#1f2937;border:1px solid #4B5563;border-radius:2px;padding:0 4px;height:20px;outline:none;font-family:inherit;line-height:20px';

function onNameClick(tile) {
  if (builderStore.selectedTileId === tile.id && renamingId.value !== tile.id) {
    renamingId.value = tile.id;
    renameValue.value = tile.settings?._label || '';
    nextTick(function() {
      var inp = stRoot.value && stRoot.value.querySelector('.st-rename-input');
      if (inp) { inp.focus(); inp.select(); }
    });
  } else if (builderStore.selectedTileId !== tile.id) {
    selectTile(tile.id);
  }
}

function confirmRename(tile) {
  if (renamingId.value !== tile.id) return;
  var val = renameValue.value.trim();
  if (!tile.settings) tile.settings = {};
  if (val) {
    tile.settings._label = val;
  } else {
    delete tile.settings._label;
  }
  renamingId.value = null;
  builderStore.isDirty = true;
}

function cancelRename() {
  renamingId.value = null;
}

// Track collapsed state (everything expanded by default)
const collapsed = reactive({});

function isExpanded(id) {
  return collapsed[id] !== true;
}

function toggle(id) {
  collapsed[id] = !collapsed[id];
}

// Close All / Expand All
const allExpanded = computed(function() {
  var ids = getAllNodeIds();
  for (var i = 0; i < ids.length; i++) {
    if (collapsed[ids[i]]) return false;
  }
  return true;
});

function getAllNodeIds() {
  var ids = [];
  for (var s of tilesStore.canvasTiles) {
    ids.push(s.id);
    if (!s.children) continue;
    for (var r of s.children) {
      ids.push(r.id);
      if (!r.children) continue;
      for (var c of r.children) {
        ids.push(c.id);
        if (!c.children) continue;
        for (var e of c.children) {
          if (e.type === 'inner-columns' && e.children) {
            ids.push(e.id);
            for (var ic of e.children) {
              ids.push(ic.id);
            }
          }
        }
      }
    }
  }
  return ids;
}

function collapseAll() {
  var ids = getAllNodeIds();
  for (var i = 0; i < ids.length; i++) collapsed[ids[i]] = true;
}

function expandAll() {
  var ids = getAllNodeIds();
  for (var i = 0; i < ids.length; i++) collapsed[ids[i]] = false;
}

// Expose expand/collapse so the parent BuilderSidebar V2 toolbar can call them.
defineExpose({ expandAll, collapseAll });

// Node type icons (SVG)
// ── Registry icone tile (3 di 3) ──────────────────────────────────────────
// Set 14×14 con path semplificati per l'albero struttura. Gli altri due set
// (InsertPanel.moduleIcon 24×24 espanso, BuilderSidebar.tileIcons 24×24 shorthand)
// hanno SVG diversi per contesto: NON unificare. Mantieni allineate le CHIAVI.
var _iconCache = {};
function nodeIcon(type) {
  if (_iconCache[type]) return _iconCache[type];
  var S = ' stroke="currentColor" stroke-width="1.2"', V = ' viewBox="0 0 14 14" fill="none"';
  var icons = {
    section: '<svg width="14" height="14"'+V+S+'><rect x="1.5" y="1.5" width="11" height="11" rx="1.5"/><line x1="1.5" y1="4.5" x2="12.5" y2="4.5"/></svg>',
    row: '<svg width="14" height="14"'+V+S+'><rect x="1" y="3" width="12" height="8" rx="1.5"/></svg>',
    column: '<svg width="14" height="14"'+V+S+'><rect x="1" y="2" width="5" height="10" rx="1"/><rect x="8" y="2" width="5" height="10" rx="1"/></svg>',
    'inner-columns': '<svg width="14" height="14"'+V+S+'><rect x="1" y="2" width="5" height="10" rx="1"/><rect x="8" y="2" width="5" height="10" rx="1"/></svg>',
    heading: '<svg width="14" height="14"'+V+S+' stroke-width="1.8"><path d="M3 3v8M11 3v8M3 7h8"/></svg>',
    headline: '<svg width="14" height="14"'+V+S+' stroke-width="1.8"><path d="M3 3v8M11 3v8M3 7h8"/></svg>',
    textblock: '<svg width="14" height="14"'+V+S+'><path d="M2 3h10M2 6h7M2 9h10M2 12h5"/></svg>',
    'text-block': '<svg width="14" height="14"'+V+S+'><path d="M2 3h10M2 6h7M2 9h10M2 12h5"/></svg>',
    content: '<svg width="14" height="14"'+V+S+'><rect x="1.5" y="1.5" width="11" height="11" rx="1.5"/><path d="M4 5h6M4 7.5h5M4 10h3"/></svg>',
    image: '<svg width="14" height="14"'+V+S+'><rect x="1.5" y="2" width="11" height="10" rx="1.5"/><circle cx="5" cy="5.5" r="1.2"/><path d="M1.5 10l3-3 2 2 3-4 3 5"/></svg>',
    button: '<svg width="14" height="14"'+V+S+'><rect x="1" y="4" width="12" height="6" rx="2"/><line x1="4" y1="7" x2="10" y2="7"/></svg>',
    video: '<svg width="14" height="14"'+V+S+'><rect x="1" y="2" width="12" height="10" rx="1.5"/><path d="M6 5v4l3-2z" fill="currentColor" stroke="none"/></svg>',
    icon: '<svg width="14" height="14"'+V+S+'><path d="M7 1l1.8 3.6L13 5.3l-3 2.9.7 4.1L7 10.4 3.3 12.3l.7-4.1-3-2.9 4.2-.7z"/></svg>',
    iconbox: '<svg width="14" height="14"'+V+S+'><rect x="1.5" y="1.5" width="11" height="11" rx="2"/><path d="M7 4l1 2 2.2.4-1.6 1.6.4 2.2L7 9l-2 1.2.4-2.2-1.6-1.6 2.2-.4z" stroke-width="1"/></svg>',
    list: '<svg width="14" height="14"'+V+S+'><line x1="5" y1="3" x2="12" y2="3"/><line x1="5" y1="7" x2="12" y2="7"/><line x1="5" y1="11" x2="12" y2="11"/><circle cx="2.5" cy="3" r="1" fill="currentColor" stroke="none"/><circle cx="2.5" cy="7" r="1" fill="currentColor" stroke="none"/><circle cx="2.5" cy="11" r="1" fill="currentColor" stroke="none"/></svg>',
    iconlist: '<svg width="14" height="14"'+V+S+'><line x1="5" y1="3" x2="12" y2="3"/><line x1="5" y1="7" x2="12" y2="7"/><line x1="5" y1="11" x2="12" y2="11"/><path d="M2 2.5l.7.7 1.3-1.3M2 6.5l.7.7 1.3-1.3M2 10.5l.7.7 1.3-1.3" stroke-width="1"/></svg>',
    desclist: '<svg width="14" height="14"'+V+S+'><path d="M2 3h5M2 6h10M2 9h4M2 12h10"/></svg>',
    accordion: '<svg width="14" height="14"'+V+S+'><rect x="1" y="1.5" width="12" height="4" rx="1"/><rect x="1" y="7" width="12" height="4" rx="1"/><path d="M10 3.5l-1.5 1-1.5-1" stroke-width="1"/></svg>',
    tabs: '<svg width="14" height="14"'+V+S+'><rect x="1" y="4" width="12" height="8" rx="1.5"/><path d="M1 6h12M4 4V2.5a1 1 0 011-1h4a1 1 0 011 1V4"/></svg>',
    animatedheading: '<svg width="14" height="14"'+V+S+' stroke-width="1.8"><path d="M3 3v8M11 3v8M3 7h8"/></svg>',
    blendtext: '<svg width="14" height="14"'+V+S+'><text x="2" y="11" font-size="10" font-family="sans-serif" fill="currentColor" stroke="none" font-weight="800">Ab</text></svg>',
    marquee: '<svg width="14" height="14"'+V+S+'><path d="M1 7h12M10 4l3 3-3 3"/></svg>',
    textmask: '<svg width="14" height="14"'+V+S+'><rect x="1.5" y="3" width="11" height="8" rx="1"/><text x="3" y="10" font-size="8" font-family="sans-serif" fill="currentColor" stroke="none" font-weight="800">A</text></svg>',
    textpath: '<svg width="14" height="14"'+V+S+'><path d="M2 11c2-7 8-7 10 0"/></svg>',
    quotation: '<svg width="14" height="14"'+V+S+'><path d="M4 7V4h3v3c0 2-1 3-3 4M8 7V4h3v3c0 2-1 3-3 4"/></svg>',
    toc: '<svg width="14" height="14"'+V+S+'><path d="M2 3h10M2 6h8M2 9h9M2 12h6"/></svg>',
    map: '<svg width="14" height="14"'+V+S+'><path d="M7 12S3 8.5 3 5.5a4 4 0 018 0C11 8.5 7 12 7 12z"/><circle cx="7" cy="5.5" r="1.5"/></svg>',
    osmmap: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 21s-6.5-5.8-6.5-11A6.5 6.5 0 0112 3.5 6.5 6.5 0 0118.5 10c0 5.2-6.5 11-6.5 11z"/><circle cx="12" cy="10" r="2.3"/></svg>',
    divider: '<svg width="14" height="14"'+V+S+'><line x1="1" y1="7" x2="13" y2="7"/></svg>',
    spacer: '<svg width="14" height="14"'+V+S+'><path d="M3 2h8M3 12h8M7 4v6" stroke-dasharray="1.5 1.5"/></svg>',
    grid: '<svg width="14" height="14"'+V+S+'><rect x="1.5" y="1.5" width="11" height="11" rx="1"/><line x1="1.5" y1="7" x2="12.5" y2="7"/><line x1="7" y1="1.5" x2="7" y2="12.5"/></svg>',
    panel: '<svg width="14" height="14"'+V+S+'><rect x="1.5" y="1.5" width="11" height="11" rx="1.5"/><path d="M1.5 5h11"/></svg>',
    overlay: '<svg width="14" height="14"'+V+S+'><rect x="1.5" y="1.5" width="11" height="11" rx="1.5"/><rect x="3" y="8" width="8" height="4" rx=".5" opacity=".5"/></svg>',
    overlaygrid: '<svg width="14" height="14"'+V+S+'><rect x="1" y="1" width="5" height="5" rx=".5"/><rect x="8" y="1" width="5" height="5" rx=".5"/><rect x="1" y="8" width="5" height="5" rx=".5"/><rect x="8" y="8" width="5" height="5" rx=".5"/></svg>',
    overlayslider: '<svg width="14" height="14"'+V+S+'><rect x="2" y="2" width="10" height="10" rx="1.5"/><rect x="3" y="8" width="8" height="3" rx=".5" opacity=".5"/></svg>',
    panelslider: '<svg width="14" height="14"'+V+S+'><rect x="1" y="2" width="5" height="10" rx="1"/><rect x="8" y="2" width="5" height="10" rx="1"/></svg>',
    flipcard: '<svg width="14" height="14"'+V+S+'><rect x="1.5" y="1.5" width="11" height="11" rx="1.5"/><path d="M7 1.5v11" stroke-dasharray="2 1"/></svg>',
    fragment: '<svg width="14" height="14"'+V+S+'><path d="M3 1.5h8M3 12.5h8M1.5 4v6M12.5 4v6" stroke-dasharray="1.5 1.5"/></svg>',
    form: '<svg width="14" height="14"'+V+S+'><rect x="1.5" y="1.5" width="11" height="11" rx="1.5"/><line x1="4" y1="5" x2="10" y2="5"/><line x1="4" y1="8" x2="8" y2="8"/></svg>',
    counter: '<svg width="14" height="14"'+V+S+'><text x="3" y="11" font-size="10" font-family="sans-serif" fill="currentColor" stroke="none">42</text></svg>',
    countercircle: '<svg width="14" height="14"'+V+S+'><circle cx="7" cy="7" r="5.5" stroke-dasharray="25 10"/></svg>',
    countdown: '<svg width="14" height="14"'+V+S+'><circle cx="7" cy="7" r="5.5"/><path d="M7 4v3l2 2"/></svg>',
    testimonial: '<svg width="14" height="14"'+V+S+'><path d="M2 2h10v7H6l-3 3V9H2z"/></svg>',
    pricing: '<svg width="14" height="14"'+V+S+'><rect x="2" y="1" width="10" height="12" rx="1.5"/><line x1="5" y1="4" x2="9" y2="4"/><line x1="5" y1="7" x2="9" y2="7"/><line x1="5" y1="10" x2="9" y2="10"/></svg>',
    pricelist: '<svg width="14" height="14"'+V+S+'><path d="M2 4h6M10 4h2M2 7h5M10 7h2M2 10h7M10 10h2"/></svg>',
    social: '<svg width="14" height="14"'+V+S+'><circle cx="4" cy="7" r="2"/><circle cx="10" cy="4" r="2"/><circle cx="10" cy="10" r="2"/><line x1="5.7" y1="6.2" x2="8.3" y2="4.8"/><line x1="5.7" y1="7.8" x2="8.3" y2="9.2"/></svg>',
    progress: '<svg width="14" height="14"'+V+S+'><rect x="1" y="5" width="12" height="4" rx="2"/><rect x="1" y="5" width="8" height="4" rx="2" fill="currentColor" opacity=".2"/></svg>',
    progresstracker: '<svg width="14" height="14"'+V+S+'><circle cx="3" cy="7" r="1.5" fill="currentColor" opacity=".3"/><circle cx="7" cy="7" r="1.5"/><circle cx="11" cy="7" r="1.5"/><line x1="4.5" y1="7" x2="5.5" y2="7"/><line x1="8.5" y1="7" x2="9.5" y2="7"/></svg>',
    chart: '<svg width="14" height="14"'+V+S+'><rect x="2" y="7" width="3" height="5" rx=".5"/><rect x="6" y="3" width="3" height="9" rx=".5"/><rect x="10" y="5" width="3" height="7" rx=".5"/></svg>',
    timeline: '<svg width="14" height="14"'+V+S+'><line x1="7" y1="1" x2="7" y2="13"/><circle cx="7" cy="3.5" r="1.5" fill="currentColor"/><circle cx="7" cy="7" r="1.5"/><circle cx="7" cy="10.5" r="1.5"/></svg>',
    hotspot: '<svg width="14" height="14"'+V+S+'><rect x="1.5" y="1.5" width="11" height="11" rx="1.5"/><circle cx="6" cy="6" r="1.5"/><circle cx="10" cy="9" r="1.5"/></svg>',
    popover: '<svg width="14" height="14"'+V+S+'><rect x="2.5" y="2" width="9" height="6" rx="1.5"/><path d="M6 8l1.5 2 1.5-2"/></svg>',
    togglebtn: '<svg width="14" height="14"'+V+S+'><rect x="2" y="5" width="10" height="4" rx="2"/><circle cx="9" cy="7" r="1.5" fill="currentColor"/></svg>',
    starrating: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polygon points="6.5 3.4 7.7 6 10.5 6.3 8.4 8.1 9 10.9 6.5 9.4 4 10.9 4.6 8.1 2.5 6.3 5.3 6"/><path d="M13 6.5h7M13 10h5"/></svg>',
    gallery: '<svg width="14" height="14"'+V+S+'><rect x="1" y="1" width="5" height="5" rx=".5"/><rect x="8" y="1" width="5" height="5" rx=".5"/><rect x="1" y="8" width="5" height="5" rx=".5"/><rect x="8" y="8" width="5" height="5" rx=".5"/></svg>',
    progallery: '<svg width="14" height="14"'+V+S+'><rect x="1" y="1" width="5" height="7" rx=".5"/><rect x="8" y="1" width="5" height="3" rx=".5"/><rect x="8" y="6" width="5" height="7" rx=".5"/><rect x="1" y="10" width="5" height="3" rx=".5"/></svg>',
    slider: '<svg width="14" height="14"'+V+S+'><rect x="2" y="2" width="10" height="10" rx="1.5"/><path d="M5 7l2.5 2 2.5-2" fill="none"/></svg>',
    proslider: '<svg width="14" height="14"'+V+S+'><rect x="1" y="3" width="12" height="8" rx="1.5"/><path d="M1 7h12" stroke-dasharray="2 1"/><circle cx="4" cy="7" r="1" fill="currentColor"/></svg>',
    carousel: '<svg width="14" height="14"'+V+S+'><rect x="1" y="4" width="4" height="6" rx=".5" opacity=".4"/><rect x="5.5" y="3" width="3" height="8" rx=".5"/><rect x="9" y="4" width="4" height="6" rx=".5" opacity=".4"/></svg>',
    slideshow: '<svg width="14" height="14"'+V+S+'><rect x="1.5" y="2" width="11" height="9" rx="1.5"/><circle cx="5.5" cy="12" r=".7" fill="currentColor" stroke="none"/><circle cx="7" cy="12" r=".7" fill="currentColor" stroke="none"/><circle cx="8.5" cy="12" r=".7" fill="currentColor" stroke="none"/></svg>',
    imgcompare: '<svg width="14" height="14"'+V+S+'><rect x="1.5" y="1.5" width="11" height="11" rx="1.5"/><line x1="7" y1="1.5" x2="7" y2="12.5"/></svg>',
    lightbox: '<svg width="14" height="14"'+V+S+'><rect x="1.5" y="1.5" width="11" height="11" rx="1.5"/><circle cx="7" cy="7" r="2.5"/></svg>',
    lottie: '<svg width="14" height="14"'+V+S+'><circle cx="7" cy="7" r="5.5"/><path d="M5 7c0-2 1-3 2-3s2 1 2 3-1 3-2 3-2-1-2-3z"/></svg>',
    shatteredimage: '<svg width="14" height="14"'+V+S+'><rect x="1.5" y="1.5" width="11" height="11" rx="1.5"/><path d="M5 1.5l2 5.5-2 5.5M9 1.5l-2 5.5 2 5.5"/></svg>',
    videoplaylist: '<svg width="14" height="14"'+V+S+'><rect x="1" y="2" width="8" height="6" rx="1"/><path d="M4 4v2l2-1z" fill="currentColor" stroke="none"/><path d="M10 3h3M10 6h3M10 9h3M1 10h12"/></svg>',
    soundcloud: '<svg width="14" height="14"'+V+S+'><path d="M2 8v-1M4 9V5M6 8.5V5.5M8 9V4M10 8.5V5.5M12 8v-1.5"/></svg>',
    audio: '<svg width="14" height="14"'+V+S+'><path d="M6 3L3 5.5H1v3h2l3 2.5V3z"/><path d="M11 3.5a6 6 0 010 7M9 5.5a3 3 0 010 3"/></svg>',
    navmenu: '<svg width="14" height="14"'+V+S+' stroke-width="1.5"><line x1="2" y1="3" x2="12" y2="3"/><line x1="2" y1="7" x2="12" y2="7"/><line x1="2" y1="11" x2="12" y2="11"/></svg>',
    megamenu: '<svg width="14" height="14"'+V+S+'><line x1="2" y1="2" x2="12" y2="2" stroke-width="1.5"/><rect x="1" y="5" width="12" height="7" rx="1"/><line x1="5" y1="5" x2="5" y2="12"/><line x1="9" y1="5" x2="9" y2="12"/></svg>',
    breadcrumbs: '<svg width="14" height="14"'+V+S+'><path d="M2 7h2M6 7h2M10 7h2"/><path d="M5 5l2 2-2 2M9 5l2 2-2 2" stroke-width="1"/></svg>',
    nav: '<svg width="14" height="14"'+V+S+'><path d="M2 3h10M2 7h7M2 11h9"/></svg>',
    subnav: '<svg width="14" height="14"'+V+S+' stroke-width="1.5"><path d="M1.5 7h3M5.5 7h3M9.5 7h3"/></svg>',
    pagination: '<svg width="14" height="14"'+V+S+'><path d="M1 6l2 2-2 2"/><rect x="4.5" y="5" width="2.5" height="4" rx=".5"/><rect x="8" y="5" width="2.5" height="4" rx=".5"/></svg>',
    postnavigation: '<svg width="14" height="14"'+V+S+'><path d="M1 7h12M3 5l-2 2 2 2M11 5l2 2-2 2"/></svg>',
    menuanchor: '<svg width="14" height="14"'+V+S+'><path d="M7 2v10M5 4l2-2 2 2"/><line x1="2" y1="9" x2="12" y2="9" stroke-dasharray="2 1.5"/></svg>',
    totop: '<svg width="14" height="14"'+V+S+'><path d="M7 11V4M4.5 6.5L7 4l2.5 2.5"/><line x1="3" y1="2" x2="11" y2="2" stroke-width="1.5"/></svg>',
    scrollprogress: '<svg width="14" height="14"'+V+S+'><rect x="5.5" y="1.5" width="3" height="11" rx="1.5"/><rect x="5.5" y="1.5" width="3" height="6" rx="1.5" fill="currentColor" opacity=".2"/></svg>',
    postgrid: '<svg width="14" height="14"'+V+S+'><rect x="1" y="1" width="5" height="5" rx=".5"/><rect x="8" y="1" width="5" height="5" rx=".5"/><rect x="1" y="8" width="5" height="5" rx=".5"/><rect x="8" y="8" width="5" height="5" rx=".5"/><line x1="2" y1="4.5" x2="5" y2="4.5"/><line x1="9" y1="4.5" x2="12" y2="4.5"/></svg>',
    queryloop: '<svg width="14" height="14"'+V+S+'><rect x="1" y="1" width="5" height="5" rx=".5"/><rect x="8" y="1" width="5" height="5" rx=".5"/><rect x="1" y="8" width="5" height="5" rx=".5"/><path d="M10 10a2 2 0 11-2 2"/></svg>',
    relatedposts: '<svg width="14" height="14"'+V+S+'><rect x="1" y="3" width="3.5" height="5" rx=".5"/><rect x="5.5" y="3" width="3.5" height="5" rx=".5"/><rect x="10" y="3" width="3.5" height="5" rx=".5"/></svg>',
    portfolio: '<svg width="14" height="14"'+V+S+'><rect x="1" y="1" width="5" height="5" rx=".5"/><rect x="8" y="1" width="5" height="5" rx=".5"/><rect x="1" y="8" width="12" height="5" rx=".5"/></svg>',
    team: '<svg width="14" height="14"'+V+S+'><circle cx="5" cy="5" r="2"/><circle cx="9" cy="5" r="2"/><path d="M2 12c0-2 1.5-3 3-3s3 1 3 3M6 12c0-2 1.5-3 3-3s3 1 3 3"/></svg>',
    authorbox: '<svg width="14" height="14"'+V+S+'><circle cx="5" cy="6" r="2"/><rect x="1.5" y="1.5" width="11" height="11" rx="1.5"/><path d="M8 5h4M8 7.5h3"/></svg>',
    sharebuttons: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="6" cy="12" r="2.6"/><circle cx="17" cy="6" r="2.6"/><circle cx="17" cy="18" r="2.6"/><path d="M8.3 10.8l6.4-3.6M8.3 13.2l6.4 3.6"/><path d="M20 4l1.5-1.5M21.5 2.5h-2M21.5 2.5v2"/></svg>',
    wpcomments: '<svg width="14" height="14"'+V+S+'><path d="M12 9a1.5 1.5 0 01-1.5 1.5H5l-2.5 2.5V4A1.5 1.5 0 014 2.5h7A1.5 1.5 0 0112.5 4z"/></svg>',
    tagcloud: '<svg width="14" height="14"'+V+S+'><rect x="1" y="2.5" width="4" height="3" rx="1"/><rect x="6" y="2.5" width="3.5" height="3" rx="1"/><rect x="1" y="7" width="3" height="3" rx="1"/><rect x="5" y="7" width="5" height="3" rx="1"/></svg>',
    popup: '<svg width="14" height="14"'+V+S+'><rect x="1.5" y="2.5" width="11" height="9" rx="1.5"/><path d="M1.5 5h11"/></svg>',
    floatingpanel: '<svg width="14" height="14"'+V+S+'><rect x="3" y="3" width="8" height="8" rx="1.5"/><path d="M3 5h8"/></svg>',
    mobilebar: '<svg width="14" height="14"'+V+S+'><rect x="3" y="1" width="8" height="12" rx="1.5"/><line x1="5" y1="10" x2="9" y2="10"/></svg>',
    newsticker: '<svg width="14" height="14"'+V+S+'><rect x="1.5" y="4.5" width="11" height="5" rx="1"/><path d="M10 5.5l2 1.5-2 1.5"/></svg>',
    alert: '<svg width="14" height="14"'+V+S+'><rect x="1.5" y="3" width="11" height="8" rx="1.5"/><path d="M7 5v2M7 9h.01" stroke-width="1.5"/></svg>',
    code: '<svg width="14" height="14"'+V+S+'><path d="M5 4l-3 3 3 3M9 4l3 3-3 3"/></svg>',
    html: '<svg width="14" height="14"'+V+S+'><path d="M5 4l-3 3 3 3M9 4l3 3-3 3M6 12l2-10"/></svg>',
    shortcode: '<svg width="14" height="14"'+V+S+'><path d="M4 4L1 7l3 3M10 4l3 3-3 3"/><line x1="5.5" y1="7" x2="8.5" y2="7"/></svg>',
    templateembed: '<svg width="14" height="14"'+V+S+'><rect x="1.5" y="1.5" width="11" height="11" rx="1.5"/><rect x="4" y="4" width="6" height="6" rx=".5" stroke-dasharray="1.5 1.5"/></svg>',
    sitemap: '<svg width="14" height="14"'+V+S+'><rect x="5" y="1" width="4" height="2.5" rx=".5"/><rect x="1" y="9" width="3.5" height="2.5" rx=".5"/><rect x="5.5" y="9" width="3.5" height="2.5" rx=".5"/><rect x="10" y="9" width="3.5" height="2.5" rx=".5"/><path d="M7 3.5v3M3 9V8h8v1M7 7v2"/></svg>',
    sitelogo: '<svg width="14" height="14"'+V+S+'><rect x="2.5" y="3.5" width="9" height="7" rx="1.5"/><circle cx="7" cy="7" r="2"/></svg>',
    shapedivider: '<svg width="14" height="14"'+V+S+'><path d="M1 9c2.5-3 4 1 6-1s3.5 1 6-1"/><line x1="1" y1="12" x2="13" y2="12"/></svg>',
    hero: '<svg width="14" height="14"'+V+S+'><rect x="1" y="1.5" width="12" height="11" rx="1.5"/><path d="M3.5 8h7M3.5 10.5h5"/></svg>',
    darkmode: '<svg width="14" height="14"'+V+S+'><path d="M12 7.5a5.5 5.5 0 11-5-5.5 4 4 0 005 5.5z"/></svg>',
    langswitcher: '<svg width="14" height="14"'+V+S+'><circle cx="7" cy="7" r="5.5"/><path d="M1.5 7h11M7 1.5c-2 2-2 9 0 11M7 1.5c2 2 2 9 0 11"/></svg>',
    loginform: '<svg width="14" height="14"'+V+S+'><rect x="3" y="1.5" width="8" height="11" rx="1.5"/><circle cx="7" cy="6" r="2"/><path d="M5 11c0-1.5 1-2 2-2s2 .5 2 2"/></svg>',
    livesearch: '<svg width="14" height="14"'+V+S+'><circle cx="6" cy="6" r="4"/><line x1="9" y1="9" x2="12" y2="12" stroke-width="1.5"/></svg>',
    search: '<svg width="14" height="14"'+V+S+'><circle cx="6" cy="6" r="4"/><line x1="9" y1="9" x2="12" y2="12" stroke-width="1.5"/></svg>',
    servicesearch: '<svg width="14" height="14"'+V+S+'><circle cx="6" cy="6" r="4"/><line x1="9" y1="9" x2="12" y2="12" stroke-width="1.5"/></svg>',
    instagram: '<svg width="14" height="14"'+V+S+'><rect x="1.5" y="1.5" width="11" height="11" rx="3"/><circle cx="7" cy="7" r="2.5"/><circle cx="10" cy="4" r=".7" fill="currentColor" stroke="none"/></svg>',
    facebookpage: '<svg width="14" height="14"'+V+S+'><rect x="2.5" y="1.5" width="9" height="11" rx="1.5"/><path d="M8 1.5v3h2l-.5 2H8v5H6v-5H4.5v-2H6V4c0-1.5 .5-2.5 2-2.5z" stroke-width="1"/></svg>',
    twitterfeed: '<svg width="14" height="14"'+V+S+'><path d="M13 3a6 6 0 01-1.8.9A2.5 2.5 0 007 5.5v.5A6 6 0 012 3s-2 5 3 7.5a6 6 0 01-4 1c7 4.5 13 0 13-7.5"/></svg>',
    table: '<svg width="14" height="14"'+V+S+'><rect x="1.5" y="1.5" width="11" height="11" rx="1.5"/><line x1="1.5" y1="5.5" x2="12.5" y2="5.5"/><line x1="1.5" y1="9" x2="12.5" y2="9"/><line x1="6" y1="1.5" x2="6" y2="12.5"/></svg>',
    switcher: '<svg width="14" height="14"'+V+S+'><rect x="1.5" y="1.5" width="4" height="4" rx="1"/><rect x="1.5" y="8.5" width="4" height="4" rx="1"/><rect x="8.5" y="1.5" width="4" height="11" rx="1"/></svg>',
    switcherpanel: '<svg width="14" height="14"'+V+S+'><rect x="1.5" y="1.5" width="11" height="11" rx="1.5"/><path d="M1.5 5.5h11"/></svg>',
    calendar: '<svg width="14" height="14"'+V+S+'><rect x="1.5" y="2.5" width="11" height="10" rx="1.5"/><path d="M5 1v3M9 1v3M1.5 6h11"/></svg>',
    booking: '<svg width="14" height="14"'+V+S+'><rect x="1.5" y="2.5" width="11" height="10" rx="1.5"/><path d="M5 1v3M9 1v3M1.5 6h11"/><path d="M5 8.5l1.5 1.5 3-3" stroke-width="1"/></svg>',
    bookingpicker: '<svg width="14" height="14"'+V+S+'><rect x="1.5" y="2.5" width="11" height="10" rx="1.5"/><path d="M5 1v3M9 1v3M1.5 6h11"/></svg>',
    hostcard: '<svg width="14" height="14"'+V+S+'><rect x="1.5" y="1.5" width="11" height="11" rx="1.5"/><circle cx="5.5" cy="6" r="2"/><path d="M8 5h3.5M8 7.5h2.5"/></svg>',
    paymentbuttons: '<svg width="14" height="14"'+V+S+'><rect x="1" y="3" width="12" height="8" rx="1.5"/><line x1="1" y1="6" x2="13" y2="6"/><path d="M3.5 9h2.5M8 9h2"/></svg>',
    pdfviewer: '<svg width="14" height="14"'+V+S+'><rect x="3" y="1.5" width="8" height="11" rx="1.5"/><text x="4.5" y="9" font-size="5" font-family="sans-serif" fill="currentColor" stroke="none" font-weight="700">PDF</text></svg>',
    pdfpro: '<svg width="14" height="14"'+V+S+'><rect x="3" y="1.5" width="8" height="11" rx="1.5"/><text x="4.5" y="9" font-size="5" font-family="sans-serif" fill="currentColor" stroke="none" font-weight="700">PDF</text></svg>',
    linkinbio: '<svg width="14" height="14"'+V+S+'><rect x="3.5" y="1.5" width="7" height="11" rx="1.5"/><path d="M5.5 5h3M5.5 7.5h3M5.5 10h2"/></svg>',
    killnextprev: '<svg width="14" height="14"'+V+S+'><path d="M10 4L4 10M4 4l6 6" stroke-width="1.5"/></svg>',
    // ── Icone tile (design handoff): 62 tile (geometria 24×24, resa 14px) ──
    announcementbar: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="4" width="20" height="6" rx="1.5"/><path d="M6 7h9"/><path d="M3 14h18M3 18h12"/></svg>',
    audiohero: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="20" height="18" rx="2"/><path d="M6 14v-3M9 15v-5M12 16.5v-9M15 15v-5M18 14v-3"/></svg>',
    buildermock: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="20" height="18" rx="2"/><path d="M2 7h20M8 7v14"/><rect x="3.8" y="9.4" width="2.6" height="2.2" rx=".4"/><path d="M10 11h8M10 14h6M10 17h7"/></svg>',
    chathero: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="20" height="18" rx="2"/><path d="M6 8h12M6 10.5h7"/><path d="M6 14h10v4l-3-2H6z"/></svg>',
    featuredstory: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="4" width="20" height="16" rx="2"/><rect x="4" y="7" width="7" height="10" rx="1"/><path d="M14 8h5M14 11h5M14 14h3"/></svg>',
    glowgallery: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="7" r="3.5" opacity=".4"/><path d="M7 5h10"/><rect x="3" y="13" width="5" height="8" rx="1"/><rect x="9.5" y="13" width="5" height="8" rx="1"/><rect x="16" y="13" width="5" height="8" rx="1"/></svg>',
    glowhero: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="9" r="6" opacity=".35"/><path d="M5 15h14M8 19h8"/></svg>',
    imagehero: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="20" height="18" rx="2"/><circle cx="7" cy="8" r="1.6"/><path d="M3 16l5-4 4 3 4-4 5 5"/><path d="M6 19h9"/></svg>',
    introsplit: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 5h8M3 9h8M3 13h5"/><path d="M3 18h3M9 18h2"/><rect x="14" y="5" width="7" height="9" rx="1"/><circle cx="18" cy="17.5" r="3"/></svg>',
    maskedvideohero: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 3h20v11c0 5-5 5-10 5S2 19 2 14z"/><polygon points="10 8 15 11 10 14" fill="currentColor" stroke-width="0"/></svg>',
    masthead: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="4" width="20" height="4" rx="1"/><path d="M2 11h20M2 14h13M2 17h20M2 20h9"/></svg>',
    mediacta: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="20" height="13" rx="2"/><polygon points="10 7 15 9.5 10 12" fill="currentColor" stroke-width="0"/><rect x="6" y="19" width="12" height="3" rx="1.5"/></svg>',
    newsletter: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="4" width="20" height="10" rx="2"/><path d="M2 6l10 5 10-5"/><rect x="4" y="17" width="10" height="3.5" rx="1.5"/><rect x="16" y="17" width="4" height="3.5" rx="1"/></svg>',
    photocover: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="2" width="20" height="20" rx="1.5"/><rect x="5" y="5" width="14" height="14"/><path d="M7 16h7"/></svg>',
    producthero: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 9h20"/><circle cx="5" cy="6.5" r=".7" fill="currentColor" stroke-width="0"/><circle cx="7.5" cy="6.5" r=".7" fill="currentColor" stroke-width="0"/><path d="M8 14h8M10 17h4"/></svg>',
    searchhero: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 5h16"/><rect x="3" y="9" width="13" height="4.5" rx="2.25"/><circle cx="19" cy="11.25" r="2.6"/><path d="M5 18h4M11 18h5M18 18h2"/></svg>',
    smearhero: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="20" height="18" rx="2"/><path d="M5 12c3-5 6 3 9-1s4-2 5-2"/><path d="M7 17h9"/></svg>',
    availability: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="4" y="4" width="4" height="4" rx="1"/><rect x="10" y="4" width="4" height="4" rx="1" fill="currentColor" opacity=".22" stroke-width="0"/><rect x="16" y="4" width="4" height="4" rx="1"/><rect x="4" y="10" width="4" height="4" rx="1" fill="currentColor" opacity=".22" stroke-width="0"/><rect x="10" y="10" width="4" height="4" rx="1"/><rect x="16" y="10" width="4" height="4" rx="1"/><path d="M5 19h14"/></svg>',
    builder: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 7h7M15 7h5M17.5 4.5v5"/><path d="M4 12h7M15 12h5"/><path d="M4 17h16"/></svg>',
    finder: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="5" height="3" rx="1.5"/><rect x="10" y="4" width="5" height="3" rx="1.5"/><rect x="17" y="4" width="4" height="3" rx="1.5"/><rect x="5" y="11" width="14" height="9" rx="2"/></svg>',
    hiddenpop: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="4" y="6" width="16" height="12" rx="2" stroke-dasharray="3 2.5"/><circle cx="12" cy="12" r="2.2"/></svg>',
    hotspots: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="9" r="1.4" fill="currentColor" stroke-width="0"/><circle cx="15" cy="14" r="1.4" fill="currentColor" stroke-width="0"/><circle cx="15" cy="14" r="4"/></svg>',
    icontabs: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><circle cx="7" cy="6" r="1.2"/><circle cx="12" cy="6" r="1.2"/><circle cx="17" cy="6" r="1.2"/></svg>',
    mixer: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="9" cy="10" r="5"/><circle cx="15" cy="10" r="5"/><path d="M6 19h12"/></svg>',
    physicsbin: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 5v13a2 2 0 002 2h12a2 2 0 002-2V5"/><circle cx="9" cy="15" r="2.6"/><rect x="13" y="12.5" width="5" height="5" rx="1" transform="rotate(18 15.5 15)"/></svg>',
    projector: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 8h18"/><circle cx="9" cy="8" r="2.4"/><path d="M5 14h14M5 18h8"/></svg>',
    revealbox: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 13h18" stroke-dasharray="3 2"/><path d="M9 9l3-3 3 3"/></svg>',
    scaler: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="5" rx="1"/><path d="M14 5.5h7M5 13h16M5 17h12M5 21h8"/></svg>',
    scratchfx: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M6 13c2-3 4 2 6-1s3-2 4-3"/><path d="M14 6l4 4"/></svg>',
    timezone: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="6" cy="7" r="3"/><path d="M6 5.5V7l1.2 .8"/><path d="M12 6h9M12 11h9M12 16h9"/><circle cx="9" cy="11" r="1.3" fill="currentColor" stroke-width="0"/><circle cx="16" cy="16" r="1.3" fill="currentColor" stroke-width="0"/></svg>',
    tripfinder: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="8" width="20" height="8" rx="2"/><path d="M8 8v8M14 8v8"/><path d="M16 12h4"/></svg>',
    presencegrid: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="7" height="7" rx="1.5"/><rect x="14" y="4" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="6" rx="1.5"/><rect x="14" y="14" width="7" height="6" rx="1.5"/><circle cx="9.5" cy="5.5" r="1.3" fill="currentColor" stroke-width="0"/></svg>',
    matchfixtures: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="7" cy="12" r="3"/><circle cx="17" cy="12" r="3"/><path d="M11.5 12h1M12 10.5v3"/></svg>',
    asciiviz: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M6 9h2.5M11 9h3.5M17 9h1.5M6 13h4M12.5 13h2M6 17h6.5"/></svg>',
    beforeafter: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="5" width="8" height="11" rx="1"/><rect x="13" y="5" width="8" height="11" rx="1"/><path d="M4 19h6M14 19h6"/></svg>',
    categoryrail: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="5" width="7" height="13" rx="1.5"/><rect x="11" y="5" width="7" height="13" rx="1.5"/><rect x="20" y="5" width="3" height="13" rx="1.5"/><path d="M8 21h8"/></svg>',
    productgrid: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="8" height="11" rx="1"/><rect x="13" y="3" width="8" height="11" rx="1"/><path d="M3 16.5h6M13 16.5h6M3 19.5h4M13 19.5h4"/></svg>',
    showcasegrid: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="17" cy="7" r="2.6"/><path d="M16 8l2-2M16.4 6h1.6v1.6"/><path d="M6 17h8"/></svg>',
    svganimator: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 12h4l2.5-6 3 12 2.5-6H21"/><circle cx="3" cy="12" r="1.4" fill="currentColor" stroke-width="0"/><circle cx="21" cy="12" r="1.4" fill="currentColor" stroke-width="0"/></svg>',
    viewer360: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="9"/><path d="M3 12c0-2.2 4-4 9-4s9 1.8 9 4-4 4-9 4-9-1.8-9-4z"/><path d="M13 3.6l2.4 1.5-2.4 1.6"/></svg>',
    'cta-banner': '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M5 10h6M5 13h4"/><rect x="14" y="10" width="5.5" height="4" rx="1"/></svg>',
    'hero-split': '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M12 4v16"/><path d="M4 9h5M4 12h5"/></svg>',
    hoursstrip: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="5" cy="12" r="2.6"/><path d="M5 10.5V12l1.1 .7"/><path d="M10 9h11M10 13h8M10 17h10"/></svg>',
    hoverlist: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="5" cy="7" r="2" fill="currentColor" stroke-width="0"/><path d="M9 7h12"/><circle cx="5" cy="12.5" r="2"/><path d="M9 12.5h9"/><circle cx="5" cy="18" r="2"/><path d="M9 18h11"/></svg>',
    'info-cards': '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="8" height="8" rx="1.5"/><rect x="13" y="4" width="8" height="8" rx="1.5"/><path d="M5 15h6M15 15h4M5 18h4M15 18h5"/></svg>',
    lookbookmixer: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="5" width="5" height="12" rx="1"/><rect x="10" y="5" width="5" height="12" rx="1"/><rect x="17" y="5" width="4" height="12" rx="1"/><path d="M5.5 3.6l-1 1.4h2zM6 20h12"/></svg>',
    'process-steps': '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="6" cy="9" r="3"/><circle cx="18" cy="9" r="3"/><path d="M9 9h6"/><path d="M3 16h6M15 16h6"/></svg>',
    'product-cards': '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="8" height="18" rx="1.5"/><rect x="13" y="3" width="8" height="18" rx="1.5"/><path d="M3 13h8M13 13h8"/><path d="M5 16h3M15 16h3"/></svg>',
    schedule: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="16" rx="1.5"/><path d="M3 8h18M9 4v16M15 4v16"/><rect x="9.5" y="8.6" width="5" height="3" rx=".5" fill="currentColor" opacity=".22" stroke-width="0"/></svg>',
    scrollscrub: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="6" width="5.5" height="9" rx="1"/><rect x="9.25" y="6" width="5.5" height="9" rx="1"/><rect x="16.5" y="6" width="5.5" height="9" rx="1"/><path d="M4 19h13M15 17l2 2-2 2"/></svg>',
    'section-header': '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 5h6"/><path d="M3 9.5h15M3 13h10"/><path d="M16 19h5"/></svg>',
    stackscroll: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="8" y="3" width="13" height="6" rx="1.5"/><rect x="6" y="8.5" width="13" height="6" rx="1.5"/><rect x="4" y="14" width="13" height="6" rx="1.5"/></svg>',
    statstrip: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 8h6" stroke-width="2.2"/><path d="M3 12h4"/><path d="M11 4v16"/><path d="M15 8h6" stroke-width="2.2"/><path d="M15 12h4"/></svg>',
    'step-timeline': '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 10h18" stroke-dasharray="3 2"/><circle cx="6" cy="10" r="2.2"/><circle cx="12" cy="10" r="2.2"/><circle cx="18" cy="10" r="2.2"/><path d="M4 16h4M10 16h4M16 16h4"/></svg>',
    'trust-strip': '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="6" cy="12" r="3"/><path d="M4.8 12l1 1 1.4-1.7"/><path d="M11 12h2"/><circle cx="18" cy="12" r="3"/><path d="M16.8 12l1 1 1.4-1.7"/></svg>',
    workgrid: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/><circle cx="16.5" cy="16.5" r="2.5"/><path d="M18.3 18.3l1.7 1.7"/></svg>',
    worklist: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 6h2M8 6h13M3 12h2M8 12h11M3 18h2M8 18h12"/></svg>',
    goo: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="9" cy="10" r="4.5"/><circle cx="15" cy="13.5" r="3.5"/><circle cx="14" cy="8" r="2"/></svg>',
    particlefx: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="6" cy="7" r="1.1" fill="currentColor" stroke-width="0"/><circle cx="12" cy="5" r="1.1" fill="currentColor" stroke-width="0"/><circle cx="18" cy="8" r="1.1" fill="currentColor" stroke-width="0"/><circle cx="8" cy="13" r="1.1" fill="currentColor" stroke-width="0"/><circle cx="15" cy="12" r="1.1" fill="currentColor" stroke-width="0"/><circle cx="11" cy="18" r="1.1" fill="currentColor" stroke-width="0"/><circle cx="19" cy="17" r="1.1" fill="currentColor" stroke-width="0"/></svg>',
    badge: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="8" width="13" height="8" rx="4"/><circle cx="19.5" cy="12" r="2.2"/></svg>',
    variablespecimen: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 19l5-13 5 13M6 14h6"/><path d="M16 11h5M18.5 9.5v3"/></svg>',
    leaderboard: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="6" width="13" height="3" rx="1.5"/><rect x="3" y="11" width="10" height="3" rx="1.5"/><rect x="3" y="16" width="7" height="3" rx="1.5"/><polygon points="20 5 20.7 6.5 22.3 6.7 21.1 7.8 21.4 9.4 20 8.6 18.6 9.4 18.9 7.8 17.7 6.7 19.3 6.5" fill="currentColor" stroke-width="0"/></svg>',
    // ── Tile fuori dal pacchetto handoff (North dormienti + clod-evoluzione) ──
    northvideohero: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="20" height="18" rx="2"/><polygon points="10 8 16 12 10 16" fill="currentColor" stroke-width="0"/><path d="M5 6l3 0-1 2H4z"/></svg>',
    northquoteslider: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 6h4v4c0 2-1.5 3-3.5 3.5"/><path d="M13 6h4v4c0 2-1.5 3-3.5 3.5"/><path d="M4 19h13M15 17l2 2-2 2"/></svg>',
    studiohero: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="20" height="18" rx="2"/><circle cx="12" cy="10" r="3.5"/><path d="M12 3v3M6 15h12"/></svg>',
    filmreel: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 9h18M3 15h18"/><path d="M7 5v14M17 5v14"/></svg>',
    scrubtext: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 6h14M12 6v9"/><path d="M4 20h16M15 18l2 2-2 2"/></svg>',
    themedemos: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="8" height="7" rx="1"/><rect x="13" y="4" width="8" height="7" rx="1"/><path d="M3 6.5h8M13 6.5h8"/><rect x="3" y="14" width="18" height="6" rx="1"/><path d="M3 16.5h18"/></svg>',
    evonotes: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 3H6a2 2 0 00-2 2v14a2 2 0 002 2h12a2 2 0 002-2V9z"/><path d="M14 3v6h6"/><path d="M8 13h7M8 17h5"/></svg>',
    // ── Gap legacy chiuso: tile WooCommerce / OLO Room / misc (geometria 24×24, resa 14px) ──
    offcanvas: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="20" height="18" rx="2"/><path d="M9 3v18M3 8h6M3 12h6M3 16h4"/></svg>',
    olo_room_availability: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M8 2v4M16 2v4M3 10h18"/><path d="M8 14l3 3 5-5"/></svg>',
    olo_room_calendar: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M8 2v4M16 2v4M3 10h18"/></svg>',
    olo_room_contacts: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 5 9-5"/></svg>',
    olo_room_description: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 5h16M4 9h12M4 13h16M4 17h8"/></svg>',
    olo_room_gallery: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="18" height="8" rx="1"/></svg>',
    olo_room_grid: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/><rect x="13" y="13" width="8" height="8" rx="1"/></svg>',
    olo_room_hero: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="20" height="18" rx="2"/><path d="M6 14h12M6 18h8"/></svg>',
    olo_room_info: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="9"/><path d="M12 8h.01" stroke-width="2.5"/><path d="M12 12v4"/></svg>',
    olo_room_pricing: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2v20M9 5.5h4.5a2.5 2.5 0 010 5H9h5.5a2.5 2.5 0 010 5H9"/></svg>',
    olo_room_related: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="5" width="6" height="8" rx="1"/><rect x="9" y="5" width="6" height="8" rx="1"/><rect x="16" y="5" width="6" height="8" rx="1"/></svg>',
    pagetitlebar: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="4" width="20" height="6" rx="2"/><path d="M6 7h8M6 14h16M6 18h10"/></svg>',
    postmeta: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>',
    readingtime: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l3 3"/></svg>',
    viewscounter: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>',
    woo_addtocart: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/><path d="M12 9v4M10 11h4"/></svg>',
    woo_breadcrumbs: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 12h3M11 12h3M18 12h3"/><path d="M8 9l3 3-3 3M15 9l3 3-3 3"/></svg>',
    woo_cart: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>',
    woo_categories: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="3" width="8" height="8" rx="2"/><rect x="3" y="13" width="8" height="8" rx="2"/><rect x="13" y="13" width="8" height="8" rx="2"/></svg>',
    woo_checkout: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 10l3 3 5-5"/></svg>',
    woo_checkout_multistep: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="5" cy="6" r="2.5" fill="currentColor" opacity=".3"/><circle cx="12" cy="6" r="2.5"/><circle cx="19" cy="6" r="2.5"/><path d="M7.5 6h2M14.5 6h2"/><rect x="3" y="11" width="18" height="10" rx="2"/></svg>',
    woo_comparison: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="8" height="18" rx="2"/><rect x="13" y="3" width="8" height="18" rx="2"/><path d="M5 9h4M15 9h4M5 13h4M15 13h4"/></svg>',
    woo_cross_sells: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="5" width="6" height="8" rx="1"/><rect x="9" y="5" width="6" height="8" rx="1"/><rect x="16" y="5" width="6" height="8" rx="1"/><path d="M5 16h14"/></svg>',
    woo_minicart: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4zM3 6h18"/><path d="M16 10a4 4 0 01-8 0"/></svg>',
    woo_myaccount: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="4"/><path d="M5 20c0-4 3-6 7-6s7 2 7 6"/></svg>',
    woo_notices: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M12 9v3M12 15h.01" stroke-width="2"/></svg>',
    woo_order_tracking: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18"/><path d="M7 15h3M14 15h3"/></svg>',
    woo_price: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2v20M9 5.5h4.5a2.5 2.5 0 010 5H9h5.5a2.5 2.5 0 010 5H9"/></svg>',
    woo_product_bundle: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="8" width="7" height="7" rx="1"/><rect x="14" y="8" width="7" height="7" rx="1"/><rect x="8" y="3" width="8" height="5" rx="1"/><path d="M12 15v6"/></svg>',
    woo_product_description: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 5h16M4 9h12M4 13h16M4 17h8"/></svg>',
    woo_product_filter: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 6h16M8 12h8M10 18h4"/></svg>',
    woo_product_gallery_slider: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="12" rx="2"/><rect x="3" y="17" width="5" height="4" rx="1"/><rect x="10" y="17" width="5" height="4" rx="1"/><rect x="17" y="17" width="4" height="4" rx="1"/></svg>',
    woo_product_image: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="2"/><path d="M3 16l5-5 4 4 4-6 5 7"/></svg>',
    woo_product_meta: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 7h4M12 7h4M4 12h8M4 17h6"/><circle cx="10" cy="7" r="1.5"/></svg>',
    woo_product_navigation: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 12h18M6 8l-4 4 4 4M18 8l4 4-4 4"/></svg>',
    woo_product_stock: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="6" width="18" height="15" rx="2"/><path d="M8 6V4M16 6V4"/><path d="M8 13l3 3 5-5"/></svg>',
    woo_product_tabs: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="7" width="18" height="14" rx="2"/><path d="M3 11h18M7 7V4h4v3M13 7V4h4v3"/></svg>',
    woo_product_title: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4v16M20 4v16M4 12h16"/></svg>',
    woo_products: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/><rect x="13" y="13" width="8" height="8" rx="1"/></svg>',
    woo_quickview: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>',
    woo_rating: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
    woo_recently_viewed: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/><path d="M3 12h2"/></svg>',
    woo_related: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="5" width="6" height="8" rx="1"/><rect x="9" y="5" width="6" height="8" rx="1"/><rect x="16" y="5" width="6" height="8" rx="1"/></svg>',
    woo_sale_badge: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="9"/><text x="7" y="16" font-size="9" font-family="sans-serif" fill="currentColor" stroke="none" font-weight="700">%</text></svg>',
    woo_upsells: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="5" width="6" height="8" rx="1"/><rect x="9" y="5" width="6" height="8" rx="1"/><rect x="16" y="5" width="6" height="8" rx="1"/><path d="M12 16v4M10 18l2-2 2 2"/></svg>',
    woo_wishlist: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 000-7.78z"/></svg>',
  };
  var svg = icons[type] || '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.2"><rect x="2" y="2" width="10" height="10" rx="2"/></svg>';
  _iconCache[type] = svg;
  return svg;
}

const tileNameMap = computed(() => {
  const map = {};
  for (const t of tilesStore.registeredTiles) {
    map[t.type] = t.name;
  }
  return map;
});

function tileLabelFull(tile) {
  const s = tile.settings || {};
  if (s._label) return s._label;
  const custom = s.title || s.heading || s.plan_name || s.name || s.quote || s.text || '';
  if (custom) {
    const clean = custom.replace(/<[^>]*>/g, '').trim();
    if (clean) return clean;
  }
  return tileNameMap.value[tile.type] || tile.type;
}

function tileLabel(tile) {
  const full = tileLabelFull(tile);
  return full.length > 30 ? full.substring(0, 30) + '\u2026' : full;
}

function selectTile(id, zone) {
  builderStore.selectTile(id);
  if (zone) builderStore.setActiveZone(zone);
  // Canvas vive in un iframe: scroll va richiesto via canale dedicato → useIframeBridge → postMessage 'olo:scroll-to'.
  // Il vecchio document.querySelector('[data-tile-id]') falliva perché cercava nel DOM esterno.
  requestScrollToTile(id);
}

function duplicate(id) {
  tilesStore.duplicateTile(id);
  builderStore.isDirty = true;
}

function remove(id) {
  removeTiles(id);
}

function onZoneClick(zone) {
  builderStore.setActiveZone(zone);
}

// ═════════════════════════════════════════════════════════════════
// Accessibilità — navigazione e selezione da tastiera dell'albero
// (WCAG 2.1.1 Keyboard / 4.1.2 Name-Role-Value). Le righe sono treeitem
// focusabili con roving tabindex: Tab entra sulla riga selezionata (o la
// prima), ↑/↓ spostano il focus, Invio/Spazio selezionano, ←/→ comprimono
// /espandono, Home/Fine vanno al primo/ultimo. Un live region annuncia.
// ═════════════════════════════════════════════════════════════════
const liveMsg = ref('');
let _announceSeq = 0;
function announce(msg) {
  const seq = ++_announceSeq;
  liveMsg.value = '';
  // Sotto pressioni rapide (frecce in grab) tiene solo l'annuncio più recente:
  // una sola scrittura nella live region, niente messaggi intermedi spammati.
  nextTick(function () { if (seq === _announceSeq) liveMsg.value = msg; });
}

// Prima riga visibile nell'ordine di rendering (per il roving tabindex
// quando nessun nodo è selezionato): Header → Body → Footer.
const firstNodeId = computed(function () {
  if (builderStore.unifiedMode && showHeader.value && tilesStore.headerTiles?.length) {
    return tilesStore.headerTiles[0].id;
  }
  if (showBody.value && tilesStore.canvasTiles?.length) return tilesStore.canvasTiles[0].id;
  if (showFooter.value && tilesStore.footerTiles?.length) return tilesStore.footerTiles[0].id;
  return null;
});

// Direttiva v-strow: rende ogni riga un treeitem ARIA con roving tabindex,
// aria-selected e (dove pertinente) aria-expanded. Si riapplica a ogni
// patch perché tutte le righe ri-renderano quando cambia selectedTileId.
function applyStrow(el, id) {
  el.setAttribute('role', 'treeitem');
  el.dataset.stId = id == null ? '' : String(id);
  const sel = builderStore.selectedTileId;
  el.setAttribute('aria-selected', (sel != null && String(id) === String(sel)) ? 'true' : 'false');
  const roving = sel || firstNodeId.value;
  el.tabIndex = (roving != null && String(id) === String(roving)) ? 0 : -1;
  const tog = el.querySelector(':scope > .st-toggle');
  if (tog) el.setAttribute('aria-expanded', isExpanded(id) ? 'true' : 'false');
  else el.removeAttribute('aria-expanded');
}
const vStrow = {
  mounted: function (el, binding) { applyStrow(el, binding.value); },
  updated: function (el, binding) { applyStrow(el, binding.value); },
};

function zoneOfRow(rowEl) {
  if (rowEl.closest('.st-zone-root--header')) return 'header';
  if (rowEl.closest('.st-zone-root--footer')) return 'footer';
  return undefined;
}
function visibleRows() {
  if (!stRoot.value) return [];
  return Array.from(stRoot.value.querySelectorAll('.st-row[data-st-id]'))
    .filter(function (r) { return r.dataset.stId && r.offsetParent !== null; });
}
function onTreeKeydown(e) {
  // Modalità "afferra e sposta" attiva → tutte le frecce/Invio/Esc sono modali.
  if (grabbedId.value) { handleGrabKey(e); return; }
  const rowEl = (e.target && e.target.closest) ? e.target.closest('.st-row') : null;
  // Gestisci solo quando il focus è sulla riga stessa: i controlli interni
  // (toggle, azioni, input di rename) mantengono il loro comportamento nativo.
  if (!rowEl || rowEl !== e.target) return;
  const id = rowEl.dataset.stId;
  if (!id) return;
  const key = e.key;
  // I tasti gestiti dall'albero non devono attivare anche gli shortcut globali.
  if (key === 'Enter' || key === ' ' || key === 'Spacebar') {
    e.preventDefault(); e.stopPropagation();
    selectTile(id, zoneOfRow(rowEl));
    const nm = rowEl.querySelector('.st-name');
    announce(t('Selezionato') + ': ' + (nm ? nm.textContent.trim() : id));
    return;
  }
  // "M" = afferra il nodo per spostarlo tra contenitori da tastiera.
  if (key === 'm' || key === 'M') {
    e.preventDefault(); e.stopPropagation();
    enterGrab(id);
    return;
  }
  if (key === 'ArrowDown' || key === 'ArrowUp') {
    e.preventDefault(); e.stopPropagation();
    const rows = visibleRows();
    const idx = rows.indexOf(rowEl);
    if (idx === -1) return;
    const next = key === 'ArrowDown' ? rows[idx + 1] : rows[idx - 1];
    if (next) next.focus();
    return;
  }
  if (key === 'ArrowRight') {
    if (!isExpanded(id)) { e.preventDefault(); e.stopPropagation(); toggle(id); }
    return;
  }
  if (key === 'ArrowLeft') {
    if (isExpanded(id)) { e.preventDefault(); e.stopPropagation(); toggle(id); }
    return;
  }
  if (key === 'Home' || key === 'End') {
    e.preventDefault(); e.stopPropagation();
    const rows = visibleRows();
    const target = key === 'Home' ? rows[0] : rows[rows.length - 1];
    if (target) target.focus();
  }
}

// ═════════════════════════════════════════════════════════════════
// Modalità "afferra e sposta" da tastiera (grab-and-move) — copre lo
// spostamento CROSS-container che il nudge Alt+↑/↓ (solo dentro il parent)
// non permette, senza mouse. WCAG 2.1.1. Modello: M afferra il nodo
// focalizzato, ↑/↓ lo spostano LIVE tra le posizioni valide della stessa
// zona, Invio/Spazio rilasciano, Esc annulla (ripristina la posizione).
// moveNodeTo è il primitivo low-level (nessuna validazione): la validità
// la garantisce qui l'enumerazione degli slot per "kind".
// ═════════════════════════════════════════════════════════════════
const grabbedId = ref(null);
const grabOrigin = ref(null);

function rowElById(id) {
  if (!stRoot.value || id == null) return null;
  const sel = '.st-row[data-st-id="' + String(id).replace(/["\\]/g, '\\$&') + '"]';
  return stRoot.value.querySelector(sel);
}
function focusRowById(id) {
  const el = rowElById(id);
  if (el) el.focus();
}
function markGrabbed(id, on) {
  const el = rowElById(id);
  if (!el) return;
  if (on) { el.classList.add('st-row--grabbed'); el.setAttribute('aria-grabbed', 'true'); }
  else { el.classList.remove('st-row--grabbed'); el.removeAttribute('aria-grabbed'); }
}

// Localizza un nodo in una delle tre zone: ritorna parentNode (null se root),
// indice, array genitore e zona.
function _walkLoc(arr, parentNode, id) {
  for (let i = 0; i < (arr || []).length; i++) {
    const n = arr[i];
    if (n.id === id) return { parentNode, parentArray: arr, index: i, node: n };
    if (n.children && n.children.length) {
      const r = _walkLoc(n.children, n, id);
      if (r) return r;
    }
  }
  return null;
}
function locateNode(id) {
  const zones = [['header', tilesStore.headerTiles], ['body', tilesStore.canvasTiles], ['footer', tilesStore.footerTiles]];
  for (const [zone, root] of zones) {
    const r = _walkLoc(root, null, id);
    if (r) return Object.assign({ zone }, r);
  }
  return null;
}
// "kind" spostabile derivato dal tipo del genitore (coerente col DnD):
// figlio di root = section; figlio di section = row; figlio di column/
// inner-column = element. Tutto il resto (colonne, ecc.) non è grabbabile.
function grabKindOf(loc) {
  if (!loc) return null;
  if (loc.parentNode == null) return 'section';
  const pt = loc.parentNode.type;
  if (pt === 'section') return 'row';
  if (pt === 'column' || pt === 'inner-column') return 'element';
  return null;
}
function _collect(arr, pred, out, excludeId) {
  for (const n of (arr || [])) {
    // Salta il nodo afferrato E tutto il suo sottoalbero: impedisce di
    // spostare un wrapper (es. inner-columns) dentro un suo discendente.
    if (excludeId != null && n.id === excludeId) continue;
    if (pred(n)) out.push(n);
    if (n.children && n.children.length) _collect(n.children, pred, out, excludeId);
  }
}
// Contenitori validi per il kind, nella zona del nodo, in ordine visivo.
function grabContainers(kind, zone, excludeId) {
  const root = tilesStore.getZoneTiles(zone);
  if (kind === 'section') return [{ id: null, children: root, label: t('Sezioni') }];
  const out = [];
  if (kind === 'row') _collect(root, n => n.type === 'section', out, excludeId);
  else if (kind === 'element') _collect(root, n => n.type === 'column' || n.type === 'inner-column', out, excludeId);
  return out.map(n => ({
    id: n.id,
    children: Array.isArray(n.children) ? n.children : (n.children = []),
    label: n.settings?._label || (n.type === 'section' ? t('Sezione') : t('Colonna')),
  }));
}
// Lista lineare di slot di inserimento (calcolata come se il nodo fosse già
// rimosso) + indice dello slot attualmente occupato dal nodo.
function buildGrabSlots(loc) {
  const kind = grabKindOf(loc);
  const containers = grabContainers(kind, loc.zone, loc.node.id);
  const curParentId = loc.parentNode ? loc.parentNode.id : null;
  const slots = [];
  let currentSlotIdx = -1;
  for (const c of containers) {
    const isHolder = (c.id === curParentId) || (c.id === null && curParentId === null && c.children === loc.parentArray);
    const count = isHolder ? c.children.length - 1 : c.children.length;
    for (let i = 0; i <= count; i++) {
      if (isHolder && i === loc.index) currentSlotIdx = slots.length;
      slots.push({ parentId: c.id, index: i, label: c.label });
    }
  }
  return { slots, currentSlotIdx: currentSlotIdx < 0 ? 0 : currentSlotIdx };
}

function enterGrab(id) {
  const loc = locateNode(id);
  const kind = grabKindOf(loc);
  if (!kind) { announce(t('Questo elemento non è spostabile da tastiera')); return; }
  // Un solo checkpoint undo per l'intera operazione di grab.
  useHistory().pushStateNow();
  grabbedId.value = id;
  grabOrigin.value = { parentId: loc.parentNode ? loc.parentNode.id : null, index: loc.index };
  builderStore.selectTile(id);
  const el = rowElById(id);
  const label = el ? (el.querySelector('.st-name')?.textContent || '').trim() : id;
  nextTick(function () { focusRowById(id); markGrabbed(id, true); });
  announce(t('Afferrato') + ': ' + label + '. ' + t('Frecce per spostare, Invio per rilasciare, Esc per annullare.'));
}
function moveGrab(dir) {
  const id = grabbedId.value;
  const loc = locateNode(id);
  if (!loc) { exitGrab(); return; }
  const { slots, currentSlotIdx } = buildGrabSlots(loc);
  const next = currentSlotIdx + dir;
  if (next < 0 || next >= slots.length) { announce(t('Limite raggiunto')); return; }
  const target = slots[next];
  tilesStore.moveNodeTo(id, target.parentId, target.index);
  tilesStore._bumpVersion();
  builderStore.markDirtyForTile(id);
  announce((target.label ? target.label + ', ' : '') + t('posizione') + ' ' + (target.index + 1));
  nextTick(function () { focusRowById(id); markGrabbed(id, true); });
}
function confirmGrab() {
  const id = grabbedId.value;
  markGrabbed(id, false);
  exitGrab();
  announce(t('Rilasciato'));
  nextTick(function () { focusRowById(id); });
}
function cancelGrab() {
  const id = grabbedId.value;
  const o = grabOrigin.value;
  markGrabbed(id, false);
  if (o) {
    tilesStore.moveNodeTo(id, o.parentId, o.index);
    tilesStore._bumpVersion();
    builderStore.markDirtyForTile(id);
  }
  exitGrab();
  announce(t('Spostamento annullato'));
  nextTick(function () { focusRowById(id); });
}
function exitGrab() {
  grabbedId.value = null;
  grabOrigin.value = null;
}
function handleGrabKey(e) {
  const key = e.key;
  // Modalità modale: i tasti gestiti non devono raggiungere gli shortcut globali.
  const stop = function () { e.preventDefault(); e.stopPropagation(); };
  if (key === 'ArrowDown') { stop(); moveGrab(1); return; }
  if (key === 'ArrowUp') { stop(); moveGrab(-1); return; }
  if (key === 'Enter' || key === ' ' || key === 'Spacebar' || key === 'm' || key === 'M') { stop(); confirmGrab(); return; }
  if (key === 'Escape') { stop(); cancelGrab(); return; }
  if (key === 'Tab') { confirmGrab(); return; } // lascia uscire il focus
  // Inghiotti gli altri tasti di navigazione per non sorprendere durante il grab.
  if (key === 'ArrowLeft' || key === 'ArrowRight' || key === 'Home' || key === 'End') stop();
}

// ═════════════════════════════════════════════════════════════════
// DnD Pragmatic — factory functions (stesso pattern di OlobuilderGrid).
// Il monitor centralizzato vive in BuilderCanvas (useDragDrop.applyPragmaticDrop).
// ═════════════════════════════════════════════════════════════════

function draggableOpts(nodeKind, node, parentId, index) {
  return {
    dragHandle: '.st-row',
    getInitialData: () => makeNodePayload(node.id, nodeKind, parentId, index),
    onDragStart: () => {},
  };
}
function nodeEdgeDrop(nodeKind, node, parentId, index, allowedEdges) {
  return {
    canDrop: ({ source }) => {
      if (!isOloData(source.data)) return false;
      const p = source.data;
      if (p.kind === 'node') {
        // Anche cross-parent (element verso un'altra colonna/sezione, row verso
        // un'altra sezione): moveNodeTo gestisce lo spostamento tra contenitori,
        // e così l'indicatore di drop appare ovunque il rilascio sia valido.
        return p.nodeKind === nodeKind && p.nodeId !== node.id;
      }
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
    onDragEnter: ({ self } = {}) => {
      if (!self?.element) return;
      self.element.classList.add('st-dnd-over');
      const edge = extractClosestEdge(self.data);
      self.element.classList.toggle('st-dnd-over-top', edge === 'top');
      self.element.classList.toggle('st-dnd-over-bottom', edge === 'bottom');
    },
    onDrag: ({ self } = {}) => {
      // v1.0.62 — aggiorna classe edge-specific live durante drag così la linea
      // di drop indicator segue il pointer (top vs bottom).
      if (!self?.element) return;
      const edge = extractClosestEdge(self.data);
      self.element.classList.toggle('st-dnd-over-top', edge === 'top');
      self.element.classList.toggle('st-dnd-over-bottom', edge === 'bottom');
    },
    onDragLeave: ({ self } = {}) => {
      if (!self?.element) return;
      self.element.classList.remove('st-dnd-over', 'st-dnd-over-top', 'st-dnd-over-bottom');
    },
    onDrop: ({ self } = {}) => {
      if (!self?.element) return;
      self.element.classList.remove('st-dnd-over', 'st-dnd-over-top', 'st-dnd-over-bottom');
    },
  };
}

function listEndDrop(listKind, parentId) {
  return {
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
  };
}

const sectionDraggable = (section, idx) => draggableOpts('section', section, null, idx);
const sectionDrop = (section, idx) => nodeEdgeDrop('section', section, null, idx, ['top', 'bottom']);
const rowDraggable = (row, sectionId, idx) => draggableOpts('row', row, sectionId, idx);
const rowDrop = (row, sectionId, idx) => nodeEdgeDrop('row', row, sectionId, idx, ['top', 'bottom']);
const elementDraggable = (el, parentId, idx) => draggableOpts('element', el, parentId, idx);
const elementDrop = (el, parentId, idx) => nodeEdgeDrop('element', el, parentId, idx, ['top', 'bottom']);

// Expand ancestors of a tile so it's visible in the tree.
// Cerca in body, header E footer — l'utente può cliccare tile in qualunque zona.
function expandAncestors(tileId) {
  const zones = [
    tilesStore.canvasTiles || [],
    tilesStore.headerTiles || [],
    tilesStore.footerTiles || [],
  ];
  for (const tiles of zones) {
    if (expandAncestorsIn(tiles, tileId)) return;
  }
}
function expandAncestorsIn(tiles, tileId) {
  for (var s of tiles) {
    if (s.id === tileId) return true;
    if (!s.children) continue;
    for (var r of s.children) {
      if (r.id === tileId) { collapsed[s.id] = false; return true; }
      if (!r.children) continue;
      for (var c of r.children) {
        if (c.id === tileId) { collapsed[s.id] = false; collapsed[r.id] = false; return true; }
        if (!c.children) continue;
        for (var e of c.children) {
          if (e.id === tileId) { collapsed[s.id] = false; collapsed[r.id] = false; collapsed[c.id] = false; return true; }
          if (e.type === 'inner-columns' && e.children) {
            for (var ic of e.children) {
              if (ic.id === tileId) { collapsed[s.id] = false; collapsed[r.id] = false; collapsed[c.id] = false; collapsed[e.id] = false; return true; }
              if (ic.children) {
                for (var ie of ic.children) {
                  if (ie.id === tileId) { collapsed[s.id] = false; collapsed[r.id] = false; collapsed[c.id] = false; collapsed[e.id] = false; collapsed[ic.id] = false; return true; }
                }
              }
            }
          }
        }
      }
    }
  }
  return false;
}

// When a tile is selected (from canvas or anywhere), evidenzia + scrolla la riga
// nella tree. block:'center' è più visibile di nearest (riposiziona attivamente
// anche per tile parzialmente visibili). Flash CSS class temporaneo aiuta a notare
// dove è andata la selezione.
watch(function() { return builderStore.selectedTileId; }, function(newId) {
  if (!newId) return;
  expandAncestors(newId);
  nextTick(function() {
    if (!stRoot.value) return;
    var el = stRoot.value.querySelector('.st-row--active');
    if (!el) return;
    // Scroll: il container scrollabile è di solito il parent della sidebar.
    // Calcoliamo manualmente per controllare meglio il container target.
    var scrollContainer = el.closest('.mb-overflow-y-auto, [data-olo-scroll]') || stRoot.value.parentElement;
    if (scrollContainer && scrollContainer.scrollHeight > scrollContainer.clientHeight) {
      var er = el.getBoundingClientRect();
      var cr = scrollContainer.getBoundingClientRect();
      var offset = er.top - cr.top - (cr.height / 2) + (er.height / 2);
      scrollContainer.scrollBy({ top: offset, behavior: 'smooth' });
    } else {
      el.scrollIntoView({ block: 'center', behavior: 'smooth' });
    }
    // Flash effetto via inline style temporaneo (memoria: evitare <style scoped>
    // di StructureTree per non scatenare bug di cache già osservati).
    var prevBoxShadow = el.style.boxShadow;
    var prevTransition = el.style.transition;
    el.style.transition = 'box-shadow 0.18s ease-out';
    el.style.boxShadow = '0 0 0 2px var(--olo-ui-accent, #e8622a)';
    setTimeout(function() {
      el.style.transition = 'box-shadow 0.45s ease-in';
      el.style.boxShadow = prevBoxShadow || '';
      setTimeout(function() { el.style.transition = prevTransition || ''; }, 500);
    }, 400);
  });
});
</script>

<style scoped>
.st-root {
  padding: 8px 6px;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
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
  background: rgba(0, 0, 0, 0.04);
}
.st-row--active {
  background: rgb(var(--olo-primary-rgb, 232 98 42) / 0.1);
  border-left-color: var(--olo-ui-accent, #e8622a);
}
.st-row--active:hover {
  background: rgb(var(--olo-primary-rgb, 232 98 42) / 0.14);
}
/* Focus da tastiera visibile sulla riga (WCAG 2.4.7) */
.st-row:focus-visible {
  outline: 2px solid var(--olo-ui-accent, #e8622a);
  outline-offset: -2px;
}
/* Nodo "afferrato" in modalità spostamento da tastiera (grab-and-move) */
.st-row--grabbed {
  outline: 2px dashed var(--olo-ui-accent, #e8622a);
  outline-offset: -2px;
  background: rgb(var(--olo-primary-rgb, 232 98 42) / 0.08);
  box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.02) inset;
}
/* Live region / etichette solo per screen-reader (visivamente nascoste) */
.st-sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0 0 0 0);
  white-space: nowrap;
  border: 0;
}

/* Section row styling */
.st-row--section {
  font-weight: 500;
}

/* Drag grip */
.st-grip {
  flex-shrink: 0;
  width: 16px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: grab;
  color: #9CA3AF;
  transition: color 0.15s, background-color 0.15s;
  border-radius: 3px;
}
.st-grip:active {
  cursor: grabbing;
}
.st-row:hover .st-grip {
  color: #6B7280;
}
.st-grip:hover {
  color: #374151 !important;
  background: rgba(0, 0, 0, 0.06);
}
.st-grip-ph {
  flex-shrink: 0;
  width: 16px;
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
  color: #94A3B8;
  cursor: pointer;
  padding: 0;
  border-radius: 2px;
  transition: color 0.15s, transform 0.15s;
}
.st-toggle:hover {
  color: #475569;
}
.st-toggle--open {
  transform: rotate(90deg);
}
.st-toggle-ph {
  flex-shrink: 0;
  width: 14px;
}

/* Type icon */
.st-icon {
  flex-shrink: 0;
  width: 14px;
  height: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #94A3B8;
}
.st-row--active .st-icon {
  color: var(--olo-ui-accent, #e8622a);
}

/* Label */
.st-name {
  flex: 1;
  min-width: 0;
  font-size: 12px;
  color: #475569;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  line-height: 1;
  font-weight: 450;
}
.st-row--active .st-name {
  color: #1E293B;
  font-weight: 600;
}

/* Hover actions
 * v1.0.58 — fix: opacity 0 + transition causava race condition col click fisico.
 * Quando il mouse passava dal label al button X, in alcuni casi perdeva l'hover
 * sul .st-row e .st-actions tornava opacity 0 mid-click. Il click finiva sul
 * nome "Sezione" sotto invece che sul button Elimina, e la sezione veniva
 * solo selezionata (apertura Inspector) senza essere rimossa.
 * Soluzione: opacity bassa di default per indicare cliccabilità + pointer-events
 * sempre auto. Su hover full opacity. Niente più race. */
.st-actions {
  display: flex;
  gap: 1px;
  opacity: 0.35;
  transition: opacity 0.12s;
  flex-shrink: 0;
  pointer-events: auto;
}
.st-row:hover .st-actions,
.st-actions:hover {
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
  color: #94A3B8;
  cursor: pointer;
  border-radius: 3px;
  padding: 0;
  transition: color 0.1s, background-color 0.1s;
}
.st-actions button:hover {
  color: #1E293B;
  background: rgba(0, 0, 0, 0.06);
}

/* v1.0.62 — Drop indicator durante drag&drop nella Struttura tree.
 * Mostra una linea orizzontale brand orange in alto o in basso dell'item
 * a seconda di dove il drop avverrà (top/bottom edge), + leggero highlight
 * sull'intera riga per evidenziare quale item sta ricevendo il drop. */
.st-item.st-dnd-over {
  position: relative;
}
.st-item.st-dnd-over > .st-row {
  background: rgba(232, 98, 42, 0.08);
}
.st-item.st-dnd-over::after {
  content: "";
  position: absolute;
  left: 8px;
  right: 8px;
  height: 2px;
  background: #e8622a;
  border-radius: 2px;
  pointer-events: none;
  z-index: 10;
  box-shadow: 0 0 0 2px rgba(232, 98, 42, 0.25);
}
.st-item.st-dnd-over-top::after {
  top: -1px;
}
.st-item.st-dnd-over-bottom::after {
  bottom: -1px;
}
/* Fallback se per qualche motivo nessuna delle 2 edge classi è settata: linea in alto */
.st-item.st-dnd-over:not(.st-dnd-over-top):not(.st-dnd-over-bottom)::after {
  top: 0;
}

/* Subtree (children) — V2 indent guide tratteggiata */
.st-sub {
  margin-left: 18px;
  padding-left: 8px;
  position: relative;
}
.st-sub::before {
  content: "";
  position: absolute;
  left: 0;
  top: 2px;
  bottom: 6px;
  border-left: 1px dashed #d1d5db;
  pointer-events: none;
}
/* Hide the guide on the top-level sub of each zone (the zone bar already marks the column) */
.st-zone-root > .st-sub::before {
  display: none;
}

/* Dropzone for elements */
.st-dropzone {
  min-height: 20px;
  border: 1px dashed transparent;
  border-radius: 4px;
  transition: border-color 0.15s, background 0.15s;
}
.st-dropzone:empty {
  min-height: 24px;
  border-color: rgba(232, 98, 42, 0.2);
  background: rgba(232, 98, 42, 0.03);
}
.st-dropzone:empty::after {
  content: 'Trascina qui';
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  font-size: 9px;
  color: #6b7280;
  opacity: 0.5;
}

/* Drag ghost */
.st-ghost {
  opacity: 0.3;
}

/* Zone root nodes (unified editing) — V2 color-coded macro areas */
.st-zone-root {
  margin-bottom: 6px;
  border-radius: 8px;
  overflow: hidden;
  position: relative;
}
.st-zone-root::before {
  content: "";
  position: absolute;
  left: 0;
  top: 4px;
  bottom: 4px;
  width: 3px;
  border-radius: 0 3px 3px 0;
  background: var(--ot-text-muted, #64748b);
  z-index: 1;
}
.st-zone-root--header { background: #eff6ff; }
.st-zone-root--header::before { background: #3b82f6; }
.st-zone-root--body   { background: #faf5ff; }
.st-zone-root--body::before { background: #a855f7; }
.st-zone-root--footer { background: #f0fdf4; }
.st-zone-root--footer::before { background: #22c55e; }

.st-row--zone {
  height: 30px;
  padding: 0 8px 0 12px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: background-color 0.12s;
}
.st-row--zone:hover {
  background: rgba(0, 0, 0, 0.03);
}
.st-row--zone-active {
  background: rgba(0, 0, 0, 0.04);
}

/* Header zone tone overrides for the row label/icon */
.st-zone-root--header .st-row--zone .st-name { color: #1d4ed8; }
.st-zone-root--body   .st-row--zone .st-name { color: #7e22ce; }
.st-zone-root--footer .st-row--zone .st-name { color: #15803d; }

/* Override hardcoded indigo on selected — use brand orange */
.st-row--active {
  background: rgba(232, 98, 42, 0.08);
  border-left-color: var(--olo-color-primary, #e8622a);
}
.st-row--active:hover {
  background: rgba(232, 98, 42, 0.12);
}
.st-row--active .st-icon {
  color: var(--olo-color-primary, #e8622a);
}
</style>
