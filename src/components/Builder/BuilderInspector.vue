<template>
  <transition name="slide">
    <div
      v-if="selectedTile || builderStore.pageSettingsOpen"
      class="mb-w-72 mb-bg-gray-800 mb-border-l mb-border-gray-700 mb-overflow-y-auto mb-shrink-0"
    >
      <div class="mb-p-4">
        <!-- Page Settings Panel -->
        <PageSettingsPanel v-if="builderStore.pageSettingsOpen && !selectedTile" />

        <!-- Tile Inspector -->
        <template v-if="selectedTile">
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
        <div class="mb-flex mb-items-center mb-justify-between mb-mb-4">
          <h3 class="mb-text-sm mb-font-semibold mb-text-gray-200">
            {{ t('Impostazioni') }} {{ elementDef ? t(elementDef.name) : selectedTile.type }}
          </h3>
          <button
            @click="builderStore.deselectTile()"
            class="mb-text-gray-500 hover:mb-text-gray-300 mb-text-lg"
          >
            &times;
          </button>
        </div>

        <!-- Settings search (all tabs) -->
        <div class="mb-relative mb-mb-3">
          <svg class="mb-absolute mb-left-2 mb-top-1/2 -mb-translate-y-1/2 mb-text-gray-500 mb-pointer-events-none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          <input
            v-model="inspectorSearch"
            type="text"
            :placeholder="t('Cerca impostazioni...')"
            class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-pl-8 mb-pr-7 mb-py-1.5 mb-text-xs mb-text-gray-300 focus:mb-border-primary-500 mb-outline-none mb-transition-colors"
          />
          <button v-if="inspectorSearch" @click="inspectorSearch = ''" class="mb-absolute mb-right-2 mb-top-1/2 -mb-translate-y-1/2 mb-text-gray-500 hover:mb-text-gray-300 mb-text-sm">&times;</button>
        </div>

        <!-- Tabs -->
        <div class="mb-flex mb-gap-1 mb-mb-4 mb-bg-gray-700 mb-rounded-lg mb-p-0.5">
          <button
            v-for="tab in tabs"
            :key="tab"
            @click="activeTab = tab"
            :class="[
              'mb-flex-1 mb-py-1.5 mb-text-xs mb-font-medium mb-rounded-md mb-transition-colors',
              activeTab === tab
                ? 'mb-bg-primary-600 mb-text-white'
                : 'mb-text-gray-400 hover:mb-text-gray-300'
            ]"
          >
            {{ t(tab) }}
          </button>
        </div>

        <!-- ============ Content tab (data-driven) ============ -->
        <div v-if="activeTab === 'Contenuto'" class="mb-space-y-3">
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
                      :modelValue="selectedTile.settings?.[field.key]"
                      :tileSettings="selectedTile.settings || {}"
                      :tileId="selectedTile.id"
                      :dynamic="selectedTile.dynamic || {}"
                      @update:modelValue="updateSetting(field.key, $event)"
                      @update:responsiveValue="updateSetting($event.key, $event.value)"
                      @update:attachmentId="updateSetting(field.key + '_id', $event)"
                      @update:dynamic="onDynamicFieldUpdate"
                    />
                  </template>
                </template>
              </template>

              <!-- Named section — collapsible, hidden if no visible fields -->
              <CollapseSection
                v-else-if="sectionHasVisibleFields(section)"
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
                      :modelValue="selectedTile.settings?.[field.key]"
                      :tileSettings="selectedTile.settings || {}"
                      :tileId="selectedTile.id"
                      :dynamic="selectedTile.dynamic || {}"
                      @update:modelValue="updateSetting(field.key, $event)"
                      @update:responsiveValue="updateSetting($event.key, $event.value)"
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
        <div v-else-if="activeTab === 'Stile'" class="mb-space-y-4">
          <!-- Normal / Hover toggle -->
          <div class="mb-flex mb-gap-1 mb-bg-gray-700 mb-rounded-lg mb-p-0.5">
            <button
              @click="styleState = 'normal'"
              :class="[
                'mb-flex-1 mb-py-1.5 mb-text-xs mb-font-medium mb-rounded-md mb-transition-colors',
                styleState === 'normal'
                  ? 'mb-bg-primary-600 mb-text-white'
                  : 'mb-text-gray-400 hover:mb-text-gray-300'
              ]"
            >{{ t('Normale') }}</button>
            <button
              @click="styleState = 'hover'"
              :class="[
                'mb-flex-1 mb-py-1.5 mb-text-xs mb-font-medium mb-rounded-md mb-transition-colors',
                styleState === 'hover'
                  ? 'mb-bg-amber-600 mb-text-white'
                  : 'mb-text-gray-400 hover:mb-text-gray-300'
              ]"
            >{{ t('Hover') }}</button>
          </div>

          <!-- === NORMAL state controls === -->
          <template v-if="styleState === 'normal'">
          <!-- Full Width -->
          <div v-show="matchSearch('larghezza piena', 'full width')">
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <button
                @click="updateStyle('full_width', !tileStyle.full_width)"
                :class="[
                  'mb-relative mb-w-10 mb-h-5 mb-rounded-full mb-transition-colors mb-shrink-0',
                  tileStyle.full_width ? 'mb-bg-primary-600' : 'mb-bg-gray-600'
                ]"
              >
                <span
                  :class="[
                    'mb-absolute mb-top-0.5 mb-w-4 mb-h-4 mb-rounded-full mb-bg-white mb-transition-transform',
                    tileStyle.full_width ? 'mb-left-5' : 'mb-left-0.5'
                  ]"
                ></span>
              </button>
              <span class="mb-text-xs mb-font-semibold mb-text-gray-300">{{ t('Larghezza piena') }}</span>
            </label>
          </div>

          <!-- Tile Width -->
          <InspectorField v-show="matchSearch('larghezza', 'width')"
            :field="{ key: 'tile_width', label: t('Larghezza'), type: 'text', responsive: true, placeholder: t('auto (es. 25%, 200px)') }"
            :modelValue="tileStyle.tile_width || ''"
            :tileSettings="tileStyle"
            @update:modelValue="updateStyle('tile_width', $event)"
            @update:responsiveValue="updateStyle($event.key, $event.value)"
          />

          <!-- Tile Max-Width -->
          <InspectorField v-show="matchSearch('larghezza massima', 'max width')"
            :field="{ key: 'tile_max_width', label: t('Larghezza massima'), type: 'text', responsive: true, placeholder: t('none (es. 600px, 80%)') }"
            :modelValue="tileStyle.tile_max_width || ''"
            :tileSettings="tileStyle"
            @update:modelValue="updateStyle('tile_max_width', $event)"
            @update:responsiveValue="updateStyle($event.key, $event.value)"
          />

          <!-- Tile Min-Height -->
          <InspectorField v-show="matchSearch('altezza minima', 'min height')"
            :field="{ key: 'tile_min_height', label: t('Altezza minima'), type: 'text', responsive: true, placeholder: 'auto (es. 300px, 50vh)' }"
            :modelValue="tileStyle.tile_min_height || ''"
            :tileSettings="tileStyle"
            @update:modelValue="updateStyle('tile_min_height', $event)"
            @update:responsiveValue="updateStyle($event.key, $event.value)"
          />

          <!-- Spacing breakpoint switcher -->
          <div v-show="matchSearch('margine', 'padding', 'spaziatura', 'margin', 'spacing')" class="mb-flex mb-gap-1 mb-bg-gray-700 mb-rounded-lg mb-p-0.5 mb-mb-1">
            <button
              v-for="bp in responsiveBreakpoints"
              :key="bp.key"
              @click="spacingBp = bp.key"
              :class="[
                'mb-flex-1 mb-py-1 mb-text-[10px] mb-font-medium mb-rounded-md mb-transition-colors mb-flex mb-items-center mb-justify-center mb-gap-1',
                spacingBp === bp.key
                  ? 'mb-bg-primary-600 mb-text-white'
                  : 'mb-text-gray-400 hover:mb-text-gray-300'
              ]"
              :title="bp.label"
              v-html="bp.icon"
            ></button>
          </div>

          <!-- Margin -->
          <div v-show="matchSearch('margine', 'margin', 'spaziatura')">
            <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">{{ t('Margine (px)') }} <span v-if="spacingBp !== 'desktop'" class="mb-text-amber-400 mb-text-[10px]">{{ spacingBpLabel }}</span></label>
            <FieldSpacing
              :modelValue="marginObj"
              @update:modelValue="onMarginUpdate"
              :max="200"
            />
          </div>

          <!-- Padding -->
          <div v-show="matchSearch('padding', 'spaziatura')">
            <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">{{ t('Padding (px)') }} <span v-if="spacingBp !== 'desktop'" class="mb-text-amber-400 mb-text-[10px]">{{ spacingBpLabel }}</span></label>
            <FieldSpacing
              :modelValue="paddingObj"
              @update:modelValue="onPaddingUpdate"
              :max="200"
            />
          </div>

          <!-- Background -->
          <div v-show="matchSearch('sfondo', 'background', 'pattern', 'gradiente')">
            <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">{{ t('Sfondo') }}</label>
            <BackgroundControls
              :modelValue="tileBg"
              :showParallax="true"
              @update:modelValue="onTileBgUpdate"
            />
          </div>

          <!-- Border Radius -->
          <div v-show="matchSearch('border radius', 'bordo', 'raggio', 'angoli')">
            <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">Border radius (px)</label>
            <FieldBorderRadius
              :modelValue="tileStyle.border_radius || 0"
              @update:modelValue="updateStyle('border_radius', $event)"
            />
          </div>

          <!-- Border -->
          <div v-show="matchSearch('bordo', 'border')">
            <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">Bordo</label>
            <div class="mb-space-y-2">
              <div class="mb-flex mb-gap-2">
                <input
                  type="number"
                  :value="tileStyle.border_width || 0"
                  @input="updateStyle('border_width', $event.target.value)"
                  placeholder="Larghezza"
                  min="0" max="20" step="1"
                  class="mb-w-16 mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900"
                />
                <select
                  :value="tileStyle.border_style || 'solid'"
                  @change="updateStyle('border_style', $event.target.value)"
                  class="mb-flex-1 mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900"
                >
                  <option value="solid">Continuo</option>
                  <option value="dashed">Tratteggiato</option>
                  <option value="dotted">Punteggiato</option>
                  <option value="double">Doppio</option>
                  <option value="groove">Incasso</option>
                  <option value="ridge">Rilievo</option>
                  <option value="inset">Inset</option>
                  <option value="outset">Outset</option>
                  <option value="none">Nessuno</option>
                </select>
              </div>
              <div class="mb-flex mb-gap-2">
                <input
                  type="color"
                  :value="tileStyle.border_color || '#374151'"
                  @input="updateStyle('border_color', $event.target.value)"
                  class="mb-w-8 mb-h-8 mb-rounded mb-cursor-pointer mb-border-0"
                />
                <input
                  type="text"
                  :value="tileStyle.border_color || '#374151'"
                  @change="updateStyle('border_color', $event.target.value)"
                  class="mb-flex-1 mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900"
                />
              </div>
            </div>
          </div>

          <!-- Box Shadow -->
          <div v-show="matchSearch('ombra', 'shadow')">
            <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">Ombra</label>
            <select
              :value="tileStyle.shadow || 'none'"
              @change="updateStyle('shadow', $event.target.value)"
              class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
            >
              <option value="none">Nessuna</option>
              <option value="sm">Piccola</option>
              <option value="md">Media</option>
              <option value="lg">Grande</option>
              <option value="xl">Extra grande</option>
              <option value="custom">Personalizzata</option>
            </select>
            <FieldBoxShadow
              v-if="tileStyle.shadow === 'custom'"
              :modelValue="tileStyle.shadow_custom || {}"
              @update:modelValue="updateStyle('shadow_custom', $event)"
              class="mb-mt-2"
            />
          </div>

          <!-- Opacity -->
          <div v-show="matchSearch('opacità', 'opacity')">
            <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">Opacità</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <input
                type="range"
                :value="tileStyle.opacity || 100"
                @input="updateStyle('opacity', $event.target.value)"
                min="0" max="100" step="5"
                class="mb-flex-1"
              />
              <span class="mb-text-xs mb-text-gray-400 mb-w-10 mb-text-right">{{ tileStyle.opacity || 100 }}%</span>
            </div>
          </div>

          <!-- Transform -->
          <div v-show="matchSearch('trasformazione', 'transform')">
            <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">Trasformazione</label>
            <FieldTransform
              :modelValue="tileStyle.transform || {}"
              @update:modelValue="updateStyle('transform', $event)"
            />
          </div>

          <!-- Text Shadow -->
          <div v-show="matchSearch('ombra testo', 'text shadow')">
            <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">Ombra testo</label>
            <div class="mb-flex mb-gap-2">
              <div class="mb-flex-1">
                <label class="mb-text-[10px] mb-text-gray-400">H</label>
                <input type="number" :value="tileStyle.text_shadow_h || 0" @input="updateStyle('text_shadow_h', parseInt($event.target.value))"
                  class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-1 mb-text-xs mb-text-gray-900" />
              </div>
              <div class="mb-flex-1">
                <label class="mb-text-[10px] mb-text-gray-400">V</label>
                <input type="number" :value="tileStyle.text_shadow_v || 0" @input="updateStyle('text_shadow_v', parseInt($event.target.value))"
                  class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-1 mb-text-xs mb-text-gray-900" />
              </div>
              <div class="mb-flex-1">
                <label class="mb-text-[10px] mb-text-gray-400">Blur</label>
                <input type="number" :value="tileStyle.text_shadow_blur || 0" @input="updateStyle('text_shadow_blur', parseInt($event.target.value))" min="0"
                  class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-1 mb-text-xs mb-text-gray-900" />
              </div>
              <div class="mb-shrink-0">
                <label class="mb-text-[10px] mb-text-gray-400">Colore</label>
                <input type="color" :value="tileStyle.text_shadow_color || '#000000'" @input="updateStyle('text_shadow_color', $event.target.value)"
                  class="mb-w-8 mb-h-7 mb-rounded mb-border-0 mb-cursor-pointer" />
              </div>
            </div>
          </div>

          <!-- Backdrop Filter -->
          <div v-show="matchSearch('filtro sfondo', 'glassmorphism', 'backdrop', 'blur')">
            <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">Filtro sfondo (glassmorphism)</label>
            <div class="mb-space-y-2">
              <div class="mb-flex mb-items-center mb-gap-2">
                <label class="mb-text-[10px] mb-text-gray-400 mb-w-16">Blur</label>
                <input type="range" :value="tileStyle.backdrop_blur || 0" @input="updateStyle('backdrop_blur', parseInt($event.target.value))" min="0" max="30"
                  class="mb-flex-1" />
                <span class="mb-text-xs mb-text-gray-400 mb-w-10 mb-text-right">{{ tileStyle.backdrop_blur || 0 }}px</span>
              </div>
              <div class="mb-flex mb-items-center mb-gap-2">
                <label class="mb-text-[10px] mb-text-gray-400 mb-w-16">Luminosità</label>
                <input type="range" :value="tileStyle.backdrop_brightness || 100" @input="updateStyle('backdrop_brightness', parseInt($event.target.value))" min="0" max="200" step="5"
                  class="mb-flex-1" />
                <span class="mb-text-xs mb-text-gray-400 mb-w-10 mb-text-right">{{ tileStyle.backdrop_brightness || 100 }}%</span>
              </div>
              <div class="mb-flex mb-items-center mb-gap-2">
                <label class="mb-text-[10px] mb-text-gray-400 mb-w-16">Saturazione</label>
                <input type="range" :value="tileStyle.backdrop_saturate || 100" @input="updateStyle('backdrop_saturate', parseInt($event.target.value))" min="0" max="200" step="5"
                  class="mb-flex-1" />
                <span class="mb-text-xs mb-text-gray-400 mb-w-10 mb-text-right">{{ tileStyle.backdrop_saturate || 100 }}%</span>
              </div>
            </div>
          </div>

          <!-- Overflow -->
          <div v-show="matchSearch('overflow')">
            <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">Overflow</label>
            <select
              :value="tileStyle.overflow || 'visible'"
              @change="updateStyle('overflow', $event.target.value)"
              class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
            >
              <option value="visible">Visibile</option>
              <option value="hidden">Nascosto</option>
              <option value="auto">Auto (scroll)</option>
            </select>
          </div>

          <!-- Mask -->
          <div v-show="matchSearch('maschera', 'mask')">
            <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">Maschera</label>
            <select
              :value="tileStyle.mask || 'none'"
              @change="updateStyle('mask', $event.target.value)"
              class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
            >
              <option value="none">Nessuna</option>
              <option value="circle">Cerchio</option>
              <option value="triangle">Triangolo</option>
              <option value="diamond">Diamante</option>
              <option value="hexagon">Esagono</option>
              <option value="star">Stella</option>
              <option value="blob">Blob</option>
              <option value="wave">Onda</option>
            </select>
          </div>
          </template>

          <!-- === HOVER state controls === -->
          <template v-else>
          <!-- Background Color -->
          <div>
            <div class="mb-flex mb-items-center mb-justify-between mb-mb-1">
              <label class="mb-text-xs mb-font-semibold mb-text-gray-300">Colore sfondo</label>
              <button v-if="tileHover.bg_color" @click="resetHover('bg_color')" class="mb-text-gray-500 hover:mb-text-gray-300 mb-text-xs" title="Ripristina">&times;</button>
            </div>
            <div class="mb-flex mb-gap-2">
              <input
                type="color"
                :value="tileHover.bg_color || '#000000'"
                @input="updateHover('bg_color', $event.target.value)"
                class="mb-w-8 mb-h-8 mb-rounded mb-cursor-pointer mb-border-0"
              />
              <input
                type="text"
                :value="tileHover.bg_color || ''"
                @change="updateHover('bg_color', $event.target.value)"
                placeholder="Nessun override"
                class="mb-flex-1 mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900"
              />
            </div>
          </div>

          <!-- Text Color -->
          <div>
            <div class="mb-flex mb-items-center mb-justify-between mb-mb-1">
              <label class="mb-text-xs mb-font-semibold mb-text-gray-300">Colore testo</label>
              <button v-if="tileHover.text_color" @click="resetHover('text_color')" class="mb-text-gray-500 hover:mb-text-gray-300 mb-text-xs" title="Ripristina">&times;</button>
            </div>
            <div class="mb-flex mb-gap-2">
              <input
                type="color"
                :value="tileHover.text_color || '#000000'"
                @input="updateHover('text_color', $event.target.value)"
                class="mb-w-8 mb-h-8 mb-rounded mb-cursor-pointer mb-border-0"
              />
              <input
                type="text"
                :value="tileHover.text_color || ''"
                @change="updateHover('text_color', $event.target.value)"
                placeholder="Nessun override"
                class="mb-flex-1 mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900"
              />
            </div>
          </div>

          <!-- Border Color -->
          <div>
            <div class="mb-flex mb-items-center mb-justify-between mb-mb-1">
              <label class="mb-text-xs mb-font-semibold mb-text-gray-300">Colore bordo</label>
              <button v-if="tileHover.border_color" @click="resetHover('border_color')" class="mb-text-gray-500 hover:mb-text-gray-300 mb-text-xs" title="Ripristina">&times;</button>
            </div>
            <div class="mb-flex mb-gap-2">
              <input
                type="color"
                :value="tileHover.border_color || '#374151'"
                @input="updateHover('border_color', $event.target.value)"
                class="mb-w-8 mb-h-8 mb-rounded mb-cursor-pointer mb-border-0"
              />
              <input
                type="text"
                :value="tileHover.border_color || ''"
                @change="updateHover('border_color', $event.target.value)"
                placeholder="Nessun override"
                class="mb-flex-1 mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900"
              />
            </div>
          </div>

          <!-- Box Shadow -->
          <div>
            <div class="mb-flex mb-items-center mb-justify-between mb-mb-1">
              <label class="mb-text-xs mb-font-semibold mb-text-gray-300">Ombra</label>
              <button v-if="tileHover.shadow" @click="resetHover('shadow')" class="mb-text-gray-500 hover:mb-text-gray-300 mb-text-xs" title="Ripristina">&times;</button>
            </div>
            <select
              :value="tileHover.shadow || ''"
              @change="updateHover('shadow', $event.target.value)"
              class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
            >
              <option value="">Nessun override</option>
              <option value="none">Nessuna</option>
              <option value="sm">Piccola</option>
              <option value="md">Media</option>
              <option value="lg">Grande</option>
              <option value="xl">Extra grande</option>
              <option value="custom">Personalizzata</option>
            </select>
            <FieldBoxShadow
              v-if="tileHover.shadow === 'custom'"
              :modelValue="tileHover.shadow_custom || {}"
              @update:modelValue="updateHover('shadow_custom', $event)"
              class="mb-mt-2"
            />
          </div>

          <!-- Opacity -->
          <div>
            <div class="mb-flex mb-items-center mb-justify-between mb-mb-1">
              <label class="mb-text-xs mb-font-semibold mb-text-gray-300">Opacità</label>
              <button v-if="tileHover.opacity != null" @click="resetHover('opacity')" class="mb-text-gray-500 hover:mb-text-gray-300 mb-text-xs" title="Ripristina">&times;</button>
            </div>
            <div class="mb-flex mb-items-center mb-gap-2">
              <input
                type="range"
                :value="tileHover.opacity ?? 100"
                @input="updateHover('opacity', parseInt($event.target.value))"
                min="0" max="100" step="5"
                class="mb-flex-1"
              />
              <span class="mb-text-xs mb-text-gray-400 mb-w-10 mb-text-right">{{ tileHover.opacity ?? '-' }}%</span>
            </div>
          </div>

          <!-- Transform Scale -->
          <div>
            <div class="mb-flex mb-items-center mb-justify-between mb-mb-1">
              <label class="mb-text-xs mb-font-semibold mb-text-gray-300">Scala</label>
              <button v-if="tileHover.transform_scale != null" @click="resetHover('transform_scale')" class="mb-text-gray-500 hover:mb-text-gray-300 mb-text-xs" title="Ripristina">&times;</button>
            </div>
            <div class="mb-flex mb-items-center mb-gap-2">
              <input
                type="range"
                :value="tileHover.transform_scale ?? 1"
                @input="updateHover('transform_scale', parseFloat($event.target.value))"
                min="0.5" max="1.5" step="0.05"
                class="mb-flex-1"
              />
              <span class="mb-text-xs mb-text-gray-400 mb-w-10 mb-text-right">{{ tileHover.transform_scale ?? '-' }}</span>
            </div>
          </div>

          <!-- Transform TranslateY -->
          <div>
            <div class="mb-flex mb-items-center mb-justify-between mb-mb-1">
              <label class="mb-text-xs mb-font-semibold mb-text-gray-300">Traslazione Y (px)</label>
              <button v-if="tileHover.transform_translateY != null" @click="resetHover('transform_translateY')" class="mb-text-gray-500 hover:mb-text-gray-300 mb-text-xs" title="Ripristina">&times;</button>
            </div>
            <div class="mb-flex mb-items-center mb-gap-2">
              <input
                type="range"
                :value="tileHover.transform_translateY ?? 0"
                @input="updateHover('transform_translateY', parseInt($event.target.value))"
                min="-50" max="50" step="1"
                class="mb-flex-1"
              />
              <span class="mb-text-xs mb-text-gray-400 mb-w-10 mb-text-right">{{ tileHover.transform_translateY ?? '-' }}px</span>
            </div>
          </div>

          <!-- Transform Rotate -->
          <div>
            <div class="mb-flex mb-items-center mb-justify-between mb-mb-1">
              <label class="mb-text-xs mb-font-semibold mb-text-gray-300">Rotazione (deg)</label>
              <button v-if="tileHover.transform_rotate != null" @click="resetHover('transform_rotate')" class="mb-text-gray-500 hover:mb-text-gray-300 mb-text-xs" title="Ripristina">&times;</button>
            </div>
            <div class="mb-flex mb-items-center mb-gap-2">
              <input
                type="range"
                :value="tileHover.transform_rotate ?? 0"
                @input="updateHover('transform_rotate', parseInt($event.target.value))"
                min="-180" max="180" step="1"
                class="mb-flex-1"
              />
              <span class="mb-text-xs mb-text-gray-400 mb-w-10 mb-text-right">{{ tileHover.transform_rotate ?? '-' }}&deg;</span>
            </div>
          </div>

          <!-- Transform TranslateX -->
          <div>
            <div class="mb-flex mb-items-center mb-justify-between mb-mb-1">
              <label class="mb-text-xs mb-font-semibold mb-text-gray-300">Traslazione X (px)</label>
              <button v-if="tileHover.transform_translateX != null" @click="resetHover('transform_translateX')" class="mb-text-gray-500 hover:mb-text-gray-300 mb-text-xs" title="Ripristina">&times;</button>
            </div>
            <div class="mb-flex mb-items-center mb-gap-2">
              <input
                type="range"
                :value="tileHover.transform_translateX ?? 0"
                @input="updateHover('transform_translateX', parseInt($event.target.value))"
                min="-50" max="50" step="1"
                class="mb-flex-1"
              />
              <span class="mb-text-xs mb-text-gray-400 mb-w-10 mb-text-right">{{ tileHover.transform_translateX ?? '-' }}px</span>
            </div>
          </div>

          <!-- Transform SkewX/Y -->
          <div>
            <div class="mb-flex mb-items-center mb-justify-between mb-mb-1">
              <label class="mb-text-xs mb-font-semibold mb-text-gray-300">Skew (deg)</label>
            </div>
            <div class="mb-flex mb-gap-2">
              <div class="mb-flex-1">
                <label class="mb-text-[10px] mb-text-gray-400">X</label>
                <div class="mb-flex mb-items-center mb-gap-1">
                  <input type="number" :value="tileHover.transform_skewX ?? 0" @input="updateHover('transform_skewX', parseInt($event.target.value))" min="-45" max="45"
                    class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-1 mb-text-xs mb-text-gray-900" />
                </div>
              </div>
              <div class="mb-flex-1">
                <label class="mb-text-[10px] mb-text-gray-400">Y</label>
                <div class="mb-flex mb-items-center mb-gap-1">
                  <input type="number" :value="tileHover.transform_skewY ?? 0" @input="updateHover('transform_skewY', parseInt($event.target.value))" min="-45" max="45"
                    class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-1 mb-text-xs mb-text-gray-900" />
                </div>
              </div>
            </div>
          </div>

          <!-- Hover Text Shadow -->
          <div>
            <div class="mb-flex mb-items-center mb-justify-between mb-mb-1">
              <label class="mb-text-xs mb-font-semibold mb-text-gray-300">Ombra testo</label>
              <button v-if="tileHover.text_shadow_h != null" @click="resetHover('text_shadow_h'); resetHover('text_shadow_v'); resetHover('text_shadow_blur'); resetHover('text_shadow_color')" class="mb-text-gray-500 hover:mb-text-gray-300 mb-text-xs" title="Ripristina">&times;</button>
            </div>
            <div class="mb-flex mb-gap-2">
              <div class="mb-flex-1">
                <label class="mb-text-[10px] mb-text-gray-400">H</label>
                <input type="number" :value="tileHover.text_shadow_h ?? ''" @input="updateHover('text_shadow_h', parseInt($event.target.value))"
                  placeholder="-" class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-1 mb-text-xs mb-text-gray-900" />
              </div>
              <div class="mb-flex-1">
                <label class="mb-text-[10px] mb-text-gray-400">V</label>
                <input type="number" :value="tileHover.text_shadow_v ?? ''" @input="updateHover('text_shadow_v', parseInt($event.target.value))"
                  placeholder="-" class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-1 mb-text-xs mb-text-gray-900" />
              </div>
              <div class="mb-flex-1">
                <label class="mb-text-[10px] mb-text-gray-400">Blur</label>
                <input type="number" :value="tileHover.text_shadow_blur ?? ''" @input="updateHover('text_shadow_blur', parseInt($event.target.value))" min="0"
                  placeholder="-" class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-1 mb-text-xs mb-text-gray-900" />
              </div>
              <div class="mb-shrink-0">
                <label class="mb-text-[10px] mb-text-gray-400">Colore</label>
                <input type="color" :value="tileHover.text_shadow_color || '#000000'" @input="updateHover('text_shadow_color', $event.target.value)"
                  class="mb-w-8 mb-h-7 mb-rounded mb-border-0 mb-cursor-pointer" />
              </div>
            </div>
          </div>

          <!-- Border Radius -->
          <div>
            <div class="mb-flex mb-items-center mb-justify-between mb-mb-1">
              <label class="mb-text-xs mb-font-semibold mb-text-gray-300">Border radius</label>
              <button v-if="tileHover.border_radius != null" @click="resetHover('border_radius')" class="mb-text-gray-500 hover:mb-text-gray-300 mb-text-xs" title="Ripristina">&times;</button>
            </div>
            <FieldBorderRadius
              :modelValue="tileHover.border_radius ?? ''"
              @update:modelValue="updateHover('border_radius', $event)"
            />
          </div>

          <!-- CSS Filters -->
          <div>
            <div class="mb-flex mb-items-center mb-justify-between mb-mb-1">
              <label class="mb-text-xs mb-font-semibold mb-text-gray-300">Filtri CSS</label>
              <button v-if="tileHover.filter_blur != null || tileHover.filter_brightness != null || tileHover.filter_saturate != null || tileHover.filter_contrast != null || tileHover.filter_grayscale != null || tileHover.filter_sepia != null"
                @click="resetHover('filter_blur'); resetHover('filter_brightness'); resetHover('filter_saturate'); resetHover('filter_contrast'); resetHover('filter_grayscale'); resetHover('filter_sepia')"
                class="mb-text-gray-500 hover:mb-text-gray-300 mb-text-xs" title="Ripristina">&times;</button>
            </div>
            <div class="mb-space-y-2">
              <div class="mb-flex mb-items-center mb-gap-2">
                <label class="mb-text-[10px] mb-text-gray-400 mb-w-16">Blur</label>
                <input type="range" :value="tileHover.filter_blur ?? 0" @input="updateHover('filter_blur', parseInt($event.target.value))" min="0" max="20"
                  class="mb-flex-1" />
                <span class="mb-text-xs mb-text-gray-400 mb-w-10 mb-text-right">{{ tileHover.filter_blur ?? '-' }}px</span>
              </div>
              <div class="mb-flex mb-items-center mb-gap-2">
                <label class="mb-text-[10px] mb-text-gray-400 mb-w-16">Luminosità</label>
                <input type="range" :value="tileHover.filter_brightness ?? 100" @input="updateHover('filter_brightness', parseInt($event.target.value))" min="0" max="200" step="5"
                  class="mb-flex-1" />
                <span class="mb-text-xs mb-text-gray-400 mb-w-10 mb-text-right">{{ tileHover.filter_brightness ?? '-' }}%</span>
              </div>
              <div class="mb-flex mb-items-center mb-gap-2">
                <label class="mb-text-[10px] mb-text-gray-400 mb-w-16">Contrasto</label>
                <input type="range" :value="tileHover.filter_contrast ?? 100" @input="updateHover('filter_contrast', parseInt($event.target.value))" min="0" max="200" step="5"
                  class="mb-flex-1" />
                <span class="mb-text-xs mb-text-gray-400 mb-w-10 mb-text-right">{{ tileHover.filter_contrast ?? '-' }}%</span>
              </div>
              <div class="mb-flex mb-items-center mb-gap-2">
                <label class="mb-text-[10px] mb-text-gray-400 mb-w-16">Saturazione</label>
                <input type="range" :value="tileHover.filter_saturate ?? 100" @input="updateHover('filter_saturate', parseInt($event.target.value))" min="0" max="200" step="5"
                  class="mb-flex-1" />
                <span class="mb-text-xs mb-text-gray-400 mb-w-10 mb-text-right">{{ tileHover.filter_saturate ?? '-' }}%</span>
              </div>
              <div class="mb-flex mb-items-center mb-gap-2">
                <label class="mb-text-[10px] mb-text-gray-400 mb-w-16">Scala grigi</label>
                <input type="range" :value="tileHover.filter_grayscale ?? 0" @input="updateHover('filter_grayscale', parseInt($event.target.value))" min="0" max="100"
                  class="mb-flex-1" />
                <span class="mb-text-xs mb-text-gray-400 mb-w-10 mb-text-right">{{ tileHover.filter_grayscale ?? '-' }}%</span>
              </div>
              <div class="mb-flex mb-items-center mb-gap-2">
                <label class="mb-text-[10px] mb-text-gray-400 mb-w-16">Seppia</label>
                <input type="range" :value="tileHover.filter_sepia ?? 0" @input="updateHover('filter_sepia', parseInt($event.target.value))" min="0" max="100"
                  class="mb-flex-1" />
                <span class="mb-text-xs mb-text-gray-400 mb-w-10 mb-text-right">{{ tileHover.filter_sepia ?? '-' }}%</span>
              </div>
            </div>
          </div>

          <!-- Hover Backdrop Filter -->
          <div>
            <div class="mb-flex mb-items-center mb-justify-between mb-mb-1">
              <label class="mb-text-xs mb-font-semibold mb-text-gray-300">Filtro sfondo</label>
              <button v-if="tileHover.backdrop_blur != null" @click="resetHover('backdrop_blur')" class="mb-text-gray-500 hover:mb-text-gray-300 mb-text-xs" title="Ripristina">&times;</button>
            </div>
            <div class="mb-flex mb-items-center mb-gap-2">
              <label class="mb-text-[10px] mb-text-gray-400 mb-w-10">Blur</label>
              <input type="range" :value="tileHover.backdrop_blur ?? 0" @input="updateHover('backdrop_blur', parseInt($event.target.value))" min="0" max="30"
                class="mb-flex-1" />
              <span class="mb-text-xs mb-text-gray-400 mb-w-10 mb-text-right">{{ tileHover.backdrop_blur ?? '-' }}px</span>
            </div>
          </div>

          <!-- Separator -->
          <div class="mb-border-t mb-border-gray-500 mb-pt-3">
            <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">Transizione</label>
            <!-- Duration -->
            <div class="mb-mb-2">
              <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Durata (ms)</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <input
                  type="range"
                  :value="tileTransition.duration"
                  @input="updateTransition('duration', parseInt($event.target.value))"
                  min="0" max="1000" step="50"
                  class="mb-flex-1"
                />
                <span class="mb-text-xs mb-text-gray-400 mb-w-12 mb-text-right">{{ tileTransition.duration }}ms</span>
              </div>
            </div>
            <!-- Easing -->
            <div>
              <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Curva</label>
              <select
                :value="tileTransition.easing"
                @change="updateTransition('easing', $event.target.value)"
                class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
              >
                <option value="ease">Ease</option>
                <option value="linear">Linear</option>
                <option value="ease-in">Ease In</option>
                <option value="ease-out">Ease Out</option>
                <option value="ease-in-out">Ease In Out</option>
              </select>
            </div>
          </div>

          <!-- Info box -->
          <div class="mb-bg-amber-900/30 mb-border mb-border-amber-700/50 mb-rounded-md mb-p-2">
            <p class="mb-text-[10px] mb-text-amber-300">Passa il mouse sul tile nel canvas per l'anteprima hover.</p>
          </div>
          </template>
        </div>

        <!-- ============ Advanced tab ============ -->
        <div v-else class="mb-space-y-4">
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

          <!-- SEO & Accessibility -->
          <CollapseSection title="SEO & Accessibilità">
            <div class="mb-space-y-3">
              <!-- ARIA Label -->
              <div>
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Aria Label</label>
                <input
                  type="text"
                  :value="tileAdvanced.aria_label || ''"
                  @input="updateAdvanced('aria_label', $event.target.value)"
                  placeholder="Descrizione per screen reader"
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
                  placeholder="Tooltip al passaggio del mouse"
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

          <!-- Sticky -->
          <CollapseSection title="Sticky">
            <div class="mb-space-y-3">
              <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                <input type="checkbox" :checked="tileAdvanced.sticky === true" @change="updateAdvanced('sticky', $event.target.checked)" class="mb-accent-primary-500" />
                <span class="mb-text-xs mb-text-gray-300">Elemento sticky</span>
              </label>
              <template v-if="tileAdvanced.sticky">
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Posizione</label>
                  <select :value="tileAdvanced.sticky_position || 'top'" @change="updateAdvanced('sticky_position', $event.target.value)" class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900">
                    <option value="top">In alto</option>
                    <option value="bottom">In basso</option>
                  </select>
                </div>
                <div>
                  <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">Offset (px): {{ tileAdvanced.sticky_offset || 0 }}</label>
                  <input type="range" min="0" max="200" step="5" :value="tileAdvanced.sticky_offset || 0" @input="updateAdvanced('sticky_offset', $event.target.value)" class="mb-w-full mb-accent-primary-500" />
                </div>
                <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                  <input type="checkbox" :checked="tileAdvanced.sticky_on_mobile !== false" @change="updateAdvanced('sticky_on_mobile', $event.target.checked)" class="mb-accent-primary-500" />
                  <span class="mb-text-xs mb-text-gray-300">Sticky su mobile</span>
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

          <!-- Note editor (solo builder, non renderizzate nel frontend) -->
          <CollapseSection title="Note editor">
            <textarea
              :value="tileAdvanced.editor_note || ''"
              @input="updateAdvanced('editor_note', $event.target.value)"
              rows="3"
              placeholder="Note visibili solo nel builder..."
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

          <!-- Positioning -->
          <CollapseSection title="Posizionamento">
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
                    placeholder="HTML ID della sezione (es. fine-nav)"
                    class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-0.5 mb-text-[11px] mb-text-gray-900"
                  />
                  <p class="mb-text-[9px] mb-text-gray-500 mb-mt-0.5">L'elemento scompare quando lo scroll raggiunge la sezione con questo ID.</p>
                </div>
              </template>
            </div>
          </CollapseSection>

        </div>
        </template>

        <!-- ProSlider Editor Modal -->
        <ProSliderEditor
          v-if="showProSliderEditor && selectedTile"
          :tileId="selectedTile.id"
          @close="showProSliderEditor = false"
        />
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { t } from '@/i18n';
import { useBuilderStore } from '@/stores/builder';
import { useTilesStore } from '@/stores/tiles';
import { getElementDef, getElementFields } from '@/config/elementRegistry';
import BackgroundControls from './BackgroundControls.vue';
import PageSettingsPanel from './PageSettingsPanel.vue';
import ContentItemsEditor from './ContentItemsEditor.vue';
import InspectorField from './InspectorField.vue';
import FieldBorderRadius from './fields/FieldBorderRadius.vue';
import FieldSpacing from './fields/FieldSpacing.vue';
import CollapseSection from './CollapseSection.vue';
import FieldBoxShadow from './fields/FieldBoxShadow.vue';
import FieldTransform from './fields/FieldTransform.vue';
import ParallaxEditor from './ParallaxEditor.vue';
import BezierPathEditor from './BezierPathEditor.vue';
import ProSliderEditor from '../ProSlider/ProSliderEditor.vue';
import HeightModeSelector from '../ProSlider/HeightModeSelector.vue';

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

const activeTab = ref('Contenuto');
const inspectorSearch = ref('');
function matchSearch(...keywords) {
  const q = inspectorSearch.value.trim().toLowerCase();
  if (!q) return true;
  return keywords.some(k => k.toLowerCase().includes(q));
}
const tabs = ['Contenuto', 'Stile', 'Avanzate'];
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
  const def = getElementDef(node.type);
  if (def?.name) return def.name;
  if (typeLabels[node.type]) return typeLabels[node.type];
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
  inspectorSearch.value = '';
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
  return true;
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
  // Filter by inspectorSearch
  const q = inspectorSearch.value.trim().toLowerCase();
  if (!q) return sections;
  return sections.map(sec => ({
    ...sec,
    fields: sec.fields.filter(f => {
      const label = (f.label || '').toLowerCase();
      const key = (f.key || '').toLowerCase();
      return label.includes(q) || key.includes(q);
    }),
  })).filter(sec => sec.fields.length > 0);
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
  styleState.value = 'normal';
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
  // Row custom_widths: handled by dedicated inline input, not here
  tilesStore.updateTile(builderStore.selectedTileId, { [key]: value });
  builderStore.markDirtyForTile(builderStore.selectedTileId);
}

function updateStyle(key, value) {
  if (!builderStore.selectedTileId) return;
  tilesStore.updateTileStyle(builderStore.selectedTileId, { [key]: value });
  builderStore.markDirtyForTile(builderStore.selectedTileId);
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

// --- Hover styles ---
const styleState = ref('normal');

const tileHover = computed(() => selectedTile.value?.style?.hover || {});
const tileTransition = computed(() => selectedTile.value?.style?.transition || { duration: 300, easing: 'ease' });

function updateHover(key, value) {
  if (!builderStore.selectedTileId) return;
  tilesStore.updateTileHover(builderStore.selectedTileId, { [key]: value });
  builderStore.markDirtyForTile(builderStore.selectedTileId);
}

function resetHover(key) {
  if (!builderStore.selectedTileId) return;
  const resetVal = (key === 'opacity' || key === 'transform_scale' || key === 'transform_translateY' || key === 'transform_rotate') ? null : '';
  tilesStore.updateTileHover(builderStore.selectedTileId, { [key]: resetVal });
  builderStore.markDirtyForTile(builderStore.selectedTileId);
}

function updateTransition(key, value) {
  if (!builderStore.selectedTileId) return;
  tilesStore.updateTileTransition(builderStore.selectedTileId, { [key]: value });
  builderStore.markDirtyForTile(builderStore.selectedTileId);
}

// styleState reset is handled in the watcher above (with AB test polling)

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
</style>
