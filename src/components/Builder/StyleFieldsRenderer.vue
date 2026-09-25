<template>
  <div class="mb-space-y-3">
    <!-- Due blocchi, sempre in quest'ordine e con questi nomi: ELEMENTO (ciò che la
         tile disegna, tile.settings) e CONTENITORE (il riquadro della tile nella
         griglia, tile.style). Stessa parola, due oggetti diversi — «Raggio», «Bordo»,
         «Ombra» esistono in entrambi — e senza l'intestazione non si capiva su cosa
         agisse ciascuno. Sezione, riga e colonna non le hanno: lì l'elemento È il
         contenitore. -->
    <div v-if="conBlocchi && tileStyleSections.length" class="olo-sfr-blocco">
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
              :tileType="tileType"
              @update:modelValue="emitSetting(field.key, $event)"
              @update:hoverValue="emitSetting($event.key, $event.value)"
              @update:responsiveValue="emitSetting($event.key, $event.value)"
              @update:settingKey="emitSetting($event.key, $event.value)"
            >
              <template #content-items>
                <ContentItemsEditor
                  :modelValue="vociDi(field)"
                  :itemFields="field.itemFields || []"
                  :newItemDefaults="defaultVociDi(field)"
                  :itemLabel="field.itemLabel || 'Item'"
                  :tileSettings="tileSettings"
                  :strutturaFissa="true"
                  :etichettaDa="field.etichettaDa || ''"
                  :miniaturaDa="field.miniaturaDa || ''"
                  @update:modelValue="emitSetting(field.key, $event)"
                />
              </template>
            </InspectorField>
          </template>
        </div>
      </CollapseSection>
      <!-- Solo la sezione SENZA intestazione (campi prima del primo separatore): una sezione
           con nome nascosta dalla condizione del separatore non deve ricadere qui e mostrare
           i campi senza titolo. -->
      <template v-else-if="!section.label">
        <div class="mb-space-y-3">
          <template v-for="(field, fIdx) in section.fields" :key="field.key || ('tsf0-' + fIdx)">
            <InspectorField
              v-if="isFieldVisible(field, tileSettings)"
              :field="field"
              :modelValue="tileSettings?.[field.key] ?? ''"
              :tileSettings="tileSettings"
              :tileType="tileType"
              @update:modelValue="emitSetting(field.key, $event)"
              @update:hoverValue="emitSetting($event.key, $event.value)"
              @update:responsiveValue="emitSetting($event.key, $event.value)"
              @update:settingKey="emitSetting($event.key, $event.value)"
            >
              <template #content-items>
                <ContentItemsEditor
                  :modelValue="vociDi(field)"
                  :itemFields="field.itemFields || []"
                  :newItemDefaults="defaultVociDi(field)"
                  :itemLabel="field.itemLabel || 'Item'"
                  :tileSettings="tileSettings"
                  :strutturaFissa="true"
                  :etichettaDa="field.etichettaDa || ''"
                  :miniaturaDa="field.miniaturaDa || ''"
                  @update:modelValue="emitSetting(field.key, $event)"
                />
              </template>
            </InspectorField>
          </template>
        </div>
      </template>
    </template>

    <div v-if="conBlocchi && groupedSections.length" class="olo-sfr-blocco">
      <span class="olo-sfr-blocco-titolo">{{ t('Contenitore') }}</span>
      <span class="olo-sfr-blocco-nota">{{ atomica
        ? t('Lo spazio attorno all\'elemento nella griglia resta trasparente: lo Sfondo qui sotto si disegna sull\'elemento.')
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
            <StyleNestedField
              v-else-if="field.key && field.key.includes('.')"
              :field="field"
              :tileStyle="tileStyle"
              @update="$emit('update', $event)"
            />
            <InspectorField
              v-else-if="isFieldVisible(field, tileStyle)"
              :field="field"
              :modelValue="tileStyle?.[field.key] ?? ''"
              :tileSettings="tileStyle"
              :hoverNested="true"
              @update:modelValue="emitMain(field.key, $event)"
              @update:hoverValue="emitHover($event)"
              @update:responsiveValue="emitResponsive($event)"
            />
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
            <StyleNestedField
              v-else-if="field.key && field.key.includes('.')"
              :field="field"
              :tileStyle="tileStyle"
              @update="$emit('update', $event)"
            />
            <InspectorField
              v-else-if="isFieldVisible(field, tileStyle)"
              :field="field"
              :modelValue="tileStyle?.[field.key] ?? ''"
              :tileSettings="tileStyle"
              :hoverNested="true"
              @update:modelValue="emitMain(field.key, $event)"
              @update:hoverValue="emitHover($event)"
              @update:responsiveValue="emitResponsive($event)"
            />
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
import ContentItemsEditor from './ContentItemsEditor.vue';
import { getElementDefaults, getElementFields } from '@/config/elementRegistry';
import StyleBoxStack from './style-renderers/StyleBoxStack.vue';
import StyleLayoutStack from './style-renderers/StyleLayoutStack.vue';
import StyleEffectsStack from './style-renderers/StyleEffectsStack.vue';
import StyleNestedField from './style-renderers/StyleNestedField.vue';
import { t } from '@/i18n';
import { ATOMIC_TILE_TYPES } from '@/composables/useBackgroundStyle';
import { isFieldVisible as sharedIsFieldVisible, isSectionVisible } from '@/utils/fieldCondition';
import { normalizeSearchQuery, fieldMatchesSearch, sectionLabelMatchesSearch } from '@/utils/inspectorSearch.js';

/**
 * StyleFieldsRenderer — render data-driven del tab Stile a partire da
 * styleFieldsBase(), montato dal tab Stile di BuilderInspector.vue.
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

// Stile delle voci di un ripetitore: stesse voci del Contenuto. Se la tile non le ha
// ancora salvate, si parte dai default del config — come fa il Contenuto
// (ensureContentItems) — così un colore scelto qui non crea voci senza testo.
function vociDi(field) {
  const v = props.tileSettings?.[field.key];
  if (Array.isArray(v)) return v;
  const def = (getElementDefaults(props.tileType) || {})[field.key];
  return Array.isArray(def) ? JSON.parse(JSON.stringify(def)) : [];
}

// Valori di una voce NUOVA del ripetitore: lo specchio dello Stile non li dichiara, li ha
// il campo gemello del Contenuto (stessa chiave). Servono al doppio clic sui numeri delle
// voci, che torna al valore di una voce nuova invece che al minimo.
function defaultVociDi(field) {
  if (field.newItemDefaults) return field.newItemDefaults;
  const gemello = getElementFields(props.tileType).find(f => f && f.key === field.key && f.type === 'content-items');
  return gemello?.newItemDefaults || {};
}

const searchQ      = computed(() => normalizeSearchQuery(props.searchQuery));
const searchActive = computed(() => !!searchQ.value);

function groupBySeparator(fields) {
  const sections = [];
  let current = { label: null, sep: null, fields: [] };
  for (const f of fields) {
    if (f.type === 'separator') {
      if (current.fields.length > 0) sections.push(current);
      // `sep`: la condizione del separatore vale per tutta la sezione (isSectionVisible)
      current = { label: f.label, sep: f, fields: [] };
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
// SOLO se `text_effect === 'typewriter-loop'`): valutatore condiviso (@/utils/fieldCondition).
function isFieldVisible(field, settings) {
  return sharedIsFieldVisible(field, settings || {});
}
// Una sezione si vede se la condizione del suo SEPARATORE è vera e ha un campo visibile.
function sectionHasVisibleFields(section) {
  return isSectionVisible(section.sep, section.fields, props.tileSettings || {});
}

const groupedSections   = computed(() => filterSectionsBySearch(groupBySeparator(styleFieldsBase(props.tileType))));
// Nessun campo della tile viene tolto qui. Gli effetti del bordo dell'ELEMENTO (settings, sotto il
// suo Bordo) e quelli del CONTENITORE (style) agiscono su oggetti diversi: non sono un doppione.
// Quando si vedono lo decidono le loro condizioni (borderFields in _shared.js).
const tileStyleSections = computed(() => filterSectionsBySearch(groupBySeparator(props.tileFields || [])));

// Blocchi «Elemento» / «Contenitore» per ogni tile di contenuto. Per sezione, riga
// e colonna l'elemento È il contenitore: un blocco solo, senza titoli.
const STRUTTURALI = new Set(['section', 'row', 'column', 'inner-columns']);
const atomica    = computed(() => ATOMIC_TILE_TYPES.has(props.tileType));
const conBlocchi = computed(() => !!props.tileType && !STRUTTURALI.has(props.tileType));

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
