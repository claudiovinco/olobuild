<template>
  <div class="mb-space-y-3">
    <!-- Due blocchi, sempre in quest'ordine e con questi nomi: ELEMENTO (ciò che la
         tile disegna, tile.settings) e CONTENITORE (il riquadro della tile nella
         griglia, tile.style). Stessa parola, due oggetti diversi — «Raggio», «Bordo»,
         «Ombra» esistono in entrambi — e senza l'intestazione non si capiva su cosa
         agisse ciascuno. Le intestazioni compaiono solo quando i blocchi sono due. -->
    <div v-if="dueBlocchi && tileStyleSections.length" class="olo-sfr-blocco">
      <span class="olo-sfr-blocco-titolo">{{ t('Elemento') }}</span>
    </div>
    <!-- Tile-specific style sections (es. tipografia hero, colori CTA) — letti/scritti su tile.settings -->
    <template v-for="section in tileStyleSections" :key="'tilesty-' + section.idx">
      <CollapseSection
        v-if="section.label && sectionHasVisibleFields(section)"
        :id="'v2i-sec-tilesty-' + section.idx"
        :title="t(section.label)"
        :defaultOpen="section.idx <= 1"
        :forceOpen="searchActive"
      >
        <div class="mb-space-y-3">
          <template v-for="(field, fIdx) in section.fields" :key="field.key || ('tsf-' + section.idx + '-' + fIdx)">
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

    <div v-if="dueBlocchi && groupedSections.length" class="olo-sfr-blocco">
      <span class="olo-sfr-blocco-titolo">{{ t('Contenitore') }}</span>
      <span class="olo-sfr-blocco-nota">{{ atomica
        ? t('Lo spazio attorno all\'elemento nella griglia. Sfondo, bordo e ombra si impostano sull\'elemento, qui sopra.')
        : t('Il riquadro che contiene la tile nella griglia.') }}</span>
    </div>
    <!-- Wrapper style sections (universali — letti/scritti su tile.style) -->
    <template v-for="section in groupedSections" :key="'sec-' + section.idx">
      <CollapseSection
        v-if="section.label"
        :id="'v2i-sec-stile-' + section.idx"
        :title="t(section.label)"
        :defaultOpen="section.idx <= 1"
        :forceOpen="searchActive"
      >
        <div class="mb-space-y-3">
          <template v-for="(field, fIdx) in section.fields" :key="field.key || ('f-' + section.idx + '-' + fIdx)">
            <StyleLayoutStack
              v-if="field.type === 'layout-stack'"
              :tileStyle="tileStyle"
              :tileType="tileType"
              @update="$emit('update', $event)"
            />
            <StyleBoxStack
              v-else-if="field.type === 'box-stack'"
              :tileStyle="tileStyle"
              :soloSpazi="!!field.soloSpazi"
              @update="$emit('update', $event)"
            />
            <StyleEffectsStack
              v-else-if="field.type === 'effects-stack'"
              :tileStyle="tileStyle"
              :atomica="!!field.atomica"
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
              v-else-if="!isMultiKey(field.type) && isFieldVisible(field, tileStyle)"
              :field="field"
              :modelValue="tileStyle?.[field.key] ?? ''"
              :tileSettings="tileStyle"
              :hoverNested="true"
              @update:modelValue="emitMain(field.key, $event)"
              @update:hoverValue="emitHover($event)"
              @update:responsiveValue="emitResponsive($event)"
            />
            <!-- Multi-key types: text-shadow, backdrop-filter, border-legacy.
                 v-else-if (non v-else): un field normale nascosto da condition
                 non deve cadere qui e renderizzare una label orfana. -->
            <div v-else-if="isFieldVisible(field, tileStyle)">
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
              :soloSpazi="!!field.soloSpazi"
              @update="$emit('update', $event)"
            />
            <StyleEffectsStack
              v-else-if="field.type === 'effects-stack'"
              :tileStyle="tileStyle"
              :atomica="!!field.atomica"
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
              v-else-if="!isMultiKey(field.type) && isFieldVisible(field, tileStyle)"
              :field="field"
              :modelValue="tileStyle?.[field.key] ?? ''"
              :tileSettings="tileStyle"
              :hoverNested="true"
              @update:modelValue="emitMain(field.key, $event)"
              @update:hoverValue="emitHover($event)"
              @update:responsiveValue="emitResponsive($event)"
            />
            <div v-else-if="isFieldVisible(field, tileStyle)">
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
import StyleBoxStack from './style-renderers/StyleBoxStack.vue';
import StyleLayoutStack from './style-renderers/StyleLayoutStack.vue';
import StyleEffectsStack from './style-renderers/StyleEffectsStack.vue';
import StyleShadowBlock from './style-renderers/StyleShadowBlock.vue';
import StyleNestedField from './style-renderers/StyleNestedField.vue';
import FieldTextShadow from './fields/FieldTextShadow.vue';
import FieldBackdropFilter from './fields/FieldBackdropFilter.vue';
import FieldBorderLegacy from './fields/FieldBorderLegacy.vue';
import { t } from '@/i18n';
import { ATOMIC_TILE_TYPES } from '@/composables/useBackgroundStyle';
import { evaluateCondition } from '@/utils/fieldCondition';
import { normalizeSearchQuery, fieldMatchesSearch, sectionLabelMatchesSearch } from '@/utils/inspectorSearch.js';

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
  // Query del campo "Cerca impostazione..." dell'inspector — filtra i field
  // del tab Stile con la stessa logica label/key del tab Contenuto.
  searchQuery:  { type: String, default: '' },
});
const emit = defineEmits(['update']);

const searchQ      = computed(() => normalizeSearchQuery(props.searchQuery));
const searchActive = computed(() => !!searchQ.value);

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
  // idx = indice ORIGINALE della sezione: gli id v2i-sec-* devono restare stabili
  // anche quando il filtro di ricerca rimuove sezioni (rail + scroll-spy + restore).
  return sections.map((s, i) => ({ ...s, idx: i }));
}

// Filtro ricerca: una sezione resta visibile per intero se la sua label matcha,
// altrimenti restano solo i field che matchano; sezioni senza match spariscono.
function filterSectionsBySearch(sections) {
  if (!searchQ.value) return sections;
  const out = [];
  for (const s of sections) {
    if (sectionLabelMatchesSearch(s.label, searchQ.value)) { out.push(s); continue; }
    const fields = s.fields.filter((f) => fieldMatchesSearch(f, searchQ.value));
    if (fields.length > 0) out.push({ ...s, fields });
  }
  return out;
}

// Valuta la `condition` dichiarata sul field (es. `text_effect_phrases` visibile
// SOLO se `text_effect === 'typewriter-loop'`). Stessa logica di
// BuilderInspector.evaluateCondition: senza questa i field tile-specific
// venivano renderizzati sempre, ignorando la condizione.
// evaluateCondition: helper condiviso (@/utils/fieldCondition).
function isFieldVisible(field, settings) {
  if (field.condition && !evaluateCondition(field.condition, settings)) return false;
  if (typeof field.show === 'function' && !field.show(settings)) return false;
  return true;
}
function sectionHasVisibleFields(section) {
  return (section.fields || []).some(f => isFieldVisible(f, props.tileSettings));
}

const groupedSections   = computed(() => filterSectionsBySearch(groupBySeparator(styleFieldsBase(props.tileType))));
const tileStyleSections = computed(() => {
  // Nel tab Stile gli "Effetti bordo" del WRAPPER (groupedSections → borderEffectFields)
  // sono già presenti. Rimuoviamo l'eventuale sezione effetti dai field-stile del tile
  // (quando il config include `...borderFields()` nei styleFields) per evitare il doppione
  // "Effetti bordo". Solo rendering: `borderFields()` e lo standard restano INVARIATI — il
  // controllo "Bordo" del tile resta, sparisce solo la sezione effetti duplicata.
  const fields = (props.tileFields || []).filter(f => {
    if (f.type === 'separator' && f.label === t('Effetti bordo')) return false;
    if (typeof f.key === 'string' && f.key.startsWith('border_effect')) return false;
    return true;
  });
  return filterSectionsBySearch(groupBySeparator(fields));
});

// Blocchi «Elemento» / «Contenitore»: servono quando la tile ha stili propri. Per
// sezione, riga e colonna l'elemento È il contenitore: un blocco solo, senza titoli.
const STRUTTURALI = new Set(['section', 'row', 'column', 'inner-columns']);
const atomica   = computed(() => ATOMIC_TILE_TYPES.has(props.tileType));
const dueBlocchi = computed(() => !STRUTTURALI.has(props.tileType) && (props.tileFields || []).length > 0);

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

<style scoped>
/* Intestazione di blocco (Elemento / Contenitore): piu' in alto nella gerarchia
   delle sezioni, quindi a sinistra, accento chrome e filetto, non un'altra card. */
.olo-sfr-blocco {
  display: flex;
  flex-direction: column;
  gap: 3px;
  padding: 8px 4px 0;
}
.olo-sfr-blocco-titolo {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--olo-ui-accent, #e8622a);
}
.olo-sfr-blocco-titolo::after {
  content: '';
  flex: 1;
  height: 1px;
  background: currentColor;
  opacity: 0.25;
}
.olo-sfr-blocco-nota {
  font-size: 12px;
  line-height: 1.45;
  color: #6b7280;
}
</style>
