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

      <template v-else>
      <!-- Responsive wrapper for fields with responsive: true -->
      <template v-if="field.responsive">
        <div class="mb-flex mb-items-center mb-justify-between mb-mb-1">
          <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400">
            {{ field.label }}
          </label>
          <button
            @click="respOpen = !respOpen"
            class="mb-p-0.5 mb-rounded mb-transition-colors"
            :class="respBp !== 'desktop' || respOpen ? 'mb-text-primary-400' : 'mb-text-gray-500 hover:mb-text-gray-300'"
            title="Responsive"
          >
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg>
          </button>
        </div>
        <div v-if="respOpen" class="mb-flex mb-gap-0.5 mb-mb-1.5 mb-bg-gray-700 mb-rounded-lg mb-p-0.5">
          <button
            v-for="bp in respBreakpoints"
            :key="bp.key"
            @click="respBp = bp.key"
            :class="[
              'mb-flex-1 mb-py-0.5 mb-text-[9px] mb-font-medium mb-rounded mb-transition-colors mb-text-center',
              respBp === bp.key
                ? 'mb-bg-primary-600 mb-text-white'
                : 'mb-text-gray-500 hover:mb-text-gray-400'
            ]"
            :title="bp.label"
          >{{ bp.short }}</button>
        </div>
        <div v-if="!respOpen && respBp !== 'desktop'" class="mb-mb-1">
          <span class="mb-text-[9px] mb-bg-primary-700 mb-text-primary-200 mb-px-1.5 mb-py-0.5 mb-rounded mb-font-medium">
            {{ respBreakpoints.find(b => b.key === respBp)?.label }}
          </span>
        </div>
      </template>
      <label v-else class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">
        {{ field.label }}
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
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldRange
        v-else-if="field.type === 'range'"
        :modelValue="effectiveValue"
        :min="field.min || 0"
        :max="field.max || 100"
        :step="field.step || 1"
        :placeholder="field.responsive && respBp !== 'desktop' ? 'Eredita' : ''"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldSpacing
        v-else-if="field.type === 'spacing'"
        :modelValue="effectiveValue"
        :min="field.min ?? 0"
        :max="field.max ?? 200"
        @update:modelValue="onFieldUpdate($event)"
      />

      <FieldBorderRadius
        v-else-if="field.type === 'border-radius'"
        :modelValue="effectiveValue"
        @update:modelValue="onFieldUpdate($event)"
      />

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

      <FieldMedia
        v-else-if="field.type === 'media'"
        :modelValue="effectiveValue"
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
          title="Genera alt text con AI"
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
            placeholder="Via Roma 1, Milano..."
          />
          <button
            class="mb-shrink-0 mb-px-2 mb-py-1.5 mb-bg-blue-600 hover:mb-bg-blue-500 mb-text-white mb-rounded mb-text-xs mb-transition-colors"
            :class="{ 'mb-opacity-50 mb-cursor-wait': geocodeLoading }"
            :disabled="geocodeLoading"
            title="Cerca indirizzo"
            @click="geocodeAddress"
          >
            <svg v-if="!geocodeLoading" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <span v-else class="mb-inline-block mb-animate-spin">⟳</span>
          </button>
        </div>
        <div v-if="geocodeError" class="mb-text-[10px] mb-text-red-400">{{ geocodeError }}</div>
        <div v-if="geocodeResult" class="mb-text-[10px] mb-text-green-400 mb-truncate" :title="geocodeResult">{{ geocodeResult }}</div>
      </div>

      <FieldText
        v-else
        :modelValue="effectiveValue"
        @update:modelValue="onFieldUpdate($event)"
        @confirm="$emit('confirm', $event)"
      />
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
import FieldSpacing from './fields/FieldSpacing.vue';
import FieldBorderRadius from './fields/FieldBorderRadius.vue';
import FieldImage from './fields/FieldImage.vue';
import FieldMedia from './fields/FieldMedia.vue';
import FieldLottiePicker from './fields/FieldLottiePicker.vue';
import FieldGallery from './fields/FieldGallery.vue';
import FieldIcon from './fields/FieldIcon.vue';
import FieldMegaPanelMap from './fields/FieldMegaPanelMap.vue';
import FieldMultiPills from './fields/FieldMultiPills.vue';
import FieldBoxShadow from './fields/FieldBoxShadow.vue';
import FieldGradient from './fields/FieldGradient.vue';
import FieldTransform from './fields/FieldTransform.vue';
import FieldFontFamily from './fields/FieldFontFamily.vue';
import FieldDatetime from './fields/FieldDatetime.vue';
import FieldDate from './fields/FieldDate.vue';
import FieldTime from './fields/FieldTime.vue';
import FieldIconSelect from './fields/FieldIconSelect.vue';
import DynamicFieldToggle from './DynamicFieldToggle.vue';
import { useTilesStore } from '@/stores/tiles';
import { useStylesStore } from '@/stores/styles';

import FieldEditor from './fields/FieldEditor.vue';

const DYNAMIC_TYPES = ['text', 'textarea', 'editor', 'image', 'media'];

const props = defineProps({
  field: { type: Object, required: true },
  modelValue: { default: '' },
  tileId: { type: String, default: '' },
  dynamic: { type: Object, default: () => ({}) },
  tileSettings: { type: Object, default: null },
});

const emit = defineEmits(['update:modelValue', 'update:dynamic', 'update:attachmentId', 'confirm', 'update:responsiveValue']);

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

function onFieldUpdate(value) {
  if (props.field.responsive && respBp.value !== 'desktop') {
    emit('update:responsiveValue', { key: respKey.value, value });
  } else {
    emit('update:modelValue', value);
  }
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
    if (props.field.optionsSource === 'metaPrefixes') {
      return md.metaPrefixes || [];
    }
    if (props.field.optionsSource === 'serviceList') {
      const list = (md.serviceList || []);
      return [{ value: '', label: '— Tutti i servizi —' }, ...list];
    }
    if (props.field.optionsSource === 'wpPages') {
      return md.wpPages || [];
    }
    if (props.field.optionsSource === 'searchTiles') {
      const tilesStore = useTilesStore();
      const results = [{ value: '', label: '— Nessuna ricerca —' }];
      const walk = (nodes) => {
        for (const node of nodes) {
          if (node.type === 'livesearch' || node.type === 'search') {
            const lbl = node.settings?.placeholder || (node.type === 'livesearch' ? 'Ricerca Live' : 'Ricerca');
            results.push({ value: node.id, label: (node.type === 'livesearch' ? 'Ricerca Live' : 'Ricerca') + ' — ' + lbl });
          }
          if (Array.isArray(node.children)) walk(node.children);
        }
      };
      walk(tilesStore.canvasTiles || []);
      return results;
    }
    if (props.field.optionsSource === 'globalTypography') {
      const stylesStore = useStylesStore();
      const sets = stylesStore.globalTypography || [];
      return [
        { value: '', label: '— Nessuno —' },
        ...sets.map(gt => ({ value: gt.id, label: gt.label || gt.id }))
      ];
    }
    if (props.field.optionsSource === 'wpMenuItems') {
      const depKey = props.field.optionsDependOn || 'menu_id';
      const tilesStore = useTilesStore();
      const tile = props.tileId ? tilesStore.getTileById(props.tileId) : null;
      const menuId = parseInt(tile?.settings?.[depKey] || 0);
      const menu = (md.wpMenus || []).find(m => m.id === menuId);
      if (!menu || !menu.items) return [{ value: '0', label: '— Seleziona —' }];
      const opts = [{ value: '0', label: '— Seleziona —' }];
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
    case 'border-radius': return FieldBorderRadius;
    case 'editor': return FieldEditor;
    case 'image': return FieldImage;
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
    default: return FieldText;
  }
});

const effectiveValue = computed(() => props.field.responsive ? respValue.value : props.modelValue);

const fieldProps = computed(() => {
  const base = { modelValue: effectiveValue.value };
  switch (props.field.type) {
    case 'select': return { ...base, options: resolvedOptions.value };
    case 'range': return { ...base, min: props.field.min || 0, max: props.field.max || 100, step: props.field.step || 1 };
    case 'editor': return { ...base, mode: props.field.mode || 'inline' };
    case 'icon-select': return { ...base, options: props.field.options || [] };
    case 'multi_pills': return { ...base, options: props.field.options || [] };
    case 'code': return { ...base, class: 'olo-field-code' };
    default: return base;
  }
});

// ── AI Alt Text Generation ──
const tilesStore = useTilesStore();
const aiAltLoading = ref(false);
async function generateAiAlt() {
  const tile = tilesStore.tiles?.[props.tileId];
  const imageUrl = tile?.settings?.image_url || tile?.settings?.image || tile?.settings?.url || '';
  if (!imageUrl) return;
  aiAltLoading.value = true;
  try {
    const resp = await fetch((window.oloData?.restUrl || '/wp-json/') + 'olo/v1/ai/generate-alt', {
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
      geocodeError.value = 'Nessun risultato trovato';
    }
  } catch (e) {
    geocodeError.value = 'Errore nella ricerca';
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
:deep(.olo-field-code) textarea,
.olo-field-code :deep(textarea) {
  font-family: 'Fira Code', 'Consolas', 'Monaco', 'Courier New', monospace;
  font-size: 12px;
  tab-size: 2;
  white-space: pre;
  line-height: 1.5;
}
</style>
