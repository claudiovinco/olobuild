<template>
  <transition name="slide">
    <div
      v-if="selectedTile || builderStore.pageSettingsOpen || builderStore.stylePanelOpen"
      class="mb-w-72 mb-bg-gray-800 mb-border-l mb-border-gray-700 mb-overflow-y-auto mb-shrink-0"
    >
      <div class="mb-p-4">
        <!-- Style Panel -->
        <StylePanel v-if="builderStore.stylePanelOpen" />

        <!-- Page Settings Panel -->
        <PageSettingsPanel v-if="builderStore.pageSettingsOpen && !selectedTile && !builderStore.stylePanelOpen" />

        <!-- Tile Inspector -->
        <template v-if="selectedTile">
        <!-- Header -->
        <div class="mb-flex mb-items-center mb-justify-between mb-mb-4">
          <h3 class="mb-text-sm mb-font-semibold mb-text-gray-200">
            Impostazioni {{ elementDef ? elementDef.name : selectedTile.type }}
          </h3>
          <button
            @click="builderStore.deselectTile()"
            class="mb-text-gray-500 hover:mb-text-gray-300 mb-text-lg"
          >
            &times;
          </button>
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
            {{ tab }}
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
          </div>

          <template v-if="elementFields.length > 0 && elementDef?.customEditor !== 'proslider'">
            <template v-for="(field, idx) in elementFields" :key="field.key || ('sep-' + idx)">
             <template v-if="!field.condition || evaluateCondition(field.condition, selectedTile.settings)">
              <!-- Content items editor (generic multi-item) -->
              <div v-if="field.type === 'content-items' && selectedTile?.settings?.[field.key]">
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ field.label }}</label>
                <ContentItemsEditor
                  :modelValue="selectedTile.settings[field.key]"
                  :itemFields="field.itemFields"
                  :newItemDefaults="field.newItemDefaults || {}"
                  :itemLabel="field.itemLabel || 'Item'"
                  :tileId="selectedTile.id"
                  :supportsDynamic="field.supportsDynamic || false"
                  :dynamic="selectedTile.dynamic || {}"
                  @update:modelValue="updateSetting(field.key, $event)"
                  @update:dynamic-query="updateDynamicQuery"
                  @update:dynamic-item-map="updateDynamicItemMap"
                />
              </div>

              <!-- Custom widths: local input, apply only on Enter/blur -->
              <div v-else-if="field.key === 'custom_widths' && selectedTile.type === 'row'">
                <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ field.label }}</label>
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

              <!-- Standard fields via InspectorField -->
              <InspectorField
                v-else
                :field="field"
                :modelValue="selectedTile.settings?.[field.key]"
                :tileId="selectedTile.id"
                :dynamic="selectedTile.dynamic || {}"
                @update:modelValue="updateSetting(field.key, $event)"
                @update:attachmentId="updateSetting(field.key + '_id', $event)"
                @update:dynamic="onDynamicFieldUpdate"
              />
             </template>
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
            >Normale</button>
            <button
              @click="styleState = 'hover'"
              :class="[
                'mb-flex-1 mb-py-1.5 mb-text-xs mb-font-medium mb-rounded-md mb-transition-colors',
                styleState === 'hover'
                  ? 'mb-bg-amber-600 mb-text-white'
                  : 'mb-text-gray-400 hover:mb-text-gray-300'
              ]"
            >Hover</button>
          </div>

          <!-- === NORMAL state controls === -->
          <template v-if="styleState === 'normal'">
          <!-- Full Width -->
          <div>
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
              <span class="mb-text-xs mb-font-semibold mb-text-gray-300">Larghezza piena</span>
            </label>
          </div>

          <!-- Spacing breakpoint switcher -->
          <div class="mb-flex mb-gap-1 mb-bg-gray-700 mb-rounded-lg mb-p-0.5 mb-mb-1">
            <button
              v-for="bp in spacingBreakpoints"
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
          <div>
            <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">Margine (px) <span v-if="spacingBp !== 'desktop'" class="mb-text-amber-400 mb-text-[10px]">{{ spacingBpLabel }}</span></label>
            <div class="mb-grid mb-grid-cols-2 mb-gap-2">
              <div v-for="side in sides" :key="'m-' + side">
                <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">{{ side }}</label>
                <input
                  type="number"
                  :value="tileStyle[spacingKey('margin_' + side)] || 0"
                  @input="updateStyle(spacingKey('margin_' + side), $event.target.value)"
                  class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900"
                  min="0" max="200" step="4"
                  :placeholder="spacingBp !== 'desktop' ? 'Eredita' : ''"
                />
              </div>
            </div>
          </div>

          <!-- Padding -->
          <div>
            <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">Padding (px) <span v-if="spacingBp !== 'desktop'" class="mb-text-amber-400 mb-text-[10px]">{{ spacingBpLabel }}</span></label>
            <div class="mb-grid mb-grid-cols-2 mb-gap-2">
              <div v-for="side in sides" :key="'p-' + side">
                <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">{{ side }}</label>
                <input
                  type="number"
                  :value="tileStyle[spacingKey('padding_' + side)] || 0"
                  @input="updateStyle(spacingKey('padding_' + side), $event.target.value)"
                  class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900"
                  min="0" max="200" step="4"
                  :placeholder="spacingBp !== 'desktop' ? 'Eredita' : ''"
                />
              </div>
            </div>
          </div>

          <!-- Background -->
          <div>
            <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">Sfondo</label>
            <BackgroundControls
              :modelValue="tileBg"
              :showParallax="true"
              @update:modelValue="onTileBgUpdate"
            />
          </div>

          <!-- Border Radius -->
          <div>
            <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">Border radius (px)</label>
            <FieldBorderRadius
              :modelValue="tileStyle.border_radius || 0"
              @update:modelValue="updateStyle('border_radius', $event)"
            />
          </div>

          <!-- Border -->
          <div>
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
          <div>
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
            </select>
          </div>

          <!-- Opacity -->
          <div>
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
            </select>
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
            <ParallaxEditor
              :modelValue="elementParallaxData"
              :properties="elementParallaxProperties"
              @update:modelValue="updateAdvanced('parallax', $event)"
            />
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
                </select>
              </div>
              <template v-if="tileAdvanced.position_mode && tileAdvanced.position_mode !== 'static'">
                <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                  <div>
                    <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Top</label>
                    <input
                      type="text"
                      :value="tileAdvanced.position_top ?? ''"
                      @change="updateAdvanced('position_top', $event.target.value)"
                      placeholder="auto"
                      class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-0.5 mb-text-[11px] mb-text-gray-900"
                    />
                  </div>
                  <div>
                    <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Left</label>
                    <input
                      type="text"
                      :value="tileAdvanced.position_left ?? ''"
                      @change="updateAdvanced('position_left', $event.target.value)"
                      placeholder="auto"
                      class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-0.5 mb-text-[11px] mb-text-gray-900"
                    />
                  </div>
                  <div>
                    <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Bottom</label>
                    <input
                      type="text"
                      :value="tileAdvanced.position_bottom ?? ''"
                      @change="updateAdvanced('position_bottom', $event.target.value)"
                      placeholder="auto"
                      class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-0.5 mb-text-[11px] mb-text-gray-900"
                    />
                  </div>
                  <div>
                    <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Right</label>
                    <input
                      type="text"
                      :value="tileAdvanced.position_right ?? ''"
                      @change="updateAdvanced('position_right', $event.target.value)"
                      placeholder="auto"
                      class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-0.5 mb-text-[11px] mb-text-gray-900"
                    />
                  </div>
                </div>
                <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                  <div>
                    <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Larghezza</label>
                    <input
                      type="text"
                      :value="tileAdvanced.position_width ?? ''"
                      @change="updateAdvanced('position_width', $event.target.value)"
                      placeholder="auto"
                      class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-0.5 mb-text-[11px] mb-text-gray-900"
                    />
                  </div>
                  <div>
                    <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">z-index</label>
                    <input
                      type="number"
                      :value="tileAdvanced.position_zindex ?? ''"
                      @change="updateAdvanced('position_zindex', $event.target.value === '' ? '' : parseInt($event.target.value))"
                      placeholder="auto"
                      class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-0.5 mb-text-[11px] mb-text-gray-900"
                    />
                  </div>
                </div>
                <p class="mb-text-[9px] mb-text-gray-500 mb-leading-relaxed">
                  Valori: px (es. 100px), % (es. 50%), vh/vw. Assoluto è relativo alla sezione, Fisso alla finestra.
                </p>
              </template>
            </div>
          </CollapseSection>

          <!-- Column Span -->
          <div>
            <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">Larghezza colonna</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <input
                type="range"
                :value="selectedTile.columns || 12"
                @input="updateColumns($event.target.value)"
                min="1" max="12" step="1"
                class="mb-flex-1"
              />
              <span class="mb-text-xs mb-text-gray-400 mb-w-12 mb-text-right">{{ selectedTile.columns || 12 }}/12</span>
            </div>
            <div class="mb-grid mb-grid-cols-12 mb-gap-0.5 mb-mt-2">
              <div
                v-for="i in 12"
                :key="i"
                :class="[
                  'mb-h-2 mb-rounded-sm',
                  i <= (selectedTile.columns || 12) ? 'mb-bg-primary-500' : 'mb-bg-gray-700'
                ]"
              ></div>
            </div>
          </div>
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
import { useBuilderStore } from '@/stores/builder';
import { useTilesStore } from '@/stores/tiles';
import { getElementDef, getElementFields } from '@/config/elementRegistry';
import BackgroundControls from './BackgroundControls.vue';
import PageSettingsPanel from './PageSettingsPanel.vue';
import StylePanel from './StylePanel.vue';
import ContentItemsEditor from './ContentItemsEditor.vue';
import InspectorField from './InspectorField.vue';
import FieldBorderRadius from './fields/FieldBorderRadius.vue';
import CollapseSection from './CollapseSection.vue';
import ParallaxEditor from './ParallaxEditor.vue';
import ProSliderEditor from '../ProSlider/ProSliderEditor.vue';

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
const tabs = ['Contenuto', 'Stile', 'Avanzate'];
const showProSliderEditor = ref(false);
const sides = ['top', 'right', 'bottom', 'left'];
const viewports = [
  { key: 'desktop', label: 'Desktop', svg: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg>' },
  { key: 'tablet', label: 'Tablet', svg: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2"/><line x1="12" x2="12.01" y1="18" y2="18"/></svg>' },
  { key: 'mobile', label: 'Mobile', svg: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="20" x="5" y="2" rx="2"/><line x1="12" x2="12.01" y1="18" y2="18"/></svg>' },
];

const selectedTile = computed(() => {
  if (!builderStore.selectedTileId) return null;
  return tilesStore.getTileById(builderStore.selectedTileId);
});

// Local ref for custom_widths input (avoids reactive re-render overwriting user input)
const customWidthsLocal = ref('');
const customWidthsEditing = ref(false);

// Only sync from store when tile selection changes, NOT while user is editing
watch(() => builderStore.selectedTileId, () => {
  customWidthsEditing.value = false;
  const tile = selectedTile.value;
  customWidthsLocal.value = tile?.settings?.custom_widths || '';
}, { immediate: true });

function onCustomWidthsFocus() {
  customWidthsEditing.value = true;
}

function applyCustomWidthsFromInspector() {
  customWidthsEditing.value = false;
  if (!builderStore.selectedTileId || !customWidthsLocal.value.trim()) return;
  const result = tilesStore.applyCustomWidths(builderStore.selectedTileId, customWidthsLocal.value);
  if (result) customWidthsLocal.value = result;
  builderStore.isDirty = true;
}

const currentTileType = computed(() => selectedTile.value?.type || '');

// Element definition from registry
const elementDef = computed(() => getElementDef(currentTileType.value));

// Fields from element definition
const elementFields = computed(() => getElementFields(currentTileType.value));

// Fallback: filtered settings for elements without definition
const hiddenKeys = ['columns_data'];
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

const tileStyle = computed(() => selectedTile.value?.style || {});

// Responsive spacing breakpoints
const spacingBp = ref('desktop');
const spacingBreakpoints = [
  { key: 'desktop', label: 'Desktop', icon: '<svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="16" height="11" rx="1"/><path d="M6 17h8M10 14v3"/></svg>' },
  { key: 'tablet', label: 'Tablet', icon: '<svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="4" y="2" width="12" height="16" rx="1.5"/><circle cx="10" cy="16" r="0.5" fill="currentColor"/></svg>' },
  { key: 'mobile', label: 'Mobile', icon: '<svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="5" y="2" width="10" height="16" rx="1.5"/><circle cx="10" cy="16" r="0.5" fill="currentColor"/></svg>' },
];
const spacingBpLabel = computed(() => spacingBreakpoints.find(b => b.key === spacingBp.value)?.label || '');
function spacingKey(base) {
  return spacingBp.value === 'desktop' ? base : base + '_' + spacingBp.value;
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

// Migrate old flat parallax format to new multi-stop object
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
    builderStore.isDirty = true;
    return;
  }
  // Row custom_widths: handled by dedicated inline input, not here
  tilesStore.updateTile(builderStore.selectedTileId, { [key]: value });
  builderStore.isDirty = true;
}

function updateStyle(key, value) {
  if (!builderStore.selectedTileId) return;
  tilesStore.updateTileStyle(builderStore.selectedTileId, { [key]: value });
  builderStore.isDirty = true;
}

function onTileBgUpdate(newBg) {
  if (!builderStore.selectedTileId) return;
  tilesStore.updateTileStyle(builderStore.selectedTileId, { bg: newBg });
  builderStore.isDirty = true;
}

function updateAdvanced(key, value) {
  if (!builderStore.selectedTileId) return;
  tilesStore.updateTileAdvanced(builderStore.selectedTileId, { [key]: value });
  builderStore.isDirty = true;
}

function updateColumns(value) {
  if (!selectedTile.value) return;
  selectedTile.value.columns = parseInt(value);
  builderStore.isDirty = true;
}

// --- Hover styles ---
const styleState = ref('normal');

const tileHover = computed(() => selectedTile.value?.style?.hover || {});
const tileTransition = computed(() => selectedTile.value?.style?.transition || { duration: 300, easing: 'ease' });

function updateHover(key, value) {
  if (!builderStore.selectedTileId) return;
  tilesStore.updateTileHover(builderStore.selectedTileId, { [key]: value });
  builderStore.isDirty = true;
}

function resetHover(key) {
  if (!builderStore.selectedTileId) return;
  const resetVal = (key === 'opacity' || key === 'transform_scale' || key === 'transform_translateY' || key === 'transform_rotate') ? null : '';
  tilesStore.updateTileHover(builderStore.selectedTileId, { [key]: resetVal });
  builderStore.isDirty = true;
}

function updateTransition(key, value) {
  if (!builderStore.selectedTileId) return;
  tilesStore.updateTileTransition(builderStore.selectedTileId, { [key]: value });
  builderStore.isDirty = true;
}

watch(() => builderStore.selectedTileId, () => {
  styleState.value = 'normal';
});

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
  builderStore.isDirty = true;
}

function updateDynamicQuery(queryConfig) {
  if (!builderStore.selectedTileId) return;
  tilesStore.updateTileDynamic(builderStore.selectedTileId, { _query: queryConfig });
  builderStore.isDirty = true;
}

function updateDynamicItemMap(itemMap) {
  if (!builderStore.selectedTileId) return;
  tilesStore.updateTileDynamic(builderStore.selectedTileId, { _itemMap: itemMap });
  builderStore.isDirty = true;
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
