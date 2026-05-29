<template>
  <div ref="stRoot" class="st-root">
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
                <div class="st-row st-row--section" :class="{ 'st-row--active': builderStore.selectedTileId === section.id }" @click="selectTile(section.id, 'header')">
                  <span class="st-grip" :title="t('Trascina')"><svg width="6" height="10" viewBox="0 0 6 10" fill="currentColor"><circle cx="1" cy="1" r="1"/><circle cx="5" cy="1" r="1"/><circle cx="1" cy="5" r="1"/><circle cx="5" cy="5" r="1"/><circle cx="1" cy="9" r="1"/><circle cx="5" cy="9" r="1"/></svg></span>
                  <button class="st-toggle" :class="{ 'st-toggle--open': isExpanded(section.id) }" @click.stop="toggle(section.id)"><svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><path d="M2 1l4 3-4 3z"/></svg></button>
                  <span class="st-icon" v-html="nodeIcon('section')"></span>
                  <span class="st-name" :title="section.settings?._label || 'Sezione'">{{ section.settings?._label || 'Sezione' }}</span>
                </div>
                <div v-if="isExpanded(section.id)" class="st-sub" style="margin-left:10px;padding-left:5px">
                  <div class="st-list" v-olo-drop-target="listEndDrop('rows', section.id)">
                    <template v-for="(row, rIdx) in (section.children || [])" :key="row.id">
                      <div v-show="isVisible(row.id)" class="st-item" v-olo-draggable="rowDraggable(row, section.id, rIdx)" v-olo-drop-target="rowDrop(row, section.id, rIdx)">
                        <div class="st-row st-row--row" :class="{ 'st-row--active': builderStore.selectedTileId === row.id }" @click.stop="selectTile(row.id, 'header')">
                          <span class="st-grip" :title="t('Trascina')"><svg width="6" height="10" viewBox="0 0 6 10" fill="currentColor"><circle cx="1" cy="1" r="1"/><circle cx="5" cy="1" r="1"/><circle cx="1" cy="5" r="1"/><circle cx="5" cy="5" r="1"/><circle cx="1" cy="9" r="1"/><circle cx="5" cy="9" r="1"/></svg></span>
                          <button class="st-toggle" :class="{ 'st-toggle--open': isExpanded(row.id) }" @click.stop="toggle(row.id)"><svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><path d="M2 1l4 3-4 3z"/></svg></button>
                          <span class="st-icon" v-html="nodeIcon('row')"></span>
                          <span class="st-name">{{ row.settings?._label || 'Row' }}</span>
                        </div>
                        <div v-if="isExpanded(row.id)" class="st-sub" style="margin-left:10px;padding-left:5px">
                          <div v-for="col in (row.children || [])" v-show="isVisible(col.id)" :key="col.id" class="st-item">
                            <div class="st-row st-row--column" :class="{ 'st-row--active': builderStore.selectedTileId === col.id }" @click.stop="selectTile(col.id, 'header')">
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
                                    <div class="st-row st-row--element" :class="{ 'st-row--active': builderStore.selectedTileId === tile.id }" @click.stop="selectTile(tile.id, 'header')">
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
          <span v-if="builderStore.isDirty" style="width:6px;height:6px;border-radius:50%;background:#FBBF24;flex-shrink:0" title="Modifiche non salvate"></span>
        </div>
        <div :class="{ 'st-sub': builderStore.unifiedMode }" :style="builderStore.unifiedMode ? 'margin-left:10px;padding-left:5px' : ''" v-show="!builderStore.unifiedMode || isExpanded('__zone_body')">

      <!-- Sections -->
      <div class="st-list" v-olo-drop-target="listEndDrop('sections', null)">
        <template v-for="(section, sIdx) in tilesStore.canvasTiles" :key="section.id">
          <div v-show="isVisible(section.id)" class="st-item" v-olo-draggable="sectionDraggable(section, sIdx)" v-olo-drop-target="sectionDrop(section, sIdx)">
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
              <button class="st-toggle" :class="{ 'st-toggle--open': isExpanded(section.id) }" aria-label="Espandi/comprimi sezione" @click.stop="toggle(section.id)">
                <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><path d="M2 1l4 3-4 3z"/></svg>
              </button>
              <span class="st-icon" v-html="nodeIcon('section')"></span>
              <input v-if="renamingId === section.id" class="st-rename-input" :style="renameInputStyle" :value="renameValue" @input="renameValue = $event.target.value" @keydown.enter.prevent="confirmRename(section)" @keydown.escape="cancelRename()" @blur="confirmRename(section)" @click.stop />
              <span v-else class="st-name" @click.stop="onNameClick(section)" :title="section.settings?._label || 'Sezione'">{{ section.settings?._label || 'Sezione' }}</span>
              <span class="st-actions">
                <button title="Duplica" aria-label="Duplica" @click.stop="duplicate(section.id)">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="9" y="9" width="13" height="13" rx="2"/>
                    <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
                  </svg>
                </button>
                <button title="Salva come template" aria-label="Salva come template" @click.stop="emit('save-as-template', section)">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                  </svg>
                </button>
                <button title="Elimina" aria-label="Elimina" @click.stop="remove(section.id)">
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
                      <button class="st-toggle" :class="{ 'st-toggle--open': isExpanded(row.id) }" aria-label="Espandi/comprimi riga" @click.stop="toggle(row.id)">
                        <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><path d="M2 1l4 3-4 3z"/></svg>
                      </button>
                      <span class="st-icon" v-html="nodeIcon('row')"></span>
                      <input v-if="renamingId === row.id" class="st-rename-input" :style="renameInputStyle" :value="renameValue" @input="renameValue = $event.target.value" @keydown.enter.prevent="confirmRename(row)" @keydown.escape="cancelRename()" @blur="confirmRename(row)" @click.stop />
                      <span v-else class="st-name" @click.stop="onNameClick(row)" :title="row.settings?._label || 'Row'">{{ row.settings?._label || 'Row' }}</span>
                      <span class="st-actions">
                        <button title="Duplica" aria-label="Duplica" @click.stop="duplicate(row.id)">
                          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="9" y="9" width="13" height="13" rx="2"/>
                            <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
                          </svg>
                        </button>
                        <button title="Elimina" aria-label="Elimina" @click.stop="remove(row.id)">
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
                          :class="{ 'st-row--active': builderStore.selectedTileId === col.id }"
                          @click.stop="selectTile(col.id)"
                        >
                          <span class="st-grip-ph"></span>
                          <button
                            class="st-toggle"
                            :class="{ 'st-toggle--open': isExpanded(col.id) }"
                            aria-label="Espandi/comprimi colonna"
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
                                  <button
                                    v-if="tile.type === 'inner-columns' && tile.children?.length"
                                    class="st-toggle"
                                    :class="{ 'st-toggle--open': isExpanded(tile.id) }"
                                    aria-label="Espandi/comprimi elemento"
                                    @click.stop="toggle(tile.id)"
                                  >
                                    <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><path d="M2 1l4 3-4 3z"/></svg>
                                  </button>
                                  <span v-else class="st-toggle-ph"></span>
                                  <span class="st-icon" v-html="nodeIcon(tile.type)"></span>
                                  <input v-if="renamingId === tile.id" class="st-rename-input" :style="renameInputStyle" :value="renameValue" @input="renameValue = $event.target.value" @keydown.enter.prevent="confirmRename(tile)" @keydown.escape="cancelRename()" @blur="confirmRename(tile)" @click.stop />
                                  <span v-else class="st-name" @click.stop="onNameClick(tile)" :title="tileLabelFull(tile)">{{ tileLabel(tile) }}</span>
                                  <span class="st-actions">
                                    <button title="Duplica" aria-label="Duplica" @click.stop="duplicate(tile.id)">
                                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="9" y="9" width="13" height="13" rx="2"/>
                                        <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
                                      </svg>
                                    </button>
                                    <button title="Elimina" aria-label="Elimina" @click.stop="remove(tile.id)">
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
                                      :class="{ 'st-row--active': builderStore.selectedTileId === icol.id }"
                                      @click.stop="selectTile(icol.id)"
                                    >
                                      <span class="st-grip-ph"></span>
                                      <button
                                        v-if="icol.children && icol.children.length > 0"
                                        class="st-toggle"
                                        :class="{ 'st-toggle--open': isExpanded(icol.id) }"
                                        aria-label="Espandi/comprimi colonna interna"
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
                                            :class="{ 'st-row--active': builderStore.selectedTileId === innerTile.id }"
                                            @click.stop="selectTile(innerTile.id)"
                                            v-olo-draggable="elementDraggable(innerTile, icol.id, iIdx)"
                                            v-olo-drop-target="elementDrop(innerTile, icol.id, iIdx)"
                                          >
                                            <span class="st-grip" title="Trascina per riordinare">
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
                                              <button title="Duplica" aria-label="Duplica" @click.stop="duplicate(innerTile.id)">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                  <rect x="9" y="9" width="13" height="13" rx="2"/>
                                                  <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
                                                </svg>
                                              </button>
                                              <button title="Elimina" aria-label="Elimina" @click.stop="remove(innerTile.id)">
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
          <span v-if="builderStore.footerDirty" style="width:6px;height:6px;border-radius:50%;background:#FBBF24;flex-shrink:0" title="Modifiche non salvate"></span>
        </div>
        <div v-if="isExpanded('__zone_footer')" class="st-sub" style="margin-left:10px;padding-left:5px">
          <div class="st-list" v-olo-drop-target="listEndDrop('sections', null)">
            <template v-for="(section, sIdx) in tilesStore.footerTiles" :key="section.id">
              <div v-show="isVisible(section.id)" class="st-item" v-olo-draggable="sectionDraggable(section, sIdx)" v-olo-drop-target="sectionDrop(section, sIdx)">
                <div class="st-row st-row--section" :class="{ 'st-row--active': builderStore.selectedTileId === section.id }" @click="selectTile(section.id, 'footer')">
                  <span class="st-grip" title="Trascina"><svg width="6" height="10" viewBox="0 0 6 10" fill="currentColor"><circle cx="1" cy="1" r="1"/><circle cx="5" cy="1" r="1"/><circle cx="1" cy="5" r="1"/><circle cx="5" cy="5" r="1"/><circle cx="1" cy="9" r="1"/><circle cx="5" cy="9" r="1"/></svg></span>
                  <button class="st-toggle" :class="{ 'st-toggle--open': isExpanded(section.id) }" @click.stop="toggle(section.id)"><svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><path d="M2 1l4 3-4 3z"/></svg></button>
                  <span class="st-icon" v-html="nodeIcon('section')"></span>
                  <span class="st-name" :title="section.settings?._label || 'Sezione'">{{ section.settings?._label || 'Sezione' }}</span>
                </div>
                <div v-if="isExpanded(section.id)" class="st-sub" style="margin-left:10px;padding-left:5px">
                  <div class="st-list" v-olo-drop-target="listEndDrop('rows', section.id)">
                    <template v-for="(row, rIdx) in (section.children || [])" :key="row.id">
                      <div v-show="isVisible(row.id)" class="st-item" v-olo-draggable="rowDraggable(row, section.id, rIdx)" v-olo-drop-target="rowDrop(row, section.id, rIdx)">
                        <div class="st-row st-row--row" :class="{ 'st-row--active': builderStore.selectedTileId === row.id }" @click.stop="selectTile(row.id, 'footer')">
                          <span class="st-grip" title="Trascina"><svg width="6" height="10" viewBox="0 0 6 10" fill="currentColor"><circle cx="1" cy="1" r="1"/><circle cx="5" cy="1" r="1"/><circle cx="1" cy="5" r="1"/><circle cx="5" cy="5" r="1"/><circle cx="1" cy="9" r="1"/><circle cx="5" cy="9" r="1"/></svg></span>
                          <button class="st-toggle" :class="{ 'st-toggle--open': isExpanded(row.id) }" @click.stop="toggle(row.id)"><svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><path d="M2 1l4 3-4 3z"/></svg></button>
                          <span class="st-icon" v-html="nodeIcon('row')"></span>
                          <span class="st-name">{{ row.settings?._label || 'Row' }}</span>
                        </div>
                        <div v-if="isExpanded(row.id)" class="st-sub" style="margin-left:10px;padding-left:5px">
                          <div v-for="col in (row.children || [])" v-show="isVisible(col.id)" :key="col.id" class="st-item">
                            <div class="st-row st-row--column" :class="{ 'st-row--active': builderStore.selectedTileId === col.id }" @click.stop="selectTile(col.id, 'footer')">
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
                                    <div class="st-row st-row--element" :class="{ 'st-row--active': builderStore.selectedTileId === tile.id }" @click.stop="selectTile(tile.id, 'footer')">
                                      <span class="st-grip" title="Trascina"><svg width="6" height="10" viewBox="0 0 6 10" fill="currentColor"><circle cx="1" cy="1" r="1"/><circle cx="5" cy="1" r="1"/><circle cx="1" cy="5" r="1"/><circle cx="5" cy="5" r="1"/><circle cx="1" cy="9" r="1"/><circle cx="5" cy="9" r="1"/></svg></span>
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
import { loadScrollFlashPrefs } from '@/utils/scrollFlashPrefs';
import { requestScrollToTile } from '@/utils/scrollToTileChannel';
import {
  vOloDraggable,
  vOloDropTarget,
  attachClosestEdge,
  extractClosestEdge,
  makeNodePayload,
  isOloData,
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
const stRoot = ref(null);

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
    osmmap: '<svg width="14" height="14"'+V+S+'><path d="M7 12S3 8.5 3 5.5a4 4 0 018 0C11 8.5 7 12 7 12z"/><circle cx="7" cy="5.5" r="1.5"/></svg>',
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
    starrating: '<svg width="14" height="14"'+V+S+'><path d="M4 7l.5 1 1 .2-.8.8.2 1.2L4 9.5l-1 .7.2-1.2-.8-.8 1-.2z" fill="currentColor"/><path d="M7 7l.5 1 1 .2-.8.8.2 1.2L7 9.5l-1 .7.2-1.2-.8-.8 1-.2z" fill="currentColor"/><path d="M10 7l.5 1 1 .2-.8.8.2 1.2-1-.7-1 .7.2-1.2-.8-.8 1-.2z"/></svg>',
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
    sharebuttons: '<svg width="14" height="14"'+V+S+'><circle cx="4" cy="7" r="1.5"/><circle cx="10" cy="4" r="1.5"/><circle cx="10" cy="10" r="1.5"/><line x1="5.3" y1="6.3" x2="8.7" y2="4.7"/><line x1="5.3" y1="7.7" x2="8.7" y2="9.3"/></svg>',
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
  tilesStore.removeTile(id);
  if (builderStore.selectedTileId === id) builderStore.deselectTile();
  builderStore.isDirty = true;
}

function onZoneClick(zone) {
  builderStore.setActiveZone(zone);
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
        return p.nodeKind === nodeKind && p.fromParentId === parentId && p.nodeId !== node.id;
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
    el.style.boxShadow = '0 0 0 2px var(--olo-color-primary, #6366F1)';
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
  color: #6366F1;
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
  border-color: rgba(99, 102, 241, 0.2);
  background: rgba(99, 102, 241, 0.03);
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
