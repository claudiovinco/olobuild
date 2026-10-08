<template>
  <div class="dbp-panel">
    <div class="dbp-header">
      <span class="dbp-title">{{ t('Collegamento dinamico') }}</span>
      <button type="button" class="dbp-close" @click="$emit('close')">{{ t('&times;') }}</button>
    </div>

    <!-- Step 1: Select source -->
    <div class="dbp-section">
      <label class="dbp-label">{{ t('Sorgente') }}</label>
      <FieldSelect ui="dropdown" :model-value="selectedSource" :options="sourceOpts" @update:model-value="onSourceChange" />
    </div>

    <!-- Step 2: Select field -->
    <div v-if="selectedSource" class="dbp-section">
      <label class="dbp-label">{{ t('Campo') }}</label>

      <!-- Manual input for custom_field -->
      <template v-if="fieldsForSource === 'manual'">
        <input
          v-model="selectedField"
          type="text"
          class="dbp-input"
          :placeholder="t('meta_key')"
        />
      </template>

      <!-- ACF grouped fields -->
      <template v-else-if="selectedSource === 'acf' && isGroupedFields">
        <FieldSelect ui="dropdown" :model-value="selectedField" :options="groupedFieldOpts" @update:model-value="selectedField = $event" />
      </template>

      <!-- Standard flat fields -->
      <template v-else-if="Array.isArray(fieldsForSource)">
        <FieldSelect ui="dropdown" :model-value="selectedField" :options="flatFieldOpts" @update:model-value="selectedField = $event" />
      </template>
    </div>

    <!-- Preview -->
    <div v-if="previewLoading || previewValue !== null" class="dbp-preview">
      <label class="dbp-label">{{ t('Anteprima') }}</label>
      <div class="dbp-preview-value" aria-live="polite">
        <span v-if="previewLoading" class="dbp-preview-loading">{{ t('Carico…') }}</span>
        <img v-else-if="previewIsImage" :src="previewValue" alt="" class="dbp-preview-img" />
        <span v-else>{{ previewDisplayValue }}</span>
      </div>
    </div>

    <!-- Actions -->
    <div class="dbp-actions">
      <button
        type="button"
        class="dbp-btn dbp-btn--apply"
        :disabled="!selectedSource || !selectedField"
        @click="applyBinding"
      >{{ t('Applica collegamento') }}</button>
      <button
        v-if="binding"
        type="button"
        class="dbp-btn dbp-btn--remove"
        @click="$emit('select', null)"
      >{{ t('Rimuovi') }}</button>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, computed, watch, onMounted } from 'vue';
import { useDynamicContent } from '@/composables/useDynamicContent';
import FieldSelect from './fields/FieldSelect.vue';

const props = defineProps({
  binding: { type: Object, default: null },
  fieldType: { type: String, default: 'text' },
});

const emit = defineEmits(['select', 'close']);

const { fetchSources, getFieldsForSource, getBindingSources, previewBinding } = useDynamicContent();

const selectedSource = ref(props.binding?.source || '');
const selectedField = ref(props.binding?.field || '');
const previewValue = ref(null);
const previewLoading = ref(false);

const bindingSources = computed(() => getBindingSources());

// Opzioni FieldSelect (label RAW: t() la applica FieldSelect internamente;
// le label dinamiche passano per t() come fallback identità).
const sourceOpts = computed(() => [
  { value: '', label: 'Seleziona sorgente...' },
  ...bindingSources.value.map(s => ({ value: s.value, label: s.label })),
]);

// Cambio sorgente: stessa semantica del vecchio @change (reset del campo solo
// se la sorgente cambia davvero).
function onSourceChange(value) {
  if (value === selectedSource.value) return;
  selectedSource.value = value;
  selectedField.value = '';
}

const fieldsForSource = computed(() => {
  if (!selectedSource.value) return [];
  return getFieldsForSource(selectedSource.value);
});

const flatFieldOpts = computed(() => {
  const f = fieldsForSource.value;
  if (!Array.isArray(f)) return [];
  return [
    { value: '', label: 'Seleziona campo...' },
    ...f.map(x => ({ value: x.key, label: x.label })),
  ];
});

// Campi ACF raggruppati: stessi value (f.key) del vecchio select nativo.
const groupedFieldOpts = computed(() => {
  const f = fieldsForSource.value;
  if (!Array.isArray(f)) return [];
  return [
    { value: '', label: 'Seleziona campo...' },
    ...f.map(g => ({
      group: g.group_label,
      options: (g.fields || []).map(x => ({ value: x.key, label: x.label })),
    })),
  ];
});

const isGroupedFields = computed(() => {
  const f = fieldsForSource.value;
  return Array.isArray(f) && f.length > 0 && f[0]?.group_label;
});

// Il campo scelto, come lo descrive il server ({ key, label, type }), anche
// dentro i gruppi ACF. Il campo libero (custom_field) non ha descrizione.
const selectedFieldMeta = computed(() => {
  const f = fieldsForSource.value;
  if (!Array.isArray(f) || !selectedField.value) return null;
  for (const x of f) {
    if (x?.key === selectedField.value) return x;
    const g = (x?.fields || []).find(y => y.key === selectedField.value);
    if (g) return g;
  }
  return null;
});

// Un'immagine solo se il campo lo è, o se l'indirizzo finisce con l'estensione
// di un'immagine. Prima bastava che cominciasse per «http»: il Permalink e ogni
// campo URL diventavano un'immagine rotta.
const previewIsImage = computed(() => {
  const v = previewValue.value;
  if (!v || typeof v !== 'string') return false;
  const haEstensione = /\.(jpe?g|png|gif|webp|avif|svg)(\?|#|$)/i.test(v);
  const tipo = selectedFieldMeta.value?.type;
  if (tipo) return tipo === 'image' && (haEstensione || /^(https?:)?\/\//i.test(v));
  return haEstensione;
});

const previewDisplayValue = computed(() => {
  const val = previewValue.value;
  if (val === null || val === undefined) return '';
  const str = String(val);
  return str.length > 120 ? str.substring(0, 120) + '...' : str;
});

onMounted(() => {
  fetchSources();
});

// Auto-preview when source+field change, e subito all'apertura di un
// collegamento già fatto. Vale l'ultima richiesta: una risposta lenta del campo
// di prima non copre quella del campo scelto adesso.
let richiesta = 0;
watch([selectedSource, selectedField], async () => {
  const mia = ++richiesta;
  if (!selectedSource.value || !selectedField.value) {
    previewValue.value = null;
    previewLoading.value = false;
    return;
  }
  previewLoading.value = true;
  const result = await previewBinding(selectedSource.value, selectedField.value);
  if (mia !== richiesta) return;
  previewValue.value = result?.value ?? null;
  previewLoading.value = false;
}, { immediate: true });

function applyBinding() {
  if (!selectedSource.value || !selectedField.value) return;
  emit('select', {
    source: selectedSource.value,
    field: selectedField.value,
  });
}
</script>

<style scoped>
/* Tema chiaro del chrome (dalla 1.4.563 l'Inspector è chiaro: il pannello scuro ne usciva a metà) */
.dbp-panel {
  --olo-ui-accent: #e8622a;
  margin-top: 6px;
  padding: 10px;
  background: #fff;
  border: 1px solid rgba(17, 24, 39, 0.12);
  border-radius: 8px;
  box-shadow: 0 6px 18px rgba(17, 24, 39, 0.08);
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.dbp-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.dbp-title {
  font-size: 11px;
  font-weight: 600;
  color: #1f2937;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.dbp-close {
  width: 18px;
  height: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  border-radius: 3px;
  background: transparent;
  color: #6b7280;
  font-size: 14px;
  cursor: pointer;
}

.dbp-close:hover {
  background: #f3f4f6;
  color: #111827;
}

.dbp-close:focus-visible,
.dbp-btn:focus-visible {
  outline: 2px solid var(--olo-ui-accent);
  outline-offset: 2px;
}

.dbp-section {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.dbp-label {
  font-size: 10px;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.dbp-input {
  width: 100%;
  background: #fff;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  padding: 5px 8px;
  font-size: 12px;
  color: #111827;
}

.dbp-input:focus {
  outline: none;
  border-color: var(--olo-ui-accent);
}

.dbp-preview {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.dbp-preview-value {
  padding: 6px 8px;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 4px;
  font-size: 12px;
  color: #374151;
  word-break: break-word;
  max-height: 80px;
  overflow: auto;
}

.dbp-preview-loading {
  color: #6b7280;
  font-style: italic;
}

.dbp-preview-img {
  max-width: 100%;
  max-height: 60px;
  object-fit: cover;
  border-radius: 3px;
}

.dbp-actions {
  display: flex;
  gap: 6px;
}

.dbp-btn {
  flex: 1;
  padding: 6px 0;
  border: 1px solid transparent;
  border-radius: 4px;
  font-size: 11px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s, border-color 0.15s;
}

.dbp-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.dbp-btn--apply {
  background: var(--olo-ui-accent);
  color: #fff;
}

.dbp-btn--apply:hover:not(:disabled) {
  filter: brightness(0.88);
}

/* Togliere è l'azione secondaria: contorno, non un secondo pulsante pieno accanto ad Applica */
.dbp-btn--remove {
  background: #fff;
  border-color: #e5e7eb;
  color: #b91c1c;
}

.dbp-btn--remove:hover {
  background: #fef2f2;
  border-color: #fecaca;
}
</style>
