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

        <!-- Fase 3 unificazione hero: conversione esplicita tile legacy → canonico -->
        <div v-if="heroConversionTarget" class="insp-heroconvert">
          <div class="insp-heroconvert-txt">
            {{ t('Questa tile è stata sostituita da') }} <b>{{ heroConversionTarget === 'hero' ? 'Hero' : 'Hero Split' }}</b>.
            {{ t('Puoi convertirla mantenendo contenuti e scena (annullabile con Ctrl+Z).') }}
          </div>
          <button class="insp-heroconvert-btn" @click="onConvertToHero">
            {{ t('Converti in') }} {{ heroConversionTarget === 'hero' ? 'Hero' : 'Hero Split' }}
          </button>
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
            {{ t(tab) }}<span
              v-if="searchActive && activeTab !== tab && searchTabCounts[tab]"
              class="v2i-tab-count"
              :title="t('Risultati della ricerca in questo tab')"
            >{{ searchTabCounts[tab] }}</span>
          </button>
        </div>
      </div>
      <!-- ─── /HEAD ────────────────────────────────────────────── -->

      <!-- ─── BODY: panel + rail ───────────────────────────────── -->
      <div class="v2i-body">
        <div ref="contentRef" class="v2i-content" :class="{ 'is-hover-mode': builderStore.editingHover }">

        <!-- Ricerca cross-tab: nessun match nel tab corrente ma match altrove → link diretti -->
        <div v-if="searchCrossTabHints.length" class="v2i-search-crosshint">
          <span>{{ t('Nessun risultato in questo tab.') }}</span>
          <button
            v-for="h in searchCrossTabHints"
            :key="h.tab"
            type="button"
            @click="onTabChange(h.tab)"
          >{{ t(h.tab) }} ({{ h.count }})</button>
        </div>

        <!-- ============ Content tab (data-driven) ============ -->
        <div v-if="activeTab === 'Contenuto'" class="mb-space-y-3" role="tabpanel" id="inspector-panel-Contenuto" :aria-labelledby="'inspector-tab-Contenuto'">
          <!-- Custom editor: ProSlider -->
          <div v-if="elementDef?.customEditor === 'proslider'" class="mb-space-y-3">
            <p class="mb-text-xs mb-text-gray-400">{{ t("Configura slide, livelli e animazioni nell'editor visuale.") }}</p>
            <button
              @click="showProSliderEditor = true"
              class="mb-w-full mb-py-2.5 mb-bg-primary-600 mb-text-white mb-text-sm mb-font-semibold mb-rounded-lg hover:mb-bg-primary-500 mb-transition-colors"
            >
              {{ t('Apri editor slider') }}
            </button>
            <p class="mb-text-[10px] mb-text-gray-400">{{ (selectedTile.settings?.slides || []).length }} {{ t('slide configurate') }}</p>

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
                :forceOpen="searchActive"
              >
                <template v-for="(field, fIdx) in section.fields" :key="field.key || ('f-' + sIdx + '-' + fIdx)">
                  <template v-if="isFieldVisible(field, section.label)">
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
            :searchQuery="settingsSearch"
            @update="onStyleUpdate"
          />
        </div>

        <!-- ============ Advanced tab ============ -->
        <div v-else class="mb-space-y-4" role="tabpanel" id="inspector-panel-Avanzate" :aria-labelledby="'inspector-tab-Avanzate'">
          <!-- ===== MACRO: Identificatori ===== -->
          <CollapseSection id="v2i-sec-adv-id" :title="t('Identificatori')" :defaultOpen="true" :macro="true">
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
            <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-1">{{ t('Classi CSS') }}</label>
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
            <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-1">{{ t('CSS personalizzato') }}</label>
            <textarea
              :value="tileAdvanced.custom_css || ''"
              @input="updateAdvanced('custom_css', $event.target.value)"
              rows="4"
              placeholder="color: red;&#10;transform: rotate(2deg);"
              class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-xs mb-text-gray-900 mb-font-mono mb-resize-y"
            />
            <p class="mb-text-[10px] mb-text-gray-400 mb-mt-1">{{ t('Proprietà CSS applicate direttamente al wrapper della tile') }}</p>
          </div>
            </div>
          </CollapseSection>

          <!-- ===== MACRO: Visibilità & Condizioni ===== -->
          <CollapseSection id="v2i-sec-adv-visibility" :title="t('Visibilità & Condizioni')" :macro="true">
            <div class="mb-space-y-3">
          <!-- Visibility -->
          <div>
            <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">{{ t('Visibilità') }}</label>
            <div class="mb-flex mb-items-center mb-gap-2">
            <label v-for="vp in viewports" :key="vp.key" class="mb-flex mb-items-center mb-gap-1 mb-cursor-pointer" :title="(tileAdvanced['visible_' + vp.key] !== false ? t('Visibile su ') : t('Nascosto su ')) + vp.label">
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
          <CollapseSection :title="t('Visibilità condizionale')">
            <div class="mb-space-y-3">
              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Mostra solo a') }}</label>
                <FieldSelect
                  ui="dropdown"
                  :model-value="tileAdvanced.cond_user_role || ''"
                  :options="COND_USER_ROLE_OPTIONS"
                  @update:model-value="updateAdvanced('cond_user_role', $event)"
                />
              </div>
              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Mostra da data') }}</label>
                <input
                  type="datetime-local"
                  :value="tileAdvanced.cond_show_from || ''"
                  @change="updateAdvanced('cond_show_from', $event.target.value)"
                  class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
                />
              </div>
              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Nascondi dopo data') }}</label>
                <input
                  type="datetime-local"
                  :value="tileAdvanced.cond_show_until || ''"
                  @change="updateAdvanced('cond_show_until', $event.target.value)"
                  class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
                />
              </div>
              <div v-if="singlePostItems.length > 0">
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Mostra solo su queste strutture') }}</label>
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
                >{{ t('Rimuovi filtro strutture') }}</button>
              </div>
              <p class="mb-text-[10px] mb-text-gray-500">{{ t('Condizioni verificate server-side al momento del rendering.') }}</p>
            </div>
          </CollapseSection>

          <!-- A/B Testing -->
          <CollapseSection title="A/B Testing">
            <div class="mb-space-y-3">
              <!-- Loading -->
              <div v-if="abLoading" class="mb-text-xs mb-text-gray-400 mb-text-center mb-py-2">{{ t('Caricamento...') }}</div>

              <!-- No test: create button -->
              <template v-else-if="!abTest">
                <button
                  @click="createAbTest"
                  class="mb-w-full mb-bg-primary-600 mb-text-white mb-text-xs mb-font-medium mb-py-2 mb-px-3 mb-rounded-md hover:mb-bg-primary-700 mb-transition-colors"
                >{{ t('Crea test A/B') }}</button>
                <p class="mb-text-[10px] mb-text-gray-500">{{ t('Confronta due varianti di questa tile per scoprire quale converte meglio.') }}</p>
              </template>

              <!-- Test exists -->
              <template v-else>
                <!-- Name -->
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Nome test') }}</label>
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
                  >{{ abTest.status === 'running' ? t('Attivo') : abTest.status === 'stopped' ? t('Fermato') : t('Bozza') }}</span>
                </div>

                <!-- Variant B overrides (only in draft) -->
                <template v-if="abTest.status === 'draft'">
                  <div>
                    <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Variante B — proprietà da modificare') }}</label>
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
                    <FieldSelect
                      ui="dropdown"
                      model-value=""
                      :options="abOverrideAddOptions"
                      @update:model-value="addAbOverride($event)"
                    />
                  </div>

                  <!-- Goal type -->
                  <div>
                    <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Obiettivo conversione') }}</label>
                    <FieldSelect
                      ui="dropdown"
                      :model-value="abTest.goal_type || 'click'"
                      :options="AB_GOAL_TYPE_OPTIONS"
                      @update:model-value="updateAbField('goal_type', $event)"
                    />
                  </div>

                  <!-- Goal selector (optional) -->
                  <div>
                    <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Selettore CSS obiettivo (opzionale)') }}</label>
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
                        <div class="mb-text-[10px] mb-text-gray-500 mb-uppercase mb-font-bold">{{ t('Variante A') }}</div>
                        <div class="mb-text-lg mb-font-bold mb-text-gray-800">{{ abStats.variant_a?.conversion_rate || 0 }}%</div>
                        <div class="mb-text-[10px] mb-text-gray-400">{{ abStats.variant_a?.views || 0 }} {{ t('visite') }} · {{ abStats.variant_a?.conversions || 0 }} {{ t('conv.') }}</div>
                      </div>
                      <div class="mb-bg-gray-50 mb-rounded-lg mb-p-2 mb-text-center">
                        <div class="mb-text-[10px] mb-text-gray-500 mb-uppercase mb-font-bold">{{ t('Variante B') }}</div>
                        <div class="mb-text-lg mb-font-bold mb-text-gray-800">{{ abStats.variant_b?.conversion_rate || 0 }}%</div>
                        <div class="mb-text-[10px] mb-text-gray-400">{{ abStats.variant_b?.views || 0 }} {{ t('visite') }} · {{ abStats.variant_b?.conversions || 0 }} {{ t('conv.') }}</div>
                      </div>
                    </div>
                    <div v-if="abStats.significant" class="mb-text-[10px] mb-font-bold mb-text-center mb-py-1 mb-rounded mb-bg-green-50 mb-text-green-700">
                      {{ t('Significativo') }} (p={{ abStats.p_value }}) — {{ t('Vincitore:') }} {{ abStats.winner === 'a' ? t('A (originale)') : t('B (variante)') }}
                    </div>
                    <div v-else class="mb-text-[10px] mb-text-center mb-text-gray-400">
                      {{ t('Non ancora significativo') }}{{ abStats.p_value ? ' (p=' + abStats.p_value + ')' : '' }} — {{ t('servono più dati') }}
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
                  >{{ t('Avvia test') }}</button>
                  <button
                    v-if="abTest.status === 'running'"
                    @click="stopAbTest"
                    class="mb-flex-1 mb-bg-yellow-600 mb-text-white mb-text-xs mb-font-medium mb-py-1.5 mb-px-2 mb-rounded-md hover:mb-bg-yellow-700 mb-transition-colors"
                  >{{ t('Ferma test') }}</button>
                  <button
                    @click="deleteAbTest"
                    class="mb-bg-red-600 mb-text-white mb-text-xs mb-font-medium mb-py-1.5 mb-px-2 mb-rounded-md hover:mb-bg-red-700 mb-transition-colors"
                  >{{ t('Elimina') }}</button>
                </div>
              </template>
            </div>
          </CollapseSection>

            </div>
          </CollapseSection>

          <CollapseSection id="v2i-sec-adv-seo" :title="t('SEO & Accessibilità')" :macro="true">
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
                <FieldSelect
                  ui="dropdown"
                  :model-value="tileAdvanced.aria_role || ''"
                  :options="ARIA_ROLE_OPTIONS"
                  @update:model-value="updateAdvanced('aria_role', $event)"
                />
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
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Caricamento immagine') }}</label>
                <FieldSelect
                  ui="dropdown"
                  :model-value="tileAdvanced.img_loading || 'lazy'"
                  :options="IMG_LOADING_OPTIONS"
                  @update:model-value="updateAdvanced('img_loading', $event)"
                />
              </div>
              <!-- Fetch Priority -->
              <div v-if="tileHasImage">
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Fetch Priority</label>
                <FieldSelect
                  ui="dropdown"
                  :model-value="tileAdvanced.fetch_priority || 'auto'"
                  :options="FETCH_PRIORITY_OPTIONS"
                  @update:model-value="updateAdvanced('fetch_priority', $event)"
                />
              </div>
              <!-- Schema.org Type -->
              <div v-if="schemaOptions.length">
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Schema.org</label>
                <FieldSelect
                  ui="dropdown"
                  :model-value="tileAdvanced.schema_type || ''"
                  :options="schemaTypeOptions"
                  @update:model-value="updateAdvanced('schema_type', $event)"
                />
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
                <p class="mb-text-[10px] mb-text-gray-500 mb-mt-1">{{ t('Aggiunge data-key="value" al wrapper') }}</p>
              </div>
            </div>
          </CollapseSection>

          <!-- ===== MACRO: Effetti & Animazioni ===== -->
          <CollapseSection id="v2i-sec-adv-effects" :title="t('Effetti & Animazioni')" :macro="true">
            <div class="mb-space-y-3">
          <!-- Entrance Animation (olo-entrance-*) -->
          <CollapseSection :title="t('Animazione di ingresso')">
            <div class="mb-space-y-3">
              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Animazione') }}</label>
                <FieldSelect
                  ui="dropdown"
                  :model-value="selectedTile?.settings?.entrance_animation || 'none'"
                  :options="ENTRANCE_ANIMATION_OPTIONS"
                  @update:model-value="updateSetting('entrance_animation', $event)"
                />
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
                    <span class="mb-text-xs mb-text-gray-300">{{ t('Stagger figli') }}</span>
                  </label>
                  <p class="mb-text-[10px] mb-text-gray-500 mb-mt-0.5">{{ t("Anima i figli uno dopo l'altro con ritardo incrementale") }}</p>
                </div>
                <div v-if="selectedTile?.settings?.entrance_stagger">
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Ritardo stagger (ms)') }}</label>
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
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Durata (ms)') }}</label>
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
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Ritardo iniziale (ms)') }}</label>
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
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Intensità (×)') }}</label>
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
                  <p class="mb-text-[10px] mb-text-gray-500 mb-mt-0.5">{{ t("Scala distanze e dimensioni dell'animazione (1× = default)") }}</p>
                </div>
                <!-- Easing -->
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Curva di animazione') }}</label>
                  <FieldSelect
                    ui="dropdown"
                    :model-value="selectedTile?.settings?.entrance_easing || 'auto'"
                    :options="ENTRANCE_EASING_OPTIONS"
                    @update:model-value="updateSetting('entrance_easing', $event)"
                  />
                </div>
              </template>
            </div>
          </CollapseSection>

          <!-- Scrollspy -->
          <CollapseSection :title="t('Animazione allo scroll')">
            <div class="mb-space-y-3">
              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Animazione') }}</label>
                <FieldSelect
                  ui="dropdown"
                  :model-value="tileAdvanced.scrollspy_animation || ''"
                  :options="scrollspyAnimations"
                  @update:model-value="updateAdvanced('scrollspy_animation', $event)"
                />
              </div>
              <template v-if="tileAdvanced.scrollspy_animation">
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Ritardo (ms)') }}</label>
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
                    <span class="mb-text-xs mb-text-gray-300">{{ t('Ripeti ad ogni scroll') }}</span>
                  </label>
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Stagger figli (ms)') }}</label>
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
                  <p class="mb-text-[10px] mb-text-gray-500 mb-mt-0.5">{{ t('Anima i figli diretti in sequenza') }}</p>
                </div>
              </template>
            </div>
          </CollapseSection>

          <!-- Element Parallax -->
          <CollapseSection :title="t('Parallax allo scroll')">
            <template #header-right>
              <span class="mb-text-[10px] mb-mr-2" :class="hasElementParallax ? 'mb-text-primary-400' : 'mb-text-gray-500'">{{ hasElementParallax ? t('ATTIVO') : 'OFF' }}</span>
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
          <CollapseSection :title="t('Percorso Bezier allo scroll')" :headerRight="true">
            <template #header-right>
              <span class="mb-text-[10px] mb-mr-2" :class="tileAdvanced.bezier_path ? 'mb-text-primary-400' : 'mb-text-gray-500'">{{ tileAdvanced.bezier_path ? t('ATTIVO') : 'OFF' }}</span>
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
          <CollapseSection :title="t('Scroll fisso (sticky)')">
            <div class="mb-space-y-3">
              <p class="mb-text-[11px] mb-text-gray-400 mb-italic mb-leading-relaxed">
                {{ t("Mantiene questo elemento fermo mentre il resto della pagina scorre. Utile per immagini, sommari, CTA persistenti. Funziona solo se la sezione genitrice è più alta dell'elemento.") }}
              </p>

              <!-- Toggle attivazione -->
              <label class="mb-flex mb-items-center mb-justify-between mb-cursor-pointer mb-py-1">
                <span class="mb-text-xs mb-text-gray-300 mb-font-medium">{{ t('Attiva scroll fisso') }}</span>
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
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Posizione') }}</label>
                  <FieldSelect
                    ui="dropdown"
                    :model-value="tileAdvanced.sticky_position || 'top'"
                    :options="STICKY_POSITION_OPTIONS"
                    @update:model-value="updateAdvanced('sticky_position', $event)"
                  />
                </div>

                <!-- Offset -->
                <div>
                  <div class="mb-flex mb-items-center mb-justify-between mb-mb-1">
                    <label class="mb-text-xs mb-font-medium mb-text-gray-400">{{ t('Distanza dal bordo (px)') }}</label>
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
                  <span class="mb-text-xs mb-text-gray-300">{{ t('Attivo anche su mobile') }}</span>
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
          <CollapseSection :title="t('Effetti mouse')">
            <div class="mb-space-y-3">
              <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                <input type="checkbox" :checked="tileAdvanced.mouse_tilt === true" @change="updateAdvanced('mouse_tilt', $event.target.checked)" class="mb-accent-primary-500" />
                <span class="mb-text-xs mb-text-gray-300">{{ t('Tilt 3D al hover') }}</span>
              </label>
              <template v-if="tileAdvanced.mouse_tilt">
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Intensità:') }} {{ tileAdvanced.mouse_tilt_intensity || 15 }}</label>
                  <input type="range" min="5" max="30" step="1" :value="tileAdvanced.mouse_tilt_intensity || 15" @input="updateAdvanced('mouse_tilt_intensity', $event.target.value)" class="mb-w-full mb-accent-primary-500" />
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Applica a') }}</label>
                  <FieldSelect ui="segmented" :model-value="tileAdvanced.mouse_tilt_target || 'block'" :options="TILT_TARGET_OPTIONS" @update:model-value="updateAdvanced('mouse_tilt_target', $event)" />
                  <p class="mb-text-[10px] mb-text-gray-500 mb-leading-snug mb-mt-1">{{ t('"Foto interne": ogni immagine o video dentro la tile (gallerie, griglie) si inclina singolarmente.') }}</p>
                </div>
              </template>
              <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                <input type="checkbox" :checked="tileAdvanced.mouse_track === true" @change="updateAdvanced('mouse_track', $event.target.checked)" class="mb-accent-primary-500" />
                <span class="mb-text-xs mb-text-gray-300">{{ t('Segui cursore') }}</span>
              </label>
              <template v-if="tileAdvanced.mouse_track">
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Velocità:') }} {{ tileAdvanced.mouse_track_speed || 3 }}</label>
                  <input type="range" min="1" max="10" step="1" :value="tileAdvanced.mouse_track_speed || 3" @input="updateAdvanced('mouse_track_speed', $event.target.value)" class="mb-w-full mb-accent-primary-500" />
                </div>
              </template>

              <div class="mb-border-t mb-border-gray-700 mb-pt-3 mb-mt-1"></div>
              <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                <input type="checkbox" :checked="tileAdvanced.cursor_spotlight === true" @change="updateAdvanced('cursor_spotlight', $event.target.checked)" class="mb-accent-primary-500" />
                <span class="mb-text-xs mb-text-gray-300">{{ t('Spotlight cursore (torcia)') }}</span>
              </label>
              <p class="mb-text-[10px] mb-text-gray-500 mb-leading-snug">{{ t('Un disco-torcia segue il cursore e inverte i colori, confinato a questo elemento. Si disattiva su touch e con riduzione del movimento.') }}</p>
              <template v-if="tileAdvanced.cursor_spotlight">
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Inversione (blend)') }}</label>
                  <FieldSelect ui="dropdown" :model-value="tileAdvanced.cursor_spotlight_blend || 'difference'" :options="SPOTLIGHT_BLEND_OPTIONS" @update:model-value="updateAdvanced('cursor_spotlight_blend', $event)" />
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Colore luce') }}</label>
                  <input type="color" :value="tileAdvanced.cursor_spotlight_color || '#ffffff'" @input="updateAdvanced('cursor_spotlight_color', $event.target.value)" class="mb-w-full mb-h-8 mb-rounded mb-cursor-pointer" />
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Dimensione disco:') }} {{ tileAdvanced.cursor_spotlight_size || 300 }}px</label>
                  <input type="range" min="80" max="600" step="10" :value="tileAdvanced.cursor_spotlight_size || 300" @input="updateAdvanced('cursor_spotlight_size', $event.target.value)" class="mb-w-full mb-accent-primary-500" />
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Morbidezza bordo:') }} {{ tileAdvanced.cursor_spotlight_softness ?? 40 }}%</label>
                  <input type="range" min="0" max="100" step="5" :value="tileAdvanced.cursor_spotlight_softness ?? 40" @input="updateAdvanced('cursor_spotlight_softness', $event.target.value)" class="mb-w-full mb-accent-primary-500" />
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Inseguimento:') }} {{ tileAdvanced.cursor_spotlight_easing || 22 }}</label>
                  <input type="range" min="5" max="100" step="1" :value="tileAdvanced.cursor_spotlight_easing || 22" @input="updateAdvanced('cursor_spotlight_easing', $event.target.value)" class="mb-w-full mb-accent-primary-500" />
                </div>
              </template>

              <!-- Cursore magnetico — impostazione GLOBALE del sito (option
                   olo_magnetic_cursor), non della tile: esposta qui perché è
                   qui che si cercano le impostazioni del puntatore. -->
              <div class="mb-border-t mb-border-gray-700 mb-pt-3 mb-mt-1"></div>
              <div class="mb-flex mb-items-center mb-justify-between">
                <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                  <input type="checkbox" :checked="magCursor?.enabled === true" :disabled="!magLoaded" @change="updateMag('enabled', $event.target.checked)" class="mb-accent-primary-500" />
                  <span class="mb-text-xs mb-text-gray-300">{{ t('Cursore magnetico') }}</span>
                </label>
                <span class="mb-text-[9px] mb-uppercase mb-tracking-wide mb-text-gray-500 mb-bg-gray-800 mb-rounded mb-px-1.5 mb-py-0.5">{{ t('Globale sito') }}</span>
              </div>
              <p class="mb-text-[10px] mb-text-gray-500 mb-leading-snug">{{ t('Anello + dot che sostituiscono il puntatore su tutto il sito, con attrazione magnetica sugli elementi interattivi. Vale per tutte le pagine; nel canvas resta il cursore di sistema.') }}</p>
              <template v-if="magLoaded && magCursor?.enabled">
                <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                  <div>
                    <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Colore anello') }}</label>
                    <input type="color" :value="magCursor.ring_color" @input="updateMag('ring_color', $event.target.value)" class="mb-w-full mb-h-8 mb-rounded mb-cursor-pointer" />
                  </div>
                  <div>
                    <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Colore dot') }}</label>
                    <input type="color" :value="magCursor.dot_color" @input="updateMag('dot_color', $event.target.value)" class="mb-w-full mb-h-8 mb-rounded mb-cursor-pointer" />
                  </div>
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Dimensione anello') }}: {{ magCursor.ring_size }}px</label>
                  <input type="range" min="8" max="120" step="2" :value="magCursor.ring_size" @input="updateMag('ring_size', parseInt($event.target.value))" class="mb-w-full mb-accent-primary-500" />
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Ingrandimento su elementi') }}: ×{{ magCursor.hot_scale }}</label>
                  <input type="range" min="1" max="3" step="0.1" :value="magCursor.hot_scale" @input="updateMag('hot_scale', parseFloat($event.target.value))" class="mb-w-full mb-accent-primary-500" />
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Forza magnetica') }}: {{ magCursor.pull_strength }}</label>
                  <input type="range" min="0" max="1" step="0.05" :value="magCursor.pull_strength" @input="updateMag('pull_strength', parseFloat($event.target.value))" class="mb-w-full mb-accent-primary-500" />
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Fusione colore (blend)') }}</label>
                  <FieldSelect ui="dropdown" :model-value="magCursor.blend_mode" :options="MAG_BLEND_OPTIONS" @update:model-value="updateMag('blend_mode', $event)" />
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Elementi che attraggono') }}</label>
                  <div class="mb-grid mb-grid-cols-2 mb-gap-x-2 mb-gap-y-1.5">
                    <label v-for="p in MAG_TARGET_PRESETS" :key="p.key" class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                      <input type="checkbox" :checked="magPresetOn(p)" @change="toggleMagPreset(p, $event.target.checked)" class="mb-accent-primary-500" />
                      <span class="mb-text-xs mb-text-gray-300">{{ t(p.label) }}</span>
                    </label>
                  </div>
                  <label class="mb-block mb-text-[10px] mb-font-medium mb-text-gray-500 mb-mt-2 mb-mb-1">{{ t('Altri selettori CSS (avanzato)') }}</label>
                  <input type="text" :value="magCustomSel" @change="setMagCustomSel($event.target.value)" placeholder=".mia-classe, #mio-id" class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-xs mb-text-gray-900 mb-font-mono" />
                </div>
                <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                  <input type="checkbox" :checked="magCursor.hide_system === true" @change="updateMag('hide_system', $event.target.checked)" class="mb-accent-primary-500" />
                  <span class="mb-text-xs mb-text-gray-300">{{ t('Nascondi il cursore di sistema') }}</span>
                </label>
              </template>
              <p v-if="magLoaded" class="mb-text-[10px]" :class="magStatus === 'error' ? 'mb-text-red-400' : magStatus === 'saved' ? 'mb-text-green-500' : 'mb-text-gray-500'">
                {{ magStatus === 'saving' ? t('Salvataggio…')
                  : magStatus === 'error' ? t('Errore di salvataggio — riprova')
                  : magStatus === 'saved' ? t('✓ Salvato sul sito')
                  : t('Si salva da solo a ogni modifica — non serve il tasto Salva del template.') }}
              </p>

              <!-- HUD mirino — impostazione GLOBALE del sito (option olo_cursor_hud,
                   Olo_Cursor_Hud): crosshair full-viewport + coordinate px + label
                   sezione. Stessa famiglia del cursore magnetico. -->
              <div class="mb-border-t mb-border-gray-700 mb-pt-3 mb-mt-1"></div>
              <div class="mb-flex mb-items-center mb-justify-between">
                <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                  <input type="checkbox" :checked="hudCursor?.enabled === true" :disabled="!hudLoaded" @change="updateHud('enabled', $event.target.checked)" class="mb-accent-primary-500" />
                  <span class="mb-text-xs mb-text-gray-300">{{ t('HUD mirino (linee + coordinate)') }}</span>
                </label>
                <span class="mb-text-[9px] mb-uppercase mb-tracking-wide mb-text-gray-500 mb-bg-gray-800 mb-rounded mb-px-1.5 mb-py-0.5">{{ t('Globale sito') }}</span>
              </div>
              <p class="mb-text-[10px] mb-text-gray-500 mb-leading-snug">{{ t('Due linee a tutto schermo seguono il puntatore, con coordinate in pixel e nome della sezione corrente. Vale per tutte le pagine; si disattiva su touch e con riduzione del movimento.') }}</p>
              <template v-if="hudLoaded && hudCursor?.enabled">
                <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                  <input type="checkbox" :checked="hudCursor.show_coords === true" @change="updateHud('show_coords', $event.target.checked)" class="mb-accent-primary-500" />
                  <span class="mb-text-xs mb-text-gray-300">{{ t('Mostra coordinate (X · Y)') }}</span>
                </label>
                <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                  <input type="checkbox" :checked="hudCursor.show_label === true" @change="updateHud('show_label', $event.target.checked)" class="mb-accent-primary-500" />
                  <span class="mb-text-xs mb-text-gray-300">{{ t('Mostra nome sezione') }}</span>
                </label>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Colore linee') }}</label>
                  <FieldColor :modelValue="hudCursor.line_color" @update:modelValue="updateHud('line_color', $event)" />
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Colore coordinate') }}</label>
                  <FieldColor :modelValue="hudCursor.coords_color" @update:modelValue="updateHud('coords_color', $event)" />
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Colore nome sezione') }}</label>
                  <FieldColor :modelValue="hudCursor.label_color" @update:modelValue="updateHud('label_color', $event)" />
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Dimensione testo') }}: {{ hudCursor.font_size }}px</label>
                  <input type="range" min="7" max="24" step="1" :value="hudCursor.font_size" @input="updateHud('font_size', parseInt($event.target.value))" class="mb-w-full mb-accent-primary-500" />
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Distanza tag dal puntatore') }}: {{ hudCursor.tag_offset }}px</label>
                  <input type="range" min="0" max="80" step="2" :value="hudCursor.tag_offset" @input="updateHud('tag_offset', parseInt($event.target.value))" class="mb-w-full mb-accent-primary-500" />
                </div>
              </template>
              <p v-if="hudLoaded" class="mb-text-[10px]" :class="hudStatus === 'error' ? 'mb-text-red-400' : hudStatus === 'saved' ? 'mb-text-green-500' : 'mb-text-gray-500'">
                {{ hudStatus === 'saving' ? t('Salvataggio…')
                  : hudStatus === 'error' ? t('Errore di salvataggio — riprova')
                  : hudStatus === 'saved' ? t('✓ Salvato sul sito')
                  : t('Si salva da solo a ogni modifica — non serve il tasto Salva del template.') }}
              </p>
            </div>
          </CollapseSection>

          <!-- Infinite (Looping) Animations -->
          <CollapseSection :title="t('Animazione continua')">
            <div class="mb-space-y-3">
              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Animazione') }}</label>
                <FieldSelect ui="dropdown" :model-value="tileAdvanced.infinite_animation || 'none'" :options="INFINITE_ANIMATION_OPTIONS" @update:model-value="updateAdvanced('infinite_animation', $event)" />
              </div>
              <template v-if="(tileAdvanced.infinite_animation || 'none') !== 'none'">
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Velocità:') }} {{ tileAdvanced.infinite_speed || 3 }}s</label>
                  <input type="range" min="1" max="10" step="0.5" :value="tileAdvanced.infinite_speed || 3" @input="updateAdvanced('infinite_speed', $event.target.value)" class="mb-w-full mb-accent-primary-500" />
                </div>
                <div v-if="['float','float-rot','bounce'].includes(tileAdvanced.infinite_animation)">
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Ampiezza:') }} {{ tileAdvanced.infinite_amplitude || (tileAdvanced.infinite_animation === 'bounce' ? 15 : 12) }}px</label>
                  <input type="range" min="2" max="60" step="1" :value="tileAdvanced.infinite_amplitude || (tileAdvanced.infinite_animation === 'bounce' ? 15 : 12)" @input="updateAdvanced('infinite_amplitude', $event.target.value)" class="mb-w-full mb-accent-primary-500" />
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Ritardo:') }} {{ tileAdvanced.infinite_delay || 0 }}ms</label>
                  <input type="range" min="0" max="3000" step="100" :value="tileAdvanced.infinite_delay || 0" @input="updateAdvanced('infinite_delay', $event.target.value)" class="mb-w-full mb-accent-primary-500" />
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Direzione') }}</label>
                  <FieldSelect ui="dropdown" :model-value="tileAdvanced.infinite_direction || 'normal'" :options="INFINITE_DIRECTION_OPTIONS" @update:model-value="updateAdvanced('infinite_direction', $event)" />
                </div>
              </template>
            </div>
          </CollapseSection>

          <!-- CSS Mask / Clip-path -->
          <CollapseSection :title="t('Maschera forma')">
            <div class="mb-space-y-3">
              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Tipo maschera') }}</label>
                <FieldSelect ui="dropdown" :model-value="tileAdvanced.mask_type || 'none'" :options="MASK_TYPE_OPTIONS" @update:model-value="updateAdvanced('mask_type', $event)" />
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
          <CollapseSection id="v2i-sec-adv-dev" :title="t('Sviluppatore')" :macro="true">
            <div class="mb-space-y-3">
          <!-- Note editor (solo builder, non renderizzate nel frontend) -->
          <CollapseSection :title="t('Note editor')">
            <textarea
              :value="tileAdvanced.editor_note || ''"
              @input="updateAdvanced('editor_note', $event.target.value)"
              rows="3"
              :placeholder="t('Note visibili solo nel builder...')"
              class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-xs mb-text-gray-900 mb-resize-none"
            ></textarea>
            <p class="mb-text-[9px] mb-text-gray-500 mb-mt-1">{{ t('Queste note sono visibili solo nel builder e non vengono pubblicate.') }}</p>
          </CollapseSection>

          <!-- Custom JavaScript -->
          <CollapseSection :title="t('JavaScript personalizzato')">
            <textarea
              :value="tileAdvanced.custom_js || ''"
              @input="updateAdvanced('custom_js', $event.target.value)"
              rows="4"
              placeholder="// La variabile 'el' contiene l'elemento DOM"
              class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-xs mb-text-gray-900 mb-font-mono mb-resize-y"
              spellcheck="false"
            ></textarea>
            <p class="mb-text-[9px] mb-text-gray-500 mb-mt-1">{{ t('JS eseguito nel frontend. La variabile') }} <code style="background:#E5E7EB;padding:1px 4px;border-radius:3px">el</code> {{ t("contiene l'elemento DOM.") }}</p>
          </CollapseSection>

            </div>
          </CollapseSection>

          <CollapseSection id="v2i-sec-adv-position" :title="t('Posizionamento')" :macro="true">
            <div class="mb-space-y-3">
              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Larghezza elemento') }}</label>
                <FieldSelect ui="segmented" :model-value="tileAdvanced.tile_width || 'full'" :options="TILE_WIDTH_OPTIONS" @update:model-value="updateAdvanced('tile_width', $event)" />
                <p class="mb-text-[10px] mb-text-gray-500 mb-leading-snug mb-mt-1">{{ t('Adattata: la tile è larga quanto il suo contenuto; più tile adattate consecutive si affiancano sulla stessa riga (es. pulsanti vicini).') }}</p>
              </div>
              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Modalità') }}</label>
                <FieldSelect
                  ui="dropdown"
                  :model-value="tileAdvanced.position_mode || 'static'"
                  :options="POSITION_MODE_OPTIONS"
                  @update:model-value="updateAdvanced('position_mode', $event)"
                />
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
                    <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">{{ t('Larghezza') }} <span v-if="positionBp !== 'desktop'" class="mb-text-amber-400">{{ positionBpLabel }}</span></label>
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
                  {{ t('Valori: px (es. 100px), % (es. 50%), vh/vw. Assoluto è relativo alla sezione, Fisso alla finestra.') }}
                </p>
                <div v-if="tileAdvanced.position_mode === 'fixed'">
                  <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">{{ t('Nascondi al raggiungimento di') }}</label>
                  <input
                    type="text"
                    :value="tileAdvanced.position_hide_at ?? ''"
                    @change="updateAdvanced('position_hide_at', $event.target.value)"
                    :placeholder="t('HTML ID della sezione (es. fine-nav)')"
                    class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-0.5 mb-text-[11px] mb-text-gray-900"
                  />
                  <p class="mb-text-[9px] mb-text-gray-500 mb-mt-0.5">{{ t("L'elemento scompare quando lo scroll raggiunge la sezione con questo ID.") }}</p>
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
import { getElementDef, getElementFields, getElementDefaults } from '@/config/elementRegistry';
import { heroConvertTarget, convertHeroTile } from '@/utils/heroConvert';
import BackgroundControls from './BackgroundControls.vue';
import PageSettingsPanel from './PageSettingsPanel.vue';
import ContentItemsEditor from './ContentItemsEditor.vue';
import InspectorField from './InspectorField.vue';
import StyleFieldsRenderer from './StyleFieldsRenderer.vue';
import { styleFieldsBase } from '@/config/elements/_styleFieldsBase.js';
import { normalizeSearchQuery, fieldMatchesSearch, sectionLabelMatchesSearch, countSearchMatches } from '@/utils/inspectorSearch.js';
import FieldSpacing from './fields/FieldSpacing.vue';
import FieldSelect from './fields/FieldSelect.vue';
import FieldColor from './fields/FieldColor.vue';
import CollapseSection from './CollapseSection.vue';
import FieldBoxShadow from './fields/FieldBoxShadow.vue';
import FieldTransform from './fields/FieldTransform.vue';
import ParallaxEditor from './ParallaxEditor.vue';
import BezierPathEditor from './BezierPathEditor.vue';
import ProSliderEditor from '../ProSlider/ProSliderEditor.vue';
import HeightModeSelector from '../ProSlider/HeightModeSelector.vue';
import { TILE_PRESETS, BASE_THEME_PRESETS } from '@/config/tilePresets';
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

const COND_USER_ROLE_OPTIONS = [
  { value: '', label: 'Tutti (nessun filtro)' },
  { value: 'logged_in', label: 'Utenti autenticati' },
  { value: 'logged_out', label: 'Visitatori non autenticati' },
  { value: 'administrator', label: 'Amministratori' },
  { value: 'editor', label: 'Editor' },
  { value: 'author', label: 'Autori' },
  { value: 'subscriber', label: 'Subscriber' },
];

const AB_GOAL_TYPE_OPTIONS = [
  { value: 'click', label: 'Click sulla tile' },
  { value: 'submit', label: 'Invio form' },
];

const ARIA_ROLE_OPTIONS = [
  { value: '', label: 'Automatico' },
  { value: 'region', label: 'Region' },
  { value: 'navigation', label: 'Navigation' },
  { value: 'complementary', label: 'Complementary' },
  { value: 'banner', label: 'Banner' },
  { value: 'contentinfo', label: 'Content Info' },
  { value: 'main', label: 'Main' },
  { value: 'search', label: 'Search' },
  { value: 'form', label: 'Form' },
  { value: 'none', label: 'None (decorativo)' },
];

const IMG_LOADING_OPTIONS = [
  { value: 'lazy', label: 'Lazy (default — carica quando visibile)' },
  { value: 'eager', label: 'Eager (carica subito — per above the fold)' },
];

const FETCH_PRIORITY_OPTIONS = [
  { value: 'auto', label: 'Auto (default)' },
  { value: 'high', label: 'High (LCP — hero, slider, prima immagine)' },
  { value: 'low', label: 'Low (sotto il fold)' },
];

const ENTRANCE_ANIMATION_OPTIONS = [
  { value: 'none', label: 'Nessuna' },
  { value: 'fade', label: 'Dissolvenza' },
  { value: 'slide-up', label: 'Scorrimento dal basso' },
  { value: 'slide-left', label: 'Scorrimento da sinistra' },
  { value: 'slide-right', label: 'Scorrimento da destra' },
  { value: 'slide-down', label: "Scorrimento dall'alto" },
  { value: 'zoom-in', label: 'Zoom in' },
  { value: 'zoom-out', label: 'Zoom out' },
  { value: 'flip', label: 'Flip' },
  { value: 'rotate-in', label: 'Rotazione oraria' },
  { value: 'rotate-ccw', label: 'Rotazione antioraria' },
  { value: 'bounce', label: 'Rimbalzo' },
  { value: 'elastic', label: 'Elastico' },
  { value: 'blur-in', label: 'Sfocatura' },
  { value: 'swing', label: 'Oscillazione' },
  { value: 'rubber', label: 'Gomma' },
  { value: 'jello', label: 'Gelatina' },
  { value: 'back-in-left', label: 'Ritorno da sinistra' },
  { value: 'back-in-right', label: 'Ritorno da destra' },
  { value: 'typewriter', label: 'Macchina da scrivere' },
  { value: 'fade-up-big', label: 'Grande dissolvenza dal basso' },
  { value: 'fade-down-big', label: "Grande dissolvenza dall'alto" },
  { value: 'lightspeed-left', label: 'Velocità luce da sinistra' },
  { value: 'lightspeed-right', label: 'Velocità luce da destra' },
  { value: 'roll-in', label: 'Rotolamento in entrata' },
  { value: 'jack-in-box', label: 'Scatola sorpresa' },
  { value: 'hinge', label: 'Cardine che cade' },
  { value: 'flip-y', label: 'Capovolgimento asse Y' },
  { value: 'flip-x', label: 'Capovolgimento asse X' },
  { value: 'zoom-in-down', label: 'Zoom + discesa' },
  { value: 'zoom-in-up', label: 'Zoom + salita' },
  { value: 'bounce-left', label: 'Rimbalzo da sinistra' },
  { value: 'bounce-right', label: 'Rimbalzo da destra' },
  { value: 'skew-in', label: 'Distorsione in entrata' },
  { value: 'curtain-reveal', label: 'Effetto tendina' },
  { value: 'blur-zoom', label: 'Sfocatura + Zoom' },
];

const ENTRANCE_EASING_OPTIONS = [
  { value: 'auto', label: 'Automatica (per effetto)' },
  { value: 'linear', label: 'Lineare' },
  { value: 'ease', label: 'Ease (default)' },
  { value: 'ease-in', label: 'Ease in (parte lento)' },
  { value: 'ease-out', label: 'Ease out (finisce lento)' },
  { value: 'ease-in-out', label: 'Ease in-out' },
  { value: 'cubic-bezier(.34,1.56,.64,1)', label: 'Overshoot (rimbalzo)' },
  { value: 'cubic-bezier(.68,-.55,.27,1.55)', label: 'Bounce forte' },
  { value: 'cubic-bezier(.4,0,.2,1)', label: 'Material' },
];

const STICKY_POSITION_OPTIONS = [
  { value: 'top', label: 'In alto' },
  { value: 'bottom', label: 'In basso' },
];

const SPOTLIGHT_BLEND_OPTIONS = [
  { value: 'difference', label: 'Differenza' },
  { value: 'exclusion', label: 'Esclusione' },
  { value: 'screen', label: 'Schermo' },
  { value: 'overlay', label: 'Sovrapposizione' },
  { value: 'hard-light', label: 'Hard Light' },
];

const TILT_TARGET_OPTIONS = [
  { value: 'block', label: 'Blocco intero' },
  { value: 'items', label: 'Foto interne' },
];

const TILE_WIDTH_OPTIONS = [
  { value: 'full', label: 'Piena' },
  { value: 'inline', label: 'Contenuto' },
];

// ── Cursore magnetico (impostazione GLOBALE, option olo_magnetic_cursor) ──
// Whitelist allineata a Olo_Magnetic_Cursor::BLEND_MODES.
const MAG_BLEND_OPTIONS = [
  { value: 'normal', label: 'Normale' },
  { value: 'screen', label: 'Schermo' },
  { value: 'difference', label: 'Differenza' },
  { value: 'exclusion', label: 'Esclusione' },
  { value: 'overlay', label: 'Sovrapposizione' },
  { value: 'lighten', label: 'Schiarisci' },
  { value: 'multiply', label: 'Moltiplica' },
];

// Preset "elementi che attraggono": checkbox umane che compongono la stringa
// magnetic_selector (il formato salvato resta il selettore CSS, invariato).
// Un preset risulta spuntato se ALMENO UNO dei suoi token è nel selettore;
// spuntarlo aggiunge tutti i token, toglierlo li rimuove tutti.
const MAG_TARGET_PRESETS = [
  { key: 'links',    label: 'Link',          tokens: ['a'] },
  { key: 'buttons',  label: 'Bottoni',       tokens: ['button', '.olo-btn-link', 'input[type=submit]'] },
  { key: 'images',   label: 'Immagini',      tokens: ['img'] },
  { key: 'headings', label: 'Titoli',        tokens: ['h1', 'h2', 'h3'] },
  { key: 'fields',   label: 'Campi modulo',  tokens: ['input', 'textarea', 'select'] },
];

const magSelTokens = computed(() =>
  (magCursor.value?.magnetic_selector || '').split(',').map(s => s.trim()).filter(Boolean)
);
const magCustomSel = computed(() => {
  const known = new Set(MAG_TARGET_PRESETS.flatMap(p => p.tokens));
  return magSelTokens.value.filter(tk => !known.has(tk)).join(', ');
});

function magPresetOn(p) {
  return p.tokens.some(tk => magSelTokens.value.includes(tk));
}

function toggleMagPreset(p, on) {
  let tokens = magSelTokens.value.filter(tk => !p.tokens.includes(tk));
  if (on) tokens = tokens.concat(p.tokens);
  updateMag('magnetic_selector', tokens.join(', '));
}

function setMagCustomSel(value) {
  const known = new Set(MAG_TARGET_PRESETS.flatMap(p => p.tokens));
  const presetTokens = magSelTokens.value.filter(tk => known.has(tk));
  const custom = String(value).split(',').map(s => s.trim()).filter(Boolean);
  updateMag('magnetic_selector', presetTokens.concat(custom).join(', '));
}

const magCursor = ref(null);
const magLoaded = ref(false);
const magStatus = ref('');
let magSaveTimer = null;
let magStatusTimer = null;

// ⚠️ oloData.restUrl del BUILDER è senza trailing slash (a differenza della
// pagina cfg, che lo include): normalizziamo per non dipendere dal contesto.
const MAG_ENDPOINT = `${(window.oloData?.restUrl || '').replace(/\/$/, '')}/magnetic-cursor`;

async function loadMagneticCursor() {
  try {
    const res = await fetch(MAG_ENDPOINT, {
      headers: { 'X-WP-Nonce': window.oloData.nonce },
    });
    if (res.ok) {
      magCursor.value = await res.json();
      magLoaded.value = true;
    }
  } catch (e) { /* pannello resta disabilitato */ }
}

function updateMag(key, value) {
  if (!magCursor.value) return;
  magCursor.value[key] = value;
  clearTimeout(magSaveTimer);
  magSaveTimer = setTimeout(saveMagneticCursor, 600);
}

async function saveMagneticCursor() {
  magStatus.value = 'saving';
  try {
    const res = await fetch(MAG_ENDPOINT, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify(magCursor.value),
    });
    if (!res.ok) throw new Error();
    magCursor.value = await res.json();
    magStatus.value = 'saved';
  } catch (e) {
    magStatus.value = 'error';
  }
  clearTimeout(magStatusTimer);
  magStatusTimer = setTimeout(() => { magStatus.value = ''; }, 2000);
}

// ── HUD mirino (impostazione GLOBALE, option olo_cursor_hud) ──
// Stesso pattern del cursore magnetico: GET al mount, PUT debounced a ogni
// modifica, merge lato server (si può inviare anche solo `enabled`).
const hudCursor = ref(null);
const hudLoaded = ref(false);
const hudStatus = ref('');
let hudSaveTimer = null;
let hudStatusTimer = null;

const HUD_ENDPOINT = `${(window.oloData?.restUrl || '').replace(/\/$/, '')}/cursor-hud`;

async function loadCursorHud() {
  try {
    const res = await fetch(HUD_ENDPOINT, {
      headers: { 'X-WP-Nonce': window.oloData.nonce },
    });
    if (res.ok) {
      hudCursor.value = await res.json();
      hudLoaded.value = true;
    }
  } catch (e) { /* pannello resta disabilitato */ }
}

function updateHud(key, value) {
  if (!hudCursor.value) return;
  hudCursor.value[key] = value;
  clearTimeout(hudSaveTimer);
  hudSaveTimer = setTimeout(saveCursorHud, 600);
}

async function saveCursorHud() {
  hudStatus.value = 'saving';
  try {
    const res = await fetch(HUD_ENDPOINT, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify(hudCursor.value),
    });
    if (!res.ok) throw new Error();
    hudCursor.value = await res.json();
    hudStatus.value = 'saved';
  } catch (e) {
    hudStatus.value = 'error';
  }
  clearTimeout(hudStatusTimer);
  hudStatusTimer = setTimeout(() => { hudStatus.value = ''; }, 2000);
}

const INFINITE_ANIMATION_OPTIONS = [
  { value: 'none', label: 'Nessuna' },
  { value: 'float', label: 'Galleggiamento' },
  { value: 'float-rot', label: 'Galleggiamento + rotazione' },
  { value: 'pulse', label: 'Pulsazione' },
  { value: 'spin', label: 'Rotazione' },
  { value: 'wiggle', label: 'Dondolio' },
  { value: 'bounce', label: 'Rimbalzo' },
  { value: 'swing', label: 'Oscillazione' },
  { value: 'breathe', label: 'Respiro' },
];

const INFINITE_DIRECTION_OPTIONS = [
  { value: 'normal', label: 'Normale' },
  { value: 'alternate', label: 'Alternata' },
  { value: 'reverse', label: 'Inversa' },
];

const MASK_TYPE_OPTIONS = [
  { value: 'none', label: 'Nessuna' },
  { value: 'circle', label: 'Cerchio' },
  { value: 'ellipse', label: 'Ellisse' },
  { value: 'triangle', label: 'Triangolo' },
  { value: 'hexagon', label: 'Esagono' },
  { value: 'star', label: 'Stella' },
  { value: 'diamond', label: 'Diamante' },
  { value: 'blob', label: 'Blob' },
  { value: 'custom', label: 'Personalizzata' },
];

const POSITION_MODE_OPTIONS = [
  { value: 'static', label: 'Normale (nel flusso)' },
  { value: 'relative', label: 'Relativo (offset dal flusso)' },
  { value: 'absolute', label: 'Assoluto (libero nella sezione)' },
  { value: 'fixed', label: 'Fisso (libero nella pagina)' },
  { value: 'sticky', label: 'Sticky (fisso durante lo scroll)' },
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
const searchQ = computed(() => normalizeSearchQuery(settingsSearch.value));
const searchActive = computed(() => !!searchQ.value);
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

// ── Ricerca cross-tab ──
// Il tab Avanzate è markup statico (non data-driven): filtrare i suoi campi
// field-by-field richiederebbe di instrumentare ~50 blocchi hard-coded, fuori
// scope. Compromesso: indice statico delle label per il SOLO conteggio nel
// badge del tab — la ricerca segnala che il setting esiste lì, il tab poi
// mostra tutto. Tenere allineato se si aggiungono campi al tab Avanzate.
const ADV_SEARCH_LABELS = [
  'HTML ID', 'Classi CSS', 'CSS personalizzato',
  'Visibilità', 'Visibilità condizionale', 'Mostra solo a', 'Mostra da data',
  'Nascondi dopo data', 'Mostra solo su queste strutture',
  'A/B Testing', 'Nome test', 'Obiettivo conversione', 'Selettore CSS obiettivo',
  'Aria Label', 'Role', 'Link Rel', 'Link Title', 'Caricamento immagine',
  'Fetch Priority', 'Schema.org', 'Data Attributes',
  'Animazione di ingresso', 'Animazione allo scroll', 'Parallax allo scroll',
  'Percorso Bezier allo scroll', 'Scroll fisso (sticky)', 'Effetti mouse',
  'Animazione continua', 'Maschera forma', 'Tipo maschera', 'Clip-path CSS',
  'Ritardo stagger (ms)', 'Durata (ms)', 'Ritardo iniziale (ms)', 'Intensità (×)',
  'Curva di animazione', 'Ritardo (ms)', 'Stagger figli (ms)',
  'Posizione', 'Distanza dal bordo (px)', 'Inversione (blend)', 'Colore luce',
  'Direzione', 'Note editor', 'JavaScript personalizzato',
  'Posizionamento', 'Modalità', 'Nascondi al raggiungimento di',
];

// Conteggio match per tab — alimenta i badge sui tab non attivi e l'hint
// "Nessun risultato in questo tab".
const searchTabCounts = computed(() => {
  const q = searchQ.value;
  if (!q) return {};
  const styleFields = [
    ...styleFieldsBase(selectedTile.value?.type),
    ...(elementDef.value?.styleFields || []),
  ];
  return {
    Contenuto: countSearchMatches(elementFields.value, q),
    Stile:     countSearchMatches(styleFields, q),
    Avanzate:  ADV_SEARCH_LABELS.filter(l => sectionLabelMatchesSearch(l, q)).length,
  };
});

// Hint mostrato quando il tab corrente non ha match ma altri sì. Escluso il
// tab Avanzate attivo: lì i campi non vengono filtrati, "nessun risultato"
// sarebbe contraddetto dal pannello pieno.
const searchCrossTabHints = computed(() => {
  if (!searchActive.value || activeTab.value === 'Avanzate') return [];
  if ((searchTabCounts.value[activeTab.value] || 0) > 0) return [];
  return tabs
    .filter(tab => tab !== activeTab.value && (searchTabCounts.value[tab] || 0) > 0)
    .map(tab => ({ tab, count: searchTabCounts.value[tab] }));
});

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
onMounted(loadMagneticCursor);
onMounted(loadCursorHud);
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

// ── Fase 3 unificazione hero: conversione esplicita legacy → canonico ──
const heroConversionTarget = computed(() => {
  const tile = selectedTile.value;
  return tile ? heroConvertTarget(tile.type) : null;
});

function onConvertToHero() {
  const tile = selectedTile.value;
  if (!tile) return;
  const conv = convertHeroTile(tile.type, tile.settings, getElementDefaults);
  if (!conv) return;
  tilesStore.convertTileType(tile.id, conv.type, conv.settings, conv.styleBg);
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
const schemaTypeOptions = computed(() => [{ value: '', label: 'Nessuno' }, ...schemaOptions.value]);

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

function isFieldVisible(field, sectionLabel = null) {
  const settings = selectedTile.value?.settings || {};
  if (field.condition && !evaluateCondition(field.condition, settings)) return false;
  if (typeof field.show === 'function' && !field.show(settings)) return false;
  // Filter "show only modified": confronta valore corrente con default del tile config
  if (showOnlyModified.value && field.key) {
    const cur = settings[field.key];
    const def = (elementDef.value?.defaults || {})[field.key];
    if (isFieldDefault(cur, def)) return false;
  }
  // Search filter (helper condiviso col tab Stile): se la label della sezione
  // matcha, tutta la sezione resta visibile.
  if (searchQ.value) {
    if (sectionLabelMatchesSearch(sectionLabel, searchQ.value)) return true;
    return fieldMatchesSearch(field, searchQ.value);
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
  return section.fields.some(f => isFieldVisible(f, section.label));
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

const abOverrideAddOptions = computed(() => [
  { value: '', label: '+ Aggiungi proprietà...' },
  ...abAvailableFields.value.map(f => ({ value: f.key, label: f.label })),
]);

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
  const obj = { x: [], y: [], scale: [], rotate: [], opacity: [], blur: [], nomobile: false, easing: null, start: '', end: '' };
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
    obj.nomobile = adv.parallax_nomobile === true;
  }
  return obj;
});

// I preset stilistici dei tile (BASE_THEME_PRESETS + TILE_PRESETS, ~3.100 righe
// di dati puri) vivono in src/config/tilePresets.js dalla v1.4.388 (dieta del
// monolite). La logica di apply resta qui (applyTilePresetTheme).

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
    if (tile.type === 'hero-split') {
      // Wrapper esterno trasparente; i colori interni restano scelte dell'utente
      // (i default della split sono già leggibili su chiaro).
      tilesStore.applyStylePreset(tile.id, { bg: { type: 'none' } });
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
    if (presetValues.settings && typeof presetValues.settings === 'object') {
      // v1.4.386 — chiave speciale `_recolor` (preset hero-split): ricolora le righe
      // headline e i valori delle stats PRESERVANDO testi/corsivi dell'utente — i preset
      // non toccano mai i contenuti. Gli array ciclano sugli item esistenti. La chiave
      // viene consumata qui e NON persiste nei settings salvati.
      const settingsCopy = { ...presetValues.settings };
      const recolor = settingsCopy._recolor;
      delete settingsCopy._recolor;
      if (recolor && typeof recolor === 'object') {
        if (Array.isArray(tile.settings?.headline_lines) && Array.isArray(recolor.headline) && recolor.headline.length) {
          settingsCopy.headline_lines = tile.settings.headline_lines.map((line, i) => ({
            ...line, color: recolor.headline[i % recolor.headline.length],
          }));
        }
        if (Array.isArray(tile.settings?.stats) && Array.isArray(recolor.stats) && recolor.stats.length) {
          settingsCopy.stats = tile.settings.stats.map((st, i) => ({
            ...st, value_color: recolor.stats[i % recolor.stats.length],
          }));
        }
      }
      tilesStore.updateTile(tile.id, settingsCopy);
    }
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
    updateAdvanced('parallax', { x:[], y:[], scale:[], rotate:[], opacity:[], blur:[], nomobile:false });
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
  border-color: var(--olo-ui-accent, #e8622a);
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
.insp-filter-modified--active { color: var(--olo-ui-accent, #e8622a); }
.insp-filter-modified--active:hover { color: var(--olo-ui-accent, #e8622a); }

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
/* Fase 3 unificazione hero — banner di conversione tile legacy */
.insp-heroconvert {
  display: flex; align-items: center; gap: 10px; margin-bottom: 12px;
  padding: 10px 12px; border-radius: 8px;
  background: color-mix(in srgb, var(--olo-ui-accent, #e8622a) 10%, transparent);
  border: 1px solid color-mix(in srgb, var(--olo-ui-accent, #e8622a) 30%, transparent);
}
.insp-heroconvert-txt { flex: 1; font-size: 11.5px; line-height: 1.45; opacity: .92; }
.insp-heroconvert-btn {
  flex: none; padding: 7px 12px; border: 0; border-radius: 6px; cursor: pointer;
  font-size: 11.5px; font-weight: 700;
  background: var(--olo-ui-accent, #e8622a); color: #fff;
  transition: filter .15s;
}
.insp-heroconvert-btn:hover { filter: brightness(1.08); }
.insp-heroconvert-btn:focus-visible { outline: 2px solid var(--olo-ui-accent, #e8622a); outline-offset: 2px; }

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

/* Badge conteggio match ricerca sui tab non attivi */
.v2i-tab-count {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 16px;
  height: 16px;
  padding: 0 4px;
  margin-left: 5px;
  border-radius: 8px;
  font-size: 10px;
  font-weight: 700;
  line-height: 1;
  background: var(--olo-color-primary, #e1474f);
  color: #fff;
  vertical-align: middle;
}

/* Hint cross-tab: nessun match nel tab corrente, match altrove */
.v2i-search-crosshint {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 6px;
  padding: 8px 10px;
  margin-bottom: 12px;
  background: #f8fafc;
  border: 1px dashed #cbd5e1;
  border-radius: 8px;
  font-size: 12px;
  color: #64748b;
}
.v2i-search-crosshint button {
  border: 1px solid #e2e8f0;
  background: #fff;
  border-radius: 6px;
  padding: 2px 8px;
  font: inherit;
  font-size: 11px;
  font-weight: 600;
  color: #1e293b;
  cursor: pointer;
  transition: border-color 0.15s, color 0.15s;
}
.v2i-search-crosshint button:hover {
  border-color: var(--olo-color-primary, #e1474f);
  color: var(--olo-color-primary, #e1474f);
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
