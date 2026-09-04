<template>
  <div>
    <!-- Dynamic-capable field -->
    <DynamicFieldToggle
      v-if="allowDynamic && tileId"
      :field="field"
      :tileId="tileId"
      :dynamic="dynamic"
      @update:dynamic="onDynamicUpdate"
    >
      <component :is="fieldComponent" v-bind="fieldProps" @update:modelValue="$emit('update:modelValue', $event)" @update:attachmentId="$emit('update:attachmentId', $event)" />
    </DynamicFieldToggle>

    <!-- Non-dynamic field (original behavior) -->
    <template v-else>
      <!-- Separator — now handled by CollapseSection in BuilderInspector -->
      <template v-if="field.type === 'separator'" />

      <!-- Description: testo informativo, no input -->
      <p v-else-if="field.type === 'description'" class="mb-text-[10px] mb-text-gray-400 mb-italic mb-leading-snug mb-py-0.5">
        {{ t(field.description || field.label || '') }}
      </p>

      <template v-else>
      <!-- Layout INLINE compatto: per i campi numerici a dominio noto (range/number)
           la label sta a sinistra e il controllo compatto a destra, così un numero
           1–100 non occupa più una riga intera. Lo slider compare nel popover al
           focus (NumberScrubber). Escluso per hoverable o field.layout==='block'. -->
      <div v-if="renderInline" :class="inlineFill ? 'olo-field-inline-fill' : 'olo-field-inline'">
        <label class="olo-fi-label" :title="t(field.label)">
          <span class="olo-fi-text">{{ t(field.label) }}</span>
          <span
            v-if="field.responsive && respBp !== 'desktop'"
            class="mb-text-[9px] mb-bg-primary-700 mb-text-primary-200 mb-px-1.5 mb-py-0.5 mb-rounded mb-font-medium mb-ml-1"
            :title="t('Stai modificando questo breakpoint — cambia dispositivo dalla barra in alto')"
          >{{ t(respBreakpoints.find(b => b.key === respBp)?.label) }}</span>
        </label>
        <div class="olo-fi-control">
          <!-- compatti (controllo a destra) -->
          <FieldRange
            v-if="field.type === 'range'"
            compact
            :modelValue="effectiveValue"
            :min="field.min || 0"
            :max="field.max || 100"
            :step="field.step || 1"
            :defaultValue="fieldDefaultValue"
            :placeholder="field.responsive && respBp !== 'desktop' ? t('Eredita') : (field.placeholder || '')"
            @update:modelValue="onFieldUpdate($event)"
          />
          <FieldToggle
            v-else-if="field.type === 'toggle'"
            :modelValue="effectiveValue"
            @update:modelValue="onFieldUpdate($event)"
          />
          <NumberScrubber
            v-else-if="field.type === 'number'"
            :modelValue="effectiveValue"
            :min="field.min ?? null"
            :max="field.max ?? null"
            :step="field.step ?? 1"
            :defaultValue="fieldDefaultValue"
            :placeholder="field.responsive && respBp !== 'desktop' ? t('Eredita') : (field.placeholder || '')"
            emitAs="string"
            @update:modelValue="onFieldUpdate($event)"
          />
          <!-- fill (il controllo riempie la parte destra) -->
          <FieldSelect
            v-else-if="field.type === 'select'"
            :modelValue="effectiveValue"
            :options="resolvedOptions"
            :ui="field.ui || 'auto'"
            @update:modelValue="onFieldUpdate($event)"
          />
          <FieldColor
            v-else-if="field.type === 'color'"
            :modelValue="effectiveValue"
            @update:modelValue="onFieldUpdate($event)"
          />
          <FieldLink
            v-else-if="field.type === 'link'"
            :modelValue="effectiveValue"
            :placeholder="field.placeholder || ''"
            :types="field.linkTypes || ''"
            @update:modelValue="onFieldUpdate($event)"
          />
          <FieldFontFamily
            v-else-if="field.type === 'font-family'"
            :modelValue="effectiveValue"
            @update:modelValue="onFieldUpdate($event)"
          />
          <FieldUnit
            v-else-if="field.type === 'unit'"
            :modelValue="effectiveValue"
            :units="field.units"
            :min="field.min"
            :max="field.max"
            :step="field.step"
            :placeholder="field.placeholder || ''"
            @update:modelValue="onFieldUpdate($event)"
          />
          <FieldDatetime
            v-else-if="field.type === 'datetime'"
            :modelValue="effectiveValue"
            @update:modelValue="onFieldUpdate($event)"
          />
          <FieldDate
            v-else-if="field.type === 'date'"
            :modelValue="effectiveValue"
            @update:modelValue="onFieldUpdate($event)"
          />
          <FieldTime
            v-else-if="field.type === 'time'"
            :modelValue="effectiveValue"
            @update:modelValue="onFieldUpdate($event)"
          />
          <FieldText
            v-else
            :modelValue="effectiveValue"
            @update:modelValue="onFieldUpdate($event)"
            @confirm="$emit('confirm', $event)"
          />
        </div>
      </div>

      <template v-else>
      <!-- Label + occhio hover + badge breakpoint. Lo switch device NON è qui:
           il dispositivo si cambia dalla barra in alto (builderStore.viewMode) e
           questo campo segue automaticamente quel breakpoint. Niente duplicati. -->
      <template v-if="field.responsive || field.hoverable">
        <div class="mb-flex mb-items-center mb-justify-between mb-mb-1">
          <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400">
            {{ t(field.label) }}
          </label>
          <div class="mb-flex mb-items-center mb-gap-1">
            <!-- Toggle Normale/Hover (sostituisce la vecchia icona "occhio"). Pilota la
                 stessa `hoverOpen`: "Hover" apre il controllo della variante hover qui sotto.
                 Classe dedicata .olo-hover-seg (NON Tailwind arbitrario [#hex]: il JIT non
                 lo genera in modo affidabile) → stessa grafica di .olo-es-seg/.olo-bs-seg. -->
            <div
              v-if="field.hoverable"
              class="olo-hover-seg"
              role="tablist"
              :aria-label="t('Stato Normale o Hover')"
            >
              <button
                type="button" role="tab" :aria-selected="!hoverOpen ? 'true' : 'false'"
                :class="{ on: !hoverOpen }"
                @click="hoverOpen = false"
              >{{ t('Normale') }}</button>
              <button
                type="button" role="tab" :aria-selected="hoverOpen ? 'true' : 'false'"
                :class="{ on: hoverOpen }"
                @click="hoverOpen = true"
              >
                {{ t('Hover') }}
                <span v-if="hasHoverValue" class="olo-hover-seg-dot"></span>
              </button>
            </div>
            <span
              v-if="field.responsive && respBp !== 'desktop'"
              class="mb-text-[9px] mb-bg-primary-700 mb-text-primary-200 mb-px-1.5 mb-py-0.5 mb-rounded mb-font-medium"
              :title="t('Stai modificando questo breakpoint — cambia dispositivo dalla barra in alto')"
            >
              {{ t(respBreakpoints.find(b => b.key === respBp)?.label) }}
            </span>
          </div>
        </div>
      </template>
      <label v-else-if="field.type !== 'typography' && field.type !== 'content-popup' && !field.reveal" class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">
        {{ t(field.label) }}
      </label>

      <FieldToggle
        v-if="field.type === 'toggle'"
        :modelValue="effectiveValue"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldColor
        v-else-if="field.type === 'color'"
        :modelValue="effectiveValue"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldSelect
        v-else-if="field.type === 'select'"
        :modelValue="effectiveValue"
        :options="resolvedOptions"
        :ui="field.ui || 'auto'"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldRange
        v-else-if="field.type === 'range'"
        :modelValue="effectiveValue"
        :min="field.min || 0"
        :max="field.max || 100"
        :step="field.step || 1"
        :defaultValue="fieldDefaultValue"
        :placeholder="field.responsive && respBp !== 'desktop' ? t('Eredita') : (field.placeholder || '')"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldSpacing
        v-else-if="field.type === 'spacing'"
        :modelValue="effectiveValue"
        :min="field.min ?? 0"
        :max="field.max ?? 200"
        :defaultValue="fieldDefaultValue"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldBox
        v-else-if="field.type === 'border-radius'"
        :modelValue="effectiveValue"
        mode="corners"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldBorder
        v-else-if="field.type === 'border'"
        :modelValue="effectiveValue"
        @update:modelValue="onFieldUpdate($event)"
      />

      <!-- Punto focale grafico (object-position): riceve immagine + frame del fratello
           dallo stesso settings per disegnare il ritaglio reale. Senza contesto degrada
           elegante. Emette la STESSA stringa CSS (keyword o '%') → nessuna migrazione dati.
           Con field.reveal il pad (alto ~270px) resta nascosto dietro un pulsante e si apre
           on-demand: utile dove la tile è già densa o ha immagini multiple (punto focale globale). -->
      <template v-else-if="field.type === 'object-position'">
        <button
          v-if="field.reveal"
          type="button"
          class="olo-reveal-btn"
          :class="{ 'is-open': revealOpen }"
          :aria-expanded="revealOpen ? 'true' : 'false'"
          @click="revealOpen = !revealOpen"
        >
          <svg class="olo-reveal-chev" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
          <span>{{ revealOpen ? t('Nascondi punto focale') : (t(field.label) + ' — ' + t('punto focale')) }}</span>
          <span v-if="!revealOpen && hasNonDefaultValue" class="olo-reveal-dot" :title="t('Posizione personalizzata')"></span>
        </button>
        <FieldObjectPosition
          v-if="!field.reveal || revealOpen"
          :class="{ 'olo-reveal-body': field.reveal }"
          :modelValue="effectiveValue"
          :image-src="objectPositionContext.imageSrc"
          :frame-ratio="objectPositionContext.frameRatio"
          :object-fit="objectPositionContext.objectFit"
          @update:modelValue="onFieldUpdate($event)"
        />
      </template>

      <FieldEditor
        v-else-if="field.type === 'editor'"
        :modelValue="effectiveValue"
        :mode="field.mode || 'inline'"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldImage
        v-else-if="field.type === 'image'"
        :modelValue="effectiveValue"
        @update:modelValue="onFieldUpdate($event)"
        @update:attachmentId="$emit('update:attachmentId', $event)"
      />

      <FieldLink
        v-else-if="field.type === 'link'"
        :modelValue="effectiveValue"
        :placeholder="field.placeholder || ''"
        :types="field.linkTypes || ''"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldMedia
        v-else-if="field.type === 'media'"
        :modelValue="effectiveValue"
        :accept="mediaAccept"
        @update:modelValue="onFieldUpdate($event)"
        @update:attachmentId="$emit('update:attachmentId', $event)"
      />

      <FieldLottiePicker
        v-else-if="field.type === 'lottie_picker'"
        :modelValue="effectiveValue"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldGallery
        v-else-if="field.type === 'gallery'"
        :modelValue="effectiveValue"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldIcon
        v-else-if="field.type === 'icon'"
        :modelValue="effectiveValue"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldTextarea
        v-else-if="field.type === 'textarea'"
        :modelValue="effectiveValue"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldMegaPanelMap
        v-else-if="field.type === 'megapanel-map'"
        :modelValue="effectiveValue"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldIconSelect
        v-else-if="field.type === 'icon-select'"
        :modelValue="effectiveValue"
        :options="field.options || []"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldMultiPills
        v-else-if="field.type === 'multi_pills'"
        :modelValue="effectiveValue"
        :options="field.options || []"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldBoxShadow
        v-else-if="field.type === 'box-shadow'"
        :modelValue="effectiveValue"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldGradient
        v-else-if="field.type === 'gradient'"
        :modelValue="effectiveValue"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldTransform
        v-else-if="field.type === 'transform'"
        :modelValue="effectiveValue"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldFontFamily
        v-else-if="field.type === 'font-family'"
        :modelValue="effectiveValue"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldDatetime
        v-else-if="field.type === 'datetime'"
        :modelValue="effectiveValue"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldDate
        v-else-if="field.type === 'date'"
        :modelValue="effectiveValue"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldTime
        v-else-if="field.type === 'time'"
        :modelValue="effectiveValue"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldTextarea
        v-else-if="field.type === 'code'"
        :modelValue="effectiveValue"
        class="olo-field-code"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldTextShadow
        v-else-if="field.type === 'text-shadow'"
        :modelValue="effectiveValue"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldBackdropFilter
        v-else-if="field.type === 'backdrop-filter'"
        :modelValue="effectiveValue"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldBorderLegacy
        v-else-if="field.type === 'border-legacy'"
        :modelValue="effectiveValue"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldContentPopup
        v-else-if="field.type === 'content-popup'"
        :field="field"
        :settings="tileSettings || {}"
        :tileId="tileId"
        @update:settingKey="$emit('update:settingKey', $event)"
      />

      <BackgroundControls
        v-else-if="field.type === 'background'"
        :modelValue="effectiveValue || { type: 'none' }"
        :showParallax="field.showParallax !== false"
        @update:modelValue="onFieldUpdate($event)"
      />

      <!-- Typography popover (multi-key consolidated control) -->
      <FieldTypography
        v-else-if="field.type === 'typography'"
        :keys="field.keys || {}"
        :values="tileSettings || {}"
        :label="field.label || 'Tipografia'"
        :sizeMin="field.sizeMin"
        :sizeMax="field.sizeMax"
        :sizeStep="field.sizeStep"
        @update="$emit('update:settingKey', $event)"
      />

      <!-- Custom field types rendered by parent (content-items) -->
      <slot v-else-if="field.type === 'content-items'" name="content-items" />

      <!-- Text with AI alt generate button -->
      <div v-else-if="field.aiGenerate === 'alt'" class="mb-flex mb-gap-1 mb-items-end">
        <FieldText
          class="mb-flex-1"
          :modelValue="effectiveValue"
          @update:modelValue="onFieldUpdate($event)"
          @confirm="$emit('confirm', $event)"
        />
        <button
          class="mb-shrink-0 mb-px-2 mb-py-1.5 mb-bg-purple-600 hover:mb-bg-purple-500 mb-text-white mb-rounded mb-text-xs mb-transition-colors"
          :class="{ 'mb-opacity-50 mb-cursor-wait': aiAltLoading }"
          :disabled="aiAltLoading"
          :title="t('Genera alt text con AI')"
          @click="generateAiAlt"
        >
          <svg v-if="!aiAltLoading" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
          <span v-else class="mb-inline-block mb-animate-spin">⟳</span>
        </button>
      </div>

      <!-- Geocode field: text + search button (Nominatim) -->
      <div v-else-if="field.type === 'geocode'" class="mb-flex mb-flex-col mb-gap-1">
        <div class="mb-flex mb-gap-1 mb-items-end">
          <FieldText
            class="mb-flex-1"
            :modelValue="effectiveValue"
            @update:modelValue="onFieldUpdate($event)"
            @confirm="geocodeAddress"
            :placeholder="t('Via Roma 1, Milano...')"
          />
          <button
            class="mb-shrink-0 mb-px-2 mb-py-1.5 mb-bg-blue-600 hover:mb-bg-blue-500 mb-text-white mb-rounded mb-text-xs mb-transition-colors"
            :class="{ 'mb-opacity-50 mb-cursor-wait': geocodeLoading }"
            :disabled="geocodeLoading"
            :title="t('Cerca indirizzo')"
            @click="geocodeAddress"
          >
            <svg v-if="!geocodeLoading" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <span v-else class="mb-inline-block mb-animate-spin">⟳</span>
          </button>
        </div>
        <div v-if="geocodeError" class="mb-text-[10px] mb-text-red-400">{{ geocodeError }}</div>
        <div v-if="geocodeResult" class="mb-text-[10px] mb-text-green-400 mb-truncate" :title="geocodeResult">{{ geocodeResult }}</div>
      </div>

      <!-- number (caso non-inline: hoverable o layout='block'): NumberScrubber.
           Emette $event RAW come STRINGA (emitAs='string', '' se vuoto): stesso
           formato di prima — i renderer che distinguono '' (es. "Auto") e quelli
           che fanno parseInt restano identici. Slider inline solo se min/max noti. -->
      <NumberScrubber
        v-else-if="field.type === 'number'"
        :modelValue="effectiveValue"
        :min="field.min ?? null"
        :max="field.max ?? null"
        :step="field.step ?? 1"
        :defaultValue="fieldDefaultValue"
        :placeholder="field.responsive && respBp !== 'desktop' ? t('Eredita') : (field.placeholder || '')"
        emitAs="string"
        :sliderOnFocus="false"
        @update:modelValue="onFieldUpdate($event)"
      />

      <!-- unit: dimensione CSS con unità ('200px', '0.2em', '50%'). Salva la
           STESSA stringa CSS del vecchio campo testo ('' se vuoto = auto);
           valori non parsabili (calc, keyword) restano editabili raw. -->
      <FieldUnit
        v-else-if="field.type === 'unit'"
        :modelValue="effectiveValue"
        :units="field.units"
        :min="field.min"
        :max="field.max"
        :step="field.step"
        :placeholder="field.placeholder || ''"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldText
        v-else
        :modelValue="effectiveValue"
        @update:modelValue="onFieldUpdate($event)"
        @confirm="$emit('confirm', $event)"
      />

      <!-- Stato Hover: il CONTROLLO sopra mostra e scrive già il valore hover (pilotato dal
           toggle Normale/Hover). Qui resta solo la durata della transizione + reset. NIENTE
           striscia arancione né controllo duplicato (era il "vecchio sistema"). -->
      <div
        v-if="field.hoverable && hoverOpen"
        class="mb-mt-2 mb-flex mb-items-center mb-gap-2"
      >
        <span class="mb-text-[10px] mb-font-semibold mb-text-gray-400 mb-uppercase mb-tracking-wide">{{ t('Durata') }}</span>
        <div class="mb-inline-flex mb-items-center mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-overflow-hidden">
          <input
            type="number"
            min="0"
            max="3000"
            step="50"
            :value="hoverDurationValue"
            @input="onHoverDurationUpdate(Number($event.target.value))"
            class="mb-w-14 mb-bg-transparent mb-px-2 mb-py-1 mb-text-xs mb-text-gray-900 mb-text-center mb-outline-none"
          />
          <span class="mb-px-2 mb-py-1 mb-text-[10px] mb-text-gray-400 mb-bg-gray-50 mb-border-l mb-border-gray-200">ms</span>
        </div>
        <button
          v-if="hasHoverValue"
          @click="resetHoverValue"
          class="mb-ml-auto mb-text-[11px] mb-text-gray-400 hover:mb-text-red-500 mb-px-1.5 mb-py-1 mb-transition-colors"
          :title="t('Reset valore hover')"
        >{{ t('Azzera') }}</button>
      </div>
      </template>
      </template>
    </template>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { useBuilderStore } from '@/stores/builder';
import FieldText from './fields/FieldText.vue';
import FieldTextarea from './fields/FieldTextarea.vue';
import FieldSelect from './fields/FieldSelect.vue';
import FieldToggle from './fields/FieldToggle.vue';
import FieldColor from './fields/FieldColor.vue';
import FieldRange from './fields/FieldRange.vue';
import NumberScrubber from './fields/NumberScrubber.vue';
import FieldSpacing from './fields/FieldSpacing.vue';
import FieldBox from './fields/FieldBox.vue';
import FieldBorder from './fields/FieldBorder.vue';
import FieldObjectPosition from './fields/FieldObjectPosition.vue';
import FieldImage from './fields/FieldImage.vue';
import FieldMedia from './fields/FieldMedia.vue';
import FieldLink from './fields/FieldLink.vue';
import FieldLottiePicker from './fields/FieldLottiePicker.vue';
import FieldGallery from './fields/FieldGallery.vue';
import FieldIcon from './fields/FieldIcon.vue';
import FieldMegaPanelMap from './fields/FieldMegaPanelMap.vue';
import FieldMultiPills from './fields/FieldMultiPills.vue';
import FieldBoxShadow from './fields/FieldBoxShadow.vue';
import FieldGradient from './fields/FieldGradient.vue';
import FieldTransform from './fields/FieldTransform.vue';
import FieldFontFamily from './fields/FieldFontFamily.vue';
import FieldUnit from './fields/FieldUnit.vue';
import FieldTypography from './fields/FieldTypography.vue';
import FieldDatetime from './fields/FieldDatetime.vue';
import FieldDate from './fields/FieldDate.vue';
import FieldTime from './fields/FieldTime.vue';
import FieldIconSelect from './fields/FieldIconSelect.vue';
import DynamicFieldToggle from './DynamicFieldToggle.vue';
import { useTilesStore } from '@/stores/tiles';
import { useStylesStore } from '@/stores/styles';
import { t } from '@/i18n';

import FieldEditor from './fields/FieldEditor.vue';
import FieldTextShadow from './fields/FieldTextShadow.vue';
import FieldBackdropFilter from './fields/FieldBackdropFilter.vue';
import FieldBorderLegacy from './fields/FieldBorderLegacy.vue';
import FieldContentPopup from './fields/FieldContentPopup.vue';
import BackgroundControls from './BackgroundControls.vue';

const DYNAMIC_TYPES = ['text', 'textarea', 'editor', 'image', 'media'];

const props = defineProps({
  field: { type: Object, required: true },
  modelValue: { default: '' },
  tileId: { type: String, default: '' },
  dynamic: { type: Object, default: () => ({}) },
  tileSettings: { type: Object, default: null },
  // hover storage layout:
  //   false (default) → flat in tileSettings: settings[`${key}_hover`]
  //   true            → nested under .hover: tileSettings.hover[key]   (per scope=style legacy)
  hoverNested: { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue', 'update:dynamic', 'update:attachmentId', 'confirm', 'update:responsiveValue', 'update:hoverValue', 'update:settingKey']);

// ── Responsive per-field breakpoint ──
const respBreakpoints = [
  { key: 'desktop', label: 'Desktop', short: 'DT' },
  { key: 'tablet_landscape', label: 'Tablet L', short: 'TL' },
  { key: 'tablet', label: 'Tablet', short: 'TP' },
  { key: 'mobile_landscape', label: 'Mobile L', short: 'ML' },
  { key: 'mobile', label: 'Mobile', short: 'MB' },
];
const builderStore = useBuilderStore();
const respOpen = ref(false);
const respBp = ref('desktop');

// Disclosure per field.reveal (es. object-position pesante): parte chiuso, ma si apre
// già se il campo ha un valore personalizzato (≠ default) per non nascondere il lavoro fatto.
const revealOpen = ref(
  !!props.field.reveal &&
  props.modelValue != null && props.modelValue !== '' && props.modelValue !== 'center center'
);

// Sincronizza il breakpoint del campo con il viewMode della toolbar
watch(() => builderStore.viewMode, (mode) => {
  if (mode === 'desktop' || mode === 'widescreen') {
    respBp.value = 'desktop';
  } else {
    respBp.value = mode;
    respOpen.value = true;
  }
}, { immediate: true });

const respKey = computed(() => {
  if (!props.field.responsive || respBp.value === 'desktop') return props.field.key;
  return props.field.key + '_' + respBp.value;
});

const respValue = computed(() => {
  if (!props.field.responsive || respBp.value === 'desktop') return props.modelValue;
  if (props.tileSettings && respKey.value in props.tileSettings) {
    return props.tileSettings[respKey.value];
  }
  return '';
});

// Layout INLINE compatto (label a sx, controllo a dx) per i numerici a dominio noto.
// Opt-out con field.layout==='block'; escluso per hoverable (la UI hover ha bisogno
// della riga intera). Opt-in generico futuro con field.layout==='inline'.
// Layout "controllo a destra del titolo" (densità).
//  • compatti  → controllo piccolo allineato a destra (toggle/numero/range)
//  • fill      → il controllo riempie la parte destra (select/colore/testo/link/…)
// Restano IMPILATI a tutta larghezza: textarea, editor, media, immagine, galleria e
// tutti i compositi (background, gradient, transform, shadow, border, spacing, …),
// che hanno bisogno di tutta la riga.
const INLINE_COMPACT = ['range', 'number', 'toggle', 'unit'];
const INLINE_FILL = ['select', 'color', 'text', 'link', 'font-family', 'date', 'time', 'datetime'];
const inlineFill = computed(() => INLINE_FILL.includes(props.field.type));
const renderInline = computed(() => {
  const f = props.field;
  if (f.layout === 'block') return false;
  if (f.hoverable) return false;
  if (f.aiGenerate === 'alt') return false; // testo + bottone AI accanto → resta block
  if (f.type === 'geocode') return false;    // testo + bottone ricerca → resta block
  return INLINE_COMPACT.includes(f.type) || INLINE_FILL.includes(f.type);
});

function onFieldUpdate(value) {
  // Stato Hover (toggle Normale/Hover su campo hoverable): scrivi sul valore hover,
  // così il CONTROLLO principale modifica direttamente l'hover (niente più sub-control).
  if (props.field.hoverable && hoverOpen.value) {
    emit('update:hoverValue', { key: hoverKey.value, value });
    return;
  }
  if (props.field.responsive && respBp.value !== 'desktop') {
    emit('update:responsiveValue', { key: respKey.value, value });
  } else {
    emit('update:modelValue', value);
  }
}

// ── Hover state (inline per-field) ──
// Local override (user clicked the eye icon on this specific field).
const _localHoverOpen = ref(false);
// Global toggle (Inspector V2 amber "Hover" pill): when ON, every hoverable
// field opens its hover variant by default. The local toggle still wins when
// the user clicks the eye on a specific field — _localHoverOpen tracks that.
const hoverOpen = computed({
  get() {
    if (!props.field.hoverable) return false;
    return _localHoverOpen.value || builderStore.editingHover;
  },
  set(v) { _localHoverOpen.value = v; },
});

const hoverKey = computed(() => {
  if (!props.field.hoverable) return '';
  // For nested layout (style.hover.X) the "hoverKey" è il NAME usato dentro tileSettings.hover[],
  // di default uguale a field.key (NO suffisso _hover, perché siamo già nel namespace .hover).
  if (props.hoverNested) return props.field.hoverKey || props.field.key;
  return props.field.hoverKey || `${props.field.key}_hover`;
});

const hoverDurationKey = computed(() => {
  if (!props.field.hoverable) return '';
  if (props.hoverNested) return props.field.hoverDurationKey || `${props.field.key}_hover_duration`;
  return props.field.hoverDurationKey || `${props.field.key}_hover_duration`;
});

const hoverValue = computed(() => {
  if (!hoverKey.value || !props.tileSettings) return '';
  if (props.hoverNested) {
    const nested = props.tileSettings.hover || {};
    return hoverKey.value in nested ? nested[hoverKey.value] : '';
  }
  return hoverKey.value in props.tileSettings ? props.tileSettings[hoverKey.value] : '';
});

const hasHoverValue = computed(() => {
  const v = hoverValue.value;
  if (v == null || v === '') return false;
  // For object-typed fields (border, border-radius, spacing) treat as set when any non-zero/non-empty member exists
  if (typeof v === 'object') {
    try {
      return Object.values(v).some(x => x !== 0 && x !== '' && x !== false && x != null);
    } catch (_) { return false; }
  }
  return true;
});

const hoverDurationValue = computed(() => {
  if (!hoverDurationKey.value || !props.tileSettings) return props.field.hoverDefaultDuration ?? 300;
  const v = props.tileSettings[hoverDurationKey.value];
  return (v == null || v === '') ? (props.field.hoverDefaultDuration ?? 300) : v;
});

function onHoverUpdate(value) {
  if (!hoverKey.value) return;
  emit('update:hoverValue', { key: hoverKey.value, value });
}

function onHoverDurationUpdate(value) {
  if (!hoverDurationKey.value) return;
  emit('update:hoverValue', { key: hoverDurationKey.value, value });
}

function resetHoverValue() {
  if (!hoverKey.value) return;
  // Reset to empty/null per field type
  const f = props.field;
  let empty = '';
  if (f.type === 'spacing' || f.type === 'border-radius') empty = { top: 0, right: 0, bottom: 0, left: 0, linked: true };
  else if (f.type === 'border') empty = { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: '', color: '' };
  else if (f.type === 'toggle') empty = false;
  emit('update:hoverValue', { key: hoverKey.value, value: empty });
}

const resolvedOptions = computed(() => {
  if (props.field.optionsSource) {
    const md = window.oloData || {};
    if (props.field.optionsSource === 'wpMenus') {
      return (md.wpMenus || []).map(m => ({ value: m.id, label: m.name }));
    }
    if (props.field.optionsSource === 'postTypes') {
      return (md.postTypes || []);
    }
    if (props.field.optionsSource === 'taxonomies') {
      return (md.taxonomies || []);
    }
    if (props.field.optionsSource === 'templates') {
      return (md.templateList || []);
    }
    if (props.field.optionsSource === 'widgetTemplates') {
      return (md.widgetTemplates || [{ value: 0, label: t('— Nessun widget —') }]);
    }
    if (props.field.optionsSource === 'metaPrefixes') {
      return md.metaPrefixes || [];
    }
    if (props.field.optionsSource === 'serviceList') {
      const list = (md.serviceList || []);
      return [{ value: '', label: t('— Tutti i servizi —') }, ...list];
    }
    if (props.field.optionsSource === 'wpPages') {
      return md.wpPages || [];
    }
    if (props.field.optionsSource === 'searchTiles') {
      const tilesStore = useTilesStore();
      const results = [{ value: '', label: t('— Nessuna ricerca —') }];
      const walk = (nodes) => {
        for (const node of nodes) {
          if (node.type === 'livesearch' || node.type === 'search') {
            const lbl = node.settings?.placeholder || (node.type === 'livesearch' ? t('Ricerca Live') : t('Ricerca'));
            results.push({ value: node.id, label: (node.type === 'livesearch' ? t('Ricerca Live') : t('Ricerca')) + ' — ' + lbl });
          }
          if (Array.isArray(node.children)) walk(node.children);
        }
      };
      walk(tilesStore.canvasTiles || []);
      return results;
    }
    if (props.field.optionsSource === 'langTiles') {
      // Tile langswitcher del template, referenziabili dal megamenu (lang_tile_id).
      const tilesStore = useTilesStore();
      const results = [{ value: '', label: t('— Nessun selettore —') }];
      const walk = (nodes) => {
        for (const node of nodes) {
          if (node.type === 'langswitcher') {
            results.push({ value: node.id, label: t('Selettore lingua') + ' — ' + String(node.id).slice(0, 8) });
          }
          if (Array.isArray(node.children)) walk(node.children);
        }
      };
      walk(tilesStore.canvasTiles || []);
      return results;
    }
    if (props.field.optionsSource === 'metaKeys') {
      // Lista meta_key disponibili per il post_type corrente, dipendente da optionsDependOn (default: 'post_type')
      const depKey = props.field.optionsDependOn || 'post_type';
      const tilesStore = useTilesStore();
      const tile = props.tileId ? tilesStore.getTileById(props.tileId) : null;
      const postType = tile?.settings?.[depKey] || 'post';
      const map = (md.metaKeys || {})[postType] || [];
      const opts = [{ value: '', label: t('— Nessun filtro —') }];
      map.forEach(m => opts.push({ value: m.key, label: m.label }));
      return opts;
    }
    if (props.field.optionsSource === 'metaValues') {
      // Valori distinti per il meta_key selezionato. optionsDependOn può essere
      // stringa (campo meta_key) o array [post_type_field, meta_key_field].
      const dep = props.field.optionsDependOn || ['post_type', 'meta_filter_key'];
      const [ptKey, mkKey] = Array.isArray(dep) ? dep : ['post_type', dep];
      const tilesStore = useTilesStore();
      const tile = props.tileId ? tilesStore.getTileById(props.tileId) : null;
      const postType = tile?.settings?.[ptKey] || 'post';
      const metaKey = tile?.settings?.[mkKey] || '';
      if (!metaKey) return [{ value: '', label: t('— Seleziona prima la chiave —') }];
      const map = (md.metaKeys || {})[postType] || [];
      const entry = map.find(m => m.key === metaKey);
      if (!entry || !entry.values?.length) return [{ value: '', label: t('— Nessun valore disponibile —') }];
      const opts = [{ value: '', label: t('— Tutti i valori —') }];
      entry.values.forEach(v => opts.push({ value: v, label: v }));
      return opts;
    }
    if (props.field.optionsSource === 'globalTypography') {
      const stylesStore = useStylesStore();
      const sets = stylesStore.globalTypography || [];
      return [
        { value: '', label: t('— Nessuno —') },
        ...sets.map(gt => ({ value: gt.id, label: gt.label || gt.id }))
      ];
    }
    if (props.field.optionsSource === 'wpMenuItems') {
      const depKey = props.field.optionsDependOn || 'menu_id';
      const tilesStore = useTilesStore();
      const tile = props.tileId ? tilesStore.getTileById(props.tileId) : null;
      const menuId = parseInt(tile?.settings?.[depKey] || 0);
      const menu = (md.wpMenus || []).find(m => m.id === menuId);
      if (!menu || !menu.items) return [{ value: '0', label: t('— Seleziona —') }];
      const opts = [{ value: '0', label: t('— Seleziona —') }];
      menu.items.forEach(item => {
        const indent = item.parent ? '— ' : '';
        opts.push({ value: String(item.id), label: indent + item.title });
      });
      return opts;
    }
  }
  return props.field.options || [];
});

const allowDynamic = computed(() => {
  if (props.field.allowDynamic === false) return false;
  if (props.field.allowDynamic === true) return true;
  return DYNAMIC_TYPES.includes(props.field.type);
});

// Determine component and props for the slot content
const fieldComponent = computed(() => {
  switch (props.field.type) {
    case 'toggle': return FieldToggle;
    case 'color': return FieldColor;
    case 'select': return FieldSelect;
    case 'range': return FieldRange;
    case 'border-radius': return FieldBox;
    case 'border': return FieldBorder;
    case 'object-position': return FieldObjectPosition;
    case 'editor': return FieldEditor;
    case 'image': return FieldImage;
    case 'link': return FieldLink;
    case 'media': return FieldMedia;
    case 'gallery': return FieldGallery;
    case 'icon': return FieldIcon;
    case 'textarea': return FieldTextarea;
    case 'megapanel-map': return FieldMegaPanelMap;
    case 'icon-select': return FieldIconSelect;
    case 'multi_pills': return FieldMultiPills;
    case 'box-shadow': return FieldBoxShadow;
    case 'gradient': return FieldGradient;
    case 'transform': return FieldTransform;
    case 'datetime': return FieldDatetime;
    case 'date': return FieldDate;
    case 'time': return FieldTime;
    case 'code': return FieldTextarea;
    case 'text-shadow': return FieldTextShadow;
    case 'backdrop-filter': return FieldBackdropFilter;
    case 'border-legacy': return FieldBorderLegacy;
    case 'background': return BackgroundControls;
    default: return FieldText;
  }
});

const effectiveValue = computed(() => {
  // Stato Hover attivo: il controllo principale mostra il valore hover (toggle Normale/Hover).
  if (props.field.hoverable && hoverOpen.value) return hoverValue.value;
  return props.field.responsive ? respValue.value : props.modelValue;
});
// `hoverOpen`/`hoverKey`/`hoverValue` sono definiti più sotto (const): vengono usati qui solo
// a runtime (computed lazy / handler), mai durante l'inizializzazione → nessun TDZ.

// Auto-detect media type for FieldMedia from explicit field.accept or by parsing field.key.
// e.g. 'bg_video' / 'hover_video' / 'video_url' → 'video';  'pdf_url' → 'application/pdf'
const mediaAccept = computed(() => {
  if (props.field.accept) return props.field.accept;
  const k = String(props.field.key || '').toLowerCase();
  if (/(^|_)(video)(_|$)|video_url|hover_video|bg_video|front_video|back_video/.test(k)) return 'video';
  if (/(^|_)(audio|sound|music)(_|$)|audio_url/.test(k)) return 'audio';
  if (/(^|_)(pdf)(_|$)|pdf_url/.test(k)) return 'application/pdf';
  if (/(^|_)(image|img|photo|poster|bg_image|hover_image|cover)(_|$)/.test(k)) return 'image';
  return 'all';
});

// Contesto per il field 'object-position' (FieldObjectPosition): immagine + frame del
// FRATELLO nello stesso settings, così il pad disegna il ritaglio reale e calcola l'asse
// bloccato. Chiavi standard del tile Immagine, sovrascrivibili via field.contextKeys.
// Senza tileSettings/immagine il controllo resta usabile (pad neutro).
// contextKeys: ogni voce può essere il NOME di una chiave di settings (risolta su
// tileSettings) OPPURE un valore LETTERALE costante (utile dove l'aspetto/fit è fisso
// e non esiste una chiave, es. una cover sempre 1:1 → ratio:'1/1', fit:'cover'/'(cover)').
const OP_FITS = ['cover', 'contain', 'fill', 'none', 'scale-down'];
function literalFit(k) {
  if (!k) return null;
  const c = String(k).replace(/[()]/g, '').trim();
  return OP_FITS.includes(c) ? c : null;
}
function literalRatio(k) {
  if (!k) return null;
  if (k === 'auto') return 'auto';
  if (/^\d+(?:\.\d+)?\s*[/:]\s*\d+(?:\.\d+)?$/.test(k)) return k; // '16/9', '4:3'
  if (/^\d+(?:\.\d+)?$/.test(k)) return k;                        // '1.5'
  return null;
}
const objectPositionContext = computed(() => {
  const s = props.tileSettings || {};
  const ck = props.field.contextKeys;
  // Senza contextKeys → fallback alle chiavi del tile Immagine.
  const srcKey   = ck ? ck.src         : 'image_url';
  const fitKey   = ck ? ck.fit         : 'object_fit';
  const ratioKey = ck ? ck.ratio       : 'aspect_ratio';
  const rcKey    = ck ? ck.ratioCustom : 'aspect_ratio_custom';

  // fit: chiave settings, altrimenti valore letterale, altrimenti 'cover'.
  let objectFit = 'cover';
  if (fitKey && s[fitKey]) objectFit = s[fitKey];
  else { const lf = literalFit(fitKey); if (lf) objectFit = lf; }

  // ratio: chiave settings, altrimenti valore letterale ('16/9','1/1','auto').
  let ar;
  if (ratioKey && (ratioKey in s)) ar = s[ratioKey];
  else ar = literalRatio(ratioKey);
  const frameRatio = ar === 'custom'
    ? (rcKey && s[rcKey] ? s[rcKey] : 'auto')
    : (ar || 'auto');

  return {
    imageSrc: (srcKey && s[srcKey]) ? s[srcKey] : (ck ? '' : (s.hover_image || '')),
    objectFit,
    frameRatio,
  };
});

// Resolve the default value for this field by looking up the registered tile defaults.
// Used by FieldRange (and similar) for the "double-click to reset" feature.
const fieldDefaultValue = computed(() => {
  // Explicit per-field default has priority
  if (Object.prototype.hasOwnProperty.call(props.field, 'default')) return props.field.default;
  if (!props.tileId) return null;
  const tilesStore = useTilesStore();
  const tile = tilesStore.getTileById?.(props.tileId);
  if (!tile?.type) return null;
  const reg = tilesStore.registeredTiles?.find?.(t => t.type === tile.type);
  const defaults = reg?.defaults;
  if (!defaults) return null;
  return Object.prototype.hasOwnProperty.call(defaults, props.field.key) ? defaults[props.field.key] : null;
});

// Valore "personalizzato" (≠ default) — usato dal pallino sul pulsante reveal.
const hasNonDefaultValue = computed(() => {
  const v = effectiveValue.value;
  if (v == null || v === '') return false;
  const def = fieldDefaultValue.value;
  return def == null ? (v !== 'center center') : JSON.stringify(v) !== JSON.stringify(def);
});

const fieldProps = computed(() => {
  const base = { modelValue: effectiveValue.value };
  switch (props.field.type) {
    case 'select': return { ...base, options: resolvedOptions.value, ui: props.field.ui || 'auto' };
    case 'range': return { ...base, min: props.field.min || 0, max: props.field.max || 100, step: props.field.step || 1, defaultValue: fieldDefaultValue.value };
    case 'spacing': return { ...base, min: props.field.min ?? 0, max: props.field.max ?? 200, defaultValue: fieldDefaultValue.value };
    case 'object-position': return { ...base, imageSrc: objectPositionContext.value.imageSrc, frameRatio: objectPositionContext.value.frameRatio, objectFit: objectPositionContext.value.objectFit };
    case 'media': return { ...base, accept: mediaAccept.value };
    case 'editor': return { ...base, mode: props.field.mode || 'inline' };
    case 'icon-select': return { ...base, options: props.field.options || [] };
    case 'multi_pills': return { ...base, options: props.field.options || [] };
    case 'code': return { ...base, class: 'olo-field-code' };
    default: return base;
  }
});

const fieldPropsHover = computed(() => ({ ...fieldProps.value, modelValue: hoverValue.value }));

// ── AI Alt Text Generation ──
const tilesStore = useTilesStore();
const aiAltLoading = ref(false);
async function generateAiAlt() {
  const tile = tilesStore.tiles?.[props.tileId];
  const imageUrl = tile?.settings?.image_url || tile?.settings?.image || tile?.settings?.url || '';
  if (!imageUrl) return;
  aiAltLoading.value = true;
  try {
    const resp = await fetch((window.oloData?.restUrl || '/wp-json/') + 'olobuild/v1/ai/generate-alt', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData?.nonce || '' },
      body: JSON.stringify({ image_url: imageUrl, language: 'it' }),
    });
    const data = await resp.json();
    if (data?.text) {
      emit('update:modelValue', data.text);
    }
  } catch (e) {
    console.error('AI alt generation failed:', e);
  } finally {
    aiAltLoading.value = false;
  }
}

// ── Geocode (Nominatim) ──
const geocodeLoading = ref(false);
const geocodeError = ref('');
const geocodeResult = ref('');

async function geocodeAddress() {
  const query = (props.field.type === 'geocode') ? (effectiveValue.value || '').trim() : '';
  if (!query) return;
  geocodeLoading.value = true;
  geocodeError.value = '';
  geocodeResult.value = '';
  try {
    const url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(query);
    const resp = await fetch(url, { headers: { 'Accept-Language': 'it' } });
    const data = await resp.json();
    if (data && data.length > 0) {
      const place = data[0];
      const targetLat = props.field.targetLat || 'latitude';
      const targetLng = props.field.targetLng || 'longitude';
      const targetZoom = props.field.targetZoom || 'zoom';
      tilesStore.updateTile(props.tileId, {
        [targetLat]: parseFloat(place.lat).toFixed(6),
        [targetLng]: parseFloat(place.lon).toFixed(6),
        [targetZoom]: '16',
      });
      geocodeResult.value = place.display_name;
    } else {
      geocodeError.value = t('Nessun risultato trovato');
    }
  } catch (e) {
    geocodeError.value = t('Errore nella ricerca');
    console.error('Geocode failed:', e);
  } finally {
    geocodeLoading.value = false;
  }
}

function onDynamicUpdate(dynamicUpdate, isRemove) {
  if (isRemove) {
    emit('update:dynamic', dynamicUpdate, true);
  } else {
    emit('update:dynamic', dynamicUpdate);
  }
}
</script>

<style scoped>
/* Layout "controllo a destra del titolo".
   • .olo-field-inline      → compatto: label a sx, controllo piccolo a dx
   • .olo-field-inline-fill → il controllo riempie la parte destra (select/testo/…)
   In entrambi la label tronca con ellissi (tooltip = label intera). */
.olo-field-inline,
.olo-field-inline-fill {
  display: flex;
  align-items: center;
  gap: 10px;
  min-height: 30px;
}
.olo-field-inline { justify-content: space-between; }
.olo-field-inline > .olo-fi-label { flex: 1 1 auto; min-width: 0; }
.olo-field-inline > .olo-fi-control { flex: 0 0 auto; }
.olo-field-inline-fill > .olo-fi-label { flex: 0 1 auto; min-width: 0; max-width: 46%; }
.olo-field-inline-fill > .olo-fi-control { flex: 1 1 0; min-width: 0; }
.olo-fi-label {
  display: flex;
  align-items: center;
  margin: 0;
  font-size: 12px;
  font-weight: 500;
  color: #9ca3af;
  cursor: default;
}
.olo-fi-text {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
/* Campo `unit` reso compatto a destra del titolo (pill numero + unità, come i range):
   il numero ha larghezza fissa così non collassa nella colonna auto. */
.olo-field-inline > .olo-fi-control :deep(.fu-num) {
  flex: 0 0 48px;
  width: 48px;
  text-align: center;
}
.olo-field-inline > .olo-fi-control :deep(.fu-raw) {
  flex: 0 0 110px;
  width: 110px;
}

:deep(.olo-field-code) textarea,
.olo-field-code :deep(textarea) {
  font-family: 'Fira Code', 'Consolas', 'Monaco', 'Courier New', monospace;
  font-size: 12px;
  tab-size: 2;
  white-space: pre;
  line-height: 1.5;
}

/* Toggle Normale / Hover — UNICA grafica condivisa con .olo-es-seg (StyleEffectsStack)
   e .olo-bs-seg (StyleBoxStack): navy + pill bianca attiva. Scritto a mano (non Tailwind
   arbitrario [#hex], che il JIT non compila in modo affidabile) → identico ovunque. */
.olo-hover-seg {
  --olo-ui-accent: #e8622a;
  display: inline-flex;
  background: #16263d;
  border-radius: 9px;
  padding: 3px;
  gap: 2px;
}
.olo-hover-seg button {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  border: none;
  background: transparent;
  color: rgba(255, 255, 255, 0.7);
  font-size: 12px;
  font-weight: 600;
  padding: 4px 12px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.15s;
}
.olo-hover-seg button:hover { color: #fff; }
.olo-hover-seg button.on { background: #fff; color: #1f2937; box-shadow: 0 1px 2px rgba(16, 24, 40, 0.12); }
.olo-hover-seg button:focus-visible { outline: 2px solid var(--olo-ui-accent); outline-offset: 1px; }
.olo-hover-seg-dot { width: 5px; height: 5px; border-radius: 50%; background: var(--olo-ui-accent); }

/* Disclosure "Mostra/Nascondi punto focale" (field.reveal). Accento = arancio chrome. */
.olo-reveal-btn {
  --olo-ui-accent: #e8622a;
  display: flex; align-items: center; gap: 6px; width: 100%;
  padding: 7px 9px; border: 1px dashed #cdd2d9; border-radius: 8px;
  background: #fff; color: #374151; font-size: 12px; font-weight: 600;
  cursor: pointer; transition: border-color .15s, color .15s;
}
.olo-reveal-btn:hover { border-color: var(--olo-ui-accent); color: var(--olo-ui-accent); }
.olo-reveal-btn.is-open { border-style: solid; }
.olo-reveal-btn:focus-visible { outline: 2px solid var(--olo-ui-accent); outline-offset: 1px; }
.olo-reveal-chev { flex: none; transition: transform .15s; }
.olo-reveal-btn.is-open .olo-reveal-chev { transform: rotate(90deg); }
.olo-reveal-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--olo-ui-accent); margin-left: auto; }
.olo-reveal-body { margin-top: 8px; }
</style>
