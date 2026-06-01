<template>
  <div class="mb-space-y-3">
    <!-- Tile-specific style sections (es. tipografia hero, colori CTA) — letti/scritti su tile.settings -->
    <template v-for="(section, sIdx) in tileStyleSections" :key="'tilesty-' + sIdx">
      <CollapseSection
        v-if="section.label && sectionHasVisibleFields(section)"
        :id="'v2i-sec-tilesty-' + sIdx"
        :title="t(section.label)"
        :defaultOpen="sIdx <= 1"
      >
        <div class="mb-space-y-3">
          <template v-for="(field, fIdx) in section.fields" :key="field.key || ('tsf-' + sIdx + '-' + fIdx)">
            <InspectorField
              v-if="isFieldVisible(field, tileSettings)"
              :field="field"
              :modelValue="tileSettings?.[field.key] ?? ''"
              :tileSettings="tileSettings"
              @update:modelValue="emitSetting(field.key, $event)"
              @update:hoverValue="emitSetting($event.key, $event.value)"
              @update:responsiveValue="emitSetting($event.key, $event.value)"
              @update:settingKey="emitSetting($event.key, $event.value)"
            />
          </template>
        </div>
      </CollapseSection>
      <template v-else>
        <div class="mb-space-y-3">
          <template v-for="(field, fIdx) in section.fields" :key="field.key || ('tsf0-' + fIdx)">
            <InspectorField
              v-if="isFieldVisible(field, tileSettings)"
              :field="field"
              :modelValue="tileSettings?.[field.key] ?? ''"
              :tileSettings="tileSettings"
              @update:modelValue="emitSetting(field.key, $event)"
              @update:hoverValue="emitSetting($event.key, $event.value)"
              @update:responsiveValue="emitSetting($event.key, $event.value)"
              @update:settingKey="emitSetting($event.key, $event.value)"
            />
          </template>
        </div>
      </template>
    </template>

    <!-- Wrapper style sections (universali — letti/scritti su tile.style) -->
    <template v-for="(section, sIdx) in groupedSections" :key="'sec-' + sIdx">
      <CollapseSection
        v-if="section.label"
        :id="'v2i-sec-stile-' + sIdx"
        :title="t(section.label)"
        :defaultOpen="sIdx <= 1"
      >
        <div class="mb-space-y-3">
          <template v-for="(field, fIdx) in section.fields" :key="field.key || ('f-' + sIdx + '-' + fIdx)">
            <StyleLayoutStack
              v-if="field.type === 'layout-stack'"
              :tileStyle="tileStyle"
              :tileType="tileType"
              @update="$emit('update', $event)"
            />
            <StyleBoxStack
              v-else-if="field.type === 'box-stack'"
              :tileStyle="tileStyle"
              @update="$emit('update', $event)"
            />
            <StyleEffectsStack
              v-else-if="field.type === 'effects-stack'"
              :tileStyle="tileStyle"
              @update="$emit('update', $event)"
            />
            <StyleSpacingBp
              v-else-if="field.type === 'spacing-bp'"
              :field="field"
              :tileStyle="tileStyle"
              @update="$emit('update', $event)"
            />
            <StyleShadowBlock
              v-else-if="field.type === 'shadow-block'"
              :field="field"
              :tileStyle="tileStyle"
              @update="$emit('update', $event)"
            />
            <StyleNestedField
              v-else-if="field.key && field.key.includes('.')"
              :field="field"
              :tileStyle="tileStyle"
              @update="$emit('update', $event)"
            />
            <InspectorField
              v-else-if="!isMultiKey(field.type)"
              :field="field"
              :modelValue="tileStyle?.[field.key] ?? ''"
              :tileSettings="tileStyle"
              :hoverNested="true"
              @update:modelValue="emitMain(field.key, $event)"
              @update:hoverValue="emitHover($event)"
              @update:responsiveValue="emitResponsive($event)"
            />
            <!-- Multi-key types: text-shadow, backdrop-filter, border-legacy -->
            <div v-else>
              <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t(field.label) }}</label>
              <component
                :is="multiKeyComponent(field.type)"
                :modelValue="multiKeyValue(field)"
                :hoverable="!!field.hoverable"
                :hoverModelValue="multiKeyHoverValue(field)"
                @update:modelValue="onMultiKeyUpdate(field, $event)"
                @update:hoverModelValue="onMultiKeyHoverUpdate(field, $event)"
              />
            </div>
          </template>
        </div>
      </CollapseSection>

      <template v-else>
        <div class="mb-space-y-3">
          <template v-for="(field, fIdx) in section.fields" :key="field.key || ('f0-' + fIdx)">
            <StyleLayoutStack
              v-if="field.type === 'layout-stack'"
              :tileStyle="tileStyle"
              :tileType="tileType"
              @update="$emit('update', $event)"
            />
            <StyleBoxStack
              v-else-if="field.type === 'box-stack'"
              :tileStyle="tileStyle"
              @update="$emit('update', $event)"
            />
            <StyleEffectsStack
              v-else-if="field.type === 'effects-stack'"
              :tileStyle="tileStyle"
              @update="$emit('update', $event)"
            />
            <StyleSpacingBp
              v-else-if="field.type === 'spacing-bp'"
              :field="field"
              :tileStyle="tileStyle"
              @update="$emit('update', $event)"
            />
            <StyleShadowBlock
              v-else-if="field.type === 'shadow-block'"
              :field="field"
              :tileStyle="tileStyle"
              @update="$emit('update', $event)"
            />
            <StyleNestedField
              v-else-if="field.key && field.key.includes('.')"
              :field="field"
              :tileStyle="tileStyle"
              @update="$emit('update', $event)"
            />
            <InspectorField
              v-else-if="!isMultiKey(field.type)"
              :field="field"
              :modelValue="tileStyle?.[field.key] ?? ''"
              :tileSettings="tileStyle"
              :hoverNested="true"
              @update:modelValue="emitMain(field.key, $event)"
              @update:hoverValue="emitHover($event)"
              @update:responsiveValue="emitResponsive($event)"
            />
            <div v-else>
              <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t(field.label) }}</label>
              <component
                :is="multiKeyComponent(field.type)"
                :modelValue="multiKeyValue(field)"
                @update:modelValue="onMultiKeyUpdate(field, $event)"
              />
            </div>
          </template>
        </div>
      </template>
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { styleFieldsBase } from '@/config/elements/_styleFieldsBase.js';
import CollapseSection from './CollapseSection.vue';
import InspectorField from './InspectorField.vue';
import StyleSpacingBp from './style-renderers/StyleSpacingBp.vue';
import StyleBoxStack from './style-renderers/StyleBoxStack.vue';
import StyleLayoutStack from './style-renderers/StyleLayoutStack.vue';
import StyleEffectsStack from './style-renderers/StyleEffectsStack.vue';
import StyleShadowBlock from './style-renderers/StyleShadowBlock.vue';
import StyleNestedField from './style-renderers/StyleNestedField.vue';
import FieldTextShadow from './fields/FieldTextShadow.vue';
import FieldBackdropFilter from './fields/FieldBackdropFilter.vue';
import FieldBorderLegacy from './fields/FieldBorderLegacy.vue';
import { t } from '@/i18n';

// Mapping multi-key: oggetto-UI → chiavi piatte salvate su tile.style
// Chiavi PHP-renderer-compatible: NON cambia il formato salvato, solo la UI consolidata.
const MULTI_KEY_MAP = {
  'text-shadow':     [ ['h', 'text_shadow_h'], ['v', 'text_shadow_v'], ['blur', 'text_shadow_blur'], ['color', 'text_shadow_color'] ],
  'backdrop-filter': [ ['blur', 'backdrop_blur'], ['brightness', 'backdrop_brightness'], ['saturate', 'backdrop_saturate'] ],
  'border-legacy':   [ ['width', 'border_width'], ['style', 'border_style'], ['color', 'border_color'] ],
};

/**
 * StyleFieldsRenderer — render data-driven del tab Stile a partire da
 * styleFieldsBase(). Sostituisce il template hard-coded di
 * BuilderInspector.vue:227-534 (sotto-tab Normale).
 *
 * Ascolta gli eventi dei sub-renderer e emette UN solo evento `update` al parent
 * con un payload uniforme:
 *   { type: 'main',     key, value }   → updateStyle(key, value)
 *   { type: 'hover',    key, value }   → updateHover(key, value)
 *   { type: 'transition', key, value } → updateTransition(key, value)
 *   { type: 'multi', updates: [{key, value}, ...] } → loop updateStyle
 *   { type: 'nested', path, value }    → setIn(tile.style, path, value) — fallback
 */
const props = defineProps({
  tileStyle: { type: Object, required: true },
  // Tile-specific style fields (es. typo hero, CTA stile) — opzionale, dichiarato
  // dal config del tile come `styleFields[]`. Salvati su tile.settings (NON tile.style).
  tileFields:   { type: Array,  default: () => [] },
  tileSettings: { type: Object, default: () => ({}) },
  // Tipo della tile (section/row/column/element type) — usato da styleFieldsBase per
  // nascondere i field wrapper duplicati su tile strutturali.
  tileType:     { type: String, default: '' },
});
const emit = defineEmits(['update']);

function groupBySeparator(fields) {
  const sections = [];
  let current = { label: null, fields: [] };
  for (const f of fields) {
    if (f.type === 'separator') {
      if (current.fields.length > 0) sections.push(current);
      current = { label: f.label, fields: [] };
    } else {
      current.fields.push(f);
    }
  }
  if (current.fields.length > 0) sections.push(current);
  return sections;
}

// Valuta la `condition` dichiarata sul field (es. `text_effect_phrases` visibile
// SOLO se `text_effect === 'typewriter-loop'`). Stessa logica di
// BuilderInspector.evaluateCondition: senza questa i field tile-specific
// venivano renderizzati sempre, ignorando la condizione.
function evaluateCondition(condition, settings) {
  if (!condition || !settings) return true;
  const val = settings[condition.field];
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
function isFieldVisible(field, settings) {
  if (field.condition && !evaluateCondition(field.condition, settings)) return false;
  if (typeof field.show === 'function' && !field.show(settings)) return false;
  return true;
}
function sectionHasVisibleFields(section) {
  return (section.fields || []).some(f => isFieldVisible(f, props.tileSettings));
}

const groupedSections   = computed(() => groupBySeparator(styleFieldsBase(props.tileType)));
const tileStyleSections = computed(() => groupBySeparator(props.tileFields || []));

function emitMain(key, value) {
  emit('update', { type: 'main', key, value });
}
function emitHover({ key, value }) {
  emit('update', { type: 'hover', key, value });
}
function emitResponsive({ key, value }) {
  emit('update', { type: 'main', key, value });
}
// Tile-specific style fields → vanno scritti in tile.settings (non tile.style).
function emitSetting(key, value) {
  emit('update', { type: 'setting', key, value });
}

function isMultiKey(type) {
  return Object.prototype.hasOwnProperty.call(MULTI_KEY_MAP, type);
}
function multiKeyComponent(type) {
  if (type === 'text-shadow') return FieldTextShadow;
  if (type === 'backdrop-filter') return FieldBackdropFilter;
  if (type === 'border-legacy') return FieldBorderLegacy;
  return null;
}
function multiKeyValue(field) {
  const map = MULTI_KEY_MAP[field.type] || [];
  const out = {};
  for (const [objKey, flatKey] of map) {
    out[objKey] = props.tileStyle?.[flatKey] ?? '';
  }
  return out;
}
function onMultiKeyUpdate(field, newObj) {
  const map = MULTI_KEY_MAP[field.type] || [];
  const updates = map.map(([objKey, flatKey]) => ({ key: flatKey, value: newObj?.[objKey] ?? '' }));
  emit('update', { type: 'multi', updates });
}

// Hover support per multi-key field marcati con withHover() in styleFieldsBase.
// Lettura/scrittura su tile.style.hover.<flatKey> (stesso schema legacy del bg_color hover).
function multiKeyHoverValue(field) {
  if (!field.hoverable) return {};
  const map = MULTI_KEY_MAP[field.type] || [];
  const hover = props.tileStyle?.hover || {};
  const out = {};
  for (const [objKey, flatKey] of map) {
    out[objKey] = hover[flatKey] ?? '';
  }
  return out;
}
function onMultiKeyHoverUpdate(field, newObj) {
  if (!field.hoverable) return;
  const map = MULTI_KEY_MAP[field.type] || [];
  // Emette N eventi hover, uno per chiave: il dispatcher onStyleUpdate li applica via updateHover.
  for (const [objKey, flatKey] of map) {
    emit('update', { type: 'hover', key: flatKey, value: newObj?.[objKey] ?? '' });
  }
}
</script>
