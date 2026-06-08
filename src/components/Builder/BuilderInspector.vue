<template>
  <transition name="slide">
    <div
      v-if="selectedTile || builderStore.pageSettingsOpen"
      class="v2i-root mb-shrink-0"
    >
      <!-- Page Settings (no tile selected): plain scroll, no rail -->
      <div v-if="builderStore.pageSettingsOpen && !selectedTile" class="v2i-page-only">
        <div class="mb-p-4">
          <PageSettingsPanel />
        </div>
      </div>

      <!-- Tile Inspector V2 -->
      <template v-else-if="selectedTile">
      <!-- ─── HEAD (sticky) ────────────────────────────────────── -->
      <div class="v2i-head">
        <!-- Zone badge (unified editing) -->
        <div v-if="builderStore.unifiedMode && tileZone" class="mb-mb-2 mb-flex mb-items-center mb-gap-1.5">
          <span class="mb-text-[10px] mb-font-semibold mb-uppercase mb-tracking-wide mb-px-1.5 mb-py-0.5 mb-rounded"
            :class="{
              'mb-bg-blue-500/15 mb-text-blue-400': tileZone === 'header',
              'mb-bg-purple-500/15 mb-text-purple-400': tileZone === 'body',
              'mb-bg-emerald-500/15 mb-text-emerald-400': tileZone === 'footer',
            }"
          >{{ tileZone === 'body' ? 'Body' : tileZone === 'header' ? 'Header' : 'Footer' }}</span>
        </div>
        <!-- Breadcrumb -->
        <div v-if="ancestorPath.length > 1" class="mb-mb-2 mb-flex mb-items-center mb-flex-wrap mb-gap-0.5 mb-text-[10px]">
          <template v-for="(crumb, idx) in ancestorPath" :key="crumb.id">
            <span v-if="idx > 0" class="mb-text-gray-600">›</span>
            <button
              v-if="idx < ancestorPath.length - 1"
              @click="builderStore.selectTile(crumb.id)"
              class="mb-text-gray-500 hover:mb-text-primary-400 mb-transition-colors mb-cursor-pointer mb-truncate mb-max-w-[80px]"
              :title="crumbLabel(crumb)"
            >{{ crumbLabel(crumb) }}</button>
            <span
              v-else
              class="mb-text-gray-300 mb-font-medium mb-truncate mb-max-w-[80px]"
              :title="crumbLabel(crumb)"
            >{{ crumbLabel(crumb) }}</span>
          </template>
        </div>

        <!-- Header -->
        <div class="mb-flex mb-items-center mb-justify-between mb-mb-2">
          <h3 class="mb-text-sm mb-font-semibold mb-text-gray-200 mb-flex-1 mb-truncate">
            {{ t('Impostazioni') }} {{ elementDef ? t(elementDef.name) : selectedTile.type }}
          </h3>
          <div class="mb-flex mb-items-center mb-gap-1">
            <button
              @click="onCopyStyle"
              class="insp-action-btn"
              :title="t('Copia stile (Ctrl+Alt+C)')"
            >
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
            </button>
            <button
              @click="onPasteStyle"
              :disabled="!hasClipboardStyle"
              class="insp-action-btn"
              :class="{ 'insp-action-btn--disabled': !hasClipboardStyle }"
              :title="t('Incolla stile (Ctrl+Alt+V)')"
            >
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
            </button>
            <button
              @click="onSaveAsPreset"
              class="insp-action-btn"
              :title="t('Salva tile come preset')"
            >
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
            </button>
            <div class="insp-preset-wrap" v-if="tilePresetsForType.length">
              <button
                @click="presetMenuOpen = !presetMenuOpen"
                class="insp-action-btn"
                :title="t('Applica preset salvato')"
              >
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
              </button>
              <div v-if="presetMenuOpen" v-click-outside="() => presetMenuOpen = false" class="insp-preset-menu">
                <div class="insp-preset-menu-title">{{ t('Preset salvati') }} ({{ selectedTile.type }})</div>
                <button
                  v-for="p in tilePresetsForType"
                  :key="p.id"
                  @click="applyPreset(p); presetMenuOpen = false"
                  class="insp-preset-item"
                >
                  <span class="mb-flex-1 mb-truncate">{{ p.name }}</span>
                  <span @click.stop="deletePreset(p.id)" class="insp-preset-del" :title="t('Elimina')">&times;</span>
                </button>
              </div>
            </div>
            <button
              @click="builderStore.deselectTile()"
              class="mb-text-gray-500 hover:mb-text-gray-300 mb-text-lg mb-px-1"
              :title="t('Chiudi')"
            >&times;</button>
          </div>
        </div>

        <!-- Tile state badges (hover/responsive/cond/animation/sticky) -->
        <div v-if="tileBadges.length" class="mb-flex mb-flex-wrap mb-gap-1 mb-mb-3">
          <button
            v-for="b in tileBadges"
            :key="b.id"
            @click="activeTab = b.target"
            class="insp-badge"
            :class="`insp-badge--${b.color}`"
            :title="t('Vai a ') + b.target"
          >{{ t(b.label) }}</button>
        </div>

        <!-- Search settings + hover-state toggle -->
        <div class="v2i-search-row">
          <div class="insp-search">
            <svg class="insp-search-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input v-model="settingsSearch" class="insp-search-input" type="text" :placeholder="t('Cerca impostazione...')" />
            <button
              @click="showOnlyModified = !showOnlyModified"
              class="insp-filter-modified"
              :class="{ 'insp-filter-modified--active': showOnlyModified }"
              :title="t('Mostra solo i campi modificati rispetto al default')"
            >
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3" fill="currentColor"/>
              </svg>
            </button>
            <button v-if="settingsSearch" class="insp-search-clear" @click="settingsSearch = ''">&times;</button>
          </div>
          <button
            type="button"
            class="v2i-hover-toggle"
            :class="{ 'on': builderStore.editingHover }"
            :title="builderStore.editingHover ? t('Chiudi controlli hover di tutti i field') : t('Apri controlli hover di tutti i field')"
            @click="builderStore.editingHover = !builderStore.editingHover"
          >
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
          </button>
        </div>

        <!-- Tabs (V2 pill segmented) -->
        <div class="v2i-tabs" role="tablist" :aria-label="t('Inspector')">
          <button
            v-for="tab in tabs"
            :key="tab"
            @click="onTabChange(tab)"
            @keydown.arrow-right.prevent="navigateTab(1)"
            @keydown.arrow-left.prevent="navigateTab(-1)"
            role="tab"
            :id="'inspector-tab-' + tab"
            :aria-selected="activeTab === tab"
            :aria-controls="'inspector-panel-' + tab"
            :tabindex="activeTab === tab ? 0 : -1"
            :class="{ 'on': activeTab === tab }"
          >
            {{ t(tab) }}
          </button>
        </div>
      </div>
      <!-- ─── /HEAD ────────────────────────────────────────────── -->

      <!-- ─── BODY: panel + rail ───────────────────────────────── -->
      <div class="v2i-body">
        <div ref="contentRef" class="v2i-content" :class="{ 'is-hover-mode': builderStore.editingHover }">

        <!-- ============ Content tab (data-driven) ============ -->
        <div v-if="activeTab === 'Contenuto'" class="mb-space-y-3" role="tabpanel" id="inspector-panel-Contenuto" :aria-labelledby="'inspector-tab-Contenuto'">
          <!-- Custom editor: ProSlider -->
          <div v-if="elementDef?.customEditor === 'proslider'" class="mb-space-y-3">
            <p class="mb-text-xs mb-text-gray-400">Configura slide, livelli e animazioni nell'editor visuale.</p>
            <button
              @click="showProSliderEditor = true"
              class="mb-w-full mb-py-2.5 mb-bg-primary-600 mb-text-white mb-text-sm mb-font-semibold mb-rounded-lg hover:mb-bg-primary-500 mb-transition-colors"
            >
              Apri editor slider
            </button>
            <p class="mb-text-[10px] mb-text-gray-400">{{ (selectedTile.settings?.slides || []).length }} slide configurate</p>

            <!-- Height mode selector -->
            <HeightModeSelector
              :settings="selectedTile.settings || {}"
              @update="updateSetting"
            />
          </div>

          <template v-if="elementFields.length > 0">
            <template v-for="(section, sIdx) in groupedSections" :key="'sec-' + sIdx">
              <!-- Section without label (fields before first separator) — always open -->
              <template v-if="section.label === null">
                <template v-for="(field, fIdx) in section.fields" :key="field.key || ('f-' + sIdx + '-' + fIdx)">
                  <template v-if="isFieldVisible(field)">
                    <div v-if="field.type === 'content-items'">
                      <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t(field.label) }}</label>
                      <ContentItemsEditor
                        :modelValue="ensureContentItems(selectedTile, field)"
                        :itemFields="field.itemFields"
                        :newItemDefaults="field.newItemDefaults || {}"
                        :itemLabel="field.itemLabel || 'Item'"
                        :tileId="selectedTile.id"
                        :tileSettings="selectedTile.settings || {}"
                        :supportsDynamic="field.supportsDynamic || false"
                        :dynamic="selectedTile.dynamic || {}"
                        @update:modelValue="updateSetting(field.key, $event)"
                        @update:dynamic-query="updateDynamicQuery"
                        @update:dynamic-item-map="updateDynamicItemMap"
                      />
                    </div>
                    <div v-else-if="field.key === 'custom_widths' && selectedTile.type === 'row'">
                      <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t(field.label) }}</label>
                      <input
                        type="text"
                        :value="customWidthsLocal"
                        :placeholder="field.placeholder || ''"
                        class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
                        @input="customWidthsLocal = $event.target.value"
                        @focus="onCustomWidthsFocus"
                        @keydown.enter="applyCustomWidthsFromInspector"
                        @blur="applyCustomWidthsFromInspector"
                      />
                    </div>
                    <InspectorField
                      v-else
                      :field="resolveField(field)"
                      :modelValue="field.scope === 'style' ? selectedTile.style?.[field.key] : selectedTile.settings?.[field.key]"
                      :tileSettings="selectedTile.settings || {}"
                      :tileId="selectedTile.id"
                      :dynamic="selectedTile.dynamic || {}"
                      @update:modelValue="field.scope === 'style' ? updateStyle(field.key, $event) : updateSetting(field.key, $event)"
                      @update:responsiveValue="updateSetting($event.key, $event.value)"
                      @update:hoverValue="updateSetting($event.key, $event.value)"
                      @update:settingKey="updateSetting($event.key, $event.value)"
                      @update:attachmentId="updateSetting(field.key + '_id', $event)"
                      @update:dynamic="onDynamicFieldUpdate"
                    />
                  </template>
                </template>
              </template>

              <!-- Named section — collapsible, hidden if no visible fields -->
              <CollapseSection
                v-else-if="sectionHasVisibleFields(section)"
                :id="contentSectionId(sIdx)"
                :title="t(section.label)"
                :defaultOpen="sIdx <= 1"
              >
                <template v-for="(field, fIdx) in section.fields" :key="field.key || ('f-' + sIdx + '-' + fIdx)">
                  <template v-if="isFieldVisible(field)">
                    <div v-if="field.type === 'content-items'">
                      <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t(field.label) }}</label>
                      <ContentItemsEditor
                        :modelValue="ensureContentItems(selectedTile, field)"
                        :itemFields="field.itemFields"
                        :newItemDefaults="field.newItemDefaults || {}"
                        :itemLabel="field.itemLabel || 'Item'"
                        :tileId="selectedTile.id"
                        :tileSettings="selectedTile.settings || {}"
                        :supportsDynamic="field.supportsDynamic || false"
                        :dynamic="selectedTile.dynamic || {}"
                        @update:modelValue="updateSetting(field.key, $event)"
                        @update:dynamic-query="updateDynamicQuery"
                        @update:dynamic-item-map="updateDynamicItemMap"
                      />
                    </div>
                    <div v-else-if="field.key === 'custom_widths' && selectedTile.type === 'row'">
                      <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t(field.label) }}</label>
                      <input
                        type="text"
                        :value="customWidthsLocal"
                        :placeholder="field.placeholder || ''"
                        class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
                        @input="customWidthsLocal = $event.target.value"
                        @focus="onCustomWidthsFocus"
                        @keydown.enter="applyCustomWidthsFromInspector"
                        @blur="applyCustomWidthsFromInspector"
                      />
                    </div>
                    <InspectorField
                      v-else
                      :field="resolveField(field)"
                      :modelValue="field.scope === 'style' ? selectedTile.style?.[field.key] : selectedTile.settings?.[field.key]"
                      :tileSettings="selectedTile.settings || {}"
                      :tileId="selectedTile.id"
                      :dynamic="selectedTile.dynamic || {}"
                      @update:modelValue="field.scope === 'style' ? updateStyle(field.key, $event) : updateSetting(field.key, $event)"
                      @update:responsiveValue="updateSetting($event.key, $event.value)"
                      @update:hoverValue="updateSetting($event.key, $event.value)"
                      @update:settingKey="updateSetting($event.key, $event.value)"
                      @update:attachmentId="updateSetting(field.key + '_id', $event)"
                      @update:dynamic="onDynamicFieldUpdate"
                    />
                  </template>
                </template>
              </CollapseSection>
            </template>
          </template>

          <!-- Fallback: auto-generated fields for elements without definition -->
          <template v-else>
            <div v-for="(value, key) in filteredSettings" :key="key">
              <InspectorField
                :field="inferField(key, value)"
                :modelValue="value"
                @update:modelValue="updateSetting(key, $event)"
                @update:attachmentId="updateSetting(key + '_id', $event)"
              />
            </div>
          </template>
        </div>

        <!-- ============ Style tab ============ -->
        <div v-else-if="activeTab === 'Stile'" class="mb-space-y-4" role="tabpanel" id="inspector-panel-Stile" :aria-labelledby="'inspector-tab-Stile'">
          <StyleFieldsRenderer
            :tileStyle="tileStyle"
            :tileFields="elementDef?.styleFields || []"
            :tileSettings="selectedTile.settings || {}"
            :tileType="selectedTile.type"
            @update="onStyleUpdate"
          />
        </div>

        <!-- ============ Advanced tab ============ -->
        <div v-else class="mb-space-y-4" role="tabpanel" id="inspector-panel-Avanzate" :aria-labelledby="'inspector-tab-Avanzate'">
          <!-- ===== MACRO: Identificatori ===== -->
          <CollapseSection id="v2i-sec-adv-id" title="Identificatori" :defaultOpen="true" :macro="true">
            <div class="mb-space-y-3">
          <!-- HTML ID -->
          <div>
            <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-1">HTML ID</label>
            <input
              type="text"
              :value="tileAdvanced.html_id || ''"
              @input="updateAdvanced('html_id', $event.target.value)"
              placeholder="my-section"
              class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
            />
          </div>

          <!-- CSS Classes -->
          <div>
            <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-1">Classi CSS</label>
            <input
              type="text"
              :value="tileAdvanced.css_classes || ''"
              @input="updateAdvanced('css_classes', $event.target.value)"
              placeholder="my-class another-class"
              class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
            />
          </div>

          <!-- Custom CSS -->
          <div>
            <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-1">CSS personalizzato</label>
            <textarea
              :value="tileAdvanced.custom_css || ''"
              @input="updateAdvanced('custom_css', $event.target.value)"
              rows="4"
              placeholder="color: red;&#10;transform: rotate(2deg);"
              class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-xs mb-text-gray-900 mb-font-mono mb-resize-y"
            />
            <p class="mb-text-[10px] mb-text-gray-400 mb-mt-1">Proprietà CSS applicate direttamente al wrapper della tile</p>
          </div>
            </div>
          </CollapseSection>

          <!-- ===== MACRO: Visibilità & Condizioni ===== -->
          <CollapseSection id="v2i-sec-adv-visibility" title="Visibilità & Condizioni" :macro="true">
            <div class="mb-space-y-3">
          <!-- Visibility -->
          <div>
            <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">Visibilità</label>
            <div class="mb-flex mb-items-center mb-gap-2">
            <label v-for="vp in viewports" :key="vp.key" class="mb-flex mb-items-center mb-gap-1 mb-cursor-pointer" :title="(tileAdvanced['visible_' + vp.key] !== false ? 'Visibile su ' : 'Nascosto su ') + vp.label">
              <input
                type="checkbox"
                :checked="tileAdvanced['visible_' + vp.key] !== false"
                @change="updateAdvanced('visible_' + vp.key, $event.target.checked)"
                class="mb-hidden"
              />
              <span
                :class="[
                  'mb-flex mb-items-center mb-justify-center mb-w-8 mb-h-8 mb-rounded-md mb-border mb-transition-all',
                  tileAdvanced['visible_' + vp.key] !== false
                    ? 'mb-border-primary-500 mb-bg-primary-600/20 mb-text-primary-300'
                    : 'mb-border-gray-600 mb-bg-gray-700/50 mb-text-gray-500'
                ]"
                v-html="vp.svg"
              ></span>
            </label>
            </div>
          </div>

          <!-- Conditional Visibility -->
          <CollapseSection title="Visibilità condizionale">
            <div class="mb-space-y-3">
              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Mostra solo a</label>
                <select
                  :value="tileAdvanced.cond_user_role || ''"
                  @change="updateAdvanced('cond_user_role', $event.target.value)"
                  class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
                >
                  <option value="">Tutti (nessun filtro)</option>
                  <option value="logged_in">Utenti autenticati</option>
                  <option value="logged_out">Visitatori non autenticati</option>
                  <option value="administrator">Amministratori</option>
                  <option value="editor">Editor</option>
                  <option value="author">Autori</option>
                  <option value="subscriber">Subscriber</option>
                </select>
              </div>
              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Mostra da data</label>
                <input
                  type="datetime-local"
                  :value="tileAdvanced.cond_show_from || ''"
                  @change="updateAdvanced('cond_show_from', $event.target.value)"
                  class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
                />
              </div>
              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Nascondi dopo data</label>
                <input
                  type="datetime-local"
                  :value="tileAdvanced.cond_show_until || ''"
                  @change="updateAdvanced('cond_show_until', $event.target.value)"
                  class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
                />
              </div>
              <div v-if="singlePostItems.length > 0">
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Mostra solo su queste strutture</label>
                <div class="mb-max-h-40 mb-overflow-y-auto mb-border mb-border-gray-300 mb-rounded-md mb-bg-white mb-p-1.5 mb-space-y-0.5">
                  <label
                    v-for="item in singlePostItems"
                    :key="item.value"
                    class="mb-flex mb-items-center mb-gap-2 mb-px-1.5 mb-py-1 mb-rounded mb-cursor-pointer hover:mb-bg-gray-100 mb-text-sm mb-text-gray-900"
                  >
                    <input
                      type="checkbox"
                      :checked="(tileAdvanced.cond_post_ids || []).includes(item.value)"
                      @change="toggleCondPostId(item.value)"
                      class="mb-w-3.5 mb-h-3.5 mb-rounded mb-accent-primary-600"
                    />
                    <span class="mb-truncate">{{ item.label }}</span>
                  </label>
                </div>
                <button
                  v-if="(tileAdvanced.cond_post_ids || []).length > 0"
                  @click="updateAdvanced('cond_post_ids', [])"
                  class="mb-mt-1 mb-text-[10px] mb-text-red-400 hover:mb-text-red-300 mb-cursor-pointer"
                >Rimuovi filtro strutture</button>
              </div>
              <p class="mb-text-[10px] mb-text-gray-500">Condizioni verificate server-side al momento del rendering.</p>
            </div>
          </CollapseSection>

          <!-- A/B Testing -->
          <CollapseSection title="A/B Testing">
            <div class="mb-space-y-3">
              <!-- Loading -->
              <div v-if="abLoading" class="mb-text-xs mb-text-gray-400 mb-text-center mb-py-2">Caricamento...</div>

              <!-- No test: create button -->
              <template v-else-if="!abTest">
                <button
                  @click="createAbTest"
                  class="mb-w-full mb-bg-primary-600 mb-text-white mb-text-xs mb-font-medium mb-py-2 mb-px-3 mb-rounded-md hover:mb-bg-primary-700 mb-transition-colors"
                >Crea test A/B</button>
                <p class="mb-text-[10px] mb-text-gray-500">Confronta due varianti di questa tile per scoprire quale converte meglio.</p>
              </template>

              <!-- Test exists -->
              <template v-else>
                <!-- Name -->
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Nome test</label>
                  <input
                    type="text"
                    :value="abTest.name"
                    @change="updateAbField('name', $event.target.value)"
                    :disabled="abTest.status === 'running'"
                    class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
                  />
                </div>

                <!-- Status badge -->
                <div class="mb-flex mb-items-center mb-gap-2">
                  <span
                    :class="[
                      'mb-text-[10px] mb-font-bold mb-uppercase mb-px-2 mb-py-0.5 mb-rounded-full',
                      abTest.status === 'running' ? 'mb-bg-green-100 mb-text-green-700' :
                      abTest.status === 'stopped' ? 'mb-bg-red-100 mb-text-red-700' :
                      'mb-bg-gray-100 mb-text-gray-600'
                    ]"
                  >{{ abTest.status === 'running' ? 'Attivo' : abTest.status === 'stopped' ? 'Fermato' : 'Bozza' }}</span>
                </div>

                <!-- Variant B overrides (only in draft) -->
                <template v-if="abTest.status === 'draft'">
                  <div>
                    <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Variante B — proprietà da modificare</label>
                    <div v-for="(val, key) in abVariantB" :key="key" class="mb-flex mb-items-center mb-gap-1 mb-mb-2">
                      <span class="mb-text-[10px] mb-text-gray-300 mb-w-20 mb-truncate" :title="key">{{ abFieldLabel(key) }}</span>
                      <input
                        type="text"
                        :value="val"
                        @change="setAbOverride(key, $event.target.value)"
                        class="mb-flex-1 mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-1.5 mb-py-1 mb-text-xs mb-text-gray-900"
                      />
                      <button @click="removeAbOverride(key)" class="mb-text-red-400 hover:mb-text-red-300 mb-text-xs mb-px-1">x</button>
                    </div>
                    <select
                      @change="addAbOverride($event.target.value); $event.target.value = ''"
                      class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-xs mb-text-gray-500"
                    >
                      <option value="">+ Aggiungi proprietà...</option>
                      <option v-for="f in abAvailableFields" :key="f.key" :value="f.key">{{ f.label }}</option>
                    </select>
                  </div>

                  <!-- Goal type -->
                  <div>
                    <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Obiettivo conversione</label>
                    <select
                      :value="abTest.goal_type || 'click'"
                      @change="updateAbField('goal_type', $event.target.value)"
                      class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
                    >
                      <option value="click">Click sulla tile</option>
                      <option value="submit">Invio form</option>
                    </select>
                  </div>

                  <!-- Goal selector (optional) -->
                  <div>
                    <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Selettore CSS obiettivo (opzionale)</label>
                    <input
                      type="text"
                      :value="abTest.goal_selector || ''"
                      @change="updateAbField('goal_selector', $event.target.value)"
                      placeholder="es. .my-button, a.cta"
                      class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-xs mb-text-gray-900"
                    />
                  </div>
                </template>

                <!-- Stats (running or stopped) -->
                <template v-if="abTest.status === 'running' || abTest.status === 'stopped'">
                  <div v-if="abStats" class="mb-space-y-2">
                    <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                      <div class="mb-bg-gray-50 mb-rounded-lg mb-p-2 mb-text-center">
                        <div class="mb-text-[10px] mb-text-gray-500 mb-uppercase mb-font-bold">Variante A</div>
                        <div class="mb-text-lg mb-font-bold mb-text-gray-800">{{ abStats.variant_a?.conversion_rate || 0 }}%</div>
                        <div class="mb-text-[10px] mb-text-gray-400">{{ abStats.variant_a?.views || 0 }} visite · {{ abStats.variant_a?.conversions || 0 }} conv.</div>
                      </div>
                      <div class="mb-bg-gray-50 mb-rounded-lg mb-p-2 mb-text-center">
                        <div class="mb-text-[10px] mb-text-gray-500 mb-uppercase mb-font-bold">Variante B</div>
                        <div class="mb-text-lg mb-font-bold mb-text-gray-800">{{ abStats.variant_b?.conversion_rate || 0 }}%</div>
                        <div class="mb-text-[10px] mb-text-gray-400">{{ abStats.variant_b?.views || 0 }} visite · {{ abStats.variant_b?.conversions || 0 }} conv.</div>
                      </div>
                    </div>
                    <div v-if="abStats.significant" class="mb-text-[10px] mb-font-bold mb-text-center mb-py-1 mb-rounded mb-bg-green-50 mb-text-green-700">
                      Significativo (p={{ abStats.p_value }}) — Vincitore: {{ abStats.winner === 'a' ? 'A (originale)' : 'B (variante)' }}
                    </div>
                    <div v-else class="mb-text-[10px] mb-text-center mb-text-gray-400">
                      Non ancora significativo{{ abStats.p_value ? ' (p=' + abStats.p_value + ')' : '' }} — servono più dati
                    </div>
                  </div>
                </template>

                <!-- Action buttons -->
                <div class="mb-flex mb-gap-2 mb-mt-2">
                  <button
                    v-if="abTest.status === 'draft'"
                    @click="startAbTest"
                    :disabled="Object.keys(abVariantB).length === 0"
                    class="mb-flex-1 mb-bg-green-600 mb-text-white mb-text-xs mb-font-medium mb-py-1.5 mb-px-2 mb-rounded-md hover:mb-bg-green-700 disabled:mb-opacity-40 mb-transition-colors"
                  >Avvia test</button>
                  <button
                    v-if="abTest.status === 'running'"
                    @click="stopAbTest"
                    class="mb-flex-1 mb-bg-yellow-600 mb-text-white mb-text-xs mb-font-medium mb-py-1.5 mb-px-2 mb-rounded-md hover:mb-bg-yellow-700 mb-transition-colors"
                  >Ferma test</button>
                  <button
                    @click="deleteAbTest"
                    class="mb-bg-red-600 mb-text-white mb-text-xs mb-font-medium mb-py-1.5 mb-px-2 mb-rounded-md hover:mb-bg-red-700 mb-transition-colors"
                  >Elimina</button>
                </div>
              </template>
            </div>
          </CollapseSection>

            </div>
          </CollapseSection>

          <CollapseSection id="v2i-sec-adv-seo" title="SEO & Accessibilità" :macro="true">
            <div class="mb-space-y-3">
              <!-- ARIA Label -->
              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Aria Label</label>
                <input
                  type="text"
                  :value="tileAdvanced.aria_label || ''"
                  @input="updateAdvanced('aria_label', $event.target.value)"
                  :placeholder="t('Descrizione per screen reader')"
                  class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
                />
              </div>
              <!-- ARIA Role -->
              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Role</label>
                <select
                  :value="tileAdvanced.aria_role || ''"
                  @change="updateAdvanced('aria_role', $event.target.value)"
                  class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
                >
                  <option value="">Automatico</option>
                  <option value="region">Region</option>
                  <option value="navigation">Navigation</option>
                  <option value="complementary">Complementary</option>
                  <option value="banner">Banner</option>
                  <option value="contentinfo">Content Info</option>
                  <option value="main">Main</option>
                  <option value="search">Search</option>
                  <option value="form">Form</option>
                  <option value="none">None (decorativo)</option>
                </select>
              </div>
              <!-- Link Rel (for tiles with links) -->
              <div v-if="tileHasLink">
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Link Rel</label>
                <div class="mb-flex mb-flex-wrap mb-gap-2">
                  <label v-for="rel in linkRelOptions" :key="rel.value" class="mb-flex mb-items-center mb-gap-1 mb-cursor-pointer">
                    <input
                      type="checkbox"
                      :checked="(tileAdvanced.link_rel || '').includes(rel.value)"
                      @change="toggleLinkRel(rel.value)"
                      class="mb-w-3.5 mb-h-3.5 mb-rounded mb-accent-primary-600"
                    />
                    <span class="mb-text-[11px] mb-text-gray-300">{{ rel.label }}</span>
                  </label>
                </div>
              </div>
              <!-- Link Title (for tiles with links) -->
              <div v-if="tileHasLink">
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Link Title</label>
                <input
                  type="text"
                  :value="tileAdvanced.link_title || ''"
                  @input="updateAdvanced('link_title', $event.target.value)"
                  :placeholder="t('Tooltip al passaggio del mouse')"
                  class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
                />
              </div>
              <!-- Image Loading Strategy -->
              <div v-if="tileHasImage">
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Caricamento immagine</label>
                <select
                  :value="tileAdvanced.img_loading || 'lazy'"
                  @change="updateAdvanced('img_loading', $event.target.value)"
                  class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
                >
                  <option value="lazy">Lazy (default — carica quando visibile)</option>
                  <option value="eager">Eager (carica subito — per above the fold)</option>
                </select>
              </div>
              <!-- Fetch Priority -->
              <div v-if="tileHasImage">
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Fetch Priority</label>
                <select
                  :value="tileAdvanced.fetch_priority || 'auto'"
                  @change="updateAdvanced('fetch_priority', $event.target.value)"
                  class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
                >
                  <option value="auto">Auto (default)</option>
                  <option value="high">High (LCP — hero, slider, prima immagine)</option>
                  <option value="low">Low (sotto il fold)</option>
                </select>
              </div>
              <!-- Schema.org Type -->
              <div v-if="schemaOptions.length">
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Schema.org</label>
                <select
                  :value="tileAdvanced.schema_type || ''"
                  @change="updateAdvanced('schema_type', $event.target.value)"
                  class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
                >
                  <option value="">Nessuno</option>
                  <option v-for="opt in schemaOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                </select>
              </div>
              <!-- Data attributes -->
              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Data Attributes</label>
                <input
                  type="text"
                  :value="tileAdvanced.data_attrs || ''"
                  @input="updateAdvanced('data_attrs', $event.target.value)"
                  placeholder="key=value, key2=value2"
                  class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
                />
                <p class="mb-text-[10px] mb-text-gray-500 mb-mt-1">Aggiunge data-key="value" al wrapper</p>
              </div>
            </div>
          </CollapseSection>

          <!-- ===== MACRO: Effetti & Animazioni ===== -->
          <CollapseSection id="v2i-sec-adv-effects" title="Effetti & Animazioni" :macro="true">
            <div class="mb-space-y-3">
          <!-- Entrance Animation (olo-entrance-*) -->
          <CollapseSection title="Animazione di ingresso">
            <div class="mb-space-y-3">
              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Animazione</label>
                <select
                  :value="selectedTile?.settings?.entrance_animation || 'none'"
                  @change="updateSetting('entrance_animation', $event.target.value)"
                  class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
                >
                  <option value="none">Nessuna</option>
                  <option value="fade">Dissolvenza</option>
                  <option value="slide-up">Scorrimento dal basso</option>
                  <option value="slide-left">Scorrimento da sinistra</option>
                  <option value="slide-right">Scorrimento da destra</option>
                  <option value="slide-down">Scorrimento dall'alto</option>
                  <option value="zoom-in">Zoom in</option>
                  <option value="zoom-out">Zoom out</option>
                  <option value="flip">Flip</option>
                  <option value="rotate-in">Rotazione oraria</option>
                  <option value="rotate-ccw">Rotazione antioraria</option>
                  <option value="bounce">Rimbalzo</option>
                  <option value="elastic">Elastico</option>
                  <option value="blur-in">Sfocatura</option>
                  <option value="swing">Oscillazione</option>
                  <option value="rubber">Gomma</option>
                  <option value="jello">Gelatina</option>
                  <option value="back-in-left">Ritorno da sinistra</option>
                  <option value="back-in-right">Ritorno da destra</option>
                  <option value="typewriter">Macchina da scrivere</option>
                  <option value="fade-up-big">Grande dissolvenza dal basso</option>
                  <option value="fade-down-big">Grande dissolvenza dall'alto</option>
                  <option value="lightspeed-left">Velocità luce da sinistra</option>
                  <option value="lightspeed-right">Velocità luce da destra</option>
                  <option value="roll-in">Rotolamento in entrata</option>
                  <option value="jack-in-box">Scatola sorpresa</option>
                  <option value="hinge">Cardine che cade</option>
                  <option value="flip-y">Capovolgimento asse Y</option>
                  <option value="flip-x">Capovolgimento asse X</option>
                  <option value="zoom-in-down">Zoom + discesa</option>
                  <option value="zoom-in-up">Zoom + salita</option>
                  <option value="bounce-left">Rimbalzo da sinistra</option>
                  <option value="bounce-right">Rimbalzo da destra</option>
                  <option value="skew-in">Distorsione in entrata</option>
                  <option value="curtain-reveal">Effetto tendina</option>
                  <option value="blur-zoom">Sfocatura + Zoom</option>
                </select>
              </div>
              <template v-if="selectedTile?.settings?.entrance_animation && selectedTile.settings.entrance_animation !== 'none'">
                <div>
                  <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                    <button
                      @click="updateSetting('entrance_stagger', !selectedTile?.settings?.entrance_stagger)"
                      :class="[
                        'mb-relative mb-w-10 mb-h-5 mb-rounded-full mb-transition-colors mb-shrink-0',
                        selectedTile?.settings?.entrance_stagger ? 'mb-bg-primary-600' : 'mb-bg-gray-600'
                      ]"
                    >
                      <span
                        :class="[
                          'mb-absolute mb-top-0.5 mb-w-4 mb-h-4 mb-rounded-full mb-bg-white mb-transition-transform',
                          selectedTile?.settings?.entrance_stagger ? 'mb-left-5' : 'mb-left-0.5'
                        ]"
                      ></span>
                    </button>
                    <span class="mb-text-xs mb-text-gray-300">Stagger figli</span>
                  </label>
                  <p class="mb-text-[10px] mb-text-gray-500 mb-mt-0.5">Anima i figli uno dopo l'altro con ritardo incrementale</p>
                </div>
                <div v-if="selectedTile?.settings?.entrance_stagger">
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Ritardo stagger (ms)</label>
                  <div class="mb-flex mb-items-center mb-gap-2">
                    <input
                      type="range"
                      :value="selectedTile?.settings?.entrance_stagger_delay || 100"
                      @input="updateSetting('entrance_stagger_delay', $event.target.value)"
                      min="50" max="500" step="25"
                      class="mb-flex-1"
                    />
                    <span class="mb-text-xs mb-text-gray-400 mb-w-14 mb-text-right">{{ selectedTile?.settings?.entrance_stagger_delay || 100 }}ms</span>
                  </div>
                </div>
                <!-- Durata animazione -->
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Durata (ms)</label>
                  <div class="mb-flex mb-items-center mb-gap-2">
                    <input
                      type="range"
                      :value="selectedTile?.settings?.entrance_duration || 600"
                      @input="updateSetting('entrance_duration', $event.target.value)"
                      min="100" max="3000" step="50"
                      class="mb-flex-1"
                    />
                    <span class="mb-text-xs mb-text-gray-400 mb-w-16 mb-text-right">{{ selectedTile?.settings?.entrance_duration || 600 }}ms</span>
                  </div>
                </div>
                <!-- Ritardo iniziale -->
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Ritardo iniziale (ms)</label>
                  <div class="mb-flex mb-items-center mb-gap-2">
                    <input
                      type="range"
                      :value="selectedTile?.settings?.entrance_delay || 0"
                      @input="updateSetting('entrance_delay', $event.target.value)"
                      min="0" max="3000" step="50"
                      class="mb-flex-1"
                    />
                    <span class="mb-text-xs mb-text-gray-400 mb-w-16 mb-text-right">{{ selectedTile?.settings?.entrance_delay || 0 }}ms</span>
                  </div>
                </div>
                <!-- Intensità (moltiplicatore distanza/scala) -->
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Intensità (×)</label>
                  <div class="mb-flex mb-items-center mb-gap-2">
                    <input
                      type="range"
                      :value="selectedTile?.settings?.entrance_intensity || 1"
                      @input="updateSetting('entrance_intensity', $event.target.value)"
                      min="0.2" max="4" step="0.1"
                      class="mb-flex-1"
                    />
                    <span class="mb-text-xs mb-text-gray-400 mb-w-12 mb-text-right">{{ Number(selectedTile?.settings?.entrance_intensity || 1).toFixed(1) }}×</span>
                  </div>
                  <p class="mb-text-[10px] mb-text-gray-500 mb-mt-0.5">Scala distanze e dimensioni dell'animazione (1× = default)</p>
                </div>
                <!-- Easing -->
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Curva di animazione</label>
                  <select
                    :value="selectedTile?.settings?.entrance_easing || 'auto'"
                    @change="updateSetting('entrance_easing', $event.target.value)"
                    class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
                  >
                    <option value="auto">Automatica (per effetto)</option>
                    <option value="linear">Lineare</option>
                    <option value="ease">Ease (default)</option>
                    <option value="ease-in">Ease in (parte lento)</option>
                    <option value="ease-out">Ease out (finisce lento)</option>
                    <option value="ease-in-out">Ease in-out</option>
                    <option value="cubic-bezier(.34,1.56,.64,1)">Overshoot (rimbalzo)</option>
                    <option value="cubic-bezier(.68,-.55,.27,1.55)">Bounce forte</option>
                    <option value="cubic-bezier(.4,0,.2,1)">Material</option>
                  </select>
                </div>
              </template>
            </div>
          </CollapseSection>

          <!-- Scrollspy -->
          <CollapseSection title="Animazione allo scroll">
            <div class="mb-space-y-3">
              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Animazione</label>
                <select
                  :value="tileAdvanced.scrollspy_animation || ''"
                  @change="updateAdvanced('scrollspy_animation', $event.target.value)"
                  class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
                >
                  <option v-for="anim in scrollspyAnimations" :key="anim.value" :value="anim.value">{{ anim.label }}</option>
                </select>
              </div>
              <template v-if="tileAdvanced.scrollspy_animation">
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Ritardo (ms)</label>
                  <div class="mb-flex mb-items-center mb-gap-2">
                    <input
                      type="range"
                      :value="tileAdvanced.scrollspy_delay || 0"
                      @input="updateAdvanced('scrollspy_delay', parseInt($event.target.value))"
                      min="0" max="1500" step="50"
                      class="mb-flex-1"
                    />
                    <span class="mb-text-xs mb-text-gray-400 mb-w-14 mb-text-right">{{ tileAdvanced.scrollspy_delay || 0 }}ms</span>
                  </div>
                </div>
                <div>
                  <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                    <button
                      @click="updateAdvanced('scrollspy_repeat', !tileAdvanced.scrollspy_repeat)"
                      :class="[
                        'mb-relative mb-w-10 mb-h-5 mb-rounded-full mb-transition-colors mb-shrink-0',
                        tileAdvanced.scrollspy_repeat ? 'mb-bg-primary-600' : 'mb-bg-gray-600'
                      ]"
                    >
                      <span
                        :class="[
                          'mb-absolute mb-top-0.5 mb-w-4 mb-h-4 mb-rounded-full mb-bg-white mb-transition-transform',
                          tileAdvanced.scrollspy_repeat ? 'mb-left-5' : 'mb-left-0.5'
                        ]"
                      ></span>
                    </button>
                    <span class="mb-text-xs mb-text-gray-300">Ripeti ad ogni scroll</span>
                  </label>
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Stagger figli (ms)</label>
                  <div class="mb-flex mb-items-center mb-gap-2">
                    <input
                      type="range"
                      :value="tileAdvanced.scrollspy_stagger || 0"
                      @input="updateAdvanced('scrollspy_stagger', parseInt($event.target.value))"
                      min="0" max="500" step="25"
                      class="mb-flex-1"
                    />
                    <span class="mb-text-xs mb-text-gray-400 mb-w-14 mb-text-right">{{ tileAdvanced.scrollspy_stagger || 0 }}ms</span>
                  </div>
                  <p class="mb-text-[10px] mb-text-gray-500 mb-mt-0.5">Anima i figli diretti in sequenza</p>
                </div>
              </template>
            </div>
          </CollapseSection>

          <!-- Element Parallax -->
          <CollapseSection title="Parallax allo scroll">
            <template #header-right>
              <span class="mb-text-[10px] mb-mr-2" :class="hasElementParallax ? 'mb-text-primary-400' : 'mb-text-gray-500'">{{ hasElementParallax ? 'ATTIVO' : 'OFF' }}</span>
              <button
                @click.stop="toggleParallax"
                :class="['mb-relative mb-w-10 mb-h-5 mb-rounded-full mb-transition-colors mb-shrink-0', hasElementParallax ? 'mb-bg-primary-600' : 'mb-bg-gray-600']"
              ><span :class="['mb-absolute mb-top-0.5 mb-w-4 mb-h-4 mb-rounded-full mb-bg-white mb-transition-transform', hasElementParallax ? 'mb-left-5' : 'mb-left-0.5']"></span></button>
            </template>
            <ParallaxEditor
              v-if="hasElementParallax"
              :modelValue="elementParallaxData"
              :properties="elementParallaxProperties"
              @update:modelValue="updateAdvanced('parallax', $event)"
            />
          </CollapseSection>

          <!-- Bezier Path -->
          <CollapseSection title="Percorso Bezier allo scroll" :headerRight="true">
            <template #header-right>
              <span class="mb-text-[10px] mb-mr-2" :class="tileAdvanced.bezier_path ? 'mb-text-primary-400' : 'mb-text-gray-500'">{{ tileAdvanced.bezier_path ? 'ATTIVO' : 'OFF' }}</span>
              <button
                @click.stop="toggleBezier"
                :class="['mb-relative mb-w-10 mb-h-5 mb-rounded-full mb-transition-colors mb-shrink-0', tileAdvanced.bezier_path ? 'mb-bg-primary-600' : 'mb-bg-gray-600']"
              ><span :class="['mb-absolute mb-top-0.5 mb-w-4 mb-h-4 mb-rounded-full mb-bg-white mb-transition-transform', tileAdvanced.bezier_path ? 'mb-left-5' : 'mb-left-0.5']"></span></button>
            </template>
            <BezierPathEditor
              v-if="tileAdvanced.bezier_path"
              :modelValue="tileAdvanced.bezier_path"
              :tileId="selectedTile.id"
              @update:modelValue="updateAdvanced('bezier_path', $event)"
            />
          </CollapseSection>

          <!-- Sticky — scroll fisso (uk-sticky JS) -->
          <CollapseSection title="Scroll fisso (sticky)">
            <div class="mb-space-y-3">
              <p class="mb-text-[11px] mb-text-gray-400 mb-italic mb-leading-relaxed">
                Mantiene questo elemento fermo mentre il resto della pagina scorre.
                Utile per immagini, sommari, CTA persistenti. Funziona solo se la sezione
                genitrice è più alta dell'elemento.
              </p>

              <!-- Toggle attivazione -->
              <label class="mb-flex mb-items-center mb-justify-between mb-cursor-pointer mb-py-1">
                <span class="mb-text-xs mb-text-gray-300 mb-font-medium">Attiva scroll fisso</span>
                <button
                  type="button"
                  @click="updateAdvanced('sticky', !tileAdvanced.sticky)"
                  :class="['mb-relative mb-w-10 mb-h-5 mb-rounded-full mb-transition-colors mb-shrink-0',
                           tileAdvanced.sticky ? 'mb-bg-primary-600' : 'mb-bg-gray-600']"
                >
                  <span :class="['mb-absolute mb-top-0.5 mb-w-4 mb-h-4 mb-rounded-full mb-bg-white mb-transition-transform',
                                 tileAdvanced.sticky ? 'mb-left-5' : 'mb-left-0.5']"></span>
                </button>
              </label>

              <template v-if="tileAdvanced.sticky">
                <!-- Posizione -->
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Posizione</label>
                  <select
                    :value="tileAdvanced.sticky_position || 'top'"
                    @change="updateAdvanced('sticky_position', $event.target.value)"
                    class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900"
                  >
                    <option value="top">In alto</option>
                    <option value="bottom">In basso</option>
                  </select>
                </div>

                <!-- Offset -->
                <div>
                  <div class="mb-flex mb-items-center mb-justify-between mb-mb-1">
                    <label class="mb-text-xs mb-font-medium mb-text-gray-400">Distanza dal bordo (px)</label>
                    <span class="mb-text-xs mb-text-gray-300 mb-tabular-nums">{{ tileAdvanced.sticky_offset || 0 }}</span>
                  </div>
                  <input
                    type="range" min="0" max="200" step="5"
                    :value="tileAdvanced.sticky_offset || 0"
                    @input="updateAdvanced('sticky_offset', parseInt($event.target.value))"
                    class="mb-w-full mb-accent-primary-500"
                  />
                </div>

                <!-- Mobile -->
                <label class="mb-flex mb-items-center mb-justify-between mb-cursor-pointer mb-py-1">
                  <span class="mb-text-xs mb-text-gray-300">Attivo anche su mobile</span>
                  <button
                    type="button"
                    @click="updateAdvanced('sticky_on_mobile', tileAdvanced.sticky_on_mobile === false ? true : false)"
                    :class="['mb-relative mb-w-10 mb-h-5 mb-rounded-full mb-transition-colors mb-shrink-0',
                             tileAdvanced.sticky_on_mobile !== false ? 'mb-bg-primary-600' : 'mb-bg-gray-600']"
                  >
                    <span :class="['mb-absolute mb-top-0.5 mb-w-4 mb-h-4 mb-rounded-full mb-bg-white mb-transition-transform',
                                   tileAdvanced.sticky_on_mobile !== false ? 'mb-left-5' : 'mb-left-0.5']"></span>
                  </button>
                </label>
              </template>
            </div>
          </CollapseSection>

          <!-- Mouse Tracking Effects -->
          <CollapseSection title="Effetti mouse">
            <div class="mb-space-y-3">
              <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                <input type="checkbox" :checked="tileAdvanced.mouse_tilt === true" @change="updateAdvanced('mouse_tilt', $event.target.checked)" class="mb-accent-primary-500" />
                <span class="mb-text-xs mb-text-gray-300">Tilt 3D al hover</span>
              </label>
              <template v-if="tileAdvanced.mouse_tilt">
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Intensità: {{ tileAdvanced.mouse_tilt_intensity || 15 }}</label>
                  <input type="range" min="5" max="30" step="1" :value="tileAdvanced.mouse_tilt_intensity || 15" @input="updateAdvanced('mouse_tilt_intensity', $event.target.value)" class="mb-w-full mb-accent-primary-500" />
                </div>
              </template>
              <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                <input type="checkbox" :checked="tileAdvanced.mouse_track === true" @change="updateAdvanced('mouse_track', $event.target.checked)" class="mb-accent-primary-500" />
                <span class="mb-text-xs mb-text-gray-300">Segui cursore</span>
              </label>
              <template v-if="tileAdvanced.mouse_track">
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Velocità: {{ tileAdvanced.mouse_track_speed || 3 }}</label>
                  <input type="range" min="1" max="10" step="1" :value="tileAdvanced.mouse_track_speed || 3" @input="updateAdvanced('mouse_track_speed', $event.target.value)" class="mb-w-full mb-accent-primary-500" />
                </div>
              </template>

              <div class="mb-border-t mb-border-gray-700 mb-pt-3 mb-mt-1"></div>
              <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                <input type="checkbox" :checked="tileAdvanced.cursor_spotlight === true" @change="updateAdvanced('cursor_spotlight', $event.target.checked)" class="mb-accent-primary-500" />
                <span class="mb-text-xs mb-text-gray-300">Spotlight cursore (torcia)</span>
              </label>
              <p class="mb-text-[10px] mb-text-gray-500 mb-leading-snug">Un disco-torcia segue il cursore e inverte i colori, confinato a questo elemento. Si disattiva su touch e con riduzione del movimento.</p>
              <template v-if="tileAdvanced.cursor_spotlight">
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Inversione (blend)</label>
                  <select :value="tileAdvanced.cursor_spotlight_blend || 'difference'" @change="updateAdvanced('cursor_spotlight_blend', $event.target.value)" class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900">
                    <option value="difference">Differenza</option>
                    <option value="exclusion">Esclusione</option>
                    <option value="screen">Schermo</option>
                    <option value="overlay">Sovrapposizione</option>
                    <option value="hard-light">Hard Light</option>
                  </select>
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Colore luce</label>
                  <input type="color" :value="tileAdvanced.cursor_spotlight_color || '#ffffff'" @input="updateAdvanced('cursor_spotlight_color', $event.target.value)" class="mb-w-full mb-h-8 mb-rounded mb-cursor-pointer" />
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Dimensione disco: {{ tileAdvanced.cursor_spotlight_size || 300 }}px</label>
                  <input type="range" min="80" max="600" step="10" :value="tileAdvanced.cursor_spotlight_size || 300" @input="updateAdvanced('cursor_spotlight_size', $event.target.value)" class="mb-w-full mb-accent-primary-500" />
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Morbidezza bordo: {{ tileAdvanced.cursor_spotlight_softness ?? 40 }}%</label>
                  <input type="range" min="0" max="100" step="5" :value="tileAdvanced.cursor_spotlight_softness ?? 40" @input="updateAdvanced('cursor_spotlight_softness', $event.target.value)" class="mb-w-full mb-accent-primary-500" />
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Inseguimento: {{ tileAdvanced.cursor_spotlight_easing || 22 }}</label>
                  <input type="range" min="5" max="100" step="1" :value="tileAdvanced.cursor_spotlight_easing || 22" @input="updateAdvanced('cursor_spotlight_easing', $event.target.value)" class="mb-w-full mb-accent-primary-500" />
                </div>
              </template>
            </div>
          </CollapseSection>

          <!-- Infinite (Looping) Animations -->
          <CollapseSection title="Animazione continua">
            <div class="mb-space-y-3">
              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Animazione</label>
                <select :value="tileAdvanced.infinite_animation || 'none'" @change="updateAdvanced('infinite_animation', $event.target.value)" class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900">
                  <option value="none">Nessuna</option>
                  <option value="float">Galleggiamento</option>
                  <option value="float-rot">Galleggiamento + rotazione</option>
                  <option value="pulse">Pulsazione</option>
                  <option value="spin">Rotazione</option>
                  <option value="wiggle">Dondolio</option>
                  <option value="bounce">Rimbalzo</option>
                  <option value="swing">Oscillazione</option>
                  <option value="breathe">Respiro</option>
                </select>
              </div>
              <template v-if="(tileAdvanced.infinite_animation || 'none') !== 'none'">
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Velocità: {{ tileAdvanced.infinite_speed || 3 }}s</label>
                  <input type="range" min="1" max="10" step="0.5" :value="tileAdvanced.infinite_speed || 3" @input="updateAdvanced('infinite_speed', $event.target.value)" class="mb-w-full mb-accent-primary-500" />
                </div>
                <div v-if="['float','float-rot','bounce'].includes(tileAdvanced.infinite_animation)">
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Ampiezza: {{ tileAdvanced.infinite_amplitude || (tileAdvanced.infinite_animation === 'bounce' ? 15 : 12) }}px</label>
                  <input type="range" min="2" max="60" step="1" :value="tileAdvanced.infinite_amplitude || (tileAdvanced.infinite_animation === 'bounce' ? 15 : 12)" @input="updateAdvanced('infinite_amplitude', $event.target.value)" class="mb-w-full mb-accent-primary-500" />
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Ritardo: {{ tileAdvanced.infinite_delay || 0 }}ms</label>
                  <input type="range" min="0" max="3000" step="100" :value="tileAdvanced.infinite_delay || 0" @input="updateAdvanced('infinite_delay', $event.target.value)" class="mb-w-full mb-accent-primary-500" />
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Direzione</label>
                  <select :value="tileAdvanced.infinite_direction || 'normal'" @change="updateAdvanced('infinite_direction', $event.target.value)" class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900">
                    <option value="normal">Normale</option>
                    <option value="alternate">Alternata</option>
                    <option value="reverse">Inversa</option>
                  </select>
                </div>
              </template>
            </div>
          </CollapseSection>

          <!-- CSS Mask / Clip-path -->
          <CollapseSection title="Maschera forma">
            <div class="mb-space-y-3">
              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Tipo maschera</label>
                <select :value="tileAdvanced.mask_type || 'none'" @change="updateAdvanced('mask_type', $event.target.value)" class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900">
                  <option value="none">Nessuna</option>
                  <option value="circle">Cerchio</option>
                  <option value="ellipse">Ellisse</option>
                  <option value="triangle">Triangolo</option>
                  <option value="hexagon">Esagono</option>
                  <option value="star">Stella</option>
                  <option value="diamond">Diamante</option>
                  <option value="blob">Blob</option>
                  <option value="custom">Personalizzata</option>
                </select>
              </div>
              <template v-if="tileAdvanced.mask_type === 'custom'">
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Clip-path CSS</label>
                  <input
                    type="text"
                    :value="tileAdvanced.mask_custom || ''"
                    @input="updateAdvanced('mask_custom', $event.target.value)"
                    placeholder="polygon(50% 0%, 100% 100%, 0% 100%)"
                    class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-xs mb-text-gray-900 mb-font-mono"
                  />
                </div>
              </template>
            </div>
          </CollapseSection>

            </div>
          </CollapseSection>

          <!-- ===== MACRO: Sviluppatore ===== -->
          <CollapseSection id="v2i-sec-adv-dev" title="Sviluppatore" :macro="true">
            <div class="mb-space-y-3">
          <!-- Note editor (solo builder, non renderizzate nel frontend) -->
          <CollapseSection title="Note editor">
            <textarea
              :value="tileAdvanced.editor_note || ''"
              @input="updateAdvanced('editor_note', $event.target.value)"
              rows="3"
              :placeholder="t('Note visibili solo nel builder...')"
              class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-xs mb-text-gray-900 mb-resize-none"
            ></textarea>
            <p class="mb-text-[9px] mb-text-gray-500 mb-mt-1">Queste note sono visibili solo nel builder e non vengono pubblicate.</p>
          </CollapseSection>

          <!-- Custom JavaScript -->
          <CollapseSection title="JavaScript personalizzato">
            <textarea
              :value="tileAdvanced.custom_js || ''"
              @input="updateAdvanced('custom_js', $event.target.value)"
              rows="4"
              placeholder="// La variabile 'el' contiene l'elemento DOM"
              class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-xs mb-text-gray-900 mb-font-mono mb-resize-y"
              spellcheck="false"
            ></textarea>
            <p class="mb-text-[9px] mb-text-gray-500 mb-mt-1">JS eseguito nel frontend. La variabile <code style="background:#E5E7EB;padding:1px 4px;border-radius:3px">el</code> contiene l'elemento DOM.</p>
          </CollapseSection>

            </div>
          </CollapseSection>

          <CollapseSection id="v2i-sec-adv-position" title="Posizionamento" :macro="true">
            <div class="mb-space-y-3">
              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Modalità</label>
                <select
                  :value="tileAdvanced.position_mode || 'static'"
                  @change="updateAdvanced('position_mode', $event.target.value)"
                  class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900"
                >
                  <option value="static">Normale (nel flusso)</option>
                  <option value="relative">Relativo (offset dal flusso)</option>
                  <option value="absolute">Assoluto (libero nella sezione)</option>
                  <option value="fixed">Fisso (libero nella pagina)</option>
                  <option value="sticky">Sticky (fisso durante lo scroll)</option>
                </select>
              </div>
              <template v-if="tileAdvanced.position_mode && tileAdvanced.position_mode !== 'static'">
                <!-- Position breakpoint switcher -->
                <div class="mb-flex mb-gap-1 mb-bg-gray-700 mb-rounded-lg mb-p-0.5">
                  <button
                    v-for="bp in responsiveBreakpoints"
                    :key="bp.key"
                    @click="positionBp = bp.key"
                    :class="[
                      'mb-flex-1 mb-py-1 mb-text-[10px] mb-font-medium mb-rounded-md mb-transition-colors mb-flex mb-items-center mb-justify-center mb-gap-1',
                      positionBp === bp.key
                        ? 'mb-bg-primary-600 mb-text-white'
                        : 'mb-text-gray-400 hover:mb-text-gray-300'
                    ]"
                    :title="bp.label"
                    v-html="bp.icon"
                  ></button>
                </div>
                <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                  <div>
                    <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Top <span v-if="positionBp !== 'desktop'" class="mb-text-amber-400">{{ positionBpLabel }}</span></label>
                    <input
                      type="text"
                      :value="tileAdvanced[positionKey('position_top')] ?? ''"
                      @change="updateAdvanced(positionKey('position_top'), $event.target.value)"
                      placeholder="auto"
                      class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-0.5 mb-text-[11px] mb-text-gray-900"
                    />
                  </div>
                  <div>
                    <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Left <span v-if="positionBp !== 'desktop'" class="mb-text-amber-400">{{ positionBpLabel }}</span></label>
                    <input
                      type="text"
                      :value="tileAdvanced[positionKey('position_left')] ?? ''"
                      @change="updateAdvanced(positionKey('position_left'), $event.target.value)"
                      placeholder="auto"
                      class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-0.5 mb-text-[11px] mb-text-gray-900"
                    />
                  </div>
                  <div>
                    <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Bottom <span v-if="positionBp !== 'desktop'" class="mb-text-amber-400">{{ positionBpLabel }}</span></label>
                    <input
                      type="text"
                      :value="tileAdvanced[positionKey('position_bottom')] ?? ''"
                      @change="updateAdvanced(positionKey('position_bottom'), $event.target.value)"
                      placeholder="auto"
                      class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-0.5 mb-text-[11px] mb-text-gray-900"
                    />
                  </div>
                  <div>
                    <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Right <span v-if="positionBp !== 'desktop'" class="mb-text-amber-400">{{ positionBpLabel }}</span></label>
                    <input
                      type="text"
                      :value="tileAdvanced[positionKey('position_right')] ?? ''"
                      @change="updateAdvanced(positionKey('position_right'), $event.target.value)"
                      placeholder="auto"
                      class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-0.5 mb-text-[11px] mb-text-gray-900"
                    />
                  </div>
                </div>
                <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                  <div>
                    <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Larghezza <span v-if="positionBp !== 'desktop'" class="mb-text-amber-400">{{ positionBpLabel }}</span></label>
                    <input
                      type="text"
                      :value="tileAdvanced[positionKey('position_width')] ?? ''"
                      @change="updateAdvanced(positionKey('position_width'), $event.target.value)"
                      placeholder="auto"
                      class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-0.5 mb-text-[11px] mb-text-gray-900"
                    />
                  </div>
                  <div>
                    <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">z-index <span v-if="positionBp !== 'desktop'" class="mb-text-amber-400">{{ positionBpLabel }}</span></label>
                    <input
                      type="number"
                      :value="tileAdvanced[positionKey('position_zindex')] ?? ''"
                      @change="updateAdvanced(positionKey('position_zindex'), $event.target.value === '' ? '' : parseInt($event.target.value))"
                      placeholder="auto"
                      class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-0.5 mb-text-[11px] mb-text-gray-900"
                    />
                  </div>
                </div>
                <p class="mb-text-[9px] mb-text-gray-500 mb-leading-relaxed">
                  Valori: px (es. 100px), % (es. 50%), vh/vw. Assoluto è relativo alla sezione, Fisso alla finestra.
                </p>
                <div v-if="tileAdvanced.position_mode === 'fixed'">
                  <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Nascondi al raggiungimento di</label>
                  <input
                    type="text"
                    :value="tileAdvanced.position_hide_at ?? ''"
                    @change="updateAdvanced('position_hide_at', $event.target.value)"
                    :placeholder="t('HTML ID della sezione (es. fine-nav)')"
                    class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-0.5 mb-text-[11px] mb-text-gray-900"
                  />
                  <p class="mb-text-[9px] mb-text-gray-500 mb-mt-0.5">L'elemento scompare quando lo scroll raggiunge la sezione con questo ID.</p>
                </div>
              </template>
            </div>
          </CollapseSection>

        </div>
        </div><!-- /v2i-content -->

        <!-- ─── RAIL (right edge, mirrors left sidebar rail) ───── -->
        <aside class="v2i-rail" role="tablist" :aria-label="t('Sezioni inspector')">
          <button
            v-for="sec in railSections"
            :key="sec.id"
            class="v2i-rail-btn"
            :class="{ 'on': activeRailSection === sec.id }"
            :title="sec.label"
            type="button"
            @click="onRailClick(sec)"
          >
            <span class="bar"></span>
            <span class="ic" v-html="sec.icon"></span>
            <span class="lbl">{{ sec.label }}</span>
          </button>
        </aside>
      </div>
      <!-- ─── /BODY ────────────────────────────────────────────── -->

      </template>

      <!-- ProSlider Editor Modal -->
      <ProSliderEditor
        v-if="showProSliderEditor && selectedTile"
        :tileId="selectedTile.id"
        @close="showProSliderEditor = false"
      />
    </div>
  </transition>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue';
import { t } from '@/i18n';
import { useBuilderStore } from '@/stores/builder';
import { useTilesStore } from '@/stores/tiles';
import { getElementDef, getElementFields } from '@/config/elementRegistry';
import BackgroundControls from './BackgroundControls.vue';
import PageSettingsPanel from './PageSettingsPanel.vue';
import ContentItemsEditor from './ContentItemsEditor.vue';
import InspectorField from './InspectorField.vue';
import StyleFieldsRenderer from './StyleFieldsRenderer.vue';
import { styleFieldsBase } from '@/config/elements/_styleFieldsBase.js';
import FieldSpacing from './fields/FieldSpacing.vue';
import CollapseSection from './CollapseSection.vue';
import FieldBoxShadow from './fields/FieldBoxShadow.vue';
import FieldTransform from './fields/FieldTransform.vue';
import ParallaxEditor from './ParallaxEditor.vue';
import BezierPathEditor from './BezierPathEditor.vue';
import ProSliderEditor from '../ProSlider/ProSliderEditor.vue';
import HeightModeSelector from '../ProSlider/HeightModeSelector.vue';
import { MEGAMENU_PRESETS } from '@/config/megamenuPresets';
import { MEGAMENU_TEMPLATES } from '@/config/megamenuTemplates';

const elementParallaxProperties = [
  { key: 'x', label: 'Traslazione X', min: -1000, max: 1000, step: 10, unit: 'px' },
  { key: 'y', label: 'Traslazione Y', min: -1000, max: 1000, step: 10, unit: 'px' },
  { key: 'scale', label: 'Scala', min: 0.1, max: 3, step: 0.05, unit: '' },
  { key: 'rotate', label: 'Rotazione', min: -360, max: 360, step: 1, unit: 'deg' },
  { key: 'opacity', label: 'Opacità', min: 0, max: 1, step: 0.05, unit: '' },
  { key: 'blur', label: 'Sfocatura', min: 0, max: 100, step: 1, unit: 'px' },
];

const scrollspyAnimations = [
  { value: '', label: 'Nessuna' },
  { value: 'fade', label: 'Fade' },
  { value: 'scale-up', label: 'Scala su' },
  { value: 'scale-down', label: 'Scala giù' },
  { value: 'slide-top', label: 'Scorrimento alto' },
  { value: 'slide-bottom', label: 'Scorrimento basso' },
  { value: 'slide-left', label: 'Scorrimento sinistra' },
  { value: 'slide-right', label: 'Scorrimento destra' },
  { value: 'slide-top-small', label: 'Scorrimento alto (piccolo)' },
  { value: 'slide-bottom-small', label: 'Scorrimento basso (piccolo)' },
  { value: 'slide-left-small', label: 'Scorrimento sinistra (piccolo)' },
  { value: 'slide-right-small', label: 'Scorrimento destra (piccolo)' },
  { value: 'slide-top-medium', label: 'Scorrimento alto (medio)' },
  { value: 'slide-bottom-medium', label: 'Scorrimento basso (medio)' },
  { value: 'slide-left-medium', label: 'Scorrimento sinistra (medio)' },
  { value: 'slide-right-medium', label: 'Scorrimento destra (medio)' },
  { value: 'kenburns', label: 'Ken Burns' },
  { value: 'shake', label: 'Tremolio' },
];

const builderStore = useBuilderStore();
const tilesStore = useTilesStore();

const tabs = ['Contenuto', 'Stile', 'Avanzate'];

// V2 Inspector — persistenza tab + ultima sezione per tab in localStorage.
const ACTIVE_TAB_KEY = 'olo_insp_active_tab';
const LAST_SECTION_KEY = 'olo_insp_last_section'; // JSON {Contenuto, Stile, Avanzate}

function _loadLastSections() {
  try {
    const raw = localStorage.getItem(LAST_SECTION_KEY);
    return raw ? JSON.parse(raw) : {};
  } catch (_) { return {}; }
}
const _lastSections = _loadLastSections(); // mutated in-place + persisted

const activeTab = ref(
  tabs.includes(localStorage.getItem(ACTIVE_TAB_KEY))
    ? localStorage.getItem(ACTIVE_TAB_KEY)
    : 'Contenuto'
);
const settingsSearch = ref('');
const showOnlyModified = ref(false);

// V2 Inspector: rail icone con sezioni del tab corrente
const contentRef = ref(null);
const activeRailSection = ref('');

// Generic SVG icons (Lucide-style, stroke 1.6) for the rail
const _railIcons = {
  heading:    '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4v16M20 4v16M4 12h16"/></svg>',
  text:       '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7V4h16v3M9 20h6M12 4v16"/></svg>',
  spark:      '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.9 5.8H20l-4.9 3.6 1.9 5.8L12 14.6 7 18.2l1.9-5.8L4 8.8h6.1z"/></svg>',
  sliders:    '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 21V14M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M1 14h6M9 8h6M17 16h6"/></svg>',
  shape:      '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M3 12h18"/></svg>',
  square:     '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>',
  layers:     '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>',
  image:      '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>',
  spacer:     '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M4 18h16M12 9v6"/></svg>',
  icon:       '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
  code:       '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/></svg>',
  eye:        '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>',
  device:     '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>',
  pin:        '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>',
  zap:        '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>',
  sliding:    '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h18M16 7l5 5-5 5"/></svg>',
  flask:      '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3h6M10 3v6L4 21h16L14 9V3"/></svg>',
};
function railIcon(name) { return _railIcons[name] || _railIcons.square; }

// Heuristic icon assignment based on a section label
function _iconForSectionLabel(label) {
  const l = (label || '').toLowerCase();
  if (/titol|heading|font|tipograf/.test(l)) return _railIcons.heading;
  if (/effett|animaz|spark|highlight/.test(l)) return _railIcons.spark;
  if (/aspetto|dimension|layout|stile/.test(l)) return _railIcons.sliders;
  if (/decoraz|bordo|border|sfondo|color/.test(l)) return _railIcons.shape;
  if (/sotto|paragraf|content/.test(l)) return _railIcons.text;
  if (/link|url|cta|pulsant|button/.test(l)) return _railIcons.code;
  if (/spaz|padding|margin|gap/.test(l)) return _railIcons.spacer;
  if (/imag|gallery|media|video|background/.test(l)) return _railIcons.image;
  if (/ombra|shadow/.test(l)) return _railIcons.layers;
  if (/icon|emoji/.test(l)) return _railIcons.icon;
  if (/respons|breakpoint|mobile|desktop/.test(l)) return _railIcons.device;
  if (/visib|condiz|hidden|show/.test(l)) return _railIcons.eye;
  if (/posizion|position|sticky|fixed/.test(l)) return _railIcons.pin;
  if (/svilupp|css|html|develop|advanced|id/.test(l)) return _railIcons.code;
  if (/anim|transition|hover/.test(l)) return _railIcons.spark;
  if (/test|ab|experiment/.test(l)) return _railIcons.flask;
  if (/menu|nav/.test(l)) return _railIcons.sliding;
  return _railIcons.square;
}

// V2 rail sections — derived from tab content. Each entry has { id, label, icon }.
// Click → scrolls the matching #v2i-sec-XXX element into the content panel.

// Build the Stile rail entries dynamically from the same source the renderer
// uses (styleFieldsBase). We mirror StyleFieldsRenderer's grouping logic so the
// indices match the IDs it assigns to its CollapseSection components.
// tileType arg lo passiamo dal computed sotto perché styleFieldsBase ora filtra
// i field wrapper per i tile strutturali (section/row/column).
function _buildStyleRail(tileType) {
  const groups = [];
  let current = { label: null, fields: [] };
  for (const f of styleFieldsBase(tileType)) {
    if (f.type === 'separator') {
      if (current.fields.length > 0) groups.push(current);
      current = { label: f.label, fields: [] };
    } else {
      current.fields.push(f);
    }
  }
  if (current.fields.length > 0) groups.push(current);

  const out = [];
  for (let i = 0; i < groups.length; i++) {
    const g = groups[i];
    if (!g.label) continue;
    out.push({
      id: 'v2i-sec-stile-' + i,
      label: g.label,
      icon: _iconForSectionLabel(g.label),
    });
  }
  return out;
}
const _advRailFixed = [
  { id: 'v2i-sec-adv-id',         label: 'Identificatori',  icon: _railIcons.code },
  { id: 'v2i-sec-adv-visibility', label: 'Visibilità',      icon: _railIcons.eye },
  { id: 'v2i-sec-adv-seo',        label: 'SEO',             icon: _railIcons.icon },
  { id: 'v2i-sec-adv-effects',    label: 'Effetti',         icon: _railIcons.spark },
  { id: 'v2i-sec-adv-position',   label: 'Posizionamento',  icon: _railIcons.pin },
  { id: 'v2i-sec-adv-dev',        label: 'Sviluppatore',    icon: _railIcons.flask },
];

function onTabChange(tab) {
  activeTab.value = tab;
  try { localStorage.setItem(ACTIVE_TAB_KEY, tab); } catch (_) {}
  activeRailSection.value = '';
  if (contentRef.value) contentRef.value.scrollTop = 0;

  // Restore last visited section for this tab (if any), then setup scroll-spy.
  nextTick(() => {
    setupScrollSpy();
    const restoreId = _lastSections[tab];
    if (restoreId) {
      // Defer one more tick: CollapseSection may need a beat to mount.
      nextTick(() => {
        const el = document.getElementById(restoreId);
        if (el) {
          activeRailSection.value = restoreId;
          // Auto-scroll without aggressive flash for a passive restore.
          if (contentRef.value) {
            const containerTop = contentRef.value.getBoundingClientRect().top;
            const elTop = el.getBoundingClientRect().top;
            contentRef.value.scrollTo({
              top: contentRef.value.scrollTop + (elTop - containerTop) - 8,
              behavior: 'auto',
            });
          }
        }
      });
    }
  });
}

// ── Scroll-spy: highlight the rail entry matching the current scroll position ──
let _scrollSpyObserver = null;
// While the user clicks a rail entry, we programmatically scroll the panel.
// Suppress the observer briefly so it doesn't fight the user's choice.
let _suppressScrollSpyUntil = 0;

function setupScrollSpy() {
  if (_scrollSpyObserver) {
    _scrollSpyObserver.disconnect();
    _scrollSpyObserver = null;
  }
  if (!contentRef.value) return;
  const targets = contentRef.value.querySelectorAll('[id^="v2i-sec-"]');
  if (!targets.length) return;

  _scrollSpyObserver = new IntersectionObserver(
    (entries) => {
      if (Date.now() < _suppressScrollSpyUntil) return;
      // Pick the entry whose top is closest to (but at or above) the panel top.
      let bestId = '';
      let bestDist = Infinity;
      entries.forEach((e) => {
        if (!e.isIntersecting) return;
        const dist = Math.abs(e.boundingClientRect.top);
        if (dist < bestDist) { bestDist = dist; bestId = e.target.id; }
      });
      if (bestId) activeRailSection.value = bestId;
    },
    {
      root: contentRef.value,
      // Trigger when a section enters the top 30% of the panel.
      rootMargin: '0px 0px -70% 0px',
      threshold: 0,
    }
  );
  targets.forEach((t) => _scrollSpyObserver.observe(t));
}

onMounted(() => nextTick(setupScrollSpy));
onUnmounted(() => {
  if (_scrollSpyObserver) {
    _scrollSpyObserver.disconnect();
    _scrollSpyObserver = null;
  }
});

// Rebuild when the selected tile changes (sections are different per element type).
watch(() => builderStore.selectedTileId, () => {
  nextTick(setupScrollSpy);
});

// Persist last visited section per-tab whenever it changes.
watch(activeRailSection, (v) => {
  if (!v) return;
  _lastSections[activeTab.value] = v;
  try { localStorage.setItem(LAST_SECTION_KEY, JSON.stringify(_lastSections)); } catch (_) {}
});

function onRailClick(sec) {
  activeRailSection.value = sec.id;
  // Lock the active section for the duration of the smooth scroll so the
  // IntersectionObserver doesn't override it with whatever appears on screen.
  _suppressScrollSpyUntil = Date.now() + 700;
  nextTick(() => {
    const el = document.getElementById(sec.id);
    if (!el) return;

    // If the section is a CollapseSection and is currently closed, open it.
    // CollapseSection root has class "olo-collapse-section" and the first
    // child is the toggle <button class="collapse-head" aria-expanded="…">.
    if (el.classList.contains('olo-collapse-section')) {
      const head = el.firstElementChild;
      if (head && head.classList && head.classList.contains('collapse-head')) {
        if (head.getAttribute('aria-expanded') === 'false') {
          head.click();
        }
      }
    }

    // Brief highlight pulse so the user sees which section was activated even
    // if it was already expanded and the scroll delta is small.
    el.classList.remove('v2i-sec-flash');
    // Force reflow before re-adding so the animation restarts on repeat clicks.
    void el.offsetWidth;
    el.classList.add('v2i-sec-flash');
    setTimeout(() => el.classList.remove('v2i-sec-flash'), 900);

    // Scroll the section into view inside the content panel — wait one tick so
    // the expand animation starts before measuring offsets.
    nextTick(() => {
      if (!contentRef.value) return;
      const containerTop = contentRef.value.getBoundingClientRect().top;
      const elTop = el.getBoundingClientRect().top;
      const delta = elTop - containerTop;
      contentRef.value.scrollTo({
        top: contentRef.value.scrollTop + delta - 8,
        behavior: 'smooth',
      });
    });
  });
}

function contentSectionId(sIdx) { return 'v2i-sec-content-' + sIdx; }

const railSections = computed(() => {
  if (activeTab.value === 'Contenuto') {
    const out = [];
    const sections = groupedSections.value || [];
    for (let i = 0; i < sections.length; i++) {
      const s = sections[i];
      if (!s.label) continue; // skip the implicit "head" section
      if (!sectionHasVisibleFields(s)) continue;
      out.push({
        id: contentSectionId(i),
        label: t(s.label),
        icon: _iconForSectionLabel(s.label),
      });
    }
    return out;
  }
  if (activeTab.value === 'Stile') return _buildStyleRail(selectedTile.value?.type).map(s => ({ ...s, label: t(s.label) }));
  if (activeTab.value === 'Avanzate') return _advRailFixed.map(s => ({ ...s, label: t(s.label) }));
  return [];
});

// Copy/paste style — wrap delle azioni dello store già esistenti
const hasClipboardStyle = computed(() => {
  if (tilesStore.clipboardStyle) return true;
  try { return !!localStorage.getItem('olo_clipboard_style'); } catch (_) { return false; }
});
function onCopyStyle() {
  if (!builderStore.selectedTileId) return;
  tilesStore.copyStyle(builderStore.selectedTileId);
}
function onPasteStyle() {
  if (!builderStore.selectedTileId) return;
  tilesStore.pasteStyle(builderStore.selectedTileId);
  builderStore.markDirtyForTile(builderStore.selectedTileId);
}

// ── Tile presets (salvati in localStorage, per-type) ──
const STORAGE_KEY = 'olo_tile_presets';
const tilePresets = ref([]);
const presetMenuOpen = ref(false);
function loadPresets() {
  try { tilePresets.value = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'); } catch (_) { tilePresets.value = []; }
}
loadPresets();
function savePresets() {
  try { localStorage.setItem(STORAGE_KEY, JSON.stringify(tilePresets.value)); } catch (_) {}
}
const tilePresetsForType = computed(() => {
  const t = selectedTile.value?.type;
  if (!t) return [];
  return tilePresets.value.filter(p => p.type === t);
});
function onSaveAsPreset() {
  const tile = selectedTile.value;
  if (!tile) return;
  const name = window.prompt(t('Nome del preset:'), elementDef.value?.name || tile.type);
  if (!name) return;
  const id = 'p' + Date.now().toString(36);
  tilePresets.value.push({
    id,
    name: name.trim(),
    type: tile.type,
    settings: JSON.parse(JSON.stringify(tile.settings || {})),
    style:    JSON.parse(JSON.stringify(tile.style    || {})),
    advanced: JSON.parse(JSON.stringify(tile.advanced || {})),
    ts: Date.now(),
  });
  savePresets();
}
function applyPreset(preset) {
  const tile = selectedTile.value;
  if (!tile || preset.type !== tile.type) return;
  tilesStore.updateTile(tile.id, {
    settings: JSON.parse(JSON.stringify(preset.settings || {})),
    style:    JSON.parse(JSON.stringify(preset.style    || {})),
    advanced: JSON.parse(JSON.stringify(preset.advanced || {})),
  });
  builderStore.markDirtyForTile(tile.id);
}
function deletePreset(id) {
  if (!window.confirm(t('Eliminare il preset?'))) return;
  tilePresets.value = tilePresets.value.filter(p => p.id !== id);
  savePresets();
}

// Directive minima v-click-outside per chiudere il menu preset
const vClickOutside = {
  mounted(el, binding) {
    el._oloClickOut = (ev) => { if (!el.contains(ev.target)) binding.value?.(); };
    setTimeout(() => document.addEventListener('click', el._oloClickOut), 0);
  },
  unmounted(el) { document.removeEventListener('click', el._oloClickOut); },
};

/**
 * Badge stato del tile selezionato: hover/responsive/condizioni/animazione/sticky.
 * Click → switch al tab corrispondente.
 */
const tileBadges = computed(() => {
  const t = selectedTile.value;
  if (!t) return [];
  const set = t.settings || {};
  const adv = t.advanced || {};
  const sty = t.style || {};
  const badges = [];

  // Hover: chiavi con suffisso _hover non vuote, oppure style.hover non vuoto, oppure border_hover
  const respRe = /_(widescreen|tablet_landscape|tablet|mobile_landscape|mobile)$/;
  const isFilled = (v) => v !== undefined && v !== null && v !== '' && v !== false && !(typeof v === 'object' && v !== null && Object.keys(v).length === 0);
  const hasHoverKey = Object.keys(set).some(k => /_hover(_duration)?$/.test(k) && isFilled(set[k]));
  const hasStyleHover = sty.hover && Object.values(sty.hover).some(isFilled);
  if (hasHoverKey || hasStyleHover) badges.push({ id: 'hover', label: 'Hover', color: 'orange', target: 'Contenuto' });

  // Responsive: chiavi con suffisso breakpoint
  const hasResp = Object.keys(set).some(k => respRe.test(k) && isFilled(set[k]))
    || Object.keys(sty).some(k => respRe.test(k) && isFilled(sty[k]));
  if (hasResp) badges.push({ id: 'resp', label: 'Responsive', color: 'blue', target: 'Stile' });

  // Visibilità condizionale (Avanzate)
  if (isFilled(adv.cond_type) || (Array.isArray(adv.cond_post_ids) && adv.cond_post_ids.length)) {
    badges.push({ id: 'cond', label: 'Condizioni', color: 'purple', target: 'Avanzate' });
  }
  // Visibilità per breakpoint
  if (adv.visible_desktop === false || adv.visible_tablet === false || adv.visible_mobile === false || adv.visible_tablet_landscape === false || adv.visible_mobile_landscape === false) {
    badges.push({ id: 'visbp', label: 'Vis. limitata', color: 'gray', target: 'Avanzate' });
  }
  // Animazione (entrance o scrollspy)
  if ((set.entrance_animation && set.entrance_animation !== 'none')
    || (adv.scrollspy_animation && adv.scrollspy_animation !== 'none')
    || (adv.infinite_animation && adv.infinite_animation !== 'none')) {
    badges.push({ id: 'anim', label: 'Animazione', color: 'green', target: 'Avanzate' });
  }
  // Sticky
  if (adv.sticky) badges.push({ id: 'sticky', label: 'Sticky', color: 'cyan', target: 'Avanzate' });
  // Custom CSS / JS
  if (isFilled(adv.custom_css) || isFilled(adv.custom_js)) {
    badges.push({ id: 'css', label: 'CSS/JS', color: 'yellow', target: 'Avanzate' });
  }

  return badges;
});

// A11y: keyboard arrow navigation for tab buttons
function navigateTab(dir) {
  const idx = tabs.indexOf(activeTab.value);
  const next = (idx + dir + tabs.length) % tabs.length;
  activeTab.value = tabs[next];
  nextTick(() => {
    const btn = document.getElementById('inspector-tab-' + tabs[next]);
    if (btn) btn.focus();
  });
}
const showProSliderEditor = ref(false);
const sides = ['top', 'right', 'bottom', 'left'];
const viewports = [
  { key: 'desktop', label: 'Desktop', svg: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg>' },
  { key: 'tablet_landscape', label: 'Tablet L', svg: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><line x1="12" x2="12.01" y1="12" y2="12"/></svg>' },
  { key: 'tablet', label: 'Tablet P', svg: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2"/><line x1="12" x2="12.01" y1="18" y2="18"/></svg>' },
  { key: 'mobile_landscape', label: 'Mobile L', svg: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="12" x2="12.01" y1="12" y2="12"/></svg>' },
  { key: 'mobile', label: 'Mobile', svg: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="20" x="5" y="2" rx="2"/><line x1="12" x2="12.01" y1="18" y2="18"/></svg>' },
];

const selectedTile = computed(() => {
  if (!builderStore.selectedTileId) return null;
  return tilesStore.getTileById(builderStore.selectedTileId);
});

const tileZone = computed(() => {
  if (!builderStore.selectedTileId) return null;
  return tilesStore.getZoneForTile(builderStore.selectedTileId);
});

const ancestorPath = computed(() => {
  if (!builderStore.selectedTileId) return [];
  return tilesStore.getAncestorPath(builderStore.selectedTileId);
});

// ─── SEO & Accessibility ───
const tilesWithLinks = ['button', 'image', 'icon', 'content', 'grid', 'carousel', 'card', 'flipcard', 'iconbox', 'overlay', 'overlayslider', 'team', 'linkinbio', 'nav', 'navmenu'];
const tilesWithImages = ['image', 'gallery', 'progallery', 'slideshow', 'proslider', 'carousel', 'hero', 'servicehero', 'servicegallery', 'overlay', 'overlayslider', 'shatteredimage', 'imgcompare', 'lightbox'];

const tileHasLink = computed(() => tilesWithLinks.includes(currentTileType.value));
const tileHasImage = computed(() => tilesWithImages.includes(currentTileType.value));

const linkRelOptions = [
  { value: 'nofollow', label: 'nofollow' },
  { value: 'sponsored', label: 'sponsored' },
  { value: 'ugc', label: 'ugc' },
  { value: 'noopener', label: 'noopener' },
  { value: 'noreferrer', label: 'noreferrer' },
];

const schemaMap = {
  testimonial: [
    { value: 'Review', label: 'Review (recensione)' },
    { value: 'Testimonial', label: 'Testimonial' },
  ],
  accordion: [
    { value: 'FAQPage', label: 'FAQ Page (già attivo)' },
  ],
  pricelist: [
    { value: 'Product', label: 'Product (prodotto)' },
    { value: 'Offer', label: 'Offer (offerta)' },
  ],
  pricing: [
    { value: 'Product', label: 'Product' },
    { value: 'Offer', label: 'Offer' },
  ],
  breadcrumbs: [
    { value: 'BreadcrumbList', label: 'BreadcrumbList' },
  ],
  team: [
    { value: 'Person', label: 'Person' },
  ],
  form: [
    { value: 'ContactPoint', label: 'ContactPoint' },
  ],
  postgrid: [
    { value: 'Article', label: 'Article' },
    { value: 'BlogPosting', label: 'BlogPosting' },
  ],
  counter: [
    { value: 'QuantitativeValue', label: 'QuantitativeValue' },
  ],
  section: [
    { value: 'WebPageElement', label: 'WebPageElement' },
  ],
};

const schemaOptions = computed(() => schemaMap[currentTileType.value] || []);

function toggleLinkRel(relValue) {
  const current = (selectedTile.value?.advanced?.link_rel || '').split(' ').filter(Boolean);
  const idx = current.indexOf(relValue);
  if (idx === -1) {
    current.push(relValue);
  } else {
    current.splice(idx, 1);
  }
  updateAdvanced('link_rel', current.join(' '));
}

const typeLabels = {
  section: 'Sezione',
  row: 'Riga',
  column: 'Colonna',
  grid: 'Griglia',
  hero: 'Hero',
  fragment: 'Frammento',
};

function crumbLabel(node) {
  if (!node) return '';
  // Per i tipi strutturali (section/row/column/grid/...) preferiamo typeLabels:
  // sono nomi brevi adatti al breadcrumb. Il name del config (es. "Riga / Colonne")
  // è pensato per la sidebar di drag-and-drop, non per il path gerarchico.
  if (typeLabels[node.type]) return typeLabels[node.type];
  const def = getElementDef(node.type);
  if (def?.name) return def.name;
  if (node.type === 'column') {
    return 'Col ' + ((node._colIndex ?? 0) + 1);
  }
  return node.type;
}

// Local ref for custom_widths input (avoids reactive re-render overwriting user input)
const customWidthsLocal = ref('');
const customWidthsEditing = ref(false);

// Only sync from store when tile selection changes, NOT while user is editing
watch(() => builderStore.selectedTileId, () => {
  customWidthsEditing.value = false;
  const tile = selectedTile.value;
  customWidthsLocal.value = tile?.settings?.custom_widths || '';
  // ProGallery: backward compat migration (filmstrip → strip_coverflow, auto-detect family)
  if (tile && tile.type === 'progallery' && builderStore.selectedTileId) {
    const lay = tile.settings?.layout || 'grid';
    if (lay === 'filmstrip') {
      tilesStore.updateTile(builderStore.selectedTileId, { layout: 'strip_coverflow', layout_family: 'strip' });
    } else if (lay.startsWith('strip') && tile.settings?.layout_family !== 'strip') {
      tilesStore.updateTile(builderStore.selectedTileId, { layout_family: 'strip' });
    }
  }
}, { immediate: true });

function onCustomWidthsFocus() {
  customWidthsEditing.value = true;
}

function applyCustomWidthsFromInspector() {
  customWidthsEditing.value = false;
  if (!builderStore.selectedTileId || !customWidthsLocal.value.trim()) return;
  const result = tilesStore.applyCustomWidths(builderStore.selectedTileId, customWidthsLocal.value);
  if (result) customWidthsLocal.value = result;
  builderStore.markDirtyForTile(builderStore.selectedTileId);
}

const currentTileType = computed(() => selectedTile.value?.type || '');

// Element definition from registry
const elementDef = computed(() => getElementDef(currentTileType.value));

// Fields from element definition
const elementFields = computed(() => getElementFields(currentTileType.value));

// Fallback: filtered settings for elements without definition
const hiddenKeys = ['columns_data', 'slides', 'globalBackground', 'globalLayers'];
const filteredSettings = computed(() => {
  if (!selectedTile.value?.settings) return {};
  const result = {};
  for (const [key, value] of Object.entries(selectedTile.value.settings)) {
    if (!hiddenKeys.includes(key)) {
      result[key] = value;
    }
  }
  return result;
});

/**
 * Infer a field definition from a key and value (fallback for elements without definition).
 * Replicates the old auto-detection logic.
 */
function inferField(key, value) {
  const label = key.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());

  // Boolean → toggle
  if (typeof value === 'boolean') {
    return { key, label, type: 'toggle' };
  }

  // Color hex
  if (typeof value === 'string' && /^#[0-9A-Fa-f]{6}$/.test(value)) {
    return { key, label, type: 'color' };
  }

  // Image keys
  if (['image_url', 'image', 'avatar', 'photo'].includes(key)) {
    return { key, label, type: 'image' };
  }

  // Gallery
  if (key === 'images' && Array.isArray(value)) {
    return { key, label, type: 'gallery' };
  }

  // Long text
  if (typeof value === 'string' && value.length > 80) {
    return { key, label, type: 'textarea' };
  }

  // Objects/arrays we can't edit
  if (typeof value === 'object') {
    return { key, label, type: 'text' };
  }

  // Default: text
  return { key, label, type: 'text' };
}

function evaluateCondition(condition, settings) {
  if (!condition || !settings) return true;
  const val = settings[condition.field];
  // Support op shorthand (notEmpty, empty, eq, neq)
  if (condition.op) {
    switch (condition.op) {
      case 'notEmpty': return val !== undefined && val !== null && val !== '' && val !== false;
      case 'empty':    return val === undefined || val === null || val === '' || val === false;
      case 'eq':       return val === condition.value;
      case 'neq':      return val !== condition.value;
    }
  }
  if (condition.operator) {
    const nv = parseFloat(val);
    const nc = parseFloat(condition.value);
    switch (condition.operator) {
      case '!=': return Array.isArray(condition.value) ? !condition.value.includes(val) : val !== condition.value;
      case '>':  return nv > nc;
      case '<':  return nv < nc;
      case '>=': return nv >= nc;
      case '<=': return nv <= nc;
      default:   return val === condition.value;
    }
  }
  return Array.isArray(condition.value) ? condition.value.includes(val) : val === condition.value;
}

/**
 * Check if a field should be visible — evaluates both `condition` (object) and `show` (function).
 */
function resolveField(field) {
  if (typeof field.optionsFn === 'function') {
    const settings = selectedTile.value?.settings || {};
    return { ...field, options: field.optionsFn(settings) };
  }
  return field;
}

function ensureContentItems(tile, field) {
  if (!tile?.settings) return [];
  const val = tile.settings[field.key];
  if (Array.isArray(val)) return val;
  // Inizializza dal default del config se mancante
  const def = field.newItemDefaults ? [] : [];
  const elementDefs = elementDef.value?.defaults || {};
  const fallback = elementDefs[field.key];
  const init = Array.isArray(fallback) ? JSON.parse(JSON.stringify(fallback)) : [];
  tile.settings[field.key] = init;
  return init;
}

function isFieldVisible(field) {
  const settings = selectedTile.value?.settings || {};
  if (field.condition && !evaluateCondition(field.condition, settings)) return false;
  if (typeof field.show === 'function' && !field.show(settings)) return false;
  // Filter "show only modified": confronta valore corrente con default del tile config
  if (showOnlyModified.value && field.key) {
    const cur = settings[field.key];
    const def = (elementDef.value?.defaults || {})[field.key];
    if (isFieldDefault(cur, def)) return false;
  }
  // Search filter
  const q = settingsSearch.value.trim().toLowerCase();
  if (q && field.label) {
    return field.label.toLowerCase().includes(q) || (field.key || '').toLowerCase().includes(q);
  }
  return true;
}

/** Check se il valore corrente di un field coincide col default del config (= field "non modificato"). */
function isFieldDefault(cur, def) {
  // Entrambi vuoti / undefined → considerato default
  const isEmpty = (v) => v === undefined || v === null || v === '' || v === 0 || v === false;
  if (isEmpty(cur) && isEmpty(def)) return true;
  // Confronto strutturale via JSON (per oggetti spacing/border/transform/ecc.)
  try {
    return JSON.stringify(cur) === JSON.stringify(def);
  } catch {
    return cur === def;
  }
}

/**
 * Group elementFields into collapsible sections split by separator type.
 * Returns: [{ label: string|null, fields: Field[] }, ...]
 */
const groupedSections = computed(() => {
  const sections = [];
  let current = { label: null, fields: [] };
  for (const field of elementFields.value) {
    if (field.type === 'separator') {
      if (current.fields.length > 0 || current.label !== null) {
        sections.push(current);
      }
      current = { label: field.label, fields: [] };
    } else {
      current.fields.push(field);
    }
  }
  if (current.fields.length > 0) {
    sections.push(current);
  }
  return sections;
});

/**
 * Check if a section has at least one visible field.
 */
function sectionHasVisibleFields(section) {
  return section.fields.some(f => isFieldVisible(f));
}

const tileStyle = computed(() => selectedTile.value?.style || {});

// Responsive breakpoints (shared definition) — filtered by enabled state
const _allBps = [
  { key: 'desktop', label: 'Desktop', icon: '<svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="16" height="11" rx="1"/><path d="M6 17h8M10 14v3"/></svg>' },
  { key: 'tablet_landscape', label: 'Tab L', icon: '<svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="4" width="18" height="13" rx="1.5"/><circle cx="10" cy="10" r="0.5" fill="currentColor"/></svg>' },
  { key: 'tablet', label: 'Tab P', icon: '<svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="4" y="2" width="12" height="16" rx="1.5"/><circle cx="10" cy="16" r="0.5" fill="currentColor"/></svg>' },
  { key: 'mobile_landscape', label: 'Mob L', icon: '<svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="5" width="18" height="10" rx="1.5"/><circle cx="10" cy="10" r="0.5" fill="currentColor"/></svg>' },
  { key: 'mobile', label: 'Mobile', icon: '<svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="5" y="2" width="10" height="16" rx="1.5"/><circle cx="10" cy="16" r="0.5" fill="currentColor"/></svg>' },
];
const _bpEnabled = (window.oloData || {}).breakpointsEnabled || {};
const responsiveBreakpoints = _allBps.filter(bp =>
  bp.key === 'desktop' || _bpEnabled[bp.key] !== false
);

// Spacing breakpoints
const spacingBp = ref('desktop');
const spacingBpLabel = computed(() => responsiveBreakpoints.find(b => b.key === spacingBp.value)?.label || '');
function spacingKey(base) {
  return spacingBp.value === 'desktop' ? base : base + '_' + spacingBp.value;
}

// Positioning breakpoints
const positionBp = ref('desktop');
const positionBpLabel = computed(() => responsiveBreakpoints.find(b => b.key === positionBp.value)?.label || '');
function positionKey(base) {
  return positionBp.value === 'desktop' ? base : base + '_' + positionBp.value;
}

// Margin linked/unlinked
const marginObj = computed(() => ({
  top: parseInt(tileStyle.value[spacingKey('margin_top')]) || 0,
  right: parseInt(tileStyle.value[spacingKey('margin_right')]) || 0,
  bottom: parseInt(tileStyle.value[spacingKey('margin_bottom')]) || 0,
  left: parseInt(tileStyle.value[spacingKey('margin_left')]) || 0,
}));
function onMarginUpdate(val) {
  updateStyle(spacingKey('margin_top'), val.top);
  updateStyle(spacingKey('margin_right'), val.right);
  updateStyle(spacingKey('margin_bottom'), val.bottom);
  updateStyle(spacingKey('margin_left'), val.left);
}

// Padding linked/unlinked
const paddingObj = computed(() => ({
  top: parseInt(tileStyle.value[spacingKey('padding_top')]) || 0,
  right: parseInt(tileStyle.value[spacingKey('padding_right')]) || 0,
  bottom: parseInt(tileStyle.value[spacingKey('padding_bottom')]) || 0,
  left: parseInt(tileStyle.value[spacingKey('padding_left')]) || 0,
}));
function onPaddingUpdate(val) {
  updateStyle(spacingKey('padding_top'), val.top);
  updateStyle(spacingKey('padding_right'), val.right);
  updateStyle(spacingKey('padding_bottom'), val.bottom);
  updateStyle(spacingKey('padding_left'), val.left);
}

const tileBg = computed(() => {
  const s = tileStyle.value;
  if (s.bg) return s.bg;
  if (s.bg_color) return { type: 'solid', color: s.bg_color };
  return { type: 'none' };
});

const tileAdvanced = computed(() => selectedTile.value?.advanced || {});

const singlePostItems = computed(() => (window.oloData || {}).singlePostItems || []);

function toggleCondPostId(postId) {
  const current = [...(tileAdvanced.value.cond_post_ids || [])];
  const idx = current.indexOf(postId);
  if (idx >= 0) {
    current.splice(idx, 1);
  } else {
    current.push(postId);
  }
  updateAdvanced('cond_post_ids', current);
}

// ─── A/B Testing ───
const abTest = ref(null);
const abStats = ref(null);
const abLoading = ref(false);
let abStatsTimer = null;

const abVariantB = computed(() => {
  if (!abTest.value) return {};
  const variants = abTest.value.variants || {};
  return variants.b || {};
});

const abAvailableFields = computed(() => {
  const used = abVariantB.value;
  return elementFields.value
    .filter(f => f.key && f.type !== 'separator' && f.type !== 'content-items' && !(f.key in used))
    .map(f => ({ key: f.key, label: f.label || f.key }));
});

function abFieldLabel(key) {
  const f = elementFields.value.find(x => x.key === key);
  return f ? (f.label || key) : key;
}

async function abFetch(path, opts = {}) {
  const olo = window.oloData || {};
  const res = await fetch(olo.restUrl + path, {
    headers: { 'X-WP-Nonce': olo.nonce, 'Content-Type': 'application/json', ...opts.headers },
    ...opts,
  });
  return res.json();
}

async function loadAbTest() {
  if (!builderStore.selectedTileId) { abTest.value = null; abStats.value = null; return; }
  abLoading.value = true;
  try {
    const tests = await abFetch('/ab-tests');
    const tplId = builderStore.currentTemplate?.id;
    const match = (tests || []).find(t => t.tile_id === builderStore.selectedTileId && (!tplId || parseInt(t.template_id) === parseInt(tplId)));
    abTest.value = match || null;
    abStats.value = null;
    if (match && (match.status === 'running' || match.status === 'stopped')) {
      fetchAbStats();
    }
  } catch (e) { abTest.value = null; }
  abLoading.value = false;
}

async function createAbTest() {
  const tplId = builderStore.currentTemplate?.id;
  if (!tplId) { alert('Salva il template prima di creare un test A/B.'); return; }
  abLoading.value = true;
  try {
    const data = await abFetch('/ab-tests', {
      method: 'POST',
      body: JSON.stringify({
        name: 'Test ' + (currentTileType.value || 'tile'),
        tile_id: builderStore.selectedTileId,
        template_id: tplId,
        variants: { a: {}, b: {} },
        goal_type: 'click',
      }),
    });
    if (data.id) await loadAbTest();
  } catch (e) { console.error(e); }
  abLoading.value = false;
}

async function updateAbField(key, value) {
  if (!abTest.value) return;
  await abFetch('/ab-tests/' + abTest.value.id, {
    method: 'PUT',
    body: JSON.stringify({ [key]: value }),
  });
  abTest.value[key] = value;
}

function setAbOverride(key, value) {
  if (!abTest.value) return;
  const variants = { ...(abTest.value.variants || {}), b: { ...abVariantB.value, [key]: value } };
  abTest.value.variants = variants;
  abFetch('/ab-tests/' + abTest.value.id, {
    method: 'PUT',
    body: JSON.stringify({ variants }),
  });
}

function removeAbOverride(key) {
  if (!abTest.value) return;
  const b = { ...abVariantB.value };
  delete b[key];
  const variants = { ...(abTest.value.variants || {}), b };
  abTest.value.variants = variants;
  abFetch('/ab-tests/' + abTest.value.id, {
    method: 'PUT',
    body: JSON.stringify({ variants }),
  });
}

function addAbOverride(key) {
  if (!key || !abTest.value) return;
  const currentVal = selectedTile.value?.settings?.[key] || '';
  setAbOverride(key, currentVal);
}

async function startAbTest() {
  if (!abTest.value) return;
  await abFetch('/ab-tests/' + abTest.value.id + '/start', { method: 'POST' });
  abTest.value.status = 'running';
  fetchAbStats();
  startAbStatsPolling();
}

async function stopAbTest() {
  if (!abTest.value) return;
  await abFetch('/ab-tests/' + abTest.value.id + '/stop', { method: 'POST' });
  abTest.value.status = 'stopped';
  stopAbStatsPolling();
  fetchAbStats();
}

async function deleteAbTest() {
  if (!abTest.value) return;
  await abFetch('/ab-tests/' + abTest.value.id, { method: 'DELETE' });
  abTest.value = null;
  abStats.value = null;
  stopAbStatsPolling();
}

async function fetchAbStats() {
  if (!abTest.value) return;
  try {
    abStats.value = await abFetch('/ab-tests/' + abTest.value.id + '/stats');
  } catch (e) { /* ignore */ }
}

function startAbStatsPolling() {
  stopAbStatsPolling();
  abStatsTimer = setInterval(fetchAbStats, 30000);
}

function stopAbStatsPolling() {
  if (abStatsTimer) { clearInterval(abStatsTimer); abStatsTimer = null; }
}

watch(() => builderStore.selectedTileId, () => {
  stopAbStatsPolling();
  loadAbTest();
}, { immediate: true });

// Migrate old flat parallax format to new multi-stop object
const hasElementParallax = computed(() => {
  const adv = tileAdvanced.value;
  if (adv.parallax && typeof adv.parallax === 'object') return true;
  if (adv.parallax_y_start || adv.parallax_y_end || adv.parallax_opacity_start != null) return true;
  return false;
});

const elementParallaxData = computed(() => {
  const adv = tileAdvanced.value;
  // Already new format
  if (adv.parallax && typeof adv.parallax === 'object') {
    return adv.parallax;
  }
  // Migrate from old flat keys
  const obj = { x: [], y: [], scale: [], rotate: [], opacity: [], blur: [], nomobile: true, easing: null, start: '', end: '' };
  const yStart = parseInt(adv.parallax_y_start) || 0;
  const yEnd = parseInt(adv.parallax_y_end) || 0;
  if (yStart !== 0 || yEnd !== 0) {
    obj.y = [{ value: yStart, position: 0 }, { value: yEnd, position: 100 }];
  }
  const opStart = adv.parallax_opacity_start;
  const opEnd = adv.parallax_opacity_end;
  if (opStart !== '' && opStart != null && opEnd !== '' && opEnd != null) {
    obj.opacity = [{ value: parseFloat(opStart), position: 0 }, { value: parseFloat(opEnd), position: 100 }];
  }
  const scStart = adv.parallax_scale_start;
  const scEnd = adv.parallax_scale_end;
  if (scStart !== '' && scStart != null && scEnd !== '' && scEnd != null) {
    obj.scale = [{ value: parseFloat(scStart), position: 0 }, { value: parseFloat(scEnd), position: 100 }];
  }
  if (adv.parallax_nomobile !== undefined) {
    obj.nomobile = adv.parallax_nomobile !== false;
  }
  return obj;
});

// V3.23.1 — Preset library used by tiles that expose a "preset" select.
// When the user changes the preset (and it's not 'custom'), all the listed
// keys get batch-populated as normal field values. From that point on, any
// manual edit on those fields wins (no runtime override in the PHP renderer).
// Pattern condiviso da decine di tile (carousel, marquee, chart, content, …).
// Estratto per evitare ~840 LOC di copia (12 entries × ~70 tile).
// Per personalizzare un tile, basta override-are una o più chiavi dopo lo spread:
//   carousel: { ...BASE_THEME_PRESETS, 'modern-clean': { ... custom ... } }
// v1.0.51 — Audit pre-lancio preset stilistici. Fix anti-invisibilità:
// - modern-clean: shadow 'md' invece di 'sm' per essere distinguibile su sfondo bianco
// - minimal-mono: background_color '#f9fafb' (gray-50) invece di '' (era ghost invisibile senza wrapper bg)
// - compact-inline: shadow 'md' + background_color '#f3f4f6' (gray-100) per contrasto definito
// - glass-frosted: background_color 'rgba(255,255,255,0.55)' invece di 0.15 (visibile anche su sfondo chiaro)
const BASE_THEME_PRESETS = {
  'modern-clean':    { background_color: '#fff', text_color: '#0f172a', shadow: 'md',   border_radius: '12' },
  'minimal-mono':    { background_color: '#f9fafb', text_color: '#374151', shadow: 'none', border_radius: '0'  },
  'magazine-bold':   { background_color: '#000', text_color: '#fff', shadow: 'none', border_radius: '0'  },
  'editorial-serif': { background_color: '#1f2937', text_color: '#fff', shadow: 'md',   border_radius: '4'  },
  'compact-inline':  { background_color: '#f3f4f6', text_color: '#374151', shadow: 'md',   border_radius: '4'  },
  'glass-frosted':   { background_color: 'rgba(255,255,255,0.55)', text_color: '#0f172a', shadow: 'lg',   border_radius: '16' },
  'neon-glow':       { background_color: '#0a0a0a', text_color: '#00ffff', shadow: 'lg',   border_radius: '8'  },
  'brutalist-stamp': { background_color: '#fde047', text_color: '#000', shadow: 'xl',   border_radius: '0'  },
  'gradient-aurora': { background_color: 'linear-gradient(135deg,#a855f7,#ec4899)', text_color: '#fff', shadow: 'lg',   border_radius: '16' },
  'sticker-fun':     { background_color: '#fef3c7', text_color: '#78350f', shadow: 'md',   border_radius: '12' },
  'retro-terminal':  { background_color: '#0a0a0a', text_color: '#22c55e', shadow: 'none', border_radius: '4'  },
  'tilt-3d':         { background_color: '#6366f1', text_color: '#fff', shadow: 'xl',   border_radius: '12' },
};

const TILE_PRESETS = {
  lottie: {
    'modern-clean':    { shadow: 'sm',   border_radius: '12', width: '280', speed: '1',   alignment: 'center', hover_action: 'play' },
    'minimal-frame':   { shadow: 'none', border_radius: '0',  width: '180', speed: '0.8', alignment: 'left',   hover_action: 'none' },
    'magazine-bold':   { shadow: 'none', border_radius: '0',  width: '400', speed: '1',   alignment: 'left',   hover_action: 'play' },
    'cinema-wide':     { shadow: 'lg',   border_radius: '4',  width: '500', speed: '1.2', alignment: 'center', hover_action: 'play' },
    'centered-large':  { shadow: 'md',   border_radius: '20', width: '450', speed: '1',   alignment: 'center', hover_action: 'play' },
    'glass-frame':     { shadow: 'lg',   border_radius: '20', width: '300', speed: '0.7', alignment: 'center', hover_action: 'reverse' },
    'neon-glow':       { shadow: 'lg',   border_radius: '8',  width: '260', speed: '1.5', alignment: 'center', hover_action: 'play' },
    'brutalist-block': { shadow: 'xl',   border_radius: '0',  width: '500', speed: '2',   alignment: 'left',   hover_action: 'play' },
    'gradient-aurora': { shadow: 'lg',   border_radius: '16', width: '320', speed: '1',   alignment: 'center', hover_action: 'play' },
    'sticker-fun':     { shadow: 'md',   border_radius: '12', width: '240', speed: '1.3', alignment: 'center', hover_action: 'play' },
    'retro-vhs':       { shadow: 'none', border_radius: '4',  width: '280', speed: '0.6', alignment: 'left',   hover_action: 'reverse' },
    'tilt-3d':         { shadow: 'xl',   border_radius: '12', width: '320', speed: '1',   alignment: 'center', hover_action: 'play' },
  },
  video: {
    'modern-clean':    { shadow: 'sm',   border_radius: '12', display_mode: '16:9', play_icon_size: '80',  play_icon_color: '#ffffff', overlay_color: '#000000', overlay_opacity: '20' },
    'minimal-frame':   { shadow: 'none', border_radius: '0',  display_mode: '16:9', play_icon_size: '60',  play_icon_color: '#ffffff', overlay_color: '',        overlay_opacity: '0'  },
    'cinema-wide':     { shadow: 'lg',   border_radius: '4',  display_mode: '21:9', play_icon_size: '100', play_icon_color: '#ffffff', overlay_color: '#000000', overlay_opacity: '35' },
    'magazine-bold':   { shadow: 'none', border_radius: '0',  display_mode: '16:9', play_icon_size: '80',  play_icon_color: '#000000', overlay_color: '#ffffff', overlay_opacity: '10' },
    'centered-large':  { shadow: 'md',   border_radius: '20', display_mode: '4:3',  play_icon_size: '100', play_icon_color: '#ffffff', overlay_color: '#000000', overlay_opacity: '25' },
    'glass-frame':     { shadow: 'lg',   border_radius: '20', display_mode: '16:9', play_icon_size: '80',  play_icon_color: '#ffffff', overlay_color: 'rgba(255,255,255,0.15)', overlay_opacity: '60' },
    'neon-glow':       { shadow: 'lg',   border_radius: '8',  display_mode: '16:9', play_icon_size: '80',  play_icon_color: '#00ffff', overlay_color: '#0a0a0a', overlay_opacity: '40' },
    'brutalist-block': { shadow: 'xl',   border_radius: '0',  display_mode: '1:1',  play_icon_size: '120', play_icon_color: '#fde047', overlay_color: '#000000', overlay_opacity: '0'  },
    'gradient-border': { shadow: 'md',   border_radius: '16', display_mode: '16:9', play_icon_size: '80',  play_icon_color: '#ffffff', overlay_color: 'linear-gradient(135deg,#a855f7,#ec4899)', overlay_opacity: '15' },
    'sticker-tape':    { shadow: 'md',   border_radius: '12', display_mode: '16:9', play_icon_size: '70',  play_icon_color: '#78350f', overlay_color: '#fef3c7', overlay_opacity: '5'  },
    'retro-vhs':       { shadow: 'none', border_radius: '4',  display_mode: '4:3',  play_icon_size: '60',  play_icon_color: '#22c55e', overlay_color: '#0a0a0a', overlay_opacity: '25' },
    'tilt-3d':         { shadow: 'xl',   border_radius: '12', display_mode: '16:9', play_icon_size: '90',  play_icon_color: '#ffffff', overlay_color: '#6366f1', overlay_opacity: '20' },
  },
  social: {
    'modern-pills':    { style: 'icon-label', size: '40', icon_color: '#fff',    bg_color: '#0ea5e9', border_radius: '999' },
    'minimal-line':    { style: 'icon-label', size: '32', icon_color: '#1f2937', bg_color: '',         border_radius: '0'   },
    'magazine-bold':   { style: 'icon-label', size: '36', icon_color: '#fff',    bg_color: '#000',     border_radius: '0'   },
    'circle-icons':    { style: 'icon-only',  size: '44', icon_color: '#fff',    bg_color: '#7c3aed', border_radius: '999' },
    'compact-row':     { style: 'icon-only',  size: '32', icon_color: '#374151', bg_color: '#f3f4f6', border_radius: '8'   },
    'glass-pills':     { style: 'icon-label', size: '40', icon_color: '#fff',    bg_color: 'rgba(255,255,255,0.15)', border_radius: '999' },
    'neon-glow':       { style: 'icon-only',  size: '40', icon_color: '#00ffff', bg_color: '#0a0a0a', border_radius: '4'   },
    'brutalist-stamp': { style: 'icon-label', size: '44', icon_color: '#fde047', bg_color: '#000',    border_radius: '0'   },
    'gradient-pills':  { style: 'icon-label', size: '40', icon_color: '#fff',    bg_color: 'linear-gradient(135deg,#a855f7,#ec4899)', border_radius: '999' },
    'sticker-fun':     { style: 'icon-only',  size: '40', icon_color: '#78350f', bg_color: '#fef3c7', border_radius: '12'  },
    'retro-vhs':       { style: 'icon-label', size: '36', icon_color: '#22c55e', bg_color: '#0a0a0a', border_radius: '0'   },
    'tilt-3d':         { style: 'icon-only',  size: '44', icon_color: '#fff',    bg_color: '#6366f1', border_radius: '8'   },
  },
  // v1.4.11 — ricetta token-first completa estratta in src/config/megamenuPresets.js.
  // Sostituisce il vecchio bundle inline (hex off-brand #0ea5e9/#a855f7 e
  // hover_effect:'lift' inesistente nel config). Applicata dal dispatcher
  // applyTilePresetTheme al cambio di `settings.preset`.
  megamenu: MEGAMENU_PRESETS,
  sitelogo: {
    'modern-clean':    { hover_opacity: '85',  max_height: '48', alignment: 'left',   transition_duration: '0.3', show_tagline: false },
    'minimal-mono':    { hover_opacity: '70',  max_height: '36', alignment: 'left',   transition_duration: '0.2', show_tagline: false },
    'magazine-bold':   { hover_opacity: '90',  max_height: '60', alignment: 'left',   transition_duration: '0.4', show_tagline: true  },
    'centered-large':  { hover_opacity: '85',  max_height: '80', alignment: 'center', transition_duration: '0.3', show_tagline: true  },
    'compact-inline':  { hover_opacity: '80',  max_height: '32', alignment: 'left',   transition_duration: '0.2', show_tagline: false },
    'glass-frame':     { hover_opacity: '95',  max_height: '52', alignment: 'center', transition_duration: '0.4', show_tagline: false },
    'neon-glow':       { hover_opacity: '100', max_height: '50', alignment: 'center', transition_duration: '0.5', show_tagline: false },
    'brutalist-block': { hover_opacity: '100', max_height: '70', alignment: 'left',   transition_duration: '0.1', show_tagline: true  },
    'gradient-soft':   { hover_opacity: '90',  max_height: '54', alignment: 'center', transition_duration: '0.4', show_tagline: false },
    'sticker-fun':     { hover_opacity: '85',  max_height: '56', alignment: 'left',   transition_duration: '0.5', show_tagline: true  },
    'retro-vhs':       { hover_opacity: '75',  max_height: '44', alignment: 'left',   transition_duration: '0.2', show_tagline: true  },
    'tilt-3d':         { hover_opacity: '90',  max_height: '60', alignment: 'center', transition_duration: '0.4', show_tagline: false },
  },
  headline: {
    // Pulito, no effetti. Decorazione "linea sotto il titolo" classica.
    'modern-clean':      { font_weight: '700', text_transform: 'none',      letter_spacing: '0',   heading_color: '#0f172a', decoration: 'line',    text_shadow: '', gradient_text: false, text_stroke: '0' },
    // Tipografia mono ultra-pulita, no decorazione, allineato sinistra.
    'minimal-mono':      { font_weight: '500', text_transform: 'none',      letter_spacing: '-1',  heading_color: '#1f2937', alignment: 'left',     decoration: 'none', text_shadow: '', gradient_text: false, text_stroke: '0' },
    // Display tipografico forte. Italic + nero pieno.
    'magazine-editorial':{ font_weight: '900', text_transform: 'none',      letter_spacing: '-2',  heading_color: '#000',    heading_italic: true,  decoration: 'divider', text_shadow: '', gradient_text: false, text_stroke: '0' },
    // Serif elegante, italic, decorazione punto.
    'editorial-serif':   { font_weight: '700', text_transform: 'none',      letter_spacing: '0',   heading_color: '#111',    heading_italic: true,  decoration: 'dot', text_shadow: '', gradient_text: false, text_stroke: '0' },
    // Uppercase compatto, decorazione divider.
    'compact-inline':    { font_weight: '600', text_transform: 'uppercase', letter_spacing: '2',   heading_color: '#374151', decoration: 'divider', text_shadow: '', gradient_text: false, text_stroke: '0' },
    // Vetro: bg semi-trasparente scuro + heading bianco. v1.0.58: aggiunto bg_color (era invisibile su sfondo bianco).
    'glass-overlay':     { font_weight: '700', text_transform: 'none',      letter_spacing: '0',   heading_color: '#fff',    bg_color: 'rgba(15,23,42,0.85)', text_shadow: '2px 2px 4px rgba(0,0,0,0.3)',  decoration: 'none', gradient_text: false, text_stroke: '0' },
    // Cyan + bagliore neon forte su nero. v1.0.58: aggiunto bg_color nero (era cyan invisibile su bianco).
    'neon-glow':         { font_weight: '700', text_transform: 'uppercase', letter_spacing: '4',   heading_color: '#00ffff', bg_color: '#0a0a0a', text_shadow: '0 0 20px rgba(99,102,241,0.8)', decoration: 'none', gradient_text: false, text_stroke: '0' },
    // Stroke nero pieno + uppercase aggressivo (brutalismo grafico).
    'brutalist-mega':    { font_weight: '900', text_transform: 'uppercase', letter_spacing: '-3',  heading_color: '#000',    text_stroke: '3', text_stroke_color: '#000', decoration: 'none', text_shadow: '', gradient_text: false },
    // Testo riempito con gradiente colorato. v1.0.58: aggiunto bg_color gradiente (era invisibile se gradient_text non rendering).
    'gradient-aurora':   { font_weight: '800', text_transform: 'none',      letter_spacing: '0',   heading_color: '#fff',    bg_color: 'linear-gradient(135deg,#a855f7,#ec4899)', gradient_text: true, gradient_from: '#a855f7', gradient_to: '#ec4899', gradient_angle: '90', decoration: 'none', text_shadow: '', text_stroke: '0' },
    // Stile "sticker", giallo sole + bordo arrotondato implicito.
    'sticker-fun':       { font_weight: '800', text_transform: 'none',      letter_spacing: '0',   heading_color: '#78350f', text_shadow: '3px 3px 0 rgba(253,224,71,1)', decoration: 'star', text_stroke: '0', gradient_text: false },
    // Verde fosforescente + uppercase, blend tipo terminale. v1.0.58: aggiunto bg_color nero.
    'retro-terminal':    { font_weight: '700', text_transform: 'uppercase', letter_spacing: '2',   heading_color: '#22c55e', bg_color: '#0a0a0a', text_shadow: '0 0 10px rgba(34,197,94,0.6)', decoration: 'none', blend_mode: 'screen', text_stroke: '0', gradient_text: false },
    // 3D depth: bg indigo scuro + heading bianco. v1.0.58: aggiunto bg_color (era invisibile su bianco).
    'tilt-3d':           { font_weight: '800', text_transform: 'none',      letter_spacing: '0',   heading_color: '#fff',    bg_color: '#6366f1', text_shadow: '4px 4px 10px rgba(0,0,0,0.5)', decoration: 'none', text_stroke: '0', gradient_text: false },
  },
  animatedheading: {
    ...BASE_THEME_PRESETS,
  },
  authorbox: {
    ...BASE_THEME_PRESETS,
  },
  blendtext: {
    ...BASE_THEME_PRESETS,
  },
  carousel: {
    ...BASE_THEME_PRESETS,
  },
  chart: {
    ...BASE_THEME_PRESETS,
  },
  content: {
    ...BASE_THEME_PRESETS,
  },
  facebookpage: {
    ...BASE_THEME_PRESETS,
  },
  grid: {
    ...BASE_THEME_PRESETS,
  },
  instagram: {
    ...BASE_THEME_PRESETS,
  },
  langswitcher: {
    ...BASE_THEME_PRESETS,
  },
  linkinbio: {
    ...BASE_THEME_PRESETS,
  },
  map: {
    ...BASE_THEME_PRESETS,
  },
  marquee: {
    ...BASE_THEME_PRESETS,
  },
  nav: {
    ...BASE_THEME_PRESETS,
  },
  navmenu: {
    ...BASE_THEME_PRESETS,
  },
  newsticker: {
    ...BASE_THEME_PRESETS,
  },
  osmmap: {
    ...BASE_THEME_PRESETS,
  },
  overlay: {
    ...BASE_THEME_PRESETS,
  },
  pagetitlebar: {
    // v1.0.58 — Override per pagetitlebar: i default `title_color/#FFF` + `subtitle_color/#D1D5DB`
    // sono pensati per sfondo scuro. Sui preset LIGHT (modern-clean, minimal-mono, compact-inline,
    // glass-frosted, brutalist-stamp, sticker-fun) il titolo bianco diventa invisibile.
    // Per i preset DARK (magazine-bold, editorial-serif, neon-glow, gradient-aurora, retro-terminal, tilt-3d)
    // il default `#FFF` va bene; servono override solo sui LIGHT.
    'modern-clean':    { background_color: '#fff', text_color: '#0f172a', shadow: 'md', border_radius: '12', title_color: '#0f172a', subtitle_color: '#475569', breadcrumb_color: '#64748b' },
    'minimal-mono':    { background_color: '#f9fafb', text_color: '#374151', shadow: 'none', border_radius: '0', title_color: '#1f2937', subtitle_color: '#475569', breadcrumb_color: '#6b7280' },
    'magazine-bold':   { background_color: '#000', text_color: '#fff', shadow: 'none', border_radius: '0', title_color: '#fff', subtitle_color: '#e5e7eb', breadcrumb_color: '#9ca3af' },
    'editorial-serif': { background_color: '#1f2937', text_color: '#fff', shadow: 'md', border_radius: '4', title_color: '#fff', subtitle_color: '#e5e7eb', breadcrumb_color: '#cbd5e1' },
    'compact-inline':  { background_color: '#f3f4f6', text_color: '#374151', shadow: 'md', border_radius: '4', title_color: '#1f2937', subtitle_color: '#475569', breadcrumb_color: '#4b5563' },
    'glass-frosted':   { background_color: 'rgba(15,23,42,0.85)', text_color: '#fff', shadow: 'lg', border_radius: '16', title_color: '#fff', subtitle_color: '#e5e7eb', breadcrumb_color: '#cbd5e1' },
    'neon-glow':       { background_color: '#0a0a0a', text_color: '#00ffff', shadow: 'lg', border_radius: '8', title_color: '#00ffff', subtitle_color: '#a5f3fc', breadcrumb_color: '#67e8f9' },
    'brutalist-stamp': { background_color: '#fde047', text_color: '#000', shadow: 'xl', border_radius: '0', title_color: '#000', subtitle_color: '#1f2937', breadcrumb_color: '#374151' },
    'gradient-aurora': { background_color: 'linear-gradient(135deg,#a855f7,#ec4899)', text_color: '#fff', shadow: 'lg', border_radius: '16', title_color: '#fff', subtitle_color: '#fce7f3', breadcrumb_color: '#fbcfe8' },
    'sticker-fun':     { background_color: '#fef3c7', text_color: '#78350f', shadow: 'md', border_radius: '12', title_color: '#78350f', subtitle_color: '#92400e', breadcrumb_color: '#b45309' },
    'retro-terminal':  { background_color: '#0a0a0a', text_color: '#22c55e', shadow: 'none', border_radius: '4', title_color: '#22c55e', subtitle_color: '#86efac', breadcrumb_color: '#4ade80' },
    'tilt-3d':         { background_color: '#6366f1', text_color: '#fff', shadow: 'xl', border_radius: '12', title_color: '#fff', subtitle_color: '#e0e7ff', breadcrumb_color: '#ffffff' },
  },
  popover: {
    ...BASE_THEME_PRESETS,
  },
  pricelist: {
    // v1.0.58 — preset self-contained (era 0/12 PASS perché defaults vuoti + card hardcoded).
    'modern-clean':    { background_color: '#fff',                                       text_color: '#0f172a', shadow: 'md',   border_radius: '12', card_bg: '#ffffff', title_color: '#0f172a', description_color: '#475569', price_color: '#0f172a', separator_color: '#e5e7eb' },
    'minimal-mono':    { background_color: '#f9fafb',                                    text_color: '#374151', shadow: 'none', border_radius: '0',  card_bg: '',        title_color: '#1f2937', description_color: '#4b5563', price_color: '#1f2937', separator_color: '#d1d5db' },
    'magazine-bold':   { background_color: '#000',                                       text_color: '#fff',    shadow: 'none', border_radius: '0',  card_bg: '#0a0a0a', title_color: '#fff',    description_color: '#e5e7eb', price_color: '#fde047', separator_color: '#374151' },
    'editorial-serif': { background_color: '#1f2937',                                    text_color: '#fff',    shadow: 'md',   border_radius: '4',  card_bg: '#1f2937', title_color: '#fff',    description_color: '#e5e7eb', price_color: '#fbbf24', separator_color: '#4b5563' },
    'compact-inline':  { background_color: '#f3f4f6',                                    text_color: '#374151', shadow: 'md',   border_radius: '4',  card_bg: '#ffffff', title_color: '#1f2937', description_color: '#4b5563', price_color: '#1f2937', separator_color: '#e5e7eb' },
    'glass-frosted':   { background_color: 'rgba(15,23,42,0.85)',                       text_color: '#fff',    shadow: 'lg',   border_radius: '16', card_bg: 'rgba(255,255,255,0.1)', title_color: '#fff', description_color: '#e5e7eb', price_color: '#fff', separator_color: 'rgba(255,255,255,0.2)' },
    'neon-glow':       { background_color: '#0a0a0a',                                    text_color: '#00ffff', shadow: 'lg',   border_radius: '8',  card_bg: '#0a0a0a', title_color: '#00ffff', description_color: '#a5f3fc', price_color: '#00ffff', separator_color: 'rgba(0,255,255,0.3)' },
    'brutalist-stamp': { background_color: '#fde047',                                    text_color: '#000',    shadow: 'xl',   border_radius: '0',  card_bg: '#fff',    title_color: '#000',    description_color: '#1f2937', price_color: '#000', separator_color: '#000' },
    'gradient-aurora': { background_color: 'linear-gradient(135deg,#a855f7,#ec4899)',    text_color: '#fff',    shadow: 'lg',   border_radius: '16', card_bg: 'rgba(255,255,255,0.15)', title_color: '#fff', description_color: '#fce7f3', price_color: '#fff', separator_color: 'rgba(255,255,255,0.3)' },
    'sticker-fun':     { background_color: '#fef3c7',                                    text_color: '#78350f', shadow: 'md',   border_radius: '12', card_bg: '#ffffff', title_color: '#78350f', description_color: '#92400e', price_color: '#78350f', separator_color: '#fbbf24' },
    'retro-terminal':  { background_color: '#0a0a0a',                                    text_color: '#22c55e', shadow: 'none', border_radius: '4',  card_bg: '#0c0c0c', title_color: '#22c55e', description_color: '#86efac', price_color: '#22c55e', separator_color: 'rgba(34,197,94,0.3)' },
    'tilt-3d':         { background_color: '#6366f1',                                    text_color: '#fff',    shadow: 'xl',   border_radius: '12', card_bg: '#312e81', title_color: '#fff',    description_color: '#e0e7ff', price_color: '#fde047', separator_color: 'rgba(255,255,255,0.3)' },
  },
  soundcloud: {
    ...BASE_THEME_PRESETS,
  },
  subnav: {
    ...BASE_THEME_PRESETS,
  },
  'text-block': {
    ...BASE_THEME_PRESETS,
  },
  textmask: {
    ...BASE_THEME_PRESETS,
  },
  textpath: {
    ...BASE_THEME_PRESETS,
  },
  togglebtn: {
    ...BASE_THEME_PRESETS,
  },
  twitterfeed: {
    ...BASE_THEME_PRESETS,
  },
  videoplaylist: {
    ...BASE_THEME_PRESETS,
  },
  wpcomments: {
    ...BASE_THEME_PRESETS,
  },
  woo_addtocart: {
    ...BASE_THEME_PRESETS,
  },
  woo_breadcrumbs: {
    ...BASE_THEME_PRESETS,
  },
  woo_cart: {
    ...BASE_THEME_PRESETS,
  },
  woo_categories: {
    ...BASE_THEME_PRESETS,
  },
  woo_checkout: {
    ...BASE_THEME_PRESETS,
  },
  woo_checkout_multistep: {
    ...BASE_THEME_PRESETS,
  },
  woo_comparison: {
    ...BASE_THEME_PRESETS,
  },
  woo_cross_sells: {
    ...BASE_THEME_PRESETS,
  },
  woo_minicart: {
    ...BASE_THEME_PRESETS,
  },
  woo_myaccount: {
    ...BASE_THEME_PRESETS,
  },
  woo_notices: {
    ...BASE_THEME_PRESETS,
  },
  woo_order_tracking: {
    ...BASE_THEME_PRESETS,
  },
  woo_price: {
    ...BASE_THEME_PRESETS,
  },
  woo_product_bundle: {
    ...BASE_THEME_PRESETS,
  },
  woo_product_description: {
    ...BASE_THEME_PRESETS,
  },
  woo_product_filter: {
    ...BASE_THEME_PRESETS,
  },
  woo_product_gallery_slider: {
    ...BASE_THEME_PRESETS,
  },
  woo_product_image: {
    ...BASE_THEME_PRESETS,
  },
  woo_product_meta: {
    ...BASE_THEME_PRESETS,
  },
  woo_product_navigation: {
    ...BASE_THEME_PRESETS,
  },
  woo_product_stock: {
    ...BASE_THEME_PRESETS,
  },
  woo_product_tabs: {
    ...BASE_THEME_PRESETS,
  },
  woo_product_title: {
    ...BASE_THEME_PRESETS,
  },
  woo_products: {
    ...BASE_THEME_PRESETS,
  },
  woo_quickview: {
    ...BASE_THEME_PRESETS,
  },
  woo_rating: {
    ...BASE_THEME_PRESETS,
  },
  woo_recently_viewed: {
    ...BASE_THEME_PRESETS,
  },
  woo_related: {
    ...BASE_THEME_PRESETS,
  },
  woo_sale_badge: {
    ...BASE_THEME_PRESETS,
  },
  woo_upsells: {
    ...BASE_THEME_PRESETS,
  },
  woo_wishlist: {
    ...BASE_THEME_PRESETS,
  },
  image: {
    'modern-clean':    { object_fit: 'cover',   aspect_ratio: 'auto',  filter_brightness: '100', filter_saturation: '100', shadow: 'sm'   },
    'minimal-frame':   { object_fit: 'cover',   aspect_ratio: '1/1',   filter_brightness: '100', filter_saturation: '100', shadow: 'none' },
    'magazine-bold':   { object_fit: 'cover',   aspect_ratio: '4/3',   filter_brightness: '105', filter_saturation: '110', shadow: 'none' },
    'cinema-wide':     { object_fit: 'cover',   aspect_ratio: '21/9',  filter_brightness: '95',  filter_saturation: '105', shadow: 'lg'   },
    'polaroid':        { object_fit: 'cover',   aspect_ratio: '4/5',   filter_brightness: '105', filter_saturation: '95',  shadow: 'md'   },
    'glass-frame':     { object_fit: 'cover',   aspect_ratio: 'auto',  filter_brightness: '100', filter_saturation: '100', shadow: 'lg'   },
    'neon-glow':       { object_fit: 'cover',   aspect_ratio: 'auto',  filter_brightness: '110', filter_saturation: '120', shadow: 'lg'   },
    'brutalist-block': { object_fit: 'cover',   aspect_ratio: '1/1',   filter_brightness: '95',  filter_saturation: '110', shadow: 'xl'   },
    'gradient-border': { object_fit: 'cover',   aspect_ratio: 'auto',  filter_brightness: '100', filter_saturation: '105', shadow: 'md'   },
    'sticker-tape':    { object_fit: 'cover',   aspect_ratio: '4/3',   filter_brightness: '105', filter_saturation: '110', shadow: 'md'   },
    'retro-vhs':       { object_fit: 'cover',   aspect_ratio: '4/3',   filter_brightness: '95',  filter_saturation: '85',  shadow: 'none' },
    'tilt-3d':         { object_fit: 'cover',   aspect_ratio: 'auto',  filter_brightness: '100', filter_saturation: '110', shadow: 'xl'   },
  },
  button: {
    'modern-clean':    { bg_color: '#0ea5e9', text_color: '#fff',    border_radius: '8',   font_size: '15', font_weight: '600', text_transform: 'none',      shadow: 'sm',   hover_bg_color: '#0284c7', hover_effect: 'lift'   },
    // v1.0.51 — minimal-mono ghost button: bg 'transparent' esplicito (era '' che cadeva nel fallback CSS var primary = rosso del tema)
    'minimal-mono':    { bg_color: 'transparent', text_color: '#0f172a', border_radius: '0',   font_size: '14', font_weight: '500', text_transform: 'uppercase', shadow: 'none', hover_bg_color: '#0f172a', hover_text_color: '#fff', hover_effect: 'none', border_width: '2', border_color: '#0f172a' },
    'magazine-bold':   { bg_color: '#000',    text_color: '#fff',    border_radius: '0',   font_size: '16', font_weight: '800', text_transform: 'uppercase', shadow: 'none', hover_bg_color: '#fde047', hover_text_color: '#000', hover_effect: 'none' },
    'editorial-serif': { bg_color: '#1f2937', text_color: '#fff',    border_radius: '4',   font_size: '15', font_weight: '500', text_transform: 'none',      shadow: 'md',   hover_bg_color: '#0f172a', hover_effect: 'lift' },
    // v1.0.51 — compact-inline: aggiunto border + shadow sm (era bg #fff invisibile su sfondo bianco)
    'compact-inline':  { bg_color: '#fff',    text_color: '#0ea5e9', border_radius: '4',   font_size: '13', font_weight: '600', text_transform: 'none',      shadow: 'sm',   hover_bg_color: '#f0f9ff', hover_effect: 'none', border_width: '1', border_color: '#0ea5e9' },
    // v1.0.51 — glass-pill: bg da 0.15 → 0.55, text bianco→scuro, aggiunto border per visibilità su sfondo chiaro
    'glass-pill':      { bg_color: 'rgba(255,255,255,0.55)', text_color: '#0f172a', border_radius: '999', font_size: '15', font_weight: '600', text_transform: 'none', shadow: 'lg', hover_bg_color: 'rgba(255,255,255,0.75)', hover_effect: 'lift', border_width: '1', border_color: 'rgba(255,255,255,0.6)' },
    'neon-glow':       { bg_color: '#0a0a0a', text_color: '#00ffff', border_radius: '4',   font_size: '15', font_weight: '600', text_transform: 'uppercase', shadow: 'lg',   hover_bg_color: '#00ffff', hover_text_color: '#0a0a0a', hover_effect: 'scale' },
    'brutalist-stamp': { bg_color: '#fde047', text_color: '#000',    border_radius: '0',   font_size: '17', font_weight: '900', text_transform: 'uppercase', shadow: 'xl',   hover_bg_color: '#000',    hover_text_color: '#fde047', hover_effect: 'none' },
    'gradient-aurora': { bg_color: 'linear-gradient(135deg,#a855f7,#ec4899)', text_color: '#fff', border_radius: '999', font_size: '15', font_weight: '700', text_transform: 'none', shadow: 'lg', hover_bg_color: 'linear-gradient(135deg,#ec4899,#a855f7)', hover_effect: 'lift' },
    'sticker-fun':     { bg_color: '#fef3c7', text_color: '#78350f', border_radius: '12',  font_size: '15', font_weight: '700', text_transform: 'none',      shadow: 'md',   hover_bg_color: '#fbbf24', hover_text_color: '#fff', hover_effect: 'lift' },
    'retro-terminal':  { bg_color: '#0a0a0a', text_color: '#22c55e', border_radius: '0',   font_size: '14', font_weight: '600', text_transform: 'uppercase', shadow: 'none', hover_bg_color: '#22c55e', hover_text_color: '#0a0a0a', hover_effect: 'none' },
    'tilt-3d':         { bg_color: '#6366f1', text_color: '#fff',    border_radius: '8',   font_size: '15', font_weight: '700', text_transform: 'none',      shadow: 'xl',   hover_bg_color: '#312e81', hover_effect: 'scale' },
  },
  accordion: {
    'card-soft': {
      header_bg: '#ffffff', header_bg_active: '#fdf2ec', header_text_color: '#1e293b', header_text_color_active: '',
      header_padding_y: 16, header_padding_x: 20, header_font_size: 15, header_font_weight: '600', header_font_family: 'sans',
      content_bg: '#ffffff', content_padding_y: 20, content_padding_x: 20, content_font_size: 14,
      text_color: '#475569', border_color: '#e5e7eb', border_width: 1,
      gap: 12, border_radius: 12, icon_style: 'plus', icon_shape: 'none',
      separator_style: 'border', shadow: 'sm',
      backdrop_blur: 0, backdrop_saturate: 100,
      panel_hover_lift: false, panel_hover_shadow: 'none',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'minimal-underline': {
      header_bg: '', header_bg_active: '', header_text_color: '#0f172a', header_text_color_active: '#e8622a',
      header_padding_y: 22, header_padding_x: 0, header_font_size: 17, header_font_weight: '600', header_font_family: 'sans',
      content_bg: '', content_padding_y: 0, content_padding_x: 0, content_font_size: 15,
      text_color: '#475569', border_color: '#e5e7eb', border_width: 1,
      gap: 0, border_radius: 0, icon_style: 'plus', icon_shape: 'none',
      separator_style: 'border', shadow: 'none',
      backdrop_blur: 0, backdrop_saturate: 100,
      panel_hover_lift: false, panel_hover_shadow: 'none',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'pill-brand': {
      header_bg: '#ffffff', header_bg_active: '#e8622a', header_text_color: '#1e293b', header_text_color_active: '#ffffff',
      header_padding_y: 16, header_padding_x: 22, header_font_size: 15, header_font_weight: '600', header_font_family: 'sans',
      content_bg: '#ffffff', content_padding_y: 18, content_padding_x: 22, content_font_size: 14,
      text_color: '#475569', border_color: '', border_width: 0,
      gap: 8, border_radius: 14, icon_style: 'chevron', icon_shape: 'none',
      separator_style: 'shadow', shadow: 'sm',
      backdrop_blur: 0, backdrop_saturate: 100,
      panel_hover_lift: true, panel_hover_shadow: 'md',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'outline-sharp': {
      header_bg: '#ffffff', header_bg_active: '#fdf2ec', header_text_color: '#0f172a', header_text_color_active: '#0f172a',
      header_padding_y: 14, header_padding_x: 18, header_font_size: 14, header_font_weight: '600', header_font_family: 'mono',
      content_bg: '#ffffff', content_padding_y: 18, content_padding_x: 18, content_font_size: 13,
      text_color: '#475569', border_color: '#e8622a', border_width: 2,
      gap: 0, border_radius: 6, icon_style: 'plus', icon_shape: 'pill', icon_shape_size: 32, icon_shape_bg: '#fdf2ec',
      separator_style: 'border', shadow: 'none',
      backdrop_blur: 0, backdrop_saturate: 100,
      panel_hover_lift: false, panel_hover_shadow: 'none',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'glass-soft': {
      header_bg: 'rgba(255,255,255,0.55)', header_bg_active: 'rgba(255,255,255,0.75)', header_text_color: '#0f172a', header_text_color_active: '#0f172a',
      header_padding_y: 18, header_padding_x: 22, header_font_size: 15, header_font_weight: '600', header_font_family: 'sans',
      content_bg: 'rgba(255,255,255,0.85)', content_padding_y: 20, content_padding_x: 22, content_font_size: 14,
      text_color: '#475569', border_color: 'rgba(255,255,255,0.6)', border_width: 1,
      gap: 14, border_radius: 16, icon_style: 'plus', icon_shape: 'none',
      separator_style: 'border', shadow: 'lg',
      backdrop_blur: 12, backdrop_saturate: 160,
      panel_hover_lift: false, panel_hover_shadow: 'none',
      wow_disable: false, wow_backdrop_blur: 12, wow_backdrop_saturate: 160, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    // ─── Audaci ─────────────────────────────────────────────
    'liquid-glass': {
      header_bg: 'rgba(255,255,255,0.55)', header_bg_active: 'rgba(255,255,255,0.75)', header_text_color: '#0f172a', header_text_color_active: '#0f172a',
      header_padding_y: 18, header_padding_x: 22, header_font_size: 15, header_font_weight: '600', header_font_family: 'sans',
      content_bg: 'rgba(255,255,255,0.65)', content_padding_y: 20, content_padding_x: 22, content_font_size: 14,
      text_color: '#475569', border_color: 'rgba(255,255,255,0.6)', border_width: 1,
      gap: 12, border_radius: 18, icon_style: 'plus', icon_shape: 'none',
      separator_style: 'border', shadow: 'lg',
      backdrop_blur: 0, backdrop_saturate: 100,
      panel_hover_lift: false, panel_hover_shadow: 'none',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 20, wow_backdrop_saturate: 180, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'neon-cyber': {
      header_bg: '#0a0f1c', header_bg_active: '#0a0f1c', header_text_color: '#ff6a2a', header_text_color_active: '#ff6a2a',
      header_padding_y: 16, header_padding_x: 22, header_font_size: 14, header_font_weight: '700', header_font_family: 'sans',
      content_bg: '#0a0f1c', content_padding_y: 20, content_padding_x: 22, content_font_size: 14,
      text_color: 'rgba(255,255,255,0.85)', border_color: 'rgba(255,106,42,0.40)', border_width: 1,
      gap: 8, border_radius: 4, icon_style: 'plus', icon_shape: 'none',
      separator_style: 'border', shadow: 'none',
      backdrop_blur: 0, backdrop_saturate: 100,
      panel_hover_lift: false, panel_hover_shadow: 'none',
      effect_color: '#ff6a2a', effect_intensity: 'medium', effect_speed: 2200,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: true, wow_title_glow: true, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'brutalist-block': {
      header_bg: '#ffffff', header_bg_active: '#fafafa', header_text_color: '#000000', header_text_color_active: '#000000',
      header_padding_y: 18, header_padding_x: 22, header_font_size: 15, header_font_weight: '900', header_font_family: 'sans',
      content_bg: '#ffffff', content_padding_y: 20, content_padding_x: 22, content_font_size: 14,
      text_color: '#000000', border_color: '#000000', border_width: 3,
      gap: 12, border_radius: 0, icon_style: 'plus', icon_shape: 'none',
      separator_style: 'border', shadow: 'custom',
      shadow_h: 6, shadow_v: 6, shadow_blur: 0, shadow_spread: 0, shadow_color: '#000000', shadow_inset: false,
      backdrop_blur: 0, backdrop_saturate: 100,
      panel_hover_lift: false, panel_hover_shadow: 'none',
      effect_color: '#000000', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'magnetic-liquid': {
      header_bg: '#ffffff', header_bg_active: '#ffffff', header_text_color: '#0f172a', header_text_color_active: '#ffffff',
      header_padding_y: 18, header_padding_x: 22, header_font_size: 15, header_font_weight: '600', header_font_family: 'sans',
      content_bg: '#ffffff', content_padding_y: 20, content_padding_x: 22, content_font_size: 14,
      text_color: '#475569', border_color: '#e2e8f0', border_width: 1,
      gap: 10, border_radius: 22, icon_style: 'chevron', icon_shape: 'none',
      separator_style: 'border', shadow: 'sm',
      backdrop_blur: 0, backdrop_saturate: 100,
      panel_hover_lift: false, panel_hover_shadow: 'none',
      effect_color: '#e8622a', effect_intensity: 'medium', effect_speed: 500,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'sticker': {
      header_bg: '#ffffff', header_bg_active: '#fdf2ec', header_text_color: '#1e293b', header_text_color_active: '#b04217',
      header_padding_y: 16, header_padding_x: 20, header_font_size: 15, header_font_weight: '700', header_font_family: 'sans',
      content_bg: '#ffffff', content_padding_y: 20, content_padding_x: 20, content_font_size: 14,
      text_color: '#475569', border_color: 'rgba(232,98,42,0.55)', border_width: 2,
      gap: 14, border_radius: 8, icon_style: 'plus', icon_shape: 'none',
      separator_style: 'border', shadow: 'md',
      backdrop_blur: 0, backdrop_saturate: 100,
      panel_hover_lift: false, panel_hover_shadow: 'none',
      effect_color: '#e8622a', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'dashed', wow_font_family: 'inherit',
      wow_rotation: 0.8, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'retro-terminal': {
      header_bg: '#0c0c0c', header_bg_active: '#0c0c0c', header_text_color: '#00ff8c', header_text_color_active: '#00ff8c',
      header_padding_y: 16, header_padding_x: 20, header_font_size: 14, header_font_weight: '500', header_font_family: 'mono',
      content_bg: '#0c0c0c', content_padding_y: 20, content_padding_x: 20, content_font_size: 14,
      text_color: 'rgba(0,255,140,0.85)', border_color: 'rgba(0,255,140,0.40)', border_width: 1,
      gap: 8, border_radius: 0, icon_style: 'plus', icon_shape: 'none',
      separator_style: 'border', shadow: 'none',
      backdrop_blur: 0, backdrop_saturate: 100,
      panel_hover_lift: false, panel_hover_shadow: 'none',
      effect_color: '#00ff8c', effect_intensity: 'medium', effect_speed: 1000,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'monospace',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: true, wow_scanlines: true, wow_terminal_prompt: true,
    },
    '3d-tilt': {
      header_bg: '#ffffff', header_bg_active: '#ffffff', header_text_color: '#0f172a', header_text_color_active: '#0f172a',
      header_padding_y: 18, header_padding_x: 22, header_font_size: 15, header_font_weight: '600', header_font_family: 'sans',
      content_bg: '#ffffff', content_padding_y: 20, content_padding_x: 22, content_font_size: 14,
      text_color: '#475569', border_color: '#e2e8f0', border_width: 1,
      gap: 14, border_radius: 14, icon_style: 'chevron', icon_shape: 'none',
      separator_style: 'border', shadow: 'lg',
      backdrop_blur: 0, backdrop_saturate: 100,
      panel_hover_lift: false, panel_hover_shadow: 'none',
      effect_color: '', effect_intensity: 'medium', effect_speed: 500,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 1000, wow_tilt_x: -3,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
  },
  icontabs: {
    'pill-default':  { pill_bg: '#F5F2EB', active_bg: '#E8622A', active_color: '#FFFFFF', inactive_color: '#1A1A1A',
      card_bg: '#F9D7D7', card_radius: '16',
      heading_color: '#E8622A', title_color: '#1A1A1A', text_color: '#333333', link_color: '#2563EB',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'pill-subtle':   { pill_bg: '#f1f5f9', active_bg: '#ffffff', active_color: '#0f172a', inactive_color: '#64748b',
      card_bg: '#ffffff', card_radius: '12',
      heading_color: '#0f172a', title_color: '#0f172a', text_color: '#475569', link_color: '#e8622a',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'underline-bar': { pill_bg: 'transparent', active_bg: 'transparent', active_color: '#e8622a', inactive_color: '#64748b',
      card_bg: 'transparent', card_radius: '0',
      heading_color: '#e8622a', title_color: '#0f172a', text_color: '#475569', link_color: '#e8622a',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'brand-sharp':   { pill_bg: '#0f172a', active_bg: '#e8622a', active_color: '#ffffff', inactive_color: 'rgba(255,255,255,0.6)',
      card_bg: '#ffffff', card_radius: '4',
      heading_color: '#e8622a', title_color: '#0f172a', text_color: '#475569', link_color: '#e8622a',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'card-centered': { pill_bg: '#f8fafc', active_bg: '#0f172a', active_color: '#ffffff', inactive_color: '#1e293b',
      card_bg: '#ffffff', card_radius: '20',
      heading_color: '#1e293b', title_color: '#0f172a', text_color: '#475569', link_color: '#0f172a',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    // ─── Audaci ─────────────────────────────
    'liquid-glass':  { pill_bg: 'rgba(255,255,255,0.45)', active_bg: 'rgba(255,255,255,0.95)', active_color: '#0f172a', inactive_color: '#475569',
      card_bg: 'rgba(255,255,255,0.55)', card_radius: '20',
      heading_color: '#0f172a', title_color: '#0f172a', text_color: '#475569', link_color: '#e8622a',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 20, wow_backdrop_saturate: 180, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'neon-cyber':    { pill_bg: '#0a0f1c', active_bg: 'rgba(255,106,42,0.15)', active_color: '#ff6a2a', inactive_color: 'rgba(255,255,255,0.55)',
      card_bg: '#0a0f1c', card_radius: '4',
      heading_color: '#ff6a2a', title_color: '#ff6a2a', text_color: 'rgba(255,255,255,0.82)', link_color: '#ff6a2a',
      effect_color: '#ff6a2a', effect_intensity: 'medium', effect_speed: 2200,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: true, wow_title_glow: true, wow_scanlines: false, wow_terminal_prompt: false },
    'brutalist-block': { pill_bg: '#ffffff', active_bg: '#000000', active_color: '#ffffff', inactive_color: '#000000',
      card_bg: '#ffffff', card_radius: '0',
      heading_color: '#000000', title_color: '#000000', text_color: '#000000', link_color: '#000000',
      effect_color: '#000000', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'magnetic-liquid': { pill_bg: '#f8fafc', active_bg: '#e8622a', active_color: '#ffffff', inactive_color: '#475569',
      card_bg: '#ffffff', card_radius: '24',
      heading_color: '#b04217', title_color: '#0f172a', text_color: '#475569', link_color: '#e8622a',
      effect_color: '#e8622a', effect_intensity: 'medium', effect_speed: 500,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'sticker': { pill_bg: '#ffffff', active_bg: '#fdf2ec', active_color: '#b04217', inactive_color: '#1e293b',
      card_bg: '#ffffff', card_radius: '8',
      heading_color: '#b04217', title_color: '#0f172a', text_color: '#475569', link_color: '#e8622a',
      effect_color: '#e8622a', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'dashed', wow_font_family: 'inherit',
      wow_rotation: 0.8, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'retro-terminal': { pill_bg: '#0c0c0c', active_bg: 'rgba(0,255,140,0.12)', active_color: '#00ff8c', inactive_color: 'rgba(0,255,140,0.55)',
      card_bg: '#0c0c0c', card_radius: '0',
      heading_color: '#00ff8c', title_color: '#00ff8c', text_color: 'rgba(0,255,140,0.85)', link_color: '#00ff8c',
      effect_color: '#00ff8c', effect_intensity: 'medium', effect_speed: 1000,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'monospace',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: true, wow_scanlines: true, wow_terminal_prompt: true },
    '3d-tilt': { pill_bg: '#f8fafc', active_bg: '#ffffff', active_color: '#0f172a', inactive_color: '#64748b',
      card_bg: '#ffffff', card_radius: '14',
      heading_color: '#1e293b', title_color: '#0f172a', text_color: '#475569', link_color: '#e8622a',
      effect_color: '', effect_intensity: 'medium', effect_speed: 500,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 1000, wow_tilt_x: -3,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
  },
  finder: {
    // ⚠️ backward-compatible: ogni preset resetta `border` (oggetto zero) e tutti i wow_* per
    // evitare residui dal preset precedente. Chiavi storiche (zone_accent/zone_on/card_bg/
    // card_border) + nuove (chip_*/card_*/shadow). zone_accent '' = accento del tema.
    // ─── 5 sicuri ◆ ───
    'soft-card': { zone_accent: '', zone_on: '#ffffff', card_bg: '', card_border: '', chip_bg: '', chip_radius: '999',
      card_radius: '16', card_padding: { top: 34, right: 38, bottom: 34, left: 38 }, card_max_width: '680', shadow: 'sm',
      border: { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' },
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'minimal-line': { zone_accent: '', zone_on: '#ffffff', card_bg: 'transparent', card_border: 'transparent', chip_bg: 'transparent', chip_radius: '999',
      card_radius: '4', card_padding: { top: 16, right: 2, bottom: 16, left: 2 }, card_max_width: '680', shadow: 'none',
      border: { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' },
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'pill-solid': { zone_accent: '', zone_on: '#ffffff', card_bg: '', card_border: '', chip_bg: 'rgba(0,0,0,.05)', chip_radius: '999',
      card_radius: '14', card_padding: { top: 34, right: 38, bottom: 34, left: 38 }, card_max_width: '680', shadow: 'md',
      border: { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' },
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'editorial-serif': { zone_accent: '', zone_on: '#ffffff', card_bg: '', card_border: '', chip_bg: '', chip_radius: '4',
      card_radius: '2', card_padding: { top: 38, right: 42, bottom: 38, left: 42 }, card_max_width: '720', shadow: 'sm',
      border: { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' },
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'compact': { zone_accent: '', zone_on: '#ffffff', card_bg: '', card_border: '', chip_bg: '', chip_radius: '8',
      card_radius: '10', card_padding: { top: 20, right: 24, bottom: 20, left: 24 }, card_max_width: '560', shadow: 'sm',
      border: { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' },
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    // ─── 7 audaci ───
    'glass': { zone_accent: '', zone_on: '#0f172a', card_bg: 'rgba(255,255,255,.55)', card_border: 'rgba(255,255,255,.6)', chip_bg: 'rgba(255,255,255,.4)', chip_radius: '999',
      card_radius: '20', card_padding: { top: 34, right: 38, bottom: 34, left: 38 }, card_max_width: '680', shadow: 'lg',
      border: { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' },
      wow_disable: false, wow_backdrop_blur: 18, wow_backdrop_saturate: 160, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'neon': { zone_accent: '#ff6a2a', zone_on: '#0a0f1c', card_bg: '#0a0f1c', card_border: 'rgba(255,106,42,.35)', chip_bg: 'rgba(255,106,42,.12)', chip_radius: '6',
      card_radius: '4', card_padding: { top: 34, right: 38, bottom: 34, left: 38 }, card_max_width: '680', shadow: 'none', effect_color: '#ff6a2a', effect_speed: 2200,
      border: { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' },
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: true, wow_title_glow: true, wow_scanlines: false, wow_terminal_prompt: false },
    'brutalist': { zone_accent: '#000000', zone_on: '#ffffff', card_bg: '#ffffff', card_border: '', chip_bg: '#ffffff', chip_radius: '0',
      card_radius: '0', card_padding: { top: 30, right: 34, bottom: 30, left: 34 }, card_max_width: '680', shadow: 'none',
      border: { top: 3, right: 3, bottom: 3, left: 3, linked: true, style: 'solid', color: '#000000' },
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'gradient': { zone_accent: '#7c3aed', zone_on: '#ffffff', card_bg: '#faf5ff', card_border: '#e9d5ff', chip_bg: '#f3e8ff', chip_radius: '999',
      card_radius: '18', card_padding: { top: 34, right: 38, bottom: 34, left: 38 }, card_max_width: '680', shadow: 'md',
      border: { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' },
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'sticker': { zone_accent: '', zone_on: '#ffffff', card_bg: '#ffffff', card_border: '', chip_bg: '#ffffff', chip_radius: '8',
      card_radius: '14', card_padding: { top: 32, right: 36, bottom: 32, left: 36 }, card_max_width: '620', shadow: 'md',
      border: { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' },
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'dashed', wow_font_family: 'inherit', wow_rotation: -1.2, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'retro-terminal': { zone_accent: '#00ff8c', zone_on: '#0a0a0a', card_bg: '#0a120c', card_border: 'rgba(0,255,140,.3)', chip_bg: 'rgba(0,255,140,.1)', chip_radius: '2',
      card_radius: '2', card_padding: { top: 30, right: 34, bottom: 30, left: 34 }, card_max_width: '680', shadow: 'none', effect_color: '#00ff8c', effect_speed: 1000,
      border: { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' },
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'monospace', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: true, wow_scanlines: true, wow_terminal_prompt: true },
    'tilt-3d': { zone_accent: '', zone_on: '#ffffff', card_bg: '', card_border: '', chip_bg: '', chip_radius: '999',
      card_radius: '16', card_padding: { top: 34, right: 38, bottom: 34, left: 38 }, card_max_width: '680', shadow: 'lg',
      border: { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' },
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 1000, wow_tilt_x: -3, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
  },
  // Panel: schema dual { settings: {...} } — tutte le chiavi sono settings tile-specific
  // (style UIkit, card_radius, card_padding, shadow, ecc.), letti da PanelTile.vue via props.settings.
  // Lo schema flat legacy applicherebbe a tile.style (sbagliato per panel, preview builder non aggiornata).
  panel: {
    // v1.0.58 — aggiunti title_color/content_color/meta_color/link_color a tutti i preset (era "Scritto dall'autore" hardcoded ratio 2.85).
    // v1.0.73 — refactor profondo wow_*: i preset audaci settano direttamente i field standard, niente !important da PHP.
    'card-classic':    { settings: { style: 'default',   text_align: 'left',   card_radius: '12', shadow: 'sm',  card_padding: { top: 24, right: 24, bottom: 24, left: 24 }, image_zoom: false, title_color: '#0f172a', content_color: '#475569', meta_color: '#64748b', link_color: '#0ea5e9', effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false } },
    'magazine':        { settings: { style: 'default',   text_align: 'left',   card_radius: '0',  shadow: 'none', card_padding: { top: 0,  right: 0,  bottom: 24, left: 0 },  image_zoom: false, image_ratio: '4/3', title_weight: '800', title_color: '#000', content_color: '#1f2937', meta_color: '#374151', link_color: '#000', effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false } },
    'editorial':       { settings: { style: 'default',   text_align: 'left',   card_radius: '0',  shadow: 'none', card_padding: { top: 0,  right: 0,  bottom: 0,  left: 0 },  image_zoom: false, image_ratio: '16/9', title_weight: '500', title_color: '#1f2937', content_color: '#4b5563', meta_color: '#6b7280', link_color: '#1f2937', effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false } },
    'polaroid':        { settings: { style: 'default',   text_align: 'center', card_radius: '4',  shadow: 'md',  card_padding: { top: 12, right: 12, bottom: 24, left: 12 }, image_zoom: false, title_color: '#0f172a', content_color: '#475569', meta_color: '#64748b', link_color: '#0f172a', effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false } },
    'overlay-caption': { settings: { style: 'secondary', text_align: 'left',   card_radius: '12', shadow: 'lg',  card_padding: { top: 24, right: 24, bottom: 24, left: 24 }, image_zoom: true, image_ratio: '3/4', title_color: '#fff', content_color: '#e5e7eb', meta_color: '#cbd5e1', link_color: '#fff', effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false } },
    'liquid-glass':    { settings: { style: 'default', text_align: 'left',     card_radius: '18', shadow: 'lg',  card_padding: { top: 24, right: 24, bottom: 24, left: 24 }, image_zoom: false, bg_color: 'rgba(255,255,255,0.55)', border_color: 'rgba(255,255,255,0.55)', border_width: 1, title_color: '#0f172a', content_color: '#475569', meta_color: '#64748b', link_color: '#0f172a', effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 20, wow_backdrop_saturate: 180, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false } },
    'neon-cyber':      { settings: { style: 'default', text_align: 'left',     card_radius: '4',  shadow: 'none', card_padding: { top: 22, right: 22, bottom: 22, left: 22 }, image_zoom: false, bg_color: '#0a0f1c', border_color: 'rgba(255,106,42,0.45)', border_width: 1, title_color: '#ff6a2a', content_color: 'rgba(255,255,255,0.85)', meta_color: 'rgba(255,255,255,0.65)', link_color: '#ff6a2a', title_weight: '700', effect_color: '#ff6a2a', effect_intensity: 'medium', effect_speed: 2200,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: true, wow_title_glow: true, wow_scanlines: false, wow_terminal_prompt: false } },
    'brutalist-block': { settings: { style: 'default', text_align: 'left',     card_radius: '0',  shadow: 'custom', shadow_h: 8, shadow_v: 8, shadow_blur: 0, shadow_spread: 0, shadow_color: '#000000', shadow_inset: false, card_padding: { top: 24, right: 24, bottom: 24, left: 24 }, image_zoom: false, bg_color: '#ffffff', border_color: '#000000', border_width: 4, title_color: '#000000', content_color: '#000000', meta_color: '#374151', link_color: '#000', title_weight: '900', effect_color: '#000000', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false } },
    'magnetic-liquid': { settings: { style: 'default', text_align: 'left',     card_radius: '24', shadow: 'md',  card_padding: { top: 24, right: 24, bottom: 24, left: 24 }, image_zoom: true, bg_color: '#ffffff', title_color: '#0f172a', content_color: '#475569', meta_color: '#64748b', link_color: '#e8622a', title_weight: '700', effect_color: '#e8622a', effect_intensity: 'medium', effect_speed: 600,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false } },
    'sticker':         { settings: { style: 'default', text_align: 'left',     card_radius: '8',  shadow: 'lg',  card_padding: { top: 20, right: 20, bottom: 20, left: 20 }, image_zoom: false, bg_color: '#ffffff', border_color: 'rgba(232,98,42,0.55)', border_width: 2, title_color: '#0f172a', content_color: '#475569', meta_color: '#78350f', link_color: '#e8622a', title_weight: '700', effect_color: '#e8622a', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'dashed', wow_font_family: 'inherit', wow_rotation: -1, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false } },
    'retro-terminal':  { settings: { style: 'default', text_align: 'left',     card_radius: '0',  shadow: 'none', card_padding: { top: 20, right: 20, bottom: 20, left: 20 }, image_zoom: false, bg_color: '#0c0c0c', border_color: 'rgba(0,255,140,0.40)', border_width: 1, title_color: '#00ff8c', content_color: 'rgba(0,255,140,0.85)', meta_color: 'rgba(0,255,140,0.7)', link_color: '#00ff8c', title_weight: '500', effect_color: '#00ff8c', effect_intensity: 'medium', effect_speed: 1000,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'monospace', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: true, wow_scanlines: true, wow_terminal_prompt: true } },
    '3d-tilt':         { settings: { style: 'default', text_align: 'left',     card_radius: '14', shadow: 'lg',  card_padding: { top: 24, right: 24, bottom: 24, left: 24 }, image_zoom: false, bg_color: '#ffffff', title_color: '#0f172a', content_color: '#475569', meta_color: '#64748b', link_color: '#6366f1', title_weight: '700', effect_color: '', effect_intensity: 'medium', effect_speed: 600,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 1200, wow_tilt_x: -3, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false } },
  },
  switcher: {
    // v1.0.73 — refactor profondo wow_*: i preset audaci ora settano direttamente i field standard via TILE_PRESETS.
    'pill-slide': {
      tab_padding_y: 10, tab_padding_x: 18, tab_font_size: 14, tab_font_weight: '500',
      tab_gap: 4, tab_radius: 8,
      container_bg: '#f1f5f9', container_padding: 4, container_radius: 10,
      active_bg: '#ffffff', active_color: '#1e293b', inactive_color: '#64748b',
      hover_bg: 'rgba(0,0,0,0.03)',
      indicator_type: 'pill', indicator_color: '#e8622a',
      shadow: 'none',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'underline-animated': {
      tab_padding_y: 12, tab_padding_x: 20, tab_font_size: 15, tab_font_weight: '600',
      tab_gap: 8, tab_radius: 0,
      container_bg: '', container_padding: 0, container_radius: 0,
      active_bg: '', active_color: '#e8622a', inactive_color: '#64748b',
      hover_bg: '',
      indicator_type: 'underline', indicator_color: '#e8622a',
      shadow: 'none',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'card-tabs': {
      tab_padding_y: 10, tab_padding_x: 18, tab_font_size: 14, tab_font_weight: '500',
      tab_gap: 8, tab_radius: 8,
      container_bg: '', container_padding: 0, container_radius: 0,
      active_bg: '#fdf2ec', active_color: '#b04217', inactive_color: '#64748b',
      hover_bg: '#f8fafc',
      indicator_type: 'none', indicator_color: '#e8622a',
      shadow: 'sm',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'minimal-text': {
      tab_padding_y: 8, tab_padding_x: 14, tab_font_size: 14, tab_font_weight: '500',
      tab_gap: 4, tab_radius: 6,
      container_bg: '', container_padding: 0, container_radius: 0,
      active_bg: '#fdf2ec', active_color: '#b04217', inactive_color: '#64748b',
      hover_bg: 'rgba(0,0,0,0.03)',
      indicator_type: 'none', indicator_color: '#e8622a',
      shadow: 'none',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'vertical-sidebar': {
      tab_padding_y: 10, tab_padding_x: 14, tab_font_size: 14, tab_font_weight: '500',
      tab_gap: 2, tab_radius: 6,
      container_bg: '', container_padding: 0, container_radius: 0,
      active_bg: '#fdf2ec', active_color: '#b04217', inactive_color: '#64748b',
      hover_bg: 'rgba(0,0,0,0.03)',
      indicator_type: 'left-bar', indicator_color: '#e8622a',
      shadow: 'none', vertical: true,
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    // ─── Audaci ─────────────────────────────────────────────
    'liquid-glass': {
      tab_padding_y: 12, tab_padding_x: 22, tab_font_size: 14, tab_font_weight: '600',
      tab_gap: 6, tab_radius: 14,
      container_bg: 'rgba(255,255,255,0.45)', container_padding: 6, container_radius: 18,
      active_bg: 'rgba(255,255,255,0.95)', active_color: '#1e293b', inactive_color: '#475569',
      hover_bg: 'rgba(255,255,255,0.35)',
      indicator_type: 'pill', indicator_color: '#e8622a',
      shadow: 'lg',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 20, wow_backdrop_saturate: 180, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'neon-cyber': {
      tab_padding_y: 12, tab_padding_x: 24, tab_font_size: 13, tab_font_weight: '700',
      tab_gap: 8, tab_radius: 4,
      container_bg: '#0a0f1c', container_padding: 8, container_radius: 6,
      active_bg: 'rgba(232,98,42,0.15)', active_color: '#ff6a2a', inactive_color: 'rgba(255,255,255,0.4)',
      hover_bg: 'rgba(232,98,42,0.05)',
      indicator_type: 'none', indicator_color: '#ff6a2a',
      shadow: 'none',
      effect_color: '#ff6a2a', effect_intensity: 'medium', effect_speed: 2000,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: true, wow_title_glow: true, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'brutalist-block': {
      tab_padding_y: 14, tab_padding_x: 24, tab_font_size: 14, tab_font_weight: '700',
      tab_gap: 6, tab_radius: 0,
      container_bg: '', container_padding: 0, container_radius: 0,
      active_bg: '#e8622a', active_color: '#000000', inactive_color: '#000000',
      hover_bg: '#fef3ed',
      indicator_type: 'none', indicator_color: '#000000',
      shadow: 'custom', shadow_h: 4, shadow_v: 4, shadow_blur: 0, shadow_spread: 0, shadow_color: '#000000', shadow_inset: false,
      effect_color: '#000000', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'magnetic-liquid': {
      tab_padding_y: 11, tab_padding_x: 22, tab_font_size: 14, tab_font_weight: '500',
      tab_gap: 0, tab_radius: 999,
      container_bg: '#f8fafc', container_padding: 6, container_radius: 999,
      active_bg: '#e8622a', active_color: '#ffffff', inactive_color: '#475569',
      hover_bg: 'rgba(232,98,42,0.06)',
      indicator_type: 'pill', indicator_color: '#e8622a',
      shadow: 'sm',
      effect_color: '#e8622a', effect_intensity: 'medium', effect_speed: 450,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'sticker': {
      tab_padding_y: 10, tab_padding_x: 18, tab_font_size: 14, tab_font_weight: '600',
      tab_gap: 12, tab_radius: 6,
      container_bg: '', container_padding: 0, container_radius: 0,
      active_bg: '#fdf2ec', active_color: '#b04217', inactive_color: '#475569',
      hover_bg: '#f8fafc',
      indicator_type: 'none', indicator_color: '#e8622a',
      shadow: 'sm',
      effect_color: '#e8622a', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'dashed', wow_font_family: 'inherit', wow_rotation: -1.5, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'retro-terminal': {
      tab_padding_y: 10, tab_padding_x: 18, tab_font_size: 13, tab_font_weight: '500',
      tab_gap: 0, tab_radius: 0,
      container_bg: '#0c0c0c', container_padding: 4, container_radius: 0,
      active_bg: 'rgba(0,255,140,0.12)', active_color: '#00ff8c', inactive_color: 'rgba(0,255,140,0.45)',
      hover_bg: 'rgba(0,255,140,0.06)',
      indicator_type: 'none', indicator_color: '#00ff8c',
      shadow: 'none',
      effect_color: '#00ff8c', effect_intensity: 'medium', effect_speed: 1000,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'monospace', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: true, wow_scanlines: true, wow_terminal_prompt: true,
    },
    '3d-tilt': {
      tab_padding_y: 12, tab_padding_x: 22, tab_font_size: 14, tab_font_weight: '600',
      tab_gap: 8, tab_radius: 10,
      container_bg: '', container_padding: 0, container_radius: 0,
      active_bg: '#ffffff', active_color: '#1e293b', inactive_color: '#64748b',
      hover_bg: '#f8fafc',
      indicator_type: 'none', indicator_color: '#e8622a',
      shadow: 'md',
      effect_color: '', effect_intensity: 'medium', effect_speed: 400,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 800, wow_tilt_x: -3, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
  },
  switcherpanel: {
    // ─── Sicuri ─────────────────────────────────────────────
    'editorial-overlay': {
      nav_position: 'overlay',
      nav_padding_y: 12, nav_padding_x: 18, nav_font_size: 12, nav_font_weight: '700',
      nav_letter_spacing: 0.08, nav_uppercase: true, nav_gap: 0, nav_radius: 0,
      nav_container_bg: 'transparent', nav_container_padding: 0, nav_container_radius: 0,
      nav_active_bg: 'transparent', nav_active_color: '#ffffff', nav_inactive_color: 'rgba(255,255,255,0.65)',
      nav_hover_bg: 'transparent',
      nav_indicator_type: 'underline', nav_indicator_color: '#ffffff', nav_indicator_thickness: 2,
      hero_overlay_color: 'rgba(0,0,0,0.35)', hero_overlay_gradient: true, hero_radius: 0,
      panel_bg: '#ffffff', panel_text_color: '#475569', panel_title_color: '#0f172a',
      panel_title_size: 28, panel_title_weight: '700', panel_text_size: 15,
      panel_radius: 0, panel_image_radius: 0, panel_image_ratio: 'auto', panel_image_width: 40,
      shadow: 'none', button_style: 'primary',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'magazine-tabs': {
      nav_position: 'bottom',
      nav_padding_y: 14, nav_padding_x: 22, nav_font_size: 13, nav_font_weight: '600',
      nav_letter_spacing: 0.06, nav_uppercase: true, nav_gap: 4, nav_radius: 0,
      nav_container_bg: '#f8fafc', nav_container_padding: 4, nav_container_radius: 0,
      nav_active_bg: '#ffffff', nav_active_color: '#0f172a', nav_inactive_color: '#64748b',
      nav_hover_bg: '#ffffff',
      nav_indicator_type: 'overline', nav_indicator_color: '#e8622a', nav_indicator_thickness: 2,
      hero_overlay_color: 'transparent', hero_overlay_gradient: false, hero_radius: 0,
      panel_bg: '#ffffff', panel_text_color: '#475569', panel_title_color: '#0f172a',
      panel_title_size: 26, panel_title_weight: '600', panel_text_size: 15,
      panel_radius: 0, panel_image_radius: 0, panel_image_ratio: '4:3', panel_image_width: 45,
      shadow: 'none', button_style: 'underline',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'split-card': {
      nav_position: 'top',
      nav_padding_y: 10, nav_padding_x: 18, nav_font_size: 13, nav_font_weight: '600',
      nav_letter_spacing: 0.04, nav_uppercase: false, nav_gap: 4, nav_radius: 8,
      nav_container_bg: '#f1f5f9', nav_container_padding: 4, nav_container_radius: 10,
      nav_active_bg: '#ffffff', nav_active_color: '#0f172a', nav_inactive_color: '#64748b',
      nav_hover_bg: 'rgba(0,0,0,0.03)',
      nav_indicator_type: 'pill', nav_indicator_color: '#e8622a', nav_indicator_thickness: 2,
      hero_overlay_color: 'transparent', hero_overlay_gradient: false, hero_radius: 12,
      panel_bg: '#ffffff', panel_text_color: '#475569', panel_title_color: '#0f172a',
      panel_title_size: 24, panel_title_weight: '600', panel_text_size: 14,
      panel_radius: 12, panel_image_radius: 8, panel_image_ratio: 'auto', panel_image_width: 40,
      shadow: 'sm', button_style: 'pill',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'minimal-text': {
      nav_position: 'overlay',
      nav_padding_y: 10, nav_padding_x: 14, nav_font_size: 11, nav_font_weight: '500',
      nav_letter_spacing: 0.12, nav_uppercase: true, nav_gap: 8, nav_radius: 0,
      nav_container_bg: 'transparent', nav_container_padding: 0, nav_container_radius: 0,
      nav_active_bg: 'transparent', nav_active_color: '#ffffff', nav_inactive_color: 'rgba(255,255,255,0.55)',
      nav_hover_bg: 'transparent',
      nav_indicator_type: 'none', nav_indicator_color: '#ffffff', nav_indicator_thickness: 1,
      hero_overlay_color: 'rgba(0,0,0,0.45)', hero_overlay_gradient: true, hero_radius: 0,
      panel_bg: '#ffffff', panel_text_color: '#475569', panel_title_color: '#0f172a',
      panel_title_size: 30, panel_title_weight: '300', panel_text_size: 16,
      panel_radius: 0, panel_image_radius: 0, panel_image_ratio: '1:1', panel_image_width: 50,
      shadow: 'none', button_style: 'underline',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'sidebar-elegant': {
      nav_position: 'side-left',
      nav_padding_y: 12, nav_padding_x: 16, nav_font_size: 13, nav_font_weight: '600',
      nav_letter_spacing: 0.02, nav_uppercase: false, nav_gap: 4, nav_radius: 6,
      nav_container_bg: '#f8fafc', nav_container_padding: 8, nav_container_radius: 8,
      nav_active_bg: '#ffffff', nav_active_color: '#0f172a', nav_inactive_color: '#64748b',
      nav_hover_bg: 'rgba(0,0,0,0.03)',
      nav_indicator_type: 'left-bar', nav_indicator_color: '#e8622a', nav_indicator_thickness: 3,
      hero_overlay_color: 'transparent', hero_overlay_gradient: false, hero_radius: 8,
      panel_bg: '#ffffff', panel_text_color: '#475569', panel_title_color: '#0f172a',
      panel_title_size: 26, panel_title_weight: '600', panel_text_size: 14,
      panel_radius: 8, panel_image_radius: 6, panel_image_ratio: 'auto', panel_image_width: 40,
      shadow: 'sm', button_style: 'primary',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    // ─── Audaci ─────────────────────────────────────────────
    'liquid-glass': {
      nav_position: 'overlay',
      nav_padding_y: 12, nav_padding_x: 22, nav_font_size: 13, nav_font_weight: '600',
      nav_letter_spacing: 0.04, nav_uppercase: false, nav_gap: 6, nav_radius: 14,
      nav_container_bg: 'rgba(255,255,255,0.35)', nav_container_padding: 6, nav_container_radius: 18,
      nav_active_bg: 'rgba(255,255,255,0.95)', nav_active_color: '#0f172a', nav_inactive_color: '#ffffff',
      nav_hover_bg: 'rgba(255,255,255,0.25)',
      nav_indicator_type: 'pill', nav_indicator_color: '#ffffff', nav_indicator_thickness: 2,
      hero_overlay_color: 'rgba(232,98,42,0.20)', hero_overlay_gradient: true, hero_radius: 16,
      panel_bg: 'rgba(255,255,255,0.7)', panel_text_color: '#0f172a', panel_title_color: '#0f172a',
      panel_title_size: 28, panel_title_weight: '700', panel_text_size: 15,
      panel_radius: 16, panel_image_radius: 12, panel_image_ratio: 'auto', panel_image_width: 40,
      shadow: 'lg', button_style: 'pill',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 20, wow_backdrop_saturate: 180, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'neon-cyber': {
      nav_position: 'overlay',
      nav_padding_y: 12, nav_padding_x: 22, nav_font_size: 12, nav_font_weight: '700',
      nav_letter_spacing: 0.12, nav_uppercase: true, nav_gap: 6, nav_radius: 4,
      nav_container_bg: 'rgba(10,15,28,0.85)', nav_container_padding: 6, nav_container_radius: 6,
      nav_active_bg: 'rgba(255,106,42,0.15)', nav_active_color: '#ff6a2a', nav_inactive_color: 'rgba(255,255,255,0.55)',
      nav_hover_bg: 'rgba(255,106,42,0.05)',
      nav_indicator_type: 'none', nav_indicator_color: '#ff6a2a', nav_indicator_thickness: 1,
      hero_overlay_color: 'rgba(10,15,28,0.4)', hero_overlay_gradient: true, hero_radius: 0,
      panel_bg: '#0a0f1c', panel_text_color: 'rgba(255,255,255,0.85)', panel_title_color: '#ff6a2a',
      panel_title_size: 28, panel_title_weight: '700', panel_text_size: 15,
      panel_radius: 0, panel_image_radius: 0, panel_image_ratio: 'auto', panel_image_width: 40,
      shadow: 'none', button_style: 'underline',
      effect_color: '#ff6a2a', effect_intensity: 'medium', effect_speed: 2000,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: true, wow_title_glow: true, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'brutalist-block': {
      nav_position: 'top',
      nav_padding_y: 16, nav_padding_x: 24, nav_font_size: 14, nav_font_weight: '700',
      nav_letter_spacing: 0.04, nav_uppercase: true, nav_gap: 0, nav_radius: 0,
      nav_container_bg: '#ffffff', nav_container_padding: 0, nav_container_radius: 0,
      nav_active_bg: '#e8622a', nav_active_color: '#000000', nav_inactive_color: '#000000',
      nav_hover_bg: '#fef3ed',
      nav_indicator_type: 'none', nav_indicator_color: '#000000', nav_indicator_thickness: 3,
      hero_overlay_color: 'transparent', hero_overlay_gradient: false, hero_radius: 0,
      panel_bg: '#ffffff', panel_text_color: '#000000', panel_title_color: '#000000',
      panel_title_size: 32, panel_title_weight: '800', panel_text_size: 15,
      panel_radius: 0, panel_image_radius: 0, panel_image_ratio: 'auto', panel_image_width: 40,
      shadow: 'none', button_style: 'default',
      effect_color: '#000000', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'magnetic-liquid': {
      nav_position: 'overlay',
      nav_padding_y: 11, nav_padding_x: 22, nav_font_size: 13, nav_font_weight: '500',
      nav_letter_spacing: 0.02, nav_uppercase: false, nav_gap: 0, nav_radius: 999,
      nav_container_bg: 'rgba(255,255,255,0.85)', nav_container_padding: 6, nav_container_radius: 999,
      nav_active_bg: '#e8622a', nav_active_color: '#ffffff', nav_inactive_color: '#475569',
      nav_hover_bg: 'rgba(232,98,42,0.06)',
      nav_indicator_type: 'pill', nav_indicator_color: '#e8622a', nav_indicator_thickness: 2,
      hero_overlay_color: 'rgba(0,0,0,0.30)', hero_overlay_gradient: true, hero_radius: 24,
      panel_bg: '#ffffff', panel_text_color: '#475569', panel_title_color: '#0f172a',
      panel_title_size: 30, panel_title_weight: '700', panel_text_size: 15,
      panel_radius: 24, panel_image_radius: 20, panel_image_ratio: 'auto', panel_image_width: 42,
      shadow: 'md', button_style: 'pill',
      effect_color: '#e8622a', effect_intensity: 'medium', effect_speed: 450,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'sticker': {
      nav_position: 'overlay',
      nav_padding_y: 10, nav_padding_x: 18, nav_font_size: 12, nav_font_weight: '700',
      nav_letter_spacing: 0.04, nav_uppercase: false, nav_gap: 12, nav_radius: 6,
      nav_container_bg: 'transparent', nav_container_padding: 0, nav_container_radius: 0,
      nav_active_bg: '#fdf2ec', nav_active_color: '#b04217', nav_inactive_color: '#1e293b',
      nav_hover_bg: '#ffffff',
      nav_indicator_type: 'none', nav_indicator_color: '#e8622a', nav_indicator_thickness: 2,
      hero_overlay_color: 'rgba(255,250,245,0.20)', hero_overlay_gradient: false, hero_radius: 12,
      panel_bg: '#ffffff', panel_text_color: '#475569', panel_title_color: '#0f172a',
      panel_title_size: 28, panel_title_weight: '700', panel_text_size: 15,
      panel_radius: 8, panel_image_radius: 4, panel_image_ratio: 'auto', panel_image_width: 40,
      shadow: 'md', button_style: 'default',
      effect_color: '#e8622a', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'dashed', wow_font_family: 'inherit', wow_rotation: -1.5, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'retro-terminal': {
      nav_position: 'top',
      nav_padding_y: 10, nav_padding_x: 18, nav_font_size: 13, nav_font_weight: '500',
      nav_letter_spacing: 0.04, nav_uppercase: true, nav_gap: 0, nav_radius: 0,
      nav_container_bg: '#0c0c0c', nav_container_padding: 4, nav_container_radius: 0,
      nav_active_bg: 'rgba(0,255,140,0.12)', nav_active_color: '#00ff8c', nav_inactive_color: 'rgba(0,255,140,0.45)',
      nav_hover_bg: 'rgba(0,255,140,0.06)',
      nav_indicator_type: 'none', nav_indicator_color: '#00ff8c', nav_indicator_thickness: 1,
      hero_overlay_color: 'rgba(0,0,0,0.5)', hero_overlay_gradient: false, hero_radius: 0,
      panel_bg: '#0c0c0c', panel_text_color: '#00ff8c', panel_title_color: '#00ff8c',
      panel_title_size: 24, panel_title_weight: '500', panel_text_size: 14,
      panel_radius: 0, panel_image_radius: 0, panel_image_ratio: '16:9', panel_image_width: 45,
      shadow: 'none', button_style: 'default',
      effect_color: '#00ff8c', effect_intensity: 'medium', effect_speed: 1000,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'monospace', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: true, wow_scanlines: true, wow_terminal_prompt: true,
    },
    '3d-tilt': {
      nav_position: 'overlay',
      nav_padding_y: 12, nav_padding_x: 22, nav_font_size: 12, nav_font_weight: '600',
      nav_letter_spacing: 0.06, nav_uppercase: true, nav_gap: 8, nav_radius: 10,
      nav_container_bg: 'rgba(255,255,255,0.95)', nav_container_padding: 6, nav_container_radius: 12,
      nav_active_bg: '#ffffff', nav_active_color: '#0f172a', nav_inactive_color: '#64748b',
      nav_hover_bg: '#f8fafc',
      nav_indicator_type: 'none', nav_indicator_color: '#e8622a', nav_indicator_thickness: 2,
      hero_overlay_color: 'rgba(0,0,0,0.25)', hero_overlay_gradient: true, hero_radius: 16,
      panel_bg: '#ffffff', panel_text_color: '#475569', panel_title_color: '#0f172a',
      panel_title_size: 28, panel_title_weight: '700', panel_text_size: 15,
      panel_radius: 16, panel_image_radius: 12, panel_image_ratio: 'auto', panel_image_width: 42,
      shadow: 'lg', button_style: 'pill',
      effect_color: '', effect_intensity: 'medium', effect_speed: 400,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 1000, wow_tilt_x: -3, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
  },
  panelslider: {
    // ─── Sicuri ─────────────────────────────────────────────
    'card-modern': {
      gap: 'medium', card_radius: '12',
      card_padding: { top: 24, right: 24, bottom: 24, left: 24 },
      card_bg: '#ffffff', card_border_color: 'transparent', card_border_width: 0, card_border_style: 'solid',
      card_image_position: 'top', card_image_radius: 0,
      image_ratio: '4/3', image_fit: 'cover', image_zoom: true,
      title_weight: '700', title_letter_spacing: 0, title_uppercase: false, title_align: 'left',
      content_align: 'left', content_lines_clamp: 0,
      shadow: 'sm', hover_lift: true, hover_scale: false, hover_shadow: 'lg',
      show_cta: false, cta_style: 'underline',
      arrow_style: 'circle',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'editorial-magazine': {
      gap: 'large', card_radius: '0',
      card_padding: { top: 16, right: 0, bottom: 16, left: 0 },
      card_bg: 'transparent', card_border_color: 'transparent', card_border_width: 0,
      card_image_position: 'top', card_image_radius: 0,
      image_ratio: '4/3', image_fit: 'cover', image_zoom: false,
      title_weight: '600', title_letter_spacing: 0.02, title_uppercase: false, title_align: 'left',
      content_align: 'left', content_lines_clamp: 3,
      shadow: 'none', hover_lift: false, hover_scale: false, hover_shadow: 'none',
      show_cta: true, cta_style: 'underline', cta_text: 'Leggi tutto',
      arrow_style: 'minimal',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'polaroid': {
      gap: 'medium', card_radius: '4',
      card_padding: { top: 12, right: 12, bottom: 24, left: 12 },
      card_bg: '#ffffff', card_border_color: 'transparent', card_border_width: 0,
      card_image_position: 'top', card_image_radius: 0,
      image_ratio: '4/3', image_fit: 'cover', image_zoom: false,
      title_weight: '600', title_letter_spacing: 0, title_uppercase: false, title_align: 'center',
      content_align: 'center', content_lines_clamp: 2,
      shadow: 'md', hover_lift: true, hover_scale: false, hover_shadow: 'xl',
      show_cta: false, cta_style: 'underline',
      arrow_style: 'circle',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'overlay-caption': {
      gap: 'medium', card_radius: '12',
      card_padding: { top: 28, right: 28, bottom: 28, left: 28 },
      card_bg: '#1e293b', card_border_color: 'transparent', card_border_width: 0,
      card_image_position: 'bg', card_image_radius: 0,
      image_ratio: '3/4', image_fit: 'cover', image_zoom: true,
      caption_overlay_color: 'rgba(0,0,0,0.55)', caption_overlay_gradient: true,
      title_weight: '700', title_letter_spacing: 0.02, title_uppercase: false, title_align: 'left',
      content_align: 'left', content_lines_clamp: 2,
      shadow: 'lg', hover_lift: false, hover_scale: true, hover_shadow: 'xl',
      show_cta: true, cta_style: 'arrow', cta_text: 'Scopri',
      arrow_style: 'circle',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'minimal-clean': {
      gap: 'medium', card_radius: '0',
      card_padding: { top: 16, right: 0, bottom: 16, left: 0 },
      card_bg: 'transparent', card_border_color: '#e5e7eb', card_border_width: 0,
      card_image_position: 'top', card_image_radius: 6,
      image_ratio: '1/1', image_fit: 'cover', image_zoom: false,
      title_weight: '500', title_letter_spacing: 0, title_uppercase: false, title_align: 'left',
      content_align: 'left', content_lines_clamp: 2,
      shadow: 'none', hover_lift: false, hover_scale: false, hover_shadow: 'none',
      show_cta: false, cta_style: 'text',
      arrow_style: 'minimal',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    // ─── Audaci ─────────────────────────────────────────────
    'liquid-glass': {
      gap: 'medium', card_radius: '20',
      card_padding: { top: 24, right: 24, bottom: 24, left: 24 },
      card_bg: 'rgba(255,255,255,0.55)', card_border_color: 'rgba(255,255,255,0.45)', card_border_width: 1,
      card_image_position: 'top', card_image_radius: 14,
      image_ratio: '4/3', image_fit: 'cover', image_zoom: true,
      title_weight: '700', title_letter_spacing: 0, title_uppercase: false, title_align: 'left',
      title_color: '#0f172a', content_color: '#475569',
      content_align: 'left', content_lines_clamp: 0,
      shadow: 'lg', hover_lift: true, hover_scale: false, hover_shadow: 'xl',
      show_cta: true, cta_style: 'pill', cta_text: 'Esplora',
      arrow_style: 'fancy',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 20, wow_backdrop_saturate: 180, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'neon-cyber': {
      gap: 'medium', card_radius: '4',
      card_padding: { top: 22, right: 22, bottom: 22, left: 22 },
      card_bg: '#0a0f1c', card_border_color: 'rgba(255,106,42,0.35)', card_border_width: 1,
      card_image_position: 'top', card_image_radius: 0,
      image_ratio: '4/3', image_fit: 'cover', image_zoom: true,
      title_weight: '700', title_letter_spacing: 0.08, title_uppercase: true, title_align: 'left',
      title_color: '#ff6a2a', content_color: 'rgba(255,255,255,0.75)',
      content_align: 'left', content_lines_clamp: 2,
      shadow: 'none', hover_lift: false, hover_scale: false, hover_shadow: 'none',
      show_cta: true, cta_style: 'underline', cta_text: 'ACCESS',
      arrow_style: 'chevron-bold',
      effect_color: '#ff6a2a', effect_intensity: 'medium', effect_speed: 2000,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: true, wow_title_glow: true, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'brutalist-block': {
      gap: 'medium', card_radius: '0',
      card_padding: { top: 24, right: 24, bottom: 24, left: 24 },
      card_bg: '#ffffff', card_border_color: '#000000', card_border_width: 4, card_border_style: 'solid',
      card_image_position: 'top', card_image_radius: 0,
      image_ratio: '4/3', image_fit: 'cover', image_zoom: false,
      title_weight: '800', title_letter_spacing: -0.01, title_uppercase: true, title_align: 'left',
      title_color: '#000000', content_color: '#000000',
      content_align: 'left', content_lines_clamp: 0,
      shadow: 'none', hover_lift: false, hover_scale: false, hover_shadow: 'none',
      show_cta: true, cta_style: 'pill', cta_text: 'GO',
      arrow_style: 'square',
      effect_color: '#000000', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'magnetic-liquid': {
      gap: 'medium', card_radius: '24',
      card_padding: { top: 24, right: 24, bottom: 24, left: 24 },
      card_bg: '#ffffff', card_border_color: 'transparent', card_border_width: 0,
      card_image_position: 'top', card_image_radius: 18,
      image_ratio: '4/3', image_fit: 'cover', image_zoom: true,
      title_weight: '700', title_letter_spacing: 0, title_uppercase: false, title_align: 'left',
      content_align: 'left', content_lines_clamp: 2,
      shadow: 'sm', hover_lift: true, hover_scale: true, hover_shadow: 'xl',
      show_cta: true, cta_style: 'pill', cta_text: 'Scopri',
      arrow_style: 'fancy',
      effect_color: '#e8622a', effect_intensity: 'medium', effect_speed: 500,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'sticker': {
      gap: 'large', card_radius: '8',
      card_padding: { top: 18, right: 18, bottom: 18, left: 18 },
      card_bg: '#ffffff', card_border_color: 'rgba(232,98,42,0.5)', card_border_width: 2, card_border_style: 'dashed',
      card_image_position: 'top', card_image_radius: 4,
      image_ratio: '4/3', image_fit: 'cover', image_zoom: false,
      title_weight: '700', title_letter_spacing: 0, title_uppercase: false, title_align: 'left',
      content_align: 'left', content_lines_clamp: 2,
      shadow: 'md', hover_lift: false, hover_scale: false, hover_shadow: 'lg',
      show_cta: true, cta_style: 'pill', cta_text: 'Apri',
      arrow_style: 'circle',
      effect_color: '#e8622a', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'dashed', wow_font_family: 'inherit', wow_rotation: -1.5, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'retro-terminal': {
      gap: 'medium', card_radius: '0',
      card_padding: { top: 18, right: 18, bottom: 18, left: 18 },
      card_bg: '#0c0c0c', card_border_color: 'rgba(0,255,140,0.3)', card_border_width: 1,
      card_image_position: 'top', card_image_radius: 0,
      image_ratio: '16/9', image_fit: 'cover', image_zoom: false,
      title_weight: '500', title_letter_spacing: 0.04, title_uppercase: true, title_align: 'left',
      title_color: '#00ff8c', content_color: 'rgba(0,255,140,0.85)',
      content_align: 'left', content_lines_clamp: 2,
      shadow: 'none', hover_lift: false, hover_scale: false, hover_shadow: 'none',
      show_cta: true, cta_style: 'underline', cta_text: 'EXEC',
      arrow_style: 'minimal',
      effect_color: '#00ff8c', effect_intensity: 'medium', effect_speed: 1000,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'monospace', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: true, wow_scanlines: true, wow_terminal_prompt: true,
    },
    '3d-tilt': {
      gap: 'medium', card_radius: '14',
      card_padding: { top: 24, right: 24, bottom: 24, left: 24 },
      card_bg: '#ffffff', card_border_color: 'transparent', card_border_width: 0,
      card_image_position: 'top', card_image_radius: 10,
      image_ratio: '4/3', image_fit: 'cover', image_zoom: true,
      title_weight: '700', title_letter_spacing: 0, title_uppercase: false, title_align: 'left',
      content_align: 'left', content_lines_clamp: 2,
      shadow: 'lg', hover_lift: false, hover_scale: false, hover_shadow: 'xl',
      show_cta: true, cta_style: 'pill', cta_text: 'Scopri',
      arrow_style: 'circle',
      effect_color: '', effect_intensity: 'medium', effect_speed: 500,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 1000, wow_tilt_x: -3, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
  },
  overlayslider: {
    'cinematic-overlay': { image_ratio: '21/9', shadow: 'lg', hover_effect: 'zoom', hover_overlay: 'always',
      slide_radius: 0, overlay_color: 'rgba(0,0,0,0.45)', overlay_gradient: true,
      title_color: '#ffffff', title_weight: '700', title_letter_spacing: 0, title_uppercase: false,
      subtitle_color: 'rgba(255,255,255,0.85)', subtitle_size: 16,
      show_cta: false, cta_style: 'underline', cta_text: 'Scopri di più',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'minimal-caption': { image_ratio: '16/9', shadow: 'none', hover_effect: 'none', hover_overlay: 'always',
      slide_radius: 0, overlay_color: 'transparent', overlay_gradient: false,
      title_color: '#0f172a', title_weight: '500', title_letter_spacing: 0.04, title_uppercase: false,
      subtitle_color: '#64748b', subtitle_size: 14,
      show_cta: false, cta_style: 'underline',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'magazine-cover': { image_ratio: '4/3', shadow: 'md', hover_effect: 'zoom', hover_overlay: 'fade',
      slide_radius: 0, overlay_color: 'rgba(0,0,0,0.65)', overlay_gradient: true,
      title_color: '#ffffff', title_weight: '800', title_letter_spacing: 0.02, title_uppercase: true,
      subtitle_color: 'rgba(255,255,255,0.8)', subtitle_size: 14,
      show_cta: true, cta_style: 'underline', cta_text: 'Read more',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'split-frame': { image_ratio: '16/9', shadow: 'none', hover_effect: 'none', hover_overlay: 'always',
      slide_radius: 0, overlay_color: 'transparent', overlay_gradient: false,
      title_color: '#ffffff', title_weight: '700', title_letter_spacing: 0, title_uppercase: false,
      subtitle_color: 'rgba(255,255,255,0.85)', subtitle_size: 14,
      show_cta: false, cta_style: 'arrow',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'gradient-bottom': { image_ratio: '21/9', shadow: 'lg', hover_effect: 'zoom', hover_overlay: 'always',
      slide_radius: 16, overlay_color: 'rgba(0,0,0,0.55)', overlay_gradient: true,
      title_color: '#ffffff', title_weight: '700', title_letter_spacing: 0, title_uppercase: false,
      subtitle_color: 'rgba(255,255,255,0.85)', subtitle_size: 16,
      show_cta: true, cta_style: 'pill', cta_text: 'Scopri',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    // ─── Audaci ─────────────────────────────
    'liquid-glass': { image_ratio: '16/9', shadow: 'lg', hover_effect: 'zoom', hover_overlay: 'always',
      slide_radius: 24, overlay_color: 'rgba(255,255,255,0.20)', overlay_gradient: false,
      title_color: '#ffffff', title_weight: '600', title_letter_spacing: 0, title_uppercase: false,
      subtitle_color: 'rgba(255,255,255,0.85)', subtitle_size: 15,
      show_cta: true, cta_style: 'pill', cta_text: 'Esplora',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 20, wow_backdrop_saturate: 180, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'neon-cyber': { image_ratio: '21/9', shadow: 'none', hover_effect: 'zoom', hover_overlay: 'always',
      slide_radius: 0, overlay_color: 'rgba(10,15,28,0.85)', overlay_gradient: true,
      title_color: '#ff6a2a', title_weight: '700', title_letter_spacing: 0.08, title_uppercase: true,
      subtitle_color: 'rgba(255,255,255,0.85)', subtitle_size: 14,
      show_cta: true, cta_style: 'underline', cta_text: 'ACCESS',
      effect_color: '#ff6a2a', effect_intensity: 'medium', effect_speed: 2000,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: true, wow_title_glow: true, wow_scanlines: false, wow_terminal_prompt: false },
    'brutalist-block': { image_ratio: '4/3', shadow: 'custom', hover_effect: 'none', hover_overlay: 'always',
      slide_radius: 0, overlay_color: '#ffffff', overlay_gradient: false,
      title_color: '#000000', title_weight: '800', title_letter_spacing: -0.01, title_uppercase: true,
      subtitle_color: '#000000', subtitle_size: 14,
      show_cta: true, cta_style: 'pill', cta_text: 'GO',
      shadow_h: 6, shadow_v: 6, shadow_blur: 0, shadow_spread: 0, shadow_color: '#000000', shadow_inset: false,
      effect_color: '#000000', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'magnetic-liquid': { image_ratio: '16/9', shadow: 'md', hover_effect: 'zoom', hover_overlay: 'always',
      slide_radius: 28, overlay_color: 'rgba(0,0,0,0.40)', overlay_gradient: true,
      title_color: '#ffffff', title_weight: '700', title_letter_spacing: 0, title_uppercase: false,
      subtitle_color: 'rgba(255,255,255,0.85)', subtitle_size: 15,
      show_cta: true, cta_style: 'pill', cta_text: 'Scopri',
      effect_color: '#e8622a', effect_intensity: 'medium', effect_speed: 600,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'sticker': { image_ratio: '4/3', shadow: 'lg', hover_effect: 'none', hover_overlay: 'always',
      slide_radius: 6, overlay_color: 'rgba(232,98,42,0.85)', overlay_gradient: false,
      title_color: '#ffffff', title_weight: '700', title_letter_spacing: 0, title_uppercase: false,
      subtitle_color: 'rgba(255,255,255,0.9)', subtitle_size: 14,
      show_cta: true, cta_style: 'pill', cta_text: 'Apri',
      effect_color: '#e8622a', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'dashed', wow_font_family: 'inherit',
      wow_rotation: 0.8, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'retro-terminal': { image_ratio: '4/3', shadow: 'none', hover_effect: 'none', hover_overlay: 'always',
      slide_radius: 0, overlay_color: 'rgba(12,12,12,0.85)', overlay_gradient: false,
      title_color: '#00ff8c', title_weight: '500', title_letter_spacing: 0.04, title_uppercase: true,
      subtitle_color: 'rgba(0,255,140,0.85)', subtitle_size: 14,
      show_cta: true, cta_style: 'underline', cta_text: 'EXEC',
      effect_color: '#00ff8c', effect_intensity: 'medium', effect_speed: 1000,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'monospace',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: true, wow_scanlines: true, wow_terminal_prompt: true },
    '3d-tilt': { image_ratio: '16/9', shadow: 'lg', hover_effect: 'none', hover_overlay: 'always',
      slide_radius: 14, overlay_color: 'rgba(0,0,0,0.40)', overlay_gradient: true,
      title_color: '#ffffff', title_weight: '700', title_letter_spacing: 0, title_uppercase: false,
      subtitle_color: 'rgba(255,255,255,0.85)', subtitle_size: 15,
      show_cta: false, cta_style: 'underline',
      effect_color: '', effect_intensity: 'medium', effect_speed: 600,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 1000, wow_tilt_x: -3,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
  },
  overlaygrid: {
    'editorial-grid': { columns: '3', gap: 'medium', height: '320', match_height: true,
      hover_effect: 'zoom', hover_overlay: 'always', shadow: 'sm',
      item_radius: 12, overlay_color: 'rgba(0,0,0,0.45)', overlay_gradient: true,
      title_color: '#ffffff', title_weight: '700', title_letter_spacing: 0, title_uppercase: false,
      subtitle_color: 'rgba(255,255,255,0.85)', subtitle_size: 14,
      show_cta: false, cta_style: 'arrow',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'minimal-square': { columns: '3', gap: 'small', height: '300', match_height: true,
      hover_effect: 'desaturate', hover_overlay: 'fade', shadow: 'none',
      item_radius: 0, overlay_color: 'rgba(0,0,0,0.55)', overlay_gradient: false,
      title_color: '#ffffff', title_weight: '500', title_letter_spacing: 0.06, title_uppercase: true,
      subtitle_color: 'rgba(255,255,255,0.85)', subtitle_size: 12,
      show_cta: false, cta_style: 'underline',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'magazine-mosaic': { columns: '3', gap: 'medium', height: '380', match_height: false,
      hover_effect: 'zoom', hover_overlay: 'always', shadow: 'md',
      item_radius: 0, overlay_color: 'rgba(0,0,0,0.65)', overlay_gradient: true,
      title_color: '#ffffff', title_weight: '800', title_letter_spacing: 0.02, title_uppercase: true,
      subtitle_color: 'rgba(255,255,255,0.85)', subtitle_size: 14,
      show_cta: true, cta_style: 'underline', cta_text: 'Read more',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'card-modern': { columns: '3', gap: 'medium', height: '260', match_height: true,
      hover_effect: 'zoom', hover_overlay: 'always', shadow: 'sm',
      item_radius: 14, overlay_color: 'rgba(0,0,0,0.40)', overlay_gradient: true,
      title_color: '#ffffff', title_weight: '700', title_letter_spacing: 0, title_uppercase: false,
      subtitle_color: 'rgba(255,255,255,0.85)', subtitle_size: 14,
      show_cta: true, cta_style: 'arrow', cta_text: 'Scopri',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'duotone-portfolio': { columns: '3', gap: 'small', height: '320', match_height: true,
      hover_effect: 'none', hover_overlay: 'always', shadow: 'none',
      item_radius: 4, overlay_color: 'rgba(0,0,0,0.55)', overlay_gradient: true,
      title_color: '#ffffff', title_weight: '600', title_letter_spacing: 0.04, title_uppercase: true,
      subtitle_color: 'rgba(255,255,255,0.85)', subtitle_size: 13,
      show_cta: true, cta_style: 'arrow', cta_text: 'View',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    // ─── Audaci ─────────────────────────────
    'liquid-glass': { columns: '3', gap: 'medium', height: '320', match_height: true,
      hover_effect: 'zoom', hover_overlay: 'always', shadow: 'lg',
      item_radius: 18, overlay_color: 'rgba(255,255,255,0.20)', overlay_gradient: false,
      title_color: '#ffffff', title_weight: '600', title_letter_spacing: 0, title_uppercase: false,
      subtitle_color: 'rgba(255,255,255,0.85)', subtitle_size: 14,
      show_cta: true, cta_style: 'pill', cta_text: 'Esplora',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 16, wow_backdrop_saturate: 180, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'neon-cyber': { columns: '3', gap: 'medium', height: '320', match_height: true,
      hover_effect: 'zoom', hover_overlay: 'always', shadow: 'none',
      item_radius: 0, overlay_color: 'rgba(10,15,28,0.85)', overlay_gradient: true,
      title_color: '#ff6a2a', title_weight: '700', title_letter_spacing: 0.08, title_uppercase: true,
      subtitle_color: 'rgba(255,255,255,0.85)', subtitle_size: 13,
      show_cta: true, cta_style: 'underline', cta_text: 'ACCESS',
      effect_color: '#ff6a2a', effect_intensity: 'medium', effect_speed: 2200,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: true, wow_title_glow: true, wow_scanlines: false, wow_terminal_prompt: false },
    'brutalist-block': { columns: '3', gap: 'medium', height: '320', match_height: true,
      hover_effect: 'none', hover_overlay: 'always', shadow: 'custom',
      item_radius: 0, overlay_color: '#ffffff', overlay_gradient: false,
      title_color: '#000000', title_weight: '800', title_letter_spacing: -0.01, title_uppercase: true,
      subtitle_color: '#000000', subtitle_size: 13,
      show_cta: true, cta_style: 'pill', cta_text: 'GO',
      shadow_h: 6, shadow_v: 6, shadow_blur: 0, shadow_spread: 0, shadow_color: '#000000', shadow_inset: false,
      effect_color: '#000000', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'magnetic-liquid': { columns: '3', gap: 'medium', height: '320', match_height: true,
      hover_effect: 'zoom', hover_overlay: 'always', shadow: 'md',
      item_radius: 22, overlay_color: 'rgba(0,0,0,0.40)', overlay_gradient: true,
      title_color: '#ffffff', title_weight: '700', title_letter_spacing: 0, title_uppercase: false,
      subtitle_color: 'rgba(255,255,255,0.85)', subtitle_size: 14,
      show_cta: true, cta_style: 'pill', cta_text: 'Scopri',
      effect_color: '#e8622a', effect_intensity: 'medium', effect_speed: 600,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'sticker': { columns: '3', gap: 'large', height: '300', match_height: true,
      hover_effect: 'none', hover_overlay: 'always', shadow: 'lg',
      item_radius: 6, overlay_color: 'rgba(232,98,42,0.85)', overlay_gradient: false,
      title_color: '#ffffff', title_weight: '700', title_letter_spacing: 0, title_uppercase: false,
      subtitle_color: 'rgba(255,255,255,0.9)', subtitle_size: 13,
      show_cta: true, cta_style: 'pill', cta_text: 'Apri',
      effect_color: '#e8622a', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'dashed', wow_font_family: 'inherit',
      wow_rotation: 0.8, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'retro-terminal': { columns: '3', gap: 'medium', height: '300', match_height: true,
      hover_effect: 'none', hover_overlay: 'always', shadow: 'none',
      item_radius: 0, overlay_color: 'rgba(12,12,12,0.85)', overlay_gradient: false,
      title_color: '#00ff8c', title_weight: '500', title_letter_spacing: 0.04, title_uppercase: true,
      subtitle_color: 'rgba(0,255,140,0.85)', subtitle_size: 13,
      show_cta: true, cta_style: 'underline', cta_text: 'EXEC',
      effect_color: '#00ff8c', effect_intensity: 'medium', effect_speed: 1000,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'monospace',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: true, wow_scanlines: true, wow_terminal_prompt: true },
    '3d-tilt': { columns: '3', gap: 'medium', height: '320', match_height: true,
      hover_effect: 'none', hover_overlay: 'always', shadow: 'lg',
      item_radius: 14, overlay_color: 'rgba(0,0,0,0.40)', overlay_gradient: true,
      title_color: '#ffffff', title_weight: '700', title_letter_spacing: 0, title_uppercase: false,
      subtitle_color: 'rgba(255,255,255,0.85)', subtitle_size: 14,
      show_cta: false, cta_style: 'arrow',
      effect_color: '', effect_intensity: 'medium', effect_speed: 600,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 1000, wow_tilt_x: -3,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
  },
  timeline: {
    'classic-center': { layout: 'vertical-center', mobile_layout: 'vertical-left',
      line_color: '', line_width: '3', line_style: 'solid', line_progress: false,
      marker_type: 'dot', marker_size: '20', marker_color: '', marker_bg: '', marker_shape: 'circle', marker_pulse: false,
      card_bg: '', card_text_color: '', card_border_radius: '12', card_shadow: 'md', card_border_width: '0', card_hover: 'lift', card_arrow: true,
      title_size: '18', title_weight: '600', title_color: '', description_size: '14', description_color: '',
      date_position: 'outside', date_color: '', date_size: '14', date_weight: '600',
      animation: 'fade-up', stagger_delay: '150', animation_duration: '600',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'modern-cards': { layout: 'vertical-left', mobile_layout: 'vertical-left',
      line_color: '#e5e7eb', line_width: '2', line_style: 'solid', line_progress: false,
      marker_type: 'number', marker_size: '32', marker_color: '#ffffff', marker_bg: '#0f172a', marker_shape: 'circle', marker_pulse: false,
      card_bg: '#ffffff', card_text_color: '#475569', card_border_radius: '14', card_shadow: 'md', card_border_width: '0', card_hover: 'lift', card_arrow: false,
      title_size: '20', title_weight: '700', title_color: '#0f172a', description_size: '15', description_color: '#475569',
      date_position: 'inside', date_color: '#e8622a', date_size: '13', date_weight: '700',
      animation: 'fade-up', stagger_delay: '120', animation_duration: '500',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'minimal-line': { layout: 'vertical-left', mobile_layout: 'vertical-left',
      line_color: '#cbd5e1', line_width: '1', line_style: 'solid', line_progress: false,
      marker_type: 'dot', marker_size: '12', marker_color: '#1e293b', marker_bg: '#ffffff', marker_shape: 'circle', marker_pulse: false,
      card_bg: 'transparent', card_text_color: '#475569', card_border_radius: '0', card_shadow: 'none', card_border_width: '0', card_hover: 'none', card_arrow: false,
      title_size: '17', title_weight: '500', title_color: '#0f172a', description_size: '14', description_color: '#64748b',
      date_position: 'above', date_color: '#94a3b8', date_size: '12', date_weight: '600',
      animation: 'fade-in', stagger_delay: '100', animation_duration: '400',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'magazine-history': { layout: 'vertical-center', mobile_layout: 'vertical-left',
      line_color: '#0f172a', line_width: '2', line_style: 'solid', line_progress: true, line_progress_color: '#e8622a',
      marker_type: 'icon', marker_size: '36', marker_color: '#e8622a', marker_bg: '#ffffff', marker_shape: 'circle', marker_pulse: false,
      card_bg: '#fefefe', card_text_color: '#1e293b', card_border_radius: '0', card_shadow: 'sm', card_border_width: '1', card_border_color: '#e5e7eb', card_hover: 'glow', card_arrow: true,
      title_size: '22', title_weight: '800', title_color: '#0f172a', description_size: '15', description_color: '#475569',
      date_position: 'outside', date_color: '#e8622a', date_size: '16', date_weight: '700',
      animation: 'slide-left', stagger_delay: '180', animation_duration: '700',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'corporate-clean': { layout: 'vertical-right', mobile_layout: 'vertical-left',
      line_color: '#dbeafe', line_width: '4', line_style: 'solid', line_progress: false,
      marker_type: 'icon', marker_size: '28', marker_color: '#1e40af', marker_bg: '#ffffff', marker_shape: 'square', marker_pulse: false,
      card_bg: '#ffffff', card_text_color: '#1e293b', card_border_radius: '8', card_shadow: 'sm', card_border_width: '0', card_hover: 'lift', card_arrow: false,
      title_size: '18', title_weight: '600', title_color: '#1e3a8a', description_size: '14', description_color: '#475569',
      date_position: 'above', date_color: '#1e40af', date_size: '13', date_weight: '700',
      animation: 'slide-right', stagger_delay: '120', animation_duration: '500',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    // ─── Audaci ─────────────────────────────
    'liquid-glass': { layout: 'vertical-center', mobile_layout: 'vertical-left',
      line_color: 'rgba(255,255,255,0.4)', line_width: '2', line_style: 'solid', line_progress: false,
      marker_type: 'icon', marker_size: '28', marker_color: '#0f172a', marker_bg: 'rgba(255,255,255,0.7)', marker_shape: 'circle', marker_pulse: false,
      card_bg: 'rgba(255,255,255,0.55)', card_text_color: '#0f172a', card_border_radius: '16', card_shadow: 'lg', card_border_width: '1', card_border_color: 'rgba(255,255,255,0.5)', card_hover: 'glow', card_arrow: false,
      title_size: '19', title_weight: '600', title_color: '#0f172a', description_size: '14', description_color: '#475569',
      date_position: 'inside', date_color: '#e8622a', date_size: '13', date_weight: '700',
      animation: 'fade-up', stagger_delay: '150', animation_duration: '600',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 16, wow_backdrop_saturate: 180, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'neon-cyber': { layout: 'vertical-center', mobile_layout: 'vertical-left',
      line_color: '#ff6a2a', line_width: '2', line_style: 'solid', line_progress: false,
      marker_type: 'number', marker_size: '32', marker_color: '#ff6a2a', marker_bg: '#0a0f1c', marker_shape: 'circle', marker_pulse: true,
      card_bg: '#0a0f1c', card_text_color: 'rgba(255,255,255,0.85)', card_border_radius: '0', card_shadow: 'none', card_border_width: '1', card_border_color: 'rgba(255,106,42,0.45)', card_hover: 'glow', card_arrow: false,
      title_size: '18', title_weight: '700', title_color: '#ff6a2a', description_size: '14', description_color: 'rgba(255,255,255,0.78)',
      date_position: 'inside', date_color: '#ff6a2a', date_size: '13', date_weight: '700',
      animation: 'fade-in', stagger_delay: '120', animation_duration: '500',
      effect_color: '#ff6a2a', effect_intensity: 'medium', effect_speed: 2200,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: true, wow_title_glow: true, wow_scanlines: false, wow_terminal_prompt: false },
    'brutalist-block': { layout: 'vertical-left', mobile_layout: 'vertical-left',
      line_color: '#000000', line_width: '4', line_style: 'solid', line_progress: false,
      marker_type: 'number', marker_size: '36', marker_color: '#ffffff', marker_bg: '#000000', marker_shape: 'square', marker_pulse: false,
      card_bg: '#ffffff', card_text_color: '#000000', card_border_radius: '0', card_shadow: 'custom', card_border_width: '3', card_border_color: '#000000', card_hover: 'lift', card_arrow: false,
      title_size: '22', title_weight: '800', title_color: '#000000', description_size: '14', description_color: '#000000',
      date_position: 'inside', date_color: '#000000', date_size: '14', date_weight: '800',
      animation: 'slide-left', stagger_delay: '100', animation_duration: '400',
      shadow_h: 6, shadow_v: 6, shadow_blur: 0, shadow_spread: 0, shadow_color: '#000000', shadow_inset: false,
      effect_color: '#000000', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'magnetic-liquid': { layout: 'vertical-center', mobile_layout: 'vertical-left',
      line_color: '#e8622a', line_width: '3', line_style: 'solid', line_progress: false,
      marker_type: 'dot', marker_size: '24', marker_color: '#ffffff', marker_bg: '#e8622a', marker_shape: 'circle', marker_pulse: true,
      card_bg: '#ffffff', card_text_color: '#475569', card_border_radius: '20', card_shadow: 'md', card_border_width: '0', card_hover: 'lift', card_arrow: false,
      title_size: '19', title_weight: '700', title_color: '#0f172a', description_size: '14', description_color: '#475569',
      date_position: 'above', date_color: '#e8622a', date_size: '13', date_weight: '700',
      animation: 'zoom-in', stagger_delay: '180', animation_duration: '700',
      effect_color: '#e8622a', effect_intensity: 'medium', effect_speed: 600,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'sticker': { layout: 'vertical-center', mobile_layout: 'vertical-left',
      line_color: 'rgba(232,98,42,0.4)', line_width: '2', line_style: 'dashed', line_progress: false,
      marker_type: 'icon', marker_size: '34', marker_color: '#ffffff', marker_bg: '#e8622a', marker_shape: 'circle', marker_pulse: false,
      card_bg: '#ffffff', card_text_color: '#475569', card_border_radius: '8', card_shadow: 'md', card_border_width: '2', card_border_color: 'rgba(232,98,42,0.55)', card_hover: 'lift', card_arrow: false,
      title_size: '19', title_weight: '700', title_color: '#0f172a', description_size: '14', description_color: '#475569',
      date_position: 'above', date_color: '#b04217', date_size: '14', date_weight: '700',
      animation: 'fade-up', stagger_delay: '150', animation_duration: '550',
      effect_color: '#e8622a', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'dashed', wow_font_family: 'inherit',
      wow_rotation: 1.0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'retro-terminal': { layout: 'vertical-left', mobile_layout: 'vertical-left',
      line_color: '#00ff8c', line_width: '1', line_style: 'solid', line_progress: false,
      marker_type: 'number', marker_size: '28', marker_color: '#00ff8c', marker_bg: '#0c0c0c', marker_shape: 'square', marker_pulse: false,
      card_bg: '#0c0c0c', card_text_color: 'rgba(0,255,140,0.85)', card_border_radius: '0', card_shadow: 'none', card_border_width: '1', card_border_color: 'rgba(0,255,140,0.4)', card_hover: 'glow', card_arrow: false,
      title_size: '17', title_weight: '500', title_color: '#00ff8c', description_size: '14', description_color: 'rgba(0,255,140,0.85)',
      date_position: 'inside', date_color: '#00ff8c', date_size: '13', date_weight: '600',
      animation: 'slide-left', stagger_delay: '100', animation_duration: '400',
      effect_color: '#00ff8c', effect_intensity: 'medium', effect_speed: 1000,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'monospace',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: true, wow_scanlines: true, wow_terminal_prompt: true },
    '3d-tilt': { layout: 'vertical-center', mobile_layout: 'vertical-left',
      line_color: '#cbd5e1', line_width: '2', line_style: 'solid', line_progress: false,
      marker_type: 'dot', marker_size: '24', marker_color: '#ffffff', marker_bg: '#1e293b', marker_shape: 'circle', marker_pulse: false,
      card_bg: '#ffffff', card_text_color: '#475569', card_border_radius: '14', card_shadow: 'lg', card_border_width: '0', card_hover: 'none', card_arrow: false,
      title_size: '19', title_weight: '700', title_color: '#0f172a', description_size: '14', description_color: '#475569',
      date_position: 'above', date_color: '#1e293b', date_size: '13', date_weight: '700',
      animation: 'zoom-in', stagger_delay: '180', animation_duration: '700',
      effect_color: '', effect_intensity: 'medium', effect_speed: 600,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 1000, wow_tilt_x: -3,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
  },
  popup: {
    'modal-classic': { button_style: 'primary', button_size: 'large', button_radius: 6, button_uppercase: false, button_letter_spacing: 0.02, button_weight: '600',
      modal_size: '', modal_shadow: 'lg', modal_overlay: '60', modal_radius: '12', modal_border_width: '0', modal_border_style: 'solid',
      modal_bg: '#ffffff', modal_text_color: '#1e293b', modal_title_color: '#0f172a', modal_title_size: 24, modal_title_weight: '700', modal_title_uppercase: false, modal_title_letter_spacing: 0,
      modal_backdrop_blur: 0, modal_backdrop_saturate: 100, modal_font_family: 'inherit',
      modal_rotation: 0, modal_perspective: 0, modal_tilt_x: 0,
      modal_glow_pulse: false, modal_title_glow: false, modal_scanlines: false, modal_terminal_prompt: false,
      popup_animation: 'fade', popup_overlay_blur: 0,
      effect_color: '', effect_intensity: 'medium', effect_speed: 0 },
    'newsletter-promo': { button_style: 'primary', button_size: 'large', button_radius: 999, button_uppercase: false, button_letter_spacing: 0.04, button_weight: '600',
      modal_size: '', modal_shadow: 'xl', modal_overlay: '70', modal_radius: '20', modal_border_width: '0', modal_border_style: 'solid',
      modal_bg: '#ffffff', modal_text_color: '#475569', modal_title_color: '#0f172a', modal_title_size: 28, modal_title_weight: '700', modal_title_uppercase: false, modal_title_letter_spacing: 0,
      modal_backdrop_blur: 0, modal_backdrop_saturate: 100, modal_font_family: 'inherit',
      modal_rotation: 0, modal_perspective: 0, modal_tilt_x: 0,
      modal_glow_pulse: false, modal_title_glow: false, modal_scanlines: false, modal_terminal_prompt: false,
      popup_animation: 'zoom', popup_overlay_blur: 4,
      effect_color: '', effect_intensity: 'medium', effect_speed: 0 },
    'sale-banner': { button_style: 'danger', button_size: 'large', button_radius: 0, button_uppercase: true, button_letter_spacing: 0.08, button_weight: '800',
      modal_size: '', modal_shadow: 'lg', modal_overlay: '80', modal_radius: '0', modal_border_width: '0', modal_border_style: 'solid',
      modal_bg: '#fee2e2', modal_text_color: '#991b1b', modal_title_color: '#7f1d1d', modal_title_size: 36, modal_title_weight: '900', modal_title_uppercase: true, modal_title_letter_spacing: -0.01,
      modal_backdrop_blur: 0, modal_backdrop_saturate: 100, modal_font_family: 'inherit',
      modal_rotation: 0, modal_perspective: 0, modal_tilt_x: 0,
      modal_glow_pulse: false, modal_title_glow: false, modal_scanlines: false, modal_terminal_prompt: false,
      popup_animation: 'slide-down', popup_overlay_blur: 0,
      effect_color: '', effect_intensity: 'medium', effect_speed: 0 },
    'editorial-card': { button_style: 'default', button_size: '', button_radius: 0, button_uppercase: true, button_letter_spacing: 0.10, button_weight: '600',
      modal_size: '', modal_shadow: 'md', modal_overlay: '50', modal_radius: '0', modal_border_width: '1', modal_border_color: '#e5e7eb', modal_border_style: 'solid',
      modal_bg: '#fefefe', modal_text_color: '#1e293b', modal_title_color: '#0f172a', modal_title_size: 32, modal_title_weight: '800', modal_title_uppercase: false, modal_title_letter_spacing: -0.01,
      modal_backdrop_blur: 0, modal_backdrop_saturate: 100, modal_font_family: 'inherit',
      modal_rotation: 0, modal_perspective: 0, modal_tilt_x: 0,
      modal_glow_pulse: false, modal_title_glow: false, modal_scanlines: false, modal_terminal_prompt: false,
      popup_animation: 'fade', popup_overlay_blur: 0,
      effect_color: '', effect_intensity: 'medium', effect_speed: 0 },
    'compact-dialog': { button_style: 'default', button_size: 'small', button_radius: 6, button_uppercase: false, button_letter_spacing: 0, button_weight: '500',
      modal_size: '', modal_shadow: 'sm', modal_overlay: '40', modal_radius: '8', modal_border_width: '1', modal_border_color: '#e5e7eb', modal_border_style: 'solid',
      modal_bg: '#ffffff', modal_text_color: '#475569', modal_title_color: '#0f172a', modal_title_size: 18, modal_title_weight: '600', modal_title_uppercase: false, modal_title_letter_spacing: 0,
      modal_backdrop_blur: 0, modal_backdrop_saturate: 100, modal_font_family: 'inherit',
      modal_rotation: 0, modal_perspective: 0, modal_tilt_x: 0,
      modal_glow_pulse: false, modal_title_glow: false, modal_scanlines: false, modal_terminal_prompt: false,
      popup_animation: 'fade', popup_overlay_blur: 0,
      effect_color: '', effect_intensity: 'medium', effect_speed: 0 },
    'liquid-glass': { button_style: 'default', button_size: 'large', button_radius: 14, button_uppercase: false, button_letter_spacing: 0, button_weight: '600',
      modal_size: '', modal_shadow: 'lg', modal_overlay: '40', modal_radius: '24', modal_border_width: '1', modal_border_color: 'rgba(255,255,255,0.5)', modal_border_style: 'solid',
      modal_bg: 'rgba(255,255,255,0.55)', modal_text_color: '#0f172a', modal_title_color: '#0f172a', modal_title_size: 26, modal_title_weight: '600', modal_title_uppercase: false, modal_title_letter_spacing: 0,
      modal_backdrop_blur: 24, modal_backdrop_saturate: 180, modal_font_family: 'inherit',
      modal_rotation: 0, modal_perspective: 0, modal_tilt_x: 0,
      modal_glow_pulse: false, modal_title_glow: false, modal_scanlines: false, modal_terminal_prompt: false,
      popup_animation: 'zoom', popup_overlay_blur: 12,
      effect_color: '', effect_intensity: 'medium', effect_speed: 0 },
    'neon-cyber': { button_style: 'default', button_size: 'large', button_radius: 4, button_uppercase: true, button_letter_spacing: 0.08, button_weight: '700',
      modal_size: '', modal_shadow: 'none', modal_overlay: '85', modal_radius: '4', modal_border_width: '2', modal_border_color: '#ff6a2a', modal_border_style: 'solid',
      modal_bg: '#0a0f1c', modal_text_color: 'rgba(255,255,255,0.85)', modal_title_color: '#ff6a2a', modal_title_size: 28, modal_title_weight: '700', modal_title_uppercase: true, modal_title_letter_spacing: 0.08,
      modal_backdrop_blur: 0, modal_backdrop_saturate: 100, modal_font_family: 'inherit',
      modal_rotation: 0, modal_perspective: 0, modal_tilt_x: 0,
      modal_glow_pulse: true, modal_title_glow: true, modal_scanlines: false, modal_terminal_prompt: false,
      popup_animation: 'flip', popup_overlay_blur: 4,
      effect_color: '#ff6a2a', effect_intensity: 'medium', effect_speed: 2200 },
    'brutalist-block': { button_style: 'default', button_size: 'large', button_radius: 0, button_uppercase: true, button_letter_spacing: 0.04, button_weight: '800',
      modal_size: '', modal_shadow: 'custom', modal_shadow_h: 10, modal_shadow_v: 10, modal_shadow_blur: 0, modal_shadow_spread: 0, modal_shadow_color: '#000000', modal_shadow_inset: false,
      modal_overlay: '70', modal_radius: '0', modal_border_width: '4', modal_border_color: '#000000', modal_border_style: 'solid',
      modal_bg: '#ffffff', modal_text_color: '#000000', modal_title_color: '#000000', modal_title_size: 32, modal_title_weight: '900', modal_title_uppercase: true, modal_title_letter_spacing: -0.01,
      modal_backdrop_blur: 0, modal_backdrop_saturate: 100, modal_font_family: 'inherit',
      modal_rotation: 0, modal_perspective: 0, modal_tilt_x: 0,
      modal_glow_pulse: false, modal_title_glow: false, modal_scanlines: false, modal_terminal_prompt: false,
      popup_animation: 'slide-up', popup_overlay_blur: 0,
      effect_color: '#000000', effect_intensity: 'medium', effect_speed: 0 },
    'magnetic-liquid': { button_style: 'primary', button_size: 'large', button_radius: 999, button_uppercase: false, button_letter_spacing: 0.02, button_weight: '600',
      modal_size: '', modal_shadow: 'xl', modal_overlay: '50', modal_radius: '28', modal_border_width: '0', modal_border_style: 'solid',
      modal_bg: '#ffffff', modal_text_color: '#475569', modal_title_color: '#0f172a', modal_title_size: 28, modal_title_weight: '700', modal_title_uppercase: false, modal_title_letter_spacing: 0,
      modal_backdrop_blur: 0, modal_backdrop_saturate: 100, modal_font_family: 'inherit',
      modal_rotation: 0, modal_perspective: 0, modal_tilt_x: 0,
      modal_glow_pulse: false, modal_title_glow: false, modal_scanlines: false, modal_terminal_prompt: false,
      popup_animation: 'zoom', popup_overlay_blur: 6,
      effect_color: '#e8622a', effect_intensity: 'medium', effect_speed: 600 },
    'sticker': { button_style: 'default', button_size: '', button_radius: 8, button_uppercase: false, button_letter_spacing: 0, button_weight: '700',
      modal_size: '', modal_shadow: 'lg', modal_overlay: '60', modal_radius: '8', modal_border_width: '3', modal_border_color: 'rgba(232,98,42,0.55)', modal_border_style: 'dashed',
      modal_bg: '#ffffff', modal_text_color: '#475569', modal_title_color: '#b04217', modal_title_size: 26, modal_title_weight: '700', modal_title_uppercase: false, modal_title_letter_spacing: 0,
      modal_backdrop_blur: 0, modal_backdrop_saturate: 100, modal_font_family: 'inherit',
      modal_rotation: -1.2, modal_perspective: 0, modal_tilt_x: 0,
      modal_glow_pulse: false, modal_title_glow: false, modal_scanlines: false, modal_terminal_prompt: false,
      popup_animation: 'zoom', popup_overlay_blur: 0,
      effect_color: '#e8622a', effect_intensity: 'medium', effect_speed: 0 },
    'retro-terminal': { button_style: 'default', button_size: 'large', button_radius: 0, button_uppercase: true, button_letter_spacing: 0.05, button_weight: '500',
      modal_size: '', modal_shadow: 'none', modal_overlay: '85', modal_radius: '0', modal_border_width: '1', modal_border_color: 'rgba(0,255,140,0.45)', modal_border_style: 'solid',
      modal_bg: '#0c0c0c', modal_text_color: 'rgba(0,255,140,0.85)', modal_title_color: '#00ff8c', modal_title_size: 22, modal_title_weight: '500', modal_title_uppercase: true, modal_title_letter_spacing: 0.05,
      modal_backdrop_blur: 0, modal_backdrop_saturate: 100, modal_font_family: 'monospace',
      modal_rotation: 0, modal_perspective: 0, modal_tilt_x: 0,
      modal_glow_pulse: false, modal_title_glow: true, modal_scanlines: true, modal_terminal_prompt: true,
      popup_animation: 'fade', popup_overlay_blur: 0,
      effect_color: '#00ff8c', effect_intensity: 'medium', effect_speed: 1000 },
    '3d-tilt': { button_style: 'primary', button_size: 'large', button_radius: 12, button_uppercase: false, button_letter_spacing: 0, button_weight: '600',
      modal_size: '', modal_shadow: 'xl', modal_overlay: '55', modal_radius: '14', modal_border_width: '0', modal_border_style: 'solid',
      modal_bg: '#ffffff', modal_text_color: '#475569', modal_title_color: '#0f172a', modal_title_size: 26, modal_title_weight: '700', modal_title_uppercase: false, modal_title_letter_spacing: 0,
      modal_backdrop_blur: 0, modal_backdrop_saturate: 100, modal_font_family: 'inherit',
      modal_rotation: 0, modal_perspective: 1200, modal_tilt_x: -3,
      modal_glow_pulse: false, modal_title_glow: false, modal_scanlines: false, modal_terminal_prompt: false,
      popup_animation: 'flip', popup_overlay_blur: 4,
      effect_color: '', effect_intensity: 'medium', effect_speed: 600 },
  },
  postmeta: {
    'editorial-classic': {
      layout: 'inline', separator: ' · ', icon_style: 'none', item_gap: 0,
      text_color: '#475569', link_color: '#0f172a', icon_color: '',
      bg_color: '', font_size: 14, font_family: 'serif', font_weight: '400',
      text_transform: 'none', letter_spacing: 0,
      chip_style: 'none', chip_bg: '', chip_padding_x: 0, chip_padding_y: 0, chip_radius: 0,
      container_padding: { top: 0, right: 0, bottom: 0, left: 0 },
      container_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'magazine-bold': {
      layout: 'inline', separator: ' | ', icon_style: 'none', item_gap: 0,
      text_color: '#0f172a', link_color: '#e8622a', icon_color: '',
      bg_color: '', font_size: 12, font_family: 'sans', font_weight: '700',
      text_transform: 'uppercase', letter_spacing: 1.5,
      chip_style: 'none', chip_bg: '', chip_padding_x: 0, chip_padding_y: 0, chip_radius: 0,
      container_padding: { top: 0, right: 0, bottom: 0, left: 0 },
      container_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'minimal-mono': {
      layout: 'inline', separator: ' / ', icon_style: 'none', item_gap: 0,
      text_color: '#94a3b8', link_color: '#0f172a', icon_color: '',
      bg_color: '', font_size: 12, font_family: 'mono', font_weight: '400',
      text_transform: 'lowercase', letter_spacing: 0,
      chip_style: 'none', chip_bg: '', chip_padding_x: 0, chip_padding_y: 0, chip_radius: 0,
      container_padding: { top: 0, right: 0, bottom: 0, left: 0 },
      container_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'tag-pills': {
      layout: 'inline', separator: '', icon_style: 'before', item_gap: 8,
      text_color: '#475569', link_color: '#0f172a', icon_color: '#94a3b8',
      bg_color: '', font_size: 13, font_family: 'sans', font_weight: '500',
      text_transform: 'none', letter_spacing: 0,
      chip_style: 'pill', chip_bg: '#f1f5f9', chip_padding_x: 12, chip_padding_y: 6, chip_radius: 999,
      container_padding: { top: 0, right: 0, bottom: 0, left: 0 },
      container_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'underline-animated': {
      layout: 'inline', separator: ' · ', icon_style: 'none', item_gap: 0,
      text_color: '#475569', link_color: '#e8622a', icon_color: '',
      bg_color: '', font_size: 14, font_family: 'sans', font_weight: '500',
      text_transform: 'none', letter_spacing: 0,
      chip_style: 'none', chip_bg: '', chip_padding_x: 0, chip_padding_y: 0, chip_radius: 0,
      container_padding: { top: 0, right: 0, bottom: 0, left: 0 },
      container_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'glass-floating': {
      layout: 'inline', separator: '', icon_style: 'before', item_gap: 6,
      text_color: '#0f172a', link_color: '#e8622a', icon_color: '#475569',
      bg_color: '', font_size: 13, font_family: 'sans', font_weight: '500',
      text_transform: 'none', letter_spacing: 0,
      chip_style: 'pill', chip_bg: 'rgba(255,255,255,0.55)', chip_padding_x: 14, chip_padding_y: 7, chip_radius: 999,
      container_padding: { top: 0, right: 0, bottom: 0, left: 0 },
      container_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
      effect_color: '#e8622a', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 20, wow_backdrop_saturate: 180, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'neon-cyber': {
      layout: 'inline', separator: '', icon_style: 'before', item_gap: 12,
      text_color: '#94a3b8', link_color: '#ff6a2a', icon_color: '#ff6a2a',
      bg_color: '#0a0f1c', font_size: 12, font_family: 'mono', font_weight: '600',
      text_transform: 'uppercase', letter_spacing: 2,
      chip_style: 'none', chip_bg: '', chip_padding_x: 0, chip_padding_y: 0, chip_radius: 0,
      container_padding: { top: 12, right: 18, bottom: 12, left: 18 },
      container_radius: { tl: 4, tr: 4, br: 4, bl: 4 },
      effect_color: '#ff6a2a', effect_intensity: 'high', effect_speed: 2000,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: true, wow_title_glow: true, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'brutalist-stamp': {
      layout: 'inline', separator: '', icon_style: 'before', item_gap: 10,
      text_color: '#000000', link_color: '#000000', icon_color: '#000000',
      bg_color: '#fef9c3', font_size: 13, font_family: 'mono', font_weight: '700',
      text_transform: 'uppercase', letter_spacing: 1,
      chip_style: 'tag', chip_bg: '#ffffff', chip_padding_x: 10, chip_padding_y: 5, chip_radius: 0,
      container_padding: { top: 10, right: 14, bottom: 10, left: 14 },
      container_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
      effect_color: '#000000', effect_intensity: 'high', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'gradient-glow': {
      layout: 'inline', separator: ' · ', icon_style: 'none', item_gap: 0,
      text_color: '#0f172a', link_color: '#e8622a', icon_color: '',
      bg_color: '', font_size: 16, font_family: 'sans', font_weight: '700',
      text_transform: 'none', letter_spacing: 0.2,
      chip_style: 'none', chip_bg: '', chip_padding_x: 0, chip_padding_y: 0, chip_radius: 0,
      container_padding: { top: 0, right: 0, bottom: 0, left: 0 },
      container_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
      effect_color: '#e8622a', effect_intensity: 'medium', effect_speed: 4000,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'sticker-scrap': {
      layout: 'inline', separator: '', icon_style: 'before', item_gap: 14,
      text_color: '#0f172a', link_color: '#0f172a', icon_color: '#0f172a',
      bg_color: '', font_size: 13, font_family: 'sans', font_weight: '600',
      text_transform: 'none', letter_spacing: 0,
      chip_style: 'sticker', chip_bg: '#fef3c7', chip_padding_x: 12, chip_padding_y: 6, chip_radius: 6,
      container_padding: { top: 8, right: 0, bottom: 8, left: 0 },
      container_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
      effect_color: '#e8622a', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'dashed', wow_font_family: 'inherit',
      wow_rotation: 0.8, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'retro-terminal': {
      layout: 'inline', separator: '', icon_style: 'none', item_gap: 14,
      text_color: '#00ff8c', link_color: '#7cf3b9', icon_color: '#00ff8c',
      bg_color: '#0c0c0c', font_size: 13, font_family: 'mono', font_weight: '400',
      text_transform: 'lowercase', letter_spacing: 0.5,
      chip_style: 'none', chip_bg: '', chip_padding_x: 0, chip_padding_y: 0, chip_radius: 0,
      container_padding: { top: 12, right: 16, bottom: 12, left: 16 },
      container_radius: { tl: 4, tr: 4, br: 4, bl: 4 },
      effect_color: '#00ff8c', effect_intensity: 'medium', effect_speed: 1000,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'monospace',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: true, wow_scanlines: true, wow_terminal_prompt: true,
    },
    'tilt-3d': {
      layout: 'inline', separator: '', icon_style: 'before', item_gap: 12,
      text_color: '#0f172a', link_color: '#e8622a', icon_color: '#475569',
      bg_color: '', font_size: 13, font_family: 'sans', font_weight: '600',
      text_transform: 'none', letter_spacing: 0,
      chip_style: 'chip-3d', chip_bg: '#ffffff', chip_padding_x: 14, chip_padding_y: 7, chip_radius: 8,
      container_padding: { top: 8, right: 0, bottom: 16, left: 0 },
      container_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 1000, wow_tilt_x: -3,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
  },
  sitemap: {
    'classic-columns': {
      layout_mode: 'columns', columns: '2', list_style: 'disc', indent: 20, gap: 24, item_gap: 6,
      title_color: '#1e293b', link_color: '#2563eb', hover_color: '#1d4ed8', text_color: '#64748b', bg_color: '', accent_color: '',
      font_family: 'inherit', font_weight: '400', text_transform: 'none', letter_spacing: 0,
      show_counter: false, show_icons: false, show_date: false, show_excerpt: false, show_thumb: false,
      container_padding: { top: 16, right: 16, bottom: 16, left: 16 }, container_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'card-grid': {
      layout_mode: 'cards', columns: '3', list_style: 'none', indent: 0, gap: 20, item_gap: 4,
      title_color: '#0f172a', link_color: '#0f172a', hover_color: '#e8622a', text_color: '#64748b', bg_color: '#f8fafc', accent_color: '#e8622a',
      font_family: 'sans', font_weight: '500', text_transform: 'none', letter_spacing: 0,
      show_counter: true, show_icons: true, show_date: false, show_excerpt: false, show_thumb: false,
      container_padding: { top: 20, right: 20, bottom: 20, left: 20 }, container_radius: { tl: 12, tr: 12, br: 12, bl: 12 },
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'index-az': {
      layout_mode: 'index-az', columns: '1', list_style: 'none', indent: 0, gap: 16, item_gap: 4,
      title_color: '#0f172a', link_color: '#475569', hover_color: '#e8622a', text_color: '#94a3b8', bg_color: '', accent_color: '#e8622a',
      font_family: 'serif', font_weight: '400', text_transform: 'none', letter_spacing: 0,
      show_counter: true, show_icons: false, show_date: false, show_excerpt: false, show_thumb: false,
      container_padding: { top: 20, right: 20, bottom: 20, left: 20 }, container_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'tag-cloud': {
      layout_mode: 'cloud', columns: '1', list_style: 'none', indent: 0, gap: 24, item_gap: 8,
      title_color: '#0f172a', link_color: '#475569', hover_color: '#e8622a', text_color: '#94a3b8', bg_color: '', accent_color: '#e8622a',
      font_family: 'sans', font_weight: '500', text_transform: 'none', letter_spacing: 0,
      show_counter: false, show_icons: false, show_date: false, show_excerpt: false, show_thumb: false,
      container_padding: { top: 24, right: 24, bottom: 24, left: 24 }, container_radius: { tl: 8, tr: 8, br: 8, bl: 8 },
      show_categories: true, show_tags: true, show_pages: false, show_posts: false,
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'compact-chips': {
      layout_mode: 'compact', columns: '2', list_style: 'none', indent: 0, gap: 20, item_gap: 4,
      title_color: '#0f172a', link_color: '#0f172a', hover_color: '#fff', text_color: '#475569', bg_color: '', accent_color: '#0f172a',
      font_family: 'sans', font_weight: '500', text_transform: 'none', letter_spacing: 0,
      show_counter: true, show_icons: false, show_date: false, show_excerpt: false, show_thumb: false,
      container_padding: { top: 16, right: 16, bottom: 16, left: 16 }, container_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'glass-cards': {
      layout_mode: 'cards', columns: '3', list_style: 'none', indent: 0, gap: 20, item_gap: 4,
      title_color: '#0f172a', link_color: '#0f172a', hover_color: '#e8622a', text_color: '#475569', bg_color: 'linear-gradient(135deg,#fef3c7 0%,#fce7f3 100%)', accent_color: '#e8622a',
      font_family: 'sans', font_weight: '500', text_transform: 'none', letter_spacing: 0,
      show_counter: true, show_icons: true, show_date: false, show_excerpt: false, show_thumb: false,
      container_padding: { top: 24, right: 24, bottom: 24, left: 24 }, container_radius: { tl: 16, tr: 16, br: 16, bl: 16 },
      effect_color: '#e8622a', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 20, wow_backdrop_saturate: 180, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'neon-schematic': {
      layout_mode: 'columns', columns: '3', list_style: 'none', indent: 0, gap: 24, item_gap: 6,
      title_color: '#22d3ee', link_color: '#fff', hover_color: '#22d3ee', text_color: '#94a3b8', bg_color: '#0a0f1c', accent_color: '#22d3ee',
      font_family: 'mono', font_weight: '600', text_transform: 'uppercase', letter_spacing: 1,
      show_counter: true, show_icons: true, show_date: false, show_excerpt: false, show_thumb: false,
      container_padding: { top: 24, right: 24, bottom: 24, left: 24 }, container_radius: { tl: 4, tr: 4, br: 4, bl: 4 },
      effect_color: '#22d3ee', effect_intensity: 'high', effect_speed: 2400,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'mono',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: true, wow_title_glow: true, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'brutalist-map': {
      layout_mode: 'cards', columns: '2', list_style: 'none', indent: 0, gap: 20, item_gap: 4,
      title_color: '#000000', link_color: '#000000', hover_color: '#000000', text_color: '#000000', bg_color: '#fef9c3', accent_color: '#000000',
      font_family: 'mono', font_weight: '700', text_transform: 'uppercase', letter_spacing: 1,
      show_counter: true, show_icons: true, show_date: false, show_excerpt: false, show_thumb: false,
      container_padding: { top: 20, right: 20, bottom: 20, left: 20 }, container_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
      effect_color: '#000000', effect_intensity: 'high', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'mono',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'mind-map': {
      layout_mode: 'mindmap', columns: '1', list_style: 'none', indent: 0, gap: 0, item_gap: 0,
      title_color: '#e8622a', link_color: '#475569', hover_color: '#e8622a', text_color: '#94a3b8', bg_color: '#fafaf9', accent_color: '#e8622a',
      font_family: 'sans', font_weight: '500', text_transform: 'none', letter_spacing: 0,
      show_counter: false, show_icons: false, show_date: false, show_excerpt: false, show_thumb: false,
      container_padding: { top: 40, right: 40, bottom: 40, left: 40 }, container_radius: { tl: 12, tr: 12, br: 12, bl: 12 },
      effect_color: '#e8622a', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'sticky-notes': {
      layout_mode: 'cards', columns: '3', list_style: 'none', indent: 0, gap: 28, item_gap: 4,
      title_color: '#0f172a', link_color: '#0f172a', hover_color: '#0f172a', text_color: '#475569', bg_color: '#fafaf9', accent_color: '#0f172a',
      font_family: 'inherit', font_weight: '500', text_transform: 'none', letter_spacing: 0,
      show_counter: true, show_icons: false, show_date: false, show_excerpt: false, show_thumb: false,
      container_padding: { top: 30, right: 30, bottom: 30, left: 30 }, container_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
      effect_color: '#e8622a', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'dashed', wow_font_family: 'inherit',
      wow_rotation: 0.8, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'retro-terminal': {
      layout_mode: 'columns', columns: '2', list_style: 'none', indent: 0, gap: 20, item_gap: 2,
      title_color: '#00ff8c', link_color: '#7cf3b9', hover_color: '#fff', text_color: '#00ff8c', bg_color: '#0c0c0c', accent_color: '#00ff8c',
      font_family: 'mono', font_weight: '400', text_transform: 'lowercase', letter_spacing: 0.5,
      show_counter: true, show_icons: false, show_date: false, show_excerpt: false, show_thumb: false,
      container_padding: { top: 20, right: 20, bottom: 20, left: 20 }, container_radius: { tl: 4, tr: 4, br: 4, bl: 4 },
      effect_color: '#00ff8c', effect_intensity: 'medium', effect_speed: 1000,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'monospace',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: true, wow_scanlines: true, wow_terminal_prompt: true,
    },
    'honeycomb': {
      layout_mode: 'columns', columns: '4', list_style: 'none', indent: 0, gap: 10, item_gap: 0,
      // v1.0.57 — bg_color esplicito scuro: text bianco serve sfondo scuro, era bg='' che cadeva nel fallback primary del tema
      title_color: '#fff', link_color: '#fff', hover_color: '#fff', text_color: '#fff', bg_color: '#0f172a', accent_color: '#fff',
      font_family: 'sans', font_weight: '700', text_transform: 'uppercase', letter_spacing: 1,
      show_counter: false, show_icons: false, show_date: false, show_excerpt: false, show_thumb: false,
      container_padding: { top: 20, right: 20, bottom: 20, left: 20 }, container_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit',
      wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0,
      wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
  },
  portfolio: {
    // v1.0.73 — refactor profondo wow_*: i preset audaci ora settano direttamente i field standard via TILE_PRESETS.
    'editorial-magazine': {
      layout: 'magazine', columns: 3, gap: 16, image_ratio: '4:3', hover_effect: 'fade', caption_position: 'below',
      filter_bar: true, filter_style: 'underline', filter_color: '#94a3b8', filter_active_color: '#0f172a',
      title_color: '#0f172a', text_color: '#475569', bg_color: '', accent_color: '#e8622a', overlay_color: '#000', overlay_opacity: 70,
      font_family: 'serif', font_weight: '600', text_transform: 'none', letter_spacing: 0,
      show_title: true, show_category: true, show_excerpt: false,
      stagger_entrance: true, dim_others: false, featured_ribbon: true, year_stamp: false, index_numbering: false,
      grayscale_default: false, cursor_label_enabled: false, enable_search: false,
      border_radius: 6,
      container_padding: { top: 0, right: 0, bottom: 0, left: 0 }, container_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
      effect_color: '#e8622a', effect_intensity: 'medium',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'serif', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'bento-asymmetric': {
      layout: 'bento', columns: 4, gap: 12, image_ratio: 'auto', hover_effect: 'tilt-3d', caption_position: 'overlay',
      filter_bar: true, filter_style: 'pills', filter_color: '#64748b', filter_active_color: '#0f172a',
      title_color: '#fff', text_color: '#cbd5e1', bg_color: '', accent_color: '#fbbf24', overlay_color: '#000', overlay_opacity: 50,
      font_family: 'sans', font_weight: '600', text_transform: 'none', letter_spacing: 0,
      show_title: true, show_category: true, show_excerpt: false,
      stagger_entrance: true, dim_others: true, featured_ribbon: false, year_stamp: false, index_numbering: false,
      grayscale_default: false, cursor_label_enabled: false,
      border_radius: 20,
      container_padding: { top: 0, right: 0, bottom: 0, left: 0 },
      effect_color: '', effect_intensity: 'medium',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'pinterest-masonry': {
      layout: 'masonry-pin', columns: 3, gap: 14, image_ratio: 'auto', hover_effect: 'fade', caption_position: 'below',
      filter_bar: true, filter_style: 'pills', filter_color: '#64748b', filter_active_color: '#e8622a',
      title_color: '#0f172a', text_color: '#64748b', bg_color: '', accent_color: '#e8622a', overlay_color: '#000', overlay_opacity: 60,
      font_family: 'sans', font_weight: '500', text_transform: 'none', letter_spacing: 0,
      show_title: true, show_category: false, show_excerpt: false,
      stagger_entrance: true, dim_others: false, featured_ribbon: false, year_stamp: false, index_numbering: false,
      grayscale_default: false, enable_search: true,
      border_radius: 14,
      container_padding: { top: 0, right: 0, bottom: 0, left: 0 },
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'cinema-showcase': {
      layout: 'grid', columns: 1, gap: 24, image_ratio: '16:9', hover_effect: 'reveal-mask', caption_position: 'always',
      filter_bar: false,
      title_color: '#fff', text_color: '#cbd5e1', bg_color: '#0a0a0a', accent_color: '#fbbf24', overlay_color: '#000', overlay_opacity: 60,
      font_family: 'serif', font_weight: '700', text_transform: 'none', letter_spacing: 0,
      show_title: true, show_category: true, show_excerpt: true,
      stagger_entrance: true, dim_others: false, featured_ribbon: false, year_stamp: true, index_numbering: true,
      grayscale_default: false, cursor_label_enabled: true, cursor_label_text: 'Apri',
      border_radius: 0,
      container_padding: { top: 24, right: 24, bottom: 24, left: 24 }, container_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
      effect_color: '#fbbf24', effect_intensity: 'high',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'serif', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'studio-index': {
      layout: 'split-index', columns: 2, gap: 0, image_ratio: 'auto', hover_effect: 'none', caption_position: 'below',
      filter_bar: false,
      title_color: '#0f172a', text_color: '#64748b', bg_color: '#fafaf9', accent_color: '#0f172a', overlay_color: '#000', overlay_opacity: 0,
      font_family: 'serif', font_weight: '400', text_transform: 'none', letter_spacing: 0,
      show_title: true, show_category: true, show_excerpt: false,
      stagger_entrance: false, dim_others: false, featured_ribbon: false, year_stamp: false, index_numbering: true,
      grayscale_default: false, cursor_label_enabled: true, cursor_label_text: 'Vedi',
      border_radius: 0,
      container_padding: { top: 32, right: 32, bottom: 32, left: 32 },
      effect_color: '#0f172a',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'serif', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'polaroid-wall': {
      layout: 'polaroid', columns: 4, gap: 24, image_ratio: '1:1', hover_effect: 'none', caption_position: 'below',
      filter_bar: false,
      title_color: '#0f172a', text_color: '#64748b', bg_color: '#f5f5f4', accent_color: '#0f172a', overlay_color: '#000', overlay_opacity: 0,
      font_family: 'inherit', font_weight: '400', text_transform: 'none', letter_spacing: 0,
      show_title: true, show_category: false, show_excerpt: false,
      stagger_entrance: true, dim_others: false, featured_ribbon: false, year_stamp: false, index_numbering: false,
      grayscale_default: false,
      border_radius: 0,
      container_padding: { top: 24, right: 24, bottom: 24, left: 24 },
      effect_intensity: 'medium',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'postcard-stack': {
      layout: 'postcard-stack', columns: 3, gap: 30, image_ratio: '4:3', hover_effect: 'tilt-3d', caption_position: 'below',
      filter_bar: false,
      title_color: '#0f172a', text_color: '#64748b', bg_color: '', accent_color: '#e8622a', overlay_color: '#000', overlay_opacity: 50,
      font_family: 'sans', font_weight: '500', text_transform: 'none', letter_spacing: 0,
      show_title: true, show_category: true, show_excerpt: false,
      stagger_entrance: true, dim_others: false, featured_ribbon: false,
      border_radius: 8,
      container_padding: { top: 30, right: 30, bottom: 30, left: 30 },
      effect_intensity: 'medium',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'dribbble-cards': {
      layout: 'grid', columns: 3, gap: 16, image_ratio: '4:3', hover_effect: 'fade', caption_position: 'below',
      filter_bar: true, filter_style: 'pills', filter_color: '#64748b', filter_active_color: '#ea4c89',
      title_color: '#0f172a', text_color: '#64748b', bg_color: '#f8fafc', accent_color: '#ea4c89', overlay_color: '#000', overlay_opacity: 60,
      font_family: 'sans', font_weight: '600', text_transform: 'none', letter_spacing: 0,
      show_title: true, show_category: true, show_excerpt: false,
      stagger_entrance: true, dim_others: false, featured_ribbon: false,
      border_radius: 16,
      container_padding: { top: 16, right: 16, bottom: 16, left: 16 }, container_radius: { tl: 12, tr: 12, br: 12, bl: 12 },
      effect_color: '#ea4c89',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'awwwards-hero': {
      layout: 'grid', columns: 2, gap: 32, image_ratio: '4:3', hover_effect: 'tilt-3d', caption_position: 'below',
      filter_bar: false,
      title_color: '#0f172a', text_color: '#475569', bg_color: '', accent_color: '#e8622a', overlay_color: '#000', overlay_opacity: 60,
      font_family: 'serif', font_weight: '700', text_transform: 'none', letter_spacing: 0,
      show_title: true, show_category: true, show_excerpt: false,
      stagger_entrance: true, dim_others: true, featured_ribbon: false, year_stamp: true, index_numbering: true,
      grayscale_default: false, cursor_label_enabled: true, cursor_label_text: 'View case',
      border_radius: 0,
      container_padding: { top: 40, right: 40, bottom: 40, left: 40 },
      effect_color: '#e8622a',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'serif', wow_rotation: 0, wow_perspective: 1000, wow_tilt_x: -2, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'photographer-mosaic': {
      layout: 'mosaic', columns: 3, gap: 4, image_ratio: 'auto', hover_effect: 'color-splash', caption_position: 'overlay',
      filter_bar: false,
      title_color: '#fff', text_color: '#cbd5e1', bg_color: '#0a0a0a', accent_color: '#fff', overlay_color: '#000', overlay_opacity: 70,
      font_family: 'mono', font_weight: '400', text_transform: 'uppercase', letter_spacing: 2,
      show_title: true, show_category: false, show_excerpt: false,
      stagger_entrance: false, dim_others: false, featured_ribbon: false, year_stamp: false, index_numbering: false,
      grayscale_default: true, cursor_label_enabled: true, cursor_label_text: 'Open',
      border_radius: 0,
      container_padding: { top: 0, right: 0, bottom: 0, left: 0 },
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'monospace', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'agency-tape': {
      layout: 'carousel', columns: 4, gap: 20, image_ratio: '4:3', hover_effect: 'fade', caption_position: 'below',
      filter_bar: false,
      title_color: '#0f172a', text_color: '#64748b', bg_color: '', accent_color: '#0f172a', overlay_color: '#000', overlay_opacity: 60,
      font_family: 'sans', font_weight: '600', text_transform: 'none', letter_spacing: 0,
      show_title: true, show_category: false, show_excerpt: false,
      stagger_entrance: false, dim_others: false, featured_ribbon: false,
      carousel_speed: 30, carousel_pause_on_hover: true,
      border_radius: 8,
      container_padding: { top: 20, right: 0, bottom: 20, left: 0 },
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'architect-line': {
      layout: 'grid', columns: 3, gap: 0, image_ratio: '3:2', hover_effect: 'none', caption_position: 'below',
      filter_bar: true, filter_style: 'underline', filter_color: '#666', filter_active_color: '#000',
      title_color: '#000', text_color: '#666', bg_color: '#fafafa', accent_color: '#000', overlay_color: '#000', overlay_opacity: 0,
      font_family: 'mono', font_weight: '400', text_transform: 'uppercase', letter_spacing: 2,
      show_title: true, show_category: true, show_excerpt: false,
      stagger_entrance: true, dim_others: false, featured_ribbon: false, year_stamp: false, index_numbering: true,
      grayscale_default: true,
      border_radius: 0,
      container_padding: { top: 24, right: 24, bottom: 24, left: 24 },
      effect_color: '#000000',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'monospace', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
  },
  queryloop: {
    // v1.0.73 — refactor profondo wow_*: i preset audaci ora settano direttamente i field standard via TILE_PRESETS.
    'magazine-trio': {
      layout: 'magazine-trio', columns: 3, gap: 24,
      show_image: true, show_title: true, show_excerpt: true, show_date: true, show_author: false, show_category: true, show_read_more: false,
      hover_effect: 'image-zoom', card_style: 'none',
      title_color: '#0f172a', text_color: '#475569', meta_color: '#94a3b8', link_color: '#e8622a', accent_color: '#e8622a', bg_color: '',
      font_family: 'serif', title_weight: '700', text_transform: 'none', letter_spacing: 0,
      show_reading_time: false, new_badge: true, new_badge_text: 'New', trending_badge: false,
      enable_search: false, enable_sort_ui: false, enable_taxonomy_tabs: false,
      image_ratio: '16:9', card_radius: { tl: 8, tr: 8, br: 8, bl: 8 },
      container_padding: { top: 0, right: 0, bottom: 0, left: 0 },
      effect_color: '#e8622a',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'serif', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'magazine-hero': {
      layout: 'magazine-hero', columns: 3, gap: 24,
      show_image: true, show_title: true, show_excerpt: true, show_date: true, show_author: true, show_category: true, show_read_more: false,
      hover_effect: 'lift', card_style: 'none',
      title_color: '#0f172a', text_color: '#475569', meta_color: '#94a3b8', link_color: '#0f172a', accent_color: '#dc2626', bg_color: '',
      font_family: 'serif', title_weight: '700',
      show_reading_time: true, new_badge: true, new_badge_text: 'Breaking', trending_badge: true,
      enable_search: false, enable_sort_ui: false, enable_taxonomy_tabs: true, taxonomy_tabs_taxonomy: 'category',
      image_ratio: '16:9', card_radius: { tl: 6, tr: 6, br: 6, bl: 6 },
      container_padding: { top: 0, right: 0, bottom: 0, left: 0 },
      effect_color: '#dc2626',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'serif', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'news-timeline': {
      layout: 'timeline', columns: 1, gap: 0,
      show_image: false, show_title: true, show_excerpt: true, show_date: false, show_author: false, show_category: true, show_read_more: true, read_more_text: 'Approfondisci',
      hover_effect: 'border-grow', card_style: 'none',
      title_color: '#0f172a', text_color: '#64748b', meta_color: '#94a3b8', link_color: '#0f172a', accent_color: '#0f172a', bg_color: '',
      font_family: 'serif', title_weight: '600',
      show_reading_time: true, new_badge: false, trending_badge: false,
      timeline_group_by: 'month',
      enable_search: false, enable_sort_ui: false, enable_taxonomy_tabs: false,
      card_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
      container_padding: { top: 16, right: 16, bottom: 16, left: 0 },
      effect_color: '#0f172a',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'serif', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'newspaper-cols': {
      layout: 'newspaper', columns: 3, gap: 28,
      show_image: true, show_title: true, show_excerpt: true, show_date: true, show_author: true, show_category: true, show_read_more: false,
      hover_effect: 'title-underline', card_style: 'none',
      title_color: '#0f172a', text_color: '#475569', meta_color: '#94a3b8', link_color: '#0f172a', accent_color: '#dc2626', bg_color: '#fafaf9',
      font_family: 'serif', title_weight: '700', text_transform: 'none',
      show_reading_time: false, new_badge: false, trending_badge: false,
      enable_search: false, enable_sort_ui: false, enable_taxonomy_tabs: false,
      image_ratio: '4:3', card_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
      container_padding: { top: 24, right: 24, bottom: 24, left: 24 },
      effect_color: '#dc2626',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'serif', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'editorial-list': {
      layout: 'list-rich', columns: 1, gap: 32,
      show_image: true, show_title: true, show_excerpt: true, show_date: true, show_author: true, show_category: true, show_read_more: true, read_more_text: 'Leggi tutto',
      hover_effect: 'arrow-slide', card_style: 'none',
      title_color: '#0f172a', text_color: '#64748b', meta_color: '#94a3b8', link_color: '#e8622a', accent_color: '#e8622a', bg_color: '',
      font_family: 'serif', title_weight: '700',
      show_reading_time: true, show_comment_count: true, new_badge: true, new_badge_text: 'New',
      enable_search: false, enable_sort_ui: true,
      image_ratio: '4:3', card_radius: { tl: 8, tr: 8, br: 8, bl: 8 },
      container_padding: { top: 0, right: 0, bottom: 0, left: 0 },
      effect_color: '#e8622a',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'serif', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'news-ticker-strip': {
      layout: 'ticker-strip', columns: 4, gap: 20,
      show_image: true, show_title: true, show_excerpt: false, show_date: true, show_author: false, show_category: true, show_read_more: false,
      hover_effect: 'lift', card_style: 'shadow',
      title_color: '#0f172a', text_color: '#64748b', meta_color: '#94a3b8', link_color: '#0f172a', accent_color: '#e8622a', bg_color: '',
      font_family: 'sans', title_weight: '600',
      show_reading_time: false, new_badge: true, new_badge_text: 'Fresh', trending_badge: false,
      image_ratio: '16:9', card_radius: { tl: 10, tr: 10, br: 10, bl: 10 },
      container_padding: { top: 12, right: 0, bottom: 12, left: 0 },
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'cards-modern': {
      layout: 'grid', columns: 3, gap: 24,
      show_image: true, show_title: true, show_excerpt: true, show_date: true, show_author: true, show_category: true, show_read_more: true, read_more_text: 'Continua →',
      hover_effect: 'lift', card_style: 'shadow',
      title_color: '#0f172a', text_color: '#64748b', meta_color: '#94a3b8', link_color: '#0f172a', accent_color: '#3b82f6', bg_color: '',
      font_family: 'sans', title_weight: '700',
      show_reading_time: true, new_badge: true, new_badge_text: 'New', trending_badge: false,
      enable_search: true, enable_sort_ui: false,
      image_ratio: '16:9', card_radius: { tl: 14, tr: 14, br: 14, bl: 14 },
      container_padding: { top: 0, right: 0, bottom: 0, left: 0 },
      effect_color: '#3b82f6',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'compact-index': {
      layout: 'list', columns: 1, gap: 0,
      show_image: false, show_title: true, show_excerpt: false, show_date: true, show_author: false, show_category: true, show_read_more: false,
      hover_effect: 'title-underline', card_style: 'none',
      title_color: '#0f172a', text_color: '#64748b', meta_color: '#94a3b8', link_color: '#0f172a', accent_color: '#0f172a', bg_color: '',
      font_family: 'sans', title_weight: '500',
      show_reading_time: true, new_badge: false, trending_badge: false,
      enable_search: true, enable_sort_ui: false,
      card_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
      container_padding: { top: 16, right: 16, bottom: 16, left: 16 },
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'bento-news': {
      layout: 'bento', columns: 3, gap: 14,
      show_image: true, show_title: true, show_excerpt: false, show_date: false, show_author: false, show_category: true, show_read_more: false,
      hover_effect: 'image-zoom', card_style: 'shadow',
      title_color: '#0f172a', text_color: '#475569', meta_color: '#94a3b8', link_color: '#0f172a', accent_color: '#e8622a', bg_color: '',
      font_family: 'sans', title_weight: '700',
      show_reading_time: false, new_badge: true, trending_badge: true,
      image_ratio: 'auto', card_radius: { tl: 16, tr: 16, br: 16, bl: 16 },
      container_padding: { top: 0, right: 0, bottom: 0, left: 0 },
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'alternating': {
      layout: 'alternating', columns: 1, gap: 64,
      show_image: true, show_title: true, show_excerpt: true, show_date: true, show_author: true, show_category: true, show_read_more: true,
      hover_effect: 'arrow-slide', card_style: 'none',
      title_color: '#0f172a', text_color: '#475569', meta_color: '#94a3b8', link_color: '#e8622a', accent_color: '#e8622a', bg_color: '',
      font_family: 'serif', title_weight: '700',
      show_reading_time: true, new_badge: false, trending_badge: false,
      image_ratio: '4:3', card_radius: { tl: 8, tr: 8, br: 8, bl: 8 },
      container_padding: { top: 24, right: 24, bottom: 24, left: 24 },
      effect_color: '#e8622a',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'serif', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'stacked-blog': {
      layout: 'stacked', columns: 1, gap: 32,
      show_image: true, show_title: true, show_excerpt: true, show_date: true, show_author: true, show_category: true, show_read_more: false,
      hover_effect: 'image-zoom', card_style: 'none',
      title_color: '#fff', text_color: '#cbd5e1', meta_color: '#cbd5e1', link_color: '#fff', accent_color: '#fbbf24', bg_color: '',
      font_family: 'serif', title_weight: '800',
      show_reading_time: true, new_badge: true, trending_badge: false,
      image_ratio: '21:9', card_radius: { tl: 16, tr: 16, br: 16, bl: 16 },
      container_padding: { top: 0, right: 0, bottom: 0, left: 0 },
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'serif', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
    'tabs-categories': {
      layout: 'grid', columns: 3, gap: 20,
      show_image: true, show_title: true, show_excerpt: false, show_date: true, show_author: false, show_category: true, show_read_more: false,
      hover_effect: 'lift', card_style: 'shadow',
      title_color: '#0f172a', text_color: '#64748b', meta_color: '#94a3b8', link_color: '#0f172a', accent_color: '#0ea5e9', bg_color: '',
      font_family: 'sans', title_weight: '600',
      show_reading_time: true, new_badge: false, trending_badge: false,
      enable_taxonomy_tabs: true, taxonomy_tabs_taxonomy: 'category',
      enable_search: false, enable_sort_ui: false,
      image_ratio: '16:9', card_radius: { tl: 12, tr: 12, br: 12, bl: 12 },
      container_padding: { top: 0, right: 0, bottom: 0, left: 0 },
      effect_color: '#0ea5e9',
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false,
    },
  },
  gallery: {
    'modern-grid':    { entrance_animation: 'fade',       layout: 'grid', columns: 3, gap: 8, img_height: '220px', thumb_radius: 8, fx_hover_zoom: true, fx_hover_zoom_scale: 1.06, fx_hover_tilt: false, fx_kenburns: false, fx_vignette: false, fx_grain: false, fx_tint: false },
    'mosaic-art':     { entrance_animation: 'zoom-in',    layout: 'masonry', columns: 4, gap: 4, thumb_radius: 0, fx_hover_zoom: true, fx_hover_zoom_scale: 1.04, fx_hover_tilt: false, fx_kenburns: false, fx_vignette: true, fx_vignette_strength: 30, fx_grain: false, fx_tint: false },
    'cinema-strip':   { entrance_animation: 'slide-left', layout: 'grid', columns: 4, gap: 4, img_height: '300px', thumb_radius: 0, fx_kenburns: true, fx_kenburns_speed: 22, fx_kenburns_scale: 1.12, fx_hover_zoom: false, fx_vignette: true, fx_vignette_strength: 50, fx_grain: true, fx_grain_opacity: 8, fx_tint: false },
    'photo-wall':     { entrance_animation: 'fade',       layout: 'justified', columns: 4, gap: 6, thumb_radius: 4, fx_hover_zoom: true, fx_hover_zoom_scale: 1.05, fx_kenburns: false, fx_vignette: true, fx_vignette_strength: 25, fx_grain: false, fx_tint: true, fx_tint_color: '#0a0a0a', fx_tint_opacity: 35, fx_tint_blend: 'multiply' },
    'polaroid-album': { entrance_animation: 'bounce',     layout: 'grid', columns: 3, gap: 28, img_height: '240px', thumb_radius: 0, fx_hover_zoom: false, fx_hover_tilt: true, fx_hover_tilt_angle: 4, fx_kenburns: false, fx_vignette: false, fx_grain: true, fx_grain_opacity: 5, fx_tint: false },
    'glass-tiles':    { entrance_animation: 'fade',       layout: 'grid', columns: 3, gap: 14, img_height: '240px', thumb_radius: 18, fx_hover_zoom: true, fx_hover_zoom_scale: 1.06, fx_kenburns: false, fx_vignette: false, fx_grain: false, fx_tint: false, more_bg: 'rgba(255,255,255,0.55)', more_color: '#0f172a' },
    'neon-frame':     { entrance_animation: 'zoom-in',    layout: 'grid', columns: 3, gap: 12, img_height: '220px', thumb_radius: 4, fx_hover_zoom: true, fx_hover_zoom_scale: 1.08, fx_kenburns: false, fx_vignette: true, fx_vignette_strength: 30, fx_grain: false, fx_tint: true, fx_tint_color: '#ff6a2a', fx_tint_opacity: 18, fx_tint_blend: 'overlay' },
    'brutalist-grid': { entrance_animation: 'slide-left', layout: 'grid', columns: 3, gap: 0, img_height: '240px', thumb_radius: 0, fx_hover_zoom: false, fx_hover_tilt: false, fx_kenburns: false, fx_vignette: false, fx_grain: false, fx_tint: false, more_bg: '#000', more_color: '#fef9c3' },
    'soft-pastels':   { entrance_animation: 'fade',       layout: 'masonry', columns: 3, gap: 18, thumb_radius: 24, fx_hover_zoom: true, fx_hover_zoom_scale: 1.05, fx_kenburns: false, fx_vignette: false, fx_grain: false, fx_tint: true, fx_tint_color: '#fbbf24', fx_tint_opacity: 8, fx_tint_blend: 'soft-light' },
    'sticker-fun':    { entrance_animation: 'bounce',     layout: 'grid', columns: 3, gap: 22, img_height: '220px', thumb_radius: 12, fx_hover_zoom: false, fx_hover_tilt: true, fx_hover_tilt_angle: 6, fx_kenburns: false, fx_vignette: false, fx_grain: false, fx_tint: false },
    'vhs-retro':      { entrance_animation: 'slide-up',   layout: 'grid', columns: 3, gap: 6, img_height: '220px', thumb_radius: 0, fx_hover_zoom: false, fx_kenburns: false, fx_vignette: true, fx_vignette_strength: 45, fx_grain: true, fx_grain_opacity: 18, fx_tint: true, fx_tint_color: '#00ff8c', fx_tint_opacity: 12, fx_tint_blend: 'soft-light' },
    'tilt-3d':        { entrance_animation: 'zoom-in',    layout: 'grid', columns: 3, gap: 18, img_height: '240px', thumb_radius: 12, fx_hover_zoom: false, fx_hover_tilt: true, fx_hover_tilt_angle: 10, fx_kenburns: false, fx_vignette: false, fx_grain: false, fx_tint: false },
  },
  progallery: {
    'modern-grid':       { layout_family: 'classic', layout: 'grid', columns: 3, gap: 8, img_height: '240px', thumb_radius: 8, hover_effect: 'zoom', hover_zoom_scale: 1.06, filter: 'none', frame: 'none', anim_border: 'none', entrance: 'none' },
    'editorial-mosaic':  { layout_family: 'classic', layout: 'mosaic', columns: 3, gap: 4, thumb_radius: 0, hover_effect: 'zoom', hover_zoom_scale: 1.05, filter: 'none', frame: 'none', anim_border: 'none', entrance: 'fade-up', entrance_stagger: 80 },
    'cinema-strip':      { layout_family: 'strip', layout: 'strip_marquee', strip_height: 320, strip_item_width: 380, strip_speed: 35, strip_pause_hover: true, strip_fade_edges: true, gap: 10, thumb_radius: 0, hover_effect: 'zoom', filter: 'none', frame: 'none' },
    'bento-pro':         { layout_family: 'classic', layout: 'metro', columns: 4, gap: 10, metro_cell_height: 220, thumb_radius: 18, hover_effect: 'zoom', hover_zoom_scale: 1.04, filter: 'none', frame: 'none', entrance: 'fade-up' },
    'spotlight-expand':  { layout_family: 'classic', layout: 'expand', expand_ratio: 4, expand_shrink: 0.5, expand_speed: 500, gap: 6, thumb_radius: 6, hover_effect: 'none', filter: 'none', frame: 'none' },
    'glass-collage':     { layout_family: 'classic', layout: 'collage', columns: 3, gap: 14, thumb_radius: 18, hover_effect: 'glow', hover_glow_color: '#ffffff', hover_glow_spread: 24, filter: 'none', frame: 'none', anim_border: 'none' },
    'neon-honeycomb':    { layout_family: 'classic', layout: 'honeycomb', columns: 3, gap: 6, thumb_radius: 0, hover_effect: 'glow', hover_glow_color: '#ff6a2a', hover_glow_spread: 28, anim_border: 'neon', anim_border_color: '#ff6a2a', anim_border_thickness: 2, filter: 'none', frame: 'none' },
    'brutalist-mosaic':  { layout_family: 'classic', layout: 'mosaic', columns: 3, gap: 0, thumb_radius: 0, hover_effect: 'none', filter: 'none', frame: 'border', frame_color: '#000000', frame_inset_padding: 0, anim_border: 'none' },
    'parallax-drift':    { layout_family: 'classic', layout: 'drift', drift_height: 1400, drift_intensity: 70, drift_rotation: 14, columns: 3, gap: 14, thumb_radius: 12, hover_effect: 'zoom', filter: 'none', frame: 'none' },
    'sticker-cascade':   { layout_family: 'classic', layout: 'cascade', cascade_spread: 70, cascade_overlap: 40, cascade_rotation: 10, columns: 3, gap: 18, thumb_radius: 8, hover_effect: 'tilt', hover_tilt_angle: 6, filter: 'none', frame: 'border', frame_color: '#0f172a', frame_inset_padding: 6 },
    'vhs-coverflow':     { layout_family: 'strip', layout: 'strip_coverflow', filmstrip_item_width: 320, filmstrip_center_zoom: 1.18, filmstrip_side_tilt: 35, gap: 6, thumb_radius: 4, hover_effect: 'none', filter: 'duotone', duotone_dark: '#003b1f', duotone_light: '#7cf3b9', duotone_intensity: 75, frame: 'none' },
    'tilt-puzzle':       { layout_family: 'classic', layout: 'puzzle', puzzle_style: 'classic', columns: 4, gap: 8, thumb_radius: 14, hover_effect: 'tilt3d', hover_tilt_angle: 12, filter: 'none', frame: 'none', entrance: 'scale-in', entrance_stagger: 100 },
  },
  postgrid: {
    'modern-cards':     { columns: 3, gap: 'medium', card_style: 'shadow', card_radius: 12, image_height: '220', show_image: true, show_category: true, show_excerpt: true, hover_effect: 'lift', title_color: '', card_primary_bg: '#ffffff' },
    'magazine-trio':    { columns: 3, gap: 'large', card_style: 'default', card_radius: 6, image_height: '280', show_image: true, show_category: true, show_excerpt: false, hover_effect: 'zoom', title_color: '' },
    'editorial-list':   { columns: 1, gap: 'large', card_style: 'default', card_radius: 0, image_height: '200', show_image: true, show_category: true, show_excerpt: true, hover_effect: 'none' },
    'compact-index':    { columns: 1, gap: 'small', card_style: 'default', card_radius: 0, show_image: false, show_category: true, show_excerpt: false, hover_effect: 'none' },
    'newspaper-cols':   { columns: 3, gap: 'medium', card_style: 'default', card_radius: 0, image_height: '180', show_image: true, show_category: true, show_excerpt: true, hover_effect: 'none' },
    'glass-cards':      { columns: 3, gap: 'medium', card_style: 'shadow', card_radius: 18, image_height: '220', show_image: true, show_category: true, show_excerpt: true, hover_effect: 'lift', card_primary_bg: 'rgba(255,255,255,0.55)' },
    'neon-grid':        { columns: 3, gap: 'medium', card_style: 'border', card_radius: 4, image_height: '220', show_image: true, show_category: true, show_excerpt: false, hover_effect: 'glow', card_primary_bg: '#0a0f1c' },
    'brutalist-blocks': { columns: 3, gap: 'small', card_style: 'border', card_radius: 0, image_height: '220', show_image: true, show_category: true, show_excerpt: false, hover_effect: 'none', card_primary_bg: '#fef9c3' },
    'gradient-soft':    { columns: 3, gap: 'large', card_style: 'shadow', card_radius: 24, image_height: '220', show_image: true, show_category: true, show_excerpt: true, hover_effect: 'lift' },
    'sticker-cards':    { columns: 3, gap: 'large', card_style: 'shadow', card_radius: 8, image_height: '220', show_image: true, show_category: true, show_excerpt: false, hover_effect: 'none' },
    'retro-zine':       { columns: 3, gap: 'small', card_style: 'border', card_radius: 0, image_height: '220', show_image: true, show_category: true, show_excerpt: true, hover_effect: 'none' },
    'tilt-3d':          { columns: 3, gap: 'medium', card_style: 'shadow', card_radius: 12, image_height: '220', show_image: true, show_category: true, show_excerpt: false, hover_effect: 'tilt' },
  },
  relatedposts: {
    'modern-cards':     { columns: '3', gap: '20', card_border_radius: '12', image_ratio: '16/9', show_image: true, show_date: true, show_excerpt: true, show_category: true, hover_effect: 'shadow', title_color: '#0f172a', text_color: '#475569', date_color: '#94a3b8', card_background: '#ffffff' },
    'editorial-list':   { columns: '1', gap: '32', card_border_radius: '0', image_ratio: '4/3', show_image: true, show_date: true, show_excerpt: true, show_category: true, hover_effect: 'none', title_color: '#0f172a', text_color: '#475569', date_color: '#94a3b8', card_background: '' },
    'compact-row':      { columns: '1', gap: '12', card_border_radius: '0', image_ratio: '1/1', show_image: false, show_date: true, show_excerpt: false, show_category: true, hover_effect: 'none' },
    'magazine-trio':    { columns: '3', gap: '24', card_border_radius: '6', image_ratio: '16/9', show_image: true, show_date: true, show_excerpt: false, show_category: true, hover_effect: 'zoom', title_color: '#0f172a', text_color: '#475569' },
    'minimal-line':     { columns: '1', gap: '8', card_border_radius: '0', image_ratio: '1/1', show_image: false, show_date: false, show_excerpt: false, show_category: false, hover_effect: 'none' },
    'glass-cards':      { columns: '3', gap: '20', card_border_radius: '18', image_ratio: '16/9', show_image: true, show_date: true, show_excerpt: false, show_category: true, hover_effect: 'shadow', card_background: 'rgba(255,255,255,0.55)' },
    'neon-tiles':       { columns: '3', gap: '16', card_border_radius: '4', image_ratio: '4/3', show_image: true, show_date: false, show_excerpt: false, show_category: true, hover_effect: 'shadow', card_background: '#0a0f1c', title_color: '#22d3ee', text_color: '#94a3b8' },
    'brutalist-stamp':  { columns: '3', gap: '16', card_border_radius: '0', image_ratio: '4/3', show_image: true, show_date: true, show_excerpt: false, show_category: true, hover_effect: 'none', card_background: '#fef9c3', title_color: '#000', text_color: '#000' },
    'gradient-soft':    { columns: '3', gap: '24', card_border_radius: '20', image_ratio: '16/9', show_image: true, show_date: true, show_excerpt: true, show_category: true, hover_effect: 'shadow' },
    'sticker-cards':    { columns: '3', gap: '24', card_border_radius: '8', image_ratio: '4/3', show_image: true, show_date: false, show_excerpt: false, show_category: true, hover_effect: 'none' },
    'retro-zine':       { columns: '3', gap: '12', card_border_radius: '0', image_ratio: '4/3', show_image: true, show_date: true, show_excerpt: true, show_category: true, hover_effect: 'none' },
    'tilt-3d':          { columns: '3', gap: '20', card_border_radius: '12', image_ratio: '16/9', show_image: true, show_date: true, show_excerpt: false, show_category: true, hover_effect: 'shadow' },
  },
  // Hero: schema dual { settings, style }. Riscritto v3.55.14 dopo il refactor che
  // ha eliminato bg_type/bg_color/bg_gradient_*/bg_image/bg_video/overlay_* legacy
  // e passato il bg al sistema unificato style.bg (BackgroundControls).
  // Il dispatcher applyTilePresetTheme distingue i due rami e li applica al posto giusto.
  hero: {
    'modern-centered': {
      settings: {
        entrance_animation: 'fade',
        min_height: '600px', vertical_align: 'center', horizontal_align: 'center', text_align: 'center',
        tile_padding: { top: 80, right: 24, bottom: 80, left: 24 }, content_max_width: '720',
        title_font_family: '', title_font_weight: '700', title_letter_spacing: '0', title_line_height: '1.15', title_text_transform: 'none', title_color: '',
        subtitle_font_weight: '400', subtitle_color: '',
        text_color: '#FFFFFF',
        cta_style: 'filled', cta_radius: { tl: 6, tr: 6, br: 6, bl: 6 },
      },
      // `color` agisce da fallback automatico via get_effective_bg() quando l'utente non
      // ha ancora caricato un'immagine: il bg resta visibile (solid scuro) anziché
      // collassare a transparent — altrimenti il testo bianco sarebbe invisibile su WP bg.
      style: { bg: { type: 'image', image_url: '', color: '#1e293b', image_size: 'cover', image_position: 'center center', overlay_color: '#000000', overlay_opacity: 40 } },
    },
    'split-image': {
      settings: {
        entrance_animation: 'slide-left',
        min_height: '560px', vertical_align: 'center', horizontal_align: 'left', text_align: 'left',
        tile_padding: { top: 60, right: 60, bottom: 60, left: 60 }, content_max_width: '600',
        title_font_family: '', title_font_weight: '700', title_letter_spacing: '-0.5', title_line_height: '1.1', title_text_transform: 'none', title_color: '',
        subtitle_font_weight: '400', subtitle_color: '',
        text_color: '#0f172a',
        cta_style: 'filled', cta_radius: { tl: 6, tr: 6, br: 6, bl: 6 },
      },
      style: { bg: { type: 'solid', color: '#fafaf9' } },
    },
    'minimal-editorial': {
      settings: {
        entrance_animation: 'fade',
        min_height: '500px', vertical_align: 'center', horizontal_align: 'left', text_align: 'left',
        tile_padding: { top: 80, right: 80, bottom: 80, left: 80 }, content_max_width: '720',
        title_font_family: 'serif', title_font_weight: '400', title_letter_spacing: '-1', title_line_height: '1.05', title_text_transform: 'none', title_color: '',
        subtitle_font_weight: '400', subtitle_color: '',
        text_color: '#0f172a',
        cta_style: 'ghost', cta_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
      },
      style: { bg: { type: 'solid', color: '#ffffff' } },
    },
    'bold-statement': {
      settings: {
        entrance_animation: 'zoom-in',
        min_height: '720px', vertical_align: 'center', horizontal_align: 'center', text_align: 'center',
        tile_padding: { top: 100, right: 24, bottom: 100, left: 24 }, content_max_width: '900',
        title_font_family: '', title_font_weight: '900', title_letter_spacing: '-2', title_line_height: '0.95', title_text_transform: 'uppercase', title_color: '',
        subtitle_font_weight: '500', subtitle_color: '',
        text_color: '#FFFFFF',
        cta_style: 'outline', cta_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
      },
      style: { bg: { type: 'solid', color: '#0a0a0a' } },
    },
    'video-cinema': {
      settings: {
        entrance_animation: 'fade',
        min_height: '780px', vertical_align: 'center', horizontal_align: 'center', text_align: 'center',
        tile_padding: { top: 80, right: 24, bottom: 80, left: 24 }, content_max_width: '760',
        title_font_family: 'serif', title_font_weight: '700', title_letter_spacing: '-1', title_line_height: '1.1', title_text_transform: 'none', title_color: '',
        subtitle_font_weight: '400', subtitle_color: '',
        text_color: '#FFFFFF',
        cta_style: 'filled', cta_radius: { tl: 6, tr: 6, br: 6, bl: 6 },
      },
      style: { bg: { type: 'video', video_url: '', color: '#0a0a0a', video_size: 'cover', overlay_color: '#000000', overlay_opacity: 55 } },
    },
    'glass-overlay': {
      settings: {
        entrance_animation: 'fade',
        min_height: '640px', vertical_align: 'center', horizontal_align: 'center', text_align: 'center',
        tile_padding: { top: 80, right: 24, bottom: 80, left: 24 }, content_max_width: '720',
        title_font_family: '', title_font_weight: '700', title_letter_spacing: '0', title_line_height: '1.15', title_text_transform: 'none', title_color: '',
        subtitle_font_weight: '400', subtitle_color: '',
        text_color: '#0f172a',
        cta_style: 'filled', cta_radius: { tl: 999, tr: 999, br: 999, bl: 999 },
      },
      style: { bg: { type: 'image', image_url: '', color: '#cbd5e1', image_size: 'cover', image_position: 'center center', overlay_color: '#ffffff', overlay_opacity: 55 } },
    },
    'neon-cyberpunk': {
      settings: {
        entrance_animation: 'slide-up',
        min_height: '680px', vertical_align: 'center', horizontal_align: 'center', text_align: 'center',
        tile_padding: { top: 80, right: 24, bottom: 80, left: 24 }, content_max_width: '720',
        title_font_family: '', title_font_weight: '700', title_letter_spacing: '2', title_line_height: '1.1', title_text_transform: 'uppercase',
        title_color: '#ff6a2a', title_text_shadow: '0 0 20px rgba(0,0,0,0.8)',
        subtitle_color: '#94a3b8', subtitle_font_weight: '500',
        text_color: '#FFFFFF',
        cta_style: 'outline', cta_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
      },
      style: { bg: { type: 'solid', color: '#0a0f1c' } },
    },
    'brutalist-mega': {
      settings: {
        entrance_animation: 'slide-left',
        min_height: '700px', vertical_align: 'center', horizontal_align: 'left', text_align: 'left',
        tile_padding: { top: 60, right: 40, bottom: 60, left: 40 }, content_max_width: '900',
        title_font_family: '', title_font_weight: '900', title_letter_spacing: '-2', title_line_height: '0.9', title_text_transform: 'uppercase',
        title_color: '#000000',
        subtitle_font_weight: '600', subtitle_color: '',
        text_color: '#000000',
        cta_style: 'filled', cta_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
      },
      style: { bg: { type: 'solid', color: '#fef9c3' } },
    },
    'gradient-aurora': {
      settings: {
        entrance_animation: 'fade',
        min_height: '640px', vertical_align: 'center', horizontal_align: 'center', text_align: 'center',
        tile_padding: { top: 80, right: 24, bottom: 80, left: 24 }, content_max_width: '720',
        title_font_family: '', title_font_weight: '800', title_letter_spacing: '-1', title_line_height: '1.1', title_text_transform: 'none', title_color: '',
        subtitle_font_weight: '400', subtitle_color: '',
        text_color: '#FFFFFF',
        cta_style: 'filled', cta_radius: { tl: 999, tr: 999, br: 999, bl: 999 },
      },
      style: { bg: { type: 'gradient', gradient_from: '#a78bfa', gradient_to: '#ec4899', gradient_angle: 135 } },
    },
    'sticker-collage': {
      settings: {
        entrance_animation: 'bounce',
        min_height: '560px', vertical_align: 'center', horizontal_align: 'center', text_align: 'center',
        tile_padding: { top: 60, right: 24, bottom: 60, left: 24 }, content_max_width: '700',
        title_font_family: '', title_font_weight: '800', title_letter_spacing: '-1', title_line_height: '1.1', title_text_transform: 'none',
        title_color: '#0f172a',
        subtitle_color: '#475569', subtitle_font_weight: '500',
        text_color: '#0f172a',
        cta_style: 'filled', cta_radius: { tl: 8, tr: 8, br: 8, bl: 8 },
      },
      style: { bg: { type: 'solid', color: '#fef3c7' } },
    },
    'retro-poster': {
      settings: {
        entrance_animation: 'fade',
        min_height: '700px', vertical_align: 'center', horizontal_align: 'center', text_align: 'center',
        tile_padding: { top: 80, right: 24, bottom: 80, left: 24 }, content_max_width: '720',
        title_font_family: 'mono', title_font_weight: '700', title_letter_spacing: '2', title_line_height: '1.1', title_text_transform: 'uppercase',
        title_color: '#00ff8c',
        subtitle_color: '#7cf3b9', subtitle_font_weight: '400',
        text_color: '#00ff8c',
        cta_style: 'outline', cta_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
      },
      style: { bg: { type: 'solid', color: '#0c0c0c' } },
    },
    'tilt-parallax': {
      settings: {
        entrance_animation: 'zoom-in',
        min_height: '640px', vertical_align: 'center', horizontal_align: 'center', text_align: 'center',
        tile_padding: { top: 80, right: 24, bottom: 80, left: 24 }, content_max_width: '720',
        title_font_family: '', title_font_weight: '800', title_letter_spacing: '-1', title_line_height: '1.1', title_text_transform: 'none', title_color: '',
        subtitle_font_weight: '400', subtitle_color: '',
        text_color: '#FFFFFF',
        cta_style: 'filled', cta_radius: { tl: 12, tr: 12, br: 12, bl: 12 },
      },
      style: { bg: { type: 'image', image_url: '', color: '#1e293b', image_size: 'cover', image_position: 'center center', image_parallax: true, overlay_color: '#000000', overlay_opacity: 40 } },
    },
  },
  pricing: {
    // v1.0.58 — preset self-contained con cta colors espliciti (era #fff su brand orange ratio 3.63 fail).
    'modern-clean':     { card_radius: '16', shadow: 'sm',   cta_bg_color: '#0ea5e9', cta_text_color: '#fff', cta_hover_bg_color: '#0284c7', text_color: '#0f172a', price_color: '#0f172a' },
    'magazine-card':    { card_radius: '8',  shadow: 'lg',   cta_bg_color: '#000',    cta_text_color: '#fff', cta_hover_bg_color: '#1f2937', text_color: '#0f172a', price_color: '#000' },
    'minimal-mono':     { card_radius: '0',  shadow: 'none', cta_bg_color: '#0f172a', cta_text_color: '#fff', cta_hover_bg_color: '#1e293b', text_color: '#374151', price_color: '#1f2937' },
    'highlighted-pro':  { card_radius: '14', shadow: 'xl',   cta_bg_color: '#7c3aed', cta_text_color: '#fff', cta_hover_bg_color: '#6d28d9', text_color: '#0f172a', price_color: '#7c3aed' },
    'dark-luxury':      { card_radius: '12', shadow: 'lg',   bg_color: '#0a0a0a', cta_bg_color: '#fbbf24', cta_text_color: '#0a0a0a', cta_hover_bg_color: '#fde047', text_color: '#fff', price_color: '#fbbf24' },
    'glass-tier':       { card_radius: '20', shadow: 'lg',   bg_color: 'rgba(15,23,42,0.85)', cta_bg_color: '#fff', cta_text_color: '#0f172a', cta_hover_bg_color: '#f3f4f6', text_color: '#fff', price_color: '#fff' },
    'neon-cyber':       { card_radius: '4',  shadow: 'none', bg_color: '#0a0a0a', cta_bg_color: '#00ffff', cta_text_color: '#0a0a0a', cta_hover_bg_color: '#22d3ee', text_color: '#00ffff', price_color: '#00ffff' },
    'brutalist-stamp':  { card_radius: '0',  shadow: 'none', bg_color: '#fde047', cta_bg_color: '#000', cta_text_color: '#fde047', cta_hover_bg_color: '#1f2937', text_color: '#000', price_color: '#000' },
    'gradient-aurora':  { card_radius: '24', shadow: 'lg',   bg_color: 'linear-gradient(135deg,#a855f7,#ec4899)', cta_bg_color: '#fff', cta_text_color: '#a855f7', cta_hover_bg_color: '#f3f4f6', text_color: '#fff', price_color: '#fff' },
    'sticker-fun':      { card_radius: '20', shadow: 'md',   bg_color: '#fef3c7', cta_bg_color: '#fbbf24', cta_text_color: '#fff', cta_hover_bg_color: '#f59e0b', text_color: '#78350f', price_color: '#78350f' },
    'retro-receipt':    { card_radius: '0',  shadow: 'sm',   bg_color: '#fafafa', cta_bg_color: '#1f2937', cta_text_color: '#fff', cta_hover_bg_color: '#0f172a', text_color: '#1f2937', price_color: '#000' },
    'tilt-floating':    { card_radius: '12', shadow: 'xl',   bg_color: '#6366f1', cta_bg_color: '#312e81', cta_text_color: '#fff', cta_hover_bg_color: '#1e1b4b', text_color: '#fff', price_color: '#fff' },
  },
  testimonial: {
    'modern-card':      { layout: 'single', bg_color: '#ffffff', text_color: '#0f172a' },
    'magazine-pull':    { layout: 'single', bg_color: '', text_color: '#0f172a' },
    'editorial-serif':  { layout: 'single', bg_color: '', text_color: '#0f172a' },
    'minimal-line':     { layout: 'single', bg_color: '', text_color: '#0f172a' },
    'agency-bold':      { layout: 'single', bg_color: '#0a0a0a', text_color: '#ffffff' },
    'glass-frosted':    { layout: 'single', bg_color: 'rgba(255,255,255,0.55)', text_color: '#0f172a' },
    'neon-quote':       { layout: 'single', bg_color: '#0a0f1c', text_color: '#22d3ee' },
    'brutalist-stamp':  { layout: 'single', bg_color: '#fef9c3', text_color: '#000000' },
    'gradient-soft':    { layout: 'single', bg_color: '', text_color: '#0f172a' },
    'sticky-note':      { layout: 'single', bg_color: '#fef3c7', text_color: '#0f172a' },
    'retro-typewriter': { layout: 'single', bg_color: '#0c0c0c', text_color: '#00ff8c' },
    'tilt-card':        { layout: 'single', bg_color: '#ffffff', text_color: '#0f172a' },
  },
  quotation: {
    'editorial-classic': { style: 'default', alignment: 'left' },
    'magazine-pull':     { style: 'default', alignment: 'center' },
    'minimal-line':      { style: 'minimal', alignment: 'left' },
    'big-mark':          { style: 'default', alignment: 'left' },
    'centered-script':   { style: 'default', alignment: 'center' },
    'glass-frosted':     { style: 'default', alignment: 'center' },
    'neon-quote':        { style: 'default', alignment: 'center' },
    'brutalist-stamp':   { style: 'default', alignment: 'left' },
    'gradient-text':     { style: 'default', alignment: 'center' },
    'sticky-note':       { style: 'default', alignment: 'left' },
    'retro-typewriter':  { style: 'default', alignment: 'left' },
    'tilt-card':         { style: 'default', alignment: 'left' },
  },
  team: {
    // v1.0.58 — preset self-contained: aggiunti info_text_color (name), role_color (job), info_bg_color (card).
    // Era 0/12 PASS perché defaults vuoti → fallback brand orange invisibile su card chiara.
    'corporate-card':   { card_radius: '12', info_bg_color: '#ffffff',                       info_text_color: '#0f172a', role_color: '#475569' },
    'magazine-row':     { card_radius: '4',  info_bg_color: '#ffffff',                       info_text_color: '#000',    role_color: '#374151' },
    'minimal-clean':    { card_radius: '8',  info_bg_color: '',                              info_text_color: '#0f172a', role_color: '#64748b' },
    'editorial-bold':   { card_radius: '0',  info_bg_color: '#ffffff',                       info_text_color: '#000',    role_color: '#1f2937' },
    'photographer':     { card_radius: '0',  info_bg_color: '#fafaf9',                       info_text_color: '#1f2937', role_color: '#4b5563' },
    'glass-portrait':   { card_radius: '20', info_bg_color: 'rgba(15,23,42,0.85)',          info_text_color: '#ffffff', role_color: '#e5e7eb' },
    'neon-frame':       { card_radius: '4',  info_bg_color: '#0a0a0a',                       info_text_color: '#00ffff', role_color: '#a5f3fc' },
    'brutalist-stamp':  { card_radius: '0',  info_bg_color: '#fde047',                       info_text_color: '#000',    role_color: '#1f2937' },
    'polaroid-photo':   { card_radius: '0',  info_bg_color: '#ffffff',                       info_text_color: '#0f172a', role_color: '#4b5563' },
    'sticker-portrait': { card_radius: '12', info_bg_color: '#fef3c7',                       info_text_color: '#78350f', role_color: '#92400e' },
    'retro-yearbook':   { card_radius: '0',  info_bg_color: '#1a1a2e',                       info_text_color: '#fbbf24', role_color: '#fde047' },
    'tilt-3d':          { card_radius: '14', info_bg_color: '#6366f1',                       info_text_color: '#ffffff', role_color: '#e0e7ff' },
  },
  iconbox: {
    'modern-card':     { alignment: 'center', icon_size: '3', icon_position: 'top' },
    'minimal-line':    { alignment: 'left', icon_size: '2', icon_position: 'left' },
    'magazine-bold':   { alignment: 'left', icon_size: '3', icon_position: 'top' },
    'centered-pill':   { alignment: 'center', icon_size: '2', icon_position: 'top' },
    'horizontal-row':  { alignment: 'left', icon_size: '3', icon_position: 'left' },
    'glass-tile':      { alignment: 'center', icon_size: '3', icon_position: 'top' },
    'neon-icon':       { alignment: 'center', icon_size: '4', icon_position: 'top' },
    'brutalist-block': { alignment: 'left', icon_size: '3', icon_position: 'top' },
    'gradient-circle': { alignment: 'center', icon_size: '3', icon_position: 'top' },
    'sticker-fun':     { alignment: 'center', icon_size: '4', icon_position: 'top' },
    'retro-badge':     { alignment: 'center', icon_size: '3', icon_position: 'top' },
    'tilt-3d':         { alignment: 'center', icon_size: '3', icon_position: 'top' },
  },
  iconlist: {
    'modern-clean':       { icon_color: '#22c55e', icon_size: '22', text_size: '16', gap: '14', icon_shape: 'none', layout: 'vertical', divider: true,  divider_color: '#e5e7eb', text_color: '#0f172a', shadow: 'none' },
    'minimal-mono':       { icon_color: '#0f172a', icon_size: '18', text_size: '15', gap: '10', icon_shape: 'none', layout: 'vertical', divider: false, text_color: '#0f172a', shadow: 'none' },
    'magazine-numbered':  { icon_color: '#ffffff', icon_size: '20', text_size: '17', gap: '16', icon_shape: 'circle',  icon_bg_color: '#e8622a', layout: 'vertical', divider: true,  divider_color: '#fed7aa', text_color: '#0f172a', shadow: 'none' },
    'card-rows':          { icon_color: '#3b82f6', icon_size: '20', text_size: '16', gap: '12', icon_shape: 'rounded', icon_bg_color: '#dbeafe', layout: 'vertical', divider: true,  divider_color: '#e0e7ff', text_color: '#0f172a', shadow: 'sm' },
    'compact-inline':     { icon_color: '#0f172a', icon_size: '16', text_size: '14', gap: '18', icon_shape: 'none', layout: 'horizontal', divider: false, text_color: '#0f172a', shadow: 'none' },
    'glass-rows':         { icon_color: '#0f172a', icon_size: '22', text_size: '16', gap: '14', icon_shape: 'circle',  icon_bg_color: 'rgba(255,255,255,0.4)', layout: 'vertical', divider: true,  divider_color: 'rgba(15,23,42,0.1)', text_color: '#0f172a', shadow: 'sm' },
    'neon-checks':        { icon_color: '#22d3ee', icon_size: '24', text_size: '15', gap: '12', icon_shape: 'circle',  icon_bg_color: '#0f172a', layout: 'vertical', divider: true,  divider_color: '#22d3ee', text_color: '#e0f2fe', shadow: 'none' },
    'brutalist-block':    { icon_color: '#000000', icon_size: '24', text_size: '17', gap: '14', icon_shape: 'square',  icon_bg_color: '#fde047', layout: 'vertical', divider: true,  divider_color: '#000000', text_color: '#000000', shadow: 'none' },
    'gradient-bullets':   { icon_color: '#ffffff', icon_size: '20', text_size: '16', gap: '14', icon_shape: 'circle',  icon_bg_color: '#a78bfa', layout: 'vertical', divider: false, text_color: '#0f172a', shadow: 'sm' },
    'sticker-list':       { icon_color: '#000000', icon_size: '22', text_size: '16', gap: '16', icon_shape: 'rounded', icon_bg_color: '#fde047', layout: 'vertical', divider: false, text_color: '#0f172a', shadow: 'md' },
    'retro-checklist':    { icon_color: '#00ff8c', icon_size: '20', text_size: '15', gap: '10', icon_shape: 'square',  icon_bg_color: '#0a0e0a', layout: 'vertical', divider: true,  divider_color: '#00ff8c', text_color: '#00ff8c', shadow: 'none' },
    'tilt-cards':         { icon_color: '#0f172a', icon_size: '22', text_size: '16', gap: '14', icon_shape: 'rounded', icon_bg_color: '#f1f5f9', layout: 'vertical', divider: false, text_color: '#0f172a', shadow: 'md' },
  },
  flipcard: {
    // v1.0.58 — aggiunti back_cta_bg/back_cta_color per i CTA "Scopri di più" (era hardcoded brand orange ratio 3.63 fail su 12/12).
    'modern-card':     { card_height: '360', card_border_radius: '16', card_shadow: 'md',  front_bg: '#fff',                       back_bg: '#0ea5e9', front_text_color: '#0f172a', back_text_color: '#fff',    back_cta_bg: '#fff',    back_cta_color: '#0ea5e9', title_size: '22', title_weight: '700', flip_direction: 'horizontal' },
    'photo-overlay':   { card_height: '420', card_border_radius: '12', card_shadow: 'lg',  front_bg: '',                            back_bg: 'rgba(0,0,0,0.85)', front_text_color: '#fff',  back_text_color: '#fff', back_cta_bg: '#fff', back_cta_color: '#000',  title_size: '26', title_weight: '800', flip_direction: 'horizontal', front_overlay: 'rgba(0,0,0,0.3)' },
    'magazine-flip':   { card_height: '380', card_border_radius: '0',  card_shadow: 'none',front_bg: '#fde047',                    back_bg: '#000',   front_text_color: '#000',    back_text_color: '#fde047', back_cta_bg: '#fde047', back_cta_color: '#000',    title_size: '28', title_weight: '900', flip_direction: 'horizontal' },
    'minimal-clean':   { card_height: '320', card_border_radius: '4',  card_shadow: 'none',front_bg: '#f9fafb',                    back_bg: '#1f2937',front_text_color: '#1f2937', back_text_color: '#fff',    back_cta_bg: '#fff',    back_cta_color: '#1f2937', title_size: '18', title_weight: '500', flip_direction: 'horizontal' },
    'icon-driven':     { card_height: '340', card_border_radius: '20', card_shadow: 'md',  front_bg: '#fff',                       back_bg: '#7c3aed',front_text_color: '#1f2937', back_text_color: '#fff',    back_cta_bg: '#fff',    back_cta_color: '#7c3aed', title_size: '20', title_weight: '700', flip_direction: 'horizontal', front_icon_size: '64' },
    'glass-flip':      { card_height: '380', card_border_radius: '24', card_shadow: 'lg',  front_bg: 'rgba(255,255,255,0.15)',    back_bg: 'rgba(255,255,255,0.25)', front_text_color: '#fff', back_text_color: '#fff', back_cta_bg: '#fff', back_cta_color: '#0f172a', title_size: '22', title_weight: '600', flip_direction: 'horizontal' },
    'neon-frame':      { card_height: '380', card_border_radius: '8',  card_shadow: 'lg',  front_bg: '#0a0a0a',                    back_bg: '#0a0a0a',front_text_color: '#00ffff', back_text_color: '#ff00ff', back_cta_bg: '#00ffff', back_cta_color: '#0a0a0a', title_size: '24', title_weight: '700', flip_direction: 'horizontal' },
    'brutalist-card':  { card_height: '400', card_border_radius: '0',  card_shadow: 'xl',  front_bg: '#fde047',                    back_bg: '#000',   front_text_color: '#000',    back_text_color: '#fde047', back_cta_bg: '#fde047', back_cta_color: '#000',    title_size: '32', title_weight: '900', flip_direction: 'horizontal' },
    'gradient-aurora': { card_height: '380', card_border_radius: '20', card_shadow: 'lg',  front_bg: 'linear-gradient(135deg,#a855f7,#ec4899)', back_bg: 'linear-gradient(135deg,#3b82f6,#06b6d4)', front_text_color: '#fff', back_text_color: '#fff', back_cta_bg: '#fff', back_cta_color: '#a855f7', title_size: '24', title_weight: '800', flip_direction: 'horizontal' },
    'sticker-flip':    { card_height: '360', card_border_radius: '16', card_shadow: 'md',  front_bg: '#fef3c7',                    back_bg: '#f59e0b',front_text_color: '#78350f', back_text_color: '#fff',    back_cta_bg: '#fff',    back_cta_color: '#92400e', title_size: '22', title_weight: '700', flip_direction: 'horizontal' },
    'retro-poster':    { card_height: '420', card_border_radius: '4',  card_shadow: 'none',front_bg: '#0a0a0a',                    back_bg: '#22c55e',front_text_color: '#22c55e', back_text_color: '#0a0a0a', back_cta_bg: '#0a0a0a', back_cta_color: '#22c55e', title_size: '22', title_weight: '700', flip_direction: 'horizontal' },
    'tilt-3d':         { card_height: '380', card_border_radius: '12', card_shadow: 'xl',  front_bg: '#6366f1',                    back_bg: '#312e81',front_text_color: '#fff',    back_text_color: '#fbbf24', back_cta_bg: '#fbbf24', back_cta_color: '#312e81', title_size: '24', title_weight: '700', flip_direction: 'horizontal' },
  },
  alert: {
    'modern-pill':     { custom_bg_color: '#f0f9ff', custom_border_color: '#bae6fd', custom_text_color: '#0c4a6e', shadow: 'sm', text_align: 'left' },
    'minimal-line':    { custom_bg_color: '', custom_border_color: '#cbd5e1', custom_text_color: '#334155', shadow: 'none', text_align: 'left' },
    'magazine-bar':    { custom_bg_color: '#fef3c7', custom_border_color: '#f59e0b', custom_text_color: '#78350f', shadow: 'none', text_align: 'left' },
    'banner-bold':     { custom_bg_color: '#1e293b', custom_border_color: '#1e293b', custom_text_color: '#fff', shadow: 'md', text_align: 'center' },
    'centered-card':   { custom_bg_color: '#fff', custom_border_color: '#e5e7eb', custom_text_color: '#1f2937', shadow: 'lg', text_align: 'center' },
    'glass-frosted':   { custom_bg_color: 'rgba(255,255,255,0.65)', custom_border_color: 'rgba(255,255,255,0.4)', custom_text_color: '#0f172a', shadow: 'lg', text_align: 'left' },
    'neon-strip':      { custom_bg_color: '#0a0a0a', custom_border_color: '#00ffff', custom_text_color: '#00ffff', shadow: 'lg', text_align: 'left' },
    'brutalist-stamp': { custom_bg_color: '#fde047', custom_border_color: '#000', custom_text_color: '#000', shadow: 'xl', text_align: 'left' },
    'gradient-soft':   { custom_bg_color: 'linear-gradient(135deg,#fbcfe8,#a5b4fc)', custom_border_color: 'transparent', custom_text_color: '#312e81', shadow: 'md', text_align: 'left' },
    'sticker-tape':    { custom_bg_color: '#fef9c3', custom_border_color: '#facc15', custom_text_color: '#713f12', shadow: 'md', text_align: 'left' },
    'retro-banner':    { custom_bg_color: '#fee2e2', custom_border_color: '#ef4444', custom_text_color: '#7f1d1d', shadow: 'none', text_align: 'left' },
    'tilt-card':       { custom_bg_color: '#ede9fe', custom_border_color: '#8b5cf6', custom_text_color: '#4c1d95', shadow: 'lg', text_align: 'center' },
  },
  slideshow: {
    // v1.0.73 — refactor profondo wow_*: i preset audaci ora settano direttamente i field standard via TILE_PRESETS.
    'cinema-hero':     { slide_height: '600', autoplay: true,  autoplay_speed: '5000', transition: 'fade',  show_arrows: true,  show_dots: true,  overlay_color: 'rgba(0,0,0,0.4)', text_color: '#fff', shadow: 'none',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'serif', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'magazine-fade':   { slide_height: '500', autoplay: true,  autoplay_speed: '6000', transition: 'fade',  show_arrows: true,  show_dots: false, overlay_color: 'rgba(0,0,0,0.2)', text_color: '#fff', shadow: 'none',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'serif', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'minimal-clean':   { slide_height: '400', autoplay: false, autoplay_speed: '5000', transition: 'slide', show_arrows: true,  show_dots: true,  overlay_color: '',                text_color: '#1f2937', shadow: 'none',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'editorial-split': { slide_height: '520', autoplay: false, autoplay_speed: '7000', transition: 'slide', show_arrows: true,  show_dots: false, overlay_color: 'rgba(0,0,0,0.3)', text_color: '#fff', shadow: 'none',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'serif', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'fullscreen-cta':  { slide_height: '700', autoplay: true,  autoplay_speed: '8000', transition: 'fade',  show_arrows: false, show_dots: true,  overlay_color: 'rgba(0,0,0,0.5)', text_color: '#fff', shadow: 'none',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'glass-overlay':   { slide_height: '500', autoplay: true,  autoplay_speed: '5000', transition: 'fade',  show_arrows: true,  show_dots: true,  overlay_color: 'rgba(255,255,255,0.15)', text_color: '#fff', shadow: 'lg',
      effect_color: '', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 20, wow_backdrop_saturate: 180, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'neon-tron':       { slide_height: '500', autoplay: true,  autoplay_speed: '4000', transition: 'slide', show_arrows: true,  show_dots: true,  overlay_color: 'rgba(0,255,255,0.1)', text_color: '#00ffff', shadow: 'lg',
      effect_color: '#22d3ee', effect_intensity: 'medium', effect_speed: 2000,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'monospace', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: true, wow_title_glow: true, wow_scanlines: false, wow_terminal_prompt: false },
    'brutalist-mega':  { slide_height: '600', autoplay: false, autoplay_speed: '5000', transition: 'slide', show_arrows: true,  show_dots: false, overlay_color: 'rgba(0,0,0,0.7)', text_color: '#fde047', shadow: 'custom', shadow_h: 8, shadow_v: 8, shadow_blur: 0, shadow_spread: 0, shadow_color: '#000000', shadow_inset: false,
      effect_color: '#000000', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'gradient-aurora': { slide_height: '520', autoplay: true,  autoplay_speed: '5000', transition: 'fade',  show_arrows: true,  show_dots: true,  overlay_color: 'rgba(168,85,247,0.3)', text_color: '#fff', shadow: 'lg',
      effect_color: '#a855f7', effect_intensity: 'medium', effect_speed: 8000,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'sticker-collage': { slide_height: '440', autoplay: true,  autoplay_speed: '4000', transition: 'slide', show_arrows: true,  show_dots: true,  overlay_color: 'rgba(245,158,11,0.2)', text_color: '#fff', shadow: 'md',
      effect_color: '#f59e0b', effect_intensity: 'medium', effect_speed: 0,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'dashed', wow_font_family: 'inherit', wow_rotation: -1.5, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
    'retro-vhs':       { slide_height: '460', autoplay: true,  autoplay_speed: '4500', transition: 'slide', show_arrows: true,  show_dots: true,  overlay_color: 'rgba(34,197,94,0.15)', text_color: '#22c55e', shadow: 'none',
      effect_color: '#22c55e', effect_intensity: 'medium', effect_speed: 1000,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'monospace', wow_rotation: 0, wow_perspective: 0, wow_tilt_x: 0, wow_glow_pulse: false, wow_title_glow: true, wow_scanlines: true, wow_terminal_prompt: false },
    'tilt-parallax':   { slide_height: '560', autoplay: true,  autoplay_speed: '5500', transition: 'fade',  show_arrows: true,  show_dots: true,  overlay_color: 'rgba(99,102,241,0.3)', text_color: '#fff', shadow: 'xl',
      effect_color: '', effect_intensity: 'medium', effect_speed: 600,
      wow_disable: false, wow_backdrop_blur: 0, wow_backdrop_saturate: 100, wow_border_style: 'solid', wow_font_family: 'inherit', wow_rotation: 0, wow_perspective: 1200, wow_tilt_x: -2, wow_glow_pulse: false, wow_title_glow: false, wow_scanlines: false, wow_terminal_prompt: false },
  },
  proslider: {
    'cinema-hero':     { autoplay: true,  autoplaySpeed: 6000, transition: 'fade',  transitionDuration: 1000 },
    'magazine-cover':  { autoplay: true,  autoplaySpeed: 7000, transition: 'fade',  transitionDuration: 800 },
    'editorial-split': { autoplay: false, autoplaySpeed: 5000, transition: 'slide', transitionDuration: 700 },
    'minimal-clean':   { autoplay: false, autoplaySpeed: 5000, transition: 'slide', transitionDuration: 500 },
    'product-showcase':{ autoplay: true,  autoplaySpeed: 4500, transition: 'slide', transitionDuration: 600 },
    'glass-overlay':   { autoplay: true,  autoplaySpeed: 5500, transition: 'fade',  transitionDuration: 900 },
    'neon-tron':       { autoplay: true,  autoplaySpeed: 4000, transition: 'zoom',  transitionDuration: 700 },
    'brutalist-mega':  { autoplay: false, autoplaySpeed: 5000, transition: 'slide', transitionDuration: 400 },
    'gradient-aurora': { autoplay: true,  autoplaySpeed: 6000, transition: 'fade',  transitionDuration: 1200 },
    'sticker-fun':     { autoplay: true,  autoplaySpeed: 4500, transition: 'slide', transitionDuration: 600 },
    'retro-vhs':       { autoplay: true,  autoplaySpeed: 4000, transition: 'slide', transitionDuration: 500 },
    'tilt-parallax':   { autoplay: true,  autoplaySpeed: 5500, transition: 'zoom',  transitionDuration: 1000 },
  },
  table: {
    'modern-clean':       { has_header: true, striped: true,  bordered: true,  compact: false, header_bg: '#0ea5e9', header_text_color: '#fff', text_color: '#1f2937', border_color: '#e5e7eb', even_row_bg: '#f9fafb', shadow: 'sm' },
    'magazine-editorial': { has_header: true, striped: false, bordered: false, compact: false, header_bg: '#000',   header_text_color: '#fff', text_color: '#111',    border_color: '#000',    even_row_bg: '',        shadow: 'none' },
    'minimal-line':       { has_header: true, striped: false, bordered: false, compact: true,  header_bg: '',       header_text_color: '#111', text_color: '#374151', border_color: '#e5e7eb', even_row_bg: '',        shadow: 'none' },
    'striped-classic':    { has_header: true, striped: true,  bordered: true,  compact: false, header_bg: '#1f2937',header_text_color: '#fff', text_color: '#1f2937', border_color: '#e5e7eb', even_row_bg: '#f3f4f6', shadow: 'none' },
    'compact-data':       { has_header: true, striped: false, bordered: true,  compact: true,  header_bg: '#f9fafb',header_text_color: '#1f2937', text_color: '#374151', border_color: '#e5e7eb', even_row_bg: '',     shadow: 'none' },
    'glass-tier':         { has_header: true, striped: true,  bordered: false, compact: false, header_bg: 'rgba(255,255,255,0.2)', header_text_color: '#fff', text_color: '#fff', border_color: 'rgba(255,255,255,0.2)', even_row_bg: 'rgba(255,255,255,0.05)', shadow: 'lg' },
    'neon-grid':          { has_header: true, striped: false, bordered: true,  compact: false, header_bg: '#0a0a0a',header_text_color: '#00ffff', text_color: '#00ffff', border_color: '#00ffff', even_row_bg: '#0a0a0a', shadow: 'lg' },
    'brutalist-stamp':    { has_header: true, striped: false, bordered: true,  compact: false, header_bg: '#fde047',header_text_color: '#000', text_color: '#000',  border_color: '#000', even_row_bg: '',          shadow: 'xl' },
    'gradient-soft':      { has_header: true, striped: true,  bordered: false, compact: false, header_bg: 'linear-gradient(135deg,#a855f7,#ec4899)', header_text_color: '#fff', text_color: '#312e81', border_color: '#e0e7ff', even_row_bg: '#faf5ff', shadow: 'md' },
    'sticker-fun':        { has_header: true, striped: true,  bordered: true,  compact: false, header_bg: '#fef3c7',header_text_color: '#78350f', text_color: '#374151', border_color: '#fbbf24', even_row_bg: '#fffbeb', shadow: 'md' },
    'retro-terminal':     { has_header: true, striped: false, bordered: true,  compact: true,  header_bg: '#0a0a0a',header_text_color: '#22c55e', text_color: '#22c55e', border_color: '#22c55e', even_row_bg: '#0a0a0a', shadow: 'none' },
    'tilt-card':          { has_header: true, striped: true,  bordered: false, compact: false, header_bg: '#6366f1',header_text_color: '#fff', text_color: '#1e293b', border_color: '#c7d2fe', even_row_bg: '#eef2ff', shadow: 'lg' },
  },
  counter: {
    'modern-bold':       { number_font_size: '56', number_font_weight: '800', label_font_size: '14', label_font_weight: '500', text_color: '#0f172a', label_color: '#64748b', bg_color: '', border_radius: '12', shadow: 'sm' },
    'minimal-thin':      { number_font_size: '48', number_font_weight: '300', label_font_size: '13', label_font_weight: '400', text_color: '#1f2937', label_color: '#9ca3af', bg_color: '', border_radius: '0', shadow: 'none' },
    'magazine-editorial':{ number_font_size: '64', number_font_weight: '900', label_font_size: '12', label_font_weight: '600', text_color: '#111', label_color: '#666', bg_color: '', border_radius: '0', shadow: 'none' },
    'centered-circle':   { number_font_size: '40', number_font_weight: '700', label_font_size: '14', text_color: '#fff', label_color: '#fff', bg_color: '#0ea5e9', border_radius: '999', shadow: 'lg', tile_padding: { top: 48, right: 48, bottom: 48, left: 48 } },
    'highlight-box':     { number_font_size: '52', number_font_weight: '800', label_font_size: '14', text_color: '#fff', label_color: 'rgba(255,255,255,0.8)', bg_color: '#7c3aed', border_radius: '16', shadow: 'lg' },
    'glass-card':        { number_font_size: '52', number_font_weight: '700', label_font_size: '14', text_color: '#fff', label_color: 'rgba(255,255,255,0.85)', bg_color: 'rgba(255,255,255,0.15)', border_radius: '20', shadow: 'lg' },
    'neon-glow':         { number_font_size: '60', number_font_weight: '700', label_font_size: '13', text_color: '#00ffff', label_color: '#00ffff', bg_color: '#0a0a0a', border_radius: '8', shadow: 'lg' },
    'brutalist-mega':    { number_font_size: '80', number_font_weight: '900', label_font_size: '12', label_font_weight: '700', text_color: '#000', label_color: '#000', bg_color: '#fde047', border_radius: '0', shadow: 'xl' },
    'gradient-aurora':   { number_font_size: '56', number_font_weight: '800', label_font_size: '14', text_color: '#fff', label_color: '#fff', bg_type: 'gradient', bg_color: 'linear-gradient(135deg,#a855f7,#ec4899)', border_radius: '20', shadow: 'lg' },
    'sticker-badge':     { number_font_size: '44', number_font_weight: '800', label_font_size: '12', text_color: '#7c2d12', label_color: '#7c2d12', bg_color: '#fef3c7', border_radius: '999', shadow: 'md' },
    'retro-digital':     { number_font_size: '56', number_font_weight: '700', label_font_size: '12', text_color: '#22c55e', label_color: '#22c55e', bg_color: '#0a0a0a', border_radius: '4', shadow: 'none' },
    'tilt-3d':           { number_font_size: '52', number_font_weight: '800', label_font_size: '14', text_color: '#fff', label_color: '#fff', bg_color: '#6366f1', border_radius: '16', shadow: 'xl' },
  },
  countercircle: {
    'modern-clean':     { size: '160', stroke_width: '10', stroke_color: '#0ea5e9', track_color: '#e0f2fe', text_color: '#0c4a6e', title_color: '#64748b', title_position: 'below' },
    'minimal-thin':     { size: '140', stroke_width: '4',  stroke_color: '#1f2937', track_color: '#f3f4f6', text_color: '#1f2937', title_color: '#9ca3af', title_position: 'below' },
    'magazine-bold':    { size: '180', stroke_width: '14', stroke_color: '#111',    track_color: '#e5e7eb', text_color: '#111',    title_color: '#666',    title_position: 'below' },
    'gauge-meter':      { size: '180', stroke_width: '18', stroke_color: '#22c55e', track_color: '#dcfce7', text_color: '#14532d', title_color: '#15803d', title_position: 'inside' },
    'centered-large':   { size: '220', stroke_width: '12', stroke_color: '#7c3aed', track_color: '#ede9fe', text_color: '#4c1d95', title_color: '#7c3aed', title_position: 'inside' },
    'glass-ring':       { size: '180', stroke_width: '8',  stroke_color: '#fff',    track_color: 'rgba(255,255,255,0.2)', text_color: '#fff', title_color: 'rgba(255,255,255,0.85)', title_position: 'inside' },
    'neon-pulse':       { size: '180', stroke_width: '6',  stroke_color: '#00ffff', track_color: '#1e293b', text_color: '#00ffff', title_color: '#00ffff', title_position: 'inside' },
    'brutalist-arc':    { size: '200', stroke_width: '20', stroke_color: '#000',    track_color: '#fde047', text_color: '#000',    title_color: '#000',    title_position: 'below' },
    'gradient-rainbow': { size: '180', stroke_width: '12', stroke_color: '#ec4899', track_color: '#fce7f3', text_color: '#831843', title_color: '#be185d', title_position: 'below' },
    'sticker-badge':    { size: '140', stroke_width: '10', stroke_color: '#f59e0b', track_color: '#fef3c7', text_color: '#78350f', title_color: '#92400e', title_position: 'below' },
    'retro-dial':       { size: '180', stroke_width: '8',  stroke_color: '#22c55e', track_color: '#0a0a0a', text_color: '#22c55e', title_color: '#22c55e', title_position: 'inside' },
    'tilt-3d':          { size: '180', stroke_width: '12', stroke_color: '#6366f1', track_color: '#e0e7ff', text_color: '#312e81', title_color: '#4338ca', title_position: 'below' },
  },
  progress: {
    'modern-clean':    { height: '20', bar_color: '#0ea5e9', bar_bg: '#e0f2fe', text_color: '#0c4a6e', shadow: 'none', show_percentage: true },
    'minimal-thin':    { height: '4',  bar_color: '#1f2937', bar_bg: '#f3f4f6', text_color: '#374151', shadow: 'none', show_percentage: false },
    'thick-bold':      { height: '40', bar_color: '#111',    bar_bg: '#e5e7eb', text_color: '#fff',    shadow: 'md',   show_percentage: true },
    'gradient-bar':    { height: '24', bar_color: 'linear-gradient(90deg,#06b6d4,#3b82f6)', bar_bg: '#f3f4f6', text_color: '#fff', shadow: 'sm', show_percentage: true },
    'segmented':       { height: '18', bar_color: '#22c55e', bar_bg: '#dcfce7', text_color: '#14532d', shadow: 'none', show_percentage: true },
    'glass-bar':       { height: '20', bar_color: 'rgba(255,255,255,0.85)', bar_bg: 'rgba(255,255,255,0.1)', text_color: '#fff', shadow: 'lg', show_percentage: true },
    'neon-pulse':      { height: '16', bar_color: '#00ffff', bar_bg: '#0a0a0a', text_color: '#00ffff', shadow: 'lg', show_percentage: true },
    'brutalist-block': { height: '32', bar_color: '#000',    bar_bg: '#fde047', text_color: '#000',    shadow: 'xl', show_percentage: true },
    'gradient-aurora': { height: '24', bar_color: 'linear-gradient(90deg,#a855f7,#ec4899,#f59e0b)', bar_bg: '#f3f4f6', text_color: '#fff', shadow: 'md', show_percentage: true },
    'sticker-fill':    { height: '24', bar_color: '#f59e0b', bar_bg: '#fef3c7', text_color: '#78350f', shadow: 'md', show_percentage: true },
    'retro-segments':  { height: '20', bar_color: '#22c55e', bar_bg: '#0a0a0a', text_color: '#22c55e', shadow: 'none', show_percentage: true },
    'tilt-3d':         { height: '22', bar_color: '#6366f1', bar_bg: '#e0e7ff', text_color: '#312e81', shadow: 'lg', show_percentage: true },
  },
  progresstracker: {
    'modern-clean':    { layout: 'horizontal', connector_style: 'line', circle_size: '40', completed_color: '#22c55e', active_color: '#0ea5e9', pending_color: '#e5e7eb', text_color: '#1f2937', connector_color: '#e5e7eb', shadow: 'none' },
    'minimal-line':    { layout: 'horizontal', connector_style: 'line', circle_size: '24', completed_color: '#1f2937', active_color: '#1f2937', pending_color: '#d1d5db', text_color: '#374151', connector_color: '#d1d5db', shadow: 'none' },
    'magazine-stepped':{ layout: 'horizontal', connector_style: 'dashed', circle_size: '48', completed_color: '#000', active_color: '#000', pending_color: '#e5e7eb', text_color: '#000', connector_color: '#000', shadow: 'none' },
    'circle-numbered': { layout: 'horizontal', connector_style: 'line', circle_size: '52', completed_color: '#7c3aed', active_color: '#a855f7', pending_color: '#e5e7eb', text_color: '#4c1d95', connector_color: '#e5e7eb', shadow: 'sm' },
    'card-rows':       { layout: 'vertical',   connector_style: 'line', circle_size: '36', completed_color: '#0ea5e9', active_color: '#0ea5e9', pending_color: '#e5e7eb', text_color: '#0c4a6e', connector_color: '#e5e7eb', shadow: 'md' },
    'glass-tier':      { layout: 'horizontal', connector_style: 'line', circle_size: '44', completed_color: 'rgba(255,255,255,0.9)', active_color: '#fff', pending_color: 'rgba(255,255,255,0.3)', text_color: '#fff', connector_color: 'rgba(255,255,255,0.3)', shadow: 'lg' },
    'neon-trail':      { layout: 'horizontal', connector_style: 'line', circle_size: '36', completed_color: '#00ffff', active_color: '#ff00ff', pending_color: '#1e293b', text_color: '#00ffff', connector_color: '#00ffff', shadow: 'lg' },
    'brutalist-block': { layout: 'horizontal', connector_style: 'line', circle_size: '56', completed_color: '#000', active_color: '#fde047', pending_color: '#e5e7eb', text_color: '#000', connector_color: '#000', shadow: 'xl' },
    'gradient-flow':   { layout: 'horizontal', connector_style: 'line', circle_size: '44', completed_color: '#a855f7', active_color: '#ec4899', pending_color: '#e0e7ff', text_color: '#312e81', connector_color: '#c7d2fe', shadow: 'md' },
    'sticker-steps':   { layout: 'horizontal', connector_style: 'dashed', circle_size: '44', completed_color: '#f59e0b', active_color: '#fbbf24', pending_color: '#fef3c7', text_color: '#78350f', connector_color: '#fbbf24', shadow: 'md' },
    'retro-checklist': { layout: 'vertical',   connector_style: 'dashed', circle_size: '36', completed_color: '#22c55e', active_color: '#22c55e', pending_color: '#374151', text_color: '#22c55e', connector_color: '#22c55e', shadow: 'none' },
    'tilt-cards':      { layout: 'horizontal', connector_style: 'line', circle_size: '44', completed_color: '#6366f1', active_color: '#7c3aed', pending_color: '#e0e7ff', text_color: '#312e81', connector_color: '#c7d2fe', shadow: 'lg' },
  },
  postnavigation: {
    'modern-clean':    { layout: 'side-by-side', show_thumbnail: true,  show_label: true, show_title: true, gap: '20', thumbnail_size: '60',  border_radius: '12',  background_color: '#fff',    text_color: '#1f2937', link_color: '#0ea5e9', hover_color: '#0284c7', shadow: 'sm' },
    'minimal-arrows':  { layout: 'side-by-side', show_thumbnail: false, show_label: true, show_title: true, gap: '12', thumbnail_size: '0',   border_radius: '0',   background_color: '',        text_color: '#374151', link_color: '#111',    hover_color: '#0ea5e9', shadow: 'none' },
    'magazine-cards':  { layout: 'side-by-side', show_thumbnail: true,  show_label: true, show_title: true, gap: '24', thumbnail_size: '100', border_radius: '8',   background_color: '#f9fafb', text_color: '#111',    link_color: '#000',    hover_color: '#0ea5e9', shadow: 'md' },
    'centered-thumbs': { layout: 'stacked',      show_thumbnail: true,  show_label: true, show_title: true, gap: '16', thumbnail_size: '120', border_radius: '12',  background_color: '#fff',    text_color: '#1f2937', link_color: '#7c3aed', hover_color: '#6d28d9', shadow: 'lg' },
    'split-bold':      { layout: 'side-by-side', show_thumbnail: true,  show_label: true, show_title: true, gap: '0',  thumbnail_size: '80',  border_radius: '0',   background_color: '#1e293b', text_color: '#fff',    link_color: '#fff',    hover_color: '#0ea5e9', shadow: 'none' },
    'glass-pill':      { layout: 'side-by-side', show_thumbnail: false, show_label: true, show_title: true, gap: '16', thumbnail_size: '0',   border_radius: '999', background_color: 'rgba(255,255,255,0.15)', text_color: '#fff', link_color: '#fff', hover_color: 'rgba(255,255,255,0.7)', shadow: 'lg' },
    'neon-arrows':     { layout: 'side-by-side', show_thumbnail: false, show_label: true, show_title: true, gap: '20', thumbnail_size: '0',   border_radius: '4',   background_color: '#0a0a0a', text_color: '#00ffff', link_color: '#00ffff', hover_color: '#ff00ff', shadow: 'lg' },
    'brutalist-block': { layout: 'side-by-side', show_thumbnail: true,  show_label: true, show_title: true, gap: '0',  thumbnail_size: '80',  border_radius: '0',   background_color: '#fde047', text_color: '#000',    link_color: '#000',    hover_color: '#000',    shadow: 'xl' },
    'gradient-flow':   { layout: 'side-by-side', show_thumbnail: true,  show_label: true, show_title: true, gap: '20', thumbnail_size: '80',  border_radius: '16',  background_color: 'linear-gradient(135deg,#a855f7,#ec4899)', text_color: '#fff', link_color: '#fff', hover_color: '#fbbf24', shadow: 'md' },
    'sticker-pages':   { layout: 'side-by-side', show_thumbnail: true,  show_label: true, show_title: true, gap: '16', thumbnail_size: '70',  border_radius: '12',  background_color: '#fef3c7', text_color: '#78350f', link_color: '#92400e', hover_color: '#7c2d12', shadow: 'md' },
    'retro-terminal':  { layout: 'side-by-side', show_thumbnail: false, show_label: true, show_title: true, gap: '12', thumbnail_size: '0',   border_radius: '0',   background_color: '#0a0a0a', text_color: '#22c55e', link_color: '#22c55e', hover_color: '#16a34a', shadow: 'none' },
    'tilt-cards':      { layout: 'side-by-side', show_thumbnail: true,  show_label: true, show_title: true, gap: '20', thumbnail_size: '80',  border_radius: '12',  background_color: '#6366f1', text_color: '#fff',    link_color: '#fff',    hover_color: '#fbbf24', shadow: 'xl' },
  },
  toc: {
    'modern-clean':      { font_size: '15', text_color: '#1f2937', link_color: '#2563eb', title_color: '#111', list_style: 'numbered', indent: '20', shadow: 'sm' },
    'minimal-mono':      { font_size: '13', text_color: '#374151', link_color: '#111', title_color: '#111', list_style: 'bullets', indent: '12', shadow: 'none' },
    'magazine-numbered': { font_size: '16', text_color: '#111', link_color: '#111', title_color: '#000', list_style: 'numbered', indent: '24', shadow: 'none' },
    'sticky-rail':       { font_size: '13', text_color: '#475569', link_color: '#0ea5e9', title_color: '#0c4a6e', list_style: 'bullets', indent: '16', sticky: true, shadow: 'none' },
    'floating-card':     { font_size: '14', text_color: '#1f2937', link_color: '#7c3aed', title_color: '#4c1d95', list_style: 'numbered', indent: '20', shadow: 'lg' },
    'glass-tier':        { font_size: '14', text_color: '#fff', link_color: 'rgba(255,255,255,0.85)', title_color: '#fff', list_style: 'numbered', indent: '20', shadow: 'lg' },
    'neon-list':         { font_size: '14', text_color: '#00ffff', link_color: '#00ffff', title_color: '#00ffff', list_style: 'bullets', indent: '18', shadow: 'lg' },
    'brutalist-block':   { font_size: '16', text_color: '#000', link_color: '#000', title_color: '#000', list_style: 'numbered', indent: '28', shadow: 'xl' },
    'gradient-flow':     { font_size: '15', text_color: '#312e81', link_color: '#7c3aed', title_color: '#4c1d95', list_style: 'numbered', indent: '22', shadow: 'md' },
    'sticky-notes':      { font_size: '14', text_color: '#78350f', link_color: '#92400e', title_color: '#713f12', list_style: 'bullets', indent: '16', shadow: 'md' },
    'retro-terminal':    { font_size: '14', text_color: '#22c55e', link_color: '#22c55e', title_color: '#22c55e', list_style: 'numbered', indent: '20', shadow: 'none' },
    'tilt-cards':        { font_size: '14', text_color: '#1e293b', link_color: '#6366f1', title_color: '#312e81', list_style: 'numbered', indent: '20', shadow: 'lg' },
  },
  breadcrumbs: {
    'modern-clean':    { separator: '/', shadow: 'none' },
    'minimal-mono':    { separator: '·', shadow: 'none' },
    'magazine-bold':   { separator: '|', shadow: 'none' },
    'pill-rounded':    { separator: '›', shadow: 'sm' },
    'arrow-style':     { separator: '→', shadow: 'none' },
    'glass-pill':      { separator: '›', shadow: 'md' },
    'neon-trail':      { separator: '›', shadow: 'lg' },
    'brutalist-mono':  { separator: '/', shadow: 'xl' },
    'gradient-link':   { separator: '›', shadow: 'sm' },
    'sticker-tags':    { separator: '›', shadow: 'md' },
    'retro-terminal':  { separator: '>', shadow: 'none' },
    'tilt-pills':      { separator: '›', shadow: 'lg' },
  },
  pagination: {
    'modern-pills':    { border_radius: '999', background_color: '#fff', active_background: '#0ea5e9', text_color: '#0f172a', active_text_color: '#fff', border_color: '#e5e7eb', border_width: '1', font_size: '14' },
    'minimal-thin':    { border_radius: '0',   background_color: '',     active_background: '',        text_color: '#1f2937', active_text_color: '#111', border_color: '#e5e7eb', border_width: '0', font_size: '13' },
    'magazine-bold':   { border_radius: '0',   background_color: '#111', active_background: '#000',    text_color: '#fff',    active_text_color: '#fde047', border_color: '#000', border_width: '0', font_size: '16' },
    'circle-numbers':  { border_radius: '999', background_color: '#f3f4f6', active_background: '#7c3aed', text_color: '#374151', active_text_color: '#fff', border_color: 'transparent', border_width: '0', font_size: '14' },
    'compact-text':    { border_radius: '4',   background_color: '',     active_background: '#0ea5e9', text_color: '#374151', active_text_color: '#fff', border_color: '#e5e7eb', border_width: '0', font_size: '13' },
    'glass-pills':     { border_radius: '999', background_color: 'rgba(255,255,255,0.15)', active_background: 'rgba(255,255,255,0.35)', text_color: '#fff', active_text_color: '#fff', border_color: 'rgba(255,255,255,0.3)', border_width: '1', font_size: '14' },
    'neon-numbers':    { border_radius: '4',   background_color: '#0a0a0a', active_background: '#00ffff', text_color: '#00ffff', active_text_color: '#0a0a0a', border_color: '#00ffff', border_width: '1', font_size: '14' },
    'brutalist-block': { border_radius: '0',   background_color: '#fde047', active_background: '#000', text_color: '#000', active_text_color: '#fde047', border_color: '#000', border_width: '3', font_size: '16' },
    'gradient-pills':  { border_radius: '999', background_color: '#fff', active_background: 'linear-gradient(135deg,#a855f7,#ec4899)', text_color: '#374151', active_text_color: '#fff', border_color: '#e5e7eb', border_width: '1', font_size: '14' },
    'sticker-pages':   { border_radius: '8',   background_color: '#fef3c7', active_background: '#f59e0b', text_color: '#78350f', active_text_color: '#fff', border_color: '#fbbf24', border_width: '2', font_size: '14' },
    'retro-terminal':  { border_radius: '0',   background_color: '#0a0a0a', active_background: '#22c55e', text_color: '#22c55e', active_text_color: '#0a0a0a', border_color: '#22c55e', border_width: '1', font_size: '14' },
    'tilt-3d':         { border_radius: '8',   background_color: '#6366f1', active_background: '#312e81', text_color: '#fff', active_text_color: '#fff', border_color: 'transparent', border_width: '0', font_size: '14' },
  },
  tagcloud: {
    'cloud-weighted': { min_font: '12', max_font: '32', max_tags: '40' },
    'pills-uniform':  { min_font: '14', max_font: '14', max_tags: '30' },
    'minimal-line':   { min_font: '12', max_font: '14', max_tags: '20' },
    'magazine-tags':  { min_font: '14', max_font: '24', max_tags: '24' },
    'compact-chips':  { min_font: '11', max_font: '13', max_tags: '50' },
    'glass-pills':    { min_font: '13', max_font: '15', max_tags: '24' },
    'neon-tags':      { min_font: '12', max_font: '16', max_tags: '30' },
    'brutalist-stamp':{ min_font: '14', max_font: '20', max_tags: '20' },
    'gradient-tags':  { min_font: '13', max_font: '18', max_tags: '28' },
    'sticky-notes':   { min_font: '13', max_font: '16', max_tags: '20' },
    'retro-terminal': { min_font: '12', max_font: '14', max_tags: '30' },
    'tilt-3d':        { min_font: '13', max_font: '18', max_tags: '24' },
  },
  starrating: {
    // v1.0.58 — aggiunti title_color/subtitle_color (era "4 / 5" orange brand ratio 1.67 fail su 12/12)
    'classic-stars':   { star_size: '32', star_color: '#fbbf24', empty_color: '#e5e7eb', style: 'filled',  title_color: '#0f172a', subtitle_color: '#475569' },
    'minimal-line':    { star_size: '20', star_color: '#1f2937', empty_color: '#d1d5db', style: 'outline', title_color: '#1f2937', subtitle_color: '#4b5563' },
    'compact-numeric': { star_size: '18', star_color: '#0ea5e9', empty_color: '#e5e7eb', style: 'filled',  title_color: '#0f172a', subtitle_color: '#475569' },
    'hearts-pink':     { star_size: '32', star_color: '#ec4899', empty_color: '#fce7f3', style: 'filled',  title_color: '#831843', subtitle_color: '#9d174d' },
    'diamonds-luxury': { star_size: '28', star_color: '#06b6d4', empty_color: '#cffafe', style: 'filled',  title_color: '#0e7490', subtitle_color: '#155e75' },
    'glass-stars':     { star_size: '32', star_color: 'rgba(255,255,255,0.95)', empty_color: 'rgba(255,255,255,0.2)', style: 'filled', bg_color: 'rgba(15,23,42,0.85)', title_color: '#ffffff', subtitle_color: '#e5e7eb' },
    'neon-glow':       { star_size: '32', star_color: '#00ffff', empty_color: '#1e293b', style: 'filled',  bg_color: '#0a0a0a', title_color: '#00ffff', subtitle_color: '#a5f3fc' },
    'brutalist-stamp': { star_size: '40', star_color: '#000',    empty_color: '#fde047', style: 'filled',  bg_color: '#fde047', title_color: '#000', subtitle_color: '#1f2937' },
    'gradient-rainbow':{ star_size: '32', star_color: '#a855f7', empty_color: '#fce7f3', style: 'filled',  title_color: '#7e22ce', subtitle_color: '#9333ea' },
    'sticker-stars':   { star_size: '36', star_color: '#f59e0b', empty_color: '#fef3c7', style: 'filled',  title_color: '#78350f', subtitle_color: '#92400e' },
    'retro-arcade':    { star_size: '28', star_color: '#22c55e', empty_color: '#0a0a0a', style: 'filled',  bg_color: '#0c0c0c', title_color: '#22c55e', subtitle_color: '#86efac' },
    'tilt-3d':         { star_size: '32', star_color: '#6366f1', empty_color: '#e0e7ff', style: 'filled',  title_color: '#312e81', subtitle_color: '#4338ca' },
  },
  sharebuttons: {
    'modern-pills':    { style: 'icon-label', size: '40', gap: '10', alignment: 'left',   icon_color: '#fff', bg_color: '#0ea5e9' },
    'icon-only':       { style: 'icon-only',  size: '36', gap: '8',  alignment: 'left',   icon_color: '#1f2937', bg_color: '#f3f4f6' },
    'minimal-line':    { style: 'icon-label', size: '32', gap: '12', alignment: 'left',   icon_color: '#1f2937', bg_color: '' },
    'circle-icons':    { style: 'icon-only',  size: '44', gap: '10', alignment: 'center', icon_color: '#fff', bg_color: '#7c3aed' },
    'magazine-row':    { style: 'icon-label', size: '36', gap: '16', alignment: 'left',   icon_color: '#000', bg_color: '#fde047' },
    'glass-pills':     { style: 'icon-label', size: '40', gap: '10', alignment: 'center', icon_color: '#fff', bg_color: 'rgba(255,255,255,0.15)' },
    'neon-glow':       { style: 'icon-only',  size: '40', gap: '12', alignment: 'center', icon_color: '#00ffff', bg_color: '#0a0a0a' },
    'brutalist-block': { style: 'icon-label', size: '44', gap: '0',  alignment: 'left',   icon_color: '#fde047', bg_color: '#000' },
    'gradient-pills':  { style: 'icon-label', size: '40', gap: '10', alignment: 'center', icon_color: '#fff', bg_color: 'linear-gradient(135deg,#a855f7,#ec4899)' },
    'sticker-fun':     { style: 'icon-only',  size: '40', gap: '12', alignment: 'left',   icon_color: '#78350f', bg_color: '#fef3c7' },
    'retro-vhs':       { style: 'icon-label', size: '36', gap: '8',  alignment: 'left',   icon_color: '#22c55e', bg_color: '#0a0a0a' },
    'tilt-3d':         { style: 'icon-only',  size: '44', gap: '10', alignment: 'center', icon_color: '#fff', bg_color: '#6366f1' },
  },
  viewscounter: {
    'modern-clean':    { font_size: '14', font_weight: '500', text_color: '#0c4a6e', icon_color: '#0ea5e9', icon_position: 'before', show_icon: true,  show_label: true },
    'minimal-mono':    { font_size: '12', font_weight: '400', text_color: '#6b7280', icon_color: '#9ca3af', icon_position: 'before', show_icon: false, show_label: true },
    'magazine-pill':   { font_size: '13', font_weight: '700', text_color: '#fff',    icon_color: '#fff',    icon_position: 'before', show_icon: true,  show_label: true },
    'badge-floating':  { font_size: '12', font_weight: '600', text_color: '#fff',    icon_color: '#fff',    icon_position: 'before', show_icon: true,  show_label: false },
    'compact-icon':    { font_size: '13', font_weight: '500', text_color: '#374151', icon_color: '#7c3aed', icon_position: 'before', show_icon: true,  show_label: false },
    'glass-pill':      { font_size: '13', font_weight: '500', text_color: '#fff',    icon_color: '#fff',    icon_position: 'before', show_icon: true,  show_label: true },
    'neon-glow':       { font_size: '13', font_weight: '600', text_color: '#00ffff', icon_color: '#00ffff', icon_position: 'before', show_icon: true,  show_label: true },
    'brutalist-stamp': { font_size: '14', font_weight: '900', text_color: '#000',    icon_color: '#000',    icon_position: 'before', show_icon: true,  show_label: true },
    'gradient-soft':   { font_size: '13', font_weight: '500', text_color: '#312e81', icon_color: '#a855f7', icon_position: 'before', show_icon: true,  show_label: true },
    'sticker-badge':   { font_size: '12', font_weight: '700', text_color: '#78350f', icon_color: '#92400e', icon_position: 'before', show_icon: true,  show_label: false },
    'retro-digital':   { font_size: '13', font_weight: '500', text_color: '#22c55e', icon_color: '#22c55e', icon_position: 'before', show_icon: true,  show_label: true },
    'tilt-3d':         { font_size: '13', font_weight: '600', text_color: '#fff',    icon_color: '#fbbf24', icon_position: 'before', show_icon: true,  show_label: true },
  },
  countdown: {
    'modern-digital':  { number_font_size: '56', number_font_weight: '700', label_font_size: '12', separator_font_size: '32', bg_color: '#0f172a', text_color: '#fff', accent_color: '#0ea5e9', shadow: 'lg' },
    'minimal-clean':   { number_font_size: '44', number_font_weight: '300', label_font_size: '11', separator_font_size: '28', bg_color: '', text_color: '#1f2937', accent_color: '#6b7280', shadow: 'none' },
    'magazine-bold':   { number_font_size: '72', number_font_weight: '900', label_font_size: '12', separator_font_size: '40', bg_color: '', text_color: '#000', accent_color: '#666', shadow: 'none' },
    'flip-cards':      { number_font_size: '52', number_font_weight: '700', label_font_size: '11', separator_font_size: '0', bg_color: '#1e293b', text_color: '#fff', accent_color: '#fbbf24', shadow: 'lg' },
    'circle-rings':    { number_font_size: '40', number_font_weight: '600', label_font_size: '11', separator_font_size: '0', bg_color: '', text_color: '#1e293b', accent_color: '#7c3aed', shadow: 'md' },
    'glass-tier':      { number_font_size: '52', number_font_weight: '700', label_font_size: '12', separator_font_size: '32', bg_color: 'rgba(255,255,255,0.15)', text_color: '#fff', accent_color: 'rgba(255,255,255,0.85)', shadow: 'lg' },
    'neon-clock':      { number_font_size: '64', number_font_weight: '700', label_font_size: '12', separator_font_size: '40', bg_color: '#0a0a0a', text_color: '#00ffff', accent_color: '#00ffff', shadow: 'lg' },
    'brutalist-mega':  { number_font_size: '96', number_font_weight: '900', label_font_size: '14', label_font_weight: '700', separator_font_size: '48', bg_color: '#fde047', text_color: '#000', accent_color: '#000', shadow: 'xl' },
    'gradient-aurora': { number_font_size: '60', number_font_weight: '800', label_font_size: '12', separator_font_size: '36', bg_color: 'linear-gradient(135deg,#a855f7,#ec4899,#f59e0b)', text_color: '#fff', accent_color: '#fff', shadow: 'lg' },
    'sticker-fun':     { number_font_size: '48', number_font_weight: '700', label_font_size: '12', separator_font_size: '28', bg_color: '#fef3c7', text_color: '#78350f', accent_color: '#f59e0b', shadow: 'md' },
    'retro-flip':      { number_font_size: '56', number_font_weight: '700', label_font_size: '11', separator_font_size: '32', bg_color: '#0a0a0a', text_color: '#22c55e', accent_color: '#22c55e', shadow: 'none' },
    'tilt-3d':         { number_font_size: '54', number_font_weight: '800', label_font_size: '12', separator_font_size: '32', bg_color: '#6366f1', text_color: '#fff', accent_color: '#fbbf24', shadow: 'xl' },
  },
  search: {
    'modern-clean':    { style: 'default', size: 'medium', show_icon: true,  icon_position: 'left',  show_button: false },
    'minimal-line':    { style: 'underline', size: 'small', show_icon: false, icon_position: 'left',  show_button: false },
    'pill-rounded':    { style: 'rounded', size: 'medium', show_icon: true,  icon_position: 'left',  show_button: false },
    'magazine-bar':    { style: 'default', size: 'large',  show_icon: false, icon_position: 'right', show_button: true,  button_style: 'filled' },
    'with-button':     { style: 'default', size: 'medium', show_icon: true,  icon_position: 'left',  show_button: true,  button_style: 'filled' },
    'glass-floating':  { style: 'rounded', size: 'medium', show_icon: true,  icon_position: 'left',  show_button: false },
    'neon-glow':       { style: 'default', size: 'medium', show_icon: true,  icon_position: 'left',  show_button: false },
    'brutalist-stamp': { style: 'default', size: 'large',  show_icon: true,  icon_position: 'right', show_button: true,  button_style: 'filled' },
    'gradient-border': { style: 'rounded', size: 'medium', show_icon: true,  icon_position: 'left',  show_button: false },
    'sticker-fun':     { style: 'rounded', size: 'medium', show_icon: true,  icon_position: 'left',  show_button: false },
    'retro-terminal':  { style: 'default', size: 'medium', show_icon: false, icon_position: 'left',  show_button: false },
    'tilt-3d':         { style: 'rounded', size: 'medium', show_icon: true,  icon_position: 'left',  show_button: true,  button_style: 'filled' },
  },
  livesearch: {
    'modern-clean':    { mode: 'expanded' },
    'minimal-line':    { mode: 'expanded' },
    'modal-spotlight': { mode: 'modal' },
    'expanded-bar':    { mode: 'expanded' },
    'icon-trigger':    { mode: 'modal' },
    'glass-floating':  { mode: 'expanded' },
    'neon-glow':       { mode: 'expanded' },
    'brutalist-stamp': { mode: 'expanded' },
    'gradient-border': { mode: 'expanded' },
    'sticker-fun':     { mode: 'expanded' },
    'retro-terminal':  { mode: 'expanded' },
    'tilt-3d':         { mode: 'modal' },
  },
  form: {
    // v1.0.58 — preset self-contained per form: aggiunti bg_color (wrapper), input_color, submit_bg, submit_color, check_accent_color.
    // Senza questi default, sui preset DARK (glass/neon/brutalist/gradient/retro/tilt) il label/input/button risultava invisibile.
    'modern-clean':    { bg_color: '#fff', input_bg: '#fff', input_color: '#0f172a', input_border_color: '#e5e7eb', input_border_width: 1, label_color: '#374151', submit_bg: '#0ea5e9', submit_color: '#fff', check_accent_color: '#0ea5e9', check_bg: '#fff', check_border_color: '#cbd5e1' },
    'minimal-line':    { bg_color: '', input_bg: '', input_color: '#0f172a', input_border_color: '#9ca3af', input_border_width: 0, label_color: '#1f2937', submit_bg: '#0f172a', submit_color: '#fff', check_accent_color: '#0f172a', check_bg: '#fff', check_border_color: '#9ca3af' },
    'magazine-bold':   { bg_color: '#fff', input_bg: '#f9fafb', input_color: '#000', input_border_color: '#000', input_border_width: 2, label_color: '#000', submit_bg: '#000', submit_color: '#fff', check_accent_color: '#000', check_bg: '#fff', check_border_color: '#000' },
    'card-floating':   { bg_color: '#fff', input_bg: '#fff', input_color: '#0f172a', input_border_color: '#cbd5e1', input_border_width: 1, label_color: '#0f172a', submit_bg: '#0f172a', submit_color: '#fff', check_accent_color: '#0f172a', check_bg: '#fff', check_border_color: '#cbd5e1' },
    'compact-inline':  { bg_color: '#fff', input_bg: '#fff', input_color: '#374151', input_border_color: '#d1d5db', input_border_width: 1, label_color: '#374151', submit_bg: '#0ea5e9', submit_color: '#fff', check_accent_color: '#0ea5e9', check_bg: '#fff', check_border_color: '#d1d5db' },
    'glass-form':      { bg_color: 'rgba(15,23,42,0.85)', input_bg: 'rgba(255,255,255,0.15)', input_color: '#fff', input_border_color: 'rgba(255,255,255,0.3)', input_border_width: 1, label_color: '#fff', submit_bg: '#fff', submit_color: '#0f172a', check_accent_color: '#fff', check_bg: 'rgba(255,255,255,0.15)', check_border_color: '#fff' },
    'neon-cyber':      { bg_color: '#0a0a0a', input_bg: '#0a0a0a', input_color: '#00ffff', input_border_color: '#00ffff', input_border_width: 1, label_color: '#00ffff', submit_bg: '#00ffff', submit_color: '#0a0a0a', check_accent_color: '#00ffff', check_bg: '#0a0a0a', check_border_color: '#00ffff' },
    'brutalist-stamp': { bg_color: '#fde047', input_bg: '#fff', input_color: '#000', input_border_color: '#000', input_border_width: 3, label_color: '#000', submit_bg: '#000', submit_color: '#fde047', check_accent_color: '#000', check_bg: '#fff', check_border_color: '#000' },
    'gradient-aurora': { bg_color: 'linear-gradient(135deg,#a855f7,#ec4899)', input_bg: 'rgba(255,255,255,0.95)', input_color: '#312e81', input_border_color: 'transparent', input_border_width: 0, label_color: '#fff', submit_bg: '#fff', submit_color: '#a855f7', check_accent_color: '#fff', check_bg: '#fff', check_border_color: '#fff' },
    'sticker-fun':     { bg_color: '#fef3c7', input_bg: '#fff', input_color: '#78350f', input_border_color: '#fbbf24', input_border_width: 2, label_color: '#78350f', submit_bg: '#fbbf24', submit_color: '#fff', check_accent_color: '#fbbf24', check_bg: '#fff', check_border_color: '#fbbf24' },
    'retro-terminal':  { bg_color: '#0a0a0a', input_bg: '#0a0a0a', input_color: '#22c55e', input_border_color: '#22c55e', input_border_width: 1, label_color: '#22c55e', submit_bg: '#22c55e', submit_color: '#0a0a0a', check_accent_color: '#22c55e', check_bg: '#0a0a0a', check_border_color: '#22c55e' },
    'tilt-3d':         { bg_color: '#6366f1', input_bg: '#fff', input_color: '#312e81', input_border_color: '#312e81', input_border_width: 1, label_color: '#fff', submit_bg: '#312e81', submit_color: '#fff', check_accent_color: '#fff', check_bg: '#fff', check_border_color: '#312e81' },
    // Editoriale dark / studio: input "underline" trasparenti, label uppercase tracciate, CTA terracotta full-width (stile Studio Càrdo).
    'underline-dark':  { bg_color: '#211f1c', input_bg: 'transparent', input_color: '#f4efe7', input_border_color: 'rgba(244,239,231,0.22)', input_border_width: 1, input_border_style: 'underline', input_focus_border: '#c6a888', label_color: '#c6a888', label_transform: 'uppercase', label_letter_spacing: 2, submit_bg: '#bd5b38', submit_color: '#ffffff', submit_text_transform: 'uppercase', submit_letter_spacing: 1.5, submit_full_width: true, check_accent_color: '#bd5b38', check_bg: 'transparent', check_border_color: 'rgba(244,239,231,0.4)' },
  },
  newsletter: {
    // v1.0.58 — preset self-contained: aggiunti bg_color, title_color, subtitle_color, icon_color, input_*, btn_*.
    'modern-clean':    { layout: 'horizontal', icon_type: 'none',                                bg_color: '#ffffff', title_color: '#0f172a', subtitle_color: '#475569', icon_color: '#0ea5e9', input_bg: '#ffffff', input_color: '#0f172a', input_border: '#e5e7eb', btn_bg: '#0ea5e9', btn_color: '#fff', btn_hover_bg: '#0284c7' },
    'minimal-inline':  { layout: 'horizontal', icon_type: 'none',                                bg_color: '', title_color: '#0f172a', subtitle_color: '#475569', icon_color: '#0f172a', input_bg: '', input_color: '#0f172a', input_border: '#9ca3af', btn_bg: '#0f172a', btn_color: '#fff', btn_hover_bg: '#1e293b' },
    'magazine-promo':  { layout: 'vertical',   icon_type: 'icon', icon_name: 'mail',             bg_color: '#000', title_color: '#fff', subtitle_color: '#e5e7eb', icon_color: '#fff', input_bg: '#fff', input_color: '#000', input_border: '#000', btn_bg: '#fde047', btn_color: '#000', btn_hover_bg: '#fbbf24' },
    'centered-card':   { layout: 'vertical',   icon_type: 'icon', icon_name: 'mail',             bg_color: '#ffffff', title_color: '#0f172a', subtitle_color: '#475569', icon_color: '#7c3aed', input_bg: '#f9fafb', input_color: '#0f172a', input_border: '#e5e7eb', btn_bg: '#7c3aed', btn_color: '#fff', btn_hover_bg: '#6d28d9' },
    'split-image':     { layout: 'horizontal', icon_type: 'image',                               bg_color: '#fafaf9', title_color: '#0f172a', subtitle_color: '#475569', icon_color: '#0f172a', input_bg: '#ffffff', input_color: '#0f172a', input_border: '#d6d3d1', btn_bg: '#0f172a', btn_color: '#fff', btn_hover_bg: '#1e293b' },
    'glass-floating':  { layout: 'vertical',   icon_type: 'icon', icon_name: 'mail',             bg_color: 'rgba(15,23,42,0.85)', title_color: '#fff', subtitle_color: '#e5e7eb', icon_color: '#fff', input_bg: 'rgba(255,255,255,0.15)', input_color: '#fff', input_border: 'rgba(255,255,255,0.3)', btn_bg: '#ffffff', btn_color: '#0f172a', btn_hover_bg: '#f3f4f6' },
    'neon-cyber':      { layout: 'horizontal', icon_type: 'icon', icon_name: 'mail',             bg_color: '#0a0a0a', title_color: '#00ffff', subtitle_color: '#a5f3fc', icon_color: '#00ffff', input_bg: '#0a0a0a', input_color: '#00ffff', input_border: '#00ffff', btn_bg: '#00ffff', btn_color: '#0a0a0a', btn_hover_bg: '#22d3ee' },
    'brutalist-stamp': { layout: 'vertical',   icon_type: 'icon', icon_name: 'mail',             bg_color: '#fde047', title_color: '#000', subtitle_color: '#1f2937', icon_color: '#000', input_bg: '#ffffff', input_color: '#000', input_border: '#000', btn_bg: '#000', btn_color: '#fde047', btn_hover_bg: '#1f2937' },
    'gradient-aurora': { layout: 'vertical',   icon_type: 'icon', icon_name: 'mail',             bg_color: 'linear-gradient(135deg,#a855f7,#ec4899)', title_color: '#fff', subtitle_color: '#fce7f3', icon_color: '#fff', input_bg: 'rgba(255,255,255,0.95)', input_color: '#312e81', input_border: 'transparent', btn_bg: '#fff', btn_color: '#a855f7', btn_hover_bg: '#f3f4f6' },
    'sticky-note':     { layout: 'vertical',   icon_type: 'icon', icon_name: 'mail',             bg_color: '#fef3c7', title_color: '#78350f', subtitle_color: '#92400e', icon_color: '#78350f', input_bg: '#ffffff', input_color: '#78350f', input_border: '#fbbf24', btn_bg: '#fbbf24', btn_color: '#fff', btn_hover_bg: '#f59e0b' },
    'retro-zine':      { layout: 'horizontal', icon_type: 'none',                                bg_color: '#1a1a2e', title_color: '#fbbf24', subtitle_color: '#fde047', icon_color: '#fbbf24', input_bg: '#0a0a0a', input_color: '#fff', input_border: '#fbbf24', btn_bg: '#fbbf24', btn_color: '#1a1a2e', btn_hover_bg: '#f59e0b' },
    'tilt-card':       { layout: 'vertical',   icon_type: 'icon', icon_name: 'mail',             bg_color: '#6366f1', title_color: '#fff', subtitle_color: '#e0e7ff', icon_color: '#fff', input_bg: '#ffffff', input_color: '#312e81', input_border: '#312e81', btn_bg: '#312e81', btn_color: '#fff', btn_hover_bg: '#1e1b4b' },
  },
  loginform: {
    // v1.0.58 — preset self-contained: aggiunti form_bg, text_color, label_color, input_*, submit_*, link_color, icon_color.
    'modern-clean':    { mode: 'login', show_remember_me: true,  show_lost_password: true,  show_avatar: true,  form_bg: '#ffffff', text_color: '#0f172a', label_color: '#374151', input_bg: '#ffffff', input_color: '#0f172a', input_border_color: '#e5e7eb', submit_bg: '#0ea5e9', submit_color: '#fff', submit_hover_bg: '#0284c7', link_color: '#0ea5e9', icon_color: '#64748b', border_color: '#e5e7eb' },
    'minimal-line':    { mode: 'login', show_remember_me: false, show_lost_password: true,  show_avatar: false, form_bg: '', text_color: '#0f172a', label_color: '#1f2937', input_bg: '', input_color: '#0f172a', input_border_color: '#9ca3af', submit_bg: '#0f172a', submit_color: '#fff', submit_hover_bg: '#1e293b', link_color: '#0f172a', icon_color: '#64748b', border_color: '' },
    'card-floating':   { mode: 'login', show_remember_me: true,  show_lost_password: true,  show_avatar: true,  form_bg: '#ffffff', text_color: '#0f172a', label_color: '#0f172a', input_bg: '#ffffff', input_color: '#0f172a', input_border_color: '#cbd5e1', submit_bg: '#0f172a', submit_color: '#fff', submit_hover_bg: '#1e293b', link_color: '#0f172a', icon_color: '#64748b', border_color: '#e5e7eb' },
    'split-image':     { mode: 'login', show_remember_me: true,  show_lost_password: true,  show_avatar: false, form_bg: '#fafaf9', text_color: '#0f172a', label_color: '#374151', input_bg: '#ffffff', input_color: '#0f172a', input_border_color: '#d6d3d1', submit_bg: '#0f172a', submit_color: '#fff', submit_hover_bg: '#1e293b', link_color: '#0f172a', icon_color: '#78716c', border_color: '#e7e5e4' },
    'centered-modal':  { mode: 'login', show_remember_me: true,  show_lost_password: true,  show_avatar: true,  form_bg: '#ffffff', text_color: '#0f172a', label_color: '#374151', input_bg: '#f9fafb', input_color: '#0f172a', input_border_color: '#e5e7eb', submit_bg: '#7c3aed', submit_color: '#fff', submit_hover_bg: '#6d28d9', link_color: '#7c3aed', icon_color: '#64748b', border_color: '#e5e7eb' },
    'glass-frosted':   { mode: 'login', show_remember_me: true,  show_lost_password: true,  show_avatar: true,  form_bg: 'rgba(15,23,42,0.85)', text_color: '#fff', label_color: '#fff', input_bg: 'rgba(255,255,255,0.15)', input_color: '#fff', input_border_color: 'rgba(255,255,255,0.3)', submit_bg: '#ffffff', submit_color: '#0f172a', submit_hover_bg: '#f3f4f6', link_color: '#fff', icon_color: '#e5e7eb', border_color: 'rgba(255,255,255,0.2)' },
    'neon-cyber':      { mode: 'login', show_remember_me: true,  show_lost_password: true,  show_avatar: false, form_bg: '#0a0a0a', text_color: '#00ffff', label_color: '#00ffff', input_bg: '#0a0a0a', input_color: '#00ffff', input_border_color: '#00ffff', submit_bg: '#00ffff', submit_color: '#0a0a0a', submit_hover_bg: '#22d3ee', link_color: '#00ffff', icon_color: '#00ffff', border_color: '#00ffff' },
    'brutalist-stamp': { mode: 'login', show_remember_me: false, show_lost_password: true,  show_avatar: false, form_bg: '#fde047', text_color: '#000', label_color: '#000', input_bg: '#ffffff', input_color: '#000', input_border_color: '#000', submit_bg: '#000', submit_color: '#fde047', submit_hover_bg: '#1f2937', link_color: '#000', icon_color: '#000', border_color: '#000' },
    'gradient-aurora': { mode: 'login', show_remember_me: true,  show_lost_password: true,  show_avatar: true,  form_bg: 'linear-gradient(135deg,#a855f7,#ec4899)', text_color: '#fff', label_color: '#fff', input_bg: 'rgba(255,255,255,0.95)', input_color: '#312e81', input_border_color: 'transparent', submit_bg: '#fff', submit_color: '#a855f7', submit_hover_bg: '#f3f4f6', link_color: '#fff', icon_color: '#fff', border_color: 'rgba(255,255,255,0.3)' },
    'sticky-note':     { mode: 'login', show_remember_me: false, show_lost_password: true,  show_avatar: false, form_bg: '#fef3c7', text_color: '#78350f', label_color: '#78350f', input_bg: '#ffffff', input_color: '#78350f', input_border_color: '#fbbf24', submit_bg: '#fbbf24', submit_color: '#fff', submit_hover_bg: '#f59e0b', link_color: '#92400e', icon_color: '#78350f', border_color: '#fbbf24' },
    'retro-terminal':  { mode: 'login', show_remember_me: false, show_lost_password: false, show_avatar: false, form_bg: '#0a0a0a', text_color: '#22c55e', label_color: '#22c55e', input_bg: '#0a0a0a', input_color: '#22c55e', input_border_color: '#22c55e', submit_bg: '#22c55e', submit_color: '#0a0a0a', submit_hover_bg: '#16a34a', link_color: '#22c55e', icon_color: '#22c55e', border_color: '#22c55e' },
    'tilt-card':       { mode: 'login', show_remember_me: true,  show_lost_password: true,  show_avatar: true,  form_bg: '#6366f1', text_color: '#fff', label_color: '#fff', input_bg: '#ffffff', input_color: '#312e81', input_border_color: '#312e81', submit_bg: '#312e81', submit_color: '#fff', submit_hover_bg: '#1e1b4b', link_color: '#fff', icon_color: '#fff', border_color: '#312e81' },
  },
  hotspot: {
    'modern-pin':      { marker_size: '24', marker_color: '#0ea5e9', pulse_animation: false, tooltip_bg: '#fff', tooltip_color: '#0f172a', border_radius: '12' },
    'minimal-dot':     { marker_size: '14', marker_color: '#1f2937', pulse_animation: false, tooltip_bg: '#fff', tooltip_color: '#1f2937', border_radius: '0'  },
    'magazine-numbered':{ marker_size: '32', marker_color: '#000',    pulse_animation: false, tooltip_bg: '#000', tooltip_color: '#fff',    border_radius: '0'  },
    'pulse-ring':      { marker_size: '24', marker_color: '#ef4444', pulse_animation: true,  tooltip_bg: '#fff', tooltip_color: '#7f1d1d', border_radius: '12' },
    'tooltip-card':    { marker_size: '28', marker_color: '#7c3aed', pulse_animation: false, tooltip_bg: '#fff', tooltip_color: '#4c1d95', border_radius: '16' },
    'glass-pin':       { marker_size: '28', marker_color: 'rgba(255,255,255,0.95)', pulse_animation: true, tooltip_bg: 'rgba(255,255,255,0.2)', tooltip_color: '#fff', border_radius: '16' },
    'neon-glow':       { marker_size: '24', marker_color: '#00ffff', pulse_animation: true,  tooltip_bg: '#0a0a0a', tooltip_color: '#00ffff', border_radius: '4'  },
    'brutalist-stamp': { marker_size: '36', marker_color: '#fde047', pulse_animation: false, tooltip_bg: '#000', tooltip_color: '#fde047', border_radius: '0'  },
    'gradient-pulse':  { marker_size: '28', marker_color: '#ec4899', pulse_animation: true,  tooltip_bg: '#fff', tooltip_color: '#831843', border_radius: '20' },
    'sticker-pin':     { marker_size: '32', marker_color: '#f59e0b', pulse_animation: false, tooltip_bg: '#fef3c7', tooltip_color: '#78350f', border_radius: '12' },
    'retro-marker':    { marker_size: '24', marker_color: '#22c55e', pulse_animation: false, tooltip_bg: '#0a0a0a', tooltip_color: '#22c55e', border_radius: '0'  },
    'tilt-3d':         { marker_size: '28', marker_color: '#6366f1', pulse_animation: false, tooltip_bg: '#fff', tooltip_color: '#312e81', border_radius: '12' },
  },
  list: {
    'modern-clean':    { icon_default: 'check',          icon_color: '#22c55e', text_color: '#1f2937', text_align: 'left', spacing: '12', icon_size: '18', icon_gap: '10', shadow: 'none' },
    'minimal-mono':    { icon_default: 'circle',         icon_color: '#1f2937', text_color: '#374151', text_align: 'left', spacing: '8',  icon_size: '8',  icon_gap: '12', shadow: 'none' },
    'magazine-numbered':{ icon_default: 'number',         icon_color: '#000',    text_color: '#000',    text_align: 'left', spacing: '14', icon_size: '20', icon_gap: '14', shadow: 'none' },
    'editorial-serif': { icon_default: 'arrow-right',    icon_color: '#1f2937', text_color: '#1f2937', text_align: 'left', spacing: '16', icon_size: '14', icon_gap: '12', shadow: 'none' },
    'compact-inline':  { icon_default: 'check',          icon_color: '#0ea5e9', text_color: '#374151', text_align: 'left', spacing: '6',  icon_size: '14', icon_gap: '8',  shadow: 'none' },
    'glass-rows':      { icon_default: 'check',          icon_color: '#fff',    text_color: '#fff',    text_align: 'left', spacing: '14', icon_size: '18', icon_gap: '12', shadow: 'lg'   },
    'neon-checks':     { icon_default: 'check',          icon_color: '#00ffff', text_color: '#00ffff', text_align: 'left', spacing: '12', icon_size: '18', icon_gap: '10', shadow: 'lg'   },
    'brutalist-block': { icon_default: 'arrow-right',    icon_color: '#000',    text_color: '#000',    text_align: 'left', spacing: '16', icon_size: '24', icon_gap: '14', shadow: 'xl'   },
    'gradient-bullets':{ icon_default: 'circle',         icon_color: '#a855f7', text_color: '#312e81', text_align: 'left', spacing: '12', icon_size: '14', icon_gap: '12', shadow: 'sm'   },
    'sticky-notes':    { icon_default: 'check',          icon_color: '#f59e0b', text_color: '#78350f', text_align: 'left', spacing: '14', icon_size: '18', icon_gap: '10', shadow: 'md'   },
    'retro-terminal':  { icon_default: 'arrow-right',    icon_color: '#22c55e', text_color: '#22c55e', text_align: 'left', spacing: '8',  icon_size: '14', icon_gap: '10', shadow: 'none' },
    'tilt-cards':      { icon_default: 'check',          icon_color: '#6366f1', text_color: '#312e81', text_align: 'left', spacing: '12', icon_size: '18', icon_gap: '10', shadow: 'lg'   },
  },
  desclist: {
    'modern-clean':     { layout: 'stacked', show_icon: true,  icon_color: '#3b82f6', icon_size: '20', term_color: '#0f172a', term_font_size: '15', term_font_weight: '600', definition_color: '#475569', definition_font_size: '14', separator: true,  border_color: '#e5e7eb', spacing: '16', striped: false, shadow: 'none' },
    'minimal-mono':     { layout: 'stacked', show_icon: false, term_color: '#0f172a', term_font_size: '14', term_font_weight: '500', definition_color: '#64748b', definition_font_size: '14', separator: false, spacing: '12', striped: false, shadow: 'none' },
    'magazine-spec':    { layout: 'grid',    show_icon: true,  icon_color: '#0f172a', icon_size: '18', term_color: '#0f172a', term_font_size: '13', term_font_weight: '700', definition_color: '#0f172a', definition_font_size: '15', separator: true,  border_color: '#0f172a', spacing: '14', striped: false, shadow: 'none' },
    'editorial-serif':  { layout: 'stacked', show_icon: false, term_color: '#0f172a', term_font_size: '17', term_font_weight: '600', definition_color: '#334155', definition_font_size: '15', separator: false, spacing: '20', striped: true,  striped_color: '#f8fafc', shadow: 'none' },
    'compact-inline':   { layout: 'inline',  show_icon: false, term_color: '#0f172a', term_font_size: '14', term_font_weight: '600', definition_color: '#64748b', definition_font_size: '14', separator: false, spacing: '8',  striped: false, shadow: 'none' },
    'glass-rows':       { layout: 'stacked', show_icon: true,  icon_color: '#0f172a', icon_size: '20', term_color: '#0f172a', term_font_size: '15', term_font_weight: '600', definition_color: '#475569', definition_font_size: '14', separator: true,  border_color: 'rgba(255,255,255,0.4)', spacing: '16', striped: true,  striped_color: 'rgba(255,255,255,0.3)', shadow: 'sm' },
    'neon-tech':        { layout: 'stacked', show_icon: true,  icon_color: '#22d3ee', icon_size: '22', term_color: '#22d3ee', term_font_size: '15', term_font_weight: '700', definition_color: '#cbd5e1', definition_font_size: '14', separator: true,  border_color: '#22d3ee', spacing: '14', striped: false, shadow: 'none' },
    'brutalist-block':  { layout: 'stacked', show_icon: true,  icon_color: '#000000', icon_size: '22', term_color: '#000000', term_font_size: '16', term_font_weight: '700', definition_color: '#000000', definition_font_size: '14', separator: true,  border_color: '#000000', spacing: '14', striped: true,  striped_color: '#fde047', shadow: 'none' },
    'gradient-soft':    { layout: 'stacked', show_icon: true,  icon_color: '#a78bfa', icon_size: '22', term_color: '#7c3aed', term_font_size: '16', term_font_weight: '600', definition_color: '#475569', definition_font_size: '14', separator: false, spacing: '18', striped: false, shadow: 'sm' },
    'sticky-notes':     { layout: 'stacked', show_icon: false, term_color: '#0f172a', term_font_size: '15', term_font_weight: '700', definition_color: '#0f172a', definition_font_size: '14', separator: false, spacing: '16', striped: true,  striped_color: '#fef3c7', shadow: 'md' },
    'retro-terminal':   { layout: 'stacked', show_icon: true,  icon_color: '#00ff8c', icon_size: '18', term_color: '#00ff8c', term_font_size: '14', term_font_weight: '700', definition_color: '#94e4a4', definition_font_size: '13', separator: true,  border_color: '#00ff8c', spacing: '10', striped: false, shadow: 'none' },
    'tilt-cards':       { layout: 'stacked', show_icon: true,  icon_color: '#0f172a', icon_size: '20', term_color: '#0f172a', term_font_size: '15', term_font_weight: '600', definition_color: '#475569', definition_font_size: '14', separator: false, spacing: '14', striped: false, shadow: 'md' },
  },
  imgcompare: {
    'modern-slider':   { start_position: '50', orientation: 'horizontal', handle_color: '#fff',    handle_size: '40', handle_border: '3', show_labels: true  },
    'minimal-line':    { start_position: '50', orientation: 'horizontal', handle_color: '#fff',    handle_size: '24', handle_border: '1', show_labels: false },
    'magazine-bold':   { start_position: '50', orientation: 'horizontal', handle_color: '#000',    handle_size: '48', handle_border: '4', show_labels: true  },
    'cinema-wide':     { start_position: '50', orientation: 'horizontal', handle_color: '#fff',    handle_size: '36', handle_border: '2', show_labels: false },
    'before-after-tag':{ start_position: '50', orientation: 'horizontal', handle_color: '#0ea5e9', handle_size: '40', handle_border: '3', show_labels: true  },
    'glass-handle':    { start_position: '50', orientation: 'horizontal', handle_color: 'rgba(255,255,255,0.85)', handle_size: '44', handle_border: '2', show_labels: true },
    'neon-divider':    { start_position: '50', orientation: 'horizontal', handle_color: '#00ffff', handle_size: '40', handle_border: '2', show_labels: true  },
    'brutalist-block': { start_position: '50', orientation: 'horizontal', handle_color: '#fde047', handle_size: '56', handle_border: '6', show_labels: true  },
    'gradient-line':   { start_position: '50', orientation: 'horizontal', handle_color: '#a855f7', handle_size: '40', handle_border: '3', show_labels: true  },
    'sticker-handle':  { start_position: '50', orientation: 'horizontal', handle_color: '#f59e0b', handle_size: '44', handle_border: '4', show_labels: true  },
    'retro-vhs':       { start_position: '50', orientation: 'horizontal', handle_color: '#22c55e', handle_size: '36', handle_border: '2', show_labels: false },
    'tilt-3d':         { start_position: '50', orientation: 'horizontal', handle_color: '#6366f1', handle_size: '44', handle_border: '3', show_labels: true  },
  },
  viewer360: {
    'modern-clean':   { autorotate: true,  autorotate_speed: '1', mouse_drag: true, touch_drag: true, scroll_zoom: false },
    'minimal-frame':  { autorotate: false, autorotate_speed: '1', mouse_drag: true, touch_drag: true, scroll_zoom: false },
    'cinema-wide':    { autorotate: true,  autorotate_speed: '0.5', mouse_drag: true, touch_drag: true, scroll_zoom: true },
    'showcase-bold':  { autorotate: true,  autorotate_speed: '1.5', mouse_drag: true, touch_drag: true, scroll_zoom: true },
    'product-display':{ autorotate: true,  autorotate_speed: '2',   mouse_drag: true, touch_drag: true, scroll_zoom: true },
    'glass-frame':    { autorotate: true,  autorotate_speed: '1',   mouse_drag: true, touch_drag: true, scroll_zoom: false },
    'neon-frame':     { autorotate: true,  autorotate_speed: '1.5', mouse_drag: true, touch_drag: true, scroll_zoom: false },
    'brutalist-block':{ autorotate: false, autorotate_speed: '1',   mouse_drag: true, touch_drag: true, scroll_zoom: false },
    'gradient-glow':  { autorotate: true,  autorotate_speed: '1',   mouse_drag: true, touch_drag: true, scroll_zoom: false },
    'sticker-frame':  { autorotate: true,  autorotate_speed: '1.2', mouse_drag: true, touch_drag: true, scroll_zoom: false },
    'retro-monitor':  { autorotate: true,  autorotate_speed: '1',   mouse_drag: true, touch_drag: true, scroll_zoom: false },
    'tilt-3d':        { autorotate: true,  autorotate_speed: '1.5', mouse_drag: true, touch_drag: true, scroll_zoom: true  },
  },
  lightbox: {
    'modern-clean':   { columns: '3', gap: '15', thumb_ratio: '1:1',  thumb_radius: '8',   overlay_style: 'dark',   animation: 'fade',  shadow: 'sm'   },
    'minimal-thumbs': { columns: '6', gap: '8',  thumb_ratio: '1:1',  thumb_radius: '0',   overlay_style: 'dark',   animation: 'fade',  shadow: 'none' },
    'magazine-grid':  { columns: '3', gap: '4',  thumb_ratio: '4:3',  thumb_radius: '0',   overlay_style: 'dark',   animation: 'fade',  shadow: 'none' },
    'cinema-wide':    { columns: '2', gap: '20', thumb_ratio: '16:9', thumb_radius: '4',   overlay_style: 'dark',   animation: 'zoom',  shadow: 'lg'   },
    'compact-row':    { columns: '4', gap: '10', thumb_ratio: '1:1',  thumb_radius: '4',   overlay_style: 'dark',   animation: 'fade',  shadow: 'sm'   },
    'glass-tiles':    { columns: '3', gap: '12', thumb_ratio: '1:1',  thumb_radius: '12',  overlay_style: 'light',  animation: 'fade',  shadow: 'lg'   },
    'neon-frame':     { columns: '3', gap: '12', thumb_ratio: '1:1',  thumb_radius: '4',   overlay_style: 'dark',   animation: 'zoom',  shadow: 'lg'   },
    'brutalist-block':{ columns: '2', gap: '0',  thumb_ratio: '1:1',  thumb_radius: '0',   overlay_style: 'dark',   animation: 'slide', shadow: 'xl'   },
    'gradient-soft':  { columns: '3', gap: '14', thumb_ratio: '1:1',  thumb_radius: '16',  overlay_style: 'dark',   animation: 'fade',  shadow: 'md'   },
    'sticker-thumbs': { columns: '3', gap: '12', thumb_ratio: '1:1',  thumb_radius: '12',  overlay_style: 'dark',   animation: 'fade',  shadow: 'md'   },
    'retro-vhs':      { columns: '3', gap: '8',  thumb_ratio: '4:3',  thumb_radius: '0',   overlay_style: 'dark',   animation: 'fade',  shadow: 'none' },
    'tilt-3d':        { columns: '3', gap: '15', thumb_ratio: '1:1',  thumb_radius: '8',   overlay_style: 'dark',   animation: 'zoom',  shadow: 'lg'   },
  },
};

// v1.0.56 — Espone le strutture per test QA programmatico (window.__OLO_QA__).
// Inizializzata immediatamente al module load così è disponibile prima del primo apply.
if (typeof window !== 'undefined') {
  window.__OLO_QA__ = {
    TILE_PRESETS,
    BASE_THEME_PRESETS,
    get applyTilePresetTheme() { return applyTilePresetTheme; },
  };
}

function applyTilePresetTheme(tile, presetId) {
  if (!tile || !tile.type) return;
  const tilePresets = TILE_PRESETS[tile.type];
  if (!tilePresets) return;

  // 'custom' = reset specifico per la hero: i preset hero settano sia tile.settings.* (colori,
  // tipografia, layout, CTA) sia tile.style.bg. Quando l'utente torna a "Personalizzato" ripuliamo
  // gli override visivi più "rumorosi" così le scelte manuali successive funzionano da pulito.
  if (!presetId || presetId === 'custom') {
    if (tile.type === 'hero') {
      // Wrapper esterno: pulisci bg e color ereditato (GridCell li applica al wrapper).
      tilesStore.applyStylePreset(tile.id, { bg: { type: 'none' }, text_color: '' });
      // Settings interni della hero: pulisci i colori (i restanti come font_weight ecc restano).
      tilesStore.updateTile(tile.id, {
        text_color: '', title_color: '', subtitle_color: '',
      });
    }
    return;
  }

  const presetValues = tilePresets[presetId];
  if (!presetValues) return;

  // Schema dual { settings: {...}, style: {...} } — usato dai preset hero (v3.55.14+).
  // Il dispatcher applica i due rami al posto giusto, così i campi tile-specific
  // (min_height, tile_padding, title_*, cta_*) arrivano al renderer della hero,
  // e il bg unificato finisce in style.bg dove BackgroundControls lo gestisce.
  // v1.0.58 — check `typeof === 'object'`: alcuni preset flat hanno `style: 'filled'`/`style: 'default'`
  // come setting key (es. starrating, panel, quotation). Senza typeof check, la stringa
  // truthy faceva entrare nel branch dual-schema e i field flat non venivano applicati.
  const hasDualSchema = (presetValues.settings && typeof presetValues.settings === 'object')
                       || (presetValues.style && typeof presetValues.style === 'object');
  if (hasDualSchema) {
    if (presetValues.style && typeof presetValues.style === 'object') {
      // v3.55.23 — se l'utente ha media caricato (immagine, video, gallery), il preset
      // NON cambia il tipo di sfondo: l'immagine resta visibile, il preset porta solo
      // overlay/posizione/parallax/proprietà secondarie. Senza questo, applicare un
      // preset "Bold Statement" (bg solid nero) cancella visivamente l'immagine
      // anche se l'image_url resta tecnicamente nel JSON.
      const styleCopy = JSON.parse(JSON.stringify(presetValues.style));
      const userBg = tile.style?.bg;
      if (styleCopy.bg && userBg && typeof userBg === 'object') {
        const userType = userBg.type;
        const userHasMedia =
          (userType === 'image'   && !!userBg.image_url)  ||
          (userType === 'video'   && !!userBg.video_url)  ||
          (userType === 'gallery' && Array.isArray(userBg.gallery_images) && userBg.gallery_images.length > 0);

        if (userHasMedia) {
          // Vincola il tipo a quello scelto dall'utente — il preset perde questa decisione.
          styleCopy.bg.type = userType;
          // Riporta tutti i campi del media corrispondente al tipo utente.
          const MEDIA_KEYS = {
            image:   ['image_url', 'image_id', 'image_position', 'image_size', 'image_parallax', 'image_repeat'],
            video:   ['video_url', 'video_poster', 'video_id', 'video_size', 'video_position',
                      'video_no_loop', 'video_no_autoplay', 'video_audio', 'video_controls'],
            gallery: ['gallery_images', 'gallery_loop', 'gallery_duration', 'gallery_transition',
                      'gallery_transition_ms', 'gallery_lazyload', 'gallery_kenburns', 'gallery_kenburns_dir'],
          };
          for (const k of (MEDIA_KEYS[userType] || [])) {
            if (userBg[k] !== undefined) styleCopy.bg[k] = userBg[k];
          }
        } else if (userType === 'gradient' && Array.isArray(userBg.gradient_stops) && userBg.gradient_stops.length > 0
                   && styleCopy.bg.type === 'gradient') {
          // Senza media ma con gradiente custom: preservalo solo se il preset è anch'esso gradient.
          styleCopy.bg.gradient_stops = userBg.gradient_stops;
        }
      }
      tilesStore.applyStylePreset(tile.id, styleCopy);
    }
    if (presetValues.settings && typeof presetValues.settings === 'object') tilesStore.updateTile(tile.id, presetValues.settings);
    return;
  }

  // Schema legacy (flat) — mantenuto per back-compat con tutti gli altri tile non-hero.
  // Map preset keys → keys expected by renderer (PHP render_element_node + Vue GridCell).
  // Preset usa "background_color" ma renderer (Olo_Css_Builder::get_effective_bg) cerca "bg_color".
  const KEY_MAP = { background_color: 'bg_color' };
  const mapped = {};
  for (const k in presetValues) {
    const targetKey = KEY_MAP[k] || k;
    mapped[targetKey] = presetValues[k];
  }
  // Tutti i tile non-hero leggono le proprietà visive da tile.settings (i renderer PHP
  // chiamano wp_parse_args sulle settings, le chiavi sconosciute vengono scartate).
  // Quindi il preset DEVE atterrare in settings — altrimenti chiavi come font_weight,
  // heading_color, decoration, text_shadow, gradient_text, text_stroke restano in style
  // dove nessuno le legge e il preset "non si applica".
  tilesStore.updateTile(tile.id, { ...mapped });
  // v1.0.52 — Tile ATOMICHE (button/icon/divider/spacer/togglebtn): NON replicare bg/radius/shadow
  // al wrapper esterno. Sono elementi inline/atomici dentro una colonna molto più grande, e
  // replicare bg crea un container enorme colorato attorno al piccolo elemento (rotto visivamente).
  // Solo le tile "card-like" (panel, content, hero, ecc.) traggono beneficio dalla replica.
  const ATOMIC_TILES = new Set(['button', 'icon', 'divider', 'spacer', 'togglebtn']);
  if (ATOMIC_TILES.has(tile.type)) {
    // v1.0.53 — CLEANUP per tile atomiche: rimuovi bg/radius/shadow dal wrapper esistente
    // (template salvati prima del v1.0.52 potevano avere il wrapper colorato dal preset
    // precedente — questo li pulisce non appena l'utente cambia preset).
    const tileStyle = tile.style || {};
    if (tileStyle.bg_color || tileStyle.border_radius || tileStyle.shadow) {
      tilesStore.applyStylePreset(tile.id, { bg_color: '', border_radius: '', shadow: '' });
    }
  } else {
    // Le sole 3 chiavi che il wrapper esterno usa (bg/border_radius/shadow) vengono replicate
    // anche in tile.style, dove GridCell le applica al <section>/<div> contenitore.
    const WRAPPER_KEYS = ['bg_color', 'border_radius', 'shadow'];
    const wrapperPatch = {};
    WRAPPER_KEYS.forEach(k => { if (k in mapped) wrapperPatch[k] = mapped[k]; });
    if (Object.keys(wrapperPatch).length) {
      tilesStore.applyStylePreset(tile.id, wrapperPatch);
    }
  }
}

/**
 * Mega Menu — applica un "template pronto" (40 ricette in megamenuTemplates.js).
 * Apply-once: scrive l'intero bundle settings del template in tile.settings
 * (sovrascrivibile poi dall'inspector). Il template imposta anche `preset` quando
 * previsto, così il selettore Preset riflette la base. Nessuna chiave nuova salvata.
 */
function applyMegamenuTemplate(tile, templateId) {
  if (!tile || !templateId) return;
  const tpl = MEGAMENU_TEMPLATES.find(t => t.id === templateId);
  if (!tpl || !tpl.settings) return;
  tilesStore.updateTile(tile.id, structuredClone(tpl.settings));
  builderStore.markDirtyForTile(tile.id);
}

function updateSetting(key, value) {
  if (!builderStore.selectedTileId) return;
  const tile = tilesStore.getTileById(builderStore.selectedTileId);
  // Row layout change: restructure columns via store action
  if (tile && tile.type === 'row' && key === 'layout') {
    tilesStore.changeRowLayout(builderStore.selectedTileId, value);
    builderStore.markDirtyForTile(builderStore.selectedTileId);
    return;
  }
  // ProGallery: when changing layout_family, auto-set layout to first of new family
  if (tile && tile.type === 'progallery' && key === 'layout_family') {
    const firstLayout = value === 'strip' ? 'strip' : 'grid';
    tilesStore.updateTile(builderStore.selectedTileId, { layout: firstLayout });
  }
  // V3.23.1 — preset selector: batch-populate the field group when changing
  // preset (so subsequent manual edits on those fields take precedence).
  // V3.55.14 — passiamo 'custom' anch'esso per dare alla funzione l'occasione
  // di resettare gli override del preset precedente (specifico hero).
  if (tile && key === 'preset' && value !== undefined) {
    applyTilePresetTheme(tile, value);
  }
  // v1.4.11 — Mega Menu: selettore "Template pronti". Chiave SENTINELLA non salvata:
  // applica i settings del template una tantum (apply-once) e NON persiste la chiave,
  // così il select resta sui "— Applica un template… —" e il template è solo un punto
  // di partenza interamente sovrascrivibile dall'inspector.
  if (tile && key === '__megamenu_template__') {
    if (value) applyMegamenuTemplate(tile, value);
    return; // mai persistere la sentinella
  }
  // Row custom_widths: handled by dedicated inline input, not here
  tilesStore.updateTile(builderStore.selectedTileId, { [key]: value });
  builderStore.markDirtyForTile(builderStore.selectedTileId);
}

function updateStyle(key, value) {
  if (!builderStore.selectedTileId) return;
  tilesStore.updateTileStyle(builderStore.selectedTileId, { [key]: value });
  builderStore.markDirtyForTile(builderStore.selectedTileId);
}

/**
 * Dispatcher unico per gli update emessi dallo StyleFieldsRenderer.
 * Payload format:
 *   { type: 'main',       key, value }              → updateStyle
 *   { type: 'hover',      key, value }              → updateHover
 *   { type: 'transition', key, value }              → updateTransition
 *   { type: 'multi',      updates: [{key, value}] } → batch updateStyle
 *   { type: 'nested',     path, value }             → setIn(tile.style, path)  (fallback)
 */
function onStyleUpdate(payload) {
  if (!payload || !payload.type) return;
  switch (payload.type) {
    case 'main':
      updateStyle(payload.key, payload.value);
      break;
    case 'hover':
      updateHover(payload.key, payload.value);
      break;
    case 'transition':
      updateTransition(payload.key, payload.value);
      break;
    case 'multi':
      (payload.updates || []).forEach(u => updateStyle(u.key, u.value));
      break;
    case 'setting':
      // Tile-specific style fields (es. tipografia hero) — vivono in tile.settings,
      // ma sono mostrati nel tab Stile per coerenza con la regola universale.
      updateSetting(payload.key, payload.value);
      break;
    case 'nested': {
      // fallback: setIn(tile.style, path, value) via store
      const segs = String(payload.path || '').split('.');
      if (segs.length === 0) break;
      const root = segs[0];
      const sub = segs.slice(1).join('.');
      if (root === 'transition') updateTransition(sub, payload.value);
      else updateStyle(payload.path, payload.value);
      break;
    }
  }
}

function onTileBgUpdate(newBg) {
  if (!builderStore.selectedTileId) return;
  tilesStore.updateTileStyle(builderStore.selectedTileId, { bg: newBg });
  builderStore.markDirtyForTile(builderStore.selectedTileId);
}

function updateAdvanced(key, value) {
  if (!builderStore.selectedTileId) return;
  tilesStore.updateTileAdvanced(builderStore.selectedTileId, { [key]: value });
  builderStore.markDirtyForTile(builderStore.selectedTileId);
}

function toggleParallax() {
  if (hasElementParallax.value) {
    updateAdvanced('parallax', null);
  } else {
    updateAdvanced('bezier_path', null);
    updateAdvanced('parallax', { x:[], y:[], scale:[], rotate:[], opacity:[], blur:[], nomobile:true });
  }
}

function toggleBezier() {
  if (tileAdvanced.value.bezier_path) {
    updateAdvanced('bezier_path', null);
  } else {
    updateAdvanced('parallax', null);
    updateAdvanced('bezier_path', { keyframes: [{pct:0,x:0,y:0},{pct:100,x:0,y:-100}] });
  }
}

// --- Hover & transition store dispatchers (chiamati da onStyleUpdate via StyleFieldsRenderer) ---
function updateHover(key, value) {
  if (!builderStore.selectedTileId) return;
  tilesStore.updateTileHover(builderStore.selectedTileId, { [key]: value });
  builderStore.markDirtyForTile(builderStore.selectedTileId);
}

function updateTransition(key, value) {
  if (!builderStore.selectedTileId) return;
  tilesStore.updateTileTransition(builderStore.selectedTileId, { [key]: value });
  builderStore.markDirtyForTile(builderStore.selectedTileId);
}

// --- Dynamic content ---
function onDynamicFieldUpdate(dynamicUpdate, isRemove) {
  if (!builderStore.selectedTileId) return;
  if (isRemove) {
    // dynamicUpdate is the updated dynamic object after field removal
    const tile = selectedTile.value;
    if (tile) {
      tile.dynamic = dynamicUpdate;
    }
  } else {
    tilesStore.updateTileDynamic(builderStore.selectedTileId, dynamicUpdate);
  }
  builderStore.markDirtyForTile(builderStore.selectedTileId);
}

function updateDynamicQuery(queryConfig) {
  if (!builderStore.selectedTileId) return;
  tilesStore.updateTileDynamic(builderStore.selectedTileId, { _query: queryConfig });
  builderStore.markDirtyForTile(builderStore.selectedTileId);
}

function updateDynamicItemMap(itemMap) {
  if (!builderStore.selectedTileId) return;
  tilesStore.updateTileDynamic(builderStore.selectedTileId, { _itemMap: itemMap });
  builderStore.markDirtyForTile(builderStore.selectedTileId);
}
</script>

<style scoped>
.slide-enter-active,
.slide-leave-active {
  transition: transform 0.2s ease, opacity 0.2s ease;
}
.slide-enter-from,
.slide-leave-to {
  transform: translateX(100%);
  opacity: 0;
}

/* Inspector search */
.insp-search {
  position: relative;
  margin-bottom: 12px;
}
.insp-search-icon {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  color: #6B7280;
  pointer-events: none;
}
.insp-search-input {
  width: 100%;
  padding: 7px 28px 7px 30px;
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 8px;
  background: rgba(255,255,255,0.06);
  color: #E5E7EB;
  font-size: 12px;
  font-family: inherit;
  outline: none;
  transition: border-color 0.2s, background 0.2s;
  box-sizing: border-box;
}
.insp-search-input::placeholder { color: #6B7280; }
.insp-search-input:focus {
  border-color: var(--olo-color-primary, #6366F1);
  background: rgba(255,255,255,0.1);
}
.insp-search-clear {
  position: absolute;
  right: 4px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #6B7280;
  font-size: 15px;
  cursor: pointer;
  padding: 2px 6px;
  line-height: 1;
}
.insp-search-clear:hover { color: #D1D5DB; }

.insp-filter-modified {
  position: absolute;
  right: 28px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #6B7280;
  cursor: pointer;
  padding: 4px;
  line-height: 0;
  border-radius: 3px;
}
.insp-filter-modified:hover { color: #D1D5DB; background: rgba(255,255,255,0.06); }
.insp-filter-modified--active { color: var(--olo-color-primary, #6366F1); }
.insp-filter-modified--active:hover { color: var(--olo-color-primary, #6366F1); }

/* Tile state badges (hover/responsive/cond/anim/sticky/...) */
.insp-badge {
  display: inline-flex;
  align-items: center;
  font-size: 9px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding: 2px 7px;
  border-radius: 999px;
  border: 1px solid currentColor;
  background: transparent;
  cursor: pointer;
  line-height: 1.4;
  transition: background 0.15s, color 0.15s;
}
.insp-badge:hover { background: currentColor; }
.insp-badge:hover { color: #fff !important; }
.insp-badge--orange { color: #f59e0b; }
.insp-badge--blue   { color: #3b82f6; }
.insp-badge--purple { color: #a855f7; }
.insp-badge--green  { color: #10b981; }
.insp-badge--cyan   { color: #06b6d4; }
.insp-badge--yellow { color: #eab308; }
.insp-badge--gray   { color: #9ca3af; }
.insp-badge:hover { background: currentColor; box-shadow: 0 0 0 1px currentColor inset; }

/* Action buttons (copy/paste style) nell'header inspector */
.insp-action-btn {
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid rgba(255, 255, 255, 0.08);
  color: #9ca3af;
  border-radius: 4px;
  padding: 4px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: background 0.15s, color 0.15s, border-color 0.15s;
}
.insp-action-btn:hover:not(.insp-action-btn--disabled) {
  background: rgba(255, 255, 255, 0.1);
  color: #f3f4f6;
  border-color: rgba(255, 255, 255, 0.18);
}
.insp-action-btn--disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

/* Preset dropdown */
.insp-preset-wrap { position: relative; display: inline-block; }
.insp-preset-menu {
  position: absolute;
  top: calc(100% + 4px);
  right: 0;
  min-width: 200px;
  max-width: 300px;
  background: #1f2937;
  border: 1px solid rgba(255,255,255,0.12);
  border-radius: 6px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.4);
  padding: 4px;
  z-index: 50;
}
.insp-preset-menu-title {
  font-size: 9px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #6b7280;
  padding: 4px 8px;
  border-bottom: 1px solid rgba(255,255,255,0.08);
  margin-bottom: 2px;
}
.insp-preset-item {
  display: flex;
  align-items: center;
  width: 100%;
  background: transparent;
  border: none;
  color: #d1d5db;
  font-size: 12px;
  text-align: left;
  padding: 6px 8px;
  border-radius: 3px;
  cursor: pointer;
  gap: 8px;
}
.insp-preset-item:hover { background: rgba(255,255,255,0.06); }
.insp-preset-del {
  color: #6b7280;
  font-size: 16px;
  line-height: 1;
  padding: 0 4px;
}
.insp-preset-del:hover { color: #ef4444; }

/* ═══════════════════════════════════════════════════════════
   V2 INSPECTOR — Right panel, mirrors the V2 left sidebar
   ═══════════════════════════════════════════════════════════ */

/* Root: full height, flex col, light theme */
.v2i-root {
  height: 100%;
  display: flex;
  flex-direction: column;
  min-width: 0;
  overflow: hidden;
  background: #fff;
  border-left: 1px solid #e2e8f0;
  font-family: inherit;
  font-size: 12px;
}

/* Page settings (no tile) — plain scrollable */
.v2i-page-only {
  flex: 1;
  overflow-y: auto;
  background: #1f2937;     /* keep dark for PageSettingsPanel which is built dark */
  color: #e5e7eb;
}

/* HEAD (sticky) */
.v2i-head {
  flex-shrink: 0;
  padding: 10px 12px 8px;
  background: #f9fafb;
  border-bottom: 1px solid #f1f5f9;
}
.v2i-head :deep(h3) { color: #1e293b; }

/* Override dark inspector chrome inside .v2i-head */
.v2i-head :deep(.mb-text-gray-200) { color: #1e293b !important; }
.v2i-head :deep(.mb-text-gray-300) { color: #334155 !important; }
.v2i-head :deep(.mb-text-gray-400) { color: #64748b !important; }
.v2i-head :deep(.mb-text-gray-500) { color: #94a3b8 !important; }
.v2i-head :deep(.mb-text-gray-600) { color: #cbd5e1 !important; }

/* Action buttons in head — light */
.v2i-head :deep(.insp-action-btn) {
  background: #fff;
  border-color: #e2e8f0;
  color: #64748b;
}
.v2i-head :deep(.insp-action-btn:hover:not(.insp-action-btn--disabled)) {
  background: #f1f5f9;
  color: #1e293b;
  border-color: #cbd5e1;
}

/* Search row — light */
.v2i-head :deep(.insp-search) { margin-bottom: 8px; }
.v2i-head :deep(.insp-search-input) {
  background: #fff;
  border-color: #e2e8f0;
  color: #1e293b;
  border-radius: 7px;
  padding: 6px 28px 6px 30px;
}
.v2i-head :deep(.insp-search-input::placeholder) { color: #94a3b8; }
.v2i-head :deep(.insp-search-input:focus) {
  border-color: var(--olo-color-primary, #e8622a);
  background: #fff;
  box-shadow: 0 0 0 3px rgba(232, 98, 42, 0.10);
}
.v2i-head :deep(.insp-search-icon) { color: #94a3b8; }
.v2i-head :deep(.insp-search-clear) { color: #94a3b8; }
.v2i-head :deep(.insp-search-clear:hover) { color: #1e293b; }
.v2i-head :deep(.insp-filter-modified) { color: #94a3b8; }
.v2i-head :deep(.insp-filter-modified:hover) { color: #1e293b; background: #f1f5f9; }
.v2i-head :deep(.insp-filter-modified--active) { color: var(--olo-color-primary, #e8622a); }
.v2i-head :deep(.insp-filter-modified--active:hover) { color: var(--olo-color-primary, #e8622a); }

/* Search row: input search + hover-state toggle aside */
.v2i-search-row {
  display: flex;
  align-items: stretch;
  gap: 8px;
  margin-bottom: 8px;
}
.v2i-search-row .insp-search {
  flex: 1;
  margin-bottom: 0;
}

/* Hover-state toggle: amber icon button with floating "Hover" micro-pill */
.v2i-hover-toggle {
  position: relative;
  width: 30px;
  height: 30px;
  border: 0;
  border-radius: 7px;
  cursor: pointer;
  display: grid;
  place-items: center;
  background: #fef3c7;
  color: #92400e;
  flex-shrink: 0;
  align-self: center;
  transition: background-color 0.15s, color 0.15s, box-shadow 0.15s;
}
.v2i-hover-toggle:hover { background: #fde68a; }
.v2i-hover-toggle.on {
  background: #f59e0b;
  color: #fff;
  box-shadow: 0 1px 3px rgba(245, 158, 11, 0.35);
}
.v2i-hover-toggle::before {
  content: "Hover";
  position: absolute;
  top: -7px;
  right: -3px;
  font-size: 8px;
  font-weight: 700;
  background: #f59e0b;
  color: #fff;
  padding: 1px 5px;
  border-radius: 99px;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  line-height: 1.2;
  pointer-events: none;
}
.v2i-hover-toggle.on::before {
  background: #fff;
  color: #92400e;
}

/* When editingHover is active, mark the content panel with an amber rail at top */
.v2i-content.is-hover-mode {
  box-shadow: inset 0 2px 0 0 #f59e0b;
}

/* Tabs (Contenuto / Stile / Avanzate) — pill segmented arancio brand */
.v2i-tabs {
  display: flex;
  gap: 0;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 2px;
  margin-bottom: 0;
}
.v2i-tabs button {
  flex: 1;
  height: 28px;
  border: 0;
  background: transparent;
  cursor: pointer;
  font: inherit;
  font-family: inherit;
  font-size: 12px;
  font-weight: 500;
  color: #64748b;
  border-radius: 6px;
  transition: background-color 0.15s, color 0.15s, box-shadow 0.15s;
}
.v2i-tabs button:hover:not(.on) { color: #1e293b; }
.v2i-tabs button.on {
  background: var(--olo-color-primary, #e8622a);
  color: #fff;
  box-shadow: 0 1px 3px rgba(232, 98, 42, 0.3);
}

/* BODY: content + rail */
.v2i-body {
  flex: 1;
  min-height: 0;
  display: grid;
  grid-template-columns: 1fr 64px;
}
.v2i-content {
  overflow-y: auto;
  padding: 12px;
  background: #fff;
  color: #1e293b;
}

/* Override dark text/bg in content area for V2 light theme */
.v2i-content :deep(.mb-text-gray-200) { color: #1e293b !important; }
.v2i-content :deep(.mb-text-gray-300) { color: #334155 !important; }
.v2i-content :deep(.mb-text-gray-400) { color: #64748b !important; }
.v2i-content :deep(.mb-text-gray-500) { color: #94a3b8 !important; }

/* Group card style for sub-section CollapseSection (non-macro): soft wrapper
   with light bg + border light + radius 8 + eyebrow header. Macro accordions
   keep their dark style as set by CollapseSection.vue itself. */
.v2i-content :deep(.olo-collapse-section:not(.olo-collapse-section--macro)) {
  background: #f9fafb;
  border: 1px solid #f1f5f9;
  border-radius: 8px;
  padding: 2px;
}
.v2i-content :deep(.olo-collapse-section:not(.olo-collapse-section--macro) .collapse-head) {
  background: transparent !important;
  color: #64748b !important;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  padding: 6px 10px !important;
}
.v2i-content :deep(.olo-collapse-section:not(.olo-collapse-section--macro) .collapse-head:hover) {
  background: rgba(0, 0, 0, 0.02) !important;
  color: #1e293b !important;
}
.v2i-content :deep(.olo-collapse-section:not(.olo-collapse-section--macro) .collapse-head--open) {
  background: transparent !important;
  color: #1e293b !important;
}
.v2i-content :deep(.olo-collapse-section:not(.olo-collapse-section--macro) .collapse-head svg) {
  color: #94a3b8 !important;
}

/* Macro accordions inside V2 inspector: lighten the dark default to a muted
   gray-on-soft (more legible against the white panel background). */
.v2i-content :deep(.olo-collapse-section--macro .collapse-head) {
  background: #f1f5f9 !important;
  color: #1e293b !important;
}
.v2i-content :deep(.olo-collapse-section--macro .collapse-head:hover) {
  background: #e2e8f0 !important;
}
.v2i-content :deep(.olo-collapse-section--macro .collapse-head--open) {
  background: rgba(232, 98, 42, 0.08) !important;
  color: #b04217 !important;
}
.v2i-content :deep(.olo-collapse-section--macro .collapse-head svg) {
  color: #64748b !important;
}
.v2i-content :deep(.olo-collapse-section--macro .collapse-head--open svg) {
  color: #b04217 !important;
}

/* RAIL — vertical icons on right edge (mirrors left sidebar rail) */
.v2i-rail {
  background: #f9fafb;
  border-left: 1px solid #f1f5f9;
  display: flex;
  flex-direction: column;
  padding: 4px 0;
  overflow-y: auto;
  scrollbar-width: none;
}
.v2i-rail::-webkit-scrollbar { display: none; }

.v2i-rail-btn {
  position: relative;
  width: 100%;
  height: 56px;
  border: 0;
  background: transparent;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 3px;
  padding: 0 4px;
  font-family: inherit;
  color: #64748b;
  flex-shrink: 0;
  transition: background-color 0.15s, color 0.15s;
}

/* Mirror: accent bar on the RIGHT edge */
.v2i-rail-btn .bar {
  position: absolute;
  right: 0;
  top: 8px;
  bottom: 8px;
  width: 2px;
  background: transparent;
  border-radius: 2px 0 0 2px;
  transition: background-color 0.15s;
}
.v2i-rail-btn .ic {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 18px;
  height: 18px;
}
.v2i-rail-btn .ic :deep(svg) { width: 18px; height: 18px; }
.v2i-rail-btn .lbl {
  font-size: 10px;
  font-weight: 500;
  line-height: 1.1;
  text-align: center;
  letter-spacing: 0.1px;
  max-width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  padding: 0 2px;
}

.v2i-rail-btn:not(.on):hover {
  color: #1e293b;
  background: rgba(0, 0, 0, 0.02);
}
.v2i-rail-btn.on {
  color: #1e293b;
  background: #fff;
}
.v2i-rail-btn.on .bar {
  background: var(--olo-color-primary, #e8622a);
}

/* Transient highlight when a rail entry is clicked */
.v2i-content :deep(.v2i-sec-flash) {
  animation: v2iSecFlash 0.9s var(--ot-ease-std, cubic-bezier(.4, 0, .2, 1));
}
@keyframes v2iSecFlash {
  0%   { box-shadow: 0 0 0 0 rgba(232, 98, 42, 0.0); }
  20%  { box-shadow: 0 0 0 3px rgba(232, 98, 42, 0.45); }
  100% { box-shadow: 0 0 0 0 rgba(232, 98, 42, 0.0); }
}
</style>
