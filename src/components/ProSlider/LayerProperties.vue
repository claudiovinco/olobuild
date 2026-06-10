<template>
  <div class="mb-bg-gray-900 mb-border-l mb-border-gray-700 mb-overflow-y-auto mb-flex mb-flex-col mb-h-full">
    <template v-if="layer">
      <!-- Header -->
      <div class="mb-p-2 mb-border-b mb-border-gray-700 mb-text-xs mb-font-semibold mb-text-gray-300">
        Livello {{ layer.type.charAt(0).toUpperCase() + layer.type.slice(1) }}
      </div>

      <!-- Tabs -->
      <div class="mb-flex mb-bg-gray-800 mb-border-b mb-border-gray-700">
        <button
          v-for="tab in tabs"
          :key="tab"
          @click="activeTab = tab"
          :class="[
            'mb-flex-1 mb-py-1.5 mb-text-[10px] mb-font-medium mb-transition-colors',
            activeTab === tab ? 'mb-text-primary-400 mb-border-b-2 mb-border-primary-400' : 'mb-text-gray-500 hover:mb-text-gray-300'
          ]"
        >{{ tab }}</button>
      </div>

      <div class="mb-flex-1 mb-p-3 mb-space-y-3 mb-overflow-y-auto">

        <!-- ===== CONTENUTO ===== -->
        <template v-if="activeTab === 'Contenuto'">
          <!-- Text -->
          <template v-if="layer.type === 'text'">
            <div>
              <label class="mps-label">Testo</label>
              <textarea :value="layer.content" @input="up('content', $event.target.value)" rows="3"
                class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900 mb-resize-y"></textarea>
            </div>
            <div>
              <label class="mps-label">Tag</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.tag" :options="OPTS_TAG" @update:model-value="up('tag', $event)" />
            </div>
          </template>
          <!-- Image -->
          <template v-if="layer.type === 'image'">
            <div>
              <label class="mps-label">URL Immagine</label>
              <div class="mb-flex mb-gap-1">
                <input :value="layer.imageSrc" @input="up('imageSrc', $event.target.value)" class="mps-input mb-flex-1" placeholder="https://..." />
                <button @click="pickImage" class="mb-px-2 mb-bg-gray-600 mb-rounded mb-text-xs mb-text-gray-300 hover:mb-bg-gray-500">Sfoglia</button>
              </div>
            </div>
          </template>
          <!-- Button -->
          <template v-if="layer.type === 'button'">
            <div>
              <label class="mps-label">Etichetta</label>
              <input :value="layer.content" @input="up('content', $event.target.value)" class="mps-input" />
            </div>
            <div>
              <label class="mps-label">URL</label>
              <input :value="layer.buttonUrl" @input="up('buttonUrl', $event.target.value)" class="mps-input" placeholder="https://..." />
            </div>
            <div>
              <label class="mps-label">Destinazione</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.buttonTarget" :options="OPTS_LINK_TARGET" @update:model-value="up('buttonTarget', $event)" />
            </div>
          </template>
          <!-- Icon -->
          <template v-if="layer.type === 'icon'">
            <div>
              <label class="mps-label">Nome icona</label>
              <div class="mb-flex mb-gap-1 mb-items-center">
                <span v-if="layer.iconName && iconsSvg[layer.iconName]" class="mps-icon-preview" v-html="iconsSvg[layer.iconName]"></span>
                <input :value="layer.iconName" @input="up('iconName', $event.target.value)" class="mps-input mb-flex-1" placeholder="star" />
                <button @click="showIconPicker = true" class="mb-px-2 mb-bg-gray-600 mb-rounded mb-text-xs mb-text-gray-300 hover:mb-bg-gray-500">Sfoglia</button>
              </div>
            </div>
            <IconPicker
              v-if="showIconPicker"
              @select="up('iconName', $event); showIconPicker = false"
              @close="showIconPicker = false"
            />
          </template>
          <!-- Video -->
          <template v-if="layer.type === 'video'">
            <div>
              <label class="mps-label">URL Video</label>
              <div class="mb-flex mb-gap-1">
                <input :value="layer.videoSrc" @input="up('videoSrc', $event.target.value)" class="mps-input mb-flex-1" placeholder="mp4 o YouTube URL" />
                <button @click="pickVideo" class="mb-px-2 mb-bg-gray-600 mb-rounded mb-text-xs mb-text-gray-300 hover:mb-bg-gray-500">Sfoglia</button>
              </div>
            </div>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="layer.videoAutoplay" @change="up('videoAutoplay', $event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">Autoplay</span>
            </label>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="layer.videoMuted" @change="up('videoMuted', $event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">Muto</span>
            </label>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="layer.videoLoop" @change="up('videoLoop', $event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">Loop</span>
            </label>
          </template>
          <!-- Audio -->
          <template v-if="layer.type === 'audio'">
            <div>
              <label class="mps-label">URL Audio</label>
              <div class="mb-flex mb-gap-1">
                <input :value="layer.audioSrc" @input="up('audioSrc', $event.target.value)" class="mps-input mb-flex-1" placeholder="mp3 o url audio" />
                <button @click="pickAudio" class="mb-px-2 mb-bg-gray-600 mb-rounded mb-text-xs mb-text-gray-300 hover:mb-bg-gray-500">Sfoglia</button>
              </div>
            </div>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="layer.audioAutoplay" @change="up('audioAutoplay', $event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">Autoplay</span>
            </label>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="layer.audioLoop" @change="up('audioLoop', $event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">Loop</span>
            </label>
          </template>

          <!-- Stato iniziale (tutti i tipi) -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <div>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="layer.initiallyHidden === true" @change="up('initiallyHidden', $event.target.checked)" class="mb-rounded" />
              <span class="mps-label mb-mb-0">Nascosto all'avvio (mostrabile via toggle)</span>
            </label>
          </div>

          <!-- Layer Action (tutti i tipi) -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">Azione Click</p>
          <div>
            <label class="mps-label">Tipo azione</label>
            <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.action?.type || 'none'" :options="OPTS_ACTION_TYPE" @update:model-value="setAction('type', $event)" />
          </div>
          <template v-if="layer.action?.type === 'goToSlide'">
            <div>
              <label class="mps-label">Indice slide (da 0)</label>
              <input type="number" :value="layer.action.target || 0" @input="setAction('target', parseInt($event.target.value))" min="0" class="mps-input mb-w-full" />
            </div>
          </template>
          <template v-if="layer.action?.type === 'openUrl'">
            <div>
              <label class="mps-label">URL</label>
              <input :value="layer.action.url || ''" @input="setAction('url', $event.target.value)" class="mps-input mb-w-full" placeholder="https://..." />
            </div>
            <div>
              <label class="mps-label">Destinazione</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.action.urlTarget || '_self'" :options="OPTS_LINK_TARGET" @update:model-value="setAction('urlTarget', $event)" />
            </div>
          </template>
          <template v-if="layer.action?.type === 'toggleLayer'">
            <div>
              <label class="mps-label">Layer target</label>
              <FieldSelect
                v-if="targetableLayers.length"
                ui="dropdown" size="compact" theme="dark"
                :model-value="layer.action.target || ''"
                :options="targetLayerOptions"
                @update:model-value="setAction('target', $event)"
              />
              <p v-else class="mb-text-[10px] mb-text-gray-500 mb-italic">Nessun altro layer in questa slide</p>
              <p v-if="layer.action.target" class="mb-text-[9px] mb-text-gray-500 mb-mt-1 mb-font-mono">id: {{ layer.action.target }}</p>
            </div>
          </template>

          <!-- Parallax depth -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <div>
            <label class="mps-label">Parallax depth</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <input type="range" :value="layer.parallaxDepth || 0" @input="up('parallaxDepth', parseInt($event.target.value))" min="0" max="10" class="mb-flex-1" />
              <span class="mps-val">{{ layer.parallaxDepth || 0 }}</span>
            </div>
          </div>
        </template>

        <!-- ===== POSIZIONE ===== -->
        <template v-if="activeTab === 'Posizione'">
          <!-- Breakpoint indicator -->
          <div v-if="activeBreakpoint !== 'desktop'" class="mb-bg-yellow-900/30 mb-border mb-border-yellow-700/50 mb-rounded mb-p-2 mb-mb-2">
            <div class="mb-flex mb-items-center mb-justify-between">
              <span class="mb-text-[10px] mb-text-yellow-400">Override per {{ activeBreakpoint }} — valori non impostati ereditano dal livello superiore</span>
              <button
                v-if="hasAnyOverride"
                @click="clearAllOverrides"
                class="mb-text-[10px] mb-text-yellow-500 hover:mb-text-yellow-300 mb-whitespace-nowrap mb-ml-2"
              >Reset tutti</button>
            </div>
          </div>
          <!-- Visibility toggle per breakpoint -->
          <div v-if="activeBreakpoint !== 'desktop'" class="mb-mb-2">
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="rv('visible') !== false" @change="up('visible', $event.target.checked)" class="mb-rounded" />
              <span class="mps-label mb-mb-0">Visibile su {{ activeBreakpoint }}</span>
            </label>
          </div>
          <div>
            <label class="mps-label" :class="{ 'mb-text-yellow-400': isOverridden('x') }">X (%)</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <input type="range" :value="rv('x')" @input="up('x', Math.round(parseFloat($event.target.value) * 10) / 10)" min="0" max="100" step="0.5" class="mb-flex-1" />
              <span class="mps-val">{{ fmt(rv('x')) }}</span>
              <button v-if="isOverridden('x')" @click="clearOverride('x')" class="mb-text-yellow-500 mb-text-[10px]" title="Reset">&times;</button>
            </div>
          </div>
          <div>
            <label class="mps-label" :class="{ 'mb-text-yellow-400': isOverridden('y') }">Y (%)</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <input type="range" :value="rv('y')" @input="up('y', Math.round(parseFloat($event.target.value) * 10) / 10)" min="0" max="100" step="0.5" class="mb-flex-1" />
              <span class="mps-val">{{ fmt(rv('y')) }}</span>
              <button v-if="isOverridden('y')" @click="clearOverride('y')" class="mb-text-yellow-500 mb-text-[10px]" title="Reset">&times;</button>
            </div>
          </div>
          <div>
            <label class="mps-label" :class="{ 'mb-text-yellow-400': isOverridden('width') }">Larghezza (%)</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <label class="mb-flex mb-items-center mb-gap-1 mb-text-[10px] mb-text-gray-400">
                <input type="checkbox" :checked="rv('width') === 'auto'" @change="up('width', $event.target.checked ? 'auto' : 50)" class="mb-rounded" /> Auto
              </label>
              <template v-if="rv('width') !== 'auto'">
                <input type="range" :value="rv('width')" @input="up('width', Math.round(parseFloat($event.target.value) * 10) / 10)" min="5" max="100" step="0.5" class="mb-flex-1" />
                <span class="mps-val">{{ fmt(rv('width')) }}</span>
              </template>
              <button v-if="isOverridden('width')" @click="clearOverride('width')" class="mb-text-yellow-500 mb-text-[10px]" title="Reset">&times;</button>
            </div>
          </div>
          <div>
            <label class="mps-label" :class="{ 'mb-text-yellow-400': isOverridden('height') }">Altezza (%)</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <label class="mb-flex mb-items-center mb-gap-1 mb-text-[10px] mb-text-gray-400">
                <input type="checkbox" :checked="rv('height') === 'auto'" @change="up('height', $event.target.checked ? 'auto' : 30)" class="mb-rounded" /> Auto
              </label>
              <template v-if="rv('height') !== 'auto'">
                <input type="range" :value="rv('height')" @input="up('height', Math.round(parseFloat($event.target.value) * 10) / 10)" min="5" max="100" step="0.5" class="mb-flex-1" />
                <span class="mps-val">{{ fmt(rv('height')) }}</span>
              </template>
              <button v-if="isOverridden('height')" @click="clearOverride('height')" class="mb-text-yellow-500 mb-text-[10px]" title="Reset">&times;</button>
            </div>
          </div>
          <div v-if="layer.type === 'text' || layer.type === 'button' || layer.type === 'icon'">
            <label class="mps-label" :class="{ 'mb-text-yellow-400': isOverridden('fontSize') }">Font size (px)</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <input type="range" :value="rv('fontSize')" @input="up('fontSize', parseInt($event.target.value))" min="8" max="200" class="mb-flex-1" />
              <span class="mps-val">{{ rv('fontSize') }}</span>
              <button v-if="isOverridden('fontSize')" @click="clearOverride('fontSize')" class="mb-text-yellow-500 mb-text-[10px]" title="Reset">&times;</button>
            </div>
          </div>
        </template>

        <!-- ===== STILE ===== -->
        <template v-if="activeTab === 'Stile'">

          <!-- ── Tipografia (text/button) ── -->
          <template v-if="layer.type === 'text' || layer.type === 'button'">
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">Tipografia</p>
            <div>
              <label class="mps-label">Font</label>
              <select :value="layer.fontFamily || ''" @change="up('fontFamily', $event.target.value)" class="mps-select">
                <option value="">Default</option>
                <optgroup label="Sans-serif">
                  <option v-for="f in fontsSans" :key="f" :value="f">{{ f }}</option>
                </optgroup>
                <optgroup label="Serif">
                  <option v-for="f in fontsSerif" :key="f" :value="f">{{ f }}</option>
                </optgroup>
                <optgroup label="Mono">
                  <option v-for="f in fontsMono" :key="f" :value="f">{{ f }}</option>
                </optgroup>
                <optgroup label="Display">
                  <option v-for="f in fontsDisplay" :key="f" :value="f">{{ f }}</option>
                </optgroup>
              </select>
            </div>
            <div>
              <label class="mps-label">Dimensione font (px)</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <input type="range" :value="layer.fontSize" @input="up('fontSize', parseFloat($event.target.value))" min="8" max="200" step="1" class="mb-flex-1" />
                <span class="mps-val">{{ layer.fontSize }}</span>
              </div>
            </div>
            <div>
              <label class="mps-label">Peso font</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.fontWeight" :options="OPTS_FONT_WEIGHT" @update:model-value="up('fontWeight', $event)" />
            </div>
            <div>
              <label class="mps-label">Stile font</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.fontStyle || 'normal'" :options="OPTS_FONT_STYLE" @update:model-value="up('fontStyle', $event)" />
            </div>
            <div>
              <label class="mps-label">Colore</label>
              <FieldColor :modelValue="layer.color || '#ffffff'" @update:modelValue="up('color', $event)" />
            </div>
            <div>
              <label class="mps-label">Allineamento testo</label>
              <div class="mb-flex mb-gap-1">
                <button v-for="a in ['left','center','right']" :key="a"
                  @click="up('textAlign', a)"
                  :class="['mb-flex-1 mb-py-1 mb-rounded mb-text-xs', layer.textAlign === a ? 'mb-bg-primary-600 mb-text-white' : 'mb-bg-gray-700 mb-text-gray-400']"
                >{{ a }}</button>
              </div>
            </div>
            <div>
              <label class="mps-label">Line-height</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <input type="range" :value="layer.lineHeight ?? 1.2" @input="up('lineHeight', parseFloat($event.target.value))" min="0.8" max="3" step="0.1" class="mb-flex-1" />
                <span class="mps-val">{{ (layer.lineHeight ?? 1.2).toFixed(1) }}</span>
              </div>
            </div>
            <div>
              <label class="mps-label">Letter-spacing (px)</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <input type="range" :value="layer.letterSpacing ?? 0" @input="up('letterSpacing', parseFloat($event.target.value))" min="-5" max="20" step="0.5" class="mb-flex-1" />
                <span class="mps-val">{{ layer.letterSpacing ?? 0 }}</span>
              </div>
            </div>
            <div>
              <label class="mps-label">Trasformazione</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.textTransform || 'none'" :options="OPTS_TEXT_TRANSFORM" @update:model-value="up('textTransform', $event)" />
            </div>
            <div>
              <label class="mps-label">Decorazione</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.textDecoration || 'none'" :options="OPTS_TEXT_DECORATION" @update:model-value="up('textDecoration', $event)" />
            </div>
            <!-- Text Stroke -->
            <div>
              <label class="mps-label">Text stroke (px)</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <input type="range" :value="layer.textStrokeWidth ?? 0" @input="up('textStrokeWidth', parseFloat($event.target.value))" min="0" max="10" step="0.5" class="mb-flex-1" />
                <span class="mps-val">{{ layer.textStrokeWidth ?? 0 }}</span>
              </div>
            </div>
            <template v-if="(layer.textStrokeWidth ?? 0) > 0">
              <div>
                <label class="mps-label">Colore stroke</label>
                <FieldColor :modelValue="layer.textStrokeColor || '#000000'" @update:modelValue="up('textStrokeColor', $event)" />
              </div>
            </template>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="layer.selectableText" @change="up('selectableText', $event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">Testo selezionabile</span>
            </label>
          </template>

          <!-- ── Dimensione font (icon) ── -->
          <template v-if="layer.type === 'icon'">
            <div>
              <label class="mps-label">Dimensione icona (px)</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <input type="range" :value="layer.fontSize" @input="up('fontSize', parseFloat($event.target.value))" min="8" max="200" step="1" class="mb-flex-1" />
                <span class="mps-val">{{ layer.fontSize }}</span>
              </div>
            </div>
            <div>
              <label class="mps-label">Colore fill</label>
              <FieldColor :modelValue="layer.color || '#ffffff'" @update:modelValue="up('color', $event)" />
            </div>
            <div>
              <label class="mps-label">Colore fill (override)</label>
              <FieldColor :modelValue="layer.iconFillColor || ''" @update:modelValue="up('iconFillColor', $event)" />
            </div>
            <div>
              <label class="mps-label">Colore stroke</label>
              <FieldColor :modelValue="layer.iconStrokeColor || ''" @update:modelValue="up('iconStrokeColor', $event)" />
            </div>
            <div>
              <label class="mps-label">Stroke width (px)</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <input type="range" :value="layer.iconStrokeWidth ?? 0" @input="up('iconStrokeWidth', parseFloat($event.target.value))" min="0" max="10" step="0.5" class="mb-flex-1" />
                <span class="mps-val">{{ layer.iconStrokeWidth ?? 0 }}</span>
              </div>
            </div>
            <div>
              <label class="mps-label">Stroke dash</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <input type="range" :value="layer.iconStrokeDash ?? 0" @input="up('iconStrokeDash', parseFloat($event.target.value))" min="0" max="50" step="1" class="mb-flex-1" />
                <span class="mps-val">{{ layer.iconStrokeDash ?? 0 }}</span>
              </div>
            </div>
          </template>

          <!-- ── Sfondo e raggio ── -->
          <template v-if="layer.type !== 'image' && layer.type !== 'video'">
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">Sfondo</p>
          </template>

          <!-- Shape gradient toggle -->
          <template v-if="layer.type === 'shape'">
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="!!layer.shapeGradient" @change="toggleShapeGradient($event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">Gradiente</span>
            </label>
            <template v-if="layer.shapeGradient">
              <div>
                <label class="mps-label">Colore da</label>
                <FieldColor :modelValue="layer.shapeGradient.from || '#3b82f6'" @update:modelValue="upShapeGrad('from', $event)" />
              </div>
              <div>
                <label class="mps-label">Colore a</label>
                <FieldColor :modelValue="layer.shapeGradient.to || '#8b5cf6'" @update:modelValue="upShapeGrad('to', $event)" />
              </div>
              <div>
                <label class="mps-label">Angolo</label>
                <div class="mb-flex mb-items-center mb-gap-2">
                  <input type="range" :value="layer.shapeGradient.angle ?? 180" @input="upShapeGrad('angle', parseInt($event.target.value))" min="0" max="360" step="15" class="mb-flex-1" />
                  <span class="mps-val">{{ layer.shapeGradient.angle ?? 180 }}°</span>
                </div>
              </div>
            </template>
            <template v-else>
              <div>
                <label class="mps-label">Sfondo</label>
                <FieldColor :modelValue="layer.bgColor || '#3b82f6'" @update:modelValue="up('bgColor', $event)" />
              </div>
            </template>
          </template>
          <template v-else-if="layer.type !== 'image' && layer.type !== 'video'">
            <div>
              <label class="mps-label">Sfondo</label>
              <FieldColor :modelValue="layer.bgColor || '#000000'" @update:modelValue="up('bgColor', $event)" />
            </div>
          </template>

          <div>
            <div class="mb-flex mb-items-center mb-justify-between">
              <label class="mps-label mb-mb-0">Raggio bordo (px)</label>
              <button @click="up('borderRadiusLinked', !layer.borderRadiusLinked)" :class="['mb-text-[10px] mb-px-1.5 mb-rounded', layer.borderRadiusLinked !== false ? 'mb-bg-primary-600 mb-text-white' : 'mb-bg-gray-700 mb-text-gray-400']" title="Lega/slega angoli">&#x1F517;</button>
            </div>
            <template v-if="layer.borderRadiusLinked !== false">
              <div class="mb-flex mb-items-center mb-gap-2">
                <input type="range" :value="layer.borderRadius" @input="up('borderRadius', parseFloat($event.target.value))" min="0" max="200" step="1" class="mb-flex-1" />
                <span class="mps-val">{{ layer.borderRadius }}</span>
              </div>
            </template>
            <template v-else>
              <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                <div>
                  <label class="mps-label">TL</label>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <input type="range" :value="layer.borderRadiusTL ?? 0" @input="up('borderRadiusTL', parseFloat($event.target.value))" min="0" max="200" step="1" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.borderRadiusTL ?? 0 }}</span>
                  </div>
                </div>
                <div>
                  <label class="mps-label">TR</label>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <input type="range" :value="layer.borderRadiusTR ?? 0" @input="up('borderRadiusTR', parseFloat($event.target.value))" min="0" max="200" step="1" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.borderRadiusTR ?? 0 }}</span>
                  </div>
                </div>
                <div>
                  <label class="mps-label">BL</label>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <input type="range" :value="layer.borderRadiusBL ?? 0" @input="up('borderRadiusBL', parseFloat($event.target.value))" min="0" max="200" step="1" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.borderRadiusBL ?? 0 }}</span>
                  </div>
                </div>
                <div>
                  <label class="mps-label">BR</label>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <input type="range" :value="layer.borderRadiusBR ?? 0" @input="up('borderRadiusBR', parseFloat($event.target.value))" min="0" max="200" step="1" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.borderRadiusBR ?? 0 }}</span>
                  </div>
                </div>
              </div>
            </template>
          </div>

          <!-- ── Bordo ── -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">Bordo</p>
          <div>
            <div class="mb-flex mb-items-center mb-justify-between">
              <label class="mps-label mb-mb-0">Larghezza (px)</label>
              <button @click="up('borderWidthLinked', !layer.borderWidthLinked)" :class="['mb-text-[10px] mb-px-1.5 mb-rounded', layer.borderWidthLinked !== false ? 'mb-bg-primary-600 mb-text-white' : 'mb-bg-gray-700 mb-text-gray-400']" title="Lega/slega lati">&#x1F517;</button>
            </div>
            <template v-if="layer.borderWidthLinked !== false">
              <div class="mb-flex mb-items-center mb-gap-2">
                <input type="range" :value="layer.borderWidth ?? 0" @input="up('borderWidth', parseFloat($event.target.value))" min="0" max="20" step="1" class="mb-flex-1" />
                <span class="mps-val">{{ layer.borderWidth ?? 0 }}</span>
              </div>
            </template>
            <template v-else>
              <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                <div v-for="side in ['Top','Right','Bottom','Left']" :key="side">
                  <label class="mps-label">{{ side }}</label>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <input type="range" :value="layer['borderWidth' + side] ?? 0" @input="up('borderWidth' + side, parseFloat($event.target.value))" min="0" max="20" step="1" class="mb-flex-1" />
                    <span class="mps-val">{{ layer['borderWidth' + side] ?? 0 }}</span>
                  </div>
                </div>
              </div>
            </template>
          </div>
          <template v-if="(layer.borderWidth ?? 0) > 0 || (layer.borderWidthLinked === false && ((layer.borderWidthTop ?? 0) > 0 || (layer.borderWidthRight ?? 0) > 0 || (layer.borderWidthBottom ?? 0) > 0 || (layer.borderWidthLeft ?? 0) > 0))">
            <div>
              <label class="mps-label">Stile</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.borderStyle || 'solid'" :options="OPTS_BORDER_STYLE" @update:model-value="up('borderStyle', $event)" />
            </div>
            <div>
              <label class="mps-label">Colore</label>
              <FieldColor :modelValue="layer.borderColor || '#ffffff'" @update:modelValue="up('borderColor', $event)" />
            </div>
          </template>

          <!-- ── Spaziatura ── -->
          <template v-if="layer.type !== 'image' && layer.type !== 'video'">
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">Spaziatura</p>
            <div>
              <div class="mb-flex mb-items-center mb-justify-between">
                <label class="mps-label mb-mb-0">Padding (px)</label>
                <button @click="up('paddingLinked', !layer.paddingLinked)" :class="['mb-text-[10px] mb-px-1.5 mb-rounded', layer.paddingLinked !== false ? 'mb-bg-primary-600 mb-text-white' : 'mb-bg-gray-700 mb-text-gray-400']" title="Lega/slega lati">&#x1F517;</button>
              </div>
              <template v-if="layer.paddingLinked !== false">
                <div class="mb-flex mb-items-center mb-gap-2">
                  <input type="range" :value="layer.padding" @input="up('padding', parseFloat($event.target.value))" min="0" max="80" step="1" class="mb-flex-1" />
                  <span class="mps-val">{{ layer.padding }}</span>
                </div>
              </template>
              <template v-else>
                <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                  <div v-for="side in ['Top','Right','Bottom','Left']" :key="side">
                    <label class="mps-label">{{ side }}</label>
                    <div class="mb-flex mb-items-center mb-gap-1">
                      <input type="range" :value="layer['padding' + side] ?? 0" @input="up('padding' + side, parseFloat($event.target.value))" min="0" max="80" step="1" class="mb-flex-1" />
                      <span class="mps-val">{{ layer['padding' + side] ?? 0 }}</span>
                    </div>
                  </div>
                </div>
              </template>
            </div>
          </template>

          <!-- ── Immagine (solo image) ── -->
          <template v-if="layer.type === 'image'">
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">Immagine</p>
            <div>
              <label class="mps-label">Adattamento</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.objectFit || 'cover'" :options="OPTS_OBJECT_FIT" @update:model-value="up('objectFit', $event)" />
            </div>
            <div>
              <label class="mps-label">Posizione</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.objectPosition || 'center'" :options="OPTS_OBJECT_POSITION" @update:model-value="up('objectPosition', $event)" />
            </div>
          </template>

          <!-- ── Filtri CSS (image/video) ── -->
          <template v-if="layer.type === 'image' || layer.type === 'video'">
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">Filtri</p>
            <div>
              <label class="mps-label">Luminosita (%)</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <input type="range" :value="layer.filterBrightness ?? 100" @input="up('filterBrightness', parseFloat($event.target.value))" min="0" max="200" step="5" class="mb-flex-1" />
                <span class="mps-val">{{ layer.filterBrightness ?? 100 }}</span>
              </div>
            </div>
            <div>
              <label class="mps-label">Contrasto (%)</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <input type="range" :value="layer.filterContrast ?? 100" @input="up('filterContrast', parseFloat($event.target.value))" min="0" max="200" step="5" class="mb-flex-1" />
                <span class="mps-val">{{ layer.filterContrast ?? 100 }}</span>
              </div>
            </div>
            <div>
              <label class="mps-label">Saturazione (%)</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <input type="range" :value="layer.filterSaturate ?? 100" @input="up('filterSaturate', parseFloat($event.target.value))" min="0" max="200" step="5" class="mb-flex-1" />
                <span class="mps-val">{{ layer.filterSaturate ?? 100 }}</span>
              </div>
            </div>
            <div>
              <label class="mps-label">Scala di grigi (%)</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <input type="range" :value="layer.filterGrayscale ?? 0" @input="up('filterGrayscale', parseFloat($event.target.value))" min="0" max="100" step="5" class="mb-flex-1" />
                <span class="mps-val">{{ layer.filterGrayscale ?? 0 }}</span>
              </div>
            </div>
            <div>
              <label class="mps-label">Rotazione tinta</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <input type="range" :value="layer.filterHueRotate ?? 0" @input="up('filterHueRotate', parseFloat($event.target.value))" min="0" max="360" step="5" class="mb-flex-1" />
                <span class="mps-val">{{ layer.filterHueRotate ?? 0 }}°</span>
              </div>
            </div>
            <div>
              <label class="mps-label">Sfocatura (px)</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <input type="range" :value="layer.filterBlur ?? 0" @input="up('filterBlur', parseFloat($event.target.value))" min="0" max="20" step="0.5" class="mb-flex-1" />
                <span class="mps-val">{{ layer.filterBlur ?? 0 }}</span>
              </div>
            </div>
            <div>
              <label class="mps-label">Seppia (%)</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <input type="range" :value="layer.filterSepia ?? 0" @input="up('filterSepia', parseFloat($event.target.value))" min="0" max="100" step="5" class="mb-flex-1" />
                <span class="mps-val">{{ layer.filterSepia ?? 0 }}</span>
              </div>
            </div>
            <div>
              <label class="mps-label">Inverti (%)</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <input type="range" :value="layer.filterInvert ?? 0" @input="up('filterInvert', parseFloat($event.target.value))" min="0" max="100" step="5" class="mb-flex-1" />
                <span class="mps-val">{{ layer.filterInvert ?? 0 }}</span>
              </div>
            </div>
          </template>

          <!-- ── Backdrop Filter (tutti i tipi) ── -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">Backdrop Filter</p>
          <div>
            <label class="mps-label">Backdrop blur (px)</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <input type="range" :value="layer.backdropBlur ?? 0" @input="up('backdropBlur', parseFloat($event.target.value))" min="0" max="20" step="1" class="mb-flex-1" />
              <span class="mps-val">{{ layer.backdropBlur ?? 0 }}</span>
            </div>
          </div>
          <div>
            <label class="mps-label">Backdrop luminosita (%)</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <input type="range" :value="layer.backdropBrightness ?? 100" @input="up('backdropBrightness', parseFloat($event.target.value))" min="0" max="200" step="5" class="mb-flex-1" />
              <span class="mps-val">{{ layer.backdropBrightness ?? 100 }}</span>
            </div>
          </div>
          <div>
            <label class="mps-label">Backdrop scala grigi (%)</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <input type="range" :value="layer.backdropGrayscale ?? 0" @input="up('backdropGrayscale', parseFloat($event.target.value))" min="0" max="100" step="5" class="mb-flex-1" />
              <span class="mps-val">{{ layer.backdropGrayscale ?? 0 }}</span>
            </div>
          </div>

          <!-- ── Ombre ── -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">Ombre</p>

          <!-- Box shadow (tutti) -->
          <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
            <input type="checkbox" :checked="!!layer.boxShadow" @change="toggleBoxShadow($event.target.checked)" class="mb-rounded" />
            <span class="mb-text-[11px] mb-text-gray-400">Box shadow</span>
          </label>
          <template v-if="layer.boxShadow">
            <div class="mb-grid mb-grid-cols-2 mb-gap-2">
              <div>
                <label class="mps-label">X</label>
                <div class="mb-flex mb-items-center mb-gap-1">
                  <input type="range" :value="layer.boxShadow.x ?? 0" @input="upBoxShadow('x', parseInt($event.target.value))" min="-30" max="30" step="1" class="mb-flex-1" />
                  <span class="mps-val">{{ layer.boxShadow.x ?? 0 }}</span>
                </div>
              </div>
              <div>
                <label class="mps-label">Y</label>
                <div class="mb-flex mb-items-center mb-gap-1">
                  <input type="range" :value="layer.boxShadow.y ?? 4" @input="upBoxShadow('y', parseInt($event.target.value))" min="-30" max="30" step="1" class="mb-flex-1" />
                  <span class="mps-val">{{ layer.boxShadow.y ?? 4 }}</span>
                </div>
              </div>
              <div>
                <label class="mps-label">Blur</label>
                <div class="mb-flex mb-items-center mb-gap-1">
                  <input type="range" :value="layer.boxShadow.blur ?? 10" @input="upBoxShadow('blur', parseInt($event.target.value))" min="0" max="60" step="1" class="mb-flex-1" />
                  <span class="mps-val">{{ layer.boxShadow.blur ?? 10 }}</span>
                </div>
              </div>
              <div>
                <label class="mps-label">Spread</label>
                <div class="mb-flex mb-items-center mb-gap-1">
                  <input type="range" :value="layer.boxShadow.spread ?? 0" @input="upBoxShadow('spread', parseInt($event.target.value))" min="-20" max="20" step="1" class="mb-flex-1" />
                  <span class="mps-val">{{ layer.boxShadow.spread ?? 0 }}</span>
                </div>
              </div>
            </div>
            <div>
              <label class="mps-label">Colore ombra</label>
              <FieldColor :modelValue="layer.boxShadow.color || 'rgba(0,0,0,0.3)'" @update:modelValue="upBoxShadow('color', $event)" />
            </div>
          </template>

          <!-- Text shadow (solo text/button) -->
          <template v-if="layer.type === 'text' || layer.type === 'button'">
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer mb-mt-2">
              <input type="checkbox" :checked="!!layer.textShadow" @change="toggleTextShadow($event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">Text shadow</span>
            </label>
            <template v-if="layer.textShadow">
              <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                <div>
                  <label class="mps-label">X</label>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <input type="range" :value="layer.textShadow.x ?? 2" @input="upTextShadow('x', parseInt($event.target.value))" min="-20" max="20" step="1" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.textShadow.x ?? 2 }}</span>
                  </div>
                </div>
                <div>
                  <label class="mps-label">Y</label>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <input type="range" :value="layer.textShadow.y ?? 2" @input="upTextShadow('y', parseInt($event.target.value))" min="-20" max="20" step="1" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.textShadow.y ?? 2 }}</span>
                  </div>
                </div>
              </div>
              <div>
                <label class="mps-label">Blur</label>
                <div class="mb-flex mb-items-center mb-gap-2">
                  <input type="range" :value="layer.textShadow.blur ?? 4" @input="upTextShadow('blur', parseInt($event.target.value))" min="0" max="30" step="1" class="mb-flex-1" />
                  <span class="mps-val">{{ layer.textShadow.blur ?? 4 }}</span>
                </div>
              </div>
              <div>
                <label class="mps-label">Colore ombra</label>
                <FieldColor :modelValue="layer.textShadow.color || '#000000'" @update:modelValue="upTextShadow('color', $event)" />
              </div>
            </template>
          </template>

          <!-- ── Effetti ── -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">Effetti</p>
          <div>
            <label class="mps-label">Opacita (%)</label>
            <div class="mb-flex mb-items-center mb-gap-2">
              <input type="range" :value="layer.opacity ?? 100" @input="up('opacity', parseFloat($event.target.value))" min="0" max="100" step="5" class="mb-flex-1" />
              <span class="mps-val">{{ layer.opacity ?? 100 }}</span>
            </div>
          </div>
          <div>
            <label class="mps-label">Blend Mode</label>
            <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.blendMode || 'normal'" :options="OPTS_BLEND_MODE" @update:model-value="up('blendMode', $event)" />
          </div>

          <!-- SFX Block Reveal (solo text/button/icon) -->
          <template v-if="layer.type === 'text' || layer.type === 'button' || layer.type === 'icon'">
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">SFX Block Reveal</p>
          <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
            <input type="checkbox" :checked="!!layer.sfx" @change="toggleSfx($event.target.checked)" class="mb-rounded" />
            <span class="mb-text-[11px] mb-text-gray-400">Attivo</span>
          </label>
          <template v-if="layer.sfx">
            <div>
              <label class="mps-label">Effetto</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.sfx.effect || 'blockRight'" :options="OPTS_SFX_EFFECT" @update:model-value="upSfx('effect', $event)" />
            </div>
            <div>
              <label class="mps-label">Colore blocco</label>
              <FieldColor :modelValue="layer.sfx.color || '#ffffff'" @update:modelValue="upSfx('color', $event)" />
            </div>
            <div>
              <label class="mps-label">Durata (ms)</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <input type="range" :value="layer.sfx.duration || 800" @input="upSfx('duration', parseInt($event.target.value))" min="200" max="2000" step="50" class="mb-flex-1" />
                <span class="mps-val">{{ layer.sfx.duration || 800 }}</span>
              </div>
            </div>
          </template>
          </template>

          <!-- ── Cursore ── -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">Cursore</p>
          <div>
            <label class="mps-label">Mouse cursor</label>
            <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.cursor || 'auto'" :options="OPTS_CURSOR" @update:model-value="up('cursor', $event)" />
          </div>

          <!-- ── Attributi personalizzati ── -->
          <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
          <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">Attributi</p>
          <div>
            <label class="mps-label">ID personalizzato</label>
            <input :value="layer.customId || ''" @input="up('customId', $event.target.value)" class="mps-input" placeholder="es. hero-title" />
          </div>
          <div>
            <label class="mps-label">Classi CSS</label>
            <input :value="layer.customClass || ''" @input="up('customClass', $event.target.value)" class="mps-input" placeholder="es. my-class another-class" />
          </div>
          <div>
            <label class="mps-label">CSS personalizzato</label>
            <textarea :value="layer.customCSS || ''" @input="up('customCSS', $event.target.value)" rows="3"
              class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-1 mb-text-[10px] mb-text-gray-900 mb-resize-y mb-font-mono" placeholder="color: red; transform: rotate(5deg);"></textarea>
          </div>
        </template>

        <!-- ===== ANIMAZIONE ===== -->
        <template v-if="activeTab === 'Animazione'">
          <!-- Toggle Semplice / Timeline -->
          <div class="mb-flex mb-bg-gray-800 mb-rounded mb-p-0.5 mb-mb-3">
            <button
              @click="setAnimMode('simple')"
              :class="['mb-flex-1 mb-py-1 mb-rounded mb-text-[10px] mb-font-medium mb-transition-colors',
                !hasTimeline ? 'mb-bg-primary-600 mb-text-white' : 'mb-text-gray-400 hover:mb-text-gray-300']"
            >Semplice</button>
            <button
              @click="setAnimMode('timeline')"
              :class="['mb-flex-1 mb-py-1 mb-rounded mb-text-[10px] mb-font-medium mb-transition-colors',
                hasTimeline ? 'mb-bg-primary-600 mb-text-white' : 'mb-text-gray-400 hover:mb-text-gray-300']"
            >Timeline</button>
          </div>

          <!-- Modo SEMPLICE -->
          <template v-if="!hasTimeline">
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">Entrata</p>
            <div>
              <label class="mps-label">Animazione entrata</label>
              <select :value="layer.animIn" @change="up('animIn', $event.target.value)" class="mps-select">
                <optgroup v-for="g in animationsInGroups" :key="g.label" :label="g.label">
                  <option v-for="a in g.options" :key="a.value" :value="a.value">{{ a.label }}</option>
                </optgroup>
              </select>
            </div>
            <div>
              <label class="mps-label">Durata (ms)</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <input type="range" :value="layer.animInDuration" @input="up('animInDuration', parseFloat($event.target.value))" min="100" max="3000" step="50" class="mb-flex-1" />
                <span class="mps-val">{{ layer.animInDuration }}</span>
              </div>
            </div>
            <div>
              <label class="mps-label">Ritardo (ms)</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <input type="range" :value="layer.animInDelay" @input="up('animInDelay', parseFloat($event.target.value))" min="0" max="3000" step="50" class="mb-flex-1" />
                <span class="mps-val">{{ layer.animInDelay }}</span>
              </div>
            </div>

            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">Uscita</p>
            <div>
              <label class="mps-label">Animazione uscita</label>
              <select :value="layer.animOut" @change="up('animOut', $event.target.value)" class="mps-select">
                <optgroup v-for="g in animationsOutGroups" :key="g.label" :label="g.label">
                  <option v-for="a in g.options" :key="a.value" :value="a.value">{{ a.label }}</option>
                </optgroup>
              </select>
            </div>
            <div>
              <label class="mps-label">Durata (ms)</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <input type="range" :value="layer.animOutDuration" @input="up('animOutDuration', parseFloat($event.target.value))" min="100" max="3000" step="50" class="mb-flex-1" />
                <span class="mps-val">{{ layer.animOutDuration }}</span>
              </div>
            </div>
            <div>
              <label class="mps-label">Ritardo (ms)</label>
              <div class="mb-flex mb-items-center mb-gap-2">
                <input type="range" :value="layer.animOutDelay" @input="up('animOutDelay', parseFloat($event.target.value))" min="0" max="3000" step="50" class="mb-flex-1" />
                <span class="mps-val">{{ layer.animOutDelay }}</span>
              </div>
            </div>
            <div>
              <label class="mps-label">Curva di easing</label>
              <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.animEasing" :options="OPTS_EASING" @update:model-value="up('animEasing', $event)" />
            </div>

            <!-- Character Animation (solo per text) -->
            <template v-if="layer.type === 'text'">
              <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
              <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">Animazione Testo</p>
              <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                <input type="checkbox" :checked="!!layer.charAnim" @change="toggleCharAnim($event.target.checked)" class="mb-rounded" />
                <span class="mb-text-[11px] mb-text-gray-400">Anima caratteri/parole</span>
              </label>
              <template v-if="layer.charAnim">
                <div>
                  <label class="mps-label">Dividi per</label>
                  <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.charAnim.split || 'chars'" :options="OPTS_CHAR_SPLIT" @update:model-value="upCharAnim('split', $event)" />
                </div>
                <div>
                  <label class="mps-label">Direzione</label>
                  <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.charAnim.direction || 'forward'" :options="OPTS_CHAR_DIRECTION" @update:model-value="upCharAnim('direction', $event)" />
                </div>
                <div>
                  <label class="mps-label">Stagger (ms)</label>
                  <div class="mb-flex mb-items-center mb-gap-2">
                    <input type="range" :value="layer.charAnim.stagger || 30" @input="upCharAnim('stagger', parseFloat($event.target.value))" min="5" max="200" step="5" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.charAnim.stagger || 30 }}</span>
                  </div>
                </div>
                <div>
                  <label class="mps-label">Offset X (px)</label>
                  <div class="mb-flex mb-items-center mb-gap-2">
                    <input type="range" :value="layer.charAnim.offsetX || 0" @input="upCharAnim('offsetX', parseFloat($event.target.value))" min="-100" max="100" step="5" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.charAnim.offsetX || 0 }}</span>
                  </div>
                </div>
                <div>
                  <label class="mps-label">Offset Y (px)</label>
                  <div class="mb-flex mb-items-center mb-gap-2">
                    <input type="range" :value="layer.charAnim.offsetY || 0" @input="upCharAnim('offsetY', parseFloat($event.target.value))" min="-100" max="100" step="5" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.charAnim.offsetY || 0 }}</span>
                  </div>
                </div>
                <div>
                  <label class="mps-label">Rotazione (deg)</label>
                  <div class="mb-flex mb-items-center mb-gap-2">
                    <input type="range" :value="layer.charAnim.rotation || 0" @input="upCharAnim('rotation', parseFloat($event.target.value))" min="-180" max="180" step="5" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.charAnim.rotation || 0 }}</span>
                  </div>
                </div>
                <div>
                  <label class="mps-label">Scala iniziale</label>
                  <div class="mb-flex mb-items-center mb-gap-2">
                    <input type="range" :value="layer.charAnim.scale ?? 1" @input="upCharAnim('scale', parseFloat($event.target.value))" min="0" max="3" step="0.1" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.charAnim.scale ?? 1 }}</span>
                  </div>
                </div>
                <div>
                  <label class="mps-label">Opacità iniziale (%)</label>
                  <div class="mb-flex mb-items-center mb-gap-2">
                    <input type="range" :value="layer.charAnim.opacity ?? 0" @input="upCharAnim('opacity', parseFloat($event.target.value))" min="0" max="100" step="5" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.charAnim.opacity ?? 0 }}</span>
                  </div>
                </div>
                <div>
                  <label class="mps-label">Blur iniziale (px)</label>
                  <div class="mb-flex mb-items-center mb-gap-2">
                    <input type="range" :value="layer.charAnim.blur || 0" @input="upCharAnim('blur', parseFloat($event.target.value))" min="0" max="20" step="1" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.charAnim.blur || 0 }}</span>
                  </div>
                </div>
                <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                  <input type="checkbox" :checked="layer.charAnim.yoyo" @change="upCharAnim('yoyo', $event.target.checked)" class="mb-rounded" />
                  <span class="mb-text-[11px] mb-text-gray-400">Yoyo (andata e ritorno)</span>
                </label>
              </template>
            </template>

            <!-- Loop Animation -->
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">Loop</p>
            <div>
              <label class="mps-label">Animazione continua</label>
              <select :value="layer.animLoop || 'none'" @change="up('animLoop', $event.target.value)" class="mps-select">
                <optgroup v-for="g in loopAnimGroups" :key="g.label" :label="g.label">
                  <option v-for="a in g.options" :key="a.value" :value="a.value">{{ a.label }}</option>
                </optgroup>
              </select>
            </div>
            <template v-if="layer.animLoop && layer.animLoop !== 'none'">
              <div>
                <label class="mps-label">Durata loop (ms)</label>
                <div class="mb-flex mb-items-center mb-gap-2">
                  <input type="range" :value="layer.animLoopDuration || 3000" @input="up('animLoopDuration', parseFloat($event.target.value))" min="500" max="10000" step="100" class="mb-flex-1" />
                  <span class="mps-val">{{ layer.animLoopDuration || 3000 }}</span>
                </div>
              </div>
              <div>
                <label class="mps-label">Easing loop</label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.animLoopEasing || 'ease-in-out'" :options="OPTS_EASING" @update:model-value="up('animLoopEasing', $event)" />
              </div>
            </template>

            <!-- Hover Effects -->
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">Hover</p>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="!!layer.hover" @change="toggleHover($event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">Effetto hover attivo</span>
            </label>
            <template v-if="layer.hover">
              <!-- Transform -->
              <p class="mb-text-[9px] mb-text-gray-500 mb-uppercase mb-mt-1">Transform</p>
              <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                <div>
                  <label class="mps-label">Scala</label>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <input type="range" :value="layer.hover.scale ?? 1" @input="upHover('scale', parseFloat($event.target.value))" min="0.5" max="2" step="0.05" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.hover.scale ?? 1 }}</span>
                  </div>
                </div>
                <div>
                  <label class="mps-label">Rotazione Z</label>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <input type="range" :value="layer.hover.rotation ?? 0" @input="upHover('rotation', parseFloat($event.target.value))" min="-45" max="45" step="1" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.hover.rotation ?? 0 }}</span>
                  </div>
                </div>
                <div>
                  <label class="mps-label">Rotazione X</label>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <input type="range" :value="layer.hover.rotateX ?? 0" @input="upHover('rotateX', parseFloat($event.target.value))" min="-90" max="90" step="5" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.hover.rotateX ?? 0 }}</span>
                  </div>
                </div>
                <div>
                  <label class="mps-label">Rotazione Y</label>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <input type="range" :value="layer.hover.rotateY ?? 0" @input="upHover('rotateY', parseFloat($event.target.value))" min="-90" max="90" step="5" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.hover.rotateY ?? 0 }}</span>
                  </div>
                </div>
                <div>
                  <label class="mps-label">Skew X</label>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <input type="range" :value="layer.hover.skewX ?? 0" @input="upHover('skewX', parseFloat($event.target.value))" min="-30" max="30" step="1" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.hover.skewX ?? 0 }}</span>
                  </div>
                </div>
                <div>
                  <label class="mps-label">Skew Y</label>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <input type="range" :value="layer.hover.skewY ?? 0" @input="upHover('skewY', parseFloat($event.target.value))" min="-30" max="30" step="1" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.hover.skewY ?? 0 }}</span>
                  </div>
                </div>
                <div>
                  <label class="mps-label">Offset X (%)</label>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <input type="range" :value="layer.hover.x ?? 0" @input="upHover('x', parseFloat($event.target.value))" min="-20" max="20" step="1" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.hover.x ?? 0 }}</span>
                  </div>
                </div>
                <div>
                  <label class="mps-label">Offset Y (%)</label>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <input type="range" :value="layer.hover.y ?? 0" @input="upHover('y', parseFloat($event.target.value))" min="-20" max="20" step="1" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.hover.y ?? 0 }}</span>
                  </div>
                </div>
              </div>
              <!-- Aspetto -->
              <p class="mb-text-[9px] mb-text-gray-500 mb-uppercase mb-mt-2">Aspetto</p>
              <div>
                <label class="mps-label">Opacita (%)</label>
                <div class="mb-flex mb-items-center mb-gap-2">
                  <input type="range" :value="layer.hover.opacity ?? 100" @input="upHover('opacity', parseFloat($event.target.value))" min="0" max="100" step="5" class="mb-flex-1" />
                  <span class="mps-val">{{ layer.hover.opacity ?? 100 }}</span>
                </div>
              </div>
              <template v-if="layer.type === 'text' || layer.type === 'button'">
                <div>
                  <label class="mps-label">Colore testo hover</label>
                  <FieldColor :modelValue="layer.hover.color || ''" @update:modelValue="upHover('color', $event)" />
                </div>
              </template>
              <div>
                <label class="mps-label">Sfondo hover</label>
                <FieldColor :modelValue="layer.hover.bgColor || ''" @update:modelValue="upHover('bgColor', $event)" />
              </div>
              <div>
                <label class="mps-label">Bordo hover</label>
                <FieldColor :modelValue="layer.hover.borderColor || ''" @update:modelValue="upHover('borderColor', $event)" />
              </div>
              <div>
                <label class="mps-label">Border radius hover</label>
                <div class="mb-flex mb-items-center mb-gap-2">
                  <input type="range" :value="layer.hover.borderRadius ?? ''" @input="upHover('borderRadius', $event.target.value ? parseFloat($event.target.value) : '')" min="0" max="200" step="1" class="mb-flex-1" />
                  <span class="mps-val">{{ layer.hover.borderRadius ?? '-' }}</span>
                </div>
              </div>
              <!-- Filtri hover -->
              <p class="mb-text-[9px] mb-text-gray-500 mb-uppercase mb-mt-2">Filtri hover</p>
              <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                <div>
                  <label class="mps-label">Blur (px)</label>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <input type="range" :value="layer.hover.blur ?? 0" @input="upHover('blur', parseFloat($event.target.value))" min="0" max="20" step="1" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.hover.blur ?? 0 }}</span>
                  </div>
                </div>
                <div>
                  <label class="mps-label">Luminosita (%)</label>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <input type="range" :value="layer.hover.brightness ?? 100" @input="upHover('brightness', parseFloat($event.target.value))" min="0" max="200" step="5" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.hover.brightness ?? 100 }}</span>
                  </div>
                </div>
                <div>
                  <label class="mps-label">Scala grigi (%)</label>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <input type="range" :value="layer.hover.grayscale ?? 0" @input="upHover('grayscale', parseFloat($event.target.value))" min="0" max="100" step="5" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.hover.grayscale ?? 0 }}</span>
                  </div>
                </div>
              </div>
              <!-- Transizione -->
              <p class="mb-text-[9px] mb-text-gray-500 mb-uppercase mb-mt-2">Transizione</p>
              <div>
                <label class="mps-label">Durata (ms)</label>
                <div class="mb-flex mb-items-center mb-gap-2">
                  <input type="range" :value="layer.hover.duration ?? 300" @input="upHover('duration', parseFloat($event.target.value))" min="100" max="1000" step="50" class="mb-flex-1" />
                  <span class="mps-val">{{ layer.hover.duration ?? 300 }}</span>
                </div>
              </div>
              <div>
                <label class="mps-label">Easing</label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.hover.easing || 'ease'" :options="OPTS_EASING" @update:model-value="upHover('easing', $event)" />
              </div>
              <div>
                <label class="mps-label">Cursore hover</label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.hover.cursor || ''" :options="OPTS_HOVER_CURSOR" @update:model-value="upHover('cursor', $event)" />
              </div>
            </template>

            <!-- Mask / Clip-Path -->
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">Mask (Clip-Path)</p>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="!!layer.mask" @change="toggleMask($event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">Maschera attiva</span>
            </label>
            <template v-if="layer.mask">
              <div>
                <label class="mps-label">Preset</label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.mask.preset || 'custom'" :options="OPTS_MASK_PRESET" @update:model-value="applyMaskPreset($event)" />
              </div>
              <div class="mb-grid mb-grid-cols-2 mb-gap-2">
                <div>
                  <label class="mps-label">Top (%)</label>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <input type="range" :value="layer.mask.top ?? 0" @input="upMask('top', parseFloat($event.target.value))" min="0" max="100" step="1" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.mask.top ?? 0 }}</span>
                  </div>
                </div>
                <div>
                  <label class="mps-label">Right (%)</label>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <input type="range" :value="layer.mask.right ?? 0" @input="upMask('right', parseFloat($event.target.value))" min="0" max="100" step="1" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.mask.right ?? 0 }}</span>
                  </div>
                </div>
                <div>
                  <label class="mps-label">Bottom (%)</label>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <input type="range" :value="layer.mask.bottom ?? 0" @input="upMask('bottom', parseFloat($event.target.value))" min="0" max="100" step="1" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.mask.bottom ?? 0 }}</span>
                  </div>
                </div>
                <div>
                  <label class="mps-label">Left (%)</label>
                  <div class="mb-flex mb-items-center mb-gap-1">
                    <input type="range" :value="layer.mask.left ?? 0" @input="upMask('left', parseFloat($event.target.value))" min="0" max="100" step="1" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.mask.left ?? 0 }}</span>
                  </div>
                </div>
              </div>
            </template>

            <!-- Ken Burns per Layer -->
            <template v-if="layer.type === 'image'">
              <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
              <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">Ken Burns</p>
              <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                <input type="checkbox" :checked="!!layer.kenBurns" @change="toggleKenBurns($event.target.checked)" class="mb-rounded" />
                <span class="mb-text-[11px] mb-text-gray-400">Attivo</span>
              </label>
              <template v-if="layer.kenBurns">
                <div>
                  <label class="mps-label">Tipo</label>
                  <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.kenBurns.type || 'zoomIn'" :options="OPTS_KENBURNS_TYPE" @update:model-value="upKenBurns('type', $event)" />
                </div>
                <div>
                  <label class="mps-label">Intensita (%)</label>
                  <div class="mb-flex mb-items-center mb-gap-2">
                    <input type="range" :value="layer.kenBurns.intensity ?? 20" @input="upKenBurns('intensity', parseFloat($event.target.value))" min="5" max="50" step="5" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.kenBurns.intensity ?? 20 }}</span>
                  </div>
                </div>
                <div>
                  <label class="mps-label">Durata (ms)</label>
                  <div class="mb-flex mb-items-center mb-gap-2">
                    <input type="range" :value="layer.kenBurns.duration ?? 5000" @input="upKenBurns('duration', parseFloat($event.target.value))" min="1000" max="20000" step="500" class="mb-flex-1" />
                    <span class="mps-val">{{ layer.kenBurns.duration ?? 5000 }}</span>
                  </div>
                </div>
              </template>
            </template>

            <!-- Motion Path -->
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">Motion Path</p>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="!!layer.motionPath" @change="toggleMotionPath($event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">Anima lungo percorso</span>
            </label>
            <template v-if="layer.motionPath">
              <div>
                <label class="mps-label">Preset percorso</label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.motionPath.preset || 'circle'" :options="OPTS_MOTION_PRESET" @update:model-value="applyMotionPreset($event)" />
              </div>
              <template v-if="layer.motionPath.preset === 'custom'">
                <div>
                  <label class="mps-label">SVG Path (d="")</label>
                  <textarea :value="layer.motionPath.path" @input="upMotionPath('path', $event.target.value)" rows="2"
                    class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-1 mb-text-[10px] mb-text-gray-900 mb-resize-y mb-font-mono" placeholder="M0,0 C50,0 50,100 100,100"></textarea>
                </div>
              </template>
              <div>
                <label class="mps-label">Durata (ms)</label>
                <div class="mb-flex mb-items-center mb-gap-2">
                  <input type="range" :value="layer.motionPath.duration ?? 3000" @input="upMotionPath('duration', parseFloat($event.target.value))" min="500" max="20000" step="500" class="mb-flex-1" />
                  <span class="mps-val">{{ layer.motionPath.duration ?? 3000 }}</span>
                </div>
              </div>
              <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                <input type="checkbox" :checked="layer.motionPath.autoRotate" @change="upMotionPath('autoRotate', $event.target.checked)" class="mb-rounded" />
                <span class="mb-text-[11px] mb-text-gray-400">Ruota lungo il percorso</span>
              </label>
              <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                <input type="checkbox" :checked="layer.motionPath.loop !== false" @change="upMotionPath('loop', $event.target.checked)" class="mb-rounded" />
                <span class="mb-text-[11px] mb-text-gray-400">Loop</span>
              </label>
              <div>
                <label class="mps-label">Easing</label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.motionPath.easing || 'linear'" :options="OPTS_EASING_LINEAR_FIRST" @update:model-value="upMotionPath('easing', $event)" />
              </div>
            </template>

            <!-- Parallax Layer -->
            <div class="mb-border-t mb-border-gray-700 mb-pt-2 mb-mt-2"></div>
            <p class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">Parallax Layer</p>
            <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
              <input type="checkbox" :checked="!!layer.parallax" @change="toggleParallax($event.target.checked)" class="mb-rounded" />
              <span class="mb-text-[11px] mb-text-gray-400">Parallax attivo</span>
            </label>
            <template v-if="layer.parallax">
              <div>
                <label class="mps-label">Tipo</label>
                <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="layer.parallax.type || 'mouse'" :options="OPTS_PARALLAX_TYPE" @update:model-value="upParallax('type', $event)" />
              </div>
              <div>
                <label class="mps-label">Profondita (1-20)</label>
                <div class="mb-flex mb-items-center mb-gap-2">
                  <input type="range" :value="layer.parallax.depth ?? 5" @input="upParallax('depth', parseFloat($event.target.value))" min="1" max="20" step="1" class="mb-flex-1" />
                  <span class="mps-val">{{ layer.parallax.depth ?? 5 }}</span>
                </div>
              </div>
              <template v-if="layer.parallax.type === 'mouse' || layer.parallax.type === 'both'">
                <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
                  <input type="checkbox" :checked="layer.parallax.tilt3d" @change="upParallax('tilt3d', $event.target.checked)" class="mb-rounded" />
                  <span class="mb-text-[11px] mb-text-gray-400">Tilt 3D</span>
                </label>
              </template>
              <template v-if="layer.parallax.type === 'scroll' || layer.parallax.type === 'both'">
                <div>
                  <label class="mps-label">Velocita scroll</label>
                  <div class="mb-flex mb-items-center mb-gap-2">
                    <input type="range" :value="layer.parallax.scrollSpeed ?? 0.5" @input="upParallax('scrollSpeed', parseFloat($event.target.value))" min="0.1" max="2" step="0.1" class="mb-flex-1" />
                    <span class="mps-val">{{ (layer.parallax.scrollSpeed ?? 0.5).toFixed(1) }}</span>
                  </div>
                </div>
              </template>
            </template>
          </template>

          <!-- Modo TIMELINE -->
          <template v-else>
            <p class="mb-text-[10px] mb-text-gray-500 mb-italic mb-mb-2">
              Usa la timeline sotto il canvas per aggiungere e spostare i keyframe.
            </p>
            <KeyframeProperties
              :keyframe="selectedKeyframe"
              :timeline="layer.timeline"
              @update-keyframe="(id, updates) => $emit('update-keyframe', id, updates)"
              @capture-from-canvas="$emit('capture-from-canvas')"
            />
          </template>
        </template>

      </div>
    </template>

    <!-- No selection -->
    <div v-else class="mb-p-4 mb-text-xs mb-text-gray-500 mb-text-center mb-mt-8">
      Seleziona un livello per modificarne le proprietà
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import IconPicker from './IconPicker.vue';
import iconsSvg from './uikitIconsSvg.js';
import FieldColor from '@/components/Builder/fields/FieldColor.vue';
import FieldSelect from '../Builder/fields/FieldSelect.vue';
import KeyframeProperties from './KeyframeProperties.vue';
import { defaultTimeline } from './timelineUtils.js';

const props = defineProps({
  layer: { type: Object, default: null },
  selectedKeyframeId: { type: String, default: null },
  activeBreakpoint: { type: String, default: 'desktop' },
  slideLayers: { type: Array, default: () => [] },
});

const targetableLayers = computed(() => {
  const cur = props.layer?.id;
  return (props.slideLayers || []).filter(l => l && l.id && l.id !== cur);
});

const targetLayerOptions = computed(() => [
  { value: '', label: '— Seleziona un layer —' },
  ...targetableLayers.value.map(l => ({ value: l.id, label: targetLayerLabel(l) })),
]);

function fmt(v) {
  if (v === 'auto' || v === undefined || v === null) return v ?? '';
  const n = Number(v);
  if (!Number.isFinite(n)) return v;
  return (Math.round(n * 10) / 10).toString();
}

function targetLayerLabel(l) {
  const icons = { text: 'T', image: '🖼', video: '▶', button: '▢', icon: '★', shape: '◆', audio: '♫' };
  const ico = icons[l.type] || '?';
  let name = l.type;
  if (l.type === 'text' || l.type === 'button') name = l.content || l.type;
  else if (l.type === 'icon') name = l.iconName || 'icona';
  else if (l.type === 'image') name = 'Immagine';
  else if (l.type === 'video') name = 'Video';
  else if (l.type === 'shape') name = 'Forma';
  const trimmed = String(name).replace(/\s+/g, ' ').trim();
  const short = trimmed.length > 40 ? trimmed.slice(0, 40) + '…' : trimmed;
  return `${ico} ${short}`;
}

const emit = defineEmits(['update', 'update-keyframe', 'capture-from-canvas']);

const hasTimeline = computed(() => {
  return !!(props.layer?.timeline?.keyframes?.length);
});

const selectedKeyframe = computed(() => {
  if (!hasTimeline.value || !props.selectedKeyframeId) return null;
  return props.layer.timeline.keyframes.find(kf => kf.id === props.selectedKeyframeId) || null;
});

function setAnimMode(mode) {
  if (mode === 'timeline' && !hasTimeline.value) {
    // Attiva timeline con valori default basati sulla posizione corrente del layer
    up('timeline', defaultTimeline(props.layer));
  } else if (mode === 'simple' && hasTimeline.value) {
    // Disattiva timeline
    up('timeline', null);
  }
}

const showIconPicker = ref(false);

const activeTab = ref('Contenuto');
const tabs = ['Contenuto', 'Posizione', 'Stile', 'Animazione'];

// Responsive: risolve il valore effettivo per il breakpoint attivo
function rv(key) {
  if (!props.layer) return undefined;
  const bp = props.activeBreakpoint;
  if (bp !== 'desktop') {
    const chain = ['notebook', 'tablet', 'mobile'];
    const idx = chain.indexOf(bp);
    for (let i = idx; i >= 0; i--) {
      const ov = props.layer.responsive?.[chain[i]];
      if (ov && ov[key] !== undefined && ov[key] !== null) return ov[key];
    }
  }
  return props.layer[key];
}

function isOverridden(key) {
  const bp = props.activeBreakpoint;
  if (bp === 'desktop') return false;
  return props.layer?.responsive?.[bp]?.[key] !== undefined && props.layer?.responsive?.[bp]?.[key] !== null;
}

function clearOverride(key) {
  const bp = props.activeBreakpoint;
  if (bp === 'desktop' || !props.layer?.responsive?.[bp]) return;
  delete props.layer.responsive[bp][key];
}

const hasAnyOverride = computed(() => {
  const bp = props.activeBreakpoint;
  if (bp === 'desktop') return false;
  const ov = props.layer?.responsive?.[bp];
  return ov && Object.keys(ov).length > 0;
});

function clearAllOverrides() {
  const bp = props.activeBreakpoint;
  if (bp === 'desktop' || !props.layer?.responsive) return;
  props.layer.responsive[bp] = null;
}

const animationsInGroups = [
  { label: 'Base', options: [
    { value: 'none', label: 'Nessuna' },
    { value: 'fadeIn', label: 'Fade In' },
  ]},
  { label: 'Fade', options: [
    { value: 'fadeInUp', label: 'Fade In Up' },
    { value: 'fadeInDown', label: 'Fade In Down' },
    { value: 'fadeInLeft', label: 'Fade In Left' },
    { value: 'fadeInRight', label: 'Fade In Right' },
  ]},
  { label: 'Slide', options: [
    { value: 'slideInLeft', label: 'Slide In Left' },
    { value: 'slideInRight', label: 'Slide In Right' },
    { value: 'slideInUp', label: 'Slide In Up' },
    { value: 'slideInDown', label: 'Slide In Down' },
    { value: 'slideShortFromTop', label: 'Slide Short Top' },
    { value: 'slideShortFromBottom', label: 'Slide Short Bottom' },
    { value: 'slideShortFromLeft', label: 'Slide Short Left' },
    { value: 'slideShortFromRight', label: 'Slide Short Right' },
    { value: 'smoothSlideFromBottom', label: 'Smooth Slide Bottom' },
    { value: 'smoothSlideFromTop', label: 'Smooth Slide Top' },
    { value: 'smoothSlideFromLeft', label: 'Smooth Slide Left' },
    { value: 'smoothSlideFromRight', label: 'Smooth Slide Right' },
  ]},
  { label: 'Skew', options: [
    { value: 'skewFromLeft', label: 'Skew From Left' },
    { value: 'skewFromRight', label: 'Skew From Right' },
    { value: 'skewShortFromLeft', label: 'Skew Short Left' },
    { value: 'skewShortFromRight', label: 'Skew Short Right' },
  ]},
  { label: 'Flip 3D', options: [
    { value: 'flipFromTop', label: 'Flip From Top' },
    { value: 'flipFromBottom', label: 'Flip From Bottom' },
    { value: 'flipFromLeft', label: 'Flip From Left' },
    { value: 'flipFromRight', label: 'Flip From Right' },
  ]},
  { label: 'Rotate', options: [
    { value: 'rotateIn', label: 'Rotate In' },
    { value: 'rotateInFromBottom', label: 'Rotate In Bottom' },
    { value: 'rotate3D', label: 'Rotate 3D' },
    { value: 'rotateInFromLeft', label: 'Rotate In Left' },
    { value: 'rotateInFromRight', label: 'Rotate In Right' },
  ]},
  { label: 'Pop/Bounce', options: [
    { value: 'zoomIn', label: 'Zoom In' },
    { value: 'bounceIn', label: 'Bounce In' },
    { value: 'popUpSmooth', label: 'Pop Up Smooth' },
    { value: 'popUpBack', label: 'Pop Up Back' },
    { value: 'bounceInUp', label: 'Bounce In Up' },
    { value: 'bounceInDown', label: 'Bounce In Down' },
  ]},
  { label: 'Mask/Reveal', options: [
    { value: 'maskFromLeft', label: 'Mask From Left' },
    { value: 'maskFromRight', label: 'Mask From Right' },
    { value: 'maskFromTop', label: 'Mask From Top' },
    { value: 'maskFromBottom', label: 'Mask From Bottom' },
    { value: 'maskZoomOut', label: 'Mask Zoom Out' },
    { value: 'maskCenter', label: 'Mask Center' },
  ]},
];

const animationsOutGroups = [
  { label: 'Base', options: [
    { value: 'none', label: 'Nessuna' },
    { value: 'fadeOut', label: 'Fade Out' },
  ]},
  { label: 'Fade', options: [
    { value: 'fadeOutUp', label: 'Fade Out Up' },
    { value: 'fadeOutDown', label: 'Fade Out Down' },
    { value: 'fadeOutLeft', label: 'Fade Out Left' },
    { value: 'fadeOutRight', label: 'Fade Out Right' },
  ]},
  { label: 'Slide', options: [
    { value: 'slideOutLeft', label: 'Slide Out Left' },
    { value: 'slideOutRight', label: 'Slide Out Right' },
    { value: 'slideOutUp', label: 'Slide Out Up' },
    { value: 'slideOutDown', label: 'Slide Out Down' },
    { value: 'slideShortOutTop', label: 'Slide Short Top' },
    { value: 'slideShortOutBottom', label: 'Slide Short Bottom' },
    { value: 'slideShortOutLeft', label: 'Slide Short Left' },
    { value: 'slideShortOutRight', label: 'Slide Short Right' },
    { value: 'smoothSlideOutBottom', label: 'Smooth Slide Bottom' },
    { value: 'smoothSlideOutTop', label: 'Smooth Slide Top' },
    { value: 'smoothSlideOutLeft', label: 'Smooth Slide Left' },
    { value: 'smoothSlideOutRight', label: 'Smooth Slide Right' },
  ]},
  { label: 'Skew', options: [
    { value: 'skewOutLeft', label: 'Skew Out Left' },
    { value: 'skewOutRight', label: 'Skew Out Right' },
    { value: 'skewShortOutLeft', label: 'Skew Short Left' },
    { value: 'skewShortOutRight', label: 'Skew Short Right' },
  ]},
  { label: 'Flip 3D', options: [
    { value: 'flipOutTop', label: 'Flip Out Top' },
    { value: 'flipOutBottom', label: 'Flip Out Bottom' },
    { value: 'flipOutLeft', label: 'Flip Out Left' },
    { value: 'flipOutRight', label: 'Flip Out Right' },
  ]},
  { label: 'Rotate', options: [
    { value: 'rotateOut', label: 'Rotate Out' },
    { value: 'rotateOutToBottom', label: 'Rotate Out Bottom' },
    { value: 'rotateOut3D', label: 'Rotate Out 3D' },
    { value: 'rotateOutToLeft', label: 'Rotate Out Left' },
    { value: 'rotateOutToRight', label: 'Rotate Out Right' },
  ]},
  { label: 'Pop/Bounce', options: [
    { value: 'zoomOut', label: 'Zoom Out' },
    { value: 'bounceOut', label: 'Bounce Out' },
    { value: 'popOutSmooth', label: 'Pop Out Smooth' },
    { value: 'popOutBack', label: 'Pop Out Back' },
    { value: 'bounceOutUp', label: 'Bounce Out Up' },
    { value: 'bounceOutDown', label: 'Bounce Out Down' },
  ]},
  { label: 'Mask/Reveal', options: [
    { value: 'maskOutLeft', label: 'Mask Out Left' },
    { value: 'maskOutRight', label: 'Mask Out Right' },
    { value: 'maskOutTop', label: 'Mask Out Top' },
    { value: 'maskOutBottom', label: 'Mask Out Bottom' },
    { value: 'maskZoomIn', label: 'Mask Zoom In' },
    { value: 'maskOutCenter', label: 'Mask Out Center' },
  ]},
];

const loopAnimGroups = [
  { label: 'Nessuno', options: [{ value: 'none', label: 'Nessun loop' }] },
  { label: 'Pendolo', options: [
    { value: 'pendulum', label: 'Pendolo' },
    { value: 'pendulumBelow', label: 'Pendolo basso' },
    { value: 'pendulumAbove', label: 'Pendolo alto' },
    { value: 'pendulumLeft', label: 'Pendolo sinistra' },
    { value: 'pendulumRight', label: 'Pendolo destra' },
  ]},
  { label: 'Onda', options: [
    { value: 'waveSmallLeft', label: 'Onda piccola SX' },
    { value: 'waveSmallRight', label: 'Onda piccola DX' },
    { value: 'waveBigLeft', label: 'Onda grande SX' },
    { value: 'waveBigRight', label: 'Onda grande DX' },
  ]},
  { label: 'Wiggle', options: [
    { value: 'wiggleY', label: 'Wiggle Y' },
    { value: 'wiggleX', label: 'Wiggle X' },
    { value: 'wiggle3D', label: 'Wiggle 3D' },
    { value: 'crazyWiggle', label: 'Crazy Wiggle' },
  ]},
  { label: 'Rotazione', options: [
    { value: 'spinCW', label: 'Spin orario' },
    { value: 'spinCCW', label: 'Spin antiorario' },
  ]},
  { label: 'Effetto', options: [
    { value: 'blinkLoop', label: 'Lampeggio' },
    { value: 'floatLoop', label: 'Galleggiamento' },
    { value: 'pulseLoop', label: 'Pulsazione' },
    { value: 'breathLoop', label: 'Respiro' },
    { value: 'slideHLoop', label: 'Slide orizzontale' },
    { value: 'hoverLoop', label: 'Hover dolce' },
  ]},
];

function up(key, val) {
  emit('update', key, val);
}

function setAction(field, value) {
  const current = props.layer?.action || { type: 'none' };
  if (field === 'type') {
    if (value === 'none') {
      up('action', null);
    } else {
      up('action', { ...current, type: value });
    }
  } else {
    up('action', { ...current, [field]: value });
  }
}

function toggleCharAnim(enabled) {
  if (enabled) {
    up('charAnim', { split: 'chars', direction: 'forward', stagger: 30 });
  } else {
    up('charAnim', null);
  }
}

function upCharAnim(key, val) {
  if (!props.layer?.charAnim) return;
  up('charAnim', { ...props.layer.charAnim, [key]: val });
}

function toggleHover(enabled) {
  if (enabled) {
    up('hover', { scale: 1, rotation: 0, rotateX: 0, rotateY: 0, skewX: 0, skewY: 0, opacity: 100, x: 0, y: 0, duration: 300, easing: 'ease', color: '', bgColor: '', borderColor: '', borderRadius: '', blur: 0, brightness: 100, grayscale: 0, cursor: '' });
  } else {
    up('hover', null);
  }
}

function upHover(key, val) {
  if (!props.layer?.hover) return;
  up('hover', { ...props.layer.hover, [key]: val });
}

function pickImage() {
  if (!window.wp?.media) return;
  const frame = wp.media({ title: 'Seleziona immagine', multiple: false, library: { type: 'image' } });
  frame.on('select', () => {
    const url = frame.state().get('selection').first().toJSON().url;
    up('imageSrc', url);
  });
  frame.open();
}

function pickVideo() {
  if (!window.wp?.media) return;
  const frame = wp.media({ title: 'Seleziona video', multiple: false, library: { type: 'video' } });
  frame.on('select', () => {
    const url = frame.state().get('selection').first().toJSON().url;
    up('videoSrc', url);
  });
  frame.open();
}

function pickAudio() {
  if (!window.wp?.media) return;
  const frame = wp.media({ title: 'Seleziona audio', multiple: false, library: { type: 'audio' } });
  frame.on('select', () => {
    const url = frame.state().get('selection').first().toJSON().url;
    up('audioSrc', url);
  });
  frame.open();
}

const blendModes = ['normal','multiply','screen','overlay','darken','lighten','color-dodge','color-burn','hard-light','soft-light','difference','exclusion','hue','saturation','color','luminosity'];

// Option arrays per FieldSelect (stessi value dei vecchi <option>: dati salvati invariati)
const OPTS_TAG = ['h1','h2','h3','h4','h5','h6','p','span','div'].map(v => ({ value: v, label: v }));
const OPTS_LINK_TARGET = [
  { value: '_self', label: 'Stessa scheda' },
  { value: '_blank', label: 'Nuova scheda' },
];
const OPTS_ACTION_TYPE = [
  { value: 'none', label: 'Nessuna' },
  { value: 'nextSlide', label: 'Slide successiva' },
  { value: 'prevSlide', label: 'Slide precedente' },
  { value: 'goToSlide', label: 'Vai a slide N' },
  { value: 'openUrl', label: 'Apri URL' },
  { value: 'scrollBelow', label: 'Scroll sotto slider' },
  { value: 'toggleLayer', label: 'Toggle visibilita layer' },
];
const OPTS_FONT_WEIGHT = ['300','400','500','600','700','800','900'].map(v => ({ value: v, label: v }));
const OPTS_FONT_STYLE = [
  { value: 'normal', label: 'Normale' },
  { value: 'italic', label: 'Corsivo' },
];
const OPTS_TEXT_TRANSFORM = [
  { value: 'none', label: 'Nessuna' },
  { value: 'uppercase', label: 'MAIUSCOLO' },
  { value: 'lowercase', label: 'minuscolo' },
  { value: 'capitalize', label: 'Capitalizzato' },
];
const OPTS_TEXT_DECORATION = [
  { value: 'none', label: 'Nessuna' },
  { value: 'underline', label: 'Sottolineato' },
  { value: 'line-through', label: 'Barrato' },
  { value: 'overline', label: 'Sopralineato' },
];
const OPTS_BORDER_STYLE = [
  { value: 'solid', label: 'Solido' },
  { value: 'dashed', label: 'Tratteggiato' },
  { value: 'dotted', label: 'Puntinato' },
  { value: 'double', label: 'Doppio' },
];
const OPTS_OBJECT_FIT = [
  { value: 'cover', label: 'Cover' },
  { value: 'contain', label: 'Contain' },
  { value: 'fill', label: 'Fill' },
  { value: 'none', label: 'Nessuno' },
];
const OPTS_OBJECT_POSITION = [
  { value: 'center', label: 'Centro' },
  { value: 'top', label: 'Alto' },
  { value: 'bottom', label: 'Basso' },
  { value: 'left', label: 'Sinistra' },
  { value: 'right', label: 'Destra' },
  { value: 'top left', label: 'Alto Sinistra' },
  { value: 'top right', label: 'Alto Destra' },
  { value: 'bottom left', label: 'Basso Sinistra' },
  { value: 'bottom right', label: 'Basso Destra' },
];
const OPTS_BLEND_MODE = blendModes.map(v => ({ value: v, label: v }));
const OPTS_SFX_EFFECT = [
  { value: 'blockRight', label: 'Block da destra' },
  { value: 'blockLeft', label: 'Block da sinistra' },
  { value: 'blockDown', label: "Block dall'alto" },
  { value: 'blockUp', label: 'Block dal basso' },
];
const OPTS_CURSOR = [
  { value: 'auto', label: 'Auto' },
  { value: 'pointer', label: 'Pointer' },
  { value: 'default', label: 'Default' },
  { value: 'move', label: 'Move' },
  { value: 'crosshair', label: 'Crosshair' },
  { value: 'text', label: 'Text' },
  { value: 'grab', label: 'Grab' },
  { value: 'none', label: 'Nascosto' },
];
const OPTS_EASING = ['ease','ease-in','ease-out','ease-in-out','linear'].map(v => ({ value: v, label: v }));
const OPTS_EASING_LINEAR_FIRST = ['linear','ease','ease-in','ease-out','ease-in-out'].map(v => ({ value: v, label: v }));
const OPTS_CHAR_SPLIT = [
  { value: 'chars', label: 'Caratteri' },
  { value: 'words', label: 'Parole' },
  { value: 'lines', label: 'Righe' },
];
const OPTS_CHAR_DIRECTION = [
  { value: 'forward', label: 'Avanti' },
  { value: 'backward', label: 'Indietro' },
  { value: 'random', label: 'Casuale' },
  { value: 'middletoedge', label: 'Centro → Bordi' },
  { value: 'edgetomiddle', label: 'Bordi → Centro' },
];
const OPTS_HOVER_CURSOR = [
  { value: '', label: 'Nessun cambio' },
  { value: 'pointer', label: 'Pointer' },
  { value: 'default', label: 'Default' },
  { value: 'grab', label: 'Grab' },
];
const OPTS_MASK_PRESET = [
  { value: 'custom', label: 'Personalizzato' },
  { value: 'revealRight', label: 'Reveal da destra' },
  { value: 'revealLeft', label: 'Reveal da sinistra' },
  { value: 'revealDown', label: "Reveal dall'alto" },
  { value: 'revealUp', label: 'Reveal dal basso' },
  { value: 'curtainH', label: 'Sipario orizzontale' },
  { value: 'curtainV', label: 'Sipario verticale' },
];
const OPTS_KENBURNS_TYPE = [
  { value: 'zoomIn', label: 'Zoom In' },
  { value: 'zoomOut', label: 'Zoom Out' },
  { value: 'panLeft', label: 'Pan Sinistra' },
  { value: 'panRight', label: 'Pan Destra' },
  { value: 'panUp', label: 'Pan Alto' },
  { value: 'panDown', label: 'Pan Basso' },
];
const OPTS_MOTION_PRESET = [
  { value: 'circle', label: 'Cerchio' },
  { value: 'figure8', label: 'Otto' },
  { value: 'wave', label: 'Onda' },
  { value: 'zigzag', label: 'Zigzag' },
  { value: 'arc', label: 'Arco' },
  { value: 'custom', label: 'SVG personalizzato' },
];
const OPTS_PARALLAX_TYPE = [
  { value: 'mouse', label: 'Mouse' },
  { value: 'scroll', label: 'Scroll' },
  { value: 'both', label: 'Mouse + Scroll' },
];

// Font families
const fontsSans = ['Inter', 'Roboto', 'Open Sans', 'Lato', 'Montserrat', 'Poppins', 'Nunito', 'Raleway', 'Source Sans 3', 'Work Sans', 'DM Sans', 'Manrope'];
const fontsSerif = ['Playfair Display', 'Merriweather', 'Lora', 'PT Serif', 'Libre Baskerville', 'Cormorant Garamond', 'Crimson Text', 'Noto Serif'];
const fontsMono = ['JetBrains Mono', 'Fira Code', 'Source Code Pro', 'IBM Plex Mono', 'Space Mono'];
const fontsDisplay = ['Bebas Neue', 'Oswald', 'Anton', 'Permanent Marker', 'Abril Fatface', 'Righteous', 'Pacifico', 'Dancing Script', 'Lobster', 'Satisfy'];

function toggleSfx(enabled) {
  if (enabled) {
    up('sfx', { effect: 'blockRight', color: '#ffffff', duration: 800 });
  } else {
    up('sfx', null);
  }
}

function upSfx(key, val) {
  if (!props.layer?.sfx) return;
  up('sfx', { ...props.layer.sfx, [key]: val });
}

// Box shadow helpers
function toggleBoxShadow(enabled) {
  up('boxShadow', enabled ? { x: 0, y: 4, blur: 10, spread: 0, color: 'rgba(0,0,0,0.3)' } : null);
}
function upBoxShadow(key, val) {
  if (!props.layer?.boxShadow) return;
  up('boxShadow', { ...props.layer.boxShadow, [key]: val });
}

// Text shadow helpers
function toggleTextShadow(enabled) {
  up('textShadow', enabled ? { x: 2, y: 2, blur: 4, color: '#000000' } : null);
}
function upTextShadow(key, val) {
  if (!props.layer?.textShadow) return;
  up('textShadow', { ...props.layer.textShadow, [key]: val });
}

// Shape gradient helpers
function toggleShapeGradient(enabled) {
  up('shapeGradient', enabled ? { from: '#3b82f6', to: '#8b5cf6', angle: 180 } : null);
}
function upShapeGrad(key, val) {
  if (!props.layer?.shapeGradient) return;
  up('shapeGradient', { ...props.layer.shapeGradient, [key]: val });
}

// Mask (clip-path) helpers
function toggleMask(enabled) {
  up('mask', enabled ? { preset: 'custom', top: 0, right: 0, bottom: 0, left: 0 } : null);
}
function upMask(key, val) {
  if (!props.layer?.mask) return;
  up('mask', { ...props.layer.mask, [key]: val, preset: 'custom' });
}
function applyMaskPreset(preset) {
  const presets = {
    custom:     { top: 0, right: 0, bottom: 0, left: 0 },
    revealRight:{ top: 0, right: 100, bottom: 0, left: 0 },
    revealLeft: { top: 0, right: 0, bottom: 0, left: 100 },
    revealDown: { top: 100, right: 0, bottom: 0, left: 0 },
    revealUp:   { top: 0, right: 0, bottom: 100, left: 0 },
    curtainH:   { top: 0, right: 50, bottom: 0, left: 50 },
    curtainV:   { top: 50, right: 0, bottom: 50, left: 0 },
  };
  up('mask', { preset, ...(presets[preset] || presets.custom) });
}

// Ken Burns per layer helpers
function toggleKenBurns(enabled) {
  up('kenBurns', enabled ? { type: 'zoomIn', intensity: 20, duration: 5000 } : null);
}
function upKenBurns(key, val) {
  if (!props.layer?.kenBurns) return;
  up('kenBurns', { ...props.layer.kenBurns, [key]: val });
}

// Motion Path helpers
const MOTION_PRESETS = {
  circle:  'M50,0 A50,50 0 1,1 50,100 A50,50 0 1,1 50,0 Z',
  figure8: 'M50,50 C75,0 100,0 100,50 C100,100 75,100 50,50 C25,0 0,0 0,50 C0,100 25,100 50,50 Z',
  wave:    'M0,50 Q25,0 50,50 Q75,100 100,50',
  zigzag:  'M0,50 L25,10 L50,90 L75,10 L100,50',
  arc:     'M0,100 Q50,-20 100,100',
};
function toggleMotionPath(enabled) {
  up('motionPath', enabled ? { preset: 'circle', path: MOTION_PRESETS.circle, duration: 3000, autoRotate: false, loop: true, easing: 'linear' } : null);
}
function applyMotionPreset(preset) {
  if (!props.layer?.motionPath) return;
  const path = MOTION_PRESETS[preset] || props.layer.motionPath.path || '';
  up('motionPath', { ...props.layer.motionPath, preset, path });
}
function upMotionPath(key, val) {
  if (!props.layer?.motionPath) return;
  up('motionPath', { ...props.layer.motionPath, [key]: val });
}

// Parallax layer helpers
function toggleParallax(enabled) {
  up('parallax', enabled ? { type: 'mouse', depth: 5, tilt3d: false, scrollSpeed: 0.5 } : null);
}
function upParallax(key, val) {
  if (!props.layer?.parallax) return;
  up('parallax', { ...props.layer.parallax, [key]: val });
}
</script>

<style scoped>
.mps-label {
  display: block;
  font-size: 10px;
  color: #9ca3af;
  margin-bottom: 2px;
}
.mps-val {
  font-size: 11px;
  color: #9ca3af;
  width: 40px;
  text-align: right;
  flex-shrink: 0;
}
.mps-input {
  width: 100%;
  background: #ffffff;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  padding: 4px 8px;
  font-size: 0.75rem;
  color: #111827;
}
.mps-select {
  width: 100%;
  background: #ffffff;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  padding: 4px 8px;
  font-size: 0.75rem;
  color: #111827;
}
.mps-icon-preview {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}
.mps-icon-preview :deep(svg) {
  width: 18px;
  height: 18px;
  color: #d1d5db;
  stroke: currentColor;
}
.mps-icon-preview :deep(svg:not([fill="none"])) {
  fill: currentColor;
}
.mps-icon-preview :deep(svg [fill="none"]) {
  fill: none;
}
</style>
