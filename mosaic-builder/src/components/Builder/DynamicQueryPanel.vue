<template>
  <div class="dqp-wrap">
    <!-- Toggle -->
    <div class="dqp-toggle-row">
      <label class="dqp-toggle-label">
        <button
          type="button"
          @click="toggleEnabled"
          :class="[
            'dqp-switch',
            isEnabled ? 'dqp-switch--on' : ''
          ]"
        >
          <span class="dqp-switch-thumb"></span>
        </button>
        <span>Sorgente dinamica</span>
      </label>
    </div>

    <!-- Query config -->
    <div v-if="isEnabled" class="dqp-config">
      <!-- Post Type -->
      <div class="dqp-field">
        <label class="dqp-label">Post Type</label>
        <select :value="localQuery.post_type" @change="updateQuery('post_type', $event.target.value)" class="dqp-select">
          <option v-for="pt in postTypes" :key="pt.value" :value="pt.value">{{ pt.label }}</option>
        </select>
      </div>

      <!-- Posts per page -->
      <div class="dqp-field">
        <label class="dqp-label">Numero di elementi</label>
        <input
          type="number"
          :value="localQuery.posts_per_page"
          @input="updateQuery('posts_per_page', parseInt($event.target.value) || 6)"
          class="dqp-input"
          min="1"
          max="50"
        />
      </div>

      <!-- Order by -->
      <div class="dqp-row">
        <div class="dqp-field dqp-field--half">
          <label class="dqp-label">Ordina per</label>
          <select :value="localQuery.orderby" @change="updateQuery('orderby', $event.target.value)" class="dqp-select">
            <option value="date">Data</option>
            <option value="title">Titolo</option>
            <option value="modified">Ultima modifica</option>
            <option value="rand">Casuale</option>
            <option value="menu_order">Ordine menu</option>
          </select>
        </div>
        <div class="dqp-field dqp-field--half">
          <label class="dqp-label">Ordine</label>
          <select :value="localQuery.order" @change="updateQuery('order', $event.target.value)" class="dqp-select">
            <option value="DESC">DESC</option>
            <option value="ASC">ASC</option>
          </select>
        </div>
      </div>

      <!-- Taxonomy filter -->
      <div class="dqp-field">
        <label class="dqp-label">Filtra per tassonomia</label>
        <select :value="localQuery.taxonomy" @change="onTaxonomyChange($event.target.value)" class="dqp-select">
          <option value="">Nessuna</option>
          <option v-for="tax in taxonomies" :key="tax.value" :value="tax.value">{{ tax.label }}</option>
        </select>
      </div>

      <!-- Terms multi-select -->
      <div v-if="localQuery.taxonomy && selectedTaxTerms.length" class="dqp-field">
        <label class="dqp-label">Termini</label>
        <div class="dqp-terms">
          <label v-for="term in selectedTaxTerms" :key="term.value" class="dqp-term">
            <input
              type="checkbox"
              :checked="(localQuery.terms || []).includes(term.value)"
              @change="toggleTerm(term.value, $event.target.checked)"
            />
            <span>{{ term.label }}</span>
          </label>
        </div>
      </div>

      <!-- Field Mapping -->
      <div class="dqp-mapping">
        <label class="dqp-label dqp-label--section">Mappatura campi</label>
        <div v-for="field in itemFields" :key="field.key" class="dqp-map-row">
          <span class="dqp-map-key">{{ field.label }}</span>
          <select
            :value="localItemMap[field.key] || ''"
            @change="updateItemMap(field.key, $event.target.value)"
            class="dqp-select dqp-select--small"
          >
            <option value="">— Non mappato —</option>
            <option v-for="wf in wpFields" :key="wf.key" :value="wf.key">{{ wf.label }}</option>
          </select>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useDynamicContent } from '@/composables/useDynamicContent';

const props = defineProps({
  query: { type: Object, default: () => ({}) },
  itemFields: { type: Array, default: () => [] },
  itemMap: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['update:query', 'update:itemMap']);

const { fetchSources, sources } = useDynamicContent();

const defaultQuery = {
  enabled: false,
  post_type: 'post',
  posts_per_page: 6,
  orderby: 'date',
  order: 'DESC',
  taxonomy: '',
  terms: [],
};

const localQuery = ref({ ...defaultQuery, ...props.query });
const localItemMap = ref({ ...props.itemMap });

const isEnabled = computed(() => !!localQuery.value.enabled);

const postTypes = computed(() => sources.value?.post_types || []);
const taxonomies = computed(() => sources.value?.taxonomies || []);

const selectedTaxTerms = computed(() => {
  if (!localQuery.value.taxonomy) return [];
  const tax = taxonomies.value.find(t => t.value === localQuery.value.taxonomy);
  return tax?.terms || [];
});

const wpFields = [
  { key: 'post_title', label: 'Titolo' },
  { key: 'post_excerpt', label: 'Estratto' },
  { key: 'post_content', label: 'Contenuto' },
  { key: 'featured_image', label: 'Immagine in evidenza' },
  { key: 'post_date', label: 'Data' },
  { key: 'author_name', label: 'Autore' },
  { key: 'permalink', label: 'Permalink' },
  { key: 'first_term', label: 'Primo termine tassonomia' },
];

onMounted(() => {
  fetchSources();
});

watch(() => props.query, (val) => {
  localQuery.value = { ...defaultQuery, ...val };
}, { deep: true });

watch(() => props.itemMap, (val) => {
  localItemMap.value = { ...val };
}, { deep: true });

function toggleEnabled() {
  localQuery.value.enabled = !localQuery.value.enabled;
  emitQuery();
}

function updateQuery(key, value) {
  localQuery.value[key] = value;
  emitQuery();
}

function onTaxonomyChange(value) {
  localQuery.value.taxonomy = value;
  localQuery.value.terms = [];
  emitQuery();
}

function toggleTerm(termId, checked) {
  const terms = [...(localQuery.value.terms || [])];
  if (checked) {
    terms.push(termId);
  } else {
    const idx = terms.indexOf(termId);
    if (idx !== -1) terms.splice(idx, 1);
  }
  localQuery.value.terms = terms;
  emitQuery();
}

function updateItemMap(key, value) {
  if (value) {
    localItemMap.value[key] = value;
  } else {
    delete localItemMap.value[key];
  }
  emit('update:itemMap', { ...localItemMap.value });
}

function emitQuery() {
  emit('update:query', { ...localQuery.value });
}
</script>

<style scoped>
.dqp-wrap {
  margin-bottom: 8px;
  padding: 8px;
  background: #1e293b;
  border: 1px solid #334155;
  border-radius: 6px;
}

.dqp-toggle-row {
  display: flex;
  align-items: center;
}

.dqp-toggle-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  font-weight: 600;
  color: #d1d5db;
  cursor: pointer;
}

.dqp-switch {
  position: relative;
  width: 34px;
  height: 18px;
  border-radius: 9px;
  border: none;
  background: #4b5563;
  cursor: pointer;
  transition: background 0.2s;
  padding: 0;
}

.dqp-switch--on {
  background: var(--olo-color-primary, #6366f1);
}

.dqp-switch-thumb {
  position: absolute;
  top: 2px;
  left: 2px;
  width: 14px;
  height: 14px;
  border-radius: 50%;
  background: #fff;
  transition: transform 0.2s;
}

.dqp-switch--on .dqp-switch-thumb {
  transform: translateX(16px);
}

.dqp-config {
  margin-top: 10px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.dqp-field {
  display: flex;
  flex-direction: column;
  gap: 3px;
}

.dqp-field--half {
  flex: 1;
}

.dqp-row {
  display: flex;
  gap: 8px;
}

.dqp-label {
  font-size: 10px;
  font-weight: 600;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.dqp-label--section {
  font-size: 11px;
  color: #c7d2fe;
  margin-bottom: 4px;
}

.dqp-select,
.dqp-input {
  width: 100%;
  background: #111827;
  border: 1px solid #374151;
  border-radius: 4px;
  padding: 5px 8px;
  font-size: 12px;
  color: #e5e7eb;
}

.dqp-select:focus,
.dqp-input:focus {
  outline: none;
  border-color: var(--olo-color-primary, #6366f1);
}

.dqp-select--small {
  padding: 3px 6px;
  font-size: 11px;
}

.dqp-terms {
  display: flex;
  flex-direction: column;
  gap: 4px;
  max-height: 120px;
  overflow-y: auto;
  padding: 4px;
  background: #111827;
  border: 1px solid #374151;
  border-radius: 4px;
}

.dqp-term {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  color: #d1d5db;
  cursor: pointer;
}

.dqp-mapping {
  display: flex;
  flex-direction: column;
  gap: 6px;
  border-top: 1px solid #374151;
  padding-top: 8px;
}

.dqp-map-row {
  display: flex;
  align-items: center;
  gap: 8px;
}

.dqp-map-key {
  flex: 0 0 70px;
  font-size: 11px;
  color: #9ca3af;
  text-overflow: ellipsis;
  overflow: hidden;
  white-space: nowrap;
}
</style>
